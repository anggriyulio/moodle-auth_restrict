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

    $settings->add(new admin_setting_heading('auth_restrict/pluginname', '', new lang_string('auth_restrictdescription', 'auth_restrict')));
    $settings->add(new admin_setting_confightmleditor(
        'auth_restrict/message',
        'Message',
        'Message to user when login is restricted',
        ''
    ));


    $settings->add(new admin_setting_configstoredfile(
        'auth_restrict/filename',
        'Restriction File',
        'A csv file. Download CSV template here (<a href="http://docs.moodle.org/en/NTLM_authentication">http://docs.moodle.org/en/NTLM_authentication</a>)',
        'authrestrict',
        0,
        ['accepted_types' => ['.csv']]
    ));


}
