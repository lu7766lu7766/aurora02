<?php

class Library_Tidyhtml
{

    public function __construct()
    {
        $this->base = $base;
    }

    public function purifier($dirty_html)
    {
        require_once dirname(__FILE__) . "/HTMLPurifier/HTMLPurifier.auto.php";
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
        $this->tidyhtml_content = $purifier->purify($dirty_html);

        return $this;
    }
}

?>