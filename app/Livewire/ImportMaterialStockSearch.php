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

class ImportMaterialStockSearch extends Component
{
    use WithPagination;

    use WithFileUploads;

    public $searchTerm;

    public $route = 'import-material-stocks';

    public $file;

    protected $rules = [
        'file' => 'required|mimes:xlsx,xls'
    ];

    public function viewStock()
    {
        $this->dispatch('open-modal',name:'viewStock');
    }

    public function viewStockIn()
    {
        $this->dispatch('open-modal',name:'viewStockIn');
    }

    public function confirmStock()
    {
        $this->dispatch('open-modal', name: 'confirmStock');
    }

    public function confirmStockIn()
    {
        $this->dispatch('open-modal', name: 'confirmStockIn');
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

        $this->dispatch('close-modal', name: 'confirmStock');
        $this->dispatch('close-modal', name: 'viewStock');

        session()->flash('success', 'Залишки успішно імпортовано');

    }

    public function unloadingStockIn()
    {
        $this->validate();

        $path = $this->file->getRealPath();

        // Читаємо файл
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();

        // Пробігаємось по рядках
        $documentDate = null;
        $currentDepartment = null;
        $array_department = [25,207,208];

        $units = $this->getUnits();
        $departments = Department::pluck('id', 'name')->toArray();

        foreach ($worksheet->toArray() as $row) {

            if (str_contains($row[1], 'Переміщення за період')) {
                preg_match('/з\s*(\d{2}\.\d{2}\.\d{4})/', $row[1], $m);
                $documentDate = $m[1] ?? null;
                if ($documentDate) {
                    $documentDate = \Carbon\Carbon::createFromFormat('d.m.Y', $documentDate)->format('Y-m-d');
                }
                continue;
            }

            if (str_contains($row[0], 'Кому:')) {

                $raw = trim(str_replace('Кому:', '', $row[1]));

                $departmentId = (int) trim(strtok($raw, '-'));

                // например
                $currentDepartment = $departmentId;

                continue;
            }

            if ($row[0] === '№ накл.' || empty($row[0])) {

                continue;
            }

            if (is_numeric($row[0]) && in_array($currentDepartment,$array_department)) {

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
               $departmentId = $this->getDepartmentId($currentDepartment,$departments);
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
                       $material->stocks()->create([
                           'document_number' => $documentNumber,
                           'document_date' => $documentDate,
                           'department_id' => $departmentId,
                           'amount' => $quantity,
                           'type' => 'stock_in',
                       ]);
                   }
               }
           }
        }

        $this->reset('file');

        $this->dispatch('close-modal', name: 'confirmStockIn');
        $this->dispatch('close-modal', name: 'viewStock');

        $this->dispatch('open-modal', name: 'viewMaterialConflict');
        //$this->dispatch('open-modal', name: 'viewMaterialConflict');
        session()->flash('success', 'Залишки успішно імпортовано');

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

        $this->dispatch('close-modal', name: 'confirmStock');
        $this->dispatch('close-modal', name: 'viewStock');

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
            'route' => $this->route
        ]);
    }
}
