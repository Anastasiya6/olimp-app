<?php

namespace Tests\Unit;

use App\Models\ImportMaterial;
use App\Models\MaterialIssuanceItem;
use App\Models\TypeUnit;
use App\Services\Reports\OrderMaterialIssueService;
use PHPUnit\Framework\TestCase;

class OrderMaterialIssueServiceTest extends TestCase
{
    public function test_totals_use_issued_quantity_and_unique_document_numbers(): void
    {
        $rows = (new OrderMaterialIssueService)->summarize(collect([
            $this->item(1, 15, 12.5),
            $this->item(1, 15, 2.25),
            $this->item(1, 21, 3),
            $this->item(2, 21, 7),
        ]));

        $this->assertCount(2, $rows);
        $this->assertEquals(17.75, $rows[0]['quantity']);
        $this->assertSame([15, 21], $rows[0]['documents']);
        $this->assertSame('кг', $rows[0]['unit']);
        $this->assertEquals(7, $rows[1]['quantity']);
    }

    public function test_missing_materials_remain_separate_and_empty_input_is_supported(): void
    {
        $first = $this->item(1, 15, 2)->setRelation('importMaterial', null);
        $second = $this->item(2, 15, 3)->setRelation('importMaterial', null);
        $service = new OrderMaterialIssueService;
        $rows = $service->summarize(collect([$first, $second]));

        $this->assertCount(2, $rows);
        $this->assertSame('Матеріал 1С #1', $rows[0]['name']);
        $this->assertSame('-', $rows[0]['unit']);
        $this->assertTrue($service->summarize(collect())->isEmpty());
    }

    private function item(int $materialId, int $documentId, float $quantity): MaterialIssuanceItem
    {
        // Identical names must not merge different warehouse positions.
        $material = new ImportMaterial(['name' => 'Лист сталевий', 'code' => '001', 'type_unit_id' => 1]);
        $material->setRelation('unit', new TypeUnit(['unit' => 'кг']));

        return (new MaterialIssuanceItem([
            'import_material_id' => $materialId,
            'material_issuance_id' => $documentId,
            'quantity' => $quantity,
            'fact_quantity' => 999,
        ]))->setRelation('importMaterial', $material);
    }
}
