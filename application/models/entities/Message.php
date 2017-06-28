<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 09:38
 */

namespace Model\Entity;

/**
 * Class Message
 *
 * @package Model\Entity
 */
class Message extends AbstractEntity
{

    const STATUS_OK = 1;
    const STATUS_NOTICE = 2;
    const STATUS_WARN = 3;
    const STATUS_ERROR = 4;
    const STATUS_CRITICAL = 5;

    protected $map = [
        'state' => 'state',
        'text' => 'message'
    ];

    /**
     * @return string
     */
    public function getState() 
    {
        return $this->get('state');
    }

    /**
     * @param string $state
     *
     * @return $this;
     */
    public function setState($state) 
    {
        return $this->set('state', $state);
    }

    /**
     * @return string
     */
    public function getMessage() 
    {
        return $this->get('message');
    }

    /**
     * @param string $message
     *
     * @return $this
     */
    public function setMessage($message) 
    {
        return $this->set('message', $message);
    }

    /**
     * @return string|null
     */
    public function getJsCallBack() 
    {
        return $this->get('jsCallBack');
    }

    /**
     * @param string $jsCallBack
     *
     * @return $this
     */
    public function setJsCallBack($jsCallBack) 
    {
        return $this->set('jsCallBack', $jsCallBack);
    }

    /**
     * @return string
     */
    public function getHtmlContent() 
    {
        return $this->get('htmlContent');
    }

    /**
     * @param string $htmlContent
     *
     * @return $this
     */
    public function setHtmlContent($htmlContent) 
    {
        return $this->set('htmlContent', $htmlContent);
    }

    /**
     * @return null|int
     */
    public function getRedirectId() 
    {
        return $this->get('id');
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setRedirectId($id) 
    {
        return $this->set('id', $id);
    }
}