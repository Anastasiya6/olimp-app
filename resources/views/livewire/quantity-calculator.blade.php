<div>
    <div class="mb-6">
        <label class="block">
            <span class="text-gray-700">Кількість на замовлення</span>
            <input type="text" wire:model="quantity" wire:keyup="searchResult" name="quantity" class="block w-full mt-1 rounded-md" placeholder="" />
        </label>
        @error('quantity')
        <div class="text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-6">
        <label class="block">
            <span class="text-gray-700">Загальна кількість</span>
            <input type="text" wire:model="quantity_total" value="{{$quantity_total}}" name="quantity_total" class="block w-full mt-1 rounded-md" placeholder="" />
        </label>
        @error('quantity_total')
        <div class="text-sm text-red-600">{{ $message }}</div>
        @enderror
    </div>
</div>
