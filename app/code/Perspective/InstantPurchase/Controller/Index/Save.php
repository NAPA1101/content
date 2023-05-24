<?php
//namespace Perspective\InstantPurchase\Controller\Index;
//
//use Magento\Catalog\Api\ProductRepositoryInterface;
//use Magento\Customer\Api\CustomerRepositoryInterface;
//use Magento\Customer\Model\CustomerFactory;
//use Magento\Customer\Model\Session;
//use Magento\Framework\App\Action\Context;
//use Magento\Framework\View\Result\PageFactory;
//use Magento\Quote\Model\QuoteFactory;
//use Magento\Quote\Model\QuoteManagement;
//use Magento\Sales\Api\OrderManagementInterface;
//use Magento\Sales\Api\OrderRepositoryInterface;
//use Magento\Sales\Model\Order\Email\Sender\OrderSender;
//use Magento\Store\Model\StoreManagerInterface;
//use Perspective\InstantPurchase\Model\PurchaseFactory;
//
//class Save extends \Magento\Framework\App\Action\Action
//{
//
//protected $resultPageFactory;
//protected $orderRepository;
//protected $storeManager;
//protected $customerFactory;
//protected $productRepository;
//protected $customerRepository;
//protected $quote;
//protected $quoteManagement;
//protected $orderSender;
//protected $orderManInterface;
//protected $customerSession;
//
//    public function __construct(
//        PageFactory                 $resultPageFactory,
//        OrderRepositoryInterface    $orderRepository,
//        StoreManagerInterface       $storeManager,
//        CustomerFactory             $customerFactory,
//        ProductRepositoryInterface  $productRepository,
//        CustomerRepositoryInterface $customerRepository,
//        QuoteFactory                $quote,
//        QuoteManagement             $quoteManagement,
//        OrderSender                 $orderSender,
//        OrderManagementInterface    $orderManInterface,
//        Session                     $customerSession,
//        Context                     $context,
//        PurchaseFactory             $purchase
//    )
//    {
//        $this->resultPageFactory = $resultPageFactory;
//        $this->orderRepository = $orderRepository;
//        $this->storeManager = $storeManager;
//        $this->customerFactory = $customerFactory;
//        $this->productRepository = $productRepository;
//        $this->customerRepository = $customerRepository;
//        $this->quote = $quote;
//        $this->quoteManagement = $quoteManagement;
//        $this->orderSender = $orderSender;
//        $this->orderManInterface = $orderManInterface;
//        $this->customerSession = $customerSession;
//        $this->purchase = $purchase;
//        parent::__construct($context);
//    }
//
//    public function execute()
//    {
//        $data = $this->getRequest()->getParams();
//        $productItem = $this->createItem($data);
//        $post = $data;
//        $orderInfo = [
//            'email' => $data['email_customer'], //customer email id
//            'currency_id' => 'USD',
//            'address' => [
//                'firstname' => $data['name_customer'],
//                'lastname' => 'OneClick',
//                'prefix' => '',
//                'suffix' => '',
//                'street' => 'Test Street',
//                'city' => 'Miami',
//                'country_id' => 'US',
//                'region' => 'Florida',
//                'region_id' => '18', // State region id
//                'postcode' => '98651',
//                'telephone' => $data['telephone_customer'],
//                'fax' => '1234567890',
//                'save_in_address_book' => 1
//            ],
//            'items' => [$productItem],
//        ];
//
//        $store = $this->storeManager->getStore();
//        $storeId = $store->getStoreId();
//        $websiteId = $this->storeManager->getStore()->getWebsiteId();
//        $customer = $this->customerFactory->create()
//            ->setWebsiteId($websiteId)
//            ->loadByEmail($orderInfo['email']); // Customer email address
//        if (!$customer->getId()) {
//            /**
//             * If Guest customer, Create new customer
//             */
//            $customer->setStore($store)
//                ->setFirstname($orderInfo['address']['firstname'])
//                ->setLastname($orderInfo['address']['lastname'])
//                ->setEmail($orderInfo['email'])
//                ->setPassword('admin@123');
//            $customer->save();
//        }
//        $quote = $this->quote->create(); //Quote Object
//        $quote->setStore($store); //set store for our quote
//
//        /**
//         * Registered Customer
//         */
//        $customer = $this->customerRepository->getById($customer->getId());
//        $quote->setCurrency();
//        $quote->assignCustomer($customer); //Assign Quote to Customer
//
//        //Add Items in Quote Object
//        foreach ($orderInfo['items'] as $item) {
//            $product = $this->productRepository->getById($item['product_id']);
//            if (!empty($item['super_attribute'])) {
//                /**
//                 * Configurable Product
//                 */
//                $buyRequest = new \Magento\Framework\DataObject($item);
//                $quote->addProduct($product, $buyRequest);
//            } else {
//                /**
//                 * Simple Product
//                 */
//                $quote->addProduct($product, intval($item['qty']));
//            }
//        }
//
//        //Billing & Shipping Address to Quote
//        $quote->getBillingAddress()->addData($orderInfo['address']);
//        $quote->getShippingAddress()->addData($orderInfo['address']);
//
//        // Set Shipping Method
//        $shippingAddress = $quote->getShippingAddress();
//        $shippingAddress->setCollectShippingRates(true)
//            ->collectShippingRates()
//            ->setShippingMethod('freeshipping_freeshipping'); //shipping method code, Make sure free shipping method is enabled
//        $quote->setPaymentMethod('checkmo'); //Payment Method Code, Make sure checkmo payment method is enabled
//        $quote->setInventoryProcessed(false);
//        $quote->save();
//        $quote->getPayment()->importData(['method' => 'checkmo']);
//
//        try {
//            // Collect Quote Totals & Save
//            $quote->collectTotals()->save();
//
//            // Create Order From Quote Object
//            $order = $this->quoteManagement->submit($quote);
//
//            $this->messageManager->addSuccess(__('Your order is completed . Thank you!'));
//        } catch (\Magento\Framework\Exception\LocalizedException $e) {
//            $this->messageManager->addError($e->getMessage());
//        } catch (\RuntimeException $e) {
//            $this->messageManager->addError($e->getMessage());
//        } catch (\Exception $e) {
//            $this->messageManager->addException($e, __('Something went wrong . '));
//        }
//
//        $purchase = $this->purchase->create();
//        $purchase->setData($data);
//        if ($purchase->save()) {
//            $this->messageManager->addSuccessMessage(__('You saved review'));
//        } else {
//            $this->messageManager->addErrorMessage(__('Review was not saved.'));
//        }
//        $resultRedirect = $this->resultRedirectFactory->create();
//        $resultRedirect->setPath('purchase/index/index');
//        return $resultRedirect;
//    }
//
//    public function createItem($post)
//    {
//        $normalizedPostProd = [];
//        $super_attr = [];
//        $productItem = [];
//
//        foreach ($post['product'] as $data) {
//            $normalizedPostProd[$data['name']] = $data['value'];
//        }
//        $product = $this->productRepository->getById($normalizedPostProd['product']);
//        if ($product->getData("type_id") === 'configurable') {
//            $productItem['product_id'] = $normalizedPostProd['product'];
//            $productItem['qty'] = $normalizedPostProd['qty'];
//
//            foreach ($normalizedPostProd as $key => $value) {
//                $check = stripos($key, 'super_attribute');
//                if ($check !== false) {
//                    preg_match_all("/\d+/", $key, $matches);
//                    $super_attr[$matches[0][0]] = $value;
//                }
//            }
//            $productItem['super_attribute'] = $super_attr;
//            return $productItem;
//        }
//        $productItem['product_id'] = $normalizedPostProd['product'];
//        $productItem['qty'] = $normalizedPostProd['qty'];
//        return $productItem;
//    }
//}
namespace Perspective\InstantPurchase\Controller\Index;

use Magento\Framework\App\Action\Context;
use Perspective\InstantPurchase\Model\PurchaseFactory;

class Save extends \Magento\Framework\App\Action\Action
{

    protected $purchase;

    public function __construct(
        Context         $context,
        PurchaseFactory $purchase
    )
    {
        $this->purchase = $purchase;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $purchase = $this->purchase->create();
        $purchase->setData($data);
        if ($purchase->save()) {
            $this->messageManager->addSuccessMessage(__('You saved review'));
        } else {
            $this->messageManager->addErrorMessage(__('Review was not saved.'));
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('purchase/index/index');
        return $resultRedirect;
    }
}

