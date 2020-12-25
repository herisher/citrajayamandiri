<?php
/**
 *  カテゴリ管理
 */
class Manager_MaterialController extends ManagerBaseController {
    const NAMESPACE_LIST = '/manager/material/list';

    /**
     * 検索条件作成
     */
    private function createWherePhrase($order_by = 'id desc') {
        $table = $this->model('Dao_Material');
        $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);

        // セッションから検索条件を復元する
        $where = array();
        if ($session->post) {
            foreach ((array)$session->post as $key => $value) {
                // 検索条件セット
                if ( $key === 'material_desc' && $value ) {
                    $where['material_desc like ?'] = '%'.$value.'%';
                }
                if ( $key === 'id' && $value != null ) {
                    $where['id = ?'] = $value;
                }
                if ( $key === 'part_no' && $value != null ) {
                    $where['part_no = ?'] = $value;
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
            } elseif ( $this->getRequest()->getParam('csv') ) {
                // CSVダウンロード
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                $session->post = $_POST;
                $this->_redirect('/manager/item/csv');
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
            array_push($models, $model);
        }
        $this->view->models = $models;
        $this->view->subtitle = "Material List";
    }

    /**
     * 編集チェック
     */
    private function editValid($id, $form) {
        $error_str = array();
        
        // フォームチェック
        if (! $form->isValid($_POST) ) {
            $this->checkForm($form, $this->view->config, $error_str);
        }

        $order = $this->model('Logic_Material')->findByNo($form->getValue('part_no'));
        if($order) {
            if( $order['id'] != $id) {
                $error_str['part_no'] = 'Already Material Part No.';
            }
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

        // フォーム設定読み込み
        $form = $this->view->form;
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            $model = $this->model('Dao_Material')->retrieve($id);
            
            if (!$model) {
                $this->view->error_str = 'Data does not exist or has been deleted.';
                $this->_forward('error', 'Error');
                return;
            }

            // 初期値設定
            $item = $model->toArray();
            $form->setDefaults($item);
            $this->view->model = $item;
            
            // エラーチェック
            if ( $this->getRequest()->isPost() ) {
                $form->setDefaults($_POST);
                $session = new Zend_Session_Namespace(self::NAMESPACE_LIST);
                if ( $this->getRequest()->getParam('confirm') ) {
                    if ( $this->editValid($item['id'], $form) ) {
                        $this->doUpdate($item['id'], $form);
                    }
                }
            }
        }
        else {
            $this->view->error_str = 'Data does not exist or has been deleted.';
            $this->_forward('error', 'Error');
            return;
        }
        $this->view->subtitle = "Material Edit";
    }

    /**
     * 編集開始
     */
    private function doUpdate($id, $form) {
        $table = $this->model('Dao_Material');

        $item = $table->retrieve($id);
		
        $model_id = $table->update(
            array(
                'part_no'          	=> $form->getValue('part_no'),
                'material_desc'     => $form->getValue('material_desc'),
                'price'   			=> $form->getValue('price'),
                'bom'         		=> $form->getValue('bom'),
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

        $order = $this->model('Logic_Material')->findByNo($form->getValue('part_no'));
        if($order) {
            $error_str['part_no'] = 'Already Material Part No.';
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
		
        // エラーチェック
        if ( $this->getRequest()->isPost() ) {
            if ( $this->getRequest()->getParam('confirm') ) {
                if ( $this->createValid($form) ) {
                    $this->doCreate($form);
                }
            }
        }
        $this->view->subtitle = "Material Create";
    }

    /**
     * 新規登録開始
     */
    private function doCreate($form) {
        $table = $this->model('Dao_Material');
		$model_id = $table->insert(
			array(
				'part_no'			=> $form->getValue('part_no'),
				'material_desc'		=> $form->getValue('material_desc'),
				'price'				=> $form->getValue('price'),
				'bom'				=> $form->getValue('bom'),
				'create_date'       => new Zend_Db_Expr('now()'),
				'update_date'       => new Zend_Db_Expr('now()'),
			)
		);
        $this->gobackList();
    }

    /**
     * CSVアップロード
     */
    public function csvuploadAction() {
        $this->view->subtitle = "アイテム一括登録";

        $form = $this->view->form;
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('upload') ){
                if (array_key_exists("filecsv", $_FILES) &&
                    file_exists($_FILES["filecsv"]["tmp_name"]) &&
                    filesize($_FILES["filecsv"]["tmp_name"]))
                {
                    if ($this->importcsv($_FILES["filecsv"]["tmp_name"], "")) {
                        $this->_forward('csvcomplete');
                        return;
                    }
                } else {
                    $this->view->error_str = "CSVファイルを選択してください。";
                    return;
                }
            }
        }
    }

    /**
     * ZIPアップロード
     */
    public function zipuploadAction() {
        $this->view->subtitle = "アイテム一括登録";
        $form = $this->view->form;
        if ($this->getRequest()->isPost()) {
            if ($this->getRequest()->getParam('upload') ){
                // アップロードファイルサイズエラー
                if (empty($_POST) || $_FILES['filezip']['error'] == UPLOAD_ERR_INI_SIZE) {
                    $max = ini_get('upload_max_filesize');
                    $this->view->error_str = "アップロードに失敗しました。ファイルサイズを{$max}バイトまでにしてください";
                    return;
                }

                if (array_key_exists("filezip", $_FILES) &&
                    file_exists($_FILES["filezip"]["tmp_name"]) &&
                    filesize($_FILES["filezip"]["tmp_name"]))
                {
                    // ZIPファイル解析
                    $logic = new Logic_Zipfile();
                    $result = $logic->doUpload('filezip');
                    if ($result) {
                        // 画像が多すぎる
                        if (count($result) > 100) {
                            $this->view->error_str = "アップロードに失敗しました。画像は100個までにしてください";
                            return;
                        }
                        // インポート開始
                        if ($this->importcsv($logic->csv_file, $result)) {
                            $this->_forward('csvcomplete');
                            return;
                        }
                    } else {
                        $this->view->error_str = "アップロードに失敗しました。";
                        return;
                    }
                } else {
                    $this->view->error_str = "ZIPファイルを選択してください。";
                    return;
                }
            }
        } 
    }

    /**
     * CSVインポート
     */
    public function importcsv($fn, $file_hash) {
        $form = $this->view->form;

        $inserted = 0;
        $updated  = 0;

        $fpointer = fopen($fn, "r");
        if ($fpointer) {
            $lines = 1;
            while (false != ($data = Util_Csv::fgetcsv_reg($fpointer))) {
                // 
                if ($lines == 1) {
                    $lines = 2;
                    continue;
                }

                // 列数のチェック
                if (count($data) != 11 && count($data) != 12) {
                    $this->db()->rollback();
                    $this->db()->beginTransaction();
                    $this->view->error_str = "列数を確認してください。";
                    return false;
                }

                // データ投入用データの生成（文字コード変換含む）
                $datas = array(
                    'member_id'         => mb_convert_encoding($data[1], 'UTF-8', 'SJIS-win'),
                    'genre_id'          => mb_convert_encoding($data[2], 'UTF-8', 'SJIS-win'),
                    'category_id'       => mb_convert_encoding($data[3], 'UTF-8', 'SJIS-win'),
                    'sub_category_id'   => mb_convert_encoding($data[4], 'UTF-8', 'SJIS-win'),
                    'item_name'         => mb_convert_encoding($data[5], 'UTF-8', 'SJIS-win'),
                    'content'           => mb_convert_encoding($data[6], 'UTF-8', 'SJIS-win'),
                    'buy_url'           => mb_convert_encoding($data[7], 'UTF-8', 'SJIS-win'),
                    'likes'             => mb_convert_encoding($data[8], 'UTF-8', 'SJIS-win'),
                    'memo'              => mb_convert_encoding($data[9], 'UTF-8', 'SJIS-win'),
                    'disp_flag'         => mb_convert_encoding($data[10], 'UTF-8', 'SJIS-win'),
                    'create_date'       => new Zend_Db_Expr('now()'),
                    'update_date'       => new Zend_Db_Expr('now()'),
                ); 

                // ファイルアップロード
                if ($data[11] && $file_hash && isset($file_hash[$data[11]])) {
                    $results = $this->doUpload3($file_hash[$data[11]], 80, 80, 640, 640);
                    if($results) {
                        $datas['image_url'] = $results['image_url'];
                        $datas['thumb_url'] = $results['thumb_url'];
                    }
                }

                // エラーチェック
                $_POST['member_id']         = $datas['member_id'];
                $_POST['genre_id']          = $datas['genre_id'];
                $_POST['category_id']       = $datas['category_id'];
                $_POST['sub_category_id']   = $datas['sub_category_id'];
                $_POST['item_name']         = $datas['item_name'];
                $_POST['content']           = $datas['content'];
                $_POST['buy_url']           = $datas['buy_url'];
                $_POST['likes']             = $datas['likes'];
                $_POST['memo']              = $datas['memo'];
                $_POST['disp_flag']         = $datas['disp_flag'];

                if ($form->isValid($_POST)) {
                    if ($data[0]) {
                        $this->model('Dao_Item')->update($datas, "id = ".intval($data[0]));
                        $updated++;
                    } else {
                        $item_id = $this->model('Dao_Item')->insert($datas);
                        $inserted++;
                    }
                } else {
                    $this->db()->rollback();
                    $this->db()->beginTransaction();
                    
                    $errors = array();
                    $this->checkForm($form, $this->view->config, $errors);

                    $error_str = "CSVファイルの内容に不備があります。";
                    foreach ($errors as $key => $value) {
                        $error_str .= "<br /><br />[".$lines."行目]".$value.":".$form->getValue($key);
                    }
                    $this->view->error_str = $error_str;

                    return false;
                }

                $lines++;
            }
        } else {
            $this->view->error_str = "ファイルを開けませんでした。";
            return false;
        } 
        fclose($fpointer);

        $this->view->inserted = $inserted;
        $this->view->updated  = $updated;

        return true;
    }

    /**
     * CSVアップロード完了
     */
    public function csvcompleteAction() {
        $this->view->subtitle = "アイテム一括登録";
    }

    /**
     * 削除
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        if ( $id && preg_match("/^\d+$/", $id) ) {
            // データを削除
            $table = $this->model('Dao_Material');
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
     * CSVダウンロード
     */
    public function csvAction() {
        // リミッター解除
        ini_set('memory_limit','-1');

        $models = $this->model('Dao_Item')->fetchAll();

        // ビューを無効にする
        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        // ヘッダ出力
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename=item' . time() . '.csv');

        $arrCsvOutputCols = array('id','member_id','genre_id','category_id','sub_category_id','item_name','content','buy_url','likes','memo','disp_flag');

        $arrCsvOutputTitle = mb_convert_encoding('"管理ID","会員ID","ジャンルID","カテゴリID","サブカテゴリID","アイテム名","内容","購入先URL","cawaiiね！数","管理者メモ","表示フラグ"', 'SJIS-win', 'UTF-8');

        // CSV出力
        echo $arrCsvOutputTitle . "\r\n";
        foreach ($models as $model) {
            $item = $model->toArray();
            $cols = array();
            foreach ($arrCsvOutputCols as $col) {
                if ($col) {
                    // 改行をトル
                    $value = $item[$col];
                    $value = str_replace("\r", "", $value);
                    $value = str_replace("\n", "", $value);
                    $value = str_replace("\"", "\"\"", $value);
                    
                    // 列ごとに整形
                    array_push($cols, '"' . $value . '"');
                } else {
                    array_push($cols, '');
                }
            }
            echo mb_convert_encoding(join(",", $cols), 'SJIS-win', 'UTF-8') . "\r\n";
        }
    }
}