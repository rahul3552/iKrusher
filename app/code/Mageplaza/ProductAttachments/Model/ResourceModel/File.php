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

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Mageplaza\ProductAttachments\Helper\Data;
use Mageplaza\ProductAttachments\Model\File as ModelFile;

/**
 * Class File
 * @package Mageplaza\ProductAttachments\Model\ResourceModel
 */
class File extends AbstractResource
{
    /**
     * File product table
     *
     * @var string
     */
    public $fileProductTable;

    /**
     * @var Data
     */
    protected $_helperData;

    /**
     * File constructor.
     *
     * @param Context $context
     * @param Data $helperData
     * @param null $connectionName
     */
    public function __construct(
        Context $context,
        Data $helperData,
        $connectionName = null
    ) {
        $this->_helperData = $helperData;

        parent::__construct($context, $connectionName);

        $this->fileProductTable = $this->getTable('mageplaza_productattachments_file_product');
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('mageplaza_productattachments_file', 'file_id');
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     * @throws LocalizedException
     */
    public function _beforeSave(AbstractModel $object)
    {
        if (is_array($object->getStoreIds())) {
            $object->setStoreIds(implode(',', $object->getStoreIds()));
        }
        if (is_array($object->getCustomerGroup())) {
            $object->setCustomerGroup(implode(',', $object->getCustomerGroup()));
        }

        if (is_array($object->getOrderStatuses())) {
            $object->setOrderStatuses(implode(',', $object->getOrderStatuses()));
        }

        $object->setName(
            $this->_helperData->generateFileName($this, $object, $object->getName())
        );

        return $this;
    }

    /**
     * @param AbstractModel $object
     *
     * @return $this
     */
    public function _afterSave(AbstractModel $object)
    {
        $this->saveProductRelation($object);

        return $this;
    }

    /**
     * @param ModelFile $file
     *
     * @return $this
     */
    public function saveProductRelation(ModelFile $file)
    {
        if ($file->getProductId()) {
            $id       = $file->getId();
            $entityId = $file->getProductId();
            $adapter  = $this->getConnection();
            $data     = [
                'entity_id' => (int) $entityId,
                'file_id'   => (int) $id,
            ];
            $adapter->insert($this->fileProductTable, $data);
        }

        return $this;
    }

    /**
     * @param array $data
     */
    public function updateData($data)
    {
        $where = ['file_id = ?' => (int) $data['value_id']];
        $this->getConnection()->update($this->getTable($this->_mainTable), [
            'label'          => $data['label'],
            'name'           => $data['name'],
            'status'         => $data['status'],
            'file_action'    => $data['file_action'],
            'store_ids'      => $data['store_ids'],
            'customer_group' => $data['customer_group'],
            'file_icon_path' => $data['file_icon_path'],
            'priority'       => $data['priority'],
            'is_buyer'       => $data['is_buyer'],
            'order_statuses' => $data['order_statuses'],
            'customer_login' => $data['customer_login'],
            'group'          => $data['group'],
            'position'       => $data['position']
        ], $where);
    }

    /**
     * @param string $fileName
     *
     * @return string
     * @throws LocalizedException
     */
    public function isDuplicateFileName($fileName)
    {
        $adapter = $this->getConnection();
        $select  = $adapter->select()
            ->from($this->getMainTable(), 'file_id')
            ->where('name = :name');
        $binds   = ['name' => $fileName];

        return $adapter->fetchOne($select, $binds);
    }
}
