<div id='calendar'></div>
<link href="/static/css/tokeninput.css" rel="stylesheet" />
<script src="/static/js/tokeninput.js"></script>
<style>
.table-condensed{font-size: 14px;}
</style>

<!-- add Modal -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
      <h4 class="modal-title">请输入新事件名称</h4>
    </div>

    <div class="alert" style="display:none;">
        <h4>Notice!</h4>
        <p></p>
    </div>

    <div class="modal-body">
        <form class="form-horizontal" id="myForm" role="form">
          <input type="hidden" class="form-control form_datetime" id="inputId" name="id">
          <div class="form-group">
            <label for="inputTitle" class="col-lg-2 control-label">事件名称</label>
            <div class="col-lg-10">
                <textarea class="form-control" rows="3" id="inputTitle" name="title" placeholder="输入事件名称"></textarea>
            </div>
          </div>
          <div class="form-group">
            <label for="inputStart" class="col-lg-2 control-label">开始时间</label>
            <div class="col-lg-5">
              <input type="text" class="form-control form_datetime" id="inputStart" name="start">
            </div>
          </div>
          <div class="form-group">
            <label for="inputEnd" class="col-lg-2 control-label">结束时间</label>
            <div class="col-lg-5">
              <input type="text" class="form-control form_datetime" id="inputEnd" name="end">
            </div>
          </div>
          <div class="form-group">
            <label for="inputColor" class="col-lg-2 control-label">日历颜色</label>
            <div class="col-lg-3">
                <select class="form-control" id="selectColor" name="color">
                    <option>请选择</option>
                    <option value="#d9534f">红色</option>
                    <option value="#f0ad4e">橙色</option>
                    <option value="#5cb85c">绿色</option>
                    <option value="#428bca">蓝色</option>
                    <option value="#463265">紫色</option>
                    <option value="#cccccc">灰色</option>
                </select>
            </div>
          </div>
        </form>
        <p></p>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-danger pull-left" id="time_delete">删除该事件</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
      <button type="button" class="btn btn-primary" id="btn_submit"> 提 交 </button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<!-- meering Modal -->
