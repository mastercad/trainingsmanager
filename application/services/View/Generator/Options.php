<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:01
 */

abstract class Service_View_Generator_Options {

    /** @var string Additional Attributes for rendered HTML Element (input / select) */
    protected $additionalElementAttributes = '';

    protected $optionType = "NONE";

    /** @var array keys and priority who extracted from option collection */
    protected $optionValuePriorities = [];

    private $optionId = null;

    /** @var string|int value of option int or pipe separated integers */
    private $optionValue = null;

    /** @var string name of current Option */
    private $optionName = null;

    /** @var string (CSS) class Name in HTML Element */
    private $optionClassName = null;

    /** @var int unique ID of input field in html (e.g. TrainingPlanXExerciseOptionId or TrainingPlanXDeviceOptionId */
    private $inputFieldUniqueId = null;

    private $deviceId = null;

    private $exerciseId = null;

    private $trainingPlanXExerciseId = null;

    /** @var string|int selected value for option values */
    private $selectedOptionValue = null;

    /** @var int selected option key for option values  */
    private $selectedOptionKey = null;

    /** @var Zend_View_Abstract */
    private $view = null;

    /** @var bool should delete Button show for option element */
    private $showDelete = false;

    /** @var bool generates empty input, if no values set for option */
    private $forceGenerateEmptyInput = false;

    abstract public function generate();

    abstract protected function collectOptions();

    public function __construct(Zend_View_Abstract $view) {
        $this->setView($view);
    }

    /**
     * extract value from given object by key and priority
     *
     * @param $option
     *
     * @return null
     */
    protected function extractOptionValue($option) {
        foreach ($this->getOptionValuePriorities() as $priority) {
            if (isset($option[$priority])) {
                return $option[$priority];
            }
        }
        return null;
    }

    protected function generateSelectOption() {

    }

    protected function generateInputOption() {

    }

    public function generateOptionContent() {

    }

    public function generateOptionContentForEdit() {

    }

    /**
     * @return string
     */
    protected function generateOptionInputContent() {
        $optionId = uniqid('option_id_');
        $this->view->assign('optionLabelText', $this->getOptionName());
        $this->view->assign('currentValue', $this->getSelectedOptionKey());
        $this->view->assign('currentText', $this->getSelectedOptionValue());
        $this->view->assign('optionClassName', $this->getOptionClassName());
        $this->view->assign('optionId', $this->getOptionId());
        $this->view->assign('optionDeleteShow', $this->isShowDelete());
        $this->view->assign('additionalDataInformation', $this->getAdditionalElementAttributes());

        if (false != strpos($this->getOptionValue(), '|')) {
            $this->view->assign('selectId', $optionId);
            $optionValues = explode('|', $this->getOptionValue());
            $this->view->assign('optionValue', -1);
            $this->view->assign('optionText', 'Bitte wählen');
            $optionRowContent = $this->view->render('loops/option.phtml');
            foreach ($optionValues as $optionKey => $optionValue) {
                $this->view->assign('optionValue', $optionKey);
                $this->view->assign('optionText', $optionValue);
                $optionRowContent .= $this->view->render('loops/option.phtml');
                $this->view->assign('optionsContent', $optionRowContent);
            }
            return $this->view->render('globals/select.phtml');
        } else {
            $this->view->assign('optionInputId', $optionId);
            $this->view->assign('optionValue', $this->getOptionValue());
            return $this->view->render('loops/option-input.phtml');
        }
    }

    /**
     * @return string
     */
    public function getAdditionalElementAttributes() {
        return $this->additionalElementAttributes;
    }

    /**
     * @param string $additionalElementAttributes
     */
    public function setAdditionalElementAttributes($additionalElementAttributes) {
        $this->additionalElementAttributes = $additionalElementAttributes;
    }

    /**
     * @return string
     */
    public function getOptionType() {
        return $this->optionType;
    }

    /**
     * @param string $optionType
     */
    public function setOptionType($optionType) {
        $this->optionType = $optionType;
    }

