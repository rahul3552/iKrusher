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
namespace Aheadworks\Ctq\Block\History\Action;

use Aheadworks\Ctq\Api\Data\HistoryActionInterface;
use Aheadworks\Ctq\Api\Data\HistoryInterface;
use Aheadworks\Ctq\Model\Source\History\Action\Type;
use Aheadworks\Ctq\ViewModel\Customer\Quote;
use Magento\Framework\View\Element\Template;

/**
 * Class DefaultRenderer
 * @package Aheadworks\Ctq\Block\History\Action
 * @method HistoryActionInterface getAction()
 * @method DefaultRenderer setAction(HistoryActionInterface $action)
 * @method HistoryInterface getHistory()
 * @method DefaultRenderer setHistory(HistoryInterface $history)
 * @method bool|null getIsEmailForSeller()
 * @method DefaultRenderer setIsEmailForSeller(bool $value)
 * @method Quote getQuoteViewModel()
 * @method \Aheadworks\Ctq\ViewModel\History\History getHistoryViewModel()
 */
class DefaultRenderer extends Template
{
    /**
     * Prepare value
     *
     * @param mixed $value
     * @return string
     */
    public function getPreparedValue($value)
    {
        $action = $this->getAction();
        switch ($action->getType()) {
            case Type::QUOTE_ATTRIBUTE_STATUS:
                $value = $this->getQuoteViewModel()->getStatusLabel($value);
                break;
            case Type::QUOTE_ATTRIBUTE_EXPIRATION_DATE:
            case Type::QUOTE_ATTRIBUTE_REMINDER_DATE:
                $value = $this->getQuoteViewModel()->getExpiredDateFormatted($value);
                break;
            case Type::QUOTE_ATTRIBUTE_BASE_TOTAL_NEGOTIATED:
            case Type::QUOTE_ATTRIBUTE_BASE_TOTAL:
                $value = $this->getQuoteViewModel()->getQuoteTotalFormatted($value);
                break;
        }

        return $value;
    }
}
