<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaterialIssueReportController extends Controller
{
    public function __invoke(Request $request)
    {
        $request->merge(['designation_number' => trim((string) $request->input('designation_number', ''))]);
        $data = $request->validate([
            'order_id' => ['required', 'integer', 'min:1'],
            'report' => ['required', 'in:detail,order'],
            'designation_number' => ['required_if:report,detail', 'nullable', 'string', 'max:255'],
        ], [
            'designation_number.required_if' => 'Вкажіть позначення деталі для звіту по деталі та замовленню.',
        ]);

        if ($data['report'] === 'order') {
            return redirect()->route('material.issue.order.pdf', ['order' => $data['order_id']]);
        }

        return redirect()->route('material.issue.pdf', [
            'order_name_id' => $data['order_id'],
            'designation_number' => $data['designation_number'],
        ]);
    }
}
