<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Braintree\Gateway\Http\Client;

use Magento\Braintree\Gateway\Request\PaymentDataBuilder;

/**
 * Class \Magento\Braintree\Gateway\Http\Client\TransactionRefund
 *
 * @since 2.1.0
 */
class TransactionRefund extends AbstractTransaction
{
    /**
     * Process http request
     * @param array $data
     * @return \Braintree\Result\Error|\Braintree\Result\Successful
     * @since 2.1.0
     */
    protected function process(array $data)
    {
        return $this->adapter->refund(
            $data['transaction_id'],
            $data[PaymentDataBuilder::AMOUNT]
        );
    }
}
