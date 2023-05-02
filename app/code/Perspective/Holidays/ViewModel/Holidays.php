<?php
namespace Perspective\Holidays\ViewModel;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Perspective\Holidays\Model\HolidaysFactory;
use Perspective\Holidays\Helper\Data;
use Magento\Catalog\Model\ProductRepository;
use Magento\Catalog\Model\Product;
use Magento\Framework\Registry;

class Holidays implements ArgumentInterface
{
    protected $holidaysFactory;

    protected $product;
    private \Magento\Catalog\Model\ResourceModel\Product $productResource;
    private \Magento\Catalog\Api\ProductRepositoryInterface $productRepository;
    private \Magento\Catalog\Model\ProductFactory $productFactory;

    /**
     * @param DateTime $date
     * @param HolidaysFactory $holidaysFactory
     * @param ProductRepository $ProductRepository
     * @param Product $product
     * @param Registry $registry
     * @param Data $helper
     * @param \Magento\Catalog\Model\ResourceModel\Product $productResource
     * @param ProductRepositoryInterface $productRepository
     * @param ProductFactory $productFactory
     */
    public function __construct(
        DateTime $date,
        HolidaysFactory $holidaysFactory,
        ProductRepository $ProductRepository,
        Product $product,
        Registry $registry,
        Data $helper,
        \Magento\Catalog\Model\ResourceModel\Product $productResource,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Model\ProductFactory $productFactory
    ) {
        $this->holidaysFactory = $holidaysFactory;
        $this->date = $date;
        $this->helper = $helper;
        $this->product = $ProductRepository;
        $this->registry = $registry;
        $this->productPrice = $product;
        $this->productResource = $productResource;
        $this->productRepository = $productRepository;
        $this->productFactory = $productFactory;
    }

    public function getProduct()
    {
        return $this->registry->registry('current_product');
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws StateException
     * @throws InputException
     */
    public function getHolidays()
    {
        $post = $this->holidaysFactory->create();
        $collection = $post->getCollection();
        $discount = 0;
        if ($this->helper->getHolidayModule('enable')) {
            foreach ($collection as $item) {
                if ($item->getData('start_holiday') <= $this->date->gmtDate()
                    && $item->getData('end_holiday') >= $this->date->gmtDate()
                    && $item->getData('status') == 1) {
                    echo "Name: " . $item->getData('holiday_name') . "<br>" .
                        " Text: " . $item->getData('holiday_text') .
                        " Discount: " . $item->getData('discount');
                }
                if ($discount <= $item->getData('discount')) $discount = $item->getData('discount');
            }
//                $product = $this->productRepository->getById($this->getProduct()->getId());
//                $product->setData('final_price', $price);
//                $this->productRepository->save($product);


//                $product = $this->productFactory->create()->load($this->getProduct()->getId());
//                $product->setPrice($price);
//                $product->save();
            return $discount;
        }
        return null;
    }

    public function getCustomFinalPrice()
    {
        if ($this->getProduct()) {
            return $price = ($this->getProduct()->getFinalPrice() * (100 - $this->getHolidays())) / 100;
        }
        return null;
    }
}
