<html>
<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title>D9应用管理系统</title>
<link rel="stylesheet" href="/static/bootstrap/css/bootstrap.min.css" />
<link href="/static/css/main.css" rel="stylesheet" />
<link rel='stylesheet' type='text/css' href='/static/css/jquery-ui.min.css' />
<link rel='stylesheet' type='text/css' href='/static/fullcalendar/fullcalendar.css' />
<link rel='stylesheet' type='text/css' href='/static/bootstrap/css/datetimepicker.css' />
<link href="/static/font-awesome/css/font-awesome.css" rel="stylesheet" />
<script src="/static/js/jquery.js"></script>
<script src="/static/js/jquery-ui.custom.min.js"></script>
<script type='text/javascript' src='/static/fullcalendar/fullcalendar.js'></script>
<script type='text/javascript' src='/static/js/jquery-ui.custom.min.js'></script>
</head>
<body>
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
        </div>
        <div class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li class="<?php echo ($this->module == 'auth') ? 'active' : '';?>"><a href="/auth/home">首页</a></li>
            <li class="<?php echo ($this->module == 'address') ? 'active' : '';?>"><a href="/address/addresslist">通讯录</a></li>
            <li class="<?php echo ($this->module == 'meeting') ? 'active' : '';?>"><a href="/meeting/index">会议室预定</a></li>
            <li class="<?php echo ($this->module == 'time') ? 'active' : '';?>"><a href="/time/time_manage">我的时间</a></li>
<!--
            <li class="dropdown <?php echo ($this->module == 'address') ? 'active' : '';?>">
      				<a href="#" class="dropdown-toggle" data-toggle="dropdown">通讯录 <b class="caret"></b></a>
      				<ul class="dropdown-menu">
      					<li><a href="/address/address_manage">通讯录管理</a></li>
      					<li><a href="/address/addresslist">通讯录浏览</a></li>
              </ul>
      			</li>
      			<li class="dropdown <?php echo ($this->module == 'meeting') ? 'active' : '';?>">
      				<a href="/meeting/index"  class="dropdown-toggle" data-toggle="dropdown">会议室预定<b class="caret"></b></a>
      				<ul class="dropdown-menu">
      					<li><a href="/meeting/room_manage">会议室管理</a></li>
      					<li><a href="/meeting/index">会议室预定</a></li>
                <li><a href="/meeting/my_book">我的会议</a></li>
              </ul>
      			</li>
      			<li class="dropdown <?php echo ($this->module == 'time') ? 'active' : '';?>">
      				<a href="#"  class="dropdown-toggle" data-toggle="dropdown">时间管理<b class="caret"></b></a>
      				<ul class="dropdown-menu">
      					<li><a href="/time/time_manage">时间管理</a></li>
              </ul>
      			</li>
-->
          </ul>
		  <ul class="nav navbar-nav navbar-right">
            <?php 
                if($nickname) {
                    echo '
                        <li><a href="/auth/home">' . $nickname . '</a></li>
                        <li><a href="/auth/logout">退出</a></li>';
                }
                else  {
                    echo '<li><a href="/auth/login">登录</a></li>';
                }
            ?>
          </ul>
        </div><!-- /.nav-collapse -->
      </div><!-- /.container -->
    </div><!-- /.navbar -->

    <div class="container">

        <div class="col-xs-12 col-sm-12 col-lg-12">

          <div class="alert alert-danger" id="browser-alert">
            亲，你知道你的浏览器过时了吗？为了正常使用办公系统，请选择一个更为先进强大的浏览器吧！！
            <a href="http://www.google.com/chrome/" class="alert-link chrome" target="_blank">Chrome</a>
            <a href="http://firefox.com.cn/" class="alert-link firefox" target="_blank">Firefox</a>
            <a href="http://www.opera.com/zh-cn/" class="alert-link opera" target="_blank">Opera</a>
            <a href="http://support.apple.com/zh_CN/downloads/" class="alert-link safari" target="_blank">Safari</a>
            <a href="http://chrome.360.cn/" class="alert-link" target="_blank">360极速</a>
          </div>

		<!-- 主区域内容 -->

