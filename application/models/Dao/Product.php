<?php
/**
 * 管理者
 */
class Dao_Product extends Dao_Base {
    protected $_name    = 'dtb_product';
    protected $_primary = 'id';
	
    /**
     * statics
     */
    public static $statics = array(
        'type' => array(
            '1' => 'Local',
            '2' => 'Export',
        ),
        'order_by' => array(
            'project asc' => 'project',
            'article asc' => 'article',
        ),
    );
}
