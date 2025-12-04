<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = []; // Permitimos asignación masiva para todo (útil para prototipos rápidos)

    // Relación: Un libro pertenece a una categoría
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Relación: Un libro pertenece a una editorial
    public function publisher()
    {
        return $this->belongsTo(Publisher::class);
    }
    
    // Relación: Un libro tiene muchos préstamos
    public function loans()
    {
        return $this->hasMany(Loan::class);
    }

    // Lógica mágica para ver si está disponible
    // Se usa como: $book->is_available
    public function getIsAvailableAttribute()
    {
        // Contamos cuántos préstamos activos o reservados hay
        $activeLoans = $this->loans()
            ->whereIn('status', ['reserved', 'active'])
            ->count();

        // Si el stock total es mayor a los prestados, hay disponibles
        return ($this->stock_total - $activeLoans) > 0;
    }
}
