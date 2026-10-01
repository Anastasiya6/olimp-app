<?php
namespace Tests\Feature;
use App\Livewire\PlanTaskTable;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\Livewire;
use Tests\TestCase;

class PlanModalHarness extends PlanTaskTable
{
    protected function planTasks() { return new LengthAwarePaginator([], 0, 25); }
}
class PlanTaskModalTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');
        Schema::create('order_names', function (Blueprint $t) { $t->id(); $t->string('name'); $t->integer('quantity'); $t->boolean('is_order'); });
        Schema::create('departments', function (Blueprint $t) { $t->id(); $t->string('number'); });
        Schema::create('type_units', function (Blueprint $t) { $t->id(); $t->string('name'); });
        Schema::create('designations', function (Blueprint $t) { $t->id(); $t->string('designation'); });
        Schema::create('plan_tasks', function (Blueprint $t) {
            $t->id();
            foreach (['order_name_id', 'designation_id', 'sender_department_id', 'receiver_department_id', 'quantity', 'quantity_total'] as $field) $t->integer($field);
            $t->boolean('with_purchased'); $t->string('comment')->nullable();
        });
        DB::table('order_names')->insert(['id' => 1, 'name' => 'Order 1', 'quantity' => 4, 'is_order' => 1]);
        DB::table('departments')->insert([['id' => 2, 'number' => '02'], ['id' => 3, 'number' => '03']]);
        DB::table('designations')->insert(['id' => 1, 'designation' => 'ABC-123']);
        DB::table('plan_tasks')->insert(['id' => 1, 'order_name_id' => 1, 'designation_id' => 1, 'sender_department_id' => 2, 'receiver_department_id' => 3, 'quantity' => 5, 'quantity_total' => 20, 'with_purchased' => 1]);
    }
    public function test_create_and_edit_forms_preserve_context_and_routes(): void
    {
        Livewire::test(PlanModalHarness::class, ['selectedOrder' => 1, 'sender_department_id' => 2, 'receiver_department_id' => 3])
            ->call('openPlanForm')
            ->assertDispatched('plan-form-open')
            ->assertSet('planFormData.order_name_quantity', 4)
            ->assertSeeHtml('action="'.route('plan-tasks.store').'"')
            ->assertSeeHtml('name="sender_department_id" value="2"')
            ->call('openPlanForm', 1)
            ->assertSet('editingPlanTask.id', 1)
            ->assertSeeHtml('action="'.route('plan-tasks.update', 1).'"')
            ->assertSeeHtml('value="ABC-123"')
            ->assertSeeHtml('value="20"')
            ->call('openPlanForm')
            ->assertSet('editingPlanTask', null)
            ->assertSet('planFormVersion', 3);
    }
    public function test_quantity_calculator_keeps_existing_calculation(): void
    {
        Livewire::test(\App\Livewire\QuantityCalculator::class, ['order_name_quantity' => 4])
            ->call('updateQuantityTotal', 5)
            ->assertSet('quantity_total', 20);
    }
    public function test_delete_confirmation_and_deleting_loaded_record(): void
    {
        Livewire::test(PlanModalHarness::class)
            ->assertSeeHtml('aria-labelledby="delete-plan-task-title"')
            ->call('openPlanForm', 1)
            ->call('deletePlanTask', 1)
            ->assertSet('editingPlanTask', null)
            ->assertSet('planFormLoaded', false);
        $this->assertDatabaseMissing('plan_tasks', ['id' => 1]);
    }
}
