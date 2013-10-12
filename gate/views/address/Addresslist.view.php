<!--
<ol class="breadcrumb">
  <li><a href="/auth/home">Home</a></li>
  <li><a href="/address/index">通讯录</a></li>
  <li class="active">通讯录列表</li>
</ol>
<div class="breadcrumb">
<?php
//    echo $content;
?>
</div>
-->

<!--
<a data-toggle="modal" href="#addStaff" type="button" class="btn btn-primary btn-lg">添加人员</a>
-->
<div>
<form class="form-inline pull" role="form" action="/address/addresslist" method="get">
  <div class="col-lg-4">
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
  <button type="submit" class="btn btn-primary">搜索</button>
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
            <th style="display:none">id</th>
            <th>姓名</th>
            <th>邮件</th>
            <th>部门</th>
            <th>分机</th>
            <th>手机</th>
            <th>QQ</th>
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
            <td style="display:none">{$v['sid']}</td>
            <td>{$v['name_c']}</td>
            <td>{$v['mail']}</td>
            <td>{$v['departname']}</td>
            <td>{$v['extension']}</td>
            <td>{$v['phone']}</td>
            <td>{$v['qq']}</td>
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
          <h4 class="modal-title">添加人员</h4>
        </div>
        <div class="modal-body">
			<form class="form-horizontal" role="form" onSubmit="return false;">
			  <div class="form-group">
				<label for="inputUsername" class="col-lg-2 control-label">姓名</label>
				<div class="col-lg-10">
				  <input type="text" class="form-control" id="inputUsername" placeholder="请输入姓名">
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputEmail" class="col-lg-2 control-label">邮箱</label>
				<div class="col-lg-10">
				  <input type="email" class="form-control" id="inputEmail" placeholder="Email">
				</div>
			  </div>
			  <div class="form-group">
				<label for="inputDepartment" class="col-lg-2 control-label">部门</label>
				<div class="col-lg-10">
				  <input type="text" class="form-control" id="inputDepartment" placeholder="请输入所在部门或组">
				</div>
			  </div>
			  <div class="form-group">
				<div class="col-lg-offset-2 col-lg-10">
				  <button type="submit" class="btn btn-default">添加</button>
				</div>
			  </div>
			</form>	
		</div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
          <!--<button type="button" class="btn btn-primary">Save changes</button>--> 
        </div>
      </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
  </div><!-- /.modal -->
