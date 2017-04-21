<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 12.03.17
 * Time: 21:01
 */

abstract class Service_Generator_View_Options extends Service_Generator_View_GeneratorAbstract {

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

    /** @var string content for label of parent element */
    private $optionSelectText = null;

    /** @var string (CSS) class Name in HTML Element */
    private $optionClassName = null;

    /** @var string (CSS) class Name in HTML Parent Element (e.g Select) */
    private $optionSelectClassName = null;

    /** @var int unique ID of input field in html (e.g. TrainingPlanXExerciseOptionId or TrainingPlanXDeviceOptionId */
    private $inputFieldUniqueId = null;

    private $deviceId = null;

    private $exerciseId = null;

    private $trainingPlanXExerciseId = null;

    private $trainingDiaryXTrainingPlanExerciseId = null;

    /** @var string|int selected value for option values */
    private $selectedOptionValue = null;

    /** @var int selected option key for option values  */
    private $selectedOptionKey = null;

    /** @var bool should delete Button show for option element */
    private $showDelete = false;

    /** @var bool generates empty input, if no values set for option */
    private $forceGenerateEmptyInput = false;

    /** @var bool  */
    private $exerciseFinished = false;

    /** @var string|int */
    private $baseOptionValue = null;

    /** @var bool  */
    private $showTrainingProgress = false;

    abstract public function generate();

    abstract protected function collectOptions();

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
        $this->getView()->assign('optionLabelText', $this->getOptionName());
        $this->getView()->assign('optionSelectText', $this->getSelectedOptionValue());
        $this->getView()->assign('currentValue', $this->getSelectedOptionValue());
        $this->getView()->assign('optionClassName', $this->getOptionClassName());
        $this->getView()->assign('optionSelectClassName', $this->getOptionSelectClassName());
        $this->getView()->assign('optionId', $this->getOptionId());
        $this->getView()->assign('optionDeleteShow', $this->isShowDelete());
        $this->getView()->assign('baseValue', $this->getBaseOptionValue());
        $this->getView()->assign('additionalDataInformation', $this->getAdditionalElementAttributes());

        if ($this->isShowTrainingProgress()) {
            if ($this->getSelectedOptionValue() < $this->getBaseOptionValue()) {
                $this->getView()->assign('selectClass', 'negative');
            } else if ($this->getSelectedOptionValue() == $this->getBaseOptionValue()) {
                $this->getView()->assign('selectClass', 'current');
            } else {
                $this->getView()->assign('selectClass', 'positive');
            }
        }

        if ($this->isExerciseFinished()) {
            return '<label>' . $this->getOptionName() . ':</label> ' . $this->getSelectedOptionValue();
        } else if (false != strpos($this->getOptionValue(), '|')) {
            $this->getView()->assign('optionClassName', $this->getOptionClassName() . ' custom-drop-down');
            $this->getView()->assign('selectId', $optionId);
            $optionValues = explode('|', $this->getOptionValue());
            $optionRowContent = '';
            foreach ($optionValues as $optionKey => $optionValue) {
                $this->getView()->assign('optionValue', $optionKey);
                $this->getView()->assign('optionText', $optionValue);
                $this->receiveCurrentOptionClass($optionValue, $this->getSelectedOptionValue(), $this->getBaseOptionValue());
                $optionRowContent .= $this->getView()->render('loops/option.phtml');
                $this->getView()->assign('optionsContent', $optionRowContent);
            }
            return $this->getView()->render('globals/select.phtml');
        } else {
            $this->getView()->assign('optionInputId', $optionId);
            $this->getView()->assign('optionValue', $this->getOptionValue());
            return $this->getView()->render('loops/option-input.phtml');
        }
    }

    private function receiveCurrentOptionClass($currentValue, $selectedValue, $baseValue)
    {
        if ($this->isShowTrainingProgress()) {
            if ($currentValue < $baseValue) {
                $this->getView()->assign('optionClass', 'negative');
            } else if ($currentValue == $baseValue) {
                $this->getView()->assign('optionClass', 'current');
            } else {
                $this->getView()->assign('optionClass', 'positive');
            }
        }
        return $this;
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
     * @return null
     */
    public function getTrainingDiaryXTrainingPlanExerciseId() {
        return $this->trainingDiaryXTrainingPlanExerciseId;
    }

    /**
     * @param null $trainingDiaryXTrainingPlanExerciseId
     */
    public function setTrainingDiaryXTrainingPlanExerciseId($trainingDiaryXTrainingPlanExerciseId) {
        $this->trainingDiaryXTrainingPlanExerciseId = $trainingDiaryXTrainingPlanExerciseId;
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

    /**
     * @return string
     */
    public function getOptionSelectClassName() {
        return $this->optionSelectClassName;
    }

    /**
     * @param string $optionSelectClassName
     */
    public function setOptionSelectClassName($optionSelectClassName) {
        $this->optionSelectClassName = $optionSelectClassName;
    }

    /**
     * @return string
     */
    public function getOptionSelectText() {
        return $this->optionSelectText;
    }

    /**
     * @param string $optionSelectText
     */
    public function setOptionSelectText($optionSelectText) {
        $this->optionSelectText = $optionSelectText;
    }

    /**
     * @return boolean
     */
    public function isExerciseFinished() {
        return $this->exerciseFinished;
    }

    /**
     * @param boolean $exerciseFinished
     *
     * @return $this
     */
    public function setExerciseFinished($exerciseFinished) {
        $this->exerciseFinished = $exerciseFinished;
        return $this;
    }

    /**
     * @return int|string
     */
    public function getBaseOptionValue() {
        return $this->baseOptionValue;
    }

    /**
     * @param int|string $baseOptionValue
     *
     * @return $this
     */
    public function setBaseOptionValue($baseOptionValue) {
        $this->baseOptionValue = $baseOptionValue;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isShowTrainingProgress() {
        return $this->showTrainingProgress;
    }

    /**
     * @param boolean $showTrainingProgress
     *
     * @return $this
     */
    public function setShowTrainingProgress($showTrainingProgress) {
        $this->showTrainingProgress = $showTrainingProgress;
        return $this;
    }

}