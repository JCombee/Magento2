<?php
/**
 * Magento validator config factory
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Theme\Model\PageLayout\Config;

/**
 * Page layout config builder
 * @since 2.0.0
 */
class Builder implements \Magento\Framework\View\Model\PageLayout\Config\BuilderInterface
{
    /**
     * @var \Magento\Framework\View\PageLayout\ConfigFactory
     * @since 2.0.0
     */
    protected $configFactory;

    /**
     * @var \Magento\Framework\View\PageLayout\File\Collector\Aggregated
     * @since 2.0.0
     */
    protected $fileCollector;

    /**
     * @var \Magento\Theme\Model\ResourceModel\Theme\Collection
     * @since 2.0.0
     */
    protected $themeCollection;

    /**
     * @param \Magento\Framework\View\PageLayout\ConfigFactory $configFactory
     * @param \Magento\Framework\View\PageLayout\File\Collector\Aggregated $fileCollector
     * @param \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Framework\View\PageLayout\ConfigFactory $configFactory,
        \Magento\Framework\View\PageLayout\File\Collector\Aggregated $fileCollector,
        \Magento\Theme\Model\ResourceModel\Theme\Collection $themeCollection
    ) {
        $this->configFactory = $configFactory;
        $this->fileCollector = $fileCollector;
        $this->themeCollection = $themeCollection;
        $this->themeCollection->setItemObjectClass(\Magento\Theme\Model\Theme\Data::class);
    }

    /**
     * @return \Magento\Framework\View\PageLayout\Config
     * @since 2.0.0
     */
    public function getPageLayoutsConfig()
    {
        return $this->configFactory->create(['configFiles' => $this->getConfigFiles()]);
    }

    /**
     * @return array
     * @since 2.0.0
     */
    protected function getConfigFiles()
    {
        $configFiles = [];
        foreach ($this->themeCollection->loadRegisteredThemes() as $theme) {
            $configFiles = array_merge($configFiles, $this->fileCollector->getFilesContent($theme, 'layouts.xml'));
        }

        return $configFiles;
    }
}
