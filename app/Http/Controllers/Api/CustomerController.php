<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CustomerData;
use App\Models\FinanceBranch;
use Illuminate\Http\Request;
use Illuminate\Pagination\AbstractPaginator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $isSync = $request->has('is_sync') && $request->is_sync == 'true';
        $assignedFinanceIds = $user ? $user->financeMasters()->pluck('finance_masters.id') : collect();

        if (! $isSync) {
            $search = $request->input('search', '');
            $customers = [];

            if (! empty($search)) {
                try {
                    // 1. Coba Meilisearch (Scout) - Sangat Cepat
                    $customers = CustomerData::search($search)
                        ->where('is_active', 1)
                        ->take(20)
                        ->get();
                } catch (\Exception $e) {
                    // 2. Fallback: SQL Biasa jika Meilisearch gagal
                    $customers = CustomerData::where('nopol', 'like', "%$search%")
                        ->where('is_active', 1)
                        ->where('nopol', 'not like', '%-%') // Buang yang ada tanda strip (nomor mesin)
                        ->whereRaw('LENGTH(nopol) < 10')   // Buang yang terlalu panjang
                        ->limit(20)
                        ->get();
                }
            }
        } else {
            // Sinkronisasi: Cek hak akses
            if (($user && $user->type === 'superadmin') || $assignedFinanceIds->isEmpty()) {
                $query = CustomerData::query();
            } else {
                $branchIds = FinanceBranch::whereIn('finance_master_id', $assignedFinanceIds)->pluck('id');
                $query = CustomerData::whereIn('finance_branch_id', $branchIds);
            }

            $lastId = $request->input('last_id', 0);
            $lastSync = $user->last_sync;

            $customers = $query
                ->join('finance_branches', 'customer_data.finance_branch_id', '=', 'finance_branches.id')
                ->join('finance_masters', 'finance_branches.finance_master_id', '=', 'finance_masters.id')
                ->join('location_masters', 'finance_branches.location_master_id', '=', 'location_masters.id')
                ->select(
                    'customer_data.id',
                    'customer_data.nopol',
                    'customer_data.nama',
                    'customer_data.norak',
                    'customer_data.nosin',
                    'customer_data.tipe',
                    'finance_masters.fin_name as finance_name',
                    'location_masters.name as location_name'
                )
                ->where('customer_data.is_active', true)
                ->when($lastSync && $lastId > 0, function ($q) use ($lastSync) {
                    return $q->where('customer_data.created_at', '>', $lastSync);
                })
                ->when($lastId > 0 && !$lastSync, function ($q) use ($lastId) {
                    return $q->where('customer_data.id', '>', $lastId);
                })
                ->oldest('customer_data.id')
                ->simplePaginate(5000);
        }

        // Response structure agar kompatibel dengan aplikasi
        $responseData = $customers instanceof AbstractPaginator
            ? $customers
            : ['data' => $customers];

        return response()->json([
            'status' => 'success',
            'assigned_finances' => $assignedFinanceIds,
            'data' => $responseData,
        ]);
    }
}
