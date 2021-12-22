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
use Magento\Framework\Stdlib\BooleanUtils;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class SetAsDefault
 * @package Aheadworks\Ca\Ui\DataProvider\Role\Form\Modifier
 */
class SetAsDefault implements ModifierInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @param BooleanUtils $booleanUtils
     */
    public function __construct(BooleanUtils $booleanUtils)
    {
        $this->booleanUtils = $booleanUtils;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $isDefault = isset($data[RoleInterface::IS_DEFAULT])
            ? $this->booleanUtils->toBoolean($data[RoleInterface::IS_DEFAULT])
            : false;
        $data['is_default_disabled'] = $isDefault;

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }
}
