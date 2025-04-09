<input type="hidden" value="0" name="with_material_purchased">
<div class="py-4">
    <input type="checkbox" name="with_material_purchased" @if(isset($item) && $item->with_material_purchased) checked @endif id="exactMatchCheckbox" value="1">
    <label for="exactMatchCheckbox">Заміна матеріалів</label>
</div>
