<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use SQ\DAO\SQ_DAO;
use Tsugi\Core\LTIX;
use Tsugi\UI\SettingsForm;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

// Retrieve the launch data if present
$LTI = LTIX::requireData();

if (SettingsForm::isSettingsPost()) {
    if (!isset($_POST["studytitle"]) || trim($_POST["studytitle"]) === '') {
        $_SESSION["error"] = __('Title cannot be blank.');
    } else {
        SettingsForm::handleSettingsPost();
        $_SESSION["success"] = __('All settings saved.');
    }
    header('Location: ' . addSession('index.php'));
    return;
}

$title = $LAUNCH->link->settingsGet("studytitle", false);

if (!$title) {
    $LAUNCH->link->settingsSet("studytitle", $LAUNCH->link->title);
    $title = $LAUNCH->link->title;
}

SettingsForm::start();
SettingsForm::text('studytitle', __('Title'));
SettingsForm::checkbox('seecontent', __('Allow students to see the tool content before supplying a study question and answer.'));
SettingsForm::end();

if (isset($_GET["sort"])) {
    $_SESSION["sort"] = ($_GET["sort"] == "new");
}
$sortByNew = $_SESSION["sort"] ?? false;

include("menu.php");

// Start of the output
$OUTPUT->header();
?>
    <!-- Our main css file that overrides default Tsugi styling -->
    <link href="<?= $CFG->staticroot ?>/bootstrap-3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="styles/main.css">
<?php
$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container-fluid">';

$OUTPUT->pageTitle($title, true, $USER->instructor);

$questions = $SQ_DAO->getQuestions($_SESSION["sq_id"], $sortByNew);

$_SESSION["show"] = false;

$name = $SQ_DAO->findDisplayName($USER->id);
if ($USER->instructor) {
    echo '<p class="lead">This Study Questions tool is now set up and ready for student access. Students will be asked to add a question and answer that can help their peers study for an upcoming assessment.</p>';
} else {
    echo '<p class="lead">Click on the cards below to study the questions and answers added by you and your peers. You can also add additional answers to any question and see the other answers that were added.';
}

?>
    <p>
        <a href="#addQuestion" data-toggle="modal" class="btn btn-success"><span class="fa fa-plus"></span> Add Question</a>
    </p>
    <hr>
    <form method="get" action="question-home.php" id="sortForm" class="form form-inline pull-right">
        <div class="form-group">
            <label for="sort">Sort by:</label>
            <select class="form-control" id="sort" name="sort">
                <option value="votes" <?= $sortByNew ? "": "selected"?>>Most Votes</option>
                <option value="new" <?= $sortByNew ? "selected": ""?>>Newest</option>
            </select>
        </div>
    </form>
    <h3>All Questions</h3>
    <div class="list-group" style="clear:both;">
        <?php
        foreach ($questions as $question) {
            $question_id = $question["question_id"];
            $previousVote = $SQ_DAO->getStudentVote($question_id, $USER->id);
            echo('<div class="list-group-item" style="display:flex;">
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
            $dateTime = new DateTime($question["modified"]);
            $date = date_format($dateTime, "n/j/y");
            $time = date_format($dateTime, "g:i A");
            echo('<div style="flex-grow:1;padding-bottom: 2rem;">
                    <p class="questionText"><a href="view-question.php?q=' . $question_id . '">' . $question["question_txt"] . '</a></p>
                    <p class="small text-muted" style="position:absolute; bottom: 0;">Submitted by ' . $question["author"] . ' on ' . $date . ' at ' . $time . '</p>
                  </div>
    </div>');
        }
        ?>
    </div> <!-- End list group -->
    </div> <!-- End container -->
    <div class="modal fade" id="addQuestion" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title">Add Question and Answer</h3>
                </div>
                <form method="post" id="addQuestionForm" action="actions/addstudyquestion.php">
                    <div class="modal-body">
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
                    </div>
                    <div class="modal-footer" style="text-align: left;">
                        <input type="submit" form="addQuestionForm" class="btn btn-success" value="Save">
                        <button type="button" class="btn btn-link" data-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php
$OUTPUT->helpModal("Study Questions", __('
                        <h4>Help Goes Here</h4>
                        '));

$OUTPUT->footerStart();
?>
    <!-- Our main javascript file for tool functions -->
    <script>
        const sess = '<?=$_GET["PHPSESSID"]?>';
        $(document).ready(function(){
           $("#sort").on("change", function() {
               $("#sortForm").submit();
            })
        });
    </script>
    <script src="scripts/main.js" type="text/javascript"></script>
<?php
$OUTPUT->footerEnd();
