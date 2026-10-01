<?php

namespace Tests\Feature;

use App\Livewire\OrderNameIndex;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

class OrderNameModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->rebinding('request', function ($app, $request) {
            $request->setLaravelSession($app['session.store']);
        });
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('order_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('quantity');
            $table->boolean('is_order');
            $table->timestamps();
        });
        for ($id = 1; $id <= 30; $id++) {
            DB::table('order_names')->insert(['id' => $id, 'name' => 'Замовлення '.$id, 'quantity' => $id, 'is_order' => $id % 2]);
        }
    }

    public function test_search_matches_partial_name_and_resets_pagination(): void
    {
        Livewire::test(OrderNameIndex::class)
            ->assertSee('Записів: 30')
            ->set('paginators.page', 2)
            ->set('searchTerm', '  ння 12  ')
            ->assertSet('paginators.page', 1)
            ->assertViewHas('items', fn ($items) => $items->pluck('id')->all() === [12])
            ->set('searchTerm', 'немає')
            ->assertSee('За вашим запитом замовлень не знайдено.')
            ->set('searchTerm', '')
            ->assertSee('Записів: 30');
    }

    public function test_edit_switching_and_deletion(): void
    {
        Livewire::test(OrderNameIndex::class)
            ->assertSeeHtml('name="_order_name_create"')
            ->call('editOrderName', 1)
            ->assertSet('editingOrderName.id', 1)
            ->assertDispatched('order-name-edit-open')
            ->assertSeeHtml('name="is_order" value="1" checked')
            ->call('editOrderName', 2)
            ->assertSet('editingOrderName.id', 2)
            ->assertSeeHtml('edit-order-name-form-2-2')
            ->call('deleteOrderName', 2)
            ->assertSet('editingOrderName', null);
        $this->assertDatabaseMissing('order_names', ['id' => 2]);
    }

    public function test_validation_preserves_unchecked_order_flag_and_quantity(): void
    {
        session()->put('_old_input', ['_order_name_edit' => 1, 'name' => '', 'quantity' => '42', 'is_order' => '0']);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['name' => 'Введіть назву або номер замовлення'])));
        Livewire::test(OrderNameIndex::class)
            ->assertSet('editingOrderName.id', 1)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="42"')
            ->assertDontSeeHtml('name="is_order" value="1" checked')
            ->assertSee('Введіть назву або номер замовлення');
    }
}
