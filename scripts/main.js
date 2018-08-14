/*Main Javascript File*/
$(document).ready(function(){
    // Clear any entered question text on modal hide
    var addModal = $("#addOrEditQuestion");
    addModal.on('hidden.bs.modal', function() {
        $("#questionText").val('');
    });
    addModal.on('shown.bs.modal', function() {
        $("#questionText").focus();
    });

    $("#editTitleLink").on("click", function() {
        closeNav();
        $("#toolTitle").focus();
    });

    $("#toolTitle").on("blur", function() {
        saveTitle();
    }).on("keypress", function(e) {
        if(e.which === 13) {
            this.blur();
        }
    });
});
function saveTitle() {
    var sessionId = $("#sess").val();
    var titleText = $("#toolTitle").text();
    $.ajax({
        type: "post",
        url: "actions/UpdateMainTitle.php?PHPSESSID="+sessionId,
        data: {
            "toolTitle" : titleText,
            "nonav" : true
        }
    });
}
function confirmResetTool() {
    return confirm("Are you sure you want to remove all questions and answers from this tool? This cannot be undone.");
}

function openSideNav() {
    document.getElementById("sideNav").style.left = "0";
}
function closeNav() {
    document.getElementById("sideNav").style.left = "-200px";
}


function editQuestionText(questionId) {
    $("#questionText"+questionId).hide();
    var theForm = $("#questionTextForm"+questionId);
    theForm.parent().find('.question-actions').hide();
    theForm.parent().find('.question-answers').hide();
    theForm.addClass("fadeInFast");
    theForm.show();
    theForm.find('textarea[name="questionText"]').focus();
}
function cancelEditQuestionText(questionId) {
    $("#questionText"+questionId).fadeIn(400);
    var theForm = $("#questionTextForm"+questionId);
    theForm.parent().find('.question-actions').show();
    theForm.parent().find('.question-answers').show();
    theForm.removeClass("fadeInFast");
    theForm.hide();
}

function toggleAddAnswer() {
    var div = document.getElementById("addAnswer");
    var button = document.getElementById("addAnswerButton");
    if (div.style.display === "none") {
        div.style.display = "block";
        button.style.display = "none";
    } else {
        div.style.display = "none";
        button.style.display = "inline-block";
    }
}

