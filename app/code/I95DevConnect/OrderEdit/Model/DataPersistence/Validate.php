<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_OrderEdit
 */

namespace I95DevConnect\OrderEdit\Model\DataPersistence;

use \I95DevConnect\OrderEdit\Helper\Data;

/**
 * I95Dev validator class
 */
class Validate
{
    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\DataPersistence\Validate
     */
    public $validate;

    /**
     *
     * @var \I95DevConnect\MessageQueue\Model\SalesOrderFactory
     */
    public $customSalesOrder;

    /**
     *
     * @var \I95DevConnect\OrderEdit\Helper\Data
     */
    public $editOrderHelper;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate
     * @param \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder
     * @param Data $editOrderHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Model\DataPersistence\Validate $validate,
        \I95DevConnect\MessageQueue\Model\SalesOrderFactory $customSalesOrder,
        Data $editOrderHelper
    ) {
        $this->validate = $validate;
        $this->customSalesOrder = $customSalesOrder;
        $this->editOrderHelper = $editOrderHelper;
    }

    /**
     * Validate the required fields
     * @param string $stringData
     * @param array $requiredFields
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateData($stringData, $requiredFields)
    {
        if (!$this->editOrderHelper->isEnabled()) {
            throw new \Magento\Framework\Exception\LocalizedException(__('edit_order_001'));
        }
        $this->validate->validateFields = $requiredFields;
        $this->validate->validateData($stringData);
    }

    /**
     * Validate if the order is eligible for editing.
     * @param type $dataString
     * @return type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateOrderToEdit($dataString)
    {
        $this->dataString = $dataString;
        $oldOrder = $this->getOrder();
        if ($oldOrder->getId() && $oldOrder->getStatus() != "canceled") {
            if ($oldOrder->hasInvoices()) {
                throw new \Magento\Framework\Exception\LocalizedException(__("edit_order_005"));
            }
            if ($oldOrder->hasShipments()) {
                throw new \Magento\Framework\Exception\LocalizedException(__("edit_order_006"));
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(__('edit_order_004'));
        }
        return $oldOrder;
    }

    /**
     * Check for given order exist. If exists return that order else throw error.
     * @return type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrder()
    {
        $loadCustomOrder = $this->customSalesOrder->create()
            ->getCollection()
            ->addFieldToFilter("target_order_id", $this->dataString['targetId'])
            ->setOrder('id', 'DESC')
            ->getFirstItem();
        $sourceOrderId = $loadCustomOrder->getSourceOrderId();

        if ($sourceOrderId === '') {
            throw new \Magento\Framework\Exception\LocalizedException(__('edit_order_004'));
        } else {
            return $this->editOrderHelper->getOrderByIncrementId($sourceOrderId);
        }
    }
    /**
     * Validate I95Dev message que sales order info
     * @param array $dataString
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validateOrderToUpdate($dataString)
    {
        $this->dataString = $dataString;
        $oldOrder = $this->getOrder();
        if (!$oldOrder->getId() || $oldOrder->getStatus() == "canceled") {
            throw new \Magento\Framework\Exception\LocalizedException(__('edit_order_006'));
        }
        return $oldOrder;
    }

    /**
     * Validate address field and data
     * @param array $address
     * @return boolean
     */
    public function validateAddress($address)
    {
        $validateFields = [
            'firstname' => 'i95dev_addr_002',
            'lastname' => 'i95dev_addr_003',
            'country_id' => 'i95dev_addr_004',
            'region_id' => 'i95dev_addr_005',
            'city' => 'i95dev_addr_006',
            'street' => 'i95dev_addr_007',
            'postcode' => 'i95dev_addr_008',
            'telephone' => 'i95dev_addr_009'
        ];
        $this->validate->validateFields = $validateFields;
        $this->validate->validateData($address);
        return true;
    }
}
