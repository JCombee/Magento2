<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Directory\Model\Country;

/**
 * Directory country format model
 *
 * @method \Magento\Directory\Model\ResourceModel\Country\Format _getResource()
 * @method \Magento\Directory\Model\ResourceModel\Country\Format getResource()
 * @method string getCountryId()
 * @method \Magento\Directory\Model\Country\Format setCountryId(string $value)
 * @method string getType()
 * @method \Magento\Directory\Model\Country\Format setType(string $value)
 * @method string getFormat()
 * @method \Magento\Directory\Model\Country\Format setFormat(string $value)
 *
 * @api
 * @since 2.0.0
 */
class Format extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @return void
     * @since 2.0.0
     */
    protected function _construct()
    {
        $this->_init(\Magento\Directory\Model\ResourceModel\Country\Format::class);
    }
}
