<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.03.17
 * Time: 07:39
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

use \Auth\Model\Role\Member;

/**
 * Class AbstractController
 */
abstract class AbstractController extends Zend_Controller_Action
{

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
     * post dispatch function
     */
    public function postDispatch() 
    {

        $params = $this->getRequest()->getParams();

        if(isset($params['ajax'])) {
            $this->view->layout()->disableLayout();
        }

        $this->view->headMeta()->appendName('keywords', $this->keywords);
        $this->view->headMeta()->appendName('description', $this->description);
        $this->view->assign('breadcrumb', $this->breadcrumb);
    }

    /**
     * generate detail options content
     *
     * @param $optionId
     *
     * @return string
     */
    protected function generateDetailOptionsContent($optionId) 
    {
        $currentControllerName = $this->convertControllerName($this->getRequest()->getControllerName());
        $dbClassName = '\Model\DbTable\\'.$currentControllerName;

        /**
 * @var \Model\DbTable\AbstractDbTable $db 
*/
        $db = new $dbClassName();
        $row = $db->findByPrimary($optionId);

        $content = '';
        $role = new Member();
        $resourceClassName = '\Auth\Model\Resource\\'.$currentControllerName;
        $resource = new $resourceClassName($row);
        $resourceName = $this->getRequest()->getModuleName().':'.$this->getRequest()->getControllerName();

        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'edit');
        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource($role->getRole(), $resourceName, 'delete');

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'edit')) {
            $content .= '<div class="glyphicon glyphicon-edit edit-button" data-id="' . $optionId . '"></div>';
        }

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'delete')) {
            $content .= '<div class="glyphicon glyphicon-trash delete-button" data-id="' . $optionId . '"></div>';;
        }

        return $content;
    }

    /**
     * find id for current user logged in
     *
     * @return bool
     */
    protected function findCurrentUserId() 
    {
        $user = Zend_Auth::getInstance()->getIdentity();

        if (true == is_object($user)) {
            return $user->user_id;
        }
        return false;
    }

    /**
     * convert url controller name to zend Controller Name
     *
     * @param $controllerName
     *
     * @return string
     */
    protected function convertControllerName($controllerName) 
    {
        return ucFirst(
            preg_replace_callback(
                '/(\-[a-z]{1})/', function (array $piece) {
                    return ucfirst(str_replace('-', '', $piece[1]));
                }, $controllerName
            )
        );
    }

    /**
     * translate given key
     *
     * @param $key
     *
     * @return mixed
     * @throws \Zend_Exception
     */
    protected function translate($key) 
    {
        return Zend_Registry::get('Zend_Translate')->translate($key);
    }
}