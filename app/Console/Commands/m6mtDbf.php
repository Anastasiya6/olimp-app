<?php

namespace App\Console\Commands;

use App\Models\M6mt;
use App\Models\Nacop;
use Illuminate\Console\Command;
use XBase\TableReader;

class m6mtDbf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:m6mt-dbf';

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
            'c:\MASS\m6mt.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        m6mt::truncate();
        while ($record = $table->nextRecord()) {

            M6mt::create([
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
