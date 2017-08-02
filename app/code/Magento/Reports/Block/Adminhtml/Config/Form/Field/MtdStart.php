<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Reports\Block\Adminhtml\Config\Form\Field;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Dashboard Month-To-Date Day starts Field Renderer
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 * @since 2.0.0
 */
class MtdStart extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @param AbstractElement $element
     * @return string
     * @since 2.0.0
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        $_days = [];
        for ($i = 1; $i <= 31; $i++) {
            $_days[$i] = $i < 10 ? '0' . $i : $i;
        }

        $_daysHtml = $element->setStyle('width:50px;')->setValues($_days)->getElementHtml();

        return $_daysHtml;
    }
}
