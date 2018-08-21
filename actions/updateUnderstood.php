<?php
require_once "../../config.php";
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$user_id = $USER->id;
$understood = $_POST["understood"];
$question_id = $_POST["id"];
$sq_id = $_SESSION["sq_id"];
$verifiedAnswer = $SQ_DAO->getUnderStood($question_id, $USER->id);
if($verifiedAnswer){
    $SQ_DAO->updateUnderStood($question_id, $understood, $user_id ,$sq_id);
} else {
    $SQ_DAO->createUnderStood($question_id, $user_id, $sq_id, $understood);
}
