<?php
/**
 * @author Arushi Bansal
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_ShippingMapping
 */

namespace I95DevConnect\ShippingMapping\Model;

/**
 * Shipping mapping management class
 */
class ShippingMethodMagento implements \I95DevConnect\ShippingMapping\Api\ShippingMethodMagentoInterface
{

    /**
     * ShippingMethodMagento constructor.
     * @param \Magento\Shipping\Model\Config $shipconfig
     */
    public function __construct(
        \Magento\Shipping\Model\Config $shipconfig
    ) {

        $this->shipconfig = $shipconfig;
    }

    /**
     * get available shipping method
     * @return array|bool
     */
    public function availableShippingMethod()
    {
        $activeCarriers = $this->shipconfig->getActiveCarriers();

        $codeList = [];
        foreach ($activeCarriers as $carrierCode => $carrierModel) {
            if ($carrierMethods = $carrierModel->getAllowedMethods()) {
                foreach ($carrierMethods as $methodCode => $method) {
                    $code = $carrierCode . '_' . $methodCode;
                    $codeList[] = $code;
                }
            }
        }

        return $codeList;
    }
}