<div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="modal-title"></h4>
        </div>

        <div class="alert" style="display:none;">
          <h4>Notice!</h4>
          <p></p>
        </div>

        <div class="modal-body" id="modal-body">
          <form id="meetingForm" class="form-horizontal" role="form" action="" method="post">

            <div class="form-group">
            <label for="inputUsername" class="col-lg-2 control-label">会议主题</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="title" name="title" placeholder="会议主题">
            </div>
            </div>

            <div class="form-group">
            <label for="inputDepartment" class="col-lg-2 control-label">参加人员</label>
            <div class="col-lg-10"  id="inputUserBox">
              <input type="text" class="form-control" id="inputUser" name="inputUser" placeholder="参加人员">
            </div>
            </div>

            <div class="form-group">
              <label for="inputStart" class="col-lg-2 control-label">开始时间</label>
              <div class="col-lg-5">
                <input type="text" class="form-control form_datetime" name="start" id="start">
              </div>
            </div>
            <div class="form-group">
              <label for="inputEnd" class="col-lg-2 control-label">结束时间</label>
              <div class="col-lg-5">
                <input type="text" class="form-control form_datetime" name="end" id="end">
              </div>
            </div>

            <div class="form-group">
            <label for="inputDepartment" class="col-lg-2 control-label">备注</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="inputMemo" name="inputMemo" placeholder="备注">
            </div>
            </div>

            <input type="hidden" id="room_id" name="room_id">
            <input type="hidden" id="book_id" name="book_id">
          </form>
          <p class="modal-message"></p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left Gate_book_delete"> 删除预定 </button>
          <button type="button" class="btn btn-default" data-dismiss="modal"> 关 闭 </button>
          <button type="button" class="btn btn-primary" id="meeting_submit"> 提 交 </button> 
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script>
	$(document).ready(function() {

		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		var calendar = $('#calendar').fullCalendar({
            //theme: true, 
            defaultView: 'agendaWeek',
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			monthNames: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],  
			monthNamesShort: ["一月", "二月", "三月", "四月", "五月", "六月", "七月", "八月", "九月", "十月", "十一月", "十二月"],  
			dayNames: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],  
			dayNamesShort: ["周日", "周一", "周二", "周三", "周四", "周五", "周六"],  
			today: ["今天"],  
			firstDay: 0,  
			buttonText: {  
			    today: '今天',  
			    month: '月',  
			    week: '周',  
			    day: '日',  
			}, 
            //防止重叠
            slotEventOverlap:false,
			selectable: true,
			selectHelper: true,
			select: function(start, end, allDay) {
                $(".btn-danger").hide();
                var isEdit = false;
                $("#myForm")[0].reset();
                $('#eventModal').on('show.bs.modal', function() {
                    $(".alert").removeClass("alert-danger");
                    $(".alert").removeClass("alert-success");
                    $(".alert").hide();
                    $(".modal-title").html("新增事件");

                    var start_time = $.fullCalendar.formatDate(start,'yyyy-MM-dd HH:mm');
                    var end_time = $.fullCalendar.formatDate(end,'yyyy-MM-dd HH:mm');
                    if (isEdit == false) {
                        $('#inputStart').val(start_time);
                        $('#inputEnd').val(end_time);
                        isEdit = true;
                    }
                    $("#myForm").attr("action", "/time/ajax_time_add");
                });
                $('#eventModal').modal('show');
                calendar.fullCalendar('updateEvent', event);
			},
			editable: true,
            viewDisplay: function(view) {
                var viewStart = $.fullCalendar.formatDate(view.start,"yyyy-MM-dd HH:mm"); 
                var viewEnd = $.fullCalendar.formatDate(view.end,"yyyy-MM-dd HH:mm");
                $("#calendar").fullCalendar('removeEvents');
                $.getJSON('/time/ajax_get_data',{start:viewStart, end:viewEnd},function(data) {    
                    for(var i=0; i < data.length; i++) {    
                       var obj = new Object();    
                       obj.id = data[i].id;    
                       obj.book_id = data[i].book_id;    
                       obj.title = data[i].title;                   
                       if (data[i].book_id > 0) {
                            obj.display_title = data[i].display_title;                   
                       }
                       obj.color = data[i].color;  
                       obj.start = data[i].start;                   
                       obj.end = data[i].end;   
                       obj.users = data[i].users;
                       obj.others = data[i].others;
                       obj.room_info = data[i].room_info;
                       obj.editable = data[i].editable;
                       obj.allDay = data[i].allDay;
                       calendar.fullCalendar('renderEvent',obj,true);                     
                   }    
               }); //把从后台取出的数据进行封装以后在页面上以fullCalendar的方式进行显示
            },
			//events: '/time/ajax_get_data', 
            eventRender: function(event, element) {
                //element.qtip({
                //    content: event.description
                //});
            },
            dayClick: function(event, element) {
            },
            eventClick: function(event, element) {
                if (event.editable == false) {return false;}
                //个人内容修改
                if (event.book_id == 0) {
                    $("#time-delete").show();
                    $(".modal-body p").html('');
                    $(".modal-body form").show();
                    $(".btn-danger").show();
                    $(".modal-title").html("修改事件");

                    $('#inputId').val(event.id);
                    $('#inputTitle').val(event.title);
                    $('#inputStart').val($.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'));
                    $('#inputEnd').val($.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'));
                    $("#selectColor").find("option[value='" + event.color + "']").attr("selected",true);
                    $('#eventModal').modal('show');
                    $("#myForm").attr("action", "/time/ajax_update_data");
                }
                //会议室修改
                else {
                    $("#meetingForm")[0].reset();
                    $("#inputUserBox").html('<input type="text" class="form-control" id="inputUser" name="inputUser" placeholder="参加人员">');
                    $("#inputUser").tokenInput("/address/ajax_search_name", {
                        prePopulate: event.users
                    });

                    $(".Gate_book_delete").show();
                    $("#modal-title").html("修改预定：" + event.room_info.room_name + "(" + event.room_info.room_position + ")");
                    $('#book_id').val(event.book_id);
                    $('#room_id').val(event.room_info.room_id);
                    $('#title').val(event.title);
                    $('#start').val($.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'));
                    $('#end').val($.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'));
                    $('#inputMemo').val(event.others);

                    $('#bookModal').modal('show');
                    $("#modal-body p.modal-message").hide();
                    $("#modal-body form").show();
                    $("#meetingForm").attr("action", "/meeting/ajax_book_update");
                }
                //calendar.fullCalendar('updateEvent', event);
            },
            eventDrop: function(event, jsEvent, ui, view) {
                    $.post("/time/ajax_update_data", {
                       id: event.id,
                       title: event.title,
                       start: $.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'),
                       end: $.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'),
                       color: event.color,
                       rand: new Date().getTime() 
                    });
                    calendar.fullCalendar('updateEvent', event);
                    location.reload();
            },
            eventResize: function(event, jsEvent, ui, view) {
                    $.post("/time/ajax_update_data", {
                       id: event.id,
                       title: event.title,
                       start: $.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'),
                       end: $.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'),
                       color: event.color,
                       rand: new Date().getTime() 
                    });
                    calendar.fullCalendar('updateEvent', event);
                    location.reload();
            },
            eventMouseover: function(event, jsEvent, view){
                showDetail(event, jsEvent);
            },
            eventMouseout: function(event, jsEvent, view){
                $('#tip').remove();
            },
		});

        //日历
          $(".form_datetime").datetimepicker({
              startView: 1,
              format: "yyyy-mm-dd hh:ii",
              autoclose: true,
              todayBtn: true,
              pickerPosition: "bottom-left",
              //startDate: "2013-02-14 10:00",
              minuteStep: 10
          });

        //删除事件
          $("#time_delete").click(function(){
                $(this).hide();
                $(".modal-body p").show();
                $(".modal-body form").slideUp();
                $(".modal-title").html("删除事件");
                $(".modal-body p").html("确定要删除事件：<b>" + $("#inputTitle").val() + "</b> 吗？");
                $("#myForm").attr("action", "/time/ajax_time_delete");
            });

        //meeting edit
        $("#meeting_submit").click(function(){
            var _this = $(this);
            $(".alert").removeClass("alert-danger");
            var options = { 
                beforeSubmit:  function showRequest() {
                    _this.addClass("disabled");
                },
                success:function showResponse(data)  {

                    $(".alert").slideDown();
                    $(".alert").children("p").html(data.message);
                    if(data.code == '200'){
                        $(".alert").addClass("alert-success");
                        $(".alert").children("h4").html("Success!");
                        setTimeout("$('.modal').modal('hide');",1000);
                        setTimeout("location.reload();",1300);
                        return true;
                    }else{
                        $(".alert").addClass("alert-danger");
                        $(".alert").children("h4").html("Error!");
                        _this.removeClass("disabled");
                        return false;
                    }
                },
                type:      'post',
                dataType:  'json',
                timeout:   30000 
            };

            $('#meetingForm').ajaxSubmit(options);
            return false;
        });

        //delete meeting
          $(".Gate_book_delete").click(function(){
            $(".Gate_book_delete").hide();
            $("#modal-body p").show();
            $("#modal-body form").slideUp();
            $("#modal-title").html("删除预定？");
            $("#meetingForm").attr("action", "/meeting/ajax_book_delete");
            $("#modal-body p.modal-message").html("是否要删除预定？");
          });
	});

    function showDetail(obj, e){
        var eInfo = '<div id="tip" class="display:none;"><ul>';  
        eInfo += '<li class="clock">' + '开始：'+$.fullCalendar.formatDate(obj.start,"yyyy-MM-dd HH:mm:ss") +'</br>结束：'+$.fullCalendar.formatDate(obj.end,"yyyy-MM-dd HH:mm:ss")+ '</li>';  
        if (obj.book_id) {
            eInfo += '<li class="message">' +'会议室：'+ obj.room_info.room_name+ '<br/> </li>';
            eInfo += '<li class="message">' + obj.title+ '<br/> </li>';  
        }
        else {
            eInfo += '<li class="message">' +'内容：'+ obj.title+ '<br/> </li>';  
        }
        //eInfo += '<li>分类：' + obj.title + '</li>';  
        eInfo += '</ul></div>';  
        $(eInfo).appendTo($('body'));  
        $('#tip').css({"opacity":"0.4", "display":"none", "left":(e.pageX + 20) + "px", "top":(e.pageY + 10) + "px"}).fadeTo(600, 0.9);  
        //鼠标移动效果  
        $('.fc-event-inner').mousemove(function(e){  
            $('#tip').css({'top': (e.pageY + 10), 'left': (e.pageX + 20)});  
        });
    }

</script>
<style>
 /********************************************** 鼠标悬停tip提示  ************************************************/  
    #tip{  
        position: absolute;  
        width: 250px;  
        max-width: 400px;  
        text-align: left;  
        padding: 4px;  
        border: #87CEEB solid 7px;  
        border-radius: 5px;  
        background: #00BFFF;  
        z-index: 1000;  
    }  
    #tip ul{  
        margin: 0;  
        padding: 0;  
    }  
    #tip ul li{  
        font-family:'Microsoft YaHei', 微软雅黑, 'Microsoft JhengHei', 宋体;  
        font-size:15px;  
        list-style: none;  
        padding-left: 10px;  
    }
</style>
