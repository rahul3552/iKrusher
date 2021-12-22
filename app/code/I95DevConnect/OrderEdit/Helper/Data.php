<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_OrderEdit
 */
namespace I95DevConnect\OrderEdit\Helper;

use Magento\Directory\Model\Region;
use \I95DevConnect\MessageQueue\Api\LoggerInterface;

/**
 * Order edit base helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
    * Enabled config path
    */
    const XML_PATH_ENABLED = 'i95devconnect_orderEdit/orderedit_enabled_settings/enable_orderedit';
    const STREET = 'street';
    const STREET_2 = 'street2';
    const FIRST_NAME = 'firstName';
    const LAST_NAME = 'lastName';
    const REGION_ID = 'regionId';
    const POSTCODE = 'postcode';
    const COUNTRYID = 'countryId';
    const TELEPHONE = 'telephone';
    const REGIONNAME = 'region_name';
    const REGIONID = 'region_id';
    const COMPANY = 'company';
    const PREFIX = 'prefix';
    const SUFFIX = 'suffix';

    /**
     *
     * @var type \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory
     */
    public $logger;

    /**
     *
     * @var $scopeConfig
     */
    public $scopeConfig;

    /**
     * @var Region
     */
    public $regionFactory;

    /**
     *
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger
     * @param Region $regionFactory
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \I95DevConnect\MessageQueue\Api\LoggerInterfaceFactory $logger,
        Region $regionFactory,
        \Magento\Sales\Api\Data\OrderInterfaceFactory $orderFactory
    ) {
        $this->scopeConfig = $context->getScopeConfig();
        $this->logger = $logger;
        $this->regionFactory = $regionFactory;
        $this->orderFactory = $orderFactory;
        parent::__construct($context);
    }

    /**
     * Check if module is enabled
     *
     * @return string|null
     */
    public function isEnabled()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLED, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getOrderByIncrementId($incrementId)
    {
        try {
            return $this->orderFactory->create()->loadByIncrementId($incrementId);
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
    /**
     * Compare between order address and edited address, its same or not
     * @param obj $orderAddress
     * @param array $editaddress
     * @return boolean
     */
    public function compareAddress($orderAddress, $editaddress)
    {
        try {
            $street1 = (isset($editaddress[self::STREET]) ? $editaddress[self::STREET] : "");
            $street2 = (isset($editaddress[self::STREET_2]) ? $editaddress[self::STREET_2] : "");
            $existingStreet1 = isset($orderAddress->getStreet()[0]) ? $orderAddress->getStreet()[0] :"";
            $existingStreet2 = isset($orderAddress->getStreet()[1]) ? $orderAddress->getStreet()[1] :"";
            $firstName = (isset($editaddress[self::FIRST_NAME]) ? $editaddress[self::FIRST_NAME] : "");
            $lastName = (isset($editaddress[self::LAST_NAME]) ? $editaddress[self::LAST_NAME] : "");
            $city = (isset($editaddress['city']) ? $editaddress['city'] : "");
            $region = (isset($editaddress[self::REGION_ID]) ? $editaddress[self::REGION_ID] : "");
            $postCode = (isset($editaddress[self::POSTCODE]) ? $editaddress[self::POSTCODE] : "");
            $countryId = (isset($editaddress[self::COUNTRYID]) ? $editaddress[self::COUNTRYID] : "");
            $telePhone = (isset($editaddress[self::TELEPHONE]) ? $editaddress[self::TELEPHONE] : "");
            $regionCode = $this->_getRegionInfo($region, $countryId);
            if (trim((string) $orderAddress->getPostcode()) == trim((string) $postCode)
                 && trim((string) $existingStreet1) == trim((string) $street1)
                 && trim((string) $existingStreet2) == trim((string) $street2)
                 && trim((string) $orderAddress->getCity()) == trim((string) $city)
                 && trim((string) $orderAddress->getRegion()) == trim((string) $regionCode[self::REGIONNAME])
                 && trim((string) $orderAddress->getCountryId()) == trim((string) $countryId)
                 && trim((string) $orderAddress->getTelephone()) == trim((string) $telePhone)
                 && trim((string) $orderAddress->getFirstname()) == trim((string) $firstName)
                 && trim((string) $orderAddress->getLastname()) == trim((string) $lastName)
            ) {
                $compare = true;
            } else {
                $compare = false;
            }
        } catch (\Exception $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                'critical'
            );
        }
        return $compare;
    }

    /**
     * Get regionId and regionName from region and country id
     * @param int $region
     * @param int $countryId
     * @return array
     */
    public function _getRegionInfo($region, $countryId)
    {
        $regionModel = $this->regionFactory->loadByCode($region, $countryId);
        $regionCode = $regionModel->getName();
        if (!isset($regionCode)) {
            $regionCode = $region;
        }
        $regionId = $regionModel->getId();
        return [self::REGIONID => $regionId, self::REGIONNAME => $regionCode];
    }

    /**
     * Find address info for order data
     * @param array $address
     * @Exception critical
     * @return array
     */
    public function prepareAddress($address)
    {
        try {
            $street = (isset($address[self::STREET]) ? $address[self::STREET] : "");
            $street2 = (isset($address[self::STREET_2]) ? $address[self::STREET_2] : "");
            $region = (isset($address[self::REGION_ID]) ? $address[self::REGION_ID] : "");
            $countryId = (isset($address[self::COUNTRYID]) ? $address[self::COUNTRYID] : "");
            $regionCode = $this->_getRegionInfo($region, $countryId);
            $addressData = [
                'firstname'  => (isset($address[self::FIRST_NAME]) ? $address[self::FIRST_NAME] : ""),
                'middlename'   => (isset($address['middleName']) ? $address['middleName'] : ""),
                'lastname'   => (isset($address[self::LAST_NAME]) ? $address[self::LAST_NAME] : ""),
                self::STREET => [$street,$street2],
                'city'       => (isset($address['city']) ? $address['city'] : ""),
                'region'     => $regionCode[self::REGIONNAME],
                self::COMPANY    => (isset($address[self::COMPANY]) ? $address[self::COMPANY] : ""),
                self::REGIONID  => $regionCode[self::REGIONID],
                self::POSTCODE   => (isset($address[self::POSTCODE]) ? $address[self::POSTCODE] : ""),
                'country_id' => $countryId,
                self::TELEPHONE  => (isset($address[self::TELEPHONE]) ? $address[self::TELEPHONE] : ""),
                'fax'        => (isset($address['fax']) ? $address['fax'] : ""),
                self::PREFIX        => (isset($address[self::PREFIX]) ? $address[self::PREFIX] : ""),
                self::SUFFIX        => (isset($address[self::SUFFIX]) ? $address[self::SUFFIX] : "")
            ];
        } catch (\Exception $ex) {
            $this->logger->create()->createLog(
                __METHOD__,
                $ex->getMessage(),
                LoggerInterface::I95EXC,
                'critical'
            );
        }
        return $addressData;
    }
}
