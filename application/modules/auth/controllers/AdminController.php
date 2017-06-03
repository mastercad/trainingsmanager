<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 30.04.15
 * Time: 20:30
 */

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class Auth_AdminController extends AbstractController {
    private $_aUserRechteGruppenRechte;
    private $_iCurrentRechteUserGruppenRechtId;
    private $_iInputCount = 0;
    private $userRightGroupsHierarchy = [];
    private $userRightGroups = [];

    public function init() {
        $userRightGroupsDb = new Auth_Model_DbTable_UserRightGroups();
        $userRightGroups = $userRightGroupsDb->findUserRightGroups();

        foreach ($userRightGroups as $userRightGroup) {
            $this->userRightGroups[$userRightGroup->offsetGet('user_right_group_id')] = $userRightGroup->offsetGet('user_right_group_name');
        }
    }

    public function indexAction() {
        $aMoCcAcData = CAD_Tool_ModuleControllerActionLister::collect();
        $userRightGroupsDb = new Model_DbTable_UserRightGroups();
        $userRightGroup = null;
        $userRightGroupRightDb = new Auth_Model_DbTable_UserRightGroupRights();
        $iUserRechteGruppeId = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_id', 1);
        $iUserRechteGruppeParentId = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_parent_id');
        $sSpeichern = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->save');

        if (0 < $iUserRechteGruppeId) {
            $userRightGroup = $userRightGroupsDb->findByPrimary($iUserRechteGruppeId);
            if (empty($iUserRechteGruppeParentId)) {
                $iUserRechteGruppeParentId = $userRightGroup->user_right_group_parent_fk;
            }
        }

        if (null !== $sSpeichern) {
            $currentUserRightGroupRights = $this->collectCurrentUserRightGroupRights($iUserRechteGruppeId);
            $aUserRechteGruppenRechte = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_right');
            $aUserRechteGruppenRechtAktiv = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_right_active');

            foreach ($aUserRechteGruppenRechte as $iKey => $sUserRechteGruppenRecht) {
                $sUserRechteGruppenRechtAktiv = CAD_Tool_Extractor::extractOverPath($aUserRechteGruppenRechtAktiv, $iKey);

                // right exists already in db!
                if (array_key_exists($sUserRechteGruppenRecht, $currentUserRightGroupRights)) {
                    if (empty($sUserRechteGruppenRechtAktiv)) {
                        $userRightGroupRightDb->delete('user_right_group_right_id = ' . $currentUserRightGroupRights[$sUserRechteGruppenRecht]);
                    }
                    unset($currentUserRightGroupRights[$sUserRechteGruppenRecht]);
                } else if (!empty($sUserRechteGruppenRechtAktiv)) {
                    $aData['user_right_group_right'] = $sUserRechteGruppenRecht;
                    $aData['user_right_group_fk'] = $iUserRechteGruppeId;
                    $aData['user_right_group_right_create_date'] = date('Y-m-d H:i:s');
                    $aData['user_right_group_right_create_user_fk'] = $this->findCurrentUserId();
                    $userRightGroupRightDb->insert($aData);
                    unset($currentUserRightGroupRights[$sUserRechteGruppenRecht]);
                }
            }

            // clean DB from waste data
            foreach ($currentUserRightGroupRights as $currentUserRightGroupRight => $userRightGroupRightId) {
                $userRightGroupRightDb->delete('user_right_group_right_id = ' . $userRightGroupRightId);
            }

            $aData = [
                'user_right_group_parent_fk' => $iUserRechteGruppeParentId,
                'user_right_group_update_date' => date('Y-m-d H:i:s'),
                'user_right_group_update_user_fk' => $this->findCurrentUserId(),
            ];
            $userRightGroupsDb->update($aData, 'user_right_group_id = ' . $iUserRechteGruppeId);
//            $this->initAuth();
        }
        $this->userRightGroupsHierarchy = $this->generatesUserRightGroupsHierarchy();

        $oUserRechteGruppenRechteRowSet = $userRightGroupRightDb->findUserRightGroupRights();
        $this->_aUserRechteGruppenRechte = $this->_collectUserRechteGruppenRechte($oUserRechteGruppenRechteRowSet);

        $sContent = '';

        foreach ($aMoCcAcData as $sMoCcAcDataModule => $aMoCcAcDataModule) {
            $sContent .= $this->_createModuleFieldset($sMoCcAcDataModule, $aMoCcAcDataModule, $iUserRechteGruppeId);
        }
        $this->view->assign('userRightGroupSelectContent',
            $this->generateUserRightGroupSelect($iUserRechteGruppeId, 'Rechte Gruppe', 'user_right_group'));

        $userRightGroup = $userRightGroupsDb->findByPrimary($iUserRechteGruppeId);
        $iUserRechteGruppeParentId = $userRightGroup->user_right_group_parent_fk;

        if ($userRightGroup instanceof Zend_Db_Table_Row_Abstract) {
            $this->view->assign('userInheritRightGroupSelectContent',
                $this->generateUserRightGroupSelect($userRightGroup->user_right_group_parent_fk, 'Erbt von', 'user_right_group_parent'));
        }

        $this->view->assign('iUserRechteGruppeId', $iUserRechteGruppeId);
        $this->view->assign('iUserRechteGruppeParentId', $iUserRechteGruppeParentId);
        $this->view->assign('sContent', $sContent);
    }

//    private function initAuth() {
//        $oAcl = new Auth_Plugin_Acl();
//        Zend_Registry::set('acl', $oAcl);
//    }

    private function collectCurrentUserRightGroupRights($userRightGroupId)
    {
        $userRightGroupRightDb = new Auth_Model_DbTable_UserRightGroupRights();
        $currentUserRightGroupRights = $userRightGroupRightDb->findUserRightGroupRights($userRightGroupId);
        $userRightGroupRightCollection = [];

        foreach ($currentUserRightGroupRights as $currentUserRightGroupRight) {
            $userRightGroupRightCollection[$currentUserRightGroupRight->offsetGet('user_right_group_right')] = $currentUserRightGroupRight->offsetGet('user_right_group_right_id');
        }
        return $userRightGroupRightCollection;
    }

    /**
     * @return array
     * @throws \Zend_Db_Table_Exception
     */
    private function generatesUserRightGroupsHierarchy() {
        $userRightGroupsDb = new Model_DbTable_UserRightGroups();
        $userRightGroups = $userRightGroupsDb->findAllUserRightGroups();
        $userRightGroupsHierarchy = [];

        foreach ($userRightGroups as $userRightGroup) {
            $userRightGroupsHierarchy[$userRightGroup->offsetGet('user_right_group_id')] = $userRightGroup->offsetGet('user_right_group_parent_fk');
        }

        return $userRightGroupsHierarchy;
    }

    private function generateUserRightGroupSelect($selectedUserRightGroupId, $labelText, $prefix) {

        $this->view->assign('selectedValue', $selectedUserRightGroupId);
        $this->view->assign('value', null);
        $this->view->assign('text', $this->translate('label_please_select'));
        $userRightGroupOptionsContent = $this->view->render('admin/partials/user-right-group-row.phtml');
        $userRightGroupsDb = new Model_DbTable_UserRightGroups();
        $userRightGroupsCollection = $userRightGroupsDb->findAllUserRightGroups();

        foreach ($userRightGroupsCollection as $userRightGroup) {
            $this->view->assign('value', $userRightGroup->offsetGet('user_right_group_id'));
            $this->view->assign('text', $userRightGroup->offsetGet('user_right_group_name'));
            $userRightGroupOptionsContent .= $this->view->render('admin/partials/user-right-group-row.phtml');
        }
        $this->view->assign('userRightGroupOptionsContent', $userRightGroupOptionsContent);
        $this->view->assign('prefix', $prefix);
        $this->view->assign('labelText', $labelText);
        return $this->view->render('admin/partials/user-right-group-drop-down.phtml');
    }

    private function _createModuleFieldset($sMoCcAcDataModule, $aMoCcAcDataModule, $iUserRechteGruppeId) {
        $sContent = '';
        foreach ($aMoCcAcDataModule as $sMoCcAcDataController => $aMoCcAcDataController) {
            $this->view->assign('sModuleName', $sMoCcAcDataModule);
            $sContent .= $this->_createControllerInputContent($sMoCcAcDataModule, $sMoCcAcDataController, $aMoCcAcDataController, $iUserRechteGruppeId);
        }
        $this->view->assign('sModuleContent', $sContent);
        return $this->view->render('admin/partials/module-fieldset.phtml');
    }

    private function _createControllerInputContent($sMoCcAcDataModule, $sMoCcAcDataController, $aMoCcAcDataController, $iUserRechteGruppeId) {
        $sContent = '';
        $bGlobaleControllerChecked = false;
        $this->_iCurrentRechteUserGruppenRechtId = null;
        $iGlobalUserGruppenRechtId = null;
        $bGlobalControllerRightInherited = false;
        $sGlobalControllerTitle = '';

        $this->view->assign('sControllerName', $sMoCcAcDataController);

        $rightAvailableInRightGroups = $this->_checkResourceExistsInRightGroups($this->_aUserRechteGruppenRechte, $sMoCcAcDataModule . '|' . $sMoCcAcDataController . '|' . $sMoCcAcDataAction);

        if (!empty($rightAvailableInRightGroups)) {
            if (array_key_exists($iUserRechteGruppeId, $rightAvailableInRightGroups)) {
                $bGlobalControllerRightInherited = true;
            } else {
                $inherited = false;
                $inheritedRightGroupId = null;
                foreach ($rightAvailableInRightGroups as $userRightGroupId => $data) {
                    if ($this->checkCurrentUserRightGroupInheritFromUserRightGroup($iUserRechteGruppeId, $userRightGroupId)) {
                        $inherited = true;
                        $inheritedRightGroupId = $userRightGroupId;
                        break;
                    }
                }

                if ($inherited) {
                    $bGlobalControllerRightInherited = true;
                    $sGlobalControllerTitle = "Erbt von " . $this->userRightGroups[$inheritedRightGroupId];
                }
            }
        }

        foreach ($aMoCcAcDataController as $sMoCcAcDataAction) {
            $this->_iCurrentRechteUserGruppenRechtId = null;
            $rightAvailableInRightGroups = $this->_checkResourceExistsInRightGroups($this->_aUserRechteGruppenRechte, $sMoCcAcDataModule . '|' . $sMoCcAcDataController . '|' . $sMoCcAcDataAction);
            $bActionChecked = false;
            $bActionRightInherited = false;
            $sTitle = '';
            if (!empty($rightAvailableInRightGroups)) {
                if (array_key_exists($iUserRechteGruppeId, $rightAvailableInRightGroups)) {
                    $bActionChecked = true;
                } else {
                    $inherited = false;
                    $inheritedRightGroupId = null;
                    foreach ($rightAvailableInRightGroups as $userRightGroupId => $data) {
                        if ($this->checkCurrentUserRightGroupInheritFromUserRightGroup($iUserRechteGruppeId, $userRightGroupId)) {
                            $inherited = true;
                            $inheritedRightGroupId = $userRightGroupId;
                            break;
                        }
                    }

                    if ($inherited) {
                        $bActionChecked = true;
                        $bActionRightInherited = true;
                        $sTitle = "Erbt von " . $this->userRightGroups[$inheritedRightGroupId];
                    }
                }
            }
            $this->view->assign('sTitle', $sTitle);
            $this->view->assign('sActionName', $sMoCcAcDataAction);
            $this->view->assign('bActionChecked', $bActionChecked);
            $this->view->assign('iGlobalUserGruppenRechtId', $iGlobalUserGruppenRechtId);
            $this->view->assign('bGlobalControllerRightInherited', $bGlobalControllerRightInherited);
            $this->view->assign('sGlobalControllerTitle', $sGlobalControllerTitle);
            $this->view->assign('bActionRightInherited', $bActionRightInherited);
            $this->view->assign('iUserRechteGruppenRechtId', $this->_iCurrentRechteUserGruppenRechtId);
            $this->view->assign('iInputCount', $this->_iInputCount);
            $sContent .= $this->view->render('admin/partials/action-input.phtml');
            $this->_iInputCount++;
        }
        $this->view->assign('bGlobalControllerChecked', $bGlobaleControllerChecked);
        $this->view->assign('sControllerContent', $sContent);
        $this->view->assign('iInputCount', $this->_iInputCount);
        $this->_iInputCount++;
        return $this->view->render('admin/partials/controller-input.phtml');
    }

    private function checkCurrentUserRightGroupInheritFromUserRightGroup($currentUserRightGroupId, $userRightGroupId) {
        // current ID exists in userRightGroups hierarchy
        if (array_key_exists($currentUserRightGroupId, $this->userRightGroupsHierarchy)
            && $this->userRightGroupsHierarchy[$currentUserRightGroupId] != $userRightGroupId
        ) {
            return $this->checkCurrentUserRightGroupInheritFromUserRightGroup($this->userRightGroupsHierarchy[$currentUserRightGroupId], $userRightGroupId);
        } else if (array_key_exists($currentUserRightGroupId, $this->userRightGroupsHierarchy)
            && ($this->userRightGroupsHierarchy[$currentUserRightGroupId] == $userRightGroupId
//                || 0 == $this->userRightGroupsHierarchy[$currentUserRightGroupId]
            )
        ) {
            return true;
        }
        return false;
    }

    private function _collectUserRechteGruppenRechte($oUserRechteGruppenRechteRowSet) {
        $aUserRechteGruppenRechte = array();
        foreach ($oUserRechteGruppenRechteRowSet as $oUserRechteGruppenRechteRow) {
            if (false === array_key_exists($oUserRechteGruppenRechteRow->user_right_group_fk, $aUserRechteGruppenRechte)) {
                $aUserRechteGruppenRechte[$oUserRechteGruppenRechteRow->user_right_group_fk] = array();
            }
            $aResource = explode(':', $oUserRechteGruppenRechteRow->user_right_group_right);

            if (false === array_key_exists($aResource[0], $aUserRechteGruppenRechte[$oUserRechteGruppenRechteRow->user_right_group_fk])) {
                $aUserRechteGruppenRechte[$oUserRechteGruppenRechteRow->user_right_group_fk][$aResource[0]] = array();
            }

            if (false === array_key_exists($aResource[1], $aUserRechteGruppenRechte[$oUserRechteGruppenRechteRow->user_right_group_fk][$aResource[0]])) {
                $aUserRechteGruppenRechte[$oUserRechteGruppenRechteRow->user_right_group_fk][$aResource[0]][$aResource[1]] = array();
            }
            $aUserRechteGruppenRechte[$oUserRechteGruppenRechteRow->user_right_group_fk][$aResource[0]][$aResource[1]][$aResource[2]] = $oUserRechteGruppenRechteRow->user_right_group_right_id;
        }

        ksort($aUserRechteGruppenRechte);
        return $aUserRechteGruppenRechte;
    }

    private function _checkResourceExistsInRightGroups($aUserRechteGruppe, $sResource) {
        krsort($aUserRechteGruppe);
        $rightAvailableInGroups = [];

        foreach ($aUserRechteGruppe as $iUserRechteGruppeId => $aUserRechteGruppeRechte) {
            $mReturn = $this->_searchResourceByPath($aUserRechteGruppeRechte, $sResource);
            if (null !== $mReturn) {
                $rightAvailableInGroups[$iUserRechteGruppeId] = $mReturn;
//                $this->_iCurrentRechteUserGruppenRechtId = $mReturn;
//                return $iUserRechteGruppeId;
            }
        }
        return $rightAvailableInGroups;
    }

    private function _searchResourceByPath($aUserRechteGruppe, $sResource) {
        $mReturn = null;
        $aPath = explode('|', $sResource);
        $sKey = array_shift($aPath);

        if (array_key_exists($sKey, $aUserRechteGruppe)) {
            if (true === is_numeric($aUserRechteGruppe[$sKey])) {
                $mReturn = $aUserRechteGruppe[$sKey];
            } else if (true === is_array($aUserRechteGruppe[$sKey])) {
                $mReturn = $this->_searchResourceByPath($aUserRechteGruppe[$sKey], implode('|', $aPath));
            }
        }
        return $mReturn;
    }
}
