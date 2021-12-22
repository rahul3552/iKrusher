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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\ViewModel\Order\Column;

use Aheadworks\Ca\Api\Data\CompanyUserInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Framework\App\Http\Context as HttpContext;

/**
 * Class CreatedBy
 * @package Aheadworks\Ca\ViewModel\Order\Column
 */
class CreatedBy implements ArgumentInterface
{
    /**
     * @var HttpContext
     */
    private $httpContext;

    /**
     * @param HttpContext $httpContext
     */
    public function __construct(
        HttpContext $httpContext
    ) {
        $this->httpContext = $httpContext;
    }

    /**
     * Retrieve customer name from order
     *
     * @param OrderInterface $order
     * @return string
     */
    public function getCreatedBy($order)
    {
        return $order->getCustomerFirstname() . ' ' . $order->getCustomerLastname();
    }

    /**
     * Check if need to add column
     *
     * @return bool
     */
    public function needToAddColumn()
    {
        $companyInfo = $this->httpContext->getValue('company_info');
        return (bool)$companyInfo[CompanyUserInterface::COMPANY_ID];
    }
}
