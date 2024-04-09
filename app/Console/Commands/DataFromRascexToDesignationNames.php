<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\Designation1;
use App\Models\DesignationTypeUnit;
use App\Models\Naimiz;
use App\Models\Specification;
use App\Models\TypeUnit;
use App\Services\HelpService\HelpService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use XBase\TableReader;

class DataFromRascexToDesignationNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:data-from-rascex-to-designation-names';

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

        //Designation1::truncate();

        $this->fillDesignationFromRascexName();

       //$this->fillSpecification();

        //$this->typeUnit();

        //$this->fillDesignationFromM6piName();


        echo 'Програма почалась '.$start_time.PHP_EOL;
        echo 'Програма закінчилась '.now().PHP_EOL;

        echo 'Команда успешно выполнена!';

    }
    public function typeUnit()
    {
        $naimizs = Naimiz::all();

        foreach($naimizs as $naimiz){
            echo  $naimiz->naimiz;
            DesignationTypeUnit::create([
                'unit' => $naimiz->naimiz,
            ]);
        }
    }
    public function fillDesignationFromRascexName()
    {
        $table = new TableReader(
            'e:\050424\Mass\Rascex.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        $i = 0;
        while ($record = $table->nextRecord()) {

            $find_designation = $record->get('chto');

            /*Пропускаем подобные этому Н021018, т.е. начиная с Н и далее цифры*/
            /*Пропускаем подобные этому КР66000160143233, т.е. начиная с КР и далее цифры*/
            /*Пропускаем подобные этому ПИ091334491853, т.е. начиная с ПИ и далее цифры*/
            /* Пропускаем если есть точка как например ААМВ464467001.1*/

            if (!(preg_match('/^Н\d+$/', $find_designation)) &&
                !(preg_match('/^КР\d+/', $find_designation)) &&
                !(preg_match('/^ПИ\d+$/', $find_designation)) &&
                !str_contains($find_designation, '.')) {

                $find_designation = HelpService::transformNumber($find_designation);

            }
           echo $find_designation.PHP_EOL;
           $designation = Designation
               ::where('designation', $find_designation)
               ->orWhere('designation_from_rascex',$record->get('chto'))
               ->first();

            if (!empty($designation) ){
                /*if($designation->designation == 'ОП4852042-001' ){
                    echo '1'.PHP_EOL;
                }*/
                if(($designation->name == '' && $record->get('naim') != '')
                    ||
                    ($designation->route == '' && $record->get('tm') != '')
                ) {
                    /*
                    if($designation->designation == 'ОП4852042-001' ){
                        echo '2'.PHP_EOL;
                        echo print_r($designation,1);
                    }*/
                    echo $designation->designation . PHP_EOL;
                    echo 'update'.PHP_EOL;
                    $designation->update([
                        'name' => $designation->name=='' ? $record->get('naim') : $designation->name,
                        'designation_from_rascex' => $record->get('chto'),
                        'route' => $designation->route=='' ? $record->get('tm') : $designation->route
                     ]);
                }
                if( ($designation->designation != $find_designation)){
                    /*if($designation->designation == 'ОП4852042-001' ){
                        echo '3'.PHP_EOL;
                        echo $designation->designation .' '. $find_designation;
                        echo print_r($designation,1);
                    }*/
                    echo $designation->designation.PHP_EOL;
                    echo $find_designation;
                    $designation->update([
                        'designation' => $find_designation,
                    ]);
                        exit;
                }

            } else {

                echo 'create'.PHP_EOL;
                try {
                    Designation::create([
                        'designation' => $find_designation,
                        'designation_from_rascex' => $record->get('chto'),
                        'name' => $record->get('naim'),
                        'route' => $record->get('tm'),
                    ]);
                } catch (\Exception $e) {
                    // Если произошла ошибка, записываем ее в лог
                    Log::info('Ошибка при выполнении запроса к базе данных: ' . $e->getMessage());
                    Log::info($find_designation);
                    Log::info( $record->get('chto'));
                    Log::info($record->get('naim'));
                    Log::info($record->get('tm'));
                }
            }
            echo $record->get('naim') . PHP_EOL;
          /*  if($designation->designation == 'ОП4852042-001' )
               exit;*/
        }

    }
    public function fillSpecification()
    {
        $table = new TableReader(
            'e:\d\Mass\M0020.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {
           /* if($record->get('ok') != 'ААМВ452849002')
                continue;*/
            $find_designation_ok = $record->get('ok');

            $find_designation_od = $record->get('od');

            /*Пропускаем подобные этому Н021018, т.е. начиная с Н и далее цифры*/
            /*Пропускаем подобные этому КР66000160143233, т.е. начиная с КР и далее цифры*/
            /*Пропускаем если есть точка как например ААМВ464467001.1*/

            if (!(preg_match('/^Н\d+$/', $find_designation_ok)) && !(preg_match('/^КР\d+/', $find_designation_ok)) && !str_contains($find_designation_ok, '.')) {

                $find_designation_ok = HelpService::transformNumber($find_designation_ok);

            }

            /*Пропускаем подобные этому Н021018, т.е. начиная с Н и далее цифры*/
            /*Пропускаем подобные этому КР66000160143233, т.е. начиная с КР и далее цифры*/
            /* Пропускаем если есть точка как например ААМВ464467001.1*/
            if (!(preg_match('/^Н\d+$/', $find_designation_od)) && !(preg_match('/^КР\d+/', $find_designation_od))  && !str_contains($find_designation_od, '.')) {

                $find_designation_od = HelpService::transformNumber($find_designation_od);

            }

            $designation = $find_designation_ok;
            $detail = $find_designation_od;

            $designationId = Designation
                ::where('designation', $designation)
                ->orWhere('designation_from_rascex',$record->get('ok'))->value('id');
            $detailId = Designation
                ::where('designation', $detail)
                ->orWhere('designation_from_rascex',$record->get('od'))->value('id');

            echo '$designationId';
            echo $designationId;
            echo !$designationId?$designation. ' нема назви' . PHP_EOL:'';

            echo '$detailId';
            echo $detailId;
            echo !$detailId?$designation. ' нема назви' . PHP_EOL:'';

            if (!$designationId){

                $designationId = $this->createDesignationName($designation,$record->get('ok'));
            }

            if (!$detailId){

                $detailId = $this->createDesignationName($detail,$record->get('od'));

            }

            if ($designationId && $detailId) {
                echo 'param';
                echo $designationId.' '.$detailId;
                try {
                    $specification = Specification::updateOrCreate([
                        'designation_id' => $designationId,
                        'designation_entry_id' => $detailId,
                    ], [
                        'quantity' => $record->get('pe'),
                        'designation' => $record->get('ok'),
                        'detail' => $record->get('od'),
                        'category_code' => $record->get('e') !== '' ? $record->get('e') : 2,
                    ]);
                } catch (\Exception $e) {
                    // Если произошла ошибка, записываем ее в лог
                    Log::info('Ошибка при выполнении запроса к базе данных: ' . $e->getMessage());
                    Log::info($designationId);
                    Log::info($detailId);
                    Log::info( $record->get('ok'));
                    Log::info($record->get('od'));
                }
            }
        }
    }

    public function createDesignationName($designation,$designation_from_rascex,$name='',$route='')
    {
        return
            Designation::create([
            'designation' => $designation,
            'designation_from_rascex' => $designation_from_rascex,
            'name' => $name,
            'route' => $route
        ])->id;
    }
    public function fillDesignationFromM6piName()
    {
        $typeUnits = DesignationTypeUnit::all()->pluck('id','unit')->toArray();
        $naimizs = Naimiz::all()->pluck('naimiz','ediz')->toArray();

       // echo print_r($typeUnits,1);
        //echo print_r($naimizs,1);

        //exit;
        $table = new TableReader(
            'e:\d\Mass\M6pi.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {

            $designation = Designation::where('designation', $record->get('nm'))->first();
            echo print_r($designation,1);
            //echo $naimizs;
            $unit = NULL;
            if($record->get('ediz')!=''){
                $unit =  $naimizs[$record->get('ediz')]??NULL;
            }
            if (!empty($designation) ){

                $designation->update([
                    'designation' => $record->get('nm'),
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'designation_type_unit_id' =>$typeUnits[$unit]??NULL,
                    'type' => 1
                ]);


            } else {
                Designation::create([
                    'designation' => $record->get('nm'),
                    'designation_from_rascex' => $record->get('nm'),
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'type_unit' => $record->get('ediz'),
                    'type' => 1

                ]);
            }
            echo $record->get('naim') . PHP_EOL;
            //echo $designation->id;
            //exit;
        }

    }
}
