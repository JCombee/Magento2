<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Form Input/Output Strip HTML tags Filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Framework\Data\Form\Filter;

/**
 * Class \Magento\Framework\Data\Form\Filter\Striptags
 *
 * @since 2.0.0
 */
class Striptags implements \Magento\Framework\Data\Form\Filter\FilterInterface
{
    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     * @since 2.0.0
     */
    public function inputFilter($value)
    {
        return strip_tags($value);
    }

    /**
     * Returns the result of filtering $value
     *
     * @param string $value
     * @return string
     * @since 2.0.0
     */
    public function outputFilter($value)
    {
        return $value;
    }
}
