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
namespace Aheadworks\Ctq\ViewModel\QuoteList;

use Aheadworks\Ctq\Model\QuoteList\Permission\Checker as PermissionChecker;
use Aheadworks\Ctq\Model\Request\Checker as RequestChecker;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class AddButton
 * @package Aheadworks\Ctq\ViewModel\QuoteList
 */
class AddButton implements ArgumentInterface
{
    /**
     * @var PermissionChecker
     */
    private $permissionChecker;
    
    /**
     * @var RequestChecker
     */
    private $requestChecker;

    /**
     * @param PermissionChecker $permissionChecker
     * @param RequestChecker $requestChecker
     */
    public function __construct(
        PermissionChecker $permissionChecker,
        RequestChecker $requestChecker
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->requestChecker = $requestChecker;
    }

    /**
     * Check is allowed
     *
     * @return bool
     */
    public function isAllowed()
    {
        return $this->permissionChecker->isAllowed();
    }

    /**
     * Check is add action allowed
     *
     * @return bool
     */
    public function isAllowedToAdd()
    {
        return $this->isAllowed()
            && !$this->requestChecker->isConfigureAction();
    }
}
