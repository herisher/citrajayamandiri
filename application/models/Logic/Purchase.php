<?php
/**
 * Purchase
 */
   
    
class Logic_Purchase extends Logic_Base {
    public function getDetail($order_id) {
        $details = $this->db()->fetchAll(
            "SELECT `dtb_purchase_detail`.*, material_desc as material_name, part_no FROM `dtb_purchase_detail` JOIN dtb_material ON dtb_material.id = dtb_purchase_detail.material_id WHERE `purchase_id` = ?", $order_id
        );
        return $details;
    }
}