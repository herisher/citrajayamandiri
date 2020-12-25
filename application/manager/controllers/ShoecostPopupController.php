<?php
/**
 * 栄養士
 */
class Manager_ShoecostPopupController extends BaseController {
    const NAMESPACE_LIST = "/manager/shoecost-popup/list";

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Product');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
        
        // セッションから検索条件を復元する
        $where = array(); 
        if ($session->post) {
            $id_list = array();
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'article' && $value ) {
                    $where['article like ?'] = '%'.$value.'%';
                }
                if ( $key === 'project' && $value ) {
                    $where['project like ?'] = '%'.$value.'%';
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
        $this->_helper->layout->disableLayout();
		
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
                $this->_redirect('/manager/shoecost-popup/list');
            } elseif ( $this->getRequest()->getParam('select') ) {
				$product_session = new Zend_Session_Namespace('/manager/shoecost/list');
				$product_session->product_list = $this->model('Dao_Product')->retrieve($this->getRequest()->getParam('select'));
				Zend_Session::namespaceUnset( self::NAMESPACE_LIST );
				$this->view->need_close = true;
            }
        } 
        
        // 検索条件復元
        $this->restoreSearchForm($form);
        
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
}
