<?php

namespace Tests\Feature;

use App\Models\SalesTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

        $response->assertOk()
            ->assertSee('Lihat deskripsi fitur website')
            ->assertSee('Dashboard penjualan')
            ->assertSee('Analisis Drill-Down')
            ->assertSee('Simulasi What-If')
            ->assertSee('ETL Management');
    }

    public function test_language_and_theme_switchers_are_available_across_workspace_pages(): void
    {
        $this->withSession(['admin_authenticated' => true]);

        foreach (['/access', '/dashboard', '/drilldown', '/what-if', '/etl'] as $path) {
            $this->get($path)
                ->assertOk()
                ->assertSee('<select id="language"', false)
                ->assertSee('id="theme-toggle"', false)
                ->assertSee('app-theme')
                ->assertSee('js/language.js');
        }
    }

    public function test_dashboard_displays_sales_from_the_database(): void
    {
        $this->withSession(['admin_authenticated' => true]);

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

        $this->get('/dashboard?country=France')
            ->assertOk()
            ->assertSee('France')
            ->assertSee('$200.00')
            ->assertSee('$80.00');
    }

    public function test_what_if_simulation_uses_database_totals_and_requested_sliders(): void
    {
        $this->withSession(['admin_authenticated' => true]);

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
            ->assertSee('ANALISIS DINAMIS')
            ->assertSee('Saran keputusan bisnis')
            ->assertDontSee('Data keuangan di database masih bernilai nol');
    }

    public function test_what_if_zero_change_preserves_the_actual_profit_baseline(): void
    {
        $this->withSession(['admin_authenticated' => true]);
        SalesTransaction::create([
            'region' => 'Europe',
            'country' => 'France',
            'item_type' => 'Cosmetics',
            'sales_channel' => 'Online',
            'order_priority' => 'M',
            'order_date' => '2026-01-15',
            'order_id' => 100000094,
            'ship_date' => '2026-01-20',
            'units_sold' => 10,
            'unit_price' => 20,
            'unit_cost' => 12,
            'total_revenue' => 200,
            'total_cost' => 120,
            'total_profit' => 81,
        ]);

        $this->get('/what-if?country=France')
            ->assertOk()
            ->assertSee('$81.00')
            ->assertSee('Skenario relatif stabil');
    }

    public function test_drilldown_levels_can_be_opened_directly(): void
    {
        $this->withSession(['admin_authenticated' => true]);

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

    public function test_login_opens_the_workspace_gateway_or_etl_directly(): void
    {
        $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
            'destination' => 'gateway',
        ])->assertRedirect('/access');

        $this->post('/logout');

        $this->post('/login', [
            'username' => 'admin',
            'password' => 'admin',
            'destination' => 'etl',
        ])->assertRedirect('/etl');
    }

    public function test_protected_pages_require_admin_access(): void
    {
        $this->get('/etl')->assertRedirect('/');
        $this->get('/dashboard')->assertRedirect('/');
    }

    public function test_each_workspace_page_uses_its_own_background_image(): void
    {
        $this->withSession(['admin_authenticated' => true]);

        $backgrounds = [
            '/access' => 'europe-unity-mural.png',
            '/dashboard' => 'europe-blue-brush.png',
            '/drilldown' => 'europe-stars-abstract.png',
            '/what-if' => 'europe-sales-forecast.svg',
            '/etl' => 'europe-retail-logistics.svg',
        ];

        foreach ($backgrounds as $path => $image) {
            $this->get($path)->assertOk()->assertSee($image);
        }

        $this->flushSession();

        $this->get('/')
            ->assertOk()
            ->assertSee('europe-flag-landscape.png');
    }

    public function test_etl_upload_imports_a_valid_sales_csv(): void
    {
        $this->withSession(['admin_authenticated' => true]);

        $csv = implode("\n", [
            'region,country,item_type,sales_channel,order_priority,order_date,order_id,ship_date,units_sold,unit_price,unit_cost,total_revenue,total_cost,total_profit',
            'Europe,France,Cosmetics,Online,M,2026-01-15,100000099,2026-01-20,10,20,12,200,120,80',
        ]);

        $this->post('/etl/import', [
            'file' => UploadedFile::fake()->createWithContent('sales.csv', $csv),
        ])->assertRedirect('/etl')->assertSessionHas('status');

        $this->assertDatabaseHas('sales_transactions', [
            'order_id' => 100000099,
            'country' => 'France',
        ]);

        $this->post('/etl/undo')
            ->assertRedirect('/etl')
            ->assertSessionHas('status');

        $this->assertDatabaseMissing('sales_transactions', [
            'order_id' => 100000099,
        ]);
    }

    public function test_etl_upload_rejects_a_mismatched_header_without_importing_rows(): void
    {
        $this->withSession(['admin_authenticated' => true]);
        $csv = implode("\n", [
            'region,country,item_type,sales_channel,order_priority,order_date,order_id,ship_date,units_sold,wrong_price,unit_cost,total_revenue,total_cost,total_profit',
            'Europe,France,Cosmetics,Online,M,2026-01-15,100000097,2026-01-20,10,20,12,200,120,80',
        ]);

        $this->from('/etl')->post('/etl/import', [
            'file' => UploadedFile::fake()->createWithContent('invalid.csv', $csv),
        ])->assertRedirect('/etl')->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('sales_transactions', ['order_id' => 100000097]);
        $this->assertStringContainsString('Header file tidak sesuai', session('errors')->get('file')[0]);
    }

    public function test_etl_upload_reports_a_type_error_for_the_specific_column_and_row(): void
    {
        $this->withSession(['admin_authenticated' => true]);
        $csv = implode("\n", [
            'region,country,item_type,sales_channel,order_priority,order_date,order_id,ship_date,units_sold,unit_price,unit_cost,total_revenue,total_cost,total_profit',
            'Europe,France,Cosmetics,Online,M,2026-01-15,100000096,2026-01-20,10,not-a-number,12,200,120,80',
        ]);

        $this->from('/etl')->post('/etl/import', [
            'file' => UploadedFile::fake()->createWithContent('invalid-value.csv', $csv),
        ])->assertRedirect('/etl')->assertSessionHasErrors('file');

        $this->assertDatabaseMissing('sales_transactions', ['order_id' => 100000096]);
        $this->assertStringContainsString('Nilai unit_price tidak valid pada baris 2', session('errors')->get('file')[0]);
    }

    public function test_etl_template_download_contains_required_headers_and_valid_examples(): void
    {
        $this->withSession(['admin_authenticated' => true]);

        $response = $this->get('/etl/template');
        $response->assertOk()->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $content = $response->streamedContent();

        $this->assertStringContainsString('region,country,item_type,sales_channel,order_priority,order_date,order_id,ship_date,units_sold,unit_price,unit_cost,total_revenue,total_cost,total_profit', $content);
        $this->assertStringContainsString('Europe,France,Cosmetics,Online,M,2026-01-15,100000001,2026-01-20,10,20.00,12.00,200.00,120.00,80.00', $content);
    }

    public function test_etl_crud_requires_a_second_confirmation_and_update_can_be_undone(): void
    {
        $this->withSession(['admin_authenticated' => true]);
        $this->get('/etl')
            ->assertOk()
            ->assertSee('Manage transactions')
            ->assertSee('Add a transaction')
            ->assertSee('Download Template Format')
            ->assertSee('data-calculated="true"', false);

        $transaction = [
            'region' => 'Europe',
            'country' => 'France',
            'item_type' => 'Cosmetics',
            'sales_channel' => 'Online',
            'order_priority' => 'M',
            'order_date' => '2026-01-15',
            'order_id' => 100000098,
            'ship_date' => '2026-01-20',
            'units_sold' => 10,
            'unit_price' => 20,
            'unit_cost' => 12,
            'total_revenue' => 1,
            'total_cost' => 2,
            'total_profit' => -1,
        ];

        $this->post('/etl/transactions/preview', $transaction)
            ->assertRedirect('/etl/confirm');
        $this->assertDatabaseMissing('sales_transactions', ['order_id' => 100000098]);

        $token = $this->app['session.store']->get('etl_pending_change')['token'];
        $this->post('/etl/confirm', ['token' => $token])
            ->assertSessionHasErrors('confirmation');
        $this->assertDatabaseMissing('sales_transactions', ['order_id' => 100000098]);

        $this->post('/etl/cancel')->assertRedirect('/etl');
        $this->assertNull($this->app['session.store']->get('etl_pending_change'));

        $this->post('/etl/transactions/preview', $transaction)
            ->assertRedirect('/etl/confirm');
        $token = $this->app['session.store']->get('etl_pending_change')['token'];
        $this->post('/etl/confirm', [
            'token' => $token,
            'confirmation' => '1',
        ])->assertRedirect('/etl');
        $this->assertDatabaseHas('sales_transactions', [
            'order_id' => 100000098,
            'country' => 'France',
            'total_revenue' => 200,
            'total_cost' => 120,
            'total_profit' => 80,
        ]);

        $transactionId = SalesTransaction::where('order_id', 100000098)->value('id');
        $transaction['country'] = 'Germany';
        $this->post("/etl/transactions/{$transactionId}/preview-update", $transaction)
            ->assertRedirect('/etl/confirm');
        $this->assertDatabaseHas('sales_transactions', [
            'order_id' => 100000098,
            'country' => 'France',
        ]);

        $token = $this->app['session.store']->get('etl_pending_change')['token'];
        $this->post('/etl/confirm', [
            'token' => $token,
            'confirmation' => '1',
        ])->assertRedirect('/etl');
        $this->assertDatabaseHas('sales_transactions', [
            'order_id' => 100000098,
            'country' => 'Germany',
        ]);

        $this->post('/etl/undo')->assertRedirect('/etl');
        $this->assertDatabaseHas('sales_transactions', [
            'order_id' => 100000098,
            'country' => 'France',
        ]);

        $this->post("/etl/transactions/{$transactionId}/preview-delete")
            ->assertRedirect('/etl/confirm');
        $this->assertDatabaseHas('sales_transactions', ['order_id' => 100000098]);

        $token = $this->app['session.store']->get('etl_pending_change')['token'];
        $this->post('/etl/confirm', [
            'token' => $token,
            'confirmation' => '1',
        ])->assertRedirect('/etl');
        $this->assertDatabaseMissing('sales_transactions', ['order_id' => 100000098]);

        $this->post('/etl/undo')->assertRedirect('/etl');
        $this->assertDatabaseHas('sales_transactions', [
            'order_id' => 100000098,
            'country' => 'France',
        ]);
    }

    public function test_manual_etl_form_rejects_empty_and_non_numeric_fields(): void
    {
        $this->withSession(['admin_authenticated' => true]);
        $transaction = [
            'region' => 'Europe',
            'country' => '',
            'item_type' => 'Cosmetics',
            'sales_channel' => 'Online',
            'order_priority' => 'M',
            'order_date' => '2026-01-15',
            'order_id' => 100000095,
            'ship_date' => '2026-01-20',
            'units_sold' => 10,
            'unit_price' => 'not-a-number',
            'unit_cost' => 12,
        ];

        $this->from('/etl')->post('/etl/transactions/preview', $transaction)
            ->assertRedirect('/etl')
            ->assertSessionHasErrors(['country', 'unit_price']);

        $this->assertDatabaseMissing('sales_transactions', ['order_id' => 100000095]);
    }
}
