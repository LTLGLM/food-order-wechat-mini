<?php
namespace app\common\model;

use think\facade\Db;
use hema\device\Driver;

/**
 * 云设备模型
 */
class Device extends BaseModel
{
    // 定义表名
    protected $name = 'device';
    // 定义主键
    protected $pk = 'device_id';
    // 追加字段
    protected $append = ['dev_status'];
    
    /**
     * 类型
     */
    public function getDevTypeAttr($value)
    {
        if($addons = \get_addons_info($value)){
            $title = $addons['title'];
        }else{
            $title = '未知设备';
        }
        return ['text' => $title, 'value' => $value];
    }
    
    /**
     * 状态
     */
    public function getIsOpenAttr($value)
    {
        $status = ['关闭','开启'];
        return ['text' => $status[$value], 'value' => $value];
    }
    
    /**
     * 获取器: 转义数组格式
     */
    public function getValuesAttr($value)
    {
        return json_decode($value, true);
    }
    
    /**
     * 修改器: 转义成json格式
     */
    public function setValuesAttr($value)
    {
        return json_encode($value);
    }
    
    /**
     * 设备联网状态 - 自动完成
     * 返回数据格式："}{"State":0,"Code":200,"Message":"成功"}
     *State:状态值(-1错误 0正常 1缺纸 2温度保护报警 3 忙碌 4 离线)
     */
    public function getDevStatusAttr($value,$data)
    {
        $dev = new Driver($data['dev_type']);
        return $dev->status($data['dev_id']);
    }
    
    /**
     * 获取列表
     */
    public function getList($is_open = null,$page=true)
    {
        $filter = [];// 筛选条件
        !is_null($is_open) && $filter['is_open'] = $is_open;
        // 排序规则
        $sort = ['device_id' => 'desc']; 
         // 执行查询
        if($page){
            return $this->where($filter)
                ->order($sort)
                ->paginate(['list_rows'=>15,'query' => request()->param()]);
        }
        return $this->where($filter)
            ->order($sort)
            ->select();
    }
    
    /**
     * 播报语音
     * $mode = 模式(new=新订单提醒,pay=微信到账提醒) ，$row_no=参数,
     */
    public static function push($mode,$row_no='')
    {
        // 筛选条件
        $filter = [
            'dev_type' => 'hmcalling',
            'is_open' => 1
        ];
        if($call = self::get($filter)){
            if($mode == 'pay' AND $call['values']['is_pay'] == 0){
                return true; //未开启直接退出
            }
            if($mode == 'new' AND $call['values']['is_new'] == 0){
                return true; //未开启直接退出
            }
            $dev = new Driver('hmcalling');
            $dev->push([
                'dev_id' => $call['dev_id'],
                'mode' => $mode,
                'row_no' => $row_no
            ]);
        }
        return true;
    }
    
