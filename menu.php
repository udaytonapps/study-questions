<?php
$menu = new \Tsugi\UI\MenuSet();

$menu->setHome('Study Questions', 'index.php');

if ($USER->instructor) {

    $menu->addRight('<span class="fas fa-user-graduate" aria-hidden="true"></span> Contributions', 'contributions.php');

    $menu->addRight('<span class="fas fa-pen-square" aria-hidden="true"></span> Questions', 'question-home.php');
}