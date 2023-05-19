<?php

namespace Perspective\Widget\Block\Widget\Options;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Widget\Block\BlockInterface;

/**
 * Class Conditions
 * Entry point for building condition controller widget.xml ->
 * Conditions::prepareElementHtml( $this->getLayout()->createBlock(Chooser))->
 * Conditions::_prepareForm(create Form)
 */
class Conditions extends Generic implements BlockInterface
{
    const WEATHER = 'weather';
    const PRODUCT = 'product';
    /**
     * Core registry
     *
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $rendererFieldset;
    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $conditions;
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;
    /**
     * @var \Magento\Cms\Model\PageFactory
     */
    private $_pageFactory;
    /**
     * @var \Magento\CatalogWidget\Model\RuleFactory
     */
    private $ruleFactory;
    /**
     * @var \Magento\Widget\Helper\Conditions
     */
    private $conditionsHelper;
    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;
    /**
     * @var \Perspective\Widget\Model\Rule
     */
    private $customRuleFactory;

    /**
     * Conditions constructor.
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\CatalogWidget\Model\RuleFactory $ruleFactory
     * @param \Magento\Widget\Helper\Conditions $conditionsHelper
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Framework\App\Response\Http $response
     * @param \Perspective\Widget\Model\RuleFactory $customRuleFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Rule\Block\Conditions $conditions,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $rendererFieldset,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\CatalogWidget\Model\RuleFactory $ruleFactory,
        \Magento\Widget\Helper\Conditions $conditionsHelper,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Framework\App\Response\Http $response,
        \Perspective\Widget\Model\RuleFactory $customRuleFactory,
        array $data = []
    ) {
        $this->response = $response;
        $this->conditionsHelper = $conditionsHelper;
        $this->ruleFactory = $ruleFactory;
        $this->_pageFactory = $pageFactory;
        $this->coreRegistry = $coreRegistry;
        $this->rendererFieldset = $rendererFieldset;
        $this->conditions = $conditions;
        $this->customRuleFactory = $customRuleFactory;
        parent::__construct($context, $coreRegistry, $formFactory, $data);
    }

    /**
     * Entry point. First from widget.xml
     * Render the button
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function prepareElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $uniqId = $this->mathRandom->getUniqueHash($element->getId());
        $sourceUrl = $this->getUrl(
            'widget/conditions_rule_conditioncontent/conditionpagecontroller',
            [
                'name' => $element->getData('name'),
                'uniq_id' => $uniqId,
                'button_num' => $this->getData('condition')[key($this->getData('condition'))],
                'type_of_rule_model' => $this->getData('type_of_rule_model')
            ]
        );
        $chooser = $this->getLayout()->createBlock(
            \Perspective\Widget\Block\Widget\Options\Chooser::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setSourceUrl(
            $sourceUrl
        )->setUniqId(
            $uniqId
        )->setValue($this->getData('condition')[key($this->getData('condition'))]);
        if ($element->getValue()) {
            $page = $this->_pageFactory->create()->load((int)$element->getValue());
            if ($page->getId()) {
                $chooser->setLabel($this->escapeHtml($page->getTitle()));
            }
        }
        $element->setData('after_element_html', $chooser->toHtml());
        return $element;
    }

    /**
     * Prepare form before rendering HTML
     * Second stage
     * After pressing on Chooser button opens this form with conditions control and save button
     * @return Generic
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareForm()
    {
        if ($this->_request->getParam('type_of_rule_model') === self::WEATHER) {
            $model = $this->customRuleFactory->create();
        } else {
            $model = $this->ruleFactory->create();
        }
        $id = $this->_request->getParam('uniq_id');
        $name = $this->_request->getParam('name');
        $cuttedName = trim(str_replace('parameters', '', $name), '[]');
        /** @var \Magento\Framework\Data\Form $form */
        $form = $this->_formFactory->create(
            [
                'data' => [
                    'id' => 'edit_form_modal',
                    'action' => $this->getUrl(
                        'widget/conditions_rule/savewidget',
                        [
                            'uniq_id' => $this->_request->getParam('uniq_id'),
                            'button_num' => $this->_request->getParam('button_num'),
                            'name' => $this->_request->getParam('name'),
                        ]
                    ),
                    'method' => 'post',
                ],
            ]
        );
        $form->setUseContainer(true);
        $form->setHtmlIdPrefix('rule_');
        $renderer = $this->rendererFieldset->setTemplate(
            'Magento_CatalogRule::promo/fieldset.phtml'
        )->setNewChildUrl(
            $this->getUrl('widget/conditions_rule/newConditionHtml/form/rule_conditions_fieldset')
        );
        $fieldset = $form->addFieldset(
            'conditions_fieldset',
            [
                'legend' => __(
                    'Apply the rule only if the following conditions are met (leave blank for all products).'
                )
            ]
        )->setRenderer(
            $renderer
        );
        $conditionsRule = $model->getConditions();
        $conditionsRule->setJsFormObject('rule_conditions_fieldset');
        try {
            $conditions = $this->conditionsHelper->decode($this->getRequest()->getParam('element_value'));
            $model->loadPost(['conditions' => $conditions]);
        }// @codingStandardsIgnoreStart
        catch (\InvalidArgumentException $invalidArgumentException) {
            /**do nothing
             * because we dont want throw Exception
             * if user set EMPTY condition control ($model)
             */
        }
        // @codingStandardsIgnoreEnd
        $element = $fieldset->addField(
            'condition',
            'text',
            ['name' => 'conditions',
                'label' => __('Condition'),
                'title' => __('Condition'),
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveandcontinue', 'target' => '#edit_form_modal']],
                ]
            ]
        )
            ->setRule(
                $model
            )->setRenderer(
                $this->conditions
            );
        $this->getLayout()->createBlock(
            \Magento\Widget\Block\Adminhtml\Widget\Options::class
        )->setElement(
            $element
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setConfig(
            $this->getConfig()
        )->setFieldsetId(
            $this->getFieldsetId()
        )->setUniqId(
            $id
        );
        $buttonHtml = $this->getLayout()->createBlock(
            \Perspective\Widget\Block\Widget\Options\ChooseButtonTemplateHandler::class
        )->setData(
            'cuttedName',
            $cuttedName
        )->setData(
            'name',
            $name
        )->toHtml();
        /**
         * Render save button
         */
        $fieldset->addField(
            'button-save',
            'hidden',
            ['name' => 'select',
                'label' => __('Save condition'),
                'title' => __('Save condition'),
                'class' => 'action-close',
                'data-role' => 'closeBtn',
                'data_attribute' => [
                    'mage-init' => ['button' => ['event' => 'saveandcontinue', 'target' => '#edit_form']],
                ],
                'after_element_html' => $buttonHtml
            ]
        );
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
