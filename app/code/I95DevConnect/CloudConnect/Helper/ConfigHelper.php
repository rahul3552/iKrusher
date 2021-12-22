<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Helper;

/**
 * Helper class to get i95Dev configurations
 */
class ConfigHelper extends \Magento\Framework\App\Helper\AbstractHelper
{

    public $scopeConfig;
    public $reverseSkipEntities;
    public $forwardSkipEntities;
    public $entityTypeModel;

    /**
     * ConfigHelper constructor.
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel
     * @param array $reverseSkipEntities
     * @param array $forwardSkipEntities
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\MessageQueue\Model\EntityFactory $entityTypeModel,
        $reverseSkipEntities,
        $forwardSkipEntities,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->reverseSkipEntities = $reverseSkipEntities;
        $this->forwardSkipEntities = $forwardSkipEntities;
        $this->storeManager = $storeManager;
        $this->entityTypeModel = $entityTypeModel;
    }

    const XML_PATH_GENERIC_CONNECT_ERP_CRM = 'i95dev_adapter_configurations/enabled_disabled/crmerp';

    /**
     * get erpcrm config value
     * @return type
     */
    public function getSelctErpCrmConfig()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE;
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERIC_CONNECT_ERP_CRM,
            $storeScope,
            $this->storeManager->getDefaultStoreView()->getWebsiteId()
        );
    }

    /**
     * list of entities that needs to be skipped for forward sync
     * @return array
     */
    public function getForwardSkipEntities()
    {
        $entities = $this->entityTypeModel->create()->getCollection()->addFieldToSelect('entity_code');
        $entities->addFieldToFilter('support_for_outbound', 0);
        if (!$entities->getSize() > 0) {
            $this->forwardSkipEntities = [];
        } else {
            foreach ($entities as $skipEntities) {
                $this->forwardSkipEntities[] = $skipEntities->getEntityCode();
            }
        }
        return $this->forwardSkipEntities;
    }

    /**
     * list of entities that needs to be skipped for reverse sync
     * @return array
     */
    public function getReverseSkipEntities()
    {
        $entities = $this->entityTypeModel->create()->getCollection()->addFieldToSelect('entity_code');
        $entities->addFieldToFilter('support_for_inbound', 0);
        if (!$entities->getSize() > 0) {
            $this->reverseSkipEntities = [];
        } else {
            foreach ($entities as $skipEntities) {
                $this->reverseSkipEntities[] = $skipEntities->getEntityCode();
            }
        }
        return $this->reverseSkipEntities;
    }
}
