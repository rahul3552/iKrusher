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
namespace Aheadworks\OneStepCheckout\Api;

/**
 * Interface GuestDataFieldCompletenessLoggerInterface
 * @package Aheadworks\OneStepCheckout\Api
 */
interface GuestDataFieldCompletenessLoggerInterface
{
    /**
     * Log guest checkout fields completeness data
     *
     * @param string $cartId
     * @param \Aheadworks\OneStepCheckout\Api\Data\DataFieldCompletenessInterface[] $fieldCompleteness
     * @return void
     */
    public function log($cartId, array $fieldCompleteness);
}
