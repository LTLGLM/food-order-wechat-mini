<?php
namespace app\store\controller\setting;

use app\store\controller\Controller;
use think\facade\Cache as Driver;
use think\facade\View;

/**
 * 清理缓存
 */
class Cache extends Controller
{
    /**
     * 清理缓存
     */
    public function clear(bool $isForce = false)
    {
        if ($this->request->isAjax()) {
            $data = $this->postData('cache');
            $this->rmCache($data['keys'], isset($data['isForce']) ? !!$data['isForce'] : false);
            return $this->renderSuccess('操作成功');
        }
        return View::fetch('clear', [
            'cacheList' => $this->getCacheKeys(),
            'isForce' => !!$isForce ?: config('app_debug'),
        ]);
    }

    /**
     * 删除缓存
     */
    private function rmCache(string $keys, bool $isForce = false)
    {
        if ($isForce === true) {
            //Driver::clear();
            rmdirs('../runtime/');//清空缓存目录
            rmdirs('./temp/');//清空图片缓存目录
        } else {
            $cacheList = $this->getCacheKeys();
            foreach (array_intersect(array_keys($cacheList), $keys) as $key) {
                Driver::has($cacheList[$key]['key']) && Driver::delete($cacheList[$key]['key']);
            }
        }
    }

    /**
     * 获取缓存索引数据
     */
    private function getCacheKeys()
    {
        return [
			'setting' => [
                'key' => 'setting',
                'name' => '站点设置'
            ],
            'shop' => [
                'key' => 'shop',
                'name' => '门店设置'
            ],
        ];
    }

}
