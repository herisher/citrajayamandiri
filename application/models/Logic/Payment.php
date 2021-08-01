<?php
/**
 * Payment
 */
   
class Logic_Payment extends Logic_Base {
    public function getDetail($payment_id) {
        $model = $this->db()->fetchAll(
            "SELECT * FROM `dtb_payment_invoice` WHERE `payment_id` = ?", $payment_id
        );
        return $model;
    }

    public function getDetailForm($payment_id) {
        $model = $this->db()->fetchAll(
            "SELECT * FROM `dtb_invoice` WHERE `id` IN (SELECT invoice_id FROM dtb_payment_invoice WHERE `payment_id` = ?) ORDER BY `id`", $payment_id
        );
        return $model;
    }
    
    public function getLatest() {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_payment` ORDER BY `id` DESC LIMIT 1"
        );
    }
}