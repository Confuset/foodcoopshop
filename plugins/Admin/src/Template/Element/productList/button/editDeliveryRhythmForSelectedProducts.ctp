<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.4.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
use Cake\Core\Configure;

if (!empty($products)) {
    $this->element('addScript', [
        'script' => Configure::read('app.jsNamespace').".Admin.initEditDeliveryRhythmForSelectedProducts();"
    ]);
    echo '<a id="editDeliveryRhythmForSelectedProducts" class="btn btn-outline-light" href="javascript:void(0);"><i class="far fa-clock"></i> ' . __d('admin', 'Edit_delivery_rhythm_for_selected_products') . '</a>';
}

?>