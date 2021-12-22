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
namespace Aheadworks\Ca\Api;

/**
 * Interface AuthorizationManagementInterface
 * @api Aheadworks\Ca\Api
 */
interface AuthorizationManagementInterface
{
    /**
     * Check current user permission by path
     *
     * @param string $path
     * @return boolean
     */
    public function isAllowed($path);

    /**
     * Check current user permission by resource
     *
     * @param string $resource
     * @return boolean
     */
    public function isAllowedByResource($resource);
}
