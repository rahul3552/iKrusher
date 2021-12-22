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
namespace Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Aheadworks\Ca\Api\CompanyUserManagementInterface;

/**
 * Class CommentManagementPlugin
 *
 * @package Aheadworks\Ca\Model\ThirdPartyModule\Aheadworks\Ctq\Plugin
 */
class CommentManagementPlugin
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @var CompanyUserManagementInterface
     */
    private $companyUserManagement;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param CompanyUserManagementInterface $companyUserManagement
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement,
        CompanyUserManagementInterface $companyUserManagement
    ) {
        $this->authorizationManagement = $authorizationManagement;
        $this->companyUserManagement = $companyUserManagement;
    }

    /**
     * Change customer to current company user
     *
     * @param \Aheadworks\Ctq\Api\CommentManagementInterface $subject
     * @param \Aheadworks\Ctq\Api\Data\CommentInterface $comment
     * @return array
     */
    public function beforeAddComment($subject, $comment)
    {
        $currentUser = $this->companyUserManagement->getCurrentUser();
        if ($currentUser
            && $this->authorizationManagement->isAllowedByResource('Aheadworks_Ctq::company_quotes_view')
        ) {
            $comment->setOwnerId($currentUser->getId());
        }

        return [$comment];
    }
}
