<?php
/**
 *  payment
 */
class Manager_PaymentController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/payment/list';

    /**
     * where phrase
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Payment');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'payment_no' && $value != null ) {
                    $where['payment_no LIKE ?'] = '%'.$value.'%';
                }
                if ( $key === 'invoice_no' && $value != null ) {
                    $where['id IN (SELECT `payment_id` FROM `dtb_payment_detail` WHERE `invoice_id` IN 
                           (SELECT `id` FROM `dtb_invoice` WHERE `invoice_no` LIKE ?))'] = '%'.$value.'%';
                }
                if ( $key === 'payment_date_start' && $value != null ) {
                    $where['payment_date >= ?'] = $value;
                }
                if ( $key === 'payment_date_end' && $value != null ) {
                    $where['payment_date <= ?'] = $value;
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
            }/* elseif ( $this->getRequest()->getParam('csv') ) {
                // CSVダウンロード
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/item/csv');
            } */else {
                // 検索条件復元
                $this->restoreSearchForm($form);
            }
            $this->_redirect(self::NAMESPACE_LIST);
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
            $model['detail'] = $this->model('Logic_Payment')->getDetail($model['id']);
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Payment List";
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

        // フォーム設定読み込み
        $form = $this->view->form;
        
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Payment')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $item = $model->toArray();
            $form->setDefaults($item);
            $this->view->model = $item;
            
            $detail = $this->model('Logic_Payment')->getDetailForm($item['id']);
            $this->view->invoice_list = $detail;
            
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $this->doUpdate($item['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Payment Edit";
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Payment');

        $model_id = $table->update(
            array(
                'payment_date'   => $form->getValue('payment_date'),
                'update_date'    => new Zend_Db_Expr('now()'),
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

        if( count($error_str) ) {
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }   
    
    /**
     * 新規登録
     */
    public function createAction() {
        // フォーム設定読み込み
        $Logic_Invoice = $this->model('Logic_Invoice');
        $latest = $this->model('Logic_Payment')->getLatest();
        if(!$latest) {
            $payment_no = "PT001/CJM/".date('y');
        } else {
            $payment_no = $this->customizeNumber($latest['payment_no']);
        }
        
        $form = $this->view->form;
        $form->setDefault('payment_no', $payment_no);
        
        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        }

        $this->view->invoice_list = $Logic_Invoice->getAllForPayment(); 
        $this->view->subtitle = "Payment Create";
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form) {
        $table = $this->model('Dao_Payment');
        $table_dt = $this->model('Dao_PaymentInvoice');
        $model_id = $table->insert( 
            array(
                'payment_no'     => $form->getValue('payment_no'),
                'payment_date'   => $form->getValue('payment_date'),
                'create_date'    => new Zend_Db_Expr('now()'),
                'update_date'    => new Zend_Db_Expr('now()'),
            )
        );

        $checked = $_POST['checkRow'];
        foreach( $checked as $key => $value ) {
            $insert_dt = $table_dt->insert(
                array(
                    'payment_id'     => $model_id,
                    'invoice_id'     => $value,
                    'create_date'    => new Zend_Db_Expr('now()'),
                    'update_date'    => new Zend_Db_Expr('now()'),
                )
            );
        }
        $this->gobackList();
    }

    /**
     *  カテゴリ詳細
     */
    public function detailAction() {
        
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Payment')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $item = $model->toArray();
            $this->view->model = $item;
            
            $detail = $this->model('Logic_Payment')->getDetailForm($item['id']);
            $this->view->invoice_list = $detail;
            
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Payment Detail";
    }

    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // データを削除
            $table = $this->model('Dao_Payment');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->db()->query("DELETE FROM `dtb_payment_detail` WHERE `payment_id` = ?", $id);
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'Invalid URL';
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
            $model = $this->model('Dao_Payment')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $item = $model->toArray();
            $item['terbilang'] = $this->model('Logic_Invoice')->convertion($model['total']);
            $this->view->model = $item;
            
            $detail = $this->model('Logic_Payment')->getDetailForm($item['id']);
            $this->view->detail = $detail;
            
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $this->doUpdate($item['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Payment Edit";
    }

}