<?php
/**
 * トップ
 */
class Manager_OutstandingController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/outstanding/list';

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
     * デフォルト
     */
    public function printAction() {
        $this->_helper->layout()->disableLayout();
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
        
        $model = $this->model('Logic_Order')->getOutstandingByYear($session);
        $this->view->subtitle = "Outstanding List";
        $year = (isset($session->post['year']) && ($session->post['year']!='')) ? $session->post['year'] : date('Y');
        $status = (isset($param->post['status_flag']) && ($param->post['status_flag']!='')) ? Dao_Order::$statics['status_flag'][$param->post['status_flag']] : "MATERIAL READY";
        $this->view->year = $year;
        $this->view->status = $status;
        $this->view->models = $model;
    }

    /**
     * デフォルト
     */
    public function printPurchaseAction() {
        $this->_helper->layout()->disableLayout();
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
        
        $model = $this->model('Logic_Order')->getOutstandingByYear($session);
        $this->view->subtitle = "Outstanding List";
        $year = (isset($session->post['year']) && ($session->post['year']!='')) ? $session->post['year'] : date('Y');
        $this->view->year = $year;
        $this->view->models = $model;
    }
}
