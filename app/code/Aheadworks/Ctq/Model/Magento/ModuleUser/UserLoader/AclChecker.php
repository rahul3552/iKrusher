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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Model\Magento\ModuleUser\UserLoader;

use Magento\Framework\Authorization\PolicyInterface;
use Magento\User\Api\Data\UserInterface;

/**
 * Class AclChecker
 *
 * @package Aheadworks\Ctq\Model\Magento\ModuleUser\UserLoader
 */
class AclChecker
{
    /**
     * @var PolicyInterface
     */
    private $policy;

    /**
     * @param PolicyInterface $policy
     */
    public function __construct(
        PolicyInterface $policy
    ) {
        $this->policy = $policy;
    }

    /**
     * Check if admin user is allowed to view specified resource
     *
     * @param UserInterface $user
     * @param string $resourceId
     * @return bool
     */
    public function isAllowed($user, $resourceId)
    {
        $roleId = $user->getRole()->getRoleId();
        return $this->policy->isAllowed($roleId, $resourceId);
    }
}
