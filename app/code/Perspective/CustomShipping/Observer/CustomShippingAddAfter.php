<?php
namespace Perspective\CustomShipping\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\ObjectManagerInterface;


class CustomShippingAddAfter implements ObserverInterface
{
    /**
     * @var CustomerSession
     */
    protected $_customerSession;
    private CheckoutSession $_checkoutSession;

    /**
     * @param CustomerSession $customerSession
     * @param CheckoutSession $checkoutSession
     * @param ObjectManagerInterface $objectmanager
     */

    public function __construct(
        CustomerSession $customerSession,
        CheckoutSession $checkoutSession,
        ObjectManagerInterface $objectmanager,
    ) {
        $this->_customerSession = $customerSession;
        $this->_checkoutSession = $checkoutSession;
        $this->_objectManager = $objectmanager;
    }

    public function execute(Observer $observer)
    {
        $shippingMethod = $this->_checkoutSession->getQuote()->getShippingAddress()->getShippingMethod();
        if ($shippingMethod == 'customshipping_customshipping')
        {
            $result = $observer->getEvent()->getResult();
            $result->setData('is_available', false);

        }
    }
}
