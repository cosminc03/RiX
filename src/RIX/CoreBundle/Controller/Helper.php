<?php
namespace RIX\CoreBundle\Controller;
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
			if(strchr($tag,$k)){
				return true;
			}
		return false;
    }

	private function XMLtoArray($data)
	{
		$parser = xml_parser_create();
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
						while($i < count($val) && $this->checkTag($values[$val[$i]]["value"])){
							$aux .= $values[$val[$i]]["value"] . ' ';
							$i++;
						}
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

	public function Helper() {
		error_reporting( error_reporting() & ~E_NOTICE );
	}

	private function get_data($call,$params) {
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
		$data = $this->XMLtoArray($this->get_data("get_slideshows_by_tag","&tag=$tag&offset=$offset&limit=$limit&detailed=1"));
		return $data;
	}
}
?>