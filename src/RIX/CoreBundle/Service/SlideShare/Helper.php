<?php
namespace RIX\CoreBundle\Service\SlideShare;
// This is the PHP class that can be used for accessing the API

class Helper{
	//params for API requests
	private $key= 'Sa3RkM2v';
	private $secret = 'Trl6rSZn';
	private $user;
	private $password;
	private $apiurl='https://www.slideshare.net/api/2/';

	private function checkTag($tag){
		for ($k='a' ; $k <= 'z' ; $k++)
			if(strchr($tag,$k) || strchr($tag,strtoupper($k)))
				return true;
		return false;
	}

	public function XMLtoArray($data)
	{
		$parser = xml_parser_create("UTF-8");
		xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_free($parser);
		foreach ($tags as $key=>$val) {
			if(strtoupper($key) == "SLIDESHARESERVICEERROR") {
				$finarr[0]["Error"]="true";
				$finarr[0]["Message"]=$values[$tags["MESSAGE"][0]]["value"];
				return $finarr;
			}
			if ((strtolower($key) == "description") || (strtolower($key) == "username") || (strtolower($key) == "title")
				|| (strtolower($key) == "url") || (strtolower($key) == "embed")) {
				for($i = 0;$i < count($val);$i++) {
					$finarr[$i][$key]=$values[$val[$i]]["value"];
				}
			}
			else if(strtolower($key) == "created"){
				for($i = 0;$i < count($val);$i++) {
					$finarr[$i][$key] = substr($values[$val[$i]]["value"],0,10);
				}
			}
			else if(strtolower($key) == "thumbnailxlargeurl"){
				for($i = 0;$i < count($val);$i++) {
					$finarr[$i][$key]=ltrim($values[$val[$i]]["value"],'/');
				}
			}
			else if(strtolower($key) == "tag"){
				$countt = 0;
				for($i=0;$i < count($val)-1;){
					if($this->checkTag($values[$val[$i]]["value"])){
						$aux = '';
						$maxthree = 0;
						while($i < count($val) && $this->checkTag($values[$val[$i]]["value"])){
							if($maxthree < 2) {
								$aux .= $values[$val[$i]]["value"] . ', ';
								$maxthree++;
							}
							$i++;
						}
						$aux = substr($aux,0,strlen($aux)-2);
						$finarr[$countt][$key]=$aux;
						$countt++;
					}
					else $i ++;
				}
			}
			else {
				continue;
			}
		}
		return $finarr;
	}

	private function XMLtoArray4Tag($data,$tag)
	{
		$parser = xml_parser_create("ISO-8859-1");
		xml_parse_into_struct($parser, $data, $values, $tags);
		xml_parser_free($parser);
		foreach ($tags as $key=>$val) {
			if(strtoupper($key) == "SLIDESHARESERVICEERROR") {
				$finarr[0]["Error"]="true";
				$finarr[0]["Message"]=$values[$tags["MESSAGE"][0]]["value"];
				return $finarr;
			}
			if ((strtolower($key) == "description") || (strtolower($key) == "username")
				|| (strtolower($key) == "url") || (strtolower($key) == "embed") || (strtolower($key) == "id")) {
				for($i = 0;$i < sizeof($val);$i++) {
					$finarr[$i][$key]=$values[$val[$i]]["value"];
				}
			}
			else if(strtolower($key) == "title"){
				for($i = 0;$i < count($val);$i++) {
					$finarr[$i][$key]=strlen($values[$val[$i]]["value"]) <= 40 ? $values[$val[$i]]["value"] : substr($values[$val[$i]]["value"],0,40) . '...';
				}
			}
			else if(strtolower($key) == "created"){
				for($i = 0;$i < count($val);$i++) {
					$finarr[$i][$key] = substr($values[$val[$i]]["value"],0,10);
				}
			}
			else if(strtolower($key) == "thumbnailxlargeurl"){
				for($i = 0;$i < count($val);$i++) {
					$finarr[$i][$key]=ltrim($values[$val[$i]]["value"],'/');
				}
			}
			else if(strtolower($key) == "tag"){
				$countt = 0;
				for($i=0;$i < count($val)-1;){
					if($this->checkTag($values[$val[$i]]["value"])){
						$aux = '';
						$maxthree = 0;
						while($i < count($val) && $this->checkTag($values[$val[$i]]["value"])){
							if($maxthree < 2 && strcmp($tag,$values[$val[$i]]["value"])) {

								$aux .= ltrim($values[$val[$i]]["value"],'#') . ', ';
								$maxthree++;
							}
							$i++;
						}
						$aux = substr($aux,0,strlen($aux)-2);
						$finarr[$countt][$key]=$aux;
						$countt++;
					}
					else $i ++;
				}
			}
			else if(strtolower($key) == "count") {
				$finarr[0][$key]=$values[$val[0]]["value"];
			}
			else{
				continue;
			}
		}
		return $finarr;
	}

	public function __construct() {
		error_reporting( error_reporting() & ~E_NOTICE );
	}

	public function get_data($call,$params) {
		$ts=time();
		$hash=sha1($this->secret.$ts);
		try {
			$res=file_get_contents($this->apiurl.$call."?api_key=$this->key&ts=$ts&hash=$hash".$params);
		} catch (Exception $e) {
			// Log the exception and return $res as blank
		}
		return utf8_encode($res);
	}

	/* Get all the slide information in a simple array */
	public function get_slideInfo($id) {
		$data=$this->XMLtoArray($this->get_data("get_slideshow","&slideshow_id=$id&detailed=1"));
		$strfindpos = strrpos($data[0]['EMBED'],'</iframe>');
		$data[0]['EMBED'] = substr($data[0]['EMBED'],0,$strfindpos+9);
		return $data[0];
	}

	public function count_slideUser($user) {
		$xml=new SimpleXMLElement($this->get_data("get_slideshows_by_user","&username_for=$user&offset=0&limit=1"));
		return $xml->count();
	}
	/* Get all the user's slide information  in a simple multi-dimensional array */
	public function get_slideUser($user,$offset=0,$limit=0) {
		$data = $this->XMLtoArray($this->get_data("get_slideshows_by_user","&username_for=$user&offset=$offset&limit=$limit"));
		return $data;
	}
	public function count_slideTag($tag) {
		$xml=new SimpleXMLElement($this->get_data("get_slideshows_by_tag","&tag=$tag&offset=0&limit=1"));
		return $xml->count();
	}
	/* Get all the tags's slide information  in a simple multi-dimensional array */
	public function get_slideTag($tag,$offset=0,$limit=0) {
		$tag2 = str_replace(' ','-',$tag);
		$tag2 = str_replace('.','-',$tag2);
		$data = $this->XMLtoArray4Tag($this->get_data("get_slideshows_by_tag","&tag=$tag2&offset=$offset&limit=$limit&detailed=1"),$tag);
		for ($i = 0; $i < sizeof($data); $i ++){
			$tags = strlen($data[$i]['TAG']) ? $tag . ', '.$data[$i]['TAG'] : $tag;
			$data[$i]['TAG'] = $tags;
		}
		return $data;
	}
}
?>