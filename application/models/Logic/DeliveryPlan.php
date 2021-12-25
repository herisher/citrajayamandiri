<?php
/**
 * 画像アップロード
 */
   
    
class Logic_DeliveryPlan extends Logic_Base {
    public function getQuantity($delivery_plan_id) {
        return $this->db()->fetchOne(
            "SELECT sum(`quantity_outstanding`) FROM `dtb_delivery_plan_detail` WHERE `delivery_plan_id` = ?", $delivery_plan_id
        );
    }
    
    public function getDetail($delivery_plan_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_delivery_plan_detail` WHERE `delivery_plan_id` = ?", $delivery_plan_id
        );
    }
}