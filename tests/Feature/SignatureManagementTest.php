<?php

namespace Tests\Feature;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SignatureManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /** @test */
    public function user_can_upload_valid_signature()
    {
        $user = User::factory()->create();
        
        $file = UploadedFile::fake()->image('signature.png', 100, 100);

        $response = $this->actingAs($user)->post(route('signatures.store'), [
            'signature' => $file,
            'label' => 'My Signature',
        ]);

        $response->assertRedirect(route('signatures.index'));
        
        $this->assertDatabaseHas('signatures', [
            'user_id' => $user->id,
            'label' => 'My Signature',
        ]);
    }

    /** @test */
    public function user_cannot_upload_invalid_file_type()
    {
        $user = User::factory()->create();
        
        $file = UploadedFile::fake()->create('document.pdf', 100);

        $response = $this->actingAs($user)->post(route('signatures.store'), [
            'signature' => $file,
            'label' => 'My Signature',
        ]);

        $response->assertSessionHasErrors('signature');
        
        $this->assertDatabaseMissing('signatures', [
            'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function user_cannot_upload_file_too_large()
    {
        $user = User::factory()->create();
        
        $file = UploadedFile::fake()->image('signature.png', 100, 100)->size(2048); // 2MB

        $response = $this->actingAs($user)->post(route('signatures.store'), [
            'signature' => $file,
            'label' => 'My Signature',
        ]);

        $response->assertSessionHasErrors('signature');
    }

    /** @test */
    public function user_can_view_own_signature()
    {
        $user = User::factory()->create();
        $signature = Signature::factory()->create([
            'user_id' => $user->id,
            'path' => 'signatures/1/test.png',
        ]);

        $response = $this->actingAs($user)->get(route('signatures.show', $signature));

        $response->assertStatus(200);
    }

    /** @test */
    public function user_cannot_view_another_users_signature()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $signature = Signature::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->get(route('signatures.show', $signature));

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_update_own_signature_label()
    {
        $user = User::factory()->create();
        $signature = Signature::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->put(route('signatures.update', $signature), [
            'label' => 'Updated Label',
        ]);

        $response->assertRedirect(route('signatures.index'));
        
        $this->assertDatabaseHas('signatures', [
            'id' => $signature->id,
            'label' => 'Updated Label',
        ]);
    }

    /** @test */
    public function user_can_delete_own_signature()
    {
        $user = User::factory()->create();
        $signature = Signature::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user)->delete(route('signatures.destroy', $signature));

        $response->assertRedirect(route('signatures.index'));
        
        $this->assertSoftDeleted('signatures', [
            'id' => $signature->id,
        ]);
    }

    /** @test */
    public function user_cannot_delete_another_users_signature()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $signature = Signature::factory()->create([
            'user_id' => $user2->id,
        ]);

        $response = $this->actingAs($user1)->delete(route('signatures.destroy', $signature));

        $response->assertStatus(404);
    }

    /** @test */
    public function user_can_see_only_own_signatures_in_index()
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $signature1 = Signature::factory()->create(['user_id' => $user1->id]);
        $signature2 = Signature::factory()->create(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->get(route('signatures.index'));

        $response->assertStatus(200);
        $response->assertViewHas('signatures', function ($signatures) use ($signature1, $signature2) {
            return $signatures->contains($signature1) && !$signatures->contains($signature2);
        });
    }
}
