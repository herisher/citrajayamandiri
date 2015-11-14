<?php
/**
 * 全てのロジックのベースクラス
 */
class Logic_Base {
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
    public function createNavigator($datas, $page, $limit) {
        if (!$page)  { $page  = 1;  }
        if (!$limit) { $limit = 10; }

        $paginator = null;
        if ( gettype($datas) == "object" && get_class($datas) === 'Zend_Db_Table_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbTableSelect( $datas )
            );
        }
        elseif ( gettype($datas) == "object" && get_class($datas) === 'Zend_Db_Select' ) {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_DbSelect( $datas )
            );
        } else {
            $paginator = new Zend_Paginator(
                new Zend_Paginator_Adapter_Array( $datas )
            );
        }
        
        $paginator->setItemCountPerPage($limit);
        if ( $paginator->count() && $page ) {
            $paginator->setCurrentPageNumber($page);
        }
        
        return $paginator;
    }
    
     public function createThumbnail($rawdata, $sw, $sh, $path) {
        // イメージ読み込み
        $image = new Imagick();
        $image->readImageBlob($rawdata);
        $iw = $image->getImageWidth();
        $ih = $image->getImageHeight();
        
        // リサイズなし
        if ($sw == $iw && $sh == $ih) {
            // ファイル書き込み
            $fh = fopen($path, 'wb');
            fwrite($fh, $image->getImagesBlob());
            fclose($fh);

        }
        /*
        // 中央寄せ
        elseif ($sw > $iw && $sh > $ih) {

            // 重ね合わせ
            $im2 = new Imagick();
            $im2->newImage($sw, $sh, "none");
            $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $iw) / 2), intval(($sh - $ih) / 2));
            $im2->setImageFormat('jpeg');

            // ファイル書き込み
            $fh = fopen($path, 'wb');
            fwrite($fh, $im2->getImagesBlob());
            fclose($fh);
        }
        */
        // 高さ縮小
        elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
            $ww = intval( $iw * $sh / $ih );
            $hh = intval( $ih * $sw / $iw );

            $image->scaleImage($sw, $hh);

            // 重ね合わせ
            $im2 = new Imagick();
            $im2->newImage($sw, $sh, "none");
            $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
            //$im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, 0);
            $im2->setImageFormat('jpeg');

            // ファイル書き込み
            $fh = fopen($path, 'wb');
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
            //$im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, 0);
            $im2->setImageFormat('jpeg');

            // ファイル書き込み
            $fh = fopen($path, 'wb');
            fwrite($fh, $im2->getImagesBlob());
            fclose($fh);
        }
    }
}
