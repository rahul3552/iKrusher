<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace I95DevConnect\EgeClaue\Block\Cache;

use Magento\Backend\Block\Widget\Grid\Massaction\VisibilityCheckerInterface;
use Magento\Framework\App\State;

/**
 * Class checks that action can be displayed on massaction list
 */
class ProductionModeVisibilityChecker implements VisibilityCheckerInterface
{

    /**
     * {@inheritdoc}
     */
    public function isVisible()
    {
        return true;
    }
}
