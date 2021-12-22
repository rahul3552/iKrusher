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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\CustomerData;

use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListPermission;
use Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model\RequisitionListProvider;
use Aheadworks\RequisitionLists\CustomerData\RequisitionList;

/**
 * Class RequisitionListPlugin
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Plugin\CustomerData
 */
class RequisitionListPlugin
{
    /**
     * @var RequisitionListPermission
     */
    private $listPermission;

    /**
     * @var RequisitionListProvider
     */
    private $listProvider;

    /**
     * @param RequisitionListPermission $listPermission
     * @param RequisitionListProvider $listProvider
     */
    public function __construct(
        RequisitionListPermission $listPermission,
        RequisitionListProvider $listProvider
    ) {
        $this->listPermission = $listPermission;
        $this->listProvider = $listProvider;
    }

    /**
     * Add other customers lists to list if user is administrator
     *
     * @param RequisitionList $subject
     * @param array $result
     * @return array
     */
    public function afterGetSectionData($subject, $result)
    {
        if ($this->listPermission->isCustomerHasRootPermissions()) {
            $companyLists = $this->listProvider->getCompanyLists();

            $preparedLists = [];
            foreach ($companyLists as $list) {
                $preparedLists[] = [
                    'list_id' => $list->getListId(),
                    'name' => $list->getName()
                ];
            }
            $result['lists'] = array_merge($result['lists'], $preparedLists);
        }

        return $result;
    }
}
