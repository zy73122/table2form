/**
 * 对话框创建后默认隐藏，要调用show()来显示
 * @author yy
 * @version 1.0.5
 * 2010-12-22
 */
(function($){
	var s, cache_warp;
	//private
	_init = function(setting, obj){
		try {
			s = $.extend({}, $.fn.jdDialog.defaults, setting);
			var content;
			
			s.content = obj ? obj.html() : s.content;
			s.content = s.content ? s.content: '';
			if (s.content){
				if (s.content.type=='ajax' || s.content.type=='iframe'){
					var content_type = s.content.type;
					var content_url = s.content.url;
					s.content = '';
				}
			}
			//if (obj) obj.show();
			
			//构造对话框html
			var dlg_mark = $('<div class="jdDialog_mark"></div>').css({'opacity': s.opacity});
			var dlg_warp = $('<div class="jdDialog_warp"></div>');
			var dlg_head = $('<div class="jdDialog_head"><h3>'+ s.title +'</h3></div>');
			var dlg_head_icon = $('<div class="jdDialog_icon"></div>').prependTo(dlg_head.find('h3'));
			var dlg_close = $('<div class="jdDialog_head_right_bg"><div class="jdDialog_head_close"><strong>X</strong></div></div>').appendTo(dlg_head);
			var dlg_body = $('<div class="jdDialog_body"><div class="jdDialog_container">' + s.content + '</div></div>');
			var dlg_helpup = $('<div class="jdDialog_helpup"><div class="jdDialog_helpup_header"></div><div class="jdDialog_helpup_center"><div class="jdDialog_helpup_title">' + s.title + '</div><div style="height: 30px;" class="jdDialog_container">' + s.content + '</div></div><div class="jdDialog_helpup_footer"></div></div>');
			var dlg_helpdown = $('<div class="jdDialog_helpdown"><div class="jdDialog_helpdown_header"></div><div class="jdDialog_helpdown_center"><div class="jdDialog_helpdown_title">' + s.title + '</div><div style="height: 30px;" class="jdDialog_container">' + s.content + '</div></div><div class="jdDialog_helpdown_footer"></div></div>');
			var dlg_tip = $('<div class="jdDialog_tip"><div class="jdDialog_container">' + s.content + '</div></div>');
			var dlg_auto = $('<div class="jdDialog_auto"><div class="jdDialog_container">' + s.content + '</div></div>');
			var dlg_btn_container = $('<div class="jdDialog_btn_container"></div>');
			var dlg_content = dlg_body.find('.jdDialog_container');
									
			//对话框类型
			switch (s.type) {
				case 'helpup':
					dlg_content = 
					dlg_helpup.appendTo(dlg_warp);
					s.width = '271px';
					s.modal = false;
					dlg_warp.addClass('wap_helpup');
					break;
				case 'helpdown':
					dlg_helpdown.appendTo(dlg_warp);
					s.width = '271px';
					s.modal = false;
					dlg_warp.addClass('wap_helpdown');
					break;
				case 'tip':
					dlg_tip.appendTo(dlg_warp);
					s.position = 'center';
					s.width = '200px';
					s.height = '50px';
					dlg_warp.addClass('wap_tip');
					break;
				case 'auto':
					dlg_head.appendTo(dlg_warp);
					dlg_auto.appendTo(dlg_warp);
					s.position = 'center';
					s.width = parseInt(dlg_auto.children().children().css('width')) ?  parseInt(parseInt(dlg_auto.children().children().css('width'))+10)+"px" : '300px';
					s.height = 'auto';
					s.position = null;
					dlg_warp.addClass('wap_auto');
					break;
				case 'loading':
					//dlg_tip.prepend($('<img src="images/loading.gif" align="absmiddle" style="padding-right:10px;margin-left:-10px" />'));
					dlg_tip.appendTo(dlg_warp);
					s.content = s.content ? s.content : 'loading...',
					dlg_tip.text(s.content);
					s.position = 'center';
					s.width = '200px';
					s.height = '50px';
					dlg_warp.addClass('wap_loading');
					break;
				case 'loading2':
					dlg_tip.appendTo(dlg_warp);
					s.content = s.content ? s.content : 'loading...',
					dlg_tip.text(s.content);
					s.position = 'center';
					s.width = '200px';
					s.height = '50px';
					dlg_warp.addClass('wap_loading2');
					break;
				case 'loading3':
					dlg_tip.appendTo(dlg_warp);
					dlg_content.text('');
					s.position = 'center';
					s.width = '50px';
					s.height = '50px';
					dlg_warp.addClass('wap_loading3');
					break;
				case 'small':
					dlg_body.appendTo(dlg_warp);
					$('<div class="jdDialog_icon"></div>').addClass('icon_query').prependTo(dlg_content);
					var hasButtons = false;
					if (typeof s.buttons === 'object' && s.buttons !== null) {
						$.each(s.buttons, function(){
							hasButtons = true;
						});
					}
					if (hasButtons) {
						$.each(s.buttons, function(name, fn){
							var button = $('<button type="button"></button>').text(name).click(fn).appendTo(dlg_btn_container);
						});
						dlg_btn_container.appendTo(dlg_content);
					}
					dlg_warp.addClass('wap_small');
					s.width = '200px';
					s.height = '80px';
					break;
				case 'normal':
				default:
					dlg_head.appendTo(dlg_warp);
					dlg_body.appendTo(dlg_warp);
					dlg_head.find('.jdDialog_icon').addClass('icon_message');
					//自动添加按钮
//					if (!s.buttons) {
//						s.buttons = {'确 定': function(){closeit();}}
//					}
					if (s.buttons) {
						$.each(s.buttons, function(name, fn){
							var button = $('<button type="button"></button>').text(name).click(fn).appendTo(dlg_btn_container);
						});
						dlg_btn_container.appendTo(dlg_content);
					} 
					dlg_warp.addClass('wap_normal');
					break;
			}
			
			//对话框大小
			if (s.width) dlg_warp.css({'width': s.width});
			if (s.height) dlg_warp.css({'height': s.height});
			dlg_mark.width($(document).width());
			dlg_mark.height($(document).height());
			
			//字体位置
			if (s.textalign) {
				dlg_body.css({'text-align':s.textalign});
			}
			
			//对话框位置
			if (s.position == 'center') {
				if (!parseInt(s.height)) {
					dlg_warp.css({'left': (document.documentElement.clientWidth - parseInt(s.width)) / 2, 
						'top': '100px'});
				} else {
					dlg_warp.css({'left': (document.documentElement.clientWidth - parseInt(s.width)) / 2, 
						'top': document.documentElement.scrollTop+(document.documentElement.clientHeight - parseInt(s.height)) / 2});
				}
			}
			
			//拖动事件
			s.xlimited = {
				min: 10,
				max: $(document).width() - parseInt(s.width) - 10
			};//x移动范围
			s.ylimited = {
				min: 10,
				max: $(document).height() - parseInt(s.height) - 10
			};//y移动范围
			var dlg = {
				mousemove: function(e){
					var d = e.data;
					//d.pageX 原来   e.pageX 移动后
					var l = Math.min(Math.max(e.pageX - d.pageX + d.left, s.xlimited.min), s.xlimited.max);
					var t = Math.min(Math.max(e.pageY - d.pageY + d.top, s.ylimited.min), s.ylimited.max);
					
					dlg_warp.css({
						'left': l,
						'top': t
					});
					//onMoving
					//...
				},
				mouseup: function(e){
					dlg_warp.removeClass('jdDialog_hover');
					//onChanged;
					
					$(this).unbind('mousemove', dlg.mousemove).unbind('mouseup', dlg.mouseup);
				}
			};
			if (s.moveable) {
				//bind events
				dlg_warp.bind('mousedown', function(e){
					var d = {
						left: parseInt(dlg_warp.css('left')),
						top: parseInt(dlg_warp.css('top')),
						pageX: e.pageX,
						pageY: e.pageY
					};
					$(this).addClass('jdDialog_hover');
					$(this).bind('mousemove', d, dlg.mousemove).bind('mouseup', d, dlg.mouseup);
				});
			}
		
			//按键响应 
			if (s.buttons){
				var funConfirm,funCancle;
				$.each(s.buttons, function(name, fn){  //获取用户自定义函数
					if (name.replace(/\s*/g,'').search(/确认|确定|ok|confirm/gi) != -1)
					{
						funConfirm = fn;
					} else if (name.replace(/\s*/g,'').search(/取消|cancle/gi) != -1) {
						funCancle = fn;
					}
				});
				//var requiresShiftAlt = $.browser.mozilla;
				var accessKeysHighlighted = false;
				// 绑定用户自定义函数
				$(document).keydown(function(e) { //e.ctrlKey,e.shiftKey,e.altKey
					/*if (!accessKeysHighlighted && ( //组合键alt+shift
							(e.keyCode == 18 && !requiresShiftAlt) ||
							(e.keyCode == 16 && e.altKey && requiresShiftAlt) ||
							(e.keyCode == 18 && e.shiftKey && requiresShiftAlt))) {
						// Highlight all the access keys
						accessKeysHighlighted = true;
					} else */
					if (e.keyCode == 13 && funConfirm) { //回车键
						funConfirm();
/*					} else if (e.keyCode == 32 && funConfirm) { //空格
						funConfirm();*/
					} else if (e.keyCode == 27 && funCancle) { //ESC
						funCancle();
					}
				}).keyup(function(e) {
					if (accessKeysHighlighted) {
						accessKeysHighlighted = false;
					}
				});
			}
			
			//close x 事件
			dlg_close.click(function(){
				closeit();
			});
			
			
			//先隐藏 调用show显示
			dlg_warp.hide();
			dlg_mark.hide();
						
			//部分进行缓存
			if (s.cache) {
				if ($('#cache' + s.cache).is('div') == false) {//开始缓存	 cache容器不存在的话 要加一个
					//alert('开始缓存');
					cache_warp = $('<div id="cache' + s.cache + '" class="jdDialog_cache"></div>');
					
					cache_warp.append(dlg_warp);
					//遮罩 除了模态对话框，其他不显示遮罩
					if (s.modal) {
					cache_warp.append(dlg_mark);
					}
					$(document.body).append(cache_warp);
				} else {
					//用已有的
					dlg_warp = $('.jdDialog_warp');
					dlg_mark = $('.jdDialog_mark');
				}
			} else {
//				$(".jdDialog_cache").remove();
//				$(".jdDialog_warp").remove();
//				$(".jdDialog_mark").remove();
				
				//遮罩 除了模态对话框，其他不显示遮罩
				if (s.modal) {
					$(document.body).append(dlg_mark);
				}
				$(document.body).append(dlg_warp);

			}			
			
			//dlg_warp加到document.body之后，才可以设置下面的
			var setWarp = function(){
				var border_width = (dlg_warp.get(0).offsetWidth-dlg_warp.get(0).clientWidth)/2;
				var warp_width = dlg_warp.get(0).offsetWidth;
				var warp_height = dlg_warp.get(0).offsetHeight;
				var head_width = dlg_head.get(0).offsetHeight;
				var head_height = dlg_head.get(0).offsetHeight;
				
				//设置dlg_body高度 scroll
				if (s.width) dlg_body.css({'width': parseInt(warp_width-border_width*2)+'px'});
				//var h = dlg_head.offset().top;
				if (s.height) dlg_body.css({'height': parseInt(warp_height-head_height-border_width*2)+'px'});	
				
				//是否在标题前显示icon
				if (s.showicon) {
					dlg_head_icon.show();
				}else {
					dlg_head_icon.hide();
				}
				
				//ajax
				if (content_type=='ajax' && content_url) {
	//				$.get(content_url, function(data){
	//					dlg_content.html(data);
	//				});
					var strloading = $('<div id="loading">正在载入..</div>');
					//预先显示正在载入
					dlg_content.prepend(strloading);
					$.ajax({
						url:content_url, 
						type:'get', 
						dataType:'text', 
						error:function(XMLHttpRequest ,textStatus, errorThrown){dlg_content.html('获取内容失败');},
						success:function(data){
							dlg_content.css({'width': parseInt(warp_width-border_width*2)+'px'});
							dlg_content.css({'height': parseInt(warp_height-head_height-border_width*2)+'px'});
							dlg_content.prepend(data);							
							strloading.remove();
						}
					});
					dlg_warp.addClass('wap_ajax');
				}
				//iframe
				if (content_type=='iframe' && content_url) {
					$.get(content_url, function(data){
						var f = $('<iframe class="jdDialog_container" frameborder="0" marginwidth="0" marginheight="0" src="'+content_url+'"></iframe>');
						dlg_content.empty().append(f);
						f.css({'width': parseInt(warp_width-border_width*2)+'px'});
						f.css({'height': parseInt(warp_height-head_height-border_width*2)+'px'});
						dlg_content.css({'width': 'auto'});
						dlg_content.css({'height': 'auto'});
					});
					//预先显示正在载入
					dlg_content.html('正在载入..');
					dlg_warp.addClass('wap_iframe');
				}
				
							
				//显示超出屏幕宽度
				var width = parseFloat((dlg_body.attr('width')==undefined) ? '100' : parseFloat(dlg_body.attr('width')));
				var screenWidth = window.screen.width;
				if (s.top) {
					dlg_warp.css({'top': s.top});
				}
				if (s.left) {
					if (width+s.left>screenWidth) {
						dlg_warp.css({'left': (s.left-100)+'px'});
					} else {
						dlg_warp.css({'left': s.left});
					}
				}
				
			}

			//public
			var show = function(){
				try {
					if (s.onShow()) {
						s.onShow();
					}
					if (s.autoclose) {
						setTimeout(closeit, 1000);
					}
					if (s.modal) {
						dlg_mark.show();
					}
					dlg_warp.fadeIn('fast');
					setWarp();
				} 
				catch (e) {
					alert(e.message);
				}
			}
			var hide = function(){
				try {
					if (s.onHide()) {
						s.onHide();
					}
					if (s.modal) {
						dlg_mark.hide();
					}
					dlg_warp.fadeOut();
				} 
				catch (e) {
					alert(e.message);
				}
			}
			var closeit = function(){
				try {
					if (s.onClose()) {
						s.onClose();
					}
					dlg_warp.fadeOut(300);
					hide();
					setTimeout(function(){
						dlg_warp.remove();
						dlg_mark.remove();
						if (cache_warp) {
							cache_warp.remove();
						}
					}, 300);
					$(document).unbind();
				} 
				catch (e) {
					alert(e.message);
				}
			}
			
		} 
		catch (e) {
			//alert(e.message);
		}
		//
		return {
			dlg_warp: dlg_warp,
			closeit: closeit, 
			hide: hide, 
			show: show
		};
	}
	/*	show =  function(speed, callback){
	 return dlg_warp.show(speed, callback);
	 }
	 hide =  function(speed, callback){
	 return dlg_warp.hide(speed, callback);
	 }*/
	$.fn.jdDialog = function(setting){
		try {
			var newobj = new _init(setting, this);
		} 
		catch (e) {
			alert(e.message);
		}
		//return dlg_warp;
		//return public methods
		return {
			p: newobj.dlg_warp,
			close: newobj.closeit, 
			hide: newobj.hide, 
			show: newobj.show
		};
	}
	
//	jQuery.extend({
//		jdDialog: function(setting) {
//			alert(this);
//		}
//	});
//	相当于
//	$.jdDialog = function(setting) {
//		return $.fn.jdDialog(setting);
//	}	
	$.jdDialog = function(setting) {
		try {
			var newobj = new _init(setting);
		} 
		catch (e) {
			alert(e.message);
		}
		//return dlg_warp;
		//return public methods
		return {
			p: newobj.dlg_warp,
			close: newobj.closeit, 
			hide: newobj.hide, 
			show: newobj.show
		};
	}

	$.fn.jdDialog.defaults = {
		title: '标题',
		content: null, //对话框内容 content:{ type:'ajax',url:'...' }
		width: '370px', //'500px'
		height: '110px', //'350px'
		left: null, //若是由hover事件来获取该位置，可设置其值为e.pageX，其中e来自hover(e){...}；也可以用某标签的位置做定位$('#id').offset().left,
		top: null,
		opacity: '0.1',
		position: 'center', //center
		textalign: 'left', //left,center,right
		type: 'normal', //normal,tip（不含标题、不带按钮）,helpup,helpdown,loading,loading2,small（不含标题、带按钮）,auto（自动宽高）
		showicon: 'true',
		modal: false, //是否模态对话框
		moveable: false, //是否可以移动
		autoclose: false, //自动关闭
		cache: null, //字符串.缓存id  例如：'0493' 系统会生成id="cache0943"的容器来存放对话框 随机数：Math.round(Math.random()*10000)
		buttons: null, //取名"确认"、"OK"、"confirm"时，系统会自动为该按钮绑定Enter键响应； //取名"取消"、"Cancle"时，系统会自动为该按钮绑定Esc键响应
		onShow: function(){
		},
		onHide: function(){
		},
		onClose: function(){
		}
	};
	
})(jQuery);
