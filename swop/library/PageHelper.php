<?php

class PageHelper
{
    static public function getPageSelect($page,$last_page)
    {
        $pageSelect = array("id"=>"page_select","name"=>"page_select","class"=>"page-select","option"=>array(),"selected"=>$page);
        $i = 0;
        do
        {
            $pageSelect["option"][] = array("value" => $i,"name"  => ++$i);
        }while( $i<$last_page );
        return $pageSelect;
    }
}