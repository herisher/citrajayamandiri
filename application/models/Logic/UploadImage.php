<?php
/**
 * 画像アップロード
 */
   
    
class Logic_UploadImage extends Logic_Base {
    static $FORMAT = array(
                           'image/gif' => '.gif',
                           'image/jpeg' => '.jpg',
                           'image/pjpeg' => '.jpg',
                           'application/octet-stream' => '.jpg',
                           );
    
    /**
     * 最大アップロードサイズ
     */
    static $MAX_SIZE = 5120000;
    
    public function doUpload($key, $sw = 0, $sh = 0) {
        $config = Zend_Registry::get('config');
        if ( array_key_exists($key, $_FILES) && $_FILES[$key]['size'] ) {
            try {
                $image = new Imagick();
                $image->readImageBlob(file_get_contents($_FILES[$key]['tmp_name']));
                $iw = $image->getImageWidth();
                $ih = $image->getImageHeight();
                $uniqid = uniqid('',true);
                $path_n = APPLICATION_PATH . "/images/thumbnail/".$uniqid.".jpg";
                $url_n  = $config->app->base_url . "/images/thumbnail/".$uniqid.".jpg";     
                if ($sw == 0 && $sh == 0){
                    $sw = $iw;
                    $sh = $ih;
                    $path_n = APPLICATION_PATH . "/images/".$uniqid.".jpg";
                    $url_n  = $config->app->base_url . "/images/".$uniqid.".jpg";
                }           
                if ($sw == $iw && $sh == $ih) {
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $image->getImagesBlob());
                    fclose($fh);
                }
                elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );
                    $image->scaleImage($sw, $hh);
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, 0, intval(($sh - $hh) / 2));
                    $im2->setImageFormat('jpeg');
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                else {                  
                    $ww = intval( $iw * $sh / $ih );
                    $hh = intval( $ih * $sw / $iw );
                    if ($sw == 0 && $sh == 0){
                        $ww = 1;
                        $hh = 1;
                        $image->scaleImage($ww, $sh);
                        $im2 = new Imagick();
                        $im2->newImage($sw, $sh, "none");
                        $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                        $im2->setImageFormat('jpeg');
                    } else {
                        //for thumbnail set compression to 90
                        $image->scaleImage($ww, $sh);
                        $im2 = new Imagick();
                        $im2->setCompression(Imagick::COMPRESSION_JPEG);
                        $im2->setCompressionQuality(90);
                        $im2->newImage($sw, $sh, "none");
                        $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                        $im2->setImageFormat('jpeg');
                    }
                    
                    /*
                    $image->scaleImage($ww, $sh);
                    $im2 = new Imagick();
                    $im2->newImage($sw, $sh, "none");
                    $im2->compositeImage($image, Imagick::COMPOSITE_DEFAULT, intval(($sw - $ww) / 2), 0);
                    $im2->setImageFormat('jpeg');
                     */
                    $fh = fopen($path_n, 'wb');
                    fwrite($fh, $im2->getImagesBlob());
                    fclose($fh);
                }
                return $url_n;
            } catch(Exception $e) {
                return null;
            }
        } else {
            return null;
        }
    }
    
   public function doUpload2($key, $sw = 0, $sh = 0) {
        if(array_key_exists($_FILES[$key]['type'], self::$FORMAT) && $_FILES[$key]["size"] < self::$MAX_SIZE){
            $config = Zend_Registry::get('config');

            // イメージ読み込み
            $image = new Imagick();
            $image->readImage($_FILES[$key]['tmp_name']);
            $iw = $image->getImageWidth();
            $ih = $image->getImageHeight();
            
            $image_direction = 1; //portrait
            
            // フォーマットチェック
            if ($image->getImageFormat() != 'JPEG') {
                return null;
            } elseif ($iw == 1461 && $ih == 2122) {
                $orig_image = file_get_contents($_FILES[$key]['tmp_name']);
                $image_direction = 1; //portrait
            } elseif ($iw == 2122 && $ih == 1461) {
                $tt = $sw;
                $sw = $sh;
                $sh = $tt;
                $orig_image = file_get_contents($_FILES[$key]['tmp_name']);
                $image_direction = 2; //landscape
            } else {
                return null;
            }
            
            $image_id = str_replace(".","",uniqid('',true)).".jpg";

            // 拡大縮小
            if ($sw > $iw && $sh > $ih) {
                // リサイズなし
            }
            elseif ( ( $sw / $iw ) > ( $sh / $ih ) ) {
                // 幅縮小
                $image->resizeImage((int) ( $iw * $sh / $ih ), $sh, imagick::FILTER_LANCZOS, 0.9);
            }
            else {
                // 高さ縮小
                $image->resizeImage($sw, (int) ( $ih * $sw / $iw ), imagick::FILTER_LANCZOS, 0.9);
            }
            
            // ファイル書き込み
            $path = '/home/sites/livecard/www/upload/picture/'.$image_id;
            $link = $config->app->base_url . '/upload/picture/'.$image_id;
            file_put_contents($path, $orig_image);
            
            // ファイル書き込み
            $tpath = '/home/sites/livecard/www/upload/picture/thumb/'.$image_id;
            $tlink = $config->app->base_url . '/upload/picture/thumb/'.$image_id;
            $image->writeImage($tpath);
            $image->destroy();

            return array(
                'imageUrl'       => $link,
                'thumbnailUrl'   => $tlink,
                'imageDirection' => $image_direction,
            );
        }
        return null;
    }
    

    

}
