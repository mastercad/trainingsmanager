<?php

require_once(APPLICATION_PATH . '/controllers/AbstractController.php');

class CmsController extends AbstractController {

    public function indexAction() {
    }

    public function getEditorTemplateAction() {
    }

    public function getUbbReplacedContentAction() {
        $req = $this->getRequest();
        $a_params = $req->getParams();

        if (true === isset($a_params['content'])) {
            $str_content = base64_decode($a_params['content']);

            $obj_filter_ubb_replacer = new CAD_Filter_UbbReplace();

            if (true === isset($a_params['bild_pfad'])) {
                $obj_filter_ubb_replacer->setBilderPfad(base64_decode($a_params['bild_pfad']));
            }
            if (true === isset($a_params['bild_temp_pfad'])) {
                $obj_filter_ubb_replacer->setTempBilderPfad(base64_decode($a_params['bild_temp_pfad']));
            }
            $str_replaced_content = $obj_filter_ubb_replacer->filter($str_content);

            $a_content = array();
            $a_content['str_replaced_content'] = base64_encode($str_replaced_content);

            if (true === isset($a_params['language'])
                && "PHP" === strtoupper(base64_decode($a_params['language']))
            ) {

                ob_start();

                echo eval('?>' . $str_content . '<?php ');

                $a_content['str_beispiel_content'] = base64_encode(ob_get_contents());
                ob_end_clean();
            } else {
                $a_content['str_beispiel_content'] = $a_params['content'];
            }
            $this->view->assign('a_content', $a_content);
        }
    }
}