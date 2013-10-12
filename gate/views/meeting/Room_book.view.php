<ol class="breadcrumb">
  <li><a href="/auth/home">Home</a></li>
  <li><a href="/meeting/index">会议室</a></li>
  <li><a href="/meeting/index">会议室预定</a></li>
  <li class="active"><?php echo $room_data['room_name']; ?></li>
</ol>

<link href="/static/css/tokeninput.css" rel="stylesheet" />
<script src="/static/js/tokeninput.js?v=20130923"></script>
<style>
.table-condensed{font-size: 14px;}
</style>

<div id='calendar'></div>

<!-- Modal -->
<div class="modal fade" id="bookModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title"></h4>
        </div>

        <div class="alert" style="display:none;">
          <h4>Notice!</h4>
          <p></p>
        </div>

        <div class="modal-body">
          <form id="myForm" class="form-horizontal" role="form" action="" method="post">

            <div class="form-group">
            <label for="inputUsername" class="col-lg-2 control-label">会议主题</label>
            <div class="col-lg-10">
              <input type="text" class="form-control" id="title" name="title" placeholder="会议主题">
            </div>
            </div>

            <div class="form-group">
            <label for="inputDepartment" class="col-lg-2 control-label">参加人员</label>
            <div class="col-lg-10" id="inputUserBox">
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

            <input type="hidden" id="room_id" name="room_id" value="<?php echo $room_data['room_id']; ?>">
            <input type="hidden" id="book_id" name="book_id" value="0">
          </form>
          <p class="modal-message"></p>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-danger pull-left Gate_book_delete"> 删除预定 </button>
          <button type="button" class="btn btn-default" data-dismiss="modal"> 关 闭 </button>
          <button type="button" class="btn btn-primary" id="btn_submit"> 提 交 </button> 
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script type="text/javascript">
$(function () {

  $(".form_datetime").datetimepicker({
      startView: 1,
      format: "yyyy-mm-dd hh:ii",
      autoclose: true,
      todayBtn: true,
      pickerPosition: "bottom-left",
      //startDate: "2013-02-14 10:00",
      minuteStep: 10
  });

  $('.modal').on('show.bs.modal', function () {
    $(".alert").removeClass("alert-danger");
    $(".alert").removeClass("alert-success");
    $(".alert").hide();
    //$("#myForm")[0].reset();
    $("#btn_submit").removeClass("disabled");
  })

  $(".Gate_book_delete").click(function(){
    $(".Gate_book_delete").hide();
    $(".modal-body p").show();
    $(".modal-body form").slideUp();
    $(".modal-title").html("删除预定？");
    $("#myForm").attr("action", "/meeting/ajax_book_delete");
    $(".modal-body p.modal-message").html("是否要删除预定？");
  })

  var date = new Date();
  var d = date.getDate();
  var m = date.getMonth();
  var y = date.getFullYear();

  var calendar = $('#calendar').fullCalendar({
    defaultView: 'agendaWeek',
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'agendaWeek,agendaDay'
    },
    selectable: true,
    selectHelper: true,

    select: function(start, end, allDay) {
        var myDate = new Date();
        if (start > myDate) {
          //清空上次选择
          $(".token-input-token").remove();
          $("#myForm")[0].reset();

          $(".Gate_book_delete").hide();
          var start_time = $.fullCalendar.formatDate(start,'yyyy-MM-dd HH:mm');
          var end_time = $.fullCalendar.formatDate(end,'yyyy-MM-dd HH:mm');
          $('#start').val(start_time);
          $('#end').val(end_time);
          $('#bookModal').modal('show');

          $(".modal-body p.modal-message").hide();
          $(".modal-body form").show();
          $(".modal-title").html("预定会议室：<?php echo $room_data['room_name'], ' (', $room_data['room_position'], ')'; ?>");
          $("#myForm").attr("action", "/meeting/ajax_book_add");
        };
    },

    //events: '/meeting/ajax_book_get?id=<?php echo $room_data['room_id']; ?>',
    viewDisplay: function(view) {
        var viewStart = $.fullCalendar.formatDate(view.start,"yyyy-MM-dd HH:mm"); 
        var viewEnd = $.fullCalendar.formatDate(view.end,"yyyy-MM-dd HH:mm");
        $("#calendar").fullCalendar('removeEvents');
        $.getJSON('/meeting/ajax_book_get',{id:<?php echo $room_data['room_id']; ?>,start:viewStart, end:viewEnd},function(data) {    
            for(var i=0; i < data.length; i++) {
               var obj = new Object();
               obj.id = data[i].id;
               obj.title = data[i].title;
               obj.color = data[i].color;
               obj.start = data[i].start;
               obj.end = data[i].end;
               obj.users = data[i].users;
               obj.others = data[i].others;
               obj.user_id = data[i].user_id;
               obj.allDay = data[i].allDay;
               obj.selectable = data[i].editable;
               obj.editable = data[i].editable;

               calendar.fullCalendar('renderEvent',obj, true);                     
           }
       }); //把从后台取出的数据进行封装以后在页面上以fullCalendar的方式进行显示
    },

    eventClick: function(event, element) {
        if (event.editable == false) {return false;}
        $("#myForm")[0].reset();
        $("#inputUserBox").html('<input type="text" class="form-control" id="inputUser" name="inputUser" placeholder="参加人员">');

        $("#inputUser").tokenInput("/address/ajax_search_name", {
          prePopulate: event.users
        });

        $(".Gate_book_delete").show();
        $(".modal-title").html("修改预定：<?php echo $room_data['room_name'], ' (', $room_data['room_position'], ')'; ?>");
        $('#book_id').val(event.id);
        $('#title').val(event.title);
        $('#start').val($.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'));
        $('#end').val($.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'));
        $('#inputMemo').val(event.others);

        $('#bookModal').modal('show');
        $(".modal-body p.modal-message").hide();
        $(".modal-body form").show();
        $("#myForm").attr("action", "/meeting/ajax_book_update");
        calendar.fullCalendar('updateEvent', event);

    },
    eventDrop: function(event, jsEvent, ui, view) {
        $(".Gate_book_delete").show();
        $.post("/meeting/ajax_book_update", {
           room_id: <?php echo $room_data['room_id']; ?>,
           book_id: event.id,
           title: event.title,
           start: $.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'),
           end: $.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'),
           event: true,
           inputUser: event.users,
           inputMemo: event.others,
           rand: new Date().getTime() 
        });
        calendar.fullCalendar('updateEvent', event);
    },
    eventResize: function(event, jsEvent, ui, view) {
        $(".Gate_book_delete").show();
        $.post("/meeting/ajax_book_update", {
           room_id: <?php echo $room_data['room_id']; ?>,
           book_id: event.id,
           title: event.title,
           start: $.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH:mm'),
           end: $.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm'),
           event: true,
           inputUser: event.users,
           inputMemo: event.others,
           rand: new Date().getTime() 
        });
        calendar.fullCalendar('updateEvent', event);
    }

  });
  
});

</script>

