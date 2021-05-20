<?php
/**
 * 画像アップロード
 */
   
    
class Logic_Delivery extends Logic_Base {
    public function findByNo($delivery_no) {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_delivery` WHERE `delivery_no` = ?", $delivery_no
        );
    }
    
    public function getAllByProduct($product_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_delivery` WHERE `product_id` = ?", $product_id
        );
    }
    
    public function getDetail($delivery_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_delivery_detail` WHERE `delivery_id` = ?", $delivery_id
        );
    }
    
    public function getQuantity($delivery_id) {
        return $this->db()->fetchOne(
            "SELECT sum(`quantity`) FROM `dtb_delivery_detail` WHERE `delivery_id` = ?", $delivery_id
        );
    }
    
    public function getLatest() {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_delivery` ORDER BY `id` DESC LIMIT 1"
        );
    }
    
    public function getAllByOrderId( $order_id ) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_delivery` WHERE `order_id` = ? ORDER BY `delivery_date` ASC",
            array($order_id)
        );
    }

    public function getQtyDelivByOrderId( $order_id ) {
        return $this->db()->fetchOne(
            "SELECT sum(dd.quantity) FROM `dtb_delivery_detail` dd JOIN `dtb_delivery` d ON dd.delivery_id = d.id WHERE `order_id` = ? ORDER BY `delivery_date` ASC",
            array($order_id)
        );
    }
}