<?php
/**
 * ???
 */
class Dao_WagesDetail extends Dao_Base {
    protected $_name    = 'dtb_wages_detail';
    protected $_primary = 'id';

    /**
     * statics
     */
    public static $statics = array(
        'order_by' => array(
            'wages_id asc' => 'Wages No.',
            'create_date desc' => 'Create Date',
        ),
    );
}
