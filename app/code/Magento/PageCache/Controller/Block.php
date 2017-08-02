<?php
/**
 * PageCache controller
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\PageCache\Controller;

use Magento\Framework\Serialize\Serializer\Base64Json;
use Magento\Framework\Serialize\Serializer\Json;

/**
 * Class \Magento\PageCache\Controller\Block
 *
 * @since 2.0.0
 */
abstract class Block extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Translate\InlineInterface
     * @since 2.0.0
     */
    protected $translateInline;

    /**
     * @var Json
     * @since 2.2.0
     */
    private $jsonSerializer;

    /**
     * @var Base64Json
     * @since 2.2.0
     */
    private $base64jsonSerializer;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param Json $jsonSerializer
     * @param Base64Json $base64jsonSerializer
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        Json $jsonSerializer = null,
        Base64Json $base64jsonSerializer = null
    ) {
        parent::__construct($context);
        $this->translateInline = $translateInline;
        $this->jsonSerializer = $jsonSerializer
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
        $this->base64jsonSerializer = $base64jsonSerializer
            ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Base64Json::class);
    }

    /**
     * Get blocks from layout by handles
     *
     * @return array [\Element\BlockInterface]
     * @since 2.0.0
     */
    protected function _getBlocks()
    {
        $blocks = $this->getRequest()->getParam('blocks', '');
        $handles = $this->getRequest()->getParam('handles', '');

        if (!$handles || !$blocks) {
            return [];
        }
        $blocks = $this->jsonSerializer->unserialize($blocks);
        $handles = $this->base64jsonSerializer->unserialize($handles);

        $this->_view->loadLayout($handles, true, true, false);
        $data = [];

        $layout = $this->_view->getLayout();
        foreach ($blocks as $blockName) {
            $blockInstance = $layout->getBlock($blockName);
            if (is_object($blockInstance)) {
                $data[$blockName] = $blockInstance;
            }
        }

        return $data;
    }
}
