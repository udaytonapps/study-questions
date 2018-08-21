<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
$currentTime = $currentTime->format("Y-m-d H:i:s");

if ( $USER->instructor ) {
    $_SESSION["sq_id"] = $SQ_DAO->getOrCreateMain($USER->id, $CONTEXT->id, $LINK->id, $currentTime);

    $hasQuestions = $SQ_DAO->getQuestions($_SESSION["sq_id"]);

    if (!$hasQuestions) {
        $currentTime = new DateTime('now', new DateTimeZone($CFG->timezone));
        $currentTime = $currentTime->format("Y-m-d H:i:s");
        $skipSplash = $SQ_DAO->skipSplash($USER->id);
        header('Location: '.addSession('splash.php'));
        if ($skipSplash) {
             header( 'Location: '.addSession('question-home.php') ) ;
        } else {
              header('Location: '.addSession('splash.php'));
        }
    } else {
        header( 'Location: '.addSession('question-home.php') ) ;
    }
} else {
    $mainId = $SQ_DAO->getMainID($CONTEXT->id, $LINK->id);
    $count = $SQ_DAO->countQuestionsForStudent($USER->id);
    if (!$mainId) {
        echo ("<h1>The instructor has not set up this tool yet. Please contact your instructor for more info.</h1>");
    } else if($count < 1) {
        $_SESSION["sq_id"] = $mainId;
        header( 'Location: '.addSession('studentSplash.php') ) ;
    } else {
        $_SESSION["sq_id"] = $mainId;
        header( 'Location: '.addSession('question-home.php') ) ;
    }
}
