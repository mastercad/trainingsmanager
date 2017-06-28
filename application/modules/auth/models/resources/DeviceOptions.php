<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

namespace Auth\Model\Resource;

use CAD_Tool_Extractor;

class DeviceOptions extends AbstractResource
{

    /**
     * @var string ID der aktuellen Resource in der ACL 
     */
    protected $resourceId = 'default:device-options';

    protected function prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'device_option_create_user_fk'));
    }
}