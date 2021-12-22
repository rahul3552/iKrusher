<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model\DataPersistence\Order\Order\Forward;

/**
 * Class responsible for preparing order comment data which will be added in order result to ERP
 */
class OrderComments
{
    /**
     *
     * @var \Magento\Sales\Api\OrderManagementInterface
     */
    public $orderManagement;
    
    /**
     *
     * @param \Magento\Sales\Api\OrderManagementInterface $orderManagement
     */
    public function __construct(
        \Magento\Sales\Api\OrderManagementInterface $orderManagement
    ) {
        $this->orderManagement = $orderManagement;
    }
    
    /**
     * Returns comment history from order
     * @param  \Magento\Sales\Api\Data\OrderInterface $orderId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return array
     * @author Debashis S. Gopal
     */
    public function getOrderComments($orderId)
    {
        try {
            $commentsHistory = [];
            $commentsHistoryData = $this->orderManagement->getCommentsList($orderId)->getData();
            if (!empty($commentsHistoryData)) {
                foreach ($commentsHistoryData as $comments) {
                    $orderComments = [];
                    $orderComments['comment'] = str_replace('"', '', $comments['comment']);
                    $orderComments['source'] = "admin";
                    $orderComments['createdDate'] = $comments['created_at'];
                    $commentsHistory[] = $orderComments;
                }
            }
            return $commentsHistory;
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            throw new \Magento\Framework\Exception\LocalizedException(__($ex->getMessage()));
        }
    }
}
