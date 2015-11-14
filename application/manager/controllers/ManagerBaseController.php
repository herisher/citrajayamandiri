<?php
/**
 * ベース
 */
class ManagerBaseController extends Zend_Controller_Action {
    /**
     * リダイレクト
     * @override
     */
    protected function _redirect($url, array $options = array()) {
        // コミット
        $this->db()->commit();

        return parent::_redirect($url, $options);
    }

    /**
     * データベースアダプターを返す
     */
    public function db() {
        return Zend_Db_Table_Abstract::getDefaultAdapter();
    }

    /**
     * モデルのインスタンスを返す
     */
    public function model($modelname, $option = array()) {
        $table = new $modelname($option);
        return $table;
    }

    /**
     * ナビゲータの生成
     */
    public function createNavigator($datas, $limit = 10) {
        // 現在のページ数を保存するためのセッション
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $list_path   = '/' . $module_name . '/' . $class_name . '/list';
        $session     = new Zend_Session_Namespace($list_path);
        
        $paginator = null;
        if ( get_class($datas) === 'Zend_Db_Table_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbTableSelect( $datas )
            );
        } elseif ( get_class($datas) === 'Zend_Db_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbSelect( $datas )
            );
        } else {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_Array( $datas )
            );
        }
        
        // リミットの設定
        if ($this->getRequest()->getParam('limit')) {
            $limit = $this->getRequest()->getParam('limit');
        }
        $session->limit = $limit;
        
        // ページング
        $paginator->setItemCountPerPage($limit);
        if ( $paginator->count() && $this->getRequest()->getParam('page') ) {
            $page = $this->getRequest()->getParam('page');
            $session->page = $page;
            $paginator->setCurrentPageNumber($page);
            if ( $page === '1' ) {
                $this->view->assign('first_page', true);
            } else {
                $this->view->assign('first_page', false);
            }
        } else {
            $session->page = 1;
            $this->view->assign('first_page', true);
        }
        
        $this->view->assign('pages', $paginator->getPages());
        $this->view->assign('paginator', $paginator);
    }

    /**
     * フォームチェックとエラー生成
     */
    public function checkForm($form, $config, &$error_str) {
        foreach ( $config->elements as $key => $element ) {
            if ( isset($element->errors) ) {
                if ( $form->getElement($key)->getErrors() ) {
                    foreach ( $form->getElement($key)->getErrors() as $error ) {
                        if ( isset($element->errors->$error) ) {
                            $message = $element->errors->$error;
                        } else {
                            $message = $element->errors->other;
                        }
                        if ( isset($element->errors->place) ) {
                            $error_str[$element->errors->place] = $message;
                        } else {
                            $error_str[$key] = $message;
                        }
                    }
                }
            }
        }
    }

    /**
     * フォームの自動生成
     */
    public function createForm() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name = $this->getRequest()->getControllerName();
        $func_name = $this->getRequest()->getActionName();
        $config_path = APPLICATION_PATH . '/../application/' . $module_name . '/configs/' . $class_name . '.ini';
        if ( file_exists($config_path) ) {
            $config = new Zend_Config_Ini($config_path, 'form');
            $form = new Zend_Form($config->$class_name->$func_name);
            $this->view->config = $config->$class_name->$func_name;
            $this->view->form = $form;
        }
    }

    /**
     * 一覧のページ番号を取得
     */
    public function createLastPage() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $this->view->module_name = $module_name;
        $this->view->class_name  = $class_name;
    }

    /**
     * 一覧に戻る
     */
    public function gobackList() {
        $module_name = $this->getRequest()->getModuleName();
        $class_name  = $this->getRequest()->getControllerName();
        $list_path   = '/' . $module_name . '/' . $class_name . '/list';
        $session = new Zend_Session_Namespace($list_path);
        $this->_redirect($list_path . '/page/' . $session->page . '/limit/' . $session->limit);
    }

    /**
     * 一覧に戻る
     */
    public function gobackListAction() {
        $this->gobackList();
    }

    /**
     * 日付の生成
     */
    public function getYMDHMS() {
        $dates = array();
        $now = getdate();
        $dates['ym']       = sprintf("%04d%02d", $now['year'], $now['mon']);
        $dates['ymd']      = sprintf("%04d%02d%02d", $now['year'], $now['mon'], $now['mday']);
        $dates['ymdh']     = sprintf("%04d%02d%02d%02d", $now['year'], $now['mon'], $now['mday'], $now['hours']);
        $dates['ymdhm']    = sprintf("%04d%02d%02d%02d%02d", $now['year'], $now['mon'], $now['mday'], $now['hours'], $now['minutes']);
        $dates['ymdhms']   = sprintf("%04d-%02d-%02d %02d:%02d:%02d", $now['year'], $now['mon'], $now['mday'], $now['hours'], $now['minutes'], $now['seconds']);
        $dates['unixtime'] = $now[0];
        $dates['year']     = $now['year'];
        $dates['mon']      = $now['mon'];
        $dates['mday']     = $now['mday'];
        $dates['hours']    = $now['hours'];
        $dates['minutes']  = $now['minutes'];
        $dates['seconds']  = $now['seconds'];
        return $dates;
    }

    /**
     * ログインチェック
     */
    public function checkLogin($redirect_flag = true) {
        $session = new Zend_Session_Namespace('admin');
        if ($session->id) {
            // ログイン済みのサイト情報取得
            $admin = $this->model('Dao_Admin')->retrieve($session->id);
            $this->view->admin = $admin;
        }
        elseif ($redirect_flag) {
            $this->_redirect('/manager/login?return_path=' . urlencode($_SERVER['REDIRECT_URL']));
        }
    }

    /**
     * 各アクションが実行される直前に呼ばれる
     */
    public function preDispatch() {
        // ビューの拡張子をhtmlに変更する
        $this->_helper->viewRenderer->setViewSuffix('html');

        // コンフィグファイルの読み出し
        $config = Zend_Registry::get('config');
        $this->view->app = $config->app;

        // BASIC認証
        if ($this->view->app->basic_auth) {
            if (!isset($_SERVER["PHP_AUTH_USER"])) {
                header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
                header("HTTP/1.0 401 Unauthorized");
                //キャンセル時の表示
                echo "Authorization Required";
                exit;
            }
            else {
                if(!($_SERVER["PHP_AUTH_USER"] == 'buzoo' && $_SERVER["PHP_AUTH_PW"] == 'test')){
                    header("WWW-Authenticate: Basic realm=\"Please Enter Your Password\"");
                    header("HTTP/1.0 401 Unauthorized");
                    //キャンセル時の表示
                    echo "Authorization Required";
                    exit;
                }
            }
        }

        // ログインチェック
        $this->checkLogin();

        // フォームの自動生成
        $this->createForm();

        // 一覧のページ番号を取得
        $this->createLastPage();
    }

    /**
     * 各アクションが実行される直後に呼ばれる
     */
    public function postDispatch() {
        if ($this->_helper->layout->isEnabled()) {
        }
    }

    public function doUpload($key, $sw = 0, $sh = 0) {
        if ( array_key_exists($key, $_FILES) && $_FILES[$key]['size'] ) {
            try {
                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();

                $uniqid = uniqid('',true);

				if($image->getImageFormat() == 'JPEG'){
					$format = 'jpg';
				} elseif($image->getImageFormat() == 'PNG'){
					$format = 'png';
				} elseif($image->getImageFormat() == 'GIF'){
					$format = 'gif';
				} else {
					return null;
				}

                $path_n = APPLICATION_PATH . "/images/" . $uniqid . '.' . $format;
                $path_t = APPLICATION_PATH . "/images/thumbnail/" . $uniqid . '.' . $format;
                $url_n  = $this->view->app->base_url . "/images/" . $uniqid . '.' . $format;
                $url_t  = $this->view->app->base_url . "/images/thumbnail/" . $uniqid . '.' . $format;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                // ファイル書き込み
				
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
                
                return array(
                    'image_url' => $url_n,
                    'thumb_url' => $url_t,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    public function doUpload2($key, $sw = 0, $sh = 0, $uw = 0, $uh = 0) {
        if ( array_key_exists($key, $_FILES) && $_FILES[$key]['size'] ) {
            try {
                $uniqid = uniqid('',true);

                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();

				if($image->getImageFormat() == 'JPEG'){
					$format = 'jpg';
				} elseif($image->getImageFormat() == 'PNG'){
					$format = 'png';
				} elseif($image->getImageFormat() == 'GIF'){
					$format = 'gif';
				} else {
					return null;
				}

                $path_n = APPLICATION_PATH . "/upload/" . $uniqid . '.' . $format;
                $path_t = APPLICATION_PATH . "/upload/thumbnail/" . $uniqid . '.' . $format;
                $url_n  = $this->view->app->base_url . "/upload/" . $uniqid . '.' . $format;
                $url_t  = $this->view->app->base_url . "/upload/thumbnail/" . $uniqid . '.' . $format;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();
                
                // リサイズなし
                if ($uw == $iw && $uh == $ih) {
                    // ファイル書き込み
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $uw / $iw ) > ( $uh / $ih ) ) {
                    $ww = intval( $iw * $uh / $ih );
                    $hh = intval( $ih * $uw / $iw );

                    $image->scaleImage($uw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($uw, $uh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($uh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $uh / $ih );
                    $hh = intval( $ih * $uw / $iw );

                    $image->scaleImage($ww, $uh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($uw, $uh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($uw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                return array(
                    'image_url' => $url_n,
                    'thumb_url' => $url_t,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

    public function doUpload3($file_path, $sw = 0, $sh = 0, $uw = 0, $uh = 0) {
        try {
            $uniqid = uniqid('',true);

            // イメージ読み込み
            $image = new Imagick();
            $image->readImageBlob(file_get_contents($file_path));
            $iw = $image->getImageWidth();
            $ih = $image->getImageHeight();

			if($image->getImageFormat() == 'JPEG'){
				$format = 'jpg';
			} elseif($image->getImageFormat() == 'PNG'){
				$format = 'png';
			} elseif($image->getImageFormat() == 'GIF'){
				$format = 'gif';
			} else {
				return null;
			}

            $path_n = APPLICATION_PATH . "/images/" . $uniqid . '.' . $format;
            $path_t = APPLICATION_PATH . "/images/thumbnail/" . $uniqid . '.' . $format;
            $url_n  = $this->view->app->base_url . "/images/" . $uniqid . '.' . $format;
            $url_t  = $this->view->app->base_url . "/images/thumbnail/" . $uniqid . '.' . $format;
            
            // リサイズなし
            if ($sw == $iw && $sh == $ih) {
                // ファイル書き込み
                $fh = fopen($path_t, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
            }
            // 高さ縮小
            elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                $ww = intval( $iw * $sh / $ih );
                $hh = intval( $ih * $sw / $iw );

                $image->scaleImage($sw, $hh);

                // 重ね合わせ
                $im2 = new Imagick();
                $im2->newImage($sw, $sh, "none");
                $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                $im2->setImageFormat($format);

                // ファイル書き込み
                $fh = fopen($path_t, 'wb');
                fwrite($fh, $im2->getImagesBlob());
                fclose($fh);
            }
            // 幅縮小
            else {
                $ww = intval( $iw * $sh / $ih );
                $hh = intval( $ih * $sw / $iw );

                $image->scaleImage($ww, $sh);

                // 重ね合わせ
                $im2 = new Imagick();
                $im2->newImage($sw, $sh, "none");
                $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                $im2->setImageFormat($format);

                // ファイル書き込み
                $fh = fopen($path_t, 'wb');
                fwrite($fh, $im2->getImagesBlob());
                fclose($fh);
            }
            
            // イメージ読み込み
            $image = new Imagick();
            $image->readImageBlob(file_get_contents($file_path));
            $iw = $image->getImageWidth();
            $ih = $image->getImageHeight();
            
            // リサイズなし
            if ($uw == $iw && $uh == $ih) {
                // ファイル書き込み
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $image->getImagesBlob());
                fclose($fh);
            }
            // 高さ縮小
            elseif ( ( $uw / $iw ) > ( $uh / $ih ) ) {
                $ww = intval( $iw * $uh / $ih );
                $hh = intval( $ih * $uw / $iw );

                $image->scaleImage($uw, $hh);

                // 重ね合わせ
                $im2 = new Imagick();
                $im2->newImage($uw, $uh, "none");
                $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($uh - $hh) / 2));
                $im2->setImageFormat($format);

                // ファイル書き込み
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $im2->getImagesBlob());
                fclose($fh);
            }
            // 幅縮小
            else {
                $ww = intval( $iw * $uh / $ih );
                $hh = intval( $ih * $uw / $iw );

                $image->scaleImage($ww, $uh);

                // 重ね合わせ
                $im2 = new Imagick();
                $im2->newImage($uw, $uh, "none");
                $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($uw - $ww) / 2), 0);
                $im2->setImageFormat($format);

                // ファイル書き込み
                $fh = fopen($path_n, 'wb');
                fwrite($fh, $im2->getImagesBlob());
                fclose($fh);
            }
            
            return array(
                'image_url' => $url_n,
                'thumb_url' => $url_t,
            );
        } catch(Exception $e) {
            return null;
        }
    }

    public function createThumb($key, $sw = 0, $sh = 0) {
        if ( array_key_exists($key, $_FILES) && $_FILES[$key]['size'] ) {
            try {
                // イメージ読み込み
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();

                $uniqid = uniqid('',true);

				if($image->getImageFormat() == 'JPEG'){
					$format = 'jpg';
				} elseif($image->getImageFormat() == 'PNG'){
					$format = 'png';
				} elseif($image->getImageFormat() == 'GIF'){
					$format = 'gif';
				} else {
					return null;
				}

                $path_t = APPLICATION_PATH . "/images/thumbnail/" . $uniqid . '.s.' . $format;
                $url_t  = $this->view->app->base_url . "/images/thumbnail/" . $uniqid . '.s.' . $format;
                
                // リサイズなし
                if ($sw == $iw && $sh == $ih) {
                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                // 高さ縮小
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($sw, $hh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                // 幅縮小
                else {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );

                    $image->scaleImage($ww, $sh);

                    // 重ね合わせ
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat($format);

                    // ファイル書き込み
                    $fh = fopen($path_t, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                
                return array(
                    'thumb_url' => $url_t,
                );
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }

	/**
     * 以前イメージを削除
     */
	public function doDelete($url,$path = '/upload/') {
		$image_url = APPLICATION_PATH . $url;
		if(!preg_match('/('.preg_quote($path,"/").'.+$)/', $url, $match))
		{
			$image_url = APPLICATION_PATH . $url;
		}else{
			$image_url = "/home/sites/livecard/www/upload/". $match[1];
		}
		
		// ファイルチェック＆許可を変更
		if(is_file($image_url)) {
            chmod($image_url, 0666);
            unlink($image_url);
			return true;
        }
		else {
			return false;
        }
    }
	
	//number : xx000/CJM/yy
	public function customizeNumber($number) {
		if(!$number) {
			return null;
		}
		
		$exp = explode('/', $number);
		$exp[2] = substr($exp[2], 0, 2);
		
		if( date('y') != $exp[2] ) {
        //if( 0 ) {
			$new_number = substr($exp[0], 0, 2).'001/CJM/'.date('y');
		} else {
			$sub = substr($exp[0], 2, 3); //..000
			$subs = $sub+'1';
			if( $subs>0 && $subs<=9 ) {
				$nmr = "00".$subs;
			} elseif( $subs>9 && $subs<=99 ) {
				$nmr = "0".$subs;
			} else if( $subs>99 && $subs<=999 ) {
				$nmr = $subs;
			}
			$exp[0] = substr($exp[0], 0, 2).$nmr;
			$new_number = implode('/', $exp);
		}
		return $new_number;
	}
}
