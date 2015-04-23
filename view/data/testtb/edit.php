<?php include $this->gettpl('header'); ?>

<div class="wrap">
    <div class="container">
        <div id="main">
            <div class="con box-green">
                <form action="<?php echo U('testtb/edit')?>" method="post" enctype="multipart/form-data">
                    <div class="box-header">
                        <h4><?php if($data['id'] ) { ?>编辑<?php } else { ?>添加<?php } ?>示例</h4>
                    </div>
                    <div class="box-content">

                        <!--模型edit数据--><!--900--><!--该代码由系统自动生成-->
                        <table class="table-font">
                            <tr>
                                <th class="w120">用户名：</th>
                                <td><input name="username" class="textinput w270" id="username" value="<?php echo $data["username"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">昵称：</th>
                                <td><input name="nickname" class="textinput w270" id="nickname" value="<?php echo $data["nickname"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">密码，md5加密值，32位：</th>
                                <td><input name="password" class="textinput w270" id="password" value="<?php echo $data["password"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">金币：</th>
                                <td><input name="goldenmoney" class="textinput w270" id="goldenmoney" value="<?php echo $data["goldenmoney"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">邮件地址，取回密码用：</th>
                                <td><input name="email" class="textinput w270" id="email" value="<?php echo $data["email"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">是否游客，=1时为游客：</th>
                                <td><input name="isguest" class="textinput w270" id="isguest" value="<?php echo $data["isguest"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">经验值：</th>
                                <td><input name="exp" class="textinput w270" id="exp" value="<?php echo $data["exp"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">用户等级：</th>
                                <td><input name="lev" class="textinput w270" id="lev" value="<?php echo $data["lev"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">当前头像ID：</th>
                                <td><input name="avatar_id" class="textinput w270" id="avatar_id" value="<?php echo $data["avatar_id"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">激活的头像列表：</th>
                                <td><input type="hidden" id="avatar_active" name="avatar_active" value="<?php echo $data['avatar_active']?>" style="display:none" /><input type="hidden" id="avatar_active___Config" value="CustomConfigurationsPath=/table2form/tools/fckeditor/mine.simple.config.js&amp;SkinPath=/table2form/tools/fckeditor/editor/skins/white/&amp;AutoDetectLanguage=false&amp;DefaultLanguage=zh-cn&amp;ToolbarStartExpanded=true" style="display:none" /><iframe id="avatar_active___Frame" src="/table2form/tools/fckeditor/editor/fckeditor.html?InstanceName=avatar_active&amp;Toolbar=Mine" width="660" height="300" frameborder="0" scrolling="no"></iframe></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">上一次登录时间：</th>
                                <td><input name="lastlogintime" class="textinput w270" id="lastlogintime" value="<?php if($data["lastlogintime"]) { ?><?php echo date("Y-m-d H:i:s", $data["lastlogintime"])?><?php } ?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">上一次登录ip：</th>
                                <td><input name="lastloginip" class="textinput w270" id="lastloginip" value="<?php echo $data["lastloginip"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">登录次数：</th>
                                <td><input name="logincount" class="textinput w270" id="logincount" value="<?php echo $data["logincount"]?>" /></td>
                            </tr>
                            <tr>
                                <th style="VERTICAL-ALIGN: top">帐号状态，=0为禁用，=1为正常，默认为=1：</th>
                                <td><input type="radio" name="status" id="status" value="1" <?php if($data["status"] !== null ) { ?><?php if($data["status"]=="1") { ?>checked<?php } ?><?php } else { ?>checked<?php } ?>>1 <input type="radio" name="status" id="status" value="0" <?php if($data["status"] !== null ) { ?><?php if($data["status"]=="0") { ?>checked<?php } ?><?php } else { ?><?php } ?>>0 </td>
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