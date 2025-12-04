<?php

namespace App\Livewire\Admin;

use App\Models\Penalty;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class PenaltyManager extends Component
{
    use WithPagination;

    public $search = '';

    // Acción: Marcar como PAGADA
    public function markAsPaid($penaltyId)
    {
        $penalty = Penalty::findOrFail($penaltyId);
        
        // 1. Marcar esta multa como pagada
        $penalty->update(['is_paid' => true]);

        // 2. Verificar si al usuario le quedan otras multas pendientes
        $user = $penalty->user;
        $hasPendingPenalties = $user->penalties()->where('is_paid', false)->exists();

        // 3. Si ya no tiene deudas, DESBLOQUEARLO
        if (!$hasPendingPenalties) {
            $user->update(['is_blocked' => false]);
            session()->flash('message', 'Multa cobrada. ¡El usuario ha sido desbloqueado y ya puede reservar de nuevo!');
        } else {
            session()->flash('warning', 'Multa cobrada, pero el usuario aún tiene otras deudas pendientes.');
        }
    }

    public function render()
    {
        // Buscamos multas pendientes, filtrando por nombre de usuario si hay búsqueda
        $penalties = Penalty::with(['user', 'loan.book'])
            ->where('is_paid', false) // Solo nos interesan las NO pagadas
            ->when($this->search, function($q) {
                $q->whereHas('user', function($u) {
                    $u->where('name', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.penalty-manager', [
            'penalties' => $penalties
        ]);
    }
}