<?php
    ini_set('display_errors', 1); // set to 0 when not debugging
//    ini_set('memory_limit', '200M');
    error_reporting(E_ALL);

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

/**
 * Class ButlerController
 */
class ButlerController extends AbstractController
{

    /**
     *
     */
    public function createThumbAction()
    {
        $req = $this->getRequest();
        $a_params = $req->getParams();

        $file = '';
        if (isset($a_params['file'])) {
            $file = $a_params['file'];
            $bError = FALSE;
            $sErrorMessage = 'Es ist ein Fehler aufgetreten!';

            /**
             * wenn keine slashes und punkte im namen enthalten sind, davon
             * ausgehen, dass es sich um einen base64 encodierten string
             * handelt
             */
            if (!preg_match('/[\/|\.]/', $file)) {
                $file = base64_decode($file);
            }

            if (!file_exists($file) ||
                !is_file($file) ||
                !is_readable($file)
            ) {
                $bError = TRUE;
                $sErrorMessage = "Datei " . $file . " nicht vorhanden, oder nicht lesbar!";
            }
        } else {
            $bError = TRUE;
            $sErrorMessage = 'Falsche Parameterübergabe!';
        }

        if (FALSE === $bError) {
            $a_fileinformationen = getimagesize($file);

            $orig_width = $a_fileinformationen[0];
            $orig_height = $a_fileinformationen[1];

            $thumb_width = null;
            $thumb_height = null;

            $new_width = null;
            $new_height = null;

            $ratio = $orig_width / $orig_height;

            $faktor_width = 1;
            $faktor_height = 1;

            $dest_x = 0;
            $dest_y = 0;
            $src_x = 0;
            $src_y = 0;

            /**
             * die prioritäten können gesetzt werden, um bei angabe von
             * breite UND höhe des thumbs sicher zu stellen, das nach einer der
             * beiden masse der faktor berechnet wird
             */
            $b_prio_width = false;
            $b_prio_height = false;

            $type = $a_fileinformationen[2];

            if (isset($a_params['width'])) {
                $thumb_width = $a_params['width'];
            }

            if (isset($a_params['height'])) {
                $thumb_height = $a_params['height'];
            }

            if (!$thumb_width &&
                $thumb_height
            ) {
                $thumb_width = $thumb_height / $ratio;
            }
            if (!$thumb_height &&
                $thumb_width
            ) {
                $thumb_height = $thumb_width / $ratio;
            }

            if (!$thumb_width &&
                !$thumb_height
            ) {
                $thumb_width = $orig_width - 1;
                $thumb_height = $orig_height - 1;
            }

            if (isset($a_params['prio_width']) ||
                !isset($a_params['height'])
            ) {
                $b_prio_width = true;
            } else if (isset($a_params['prio_height']) ||
                !isset($a_params['width'])
            ) {
                $b_prio_height = true;
            }

            if ($orig_width > $thumb_width) {
                $faktor_width = $thumb_width / $orig_width;
            }

            if ($orig_height > $thumb_height) {
                $faktor_height = $thumb_height / $orig_height;
            }

            if ($b_prio_width) {
                $new_height = $orig_height * $faktor_width;
                $new_width = $orig_width * $faktor_width;
            } else {
                $new_height = $orig_height * $faktor_height;
                $new_width = $orig_width * $faktor_height;
            }

            if ($new_height < $thumb_height) {
                $dest_y = ($thumb_height - $new_height) / 2;
            } else {
                $thumb_height = $new_height;
            }

            if ($new_width < $thumb_width) {
                $dest_x = ($thumb_width - $new_width) / 2;
            } else {
                $thumb_width = $new_width;
            }
            /*
                            echo "Orig Breite : " . $orig_width . "<br />";
                            echo "Orig Höhe : " . $orig_height . "<br />";
                            echo "Thumb Breite : " . $thumb_width . "<br />";
                            echo "Thumb Höhe : " . $thumb_height . "<br />";
                            echo "Faktor Breite : " . $faktor_width . "<br />";
                            echo "Faktor Höhe : " . $faktor_height . "<br />";

                            echo "Ratio : " . $ratio . "<br />";
                            echo "Prio Breite : " . $b_prio_width . "<br />";
                            echo "Prio Höhe : " . $b_prio_height . "<br />";

                            echo "Neue Breite : " . $new_width . "<br />";
                            echo "Neue Höhe : " . $new_height . "<br />";

                            echo "Type : " . $type . "<br />";
            */
            $img_dest = imagecreatetruecolor($thumb_width, $thumb_height);

            $this->getHelper('viewRenderer')->setNoRender();

            switch ($type) {
                /* gif */
                case 1: {
                    header('Content-Type: image/gif');

                    $black = imagecolorallocate($img_dest, 0, 0, 0);
                    imagecolortransparent($img_dest, $black);

                    $img_src = @imagecreatefromgif($file);

                    /* eventuelle transparenz des originals beibehalten */
                    $transparent_index = imagecolortransparent($img_src);
                    if ($transparent_index != (-1)) {
                        @$transparent_color = imagecolorsforindex($img_src, $transparent_index);
                        @$transparent_new = imagecolorallocate($img_src, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                        $transparent_new_index = imagecolortransparent($img_src, $transparent_new);
                        imagefill($img_src, 0, 0, $transparent_new_index);
                    }

                    imagecopyresampled($img_dest, $img_src, $dest_x, $dest_y, $src_x, $src_y, $new_width, $new_height, $orig_width, $orig_height);
                    imagegif($img_dest);
                    ImageDestroy($img_src);
                    ImageDestroy($img_dest);
                    break;
                }
                /* jpeg */
                case 2: {
                    $img_src = @imagecreatefromjpeg($file);
//                          header('Content-Type: image/jpeg');
                    imagecopyresampled($img_dest, $img_src, $dest_x, $dest_y, $src_x, $src_y, $new_width, $new_height, $orig_width, $orig_height);
//                        imagejpeg($img_dest, "php://output");
                    imagejpeg($img_dest, NULL, 75);
                    ImageDestroy($img_src);
                    ImageDestroy($img_dest);
                    break;
                }
                /* png */
                case 3: {
                    header('Content-Type: image/png');
                    $black = imagecolorallocate($img_dest, 0, 0, 0);
                    imagecolortransparent($img_dest, $black);

                    $img_src = @imagecreatefrompng($file);
                    imagealphablending($img_src, false);
                    imagesavealpha($img_src, true);

                    imagecopyresampled($img_dest, $img_src, $dest_x, $dest_y, $src_x, $src_y, $new_width, $new_height, $orig_width, $orig_height);
                    imagepng($img_dest);
                    ImageDestroy($img_src);
                    ImageDestroy($img_dest);
                    break;
                }
                /* swf */
                // 	    		case 4:
                // 	    			{
                // 	    				break;
                // 	    			}
                default: {
                    $black = imagecolorallocate($img_dest, 0, 0, 0);
                    imagecolortransparent($img_dest, $black);
                    header('Content-Type: image/png');
                    imagestring($img_dest, 1, 1, $thumb_height / 2, "Fehler beim Verarbeiten", $black);
                    imagepng($img_dest);
                    break;
                }
            }
        } else {
            $thumb_width = 200;
            $thumb_height = 100;

            if (isset($a_params['width'])) {
                $thumb_width = $a_params['width'];
            }

            if (isset($a_params['height'])) {
                $thumb_height = $a_params['height'];
            }

            $this->getHelper('viewRenderer')->setNoRender();
            header('Content-Type: image/png');

            $img_dest = imagecreatetruecolor($thumb_width, $thumb_height);
            $black = imagecolorallocate($img_dest, 0, 0, 0);
            imagecolortransparent($img_dest, $black);
            imagestring($img_dest, 1, 1, $thumb_height / 2, $sErrorMessage, $black);
            imagepng($img_dest);
        }
    }

    /**
     *
     */
    public function postDispatch() {
        $req = $this->getRequest();
        $a_params = $req->getParams();
        /*
        if(isset($a_params['ajax']))
        {
            $this->view->layout()->disableLayout();
        }
        */
        $this->view->layout()->disableLayout();
    }
}

