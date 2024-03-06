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


        $this->fillNaimiz;

        $this->fillMaterialsFromM6mt();


        //$this->fillSpecification();

        echo 'Програма почалась '.$start_time.PHP_EOL;
        echo 'Програма закінчилась '.now().PHP_EOL;

        echo 'Команда успешно выполнена!';

    }

    public function fillDesignationFromM6piName(){

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

}
