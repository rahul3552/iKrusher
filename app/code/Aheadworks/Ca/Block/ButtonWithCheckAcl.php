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
namespace Aheadworks\Ca\Block;

use Aheadworks\Ca\Api\AuthorizationManagementInterface;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Framework\Url as FrontendUrl;

/**
 * Class ButtonWithCheckAcl
 *
 * @method string getLink()
 * @method string setLink($link)
 * @method string getAdditionalClasses()
 * @method string getLabel()
 * @package Aheadworks\Ca\Block
 */
class ButtonWithCheckAcl extends Template
{
    /**
     * @var string
     */
    protected $_template = 'Aheadworks_Ca::button_with_check_acl.phtml';

    /**
     * @var AuthorizationManagementInterface
     */
    private $authorizationManagement;

    /**
     * ButtonWithCheckAcl constructor.
     * @param Context $context
     * @param AuthorizationManagementInterface $authorizationManagement
     * @param array $data
     */
    public function __construct(
        Context $context,
        AuthorizationManagementInterface $authorizationManagement,
        FrontendUrl $urlBuilderFrontend,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->authorizationManagement = $authorizationManagement;
        $this->urlBuilderFrontend = $urlBuilderFrontend;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $path = $this->createPathFromLink($this->getLink());
        if (!$this->authorizationManagement->isAllowed($path)) {
            return '';
        }

        if (filter_var($this->getLink(), FILTER_VALIDATE_URL) === false) {
            
            $this->setLink($this->urlBuilderFrontend->getUrl($path));
        }

        return parent::_toHtml();
    }

    /**
     * Escape link and create path for acl
     * @param $link
     * @return string
     */
    private function createPathFromLink($link)
    {
        $path = trim(
        //phpcs:ignore Magento2.Functions.DiscouragedFunction
            parse_url($link, PHP_URL_PATH),
            '/'
        );
        $asArray = explode('/', $path);
        $path = implode('/', array_slice($asArray, 0, 3));

        return $path;
    }
}
