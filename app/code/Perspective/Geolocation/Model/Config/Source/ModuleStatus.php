<?php

namespace Perspective\Geolocation\Model\Config\Source;

class ModuleStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Get all options
     * @return array
     */

    public function toOptionArray()
    {
        $options=  [ ['value' => 0, 'label' => __('Disabled')],
            ['value' => 1, 'label' => __('Enabled')]
        ];

        return $options;
    }
}
