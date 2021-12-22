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
 * @package    Ca
 * @version    1.4.0
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\Ca\Block\Customer;

use Aheadworks\Ca\Model\Url;
use Magento\Framework\View\Element\Template;

/**
 * Class CreateCompanyButton
 * @package Aheadworks\Ca\Block\Customer
 */
class CreateCompanyButton extends Template
{
    /**
     * @var Url
     */
    private $url;

    /**
     * @inheritDoc
     */
    public function __construct(
        Template\Context $context,
        Url $url,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->url = $url;
    }

    /**
     * Return company create page url
     *
     * @return string
     */
    public function getCreatePageUrl()
    {
        return $this->url->getFrontendCreateCompanyFormUrl();
    }
}
