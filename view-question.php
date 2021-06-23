<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use SQ\DAO\SQ_DAO;
use Tsugi\Core\LTIX;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

// Retrieve the launch data if present
$LTI = LTIX::requireData();

if (!isset($_GET["q"]) || $_GET["q"] == "") {
    header('Location: ' . addSession('question-home.php'));
    return;
}

$title = $LAUNCH->link->settingsGet("studytitle", false);
if (!$title) {
    $LAUNCH->link->settingsSet("studytitle", $LAUNCH->link->title);
    $title = $LAUNCH->link->title;
}

// Check if should show or hide answers
if (isset($_GET["show"]) && $_GET["show"]) {
    $_SESSION["show"] = true;
}
$showAnswers = isset($_SESSION["show"]) && $_SESSION["show"];

include("menu.php");

// Start of the output
$OUTPUT->header();
?>
    <!-- Our main css file that overrides default Tsugi styling -->
    <link rel="stylesheet" type="text/css" href="styles/main.css">
<?php
$OUTPUT->bodyStart();

$question = $SQ_DAO->getQuestionById($_GET["q"]);
if (!$question) {
    header('Location: ' . addSession('question-home.php'));
    return;
}

$OUTPUT->topNav($menu);

echo '<div class="container">';

$OUTPUT->pageTitle($title, false, false);

?>
    <p style="clear:both;">
        <a href="question-home.php"><span class="fa fa-chevron-left" aria-hidden="true"></span> Back to All Questions</a>
    </p>
<?php

$name = $SQ_DAO->findDisplayName($USER->id);

$question_id = $question["question_id"];
$dateTime = new DateTime($question["modified"]);
$date = date_format($dateTime, "n/j/y");
$time = date_format($dateTime, "g:i A");
$answerId = -1;
$previousVote = $SQ_DAO->getStudentVote($question_id, $USER->id);
$verified = $SQ_DAO->getVerified($question_id);

