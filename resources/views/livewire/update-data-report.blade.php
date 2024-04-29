<div>
    @if($report_dates[$order_number])
        {{\Carbon\Carbon::parse($report_dates[$order_number])->format('d.m.Y')}}
    @endif
</div>
