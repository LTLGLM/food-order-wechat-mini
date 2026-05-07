<?php
namespace app\api\controller;

use app\api\model\Table as TableModel;

/**
 * 餐桌/包间控制器
 */
class Table extends Controller
{
    /**
     * 详情
     */
    public function detail($table_id)
    {
		if ($detail = TableModel::get($table_id)) {
            return $this->renderSuccess($detail);
        }
        return $this->renderError('餐桌/包间不存在');
    }
	
}
