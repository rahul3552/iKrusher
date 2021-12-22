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
namespace Aheadworks\Ctq\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Owner
 * @package Aheadworks\Ctq\Model\Source\Comment
 */
class Owner implements OptionSourceInterface
{
    /**#@+
     * Constants defined for RMA status types
     */
    const SELLER = 'seller';
    const BUYER = 'buyer';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::SELLER, 'label' => __('Seller')],
            ['value' => self::BUYER, 'label' => __('Buyer')]
        ];
    }
}
