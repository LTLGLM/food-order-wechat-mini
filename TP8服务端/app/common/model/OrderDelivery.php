<?php
namespace app\common\model;

/**
 * 订单配送模型
 */
class OrderDelivery extends BaseModel
{
    protected $name = 'order_delivery';
    protected $pk = 'order_delivery_id';
    protected $append = [];

    /**
     * 配送公司
     */
    public function getCompanyAttr($value)
    {
        $status = [
            'self' => '商家自配',
            'sf' => '顺丰同城',
            'dada' => '达达快送',
            'uu' => 'UU跑腿',
            'make' => '码科配送',
            'shansong' => '闪送',
            'hmpt' => '本地跑腿',
        ];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 配送状态
     */
    public function getDeliveryStatusAttr($value)
    {
        $status = [
            10 => '待骑手接单',
            20 => '骑手正赶往商家',
            30 => '骑手已到店',
            40 => '骑手开始配送',
            50 => '骑手已送达',
        ];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 订单状态
     */
    public function getStatusAttr($value)
    {
        $status = [
            10 => '进行中',
            20 => '已取消',
            30 => '已完成',
        ];
        return ['text' => $status[$value], 'value' => $value];
    }

    /**
     * 配送距离
     */
    public function getDistanceAttr($value)
    {
        if ($value >= 1000) {
            $text = sprintf('%.2f', $value / 1000) . 'km';
        } else {
            $text = $value . 'm';
        }
        return ['text' => $text, 'value' => $value];
    }
}
