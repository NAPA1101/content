<?php

namespace Perspective\Widget\Controller\Adminhtml\Conditions\Rule;

use Magento\Framework\Json\Helper\Data as JsonHelper;

/**
 * Class PrepareData
 * ConvertData from controller to string
 */
class PrepareData extends \Perspective\Widget\Controller\Adminhtml\Conditions\Rule
{
    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    private $conditionsHelper;
    /**
     * @var string
     */
    private $error;
    /**
     * @var string
     */
    private $message;
    /**
     * @var
     */
    private $data;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    private $resultJsonFactory;
    /**
     * @var JsonHelper
     */
    private $jsonHelper;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\CatalogWidget\Model\RuleFactory $ruleFactory
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        JsonHelper $jsonHelper
    ) {
        $this->jsonHelper = $jsonHelper;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->conditionsHelper = $conditionsHelper;
        parent::__construct($context, $coreRegistry, $fileFactory, $dateFilter, $ruleFactory, $logger);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        if (!$this->getRequest()->getPostValue()) {
            $this->_redirect('admin/*/');
        }
        try {
            $serializedConditionArray =
                $this->conditionsHelper->decode($this->getRequest()->getParam('serializedArray'));
            $templateArray = [];
            foreach ($serializedConditionArray as $index => $singleArrItem) {
                if ($singleArrItem['name'] == 'form_key') {
                    unset($serializedConditionArray[$index]);
                    continue;
                }
                if ($singleArrItem['name'] == 'select') {
                    unset($serializedConditionArray[$index]);
                    continue;
                }
                if (!empty($templateArray[str_replace('rule', '', $singleArrItem['name'])])) {
                    $templateArray[str_replace('rule', '', $singleArrItem['name'])] .= ',' . $singleArrItem['value'];
                } else {
                    $templateArray[str_replace('rule', '', $singleArrItem['name'])] = $singleArrItem['value'];
                }
            }
            $finalArr = [];

            foreach ($templateArray as $index => $data) {
                $indexName = $this->get_string_between(str_replace(
                    '[conditions]',
                    '',
                    $index
                ), '[', ']');
                $paramName = $this->get_string_between(str_replace(
                    '[conditions][' . $indexName . ']',
                    '',
                    $index
                ), '[', ']');
                $multiValue = str_replace(
                    'parameters[conditions][' . $indexName . ']' . '[' . $paramName . ']',
                    '',
                    $index
                );
                //@codingStandardsIgnoreLine
                if ($paramName == 'value' && strpos($data, ',')) {
                    $finalArr[$indexName][$paramName] = [$data];
                } else {
                    $finalArr[$indexName][$paramName] = $data;
                }
                if ($paramName == 'value' && $multiValue == '[]') {
                    if ($finalArr[$indexName][$paramName]) {
                        if ($finalArr[$indexName][$paramName]) {
                            $temp = $finalArr[$indexName][$paramName];
                            unset($finalArr[$indexName][$paramName]);
                            $finalArr[$indexName][$paramName][0] = $data;
                        } else {
                            $finalArr[$indexName][$paramName][0] .= ',' . $data;
                        }
                    } else {
                        $finalArr[$indexName][$paramName][0] = [$data];
                    }
                }
            }
            $this->data = $this->conditionsHelper->encode($finalArr);
            $this->message = 'success';
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            $id = (int)$this->getRequest()->getParam('rule_id');
            if (!empty($id)) {
                $this->_redirect('admin/*/*', ['id' => $id]);
            } else {
                $this->_redirect('admin/*/*');
            }
            return;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage(
                __('Something went wrong while preparing the rule data. Please review the error log.')
            );
            $this->error = $e->getMessage();
            $this->message = 'error';
            $this->logger->critical($e);
            $data = !empty($data) ? $data : [];
            $this->_session->setPageData($data);
            $this->_redirect('widget/*/*', ['id' => $this->getRequest()->getParam('rule_id')]);
            return;
        }
        $resultJson = $this->resultJsonFactory->create();
        $resultData = substr(substr($this->jsonHelper->jsonEncode($this->data), 1), 0, -1);
        return $resultJson->setData([
            'message' => $this->message,
            'data' => $resultData,
            'error' => $this->error
        ]);
    }

    //@codingStandardsIgnoreLine
    protected function get_string_between($string, $start, $end)
    {
        $string = ' ' . $string;
        $ini = strpos($string, $start);
        if ($ini == 0) {
            return '';
        }
        $ini += strlen($start);
        $len = strpos($string, $end, $ini) - $ini;
        return substr($string, $ini, $len);
    }
}
