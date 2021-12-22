<?php
namespace Vueai\ProductRecommendations\Model\ResourceModel\Signup;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define model & resource model
     */
    protected function _construct()
    {
        $this->_init(
            \Vueai\ProductRecommendations\Model\Signup::class,
            \Vueai\ProductRecommendations\Model\ResourceModel\Signup::class
        );
    }
}
