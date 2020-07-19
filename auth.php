<?php

/**
 * auth_restrict plugin
 *
 * @package    auth_restrict
 * @copyright  2020 Anggri Y Pernadna (https://github.com/anggriyulio)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');


class auth_plugin_restrict extends auth_plugin_base
{

    function __construct()
    {
        $this->authtype = 'restrict';
        $this->config = get_config('auth_restrict');
    }

    /**
     * This is the primary method that is used by the authenticate_user_login()
     * function in moodlelib.php.
     *
     * This method should return a boolean indicating
     * whether or not the username and password authenticate successfully.
     *
     * Returns true if the username and password work and false if they are
     * wrong or don't exist.
     *
     * @param string $username The username (with system magic quotes)
     * @param string $password The password (with system magic quotes)
     *
     * @return bool Authentication success or failure.
     */

    function user_login($username, $password)
    {
        // This must be rewritten by plugin to return boolean value, returns true if the username and password work and false if they are wrong or don't exist.
    }


    /**
     * Post authentication hook.
     * This method is called from authenticate_user_login() for all enabled auth plugins.
     *
     * @param object $user user object, later used for $USER
     * @param string $username (with system magic quotes)
     * @param string $password plain text password (with system magic quotes)
     */
    function user_authenticated_hook(&$user, $username, $password)
    {

        if ($this->canLogin($user->username) == NULL) {
            $message = get_config('auth_restrict', 'message');
            redirect('/login/index.php', $message, NULL, \core\output\notification::NOTIFY_ERROR);
            return FALSE;
        }
        return TRUE;

    }


    /**
     * @param $username
     * @return bool|mixed
     */
    function canLogin($username)
    {
        global $CFG, $DB, $OUTPUT;

        $currentTime = time();
        try {

            $query = 'SELECT* FROM ' . $CFG->prefix . 'auth_restrict
                    WHERE username="' . $this->ext_addslashes($username) . '"  AND start_time <= ' . $currentTime . ' AND end_time >= ' . $currentTime . '';

            $rs = $DB->get_records_sql($query);
            if (count($rs)) {
                return TRUE;
            }
            return NULL;

        } catch (Exception $e) {
            return NULL;
        }

    }

    function ext_addslashes($text)
    {
        if (empty($this->config->sybasequoting)) {
            $text = str_replace('\\', '\\\\', $text);
            $text = str_replace(['\'', '"', "\0"], ['\\\'', '\\"', '\\0'], $text);
        } else {
            $text = str_replace("'", "''", $text);
        }
        return $text;
    }

    public function test_settings()
    {

        global $CFG, $DB, $OUTPUT;
        require_once($CFG->libdir . '/csvlib.class.php');

        $setting = get_config('auth_restrict', 'filename');
        if ($setting) {
            $fname = str_replace('/', '', $setting);

            $fs = get_file_storage();
            $fs->get_area_files(1, 'auth_restrict', 'authrestrict', 0);
            $exists = $fs->file_exists(1, 'auth_restrict', 'authrestrict', '0', '/', $fname);
            if ($exists) {
                $file = $fs->get_file(1, 'auth_restrict', 'authrestrict', 0, '/', $fname);
                $content = $file->get_content();
                $uploadid = csv_import_reader::get_new_iid('importauthrestrict');
                $csv = new csv_import_reader($uploadid, 'importauthrestrict');
                $count = $csv->load_csv_content($content, 'UTF-8', 'comma');
                $csv->init();
                $table = new html_table();
                $table->head = array('#', 'Username', 'Start Time', 'End Time');

                if ($count > 0) {
                    $num = 0;
                    $DB->execute('TRUNCATE TABLE ' . $CFG->prefix . 'auth_restrict');

                    while ($row = $csv->next()) {
                        $num++;
                        $table->data[] = array($num, $row[0], $row[1], $row[2]);
                        $data = [
                            'username' => $row[0],
                            'start_time' => strtotime($row[1]),
                            'end_time' => strtotime($row[2]),
                        ];
                        $DB->insert_record('auth_restrict', $data, $returnid = true, $bulk = false);
                    }
                }
            }

            echo html_writer::table($table);
        } else {
            echo $OUTPUT->notification(get_string('notconfig', 'auth_restrict'), 'notifyproblem');

        }
    }
    
}
