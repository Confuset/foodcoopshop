<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */

use Cake\Core\Configure;

if ($groupBy == 'customer' && Configure::read('appDb.FCS_RETAIL_MODE_ENABLED') && $appAuth->isSuperadmin()) {
    $dateFrom = $pickupDay[0];
    $dateTo = count($pickupDay) == 2 ? $pickupDay[1] : $dateFrom;
    echo '<td>';
        echo $this->Html->link(
            '<i class="fas fa-fw fa-file-invoice"></i> ' . __d('admin', 'Invoice'),
            '/admin/customers/getInvoice.pdf?customerId=' . $orderDetail['customer_id'] . '&dateFrom=' . $dateFrom . '&dateTo=' . $dateTo,
            [
                'escape' => false,
                'class' => 'btn btn-outline-light'
            ]
        );
    echo '</td>';
}

?>
