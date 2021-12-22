<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Ui\Component\Invoice\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class add  TargetInvoiceId
 */
class TargetInvoiceId extends Column
{

    /**
     * @var \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory
     */
    public $customSalesInvoice;

    /**
     * @var \I95DevConnect\MessageQueue\Helper\Data
     */
    public $helperData;

    /**
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customSalesInvoice
     * @param \I95DevConnect\MessageQueue\Helper\Data $helperData
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \I95DevConnect\MessageQueue\Model\SalesInvoiceFactory $customSalesInvoice,
        \I95DevConnect\MessageQueue\Helper\Data $helperData,
        array $components = [],
        array $data = []
    ) {

        $this->customSalesInvoice = $customSalesInvoice;
        $this->helperData = $helperData;
        parent::__construct($context, $uiComponentFactory, $components, $data);
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

    /**
     * @param array $dataSource
     *
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $customInvoiceModel = $this->customSalesInvoice->create();
                $customInvoiceData = $customInvoiceModel
                        ->getCollection()
                        ->addFieldToSelect('target_invoice_id')
                        ->addFieldToFilter('source_invoice_id', $item['increment_id']);
                if ($customInvoiceData->getSize() > 0) {
                    foreach ($customInvoiceData as $customInvoiceDataCollection) {
                        $target_invoice_id = $customInvoiceDataCollection->getTargetInvoiceId();
                        $item['target_invoice_id'] = strlen($target_invoice_id) > 1 ?
                            $target_invoice_id : '';
                    }
                }
            }
        }
        return $dataSource;
    }
}
