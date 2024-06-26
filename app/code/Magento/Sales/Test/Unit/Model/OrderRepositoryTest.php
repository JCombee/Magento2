<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\Sales\Test\Unit\Model;

use Magento\Framework\Api\SearchCriteria;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use Magento\Payment\Api\Data\PaymentAdditionalInfoInterface;
use Magento\Payment\Api\Data\PaymentAdditionalInfoInterfaceFactory;
use Magento\Sales\Api\Data\OrderExtensionInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderPaymentInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterfaceFactory as SearchResultFactory;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipping;
use Magento\Sales\Model\Order\ShippingAssignment;
use Magento\Sales\Model\Order\ShippingAssignmentBuilder;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\OrderRepository;
use Magento\Sales\Model\ResourceModel\Metadata;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Sales\Model\ResourceModel\Order\Collection;
use Magento\Tax\Api\Data\OrderTaxDetailsInterface;
use Magento\Tax\Api\OrderTaxManagementInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class OrderRepositoryTest extends TestCase
{
    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var Metadata|MockObject
     */
    private $metadata;

    /**
     * @var SearchResultFactory|MockObject
     */
    private $searchResultFactory;

    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * @var MockObject
     */
    private $collectionProcessor;

    /**
     * @var OrderTaxManagementInterface|MockObject
     */
    private $orderTaxManagementMock;

    /**
     * @var PaymentAdditionalInfoInterfaceFactory|MockObject
     */
    private $paymentAdditionalInfoFactory;

    /**
     * @var ShippingAssignmentBuilder|MockObject
     */
    private $shippingAssignmentBuilder;

    /**
     * Setup the test
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->objectManager = new ObjectManager($this);

        $this->metadata = $this->createMock(Metadata::class);

        $this->searchResultFactory = $this->getMockBuilder(SearchResultFactory::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['create'])
            ->getMock();
        $this->collectionProcessor = $this->createMock(
            CollectionProcessorInterface::class
        );
        $this->orderTaxManagementMock = $this->getMockBuilder(OrderTaxManagementInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $this->paymentAdditionalInfoFactory = $this->getMockBuilder(PaymentAdditionalInfoInterfaceFactory::class)
            ->disableOriginalConstructor()
            ->setMethods(['create'])->getMockForAbstractClass();
        $this->shippingAssignmentBuilder = $this->getMockBuilder(ShippingAssignmentBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();
        $this->orderRepository = $this->objectManager->getObject(
            OrderRepository::class,
            [
                'metadata' => $this->metadata,
                'searchResultFactory' => $this->searchResultFactory,
                'collectionProcessor' => $this->collectionProcessor,
                'orderTaxManagement' => $this->orderTaxManagementMock,
                'paymentAdditionalInfoFactory' => $this->paymentAdditionalInfoFactory,
                'shippingAssignmentBuilder' => $this->shippingAssignmentBuilder
            ]
        );
    }

    /**
     * Test for method getList.
     *
     * @return void
     */
    public function testGetList()
    {
        $searchCriteriaMock = $this->createMock(SearchCriteria::class);
        $collectionMock = $this->createMock(Collection::class);
        $itemsMock = $this->getMockBuilder(OrderInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderTaxDetailsMock = $this->getMockBuilder(OrderTaxDetailsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAppliedTaxes', 'getItems'])->getMockForAbstractClass();
        $paymentMock = $this->getMockBuilder(OrderPaymentInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $paymentAdditionalInfo = $this->getMockBuilder(PaymentAdditionalInfoInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setKey', 'setValue'])->getMockForAbstractClass();

        $extensionAttributes = $this->getMockBuilder(OrderExtensionInterface::class)
            ->addMethods(
                [
                    'getShippingAssignments',
                    'setAppliedTaxes',
                    'setConvertingFromQuote',
                    'setItemAppliedTaxes',
                    'setPaymentAdditionalInfo'
                ]
            )
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $shippingAssignmentBuilder = $this->createMock(
            ShippingAssignmentBuilder::class
        );
        $itemsMock->expects($this->atLeastOnce())->method('getEntityId')->willReturn(1);
        $this->collectionProcessor->expects($this->once())
            ->method('process')
            ->with($searchCriteriaMock, $collectionMock);
        $itemsMock->expects($this->atLeastOnce())->method('getExtensionAttributes')->willReturn($extensionAttributes);
        $itemsMock->expects($this->atleastOnce())->method('getPayment')->willReturn($paymentMock);
        $paymentMock->expects($this->atLeastOnce())->method('getAdditionalInformation')
            ->willReturn(['method' => 'checkmo']);
        $this->paymentAdditionalInfoFactory->expects($this->atLeastOnce())->method('create')
            ->willReturn($paymentAdditionalInfo);
        $paymentAdditionalInfo->expects($this->atLeastOnce())->method('setKey')->willReturnSelf();
        $paymentAdditionalInfo->expects($this->atLeastOnce())->method('setValue')->willReturnSelf();
        $this->orderTaxManagementMock->expects($this->atLeastOnce())->method('getOrderTaxDetails')
            ->willReturn($orderTaxDetailsMock);
        $extensionAttributes->expects($this->any())
            ->method('getShippingAssignments')
            ->willReturn($shippingAssignmentBuilder);

        $this->searchResultFactory->expects($this->once())->method('create')->willReturn($collectionMock);
        $collectionMock->expects($this->once())->method('getItems')->willReturn([$itemsMock]);

        $this->assertEquals($collectionMock, $this->orderRepository->getList($searchCriteriaMock));
    }

    /**
     * Test for method save.
     *
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testSave()
    {
        $mapperMock = $this->getMockBuilder(OrderResource::class)
            ->disableOriginalConstructor()
            ->getMock();
        $orderEntity = $this->createMock(Order::class);
        $extensionAttributes = $this->getMockBuilder(OrderExtensionInterface::class)
            ->addMethods(['getShippingAssignments'])
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $shippingAssignment = $this->getMockBuilder(ShippingAssignment::class)
            ->disableOriginalConstructor()
            ->setMethods(['getShipping'])
            ->getMock();
        $shippingMock = $this->getMockBuilder(Shipping::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAddress', 'getMethod'])
            ->getMock();
        $orderEntity->expects($this->once())->method('getExtensionAttributes')->willReturn($extensionAttributes);
        $orderEntity->expects($this->once())->method('getIsNotVirtual')->willReturn(true);
        $extensionAttributes
            ->expects($this->any())
            ->method('getShippingAssignments')
            ->willReturn([$shippingAssignment]);
        $shippingAssignment->expects($this->once())->method('getShipping')->willReturn($shippingMock);
        $shippingAddressMock = $this->getMockBuilder(OrderAddressInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $shippingMock->expects($this->once())->method('getAddress')->willReturn($shippingAddressMock);
        $shippingMock->expects($this->once())->method('getMethod');
        $this->metadata->expects($this->once())->method('getMapper')->willReturn($mapperMock);
        $mapperMock->expects($this->once())->method('save');
        $orderEntity->expects($this->any())->method('getEntityId')->willReturn(1);
        $this->orderRepository->save($orderEntity);
    }

    /**
     * Test for method get.
     *
     * @return void
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testGet()
    {
        $orderId = 1;
        $appliedTaxes = 'applied_taxes';
        $items = 'items';
        $paymentInfo = [];

        $paymentMock = $this->getMockBuilder(OrderPaymentInterface::class)
            ->disableOriginalConstructor()->getMockForAbstractClass();
        $paymentMock->expects($this->once())->method('getAdditionalInformation')->willReturn($paymentInfo);

        $orderExtension = $this->getMockBuilder(OrderExtensionInterface::class)
            ->addMethods(
                [
                    'getShippingAssignments',
                    'setAppliedTaxes',
                    'setConvertingFromQuote',
                    'setItemAppliedTaxes',
                    'setPaymentAdditionalInfo',
                    'setShippingAssignments'
                ]
            )
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();
        $orderExtension->expects($this->once())->method('getShippingAssignments')->willReturn(null);
        $orderExtension->expects($this->once())->method('setAppliedTaxes')->with($appliedTaxes);
        $orderExtension->expects($this->once())->method('setConvertingFromQuote')->with(false);
        $orderExtension->expects($this->once())->method('setItemAppliedTaxes')->with($items);
        $orderExtension->expects($this->once())->method('setPaymentAdditionalInfo')->with($paymentInfo);

        $orderEntity = $this->createMock(Order::class);
        $orderEntity->expects($this->exactly(3))->method('getExtensionAttributes')->willReturn($orderExtension);
        $orderEntity->expects($this->once())->method('load')->with($orderId)->willReturn($orderEntity);
        $orderEntity->expects($this->exactly(2))->method('getEntityId')->willReturn($orderId);
        $orderEntity->expects($this->once())->method('getPayment')->willReturn($paymentMock);
        $orderEntity->expects($this->exactly(3))->method('setExtensionAttributes')->with($orderExtension);
        $orderEntity->expects($this->exactly(3))
            ->method('getExtensionAttributes')
            ->willReturnOnConsecutiveCalls(null, $orderExtension, $orderExtension);

        $this->metadata->expects($this->once())->method('getNewInstance')->willReturn($orderEntity);
        $orderTaxDetailsMock = $this->getMockBuilder(OrderTaxDetailsInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['setAppliedTaxes'])->getMockForAbstractClass();
        $orderTaxDetailsMock->expects($this->once())->method('getAppliedTaxes')->willReturn($appliedTaxes);
        $orderTaxDetailsMock->expects($this->once())->method('getItems')->willReturn($items);
        $this->orderTaxManagementMock->expects($this->atLeastOnce())->method('getOrderTaxDetails')
            ->willReturn($orderTaxDetailsMock);
        $this->shippingAssignmentBuilder->expects($this->once())->method('setOrder')->with($orderEntity);

        $this->orderRepository->get($orderId);
    }
}
