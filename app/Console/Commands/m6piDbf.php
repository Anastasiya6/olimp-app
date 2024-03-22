<?php

namespace App\Console\Commands;

use App\Models\M6pi;
use Illuminate\Console\Command;
use XBase\TableReader;

class m6piDbf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:m6pi-dbf';

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
            'e:\d\MASS\m6pi.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        m6pi::truncate();
        while ($record = $table->nextRecord()) {

            M6pi::create([
                'nm' => $record->get('nm'),
                'naim' => $record->get('naim'),
                'gost' => $record->get('gost'),
                'ediz' => $record->get('ediz'),

            ]);
            echo $record->get('nm');
        }

        echo 'Команда успешно выполнена!';
    }
}
