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
namespace Aheadworks\Ctq\Model\Quote\Expiration;

use Aheadworks\Ctq\Model\Config;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class Calculator
 *
 * @package Aheadworks\Ctq\Model\Quote\Expiration
 */
class Calculator
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Calculate expiration date
     *
     * @param int|null $storeId
     * @return null|string
     * @throws \Exception
     */
    public function calculateExpirationDate($storeId = null)
    {
        $expirationDate = null;
        $daysOffset = $this->config->getQuoteExpirationPeriodInDays($storeId);
        if ($daysOffset) {
            $today = new \DateTime('today', new \DateTimeZone('UTC'));
            $today->add(new \DateInterval('P' . $daysOffset . 'D'));
            $today->setTime(23, 59, 59);
            $expirationDate = $today->format(StdlibDateTime::DATETIME_PHP_FORMAT);
        }

        return $expirationDate;
    }
}
