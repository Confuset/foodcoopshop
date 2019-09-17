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
use Cake\I18n\I18n;

if (!Configure::read('app.generalTermsAndConditionsEnabled')) {
    return false;
}

echo '<div id="general-terms-and-conditions" class="featherlight-overlay">';
    echo $this->element('legal/'.I18n::getLocale().'/generalTermsAndConditions');
echo '</div>';

$initialGeneralTermsAndConditionLink = '<a href="#general-terms-and-conditions" class="open-with-featherlight">'.__('general_terms_and_conditions').'</a>';
$generalTermsAndConditionsLinks = [$initialGeneralTermsAndConditionLink];
$customGeneralTermsAndConditionsLinks = [];

$uniqueManufacturers = $appAuth->Cart->getUniqueManufacturers();
foreach($uniqueManufacturers as $manufacturerId => $manufacturer) {
    $src = $this->MyHtml->getManufacturerTermsOfUseSrc($manufacturerId);
    if ($src !== false) {
        $customGeneralTermsAndConditionsLinks[] = '<a target="_blank" href="'.$src.'">' . __('General_terms_and_conditions_of_{0}', [$manufacturer['name']]).'</a>';
    }
}
if (count($customGeneralTermsAndConditionsLinks) == count($uniqueManufacturers) && count($customGeneralTermsAndConditionsLinks) > 0) {
    array_shift($generalTermsAndConditionsLinks);
}
$generalTermsAndConditionsLinks = array_merge($generalTermsAndConditionsLinks, $customGeneralTermsAndConditionsLinks);
$label = __('I_accept_the_{0}', [join(', ', $generalTermsAndConditionsLinks)]);

echo $this->Form->control('Carts.general_terms_and_conditions_accepted', [
    'label' => $label,
    'type' => 'checkbox',
    'escape' => false
]);

?>