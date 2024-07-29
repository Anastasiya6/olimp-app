<div>
    @if($report_dates[$order_name_id])
        {{\Carbon\Carbon::parse($report_dates[$order_name_id])->format('d.m.Y')}}
    @endif
</div>
