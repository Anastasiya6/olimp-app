<form class="catalog-form" method="POST" action="{{ route($route.'.update',$item->id) }}">
                        @csrf
                        <input type="hidden" name="_task_form" value="1" />
                        <input type="hidden" name="_task_edit" value="{{ $item->id }}" />
                        @method('put')
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Деталь</span>
                                <input type="text" name="designation"
                                       class="catalog-input" readonly
                                       placeholder="" value="{{old('designation',$item->designation->designation)}}" />
                            </label>
                            @error('designation')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Кількість</span>
                                <input type="text" name="quantity"
                                       class="catalog-input"
                                       placeholder="" value="{{old('quantity',$item->quantity)}}" />
                            </label>
                            @error('quantity')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-6">
                            <label class="block">
                                <span class="text-gray-700">Цех відправник</span>
                                <select name="department_id" class="catalog-input">
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}"
                                                @selected((string) old('department_id', $item->department_id) === (string) $department->id)>
                                        {{ $department->number }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                            @error('department_id')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="flex justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                        <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                        <x-catalog.button variant="primary" type="submit">
                            Оновити
                        </x-catalog.button></div>

                    </form>