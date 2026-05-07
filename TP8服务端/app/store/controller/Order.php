<?php
namespace app\store\controller;

use app\store\model\Order as OrderModel;
use app\store\model\Device as DeviceModel;
use app\store\model\ShopClerk as ShopClerkModel;
use hema\delivery\Driver as Delivery;
use think\facade\View;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

/**
 * 订单管理
 */
class Order extends Controller
{
	
	
	/**
     * 全部退款订单列表
     */
    public function refund_list(string $search='')
    {
        return $this->getList('全部退款订单列表','refund',$search);
    }
	
	/**
     * 待退款订单列表
     */
    public function refund10_list(string $search='')
    {
        return $this->getList('待退款订单列表','refund10',$search);
    }
	
	/**
     * 已退款订单列表
     */
    public function refund20_list(string $search='')
    {
        return $this->getList('已退款订单列表','refund20',$search);
    }
    /**
     * 待收款订单列表
     */
    public function collection_list(string $search='')
    {
        return $this->getList('待收款订单列表','collection',$search);
    }
    /**
     * 待接单订单列表
     */
    public function shop_list(string $search='')
    {
        return $this->getList('待接单订单列表','shop',$search);
    }
	
    /**
     * 待发货订单列表
     */
    public function delivery_list(string $search='')
    {
        return $this->getList('待发货订单列表','delivery',$search);
    }
    /**
     * 待收货订单列表
     */
    public function receipt_list(string $search='')
    {
        return $this->getList('待收货订单列表','receipt',$search);
    }
    /**
     * 已完成订单列表
     */
    public function complete_list(string $search='')
    {
        return $this->getList('已完成订单列表','complete',$search);
    }
    /**
     * 被取消订单列表
     */
    public function cancel_list(string $search='')
    {
        return $this->getList('被取消订单列表','cancel',$search);
    }
    /**
     * 全部订单列表
     */
    public function all_list(string $search='')
    {
        return $this->getList('全部订单列表','all',$search);
    }
    /**
     * 订单列表
     */
    private function getList(string $title, string $dataType, string $search = '')
    {  
        $model = new OrderModel;
        $list = $model->getList($dataType,0,$search);
        return View::fetch('index', compact('title','list','search'));
    }
    
