<?php
namespace Vueai\ProductRecommendations\Model\ResourceModel;

class Signup extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Define main table
     */
    protected function _construct()
    {
        $this->_init(
            \Vueai\ProductRecommendations\Setup\InstallSchema::TABLE_NAME,
            \Vueai\ProductRecommendations\Setup\InstallSchema::PRIMARY_KEY
        );
    }
}
