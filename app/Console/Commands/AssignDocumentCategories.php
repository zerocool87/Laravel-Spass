<?php

namespace App\Console\Commands;

use App\Models\Document;
use Illuminate\Console\Command;

class AssignDocumentCategories extends Command
{
    protected $signature = 'documents:assign-categories {--default= : Default category to use for unmatched documents} {--dry-run : Do not write changes, just show what would be done}';

    protected $description = 'Assign categories to documents automatically based on filename/title/description keywords';

    public function handle()
    {
        $default = $this->option('default');
        $dryRun = $this->option('dry-run');

        $available = config('documents.categories', []);
        if ($default && ! in_array($default, $available, true)) {
            $this->error("Default category '{$default}' is not a valid category. Available: ".implode(', ', $available));

            return 1;
        }

        $this->info('Scanning documents for category assignment...');

        $query = Document::query()->whereNull('category')->orWhere('category', '');
        $count = $query->count();
        if ($count === 0) {
            $this->info('No documents require category assignment.');

            return 0;
        }

        $this->info("Found $count documents to check.");

        $assigned = 0;
        $skipped = 0;

        $query->chunk(100, function ($docs) use (&$assigned, &$skipped, $dryRun, $default) {
            foreach ($docs as $doc) {
                $detected = $this->detectCategory($doc);
                if (! $detected && $default) {
                    $detected = $default;
                }

                if ($detected) {
                    $this->line("[Assign] Document ID {$doc->id} => {$detected}");
                    if (! $dryRun) {
                        $doc->category = $detected;
                        $doc->save();
                    }
                    $assigned++;
                } else {
                    $this->line("[Skip] Document ID {$doc->id} (no match)");
                    $skipped++;
                }
            }
        });

        $this->info("Done. Assigned: $assigned, Skipped: $skipped");

        return 0;
    }

    protected function detectCategory(Document $doc): ?string
    {
        // Normalize string: remove accents and lower-case
        $hay = ($doc->title ?? '').' '.($doc->original_name ?? '').' '.($doc->description ?? '');
        $hay = mb_strtolower($this->removeAccents($hay));

        $patterns = [
            'Convocations' => '/\bconvoc\w*/i',
            'Ordres du jour' => '/\bordre\b/i',
            'Comptes rendus' => '/\bcompte\b|\bcompte[- ]rendu\b/i',
            'Rapports' => '/\brappor?t\b|\brapports\b/i',
            'Délibérations' => '/\bdelib\w*/i',
            'Guides' => '/\bguide\b/i',
        ];

        foreach ($patterns as $category => $regex) {
            if (preg_match($regex, $hay)) {
                return $category;
            }
        }

        return null;
    }

    protected function removeAccents(string $str): string
    {
        $trans = [
            'à' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'î' => 'i', 'ï' => 'i',
            'ô' => 'o', 'ö' => 'o',
            'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c',
            'œ' => 'oe', 'æ' => 'ae',
        ];

        return strtr($str, $trans);
    }
}
