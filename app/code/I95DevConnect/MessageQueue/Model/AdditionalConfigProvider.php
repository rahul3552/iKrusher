<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_MessageQueue
 */

namespace I95DevConnect\MessageQueue\Model;

/**
 * Class responsible for providing store configurations
 */
class AdditionalConfigProvider implements \Magento\Checkout\Model\ConfigProviderInterface
{
    public $storeManager;
    public $currencyfactory;

    /**
     *
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyfactory
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyfactory
    ) {
        $this->storeManager = $storeManager;
        $this->currencyfactory = $currencyfactory;
    }

    /**
     * returns store configurations
     *
     * @return array
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getConfig()
    {
        $code = $this->storeManager->getStore()->getCurrentCurrency()->getCode();
        $symbol = $this->currencyfactory->create()->load($code);
        $output["currencySymbol"] =$symbol->getCurrencySymbol();
        $output["xcvcv"] = "test";
        return $output;
    }
}
