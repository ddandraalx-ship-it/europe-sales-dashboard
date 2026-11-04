<?php

namespace Tests\Feature;

use App\Models\SalesTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }

    public function test_dashboard_displays_sales_from_the_database(): void
    {
        SalesTransaction::create([
            'region' => 'Europe',
            'country' => 'France',
            'item_type' => 'Cosmetics',
            'sales_channel' => 'Online',
            'order_priority' => 'M',
            'order_date' => '2026-01-15',
            'order_id' => 100000001,
            'ship_date' => '2026-01-20',
            'units_sold' => 10,
            'unit_price' => 20,
            'unit_cost' => 12,
            'total_revenue' => 200,
            'total_cost' => 120,
            'total_profit' => 80,
        ]);

        $this->get('/?country=France')
            ->assertOk()
            ->assertSee('France')
            ->assertSee('$200.00')
            ->assertSee('$80.00');
    }

    public function test_what_if_simulation_uses_database_totals_and_requested_sliders(): void
    {
        SalesTransaction::create([
            'region' => 'Europe',
            'country' => 'France',
            'item_type' => 'Cosmetics',
            'sales_channel' => 'Online',
            'order_priority' => 'M',
            'order_date' => '2026-01-15',
            'order_id' => 100000002,
            'ship_date' => '2026-01-20',
            'units_sold' => 10,
            'unit_price' => 20,
            'unit_cost' => 12,
            'total_revenue' => 200,
            'total_cost' => 120,
            'total_profit' => 80,
        ]);

        $this->get('/what-if?country=France&price_change=10')
            ->assertOk()
            ->assertSee('$220.00')
            ->assertSee('$120.00')
            ->assertSee('$100.00')
            ->assertDontSee('Data keuangan di database masih bernilai nol');
    }

    public function test_drilldown_levels_can_be_opened_directly(): void
    {
        SalesTransaction::create([
            'region' => 'Europe',
            'country' => 'France',
            'item_type' => 'Cosmetics',
            'sales_channel' => 'Online',
            'order_priority' => 'M',
            'order_date' => '2026-01-15',
            'order_id' => 100000003,
            'ship_date' => '2026-01-20',
            'units_sold' => 10,
            'unit_price' => 20,
            'unit_cost' => 12,
            'total_revenue' => 200,
            'total_cost' => 120,
            'total_profit' => 80,
        ]);

        foreach (range(1, 4) as $level) {
            $this->get("/drilldown?level={$level}&country=France&channel=Online&item_type=Cosmetics")
                ->assertOk()
                ->assertSee("LEVEL {$level}")
                ->assertSee('href="', false);
        }
    }
}
