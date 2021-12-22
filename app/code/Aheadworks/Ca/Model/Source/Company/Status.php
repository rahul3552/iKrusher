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
namespace Aheadworks\Ca\Model\Source\Company;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 * @package Aheadworks\Ca\Model\Source\Company
 */
class Status implements OptionSourceInterface
{
    /**#@+
     * Company status list
     */
    const PENDING_APPROVAL = 'pending_approval';
    const APPROVED = 'approved';
    const BLOCKED = 'blocked';
    const DECLINED = 'declined';
    /**#@-*/

    /**
     * @inheritdoc
     */
    public function toOptionArray()
    {
        return [
            [
                'value' => self::PENDING_APPROVAL,
                'label' => __('Pending Approval')
            ],
            [
                'value' => self::APPROVED,
                'label' => __('Approved')
            ],
            [
                'value' => self::BLOCKED,
                'label' => __('Blocked')
            ],
            [
                'value' => self::DECLINED,
                'label' => __('Declined')
            ]
        ];
    }

    /**
     * Return true if status is valid
     * @param string $status
     * @return bool
     */
    public function isValidStatus($status)
    {
        foreach ($this->toOptionArray() as $option) {
            if ($option['value'] == $status) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get status label
     *
     * @param string $status
     * @return string
     */
    public function getStatusLabel($status)
    {
        $result = '';
        $statusOptions = $this->toOptionArray();
        foreach ($statusOptions as $option) {
            if ($option['value'] == $status) {
                $result = $option['label'];
                break;
            }
        }

        return $result;
    }
}
