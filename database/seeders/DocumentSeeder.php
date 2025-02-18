<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Document;
use Carbon\Carbon;

class DocumentSeeder extends Seeder
{
    public function run()
    {
        $sampleDocuments = [
            [
                'origin' => 'DOLE Central Office',
                'subject' => 'Annual Budget Planning Guidelines 2024',
                'forward_to' => 'PLANNING',
                'remarks' => 'For immediate review and implementation',
            ],
            [
                'origin' => 'Regional Governor Office',
                'subject' => 'COVID-19 Workplace Safety Protocols Update',
                'forward_to' => 'HRMO',
                'remarks' => 'Please disseminate to all departments',
            ],
            [
                'origin' => 'DTI Regional Office',
                'subject' => 'Joint Memorandum on SME Support Programs',
                'forward_to' => 'TSSD',
                'remarks' => 'For review and comments',
            ],
            [
                'origin' => 'DOLE Secretary Office',
                'subject' => 'Q2 Performance Metrics Requirements',
                'forward_to' => 'IMSD',
                'remarks' => 'Please prepare necessary reports',
            ],
            [
                'origin' => 'Commission on Audit',
                'subject' => 'Annual Audit Report FY 2023',
                'forward_to' => 'ACCOUNTING',
                'remarks' => 'For compliance and action',
            ],
        ];

        foreach ($sampleDocuments as $doc) {
            $document = Document::create([
                'doc_id' => Document::generateDocId(),
                'date_received' => Carbon::now(),
                'time_received' => Carbon::now(),
                'origin' => $doc['origin'],
                'subject' => $doc['subject'],
                'forward_to' => $doc['forward_to'],
                'remarks' => $doc['remarks'],
                'status' => 'pending'
            ]);

            // Create initial route
            $document->routeTo($doc['forward_to']);
        }
    }
} 