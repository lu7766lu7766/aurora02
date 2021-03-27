<?php

class GroupCallSetting_Controller extends JController
{
    public function groupCallSchedule()
    {

        $model = $this->model;
        if($model->submit){
            if($model->status=="delete")
            {
                $model->deleteGroupCallSchedule();
            }
            else
            {
                $model->postGroupCallSchedule();
            }
        }
        $model->getGroupCallSchedule();

        $this->getConcurrentCallsSelect();

        return parent::render();
    }

    public function groupCallScheduleModify()
    {
        $model = $this->model;

        if($model->submit)
        {
            if($model->updateGroupCallScheduleDetail())
            {
                parent::redirect("groupCallSetting/groupCallSchedule");
            }
        }

        $model->getGroupCallScheduleDetail();

        $this->getConcurrentCallsSelect();

        $model->callerPresentSelect = array("id"=>"callerPresent","selected"=>$model->data["CallerPresent"]);
        $model->callerPresentSelect["option"][] = array("value"=>"0","name"=>"不顯示");
        $model->callerPresentSelect["option"][] = array("value"=>"1","name"=>"顯示");

        $model->calloutGroupIDSelect = array("id"=>"calloutGroupID","selected"=>$model->data["CalloutGroupID"]);
        for($i=0;$i<5;$i++)
        {
            $model->calloutGroupIDSelect["option"][] = array("value"=>$i,"name"=>$i);
        }

        $model->calldistributionSelect = array("id"=>"calldistribution","selected"=>$model->data["Calldistribution"]);
        $model->calldistributionSelect["option"][] = array("value"=>"0","name"=>"分號少的開始配號");
        $model->calldistributionSelect["option"][] = array("value"=>"1","name"=>"自動平均分配");

        return parent::render();
    }

    public function effectiveNumber(){
        return parent::render("effectiveNumber2");
    }

    public function getEffectiveNumber(){}

    public function getEffectiveNumber2(){}

    public function addDefaultSchedule(){
        $this->model->postDefaultSchedule($this->model->list);
    }
}

?>