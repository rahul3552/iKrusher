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
namespace Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute;

use Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\ModifierInterface;
use Aheadworks\OneStepCheckout\Model\Config;
use Magento\Customer\Helper\Address as AddressHelper;

/**
 * Class VatId
 * @package Aheadworks\OneStepCheckout\Model\Address\Form\AttributeMeta\Modifier\Attribute
 */
class VatId implements ModifierInterface
{
    /**
     * @var AddressHelper
     */
    private $addressHelper;

    /**
     * @var Config
     */
    private $config;

    /**
     * @param AddressHelper $addressHelper
     * @param Config $config
     */
    public function __construct(
        AddressHelper $addressHelper,
        Config $config
    ) {
        $this->addressHelper = $addressHelper;
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function modify($metadata, $addressType)
    {
        $formConfig = $this->config->getAddressFormConfig($addressType);
        if (!isset($formConfig['attributes']['vat_id'])) {
            $metadata['visible'] = $this->addressHelper->isVatAttributeVisible();
        }
        return $metadata;
    }
}
