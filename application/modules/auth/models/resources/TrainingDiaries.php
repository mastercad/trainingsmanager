<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

namespace Auth\Model\Resource;

use CAD_Tool_Extractor;

class TrainingDiaries extends AbstractResource
{

    /**
     * @var string ID der aktuellen Resource in der ACL 
     */
    protected $resourceId = 'default:training-diaries';

    protected function prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'training_diary_create_user_fk'));
        $this->setAlternativeMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'training_plan_user_fk'));
    }
}