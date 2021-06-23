<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use SQ\DAO\SQ_DAO;
use Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$name =$SQ_DAO->findDisplayName($USER->id);
$title = $LAUNCH->link->settingsGet("studytitle", "Study Questions");

include("menu.php");

// Start of the output
$OUTPUT->header();

echo ('<link rel="stylesheet" type="text/css" href="styles/splash.css">');

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container">';

$OUTPUT->pageTitle($title, false, $USER->instructor);
?>
    <p class="lead">
        The Study Question tool is designed to let students in a class compile questions and answers that will assist them in studying for an assessment.
    </p>
    <p>
        You must add at least one question and answer that others can use to study from before you will be able to see questions and answers provided by others.</p>
    <form method="post" id="addQuestionForm" action="actions/addstudyquestion.php">
        <input type="hidden" name="questionId" id="questionId" value="-1">
        <input type="hidden" name="username" id="username" value="<?= $name ?>">
        <div class="form-group">
            <label for="questionText">Question Text</label>
            <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus
                      required></textarea>
        </div>
        <div class="form-group">
            <label for="answerText" class="spaceAbove">Answer Text</label>
            <textarea class="form-control" name="answerText" id="answerText" rows="4" required></textarea>
        </div>
        <input type="submit" form="addQuestionForm" class="btn btn-success" value="Save">
    </form>
<?php
$OUTPUT->footerStart();

$OUTPUT->footerEnd();
