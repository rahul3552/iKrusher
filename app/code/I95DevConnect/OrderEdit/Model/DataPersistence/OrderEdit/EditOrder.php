<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_OrderEdit
 */
namespace I95DevConnect\OrderEdit\Model\DataPersistence\OrderEdit;

use \I95DevConnect\MessageQueue\Api\LoggerInterface;
use I95DevConnect\MessageQueue\Helper\Data;
use \Magento\Store\Model\ScopeInterface;

/**
 * Edit order class check Extension is enabled or not
 */
class EditOrder
{
    const TARGET_ORDER_EDIT_STATUS = 'targetOrderEditStatus';
    /**
     * @var \I95DevConnect\CancelOrder\Model\DataPersistence\OrderEdit\Edit
     */
    public $edit;

    /**
     * @var \I95DevConnect\CancelOrder\Model\DataPersistence\OrderEdit\Update
     */
    public $update;

    /**
     *
     * @var \I95DevConnect\OrderEdit\Model\DataPersistence\Validate
     */
    public $editOrderValidator;
    /**
     *
     * @var All required fields all ERP must send
     */
    public $requiredFields = [
        'targetId'=>'edit_order_002',
        self::TARGET_ORDER_EDIT_STATUS =>'edit_order_003'
    ];

    /**
     *
     * @param \I95DevConnect\OrderEdit\Model\DataPersistence\OrderEdit\Edit $edit
     * @param \I95DevConnect\OrderEdit\Model\DataPersistence\OrderEdit\Update $update
     * @param \I95DevConnect\OrderEdit\Model\DataPersistence\Validate $editOrderValidator
     */
    public function __construct(
        \I95DevConnect\OrderEdit\Model\DataPersistence\OrderEdit\Edit $edit,
        \I95DevConnect\OrderEdit\Model\DataPersistence\OrderEdit\Update $update,
        \I95DevConnect\OrderEdit\Model\DataPersistence\Validate $editOrderValidator
    ) {
        $this->edit = $edit;
        $this->update = $update;
        $this->editOrderValidator = $editOrderValidator;
    }

    /**
     * Check order edit status and update the order
     * @param array $stringData
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function edit($stringData)
    {
        try {
            $this->editOrderValidator->validateData($stringData, $this->requiredFields);
            if ($stringData[self::TARGET_ORDER_EDIT_STATUS] === 'edited') {
                return $this->edit->editOrder($stringData);
            } elseif ($stringData[self::TARGET_ORDER_EDIT_STATUS] === 'updated') {
                return $this->update->updateOrder($stringData);
            } else {
                throw new \Magento\Framework\Exception\LocalizedException(__('edit_order_007'));
            }
        } catch (\Exception $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
