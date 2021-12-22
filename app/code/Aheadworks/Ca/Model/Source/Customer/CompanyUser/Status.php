<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Model\Source\Customer\CompanyUser;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 *
 * @package Aheadworks\Ca\Model\Source\Customer\CompanyUser
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Status type list
     */
    const ACTIVE = 1;
    const INACTIVE = 0;
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::ACTIVE,
                'label' => __('Active')
            ],
            [
                'value' => self::INACTIVE,
                'label' => __('Inactive')
            ]
        ];
    }

    /**
     * Get option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $options = [];
        foreach ($this->toOptionArray() as $option) {
            $options[$option['value']] = $option['label'];
        }
        return $options;
    }

    /**
     * Get status label
     *
     * @param int $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        $label = '';
        $options = $this->toOptionArray();
        foreach ($options as $option) {
            if ($option['value'] == $status) {
                $label = $option['label'];
                break;
            }
        }

        return $label;
    }
}
