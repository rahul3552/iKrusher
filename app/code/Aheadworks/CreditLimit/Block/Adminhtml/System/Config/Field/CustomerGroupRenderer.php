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
 * @package    CreditLimit
 * @version    1.0.2
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\CreditLimit\Block\Adminhtml\System\Config\Field;

use Aheadworks\CreditLimit\Model\Source\Customer\Group as GroupSource;
use Magento\Framework\View\Element\Context;
use Magento\Framework\View\Element\Html\Select;

/**
 * Class CustomerGroupRenderer
 *
 * @package Aheadworks\CreditLimit\Block\Adminhtml\System\Config\Field
 */
class CustomerGroupRenderer extends Select
{
    /**
     * @var GroupSource
     */
    private $groupSource;

    /**
     * @param Context $context
     * @param GroupSource $groupSource
     * @param array $data
     */
    public function __construct(
        Context $context,
        GroupSource $groupSource,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupSource = $groupSource;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (!$this->getOptions()) {
            $this->setOptions($this->groupSource->toOptionArray());
        }

        return parent::_toHtml();
    }

    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value)
    {
        return $this->setName($value);
    }

    /**
     * Set html id for input element
     *
     * @param string $id
     * @return $this
     */
    public function setInputId($id)
    {
        return $this->setId($id);
    }
}
