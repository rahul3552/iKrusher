<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Cron;

use Mageplaza\CustomForm\Model\ResourceModel\Form\Collection as FormCollection;

/**
 * Class AdminNotification
 * @package Mageplaza\CustomForm\Cron
 */
class AdminNotificationDaily extends AdminNotification
{
    /**
     * @param FormCollection $formCollection
     *
     * @return FormCollection
     */
    protected function addFrequencyFilter($formCollection)
    {
        return $formCollection->addFieldToFilter('admin_nof_send_time', 'daily');
    }
}
