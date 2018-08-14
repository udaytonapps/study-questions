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
$questions = $SQ_DAO->getQuestions($_SESSION["sq_id"]);

$toolTitle = $SQ_DAO->getMainTitle($_SESSION["sq_id"]);

if ($toolTitle ===""){$toolTitle = "Study Questions";}

if ($USER->instructor) {
    echo('

    <div id="sideNav" class="side-nav">
        <a href="javascript:void(0)" class="closebtn" onclick="closeNav()"><span class="fa fa-times"></span></a>
        <a href="splash.php"><span class="fa fa-fw fa-pencil-square" aria-hidden="true"></span> Getting Started</a>
        <a href="question-home.php"><span class="fa fa-fw fa-pencil-square" aria-hidden="true"></span> Questions </a>
        <a href="javascript:void(0);" id="editTitleLink"><span class="fa fa-fw fa-pencil" aria-hidden="true"></span> Edit Tool Title</a>
        <a href="actions/DeleteAll.php" onclick="return confirmResetTool();"><span class="fa fa-fw fa-trash" aria-hidden="true"></span> Reset Tool</a>
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

if ($USER->instructor ) {
    $count = 1;
} else {
    $count = $SQ_DAO->countQuestionsForStudent($USER->id);
}
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
    </div>');
    if($count < 1){
        echo('<div class="col-sm-10 col-sm-offset-1">
            <h2> You must add a question and answer before you can see questions and answers submitted by others.</h2>
        </div>');
    }
    echo('</div>
    <hr>
    <div class="col-sm-11 col-sm-offset-1 text-left "> 
        <div class="modal fade" id="addQuestion" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add Question and Answer</h4>
                    </div>
                    <form method="post" id="addQuestionForm" action="actions/addstudyquestion.php">
                        <div class="modal-body">
                            <input type="hidden" name="questionId" id="questionId" value="-1">
                            <input type="hidden" name="username" id="username" value="' . $name . '">
                            <label for="questionText">Question Text</label>
                            <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus required></textarea>
                            <label for="answerText" class="spaceAbove">Answer Text</label>
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
</div>
<hr />
');

if($count > 0){
    echo('<div class="col-sm-10 col-sm-offset-1">');
        echo('<p>Click on the cards below to respond to study questions and view answers</p>
        <div class="row">');
        $i = 0;
        foreach ($questions as $question) {
            $i++;
            $up = "up";
            $down = "down";
            echo('<div class="col-sm-11">');
            $question_id = $question["question_id"];
            $answerId = -1;
            $previousVote = $SQ_DAO->getStudentVote($question_id, $USER->id);
            echo('<div class="list-group-item">
                    <div class="row">
                        <div class="col-sm-1 text-center">
                            <input type="hidden" id="sess" value="' . $_GET["PHPSESSID"] . '">
                            <button id="upVote' . $question_id . '" ');
                                if ($previousVote["vote"] === "up") {
                                    echo('class="btn btn-active-up btn-icon"');
                                } else {
                                    echo('class="btn btn-icon"');
                                }
                                echo('onclick="SQuestion.changeStateUp(' . $question_id . ')"> 
                                <span class="fa fa-arrow-up"></span>
                            </button>');
                            if($question["votes"] < 0){
                                echo ('<h3 class="negativePointsPlace" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                            } else {
                                echo('<h3 class="pointsPlace" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                            }
                            echo ('<button id="downVote' . $question_id . '"');
                                if ($previousVote["vote"] === "down") {
                                    echo('class="btn btn-icon btn-active-down"');
                                } else {
                                    echo('class="btn btn-icon"');
                                }
                                echo('onclick="SQuestion.changeStateDown(' . $question_id . ')"> 
                                <span class="fa fa-arrow-down"></span>
                            </button>
                        </div>
                        <div class="col-sm-10">');
                            $dateTime = new DateTime($question["modified"]);
                            $date = date_format($dateTime, "n/j/y");
                            $time = date_format($dateTime, "g:iA");
                            $question_text = substr($question["question_txt"],0,70);
                            if(strlen( $question["question_txt"] ) > 70){
                                $question_text = $question_text."...";
                            }
                            echo('
                            <form method="post"  action="actions/viewQuestionForm.php" name="viewQuestionForm' . $i . '">
                                <input type="hidden" name="viewQuestionId" value="' . $question_id . '"/>
                                <a href="#"   data-toggle="modal" onclick="viewQuestionForm' . $i . '.submit()">
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <h4>' . $question_text . '</h4>
                                        </div>');
                                        if(($USER->instructor) || ($question["user_id"] == $USER->id)){
                                            echo('
                                            <div class="col-sm-2">
                                                <a onclick="return SQuestion.deleteQuestionConfirm();" href="actions/deleteQuestion.php?question_id=' . $question_id . '">
                                                    <span aria-hidden="true" class="fa fa-lg fa-trash pull-right"></span>
                                                    <span class="sr-only">Delete Question</span>
                                                </a>
                                                <a href="#editQuestion' . $i . '" data-toggle="modal" ><span class="fa fa-lg fa-pencil pull-right"></span></a>
                                                <div class="modal fade" id="editQuestion' . $i . '" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h4 class="modal-title">Add Question and Answer</h4>
                                                            </div>
                                                            <form method="post" id="editQuestionForm' . $i . '" action="actions/addstudyquestion.php">
                                                                <div class="modal-body">
                                                                    <input type="hidden" name="questionId" id="questionId" value="' . $question_id . '">
                                                                    <input type="hidden" name="username" id="username" value="' . $name . '">
                                                                    <input type="hidden" name="page" id="page" value="main">
                                                                    <label for="questionText">Edit Question Text</label>
                                                                    <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus required>' . $question["question_txt"] . '</textarea>
                                                                    <label for="answerText" class="spaceAbove">Edit Answer Text</label>
                                                                    <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required>' . $question["answer_txt"] . '</textarea>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
                                                                    <input type="submit" form="editQuestionForm' . $i . '" class="btn btn-success" value="Save">
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        ');
                                        }
                                    echo ('</div>
                                </a>   
                                <br><br>
                                <div class="row">
                                    <div class="col-sm-12 text-right">
                                        <h5>Submitted by ' . $question["author"] . ' on ' . $date . ' at ' . $time . '</h5>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </form>
        </div>');
        }
    echo('</div>');
}

$OUTPUT->footerStart();
?>
    <!-- Our main javascript file for tool functions -->
    <script src="scripts/main.js" type="text/javascript"></script>
<?php
$OUTPUT->footerEnd();