    /**
     * 打印小票
     * $data 打印的数据
     * $tpl 模板类型 0=新订单模板 1=退单模板，2补打订单，3预约订单,4门店统计报表
     */
    public static function print($data, $tpl=0)
    {
        // 筛选条件
        $filter = ['is_open' => 1];
        //获取打印设备列表
        $list = self::where($filter)
            ->where('dev_type','<>','hmcalling')
            ->select();
        $shop = Setting::getItem('shop');
        foreach ($list as $item){
            //判断是否为订单打印
            if($tpl<3){
                //筛选打印商品订单列表
                if(isset($item['values']['category'])){
                    $goods = [];//筛选后的商品目录
                    //循环订单商品
                    for ($n = 0; $n < sizeof($data['goods']); $n++) {
                        foreach ($item['values']['category'] as $vo) {
                            //判断是否在打印类目中
                            if($data['goods'][$n]['goods']['category_id'] == $vo){
                                $goods[] = $data['goods'][$n];
                            }
                        }
                    }
                    if(sizeof($goods) == 0){
                        return true;//没有要打印的内容
                    }else{
                        $data['goods'] = $goods;//更新打印商品列表
                    }
                } 
            }
            $content = [];//生成的打印模板
            //生成用户退单模板
            if($tpl == 1){
                //退单模板
                $title = $data['order_mode']['text'];
                if($data['order_mode']['value']==10 and $data['table_id']>0){
                    $title .= $data['table']['table_name'];
                }else{
                    $title .= $data['row_no'].'号';
                }
                $content[] = '# 退单 #,<B>,<C>';
                $content[] = '- '.$title.' -,<B>,<C>';
                $content[] = str_repeat('-', 32);
                $content[] = $shop['shop_name'].',<C>';
                $content[] = "    名称                 数量,<B>";//正常一行显示16个汉字，16=4+8+4
                $content[] = str_repeat('-', 32);
                //循环拼接打印模板
                for($i = 0; $i < sizeof($data['goods']); $i++) {
                    if($data['goods'][$i]['refund_num']>0){
                        $goods_name = $data['goods'][$i]['goods_name'];//产品名字
                        !empty($data['goods'][$i]['goods_attr']) && $goods_name .= "/" . $data['goods'][$i]['goods_attr'];
                        $refund_num = $data['goods'][$i]['refund_num']; //产品数量
                        //设置名称字符长度
                        $lan = mb_strlen($goods_name,'utf-8')*2;//一个汉字2个字符长度
                        for($n=$lan;$n<26;$n++){
                            $goods_name .= ' ';
                        }
                        $content[] = $goods_name . '×' . $refund_num.",<B>";
                    }
                }
                $content[] = str_repeat('-', 32);
                $content[] = '支付金额：￥'.$data['pay_price'];
                $content[] = '退款金额：￥'.$data['refund_price'];
                $content[] = '退款理由：'.$data['refund_desc'];
                $content[] = "# 退单 # 完,<B>,<C>";
            }
            //生成订单模板
            if($tpl == 0 or $tpl == 2){
                //订单模板
                $title = $data['order_mode']['text'];
                if($data['order_mode']['value']==10 and $data['table_id']>0){
                    $title .= $data['table']['table_name'];
                }else{
                    $title .= $data['row_no'].'号';
                }
                $content[] = '# '.$title.' #,<B>,<C>';
                $content[] = '下单时间：' . $data['create_time'];
                $content[] = str_repeat('-', 32);
                $content[] = $shop['shop_name'].',<C>';
                if($data['pay_status']['value'] == 20){
                    if($data['pay_mode']['value']==1){
                        $content[] = '-- 微信已付 --,<C>';
                    }elseif($data['pay_mode']['value']==2){
                        $content[] = '-- 余额已付 --,<C>';
                    }elseif($data['pay_mode']['value']==3){
                        $content[] = '-- 当面已付 --,<C>';
                    }elseif($data['pay_mode']['value']==4){
                        $content[] = '-- 支付宝已付 --,<C>';
                    }
                    $content[] = '支付时间:' . datetime($data['pay_time']);
                }else{
                    $content[] = '-- 餐后付款(未付) --,<C>';
                }
                $content[] = "    名称                 数量,<B>";//正常一行显示16个汉字，16=4+8+4
                $content[] = str_repeat('-', 32);
                //循环拼接打印模板
                for ($i = 0; $i < sizeof($data['goods']); $i++) {
                        $goods_name = $data['goods'][$i]['goods_name'];//产品名字
                        !empty($data['goods'][$i]['goods_attr']) && $goods_name .= "/" . $data['goods'][$i]['goods_attr'];
                        $total_num = $data['goods'][$i]['total_num'];   //产品数量
                        //设置名称字符长度
                        $lan = mb_strlen($goods_name,'utf-8')*2;//一个汉字2个字符长度
                        for($n=$lan;$n<26;$n++){
                            $goods_name .= ' ';
                        }
                        $content[] = $goods_name . '×' . $total_num . ",<B>";
                }
                $content[] = str_repeat('-', 32);
                $content[] = '商品金额：+￥'.$data['total_price'];
                if($data['delivery_price']>0){
                    $content[] = '配送费用：+￥'.$data['delivery_price'];
                }
                if($data['pack_price']>0){
                    $content[] = '包装费用：+￥'.$data['pack_price'];
                }
                if($data['activity_price']>0){
                    $content[] = '优惠金额：-￥'.$data['activity_price'];
                }
                $content[] = '实付金额：￥' . $data['pay_price'];
                if(!empty($data['flavor'])){
                    $content[] = '口味偏好：' . $data['flavor'];
                }
                if(!empty($data['message'])){
                    $content[] = '顾客留言：' . $data['message'];
                }
                if($data['order_mode']['value']==20){
                    $content[] = '配送地址：' . $data['address']['detail'];
                    $content[] = '接 收 人：' . $data['address']['name'].$data['address']['phone'];
                }
                $content[] = '# ' . $title . ' # 完,<B>,<C>';
            }
            //生成预约单模板
            if($tpl == 3){
                $content[] = '# 预约订桌 #,<B>,<C>';
                $content[] = $shop['shop_name'].',<C>';
                $content[] = str_repeat('-', 32);
                $content[] = '姓名：'.$data['linkman'];
                $content[] = '电话：'.$data['phone'];
                $content[] = '人数：'.$data['people'];
                $content[] = '时间：'.date("Y-m-d H:i:s",$data['pact_time']);
                $content[] = '备注：'.$data['message'];
                $content[] = str_repeat('-', 32);
                $content[] = "# 预约订桌 # 完,<B>,<C>";
            }
            //生成门店统计报表模板
            if($tpl == 4){
                $content[] = '# 统计报表 #,<B>,<C>';
                $content[] = $shop['shop_name'].',<C>';
                $content[] = $data['star'] . '至' . $data['end'] . ',<C>';
                $content[] = str_repeat('-', 32);
                $content[] = '订单数量,<C>';
                $content[] = '堂食：'.$data['order']['tang'];
                $content[] = '外卖：'.$data['order']['wai'];
                $content[] = '打包：'.$data['order']['bao'];
                $content[] = '退单：'.$data['order']['refund'];
                $content[] = str_repeat('-', 32);
                $content[] = '订单金额,<C>';
                $content[] = '入账：￥'.$data['order']['money']['order'];
                $content[] = '退款：￥'.$data['order']['money']['refund'];
                $content[] = '待入：￥'.$data['order']['money']['order10'];
                $content[] = '待退：￥'.$data['order']['money']['refund10'];
                $content[] = '充值：￥'.$data['recharge'];
                $content[] = '买单：￥'.$data['paybill']['all'];
                $content[] = str_repeat('-', 32);
                $content[] = '支付通道,<C>';
                $alipay = $data['order']['money']['alipay'] + $data['paybill']['alipay'];
                $weixin = $data['order']['money']['weixin'] + $data['paybill']['weixin'];
                $content[] = '支付宝：￥'.$alipay;
                $content[] = '微信：￥'.$weixin;
                $content[] = '余额：￥'.$data['order']['money']['wallet'];
                $content[] = '线下：￥'.$data['order']['money']['offline'];
                $content[] = str_repeat('-', 32);
                $content[] = "# 统计报表 # 完,<B>,<C>";
            }
            $dev = new Driver($item['dev_type']['value']);
            $device = [
                'tpl' => $tpl,
                'dev_id' => $item['dev_id'],//打印机编号
                'prt_num' => $item['values']['prt_num'], //打印分数
            ];
            $dev->print($device,$content);
        }
        return true;
    }
    
