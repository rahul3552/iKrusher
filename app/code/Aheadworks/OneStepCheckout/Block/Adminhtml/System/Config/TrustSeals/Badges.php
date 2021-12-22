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
 * @package    OneStepCheckout
 * @version    1.7.5
 * @copyright  Copyright (c) 2020 Aheadworks Inc. (http://www.aheadworks.com)
 * @license    https://ecommerce.aheadworks.com/end-user-license-agreement/
 */
namespace Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\TrustSeals;

use Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\TrustSeals\Renderer\Badge;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\DataObject;

/**
 * Class Badges
 * @package Aheadworks\OneStepCheckout\Block\Adminhtml\System\Config\TrustSeals
 */
class Badges extends AbstractFieldArray
{
    /**
     * @var Badge
     */
    private $badgeRenderer;

    /**
     * {@inheritdoc}
     */
    protected $_template = 'system/config/trust_seals/field_array.phtml';

    /**
     * {@inheritdoc}
     */
    protected function _prepareToRender()
    {
        $this->addColumn(
            'script',
            [
                'label' => __('Trust Seal'),
                'renderer' => $this->getBadgeRenderer()
            ]
        );
        $this->_addAfter = false;
    }

    /**
     * Get badge renderer
     *
     * @return Badge
     */
    private function getBadgeRenderer()
    {
        if (!$this->badgeRenderer) {
            $this->badgeRenderer = $this->getLayout()->createBlock(
                Badge::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
        }
        return $this->badgeRenderer;
    }

    /**
     * Get default row data
     *
     * @return array
     */
    public function getDefaultRowData()
    {
        $result = [];
        $columns = $this->getColumns();
        foreach (array_keys($columns) as $columnName) {
            $result[$columnName] = '';
        }
        return $result;
    }

    /**
     * Get rows
     *
     * @return array
     */
    public function getRows()
    {
        $rows = [];
        foreach ($this->getArrayRows() as $row) {
            $rows[] = $row->getData();
        }
        return $rows;
    }
}
