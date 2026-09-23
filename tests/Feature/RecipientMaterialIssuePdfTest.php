<?php

namespace Tests\Feature;

use App\Http\Controllers\RecipientMaterialIssuePdfController;
use App\Models\User;
use App\Services\Reports\OrderMaterialIssueService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecipientMaterialIssuePdfTest extends TestCase
{
    public function test_report_selects_recipient_across_orders_and_manual_issuances(): void
    {
        $recipient = new User(['name' => 'Іван Петренко']);
        $recipient->id = 42;
        $response = null;

        $queries = DB::connection()->pretend(function () use ($recipient, &$response) {
            $response = (new RecipientMaterialIssuePdfController)($recipient, new OrderMaterialIssueService);
        });

        $this->assertCount(1, $queries);
        $this->assertSame([42], $queries[0]['bindings']);
        $this->assertStringContainsString('received_by_user_id', $queries[0]['query']);
        $this->assertStringNotContainsString('issued_by_user_id', $queries[0]['query']);
        $this->assertStringNotContainsString('order_name_id', $queries[0]['query']);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());

        $html = view('pdf.recipient-material-issue', [
            'recipient' => $recipient, 'rows' => collect(), 'generatedAt' => now(),
        ])->render();
        $this->assertStringContainsString('Іван Петренко', $html);
        $this->assertStringContainsString('немає матеріалів', $html);
        $this->assertStringContainsString('включно з непроведеними', $html);
    }

    public function test_populated_report_renders_multiple_pages_with_quantities_and_documents(): void
    {
        $data = [
            'recipient' => new User(['name' => 'Іван Петренко']),
            'rows' => collect(range(1, 80))->map(fn ($id) => [
                'code' => '000'.$id, 'article' => 'А-'.$id,
                'name' => 'Лист сталевий 3 мм', 'unit' => 'кг',
                'quantity' => 125.5, 'documents' => [15, 21, 38],
            ]),
            'generatedAt' => now(),
        ];
        $html = view('pdf.recipient-material-issue', $data)->render();
        $this->assertStringContainsString('125,5', $html);
        $this->assertStringContainsString('15, 21, 38', $html);
        $this->assertStringNotContainsString('немає матеріалів', $html);
        $pdf = Pdf::loadView('pdf.recipient-material-issue', $data)->setPaper('a4', 'landscape');
        $this->assertStringStartsWith('%PDF-', $pdf->output());
        $this->assertGreaterThan(1, $pdf->getDomPDF()->getCanvas()->get_page_count());
    }
}
