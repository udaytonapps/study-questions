<?php
namespace SQ\DAO;

class SQ_DAO{

    private $PDOX;
    private $p;

    public function __construct($PDOX, $p) {
        $this->PDOX = $PDOX;
        $this->p = $p;
    }

    function skipSplash($user_id) {
        $query = "SELECT skip_splash FROM {$this->p}sq_splash WHERE user_id = :userId";
        $arr = array(':userId' => $user_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return ($context && $context["skip_splash"]);
    }

    function toggleSkipSplash($user_id) {
        $skip = $this->skipSplash($user_id) ? 0 : 1;
        $query = "INSERT INTO {$this->p}sq_splash (user_id, skip_splash) VALUES (:userId, ".$skip.") ON DUPLICATE KEY UPDATE skip_splash = ".$skip;
        $arr = array(':userId' => $user_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function getOrCreateMain($user_id, $context_id, $link_id, $current_time) {
        $main_id = $this->getMainID($context_id, $link_id);
        if (!$main_id) {
            return $this->createMain($user_id, $context_id, $link_id, $current_time);
        } else {
            return $main_id;
        }
    }

    function getMainID($context_id, $link_id) {
        $query = "SELECT sq_id FROM {$this->p}sq_main WHERE context_id = :context_id AND link_id = :link_id";
        $arr = array(':context_id' => $context_id, ':link_id' => $link_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context["sq_id"];
    }

    function createMain($user_id, $context_id, $link_id, $current_time) {
        $query = "INSERT INTO {$this->p}sq_main (user_id, context_id, link_id, modified) VALUES (:userId, :contextId, :linkId, :currentTime);";
        $arr = array(':userId' => $user_id, ':contextId' => $context_id, ':linkId' => $link_id, ':currentTime' => $current_time);
        $this->PDOX->queryDie($query, $arr);
        return $this->PDOX->lastInsertId();
    }

    function getMainTitle($sq_id) {
        $query = "SELECT title FROM {$this->p}sq_main WHERE sq_id = :sqId";
        $arr = array(':sqId' => $sq_id);
        $title = $this->PDOX->rowDie($query, $arr)["title"];
        return $title ? $title["title"] : "Study Questions";
    }

    function updateMainTitle($sq_id, $title, $current_time) {
        $query = "UPDATE {$this->p}sq_main set title = :title, modified = :currentTime WHERE sq_id = :sqId;";
        $arr = array(':title' => $title, ':currentTime' => $current_time, ':sqId' => $sq_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function deleteMain($sq_id, $user_id) {
        $query = "DELETE FROM {$this->p}sq_main WHERE sq_id = :mainId AND user_id = :userId";
        $arr = array(':mainId' => $sq_id, ':userId' => $user_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function getQuestions($sq_id) {
        $query = "SELECT * FROM {$this->p}sq_questions WHERE sq_id = :sqId ORDER BY votes DESC";
        $arr = array(':sqId' => $sq_id);
        return $this->PDOX->allRowsDie($query, $arr);
    }

    function getQuestionById($question_id) {
        $query = "SELECT * FROM {$this->p}sq_questions WHERE question_id = :questionId;";
        $arr = array(':questionId' => $question_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function createFirstQuestion($sq_id, $question_text, $current_time) {
        $nextNumber = $this->getNextQuestionNumber($sq_id);
        $query = "INSERT INTO {$this->p}sq_questions (sq_id, question_num, question_txt, modified, permanent) VALUES (:sqId, :questionNum, :questionText, :currentTime, TRUE);";
        $arr = array(':sqId' => $sq_id, ':questionNum' => $nextNumber, ':questionText' => $question_text, ':currentTime' => $current_time);
        $this->PDOX->queryDie($query, $arr);
        return $this->PDOX->lastInsertId();
    }

    function createQuestion($sq_id, $question_text, $answerText, $current_time, $author, $user_id) {
        $nextNumber = $this->getNextQuestionNumber($sq_id);
        $query = "INSERT INTO {$this->p}sq_questions (sq_id, question_num, question_txt, answer_txt, modified, author, user_id) VALUES (:sqId, :questionNum, :questionText, :answer_txt, :currentTime, :author, :user_id);";
        $arr = array(':sqId' => $sq_id, ':questionNum' => $nextNumber, ':questionText' => $question_text, ':answer_txt' => $answerText, ':currentTime' => $current_time, ':author' => $author, ':user_id' => $user_id);
        $this->PDOX->queryDie($query, $arr);
        return $this->PDOX->lastInsertId();
    }

    function updateQuestion($question_id, $question_text, $answerText, $current_time) {
        $query = "UPDATE {$this->p}sq_questions set question_txt = :questionText, answer_txt = :answer_txt, modified = :currentTime WHERE question_id = :questionId;";
        $arr = array(':questionId' => $question_id, ':questionText' => $question_text, ':answer_txt' => $answerText,':currentTime' => $current_time);
        $this->PDOX->queryDie($query, $arr);
    }

    function getNextQuestionNumber($sq_id) {
        $query = "SELECT MAX(question_num) as lastNum FROM {$this->p}sq_questions WHERE sq_id = :sqId";
        $arr = array(':sqId' => $sq_id);
        $lastNum = $this->PDOX->rowDie($query, $arr)["lastNum"];
        return $lastNum + 1;
    }

    function countAnswersForQuestion($question_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->p}sq_answer WHERE question_id = :questionId;";
        $arr = array(':questionId' => $question_id);
        return $this->PDOX->rowDie($query, $arr)["total"];
    }

    function deleteQuestion($question_id) {
        $query = "DELETE FROM {$this->p}sq_questions WHERE question_id = :questionId;";
        $arr = array(':questionId' => $question_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function deleteAnswer($answer_id) {
        $query = "DELETE FROM {$this->p}sq_answer WHERE answer_id = :answer_id;";
        $arr = array(':answer_id' => $answer_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function fixUpQuestionNumbers($sq_id) {
        $query = "SET @question_num = 0; UPDATE {$this->p}sq_questions set question_num = (@question_num:=@question_num+1) WHERE sq_id = :sqId ORDER BY question_num";
        $arr = array(':sqId' => $sq_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function getUsers($sq_id) {
        $query = "SELECT DISTINCT user_id FROM {$this->p}sq_answer WHERE sq_id = :sqId;";
        $arr = array(':sqId' => $sq_id);
        return $this->PDOX->allRowsDie($query, $arr);
    }

    function getUsersWithAnswers($sq_id) {
        $query = "SELECT DISTINCT user_id FROM {$this->p}sq_answer a join {$this->p}sq_questions q on a.question_id = q.question_id WHERE q.sq_id = :sqId;";
        $arr = array(':sqId' => $sq_id);
        return $this->PDOX->allRowsDie($query, $arr);
    }

    function getStudentAnswerForQuestion($question_id, $user_id) {
        $query = "SELECT * FROM {$this->p}sq_answer WHERE question_id = :questionId AND user_id = :userId; ";
        $arr = array(':questionId' => $question_id, ':userId' => $user_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function getMostRecentAnswerDate($user_id, $sq_id) {
        $query = "SELECT max(a.modified) as modified FROM {$this->p}sq_answer a join {$this->p}sq_questions q on a.question_id = q.question_id WHERE a.user_id = :userId AND q.sq_id = :sqId;";
        $arr = array(':userId' => $user_id, ':sqId' => $sq_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context['modified'];
    }

    function createAnswer($user_id, $author, $question_id, $answer_txt, $current_time,  $sq_id) {
        $query = "INSERT INTO {$this->p}sq_answer (user_id, author, question_id, answer_txt, modified, sq_id) VALUES (:user_id, :author, :questionId, :answerTxt, :currentTime, :sq_id);";
        $arr = array(':user_id' => $user_id, ':author' => $author,':questionId' => $question_id, ':answerTxt' => $answer_txt, ':currentTime' => $current_time, ':sq_id' => $sq_id);
        $this->PDOX->queryDie($query, $arr);
        return $this->PDOX->lastInsertId();
    }

    function updateAnswer($answer_id, $answer_txt, $current_time) {
        $query = "UPDATE {$this->p}sq_answer set answer_txt = :answerTxt, modified = :currentTime where answer_id = :answerId;";
        $arr = array(':answerId' => $answer_id, ':answerTxt' => $answer_txt, ':currentTime' => $current_time);
        $this->PDOX->queryDie($query, $arr);
    }

    function getAllAnswersToQuestion($question_id) {
        $query = "SELECT * FROM {$this->p}sq_answer WHERE question_id = :questionId;";
        $arr = array(':questionId' => $question_id);
        return $this->PDOX->allRowsDie($query, $arr);
    }

    function getAnswerById($answer_id) {
        $query = "SELECT * FROM {$this->p}sq_answer WHERE answer_id = :answerId;";
        $arr = array(':answerId' => $answer_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function findEmail($user_id) {
        $query = "SELECT email FROM {$this->p}lti_user WHERE user_id = :user_id;";
        $arr = array(':user_id' => $user_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context["email"];
    }

    function findDisplayName($user_id) {
        $query = "SELECT displayname FROM {$this->p}lti_user WHERE user_id = :user_id;";
        $arr = array(':user_id' => $user_id);
        $context = $this->PDOX->rowDie($query, $arr);
        return $context["displayname"];
    }

    function updateQNumber($question_id, $question_num) {
        $query = "UPDATE {$this->p}sq_questions set question_num = :question_num where question_id = :question_id;";
        $arr = array(':question_num' =>$question_num, ':question_id' => $question_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function getQuestionBySetAndNumber($sq_id, $question_num) {
        $query = "SELECT * FROM {$this->p}sq_questions WHERE question_num = :question_num AND sq_id = :sq_id;";
        $arr = array(':question_num' => $question_num, ':sq_id' => $sq_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function countQuestionsForStudent($user_id) {
        $query = "SELECT COUNT(*) as total FROM {$this->p}sq_questions WHERE user_id = :user_id;";
        $arr = array(':user_id' => $user_id);
        return $this->PDOX->rowDie($query, $arr)["total"];
    }

    function getpoints($question_id) {
        $query = "SELECT votes FROM {$this->p}sq_questions where question_id = :question_id;";
        $arr = array(':question_id' => $question_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function updatePoints($question_id, $votes) {
        $query = "UPDATE {$this->p}sq_questions set votes = :votes where question_id = :question_id;";
        $arr = array(':question_id' => $question_id, ':votes' => $votes);
        $this->PDOX->queryDie($query, $arr);
    }

    function getStudentVote($question_id, $user_id) {
        $query = "SELECT vote FROM {$this->p}sq_questions_votes where question_id = :question_id AND user_id = :user_id;";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function createStudentVote($question_id, $user_id, $sq_id, $vote) {
        $query = "INSERT INTO {$this->p}sq_questions_votes (user_id, question_id, sq_id, vote) VALUES (:user_id, :question_id, :sq_id, :vote);";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id, ':sq_id' => $sq_id,':vote' => $vote);
        return $this->PDOX->rowDie($query, $arr);
    }

    function updateStudentVote($question_id, $user_id, $vote) {
        $query = "UPDATE {$this->p}sq_questions_votes set vote = :vote where question_id = :question_id AND user_id = :user_id;";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id, ':vote' => $vote);
        $this->PDOX->queryDie($query, $arr);
    }

    function getStudentAnswerVote($question_id, $user_id, $answer_id) {
        $query = "SELECT vote FROM {$this->p}sq_answer_votes where question_id = :question_id AND user_id = :user_id AND answer_id = :answer_id;";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id, ':answer_id' => $answer_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function createStudentAnswerVote($question_id, $user_id, $sq_id, $vote, $answer_id) {
        $query = "INSERT INTO {$this->p}sq_answer_votes (user_id, question_id, sq_id, vote, answer_id) VALUES (:user_id, :question_id, :sq_id, :vote, :answer_id);";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id, ':sq_id' => $sq_id,':vote' => $vote, ':answer_id' => $answer_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function updateStudentAnswerVotee($question_id, $user_id, $vote, $answer_id) {
        $query = "UPDATE {$this->p}sq_answer_votes set vote = :vote where question_id = :question_id AND user_id = :user_id AND answer_id = :answer_id;";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id, ':vote' => $vote, ':answer_id' => $answer_id);
        $this->PDOX->queryDie($query, $arr);
    }

    function getVerified($question_id) {
        $query = "SELECT correct FROM {$this->p}sq_questions where question_id = :question_id;";
        $arr = array(':question_id' => $question_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function updateVerified($question_id, $correct) {
        $query = "UPDATE {$this->p}sq_questions set correct = :correct where question_id = :question_id;";
        $arr = array(':question_id' => $question_id, ':correct' => $correct);
        $this->PDOX->queryDie($query, $arr);
    }

    function getAnswerVerified($answer_id) {
        $query = "SELECT correct FROM {$this->p}sq_answer where answer_id = :answer_id;";
        $arr = array(':answer_id' => $answer_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function updateAnswerVerified($answer_id, $correct) {
        $query = "UPDATE {$this->p}sq_answer set correct = :correct where answer_id = :answer_id;";
        $arr = array(':answer_id' => $answer_id, ':correct' => $correct);
        $this->PDOX->queryDie($query, $arr);
    }

    function createUnderStood($question_id, $user_id, $sq_id, $understood) {
        $query = "INSERT INTO {$this->p}sq_questions_understood (user_id, question_id, sq_id, understood) VALUES (:user_id, :question_id, :sq_id, :understood);";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id, ':sq_id' => $sq_id,':understood' => $understood);
        return $this->PDOX->rowDie($query, $arr);
    }

    function getUnderStood($question_id, $user_id) {
        $query = "SELECT understood FROM {$this->p}sq_questions_understood where question_id = :question_id AND user_id = :user_id;";
        $arr = array(':question_id' => $question_id, ':user_id' => $user_id);
        return $this->PDOX->rowDie($query, $arr);
    }

    function updateUnderStood($question_id, $understood, $user_id, $sq_id) {
        $query = "UPDATE {$this->p}sq_questions_understood set understood = :understood where question_id = :question_id AND user_id = :user_id AND sq_id =:sq_id;";
        $arr = array(':question_id' => $question_id, ':understood' => $understood, ':user_id' => $user_id, ':sq_id' => $sq_id);
        $this->PDOX->queryDie($query, $arr);
    }

}
