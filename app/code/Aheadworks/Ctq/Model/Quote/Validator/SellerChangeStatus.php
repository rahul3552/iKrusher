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
namespace Aheadworks\Ctq\Model\Quote\Validator;

use Aheadworks\Ctq\Api\Data\QuoteInterface;
use Aheadworks\Ctq\Model\Quote\Status\RestrictionsPool;
use Aheadworks\Ctq\Model\Quote\ValidatorInterface;
use Magento\Framework\Validator\AbstractValidator;

/**
 * Class SellerChangeStatus
 * @package Aheadworks\Ctq\Model\Quote\Validator
 */
class SellerChangeStatus extends AbstractValidator implements ValidatorInterface
{
    /**
     * @var RestrictionsPool
     */
    private $statusRestrictionsPool;

    /**
     * @param RestrictionsPool $statusRestrictionsPool
     */
    public function __construct(RestrictionsPool $statusRestrictionsPool)
    {
        $this->statusRestrictionsPool = $statusRestrictionsPool;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($quote)
    {
        $oldStatus = $quote->getOrigData(QuoteInterface::STATUS);
        $newStatus = $quote->getStatus();

        if ($oldStatus != $newStatus) {
            $statusRestrictions = $this->statusRestrictionsPool->getRestrictions($oldStatus);
            if (!in_array($newStatus, $statusRestrictions->getNextAvailableStatuses())) {
                $this->_addMessages(['You can\'t change status.']);
            }
        }

        return empty($this->getMessages());
    }
}
