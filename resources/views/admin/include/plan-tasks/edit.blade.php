<x-app-layout>

    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            {{ __('Редагувати запис') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="mx-auto max-w-5xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <form method="POST" action="{{ route($route.'.update',$item->id) }}">
                        @csrf
                        @method('put')
                        <input type="hidden" name="order_name_id" value="{{ $order_name_id }}">
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Замовлення №</span>
                                <input type="text" name="order_name" readonly class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ $order_number }}" />
                            </label>
                            @error('order_number')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Що</span>
                                <input type="text" name="designation_designation"
                                       class="block w-full mt-1 rounded-md" readonly
                                       placeholder="" value="{{old('designation_designation',$item->designations->designation)}}" />
                            </label>
                            @error('designation_designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість на замовлення</span>
                                <input type="text" name="quantity"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('quantity',$item->quantity)}}" />
                            </label>
                            @error('quantity')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Загальна кількість</span>
                                <input type="text" name="quantity_total"
                                       class="block w-full mt-1 rounded-md"
                                       placeholder="" value="{{old('quantity_total',$item->quantity_total)}}" />
                            </label>
                            @error('quantity_total')
                                <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="sender_department_id" value="{{ $sender_department_id }}">
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Цех відправник</span>
                                <input type="text" name="sender_department" readonly class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ $sender_department }}" />
                            </label>
                            @error('sender_department')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="receiver_department_id" value="{{ $receiver_department_id }}">
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Цех отримувач</span>
                                <input type="text" name="receiver_department" readonly class="block w-full mt-1 rounded-md" placeholder=""
                                       value="{{ $receiver_department }}" />
                            </label>
                            @error('receiver_department')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        @include('administrator::include.plan-tasks.checkbox')
                        <x-primary-button type="submit">
                            Оновити
                        </x-primary-button>

                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
