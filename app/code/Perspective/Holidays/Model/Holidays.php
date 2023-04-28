<?php
namespace Perspective\Holidays\Model;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\DataObject\IdentityInterface;

class Holidays extends AbstractModel implements IdentityInterface
{
    const CACHE_TAG = 'perspective_holidays_holidays';
    protected $_cacheTag = 'perspective_holidays_holidays';
    protected $_eventPrefix = 'perspective_holidays_holidays';
    protected function _construct()
    {
        $this->_init
        ('Perspective\Holidays\Model\ResourceModel\Holidays');
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
