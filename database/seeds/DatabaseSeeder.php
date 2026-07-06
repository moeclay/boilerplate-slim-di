<?php
declare(strict_types=1);

use Phinx\Seed\AbstractSeed;

final class DatabaseSeeder extends AbstractSeed
{
    public function run(): void
    {
        $this->table('users')->insert([
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
                'email_verified_at' => date('Y-m-d H:i:s'),
                'api_key_prefix' => 'pk_',
                'api_key_hash' => hash('sha256', 'pk_example'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]
        ])->saveData();
    }
}