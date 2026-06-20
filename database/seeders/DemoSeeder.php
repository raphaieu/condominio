<?php

namespace Database\Seeders;

use App\Models\CondominiumResult;
use App\Models\ThreadsAccount;
use App\Models\ThreadsProfileSnapshot;
use App\Models\User;
use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::query()->create([
            'name' => 'Morador Demo',
            'email' => 'demo@condominio-threads.test',
        ]);

        $account = ThreadsAccount::query()->create([
            'user_id' => $user->id,
            'threads_user_id' => 'demo_threads_user_001',
            'username' => 'morador_demo',
            'name' => 'Morador Demo',
            'avatar_url' => 'https://ui-avatars.com/api/?name=MD&background=14b8a6&color=fff&size=256',
            'biography' => 'Conta demo do Condominio Threads para testes locais.',
            'is_verified' => true,
            'access_token' => 'demo_token_encrypted',
            'connected_at' => now(),
        ]);

        ThreadsProfileSnapshot::query()->create([
            'threads_account_id' => $account->id,
            'followers_count' => 12500,
            'views' => 89000,
            'likes' => 4200,
            'replies' => 890,
            'reposts' => 340,
            'quotes' => 120,
            'clicks' => 450,
            'posts_count' => 47,
            'captured_at' => now(),
        ]);

        CondominiumResult::query()->create([
            'user_id' => $user->id,
            'threads_account_id' => $account->id,
            'score' => 68.50,
            'property_type' => 'Sobrado Premium',
            'neighborhood' => 'Parque das Endorfinas',
            'symbolic_address' => 'Parque das Endorfinas, Bloco D, Unidade 890 — @morador_demo',
            'social_class' => 'Classe B+ Digital',
            'estimated_value' => 980000,
            'description' => '@morador_demo chegou ao Condominio Threads com score 69 e conquistou um Sobrado Premium no Parque das Endorfinas.',
            'is_public' => true,
            'generated_at' => now(),
        ]);
    }
}
