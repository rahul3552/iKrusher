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
namespace Aheadworks\Ca\Plugin\Model\ActionValidator;

use Magento\Customer\Model\Customer;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ActionValidator\RemoveAction;

/**
 * Class RemoveActionPlugin
 * @package Aheadworks\Ca\Plugin\Model\ActionValidator
 */
class RemoveActionPlugin
{
    /**
     * Modify area permission for delete customer
     *
     * @param RemoveAction $subject
     * @param $result
     * @param AbstractModel $model
     * @return bool
     */
    public function afterIsAllowed(
        RemoveAction $subject,
        $result,
        AbstractModel $model
    ) {
        $return = $result;

        if (!$return && $model instanceof Customer) {
            $return = true;
        }

        return $return;
    }
}
