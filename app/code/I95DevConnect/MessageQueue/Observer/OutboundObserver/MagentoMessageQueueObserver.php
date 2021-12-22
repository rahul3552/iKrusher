<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Observer\OutboundObserver;

/**
 * Observer class to set the records in magento message queue
 */
class MagentoMessageQueueObserver extends BaseObserver
{

    const XML_PATH_GENERIC_CONNECT_ERP_CRM = 'i95dev_messagequeue/I95DevConnect_settings/component';
    const ORDER = 'order';

    /**
     * To save data in outbound messageQueue for any entity
     * @param \Magento\Framework\Event\Observer $observer
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try {
            if ($this->canEnterObserver()) {
                $dataObject = $observer->getEvent()->getData("data_object");
                $savingSource = $this->dataHelper->coreRegistry->registry("savingSource");

                $observerConfig = $this->getValueFromArray($this->observerRouting, $observer->getEvent()->getName());

                if (is_array($observerConfig)) {
                    $methodName = $observerConfig['sourceKeyMethod'];
                    $entityCode = $observerConfig['entityCode'];

                    /* @author Arushi Bansal - prevent new entry to magento outbound messagequeue,
                     * if that entity is disabled
                     */
                    $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
                    $component = $this->scopeConfig->getValue(
                        self::XML_PATH_GENERIC_CONNECT_ERP_CRM,
                        $storeScope,
                        $this->storeManager->getDefaultStoreView()->getWebsiteId()
                    );

                    if ($this->skipOutboundObserver($entityCode, $component, $dataObject)) {
                        return;
                    }

                    $this->setDataObject($dataObject);
                    $magentoId = $dataObject->$methodName();

                    if ($this->skipProduct($observer, $component, $magentoId, $entityCode)) {
                        return;
                    }

                    if ($entityCode == 'address') {
                        $entityCode = 'Customer';
                        $magentoId = $dataObject->getData('customer_id');
                    }

                    $this->setSourceData($savingSource);
                    $this->setEntitycode($entityCode);
                    $this->setMagentoId($magentoId);
                    $eventName = 'erp_connect_magento_message_queue';
                    $this->eventManager->dispatch($eventName, ['currentObject' => $this]);

                    $this->saveRecord();
                }
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
    }

    /**
     * @param $entitiyCode
     * @param $component
     * @param $dataObject
     * @return bool
     */
    public function skipOutboundObserver($entitiyCode, $component, $dataObject)
    {
        $entityStatus = $this->entityTypeModel->create()->load($entitiyCode);
        $skip = false;
        if ($entityStatus->getSupportForOutbound() == 0) {
            $skip = true;
        }

        /** @updatedBy Debashis. Stopping Customer group sync Magento to ERP, if ERP is  BC or NAV **/
        if (($component == 'NAV' || $component == 'BC') && $entitiyCode == 'CustomerGroup') {
            $skip = true;
        }

        if ($entitiyCode == self::ORDER && (($dataObject->getData('customer_is_guest') &&
                    $dataObject->getData('customer_id') !== null)
                || $dataObject->getData('status') == "closed" ||
                $dataObject->getData('status') == "canceled")) {
            $skip = true;
        }

        return $skip;
    }

    /**
     * @return bool
     */
    public function canEnterObserver()
    {
        $is_enabled = $this->dataHelper->isEnabled();
        return $is_enabled && (!($this->dataHelper->getGlobalValue('i95_observer_skip') ||
            !empty($this->request->getParam('isI95DevRestReq'))));
    }

    /**
     * @param $observer
     * @param $component
     * @param $magentoId
     * @param $entityCode
     * @return bool
     */
    public function skipProduct($observer, $component, $magentoId, $entityCode)
    {
        if ($entityCode == 'product' && $component != 'GP') {
            $product = $observer->getEvent()->getProduct();
            $supportedArray = $this->generic->getSupportedTypesForProduct();
            $productType = $product->getTypeId();
            if ($product->getTargetproductstatus() == "synced" || !in_array($productType, $supportedArray)) {
                return true;
            }
        } elseif ($entityCode == self::ORDER) {
            //fix added for multiple entry issue of order
            $mqOrder = $this->i95DevMagentoMQRepo->create()->getCollection();
            $mqOrder->addFieldToFilter("entity_code", self::ORDER)
                ->addFieldToFilter("magento_id", $magentoId);
            $mqOrder->getSelect()->limit(1);

            if (!empty($mqOrder->getData())) {
                return true;
            }
        }

        return false;
    }
}
