<?php

namespace App\Model\Table;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 1.0.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, http://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
class AttributesTable extends AppTable
{

    public function initialize(array $config)
    {
        $this->setTable('attribute');
        parent::initialize($config);
    }
    
    public $primaryKey = 'id_attribute';

    public $validate = [
        'name' => [
            'notBlank' => [
                'rule' => [
                    'notBlank'
                ],
                'message' => 'Bitte gib einen Namen an.'
            ],
            'unique' => [
                'rule' => 'isUnique',
                'message' => 'Eine Variante mit dem Namen existiert bereits.'
            ]
        ]
    ];

    public function getForDropdown()
    {
        $this->recursive = 2;
        $attributes = $this->find('all', [
            'order' => [
                'Attributes.name' => 'ASC'
            ]
        ]);

        $attributesForDropdown = [];
        foreach ($attributes as $attribute) {
            $attributesForDropdown[$attribute['Attributes']['id_attribute']] = $attribute['Attributes']['name'];
        }

        return $attributesForDropdown;
    }
}
