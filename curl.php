<?php
/**
 * Use Curl lib easy get data
 * @authors rewrite (dark)
 * @date    2018-11-14 09:41:23
 * @version $Id$
 */

class Curl {

	private $_curl_;
	private $_port_ = 80;
	private $_useragent_ = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
	private $_httpheader_;
	private $_followlocation_;
	private $_timeout_;
	private $_maxRedirects_;
	private $_cookieFileLocation_;
	private $_referer_ = '';
	
	private $_session_;
	private $_webpage_;
	private $_includeHeader_;
	private $_noBody_;
	private $_status_;
	private $_binaryTransfer_;
	
	private $_httpcode_;

	/**
	 * 初始化curl
	 */
	public function __construct($followlocation = true, $timeOut = 30, $maxRedirecs = 4, $binaryTransfer = false, $includeHeader = false, $noBody = false){

		$this->_curl_ = curl_init();

		$this->_followlocation_ = $followlocation;
		$this->_timeout_        = $timeOut;
		$this->_maxRedirects_   = $maxRedirecs;
		$this->_includeHeader_  = $includeHeader;
		$this->_binaryTransfer_ = $binaryTransfer;
		$this->_noBody_         = $noBody;	
	}

	// 設定port
	public function setPort($port=80) {
		$this->_port_ = $port;
	}

	// 設定 Refere 
	public function setReferer($referer_url)	{
		$this->_referer_ = $referer_url;
	}

	// 設定 UserAgent
	public function setUserAgent($user_agent)	{
		$this->_useragent_ = $user_agent;
	}

	//設定 header
	public function setHttpHeader($http_header)	{
		$this->_httpheader_ = $http_header;
	}

	/**
	 * 設定選項
	 * @return [type] [description]
	 */
	protected function curlOption() {

		curl_setopt($this->_curl_, CURLOPT_PORT, $this->_port_);
		curl_setopt($this->_curl_, CURLOPT_USERAGENT, $this->_useragent_);
		curl_setopt($this->_curl_, CURLOPT_REFERER, $this->_referer_);
		curl_setopt($this->_curl_, CURLINFO_HEADER_OUT, true);
		curl_setopt($this->_curl_, CURLOPT_TIMEOUT, $this->_timeout_);
		// 以字串的形式回傳
		curl_setopt($this->_curl_, CURLOPT_RETURNTRANSFER, true);

		if ($this->_httpheader_) {
			curl_setopt($this->_curl_, CURLOPT_HTTPHEADER, $this->_httpheader_);
		}

		curl_setopt($this->_curl_, CURLOPT_MAXREDIRS, $this->_maxRedirects_); 

		// 輸出 HEADER 資訊
		if ($this->_includeHeader_) {
			curl_setopt($this->_curl_, CURLOPT_HEADER, TRUE);
		}

		// 要配合post使用
		if($this->_noBody_) {
			curl_setopt($this->_curl_, CURLOPT_NOBODY, true);
		}
	}
	
	/**
	 * http Method GET 
	 * @param  [type] $url [description]
	 * @return [type]      [description]
	 */
	public function get($url) {
		// 載入 option
		$this->curlOption();

		curl_setopt($this->_curl_, CURLOPT_URL, $url);

		$curldata = curl_exec($this->_curl_); 
	  $this->_httpcode_ = curl_getinfo($this->_curl_, CURLINFO_HTTP_CODE); 
		
		return $curldata;
	}

	/**
	 * Http Method POST
	 * 發送POST請求時，HTTPHEADER 常見是用 "application/x-www-form-urlencoded"
	 * 這是html 表單最常使用的方式
	 * @param  [type] $url   [description]
	 * @param  array  $param [傳送的參數]
	 * @return [type]        [description]
	 */
	public function post($url, $param=array()) {

		// 載入 option
		$this->curlOption();

		curl_setopt($this->_curl_, CURLOPT_URL, $url);

		if (count($param)) {
			//啟用POST
			curl_setopt($this->_curl_, CURLOPT_POST, true);
			curl_setopt($this->_curl_, CURLOPT_POSTFIELDS, http_build_query($param));			
		}

		$curldata = curl_exec($this->_curl_);
	  $this->_httpcode_ = curl_getinfo($this->_curl_, CURLINFO_HTTP_CODE); 

		return $curldata;
	}

	public function getHttpCode() {
		return $this->_httpcode_;
	}

	public function __destruct(){
		curl_close($this->_curl_);
	}
}