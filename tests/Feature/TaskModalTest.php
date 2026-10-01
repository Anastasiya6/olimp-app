<?php
namespace Tests\Feature;
use App\Livewire\TaskSearch;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;
class TaskModalHarness extends TaskSearch
{
    protected function tasks() { return collect(); }
}
class TaskModalTest extends TestCase
{
    public static function taskTypes(): array
    {
        return [['department'], ['technologist']];
    }

    /** @dataProvider taskTypes */
    public function test_department_create_edit_and_switching(string $type): void
    {
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('departments', function (Blueprint $t) { $t->id(); $t->string('number'); });
        Schema::create('designations', function (Blueprint $t) { $t->id(); $t->string('designation'); });
        Schema::create('tasks', function (Blueprint $t) { $t->id(); $t->integer('designation_id'); $t->integer('department_id'); $t->integer('quantity'); $t->string('type'); });
        DB::table('departments')->insert(['id' => 2, 'number' => '02']);
        DB::table('designations')->insert(['id' => 1, 'designation' => 'ABC-123']);
        DB::table('tasks')->insert(['id' => 1, 'designation_id' => 1, 'department_id' => 2, 'quantity' => 7, 'type' => $type]);
        Livewire::withQueryParams(['type' => $type])->test(TaskModalHarness::class)
            ->set('selectedDepartmentSender', 2)
            ->call('openTaskForm')
            ->assertDispatched('task-form-open')
            ->assertSeeHtml('action="'.route('tasks.store', ['type' => $type]).'"')
            ->assertSeeHtml('name="department_id" value="2"')
            ->call('openTaskForm', 1)
            ->assertSet('editingTask.id', 1)
            ->assertSeeHtml('action="'.route('tasks.update', 1).'"')
            ->assertSeeHtml('value="ABC-123"')
            ->assertSeeHtml('value="7"')
            ->call('openTaskForm')
            ->assertSet('editingTask', null)
            ->assertSet('taskFormVersion', 3)
            ->assertSeeHtml('aria-labelledby="delete-task-title"')
            ->call('openTaskForm', 1)
            ->call('deleteTask', 1)
            ->assertSet('editingTask', null)
            ->assertSet('taskFormLoaded', false);
        $this->assertDatabaseMissing('tasks', ['id' => 1]);
    }
}
