<?php

namespace Tests\Feature;

use App\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_contacts_page_requires_authentication(): void
    {
        $response = $this->get('/contacts');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_contacts_list(): void
    {
        $response = $this->actingAs($this->user)->get('/contacts');

        $response->assertStatus(200);
        $response->assertSee('Contacts');
    }

    public function test_authenticated_user_can_view_create_contact_form(): void
    {
        $response = $this->actingAs($this->user)->get('/contacts/create');

        $response->assertStatus(200);
        $response->assertSee('Create Contact');
    }

    public function test_authenticated_user_can_view_contact(): void
    {
        $contact = Contact::factory()->create();

        $response = $this->actingAs($this->user)->get("/contacts/{$contact->id}");

        $response->assertStatus(200);
        $response->assertSee($contact->first_name);
    }

    public function test_contact_belongs_to_client(): void
    {
        $client = Client::factory()->create();
        $contact = Contact::factory()->create(['client_id' => $client->id]);

        $this->assertEquals($client->id, $contact->client->id);
    }
}
