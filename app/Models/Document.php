<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'path',
        'original_name',
        'created_by',
        'visible_to_all',
    ];

    protected $casts = [
        'visible_to_all' => 'boolean',
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'document_user');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
