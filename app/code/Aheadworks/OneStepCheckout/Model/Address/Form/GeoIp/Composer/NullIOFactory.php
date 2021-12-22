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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Composer;

use Composer\IO\NullIO;

/**
 * Class NullIOFactory
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\GeoIp\Composer
 */
class NullIOFactory
{
    /**
     * Creates NullIO instance
     *
     * @return NullIO
     */
    public function create()
    {
        return new NullIO();
    }
}
