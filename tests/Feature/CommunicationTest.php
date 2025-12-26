<?php

namespace Tests\Feature;

use App\Modules\Core\Models\User;
use App\Modules\Clients\Models\Client;
use App\Modules\Communications\Models\Communication;
use App\Modules\Contacts\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommunicationTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_communications_page_requires_authentication(): void
    {
        $response = $this->get('/communications');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_communications_list(): void
    {
        $response = $this->actingAs($this->user)->get('/communications');

        $response->assertStatus(200);
        $response->assertSee('Communications');
    }

    public function test_communication_belongs_to_client(): void
    {
        $client = Client::factory()->create();
        $communication = Communication::factory()->create(['client_id' => $client->id]);

        $this->assertEquals($client->id, $communication->client->id);
    }

    public function test_communication_can_belong_to_contact(): void
    {
        $contact = Contact::factory()->create();
        $communication = Communication::factory()->create([
            'client_id' => $contact->client_id,
            'contact_id' => $contact->id,
        ]);

        $this->assertEquals($contact->id, $communication->contact->id);
    }

    public function test_communication_has_created_by_user(): void
    {
        $communication = Communication::factory()->create(['created_by' => $this->user->id]);

        $this->assertEquals($this->user->id, $communication->createdBy->id);
    }

    public function test_communication_can_be_marked_as_completed(): void
    {
        $communication = Communication::factory()->create(['status' => 'pending']);

        $communication->markAsCompleted();

        $this->assertEquals('completed', $communication->fresh()->status);
        $this->assertNotNull($communication->fresh()->completed_at);
    }

    public function test_overdue_scope_returns_overdue_communications(): void
    {
        $overdue = Communication::factory()->create([
            'due_at' => now()->subDay(),
            'completed_at' => null,
        ]);
        $notOverdue = Communication::factory()->create([
            'due_at' => now()->addDay(),
            'completed_at' => null,
        ]);

        $overdueComms = Communication::overdue()->get();

        $this->assertTrue($overdueComms->contains($overdue));
        $this->assertFalse($overdueComms->contains($notOverdue));
    }

    public function test_is_overdue_attribute(): void
    {
        $overdue = Communication::factory()->create([
            'due_at' => now()->subDay(),
            'completed_at' => null,
        ]);
        $notOverdue = Communication::factory()->create([
            'due_at' => now()->addDay(),
            'completed_at' => null,
        ]);

        $this->assertTrue($overdue->is_overdue);
        $this->assertFalse($notOverdue->is_overdue);
    }
}
