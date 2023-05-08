<?php
namespace Perspective\CustomShipping\Model\Carrier;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Checkout\Model\Session;
use Perspective\CustomShipping\Helper\Data;

/**
 * Custom shipping model
 */
class Customshipping extends AbstractCarrier implements CarrierInterface
{
    /**
     * @var string
     */
    protected $_code = 'customshipping';

    /**
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    private $rateResultFactory;

    /**
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    private $rateMethodFactory;
    private Session $session;
    private array $data;
    private \Magento\Authorization\Model\UserContextInterface $userContext;
    private CustomerRepositoryInterface $customerRepository;
    private TimezoneInterface $timezone;

    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Magento\Authorization\Model\UserContextInterface $userContext,
        CustomerRepositoryInterface $customerRepository,
        TimezoneInterface $timezone,
        Session $session,
        Data $helper,
        array $data = []
    ) {
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);

        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->helper = $helper;
        $this->session = $session;
        $this->userContext = $userContext;
        $this->customerRepository = $customerRepository;
        $this->timezone = $timezone;
    }

    /**
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCategoryIds() {
        $quote = $this->session->getQuote();
        foreach ($quote->getAllVisibleItems() as $item) {
            $categories = $item->getProduct()->getCategoryIds();
        }
        return $categories;
    }
    /**
     * Custom Shipping Rates Collector
     *
     * @param RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        if ($this->getShowUser()) {
        /** @var \Magento\Shipping\Model\Rate\Result $result */
        $result = $this->rateResultFactory->create();

        /** @var \Magento\Quote\Model\Quote\Address\RateResult\Method $method */
        $method = $this->rateMethodFactory->create();

        $method->setCarrier($this->_code);
        $method->setCarrierTitle($this->getConfigData('title'));

        $method->setMethod($this->_code);
        $method->setMethodTitle($this->getConfigData('name'));

        $shippingCost = (float)$this->getConfigData('shipping_cost');
        // code
        $discount = $this->helper->getGeneralConfig('category_sale');
        $categorySale = explode(',', $this->helper->getGeneralConfig('multiselect_field'));
        foreach ($this->getCategoryIds() as $categoryId) {
            foreach ($categorySale as $categorySaleId) {
                if ($categoryId == $categorySaleId) {
                    $shippingCost = $shippingCost * (100 - $discount) / 100;
                }
            }
        }
        $method->setPrice($shippingCost);
        $method->setCost($shippingCost);

        $result->append($method);

        return $result;
        }
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [$this->_code => $this->getConfigData('name')];
    }

    /**
     * @return int|null
     */
    public function getShowUser()
    {
        if ($this->userContext->getUserId() != 0) {
            $customer = $this->customerRepository->getById($this->userContext->getUserId());
            $countOfYears = $this->timezone->date()->format('Y') - $this->timezone->date(strtotime($customer->getDob()))->format('Y');
            if ($countOfYears > 60) {
                return true;
            }
        }
        return false;
    }
}
