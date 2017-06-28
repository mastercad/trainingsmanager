<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 02.06.17
 * Time: 22:08
 * PHP Version: 5.5
 *
 * @category Sport
 * @package  Trainingmanager
 * @author   andreas kempe <andreas.kempe@byte-artist.de>
 * @license  GPL http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     http://www.byte-artist.de
 */

require_once APPLICATION_PATH . '/controllers/AbstractController.php';

use Service\Interpolate;

class InterpolateController extends AbstractController
{

    /**
     * index action
     */
    public function indexAction() 
    {

    }

    /**
     * interpolate training diary
     */
    public function trainingDiaryAction() 
    {
        $userId = $this->getParam('userId');

        if (0 < $userId) {
            $interpolateService = new Interpolate();
            $result = $interpolateService->trainingDiary($userId);
        }
    }
}