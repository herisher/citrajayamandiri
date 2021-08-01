<?php
/**
 * 管理者
 */
class Dao_Wages extends Dao_Base {
    protected $_name    = 'dtb_wages';
    protected $_primary = 'id';
    
    /**
     * statics
     */
    public static $statics = array(
        'wages_type' => array(
            '0' => 'Normal',    //include : material wages, cutting, sewing, finishing
            '1' => 'Shortage',    //include : all goods already sent to BATA
        ),
        'order_by' => array(
            'wages_no asc' => 'Wages No.',
            'order_id desc' => 'Order',
        ),
    );
}
