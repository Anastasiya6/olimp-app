<?php

namespace App\Console\Commands;

use App\Models\B2012;
use Illuminate\Console\Command;
use XBase\TableReader;

class B2012Dbf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:b2012-dbf';

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
            'c:\Mass\B2012.dbf',
            [
                'encoding' => 'cp866'
            ]
        );
        B2012::truncate();
        while ($record = $table->nextRecord()) {

            B2012::create([

                'kuda' => $record->get('kuda'),
                'zakaz' => $record->get('zakaz'),
                'chto' => $record->get('chto'),
                'kols' => $record->get('kols'),
                'kolzak' => $record->get('kolzak'),
                'tm' => $record->get('tm'),
                'tm1' => $record->get('tm1'),
                'naim' => $record->get('naim'),
                'hcp' => $record->get('hcp'),
                'e' => $record->get('e'),

            ]);
        }
    }
}
