<?php
namespace Phplib\TaobaoApi;

/**
 *
 *
 * {@link http://open.taobao.com/doc/detail.htm?id=959 [淘宝api网址]}	 
 * 
 * @example 
 *
 *   $c = new TopClient;
 *
 *   一般情况下不需要设置如下两个变量
 *   $c->appkey = appkey;
 *   $c->secretKey = secret;
 *
 *   $req = new SpmeffectGetRequest;
 *	 $req->setPageNo(1);
 *	 $req->setNicks(array('3435','卖家昵称'));
 *	 $req->setFields("num_iid,detail_url,title,nick,volume,pic_url,delist_time,price,score,post_fee,type");
 *   $resp = $c->execute($req);
 *
 * 清单在此結束
 * 查询淘宝导购效果跟踪： SPM
 * @package TaobaoApi 
 * @author weiwang
 * @since 2013.2.20
 */


class SpmeffectGetRequest extends TaobaoApi{

	/**
	 * spm page 
	 *
	 * @var string 
	 * @access private
	 */
	private $pageDetail = 'TRUE';

	/**
	 * spm module
	 *
	 * @var string
	 * @access private
	 */
	private $moduleDetail = 'TRUE';

	/**
	 * spm 时间
	 *
	 * @var string
	 * @access private
	 */
	private $date = NULL;

	/**
	 *
	 * @return SpmeffectGetRequest 
	 */
    public function __construct() {
		$this->method = "taobao.spmeffect.get";
	}

	/**
	 * 设置是否要获取spm的page内容
	 *
	 * @param string $pageDetail 參數1
	 * @return void
	 * @access public
	 */	
	public function setPageDetail($pageDetail = 'TRUE') {
		$this->pageDetail = $pageDetail;
	}

	/**
	 * 设置是否要获取spm的module内容
	 *
	 * @param string $moduleDetail 參數1
	 * @return void
	 * @access public
	 */	
	public function setModuleDetail($moduleDetail = 'TRUE') {
		$this->moduleDetail = $moduleDetail;
	}

	/**
	 * 设置要获取spm的具体时间
	 *
	 * @param string $date 參數1
	 * @return void
	 * @access public
	 */	
	public function setDate($date = NULL) {
		$this->date = $date;
	}

	/**
	 * 设置淘宝相应api的私有变量
	 *
	 * @return array 
	 * @access public
	 */	
	public function getParamArr(){
		if (empty($this->date)) {
			return array();
		}

		$paramArr = array(
           'date'  => $this->date,
           'page_detail'  => strtolower($this->pageDetail),  //只支持小写
           'module_detail'  => strtolower($this->moduleDetail), //只支持小写
		);	
		return $paramArr;
	}

}

