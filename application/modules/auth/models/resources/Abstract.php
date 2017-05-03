<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 16.06.15
 * Time: 23:24
 */

abstract class Auth_Model_Resource_Abstract implements Zend_Acl_Resource_Interface {

    private $_iMemberId = null;

    /** @var string ID der aktuellen Resource in der ACL */
    protected $_sResourceId = null;

    public function __construct($oRow = null) {
        if (null !== $oRow) {
            $this->_prepareData($oRow);
        }
    }

    protected function _prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'rezept_eintrag_user_fk'));
    }

    /**
     * @return null
     */
    public function getMemberId()
    {
        return $this->_iMemberId;
    }

    /**
     * @param null $iMemberId
     */
    public function setMemberId($iMemberId)
    {
        $this->_iMemberId = $iMemberId;
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return $this->_sResourceId;
    }

    /**
     * @param string $sResourceId
     */
    public function setResourceId($sResourceId)
    {
        $this->_sResourceId = $sResourceId;
    }
}