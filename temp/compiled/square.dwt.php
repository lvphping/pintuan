<!doctype html>
<html lang="zh-CN">
<head>
<meta name="Generator" content="haohaipt v6.0" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no">
<meta name="Keywords" content="<?php echo $this->_var['keywords']; ?>" />
<meta name="Description" content="<?php echo $this->_var['description']; ?>" />
<meta name="format-detection" content="telephone=no">
<title><?php echo $this->_var['page_title']; ?></title>
<link rel="shortcut icon" href="favicon.ico" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/style.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/square.css" rel="stylesheet" />
<link href="<?php echo $this->_var['hhs_css_path']; ?>/font-awesome.min.css" rel="stylesheet" />
<?php echo $this->smarty_insert_scripts(array('files'=>'jquery.js,haohaios.js')); ?>
</head>
<body id="square">
<div class="container" style="background:#fff;">
    <div class="fabu_s">
        <form action="" class="form">
            <!--select class="fabu_select" name="orderby">
                <option value="news">最新</option>
                <option value="hot">热门</option>
            </select-->
            <input name="keywords" type="text" class="input_text" placeholder="  搜索产品名看他们说" value="<?php echo $this->_var['keywords']; ?>">
            <input type="submit" value="搜索" class="input_submit"/>
        </form>
        <a href="user.php?act=team_list&composite_status=100" class="fabu">发布</a>
    </div>
    <div class="square_list">
    <?php $_from = $this->_var['goods_list']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'goods');if (count($_from)):
    foreach ($_from AS $this->_var['goods']):
?>
        <dl>
            <dt><img src="<?php echo $this->_var['goods']['headimgurl']; ?>"><p><?php echo $this->_var['goods']['uname']; ?></p></dt>
            <dd>
                <p><?php echo $this->_var['goods']['add_time']; ?></p>
                <p class="mess"><?php echo $this->_var['goods']['square']; ?></p>
                <p class="img">
                    <?php $_from = $this->_var['goods']['gallery']; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array'); }; $this->push_vars('', 'gallery');if (count($_from)):
    foreach ($_from AS $this->_var['gallery']):
?>
                    <img src="<?php echo $this->_var['gallery']['thumb_url']; ?>">
                    <?php endforeach; endif; unset($_from); ?><?php $this->pop_vars();; ?>
                </p>
                <p class="gdname"><?php echo $this->_var['goods']['goods_name']; ?></p>
                <p class="gray">已有 <?php echo $this->_var['goods']['buy_nums']; ?> 位参团购买该产品<?php echo $this->_var['luckdraw_id']; ?></p>
                <div class="square_g_core">
                <a  onclick="addToCart(<?php echo $this->_var['goods']['goods_id']; ?>,0,0,5,<?php echo $this->_var['goods']['team_id']; ?>);">
                    <div class="square_g_core_img"><img src="themes/haohaios/images/tuan_g_core-4935ae4c83.png"></div>
                    <div class="square_g_price">
                        <!--span><?php echo $this->_var['goods']['team_num']; ?>人团</span-->
                        <b>¥<?php echo $this->_var['goods']['team_price']; ?></b>
                    </div> 
                    <input type="hidden" name="luckdraw_id" id="luckdraw_id" value="<?php echo $this->_var['luckdraw_id']; ?>" />
                    <div class="square_g_btn"><span>缺<?php echo $this->_var['goods']['need']; ?>人</span>去参团</div></a>
                </div>
            </dd>
        </dl>
    <?php endforeach; else: ?>
        <div class="nothing">
            <i class="iconfont icon-guangchang"></i>
            <p>还没有小伙伴发布哦</p>
        </div>
    <?php endif; unset($_from); ?><?php $this->pop_vars();; ?>
    </div>
</div>
<?php echo $this->fetch('library/footer.lbi'); ?>
<script type="text/javascript" src="http://res.wx.qq.com/open/js/jweixin-1.0.0.js"></script> 
<script language="javascript" type="text/javascript">
    wx.config({

        debug: false,//这里是开启测试，如果设置为true，则打开每个步骤，都会有提示，是否成功或者失败

        appId: '<?php echo $this->_var['appid']; ?>',

        timestamp: '<?php echo $this->_var['timestamp']; ?>',//这个一定要与上面的php代码里的一样。

        nonceStr: '<?php echo $this->_var['timestamp']; ?>',//这个一定要与上面的php代码里的一样。

        signature: '<?php echo $this->_var['signature']; ?>',

        jsApiList: [

          // 所有要调用的 API 都要加到这个列表中

            'onMenuShareTimeline',

            'onMenuShareAppMessage',
            'checkJsApi',

            'openLocation',

            'getLocation'

        ]

    });

    

    var title="<?php echo $this->_var['title']; ?>";

    var link= "<?php echo $this->_var['link']; ?>";

    var imgUrl="<?php echo $this->_var['imgUrl']; ?>";

    var desc= "<?php echo $this->_var['desc']; ?>";

    wx.ready(function () {

        wx.onMenuShareTimeline({//朋友圈

            title: title, // 分享标题

            link: link, // 分享链接

            imgUrl: imgUrl, // 分享图标

            success: function () { 

                // 用户确认分享后执行的回调函数

                statis(2,1);

            },

            cancel: function () { 

                // 用户取消分享后执行的回调函数

                statis(2,2);

            }

        });

        wx.onMenuShareAppMessage({//好友

            title: title, // 分享标题

            desc: desc, // 

            link: link, // 分享链接

            imgUrl: imgUrl, // 分享图标

            type: '', // 分享类型,music、video或link，不填默认为link

            dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空

            success: function () { 

                // 用户确认分享后执行的回调函数

                statis(1,1);    

            },

            cancel: function () { 

                // 用户取消分享后执行的回调函数

                statis(1,2);

            }

        });

 

    });

    function statis(share_type,share_status){

        $.ajax({

            type:"post",//请求类型

            url:"share.php",//服务器页面地址

            data:"act=link&share_status="+share_status+"&share_type="+share_type+"&link_url=<?php echo $this->_var['link2']; ?>",

            dataType:"json",//服务器返回结果类型(可有可无)

            error:function(){//错误处理函数(可有可无)

                //alert("ajax出错啦");

            },
            success:function(data){
           }

        });
    }
</script>
</body>
</html>
