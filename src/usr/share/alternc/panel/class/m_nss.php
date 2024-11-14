<?php

/**
 * Manage alternc account with nss service
 */
class m_nss
{
    protected $group_file;
    protected $passwd_file;
    protected $shadow_file;
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
        $lines = [];
        while ($db->next_record()) {
            $lines[] = $db->f('login') . ":x:" . $db->f('uid') . ":";
        }

        $this->group_file = implode("\n", $lines);
    }

    protected function define_passwd_file()
    {
        global $db;
        $db->query("SELECT login,uid FROM `membres`");
        $lines = [];
        while ($db->next_record()) {
            $lines[] = $db->f('login') . ":x:" . $db->f('uid') . ":" . $db->f('uid') . "::" . getuserpath($db->f('login')) . ":/bin/false";
        }

        $this->passwd_file = implode("\n", $lines);
    }

    protected function define_shadow_file()
    {
        global $db;
        $db->query("SELECT login FROM `membres`");
        $lines = [];
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
            $fields = [$db->f('login'), '*', '', '', '', '', '', '', ''];
            $lines[] = implode(':', $fields);
        }

        $this->shadow_file = implode("\n", $lines);
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
        $file = "/var/lib/extrausers/group";
        $content = $this->group_file;
        return file_put_contents($file, $content . "\n", LOCK_EX);
    }

    protected function update_passwd_file()
    {
        $file = "/var/lib/extrausers/passwd";
        $content = $this->passwd_file;
        return file_put_contents($file, $content . "\n", LOCK_EX);
    }

    protected function update_shadow_file()
    {
        $file = "/var/lib/extrausers/shadow";
        $content = $this->shadow_file;
        return file_put_contents($file, $content . "\n", LOCK_EX);
    }

    protected function hook_alternc_add_member()
    {
        $this->update_files();
        return true;
    }
}
