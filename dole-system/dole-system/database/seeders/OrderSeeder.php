<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $orders = [
            [
                'order_id' => '2024-TSSD-169',
                'date' => '2025-01-02',
                'time' => '10:50',
                'origin' => 'TSSD',
                'subject' => 'ADVOCACY MATERIALS FOR THE 2024 REGIONAL YEPA',
                'amount' => 163753.00,
                'forward_to' => 'SUPPLY',
                'status' => 'pending',
                'remarks' => null,
                'received_by' => 'John Doe',
                'file_case_no' => 'FC-2024-001',
            ],
            [
                'order_id' => '2024-TSSD-170',
                'date' => '2025-01-02',
                'time' => '13:00',
                'origin' => 'MALSU',
                'subject' => 'MEALS & ACCOMODATION FOR YEPA AWARDS',
                'amount' => 646800.00,
                'forward_to' => 'IMSD',
                'status' => 'pending',
                'remarks' => null,
                'received_by' => 'Jane Smith',
                'file_case_no' => 'FC-2024-002',
            ],
            [
                'order_id' => 'AB-24-11-86',
                'date' => '2025-01-02',
                'time' => '14:00',
                'origin' => 'MALSU',
                'subject' => 'INDIVIDUAL LIVELIHOOD PROJECT (REED CRAFT)',
                'amount' => 25160.00,
                'forward_to' => 'IMSD',
                'status' => 'pending',
                'remarks' => null,
                'received_by' => 'Mike Wilson',
                'file_case_no' => 'FC-2024-003',
            ],
            [
                'order_id' => '2024-KFO-141',
                'date' => '2025-01-06',
                'time' => '10:40',
                'origin' => 'KFO',
                'subject' => 'CATERING SERVICES FOR TAV ORIENTATION',
                'amount' => 7000.00,
                'forward_to' => 'SUPPLY',
                'status' => 'pending',
                'remarks' => null,
                'received_by' => 'Sarah Johnson',
                'file_case_no' => 'FC-2024-004',
            ],
            [
                'order_id' => '2024-KFO-137',
                'date' => '2025-01-06',
                'time' => '14:50',
                'origin' => 'KFO',
                'subject' => 'VEHICLE MAINTENANCE',
                'amount' => 21400.00,
                'forward_to' => 'SUPPLY',
                'status' => 'pending',
                'remarks' => null,
                'received_by' => 'Robert Brown',
                'file_case_no' => 'FC-2024-005',
            ],
        ];

        foreach ($orders as $order) {
            Order::create($order);
        }
    }
} 