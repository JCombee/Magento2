<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * Generate options for media database selection
 */
namespace Magento\MediaStorage\Model\Config\Source\Storage\Media;

use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\Config\ConfigOptionsListConstants;

/**
 * Class \Magento\MediaStorage\Model\Config\Source\Storage\Media\Database
 *
 * @since 2.0.0
 */
class Database implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var DeploymentConfig
     * @since 2.0.0
     */
    protected $deploymentConfig;

    /**
     * @param DeploymentConfig $deploymentConfig
     * @since 2.0.0
     */
    public function __construct(DeploymentConfig $deploymentConfig)
    {
        $this->deploymentConfig = $deploymentConfig;
    }

    /**
     * Returns list of available resources
     *
     * @return array
     * @since 2.0.0
     */
    public function toOptionArray()
    {
        $resourceOptions = [];
        $resourceConfig = $this->deploymentConfig->get(ConfigOptionsListConstants::KEY_RESOURCE);
        if (null !== $resourceConfig) {
            foreach (array_keys($resourceConfig) as $resourceName) {
                $resourceOptions[] = ['value' => $resourceName, 'label' => $resourceName];
            }
            sort($resourceOptions);
            reset($resourceOptions);
        }
        return $resourceOptions;
    }
}
