<?php

namespace App\Test\TestCase\Traits;

/**
 * FoodCoopShop - The open source software for your foodcoop
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @since         FoodCoopShop 3.2.0
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 * @author        Swoichha Adhikari
 * @copyright     Copyright (c) Mario Rothauer, https://www.rothauer-it.com
 * @link          https://www.foodcoopshop.com
 */
trait PrepareAndTestInvoiceDataTrait
{

    public function generateInvoice($customerId, $paidInCash)
    {
        $this->get('/admin/invoices/generate.pdf?customerId='.$customerId.'&paidInCash='.$paidInCash.'&currentDay=2018-02-02');
    }

    public function prepareOrdersAndPaymentsForInvoice($customerId)
    {

        $pickupDay = '2018-02-02';

        // add product with price pre unit
        $productIdA = 347; // forelle
        $productIdB = '348-11'; // rindfleisch + attribute
        $this->addProductToCart($productIdA, 1);
        $this->addProductToCart($productIdB, 3);
        $this->finishCart(1, 1, '', null, $pickupDay);

        $this->OrderDetail = $this->getTableLocator()->get('OrderDetails');
        $query = 'UPDATE ' . $this->OrderDetail->getTable().' SET pickup_day = :pickupDay WHERE id_order_detail IN(4,5);';
        $params = [
            'pickupDay' => $pickupDay,
        ];
        $statement = $this->dbConnection->prepare($query);
        $statement->execute($params);

        $this->addPayment($customerId, 2.0, 'deposit', 0, '', $pickupDay);
        $this->addPayment($customerId, 3.2, 'deposit', 0, '', $pickupDay);

    }

    public function doAssertInvoiceTaxes($data, $taxRate, $excl, $tax, $incl)
    {
        $this->assertEquals($data->tax_rate, $taxRate);
        $this->assertEquals($data->total_price_tax_excl, $excl);
        $this->assertEquals($data->total_price_tax, $tax);
        $this->assertEquals($data->total_price_tax_incl, $incl);
    }

    public function getAndAssertOrderDetailsAfterCancellation($orderDetailIds)
    {

        $this->OrderDetail = $this->getTableLocator()->get('OrderDetails');
        $orderDetails = $this->OrderDetail->find('all', [
            'conditions' => [
                'OrderDetails.id_order_detail IN' => $orderDetailIds,
            ],
        ])->toArray();
        $this->assertEquals(5, count($orderDetails));

        foreach($orderDetails as $orderDetail) {
            $this->assertNull($orderDetail->id_invoice);
            $this->assertEquals($orderDetail->order_state, ORDER_STATE_ORDER_PLACED);
            $this->assertTrue($orderDetail->total_price_tax_excl >= 0);
            $this->assertTrue($orderDetail->total_price_tax_incl >= 0);
            $this->assertTrue($orderDetail->tax_unit_amount >= 0);
            $this->assertTrue($orderDetail->tax_total_amount >= 0);
        }

    }

    public function getAndAssertPaymentsAfterCancellation($paymentIds)
    {

        $this->Payment = $this->getTableLocator()->get('Payments');
        $payments = $this->Payment->find('all', [
            'conditions' => [
                'Payments.id IN' => $paymentIds,
            ],
        ])->toArray();
        $this->assertEquals(2, count($payments));

        foreach($payments as $payment) {
            $this->assertNull($payment->invoice_id);
            $this->assertTrue($payment->amount >= 0);
        }

    }

    public function getAndAssertOrderDetailsAfterInvoiceGeneration($invoiceId, $expectedCount)
    {
        $this->OrderDetail = $this->getTableLocator()->get('OrderDetails');
        $orderDetails = $this->OrderDetail->find('all', [
            'conditions' => [
                'OrderDetails.id_invoice' => $invoiceId,
            ],
        ])->toArray();

        $this->assertEquals($expectedCount, count($orderDetails));
        foreach($orderDetails as $orderDetail) {
            $this->assertEquals($orderDetail->order_state, ORDER_STATE_BILLED_CASHLESS);
        }
    }

    public function getAndAssertPaymentsAfterInvoiceGeneration($customerId)
    {
        $this->Payment = $this->getTableLocator()->get('Payments');
        $payments = $this->Payment->getCustomerDepositNotBilled($customerId);

        foreach($payments as $payment) {
            $this->assertEquals($payment->id_invoice, 1);
        }
    }

}
