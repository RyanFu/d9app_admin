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
 *   $resp = $c->execute($req);
 *
 * 清单在此結束
 * 查询淘宝客推广商品详细信息
 * @package TaobaoApi 
 * @author weiwang
 * @since 2012.08.01
 */


class TopatsItemcatsRequest extends TaobaoApi{

	/**
	 *
	 * @return ItemsGetRequest
	 */
    public function __construct() {
		$this->method = "taobao.topats.itemcats.get";
	}

	/**
	 * 设置淘宝相应api的私有变量
	 *
	 * @return array 
	 * @access public
	 */	
	public function getParamArr(){
		$paramArr = array(
		);	
		return $paramArr;
	}

}

