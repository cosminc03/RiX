<?php
define ('URL', 'http://dev.rix.ro/app_dev.php/api/get_article_by_tag/java');
echo '<p>Invocam serviciul Web de la <code>' . URL . '</code></p>';
// initializam cURL
$c = curl_init ();
// stabilim URL-ul resursei dorite -- in acest caz, serviciul Web invocat
curl_setopt ($c, CURLOPT_URL, URL);
// rezultatul cererii va fi disponibil ca sir de caractere
// intors de apelul curl_exec()
curl_setopt ($c, CURLOPT_RETURNTRANSFER, 1);
// preluam resursa oferita de server (aici, un document XML)
$res = curl_exec ($c);
// inchidem conexiunea cURL (i.e., conexiunea cu serverul Web)
curl_close ($c);
echo '<p>Raspunsul oferit de serviciu:</p>';
echo '<pre>' . htmlentities ($res) . '</pre>';
?>