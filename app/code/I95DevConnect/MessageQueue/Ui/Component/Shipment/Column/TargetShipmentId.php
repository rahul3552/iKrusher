<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Ui\Component\Shipment\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class to show TargetShipmentId
 */
class TargetShipmentId extends Column
{

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesShipmentFactory
     */
    public $customSalesShipment;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $dataHelper;

    /**
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customSalesShipment
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \I95DevConnect\MessageQueue\Model\SalesShipmentFactory $customSalesShipment,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        array $components = [],
        array $data = []
    ) {
        $this->customSalesShipment = $customSalesShipment;
        $this->dataHelper = $dataHelper;
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
                $customShipmentModel = $this->customSalesShipment->create();
                $customShipmentData = $customShipmentModel
                    ->getCollection()
                    ->addFieldToSelect('target_shipment_id')
                    ->addFieldToFilter('source_shipment_id', $item['increment_id']);
                if ($customShipmentData->getSize() > 0) {
                    foreach ($customShipmentData as $customShipmentDataCollection) {
                        $item['target_shipment_id'] = $customShipmentDataCollection->getTargetShipmentId();
                    }
                }
            }
        }
        return $dataSource;
    }

    /**
     * @inheridoc
     */
    public function prepare()
    {
        if (!$this->dataHelper->isEnabled()) {
            $this->setData(
                'config',
                array_replace_recursive(
                    ['componentDisabled' => true],
                    (array) $this->getData('config')
                )
            );
        }
        parent::prepare();
    }
}
