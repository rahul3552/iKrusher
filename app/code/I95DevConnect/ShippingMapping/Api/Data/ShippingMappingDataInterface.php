<?php
/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Api\Data;

/**
 * Represents Data Object for a Shipping Mapping
 */
interface ShippingMappingDataInterface
{

    const ID = 'id';
    const ERP_CODE = 'erp_code';
    const MAGENTO_CODE = 'magento_code';
    const IS_ERP_DEFAULT = 'is_erp_default';
    const IS_ECOMMERCE_DEFAULT = 'is_ecommerce_default';

    /**
     * get id
     *
     * @return int|null
     */
    public function getId();

    /**
     * set id
     *
     * @param $Id
     * @return mixed
     */
    public function setId($Id);

    /**
     * get erp code
     *
     * @return int|null
     */
    public function getErpCode();

    /**
     * Set erp code
     *
     * @param string $erpCode
     * @return $this
     */
    public function setErpCode($erpCode);

    /**
     * get magento code
     *
     * @return string
     */
    public function getMagentoCode();

    /**
     * get magento code
     *
     * @param string $magentoCode
     * @return $this
     */
    public function setMagentoCode($magentoCode);

    /**
     * get is erp default
     *
     * @return string
     */
    public function getIsErpDefault();

    /**
     * set is erp default
     *
     * @param string $isErpDefault
     * @return $this
     */
    public function setIsErpDefault($isErpDefault);

    /**
     * get is ecommerce default
     *
     * @return string
     */
    public function getIsEcommerceDefault();

    /**
     * set is ecommerce default
     *
     * @param string $isEcommerceDefault
     * @return $this
     */
    public function setIsEcommerceDefault($isEcommerceDefault);
}
