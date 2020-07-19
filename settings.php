<?php

/**
 *  Setting Page
 *
 * @package    auth_restrict
 * @copyright  2020 Anggri Y Pernadna (https://github.com/anggriyulio)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;
if ($ADMIN->fulltree) {

    // Needed for constants.
    require_once($CFG->libdir . '/authlib.php');


    $settings->add(new admin_setting_heading('auth_restrict/pluginname', '',
        new lang_string('auth_restrictdescription', 'auth_restrict')));


    $settings->add(new admin_setting_confightmleditor(
        'auth_restrict/message',
        new lang_string('auth_restrictmessage', 'auth_restrict'),
        new lang_string('auth_restrictmessagedescription', 'auth_restrict'),
        ''
    ));


    $settings->add(new admin_setting_configstoredfile(
        'auth_restrict/filename',
        new lang_string('auth_restrictfilename', 'auth_restrict'),
        new lang_string('auth_restrictfiledescription', 'auth_restrict'),
        'authrestrict',
        0,
        ['accepted_types' => ['.csv']]
    ));


}
