<?php
declare(strict_types=1);

namespace Perspective\Widget\Block\Widget\Options;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Chooser widget block.
 */
class Chooser extends \Magento\Backend\Block\Template
{
    /**
     * @var \Magento\Framework\Data\Form\Element\Factory
     */
    protected $_elementFactory;
    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;
    /**
     * @var AbstractElement
     */
    private $hiddenInput;
    /**
     * @var string
     */
    private $chooserId;
    /**
     * @var string
     */
    private $configJson;

    /**
     * @return string
     */
    public function getChooserId(): string
    {
        return $this->chooserId;
    }

    /**
     * @param string $chooserId
     */
    public function setChooserId(string $chooserId): string
    {
        return $this->chooserId = $chooserId;
    }

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Data\Form\Element\Factory $elementFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Data\Form\Element\Factory $elementFactory,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_elementFactory = $elementFactory;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getConfigJson(): string
    {
        return $this->configJson;
    }

    /**
     * @param string $configJson
     */
    public function setConfigJson(string $configJson): string
    {
        return $this->configJson = $configJson;
    }

    /**
     * Return chooser HTML and init scripts
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _toHtml()
    {
        $element = $this->getElement();
        /* @var $fieldset \Magento\Framework\Data\Form\Element\Fieldset */
        $fieldset = $element->getForm()->getElement($this->getFieldsetId());
        $chooserId = $this->getUniqId();
        $this->setChooserId($chooserId);
        $config = $this->getConfig();

        // add chooser element to fieldset
        $chooser = $fieldset->addField(
            'chooser' . $element->getId(),
            'note',
            ['label' => $config->getLabel() ? $config->getLabel() : '', 'value_class' => 'value2']
        );
        $hiddenHtml = '';
        if ($this->getHiddenEnabled()) {
            $hidden = $this->_elementFactory->create('hidden', ['data' => $element->getData()]);
            $hidden->setId("{$chooserId}value")->setForm($element->getForm());
            if ($element->getRequired()) {
                $hidden->addClass('required-entry');
            }
            $hiddenHtml = $hidden->getElementHtml();
            $this->hiddenInput = $hidden;
            $element->setValue('');
        }

        $buttons = $config->getButtons();
        $chooseButton = $this->getLayout()->createBlock(
            \Magento\Backend\Block\Widget\Button::class
        )->setType(
            'button'
        )->setId(
            $chooserId . ' control'
        )->setClass(
            'btn-chooser'
        )->setLabel(
            $buttons['open']
        )->setOnclick(
            $chooserId . '.choose()'
        )->setDisabled(
            $element->getReadonly()
        );
        $chooser->setData('after_element_html', $hiddenHtml . $chooseButton->toHtml());
        // render label and chooser scripts
        $configJson = $this->_jsonEncoder->encode($config->getData());
        $this->setConfigJson($configJson);
        $this->setTemplate('Perspective_Widget::control/ChooserButton.phtml');
        return parent::_toHtml();
    }

    /**
     * @return mixed
     */
    public function getHiddenInput()
    {
        return $this->hiddenInput;
    }

    // @codingStandardsIgnoreEnd

    /**
     * @return \Magento\Framework\Phrase
     */
    public function getLabelForButton()
    {
        $hidden = $this->getHiddenInput();
        if (!empty($hidden->getData('value'))) {
            return __('Selected');
        } else {
            return __('Not Selected');
        }
    }

    /**
     * Chooser form element getter
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_getData('element');
    }

    /**
     * Form element fieldset id getter for working with form in chooser
     *
     * @return string
     */
    public function getFieldsetId()
    {
        return $this->_getData('fieldset_id');
    }

    /**
     * Unique identifier for block that uses Chooser
     *
     * @return string
     */
    public function getUniqId()
    {
        return $this->_getData('uniq_id');
    }

    /**
     * Convert Array config to Object
     *
     * @return \Magento\Framework\DataObject
     */
    public function getConfig()
    {
        if ($this->_getData('config') instanceof \Magento\Framework\DataObject) {
            return $this->_getData('config');
        }

        $configArray = $this->_getData('config');
        $config = new \Magento\Framework\DataObject();
        $this->setConfig($config);
        if (!is_array($configArray)) {
            return $this->_getData('config');
        }

        // define chooser label
        if (isset($configArray['label'])) {
            $config->setData('label', __($configArray['label']));
        }

        // chooser control buttons
        $buttons = ['open' => __('Choose...'), 'close' => __('Close')];
        if (isset($configArray['button']) && is_array($configArray['button'])) {
            foreach ($configArray['button'] as $id => $label) {
                $buttons[$id] = __($label);
            }
        }
        $config->setButtons($buttons);

        return $this->_getData('config');
    }

    /**
     * Flag to indicate include hidden field before chooser or not
     *
     * @return bool
     * @SuppressWarnings(PHPMD.BooleanGetMethodName)
     */
    public function getHiddenEnabled()
    {
        return $this->hasData('hidden_enabled') ? (bool)$this->_getData('hidden_enabled') : true;
    }

    /**
     * Chooser source URL getter
     *
     * @return string
     */
    public function getSourceUrl()
    {
        return $this->_getData('source_url');
    }
}