    /**
     * 用户设备绑定
     */
    public function add($data)
    {
        //查询设备是否已经添加
        if($this->get([
            'dev_id' => $data['dev_id'],
            'dev_type' => $data['dev_type']
        ])){
            $this->error = '该设备已被添加，不可重复';
            return false;
        }
        $dev = new Driver($data['dev_type']);
        if(!$result = $dev->add($data)){
            $this->error = $dev->getError();
            return false;
        }
        return $this->save($data);
    }
    
    /**
     * 更新
     */
    public function edit($data)
    {
        //云叫号器
        if($this['dev_type']['value'] == 'hmcalling' AND $this['values']['volume'] != $data['values']['volume']){
            $dev = new Driver('hmcalling');
            if(!$result = $dev->push([
                'dev_id' => $this->dev_id,
                'mode' => 'volume',
                'row_no' => $data['values']['volume']
            ])){
                $this->error = $dev->getError();
                return false;
            }
        }
        return $this->save($data) !== false;
    }
    
    /**
     * 删除
     */
    public function remove() 
    {
        $dev = new Driver($this['dev_type']['value']);
        if(!$result = $dev->delete($this->dev_id)){
            $this->error = $dev->getError();
            return false;
        }
        return $this->delete();
    }
    
    /**
     * 状态
     */
    public function status()
    {
        $this->is_open['value'] ? $this->is_open = 0 : $this->is_open = 1;
        return $this->save();
    }
}
