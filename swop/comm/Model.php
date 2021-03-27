<?php

class Model
{
    public function __construct($base)
    {
        require_once $base['comm_dir']."DBA.php";
        $this->base = $base;
        $this->dba = new comm\DBA;
        $this->dba->connect();
        $this->session = $this->getSession();
    }

    public function __destruct()
    {
        $this->setSession($this->session);
    }

    private function setSession($session)
    {
        $_SESSION[$this->base["folder"]] = $session;
    }

    private function getSession()
    {
        return $_SESSION[$this->base["folder"]];
    }
}

?>