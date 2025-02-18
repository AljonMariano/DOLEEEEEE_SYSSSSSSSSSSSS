<?php

namespace Database\Factories;

use App\Models\PurchaseRequest;
use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

class PurchaseRequestFactory extends Factory
{
    protected $model = PurchaseRequest::class;

    public function definition()
    {
        $amount = $this->faker->randomFloat(2, 5000, 1000000);
        $date = $this->faker->dateTimeBetween('-3 months', 'now');

        return [
            'pr_no' => $this->faker->unique()->numerify('PR-####-###'),
            'pr_date' => Carbon::instance($date),
            'amount' => $amount,
            'purpose' => $this->faker->sentence(8),
            'status' => $this->faker->randomElement(['pending', 'acknowledged', 'processed']),
            'date_processed' => Carbon::instance($date)->addDays(rand(1, 5)),
        ];
    }
} 