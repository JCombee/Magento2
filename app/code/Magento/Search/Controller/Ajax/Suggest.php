<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Search\Controller\Ajax;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Search\Model\AutocompleteInterface;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class \Magento\Search\Controller\Ajax\Suggest
 *
 * @since 2.0.0
 */
class Suggest extends Action
{
    /**
     * @var  \Magento\Search\Model\AutocompleteInterface
     * @since 2.0.0
     */
    private $autocomplete;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Search\Model\AutocompleteInterface $autocomplete
     * @since 2.0.0
     */
    public function __construct(
        Context $context,
        AutocompleteInterface $autocomplete
    ) {
        $this->autocomplete = $autocomplete;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     * @since 2.0.0
     */
    public function execute()
    {
        if (!$this->getRequest()->getParam('q', false)) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setUrl($this->_url->getBaseUrl());
            return $resultRedirect;
        }

        $autocompleteData = $this->autocomplete->getItems();
        $responseData = [];
        foreach ($autocompleteData as $resultItem) {
            $responseData[] = $resultItem->toArray();
        }
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
        $resultJson->setData($responseData);
        return $resultJson;
    }
}
