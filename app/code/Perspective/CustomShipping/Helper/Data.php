<?php
namespace Perspective\CustomShipping\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{

    protected $scopeConfig;
    const XML_PATH_CARRIERS = 'carriers/customshipping/';


    /**
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getConfigValue(string $field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function getGeneralConfig(string $code, $storeId = null)
    {
        return $this->getConfigValue(
            self::XML_PATH_CARRIERS . $code, $storeId
        );
    }
};
