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

    public function getDetailForm($receipt_id) {
        $model = $this->db()->fetchAll(
            "SELECT * FROM `dtb_invoice` WHERE `id` IN (SELECT invoice_id FROM dtb_receipt_detail WHERE `receipt_id` = ?) ORDER BY `id`", $receipt_id
        );
        return $model;
    }
    
    public function getLatest() {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_receipt` ORDER BY `id` DESC LIMIT 1"
        );
    }
}