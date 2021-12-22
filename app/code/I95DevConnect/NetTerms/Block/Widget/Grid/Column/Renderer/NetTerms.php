<?php

/**
 * @author i95Dev Team
 * @copyright Copyright (c) 2019 i95Dev(https://www.i95dev.com)
 * @package I95DevConnect_NetTerms
 */

namespace I95DevConnect\NetTerms\Block\Widget\Grid\Column\Renderer;

use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer;
use Magento\Customer\Model\GroupFactory;
use Magento\Framework\DataObject;

class NetTerms extends AbstractRenderer
{

    /**
     * @var NetTerms Factory
     */
    public $netTermsFactory;

    /**
     *
     * @param Context $context
     * @param GroupFactory $netTermsFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        GroupFactory $netTermsFactory,
        array $data = []
    ) {
        $this->netTermsFactory = $netTermsFactory;
        parent::__construct($context, $data);
    }

    /**
     * Renders grid column
     *
     * @param DataObject $row
     * @return mixed
     */
    public function _getValue(DataObject $row)
    {
        $obj = (array) $row;
        foreach ($obj as $data) {
            $customerGrpId = $data['id'];
        }

        $collection = $this->netTermsFactory->create()->load($customerGrpId);
        if ($collection->getNetTermsId() == '0') {
            return '';
        }

        return $collection->getNetTermsId();
    }
}
