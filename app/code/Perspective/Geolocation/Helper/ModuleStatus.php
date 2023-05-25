<?php
namespace Perspective\Geolocation\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;
class ModuleStatus extends AbstractHelper
{

    const XML_PATH_SECOND = 'GeolocationTabSection/';

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
    public function getModuleApi($code, $storeId = null)
    {
        return $this->getConfigValue(self::XML_PATH_SECOND .'GeolocationTabGroup/'. $code, $storeId);
    }
}
