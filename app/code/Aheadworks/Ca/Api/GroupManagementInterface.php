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
 * Interface GroupManagementInterface
 * @api
 */
interface GroupManagementInterface
{
    /**
     * Create default group for company
     *
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function createDefaultGroup();

    /**
     * Save group for company
     *
     * @param \Aheadworks\Ca\Api\Data\GroupInterface $group
     * @return \Aheadworks\Ca\Api\Data\GroupInterface
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function saveGroup($group);
}
