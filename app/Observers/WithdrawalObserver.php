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
        $this->syncTransactions($withdrawalData);
    }

    /**
     * Handle the WithdrawalData "updated" event.
     */
    public function updated(WithdrawalData $withdrawalData): void
    {
        $this->syncTransactions($withdrawalData);
    }

    /**
     * Sync finance transactions based on withdrawal data.
     */
    /**
     * Sync finance transactions based on withdrawal data.
     */
    private function syncTransactions(WithdrawalData $withdrawalData): void
    {
        // Jika status 'validated' atau 'paid'
        if (in_array($withdrawalData->status, ['validated', 'paid'])) {

            // 1. Catat Hutang PT ke Vendor (Dana Talangan + Fee Vendor)
            // Konteks: Vendor memberikan dana (Talangan), PT menerima kewajiban bayar
            if ($withdrawalData->vendor_id && ($withdrawalData->bailout_amount > 0 || $withdrawalData->vendor_fee > 0)) {
                $totalVendor = $withdrawalData->bailout_amount + $withdrawalData->vendor_fee;
                FinanceTransaction::updateOrCreate(
                    [
                        'reference_id' => $withdrawalData->id,
                        'category' => 'penarikan',
                        'debit_type' => 'Vendor', // Tujuan (Hutang yang harus dibayar ke Vendor)
                    ],
                    [
                        'transaction_date' => $withdrawalData->withdrawal_date ?? now(),
                        'amount' => $totalVendor,
                        'credit_type' => 'PT', // Sumber (Uang keluar dari PT nanti)
                        'credit_id' => 0,
                        'debit_id' => $withdrawalData->vendor_id,
                        'description' => 'Hutang penarikan unit '.($withdrawalData->customerData->nopol ?? '-').' (Talangan + Fee)',
                        'status' => $withdrawalData->is_vendor_paid ? 'completed' : 'pending',
                    ]
                );
            }

            // 2. Catat Piutang PT ke Finance (Dana yang akan cair)
            // Konteks: PT menerima uang dari Finance
            $finance = $withdrawalData->customerData->financeBranch->financeMaster ?? null;
            $billingAmount = $withdrawalData->is_finance_paid ? $withdrawalData->finance_payout : $withdrawalData->estimated_payout;

            if ($finance && $billingAmount > 0) {
                FinanceTransaction::updateOrCreate(
                    [
                        'reference_id' => $withdrawalData->id,
                        'category' => 'billing',
                        'credit_type' => 'Finance', // Sumber uang
                    ],
                    [
                        'transaction_date' => $withdrawalData->withdrawal_date ?? now(),
                        'amount' => $billingAmount,
                        'debit_type' => 'PT', // Penerima uang
                        'debit_id' => 0,
                        'credit_id' => $finance->id,
                        'description' => 'Tagihan jasa penarikan unit '.($withdrawalData->customerData->nopol ?? '-'),
                        'status' => $withdrawalData->is_finance_paid ? 'completed' : 'pending',
                    ]
                );
            }

            // 3. Catat Biaya Penanganan (Handling Fee)
            // Konteks: Pemasukan operasional PT
            if ($withdrawalData->handling_fee > 0) {
                FinanceTransaction::updateOrCreate(
                    [
                        'reference_id' => $withdrawalData->id,
                        'category' => 'operational',
                    ],
                    [
                        'transaction_date' => $withdrawalData->withdrawal_date ?? now(),
                        'amount' => $withdrawalData->handling_fee,
                        'debit_type' => 'PT', // PT menerima fee
                        'debit_id' => 0,
                        'credit_type' => 'PT', // Dari sumber internal operasional (Gunakan PT agar konsisten)
                        'credit_id' => 0,
                        'description' => 'Biaya penanganan unit '.($withdrawalData->customerData->nopol ?? '-'),
                        'status' => 'completed',
                    ]
                );
            }
        }

        // Jika Pembayaran dari Finance sudah Lunas
        if ($withdrawalData->isDirty('is_finance_paid')) {
            FinanceTransaction::where('reference_id', $withdrawalData->id)
                ->where('category', 'billing')
                ->update(['status' => $withdrawalData->is_finance_paid ? 'completed' : 'pending']);
        }

        // Jika Pembayaran ke Vendor sudah Lunas
        if ($withdrawalData->isDirty('is_vendor_paid')) {
            FinanceTransaction::where('reference_id', $withdrawalData->id)
                ->where('category', 'penarikan')
                ->update(['status' => $withdrawalData->is_vendor_paid ? 'completed' : 'pending']);
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
