<?php
require_once "../../config.php";
require_once "../dao/SQ_DAO.php";

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

if ( $USER->instructor ) {

    $SQ_DAO->deleteMain($_SESSION["sq_id"], $USER->id);
    $skipSplash = $SQ_DAO->skipSplash($USER->id);

    if($skipSplash){
        header( 'Location: '.addSession('../index.php') ) ;
    } else {
        header( 'Location: '.addSession('../splash.php') ) ;
    }


}
