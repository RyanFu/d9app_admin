<ol class="breadcrumb">
  <li><a href="/auth/home">Home</a></li>
  <li><a href="/address/index">通讯录</a></li>
  <li class="active">通讯录列表</li>
</ol>
<div class="breadcrumb">
<?php
    echo $content;
?>
</div>
<div>
<a data-toggle="modal" href="#addStaff" type="button" class="btn btn-primary btn-lg">添加人员</a>
<form class="form-inline pull-right" role="form" action="/address/Address_manage" method="get">
  <div class="form-group">
    <label class="sr-only" for="search">Email address</label>
    <?php
    if(empty($search)){
        $search="可按姓名,拼音,邮箱,手机号,部门等搜索"; 
    }
echo <<<SEARCH
    <input type="search" class="form-control" id="search" placeholder="{$search}" name="search">
SEARCH;
    ?>
  </div>
  <button type="submit" class="btn btn-default">搜索</button>
</form>
</div>



<?php 
if(!empty($no_one)){
echo <<<DDDD
        <div class='breadcrumb' style="color:red">$no_one</div>
DDDD;
}

if(is_array($userinfo) && !empty($userinfo)){
echo <<<TABLE
<table class="table table-striped table-hover">
        <thead>
          <tr>
            <th>工号</th>
            <th>姓名</th>
            <th>邮件</th>
            <th>部门</th>
            <th>工位</th>
            <th>分机</th>
            <th>手机</th>
            <th>QQ</th>
            <th>操作</th>
          </tr>
        </thead>

TABLE;
    foreach($userinfo as $k=>$v){
        $departid = $v['departid'];
        if(isset($departinfo["$departid"])){
        }
echo <<<STAF
            <tbody>
            <tr>
            <td>{$v['sid']}</td>
            <td>{$v['name_c']}</td>
            <td>{$v['mail']}</td>
            <td>{$v['departname']}</td>
            <td>{$v['position']}</td>
            <td>{$v['extension']}</td>
            <td>{$v['phone']}</td>
            <td>{$v['qq']}</td>
            <td>
    <a class="Gate_staff_modify" data-toggle="modal" type="button" href="#addStaff" data-id="{$v['sid']}">编辑</a>
            </td>
            </tr>
            </tbody>
STAF;
    }
echo <<<TABLE
</table>
TABLE;
}
?>
<!--
<ul class="pagination pull-right">
        <li class="disabled"><a href="#">«</a></li>
        <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#">»</a></li>
</ul>
-->

<!-- Modal -->
  <div class="modal fade" id="addStaff" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
          <h4 class="modal-title" id="addstaff_title">添加人员</h4>
          </div>
          <div class="modal-body">
          <form class="form-horizontal" role="form" >
          <div class="form-group">
          <label for="inputUsername" class="col-lg-2 control-label">姓名</label>
          <div class="col-lg-10">
          <input type="text" class="form-control" id="inputUsername" placeholder="请输入姓名" name="name">
          </div>
          </div>
          <div class="form-group">
          <label for="inputEmail" class="col-lg-2 control-label">邮箱</label>
          <div class="col-lg-10">
          <input type="email" class="form-control" id="inputEmail" placeholder="Email" name="mail">
          </div>
          </div>
          <div class="form-group">
          <label for="inputDepartment" class="col-lg-2 control-label">部门</label>
          <div class="col-lg-10">
          <?php
          if(!empty($departs)){
              echo'<select id="inputDepartment" name="departid"  class="form-control">';
              foreach($departs as $v){
                  echo"<option value=\"{$v['departid']}\">{$v['departname']}</option>";
              }
              echo'</select>';
          }
        ?>
        </div>
        </div>
        <div class="form-group">
        <label for="inputExtension" class="col-lg-2 control-label">分机</label>
        <div class="col-lg-10">
        <input type="text" class="form-control" id="inputExtension" placeholder="分机" name="extension">
        </div>
        </div>
        <div class="form-group">
        <label for="inputPhone" class="col-lg-2 control-label">手机</label>
        <div class="col-lg-10">
        <input type="text" class="form-control" id="inputPhone" placeholder="手机(可填两个'/'分隔)" name="phone">
        </div>
        </div>
        <div class="form-group">
        <label for="inputPosition" class="col-lg-2 control-label">工位</label>
        <div class="col-lg-10">
        <input type="text" class="form-control" id="inputPosition" placeholder="工位" name="position">
        </div>
        </div>
        <div class="form-group">
        <label for="inputQQ" class="col-lg-2 control-label">QQ</label>
        <div class="col-lg-10">
        <input type="text" class="form-control" id="inputQQ" placeholder="qq" name="qq">
        </div>
        </div>
        <div class="form-group">
        <label for="inputStatu" class="col-lg-2 control-label">在职状态</label>
        <div class="col-lg-10">
        <select id="inputStatu" name="statu"  class="form-control">
            <option value="1">在职</option>
            <option value="2">不在职</option>
        </select>
        </div>
        </div>
        </form>
		</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary" id="buttonAdd" data-id="0" class="show">添加</button>
        <!--
        <button type="button" class="btn btn-primary" id="buttonSave" data-id="0" class="hide" >保存</button>
        -->
          <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->



  <script >
  //添加用户和更新用户
  $("#buttonAdd").click(function(){
        var attStaff_obj = $('#addStaff');
          attStaff_obj.modal('hide');
          var sid = $(this).attr('data-id');
          var name_c=$('#inputUsername').val();
          var mail=$('#inputEmail').val();
          var department=$('#inputDepartment').val();
          var extension=$('#inputExtension').val();
          var phone=$('#inputPhone').val();
          var position=$('#inputPosition').val();
          var qq=$('#inputQQ').val();
          var statusval=$('#inputStatu').val();
          // 初始化
          attStaff_obj.attr('data-id',0);  
          $(this).text('添加');
          $('#addstaff_title').text('添加人员');
          if(sid == 0){
            var posturl='/address/Ajax_staff_add';
            var postdata={'name_c':name_c,'mail':mail,'departid':department,'extension':extension,'phone':phone,'position':position,'qq':qq,'status':statusval}; 
          }else{
            var posturl = '/address/Ajax_staff_update';
            var postdata={'name_c':name_c,'mail':mail,'departid':department,'extension':extension,'phone':phone,'position':position,'qq':qq,'status':statusval,'sid':sid}; 
          }
          $.post(posturl,postdata,function(res){ 
              var ares = eval('('+res+')');
              if(ares.code== 200){
                alert(ares.message);
              }else{
               alert(ares.message);
              }
              });
          });
    //修改用户资料
    $('a.Gate_staff_modify').click(function(){
        var sid=$(this).attr('data-id');
        var purl='/address/Ajax_staff_select';
        var pdata={'sid':sid};
        $.post(purl,pdata,function(res){
            var ares = eval('('+res+')');
            if(ares.code == 200){
                var mes = ares.message;
                $('#inputUsername').val(mes.name_c);
                $('#inputEmail').val(mes.mail);
                $("#inputDepartment").val(mes.departid);
                $('#inputExtension').val(mes.extension);
                $('#inputPhone').val(mes.phone);
                $('#inputPosition').val(mes.position);
                $('#inputQQ').val(mes.qq);
                $("#inputStatu").val(mes.status);
                $('#buttonAdd').text('保存');
                $('#buttonAdd').attr('data-id',sid);
                $('#addstaff_title').text('修改人员');
            }else{

            }
        });
    }
    );
</script>
