<?php
namespace app\common\model;

use Endroid\QrCode\QrCode as CodeMode;
use Endroid\QrCode\Writer\PngWriter;
use hema\alipay\Driver as Alipay;
use hema\wechat\Wxapp;
use hema\Helper;

/**
 * 餐桌模型
 */
class Table extends BaseModel
{
    // 定义表名
    protected $name = 'table';
    // 定义主键
    protected $pk = 'table_id';
    // 追加字段
    protected $append = ['qrcode','status'];
    
    /**
     * 获取餐桌码
     */
    public function getQrcodeAttr($value,$data)
    {
        $qrcode = [
            'h5' => '',
            'weixin' => '',
            'alipay' => '',
            'qrcode' => '',
        ];
        //生成存储路径
        if(!file_exists('./temp')){
            mkdir('./temp',0777,true);
        }
        //H5二维码
        if(is_file('./h5/index.html')){
            $h5_path = '/temp/h5-table-' . $data['table_id'] . '.png';
            if(!is_file('.' . $h5_path)){
                $code = CodeMode::create(base_url() . 'api/h5/index/scene/table-' . $data['table_id'])->setSize(500);
                $writer = new PngWriter();
                $result = $writer->write($code);
                $result->saveToFile('.' . $h5_path);
            }
            $qrcode['h5'] = $h5_path;
        }
        //生成小程序聚合码
        $qrcode_path = '/temp/qrcode-table-' . $data['table_id'] . '.png';
        if(!is_file('.' . $qrcode_path)){
            $writer = new PngWriter();
            $code = CodeMode::create(base_url() . 'api/h5/index/scene/table-'. $data['table_id'])->setSize(500);
            $result = $writer->write($code);
            $result->saveToFile('.' . $qrcode_path);
        }
        $qrcode['qrcode'] = $qrcode_path;
        //生成微信小程序码
        $wechat_path = '/temp/wechat-table-' . $data['table_id'] . '.png';
        if(!is_file('.' . $wechat_path)){
            $wx = new Wxapp;
            if($wx->getUnlimitedQRCode($wechat_path,'table-' . $data['table_id'])){
                $qrcode['weixin'] = $wechat_path;
            }
        }else{
            $qrcode['weixin'] = $wechat_path;
        }
        //生成支付宝小程序码
        $alipay_path = '/temp/alipay-table-' . $data['table_id'] . '.png';
        if(!is_file('.' . $alipay_path)){
            $alipay = new Alipay('aliapp');
            if($alipay->openAppQrcodeCreate('table-' . $data['table_id'],$alipay_path)){
                $qrcode['alipay'] = $alipay_path;
            }
        }else{
            $qrcode['alipay'] = $alipay_path;
        }
        return $qrcode;
    }
    
    /**
     * 状态
     */
    public function getStatusAttr($value,$data)
    {
		$status = [10 => '空闲', 20 => '占用'];
		$list = Order::with(['goods','user'])
		    ->where(['table_id'=>$data['table_id'],'order_status'=>10])
		    ->order('order_id','asc')
		    ->select();
		$count = sizeof($list);//计算订单数量
		//初始化餐桌订单
		$order = [
	        'list' => $list,//订单列表
	        'order_count' => $count,//订单数量
	        'order_time' => 0,//接单时间
	        'time' => 0,//占用时间
	        'total_price' => 0,//商品总金额
	        'pay_price' => Helper::number2(0),//应付金额
	        'paid_price' => Helper::number2(0),//已付金额
	        'activity_price' => 0,//优惠金额
	        'ware_price' => 0,//餐具茶水费
	        'delivery_price' => 0,//配送费
	        'pack_price' => 0,//打包费
	    ];
		if($count > 0){
		    $value = 20;//餐桌状态忙
		    $order['order_time'] = date('Y-m-d H:i',$list[0]['shop_time']);//接单时间
		    $time = round((time() - $list[0]['shop_time'])/60);//四舍五入取整，获得分钟数
		    if($time >= 60){
		        $time_str = intval($time / 60) . '时';
		        $time_str .= (string) ($time % 60) . '分';
		    }else{
		        $time_str = (string) $time . '分钟';
		    }
		    $order['time'] = $time_str;
		    //统计订单数据
		    foreach ($list as $item){
		        $order['total_price'] = Helper::number2($item['total_price']); //商品总金额
	            $order['ware_price'] = Helper::number2($item['ware_price']);//餐具茶水费
	            $order['activity_price'] = Helper::number2($item['activity_price']);//优惠价格
	            $order['delivery_price'] = Helper::number2($item['delivery_price']);//配送费用
	            $order['pack_price'] = Helper::number2($item['pack_price']);//餐盒费用
	            
	            //如该订单未付款
	            if($item['pay_status']['value'] == 10){
	                $order['pay_price'] = Helper::number2($item['pay_price']);//订单实付款金额
	            }else{
	                $order['paid_price'] = Helper::number2($item['pay_price']);//订单实付款金额
	            }
		    }
		}else{
		    $value = 10;
		}
		return ['text' => $status[$value], 'value' => $value, 'order' => $order];
    }
    
    /**
     * 获取列表
     */
    public function getList($page=true)
    {
        $filter = [
            'is_delete' => 0    
        ];
        $model = $this->where($filter)
            ->order(['sort','table_id'=>'desc']);
        if($page){
            return $model->paginate(['list_rows'=>15,'query' => request()->param()]);
        }
        // 执行查询
        return $model->select();
    }
  
    /**
     * 添加
     **/
    public function add(array $data)
    {
        return $this->save($data);
    }
      
    //修改
    public function edit(array $data)
    {
        return $this->save($data)!==false;      
    }
    
    //删除
    public function remove()
    {
        //软删除
        return $this->save([
            'is_delete' => 1    
        ]);
    }
}

    