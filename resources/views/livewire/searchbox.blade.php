<div>
    <!-- CSS -->
    <style type="text/css">
        .search-box .clear{
            clear:both;
            margin-top: 20px;
        }

        .search-box ul{
            list-style: none;
            padding: 0px;
            width: 250px;
            position: absolute;
            margin: 0;
            background: white;
        }

        .search-box ul li{
            background: lavender;
            padding: 4px;
            margin-bottom: 1px;
        }

        .search-box ul li:nth-child(even){
            background: cadetblue;
            color: white;
        }

        .search-box ul li:hover{
            cursor: pointer;
        }

        .search-box input[type=text]{
            padding: 5px;
            width: 250px;
            letter-spacing: 1px;
        }
    </style>

    <div class="search-box">
        <input type="text" wire:model="search" wire:keyup="searchResult" value="{{$search}}">
        {{$search}}
        <!-- Search result list -->
        @if($showdiv)
            <ul >
                @if(!empty($records))
                    @foreach($records as $record)

                        <li wire:key="{{ $record->id }}"
                            wire:click="fetchEmployeeDetail({{ $record->id }})">{{ $record->name}}</li>

                    @endforeach
                @endif
            </ul>
        @endif

        <div class="clear"></div>
        <div >
            @if(!empty($empDetails))
                <div>
                    <input type='text' value="{{ $empDetails->name }} ">
                           Name : {{ $empDetails->name }} <br>
                    Email : {{ $empDetails->designation }}
                </div>
            @endif
        </div>
        <div class="clear"></div>
        <div>
            <div class="mb-6">
                <label class="block">
                    <span class="text-gray-700">Що</span>
                    <input type="text" readonly name="designation_entry_designation" class="block w-full mt-1 rounded-md" placeholder=""
                           value="{{!empty($selectedDesignation)?$selectedDesignation:''}}" />
                </label>
                @error('designation_entry_designation')
                <div class="text-sm text-red-600">{{ $message }}</div>
                @enderror
            </div>

        </div>
    </div>

</div>