var SQuestion = (function () {
    var sQuestion = {};
    sQuestion.changeStateUp = function (id) {
        var directionUp = "upVote" + id;
        var directionDown = "downVote" + id;
        var pointContainerId = "points" + id;
        var pointContainer = document.getElementById(pointContainerId);
        var points = pointContainer.innerHTML;
        var pointVal =parseInt(points);
        var upButton = document.getElementById(directionUp);
        if(upButton.classList.contains('btn-active-up')){
            document.getElementById(directionUp).classList.remove('btn-active-up');
            var direction = "down";
            var vote = "none";
            pointContainer.innerHTML= pointVal - 1;
        } else {
            document.getElementById(directionUp).classList.add('btn-active-up');
            var direction = "up";
            var vote = "up";
            pointContainer.innerHTML= pointVal + 1;
        }
        document.getElementById(directionDown).classList.remove('btn-active-down');

        var sessionId = $("#sess").val();
        $.ajax({
            type: 'POST',
            url: "actions/updatePoints.php?PHPSESSID="+sessionId,
            data: {
                "direction": direction,
                "id": id,
                "vote": vote
            }
        });

    };

    sQuestion.changeStateDown = function (id) {
        var directionUp = "upVote" + id;
        var directionDown = "downVote" + id;
        var pointContainerId = "points" + id;
        var pointContainer = document.getElementById(pointContainerId);
        var points = pointContainer.innerHTML;
        var pointVal =parseInt(points);
        var downButton = document.getElementById(directionDown);
        if(downButton.classList.contains('btn-active-down')){
            var direction = "up";
            var vote = "none";
            document.getElementById(directionDown).classList.remove('btn-active-down');
            pointContainer.innerHTML= pointVal + 1;
        } else {
            var direction = "down";
            var vote = "down";
            document.getElementById(directionDown).classList.add('btn-active-down');
            pointContainer.innerHTML= pointVal - 1;
        }
        document.getElementById(directionUp).classList.remove('btn-active-up');

        var sessionId = $("#sess").val();
        $.ajax({
            type: 'POST',
            url: "actions/updatePoints.php?PHPSESSID="+sessionId,
            data: {
                "direction": direction,
                "id": id,
                "vote": vote
            }
        });

    };

    sQuestion.changeStateUpAnswer = function (id) {
        var directionUp = "upVote" + id;
        var directionDown = "downVote" + id;
        var pointContainerId = "points" + id;
        var pointContainer = document.getElementById(pointContainerId);
        var points = pointContainer.innerHTML;
        var pointVal =parseInt(points);
        var upButton = document.getElementById(directionUp);
        if(upButton.classList.contains('btn-active-up')){
            document.getElementById(directionUp).classList.remove('btn-active-up');
            var direction = "down";
            var vote = "none";
            pointContainer.innerHTML= pointVal - 1;
        } else {
            document.getElementById(directionUp).classList.add('btn-active-up');
            var direction = "up";
            var vote = "up";
            pointContainer.innerHTML= pointVal + 1;
        }
        document.getElementById(directionDown).classList.remove('btn-active-down');

        var sessionId = $("#sess").val();
        $.ajax({
            type: 'POST',
            url: "actions/updatePoints.php?PHPSESSID="+sessionId,
            data: {
                "direction": direction,
                "id": id,
                "vote": vote
            }
        });

    };

    sQuestion.changeStateDownAnswer = function (id) {
        var directionUp = "upVote" + id;
        var directionDown = "downVote" + id;
        var pointContainerId = "points" + id;
        var pointContainer = document.getElementById(pointContainerId);
        var points = pointContainer.innerHTML;
        var pointVal =parseInt(points);
        var downButton = document.getElementById(directionDown);
        if(downButton.classList.contains('btn-active-down')){
            var direction = "up";
            var vote = "none";
            document.getElementById(directionDown).classList.remove('btn-active-down');
            pointContainer.innerHTML= pointVal + 1;
        } else {
            var direction = "down";
            var vote = "down";
            document.getElementById(directionDown).classList.add('btn-active-down');
            pointContainer.innerHTML= pointVal - 1;
        }
        document.getElementById(directionUp).classList.remove('btn-active-up');

        var sessionId = $("#sess").val();
        $.ajax({
            type: 'POST',
            url: "actions/updatePoints.php?PHPSESSID="+sessionId,
            data: {
                "direction": direction,
                "id": id,
                "vote": vote
            }
        });

    };

    sQuestion.verifyAnswer = function (id) {
        var verifyBtnId = "verify" + id;
        var verifyBtn = document.getElementById(verifyBtnId);
        var correct = 0;
        if (verifyBtn.classList.contains('unVerified')) {
            correct = 1;
            verifyBtn.classList.remove('unVerified');
            verifyBtn.classList.add('verified');
            document.getElementById(verifyBtnId).title = "Verified Answer";
        } else {
            correct = 0;
            verifyBtn.classList.remove('verified');
            verifyBtn.classList.add('unVerified');
            document.getElementById(verifyBtnId).title = "Unverified Answer";
        }

        var sessionId = $("#sess").val();
        $.ajax({
            type: 'POST',
            url: "actions/updateVerify.php?PHPSESSID=" + sessionId,
            data: {
                "id": id,
                "correct": correct
            }
        });
    };

    sQuestion.verifyUserAnswer = function (id) {
        var verifyBtnId = "verifyAnswer" + id;
        var verifyBtn = document.getElementById(verifyBtnId);
        var correct = 0;
        if (verifyBtn.classList.contains('unVerified')) {
            correct = 1;
            verifyBtn.classList.remove('unVerified');
            verifyBtn.classList.add('verified');
            document.getElementById(verifyBtnId).title = "Verified Answer";
        } else {
            correct = 0;
            verifyBtn.classList.remove('verified');
            verifyBtn.classList.add('unVerified');
            document.getElementById(verifyBtnId).title = "Unverified Answer";
        }

        var sessionId = $("#sess").val();
        $.ajax({
            type: 'POST',
            url: "actions/updateAnswerVerified.php?PHPSESSID=" + sessionId,
            data: {
                "id": id,
                "correct": correct
            }
        });
    };


    sQuestion.deleteQuestionConfirm = function (id) {
        return confirm("Are you sure you want to delete this question and all associated answers? This cannot be undone.");
    };

    sQuestion.deleteAnswerConfirm = function (id) {
        return confirm("Are you sure you want to delete this Answer? This cannot be undone.");
    };

    return sQuestion;
})();