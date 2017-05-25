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

    public function indexAction() {
        $aMoCcAcData = CAD_Tool_ModuleControllerActionLister::collect();
        $oUserRechteGruppenRechteDbTable = new Auth_Model_DbTable_UserRightGroupRights();
        $iUserRechteGruppeId = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_id', 4);
        $sSpeichern = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->save');
        $this->userRightGroupsHierarchy = $this->generatesUserRightGroupsHierarchy();

        if (null !== $sSpeichern) {
            $aUserRechteGruppenRechte = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_right');
            $aUserRechteGruppenRechtId = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_right_id');
            $aUserRechteGruppenRechtAktiv = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_right_active');
            $aUserRechteGruppenRechtAktivOrig = CAD_Tool_Extractor::extractOverPath($this, 'getRequest->getParams->user_right_group_right_active_orig');

            foreach ($aUserRechteGruppenRechte as $iKey => $sUserRechteGruppenRecht) {
                $iUserRechteGruppenRechtId = CAD_Tool_Extractor::extractOverPath($aUserRechteGruppenRechtId, $iKey);
                $sUserRechteGruppenRechtAktiv = CAD_Tool_Extractor::extractOverPath($aUserRechteGruppenRechtAktiv, $iKey);
                $sUserRechteGruppenRechtAktivOrig = CAD_Tool_Extractor::extractOverPath($aUserRechteGruppenRechtAktivOrig, $iKey);

                // wenn sich der status geändert hat
                if ($sUserRechteGruppenRechtAktiv != $sUserRechteGruppenRechtAktivOrig) {
                    $aData = array(
                        'user_right_group_right' => $sUserRechteGruppenRecht,
                        'user_right_group_fk' => $iUserRechteGruppeId,
                    );
                    // recht existiert bereits in der DB
                    if (0 < strlen(trim($iUserRechteGruppenRechtId))) {
                        $oUserRechteGruppenRechteDbTable->delete('user_right_group_right_id = ' . $iUserRechteGruppenRechtId);
                    } else {
                        $aData['user_right_group_right_create_date'] = date('Y-m-d H:i:s');
                        $aData['user_right_group_right_create_user_fk'] = $this->findCurrentUserId();
                        $oUserRechteGruppenRechteDbTable->insert($aData);
                    }
                }
            }
        }

        $oUserRechteGruppenRechteRowSet = $oUserRechteGruppenRechteDbTable->getUserRightGroupRights();
        $this->_aUserRechteGruppenRechte = $this->_collectUserRechteGruppenRechte($oUserRechteGruppenRechteRowSet);

        $sContent = '';

        foreach ($aMoCcAcData as $sMoCcAcDataModule => $aMoCcAcDataModule) {
            $sContent .= $this->_createModuleFieldset($sMoCcAcDataModule, $aMoCcAcDataModule, $iUserRechteGruppeId);
        }
        $this->view->assign('userRightGroupSelectContent', $this->generateUserRightGroupSelect($iUserRechteGruppeId));
        $this->view->assign('iUserRechteGruppeId', $iUserRechteGruppeId);
        $this->view->assign('sContent', $sContent);
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

    private function generateUserRightGroupSelect($selectedUserRightGroupId) {

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

        $iUserRechteGruppeFk = $this->_checkResourceExistsInRightGroups($this->_aUserRechteGruppenRechte, $sMoCcAcDataModule . '|' . $sMoCcAcDataController . '|*');

        if (false !== $iUserRechteGruppeFk) {
            $bGlobaleControllerChecked = true;

            if ($iUserRechteGruppeFk == $iUserRechteGruppeId) {
                $iGlobalUserGruppenRechtId = $this->_iCurrentRechteUserGruppenRechtId;
            } else if ($iUserRechteGruppeFk < $iUserRechteGruppeId) {
                $bGlobalControllerRightInherited = true;
                $sGlobalControllerTitle = "Erbt von Gruppe " . $iUserRechteGruppeFk;
                $iGlobalUserGruppenRechtId = $this->_iCurrentRechteUserGruppenRechtId;
            } else {
                $this->_iCurrentRechteUserGruppenRechtId = null;
                $bGlobaleControllerChecked = false;
            }
        }

        foreach ($aMoCcAcDataController as $sMoCcAcDataAction) {
            $this->_iCurrentRechteUserGruppenRechtId = null;
            $iUserRechteGruppeFk = $this->_checkResourceExistsInRightGroups($this->_aUserRechteGruppenRechte, $sMoCcAcDataModule . '|' . $sMoCcAcDataController . '|' . $sMoCcAcDataAction);
            $bActionChecked = false;
            $bActionRightInherited = false;
            $sTitle = '';
            if (false !== $iUserRechteGruppeFk) {
                if ($this->checkCurrentUserRightGroupInheritFromUserRightGroup($iUserRechteGruppeId, $iUserRechteGruppeFk)) {
//                if ($iUserRechteGruppeFk < $iUserRechteGruppeId) { // @todo hier muss auf die tatsächliche vererbung eingegangen werden!
                    $bActionChecked = true;
                    $bActionRightInherited = true;
                    $this->_iCurrentRechteUserGruppenRechtId = null;
                    $sTitle = "Erbt von Gruppe " . $iUserRechteGruppeFk;
                } else if ($iUserRechteGruppeFk == $iUserRechteGruppeId) {
                    $bActionChecked = true;
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
        foreach ($aUserRechteGruppe as $iUserRechteGruppeId => $aUserRechteGruppeRechte) {
            $mReturn = $this->_searchResourceByPath($aUserRechteGruppeRechte, $sResource);
            if (null !== $mReturn) {
                $this->_iCurrentRechteUserGruppenRechtId = $mReturn;
                return $iUserRechteGruppeId;
            }
        }
        return false;
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
