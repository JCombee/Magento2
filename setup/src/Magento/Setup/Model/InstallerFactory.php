<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Setup\Model;

use Laminas\ServiceManager\ServiceLocatorInterface;
use Magento\Framework\App\ErrorHandler;
use Magento\Framework\Setup\ConsoleLoggerInterface;
use Magento\Setup\Module\ResourceFactory;

/**
 * Factory for \Magento\Setup\Model\Installer
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InstallerFactory
{
    /**
     * Laminas Framework's service locator
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     * @var ResourceFactory
     */
    private $resourceFactory;

    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @param ResourceFactory $resourceFactory
     */
    public function __construct(
        ServiceLocatorInterface $serviceLocator,
        ResourceFactory $resourceFactory
    ) {
        $this->serviceLocator = $serviceLocator;
        $this->resourceFactory = $resourceFactory;
        // For Setup Wizard we are using our customized error handler
        $handler = new ErrorHandler();
        set_error_handler([$handler, 'handler']);
    }

    /**
     * Factory method for installer object
     *
     * @param ConsoleLoggerInterface $log
     * @return Installer
     * @throws \Magento\Setup\Exception
     */
    public function create(ConsoleLoggerInterface $log)
    {
        return new Installer(
            $this->serviceLocator->get(\Magento\Framework\Setup\FilePermissions::class),
            $this->serviceLocator->get(\Magento\Framework\App\DeploymentConfig\Writer::class),
            $this->serviceLocator->get(\Magento\Framework\App\DeploymentConfig\Reader::class),
            $this->serviceLocator->get(\Magento\Framework\App\DeploymentConfig::class),
            $this->serviceLocator->get(\Magento\Framework\Module\ModuleList::class),
            $this->serviceLocator->get(\Magento\Framework\Module\ModuleList\Loader::class),
            $this->serviceLocator->get(\Magento\Setup\Model\AdminAccountFactory::class),
            $log,
            $this->serviceLocator->get(\Magento\Setup\Module\ConnectionFactory::class),
            $this->serviceLocator->get(\Magento\Framework\App\MaintenanceMode::class),
            $this->serviceLocator->get(\Magento\Framework\Filesystem::class),
            $this->serviceLocator->get(\Magento\Setup\Model\ObjectManagerProvider::class),
            new \Magento\Framework\Model\ResourceModel\Db\Context(
                $this->getResource(),
                $this->serviceLocator->get(\Magento\Framework\Model\ResourceModel\Db\TransactionManager::class),
                $this->serviceLocator->get(\Magento\Framework\Model\ResourceModel\Db\ObjectRelationProcessor::class)
            ),
            $this->serviceLocator->get(\Magento\Setup\Model\ConfigModel::class),
            $this->serviceLocator->get(\Magento\Framework\App\State\CleanupFiles::class),
            $this->serviceLocator->get(\Magento\Setup\Validator\DbValidator::class),
            $this->serviceLocator->get(\Magento\Setup\Module\SetupFactory::class),
            $this->serviceLocator->get(\Magento\Setup\Module\DataSetupFactory::class),
            $this->serviceLocator->get(\Magento\Framework\Setup\SampleData\State::class),
            new \Magento\Framework\Component\ComponentRegistrar(),
            $this->serviceLocator->get(\Magento\Setup\Model\PhpReadinessCheck::class)
        );
    }

    /**
     * Create Resource Factory
     *
     * @return Resource
     */
    private function getResource()
    {
        $deploymentConfig = $this->serviceLocator->get(\Magento\Framework\App\DeploymentConfig::class);
        return $this->resourceFactory->create($deploymentConfig);
    }
}
