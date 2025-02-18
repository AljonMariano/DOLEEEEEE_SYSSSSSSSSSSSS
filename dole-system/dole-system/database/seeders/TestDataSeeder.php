<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PurchaseRequest;
use App\Models\BudgetRequest;
use App\Models\AccountingRequest;
use Carbon\Carbon;
use Faker\Factory as Faker;

class TestDataSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Create standalone purchase requests
        for ($i = 0; $i < 5; $i++) {
            PurchaseRequest::create([
                'pr_no' => 'PR-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                'pr_date' => Carbon::now()->subDays(rand(1, 90)),
                'amount' => $faker->randomFloat(2, 5000, 1000000),
                'purpose' => $faker->sentence(8),
                'status' => 'pending',
                'date_processed' => Carbon::now(),
            ]);
        }

        // Create budget requests with their purchase requests
        for ($i = 0; $i < 5; $i++) {
            $pr = PurchaseRequest::create([
                'pr_no' => 'PR-' . str_pad($i + 6, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                'pr_date' => $date = Carbon::now()->subDays(rand(1, 90)),
                'amount' => $amount = $faker->randomFloat(2, 5000, 1000000),
                'purpose' => $purpose = $faker->sentence(8),
                'status' => 'acknowledged',
                'date_processed' => $date->copy()->addDays(1),
            ]);

            BudgetRequest::create([
                'purchase_request_id' => $pr->id,
                'pr_no' => $pr->pr_no,
                'pr_date' => $pr->pr_date,
                'ors_no' => 'ORS-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                'ors_date' => $date->copy()->addDays(2),
                'payee' => $faker->company,
                'purpose' => $purpose,
                'amount' => $amount,
                'status' => 'acknowledged',
                'date_processed' => $date->copy()->addDays(2),
            ]);
        }

        // Create accounting requests in various states
        for ($i = 0; $i < 12; $i++) {
            $pr = PurchaseRequest::create([
                'pr_no' => 'PR-' . str_pad($i + 11, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                'pr_date' => $date = Carbon::now()->subDays(rand(1, 90)),
                'amount' => $amount = $faker->randomFloat(2, 5000, 1000000),
                'purpose' => $purpose = $faker->sentence(8),
                'status' => 'processed',
                'date_processed' => $date->copy()->addDays(1),
            ]);

            $br = BudgetRequest::create([
                'purchase_request_id' => $pr->id,
                'pr_no' => $pr->pr_no,
                'pr_date' => $pr->pr_date,
                'ors_no' => 'ORS-' . str_pad($i + 6, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                'ors_date' => $date->copy()->addDays(2),
                'payee' => $payee = $faker->company,
                'purpose' => $purpose,
                'amount' => $amount,
                'status' => 'processed',
                'date_processed' => $date->copy()->addDays(2),
            ]);

            // Determine the status based on the iteration
            $status = match (intdiv($i, 3)) {
                0 => 'acknowledged',
                1 => 'for_payment',
                2 => 'dv_processed',
                3 => 'rejected',
                default => 'acknowledged',
            };

            $ar = [
                'budget_request_id' => $br->id,
                'ors_no' => $br->ors_no,
                'po_no' => 'PO-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999),
                'po_date' => $date->copy()->addDays(3),
                'payee' => $payee,
                'amount' => $amount,
                'status' => $status,
                'date_processed' => $date->copy()->addDays(3),
            ];

            // Add DV details for processed requests
            if ($status === 'dv_processed') {
                $ar['dv_no'] = 'DV-' . str_pad($i + 1, 4, '0', STR_PAD_LEFT) . '-' . rand(100, 999);
                $ar['dv_date'] = $date->copy()->addDays(4);
            }

            // Add remarks for rejected requests
            if ($status === 'rejected') {
                $ar['remarks'] = $faker->sentence();
            }

            AccountingRequest::create($ar);
        }
    }
} 