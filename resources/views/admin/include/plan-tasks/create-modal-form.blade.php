<form class="catalog-form plan-modal-form" method="POST" action="{{ route($route.'.store') }}">
                        @csrf
                        <input type="hidden" name="_plan_form" value="1" />
                        <input type="hidden" name="order_name_id" value="{{ $order_name_id }}">
                        <div class="mb-4">
                            <label class="block">
                                <span class="text-gray-700">Замовлення №</span>
                                <input type="text" name="order_name" readonly class="catalog-input" placeholder=""
                                       value="{{ $order_number }}" />
                            </label>
                            @error('order_number')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="plan-designation-picker">
                        <livewire:plan-task-search-dropdown :selectedOrder="$order_name_id" :sender_department_id="$sender_department_id" :receiver_department_id="$receiver_department_id" :restore_input="(bool) old('_plan_form')" :key="'plan-designation-'.$planFormVersion"/>
                        </div>
                        <livewire:quantity-calculator :order_name_quantity="$order_name_quantity" :restore_input="(bool) old('_plan_form')" :key="'plan-quantity-'.$planFormVersion" />

                        <input type="hidden" name="sender_department_id" value="{{ $sender_department_id }}">
                        <div class="mb-4">
                            <label class="block">
                                <span class="text-gray-700">Цех відправник</span>
                                <input type="text" name="sender_department" readonly class="catalog-input" placeholder=""
                                       value="{{ $sender_department }}" />
                            </label>
                            @error('sender_department')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="receiver_department_id" value="{{ $receiver_department_id }}">
                        <div class="mb-4">
                            <label class="block">
                                <span class="text-gray-700">Цех отримувач</span>
                                <input type="text" name="receiver_department" readonly class="catalog-input" placeholder=""
                                       value="{{ $receiver_department }}" />
                            </label>
                            @error('receiver_department')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-4">
                            <label class="block">
                                <span class="text-gray-700">Коментар</span>
                                <textarea name="comment" class="catalog-input" rows="3" placeholder="Введіть коментар...">{{ old('comment') }}</textarea>
                            </label>
                            @error('comment')
                            <div class="text-sm text-red-600">{{ $message }}</div>
                            @enderror
                        </div>
                        <input type="hidden" name="with_purchased" value="0" />
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="with_purchased" value="1" @checked(old('_plan_form') ? old('with_purchased', 0) : (isset($item) && $item->with_purchased)) class="rounded text-teal-700" />
                            З покупними
                        </label>
                        <div class="mt-5 flex justify-end gap-3 border-t border-[#d4e5e0] pt-5">
                        <x-catalog.button x-on:click="$dispatch('close')">Скасувати</x-catalog.button>
                        <x-catalog.button variant="primary" type="submit">
                            Зберегти
                        </x-catalog.button>
                        </div>

                    </form>