<?php
namespace Perspective\Holidays\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Perspective\Holidays\Model\HolidaysFactory;
use \Magento\Framework\Stdlib\DateTime\DateTime;

class Holidays implements ArgumentInterface
{
    protected $holidaysFactory;
    public function __construct(
        HolidaysFactory $holidaysFactory,
        DateTime $date
    ) {
        $this->holidaysFactory = $holidaysFactory;
        $this->date = $date;
    }

    public function getHolidays()
    {
        $post = $this->holidaysFactory->create();
        $collection = $post->getCollection();
        foreach ($collection as $item) {
            if ($item->getData('start_holiday') <= $this->date->gmtDate()
                && $item->getData('end_holiday') >= $this->date->gmtDate())
            echo "Name: " . $item->getData('holiday_name') . " Text: " . $item->getData('holiday_text') . "<br>";
        }
    }
}
