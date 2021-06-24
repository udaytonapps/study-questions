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

$hasRosters = LTIX::populateRoster(false);

include("menu.php");

// Start of the output
$OUTPUT->header();

echo ('<link rel="stylesheet" type="text/css" href="DataTables/datatables.min.css"/>');

$OUTPUT->bodyStart();

$OUTPUT->topNav($menu);

echo '<div class="container">';

$OUTPUT->pageTitle($title, false, false);
if (!$hasRosters) {
   echo '<h4>The contributions feature requires that rosters are enabled.</h4>';
} else {
    $rosterData = $GLOBALS['ROSTER']->data;
?>
    <p class="lead">
        The following is an instructor-only summary of the number of contributions each student.
    </p>
    <div class="table-responsive">
        <table id="results" class="table table-striped table-bordered" style="width:100%;">
            <thead>
            <tr>
                <th>Student Name</th>
                <th>Questions/Answers Added</th>
                <th>Additional Answers Added</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($rosterData as $student) {
                if ($student["role"] == 'Learner') {
                    $userId = $SQ_DAO->getTsugiUserId($student["user_id"]);
                    $questionsAdded = $SQ_DAO->countQuestionsForStudent($_SESSION["sq_id"], $userId);
                    $additionalAnswers = $SQ_DAO->countAdditionalAnswersForStudent($_SESSION["sq_id"], $userId);
                    echo '<tr><td>'.$student["person_name_family"].', '.$student["person_name_given"].'</td><td>'.$questionsAdded.'</td><td>'.$additionalAnswers.'</td></tr>';
                }
            }
            ?>
            </tbody>
        </table>
    </div>
<?php
}
$OUTPUT->footerStart();
?>
    <script type="text/javascript" src="DataTables/datatables.min.js"></script>
    <script>
        $(document).ready(function () {
            $("#results").DataTable({
                order: [[0, "asc"]],
                dom: 'r<B>itp',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<span class="fa fa-download" aria-hidden="true"></span> Download All Results (.xlsx)',
                        title: '<?=$CONTEXT->title?>_<?=$title?>_Results',
                        className: 'btn btn-primary'
                    }
                ]
            });
        });
    </script>
<?php
$OUTPUT->footerEnd();
