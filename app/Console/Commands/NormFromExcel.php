<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\DesignationMaterial;
use App\Models\Material;
use App\Models\Norm69;
use App\Models\Norm71;
use App\Models\Norm73;
use App\Models\TypeUnit;
use App\Services\HelpService\HelpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NormFromExcel extends Command
{
    public $typeUnits;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:norm-from-excel';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        /*$items = ReportApplicationStatement::with(['designationMaterial.material', 'designation'])
            ->get();
        $i=0;
        foreach($items as $item){
            if( !isset($item->designationMaterial->material->name)) continue;
                echo $item->designationMaterial->material->name.PHP_EOL;
                echo $item->designation->designation.PHP_EOL;
                //dd($item->designation->designation);

        }*/
        //$this->cleanDataNormExcel();

        $this->addNorm();

    }

    public function addNorm()
    {
        $i=0;
        $norms = Norm69::all();
        $count_material = 0;
        $count_detail = 0;
        $count_designation_material = 0;
        $this->typeUnits = TypeUnit::all()->pluck('id','unit')->toArray();
        foreach($norms as $norm){
            $i++;
            $begin_designation = $norm->designation_number;
            $begin_material = $norm->material;
            if($norm->material=='' || $norm->norm=='' || $norm->designation_number==''){
                continue;
            }

            $find_designation = $this->getClearName($norm->designation_number);
            //echo 'CLEAR NAME'.PHP_EOL;
            //echo $find_designation.PHP_EOL;
            //echo $norm->designation_number.PHP_EOL;
            $find_material = HelpService::getClearName($norm->material);

            if (!(preg_match('/^Н\d+$/', $find_designation)) && !(preg_match('/^КР\d+/', $find_designation)) ) {

                $find_designation = HelpService::transformNumber($find_designation,1);

            }
            //echo $find_material.PHP_EOL;
            $designation = Designation::where('designation',$find_designation)->first();
            $find_material_without_space =  $find_material;
            $find_material = str_replace(' ', '', $find_material);
            $material = Material::whereRaw("REPLACE(name, ' ', '') = ?", [$find_material])->first();
          //  $material = Material::where('name', $find_material)->first();
            //if($i>10)
                //exit;
            if(!isset($material->id)){
                Log::info('Материал не найден');
                Log::info($begin_material);
                echo 'Материал не найден'.PHP_EOL;
                echo $begin_material.PHP_EOL;
                echo $find_material.PHP_EOL;
                $unit = trim($norm->type_unit);
                $count_material++;
                $answer = $this->ask('Можно ли добавлять эту запись в материалы? (y/n)');
                if(strtolower($answer ) === 'y'){
                    $material = $this->addmaterial($find_material_without_space,$unit);
                }else{
                    continue;
                }

                //
            }
            if (!isset($designation->id)) {
                $designation = $this->createDesignation($find_designation,$norm->designation_name,$norm->designation_number);
                echo 'не найдена деталь' . PHP_EOL;
                $count_detail++;
                echo $begin_designation . PHP_EOL;
                echo $find_designation . PHP_EOL;
                echo '-------------'. PHP_EOL;
            }



            $count_designation_material++;
            $this->createDesignationMaterial($designation->id,$material->id,$begin_designation,$begin_material,$norm->norm);

        }
        Log::info('Деталей не найдено '.$count_detail);
        Log::info('Материалов не найдено '.$count_material);
        Log::info('Норм добавлено или обновлено '.$count_designation_material);
        echo 'Материалов не найдено '.$count_material.PHP_EOL;
        echo 'Деталей не найдено '.$count_detail.PHP_EOL;
        echo 'Норм добавлено или обновлено '.$count_designation_material.PHP_EOL;
    }

    public function addmaterial($name,$unit ){
        return Material::Create([
            'name' => $name,
            'type_unit_id' => $this->typeUnits[$unit]
            ]
        );
    }
    public function createDesignation($designation_number,$designation_name,$designation_from_excel)
    {
        return Designation::create([
            'designation' => $designation_number,
            'designation_from_excel' => $designation_from_excel,
            'name' => $designation_name,
        ]);
    }

    public function createDesignationMaterial($designationId,$materialId,$designation_from_excel,$material_from_excel,$norm)
    {
        $norm = str_replace(',', '.', $norm);
        if(!is_numeric($norm)){
            echo 'not numeric '.$norm.PHP_EOL;
            return;
        }
        Log::info('$designationId '.$designationId);
        Log::info('$materialId '.$materialId);
        DesignationMaterial::updateOrCreate(
            [
            'designation_id' => $designationId,
            'material_id' => $materialId
            ],
            [
            'designation_from_excel' => $designation_from_excel,
            'material_from_excel' => $material_from_excel,
            'norm' => $norm,
            'department_id' => 5
        ]);
    }

    public function transformDataDesignation()
    {
       // $designations = Designation::where('designation', 'LIKE', 'ААМВ%')->get();
        $designations = Designation::where('designation', 'LIKE', 'НТИЯ%')->get();

        $i = 0;
        foreach ($designations as $designation) {
            //надо удалить 93162, 68759, 71034
            //надо удалить 71670, 71671
            //if(!($norm->id==68689 || $norm->id==68758 || $norm->id==71331))
              //  continue;
            $designation_number = $designation->designation;
            $prefix = 'НТИЯ';
            $i++;
            // Убираем префикс "ААМВ"
            $number = substr($designation_number, strlen($prefix));
            $number = str_replace('-', '', $number);
            // Если количество цифр меньше 9, добавляем нули в конец
            if (strlen($number) < 9) {
                 '<9'.PHP_EOL;
                echo $number.PHP_EOL;

                $number = str_pad($number, 9, '0', STR_PAD_RIGHT);

            }

            // Если количество цифр больше 9, обрезаем до 9 и добавляем дефис и две последние цифры
            if (strlen($number) > 9) {
                echo '>9'.PHP_EOL;

                echo $number.PHP_EOL;

                $number = substr($number, 0, 9) . '-' . substr($number, 9);

            }

            // Формируем новое значение поля designation_number
            $new_designation_number = $prefix . $number;
            echo $new_designation_number.PHP_EOL;
            $designation->update(['designation_number' =>  $designation->designation]);

            //if($i>115){
             //   exit;
            //}
            // Обновляем запись в базе данных
            $designation->update(['designation' => $new_designation_number]);
        }
    }

    public function cleanDataNormExcel()
    {
        $norms = Norm71::all();

        foreach($norms as $norm){
            if ($norm->designation_number == '' || $norm->material == '' || $norm->norm == '' ) {
                Norm71::where('number', $norm->number)->delete();

            }else{
                $designation_number = $this->getClearName($norm->designation_number);
                Norm71::where('number', $norm->number)->update([
                    'designation_number' => $designation_number,
                ]);
            }
        }
    }
    public function getClearName($string)
    {
        $string = trim($string);

        // Видалення всіх точок
        $string = str_replace('.', '', $string);

        // Удаление подстроки "СБ"
        $string = str_replace('СБ', '', $string);

        // Удаление всех пробельных символов (включая пробелы и символы переноса строки)
        $string = preg_replace('/\s+/', '', $string);

        // Удаляем подстроку "(А1)" или "(А2)"
        $string = preg_replace('/\s*\((А1|А2)\)/', '', $string);

        //echo $string; // Выведет: ААМВ745112066
        return $string;
    }
}
