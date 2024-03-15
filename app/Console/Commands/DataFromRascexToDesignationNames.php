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
        $i = 0;
        while ($record = $table->nextRecord()) {

            $find_designation = $record->get('chto');

            /*Пропускаем подобные этому Н021018, т.е. начиная с Н и далее цифры*/
            /*Пропускаем подобные этому КР66000160143233, т.е. начиная с КР и далее цифры*/
            /* Пропускаем если есть точка как например ААМВ464467001.1*/

            if (!(preg_match('/^Н\d+$/', $find_designation)) && !(preg_match('/^КР\d+/', $find_designation)) && !str_contains($find_designation, '.')) {

                $find_designation = $this->transformNumber($find_designation);

            }

            $designation = Designation::where('designation', $find_designation)->first();
           // echo 'designation'.PHP_EOL;
           // echo print_r($designation,1);
            if (!empty($designation) ){
                if(($designation->name == '' || $designation->route == '')
                    &&
                    ($record->get('naim') != '' || $record->get('tm') != '')
                ) {

                    echo $designation->designation . PHP_EOL;
                    echo 'update'.PHP_EOL;
                    $designation->update([
                        'name' => $designation->name ?? $record->get('naim'),
                        'designation_from_rascex' => $record->get('chto'),
                        'route' => $designation->route ?? $record->get('tm')
                    ]);
                }else{
                    echo 'update_Des'.PHP_EOL;

                    $designation->update([
                        'designation_from_rascex' => $record->get('chto'),
                        'designation' => $record->get('chto'),

                    ]);
                }

            } else {
                echo 'create'.PHP_EOL;
                Designation::create([
                    'designation' => $find_designation,
                    'designation_from_rascex' => $record->get('chto'),
                    'name' => $record->get('naim'),
                    'route' => $record->get('tm'),
                ]);
            }
            echo $record->get('naim') . PHP_EOL;

        }

    }

    public function transformNumber($string)
    {
        echo $string.PHP_EOL;
        // Удаление всех пробельных символов (включая пробелы и символы переноса строки)
        //$string = preg_replace('/\s+/', '', $string);
        //убираем все дефисы
       // $string = str_replace('-', '', $string);

        $string = preg_replace('/[^А-Яа-я0-9]+/', '', $string);

        // Извлечение префикса
        $prefix = preg_replace('/[^А-Яа-я]/u', '', $string);

        echo $prefix.PHP_EOL;

        // Вывод длины префикса
        $prefix_length = mb_strlen($prefix); // Используем mb_strlen для корректного подсчета символов Unicode
        echo $prefix_length;
        echo $prefix.' '.$prefix_length.PHP_EOL;
        //Вырезаем из строки буквы в начале
        $number = substr($string, strlen($prefix));
        echo 'Вырезаем из строки буквы в начале'.PHP_EOL;
        echo $number.PHP_EOL;
        echo $prefix_length.' '.$prefix.PHP_EOL;

        if($prefix_length == 2){
            echo 'two';
            $string = $prefix.$this->changeStringTwo($number);
        }elseif($prefix_length == 4){
            echo 'four';
            $string = $prefix.$this->changeStringFour($number);
        }
        echo $string.PHP_EOL;
        echo '-----------------------'.PHP_EOL;
        return $string;

    }

    public function changeStringTwo($string)
    {
        // Если количество цифр меньше 7, добавляем нули в конец
        if (strlen($string) < 7) {
            echo '<7'.PHP_EOL;
            echo $string.PHP_EOL;

            $string = str_pad($string, 7, '0', STR_PAD_RIGHT);

        }elseif (mb_strlen($string) > 7) {
            // Если количество цифр больше 7, обрезаем до 7 и добавляем дефис и две последние цифры
            echo '>7'.PHP_EOL;

            echo $string.PHP_EOL;
            echo 'substr';
            $after =  substr($string, 7) ;
            echo substr($string, 7) ;
            echo $after;
            if(mb_strlen(substr($string, 7)) == 1 ){
                $string = substr($string, 0, 7) . '-00' . substr($string, 7);

            }elseif(mb_strlen(substr($string, 7)) == 2){
                $string = substr($string, 0, 7) . '-0' . substr($string, 7);

            }else{
                $string = substr($string, 0, 7) . '-' . substr($string, 7);
            }
        }
        return $string;

    }

    public function changeStringFour($string)
    {

        // Если количество цифр меньше 9, добавляем нули в конец
        if (mb_strlen($string) < 9) {
            echo '<9'.PHP_EOL;
            echo $string.PHP_EOL;

            $string = str_pad($string, 9, '0', STR_PAD_RIGHT);

        }elseif (mb_strlen($string) > 9) {
        // Если количество цифр больше 9, обрезаем до 9 и добавляем дефис и две последние цифры
            echo '>9'.PHP_EOL;

            echo $string.PHP_EOL;

            $string = substr($string, 0, 9) . '-' . substr($string, 9);
        }
        return $string;
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
                    'type_unit' => $record->get('ediz'),
                    'type' => 1
                ]);


            } else {
                Designation::create([
                    'designation' => $record->get('nm'),
                    'name' => $record->get('naim'),
                    'gost' => $record->get('gost'),
                    'type_unit' => $record->get('ediz'),
                    'type' => 1

                ]);
            }
            echo $record->get('naim') . PHP_EOL;

        }

    }

    public function fillSpecification()
    {
        $table = new TableReader(
            'c:\Mass\M0020.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {

            $find_designation_ok = $record->get('ok');

            $find_designation_od = $record->get('od');

            /*Пропускаем подобные этому Н021018, т.е. начиная с Н и далее цифры*/
            /*Пропускаем подобные этому КР66000160143233, т.е. начиная с КР и далее цифры*/
            /* Пропускаем если есть точка как например ААМВ464467001.1*/

            if (!(preg_match('/^Н\d+$/', $find_designation_ok)) && !(preg_match('/^КР\d+/', $find_designation_ok))  && !str_contains($find_designation_ok, '.')) {

                $find_designation_ok = $this->transformNumber($find_designation_ok);

            }

            /*Пропускаем подобные этому Н021018, т.е. начиная с Н и далее цифры*/
            /*Пропускаем подобные этому КР66000160143233, т.е. начиная с КР и далее цифры*/
            /* Пропускаем если есть точка как например ААМВ464467001.1*/
            if (!(preg_match('/^Н\d+$/', $find_designation_od)) && !(preg_match('/^КР\d+/', $find_designation_od))  && !str_contains($find_designation_od, '.')) {

                $find_designation_od = $this->transformNumber($find_designation_od);

            }

            $designation = $find_designation_ok;
            $detail = $find_designation_od;

            $designationId = Designation::where('designation', $designation)->value('id');
            $detailId = Designation::where('designation', $detail)->value('id');

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
                $specification = Specification::updateOrCreate([
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
}
