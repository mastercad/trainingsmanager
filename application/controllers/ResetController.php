<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.05.17
 * Time: 21:55
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */
require_once APPLICATION_PATH . '/controllers/AbstractController.php';

use Service\Reset;

/**
 * Class ResetController
 */
class ResetController extends AbstractController
{

    public function indexAction() 
    {
        $resetService = new Reset();
        $resetService->cleanTestActivities();
    }
}