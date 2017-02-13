
$(function()
{
    // -- Chat Message Card Back Change Of Random --
    var url = $("#btn_submit").data('url');
    // var post_url = "./postContent.php";
    var reconnection = null,reconnectionTime = 3.5 * 1000,i = 0,rand = 1,j = 0;
    var backs = new Array();
    backs[1] = ["./20160110120839_uvHsX.thumb.224_0.jpg","22%","94%","#E5E5E5"];
    backs[2] = ["./20150806175243_VKPXa.thumb.700_0.jpg","56%","29%","#282828"];
    backs[3] = ["./5025de8b77fb8_600x.jpg","50%","64%","#FFFFFF"];
    backs[4] = ["./5025de8c963a2_600x.jpg","70%","37%","#27408B"];

    checkLogin();

    // -- Sign Up And In Change --
    var info = new Array();
    info['in'] = "Sign In";
    info['up'] = "Sign Up";
    $("#change").on('click',function(){
        var leftObj = $(this).parent().find("button[id^='btn-']").children();
        var curr = leftObj.html().substr(-2);
        var flag = (curr == "In") ? "up" : "in";
        $("input[name='action']").val(flag);
        leftObj.html(info[flag]);
        $("#sign-"+flag).show('slow');
        $("#sign-"+curr.toLowerCase()).hide('slow');
    });

    // -- clear local--
    if(typeof $("input[name=loginedUser]").val() == 'undefined' || $("input[name=loginedUser]").val() == '')
    {
        localStorage.clear();
    }

    // -- Account Process --
    $("#btn-issue").on('click', function(e){

         // SignIN
         var action = $("input[name='action']").val();
         if(action == "in" || action == 'up')
         {
            $.ajax(
                {'type' : "POST", 'dataType' : 'text','url' : "./userProcess.php?action="+$("input[name='action']").val(), 'data' : {'data':$('#sign-'+action).serialize()},success : function(response)
                {
                    var d = JSON.parse(response);
                    console.log(d);
                    if(d.errorCode == 9) {
                      $("#isseu-out").modal();
                    }
                    else $(".worng-sign").html(d.message).css({'float':'left','padding-top':'7px'});
                    if(typeof d.userId !== 'undefined')
                    {
                        setTimeout("history.go(0)", 1000);
                    }
                }
                });
         }
    });

    // -- resignin --
    $("#resignin").on('click', function(){
        resignin();
    });

    // -- EnterKey Same as Submit -- 
    $('.chat_content').on('focus', function(e){
     $(document).on('keypress',function(e){
          if(e.keyCode == 13) $("#btn_submit").click();
     });
     $(this).on('blur', function(e){
         $(document).unbind('keypress');
     });
    });

    // // -- Use Session To Judge --
    // function checkLogin(){
    //   if(typeof $("input[name='loginedUser']").val() != 'undefined' && $("input[name='loginedUser']").val() != '')
    //   {
    //     // 刷新页面，session已有值，显示用户名称，将关键值保存至隐藏表单中，并显示object选择框
    //       $('#select_button').html('wating..').html("<span class='right-top-container'><span class='user-container'><i class='fa fa-user' aria-hidden='true'></i>&nbsp&nbsp<?php if(isset($_SESSION['uName'])){if(mb_strlen($_SESSION['uName'], 'utf-8') > 10){echo sprintf('%\'.-11s',mb_substr($_SESSION['uName'], 0, 10, 'UTF-8'));}else{echo $_SESSION['uName'];}}?></span><a href='./userProcess.php?action=out' class='sign-out'>&nbsp&nbsp&nbsp<i class='fa fa-sign-out' aria-hidden='true'></i></a></span>");
    //       $(".right-top-container").fadeIn(1200);
    //       $('#userId').val($("input[name='loginedUser']").val());
    //       $('#object_select').fadeIn(1200);
    //   }
    // }

    // -- When To Connect --
    //window.v= $("input[name=userId]:checked").val();
    function startGetMess()
    {
        window.self = $("input[name=loginedUser]").val();
        window.object = localStorage.getItem('objectId');
        if(self && object)
        {
        //$('#select_button').html("<i class='fa fa-user fa-3x' aria-hidden='true'></i>&nbsp<span style='font-size:22px;'>"+$('#user'+v).html()+"</span>");
        $.ajax({"type" : "POST","data" : {'objectId' : object},"url" : url,"dataType" : 'text',"success" : function(response){
          return;
        }});
        setTimeout(connect, 100);
        }
    }

    // -- Chat Message Submit --
    $("#btn_submit").on("click", function(e){
        if(!self || !object) alert("少人！");
        else
        {
          var content = $('.chat_content').val();
          if($.trim(content) != '')
          {
            $.ajax({"type" : "POST","data" : {'content' : content, 'userId' : window.self},"url" : url,"dataType" : 'text',"success" : function(response){
                    console.log(response);
                   if(response == 'succ')
                   {
                      $("#content_continer").append("<p style='text-align:right;'><span class='message_"+ j +"'></span></p>");
                      $(".message_"+j).html(content);
                      j++;
                      $('.chat_content').val("");
                   }
                }});
           }
           else alert("内容不能为空");
        }
    });

    // -- Tool-Tips-Set --
    $(".question-des").tooltip({html : true });

    // -- Search Object --
    $('#object-search').on('click',function(e){
      e.preventDefault()//阻止默认
      e.stopPropagation(); // 阻止冒泡
      var url = $(this).data('url');
      var objectName = $(this).parent().find("input[name=search_key]").val();
      $.ajax({"url" : url, "dataType" : "json", "type" : "POST","data" : {"objectName" : objectName}, "success" : function(response){
          console.log(response);
          if(response.result == 'fail') $('#object_select .error').html(response.message);
          else
          {
             $('#object_select .error').remove();
             $('#object_select').hide();
             $('.left-top-container').fadeIn(1200).find('.user-container-left').html("<i class='fa fa-user' aria-hidden='true'></i>&nbsp&nbsp<span>"+response.objectName+"</span><span class='object-close'>&times</span>");
             //$("#objectId").val(response.objectId);
             //$("#objectName").val(response.objectName);
             if(window.localStorage) {localStorage.setItem('objectId',response.objectId);localStorage.setItem('objectName',response.objectName);}
             startGetMess();
          }
      }});
    });

    startGetMess();
    // -- Get Object --
    if(typeof localStorage.getItem('objectId') != 'undefined' && localStorage.getItem('objectId') != null)
    {
        $('#object_select').css('display', 'none');
        $('.left-top-container').fadeIn(1200).find('.user-container-left').html("<i class='fa fa-user' aria-hidden='true'></i>&nbsp&nbsp<span>"+localStorage.objectName+"</span><span class='object-close'>&times</span>");
    }

    // -- When Sign Out --
    $('.sign-out').on('click',function(){
        localStorage.clear();
    });

    // -- objetc close --
    $(".object-close").on('click', function(){
        $('#object_select').fadeIn(1200);
        $('.left-top-container').hide();
        localStorage.clear();
    });

    // -- Es Connenct --
    function connect()
    {
        if(es) es.close();
        var es = new EventSource(url);
        
        es.onmessage = function(e)
        {
              if(reconnection) clearTimeout(reconnection);
              reconnection = setTimeout(connect, reconnectionTime);
              processd(e.data);
        }

        es.onerror = function(e){
            console.log(e);
            // 如果服务器端关闭了套接字(后端程序执行结束)，sse会调用自己的重连机制：
            // 1. 设置es对象的一个readyState属性，值为EventSource.CONNECTING
            // 2. 调用这个onerror处理
            // 3. 等待retry延长时间(谷歌是三秒，火狐五秒)，重连

            //如果不想使用它的重连机制，用自己的，close掉（比如后端每次只发一次数据后，程序停止，然后调用自己的重连机制，20秒重练一次）
            this.close();
        }
    }

    // -- Es Return Data Process --
    function processd(data,es)
    {
        try{var d = JSON.parse(data);}
        catch(exception){
            console.log(exception);
            return;
        }

        if(d) 
        {
            $("#content_continer").append("<p style='text-align:left;'><span class='_message"+ i +"'></span></p>");
            $("._message"+i).html(d.content);
            rand = Math.ceil(Math.random()*4);
            $("._message"+i).css({'background': "url("+backs[rand][0]+") no-repeat "+backs[rand][1]+" "+backs[rand][2], 'color' : backs[rand][3]});
            i++;
            $.ajax({"url" : url,'type' : 'POST','data' : {'change' : 1, 'id' : d.id},'dataType' : 'json',success : function(e){
                console.log(data);
                return;
            }});
        }
    }

    function resignin()
    {
        var url = "./userProcess.php?action=resignin";
        $.ajax({'url' : url,'dataType' : 'json','type' : 'POST',success : function(response){
            history.go(0);
        }});
    }

});
