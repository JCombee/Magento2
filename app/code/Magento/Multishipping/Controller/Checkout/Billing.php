<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Multishipping\Controller\Checkout;

use Magento\Multishipping\Model\Checkout\Type\Multishipping\State;
use Magento\Framework\App\ResponseInterface;

/**
 * Class \Magento\Multishipping\Controller\Checkout\Billing
 *
 * @since 2.0.0
 */
class Billing extends \Magento\Multishipping\Controller\Checkout
{
    /**
     * Validation of selecting of billing address
     *
     * @return boolean
     * @since 2.0.0
     */
    protected function _validateBilling()
    {
        if (!$this->_getCheckout()->getQuote()->getBillingAddress()->getFirstname()) {
            $this->_redirect('*/checkout_address/selectBilling');
            return false;
        }
        return true;
    }

    /**
     * Multishipping checkout billing information page
     *
     * @return void|ResponseInterface
     * @since 2.0.0
     */
    public function execute()
    {
        if (!$this->_validateBilling()) {
            return;
        }

        if (!$this->_validateMinimumAmount()) {
            return;
        }

        if (!$this->_getState()->getCompleteStep(State::STEP_SHIPPING)) {
            return $this->_redirect('*/*/shipping');
        }

        $this->_getState()->setActiveStep(State::STEP_BILLING);
        $this->_view->loadLayout();
        $this->_view->renderLayout();
    }
}
