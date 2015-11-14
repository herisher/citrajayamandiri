<?php
/**
 *  カテゴリ管理
 */
class Manager_ProductController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/product/list';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Product');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'product_no' && $value != null ) {
                    $where['product_no = ?'] = $value;
                }
                if ( $key === 'project' && $value != null ) {
                    $where['project like ?'] = '%'.$value.'%';
                }
                if ( $key === 'article' && $value != null ) {
                    $where['article like ?'] = '%'.$value.'%';
                }
                if ( $key === 'call' && $value != null ) {
                    $where['call = ?'] = '%'.$value.'%';
                }
                if ( $key === 'type' && $value != null ) {
                    $where['type = ?'] = $value;
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
        $form->getElement('type')->setMultiOptions(array('' => '▼Choose') + Dao_Product::$statics['type']);
        $form->getElement('order_by')->setMultiOptions(array('' => '▼Choose') + Dao_Product::$statics['order_by']);
        
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
            $model['disp_type'] = Dao_Product::$statics['type'][$model['type']];
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Product List";
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
            $form->getElement('type')->setMultiOptions(array('' => '▼Choose') + Dao_Product::$statics['type']);

            $model = $this->model('Dao_Product')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $model = $model->toArray();
            $exp = explode('-',$model['article']);
            $model['article_front'] = $exp[0];
            $model['article_end'] = $exp[1];
            $form->setDefaults($model);
            $this->view->model = $model;
            
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                if ( $this->getRequest()->getParam('delete') ) {
                    $form->isValid($_POST);
                    $model_id = $this->model('Dao_Product')->update(
                        array(
                            'image_url'     => "",
                            'thumb_url'     => "",
                        ),
                        $this->model('Dao_Product')->getAdapter()->quoteInto(
                            'id = ?', $id
                        )
                    );
                    $this->view->model['image_url'] = "";
                } elseif ( $this->editValid($form) ) {
                    $this->doUpdate($model['id'], $form, $model);
                }
            }
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Product Edit";
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form, $model) {
        $table = $this->model('Dao_Product');
/*
        $image_url = $model['image_url'];
        $thumb_url = $model['thumb_url'];

        if( array_key_exists('image_url', $_FILES) && $_FILES['image_url']['size'] ) {
            $results = $this->doUpload2('image_url', 80, 80, 640, 640);
            if($results) {
                $image_url = $results['image_url'];
                $thumb_url = $results['thumb_url'];
            }
        }
*/
        $model_id = $table->update(
            array(
                'product_no'     => $form->getValue('product_no'),
                'project'        => $form->getValue('project'),
                'article'        => $form->getValue('article_front').'-'.$form->getValue('article_end'),
                'call'           => $form->getValue('call'),
                'type'          => $form->getValue('type'),/*
                'image_url'     => $image_url,
                'thumb_url'     => $thumb_url,*/
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
        // フォーム設定読み込み
        $form = $this->view->form;
        $form->getElement('type')->setMultiOptions(array('' => '▼Choose') + Dao_Product::$statics['type']);
        
        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->createValid($form) ) {
                $this->doCreate($form);
            }
        }
        $this->view->subtitle = "Product Create";
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form) {
        $table = $this->model('Dao_Product');
        /*
        $results = $this->doUpload2('image_url', 80, 80, 640, 640);
        if($results) {
            $image_url = $results['image_url'];
            $thumb_url = $results['thumb_url'];
        } else {
            $image_url = "";
            $thumb_url = "";
        }*/
        
        $model_id = $table->insert(
            array(
                'product_no'     => $form->getValue('product_no'),
                'project'        => $form->getValue('project'),
                'article'        => $form->getValue('article_front').'-'.$form->getValue('article_end'),
                'call'           => $form->getValue('call'),
                'type'          => $form->getValue('type'),/*
                'image_url'     => $image_url,
                'thumb_url'     => $thumb_url,*/
                'create_date'   => new Zend_Db_Expr('now()'),
                'update_date'    => new Zend_Db_Expr('now()'),
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
            // データを削除
            $table = $this->model('Dao_Product');
            $table->delete( $table->getAdapter()->quoteInto('id = ?', $id) );
            $this->gobackList();
        }
        else {
            $this->view->error_str = 'It is an illegal URL.';
            $this->_forward('error', 'Error');
            return;
        }
    }
}
