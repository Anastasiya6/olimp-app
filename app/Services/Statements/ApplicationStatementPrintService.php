<?php


namespace App\Services\Statements;


use App\Models\Order;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
use Illuminate\Support\Facades\DB;

class ApplicationStatementPrintService
{

    static public function queryAppStatement($filter = 0,$order_name_id=0,$department=0)
    {
     //   dd($filter,$order_name_id,$department);
        //$filter = 2 - vidomist zastosuv pokupni
        //$filter = 3 - cehovi spiski
        $add_order = '';

        if($order_name_id == 0){
            $where = "WHERE order_name_id>'$order_name_id'";
        }else{
            $where = "WHERE order_name_id='$order_name_id'";
        }
        if($department != 0 && $filter == 3){
            $where = $where.' AND tm LIKE "%' . $department . '%"';
        }elseif($department != 0 && $filter != 2){
            $where = $where.' AND SUBSTR(tm, 1, 2) ='.$department;
        }

        if($filter == 1 || $filter == 3) {
            $add_order = 'designations1.designation LIKE "КР%" ASC,';
            $where = $where.' AND
                                designations1.designation NOT LIKE "ПИ0%"';
        }elseif($filter == 2){
            if($department != 0){
                $where = $where.' AND SUBSTR(tm, LENGTH(tm) - 1, 2) ='.$department;
            }
            $where = $where.' AND
                                designations1.designation LIKE "ПИ0%"';
        }

        return DB::select('
                            SELECT
                                designations1.designation AS chto,
                                designations2.designation AS kuda,
                                designations1.name as chto_name,
                                designations1.gost as gost,
                                report_application_statements.quantity AS kols,
                                report_application_statements.quantity_total as kolzak,
                                order_names.name as zakaz,

                                tm as tm,
                                "68" as tm1,
                                hcp AS hcp,
                                designations1.route as route
                            FROM
                                report_application_statements
                            JOIN
                                order_names ON order_names.id = report_application_statements.order_name_id
                            JOIN
                                designations AS designations1 ON report_application_statements.designation_entry_id = designations1.id
                            JOIN
                                designations AS designations2 ON report_application_statements.designation_id = designations2.id
                            '.$where.'
                            ORDER BY '.$add_order.'
                                report_application_statements.order_designationEntry, report_application_statements.order_designationEntry_letters,report_application_statements.order_designation,report_application_statements.order_designation_letters, designations1.name, designations2.route, order_name_id, category_code');

    }

}

