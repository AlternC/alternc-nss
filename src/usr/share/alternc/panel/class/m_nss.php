<?php

/**
 * Manage alternc account with nss service
 */
class m_nss
{
    protected $group = array();
    protected $passwd = array();
    protected $shadow = array();

    protected $dir_backup = "/var/lib/alternc/backups/";
    protected $dir_extrausers = "/var/lib/extrausers/";

    /** Hook function called when a user is created
     * This function add acccount to nss file
     * globals $cuid is the appropriate user
     *
     * @return void
     */
    public function hook_admin_add_member()
    {
        global $msg;
        $msg->log("nss", "hook_admin_add_member");
        $this->update_files();
    }

    public function define_files()
    {
        $this->define_group_file();
        $this->define_passwd_file();
        $this->define_shadow_file();
    }

    protected function define_group_file()
    {
        global $db;
        $db->query("SELECT login,uid FROM `membres`");
        $lines = array();
        while ($db->next_record()) {
            $lines[] = $db->f('login') . ":x:" . $db->f('uid') . ":";
        }

        $this->group = $lines;
    }

    protected function define_passwd_file()
    {
        global $db;
        $db->query("SELECT login,uid FROM `membres`");
        $lines = array();
        while ($db->next_record()) {
            $lines[] = $db->f('login') . ":x:" . $db->f('uid') . ":" . $db->f('uid') . "::" . getuserpath($db->f('login')) . ":/bin/false";
        }

        $this->passwd = $lines;
    }

    protected function define_shadow_file()
    {
        global $db;
        $db->query("SELECT login FROM `membres`");
        $lines = array();
        while ($db->next_record()) {
            // shadow fields (9) :
            // 1. login
            // 2. encrypted password or * to prevent login
            // 3. date of last password change or '' meaning that password aging features are disabled
            // 4. minimum password age or '' or 0 meaning no minimum age
            // 5. maximum password age or '' meaning no maximum password age, no password warning period, and no password inactivity period
            // 6. password warning period or '' or 0 meaning there are no password warning period
            // 7. password inactivity period or '' for no enforcement
            // 8. account expiration date or '' for no expiration
            // 9. reserved
            $fields = array($db->f('login'), '*', '', '', '', '', '', '', '');
            $lines[] = implode(':', $fields);
        }

        $this->shadow = $lines;
    }

    protected function write_content($file, $file_bck, $content_new)
    {
        $content_lines = false;
        if (file_exists($file)) {
            $content_lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        }

        if (!$content_lines) {
            $content_lines = [];
        }
        if (file_exists($file_bck)) {
            $content_lines_bck = file($file_bck, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $content_lines = array_diff($content_lines, $content_lines_bck);
        }
        $content_lines = array_merge($content_lines, $content_new);
        $content = implode("\n", $content_lines);
        $content_bck = implode("\n", $content_new);

        //Provide a final return carrier
        $content .= "\n";

        return $this->write_file($file_bck, $content_bck) && $this->write_file($file, $content);
    }

    public function update_files()
    {
        $this->define_files();
        $this->update_group_file();
        $this->update_passwd_file();
        $this->update_shadow_file();
    }

    protected function update_group_file()
    {
        $file = $this->dir_extrausers . "group";
        $file_bck = $this->dir_backup . "group";

        return $this->write_content($file, $file_bck, $this->group);
    }

    protected function update_passwd_file()
    {
        $file = $this->dir_extrausers . "passwd";
        $file_bck = $this->dir_backup . "passwd";

        return $this->write_content($file, $file_bck, $this->passwd);
    }

    protected function update_shadow_file()
    {
        $file = $this->dir_extrausers . "shadow";
        $file_bck = $this->dir_backup . "shadow";

        return $this->write_content($file, $file_bck, $this->shadow);
    }

    protected function write_file($file, $content, $separator = "\n")
    {
        if (is_array($content)) {
            $content = implode($separator, $content);
        }
        return file_put_contents($file, $content, LOCK_EX);
    }

    protected function hook_alternc_add_member()
    {
        $this->update_files();
        return true;
    }
}
