<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_PriceLevel
 */

namespace I95DevConnect\PriceLevel\Helper;

/**
 * Helper Class for Module
 */
class PriceLevel extends \Magento\Framework\App\Helper\AbstractHelper
{

    /**
     * scopeConfig for system Configuration
     *
     * @var string
     */
    public $scopeConfig;
    
    /**
     *
     * @var I95DevConnect\PriceLevel\Model\DataPersistence\PriceLevel
     */
    public $priceLevelCreate;

    /**
     * Class constructor to include all the dependencies
     *
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \I95DevConnect\PriceLevel\Model\DataPersistence\PriceLevel $priceLevelCreate
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \I95DevConnect\PriceLevel\Model\DataPersistence\PriceLevel $priceLevelCreate
    ) {
    
        $this->logger = $logger;
        $this->scopeConfig = $scopeConfig;
        $this->priceLevelCreate = $priceLevelCreate;
    }
    
    /**
     * Validate if the price level exists in Magento or not if not create a new price level
     *
     * @param type $erpPriceLevel
     * @return type
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function validatePricelevel($erpPriceLevel)
    {
        $priceLevelData = $this->priceLevelCreate->getPriceLevelData($erpPriceLevel);
        if (empty($priceLevelData)) {
            $priceLevelCreateData = ['targetId' => $erpPriceLevel, 'priceLevelDescription' => $erpPriceLevel];
            $result = $this->priceLevelCreate->create($priceLevelCreateData, '');
            if (!$result->resultData) {
                $this->logger->createLog(
                    __METHOD__,
                    "Error Occured while creating new Price Level",
                    LoggerInterface::I95EXC,
                    'critical'
                );
            } else {
                $priceLevelData = $this->priceLevelCreate->getPriceLevelData($erpPriceLevel);
            }
            
        }
        return $priceLevelData;
    }
}
