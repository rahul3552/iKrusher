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
namespace Aheadworks\Ctq\Api;

/**
 * Interface QuoteExpirationManagementInterface
 * @api
 */
interface QuoteExpirationManagementInterface
{
    /**
     * Check expiration period and mark quotes as expired
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processExpiredQuotes();

    /**
     * Process expiration reminder
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function processExpirationReminder();
}
