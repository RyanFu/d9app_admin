<!--
<ol class="breadcrumb">
  <li><a href="/auth/home">Home</a></li>
  <li><a href="/meeting/index">会议室</a></li>
  <li class="active">会议室预定</li>
</ol>
-->

<link href="/static/bootstrap/css/datetimepicker.css" rel="stylesheet">
<script src="/static/bootstrap/js/bootstrap-datetimepicker.js"></script>
<style>
.form-control{display:inline;max-width:140px;}
.table-condensed{font-size: 14px;}
</style>

<div class="bs-example">
<form id="myForm" action="/meeting/index" method="get">
    <nav class="navbar navbar-default" role="navigation">
        <div class="navbar-header">
          <span class="navbar-brand">可用会议室查询</span>
        </div>
        <div class="navbar-collapse navbar-ex2-collapse">
            <input type="search" class="form-control" style="max-width:100px;" id="capacity" placeholder="参数人数" name="num" value="<?php if($search['num']>0){echo $search['num'];} ?>">

            <input size="16" class="form_datetime form-control" type="text" placeholder="开始时间"  name="start" id="start" value="<?php echo $search['start']; ?>">
            <input size="16" class="form_datetime form-control" type="text" placeholder="结束时间"  name="end" id="end" value="<?php echo $search['end']; ?>">

            <button type="submit" class="btn btn-primary navbar-btn"> 搜 索 </button>
        </div>
    </nav>
</form>
</div>

<table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>编号</th>
            <th>会议室名称</th>
            <th>位置</th>
            <th>容纳人数</th>
            <th>状态</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
<?php
if (!empty($room_data) && is_array($room_data)) {
  foreach ($room_data as $key => $value) {
    if (empty($value['booking'])) {
      $status = '<span class="glyphicon glyphicon-ok-sign text-success"></span>';
    }else{
      $status = '<span class="glyphicon glyphicon-minus-sign text-danger"></span>';
    }
echo <<<ROME
          <tr>
            <td>{$value['room_no']}</td>
            <td>{$value['room_name']}</td>
            <td>{$value['room_position']}</td>
            <td>{$value['room_capacity']}</td>
            <td>{$status}</td>
            <td>
              <a type="button" href="/meeting/room_book?id={$value['room_id']}">预定</a>
            </td>
          </tr>
ROME;

  }
}
?>
        </tbody>
</table>

<script type="text/javascript">
$(function () {

  $("#end").focus(function(){
    var _start = $("#start").val();
    if (_start != "") {
      $(this).val(_start);
    };
  });

  $(".form_datetime").datetimepicker({
      startView: 1,
      format: "yyyy-mm-dd hh:ii",
      autoclose: true,
      todayBtn: true,
      pickerPosition: "bottom-left",
      //startDate: "2013-02-14 10:00",
      minuteStep: 10
  });

});
</script>
