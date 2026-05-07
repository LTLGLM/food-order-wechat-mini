(function() {
    document.body.ondrop = function(event) {
        event.preventDefault();
        event.stopPropagation()
    };
    var diyData = {};
    var options = {};

    function diyPhone(temp, data, opts) {
        diyData = temp;
        this.init(data, opts)
    }
	
    diyPhone.prototype = {
        init: function(data, opts) {
            new Vue({
                el: '#app',
                data: {
                    diyData: data,
                    selectedIndex: -1,
                    curItem: {},
                    opts: opts
                },
                methods: {
					//添加组件
                    onAddItem: function(temp) {
                        var data = $.extend(true, {}, diyData[temp]);
                        this.diyData.items.push(data);
                        this.onEditer(this.diyData.items.length - 1)
                    },
					//拖拽组件
                    onDragItemEnd: function(DragItem) {
                        this.onEditer(DragItem.newIndex)
                    },
					//编辑组件
                    onEditer: function(index) {
                        this.selectedIndex = index;
                        this.curItem = this.selectedIndex === 'page' ? this.diyData.page : this.diyData.items[this.selectedIndex];
                        this.initEditor()
                    },
					//删除组件
                    onDeleleItem: function(index) {
                        var that = this;
                        layer.confirm('你确定要删除吗？',{
                            icon: 0,
                            btn:['取消','确定'],
                            title: '友情提示',
                            skin: 'layui-layer-hema',
                            cancel : function(){
                                // 你点击右上角 X 回调
                            },
                            btn1:function(temp,layero){
								layer.close(temp);
                            },
                            btn2:function(temp){
                                that.diyData.items.splice(index, 1);
								that.selectedIndex = -1;
                                layer.close(temp);
                            },
                            end:function() {
                                //所有操作都会执行
							}
						})
                    },
					//选择图片
                    onEditorSelectImage: function(curItemParams, index) {
                        $.fileLibrary({
                            type: 'image',
                            done: function(img) {
								curItemParams[index] = img[0].url;
                            }
                        })
                    },
					//设置颜色
                    onEditorResetColor: function(curItemStyle, background, color) {
                        curItemStyle[background] = color
                    },
					//删除组件数据项目
                    onEditorDeleleData: function(index, selectedIndex) {
                        if (this.diyData.items[selectedIndex].data.length <= 1) {
                            layer.msg('至少保留一个', {
                                anim: 6
                            });
                            return false
                        };
                        this.diyData.items[selectedIndex].data.splice(index, 1)
                    },
					//添加组件数据项目
                    onEditorAddData: function() {
                        var item = $.extend(true, {}, diyData[this.curItem.type].data[0]);
                        this.curItem.data.push(item)
                    },
					//初始化组件
                    initEditor: function() {
                        this.$nextTick(function() {
                            if (options.hasOwnProperty('key')) {
                                options.destroy()
                            };
                            this.editorHtmlComponent();
							//富文本
                            if (this.curItem.type === 'richText') {
                                this.onRichText(this.curItem)
                            };
							//商品组和拼团商品
                            if (this.curItem.type === 'goods' || this.curItem.type === 'groupGoods' || this.curItem.type === 'bargainGoods' || this.curItem.type === 'seckillGoods') {
                                this.editorSelectGoods(this.curItem)
                            }
                        })
                    },
					//选项框事件
                    editorHtmlComponent: function() {
                        var diyeditor = $(this.$refs['diy-editor']); 
                        diyeditor.find('input[type=checkbox], input[type=radio]').uCheck();
                    },
					//获取商品列表
                    onSelectGoods: function(curItem) {
                        var url = {
                            goods: 'goods/lists',
                            groupGoods: 'group/lists', 	//拼团
                            bargainGoods: 'bargain/lists',	//砍价
							seckillGoods: 'seckill/lists'	//秒杀
                        };
                        $.selectData({
                            title: '选择商品',
                            uri: url[curItem.type],
                            duplicate: false,
                            dataIndex: 'goods_id',
                            done: function(data) {
                                data.forEach(function(item) {
                                    curItem.data.push(item)
                                })
                            },
                            getExistData: function() {
                                var list = [];
                                curItem.data.forEach(function(goods) {
                                    if (goods.hasOwnProperty('goods_id')) {
                                        list.push(goods['goods_id'])
                                    }
                                });
                                return list
                            }
                        })
                    },
					//选择门店 -待开发
                    onSelectShop: function(curItem) {
                        $.selectData({
                            title: '选择门店',
                            uri: 'shop/lists',
                            duplicate: false,
                            dataIndex: 'shop_id',
                            done: function(data) {
                                data.forEach(function(item) {
                                    curItem.data.push(item)
                                })
                            },
                            getExistData: function() {
                                var list = [];
                                curItem.data.forEach(function(shop) {
                                    if (shop.hasOwnProperty('shop_id')) {
                                        list.push(shop['shop_id'])
                                    }
                                });
                                return list
                            }
                        })
                    },
					//富文本初始化
                    onRichText: function(curItem) {
                        options = UM.getEditor('ume-editor', {
                            initialFrameWidth: 375,
                            initialFrameHeight: 400
                        });
                        options.ready(function() {
                            options.setContent(curItem.params.content);
                            options.addListener('contentChange', function() {
                                curItem.params.content = options.getContent()
                            })
                        })
                    },
					//提交保存
                    onSubmit: function() {
                        if (this.diyData.items.length <= 0) {
                            layer.msg('至少存在一个组件', {anim: 6});
                            return false
                        };
                        $.post('', {
                            //data: JSON.stringify(this.diyData)
							data: this.diyData
                        }, function(result) {
                            if(typeof(result)=='string'){
                                result = JSON.parse(result);
                            }
                            result.code == 1 ? $.show_success(result.msg, result.url) : $.show_error(result.msg);
                        })
                    }
                }
            })
        }
    };
    window.diyPhone = diyPhone
})(window)