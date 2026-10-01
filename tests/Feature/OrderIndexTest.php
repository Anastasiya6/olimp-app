<?php

namespace Tests\Feature;

use App\Livewire\OrderIndex;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class OrderIndexTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('order_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
        });
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->integer('order_name_id');
            $table->integer('designation_id');
            $table->integer('quantity');
            $table->timestamps();
        });
        foreach (['designation_materials', 'specifications'] as $name) {
            Schema::create($name, function (Blueprint $table) {
                $table->id();
                $table->integer('designation_id');
            });
        }
        Schema::create('report_application_statements', function (Blueprint $table) {
            $table->id();
            $table->integer('order_name_id');
            $table->integer('designation_id');
            $table->integer('designation_entry_id');
            $table->timestamps();
        });
        DB::table('order_names')->insert([['id' => 1, 'name' => 'Order-100'], ['id' => 2, 'name' => 'Order-200']]);
        DB::table('designations')->insert([['id' => 1, 'designation' => 'ABC-01'], ['id' => 2, 'designation' => 'XYZ-02']]);
        for ($id = 1; $id <= 12; $id++) {
            DB::table('orders')->insert(['id' => $id, 'order_name_id' => $id <= 6 ? 1 : 2, 'designation_id' => $id % 2 + 1, 'quantity' => 5]);
        }
    }

    public function test_searches_combine_and_reset_pagination(): void
    {
        Livewire::test(OrderIndex::class)
            ->assertViewHas('items', fn ($items) => $items->total() === 12)
            ->set('paginators.page', 2)
            ->set('orderSearch', ' 100 ')
            ->assertSet('paginators.page', 1)
            ->assertViewHas('items', fn ($items) => $items->total() === 6)
            ->set('detailSearch', ' XYZ ')
            ->assertViewHas('items', fn ($items) => $items->total() === 3 && $items->every(fn ($item) => $item->order_name_id === 1 && $item->designation_id === 2))
            ->set('orderSearch', '')
            ->assertViewHas('items', fn ($items) => $items->total() === 6)
            ->set('detailSearch', 'missing')
            ->assertSee('Записів не знайдено.')
            ->set('detailSearch', '')
            ->assertViewHas('items', fn ($items) => $items->total() === 12);
    }

    public function test_create_modal_posts_to_existing_store_and_preserves_report_controls(): void
    {
        Livewire::test(OrderIndex::class)
            ->assertSeeHtml('aria-labelledby="create-order-title"')
            ->assertSeeHtml('action="'.route('orders.store').'"')
            ->assertSeeHtml('name="order_name_id"')
            ->assertSeeHtml('name="designation"')
            ->assertSeeHtml('name="quantity"')
            ->assertSeeHtml('wire:click="generateReport"')
            ->assertSee('Pdf');
    }
    public function test_edit_modal_loads_selected_record_and_switches_records(): void
    {
        Livewire::test(OrderIndex::class)
            ->call('editOrder', 1)
            ->assertSet('editingOrder.id', 1)
            ->assertDispatched('order-edit-open')
            ->assertSeeHtml('action="'.route('orders.update', 1).'"')
            ->assertSeeHtml('value="XYZ-02"')
            ->assertSeeHtml('name="_method" value="PUT"')
            ->call('editOrder', 8)
            ->assertSet('editingOrder.id', 8)
            ->assertSeeHtml('action="'.route('orders.update', 8).'"')
            ->assertSeeHtml('value="ABC-01"')
            ->assertSeeHtml('edit-order-form-8-2');
    }
}
