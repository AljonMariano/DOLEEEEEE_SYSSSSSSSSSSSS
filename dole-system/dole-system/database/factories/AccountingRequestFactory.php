<?php

namespace Database\Factories;

use App\Models\AccountingRequest;
use App\Models\BudgetRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class AccountingRequestFactory extends Factory
{
    protected $model = AccountingRequest::class;

    public function definition()
    {
        $budgetRequest = BudgetRequest::factory()->create();
        $date = Carbon::parse($budgetRequest->ors_date);
        $status = $this->faker->randomElement(['acknowledged', 'for_payment', 'dv_processed', 'rejected']);

        return [
            'budget_request_id' => $budgetRequest->id,
            'ors_no' => $budgetRequest->ors_no,
            'po_no' => $this->faker->unique()->numerify('PO-####-###'),
            'po_date' => $date->addDays(rand(1, 3)),
            'dv_no' => $status === 'dv_processed' ? $this->faker->unique()->numerify('DV-####-###') : null,
            'dv_date' => $status === 'dv_processed' ? $date->addDays(rand(4, 7)) : null,
            'payee' => $budgetRequest->payee,
            'amount' => $budgetRequest->amount,
            'status' => $status,
            'remarks' => $status === 'rejected' ? $this->faker->sentence() : null,
            'date_processed' => $date->addDays(rand(1, 3)),
        ];
    }

    /**
     * Configure the factory to generate a rejected request.
     */
    public function rejected()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => 'rejected',
                'remarks' => $this->faker->sentence(),
                'dv_no' => null,
                'dv_date' => null,
            ];
        });
    }

    /**
     * Configure the factory to generate an accepted request with DV.
     */
    public function accepted()
    {
        return $this->state(function (array $attributes) {
            $date = Carbon::parse($attributes['po_date']);
            return [
                'status' => 'dv_processed',
                'dv_no' => $this->faker->unique()->numerify('DV-####-###'),
                'dv_date' => $date->addDays(rand(4, 7)),
                'remarks' => null,
            ];
        });
    }
} 