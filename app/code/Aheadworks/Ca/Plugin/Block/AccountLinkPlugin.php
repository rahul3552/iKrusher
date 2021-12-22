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
namespace Aheadworks\Ca\Plugin\Block;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Framework\View\Element\Html\Link\Current;

/**
 * Class AccountLinkPlugin
 * @package Aheadworks\Ca\Plugin\Block
 */
class AccountLinkPlugin
{
    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * @param AuthorizationManagementInterface $authorizationManagement
     */
    public function __construct(
        AuthorizationManagementInterface $authorizationManagement
    ) {
        $this->authorizationManagement = $authorizationManagement;
    }

    /**
     * @param Current $subject
     * @param \Closure $proceed
     * @return string
     */
    public function aroundToHtml($subject, $proceed)
    {
        $html = '';
        $path = $subject->getPath();
        if ($this->authorizationManagement->isAllowed($path)) {
            $html = $proceed();
        }
        return $html;
    }
}
