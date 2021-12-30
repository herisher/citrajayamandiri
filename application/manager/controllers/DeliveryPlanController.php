<?php
/**
 * トップ
 */
class Manager_DeliveryPlanController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/delivery-plan/list';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_DeliveryPlan');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'delivery_plan_date' && $value != null ) {
                    $where['delivery_plan_date = ?'] = $value;
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
        
        $this->createNavigator(
            $this->createWherePhrase()
        );

        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model["quantity"] = $this->model('Logic_DeliveryPlan')->getQuantity($model["id"]);
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Delivery Plan List";
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
        
        if ($session->order_list) {
            $order_list = $session->order_list;
            $outstanding_list = $this->model('Logic_Order')->getOutstandingByOrder($order_list);
            $this->view->order_list = $outstanding_list;
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
        $table = $this->model('Dao_DeliveryPlan');
        $tableDetail = $this->model('Dao_DeliveryPlanDetail');
        $tableDetailAssortment = $this->model('Dao_DeliveryPlanDetailAssortment');
        
        $order_list = $_POST['order_id'];
        $outstanding_list = $this->model('Logic_Order')->getOutstandingByOrder($order_list);

        $model_id = $table->insert(
            array(
                'delivery_plan_date'    => $form->getValue('delivery_plan_date'),
                'quantity'              => 0,
                'update_date'           => new Zend_Db_Expr('now()'),
                'create_date'           => new Zend_Db_Expr('now()'),
            )
        );

        foreach ($outstanding_list as $model) {
            $detail_id = $tableDetail->insert(
                array(
                    'delivery_plan_id'      => $model_id,
                    'order_id'              => $model['id'],
                    'quantity_total'        => $model['quantity'],
                    'quantity_delivery'     => $model['delivery'],
                    'quantity_outstanding'  => $model['outstanding'],
                    'update_date'           => new Zend_Db_Expr('now()'),
                    'create_date'           => new Zend_Db_Expr('now()'),
                )
            );

            foreach( $model["order_detail"] as $ord_det ) :
                if( count($model['delivery_detail']) ) :
                    $quantity  =  $ord_det['quantity'] - $model['delivery_detail'][$ord_det['size']];
                else :
                    $quantity = $ord_det['quantity'];
                endif;

                $assortment_id = $tableDetailAssortment->insert(
                    array(
                        'delivery_plan_detail_id'   => $detail_id,
                        'size'                      => $ord_det['size'],
                        'quantity'                  => $quantity,
                        'update_date'               => new Zend_Db_Expr('now()'),
                        'create_date'               => new Zend_Db_Expr('now()'),
                    )
                );
            endforeach;
        }

        Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
        $this->gobackList();
    }
    
    /**
     * デフォルト
     */
    public function printAction() {
        $this->_helper->layout()->disableLayout();

        $id = $this->getRequest()->getParam('id');
        
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_DeliveryPlan')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $item = $model->toArray();
            $this->view->model = $item;

            $detail = $this->model('Logic_DeliveryPlan')->getDetail($id);
            $order_list = array();
            foreach( $detail as $det ){
                $order_list[] = $det["order_id"];
            }

            $models = $this->model('Logic_Order')->getOutstandingByOrder($order_list);
            $this->view->models = $models;

        }
    }

    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            //get delivery detail
            $model = $this->model('Dao_DeliveryPlan')->retrieve($id);
            $model = $model->toArray();
            
            // delete delivery plan
            $table = $this->model('Dao_DeliveryPlan');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );

            $detail = $this->model('Logic_DeliveryPlan')->getDetail($id);
            
            //delete delivery plan detail
            $this->db()->query(
                "DELETE FROM `dtb_delivery_plan_detail` WHERE `delivery_plan_id` = ?", $id
            );

            foreach( $detail as $det ) {
                //delete delivery plan detail assortment
                $this->db()->query(
                    "DELETE FROM `dtb_delivery_plan_detail_assortment` WHERE `delivery_plan_detail_id` = ?", $det["id"]
                );
            }
            
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'It is an illegal URL.';
            $this->_forward('error', 'Error');
            return;
        }
    }

}
