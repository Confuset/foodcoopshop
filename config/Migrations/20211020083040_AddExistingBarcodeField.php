<?php
declare(strict_types=1);

use Migrations\AbstractMigration;

class AddExistingBarcodeField extends AbstractMigration
{
    public function change()
    {
        $this->execute('ALTER TABLE `fcs_product` ADD `barcode` INT(13) UNSIGNED NULL DEFAULT NULL AFTER `unity`;');
    }
}
