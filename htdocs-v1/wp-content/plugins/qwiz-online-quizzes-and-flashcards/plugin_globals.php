<?php
// Not included in svn repository -- distinguish development environment
// (qwizcards server on localhost) from production version (server on
// qwizcards.com).
$debug = array ();
$debug[0] = false;    // General.
$debug[1] = false;    // Add wrapper divs.
$debug[2] = false;    // Check pairs.
$debug[3] = false;    // Update textentry_suggestions db table.
$debug[4] = false;    // Provide textentry suggestions.
$debug[5] = false;    // Dataset updates, retrieval.
$debug[6] = false;    // Dataset question-parsing details.
$debug[7] = false;    // [simply-random].

$server_loc        = 'http://qwizcards.com/admin';
$secure_server_loc = 'https://qwizcards.com/admin';
//$server_loc        = '//localhost/admin';
//$secure_server_loc = '//localhost/admin';

