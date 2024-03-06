<?php

namespace App\Console\Commands;

use App\Models\Nacop;
use App\Models\Rascex;
use Illuminate\Console\Command;
use XBase\TableReader;

class rascexDbf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:rascex-dbf';

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
            'c:\Mass\Rascex.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        while ($record = $table->nextRecord()) {

            Rascex::create([
                'chto' => $record->get('chto'),
                'naim' => $record->get('naim'),
                'zagot' => $record->get('zagot'),
                'tm' => $record->get('tm'),
            ]);

        }
        echo 'Команда успешно выполнена!';

    }
}
