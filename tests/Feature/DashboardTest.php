<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * TEST 1 : Les invités (non connectés) sont redirigés vers le login.
     */
    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * TEST 2 : Un utilisateur connecté mais sans email vérifié est bloqué.
     */
    public function test_unverified_users_cannot_access_dashboard(): void
    {
        // On crée un utilisateur avec email_verified_at à null
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        // Laravel redirige vers la page de vérification d'email
        // On vérifie simplement qu'il y a une redirection (302)
        $response->assertStatus(302);
    }

    /**
     * TEST 3 : L'Admin peut accéder au dashboard.
     */
    public function test_admin_can_access_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        // On attend un succès (200) car web.php appelle AdminController->index()
        $response->assertStatus(200);
    }

    /**
     * TEST 4 : Le Driver peut accéder au dashboard.
     */
    public function test_driver_can_access_dashboard(): void
    {
        $driver = User::factory()->create([
            'role' => 'driver',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($driver)->get('/dashboard');

        $response->assertStatus(200);
    }

    /**
     * TEST 5 : Le Buyer peut accéder au dashboard.
     */
    public function test_buyer_can_access_dashboard(): void
    {
        $buyer = User::factory()->create([
            'role' => 'buyer',
            'email_verified_at' => now(),
        ]);

        $response = $this->actingAs($buyer)->get('/dashboard');

        $response->assertStatus(200);
    }
}