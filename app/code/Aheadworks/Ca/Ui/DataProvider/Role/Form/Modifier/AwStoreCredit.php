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
namespace Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Model\ThirdPartyModule\Manager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Aheadworks\Ca\ViewModel\Role\Role as RoleViewModel;

/**
 * Class AwStoreCredit
 * @package Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier
 */
class AwStoreCredit implements ModifierInterface
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var RoleViewModel
     */
    private $roleViewModel;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @param ArrayManager $arrayManager
     * @param RoleViewModel $roleViewModel
     * @param Manager $moduleManager
     */
    public function __construct(
        ArrayManager $arrayManager,
        RoleViewModel $roleViewModel,
        Manager $moduleManager
    ) {
        $this->arrayManager = $arrayManager;
        $this->roleViewModel = $roleViewModel;
        $this->moduleManager = $moduleManager;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $baseAmountLimit = isset($data[RoleInterface::AW_STC_BASE_AMOUNT_LIMIT])
            ? $data[RoleInterface::AW_STC_BASE_AMOUNT_LIMIT]
            : 0;

        $data[RoleInterface::AW_STC_BASE_AMOUNT_LIMIT] = $this->roleViewModel->getRoundAmount($baseAmountLimit);

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $awStcFieldsetPath = $this->arrayManager->findPath('aw_stc_fieldset', $meta);
        if ($awStcFieldsetPath) {
            $awStcFieldsetConfig['visible'] = $this->moduleManager->isAwStoreCreditModuleEnabled();
            $meta = $this->arrayManager->merge($awStcFieldsetPath, $meta, $awStcFieldsetConfig);
        }

        return $meta;
    }
}
