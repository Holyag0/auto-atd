<?php

namespace Database\Seeders;

use App\Models\Setor;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $usuarios = [
            [
                'name' => 'Administrador',
                'email' => 'admin@auto-atd.com',
                'setor' => 'DIAF',
            ],
            [
                'name' => 'Maria Silva',
                'email' => 'maria.silva@auto-atd.com',
                'setor' => 'TESOURARIA',
            ],
            [
                'name' => 'João Santos',
                'email' => 'joao.santos@auto-atd.com',
                'setor' => 'JURÍDICO',
            ],
            [
                'name' => 'Ana Costa',
                'email' => 'ana.costa@auto-atd.com',
                'setor' => 'PECÚLIO',
            ],
            [
                'name' => 'Pedro Lima',
                'email' => 'pedro.lima@auto-atd.com',
                'setor' => 'TI',
            ],
            [
                'name' => 'Carlos Mendes',
                'email' => 'carlos.mendes@auto-atd.com',
                'setor' => 'PLANO DE SAÚDE',
            ],
        ];

        foreach ($usuarios as $userData) {
            $setor = Setor::where('nome', $userData['setor'])->first();

            if ($setor) {
                User::updateOrCreate(
                    ['email' => $userData['email']],
                    [
                        'name' => $userData['name'],
                        'password' => Hash::make('senha123'),
                        'setor_id' => $setor->id,
                    ]
                );

                $this->command->info("✅ Usuário criado: {$userData['name']} - {$userData['setor']}");
            }
        }

        $this->command->info('');
        $this->command->info('📊 Resumo:');
        $this->command->info('Total de usuários: ' . User::count());
        $this->command->info('Usuários com setor: ' . User::whereNotNull('setor_id')->count());
        $this->command->info('');
        $this->command->info('🔑 Senha padrão para todos: senha123');
        $this->command->info('🔑 Email admin: admin@auto-atd.com');
    }
}
