<?php


namespace Model\DbTable;

use Zend_Db_Table_Row_Abstract;
use Zend_Db_Table_Rowset_Abstract;
use Nette\NotImplementedException;
use Zend_Db_Table;
use Exception;

/**
 * Class Application_Model_DbTable_Exercises
 */
class ExerciseTypes extends AbstractDbTable
{
    /** @var string */
    protected $_name 	= 'exercise_types';

    /** @var string */
    protected $_primary = 'exercise_type_id';

    /**
     * @inheritdoc
     */
    function findByPrimary($id) {
        throw new NotImplementedException('Function findByPrimary not implemented yet!');
    }

    /**
     * search all available exercises
     *
     * @return array|bool
     */
    public function findAllExerciseTypes() {
        return $this->fetchAll(null, 'exercise_type_name');
    }

    /**
     * search all matching exercises by name piece
     *
     * @param string $sExerciseTypeNamePiece
     *
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findExerciseTypeByName($sExerciseTypeNamePiece) {
        return $this->fetchRow("exercise_type_name LIKE('" . $sExerciseTypeNamePiece . "')", 'exercise_type_name');
    }

    /**
     * search exercise by id
     *
     * @param int $iExerciseTypeId
     *
     * @return bool|null|Zend_Db_Table_Row_Abstract
     */
    public function findExerciseTypeById($iExerciseTypeId) {
        $oSelect = $this->select(Zend_Db_Table::SELECT_WITH_FROM_PART)->setIntegrityCheck(false);
        try {
            $oSelect->where('exercise_type_id = ?', $iExerciseTypeId);

            return $this->fetchRow($oSelect);
        } catch(Exception $oExceptions) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oExceptions->getMessage() . "<br />";
            return false;
        }
    }

    /**
     * @param array $aData
     *
     * @return bool|mixed
     */
    public function saveExerciseType($aData) {
		try {
            return $this->insert($aData);
		} catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
		}
	}

    /**
     * update exercise by id
     *
     * @param array $aData
     * @param int $iExerciseTypeId
     *
     * @return bool|int
     */
    public function updateExerciseType($aData, $iExerciseTypeId) {
		try {
            return $this->update( $aData, "exercise_type_id = '" . $iExerciseTypeId . "'");
		} catch(Exception $oException) {
			echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
			echo "Meldung : " . $oException->getMessage() . "<br />";
			return false;
		}
	}

    /**
     * delete exercise by id
     *
     * @param int $iExerciseTypeId
     *
     * @return bool|int
     */
    public function deleteExerciseType($iExerciseTypeId) {
        try {
            return $this->delete("exercise_type_id = '" . $iExerciseTypeId . "'");
        } catch(Exception $oException) {
            echo "Fehler in " . __FUNCTION__ . " der Klasse " . __CLASS__ . "<br />";
            echo "Meldung : " . $oException->getMessage() . "<br />";
            return false;
        }
	}
}
