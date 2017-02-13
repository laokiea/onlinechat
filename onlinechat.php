<!doctype html>
<head>
<meta charset="utf-8"/>
<title>在线聊天</title>
<!-- <link rel="stylesheet" href="http://cdn.bootcss.com/font-awesome/4.5.0/css/font-awesome.min.css"> -->
<!-- <link rel="stylesheet" href="http://cdn.bootcss.com/bootstrap/3.3.0/css/bootstrap.min.css">
<script src="http://cdn.bootcss.com/jquery/1.11.1/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.0/js/bootstrap.min.js"></script> -->
<!-- Latest compiled and minified CSS -->
<link href="http://libs.baidu.com/fontawesome/4.0.3/css/font-awesome.css" rel="stylesheet">
<link href="http://apps.bdimg.com/libs/bootstrap/3.2.0/css/bootstrap.min.css" rel="stylesheet">
<script src="http://lib.sinaapp.com/js/jquery/1.9.1/jquery-1.9.1.min.js"></script>
<script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>

<style>
*{margin: 0;padding: 0;font-family: Arial, Helvetica, sans-serif,'宋体';}
html,body{overflow:hidden;width: 100%;height:100%;background: url("./GJDL_2.jpg") no-repeat;background-position: center 90%;background-size: cover;}
.header{height: 90px;line-height: 90px;letter-spacing: -2px;color: #FF7846;font-family: '微软雅黑';font-size:70px;display: block;text-align: center;margin: 1% 0 2% 0;}
p{margin: 0;}
#chat_table{width: 600px;height: 400px;margin: 0px auto;margin-top: 40px;box-shadow: 0px 0px 1px #A4D3EE;border-radius: 5px;background: url("./GJDL_3.jpg") no-repeat;background-position: center;background-size: cover;position: relative;}
#chat_form{width: 100%;height: 100%;position:relative;}
#content_continer{width: 100%;height: 350px;overflow-y: scroll;padding-top: 8px;}
input.chat_content{width: 100%;height:50px;position:absolute;top: 350px;border:1px solid #EAEAEA;font-size:18px;}
#btn_submit{position: absolute;width: 50px;height: 30px;top: 360px;left: 530px;}
span[class^="_message"],span[class^="message_"]{color:    #282828;min-width:80px;text-align: center;letter-spacing:1px;display: inline-block;height: 40px;line-height: 40px;margin: 7px 14px 7px 15px;padding: 0px 20px; border-radius: 6px;border: 1px solid #dddddd;
background: url("20150806175243_VKPXa.thumb.700_0.jpg") no-repeat;background-position : 56% 29%;font-size:16px;}
.modal-head-title{letter-spacing: -2px;color: #FF7846;font-family: '微软雅黑';font-size:45px;}
#change{cursor: pointer;color:#FF7846;}
button[id^="btn-"]{margin: 0 10px 0 0;background: #FF7846;border:none;font-size: 13px;color: #ffffff;}
span[class^='worng-']{font-size: 13px;color: #FF7846;}
a[class='sign-out']{color:#FF7846;text-decoration: none;font-size: 16px;}
#object_select{width:320px;margin: 0 auto;display: none;}
.right-top-container{position:fixed;top:40px;left:88%;display:none;}
.left-top-container{display: none;text-align: center;}
.user-container,.user-container-left{background-color:#FF7846;border:none;font-family: 微软雅黑;color:  #fff;display:inline-block;font-size:15px;min-width:80px;height:30px;line-height:27px;border-radius:20px;padding:0 15px 0 15px;}
.user-container-left{background-color:#65BD79;position: relative;}
.tips-cont{display: inline-block;width: 52px;height: 32px;font-size: 12px;}
.question-des{position: absolute;top: 0;left: 205px;top:6px;cursor: pointer;}
#object-search{height :16px;padding: 1px 0 0 8px;line-height:16px;cursor: pointer;position: absolute;left: 157px;top:9px;color: #666;font-size: 12px;border-left: solid 1px #999;}
.error{display: inline-block;position: absolute;top: 40px; left: 20px;width: 160px;color: #E80F0F;font-size: 13px;}
.resignin-title{font-size: 14px;}
.resignin-tip{width: 100%;height: 70px;line-height: 70px;font-size: 15px;}
.object-close{position: absolute;left: 90%;top: -6px;background-color: #65BD79;border-radius: 20px;width: 15px;height: 15px;line-height: 15px;cursor: pointer;}
.object-close:hover{transform: rotate(90deg) scale(1.3);-moz-transform:rotate(90deg) scale(1.3);transform-origin: (left,top)}
</style>
</head>
<body>
<?php 
session_start();
if(isset($_SESSION['userId']))
{
    echo "<input type='hidden' name='loginedUser' value={$_SESSION['userId']}>";
}
?>

<span class="text-center header">!chat</span>

<!-- modal for sign up or in -->
<div class="modal fade" id="user_select" tabindex="-1" role="dialog" >
<div class="modal-dialog modal-sm">
<div class="modal-content">

<div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span>&times</span></button>
<h4 class="text-center modal-head-title">!chat</h4>
</div>

<div class="modal-body">
    <form role="form" id="sign-in">
   <!--  <div class="radio"><label><input type="radio" name="userId" value='1'/> <span id="user1">用户1</span></label></div>
    <div class="radio"><label><input type="radio" name="userId" value='2'/> <span id="user2">用户2</span></label></div> -->
    <div class="from-group form-cell" style="margin: 0 0 10px 0;">
    <input type="input" name="uName" class="form-control" placeholder="username" />
    </div>
    <div class="from-group form-cell">
    <input name="uPass" type='password' class="form-control" placeholder="password" />
    </div>
    </form>

    <form role="form" id="sign-up" style="display: none;">
    <!--  <div class="radio"><label><input type="radio" name="userId" value='1'/> <span id="user1">用户1</span></label></div>
    <div class="radio"><label><input type="radio" name="userId" value='2'/> <span id="user2">用户2</span></label></div> -->
    <div class="from-group form-cell" style="margin: 0 0 10px 0;">
    <input type="input" name="uName" class="form-control" placeholder="new user" />
    </div>
    <div class="from-group form-cell">
    <input name="uPass" type='password' class="form-control" placeholder="new pass" />
    </div>
    </form>
    
</div>

<div class='modal-footer'>
    <span class='worng-sign'></span>
    <button class='btn btn-default btn-small' type='button' id="btn-issue"><span>Sign In</span></button>
    <span id="change"><i class="fa fa-exchange" aria-hidden="true"></i></span>
    <input name="action" type="hidden" value="in"/>
</div>

</div>
</div>
</div>

<!-- modal for user login out -->
<div class="modal fade" id="isseu-out">
<div class='modal-dialog modal-sm'>
<div class="modal-content">
<!-- <div class="modal-header">
<button type="button" class="close" data-dismiss="modal"><span>&times</span></button>
<h4 class="text-left resignin-title">有话说</h4>
</div> -->

<div class="modal-body">
   <p class='resignin-tip'>您已在别处登陆，是否注销，重新登陆?</p>
</div>
<div class='modal-footer'>
    <button class='btn btn-default btn-small' type='button' id="resignin" style="background: #FF7846;color: #fff;"><span>OK</span></button>
    <button class='btn btn-default btn-small' type='button' data-dismiss="modal"><span>Cancel</span></button>
</div>
</div>
</div>
</div>

<!--触发按钮-->
<p class="text-center" id="select_button"><button id='tooltip' type="button" class='btn btn-small btn-default' data-toggle='modal' data-toggle='tooltip' data-placement='top' title='start' data-target='#user_select'><span>start</span></button></p>

<!-- object container -->
<div class='left-top-container'>
<span class='user-container-left'>
</span>
</div>

<!-- search-->
<div id="object_select">
<form role='form' class="form-horizontal">
<div class='form-group has-feedback'>
 <label class='col-sm-5 control-label' style='padding-right: 0;'>!Chat With:</label>
 <div class='col-sm-7' style="position: relative;">
  <input class='form-control' type='input' name='search_key' placeholder="选择一个聊天对象"> 
  <span class='question-des' data-toggle='tooltip' data-placement='right' title="<span class='tips-cont'>输入昵称以搜索</span>"><i class="fa fa-question-circle" aria-hidden="true"></i>
  </span>
  <a id="object-search" data-url='./userProcess.php?action=search'><span class="glyphicon glyphicon-search"></span></a>
  <span class='error'></span>
 </div>
</div>
</form>
</div>

<div id="chat_table">
  <div id="chat_form">
    <input type="text" class="chat_content"/>
    <button class="btn btn-small btn-link text-center" type="button" id="btn_submit" data-url="./chatMessProcess.php">发送</button>
      <input type="hidden" name="lastId" id="lastId"/>
      <!-- <input type="hidden" name="userId" id="userId"/> -->
      <div id="content_continer"><!--  --></div>
  </div>
</div>
</body>
<script>
   // -- Use Session To Judge --
    function checkLogin(){
      if(typeof $("input[name='loginedUser']").val() != 'undefined' && $("input[name='loginedUser']").val() != '')
      {
        // Ë¢ÐÂÒ³Ãæ£¬sessionÒÑÓÐÖµ£¬ÏÔÊ¾ÓÃ»§Ãû³Æ£¬½«¹Ø¼üÖµ±£´æÖÁÒþ²Ø±íµ¥ÖÐ£¬²¢ÏÔÊ¾objectÑ¡Ôñ¿ò
          $('#select_button').html('wating..').html("<span class='right-top-container'><span class='user-container'><i class='fa fa-user' aria-hidden='true'></i>&nbsp&nbsp<?php if(isset($_SESSION['uName'])){if(mb_strlen($_SESSION['uName'], 'utf-8') > 10){echo sprintf('%\'.-11s',mb_substr($_SESSION['uName'], 0, 10, 'UTF-8'));}else{echo $_SESSION['uName'];}}?></span><a href='./userProcess.php?action=out' class='sign-out'>&nbsp&nbsp&nbsp<i class='fa fa-sign-out' aria-hidden='true'></i></a></span>");
          $(".right-top-container").fadeIn(1200);
          $('#userId').val($("input[name='loginedUser']").val());
          $('#object_select').fadeIn(1200);
      }
    }
</script>
<script src="http://guitoo.cc/onlinechat/com_onlinechat.js"></script>
</html>