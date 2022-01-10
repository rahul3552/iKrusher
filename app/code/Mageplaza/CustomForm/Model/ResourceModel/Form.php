<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Mageplaza\CustomForm\Helper\Data;

/**
 * Class Form
 * @package Mageplaza\CustomForm\Model\ResourceModel
 */
class Form extends AbstractDb
{
    /**
     * @var DateTime
     */
    private $date;

    /**
     * Form constructor.
     *
     * @param Context $context
     * @param DateTime $date
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        DateTime $date,
        $connectionName = null
    ) {
        $this->date = $date;

        parent::__construct($context, $connectionName);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_custom_form_form', 'id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _afterLoad(AbstractModel $object)
    {
        if (!($emailPlaning = $object->getData('email_planing'))) {
            $emailPlaning = '';
        }
        $object->setData('email_planing', Data::jsonDecode($emailPlaning));
        $customGroupIds = $object->getData('customer_group_ids');
        if (is_string($customGroupIds)) {
            $object->setData('customer_group_ids', explode(',', $customGroupIds));
        }
        $storeIds = $object->getData('store_ids');
        if (is_string($storeIds)) {
            $object->setData('store_ids', explode(',', $storeIds));
        }

        return parent::_afterLoad($object);
    }

    /**
     * @param AbstractModel $object
     *
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $customGroupIds = $object->getData('customer_group_ids');
        if (is_array($customGroupIds)) {
            $object->setData('customer_group_ids', implode(',', $customGroupIds));
        }
        $storeIds = $object->getData('store_ids');
        if (is_array($storeIds)) {
            $object->setData('store_ids', implode(',', $storeIds));
        }
        if ($object->isObjectNew()) {
            $object->setData('created_at', $this->date->date());
        }
        $customForm = $object->getData('custom_form');
        if (is_array($customForm)) {
            $object->setData('custom_form', Data::jsonEncode($customForm));
        }
        $emailPlaning = $object->getData('email_planing');
        if (is_array($emailPlaning)) {
            $object->setData('email_planing', Data::jsonEncode($emailPlaning));
        }
        $object->setData('updated_at', $this->date->date());

        return parent::_beforeSave($object);
    }
}
