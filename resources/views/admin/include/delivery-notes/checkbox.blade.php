<input type="hidden" value="0" name="with_purchased">
<div class="py-4">
    <input type="checkbox" name="with_purchased" @if(isset($item) && $item->with_purchased) checked @endif id="exactMatchCheckbox" value="1">
    <label for="exactMatchCheckbox">З покупними</label>
</div>
