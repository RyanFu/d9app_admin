$(function () {
  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });

	//判断浏览器类型
	if ('undefined' == typeof($.browser)) {$.browser = {};}
	$.browser.mozilla = /firefox/.test(navigator.userAgent.toLowerCase());
	$.browser.webkit = /webkit/.test(navigator.userAgent.toLowerCase());
	$.browser.opera = /opera/.test(navigator.userAgent.toLowerCase());
	$.browser.safari = /safari/.test(navigator.userAgent.toLowerCase());
	$.browser.msie = /msie/.test(navigator.userAgent.toLowerCase());

	var userAgent = window.navigator.userAgent.toLowerCase();
	$.browser.msie10 = $.browser.msie && /msie 10\.0/i.test(userAgent);
	$.browser.msie9 = $.browser.msie && /msie 9\.0/i.test(userAgent); 
	$.browser.msie8 = $.browser.msie && /msie 8\.0/i.test(userAgent);
	$.browser.msie7 = $.browser.msie && /msie 7\.0/i.test(userAgent);
	$.browser.msie6 = !$.browser.msie8 && !$.browser.msie7 && $.browser.msie && /msie 6\.0/i.test(userAgent);
	if ('undefined' == typeof(help_browser)) {help_browser = true;}

	if($.browser.webkit || $.browser.safari || $.browser.mozilla || $.browser.opera || $.browser.msie9 || $.browser.msie10) 
	{

	}
	else
	{
		if (help_browser && ($.browser.msie6 || $.browser.msie7 || $.browser.msie8))
		{
			window.location.href="/help/browser"; 
		}
		else
		{
			$("#browser-alert").slideDown();
		}
	}
});

//提交动作
$(function () {

	$("#btn_submit").click(function(){

		var _this = $(this);
		//$(".alert").slideUp();
		$(".modal-content .alert").removeClass("alert-danger");
		var options = { 
			beforeSubmit:  function showRequest() {
				_this.addClass("disabled");
			},
			success:function showResponse(data)  {

				$(".modal-content .alert").slideDown();
				$(".modal-content .alert").children("p").html(data.message);
				if(data.code == '200'){
					$(".modal-content .alert").addClass("alert-success");
					$(".modal-content .alert").children("h4").html("Success!");
					setTimeout("$('.modal').modal('hide');",1000);
					setTimeout("location.reload();",1300);
					return true;
				}else{
					$(".modal-content .alert").addClass("alert-danger");
					$(".modal-content .alert").children("h4").html("Error!");
					_this.removeClass("disabled");
					return false;
				}
			},
			type:      'post',
			dataType:  'json',
			timeout:   30000 
		};

		$('#myForm').ajaxSubmit(options);
		return false;
	});

	$("#btn_back").click(function(){
		history.back();
	});

	//输入
	if ('undefined' == typeof($.fn.tokenInput)) {

	}else{
		$("#inputUser").tokenInput("/address/ajax_search_name", {
			prePopulate: [
				//{id: 11, name: "Slurms"},
				//{id: 12, name: "address"},
				//{id: 13, name: "back"}
			],
			onAdd: function (item) {
				//alert("Added " + item.name);
			},
			onDelete: function (item) {
				//alert("Deleted " + item.name);
			}
		});
	}

});
