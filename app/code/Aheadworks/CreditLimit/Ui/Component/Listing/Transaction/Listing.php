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
namespace Aheadworks\CreditLimit\Ui\Component\Listing\Transaction;

use Magento\Ui\Component\Listing as ListingComponent;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentInterface;

/**
 * Class Listing
 *
 * @package Aheadworks\CreditLimit\Ui\Component\Listing\Transaction
 */
class Listing extends ListingComponent
{
    /**
     * @var array
     */
    protected $componentNamesToModify = [];

    /**
     * @param ContextInterface $context
     * @param array $componentNamesToModify
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        array $componentNamesToModify = [],
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $components, $data);
        $this->componentNamesToModify = $componentNamesToModify;
    }

    /**
     * @inheritdoc
     *
     * @throws /Exception
     */
    public function prepare()
    {
        if (!empty($this->componentNamesToModify)) {
            $this->modifyComponents($this);
        }
        parent::prepare();
    }

    /**
     * Modify components according to the list
     *
     * @param UiComponentInterface $component
     * @return $this
     */
    private function modifyComponents(UiComponentInterface $component)
    {
        $childComponents = $component->getChildComponents();
        if (!empty($childComponents)) {
            foreach ($childComponents as $child) {
                $this->modifyComponents($child);
            }
        }

        if (isset($this->componentNamesToModify[$component->getName()])) {
            $config = $component->getData('config');
            $config = array_merge($config, $this->componentNamesToModify[$component->getName()]);
            $component->setData('config', $config);
        }

        return $this;
    }
}
