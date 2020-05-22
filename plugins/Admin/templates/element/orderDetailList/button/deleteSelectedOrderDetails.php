<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
use Cake\Core\Configure;

if ($deposit == '' && $groupBy == '' && count($orderDetails) > 0 && (!$appAuth->isCustomer() || Configure::read('app.isCustomerAllowedToModifyOwnOrders'))) {
    $this->element('addScript', [
        'script' => Configure::read('app.jsNamespace').".ModalOrderDetailDelete.initBulk();"
    ]);
    echo '<a id="deleteSelectedProductsButton" class="btn btn-outline-light" href="javascript:void(0);"><i class="fas fa-minus-circle"></i> ' . __d('admin', 'Cancel_selected_products') . '</a>';
}

?>