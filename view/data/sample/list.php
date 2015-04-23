<?php include $this->gettpl('header'); ?>
<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con">
                <form action="<?php echo U('sample/list_index')?>" method="get">
                    <div class="th">
                        <div class="form">
                            <input type="button" class="btn4" value="新增" onClick="location.href='<?php echo U('sample/edit')?>'" />

                            <!--列表头部html数据--><!--905-->
                            &nbsp;
                            <select name="isguest" id="isguest">
                                <option value="">--请选择是否游客--</option>
                                <option value="">全部</option>
                                <option value="0">否</option>
                                <option value="1">是</option>
                            </select>
                            &nbsp;
                            <input type="text" name="nickname" id="nickname" onclick="this.value=''" onblur="(this.value == '') ? this.value='快速搜索用户名':''" onmouseover="this.select()" value="快速搜索用户名" class="textinput gray9 w270" />
                            &nbsp;
                            <input type="submit" class="formbtn" value="搜索">
                            <!--列表头部html数据 end-->

                        </div>
                    </div>
                </form>

                <form action="<?php echo U('sample/list_submit')?>" method="post">
                    <div class="table">
                        <!--模型list数据--><!--906-->
                        <table class="admin-tb" id="tb1">
                            <tr>
                                <th width="41" class="text-center"><input type="checkbox" rel="control" onClick="this.checked?selCheckbox.Init('tb1','del',1):selCheckbox.Init('tb1','del',2);" /></th>
                                <th width="100">排序</th>
                                <th width="150">名称</th>
                                <th>图标</th>
                                <th>操作</th>
                            </tr>
                            <?php if($data) { ?>
                            <?php foreach((array)$data as $k => $v) {?>
                            <tr> <!-- <tr class="checked">默认选中 -->
                                <td class="text-center"><input name="id[]" type="checkbox" id="id[]" value="<?php echo $v['id']?>" rel="del"  /></td>
                                <td><input name="orderby[<?php echo $v['id']?>]" type="text" id="orderby[<?php echo $v['id']?>]" class="textinput" value="<?php echo $v['displayorder']?>"></td>
                                <td><?php echo $v['nickname']?></td>
                                <td><?php if($v['img_thumb']) { ?><img src="../<?php echo $v['img_thumb']?>" /><?php } ?></td>
                                <td><a href="<?php echo U('sample/edit?id='.$v['id'])?>">编辑</a></td>
                            </tr>
                            <?php }?>
                            <?php } ?>
                            <tr class="foot-ctrl">
                                <td colspan="5" class="gray">选择: <a href="javascript:void(0)" onclick="selCheckbox.Init('tb1','del',1);">全选</a> - <a href="javascript:void(0)" onclick="selCheckbox.Init('tb1','del',3);">反选</a> - <a href="javascript:void(0)" onclick="selCheckbox.Init('tb1','del',2);">无</a></td>
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
<!--autocomplete-->
<script type="text/javascript" src="<?php echo URL?>static/js/m_autocomplete.js"></script>
<!--autocomplete end-->

<!--列表头部js数据--><!--904-->
<!--autocomplete-->
<script type="text/javascript">
    $(function() {
        m_autocomplete($('#nickname'), "<?php echo U('sample/ajax_search')?>");
    });
</script>
<!--autocomplete end-->
<!--列表头部js数据 end-->

<!--ajax设置checkbox-->
<script type="text/javascript">
    $(function() {
        $('.btnSet').click(function(){
            var id = $(this).attr('id');
            var setwhat = $(this).attr('name');
            var val = $(this).get(0).checked==true ? 1 : 0;
            var d = $.jdDialog({'type':'loading3'});d.show();
            $.post('<?php echo U('sample/ajax_set_value')?>', "id="+id+"&val="+val+"&setwhat="+setwhat, function(data){
                //alert(data);
                d.hide();
            });
        });
    });
</script>
<!--ajax设置checkbox end-->

<?php include $this->gettpl('footer'); ?>