<?php
/**
 *  カテゴリ管理
 */
class Manager_InvoiceController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/invoice/list';

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
        $form->getElement('status')->setMultiOptions(array('' => '▼Choose') + Dao_Invoice::$statics['status']);
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
        
        $this->createNavigator(
            $this->createWherePhrase()
        );

        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            $model['disp_status'] = Dao_Invoice::$statics['status'][$model['status']];
            $model['disp_payment_status'] = Dao_Invoice::$statics['payment_status'][$model['payment_status']];
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['delivery'] = $this->model('Dao_Delivery')->retrieve($model['delivery_id']);
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Invoice List";
    }

    /**
     * 編集チェック
     */
    private function editValid($form) {
        $error_str = array();
        
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }
        
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
            $form->getElement('payment_status')->setMultiOptions(array('' => '▼Choose') + Dao_Invoice::$statics['payment_status']);
            $form->getElement('status')->setMultiOptions(array('' => '▼Choose') + Dao_Invoice::$statics['status']);

            $model = $this->model('Dao_Invoice')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $form->setDefaults($model);
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['delivery'] = $this->model('Dao_Delivery')->retrieve($model['delivery_id']);
            $this->view->model = $model;

            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $params = $this->getRequest()->getParams();
                    $this->doUpdate($model['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Invoice Edit";
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Invoice');
        $model_id = $table->update(
            array(
                'invoice_no'    => $form->getValue('invoice_no'),
                'invoice_date'  => $form->getValue('invoice_date'),
                'due_date'      => $form->getValue('due_date'),
                'status'        => $form->getValue('status'),
                'payment_status'=> $form->getValue('payment_status'),
                'quantity'      => $form->getValue('quantity'),
                'price'         => $form->getValue('price'),
                'total'         => $form->getValue('total'),
                'discount'              => $form->getValue('discount'),
                'total_min_discount'    => $form->getValue('total_min_discount'),
                'tax'                   => $form->getValue('tax'),
                'total_include_tax'     => $form->getValue('total_include_tax'),
                'update_date'           => new Zend_Db_Expr('now()'),
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
        
        $latest = $this->model('Logic_Invoice')->getLatest();
        if(!$latest) {
            $invoice_no = "FK001/CJM/".date('y');
        } else {
            $invoice_no = $this->customizeNumber($latest['invoice_no']);
        }
        
        $form->setDefault('invoice_no', $invoice_no);
        
        if ($session->delivery_list) {
            $this->view->delivery_list = $session->delivery_list;
            $form->setDefaults($session->delivery_list);
            //print_r($session->delivery_list);
        }
        
        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('delete') ) {
                $form->isValid($_POST);
                $session->delivery_list = null;
                $this->view->delivery_list = null;
                $this->view->models = null;
                $form->setDefault('quantity', 0);
                $this->_redirect("/manager/invoice/create");
            } elseif ( $this->getRequest()->getParam('select') ) {
                //$form->isValid($_POST);
            } elseif ( $this->createValid($form) ) {
                $this->doCreate($form, $params);
            }
        } else {
            $session->delivery_list = null;
            $this->view->models = $session->delivery_list;
        }
        $this->view->subtitle = "Invoice Create";
    }

    public function generateNumberAction() {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        
        $q = $this->_getParam("q");    //yyyy-mm-dd
        
        echo $this->model('Logic_Invoice')->generateNumber($q);
    }
    
    /**
     * 新規登録開始
     */
    private function doCreate($form, $params) {
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        $table = $this->model('Dao_Invoice');
        $model_id = $table->insert(
            array(
                'invoice_no'        => $form->getValue('invoice_no').'-'.$form->getValue('call'),
                'delivery_id'       => $session->delivery_list['id'],
                'order_id'          => $session->delivery_list['order_id'],
                'product_id'        => $session->delivery_list['product_id'],
                'ob_no'             => $form->getValue('ob_no'),
                'invoice_date'      => $form->getValue('invoice_date'),
                'due_date'          => $form->getValue('due_date'),
                'quantity'          => $form->getValue('quantity'),
                'price'             => $form->getValue('price'),
                'total'             => $form->getValue('total'),
                'discount'              => $form->getValue('discount'),
                'total_min_discount'    => $form->getValue('total_min_discount'),
                'tax'                   => $form->getValue('tax'),
                'total_include_tax' => $form->getValue('total_include_tax'),
                'update_date'       => new Zend_Db_Expr('now()'),
                'create_date'       => new Zend_Db_Expr('now()'),
            )
        );
        
        $this->db()->query('UPDATE `dtb_delivery` SET `status` = 1 WHERE `id` = ?', $session->delivery_list['id']);
        
        $order_qty = $this->model('Logic_Order')->getQuantity($session->delivery_list['order_id']);
        $invoice_qty = $this->model('Logic_Invoice')->getQuantity($session->delivery_list['order_id']);
        //Change status order become invoice finish
        if( $order_qty == $invoice_qty ) {
            $this->db()->query('UPDATE `dtb_order` SET `status_flag` = 2 WHERE `id` = ?', $session->delivery_list['order_id']);
        }
        
        Zend_Session::namespaceUnset(self::NAMESPACE_LIST);
        $this->gobackList();
    }
    
    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // データを削除
            $table = $this->model('Dao_Invoice');
            $invoice = $table->retrieve($id);
            $this->db()->query("UPDATE `dtb_delivery` SET `status` = 0, `update_date` = now() WHERE `id` = ?", $invoice['delivery_id']);
            $order = $this->model('Dao_Order')->retrieve($invoice['order_id']);
            if( $order['status_flag'] == 2 ) {
                $this->db()->query("UPDATE `dtb_order` SET `status_flag` = 1, `update_date` = now() WHERE `id` = ?", $invoice['order_id']);
            }
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'It is an illegal URL.';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    /**
     *  カテゴリ詳細
     */
    public function printAction() {
        $this->_helper->layout()->disableLayout();
        $id = $this->getRequest()->getParam('id');
        
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Invoice')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['delivery'] = $this->model('Dao_Delivery')->retrieve($model['delivery_id']);
            $model['terbilang'] = $this->model('Logic_Invoice')->convertion($model['total_include_tax']);
            $model['kwitansi_no'] = $this->model('Logic_Invoice')->getKwitansiNo($model);
            $model['invoice_no1'] = substr($model['invoice_no'],2,3);
            $this->view->rs = $model;
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    public function printKwAction() {
        $this->_helper->layout()->disableLayout();
        $id = $this->getRequest()->getParam('id');
        
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Invoice')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $model['order'] = $this->model('Dao_Order')->retrieve($model['order_id']);
            $model['product'] = $this->model('Dao_Product')->retrieve($model['product_id']);
            $model['delivery'] = $this->model('Dao_Delivery')->retrieve($model['delivery_id']);
            $model['terbilang'] = $this->model('Logic_Invoice')->convertion($model['total_include_tax']);
            $model['kwitansi_no'] = $this->model('Logic_Invoice')->getKwitansiNo($model);
            $model['invoice_no1'] = substr($model['invoice_no'],2,3);
            $this->view->rs = $model;
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
    }
    
    public function printPolybagAction() {
        $this->_helper->layout()->disableLayout();
        $id = $this->getRequest()->getParam('id');
    }
}