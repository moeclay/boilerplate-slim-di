<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateUsersTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('users', ['id' => false, 'primary_key' => ['id']]);

        $table
            ->addColumn('id', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('email', 'string', ['limit' => 190])
            ->addColumn('password_hash', 'string', ['limit' => 255])
            ->addColumn('email_verified_at', 'datetime', ['null' => true])
            ->addColumn('api_key_prefix', 'string', ['limit' => 20, 'null' => true])
            ->addColumn('api_key_hash', 'string', ['limit' => 255, 'null' => true])
            ->addColumn('created_at', 'datetime', ['null' => false])
            ->addColumn('updated_at', 'datetime', ['null' => false])
            ->addIndex(['email'], ['unique' => true])
            ->addIndex(['api_key_hash'], ['unique' => true])
            ->create();
    }
}