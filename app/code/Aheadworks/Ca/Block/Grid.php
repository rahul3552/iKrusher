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

use Aheadworks\Ca\ViewModel\ListViewModelInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\Template;

/**
 * Class Grid
 * @package Aheadworks\Ca\Block
 */
abstract class Grid extends Template
{
    /**
     * {@inheritdoc}
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        /** @var Pager $pager */
        $pager = $this->getLayout()->createBlock(
            Pager::class,
            $this->getPagerName()
        );

        $this->getListViewModel()
            ->getSearchCriteriaBuilder()
            ->setCurrentPage($pager->getCurrentPage())
            ->setPageSize($pager->getLimit());

        $historySearchResults = $this->getListViewModel()->getSearchResults();
        if ($historySearchResults) {
            $pager->setSearchResults($historySearchResults);
            $this->setChild('pager', $pager);
        }

        return $this;
    }

    /**
     * Retrieve button html
     *
     * @param string $blockName
     * @param string $link
     * @return string
     */
    public function renderButton(string $blockName, string $link)
    {
        /** @var ButtonWithCheckAcl $block */
        try {
            $block = $this->getLayout()->getBlock($blockName);
            $block->setLink($link);
            $html = $block->toHtml();
        } catch (LocalizedException $e) {
            $html = '';
        }

        return $html;
    }

    /**
     * Retrieve pager name
     *
     * @return string
     */
    abstract protected function getPagerName();

    /**
     * Retrieve list view model
     *
     * @return ListViewModelInterface
     */
    abstract protected function getListViewModel();
}
