<?php
/**
 * Receipt
 */
   
class Logic_Receipt extends Logic_Base {
    public function getDetail($receipt_id) {
        $model = $this->db()->fetchAll(
            "SELECT * FROM `dtb_receipt_detail` WHERE `receipt_id` = ?", $receipt_id
        );
        return $model;
    }
}