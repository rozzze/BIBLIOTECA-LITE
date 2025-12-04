<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Penalty extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_paid' => 'boolean',
        'amount' => 'decimal:2',
    ];

    // Relación: Una multa pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una multa pertenece a un préstamo específico
    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }
}