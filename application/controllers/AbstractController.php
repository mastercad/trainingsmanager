<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.03.17
 * Time: 07:39
 */

class AbstractController extends Zend_Controller_Action {

    /**
     * @var
     */
    private $breadcrumb;
    /**
     * @var
     */
    private $keywords;
    /**
     * @var
     */
    private $description;

    /**
     *
     */
    public function postDispatch() {

        $params = $this->getRequest()->getParams();

        if(isset($params['ajax'])) {
            $this->view->layout()->disableLayout();
        }

        $this->view->headMeta()->appendName('keywords', $this->keywords);
        $this->view->headMeta()->appendName('description', $this->description);
        $this->view->assign('breadcrumb', $this->breadcrumb);
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function generateDetailOptionsContent($id) {
        $currentControllerName = $this->convertControllerName($this->getRequest()->getControllerName());
        $dbClassName = 'Model_DbTable_'.$currentControllerName;
        $db = new $dbClassName();
        $row = $db->find($id)->current();

        $content = '';
        $role = new Auth_Model_Role_Member();
        $resourceClassName = 'Auth_Model_Resource_'.$currentControllerName;
        $resource = new $resourceClassName($row);
        $resourceName = $this->getRequest()->getModuleName().':'.$this->getRequest()->getControllerName();

        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'edit');
        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'delete');

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'edit')) {
            $content .= '<div class="glyphicon glyphicon-edit edit-button" data-id="' . $id . '"></div>';
        }

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'delete')) {
            $content .= '<div class="glyphicon glyphicon-trash delete-button" data-id="' . $id . '"></div>';;
        }

        return $content;
    }

    protected function findCurrentUserId() {
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            return $user->user_id;
        }
        return false;
    }

    protected function convertControllerName($controllerName) {
        return ucFirst(preg_replace_callback('/(\-[a-z]{1})/', function(array $piece) {
            return ucfirst(str_replace('-', '', $piece[1]));
        }, $controllerName));
    }

    protected function translate($key) {
        return Zend_Registry::get('Zend_Translate')->translate($key);
    }
}