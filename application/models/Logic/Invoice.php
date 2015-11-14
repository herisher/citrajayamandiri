<?php
/**
 * 画像アップロード
 */
   
class Logic_Invoice extends Logic_Base {
    public function findByNo($delivery_no) {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_invoice` WHERE `invoice_no` = ?", $delivery_no
        );
    }
    
    public function findByDeliveryId($delivery_id) {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_invoice` WHERE `delivery_id` = ?", $delivery_id
        );
    }
    
    public function getAllByProduct($product_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_invoice` WHERE `product_id` = ?", $product_id
        );
    }
    
    public function getDetail($delivery_id) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_delivery_detail` WHERE `delivery_id` = ?", $delivery_id
        );
    }
    
    public function getQuantity($order_id) {
        return $this->db()->fetchOne(
            "SELECT sum(`quantity`) FROM `dtb_invoice` WHERE `order_id` = ?", $order_id
        );
    }
    
    public function getLatest() {
        return $this->db()->fetchRow(
            "SELECT * FROM `dtb_invoice` ORDER BY `id` DESC LIMIT 1"
        );
    }
    
    public function generateNumber($invoice_date) {
        $q = $invoice_date;    //yyyy-mm-dd
        $exp = explode('-', $q);
        $day = $exp[2];
        $month = $exp[1];
        $year = $exp[0];
        $month2 = $month+2;
        
        if( $month2>12 ) {
            $month2 = $month2-12;
            $month2 = '0'.$month2;
            $year = $year+1;
        }
        
        if( ($day==31 && $month2==9) || ($month2==2 && $day>28) ) {
            $day = "01";    
            $month2 = $month2+1;
        }
        
        if( strlen($month2) < 2 ) {
            $month2 = '0'.$month2;
        }
        
        $duedate = $year."-".$month2."-".$day;
        return $duedate;
    }
    
    public function getAllByOrderId( $order_id ) {
        return $this->db()->fetchAll(
            "SELECT * FROM `dtb_invoice` WHERE `order_id` = ? ORDER BY `due_date` ASC",
            array($order_id)
        );
    }
    
    public function getKwitansiNo( $model ) {
        $no = substr($model["invoice_no"],2,3);
        $n = date('m',strtotime($model["invoice_date"]));
        $n0 = $n[0];
        if( $n0 == '1' ) {
            $n = $n;
        } else {
            $n = $n[1];
        }
        
        $bln="";
        $rmw = array("","I","II","III","IV","V","VI","VII","VIII","IX","X","XI","XII");
        
        if( array_key_exists($n,$rmw) ) {
            $bln = $rmw[$n];
        }
        
        $thn = date('y',strtotime($model["invoice_date"]));
        return $no.'/'."CJM-".$bln.'/'.$thn;
    }
    
    public function convertion( $tot1 ) {
        if( !$tot1) {
            return;
        }
        
        $tot = strval($tot1);
        $len = strlen($tot);
        $a = "";
        $b = "";
        $bil = "";
        $bil2 = "";
        $tem = 0;
        $var = 0;
        
        for( $i=$len; $i>=0; $i-- ) {
            if( isset($tot[$i]) ) {
                $bil .= $tot[$i];
            }
        }
        
        $spl = str_split($bil,3);
        
        for( $j=0; $j<($len/3); $j++ ) {
            $j = $j;
        }
        
        for( $k=$j-1; $k>=0; $k-- ) {
            for( $l=3; $l>=0; $l-- ) {
                if( isset($spl[$k][$l]) ) {
                    $bil2 .= $spl[$k][$l];
                }
            }
            
            $len2 = strlen($bil2);
            
            for( $m=0; $m<$len2; $m++ ) {
                if( $bil2[$m] == 2 ) {
                    $a .= " Dua";
                } elseif( $bil2[$m] == 3 ) {
                    $a .= " Tiga";
                } elseif( $bil2[$m] ==4 ) {
                    $a .= " Empat";
                } elseif( $bil2[$m] == 5 ) {
                    $a .= " Lima";
                } elseif( $bil2[$m] == 6 ) {
                    $a .= " Enam";
                } elseif( $bil2[$m] == 7 ) {
                    $a .= " Tujuh";
                } elseif( $bil2[$m] == 8 ) {
                    $a .= " Delapan";
                } elseif( $bil2[$m] == 9 ) {
                    $a .= " Sembilan";
                } elseif( $bil2[$m] == 0 ) {
                    $a = $a;
                } elseif( $bil2[$m] == 1 ) {
                    if( $m == ($len2-1) ) {
                        if( $len2 > 1 && $bil2[$m-1] == 1 ) {
                            $a .= " Sebelas";
                            $tem = 1;
                        } else {
                            $a .= " Satu";
                        }
                    } elseif( $m == ($len2-3) ) {
                        $a .= " Seratus";
                        $tem = 1;
                    } elseif( $m == ($len2-2) ) {
                        if( $bil2[$m+1] == 0 ) {
                            $a .= " Sepuluh";
                            $tem = 1;
                        } elseif( $bil2[$m+1] == 1 ) {
                            $a = $a;
                            $tem = 1;
                        } elseif( $bil2[$m+1] > 1 ) {
                            $tem = 1;
                            $var = 1;
                        }
                    }
                }
                
                if( $len2 > 1 ) {
                    if(strlen($bil2)==3 && $m==0 && $bil2[$m]!=0 && $tem!=1) {
                        $a .= " Ratus";
                    } elseif((strlen($bil2)==3 && $m==1&& $bil2[$m]!=0 && $tem!=1) || (strlen($bil2)==2&&$m==0&& $bil2[$m]!=0 && $tem!=1)) {
                        $a .= " Puluh";
                    } elseif( $tem == 1 && $var == 1 ) {
                        $var = 2;
                    } elseif( $var == 2 ) {
                        $a .= " Belas";
                        $var = 0;
                    }
                    $tem = 0;
                }
            }
                
            $b .= $a;
            
            if( $k == 2 ) {
                $b .= " Juta";
            } elseif( $k == 1 ) {
                $b .= " Ribu";
            }
            
            $a = "";
            $bil2 = "";
        }
        
        return $b . " Rupiah";
    }
}