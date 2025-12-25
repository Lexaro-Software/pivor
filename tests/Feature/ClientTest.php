<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_clients_page_requires_authentication(): void
    {
        $response = $this->get('/clients');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_clients_list(): void
    {
        $response = $this->actingAs($this->user)->get('/clients');

        $response->assertStatus(200);
        $response->assertSee('Clients');
    }

    public function test_authenticated_user_can_view_create_client_form(): void
    {
        $response = $this->actingAs($this->user)->get('/clients/create');

        $response->assertStatus(200);
        $response->assertSee('Create Client');
    }

    public function test_authenticated_user_can_view_client(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->user)->get("/clients/{$client->id}");

        $response->assertStatus(200);
        $response->assertSee($client->name);
    }

    public function test_client_has_contacts_relationship(): void
    {
        $client = Client::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $client->contacts);
    }

    public function test_client_has_communications_relationship(): void
    {
        $client = Client::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $client->communications);
    }

    public function test_active_scope_filters_active_clients(): void
    {
        $activeClient = Client::factory()->create(['status' => 'active']);
        $inactiveClient = Client::factory()->create(['status' => 'inactive']);

        $activeClients = Client::active()->get();

        $this->assertTrue($activeClients->contains($activeClient));
        $this->assertFalse($activeClients->contains($inactiveClient));
    }

    public function test_prospect_scope_filters_prospects(): void
    {
        $prospect = Client::factory()->create(['status' => 'prospect']);
        $activeClient = Client::factory()->create(['status' => 'active']);

        $prospects = Client::prospects()->get();

        $this->assertTrue($prospects->contains($prospect));
        $this->assertFalse($prospects->contains($activeClient));
    }

    public function test_display_name_returns_trading_name_when_available(): void
    {
        $client = Client::factory()->create([
            'name' => 'Legal Company Name',
            'trading_name' => 'Trading As Name',
        ]);

        $this->assertEquals('Trading As Name', $client->display_name);
    }

    public function test_display_name_returns_name_when_no_trading_name(): void
    {
        $client = Client::factory()->create([
            'name' => 'Legal Company Name',
            'trading_name' => null,
        ]);

        $this->assertEquals('Legal Company Name', $client->display_name);
    }
}
