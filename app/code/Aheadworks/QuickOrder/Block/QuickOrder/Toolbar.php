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
 * @package    QuickOrder
 * @version    1.0.3
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\QuickOrder\Block\QuickOrder;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Aheadworks\QuickOrder\Model\Toolbar\Layout\LayoutProcessorInterface;

/**
 * Class Toolbar
 *
 * @package Aheadworks\QuickOrder\Block\QuickOrder
 */
class Toolbar extends Template
{
    /**
     * @var LayoutProcessorInterface[]
     */
    private $layoutProcessors;

    /**
     * @param Context $context
     * @param array $layoutProcessors
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $layoutProcessors = [],
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->layoutProcessors = $layoutProcessors;
        $this->jsLayout = isset($data['jsLayout']) && is_array($data['jsLayout'])
            ? $data['jsLayout']
            : [];
    }

    /**
     * Prepare JS layout of block
     */
    public function getJsLayout()
    {
        foreach ($this->layoutProcessors as $processor) {
            if (!$processor instanceof LayoutProcessorInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Layout processor does not implement required interface: %s.',
                        LayoutProcessorInterface::class
                    )
                );
            }
            $this->jsLayout = $processor->process($this->jsLayout);
        }

        return \Zend_Json::encode($this->jsLayout);
    }
}
