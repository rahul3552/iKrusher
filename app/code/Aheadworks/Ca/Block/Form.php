<?php
/**
 * Aheadworks Inc.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://ecommerce.aheadworks.com/end-user-license-agreement/
 *
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Form
 *
 * @package Aheadworks\Ca\Block
 * @method \Aheadworks\Ca\ViewModel\Form getFormViewModel()
 */
class Form extends Template
{
    /**
     * {@inheritdoc}
     */
    protected $_template = 'Aheadworks_Ca::form.phtml';

    /**
     * @inheritdoc
     */
    public function getJsLayout()
    {
        $dataProvider = $this->getFormViewModel()->getDataProvider();
        $this->jsLayout = $dataProvider->modifyMeta($this->jsLayout);

        $id = $this->_request->getParam($dataProvider->getRequestFieldName(), null);
        $data = $dataProvider->getData();
        if ($id && isset($data[$id])) {
            $this->jsLayout['components'][$dataProvider->getName()]['data'] = $data[$id];
        }
        return json_encode($this->jsLayout);
    }
}
