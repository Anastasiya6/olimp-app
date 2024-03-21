<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\Designation1;
use App\Models\Material;
use App\Models\Naimiz;
use App\Models\Specification;
use App\Models\TypeUnit;
use Illuminate\Console\Command;
use XBase\TableReader;

class DataFromM6mtToMaterials extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-from-m6mt-to-materials';

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
        $start_time = now();

        //$this->fillTypeUitFromNaimiz();
        echo print_r(TypeUnit::all()->pluck( 'id','code')->toArray(),1);
        $this->fillMaterialsFromM6mt();

        echo 'Програма почалась '.$start_time.PHP_EOL;
        echo 'Програма закінчилась '.now().PHP_EOL;

        echo 'Команда успешно выполнена!';

    }
    public function fillTypeUitFromNaimiz()
    {
        $table = new TableReader(
            'c:\Mass\Naimiz.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {

            TypeUnit::updateOrCreate([
                'code' => $record->get('ediz'),
                'unit' => $record->get('naimiz'),
            ]);
            echo $record->get('naimiz') . PHP_EOL;

        }
    }

    public function fillMaterialsFromM6mt()
    {
        Material::truncate();
        $table = new TableReader(
            'c:\Mass\M6mt.dbf',
            [
                'encoding' => 'cp866'
            ]
        );

        $typeUnits = TypeUnit::all()->pluck('id','code')->toArray();

        while ($record = $table->nextRecord()) {
            if($record->get('nm')=='')
                continue;
            echo $record->get('ediz').PHP_EOL;
           // echo $typeUnits[$record->get('ediz')].PHP_EOL;
            if($record->get('nm') == 'МТ043742001' || $record->get('nm') == 'МТ043764208') {
                $type_unit_id = 4;
            }elseif(isset($typeUnits[$record->get('ediz')]) && $record->get('ediz') != ''){
                $type_unit_id = $typeUnits[$record->get('ediz')];
            }
            try {
                Material::updateOrCreate(
                    [
                        'code' => $record->get('nm'),
                    ],
                    [
                        'name' => $record->get('naim'),
                        'gost' => $record->get('gost'),
                        'type_unit_id' => $type_unit_id,
                    ]
                );
            } catch (\Exception $e) {
                echo 'Нет такого индекса '.$record->get('ediz').' '.$e->getMessage();
                continue; // Пропускаем текущую итерацию цикла
            }

            echo $record->get('naim') . PHP_EOL;

        }
    }
}
