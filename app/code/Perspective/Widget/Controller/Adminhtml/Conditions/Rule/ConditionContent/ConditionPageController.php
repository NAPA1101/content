<?php

namespace Perspective\Widget\Controller\Adminhtml\Conditions\Rule\ConditionContent;

use Magento\Backend\App\Action;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface as HttpPostActionInterface;

//@codingStandardsIgnoreLine
class ConditionPageController extends \Perspective\Widget\Controller\Adminhtml\Conditions\Rule implements HttpPostActionInterface, HttpGetActionInterface
{
    /**
     * Authorization level of a basic admin session
     */
    const ADMIN_RESOURCE = 'Magento_Widget::widget_instance';

    /**
     * @var \Magento\Framework\View\LayoutFactory
     */
    protected $layoutFactory;

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @param Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\View\LayoutFactory $layoutFactory
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter
     * @param \Magento\CatalogWidget\Model\RuleFactory $ruleFactory
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        Action\Context $context,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\View\LayoutFactory $layoutFactory,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->layoutFactory = $layoutFactory;
        $this->resultRawFactory = $resultRawFactory;

        parent::__construct(
            $context,
            $coreRegistry,
            $fileFactory,
            $dateFilter,
            $ruleFactory,
            $logger
        );
    }

    /**
     * Chooser Source action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $button_num = $this->getRequest()->getParam('button_num');
        $name = $this->getRequest()->getParam('name');
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $this->layoutFactory->create();
        $pagesGrid = $layout->createBlock(
            \Perspective\Widget\Block\Widget\Options\Conditions::class,
            '',
            ['data' => ['id' => $uniqId, 'button_num' => $button_num, 'name' => $name]]
        );
        $html = $pagesGrid->toHtml();
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();

        return $resultRaw->setContents($html);
    }
}
