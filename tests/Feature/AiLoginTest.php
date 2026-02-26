<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Description of AiLoginTest
 *
 * @author IDRISAH
 */
class AiLoginTest extends TestCase {
    use RefreshDatabase;
    
    public function test_normal_login_works()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);
        
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        
        $response->assertStatus(200)
            ->assertJsonStructure(['token', 'user']);
    }
    
    public function test_suspicious_login_triggers_verification()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123')
        ]);
        
        // Simulate login from unusual location
        $response = $this->withHeaders([
            'X-Forwarded-For' => '8.8.8.8', // Google DNS - different location
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
        ])->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password123'
        ]);
        
        $response->assertStatus(200)
            ->assertJson(['requires_verification' => true]);
    }
    
    public function test_verification_works()
    {
        // Test verification flow
    }
    
    public function test_admin_can_view_dashboard()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        
        $response = $this->actingAs($admin)
            ->getJson('/api/admin/dashboard');
            
        $response->assertStatus(200)
            ->assertJsonStructure(['stats', 'recent_suspicious_activities']);
    }
}
