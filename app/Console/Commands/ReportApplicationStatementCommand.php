<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\NpController;
use App\Services\Statements\ApplicationStatementService;
use Illuminate\Console\Command;

class ReportApplicationStatementCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:report-application-statement-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(ApplicationStatementService $service)
    {
        $service->make();
    }
}
