<?php

/**
 * 分頁物件
 */
class Pager
{

    private $return = array();
    private $start_page;
    private $end_page;
    private $page_rows;
    private $rows_per_page;

    /**
     * 建構式
     * @param array $origin_arr 資料陣列
     * @param int $start_page 起始頁數
     * @param int $rows_per_page 每頁幾筆資料
     */
    public function __construct($origin_arr)
    {
        $start_page = isset($_GET["page"]) ? $_GET["page"] : 1;
        $rows_per_page = isset($_GET["rpp"]) ? $_GET["rpp"] : 20;
        if ($start_page < 1 || $rows_per_page < 1) return array();
        $len = count($origin_arr);
        $start_index = ($start_page - 1) * $rows_per_page;
        $end_index = ($start_page - 1) * $rows_per_page + $rows_per_page - 1;
        if ($start_index > $len - 1) $start_index = $len - $rows_per_page < 0 ? 0 : floor($len / $rows_per_page) * $rows_per_page - 1;
        if ($end_index > $len - 1) $end_index = $len - 1;
        $i = 0;
        foreach ($origin_arr as $value)
        {
            if ($i < $start_index)
            {
                $i++;
                continue;
            }
            if ($i > $end_index) break;
            foreach ($value as $k => $v) $return[$i][$k] = $v;
            $i++;
        }
        $this->page_rows = $end_index - $start_index + 1;
        $this->start_page = ceil($start_index / $rows_per_page) + 1;
        $this->end_page = ceil($len / $rows_per_page);
        $this->rows_per_page = $rows_per_page;
        $this->return = array_values($return);
    }

    /**
     * 取得過濾後陣列
     * @return array   過濾後陣列
     */
    public function get_filter_array()
    {
        return $this->return;
    }

    /**
     * 取得目前頁碼
     * @return int   目前頁碼
     */
    public function get_page_number()
    {
        return $this->start_page;
    }

    /**
     * 取得結束頁碼
     * @return int   結束頁碼
     */
    public function get_final_page_number()
    {
        return $this->end_page;
    }

    /**
     * 取得目前頁面資料筆數
     * @return int   目前頁面資料筆數
     */
    public function get_page_rows()
    {
        return $this->page_rows;
    }

    /**
     * 取得每頁資料筆數
     * @return int   每頁資料筆數
     */
    public function get_rows_per_page()
    {
        return $this->rows_per_page;
    }

    /**
     * 顯示分頁區塊
     */
    public function display()
    {
        $display = "<div id='pager' style='text-align:center;'>";
        $display .= "<a href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?page=1&rpp=" . $this->get_rows_per_page() . "\"'><<</a>&nbsp;";
        $display .= "<a href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?page=" . ($this->get_page_number() - 1 < 1 ? 1 : $this->get_page_number() - 1) . "&rpp=" . $this->get_rows_per_page() . "\"'><</a>&nbsp;";
        $display .= "<span><input name='show_rows' type='button' value='顯示:' onclick='var p=$(this).parent().find(\"input[name=start_page]\").val(),r=$(this).parent().find(\"input[name=show_rows_num]\").val();location.href=location.href.split(\"?\")[0]+\"?page=\"+p+\"&rpp=\"+r'><input name='show_rows_num' type='text' value='' style='width:40px;'>&nbsp;筆記錄，開始頁數:&nbsp;<input name='start_page' type='text' value='' style='width:40px;'>&nbsp;</span>";
        $display .= "<a href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?page=" . ($this->get_page_number() + 1 > $this->get_final_page_number() ? $this->get_final_page_number() : $this->get_page_number() + 1) . "&rpp=" . $this->get_rows_per_page() . "\"'>></a>&nbsp;";
        $display .= "<a href='javascript:void(0);location.href=location.href.split(\"?\")[0]+\"?page=" . $this->get_final_page_number() . "&rpp=" . $this->get_rows_per_page() . "\"'>>></a>";
        $display .= "</div>";
        $display .= "<script>$(function(){\$(\"#pager\").find(\"input[name=start_page]\").val(\"" . $this->get_page_number() . "\");\$(\"#pager\").find(\"input[name=show_rows_num]\").val(\"" . $this->get_rows_per_page() . "\");\$(\"#pager a\").css({\"text-decoration\":\"none\",\"color\":\"#000\"});})</script>";
        echo $display;
    }
}

