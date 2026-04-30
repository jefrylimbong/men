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

        // Optimasi: Hanya ambil kolom yang dibutuhkan untuk sinkronisasi
        $baseQuery = CustomerData::select('id', 'nopol', 'nama', 'norak', 'nosin', 'tipe', 'finance_branch_id', 'is_active')
            ->with(['financeBranch:id,location_master_id', 'financeBranch.locationMaster:id,name'])
            ->where('is_active', true);

        // Realtime online search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $searchBy = $request->searchBy ?? 'semua';

            $baseQuery->where(function ($q) use ($search, $searchBy) {
                if ($searchBy === 'nopol') {
                    $q->where('nopol', 'like', "%{$search}%");
                } elseif ($searchBy === 'norak') {
                    $q->where('norak', 'like', "%{$search}%");
                } elseif ($searchBy === 'nosin') {
                    $q->where('nosin', 'like', "%{$search}%");
                } elseif ($searchBy === 'nama') {
                    $q->where('nama', 'like', "%{$search}%");
                } else {
                    $q->where('nopol', 'like', "%{$search}%")
                        ->orWhere('nama', 'like', "%{$search}%")
                        ->orWhere('norak', 'like', "%{$search}%")
                        ->orWhere('nosin', 'like', "%{$search}%");
                }
            });
        }

        $customers = collect();

        if ($user && $user->type === 'user') {
            $assignedFinanceIds = $user->financeMasters()->pluck('finance_masters.id');
            $count = $assignedFinanceIds->count();

            if ($count > 0) {
                if ($isSync) {
                    $branchIds = FinanceBranch::whereIn('finance_master_id', $assignedFinanceIds)->pluck('id');

                    $lastId = $request->input('last_id', 0);

                    // Optimasi Ekstrem: Gunakan JOIN agar database hanya dieksekusi 1x (Sangat Cepat)
                    // Dan ratakan struktur JSON agar payload lebih kecil
                    $customers = CustomerData::query()
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

                        /**
                         * FILTER CABANG (Toggle)
                         * Hilangkan komentar line di bawah jika ingin membatasi data berdasarkan cabang yang diassign ke user.
                         * Berikan komentar (//) jika ingin menarik SEMUA data aktif ke HP.
                         */
                        ->whereIn('customer_data.finance_branch_id', $branchIds)

                        ->where('customer_data.id', '>', $lastId)
                        ->oldest('customer_data.id')
                        ->simplePaginate(2000);
                } else {
                    // Pencarian Online: Limit menjadi 5 dan lakukan alokasi kuota dinamis
                    $dataLimit = $user->data_limit > 0 ? $user->data_limit : 5;
                    $totalLimit = 5; // Selalu 5 untuk pencarian online agar terhindar dari chunking error

                    $financeCounts = [];
                    foreach ($assignedFinanceIds as $financeId) {
                        $subQuery = clone $baseQuery;
                        $subQuery->whereHas('financeBranch', function ($q) use ($financeId) {
                            $q->where('finance_master_id', $financeId);
                        });
                        $financeCounts[$financeId] = $subQuery->count();
                    }

                    $quotas = array_fill_keys($assignedFinanceIds->toArray(), 0);
                    $remainingLimit = $totalLimit;
                    $activeFinances = $assignedFinanceIds->toArray();

                    while ($remainingLimit > 0 && count($activeFinances) > 0) {
                        $share = (int) ceil($remainingLimit / count($activeFinances));

                        foreach ($activeFinances as $key => $financeId) {
                            $available = $financeCounts[$financeId] - $quotas[$financeId];

                            if ($available <= 0) {
                                unset($activeFinances[$key]);

                                continue;
                            }

                            $take = min($share, $available);
                            $quotas[$financeId] += $take;
                            $remainingLimit -= $take;

                            if ($remainingLimit <= 0) {
                                break;
                            }
                        }
                    }

                    foreach ($assignedFinanceIds as $financeId) {
                        if ($quotas[$financeId] > 0) {
                            $subQuery = clone $baseQuery;
                            $subQuery->whereHas('financeBranch', function ($q) use ($financeId) {
                                $q->where('finance_master_id', $financeId);
                            });

                            $subQuery->latest()->limit($quotas[$financeId]);
                            $customers = $customers->concat($subQuery->get());
                        }
                    }

                    $customers = $customers->sortByDesc('id')->take($totalLimit)->values();
                }
            }

        } elseif ($user && $user->type !== 'superadmin') {
            // Tipe selain superadmin & user (misal admin)
            $assignedFinanceIds = $user->financeMasters()->pluck('finance_masters.id');
            $baseQuery->whereHas('financeBranch', function ($q) use ($assignedFinanceIds) {
                $q->whereIn('finance_master_id', $assignedFinanceIds);
            });

            $baseQuery->latest();

            if (! $isSync) {
                $baseQuery->limit(5);
                $customers = $baseQuery->get();
            } else {
                $lastId = $request->input('last_id', 0);
                $customers = CustomerData::query()
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

                    /**
                     * FILTER CABANG (Toggle)
                     * Hilangkan komentar line di bawah jika ingin membatasi data berdasarkan cabang yang diassign ke admin.
                     * Berikan komentar (//) jika ingin menarik SEMUA data aktif ke HP.
                     */
                    ->whereIn('customer_data.finance_branch_id', FinanceBranch::whereIn('finance_master_id', $assignedFinanceIds)->pluck('id'))

                    ->where('customer_data.id', '>', $lastId)
                    ->oldest('customer_data.id')
                    ->simplePaginate(2000);
            }

        } else {
            // Superadmin
            $baseQuery->latest();
            if (! $isSync) {
                $baseQuery->limit(5);
                $customers = $baseQuery->get();
            } else {
                $lastId = $request->input('last_id', 0);
                $customers = CustomerData::query()
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
        }

        // Jika response bukan paginator (untuk online search), buat array structure agar tetap kompatibel
        $responseData = $customers instanceof AbstractPaginator
            ? $customers
            : ['data' => $customers];

        return response()->json([
            'status' => 'success',
            'assigned_finances' => $assignedFinanceIds ?? [],
            'data' => $responseData,
        ]);
    }
}
