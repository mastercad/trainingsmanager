<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 09:38
 */

class Model_Entity_Message extends Model_Entity_AbstractEntity {

    const STATUS_OK = 1;
    const STATUS_NOTICE = 2;
    const STATUS_WARN = 3;
    const STATUS_ERROR = 4;
    const STATUS_CRITICAL = 5;

    protected $map = [
        'state' => 'status',
        'text' => 'message'
    ];

    /** @var string status of message */
    private $status = null;

    private $message = null;

    private $jsCallBack = null;

    private $htmlContent = null;

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return null
     */
    public function getMessage() {
        return $this->message;
    }

    /**
     * @param null $message
     */
    public function setMessage($message) {
        $this->message = $message;
    }

    /**
     * @return null
     */
    public function getJsCallBack() {
        return $this->jsCallBack;
    }

    /**
     * @param null $jsCallBack
     */
    public function setJsCallBack($jsCallBack) {
        $this->jsCallBack = $jsCallBack;
    }

    /**
     * @return null
     */
    public function getHtmlContent() {
        return $this->htmlContent;
    }

    /**
     * @param null $htmlContent
     */
    public function setHtmlContent($htmlContent) {
        $this->htmlContent = $htmlContent;
    }


}