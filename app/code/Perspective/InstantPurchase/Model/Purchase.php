<?php
namespace Perspective\InstantPurchase\Model;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Purchase extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'perspective_instant_purchase';
    protected $_cacheTag = 'perspective_instant_purchase';
    protected $_eventPrefix = 'perspective_instant_purchase';
    protected function _construct()
    {
        $this->_init('Perspective\InstantPurchase\Model\ResourceModel\Purchase');
    }
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }
    public function getDefaultValues()
    {
        $values = [];
        return $values;
    }
}