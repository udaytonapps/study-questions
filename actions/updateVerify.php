<?php
require_once "../../config.php";
require_once('../dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$correct = $_POST["correct"];
$question_id = $_POST["id"];

$SQ_DAO->updateVerified($question_id, $correct);
