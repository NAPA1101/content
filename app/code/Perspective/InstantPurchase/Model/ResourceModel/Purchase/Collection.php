<?php
namespace Perspective\InstantPurchase\Model\ResourceModel\Purchase;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';
    protected $_eventPrefix =
        'perspective_instant_purchase_collection';
    protected $_eventObject = 'purchase_collection';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Perspective\InstantPurchase\Model\Purchase',
            'Perspective\InstantPurchase\Model\ResourceModel\Purchase');
    }
}
