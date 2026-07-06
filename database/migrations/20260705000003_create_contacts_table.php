<?php
declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateContactsTable extends AbstractMigration
{
    public function change(): void
    {
        $table = $this->table('contacts', ['id' => false, 'primary_key' => ['id']]);

        $table
            ->addColumn('id', 'integer', ['identity' => true, 'signed' => false])
            ->addColumn('user_id', 'integer', ['signed' => false])
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('email', 'string', ['limit' => 190])
            ->addColumn('phone', 'string', ['limit' => 50, 'null' => true])
            ->addColumn('message', 'text', ['null' => true])
            ->addColumn('created_at', 'datetime')
            ->addColumn('updated_at', 'datetime')
            ->addIndex(['user_id'])
            ->addIndex(['email'])
            ->addForeignKey('user_id', 'users', 'id', [
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
            ])
            ->create();
    }
}