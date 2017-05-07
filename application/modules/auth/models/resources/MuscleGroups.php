<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

class Auth_Model_Resource_MuscleGroups extends Auth_Model_Resource_Abstract {

    /** @var string ID der aktuellen Resource in der ACL */
    protected $resourceId = 'default:muscle-groups';

    protected function _prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'muscle_group_create_user_fk'));
    }
}