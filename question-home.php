<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

// Retrieve the launch data if present
$LTI = LTIX::requireData();

include("menu.php");

// Start of the output
$OUTPUT->header();
?>
    <!-- Our main css file that overrides default Tsugi styling -->
    <link rel="stylesheet" type="text/css" href="styles/main.css">
<?php
$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

$questions = $SQ_DAO->getQuestions($_SESSION["sq_id"]);

$toolTitle = $SQ_DAO->getMainTitle($_SESSION["sq_id"]);

if ($toolTitle ==""){$toolTitle = "Study Questions";}

$_SESSION["show"] = $USER->instructor;

$name =$SQ_DAO->findDisplayName($USER->id);
echo(' 
<div class="container-fluid">
    <div class="col-sm-5 col-sm-offset-1 text-left ">
        <input type="hidden" id="sess" value="' .$_GET["PHPSESSID"]. '">');
        if ($USER->instructor) {
            echo('<h1 contenteditable="true" id="toolTitle">' .$toolTitle. '</h1>');
        } else {
            echo('<h1>' .$toolTitle. '</h1>');
        }
        echo('  
        <a href="#addQuestion" data-toggle="modal" class="btn btn-success small-shadow"><span class="fa fa-plus"></span> Add Question</a>
    </div>

    <div class="col-sm-11 col-sm-offset-1 text-left "> 
        <div class="modal fade" id="addQuestion" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title">Add Question and Answer</h3>
                    </div>
                    <form method="post" id="addQuestionForm" action="actions/addstudyquestion.php">
                        <div class="modal-body">
                            <input type="hidden" name="questionId" id="questionId" value="-1">
                            <input type="hidden" name="username" id="username" value="' . $name . '">
                            <label for="questionText">Question:</label>
                            <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus required></textarea>
                            <label for="answerText" class="spaceAbove">Answer:</label>
                            <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required></textarea>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                            <input type="submit" form="addQuestionForm" class="btn btn-success" value="Save">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

');

echo('<div class="col-sm-10 col-sm-offset-1 spaceAbove">');
    echo('<p>Click on the cards below to respond to study questions and view answers</p>
    <div class="row">');
    $i = 0;
    $x = 0;
    foreach ($questions as $question) {
        $i++;
        $up = "up";
        $down = "down";
        echo('<div class="col-sm-11">');
        $question_id = $question["question_id"];
        $answerId = -1;
        $previousVote = $SQ_DAO->getStudentVote($question_id, $USER->id);
        echo('<div class="list-group-item rowShrinker">
                <div class="row">
                    <div class="col-sm-1 text-center pull-left">
                        <input type="hidden" id="sess" value="' . $_GET["PHPSESSID"] . '">
                        <button id="upVote' . $question_id . '"  ');
                            if ($previousVote && $previousVote["vote"] === "up") {
                                echo('class="btn btn-active-up btn-icon compressed"');
                            } else {
                                echo('class="btn btn-icon compressed"');
                            }
                            echo('onclick="SQuestion.changeStateUp(' . $question_id . ')"> 
                            <span class="fa fa-arrow-up"></span>
                        </button>');
                        if($question["votes"] < 0){
                            echo ('<h4 class="negativePointsPlace1100" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                        } else {
                            echo('<h4 class="pointsPlace1100" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                        }
                        echo ('<button id="downVote' . $question_id . '"');
                            if ($previousVote && $previousVote["vote"] === "down") {
                                echo('class="btn btn-icon btn-active-down compressed"');
                            } else {
                                echo('class="btn btn-icon compressed"');
                            }
                            echo('onclick="SQuestion.changeStateDown(' . $question_id . ')"> 
                            <span class="fa fa-arrow-down"></span>
                        </button>
                    </div>
                    <div class="col-sm-11">');
                        $dateTime = new DateTime($question["modified"]);
                        $date = date_format($dateTime, "n/j/y");
                        $time = date_format($dateTime, "g:i A");
                        $question_text = substr($question["question_txt"],0,73);
                        if(strlen( $question["question_txt"] ) > 63){
                            $question_text = $question_text."...";
                        }
                        echo('
                    <div class="row">
                        <form method="post"  action="actions/viewQuestionForm.php" name="viewQuestionForm' . $i . '">
                            <input type="hidden" name="viewQuestionId" value="' . $question_id . '"/>
                        </form>
                        <a href="#"   data-toggle="modal" onclick="viewQuestionForm' . $i . '.submit()">
                            <div class="row">
                                <div class="col-sm-1 pull-right">
                                    ');
                                        $verifiedAnswer = $SQ_DAO->getUnderStood($question_id, $USER->id);
                                        if($verifiedAnswer && $verifiedAnswer["understood"]){
                                            echo('<button title="Understood" id="underStand' . $question_id . '"  class="btn-icon fa fa-check-square pull-right verifier verified mainPageGotThis disabled">');
                                        } else {
                                            echo('<button title="Not Yet Understood" id="underStand' . $question_id . '"  class="btn-icon fa fa-square-o pull-right verifier unVerified mainPageGotThis disabled" >');
                                        }
                                        echo('</button>');
                                echo ('</div>
                                <div class="col-sm-11">
                                    <p class="hider" name="questionId" id="questionId' . $x . '">' . $question_text . '</p>
                                    <h5 class="questionText">' . $question_text . '</h5>
                                    <div class="row">
                                        <div class="col-sm-10 submittedText">
                                            <p class="noMargins">Submitted by ' . $question["author"] . ' on ' . $date . ' at ' . $time . '</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </a>
                        </div>
                    </div>
                </div>
            </div>
    </div>');
    $x++;
    }
echo('</div>');


$OUTPUT->footerStart();
?>
    <!-- Our main javascript file for tool functions -->
    <script src="scripts/main.js" type="text/javascript"></script>
    <script>
        $(window).on('resize', function() {
            allignVotes();
        });
    </script>
<?php
$OUTPUT->footerEnd();
