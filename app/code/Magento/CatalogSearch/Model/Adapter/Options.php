<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\CatalogSearch\Model\Adapter;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Search\Adapter\OptionsInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * @api
 * @since 2.0.0
 */
class Options implements OptionsInterface
{
    const XML_PATH_INTERVAL_DIVISION_LIMIT = 'catalog/layered_navigation/interval_division_limit';
    const XML_PATH_RANGE_STEP = 'catalog/layered_navigation/price_range_step';
    const XML_PATH_RANGE_MAX_INTERVALS = 'catalog/layered_navigation/price_range_max_intervals';

    /**
     * @var ScopeConfigInterface
     * @since 2.0.0
     */
    private $scopeConfig;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @since 2.0.0
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     * @since 2.0.0
     */
    public function get()
    {
        return [
            'interval_division_limit' => (int)$this->scopeConfig->getValue(
                self::XML_PATH_INTERVAL_DIVISION_LIMIT,
                ScopeInterface::SCOPE_STORE
            ),
            'range_step' => (double)$this->scopeConfig->getValue(
                self::XML_PATH_RANGE_STEP,
                ScopeInterface::SCOPE_STORE
            ),
            'min_range_power' => 10,
            'max_intervals_number' => (int)$this->scopeConfig->getValue(
                self::XML_PATH_RANGE_MAX_INTERVALS,
                ScopeInterface::SCOPE_STORE
            )
        ];
    }
}
