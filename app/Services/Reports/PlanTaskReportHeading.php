<?php

namespace App\Services\Reports;

use App\Models\Section;

class PlanTaskReportHeading
{
    public static function make($orderName, $senderNumber, $receiverNumber, ?int $sectionId): string
    {
        $receiver = $receiverNumber === null ? 'Усі цехи-отримувачі' : "У цех {$receiverNumber}";
        $section = $sectionId === null
            ? 'Всі ділянки'
            : 'Ділянка '.Section::findOrFail($sectionId)->number;

        return "Замовлення {$orderName} · З цеху {$senderNumber} · {$receiver} · {$section}";
    }
}
