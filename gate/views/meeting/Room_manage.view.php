<ol class="breadcrumb">
  <li><a href="/auth/home">Home</a></li>
  <li><a href="/meeting/index">会议室</a></li>
  <li class="active">会议室管理</li>
</ol>

<p>
  <a data-toggle="modal" href="#roomForm" type="button" class="btn btn-primary btn-lg Gate_room_add">添加会议室</a>
</p>

<table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>编号</th>
            <th>会议室名称</th>
            <th>位置</th>
            <th>容纳人数</th>
            <th>操作</th>
          </tr>
        </thead>
        <tbody>
<?php
if (!empty($room_data) && is_array($room_data)) {
  foreach ($room_data as $key => $value) {

echo <<<ROME
          <tr>
            <td>{$value['room_no']}</td>
            <td>{$value['room_name']}</td>
            <td>{$value['room_position']}</td>
            <td>{$value['room_capacity']}</td>
            <td>
              <a class="Gate_room_modify" data-toggle="modal" type="button" href="#roomForm" data-id="{$value['room_id']}">编辑</a> | 
              <a class="Gate_room_delete" data-toggle="modal" type="button" href="#roomForm" data-id="{$value['room_id']}">删除</a>
            </td>
          </tr>
ROME;

  }
}
?>
        </tbody>
</table>

<!-- Modal -->
<div class="modal fade" id="roomForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
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
    				<label for="inputUsername" class="col-lg-2 control-label">名称</label>
    				<div class="col-lg-10">
    				  <input type="text" class="form-control" id="inputName" name="inputName" placeholder="会议室名称">
    				</div>
    			  </div>
    			  <div class="form-group">
    				<label for="inputEmail" class="col-lg-2 control-label">位置</label>
    				<div class="col-lg-10">
    				  <input type="text" class="form-control" id="inputPosition" name="inputPosition" placeholder="会议室位置">
    				</div>
    			  </div>
    			  <div class="form-group">
    				<label for="inputDepartment" class="col-lg-2 control-label">人数</label>
    				<div class="col-lg-10">
    				  <input type="text" class="form-control" id="inputCapacity" name="inputCapacity" placeholder="容纳人数">
    				</div>
    			  </div>

            <input type="hidden" id="inputId" name="inputId" value="">
    			</form>
          <p></p>
    		</div>

        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal"> 关 闭 </button>
          <button type="button" class="btn btn-primary" id="btn_submit"> 提 交 </button> 
        </div>

      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
$(function () {
  $('.modal').on('show.bs.modal', function () {
    $(".alert").removeClass("alert-danger");
    $(".alert").removeClass("alert-success");
    $(".alert").hide();
    $("#myForm")[0].reset();
    $("#btn_submit").removeClass("disabled");
  })
  $("a.Gate_room_add").click(function(){
    $(".modal-body p").hide();
    $(".modal-body form").show();
    $(".modal-title").html("添加会议室");
    $("#myForm").attr("action", "/meeting/ajax_room_add");
  })
  $("a.Gate_room_modify").click(function(){
    $(".modal-body p").hide();
    $(".modal-body form").show();
    $(".modal-title").html("修改会议室");
    $("#myForm").attr("action", "/meeting/ajax_room_modify");
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/meeting/ajax_room_get?inputId="+$(this).attr("data-id"),
        success: function(ajaxData){
          if (ajaxData.code == '200') {
            var data = ajaxData.data;
            $("#inputId").val(data.room_id);
            $("#inputName").val(data.room_name);
            $("#inputPosition").val(data.room_position);
            $("#inputCapacity").val(data.room_capacity);
          }else{
            $(".alert").addClass("alert-danger");
            $(".alert").children("h4").html("Error!");
            $(".alert").children("p").html(ajaxData.message);
            setTimeout("$('.modal').modal('hide');",1000);
          }
        }
    });
  })
  $("a.Gate_room_delete").click(function(){
    $(".modal-body p").show();
    $(".modal-body form").hide();
    $(".modal-title").html("删除会议室");
    $("#myForm").attr("action", "/meeting/ajax_room_delete");
    $("#inputId").val($(this).attr("data-id"));
    $.ajax({
        type: "post",
        dataType: "json",
        url: "/meeting/ajax_room_get?inputId="+$(this).attr("data-id"),
        success: function(ajaxData){
          if (ajaxData.code == '200') {
            var data = ajaxData.data;
            $(".modal-body p").html("是否要删除会议室：<b>"+data.room_name+"</b> ？");
          }else{
            $(".modal-body p").html("是否要删除所选会议室？");
          }
        }
    });
  })
});
</script>
