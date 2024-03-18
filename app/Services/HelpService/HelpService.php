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

        $begin_string = $string;
        $string = preg_replace('/[^А-Яа-я0-9]+/', '', $string);

        // Извлечение префикса
        $prefix = preg_replace('/[^А-Яа-я]/u', '', $string);

        // Длина префикса
        $prefix_length = mb_strlen($prefix);

        //Вырезаем из строки буквы в начале
        $number = substr($string, strlen($prefix));

        if($prefix_length == 2 || $prefix_length == 3){
            $string = $prefix.self::changeStringTwo($number);
        }elseif($prefix_length == 4){
            $string = $prefix.self::changeStringFour($number);
        }else{
            return $begin_string;
        }

        return $string;

    }
    public static function changeStringTwo($string)
    {
        // Если количество цифр меньше 7, добавляем нули в конец
        if (strlen($string) < 7) {

            $string = str_pad($string, 7, '0', STR_PAD_RIGHT);

        }elseif (mb_strlen($string) > 7) {

            // Если количество цифр больше 7, обрезаем до 7 и добавляем дефис
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

            $string = str_pad($string, 9, '0', STR_PAD_RIGHT);

        }elseif (mb_strlen($string) > 9) {

            // Если количество цифр больше 9, обрезаем до 9 и добавляем дефис и две последние цифры
            $string = substr($string, 0, 9) . '-' . substr($string, 9);
        }
        return $string;
    }
}
