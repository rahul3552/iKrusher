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
namespace Aheadworks\Ca\Plugin\Block\Adminhtml\Order\View;

use Aheadworks\Ca\Api\SellerCompanyManagementInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Block\Adminhtml\Order\View\Info;

/**
 * Class InfoPlugin
 * @package Aheadworks\Ca\Plugin\Block\Adminhtml\Order\View
 */
class InfoPlugin
{
    /**
     * @var SellerCompanyManagementInterface
     */
    private $sellerCompanyService;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param SellerCompanyManagementInterface $sellerCompanyService
     * @param Escaper $escaper
     */
    public function __construct(
        SellerCompanyManagementInterface $sellerCompanyService,
        Escaper $escaper
    ) {
        $this->sellerCompanyService = $sellerCompanyService;
        $this->escaper = $escaper;
    }

    /**
     * Set Company Legal Name to customer account data if isset
     *
     * @param Info $block
     * @param array $result
     * @return array
     */
    public function afterGetCustomerAccountData(
        Info $block,
        $result
    ) {
        try {
            $order = $block->getOrder();
            $customerId = $order->getCustomerId();
            $company = $this->sellerCompanyService->getCompanyByCustomerId($customerId);
        } catch (LocalizedException $exception) {
            $company = null;
        }

        if ($company && $company->getLegalName()) {
            $result = array_merge($result, [[
                'label' => __('Company Legal Name'),
                'value' => $this->escaper->escapeHtml($company->getLegalName(), ['br']),
            ]]);
        }

        return $result;
    }
}
