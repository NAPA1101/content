<?php
namespace Perspective\InstantPurchase\Model;

use Magento\Framework\Data\OptionSourceInterface;

class Status implements OptionSourceInterface
{
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => 'New', 'value' => 0];
        $options[] = ['label' => 'In work', 'value' => 1];
        $options[] = ['label' => 'Completed', 'value' => 2];
        return $options;
    }
}
