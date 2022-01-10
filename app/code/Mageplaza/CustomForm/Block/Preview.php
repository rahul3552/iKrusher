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

namespace Mageplaza\CustomForm\Block;

use Mageplaza\CustomForm\Model\Form as CustomFormModel;

/**
 * Class Preview
 * @package Mageplaza\CustomForm\Block
 */
class Preview extends CustomForm
{
    /**
     * @var string
     */
    protected $_template = 'Mageplaza_CustomForm::preview.phtml';

    /**
     * @return CustomFormModel
     */
    public function loadCustomForm()
    {
        $data = $this->request->getParam('form');
        /** @var CustomFormModel $customForm */
        $customForm = $this->customFormFactory->create();
        $customForm->setData($data);
        $customForm->setCustomerGroupIds(['0']);

        return $customForm;
    }
}
