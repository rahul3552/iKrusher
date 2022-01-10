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
 * @package     Mageplaza_ProductAttachments
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\ProductAttachments\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime\DateTime;

/**
 * Class Log
 * @package Mageplaza\ProductAttachments\Model\ResourceModel
 */
class Log extends AbstractDb
{
    /**
     * @var DateTime
     */
    protected $date;

    /**
     * Log constructor.
     *
     * @param DateTime $date
     * @param Context $context
     * @param null $connectionName
     */
    public function __construct(
        DateTime $date,
        Context $context,
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
        $this->_init('mageplaza_productattachments_log', 'log_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return Log
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if (empty($object->getCreatedAt())) {
            $object->setCreatedAt($this->date->date());
        }

        return parent::_beforeSave($object);
    }
}
