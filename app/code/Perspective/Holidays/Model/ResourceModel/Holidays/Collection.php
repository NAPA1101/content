<?php
namespace Perspective\Holidays\Model\ResourceModel\Holidays;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
class Collection extends AbstractCollection
{
    protected $_idFieldName = 'entity_id';
    protected $_eventPrefix =
        'perspective_holidays_holidays_collection';
    protected $_eventObject = 'holidays_collection';
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Perspective\Holidays\Model\Holidays',
            'Perspective\Holidays\Model\ResourceModel\Holidays');
    }
}
