<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.06.15
 * Time: 09:45
 */

class Auth_Model_Resource_DeviceOptions extends Auth_Model_Resource_Abstract {

    /** @var string ID der aktuellen Resource in der ACL */
    protected $resourceId = 'default:device-options';

    protected function _prepareData($oRow)
    {
        $this->setMemberId(CAD_Tool_Extractor::extractOverPath($oRow, 'device_option_create_user_fk'));
    }
}