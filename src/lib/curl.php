<?php

/**
 * Use Curl lib easy get data
 * @authors rewrite (dark)
 * @date    2018-11-14 09:41:23
 * @version $Id$
 */
class Curl
{
	private $_curl_;
	private $_port_ = 80;
	private $_useragent_ = 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)';
	private $_httpheader_;
	private $_followlocation_;
	private $_timeout_;
	private $_maxRedirects_;
	private $_cookieFileLocation_;
	private $_referer_ = '';

	private $_ignoreSSL_;
	private $_session_;
	private $_webpage_;
	private $_includeHeader_;
	private $_noBody_;
	private $_binaryTransfer_;

	private $_httpcode_;

	/**
     * 初始化curl
     *
     * @param boolean $followlocation 
     * @param integer $timeOut
     * @param integer $maxRedirecs
     * @param boolean $binaryTransfer
     * @param boolean $includeHeader
     * @param boolean $noBody
     */
	public function __construct($followlocation = true, $timeOut = 30, $maxRedirecs = 4, $binaryTransfer = false, $includeHeader = false, $noBody = false)
	{

		$this->_curl_ = curl_init();
        // 
		$this->_followlocation_ = $followlocation;
        // 超時限制
        $this->_timeout_        = $timeOut;
		$this->_maxRedirects_   = $maxRedirecs;
		$this->_includeHeader_  = $includeHeader;
		$this->_binaryTransfer_ = $binaryTransfer;
		$this->_noBody_         = $noBody;
		$this->_ignoreSSL_      = false;
	}

	/**
	 * 設定port
	 *
	 * @param integer $port
	 * @return void
	 */
	public function setPort($port = 80)
	{
		$this->_port_ = $port;
	}

	/**
     * 設定 Refere
     *
     * @param string $referer_url
     * @return void
     */
	public function setReferer(string $referer_url)
	{
		$this->_referer_ = $referer_url;
	}

	/**
     * 設定 UserAgent
     *
     * @param string $user_agent
     * @return void
     */
	public function setUserAgent(string $user_agent)
	{
		$this->_useragent_ = $user_agent;
	}

	/**
     * 設定 header
     *
     * @param array $http_header
     * @return void
     */
	public function setHttpHeader(array $http_header)
	{
		$this->_httpheader_ = $http_header;
	}

	/**
	 * 設定是否忽略憑證檢查
	 *
	 * @param boolean $ignoreSSL
	 * @return void
	 */
	public function setIgnoreSSL(bool $ignoreSSL)
	{
		$this->_ignoreSSL_ = $ignoreSSL;
	}

	/**
	 * 設定選項
	 * @return void
	 */
	protected function curlOption()
	{
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

		if ($this->_ignoreSSL_) {
			curl_setopt($this->_curl_, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($this->_curl_, CURLOPT_SSL_VERIFYPEER, 0);
		}

		curl_setopt($this->_curl_, CURLOPT_MAXREDIRS, $this->_maxRedirects_);

		// 輸出 HEADER 資訊
		if ($this->_includeHeader_) {
			curl_setopt($this->_curl_, CURLOPT_HEADER, TRUE);
		}

		// 要配合post使用
		if ($this->_noBody_) {
			curl_setopt($this->_curl_, CURLOPT_NOBODY, true);
		}
	}

	/**
	 * http Method GET
	 *
	 * @param string $url
	 * @return void
	 */
	public function get(string $url)
	{
		// 載入 option
		$this->curlOption();

		curl_setopt($this->_curl_, CURLOPT_URL, $url);

		$curldata = curl_exec($this->_curl_);
		$this->_httpcode_ = curl_getinfo($this->_curl_, CURLINFO_HTTP_CODE);

		return $curldata;
	}

	/**
     * * Http Method POST
	 * 發送POST請求時，HTTPHEADER 常見是用 "application/x-www-form-urlencoded"
	 * 這是html 表單最常使用的方式
     *
     * @param string $url
     * @param array $param 要傳送的資料
     * @param boolean $raw 以原始數據方式傳送
     * @return void
     */
	public function post(string $url, $param = array(), $raw = false)
	{
		// 載入 option
		$this->curlOption();

		curl_setopt($this->_curl_, CURLOPT_URL, $url);
		//啟用POST
		curl_setopt($this->_curl_, CURLOPT_POST, true);

		if (count($param) >= 1 && gettype($param) == 'array') {
			curl_setopt($this->_curl_, CURLOPT_POSTFIELDS, http_build_query($param));
		}

		if ($param != '' && $raw == true) {
			curl_setopt($this->_curl_, CURLOPT_POSTFIELDS, $param);
		}

		$curldata = curl_exec($this->_curl_);
		$this->_httpcode_ = curl_getinfo($this->_curl_, CURLINFO_HTTP_CODE);

		return $curldata;
	}

	/**
	 * 回傳http statuscode(狀態碼)
	 *
	 * @return integer
	 */
	public function getHttpCode()
	{
		return $this->_httpcode_;
	}

	public function __destruct()
	{
		curl_close($this->_curl_);
	}
}
