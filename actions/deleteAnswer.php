<?php
require_once "../../config.php";
require_once "../dao/SQ_DAO.php";

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$answer_id = $_GET["answer_id"];

$SQ_DAO->deleteAnswer($answer_id);

header( 'Location: '.addSession('../view-question.php') ) ;
