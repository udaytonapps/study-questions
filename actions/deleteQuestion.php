<?php
require_once "../../config.php";
require_once "../dao/SQ_DAO.php";

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$question_id = isset($_GET["question_id"]) ? $_GET["question_id"] : false;

$SQ_DAO->deleteQuestion($question_id);

$SQ_DAO->fixUpQuestionNumbers($_SESSION["sq_id"]);

header( 'Location: '.addSession('../index.php') ) ;
