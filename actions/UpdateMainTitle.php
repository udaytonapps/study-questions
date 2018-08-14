<?php
require_once "../../config.php";
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

if ($USER->instructor) {
    if (isset($_POST["toolTitle"])) {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        $SQ_DAO->updateMainTitle($_SESSION["sq_id"], $_POST["toolTitle"], $currentTime);
    }
    header( 'Location: '.addSession('../question-home.php') ) ;
}
