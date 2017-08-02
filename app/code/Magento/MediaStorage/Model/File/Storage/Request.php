<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\MediaStorage\Model\File\Storage;

use Magento\Framework\HTTP\PhpEnvironment\Request as HttpRequest;

/**
 * Class \Magento\MediaStorage\Model\File\Storage\Request
 *
 * @since 2.0.0
 */
class Request
{
    /**
     * Path info
     *
     * @var string
     * @since 2.0.0
     */
    private $pathInfo;

    /**
     * @param HttpRequest $request
     * @since 2.0.0
     */
    public function __construct(HttpRequest $request)
    {
        $this->pathInfo = str_replace('..', '', ltrim($request->getPathInfo(), '/'));
    }

    /**
     * Retrieve path info
     *
     * @return string
     * @since 2.0.0
     */
    public function getPathInfo()
    {
        return $this->pathInfo;
    }
}
