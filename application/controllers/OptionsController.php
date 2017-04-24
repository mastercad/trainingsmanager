<?php
/**
 * Created by JetBrains PhpStorm.
 * User: mastercad
 * Date: 24.04.13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

abstract class OptionsController extends AbstractController {

    private $optionsStorage = null;

    protected $map = [];

    /**
     * @return Interface_OptionsStorageInterface
     */
    protected abstract function useOptionsStorage();

    public function indexAction() {

        $this->view->headScript()->appendFile($this->view->baseUrl() . '/js/edit.js', 'text/javascript');
        $optionsCollection = $this->useOptionsStorage()->findAllOptions();
        $optionsContent = 'Es konnten leider keine optionen gefunden werden!';

        if (0 < count($optionsCollection)) {
            $optionsContent = '';
            foreach ($optionsCollection as $option) {
                $this->view->assign('editLink', '/'.$this->getRequest()->getControllerName().'/edit/id/' . $option->offsetGet($this->map['option_id']));
                $this->view->assign('name', $option->offsetGet($this->map['option_name']));
                $this->view->assign('id', $option->offsetGet($this->map['option_id']));
                $optionsContent .= $this->view->render('loops/item-row.phtml');
            }
        }
        $this->view->assign('optionsContent', $optionsContent);
    }

    public function editAction() {
        $params = $this->getRequest()->getParams();

        $optionName = '';
        $optionValue = '';
        $optionContent = '';

        if (true === isset($params['id'])
            && true === is_numeric($params['id'])
            && 0 < $params['id']
        ) {
            $optionContent = '';
            $optionId = intval($params['id']);
            $option = $this->useOptionsStorage()->findOptionById($optionId);

            if ($option instanceof Zend_Db_Table_Row) {
                $optionName = $option->offsetGet($this->map['option_name']);
                $optionValue = $option->offsetGet($this->map['option_value']);
                $this->view->assign('id', $option->offsetGet($this->map['option_id']));
            } else {
                $optionContent = 'Das Übungsoption konnte leider nicht gefunden werden!';
            }

        }
        $this->view->assign('value', $optionName);
        $this->view->assign('type', 'option-name');

        $optionContent .= $this->view->render('/loops/item-edit.phtml');
        $this->view->assign('value', $optionValue);
        $this->view->assign('type', 'option-value');

        $optionContent .= $this->view->render('/loops/item-edit.phtml');
        $this->view->assign('optionContent', $optionContent);
    }

    public function showAction() {

        $optionId = intval($this->getParam('id'));
        if (0 < $optionId) {
            $option = $this->useOptionsStorage()->findOptionById($optionId);

            if ($option instanceof Zend_Db_Table_Row_Abstract) {
                $this->view->assign('optionName', $option->offsetGet($this->map['option_name']));
                $this->view->assign('optionValue', $option->offsetGet($this->map['option_value']));
                $this->view->assign('optionId', $option->offsetGet($this->map['option_id']));
                $this->view->assign('detailOptionsContent', $this->generateDetailOptionsContent($optionId));
            }
        }
    }

    public function saveAction() {
        if ($this->getRequest()->isPost()) {
            $optionId = $this->getParam('id');
            $optionName = $this->getParam('name');
            $optionValue = $this->getParam('value');
            $messages = [];

            if (!$optionName) {
                array_push($messages, array('type' => 'fehler', 'message' => 'Es muss ein name angegeben werden', 'result' => false, 'id' => $optionId));
                $this->view->assign('json_string', json_encode($messages));
                return false;
            }

            $data = [
                $this->map['option_name'] => $optionName,
                $this->map['option_value'] => $optionValue
            ];

            if (is_numeric($optionId)
                && 0 < $optionId
            ) {
                $this->useOptionsStorage()->updateOption($data, $optionId);
            } else {
                $optionId = $this->useOptionsStorage()->insertOption($data);
            }
            array_push($messages, array('type' => 'meldung', 'message' => 'Übungsoption erfolgreich gespeichert', 'result' => true, 'id' => $optionId));
        } else {
            array_push($messages, array('type' => 'fehler', 'message' => 'Falscher Aufruf dieser Seite', 'result' => false));
        }
        $this->view->assign('json_string', json_encode($messages));
    }

    public function deleteAction() {

        $optionId = intval($this->getParam('id'));
        if (0 < $optionId) {
            $result = $this->useOptionsStorage()->deleteOption($optionId);
            echo $result;
        }
    }
}
