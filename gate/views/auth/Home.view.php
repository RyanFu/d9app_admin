        <div class="panel panel-default">
              <div class="panel-heading">通讯录查询</div>
              <div class="panel-body">
                 <form class="navbar-form navbar-middle" action="/address/addresslist" role="search">
                    <div class="col-lg-4">
                        <input type="text" class="form-control" name="search" placeholder="可按姓名，拼音，邮箱，手机号，部门等搜索">
                    </div>
                    <button type="submit" class="btn btn-primary">搜索</button>
                </form>
              </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">我的时间</div>
          <div class="panel-body">
<?php
if(!empty($time_data)) {
?>
            <div class="tab-content">
              <div class="row">

                <div class="tab-pane" id="myTime">
                  <table class="table table-hover">

                    <thead>
                      <tr>
                        <th width="250px;" >时间</th>
                        <th>工作内容</th>
                      </tr>
                    </thead>
                    <tbody>
<?php

  foreach($time_data as $val) {
    $start = str_replace(array('am','pm'),array('上午','下午'),date('d日ag:i',strtotime($val['start_time'])));
    $end   = str_replace(array('am','pm'),array('上午','下午'),date('ag:i', strtotime($val['end_time'])));
    $dowhat = !empty($val['dowhat']) ? $val['dowhat'] : '会议：'. $val['meeting_topic'];
echo <<<TIME
    <tr>
      <td>{$start} 至 {$end}</td>
      <td>{$dowhat}</td>
    </tr>
TIME;
  }

?>

                      </tbody>
                    </table>
                  </div>

              </div><!--/row-->
            </div>
<?php
}else{
echo '没有内容';
}
?>
          </div>
        </div>

<div class="panel panel-default">
  <div class="panel-body">
    <button data-toggle="modal" class="btn btn-warning" href="#feedbackForm">意见反馈</button>
  </div>
</div>


<!-- Modal -->
<div class="modal fade" id="feedbackForm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title">意见反馈</h4>
        </div>

        <div class="alert" style="display:none;">
          <h4>Notice!</h4>
          <p></p>
        </div>

        <div class="modal-body">
          <form id="myForm" class="bs-example" role="form" action="/help/ajax_feedback_add" method="post">
            <div class="form-group">
  <label><input type="radio" name="type" value="2" checked>Bug反馈</label>
  <label><input type="radio" name="type" value="1"> 修改建议</label>
            </div>

            <div class="form-group">
              <textarea class="form-control" rows="3" name="text"></textarea>
            </div>

          </form>
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

  $('#myTab a:first').tab('show');//初始化显示哪个tab 

  $('#myTab a').click(function (e) { 
    e.preventDefault();//阻止a链接的跳转行为 
    $(this).tab('show');//显示当前选中的链接及关联的content 
  }) 

});

</script>
