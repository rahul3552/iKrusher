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
namespace Aheadworks\Ca\Model\ResourceModel;

use Aheadworks\Ca\Api\Data\RoleInterface;
use Magento\Framework\Model\AbstractModel;

/**
 * Class Role
 * @package Aheadworks\Ca\Model\ResourceModel
 */
class Role extends AbstractResourceModel
{
    const MAIN_TABLE_NAME = 'aw_ca_role';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->_init(self::MAIN_TABLE_NAME, RoleInterface::ID);
    }

    /**
     * Perform actions after object save
     *
     * @param AbstractModel|RoleInterface $object
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _afterSave(AbstractModel $object)
    {
        if ($object->isDefault()) {
            $this->getConnection()
                ->update(
                    $this->getMainTable(),
                    [RoleInterface::IS_DEFAULT => 0],
                    RoleInterface::COMPANY_ID . ' = ' . $object->getCompanyId()
                    . ' And '
                    . RoleInterface::ID . ' != ' . $object->getId()
                );
        }
        return parent::_afterSave($object);
    }
}
