(function() {
    document.body.ondrop = function(event) {
        event.preventDefault();
        event.stopPropagation()
    };
    var menus = {};

    function diyPhone(data) {
        menus = data;
        this.init(data)
    }
	
    diyPhone.prototype = {
        init: function(data) {
            new Vue({
                el: '#app',
                data: {
                    menus: data,
                    checkedMenu:{		//选中的菜单数据 - 显示在编辑区
						type:'click',
						name:''
					},
					checkedMenuId:null,	//选中菜单编号
					parentMenuId:null	//父级菜单编号
                },
                methods: {
					//一级菜单默认数据
					defaultMenusData:function(){
						return {
							type:'click',
							name:'',
							key:'',
							sub_button:[]
						};
					},
					//二级菜单默认数据
					defaultChildData:function(){
						return {
							type:'click',
							name:'',
							key:''
						};
					},
					//添加一级菜单
					addMenu:function(){
						if(!this.check()) return false;
						var data = this.defaultMenusData();
						var id = this.menus.length;
						this.menus.push(data);
						this.checkedMenu = data;
						this.checkedMenuId = id;
						this.parentMenuId = null;
					},
					//添加二级菜单
					addChild:function(menu,index){
						if(this.checkedMenu.type !== '' || this.checkedMenuId !== index){
							if(!this.check()) return false;
						}
						if(typeof menu.sub_button == 'undefined'){
							menu.sub_button = [];
						}
						var data = this.defaultChildData();
						var id = menu.sub_button.length;
						menu.sub_button.push(data);
						this.checkedMenu = data;
						this.checkedMenuId = id;
						this.parentMenuId = index;
					},
					//删除菜单
					delMenu:function(){
						console.log(this.parentMenuId);
						this.parentMenuId === null ?
							this.menus.splice(this.checkedMenuId,1) : this.menus[this.parentMenuId].sub_button.splice(this.checkedMenuId,1);
						this.parentMenuId = null;
						this.checkedMenu = {};
						this.checkedMenuId = null;
					},
					//选中菜单
					activeMenu:function(menu,index,pid){
						if(!this.check()) return false;
						if(pid === null){
							this.checkedMenu = menu;
							this.parentMenuId = null;
						}else{
							this.checkedMenu = this.menus[pid].sub_button[index];
							this.parentMenuId = pid;
						}
						this.checkedMenuId=index;
					},
					//切换菜单事件 - 验证当前选中菜单数据的合法性
					check:function(){
						if(this.checkedMenuId === null) return true;
						//如果是一级菜单并且动作类型为空则必须有二级菜单
						if(this.parentMenuId === null && this.checkedMenu.sub_button.length == 0 && this.checkedMenu.type == ''){
							layer.msg('无动作菜单至少包含1个二级菜单', {anim: 6});
							return false;
						}
						if(!this.checkedMenu.name){
							layer.msg('请输入菜单名称', {anim: 6});
							return false;
						}
						if(this.checkedMenu.type == 'click' && !this.checkedMenu.key){
							layer.msg('请输入关键字', {anim: 6});
							return false;
						}
						if(this.checkedMenu.type == 'view' && !this.checkedMenu.url){
							layer.msg('跳转地址不可为空', {anim: 6});
							return false;
						}
						if(this.checkedMenu.type == 'miniprogram'
							&& (!this.checkedMenu.appid
							|| !this.checkedMenu.pagepath
							|| !this.checkedMenu.url)){
							layer.msg('请填写完整小程序配置', {anim: 6});
							return false;
						}
						return true;
					},
					//提交保存
                    onSubmit: function() {
                        if (!this.menus.length) {
                            layer.msg('至少存在一个菜单', {anim: 6});
                            return false
                        };
                        if(!this.check()){
							return false;
						}
						$("#my-modal-loading").html('<div class="am-modal-dialog"><div class="am-modal-hd">数据处理中，请等待...</div><div class="am-modal-bd"><span class="am-icon-spinner am-icon-spin"></span></div></div>');
						$("#my-modal-loading").modal('open');
						console.log(this.menus);
                        $.post('', {
							data: this.menus
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
                }
            })
        }
    };
    window.diyPhone = diyPhone
})(window)