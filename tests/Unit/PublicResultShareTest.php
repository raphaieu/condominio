<?php

namespace Tests\Unit;

use App\Models\CondominiumResult;
use App\Models\GeneratedAsset;
use App\Models\ImageGeneration;
use App\Models\ThreadsAccount;
use App\Models\User;
use App\Support\PublicResultShare;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicResultShareTest extends TestCase
{
    use RefreshDatabase;

    public function test_builds_rich_meta_with_house_image(): void
    {
        $user = User::factory()->create();
        $account = ThreadsAccount::query()->create([
            'user_id' => $user->id,
            'threads_user_id' => 't-share',
            'username' => 'raphaieu',
            'connected_at' => now(),
        ]);

        $result = CondominiumResult::query()->create([
            'user_id' => $user->id,
            'threads_account_id' => $account->id,
            'score' => 58,
            'property_type' => 'Apartamento Funcional',
            'neighborhood' => 'Vila da Resenha',
            'symbolic_address' => 'Vila da Resenha, Bloco A, Unidade 042 — @raphaieu',
            'social_class' => 'Classe B Digital',
            'estimated_value' => 420_000,
            'is_public' => true,
            'generated_at' => now(),
        ]);

        $generation = ImageGeneration::query()->create([
            'user_id' => $user->id,
            'condominium_result_id' => $result->id,
            'status' => ImageGeneration::STATUS_COMPLETED,
            'provider' => 'mock',
        ]);

        $facade = GeneratedAsset::query()->create([
            'image_generation_id' => $generation->id,
            'user_id' => $user->id,
            'condominium_result_id' => $result->id,
            'type' => GeneratedAsset::TYPE_FACADE,
            'disk' => 'public',
            'path' => 'generated/1/1/facade.png',
            'public_url' => 'https://example.com/storage/generated/1/1/facade.png',
            'width' => 1024,
            'height' => 1536,
        ]);

        $meta = PublicResultShare::meta($account, $result, $facade);

        $this->assertStringContainsString('@raphaieu', $meta['title']);
        $this->assertStringContainsString('Apartamento Funcional', $meta['title']);
        $this->assertStringContainsString('Score 58/100', $meta['description']);
        $this->assertStringContainsString('Vila da Resenha', $meta['description']);
        $this->assertStringContainsString('Classe B Digital', $meta['description']);
        $this->assertStringContainsString('Casa gerada com IA', $meta['description']);
        $this->assertSame('summary_large_image', $meta['twitter_card']);
        $this->assertSame('https://example.com/storage/generated/1/1/facade.png', $meta['image']);
    }
}
