<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 16.06.15
 * Time: 23:24
 */

abstract class Auth_Model_Resource_Abstract implements Zend_Acl_Resource_Interface {

    /** @var int|null  */
    private $memberId = null;

    /** @var array|int|null */
    private $alternativeMemberId = null;

    /** @var string ID der aktuellen Resource in der ACL */
    protected $resourceId = null;

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
        return $this->memberId;
    }

    /**
     * @param int $memberId
     *
     * @return $this
     */
    public function setMemberId($memberId)
    {
        $this->memberId = $memberId;
        return $this;
    }

    /**
     * @return array|int|null
     */
    public function getAlternativeMemberId() {
        return $this->alternativeMemberId;
    }

    /**
     * @param array|int|null $alternativeMemberId
     *
     * @return $this
     */
    public function setAlternativeMemberId($alternativeMemberId) {
        $this->alternativeMemberId = $alternativeMemberId;
        return $this;
    }

    /**
     * @return string
     */
    public function getResourceId()
    {
        return $this->resourceId;
    }

    /**
     * @param string $resourceId
     *
     * @return $this
     */
    public function setResourceId($resourceId)
    {
        $this->resourceId = $resourceId;
        return $this;
    }
}