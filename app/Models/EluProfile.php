<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EluProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'code_insee',
        'collectivite',
        'epci_commune',
        'secteur',
        'nom_secteur',
        'date_deliberation',
        'visa_prefecture',
        'probleme_delib',
        'civilite',
        'rt_ds_dt',
        'titre',
        'ordre_suppleants',
        'contact',
        'mail_personnel',
        'mail_2',
        'telephone',
        'adresse_1',
        'adresse_2',
        'code_postal',
        'profession',
        'societe',
        'date_naissance',
        'newsletter',
        'frais_route',
        'rib_fourni',
        'chevaux_fiscaux',
    ];

    public function casts(): array
    {
        return [
            'date_deliberation' => 'date',
            'date_naissance' => 'date',
            'newsletter' => 'boolean',
            'frais_route' => 'boolean',
            'rib_fourni' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
