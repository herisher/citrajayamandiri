<?php
/**
 *  カテゴリ管理
 */
class Manager_DeliveryController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/delivery/list';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'delivery_date desc') {
        $table = $this->model('Dao_Delivery');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $order_by = array('delivery_date desc', 'id desc');
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'order_no' && $value != null ) {
                    $where['order_id IN (SELECT id FROM dtb_order WHERE order_no = ?)'] = $value;
                }
                if ( $key === 'delivery_no' && $value != null ) {
                    $where['delivery_no LIKE ?'] = '%'.$value.'%';
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
                if ( $key === 'delivery_date_start' && $value != null ) {
                    $where['date(delivery_date) >= ?'] = $value;
                }
                if ( $key === 'delivery_date_end' && $value != null ) {
                    $where['date(delivery_date) <= ?'] = $value;
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
        $form->getElement('status')->setMultiOptions(array('' => '▼Choose') + Dao_Delivery::$statics['status']);
        $form->getElement('order_by')->setMultiOptions(array('' => '▼Choose') + Dao_Delivery::$statics['order_by']);
        
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
            $model['disp_status'] = Dao_Delivery::$statics['status'][$model['status']];
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            if( $model['status'] ) {
                $model['invoice'] = $this->model('Logic_Invoice')->findByDeliveryId($model['id']);
            }
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Delivery List";
    }

    /**
     * 編集チェック
     */
    private function editValid($form) {
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
     *  カテゴリ詳細
     */
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // フォーム設定読み込み
            $form = $this->view->form;
            $form->getElement('status')->setMultiOptions(array('' => '▼Choose') + Dao_Delivery::$statics['status']);

            $model = $this->model('Dao_Delivery')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $form->setDefaults($model);
            $form = $this->model("Logic_DeliveryDetail")->getAllAsForm($id, $form);
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $this->view->model = $model;
            $this->view->models = $this->model("Logic_Delivery")->getDetail($id);

            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $params = $this->getRequest()->getParams();
                    $this->model("Logic_DeliveryDetail")->doSave($model['id'], $params);
                    $this->doUpdate($model['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Delivery Edit";
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Delivery');
        $model_id = $table->update(
            array(
                'delivery_no'        => $form->getValue('delivery_no'),
                'delivery_date'        => $form->getValue('delivery_date'),
                'status'            => $form->getValue('status'),
                'description'        => $form->getValue('description'),
                'quantity'             => $form->getValue('quantity'),
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
        
        $latest = $this->model('Logic_Delivery')->getLatest();
        if(!$latest) {
            $delivery_no = "SJ001/CJM-I/".date('y');
        } else {
            $delivery_no = $this->customizeNumber($latest['delivery_no']);
        }
        
        $form->setDefault('delivery_no', $delivery_no);
        
        if ($session->order_list) {
            $form = $this->model("Logic_DeliveryDetail")->getAllAsForm(null, $form, $session->order_list['id']);
            $this->view->models = $this->model('Logic_Order')->getDetail($session->order_list['id']);
            $this->view->order_list = $session->order_list;
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
            $form->setDefault('quantity', 0);
        }
        $this->view->subtitle = "Delivery Create";
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form, $params) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        $table = $this->model('Dao_Delivery');
        
        $delivery_no = $form->getValue('delivery_no');
        if( $form->getValue('delivery_no_add') ) {
            $delivery_no .= "-".$form->getValue('delivery_no_add');
        }
        
        $model_id = $table->insert(
            array(
                'delivery_no'       => $delivery_no,
                'delivery_date'     => $form->getValue('delivery_date'),
                'order_id'          => $session->order_list['id'],
                'product_id'        => $session->order_list['product_id'],
                'description'       => $form->getValue('description'),
                'quantity'          => $form->getValue('quantity'),
                'update_date'       => new Zend_Db_Expr('now()'),
                'create_date'       => new Zend_Db_Expr('now()'),
            )
        );
        $this->model("Logic_DeliveryDetail")->doSave($model_id, $params, $session->order_list['id']);
        
        if( $form->getValue('quantity_ori') == $form->getValue('quantity') ) {
            $status = 1;
        } else {
            $status = 0;
        }
        
        $model_id = $this->model('Dao_Order')->update(
            array(
                'status_flag'           => $status, //delivery finish
                'finish_delivery_date'  => $form->getValue('delivery_date'),
                'update_date'           => new Zend_Db_Expr('now()'),
            ),
            $this->model('Dao_Order')->getAdapter()->quoteInto(
                'id = ?', $session->order_list['id']
            )
        );

        Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
        $this->gobackList();
    }
    
    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
			//get delivery detail
            $model = $this->model('Dao_Delivery')->retrieve($id);
            $model = $model->toArray();
			
            // delete delivery
            $table = $this->model('Dao_Delivery');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
			
			//delete delivery detail
            $this->db()->query(
                "DELETE FROM `dtb_delivery_detail` WHERE `delivery_id` = ?", $id
            );
			
			//update order status
            $this->db()->query(
                "UPDATE `dtb_order` SET `status_flag` = 0 WHERE `id` = ?", $model["order_id"]
            );
			
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'It is an illegal URL.';
            $this->_forward('error', 'Error');
            return;
        }
    }
}