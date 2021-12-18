<?php
/**
 * Order
 */
   
    
class Logic_Order extends Logic_Base {
    public function findByNo($po_no) {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_order` WHERE `order_no` = ?", $po_no
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
    
    public function getOutstandingByYear($param) {
        $year = (isset($param->post['year']) && ($param->post['year']!='')) ? $param->post['year'] : date('Y');
        $order = (isset($param->post['order_by']) && ($param->post['order_by']!='')) ? $param->post['order_by'].', ' : "";

        $query = 
            "SELECT `o`.*, article, project ".
            "FROM `dtb_order` `o`, `dtb_product` `p` ".
            "WHERE `o`.`product_id` = `p`.`id` AND `o`.`status_flag` IN (0) ".
            "AND SUBSTRING(delivery_week, 1, 4) = " . $year . " " .
            // "AND `o`.`order_no` IN (126479, 126989, 128095, 127520, 128272, 128193, 127423, 128268, 127458)".
            "ORDER BY " . $order . " o.`delivery_week`, `o`.`plan`, `o`.`order_no`";

        $models = $this->db()->fetchAll($query);
        
        foreach($models as &$model) {
            $delivery = $this->model('Logic_Delivery')->getAllByOrderId($model['id']);
            $purchase = $this->model('Logic_Purchase')->getDetailByOrderId($model['id']);
            $total = 0;
            $assortment = array();

            if( count($delivery) ) {
                foreach( $delivery as $deliver ){
                    $total += $deliver['quantity'];

                    $detail = $this->model('Logic_Delivery')->getDetail($deliver['id']);
                    foreach( $detail as $det ) {
                        $assortment[$det['size']][] = (int)$det['quantity'];
                    }
                }

                foreach( $assortment as $k => $val ) {
                    $assortment[$k] = array_sum($val);
                }
            }

            // get material

            $model['delivery'] = $total;
            $model['outstanding'] = $model['quantity'] - $total;
            $model['order_detail'] = $this->getDetail($model['id']);
            $model['delivery_detail'] = $assortment;
            $model['purchase'] = $purchase;
        }
        return $models;
    }
}