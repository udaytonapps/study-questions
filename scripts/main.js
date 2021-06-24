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
});
function editQuestionText(questionId) {
    $("#questionText"+questionId).hide();
    var theForm = $("#questionTextForm"+questionId);
    theForm.parent().find('.question-actions').hide();
    theForm.parent().find('.question-answers').hide();
    theForm.addClass("fadeInFast");
    theForm.show();
    theForm.find('textarea[name="questionText"]').focus();
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
const SQuestion = (function () {
    let sQuestion = {};
    sQuestion.changeStateUp = function (id) {
        var directionUp = "upVote" + id;
        var directionDown = "downVote" + id;
        var pointContainerId = "points" + id;
        var pointContainer = document.getElementById(pointContainerId);
        var points = pointContainer.innerHTML;
        var pointVal =parseInt(points);
        var upButton = document.getElementById(directionUp);
        var downButton = document.getElementById(directionDown);
        if(upButton.classList.contains('btn-active-up')){
            document.getElementById(directionUp).classList.remove('btn-active-up');
            var direction = "down";
            var vote = "none";
            pointContainer.innerHTML= pointVal - 1;
        } else {
            document.getElementById(directionUp).classList.add('btn-active-up');
            var vote = "up";
            if(downButton.classList.contains('btn-active-down')){
                var direction = "doubleUp";
                pointContainer.innerHTML= pointVal + 2;
            } else {
                var direction = "up";
                pointContainer.innerHTML= pointVal + 1;
            }
        }
        document.getElementById(directionDown).classList.remove('btn-active-down');

        $.ajax({
            type: 'POST',
            url: "actions/updatePoints.php?PHPSESSID="+sess,
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
        var upButton = document.getElementById(directionUp);
        var downButton = document.getElementById(directionDown);
        if(downButton.classList.contains('btn-active-down')){
            var direction = "up";
            var vote = "none";
            document.getElementById(directionDown).classList.remove('btn-active-down');
            pointContainer.innerHTML= pointVal + 1;
        } else {
            var vote = "down";
            document.getElementById(directionDown).classList.add('btn-active-down');
            if(upButton.classList.contains('btn-active-up')){
                var direction = "doubleDown";
                pointContainer.innerHTML= pointVal - 2;
            } else {
                var direction = "down";
                pointContainer.innerHTML= pointVal - 1;
            }
        }
        document.getElementById(directionUp).classList.remove('btn-active-up');

        $.ajax({
            type: 'POST',
            url: "actions/updatePoints.php?PHPSESSID="+sess,
            data: {
                "direction": direction,
                "id": id,
                "vote": vote
            }
        });
    };

    sQuestion.verifyAnswer = function (id) {
        let verifyBtnId = "verify" + id;
        let correct = _restyleVerify(verifyBtnId);

        $.ajax({
            type: 'POST',
            url: "actions/updateVerify.php?PHPSESSID=" + sess,
            data: {
                "id": id,
                "correct": correct
            }
        });
    };

    sQuestion.verifyUserAnswer = function (id) {
        let verifyBtnId = "verifyA" + id;
        let correct = _restyleVerify(verifyBtnId);

        $.ajax({
            type: 'POST',
            url: "actions/updateAnswerVerified.php?PHPSESSID=" + sess,
            data: {
                "id": id,
                "correct": correct
            }
        });
    };

    const _restyleVerify = function (verifyBtnId) {
        let verifyBtn = document.getElementById(verifyBtnId);
        let correct;
        if (verifyBtn.classList.contains('unVerified')) {
            correct = 1;
            verifyBtn.title = "Verified Answer";
            $(verifyBtn).find(".button-text").text("Verified answer");
        } else {
            correct = 0;
            verifyBtn.title = "Unverified Answer";
            $(verifyBtn).find(".button-text").text("Mark answer as verified");
        }
        $(verifyBtn).parent(".list-group-item").toggleClass("bg-success");
        $(verifyBtn).find("span.fa").toggleClass("fa-check");
        $(verifyBtn).find("span.fa").toggleClass("fa-square-o");
        $(verifyBtn).toggleClass("unVerified");
        $(verifyBtn).toggleClass("verified");
        return correct;
    }

    sQuestion.updateUnderstood = function (id) {
        var underStandBtnId = "underStand" + id;
        var underStandBtn = document.getElementById(underStandBtnId);
        var understood = 0;
        if (underStandBtn.classList.contains('unVerified')) {
            understood = 1;
            underStandBtn.classList.remove('unVerified');
            underStandBtn.classList.add('verified');
            underStandBtn.classList.remove('fa-square-o');
            underStandBtn.classList.add('fa-check-square');
        } else {
            understood = 0;
            underStandBtn.classList.remove('verified');
            underStandBtn.classList.add('unVerified');
            underStandBtn.classList.remove('fa-check-square');
            underStandBtn.classList.add('fa-square-o');
        }

        $.ajax({
            type: 'POST',
            url: "actions/updateUnderstood.php?PHPSESSID=" + sess,
            data: {
                "id": id,
                "understood": understood
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