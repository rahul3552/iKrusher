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
namespace Aheadworks\Ctq\Block\Adminhtml\Quote\Edit;

use Magento\Framework\Phrase;

/**
 * Class NegotiationTabs
 *
 * @package Aheadworks\Ctq\Block\Adminhtml\Quote\Edit
 */
class NegotiationTabs extends AbstractEdit
{
    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Comments and History');
    }

    /**
     * Get changed tab text
     *
     * @return Phrase
     */
    public function getChangedText()
    {
        return __('The information in this tab has been changed.');
    }

    /**
     * Get error tab text
     *
     * @return Phrase
     */
    public function getErrorText()
    {
        return __('This tab contains invalid data. Please resolve this before saving.');
    }

    /**
     * Get loader tab text
     *
     * @return Phrase
     */
    public function getLoaderText()
    {
        return __('Loading...');
    }
}
