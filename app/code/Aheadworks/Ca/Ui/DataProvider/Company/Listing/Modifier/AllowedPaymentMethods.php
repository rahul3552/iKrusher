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
namespace Aheadworks\Ca\Ui\DataProvider\Company\Listing\Modifier;

use Aheadworks\Ca\Model\ThirdPartyModule\Manager as ThirdPartyModuleManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AllowedPaymentMethods
 *
 * @package Aheadworks\Ca\Ui\DataProvider\Company\Listing\Modifier
 */
class AllowedPaymentMethods implements ModifierInterface
{
    /**
     * @var ThirdPartyModuleManager
     */
    private $thirdPartyModuleManager;

    /**
     * @var OptionSourceInterface
     */
    private $optionSource;

    /**
     * @param ThirdPartyModuleManager $thirdPartyModuleManager
     * @param OptionSourceInterface $optionSource
     */
    public function __construct(
        ThirdPartyModuleManager $thirdPartyModuleManager,
        OptionSourceInterface $optionSource
    ) {
        $this->thirdPartyModuleManager = $thirdPartyModuleManager;
        $this->optionSource = $optionSource;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        if ($this->thirdPartyModuleManager->isAwPayRestModuleEnabled()) {
            $meta = $this->addAllowedPaymentMethodsColumn($meta);
        }
        return $meta;
    }

    protected function addAllowedPaymentMethodsColumn($meta)
    {
        $allowedPaymentMethodsColumnMetadata = [
            'aw_ca_company_columns' => [
                'children' => [
                    'allowed_payment_methods' => [
                        'children' => [],
                        'attributes' => [
                            'name' => 'allowed_payment_methods',
                            'component' => 'Magento_Ui/js/grid/columns/column',
                        ],
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => 'select',
                                    'component' => 'Magento_Ui/js/grid/columns/select',
                                    'componentType' => 'column',
                                    'filter' => 'select',
                                    'label' => __('Allowed Payment Methods'),
                                    'sortable' => false,
                                    'sortOrder' => '90',
                                    'options' => $this->optionSource->toOptionArray(),
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $meta = array_merge_recursive($meta, $allowedPaymentMethodsColumnMetadata);
        return $meta;
    }
}
