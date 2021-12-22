<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Helper;

/**
 * Helper Class for Message Queue module
 */
class Data extends \I95DevConnect\MessageQueue\Helper\AbstractData
{
    /**
     * To delete Inbound MQ data
     *
     * @param type $mqEntity
     *
     * @return boolean
     * @throws \Exception
     */
    public function deleteMQData($mqEntity)
    {
        try {
            $toDeleteDate = $this->getMQCleanDate();
            $successRecord = $this->erpMessageQueue->create()->getCollection()
                ->addFieldToSelect([self::MSG_ID, self::ERROR_ID])
                ->addFieldtoFilter(self::ENTITY_CODE, $mqEntity)
                ->addFieldtoFilter('updated_dt', ['to' => $toDeleteDate])
                ->addFieldtoFilter('status', ['IN' => 2, 3, 4, 5]);
            $successRecord = $successRecord->getData();
            if ($successRecord) {
                foreach ($successRecord as $record) {
                    $dataRec = $this->erpMessageQueue->create()->get($record[self::MSG_ID]);
                    if ($dataRec['data_id'] > 0) {
                        $this->modelEntityUpdateDataFactory->create()->deleteMQData($dataRec['data_id']);
                    }
                    if ($record[self::ERROR_ID] > 0) {
                        $this->errorModel->create()->load($record[self::ERROR_ID])->delete();
                        $dataRec->setErrorId(null);
                    }
                    $dataRec->delete();
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::CLEAN, LoggerInterface::CRITICAL);
        }
        return true;
    }

    /**
     * To delete outbound MQ data
     * @param type $mqEntity
     * @return boolean
     * @throws \Exception
     */
    public function deleteMMQData($mqEntity)
    {
        try {
            $toDeleteDate = $this->getMQCleanDate();
            $successRecord = $this->magentoMessageQueue->create()->getCollection()
                ->addFieldtoFilter(self::ENTITY_CODE, $mqEntity)
                ->addFieldtoFilter('updated_dt', ['to' => $toDeleteDate])
                ->addFieldtoFilter('status', ['IN' => 2, 3, 4, 5]);
            $successRecord = $successRecord->getData();
            if ($successRecord) {
                foreach ($successRecord as $record) {
                    $dataRec = $this->magentoMessageQueue->create()->get($record[self::MSG_ID]);
                    if (!empty($dataRec)) {
                        $this->magentoMessageQueue->create()->deleteMQData($dataRec[self::MSG_ID]);
                    }
                    if ($record[self::ERROR_ID] > 0) {
                        $this->errorModel->create()->load($record[self::ERROR_ID])->delete();
                        $dataRec->setErrorId(null);
                    }
                    $dataRec->delete();
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::CLEAN, LoggerInterface::CRITICAL);
        }
        return true;
    }

    /**
     * To get MQ clean date
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Exception
     * @author Ranjith R
     */
    public function getMQCleanDate()
    {
        try {
            $todayDate = $this->date->gmtDate();
            $mqDays = $this->scopeConfig->getValue(
                'i95dev_messagequeue/I95DevConnect_mqsettings/mqdata_clean_days',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );

            if (!trim($mqDays)) {
                $mqDays = self::MQDATA_CLEAN_DAYS;
            }

            $dateObj = new \DateTime($todayDate);
            $dateObj->modify('-' . $mqDays . ' day');
            return $dateObj->format('Y-m-d H:i:s');
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::CLEAN, LoggerInterface::CRITICAL);
            throw new \Magento\Framework\Exception\LocalizedException(
                $ex->getMessage()
            );
        }
    }

    /**
     *
     * @param string $key
     * @param array $array
     * @param string $defaultValue
     * @param boolean $issetflag
     * @return string
     */
    public function getValueFromArray($key, $array, $defaultValue = null, $issetflag = true)
    {
        $value = "";
        if (isset($array[$key])) {
            $value = $array[$key];
        } else {
            if ($issetflag) {
                $value = $defaultValue;
            }
        }
        return $value;
    }

    /**
     * Get region details
     * @param string $regionCode
     * @param string $countryCode
     * @return array
     */
    public function getRegionDetails($regionCode, $countryCode)
    {
        $regionDetails = $this->regionModel->create()->loadByCode($regionCode, $countryCode)->getData();
        return is_array($regionDetails) ? $regionDetails : [$regionDetails];
    }

    /**
     * Prepare entity data
     * @param string $fieldmap
     * @param string $data
     * @return array
     */
    public function prepareInfoArray($fieldmap, $data)
    {
        $entityData = [];
        foreach ($fieldmap as $key => $value) {
            if (isset($data[$value])) {
                $entityData[$key] = $data[$value];
            } else {
                $entityData[$key] = null;
            }
        }

        return $entityData;
    }

    /**
     * Set Custom Attributes for customer
     * @param obj $customer
     */
    public function customCustomerAttributes($customer)
    {
        try {
            $targetId = $customer->getTargetCustomerId();
            $customer->setData('update_by', 'Magento')->getResource()->saveAttribute($customer, 'update_by');
            if ($customer->getOrigin() == "" || $customer->getOrigin() == "website") {
                $customer->setData(self::ORIGIN, 'website')->getResource()->saveAttribute($customer, self::ORIGIN);
            } else {
                // @updatedBy Arushi Bansal
                $component = $this->getComponent();
                $customer->setData(self::ORIGIN, $component)->getResource()->saveAttribute($customer, self::ORIGIN);
            }
            $customer->setData(self::TARGET_CUSTOMER_ID, $targetId)->getResource()
                ->saveAttribute($customer, self::TARGET_CUSTOMER_ID);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $this->logger->createLog(__METHOD__, $ex->getMessage(), self::I95EXC, LoggerInterface::CRITICAL);
        }
    }

    /**
     * Get region id
     *
     * @updatedBy Debashis S. Gopal. Added create() method in regionModel as it is a factory object.
     * @param string $regionCode
     * @param string $countryCode
     * @return string
     */
    public function getRegionId($regionCode, $countryCode)
    {
        $regionId = $this->regionModel->create()->loadByCode($regionCode, $countryCode)->getId();
        if ($regionId == "") {
            //@author kavya.koona Removed create() method to get the previous region collection
            $collection = $this->regionModel->getCollection()
                ->addFieldToFilter("country_id", $countryCode);
            $collection->getSelect()->limit(1);
            $itemsCount = count($collection->getItems());
            if ($itemsCount > 0) {
                $regionId = false;
            }
        }
        return $regionId;
    }

    /**
     * Get packet size
     * @return int
     */
    public function getPacketSize()
    {
        if ($this->getscopeConfig(
            'i95dev_messagequeue/i95dev_extns/packet_size',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        )
        ) {
            $packetSize = $this->getscopeConfig(
                'i95dev_messagequeue/i95dev_extns/packet_size',
                \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
                $this->storeManager->getDefaultStoreView()->getWebsiteId()
            );
            $packetSize = (int) $packetSize;
        } else {
            $packetSize = 1;
        }

        return $packetSize;
    }

    /**
     * check if email notification is enabled
     * @param string $entity
     * @return boolean
     * @createdBy Arushi Bansal
     */
    public function isEmailNotifyEnable($entity)
    {
        $isEnabled = $this->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_notifications/email_notifications',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
        $enabled = preg_split('/\,/', $isEnabled);

        if (in_array($entity, $enabled)) {
            return true;
        }

        return false;
    }

    /**
     * get Component name
     * @return string
     * @createdBy Arushi Bansal
     */
    public function getComponent()
    {
        return $this->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/component',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get true if we can capture invoice
     * @return bool
     * @createdBy Arushi Bansal
     */
    public function isCaptureInvoiceEnabled()
    {
        return $this->getscopeConfig(
            'i95dev_messagequeue/I95DevConnect_settings/capture_invoice',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get manage stock configuration
     * @return bool
     * @createdBy Arushi Bansal
     */
    public function getManageStock()
    {
        return $this->getscopeConfig(
            'cataloginventory/item_options/manage_stock',
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * get customer attribute
     * @param $current_customer_id
     * @return string
     */
    public function getCustomAttribute($current_customer_id)
    {
        $customerCollection = $this->customerFactory->create()
            ->load($current_customer_id)->getData();

        return isset($customerCollection[self::TARGET_CUSTOMER_ID]) ?
            $customerCollection[self::TARGET_CUSTOMER_ID] : "Customer Sync In Process";
    }

    /**
     * @param $title
     * @param $block
     * @param $menu
     * @param $breadcrumb
     * @return mixed
     */
    public function loadPage($title, $block, $menu, $breadcrumb)
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu($menu);
        $resultPage->getConfig()->getTitle()->prepend(__($title));
        $resultPage->addBreadcrumb(__($breadcrumb['label']), __($breadcrumb['title']));
        $resultPage->addContent(
            $resultPage->getLayout()->createBlock($block)
        );
        return $resultPage;
    }

    /**
     * @return mixed
     */
    public function returnToIndex()
    {
        $url = $this->_redirect->getRefererUrl();
        $login_url = $this->urlInterface
            ->getUrl('admin/index', ['referer' => base64_encode($url)]);

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setUrl($login_url);
        return $resultRedirect;
    }

    /**
     * Check if $erpCustomerGroupId is already exists in magento default groups.
     *
     * @createdBy Debashis S. Gopal
     * @param string $erpCustomerGroupId
     * @return int
     * @throws LocalizedException
     */
    public function checkInDefaultCustomerGroups($erpCustomerGroupId)
    {
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('customer_group_code', $erpCustomerGroupId, 'eq')
            ->create();
        $searchResults = $this->groupRepository->getList($searchCriteria);
        $groupInfo = $searchResults->getItems();
        if (!empty($groupInfo)) {
            if (isset($groupInfo[0])) {
                return $groupInfo[0];
            } else {
                return false;
            }
        }
    }
}
