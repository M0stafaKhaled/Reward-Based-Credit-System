<?php
namespace App\Observers;

use App\Models\Purchase;
use App\Models\CreditLog;
use App\Models\User;

class CreditLogObserver
{
    public function created(Purchase $purchase)
    {
        $user = User::find($purchase->user_id);
        CreditLog::create([
            'user_id' => $purchase->user_id,
            'type' => 'purchase',
            'amount' => $purchase->credits,
            'balance_after' => $user ? $user->credits : null,
            'description' => 'Purchased credit package',
            'reference_id' => $purchase->id,
        ]);
    }
} 