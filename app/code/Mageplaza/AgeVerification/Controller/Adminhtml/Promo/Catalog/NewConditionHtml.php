<?php
/**
 *
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mageplaza\AgeVerification\Controller\Adminhtml\Promo\Catalog;

use Magento\CatalogRule\Controller\Adminhtml\Promo\Catalog;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Rule\Model\Condition\AbstractCondition;
use Mageplaza\AgeVerification\Model\PurchaseCondition;

/**
 * Class NewConditionHtml
 * @package Mageplaza\AgeVerification\Controller\Adminhtml\Promo\Catalog
 */
class NewConditionHtml extends Catalog
{
    /**
     * @return ResponseInterface|ResultInterface|void
     */
    public function execute()
    {
        $proId = $this->getRequest()->getParam('id');
        $formName = $this->getRequest()->getParam('form_namespace');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = $this->_objectManager->create($type)
            ->setId($proId)
            ->setType($type)
            ->setRule($this->_objectManager->create(PurchaseCondition::class))
            ->setPrefix('purchase_conditions');

        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof AbstractCondition) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $model->setFormName($formName);
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }

        $this->getResponse()->setBody($html);
    }
}
