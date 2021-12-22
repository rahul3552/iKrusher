<?php

/**
 * @author i95Dev <arushi bansal>
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Ui\Component\Order\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class to OrderCustom
 */
class OrderCustom extends Column
{
    const TARGET_ORDER_ID='target_order_id';
    const ORIGIN='origin';

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesOrderFactory
     */
    public $customSalesOrder;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $helperData;

    /**
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param \I95DevConnect\MessageQueue\Helper\Data $helperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        \I95DevConnect\MessageQueue\Helper\Data $helperData,
        array $components = [],
        array $data = []
    ) {

        $this->customSalesOrder = $customSalesOrder;
        $this->helperData = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $customOrderModel = $this->customSalesOrder->create();
                $customOrderData = $customOrderModel
                        ->getCollection()
                        ->addFieldToSelect([self::TARGET_ORDER_ID, self::ORIGIN])
                        ->addFieldToFilter('source_order_id', $item['increment_id']);
                if ($customOrderData->getSize() > 0) {
                    $this->prepareCustomOrderDataSource($customOrderData, $dataSource);
                }
            }
        }
        return $dataSource;
    }

    /**
     * @param $customOrderData
     * @param $dataSource
     * @return mixed
     */
    public function prepareCustomOrderDataSource($customOrderData, $dataSource)
    {
        foreach ($customOrderData as $customOrderDataCollection) {
            switch ($this->getData('name')) {
                case self::TARGET_ORDER_ID:
                    $item[self::TARGET_ORDER_ID] = $customOrderDataCollection->getTargetOrderId();
                    break;
                case self::ORIGIN:
                    $item[self::ORIGIN] = $customOrderDataCollection->getOrigin();
                    break;
                default:
                    return $dataSource;
            }
        }
    }

    /**
     * @inheridoc
     */
    public function prepare()
    {
        if (!$this->helperData->isEnabled()) {

            $this->setData(
                'config',
                array_replace_recursive(
                    ['componentDisabled' =>true],
                    (array)$this->getData('config')
                )
            );
        }

        parent::prepare();
    }
}
