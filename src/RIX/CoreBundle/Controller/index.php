<?php
require_once('Vimeo.php');
//session_start();
/**
 *   Copyright 2013 Vimeo
 *
 *   Licensed under the Apache License, Version 2.0 (the "License");
 *   you may not use this file except in compliance with the License.
 *   You may obtain a copy of the License at
 *
 *       http://www.apache.org/licenses/LICENSE-2.0
 *
 *   Unless required by applicable law or agreed to in writing, software
 *   distributed under the License is distributed on an "AS IS" BASIS,
 *   WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *   See the License for the specific language governing permissions and
 *   limitations under the License.
 */
$vimeo = new Vimeo('b934c6d8a38fc37a2958bc730ba0259d74ebe3a9', '7Pdi+kUkEqz6R7i4SblRTxpFm8tGrmAMLtQ1lIvL9ThKhiGm+aXuiIiOj0vPGTdy5r8BCipZmkBVVwUAkPS8Kj4j32X9zdGVNkPCRSoGua52eBObaoV1MnORJdUJhAvd', '44c4932c50c0cdce24e06d86d36c85f0');
$videos = $vimeo->request("/tags/java/videos");
$videosNum = $videos["body"]["total"];
$pageNumbers = $videos["body"]["paging"]["last"];
echo 'Numarul total de inregistrari gasite este '. $videosNum . " structurate in ".$pageNumbers." pagini. <br><br>";
for ($i = 0; $i <= 24; $i++){
	echo($i. '. ' . $videos["body"]["data"][$i]["name"]."<br>");
	echo('Website: ' . $videos["body"]["data"][$i]["link"]."<br>");
	echo('Description: ' . $videos["body"]["data"][$i]["description"]."<br>");
	echo('Preview: ' . $videos["body"]["data"][$i]["pictures"]["sizes"][2]["link"]."<br>");
	echo('User: ' . $videos["body"]["data"][$i]["user"]["name"]. ' aka ' . $videos["body"]["data"][$i]["user"]["link"] ."<br>");
	echo('Date created: ' . $videos["body"]["data"][$i]["created_time"]."<br>");
	echo('Tags: ');
	$tagsNumber = count($videos["body"]["data"][$i]["tags"]);
	for($j=0; $j <=$tagsNumber - 1;$j++)
		echo($videos["body"]["data"][$i]["tags"][$j]["name"].' ');
	echo ("<br>");
	echo('Video: <textarea readonly>' . $videos["body"]["data"][$i]["embed"]["html"]."</textarea> <br><br><br>");
}
?>