<?php

namespace App\Services\Reports;

use Illuminate\Support\Collection;

class OrderMaterialIssueService
{
    public function summarize(Collection $items): Collection
    {
        return $items->groupBy(fn ($item) => json_encode([
            $item->import_material_id,
            $item->importMaterial?->type_unit_id,
        ]))->map(function ($group) {
            $material = $group->first()->importMaterial;

            return [
                'code' => $material?->code ?? '-',
                'article' => $material?->article ?? '-',
                'name' => $material?->name ?? 'Матеріал 1С #'.$group->first()->import_material_id,
                'unit' => $material?->unit?->unit ?? '-',
                'quantity' => $group->sum('quantity'),
                'documents' => $group->pluck('material_issuance_id')->unique()->values()->all(),
            ];
        })->sortBy('name')->values();
    }
}
