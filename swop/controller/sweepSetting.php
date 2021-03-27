<?php

class SweepSetting_Controller extends JController
{
    public function addSweep()
    {

        $model = $this->model;
        if($model->submit){
            if($model->status=="delete")
            {
                $model->deleteAddSweep();
            }
            else
            {
                $model->postAddSweep();
            }
        }
        $model->getAddSweep();

        $this->getConcurrentCallsSelect();

        $this->getInspectModeSelect();

        return parent::render();
    }

    public function addSweepModify()
    {
        $model = $this->model;

        if($model->submit)
        {
            if($model->updateAddSweepDetail())
            {
                parent::redirect("sweepSetting/addSweep");
            }
        }

        $model->getAddSweepDetail();

        $this->getConcurrentCallsSelect();

        $this->getInspectModeSelect();

        return parent::render();
    }

    public function getPhoneTip()
    {
        $cn_phone_rule = $this->base["cn_phone_rule"];
        $tmp = array();
        $result = array();

        $search = $_POST["search"];

        if(!empty($search) && strlen($search)<2)die(json_encode($result));


        //die(join(",",$cn_phone_rule));
        foreach($cn_phone_rule as $number)
        {
            if($number==$search || strpos($number,$search)!==0 ) continue;

            if(count($tmp)==10) break;

            //$value = preg_replace("/^{$search}/", '',$number);
//            $tmp[substr($value,0,1)] = true;
            $tmp[substr($number,strlen($search),1)] = true;
        }

        foreach($tmp as $key=>$val){
            $result[] = $key;
        }
        //die(join(",",$result));
        echo json_encode($result);
    }

    public function getCNPhoneRule(){
        echo json_encode($this->base["cn_phone_rule"]);
    }

    public function getInspectModeSelect()
    {
//        $this->model->inspectModeSelect = array("id"=>"inspectMode","name"=>"inspectMode","class"=>"form-control","option"=>array(),"selected"=>$this->model->data["InspectMode"]);
//
//        $this->model->inspectModeSelect["option"][] = array(
//            "value" => 0,
//            "name"  => "模式0"
//        );
//
//        $this->model->inspectModeSelect["option"][] = array(
//            "value" => 1,
//            "name"  => "模式1"
//        );
    }
}

?>