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
namespace Aheadworks\Ca\Controller\Role;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Controller\AbstractCustomerAction;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class AbstractRoleAction
 * @package Aheadworks\Ca\Controller\Role
 */
abstract class AbstractRoleAction extends AbstractCustomerAction
{
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param RoleRepositoryInterface $roleRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RoleRepositoryInterface $roleRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->roleRepository = $roleRepository;
    }

    /**
     * Retrieve role
     *
     * @return RoleInterface
     * @throws NotFoundException
     */
    protected function getEntity()
    {
        try {
            $id = $this->getEntityIdByRequest();
            $entity = $this->roleRepository->get($id);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     */
    protected function isEntityBelongsToCustomer()
    {
        if (!$this->isForwardAction(['create'])) {
            $role = $this->getEntity();

            if ($this->getCurrentCompanyId() != $role->getCompanyId()) {
                return false;
            }
        }

        return true;
    }
}
