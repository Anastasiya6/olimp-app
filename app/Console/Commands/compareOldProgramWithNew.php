<?php

namespace App\Console\Commands;

use App\Models\B2012;
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

        $queryResult = DB::select('
                                    SELECT
                                        designations1.designation AS chto,
                                        designations2.designation AS kuda,
                                        designations1.name,
                                        min(report_application_statements.quantity) AS kols,
                                        sum(report_application_statements.quantity_total) as kolzak,
                                        order_number as zakaz,

                                        CASE
                                            WHEN (designations1.route="" && designations2.route!="") THEN "99"
                                            WHEN (designations1.designation=designations2.designation && designations1.route="" && designations2.route="") THEN "99"
                                            WHEN (SUBSTRING(designations1.route, 1, 2) = SUBSTR(designations2.route,1,2)  && designations1.route != "") THEN designations1.route
                                            WHEN SUBSTRING(designations2.route, 1, 2) != "" THEN CONCAT(designations1.route, "-99")
                                            WHEN SUBSTRING(designations2.route, 1, 2) = "" THEN designations1.route
                                        END as tm,
                                        "68" as tm1,
                                        SUBSTR(designations2.route,1,2)  AS hcp,
										designations1.route
                                    FROM
                                        report_application_statements
                                    JOIN
                                        designations AS designations1 ON report_application_statements.designation_entry_id = designations1.id
                                    JOIN
                                        designations AS designations2 ON report_application_statements.designation_id = designations2.id
                                    GROUP BY chto,kuda,designations1.name,designations1.route, designations2.route,order_number,category_code
									ORDER BY
                                        chto, kuda, designations1.name, designations2.route, order_number, category_code');

        $newData = collect($queryResult)->map(function ($item) {
            return [
                'chto' => $item->chto,
                'kuda' => $item->kuda,
                'zakaz' => $item->zakaz,
                'name' => $item->name,
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


               if($i>115){
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
