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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Model\System\Config\Backend;

use Magento\Framework\App\Config\Value as ConfigValue;
use Magento\Customer\Model\GroupManagement;

/**
 * Class CustomerGroupList
 *
 * @package Aheadworks\QuickOrder\Model\System\Config\Backend
 */
class CustomerGroupList extends ConfigValue
{
    /**
     * Remove other options if All Groups option is included
     *
     * @throws \Exception
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        if (is_array($value) && in_array(GroupManagement::CUST_GROUP_ALL, $value)) {
            $value = [GroupManagement::CUST_GROUP_ALL];
            $this->setValue($value);
        }

        return parent::beforeSave();
    }
}
