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
namespace Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider;

use Magento\Checkout\Model\ConfigProviderInterface;

/**
 * Class Payment
 *
 * @package Aheadworks\Ctq\Model\Quote\Customer\ConfigProvider
 */
class Payment implements ConfigProviderInterface
{
    /**
     * @inheritdoc
     */
    public function getConfig()
    {
        $output['payment'] = [
            'klarna_kp' => [],
            'customerBalance' => [],
            'awStoreCredit' => []
        ];
        $output['awGiftcard'] = [];

        return $output;
    }
}
