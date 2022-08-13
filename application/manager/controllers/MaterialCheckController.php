<?php
/**
 *  Order
 */
class Manager_MaterialCheckController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/material-check/list';

    /**
     * where phrase
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Order');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'order_no' && $value != null ) {
                    $where['order_no = ?'] = $value;
                }
                if ( $key === 'article' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE article LIKE ?)'] = '%'.$value.'%';
                }
                if ( $key === 'project' && $value != null ) {
                    $where['product_id IN (SELECT id FROM dtb_product WHERE project LIKE ?)'] = '%'.$value.'%';
                }
                if ( $key === 'plan' && $value != null ) {
                    $where['plan LIKE ?'] = '%'.$value.'%';
                }
                if ( $key === 'status_flag' && $value != null ) {
                    $where['status_flag = ?'] = $value;
                }
                if ( $key === 'payment_flag' && $value != null ) {
                    $where['payment_flag = ?'] = $value;
                }
                if ( $key === 'document_flag' && $value != null ) {
                    $where['document_flag = ?'] = $value;
                }
                if ( $key === 'create_date_start' && $value != null ) {
                    $where['date(create_date) >= ?'] = $value;
                }
                if ( $key === 'create_date_end' && $value != null ) {
                    $where['date(create_date) <= ?'] = $value;
                }
                if ( $key === 'delivery_week_start' && $value != null ) {
                    $where['delivery_week >= ?'] = $value;
                }
                if ( $key === 'delivery_week_end' && $value != null ) {
                    $where['delivery_week <= ?'] = $value;
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
        }
    }

    /**
     *  カテゴリ一覧
     */
    public function listAction() {
        // 整列
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('status_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['status_flag']);
        $form->getElement('payment_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['payment_flag']);
        $form->getElement('document_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['document_flag']);
        $form->getElement('order_by')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['order_by']);
        
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
            } elseif ( $this->getRequest()->getParam('print') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/material-check/print');
            } else {
                // 検索条件復元
                $this->restoreSearchForm($form);
            }
        } else {
            // 検索条件復元
            $this->restoreSearchForm($form);
        }
        
        $this->createNavigator(
            $this->createWherePhrase()
        );

        // 表示用カスタマイズ
        $models = array();
        $po = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['disp_status'] = Dao_Order::$statics['status_flag'][$model['status_flag']];
            $model['disp_payment'] = Dao_Order::$statics['payment_flag'][$model['payment_flag']];
            $model['purchase_date'] = '-';
 
            $query = "
                select p.purchase_date, dt.* 
                from dtb_purchase_detail dt 
                JOIN dtb_purchase p ON dt.purchase_id = p.id
                WHERE order_id = " . $model['id'] . "
                order by id";
            $purchases = $this->db()->fetchAll($query);
            $i = 0;
            foreach( $purchases as $purchase ) {
                if($i==0) { $model['purchase_date'] = $purchase['purchase_date']; }
                $model['material_id_'.$purchase['material_id']] = isset($model['material_id_'.$purchase['material_id']]) ? $model['material_id_'.$purchase['material_id']]+$purchase['qty'] : $purchase['qty'];
                $i++;
            }
            
            $delivery = $this->model('Logic_Delivery')->getAllByOrderId($model['id']);
            $total = 0;

            if( count($delivery) ) {
                foreach( $delivery as $deliver ){
                    $total += $deliver['quantity'];
                }
            }

            $model['outstanding'] = $model['quantity'] - $total;

            array_push($models, $model);
            $po[] = $model['id'];
        }

        $list_po = join(',',$po);
        $query = "
        SELECT m.id, m.material_desc 
        FROM dtb_material m 
        WHERE m.id IN (
                        SELECT material_id 
                        FROM dtb_purchase_detail 
                        WHERE purchase_id IN (
                                SELECT dtb_purchase.id 
                                FROM dtb_purchase 
                                WHERE order_id IN (
                                        SELECT id 
                                        FROM dtb_order WHERE id IN (".$list_po."))))";

        $materials = $this->db()->fetchAll($query);

        $this->view->models = $models;
        $this->view->materials = $materials;
        $this->view->subtitle = "Material Check";
    }

    /**
     *  カテゴリ一覧
     */
    public function printAction() {
        $this->_helper->layout()->disableLayout();
        // 整列
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

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
            } elseif ( $this->getRequest()->getParam('print') ) {
                // 検索開始
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/material-check/print');
            }
        }
        
        $this->createNavigator(
            $this->createWherePhrase()
        );

        // 表示用カスタマイズ
        $models = array();
        $po = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['disp_status'] = Dao_Order::$statics['status_flag'][$model['status_flag']];
            $model['disp_payment'] = Dao_Order::$statics['payment_flag'][$model['payment_flag']];
            $model['purchase_date'] = '-';

            $query = "
                select p.purchase_date, dt.* 
                from dtb_purchase_detail dt 
                JOIN dtb_purchase p ON dt.purchase_id = p.id
                WHERE order_id = " . $model['id'] . "
                order by id";
            $purchases = $this->db()->fetchAll($query);
            $i = 0;
            foreach( $purchases as $purchase ) {
                if($i==0) { $model['purchase_date'] = $purchase['purchase_date']; }
                $model['material_id_'.$purchase['material_id']] = isset($model['material_id_'.$purchase['material_id']]) ? $model['material_id_'.$purchase['material_id']]+$purchase['qty'] : $purchase['qty'];
                $i++;
            }
            
            $delivery = $this->model('Logic_Delivery')->getAllByOrderId($model['id']);
            $total = 0;

            if( count($delivery) ) {
                foreach( $delivery as $deliver ){
                    $total += $deliver['quantity'];
                }
            }

            $model['outstanding'] = $model['quantity'] - $total;
            array_push($models, $model);
            $po[] = $model['id'];
        }

        $list_po = join(',',$po);
        $query = "
        SELECT m.*
        FROM dtb_material m 
        WHERE m.id IN (
                        SELECT material_id 
                        FROM dtb_purchase_detail 
                        WHERE purchase_id IN (
                                SELECT dtb_purchase.id 
                                FROM dtb_purchase 
                                WHERE order_id IN (
                                        SELECT id 
                                        FROM dtb_order WHERE id IN (".$list_po."))))";

        $materials = $this->db()->fetchAll($query);

        $this->view->models = $models;
        $this->view->materials = $materials;
        $this->view->subtitle = "Material Check";
    }

}
