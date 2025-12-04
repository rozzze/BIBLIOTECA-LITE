<?php

namespace App\Livewire\Admin;

use App\Models\Loan;
use App\Models\Penalty;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class LoanManager extends Component
{
    use WithPagination;

    public $filter = 'all'; // all, reserved, active, returned

    // Acción 1: Entregar el libro (De Reserva a Activo)
    public function approve($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->status !== 'reserved') return;

        // Regla de Negocio: 3 Días Fijos
        $pickupDate = Carbon::now();
        $dueDate = $pickupDate->copy()->addDays(3);

        $loan->update([
            'status' => 'active',
            'pickup_date' => $pickupDate,
            'due_date' => $dueDate,
        ]);

        // Restamos 1 al stock disponible real (Opcional si usas lógica calculada, 
        // pero con nuestra lógica is_available, el estado 'active' ya descuenta el stock).
        
        session()->flash('message', 'Libro entregado. Fecha límite: ' . $dueDate->format('d/m/Y'));
    }

    // Acción 2: Recibir devolución
    public function conclude($loanId)
    {
        $loan = Loan::findOrFail($loanId);

        if ($loan->status !== 'active') return;

        $returnDate = Carbon::now();
        $loan->update([
            'status' => 'returned',
            'return_date' => $returnDate,
        ]);

        // VERIFICAR SI HUBO RETRASO (Generar Multa)
        // Comparamos si la fecha actual es mayor a la fecha límite (ignorando horas)
        if ($returnDate->startOfDay()->gt(Carbon::parse($loan->due_date)->startOfDay())) {
            
            $daysLate = $returnDate->diffInDays(Carbon::parse($loan->due_date));
            $fineAmount = $daysLate * 5.00; // Ejemplo: 5.00 monedas por día de retraso

            Penalty::create([
                'user_id' => $loan->user_id,
                'loan_id' => $loan->id,
                'amount' => $fineAmount,
                'reason' => "Devolución tardía ($daysLate días de retraso)",
                'is_paid' => false,
            ]);

            // Bloquear al usuario automáticamente
            $loan->user->update(['is_blocked' => true]);

            session()->flash('warning', "Libro devuelto con RETRASO. Se generó una multa de $fineAmount y el usuario ha sido bloqueado.");
        } else {
            session()->flash('message', 'Libro devuelto a tiempo. ¡Excelente!');
        }
    }

    // Acción 3: Cancelar reserva (Si el estudiante nunca vino)
    public function cancel($loanId)
    {
        $loan = Loan::findOrFail($loanId);
        $loan->update(['status' => 'cancelled']);
        session()->flash('message', 'Reserva cancelada.');
    }

    public function render()
    {
        $loans = Loan::with(['user', 'book'])
            ->when($this->filter !== 'all', function($q) {
                $q->where('status', $this->filter);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.admin.loan-manager', [
            'loans' => $loans
        ]);
    }
}
