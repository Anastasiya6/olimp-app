<?php

namespace App\Console\Commands;

use App\Models\MaterialIssuance;
use App\Models\Nacop;
use App\Models\PlanTask;
use App\Models\Rascex;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
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
        MaterialIssuance::whereDoesntHave('items')->delete();

        $materialIssuances = MaterialIssuance::with('items')->get();

        foreach ($materialIssuances as $materialIssuance ) {

            $designationId = $materialIssuance->designation_id;
            $orderId = $materialIssuance->order_name_id;

            $saved = false;
            $checked = [];

            while ($designationId && !in_array($designationId, $checked)) {

                $checked[] = $designationId;

                $planTask = PlanTask::where('designation_id', $designationId)
                    ->where('order_name_id', $orderId)
                    ->first();

                if ($planTask) {
                    $this->saveMaterialIssuance($materialIssuance, $planTask);
                    $saved = true;
                    break;
                }

                $report = ReportApplicationStatement::where('designation_entry_id', $designationId)
                    ->where('order_name_id', $orderId)
                    ->first();

                if (!$report) {
                    break;
                }

                $designationId = $report->designation_id;
            }

            if(!$saved){

                $designationId = $materialIssuance->designation_id;

                $checked = [];

                while ($designationId && !in_array($designationId, $checked)){

                    $checked[] = $designationId;

                    $planTask = PlanTask::where('designation_id', $designationId)
                        ->where('order_name_id', $orderId)
                        ->first();

                    if ($planTask) {
                        $this->saveMaterialIssuance($materialIssuance, $planTask);
                        break;
                    }

                    $specification = Specification::where('designation_entry_id', $designationId)
                        ->first();
                    if (!$specification) {
                        break;
                    }

                    $designationId = $specification->designation_id;
                }
            }

        }

//            $detail_kuda = PlanTask::where('designation_id', $detail)
//                ->where('order_name_id', $order)
//                ->first();
//
//            if ($detail_kuda) {
//                $this->saveMaterialIssuance($material_issuance,$detail_kuda);
//                $save_detail = true;
//            }else{
//                $detail_report = ReportApplicationStatement
//                    ::where('designation_entry_id', $detail)
//                    ->where('order_name_id',$order)
//                    ->first();
//
//                if($detail_report){
//                    $detail_kuda = PlanTask::where('designation_id', $detail_report->designation_id)
//                        ->where('order_name_id', $order)
//                        ->first();
//                    if ($detail_kuda) {
//                        $this->saveMaterialIssuance($material_issuance, $detail_kuda);
//                        $save_detail = true;
//
//                    }else{
//                        $detail_report = ReportApplicationStatement
//                            ::where('designation_entry_id', $detail_report->designation_id)
//                            ->where('order_name_id',$order)
//                            ->first();
//                        if($detail_report){
//
//                        }
//                    }
//                }
//                if(!$save_detail){
//
//                }
              //  dd($detail,$order,$detail_kuda);

//        $table = new TableReader(
//            'c:\Mass\Rascex.dbf',
//            [
//                'encoding' => 'cp866'
//            ]
//        );
//        while ($record = $table->nextRecord()) {
//
//            Rascex::create([
//                'chto' => $record->get('chto'),
//                'naim' => $record->get('naim'),
//                'zagot' => $record->get('zagot'),
//                'tm' => $record->get('tm'),
//            ]);
//
//        }
        echo 'Команда успешно выполнена!';

    }
    public function saveMaterialIssuance($material_issuance,$detail_kuda){
        $material_issuance->plan_task_designation_id = $detail_kuda->designation_id;
        $material_issuance->save();
    }
}
