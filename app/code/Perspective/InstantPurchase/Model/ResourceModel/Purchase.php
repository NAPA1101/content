<?php
namespace Perspective\InstantPurchase\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;

class Purchase extends AbstractDb
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
        $this->_init('instant_purchase', 'id');
    }
}
