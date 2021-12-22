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
namespace Aheadworks\OneStepCheckout\Api\Data;

/**
 * Interface CheckoutSectionInformationInterface
 * @package Aheadworks\OneStepCheckout\Api\Data
 */
interface CheckoutSectionInformationInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const CODE = 'code';
    /**#@-*/

    /**
     * Get section code
     *
     * @return string
     */
    public function getCode();

    /**
     * Set section code
     *
     * @param string $code
     * @return $this
     */
    public function setCode($code);
}
