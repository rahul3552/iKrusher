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
namespace Aheadworks\Ca\Controller\User;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Aheadworks\Ca\Api\RoleRepositoryInterface;
use Aheadworks\Ca\Controller\AbstractCustomerAction;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Session as CustomerSession;

/**
 * Class AbstractUserAction
 * @package Aheadworks\Ca\Controller\User
 */
abstract class AbstractUserAction extends AbstractCustomerAction
{
    /**
     * @var RoleRepositoryInterface
     */
    private $customerRepository;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context, $customerSession);
        $this->customerRepository = $customerRepository;
    }

    /**
     * Retrieve role
     *
     * @return CustomerInterface
     * @throws NotFoundException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getEntity()
    {
        try {
            $id = $this->getEntityIdByRequest();
            $entity = $this->customerRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            throw new NotFoundException(__('Page not found.'));
        }

        return $entity;
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function isEntityBelongsToCustomer()
    {
        if (!$this->isForwardAction(['create'])) {
            $companyUser = $this->getEntity();
            $companyId = $companyUser->getExtensionAttributes()->getAwCaCompanyUser()->getCompanyId();

            if ($this->getCurrentCompanyId() != $companyId) {
                return false;
            }
        }

        return true;
    }
}
