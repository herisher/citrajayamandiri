<?php
/**
 * Material
 */
   
    
class Logic_Material extends Logic_Base {
    public function findByNo($part_no) {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_material` WHERE `part_no` = ?", $part_no
        );
    }
    
    public function getAllByProduct($product_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_order` WHERE `product_id` = ?", $product_id
        );
    }
    
    public function getDetail($order_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_order_detail` WHERE `order_id` = ?", $order_id
        );
    }
    
    public function getQuantity($order_id) {
        return $this->db()->fetchOne(
            "SELECT sum(`quantity`) FROM `dtb_order_detail` WHERE `order_id` = ?", $order_id
        );
    }
    
    public function getOutstanding() {
        $order = $this->db()->fetchAll(
            "SELECT `o`.*, article, project ".
            "FROM `dtb_order` `o`, `dtb_product` `p` ".
            "WHERE `o`.`product_id` = `p`.`id` AND `o`.`status_flag` IN (0,1) ".
            "ORDER BY o.`delivery_week`, `o`.`plan`, `o`.`order_no`"
        );
        
        foreach($order as &$model) {
            $delivery = $this->model('Logic_Delivery')->getAllByOrderId($model['id']);
            $total = 0;
            foreach( $delivery as $deliver ){
                $total += $deliver['quantity'];
            }
            $model['delivery'] = $total;
            $model['outstanding'] = $model['quantity'] - $total;
        }
        return $order;
    }
}