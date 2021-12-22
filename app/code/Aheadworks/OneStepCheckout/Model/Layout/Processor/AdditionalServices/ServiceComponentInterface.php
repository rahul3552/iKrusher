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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices;

/**
 * Interface ServiceComponentInterface
 * @package Aheadworks\OneStepCheckout\Model\Layout\Processor\AdditionalServices
 */
interface ServiceComponentInterface
{
    /**
     * Check if service component enabled
     *
     * @return bool
     */
    public function isEnabled();

    /**
     * Add config to js layout
     *
     * @param array $jsLayout
     * @return void
     */
    public function configure(&$jsLayout);
}
