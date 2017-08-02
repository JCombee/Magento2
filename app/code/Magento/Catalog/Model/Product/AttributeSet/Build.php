<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Catalog\Model\Product\AttributeSet;

use \Magento\Framework\Exception\AlreadyExistsException;

/**
 * Class \Magento\Catalog\Model\Product\AttributeSet\Build
 *
 * @since 2.0.0
 */
class Build
{
    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     * @since 2.0.0
     */
    protected $attributeSetFactory;

    /**
     * @var string
     * @since 2.0.0
     */
    protected $name;

    /**
     * @var int
     * @since 2.0.0
     */
    protected $entityTypeId;

    /**
     * @var int
     * @since 2.0.0
     */
    protected $skeletonId;

    /**
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @since 2.0.0
     */
    public function __construct(
        \Magento\Eav\Model\Entity\Attribute\SetFactory  $attributeSetFactory
    ) {
        $this->attributeSetFactory = $attributeSetFactory;
    }

    /**
     * @param int $entityTypeId
     * @return $this
     * @since 2.0.0
     */
    public function setEntityTypeId($entityTypeId)
    {
        $this->entityTypeId = (int)$entityTypeId;
        return $this;
    }

    /**
     * @param int $skeletonId
     * @return $this
     * @since 2.0.0
     */
    public function setSkeletonId($skeletonId)
    {
        $this->skeletonId = (int)$skeletonId;
        return $this;
    }

    /**
     * @param string $setName
     * @return $this
     * @since 2.0.0
     */
    public function setName($setName)
    {
        $this->name = $setName;
        return $this;
    }

    /**
     * @return \Magento\Eav\Model\Entity\Attribute\Set
     * @throws AlreadyExistsException
     * @since 2.0.0
     */
    public function getAttributeSet()
    {
        $this->validateParameters();
        /** @var \Magento\Eav\Model\Entity\Attribute\Set $attributeSet */
        $attributeSet = $this->attributeSetFactory->create();
        $attributeSet->setEntityTypeId($this->entityTypeId)->load($this->name, 'attribute_set_name');
        if ($attributeSet->getId()) {
            throw new AlreadyExistsException(__('Attribute Set already exists.'));
        }

        $attributeSet->setAttributeSetName($this->name)->validate();
        $attributeSet->save();
        $attributeSet->initFromSkeleton($this->skeletonId)->save();

        return $attributeSet;
    }

    /**
     * @trows \InvalidArgumentException
     * @return void
     * @since 2.0.0
     */
    protected function validateParameters()
    {
        if (empty($this->name)) {
            throw new \InvalidArgumentException();
        } elseif (empty($this->skeletonId)) {
            throw new \InvalidArgumentException();
        } elseif (empty($this->entityTypeId)) {
            throw new \InvalidArgumentException();
        }
    }
}
