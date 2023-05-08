<?php
declare(strict_types=1);

namespace Perspective\CustomShipping\Plugin;

use Magento\Quote\Model\Quote\Address\Rate;
use Magento\Quote\Model\Quote\Address\RateResult\AbstractResult;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

class ModifyCustomShippingPrice
{
    public function afterImportShippingRate(
        Rate $subject,
        Rate $result,
        AbstractResult $rate
    ): Rate {
        if ($rate instanceof Method) {
            if ($result->getCode() == "customshipping_customshipping") {
                $result->setPrice($result->getPrice() + 100);
            }
            return $result;
        }
        return $result;
    }
}
