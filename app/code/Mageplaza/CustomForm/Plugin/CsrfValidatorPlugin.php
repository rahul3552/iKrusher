<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_CustomForm
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\CustomForm\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\Console\Request;
use Magento\Framework\App\Request\CsrfValidator;
use Magento\Framework\App\RequestInterface;

/**
 * Class CsrfValidatorPlugin
 * @package Mageplaza\CustomForm\Plugin
 */
class CsrfValidatorPlugin
{
    /**
     * @var Request
     */
    protected $request;

    /**
     * CsrfValidatorPlugin constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param CsrfValidator $subject
     * @param RequestInterface $request
     * @param ActionInterface $action
     *
     * @return array
     */
    public function beforeValidate(
        CsrfValidator $subject,
        RequestInterface $request,
        ActionInterface $action
    ) {
        if ($request->getFullActionName() === 'mpcustomform_preview_index') {
            $request = $this->request;
        }

        return [$request, $action];
    }
}
