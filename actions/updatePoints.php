<?php
require_once "../../config.php";
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$user_id = $USER->id;
$direction = $_POST["direction"];
$question_id = $_POST["id"];
$vote = $_POST["vote"];
$sq_id = $_SESSION["sq_id"];
$oldPoints = $SQ_DAO->getpoints($question_id);
$previousVote = $SQ_DAO->getStudentVote($question_id, $user_id);
if($direction == "up"){
    $points = $oldPoints["votes"] + 1;
} else {
    $points = $oldPoints["votes"] - 1;
}
$SQ_DAO->updatePoints($question_id, $points);

if($previousVote["vote"] === null){
    $SQ_DAO->createStudentVote($question_id, $user_id, $sq_id, $vote);
}else {
    $SQ_DAO->updateStudentVote($question_id, $user_id, $vote);
}
