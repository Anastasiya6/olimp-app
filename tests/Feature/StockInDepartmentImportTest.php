<?php

namespace Tests\Feature;

use App\Livewire\ImportMaterialStockSearch;
use App\Models\Department;
use App\Models\ImportMaterial;
use App\Models\ImportMaterialStaging;
use App\Models\ImportMaterialStock;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Tests\TestCase;

class StockInDepartmentImportTest extends TestCase
{
    private array $files = [];

    protected function setUp(): void
    {
        parent::setUp();
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('Run with php -d extension=pdo_sqlite to use an isolated in-memory database.');
        }
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('departments', function (Blueprint $table) {
            $table->id(); $table->string('name'); $table->timestamps();
        });
        Schema::create('type_units', function (Blueprint $table) {
            $table->id(); $table->string('unit'); $table->timestamps();
        });
        Schema::create('import_materials', function (Blueprint $table) {
            $table->id(); $table->string('article'); $table->string('name');
            $table->unsignedBigInteger('type_unit_id'); $table->timestamps();
        });
        Schema::create('import_material_stocks', function (Blueprint $table) {
            $table->id(); $table->unsignedBigInteger('import_material_id');
            $table->unsignedBigInteger('department_id'); $table->string('document_number');
            $table->date('document_date')->nullable(); $table->double('amount');
            $table->string('type'); $table->timestamps();
        });
        Schema::create('import_material_stagings', function (Blueprint $table) {
            $table->id(); $table->string('article'); $table->string('name');
            $table->double('quantity'); $table->string('document_number');
            $table->date('document_date')->nullable(); $table->unsignedBigInteger('department_id');
            $table->unsignedBigInteger('type_unit_id'); $table->string('status'); $table->timestamps();
        });
        DB::table('departments')->insert([
            ['id' => 1, 'name' => '25'], ['id' => 2, 'name' => '207'], ['id' => 3, 'name' => '208'],
        ]);
        DB::table('type_units')->insert(['id' => 1, 'unit' => 'кг']);
    }

    protected function tearDown(): void
    {
        foreach ($this->files as $file) {
            if (is_file($file)) unlink($file);
        }
        parent::tearDown();
    }

    public function test_only_selected_department_is_imported_and_existing_stocks_are_preserved(): void
    {
        $existing = ImportMaterial::create(['article' => 'OLD', 'name' => 'Existing', 'type_unit_id' => 1]);
        $existing->stocks()->create([
            'department_id' => 1, 'document_number' => '99', 'amount' => 5, 'type' => 'stock_in',
        ]);
        $component = $this->importComponent($this->rows(), 2);
        $component->confirmStockIn();
        $this->assertTrue($component->confirmingStockIn);
        $this->assertSame(1, ImportMaterialStock::count());
        $component->unloadingStockIn();

        $this->assertSame(2, ImportMaterialStock::count());
        $stock = ImportMaterialStock::where('document_number', '102')->firstOrFail();
        $this->assertEquals(2, $stock->department_id);
        $this->assertEquals(7.5, $stock->amount);
        $this->assertSame('2026-09-23', $stock->document_date);
        $this->assertSame('B', $stock->materials->article);
        $this->assertFalse(ImportMaterial::whereIn('article', ['A', 'C'])->exists());
        $this->assertFalse($component->confirmingStockIn);
    }

    public function test_receipt_dialog_does_not_open_the_opening_stock_confirmation(): void
    {
        $component = new ImportMaterialStockSearch;
        $component->viewStockIn();
        $events = array_map(fn ($event) => $event->serialize()['name'], \Livewire\store($component)->get('dispatched', []));
        $this->assertSame(['stock-in-open'], $events);

        $component = $this->importComponent($this->rows(), 2);
        $component->confirmStockIn();
        $this->assertTrue($component->confirmingStockIn);
        $this->assertSame([], \Livewire\store($component)->get('dispatched', []));
        $this->assertSame(0, ImportMaterialStock::count());
    }

    public function test_leading_zero_department_code_is_matched(): void
    {
        $component = $this->importComponent($this->rows(), 1);
        $component->unloadingStockIn();
        $this->assertSame(1, ImportMaterialStock::count());
        $this->assertSame('A', ImportMaterialStock::first()->materials->article);
    }

    public function test_conflict_does_not_add_quantity_to_the_previous_material(): void
    {
        foreach (['First', 'Second'] as $name) {
            ImportMaterial::create(['article' => 'DUP', 'name' => $name, 'type_unit_id' => 1]);
        }
        $rows = $this->rows();
        array_splice($rows, 5, 0, [[103, 'DUP', 'Ambiguous', 'кг', 50]]);
        $component = $this->importComponent($rows, 2);
        $component->unloadingStockIn();
        $this->assertSame(1, ImportMaterialStock::count());
        $this->assertEquals(7.5, ImportMaterialStock::sum('amount'));
        $this->assertSame(1, ImportMaterialStaging::count());
        $this->assertEquals(2, ImportMaterialStaging::first()->department_id);
    }

    public function test_missing_department_is_rejected_before_writing(): void
    {
        $component = $this->importComponent($this->rows(), '');
        try {
            $component->unloadingStockIn();
            $this->fail('A department must be required.');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('stockInDepartmentId', $exception->errors());
            $this->assertSame(0, ImportMaterialStock::count());
        }
    }

    public function test_absent_department_reports_no_matching_rows(): void
    {
        DB::table('departments')->insert(['id' => 4, 'name' => '999']);
        $component = $this->importComponent($this->rows(), 4);
        $component->unloadingStockIn();
        $this->assertSame(0, ImportMaterialStock::count());
        $this->assertSame(0, ImportMaterial::count());
        $this->assertTrue($component->getErrorBag()->has('file'));
    }

    private function importComponent(array $rows, $departmentId): ImportMaterialStockSearch
    {
        $path = tempnam(sys_get_temp_dir(), 'stock-in-test-');
        $this->files[] = $path;
        $book = new Spreadsheet;
        $book->getActiveSheet()->fromArray($rows);
        (new Xlsx($book))->save($path);
        $book->disconnectWorksheets();
        $component = new ImportMaterialStockSearch;
        $component->file = new UploadedFile($path, 'receipts.xlsx', null, null, true);
        $component->stockInDepartmentId = $departmentId;
        return $component;
    }

    private function rows(): array
    {
        return [
            [null, 'Переміщення за період з 23.09.2026'],
            ['Кому:', '025 - Цех 25'],
            [101, 'A', 'Material A', 'кг', 4],
            ['Кому:', '207 - Цех 207'],
            [102, 'B', 'Material B', 'кг', 7.5],
            ['Кому:', '208 - Цех 208'],
            [104, 'C', 'Material C', 'кг', 12],
        ];
    }
}
