<?php
namespace Perspective\Holidays\Model\ResourceModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
class Holidays extends AbstractDb
{
    public function __construct(
        Context
        $context
    )
    {
        parent::__construct($context);
    }
    protected function _construct()
    {
        $this->_init('holidays_table', 'entity_id');
    }
}
