<?php
/**
 * Created by PhpStorm.
 * User: mastercad
 * Date: 15.05.17
 * Time: 19:37
 */

require_once 'init.php';

$resetService = new Service_Reset();
$resetService->cleanTestActivities();