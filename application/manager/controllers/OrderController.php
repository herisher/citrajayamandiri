<?php
/**
 *  Order
 */
class Manager_OrderController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/order/list';

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
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['disp_status'] = Dao_Order::$statics['status_flag'][$model['status_flag']];
            $model['disp_payment'] = Dao_Order::$statics['payment_flag'][$model['payment_flag']];
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Order List";
    }

    public function detailAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Order')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['status_disp'] = isset(Dao_Order::$statics['status_flag'][$model['status_flag']]) ? Dao_Order::$statics['status_flag'][$model['status_flag']] : "";
            $model['payment_disp'] = isset(Dao_Order::$statics['payment_flag'][$model['payment_flag']]) ? Dao_Order::$statics['payment_flag'][$model['payment_flag']] : "";
            $model['document_disp'] = isset(Dao_Order::$statics['document_flag'][$model['document_flag']]) ? Dao_Order::$statics['document_flag'][$model['document_flag']] : "";
            $this->view->model = $model;
            
            $models = $this->model('Logic_Order')->getDetail($id);
            $this->view->models = $models;
            
            $this->view->delivery = $this->model('Logic_Delivery')->getAllByOrderId($id);
            $this->view->invoice = $this->model('Logic_Invoice')->getAllByOrderId($id);
            $this->view->purchase = $this->model('Logic_Purchase')->getAllByOrderId($id);
            $this->view->wages = $this->model('Logic_Wages')->getAllByOrderId($id);
        } else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Order Detail";
    }

    /**
     * 編集チェック
     */
    private function editValid($form, $model) {
        $error_str = array();
        
        // フォームチェック
        if ( !$form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
        
        if($model['price'] != $form->getValue('price') && !$form->getValue('description')) {
            $error_str['description'] = 'Price changed. Please input old price.';
        }
        
        // if( $model['quantity'] > $form->getValue('quantity') && $form->getValue('status_flag') ) {
        //     $error_str['status_flag'] = 'Please check this status.';
        // }
        
        if(count($error_str)) {
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }

    /**
     *  カテゴリ詳細
     */
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // フォーム設定読み込み
            $form = $this->view->form;
            $form->getElement('status_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['status_flag']);
            $form->getElement('payment_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['payment_flag']);
            $form->getElement('document_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['document_flag']);
            $form = $this->model("Logic_OrderDetail")->getAllAsForm($id, $form);
        
            $model = $this->model('Dao_Order')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $form->setDefaults($model);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $this->view->model = $model;
            
            $models = $this->model('Logic_Order')->getDetail($id);
            $this->view->models = $models;

            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form, $model) ) {
                    $params = $this->getRequest()->getParams();
                    $this->model("Logic_OrderDetail")->doSave($model['id'], $params);
                    $this->doUpdate($model['id'], $form);
                }
            }
            $this->view->delivery = $this->model('Logic_Delivery')->getAllByOrderId($id);
            $this->view->invoice = $this->model('Logic_Invoice')->getAllByOrderId($id);
            $this->view->purchase = $this->model('Logic_Purchase')->getAllByOrderId($id);   
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Order Edit";
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Order');
        $sub_total = $form->getValue('price')*$form->getValue('quantity');
        $ppn = $sub_total/10;
        $total = $sub_total + $ppn;

        $model = $table->retrieve($id);
        $status_flag = $form->getValue('status_flag');
        if( $model['quantity'] < $form->getValue('quantity') && $form->getValue('status_flag') ) {
            $status_flag = 0;
        }
        
        $model_id = $table->update(
            array(
                'order_date'        => $form->getValue('order_date'),
                'price'             => $form->getValue('price'),
                'quantity'          => $form->getValue('quantity'),
                "sub_total"         => $sub_total ,
                "ppn"               => $ppn,
                "total"             => $total,
                'payment_term'      => $form->getValue('payment_term'),
                'plan'              => $form->getValue('plan'),
                'description'       => $form->getValue('description'),
                'receipt_date'      => $form->getValue('receipt_date'),
                'delivery_week'     => $form->getValue('delivery_week'),
                'dept'              => $form->getValue('dept'),
                'status_flag'       => $status_flag,
                'payment_flag'      => $form->getValue('payment_flag'),
                'document_flag'     => $form->getValue('document_flag'),
                'update_date'       => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );
        $this->gobackList();
    }

    /**
     * 新規登録チェック
     */
    private function createValid($form) {
        $error_str = array();
        
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }

        $order = $this->model('Logic_Order')->findByNo($form->getValue('order_no'));
        if($order) {
            $error_str['order_no'] = 'Already Order No.';
        }
        
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        if(!$session->product_list) {
            $error_str['product'] = 'Please be sure to input.';
        }
        
        if(count($error_str)) {
            $this->view->error_str = $error_str;
            return false;
        }
        return true;
    }   
    
    /**
     * 新規登録
     */
    public function createAction() {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('document_flag')->setMultiOptions(array('' => '▼Choose') + Dao_Order::$statics['document_flag']);
        
        if ($session->product_list) {
            $this->view->product_list = $session->product_list;
        }
        
        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('delete') ) {
                $form->isValid($_POST);
                $session->product_list = null;
                $this->view->product_list = null;
            } elseif ( $this->getRequest()->getParam('check') ) {
                $form->isValid($_POST);
                $error_str = array();
                if( $form->getValue('order_no') ) {
                    $order = $this->model('Logic_Order')->findByNo($form->getValue('order_no'));
                    if($order) {
                        $error_str['order_no'] = 'Already Order No.';
                    } else {
                        $error_str['order_no'] = 'Available Order No.';
                    }
                    $this->view->error_str = $error_str;
                } else {
                    $error_str['order_no'] = 'Please be sure to input.';
                }
                $this->view->error_str = $error_str;
            } elseif ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        } else {
            $session->product_list = null;
            $this->view->product_list = $session->product_list;
        }
        $this->view->subtitle = "Order Create";
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        $table = $this->model('Dao_Order');
        $sub_total = $form->getValue('price')*$form->getValue('quantity');
        $ppn = $sub_total/10;
        $total = $sub_total + $ppn;
        $model_id = $table->insert(
            array(
                'product_id'        => $session->product_list['id'],
                'order_no'          => $form->getValue('order_no'),
                'order_date'        => $form->getValue('order_date'),
                'price'             => $form->getValue('price'),
                'quantity'          => $form->getValue('quantity'),
                "sub_total"         => $sub_total ,
                "ppn"               => $ppn,
                "total"             => $total,
                'payment_term'      => $form->getValue('payment_term'),
                'plan'              => $form->getValue('plan'),
                'description'       => $form->getValue('description'),
                'receipt_date'      => $form->getValue('receipt_date'),
                'delivery_week'     => $form->getValue('delivery_week'),
                'dept'              => $form->getValue('dept'),
                'document_flag'     => $form->getValue('document_flag'),
                'status_flag'       => 4,//waiting material
                'create_date'       => new Zend_Db_Expr('now()'),
                'update_date'       => new Zend_Db_Expr('now()'),
            )
        );
        
        for( $i=1; $i<=13; $i++) {
            if( $form->getValue('size_'.$i) ) {
                $mod_id = $this->model('Dao_OrderDetail')->insert(
                    array(
                        'order_id'        => $model_id,
                        'size'          => $i,
                        'quantity'        => $form->getValue('size_'.$i),
                    )
                );
            }
        }
        
        $this->gobackList();
    }
    
    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // データを削除
            $table = $this->model('Dao_Order');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->db()->query(
                "DELETE FROM `dtb_order_detail` WHERE `order_id` = ?", $id
            );
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'It is an illegal URL.';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    public function printOutstandingAction() {
        $this->_helper->layout()->disableLayout();
        $model = $this->model('Logic_Order')->getOutstanding();
        $this->view->models = $model;
    }
}
