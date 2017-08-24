<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 24.03.17
 * Time: 19:45
 */

namespace Service\Generator\View;

use Zend_View_Abstract;
use Zend_Registry;
use Auth\Model\Role\Member;
use Model\DbTable\AbstractDbTable;

/**
 * Class GeneratorAbstract
 *
 * @package Service\Generator\View
 */
abstract class GeneratorAbstract
{

    /**
     * @var Zend_View_Abstract
     */
    private $view = null;

    /**
     * @var string name of current controller
     */
    private $controllerName = null;

    private $moduleName = null;

    private $actionName = null;

    /**
     * @return Zend_View_Abstract
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param Zend_View_Abstract $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    public function __construct(Zend_View_Abstract $view)
    {
        $this->setView($view);
    }

    /**
     * @param      $tag
     * @param null $locale
     *
     * @return mixed
     * @throws \Zend_Exception
     */
    protected function translate($tag, $locale = null)
    {
        return Zend_Registry::get('Zend_Translate')->translate($tag, $locale);
    }

    /**
     * @param $id
     *
     * @return string
     */
    protected function generateDetailOptionsContent($id)
    {
        $currentControllerName = $this->convertControllerName($this->getControllerName());
        $dbClassName = '\Model\DbTable\\'.$currentControllerName;

        /**
         * @var AbstractDbTable $db
        */
        $db = new $dbClassName();
        $row = $db->findByPrimary($id);

        $content = '';
        $role = new Member();
        $resourceClassName = '\Auth\Model\Resource\\'.$currentControllerName;
        $resource = new $resourceClassName($row);
        $resourceName = $this->getModuleName().':'.$this->getControllerName();

        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource(
            $role->getRole(),
            $resourceName,
            'edit'
        );
        Zend_Registry::get('acl')->prepareDynamicPermissionsForCurrentResource(
            $role->getRole(),
            $resourceName,
            'delete'
        );

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'edit')) {
            $content .= '<div class="glyphicon glyphicon-edit edit-button" data-id="' . $id . '"></div>';
        }

        if (Zend_Registry::get('acl')->isAllowed($role, $resource, 'delete')) {
            $content .= '<div class="glyphicon glyphicon-trash delete-button" data-id="' . $id . '"></div>';
        }

        return $content;
    }

    /**
     * @param $controllerName
     *
     * @return string
     */
    protected function convertControllerName($controllerName)
    {
        return ucFirst(
            preg_replace_callback(
                '/(\-[a-z]{1})/',
                function (array $piece) {
                    return ucfirst(str_replace('-', '', $piece[1]));
                },
                $controllerName
            )
        );
    }

    /**
     * @return string
     */
    public function getControllerName()
    {
        return $this->controllerName;
    }

    /**
     * @param string $controllerName
     *
     * @return $this;
     */
    public function setControllerName($controllerName)
    {
        $this->controllerName = $controllerName;
        return $this;
    }

    /**
     * @return null
     */
    public function getModuleName()
    {
        return $this->moduleName;
    }

    /**
     * @param null $moduleName
     *
     * @return $this;
     */
    public function setModuleName($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    /**
     * @return null
     */
    public function getActionName()
    {
        return $this->actionName;
    }

    /**
     * @param null $actionName
     *
     * @return $this;
     */
    public function setActionName($actionName)
    {
        $this->actionName = $actionName;
        return $this;
    }
}
