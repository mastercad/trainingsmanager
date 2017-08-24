<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 18.05.17
 * Time: 20:06
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

use Service\Generator\Thumbnail;

/**
 * Class ButlerController
 */
class ButlerController extends AbstractController
{
    /**
     * create thumb action
     *
     * this action generates image
     */
    public function createThumbAction()
    {
        $this->view->layout()->disableLayout();
        $this->getHelper('viewRenderer')->setNoRender();

        $thumbnailService = new Thumbnail();
        $thumbnailService->generate($this->getAllParams());
    }

    /**
     * create image string action
     *
     * this action generates source string
     */
    public function createImageStringAction()
    {

        $this->view->layout()->disableLayout();

        $thumbnailService = new Thumbnail();
        $imageSourceData = $thumbnailService->generateImageString($this->getAllParams());

        $this->view->assign('sourceData', $imageSourceData);
    }
}

