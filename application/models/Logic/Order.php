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
        $status = (isset($param->post['status_flag']) && ($param->post['status_flag']!='')) ? $param->post['status_flag'] : 0;
        $order = (isset($param->post['order_by']) && ($param->post['order_by']!='')) ? $param->post['order_by'].', ' : "";

        $query = 
            "SELECT `o`.*, 
                    article, 
                    project,
                    (SELECT purchase_date FROM dtb_purchase WHERE order_id = o.id ORDER BY purchase_date LIMIT 1) AS first_purchase_date ".
            "FROM `dtb_order` `o`, `dtb_product` `p` ".
            "WHERE `o`.`product_id` = `p`.`id` AND `o`.`status_flag` = " . $status . " " .
            "AND SUBSTRING(delivery_week, 1, 4) = " . $year . " " .
            // "AND `o`.`order_no` IN (126479, 126989, 128095, 127520, 128272, 128193, 127423, 128268, 127458)".
            "ORDER BY " . $order . " o.`delivery_week`, `o`.`plan`, `o`.`order_no`";

        $models = $this->db()->fetchAll($query);
        
        foreach($models as &$model) {
            $delivery = $this->model('Logic_Delivery')->getAllByOrderId($model['id']);
            $purchase = $this->model('Logic_Purchase')->getFirstPurchase($model['id']);
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
            if( $purchase ) {
                $model['first_purchase_date'] = $purchase['purchase_date'];
            }
        }
        return $models;
    }

    public function getOutstandingByOrder($param) {
        if( !is_array($param) && !count($param) ) { return false; }

        $order_list = join(',', $param);
        $query = 
            "SELECT `o`.*, article, project ".
            "FROM `dtb_order` `o`, `dtb_product` `p` ".
            "WHERE `o`.`product_id` = `p`.`id` " .
            "AND `o`.`id` IN (" . $order_list . ")".
            "ORDER BY o.`delivery_week`, `o`.`plan`, `o`.`order_no`";

        $models = $this->db()->fetchAll($query);
        
        foreach($models as &$model) {
            $delivery = $this->model('Logic_Delivery')->getAllByOrderId($model['id']);
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
        }
        return $models;
    }
}