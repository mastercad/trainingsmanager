<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 04.03.17
 * Time: 22:00
 */

namespace Service;

/**
 * Class Options
 *
 * @package Service
 */
abstract class Options
{

    /**
     * @var array
     */
    protected $hierarchy = [];

    /**
     * @var null
     */
    protected $storage = null;

    /**
     * @var
     */
    protected $storageClassName;

    /**
     * @return null
     */
    protected function getStorage()
    {
        return $this->storage;
    }

    /**
     * @param $storage
     *
     * @return $this
     */
    protected function setStorage($storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @return null
     */
    protected function useStorage()
    {
        if (is_null($this->storage)) {
            $this->storage = new $this->storageClassName();
        }
        return $this->getStorage();
    }
}