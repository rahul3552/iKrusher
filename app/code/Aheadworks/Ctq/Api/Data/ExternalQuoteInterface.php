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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Api\Data;

/**
 * Interface ExternalQuoteInterface
 * @api
 */
interface ExternalQuoteInterface extends QuoteInterface
{
    /**
     * Get cart
     *
     * @return \Aheadworks\Ctq\Api\Data\ExternalQuoteCartInterface
     */
    public function getCart();

    /**
     * Set cart
     *
     * @param \Aheadworks\Ctq\Api\Data\ExternalQuoteCartInterface $cart
     * @return $this
     */
    public function setCart($cart);
}
