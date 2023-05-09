<?php

namespace Perspective\CustomerImage\Plugin\Customer;

use Magento\Customer\Model\Metadata\Form\AbstractData;

class RequestScope
{

    /**
     * @param AbstractData $subject
     * @param $scope
     * @param $result
     * @return mixed|void
     */
    public function afterSetRequestScope(AbstractData $subject, $scope, $result)
    {
        $this->_requestScope = $scope;
        if ($this->_requestScope === null) {
            $this->_requestScope = '';
            return $result;
        }
    }
}
