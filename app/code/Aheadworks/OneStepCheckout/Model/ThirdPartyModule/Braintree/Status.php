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
namespace Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree;

use Magento\Framework\App\ProductMetadataInterface;

/**
 * Class Status
 *
 * @package Aheadworks\OneStepCheckout\Model\ThirdPartyModule\Braintree
 */
class Status implements StatusInterface
{
    /**
     * @var ProductMetadataInterface
     */
    private $productMetadata;

    /**
     * @param ProductMetadataInterface $productMetadata
     */
    public function __construct(
        ProductMetadataInterface $productMetadata
    ) {
        $this->productMetadata = $productMetadata;
    }

    /**
     * @inheritdoc
     */
    public function isPayPalInContextMode()
    {
        $magentoVersion = $this->productMetadata->getVersion();
        return version_compare($magentoVersion, '2.3.3', '>=');
    }
}
