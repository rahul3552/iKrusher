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
namespace Aheadworks\CreditLimit\Ui\Component\Form\Customer;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\ComponentVisibilityInterface;
use Magento\Ui\Component\Form\Fieldset;

/**
 * Class CreditLimitFieldset
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Form\Customer
 */
class CreditLimitFieldset extends Fieldset implements ComponentVisibilityInterface
{
    /**
     * @param ContextInterface $context
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        array $components = [],
        array $data = []
    ) {
        $this->context = $context;

        parent::__construct($context, $components, $data);
    }

    /**
     * Render component in case customer is modified and hide for new customer
     *
     * @return boolean
     */
    public function isComponentVisible(): bool
    {
        $customerId = $this->context->getRequestParam('id');
        return (bool)$customerId;
    }
}
