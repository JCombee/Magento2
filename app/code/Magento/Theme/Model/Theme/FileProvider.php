<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\Theme;

/**
 * Class \Magento\Theme\Model\Theme\FileProvider
 *
 * @since 2.0.0
 */
class FileProvider implements \Magento\Framework\View\Design\Theme\FileProviderInterface
{
    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\File\CollectionFactory
     * @since 2.0.0
     */
    protected $fileFactory;

    /**
     * @param \Magento\Theme\Model\ResourceModel\Theme\File\CollectionFactory $fileFactory
     * @since 2.0.0
     */
    public function __construct(\Magento\Theme\Model\ResourceModel\Theme\File\CollectionFactory $fileFactory)
    {
        $this->fileFactory = $fileFactory;
    }

    /**
     * {@inheritdoc}
     * @since 2.0.0
     */
    public function getItems(\Magento\Framework\View\Design\ThemeInterface $theme, array $filters = [])
    {
        /** @var \Magento\Framework\View\Design\Theme\File\CollectionInterface $themeFiles */
        $themeFiles = $this->fileFactory->create();
        $themeFiles->addThemeFilter($theme);
        foreach ($filters as $field => $value) {
            $themeFiles->addFieldToFilter($field, $value);
        }
        $themeFiles->setDefaultOrder();
        return $themeFiles->getItems();
    }
}
