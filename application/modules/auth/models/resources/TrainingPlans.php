<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

namespace Auth\Model\Resource;

use CAD_Tool_Extractor;

class TrainingPlans extends AbstractResource {

    /** @var string ID der aktuellen Resource in der ACL */
    protected $resourceId = 'default:training-plans';

    protected function prepareData($oRow) {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'training_plan_create_user_fk'));
        $this->setAlternativeMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'training_plan_user_fk'));
        $this->setGroupId(CAD_Tool_Extractor::extractOverPath($oRow, 'user_group_id'));
        $this->setGroupName(CAD_Tool_Extractor::extractOverPath($oRow, 'user_group_name'));
    }
}