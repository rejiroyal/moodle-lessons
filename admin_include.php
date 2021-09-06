<?php

require('../config.php');

defined('MOODLE_INTERNAL') || die();

global $USER, $COURSE, $DB, $CFG;

//require_once($CFG->dirroot . '/local/pages/lib.php');

// Setup the page.
//$PAGE->set_context(\context_system::instance());
//$PAGE->set_url("{$CFG->wwwroot}/local_pages/index.php", ['id' => $pageid]);

//custom auth
require_login();
$roleid = $DB->get_field('role', 'id', ['shortname' => 'editingteacher']);
$isteacheranywhere = $DB->record_exists('role_assignments', ['userid' => $USER->id, 'roleid' => $roleid]);
if(!(is_siteadmin() || $isteacheranywhere) ){
    redirect($CFG->wwwroot);
}

$db_prefix = $CFG->prefix;
