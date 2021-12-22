<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2020 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PaymentMapping
 */

namespace I95DevConnect\PaymentMapping\Model;

use Exception;
use I95DevConnect\PaymentMapping\Api\PaymentMappingManagementInterface;
use I95DevConnect\PaymentMapping\Api\Data\PaymentMappingDataInterfaceFactory;
use I95DevConnect\PaymentMapping\Model\ResourceModel\PaymentMappingFactory;
use I95DevConnect\PaymentMapping\Model\PaymentMappingFactory as PaymentMapping;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\DateTime;
use I95DevConnect\MessageQueue\Helper\Data;

/**
 * class for managing Payment Mapping database interaction
 */
class PaymentMappingManagement implements PaymentMappingManagementInterface
{

    public $date;
    public $paymentMappingData;
    public $paymentMappingResource;
    public $dataHelper;
    /**
     * @var PaymentMapping
     */
    public $paymentMapping;

    /**
     * payment mapping management constructor
     * @param PaymentMappingDataInterfaceFactory $paymentMappingData
     * @param PaymentMappingFactory $paymentMappingResource
     * @param DateTime $date
     * @param Data $dataHelper
     * @param PaymentMapping $paymentMapping
     */
    public function __construct(
        PaymentMappingDataInterfaceFactory $paymentMappingData,
        PaymentMappingFactory $paymentMappingResource,
        DateTime $date,
        Data $dataHelper,
        PaymentMapping $paymentMapping
    ) {
        $this->paymentMappingResource = $paymentMappingResource;
        $this->paymentMappingData = $paymentMappingData;
        $this->date = $date;
        $this->dataHelper = $dataHelper;
        $this->paymentMapping = $paymentMapping;
    }

    /**
     * insert the data in payment mapping table
     * @param string|object $payment_mapping_list
     * @return bool
     * @throws LocalizedException
     */
    public function processMappingData($payment_mapping_list)
    {
        try {
            if (is_string($payment_mapping_list)) {
                $payment_mapping_list = json_decode($payment_mapping_list);
            }

            if ($payment_mapping_list) {
                $prepareDataObject = $this->paymentMappingData->create();
                $prepareDataObject->setMappedData(json_encode($payment_mapping_list));
                $prepareDataObject->setCreatedAt($this->date->gmtDate());
                $this->paymentMappingResource->create()->save($prepareDataObject);
            }

            return true;
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * get erp payment method code using magento payment method
     * @param string $magentoPaymentMethod
     * @return string
     * @throws LocalizedException
     */
    public function getByMagentoCode($magentoPaymentMethod)
    {
        try {
            $mappedData = $this->getMappingData($magentoPaymentMethod, "Matched_erp_payment_method_not_found");
            $mappedMethod = "";

            $mappedInfo = json_decode($mappedData["mapped_data"]);
            $i = 0;
            foreach ($mappedInfo as $data) {
                if (strtolower(trim($data->ecommerceMethod)) == strtolower(trim($magentoPaymentMethod))) {
                    if (empty($i)) {
                        $mappedMethod = trim($data->erpMethod);
                        $i++;
                    }

                    if (trim($data->isErpDefault)) {
                        return trim($data->erpMethod);
                    }
                }
            }

            if (!$mappedMethod) {
                throw new LocalizedException(
                    __("(%1) - Matched_erp_payment_method_not_found", $magentoPaymentMethod)
                );
            }

            return $mappedMethod;
        } catch (LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * get Magento payment method code using erp payment method
     * @param string $erpPaymentMethod
     * @return string
     * @throws LocalizedException
     */
    public function getByErpCode($erpPaymentMethod)
    {
        try {
            $mappedData = $this->getMappingData($erpPaymentMethod, "Matched_magento_payment_method_not_found");
            $mappedMethod = "";

            $mappedInfo = json_decode($mappedData["mapped_data"]);
            $i = 0;
            foreach ($mappedInfo as $data) {
                if (strtolower(trim($data->erpMethod)) == strtolower(trim($erpPaymentMethod))) {
                    if (empty($i)) {
                        $mappedMethod = trim($data->ecommerceMethod);
                        $i++;
                    }

                    if (trim($data->isEcommerceDefault)) {
                        return trim($data->ecommerceMethod);
                    }
                }
            }

            if (!$mappedMethod) {
                throw new LocalizedException(
                    __("(%1) - Matched_magento_payment_method_not_found", $erpPaymentMethod)
                );
            }

            return $mappedMethod;
        } catch (LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }

    /**
     * get payment mapping ing erp payment method
     * @param $erpPaymentMethod
     * @param $msg
     * @return string
     */
    public function getMappingData($erpPaymentMethod, $msg)
    {
        try {
            $mappedData = $this->paymentMapping->create()->getCollection()
                ->setOrder("created_at", "DESC")->setOrder("id", "DESC")->getFirstItem()->getData();
            if (!empty($mappedData)) {
                return $mappedData;
            } else {
                throw new LocalizedException(
                    __("(%1) - $msg", $erpPaymentMethod)
                );
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        }
    }
}
