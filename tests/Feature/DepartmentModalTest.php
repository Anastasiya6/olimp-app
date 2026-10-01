<?php

namespace Tests\Feature;

use App\Livewire\DepartmentIndex;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\MessageBag;
use Illuminate\Support\ViewErrorBag;
use Livewire\Livewire;
use Tests\TestCase;

class DepartmentModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->app->rebinding('request', function ($app, $request) {
            $request->setLaravelSession($app['session.store']);
        });
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('number');
            $table->timestamps();
        });
        DB::table('departments')->insert([['id' => 1, 'number' => '06'], ['id' => 2, 'number' => '25']]);
    }

    public function test_all_departments_and_modal_record_switching(): void
    {
        Livewire::test(DepartmentIndex::class)
            ->assertSee('Записів: 2')
            ->assertSeeHtml('name="_department_create"')
            ->assertDontSee('Перелік')
            ->call('editDepartment', 1)
            ->assertDispatched('department-edit-open')
            ->assertSet('editingDepartment.id', 1)
            ->assertSeeHtml('value="06"')
            ->call('editDepartment', 2)
            ->assertSet('editingDepartment.id', 2)
            ->assertSeeHtml('edit-department-form-2-2')
            ->assertSeeHtml('value="25"');
    }

    public function test_invalid_number_returns_to_edit_modal(): void
    {
        session()->put('_old_input', ['_department_edit' => 1, 'number' => '25']);
        session()->put('errors', (new ViewErrorBag)->put('default', new MessageBag(['number' => 'Такий цех вже є в довіднику'])));
        Livewire::test(DepartmentIndex::class)
            ->assertSet('editingDepartment.id', 1)
            ->assertSeeHtml('show: true')
            ->assertSeeHtml('value="25"')
            ->assertSee('Такий цех вже є в довіднику');
    }
}
