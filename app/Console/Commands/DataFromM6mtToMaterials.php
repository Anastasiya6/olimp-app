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

        $this->fillTypeUitFromNaimiz();
        //dd(print_r(Naimiz::all()->pluck('naimiz', 'ediz')->toArray(),1));
        //$this->fillMaterialsFromM6mt();

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

                'name' => $record->get('naimiz')
            ]);
            echo $record->get('naimiz') . PHP_EOL;

        }
    }


    public function fillMaterialsFromM6mt()
    {

        $table = new TableReader(
            'c:\Mass\M6mt.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {

            $material = Material::where('code', $record->get('nm'))->first();

            if (!empty($material)) {

                $material->update([
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'type_units' => $record->get('ediz'),
                ]);

            } else {
                $this->getTypeUnitId();
                Material::create([
                    'code' => $record->get('nm'),
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'type_units' => $record->get('ediz'),
                ]);
            }
            echo $record->get('naim') . PHP_EOL;

        }
    }
    public function getTypeUnitId()
    {

    }
}
