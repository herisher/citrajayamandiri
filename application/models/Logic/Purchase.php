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

    public function getAllByOrderId( $order_id ) {
        return $this->db()->fetchAll(
            "SELECT `dtb_purchase`.*, (SELECT (SUM(qty*price)+(SUM(qty*price)*10/100)) as total FROM `dtb_purchase_detail` WHERE `purchase_id` = `dtb_purchase`.`id`) as total FROM `dtb_purchase` WHERE `order_id` = ? ORDER BY `purchase_date` ASC",
            array($order_id)
        );
    }
    
}