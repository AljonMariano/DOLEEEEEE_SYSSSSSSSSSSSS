<?php

namespace Database\Factories;

use App\Models\BudgetRequest;
use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class BudgetRequestFactory extends Factory
{
    protected $model = BudgetRequest::class;

    public function definition()
    {
        $purchaseRequest = PurchaseRequest::factory()->create();
        $date = Carbon::parse($purchaseRequest->pr_date);

        return [
            'purchase_request_id' => $purchaseRequest->id,
            'pr_no' => $purchaseRequest->pr_no,
            'pr_date' => $date,
            'ors_no' => $this->faker->unique()->numerify('ORS-####-###'),
            'ors_date' => $date->addDays(rand(1, 3)),
            'payee' => $this->faker->company(),
            'purpose' => $purchaseRequest->purpose,
            'amount' => $purchaseRequest->amount,
            'status' => $this->faker->randomElement(['acknowledged', 'processed']),
            'date_processed' => $date->addDays(rand(1, 3)),
        ];
    }
} 