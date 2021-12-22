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
namespace Aheadworks\OneStepCheckout\Model;

use Aheadworks\OneStepCheckout\Api\Data\CheckoutSectionInformationInterface;
use Magento\Framework\Api\AbstractSimpleObject;

/**
 * Class CheckoutSectionInformation
 * @package Aheadworks\OneStepCheckout\Model
 */
class CheckoutSectionInformation extends AbstractSimpleObject implements CheckoutSectionInformationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getCode()
    {
        return $this->_get(self::CODE);
    }

    /**
     * {@inheritdoc}
     */
    public function setCode($code)
    {
        return $this->setData(self::CODE, $code);
    }
}
