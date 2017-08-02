<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sales\Block\Adminhtml\Order\Create\Sidebar;

use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Adminhtml sales order create sidebar block
 *
 * @api
 * @author      Magento Core Team <core@magentocommerce.com>
 * @since 2.0.0
 */
class AbstractSidebar extends \Magento\Sales\Block\Adminhtml\Order\Create\AbstractCreate
{
    /**
     * Default Storage action on selected item
     *
     * @var string
     * @since 2.0.0
     */
    protected $_sidebarStorageAction = 'add';

    /**
     * Sales config
     *
     * @var \Magento\Sales\Model\Config
     * @since 2.0.0
     */
    protected $_salesConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Backend\Model\Session\Quote $sessionQuote
     * @param \Magento\Sales\Model\AdminOrder\Create $orderCreate
     * @param PriceCurrencyInterface $priceCurrency
     * @param \Magento\Sales\Model\Config $salesConfig
     * @param array $data
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Model\Session\Quote $sessionQuote,
        \Magento\Sales\Model\AdminOrder\Create $orderCreate,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Sales\Model\Config $salesConfig,
        array $data = []
    ) {
        parent::__construct($context, $sessionQuote, $orderCreate, $priceCurrency, $data);
        $this->_salesConfig = $salesConfig;
    }

    /**
     * Return name of sidebar storage action
     *
     * @return string
     * @since 2.0.0
     */
    public function getSidebarStorageAction()
    {
        return $this->_sidebarStorageAction;
    }

    /**
     * Retrieve display block availability
     *
     * @return bool
     * @since 2.0.0
     */
    public function canDisplay()
    {
        return $this->getCustomerId();
    }

    /**
     * Retrieve disply item qty availablity
     *
     * @return false
     * @since 2.0.0
     */
    public function canDisplayItemQty()
    {
        return false;
    }

    /**
     * Retrieve availability removing items in block
     *
     * @return true
     * @since 2.0.0
     */
    public function canRemoveItems()
    {
        return true;
    }

    /**
     * Retrieve identifier of block item
     *
     * @param \Magento\Framework\DataObject $item
     * @return int
     * @since 2.0.0
     */
    public function getIdentifierId($item)
    {
        return $item->getProductId();
    }

    /**
     * Retrieve item identifier of block item
     *
     * @param \Magento\Framework\DataObject $item
     * @return int
     * @since 2.0.0
     */
    public function getItemId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve product identifier linked with item
     *
     * @param \Magento\Framework\DataObject $item
     * @return int
     * @since 2.0.0
     */
    public function getProductId($item)
    {
        return $item->getId();
    }

    /**
     * Retrieve item count
     *
     * @return int
     * @since 2.0.0
     */
    public function getItemCount()
    {
        $count = $this->getData('item_count');
        if ($count === null) {
            $count = count($this->getItems());
            $this->setData('item_count', $count);
        }
        return $count;
    }

    /**
     * Retrieve all items
     *
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @since 2.0.0
     */
    public function getItems()
    {
        $items = [];
        $collection = $this->getItemCollection();
        if ($collection) {
            $productTypes = $this->_salesConfig->getAvailableProductTypes();
            if (is_array($collection)) {
                $items = $collection;
            } else {
                $items = $collection->getItems();
            }

            /*
             * Filtering items by allowed product type
             */
            foreach ($items as $key => $item) {
                if ($item instanceof \Magento\Catalog\Model\Product) {
                    $type = $item->getTypeId();
                } elseif ($item instanceof \Magento\Sales\Model\Order\Item) {
                    $type = $item->getProductType();
                } elseif ($item instanceof \Magento\Quote\Model\Quote\Item) {
                    $type = $item->getProductType();
                } else {
                    $type = '';
                    // Maybe some item, that can give us product via getProduct()
                    if ($item instanceof \Magento\Framework\DataObject || method_exists($item, 'getProduct')) {
                        $product = $item->getProduct();
                        if ($product && $product instanceof \Magento\Catalog\Model\Product) {
                            $type = $product->getTypeId();
                        }
                    }
                }
                if (!in_array($type, $productTypes)) {
                    unset($items[$key]);
                }
            }
        }

        return $items;
    }

    /**
     * Retrieve item collection
     *
     * @return false
     * @since 2.0.0
     */
    public function getItemCollection()
    {
        return false;
    }

    /**
     * Retrieve disply price availablity
     *
     * @return true
     * @since 2.0.0
     */
    public function canDisplayPrice()
    {
        return true;
    }

    /**
     * Get item qty
     *
     * @param \Magento\Framework\DataObject $item
     * @return int
     * @since 2.0.0
     */
    public function getItemQty(\Magento\Framework\DataObject $item)
    {
        return $item->getQty() * 1 ? $item->getQty() * 1 : 1;
    }

    /**
     * Check whether product configuration is required before adding to order
     *
     * @param string|int|null $productType
     * @return false
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @since 2.0.0
     */
    public function isConfigurationRequired($productType)
    {
        return false;
    }
}
