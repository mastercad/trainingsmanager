<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 23.04.17
 * Time: 10:06
 */

namespace Model\Collection;

use Model\Entity\Message;

/**
 * Class AbstractCollection
 *
 * @package Model\Collection
 */
abstract class AbstractCollection
{

    /**
     * @var array  
     */
    private $data = [];

    /**
     * @param $messages
     */
    public function __construct($messages) 
    {
        if (is_array($messages)) {
            foreach ($messages as $message) {
                $this->addMessage(new Message($message));
            }
        }
    }

    /**
     * adds message to collection
     *
     * @param \Model\Entity\Message $message
     *
     * @return $this
     */
    public function addMessage(Message $message) 
    {
        $this->data[] = $message;

        return $this;
    }

    /**
     * @return array
     */
    protected function getData() 
    {
        return $this->data;
    }

    /**
     * @param $data
     *
     * @return $this
     */
    protected function setData($data) 
    {
        $this->data = $data;
        return $this;
    }
}