<?php
/**
 * 示例 该模型由系统生成
 *
 * @copyright JDphp框架
 * @version 1.0.8
 * @author yy
 */
class m_sample extends base_model
{
    public function __construct()
    {
        parent::__construct();
        $this->db = dbmysqli::instance('default');
        $this->table_name = 'sample';
        $this->table_prid = 'primary_id';
    }

}
?>