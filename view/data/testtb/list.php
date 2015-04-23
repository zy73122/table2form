<?php include $this->gettpl('header'); ?>
<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con">
                <form action="<?php echo U('testtb/list_index')?>" method="get">
                    <div class="th">
                        <div class="form">
                            <input type="button" class="btn4" value="新增" onClick="location.href='<?php echo U('testtb/edit')?>'" />

                            <!--列表头部html数据--><!--905--><!--该代码由系统自动生成-->
                            <input type="text" id="username" value="<?php echo $_GET['username']?>" onclick="this.value=''" onblur="(this.value == '') ? this.value='快速搜索用户名':''" onmouseover="this.select()" value="快速搜索用户名" class="textinput gray9 w270" />
                            <input type="text" id="nickname" value="<?php echo $_GET['nickname']?>" onclick="this.value=''" onblur="(this.value == '') ? this.value='快速搜索昵称':''" onmouseover="this.select()" value="快速搜索昵称" class="textinput gray9 w270" />
                            <select name="帐号状态，=0为禁用，=1为正常，默认为=1" id="status">
                                <option>--请选择帐号状态，=0为禁用，=1为正常，默认为=1--</option>
                                <option value="1" <?php if($_GET["status"]==1) { ?>selected="selected"<?php } ?>>1</option><option value="0" <?php if($_GET["status"]==0) { ?>selected="selected"<?php } ?>>0</option>
                            </select>&nbsp;

                            <input type="submit" class="formbtn" value="搜索">
                            <!--列表头部html数据 end-->

                        </div>
                    </div>
                </form>

                <form action="<?php echo U('testtb/list_submit')?>" method="post">
                    <div class="table">
                                                <!--模型list数据--><!--906--><!--该代码由系统自动生成-->
                        <table class="admin-tb" id="tb1">
                            <tr>
                                <th width="41" class="text-center"><input type="checkbox" rel="control" onClick="this.checked?selCheckbox.Init('tb1','del',1):selCheckbox.Init('tb1','del',2);" /></th>
                                <th>uid</th><th>用户名</th><th>昵称</th><th>金币</th><th>邮件地址，取回密码用</th><th>是否游客，=1时为游客</th><th>经验值</th><th>用户等级</th><th>当前头像ID</th><th>激活的头像列表</th><th>上一次登录时间</th><th>上一次登录ip</th><th>登录次数</th><th>帐号状态，=0为禁用，=1为正常，默认为=1</th>
                                <th>操作</th>
                            </tr>
                            <?php if($data) { ?>
                            <?php foreach((array)$data as $k => $v) {?>
                            <tr>
                                <td class="text-center"><input name="id[]" type="checkbox" id="id[]" value="<?php echo $v["uid"]?>" rel="del"  /></td>
                                <td><?php echo $v["uid"]?></td><td><?php echo $v["username"]?></td><td><?php echo $v["nickname"]?></td><td><?php echo $v["goldenmoney"]?></td><td><?php echo $v["email"]?></td><td><input type="checkbox" name="isguest" id="<?php echo $v["uid"]?>" class="btnSet" <?php if($v["isguest"]=='1' || $v["isguest"]=='是') { ?>checked="checked"<?php } ?> /></td><td><?php echo $v["exp"]?></td><td><?php echo $v["lev"]?></td><td><?php echo $v["avatar_id"]?></td><td><?php echo $v["avatar_active"]?></td><td><?php if($v["lastlogintime"]) { ?><?php echo date("Y-m-d H:i:s", $v["lastlogintime"])?><?php } ?></td><td><?php echo $v["lastloginip"]?></td><td><?php echo $v["logincount"]?></td><td><input type="checkbox" name="status" id="<?php echo $v["uid"]?>" class="btnSet" <?php if($v["status"]=='1' || $v["status"]=='是') { ?>checked="checked"<?php } ?> /></td>
                                <td><a href="<?php echo U('testtb/edit')?>?id=<?php echo $v["uid"]?>">编辑</a></td>
                            </tr>
                            <?php }?>
                            <?php } ?>
                            <tr class="foot-ctrl">
                                <td colspan="14+2" class="gray">选择: <a href="#" onClick="selCheckbox.Init('tb1','del',1);">全选</a> - <a href="#" onClick="selCheckbox.Init('tb1','del',3);">反选</a> - <a href="#" onClick="selCheckbox.Init('tb1','del',2);">无</a></td>
                            </tr>
                        </table>
                        <!--模型list数据 end-->

                        <div class="th">
                            <?php if($pages) { ?>
                            <div class="pages">
                                <?php if($pages['prev'] > -1) { ?>
                                <a href="<?php echo $page_url?>&start=<?php echo $pages['prev']?>">&laquo; 上一页</a>
                                <?php } else { ?>
                                <span class="nextprev">&laquo; 上一页</span>
                                <?php } ?>
                                <?php foreach((array)$pages as $k => $i) {?>
                                    <?php if($k != 'prev' && $k != 'next') { ?>
                                        <?php if($k == 'omitf' || $k == 'omita') { ?>
                                        <span>…</span>
                                        <?php } else { ?>
                                            <?php if($i > -1) { ?>
                                            <a href="<?php echo $page_url?>&start=<?php echo $i?>"><?php echo $k?></a>
                                            <?php } else { ?>
                                            <span class="current"><?php echo $k?></span>
                                            <?php } ?>
                                        <?php } ?>
                                    <?php } ?>
                                <?php }?>
                                <?php if($pages['next'] > -1) { ?>
                                <a href="<?php echo $page_url?>&start=<?php echo $pages['next']?>">下一页 &raquo;</a>
                                <?php } else { ?>
                                <span class="nextprev">下一页 &raquo;</span>
                                <?php } ?>
                            </div>
                            <?php } ?><!--/ pages-->
                            <div class="form">
                                <input type="radio" name="action" value="delete">
                                删除
                                <input type="radio" name="action" value="order" checked>
                                排序&nbsp;&nbsp;&nbsp;
                                <input type="submit" class="formbtn" value="提交修改">

                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <!--/ con-->

        </div>
    </div>
    <!--/ container-->

