<?php

namespace Tests\Feature;

use App\Http\Controllers\OrderMaterialIssuePdfController;
use App\Models\OrderName;
use App\Services\Reports\OrderMaterialIssueService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class OrderMaterialIssuePdfTest extends TestCase
{
    public function test_populated_report_formats_quantities_and_renders_multiple_pages(): void
    {
        $data = [
            'order' => new OrderName(['name' => 'Замовлення № 125']),
            'rows' => collect(range(1, 80))->map(fn ($id) => [
                'code' => '000'.$id,
                'article' => 'А-'.$id,
                'name' => 'Лист сталевий 3 мм для виготовлення деталей',
                'unit' => 'кг',
                'quantity' => 125.5,
                'documents' => [15, 21, 38],
            ]),
            'generatedAt' => now(),
        ];
        $html = view('pdf.order-material-issue', $data)->render();
        $this->assertStringContainsString('125,5', $html);
        $this->assertStringContainsString('15, 21, 38', $html);
        $this->assertStringNotContainsString('немає матеріалів', $html);

        $pdf = Pdf::loadView('pdf.order-material-issue', $data)->setPaper('a4', 'landscape');
        $this->assertStringStartsWith('%PDF-', $pdf->output());
        $this->assertGreaterThan(1, $pdf->getDomPDF()->getCanvas()->get_page_count());
    }

    public function test_report_filters_by_order_and_posted_status_and_renders_empty_pdf(): void
    {
        $order = new OrderName(['name' => 'Тестове замовлення']);
        $order->id = 123;
        $response = null;

        // Pretend executes no SQL and does not modify the application's database.
        $queries = DB::connection()->pretend(function () use ($order, &$response) {
            $response = (new OrderMaterialIssuePdfController)($order, new OrderMaterialIssueService);
        });

        $this->assertCount(1, $queries);
        $this->assertSame([123, 'posted'], $queries[0]['bindings']);
        $this->assertStringContainsString('order_name_id', $queries[0]['query']);
        $this->assertStringContainsString('status', $queries[0]['query']);
        $this->assertSame('application/pdf', $response->headers->get('Content-Type'));
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $html = view('pdf.order-material-issue', [
            'order' => $order, 'rows' => collect(), 'generatedAt' => now(),
        ])->render();
        $this->assertStringContainsString('немає матеріалів у проведених документах', $html);
    }
}
