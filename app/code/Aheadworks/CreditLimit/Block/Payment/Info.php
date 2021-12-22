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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Block\Payment;

use Magento\Payment\Block\Info as BaseInfo;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Info
 *
 * @package Aheadworks\CreditLimit\Block\Payment
 */
class Info extends BaseInfo
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_CreditLimit::payment/info.phtml';

    /**
     * Get purchase order number
     *
     * @return string|null
     * @throws LocalizedException
     */
    public function getPoNumber()
    {
        return $this->getInfo()->getPoNumber();
    }
}
