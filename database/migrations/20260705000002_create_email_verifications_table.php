<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateEmailVerificationsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('email_verifications', ['id' => false, 'primary_key' => ['id']]);

        $table
            ->addColumn('id', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('token_hash', 'string', ['limit' => 255])
            ->addColumn('expires_at', 'datetime')
            ->addColumn('consumed_at', 'datetime', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addIndex(['token_hash'], ['unique' => true])
            ->addIndex(['user_id'])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}