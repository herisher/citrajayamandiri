<?php
/**
 * トップ
 */
class Manager_LoginController extends ManagerBaseController {
    /**
     * デフォルト
     */
    public function indexAction() {
        $form = $this->view->form;

        if ( $this->getRequest()->isPost() ) {
            if ( $this->indexValid( $form ) ) {
                // 戻り先があるときはそこへ戻る
                if ( $this->getRequest()->getParam('return_path') ) {
                    $this->_redirect( $this->getRequest()->getParam('return_path') );
                }
                // ないときは管理画面トップへ
                else {
                    $this->_redirect('/manager');
                }
            } else {
                $this->view->error_str = "Incorrect Account or password.";
            }
        }
        else {
            $form->getElement('return_path')->setValue( $this->getRequest()->getParam('return_path') );
        }

        $this->view->subtitle = 'Log In';
    }

    /**
     * ログインチェック
     */
    private function indexValid($form) {
        if (!$form->isValid($_POST)) {
            return false;
        }
        
        $models = $this->model('Dao_Admin')->search(array(
            'account  = ?' => $form->getValue('login_id'),
            'password = ?' => $form->getValue('login_pw')
        ));
        if (count($models)) {
            $session = new Zend_Session_Namespace('admin');
            $session->id = $models->current()->id;
        } else {
            return false;
        }
    
        return true;
    }

    /**
     * 各アクションが実行される直前に呼ばれる
     */
    public function preDispatch() {
        // ビューの拡張子をhtmlに変更する
        $this->_helper->viewRenderer->setViewSuffix('html');
        
        // コンフィグファイルの読み出し
        $config = Zend_Registry::get('config');
        
        // 共通情報
        $this->view->app = $config->app;
        
        // フォームの自動生成
        $this->createForm();
    }
}
