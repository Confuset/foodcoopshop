<?php
/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 2.5.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 * @author        Mario Rothauer <office@foodcoopshop.com>
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
use App\Test\TestCase\AppCakeTestCase;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;

class SelfServiceControllerTest extends AppCakeTestCase
{
    
    public function testPageSelfService()
    {
        $this->loginAsSuperadmin();
        $this->changeConfiguration('FCS_SELF_SERVICE_MODE_FOR_STOCK_PRODUCTS_ENABLED', 1);
        $testUrls = [
            $this->Slug->getSelfService()
        ];
        $this->assertPagesForErrors($testUrls);
    }
    
    public function testBarCodeLoginAsSuperadminIfNotEnabled()
    {
        $this->doBarCodeLogin();
        $this->assertRegExpWithUnquotedString(__('Signing_in_failed_account_inactive_or_password_wrong?'), $this->httpClient->getContent());
    }
    
    public function testBarCodeLoginAsSuperadminValid()
    {
        $this->changeConfiguration('FCS_SELF_SERVICE_MODE_FOR_STOCK_PRODUCTS_ENABLED', 1);
        $this->doBarCodeLogin();
        $this->assertNotRegExpWithUnquotedString(__('Signing_in_failed_account_inactive_or_password_wrong?'), $this->httpClient->getContent());
    }
    
    public function testSelfServiceOrderWithoutCheckboxes() {
        $this->changeConfiguration('FCS_SELF_SERVICE_MODE_FOR_STOCK_PRODUCTS_ENABLED', 1);
        $this->doBarCodeLogin();
        $this->addProductToSelfServiceCart(349, 1);
        $this->finishSelfServiceCart(0, 0);
        $this->assertRegExpWithUnquotedString('Bitte akzeptiere die AGB.', $this->httpClient->getContent());
        $this->assertRegExpWithUnquotedString('Bitte akzeptiere die Information über das Rücktrittsrecht und dessen Ausschluss.', $this->httpClient->getContent());
    }
    
    public function testSelfServiceOrder()
    {
        $this->changeConfiguration('FCS_SELF_SERVICE_MODE_FOR_STOCK_PRODUCTS_ENABLED', 1);
        $this->doBarCodeLogin();
        $this->addProductToSelfServiceCart(349, 1);
        $this->finishSelfServiceCart(1, 1);
        
        $this->Cart = TableRegistry::getTableLocator()->get('Carts');
        $cart = $this->Cart->find('all', [
            'order' => [
                'Carts.id_cart' => 'DESC'
            ],
        ])->first();
        
        $cart = $this->getCartById($cart->id_cart);

        $this->assertEquals(1, count($cart->cart_products));
        
        foreach($cart->cart_products as $cartProduct) {
            $orderDetail = $cartProduct->order_detail;
            $this->assertEquals($orderDetail->pickup_day->i18nFormat(Configure::read('app.timeHelper')->getI18Format('Database')), Configure::read('app.timeHelper')->getCurrentDateForDatabase());
        }
        
        $this->EmailLog = TableRegistry::getTableLocator()->get('EmailLogs');
        $emailLogs = $this->EmailLog->find('all')->toArray();
        $this->assertEquals(1, count($emailLogs));
        
        $this->assertEmailLogs(
            $emailLogs[0],
            'Dein Einkauf',
            [
                'Lagerprodukt'
            ],
            [
                Configure::read('test.loginEmailSuperadmin')
            ]
        );
    }
    
    private function addProductToSelfServiceCart($productId, $amount)
    {
        $this->httpClient->ajaxPost(
            '/warenkorb/ajaxAdd/',
            [
                'productId' => $productId,
                'amount' => $amount
            ],
            [
                'headers' => [
                    'X-Requested-With:XMLHttpRequest',
                    // referer needed to get correct cart (self-service)
                    'REFERER' => Configure::read('app.cakeServerName') . '/' . __('route_self_service')
                ],
                'type' => 'json'
            ]
        );
        return $this->httpClient->getJsonDecodedContent();
        
    }
    
    private function finishSelfServiceCart($general_terms_and_conditions_accepted, $cancellation_terms_accepted)
    {
        $data = [
            'Carts' => [
                'general_terms_and_conditions_accepted' => $general_terms_and_conditions_accepted,
                'cancellation_terms_accepted' => $cancellation_terms_accepted
            ],
        ];
        $this->httpClient->post(
            $this->Slug->getSelfService(),
            $data,
            [
                'headers' => [
                    'REFERER' => Configure::read('app.cakeServerName') . '/' . __('route_self_service')
                ]
            ]
        );
    }
    
    private function doBarCodeLogin()
    {
        $this->httpClient->loginEmail = Configure::read('test.loginEmailSuperadmin');
        $this->httpClient->followOneRedirectForNextRequest();
        $this->httpClient->post($this->Slug->getLogin(), [
            'barCode' => Configure::read('test.superadminBarCode')
        ]);
    }
    
}
