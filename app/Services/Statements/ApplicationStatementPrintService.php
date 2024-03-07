<?php


namespace App\Services\Statements;


use App\Models\Order;
use App\Models\ReportApplicationStatement;
use App\Models\Specification;
use Illuminate\Support\Facades\DB;

class ApplicationStatementPrintService
{

    static public function queryAppStatement($filter = 0)
    {
        $where = '';
        $add_order = '';
        if($filter == 1) {
            $add_order = 'designations1.designation LIKE "КР%" ASC,';
            $where =  'WHERE
                                designations1.designation NOT LIKE "ПИ0%"';

        }

        return DB::select('
                            SELECT
                                designations1.designation AS chto,
                                designations2.designation AS kuda,
                                designations1.name as chto_name,
                                min(report_application_statements.quantity) AS kols,
                                sum(report_application_statements.quantity_total) as kolzak,
                                order_number as zakaz,

                                CASE
                                    WHEN (designations1.route="" && designations2.route!="") THEN "99"
                                    WHEN (designations1.designation=designations2.designation && designations1.route="" && designations2.route="") THEN "99"
                                    WHEN (SUBSTRING(designations1.route, 1, 2) = SUBSTR(designations2.route,1,2)  && designations1.route != "") THEN designations1.route
                                    WHEN SUBSTRING(designations2.route, 1, 2) != "" THEN CONCAT(designations1.route, "-99")
                                    WHEN SUBSTRING(designations2.route, 1, 2) = "" THEN designations1.route
                                END as tm,
                                "68" as tm1,
                                SUBSTR(designations2.route,1,2)  AS hcp,
                                designations1.route as route
                            FROM
                                report_application_statements
                            JOIN
                                designations AS designations1 ON report_application_statements.designation_entry_id = designations1.id
                            JOIN
                                designations AS designations2 ON report_application_statements.designation_id = designations2.id
                            '.$where.'
                            GROUP BY chto,kuda,designations1.name,designations1.route, designations2.route,order_number,category_code
                            ORDER BY '.$add_order.'
                                chto, kuda, designations1.name, designations2.route, order_number, category_code');
    }

}

