<?php

namespace App\Console\Commands;

use App\Models\Naimiz;
use Illuminate\Console\Command;
use XBase\TableReader;

class naimizDbf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:naimiz-dbf';

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
            'c:\MASS\naimiz.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        Naimiz::truncate();
        while ($record = $table->nextRecord()) {

            Naimiz::create([
                'ediz' => $record->get('Ediz'),
                'naimiz' => $record->get('Naimiz'),
            ]);
        }

        echo 'Команда успешно выполнена!';
    }
}
