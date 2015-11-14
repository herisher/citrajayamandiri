<?php
/**
 *  ??????
 */
class Manager_SyncController extends ManagerBaseController {
    public function orderAction() {
        $table = $this->model('Dao_Order');
        $models = $table->fetchAll();
        foreach($models as $model) {
            if($model['status'] == 'Progress') $status = 0;
            elseif($model['status'] == 'Finish') $status = 2;
            
            $exp = explode('/', $model['week_kirim']);
            $wk = $exp[1].$exp[0];
            
            $qty = $this->model('Logic_Order')->getQuantity($model['id']);
            $product = $this->model('Logic_Product')->findByNo($model['product_no']);
            
            $table->update(
                array(
                    'product_id'        => $product['id'],
                    'status_flag'       => $status,
                    'delivery_week'     => $wk,
                    'quantity'          => $qty,
                ),
                $table->getAdapter()->quoteInto(
                    'id = ?', $model['id']
                )
            );
        }
        $this->_redirect('/manager/order/list');
    }
    
    public function orderDetailAction() {
        $table = $this->model('Dao_OrderDetail');
        $models = $table->fetchAll();
        foreach($models as $model) {
            $order = $this->model('Logic_Order')->findByNo($model['po_no']);
            $table->update(
                array(
                    'order_id'    => $order['id'],
                ),
                $table->getAdapter()->quoteInto(
                    'id = ?', $model['id']
                )
            );
        }
        $this->_redirect('/manager/order/list');
    }
    
    public function deliveryAction() {
        $table = $this->model('Dao_Delivery');
        $models = $table->fetchAll();
        foreach($models as $model) {
            $order = $this->model('Logic_Order')->findByNo($model['po_no']);
            $qty = $this->model('Logic_Delivery')->getQuantity($model['id']);
            $table->update(
                array(
                    'order_id'      => $order['id'],
                    'product_id'    => $order['product_id'],
                    'quantity'      => $qty,
                ),
                $table->getAdapter()->quoteInto(
                    'id = ?', $model['id']
                )
            );
        }
        $this->_redirect('/manager/delivery/list');
    }
        
    public function deliveryDetailAction() {
        $table = $this->model('Dao_DeliveryDetail');
        $models = $table->fetchAll();
        foreach($models as $model) {
            $delivery = $this->model('Logic_Delivery')->findByNo($model['sj_no']);
            $table->update(
                array(
                    'delivery_id'    => $delivery['id'],
                ),
                $table->getAdapter()->quoteInto(
                    'id = ?', $model['id']
                )
            );
        }
        $this->_redirect('/manager/delivery/list');
    }
    
    public function invoiceAction() {
        $table = $this->model('Dao_Invoice');
        $models = $table->fetchAll();
        foreach($models as $model) {
            $status = $model['status'];
            if( $model['bayar_no'] ) $status = 1;
            
            $order = $this->model('Logic_Order')->findByNo($model['po_no']);
            $delivery = $this->model('Logic_Delivery')->findByNo($model['sj_no']);
            
            $total = $model["quantity"]*$order["price"];
            $total_min_discount = $total - $model['discount'];
            $total_include_tax = ($total-$model["discount"])+$model["ppn"];
            
            $table->update(
                array(
                    'order_id'      => $order['id'],
                    'delivery_id'   => $delivery['id'],
                    'product_id'    => $order['product_id'],
                    'price'         => $order['price'],
                    'status'        => $status,
                    'total'                 => $total,
                    'total_min_discount'    => $total_min_discount,
                    'total_include_tax'     => $total_include_tax,
                ),
                $table->getAdapter()->quoteInto(
                    'id = ?', $model['id']
                )
            );
        }
        $this->_redirect('/manager/invoice/list');
    }
    
    public function addTotalAction() {
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        
        $models = $this->model('Dao_Order')->fetchAll();
        foreach( $models as $model ) {
            $model = $model->toArray();
            $table = $this->model('Dao_Order');
            $sub_total = $model['price']*$model['quantity'];
            $ppn = $sub_total/10;
            $total = $sub_total + $ppn;
            $update = $table->update(array(
                "sub_total" => $sub_total ,
                "ppn"       => $ppn,
                "total"     => $total,
            ), $table->getAdapter()->quoteInto(
                "id = ?", $model['id']
            ));
            
            if($update) {
                echo "OKE <br>";
            }
        }
    }
}
