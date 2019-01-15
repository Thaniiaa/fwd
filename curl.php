<?php
class MCurl
{
    protected $_urls;
    protected $_result;
    protected $_timeout;
   
    public function __construct($timeout = 5, $urls = false)
    {
        $this->_timeout = $timeout;
		$this->_urls = $urls ? $urls : array();
        $this->_result = array();
		$this->_headers = $headers ? $headers : array();
		$this->_gz = false;
    }
   
    public function setTimeout($timeout)
    {
        $this->_timeout = $timeout;
    }
       
    public function setUrls($urls,$body=false,$header=false,$viewheader=false)
    {
        $this->_urls = $urls;
		$this->_body = $body;
		$this->_header = $header;
		$this->_viewheader = $viewheader;
    }
   
    public function getResults()
    {
        $this->scan();
		$hae = $this->_result;
		unset($this->_result);
        return $hae;
    }
	
	public function gzipDecode($gz = false){
		$this->_gz = $gz ? $gz : true;
	}
	
    public function scan()  
    {
        $curl = array();  
        $mh = curl_multi_init();
        foreach ($this->_urls as $id => $url)
        {
			$urls = $url;
			if($this->_body) $urls = $url[0];
            $curl[$id] = curl_init();
            curl_setopt($curl[$id], CURLOPT_URL, $urls);
			if($this->_gz) curl_setopt($curl[$id], CURLOPT_ENCODING, 'gzip, deflate');
            if($this->_header) curl_setopt($curl[$id], CURLOPT_HTTPHEADER, $url[2]);
			if($this->_body){
				curl_setopt($curl[$id], CURLOPT_POSTFIELDS, $url[1]);
				curl_setopt($curl[$id], CURLOPT_POST, 1);
			}
            curl_setopt($curl[$id], CURLOPT_HEADER, $this->_viewheader);
            curl_setopt($curl[$id], CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl[$id], CURLOPT_TIMEOUT, $this->_timeout);
            curl_multi_add_handle($mh, $curl[$id]);
        }
        $running = null;
        do curl_multi_exec($mh, $running);
        while($running > 0);
        foreach($curl as $id => $c)
        {
            $this->_result[$id] = curl_multi_getcontent($c);
            curl_multi_remove_handle($mh, $c);
        }
        curl_multi_close($mh);
    }
}
$MCurl = new MCurl();