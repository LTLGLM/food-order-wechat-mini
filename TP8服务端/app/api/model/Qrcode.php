<?php
namespace app\api\model;

use app\common\model\Qrcode as QrcodeModel;

/**
 * 普通二维码模型（小程序聚合）
 */
class Qrcode extends QrcodeModel
{
    /**
     * 隐藏字段
     * @var array
     */
    protected $hidden = [];
}
