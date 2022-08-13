<?php
/**
 * Purchase
 */
   
    
class Logic_Purchase extends Logic_Base {
    public function getDetail($purchase_id) {
        $details = $this->db()->fetchAll(
            "SELECT `dtb_purchase_detail`.*, material_desc as material_name, part_no FROM `dtb_purchase_detail` JOIN dtb_material ON dtb_material.id = dtb_purchase_detail.material_id WHERE `purchase_id` = ?", $purchase_id
        );
        return $details;
    }

    public function getFirstPurchase($order_id) {
        $purchase = $this->db()->fetchRow(
            "SELECT `dtb_purchase`.purchase_no, `dtb_purchase`.purchase_date
            FROM dtb_purchase 
            WHERE `dtb_purchase`.order_id = ?
            ORDER BY dtb_purchase.purchase_date"
            , $order_id
        );

        return $purchase;
    }

    public function getDetailByOrderId($order_id) {
        $details = $this->db()->fetchAll(
            "SELECT `dtb_purchase`.purchase_no, `dtb_purchase`.purchase_date, `dtb_purchase_detail`.*, material_desc as material_name, part_no 
            FROM `dtb_purchase_detail` 
            JOIN dtb_material ON dtb_material.id = dtb_purchase_detail.material_id 
            JOIN dtb_purchase ON dtb_purchase.id = dtb_purchase_detail.purchase_id
            WHERE `dtb_purchase`.order_id = ?
            ORDER BY dtb_purchase.purchase_date, dtb_purchase.purchase_no"
            , $order_id
        );

        return $details;
    }

    public function getAllByOrderId( $order_id ) {
        return $this->db()->fetchAll(
            "SELECT `dtb_purchase`.*, (SELECT (SUM(qty*price))FROM `dtb_purchase_detail` WHERE `purchase_id` = `dtb_purchase`.`id`) as total, (SELECT ((SUM(qty*price)*10/100)) FROM `dtb_purchase_detail` WHERE `purchase_id` = `dtb_purchase`.`id`) as tax, (SELECT (SUM(qty*price)+(SUM(qty*price)*10/100)) FROM `dtb_purchase_detail` WHERE `purchase_id` = `dtb_purchase`.`id`) as total_all FROM `dtb_purchase` WHERE `order_id` = ? ORDER BY `purchase_date` ASC",
            array($order_id)
        );
    }
    
}