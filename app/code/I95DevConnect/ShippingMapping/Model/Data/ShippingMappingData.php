<?php

/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Model\Data;

use \I95DevConnect\ShippingMapping\Api\Data\ShippingMappingDataInterface;
use \Magento\Framework\Model\AbstractModel;

/**
 * i95Dev Shipping mapping data model
 */
class ShippingMappingData extends AbstractModel implements ShippingMappingDataInterface
{

    protected $_eventPrefix = 'i95dev_shipping_mapping';

    /**
     * i95Dev Shipping mapping data model constructor
     */
    protected function _construct()
    {
        $this->_init('I95DevConnect\ShippingMapping\Model\ResourceModel\ShippingMapping');
    }

    /**
     * get id
     * @return int|mixed|void|null
     */
    public function getId()
    {
        $this->getData(self::ID);
    }

    /**
     * Set Id
     *
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * get Erp Code
     *
     * @return string
     */
    public function getErpCode()
    {
        return $this->getData(self::ERP_CODE);
    }

    /**
     * get Erp Code
     *
     * @param string $erpCode
     * @return $this
     */
    public function setErpCode($erpCode)
    {
        return $this->setData(self::ERP_CODE, $erpCode);
    }

    /**
     * get magento code
     *
     * @return string|null
     */
    public function getMagentoCode()
    {
        return $this->getData(self::MAGENTO_CODE);
    }

    /**
     * set magento code
     *
     * @param string $magentoCode
     * @return $this
     */
    public function setMagentoCode($magentoCode)
    {
        return $this->setData(self::MAGENTO_CODE, $magentoCode);
    }

    /**
     * get is erp default
     *
     * @return string|null
     */
    public function getIsErpDefault()
    {
        return $this->getData(self::IS_ERP_DEFAULT);
    }

    /**
     * set is erp default
     *
     * @param string $isErpDefault
     * @return $this
     */
    public function setIsErpDefault($isErpDefault)
    {
        return $this->setData(self::IS_ERP_DEFAULT, $isErpDefault);
    }

    /**
     * get is ecommerce default
     *
     * @return string|null
     */
    public function getIsEcommerceDefault()
    {
        return $this->getData(self::IS_ECOMMERCE_DEFAULT);
    }

    /**
     * set is Ecommerce Default
     *
     * @param string $isEcommerceDefault
     * @return $this
     */
    public function setIsEcommerceDefault($isEcommerceDefault)
    {
        return $this->setData(self::IS_ECOMMERCE_DEFAULT, $isEcommerceDefault);
    }
}
