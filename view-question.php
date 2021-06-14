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
    <link rel="stylesheet" type="text/css" href="styles/animations.css">
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
                <h2>' . $toolTitle . '</h2>
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
    $time = date_format($dateTime, "g:i A");
    $answerId = -1;
    $previousVote = $SQ_DAO->getStudentVote($question_id, $USER->id);
    $verified = $SQ_DAO->getVerified($question_id);

    echo('

    <div class="col-sm-10 col-sm-offset-1 text-left">
    <div class="pull-right">
    ');
            $verifiedAnswer = $SQ_DAO->getUnderStood($question_id, $USER->id);
            if($verifiedAnswer && $verifiedAnswer["understood"]){
                echo('<button id="underStand' . $question_id . '"  class="btn-icon fa fa-check-square verifier verified"onclick="SQuestion.updateUnderstood(' . $question_id . ')">');
            } else {
                echo('<button id="underStand' . $question_id . '"  class="btn-icon fa fa-square-o verifier unVerified" onclick="SQuestion.updateUnderstood(' . $question_id . ')">');
            }
            echo('
            </button>
            <span class="sizeUp">Got It!</span>
        </div>
        <h4>' . $questionName . ' - ' . $date . ' - ' . $time . '</h4>
        
    </div>
    <div class="col-sm-10 col-sm-offset-1">
        <div class="list-group-item">
            <div class="row">
                <div class="col-sm-1 text-center pull-left">
                    <input type="hidden" id="sess" value="' . $_GET["PHPSESSID"] . '">
                    <button id="upVote' . $question_id . '" ');
                        if($previousVote && $previousVote["vote"] === "up"){
                            echo('class="btn btn-icon btn-active-up compressed"');
                        } else {
                            echo('class="btn btn-icon compressed"');
                        }
                        echo('onclick="SQuestion.changeStateUp(' . $question_id . ')">
                        <span class="fa fa-arrow-up"></span>
                    </button>');
                    if($question["votes"] < 0){
                        echo ('<h3 class="negativePointsPlaceViewQuestion1100" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                    } else {
                        echo ('<h3 class="pointsPlaceViewQuestion1100" id="points' . $question_id . '">' . $question["votes"] . '</h3>');
                    }
                    echo ('<button id="downVote' . $question_id . '"');
                        if($previousVote && $previousVote["vote"] === "down"){
                            echo('class="btn btn-icon btn-active-down compressed"');
                        } else {
                            echo('class="btn btn-icon compressed"');
                        }
                        echo('onclick="SQuestion.changeStateDown(' . $question_id . ')">
                        <span class="fa fa-arrow-down"></span>
                    </button>
                </div>
                <div class="col-sm-9">
                    <div class="row">
                    <div class="col-sm-10">
                        <h4 class="viewQuestionHeaders">Question:</h4>
                    </div>
                        <div class="col-sm-12 questionText" style="overflow: auto">
                            <h4>'.$question["question_txt"].'</h4>
                        </div>
                    </div>
                </div>
            <div class="col-sm-2">');
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
     echo('</div>
        </div>
    </div>');
    $answers = $SQ_DAO->getAllAnswersToQuestion($question_id);
    if(!$_SESSION["show"]){
        echo ('
        <div id = "hideAnswer" class="list-group-item text-center showthis hideAnswer" onclick="revealAnswers()">');
        if(count($answers) > 0) {
            echo (' <h2>Click to Reveal Answers</h2> ');
        }else{
            echo (' <h2>Click to Reveal Answer</h2> ');
        }
        echo ('
        </div>
        <div id = "answerblock" class="hider">');
    } else {
        echo ('<div class="showthis">');
    }

    echo ('
    <div class="list-group-item answerBlock">
        <div class="row">
            <div class="col-sm-10 col-sm-offset-1">
            <div class="row">
            <div class="col-sm-11 answertext">
                    <h4 class="viewQuestionHeaders">Answer:</h4>
                </div>
                
                <div class="col-sm-11 answertext">
                    <h4>'.$question["answer_txt"].'</h4>
                </div>
                </div>
            </div>
            <div class="col-sm-1 pull-right">
                <div class="row">
                <div class="col-sm-12">
                   ');

                        if ($USER->instructor){
                            if($verified["correct"]){
                                echo(' <button id="verify' . $question_id . '" title="Verified Answer" class="btn-icon fa fa-3x fa-check-circle-o pull-right verifier verified" onclick="SQuestion.verifyAnswer(' . $question_id . ')" ></button>');
                            } else {
                                echo(' <button id="verify' . $question_id . '" title="Unverified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier unVerified" onclick="SQuestion.verifyAnswer(' . $question_id . ')" ></button>');
                            }
                        } else {
                            if($verified["correct"]){
                                echo(' <button id="verify' . $question_id . '" title="Verified Answer" class="btn-icon fa fa-3x fa-check-circle-o pull-right verifier verified" disabled ></button>');
                            }
                        }
                    echo('
                </div>
                <div class="col-sm-12" style="padding-right: 8px !important;">');
                    if($verified["correct"]){
                        echo('
                        <div id="verifiedTextQuestion' . $question_id . '" class="verifiedTextQuestion pull-right showthis">
                            <p class="instructorVerified">Instructor Verified</p>
                        </div>');
                    } else {
                        echo('
                        <div id="verifiedTextQuestion' . $question_id . '" class="verifiedTextQuestion pull-right hider">
                            <p class="instructorVerified">Instructor Verified</p>
                        </div>');
                    }
            echo('
                </div>
                </div>
            </div>
            
            
        </div>   
    </div> ');

    if(count($answers) > 0) {
    echo ('
        <div class="col-sm-12" >
            <h3>Additional Answers</h3>
        </div >
        ');
    }
    $i = 0;
    foreach ($answers as $answer) {
        $i++;
        $dateTime = new DateTime($answer["modified"]);
        $date = date_format($dateTime, "n/j/y");
        $time = date_format($dateTime, "g:i A");
        echo('<div class="col-sm-12">');
            $answer_id = $answer["answer_id"];
        echo('<div class="list-group-item">
                <div class="row">
                    <div class="col-sm-10">
                    <p>'.$answer["author"].' - ' . $date . ' - ' . $time . '</p>
                        
                        <div class="row">
                            <div class="col-sm-11 col-sm-offset-1">
                                <h4>'.$answer["answer_txt"].'</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-2">
                    <div class="row">
                    <div class="col-sm-12 pull-right">
                    ');
                    if(($USER->instructor) || ($answer["user_id"] == $USER->id)) {
                        echo('
                        <a onclick="return SQuestion.deleteAnswerConfirm();" href="actions/deleteAnswer.php?answer_id=' . $answer_id . '">
                            <span aria-hidden="true" class="fa fa-lg fa-trash pull-right adjuster"></span>
                            <span class="sr-only">Delete Question</span>
                        </a>
                        <a href="#editAnswer' . $i . '" data-toggle="modal" ><span class="fa fa-lg fa-pencil pull-right adjuster"></span></a>
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
                        $verifiedAnswer = $SQ_DAO->getAnswerVerified($answer_id);
                        if ($USER->instructor){
                            if($verifiedAnswer["correct"]){
                                echo('<button id="verifyAnswer' . $answer_id . '" title="Verified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier verified" onclick="SQuestion.verifyUserAnswer(' . $answer_id . ')"></button>');
                            } else {
                                echo('<button id="verifyAnswer' . $answer_id . '" title="Unverified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier unVerified" onclick="SQuestion.verifyUserAnswer(' . $answer_id . ')"></button>');
                            }
                        } else {
                            if($verifiedAnswer["correct"]){
                                echo('<button id="verifyAnswer' . $answer_id . '" title="Verified Answer" class="btn-icon fa fa-check-circle-o pull-right verifier verified" disabled></button>');
                            }
                        }
                    echo('
                    </div>
                    </div>
                        <div class="row"><div class="col-sm-2 pull-right">');
                            if($verifiedAnswer["correct"]){
                                echo('
                                    <div id="verifiedTextAnswer' . $answer_id . '"  class="verifiedTextAnswer showthis">
                                        <p class="instructorVerified">Instructor Verified</p>
                                    </div>');
                            } else {
                                echo('
                                    <div id="verifiedTextAnswer' . $answer_id . '"  class="verifiedTextAnswer hider">
                                        <p class="instructorVerified">Instructor Verified</p>
                                    </div>');
                            }echo('
                        </div></div>
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
                </div>
                <div class="col-sm-12 text-left spaceAbove ">
                    <a href="#" onclick="toggleAddAnswer();" id ="addAnswerButton" class="btn btn-success small-shadow'); if(!$_SESSION["show"]){echo (' hider'); } echo('"><span class="fa fa-plus"></span> Add Answer</a>
                    <a href="question-home.php"  class="btn btn-info small-shadow pull-right">Back</a>
                </div>
            </div>
        </div>
    </div>
</div>');
$_SESSION["show"] = false;
$OUTPUT->footerStart();
?>
    <!-- Our main javascript file for tool functions -->
    <script src="scripts/main.js" type="text/javascript"></script>
    <script>
        $(window).on('resize', function() {

            var points1 = document.getElementsByClassName("pointsPlaceViewQuestion701");
            var points2 = document.getElementsByClassName("pointsPlaceViewQuestion936");
            var points3 = document.getElementsByClassName("pointsPlaceViewQuestion1100");
            var negPoints1 = document.getElementsByClassName("negativePointsPlaceViewQuestion1100");
            var negPoints2 = document.getElementsByClassName("negativePointsPlaceViewQuestion936");
            var negPoints3 = document.getElementsByClassName("negativePointsPlaceViewQuestion701");
            var points =[];
            var negPoints=[];
            if(points1.length > 0){
                points = points1;
            } else if(points2.length > 0){
                points = points2;
            } else if(points3.length > 0){
                points = points3;
            }
            if(negPoints1.length > 0){
                negPoints = negPoints1;
            } else if(negPoints2.length > 0){
                negPoints = negPoints2;
            } else if(negPoints3.length > 0){
                negPoints = negPoints3;
            }

            var length = 0;
            var negLength = 0;
            if(points.length > 0) {
                length = points.length;
            }
            if(negPoints.length > 0) {
                negLength = negPoints.length;
            }

            if($(window).width() > 1100){
                for (var i = 0; i < length; i++){
                    var x = points[0];
                    if(x.classList.contains("pointsPlaceViewQuestion936")){
                        x.classList.remove("pointsPlaceViewQuestion936");
                        x.classList.add("pointsPlaceViewQuestion1100");
                    } else if (x.classList.contains("pointsPlaceViewQuestion701")){
                        x.classList.remove("pointsPlaceViewQuestion701");
                        x.classList.add("pointsPlaceViewQuestion1100");
                    }
                }
                for (var i = 0; i < negLength; i++){
                    var x = negPoints[0];
                    if(x.classList.contains("negativePointsPlaceViewQuestion936")){
                        x.classList.remove("negativePointsPlaceViewQuestion936");
                        x.classList.add("negativePointsPlaceViewQuestion1100");
                    } else if (x.classList.contains("negativePointsPlaceViewQuestion701")){
                        x.classList.remove("negativePointsPlaceViewQuestion701");
                        x.classList.add("negativePointsPlaceViewQuestion1100");
                    }
                }
            } else if($(window).width() > 935) {
                for (var i = 0; i < length; i++){
                    var x = points[0];
                    if(x.classList.contains("pointsPlaceViewQuestion1100")){
                        x.classList.remove("pointsPlaceViewQuestion1100");
                        x.classList.add("pointsPlaceViewQuestion936");
                    } else if (x.classList.contains("pointsPlaceViewQuestion701")){
                        x.classList.remove("pointsPlaceViewQuestion701");
                        x.classList.add("pointsPlaceViewQuestion936");
                    }
                }
                for (var i = 0; i < negLength; i++){
                    var x = negPoints[0];
                    if(x.classList.contains("negativePointsPlaceViewQuestion1100")){
                        x.classList.remove("negativePointsPlaceViewQuestion1100");
                        x.classList.add("negativePointsPlaceViewQuestion936");
                    } else if (x.classList.contains("negativePointsPlaceViewQuestion701")){
                        x.classList.remove("negativePointsPlaceViewQuestion701");
                        x.classList.add("negativePointsPlaceViewQuestion936");
                    }
                }
            } else {
                for (var i = 0; i < length; i++){
                    var x = points[0];
                    if(x.classList.contains("pointsPlaceViewQuestion1100")){
                        x.classList.remove("pointsPlaceViewQuestion1100");
                        x.classList.add("pointsPlaceViewQuestion701");
                    } else if (x.classList.contains("pointsPlaceViewQuestion936")){
                        x.classList.remove("pointsPlaceViewQuestion936");
                        x.classList.add("pointsPlaceViewQuestion701");
                    }
                }
                for (var i = 0; i < negLength; i++){
                    var x = negPoints[0];
                    if(x.classList.contains("negativePointsPlaceViewQuestion1100")){
                        x.classList.remove("negativePointsPlaceViewQuestion1100");
                        x.classList.add("negativePointsPlaceViewQuestion701");
                    } else if (x.classList.contains("negativePointsPlaceViewQuestion936")){
                        x.classList.remove("negativePointsPlaceViewQuestion936");
                        x.classList.add("negativePointsPlaceViewQuestion701");
                    }
                }
            }
        });
    </script>
<?php
$OUTPUT->footerEnd();
