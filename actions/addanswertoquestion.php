<?php
require_once "../../config.php";
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$answerId = $_POST["answerId"];
$answerText = $_POST["answerText"];
$questionId = $_POST["questionId"];
$name = $_POST["username"];

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");
if ($answerId > -1) {
    $SQ_DAO->updateAnswer($answerId, $answerText, $currentTime);
} else {
    $SQ_DAO->createAnswer($USER->id, $name, $questionId, $answerText, $currentTime,  $_SESSION["sq_id"]);
}

header( 'Location: '.addSession('../view-question.php') ) ;
