<?php
namespace Perspective\Widget\Block\Widget\Product;

use Magento\CatalogWidget\Block\Product\ProductsList;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Url\EncoderInterface;
use Magento\Framework\View\LayoutFactory;

class ProductList extends ProductsList
{
    protected $_template = "widget/tabs.phtml";

    const DEFAULT_SORT_BY = 'id';

    const DEFAULT_SORT_ORDER = 'asc';
    /**
     * Image helper
     *
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;
    /**
     * @var \Magento\Checkout\Helper\Cart
     */
    protected $_cartHelper;
    /**
     * @var \Magento\Review\Block\Product\View
     */
    protected $product;
    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $productFactory;
    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $productCollectionFactory;
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Model\Product\Visibility $catalogProductVisibility,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Rule\Model\Condition\Sql\Builder $sqlBuilder,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Review\Block\Product\View $product,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        array $data = [],
        Json $json = null,
        LayoutFactory $layoutFactory = null,
        EncoderInterface $urlEncoder = null
    ) {
        $this->_imageHelper = $context->getImageHelper();
        $this->_cartHelper = $context->getCartHelper();
        $this->product = $product;
        $this->productFactory = $productFactory;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->categoryFactory = $categoryFactory;
        parent::__construct(
            $context,
            $productCollectionFactory,
            $catalogProductVisibility,
            $httpContext,
            $sqlBuilder,
            $rule,
            $conditionsHelper,
            $data,
            $json,
            $layoutFactory,
            $urlEncoder
        );
    }

    public function imageHelperObj()
    {
        return $this->_imageHelper;
    }

    public function getSortBy()
    {
        if (!$this->hasData('products_sort_by')) {
            $this->setData('products_sort_by', self::DEFAULT_SORT_BY);
        }
        return $this->getData('products_sort_by');
    }

    public function getSortOrder()
    {
        if (!$this->hasData('products_sort_order')) {
            $this->setData('products_sort_order', self::DEFAULT_SORT_ORDER);
        }
        return $this->getData('products_sort_order');
    }

    public function getProductsInTab($getConditions)
    {

        $collection = $this->productCollectionFactory->create();
        $collection->setVisibility($this->catalogProductVisibility->getVisibleInCatalogIds());

        $collection = $this->_addProductAttributesAndPrices($collection)
            ->addStoreFilter()
            ->setPageSize($this->getPageSize())
            ->setCurPage($this->getRequest()->getParam($this->getData('page_var_name'), 1))
            ->setOrder($this->getSortBy(), $this->getSortOrder());

        $conditions = $this->getSpecConditions($getConditions);
        $conditions->collectValidatedAttributes($collection);
        $this->sqlBuilder->attachConditionToCollection($collection, $conditions);

        return $collection;
    }

    public function getSpecConditions($getConditions)
    {
        $conditions = $this->getData($getConditions)
            ? $this->getData($getConditions)
            : $this->getData('conditions');

        if ($conditions) {
            $conditions = $this->conditionsHelper->decode($conditions);
        }

        foreach ($conditions as $key => $condition) {
            if (!empty($condition['attribute'])
                && in_array($condition['attribute'], ['special_from_date', 'special_to_date'])
            ) {
                $conditions[$key]['value'] = date('Y-m-d H:i:s', strtotime($condition['value']));
            }
        }

        $this->rule->loadPost(['conditions' => $conditions]);
        $specConditions = $this->rule->getConditions();
        return $specConditions;
    }

    /**
     * Render pagination HTML
     *
     * @return string
     */
    public function getPagersHtml($getConditions)
    {
        if ($this->showPager() && $this->getProductsInTab($getConditions)->getSize() > $this->getProductsPerPage()) {
            if (!$this->pager) {
                $this->pager = $this->getLayout()->createBlock(
                    \Magento\Catalog\Block\Product\Widget\Html\Pager::class,
                    $this->getWidgetPagerBlockName()
                );

                $this->pager->setUseContainer(true)
                    ->setShowAmounts(true)
                    ->setShowPerPage(false)
                    ->setPageVarName($this->getData('page_var_name'))
                    ->setLimit($this->getProductsPerPage())
                    ->setTotalLimit($this->getProductsCount())
                    ->setCollection($this->getProductsInTab($getConditions));
            }
            if ($this->pager instanceof \Magento\Framework\View\Element\AbstractBlock) {
                return $this->pager->toHtml();
            }
        }
        return '';
    }
    /**
     * Get widget block name
     *
     * @return string
     */
    private function getWidgetPagerBlockName()
    {
        $pageName = $this->getData('page_var_name');
        $pagerBlockName = 'widget.products.list.pager';

        if (!$pageName) {
            return $pagerBlockName;
        }

        return $pagerBlockName . '.' . $pageName;
    }
    /**
     * Return flag whether pager need to be shown or not
     *
     * @return bool
     */
    public function showPager()
    {
        if (!$this->hasData('show_pager')) {
            $this->setData('show_pager', self::DEFAULT_SHOW_PAGER);
        }
        return (bool)$this->getData('show_pager');
    }
}
