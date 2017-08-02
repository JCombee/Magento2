<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Analytics\Model;

use Magento\Analytics\Api\Data\LinkInterfaceFactory;
use Magento\Analytics\Api\LinkProviderInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Provides link to file with collected report data.
 * @since 2.2.0
 */
class LinkProvider implements LinkProviderInterface
{
    /**
     * @var LinkInterfaceFactory
     * @since 2.2.0
     */
    private $linkFactory;

    /**
     * @var FileInfoManager
     * @since 2.2.0
     */
    private $fileInfoManager;

    /**
     * @var StoreManagerInterface
     * @since 2.2.0
     */
    private $storeManager;

    /**
     * @param LinkInterfaceFactory $linkInterfaceFactory
     * @param FileInfoManager $fileInfoManager
     * @param StoreManagerInterface $storeManager
     * @since 2.2.0
     */
    public function __construct(
        LinkInterfaceFactory $linkFactory,
        FileInfoManager $fileInfoManager,
        StoreManagerInterface $storeManager
    ) {
        $this->linkFactory = $linkFactory;
        $this->fileInfoManager = $fileInfoManager;
        $this->storeManager = $storeManager;
    }

    /**
     * Returns base url to file according to store configuration
     *
     * @param FileInfo $fileInfo
     * @return string
     * @since 2.2.0
     */
    private function getBaseUrl(FileInfo $fileInfo)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA) . $fileInfo->getPath();
    }

    /**
     * Verify is requested file ready
     *
     * @param FileInfo $fileInfo
     * @return bool
     * @since 2.2.0
     */
    private function isFileReady(FileInfo $fileInfo)
    {
        return $fileInfo->getPath() && $fileInfo->getInitializationVector();
    }

    /**
     * @inheritdoc
     * @since 2.2.0
     */
    public function get()
    {
        $fileInfo = $this->fileInfoManager->load();
        if (!$this->isFileReady($fileInfo)) {
            throw new NoSuchEntityException(__('File is not ready yet.'));
        }
        return $this->linkFactory->create(
            [
                'url' => $this->getBaseUrl($fileInfo),
                'initializationVector' => base64_encode($fileInfo->getInitializationVector())
            ]
        );
    }
}
