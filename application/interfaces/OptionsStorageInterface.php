<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 19.04.17
 * Time: 17:23
 */
namespace Interfaces;

interface OptionsStorageInterface
{

    /**
     * find all available options in current table
     *
     * @return \Zend_Db_Table_Rowset
     */
    public function findAllOptions();

    /**
     * find option in current table by given id
     *
     * @param integer $optionId
     *
     * @return \Zend_Db_Table_Row
     */
    public function findOptionById($optionId);

    /**
     * update option with given data by given id
     *
     * @param array   $data
     * @param integer $optionId
     *
     * @return bool|integer
     */
    public function updateOption($data, $optionId);

    /**
     * delete given option from table
     *
     * @param integer $optionId
     *
     * @return bool|integer
     */
    public function deleteOption($optionId);

    /**
     * insert option with given data
     *
     * @param array $data
     *
     * @return bool|integer
     */
    public function insertOption($data);
}