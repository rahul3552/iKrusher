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

namespace Mageplaza\CustomForm\Model;

use DateTime;
use Magento\Framework\DataObject;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\Store;
use Mageplaza\CustomForm\Model\ResourceModel\Form as FormResource;

/**
 * Class Form
 * @package Mageplaza\CustomForm\Model
 * @method getName()
 * @method getIdentifier()
 * @method getCustomForm()
 * @method getStatus()
 * @method getValidFromDate()
 * @method getValidToDate()
 * @method getStoreIds()
 * @method getCustomerGroupIds()
 * @method getFormStyle()
 * @method getFbButtonText()
 * @method getPopupType()
 * @method getLastResponsesUpdate()
 * @method getResponsesSummary()
 * @method setResponsesSummary(string $responsesSummary)
 * @method setLastResponsesUpdate(DateTime $param)
 * @method getAutoResEnabled()
 * @method getAutoResSender()
 * @method getAdminNofSender()
 * @method getAdminNofTemplate()
 * @method getAdminNofEnabled()
 * @method getAdminNofSendTo()
 * @method getAdminNofCcToEmail()
 * @method getAdminNofBccToEmail()
 * @method getAdminNofAttachedFiles()
 * @method getAutoResAttachedFiles()
 * @method getAutoResTemplate()
 * @method setAdminNofSender($sender)
 * @method setAdminNofTemplate($emailTemplate)
 * @method getActionAfterSubmit()
 * @method getPageUrl()
 * @method getCmsPage()
 * @method getEmailPlaning()
 * @method setAdminNofEnabled($adminNofEnabled)
 * @method setAdminNofSendTo($adminNofSendTo)
 * @method setAdminNofCcToEmail($adminNofCcToEmail)
 * @method setAdminNofBccToEmail($adminNofBccToEmail)
 * @method setAdminNofAttachedFiles($adminNofBccToEmail)
 * @method setAutoResAttachedFiles($customerNofAttachedFiles)
 * @method setAutoResEnabled($customerNofEnabled)
 * @method setAutoResSender($customerNofSender)
 * @method setAutoResTemplate($customerNofEmailTemplate)
 * @method getCustomCss()
 */
class Form extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_custom_form_form';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_custom_form_form';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_custom_form_form';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(FormResource::class);
    }

    /**
     * @param array $storeIds
     * @param string $identifier
     *
     * @return bool
     */
    public function getIsUniqueFormToStores($storeIds, $identifier)
    {
        $conditions = [];

        foreach ($storeIds as $storeId) {
            $conditions[] = ['finset' => $storeId];
        }

        $customForms = $this->getCollection()->addFieldToFilter('id', ['neq' => $this->getId()])
            ->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('store_ids', $conditions);

        if (!$customForms->getSize()) {
            return true;
        }

        return false;
    }

    /**
     * @param string $identifier
     * @param int $storeId
     *
     * @return DataObject
     */
    public function getFilterByStoreId($identifier, $storeId)
    {
        $customForms = $this->getCollection()
            ->addFieldToFilter('identifier', $identifier)
            ->addFieldToFilter('store_ids', ['finset' => $storeId]);

        if (!$customForms->getSize()) {
            $customForms = $this->getCollection()
                ->addFieldToFilter('identifier', $identifier)
                ->addFieldToFilter('store_ids', ['finset' => Store::DEFAULT_STORE_ID]);
        }

        return $customForms->getFirstItem();
    }
}