    /**
     * 订单详情
     */
    public function detail(int $id)
    {
        $detail = OrderModel::detail($id);
        return View::fetch('detail', compact('detail'));
    }
    /**
     * 商家取消订单
     */
    public function cancel()
     {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->cancel()) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
     }
    
    /**
     * 确认接单
     */
    public function shop()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->shopStatus()) {
            return $this->renderSuccess('操作完成');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 确认收到用户付款
     */
    public function collection()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->collection()) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
     /**
     * 确认发货 - （堂食、自取）
     */
    public function delivery()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->deliveryStatus(30)) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 确认完成
     */
    public function receipt()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->receipt()) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 退款操作
     */
    public function refund()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->refund($data['status'])) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 重打订单
     */
    public function prints()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if (DeviceModel::print($model,2)) {
            return $this->renderSuccess('操作成功');
        }
        return $this->renderError('操作失败');
    }
    
    /**
     * 叫号
     */
    public function spk()
    {
        $data = $this->postData();
		if(DeviceModel::push('rows',$data['id'])){
			return $this->renderMsg('操作成功');
		}
		return $this->renderError('操作失败');

    }
    
    /**
     * 外卖配送
     */
    public function waimai(int $id)
    {
        if ($this->request->isGet()) {
            $model = new ShopClerkModel;
    		$clerk = $model->getList('',false);
            $dv = new Delivery();
            $company = $dv->company();
            return $this->renderSuccess('', '', compact('company','clerk')); 
        }
        $model = OrderModel::detail($id);
        // 更新记录
        if ($model->waimai($this->postData('data'))) {
            return $this->renderSuccess('操作成功', url('order/all_list'));
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    
    /**
     * 取消外卖配送
     */
    public function cancelDelivery()
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->cancelDelivery()) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    /**
     * 设置外卖配送状态
     */
    public function setDeliveryStatus(int $status)
    {
        $data = $this->postData();
        $model = OrderModel::detail($data['id']);
        if ($model->setDeliveryStatus($status)) {
            return $this->renderSuccess('操作成功');
        }
        $error = $model->getError() ?: '操作失败';
        return $this->renderError($error);
    }
    
    /**
     * 导出订单
     */
    public function export_order()
    {
        $data = $this->postData('data');
        $star = strtotime($data['star'].' 00:00:00');
        $end = strtotime($data['end'].' 23:59:59');
        $model = new OrderModel;
        $list = $model->with(['goods' => ['image', 'spec', 'goods'], 'address', 'table','delivery'])
            ->where('create_time','>',$star)
            ->where('create_time','<',$end)
            ->select();
        # 实例化 Spreadsheet 对象
        $spreadsheet = new Spreadsheet();
        # 获取活动工作薄
        $sheet = $spreadsheet->getActiveSheet();
        //文字左右居中对齐
        $styleArray = [
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        //黑色边框
        $borderStyleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN//细边框
                ],
            ],
        ];
        //设置单元格宽度
        $sheet->getColumnDimension('A')->setWidth(8);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(8);
        $sheet->getColumnDimension('D')->setWidth(8);
        $sheet->getColumnDimension('E')->setWidth(8);
        $n = 0;
        foreach ($list as $vo) {
            $n++;
            $sheet->mergeCells('A'.$n.':E'.$n); //合并单元格  
            $title = '     订单号：' . $vo['order_no'];
            $title .= '     下单时间：' . $vo['create_time'];
            $sheet->getCell('A'.$n)->setValue($title);
            
            $n++;
            $sheet->mergeCells('A'.$n.':E'.$n); //合并单元格  
            $title = '# ' . $vo['order_mode']['text'] . ' #';
            if($vo['order_mode']['value']==10 and $vo['table_id'] > 0){
                $title .= '     餐桌：' . $vo['table']['table_name'];
            }else{
                $title .= '     取餐号：' . $vo['row_no'];
            }
            $title .= '     来源：' . $vo['source']['text'];
            $sheet->getCell('A'.$n)->setValue($title);
            
            $n++;
            $sheet->setCellValue('A'.$n,'编号');
            $sheet->setCellValue('B'.$n,'商品');
            $sheet->setCellValue('C'.$n,'单价');
            $sheet->setCellValue('D'.$n,'数量');
            $sheet->setCellValue('E'.$n,'退单');
            $sheet->getStyle('A'.$n.':E'.$n)->getFont()->setBold(true);
            $y = $n;
            $num = 0;//商品列表编号
            foreach ($vo['goods'] as $goods) {
                $n++;
                $num++;
                $sheet->setCellValue('A'.$n,$num);
                if(empty($goods['goods_attr'])){
                    $name = $goods['goods_name'];
                }else{
                    $name = $goods['goods_name'] . '/' . $goods['goods_attr'];  
                }
                $sheet->setCellValue('B'.$n,$name);
                $sheet->setCellValue('C'.$n,$goods['goods_price']);
                $sheet->setCellValue('D'.$n,$goods['total_num']);
                $sheet->setCellValue('E'.$n,$goods['refund_num']);
            }
            $sheet->getStyle('A'.$y.':E'.$n)->applyFromArray($styleArray);
            
            $n++;
            $sheet->mergeCells('A'.$n.':E'.$n);
            $sheet->getCell('A'.$n)->setValue('顾客留言：'.$vo['message']);
            $n++;
            $sheet->mergeCells('A'.$n.':E'.$n);
            if($vo['order_mode']['value']==20){
                $title = '地址：' . $vo['address']['district'] . ' ' . $vo['address']['detail'] . ' ' . $vo['address']['name'] . ' ' .$vo['address']['phone'];
            }else{
                $title = '用户：' . $vo['user']['nickname'] . ' ID：' . $vo['user']['user_id'];
            }
            $sheet->getCell('A'.$n)->setValue($title);
            $n++;
            $sheet->mergeCells('A'.$n.':E'.$n);
            $sheet->getCell('A'.$n)->setValue('付款状态：'.$vo['pay_status']['text'].'  应付金额：￥' . $vo['pay_price']);
            $sheet->getStyle('A'.$y.':E'.$n)->applyFromArray($borderStyleArray);
            $n++;
            $sheet->mergeCells('A'.$n.':E'.$n);
        }  
        # Xlsx类 将电子表格保存到文件
        $writer = new Xlsx($spreadsheet);
        $file_name = $data['star'] . '-' .$data['end'] .'.xls';
        //$writer->save('temp/' . $file_name);//保存在本地
        
       // 客户端文件下载
        header('Content-Type:application/vnd.ms-excel');
        header('Content-Disposition:attachment;filename='.$file_name);
        header('Cache-Control:max-age=0');
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }
}