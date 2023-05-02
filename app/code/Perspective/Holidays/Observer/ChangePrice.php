<?php
namespace Perspective\Holidays\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\App\Http\Context as AuthContext;

class ChangePrice implements \Magento\Framework\Event\ObserverInterface
{
    protected $_helper;
    private $authContext;

    public function __construct(
        AuthContext $authContext)
    {
        $this->authContext = $authContext;
    }
    public function execute(Observer $observer)
    {
        $isLoggedIn = $this->authContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        if ($isLoggedIn) {
//            if ($this->_helper->getGeneralConfig("discount_enable")) {
//                $is_cron_enable = $this->_helper->isCronEnable();
//                if ($is_cron_enable) {
//                    $discount = (float)$this->_helper->getGeneralConfig("discount_text");
                    $product = $observer->getProduct();
                    $price = $product->getData('final_price');
                    $new_price = $price * ((100 - 10) / 100);
                    $product->setFinalPrice($new_price);
                }
            }
//        }
//    }
}