    /**
     * @return array
     */
    public function getOptionValuePriorities() {
        return $this->optionValuePriorities;
    }

    /**
     * @param array $optionValuePriorities
     */
    public function setOptionValuePriorities($optionValuePriorities) {
        $this->optionValuePriorities = $optionValuePriorities;
    }

    /**
     * @return null
     */
    public function getOptionId() {
        return $this->optionId;
    }

    /**
     * @param null $optionId
     */
    public function setOptionId($optionId) {
        $this->optionId = $optionId;
    }

    /**
     * @return Zend_View_Abstract
     */
    public function getView() {
        return $this->view;
    }

    /**
     * @param Zend_View_Abstract $view
     */
    public function setView($view) {
        $this->view = $view;
    }

    /**
     * @return int|string
     */
    public function getOptionValue() {
        return $this->optionValue;
    }

    /**
     * @param int|string $optionValue
     */
    public function setOptionValue($optionValue) {
        $this->optionValue = $optionValue;
    }

    /**
     * @return int
     */
    public function getInputFieldUniqueId() {
        return $this->inputFieldUniqueId;
    }

    /**
     * @param int $inputFieldUniqueId
     */
    public function setInputFieldUniqueId($inputFieldUniqueId) {
        $this->inputFieldUniqueId = $inputFieldUniqueId;
    }

    /**
     * @return null
     */
    public function getDeviceId() {
        return $this->deviceId;
    }

    /**
     * @param null $deviceId
     */
    public function setDeviceId($deviceId) {
        $this->deviceId = $deviceId;
    }

    /**
     * @return null
     */
    public function getExerciseId() {
        return $this->exerciseId;
    }

    /**
     * @param null $exerciseId
     */
    public function setExerciseId($exerciseId) {
        $this->exerciseId = $exerciseId;
    }

    /**
     * @return null
     */
    public function getTrainingPlanXExerciseId() {
        return $this->trainingPlanXExerciseId;
    }

    /**
     * @param null $trainingPlanXExerciseId
     */
    public function setTrainingPlanXExerciseId($trainingPlanXExerciseId) {
        $this->trainingPlanXExerciseId = $trainingPlanXExerciseId;
    }

    /**
     * @return int|string
     */
    public function getSelectedOptionValue() {
        return $this->selectedOptionValue;
    }

    /**
     * @param int|string $selectedOptionValue
     */
    public function setSelectedOptionValue($selectedOptionValue) {
        $this->selectedOptionValue = $selectedOptionValue;
    }

    /**
     * @return int
     */
    public function getSelectedOptionKey() {
        return $this->selectedOptionKey;
    }

    /**
     * @param int $selectedOptionKey
     */
    public function setSelectedOptionKey($selectedOptionKey) {
        $this->selectedOptionKey = $selectedOptionKey;
    }

    /**
     * @return string
     */
    public function getOptionName() {
        return $this->optionName;
    }

    /**
     * @param string $optionName
     */
    public function setOptionName($optionName) {
        $this->optionName = $optionName;
    }

    /**
     * @return string
     */
    public function getOptionClassName() {
        return $this->optionClassName;
    }

    /**
     * @param string $optionClassName
     */
    public function setOptionClassName($optionClassName) {
        $this->optionClassName = $optionClassName;
    }

    /**
     * @return boolean
     */
    public function isShowDelete() {
        return $this->showDelete;
    }

    /**
     * @param boolean $showDelete
     */
    public function setShowDelete($showDelete) {
        $this->showDelete = $showDelete;
    }

    /**
     * @return boolean
     */
    public function isForceGenerateEmptyInput() {
        return $this->forceGenerateEmptyInput;
    }

    /**
     * @param boolean $forceGenerateEmptyInput
     */
    public function setForceGenerateEmptyInput($forceGenerateEmptyInput) {
        $this->forceGenerateEmptyInput = $forceGenerateEmptyInput;
    }
}