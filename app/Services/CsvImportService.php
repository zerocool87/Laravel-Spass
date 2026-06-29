<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EluProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class CsvImportService
{
    private const TITRE_MAPPING = [
        'Maire' => 'Président',
        'Conseiller municipal' => 'Membre du bureau',
    ];

    private const DATE_FORMATS = ['d/m/Y', 'Y-m-d', 'd-m-Y', 'm/d/Y'];

    /** @var string[] */
    private array $communes;

    /** @param string[] $communes */
    public function __construct(array $communes)
    {
        $this->communes = $communes;
    }

    /**
     * @return array{created: int, skipped: list<string>}
     */
    public function import(UploadedFile $file): array
    {
        $handle = fopen($file->getRealPath(), 'r');
        if (! $handle) {
            throw new \RuntimeException(__('Impossible de lire le fichier.'));
        }

        try {
            $this->skipBom($handle);
            $created = 0;
            $skipped = [];
            $rowNumber = 0;

            while (($row = fgetcsv($handle, 0, "\t", '"')) !== false) {
                $rowNumber++;

                if ($this->isRowEmpty($row)) {
                    continue;
                }

                if ($rowNumber === 1 && $this->isHeaderRow($row)) {
                    continue;
                }

                $result = $this->importRow($row, $rowNumber);
                if ($result['success']) {
                    $created++;
                } else {
                    $skipped[] = $result['error'];
                }
            }
        } finally {
            fclose($handle);
        }

        return ['created' => $created, 'skipped' => $skipped];
    }

    /**
     * @param  array<int, string|null>  $row
     * @return array{success: bool, error?: string}
     */
    private function importRow(array $row, int $rowNumber): array
    {
        try {
            $email = $this->cleanString($row[16] ?? '');

            if ($email === '') {
                return ['success' => false, 'error' => "Ligne {$rowNumber} : email vide"];
            }

            if (User::where('email', $email)->exists()) {
                return ['success' => false, 'error' => "Ligne {$rowNumber} : {$email} déjà existant"];
            }

            $nom = $this->cleanString($row[8] ?? '');
            $prenom = $this->cleanString($row[9] ?? '');
            $titreRaw = $this->cleanString($row[13] ?? '');
            $commune = $this->cleanString($row[22] ?? '');

            $titres = $this->parseAndMapTitres($titreRaw);
            $tempPassword = Str::random(16);

            if ($commune !== '' && ! in_array($commune, $this->communes, true)) {
                $commune = '';
            }

            $user = User::create([
                'name' => $nom,
                'prenom' => $prenom,
                'email' => $email,
                'password' => $tempPassword,
                'titres' => $titres ?: null,
                'commune' => $commune !== '' ? $commune : null,
            ]);

            $user->forceFill(['is_elu' => true])->save();

            EluProfile::create([
                'user_id' => $user->id,
                'code_insee' => $this->nullable($row[0] ?? ''),
                'collectivite' => $this->nullable($row[1] ?? ''),
                'epci_commune' => $this->nullable($row[2] ?? ''),
                'secteur' => $this->nullable($row[3] ?? ''),
                'nom_secteur' => $this->nullable($row[4] ?? ''),
                'date_deliberation' => $this->parseDate($row[5] ?? ''),
                'visa_prefecture' => $this->nullable($row[6] ?? ''),
                'probleme_delib' => $this->nullable($row[7] ?? ''),
                'civilite' => $this->nullable($row[11] ?? ''),
                'rt_ds_dt' => $this->nullable($row[12] ?? ''),
                'titres' => $titres ?: null,
                'ordre_suppleants' => $this->parseInt($row[14] ?? ''),
                'contact' => $this->nullable($row[15] ?? ''),
                'mail_personnel' => $email,
                'mail_2' => $this->nullable($row[17] ?? ''),
                'telephone' => $this->nullable($row[18] ?? ''),
                'adresse_1' => $this->nullable($row[19] ?? ''),
                'adresse_2' => $this->nullable($row[20] ?? ''),
                'code_postal' => $this->nullable($row[21] ?? ''),
                'profession' => $this->nullable($row[23] ?? ''),
                'societe' => $this->nullable($row[24] ?? ''),
                'date_naissance' => $this->parseDate($row[25] ?? ''),
                'newsletter' => $this->parseBoolean($row[27] ?? ''),
                'frais_route' => $this->parseBoolean($row[29] ?? ''),
                'rib_fourni' => $this->parseBoolean($row[30] ?? ''),
                'chevaux_fiscaux' => $this->nullable($row[31] ?? ''),
            ]);

            return ['success' => true];
        } catch (\Throwable $e) {
            return ['success' => false, 'error' => "Ligne {$rowNumber} : erreur — {$e->getMessage()}"];
        }
    }

    /**
     * @param  resource  $handle
     */
    private function skipBom($handle): void
    {
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isRowEmpty(array $row): bool
    {
        if (count($row) < 2) {
            return true;
        }

        return empty(array_filter(array_map('trim', $row)));
    }

    /**
     * @param  array<int, string|null>  $row
     */
    private function isHeaderRow(array $row): bool
    {
        $firstCell = strtoupper($this->cleanString($row[0] ?? ''));

        return str_contains($firstCell, 'CODE') || str_contains($firstCell, 'INSEE');
    }

    /** @return list<string> */
    private function parseAndMapTitres(string $raw): array
    {
        $parts = array_filter(array_map('trim', explode('|', $raw)));

        return array_values(array_map(
            fn (string $t) => self::TITRE_MAPPING[$t] ?? $t,
            $parts
        ));
    }

    private function cleanString(mixed $value): string
    {
        return trim((string) $value);
    }

    private function nullable(mixed $value): ?string
    {
        $v = $this->cleanString($value);

        return $v !== '' ? $v : null;
    }

    private function parseInt(mixed $value): ?int
    {
        $v = $this->cleanString($value);

        return is_numeric($v) ? (int) $v : null;
    }

    private function parseBoolean(mixed $value): bool
    {
        $v = strtolower($this->cleanString($value));

        return in_array($v, ['1', 'true', 'oui', 'yes', 'x', 'ok'], true);
    }

    private function parseDate(mixed $value): ?string
    {
        $v = $this->cleanString($value);

        if ($v === '' || $v === '0') {
            return null;
        }

        foreach (self::DATE_FORMATS as $format) {
            $date = \DateTime::createFromFormat($format, $v);
            if ($date && $date->format($format) === $v) {
                return $date->format('Y-m-d');
            }
        }

        return null;
    }
}
