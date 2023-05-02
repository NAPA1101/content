<?php

namespace Perspective\Holidays\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    const XML_PATH_SECOND = 'holidays_section/';

    /**
     * @param $field
     * @param $storeId
     * @return mixed
     */
    public function getConfigValue($field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field, ScopeInterface::SCOPE_STORE, $storeId
        );
    }

    /**
     * @param $code
     * @param $storeId
     * @return mixed
     */
    public function getHolidayModule($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECOND .'general/'. $code, $storeId);
    }

}
