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
namespace Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder;

/**
 * Class EmailVariables
 *
 * @package Aheadworks\Ctq\Model\Source\Quote\ExpirationReminder
 */
class EmailVariables
{
    /**#@+
     * Expiration reminder email variables
     */
    const QUOTE = 'quote';
    const QUOTE_URL = 'quote_url';
    const CUSTOMER_NAME = 'customer_name';
    const DAYS_NUMBER_UNTIL_EXPIRED = 'days_number_until_expired';
    /**#@-*/
}
