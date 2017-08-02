<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Tax\Controller\Adminhtml\Tax;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class \Magento\Tax\Controller\Adminhtml\Tax\IgnoreTaxNotification
 *
 * @since 2.0.0
 */
class IgnoreTaxNotification extends \Magento\Tax\Controller\Adminhtml\Tax
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     * @since 2.0.0
     */
    protected $_cacheTypeList;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassService
     * @param \Magento\Tax\Api\Data\TaxClassInterfaceFactory $taxClassDataObjectFactory
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Tax\Api\TaxClassRepositoryInterface $taxClassService,
        \Magento\Tax\Api\Data\TaxClassInterfaceFactory $taxClassDataObjectFactory,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
    ) {
        $this->_cacheTypeList = $cacheTypeList;
        parent::__construct($context, $taxClassService, $taxClassDataObjectFactory);
    }

    /**
     * Set tax ignore notification flag and redirect back
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @since 2.0.0
     */
    public function execute()
    {
        $section = $this->getRequest()->getParam('section');
        if ($section) {
            try {
                $path = 'tax/notification/ignore_' . $section;
                $this->_objectManager->get(\Magento\Config\Model\ResourceModel\Config::class)
                    ->saveConfig($path, 1, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, 0);
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());
            }
        }

        // clear the block html cache
        $this->_cacheTypeList->cleanType('block_html');
        $this->_eventManager->dispatch('adminhtml_cache_refresh_type', ['type' => 'block_html']);

        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setRefererUrl();
    }
}
