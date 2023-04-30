<?php
namespace Perspective\Holidays\Model;

use Perspective\Holidays\Model\ResourceModel\Holidays\CollectionFactory;
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
        CollectionFactory $holidaysCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $holidaysCollectionFactory->create();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    // @codingStandardsIgnoreEnd

    public function getData()
    {

        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $items = $this->collection->getItems();

        foreach ($items as $holidays) {
            $this->loadedData[$holidays->getEntityId()] = $holidays->getData();
        }
        return $this->loadedData;
    }
}
