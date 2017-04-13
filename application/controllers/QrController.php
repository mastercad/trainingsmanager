<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 25.05.14
 * Time: 23:02
 */

require_once APPLICATION_PATH . "/../library/qrlib/qrlib.php";
require_once(APPLICATION_PATH . '/controllers/AbstractController.php');


/**
 * Class QrController
 */
class QrController extends AbstractController
{
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
