<?php
/**
 *  カテゴリ管理
 */
class Manager_PaymentPredictionController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/payment-prediction/index';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Invoice');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'id_start' && $value != null ) {
                    $where['id >= ?'] = $value;
                }
                if ( $key === 'id_end' && $value != null ) {
                    $where['id <= ?'] = $value;
                }
                if ( $key === 'invoice_no' && $value != null ) {
                    $where['invoice_no LIKE ?'] = '%'.$value.'%';
                }
                if ( $key === 'delivery_no' && $value != null ) {
                    $where['delivery_id IN (SELECT id FROM dtb_delivery WHERE delivery_no LIKE ?)'] = '%'.$value.'%';
                }
                if ( $key === 'order_no' && $value != null ) {
                    $where['order_id IN (SELECT id FROM dtb_order WHERE order_no = ?)'] = $value;
                }
                if ( $key === 'article' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE article LIKE ?)'] = '%'.$value.'%';
                }
                if ( $key === 'project' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE project LIKE ?)'] = '%'.$value.'%';
                }
                if ( $key === 'status' && $value != null ) {
                    $where['status = ?'] = $value;
                }
                if ( $key === 'payment_status' && $value != null ) {
                    $where['payment_status = ?'] = $value;
                }
                if ( $key === 'invoice_date_start' && $value != null ) {
                    $where['date(invoice_date) >= ?'] = $value;
                }
                if ( $key === 'invoice_date_end' && $value != null ) {
                    $where['date(invoice_date) <= ?'] = $value;
                }
                if ( $key === 'due_date_start' && $value != null ) {
                    $where['date(due_date) >= ?'] = $value;
                }
                if ( $key === 'due_date_end' && $value != null ) {
                    $where['date(due_date) <= ?'] = $value;
                }
                if ( $key === 'order_by' && $value != null ) {
                    $order_by = $value;
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
            $session->post['invoice_date_start'] = date("Y-01-01");
            $session->post['invoice_date_end'] = date("Y-12-31");
            $form->setDefaults($session->post);
        }
    }

    /**
     *  カテゴリ一覧
     */
    public function indexAction() {
        // 整列
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('status')->setMultiOptions(array('' => '▼Choose') + Dao_Invoice::$statics['status']);
        $form->getElement('payment_status')->setMultiOptions(array('' => '▼Choose') + Dao_Invoice::$statics['payment_status']);
        $form->getElement('order_by')->setMultiOptions(array('' => '▼Choose') + Dao_Invoice::$statics['order_by']);
        
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
                $this->_redirect(self::NAMESPACE_LIST);
            } else {
                // 検索条件復元
                $this->restoreSearchForm($form);
            }
        } else {
            // 検索条件復元
            $this->restoreSearchForm($form);
        }
        
        //print_r($this->createWherePhrase()->__toString());
        $mod = $this->db()->fetchAll(
            $this->createWherePhrase()
        );

        // 表示用カスタマイズ
        $models = array();
        $total = 0;
        $total_with_tax = 0;
        $order_arr = array();
        foreach ($mod as $model) {
            //$model = $model->toArray();
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);

            $model['purchase'] = array();
            if( !in_array($model['order_id'], $order_arr) ) {
                $model['purchase'] = $this->model('Logic_Purchase')->getAllByOrderId($model['order_id']);
                $order_arr[] = $model['order_id'];
            }
            array_push($models, $model);
        }
        
        $this->view->total = $total;
        $this->view->total_with_tax = $total_with_tax;
        $this->view->models = $models;
        $this->view->subtitle = "Payment Prediction List";
    }

}