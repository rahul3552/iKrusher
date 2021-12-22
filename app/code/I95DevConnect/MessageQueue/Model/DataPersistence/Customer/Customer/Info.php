<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 * @updatedBy Divya Koona. Removed getCustomerById function and added in Generic Helper.
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Customer;

/**
 * Class for preparing customer result data to be sent to ERP
 */
class Info
{

    const EMAIL = "email";
    public $dataHelper;
    public $eventManager;
    public $address;
    public $customerGroup;
    public $customer;
    public $customerId;
    public $fieldMapInfo = [
        'sourceId' => 'id',
        self::EMAIL => self::EMAIL,
        'firstName' => 'firstname',
        'lastName' => 'lastname',
        'reference' => self::EMAIL,
        'prefix' => 'prefix',
        'suffix' => 'suffix',
        'middleName' => 'middlename'
    ];

    public $InfoData = [];

    public $genericHelper;

    /**
     *
     * @param \Magento\Framework\Event\Manager $eventManager
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address $address
     * @param \I95DevConnect\MessageQueue\Helper\Data $dataHelper
     * @param \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup $customerGroup
     * @param \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
     */
    public function __construct(
        \Magento\Framework\Event\Manager $eventManager,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\Address $address,
        \I95DevConnect\MessageQueue\Helper\Data $dataHelper,
        \I95DevConnect\MessageQueue\Model\DataPersistence\Customer\CustomerGroup $customerGroup,
        \I95DevConnect\MessageQueue\Helper\Generic $genericHelper
    ) {
        $this->dataHelper = $dataHelper;
        $this->address = $address;
        $this->eventManager = $eventManager;
        $this->customerGroup = $customerGroup;
        $this->genericHelper = $genericHelper;
    }

    /**
     * Prepare customer information to be sent as customer result
     *
     * @param string $customerId
     * @return array
     */
    public function getInfo($customerId, $entityCode, $erpCode = null, $messageId = null) //NOSONAR
    {
        $this->customerId = $customerId;
        $this->customer = $this->genericHelper->getCustomerById($customerId);
        if (isset($this->customer)) {
            $this->InfoData = $this->dataHelper->prepareInfoArray($this->fieldMapInfo, $this->customer);

            if (isset($this->customer['custom_attributes'])) {
                foreach ($this->customer['custom_attributes'] as $value) {
                    if ($value['attribute_code'] == 'target_customer_id') {
                        $this->InfoData['targetCustomerId'] = $value['value'];
                        $this->InfoData['targetId'] = $value['value'];
                    }
                }
            }

            if (isset($this->customer['group_id'])) {
                $this->InfoData['customerGroup'] = $this->customerGroup
                        ->getCustomerGroupEntityByGroupId($this->customer['group_id']);
            } else {
                $this->InfoData['customerGroup'] = null;
            }
            $addressData = $this->address->getInfo($this->customer);
            $this->InfoData['addresses'] = $addressData;
        }

        $customerInfoEvent = "erpconnect_forward_customerinfo";
        $this->eventManager->dispatch($customerInfoEvent, ['customer' => $this]);
        return $this->InfoData;
    }
}
