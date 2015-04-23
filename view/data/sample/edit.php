<?php include $this->gettpl('header'); ?>

<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con box-green">
                <form action="<?php echo U('sample/edit')?>" method="post" enctype="multipart/form-data">
                    <div class="box-header">
                        <h4><?php if($data['id'] ) { ?>编辑<?php } else { ?>添加<?php } ?>示例</h4>
                    </div>
                    <div class="box-content">

                        <!--模型edit数据--><!--900-->
                        <table class="table-font">
                            <tr>
                                <th class="w120">示例名称：</th>
                                <td><input name="nickname" type="text" class="textinput w270" id="nickname" value="<?php echo $data['nickname']?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">描述：</th>
                                <td><textarea name="remark" rows="5" class="textinput w270" id="remark"><?php echo $data['remark']?></textarea></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">图标：</th>
                                <td><input name="upimage[]" type="file" id="upimage"> <input name="removeico" type="radio" value="yes">去掉图标<br>
                                    <?php if($data['img_url']) { ?><img src="../<?php echo $data['img_url']?>" width="150" /><?php } ?>
                                </td>
                            </tr>
                        </table>
                        <!--模型edit数据 end-->

                    </div>
                    <div class="box-footer">
                        <div class="box-footer-inner">
                            <input type="submit" class="formbtn" value="提交" />
                            <input type="button" class="formbtn" value="返回" onclick="javascript:history.go(-1)" />
                            <?php if($_REQUEST['id']) { ?>
                            <input name="id" type="hidden" id="id" value="<?php echo $_REQUEST['id']?>">
                            <?php } ?>
                            <?php if($_REQUEST['parent']) { ?>
                            <input name="parent" type="hidden" id="parent" value="<?php echo $_REQUEST['parent']?>">
                            <?php } elseif($data['parent']) { ?>
                            <input name="parent" type="hidden" id="parent" value="<?php echo $data['parent']?>">
                            <?php } ?>
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

<?php include $this->gettpl('footer'); ?>