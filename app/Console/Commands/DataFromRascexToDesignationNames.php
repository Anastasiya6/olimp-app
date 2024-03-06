<?php

namespace App\Console\Commands;

use App\Models\Designation;
use App\Models\Designation1;
use App\Models\Specification;
use Illuminate\Console\Command;
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

       // $this->fillDesignationFromM6piName();


        //$this->fillSpecification();

        echo 'Програма почалась '.$start_time.PHP_EOL;
        echo 'Програма закінчилась '.now().PHP_EOL;

        echo 'Команда успешно выполнена!';

    }
    public function fillDesignationFromRascexName()
    {
        $table = new TableReader(
            'c:\Mass\Rascex.dbf',
            [
                'encoding' => 'cp866'
            ]
        );

        while ($record = $table->nextRecord()) {

            $designation = Designation::where('designation', $record->get('chto'))->first();

            if (!empty($designation) ){
                if(($designation->name == '' || $designation->route == '')
                    &&
                    ($record->get('naim') != '' || $record->get('tm') != '')
                ) {

                    echo $designation->designation . PHP_EOL;

                    $designation->update([
                        'name' => $designation->name ?? $record->get('naim'),
                        'route' => $designation->route ?? $record->get('tm')
                    ]);
                }

            } else {
                Designation::create([
                    'designation' => $record->get('chto'),
                    'name' => $record->get('naim'),
                    'route' => $record->get('tm'),
                ]);
            }
            echo $record->get('naim') . PHP_EOL;

        }

    }

    public function fillDesignationFromM6piName()
    {

        $table = new TableReader(
            'c:\Mass\M6pi.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {

            $designation = Designation::where('designation', $record->get('nm'))->first();

            if (!empty($designation) ){

                $designation->update([
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'type_units' => $record->get('ediz'),
                ]);


            } else {
                Designation::create([
                    'designation' => $record->get('nm'),
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'type_units' => $record->get('ediz'),
                    'type' => 1

                ]);
            }
            echo $record->get('naim') . PHP_EOL;

        }

    }

    public function fillSpecification(){
        $table = new TableReader(
            'c:\Mass\M0020.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {
            $designation = $record->get('ok');
            $detail = $record->get('od');

            $designationId = Designation::where('designation', $designation)->value('id');
            $detailId = Designation::where('designation', $detail)->value('id');

            echo '$designationId';
            echo $designationId;
            echo !$designationId?$designation. ' нема назви' . PHP_EOL:'';

            echo '$detailId';
            echo $detailId;
            echo !$detailId?$designation. ' нема назви' . PHP_EOL:'';

            if (!$designationId){

                $designationId = $this->createDesignationName($designation);
            }

            if (!$detailId){

                $detailId = $this->createDesignationName($detail);

            }

            if ($designationId && $detailId) {
                echo 'param';
                echo $designationId.' '.$detailId;
                $specification = Specification::firstOrCreate([
                    'designation_id' => $designationId,
                    'designation_entry_id' => $detailId,
                ], [
                    'quantity' => $record->get('pe'),
                    'designation' => $designation,
                    'detail' => $detail,
                    'category_code' => $record->get('e') !== '' ? $record->get('e') : 2,
                ]);
            }
        }
    }

    public function createDesignationName($designation,$name='',$route=''){
        return
            Designation::create([
            'designation' => $designation,
            'name' => $name,
            'route' => $route
        ])->id;
    }
}
