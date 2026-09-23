<?php

namespace Tests\Feature;

use App\Livewire\IssuanceMaterialIndex;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class IssuanceMaterialFiltersTest extends TestCase
{
    public function test_order_filter_works_without_a_designation(): void
    {
        $component = new IssuanceMaterialIndex;
        $component->filterOrder = '123';
        $component->filterPlanDesignation = '';

        $queries = DB::connection()->pretend(fn () => $component->render());
        $query = collect($queries)->first(fn ($query) => str_contains($query['query'], 'material_issuances'));

        $this->assertNotNull($query);
        $this->assertSame(['123'], $query['bindings']);
        $this->assertStringContainsString('order_name_id', $query['query']);
        $this->assertStringNotContainsString('plan_task_designation_id', $query['query']);
    }

    public function test_search_combines_order_with_partial_plan_designation(): void
    {
        $component = new IssuanceMaterialIndex;
        $component->filterOrder = '123';
        $component->filterPlanDesignation = '  ААМВ685  ';

        $queries = DB::connection()->pretend(fn () => $component->render());
        $query = collect($queries)->first(fn ($query) => str_contains($query['query'], 'material_issuances'));

        $this->assertNotNull($query);
        $this->assertSame(['123', '%ААМВ685%', '%ААМВ685%'], $query['bindings']);
        $this->assertStringContainsString('plan_task_designation_id', $query['query']);
        $this->assertStringContainsString('received_by_user_id', $query['query']);
        $this->assertStringContainsString('and (exists', $query['query']);
        $this->assertStringContainsString('or exists', $query['query']);
    }

    public function test_search_accepts_recipient_surname_without_an_order(): void
    {
        $component = new IssuanceMaterialIndex;
        $component->filterPlanDesignation = '  Петренко  ';

        $queries = DB::connection()->pretend(fn () => $component->render());
        $query = collect($queries)->first(fn ($query) => str_contains($query['query'], 'material_issuances'));

        $this->assertNotNull($query);
        $this->assertSame(['%Петренко%', '%Петренко%'], $query['bindings']);
        $this->assertStringContainsString('received_by_user_id', $query['query']);
        $this->assertStringNotContainsString('issued_by_user_id', $query['query']);
    }

    public function test_blank_filters_leave_all_orders_and_plan_designations_available(): void
    {
        $component = new IssuanceMaterialIndex;
        $component->filterPlanDesignation = '   ';

        $queries = DB::connection()->pretend(fn () => $component->render());
        $query = collect($queries)->first(fn ($query) => str_contains($query['query'], 'material_issuances'));

        $this->assertNotNull($query);
        $this->assertSame([], $query['bindings']);
        $this->assertStringNotContainsString('plan_task_designation_id', $query['query']);
    }

    public function test_changing_filters_and_resetting_clear_page_and_print_selection(): void
    {
        $component = new IssuanceMaterialIndex;
        $component->paginators = ['page' => 4];
        $component->selectedItems = [15, 21];
        $component->updatedFilterPlanDesignation();

        $this->assertSame(1, $component->paginators['page']);
        $this->assertSame([], $component->selectedItems);

        $component->filterOrder = '123';
        $component->filterPlanDesignation = 'ААМВ';
        $component->paginators = ['page' => 3];
        $component->selectedItems = [38];
        $component->resetFilters();

        $this->assertSame('', $component->filterOrder);
        $this->assertSame('', $component->filterPlanDesignation);
        $this->assertSame(1, $component->paginators['page']);
        $this->assertSame([], $component->selectedItems);
    }
}
