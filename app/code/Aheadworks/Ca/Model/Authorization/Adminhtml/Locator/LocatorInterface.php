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
namespace Aheadworks\Ca\Model\Authorization\Adminhtml\Locator;

/**
 * Interface LocatorInterface
 *
 * @package Aheadworks\Ca\Model\Authorization\Adminhtml\Locator
 */
interface LocatorInterface
{
    /**
     * Get customer id depending on the page in backend you are on
     *
     * @return int|null
     */
    public function getCustomerId();
}
