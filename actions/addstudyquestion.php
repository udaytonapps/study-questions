<?php
require_once "../../config.php";
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

    $questionId   = $_POST["questionId"];
    $questionText = $_POST["questionText"];
    $answerText   = $_POST["answerText"];
    $name         = $_POST["username"];
    $page         = $_POST["page"];
    $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
    $currentTime = $currentTime->format("Y-m-d H:i:s");
    if ($questionId > -1) {
        echo($questionId);
        echo($questionText);
        echo($answerText);
        echo($currentTime);
        $SQ_DAO->updateQuestion($questionId, $questionText, $answerText, $currentTime);
    } else {
        $SQ_DAO->createQuestion($_SESSION["sq_id"], $questionText, $answerText, $currentTime, $name, $USER->id);
    }
    if($page == "question"){
        header( 'Location: '.addSession('../view-question.php') ) ;
    } else {
        header( 'Location: '.addSession('../question-home.php') ) ;
    }


