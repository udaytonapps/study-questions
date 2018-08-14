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

// Start of the output
$OUTPUT->header();
?>
    <!-- Our main css file that overrides default Tsugi styling -->
    <link rel="stylesheet" type="text/css" href="styles/main.css">
<?php
$OUTPUT->bodyStart();

//include("menu.php");
$question = $SQ_DAO->getQuestionById($_SESSION["questionId"]);

$toolTitle = $SQ_DAO->getMainTitle($_SESSION["sq_id"]);

if ($USER->instructor) {
    echo('
    <div id="sideNav" class="side-nav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><span class="fa fa-times"></span></a>
        <a href="splash.php"><span class="fa fa-fw fa-pencil-square" aria-hidden="true"></span> Getting Started</a>
        <a href="question-home.php"><span class="fa fa-fw fa-pencil-square" aria-hidden="true"></span> Questions </a>
    </div>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="javascript:void(0);" onclick="openSideNav();"><span class="fa fa-bars"></span> Menu</a>
            </div>
        </div>
    </nav>
');
} else {
    echo('
    <div id="sideNav" class="side-nav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><span class="fa fa-times"></span></a>
        <a href="splash.php"><span class="fa fa-fw fa-pencil-square" aria-hidden="true"></span> Getting Started</a>
        <a href="question-home.php"><span class="fa fa-fw fa-pencil-square" aria-hidden="true"></span> Questions </a>
    </div>

    <nav class="navbar navbar-default">
        <div class="container-fluid">
            <div class="navbar-header">
                <a class="navbar-brand" href="javascript:void(0);" onclick="openSideNav();"><span class="fa fa-bars"></span> Menu</a>
            </div>
        </div>
    </nav>
');
}

$name =$SQ_DAO->findDisplayName($USER->id);

echo('<div class="container-fluid">
        <div class="row">      
            <div class="col-sm-10 col-sm-offset-1 text-left "> 
                <h2>' . $toolTitle . '</h2>');

                echo('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus metus ante, congue eget molestie vitae. </p>
            </div>
        </div>
    </div>
');

    $i = 0;
    $up = "up";
    $down = "down";
    $questionName = $question["author"];
    $question_id = $question["question_id"];
    $dateTime = new DateTime($question["modified"]);
    $date = date_format($dateTime, "n/j/y");
    $time = date_format($dateTime, "g:iA");
    $answerId = -1;
    $previousVote = $SQ_DAO->getStudentVote($question_id, $USER->id);
    $verified = $SQ_DAO->getVerified($question_id);

    echo('
    <div class="col-sm-10 col-sm-offset-1">
        <div class="list-group-item">
            <div class="row">
                <div class="col-sm-1 text-center">
                    <input type="hidden" id="sess" value="' . $_GET["PHPSESSID"] . '">
                    <button id="upVote' . $question_id . '" ');
                        if($previousVote["vote"] === "up"){
                            echo('class="btn btn-icon btn-active-up"');
                        } else {
                            echo('class="btn btn-icon"');
                        }
                        echo('onclick="SQuestion.changeStateUp(' . $question_id . ')"> 
                        <span class="fa fa-arrow-up"></span>
                    </button>');
                    if($question["votes"] < 0){
                        echo ('<h3 class="negativePointsPlaceViewQuestion" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                    } else {
                        echo ('<h3 class="pointsPlaceViewQuestion" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                    }
                    echo ('<button id="downVote' . $question_id . '"');
                        if($previousVote["vote"] === "down"){
                            echo('class="btn btn-icon btn-active-down"');
                        } else {
                            echo('class="btn btn-icon"');
                        }
                        echo('onclick="SQuestion.changeStateDown(' . $question_id . ')"> 
                        <span class="fa fa-arrow-down"></span>
                    </button>
                </div>
                <div class="col-sm-10">
                    <form method="post"  action="actions/viewQuestionForm.php" name="viewQuestionForm' . $i . '">
                        <input type="hidden" name="viewQuestionId" value="' . $question_id . '"/>
                        <a href="#"   data-toggle="modal" onclick="viewQuestionForm' . $i . '.submit()">
                        <div class="row">
                            <div class="col-sm-12">
                                <h4>'.$question["question_txt"].'</h4>
                            </div>
                        </div>
                        <br><br>
                        </a>
                    </form>
                    <div class="row spaceAbove">
                        <div class="col-sm-12 text-right">
                            <p>' . $questionName . ' - ' . $date . ' - ' . $time . '</p>
                        </div>
                    </div>
                </div>
            <div class="col-sm-1">');
                if(($USER->instructor) || ($question["user_id"] == $USER->id)){
                    echo('<a onclick="return SQuestion.deleteQuestionConfirm();" href="actions/deleteQuestion.php?question_id=' . $question["question_id"] . '">
                        <span aria-hidden="true" class="fa fa-lg fa-trash pull-right"></span>
                        <span class="sr-only">Delete Question</span>
                    </a>
                    <a href="#editQuestion" data-toggle="modal" ><span class="fa fa-lg fa-pencil pull-right"></span></a>
                    <div class="modal fade" id="editQuestion" tabindex="-1" role="dialog" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h4 class="modal-title">Add Question and Answer</h4>
                                </div>
                                <form method="post" id="editQuestionForm" action="actions/addstudyquestion.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="questionId" id="questionId" value="' . $question["question_id"] . '">
                                        <input type="hidden" name="username" id="username" value="' . $name . '">
                                        <input type="hidden" name="page" id="page" value="question">
                                        <label for="questionText">Edit Question Text</label>
                                        <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus required>' . $question["question_txt"] . '</textarea>
                                        <label for="answerText">Edit Answer Text</label>
                                        <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required>' . $question["answer_txt"] . '</textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                        <input type="submit" form="editQuestionForm" class="btn btn-success" value="Save">
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div> ');
                }
            echo (' </div>
        </div>
    </div>');
    echo('<div class="list-group-item">
        <div class="row">
            <div class="col-sm-11">
                <p>' . $questionName . '\'s Answer</p>
            </div>
            <div class="col-sm-1">
                <button id="verify' . $question_id . '" ');
                    if($verified["correct"]){
                        echo('title="Verified Answer" class="btn-icon fa fa-3x fa-check-circle-o pull-right verifier verified"');
                    } else {
                        echo('title="Unverified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier unVerified"');
                    }
                    if ($USER->instructor){
                        echo(' onclick="SQuestion.verifyAnswer(' . $question_id . ')"');
                    } else {
                        echo('disabled');
                    }
                echo(' ></button>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-11  col-sm-offset-1">
                    <h4>'.$question["answer_txt"].'</h4>
            </div>
        </div>   
    </div> ');
    $answers = $SQ_DAO->getAllAnswersToQuestion($question_id);
    if(count($answers) > 0) {
    echo ('
        <div class="col-sm-12" >
            <h3>Student Answers</h3>
        </div >
        ');
    }
    $i = 0;
    foreach ($answers as $answer) {
        $i++;
        $dateTime = new DateTime($answer["modified"]);
        $date = date_format($dateTime, "n/j/y");
        $time = date_format($dateTime, "g:iA");
        echo('<div class="col-sm-12">');
            $answer_id = $answer["answer_id"];
        echo('<div class="list-group-item">
                <div class="row">
                    <div class="col-sm-10">
                        <h4>'.$answer["answer_txt"].'</h4>
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <p>'.$answer["author"].' - ' . $date . ' - ' . $time . '</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                    ');
                    if(($USER->instructor) || ($answer["user_id"] == $USER->id)) {
                        echo('
                        <a onclick="return SQuestion.deleteAnswerConfirm();" href="actions/deleteAnswer.php?answer_id=' . $answer_id . '">
                            <span aria-hidden="true" class="fa fa-lg fa-trash pull-right"></span>
                            <span class="sr-only">Delete Question</span>
                        </a>
                        <a href="#editAnswer' . $i . '" data-toggle="modal" ><span class="fa fa-lg fa-pencil pull-right"></span></a>
                        <div class="modal fade" id="editAnswer' . $i . '" tabindex="-1" role="dialog" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Add Question and Answer</h4>
                                    </div>
                                    <form method="post" id="editAnswerForm' . $i . '" action="actions/addanswertoquestion.php">
                                        <div class="modal-body">
                                            <input type="hidden" name="answerId" id="answerId" value="' . $answer_id . '">
                                            <input type="hidden" name="questionId" id="questionId" value="' . $question_id . '">
                                            <input type="hidden" name="username" id="username" value="' . $name . '">
                                            <input type="hidden" name="page" id="page" value="main">
                                            <label for="answerText">Edit Answer Text</label>
                                            <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required>' . $answer["answer_txt"] . '</textarea>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                            <input type="submit" form="editAnswerForm' . $i . '" class="btn btn-success" value="Save">
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>');
                    }
                echo('
                    <button id="verifyAnswer' . $answer_id . '"  ');
                        $verifiedAnswer = $SQ_DAO->getAnswerVerified($answer_id);
                        if($verifiedAnswer["correct"]){
                            echo('title="Verified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier verified"');
                        } else {
                            echo('title="Unverified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier unVerified"');
                        }
                        if ($USER->instructor){
                            echo(' onclick="SQuestion.verifyUserAnswer(' . $answer_id . ')"');
                        } else {
                            echo('disabled');
                        }
                    echo('>
                    </button>
                    </div>
                </div>
            </div>
        </div>
        ');
    }
    echo('
            <div id="addAnswer" style="display:none;" class="col-sm-12 text-left spaceAbove "> 
                <div class="list-group-item" >
                    <div class="row">
                        <div class="col-sm-12">
                            <h4>Add Answer</h4>
                            <form method="post" id="addAnswerForm" action="actions/addanswertoquestion.php">
                                <div class="modal-body">
                                    <input type="hidden" name="answerId" id="answerId" value="-1">
                                    <input type="hidden" name="questionId" id="questionId" value="' . $_SESSION["questionId"] . '">
                                    <input type="hidden" name="username" id="username" value="' . $name . '">
                                    <label for="answerText">Answer Text</label>
                                    <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required></textarea>
                                </div>
                                <div class="text-right">
                                    <input type="submit" form="addAnswerForm" class="btn btn-success" value="Save">
                                    <a href="javascript:void(0);" class="btn btn-link" onclick="toggleAddAnswer();">Cancel</a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
                <div class="col-sm-12 text-left spaceAbove "> 
                    <a href="#" onclick="toggleAddAnswer();" id ="addAnswerButton" class="btn btn-success small-shadow "><span class="fa fa-plus"></span> Add Answer</a>
                    <a href="question-home.php"  class="btn btn-info small-shadow pull-right">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>');

$OUTPUT->footerStart();
?>
    <!-- Our main javascript file for tool functions -->
    <script src="scripts/main.js" type="text/javascript"></script>
<?php
$OUTPUT->footerEnd();
