<?php

namespace App\Services\HelpService;
use App\Models\Designation;
use App\Models\Material;
use App\Models\Specification;
use App\Models\Ticker\PostStatus;
use Illuminate\Http\Request;
use App\Models\Publication\Publication;
use Illuminate\Database\Eloquent\Builder;

class HelpService
{
    public static function getClearName($name)
    {
        $name = trim($name);
        $name = trim($name, '"');
        //для замены всех последовательностей пробельных символов в строке на один пробел.
        $name = preg_replace('/\s+/', ' ', $name);
        $name = str_replace('?', 'd', $name);

        // Заменяем символ "ø" на "d" в строке
        $name = str_replace('ø', 'd', $name);

        if (preg_match( '/(\d+)\.0мм/', $name)) {
            //echo 'Найдено значение с точкой, нулем и "мм"'.PHP_EOL;

           // echo $name.PHP_EOL;
            $name = preg_replace('/(\d+)\.0мм/', '$1мм', $name);
           // echo $name.PHP_EOL;
        }
        if (preg_match('/\(Цех №7\)/', $name)) {
            //echo "Подстрока '(Цех №7)' найдена в строке.".PHP_EOL;
            //echo $name.PHP_EOL;
            $name = preg_replace('/\(Цех №7\)/', "", $name);
            //echo $name.PHP_EOL;
        }
        return $name;
    }

    public static function transformNumber($string)
    {
        //echo $string.PHP_EOL;
        // Удаление всех пробельных символов (включая пробелы и символы переноса строки)
        //$string = preg_replace('/\s+/', '', $string);
        //убираем все дефисы
        // $string = str_replace('-', '', $string);

        $string = preg_replace('/[^А-Яа-я0-9]+/', '', $string);

        // Извлечение префикса
        $prefix = preg_replace('/[^А-Яа-я]/u', '', $string);

        //echo $prefix.PHP_EOL;

        // Вывод длины префикса
        $prefix_length = mb_strlen($prefix); // Используем mb_strlen для корректного подсчета символов Unicode
        //echo $prefix_length;
        //echo $prefix.' '.$prefix_length.PHP_EOL;
        //Вырезаем из строки буквы в начале
        $number = substr($string, strlen($prefix));
        /*echo 'Вырезаем из строки буквы в начале'.PHP_EOL;
        echo $number.PHP_EOL;
        echo $prefix_length.' '.$prefix.PHP_EOL;*/

        if($prefix_length == 2){
            //echo 'two';
            $string = $prefix.self::changeStringTwo($number);
        }elseif($prefix_length == 4){
            //echo 'four';
            $string = $prefix.self::changeStringFour($number);
        }
        //echo $string.PHP_EOL;
        //echo '-----------------------'.PHP_EOL;
        return $string;

    }
    public static function changeStringTwo($string)
    {
        // Если количество цифр меньше 7, добавляем нули в конец
        if (strlen($string) < 7) {
            //echo '<7'.PHP_EOL;
            //echo $string.PHP_EOL;

            $string = str_pad($string, 7, '0', STR_PAD_RIGHT);

        }elseif (mb_strlen($string) > 7) {
            // Если количество цифр больше 7, обрезаем до 7 и добавляем дефис и две последние цифры
            //echo '>7'.PHP_EOL;

            //echo $string.PHP_EOL;
            //echo 'substr';
            $after =  substr($string, 7) ;
            //echo substr($string, 7) ;
            //echo $after;
            if(mb_strlen(substr($string, 7)) == 1 ){
                $string = substr($string, 0, 7) . '-00' . substr($string, 7);

            }elseif(mb_strlen(substr($string, 7)) == 2){
                $string = substr($string, 0, 7) . '-' . substr($string, 7).'0';

            }else{
                $string = substr($string, 0, 7) . '-' . substr($string, 7);
            }
        }
        return $string;

    }

    public static function changeStringFour($string)
    {

        // Если количество цифр меньше 9, добавляем нули в конец
        if (mb_strlen($string) < 9) {
            //echo '<9'.PHP_EOL;
            //echo $string.PHP_EOL;

            $string = str_pad($string, 9, '0', STR_PAD_RIGHT);

        }elseif (mb_strlen($string) > 9) {
            // Если количество цифр больше 9, обрезаем до 9 и добавляем дефис и две последние цифры
            //echo '>9'.PHP_EOL;

            //echo $string.PHP_EOL;

            $string = substr($string, 0, 9) . '-' . substr($string, 9);
        }
        return $string;
    }
}
