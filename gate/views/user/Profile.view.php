<style type="text/css">
#container { position:relative; width:860px; margin:0 auto; }

.item {width:408px; margin:20px 10px 10px; float:left; background:#fff; border:1px solid #b4bbcd; text-align:justify; word-wrap:break-word;}
.inner { padding:10px;}
.inner h2 { margin-bottom:10px;}
.inner h2 a { font-size:15px; color:#333; text-decoration:none;}
.inner h2 a:hover {color:#f30;}

/*timeline navigatior css*/
.timeline_container { display:block; width:16px; height:100%; margin:0 auto;text-align:center; cursor:pointer;}
.timeline{ display:block; width:4px; height:100%; margin:0 auto; overflow:hidden; font-size:0; float:left; position:absolute; left:426px; top:10px; background-color:#e08989;}

/*arrow css style*/
.leftCorner, .rightCorner { display:block; width:13px; height:15px; overflow:hidden; position:absolute; top:8px; z-index:2; }
.rightCorner { right:-13px; background-image:url(/static/images/right.gif);}
.leftCorner { left:-13px; background-image:url(/static/images/left.gif);}

</style>

<div id="container">

    <!-- E TimeLine导航 -->
        <div class="timeline_container">
            <div class="timeline">
                <div class="plus"></div>
            </div>
        </div>
    <!-- E TimeLine导航 -->

    <?php foreach($feed_data as $feed) { ?>
    <!-- S item -->
    <div class="item">
        <div class="inner">
            <div class="media">
                <a class="pull-left" href="/user/profile/<?php echo $user_id;?>">
                  <img class="media-object" data-src="holder.js/64x64" alt="64x64" src="/static/avatar/avatar_default.jpeg" style="width: 64px; height: 64px;">
                </a>
                <div class="media-body">
                    <h5 class="media-heading">
                        <?php //echo $username;?> 
                        <?php if ($feed['feed_type'] == 'meeting') { ?> 
                            参加会议
                        <?php } ?>
                        <p class="pull-right"><?php echo $feed['dateline']; ?></p>
                    </h5>
                        在 <strong><?php echo $feed['feed_body']['room_name']; ?></strong> 会议室 参加了：<?php echo $feed['feed_body']['meeting_topic'];?>
                </div>
            </div>

        </div>
    </div>
    <!-- E item -->
    <?php } ?>
</div>

<script type="text/javascript" src="/static/js/jquery.masonry.min.js"></script>
<script type="text/javascript" >
$(function(){
    // masonry plugin call
    $('#container').masonry({itemSelector : '.item'});
    
    //injecting arrow points
    function Arrow_Points(){
      var s = $("#container").find(".item");
      $.each(s,function(i,obj){
        var posLeft = $(obj).css("left");
        if(posLeft == "0px"){
          html = "<span class='rightCorner'></span>";
          $(obj).prepend(html);
        } else {
          html = "<span class='leftCorner'></span>";
          $(obj).prepend(html);
        }
      });
    }
    Arrow_Points();
});
</script>

