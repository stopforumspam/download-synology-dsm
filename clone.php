<?php
function getLinks($url, $dir) {
    $html = file_get_contents($url);
    $dom = new DOMDocument;
    @$dom->loadHTML($html);
    foreach ($dom->getElementsByTagName('a') as $node) {
        if (strpos($node->getAttribute("href"), ".pat") !== false || strpos($node->getAttribute("href"), ".zip") !== false) {

            echo "FILE: " . $node->getAttribute("href")."\n" ; 

            $remote = parse_url($node->getAttribute("href"));
            $fullpath = explode("/", $remote["path"]);
            $filename = urldecode(array_pop($fullpath));
            $path = "download/Os/DSM/$dir/";

            @mkdir($path, 0777, true);
            $dest = "$path$filename";            
            if (!file_exists($dest)) {
                echo "downloading " . $node->getAttribute("href") . " to $dest \n";
                $fh = fopen($dest, "w");
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_USERAGENT,"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:109.0) Gecko/20100101 Firefox/113.0 ");             
                curl_setopt($ch, CURLOPT_URL, $node->getAttribute("href"));
                curl_setopt($ch, CURLOPT_FILE, $fh);
                curl_exec($ch);
                curl_close($ch); 
            } else {
                echo "skipping download\n";
            }
        } else
        if (strpos($node->getAttribute("href"), "/download/Os/DSM/") !== false) {
            echo "DIR: " . $node->getAttribute("href")."\n";
            $url = "https://archive.synology.com" .$node->getAttribute("href");
            getLinks($url,  $node->nodeValue);
        }
    }
}
getLinks("https://archive.synology.com/download/Os/DSM");