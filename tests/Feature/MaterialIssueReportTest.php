<?php

namespace Tests\Feature;

use App\Http\Controllers\MaterialIssueReportController;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class MaterialIssueReportTest extends TestCase
{
    public function test_detail_report_uses_submitted_order_and_trimmed_designation(): void
    {
        $request = Request::create('/material-issuance-report', 'GET', [
            'order_id' => '321', 'designation_number' => '  ААМВ12345  ', 'report' => 'detail',
        ]);
        $response = (new MaterialIssueReportController)($request);

        $this->assertSame(route('material.issue.pdf', [
            'order_name_id' => 321, 'designation_number' => 'ААМВ12345',
        ]), $response->getTargetUrl());
    }

    public function test_order_report_uses_submitted_order_without_requiring_designation(): void
    {
        $request = Request::create('/material-issuance-report', 'GET', [
            'order_id' => '654', 'designation_number' => '', 'report' => 'order',
        ]);
        $response = (new MaterialIssueReportController)($request);

        $this->assertSame(route('material.issue.order.pdf', ['order' => 654]), $response->getTargetUrl());
    }

    public function test_detail_report_rejects_blank_designation(): void
    {
        $this->expectException(ValidationException::class);
        (new MaterialIssueReportController)(Request::create('/material-issuance-report', 'GET', [
            'order_id' => '321', 'designation_number' => '   ', 'report' => 'detail',
        ]));
    }
}
