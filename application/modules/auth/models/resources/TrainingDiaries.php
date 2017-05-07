<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

class Auth_Model_Resource_TrainingDiaries extends Auth_Model_Resource_Abstract {

    /** @var string ID der aktuellen Resource in der ACL */
    protected $resourceId = 'default:training-diaries';

    protected function _prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'training_diary_create_user_fk'));
        $this->setAlternativeMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'training_plan_user_fk'));
    }
}