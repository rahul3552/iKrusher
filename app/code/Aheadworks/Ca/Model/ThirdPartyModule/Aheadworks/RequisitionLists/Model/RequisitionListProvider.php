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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model;

use Aheadworks\Ca\Api\CompanyUserManagementInterface;
use Aheadworks\RequisitionLists\Api\Data\RequisitionListInterface;
use Aheadworks\RequisitionLists\Api\RequisitionListRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Class RequisitionListProvider
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\RequisitionLists\Model
 */
class RequisitionListProvider
{
    /**
     * @var array
     */
    private $listCache = [];

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @param RequestInterface $request
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        RequestInterface $request,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->request = $request;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Retrieve list id
     *
     * @return int
     */
    public function getListId()
    {
        return $this->request->getParam('list_id');
    }

    /**
     * Retrieve list
     *
     * @param int $listId
     * @return RequisitionListInterface
     */
    public function getList($listId = null)
    {
        if (!$listId) {
            $listId = $this->getListId();
        }
        if (!isset($this->listCache[$listId])) {
            $listRepository = $this->getRequisitionListRepository();
            $this->listCache[$listId] = $listRepository->get($listId);
        }

        return $this->listCache[$listId];
    }

    /**
     * Retrieve all other company customers lists
     *
     * @return RequisitionListInterface[]|null
     */
    public function getCompanyLists()
    {
        $repository = $this->getRequisitionListRepository();
        $currentCustomer = $this->companyUserManagement->getCurrentUser();
        if (!$repository
            || !$currentCustomer
        ) {
            return null;
        }

        $customerIds = $this->companyUserManagement->getChildUsersIds($currentCustomer->getId());

        return $repository
            ->getList(
                $this->searchCriteriaBuilder
                    ->addFilter('customer_id', array_diff($customerIds, [$currentCustomer->getId()]), 'in')
                    ->create()
            )->getItems();
    }

    /**
     * Get Requisition List Repository
     *
     * @return RequisitionListRepositoryInterface
     */
    private function getRequisitionListRepository()
    {
        return ObjectManager::getInstance()
            ->get(RequisitionListRepositoryInterface::class);
    }
}
