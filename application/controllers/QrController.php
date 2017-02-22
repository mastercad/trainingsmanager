<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 25.05.14
 * Time: 23:02
 */

require_once APPLICATION_PATH . "/../library/qrlib/qrlib.php";

/**
 * Class QrController
 */
class QrController extends Zend_Controller_Action
{
    /**
     *
     */
    public function __init()
    {
        $a_params = $this->getRequest()->getParams();

        if(isset($a_params['ajax']))
        {
        }
    }

    /**
     * @param string $sUrl
     */
    public function getImageForUrlAction($sUrl = 'TESTIMAGE!')
    {
        $aParams = $this->getAllParams();
        if (array_key_exists('url', $aParams)) {
            $sUrl = base64_decode($aParams['url']);
        }

        $this->view->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();
        QRcode::png($sUrl);
    }
}
