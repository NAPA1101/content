<?php
namespace Perspective\InstantPurchase\Model;

use Perspective\InstantPurchase\Model\ResourceModel\Purchase\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;

    // @codingStandardsIgnoreStart
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $purchaseCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $purchaseCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    // @codingStandardsIgnoreEnd

    public function getData()
    {

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $purchase) {
            $this->loadedData[$purchase->getId()] = $purchase->getData();
        }
        return $this->loadedData;
    }
}
