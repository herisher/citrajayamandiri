<?php
/**
 * 管理者
 */
class Manager_AdminController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/admin/list';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Admin');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'account' && $value ) {
                    $where['account = ?'] = $value;
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
     * 一覧
     */
    public function listAction() {
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

        // 一覧取得
        $this->createNavigator(
            $this->createWherePhrase()
        );

        // 表示用カスタマイズ
        $models = array();
        foreach ($this->view->paginator as $model) {
            $model = $model->toArray();
            array_push($models, $model);
        }
        $this->view->models = $models;
    }

    /**
     * 新規登録チェック
     */
    private function createValid($form) {
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $error_str = array();
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
        // フォーム設定読み込み
        $form = $this->view->form;

        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        }
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form) {
        $table = $this->model('Dao_Admin');
        $model_id = $table->insert(
            array(
                'account'     => $form->getValue('account'),
                'password'    => $form->getValue('password'),
                'type'        => 0,
                'create_date' => new Zend_Db_Expr('now()'),
                'update_date' => '0000-00-00 00:00:00'
            )
        );
        $this->gobackList();
    }

    /**
     * 編集チェック
     */
    private function editValid($form) {
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $error_str = array();
            $this->checkForm($form, $this->view->config, $error_str);
            $this->view->error_str = $error_str;
            return false;
        }
        
        return true;
    }

    /**
     * 編集
     */
    public function editAction() {
        // フォーム設定読み込み
        $form = $this->view->form;

        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Admin')->retrieve($id);

            if (!$model) {
                $this->view->error_str = 'This data is deleted or not exists.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $admin = $model->toArray();
            $form->setDefaults($admin);
            $this->view->model = $admin;

            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->editValid($form) ) {
                    $this->doUpdate($admin['id'], $form);
                }
            }
        }
        else {
            $this->view->error_str = 'This data is deleted or not exists.';
            $this->_forward('error', 'Error');
            return;
        }
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Admin');
        $model_id = $table->update(
            array(
                'password' => $form->getValue('password'),
                'update_date' => new Zend_Db_Expr('now()'),
            ),
            $table->getAdapter()->quoteInto(
                'id = ?', $id
            )
        );
        $this->gobackList();
    }

    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $table = $this->model('Dao_Admin');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'Invalid URL.';
            $this->_forward('error', 'Error');
        }
    }
}
