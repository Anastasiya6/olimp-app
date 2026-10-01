<?php

namespace Tests\Feature;

use App\Livewire\DesignationMaterialSearch;
use App\Livewire\DesignationSearchDropdown;
use App\Livewire\MaterialSearchDropdown;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

class DesignationMaterialModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        // Livewire's test requests bypass the session middleware.
        $this->app->rebinding('request', function ($app, $request) {
            $request->setLaravelSession($app['session.store']);
        });
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('designations', function (Blueprint $table) {
            $table->id();
            $table->string('designation');
        });
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->integer('number');
        });
        Schema::create('designation_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('designation_id');
            $table->unsignedInteger('material_id');
            $table->unsignedInteger('department_id');
            $table->decimal('norm');
            $table->timestamps();
        });
        DB::table('designations')->insert(['id' => 1, 'designation' => 'Деталь А']);
        DB::table('materials')->insert([
            ['id' => 1, 'name' => 'Матеріал А'],
            ['id' => 2, 'name' => 'Матеріал Б'],
        ]);
        DB::table('departments')->insert(['id' => 1, 'number' => 25]);
        foreach ([1, 2] as $id) {
            DB::table('designation_materials')->insert([
                'id' => $id, 'designation_id' => 1, 'material_id' => $id,
                'department_id' => 1, 'norm' => $id * 10,
            ]);
        }
    }

    public function test_edit_modal_loads_each_selected_record_and_remounts_its_form(): void
    {
        Livewire::test(DesignationMaterialSearch::class)
            ->assertSeeHtml('create-norm-title')
            ->assertSeeHtml('name="_norm_create"')
            ->call('editNorm', 1)
            ->assertSet('editingNorm.id', 1)
            ->assertDispatched('norm-edit-open')
            ->assertSeeHtml('edit-norm-form-1-1')
            ->assertSeeHtml('name="_norm_edit" value="1"')
            ->call('editNorm', 2)
            ->assertSet('editingNorm.id', 2)
            ->assertSeeHtml('edit-norm-form-2-2')
            ->assertSeeHtml('name="_norm_edit" value="2"');
    }

    public function test_validation_return_reopens_edit_form_and_preserves_input(): void
    {
        session()->put('_old_input', [
            '_norm_edit' => 1, 'norm' => 'invalid',
            'material_id' => 2, 'material' => 'Матеріал Б',
        ]);
        session()->put('errors', (new \Illuminate\Support\ViewErrorBag)->put(
            'default', new \Illuminate\Support\MessageBag(['norm' => 'Норма повинна бути числовим значенням'])
        ));

        Livewire::test(DesignationMaterialSearch::class)
            ->assertSet('editingNorm.id', 1)
            ->assertSeeHtml('name="norm" value="invalid"')
            ->assertSeeHtml('id="create-norm-value" type="text" name="norm" value=""')
            ->assertSeeHtml('show: true')
            ->assertSee('Норма повинна бути числовим значенням');

        Livewire::test(MaterialSearchDropdown::class, [
            'material_id' => 1, 'material_name' => 'Матеріал А', 'restore_input' => true,
        ])->assertSet('selectedMaterialId', 2)->assertSet('selectedMaterial', 'Матеріал Б');
    }

    public function test_create_prefills_last_norm_and_restores_submitted_selections(): void
    {
        $params = ['designation_hidden' => 'designation_id', 'designation_name' => 'designation',
            'designation_title' => 'Деталь', 'last_record' => \App\Models\DesignationMaterial::class];
        Livewire::test(DesignationSearchDropdown::class, $params)->assertSet('selectedDesignationId', 1);
        Livewire::test(MaterialSearchDropdown::class, [
            'material_id' => null, 'material_name' => null,
            'last_record' => \App\Models\DesignationMaterial::class,
        ])->assertSet('selectedMaterialId', 2);

        session()->put('_old_input', ['designation_id' => 7, 'designation' => 'Деталь Б']);
        Livewire::test(DesignationSearchDropdown::class, $params + ['restore_input' => true])
            ->assertSet('selectedDesignationId', 7)->assertSet('selectedDesignation', 'Деталь Б');
    }
}

