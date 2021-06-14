<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$name =$SQ_DAO->findDisplayName($USER->id);
$toolTitle = $SQ_DAO->getMainTitle($_SESSION["sq_id"]);

//Start of the output
$OUTPUT->header();

echo ('<link rel="stylesheet" type="text/css" href="styles/splash.css">
        <link rel="stylesheet" type="text/css" href="styles/animations.css">');

$OUTPUT->bodyStart();

echo ('
    <div class="container-fluid">

        <img src="images/standing_idea.png" class="student-splash-img slideInLeft">

        <div class="row">
            <div class="col-sm-6 col-sm-offset-1" id="splashMessage">
');
if ("Study Questions" !== $toolTitle) {
    echo '<h1 class="fadeIn"><small style="color:#fff;">Study Questions</small><br>'.$toolTitle.'</h1>';
} else {
    echo '<h1 class="fadeIn">Study Questions</h1>';
}
echo('
                <p class="fadeIn ">
                    You must add a question/answer before you can see questions/answers submitted by others.
                </p>

            </div>
        </div>
        <div class="col-sm-5 col-sm-offset-6" id="createFirstQuestion">
            <form method="post" id="createForm" action="actions/addstudyquestion.php">
                <input type="hidden" name="questionId" id="questionId" value="-1">
                <input type="hidden" name="username" id="username" value="' .$name . '">
                <label for="questionText"><h2 class="noTopMargin">Question</h2></label>
                <textarea class="form-control" name="questionText" id="questionText" rows="4" autofocus required></textarea>
                <label for="answerText" class="spaceAbove"><h2>Answer</h2></label>
                <textarea class="form-control" name="answerText" id="answerText" rows="4" autofocus required></textarea>
                <input type="submit" form="createForm" class="btn btn-success spaceAbove" value="Submit">
            </form>
        </div>
    </div>
');
$OUTPUT->footerStart();
?>
    <script type="text/javascript">
        function toggleSkipSplash() {
            $("#spinner").show();
            var sess = $('input#sess').val();
            $.ajax({
                url: "actions/ToggleSkipSplashPage.php?PHPSESSID="+sess,
                success: function(response){
                    $("#spinner").hide();
                    $("#done").show();
                    setTimeout(function() {
                        $("#done").fadeOut("slow");
                    }, 5);
                }
            });
        }
    </script>
<?php
$OUTPUT->footerEnd();
