<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 14.05.17
 * Time: 22:00
 */

class Service_Reset {

    private $testUserCollection = [];

    /**
     * removes all activities from test user login
     */
    public function cleanTestActivities() {

        $this->collectCurrentTestUsers();
        $this->cleanExerciseUploadPictures();
        $this->cleanDeviceUploadPictures();
        $this->resetDatabase();
    }

    /**
     * collect user ids to remove specific data from tables and folders
     */
    private function collectCurrentTestUsers() {
        $usersDb = new Model_DbTable_Users();
        $testUsersCollection = $usersDb->findTestUsers();
        $this->testUserCollection = [];
        foreach ($testUsersCollection as $testUser) {
            $this->testUserCollection[] = $testUser;
        }
    }

    private function cleanExerciseUploadPictures() {
        $exercisesBaseImagesPath = APPLICATION_PATH . '/../public/images/content/dynamisch/exercises/';

        foreach ($this->testUserCollection as $testUser) {
            $currentPath = $exercisesBaseImagesPath . $testUser->user_id;
            $cadFileService = new CAD_File();
            $cadFileService->cleanDirRek($currentPath);
        }
    }

    private function cleanDeviceUploadPictures() {
        $devicesBaseImagesPath = APPLICATION_PATH . '/../public/images/content/dynamisch/devices/';

        foreach ($this->testUserCollection as $testUser) {
            $currentPath = $devicesBaseImagesPath . $testUser->user_id;
            $cadFileService = new CAD_File();
            $cadFileService->cleanDirRek($currentPath);
        }
    }

    /**
     * imports all stored dumps and reset the contained tables
     */
    private function resetDatabase() {
        $dumpFolder = APPLICATION_PATH . '/../data/dumps/';

        $directoryIterator = new DirectoryIterator($dumpFolder);
        /** @var Zend_Db_Adapter_Abstract $db */
        $db = Zend_Registry::get('db');

        foreach ($directoryIterator as $file) {
            if (!$file->isDot()
                && $file->isFile()
                && $file->isReadable()
            ) {
                $sql = file_get_contents($file->getPathname());
                $db->query($sql);
            }
        }
    }
}