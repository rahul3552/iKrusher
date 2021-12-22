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

use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Customer\Model\Session as CustomerSession;
use Aheadworks\Ca\Api\RoleRepositoryInterface;

/**
 * Class Create
 * @package Aheadworks\Ca\Controller\Role
 */
class Create extends AbstractRoleAction
{
    /**
     * @var ForwardFactory
     */
    private $resultForwardFactory;

    /**
     * @param Context $context
     * @param CustomerSession $customerSession
     * @param RoleRepositoryInterface $roleRepository
     * @param ForwardFactory $resultForwardFactory
     */
    public function __construct(
        Context $context,
        CustomerSession $customerSession,
        RoleRepositoryInterface $roleRepository,
        ForwardFactory $resultForwardFactory
    ) {
        parent::__construct($context, $customerSession, $roleRepository);
        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Forward to edit
     *
     * @return Forward
     */
    public function execute()
    {
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
