<?php
namespace Phplib\TaobaoApi;

/**
 *
 *
 * {@link http://api.taobao.com/apidoc/api.htm?path=cid:38-apiId:339 [淘宝api网址]}	 
 * 
 * @example 
 *
 *   $c = new TopClient;
 *
 *   一般情况下不需要设置如下两个变量
 *   $c->appkey = appkey;
 *   $c->secretKey = secret;
 *
 *   $req = new ItemsGetRequest;
 *	 $req->setPageNo(1);
 *	 $req->setNicks(array('3435','卖家昵称'));
 *	 $req->setFields("num_iid,detail_url,title,nick,volume,pic_url,delist_time,price,score,post_fee,type");
 *   $resp = $c->execute($req);
 *
 * 清单在此結束
 * 查询淘宝客推广商品详细信息
 * @package TaobaoApi 
 * @author weiwang
 * @since 2012.08.01
 */


class ItemsGetRequest extends TaobaoApi{


	/**
	 * num_iids
	 *
	 * @var array
	 * @access private
	 */

	private $numIids = array();

	/**
	 *
	 * @return ItemsGetRequest
	 */
    public function __construct() {
		$this->method = "taobao.items.list.get";
	}

	/**
	 * 设置要获取的淘宝nick
	 *
	 * @param array $nicks 參數1
	 * @return void
	 * @access public
	 */	
	public function setNumIids(array $nids) {
		$this->numIids = $nids;
	}

	/**
	 * 设置淘宝相应api的私有变量
	 *
	 * @return array 
	 * @access public
	 */	
	public function getParamArr(){
		if (empty($this->numIids)) {
			return array();
		}
		$nids = implode(',', $this->numIids);
		$paramArr = array(
		    'num_iids' => $nids,
		);	
		return $paramArr;
	}

}


