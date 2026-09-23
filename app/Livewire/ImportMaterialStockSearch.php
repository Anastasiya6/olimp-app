<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\ImportMaterial;
use App\Models\ImportMaterialStaging;
use App\Models\ImportMaterialStock;
use App\Models\MaterialIssuance;
use App\Models\MaterialIssuanceItem;
use App\Models\TypeUnit;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class ImportMaterialStockSearch extends Component
{
    use WithPagination;

    use WithFileUploads;

    public $searchTerm;

    public $route = 'import-material-stocks';

    public $file;

    public $stockInDepartmentId = '';

    public $confirmingStockIn = false;

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls'
    ];

    public function viewStock()
    {
        $this->reset('file');
        $this->resetValidation();
        $this->dispatch('opening-stock-open');
    }

    public function viewStockIn()
    {
        $this->reset('file', 'stockInDepartmentId', 'confirmingStockIn');
        $this->resetValidation();
        $this->dispatch('stock-in-open');
    }

    public function confirmStock()
    {
        $this->validate();
        $this->dispatch('opening-stock-confirm');
    }

    public function confirmStockIn()
    {
        $this->validateStockIn();
        $this->confirmingStockIn = true;
    }

    private function validateStockIn()
    {
        $this->validate([
            'file' => 'required|mimes:xlsx,xls',
            'stockInDepartmentId' => 'required|integer|exists:departments,id',
        ], [
            'file.required' => 'Оберіть файл приходу з 1С.',
            'file.mimes' => 'Оберіть файл Excel у форматі .xlsx або .xls.',
            'stockInDepartmentId.required' => 'Оберіть цех для імпорту.',
            'stockInDepartmentId.exists' => 'Вибраний цех не знайдено.',
        ]);
    }


    public function unloadingStock()
    {
        $this->validate();

        $path = $this->file->getRealPath();

        ImportMaterialStock::query()->delete();
        MaterialIssuanceItem::query()->delete();
        MaterialIssuance::query()->delete();
        ImportMaterial::query()->delete();
        ImportMaterialStaging::query()->delete();
        // Читаємо файл
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();

        $units = $this->getUnits();

        // Пробігаємось по рядках
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Пропускаємо заголовок

            $code = $row[1] ?? null;
            $article = $row[2] ?? null;
            $name = $row[3];

            $quantity = $row[4] ?? null;
            $quantity = str_replace(',', '', $quantity);
            $quantity = (float) $quantity;

            $unitId  = $this->getKeyUnit($row[5],$units);


            $material = ImportMaterial::firstOrCreate(
                ['code' => $code, 'article' => $article], // умова пошуку
                [
                    'name' => $name,
                    'type_unit_id' => $unitId,
                ]
            );

            if ($quantity > 0) {
                $material->stocks()->firstOrCreate(
                    [], // умова пошуку: перший stock
                    ['amount' => 0] // якщо створюємо новий — дефолтне amount
                )->increment('amount', $quantity);
            }

        }

        $this->reset('file');

        $this->dispatch('opening-stock-close');

        session()->flash('success', 'Залишки успішно імпортовано');

    }

    public function unloadingStockIn()
    {

        $this->validateStockIn();
        $selectedDepartment = Department::findOrFail($this->stockInDepartmentId);

        $path = $this->file->getRealPath();

        // Читаємо файл
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        // Пробігаємось по рядках
        $documentDate = null;
        $currentDepartment = null;
        $selectedDepartmentCode = trim((string) $selectedDepartment->name);
        $imported = 0;
        $conflicts = 0;
        $skipped = 0;

        $units = $this->getUnits();


        DB::transaction(function () use ($worksheet, $selectedDepartment, $selectedDepartmentCode, $units, &$documentDate, &$currentDepartment, &$imported, &$conflicts, &$skipped) {
            foreach ($worksheet->toArray() as $row) {

                if (str_contains((string) ($row[1] ?? ''), 'Переміщення за період')) {
                    $currentDepartment = null;
                    preg_match('/з\s*(\d{2}\.\d{2}\.\d{4})/', $row[1], $m);
                    $documentDate = $m[1] ?? null;
                    if ($documentDate) {
                        $documentDate = \Carbon\Carbon::createFromFormat('d.m.Y', $documentDate)->format('Y-m-d');
                    }
                    continue;
                }

                if (str_contains((string) ($row[0] ?? ''), 'Кому:')) {

                    $raw = trim(str_replace('Кому:', '', (string) ($row[1] ?? '')));

                    $departmentId = preg_match('/^(\d+)\s*(?:[-–—]|$)/u', $raw, $departmentMatch) ? $departmentMatch[1] : null;

                    // например
                    $currentDepartment = $departmentId;

                    continue;
                }

                if (empty($row[0]) || $row[0] === '№ накл.') {

                    continue;
                }

                if (is_numeric($row[0]) && $currentDepartment !== null
                    && ctype_digit($selectedDepartmentCode)
                    && (int) $currentDepartment === (int) $selectedDepartmentCode) {

                    $documentNumber = $row[0];
                    $article = $row[1];
                    $name = $row[2];
                    $quantity = $row[4];

                    Log::info($row[0]);
                    Log::info($row[1]);
                    Log::info($row[2]);
                    Log::info($row[3]);
                    Log::info($row[4]);


                   $unitId  = $this->getKeyUnit($row[3],$units);
                   $departmentId = $selectedDepartment->id;
                   $material = null;
                   Log::info( 'Відділ '.$currentDepartment . ' '.$departmentId.' '.$unitId );
                   if( $departmentId  && $unitId ){

                       $materials = ImportMaterial::where('article', $article)->get();
                       $materialsCount = $materials->count();

                       if ($materialsCount === 0) {
                           $material = ImportMaterial::create([
                               'article' => $article,
                               'name' => $name,
                               'type_unit_id' => $unitId,
                           ]);

                       } elseif ($materialsCount === 1) {

                           $material = $materials->first();

                       } else {
                           $conflicts++;
                           Log::info('conflict');
                           ImportMaterialStaging::create([
                               'article' => $article,
                               'name' => $name,
                               'quantity' => $quantity,
                               'document_number' => $documentNumber,
                               'document_date' => $documentDate,
                               'department_id' => $departmentId,
                               'type_unit_id' => $unitId,
                               'status' => 'conflict',
                           ]);


                       }

                       if($material){
                           $imported++;
                           $material->stocks()->create([
                               'document_number' => $documentNumber,
                               'document_date' => $documentDate,
                               'department_id' => $departmentId,
                               'amount' => $quantity,
                               'type' => 'stock_in',
                           ]);
                       }
                   } else {
                       $skipped++;
                   }
               }
            }
        });
        $spreadsheet->disconnectWorksheets();

        if ($imported === 0 && $conflicts === 0) {
            $this->confirmingStockIn = false;
            $this->addError('file', 'Для вибраного цеху немає рядків для імпорту. Перевірте цех у секції «Кому:» та одиниці виміру у файлі.');
            return;
        }

        $this->reset('file', 'stockInDepartmentId', 'confirmingStockIn');
        $this->resetPage();

        $this->dispatch('stock-in-close');

        //$this->dispatch('open-modal', name: 'viewMaterialConflict');
        session()->flash('success', "Цех {$selectedDepartment->name}: імпортовано рядків — {$imported}; конфліктів артикулів — {$conflicts}; пропущено через невідому одиницю виміру — {$skipped}.");

    }

    public function unloadingCode()
    {
        $this->validate();

        $path = $this->file->getRealPath();

        ImportMaterialStock::query()->delete();
        ImportMaterial::query()->delete();
        // Читаємо файл
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = $worksheet->toArray();

        $units = $this->getUnits();

        // Пробігаємось по рядках
        foreach ($rows as $index => $row) {
            if ($index === 0) continue; // Пропускаємо заголовок

            $code = $row[1] ?? null;
            $article = $row[2] ?? null;

            if ($article !== null) {
                $material = ImportMaterial::firstOrCreate(
                    ['article' => $article],
                    ['code' => $code]
                );

                if (empty($material->code)) {
                    $material->code = $code;
                    $material->save();
                }
            }

        }

        $this->reset('file');

        $this->dispatch('opening-stock-close');

        session()->flash('success', 'Залишки успішно імпортовано');

    }

    private function getUnits(){
        return TypeUnit::pluck('id', 'unit')->toArray();
    }

    private function getKeyUnit($value,$units){

        $unit = rtrim(trim($value), '.');
        $unitKey = $unit ?? null;
        return $units[$unitKey] ?? null;
    }

    private function getDepartmentId($value,$departments){
        $department = $value ?? null;
        return $departments[$department] ?? null;
    }

    public function updateSearch()
    {
        $this->resetPage();
    }

    protected function importMaterialStocks()
    {
        $searchTerm = '%' . trim($this->searchTerm) . '%';

        if ($searchTerm == '%%') {

            return ImportMaterialStock
                ::with('materials','unit')
               // ->orderBy('document_number','desc')
                ->orderBy('updated_at','desc')
                ->paginate(25);

        } else {

            return ImportMaterialStock
                ::whereHas('materials', function ($query) use ($searchTerm) {
                    $query->where('article', 'like', $searchTerm)
                        ->orderByRaw("CAST(article AS SIGNED)");
                })
                ->with('materials','unit')
                ->orderBy('document_number','desc')
                ->orderBy('updated_at','desc')
                ->paginate(25);
        }
    }

    public function render()
    {
        return view('livewire.import-material-stock-search',[
            'items' => $this->importMaterialStocks(),
            'route' => $this->route,
            'stockInDepartments' => Department::orderBy('name')->get(),
        ]);
    }
}
