(function() {
    document.body.ondrop = function(event) {
        event.preventDefault();
        event.stopPropagation()
    };
    var newList = {};

    function diyPhone(data) {
        newList = data;
        this.init(data)
    }
	
    diyPhone.prototype = {
        init: function(data) {
            new Vue({
                el: '#app',
                data: {
					indexItem:0,
					newList : data, //初始数据
					action:0,	//当前选中的数组下标
					newListIndex:{
						'name':'',//素材集名称
						'title':'',
						'author':'',
						'content':'',
						'url':'/assets/img/wechat/banner_01.jpg',
						'file_path':'',
						'digest':''
					}
                },
                methods: {
					
					swapNewList:function (arr, index1, index2) {
						arr[index1] = arr.splice(index2, 1, arr[index1])[0];
						return arr;
					},
					//上移
					itemUp:function (indexItem) {
						var that = this;
						if(indexItem){
							that.newList = that.swapNewList(that.newList, indexItem, parseInt(indexItem) - parseInt(1));
							if(indexItem == that.indexItem){
								if(that.newList.length == 1) that.setNewListIndex(0);
								else that.setNewListIndex(parseInt(indexItem) + parseInt(1));
							}else{
								if(indexItem < that.indexItem){
									that.indexItem = parseInt(that.indexItem) + parseInt(1);
									that.setNewListIndex(that.indexItem);
								}
							}
						}else return $eb.message('错误','已经处于置顶，无法上移');
					},
					//下移
					itemDown:function (indexItem) {
						var that = this;
						var length = parseInt(that.newList.length) - parseInt(1);
						if(indexItem != length){
							that.newList = that.swapNewList(that.newList, indexItem, parseInt(indexItem) + parseInt(1));
							if(indexItem == that.indexItem){
								if(that.newList.length == 1) that.setNewListIndex(0);
								else that.setNewListIndex(parseInt(indexItem) + parseInt(1));
							}else{
								if(indexItem < that.indexItem){
									that.indexItem = parseInt(that.indexItem) + parseInt(1);
									that.setNewListIndex(that.indexItem);
								}
							}
						}else return $eb.message('错误','已经处于置底，无法下移');
					},
					//删除项目
					itemDel:function (indexItem) {
						var that = this;
						if(that.newList.length == 1) return $eb.message('错误','不能再删除了');
						that.newList.splice(indexItem,1);//删除数组
						if(indexItem == that.indexItem){
							if(that.newList.length == 1)
								that.setNewListIndex(0);
							else
								that.setNewListIndex(parseInt(indexItem) + parseInt(1));
						}else{
							if(indexItem < that.indexItem){
								that.indexItem = parseInt(that.indexItem) + parseInt(1);
								that.isShow(that.indexItem);
							}
						}
					},
					//获取选中项数据
					setNewListIndex:function (indexItem) {
						var that = this;
						that.indexItem = indexItem;
						that.newListIndex = that.newList[indexItem];
						setContent(that.newListIndex.content);
					},
					//获取图片素材
					addImage: function() {
						var that = this;
						$.fileLibrary({
							type: 'image',
							done: function(img) {
								that.newList[that.action]['url'] = img[0].url;
								that.newList[that.action]['file_path'] = img[0].file_path;
							}
						})
					},	
					//显示选中项目		
					isShow:function (indexItem) {
						var that = this;
						this.action=indexItem;
						that.newListIndex.content = getContent();
						that.newList[that.indexItem] = that.newListIndex;
						that.indexItem = indexItem;
						for (index in that.newList){
							if(index == indexItem) that.newListIndex = that.newList[index]
						}
						setContent(that.newListIndex.content);
					},
					//添加项目
					addItem:function () {
						var arr = {
							'name':'',//素材集名称
							'title':'',
							'author':'',
							'content':'',
							'url':'/assets/img/wechat/banner_01.jpg',
							'file_path':'',
							'digest':''
						}
						this.newList.push(arr);
					},
					//提交保存
                    onSubmit: function() {
						var that = this;
						if(that.newList[0].name == ''){
							layer.msg('素材集名称不可为空', {anim: 6});
							return false
						}
						for (index in that.newList){
							if(that.newList[index].title == ''){
								layer.msg('请输入第'+(parseInt(index)+1)+'篇文章的标题', {anim: 6});
								return false
							}
							if(that.newList[index].author == ''){
								layer.msg('请输入第'+(parseInt(index)+1)+'篇文章的作者', {anim: 6});
								return false
							}
							if(that.newList[index].file_path == ''){
								layer.msg('请输入第'+(parseInt(index)+1)+'篇文章的封面', {anim: 6});
								return false
							}
							if(that.newList[index].digest == ''){
								layer.msg('请输入第'+(parseInt(index)+1)+'篇文章的摘要', {anim: 6});
								return false
							}
							if(that.newList[index].content == ''){
								layer.msg('请输入第'+(parseInt(index)+1)+'篇文章的内容', {anim: 6});
								return false
							}
						}
						$("#my-modal-loading").html('<div class="am-modal-dialog"><div class="am-modal-hd">数据处理中，请等待...</div><div class="am-modal-bd"><span class="am-icon-spinner am-icon-spin"></span></div></div>');
						$("#my-modal-loading").modal('open');
                        $.post('', {
							data: this.newList
                        }, function(result) {
                            if(typeof(result)=='string'){
                                result = JSON.parse(result);
                            }
                            if(result.code == 1){
								$("#my-modal-loading").modal('close');
								$.show_success(result.msg, result.url)
							}else{
								$("#my-modal-loading").modal('close');
								$.show_error(result.msg)
							}
                        })
                    }
                },
				mounted:function () {
					this.newListIndex = this.newList[this.indexItem]
				}
            })
        }
    };
    window.diyPhone = diyPhone
})(window)