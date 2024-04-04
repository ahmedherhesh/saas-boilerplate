<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Plan::create([
            'title' => 'Monthly',
            'days' => 30,
            'period' => 'month',
            'price' => 9,
            'currency' => 'usd',
            'stripe_price_id' => 'price_1Oz6fAP2bzFyEkDsoNsvJ4ez',
            'paypal_plan_id' => 'P-1JH79129675095217MYGJQPQ',
        ]);
        Plan::create([
            'title' => 'Yearly',
            'days' => 365,
            'period' => 'year',
            'price' => 90,
            'currency' => 'usd',
            'stripe_price_id' => 'price_1Oz6mAP2bzFyEkDs8iA7iXNa',
            'paypal_plan_id' => 'P-824604400H831193KMYHND7I',
        ]);
    }
}
