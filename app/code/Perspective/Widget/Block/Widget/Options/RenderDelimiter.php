<?php

namespace Perspective\Widget\Block\Widget\Options;

use Magento\Backend\Block\Template;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class RenderDelimiter extends Template implements RendererInterface
{
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;

    /**
     * @var \Magento\CatalogWidget\Model\Rule
     */
    protected $rule;

    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $elementFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var AbstractElement
     */
    protected $element;

    /**
     * @var \Magento\Framework\Data\Form\Element\Text
     */
    protected $input;
    /**
     * @var \Magento\Cms\Model\BlockFactory
     */
    protected $blockFactory;

    /**
     * @var string
     */
    protected $_template = 'Perspective_Widget::widget/delimiter.phtml';

    /**
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\CatalogWidget\Model\Rule $rule
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\CatalogWidget\Model\Rule $rule,
        \Magento\Framework\Registry $registry,
        \Magento\Cms\Model\BlockFactory $blockFactory,
        array $data = []
    ) {
        $this->elementFactory = $elementFactory;
        $this->conditions = $conditions;
        $this->rule = $rule;
        $this->registry = $registry;
        $this->blockFactory = $blockFactory;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $widgetParameters = [];
        $widget = $this->registry->registry('current_widget_instance');
        if ($widget) {
            $widgetParameters = $widget->getWidgetParameters();
        } elseif ($widgetOptions = $this->getLayout()->getBlock('wysiwyg_widget.options')) {
            $widgetParameters = $widgetOptions->getWidgetValues();
        }

        if (isset($widgetParameters['conditions'])) {
            $this->rule->loadPost($widgetParameters);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->element = $element;
        $this->rule->getConditions()->setJsFormObject($this->getHtmlId());
        return $this->toHtml();
    }

    public function prepareElementHtml(AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl('cms/block_widget/chooser', ['uniq_id' => $uniqId]);

        $chooser = $this->getLayout()->createBlock(
            \Perspective\Widget\Block\Widget\Options\Delimit::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setUniqId(
            $uniqId
        );


        $element->setData('after_element_html', $chooser->toHtml());
        return $element;

    }

    /**
     * @return string
     */
    public function getNewChildUrl()
    {
        return $this->getUrl(
            'catalog_widget/product_widget/conditions/form/' . $this->getElement()->getContainer()->getHtmlId()
        );
    }

    /**
     * @return AbstractElement
     */
    public function getElement()
    {
        return $this->element;
    }

    /**
     * @return string
     */
    public function getHtmlId()
    {
        return $this->getElement()->getContainer()->getHtmlId();
    }

    /**
     * @return string
     */
    public function getInputHtml()
    {
        $this->input = $this->elementFactory->create('text');
        $this->input->setRule($this->rule)->setRenderer($this->conditions);
        return $this->input->toHtml();
    }
}
