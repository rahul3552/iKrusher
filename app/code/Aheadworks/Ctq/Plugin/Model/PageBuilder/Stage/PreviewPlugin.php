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
namespace Aheadworks\Ctq\Plugin\Model\PageBuilder\Stage;

use Aheadworks\Ctq\Model\Email\Template\RenderState;
use Magento\PageBuilder\Model\Stage\Preview;

/**
 * Class PreviewPlugin
 * @package Aheadworks\Ctq\Plugin\Model\PageBuilder\Stage
 */
class PreviewPlugin
{
    /**
     * @var RenderState
     */
    private $renderState;

    /**
     * @param RenderState $renderState
     */
    public function __construct(
        RenderState $renderState
    ) {
        $this->renderState = $renderState;
    }

    /**
     * Fix magento bug, return bool value
     * for details see Magento\PageBuilder\Model\Stage\Preview::isPreviewMode()
     * method can returns not boolean value, as result fatal error occurs
     *
     * @param Preview $subject
     * @param \Closure $proceed
     * @return bool
     */
    public function aroundIsPreviewMode(
        $subject,
        \Closure $proceed
    ) {
        if ($this->renderState->isRendering()) {
            return false;
        }

        return $proceed();
    }
}
