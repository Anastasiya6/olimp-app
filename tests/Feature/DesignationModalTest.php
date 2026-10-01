<?php

namespace Tests\Feature;

use App\Livewire\DesignationSearch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

class DesignationModalTest extends TestCase
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
            $table->string('route')->nullable();
            $table->string('code_1c')->nullable();
            $table->integer('type')->default(0);
            $table->timestamps();
        });
        DB::table('designations')->insert([
            ['id' => 1, 'designation' => '100', 'name' => 'Виріб А', 'type' => 0],
            ['id' => 2, 'designation' => '200', 'name' => 'Виріб Б', 'type' => 0],
            ['id' => 3, 'designation' => '300', 'name' => 'Інший тип', 'type' => 1],
        ]);
    }

    public function test_modal_editing_loads_selected_product_and_refreshes_form(): void
    {
        Livewire::test(DesignationSearch::class)
            ->assertDontSee('Перелік виробів')
            ->assertSeeHtml('name="_designation_create" value="1"')
            ->assertDontSee('Інший тип')
            ->call('editDesignation', 1)
            ->assertSet('editingDesignation.id', 1)
            ->assertDispatched('designation-edit-open')
            ->assertSeeHtml('edit-designation-form-1-1')
            ->assertSeeHtml('name="_designation_edit" value="1"')
            ->call('editDesignation', 2)
            ->assertSet('editingDesignation.id', 2)
            ->assertSeeHtml('edit-designation-form-2-2')
            ->assertSeeHtml('name="_designation_edit" value="2"');
    }

    public function test_edit_validation_reopens_modal_and_keeps_all_fields(): void
    {
        session()->put('_old_input', [
            '_designation_edit' => 1, 'designation' => '200', 'name' => 'Нова назва',
            'route' => '25-06', 'code_1c' => '00123',
        ]);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag([
            'designation' => 'Такий креслярський номер вже є у виробах.',
        ])));

        Livewire::test(DesignationSearch::class)
            ->assertSet('editingDesignation.id', 1)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="Нова назва"')
            ->assertSeeHtml('value="25-06"')
            ->assertSeeHtml('value="00123"')
            ->assertSee('Такий креслярський номер вже є у виробах.');
    }

    public function test_create_validation_preserves_input_without_opening_edit_form(): void
    {
        session()->put('_old_input', ['_designation_create' => 1, 'designation' => '100', 'name' => 'Новий виріб']);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['designation' => 'Номер вже існує'])));
        Livewire::test(DesignationSearch::class)
            ->assertSet('editingDesignation', null)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="Новий виріб"')
            ->assertSee('Номер вже існує');
    }

    public function test_filters_reset_pagination_and_combine_number_with_name(): void
    {
        Livewire::test(DesignationSearch::class)
            ->set('paginators.page', 3)
            ->set('searchTerm', 'Виріб')
            ->assertSet('paginators.page', 1)
            ->set('searchTermChto', '200')
            ->assertSee('Виріб Б')
            ->assertDontSee('Виріб А')
            ->set('searchTermChto', '999')
            ->assertSee('За вашим запитом виробів не знайдено.');
    }
}
