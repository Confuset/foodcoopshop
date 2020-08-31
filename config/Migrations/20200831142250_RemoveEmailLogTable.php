<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class RemoveEmailLogTable extends AbstractMigration
{
    public function change()
    {
        $this->execute('DROP TABLE fcs_email_logs;');
    }
}
