<?php
/**
 * Wages
 */
   
    
class Logic_Wages extends Logic_Base {
    public function getDetail($order_id) {
        $details = $this->db()->fetchAll(
            "SELECT `dtb_wages_detail`.*, dtb_type_work.name as name FROM `dtb_wages_detail` JOIN dtb_type_work ON dtb_type_work.id = dtb_wages_detail.work_id WHERE `wages_id` = ?", $order_id
        );
        return $details;
    }

    public function getAllByOrderId( $order_id ) {
        return $this->db()->fetchAll(
            "SELECT wd.*,(wd.qty*wd.price) as total_price, tw.name FROM dtb_wages_detail wd JOIN dtb_wages w ON wd.wages_id = w.id JOIN dtb_type_work tw ON tw.id = wd.work_id WHERE w.order_id = ?",
            array($order_id)
        );
    }
    
}