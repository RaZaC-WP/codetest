<?php
require_once('initTsugi.php');

// Start of the output
$OUTPUT->header();

$OUTPUT->bodyStart();

$OUTPUT->topNav();

if ($USER->instructor) {
    $OUTPUT->splashPage(
        "Code Test",
        __("Add questions to quickly collect<br />feedback from your students."),
        "actions/MarkSeenGoToHome.php"
    );
} else {
    $OUTPUT->splashPage(
        "Code Test",
        __("Your instructor has not yet configured this learning app.")
    );
}

$OUTPUT->footerStart();

$OUTPUT->footerEnd();
