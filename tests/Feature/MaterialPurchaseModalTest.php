<?php

namespace Tests\Feature;

use App\Livewire\MaterialPurchaseSearch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

class MaterialPurchaseModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->rebinding('request', function ($app, $request) {
            $request->setLaravelSession($app['session.store']);
        });
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
            $table->string('name')->nullable();
        });
        Schema::create('type_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit');
        });
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('type_unit_id');
        });
        DB::table('type_units')->insert(['id' => 1, 'unit' => 'кг']);
        DB::table('materials')->insert([
            ['id' => 1, 'name' => 'Матеріал 1', 'type_unit_id' => 1],
            ['id' => 2, 'name' => 'Матеріал 2', 'type_unit_id' => 1],
        ]);
        Schema::create('material_purchases', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('designation_id');
            $table->unsignedInteger('designation_entry_id');
            $table->unsignedInteger('material_id');
            $table->decimal('norm');
            $table->string('code_1c')->nullable();
            $table->timestamps();
        });
        Schema::create('order_names', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->boolean('is_order');
        });
        Schema::create('material_purchase_order_names', function (Blueprint $table) {
            $table->unsignedInteger('material_purchase_id');
            $table->unsignedInteger('order_name_id');
        });
        DB::table('designations')->insert([
            ['id' => 1, 'designation' => 'A100'], ['id' => 2, 'designation' => 'B200'], ['id' => 3, 'designation' => 'B300'],
        ]);
        DB::table('order_names')->insert([
            ['id' => 1, 'name' => 'Замовлення А', 'is_order' => 1],
            ['id' => 2, 'name' => 'Замовлення Б', 'is_order' => 1],
        ]);
        foreach ([1, 2] as $id) {
            DB::table('material_purchases')->insert(['id' => $id, 'designation_id' => 1, 'designation_entry_id' => $id + 1, 'material_id' => $id, 'norm' => 2]);
            DB::table('material_purchase_order_names')->insert(['material_purchase_id' => $id, 'order_name_id' => $id]);
        }
    }

    public function test_modal_editing_loads_the_record_and_its_orders(): void
    {
        Livewire::test(MaterialPurchaseSearch::class)
            ->assertSee('Замовлення А')
            ->assertSeeHtml('name="_material_purchase_create"')
            ->assertDontSee('Перелік')
            ->call('editMaterialPurchase', 1)
            ->assertSet('editingMaterialPurchase.id', 1)
            ->assertDispatched('material-purchase-edit-open')
            ->assertSeeHtml('value="1" checked')
            ->call('editMaterialPurchase', 2)
            ->assertSet('editingMaterialPurchase.id', 2)
            ->assertSeeHtml('edit-material-purchase-form-2-2')
            ->assertSeeHtml('value="2" checked');
    }

    public function test_invalid_norm_preserves_submitted_orders(): void
    {
        session()->put('_old_input', ['_material_purchase_edit' => 1, 'norm' => 'invalid', 'orders' => [2], 'material_id' => 2, 'material' => 'Матеріал 2', 'unit' => 'кг']);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['norm' => 'Введіть кількість'])));
        Livewire::test(MaterialPurchaseSearch::class)
            ->assertSet('editingMaterialPurchase.id', 1)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="invalid"')
            ->assertSeeHtml('value="Матеріал 2"')
            ->assertSeeHtml('value="2" checked')
            ->assertDontSeeHtml('value="1" checked')
            ->assertSee('Введіть кількість');
    }

    public function test_filters_and_delete_keep_list_in_sync(): void
    {
        Livewire::test(MaterialPurchaseSearch::class)
            ->set('paginators.page', 3)
            ->set('searchTermChto', 'B200')
            ->assertSet('paginators.page', 1)
            ->assertSee('Матеріал 1')
            ->assertDontSee('Матеріал 2')
            ->call('editMaterialPurchase', 1)
            ->call('deleteMaterialPurchase', 1)
            ->assertSet('editingMaterialPurchase', null)
            ->assertSee('За вашим запитом замін матеріалів не знайдено.');
        $this->assertDatabaseMissing('material_purchases', ['id' => 1]);
    }
}
