<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

/**
 * Class for reading custom xml
 */
class ReadCustomXml
{
    public $idAttributes;
    public $messageQueueHelper;

    /**
     *
     * @param \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper
     */
    public function __construct(
        \I95DevConnect\MessageQueue\Helper\Data $messageQueueHelper
    ) {
        $this->messageQueueHelper = $messageQueueHelper;
    }
    
    /**
     * Gets entity code list by sort order
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getXmlDataOrderBySortOrder()
    {
        try {
            $entityList = $this->getEntityList();
            $entityCodeList=[];
            foreach ($entityList as $entityKey => $entityVal) {
                $entityCodeList[$entityKey]['id'] = $entityKey;
                $entityCodeList[$entityKey]['title'] = $entityVal;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        
        return $entityCodeList;
    }
    
    /**
     * Gets entity code list by sync order
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getXmlDataOrderBySyncOrder()
    {
        try {
            $entityList = $this->messageQueueHelper->getEntityTypeListBySyncOrder();
            $entityCodeList=[];
            foreach ($entityList as $entityKey => $entityVal) {
                $entityCodeList[$entityKey]['id'] = $entityKey;
                $entityCodeList[$entityKey]['title'] = $entityVal;
            }
        } catch (\Magento\Framework\Exception\LocalizedException $ex) {
            $message = $ex->getMessage();
            throw new \Magento\Framework\Exception\LocalizedException(__($message));
        }
        
        return $entityCodeList;
    }
    
    /**
     * Get entity list array
     *
     * @return array
     */
    public function getEntityList()
    {
        return $this->messageQueueHelper->getEntityTypeList();
    }
}
