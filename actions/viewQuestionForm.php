<?php
require_once "../../config.php";

use \Tsugi\Core\LTIX;

$LAUNCH = LTIX::requireData();

$_SESSION["questionId"] = $_POST["viewQuestionId"];

header('Location: ' . addSession('../view-question.php'));
