<?php
/**
 * 栄養士
 */
class Manager_InvoicePopupController extends BaseController {
    const NAMESPACE_LIST = "/manager/invoice-popup/list";

    private $step_keys = array(
        'session' => array(
            'invoice_date',
            'due_date',
            'quantity',
            'price',
            'discount',
            'total',
            'tax_percentage',
            'tax',
            'total_include_tax',
        ),
    );
    
    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Delivery');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        
        // セッションから検索条件を復元する
        $where = array(); 
        if ($session->post) {//print_r($session->post);
            $id_list = array();
            foreach ((array)$session->post as $key => $value) {//echo $key.'-'.$value;
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key == 'status' && $value != null ) {
                    $where['status = ?'] = $value;
                }
                if ( $key === 'delivery_no' && $value != null ) {
                    $where['delivery_no like ?'] = '%'.$value.'%';
                }
                if ( $key === 'order_no' && $value != null ) {
                    $where['order_id IN (SELECT id FROM dtb_order WHERE order_no like ?)'] = '%'.$value.'%';
                }
                if ( $key === 'article' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE article like ?)'] = '%'.$value.'%';
                }
                if ( $key === 'project' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE project like ?)'] = '%'.$value.'%';
                }
            }
        }
        
        return $table->createWherePhrase($where, $order_by);
    }

    /**
     * 検索条件復元
     */
    private function restoreSearchForm($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        if ($session->post) {
            $form->setDefaults($session->post);
        } else {
            $session->post['status'] = '0';
            $form->setDefault('status','0');
        }
    }

    /**
     * 一覧
     */
    public function listAction() {
        $this->_helper->layout->disableLayout();
        
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        
        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('status')->setMultiOptions(array('' => '▼Choose') + Dao_Delivery::$statics['status']);
          
        // 検索・クリア
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('clear') ) {
                // クリア
                Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
            } elseif ( $this->getRequest()->getParam('search') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/invoice-popup/list');
            } elseif ( $this->getRequest()->getParam('select') ) {
                $delivery_session = new Zend_Session_Namespace('/manager/invoice/list');
                $delivery = $this->model('Dao_Delivery')->retrieve($this->getRequest()->getParam('select'));
                $delivery = $delivery->toArray();
                $order = $this->model('Dao_Order')->retrieve($delivery['order_id']);
                $product = $this->model('Dao_Product')->retrieve($delivery['product_id']);
                $products = $product->toArray();
                $tax_percentage = $order['ppn_percentage'];
                
                $delivery_session->delivery_list = array();
                $delivery_session->delivery_list = $delivery;
                $delivery_session->delivery_list['order_no'] = $order['order_no'];
                $delivery_session->delivery_list['price'] = $order['price'];
                $delivery_session->delivery_list['invoice_date'] = $delivery['delivery_date'];
                $delivery_session->delivery_list['due_date'] = $this->model('Logic_Invoice')->generateNumber($delivery['delivery_date']);
                
                $delivery_session->delivery_list['total'] = $delivery["quantity"]*$order["price"];
                $delivery_session->delivery_list['discount'] = 0;
                $delivery_session->delivery_list['total_min_discount'] = $delivery_session->delivery_list['total'];
                $delivery_session->delivery_list['tax_percentage'] = $tax_percentage;
                $delivery_session->delivery_list['tax'] = floor($delivery_session->delivery_list['total']*($tax_percentage/100));
                
                $delivery_session->delivery_list['total_include_tax'] = intval($delivery_session->delivery_list['total']+$delivery_session->delivery_list['tax']);
                foreach($products as $key => $value) {
                    if($key != 'id') {
                        $delivery_session->delivery_list[$key] = $value;
                    }
                }
                
                Zend_Session::namespaceUnset( self::NAMESPACE_LIST );
                $this->view->need_close = true;
            }
        } else {
            // 検索条件復元
            $this->restoreSearchForm($form);
        }
        
        //echo $this->createWherePhrase()->__toString();
        // 一覧取得
        $this->createNavigator(
            $this->createWherePhrase()
        );
        
        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $order = $this->model('Dao_Order')->retrieve($model['order_id']);
            $product = $this->model('Dao_Product')->retrieve($model['product_id']);
            $products = $product->toArray();
            foreach($products as $key => $value) {
                if($key != 'id') {
                    $model[$key] = $value;
                }
            }
            $model['order_no'] = $order['order_no'];
            $model['disp_status'] = Dao_Delivery::$statics['status'][$model['status']];
            array_push($models, $model);
        }
        $this->view->models = $models;
    }
}
