<?php
require_once('../../config.php');
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

if ($USER->instructor) {

    $SQ_DAO->toggleSkipSplash($USER->id);

}

exit;
