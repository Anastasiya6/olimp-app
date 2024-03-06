<?php

namespace App\Console\Commands;

use App\Models\Nacop;
use Illuminate\Console\Command;
use XBase\TableReader;

class nacopDbf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:nacop-dbf';

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
        $table = new TableReader(
            'c:\MASS\Nacop.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        Nacop::truncate();
        while ($record = $table->nextRecord()) {

            Nacop::create([
                'od' => $record->get('od'),
                'e' => $record->get('e'),
                'ok' => $record->get('ok'),
                'pe' => $record->get('pe'),
                'pi' =>$record->get('pi'),
                'na' =>$record->get('ha'),
            ]);
            echo $record->get('ok');
        }
        echo 'Команда успешно выполнена!';
    }
}
