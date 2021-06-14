<?php
$menu = new \Tsugi\UI\MenuSet();

$menu->setHome('Study Questions', 'index.php');

if ($USER->instructor) {

    $advanced = array(
        new \Tsugi\UI\MenuEntry("Getting Started", "splash.php"),
        new \Tsugi\UI\MenuEntry("Reset Tool", "actions/DeleteAll.php")
    );

    $menu->addRight('<span class="fas fa-tools" aria-hidden="true"></span> Advanced', $advanced);

    $menu->addRight('<span class="fas fa-pen-square" aria-hidden="true"></span> Questions', 'question-home.php');

}