<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_CloudConnect
 */

namespace I95DevConnect\CloudConnect\Model\Config\Source;

/**
 * Class to get I95Dev Cloud Options
 */
class CrmErp implements \Magento\Framework\Option\ArrayInterface
{

    const CLOUD = 0;

    /**
     * CRM or ERP Values for Configuration
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        try {
            return [
                [
                    'value' => \I95DevConnect\CloudConnect\Model\Config\Source\CrmErp::CLOUD,
                    'label' => __('I95Dev Cloud')
                ]
            ];
        } catch (\Magento\Framework\Exception\LocalizedException $exc) {
            throw new \Magento\Framework\Exception\LocalizedException(__($exc->getMessage()));
        }
    }
}
