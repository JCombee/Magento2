<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Model\Order\Shipment\Track;

use Magento\Sales\Model\Order\Shipment\Track;

/**
 * Class Validator
 * @since 2.0.0
 */
class Validator
{
    /**
     * Required field
     *
     * @var array
     * @since 2.0.0
     */
    protected $required = [
        'parent_id' => 'Parent Track Id',
        'order_id' => 'Order Id',
        'track_number' => 'Number',
        'carrier_code' => 'Carrier Code',
    ];

    /**
     * Validate data
     *
     * @param \Magento\Sales\Model\Order\Shipment\Track $track
     * @return array
     * @since 2.0.0
     */
    public function validate(Track $track)
    {
        $errors = [];
        $commentData = $track->getData();
        foreach ($this->required as $code => $label) {
            if (!$track->hasData($code)) {
                $errors[$code] = sprintf('%s is a required field', $label);
            } elseif (empty($commentData[$code])) {
                $errors[$code] = sprintf('%s can not be empty', $label);
            }
        }

        return $errors;
    }
}
