<?php
/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Observer\Reverse;

use I95DevConnect\MessageQueue\Helper\Data;
use I95DevConnect\NetTerms\Model\NetTermsFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Observer class for assigning netterms to customer
 */
class NetTermsObserver implements ObserverInterface
{

    /**
     * Helper
     *
     * @var \I95DevConnect\NetTerms\Helper\Data
     */
    public $netTermsHelper;

    /**
     *
     * @var Data
     */
    public $mqHelper;

    /**
     * @var NetTermsFactory
     */
    public $nettermsModel;

    /**
     *
     * @param \I95DevConnect\NetTerms\Helper\Data $netTermsHelper
     * @param Data $mqHelper
     * @param NetTermsFactory $nettermsModel
     */
    public function __construct(
        \I95DevConnect\NetTerms\Helper\Data $netTermsHelper,
        Data $mqHelper,
        NetTermsFactory $nettermsModel
    ) {
        $this->netTermsHelper = $netTermsHelper;
        $this->mqHelper = $mqHelper;
        $this->nettermsModel = $nettermsModel;
    }

    /**
     * Set netterms to customer
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $is_enabled = $this->netTermsHelper->isNetTermsEnabled();
        if (!$is_enabled) {
            return;
        }

        if ($this->mqHelper->getGlobalValue('i95_observer_skip')) {
            return;
        }

        $currentObj = $observer->getEvent()->getData("currentObject");
        $netTermsId = $currentObj->dataHelper->getValueFromArray("targetNetTermsId", $currentObj->stringData);
        if (isset($netTermsId) && $netTermsId != null) {
            $netTermsData = $this->nettermsModel->create()->getCollection()
                    ->addFieldtoFilter('target_net_terms_id', $netTermsId)
                    ->getData();
            /**
             * @updatedBy Debashis S Gopal. Setting custom attribute net_terms_id to customer object.
             */
            if (!empty($netTermsData)) {
                $currentObj->customerInterface->setCustomAttribute('net_terms_id', $netTermsId);
            } else {
                $message = "Netterms Not Found ::" . $netTermsId;
                throw new LocalizedException(__($message));
            }
        } else {
            $currentObj->customerInterface->setCustomAttribute('net_terms_id', null);
        }
    }
}
