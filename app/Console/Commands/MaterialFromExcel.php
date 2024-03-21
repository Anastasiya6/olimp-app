<?php

namespace App\Console\Commands;

use App\Models\CsvMaterial;
use App\Models\Material;
use App\Models\PiFromExcel;
use App\Models\TypeUnit;
use App\Services\HelpService\HelpService;
use Illuminate\Console\Command;

class MaterialFromExcel extends Command
{
    public $typeUnits;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:material-from-excel';

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
        /*Material::truncate();
        PiFromExcel::truncate();
        TypeUnit::truncate();*/

        $this->typeUnits = TypeUnit::all()->pluck('id','unit')->toArray();
       // echo print_r(   $this->typeUnits,1 );
        $cv_materials = CsvMaterial::all();
        foreach($cv_materials as $value){


            if($value->name=='')
                continue;
           // echo $value->unit;
            $this->getMaterialType($value->name);

            $unit = trim($value->unit);
            if(!isset($this->typeUnits[$unit])){
                if($value->unit==''){
                    continue;
                }
                $this->createTypeUnit($unit);
            }
            $name = HelpService::getClearName($value->name);

            $type_material = $this->getMaterialType($value->name);
            if($type_material == 0){
                Material::updateOrcreate([
                    'name' => $name,
                ],
                    [
                        'type_unit_id' => $this->typeUnits[$unit]
                    ]
                );
            }else{
                PiFromExcel::updateOrcreate([
                    'name' => $name,
                ],
                    [
                        'type_unit_id' => $this->typeUnits[$unit]
                    ]
                );
            }

        }
    }

    public function getMaterialType($name)
    {
        /* 0 - матеріал, 1 - покупні*/
        //echo $name.PHP_EOL;
        $wordsActions = [
            'АМОРТИЗАТОР',
            'Аммартизатор',
            'Амортизатор',
            'Ампула',
            'Вентилятор',
            'Винт',
            'Блот',
            'Бризковик',
            'Болт',
            'Гачок',
            'Гайка',
            'Гвинт',
            'Дефлектор',
            'Заготовка',
            'Заглушка',
            'Замок',
            'Затискач',
            'Захват',
            'Заклепка',
            'Заклепки',
            'Коуш',
            'Канал 3',
            'Карабін',
            'Ключ',
            'Корпус',
            'Кронштейн вогнегасника',
            'Манжета',
            'Маслянка',
            'Оливниця',
            'Пелюстка',
            'Підшипник',
            'Півкільце',
            'Подшипник',
            'Пластиковий Кейс',
            'Пломба',
            'Петля',
            'Примач',
            'Пристрій пломбувальний',
            'рейка',
            'Рейка',
            'Ручка',
            'Ущільнювач',
            'Штіфт',
            'Шпонка',
            'Шплінт',
            'Шуруп',
            'Шайба',
            'Штіфт',
            'Шафа'
        ];
        $name = mb_strtolower($name, 'UTF-8');

        foreach ($wordsActions as $word) {

            $word = mb_strtolower($word, 'UTF-8');

            if (strpos($name, $word) !== false) {
                //echo '1'.PHP_EOL;
                return 1;
            }
        }
        //echo '0'.PHP_EOL;

        return 0;
    }

    public function createTypeUnit($unit)
    {
        $create = TypeUnit::create(
            [
                'unit' => $unit,
            ]
        );
        //echo $create;
        $this->typeUnits = TypeUnit::all()->pluck('id','unit')->toArray();
    }
}
