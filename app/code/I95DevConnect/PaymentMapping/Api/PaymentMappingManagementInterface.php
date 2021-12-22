<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\PaymentMapping\Api;

use Magento\Framework\Exception\LocalizedException;

/**
 * Management Interface for Payment Mapping
 */
interface PaymentMappingManagementInterface
{

    /**
     * data insertion into Payment mapping table
     *
     * @param string|object $data
     * @throws LocalizedException
     * @return bool
     */
    public function processMappingData($data);
    
    /**
     * get the magento payment method code from mapping table
     *
     * @param string $magentoPaymentMethod
     * @throws LocalizedException
     * @return string
     */
    public function getByMagentoCode($magentoPaymentMethod);
    
    /**
     * get the erp payment method code from mapping table
     *
     * @param string $erpPaymentMethod
     * @throws LocalizedException
     * @return string
     */
    public function getByErpCode($erpPaymentMethod);
}
