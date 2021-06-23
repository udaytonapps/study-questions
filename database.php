<?php

// The SQL to uninstall this tool
$DATABASE_UNINSTALL = array(
    // Nothing
);

// The SQL to create the tables if they don't exist
$DATABASE_INSTALL = array(
    array( "{$CFG->dbprefix}sq_main",
        "create table {$CFG->dbprefix}sq_main (
    sq_id       INTEGER NOT NULL AUTO_INCREMENT,
    user_id     INTEGER NOT NULL,
    context_id  INTEGER NOT NULL,
	link_id     INTEGER NOT NULL,
    modified    datetime NULL,

    PRIMARY KEY(sq_id)

) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
    array( "{$CFG->dbprefix}sq_questions",
        "create table {$CFG->dbprefix}sq_questions (
    question_id   INTEGER NOT NULL AUTO_INCREMENT,
    sq_id         INTEGER NOT NULL,
    question_num  INTEGER NULL,
    question_txt  TEXT NULL, 
    answer_txt    TEXT NULL,    
    author        TEXT NULL,
    user_id       TEXT NULL,
    votes         INTEGER NOT NULL DEFAULT 0,
    modified      datetime NULL,
    correct       BOOL NOT NULL DEFAULT 0,

    CONSTRAINT `{$CFG->dbprefix}sq_ibfk_1`
        FOREIGN KEY (`sq_id`)
        REFERENCES `{$CFG->dbprefix}sq_main` (`sq_id`)
        ON DELETE CASCADE,

    PRIMARY KEY(question_id)

) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
    array( "{$CFG->dbprefix}sq_questions_votes",
        "create table {$CFG->dbprefix}sq_questions_votes (
    vote_id       INTEGER NOT NULL AUTO_INCREMENT,
    question_id   INTEGER NOT NULL,
    sq_id         INTEGER NOT NULL,
    user_id       TEXT NULL NOT NULL,
    vote          TEXT NULL,

    CONSTRAINT `{$CFG->dbprefix}sq_ibfk_3`
        FOREIGN KEY (`question_id`)
        REFERENCES `{$CFG->dbprefix}sq_questions` (`question_id`)
        ON DELETE CASCADE,

    PRIMARY KEY(vote_id)

) ENGINE = InnoDB DEFAULT CHARSET=utf8"),
    array( "{$CFG->dbprefix}sq_answer",
        "create table {$CFG->dbprefix}sq_answer (
    answer_id    INTEGER NOT NULL AUTO_INCREMENT,
    user_id      INTEGER NOT NULL,
    sq_id        INTEGER NOT NULL,
    question_id  INTEGER NOT NULL,
    votes        INTEGER NOT NULL DEFAULT 0,
	answer_txt   TEXT NULL,
	author       TEXT NULL,
    modified     datetime NULL,
    correct      BOOL NOT NULL DEFAULT 0,

    CONSTRAINT `{$CFG->dbprefix}sq_ibfk_2`
        FOREIGN KEY (`question_id`)
        REFERENCES `{$CFG->dbprefix}sq_questions` (`question_id`)
        ON DELETE CASCADE,

    PRIMARY KEY(answer_id)

) ENGINE = InnoDB DEFAULT CHARSET=utf8")
);