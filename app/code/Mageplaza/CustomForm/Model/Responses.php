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

namespace Mageplaza\CustomForm\Model;

use Magento\Framework\Model\AbstractModel;
use Mageplaza\CustomForm\Model\ResourceModel\Responses as ResponsesResource;

/**
 * Class Responses
 * @package Mageplaza\CustomForm\Model
 * @method getFormId()
 * @method getCreatedAt()
 * @method getFormData()
 * @method setAdminNof(int $int)
 * @method getCustomerId()
 * @method setIsComplete(int $int)
 */
class Responses extends AbstractModel
{
    /**
     * Cache tag
     *
     * @var string
     */
    const CACHE_TAG = 'mageplaza_custom_form_responses';

    /**
     * Cache tag
     *
     * @var string
     */
    protected $_cacheTag = 'mageplaza_custom_form_responses';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'mageplaza_custom_form_responses';

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResponsesResource::class);
    }
}
