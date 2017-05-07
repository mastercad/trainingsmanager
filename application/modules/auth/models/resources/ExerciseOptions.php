<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

class Auth_Model_Resource_ExerciseOptions extends Auth_Model_Resource_Abstract {

    /** @var string ID der aktuellen Resource in der ACL */
    protected $resourceId = 'default:exercise-options';

    protected function _prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'exercise_option_create_user_fk'));
    }
}