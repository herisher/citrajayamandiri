<?php
// インクルードパスの設定
define('APPLICATION_PATH', realpath(dirname(__FILE__)));
set_include_path(
    APPLICATION_PATH . '/../library' . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/default/controllers' . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/api/controllers'     . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/manager/controllers' . PATH_SEPARATOR .
    APPLICATION_PATH . '/../application/models'
);

// magic_quotes_gpcを強制的にオフにする
/*
if (get_magic_quotes_gpc()) {
    function stripslashes_deep($value) {
        $value = is_array($value) ? array_map('stripslashes_deep', $value) : stripslashes($value);
        return $value;
    }
    $_POST    = array_map('stripslashes_deep', $_POST);
    $_GET     = array_map('stripslashes_deep', $_GET);
    $_COOKIE  = array_map('stripslashes_deep', $_COOKIE);
    $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
}
*/

// デフォルトタイムゾーンの設定
date_default_timezone_set('Asia/Jakarta');

// エラーチェックを非常に厳密に行う
error_reporting(E_ALL|E_STRICT);

// オートローダ有効化
require_once 'Zend/Loader.php';
Zend_Loader::registerAutoload();

// 設定ファイル読み込み
if ( (isset($_ENV['BZ_STAGE']) && $_ENV['BZ_STAGE'] === '1') || file_exists( "/etc/jkt48_stage" ) ) {
    $config = new Zend_Config_Ini(APPLICATION_PATH . "/../application/configs/config.ini", "staging");
    Zend_Registry::set('config', $config);
} else {
    $config = new Zend_Config_Ini(APPLICATION_PATH . "/../application/configs/config.ini", "production");
    Zend_Registry::set('config', $config);
}

// 携帯機種判別機能読み込み
//$mobile = new Util_Mobile();
//Zend_Registry::set('mobile', $mobile);

// セッション使用開始
Zend_Session::start();

// レイアウト有効化
Zend_Layout::startMvc();

// コントローラの設定
$front = Zend_Controller_Front::getInstance();
$front->setControllerDirectory(array(
    'default' => APPLICATION_PATH . '/../application/default/controllers',
    'api'     => APPLICATION_PATH . '/../application/api/controllers',
    'manager' => APPLICATION_PATH . '/../application/manager/controllers'
));

// ルータの設定
//$routes = array();
//$front->getRouter()->addRoutes($routes);
/*
// テーブルキャッシュの設定
$cache = Zend_Cache::factory('Core',
                             'File',
                             array('automatic_serialization' => true),
                             array('cache_dir' => APPLICATION_PATH . '/../cache'));
Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
*/
// データベース接続初期化
$db = Zend_Db::factory('Pdo_Mysql', $config->database);

// アプリケーション実行開始
$front->throwExceptions(true);

try {
    // データベース接続開始
    $db->beginTransaction();
    $db->query('set names utf8');
    Zend_Db_Table_Abstract::setDefaultAdapter($db);

    // ディスパッチ開始
    $front->dispatch();

    // 変更内容をコミット
    $db->commit();
} catch(Zend_Controller_Dispatcher_Exception $e) {
    if ($config->app->debug) {
        // エラー表示
        echo '<html><body>'.$e->__toString().'</body></html>' . "\n";
        exit();
    } else {
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
} catch(Zend_Controller_Action_Exception $e) {
    if ($config->app->debug) {
        // エラー表示
        echo '<html><body>'.$e->__toString().'</body></html>' . "\n";
    } else {
        header('HTTP/1.1 404 Not Found');
        echo '404 Not Found';
    }
} catch(Exception $e) {
    // データベースに接続されていたらロールバック
    if ($db->isConnected()) {
        $db->rollBack();
    }

    // エラーメールを飛ばす
    Util_Mail::send(array(
        'to' => 'tya@buzoo.biz',
        'subject' => '['.$_SERVER['HTTP_HOST'].'] application error',
        'body' => 'env => ' . print_r($_SERVER, true) . "\n" .
                  'err => ' . $e->__toString()

    ));

    // エラー表示
    if ($config->app->debug) {
        echo '<html><body>'.$e->__toString().'</body></html>' . "\n";
        exit();
    } else {
        header('HTTP/1.1 500 Internal Server Error');
        echo 'Internal Server Error';
    }
}

// ショートカット
function h($var) {
    return htmlspecialchars($var);
}

// 全角→半角カナ
function hankana($str) {
    if (is_array($str)) {
        $datas = array();
        foreach ($str as $key => $data) {
            $datas[$key] = mb_convert_kana($data,"ka","UTF8");
        }
        return $datas;
    } else {
        return mb_convert_kana($str,"ka","UTF8");
    }
}

// エラー表示
function my_mb_error($errors, $key) {
    if (isset($errors[$key])) {
        $front = Zend_Controller_Front::getInstance();
        if ($front->getRequest()->getModuleName() == 'default') {
            return '<font color="#FF0000">※'. $errors[$key] .'</font><br>'."\n";
        } elseif ($front->getRequest()->getModuleName() == 'manager') {
            return '<font color="#FF0000">'. $errors[$key] .'</font><br>'."\n";
        } else {
            return '<font color="#FF0000">※'. $errors[$key] .'</font><br>'."\n";
        }
    }
}
