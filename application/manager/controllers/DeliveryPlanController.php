<?php
/**
 * トップ
 */
class Manager_DeliveryPlanController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/delivery-plan/list';

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
     * デフォルト
     */
    public function listAction() {
        // 整列
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // フォーム設定読み込み
        $form = $this->view->form;

        $form->getElement('status_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['status_flag']);
        $form->getElement('order_by')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['outstanding_order']);

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
        
        $model = $this->model('Logic_Order')->getOutstandingByYear($session);
        $this->view->subtitle = "Outstanding List";
        $this->view->models = $model;
    }

    /**
     * 新規登録チェック
     */
    private function createValid($form) {
        $error_str = array();
        
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }

        return true;
    }   
    
    /**
     * 新規登録
     */
    public function createAction() {
        //Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('purchase_type')->setMultiOptions(array('' => '▼Choose') + Dao_Purchase::$statics['purchase_type']);
        
        if ($session->order_list) {
            // $form = $this->model("Logic_DeliveryDetail")->getAllAsForm(null, $form, $session->order_list['id']);
            $this->view->models = $this->model('Logic_Order')->getDetail($session->order_list['id']);
            $this->view->order_list = $session->order_list;
        }
        
        if ($session->material_list) {
            $this->view->material_list = $session->material_list;
        }
        
        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('delete') ) {
                $form->isValid($_POST);
                $session->order_list = null;
                $this->view->order_list = null;
                $this->view->models = null;
                $form->setDefault('quantity', 0);
            } elseif ( $this->getRequest()->getParam('select') ) {
                //$form->isValid($_POST);
            } elseif ( $this->createValid($form) ) {
                $params = $this->getRequest()->getParams();
                $this->doCreate($form, $params);
            }
        } else {
            $session->order_list = null;
            $this->view->models = $session->order_list;
            $this->view->order_list = $session->order_list;
            $this->view->material_list = $session->material_list;
            $form->setDefault('quantity', 0);
        }
        $this->view->subtitle = "Delivery Plan Create";
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form, $params) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        $table = $this->model('Dao_Purchase');
        $tableDetail = $this->model('Dao_PurchaseDetail');
        
        $order = $this->model('Dao_Order')->retrieve($session->order_list['id']);

        if( $order['status_flag'] == 4 ) {
            $table_order = $this->model('Dao_Order');
            $model_id = $table_order->update(
                array(
                    'status_flag'       => 0,   //Process
                    'update_date'       => new Zend_Db_Expr('now()'),
                ),
                $table_order->getAdapter()->quoteInto(
                    'id = ?', $order['id']
                )
            );
        }

        $model_id = $table->insert(
            array(
                'purchase_no'       => $form->getValue('purchase_no'),
                'purchase_date'     => $form->getValue('purchase_date'),
                'order_id'          => $session->order_list['id'],
                'product_id'        => $order['product_id'],
                'purchase_type'     => $form->getValue('purchase_type'),
                'description'       => $form->getValue('description'),
                'update_date'       => new Zend_Db_Expr('now()'),
                'create_date'       => new Zend_Db_Expr('now()'),
            )
        );

        $i = 0;
        foreach ($_POST['material_id'] as $key => $value) {
            $detail_id = $tableDetail->insert(
                array(
                    'purchase_id'   => $model_id,
                    'material_id'   => $value,
                    'qty'           => $_POST['qty'][$i],
                    'bom'           => $_POST['bom'][$i],
                    'price'         => $_POST['price'][$i],
                    'update_date'   => new Zend_Db_Expr('now()'),
                    'create_date'   => new Zend_Db_Expr('now()'),
                )
            );
            $i++;
        }
        
        Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
        $this->gobackList();
    }
    
}
