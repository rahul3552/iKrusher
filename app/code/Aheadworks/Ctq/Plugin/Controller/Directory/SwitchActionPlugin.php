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
 * @package    Ctq
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ctq\Plugin\Controller\Directory;

use Aheadworks\Ctq\Model\Directory\Currency\SwitchProcessor;
use Magento\Framework\App\ActionInterface;

/**
 * Class SwitchActionPlugin
 * @package Aheadworks\Ctq\Plugin\Controller\Directory
 */
class SwitchActionPlugin
{
    /**
     * @var SwitchProcessor
     */
    private $switchProcessor;

    /**
     * @param SwitchProcessor $switchProcessor
     */
    public function __construct(
        SwitchProcessor $switchProcessor
    ) {
        $this->switchProcessor = $switchProcessor;
    }

    /**
     * Workaround solution for quote list currency
     *
     * @param ActionInterface $subject
     * @return void
     */
    public function afterExecute(ActionInterface $subject)
    {
        $this->switchProcessor->switchQuoteListCurrency();
    }
}
