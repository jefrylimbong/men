<?php

namespace Database\Seeders;

use App\Models\FinanceBranch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CustomerData10mSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total = 10000000;
        $chunkSize = 1000;

        $financeBranchIds = FinanceBranch::pluck('id')->toArray();
        if (empty($financeBranchIds)) {
            $financeBranchIds = [1];
        }

        $now = now();
        $this->command->info("Memulai insert {$total} data dummy customer...");
        $bar = $this->command->getOutput()->createProgressBar($total);
        $bar->start();

        for ($i = 0; $i < $total; $i += $chunkSize) {
            $data = [];
            for ($j = 0; $j < $chunkSize; $j++) {
                $idx = $i + $j;
                if ($idx >= $total) {
                    break;
                }

                $data[] = [
                    'nopol' => 'B '.rand(1000, 9999).' '.strtoupper(Str::random(2)),
                    'norak' => 'MH1'.strtoupper(Str::random(14)),
                    'nosin' => 'J'.strtoupper(Str::random(11)),
                    'tipe' => 'HONDA BEAT',
                    'nama' => 'Customer Dummy '.$idx,
                    'tenor' => rand(12, 36),
                    'ke' => rand(1, 12),
                    'od' => rand(0, 120),
                    'ph' => '08'.rand(100000000, 999999999),
                    'finance_branch_id' => $financeBranchIds[array_rand($financeBranchIds)],
                    'alamat' => 'Alamat Dummy Jalan '.Str::random(10),
                    'is_active' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('customer_data')->insert($data);
            $bar->advance(count($data));
        }

        $bar->finish();
        $this->command->info("\nSelesai!");
    }
}
