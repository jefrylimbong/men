<?php

namespace App\Observers;

use App\Models\FinanceTransaction;
use App\Models\Vendor;
use App\Models\WithdrawalData;

class WithdrawalObserver
{
    /**
     * Handle the WithdrawalData "created" event.
     */
    public function created(WithdrawalData $withdrawalData): void
    {
        //
    }

    /**
     * Handle the WithdrawalData "updated" event.
     */
    public function updated(WithdrawalData $withdrawalData): void
    {
        // Jika status berubah menjadi 'validated' (Tervalidasi)
        // Jika status 'validated' atau 'paid'
        if (in_array($withdrawalData->status, ['validated', 'paid'])) {

            // 1. Catat Hutang PT ke Vendor (Dana Talangan + Fee Vendor)
            if ($withdrawalData->vendor_id && ($withdrawalData->bailout_amount > 0 || $withdrawalData->vendor_fee > 0)) {
                $totalVendor = $withdrawalData->bailout_amount + $withdrawalData->vendor_fee;
                FinanceTransaction::updateOrCreate(
                    [
                        'reference_id' => $withdrawalData->id,
                        'category' => 'penarikan',
                        'credit_type' => 'Vendor',
                        'credit_id' => $withdrawalData->vendor_id,
                    ],
                    [
                        'transaction_date' => $withdrawalData->withdrawal_date ?? now(),
                        'amount' => $totalVendor,
                        'debit_type' => 'PT',
                        'debit_id' => 0,
                        'description' => 'Hutang penarikan unit '.($withdrawalData->customerData->nopol ?? '-').' (Talangan + Fee)',
                        'status' => 'pending',
                    ]
                );
            }

            // 2. Catat Piutang PT ke Finance (Dana yang akan cair)
            $finance = $withdrawalData->customerData->financeBranch->financeMaster ?? null;
            if ($finance && $withdrawalData->finance_payout > 0) {
                FinanceTransaction::updateOrCreate(
                    [
                        'reference_id' => $withdrawalData->id,
                        'category' => 'billing',
                        'debit_type' => 'Finance',
                        'debit_id' => $finance->id,
                    ],
                    [
                        'transaction_date' => $withdrawalData->withdrawal_date ?? now(),
                        'amount' => $withdrawalData->finance_payout,
                        'credit_type' => 'PT',
                        'credit_id' => 0,
                        'description' => 'Tagihan jasa penarikan unit '.($withdrawalData->customerData->nopol ?? '-'),
                        'status' => $withdrawalData->is_finance_paid ? 'completed' : 'pending',
                    ]
                );
            }

            // 3. Catat Biaya Penanganan (Handling Fee)
            if ($withdrawalData->handling_fee > 0) {
                FinanceTransaction::updateOrCreate(
                    [
                        'reference_id' => $withdrawalData->id,
                        'category' => 'operational',
                        'description' => 'Biaya penanganan unit '.($withdrawalData->customerData->nopol ?? '-'),
                    ],
                    [
                        'transaction_date' => $withdrawalData->withdrawal_date ?? now(),
                        'amount' => $withdrawalData->handling_fee,
                        'debit_type' => 'PT',
                        'debit_id' => 0,
                        'credit_type' => 'PT',
                        'credit_id' => 0, // Operasional internal
                        'status' => 'completed',
                    ]
                );
            }
        }

        // Jika Pembayaran dari Finance sudah Lunas
        if ($withdrawalData->isDirty('is_finance_paid') && $withdrawalData->is_finance_paid) {
            FinanceTransaction::where('reference_id', $withdrawalData->id)
                ->where('category', 'billing')
                ->update(['status' => 'completed']);
        }
    }

    /**
     * Handle the WithdrawalData "deleted" event.
     */
    public function deleted(WithdrawalData $withdrawalData): void
    {
        //
    }

    /**
     * Handle the WithdrawalData "restored" event.
     */
    public function restored(WithdrawalData $withdrawalData): void
    {
        //
    }

    /**
     * Handle the WithdrawalData "force deleted" event.
     */
    public function forceDeleted(WithdrawalData $withdrawalData): void
    {
        //
    }
}
