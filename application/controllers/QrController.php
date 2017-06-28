<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 25.05.14
 * Time: 23:02
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

require_once APPLICATION_PATH . "/../library/qrlib/qrlib.php";
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

/**
 * Class QrController
 */
class QrController extends AbstractController
{
    /**
     * get image for url action
     *
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
