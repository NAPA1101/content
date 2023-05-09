<?php


namespace Perspective\CustomerImage\Model\Config\Source;


class Size
{
    /**
     * Get source model array
     * @return array
     */
    public function toOptionArray()
    {
        $source = [
            ['value' => 0, 'label' => '25x25px'],
            ['value' => 1, 'label' => '25x40px']
        ];
        return $source;
    }
}
