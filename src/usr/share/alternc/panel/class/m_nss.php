<?php

/**
 * Manage alternc account with nss service
 */
class m_nss
{
    protected $group_file;
    protected $passwd_file;

    public function define_files()
    {
        $this->define_group_file();
        $this->define_passwd_file();
    }

    protected function define_group_file()
    {
        global $db;
        $db->query("SELECT login,uid FROM `membres`");
        $lines=array();
        $lines[]='##ALTERNC ACCOUNTS START##';
        while ($db->next_record()) {
            $lines[] = $db->f('login').":x:".$db->f('uid').":";
        }
        $lines[]='##ALTERNC ACCOUNTS END##';

        $this->group_file = implode("\n", $lines);
    }

    protected function define_passwd_file()
    {
        global $db;
        $db->query("SELECT login,uid FROM `membres`");
        $lines=array();
        $lines[]='##ALTERNC ACCOUNTS START##';
        while ($db->next_record()) {
            $lines[] = $db->f('login').":x:".$db->f('uid').":".$db->f('uid').":::/bin/false";
        }
        $lines[]='##ALTERNC ACCOUNTS END##';

        $this->passwd_file = implode("\n", $lines);
    }

    public function write_files()
    {
        $this->define_files();
        $this->write_group_file();
        $this->write_passwd_file();
    }


    protected function write_group_file()
    {
        $file = "/var/lib/extrausers/group";
        $content = file_get_contents($file);
        $content = preg_replace('/##ALTERNC ACCOUNTS START##.*##ALTERNC ACCOUNTS END##/ms', '', $content);
        $content .= $this->group_file;
        return file_put_contents($file, $content, LOCK_EX);
    }

    protected function write_passwd_file()
    {
        $file = "/var/lib/extrausers/passwd";
        $content = file_get_contents($file);
        $content = preg_replace('/##ALTERNC ACCOUNTS START##.*##ALTERNC ACCOUNTS END##/ms', '', $content);
        $content .= $this->passwd_file;
        return file_put_contents($file, $content, LOCK_EX);
    }
}