</div>
<!--/ wrap-->
<!--全选、取消-->
<script type="text/javascript">$(function(){selCheckbox.Init('tb1','del');});</script>
<script type="text/javascript" src="<?php echo URL?>static/js/sel_checkbox.js"></script>
<!--全选、取消end-->

<!--列表头部js数据--><!--904--><!--该代码由系统自动生成-->
<!--autocomplete-->
<script type="text/javascript">
    $(function() {
        $("#username").autocomplete({
            minLength: 1,
            //source: "<?php echo U('testtb/ajax_search')?>",
            source: function( request, response ) {
                $.ajax({
                    url: "<?php echo U('testtb/ajax_search')?>",
                    dataType: "json",
                    data: {
                        term: request.term,
                        fieldname: 'username'
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            extraParams: { a: "1", b: "2", c: "3"},
            focus: function(event, ui) {
                $('#username').val(ui.item.value);
            },
            select: function(event, ui) {
                var message = ui.item ? ui.item.value : null;
                if (message) {
                    self.location.href = '<?php echo U('testtb/index?username=')?>'+encodeURI(message);
                }
            }
        })._renderItem = function( ul, item ) {
            //搜索结果中增加图像显示
            $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.value + "</a>" )
                .appendTo( ul );
        };
    });
</script>
<!--autocomplete end--><!--autocomplete-->
<script type="text/javascript">
    $(function() {
        $("#nickname").autocomplete({
            minLength: 1,
            //source: "<?php echo U('testtb/ajax_search')?>",
            source: function( request, response ) {
                $.ajax({
                    url: "<?php echo U('testtb/ajax_search')?>",
                    dataType: "json",
                    data: {
                        term: request.term,
                        fieldname: 'nickname'
                    },
                    success: function( data ) {
                        response( data );
                    }
                });
            },
            extraParams: { a: "1", b: "2", c: "3"},
            focus: function(event, ui) {
                $('#nickname').val(ui.item.value);
            },
            select: function(event, ui) {
                var message = ui.item ? ui.item.value : null;
                if (message) {
                    self.location.href = '<?php echo U('testtb/index?nickname=')?>'+encodeURI(message);
                }
            }
        })._renderItem = function( ul, item ) {
            //搜索结果中增加图像显示
            $( "<li></li>" )
                .data( "item.autocomplete", item )
                .append( "<a>" + item.value + "</a>" )
                .appendTo( ul );
        };
    });
</script>
<!--autocomplete end--><!--status列表改变动作-->
<script type="text/javascript">
$(function() {
    $('#status').change(function(){
        self.location.href='<?php echo U("testtb/list_index")?>?status='+$(this).val();
    });
});
</script>
<!--status列表改变动作 end-->
<!--列表头部js数据 end-->

<!--ajax设置checkbox-->
<script type="text/javascript">
    $(function() {
        $('.btnSet').click(function(){
            var id = $(this).attr('id');
            var setwhat = $(this).attr('name');
            var val = $(this).get(0).checked==true ? 1 : 0;
            var d = $.jdDialog({'type':'loading3'});d.show();
            $.post('<?php echo U('testtb/ajax_set_value')?>', "id="+id+"&val="+val+"&setwhat="+setwhat, function(data){
                //alert(data);
                d.hide();
            });
        });
    });
</script>
<!--ajax设置checkbox end-->

<?php include $this->gettpl('footer'); ?>