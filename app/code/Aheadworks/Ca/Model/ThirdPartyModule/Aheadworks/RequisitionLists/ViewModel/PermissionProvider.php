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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\ViewModel;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class PermissionProvider
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\ViewModel
 */
class PermissionProvider implements ArgumentInterface
{
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @param RequisitionListPermission $listPermission
     */
    public function __construct(
        RequisitionListPermission $listPermission
    ) {
        $this->listPermission = $listPermission;
    }

    /**
     * Check whether list is editable
     *
     * @return bool
     */
    public function isEditable()
    {
        return $this->listPermission->isEditable();
    }
}
