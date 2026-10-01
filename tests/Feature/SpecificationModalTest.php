<?php

namespace Tests\Feature;

use App\Livewire\SpecificationSearch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

class SpecificationModalTest extends TestCase
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
        Schema::create('specifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('designation_id');
            $table->unsignedInteger('designation_entry_id');
            $table->string('designation');
            $table->decimal('quantity');
            $table->string('category_code')->nullable();
            $table->timestamps();
        });
        DB::table('designations')->insert([
            ['id' => 1, 'designation' => 'A100'],
            ['id' => 2, 'designation' => 'B200'],
            ['id' => 3, 'designation' => 'B300'],
        ]);
        foreach ([1, 2] as $id) {
            DB::table('specifications')->insert([
                'id' => $id, 'designation_id' => 1, 'designation_entry_id' => $id + 1,
                'designation' => 'A100', 'quantity' => $id * 10,
            ]);
        }
    }

    public function test_create_and_edit_forms_render_and_switch_between_records(): void
    {
        Livewire::test(SpecificationSearch::class)
            ->assertSet('searchTerm', 'A100')
            ->assertSeeHtml('name="_specification_create"')
            ->assertSeeHtml('delete-specification-title')
            ->call('editSpecification', 1)
            ->assertDispatched('specification-edit-open')
            ->assertSet('editingSpecification.id', 1)
            ->assertSeeHtml('edit-specification-form-1-1')
            ->call('editSpecification', 2)
            ->assertSet('editingSpecification.id', 2)
            ->assertSeeHtml('edit-specification-form-2-2');
    }

    public function test_validation_reopens_edit_modal_with_submitted_quantity_and_code(): void
    {
        session()->put('_old_input', ['_specification_edit' => 1, 'specification_quantity' => 'invalid', 'specification_category_code' => '09']);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['specification_quantity' => 'Кількість повинна бути числовим значенням'])));
        Livewire::test(SpecificationSearch::class)
            ->assertSet('editingSpecification.id', 1)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('name="specification_quantity" value="invalid"')
            ->assertSeeHtml('name="specification_category_code" value="09"')
            ->assertSee('Кількість повинна бути числовим значенням');
    }

    public function test_filters_reset_pagination_and_empty_catalog_can_render(): void
    {
        Livewire::test(SpecificationSearch::class)
            ->set('paginators.page', 3)
            ->set('searchTermChto', 'B200')
            ->assertSet('paginators.page', 1)
            ->assertViewHas('specifications', fn ($items) => $items->pluck('id')->all() === [1])
            ->set('exactMatch', true)
            ->assertViewHas('specifications', fn ($items) => $items->pluck('id')->all() === [1]);
        DB::table('specifications')->delete();
        Livewire::test(SpecificationSearch::class)
            ->assertSee('Записів M0020 поки немає.')
            ->assertSeeHtml('name="_specification_create"');
    }

    public function test_create_validation_restores_new_designation_fields(): void
    {
        session()->put('_old_input', [
            '_specification_create' => 1, 'designation_designation' => 'NEW100',
            'designation_entry_designation' => 'ПИ0999', 'designation_name' => 'Новий виріб',
            'designation_entry_designation_name' => 'Нова складова',
            'designation_entry_gost' => '123-45', 'specification_quantity' => 'invalid',
        ]);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['specification_quantity' => 'Введіть кількість'])));
        Livewire::test(SpecificationSearch::class)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="Новий виріб"')
            ->assertSeeHtml('value="Нова складова"')
            ->assertSeeHtml('value="123-45"')
            ->assertSeeHtml('name="specification_quantity" value="invalid"');
    }
}
