<x-app-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Створити запис') }}
        </h2>
    </x-slot>

    <div class="py-12 search-box">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200 max-w-lg w-full lg:max-w-xs">
                    <form method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Документ №</span>
                                <input type="text" name="document_number" class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ $last_record->document_number ?? old('document_number') }}" />
                            </label>
                            @error('document_number')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Дата документа</span>
                                <input type="date" name="document_date" value="{{ \Carbon\Carbon::parse($last_record->document_date)->format('Y-m-d') ?? \Carbon\Carbon::now()->format('Y-m-d')}}" class="block w-full mt-1 rounded-md"/>
                            </label>
                            @error('document_date')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>

                        @livewire('delivery-note-search-dropdown')

                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Виберіть замовлення</span>
                                <select name="order_name_id" class="block w-full mt-1 rounded-md">
                                    @foreach($order_names as $order_name)
                                        <option value="{{ $order_name->id }}"
                                                @if($last_record->order_name_id == $order_name->id) selected @endif
                                        >{{ $order_name->name }}</option>
                                    @endforeach
                                </select>
                            </label>
                            @error('order_name_id')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity" class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ old('quantity') }}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Цех відправник</span>
                                <select name="sender_department_id" class="block w-full mt-1 rounded-md">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            @if($last_record->sender_department_id == $department->id) selected @endif>
                                            {{ $department->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('sender_department_id')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Цех отримувач</span>
                                <select name="receiver_department_id" class="block w-full mt-1 rounded-md">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                            @if($last_record->receiver_department_id == $department->id) selected @endif>
                                            {{ $department->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('receiver_department_id')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        @include('administrator::include.delivery-notes.checkbox')
                        @include('administrator::include.delivery-notes.checkbox-material')
                        <x-primary-button type="submit">
                            Зберегти
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
