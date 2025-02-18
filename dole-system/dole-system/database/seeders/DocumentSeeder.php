<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    public function run(): void
    {
        $documents = [
            [
                'doc_id' => '2024-TSSD-169',
                'date_received' => '2025-01-02',
                'time_received' => '10:50',
                'origin' => 'TSSD',
                'subject' => 'ADVOCACY MATERIALS FOR THE 2024 REGIONAL YEPA CUM CAPACITY DEVELOPMENT & AWARDS & RECOGNITION',
                'forward_to' => 'SUPPLY',
                'status' => 'pending',
                'remarks' => null,
            ],
            [
                'doc_id' => '2024-TSSD-170',
                'date_received' => '2025-01-02',
                'time_received' => '13:00',
                'origin' => 'MALSU',
                'subject' => 'MEALS & ACCOMODATION FOR THE CONDUCT OF 2024 REGIONAL YEPA CUM CAPACITY DEVELOPMENT & AWARDS RECOGNITION',
                'forward_to' => 'IMSD',
                'status' => 'pending',
                'remarks' => null,
            ],
            [
                'doc_id' => 'AB-24-11-86',
                'date_received' => '2025-01-02',
                'time_received' => '14:00',
                'origin' => 'MALSU',
                'subject' => 'INDIVIDUAL LIVELIHOOD PROJECT (REED CRAFT) OF 1 PCL',
                'forward_to' => 'IMSD',
                'status' => 'pending',
                'remarks' => null,
            ],
            [
                'doc_id' => '2024-KFO-141',
                'date_received' => '2025-01-06',
                'time_received' => '10:40',
                'origin' => 'KFO',
                'subject' => 'CATERING SERVICES ON THE CONDUCT OF TAV CUM ORIENTATION',
                'forward_to' => 'SUPPLY',
                'status' => 'pending',
                'remarks' => null,
            ],
            [
                'doc_id' => '2024-KFO-137',
                'date_received' => '2025-01-06',
                'time_received' => '14:50',
                'origin' => 'KFO',
                'subject' => 'VEHICLE MAINTENANCE',
                'forward_to' => 'SUPPLY',
                'status' => 'pending',
                'remarks' => null,
            ],
        ];

        foreach ($documents as $doc) {
            Document::create($doc);
        }
    }
} 