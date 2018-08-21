<?php
require_once('../config.php');
require_once('dao/SQ_DAO.php');

use \Tsugi\Core\LTIX;
use \SQ\DAO\SQ_DAO;

// Retrieve the launch data if present
$LAUNCH = LTIX::requireData();

$p = $CFG->dbprefix;

$SQ_DAO = new SQ_DAO($PDOX, $p);

$skipSplash = $SQ_DAO->skipSplash($USER->id);
$toolTitle = $SQ_DAO->getMainTitle($_SESSION["sq_id"]);

 //Start of the output
$OUTPUT->header();

echo ('<link rel="stylesheet" type="text/css" href="styles/splash.css">
        <link rel="stylesheet" type="text/css" href="styles/animations.css">');

$OUTPUT->bodyStart();
?>

    <div class="container-fluid">

        <img src="images/lisidore-small.png" class="splash-img slideInRight">

        <div class="row">
            <div class="col-sm-6 col-sm-offset-1" id="splashMessage">

                <h1 class="fadeIn">Study Questions</h1>

                <p class="fadeIn text-justify">
                    Create a space where students can share study questions and get answers or something like that. Extra info because radical text needs to be two lines
                </p>

                <div class="toggle-container fadeIn">
                    <div class="checkbox">
                        <input type="hidden" id="sess" value="<?php echo($_GET["PHPSESSID"]) ?>">
                        <label><input id="toggleSplash" type="checkbox" value="showsplash" <?php if(!$skipSplash) echo('checked="checked"'); ?>
                                      onchange="toggleSkipSplash();"> Show this screen with every fresh install of this tool.</label>
                    </div>
                    <span id="spinner" class="fa fa-spinner fa-pulse" style="display:none;"></span>
                    <span id="done" class="fa fa-check" style="display:none;"></span>
                </div>

            </div>
        </div>
        <div class="col-sm-5 col-sm-offset-1" id="createFormContainer">
            <form method="post" action="actions/UpdateMainTitle.php" id="createForm" class="fadeInUp">
                <h3>Add the Study Question Tool Title</h3>
                <div class="form-group">
                    <label for="toolTitle">Title</label>
                    <input type="text" class="form-control" id="toolTitle" name="toolTitle" placeholder="Study Questions" autofocus value="<?php echo($toolTitle); ?>">
                </div>
                <button type="submit" class="btn btn-primary">Get Started</button>
            </form>
        </div>
    </div>

<?php
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
