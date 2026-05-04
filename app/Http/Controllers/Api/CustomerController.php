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

        // Optimasi: Hanya ambil kolom yang dibutuhkan
        $baseQuery = CustomerData::select('id', 'nopol', 'nama', 'norak', 'nosin', 'tipe', 'finance_branch_id', 'is_active')
            ->with(['financeBranch' => function($query) {
                $query->select('id', 'location_master_id')
                      ->with('locationMaster:id,name');
            }])
            ->where('is_active', true);

        $assignedFinanceIds = $user ? $user->financeMasters()->pluck('finance_masters.id') : collect();

        if (! $isSync) {
            $search = $request->input('search', '');
            
            if (! empty($search)) {
                $searchTerm = $search . '*';
                $baseQuery->whereFullText('nopol', $searchTerm, ['mode' => 'boolean']);
            }

            try {
                $customers = $baseQuery
                    ->limit(10)
                    ->get();
            } catch (\Exception $e) {
                // Jika Full-Text gagal (indeks belum ada), gunakan LIKE biasa
                \Log::error("Full-Text Search failed, falling back to LIKE: " . $e->getMessage());
                
                $fallbackQuery = CustomerData::select('id', 'nopol', 'nama', 'norak', 'nosin', 'tipe', 'finance_branch_id', 'is_active')
                    ->with(['financeBranch' => function($query) {
                        $query->select('id', 'location_master_id')
                              ->with('locationMaster:id,name');
                    }])
                    ->where('is_active', true);

                if (! empty($search)) {
                    $fallbackQuery->where('nopol', 'LIKE', "%{$search}%");
                }

                $customers = $fallbackQuery->limit(10)->get();
            }
        } else {
            // Sinkronisasi: Tetap gunakan filter cabang (kecuali superadmin)
            if ($user && $user->type === 'superadmin') {
                $query = CustomerData::query();
            } else {
                $branchIds = FinanceBranch::whereIn('finance_master_id', $assignedFinanceIds)->pluck('id');
                $query = CustomerData::whereIn('finance_branch_id', $branchIds);
            }

            $lastId = $request->input('last_id', 0);
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
                ->where('customer_data.id', '>', $lastId)
                ->oldest('customer_data.id')
                ->simplePaginate(2000);
        }

        // Jika response bukan paginator (untuk online search), buat array structure agar tetap kompatibel
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
