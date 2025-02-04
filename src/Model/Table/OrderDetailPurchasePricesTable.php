<?php

namespace App\Model\Table;

use Cake\Validation\Validator;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.3.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class OrderDetailPurchasePricesTable extends AppTable
{

    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->hasOne('OrderDetails', [
            'foreignKey' => 'id_order_detail'
        ]);
        $this->setPrimaryKey('id_order_detail');
    }

    public function validationEdit(Validator $validator): Validator
    {
        $validator->notEmptyString('total_price_tax_excl', __('Please_enter_a_number.'));
        $validator->numeric('total_price_tax_excl', __('Please_enter_a_correct_number.'));
        $validator->greaterThanOrEqual('total_price_tax_excl', 0.01, __('The_amount_(money)_needs_to_be_greater_than_0.'));
        return $validator;
    }

}
