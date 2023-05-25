<?php

namespace Perspective\CustomerImage\Block\Customer;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\UrlInterface;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\View\Element\Template;
use Perspective\CustomerImage\Helper\Data;

class Account extends Template
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var SessionFactory
     */
    protected $_customerSession;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $customerModel;

    /**
     * @var Data
     */
    protected $helper;

    /**
     * @var \Magento\Framework\View\Asset\Repository
     */
    protected $_assetRepo;

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param SessionFactory $customerSession
     * @param \Magento\Customer\Model\Session $session
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Magento\Framework\View\Asset\Repository $assetRepo
     * @param Data $helper
     * @param array $data
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        SessionFactory $customerSession,
        \Magento\Customer\Model\Session $session,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Customer\Model\Customer $customerModel,
        \Magento\Framework\View\Asset\Repository $assetRepo,
        Data $helper,

        array $data = []
    ){
        $this->urlBuilder  = $urlBuilder;
        $this->_customerSession = $customerSession;
        $this->customerSession = $customerSession->create();
        $this->storeManager = $storeManager;
        $this->httpContext = $httpContext;
        $this->customerModel = $customerModel;
        $this->session = $session;
        $this->helper = $helper;
        $this->assetRepo = $assetRepo;

        parent::__construct($context, $data);

        $collection = $this->getContracts();
        $this->setCollection($collection);
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getBaseUrl()
    {
        return $this->storeManager->getStore()->getBaseUrl();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getMediaUrl()
    {
        return $this->getBaseUrl() . 'pub/media/';
    }
    /**
    * @param $filePath
    * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerImageUrl($filePath)
    {
        return $this->getMediaUrl() . 'customer' . $filePath;
    }
    /**
     * @return false|string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getFileUrl()
    {
        if (!empty($customerData = $this->customerModel->load($this->customerSession->getId()))){
            $url = $customerData->getData('customer_image');
        }else{
            $url = $this->customerSession->getData('customer_image');
        }
        if (!empty($url)) {
            return $this->getCustomerImageUrl($url);
        }
        return false;
    }

    /**
     * @return bool
     */
    public function customerLoggedIn()
    {
        if ($this->customerSession->isLoggedIn()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * @return int|void
     */
    public function getImageSize() {
        if ($this->helper->getGeneralConfig('enable')) {
            $value = $this->helper->getGeneralConfig('imageSize');
            return $value == 1 ? 40 : 25;
        }
    }
}
