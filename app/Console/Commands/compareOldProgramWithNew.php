<?php

namespace App\Console\Commands;

use App\Models\B2012;
use App\Services\Statements\ApplicationStatementPrintService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class compareOldProgramWithNew extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:compare-old-program-with-new';

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
        $oldData = B2012::orderBy('chto')->orderBy('kuda')->orderBy('zakaz')->orderBy('e')->get()->map(function ($item) {
            return [
                'chto' => $item->chto,
                'kuda' => $item->kuda,
                'zakaz' => $item->zakaz,
                'name' => $item->naim,
                'tm' => $item->tm,
                'tm1' => $item->tm1,
                'hcp' => $item->hcp,
                'kols' => $item->kols,
                'kolzak' => $item->kolzak,
            ];
        });

        $queryResult = ApplicationStatementPrintService::queryAppStatement();

        $newData = collect($queryResult)->map(function ($item) {
            return [
                'chto' => $item->chto,
                'kuda' => $item->kuda,
                'zakaz' => $item->zakaz,
                'name' => $item->chto_name,
                'tm' => $item->tm,
                'tm1' => $item->tm1,
                'hcp' => $item->hcp,
                'kols' => $item->kols,
                'kolzak' => $item->kolzak,
            ];
            });
        //echo print_r($newData[5],1);
        //exit;
        echo '$oldData '. count($oldData);
        echo '$newData '.count($newData).PHP_EOL;

        $i = 0;
        $mistake_array = array();
        $mistake = 0;
        if(count($oldData) == count($newData)) {
           foreach ($oldData as $key => $array1) {
              //  echo print_r($array1,1);
             //   echo print_r($newData[$key],1);
               $difference = array_diff($array1, $newData[$key]);
               $i++;
               if (!empty($difference)) {
                   echo "Различия найдены: $i\n";
                   print_r($difference);
                   $mistake_array[] = $i;
                   $mistake++;
                   echo print_r($array1,1);
                   echo print_r($newData[$key],1);
               }


               if($i>1115){
                  //exit;
               }
           }
            echo 'Ошибок '.$mistake;
            echo 'записи '.print_r($mistake_array,1);
            echo 'Ошибок '.$mistake;


        }else{
           echo 'Количество элементов в массивах не равны';
       }
        //$difference->all();
        //echo print_r($newData,1);
        //$difference = $oldData->diffAssoc($newData);
        //echo $difference;
    }

}
