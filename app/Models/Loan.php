<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'request_date' => 'datetime',
        'pickup_date' => 'datetime',
        'due_date' => 'date',     // Importante para comparar solo fechas
        'return_date' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
    
    // Scope para buscar préstamos activos fácilmente
    // Uso: Loan::active()->get();
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}