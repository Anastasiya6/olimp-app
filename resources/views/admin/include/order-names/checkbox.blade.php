<input type="hidden" value="0" name="is_order">
<div class="py-4">
    <input type="checkbox" name="is_order" @if(isset($item) && $item->is_order) checked @endif id="exactMatchCheckbox" value="1">
    <label for="exactMatchCheckbox">Замовлення</label>
</div>
