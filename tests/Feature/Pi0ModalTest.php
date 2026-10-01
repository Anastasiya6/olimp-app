<?php

namespace Tests\Feature;

use App\Livewire\Pi0Search;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

class Pi0ModalTest extends TestCase
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
            $table->string('gost')->nullable();
            $table->unsignedInteger('type_unit_id')->nullable();
            $table->string('code_1c')->nullable();
            $table->integer('type')->default(0);
            $table->timestamps();
        });
        Schema::create('type_units', function (Blueprint $table) {
            $table->id();
            $table->string('unit');
        });
        DB::table('type_units')->insert([['id' => 1, 'unit' => 'шт'], ['id' => 2, 'unit' => 'кг']]);
        DB::table('designations')->insert([
            ['id' => 1, 'designation' => '100', 'name' => 'ПИ0 А', 'type' => 1],
            ['id' => 2, 'designation' => '200', 'name' => 'ПИ0 Б', 'type' => 1],
            ['id' => 3, 'designation' => '300', 'name' => 'Інший тип', 'type' => 0],
        ]);
    }

    public function test_modal_editing_loads_selected_product_and_refreshes_form(): void
    {
        Livewire::test(Pi0Search::class)
            ->assertDontSee('Перелік ПИ0')
            ->assertSeeHtml('name="_pi0_create" value="1"')
            ->assertDontSee('Інший тип')
            ->call('editPi0', 1)
            ->assertSet('editingPi0.id', 1)
            ->assertDispatched('pi0-edit-open')
            ->assertSeeHtml('edit-pi0-form-1-1')
            ->assertSeeHtml('name="_pi0_edit" value="1"')
            ->call('editPi0', 2)
            ->assertSet('editingPi0.id', 2)
            ->assertSeeHtml('edit-pi0-form-2-2')
            ->assertSeeHtml('name="_pi0_edit" value="2"');
    }

    public function test_edit_validation_reopens_modal_and_keeps_all_fields(): void
    {
        session()->put('_old_input', [
            '_pi0_edit' => 1, 'designation' => '200', 'name' => 'Нова назва',
            'gost' => '25-06', 'type_unit_id' => 2, 'code_1c' => '00123',
        ]);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag([
            'designation' => 'Такий креслярський номер вже є у виробах.',
        ])));

        Livewire::test(Pi0Search::class)
            ->assertSet('editingPi0.id', 1)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="Нова назва"')
            ->assertSeeHtml('value="25-06"')
            ->assertSeeHtml('value="00123"')
            ->assertSeeHtml('value="2" selected')
            ->assertSee('Такий креслярський номер вже є у виробах.');
    }

    public function test_create_validation_preserves_input_without_opening_edit_form(): void
    {
        session()->put('_old_input', ['_pi0_create' => 1, 'designation' => '100', 'name' => 'Новий виріб']);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['designation' => 'Номер вже існує'])));
        Livewire::test(Pi0Search::class)
            ->assertSet('editingPi0', null)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="Новий виріб"')
            ->assertSee('Номер вже існує');
    }

    public function test_filters_reset_pagination_and_combine_number_with_name(): void
    {
        Livewire::test(Pi0Search::class)
            ->set('paginators.page', 3)
            ->set('searchTerm', 'ПИ0')
            ->assertSet('paginators.page', 1)
            ->set('searchTermChto', '200')
            ->assertSee('ПИ0 Б')
            ->assertDontSee('ПИ0 А')
            ->set('searchTermChto', '999')
            ->assertSee('За вашим запитом записів ПИ0 не знайдено.');
    }

    public function test_gost_search_number_sorting_and_pdf_link_remain_available(): void
    {
        DB::table('designations')->where('id', 2)->update(['gost' => '123-45']);
        Livewire::test(Pi0Search::class)
            ->assertSeeHtml(route('pi0.all'))
            ->call('sortBy', 'designation')
            ->assertViewHas('items', fn ($items) => $items->pluck('id')->all() === [1, 2])
            ->assertSeeHtml('aria-sort="ascending"')
            ->call('sortBy', 'designation')
            ->assertViewHas('items', fn ($items) => $items->pluck('id')->all() === [2, 1])
            ->assertSeeHtml('aria-sort="descending"')
            ->set('searchTerm', '123-45')
            ->assertSee('ПИ0 Б')
            ->assertDontSee('ПИ0 А');
    }
}