if (($USER->instructor) || ($question["user_id"] == $USER->id)) {
    echo('<div class="pull-right">
                    <a href="#editQuestion" data-toggle="modal" class="btn btn-small btn-link"><span class="fa fa-pencil" aria-hidden="true"></span> Edit Q&A</a>
                    <div class="modal fade" id="editQuestion" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Question and Answer</h4>
                                </div>
                                <form method="post" id="editQuestionForm" action="actions/addstudyquestion.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="questionId" id="questionId" value="' . $question["question_id"] . '">
                                        <input type="hidden" name="username" id="username" value="' . $name . '">
                                        <input type="hidden" name="page" id="page" value="question">
                                        <div class="form-group">
                                        <label for="questionText">Question Text</label>
                                        <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus required>' . $question["question_txt"] . '</textarea>
                                        </div>
                                        <div class="form-group">
                                        <label for="answerText">Answer Text</label>
                                        <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required>' . $question["answer_txt"] . '</textarea>
                                        </div> 
                                    </div>
                                    <div class="modal-footer" style="text-align: left;">
                                        <a class="btn btn-link pull-right text-danger" onclick="return SQuestion.deleteQuestionConfirm();" href="actions/deleteQuestion.php?question_id=' . $question["question_id"] . '">
                        <span aria-hidden="true" class="fa fa-trash text-danger"></span>
                        <span class="text-danger">Delete Question</span>
                    </a>
                                        <input type="submit" form="editQuestionForm" class="btn btn-success" value="Save">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div> ');
}

echo('<h4>Question</h4><div class="list-group" style="clear:both;">
            <div class="list-group-item" style="display:flex;">
                <div class="vote-container">
                <button id="upVote' . $question_id . '"  ');
if ($previousVote && $previousVote["vote"] === "up") {
    echo('class="btn btn-active-up btn-icon"');
} else {
    echo('class="btn btn-icon"');
}
echo('onclick="SQuestion.changeStateUp(' . $question_id . ')"> 
                            <span class="fa fa-arrow-up"></span>
                        </button>');
echo('<h4 class="text-center" id="points' . $question_id . '">' . $question["votes"] . '</h4>');
echo('<button id="downVote' . $question_id . '"');
if ($previousVote && $previousVote["vote"] === "down") {
    echo('class="btn btn-icon btn-active-down"');
} else {
    echo('class="btn btn-icon"');
}
echo('onclick="SQuestion.changeStateDown(' . $question_id . ')"> 
                            <span class="fa fa-arrow-down"></span>
                        </button>
                        </div>');
echo('<div style="flex-grow:1;padding-bottom: 2rem;">
                    <p class="questionText">' . $question["question_txt"] . '</p>
                    <p class="small text-muted" style="position:absolute; bottom: 0;">Submitted by ' . $question["author"] . ' on ' . $date . ' at ' . $time . '</p>
                  </div>

    </div>'); // End list group item
echo('</div>'); // end list group

if ($showAnswers) {
    echo('<h4>Answer</h4><div class="list-group">
                <div class="list-group-item');
    if ($verified["correct"]) {
        echo ' bg-success';
    }
    echo('" style="padding-bottom: 2rem;">');
    if ($USER->instructor) {
        if ($verified["correct"]) {
            echo(' <button id="verify' . $question_id . '" title="Verified Answer" class="verifier verified" onclick="SQuestion.verifyAnswer(' . $question_id . ')" ><span class="fa fa-check-circle" aria-hidden="true"></span> <span class="button-text">Verified</span></button>');
        } else {
            echo(' <button id="verify' . $question_id . '" title="Unverified Answer" class="verifier unVerified" onclick="SQuestion.verifyAnswer(' . $question_id . ')" ><span class="fa fa-check-circle-o" aria-hidden="true"></span> <span class="button-text">Unverified</span></button>');
        }
    } else {
        if ($verified["correct"]) {
            echo('<div style="position:absolute;bottom:4px;right:14px;" class="text-success"><span class="fa fa-check"></span> Verified by the instructor</div>');
        }
    }
    echo('
                <p class="answerText">' . $question["answer_txt"] . '</p>
                <p class="small text-muted" style="position:absolute; bottom: 0;">Submitted by ' . $question["author"] . ' on ' . $date . ' at ' . $time . '</p>
                </div>
              </div>'); // end list group

    $answers = $SQ_DAO->getAllAnswersToQuestion($question_id);
    echo '<hr><h4>Additional Answers</h4><div class="list-group">';
    if (!$answers || count($answers) == 0) {
        echo '<p>No additional answers have been added at this time.</p>';
    }
    foreach ($answers as $answer) {
        $dateTime = new DateTime($answer["modified"]);
        $date = date_format($dateTime, "n/j/y");
        $time = date_format($dateTime, "g:i A");
        $answer_id = $answer["answer_id"];
        $verifiedAnswer = $SQ_DAO->getAnswerVerified($answer_id);
        echo '<div class="list-group-item';
        if ($verifiedAnswer["correct"]) {
            echo ' bg-success';
        }
        echo('" style="padding-bottom: 2rem;">');
        if ($USER->instructor) {
            if ($verifiedAnswer["correct"]) {
                echo(' <button id="verifyA' . $answer_id . '" title="Verified Answer" class="verifier verified" onclick="SQuestion.verifyUserAnswer(' . $answer_id . ')" ><span class="fa fa-check-circle" aria-hidden="true"></span> <span class="button-text">Verified</span></button>');
            } else {
                echo(' <button id="verifyA' . $answer_id . '" title="Unverified Answer" class="verifier unVerified" onclick="SQuestion.verifyUserAnswer(' . $answer_id . ')" ><span class="fa fa-check-circle-o" aria-hidden="true"></span> <span class="button-text">Unverified</span></button>');
            }
        } else {
            if ($verifiedAnswer["correct"]) {
                echo('<div style="position:absolute;bottom:4px;right:14px;" class="text-success"><span class="fa fa-check"></span> Verified by the instructor</div>');
            }
        }
        if (($USER->instructor) || ($answer["user_id"] == $USER->id)) {
            echo('<div class="pull-right">
                    <a href="#editAnswer' . $answerId . '" data-toggle="modal"><span class="fa fa-pencil" aria-hidden="true"></span> Edit Answer</a>
                    <div class="modal fade" id="editAnswer' . $answerId . '" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Edit Answer</h4>
                                </div>
                                <form method="post" id="editAnswerForm' . $answerId . '" action="actions/addanswertoquestion.php">
                                    <div class="modal-body">
                                            <input type="hidden" name="answerId" id="answerId" value="' . $answer_id . '">
                                            <input type="hidden" name="questionId" id="questionId" value="' . $question_id . '">
                                            <input type="hidden" name="username" id="username" value="' . $name . '">
                                            <input type="hidden" name="page" id="page" value="main">
                                        <div class="form-group">
                                            <label for="answerText">Edit Answer Text</label>
                                            <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required>' . $answer["answer_txt"] . '</textarea>
                                        </div> 
                                    </div>
                                    <div class="modal-footer" style="text-align: left;">
                                        <a class="btn btn-link pull-right text-danger" onclick="return SQuestion.deleteAnswerConfirm();" href="actions/deleteAnswer.php?answer_id=' . $answer_id . '&q='.$question_id.'">
                        <span aria-hidden="true" class="fa fa-trash text-danger"></span>
                        <span class="text-danger">Delete Answer</span>
                    </a>
                                        <input type="submit" form="editAnswerForm' . $answerId . '" class="btn btn-success" value="Save">
                                        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    </div> ');
        }

        echo '   <p class="answerText">' . $answer["answer_txt"] . '</p>
                <p class="small text-muted" style="position:absolute; bottom: 0;">Submitted by ' . $answer["author"] . ' on ' . $date . ' at ' . $time . '</p>
                </div>'; // end list group item
    }
    echo('
            <div class="list-group-item" id="addAnswer" style="display:none;margin-bottom:2rem;">
                <h4>Add Answer</h4>
                <form method="post" id="addAnswerForm" action="actions/addanswertoquestion.php">
                    <input type="hidden" name="answerId" id="answerId" value="-1">
                    <input type="hidden" name="questionId" id="questionId" value="' . $_GET["q"] . '">
                    <input type="hidden" name="username" id="username" value="' . $name . '">
                    <div class="form-group">
                        <label for="answerText">Answer Text</label>
                        <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required></textarea>
                    </div>
                    <div>
                        <input type="submit" form="addAnswerForm" class="btn btn-success" value="Save">
                        <a href="javascript:void(0);" class="btn btn-link" onclick="toggleAddAnswer();">Cancel</a>
                    </div>
                </form>
            </div>
');
    echo '</div>
            <a href="#" onclick="toggleAddAnswer();" id ="addAnswerButton" class="btn btn-success"><span class="fa fa-plus"></span> Add Answer</a>
            '; // end list group
} else {
    // Don't show answers
    echo('<h4>Answer</h4>
            <div class="list-group">
                <a class="list-group-item text-center reveal-box" href="view-question.php?q='.$question_id.'&show=true">Click to Reveal Answer</a>
            </div>');

}
echo '</div>'; // End container
$OUTPUT->footerStart();
?>
    <!-- Our main javascript file for tool functions -->
    <script>
        const sess = '<?=$_GET["PHPSESSID"]?>';
    </script>
    <script src="scripts/main.js" type="text/javascript"></script>
<?php
$OUTPUT->footerEnd();
