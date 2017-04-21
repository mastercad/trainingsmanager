<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.04.17
 * Time: 17:23
 */

interface Interface_OptionsStorageInterface {

    /**
     * @return Zend_Db_Table_Rowset
     */
    public function findAllOptions();

    /**
     * @param integer $optionId
     *
     * @return Zend_Db_Table_Row
     */
    public function findOptionById($optionId);

    /**
     * @param array $data
     * @param integer $optionId
     *
     * @return bool|integer
     */
    public function updateOption($data, $optionId);

    /**
     * @param integer $optionId
     *
     * @return bool|integer
     */
    public function deleteOption($optionId);

    /**
     * @param array $data
     *
     * @return bool|integer
     */
    public function insertOption($data);
}