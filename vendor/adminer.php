<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.1
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.1.1";error_reporting(24575);set_error_handler(function($Yc,$ad){return!!preg_match('~^Undefined (array key|offset|index)~',$ad);},E_WARNING|E_NOTICE);$Cd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Cd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$_l=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($_l)$$X=$_l;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Pb=adminer()->credentials();$J=Driver::connect($Pb[0],$Pb[1],$Pb[2]);return(is_object($J)?$J:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$Af=substr($t,-1);return
str_replace($Af.$Af,$Af,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($_a,$w,$i=null){return($_a&&array_key_exists($w,$_a)?$_a[$w]:$i);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($U){return
in_array($U,idx(driver()->structuredTypes(),lang(0),array()));}function
full_type_sql(array$k){$U=$k["type"];return(is_user_type($U)?idf_escape($U).substr($k["full_type"],strlen($U)):$k["full_type"]);}function
is_searchable(array$k,array$X){if(!isset($k["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$X["op"]))return
true;$U=$k["type"];$vj=$X["val"];$Qa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Qa~",$U))return
false;if(preg_match(number_type(),$U)){$Zg='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$Zg.(preg_match('~IN$~',$X["op"])?"( *, *$Zg)*":'').'$~',$vj);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$vj);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$vj);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$vj);return
true;}function
remove_slashes(array$Vl,$Cd=false){$J=array();foreach($Vl
as$w=>$X)$J[stripslashes($w)]=(is_array($X)?remove_slashes($X,$Cd):($Cd?$X:stripslashes($X)));return$J;}function
bracket_escape($t,$Ja=false){static$hl=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Ja?array_flip($hl):$hl));}function
url_escape($Q){static$hl=array();if(!$hl){$hl=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$cb)$hl[$cb]=sprintf('%%%02X',ord($cb));for($r=0;$r<256;$r++){if($r<32||$r>126)$hl[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$Q,$hl);}function
min_version($Yl,$Vf="",$f=null){$f=connection($f);$Jj=$f->server_info;if($Vf&&preg_match('~([\d.]+)-MariaDB~',$Jj,$A)){$Jj=$A[1];$Yl=$Vf;}return$Yl&&version_compare($Jj,$Yl)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($vh,$Y){return(function_exists('ini_set')?\ini_set($vh,$Y):false);}function
ini_bool($Ue){$X=ini_get($Ue);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Ue){$X=ini_get($Ue);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$Hh){$Yf=(int)ini_get("max_input_vars");return($Yf?(int)floor(($Yf-$Hh)/$K):0);}function
max_input_vars_error(){$Ue="max_input_vars";return
lang(1,"<b>$Ue = ".ini_get($Ue)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Xl,$O,$V,$F){$_SESSION["pwds"][$Xl][$O][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$k=0,$Bb=null){$Bb=connection($Bb);$I=$Bb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$k]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$Mj=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Mj)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$j="<p class='error'>"){$Bb=connection($f);$J=array();$I=$Bb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$J;}function
unique_array($K,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]){$J=array();foreach($u["columns"]as$w){if(!isset($K[$w]))continue
2;$J[$w]=$K[$w];}return$J;}}}function
where_function($Ud,$c,array$k){if($Ud=="md5")return
driver()->md5($c,$k)?:$c;return(in_array($Ud,driver()->functions)||in_array($Ud,driver()->grouping)?apply_sql_function($Ud,$c):$c);}function
where(array$Z,array$l=array()){$J=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$c=idf_escape($w);$k=idx($l,$w,array());$xd=$k["type"];$hf=$k&&(is_blob($k)||preg_match('~binary~',$xd));$J[]=$c.($hf&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$xd=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($xd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($k,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$xd)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$J[]=idf_escape($w)." IS NULL";foreach((array)$Z["col"]as$r=>$pb){$X=idx($Z["val"],$r);$J[]=where_function(idx($Z["fun"],$r),idf_escape($pb),idx($l,$pb,array())).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$J);}function
where_columns(array$l){$J=array();foreach((array)$_GET["null"]as$w)$J[$w]=true;foreach(array_keys((array)$_GET["where"])as$w)$J[bracket_escape($w,true)]=true;foreach((array)$_GET["col"]as$pb)$J[$pb]=true;return
array_intersect_key($J,$l);}function
where_check($X,array$l=array()){parse_str($X,$fb);remove_slashes(array(&$fb));return
where($fb,$l);}function
where_link($r,$c,$Y,$sh="="){$ph=($Y!==null?$sh:"IS NULL");return"&where[$r][col]=".url_escape($c).($ph!=first(adminer()->operators())?"&where[$r][op]=".url_escape($ph):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$d,array$l,array$N=array()){$J="";foreach($d
as$w=>$X){if($N&&!in_array(idf_escape($w),$N))continue;$Aa=convert_field($l[$w]);if($Aa)$J
.=", $Aa AS ".idf_escape($w);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($B,$Y,$Kf=2592000){header("Set-Cookie: $B=".rawurlencode($Y).($Kf?"; expires=".gmdate("D, d M Y H:i:s",time()+$Kf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($Il,$Gb){$http_response_header=null;$Zc=array();set_error_handler(function($Yc,$j)use(&$Zc){$Zc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$J=file_get_contents($Il,false,$Gb);restore_error_handler();$qe=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($qe,0,''),$A)?$A[1]:''),(array)$qe,($J===false?implode("\n",$Zc):''),);}function
json_decode_exact($pf){$pf=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$pf);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$pf));}function
json_scalar($X){return(is_string($X)&&substr($X,0,1)=="\1"?substr($X,1):$X);}function
json_encode_exact($X,$Fd=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($X,$Fd));}function
get_settings($Jb){parse_str($_COOKIE[$Jb],$Nj);return$Nj;}function
get_setting($w,$Jb="adminer_settings",$i=null){return
idx(get_settings($Jb),$w,$i);}function
save_settings(array$Nj,$Jb="adminer_settings"){$Y=http_build_query($Nj+get_settings($Jb));cookie($Jb,$Y);$_COOKIE[$Jb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Id=false){$Ll=ini_bool("session.use_cookies");if(!$Ll||$Id){session_write_close();if($Ll&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Xl,$O,$V,$h=null){$Hl=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($Xl=='mssql'||$Xl=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Hl,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Xl!="server"||$O!=""?url_escape($Xl)."=".url_escape($O)."&":"")."username=".url_escape($V).($h!=""?"&db=".url_escape($h):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_,$og=null){if($og!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_!==null?$_:$_SERVER["REQUEST_URI"]))][]=$og;}if($_!==null){if($_=="")$_=".";header("Location: $_");exit;}}function
query_redirect($H,$_,$og,$Mi=true,$hd=true,$sd=false,$Uk=""){if($hd){$gk=microtime(true);$sd=!connection()->query($H);$Uk=format_time($gk);}$ak=($H?adminer()->messageQuery($H,$Uk,$sd):"");if($sd){adminer()->error
.=adminer()->error().$ak.script("messagesPrint();")."<br>";return
false;}if($Mi)redirect($_,$og.$ak);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");}function
queries($H){remember_query($H);return
connection()->query($H);}function
apply_queries($H,array$T,$bd='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$bd($R)))return
false;}return
true;}function
queries_redirect($_,$og,$Mi){$Hi=implode("\n",Queries::$queries);$Uk=format_time(Queries::$start);return
query_redirect($Hi,$_,$og,$Mi,false,!$Mi,$Uk);}function
format_time($gk){return
lang(2,max(0,microtime(true)-$gk));}function
relative_uri($Hl=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Hl?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Mh=""){return
substr(preg_replace("~(?<=[?&])($Mh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$ec=false){$zd=$_FILES[$B];if(!$zd)return
null;foreach($zd
as$w=>$X)$zd[$w]=(array)$X;$J=array();foreach($zd["error"]as$w=>$j){if($j)return$j;$m=$zd["name"][$w];$cl=$zd["tmp_name"][$w];$Eb=file_get_contents($ec&&preg_match('~\.gz$~',$m)?"compress.zlib://$cl":$cl);if($ec){$gk=substr($Eb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$gk))$Eb=iconv("utf-16","utf-8",$Eb);elseif($gk=="\xEF\xBB\xBF")$Eb=substr($Eb,3);}$J[]=array($m,$Eb);}return$J;}function
get_file($w,$ec=false,$lc=""){$Bd=get_files($w,$ec);if(!is_array($Bd))return$Bd;$J='';foreach($Bd
as$zd){$Eb=$zd[1];$J
.=$Eb;if($lc)$J
.=(preg_match("($lc\\s*\$)",$Eb)?"":$lc)."\n\n";}return$J;}function
upload_error($j){$gg=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(3).($gg?" ".lang(4,$gg):""):lang(5));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$A);$Rj=strlen($A[3]);$J=number_format($X,0,".","");$J=preg_replace('~\B(?=(\d{'.(strlen($A[2])?:$Rj).'})*\d{'.$Rj.'}$)~',$A[1],$J);return
strtr($J,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$w){$X=idx($S,$w,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$xa=($w=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($xa?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$td=false){$J=table_status($R,$td);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$n){foreach($n["source"]as$X)$J[$X][]=$n;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$B=bracket_escape($w,true);$J[$B]=array("field"=>$B,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($B==driver()->primary),);}return$J;}function
dump_headers($De,$Eg=false){$J=adminer()->dumpHeaders($De,$Eg);$Jh=$_POST["output"];if($Jh!="text"||$J=="tar"){$zb=($Jh!="text"&&$Jh!="file"&&preg_match('~^[0-9a-z]+$~',$Jh)?".$Jh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($De).".$J$zb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$ql=$_POST["format"]=="tsv";foreach($K
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($ql?'\t':'[,;]|^$').'~',$X))$K[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($ql?"\t":";")),$K)."\r\n";}function
parse_csv($Sb,$Dj){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Sb,$Wf);foreach($Wf[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$Dj]*)$Dj~",$K.$Dj,$Xf);$J[]=$Xf[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($p,$c){return($p?($p=="unixepoch"?"DATETIME($c, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$o=@fopen($m,"c+");if(!$o)return;@chmod($m,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Wb){rewind($o);fwrite($o,$Wb);ftruncate($o,strlen($Wb));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$_a){return
reset($_a);}function
password_file($Mb){$m=get_temp_dir()."/adminer.key";if(!$Mb&&!file_exists($m))return'';$o=file_open_lock($m);if(!$o)return'';$J=stream_get_contents($o);if(!$J){$J=rand_string();file_write_unlock($o,$J);}else
file_unlock($o);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$z,array$k,$Sk,array$fi=array()){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$tf=array();foreach($X
as$W)$tf+=array_fill_keys(array_keys($W),null);foreach(array_keys($tf)as$rf)$J
.="<th>".h($rf);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($tf,$W)as$Rl)$J
.="<td>".select_value($Rl,$z,$k,$Sk,$fi);}}else{foreach($X
as$rf=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($rf):"")."<td>".select_value($W,$z,$k,$Sk,$fi);}return"<table>$J</table>";}if(!$z)$z=adminer()->selectLink($X,$k);if($z===null){if(is_mail($X))$z="mailto:$X";if(is_url($X))$z=$X;}$X=driver()->value($X,$k);$J=adminer()->editVal($X,$k);if($J!==null){if(!is_utf8($J))$J="\0";elseif($Sk!=""&&is_shortable($k))$J=shorten_utf8($J,max(0,+$Sk),"",$fi);else$J=highlight_matches($J,$fi);}return
adminer()->selectVal($J,$z,$k,$X);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$k){return$k["auto_increment"]&&(JUSH=="mssql"||$k["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($Oc){$Ca='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Cc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$ei="$Ca+(\\.$Ca+)*@($Cc?\\.)+$Cc";return
is_string($Oc)&&preg_match("(^$ei(,\\s*$ei)*\$)i",$Oc);}function
is_url($Q){$Cc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Cc?\\.)+$Cc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_ipv6($ja){$q='[\da-f]{1,4}';$gf='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($q:){7}$q|($q:){6}$gf|(($q:)*$q)?::(($q:)*($q|$gf))?)$~iD",$ja);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($_e){return(strpos($_e,":")!==false?"[$_e]":$_e);}function
server_parts(array$Yh){return
array("scheme"=>(string)$Yh["scheme"],"host"=>(string)$Yh["host"],"port"=>(string)$Yh["port"],"socket"=>(string)$Yh["socket"],"path"=>(string)$Yh["path"],);}function
parse_server($O){if($O=="")return
server_parts(array());if($O[0]==":"&&!is_ipv6($O)){$Zi=substr($O,1);if(preg_match('~^\d+$~D',$Zi))return
server_parts(array("port"=>$Zi));return(preg_match('~^/[-\w.:/]*$~D',$Zi)?server_parts(array("socket"=>$Zi)):null);}$tj="";if(preg_match('~^([-+.\w]+)://~',$O,$A)){$tj=strtolower($A[1]);$O=substr($O,strlen($A[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$O,$A))return(is_ipv6($A[1])?server_parts(array("scheme"=>$tj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);if(is_ipv6($O))return
server_parts(array("scheme"=>$tj,"host"=>$O));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$O,$A))return
server_parts(array("scheme"=>$tj,"host"=>$A[1],"port"=>$A[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$O,$A)?server_parts(array("scheme"=>$tj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);}function
count_rows($R,array$Z,$if,array$q){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($if&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$H":"SELECT COUNT(*)".($if?" FROM (SELECT 1$H GROUP BY ".implode(", ",$q).") x":$H));}function
slow_query($H){$h=adminer()->database();$Vk=adminer()->queryTimeout();$Sj=driver()->slowQuery($H,$Vk);$f=null;if(!$Sj&&support("kill")){$f=connect();if($f&&($h==""||$f->select_db($h))){$uf=number(get_val(connection_id(),0,$f));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$uf&token=".get_token()."'); }, 1000 * $Vk);");}}ob_flush();flush();$J=@get_key_vals(($Sj?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Ki=rand(1,1e6);return($Ki^$_SESSION["token"]).":$Ki";}function
verify_token(){list($dl,$Ki)=explode(":",$_POST["token"]);return($Ki^$_SESSION["token"])==$dl&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$rc=""){$ta=array_flip(str_split(compress_alphabet()));$x=strlen($Q);$Tl=($x?13*($x-1)/2-$ta[$Q[0]]:0);$Qa="";$Zi=0;$aj=0;for($r=1;$r<$x;$r+=2){$Zi=($Zi<<13)+$ta[$Q[$r]]*93+$ta[$Q[$r+1]];$aj+=13;while($aj>=8&&$Tl>=8){$aj-=8;$Tl-=8;$Qa
.=chr($Zi>>$aj);$Zi&=(1<<$aj)-1;}}if($Qa=="")return"";if($rc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$rc)),$Qa,ZLIB_FINISH);return($rc==""&&function_exists('gzinflate')?gzinflate($Qa):inflate($Qa,$rc));}function
inflate($Qa,$rc=""){$Hf=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$If=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$vc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$xc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$rc;$G=0;do{$Dd=inflate_bits($Qa,$G,1);$U=inflate_bits($Qa,$G,2);if(!$U){$G=($G+7)&~7;$x=inflate_bits($Qa,$G,16);$G+=16;$J
.=substr($Qa,$G>>3,$x);$G+=$x<<3;}else{if($U==1){$Qf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$yc=array_fill(0,30,5);}else{$Pf=inflate_bits($Qa,$G,5)+257;$wc=inflate_bits($Qa,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$ug=array_fill(0,19,0);$tg=inflate_bits($Qa,$G,4)+4;for($r=0;$r<$tg;$r++)$ug[$D[$r]]=inflate_bits($Qa,$G,3);$vg=inflate_table($ug);$Jf=array();while(count($Jf)<$Pf+$wc){$uk=inflate_symbol($Qa,$G,$vg);if($uk==16)$Jf=array_merge($Jf,array_fill(0,inflate_bits($Qa,$G,2)+3,end($Jf)));elseif($uk==17)$Jf=array_merge($Jf,array_fill(0,inflate_bits($Qa,$G,3)+3,0));elseif($uk==18)$Jf=array_merge($Jf,array_fill(0,inflate_bits($Qa,$G,7)+11,0));else$Jf[]=$uk;}$Qf=array_slice($Jf,0,$Pf);$yc=array_slice($Jf,$Pf);}$Rf=inflate_table($Qf);$_c=inflate_table($yc);while(($uk=inflate_symbol($Qa,$G,$Rf))!=256){if($uk<256)$J
.=chr($uk);else{$x=$Hf[$uk-257]+inflate_bits($Qa,$G,$If[$uk-257]);$zc=inflate_symbol($Qa,$G,$_c);$gh=strlen($J)-$vc[$zc]-inflate_bits($Qa,$G,$xc[$zc]);for($r=0;$r<$x;$r++)$J
.=$J[$gh+$r];}}}}while(!$Dd);return($rc==""?$J:substr($J,strlen($rc)));}function
inflate_bits($Qa,&$G,$Lb){$J=0;for($r=0;$r<$Lb;$r++){$J+=((ord($Qa[$G>>3])>>($G&7))&1)<<$r;$G++;}return$J;}function
inflate_table(array$Jf){$R=array();$ob=0;for($Ra=1;$Ra<=max($Jf);$Ra++){foreach($Jf
as$uk=>$x){if($x==$Ra){$R[$Ra][$ob]=$uk;$ob++;}}$ob<<=1;}return$R;}function
inflate_symbol($Qa,&$G,array$R){$ob=0;$Ra=0;do{$ob=($ob<<1)+inflate_bits($Qa,$G,1);$Ra++;}while(!isset($R[$Ra][$ob]));return$R[$Ra][$ob];}function
script($Xj,$gl="\n"){return"<script".nonce().">$Xj</script>$gl";}function
script_src($Il,$hc=false){return"<script src='".h($Il)."'".nonce().($hc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($cd,$ie,$ya=null){$za=array();foreach(array_slice(func_get_args(),2)as$X)$za[]=json_encode($X,256);return" data-on$cd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$ie(".implode(", ",$za).")")."'";}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$hb,$wf="",$b="",$nb="",$yf=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($hb?" checked":"").($wf==""&&$nb?" class='$nb'":"").($yf?" aria-labelledby='$yf'":"").$b.">";return($wf!=""?"<label".($nb?" class='$nb'":"").">$J".h($wf)."</label>":$J);}function
optionlist($C,$Aj=null,$Ml=false){$J="";foreach($C
as$rf=>$W){$xh=array($rf=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($rf).'">';$xh=$W;}foreach($xh
as$w=>$X)$J
.='<option'.($Ml||is_string($w)?' value="'.h($w).'"':'').($Aj!==null&&($Ml||is_string($w)?(string)$w:$X)===$Aj?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
group_system(array$Mg,$sj=false){$J=array();$vk=array();foreach($Mg
as$B){if($sj?driver()->isSystem(DB,$B):driver()->isSystem($B))$vk[]=$B;else$J[]=$B;}if($vk)$J[lang(8,'')]=$vk;return$J;}function
html_select($B,array$C,$Y="",$b="",$yf=""){static$wf=0;$xf="";if(!$yf&&substr($C[""],0,1)=="("){$wf++;$yf="label-$wf";$xf="<option value='' id='$yf'>".h($C[""]);unset($C[""]);}return"<select name='".h($B)."'".($yf?" aria-labelledby='$yf'":"")."$b>".$xf.optionlist($C,$Y)."</select>";}function
html_radios($B,array$C,$Y="",$Dj=""){$J="";foreach($C
as$w=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$Dj";return$J;}function
confirm($og=""){return
on('click','confirmClick',$og?:lang(9));}function
print_fieldset($s,$Gf,$bm=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$Gf</a>","</legend>","<div id='fieldset-$s'".($bm?"":" class='hidden'").">\n";}function
bold($Sa,$nb=""){return($Sa?" class='active $nb'":($nb?" class='$nb'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Tb){return" ".($E==$Tb?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$Di,array$He=array(),$vi=''){$J=false;foreach($Di
as$w=>$X){if(!in_array($w,$He)){if(is_array($X))hidden_fields($X,array(),$w);else{$J=true;echo
input_hidden(($vi?$vi."[$w]":$w),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Gl){$Gl=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Gl?on('submit','uploadProgress',ME."upload=$Gl",SESSION_NAME."=$Gl"):"");}function
file_input($b,$Zi=""){$ag="max_file_uploads";$bg=ini_get($ag);$gg="upload_max_filesize";$hg=ini_bytes($gg);$ri=ini_bytes("post_max_size");if($ri&&$ri<$hg){$gg="post_max_size";$hg=$ri;}$ig=ini_get($gg);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$bg,lang(10,"$ag = $bg"),$hg,lang(10,"$gg = $ig")).">$Zi":lang(11));}function
enum_input($U,$b,array$k,$Y,$Rc=""){preg_match_all("~".driver()->enumLength."~",$k["length"],$Wf);$vi=($k["type"]=="enum"?"val-":"");$hb=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($k["null"]&&$vi?"<label><input type='$U'$b value='null'".($hb?" checked":"")."><i>$Rc</i></label>":"");foreach($Wf[0]as$X){$X=stripcslashes(idf_unescape($X));$hb=(is_array($Y)?in_array($vi.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$b value='".h($vi.$X)."'".($hb?' checked':'').'>'.h(adminer()->editVal($X,$k)).'</label>';}return$J;}function
input(array$k,$Y,$p,$Ha=false,$Dl=false){$B=h(bracket_escape($k["field"]));echo"<td class='function'>";$Xc=driver()->enumLength($k);if($Xc){$k["type"]="enum";$k["length"]=$Xc;}$C=($k["type"]=="enum"||$k["type"]=="set");if(is_array($Y)&&!$p&&!$C)$p="json";$pf=($p=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($pf&&$Y!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=(is_array($Y)?json_encode($Y,128|64|256):json_encode_exact(json_decode_exact($Y),128|64|256));$Yi=($Dl&&is_identity_always($k));if($Yi&&!$_POST["save"])$p=null;$Vd=(isset($_GET["select"])||$Yi?array("orig"=>lang(12)):array())+adminer()->editFunctions($k);$b=" name='fields[$B]".($C?"[]":"")."'".($Ha?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$R=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($Vd[""])."<td>".adminer()->editInput($R,$k,$b,$Y);else{$ke=(in_array($p,$Vd)||isset($Vd[$p]));$Ed=0;foreach($Vd
as$w=>$X){if($w===""||!$X)break;$Ed++;}echo(count($Vd)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Vd,$p===null||$ke?$p:"")."</select>":h(reset($Vd)))."<td".($Ed&&count($Vd)>1?on('input','skipOriginal',$Ed):"").">";$We=adminer()->editInput($R,$k,$b,$Y);if($We!="")echo$We;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$b,$k,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($pf)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($Rk=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$Y)){if($Rk&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$ul=driver()->types();$sl=$ul[$k["type"]];$_a=preg_match('~\[]~',$k["full_type"]);if($_a)$jg=0;elseif(preg_match('~date|time|year~',$k["type"])){$ui=($k["length"]==""&&JUSH=="pgsql"?6:$k["length"]);$Pd=(preg_match('~time~',$k["type"])&&preg_match('~^[1-9]\d*$~',$ui)?$ui+1:0);$jg=($sl?$sl+$Pd:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$A))$jg=(preg_match("~binary~",$k["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$k["unsigned"]?1:0);else$jg=($sl?$sl+($k["unsigned"]?0:1):0);echo"<input".((!$ke||$p==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!$_a?" type='number'":"")." value='".h($Y)."'".($jg?" data-maxlength='$jg'":"").(preg_match('~char|binary~',$k["type"])&&$jg>20?" size='".($jg>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$k,$Y),(count($Vd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$t=bracket_escape($k["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$zd=get_file("fields-$t");if(!is_string($zd))return
false;return
driver()->quoteBinary($zd);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($k["auto_increment"]&&$Y=="")return
null;if($k["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($k,$Y,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Cj="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array(),$S)),1));if(!$I||$I->fetch_row()){$_i="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$Cj<li>".($I?$_i:"<p class='error'>$_i: ".adminer()->error())."\n";$Cj="";}}}echo($Cj?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($Rk,$Qj=0){return
on('mouseover','helpMouseover',$Rk,$Qj).on('mouseout','helpMouseout');}function
on_help_value($Ti="",$Xi=""){return
on('mouseover','helpValueMouseover',$Ti,$Xi).on('mouseout','helpMouseout');}function
edit_form($R,array$l,$K,$Dl,$j='',$H='',$Uk=''){$Ak=adminer()->tableName(table_status1($R,true));page_header(($Dl?lang(14):lang(15)),$j,array("select"=>array($R,$Ak)),$Ak);adminer()->editRowPrint($R,$l,$K,$Dl,$H,$Uk);if($K===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Mc=false;$hm=($Dl&&!isset($_GET["select"])?where_columns($l):array());$Hb=(count($hm)!=count($l));if(!$Hb)$hm=array();if(!$l)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ha=!$_POST;foreach($l
as$B=>$k){echo"<tr".($hm[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($B));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Vi))$i=$Vi[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$Y=($K!==null?($k["type"]=="set"&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$Dl&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$k);if(($Dl&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($Y,'',$k,null);else{$Mc=true;$p=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($Dl&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Dl&&$Y==$k["default"]&&preg_match('~^[\w.]+\(~',$Y))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p="now";}if($k["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Ha!==false)$Ha=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$Y,$p,$Ha,$Dl);if($Ha)$Ha=false;}}if(!fields($R)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Mc){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$Hb){$sc=($hm&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Dl?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$sc".($Dl?on('click','ajaxForm',lang(21)):"").">\n";}}echo($Dl?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($ei,$x){return
str_repeat("$ei{0,65535}",$x/65535)."$ei{0,".($x%65535)."}";}function
shorten_utf8($Q,$x=80,$qk="",array$fi=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$Q,$A);$x=strlen(isset($A[2])?$A[1]:preg_replace('~\n[^\n]*\z~',"\n",$A[1]));return
highlight_matches($Q,$fi,$x).$qk.(isset($A[2])?"":"<i>…</i>");}function
highlight_matches($Q,array$fi,$x=null){if($x===null)$x=strlen($Q);$J="";$G=0;if($fi&&@preg_match_all("((?|".implode("|",$fi)."))su",$Q,$Wf,PREG_OFFSET_CAPTURE)){foreach($Wf[0]as$A){list($Rk,$gk)=$A;if($Rk!=""&&$gk<$x){$Sc=min($gk+strlen($Rk),$x);$J
.=h(substr($Q,$G,$gk-$G))."<mark>".h(substr($Q,$gk,$Sc-$gk))."</mark>";$G=$Sc;}}}return$J.h(substr($Q,$G,$x-$G));}function
icon($Ce,$B,$Be,$Xk,$b=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($Xk)."' class='icon icon-$Ce".($B?"":" jsonly")."'$b><span>$Be</span></button>";}function
copy_icon(){$Kb=lang(23);return"<a href='' class='jsonly icon-copy' title='$Kb'><span>$Kb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('+c(<]iDp;+<8]XG-X#ETBP{IAOo`HAD0Z,$t2FfTr3g#Vd(TVf>Cx5+d&ycL<,9"B6]b"oPK(+THBssK@e0=xnaBRf;$]A0]jsW_*Ibe$(2;yd5/}P:0_3xBmwvnkq<ydKh3e?rW3UW8c6iorbru~.;FOKSUq)z0u4OH&MRf7i:
N[U_7;F1p9i(5s29CE=`-:Ze`.kn@9RF4QL-+YuW|6!U&>e-zoVW6?4.A8t`/B@/ELQUIrIU%7Kvs3S?k#p2;1H
X/y@s?AR7M+0t;gg~I=j9:,1HW;ic+?$`BB#$=qSuRwY!<DX3Ny>D#NN*xh]"#vZCW/M>Y@mG3*h%?kSmN=OT;f?cTPrvH,vnUVam2HYmY(?/@
0?NF?2Ol^+5I
U%GE]1O0$>ys`u;eY*<H,E)jU7$
/G"I;uWG$9)5gsRW{:{e~U~.8AE.5W!
iWp-~OBKj1jL_EwRB?Y&i9o<;$`HciPwu)}$:S%2WeZN_EB+TMeR~sZ$~6Tlb.PFzj2c5D6$IrS,VAG6-$YN,Jsp.hB-"5(?g%)]_85S;K}Q)!]Ug5W7nWvawCpy>%pke1%;C>,8*br=&cDBB+3bJLku@UA6Pj:[LNyDz#!]HdCU2Vp/Tlj+yr(,J,*Wh<pP&Q5qxHb/^oeg5%v+WD+:w2T0+%DHK8Z6Oq>bbmt"W9"Gv0Dk;uesBj-9`dThaOFtw)#)nPV3&XyFGk01&HN4/Q{y|&Q`8uS0E,^"<CZDxnmK?l<Ff_g^SD_u$Z@<+<tC&b^k)8;L,&z5t.6)K0^"X#G_Rn:/T#T(cmAla^WGNtZS4XlovO
"JS=mYRH05NTD)D$P<[|2YAPQ@>B#ogdeOCH)M?8)+*D+`r2kS@J7:27wU2&LL!0(7rXo)F2]gv#2me/LOGGPS`9P@Wp!c9C)IEitF`CKjug?LA"_w?5f5ERLbV%"Sekh?;mD9kN
Kgs*3=$EYJJoJm;SuFwOv5@2rMMTVhHs
L]vkS}w&YkWCkI/+1n,j:]OE&&-ng%CfmYK`SO(N(v3A:cVgy
Z)dKHyOq`7+i0G]M(r,[9,?ZYq
]/"%_Hv07O6xyQ^+0#-*3$SpP!ci1={S}nLm3E:8k`7A`ng!$yH-j]G/|y}d8Vhy6XZ5FNRiRhJ:f23:43;VJp2REp"2/PU]Gt#F?,).3Oi]/oy2tM5RMl*RzvJsW2/:.fEIoUE.
4l]"?S9(cOr~IfBV51Su7^`]oq&3b5HUl~B4/Sb^C7,cE.H-tQV*^R#rEmw+$qICNudLR<En:sNot2

KY)]Xs7@-;-#x`3qs]OY=[_lc=L3=|G6qr8vNfc@]Vm(F,&^2w_="t$w3x[VZM*ybGUsmQORjL@FP4&?
5C3C%d%,g6fMf@kAN>(<M)9lHg<(aowp0%0gvB
>uekZH[Qhb3m3EZu69I0Z3Sk!}C,Cs`CJNb|ho&F+_Fv1V*9@fd39
&)f27Wk->IL*%gW1AqC,hV&i=^gG(P&
jb8I5PFUY-4Y_YjN@_kBFm$)`,?gA<HJm&6&N`7b69`OSgA1e]O:Jd%MEx>s2|
d%"mb?yp
C1+?Ml&im[ZB*N!c%e=]R
6B%O;a,NRqW|U}tM;5Z6t7o/?c>a9WsG1Tk!?#]gn|vgx{qBT_505u#2(M@LqfO(RlG=I)aR6fMs]
nyL/y6bx-,d|UacKJhi=PhxG2`Qhl5=kK?AT_AUf&(NI!:9i)TXDh`9<_N>d$}d?99epf"Lte7lxQz/KD0&
ZA8jpQrjlcQ`!?dHd(6IFPRu1E.&@RfvL"p*6zsdlUE%#5/[>7_)D3jrqi%&e%U492k}9j(NS]G2KOF?I*ib%:`c)lY)b}l`asY{0YvMx=TkbBq:&ne?mKPtlwO9<i6[J=Jvib,r+Fh4>y%C9%Yf)_/A$Z=)PHmQN[3@8SPov"@_3L7^5<?Dc?#_LK9z`l1?RA7mfD#{AcNbJjBY6}GCA*S
jaGHPp.*:(DO&{E97kVgEB_Mj
eyxuy@#C.PNGx>5*Frh_a_R?jDEH
0SJj74wk2(zJX$+G~tIe<)cUp$,i.@m:h$uqJkHp`rw5Oi^B#;af$OYt3F5ljH4E)n
D.E35kH:ddpC<:o)_ZTHo`JxY;Qo/~4cD]Kka#&Jg*
y8e"5(G+TB3VllVi"P,;M4*yU*:INGrm[E8&;fe79XPN-#HZv5M?*PHeu.j&qH`E)#ewBDs*XVW1sO4U^:"lWmS1I>hX!E/xZ:hSZq%;>_)s>QJx!o{4A%]JSThrG%Aq<$N00!#oQX&8YX@`nJxO-^f-2`Z#9H&@%7^<DA?mq`x3NL0"#LEPiR>]RVtp;MmL/xD4|W(@Q5=oF=5mMmOh/Ed]Sn#e4w!Xa3S0b5Zs2b4[JPU>Y-Q_NdQDebkvBS_`_2,Kmh/dlNO,=7^iJKsDWC+Y}eCrN]v7WqiU|@~@STU"M@~Z[ZQJfm!ZJ=,hW_1eaXa:~*LCm$prSpmT^XnJF)]#[MQtoKXlVgoIg#irnY4IhC=FVmq=3*Yv8]G>Mm=I"2/G")dC3B*e8=5rlIaC%)KY<2qP_N!X`(xkecZi(l?<@TCJAua%LjXkPpuSBqc%1"{/VvHRN]7dh00ywYgc4M/2.9$N-_EFZ=#GUk@,3jn5zb@70neZ0+4dd?x*0kYA~rA!AKSR.)6S~whrt^m2NhrAvS`$=p)^aw:ss1zm[bmvTR?tU->o$OHx",{x
vU@iBfpiWCxF%}yCyf`rpPj5N%[zq+ww-"f!bm,qDw*~l4Q$y|vWbth!KtKd!<p&2<BU,[sQmx7pn}fhC^bKwW4>csrq@JyewRu|l7Ko>ggj>ayQr+wB9$qURwa"&%IN+g5arv+VyAd"lDSyM|Pi5Y*5K<uvJ778nz<2xCBq&]*7p+X);jN9`i
o`,]_p5MuX3RtD*CcKdl&)hNr;rJ(d}KjWtTq]NRh(Xpx$ql^bW(3*]%@M)BntuCI]vqpX|v[,gS`tfFX)bclBkp~:F0/Ja^>h1q+MZ-U_2J4WqqJM;oN-/r/E{Qh9U1c@v[7r.C_i_Tje
0J`0("b_3TT`&!Lb&)u:F|(8mo,ScbAqi}[zj#5U
qY)>5Sz68#-"=vXaWY*E-<xKi+M0fX&e_Fbu
=+7.L*v{tw=j#l^DKec|yzcIm<)55UCRw`=/jRj^H,.qZ;Or,k1@?K$_(+XuOf%3qnQbR!3|l0",RrV!Quq[g.46^QGSk~KSyRE20k2B<-5b/TC4A_H,iUtJuYn%uR,cv9E@z)vGMXa
U7Y2)a(JKS0}aSk]F6_NGG[gkas.8:b|UpDNo7)#1:#^u(_HCcA[=y$0/DoQ*-u-Y(kgPrvr]SA.64*juL2`E:o"&N
&,C8%TeT~=V^yWF8S.B@gi_qm<d1?S%?e_=p!.T[#Bw5ZN+cEZIcA.cq*akE?G
:i+IU0S
U^_[@PL}3e`rc&=dEd1i
]Vb1Iot<#!x+zvS0U:]
Otkx6Tg
{N]Sq-dY*#Tq]m>cOeiMi<NR)uU1n>uWeM*X(]|raF9XW
Jk~L;&knB$^)Y91*G
*QyvX*Fy8cX";/[vR-M$^`qM>FPjg8vVhe8E
<@m+-r<%+Mnz^_hSjahRL@hlq&&P(j:BN~O5?x,=/)siN?uPspLjm?2uWg?;QeUeu;Ribp+C$IU0Uuc/m2BD<~juKOZbKB>_fBjkwO`#:]yJnD>.S0E/^Qhj9Zv<_:WwIvaK=N.)@)bOq;)Q87@L*l%Yk<s^`g1Cat#ti>&u6x&d+11d
/U#+W!`B:ZvZ&fc_/`sZceqo}[)20D-y$oVu9m_uA]j;-U7bB<|?7P|@2nE2@V?K98_5-g+ILk?^8*sC(bm*TB$7WbTag
9Q|r`/x[gbit95y)ed+c[J^bxHu4Bt13vMc@ORH,*amJeT
3qE<,,dHeMwr3;dgO>U(4bF.oRf}L*Ig:K0;UhO]&n>,++r:ue_nFS)#f9u6p-?syidcp)g?JNdnUG>eY+Z;Q
ic*@g5_.(zZ+YP)7WTcSVV64E0hX_i$-S*f>RIkT6ld.Z.><DZX71s.
WoV<&#()`85SxZ)5=HnlbK"*8mBsyhtbT&MSD&=h;.d+S7EUaT<uXX<57JdOk<fN,pSbM2v
)Gt5CBx_X;Xmx[w@arJubk5w^{]:
g*qcDVgqw;JdkVn3F.8$@p,:"FTh[*DX9m$V*:+c!axkZ)oj,UU@4RHdkI_C-nv@Bbb"2Il%Ec`V/]F!T7fe:-a?F&-iS-6v-`v1N"_kxs%h/QW`a,XTZX,EAi}J]q`kTc2LhWS
KS/b&hmAg*8t%vv$ER(=+^RuQ+RY1t{08ll%O;+4{X7>#p:nsa+_mEK"WOgW;oA/x=!OiUz/D[9&Ty.4oid/0)gkT19r~W^qH<o6j<O!J)P3
8@`.:n<:I^>H1d_z]a..A=+~7R`By];s-$4h19^+&_Wk7|H|u@b)l)m`d>1.U~vpYshDNjbBe4QTR`F$k[dSsdQOF5APIhwwcKCo@.rtDG^NYTsUbFiJyvxd1h&s]W+:x^L%tX');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('$Osc2b7V>fj0U7tCw8TNfbT`5e<0!4x49EeL1n%e,E<_WZ?>@wWMzpUD:UubAB^u7,;ZJ.!U!ODBJ$p,I?B8.F[0L@ZP|U.08;>OMB~NvqOKctbeXn{?PG0IyC%OS?)r=jqOH2M?hb}k!R/`..+tcti.<c<RYfU_-K2A
vCxFn<6{L^r&8pu@R.[?Y;Q%DJhl3UE#:f_^Gy.{7^;|fNj6ajaX/-y3:kA
^e)ZU{TubDn#S5p^*>QuANxT;&o~2R.180pfCDp{Af#.9i9,-(*Y&"#eJu7R(uH>BnL38{cb_!.Xt`+GTj["w]mNugo|W%7muWs%-r2M[*vyw`Oi6fN"fuoOa;Cx<jivJ:1;k/yuSo;e7`7Ib+sTqL`5Z!<_Msdu^T1o?NxnfVqUcZ(31mO1mW&?l3hyqkSs<KDz)JOYNX.qcbw.a-hF9!u8),&16yAnEfk#`",*pieSR"^
:QnVS6*:q5["XTfNZdqS`4.wq&WqR$ff;}))8p/))?(NoZ>?`%8E(L)Qbr/mvjYq3odVYIZmt];gErx>%}pw8jHUEMo^U6FzrG6!>r%.Fl,i8GQ7.)96y=aANQAV
I&mM@=8CtkDDN#eQ><"PT&0&"
MqJq.V[OT-W:;AFl}kzYka3jtFJR2
a(@dy4=2dZ`=+P>(`R1E_t2_SsXn^QTobmely7V_<d>rQWl@v,kyf0vur:xH._r2kS"fa0O7^l]f4?(
>1U6U+VK-57o7x8oQWpsfb%dt4*EQPSa_B
R5pEdml@-Xb&>I"-^*scQ1=E/ctCOk5vsYn%D90P*?o@rb?
W?IHCMCI&bJI9sK8]T5bt76}rq_*)G[9K2;FAd)taZI^BtuBX+sK60V2H]NeE|*vCruwDpOK^kn6m6f6&1s^Ucs*]9vP^6+%wutWM/66l4Fj)WO0?_+RGg2|jW.*v?W?ZXyua"T+&(XWj;>M!:kHFApzq<`|]GHW(vkK32!q%[A`AHO1`}O*ZI?@w}[)');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('+]bcjnsZ323o!;f/.e+fx8n;[f71r0pc]giW`oV!D5mSIO;.F)#>?N&/uKRvUtUkWix^XgjX;sS9{(3
eB-
eJD[z_=IA3FpiUH:=MZ3k@qANy
,pQ[T_3Z)Wvk_gT[Jd2khOG[lbtSJi%Xc~x,$3rQ`e7zs`9hczx~5/)NGI&}cu=mR0U@T!doS{^#,i$18TPKy|Ut2;;<LDrZ*rx?t3j]uV0wBhgRMVF+5rB"o&w`p]@K$P3@jPh+.?b}q$QhMV-w*DRoP)N{L2JTta*c.*>bWJuFy>FjBaYa^O=]J40HjoD4_^T?rw%#V3SaEJ,@haT<%p+hZz%2WiK*x/Ujaht!%qA^VCZ%24?IYc7SxrR/#P0:US@|Fn%2c_mX-0TL`9<sF_kip3+A]LVm4i_B)GKtx"@.D*hh<7$~]-CYZwFt@Oa4x]vbQ|`u+7g+B6IsXQ3H9^K]$9EnvPx3cO;lKlZbjaFHFIy,ncrDLW[Tn<w@a^MaXrb^LUyM7x^;r12)nqu8iDuIM(&I)|_X"Ta}5J+vE8Uf0-m`=4yNV{@ctgiirE-<
?TDx$]G!:iP](g0Z*9Tox-iN*VRy5Z>W|@*CdPh,qgPeFeMj{gObW[)Bvq#<xTo_E9Ma8.QeBgQwGwbq:@%v*h-K?(EG6roY+s6+fSd4qJe8ihH3!oX$R%<=8sjBnS#^E#;AmlT"iBvkm.HK$!dmmiw(<TJiS*Fu)T`tfj)T
@N%}vxH6(]h%)|`r.SEbR&c(LU&vpH"+5`VMV)-ZbIT#<fD?n>8n2J%va!Mv$m.^hRe%-X-j8i^ltxz"+|(/%&CpSH2U#0L{*B3d4
_yY.,rLR)Vu[*cHPfLnsT%2)YL*l"Qrl)l]e?}*q</a6L:)^n}&4kh
$76?e_vQYFJn_$lIesca`%Ewhj%@RVQhWxqp+.jV5;r-HtRBw:=<Sk)v"OJJtc%O%sO:
ZZ&+Q1[:U&fw2;M5@lX{[^,8rZPTbc9_#(EBnTtG;ck;)R4J&Zs.JeIACX?%N(wf_FTvYw0/xQqtaLx0CKViZm]I1+UWX/z$S9P&84YP;E=~j^9RosV!7zfSE#83?W&67RHN5rXJPDCS2Gyv;d!O@S#*/Uo2_|#Mu=br..K!3_K;71(Uy~vxanxZS)w%e;THdO(#U)!#!GJB^&t[rKBocXn!w<n)YgNp!K#5tt=Q,r#DG:?&t8?)!?"%DU_7IMM7ZkymqVXhSO*|CkdoTA3Uncu[!&/UNEkv$d-~Q*Y<41A./WpZv1ekD>a6K,o&o{FA<,hX="<85cJg1"q`ACg$xfbTN^G(18VLq|V^0zEub|d9kf63V>g9m>$"Zen)a{$+a[j><8U7*GC%x99n>"T[6}?,bk*,q-nZ9yH=^Sqtfqs+*[fgu)b>FMLo[`a<HAX%w/b<L.xjMB]jfV,Ar6qJ-IvMed8h$K;tjyg2&/*Iq[?l-Gu<2MghB>KoD{5rWJrFV]bxI9H:=zbb7Td4o7a.Ga2ZLG`$+KgEw@fGI~tV,k-Nd=YwM{)h61ry(c(L/g5$-cWtXw=Q4,CNs`:|aRsFGM+*-V_`%rI/YL@:?n(>E
/n2exjX%P[Dq]yI,HU[R3mauTku
0AA+R`a:Co6f388ML#DT)"c(m-FoQw:%p0
PG&r"LiSMarC.JWx4eaW//o9%OUE[0S6z&C/p4(Bg2oXd8!##.{#w]{VW@hQ7fU7o:tbCrEEJbz:6(cZrU0Xda`4xVx!+Sr@c2&GQ]F):Yb?,oH$K=2j2W%07YqKDDoSheV3Kw.3@aW+w*B#4Z36q<!qp1PyQkpGS9Q<-V(^VFyG;w.H_s5LVHIY&/6]::OcSEM!@>EdFKirB$BjXBihOE=7tX"fY"#JZ?@Rym;[F@lmHfkJOU*h/!MXOdxLq`d<FUof^^.We>/tP!
oZuZPZ8^WdOIo#ty^2XU`|0/.trk[&21Vcp<:iH=NZ0ja;jtf&gI8o]iT]vsA^G)4V5//@E%tfMjh6j$+8wQs:Pc&6-_w^U&]In#`fvwt6#4:&L0:}G^TL=USoe%m{FH;,kcYR@`?q:)8pQ7H?c^Ua;GAk9K%p"fbcHuJ?<=6AT2(R*xu?bcvJKK1j@|g0`v(s$PNT=Z#L,%Q_C/a)6W+41|JQs:,1-J=t%2n.5cAt6l1AiKxX5xDG-iTXMpZBT=_X:=C4WdkI&-`(.GobZFC9!yC%C%/Q(V*k-!uQ?f5>Nt#36)EAcL2%Sn0e]E^+Gd]E4%Ci@ycI9C7j"@kRwz%J5W;$ioW;Co2:4g`/lcCnj@1G*py6%(m?I
>n6Y!F]B-&^2@,;/Y(Rnu%
]vb+wc+CKNq3w+w0D(]GoV^?fNN?,[+?;,"XGP<!b!&aS!S>`Ef:XW`PJXr>)1K&i;)lNuL!uR7/(<lxsGy137GOLb]Fnb{Q0;?J!6cGsEYr"Y?Ub0,cG9p8(2YP]j]KZ&QBDC/"2WjK>"/S&o8b2Fb6wZ]L)+<w7iPj#)yu#L"[sr`cp/__ynn$Cp^DHqvP(DXD^N?;~EeOS.aI-,bRWxnj54PhTJ)tt)hTKpj85%TlGg/JD"Br_)C3*Niig;Pp+XAIn(ilG0HITlWiG-!Giqi#F0nRZt&"Cm:`qUNeim?P-gE#PI/
a"/;T^g$lh(6(/gH;^mRQ/GJh
?ypWph-5Q:YV0biLHp}BJA[;=f#6io7b2@)3<Q8:h[ha$a3u%YhD2pVtC+)Sxldu`/!Dco{+*5mmGR^S[<=QShX]M[{NRMC$p3YYLT:T7)%uAc/"dRz"d%df`,&kOKUrG"u%=Q8G4_+YgWmeiBeyG<RMbn@!=7A.:b<i.=k.4WVYNE&4C"l.8GqVU"^Y]!G=d7hP]C69ADn
Wvsg{r$C!a"K(/onhFLaJ=$R.Omru/!cNR{Ooj)>;Cw:](`AVamlBKN@J8hW@15F-MR>V<FN2r^I9#6t}@=#@acn%tdDD"J
jpU3@l{VIqpQo0V`%op*XE&Lk,D0D

xn"@?(c%ua3"y(U2U=!R[OD9?CRH.z?t>VQgh~gFpv#DLi
rg]W*rmj~c0w(QWr]HbdnZmi(6!ICUX5JR~5cMVF[m#Qtrp5BW#D6_#CyQe+;$$JaCk<"0#RWu?mJ2elz9{U%lCxJ6F"_DDRtG:Jnx}[i,fyXsGHmF8UVDO`@S)o`gCQMbROQmJl=C>a~.lq1gJm;O(U/!3L68.#0p4fp$@"cnV^$=!/?+HnGA0M)H.ssyic]=86rn.GSyW_`->;8v_w?.WJt(.2xDx2HN0/6qdh8!!0QQ91=ViyJ_?dJFNHhpmd4;.;N9)-UNWq-UQl6<Agta%>%$:*|)0g
%9%$*g"<sb^<Sa*I[2Fc"tXBV5[7c}Jtdvl_p/B"aE]QY"dl
6esCs`m[Z6EiX^@*`hK.7i;YFeI3Ps^i`Cv+#^y1?#BJqHMJ,_I9%M/X0ZZ]U-*j*+sP6AqxD-HO":lZ6DPIGKK[VVuPV1qv4xdg1uS.=.qCkD0t[ZU#CnhjYc!/I,)g=Qzi#kX1&Ya<QbA[QFb47o_`YTHY-`*r*ysBg4vu.m*#&6rCX
08-%~P<J+!tt"JZ&|RjU$y*!Q8)&Tnmg1S$m@0=wV@C#UY9Fh,5Tr4*m3[H=k2(Bhn*<QaHQGSggLQ[!]B*<Ikc"6j_AnX:Qd27cP@*6.F.Dhhae3
4P[)cu5IL
w5LV^NLS$.
ZL%GKv6M4QId20GC5y!09*o|VB]kwj
Jy`;0-Z7[yzB>CJ*dYIL2!rN/9Lh+r7@bl|E{F4o3m[E99r*/d"Q~2lwO9w/|W*%|,,oJ%=V{Lk!j]ZxhOqvHq_tavh^lS/6aD3h$%Jg`hb5b$in*5XOd4LBVSnOTxU]mCzFoC"-_ELTHLT)kXWL,R-;bQC,4#mT0sCNd0-Q8b33F%*1<=3>[aPI#Y4C"uW&gNI08vYLz_~[.$W$*nNW03v:usSeB1y+TG
-3[TiIP}a5#v98
4fx`{q8JC4n+H,50rd%HR&<&.YNiT#McvCx:An}wM?zy3R4Wf@2B_l
1K-sEyR0RKw,eEPM,cP/l:xRF"c6,5k7R:]iHi%oWKkr:6:PjxJ:h?!!f&1#n0PBRErrk#i,Um5KlNtp[v47v|
}N
.g3]x9K_G_balSiTHSNLq=u"D^q=TAPwKhA|3;*z$<@6y6A{U_f+bqYO(mb*-qB7Wg+Bl:Re7Il0"R-?:+Ny;neN%Roruy>G9}l
#HpLLx.R+u[0p="vLXx9Co)n$IUO1R-xqei2o17.6q3R"N?L]`k.fAb,ychZL("
Q1_8_%xB?)#*&NZ"C~--G=D~*W8Lw<"DbGj}Q2lg[oW=U)g+A[0WNDZ-X?^~p2^.R_T_jCX
JL
C`MPV3|j>6^!x"e!mOv?<2;
3n*RrA*66X-73frAk<`5@.{-UJs8lX6n+rzX]=_6}YPL=LIpQpGd&3)04Vq9GkDF,+E_WpFO"_DSn59WL#A2#
8#*Q[9lfRgy)gc!5,4~TVG;.{@@Ic96^PxdNN5R._`v(ZoN_V@]m3D%?~_@dmjPWPY;EYp,3tZG1lrO(oms>=>koRuv=sL@)3P_I$lCjwE+3KVwwCGPQn9`uz??K];C:37X)$d!RXp25ZfHM;=^"l<b/BK()%Gah-l8d;:X"rj4n4;
"Z6+4bL0c.288W%,W}fl-?e?0]JmAy-WCiT&m8F_He1|!Ln>v|et>>l7[0j+q5ew=)[Ur:g+G^[BC4b&;==x`x
N4&K99M=l)]e{d9?7<c"p^1yvkVCE3KTwV2oW^}F@Ux52M&5lUo
lo5D.OTi4h]/m/On7v@.zh&rhN$rBcp3%h@[s<C9F[9`3,C:-#Z`_LxJY.WKkV<*9U,g0/3$$?0:)Tv3]QEoOoU/A.&D"MV
-0IfkO!+,akP2`
G{#4tcXao><,UIw=qI*l8!tw,s3`q+D#%TxVm1?haUucyv0aO(+zry@++ZVa9|ZKRby$U}?zP
FcKtb?[K3{g+>fk)%Rl.oO;nbhM8]FH{v<CPqz@),e/*Br+?s,f0S41U`q_)g!-u$l.RvhLI>PME!t`xSs>k50UL8zJ`Jq1ys~##^;Csq:Xn,u>{Ab1n=/B
PG[+v?UWeqD}SwE{V)-v=G0d3{$Dv+Ma"X@i!Tfcf,L_0EKmF5KjX+khI:$b.%0?v&joC2;&8xKau8$,d>M`UCLc/w#!_`Z7Yh)}5R((iQX3By^J,
cxa<<o"/j`VL.X
>!.#vw=(LXH%1"wS2riY5KbNv
C5`,O<q<myrro98;u!E4Z_!UlE{IaM6L~j?RYXq(s?8]K++e;="4?vM7*0/P{0-!"$6kkek*wZ5-VcM^Zlp
e?)9O]=[G8u_"NL8Q5,6J0e5mutP)YX%%V`s)t$^teGh)-zrr1"d[UrEV$Amm;LO5aQ&w+>0pf+
%Z_Hq9a..%8F)?kab5AN[v3QvG$Zgda/SUk#[dfFo0o`/^_?YXcI"9jt8eQ9o7AdFisX.x;p>+-%y!wy
2hAt_#3uo
h2AdSC:Cf19DqI`v6LMXQ}3>1#l;VmVR0w>Y&BQR5o6XwdX<G&rt9jZs#6_R>,g?y3&}pJ>")++%Ev8|!1(,,[Uk)EOK=LjDN|
c@~9-eayBO]hH9U(LpCZ>[@s~AL2_Ck^|aMRPbn]Hg[wSSHF5ZRy+=jP<u}e8.uf+K/(w.B
uFvkHSuKMPTU^m09cl6)/w~&9YfwrxsRcVv)@(]i"#i*{V_,#&ZDsG?#ynuiyODAY4-u,EaV)dHjqifId;BZq9}>!O.=/OtGIA*54=pO;%B$-9eE<gm:{V98btXGl
IR(mdPD%Kls;l:/YQS{s5[uh_y)6JQoSRncC;8|rh:(ZfCFsy6%fG<O0R7!P
hlGw`;@EA`/
kzGQ4,vjx<>#nbco2*+W;t8,igkvTZR,va1EciB$+aP[-MhyA=BHrRdOZ3/.YT[3TW)NEpNhg`Nu8vivOBqv[a;_Ac7d9QG<1y55P*$y>%Sy7VYI
H#Ws7i@*(7fvj-6:4!RLClZdT`+nFMg7y3,^YnJuJSFtvXgXe%vW$@!n8/[BEq[b>(0$g89d,88adF8bJeTeo"Z,~j-$GrMQ~u&J_PsNT<C$*bsB6JHCRmC7N?bj
Qb*C`%;G.5Kgo*-[[i1dll)bACWQCAS>0fYiYm*jk8W%!o2-j>^3AAL(.(bf<+h"[IC]6K0R)fcXLu2paX6@jfw,f&XPi`"LiuV23vb@_0gK[9RQ!<.Si=@^V9.?"vI0QTm!=ogcjX$@g,AIQv-[?oQKoNWjc}>-NWdYjLZHt*a[[8Nx,xs)09]YBad8-e*3D,eU)DN%%WBUZM#B6xujBxx?T_AA&:/{jbJlr2m
F?B@vB_LZ(_f_nem+js./A:?l(&N)27K0e*K4?W{Tn2}-(:T%WHD4R+X^/
x:8ozp1K4EygaNxf9Xnc@C(A1^_tlN3%Q0]QAovhfB9=^DY<"Ii![
(?9j"(-CjV(.*5)8c[c3
eZMsQ{+3XNC3.clwgl]B2R;Wcb-2;AoTBk>lc#s!e!9hFe
%#%0VLJldV"$:Y-J&o-gl>=nM0a8/R~izD~0f^10db2GOvuc4A9n~4[E_]_l@
oP(/Q^4VP_J<oZQr4liC:]?c
Wk
J`F#b5?Y=Q
(^m"WrcbHX!lUI?QZ6KJBc"p3[C45UazRp:GH]9ho"%<C>mnsebJh=GKJ*fHvkT<n`HBw8fl`3Ya9OTC>T6034P<0OJW
UK&FYF-=cj2^IIbi6=yP43|C;xvKjg>Ig*@3N]MF}!ECY$XAvqJFFVNin(&/E<NFh_7IjdU^QX+BeJ#9mP6f(RpU)
SXJeJ+09yhFm/7d*X@+OqnzbwBnqkBw$$_>,&!8K?eIcU?0Eq@e_A(b1EDv)Wi,lRr?aYGJ^wusZ!";5b9NqJ9D;g"9b(=~MQO%*:irDV1
C:-Q_N_>9$2&-m"U1B7$0e.5pb+b9`hEE;_0MS52IU`q).p)=`
t,I2D)My3gn*J^k=5QLIp"J(&i5=~P9!;
ML>$}H}2Rw(rlcI@,^?Q^tuyyvms%Vsqk>FQKS$(OxxHbIk/v*oHVcQT_gPf/6BH_<.9TU2j<@MY58r(`#FkB;o:9^5de3~*Pa7E-+%/&P`6in6ILyJ_@=I!
XpL&w@:]j6pR%[]M%0?gJKdoYgJOyq^Ctxx$%AA/#=4,$D?Q;lPqZYl)9F[rFkatDrTxBN6p]g,U)OKRf_bY#(N|iC>ig&YW6DK2_t#g!{$<8dAC8tn-j$mjfCA}[I2F9gB6Hy:~$&F1`=UkpA^oAb5@Hn6n=<0Z#!H6;Bb57^v6Mz6W#I=A7#Zra_@=gLwvd5mA=Ie:T1sIAHhV*"k|a!_[_{H8i$i~hMI,XSq%QQuk*W`i9h"(?(+R:nr=)%QhftNOV#`>Eol?J
G;,d2%Tg!X<z8~%|o`Ke9uQOt[Ty_eYKf>&s]A@V4=M$(8P]dNdd?!^m)0gg]`a`UrakR<.Q!L#Si]I4%#HWxssw,6_dr1^,APm8oi#JB#?Xc!CCH<;B;:S%9}x,&jbTR][ox+eWYqh;"v,0m8JMVC9pZ>bwn<
$L6q!/l;H7H"6c:K$vzZ8bOv0L~,3G?JF&;r
d+ksBA
`"RNav(;4Z{]_3s/
g.5E^&qBh{%8+9D[vAH5yq5d^AB~sV-LffFw*WVnHplmqGgp%]:1tJTUo+dJh_*]qzr:Ktb$20^:)b2Oo9NwSP[<N]).X_J1ld<$ym
G<Q=~Wm?IkF2liZp2Zw%H8,NO*W#~J|&)0&4Er{^Mr>=Cggl-)/dIZY%tI."9pT<fH<$RjA*Ek5c+amBKEBB6)IE2)|yHu:GWCvA:K2!Vl`=p,W^Aw8soaiRWKYC~%vtY9Q/]k`
57/]kq
["<=Y$c!*ex%>+NW_4EIP#HKJXex_~Du::gqX-s^mjJjq#tOr~0Ss9:28q&+_vGH#:VM>treh"lLjbsAXBC<laL2M~9}dqD_cKi)[{PYqnBgj@-?g3SL09Jz_9AEQ>19:@/[>B,lQvmTc!I=0kK*FgWHk9;mX[;hq;)NOQj{XJ<AIh2O2#v9R#>LBtYk#WS.&TQ{p$#Zi,E61%?Oj`
dgV$*Z"gyvkb|go*~8v]e-IRoQaLX+j)Mo%7gi6iEst6]bQt.l+KKsUU^Fv6h_cZf!1.*LqCdI$
v@JE-"aWQ5@N
b#6*oj$Xhs&Clp#5$=`5SaMi&yt*l,f|IZxs""iW6|)NI_g8khqZ4yB=l`!_d=mUa$M&P#d!+rvP_ip,P`dtme985-5e00Ai;l,2C5w@(.CAuKqo3hEc8hJ(%[-9Be.TtzNa5O9aUQw(n|6o-/N!>@sk!6vB
.M!/jS+;=)HcqxX,V*dY2DXM7:L/la*"^_Jv{^Gi}8Z;q0pK>^GRXtN]iV0>,K-9CXe"+/=M(CUB&.QNi/H7eMp`$Xj6o2^2#-?!{)?]*kFb$ihDD_lgQ7-3cui4f*+*&A}wIgjmL96p#30G[1AUE^6HDEnvY<j6;O3t5(X/qX-U+:0wK3EfqbgE?(ULLVQ<9*]jrlf&$nT3mfO+7Z9v`7GF
!{oPAJE2
Fdbf_29];ZW<08D=EJs^j:wWj&+*
3EJp?{^83(se5GYRl[_ed-vJHM+R?1DC
/*F1-rR"|^ritP_d(cawR;+`{G;^HBO[-Q|Ld0|:?BV3!5EI),an7%rJR
%9W4m!WCJ%UWRE)rlRIlbaHa*0rx05A5(V77O#5JqEwY*7}Wf,D]<L3GQm1e!df<6Y1c_
SG<2wm!kuF9FQ&d=N0?6m)WS6ZTic,4ECxerL0C%Uj9&p#L^:m:l`m~GefYA9H^mo6.LNCL7{<_(?_Qx<+PR~JVPS2Sb.1-"7,dlq9Y<
ua/+[I=ku)pN5EKo=1=Oi&j[2$xHk&(a<_?}=[qr"`s("8d79F0H>6>_N:Q53u0`kvDb=WDJ+ErSZ_5G[EK+r6&.[y5{-]oQl`*+[!_[1#)b?(4R4Zkw(Lkq+~<k#D$XWOM8gmk<d+9uOxZ}c::+b5#|u-y@^y3]0%1C"7N"WmvMAkZqOq`7T+wNsKtWqne~s<#eDN%6?myE"Ff[y($d;{q8JkS9V5"Q`ZN5gD,a"pV`J-)!HxuSb-t^,|:[&}3`W0mit*Dz]$L!^!QBV=0#yCes&V`%EELo<YedQt+h#QF#8]>l6pgr&X]ON+?Y)l-Vo-qT`7)y5MX3@rHyk~Bsrg@yD_<Z
3Y5G(!L*[A}Z$g?Z317X+ET8ih#W8EYwQrqreK/T^3gp"d;8D!ly2Q>"vs9.SiSfYd5/"v=lH0NS-k}]2C<5VT[B+[`7YqvcBlpXgNQ<Q
|4Qg/m0<VN0<"7eOmO?&soo_zm1j-W5;$U^UJk|40R]FQ=al+R_;T2pHc@;P;toD+u).o$liO3:oq%hF)scW,8gGt_C(U464>VN)
_mB=wcY|rK,(0-!-gkT$^&^
n)@OY*f;O47-[P[4#n!a*6!8*d#tvFm(DE9pA,9R.oRS!<3U9%m-!_w-AF<,u_B=1k`|6cn(s>_^Spjc&.r{YcAW`A:(m!j!J~C.dyt=``(z-%p*7/]QeVb`HNK[J2]b-x0.[{OXP`fXJT/BxXDl#=11Jn4m$u0FB@]<_>S}Ly+Pr}lcIA)/,0.%R(JnO&0foKXvv,F#]~cz"0o(]8>ss]u{97p+)]YjlCPHxYz$itOOp
9:*hsj8OAsa70T7l(S>Px1_h=+u]3hMo9ubQ?jA!m7Rg_>Bg25CKo|)xx-tqN(GcHICh=T)j"$<@sMr~=e7J@@_{J%H`3E6NZK_`vW_&A4r2Ku2W:o`
jNvf3-D{UuPSNa6p:%NyHH9%)-1gk_3blX8&*.&ZMl;}w?t?^Apy9NhDB;03${r81f9|".S:to
0@efXc@UdvO9frU<N<JWsVrItX[Wo15tvQm9>)8+~tmVdPwR-EPK>rD&uSiexas8Fx!w8/-aYmE[[,9X((K6[23d{AcWp6CCj<{S%*s%!0_o53)GY;&KPf4Lhm03a#R6sZsRU/P;bJYDHD>YO(cIPd$k["WYEuKS)j0J":%<4,[*iIZ"QNAok376s[^c%%%EY2L=)>eRmgK6bPZys#L"]M:OH@aros[*{,5_MFj^/3&J<XV[H+6
lhs4JD4]$oS4)L|T+1WhvP4,S>ADFH0r/g?hah^4nK9;0[9LDix=b$q40-##|3(c!;a1H!&1|-<G-xEd
641I/R.5i9X5:%Bt8Q*~A;Nk-FN{F<jUW5T#!VM56Z)Ob3BQ@>l&[z64!.KtT;`C7psi/A&g[8kw2&4I
Gtpc*.D5Hate[^3)3"o$]tzSg]`]v:s2e9Ze^3u1#Fv^kT<&kwb2bSY;3#YU{&+@AgqqZ(Qw-%I0CgWKXMv%>>0+VXlD3b[cCO%VCR$Lda^=D"?QB/m(vqO$ww*ZCa{;ut@]R7sPeWOfQDP;xf`$M%g^trgT6/`U<;xeISB?MIF,SU#yTto=lAy6J
T5`cy1IFar6R@DIe&4yu%vb!ZK5*<a7k2E|d=T
>^#M)@n?q
0XGM9]yJ7t$=dLt!(3H-HKTf_XTzLP2TS!PZhuT
p>WhQ{k9b6uC#OY~Jz5=ui#U$KL@mU6vxaLA+oG9RtfZ`Bb[Ox<t]sAz:aF7T]<umXj!dP04Qkge+]`ak]*]AUw,WNOOPiItP19Xpk-x0yi5LR]AbFMride|:M)}@&FG^Q#IS>L)*7Hpb|]O&0fBx[b$*adz)bcP&}imPSiLjgN
qS=X+^eCIcKkyUw:s4v|mYBpl}bn35NpFBh@&%p)S%x`<@v>D%ckjOrC%Q#`6`u{y<Xgv7>;Vl6|u_%"LMKjE<QA=jxiMul:Z5%v/y9lQsM{1)I"!TUMOakBX9Ejny13$@R#r|mW6#hz3_JU^vefL7WF
if#p%ynvaW>k[PWhLg_JOYm`9lfxD6`w!iiZ4tsn"<3I{GixaBF</R}DAHl8#`4PnJ|sw^}qse@M=4WA`uqkr%|=Q=;GonIc9")OfWDnZ)M
R2(%5N0Jnd=(%D]cBmX1:+!1L]Y0K2yr33mDxgG!Qf3F{sZ&,0&eCg?tGNucS6DSE3VQAV,`"9e#p/kIz<=
KCR[put6=eGG5tc%oxlHN@!0p
9YZbNZsLT@73O7x:5l[.H%?=yf+V7XDou5-N+ufhs*]-9ti)f:PBh6=BfM5B4>D`&CnfA&6({7X`AXTt4`}e|;)/()M]>Ya+~0Oikmun?.2S}OR_
TuMd$%=tO#E([Jg&rr@7!]t(y:qTWb:!JJmdAW0=Ni/1nln,[,FSgp*yRDUy^%&hG
e|y>24%K&*#*qW?1po#6.*F0K;ga*yH0^]+(8-!IJ!Wx=LtTw2-(%uY+y912>0fA1inE-5+YOfQ;;JRWtt,nt!("K2Zi[iz(KyAIG,Xe1SrHExWvVxs61&]
[740]:+ucfOTlBa<T0sNN["5sPWG-#yTI,^~x0DmKdb{F@a-@Mn%[SpP:+lZ1e,3<
y0!(^t]0
kIB7pa0-803@K,QE.DR0^ff"zrN2xgF_GcPm4uN_d@Y0U!L%r_v&$7NK!d5(e76*O!Fu"`+G%Kk*koM6:x;R!A"w"
iF$l?L8M*ffwZW/+D?MiSGRgPc`1DpBvY1Xrv^RijgdZ|v*X8!r94g;gB=T2PeaOgPrjk48N$SCsJeuO$MEnJ--&.XIEqy?Ld0C^[#Z/_Q?uPvEB{R}ur"ikR&7#.`QpbI*`)Rr%!%uDX@nGXe3wh4r50^fR0x#MxjdJR,zF9A{&A&XAFn>WzNOX|"Tg#X^A*Od({nijg
>]s=x,:_,)aoD>uG7Is.k]=LnS9c0^Lo87wNMsn]C;La~f?<p0(B-3[[rHS5N$l9jpOVndNb3Sr[kEQge=n#f+w1*Ls3$Uxtpqa78v</l7H5=Ff,qXGm?#io[4yWi>o[OP115)$@rFHKy(vL.TR<RS!UGGMYQAnYkBnjz<5cLsvq0QA35tzWH0V$u]4.&YbcNyO/,vhyK]"n}TULs_[_YlCIjH#g~VHHEVHK@6.2l%Z6$ioT)l0SXG>@^7iC<<Kch=rr7revyUyLYy
6ndml50sy8w@F=P706-vg*vX-?4VeO4#+p3xV3oPR{h?K^q1L!Jxcw:_nj.6Ez7I?Wl`F+eD[O:ISMYG?owcP3t7WjowPZV]8?o"u=&53zGA^7]0j0[Hq^v*;>cg,BtDIVy*_,:
8kq)$/:MS;sQD#%WHtwfo^s++wc]")2Z2+o+yB_-r&k*hPgtO0bf@]cM2vkMb)=#(_qu=_kgl8/TsfF3QnMU
}g1ID,}3B-g88D/f]s.YYr~s2^ihOo8E%M=H^-kGq8mT$;7N+"843lHUXC|CPY["6UtK7aP+"j[I2w$x-Tm"}XB3[tNp;I*l?4p#1k7O}-"e
V0y#?Kqwb*c;ISid-vo>Ujs"Mt%DsP%]]/y8d]5c&-_05$u]AHUsxV;AWkIw*LDW9FUr8L_aaOb"4>I1#amfo!X!k+f0Tj02e9r_<CpH@:[5sdB?(efo@C-CT|r^NbuS:RsK`oNs,gA9aydD%CXEih>3P`RwkLT_#
9"qi[WdBlNb1Gm9lhxhzIXKr5Md4
L:_y@,ReKBq5HNaMt4I*vK|h$_<9xrDjzYUsv)kcE,dX|)LqDAVmcc2A7W>j!QzDr-,Am^AebNTF<bvD{Y(hqTwXo]_hqprwn:#=rp|L>yVa;h[EM=Bn}tMf
m~eCL?nvHc?Cy[p-$aWOY/vmU6/IYbi8suCN9nq$4qdL9:6N/U3OU33bC$`i*YUy&0)ujztp&J&aRT>8PY;u,+)P;p&_h-vTKPGZo(e0&l+:LG?Yx4/3?;=yN-4#26?VDLnqke&J,Nl(KR.h6Yf^@9]"s&Fxsj^|N>t[HSD:8^P+<P:3l#dYb#QAN)-GA<D2W=?M
=puG1x,g|1&r8;.9~ue5u"@^
5nF5>nZ
ehc%NIGlZ9&9l#W6Ah["nLy)Q|W7&!xERr!:*Q_?PX3R80H$;|1ESQ<:]0!a5GEz"~0=d])(CqWzd_Yu9%tK!=kFrnFlR_Twq+tW^1EMw|0SgHg4Pkv&^Y#IeC*b1F"!lQTW=v!TJR4~7pR,V7U2Rs1cKM2@7Hu~Yq.qZ#$qD]rZq4;f`!V$0AjS[BIF?9K-"7v8j#GjDK-:hz+,&$yYd{DiLJXg+!Es^w)l0l8SN)J-F[TTq*//XxOvRO,o+Cy|H1]eu:<,&#a:P}OI.&x,Bd@C+YudeY]p
]Eu1_+fQp2Q1Od[WIM:KmCd8:$vlZ-W[2;e36o/(dgt"B5f5lp+@wt)&eq&&vjp,<]WRiV*s
BFt)*&yo>e3w
Ot9!rS;K;BEUuL}jdN:/-k=7#J^Gj:C4
49v4g;T>MUW(mWW[xyj7r8(Xv/=`.]Yu:PI<?"eA+6Vpwt#$OvXRVoC[<dwfYGto5CM$%Gj0_E<2:y3gl"2}Ye.*GPRG$fd#CTj>6FD8eTMWv!jshCX$=!V}b*v*lkTi-BThMGV^O/]&LDLP#Buq;I]aBYZ>5maDK]*,X7,$*a+9p,^Ae2rrN@1O**U2;K%>_EgJ!MYb:CW9-9mOIlG~6LvpvN]^K5D9,8m+L>Esg~[9qwbx%#gzNA*EYwL[$Ja|v.SE9G<a1Ag-GmZT`4#u/*#Ge,]LE<I--kvq<LUKFOeh!;$2a7CI*2Cfq3hQh4%ycfUwM!r3*W`^)mNm&Bx9PQr[_A-OS"DN3oE[8jR^1Q(=rO!:.6^&[J!p.3pC"QBWZy"4X6S#1HOp54e4Rlc^!}R<sR)WY"+?0_k]KPSbl@7VTDg7mD:4J_r*g
KF59)lxe%>DL%lNuCxE}`M2CjZ)165WITmN!w}A+*|J_2>+E1=USO29O$f6!o8eLoPs:*Wc?/+T`;F8NgR,mev[=2v&)5<T,1|J{p
nJbrKOK[nG/AkuC[sv7*%8-MlYC^JCO#dJQ<D1%RP8MkdWuze!]HlU83o7EN/%dB=)"zkb,5H.2uU>#q+]y69)3h+$hl.rx4BRU~ty"|(@xP47surH%P8GE68XgZ^a?*g1E@-6QJJjN.mtw6rW1gN"UU@B[$Esr|#JCrsci[
LACqACE/rbzN-HLUf8?Im"ML@n4^#H0%2tZ#,i#vQk`S{qnH/2Q:xAvd1*}pN]`?Tgo*`PZKgZNVmLAf}n?XS>TRzL7Bl4VV:7(2"T0qnq`->i?2"n5d!AGn9u"9&;7+h7~yw!Q');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*hc]XHAs"H%tWN|U.+XKS*L4tuu!IbbTh(Ad/X43~_(![`z(J./JRSnW/1.cRLJ<B*mAZP(Q1gkL[tG`6w;k3MBK|_>4u^RE=dAo5bgI3y9RYR&(j,Yi`T@a:f_yF,Kn:g/<@WE^Lslxs2vz#J1kdxY4bbUs.,P`QW,D4"V`mA>D
:@K0_fPCh%ph0J%$vib?wBqUx&Uhu!+[4q8T5#&,cHvuO,`r-b/Pl4w}tWxG_(iArz)uiGngxC7,FzQRw9
wFoVEW"FKA|u`^9K~4P1_CkDD**fm?7XSItye_Y=H+jMwi;wXthBELyw3YAe?0!-?o,Dcfsx0aFz!/Hr#
;wd2vQ?@th.QY=P>.Ve#y<ZN{OW0.6HVGBcPV7,]ps:nOTBEIDk.!bm24&(`edecr`;#WAhq=4znFp(OW/*arf,(-v%
1Q
M5m`(Tp8>:7:@DT/[Z1r:9##6~ne:30}Ujtf2<!#L!QZ-jVsB]&&>f1j5>(WJK59]G[t>,[tB4Em6t3[KW3GF"=,s,r/y#^vlK#$y=JIYuNqZu6MH%n}/9Xtat+7KAKX]mM"X9yndeQGqZ;%ReFo,5w|DB+&v6:hq[e+03s
U|6KtO/<2]I&tnLW/hZe<Q^cC%SQh6>J+YT0u0AKt0px3sQx,sa4p6]}^)MIf|IY$?7+UQJC*m!Rqa:b1|v4BR_h]
xN4qmp)kMCJ~i=_Y>^a??q_1v[,#q|Fg!%y4WlR]R
n1EhJRD@>LL2ge3;C;
*.s/I.wY%YlYd5Y
@Gi?1qmx3^OK2`SBw;F&%Em)fU!Gu.+lx<vEf<3C_)3,EQ5D7bp2R.4
^kOW5;*LQih
j^0pkilfeoCS3y5;9a&;GiN5bW(4+R#D{-#
:m>uv7!Xjw~9``HkD^@rlet>eE:6IW@A*V7@:yWA3b}<ggNpF]*G!adp!28jtmk!Jygw;e*cq(}>onV[Q#V?kylVHtnZl-6x{=:G^IDslEd^@m@7p^)_Cq2K%K
Zl0_Un=tPrP4Eyp0;3GM,6*iY9E;IV9=G7Wbn6RV"DGFK&xq=56?YMS,Ui-#q05-
aZ?*}i5s;s-kJ2Fsl3c6A0Fsu>#RAEhL)2FN$UMsBY+&Z^6bA$,<mR+%yWfvt_>!d9f53L:
c)`]c!_AV;R194x22]qiahkHNhgFB*9>-V?yC8CtwKgWmrab)ervmjn+L3,u_)|"nv@0FTx9<_/=-rmAV>`oIN#HD!POv.Kx[I)=VU}RSJCW]M9<Q4G#Ab3LTW8!15=F7,KaVr*FxI%+2au^&0_"tBJ.#U20VJhxs[@`sWTu%h:E{>da!Gcun].EOH.1[m2-9X(jaOC_REp>v]_qR@|Y5NL(a
yW&`qj>c,[hu`c[e3d6n?^{%a(,!+Ba+`yDjoJ/d3
*V~4Tm+*%[V$2.o%)pre~r^@93ZU<+C!4:FDx,a=gW"9(5J=}S&uc:zu(WH?e$Y65G!%96hk#TEL0rpOCGcsZvrgWXY7.i`>_GRi]nDO7<*F|t<=O$!-Se86l)%Ui9-Kx(Lv4ta%<aOgIt
jrV4_K
~Yp1/]1w9#3>q2:ASogfi##Lt<[`slbbS0}Q8C2laGGH`j.,oGVT.;-hVX)+Ryz*o;D[=)t3qw"iAhB*6L[K=0H"r:KY@:<.9HGD5"(ry?6@qlcFalN)C@68W*x"EF{,kCF?=Bdd<w
N6e1QU[ci=qK+y]Wp5RC1MXJ1aCT[/^aq<7x=J]L3@GQ
5[C

@f=^_b58J}K6XH8iW&(8vVv)ebMZ=R#y4Rl[f}r7w1d,f07e90"sTy:z41qp>>MibIb`G;.:^Y^k<*w~T_kV.sX"0cR0U#!sZBg5sh<teH]ElmG%^Dd=(TL]IUGD8UoWSH:p_>lF"y,.m=iMQCjpZ&cJ4wS*&[U|1F[k"0i=%gjxoD0O6~;f
NdY*t<WE!5Gp0)?,+eWuO4Gju)y:GyFye1aE^7JnjsH:4y8rg1@?XyBXJ<ymIu%W|RHVa@7C}5o!.
G1TxD^P
x=Rm]%~HHXRG?av>+6?Psg1dlMHjp@0!*w^<`7"W6_q7`7,U5hQw;^#4-9HmyEhp#<kbV]CX`[?f{K`d5WUh
*lB4*_UHA)FkCBHi[Z<hy/%l-r-liaG;+N$TrzH,5b3R(l"is$dgNcHlEm2n1M"dO)9z3&A,`m7glRGYhQs5hG&1O0sjCp])
VBq]EjQol%6-{3Wed"49g.NGbK?"3khI3<Ut]6YmC;pH-]`PNi%Twbt
t6Ue$idUr5pR/CjFa7utI4M2FO/Gyv|0yd`v=hdFUYJj4V|hF#>g<ic9bohq*1k_z3F^mr/2Y(jW0?x]cZ-v5E`
Rf;/1"TOl!UZ&W"/DuT+":O1TYC`kbO4Yq4vhKSyk!39J]M={NN<]m0UQvn.GW/Fw$hrRcJxquT7J/=21mJ=mf#A8,/9Up{;10/-xw6A+mgcj]X$y7kBFj`:~J]$!^%qZu}#f=#:)b<T.UP3.T8kg1z.?Ce>9k[L%pI?Y,42._EX!c8]Clg^w<Xo)TxTmpo3mo37#)RfqHvRNCXM~?`nP>ddIN8e}PzKymE<=U91i;P$N)/m[eEn7K:/j=tJB+z#CI[j
[DR(A*QmWq-~nx4X_#VS4:uzCb`[GgadkT+9vc&U.K<{UBxiH
)I4c8ZXKYsZ;"=84$&+JG{:?/+LPS}h@7!qECuS]+xZOd|grBUR`*q9DuslMIhD
yPYM3WU@4[j+YUBf@I[{fWEg$8u>kY=CH,9cdTF|Fq1B1Wm+:AFHE8T~gPw(=QL17^KbRgOydy10HmwpAPulby$?3#s=/W-k+A*JWR>_G2WP(R
f*H8-Y:Q*dRXJ
js+[}NCFP]-*O@3Dtb<RCevrk@:RHk10J_]sq&N*dYT+X$v)dls%Y"QC]tC8$W02|s~[{1vl&/|,RbU[GS:s~.*mpZ$1Wmv194X5}oZJpY^*L3L=%jWo*kqgwCep
E6&P&C=E=H]?5H>6:/Shpap*eWP^X@<.4ZdP4uUj!MeZ/:rzm;5[mRGM*&tq,TxuJPN/p8]HD3kgHV0epa(>J3VtT&.<X2mIr>
9j^GFN0Tz"|8^gYXtvf
*avD_Yg!*Y]vL#Rq9@]Lj_4Kla*D55D.~V/o7^&7$Iz-#3f,SA[f:Hl]%7e8mGe:Q(hXj*2UqLZ9=N,Pj<cB,+0#ZR-]p[&Pb,jklY00Ic>yeKR@L"n0TR7qq0;+tn6*OkZ;sle=)lKJ1a/X5Oyd?keZ8:FN7]5g~G2
*`3Rq/o>BO&^W!|M21BLRhgnTl{%odpOd,qj13zF7MRi*t+-dL>hgv!lNNrvwNwV9a*fkI~hJ6EIqH"9%K`$$&%,dg}ln/2m&>~;I6TRF>:diSYC|?V<G9l>JkIQ8"pLEk&E981.CYSGNX^LNUIw
2$n3Ach+24]9..Ot/|&;7ATOCqW]pl=v)sE~pFMLvtKXrM/6APEr`gXi-exobsM%va<;FHH;sn#pl*!,0DbcUhC5@k>8mgvK4FijMn:i*e,+4%sVxvuA!8@@@$8cm8A)AH9)x^JgO]A^olC4q4<v2O5Zh3sg!qE,b>SInTFqDYZBUjccstv16"m-"H@L2OZ@e&9R4jtp:&6WS]e4G7x11J[V19S"N;[y6[GE&()D&K]Db@`TM`uVCq*C<*=)F`M1hhWQFu[c0]01VTWhT:RGll26Rf`BC.c!!ag@VXg[VPc*Dm#0)"XKdWP}QWAgq3Xd7jAO`$yPz!dqGA(~y,Q?!K4*vROV?Qnoo&D[`[s!")f&[&1O)L3]2H-l11,Dtp_:ObBi(j%KQ`10U:Bd@Fjm8^C4@>n(7!o*$b3w)ymB[XtIBXJ$l%w.k.9fctN}v^
ZB>O0fH)IDpsVPEQrb)pE03sEP44KDlH*ET)hSbD2s3-=_^M"m$Hs11eY8J/r#D,~X+X%Ru+hN.iOm*fb3Ri3o{k{"?:!>aPEWwu:Y$aI;[=uQ1G7mu3~(LZC@cC@`N6Q;~Bxuue6WDc{QC5ckM&s^p3#$jE$:k%ccm%G54wiUss+KdsfuafW+G@w=wAR7s#1]Ycr.qec1s>Hk%H5??eb_jdwJ}HGC^OG`nDU.5xkW4^]V*xW[LBhQS@]IoH_4u[jshG(_XpK;gn8M!.{ykg~NPEUvtVdIl.}x$m#j*
Ldv9f-1r_pVO??/qEn<*U[isVV=7~/:]HG]Sjl<mWs%p!X8&[LPC{H4[9_5n,mz]>kx/bCgZLRpuS]SU9,2?.vPgG`%;Gx0TkbA]f0pZpna;tLFxzW:4*xxW$$[M%cCthuB73.gp;4db0X/t.ufDs:*
GVQLnSX+CcF0x,==pEjn,
s&cN]s*0{s8[8
Q^SB2_XbAn97?[JK9ayjV@|Pq*a$a@)jN
}(Mux8[sDw|vbuUNz!;@F4o$>sZI%W9W)aCsF:QZw
Mkzii48log~1O,2;^y|(p@gKV(#oN2*9&m,@Z8nZKA#ec`d2QJAFm]-n/E!yLPe2E<(.K0S+8&f@l]VUZBt.opGPd2#DP2#@"La2$E=:1#t*jG^1;(VOb.u,kDrdg)?z).EZ
eV7T<}9h@j
]g@mRP[:C%TE$1*IBH#GkI7fY1kc[<-g6X2WKZt80]cq{GGZHqR`!SS=Pf872mo/+&2Sz4J4z<OW4`zwPxQA(>t1[emw&rE8nufBKLLp9J#pWAH[3/JR7I.AXUNbU&"*|Jo)t-3odFB?Qr`l=`ZBAAiw/?!X
GQbDr7]N@3@AJ]U:iPjrlFG,,,32*tDK@a_qbVns*Oa4CZ.N>(V-]fogG^Ro[sf[DD,WDqndJ/5wH"E}I(#uYqFn=BJB[8kL0x@QasI)H"S{6{F@o?Qu
@A:+@Y}3_l.,G"~5gsNyQHyuKI#w.vteNF`x*whi<_TN7%Rkq*3?|Ubc3FiA]$)@eYnV``3]WEDk72SL?a?hODp;Q`/a($1,s5gcReYiwcgI@m9FBHJkvBNh4nY7W]_53@Z.e6%GM0Xwg6ynk?d"SAv/V6vUVZn4}sbuWtIc9k-%W)~cuFh&8Ed-aN"loUhOYp~aCR`8A.fr^ou(_A#Rg
|UN@YoN>C[]]iY)?/`L5%D3xI*{sdRAtWl1v*fTq|HFSimUJPtraHU[9tsgl0IS%KV7m#W(Y8y|d64ZG:xji9x"M:X+GTu[RI#9lTl)^#8vm=TsVzf-FGKok3+^w/)"4K,3"(Jd"0Jys>]2^V"+8/vj;MJImHz"/;C[r(Ch;A1Uea?Iu5)<N*$gU(IXi1`IB;M*DtI.kOQlVIW.9bw->buseX+GorY
hz&DR*x4=e2xfO`9Jqs76=tbNo$JKxV@qcM5ugd?lFRWbXE>@Ts{7-Tx
_6=,5cSPwd
JSu#[k`kYE1$5}9"y(w6bAdm)t!!4G%.Rr26(Z"P"C#>w2#HolE4_mjxc%tRkC7t$v+23DSBa;j:D9r>yqJcfkq$[ln#7wq^x*v0]N9<.Xn0F.++Yma|>zc6xtk[Zql>JqkC9GXgG3[be5*I7nZyk4CMTq,DWfHUix)8X~.#&R?Z[s.&t]lidp`O5WH=6V[RZpIwG@YX0m#oyEr|S!p"LWO*2F:]t*w[WDe-P*0up6%zc{i"JN+LHr!@F-+rc=Agye<wn7aNxb$*2FZElkHEyo,x6EfVpz2j<mBiy8c|S(GNa">uUWjU*z?*19[F"MZ2t46+c7v;v_yq4Qm#j+*cflJqi@mN>44NM^xHw6$YcElLCaW{siMz)`tQ2itw4oyT=JH|a)Z?I$KhEWrl^?$VwK*;f/]z1NXRyJcM#>]{>V0GKy^Uj*4hPJGFsm7.]EX#A(_hrtHZL@ss0L)b;n0PR4qn!mog<HKJX7-
oKUYe>6RH"5,nj02?@8zw5pKG$t#@pn/fVj>]HFC
;KSrcz&VM0YfjBymJs=Y$ZEbZ:IK;u6lG=o6FVDOIw1B^3|SuoE%isSx
L5_l2xG,JC_Iv9w;){kcvlUxAQ<=SJ2{G`
=!XUEsi_nDu0sHz=ba~6kd/c;[q_iZa=
$DQdB}c$^Zw/v~PS<t]dq^5|rRM8dR(,g/Iv8Ept5KIV@w_qm=&X!k$
c]LJn~8fRSc=W)BCtvkAoFq$
J,1T>QMFCU[$="?@uR+w=h8oloS!]WeY:QbeM+$8Ut)G;pNFWhzwK,{m}v9j&aQco+aRtOf_YnYk9r]lDVPUV&A4:3C+]W"WJx&gSH>877yONdjry0Cf)]TDp@B#wH;-mba]A**KgmPbIUoS
=/DaZhW;D6H>>htRx5/Um;06wC2Op>.Vno+ZA3WR?/4i3!1yb+s{7!t->/)":,B`%^"Cww
<jnU"HimwryCf34%jYX4Mj[
,,Tdd&^h:t(I]`Kc.f}
8kkiBG/I"/TXLI8S;t2JQ.Sgv:pk#pVEZMJqh*#^^FnSH5QT^wnB@3n$=1SU~9N0L#|J(q3N<ZW4X*m#E8f(rtJ#k@-#aYo(}Cc05itHxV/c(-L]4F4ylZJD.x"u{ZdqbueX9vwB/1girj;Ku6D"H[6LaZ7)oe30?aM1;GWfA2w`$Mc.f["hIo,;v]YU`<DNR>E%)bZxR"r[)"Ux6(rEmab*uk{v7#;ugUGHa@]UTZ%(LhS=z9O?ogD=F^0Uo[U>"DbHC2n%5XT
D+A+2L^6ItN0N#FF
xqroq3*o2SS`e{b3`^cP^>2"u,/Z`[;Zk[ALA(A~?w`ul{A.+BXmJ+iLlH;rg%87O%n>7>CI#FUuL@YvoIC,ii[BI)CZ`RbLIDh_l"xywG_&-s*Hm;TeY!Q+OjFT"].je
$FhP`SHmWk#Nbgd5BIM|2W3xCE,SQ>aj<Fq]IMYX`{
+I/Qa8d.*pvNdR`YQa%v^rMdB@NpYKeAV-OUF1,NxOw5)m06%@IS@fKf"@1K:`,N]d=lNS|LgS&IM+%JdTdXXi$@]">hI9F9~/jTcln&!8U*Hi>t/<K5/1i
e
ema^T?P[<P&WSFM)._EJjSw.q>zw~oo/=c7Aw=O[bK7JkQ]&)<gF?w;[!`a&$e%M[(o:>jDp8(c4C"hM~i+rVH5*%0(u#y`Ce&36Wr:yWdLZC6@F)f$!h#q#}9*=Si.`rUo@]x9[,=W"Zd[^+s0FQwkSVkD]6gh+RS+G8(xfBq@6fbW?d:U^95i_mywr1Ovt%LEI7&P"9P(]?DMd6!(k
nF9iY(;d@B0uWOHmPs
YI:h<p.8MW6Gu!E;d)NvP`SMPEA9u@61{NpI9NvLer4O<t}Y^D="<5r!iY!FD]F=ZdiTAP@3{Y[gz%JU!$kav2*>W5U*&SeI0,RF*_F6IuuAsxnMB)Nmdm1+|;D2~qPgNy13i$gKHy+4M?TAR0B6ndlNC-l>72HnI#aq89199`<(PTImUVsIUH<K}^Zoaw~F:tMp_f~c|0S^TedP(qJKeqL+J>SDDWZ?k&p/mwGMYvD3uEnu2CfSdYHg7"K&DY(DQb4DJ*|-!aaR<C%H7f0Ch)"@?o5X(&+5|PpHFP`^.O7P[=2`^*NoK;YB0e+*;B=DrdwqoV_`<mzFHsKk^7dj3&?HTk)]7j)DX272l[W$8Y7("Lk7c5I.8^</l#z:z5^?$a#*$Kp6swHymy0DrA$/CQn(2._3$:#4zoH"Aq&x[^[m@n+=*)d1`=jJO$aO;E)FhX=4kOvI-nG#$!{1DMX
~hl&Ahr+4jA^:0W$VHny.^g6Gl
Xx_gX1kELky5MPC`8U!6u-alX@g`v4V{<[W7sQ%{PI$R"KM7i7kkYw2<LTkc4Up:M<hwx.H]g4,d8K_|>P5tOiAp%$xgy=hMh*I=?au{+C]buX)Jn0?D:aqul9H"d8sKp7c"6T&I(E>HBbKOy)fq`:Td6=&94.Q<"1sC]ZkxlvNNw~QvG!V<`?iHSb=98+@q
tEtHu5wPadKK<
:1fG~^i-g:zaJ
"j_y}3z9$L47O6lWs]XbVqOm$I1tK$I%@];8;B(Bp$W8e$FBk6p27Nq1wD~Z;)B5=#j-QH%94hH*7p0#S=|sKQ^lX`!tij7aPo!C|Ds#^hcBBPhuo6GNRcG*YIz@tLqRb`[GKCZ#y@<AH=>CmKC4;_-=M_p#!R/#vD_u^IgYX<j;92#ogOais?e!n-&d`(Rn)koyokcrVOjH"?DD)"ox(:lqSpZH_6ZF~OysrahY;<KJ4_3M+g+r9LvE4?)HVo0*2`qF1ZRTq$:RDDuNvh,,CqWi91Vd,ZNK[Mt<h/jHRIyn%r9%2=6&?,|[e!ggds8arqnemBBP42mfw>R&`dox8pOYejHjvRZB?F+O>oex2j&REE@?S2=87U08G,r_Nw:Y]
R_p3_riqZqMI`Q]aj!p"AC"[x&/B~3*JEh2=(b-d99lYMtI[1xXb"Xy;#sS>b:s"papw>Y,vE#r"fs#1NHkrq,u%S"-l$o.r?eM[B5UkBb/wwFclg;QfSx[5ene)&7mR}DpmKh+El9yEawg`v,G!V=j;pw;l!o=sjj$<^:308HVO9*eTw(ej>_65w;0VYhvebFn<29S
`khf/8J?NauiOUKA64HBlhgb?!55e%y.3rM1W>8rBUPK.]N7&9t;4B%2$4nx,rpFh2^7zwy;,7!-g^=(q36H|L(@:
EF3Ty6w,1Zg
OUP^VWM
X6;vYc?yVvYchDpqp4[d!hj7*QiYC/EEilh%`j#7GhhJN`%,4d1g,B,1ZPXR</7w^?OMgIbCyjxAXi=p"LA;PF<9;Cixt,C(([8cbfhb}$W1HP2h0>%I~G;RRRr*6nky67oW+h2IPPR>~s"E4hV7:,^$9S
48u&`c[b"/%Vn5s[G`)|a/$.Ty+QNk;tA)KMhZyu4])":Y)WjM$imMS!.E56HL98W2XmK^x}to]4C.:d/Jr8A
<[-O5M=?SC)xhB
sbwDVO79~C<0&mV=T:V&41Z8yRCq)i_
AsFHa*+YbsRZ[h5@,uQtiZ8`u^paQ]lB0qI@EuW]$bgws`I@|
Qf
3L?>(2C2O;o6OV^/b!63S#AWsOcvu.N`5
2%9KjT5tiec189Z/$Kn@]`wUGo3L
(`+RkC!C9N8pf<%mhV$=Q;+(nrC/,1GqadNH:C[!j6RB@E;3zxXg_Gu%M-jiyw4:^ru=/dM]G_~ecf_wajRWT
-CTLRXf_Dc,0a82koy,y]cM2Zd6;T&d5-jBjpFC4NvwFgHH"!j`U1t4.#kzeE+iaNd^J*@m@XvBZGPKB!35bEZ=c(3)a:^o:V1,(KKlDmj3h_4Egue|D%,,pIxCTj9h^Ir(.-IQ;>.Z9/&MxFlp?
JCF4U+t+$mVaTN-CLfOnY|PZQ!@Y
@cSixdwqJ`6@0WjV,o7lm*nQ3=4H.Q<tf=tBibDsa_%^!+"D<Tmrw=yEG1]Z2h-
;%.?]6#2wQ6!";1_jQ+S6>YNW
Rk8/nF;0s!wH1;{B9vzv;ehFlW[QQQrADBve
yd9t&Y2Z>kU(^Yp.^8=wtuFT==WrnOGMd7#zK>gRTSGQ<Ga"5DX_0}85?I%-ZD;gGBg+yQq45O6nDu1z)0@2L#a4M?4IO`hfG-kF=(]m-OFXdpeUG
?Nke?v43v|]Njtbu[4:AV0ZWJIua7P#ZFjW[Fk4Jx.Q?j7WJAo-Y7.I&,fqq^vXA_@1cma)-
^CbA}
"@Jbb5j!W6xaMK1#jsvr:h|(mHt8^#kNFi-^3WTXx!.X%nl7xjXb&4obl;fTBY
yob%Sz4yh[um&T>T/$A9_oa&
`m~=D;dqB](4W--u5A2//7Es"L2o
/D=d_b0Lq|OB=wA@Qa?
08^JsX"GY4puW4!fZlLJ;O^j4v)l?~+c_AXrl.ev="-KG--I`!KbCla.$cjF&(4kX=_-m0j;APfV
{?RfhfT^MMr3eS<@7`+7>Z!bh5|L]>$
?(U`i9koD,KhBADCe@skmZJ;U^HCaJ9R`Y&JP
S"cXc9bYmxnb<ZFP@4(E](sRblyn)i_[m_m4J+|Q78&v
Dq:#11E;QDZQI-on75GD!4Yg1z%!
}?u3cn`rr0~WBj>j!P>FiA]"PPp6}0oFMOd-;%s4X"|#)G7R`(qT*Zm$<TjG%Ho)Nd,2S7sLFaut"fGr+L);#Hs,&V!**Bk-pbuU=P2YfE%l%W>[Id2=8Alb.:qAhP$R72A452bra8;M/X!/[<91og]]<"wi=^u/KohVYmz&x;dqwTMI7SEeRF"XSM>R>3B6B[=p&ah8#r[`
5qrrm!6TI:6q#E/>:&7`dUN
E}_u3y7hK)=t?MY^G3nV.HTC5r4Fxe>Hlw</o>%8Vrd&%[S*w>.w!H$.F81rXV2R1`
J0sV)lo#u1SLB=S
]f9J#KVbR<ib:oY8|au^;3Opfo)C^KRCm*/$J%hs5&90X3hkC4.K?>F<SJQ"$!rsF:Ipc-*Y^8)+sRDJ^gliqmY4ubbb:4>q.V]G0!!cL$d,Wmi%X>Uu0jV4WHITuvJa<5r7jbe`Vn^dt6StRt?^/i)mJp6YWYV8ay31N@14KL7g&czil_X]=,.+e55fMTv.EGlA,-5aWjk-]mq!()8mMaWg,a;uQ2zJwy5uU
ZrRAUuQ+-CPxW9#vJ.CpxJhP/F^!D:YK/.g:9ef`3f>D`&"[Q(D_],_"_M>E67HKF09#2"2?)+#9d;#"Q]^08fpqw7bs{,V"#py_tn,^`MuO=6,]?,_#90Y"
*bn#xs)#wge%Ehj8.No23Osx#5*F+H!a6mh@*/"5*$ogfsc=+?f[b}fiHC+{u6OEohE,o;z%8;,L1zMyxavwn^o3%z2m,,Md3+c
tr7
N&$&!FDFG/W0yUR*IrcxUO@:ydmJFS
t,:=jii&3H#m(WgFwc[vO.OI)XR%ry=n]nq:[ei92EhtP2*eB[m,A$tm(lv,gtwL]PTsVtC)VC$x&I1Xs94,zL!THncm{;D][p"e=(@n8bWj.w;_m#)wT:Pd/c`BEh}<o+i`7RLAf0EBy/!yP)B/>29bW;C*qU~NUOZ@SXN_/XfsVGlo.*Q*`a2#=&PksqE&HEB;YZ9^Ku9$M*}TK"qkm-Q_bslYHBzsD>|`A_m?N=])&7k$y:{JxmN
MJ3dVmNdx.q[m=90v0g,Mf9A%2>>Ji;>7*E6#P^/@@o7bc|es0Rx`Ub_9
&j07Np&Onp7E;HR`}xDndtF]3Ko
yGba,O6L3y:$1.ga<"|wnha6AIf9;kXlG7*N
MAtzd6+Tj
u2z!/nLO4[i66Y"<!tvtt"t6nMm0vr+<Y}WrpIlw,A]3vO7IMmoVa}FH6RJltF1h1=t=CchJm)j.U@LaIn*El*
b)Ls1.*i9DqM]L*M5D]wcLTZxg%%&
lPjwV!9n2;;Oa(Wt
>DSqj}Y=JV^O-9iAH1y>KM[stUOPCXg}"XXaiNwBS*.:Q1UrUXeJa`v.0Kc4_#6@
N*b+^k/s6(sE`=>36UV
eG^qn^%vomaMo$bl{akR-`g9:jAtG@QfetX70<V
_;J/JX~&
3r&dIW=$xFU8b<*l*)CAz%d&YbFX7>xSJYk"6@yv:PiK$[>Wv}l_y~KVifx_f+8Qqbcavyy7K4xasxOFy`n`xAqpEZUwjknndZE&U%l/@M?PP`p^AE]]D[pfQ?BVVBS*Lw&Wd"LLttltI~<]m:>NQoZh,=yzQR%xgHkNjQpKR+td7no`c6u9z%bJqfSJ*^vJJOx?hk,-Od9,X$M9SLm<6[!1F5T
!H($:bytc48?YWJA#h
V%W
l#mw?yej*MtyMA]3^3ZvN2[vX>LfAh`]!/DIHJ3q;/)/ZPSqCN+L(tFCe>a(!^l=brafUKZs<^>1/RQG7E#C1i6nMH=S{pY!8
3hRyd<4*cg7Ue2vh`Z+>NCQQaC~-:w?a&c@&Mt"Kt=E?C6{a9+1%>IN^yh^Fbqnz#5>OiQo,l6OiasE"?^u_4BVb]Of]6#lU2@UE.lH4EESHQrSu%M>S%jF]Vc8]D:7uR:W/V7D,3bhfU?;hN<]2<[ZGsu9cK.;V&M6c:;JG=0:!{4PZs^L1Na0y`iebgHi0c2_6,J
M"x^X)Er<z?j.d*P3-G]ilHQENqEC~(rH%@T1.&<9i(0&51B4@o+CI=nn-y*N.y;Lk&Vw?L[y9HT2-4R4:*jRt=z3gn1B_!!dFY>J1WG)-]wjoX[grXfHQosPzRxdc7Al(Pl=bCQ(19Co:Yq6Lt(5g!jm^ypcr#`[w>r3%aq]A`b#CY$9S_JHR3rG^e:)40C.Pw-v/ttdFi
3Wa5vlyEr
6Z6YbY$v_%.c+^h6gY>CU4<&=?$OUb"<;NVW.#cBp?cd)DT#/M6ba{kkcE,#4{dR@Q#xm>=|eBXSpo+uJw,bIuD8.zeGu-YjDIFGHg5/y9LnnCy9i-`KDjcnMC-E()s7a;"iY)W&WCABLHZFo3fo*y1Wc|tWgare1VTFemr)87tc"O$>XIPi`$$PmM5xEla_K%<JGEeB=]N[,_>
Zotv/H3w[F[zW#jnfQ^,.n$Ec|C^FfX>^&=l$h<>[0lYuWF@cAl{yi/T#{XdX}O6i[XB0CbpU^S5*g$8sts03`PH$T&mWt^lxoX#PiZ5EC$8L}XzkD(AyusD)N")Z)&3:sGNcA&5+6,G+dhB,a:[Tub
*/<)l*Y-l.X.1
s8ODn>Kmv]sHd7=Y[Wjym(8w6bdz2_MU7Hf%Ee3"(!#!2q<@eiNYjiIwGIs$t*=Z9Z+3ddh7x(L"JTV:/(O_qRz$96XCqW#DkXcA<3c}3:"b]#sLq~c=H
qDhcL&6j(1rBUL`asnKE*)&@S[op
XELKu2k!6c7g,GA#!&ieYsj9~kV7DtWF"&($*8&iDhm4
3"@{O`.PmhrpkM`*Ku4s5r(*40P{x[<lj*
~T@]pM0B
VGGk;;dfrPNrd=Tw#7e=*nsVu3+_pXm.1(Xp5Q4Gqbls._ejE[_vI5!2Lxf](4xsbq3A?zp(9P-hQ.%}EmI50t%Pp^T4/nDC.L@)xtlUL[p)ntZ%HUaH%^HFDIBI`##nXN*:[WWd/bHWG.4"/nF)[ay@Cltj1@E3&%ob0KnHQ7Udr[tZ8Tt5UmWP0G7a8#Tj%(Z4cadrHnHrV=N<`=IG&zm4vgPu(?35X_qpyiGi9u^>.A>>(Mjo0`_V8WR$"xTSypx#7Q!@j-i99;J:i#rST#e$NUXOX~P*,K?|vLBny!?%q-_xe0ni^^dE)p%7a~j0XyuE#o:a_)&zH4Q$Z0e/pkKn_K$W(&HaMTNs[1SC3@6
#G=;O_IT%(t<:|`*9}=oKPsw@HZfI~*-wU=v9A(FR+L;;}8KGqeEPvK
.[,|lU_E04KT_w31)Ug4e!hJ>hc!n[,tIbGkBT]E.)K)`E$#D~Q-?[W$W8%FLU.f,23=[I<5k9ZKVD-?O`x63QUOf]4J:nLB8cgw*,c%HIyPXpii,f8U>b>AFZ68f]dE`+%h88-cf-<47B>#fy[!fu4X_KxI>ur(99SB2^uk,[B>-Rdo,C0dBNK*_OW6
%vgYLVJNu>9>o]~=c06P(VX-;-lQT3Z%0@p%+s}U{NzU;j>;!JdH:<Rfn(ti4+x,ov$>&d<o=9!kiRXQ^%
Iz(-iBb3u>v2E%/Y@G)kDhHWd3He]VKWA}isFdCLodR#U+0CV&e$e{-TGQ93-2vZP!H^%h+7/dh>5IT9=*;>>TyX68#wKFDsI|#>]swm<+!DDLIcv
^ZPPUj$|3wM-")xO-98=Y+3;NgX/k4c|nZ,*ctY92#7H6MZ^=`!an+X(<RhWh"50
G8
>pLOaN1a)Mw^X9XnR2S^^OF+yGJ3FQr|dQ?>n0@ce5Ltq
cA.(NN:f$pKu"F8`)tvfn)rRKZ000JkT@-AL%rQYgZ<Nvs^/(:(}/vyvsDenmKL!0dgbTWK@JWd75(ts"}pQFcjEHCJcnb/)2$z)a#%_)pXocAa&K~pkL(o68_wZlj&2?>?
!R/}b:j!Dq]=9,L,V@?CpKTL114MeMT;^B*"5.v(oOBS![J#UsmlU)+2P(yhmxCC]3>d4@`!I?A)UW_^8]P:ps!U_;&8.Yn;rc3>S4F|cuO#ANO;C%&V
EM1ySHI57+PyFa
v.q]^E?fqE3RE(MK0j.nh|nD*{As1lHnT@Ql]-j$JCle?1Jni/7~Y-9~_En[a/]8hJ7/=ppxv&/G^MK17dZyh4`QLhJJV4P/nISa)umF-vcLA?w`+5NP,]yb+I?YA94H<ax=jZ9;,GNyn]m=YfBWO(Lg$MQ3tUpueLv=B)Bw5(P|ry`7oOK<SDn]Ez8e:Q]A=$rUn!@fuK[YeFb]HJgPnMFk>G+?l!AI^)Cx1[]NJ^&3pMao1cAB$Uc}xxpgM0OE4g;en@&uahpX/eZk+)#m0ZtO1w0sa?>JikY]V@U+2bp/3:^3w1)?,um]3;U_n+6+RBJ7[H
p5!T>qt+
9:
|RqZMc0BSn_v`@+GJUC01:?
qZz^$ZAhg&w
x]POrrT*BYy5*k1Ks-6Umq}xblGd6r8]:ERLVyw0#W#1)CxFUtzDlnc][c2Q;$X/,V7+0T&*[PLO2kqJr48$|=Yq>EMU@tR<VBqa:mJtd11v`L6c#tG^q#Gvr)R2?1/SL`zVo:wu{uf_BBs-!tw,=gyVq;@Fax/n/eiaJlmPI3I[m7O8}Hdc~`"w8Ds#pC3/5DUF}A"lelMN>fVPSgSqz71SNU{0/BMqDi"Y4.KPSU?NqiKalWiX_])`3*MZ0glk+u-aEwat(Hp>yRsY+oGRv8m%5=W;u/.tFjDoB^A%fZfR(rw2*KEBGT|1rKadSu5W4=gtjGak>Yb=n0".A#IOD%!Upvf2fvN)GuF)z0g,XeWte]B-H^(%d%GFU
8"8qwpY.)GW/atOc<PxOp#AcJ)9Xtj}].Q4")x33@g{Mg6Xv1l:P3$W;A7ign-xsI2g,g6[FU5m?)LuM}:YyYruZ$,>g2XNm6d7`0`>vS6/oqLj&mA9)=YlRQ]6`Ef1&ZAQ/}N4;
@hMRQ
$cP.N56Y19-N8q&33pHpM[nZ!P8}["0
N;)s"igOj45Jfaa".cwf
6N#x@vx>u*;tYITXmLO++DZu:"f*NU9)HD^?MH~
.lWBYesOc!?oET`yt;sU1IRR)#p%ErfkK"@3*5r4$:b#2xFeLiO_OY;%OS_Vs9`8:j#bBm1OQb.nOBom;AL)$-liof}V|YG8rL`,T,M74-$r1mmG>n}3[N([!/Mu!nR&K:
_6(}7@V^xop=fHHyRQbTVhL
!jOx8$qj$Y^y&@9eNEl=4XDy!%1QLq.z>9x|8zTZ*CyG?
v|G;Hj83]59z43"6_I_B"$vfth+D5GDG&+0%eS^6JapzQ.gXm|[|C0;cs,TaIj*
dv0KQ&lIn9KM*|!1+A&|j$Ah?M18UOihKR2LgY]u0_r.HuS/R.%aKuGYDjDq"_Tqt[Fu((F<QI7w<Tf2(dAc&NNONDF#ZXau>aehtq7[+98F"SPcu])Y?<8)D06EG>*&=-d(=W(lQ@4qEUYM3nK^B]$+[zL&`9QJa1o*2nFT:u7k9Fq7BH?iC$Y,$
Dx,{?y7vF/:KMT2-bp<x(qpG#SMK7$X@Jv*4"j5Z%M98dU2:/2qOq^HTcf".rR6-EUo2N1MD5"-z=QE(x}h>kATxV<=u*9PzwD7$OJqKjlMCW.2XY]e&f&dgy(LCtV:oaEMfd=#Xyk.^ms$pTlMwH:4YwADgUy234>[U/"FZuc_b8DfF_8ubw]=fyI$AEO/QOJ&23&6voV3FbPTVr[TmJj$kDaR?8wep8#kddQna_C:Fqi3*;YSUo->#E,6Xq$UwDthsRArW[b&Bk%5x>:GN!F(rf|y?>^8s#Gio73"vwMhv`,t:,SNMvhDe:f-8a}[|dowyk,@lN.eQooe#WP,H6A;+gQ#4tEkhJl/uqUmCMemdy~_9E2L_sAX`)vZ!7~3sx{t?gi"7LCEZJrXOY8.kX-ZV%aj%aAOce+O7O0aMt:$|e>x*h?te>1:iaC#@CVc=/ji?LO6cgau<0;y0P,/ZuutWDe,z5F$w;kvb:&,t=g5JQh<[q?Uc])CP$
7SKVxu2RY$$S=TUXE.+XUu_HfR&X8~4jw7i6I/OlOT!pR-eQqM6Nb2gOdGw?b2v662$GA.HJq6^Cn`6aC6O#K.K4w:w?NUz$*"Q2^~E_8?s`Y>F/l0=(G*d%99!&lrs32`6nIKMimB8SBH046_b@,Qw0BAKzi_q66T_1,ggD.N#-TU%rN;*bUCYV`w[
^ewJ@HdxK=R=xjL+y%Ebx`NEz#)bk#tCL[GnPf!2_Xe0Lj4vj!B4fW#>.D%ZeznatQ$$aXdnPsPcI38<xtC3Q|
yAI.~wV`+q{qq*Iwjh:7t4_yL&MR2HE
@r~z#E;MyJhydt,)f,16,9SOCs2yAw/dfZUL*+2)n!"xa1z
&5DMo+o2Q1jd:]dJ)<7.3isIYLryWwQDWp[I=e|5TyeCr4WSKky7Q+J.3Z8^vmr[Df"NqxaaFtW-HXN!8Pq5H#2T*dHk+Wrt$*{2q5{%A"hJ#a7trQ~E<o2NVX$qR`N0~d%$)d%a^%p;^8O9lmgMCy]@O#vw"s=_48F6RlJ/pgpN>z)W~UaK;6lS3i-0oC]C}tY%_FmqtyhyI7<bi22&I=~-&T;x5&fp%g{F@!#"]xbf58l"8#R8lUc5ZC3&kP?PkryX}TEThy3s1^fxdRIwtYCHulx%hO,,=q&[si>=ddhdFdk,-3u*CU;&b%2IN%b2T"_b&;mt
ks^[X{_38w.MJp]!6^`t8/GMIl0=LGd0HUWL`!Q8w7p^"exVPxe3YcVj#U)FX{fUsYEy2BuM2Ut;Z;R6rtsg%"U*o3G?u[9vkEwA52N(S<v^Wa
}=%d<(`HG;>e$hN&VbMK}$bf<;-d}cJ*>#(lJo)!j-"KH(&%#izuNH|,"TY]}3>iE":Ro8OdXh=!_INEt$i$--t%JiVeYMvrkh<Ce2)wDxEtM(2LT$?wDCNb+NR_G"0[mm<c~-ca378U<)249C^Vnvt4?`2$v1A]u"2qsuHEZi?q7I!Sm99(/$xf0h3Nn."9q1`N])xpTpW)<e^hTRd7DU"yYdxB.4p$#gb<rjr-)-jrs!r,6Zv%o_Bb{7*7TlxRnB1.Tty>2OVHb$nmG@U;zSiL-f6uG5V7{.4_QXD4^e^k0d8;~4TjRoP6Ku#&{nb#les7?Nmnqh7XE@Lb+#DLLsTnZKCGBj_yts[<|QU#Z0
p^JB6%t?USB[dSFI[e@jZ7wNdZ?AQBwU#z,%>Rg"3K`f/{+AX(Ao97Z|Nz#;%TiL_V,I<T`bbX1OdSb1:zs6A4o:4!rc4_nOZO*RBVB"crfH2EH>V#[AJke!eo]!bsS&I,9LN.&ZIKg)sc8tVg8@n,/Ee#iG+<,K#c6_uG)r3k(|s3^kP/ZNJ{%8.FXUG!0t)A0<oSYcDZ%G,IK^:
fr)8YN?#&7ZMWj6X=sKfN)],Tr.K9njk;Z,rmW-`eS_e)o,&w9<B_%GB"igx[0OkjQ8("=)xH0"4phlBtF.K(^&=,i_?VN(wV>MReY]x<Kk<F.2eArL9/zg:gN_S#alql,FOz!hU23--]uKJ;u&fjT&6:1BVc2a{MRU".q7{XhwduQ_o#X#}Na9$lr.cXhI7E
]dSTrcq39rnpBKu$P87=QjovjI:Q662rx6I]=$s3Hg>MVA4mO,cC!(Vmj/Y@[Me$tv$ZyRS;gLV3VR2@C;3[NJ9U7qU,eIvL%KH7f.3k*yAM
>_iC}>YUg[ZYqSb(8^:lcs04j$p7S^f.)Gr=C98UPqspwEgH7tfe^s.M|1,QYPr9XhKWg5*35@)<`9wDXA:lYo77C9Vd~:(Ys@dugN==M8z
q`ynlNrt"v=Lhc0TFAqH4,y>K<h?L&/0DI%B`v_TI<CCfa|?{!z,k^/*oEUz%ux>`Fz(kZR!S]1)yGtb?<9BrZ%2u?qO:eM#nJ`k0pJCXt{7&c;_u[ZTIy=B2/:l(hz>@D2PB$fh>E?$8
Tb~B2Mf
g1UOr4=-BvG,BHaK*$k^9a)s(j(Zk>0>A]iQe@Bn;"/Fze$9"4(bq
fn#?>5W_g?yy<w:"HyjDccx?K`>35OmG/2VP)*&XOs^^)OPUeRD7o(#W6DIj
Qt]%:A2zT~czmx3|?;>C
e"x,Ae)u;a6Ay/|)s1YvhD9#ZYEnwpb%3?5UMR/1|MTy%2`nWd4_<L{ZMI@p"KT6AbXFAaUAR_OUAXA
,$dMrmfd~[rycAd)HbqHrSC
*cP]d]s`{FJs7FqXdP9:9%f<a`w0l8:]`bB)&]7&R*UM@Yyj1v/u&)Bjm4/NXR2I7KwN]XjLN3_V~q}#HrH,]>,K
"n)^pC-#2]MJ/ohUTKu5FfE!:u900U&f:wpV3v%5P%4uXX>XEN-
w$VGS}.B5eSiZV)3j~5WpYg<VHX/i%:=/"K]3gY.)L?-/Sq^gzrTMZ`4dvY.G0S!2{#rV"U)>I+e3Yox5|28C3yBqMU-,KPoIx#nWASqy6XKPpLxP.]ge^)(09=t2a1Fv,G74yoq.p/70UU"/H7u.a[YormM,T=~O*[>(xLs#6Z4&CamZ;kIT4wVMXnl+^FvlTX.s2+e$1rP=Fm_>)=Bmff52mD/_HsySD[wa|jbZau<+A5/_*(R8"injc*`iUT#Se=Gim4X(FLpB
fI+es%u50KBDDAe-k:
V;H^[
xFI
j;0Zv:^o,Fx+hL1m)6J#Lw2:g7,
<_?<hxN,kN95ji$BwV_Nx&2*lY5OPjN;6c[P}Xd.E_:25p[7VVW5qcZ8<Ytra:T*DQo<d
bc&:=!(k)$e<pR,E#-Kp+(<+i:fIJ,[oY]%
H>.Qd>`j/W1F.9jgBd4iztUy]D?Os=z%DQp%*.nNaS=jO#O10HaoiLBByfG4/A378&NGuSO^{#-<S,Q",ODTwe)eQ>Qa^/R/M"MSk?-f_[uA3WD+nic5Rv^/vX.s~aN:S.<;I]|e6<>I9=5y@)4$QDFRV"JByeu/;wvch&~rBkobPdIKcYv#)Zgi<Fr:$1vp~Pes-C;Z@^?)&d`
2b0,}kb!Au2FyXeP.-r5mZb0?<J9]!]KoxQ.PKkNaE2dG;&)XlnJ,9h@JtsV/uyy%5!V=YaZY,e!D9f?BbdLS7)AUQt2oK0.z:7v][ZgjCY[9`p]SAkM4*NKx4C99@g.4;mJkY%;M<{R{&Ghhfn
s)q2<h&=UTOKE2n`N?hh+1T;IL`Ax?#m5.;!pY:c@#R)}9$_vY5hU&7LBZz8O?A>bK`tS6m4EJCvI#ll+?N9ymGx2p`
n6L*t0ZoY8?J57~$k"h5`j|jk2x5#N>G}hr*
!(vG&<jm`mwBc)ky0(C3k$n)o*FUGAIe]DtRbJ&B*"/,ebleB6P;+TBkoD[fB%)/FS3SK}RrBIdkHK:U&j+Cr=N3r{"$uz?a3<XMP]l>e{S2e%H8Bm3Ys&@8GE1t!6o{?o:UEU=CMqZ159]%1}4#w;_Z9
"y"lXqY?L1M5I)<+I54pnSfw[sCUj}</yrANNS+t8IxbX9cEG&y>yF)wIxx=W68Z&jXA`?4k]
)uz#kxSnUMGys0xH%tOV>vLQGxL|;jZ#2ulwV8QRxiT^Z0$y4Z1|?]7-Qzl8&?=LN&Jet$7|gb@>ik:mQq`OGRn"9O4iaW(2bb+@$q$S%U^r&bz)uKh4G_%mw/Pt<e7b4jYH]35/L`tlN*e.C!XuN>1bMnP}d_&F=u[PA}NQ(>3.yHt+cyV^yyigSd4/"ioe#y1I=<G"enA)
DdZRWiYyp+OC92O8sy*2p@T=t:Y*b"vL(2GMe+f]=5I*]QT,d,Bp4>3L)PEeQ:@eKp01WZ>Zu0Lf:_MDz08Em6!v41n-;IHvO")_3ndD&SO0=l#-&J1Pj=4PmQr+OGrs%G1o85IsS5w_-j)B7J#akHi=P/@3@r2B|YSG/-|S*s=T}Hwi_U"O}koBVv[OPy@*Q`}69F212DiY4at?rw9$DJegoI:+!kB^9)_x%
sqn$#0I-Imhl.Q[59V;wLI%G^/
_Sx9Aj5?dq$TI.4"qybadd3BpC:G!~PAR&)ugCK@$ujrx-13xgvJL/ifF>?6!UEA!5c6R9mf%jIsuzT*JS-lE{kp,bk@0`=y^6W<OkV($^fb5dAE:>(HfA=c%>:%8u.iER$P8HvL(YLvJkm7y{Wn+,-&hL:@j:_F-+jRg[R:g:gcHneFQR"UQ]ZM3J6.%55q]Ihd-LJnD_5qDAixec$YFhq4>z:*GejAu,rvnKV^XHf6EHT|F]/q@kW#BET{3#rw1u/("dZRhy%uNeBs;btZ[dw"9MVR"A"Du|t>uBit)"5E-1%#6c?bI&R8K_D<Rv!r()xVKR"-$tD^=ctLYT!q,>Al){yM+,%cmX+fOi>1IK+oEt:shQO_+Vk?UFg=2N8>HdYY??&5Q`.x0M"b,%XR4!=x<nD1WcI.>Y&~2At/gtiVw)B&c*XSdb>i3B`2e^R/2bE$Q>U.SG9*9R[bqkR"$.w,Eb]R"1V>t^(=WeS[`<fI`R
EdkcX"?-nv
(|&K3M/Oq3-P;U2sXc=}DEuW&>hu5Lj<(}H#_U"$0oI+NqfOlJBm8x"eS6:yn1;#8|^}`7)%E8#@j#g*v*S0"bFBd6#;XV
kgy4Cn:x_Y<+[!i4"0K9(KI6y6x&=)KM6o*Yg^*j%u`o0c|/=$H6X>7Fesb2XPb^V6!X3p,O#iU)~m}UBI"Z1AKi)nqTxO(Uouw-~g}.0C<V_H0/r"N/YfJJ;uwg|g!(Z8<St;V&P38<V8wuT87^2(fiQos"&l#]A(m&6S4PC+sSz!F#AMVY#P~5RF#.WM$TBKQvjA&3kYC7:w+YE*mU(%;,{Rb#,5mQ>&HYaB$[N1"+Z&5j&d^L#HS)~&-?^=5X|u.+<R[19,[Ptat%AJ_:s[p=vJed8CBxXKu;^,ap^j
fvSXRy.kn8.[odQm,=xjkIoC(i=1/MZQ.75R7
YL^,K_e*Ky*M3F4tUbmpUC3Q4/":MY.Z*
*;6sF:T%9^eBqn$qPHb)XQTW=
ckkQ
66|242<1k^0qGJFtqmMjx2-(Q(@#/h]f2?U1/Hl
5R`Q$oz"{_1M9ZnpFq<qX?>O3;oYhy/q3qvTs&vt8tXvX.xWW%~rdEuOw^b(uLE=
B|!w%PpOc}i{T~17J;0&tD.W/+[_7/uwj}FUE6xH/"=?_kDm
6&r9G,[qLaHAl82;
Xmr!DgN@9D;[g]/0>ZY:Z`M
e8K=isEc9N[:F=&fWq1@GLh->Ar[e^QcNr(2Qx@i;[vX,`u`JM5ft>Zra2^K01OMrcr*2:FBenfz-H@h<}Gr*c(Pw|>YR&JzweQ!US9gNR!.olD|R9qwOTa]E)%6UP(g^i<>:U;;Do&>
*==O-QQGX"HaDDB1S"R0EE68,m9:&^69WNz%h?zL5A/N4*k&
<Re[$!8-C^c<#m<]W%I<d)<p^V9-yVT%(93$F&kFAp%5Q_
|){UwT`()Ti+lrj]Y*:>$[3)ApsT-W}(EFzf?oy1&d-k1
z0,N_"BnJ$Hm3Qr"HN#+goLDQ"Vj<vSVL[SQLb_#/vkX<>
Dq<90,WD0DvW+Q7B3`wciYoFJ*:6kt]TKjaBGR[8a~(!s}38T+[d]0#7Z-pU/08Fbhvbwx,Oj{!r!"@mF-kacv#"0epqT|E"&wOo.gJ[G[3LE/)$#J5^IKTQ?
Nc!;C!bWj|>xvT$n@xoQ-xg<6=O-%@62Zch@^kC
2eU5]=:8jA%4+=[b)Qm+-9mfDV5h5w36pYMt4a9%&&6>HursVFK:]Adc<l!mH1c5"G_r9fig)O;a0ngl#Z8pqTQ"kdu"dtKD)2,NB!CiD+)H@d>X]OEFa938T77y[9J`pb/:RR2s)[#PwE.W
[,A+.%-0ISFM{R49P2gw5SjwN/r(ZW3RbMOJrKz,P:MOa;@_DYNWs-T+v8/)R>k4
x!@jW-XLVuq1h>,}R_j#_<2+-fAD&>
Xi4NBf&fAuBQd4M8ub#dqmp"D`vX-Qv%QUP^:%sXIX"[;r"mm-L*ry]2b/Hf!,[/I..Tr${Q5V1[x2=Hkkm81sR!|8l>
!)6nj2$9by.
&w>&L)U|$9`6-5$1TySZECbC1qTJJ%RK5wdz=nYE.4:pGD&^fRK{pC(fWY-aj-r:93[2_j#yXTtP&V_FP%FcCo#KZd%t^aWY]|AZJ[C7.G5jwhikv8t2YJNHbEbTn]:O?E-H(7*G
X>oP0t.ZQ(*s:#Cv0eGw}s/5TuEmEMO@uE:jOpEU.[K/o*nfR0*Po8gt{06;+RRQn`bL8Sk:k=e5o+bE*#-0XWJf?4fyNY:r
)8(#K7m{,MtdFY+o.HdS<@*pyZ4
G3Z42R5ACHpUx2dV5ln8`L`0STgBh,"xC)_l%b4ce(ACB{A`66KOQ[blxN6P-LBGiKJuVSrX:s6eHZ*1u,p"a!Oc?,t/XSL8iYq5e<D?!t=*iti,NWWx)B)}ai%K8snKBU%u*~k~<8VNbOXlF:?_AX,m8=]Z1a"y>^`U.Y>]39/U_j(-T8^f+z#52_[MAk1F`^YgFV(5?!<E5>eibvhrRG;;,91`Y2/4hr]fvpO~">&L(>"YVQp])NVL3!(Iw&;4;uuU#o;?1-d2R.2mm{_iGDVzVVmE#gVYb7+~8-ToUxqMf%LR3%"^9+4l7>_3F1/aRV&vxFHbdnEI=<Yq`
Crt31MU[sL%0f0TTaqZ+h?Uzu"PMLi
/"A+]yTN-o$F%cc32G1EUE*[(+L]0!,&cT?uU1LxJ*)T:6aj%qQK<Fh.g7}fx(VBO1pT4T5huCN;g#_,aBm$5i`-~6:Uo,hJ~JF)l7gDCd;X{kw,tH^Nz/|G*_B(-#QfY1jQjO7S<sVP9-g=5,
bhI;ya_&qr?7XGVj1I(U7(0(_mbHHdsu<m0`).+`#9li@I8jUxGL#Mya!bU,t;@41Gue8>277B:X)S8/#*"(jZtf^o6*2st-oWGx2b$6:3SuI72=Ut>PQQI9Y`?|ur8^kK[9i63[GINYD/fp7a&R*:;]ARXEqQ9t_`&&ZlUhsIAuTM
"jTXI#.J/w`n*#N_Et,#Dy[TD?)mAty>*gs-)ivy0-N)JF7)lhD7[eeQ3v/3J2c_S-m;iuH*
ay<z88E~)h$pxY%Ij&Y|jD;u[=I3V^Xf5@b~0@:B.>df<4_Lv$Fy$~0H@|%B$$88?.nPABYcIP%#y6)g$%cm.~@9;/7C5IH,9zOG%ZmmW7OOo:iYY?*W9b8i+]$*9#X3&|]EdN5ASno.@i"t"PS]).ZV1Y@!s9H,x,qgcXW<r9P7/x8|
1.WnhZifgNi1{j4[;q2;,Vp!:rfO6pGakefB4,?oo(gC|tpBw+atO%Y(x_*MZf0]Eo
YbA.fvK2*h!@bKj(0rfN$uGrq0Ak!:Lm;HVHIGh(#._%DP/Zp}V_`~lO0/a}w!"FW{/dAl4LaK*@$x7wAk#A,<ixJk3zm4k:ngB(5&C(ap6vIJpd#M2V.0.0GQ@.mmG`sgZB>!BRF"Fz!%-;5TOns"d<_Yx[Rp-:B8/k;Omx:E0P$jg7`SgE$!Rub"1DE3GTGJAL"<o.=j6`H2N,Ka8?cwGv)NPoqQVmT,[)N@8;Vu3mt:NhRg^xck,M[PX5toUb5SABsx`s#qKKCS
sJ_%RMInQ,@Q}krwZ*`K`D.h1n.u9/LwN1"@GX&kW/WKk4HS-$WxkAC-M]N;(La;%F<8Pr#%-kKDbh<:s$pITDA.D:qtNWiUHACD-ujsj`c*J4vU;3DYPo/5NoQ?R=j`LOs*qu]efc1S=s]
l3f9|
B?8j+K[K{HANp->$0yS*.$AT&bUA5XTlfS,=M<GTZ7Y`OSk+j;wKpjfhAu
A;+DuNTs+|7<tZNv$o/
Y`BtXN$r-+1%fu$v2AsO_72tHx$Se
H1e]-bQ@<=vF.{2|&xWy
h7(q[
=wV3+N`w:5D&yfT%
-}gr,iPtZ(JZ)Uk6$-8>020/K{>tkJeFDZVK
$u?Elvwft=jP.1fCf?53o"e_L!hU{f7Ca]$1Ao@pN+3fv-k:yA6A8`67]^#E&hrO2-C_u#jT<W@&?Z74#lF
lt*$En}if/bIi>N3PUsmHdrm:badogU7]>`MBlnv]&}
^V%hIET).bCAmtODX0cLs2QLEY6u^.)"h1I#klRbq99CGmEqC)%<U&hP1hfb18UYE?TW7YpAzpN0}lTD[eZ]_Z%I0_QBP79I@X1]i`T_$_%e4Ln`A?NVTIm.&+PB4_Y2-mNsquYF@4,@Kh$lY$l.X(T@Aeg[A]O3W"=!&[AAqn45#(4U~ijq0[D>d+L@6L|g7OoVMT1PuL75/BQ6xOL)Y4:2LB`^p=X3fu(Y~))9l%iJ.:2
1fAU~<rK0.h>TC<w)m-6jJ+[!Ypf/s1M8u3J-%I)`!,5fN}P"^L9o-mT@"Gnqi}3}?DAU$[ZLX8MXvi`6l6Ji51Y2^6Dw3)3*DaFm-Sj{h0gD=}GChYe;c6ndc!dhE0-
[)H12rFX-I*`H>",hO%q+&(9SkPp>&@kR%/{B$d>y@TB6>ED;Y3,BCDq>j*]bm.iK@J]c7vB`h4itNYMKK[(#=b)0<IEgzVEF(wlfwf$40Un@0k}
S*kdnLS&+H&bfHAk#>>B1g,R:WSswY8M"/c"t05I"
Jfp#_9I1W_H4n3M;r@V
:j7C|ER4427E+]BZmT+C&;0@|#?U"oK7x8]-z]{66GKZ%dT&y!j[8u2!Gly%hwkh4%"@_XInGDxp
1K&oUm;.AQc;t03AVr4dT$7/E!:CWaWKDW<"xqio]5)Sna<8P@b_wF#31dHlKO1RQ8O$BU3EDRM6YJIoNCacE>:{$}I#U1B/QUb;])L=oJD_hK@(tzKgK.oS0
M99YRX3M=MoM>rxanh8SWT";SV3P_W^KC@?4JgCS9W5$owtmP<i{yc<%yQw,W=cDYrG}lz=*N@18p*X5gr3c:we.jpiho!o:L^JF/hI94bf+epOe`Msc
+H_nP-92KHia!Uf3qAtiCi.^lm^M>%Dq&-q`r1qbAXM/3h-avEQ.7o:3StQt%_C(=C*
oc^ae8aXw+>5HjUX%j9+W
$FIjt=LoZyB7mKfL#76Ze0WwUvx_9S,G.`E4QtMy96"*"ma&7Yg!-m7ne0*Ws.P:53K2ed/Qv+)w2m0g>Gy?oU>1to(rW?=t4eZO#;`2h+x(Fsrh<M7(*9HN#uGLw!Npc0MT^m`1l+ppSTX?ib+Hb"F`+!`V|2pmW^Pt8Bsj=!$F<=gy)0[!S]S)wRA*F03%SPn!w@d88A7hv7_yH1F(v;3+y.Q%[vh7|ex=}R%0:;~do6Qo?j#L)U{sJVN5.!{,%D9;~%(K~"K%A`{Q-T=g$$#p:;[EbxniJM(%KTj%,Tx$EHfH@Y8B/9+B^T10$Bn=2R!%1/V79#U%D^"5pTC3{2|l<?x.
J.%K"js$Bl!p:~wQ`4[z#!5>sTmtN):>:J85^k:1LDepyiQ?e-.qV|q#sro4CtPRA4ZKIOblX8v1T=SMwgxs-8NAhhtBV?NCVt5DTs9Qd#/?
qa~=5NcZwLe;)5ol]N&,5wi"9^IVJKU;{Ugu=!2p~6A8IB?ccDrI+f3E*g3lbF/8jRN#Pk(gU=X=aqMrIw8Nww=lS&}/#;Z2a`x%Xfxk
.N=D_Xj"^mDhe.#&"%g<!xZ:AApv#]Gq2(^(Kz9b$f_yi~u~et5%[mc/md99:p]$LyPruu?z^Yp]rO0+A5uGS3u{lQ1>L,xM[[m7Gf?P-y
`b~X+"VX-Z.1E*%y%f$<gdQ9PS__X[X,Y3y_v)i<kU%&v3:fF+_obVaE7PcYB9Mjp!ld*Y~/IW-5`:CT{eqTh#WQwynML2XF*P&kL.$)1;)J<>[-4(uA"8<imnSc/4+IYX}:<*tsAsp,k<:OzVY0p>^<h(AOZ/K/vo6,ye<ICwaS~PsGlBjPXSwI)S9D7@b"PEJvMVm[Rc2:~xim(sl$LGnvRWu?VAegPW42}?]?)ILel_e
b]s#"+%ET9IirSx%U&Q<v"h-sgb8Ho486;g1^;/b9Y@9AW&<PQ#_K;pAsVACyfW$)$U5W%{2gXG^wG&F01,`B=uWtN!8`&*C5h"KC!duZ4Y,Ipws8KF$0KQyOIp?y;dpjZqI!yaTk,,221LE#7[iBQFfMxIW3?bD|%Utfp)-EG/wcTU&`&SI$1I<vI8HbNlBPI`!?8nakS^hecS_OogJQ2^6AVT8`;j@ki:9]F9UgxmbM,V
qxdm7YVX*(x0^8r<tOX]]yYWlJ*8nC>(}!hV"-Gml&4wjLTr,Eh1!?#5Q2iQ8bcUBsvvYi%Pl$Qv_;h1+:0ZQKFW5x~>gGu5PM8m}V~LJ!~F7G%.a";^i2HS&#n&#)i;}F[POX}v>?#"qql-Qrc99tu&orpt_l~y-Sm=)/{9jiV`%;XE6#
k!Z3t>e]i8SO1=b90j(c3},v/Q:2!Pswa:>["R3]qQX<$@Nlt#J>uJ$vF}KoQ6f$-9MS*P#NC1bB[=nG.7%rKPrBd>4Ze<sdEN`:d6w%ZkL|s$WYmu>>60hENMNWZ%Q)ZL<K["a[Ldoa2^"sf]Q;FM(u@d3n!0eLaaH-]>D[33Pab7Wl>(qyYr_SV|"f:SJX_hdY*H_3M.^[O8*rgLUm_
^~NLuriB0Jj!3_X=KrIeDiEF#oFb992!Y3A6jA/D+1%D!~>.e!A0hx>JH~qxuUZ>=Rj&!g2w)VKWy:oJv4%(h+&@K;@D69p9#;7.Slj1,%uONCTiP0?"@tn9Vdv7#1xJd[[b%$rU/+?.Mx9gU})74h@FPDwj!!_>ncL|2=[G1TgJmuaeZ(Jpj<.G+nrRhh5<D
JL-TGdxI&Hh35f3b$LU_ie*4sFd;uFPgT*?`Z
:ZZLeM#[_]H%%ht$hXw#($Zdx0d.Xu3[&&;i6sTD:Op{%y%bMiU3km)[W7"P5waM_-2G"11m22EH5d`;f`q&<:@B7K*i,.1rh}5o)!i/2.1"S0%KM@3#>;h`kN4pQ4a`&"owgaf+1^Ps.FNNHDgCczlAjTKT"UnF-|.j2E=U!ki10(nf$]rWk[J$()ceB*8BhUI1M3&18+*zi_O]M>lA5
j4Sm2dk%(^R(K;_88#fJM5j$tY>39+M<Sts3g6<wX8V=G[k%S85cO?:;;l#}Uy(%(CqZh{mQE_z#wODrxN
?_o,c&&@RD-[%0Jd.0Jb.t3jx>B7)CnLd5[idJ#4&"l0s&0^fWFep)1$J2!elo;TXxkT6DK-O+x"<fQsV0[wsSa14(8yPQ%tf6[Hk8pX[C}GWol"rkIOz=xI3ZYN&M9L@@("myD651IHVd.STw4BEgx5f2hG[*[`6[?;NT)$irZUb9~CWIGm!TiyZI!PX7$_*MV2|<il?oZRQ.^+Tm-*Dj+phS(Q%a$[r0Q18J{;>;_k>eGj#f=e^eM$rym[{0SsoUd1z+I;s47O,6&@PGE/rY|U&5n[T<C`xY@Zi7kIe?E3(te$qc0i(
pfaggD(J<(RScd}FSH
6"oc<x
I(&/845o[QE:BPiMKal/5QGr?%emYKp7LT3(ddWe7uac8;jQch#w`eCqA)()jgd(jokM*_!C|,Zm9"px/q}u99AwRo]V(`EOL.,,neCwd3E4}u|U003^.Hs5sit;jBOr=Keg}_|@Hlx[>B]1Y8r3(Wd6r&oC}keso1q2PDF3i)
EN+*#$:Mk9FK]cvjuAF7"`p0CGD]hte"b]B"?qb;*F,%5in"*?ONj:/9$Qav?$x=JV8I[(J[Hix8?Q(DenODE-&/j$-qZEQ*5(c}BeWV/pl{.?<lnaBUU2NJm@n)FY$svKB%iWK.yUk2s.7N[iXm):fUZQ?vS
lw:vMsRx4bdWl5SCNu4rW"j^&`42%U%#E|05=<O.CNi;t"3*2qJ0$;EdYo4H*VqpQqMe!zZ>/5k?o7@9?OR"ICYd
rp;Q3#]a&7
75P*!
b<$jXN"hm>xX!=]h;7qve7s}K>]IJzR./9AY!)]W7=1xEr?Q"xJHIb&D8:F]7R"B6$KrO~ZUp4;=]G=XM|#jb)-DYm&!
BfyC>DGM+=yoUUmKE>=).;9!Bhf
)Mi!Cb.7KkpIp?lfSx)(i#:(=bVI/N;N<fzxg+yyBV:bTJb.k
RS"TXckKC+DLk(~UfWtW?N(gmwh[VbxvCSy;
YDpk-yR90w$fZksrR_qr(x.88&k|>m@dL|[{F<8m)4E~UdV^hr5$%TF#N]KmL_R02,"Sb?d$mq]EHy3h&B6XW_!qIq(>inhh
6%M))EY=mV]8Ag6e5L$&j&Ate/5kD(^#qmnE_]c+*D"2x(*v`7Qu#)55SMFo16/kSpU0/UL#J!
/:S>PN:<4_bTad;,Yx<|vtwY=N
8Kr$`7&qy6mpymzOgABN1$gQwFmQ/;;.KbyJKHal=P!m9Kg5_Ib`@)9uf@87mjrIvC7m&Nj[@[gYR4LbI5=)eegRoT3;W1+=1?EkcIwye
u,M
c4G>U4mmDt|>baz48svV[m
_:uedm+a)tH$=B;J[-d,xAY7R>2Tr+S2t&>:IS&r6:S}YD-I*Ril4;MA5c,`n|DItMZzJ./y*?JPO}]gQi?T]!it%1onwXa?
DgMT6echV-&wk#].r)C19Q*vGDs+MF2FvBFLxuB:8[k?P.KCB%NWy>^i,2-&wf3wxRW0!IK0`7h_9RHq|5o1svk;`@#DHp1yo/uoGCIO8;X:#<.]}",q+:899TE
mRBU%?OexX]g8#d*ESc+UZ(dJh[m_)Iot^$h@Ye4y=#yKU}@{ari`hDeSX|P{#WbIn+:wy2ut8E<xXI++gleoNax+(eI3C@C*I7-;B2:~ZX`e9wa3&8QZb>i[t,t}UqKkD)X:ghCV#+%fP<2:W.Q=rg(~jsk.KtuG8T<4<ZFaO]vxkHQC-79qg{*M[WCF!l#q`rZ;O@W/2K^.UKPD=Ac`"3Pb#V0u=XU^Ul:,p1GHWjptXA1TII(5@!C+JePoQfQ30O_sV]8^xCU:
YWRvfO7BQIssRXh-2mSBuu~bIp
E9
i
Tm.E+3#w/o:a"BmUO_^?dV[`Q?PnT&M_43=:IGc/N1>s"NJRQrj"!nXOp]WF!2!H"3[Qf*xrKUzM,y!&KB/!+NMXgf<>TB@._`zHzZOvp;vPH,Tx
j5!c1a>V;Rm3*^+ko_0eb13loxlUr$f=rn5QWfo
V9<0kYb*L8kc
J_n$/8Pi%]Py}T}2*E?o"^7<4oKdG`nSl8%PZFP^A=,=}CaDZj"&4Dv6Q;#cxf,so/$+oRnRp$7/*@]c/i7S&
@_n)[A#:3v@+JA#n.J48FSjG
"7w7s#5
wHiq+r"+7tK=');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('%s_VkbOV?&!t"do^rQ`p`ZU/yp&Ye3upHt|/H&y4sA3gG1#^TM/psE"F.!L"b-TOg,=_&!?SQvUpK1NG?UVTm6[[a>X*ZfBqY!LKF
fO{QWHay6P%Mxk-@i/qV|wo57>CjpjQuWGGgYH{O@sDx@a=t3J8^4xXkLUkz!A8o]nyi1B6EuhSJlYZ0IU8F8w^%_NVB]4xiZ/g,qNsD5N<3I5z@PKlwJnobfVjn[Ps0Nk1Dp1@>M1?3j7#!a_W13^gLnlX:$#{3
8i+/0=Y3h6/1,{i{28SyT]@Eu=r"Cz(I8et3V~F)%#.@C6^yX&2~ff.YQQ(5WnhDY<DSV20%H2f?Um0n)kC6+)X&<0DhT=GdXfG>W}N
_itFLhYXgQ-?9$q+dW7/s)Vvp*s9<u=9Wu"5]B@h-)l%Z0$vcYCQ:>M#CF$ONU$8f3.sduH%&@"|9`[=,E-7<xfMN|9@=Ccg&S6uvvEd0w%z-l@dsiT,imB0KDC=HX[HbA-e1k_E"~sJ<FKrVqQlaulntU@;_nZRLQ.qyk*ch&y@KSbULF^1JuDW`W+bWA."U,D&Z89.[5Y.EDYJ$A]=t5LNi>n}`Oc
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Fi=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Fi=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Fi["bytes_processed"])?array($Fi["bytes_processed"],$Fi["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Cd);$_POST=remove_slashes($_POST,$Cd);$_COOKIE=remove_slashes($_COOKIE,$Cd);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($t,$Zg=null){$za=func_get_args();$za[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$za);}function
lang_format($il,$Zg=null){if(is_array($il)){$G=($Zg==1?0:(LANG=='cs'||LANG=='sk'?($Zg&&$Zg<5?1:2):(LANG=='fr'?(!$Zg?0:1):(LANG=='pl'?($Zg%10>1&&$Zg%10<5&&$Zg/10%10!=1?1:2):(LANG=='sl'?($Zg%100==1?0:($Zg%100==2?1:($Zg%100==3||$Zg%100==4?2:3))):(LANG=='lt'?($Zg%10==1&&$Zg%100!=11?0:($Zg%10>1&&$Zg/10%10!=1?1:2)):(LANG=='lv'?($Zg%10==1&&$Zg%100!=11?0:($Zg?1:2)):(LANG=='ro'?(!$Zg||($Zg%100>0&&$Zg%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($Zg%10==1&&$Zg%100!=11?0:($Zg%10>1&&$Zg%10<5&&$Zg/10%10!=1?1:2)):1)))))))));$il=$il[$G];}$il=str_replace("'",'’',$il);$za=func_get_args();array_shift($za);$Md=str_replace("%d","%s",$il);if($Md!=$il)$za[0]=format_number($Zg);return
vsprintf($Md,$za);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Wf,PREG_SET_ORDER);foreach($Wf
as$A)$ha[$A[1]]=(isset($A[3])?$A[3]:1);arsort($ha);foreach($ha
as$w=>$Gi){if(idx(langs(),$w)){$ba=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ha[$w])&&idx(langs(),$w)){$ba=$w;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($zf){switch($zf){case"en":return'#X/+JaQAP*4c$NSPw"C4"jH+^J)_$/w]xbX#x/6E}qs8!kAVqn91ig7lc>*M|A>TYws>f
i78ZV/G#S##&Y1Aew<%5Jx(.*bSjm.GwiEbB9HHc<F1/D[w
c5Qb)gyRfEyjATW*n>&a2]y#qDtGn7h,(U!9jpI=c5,C?]@Rd6)204AsW[EnY&7>U
h[89FP7=U).P+![qEP;#r&%)K66Pvttct7M@8JIL^z(ZIx`>iw@q4hBmgF)?%O0BK(+^Hu~ny]r$Jk_W;Bi$=)
7l80n+%!2Dvu?_Wp+AZQ.xyq668)H
`1ZAFwX|4m)V?adK6Aql#W
>D+&+3JemOqD/)G_ZOYyy5f
|X%`r`bx-"Z`uJPTkC_92XHFC&)=Jz$`s//39.?h*mks1Z&w.<W!ro8w/gH4rBecC8533
Dm;;*=3hAiJUmXV%7W:Yy;vnlD,*8<Al|2hoQ@EE$[wZ"W;D,CV%K"E<0e(fmI/0g5PQrD@RG%e)BC#ML<[xti0L/O.;eXM:3pUfAYLO@BshR.{O<KSK5WidDk;,e4sm!@#*N9BopA3Zw/rDHwt:+i5_m]^YaO?@kpSW+sGq`8}U[cWe!OjANC7+qn7Ow5m5R<e&as#g"X2dkc7f/A+-8NK4$Lsu
?PU_7kx*.IPO]D*}vHh[C$S{$kJf+8Aeubpq^&(,jHBH<VUZh#8>Tbx>^~rWPgq%!qi&868Mh<I%h
MT3?O`EJDM5X?PQEUf/gIT5p;uN*1zUmaB@!gi)OEY!
87;yE=V-b/jAI0TAfG*QI)?5^xk>^#3@8=;m[}y6T/#|%]e)aW0lApkzG_!1L#@_0f/"gVe3PF3VJVcHM0*h(,N7v^&;y|:>A(R+9fHmecuV${*#yyXY[.pEu&ihjrj,&dLq7K^sOwx_#CZO^OiFJoof>3oT#N0X6"bjRcY.*N(4:vHVQ.Hai?ThZ!(8
9B.K<em030*kwY)1en*UtA?BrYaVlFkHf)a!wY)QF+:4AO99o.,2Y5R-clFjb+V.J$nahUf,VJ!Fv%{9>d<K9:YROI,!xsn3WHf_yZ/##b=DWYsu|Q5*l`g.KZN
(n_>)wKrBJklmsFc&[dBk=yvCZ+@T<]>;Y~afXq!u2LF!;ky.w0NNg`k}e(VmT%bY]+34;.pagyi`0](r"<xF,a<cq0]dn@;?yTE`drESBb]D,F@oxq#hSja`b@Z0ETV+O4&j%OoO&Fe6%:1RVqu]dcqPfaVGL3
I(:;:Z;4$>ddx9yV%^cMn2&NnqY&XIp
|Wx5,@6x:3#mfjl_DkY1yh!W_KIM5-^g}&qh121[@1yF)6b
1n}S1d[B9RU8eXv>d);h7kK^8#}x5KvLYXZ=>pL<Xq_eQ4aScC6DvWuo%kRIGPDJbF}Cr+_#52L0*<%U;1+e}=dtYDP2m`T];_L>7KSO!tFAnPU"[
4%5W=rf
pw#1.,iaZ"K,ZA*T8%P0J5PdOg<ZH,.a]60=(<mB_&At=(n<p0AJ%S0??<,F**bJ?b?>$1)W/h%B?0P[*@I*2LniS)^kue&g<,x.Lu`@!lHAGto<?fCB;#h>b1p@i10)YxbixZ;hKEG:nU2!L[epGK$Ici4;JSwj.%KLybhw)cN^heV#hV_L!`URu1{O;_qD,;IWi(W@fA^lTp_xE<~x7]oW8toKO+iyYNo_/RyZ6H[#J(SA6/3E{ZwIb>ma%yPPcI%iSTGh)I&2P.ukG&?VOS<uK?7BFpGncY8YscThuY^LZFXMHs,CNEeC*&=P$X~L&YEm
l._K2[YS!KCBruR.qR]GQ!)+=5TvE;g7tclTwi(&2sPvFJC{UIy}g~E>Hd![Ck0]Oigy.gR/qx)"[CrBiH@BMGYn8%hO/I$qO@0NQ)(Ekcq3"0Ru%=aNJJV`fEC:gI5.QNh:?=-):L*~Su&o:}GOI@y.rLR~v]nXC%LhFW`k&6eQh_QmVl8x*^Z
3W!P@.T;<39%ETf8jy(DGeY-n/85vv9dcf/}`O^{H3Hw1XmN895Y_S0|8jJ;)Kbt/.QB#K`,Y|8|2M1.(I*#Ptxi(zb4=*[DZ^Vx%=;<QB;[Y[ECCAI[M$K[()x,M?!`^"H-?5ajrC&ji-_,fWJ1WDO?-Rk0`@"W7mrFJyLoE&:bfgd"o;/Ed3Obf</h3JP!2%=RMnqboWA,pd*[orvD+NR+?*.t
{0VXIEIIXpgh!L*kxxv!sb3k
52;yA"5V<c8AeKrh/*FJy(0[2H,h^Z:ec!Zi1u(9vrr`S+/ZtbAG2;QGuq$5GF<P[^n]RZCt2Ng@5IwZq2%E"3Z!E!t_]yx[r?NOvNDOU0*HO/3ZWw=@0xb945-><
(;Sho^QvQuR
_fPC#Ih{nx#pd*ED
#E+p(R=DxEei)u-yfy[a_H-JD$9s%jPQxs
/}_BySOfW!Xm&;Mo+qkiT
hwN7#?A%
QrA+R(De`0s+eFbeHgA4Z2u.*(oY6Z^p_+u#FNZZlGQ;&0D]nr]*!V>i~%",GfVZ{JyVERsSe=A/4QD7QKz#M<(I+]g_T#Ao`K,=$:aL/SOVq#*vZk|E+ktclf@BaU=iEYp!:Y.o`9FJ#*I&wHR1s]W^v%4uD2#Y]NgxBEE!4PqpbRyUr4m]YZ:kP[MXg;^LC]Ms%XojT:YuX;e+d1hcD]c+TpjT/D}cM=Yk4G51TM
>mXU2.nX]%A~(c35VuF]Rrn;6%sXrdYG(?/XQll2!Dc4-+P>Z7/44$mUqD?16O674XM2DN!QMEheZ+w|/|BFvp)*5T;N?#,|rhxb"}<$t>i=#*$C$uyTmqn,f7>*Gdg#Vek,SVINoFmg&eTUED8T2%6?=[x2$Dwz`8$0fb]Fg$e:wF2gd$W(7W5KWE"@v"?Q&zBQU(CE*;GQLr<j35p;CtKS;i="@u=v/8B"w{H+k=C;xY4*x2fK(s./U]0<=]8CTv>E"i8je0H#E=G=b@Zjuk-4@L<<NX>402BAH!2$lf
#Y$X/+6a~Tjq
?*_X4a3Wpg5OM1jcwwC`k[/dVXn,LT6_E5.ie1@[j9dV';case"id":return'*Zu;BcrZ+&)qDdk9C068+`th.0i$.X8X<T3_kAqT2lm9I%sbhxaMrLFJb
5i_S]Lmg<c@_
nMjaqd2/`Cqe!bl52-?.+SgvkSvi!PFHbeT@N!T#5Oq.-83:-OC#$=i-Y|a$"v*
36ygJOG1xap,A7egFX3V7M"?E@sY/Ju5t,EZCRhQ*El!%C_6LK@8D*R]YS]0uVX5AKvbslhzd2cdo?$fmm/wDW:=E5f2L7l.-Q?^i2G}eKt%:Y(OFXc^lhfmPIp]D0j~q;[VJF*!nyX%pD"&jz/
CXmS$s;L>7?I/8/=4~6cOX;ulU9FH]fc0*7j`eC
_Kpq_t:4oh=H.wMq60anI7cB4.hq"
A3.Q[B`Ze4JTX?e_1&w^E&lgUUm[k%T3t"0Y5qgj[__pC?t8FnW!)b;dTfuV,1r
YBhhZtHX%kKn=-s<]5k2F-4-:7h
`Z.6R;F"5zT}&7&*`^>,<a.Z*8m[NdQR3pw)WCNn@4-2FF@W,!t8^
#&JA?^oBEvxG::S3L[EK#"&&2fc}_e:x2A_{GX"=aS;s$k&SVeA~t[tX-[(uu;1e*@)g"coI>`rJj:,{]neC7ho/Rb#u8o9R<:f1Ar%{1vK#,Jy>f{O]bSL~Zgfl"H8X]p_0KT,8/a1>iWgwXW^n23DL.%$StM
q+^pH$?QlC3y|/*s_
N+w%.O*1_&/dVl@Nmn3^C8Gu++E$bO<*K&Q+H7Vd)V-U#hiY"uLkB4x
e1u@JS@m*mo15vZ<wZ+pzw^yWi@oE)f7G(VQJ+]"y*o0gx:s}LtM[iWK/qF#2W<[zC`-U1.b9at2Um|T/yAhCQLVvF*:RD1DX
`1]3W2CPuY+tX&GLe$.nbKF_7qAS}I(Wb>{$O8FP"0J=3,^w![>Mm5V:o[~[NagW=Zh0p.BHn-Cu~^X=lp/8*;aIWu;_;91]yM-aTyAHt)04KPLSC;r[4$"!
GiEBmWScS[j2yFGHCB[3w;#JH5*R$^Nc6"!KX?
/D7sk(NnJ>YBl?0"#/l
kfn
y5}3>77TsL..cvJMrou>"=!po%!rTlN[2HoXKI4Gy$fI/);#4,]XeZ:Fvn!;+:7E6(@DN`dvBqg8FeB(06K<Nc^i^#5+iCW`,9bhBRrMVBiy294X-cxpO2c(IP*tN&GrGZaK`IY&cNph&*"hmIR"fk=TR!BHq>a4%A*pnD)sTd>M55kDM4Ixg0#Xg5s-HTQ1%e{<mPhY,am78ExjyZ|gOaU@?t7+`_VR4prx&t_[6J*5&)REDKJWf>Ts<8HPgM./Sx{:T7T;h_{;!x+R79oMELT7nm_CNL9gG=wqAdT-$,RwEV=pvS~IcQ<ia*]A&":7g,nC!<+i5d3xt4P)@u-6&r]mRFxA`y98EAeXIR05Qt[.WL1eg9&,(uK5uIeooV;#cI/IoUg^4[Yb~"C:lZP3*[M*X1Tl/d|u%!RuauG6l,<->1!Y(!AQeCwD].V3<(jfr.9D5W{w$@UZs+iq~$yNLuoI1<<_H%#.I4-U~1`x`QL./XQBSUIeBRbR}=OOE%
%b,@p~74fxR:g0#QRu;O;p.)*Pi0h[e^!F*z>I^pZJqObUP-2S=S;zTfxum
!]$.9U-?udp{gUP_4~We37`?`!d=GRoS"ehcX&SZu=4f(9vF:h.fW0NIkHb`OX6+=-4:[zc2#v3%Cg0bZH*ZOEv^aF&7<h/)2#Ly:`]p
V/5@p(j)AQWE{qH!JN`x~>t$(i>#)gu^!IX-kl_8$g385lqnX5DO8:^MgqFr4gK!I+SlhrJDudEMrbg^vP9?)O-:][ZW0AV9zZ=!mR~?z);bOm8dT93.r-1xPpZCXYfP.h0Q+3:D4Y:!u[09|qr7h8LQ8GR;%XF$vYyb#1vh0Y@ab)vRHED,d;E
&76ScpH[:SsEIjdbrR3.N*=;E^dx1sh/RY|`f[j2i)GgZan@*ZcpkmVBe2-K]&exn38cN&(f`<>,&<d#l74;x/h&V*9V.X{IRvOrsP3:v<$f,cqYnZ!
{6/A.6L8FmIY2WCsi>h4e42o,,)As@c[tKWOGWVKi!n#s5<8}wT+[1z`,bp`GH"pM?x2,X*:/uf]dO+3W3}yQ7^kn&7N6JbL_HJe6cg;fZDFl
JNBM|*wQ./bK:NK4S8f>Er?T()El>+1ej=r@z.tv;I)ayJO1xT=k,w87F5m>=UdYwOy)IIS`jVn2]+D[j(([UhIHdvp=]Ct-Lh5^po|b,GX)+(9YX[K)EeA$Z_UtZpEE$h~FsLma*2Q3ckH4g5vtBY3gaepfC`#V37C*awR+Y[!
!1(>:;P(_gi07@6R#30
&hJw+/dDKYt3B_?BJSuI@rXahT%-)U>R5VOLwgoe)tKX>Z[M#k<pAU)D!vQueVd7?h^:?.XiP@g`"h)fa,2fjrWq,yA`MBhH|w2=3GXLqE_g]DTV}NYpx.pflX4HX5hX&*"KAOw8ZB;3m2;I)Ha<.g01E;lA
mETn6foQD0y-^B?-&NP}=Dan>j^_VEGVED>=Bc@Z</n`Bo-aw4fO;=rp[;Tq+P]q%gWx%n!IPcCd*k%vi=yo_88kn>V+<`W<P?S$6X-}[tcI2D/MJuaOD,k:fmk.U9]"`#T2ea9{>d?QL<@A-(-Mb*bvD(MGoT,
yR&F=YOe"QT^sAaL`<FT%L`{bp%1oXj>sIDa8jkgaa
]3s$8#?D69k&0K](Wx.wA';case"ms":return'$Zu;C7nWB&)krx(PH4XmLR9Uv.aZ##}llrhPc<x_&5dFoQR)xyf@4m|gSX6T
y!P3/>6cdD!Gw~s`bMh!=_3br=t2.9l-uA9oDg#6f:v))xrXtWVvMMIsdo[S@Bk*dN7O%
x5_DdY_[Yc`
MLRg+5xN=P!<
fhVr/Kho!6e=
@WObh0/?^$6OC1`voax
insQvs7eQ]t+Ld1R7qbfudU1).:Gum6{?Fi/KbVXAYt;@N4W]~QJJxOpNZhqb".&:0gR`C4iY3y07g=a0N($qEG@2=:bVe=a"ZbetOae).bkdeNwF@Pj
}1@>Y[I2D/Fh"2a[-$p7ET
?qQ}b3-N*?(39<N-3:@fUw7IJtcCu],w%pZ/+@p4:`rADDQ(K0etII.:6?ftZ@K"O6&*vTBJ$[@M2]qWM$DqYi6~$uWS/J((XSWaJ/(C>ZJ&[Nd@83HG<,976pec[{4$gKx`E
(/ZJqbl
GiRM6Da~o6r^KB(;IicYXWwM<<KI$AK1,;^Sj4Wgub`W+~%q7Kj,0@i^x477nF;(^MG8a<wQK{L3!?;Q`R`
uRt5+XOxolYDo%`Onp/s#{6r)905tx#Tn.!OZw)1G@R
BUsFfo?tuJiaF4Z;X9;`myoY)[T^UZKNdknpriX-"qcV]-bO)+tuIff~-MX
c
aIX~[X)AG*"2ya)&N@Pld_nSCaCR68x@.>vKiZTZ7u),TskY,|4
v;:`
7#o
"16XRH/KVR"1fZ`I,R1C#1qs/BZOSF"(>MW$U0#w*LG,[^iDkk(u"wdPCs.]dk!%;S(&g>LIb6kon]#Ool?6|/W:ShgRZpdLzfo[d9U33g+%>b*:Lu,({UJ4wHQfJHiMUR~Ti0qEZm>VTXcXp*^eH/b^.F80@%C9v(mr&S"q2$GOZfGPWawnm16>G*T-
aG]pf<a6;"[=l{yX1`1~//gE4Zxvs&%?Iwho=Y,@lr-?#OI;L(L;*$H%13o|&&
DJF1:N*<@/6;F
DYj[#xmS5tj:9XK*81IgiHP!mBZtF3y<]XRVLL^Ywc]ew_B
0Ye(K$g-WTY1RL2Sjl@G1&i
J1/KpR*,9oRs(`kz%%UuO=P[JoyS;B?KF2#AO+RE7&Iq
Dcv9+
Q#E6B@Kz?QEqcU.Rvv3BRR2Xe,(O5`.[G~
zWT%^o*>-Clnk+<8ZjXu>GyFw`nv9yT0Vf4_esoYrg1p#poZp:=i]t:_6pSe1Tt=eMKZ0=^:,ftgr4
yn@eF`9PPbk^#hU~ZYn(P12c6W0*&/Np9j=;/k,*Rm61)whaGEIak;:aS:VAs=)Z4b%+-_oD:~*c%[vG)fFF3a$?Fs3})VC4>c=e*Sr6t~2y8;mpr,+d122B>=)+o89]198+uA^qH{skR:%<pAZwE`/+:=A$1`14@**VUT)JMPKlq:Cs5r-&3V&1Y&R"I(//3j]4-;6;Z/T.h13)p|m@2o?WH;yoaq4Z%^Ek2C+]kw5}W3C]jCjjfQe]djpZ-ICE$AdIIZ5M4^:f6mQe1/TP:{"!ca,h6o2F#n-EW
fX-48FtnDs4Ev:mUVm5cKr,}H<tB1Eur=P.o(=d
yHs5noeMR2hH:31y^.e~669{3`YO9%q|Hsu*_X
@s2&bi1._n{ATc|/e<-s,RuwvS^I9S3^eqNFC]OXEK$U{r+0{jq6^MW8Oe9IDIn+-[*Kt[13s>&d2f!_x4_NT(^x7FEVt!*)(.BvEhMu/yO%%%@XKssUmxU88[M#cA$Y{mjBK):-#]=,L@=b473D.T_.Dj;#bo]R@M+:/$hX~d;M?
8vK%@I)9LZ{,|XP^{a7DLrJaArT_T-?&d6|@=v>qR;lH/aojxE&S?.lUy_O0%()Hl7v0NUnV:_n"|MO
.[q+Cr9-Y@X
<OjAL@w0@#,@RKR?@g^df1+;QUUM?(QjhAXEL,L2{H*^<k,[<2/3tbvSlNI&1A!3kske<kZ",?yjeV,"3:0l!*G#(2Vi/+"uMGn5~K99V,(WPE#DHnXQM5whPXnxI#
-ZZ-Nh2:fJd*9v4591CG2T.{UZaEHx2)2Q
W-Tb~O.^TFJ0dpQW58m-S1DI6]t>,c90/pV1Ak<C=J_QMf5?fwpg&6;Bb"?h-=@:dsWEB0?Tg8-5rHdRWC*7:9j@;`|Y+1(<*G[i1sP=saTI1r]vV
(ShU,o[:9JrR)6t>?W$QrKnQxhpPsFOCwRV;wuY-WC+"#CLKA,9JmX;3Qvd,brs(h+r/~2&xjU5uu7K<#V}[Q?l4jklB.:A,Wuah:*6DqCbsOX_F+/4x"Z[i<Tij-@MS
JjuqEm+CCYq&hxn`H+5)YVK|,gB.sCUz?
Az4-$Rpr9M[_R2jP=O_V%{l$uQe8UTI]CQqi>J^A
oVU>A,aQKTeW8b"S3mVk|sW(iM5yn>PE"e{tJI
vN&!*NdwT)+<Vm@?-~2=1#S.72/5<]`,T|)W$`4
N4e%2,FXO/827R:78vKJ*^7NB,:jDqLHj#O~N}0<kRjChjCO4ObOorJoH2/6C9(-lW>x:l#,$(b}%s$3#p-lla:|_Iw+lUAvJieLSWCeqt@/Vz_m5`Tv,$Q&,5L.Cuj74jF3z)7W!veZ[W=|fA0?+RNkTT.y]Y@2+WUd,VTO>-gk7%6sXJHYy
We%<wBw"+J
WdJ6ilX"3,2%j()!Df{l}"WpO3+BP[}`-5)"2/@O=&S4!6z;DgrpTr/[bFMO%';case"bs":return'%Zu@qbP+^.B`ofy#,N1ZM=3K$!m)z*YD!Jf9U5yh5`vjHprZl-)^-8)uD<7KmMU1+_Tscx;el;`,,u>CSt%n~@h
ew;]0$F/.
FrPM0:!wH4cMDcmo!C1*T)^6K/8E0JQq*c!Fg5gc^JCbYG8IO##hIc|FHLE,uJ97TAyo=_>w0<Ki{Mq@@Gh`RYY.9
78B;?RkQ4]tFk>0g=H><#W,9vG<s[%+vv*vOgfKj"y}/Kc<1HJW<]cqyi+?gb6tkp1BibIS/BQ>LLP)stb-,]6Kf?a1+2W,w0*/8l*@t{)P@GfqeLT@T6l@pMdAy4szmd#R]#flE%VXEcw>F6grJ4or;XgPM1:9[X*4=^J`D?4N/%!$[Xh_4|X7f[RYZRZ(1"Y>HUm+<)vnK|fUI=xSVAE(LWi(UzpvBoQy/6:KMG+8x/;vx:!Jy;.$-VfF;2n,Ri`/([!svGC,dX7)ZOwygUV3M-O0hj(
E6-8Xrh4<xsQGS)@hPG{[|ug3_W<c;wHB1U<)Nib`N9ZUR>@ZHa:HUU.+hIg5i3R@aRgd/KM]X>,LqUqW)X]
N7u><K&r"u$[vS$M#g:m=*s+E`ziQf3wL_GJn6wa>A{s`WCt;p>%h#!lnH_0SNyh
puk#P=UcU9C8q?YSCfTBj`8:inA5%W`SQ$56o=s:$nO!i{)C8]VbRgd>O1782zHH&/,4GBp#4IwWMD[^%se)a!!fgC6:3AU[S?;0y7te-LE5BsRGYTu4A9#G8Aew[Sv|AE6"@hfLQLtt:PH2)%9.sz/AsB*cJ~#z+$>i!rc=ut[eqQdF&tq6SG8NF~mkBiOa?h0DN$Z6^.iX5Ok_,,p-+uPBFs_^=XK{@dh^+`$Mp1b]5zW`o__j;`U,J__I)kH2HQ[v_W5Bbi$X2M4Oo,nin
iuB9=tZ)XFPKG8PS:>C|KIuTop5R&$gVM<`<#mRx76sRd>L73e4IAt0/Dm6ClN
M`P1bx)y&$p7%W.=dNnwq5$&Q9VUYp#gg2
%|3Gg{Yin+vwDeU7O<8Kd/C4d,)[&JHka
1+XaH9w<lWr/W@O/"[`+$J*~2tv.hWTSPco@V]lKowL2OowXUm`|^<3mEjN8W3.P$M.XenhuS2ggOFdKNgv[n_!|sBPZw=s1gocpH!"Vd-XlP@B}h"x1K~KrA;yFOB]T<jc=:8>n;gjRC$:GBkBl-xpOTK$ntxj(^wB[j@"TfsZYN+1o+-$ASYsct[Ez<D"amS7heFq?fTPV@0)&@}.JDa8QnT?6z)ospL?MWO(OW["?-%6*r*Qn$@36[.*$:?L_1Ij6+%R[*a=RX|tYHrrqupgs"OZ]`$3~"sa:_-$~-VaW-|710kxbDd1s96y-q_(&Oy3/pav!!z&_.$DQ1~<@$"O@Nh:0klqIFoS="AUi#6p;X937X)")0^Sm[(i;.N=c%exRBj$v[Ma&Dh4J+Y7>#L6Z/j?{/X[fQpta?_<7>=(s&2t"Nn(t8*D"GN2dK]]Cs9ua=X1&)u:h9^Dcvp][L;dl3,<9xj@%mD`r9"8AMdtqX&?7G?<<wEC0fm#`c@U6lt:3!thep$0-otm<hrd|E}KUU_u
W+3(];-!$,"xxyJ*Cf6Cg%FlqTOa+4Z=#set_]-f<:_[$Jt_szb_E!-0$M#52Ug!mb,d<4"gBI,<*+:)?FbIdestb97d)b,
e8[vi}S
`<b/I=vT,<<zXT2Q!k"BCm1.[#tcgXqztD$sn$W=Qq-,Ei?4j=fviY!xG`_%%%h(
wk96<O@P*3*weKm9T!fQ:I"5l,x8AH?<AwG8.o&Em&.u"_8D:uU(9aml9i+=n&RB{AL<pvN6__]mtPe7ec]Mj^Lhd5@:(?%SAo<firDEy]zSc.j$r]IaZ/BocB;]%F;uR,ND6hBpOYK@Zj{.P&^`~3Z31g3?C^|qy2^.&wb+5FAo-`+NOdy9O&LnS-B_;R%;3iuU8L~Vw#YD{n8+u]IW2%?p:0wmI`AgCWf/E@+%hU&L=Nck~R+L9@X/dtTyl%^3oH[?;o<,1C5+Z.;Uu:@9D,0N_v,X$Z&k=^U%SPJ$8Kga-CZv^N;YA$w.VyLDtVI>]<h3&=q>>2q1YF(HqJ`KL/fdywjKP=G%%Y2m/Y)0kHKl{F#Jhv6He7z4oWkj2;,15=UI-`O;efR_#J</B%i*"?bA:r(Hz_VazxU9,Vw]"4BqU(pp(#S1)[H
j(fPP!Ux+xvrp@1_TwRp|N*WnKL-H,Wu`LAQ^>^QE[;^{e*sm-)y2hxco$b+r()n+bYB)*ZlkRB=@oOoNIx[J&w.,Q|ru<k4R_vOy"nMH.nORIYSy(f>YNGWmwEU}G>3Gd~8>hQ-G<qr+buLo+o.d@Y_=FWnR?Ud`Lc,PL!%CQ#0U;Y<MTFw."stk98*-/E#ji|<hX=$-8X%hyuI^2|aD.;+5tZZ
DH_]fg-o8^O/&O
d-%]4DAB^m{&p#MaSnRgGiVfwSL@WfI9?-9*1L/)u@J^B0aunT6xr#JlG[5Oc29+eBe`yn%y*pUKun*fyWdd#(JUZij5svGgUKRA]>q-R].+]U_Nt/:we"SryUE&v/AF>CX*{M-O3WD(>0FNm^#/k]Ye/eXYc^0Y[:~t/F<%m+HDA]rM!rTf?>d;O%_/x-`nY>2`Wg^
=V4%%`"QJ+C7d`{a%c;:El"b/E@EvkzDUU|h$1:%vf15P=wwvUU4nK}hN<16"J[-($Yu2g(S;l?FWS-JDf*29VcX]wP:G5{@-jL0;vZ#(RYW^e8iVZ]^J:/H_;eu)(FTn*1eq30^5A]CL?!<L:C=f=zJ_bN5*M4GZTfkIM>bvM#6e+co[EtOM"(OijPE^=xEAOy)I4{T-ksgqMhhQ<7qjdiurd>HSY*eFcb?QauFB08pil8u1!Z=vkC57]<$oU@W%*{,fOOkitEo-`lh:E]JYw:QAj08|9I?o;o"{X<Jbi`!"M>r9agk=c.`TDm!K:<M<hNV.h|@hbZGF%kc$ppG,M
!4ptsCix.S9xTWl?Rq$JbX&yEtrrvG`4T`/
LOe[bW^3CBT4$hL@UJx1P>oxpp2{DI;>bpk_C7LHQIGF!3[rUW2E"9"[fvQ{v%0XvFb$8e^}BPn=2}xwSILX[?Oa;RK>7J55*Lb@O9cY;8"hWt,DJ1l|IGE.@0iu$CfNm3g5c4&JXXE=&ZcH%MOvtwN>';case"ca":return'#]^@j5I.W?SK,g"1u
i
A/A35,U>PD@aLR8T/)2,#7;63UN053KGXo,yq*)2s<^TpNVRK*9&<M;vfhI)A&/Vq+T9oQ$S4E^mQnEAktY!N=Mh;Vz$UrmKs&/_Zq}6db=jZ`[Z@SOwph}t/4efkI2UT3Bq)Ur.Z;O3^4-iKn7WH%2H3XW(5IGJ}`I_t_k>N^4P}YB(]s1hqeZ_467ZD*@W:o(4-d"l^uN6QEHo+H+D>V06vF{9qMN]*56nL(cU`Ane<M_rzKpy70ookf.e$,gs?gcAPut0/QGy(fE210EX6ASp1mu=;t&3<8(d,9"f!ix:=71Q%JM%Kn-7I9x5P[>D5yIUWOHFsx?+Tt{9dIJ$/_p0P_%V#$Q-UHNOaKO,=6JCxw8WVb^yuPOa4OC$
cxw1I-bk^^=#_Hr>&6=6jpS
/eafU$r}UBvH+):B>BtPD"Q!*^5Z=Dsu=5y*BafS.6`JkLZzv{(VN/[|4=Or[vrg][_GcYoL=5Xibbg^`m7Q+7XDyWqmqd.x+>m)%Q0CXX[7!RRgeG?%C6@a]wT?X:.TRHSp0+UaZFie^Mwbmu!I*WX"/qgcbB[Wp6.<<G<7v.B7)kDqjp[oF~xu"A=F29`ZK|12gr!;Rs7u"2u+n*DNFEJ8%LxU;BF>Fq])C@wDQ/[_C0=
h)[[5{lQe/E|I>9Qj}6jj
90u@DxF-Dc&$TxNLABL!@NMqBiH:F>.?7`!bWoDbKHCL^|K!
HK1LS/E+R<IAap9AQQMX{8NBr_T6er]b@tDnk#<MYDIr95m^XesXOPA;%m@TfdG
U2VT|_X%H^`8wF^%,RMIR:~%TW{NM_}4IuaWXpH]bxRP^].xw6a`qH%JCI61lnMu$A
u-afZF8zz(?A]VPz#97:15=.&n5WPA0&ScLwV]K$;HR[K,*Wwrsxf|v-[yT7`ZA
2>40XF;sZbxlnt:1ZfQ~NPKE_!+Dq?K)Kxyksyb$,&>cZ~8lSACU@gP0S1K)P]rCVmnNq#M|v>e%M7m2<Ab/CL&|<iLxSCHw!Sy|3DU|o<T?t=,wQy3"><I)O-8y"%eM@y9!l5QX>-r%qa(U2A."<kw12X5i**$w4tGs4?Q_U
7x`a=JOMG2&WjqJ#OxZUiOh0Uyk1qnxj=gTGpk4y:G8B(4%0U;9Y$eYbN[>BT8*3`;%s,_"|lZjvgXpPSmRW%h.*8)y<2@tqP2jswvi-z(2QI8:-p99dQ-wG($`(gJeA6fRMwW-"r),|@ID=AeSAa82|L4FTC6;!&=pA@On-A8L6g#Fg(YSJ,N*.Moc
Ri>Wn"8E<scQVPW<
pqtw&//:&3$p
a9$}PIlkiUf=Zh.g2VZ@$4Muxa%,E/e4yf/e3v0nPz^s7_e2yDXSWCEZ@q`|.=(HuZjK".h|DlOrGoHV6:-?
4tYZ:J^hY%U_*$)I#E&3N7N0"i@Mgqt,FNEvnvf?(`^S*%@L"_F*:HEOFV&!{IpWe
Di?Zzx+s-Wbh,/y2-
wb5K=WTJ7LE=<#2#9`U
NC}.fDDK*"eYk&DAB4)gXRI]TB3S7vyMn@VH5j7L8L9!=S|=[+~KE8>,c$tpjTJo2tVx"uu;6i"Z&M#EFS
1:@1r.BK57Opk~6-)yl
pJCeTBp!fc
|
#_1r8SFF*`4_~Tx^7.|3y[_X;99Vo_q$@f<1C&#<xi[5!Hr-:g!b%WY
9N`3xBy
wR=.L"6Vjd.PtThL`-%!vnVs7/PFd2#&onXZ:^mD`JA<@R=w*,
YG
~!D)&OUq}7<M|
iEln;9#SG@er;SlOIU5YL6#huI{U,.2=tg;Lj9XZfD+?]wF0F0C=`LDORUOgIOwldanX?`9jWSSM[_?y>JSIbU;(w_~0)o.8h%H&.Z4qN!fv[lBmO^g*`^(6#Cd3u+hH
f{>R+qYO;_?VsE[j]?me->AIN4Iq]b!yLve5wbKlT~>mBbJs$`C{^6gh+77ES<@:$>eRW1qi.r]:ASAqHofVUMiaTcazOZ`.t]B#8fuYDz4>0Btpm1Okr/sYKiNB1n1C-)_e2zNJtzEHoE[cS>MJw/j(/)C>!HsVEA`Aai3kB&`*o,>_7*vF$YHA3d7l>A0T/}DN/~i0Ejp%!"*56[R,P+]uyq:s+r(C.:[mu#=$A[c^BZkH9Q1RG(Qj%xLEstDgLk,>50h2ic:^LU$8?Gl`$hx?ux`t=ma09wbk1EFeAL5PFKYOUZgm0Ivb*T13*uQyx8]9aqsICzz!RmQn?W?=b$>R9x$##tOr,4)CwHIw*[_#9%fuEz1*p(m7MEm5"/6?TBFxR[(aYO.5jC)OXfK89&-5SQD_r%Yu&<sH%G02i7W3PB;z$`Nd`2992;Kin&t4i8L(iH#^p~7uR+4P<{]?k6xC4NROe|H?TmrzRWAN0x@Tk!3P;)H&3AAY"{73`4u[RfT
HQ.B:1Z~L3+VemT"Nu8v0unPm
a(=*W5L2pd1{iIhd%
PjlCM|>a"cFen*2Qy&?k#>uuEmt
`JKdjpKh[3U5[_)4cFnx?OBm$mqD7kr
HUUc#:3=^ik@29lz:ixqLB:nDQ^A*==I*q^v6uTA!,-CXxodGA/GH(Q3Jh8lPvA(i8%4Pj0-qe8h6QdLS<or%sm/@eYMN?c|vVL.S2@Ta$boD/:D<m0=%"rVL1#hY|5RJ?Y5dwo1GZm-2*Vpq!u#EAWf/Kdw[3aR:p#:Y.V9sAn%Zg2L;)mF3H[.C+ELe3_%yU^:h#2
eD$o+1
q0AH|/7X=
#]cud[uD_JYJ&er_wCvM1+ggB!Zu)!)j|9]=qUwj6VSd:04G}U3-LplSXAU_QUWj2]-A1B:iFXk1AgT`2`yQ:F</*Txk.qej=Na6K%Q3zC-IQ35Cx;`Ac(neebj>CM.;D`V?I>oI(oYwj
5U5*
0"Y{n=QG:D_O+,v:M8DfxpDEZ+<zvu,r5|42CPAuUL+e7y:(l7<0;Wm~G%GVh(
5mE){oR%^&{093-Y^E=2h3TO+!yGZn|[3d?D&@ooureaLca]V3Xs*/(tG@{Ax&h(:z!v{p>l&sCcvf=%8[;p
PbiD&:XQA7Fr22L<9qh&<
T8P2SaIqR](/Yf-uQt2KU}dBrMtX';case"cs":return',]^@)bTDI@W!(ie"]Vx!QJ7*QE8#v98C.Uoo6UmT`kj/`JE8b&4BJ,VKf=}s-/i*{dTdU-!hgyKB~<}c/@V_IjSd+HyJ|
v6[XSJTwGE`VD0nRIKLv9EFrz%z*!Hfy#rPnP<XTFy>mFjAwVF|Y7FEyI(hgU)L>%wqR!uhmO<dOjs8x@P87Jvzr:o^kJYI.KpcbQdgo|vOcjD!nq2G7%!K:
n-;l]/;^^Rvpcv+[Sn+[c5B)w1nuqexE5:Hc>$iO"lB/S3
4vIv!n+!7snWuM*ntG?e/KUa.">L"hj,cZ,I()x
Kv-PLL5DSbrcDdTR9R+YK5N0kV[hw"}qT79@d_3*g`ttFan5n5U3Fpx;X3`T-V4@kD!j=fYMWu5m
a07nN"^qq-A?USl:U0&=D9gVt>$FSq#*;Wl-Fc$PSSK2`{xEf>`(M-G/GTLO"0Rs")F2FQ-zUsnK(qs9xN@6b$BJ<}H2r7DmuM.Q*obxp&K{6ch2-ZDrqI3O7wwbj"8Q/,=TLk4MFpXYAWS9W?
)rgt)PCr"Uk`wd-/%_L1}7UgBP|xbN<-,4NJi+@hl(+6[8v@OsM!<y+M3.[`UW(<
+lK;-lt,<MyPfzjqU-$>S#Ljv_qsaI<rUIR!P3bbJtPdtkWpc*S(H-G],=P4*%1t"7omqp+:UtBMTchb[@t}dIW}
6Yab
WUEz;LX$U$-6$(W#)?As9Xti5-9qAF
vbi6H?P`iy4xW#=RGoVJQrB;^s/1J5D!?6S3Yx;SRF$66J`Bz9=8^vu#X>/g
,>)}D<6Wh,G5Q6
9!UPy4y<V"vP:-/m^K6vGyyPPN6OlGif
xWVGBeNgXQI;ANFf9s1~Kj?$J,[FuR>#[b*_t}:*C:R"_)^IfO
j6$<?Ar5:K^&@U!S"=3-s<iM]H0u`@pH5/oQ&wL"!=9Tfh+cv%Z2%Pa$.Q(%NeS=r+ZWdaCSOV>y~rnp^dv]^FRWeGu]<y2oP&5U)1FQ~^)7{[v%~gy@{HDsS?_6?^-hR!OpUfQe=G{l*4o0dui>(_g$;ezMPt+,88F<0Ux<Iq3I+yh1?`
!G-_Ln`1>4,OEya2aaY(m^Gz:XyuJl`%V0*vk`NePr*[O}Tj!U*y.E1d.rJX;BZy=)y/I;58bmn(V%R/L+OV/`fd@wR8Pu;ZPGr&j2sOlnnqlwL]TFxKJP&z9swf-ctT%yXH6y5DMLmkY{ch7YY7i~&8?!OGxAv/3mz"yD0O(lX}N3^uW!1A?]dLxxib^K;=sZPyiB3HK9bH_7B2E-(QY>lhtY:|^x:0J9z#wW3og-g@0%;mF&&4*twohjyEM3+
yWH:i5&xf+6k;0+n9"f}S{AlOVP4kf5=Lh27`qNh2@)2Mxho=O9j4p8X$VBnu6qReN?4[ikZISV2Q0:RIK[sG]uq!];XS)*KdX)[hE$xnS,Atd!/;YMS6Cc"H%`7&:Im"mJV?n.T!Z"=u[+$x6>IwI#jYeVut`qz7EGWCLSM[FmsFTO`mw/a"PU3SVT3]7l?&CI5C|#gI[9*lhH<1SSq6+(JliVL]|td=D#RDYFG$$,/EJFzA,`bfOUKU)1VNW]!/06ny.Vq4Uoc&Bxk/To[4>IBXl`6Zke5h?B3G*8f!l^0X0.mYGZM[31Fg8<a/LDau?<V6P4m;xQo?*m_+Rdz%Pjl"ErSrZ^t1k@
;yC1#_$h
7hRNF6Re[]ql@0AX$9-3;$&$Q=ZwVc:bC7?J<D:Lg^B3G.FY+Al_<M
3^AXC.eaLr=q)["GIf/zyQkYkoU*ik`pf>x(n[g/#ry{d9?y)wx4={OQf4ogCJy_TC0LRp9U(G+<4fVtIeo;8H,]Ctsr3n"[&>"E+*H_3F"SV#S{=MRzG|!/)|8dl>#c0872f~F_noQ}jR/K)WQ!3{I#K>lF+DM*sMgak{:ln^!no.sEQ`5<NDp@R?L+)Gmh<,es%Mwp#1eMp6F:h!IW2r=H5M6=KS2k]jpN$pp0+3"oc@d70o7qN!MOGo/[]C9RIDBo#Q%2HGY?3|Hx"^tJfkSgSb<~u"eTMqoBx:d^dSu[D$Z;h-)&^nn)Ft8n.|%%Nb(Q?0G%iwyUo5kNl][[RRF7&"Pel_Q*Z6@{T0-
,^G6]t(^Q7i[^bbr969?exe;U6sMeH$ogkkXvB>:M/+U;oG4VAZg/!Ehqi."mmTe2lljV9;0
@6%ZN&/
gA>$>%Vd|+5"aT[0&"caHBwHJBd>d:YmoA^r5&7p4W}v^)N)4qCl=KPJ&mGim:zjf@"O<]n<cYg6jDp3N_JX>OgKd=8d!bXst+^)1HCvI,!jr-Y[MgiTR)%FB27l[=}>cG.F};#yLSPZycQK-,cM#PuC~Izmlt+vA+.OEO-p;]!7)fq,5Er3Jxmo<RuJ0;Fb"TS4&%k1WbwQ/wfB++_6F+bF("mV!B%]pYXUutv/DR?:U:z4~c:09)<1&Ty?RZeR78vN9$QhQex<AHp:#D(EuEF!p3if37GrA&:t~<+12b`A^vHk,K^7]JE>(l_=WSdjUt,,/){8TNIE-C5LK?sXOi>Bm$!4b1
Pz*sDP<oUDK:OQRA0>u;Lig6]L/*>)>z+^Q_jBO$Gn<C-<.[=A@])/g4Nu#J+yRDv*PV>^37YJkJ":oleyd>`%X<G$luP[g{X{c;sdMA`wGt9
w^fwL~?=A[_OZrU^5ak&7&@To=I8,EPnf7%tQc5sVt,G/rhPuaHkwRtz)]_vU+r6n*y`(%hjE_
@L(R=j84lnHtP;E>mE7@0p1b3sSMaW8#-i(NX
Z;fT^)4Pp#%37Qod6E!:8EQHls/u,:dJA"vEUI{PI+*vTtHV%<6t(53-&3]i:u0
=W7%rQL>9<:k]%DG3odao^IvJub3C1u#)1v`C<q`QM.Nt*5$pyI=Iw-bu/K3nG_F
[]CoKdBIL0>iBO$BI2O2%!&ia@:+Qp(.y[al8fiKK8D:6*EIq=P_J>Uoh,Qi&T=r6&NI/=5|8a;u"fg{
lS8`T1:Rz=)iN)XTKA(yLiu5"81VW2AONKt;Gx(<mQ6hhd/B]KRqn.t-ulzD.EO9
prOhBr)xOB+.NTVQdFn*px7aynx%4DB!Ua[/4<n|
/f3>.jo0~Uf4m<f2Ba}[Ue"sY;<o9p
3WxD6JGr7=L}"@K)IlSTd!_"W3Y8G*Faq}!F5?fd&~7a]Xe~ETCS8&ZMsxJ|>B[Om}dyBuD|owIl-H*}/W.]l*JJd^d07q;rUl]Fa$Wjcfkm-6_DxSSkqOW*LvXg!B6m./(u2d9<ppU)P?q$b[q}Wf7r7#[1Ca^s[Rpj5Fk_RzRKSbha543_aXv|4,fp(dBd;1Vw]B(i)r-yygo)';case"da":return'-Z}5pbPDI@G^OU3P4hMf#O"#p-6R^*bm}5K,7f=$=F}=k]KGcW1!,DZ5-/DE`E^Lr/b/PnuM)+KF]q;0m_""}^Ir}XRyFA0oJvm1(,<1RuMc&ZIMnXAKL9-?gt)HcITYwHQAtZQO|tAb)^`_Is(L."UXBx+PH<QN}azHTo_sW&H[C/_4iv?"^n95{lw$Ikg?l0`JH^K/hz%i6qe,WH:+*<Nw],bnY)GpgdeFnt0mQln!Bxy8Ml^^DSWGBf|q<DZq$,,Y)GJ)oxgn)uT*u:g:d:cJa_}3:d>>D:`SUqY[GQ>S=@j"
2"7W/bKgjqo@nto
5[c_2-uNcN)4
nVmkuMM-_l_BBRYYIL0w>=n#Iu+J5^kw&K!EUUk`oI5rsJ)xcL[J%(Mu(j_6:@=q+8Za(u}m9Aeg7m5721q.|,qA!6A>dgM"b-9@?M6H+aEkPU|1`]k*BIX*/q
Td=RQurPCFeYRD8gRk<g<n/]V@a|W[06KjkG7p7*&Iy/OgjV6-!SvR_iIoJP"YL|];S,vTXv=YM-n=KlM8XH+aF;7w[[_WJ]T1P(`cL5^Mfq>:Abj^e8Menh8iS`v4r!*5HunOfz3y^"h[B_Dtl2,kXpw}He<!&hB;=z_,XV&[E-j`sAsLfd,1imTEl}4JIz:bs*oK&,:b.,[$3r8ASEh"!u%(lYS2O66a3~K$GXi[J]n:7)IV-_Zef%6.
DTg&hx/Zv?pvSJJC7w!W=V^b5qc^;7}%e&,i+PnB`<h,~Z>QE3d6xNA*-n?p1&KW!C@ynRx=CK3kD^3c2Bp"oFG/Jp<]{WWjHspZ5k{:!:ZXz)Y^:o"e=FUc3<-JMv&MF`"cRu{>OYt(&Nh&s&::]dSw|R"+Ml/(|Kot//=e{r^rMX@mfg?f
Lg(f^W
K><qzBK8Ga4a&ixZSdN4z.&X:/J,5UZKVyNDB/9(5fb<?j~ulHcTl:{1OIvYzMlt6GD_r6"G9=3u=?ChRqF?&%
9OkVw;_(z!Fb!*9^mo:ixobuOp!oXpMb<esbG}p>`p$Yyy$gmOm`1VVtQS.BL>v)&9muF=*!x?XagCN>uN`GSPesqR5JLDy&-(SjQ=FwB9uPw?vgu%j|Y4YQv^?DID-*Yy=L,;z)_W1oeN:&Qx_~[cI3C-p6d=q{qisj+{B+BtXA.c`|JhtI6UjxZgy[jKN@Vn`cL7qd_[0s(USjxk^dP5-:]@02us_hM!5$b%S0GR%rUCtVttUGq"yGV+ygb"b/=Z/)6fv%QS`HNo
Kv^Q]^`,ZjdOFWtc[Q[N4Jcu8N#y/T}m
30Bne+VDc]3LQ]W<
wq`b{5ePL^5hE6dbWOx2I!hqdY|
nM@[Lj/>Q"^?gCURY]e$@
1F5DO,XI[eC)u@(,dC8Z9DbM!oK!@OQ6l1^n.,=&r5*uv*?$,p5OTAqXQuh9O7L"iRRX{k!vYJ)rb7]bET)4|yC7:+kNI@v!409mEoLSEqhZqFE@d!"@?;*_^D<SWScd<NYxn(*M?BMu!iyEg:P0Em-ArkJ]GZvuc&}q><07RcVpBd.u`qBxu$;s+wcugN1):(g3S
_Una;i{LkOin_&eUUcfx;k+S?kY8#<o$d-+djl$q5dA?_FN$Ag:+hA=7-G}TqV(*-p2GSKrlX]H%DM.q2Md(WBV815l
,wg^NGMe;MGM81*OKbR6wIA%.y62DEz!x5,*V!<DnCSZ9@b;E9tDOA^%Qfcs9O<hn<&DuJ8..)2M8T4vLlnB41%ops?0GnAGh$`%OI?/WhgF)9Oc@xagGI0e;Awt;W:yQ7,LsI.[*[+R{[AH#;7rxy5^<D%g"2QOo_.!^6Tps5h7wG/==t}IHHp%nSh_M3ZADk4;Rx1F_WXyCt>`k%j>FZBW{PpxDXQ[^&?-K2M_JRqyqX.r!%@*gJNLEk"@T"CM~NXyNBs)Gw>&XD1wX!x)W4Zv?KD,N0:Bwk-k.k#EYhM;@]3/ab|!zM?vM7frD*g3v7.IqOBYHF`sV)/A0wPWDGED!(%f?gP?w!%4mbN`*bY]Q5^e/:b
-5^VKei)I7QE6pUp2V3yH.9Kz1R2P+.os
V1SO6&nYoyc)%yM0Ne7MoF`bq7y%9!sUYtkgh?(&*8{yP4+g:i"gQ1WbiJp^RjQD"_i+B^a3JQ>-s/53i7g16l*/O</b@spbt
)hpUSv]r,Hq.9N.i<GXb4KB@q*4P1)O)vfE;`/ojaEMXz_/0F+(vcAC;F4hddsVNt55:qRL
t]o
:><`p^ov6e(`KPnUGo
m}F[adZ^W?JoDi:#
]PH2>l*qiXrsQbXLgMfGkFWuQOXpsyIU+ggBq%
:1O|%lM^EV>o3QQWnKWL<(:%KQN?0!J3E>P77A7&#GeE@{sJJ(8cc/w}BqXXvRf7UP
qT]rz_7EDAVSd*>4"1tcOF=
+mA9C%v8^nGr_[IXj.%,sVNu3:G(7ZhI>1r^@=MEn/UDB1*J*(cVK$%:i15N_6hu5>Da>_:$(Qno7b6!j=ySPr$k%,VK1$&!>H=Vk@--YENZx8taZwXAx[?/}`iJp-j*y;#!DJTcW^J;~9*_z9*ylsHyg>49xMb5Mt`pT0@OR`+_G>95"5qZ=bM+
M}xb,qJ1f}7Fn$?knE.GPgroMCdSUk!2srD!"3rgnzbG:c30po@a1(Jai)s>S>Ra@u=Gw7;IMX^AunP8Bi)C5,NB]C(Wc7YS77o;TL?*t4e1CDLHn^KzX:wR^vvYvmd(';case"de":return')]^@iaTp=)Rd&Cu;m#+eK00;rT@8(JdJ>xU(KY(G]lPW=S;*`2spsPQ9~eW"=xUvYb
<kwS2K/P?WR
,K>&)Hjft54Rb9B/@&k(Z.tR(q6;WJ]C]6cMQATp%^W~MXM`(5
j-t&rk7>p0zgD6+y8]Zo,wJa^Xm*ruq5OhR`vt}yIo_a.eDPP0I_8bYRbF`0WHR$|DNM,uLbHl-
G,Dkh*WfAeG
tm1nvuz3C>n95y2,@yPB#Os8NAVz)KRuZ.=Gl-tGvE6WI.%a6g8;awMTTgY
f_"g#.z`FApSXj6iF7@^M_}&eSS$=dO9rbqRVN
<en0sAiJ*|sw2.g?xlWURi%o9}.y
zV]WZ"]p(9/Z|G3uy+;?(aBF_QGTY6RI>.|yLL
d;_ro
$$2kS(x#HLr>-?U+9Wf3u@CVInDwC6B!`:SjAR1g28_ub"vdiNF~7q/jZ?CP2ySQV[8H8QaZXW?yWBG
b?/Rmv)q)tODcML/0mH[Ei8^7.bLyT7Wfd]JO~O$qn#ScTsZCkDq@w"j67)fCRq<Ld,|d&,6kGEfV54Up7l_`z3Kr_pWb$o
k]E(M}u:8"OL9"%y$ebO`76q)opj4=)jCUpKPC_k)@X;&@f|ko+6SG[oVVe$.9@$7t$?8G@41R"VAm:b?
9KDyt*i(Iwqehmes@MdsA[&A2Wn7^}e/]];&yg-kFb[=@Ix/yE3:&tJ"R1e^2pH4FL9%
po^cg<[x_.II9FQlXT^,NYMqkozvymQ7Nm&bZYL7_EgESL*AVAc?!7"wsJPbn.y%6YdZmyGYB8
g;>`gzp>hp4#_6Lw
*HEXWmeRJAKnb#H3v_<_2aLJ+l-P$XsoPoFAE7h&c_/+N4|uoycFy^6-DH~F;iDK`]fL|x`B22qokT7M.k|Bu"?_(pUoM!6_;"t#S;>y&<oy!DgFeV@FPq.p>]X;2loi7Pc6k!/*z%H&*^I^rUXOw>Gj4%ZyF
7^m
/sLM~u;r`<JkZfztU*
-V6.8&ZEh^iy&+ox1qbb1n"$Y<a.<n6)C=
#&,kb5Q/OdpI$loJcl!&]C3oe*&naQj%xphFg-{#D?[S[fh,H5P*cc}->]i=/&O]c-9^Zm)dRj"2V3^^B!D6)IPrUX]c=,cc<VEWI4`>*0J$ke@`&#A0TqXBdM,W~s5a98S-9dJ-N&V,?i=y}UAK3DoK/Dw/:?MS[yz4#t6uxQ:^rcDy[@)"6@[,*sZ@=rcGbpNV$e;vvM<+b9(#6XlZ1^}-m!9DsKac$!p&k,H=_E/O+$XoM@Y1WO7h*FfZ:;WD$4%FVl[z$cbO"mCEB;138EWZ#Xg0sK(ez6s5Rf%06"A$MxX:2cx:%a6SqdngBC5o"2y:8R>*.W)V4Qr@OJlyG]kO$C*>vA#z#?mrg=;Re@~r#sH,XWw4pR)TJ<aeo&[D]DA#An+eA7cmdAev],!]u7;!SDiF3]]xy^!%Q(u`H7wPr[aXI3"UFH~-xBfg*[#T>Jzx(d
HrE-V>k54@$hhJljEo6c/a*d@gC"3!L*"&[~@L#oT._7g1;<*-Rx1%i6).evH$`ba0F4l>KO%}xq2qgBo>G/nI9>Oi;b3TnFg"DNwLRw,%vl]:GHre!p*TDC>BP~4coZNY%npfZLA>hcGYg+0;KDSNXi9C5`mH2OOf#06Pd7:IT#jrqp;gkMJt?"Euqed+N`gr>@h3GW:#B?gq43:*:[RynDC.d5`,y[uzMlA0I*Jep&F*ON]9)DbM!M;LE)X8RAX0
T9Xt8MXkFeJ/2ywj/+F@gSC>jlt+$("x>%^HH0)5hYH.m5dRe%}YaR;1
T^mq,==/kIEUU~1YxbhYI07ELjQdxp>s+FUM_2iewO
4[D=l4ct.Q[/e7BmeZcMjcW/3aO_KINxk9.]jJN;q/i2ha5$Pj@Pi7*p_NW/-8H:}WrZ~H]c(R_t46SZKe0k
irHh__q8dEvRy&qC51N1W:r..y;JT}*dB2<GcF@G[@jbJ53@wUCI08u6A[GmSC4<3~G*yZn#)gUzZkZC.Ll=ZnleR[q9uto%5/SLi(S{lgaGqj7a6li2l+9SK<85(9N^tQ)A<4CunCC{Ap=ehdB/51ga3a3W)fOUOF/Lb
H/7Kq$PzQ/fUhgs]2mx)B&pLU+B!h:kx^fLRld:4AMST)#@fdt>!CkB):~jFAt;&c1K~SC].,</cp%ceP6VqHRAhMRWcG9w,GG!LsKy{aJ`gNQ!2e#Jt/S52w2Cl/;v15nXmGA-q&x9a13gU"MpiD{27lrjBGlB?A?PnUhw]9V({QR31]WR&T&xZ="]xmqikMz?1Y}*;
an!RnJR.>M]>-pHrL0"RV?-Cv0t>B"8O6E`2h.+;b[GJ[F
QZ;$"G:h3_P=aRH`qYKs+
_yA$KUHmTb@uUR#yLf;RFP;W=e;eKMg6F6RV@Dx+[s^S*FE78H$-%.S_jps64&1T*@BgF|6JF6kQ31[m1=w6p1?rQu8K3
A25ze*q,V"FGk;QRIZ5NCWlNVpJ{I+#swS
~vQ;H7O4@wXpNc|pI.D-KGsE*qbO@YS`#NBFIklw7;}SNmJ*M.84_z!0!5w)@[9T$%~.mNoJ^#mYlvWU+7}FlOz6%671xQVvja^h)veKPmv8B-d%u3`Bmp$/WH"W*_<:>DO9-k.dY,JK{Wpa/GUEL^U.SO=W_;i.F.sni7L%%o)ms6zqHa!D|_>!&J2+(DK%F!e_w3xE}?#_9cJrz-;Vy:vl$N*E6C?!8L<%Su=L|(lMR:LFrWfNb<$Izmf7Y5>0Hf&M!,0]Vof@>yJt=B:<;)CXgqIGU/Ra=.i[J]p^&WO*+,o1.eUF~"+Ao@]>UXug/3}w.#NZE2&R^)UjVd:?XDC.Vlx@Z;b
c)q_V@z]EJ#b#0>[A!xXW9Q;2mh4C#TyaveSKkiAAdpFPhO#I$:o7TbOk90Qp1!H%jo#}@KE7]R.Lx
XwW$Lr]evKwx5sl[W*CK:`Y??jla^*TF7gJ}fGu@vlr$oD0Y4GLxM:
uA}Dj)x8B<5_Zs`Y/xBCJb|xyJs4wBou;LLm0cIghM7FVt&2F2VXSi#)lK);sj,[l#%!zvnX(W4["++jAThX(&zZLD1liG{Tzy-RG";Q{W:GQl3N:WPGCp}ErecUEI~ws)Fu$x~NvT}hHqaX6dqX|+]';case"et":return'&s`@ibOZ+:%!(id"*-#1vwQP!?4g:(+Dprt/K9j>:9J5oH%yX8"*2^MnT5zr$n
J7$Q2h;ZW(i_=k2v_1buV$c>w!,M?5?LGEGTxS,RU/$AV/WE]]iY(/`Zm3!W&Gi65(.58XYC(j&7*ngB>t?}$Yw7y.bzt)Ozbw8mPPqac5H1^e^YO.gctf,>I7mLgyYHF$`5.sa;Amh+cdyVGMN7c|Wy2<#>d[60[0[274y9,3>Y#cL1j++K^g"3D[!fs
cs`U8p/z"8]bX?i[ZY3EN+M=YlE`G$@$sm^Tftyh[d`pU+qL_V<WMday9Ie<Bqbvt?5cJrP&Grm;$bhPfO=Fu:Ux(k].`a?~jK&fJG
5BQg
vd$#OIjlby?Qr+j"&t>r,W^
7KA27i&u&ROhm=*28z5C@#e.[d^MPGpp*EgzTWw[VJy)-8X}Df2=m
W(f=3[0Ay}W*0RFoZ*v(peLEXtmONTTZUF`bh/pU1QT=CU.PDm*21:K)2|kuowv
:W;Y`3UkE?v?e9w
D,Y4y(1,U>A+IU_.
,_C"tdTvCI.wvb(+wS-X)p5^OnXO]W
X:<`.5j4/

sLpnPxMnuUWSV29C+&y,%:mFp[];&=xA>)"cRn4pIsIREL0v9JMw+kc&!8WN&aSbvwdL4"LO/jb]tvkH/F-I
;lUdGW2?nAX3??v6GKfGwHnj_lRcolV9;4tO]dM3*u3<P=n=HQt.:`k";84"*.0,Uo:YR}e"MK0.6Vkq;YYTmSq}CL6SwB8#m#:H#|XhPz7n43y<vCx1>Au"AS^F&=s54X%oFMpqY54,k!R]a-M#TuihJR,""O+eWC&.yWVa5feaoT"ItC![M1r;5MBm1AM%Q8.uU;*siI"r.G;CQ9^Bp!<^?_kpjZ.M>r?BBLH2,R>Hc
P</$D?cSZ!M&`7x=2:+k`$k@q}lCsO5K:HT1@(7q^OJO>bl>;s]d-FlAD=H`W}[q_b0/;/rz04%vOx-^["BgyfBHTe:gFZ:Yq~+~w}gWLb;
Re&HA,wc#!<_Of;j,E38g_W(iDdV:mR(Oiz$5ANO9vg/hO.(#y:T>fXBd~P|;oorHF_qoNF;/F!:NtM=H;Q7m96Dp]$E"i3OPe2T
r/rBqE*.#o=@~uG;C$r5JT4!f%0xR0Rx`tYNSoN5
!rp$(Me}Xs1lXHa}<mb}Ylk,CYNHty.RKaEn&j4?88m;m9G,F&#y=q5Os[Dq:kr-=BT7"CF-#V.Mg-fgmAUXX;SR7H2AyD9@ySnOq+]/Jp4^Q>/(^cJ8cMw;y9,o.*r86rNShQcre?lQ-_(:+=6s6.,O#)6SeJj.#<@19fCrdWQ./0.F
J&tXEY>M*ij1uTH<E^(?]5s^D<gC/vfc15rvBpz0K
"kg?/33mv;UFlKSPdWpxAIzt)vTNDxw%,hEd^cYHi]#60a.d?;g!v90JnGUG/2$cc5"^.Ss&W@pWCIC"@[/mjA52)Gv^8
-$6Qq<"]MVYk:D>?W=<`j=rcAr<qNX&fbLysQn5L+1?$mxlYOFZHrY%06t5jQm5/KL]%{PwO?CSDL7F0C>ug0d"*kw]T,cB%o0F`+GD5bcW0B,2n%?@XmtkwY:Em*wmtkufW}xTm@i^liz!3.LYEv2!QNVrX(UiIP3fS66J2h+{.?4$v`NWn0VxNa,%T^hDEo6;)aDP^thO!wbJoSm`ey3z#0,Q1Y.
VPg+*
UyQD2bAi7DMzOr24/1cnk__rG)w!?Y`2jB5tXQx0pO^Q8d:ZDAl_KxlloW6Yrs[#1(qOOcgJ_CTi3T"?nv8cp=:6]vHW]/<n;h(=H?_at?RFl!js=cd[(gp9+P=e*Vr*=RQY*IU7Y8p?qZb1bFS/l}Fer?n/i!^==[>=b?5zAjgK
?Zcp=q-Ct(]w<m*;)+vi3duh.*SA$F_?NR)xqiyT()cX)Ui){Eze+f0KaumC6rx$1,nDN.|IrF5Avp"bzSU[Uyxxz
@`%z)q6`>f`k{^2JD9sxRm#sTN"j62Ff,MKi61UM!>{gAJZy`?1q~3*xKEUKM@ccjKO.I[>+}HM.BZ6Orv*/D]b)Hl;nbre9ikgZ-1L"17-?xSlI
2IX^tB`Aqp,+&/_2&B&I1QDIae`=.!:$+XSd9pNhq`l771XuKt
2DS25QtEg2l6j,al
,WVQkfgr*ls/H8?jc!KOLA;9on.uBKx`&!Zt0~!~cf:wNfK]SHvl%TFcMQ"]pK!3o
KO6gjsc;HpG!2?r_Cc$AxZwf.Vo>fSD:2hmGQmHqR,[-DAA_4N#TfG=UD}l:O9';case"es":return'#`G@r5IAP(q@#lT"z5!3,TD:%)L+$8,m,lI$Sp/J4FvF,[AWGiBB0<_SUxFRy8J,P<Ro8Zepe7rw+YzEa]CDZ5ysSjG_x%Y`k@!wx1g9GXud;:,({[MM)C42`tid/8/%klT1>xDcK<4:eVsuv*8daMS`xyf3Wg%(V"vV
D8fIbdcNf<&8llm~-E
:#zFj4*JpZB*C4Z2D;LGNy#MTUl1"+:LzmDausZY4>*C!>j$gy|y(k:4370CB#?qhxPj`b_$0;$+eBhb}>m<$)wnlS=JnK?gKqQxL9CXomvBi@fx;q)J.p4C]>dE&>g1/v"$/_*:6!1.Pl^NyywOW8b=dQ1oxk.Q?`0<<@oV<_uezHx9;X_z#RdXh+]y5&-D%2$+MaHQ0dIP^[!^wQilvitKChBo0U~o#JU!a7nKO>*WbeT=Z*JAJc/D@G8T+IrqFhbUco)XRPSrEAfd4"IXh3t,(=6(lCt[1(E=>mh>"EtYosMb:dt<7<gX+-YU`Hl0/brsYPWGE_OnKUFxcXo).UUGwBVomMjY"^#p&O
2+_uWJ
>3_XL-KPz?JDC&se@na""N
3<EhuQUYF>Ho0Mk!>T]DAou"P]M{sk`7KUQAl_-0<61qZ2*,tpkBYi9fyR?"
euI
5C[y"++KMWSQI?=ZaIe=}H4^*1.>S)LS6lb9T`U3$Sm3,<0Smnbd$bgJ;W]-
3x"!0iEz%~Rjen&x(IjPNDQ3tI,;8C]"lTGtgb/?DFYnYN3XV!^o+v&hq_L}[im}*i;Rac@aT_#+[YKZDoyllI$U,WsKRIZ)n/&k,J%$<w]M$)-|+Sp7OvHMbRAWkh?ejwdrbE?8nPmMz)K(sf@SL*JHWAp^Y~?v,IW/%,3!Mqj^baFOV8*#s3j<V]$v1bikFWM"p=+eQ~/cM>K8wBG!xqV{Xa1*2F%ID}o"SxT1gUEjU0F&N"=5GS>ALovKhDh"c/,y8T@aegj9t"f*kU[fEd_x6-LYaDy|sk^e6;:_$(lD"s&{cKM+L}3riVxT@FJ75vqvn!^:BoL~@;Ot5`]Xbu-{,r[IY+Dbz$Dg:5(oR+:}QuOf/J"=[m!s4RWh[5x</>NkmT+!Bs&(pG2aMw=u_,8:*K59J54CR*5Cx#Z*DritD?k$plMS6om|2}+}UKJK(9$</[@:lQ&I,=d
AqoOXS,d#/pFe<F9$l-N$p(I&iDv0:N#w{tI7T2Igm..a7syDzSdvyZyxx3Qq1$b=QQl[!e`/G3q5EgLr-wxct#"jP"q>X]Gx=(Ve3CjE^pKmB/J++/imDZI)L;O:tmd4;]X8k_N@D_5C8,YaHPU^Ttm?>!=2to{o&Z:Q/FY2]m[N#8nU79N2hoOQ&(ML3p(gSHz%2MCo_V]*=y;3WTMeP.Rk5m*3wWYUeg9CaDcc;Z(OdONExu8vx7P#;I-!B/BVHNsd<wiY/IWA:b}y(%Bq{Ih&pv
0cT-e.g{.9PV?Xdi&)a_+y8hV0*M+@I6!2JM4`8]efSuV7SJD?-c<>6`Y[D;,5;j8-NWj<E4.j
;fVY/l`X0@)+}%>4LUpq-x3irWS9@b"G$#IEEIz#+?__5u6O=d9f&&1Ei
$j_QV%%_[C}H>0Xo_u}Q(J-,<-Y=f0W4L@iy<KnL2kvewEZAWlIT@S5byR6Aw%N>F98Lj
Sp!TMjmH,,JG>9h0t(zs%+^Uq52btNm`V?Ps7Va!tR3K~F0fw2G!<`Hp*HMf!L?X/qjw>MK[L,gl:t9r=6Qc(@5`Cak]Xp<HfI!M[^VNncOiXTBW![(9#QRl)_jgL"xPM6x?r"DLW&vhXpk--8<mB)7tx[B8V$z9A,RF(PuA6[}C(8jK#K:7D5,Fi2Bidn;/7BTu#H]oyQsL|Tc!jACp{.j/oyc_$-IAL;jj~-N3:%Pkmt)+I
O]:-l[Q"Y%;)3.-9
X/k?CF*!b-?fBk).dq_ho79.s-Q%Cv/AvAy5>)4<=[Dygh%]0A;7CFVd5Ih?PwHKUBPjQp84
i]{FC7LYUa8EM<4$(U/.srg9Mlo
L]%_(@qMoM:9sVP?e5m6~uVK^2O[jpdSg#t6WW7/5rAo!=YZe;[>D>;x<Cl]ANhuVIXA#l&H"$#V`(Ea_Xn^*>Wf.x9h^^r*2EmP2mpowcnn*VT*MU`Ozc2]IFC=at{wYaFD-tqQ2rg<xAaqChx7R>Gc#X?Q!grQ2bM]t&?Ab3
@]rNX4hNx}byu[v67+GE>63Sw{D
L4z#FWqV4.YQskGhMKw0/ra5g*/~7K*Q@E3=c_<y7gEi5{Edl]ksaXcve5kwYE])Q.OV`2ImLkV/1L0yH?<$nk52VF.^0g:^o}1x!MlK;.WZ4w!yIuA2_EgG`0S(Vz^*%F];5l?7
@$Q5!ZAXy?}7n.i6Fu]i1e3!{."W/MO.~PWbiD+<y0[<cW[t?^D;3@w_M""7;Nx_!aalvrCx,6Q#Ci9gerlh"TCAu=6F}oa"j5?Nwd*tEC$]z7$y^707{B3(V`!-G<rqTBUa(K;(ndnlH7B$^R.>*M!:O/($Qx{HEl#?"Qf4F1+W?>PuNSHoQu|,UD-PGn9&,7K]*GuqdL)<,LT.Osoi&Z%9
@NO_go;u99j(skT<jdLKp2IRG+?eW],}E`]v[CO!XEqA,sDk#XyJ-DK)f!6
BbiW$]@~C;
rresbR!fmasIl7n`B(UcZiuTA
N&/SE#b<tW`B7IaKk1
m`OEB+5wc!I^
O?F=NUpM"s&jx$Irob]
$$`FmmX=ziD^;W,5tY~]OT8c6!"7n"f8*&;;tSs;5w+d}fp>
`sZJZ>W0Lumcusc0n=Oj)U-n(;;Mmv+v04shb4#cT.?Os19D/FMyUpfe>z6(x,E7"q1Vt@<mW0uoKa6:LYsdUa`G%>1#]sC7_2kF5@,@nhBC2xwlpV)(.$3:Bo$"d3i;.XD^mbvr=30GvC9jo5b<9l
ujf.j/0dhw](LYZUmMh,QWOOCV[gs[:I.=kxN6eA!CZGFhM6}FuhoT?
6noA-NJ.UD<A7?(1bvEei_#>
jI1V.^`bcWeRn8TBh[+m;hZJ!1Khh1x:+9I0KsVMRO(x)aY*%}?=N;eS"P<q01"]oJN0vo!O$CGU[|J=e%*=v>G4t^';case"fr":return'.Zu@BaLYxE&fYm#.W8E>PC7hi(5"j!:-gdtLU[8yX
{a(_T1@a+:Yh>lwf;O$*p,:ysV8(-w+m8M:AP5<)a3o^+?S`ybPcCp?W>6"Bz/s$BVC!&#Bd>LT]6J&O6BD=9B_"`"=b<w-uTP*c"?QJJ0Ep{gW+T/+_fUsp`
RBk7~GHikV*%j+?Rjl%gB_B%/RQ9r;^lQ
A7xgK[Ec^34/b^+s7K@x6=2bixCyNKJeFP_^d;BN%nnxhv;v>BR?B$Vb[,wI55z)`mqPb_v(^p.1GaXcfi:n]i+o$`"yVdr
xIf_97#;K9Iw=oby+7~,Be)+A;IJSF$i|bo(n@2C8t[,lJ?dH)}@fyh5Jo(({yPXFiNXxi2Kz`dJmO*y^OuV`U?X>1.Rt#^l*aP!uV9]6;@"XwwHO4c3x(kDQ[Tb2[FnKw=>)t}]!*}^c+SmyV8V__mP|1eTZGAXm*>*-tfGH3?ZHy$`bn
6eEsM=lFN;LJ19nz//F;LvOmZ%C~vv.^Wi,tnNb|Lb#hfg@VFXaCb=%+kn
]y`R&x)f
_(6ql2/OwRN^(bjS<sF&fpjY[hbL#<qz/$0av?s03ED"yB/"q"__*f@.1}^Ri,fxAWxovM
2Ss)}h(cP!uknFqe!;&d9:>_Edwl>CJJX^[%MjT:(CV`2h13*?R4NTiw:5c0IyiQ@&WTgoe1srq:9tX&EVFdpp61[ei!k%7ofYd7Cz$9&!Grz9d#T">g+:R1K:`Zq4Ng4jayXxy+zeg/m"$AZWc;v$)y#SMPiVB"mw0Wwr#yLYE=IyDwQ-WCZ8Nn.yEE?>L
V!AjV&%][<tTY;(Y#2K*p`(bW,F,fg<B
xs8~`ON*$V)K3_xBFCc4

[6"%Dd%K5#rnPG,K&-_r7h/36$"U`a@Cy.*^,~Bvz$P2_x*%*lk&th(8@4cIORuyFV^fk^0]CG-KkopfRWVHI@aHr
%94@D}NBFo1X%CI!d5dMv>NnL0="MXk|;@7
ZJ5d
FOhekJdYo=~:rr3Y>ZuI2kg:WLtQ?VW^!8Q-0AWq/mzJeyMr|3bd]4y8]s:M}ujxBK}R9&Pt^]kEz4+!uFONmR&"^"3>jYZhe@I@H>0B:,)<(Vi
X1>X9K*w1*MpYf*Kh@h,[3xUaU?3^9;:aw!&HOMjgFBDA%jnge]-fiNr"YLKxuyTep"1Z`W^KMg)5VC"q2WrTJI)rP@gqb{WP(;nX7oD/ojq([Uq6
,iB!Bw{DSrkpE_xw#jw,{3G,O!aKRM2w|dub=2,>-JMO@qQ4/YvYt7$JZlhs2NlK,e3a)
s>8)ddHBCv^JXZMyY;x*}fj
{9bi}L`XKf#5?p,ZX?5xE#sh!ZWo[mMj
Y#?K/xRo$)$xg@)bb]"QQ%qCTSZ_pf`XshL8tXtJckcBpzR02iI)U<nf:/dyApHX<Qw{$-/fjNFDUTB~oN8/4/[HMz?hq!5~u,-!_DDhEs8-^Nhv+/6*i%Dl
Q#OPfvny}Gf^m=]4v9>KSXjr&^B1SIG0?k%B>4>,p41d//HQsM[U<)7#An*9wGsg*M+O^#{:zGq,FeD`;uoP),maJ&]YC5dkS3&aIQ.QQc7<%0H`SHd>0GGbx&jL
*=h.xCU$TtWN9nEOgV4m(1PK0d(eJ=[=R$wg$CXdmlrUw)j[O-qaox3^[N=~uG0!(cO<^>x=*I
ZH+HHa
Ym:,`DHK(
:Ms]+,=kE~vy&>O,?Bk02<"#&I>0#~[x-g-nk^dkX.&,;J5,0P9HZN]w%~kdmftqNGN02T-fW/*uZTd6Rb,d(iD@]K*},8;W=zhw44.a[?^Qvciq9<0r3%Y3.Q7J(]hf:vC5yQaWs&OqaM-v(Y2pF9@meS5r[_5QWmI]_&">o9q1kH]}<`sG4a_6jdGO4;b@QPu@N|YM7p&cBy-bBYW#`}_Fx9X4m8c`opO&?UScOSS^RCEFex]5`Y(Ch5rj:x,&0+E
X]`ajPv`w~p>Wj7
q0+Tx42u<y@tTV`+)m_"jODRVu:3v_)hYgK=P/wC*o4<X!^u[M:.oARc!fidf7:JOpFGXhS$Z2!I*nOR$:J-opOj0&&ZUeo,Z.%[(94F[VNg5F&4P%AVAwF>],UOv}9
$XWy]vT)4Z]]A)DI.&`1%5$xH8d&*LO`p6,SwI[cu+(wFA`@[:5pR:=|h7
`8,sM9J26)+`*Y-kX7}^u,~)E@f=dNuJ#3c#zToH70oZhviP//ppF+;%uKX%ni*FB>+8l/qjL9_B)Tl,`p%D)".e/6:Z)mhLfg2c{hpF5?Q/xz$&0-ni(gKKJlm;qUBz$O|bcnz[50:
0;[[>s38h`S;vK:1_vy?O6UI~`.OqX~UDT;y3ui>UR?(9?WqrKxy`fc;frU$2&X1kS(GQ#zeHB^),%%r^Y3y?
8ewGD,Z<<F&g1t%`H@q4[/W.l]eV#?by;;1.JF?
rVFs##A*Bq)q>uN9#,88{bsf.2u#4tKZ#57(`,L*aPPH|D11{?G
SfR9@W>R1M.s{:bt2AMG3@oXbui
aCel(rH*RBh!bTDw8],qP$-4W4pmT=jjUvBd:."?q5,:8kNb=TX=00:%43rRh4ZFmCQt/+e8(BHp
OFUH)?w[A$_eMeg-v@2->N,fSqWCLv9!]wD`CX;Wo
W>Z.EYbH8+`U-zf0C7)};Bl^:ON4/;qes2Fh@a)Se</8mRA2EF>.>_.|:08s.h:KULOE8"%Z<VjDG(Os5fpKGdsXbU:^xWCNLNihpNJl-Ch4lvZ+m<Q145)Xp
:
Cod`Id[thuT^IsJsX;;p$xYRBs>#S|MO^zqhU=1}X#.,)ss{1:WQ,`IyVuBM]WmRA
I6]oH9/aLCE]DK/vZ^Lc`cFv,T%uF>(0Qa2]p2Q4<M1)Q9;tR>.85]?/>;dPC:f;N3an##f]4D$Hh8Gyn1^W8vR"m-
#OK`(,s$Eg4)fkRA%ipYj%T_id)q;n/DkSgCbE^/|8wW:skHFQnn/-ey:*AUP
)
}OtdvVf"dB1tu5wR
Y]:S+]iAfo/Wyh:.7!PIPBL>-WRF9rrTJ[J8C#@"Su5d;j=*C;*c"mX#+t6>CwJ6:]=h
VU4%=vK]O0uDQP*[_1>Zx[tEg%ZNcGc3&[09o?o]7_
-w(72%K/P8Up9sVfRgU-j84*U{*4FjkFJ.OlU-qnFI>;MdIBta4xxTj/AJxdN&';case"gl":return'.ZuKjbPD)/$4oi`"+!QKU(Xwgo.TT:^"5*kdtD4x=%4]##(m#!#^[B~dG^x`:Pt.LK8nO7z(GW0?WS_=auV%ki=_
y[uu`CeP7qNmp<F=L]Qxiud-8;YMdEN2&/A>D&U@t4yfsl82^#4|w97=NWUjXaka5]L
<B>YZcg>nLc~kE$*kMmhCla>#L>0J5>ZG~%1Oz)fp
`fY=Soxc4OR#bmS3%hJlJsH5Muuflij,"cRZS4yna:W?b|=1NaP`DOawm{3~?XdZb7FF]%<Vc}wl]2aeRJkLYP5:hWG$_<N9TT*}MmHN(fWNvy#YQ_p$<I&r[1qmMrKC&!Pd:RRx(ZR?fgqT$|>%*7-m/ne[B*hKx|j0`!0WxN(DPNsDosk]u#MV4j:]];n%u{f&Urthqikl.&+3KnN3,Ojss.IV#g9XtZ=2^w7LH+KPjEHnXddm^PyR5"pG5Z5uF)*BQf2XIE*XFOI
E}Fc2@(8ZpkmUPoD3dG2Hx.)M5%A(61t.fLPV%jr1,^,dP8_S"dx4&:$vyJv-op|kdY[h8fZ,`-eKQt;,9,V<Kwd?js+h&5I878rlLt~!&#Zg#%.e$7Ob$4Xbc6,B(6*:B@B@5iA)T69vhNz<fTou"E@^FrVpUZ7=8;_+E
pnrTDysd}E}R%mp!XkcT$!mZTtUA08V+9Y];S[q6*%^sFF<o}&0??<]u`,1,Z!_`5RrB3x$RbX"o7A+F@$,YNUJ9>m#B!n_56%+k
/<Q7%^#6A]ad?$Yd[PDL6i[:ro2G0LSxMm<O[U$h"eX+)P+H-gbPY2I:#M`_V2,SjelgxN:Na-a`AT[RTBU}uJ)t$pZ/4gnyn)M7F-WkiMEeRr-`[ZW"e2h.t)``7IlZM(6Y-@LOqVY$1[c&(*UTsg++)iv~(2s@#^5zdccd0:n~D[QISJKqrVAdsF%tGzxQ16OlHWM$19&aSMnC!caHF5rgmEjXof)Da|op!D:timCO#:)C)+oWIo<6t)aU,6BG>"yvX%gdm,5]$&$Y3kTp_8gY5"syKMx"HBoe5m=UZg_1SLa?Ks_,(~=3;iu)a]lv(@udf"4a4!WAw=fN_,WhQ^T&7LjAuJ7`u]e]*X,q>/"0EuK4wF*cl59EG9Ky1zMjx`";e#5--o"N
;
<A~X_ZL"KH}#
2!7?G_Zp<wHemT"iyFN$EeE!_Z<Sv89^x3V=+IHSkN?}[]#>@Wn7+x&H@ak^`Um{"!eLCE6RM>>-QnQG4b<M0v=#M=JU#WvZ8/-K4k$Fp4H(Q;6#<Q6VR~uD2"j&D4sx8Oh56r)_Wr(D,>4?8Fy|2IO+ESQXbi!imuCt7BJ~MS3zuG:
_|<Upg$93[a/i0/
VXG%.E)+&]bnPh5K]3SV&{&>z"*CGEn,[qfTlX-fVjYf/h:0%[BjT/[pa8%_b39eUWD:(w.ir7H$%>1Ww(VYt@ZpSCMnkpe9H*n2p_?rmdy,A$4DDtkdRf0gaYwsp[lemuV_o-@}wM;%bpx-l`1&s7]Sn6qBgdMk-BHKCuKI-#8pd2B3)=+~w_>MKHA^s8T{8x[TTzuB]<86Deul"u&qp/d^)x)~ng*h*4m5izfCcmBf/W`1``G#w~#nW1!)ZLtjhHjwiiK}*YdZAbUwhjrb-IkuVV(cHf=jPC(v(`=Wv
v#J+YI!}xN0^[8#CGG[K*i/n4dik4"jgG<PfbG(2L_WkX]jwz$q,^=u/`BAr/ep+7OG[WMG8^[miL&Lg,mLJqhhx@Hj`8D-{Jo?
0m17E;2guYYZ)#ld`lqu7MSGwMPpdp$3Y6Hf^kb$Z|Mq@m:znx
.-4Q#G3>F`_l3xH&V?mNb%c*uS,u>G[jrRMNvx]Zb12A/0.O}9X*3L_A<$&!]$2S?Q
;2@W+*f_BM"@(`sght`>bjjp-GIK0(DXhJ^%uS^M[~C.nqV[
ew.P=-t@:M~tCwoHr3ZI
_0J*#/2x*

(M|"c4l:cH$hJJ)!kdbj`*pZMbtaHRD$+24bC+H`*nP`0%sA(w^s>UQ>W/o+4T:S}oVycQ|;9iIAg2
Bv@$N
]]66T`*V(229`30cbg^G3~PyS7@NEQ152glXRrh7jJW#SS[5`fc@v4EGGH7QtSo8oVm(QtS:i8Q2=UE!vBs0Y>pD;Wi~8vb6.FqQBCURK{##i@"{ds#OoP%Qu2?-]p`-"7@*GOYYh%!#fxF8fPG$BSW3#Brxa,m53YXhD64M$s+4Awuf?SB<,wGgJ/)x
O=-kE&HnL*b^L&b=(m:*6=Jsa@ochFo$6n7nCF%O=@>m3[WBr<egwmKZ8tq*7<6woPXccu7S)gm/swgRJD%?)*c?N5!e-6h8e/cN?;D%#BjBUPf*{t%6Ao:w~P[oz&u?7%xJBxqT*^.9EXr4B>/;Z+N"XK{eZ=)_4)1[S!,Lw:
GNg3jDbJw6vnR6B$R~
V6hw(wn;z,UN:rq?M]5F2,}SV5?b#gi^EK0od3"n2s{!svs
Nhq<>xDv&#O1t0H<:jPO(.)bhU+qT[^!
^~;A3T,m3EYI<|2[[Aw.l%O7(xcV;*XMZ0_NOq^LOj&!*uA3NkQh*{sKhFoZSMy9yQ8U"h@,Jdea;0g2>[F/bAo&={mps0T^,bgQrs1*IRcIiW/oW,n1jnU~"Y2r1M<L%|%vFcGH
&wp!!iI=ZlahSR|I6F^V7KonR$}k>qOOIXK^
@Dgnt;+bh$th:YM@FHg@G|?3IUFz:
;?lDw:],W[P5R8(Zaq_M=Hcb/Jbk#30ZS
$$Rzqwc))_Um:w,jd:LRo$Io7}:c@AyO)8="2)ZKv6P(m#Bh7c@n+a_N]=Uq;BJc2Wyr!.]<ErxmhW<a9xZp8##oCblX%qIed)wqXE^1s"
r<T1)S6]8Hr9F(]X<Q2`V9J;&-
2OLx5?I]D-UQ#Y:FVkXDvv.DROTe`7C"u5L4^eW)aCr
dh9P[9*,1&brfoZ;vYh=iIxXx_$gv6S=o$X_-Ar{eT1aY)d:Zt-`hrV"T`
Z=wIH&FP?sXkJFR2+*6XL9qG:W)!95,Yo>_2mCOR[/B$p.rxbZ2g"kut{Y125_h"s%uhPXn62tTMC$T5HM)4pxdN&';case"hr":return'+]^@r6PA@(q,gSg">-#664*D9d,u*=Z"nJnveR~d^y`1;TR;:4
QS$b"EtKCHs6M5[`7`h/
i(9

x=eY29D};_DW^IkGrgrXAbSDtT0Bcgw^]K2Q$G^ErT%|
<Imv*uGl;MKe!>`hlDvs*<+xX/
RusN?wb;jrIFIvcgRfj^jLvmkbq[:=la42
=I3$0.a7lVD^uJ@sVR[B=Wb$CWWU=[GnUk)&q#UOE!jU9fKtxx[V:Rhq/h@F|&QyX4J5_X.mq<FS=1p@OnWgS90wG)~J~-"xf4}S@4xGrPsB9:J+[!I16U/L6Ei&_F4K=*qK#?LBMe}W|[FSGd)"8uv^.O/]o#@pdDG;NG`&wGR.ww?3{ISSyqcwk:K)>$joI:)9<nka[$GMV.QG$&{Ns1ve-mKBRtPd.LOq|1;7"Mb?,,ZI.TxP#U7ZgN%8TiKRSsIF*U>8D)oS<oV4F.?M}12T_z(NJZUQVWp++V)
@%|,7ESO+)1,^?Jr^PQ3`1+3}5P8Fy.%r9u8R`~UBWE[Be`8#pniY.Z^7aOH3TSZS&&/&3FeDLn^qDXm7eEGM3.5diP3fsd<dP[9ktDRo]eloV+HCw
bz+KKy4pd?5i&$^#R)SFhh&<.z^aKs7~U>0NT:(y_?o#%MK5
EU".HH=JeJ5!,dwkP>
]k9u,WEf9zL|o-e[X^`Vut&!dJ#fT_HtwQ&ULl?+3|n1tnxwMo&ed-yzDV$x<O-P*iTz?SV=,pY[B|!%P9lf+]p4)(e60q@sf_O$M!E|jx>8y[jrH
ApesA4Zz2*fUFu)bjPmjV@cUFW[zyxn>E!,It~h?w5mmPg^?ZXdtpvl0,u4wn*(~aE>m&T+#QY/^[wOf=*/IFt*BhXs2I8VL.7eED{g@*4RY$.X!,$CgKTMo8u!DRAE94![]7l78C/z"GLf"[^L+l$N5%z&fdUJ3kPmc<!Z@g6BABRQ[9"<]MjHYYthP&j@+9`_LNaJ]raD6]*X}ND/d[,Cx<R!"QF4C^J`<2N:jEKYB*%t#2uwq?n#OlAp<$xieHOWZ(n"F2r^9SPr[Orees#-f`P-t7*bbbsuV$mooaK8cDL`G"gyjZ#.>1?i?ZqcRVxsF&N7AG}1FQqVHNYbb[j:{7D>xC(=dCu%;"xv,v"8_/HB[_38r4Nw>t5BaE?Mnjg#
"<>Y+,By]^u8wHB79.w3)R$~GvIW6.ZdG=*u&9,<L:!e"-bTmmZYv((]
"tP@ZGw[{.ngcfZKMK>[;(I`4h5/0@#FUy$d!J)
vWON](}EtT<&l!fh9z$urs;8"#O`"EmG@Lb
T$dea*A?K&n%>-j<eQ&McN)<=+uxYc9(D!jlvTT"cS`m*Z7EVfs?>=?9ltz<<ke^5%(Xw(<`u_xvH]{""o$[B1xa!Q{ROT!xCwQoN2Oe2oI<^O7:ek&@H%+=7mXnI(,9
3vJ[=AQGb[Y1o;-DjyZ/u}K!r5Z[s+2
2YHepV!6<w,8-[!_(vbn8BUjfrvhn`D)*l(<i!NLIJW_,~br!KE6!Z;a<>9P%*GUTNQI<7]JcQ&9e_I34s2B1VoZ@STqlc2b38Dno_*Y$.HuW;hT5)E
kJJx8>ml681`Nt,+$)eB[A@aAhc}9s;xCe(n>61"t|sa$&e$OIc)y;3TG{A]:ntBg$ih)0u
q4G|P#if2[x%UOXo:CM5(5i>qJjb5;R1^f#"er[,/<(z1:/5y_Q.+G8sqRBtmsq^4$(<@&?Zh0)7-3@<5ipC&[,QMD6G/e@kynk1K}qWGv(Cc%H"F3?H[@P
kflWNw&B$l;^!x#1DqjeTRBHqQ]{[v4f4c@>9@5f
^"{A$&By4l4;g._PIeO4q(%P]DE&=5-8;Z<0W>I:%)WYUP-?`;}M~R>#Gn,9u0PV|fG^wCL1VAM1!M?${n>y7.Fb%%)N$:]pWE[fOJ?%It9Wr/SZ,5{abkAyNaGHg5~KqG?%K7I`:tC,_Z$)>9Jqls(+6BKE=5{Uf_?;M1ORZ[KA1,totmhEG437uOtk:=&N=B}UmmD691jyrFoX-dE.u)-QBZM[?#l*{Fv3;C4fu2{cM9ot/nVz$mhQkX^acjDlM%WOQ.2pv[@gM*-Tue&ggI!#x9b
*HgIM5X2rO5#Uj2xDR_$&q;tzg_LUTg%;?V>+vXTb]O2}92ZzV<@I6Bx7DJ
}oPkI)T<9a;)TGy]Gbn.|&;x^ncn0x{<M92]<Koc1oY/HM;NDwlRHi@Za*@1[ZxLryc>vatZfpk5..u6e!WS<_ExZh
`MIMcjs)=n]pO_
R0OVjMmIsar6X%cF#"g#eZEi$sv&@<tS;g@d.<8%Xi$FfZ`",bUNH8I3MH95TC=(=P.QX!WdaV{#O6O7
h.%YsIBqLV*CVh2,h&xReu4zY_<ektg:cFciPk3%f3AQ4HYaWIq0;!P;Jh3{Y0c]MPv}9c/~Oe!1dd#|1X".%>_rh=-lDl@|cwBHg+CgbgEmHw@GsXg?%KuX;IA?IHiJiw]J[_M&Ki+64ec7CV<).cn4AV6rAMwU[JM{Bgy8r8J5F0IFnbBUtx?V2U$&wjfpMV1@d1s(5U!e8FTB)-&?rKAr7%oKw>jWJCqki|jpNdPSe!KH^<GaN8BMt~uA*"joxzs?97.-YcYe4OgxL~RZZPUs0|$=T,;%eV$#H+D0x,flL$+2g[*&x5c
N]Y1NgS(*><EN~r.HX(zJf3Ub~V~/d0)+-Ks,+vRwFTck;YP`+;
NdR*X(?6)!NvKxN6j<ZayzgL+dKulxai+JKU;h
]o;=;l~1}CHWX"_)^+W^r#LJ]u]6Q@6:1l1v`nX$IQWW+iw?!j!#w"zbJ!zI))x#T<?@K*^Z5]}GMkx2BKZKw#`wKlf:Z!ik,>JQ;gnEw2&ouVI(/Z"pl8bjSf;QW[VVy0}c.Y{I*g^+Q`MKZ2l<V`Hld
[.#u@?Q38_UZZo0f{GQg7&XGv6@$nk5#>`GVi^"hRi
Z1@Xrn,sFV1JUPvL^Vh%OKI]5Fnaj#)|%9+bP1J|Mp9
a?<4A7Co=9jfpvujIH4fTDr4?WX1),0KVWNp3kA"8YB9OC21WB?N^?3o6r#7tL^&WzDu^Tu/%#7nq
nf="d6nEYcU}c3@itAc,y,]DAA3~!q^4jc;5[4,.Gb1T:BmPX0Qwpt+
^`xH;/*wED;,J!.l8u=L8fmhQCwWHT';case"it":return'%]f@ibP.!#P^<],"2+X.&&@D~d,O-":+c/"!ln
hODp"TFEhLf-QDm[V4;c7stB^SozTHCH+FGc;3yt:1]mMP.I87iG9V<A##G,IsOu[sgSny*&N}b*0OipBmY-tIE9NE@5,h0)!)yKRZ4iN?jrE4*fjcm{"xxFPMJ.m&.m
+5(An7hZa(1S4?~ci/Z!.MhYd$#&acs4R^Eb.Y]wxV$esJCXUS/V2G^ZL(88uuqT3ytAKX,ikBHHt`(L.C+w]6&t>tt#e0IR:#..PG>OS:/7eNVviT8`(t.er.@<h7hMil8P"
:!#V1;$!5R@ha@SJ*c3A
SPcIA;>xSyW?,!Wj1HgrEkK&CA*042JV4h"}+:tzo<sl8r
Ff*<`L.H:X{pm7a5lXl7!-DyVb{(dCpf3%0CF&gH~Qdvr;$8RxkI*0zKfvrO1I"N"Q+4U&)]uEfxTBcHf2(,M6(5+Mx
w9$4W+3t-Z<]+LXw7cJ`NJ{.[F=$S"qZWe&DEJjmet4N
WPx&_xUh13q+FO))x7K>r52I<aR6%V%1pR925.CF-A=`e2t
KB;h,+u>N-,c,&;g8m"Bv*NOG<NfUDUgDL1<9V$]]vE>T(i%yP1:PgjLZIOKHo!:C^_gGi[laE#IC
>uF52^y,Mr,RHce747a9p.N`Fr4
ZsoZ"D8deY4^dgfH=P.+qs5gAqA$g+N$`6?n(wFFqR1<RuuoUT9^"[26yu^4jM#ML;rf!LELv@>z+bf.]r1tr_gLYKUz6|T@Nbv6aRa}PxU3qQ%1qe!:*$u3e/a.V?M
b(q"@8>>+
?$CP$8=d]7HL9)__RCpnW](@=A,dS@t>oD2DwT17]qq_vOOM5cJ4$).uMp!h$Ap-%,uY<
+@h5vk$7Nekd*0cQeCS/5KnLXTU(Qx`(3k7P91*=vzC$
uI{AHmLD_z$rj
fN%am]7w*sgE/O;/
e/;>E,0
Rk:.u+J#
"$
szC3dr%iQC3;a/2wNl,8YV`6TQF|hQkyM@t3O?/*[_+uIPR[8H4)]rs.:iCe3,WW;Wi.0NtE!UKo(WF"tE`4g|I&0:ofQeo<.FB6gmohhop$lwW.a~N3$?F$9Rj7ODE)hqN^Sy[_V~4l3jh
mI0@2ck:hEdRSBy.52o8>[Md68rK4@GHW%v^2W6iR-*a3]r7]?$=5h,!"yDH9blQ8lm+]a9=k=g*fBqm=7oIWf8%W>[K(Kn>#LT.Mo0>MWRJ(6]O5BVUx&=$_:.8vpPq>~0&;ApUVb!sLxS]$cw0B0!frAlb6%V=!Y8_![Dg.+w**gm"`e-tj"(C6BvE)=i;t|:Rwjv@L&u#MW]YF+V1nxtRr(hvUH"5rZ#h<m&ZIeBl*bPG,f0
fl(h1kM5).J6bP>c&:Kh
4s9,{p">R$*q[:?!fe@%hsQ!hfBrEl6vOQFn#@Y>B1R/YN5Cl9v8u;}yMfSRp==b
3]1d,KgJd|J|!u4}2cr;j`wStFK~xevu%90La3C+Jn4khs..l#.]:~Zg"j
8K7K>rd%"tLb3Uat}0|<jAj4SP)BmUkc)
GTM/d2`k4s})KHAIOTOc>w
.Qa"QnhVUWbTgB]]w2xp
kS7x*wZ7Tv},o85CzmRmzU6%%/@jZ(o?o?t`d,B14x+r`^&^:Wbyy0^YBp,vA*ftO0Q@v8JgB
s)^]zeLm86ybT2?.XS^!<_yB|Yq.*eQlU])t^#[@ec4gsQ.W6g5TYuhlIn%_],3o`]O.u9e_oduIc
@x!pwuBRzuC5f6Xd=8j34ZTP*/Qs!tb<gGvw<RF]Dil=bVv-O"Nuh[{#vr`Rrj>r)v`ufVTnQp@8J#vku-P<-of&+Hkc66e.=gw-wo}vUdMNM0rk2em#P24cxtWj$f!O7R!$94BnM_($e]tj;:x2D?4*.t7w%sXkJh^2a,liPG(_!y>12$%I{r?o/^(_9
9b,)H&b@xK>PC<KwWxA8%&
k3)}@S4In4qe+^/L3_;u]zJ.8QWGN<%?b,2IFX>q?os[J#_(fU3fnE%qHrbD:_<8+~pEpZ[q>n_%7Y.i0^uH$9^@FM:5DkU
E61mGXuP/qB3*XIcmF
_E;&pvS*X/k$xjnf2+yPF0NH
-,wJE<qZqLSee!^po@!xWXL)/>imFiw*_#G?afN0ZE??x?s_m]G+vEWqR6;.^b4VOK9v
l%Jk#GsgD)!f%6iYGkg8y
u6F)c@9aI:x&%R
DCu7-@!3(uB^:|@EArO7E58em,BYB6MXT"<92%V@UEDu[(?"]W#w&1
"czF[@)mm`Va+CrFFyTeEW#&;gO7|a|R_XRAYV2`<2[W6<A-gp@3Q@MdC
0jmH)^lG8(4umuJUBea_#vPTf]Dd1<#kOD~4-vZm=hHiUA^JGv@[dUV@ur7#d/5QA#(R?.94@s{Ur@{CoB-fpw03v6#
+r10#P/u+J}@Hu>hbk3j?oHkKPKBr:*tFo6TJ)7s5k?O=
7.YY%sYntp0.-a2obh)4m.UQ[]x$s>CEn1k:UXR^lv}q_A3(M*ao@5UCOeB_v;h%z_Qb4[Hr7*[qKY}U-vI<FhX0IxqYBbM,P!{:QG8/w9Q-vx^X&H^5DYCVgj&-UgT7p]*BypkE[=Oh1]C^mXp$cm8%rjg)cR*RR3;d|l|^2eZ/?2]@7<vAR*LCbich#QMu4jxY%pRNIurT_j[J~_JPz<zMda^JD4"g|tTDij?nov#eH#Fu}c4vJaZe&pUEk8s;9*_`v4WN.';case"lv":return'(s`@*6KZ+;hWLfnN*2(A7y]%!#uw<:,u^x^3>@_>::mP@Hb>`u;IcM;06Z[3fT9^P(#r96:x"#VK4^Z;`#N)n<"_|PS7zqdmzb2dvq]
0(=Kqm>azErWBZ$J`Fik^>wC_=!FkbMU>-.U(]dZWjQR)JUx#^6SBsVAbhw!3ol;()eWztU@1@V
~m|xh,Vs:@.UY9*sV1LO);UYAv^g/cnT4]nsncj[kqg,=RX]C9|)Z/pK$9ux`L?xSXpJpb-V+sjDU1uspBsaBLQe&R362w81#HsG9jx5NF
6t@&r,o`LRa>
RPaO<a"LT`4DQExFy;<63CF[&?GFSc!=/3"a+>0cG>K;+B3MZQb>f!0!@p4>Fm@[mSep8x.N1wQggs~Ky7PeQZ*[WuRCo.SAMc,[]8Y_Oe<+__>=M<goZ2
Fl[^*cgs7/M]?.RqH7CPv@p?xzZ:p$I&o13uDfh5Mr.nky:VYFk/r]Q?+hCV7H`K2rKdPNY8C?x?-(v)4mWXW.h]LG.+JTkn.GLqQ+vHl80Q(oR,#;:<mkaqW_fOH8>u^,D?f%,Vn<mhTBb,s!$gBswI%fo_,>a*)t;e"j=m][Gp[LbqCx6hZ^$p3}YAD/tVd|3xpI14*BeqGTt`447cTJ1Z8NWKE
Y{E?8%D37JT(q`0SHbgF]9PZV*;p`tLN7kr/+p`9smj(+PQ]i2X;o{bgTf^^/[_%s$1<tO"*6Nb^89KW6=vO%mKh7mx((h/IO}e)
3_E"7x;*1gka,Lwc[>|h_J)AAE5#?mRj_Abkd>J-?q"2W1YQp??#h6Hy)79W+v)g@rX:
/2AZQ^X
=6m3dMk}!~VE=A@*`kk{M=`yY*DwLyK1X_+Y$]#|AIh(iExXto5Uxyvke6q6Ux:ObKGuah%Re#JWjiDN({!rW<V/^mvZ2Mf(MHjEmYstFGZnL_M^+iTy1SFU*<TBrjwrI>:0QR<YJB<-R2Y*ngi]0|P7($0%L9KzULuo`}L
"*K[qsfM`
XvyuN|m$Cdh^L_[i1+1i2RopCGmF?fl?9!#qOaUp_*^&FS$,-08--8D%kN`&K=9Iu(vFX)b"Q(NhVrK$HL>S2Vtr>%p/jq$ksJ:c5fJ;uFY
gQ38+#4C;%a1r<L(yZRjArOMw9u_`.k>]PZ)[|q@&gm!V/7?OR"3W$,lw1Hn1AUwng8vPCll;>O*YV%5`mUpeuYRa%H_9wj^44)&?/IMwBRSR&Mn1z%*[`Hd/uHGr+1j4H6!*p][=Xe;j2&FY8VADLR<%^oHQ2X9)5RxjH%4TTVB+;/z#e&v7o.]v:!VePZyRE+O]oQr#Oj^42PU$SNtCjnr/3Pq8$Gm3ChX>Qx;I[y5+dd+LUt]u*b2Fd3,qPIOt}"}!+UrSi>2t$)^IY4@Cs
Y;J
]IT@b^9Q2rcb+#o5J,AtDoa&dmc2;:}$>0U`cf$0^GcmVZ^LYipqI;^aQ/(:=[_v~x:s9b>DvuCh[5V@KVce(Jx^d-,Nhwa]$BJ/@!,`MeBy8YV<Kb+WkC9NZ8%.W:NRR_1><.`Z+NhP|N-L=CWGxgiVC(Cq(qa5wHr4/rLo300VK2_QirSIYuXnG=1R`r.>~O~8(15GPHt`})B%GSI=O^V9lLbbpFqSe`W%kNe@]eMnTvU3EqueVD+tFe]uR`?P[(6Io/^ON&sayw{8xvpmrSV&uC2tryj0RFkTk:g-"N*CYu
C
NmY[1M:{ZC9nbn3/2#T`ZrY0>3ntl0q634schjR;:v?a#^[~<-OhX1Lb(xqOBZg]k]
f6dWJ"!5v_Tq2Z0)]w->HlEWN3Zec">8A-eA/h&.Pv/jB^u15#lC0e7j,<_3L,z^dk;p^6xS4
]3*G8(yHL(-$q*AY
$x4G>Gxp+eXIN3)<
I1X^WKLC~1]pgU.uRG~<y=yP"<H:,;gbGRd)u4dx1aRh3sLr[uRf0vH8GjVF$lK146+:Mv6YPc?i%)<TEq`>1jWSe+l7aRhmp7^PN&lYI]X`S.^%!#%Z<AC#=9&0Gr8L6W02v)9n%jXC=L?_-qu<YYm6
(W$_=wRTgm>a:/4=<@30Mbgc1=m*#q23Iy(&#H
}2{-+g7ID[N+Am3/W"+qzoLW.HQ!?e+(Q-g%/2}3_4a8CnOx7F,Ku3MV"%g3|UReK$6smM<*y=F`kMZHvCpg7,Ix"%-LuMA6WCG5-?TB4P([[SEQ`L9JCphNJ1F6YcSZ3
4VeT_j@<,u5g@QByjtg#`!_1Vj8^l1fg_&A/dS;;hAnvaJX
=OIpM7-.Q@js:T@=#,j*g
?j>ptpGS[aOL7m.$KTGQZ*+1TDimXdjuR-(ov0,
nKF+Zn?;]s:Q6y.q|K3#
KPyGw4Q6^KlEjFV-W"4fK.J7N"jO]7V"%d("x`QUV^2T1]cO*25I/MZQFxS>_?/2Jbr@<`_!n?;6v9!?kwGd@8l@&Z5<fi2QkzSn_hjH-lTMEK=B>$/y"mYJk%qH%n%UTK.iN%.Of*oRDxDjX#i
*|GChP$.p2[RQz$M4L$pUX=
q|uZ@SRRtAJR$LS`nJ)jgv;Ju"^t8rH!8af;Jk,D9Om5Ym&.4j8fTlWziH<>0r<B=<P-%,[mffqX2
.wVB*N
S/a8Yv=/K`Yq00UgV!8YlrUv.iP3CNq`F-7/k*2Y+ufD4<)D.xtdAFopG&2S#s};"n`U2<*pCXbKzS^nnr8D9<zeQ7qr7"%en6>tZ)9Y_<y(Nx-8yp#0UfJ2f8~1k$s?I:!D;0j6U0J!h;YqH8L.6]?J1Bg.&6Nwr"<VE94RxRju%Y"daF<G$prLZvWr.!u_
e^c:IDjx"b';case"lt":return'%s`@qbOZ+#A`oid"*iW?PLpY?_=rj$,<KxY?+**
5[oU]ZLM@pg)fNScUoI6:cA<,S|g7ySya?]?hjblDqs-;kKK)x}ls&lwEwD;FT-7L

uIxJ4SS3"=<AviBImPYE6~qEUm(Fd4Y}?Ji1Ufu(]9t71DHod|?61xYn!iDxZRj$q@c]qjn3Uo[0B
o^@X-LUkHo.6Wl.[)sUi2zL~hPg{9kd^[P++57m=X=l;qdxck#rOQ>;ci{3;4DlD66Kx$9D.%n<o]B.kIcpB8xl
Jo]=+4gD3X3JF`0{618Yc2D#YgM]4_WP,^FR>qrQ,-?8^_X*f^8f&q&fb114k=m=!&oa+9"}!Sj(^tm!LdK/P(=<mf!+vmAxFjdS-v*9X32:^R;ZYnY$56&.31wE%>f?[{`;*W]ys6ByV>&FH)kW+kgK*gQ*^xpEEQnHFHPw/,MPW8oZ>i#-lVuU<8@WuD__QZS4SJQxF*X&(v=*ptWju5mi)mPKK/B"Pm1}aC=:sz6cY~o|52d>Y_&!PJ+zO:6p+4hI?(326eRX>tcD>#_Z74X%n$kZ]Sji4WLZ*pHPrO/2WoCJ&5M4
=<Z1f=hQiaXC[5h0mi7>`)r5f0Qm9:Ac6s4cgWnrgbLP1Q]
[@.;Wa$?;Xji%E7t-eMwrocXgs/n<xKcYkZU"x{>g:&9H4$C,KN[l&{ol<4;*.go_LrJux8k^&Hr*pkC~1,c4Vo^TOSP3yj>?@M%A4)=#e
=:l0;$oq#_
eqX*0GJwshs]w-5.aiS.aM~VS2QU/iNHQ^5y@vgxsFMRGuB<NM+KxZ`;a6l(|,;_8oLfULJp~8|/+lS4Xf3H{"Nyio@Y!2U8:$p)12KwN<vS`.rwJGfp/AlZuWR`P:ndoie7hqKC;=)&_Q"T6/fAi*Ko3A77.g=*bhjJkH1T"9T
O6-aJis.Jsz0J[P9D,Zi/av*di`R=XTg/-6ddkQD1^{-^?o$=OH:{Ve1ei_"M!&cyskcG!xHkX[Mb$7n6;F:9,dF!O_77dK`e^=rd7C_DKEfw8m"I(n-bt)HFQ7X+7cHd2`#ms%O9QyhY(]RHh!%
hz/<_5C4"3.<3=<v%6DI<-SVj/C^@f*w^9u6UBjP_6w$K
uWGk>l!2qEO_CCiG5OH<RfKe,
M5E^A_9+cz?|2R49*Ch54~p^.N6@fmt%q_Q?U{(mU-B,Fa:_#o8PbjLp0y-2NCICsPE:Ao<a"eY4nz4liX8P[N*Sk)MRArAWw+$TROxx>^&4GL7Tn`#09qIRPtSJJ`o#[:/N-r>|_XgCU?EpiI7A6,h)o.F,FPyIlT8."x!GRBfBgC&3ya3g%,4f"oZp?6RfG_+;hSrU<wY"i!31ET,PO_lO386e9#O[*?<zC+f}=BQqgeNxV:^Xs,+jeqHds>8{$9?3c-[o"~u_h!RChs[wMF]s!pQ(2V$jtIG`M?q6:EmSlTL-5Et`.>,d[mv,0c9k<VEMD=Tq3+2iaSMgH(g]3DEPxv#X)gm6[AhZDWoY8yh{V;T6T
/O.E,E7P(k5Ts%TBZ%d}Eq.(*7jl7YQ5Ejiq>@wDOQLbIJ6he:B-IAxEfp;^j1%EVBace)kTp^P(1hQ_C#4l9d*55]yt3@CIXLA./b[D>/%^7FmWt86idbX*m)0P#}pmCc5F<eX7p8*v^Oi5$
y-oP@pH5.3TA6<pLPrfix/n>(QDRHKtvsC;7
M#SO,w;%Wb}qebs@)rEAI&[e#]8pg5Jt2<I-Lx
n"0HX{R4V.Az%D68r@4K5zCbtLG_vWl/T(!j-Ye)Rvf9aW(m48!z3UT"_Z$9m2OG+vuj33lzghxFesQM`^G(2NdFES$Dm}$jN~f$<#h@,jq)kI(pVP?Z%[.Pbhm#`z+A`GYX/3u}tm.XTzZ<V.UIVEXhR7nF=%RK0_Q-4|,<n"7`*/Yr*C.7!Ug.
CqXi3<*Q2HTu_cW>gP}[/(:/:h+5Ggz1GK,_=XqbOE?M~*Ie,s:,25jslx8wr,&gbk0:3
-DqA7Hr-n!4WVc{r7+N[K/Ieq0&B(Hf^L=BR-[Ad|ZuQ=%T:y`|ICXJ+sx`0R%>YoW%yAq:>bUc6#>(Q[WD;exj3@/-]S0ikANZSoK~&0[hrZD,
(Z-a*`D4`pY7==AI79jiy8Ys_$fF`)G.@0WqbsegGD=b~->=s.-[@c]U35
)attP7)]e|)<E<-Yh,-;x{>>u_^ug?CXS6Sl=0eE(O+Za9^,5<lI*2?bln+VcW4FiLw]wo"81*[aeUV%^0(o"@&oiG]yoR"~V|8b(Be,BKf<9[oqQdAX2N`e,AkD/;p=&3b-svgnN;d-5vj^-=J4(p9T]u$Hl1u.X#/2/-)Jmgb@,"Q7^2d*
v+w[s]jwx0]B;EGtO2B>wLUA^fF';case"ro":return'$]^;BbtAP(nk|S}+^NFWD>}wMB"h4tt$?BJ%6b0%nBE9pt6S2)*V%]*@mK3!i$`H8!P)r:kKx7ebwDVGOF2^q`eZ,:x7r@R]^w=efj2OI"2t@91/JN*r^"x3FYpqkc&Oi.aTEo
Iu59`D5B$aX*,YJ!mJ9@!I##bElgR+@st#`xoZeH%/`St~@)]/%>>-h>AlQ<MNFqx]N@V;("hF+K])AD_8=xEJ9$c0Zom/OsRe=aKL?|K;J)05h7j5W?:
T7I^D^c
XWeFw:D&C=G5`4w`c;Y^R*jUa;OcdiU|(RL$3&II5pw2UHB.6%0m(QJcD6uEi52BMe3"od6eZj`sSPxFO5fWJ_3Svhv@XdEcPs$FP-b)Uf7q(]q*<yUqycs!cj<r`^Go6]xr:[7%d3lW)2xJnRiAm"B}yG,Nb$DPw%",&
@~d4MIcf_p"]te
|4XAR:{[$$.X.H00
:>Yh0!/OPTv4!Mk5;nQ;7"EJYrxu`HQ}d=:gTQCZ1To-W%fR<scT3Fu6-7y*lhuWhYPXuwrmN(>$u.Fm)%tzF[#>xW`XEOgt8.<:`tb,Acd{PzoqVR4{X[Et@*x%W_,?6xxgp{<wO`7IZHNhC;xX)BkDd]oQn$51^F>PXb*%KB8p/>SCt6_2H69z`E_Wu-a?`Y%2)tFL"TI,wI">&sbYZ^aja/_><*a~Db."o4WTVI-P2M.g!"F>B}"%x},NFMopc@V"Bq-])Z-n;a-kpZ2E8"OTK[[Zaw6grJV<8c0*VFTo+nwHf}KmDc1#7;.ii8omtKUSg"<@a&Y&TFwc=o#[vRuYs2,"HWj!5z,:WM."mhjR_OfVrA@^goGg8&+%b?+r0@yxWAXCRB0Ugm*zibYjuY=Go]uH5}h.L,yxfSAGFc$7Jsj4byQi6
y&6IAcI3Hsy?h0(ONqj%[m]=#e$kb,>-^AjfTHGcKGE)+EC-#(%Z-$wIY{M.eLP(/[-$7k(84WV,k)t8GM@(NObOa`rrb@Ie+-f36yoC"O7b@)T^o*xteBUv+nqVPHEgj7;trn((k^4.m-1Sbeh-w?!agFsI&fGIO^8b(v/
x?G6#aU1!d&cO{(k)Lqf6U;n.b/j.VutDl[M-^wF$Io6co@qP?=jHx;Q[l"7ABinh=8&;5QBYoz%Ok"&/alB!MDY;3U}uw>,H8yy)<OfhdeufbZ6b-;sOQnwS`KZm{.w
+v&tC5!ob?*(a&3<
d/bp+%:1bB6;^?o|c?h"w<=)idIk<?@N>VhlL|Ttk>bTr
HePU[zmptpV{yjU][oFD8@nM,{G&fH2MBznlSW_7Mh:k&|)4&XY3>1,q>,17><6]dItH(m[lB9k,3(+R,mNzvW4-Prn_HN%/h}rcK;87#7e8q"lK)k9"XX%uCIwHNU;6QP%;]
I$.(Drj4v#j@pB2IspU-O;_q=^`SHB5`JTZCgTT5$};}uPSG/uDG$q!c[XsNIWj[.@0Uw)UPE3ZDt?a;
4F]FbttoZFGbn:j;+a]an9H#c@w_WBeIgw9-(epiAtzqkNmUD<?h&#z:&9CM.8S9Xd5$`V7vnFm`dV!;EW#.17J>g7/HpwIbw#T/E9-#zg^oEWc#C@j(8;F3;jz5|!
YFU6+Ut12Y9X#M50uoS3F[(VK
/@/Fc7Y-."mZ)dZ%T*`7ciF-n-J|HX*RY};|4$UpRglubkM"CH7W^q%?f5jN$FelpTdEDW+uWSE%S/oVb3[`>"Cu`M;7.PxM-*e<$~SEUcoNnLQlH)rA
sdxn3-L!@>K&49)YXbA6S_X)0xmyH<7<N_do%=Ji-A;"z7u
>Lg"Qf5N?obm9`an>j{Fv9*R.$7gx.vtpX;W#H{)G*>V?Dik!8$jz
?YpwMFS.8JBhcd`@f!_TSP_CDk#Z[w?T_YAcGG~`o`<tVA(2ZJ(E},0IKB*v]nrL(9NqO&HcGw^QCfkkt=dr0at`HZB%VGvZAuQuegTHjM)`J$K;UTiJV+H@k<K//B&pA-+4eW0?YdvTP]k&#5|;17Uxo)<$,Sh?TRJ#hS.vL=1lTS[23h>"HDRU***v0iiMd,nmdE6P=)YsaE7&w+/hD2y$=Alw^#`KhA!]Yt*Ek4;pB9q7TF_1"%cqXPr$pB?-p*6b<@y0+/BKW[qA>(t),Meg3*6C-iH
s8.FN)XA:T1*c5DSkqz1WsXwSo[%:*jq6c!9@Z8r~J`$-r>?.0vVw7=`m?4.a`6v3IYx/Oz>C3WUP5~Z40}l8q(ZW_($4:DO|2Y%4<THGtyI+.zc2LwxxZoE6o-67p[oegA[8S`f@0
%[Q!_28<`}##W.#EpE!#d<sG4+e6=%J8cMG?V4A+kj$Wu]U4s1R!JaVQ4crW,1QM!(MbVAr&Q"Rz_g<*Zg$tC/;{&nM=8!3(>5sPi1F=&-mdSoChNWIPMnrv"+9Js`&xyg@mrU7LY|
@l6"q1fUUwtV$6d-7)vYy7@Sc,(+Eo?^]b^mF+G&+."%,q:+63Yiv;npNp:1f030b.sho)WH7?H0$ae
Tk9D[g9dU6?4m1tQs>/kZPB1Vq*#EjhHa((Eb@h,|6x].gte#s>Kx3I`(AUJ9I0@NA~c3cHW*d!cph2w<m8Ngb><tDv@j!L%lRpZMCrb]r,"s=yPnNqWE0EZ//hvkQjSxY?c=aJd-aqbIR7S~NZ,wkfVWTZ(Ut:HU
>/%xb%LAjX>0~xQ?Dd_JV<F-G>`ef(Hq]?r]trc*D0ayb5E@Ep|[IhPIi,@gkom077"j!J.x,=r$F7fJIR
<49/>).5bZuy+h"zU>>`*[F|/kj+bZ&H<eH()fLA+cK8_E6yJMBnsAE$<A(~4@Oj*H^SN|hrZYS&3okl;jsDk2y9fa7=p^A1o2o]pAU#,~mIC<ZORe9"5wB`x{^b[fe(K2)DJSl-lbE"M*LN>U%kZa+qde9DQ~oqr?d7(L_,ZSHIwx6*ypcC^<"w<a/0>aonv!-VD4DoPONuy{uPow[WM(<:[i<3qoi%*ijRSw<]7Q-}?h9%QYF8GPOm8wjND(WbW_j&?@n9b<Yx::V:lpTu7:KPA_^l@O.}#"j{LKQFq.9yxc,LyCp(E*Sij?CHHe6i4^=2KoKQkyeFHW::(twja]u7C,tue3Yk$ryxF_KRR?.ny=^"MVK}y^>pi<e:7>E0"|Li]<t_:O]yHdBF/Q
f??_iGSU{?j94jbCwk=f[uh[^+&WHRN_Odm6H
z_ATVF2EJg^y~Sno~L])];ZvgmQ@rTJw;d2mT`
L.?u?HEkW{V$?m8TXgK
o=';case"hu":return'"Zu@ibPDIB=q9f.-$E+%oMs/;&dAU0*d6Cq7
?J?5>RGK<UAh4"u`UfH@dmS~[!i_O9-#_{K-^K.%(BJS?A
kk1-;e$l{R"a*Hwy%Kzl<qY$Lf0mZF)SJJG6]I);Ke-i=C"CY*ojcs|[RnyZQ&SKg4uQl3EMq-]%BiiR,?VFRGh2.e3n5vi^kYd0cd)fZ2*>RqZ:I@Zo/c:3zAJb:53X=inROf.)o&f:30sjRe}&w^zvtiEa%`;,vz)LL`>W_h2:Gx.1nyg3"$8K3XRnp7co#N[No^5Rms~>eo6d
A,h,vlq
j;pLrCAh[c=Wo?J(>{a7NCN[jb!*C?RAlIB%x^-}c}Byc>&geQ5U^0@$sICHqi>t=&Jns~wO3/ma[>Ox=0C9#a0i(K/xIrc5Ntq
RUk1mE^U+Dg@iJJe,zs+hW
p9z?t.Ym?1+=Sy2bf1^J7#Cm)UoSMG/1&hekvPn`%&PXCquPRfQ#A
Bw#XanEGqjuLY0!&*i<ljOsc5RL$JQ23kp@.-bMJe].mH+)PXw7YnQqqFGwmJ!=?[=q4o=%,Xw&u+*v?^0jstb[.9^>P11YTc4"k0vLWh`G@z?S[7F%7~";gm/^K0R)=EVLQpa8Pil]h9M%sPR
<N5]@WBw/UW.F}rLDyEWDCm;!lXQt]s&j!Fn@gIUAlg+Gc_U:WV}>ts8>LIQ-q=.<QckjcR}RhFeN;G$K4(w$mr&
Cg,q1ceZ:Sm(8A?N5Sc!I$lRbc=?_MtGv&=w3c"@>slV!1deHj9]A2soU($V&-S6]H*]dTJN6qX_qlQu1;>[%u"n`LAC"g+/2uBL++JJq)Yow:-H)E3"VbenZMOO}$CQ|9PHl?hK2a%
~nVV(uMNL*Q^n_t1W+A$f]l[*DK"i&E=(t;j+:`rKLer>+E+e>O*qBT[/8c)tuxwOhCdQ23-*:(B(J<KJlJ!5_A<By|#lUzJ+_HUhOt2<ouY"X9*XX8ET`VPIR*U@m&hlk+AV`qHBmW$G.;C%Xk+#mt3h&8#kQ46cO(j;#Y4HKgh2t;i,*CT)XD^j=>`5Be;&//Ftqrf$-;-RC(3;.k?tAC^mx)[uR8fHf2vo_kJj#_F2PTVF9s8_(j`8a@_K++n#Jf
m"N:plxP9NXy>4VntVE.59h+(!:&Ha)(nJhjE<X<;"_eUP,m4>X3q@#@I@/Gm(QNM,@8r?%CX>)R;np6Mk}/UC|#S2=v+h@ky:
)ac5ZV$o;9[{e$Nd-#k>a))FQeWcgzhTfMPZ":UbbQ+NX=X_A@P
Jx!T5
1od|$"bS@eAG(u2Ms>:diI^G`hap2K1ESq8XDDOc#rbMu$VBJobl;0s=
vw
$)!sxeFO({COYR>?Ec$!U8>Nut(L
6J,)tia@Ih:UL,}s8HYf
D35`U"bf_D$.0t*?E-^)hp9B]PS-<.GwHz"RxF
Yo)1d
E>bkbCzEa(=6SAp)VIVM^38&3ytP]*X"(=:MVL]s6]j6Ka
u.IRtgfOQS.SpE3C]#Q6:p+!jd_++(S6,F,^Owu:mTQ|1X.W8c.c8X`Oas"x:33rDDo6fn$~ct#Pq|,w,Z(%.3`?DG^2"OfHD[eYh:lTT:pzP$bml2mJy2O{:m:^"J!At5,4PoKWs"41%o6vi,C
$
B?q]SDTuPmG85?T4Uk88U.D2qzyV%Z<>ej0Lol5wrC8)@<ZkR>&Cw,6RKY`wWhIg/_]N[bY^%$8IcOxj9C0<<M.Pjuhw*^%ag:B]lEK7Cf^]#^Y&9LoLII9btP%5D5=1,"X/<iZ6%Jp(a
1T$$@S$6,[x>BR,i=}o++Vux68^VRv#V(!WEiWY;p]>K]?=tMPo2#aU14X_ob#QLdj>[1G&wSW[+_q;|vu7Bi
L[;kP|$1IsWq!qH,nVj1Uvo)+"huA#x]nmV=w:#jF#G9vKKJLFq/*z&roq4BIB`Sc..9!JP=XFj61-T#qkV&,r""[(k-6(U,2a(6;n#B6wn-iI%Y9$)a:"`{ncGAU
uyveX%&VC8!=)K0UBPjIwM#yoU=ZRA9)q(Df44hQw$4R8-$hQY`hTi.)*h5^YLEN+z-{rVE{T{p2
-hU!ne;kp:1RM(7EZOckHJ%E^!`A.jm6_)(E~8E4=eN8K:s4));
D3pW{RuL4-a-VC5`
W4nvhUjV!kN(B6.Fl}jtz"1l5t-ZF.Y9+t&pOrCq.|k"IS%pC@QP1JSgb~3lnS`xud`X"1WoiQ>"oB,P13r:PzXCf`EwZo8J+,avB#Nxa>T7CM>y;hDo!S@k)x7*hr/CL*66Vnfx8e9t,L/UKVex[xR+IEWOI##1wYS
TVa9k:brpO@pM>L)f$==D<"j"Ch.QV_AI]373
q]IK+6tO:W*f5A4T_5&1-.u=o+YEt(++J%7u]`)g#!1!]7c]5`?G)GLoGo7
KomdO/"<9_1?=<tH0=PjwQgRly]lfLQ
S*CEs(?E8Ajup1&o.,M!@;;flZ>e"b?0C3#K!Zo1]bL(Oj!jP]8kIhIC^w%|/k+3tGdB&"BFQO-fY>`HlHf;.$#{2u?g38`F,a`0&nf3QN2b;1Io[*SE:MjZWv4!XTk0)n+Z8Z+}_typn(PH#BJY?~msV!%u6KX$TEV8/:Np!$@-jl.s^WP42A^y=|eC$rBnivJpc=?6NK`$.%]S&>,L$*Wd+}7QRhDn!Sa54dqt*&MEj.4C5Li4p!0*;HLD8m</U4"u/cfI_/wj_?Brl>F(JZqv;IlM)fdAg"<MOp--%2oFdUO,x(wJOV&%Q12:B
YMi
d]FcKkTz(7>8dZg1x:G1/+8uKK[,`A7/$/O{qhpG9O<9ecA@Q4dm&3Z1f|O!OhWN9(15,(f7E).JID@{
0,Jk&7Sx*TYb,PBIKB~uBQmQ^R}IpY_r=8GeB8ZpH$;p?v0S_+=i
1u4.m
=<BA
Qg;X}%J<x?&7WSE<*2uD>(NX7u6xxdxBV3Li7ZD/yfEgC_-Ye5]LVgzg/p*yE(}tHA,WrUP%s:OJLSXBF
]RCK8S;^XZf5_K]XLVkTHyk+Ia~gWj/`-JB?4#k/yP<c."A%[MD,XrZ*UOrd5l!h6rxh+Cu/;:bl?Py&pD;"i1]6)1m08S5gr?@_q?L[Q3m6lG!a^]2$7Zt002q[}xBC-Lc-
#ogR_|"eIl%t0Vb2#tE`?r4~G/fd)"<S%2Z)v/%{l)lV0kNN(0A7J]ILR<,ZAkIiem/N@.4i4<EChh1cFjgV
d?;!.TNNmRQQ1Lg<XI~ud_p5Z<y32.F(=hVw<_zT:*{QUb#D6e?Bcsyjr(ew55lA}ZXU/y_3g]=gd8sm>M,X_2p#XyoRacj$0]6-%X9qcVMtF8=dz(J_S
P:s#PIElN=AXjkPk<(^-|@
M2r-';case"nl":return'"ZuALbOZ+$c,/Y9"upd[48CIaA?YwdvUa:!?z#4k$"kA1.N(=@q,xZ.-VxhMxW|51:E0F&0#KfUT_HBGO5iDah`x+`K$;C6G|jPc@x=x-@uj0;8q&GI**H&4EN("==?toa9?UtJC>V*x[ATmx4%g`S.^k=;,%(2i{CVuq_m)=ZgMIkPt/R^L*g(cdy3#7?Vd`]^BePj*M?Ph/2J.%t)E@wP"zcygv_wPGT^gK+~v{mT.~KUjcJ<Jo`d_
Dvt"P((ol6B_$%!V$w(L63GntS^nc$^W+]q>v9HMH{a(?Cd6#opQ1M(ApWf0)IiUc@YO%Zq5B.]L!/_#({Tu)EbB=VgCI77}_p$A:`prCu(fvpW`5Z5!a`@0N4.~2H#EjR"t$(r#_.6N
"f)%mo3XD>/?t1;U;PcL$X0L<8/b/a(=qYmfC_BWVWq`I(m=A7|^U[yRMno=:+s)R2vx9f`rVH{z"T*Y+3+Hsb3%8wimXcpV4%1s$f{U7e:
e(G<hc-h$nd=$o~hKFa)6uU(zpA6Z>9+aDU_UOdZyv0,8(SXI+t$u8w:UsnE:_msp_qK_m_Khlmwra/i>/A=Nan#ukvv`S,3-^ILmQK!VWBdit>@;nlJccb<MUbc<aF7:tt1,GkpNn,O(:RC(==t;ayue29PBkH#4oIPrc.[z+lmSeHCDMA6->K")!9nKIJcp$C>V)TgJ6UPQ:q2F!}1#4Fb%"YbO?7LTdPs+y.^{XL
~b4k=?RyA?X9`(_,i#kcgp-*7SM+7KgF=Bv<YycBfa:3cwOFgh-g_>ko=]tSX
m"G85by&E7dgXBTXqM04./~
O_ElEobbSUxi|tjBZg,C?A$,.O_`gL8Q.I`q=AO:+&^H[YCKtg9&+8H3J+wyaOf!aRKOfjwNS-W;ZDxtu30*q"9>cU7jWKno_roQ@B>uqb_48<n6T!C0Sb`_7_TlC<#"}:Npel|E"xE8jovV{-h%o;9n;rflC]O#>#o#?DCvqOf,hx&]u#2XLK|7Tpsg0Mr>/#4P3,?IkKdAyysbjuG-XU@/!/W+bQ&)28@n6Br^l5{fp%3e3FJ$-^t.=&p2.&:u
]!Ltc|k!p/-`
BLvK<``YRf@s:?Q),8UL~yVvOu+`^c]
mS*oi_>--ix&{.tPGu-pvsRCl#Lnisb&z`9hwjNDr]AD;"aK>W_Trf#mA,Ai^L`6V-#,QepyVQV-]6U%VmrS1Lvliy0p+TUcmMw6~0"BwMd#1J|cniiZ<?I$-2s.0dq
<:2$(OU-Cs"&B"6:
$LU:7-o30i96jdLi#a9h)SJHt2q#]7>`+&Y!5AqG=k-tqtNx1UhM1<PSrrNJ+BNYS}u.LjZ)Sa#,ZP,)u*-?b=3
wPKMSd`1^_BBua4K]A3F[/ZNRu1o:T%`<F#kfQVBECe*N<Ge6JjdXN9C[apJ1PESTI(7$3g`Hyl`)7:z%8j|KV+:PB7E>N0;Cz-*]Eu8;@
3<tqfb,CjGvKpY68bIkUsrhRRNXiJ86:W[C3M-d2&
xGv##.<hl`>X;kCx82
",P>)`C<rL<hBr<;NfRiSR.s@YYc#SE~JiA58&#xx
%~p^Tn3%MujF%PEP9?j@0Wqc8g@u7f#46sbLo|YxKTNMjCYAwi$;?+tX?HXE%Q%8!.c)#o+1]8h
a}6GS7g1=[VzPvQCSRU$86U>z#Lc[6"K0/?xR{?i(3@/[yc{KVj&V}hY98u*Cp9~PO4hVH%PV9#.14iE<zasuT:[AI>#
OOJX)lF]MnOyCtQD
WuUE&Z<F*A4z<j_`KH&N84lWmLU%&s8O:dVV_b:w^;#ud)W??qWN@"1ww`
v?|41yC?se1EvHdCg*o(%QhUzUT%.3
Ybj;e5FSLOTAyeY^Jg<7rKrC;&tdS`]=sRVD#vH&(vq[P,@LyhT6xkRjyRLUo1b,g..zX$84c+Lk(<-[dR^vJk-|Cssb-n1BKV`WF{5n#`F$^.WeDiWc8uX;>DL
.m;wCI@;_0jbI3T}/<P~:sS79zF0Szs)%fA_h._~)WpyI9ena[M=<ft1FU[xi/s]=^(=Nt_0;&?{[&UMJ?
qh58iGIxd<qW@!hZX/6hy`Z?:?--p;ePu!toF!a.cbtGLuQa{GgQ3UjeZX$fUZ*n54kiNZ_fMBob#+DUTXT%MD+8ZDFh,`b@~j]O
:)@,0v9EioqbG5N~A)I`dRUI3$eM)9e%VfwEY}w+H-*?kI(KyE!CeQQY;:xNK?7S4ahKZr;B%[)iFs$"DIFE<fKucRCb1:>ui=Z[yxgrd^*z0ne>(H3Sg;Fgu-L;c-]`5Dfju8VkH:$36mDUHVx>rvH(NgoBb[f3-5W]PxVaMRYu7_cSD"9!%6#X9},C:dnXe$:ru&PB/+Uqo)u3`/wWfr7KI)/mgDC/+/AE"{
SU+E61XpUGRMWW(H#[,]gIE)&r7?V#*#^MHO19w=[%9AP_Grrhi`jMC`vs|t~cs]?[o>Y^]^B`MopLZ#.!siD82ZLvJAbj[`A%cxtPmn/U_M"u08g2`8DNKt,W]vSdfGFB(EdWke"!^q}_.Te1(C%J_]VTDn!CQbx7]@T]"c%KOOm<~g">J&CQy/P>xH(cp`N-3+d>g8MN)8ZlIIWesuCAdk5%9;?xQ-h/FSlK-7SF8&2Q6m.#P=:.V`][P`3RHv89O(xH{,&
n=<=c[fXKDy-5(PP*J%,qfuy-09!))z%]$V*He{J}A,?k*}+HZu8Yohdv;D/$36ex;m"qN2,:vTjt@Ol!5#1{xnS<qv';case"no":return'-Zu@ibOZ+:%!(id$11u@~b!5#YXB&iY:te`7]i{F+9gPD<pU;B)v*yw"Gl+l/Mx.%jHM=J&PN>,W%:n:7-J5xt?X#M$2-Vx$]fxoF4MZ5@k9?mpev[KvyF?lU<i&GE<KTSpvyc2"o^9ZC1&]%EK.PJ:b~]z9TG>13ar$SlNm6Hw;Gj5x`J/n*9W:Cbzk_$Qq;Cx<8E9^!<ox`yeyV*u*7N|n=1x$ca~*2P]6Vj}IidH[UEdeCE6w@,&Wzte>o@~vsX)+|+Fq:<r#&f~#<^DnWq
=>C=6n5C;ZOk_5_VC)gl3y3-6BB;c&`x!K*)
6Pi=i$m8"B#q%M~SuLd)jN
K*R~"D
kZ+n<7ma?6isfD`R<Q<x[e+%Nh?f;]L4<]6BAq&vx)&1Y#Lt*v*w%;
Q|_Z_0.E:7B0xwDcYr"hh5(r0`p/?@MMp7qOT-PmjSJ_RxC$HkGFs~trj=SE#[B}k;1hV1iJ6X3BK06QGfJZW44Q9uY^?NC!3&]>d#IEno"5PKc`YGlvA%o&ujNh?Ul[E[SS]TqiuRSuPW&$3uy=2ztx?cnltJ^.b"`w:^WC#NZwZ$(5M]`<YF!"P$_02ET[^Tb9Ik22pFMW0RXA?-p8?c%FI-o/_
JB#0FlbEA-`8hDFlnvd+#]@+]hB?U<er.3I.r1ec4fr4y^chI3P/[E-QY/=U^6F;tx)>_Mg~H:!RRExyoZyo[?v)Z4b-8r!DCRo
>|ad[B%T1wth[Bq$xP`eT}ovP<&+,ohW,|_sDb9/N-O~&pj9)>*URndyc"xE[Yt2Z_6~kcu3c>XfW^ok%J52_L<.b>^N0Yn
/#2A6
f@z))2n!xQ^at6QWKjA!B$>)@YoD*Yp9`;y$k5gK7.=(1lCmFIcbh_!"4rFC6OrBraj()?*KdvqCw99!"p<>,rpBUuH|l|.7c>:>k:p3d=t{8S=*%K3>GNAg%xXwD48bFL_y87`,)2bBM{5_kGe/D)FkK-+;-;6~jBIFbXB0B4CqnjtwCSBq]{Bd)m2]tVoecV-0m7B@xCebYc:
;$Sm4O"5x{UH2fXV)+k$tGV)XTocfVQpwu]7=k%%8]_>qY%&ts,TLpqbtQ%jOe&my-^Eqati3`HYK#?T
X?tGLPdb059x!y$";U&%-$v7TN,s*IM9?
s&<TCq(49mdF77hInh}l)(Sd!B(5::QZKL,i!lcpJn&ZJ8oYhv_M:*R#@-B4BO!eRTC)_R-sG[0r
CQm"(xBYUn]#]KT:K^_sqV^x=co+#8,a<R+b;:3qT2(:W&2M!wg<ZL,ZonX]2?J|3x3Pu7/Q1B]WPXf(#=.KYJ@X5We=VgE(MOQrP!)J_mN*c
9Whebk
`!,qL/EHJOv4?auVCk`PEgdn@!
p)mK/?6~HnuooHAj+"W0Z4/XM!6Vs<M-F1Y$Rg"_w1A+S.!y!sm3_2[LM5QE_kp>jg.cC^0Z<f6Abj?H
Ag1JZYLGW#KGwPmG}7Rk6A]GOANlzY,F#^hCx=WcH:oZ*hRHa+:5k1Mc>$ja};8QS?e&af~A$!8iU,a8TU0Q;@TOCV@Htt0N-)YDOgXKo"5t",1vfN*(b&KV3Cb0smV4`[aBHr5TiB.kl)ao6:iS;ar009"v^E6oo0`khT};ENG`?
<o#u?7+9BcN7{tuw_M2O0w|4dYmS<hI5,)(e~^P0HC+FlaLfL
,jJtH.GRv6fpVh1R;]e]Hs-PWJ,-mYc,2=Cf^S54fs+6lZrs0w~I=Dz[X!3SL?Sb*?%r#W%^dXIget*$+re`pi,J,8g)3?MSHj
FN)9)y5R894#9ZIH6
ttaG]zP:&p]Kx/Z!<p>,4XC|(bw>.ZrxM(HU%kaF-%@-G,fOSTP"gTII+FhrP(_l=8)-&F3Gs8A3)p)Kv;Vi]2?m[,BXWDvW.k!pk&5"Zl:z<e[A?37`7HhxXs6nwzNzQ`Q4;q)/%zbiVLtf"fLHNop6fT,5E]ORgkX+7S;~ePP_K"5Q5N$1*f_X-rXCosocpu7|qt2aSHJ=n!SiW/7:k]Q+5Evd#nP#YrPghXH>S^rgEw^:9@3$qLcO/<qA4e4-3[8V5rOGtfE5%;pQr`^-TP<^4/pGx3TJy>oe_:,[DbL%+,RQ0C<XPIBD1]2yK+AD.>Ea;*HCEt?xDfbR=CW<s%J)aH5P:#5"Uvur_[l#p^s@/Ny],*"t6_pa-Lw?k$
6ar]:$Nt+a+G#KZ<,nEN@H^HUdE/pL}(.1]mmqt^^CR@>=@>p??^v6rT"wqpM/Imu&"68SoXc!n+>,y4Rom?,LlOQVs:3)=ov4Z-3@I$(du?5A#gn1UGL2bn"t4VFMyJ7CRJmfCUIv;h^lcmgp!t>qd[v?)=vdz,,[.NkbtjWO)@46hAl9sFGB<[c`kO$Id=J/MI@8k<A_$EaFON=1{:]:HXX=>@xWYD-A-t
]p3u;@Kzr`$7D=u,dB&6A`CV>mmcs0&@rwM4>ubBw2JsJ3@TT`cm3mD+&(R/l+(y6a3=ulGaB/#XgdLV/y9@%^,&x`>-KA3c;7;T;|
.)Zvi%==J_DE}#plx
)ui"4
v9#)7hqaD;G8m6PB!G*rrG?"Bk-:KHj%]y%b3`u:C
m@_J$^GmaO@^5OoVa(v:LorTB`#@k(6Ucm2-5_495aRj3=Ln(X[X=WIe>2>qIJjUqc-GblPG*16Wo.iC[;[t-DQpEbZaQ%).98@agoYT%];@NtWEW!927M^Up
ivSR=W!]@-q6Cs[kbtjEA^*;|8y!ty$5XZv/FXR$a%,YAQt;2L~<!v5d(';case"uz":return',s`09f{WR$"vqR<
iHXM/6/!%LDlJGD-z_yP@=VhTucMHYdB^/wwj/bB#ao"r(IQbBNeXff4w=As2daWG
ZXZdfpS=hf?F#$i7Ojp%FvuAR##s+ZRWd$5Z}`EltB7glfG7[J9Zlea40tH`it$`P<q;ryq&:.~w[NFs^FkVf
y#-<#!~5f#gYRcBCl^7ueJ/`<<kuRF-$<Qc?z9nRId!yz^(c4C0NDMm#J:a
L1Xv91Sp#ECI[IN:ZkbOC0:mrTbY~/,rf+#jDKYhhDKP_?ovn@o#(qDwo$t8T2=-^ZSw;/79{*{utKV!XNCB^vG3xuiP8+Jgh&1sdu[#?-hlFPXRZ1aAmqeaE%}+AZ>t,FcOmHqXypw;?:&i#`CoU<[Yb9mxX[i.H_K6zDpk==Wf
tjQWBE_X*Q?Nr0hKW>Lb>4kD;.b`*e1e#"!eouc"vX3$v9o&7/cJ8=`XQ38L%X%D9^9MQx;j
fX0m*Ouv~kug*3t&xk03rY2No)E&tbjZ"&=fK8J?7RfhjAO[CT2?cMf<^Ab1LciGTamescz
Rf25x^&
{%aGQN!cCCmyAUw4eD?eMI]$7S&Ewf;r9)@W:;>/#p:<qp_/xa-4=J`yXcz7"^Qa;NSS:[6f)iYc2Wb]lyr(&_8=exkjTE}6),]q%d79(YSLji."oFkcWD6I{ijSZ&CHHQ!YWJQY|-8eSl~8vRqDIAQLh]Ym-5M4__<n3;D6%2a]WJ|"#b868B="g4NC:FbUHPb#9mh<qXT,1i2ASPLI!q)n>6"HbdomRP)%&?jix9iRKS0iMsw1`+AeVU|IrghY>bC_!SLas3MG/aaB{:"z#(,tN<GQ9C.@Ro}rObK#!.lQ6az+=NdU.P"B/JULQv=#DKeg-TT>4GPB{k|3$]st@uHdHU~xf^we
AY1]/Xi}UNX3
Z3Je{%xo/Kt)L
lAc1Q&NA`SC)4La(tyQ2h^N%E/ke[x21DyRIWrNkb=d;N4&mmynLRn<%+4HO7O!7p!u?=Y=`2A
6/"YO`EU(YVK%[*q64K?&zF)/zj9^Ql?BwB`L~i96BN<E?Chv[6Q=UEk5j(pDA^V-:Q/uFf{
d5Vav-jR1:VkfFz#
(5ljgRcf(H>l-6LM-2U.J3"J-gLM",gJ;CY/ozOA:tIPqU%~Q/m)LIx6ZTpN(?X+ZMiGI7^bN:yv&f$V$}[7O_l43wX&wQQ&_-$*aPu?:iP*"eX$r;JuZqgw$0%jrZ*-0BT.=,+]:S/l6!L}PrKf-Z!w/=@DS-8*9bo0X<7a!cG:Z]];vj+bX
1>!1R+r^.(xje##KRXtc4
)&N0EBO4:q<F>aM2$=>xh3yQ:X()W9U[kJ7iGFdR6T)uENN2%*:v2^v%+8_qY1^-Cf$%g_#)!zc"gMO:i%y&rzlT7xh8t|&W,Xt!M#M</E,piM5}ccOj*$@Z"D[_fu:_kuka/g2I->aJY)oNa|8~C@8@O0^}%.(L"#@1!,;)9uQg/NmQY@>+9pvEDT%}Gue(,{czm[Ohv:B9[i!I7HK:pEKQW]3"(UN3m44Muy_J&jSn=NJ?^_f0m3hIZzf.6E]<LtFmSchR@/A@]wN9.*rsxr-*VNkW5_[d7m;+Yk[g_;T0ew.P*<Q92uX!bL;^PN#2Q7*ft.`rkaLqgOd;(tgU+jgHITi!#a1^jl8lIHg#-HBXCm"F3>3|r&7rr[_ef:Y1dI$Cq5et4c$jEY8@CY@3WEAQ2#I0!!y8)&9z
ZoVx1[S@h6{u-bwVoS-p_=3&}x]0pN9&WPWDT2@
fI+qac_+Cda"}PZT@^,7v.lt8j~j)K:hz(Xp383"B"P=q3)Qdcx&K0(oM`>%G#jG@_D4Q_Ha{O^d+WQVsvee,1GHONdR*eF$SQSP>Rj3KE5Hv*=p6tOghZnVDlXl~4Gm$n=XS1l;x1?v<N
uZap34Mxe>3i;p27vjB>U2Oc=kV(:,oMc]mBdmv5M.?TL=HD^FP8hvk1Sa`tL6,rEhd=I?,1C_wk/%@TOU5w[XY!M@97<xB
>Oi<3Of=37N[x2
VH@RM_Y^aF[j0uXCh8[EyC2uq*{IWLr#ttjvTmK?2v;:76GW~=9NtlC06U@OZ?lFe^siIV:H#p^uppe=&%|!w;QekL2:e<n&4d9I_`.w-ch
2ezxRZ8l`j0B[-l:^mqXGw4Q=PN?bWG#^i?+>w:
6M~2J($d>Ml7gI`oe
O#)Hn3J2|vx5;QSD&g!e{3-Tz&R$V6fHxWYIKw&3v?1HijAbg[a1_C_jdP2H0`j9y+-Y&VJELgu`sB2gR)hdhdP;u3$Mu/5iv6/SABFNrW7MPO>W":"f?tFWE.Ug[d/HH`b#8K*Oz@"Bsb"OQ]5&lP7UVx4`9G9Sq+uVk<oE<5Lx<f>U{<O:qPR%9lReF(YB]"_=~iOoSHEN4Qwh$#EogRR^t,,"7xx,o8[0RixN7$F)U.ydTa[Ngqfe.<n<FirKovQB@KT1i2gY>G;!jt`kS`bW0>41U+-Mh:N__`o(9
bRO=Xu0T&!zM~+nL4db3,adj7n1xQ1BTiAzW&("4gMj,}oBv-484j^We$Ko1op8*A>/YY>U>Jb6#(#U.8!/
LC
QMa3u]Id"b3fv0_YLYA;;X5_NfkY6cjKd7T"CHRFP=KIdNC`=|_3mj[eA,e.:QV,tviDm55#e`GUVgnhL+ws.pMbb[,;+ua(h%iafhA-Qf[yWY+)5jl&<vK?
O0PQ;11m][OuQ="_R2<enBW#|7Uhxm4WX_Vo?)5mPI9vMrDv?o)';case"pl":return'&]^@j6LB#)Q`sfw"YbJ-2._9w;m=#*:7d;Ie34~:LjLf@Q|S&T<%M^8@C+[eC.D:,
tW[qiT7Vc5Og22H_y<,sa[E`z1Yjns_+4%)HOFDM9
iW$[2W{Fo>^C"(
.a1d_Ox`nV$?h,mZ[En]f`FEr9bec

i`(IimR,t+iFJ@IJD,}iDrE0Ch`hrtF_|q$nwh9tnA@B5M*SJT;q.E.ud]}1FqS./]+>r;vd!,]N%no]5GAd5@0C{Wa(83}LmwLR{lX<|Stpr2EoCjlFQJZj#eHoKkhp#RO0q
zTTr/@4m{ZEqeGhA*eQa#
>pG+--A*NG>xade+_g"B)vk"
7MG[n0ilZuKBbk7@?9AVvv4GX.k6[cFEL+57yYocITdBt2H2Xg-
RvBR3PfAw;BqS_m4>Dv<,Xr~UHX96ip?+PYX4aPy`XA6.vb>"!kd0}O"(m^ZO9x!E(Ke(7O4mJ)-%h:1C&%08ajSF#we-tab-Ah?YDm:fP066oqXmgo#/)Do@Z@WC$Ty*TO+&3PQ2k9Zp$;=BAtFg1?W
42V;@/bx:ZH#2P`nMPqQe6L4<F-X.dG*c"D*xQ3q;s[K0*nGv(C`X0Uot6CToxzbW%Aw!rPP/(BXy=}[e+Os}Zh-22|ZsAu8DddVLKC-h8%au!Wy`okSpF&UQ[WL)WWTw]HMc1ab@BtBcE%#FAGL%kX2WB<`o-(7Nnh<WPe7/m$jNrQp1(DV"
mTsqNWPh5`lV89@))Lwo?Y:^?&$Aq.A4[I^9*vOtr8iQ5*SDuh
ssdr>B&|<RXk?Cd0wS57q-t0l>%C,^;TRV76PT;wAx8KGHOkO}D6L}BL?.bR@&RiBk`%`(`D2g]-YF
Kbo.fg_>lNxNlevwSR9A@6qcDL,LirBf,M;:?)/=E-m@55XI(]lk4:;2xh?yB2hu*k.6|434Nj/"j0GiA5|ZS7yS&+gb6#,Ozww`lrUU/sxYcDf,n]-o%1
dnEewhb~KILK"b+Ran,3i7H{!Lq94(Y;xtl}7H/p^SB~dm)V&/7j"Zw":(=T
_V{0n[#m<]?N&9Qte0ggB*%l-=!6ElWn00m$d31P]>9,28m1l)S!PnOZ-6@nA0
f^LSjTrr7&uNgKXpk*]{S$S+O#_Wb;V3pdUTI~U0nY#kN,CoNRBoQQ"U%ggpea"v<3pHTnA^F!-7PRKb`fnu<O-#eB<<[U`y.k`oO.->%HS
J>p]c|]kK{w;c6yxr&m?*JejXRAseboqG73YeE7S%%)_)O(8[$qXC$rUxOB]tE44FjyoMT*idLIW&lgP4`>1TI,&Ep2(pw?6Zzti;]0-hc1a,1"0*9q:?/r&gM2<xG<
sS
<k4sb,7hEc_uFV"n-/[P[v,Yb8>6,Pb:|".[CV,?-DV/.Xq#&OA2<]wyK7~K{3v<jJA7d8E-qfu5te"lmFUEgxK#Uly2SQjpdBR7V!bC[hOXr_LhaCPn+9-a@$k$=Qz;)R;sGp.bfC-,8"{di;4Vr0#v;lbY!Z4!Y6lL1QJm7"1A~Ze&!6&8R-s97_Qez2o*3Ep^y"X!ZL>:RTwP=j4AH?F^jq4G}j)f0)SFFgO[+?:"$-k$]hDB^LWJ>=a;*Vzp4%8-j.Muz<;>zxD[(x.:lYCu0$ru6MI[H6`n>)|h"@%D<miJOt}Kp/237I#CwS8o.?zQl6wr0`.vZBZDJ6][NO>F12sp}FCqzlF0l[mT[/fZ%wPJ<#t;1Pl/0%/7}*CtviYHF=u-7&A,dLtKg^)AS;G<`]DUpTw^7[YFIm2-diPNCW>Ti*>LG$BNb4xBc5[xgEKm?`i4=Jz[wRK1nN#0R-dR{JHZ}#LgqBB*
$M
6u4^$[05Cl
R4cSD?E->82K:Kuyl7KGD<Y<o.%)FSWW.?#C6CEswu&wdQEg1v<_bN;r*pX)+=s<)gDQL=wO]t<{^gxL/Z*uc|i`=Jp"U]BEk(MPZwAeoF
4d[tzXPD7GL!@<uQ?DF+tSh0DFrS#>u6n#<bTy+AoW,-.q:+%GV_O;Vjo;=Xe6>9~Hs)l?d:rbsk<7Q?k$Ut2r!nWv.]9Z<WUf*[@Y,?BIxM$Toy7@iG~(YK|jH4{lNpr%/"vK7cxptTf^:=M]_9@PkUvY}9#dDG64&2D/2%.-1AHyn7u!DjU(<Bd^EL|9qgUcY%lETH4LW*HIjsa"Us:],opP%<rozm9O^P-s9-.orY:E,)})0lo8vv@T;p
K/^A0[f),?fAIh@fC=6qw.bF=+`"G+.-_/q_Y{fh:M&!oCTT4sA4@@[=421D`S*++Ye[6F*>>~vi`%&.L^WprfGs0bKSn9g!2OZrd2[6L`D)!W%D!9#{R^R`MV=&Hz]p+B#PcBuP&seqa8q6M
p
:S:FsEdg$J*3s
Owj_<r%Uf*xE@_k!r+3l`:UsA/ib9]s@6VTTxUQ_aIYNwv!}"i1,IdP:nIJ|C=]U"=%.u/T<ioeb"St@K@EWc[@5:g;6+*I8q;<
5xVZr.2!<ICAAc%:EB$2H!n`3z-)gtnt/Hi9$N43#L_vx&_l(Q?c^41c]aXcx<hY<$4j7HGa]-Isp9$r<nTk(yFr!pNwoMk0<iA,omyjc6-*qnkv/i%O%{2jiP=3,[Nnk4Kg<fvGw+`e56o01HfEs8;$0v^A:+rAaY-XQ5Cf
AawM7bPpeWj]Hkk.|m&wxmh+=LYDD*"cLy)p[x+.eG!hV:7x~T!gF9rc13zl/=]:RX!aqN8)pH~vjec.O^?.D)zb@=$@X5ELG!PY{8XEodmt[%A3-si#<t*o5]k`/&nt-*DjkWeF^drBV%P4wWtI"IDWYqPKu"fruI4KKJ<[TH?N{y`,5A
s1=hAkbIQDEKNOdkS$5wV)_@)m[
G(AcP33FS~IqHKE;C7sa&SN&n2N_u{a`SxKmg;k~?=7ER2Oxo-A?09lPpuYJQ3UyS0:weyvcrV0yXGo|:1#fu"+K$^&V;)ET/
ObUNcDGBT2wZKs1x?n+!FX^-DTR_REX5_8-w!6c1)0YtXZZO+2c;uf!/m^]:BhRo8w,u@e2>_`R(=M6KqQPiH8KUwHtRC@8:T5F3S!s&Y2AJXcS-.{689"7UjE0tK_W5=+w@ehH=,rr@VeVBFyT)6I#WQ1)Nlq%yS~G)P~n>Xtax2?!rf*j-X&O*W@$4(zDniKa{Wy6?0x%fhFa}`l0*e15q<A!Mup&VJd[xs"oN?Lbt$<B^a}tl=vUXl&._1=m/5aq*T$J<."#O@$iuba&q[4<jVG]sFdIhke<xu*7(DF*+h;.d7AyFx6L^%tEBL[F9Iq%E[xcV,|L[:@GRD45n:>3M%!Y,HaAL/%_q6@cEbrbK1"fOV>Xjd>OK53J/g6Cx;P$<!Nf6X6lw0Xe_i3XEk9&ct-+bZTN%U$?+jh(wE.-(krgUeGWAEk`CVjg,k>I0xKv?R8iGt4`es21bUgU_ba"R';case"pt":return'(]^;Bbp+N.AJyj"+x+_ht!cDCi`DPg1$zh"&GvqjdU=Bk[EXzhITy=R-*qxe[l@+#&vf(x_,|TWTVkK1>UqR^D,p*ol_T1AeGv[>KA]V"ht!6yueBKXBg`PHo"cGI.>RF%VmPnVCCj[c
cA,CC%TeO
dZ5l+L(.spp.MLS7E_a.Q>jlTzP"Xy_8%h^Hrtv[7zso1nP=.LE/Uc9PBnD<$}$Fl(/cD5*)=7bFi(H5pMTB,0.5w2fWw@K!n3T^Hh0
]kSBL=Sp+[sLha)kr|Exo#?b^n7Sq<3].E7X@w?v6]XQNboWL//f>Av+*~fVoP3p(Pu~Gyl3Eaf;/[_hXo<YsaR!`E,FCOC!&>M*>et*M"]seloxHSkatLI^fWJ0]JX3c?Nhmc<Qa|[JF/yFAK%|p4fZ
KOC2Hy/&*z%oxp]ZLAN*pKHKui9FTeCY.ql5VssU1=9/M"yLDF:OUmtKcA}j2bJcMm~2k<YH@KT7%nw^lS$t0tUJ(*Q.7^zRZE-XU0)`}Kc7]*4YXGZ7b=avFc$">y6AuD;])GuM[u9wbMTR?e.xUs<X0=$1[OpimbJ+`Thsq7="[Ia0J+WO@dQ:Hr=;>OzqWBm)j<;<?Rus|YM!h<8fD]BGXQMYI#+y-[jqeJQ;i-y&k-uv7*_A_WKsiB1YesWL9q_N1,607NOhv[b6OXj@C!pm%y=ER)sK@udtL<Z-/[FJztQ`zeR/2MANxN~v5K.WeX$$V8AD~[`G4$8]Ux.tf6ML,RJ^MqQF]xd[_8Zft!lLxu6=Aw)7(r#Dg_>>tf?I/mXhpc.?UI}7[r!I+T)/4OoaNbcdy=`6Z+3k"r3UjjE]]hd_K1jtMG*Y$y~llx;w,P@5"G#B!y&5qpC=+Xv91MPpVl>j}M[d{yz?+<v);b"EP>tR6vaU
.]1p!F7ldSj!pstl#
]~<BFG%5cvw*1[x<u):bp(+wZauVBNlC-|Bz0UV<-]c#B$MLp.s40]9?Ma"%CkcO"a#,Nk2KBS4;cgFcK{EB:crAC.tp^!lS#9#IR]sho2RhK/cw>3S7oXeN5*!|W{"3>W>%IM]z/W"2ccO!MgZ;
$94^=,Hf4>L^8quRE8c>d@K/:o(y#]4oJrNom>/T}qhC/#XtSwEYAIQJ(`GMX,L"]CCqZLE#uqu/kN|u8=#/in*0ian@-6->*lX&nsdp/%+dIIDcm3;<06A:xw%"0y$LoL2<rxFTG5TwHnO;jA^oAw<g?ympe4d_v?c]s6=9k;&Nvw)fZ3.PMRcYES2;1$lZq%bgb2KSJOZJwR]eh`9e0.nYB]:BcdHnm0YHTk@o)tHQ!>$Q[!AscT/eG.~=^QOLqBYx^!hup"&K^uQmvEl47r[XBt2c=*vVOS*a+tB3P87JzyK7o:>:!V+q=e{PM/eY10runM2LXRH$i+UMyed".HA(<@!%E9(mVI*l@.eEAwQ<5BsJY_AYO3#;G7T;$:.*zA]/KY@$s*%Bd3>k^a,D|ni[0a*#Y!P#E?8<y,8f|P>@IKL5R
Q%lY;L:_rU@&
/J^dPvf&qv^JQe&.C~rQ7cM[_SV)S4_p&,Inr<xClgbQ3P3O(BVH!$ZQ7u+QO*Jdi9oaUzugPDg.!)g&jF[nfbq_wkSR8yQ(@zpou9;mTAVgCbqowR?EcT%7>)]oxQV!FMu>.YkB<BFN3**A,n+b$|]r5E&[,KGNl&2KV6d*9(Bi-i;oU*S8LqkC&@Cj!f/U?>5O+TQtg9**;~:WvV.0VffD0#]e<DorwpBcTG@Quxb)mlu,(GWsaDKW?Z9"#x6WkYX;*MbPtdC}`&8;#&#z%uII4`7NU`^<WX-N6l
|l>U]gKV0i2wo.wMd^H!LbBl;B7.CAesV>t?HW3`b6p/l)c+Jr,Mn$[RRqn%0qNi=!oyciihavUZX&<Y6pL%,7"gb+.ul$C>]Y5b,l2.TbDee5g61aSHI7](#byT#Zf?<q
)/dVY|a)=1-mgh+tbgHMda]qezktlHQGtZ;u
jY+,T4R
u(I"ERIXHul/xrT
zgU`?/55HPqS!K?ig_B8C(aTEj.aFkqYeUWP`]z>69K=ay_nT<;yH5;(~/q0NS&-8aPyU@bVdNr!$vI#a^+d!yNkPFV-v71[`Ge%dL%>)+%xQJ6Ju38_dP*c=6
RgG|8
H_>0iXesbqs/G7g*Q3hN)xTDxT[b-#?&mByo0;?L?ou_c+I?"xt2=++*Dtq9h
-FE}1E>ph3P$groC[Y]Ofx&U^A"
?)TQh}<`H9bLTov[P;H<XTO:g|#YA]>$UOS,cx3a4,T{NO4dVm7l
6v7>78iUkT]oLcr_3VbrG_P-L__cDA0X1ld9UTbP,?@F!"f_,=DN:Q[]oZ$JEpnfSAsXS=7.!I)a5l^f~y8+=LT.bDyGfUK/u&^J?z%O$VLnCM"Innq6(Ifm3qq/U/ydr(*mv6`_1ldW]d
&pZl7Ze[faA%/e(7e;6AxnB9VD
E*R.e`:%PwIa4DZb-jz=|aV1AFg8toD?#swiCYD`0[7F)T>.w"f;7(FqNJWDmcRA7ToeB]~`49ek,H:]7&l$1-[:Aq,iar?m;:B:]
*cd+KQ`1
W&x#Uvb7`qX(gSX#h&2v&"q1cY<<TwN7P)Q"OOQ-X;,]&h!Ob<m"j}u.M!ZRv(i5]L<Y`uRZsTx]h$LD*"Je_Tta%)aT?NX[GZ6Gm"GVK?+^xN75F/mi&A$+@^R+0%&<bVUA>Mn+)9pjbR9=WqPtj-TWr(6Z5_:GK-j=%UWh5P$vrZIv<A+%sxgK@Q+n3%A.b(HEs^@OOst
PU/Ba8)l)yVB0$O/R9L`3#[}A8QQu#5^y3Eq3GLsBdPqo}n^3:sIv/
|]1HC`kMR^a
I9(pVm=mXsWDc;z:D?j>ILzMC`bY*q8&ca)]|?(m(!2:AbR@%bmc?O+W%ipy[_P)/0dZ:x95_Y=ij!sld:vBHVO-">~8QrE;pl?]z
0Ib40PS%g)y$7MI
<]u^?Dz+LjF;H>`-swJkLH0WU%!xqLq;*rPnE"feeMeBX^lw^tR(4';case"pt-br":return'-]^@qaMAp(pw(
$&cHJiZTT<&!*
iND-=eEno5amU8i[`?dT^D!(DBf-UOE$uVEE,lQJYv1M5=Ix9=|_t.a/+;RrVmHuOrvy6cX<~xjOhRsZMtN2%`wC6##y^NLx$/m)ex&eMp@pE_hL}f2>FjgsJY)@%+<So]nokJkX%qcP[S+:rE+S0EgK,8jK.a872ZF6L#%
$[vRpeC9}iO.P(k,,r~8N)iV5sV)~!h=@ZhNXKid(*)l.w0y|r86i6,tH)DMFL?V~8!?iU~c3^OD^kEjO3.u2um9:W3A29`1&ootSX1
S7FZ:iJHolMRBp4<M&sI`4]!*B>9P%6m4M9Hr.yw`n1iD.P,5Ps+>QO-!OTb5DG3q_a!$s,v91]+vxWo]-X="rue4oy`(nVBe&{3n$Ec"VkSE!GyZYKRrUbP0W$GU,@=#Ve+^2q[0Ks>@kUiE:%o)2B2hK5In#23IM?NAS4Qm/Yt=,D:""z5ReksUDbk3ysx0nvw=L7=/T3xtGGL{U:@7QY:Iv
rbn4nB?<95A?2,onWk/JkaowT%?i_K:oY>]7$5.:Hl$c:ymzvFXD-eK:Plk;)8]N8
kO(iP$Qq<{Nt:mAU,E8,fcBF$=/t#!kgxr,ShfK$(?Bx/i<]YuTBXiDSJnn$8F
RlRvo*?7"hf0}7}Bmeg$[N6/djHRxfPAf"AXS]s1l#OGA-H"w`msi.I`#]phuOKIO(<^
t!9{<nB:d|tQN1HK[afUtG-x({<`"KJp3"a"5(^rl$CCH}?#tH&0nJ6M,_Cz&@.W"2O`2t-ONY*!e9
wae<rB<#?=@/ZGEaNgCQp=KA"tMJo=Qqm7fVUUY5E!2S>l%s-dg?T_7ZQ=MW,.5s]%ie9Y&j|>Tpziy5~l#n,s]D9!
nzsmP:L.L~8P/2c$qC<1#W><q^O"i"ru-^x5gyZ84r^R^%*|CRf<Mm)xtCL^n.gEqlbLW|aJEgmqyHmpHd#+$]1*5*X>,^
<.4$_&r4Sa%5[qYp@I~.Weuvz-MS;r/(s)GG<i5jx74>
n1N:-Q3^Qh7>[Utc"<8i+`PWyl/)SIoeVEy8Qy7s$IMz6e3fo(:9SB/oEoewK0wQEU&I0<r/O$f,5V@kb^^Oa6@<N[P48NG5x2Q|5|Z"8)V#,c9p#v$fA~/RV
^OH2*SK8ybEf89C9<fxus"O^(g
Gfa>QysUFX1;
9m92yi:_6t)E"Z!`aIgxmm>ck-84<67Glk#cR/FS+d=1Jq=+G;%OeU"eL2lOeuW~`x#9
unm
2=|*>,Q"@S0&UXtcxy1!&)+<CncwKGV#"+l*Gp,qBH
C?Ug60.x;2
cHTw4f:4]O#lvP;mcT(2^P9!a$kM:,!qvH:#G!K<yXm&yF-fOJacy^!PfMN-;oL8q?#N^OMPzc=eoHY]Mg4ZjE@g<>tlp$t"3%T^v)R/DRQ*Kr*%GC*T6X(1N8(1v%T(
5FrbZL+qG3u{iPguQw4D!Yn,`8Cq@axEcg,]?Hu}[<xhnw&=Pcoq;V9lH7JIJmj8H9a&kx&[PWKE-Lp%=Qi[?aT_TKpQD^4lFo1lMchyPCgickU~
b2V&l;5!5aQJ50()FDoSohFwCNreu=]ah9gVx;l`<u,hUQqCrT74*#Lj?0Lqfrk6z$$2D(,<AeFsh9#b)M}*H,NE,,EV}E(,c2qeTo4nPGe+%Ii/o&]@kq3.*Bt=]=0kc;+Em`DxQovYi&*a7PPSi,xuh!DQ*S{A7bbm"@$Zx=Vxz:X3lQ?vp`[(i#o?<unF0f|15DHnO!{mbl%VCYwsTaX^DwQl<vR1=Hl,EelDy#e_K/fGaEh[H(m#eC&5rO#Z5x.>Z"p?^,-`[)=1EXyQ(yNPsD68kLm,5]?*TsDHmoI<03-VX
(7MrFN6=xmG?V8gS/DUAn;gaw07+=)b#xy%5^3J-lY^tzmR
w-6asLdKW^CCwdy."R2*B-@T1o%KFipTIWrFPk)XO=*Tm?/O3T7Ji]l7zB$!5*MZNH6wBL11FSzRWd12%Qw?0$-Iu=Z
LT~7+!yAu237~("B3SL#1av]>QU"K^CE84uz&]3
7>2r@4B@>dJ]rTL)%pnMTZUqJ<n3}G6mV6!s]fBcl"<!GWk,IUk)t
D_ESn#1sq!z^71l,mJW<yM=
kuTn@&)ggEGlc.oF1dBxPV"$MT6>T(t=Yi;FkWE3Dj#2~@GW<0_8j&W4MI??]j^S!PlaYQVi0c"JarRkp]^KGZ?A2@(4Z=fX=@SFT/DhqM%U3y<.
.DY!ezaijVjgQ>IJ6v
e%oBs?`gH(]YvUvLZ)5Fd054S$5T4r*k>$NN(ULq41t7T$1Cp;n2{;h8u9kp+QT3}#n^LUf@Gi6JU6J:CP[c@
u@wqTbj)XvU,>4?yu
Lc
)Gm:3Sn$u:$kmi>xtUmgKgq88y4(%}=K#wtkbpV@qCTa-XSeo/gA-LrHU#y@.f6)pIy/$z?g?r_aXU_+]XSr:(%0vKOh)gtHo@<a44q9l$9b(YJK_e^cO~k+.p:G7J?^
vo46A9sAE_b
;tPVQ]^FD_@hZ9>=(BGMZFLLO0{88:M#6.Cv4vM?]376VR$/YWrc,7yl^^#jp4C@uIiE*AJyLvx;~3|r/&g(N
hp%e)/)cWgjpSh-$6i,A<o&feLS,Ngop`!Nv%@jH(:$c@r$KZH
5X-srV[i(@Mpf7KtH)+3I1t
#xBN/#1`"j]CNf)Wp6Zo:1xR9<&q/U>T4!rwqxQ}Y_V^#_UeRZU@^gN]%`%afHJKw.vus.
42d+!f?bOH}3DZ{>QKwaoGWM/(J8AiHQ3wH:7jVjpR+EaU.F2_
>*F[+ZutnTX[[-/{S"4F%OngNAh@5d-".U+1YZc=^W>EVm)c)ZEhc@8se*;|V]9`:0cQJnp]&P$sOqOJD>yq8>Lmgx58G8#8i?pa(cx9l`en,74g6,0.>^rIkQi)]h%VhU,~pNJb],
4kUFG
#g|]06Uwak7NXDN>`l)wW@?`0R$.cWH;>+GU5iqQb/@O6h3K/XL-#IGwv2h%sQ[r+!cH5h>hAmfd(';case"sk":return'"]^@1bWpMA;Bio;$^JjiYS{%Ig|)h9|OVJOdQmtf~`?:w"Iy9DZ[8)TKmd|ai#GTd0LFg!IZ)-lunR{XG!vba4IAR.1M
uopktGWp7blSuc_1X%E>MLuvG4=J9S-l;|[V0Gn`cY+tv.Gp;~,nSP;Nw6#.K?Lg&Tb;Mv!g7!yz^.:q.mrsp2LH8"p|/<Fc0Lp>+}6/Wn6uev>.JhPF`3R}^M83.eS*/!+P@ZAor@DD/L5,GbQVyv8[iSo`z)pGe#pCc}uRnCo3UR;[sduFo9O
P#!e&,l5&b<}/@8KZMbM/nlKK%HMO%/>:Rgtm$&K%soM2<TD3
o<h[PP=JC=>voo4t:A+F)9$>ibY.DLH^?D;b>U5~cPZKkJoW`3vQK#cn"89[tlr0v.D<)}s8y(]Y2IU"`Ji&jQgamYy+7cI*)?.BUlIn]k=vbz,ovIeI0;)NN}kS]Wd_R)8[-PtrgH%M4uJ.;0wOS9eyeuuRg2gouc6!+iI1Sm_e6FISLJj&8"7e41t&s+1Hkk4i**?>pUySHAa6Q-IL)
vqS<w89B`7xfD$E6:*Bm3dK(1,2&XX=XEK1`$,jtGd,("i[c;rQT<X9&u{bKBHr!UbV81zR6&HRiIh4iEVj`p`gH@<,PZG1)`9R;>Y-&dyr@)(U1L-O2utQp(-7-/MUg_<Emg+6gwfB(X<6FaD,FscS%;950++]2k3[%<YQ(H+1)I=TzcclX>G[u*E-Co>?k#o^)J#t}Q22,m9,yW
@{IwJ+f+Df3@.@h4VhrqG%e}s3wduf0II+Gs/1r^eaDg2;*))ogI??<RjGp5):>iNRDSxt+76G[VLmKgThIf9M:ZREwaY>rOY/JeYo4s-f.zJwlNk:1t
eJj?IHQ)TA6@~?^7Yath8kl`Nag?_?aESi=m6i7cxk}R<]SDwO]!~d*GTk5-vTUi%Sn&p0>Caa]l.A,ff)]
IDiqn=^fN[7%:)Bh`>2D!rlU^)Qc40dwqlOD#vIOUo<pXvUR!bJ^xL!i+iuMPmC(GPR+!=@PSszaeU6o;66sBfOgY+-pXy&/}b$J|Q_0!]pD`Zq,~.?37WT#tv&O/L/%V(QPXx8_*H+=_M^Yt<75=#=;b!H[aSmp=l)@Ddp``kDrc="Merwy*#$83@b;/F~G6Q=(+c[E&^>AqjEln1X^qAmN6<uDyuz
=AE,ou4V+tNCU"LvES7Qb+iHNV|K
YT1>uQ@_[16b<vyyX[qPoEMBbkB<Q"poh%c(EL"-:C-ECEX#+DKBm~>H;*e4V):h@gih]qhx//8r-/8<DYA?uh2az!4=egizHGf0=_&@(%j-e=
Ax#y|u]qf+PHooJed5H]i.<&cUi-NR?ZkvFtBsw?K/j;l>#ki3;S,MyQh^PEh?y_Z%WpIE^"b-%-=c(mHsW8j1`>9(Nn9YYqvV6%X^oZUr=*@:?F>kE$9-5i@Wm2+ykbRt?PNs6N5M_y*u#dL+a5%_J1)U*Zf_YAS;N/c.K+kl7K$qx
-Zw8g+yT(&(P3^jMsyD#[h0Q2+*^^8Yuh(1%Re]AGC-.YC4O8SxeF1!t]4x@$PpTE"/+]
/k-_GV@>6%r,4s>(0=$[RX{53^`tjT%#}@@KQUMKqFBI1`
[@;q:H(Ayjr=`U7):s[Ad>7v-EV;+UCa8}K_y,P7(82Bm}QT%{@1W.(Bfd!"x/4Kr(P)8hekI/NYY/O:c`Cypo6F$<=yVqbF"5E$1N8h-.s-^Z0|%vffOJG9N:oA=;,0ae;zv[&{7A(uH&29
d"#R{YTfmfRd&Dvt~.Gp-JDlT=EI!>/Hdfm+@%U<LI>CLRi:}PCvXQViu=&_([s^^w
ky,S0KjkO|PRq1SA#xaXmg;aSN;u:-1ac{EP8XFN@g"i6&EcP%<Jwn,fLE.~:1:p4|K]olHehtY@<znM,rrzms(-(
_!N2Q-k`,LySIfSv0J=Zav)va`Uot
m(PZt=.=g}#:5{lb=9JW"|2,!Q7}f|4v""aVpD[ca8CwW<J"0D&v?Qyq6"#<!apzv<PNmr8qAS]?C8xd_bO."5>C7)u;e`.6+;WXy"6~0|RotP/]r&bgaNSYbzWFve?z5?C^_UX_>Rd+W{TX&6J5CyrGXU+/FC9~<Ia.#@fB7yT>_=pGwemA*J9jERnwJJT2U,YPBiy,@L>5T0%?^s3@"(f4X7RGF[ujHk[.PF)XTrN-d5NGp>]G`].vOS=]oEvYU&aiU&xD1X6L&DZ-;6Yu4]ngCKXs$fIMmZ#zG;BKW-`@mSWV9<L-4??,HZk@eVIA1<*|g3#vh
;^EVfxtiAvT@4_75!LSx-RrN_rHE!wG`bH`SqP4uq@%jS7H$h7c}7q`O
LZ"gL&K3$h=uA>PWPcf(M17s0l:1y6m>rPiaPt6Gj,&h[Kuf5
"Ky/),rFPKJ
u[z$S?U6V9oRaM,/Y6q>W3mh
#|h%:N!v,EfYS,^"akZL<oqAjyxWd>Nzh9l.,q!=&/bZHA:_<@_}=Xefhk<`-UN]508pT]-B_7rw+h$RvZLZGMbX/@bhxl=<2t6Sy4Mo*.YLbWDkmvK(:fLXv[$WJ<wSD$RL#A*
.^fNd]`Kq"!(f5hFu$gHe.(FlI7W^JEl5$+a2;>I.8ONNM?)D$D~F*0t=hpR/{
t,3jWdHX545Nx?L*Mi2r.<GF(ivT7q>*,fhTwm!6T#]2IB(BuBPx.ckc-03c,j)GG;wKx^8dGD*)~Im+EE=VEP9t<*L$WrAA&P<RlY/]ee]_>w[taC<S`Be4yZLV^f)NKeqJ&o=Mfsg4Q0@L4;S8*[9"|v0c8!vSo*q6pi;]xJgMJG`t=(GaUO[^spd({;S/Xc/p_MtvZU:S9Om1Qd@C<]Qa0aYn55(cXV-RZhV1l+HK}S~-sML9kt8`4hnC|2]I;@
Ql*IFTjW^&T=u,b&rBjmyORXc<k_5Plv53_5#v9?e-"@F?*{tnNqTKPJ54=JK|.`o|)EAeL^iD#"xYhLvND|!UvqSJ=%B>[VkZF8A,xSQIK7hrrfYAJv9^L&2SI=&2X"0;2sF@D%A?LaY9hARzkRs]yBGGeEX(uF)W4B_pY}hQ#Qhvk:o27_<SF|>LM"X*h>1Ggo9FTd/+*D?-FB&CDfH@WigMBh=BdlF46XuFmkZ*RN*bmCG[.(ZJaA>pVU$F&gs`>yA6o!NBVbn8HE.^#LL)!_B6.:y@v)pohNHU`TI<$,k-ya_TmMT|?:uL]uTh,,rR0j"C]sa,Zlh~e58L9P+dE3[H`k6,/#mWvTdAO)BFq(2oV9Nrxa)n3URmvS^Z4[jG`=+a6$wG2gY^]%H6(/rJ$u>}[8GyoiSLO&nh@z1FMO!aj-6zANwaQ^cbxWs}
iAQVRoQUNUi*Q`X4&RLZ{y@8$a7.7W1^e2*P]2)tg';case"sl":return'*Zu;:h&D))Q,SY/8obP0+CJw3dzOPZDH&datbsj=w8Q"3X,"/1E,H$J,9Nuo,GT(SeOyLt$x:cr$y
.hdbWCPl$psiJplGne4_
bUf@auRbK&2u`M^OvT`cV.LWI.`XFmL{xO#cria<>Lrn9:x|Y_KGw_NuHPxbR}:S^Boqlrx<axe?<kw9s38YArw@C,;~u)$PhQtCy%V^JHD,:D7n!_Zv`2ra$BId55eJz%w0m^0y:cS(^LAQdVgtyeQp97C/b~t5aJjN":M0RkGqiyG-pKw6VvvIT/.:bJLXN2%z0cM4P$`apXjr/^NAAT35QMitr,@HBJ4/Bk>@
41L(f7a-@Is=Z^o<<Bkn=vI81#wyQt(b{%yE;-C?+qIP+u[[|
vlQ@8vH7yHa+F]*xS?S;Ro}H.JP"/3(LBErTcZL?I.AiNjF5R^
l-aOd}h!TZDZKAd7cwj?b&kq8c"9ARi#Hj?NE#
yQpqAt@#4+sHJrBDD-|aAetm@udN_+kgpBcldHE2vyt)6KhtQUFrF.|_yDCxfp)1j;=;/^p,0,#80s8*wt?/Wtz_@b
j8C+;{$Ah*3ypB@EyIJ%?c?`6WxBIZPON6X3aZ8Xi6we:q7y/RdtDdRwS:vY5swQgo&w!^N2QzLXD$cpeH,i`cl+L%q;iZ,@70qJxlU)%w_|r-U75;#$oJK3:8RsqgBA$;f3f#z#Og[{yb$
Ugw/-I[R/l8Xe6AViq9_/jHxxBGY6<_f]u=ts8EVq*^a&rM)Ku2~n;8cg,)Ec_T4dj?G<voG,[P5Z$rF!$iuiC,[()wVc?4Jkiu1M["H7"P,XYGQpjs4G$uOa@`Pu=OQT!OB#l_<=NS;UeCK1>u5qG.pAe>ON(F;s.x!`2h#-0@NWpcM5Ja01!K$U,qP66y7yC
cO3&v&=k@G!*Ztb@l2D*fcK-qFQ(MD%$3B>V!rnY)XTT69xH;-=m[)mcRs.Nq
GilNr_?6DQ$L+;l1E!c(p"m9?X"AG?qPh"1/z*/IXw
wEb6_#M>oj(t7e4]Ge$r&5X.*bL#$A_pFdyqY#X^$BEE;D0XlS<T^QYF?NUCITvI/|pJ2}st7UC>Vrw1uZ.Q0-dA)khnKYH3@<Y?1!dQQ[7|hkL[vtM;`klUiDk&!b<oK3iRwB,a8~_Xw{EJ;J,y/qnGfVWcnfFR,[
A"`_<o`"@Js(&48O=EE;_d+0@c4&t_tC2@3ki-^d596#!:+wCY&qTa6v;R
q`M$[>d?c`.YfMW:q.n(EiIB$
]in:$z&i]k"R,CM##X(~!;uf(YZ]=b4))
fki@[_B}njM7!<]|=?Ntu>1G
`,|UcoC=qg@H]%!q"pIt1jF6!iV)S=O9a!QNj-PywtqAe#H-L$3:R[q#SeMBe9y]]]49m9`d3R4j|BmbP`"dODPy3s1<Q"=g*/j]7`*;:Nu8$Wq*=6o4<J&OM`xa)5-.`,x[!aPc-aPJZxl)]ck$OT>5Kqq-XlKhtC^.7qH(t9MJZINRmS{AN,B,JB|x)lS]w8nx8L{Nhyf6(n-0i1jbZuJ*EBCi+/>[<BwEO;pYdiZ-k5hlbD+JGB0PAsn2|Uvd@lM*Jf/e3E1[dV0x0g`Mz^cGx)X)^)
F1&JcaK."I$;$igRqP`O-hfxxs+D)WE=>0Y$RG_M5Q`tpC)roRW>e-WX7Xj}ZnS.KX[WP.FcsER;[ZX8N)gLh%NaRJQ(be$LxOebwYTnc--,dn-vo8g2i{BK$*JZBcQ%f
Oto:htiJ0E=K*Z4IOy0B!u%0L8qE
d21.e>R(6gF`8FhA&aiI0hgp==y/5NI"hA(_p7#HD6t^S$iKB9nHw5zWsj[0vxY+BH&RII9wa-Y5.L?WmxhnvEc"g#zC,+5)fn=IIwoO"^s1hH
>iKP9k[l[PP`8@)VU|I}
ZKp3Y,"SyJTEpXr6!NhdPh_tCW[W@q3J:,z9t>LS,
Z=0x^RMpb!+Q>bKT#uM"lq1U9$+IXdE-nypB^1kC443A+uy3K?pRr"9>d?eGwJZctaCa`x=&I?l2avU[:dABCeyp_%8]NK4";N)y@SKWW6%;`axjqk;<ty[1?
3$G@!qEt<#2g7EOQ/N8opu0fjW)4fUc47n[paqLyq0}"[KO3vI!ER<>_fK]wRZA*8$D":rG]
5R9?N<$[^:%Nt39~^rE(tE#15GEJaMTG/LGPexqi!1)<i!M.eR!h6J6}N^.tl"gmGoE%6{DU[02
ofh&VCr-E$;Ok,0^yZSiI$15I&K#d_kzfG(3UPa8EppNnn!@ZyK*NW.yl!Ypl7:Z.O4PkTATc_y.F`#V,OD}&hY|/rBs$aBt5w<mYm(w[xZ.mrhFK">6cJ.2*0y`Gr2HaNcm(-[pG;f[NW9=DlG65-lvh6@wD,`]/i$<we-}438|%by)O!,
e$4x5D=p5iI7-@]dT;nT7b^y=f0OB=<fN9&yBnvQFfiWnJl8:2rb)J+2IaD+l,fC1gRAM.<9z(FR5:B6/uJ1ivm7JsOaO>A0%p0u1`otCLK5@onMDgU`nD4,y|goPwN~a^L6DGyI7P.EcP7Op+EG_rWK[KZLQdHI`c
Oe&(mUv:P>m1[$
PXq89H9[eRG;tPEw;_&r;&cy["=J[U;$2H+-q^2zHWHIdI;]G6e@u-_?hpR3X@/nD|qRV^UBB64|sg_7>xe6f|,>6l]n2eq%lH-7bT`Z-1=e]zxp+"C|/|8R/RY@[GX.iyy~Y>4(bLZEv^yYwg-+9&Bk^f&#L:g{]u.?ogBopj1~
cxU7;?46"DUZl!+T)jwH(B,DAC|vQJvvZflK7ih^Lt+[N(=Z)D~XH-U])VRH+Edjq;C1(y"0Gh";!E`G(jD4QmL0&fXnTa8fO]Q-}e0-FXaDH;dKe#kM0V#-|C?"V4L(b_4`.5aA<S1q-v<;QDS.C$)Z)LWn?SD)zmGkPE^-lVU[V98mQEZZhkmNHLavcO#,{5w!g5T
X^u5?D_f0NU3Bo1C|Hu]1+~HFDSHqgT@6nG9Keb
<+,p%o:B6c:275@oU4|S9yyoW1I#JMU8P9Su&YJ=0SnI*-X/gL[F]<88MmES?f$<1/v
urk?%
jw,I9jum>Vd1oFIFR>(p;$mkz12wBb
P2QJG[Rq[=j}ZF(=V(T1?:uOF_/Pr!;yMc6
+1Fv)zjpp33RZP5#`b;(^1+n)78-x6#;U?4Z]&<ux^1T!5Fqt81h,#2=Y@T>OqKvz"*:';case"fi":return'(X/;;5LWR/#t?Sk#eJ~ZA"oY8cb#<FeI%aL8ND*U[Oq4@[/=]-v/lbU*EBqAjqsn~.Uf,%=*_>cd4J{8m<4e13
e|Iznqs0Wml:xskg^ia}e7iA!qA(NI-7WMC-H@
NXyk=nxqN2<rq4^9Fioqj]Lcf-p+wh0
GFp7i44LRCkfcK#H7rs);F6q<^tY6lOT>wxZ{Gem(xwlOyTSl4Wn{Z)hrJkxcx_vZn<vbwblbFkCD-!fU=~iY1
2UBQFLb&Di)-p}fNV<F_NRu3jKPMAg-bE7o;?y$R"(X>S7g*49X%YPy9=.5GmM+-4g^0$#i[gNWD8z3:*)6lcy0*BwCX@q9=FpHPNDAy5}H,LCQFvw+|/!rE,EUp%01nvZEviz:4%5QpV.qc5V*d8aKl1K6}LAK0".X?(l%*TI9tdgGxI[^6sU@_aFj[uXX0ad->B{7QxK5RZT_{tg#2Z]se2CK=.DnPkxQGoq<{Rg1QMUXB!Rsq6N"2eIS<#2=P@ssk/U"
i|G^]T.!88X-qZ%C#6OqssRUxixAX8Z%nnk^jaZ5"eik]JWu-Pr.<_;1=hd@*si#!m++P8D0:[jaGee:ol)z<4amj7"f7eDp({"sDjJg^1)-ug,rh}`#*g<yP;GB5$eKEl<qk66mK0I}6
@_:NNu[Hj%)8dI1q3JXd;3"gZ"T}b)pn/Dh*4i+Y)C._:GPY1?3e,jL;a<=+5f.NsTLi^|*.*un86S501~e:[6#|DfO+RIr[m@rjHLT^//0FH};s&F8v.M-fV-d/+o08<_.Nx#W8i&*W9B-XP#qH+9$S-B$SfmlH*R2.yT!}n6JyC[F.C%@uo+JaWOAn@_QnSEMyc
_maGN3Rq4Oh~FMKM-7j&V$@
YT$aOT6BnUcPrllZ^Sy9N("RY1>%A&+lvenF0e!A^.(g4(TaGWXf<ji;StWbr#2aLIno
tMzo`FTt8*Jj6U,kd+8!J#Fu_7OK6[Vz#C]<EIT*xObtx0D=E%a+N9;uy9;O/$`X-67Vq^CCF&-r"?5Epyc-Zn*mEw9PtI<%/ro+w2eAhNHcZ1$m_!@=U=eQ&oZR0xlu+j:sh$rDh%&x,[Lo8%*$xyW?J&?d(<j&)OLZ2qI!47,Y*M"AY5x4^/Jo&?wVzIm9d]9rxp=dv>)HKjXF<VMKI01W{-FR08T"S@#1_:>SY%684q~p=e&*z`Z!BJORR"d!3,yIY&BOpx91
P2o:"dt6IFn<Rxd6%<3%h$I5UPCu&:DixG7/O}e{0z@I(AH>e
m&:JXJ
/9QKAL8@j8bgO$UQH)qLXX!,W)5rUuX-;OD$EODHJ&?PxHz86C#w!xAOBa"?2W5ce%99+RH-
f*bNvSK9O1^Ky
XuBK_YVlY2Y=[8u,0v.uF|CJh`9mVDxyI)r4`^N4dFU_z(y1P!#>L0q["],F@-088exyK8Q8e*.rp,2S$+XHc8(?[Aen;&bw%{sk3]q2*0!U3HaRK<p;mj/MO=NV#.D#9z)2<D`*tOA1T<e]dUNzHG@H=g,>/]GXg].F85Yl_7)U-v-wZ:OuRT$XyUYt7^>tuS&ZCq4
sq.ra#Xa^s-C>[a?jy8oOlLp:Qm~I[-sh8.r>7`f)Na{O%=_O@/t"J*`+=Lqxw*B7Mofsx8t8(H
AwK`gh]xG=@suXX-aa3gitVP;MLxNwV~`Cz(5tQt]yJ7#eH2HQv
b"<KO+vLMf9Bq1E-&P80h41w?YB^39dCn0dTK=si7dXpg"sGS-EfNHWQaX+?;)%dL]`+:u@|;`5xd9q}fL!`N0Q}3,ZZ2~K^%(Rv3B1ML<!T4zP)By^JN@X"3UJc,3XLu`#,OMiSA!pF2l2v)x9~;+%:6FseglMr)+Og9$r|-i[0KrVVMUno97v|oUv58kFsp)g8jVuyu$n>p~DD-1-YX2rDqA-[aM`n"K58q+wP)Zt=VYDSYo5t3<",;7Ki381v+7xQFIa#g}pR
$mY!Sl2CE58Q`#4iN?B
NS^0#NJ!fWfqyR&2E*wRTbmT~[t*nT63h6q9%B6:.2Ge<CWDZHQ.%Gq0luf1A1HQzBJRTW
E!Ze0^:Jp+bEC$.o[zJ(_:<I`HF*[(F4o3$TP8RYNi!`b0k[DwJ=]9xsO[ZLilC?Y6$r*doY8"MIS~[U>]"MK`2:!5,eA`K,-)pYrB?5^l29-zZ
5N7s?;xH$l/m%iD:?>x<LQ7WA)CU[<W-Fn^!br-*pQiq+O+nM~xu7Gpr<@jm^[->PptF8<E|(6uFY}Z>:<m+/M3?j~gju%61^i6a3geRUxK_c)>j&
E%[f/vp6gJmk@_gSo_NGFe)f=z2g?eiU`LDa"m;y!VoO&1WAPKAVf4;?Xki};.:=L*shUqh>9t%h+@pj3[agC7FMiGU#Ow?WjgI@.b%o*--$H_1;;|:AZGte*bRL;Tb*-OC.cyn
F&PLk~L
S/,VgPF.f;iyZYB+)>7f$DZx>)Cw`LRnkY[,Ko-bg|/
=T^Lq(fConh2Hx%%k]4Fl#_voT9C7Y94gE2VA?Of`>cH4X]#v*&IEQmw`ogR>N0RJpeHgY2fZa-+&qo-utt
m^xR!mZ$h&Q%5M,xud`S2>ka>RAKf%ajeH=uh%g*=2#ZUFr!r}?jinNh_!n`,@j(G6.#)WoH[To4?lj(ksAPyLmrKw5kPv
;<aX2U
U7@:<-Y9^}sqEJ/~.:aT""g-*65x*1Rdy&efiDsNXFq#I;KaHaw@#
vgc=LOv7"F%`b]eYRI%Ukf.pFX)fPv+T_%^,>TeW^2PLh[o""[>>&{XLw=Z/sRE737!$>@`}RUnOog>.S"%Qj(Y~Fb@+l.X+s,lzYU]eeI;8nFN}IaR(i9m&B@C!7z*%J8ftf7E",uAFaXsGpo1"I1%^mJ4dN>1dOog0`TDcE.Td3I,>!pVN"x9->|q04Y443zBR1`6.;Y`(DM.I.QR;Ey`=O"+Gen$z-}3zb_UB<2u641OQ>CP{
B*^3QdA`rx37`A+Y|D}TLF=R]loJ7(ImYcc&(q(JHRBI&62N^"ZvXB}8K1)CuTv^aCm%y5T]4mvyr9=>Zn^;lfNHx4^tg6;-^BL5n:g07*_3QMi

QW?E$hFJm8dyrMn$;i.P4mkhgEo$x!&g-w8xqk^?N6@Jv[$>/@i]=;9PG=iC8TAMkkZ][c=c&".aBoQ*15oC4ZjbM.;;(&!:g
[#+NOV`?<.$(';case"sv":return'.Zu;:bSZ+$#!:frO-4z>6_RO_Gqu,]96,f1nEGAjX(]

_=^Vsk;/b*%HS,l_"0t|/b2?@*qAD":.dIO5
CM4cokRL?wwbN5B?y0{O"21)stnc?eRmP[Fi]]@H5i~;WcH#1BYe3;i2yuhxPO"xP^<2-skF)YvO3M"IY7m(lB3ofTrgWl@LKMsf~!0I{ld,n2+f;5
V+G]ZRTkgAR$^(t^AAs,z)sjjUV4t_JtwVyE+`]jDF2B[S
W25A(gOvFb-.B_vqdA>l$g]i~q{lw?qLDrw1@>c]yGVM9G%LTJc"Q;GY]wx`l)%bp*j`#R7d_W~7e@/Y.yxLsfVp%6EtR,I3PwXAZ%71(Kt$i^T62bI,3sVSWTIa{v%knn@;~`CbvMDn-(-D7K5.Jab)bG"Ei-)`OGcaZ?u`SrtW[w
BYa&,=+Y>8lenF)_Y+74TOI(AaJRv!&J9r<@?DwA4W5>v/Q4]uz)S}&EuDO2p%-Fh{)ywI9OKQti1HZ/sK.ku0Z2q*>>OXs&Dc,YgRm8%gCS%:;cV$gWwyh=Vn887(r`D8RoYjMH#I.6X&IR]>l1Y]=qRqr]Iryh:Eo=)LNa->aRfN1[]o7y"%(&({YJ2J=RFt$_AMVY(,e|Q#[JOtIuMd#D>p9kaFi
5iu!/HWSGP[bZ;LbRVH{[~eYq$y+__lx/pTlC![^lED=y2FYY
)P.vRAFa?@U|g3mnvn<UpTq%`u<nCW*#oRl3+:kv@@?~]b%gflHqI/M-KkdnHEb.*85f+>42WP&]w?q*C`GxG"DA-mQ)c{m~LF6-5mj}RL2lpt]ux;)i%)>a(!d=hk2M@"p#4aSq3f6MQf_i+?ma!tGX+<6:;,]#w`fm/[i;Y(HTOfMlHgyZd^mvyDIX/KW&`$F*w$,3"PgO4=l-k|Gn/WdY*ny{v+Eogl/lYVcYR7h;*nef
"x-!`o0JH
Gk9>-M9Nl/a[A?P6.2e-R.
?3h{sT.l`7ok81RlQyW0tH-k:6k7t^/+9|f*MD6[1o80VQb&h=b/n|tuLv8*Mmh(^6V;sa1YU]%Qe@V7uPN$q/2{pM+0o$nuYtW[]^M1nucb5h:aUMie?Te+D|U-7T?)B{ykSv8FT&nFJse"9j87nhF(IcC&NTA:`L8E-HRUmgc3Vdl?tsw^/XFfkZ9HP]olS`/H?F/BL>d*u7dw]afZkJIR86GqoXN<RL)/dfW*YkGEJx%!g>G%Xw"9;S9wox_AW`s,8+i:UogT5%fS79:=7TvR3E.>d60eGox8whyRo(<+a{diFY=@`vS9T-7^"YU47z!@g{@3b3J|0D!utfsK%9(TkSe!Hf4TBbM4=dL_64ezE6Wu]CpMDwR<g4JQ=)h=Ig,zd|PUNjlb$*XMB1J_Pq^_?"ZR7iBy8bp7Cf8_E}*i1S
y.@@7F7jhF.jptq2cn>pzCfQ+w)%.!m_]Zb8^(7gi&9@=4X4s)l@<6CO"o8@+`rO[ZwS4gwCx/p5xY{Z4RU7bBTxZ"2iF5_o+cx6#^#pz&F]4ZzD)<qfqbi(w
icRp>%[1z*>qg3mA5"GUG"Q7IH{*k)_:4`hDN"4)T=V$p3)NLOF;W@0^h++o<CrTmr@,4a7H(CgaE)zR-Jd=Nu!pl-bWla=@35%pIav2=x_g.r:
Ri
nK1@E"jR/m1OfYi:QMQENYLs8/6d`ovn(v9kJVX06h:B"rf!w2DqZ(KcIKf~t(@?a=)`"4^b<U9,in;Mk~_%3krs
&uwpN(]
o[oR)2#`_<lv=jc
05OE..t#@eTkbQJDs`B2<y
D%N&v|Gne3uD48IquHQ]w.-DVj2u0R&k7:YcPtQ7gkIpb&nhy%+?+$5LA6j;JKH*]^N&LI1c]@qVEP[,ne>.qWrY*~
xC$$l3<sz`WvE9`,Y0U0-KcN!B2esogVRvlq_QW<&gUkbs}*f?niKb$y<2rfYjl8H%YNS1(JZ+1qNF#g{4iLBS7IB=0"h[5yn)7g^v0d%Phdj=`_{S;Rl$oQ=2u2oI*jR>S%>!T-/=G0d+f@TN*Sza$1lqc.F`w_%O&?*([CWlBa($J]DNbXgg7jrseUoq*fg:uN3P/W*eL-_/|-[OKfN"QQsKm;;!9Sy(!?8I?vUU6k`p4&o:LU(4K
u$`gE]aK95hFj!KXcw@V0Z2(+:hG3<MC|7pEhbqsau1d(#0Ic_eJmBDp@@)6r$]eI7PcVuy.gwkF"HHg;9b:f8x/p<h%?2Z%-+a=&T{rA)ZM3TeH]t7tNR`))&*TA^cjS%9h{tT^doT:
azH"p]DQ+&SQt3v_OTiH`So#EO8BtTt:SFYOtG=RCi3#.Rd/t2:$QcQ8X&<4T@Gs#2xn>2@iCUj+Bh<7L.#oc9aW%@]iY7I<yS/*N(p2HtT~M3Rj#vUi.yYaRtD>U~9`$^lMj?#n$z*f(mO?YAPb5Xm]:+P
io-cfg`H/TJvqCk?gxJHywWS8M"8n(Ye_Z]y@Tvy-dDFHGg=&vPNk#V(T!:oK?,yOJ7j9^TZ(IM?O;5Zg:XuZIcMTWS&5xXRL8kDW/arWECcU5[.#n*#BPZEDd$vIA<?7s&;QTt+2<=c$*?H$:COp71tDuVpeH!UjN^#vpLm&Iv@b~`E?=/=+]3U>1U`m(Yz`0QVgru%?{ym:{FXZ|4.+Ig])cLQ="V|.rDS&:E6Yq%Q.N6zWZ?_-^U>o)nJd<k0MM*=R;<Q0
xKUD][C,YqQ1^+?ZI9D[l1<[7jwrpoat){4l-D^FY#7w@GtwE8vDTA>Zh(^FYU5k)J?M0hql
.Q?3v,EQ..t(Ny_(w62Yg^yTN<&uF`(RCY8aoIo&Aq,oSB0!oOO;NLAp6s9L2on
(8EpwT</YvSG7W$xwB;#>J}t#`kB8,e#KeC?;#p>S8o?8N&';case"vi":return'!X/<%]@Z[Efn]v,A--#6-2II*pY"c6>:}-un8SC!>;(@a`SI-kyI9,0RBE013!ae-9z)I<</fG($z#(gyTwx+y9EfXs1YUo^YTjp2^+B0]`x27?p!lyTg*+U=rIR0yNUC@{Q`T0r.uWXbrUHFEJdM@quaJa>K[Dg|LnckD&r<fjE>5Omz=P;rusL}JfD@skH69jhS4V1R;#:`-3XCH[2MVZ=[Ig72KZCc,,H.(@^q5+,!kFR8hsRgkRsEMBVxd&>OVVO$h`qWl<5lt7
+)$ujkIpm07!@(7G{XzQ{M8[hd~/)v{C~xyLaWJXLY$2}&T5*CZ3fn0L$+anBgDQiBj,1V}=7HCkSHu[-`g">]ZrMOj0w"b8EY:GhYq<S_+<d<b^jHww/X{5(f%&lmmQ.SG%YH6RI=rrsW<a)z&)UJM5SozOMDqFe%+tSOhkAWHu#Clc>=I,T!ixSX2UZOax$4<`P^S,$V(r^34+J9uXh(`V{s?5cy%RZXLdesEDK:.rApHXe2t%<(GeMJs3BZ%7:TuJ
L3EVadj0nWk>7Cv]J8MlpNiE)N*(Lo.Vx.h^tqPpe(VkM]#pC?4iDfgn<Oxtb9Jf8,/Gy4MtL/5dHRvhUxq~B*dS$cc/;8VA5N_U^t#eG0[^_k.9_6LuluLZ.$cl^q0)x*wCU~[FXvj?X*b+#%t{utv9$Nt%5i^:><+qT__f[ld$.6+<N?suY[:*(k97wNX;Ix^GSb9PA:0g_hyuS5<0g4/UCwoyp_yVAP$CNi
,t
H=!(7a[[4v9d94pC!Trah[yFMUr$7)A/;BJkz&]E?_RJvEW7p,Z3?jA>UQeS:Z"7?
,k5,JAZHpgeDcvEw9>a_s7"~:RL9d("}Y|d>m#u24pq|s27#QBt:pS[(x1*N>1o[""]>7*uy)x,]iv.7a
u<C]09/gXf9dKgPsEgMfe</M"VcQC|$D!z&J0T@$
&dVw{i3C1IGR"9!6VlRJb$R^M0+)GC7NYqW_35D+e3*SHsf(wPuVAWJb4/!MV;+J]x_D}$EP2PqN:GGsws0l=o96d5vY&vfp-rR=*62;DKfN_"cJEtKGBu0(?#.Y`h%ae&?*3R8HYBvge7|q5Mr-*R#T~^]tK(|KkSd3S3;W{/VHDZ7_Qj/dW3eho-pN
Z^oy]rXcrxuOTXkKB1bwl*N0nb5z3n"tf$ZK!mALwt"@yR+dT+nhJf!v)5DL>y7Dl|Smcm1e^;$NC&E6K[E7YGwC(8.!l4nL[k9zOojmlrFGAx+?Mw&VQW*STJ6&BUfb`L,h+4K^n!OJMpCLtRMH+_djS8FPwMRe@M#uXgRI*52Tl|s-e]d=hjtG?>Q-$X5w,]qF[}=dC9Viz#/GcHf_[jy`ffBhX9?-[q(x"Ul`Ur3|FAuMA!`{k=[g@jAcjdbmL9Ts0Ed+#oF$7rpgA,$xOt[lx:^EeAYJ0^ZsQ[EPH2EW6]k>l1q|@0j>8~
8f$cHP0WU#Q[f6`YiDU2oXr_p&kvc`i7w[uL|s
d&r6)c<MXCAj_1m.nr[M+eaS5w-?,V6#HPvoh$?~0zCLD
+y%Ha#blTCyV
Q)8*o<.y*Tif422UE>5=9FKB9L+Nog6dsh~ClD#)
*+c:u7g5Rk
ScCAWu,a]ysja+&y*MDkjumI3ORb*.i8bS`T91/;NMEu/7z*CQK;cCU^"xu7dpv>s0<Xv?:xN_

trjU$b.7%$maTv^,#H|oiAQ?^`@?VHIrSXvpT=lM!/DY|uu$2M[^7$]>VLo"M2>v
oE*=h8/r75;]e]r)lfwOlF/PYS!9F#PxDggkyfSy3ecOh18oc@jDbQOaT>2wQvvmuFU*o,oxY,LiZM41LF$"TP[2I
9D:7<nFLe(
R&Qr=%0>1kGfd(CcdT>Yt.&;@^B2<04gA0ObB,k0Pd~3l%4am0#Cygi#I[L/vUD!UGw"rjxxx<y#
0+=ul#Z+VKF/[gNS1WP|NnYvgqcIMT`~-A/Gjzw28)`6#4ZmwI&`/bvyr24(D2299}y/8X_m:ULib%)L[@bF/%z"EHGw-Dm]1-Z"i2!w[CfJf|4i*W&5-<%{>k;-!l-P0hHp<-#TgF[K?{p.h]rZJ{dzm,1E#U$dWvPlErF3B*]Chj8h8+R`)`Ot2sC*dZwYh"L
@]iSXfXk!yt[8UIns/f
(S;2XZmD<kGH@OVWIdmK7?<(T]Ny0EFh$s#0G&5V#M;NI131oU"0r2SXPExpZG``=^AA)G)>IO-{J
5WRw^zJlW/qA?#"OkcZ@0mUGU6wJ"$"F>3;
t
UA?zDgCV-rvQ*2AQb:-{AL
Lm"x..fPsC;Bp.-0]%BiT=SJic*O5j:Ze<(WSV>3WAz^}>%p8[!`yMU=Ov/.:W*VIe1x5,{4R`jyi"]MS*/ssj//+V6${cvC:AQ:>mCa+]W"gHRZ,g,_Vvk*lO`g
`s&t@Tr%NGGT/Ds=9YB_H%`;4ic(8/;ULq:b
p*[cPiBRwV_?*gDeQ]I
Pg<:@Ia0b"!#:d$.YXF[:tACuWgS5OJ
L0K;#Q(5yyo@?-#gbD%hCXpg[`;DDG%OX2^T!fOQ~&m0=$j]<&Ob564im2@
Q.N4m"cDqOuY=NrAD6W2Epnt3`Dmun1$fO?Ee0t2]XU_u-88R)4x6u]x>.AcUVf^1C?V]b356A!GiibM7Rb*nA??Mr~gm?RjTBNd|9Ba,.
;[];>GI!geti2r@y[N$9]#)
6A"Gnv;wnB08mN>ZhBh
k3^0I>Q*xa!#`z*$g{?aTpVKvY9*Dp0:EVccdmOEYyL:h!+px;K"vewNGOje%#y^Fnft`,$psr2$[Hh1BM.7!:qD(Oty4xEir"S(xI(^g%i6.~.=F/UI){@O.+0LB739WFdM:=_AiW21hU+(dRx>v6b.K(^IE.cQF]s$mn$#+IcXqM65f!+:bXxUg}JzmUR8yjSROfZ5<Nb0UHCotC#q03n*G]
"<K<O]W(11$NzJX:"xS]AX*T=?WJ~YsU;`I`RtB7$3l1Z%p
iDIgE-Da&d<f}G`5r(or4r3kpDFnuAgq%xWQ6"YA,x>4r)n]h;kT{[wnUa^-pbs
E*7V,YT!%7aWgZJT%K3VrcC0k^OcHxLg1GHVhDp]^(j1LBmxU!_$AUg]*0e0JRyKV`QUjB4Fukl#N%1xC<Jt^n_a7M
K$%Hv$Y)P=PWnFW)#;xaCpqTI46.adF[Oj4u=xw?QvP<upEZ;IN4=P/vk9mg.aSz#y]NfGy>U7?^`I>$c.<`<^rNrQ=!*.lMvUwgb.>-Euc_(t[)3KOBd!$H';case"tr":return'$UF@ibPDI?T!(ib#+SX/jY7DZr(%%]V*=Qk-%:pU&`qE44GNKtl2etXy{@1jeB_+2tA,uBiAzMt/x72CQJ|u(?tw/79qJy1/v4kX[,GyZ+6J.pX$/#d_U@u@rwGEm:b>e<CD(oG=^Z2W@grS06acnR;L<J5u.xV[J@V<hdnym$31"-2>Ky=7#7msBXAgj@Z)rsw.eXy?/c60#AOf7ZYb]=}S+0bVC`+vT2$+E+u)TWy;swE7Nb9y<u:y<Asy,;f%Xxbs7>-TUep?N-P`&B(f&7vKGbKJtL(o.D9Qmr;`O4iqDD)wX?"?.7
A4N_Zbu#Xv?Q<XwH21I)kj]HZ5J?m]22Mr>u5Jp3p~
=cH)6<3:"KHy$CUAc)jIrg1u@2&-{De;mqB.=RgN{_5GKd_lL;(*LVuPLg^^aB]M/do?Z8zMnRx^doi)S"ev6p7v~o!NDv"PD+2Rr:4+*VJ/Yl(h%[Hh_BN-vtCZLjFG:+J):I
"qr>s9&D1^bJGEmd876{I7wm`_!<SnV0=rVZH4L<v!9#4GB>.@6>
|0nH
A??2e,s
LO"$gkT%f&4?3DTDm*[{ps>@LMayL4)_b"#wvl>.5IsmP(r#WPTl9=ip%qp#a
pr4hLmW&u;V"g+pSBb(Vc(ZzRSLnuO0?4pc5S{&ca|u8t[nrY#[)2%llo3WU[c[[
NXeo{$H7.fGi}_U<u3%M_51O+4.<g&TO1XZ"+,bO6M;H2?P-KyRO?Kb1z!22&*xD2iw+5PU,o-}A{2.G[Ag-h0FO!KE(UaP[#QK(aCg7hRp.SnnN"1X_w)DOOk(xXd1I9wkPv,jEU>_0)J1*vpr^cJK$AAO_Es;3nn:z$O}4]u-)G7PO}2$r,)~b4G16^9~[}VpV]H4m&S9,AwXd!&&KS(g9G>qmFjj7ljigj?%bK*vJ+$`kWk&2f(|Gm"N=Z:EWq1%V-qXi>uTPgbh^hZB>qyH4x2}.?Jmz$_8
}$w84I=QYnuEw:nD<d}H/9]hF]j)*U!5nZg6
g{PZ&fU+Od,y91]D@yu%nY5$ed.LpTe@!yMO9^UheKZ2l#QGehVYBHad&]C|+~Bn$"e|#8GeB7E(9te&Yn0912
W?4s$[]1Ub[xdqF(z52dW5xY~;F[}8Z+2-lX{aH3L612$#_06]RXB(78!(sNOSDb{0|h9o_s
lLLh:I&_ZkF`g?;nkm!(D,CGGB.fc+Sj-ZP)F00v%TDvj~:Li{,LJMpsnK"oQnW74gblYW4.S"y:$.egD)C.JWNy<1Ni1xPnukViD
:/W)=W^(>~FXIGFvN@8_l>]`:F,olU5F8S^T9:=9(:#^=zd&<YRJmHD1rqLqFsKij%yTBQHZ-!i2KJPA"*B}F
%>Vh"X"ryq&+fKs,#s4idnPl5FPOv|h,J2,=-*im$
Gljt:n$U#?r?CV%<p~p|?eeC"~bYajF7Xx=B"G-2Hp]l9EYS]>@"xh(B:hlZo<V;6Ex#W97:DY,H-O`zm/b|3@tX@%u8V^@mWBwD37&IepgEIcY.-z((-(f=]Y8:Sx!Y#.qWC_Saiy"aCekI6ktx)l-x8dAw>l7C1L81+,t&d8xIAV&eeGpft)e|Xz_[G{o]L)8r)WsX<u25I}oNkkN
Ymg&-SQ4ovt|m:y:."6=dssk?LlA&e?V@Bk#n2pR/P)N4xnhbK;u;lgRFw-kX)heW}AZ$-NVQwAMv:8)sCu:So_LaeMcKkC72&[sRBsQQl;7xh=9t-haF6`.)31u@nWQ4p/qAYFy&UChovh<=%;{PnQh=cMH97-oe.Z99?C7ZO)8?%;kE}CVK,*Ct`<d-7(B"`%34RM8(pKncVu<[@X%Ef*&fJq(^C2L8[+blrxvv;/HOfS
&Dp:t^=|MrmZ=_$*t0gQKHF<@f98;Yx-KiHds@Ya-CK!Zb(:Q4Cg:z]vTPI@r4`p>9$lR$39d$DQ,V+c%BNmRG_;:xu38W3MDCG*UI0%t?BKsU(8BM]afAWm,QP)0z]eZo]aP#K}2sD
"xU7eII=B9#VgJH:_[DPmO?KNAe~wf,(]7.>Q~L$/=fVS+n+yh]88Tv7K>%fgdYgca7fuK.LSBgLZzGMh=#+!%f*Cb0j<i*90dOQ+w!PI&=+).)DCQUQd(5Rxo.?7ZaWR{]F[}I^2m/9lqKR9C>=wM5hNM0a5P?}!=+#SNLN[J[n-mB2c5<o$t?I)/Eve7QF!r8B
yu3T}3Yck*)SeCU5NM=B<0aA$YSf2=wV|k5%V"|Y)n>?}8xs?w%R%6[U[wa3;ZoS`W,N%9tXx,bkj4{xg-L"XQj+"uRM[C?L-mI2@d=2Dd",(?4`da/poh6$MMkyAw,%j@M!/Y;R,cO7B+47wUK4E.K
/v_38AA=AZ,D7^u"G]O[*_n1]-@u-3(@+Er,ad5N:HS7Ge8W"NXnU!vQ^+8x*_dP`W$[]d{C
&u#JL.u`2DYA3FC%?Rd#/2&SF5N/A1:yO?wKNVM=m@nwmh6DKaT@z&4oNuq|)^!uN&6.JRA]Jzuft_>+!6efxp"]7P^fh!d|L?8q0I,)xXf~26?v]fKSSM7h-{"Xk/yW2<^#ts$wniHNuo%nQ~6xh}o?r~`]>ok)#*2)p[*K"GA~77+,b&RJ.1?Ro47+g)(]/VYN]_nRoJ>))ZscZgb4#bV};f?ZUqV.O(YUj5t%G#?#X^Ksh5E
r*@"p5CYu3C0Y)[7x{Gn$YUTB?%}h+.HjH$~x0)o)
^b(R1+9vt~QNi9+%88ByPj_Rw};Ya=cGWiHP
mVH[@Tdor)~hfAWT8/,/rD1a7VfOhffN
]V]iJSk3S*tAA(_{E./T_u%tT)P`dX:*fcHItR.Jk;DR^}I#tg7#7c!pknWYDG2&74C:W|P8-DPjd_TAp)%>x%HxUU3+@/@hnqFP@SJQgi*C;/X[mjY#6(=AJ6:[2,08L}Oqe_j[e8+600LeaJBUP4T|={*c;/p};]P4e]Bv[di)P:Um)O/~Ry7*mpf4/=p?nJT!vZwUWti37WvwmSEZq@,QY[YBsZ+?IJFsQXB.d^VYb`o?r]*^Z]9=:[gp!Fln:+@UkaF=>Zg0o2OkQTmfL>m=bKU)HTr-_2dI$8k<&[RyeEtbki
ongwh3hndXD)[oK)2`Bramrq4d.?jUx(dUZt4IcewJxM@9@GWo1"KC3S;n0)WS6Qha!A5B"ZY%@[E`KeLf$8G,aG="vDgdd';case"bg":return'&ev@qf{p=(q)js?]$gSp&]$>-4(
g_BK=N8Ole{1P/#ZTFA-u`jKm;m%72]CC-Qghg#J-tMiDw{%SI1]q;%X7uu7xyAEhZiHNKdvt4vm]FM[lqJuaALDsrBvkq[sMECY"Tm]h1^f;n*cKt5,Y@.a5XVi.m;CA@Tm=?#vxP[s<3xEB`3yB;5[
8jMq
ykPnHAyGp8R_-I%F}LHG:VxF7z(yY5n]fVB(}
=@V,DsT2fIFs.y.BSGue_%hetG[shDh89_y7"G6@O<VdNleKGv=TFma-sHIUzV5&zMKiUyXH*FwXDXjZ]Toyxo&=Mh6m~ovg*eb1D9Oie38Jc3j31=Y7o??L|KPx;.j:g44Tdw|)2m!8f,jF1w-!9,;,=
WP3TW!%Gz*5v(f&S?V*mr8/F{+x7[3U>I/"a(H{vobYm.Ys<4+[#23MNBDewMFR4_G%1Z2SK+W+61r<9p$49qN5Go_4+X)c,QXfah^K.]h7t=Xp^iZ2!-?
Q^
N@T9:#=<wm*u9qM
.QZ"jsGPqI?K3:vG^VAM+11@qFPkmL@F[XYVr8`TOQr0VsO@8=>,_s9B&:<lRsOj^o%OqfR(TS)r{o4Bx=^eNf]:%-.Gd_@tG#|VDFG2tE7js
t*4JPBLNfX0O/[<i+%V^,v4MCgm.^&PRiwuZ,Q@rkO=YZb]&WB,-,@g,|IV@-i,kKP&.ZG9]2qs6Oi/]PNOSspQC_/<5QqRPhJw6ZBTJo]b4jVqu7Q0Nj(]Hr)1vi*
dQ(48JYMdpbjb.>#.M7J=y=;M6_rD)rccN1aS4[~0=GFeL1Z)M5f8$4Q`eW_dMA1B*tbvP_N%K9nj19p.{!$Ta]%,iO8[*Ob?G/t
b`k%E^yiyQA`Kf&1cO<Zg:)oBd+6p
P*tKjRQ7RC$WG,d(V5Bt~P32SPbW^@jOhlCQ=I0$5HjWf#d$J@&C@)uDEp[71B**

6wR`M=0yc%+0Jm2@Ye^3:S!y]_+F{m/<mOEA~N.h#!I
W?8D]qy+95"H~JS)eMAc1:|t8kE^C*Y]pSR4WXh
le5lP%jb;PD!1nA!7^d3"/}<`n!Z19qAW7M^6I(a+OPmel2j-U>)ZA3;o:SZ6UINs7rU7u6SF0K/w00s;GOR3PtD4IU=/*k2i1e%XRy0EM(8e%01=vlU5*e$ge%8Ec}r56LH_AZB4AtMbk[$T^#,=%gfsYfENQP$l&D>&C|)q7uHJ7eK@^f8NQ>6GAmbTDW!Q,7^]TRg3"3:|D@w}wLhe<YVG]cbUT%>)_-lPOsoB$)2n+,9NpCK<+>H}J7xsN7Kk/_*FTGW7<*$}P<ekqE"@K;&]>6_P=%(%,%R
nm&b>KrwkzO"Tu"OBzW)f
3|L/3P6RW*J3QiiV;q8f"o=mFMgwrl9jLj!r#0)K/0<#/:$t0d(XL-nG?4:PTT+9$jV~.}Gq%-@=G648kgYOunyuD{ZKX-lLB,XK:/a1`ec|K=V);X$1QLxBC]lu-38#Pm@drkn,s3:X-~Jll,KJxavAO@!ml_3b#1#!3QRvf
1UgmC&JhVrEkwOc6*]I+jkJTZ{u7edtT45#5=~`TYLPb*D@/Z$wt*R2m;-Nli-$oC
W9x4=dPkXq,NQUjM7=AK"eFw)nA?_`t`rqx`Rw5}Hv;}Q8=Cot.5^aSk00cYc]&N_5D~$J4F%aA@e~X.wZ4+VAWA2Dv_"&;4dl:Mrs)M(n`$GY4fADdIt^0A0,sKQFiF)LNnUrNU0v)`Wz;bdCs$e&T~
a#[@+dh)Ip0jyxYe)kwC@O`Z
`SY!-5"sx6aT*e0}T$/Z3vl%yJc
pp@O-kIS_!bS_cL)$?Vf=;
zVif-&MNF]vp{Zs&ZO*%W;a"dWZ%w-jG~;SI]^IgkV
FwT@F)["W2"6Dsc/gy3[#+D-b!?#Q0sn!?uQH^IWs[,2TBwDUvE;/Uw1?!3m*l_*`v0/db-&NQ8N2C8U`Z0v<%91O1n"H#Z/?rs)/Vo#Y_)=)<OZc5;pk@>!rk"yYF8]x&h#%xu>z">sq?xt74P7X#$vO6#NZP4nW+%L4=+-DKB6fn5vs(Qm(Fj|b1?N#~umKl>zMl9$w/@>darg.

{G[)7=UgQ8KnORx`]<e<u?Mn.L$blV|4.!8^}?jjL1g>sa?t7bGF,n:V
aASw**
xaNcKb;MW4!S8#+a80x*j)3)qc2Q=F>g3Z*){qwAXx<_!pS9E`=y#E5xO<Gpt*hi&DD2,[?P?;Y0Txn.yKWRDr
@[[L^Z%6x!.sMbT0,HXdGTple^b.G
^38Og|]k/9Wu,Wd>6{9KUZm<jKa_&79Bd~`WmOCtKrlppP9.G
"|WN.l0Et.d5DLw,N!:$6OC$*A->r=
4hF35;SrV#iJ,/G12&KtKTdOR5!R.:24&9CaHqvr>x%;WBf)cROmon9Y3w
d3dI&s6&)*SQ!ua8<V=Wl]:m5WWyO~5c>i@d5b"L02Le^i7ZO8D;ydq(TWFz=4!kBzhUkmj>g<1,XPNOF_@}R|.siByZMNc.gBg|6}au&jc)BONcEYXmWqkQSH]VvMT~n>!8Z/lJr,1)lA.H46J^1*Zc*)L$VR8>.?0DqN)J7qCmfS2zvZELHaSkby*p`#)RRYIghww-+]>C?.l.;L&m
B*Y%.t%i^;&.&=(p@3XbU(<4[YnTF[f&:1oZ
o4M-$R$v)2<1YyZ<T$8yiV*K[
1HyXK<Oc[
o@nJ?A:ugM
SXK6Z^7ifYxty4|*86F6n"WVHl24P_NO_lWHHE`ppyyAAI~/2[MAy3lc;<1?EbBUM=wP|lLj=Y!D>vK.Zp>D!tm8VI=-zLRVJ;#^Im,]FV*&U
7c+*?%RAR#=n*#TM;mNoDZA#Wq$U:AX3Qu0>KMIG?(=^L(PJ[8FBYx~IwMVvL-.2m^2:gvvt]]O[l;m$sRCvmnvxig+=IX5w02+K1"ggt?GtF;!slT5;7dFU8@X7x1:^6MO"!(J
ix#"lxH<:Ka_U/-YabDqI,MwM7?>7xo>!j%8]N3_onpBH/#C<reF(HxN|;=
<U[)XCR?YvV_~)*E8JS=nfFh4:)tIM;s)j{9BeSC1Q;uzZ.qCavfmqsf:3j09.Z(.;?RAn1[^R;.8p2l$VoB|CwYw3hkL4x=x$q7n,Vto>ocU(4M13la}aAfNSFM`t15>MnmkIjjfL8>xL.#_YmeS=MA65S%?AZ7O5+^p40:FDV:I;OpD1i(KSP0&+kfM*X(-fb%KiGuS$KnOYKT
R(Ss:dXx
AGq4|V&4_&x"af,?WaWQBfkSm9a,3TXD*Lmk
;q+f._)9b",Lv-"aDyWHV2QjCwMO,{13mon*%$sCB&)0<g8);}:^ZYi"1j7R+h6QYnd02<)K$#ZFe_]t7oMwP(x>jhiA!&Ors-hN>Cw$>MJR>KJmQ_v2O`LkaFfNW5GD*ecNPuR
up7=tr+T?|.,CRwncK^y^Am]q./WJUQ4RmhDK.2cQz>@jsCM^dUIwMWp@`3i33o.o)@+a[MO8*HHGF;NZlP@K@?
B;oKQmu3h.YdrE0#<x):tKL,Z*Ha5ryPm@10P)!KGFjtpqhBX;a7IRWS(<-iZ<DI
^;><veY*O0<?.@s&wDg1d9?mxn//;5EFf>S=d1U2,$SRW&}ddLt
nwQyj
Ou(JP2MO%pd53Z`R.LZ$#i:4{"V4baH5gMfozi1:cr2F&tLcXhVl`?*iOHfT`h9=n*ev``rBNtuWXgFEOq`-t_rg@,3w{r69DF.].H:j@ElyDI8fm6Vj~BKBK6uF0EaKy7P,D!I`zR2n`ef))_HB6@S.$sv4tii!~Z!3Ku
g:IiEIL53ssUF_9SE=))pIFIb
lGa`<!4mc2$-bri3+Ab:-Wm-i::ES#c?k}iBwD](
J(X9z!AR9ku-{f40N8#!A';case"el":return'*h_@qaMAp(pw(
$%wH,8!.^P*;EP/^48?#^"q^f]+vjR^N,hulB=f?N!bWFLEyO%8R^tAxR57$e;b,j
tTeX_.5)XDKCH
Dm0)hFcMA
dByA~Udy>U@_(rl!>P^LK(/`q_vy.[3!8gz<7`n
qK+hLM99fJDL+bFT>U9^y+E2Q>h+34msq3SU]j/c*<ZBDfCc9SCgWMoc`xDt6+LDNybL:cAL-Pi]XE&%(mln6S3sr6_65?_*|1<37ZqvM5/N@qlQ{vD0f^}`5S_*.6
h+`e:}aIG9kT.v)BB0"qv^-u
]P7k5(V&)Z;2{r8KqyquqL_sVXnWX`:v/^+;R92QKLI069urEoU:jg{:LSwW`<#(/?Y*%$Gm%)c#+KnO=cz$wu$S!pYIQ]{*vb2?]$Y7S.2YZZ4eKF)CGFa6]YGo5FFE5IFw8u~/0`;eaA]ki_-SpXEM!EnB["kXqP^F?DH1U,fF?d(b?*X[O1j-(Ua">@pDFJ3kb<q=?;[qJX@u;3Y3ZP&j[kCq9F-4PpJ_/i"-Auoki6cU_
<Md.tFR_0X|glpK^Gd34[+}NJBX1~7Q3Worcg%q35fo;59]Saw^0r?"n@EHV:Iwn|+_>fBxW}yOlg`*Tr7N#)59tPQJ`gA3
%IOA-i-54ly(okhQ$RT0jD9;WyzepQ8L851lgF%G6JKfzq]r,[V,Na3qW,x>5OXfIRlZltOJIG["#v&gZv01%,GLXh0wB*<Z;h;ZGN^yg8N8OLZT&yDstKO!?H}3pFt]@Y;G|4$7;vU9
Bz:Jtk?/"lg:5[3$%Cq*:zVUgWCYRm-Or2RfddZ<Rv?A/u"v_=Bn5ydstfw(U9bp%S_UQ!FkDz=*c3:J29"[kELXJeS~S,GaX3heID
vq(HDc<6vWZe<30dn*RNE*O0*5_I~HQ2Z#}4=s(NNU1*L_.Lc7%BO&[(qgs@U#Z>CXPsAoEk}l-U(m{3uypcjcs_W3(5~Y-5QLbTx75xkXwO2B2F2w>VphAHdtHaw_.VtDAR<30>2t&qd-fDT>_@reI/VnUpG6[h}r?*q#c%Q4d/axhZ"bjepJ^u)
QAe.h<"^[2^l^%!m8=Xg`id)(P@oGFEp
a~-Wx~q[e))s.aMKXpCY`G8B#C#OT(>)H]?"=<e:pk/l$~Nh*<hqPSDiC|&7SW.lm#;Q9iOC%C#=N]<#OXCjIw&HE`<J&oiX=e%XwV.]-du6sDtWx^S0y=PhO6VITI.Er*(5xF1njZ=nSX5nij5p"N#n&LRRgbw.i]N%oW4__Hk1&RqZKmh]$t0o(a^{_:ffkZ5MU^u~(igtU9>WA)B|=W)6@].S1n4jw6c#`=v|NYHu]3JWXb3_`*ko8*+.8
!{yIxvL@I1T6f"OI1ij^:w!mY+):AT"el.X}]9[5#XFv%J?/fLK#kCQ<D3oI?m-ct^f/rj)/%c)>&RdIO2Ey-^#*+ug.2<Jf?5n,#]T53!-B%s1uF2y;:(2o&=;[dWO]RqZ<r~/qx!5-Hn,mO#(r(,8$9~Y~/o[7NQka
(WI42C=rqk~c3U$*Nd9C0J!^{hbZUf)C1&SK/45pZLaDc$,A1f,`Z:8O4%k_=6B*!`(.<N7LPYic/S]@CQ"O.@G/*0g2V!fC)H6NP3?mLf
8lyUeZy.0>_x(9(6$D$7k>7mCn[O=w/8&BJlOm^Uysyoe+tNxX$,2u>M.(1SJEC8yvhN,_jgrYxCtWs(9;0Dqt4(]daC`!+~)pyB3.S@VlY>kr9"l<S0?d*VNRDi$jKf14nx[
oFSG^j/)!4V27j3a@jv!x-,bETOl-+8gCKWiaf!vpMS5>E&y`t$TLOyfHfafhG&c7)
W;IdDOY8#Tsh,"Wi&,|%v0J7[Dx8]@l3ZmmBz"avUo(+}cIF)&H:M_
VBS=4}$,B}EqPIg#,>#`dI#"19xzrX<1_5)8b0Z_E?UCW*V0!EKG9}owyQ>Lv~$S]6Z,UL01M=WM#B$^h+FbeE_L1^9TC;*Vm&2.YdXZ/+fh_R-7^9]=-NR!e:YfG]T2jQ%k=TyX
G+E+GfnqC@+A+s$=0DCEGjg[j=$/T0]K[*Rcue9IoYr,>msO7uNfSmasK>:X2u`y}T@f#ST.eW?@uZTQEfHPr
(0DwZ9AIeVjAHRm(=Uk9#0Y:V5=/WYoEs!Vu8fb&J*B:*<?e2*k7M!cCwc_=JgSfdHuB:e|i?J9nluc?G#Ce^Lbo()yO17F#Nx
aE4v:h`kLv@{I-Ed-Bx`@y;e%}u6!fXVXROPoYMg@FB1NT#d"hoE5O=w#o&D3"$IT-B!M#BfD!XXlp5UQu(Pm
p6Yz2)O_8M+=bSQ_pngOsiQsy>(!p]VYKkg-*<m;!93E`$<,f$IBxUIGi4e[>b!
F^M[R-s0rWaK"l<[;fIGcO3>2ZGquaulU"F|OENw@1!5K3a<B(f4ySRj?FbKdXq*&&0x#:tdp9BD5P&sA?R0!&!(rcdWjTiM.hKJP-6LTKrD&3m1:)/;Xu2WM1Lux{3zM"q~oxQr1.).Gp:iS71ncs#tW[>B_`-/cN<N+O#`w6.w[{39?|3|ZO%)3^>HO@YG?@h^)7u;uKluT},Ivpuk3U)6nLAi9Mn2>Y0RJ/c0[|_u^0xrg|6Rt+c38N(E^K)o&cBMWk)m=,,YpRcvXYm$wp,@dP"@gV7UG
8_fH"U"4d}1n9O1D+eIx4!u6B*E15}/vhoZOgfiY]OpMQw.G_B8`+GQ+m_`m:&X-vmgRL|KmF?CAv<,VZv;Qlry?.X2f"v5/,R^VjbD!A6;LOxJeQ$?ud*1.--p|Z#/*AH*=]J8X+O;qS-qW,12
FnXa!)
M.uske9>N`#Yw84x)/8!
=yP)PQqZUS&ip&>|wnF[V2sBAK
T<Jqt,X5tNQ)6/I4)gdi+qr?vED9ovr*RB*iC1DP+c7:BT$Obtj7%bpY6]P:|6~O]h-I]Z+*W:?3)4:gx+Hi|U%Q>boqC6$etgG5$:sf)=+r4@0=2:-ZpLoapYFF#+*B&+w?0o`n+tb/9JzN*I,[txk[<MRhWo?yIF$V$B(9&HtgB]+YE)P_Fad8-ReQKa).Nq9!W5
L1/e78jBNK`G]f#?BOb{?jpeGN:%Ya0lgSD$Fs:8lby_o@q>R
Mrm8OJ234
TAcG1>ZF:RAn2M4,nF#/l:
PKKL#_thP>0X3
]$-eh!C*]i^1|(V[FETW|HEtGy}g#_o_Tc7;+E}clHrE>XRL3bSi744e,d-xs,4RXPptH_u0C7[S$GM*cw,SdNs
&T1M;o6ij>c@--LSyC`0K5,fshFx8Z?/FKw8z^0**p`^nm|f;Q`>-<ii*@!AjF|&8w|4%dN.6Jrmm$aX36.[T32sp)gLbL0Dls5TI#[?:cqfeGm0pb&/=9/lrGBPC
Gdndv$5C{uF8&l[kSBRchoX0kRjT~ecMwKk#(Uwv4"eUV$AMnZX?N2n`lwaa]p}#dQ#s-JJ>I%:Lgfs<7R&d*$ct~WVXy.7mL/.aQo>9"`qBke!E**uz(m_Ki#"8[*ta_]~H([iDkpI>c&(yEx2]k(tn]8OJ31Se"I[EyEyc(>z!kF@T|!!lTn16!$>+U
:iQ>2ke)^YMt9q04Zwi8vV/Xu4JIKWUcKI6pnf=W>&;on&yue-Fn-
Dh%"qINSa9;x`F|RL.zf
Z7!0:gXYakwQyI!./m<wZ=qJXoE8nks
)|dk"UnOam$&8^$3VQSo73qmj!vE7QS)s_m@#Wq(@S5FRVMAZOyrNOx2+jo6?ge8_0sj3QX,vCrwhfUE_[(@?,Pv/j=%3khC9.jTA|<n0"D9rV-6;5p{%H(zaH$7aC*!5zr|y+
4ck%oN*U~i2JmgbXwEwsX59)U<Kh(MwRpJHKpNA.{-Ak,:a`1l+?fmZHwcWTAA}1E5)26(6Bq^~xB0ouGpfdUPV0ZFWKx:G>7_

k6:dwm;f]>Si)2.^5W;[|FX.F^n&D
>+"Y8L;0<Mu"ln}ClKF]q>he;m:@;MJf}%:jc_Ll1+|^cy?GtJr]%
{+H7Vmm(lX_phu&X<#
[=8WafK-V[tNq?HE<nR-ZAsfLo3_@;ibZUm1b(n:Imr|H(s6e_j)3:AJk/8)ZEoyM2p
idaPyoOpdKL8&Af+a
a%V$tJ;j!8;,.4]%o^F<?N7/;c
W8R%fbo*{>[<eH_GzEc66:vhg8[Hqspw#3v4QJ}>Tsnj%]Jf
G9x<.b:FyDZu63K;9f16C<!)WCUvV%1^iT?Mh"u~d;/=VD4uq>B/g<IvWsou/m[U%lI
^$$9,vo2G
DmCSaWRq1<wID;Y6+3n?_G]0>0[/r<&%23%b
xj.W4rz&[pvny7EL%I;p}^o?7o]$zC,MBabw4#Pc]U-pR2D*[?Kn~$h';case"ru":return'&evAM5Hp=*4G`lT"zH9C0.w^6-,u`7e&,N(r6]uRll-::-4+F`R>jN>"BxWg-^|@LiYyZu<Bt:TXAB-k#bX8XCEuI?1XaK,A3JMDu<}`s4e]oa;05WR0sP!VqC[[S?B$w0ZGRV?w}]
WG!LMOcX5dM=^mFZEqkgq`24+E2"
b,isEbLZrs^0+.*Z4G5:NZXEu_cTc5%&v<}@Z?/8C@Rq9MF9&UG]^M$A@;8("XvIXn74ZO7hLJ3k^`Pa(
2KT_SaRKwP^@_iSg}"2oVP&FVeNGw9!7gGJtThM=CZz[S]bq)G$q?B{3De|^H`PvO>5RUkBp:yS9I-{-YYz]U"J$}sD:W5mL]CS+.4[detPG/s6]R4rwLr^>sU~hY",+OlYOMc19<av^}dYoD3jpTj0!lJ^Q2EL@~k+k#qV`!#ijZCF7sdRHd@?O|0[wkYABa6PM7N*XN@d&&Kb9DyO-G_M^M(&9"K*@tO-R:ank%UmS]/krrBu<.!fxpn7lP:$@bN03l;z&->$r>wxMs;`#pS(hDm$[[XR,nw.mR`D2d]k%_tpF}X&>(t*r%iLib?28/0Bl%.q1jF/tK&
FT+O4yc7fb4Np[o{UihDlyO<!o-K=p4QYNJbc;I<9@[:pj97^nrvJG"^RePb4
UO]2-d<,V6?R
crjXOv0/i?*m)^M#<(c?64?-dmnoMU-@F4eapRaq3
s7~&,g=vT0&NmUJi6[g!H[{cM<<&SAN4r$#BTn9>8f3N#gBH5QJF+5}DIEe*M.JWV!3&$;eW!9Xmc@[*M$FLOL,Y/%CdoK;*<w:j]Fe5{^xmvq:B,n0[Ut~:?K-s.I;*&+gp.I"`ML*t2#fGL&P93yYvDg&e&6f?wW+j*pi
NPH$"E6K~*oCt!Vv#Z6L2w/`U>G;ekDTwjPG8GItpgH0^":QY8Og~"~bbcQg#O;&%ie,Um[:SD!WUSyZ{-jnFKoT//,TKQzQjqq0_5;ksQMois2)Vhg2D;j"_JRYPg2@&SbuMPj-(?/<OFX:mm7PKExDc
uDi[u46Pc1)n+vv*DiaOl"=>iD3LR7(n#]N2~g`pqH~G`[xd_uZeq)hO"%/;zP"!EbR;c8c?kUv0EnTBEAuh$!XMg?G/&;(URv3`eNd4e:.tcNT#g#VB^;RmjS/-S^G,Mp,,:O`_Z.H$f@u(h:L85NJuGQcN_SICZwNot[9uu#:B:6#S,aSu>Q|MaU,WwQ4M?-/9_eTL?m^fAT>L|c2J^+KPe0MJ!.>XGMBd/!sB$n
[malOJ"xw~G~#eFQD;[5(Qhwj]xlP+V|_!N
6;T6>RyLXv<-rJ)&&Y/2VgRHO~KM2:?tfbVN&Z9UX<Y+U~A@k8]AX"7r5k90cw5kTk]=wLOTL`Fj/VOoBZ1ks^^W8rEJqyd
WOErm.!DevxH*SS?ecH-hYLVJ"p&;~jup1]8WRgCZ%p`cY<K+qC%]f8`A(qTxEsB
e[]wf0QS)Gjwh13"^GS(sAj!P+k@5UF-*AWpjF8Me*y]63m;`f%1HiZQWQL;*W1)BvJ(GYP5z.J#%9H*z:7?bqPQz[CJJ<O2uq?5/)u8jS^gCZf,s$g;{45AJ%,DM,#yL?}I`ZoI#&>1cTB>.`E!/4hQt/urzx3OtnyStE"qg4Ii:CZB$*9vS(TSPr>Hv2d/,rPs1y$),O^dGe?;=o"T
YLfj3_t^4hvx
3DQo[vSL9OVkpC;h6U;kxSrbBfG5mn^k]WB#>?Ayo+AQo*69WU,WTI~(@4@#wjh1a&8S.f7
rHpd!fYGsgXVfb#cGl7&H08F=X):h!o(R2K?Wr?Tt]Au`"LC.x~F(=*TVe!*dxam4iKBM(H0l4>E?_?0Jf=@)9Qn,GkDYLDLL8L"5?SW?S7=kM+=bur7e`ULh={5}i$`@oTE]Q?*)@?#^/P"(p6Sf/~IdZ[rb"--L&x-lVy`^/fl9
z_/2pE1dK=rfRAas;.|
I-7xe^-3sD%v|q6)EX04O@[R3S0%lLGAF+E,<&}RG,NwB@(.CotD{AY)LNM1e[)"yWh#2q.
99lq-4qiO1B.t?YSZ`q8o`R;i(Ih}d&B2s^u]dbEd*bhDxWY1Wy:G.>c&I+VaX9jBKe45D~"qjA`cvNy?*h!hl+EoM+#h)KkWo"As=*-:dV=~+D4S"bH|Vy.K&cuc+Jr[`3[UWwl#VHY
I!!2T
vf@212sbNg/SA#"kkg"~4gd]?MGU6vNa;oGzyaL8S1.DUAg|f=@S_ZclihT$=B?vqa:cFvDlw{%["=wjhnadOX?U]}#;km5Uw(QvfF9-hi@UKGa$
M](6:In)U)CM4F|mTZk=0uH[;A4H%dNuu:`Y|g>H,U:dB-R]ra#CM_o6/))
(SyN4HnkCmB[;r1h%R;P$[)>9(2R]oQdL3MmNs$:Cy[g_jUa&I7VFi#Ry&Pr$")YU<_f&Y6GNG#f15VvIGWX)`9Hl4Sp{ITozPh/SVC/N[Pxy=sJK<~:MKPT}B`m)`0MbjZ=vAnK)]9E`d^jquf$R=.XYdysW,[yb?$S!k_2kt3Zd`9HJ`Ps_S?B6`Q&$T_X^=yK>eeQG`a./+
FNCy:Nc2[YAJ3m%3tJ";mvgtOjw(C?9q:xXt.LLfkkKrsl_SbD,=y#RA;Hq+N"C8l.eORbI]tg8ZgkP-9ViDi_+&^;,nOCr9NW$*(cW<R4PjjT+ik&OyFA5XJTX|[v$~sG,VJun
amM{m<c{Ui<
p?%QkLGp31prjh$dm]H9lru`@R=}!Pg1ko1`,6M0:3K-ygva(Ex7?_yOw]
@L}p!8AFtLAonxkXNig<5s__c,r`F3E+rs=Qc#(ZH(:37G$_zRgN!Ad>f-E@yRbl.r}O)3H=Gd~ff_V-mx|](&b$&iZ@a1b0hB&2_o?OE>iFOJGA(FRIh>F/pas^(GfGvbvEQ8XLrKHG!O6_}M>NYMAxkI7m"aP#g6]MzOtlsWs
.x9x/C4CUZDm^KA@suFB5]
Els
Ys&)YU`Ou})`q7Y-*HxNvEnro
5WCZp.>o0Mo27x_1_U9;A,"Pp@+&bYZ`m!Zc;-!J0f1?Gt1**m<hGlDuG-&{KJ.Tf$tPMXj9ow6tJi3RDTw=VU1s<[tW$d@8?[)EPxAS2jt-s+Jm"y+2
|pS<XLGm@dn0M>_UeQ,X|q6"WrXyJ&&>VZ]_|b`uf2sCgSP_~=q6)gu,jS640ND9|vj8Ns]#?VUDzj.FhV#^`U6%x`i5yrM*{m%HqTFX2G-K&mXIh).sB&w9x<Zy$#iNFw1E_C9DmP*#~k0qem}T>:D21UXgt:[[_lBZ/IhU_iX-pB;[tYy%&rw+)hMjrZ*3nC(YT`
R{@>1],7WRd7*>Ly]6=!tRk:0yw3evq{Kla"JJD&)JIxIlrNfqmy0[akpN2(o8,(SQL<fC>)#7r^KWK|dKbQXZi;Sy+uvpb{K^bB<#]Eh{EA#;4bM?M-2=am&xRpk{AERSK8m=_Mlg)|<Tf/j/h!8AuY=L:[NhKw1KVjG,.uB6.q-ZEfABLN"p<,MEOaO1SVm,N9k[QG_ng]Kff)rTL#i2OBXiSP9(yPT{^9b"vS]RmUqJtsS):!nK?b#CtNW(4!]c_gc([4=y6w:A7&EVSk32Q
pKd`I6R9A5A,xcZh#/=>qvvQw[XqvV+MT{HVyvVa
-Cpvti7F":Zg.D~Lo]`]RT_<aS8_v<VXSj%k
^S[!p}^XeP0KZ8`:M7Y$YuYT/kK|3&cq/VGoBn;Aul
i!.`6>IEZf"V4Q1x*qh4=!LH#y`eI.<
)tFrW7SxmrDCTUE_pwTr[J3lRGW5*H[8uuK`g>+=HN_/e?Wo:a7JQ6)Z=C"6eojP(d^&Dp?bT)q=QaDKgP0GP)O!%U:8zW%M:v,U1nnE(xkg.T5(J4m]zmyT!A:A5m@B}^9^1Mkri+k
KjI"c>Af5>]V$vBU]:!`9uuqe<DXuiMLKie^wH5l(c=bGt
pUg7gFE:p^/=TlAbTqAwHqYu0Q?WMm(n`Z28F}qMAxXsA,Z1.c2eZgPw"]%{RFJ?i96PB|mn.5uE,"[FO**^5%jAAN=|tDSlW2yDj=3u7>Ggw;j4eZh~3)WQ)vAOkI9O`{m:@dj/9$&@+ZaO13j^10Kxfkg=H++Z/8.@ltvgxC2|5rw^HVUZ7kq~8a^Ys.gU36H~I0Z7<n]ym9@$P7b}Ew>qUaRQDlC:6?f<CS<!c%$dGHLIdg$oYCZmF`=aIF%-MS6`y23TR(Y&NV';case"sr":return'!c0@qbSZ+.C,gY="Hd-mc.2,^CIdTDETgSWu>a%G=LhD41B7G,UuHNmgSf#$s)$MSLtJQcZvDD2TjDb&>O5r$gPkKct@&O>c&F:v#afo]tUL"/CN=s`4;&M+#(!k/Guct?T!kgog`w/Q]eE]4qe(9Dbd#k]s99xUOFJr!D,uF?1n|PJDYUVp%CMSA`n-r_@5R<5bKh@
kB`h1G|D#wg=4oq-9@3*5?PU~<Lll84
%q/W%4,tc(#H;s><^T@wr!dR{-FcN
tlSPAA*yYAR@^m2&hxNWjB!Eb+qtTv$KdMf2,](oXJ4#5R_%Y?[V.+weyf&i`o>qjTF<NoNb^/8DW(pBrPU^k//e>vmD.Q9Sk5S#H;`0.q2T[f<t~CIykj=KI_Z8Hh7>$Lk4B3HHe+[gQ$cC]#Lv)Z
btb7oZT+N-=u;q]TEUY
kd&RE:6yD}y*!qvmtCrI$=lkm#1a0Wmr#sQ9-6p@[E
,3b?VT?rwhfBQegi+"V&*HjaJqj%Wjl!T?tdC3A$m%"T(aHOA:2L0wWZ;b{v<d1RZBhd{ammu++LhVfb(c>Qo%0hucN08SU!o&`GsqL@k-3??5z>o,e6V3^(Z2x(W&NpR2c*Q(HlGT2Lx>oAxL$_$VvjL
|6`(i]bW_M,A]8yGB?wo_S~a:dpqR?iq+9J
zJ+[OFaigK_R)brNw?]"IF9?K2<TM,|128h6ue*0g+Mebc]`GRc2?O[tQyok$;u5LX?%&np(0e|Y+wD$zOs6O$z!Nc$W<ux5SqSE0b6P2LT>
%JqHdB!rEdxfGb8/:te@%!yg5R,.
4>#0|.1wXK[LLXJiwv<x}9$u]n$"(r!
#[T3Qua2in_JTDqlGV]?2,st?DS,|:TbjQ{V
BriX`BO7H0_MN.ZJ,=niM[t"sJ0Rq^VfD1-QgQ$jUrk)i8TE+loQpMCXcK7Z%4pM;~46phKE>)^nDU2z8^g:qdve_"7c*(H?=2[mb.2r)25U__oKE[=:qa#((Q"nH]LSJXRNPB9ZIJ)EMRdqum3YXh5@^"^cXI]Y
)U/&y4:ZjO}GzC$6Zt(urCF0K@WH[lw)afZ5C@!dD(Q
h%F,1VQ=+Ff/iF*3_k93?fVFT(#Am&
T^t1N$jS-n_QRWS5K;RxSr7K)~OlNwm8j18fhc
BU-h_BE`k@Q.U_KY/MY:(fMn4LiV+KPw{x
B|fa.J*psZ#gN.5W[9Hi[=]$l/j<Qm8Tf61mm=((f="@[|"#LZuIWt9UR$/r0]`40)v>lMl/wByzxI
aR&5TjAO39j?fhZlRxRN,h
&?F!?p3!!B3:Ob?M`/615,Q6ekdBYw?}fI**kQ]_*<lFT0*B1])d@v2|9S+m8UaZnzIyME$-nT4gS;.Q[qbW8!)y+$xi1$)ve5j_81RS2bKN#@^{<TC9ABnj]H+0]WAY=^Q,HI2nr%=~xvd,:Mw^@X@Wekv_Xq]wm7KL<K7mpK#W7nv:KnHvhWdGng0FWn4}FotSy|M+#NZs5$+sT=k0AwGb+*j{[y(DgjPq#<%j6HqQ<WI4o_y8X_+E=~
_F^h]R*F":>KQlz?YgT<4]9k.o>0CyyQJpeJb/6"_f7*Xp4sg`j7/HH(OtH!px&3X/b3e=37"tvL.SB.~T
5f,/n:36#Kh*@11-c)jO*vgID~?6=OnNf.?#[Q92>CR-nK?0%
-Zn,JLp=2zm3L2;z:xrMsmwjWd@k+s.3gH3BTWjD>Tq"r%yI9itY;-@W8:bUJKck
BsnZ9?tkXdkt]]mDwA**J^Y=m1L2X4`SW=zA{R05EKmL4*>$t.FTi)jVD<YUt,?>%iY1qL6.qW~+bpX1diVh-[e_"Zpb91,h,NhtzGj+5K(kHr|;WIg!)tqg[ki3{&l84M%hPE#u9ISWZR#"^@,]Uji6Av0<0Oc
1:yJ5S()bexc_J8a)ZW]G6l`ipf)XOB4=?G`^=W1q+Y:YM|:cn;kA90E9>d@T:
#~SV;uq<QNj/8^6oNnk&o^LiS|463,Ub*[[9BnwdQ<[okB7*0vaCbClSakOaYZy}NIFJu&0":ac.Ym`FF#sVm$WgGLoTuFQ%%}

<t_lO3X]_=gXl<3zG<--0@FI"PW"k3?bm8Kvv&Is*j?Dyo@Q#Unam*&Ce("@]CGxDS)RbT$Y?~FbYx]HC%
bdS*pYfQ|ni^
hAP&N/`2Y:P@:
j,E[]0XvKy@@e-CXZ*$IviR<A`5po?qH3V?/J=n+LmZ"&
)5*#`4iwW,x_6GXq9s0/wkWbKTlIa0A";`2fES;5BXP^UH9Z$/[mBPjL&W4W50;u>MI%ns/oD,>S3$Bg`"KGXe[Ila=QY6X*]J*XrXRf6aT=aHI?yW+M23Jw&M>q1G>?&LMs@66zj]+wF:q=_c2eFAEgr`Vy<Zt5A+t>Pi9ifpm3Z3v;ppb|69/><WK:95ta:oqL;^sG_U:v!5"m[&jTO1V#+<c*;`.h[wBrTRH/bC"Y7$[4:~5d?l
ww]Vz
+.<HdRI;0[bMz#P*nD3E%5pWK9TW*jBp1CKKU
yZoRK,)j!M+B<3c>#<kG:Aq#p$3(J*_MKrg;oa(nqfHo~Z_g9tHkAwJ9
G]II@27
U^0rA`vJrSvK,+`SRI%KHFHiNSfb+ij_]fC{R?T{5DJ|LwB|Pmc.*4i
uw^8v!YavHP_u5>M?j`YR|^N+JmxV^F"Nzv8,Ybku;h@Hn+(7`/~]E_lSvSZs(7vey4](0Iu`SFfp1Hl.0lUKqg]P;YOL0S:Guq[r#`ErIqiR#q76u]9P])/*x*1ntASpZ"p5*y/P-_5o;U::=G%38HG48`KAk"}45EXmihv!Yo)48c$l.)FHaKWTfl[`"cA5BVVJ{dD?~^K;^YY@b*mHtXy8Z6dP9^+_X<p3Q;DZ*1Z0Fh
5QA>o}7KyI3.B2rUm=ScSC%a6)H>PxDmm[F%brfvkvlD4hh_Kxr(M1(HS.cQ1BFI#HY6xPHvppJV4<stXO<Dxki9f_Mw3(6d"8l8LpGkjr8K+92CT$02_*=zWIk:=oAzVy@v68i?p]pq;XlUVs1?!^iJ)y>0ut"P@0!>`u11yEp_6IX,l34,gNL{]*z)"NK5w
ve-[p?r?K`(S=HdY1^,C_Z0>I}0F2vQPO:kn[_qyTiBcGrK)Y^kOHfXAJ>5X5`;dr%[j"<o~en%P>?T59
*f<GB}JwI7m&/>fn<mdA
&svqB4PHwTKx/.UJQspP7Z#@jqg!VODHrSnXf-Qr.3WxLM(&a:i0obVHeq}RP?o4_vC1vp,p|ofZ@7UBio9TUhpX8(_UX.^h,<Fi@!b=3Nv8<+jtCY*X`P]3$Z&o7+4:!L|:PW8wN;B9JB^,.EOpV]+_q9:r)2(GFET@[5s4-n#?<Z5l=c9B)DhS&_8eX+>fPQ>sFEY
mK.K0h[@si+Nc;H6wg_o5S2A.pag&n@<._mh&`qyLc49"=;6U0U-EF=;.q03tWK;=X!J*-a=MeJGYe?^!6KMT%/q_Gq]+$Bd!fM;"[NR$Lmbhx&P7m6=
4Vhd:<x>0n]ov"!]*rp%UY_rRo*BW"_AH=Fe)-01bdcrtFS&J)cJ]]s,t*J<5!kB[@rw4C&XY<vz3|lpSc^v`0:o^[Ly91cL$:,U
5o8$av!gpbp[ZxM2*ex
T]:X6tE+Gx*S`0fP?XI1&%KVrjeD^5r>P(,OS5,llySD2b0f&M8:kLMB]sWp=6)i|ss%tR
pJ(Dr#Jb/2QZn|fLfwQ$;~
o5
<p(HbO4VBOP87`N]@DkVo%@/mdyW.KPL(xl^!=iNhYl$^`(qXkEK;2Ul7oc#FHt{gL)+%;W(Ex1t.{G/E$_XJ>1JV~eG8:]Fp#f})ErjEf@WXw(vTS?VY*g)akdVt0Y&$H';case"uk":return',ev;:f{p])Q*#x$9V8gCM:eF/`*Hs%%qo.J^x0<$j->$@keT=RYbqJ4-9!1h,d~
gVPENMhkbw0$Q0/R~Cmg"n@
ot>gj]~c3
PsnDWrvMnD.t-SskqsnJAez]CAVIhG,GvR^6Sg,a)tUG:oW&$t.U1,)8Otrg-yco_2E+lwcw/KHJ~CDDFIm_%r|tmucp^
#58Z)am2>gqmkj2yA`ACa0gaBLTZ):zmDWUP[a^AjVv`J@;.aZ|%~P-Q:8:x4[Zp$O((}wUXyEH[ht!a
v(e^6TdrI}Dvp{x:Oz)%:pmZ268?(|wL%Hh%m`ARoyg1nN%A0>Rxz%e@tfMPy^EYA#
>&%vFXDJ7fz-,]ZH8mES<SR29^0C}Uy]DC4I]hFf,BZZs(7.qhO
V6(5)]~oT8yh21U:^;Z1*$}DAj,Y>ncdGgQmyMm;khPWG`(o:,hxtJl&p>9tk6a^vj`k;vobx-$l2_
j?xlJ:,6#YW[uWN`(U;+)+Nr>,T+v"-
1tySr%;Q%&wp9G]jgi<<$
C%uP>(x2ZGYCr5767prFqQCmU]x;+QM%$b$r6lMq4cqH

F8It*6nXJ$h!Xl[Q!PQ[-+_)&&-lv~&Ua/7Xn@"!T<O"
a^"8nE:*9_VS~t.3Q*l>,Z"Uv4hP*@e<@R+w#88My*cpat7oZW4d*Ue[7o~:b^:A]jdA),L920seTV:KsQHIv9$7hmkC5uv,6#WdRR[3Fm;?;Sf]>j&Ii!I+#U5IqJB(W=@aBeq-mZovMK81BlEO~8%$=9,#~2}FWEZ-NfN-lH06gD^*n,>t{O]h<d_"9(jJ+^AJ
4sx0xf?SDLDJa,;pQA6Gv-P!b>>^m,cL^Zrf28SZG9,L?5OZ1/aH7m:em.%KVqr
q~N3h~,^6Y4HeLILxz
jlf;3$nimN@.><-eZeU7Jt*5QuM!,9HsOt>A;yHR!RZZPw+!>L<lxI0%pi|oJ^&NS7j7zlry!_%XLp^WAsbMfVq_<V9J3<T3V&F+ZH,hf5%Z^%MT*u+Q/Ja&#t4dK?&E?49+YgZBFrMTS:D),]7B@)!#1.
5ZFgR<wp!Y<r5oSo9~*0N`j{,m8_[M0ztf6@jfdaX85D;_OQ1WvBPE4Df7w*
-JoeGR&pwQDA"j@6!Li%Z*MV@)uS,>OY2L-^:]U0GY3p%%NdT>B,7&2Yj_2fU3"6gIE[/e"G(^EWR[*U7/81VkqQM.Xi?-Pd
)nwe#K$46DKBYK*Awr>uYZV,^+_Vxl8aQ%,:Wyc}mNtGj:3wDfc[-y]L!wCA&oNA6)La_]g7>]cKUdLnYo;~pKd0GpB*$Z&r)`P_&|&=v6#2y-IMqk6]1Gval<.Oq|?jY(kr^pV
RD9)UT=3/-TB$
7:Y4`]Vh09i&/)"bbdjLu&>X$AM;Wd<Cu<^%fyHT,3!>.~#sd>fw*E.Yo|+~WNCUfk3/jLPT#`^QK83m$)!Yw^o8kg?bWv``$<59H8=dZ>?(0e2zFmnH3,6HTT,]UYie="EM)9G!)gX1C0)nWQp.^LH:HD&!Kea!qzVUn`7#]5aIdhIK#,.u!UW)cI@$#:q*M"xwn$tJw8b^3pS*!
y?X
90z!5*r?a?^@6Myzb$h_A(U%_ZY<bZ2aOJ;mM,W+vfL8H1E/]t)m!-YL+a(|Tr[Ax<:2WCU#ohQwpr4{_I[{8viu@UU$Gz&nt=KtoO>57Nax-~39!?+!-pn15JM;MH]}/:m=uJx1i,0QM&@hDAadP8ry$;1TATe[@r]7&.Pty)tNS~h8YhK7$/;v(6Ey^L%~w`::9.Q1moWs?NUM
!S+[C+brd.a)lUW57)<[/FkgKJLk!97PYT0m;JGi
p.^"HxymmgBf+t`T7ANtkwF4<1tofOPdPA:?%}d2!}HE4P<xB[TW29OWh@rgCktnE7KQ7H%
f^%1tOc]OzCF!wto#ybZV![O:{jv>(`]U,(@Cv56O]Q1B,Gf?((e)K)(GAn&V^"0OhpE8k#rfi5Upscf+P"7swsNW&o.N#`]7]d`f8Ej6.(~-q^JPor};!h#(8nqGwhv+u4K"KdE@du>m%uXp4>7ALT=,:k*yx/KIUb{Q!Q3hCQq,
)}jNOPPf=9P|%p%sq7GjV`D`o4e3"Re!,4)]`|?{Ke*M(rUbfZI)?S2C-cPR#t0s-^,u_m":tN-FE?R.ei16Q)HbD"S;R2H`*~D-/QkrN=_YX2+NbrO)<B+8D%DS4+eRD@7m29!p-Om.
~SFhJl#BK
DC&WjU~d5#p`<kt3|]u2.?RHN.c:c#xl%jRwq<T/anAb@j/*x,0(#7ePPIbSEhPrrLN.]*%6|!ggubmn@VKN]G%F0ljqca3Ook^IT;<,dLY73u.v~fJ2y*7iD/9a<ma%CY%N],k&{2riW!f_Vokt:WUgQ?kTZ>))D[VKG1Wsc[e#/BA0^CAS2L.lvxN<3&tH^ATGQ6{s^J~CMjY
-:ByM7Ufd"y)AkPW>&%Si3
9[)""DaSqFx9RsHi
DF0-V$u1T5pB)b|?]c"40UVtq
nL7UnCiI3!e98ZY
0==L7x
y:W;rbKJ-e
>^Ka`CH!&#vU,TQ0)G

jG9^L
ZwRv757W!DR>+%Ix7&<CA!6b=+c!H_olm*7(2?"2pf}Y04"Afs9m>_[>F:$mI]gBr;<_an2DG:Gl6?BtNZBjA,{&{FuSl#XGI>^vKO)`*?PqmV#wG,0*r4iAVhUiO*`WT5X9v,qF{N;db?G?ZEeJ#0RXdBPk._>vt9H,&Rz!Ul
1K:,G+:vlWv^bbCr-`&qkx><R@ut354OH:
Gf
X)l~C|u1!*)[Q^`xZqS@2ks,*V_cA{,&2ElX?%g?9ED<4PIArDO!yEqMwJgm+N=*18xpb:<o>%P/<([=hXr7GQZ?Q&AGhh<932rkE6@C?r^e!Rs__FLfojPYOT.q#ly]X>j|ZIudfIV6eN*eIJX1@"t&x%A4KMM"hz!&bh2Q:k/%ta49hHs]Ch]fhUaO?n%E,EajqrAWXhOB%;iiobQAqttLR=uk@WTFpH`2lX%s@Ra#kaN,X^2[7APdA_8?M7&5h@5M8(Vouer0%$l&NY_5"Xbh.;r`/l?yP%N]<ZhJW)k%09gh.$
kv<^On>v9]OVjBWq&i0DtKZ9uDC3_cCZr<2h0E_G6TpRnc{MIy!2~2=,NWU2zE8
**x
s@xhy#4*UWw<AUMsP_
SK
@Y59*HxI@eKnM5$DKCd7C(AVZ]sdPf)Gp^Ii-c]6#*UjLP8<AYHW=5Ps?cSw!OI"LLSe9xtQ.4rY&NB[K)id
Fd0Nh+4w"Vy+q5Oes7NDpM1zc)[hqAa!byyNf1=Nc}x[n7n&>}U#iNm}^]4muIG!
6^6lpmC
wWyTWx?9Uv0;>Ws"}RCPl+($^vYJTj`YPL_6k)f:z(0imVGbS(85W-mRZMtl*SF.=m_EHt@LXwd6W
po^Hs
hSXEOA&&x-3TRpL6{
!`I`+i%^CjGYE+5M?V[o$jcL>
#H%ksJD+!9EqVy1_-oTb8oPFL!YwTZD_"FjVxrTXMRvx5+6OIu^O[E@*R.AUJEHQ~7bt6_of{^S
spRE^D9k(_yj$qF,YS#P&RDL0lpIzrl#iuiaMTa)F;T@A,=iCCXQn-REg*o1|J/r)FfvB1$oHyp6xWPu/KwsQQ!K![+u{b/b%ruMco@wJ^zoi%ev{,m`{3NyoiO[Z9,W:kKlg`2lvr>bs*2N^auF*Uey&K"UtHJ<t0mglN4^W87nn[%<9hlqp
QM_D9UA!*M|%(<fugoNtf4#BGy>V_5e:u:./c.pcfBxP
+h74t)KA[CsQEvURYTh&;T)-B0:FV$mQl(B)<ViCX&&~mZQ{D%ct`)T38ZL~MC?l=I,XiDcF5e]w7!c-H"nO;p1u_)71x]?Ca!5(1B:G`U%zQ?
/F@`F=D@BqFe6e~Ax4KvqE=fK/C5y`+Adc0(aI(tCbaD$tCHyJ6W5_0=Z@7ND)9-0(l2Gh<k4-j+91oF+Yc7y
[e|h70U+9AdOt6i(QJ&4}0CF}4%rV,/er`D1v#uqr"WwOQo;)[#ubWqrP.}sG<nD}^r:0FgV2WEVr$9rz+.,|R2Qq1VBPUUyy^z.kw.=]6#d/m-h{F*VibWyGo)';case"he":return'+s`0:6KZ+&iq4.1ENu"<
S:`d-[8t)Itqh{`-P%T@p<.XIOUd&,^+<!
gAETj@Z-S!T"D%G?(q]x;^C=axN!ni+-EK;c
&t@#D-*F8YMDR9%*p+#j`=uG1uyUq8Rj0/GESw
zvx$N(+kJjqv{PAH%qR]I?iau36"C4BmaAZ23Gfj-0eDy&as*Z+YAD3NkhIH&Q7y.+EfOf,fmhzh7ktp{fx_A;eZXA,;~XnpwLy4p2iUcAeUH$|8Ns_P?eJilI)pnSkf7o@XY6`/>ZLcFs^rJrm<u;PK"s&>2D=2`IM[!4%X<P/AHdnLsP>c"=%?O-k+km=*
0^UdBo.6iS1YSZ.&f@$ZN$u%bdgxlg7:n{v4
H,AefGs)ScAam0
dNLq`)oX[|/?3tk(Z&w9ig6&HFT7[GOtaWtn:+VJ
bFKMk<4apbk1jI`3c@)/@BZn:TGf{6ihI$MpIE{^8pm:SJKb?C&B+<fol-gB~pXaa"MVI*uHYWlpkb0U`F$I3gfq{VlWcKp0r56&O4<:p_|k%%vH1%)dCta%~V*r1O4r,b&(&m$ma8:1^(gt`c0WbG~*dtLS0){-O;+)pRzSa=:0.[M6&uxu]>F
LfB7?lSI5mEYrrvD7jX/l@b$Tu
!Qo=![+V25z&yJJ40pU`gv
RGo3[B5Z<DK6s,NVmy2,:7l/>:Nd%SQCwVc"v%;^AlY.7[fsC^R`GGO+{%*mJB+qZxa)cN"V
&:@+<5"6Xet[yOdn&Yf6417)bh3}`
[#^@WqBMJf@Z0.BBrQp]oaK
n^?MdmAz[}i-2YvEqv>"
f8V
z!C2lIB#
(P9q?)/"%$v`<=T2O5psf-?PmC13b*ys^z]Zz)u"Ovi8NW#1d("naP*K]gwaAZBc285t%L<(vJ.Yvpgz#Yjyxbfn>Vnhy!/VX^&*7eAXs6%{Q"7nEe03#bf##5r1*k^R3B0dEVR:&d$$6MKOu=):uBw%ggT{0|fRY}9&&DT)(:VH_IXi`HNk"QnwH$+3QF,Po3<=Z2R_E->@g7V%HKcx:&Q{<dw3JdKHx-b;$+p|Yc#%s&m*]mk6f7)1
>Kt_qbL,?,f+GA!J5DZ!P+EyFKYZFfSi{vP;tPrl]YCPi2x43Pi(BhU-8.![3?|R`O1:T%:9dK,%kJ1KU_~I+]wj`gr`]t8(0kSfk,jGUD,,ViaB~hm8Nfvfa+aT;oeO<UoX0TZA<*w*Z6ZxTKM65%Thb;>a%UcvUon#A3XX0RY*;7#J=(!fo77>dM&,BUQ`"wY*}cg*`3aw
L,q{%L0/NWq77)Co,YVlwm*z)e]Y/}!??^r29d,=iIUS%Mpeu"3c_)>#SPn4224p6FvFvx#?U>mKdX"bm;Pf9~C2$n2Gb2>/r?MCoQt%WMb^&}1U?KBt[PKab{(0a&)9U`E$?PKTwV6]nkV1*K=fm7dxi4"LSP_"&_W]T^5meeJi1{e8mK`zY`;:QAu.0>[:b(bYBo?y%/ALQ
<|=J+;5A^yt2m
cQGiwa:jNIBc>JIIkF0.f<#sF%mWv{cyd<NM#%qS`tjBu-Ab$m0Pk
@vf5Vn.O"9a{dayCq+qO`}vY7xqgo`ctEcR!Nvb^#IVkg|1&2W=92/3jX4CL)wNv!`1>8!Z&G?iA2#_CVOZLvr%dm8eWr8[7QTi:]j>JQ5]p0[Oq^BJ3*Go7@dWlF{8+$E]=48Rzh)1YOm3L=(Cm%WF<ZFj,2[:u5dAR(V[T]?ST;q_}Jxs@PA?_.z--AL"L&Eub7
5vrK`n;gO8g<<8"pg`a+8FZroo^]?XF1hB0vjUg_la8>uT`*srYT2NvYcUe)SFUWcZ$OraHevNZtNa<;=(JCrhyJrOG]Qz^W!_uu>)y`E$MB1>Z?Ksf}Lmj<Mgbx^~-|Lmc]@/=xI"DYFe#pEIa+7}RAODD#Wp`viMc*_$,6twXfG:[Aczt"_>",df`Vo6,Dx]KnV$W&ea;Qo1KoV")U7S<TqNc.;*vmVGE"%imzQI^oEZV812IZu"a099.I_<h9X}D|]2DeZwyl7$1mD53:>-MLnoB/DuZ0%dFx;D@j#8=]`ux<QuAl*oQz8Pbekz^wIb4&FcT*c)a6B`O|bA*gN?G|&^K)0y/Ui=J~M<5/Kt6tuI%
JhH!ynqTU%IYyP?~E(TYgjr}:Nhpqvgs^`axdO
uFk51O{v9A9:g$d@
sEFv$uJnp:$ufnHYWCUX2jJw*HY,IO`R*fQwPP2j!}
#w!tcK&I[ZvMA8#n]ep@L0_4<Rpgw;}OH?n@o%0bh#BjI5"El"sRWEyh>W&37yZc`Vp[Kau>S:LddD4fA*[fh>kK^orrEoKCT_6c`P";^bKB0[U?BFeX>8XVsJ)G+8{MTl;fs:bh6n#_u>LyIj655k$]]<X%Ej@t^JA6RW]:C%dq_ePWOsA`WGG0T=zx9YrP/FxhTMP;T<Qj~k-oPBFi<9a9r(dDh/U*=%bB.
m8.E16v+[_;LO3ZU:
tiDm,X{1;%5>
0(XRyg!Q';case"ar":return'-c0;;5Lp=)R?lZRO-d(84G,01IZKqth@k#`CAe*Yd,=tY2f$VE=0BgTEAEQH]uHk*p#^yu%lW]?7_&<t-wr_+31KO/N`-H3Y^qUy=_GyB7@ucmajs.urKuluIx_qzMpXcI)*9pOJyP%V"(SmvTn/6y;(rmH6A?~nx/`uXW(+x?__h[Zbmbz#zRm@i-1ycW/.r,9E)xp
ooi*-(]9}V=K2]~]an"S42h3MEP3CBa6xc]A)u_pxa1%Z/4,?VwUIBwK9_nUe:QUw>ae?RhUR;xMgB~h~T(xoGGH.UH*sc!5,"));PfNFPh5l4u`BKgyk2<xH`ZureS._YfxCYHm;wgL0so]8hh,E+Y6Y5g#lxvGR#?mx!nTzG_YhDgpoHIdy*GU
$=M&-I;Tj+.!"u*p6p4P$SjBT0>|B9_@k
lqz#1nW*mZ+`x<ux)Tx2U9Y(?_EvqruiBtpI.C5u5m@;PQLmahLDK8,~-.+v6+1%5Ea<m>>/_%cn`wq(WMyk("jq@?4D@mGt]d<O57){l*#Onfy_y;

!L.W+b,O]+&-rWxK
dg87*p;a(Bj_0SDcvyP&c,|#61Z]UH8?5rL__FhH-^W[aXLt]<B:lZ1_EAb+"EB4Y>SE!>0pEj!hj6Qc}<0l;%/qz/j8<WvWLO&b?-nxbtU,WnaD#mdEpR$=n.IY}R-A)c1mIvm.SNO0tQ:l<N3+GCbK7+
=OoXJs).KmlKem34`7OmAoDyN9>+y}ONAHn_I|=Gk3+lY1[UH~AEC;8._sN=fHj[_g/xLx,A&y;2:<U%xV5v&@d="ToX%gsUTw6l#`Qy4v-ImDv&Sm(ai[M
I!;gCD)e_I[-8m63CEM0>+sR.Dg:Us.@
]+xC(s^T~0Q4N*.AFkCGdwhGCvs#_S{a
7I5tt)48U:vk3G=u*5U>HE0hv
N}h49@
S+26wDLQ9
?8[hi2:M
+&&}53FZALoQDyRf-4r("bR[vD:E+G6uQ8]P%uhRI:eRx!%ZJaV+uhCM?ep>NRBLKUJn74/B0%V9tEKVZv##-z5rjO"::x3W):X`QJn?9Mn2oape3!
(9fnT<P"(49Db-OUI74,>@bh+s`y(B]n5;SGfL1C`p|O;,ONO_)2yo9"L-p>]apkT22OSq_mbg_ah(6:6:"GA#K9#-e3X2N]0<lRi3FRdpf9=DIQ[ovY0nYIz7y<5=MX
R@ZQR2OnS++tBJV`[+37v*z)b>wkD).m.<$l8?qv[y%).IvD8bp!RA^]lS^eYT3]b{-2971#?IC|`.V;1vS^m>"I=
2*&{$l^WpO:s!sK9JXx3y>,A[aG4><I""Zd!F!3hoDt}=@W4cDy/hob"P0?<S:G<8iHVNL7fHz^&Wr[a%a5:YGVcalCv3J!zBv
NJJ(~D8>-#sEY#/E392OIlzt3hD;To*?J2LxcSOf~]~.W^f1B&o43B
%qrZ]D9?fh+>uObqxoOjCLc<TipyE+NjDh^T74rMPZq--F#6Xt9dn;U3WoEte7/,<x(6!q!ajiydw(BGpeR)eobj_@J~Zo
?G*@!J[Wn:$XTE}AeUGqRiuS#op=xd_tSNI-c>yUa^uxvf5wC0.F6RS`IhloKJ@o*Xd3IYX<P)]*_Y[P/>uI52o9i<?<dNxwiKbj?2v+8!F#T(3-j[Qk14sWh)em9AoB;K]
VV{]N_hyzRyy*-SmaoZ8+7/pkiS-VaNe1Sm9Kg=$ssX6"@0OZ&C>ut=h8[:K2]|/cFm$[`Qu_/n"0Vtedt2Qk%a8/afG~1X>m*:uMT//tb/K,2xlUv)RX@"aEc"1a6l7AqJfu::6K^(={R$Dw,m7DOl(Th!+&m-p!tJeUX~e2wftFmhhX"7i>em_^p$e+EB4|1F3=@<Lo-Chb_:T8Zf:HBld+e-&B8*)
h36)[O2s-|I?e}8X3U$ok@R!*Hi`3oCYGvU-M!K0nU-.2ZAGxGi)`vIec{*fPE)_#j+<Vf5gp_iY*nM(m`2<ZC#<*quUT>flxh?P$v;!!T`"E/Af=9PJKF<!ce*Wa_+/w6Lwh.rpSuPO!s0=5jtu^9<mE&j.MlbXX*DIY
QSShlXDm3Tx^!5&vV(qtIG=cTEM3h"?_@=E73>*NO]h0eS3^O|]lRTs_lK#AEG,1=9R&N{H8BXKFZZX0SLE{6v,3,N+-+6t:^jImR>94HLZjQ)W5jg@m"hxRVyE7cbmeI&0~;cG$(WIrZwD/x-^Vg021>>6n
_?bH^@1$(u{F_.,=SEwbxO_0~aAQMrHt;Ei_?um6h5l2#-^NV_D66(3R9>ZWXSECZjXp-S.$n,K?=$~MM4<TWxjx#WLp{uMOxy:LnS/Andv>.>(^n"H$&f~g5Hy;i<R/LCjEfLjTUDHIYv:JT:"fE=R+nT7s881y_3[2Rfz_sT<Dv1(u:99cn!5yE/q@_D2Ptr?0cNdF^7`TxN&!XxP?N8MX&$j]hsHj&j@Kdl@=.eCP6j2;u^mQwD;.;eOdCA<`ok5lB$}3N!T;{umC8hj$SG"dx>O$V2hQqtMz$R0Pk$eI(w/i0b736slt,qN=./DkPKOdPf38nP`gVFB!KTA9Z</]~*RuP^tK]N03-vsv*vq^L.aG&w*"&
3
oT9Nw^|dMDEUj@z1?0LB]p&O5Ul:^#"]0;=6bv;nGKDJyoL)
Bl9eS8#oS04_.,<v!J3[AP"8!2X~Pm1;;|&6^Cc>9zDw1ZtOiZ8.+p!z1+j`,bR<9mA"Z24Ft^KJ1Hc[a0KTE%7N@Wh;O?FzL8d-GMZ"Fm`TCix&Svwg$fP6&6/RtE^Ba;g_mnu_Ck"EGd;=c.TP1A@ZRP*?B[h,/_aG<:H7
_f<"C2U!LkhJolX(S?dVf$vx<$"c.ZJ>^]0XGs>L-COJ[X8oG^ZY~L4<)3yD=[m8gPCs<8|&v4_E8(Smk6SKlZHFSMZ&cAkE]M;fDW$/oJ>#Y1b38CF#o?.kBE_$10@9/H?TP&w/+WJsrKRZ:d5ntJ8km@vjhV`l9R
l(T?^Mi.+]9@x(K&O2RL+:aT6(?.pyfn,82)R_g,@re$.O9N"x^#NF
f3W7V$7&XcW-L8iCv)#^Qmx::P+p!o?]%37R0d(J=xCfS*Wvf;&2w.==`_l/ghfE?C^);U~<=Zu(2T&-mVYxw<y>:w?Z+LGFF67[Ws%8_XyODpd/aEFI2e5H#rwPA:i*!&h>_s>ewG;r"#:b<cOiJcpS@Pxll)M
#BWL!,jMaOhH#!LLc[FB}d:*@1:MbsznL,CI+k!tG3ZVg>ZFa_`J4:L*,fD0Pv)K)Fm"BK[m[*t,b?ckR^riz*X?5
t^JB#-YqC1wD
b/1)]S=}Lv-4[]duN|e{/tA$=Q;8+IZ?u]/uw
u&wAN&';case"fa":return'(s`09bSZ+&iq4"GV5-S9yh8ag:CE-Wq%$x)^H+]22e>*U#62++,6chn<iJ-L5yt<V/iF3VF$1&9*KGBy
kRvGKgn"FmxYRaISct_IyH`Yk>I8v[m)BaX*+J6<r*SJvYu,ne4/^Tp/BHb]?TH{tVvuu6gYUKQnc&c:&5Bh)LY4)bW2T.:M,VLRfQ"Q0RXEAZxoq%0k]NW6I,c#c=</_2o1=QloEB3RWer`:Ba+aagn49
?;mWHn0S<xOJx;K5L-[Ffq6K:&jcTe3,8nwpXfl1Ttg/g7Pp3#$0~k/MvtHG,>.M%5Bu#<,e)%hR#TdNJ]O9}O
hF[s4eg7chX@^>^Ts$uI<,k|D(0aG3PGUU:|[W
=I=LQ9)9"yA9h+JRq@PU3^A6;kPAh](&9)V.BfO57k;_"KvCZM{Zf]_+e[Rf4dI_kfy`2ADMA>%])j1u&utJ,.*czd8C;l*pqx
2"#Vy|=&bi9j1.#pN`SFtcd-M=(fZ-h<j6^i2!._9gp|Bz?v7)50G^:>W}Shsw)KeJtL8Zo$+BMF<*Wq4UfDWno]ilJzr&TR%S#fi*vJ;^N?g<K"l}fvcE/K1~"!$onKAZ@g"6:6s`bi1Nq)Et=pi;H&^qTJD"n/kU+Vpxp0qKIu-tJ/";E6=I4t?XJe"e*ee^5kf}Ag+MSuE^`uV$@nLV]lu9C.V.(xME/DTnpANeQtVjFCZ;^vtc-p8`56<Skm>%)6N%;TK#Xi`?GgSii.Say
K+$Et3j5uZckob>~8@6gm]6_[m`kP
%Q/P]p=Gl874%%J0riy9cE/$#@%DU]r53{Q,7pR/KgW^h{Na)>rf0X4}aPi-V{<0NNDZA&H5C:4YV6/~>E"c.n+E+DMK!(-K0rO#@Qlacdc~]xP{K;8D?x+^BF1+#mZ*d-lc=ZwJ3n/d){[fuHG]Leaq0LO%d(e).<g9&E_O@3e[xh%`"5lAT`7Dkrd!b$yP/%(?
lJ1"7r]:q&E
/kh2Muq9W&n({dL-%Zdyyg"WOu[-,>89YgS2`t]Qj:js884;_;w.Q*+$&i/(jS|L7(dVO+tMzl=&aHBQEp&MZOX=TJWV?o]]y^Wb2iy3AYVRVE@v_,|[K[r,p,~T?Pu]E0XN2-7g@MwB9DPT`&?=^.II/<K!bd{QR`7Z+4=H,0@DRV@#7/m#dqUuE)@w<53%tQ]D-v_^N2[kZCO3Gui(HHw&,<GsRm}u__HVT0n<[un.ah7noyvx%(3H4g(mk-k,f#B
i=k.k7ZE6DNVMru39,ap
*]Es;6>err/IRN1UQET^Tm6aAY_]P#rC&yx&yh,K.!UIE5N&x|=DpY#kU-3VxKB]:01OB*r<I-mj<}8ScE
`)FYq;[[PE@D<A?5ete*IV:$4HXZ:;s"d<y=h34%ANM^
iuYa[Plh7I280(h;lmB,-);TVx@pZpj;"1.Lpwmp"fk2f0bjB#B_bjyX(u826X=rk7*.&I*e/[Kq*Kk^[p$7Q}pzNiC/wJ$^&iwb=|^z!zv96+t7_av63=h^`2V0;1?R9c.rIAj|oS`BMtx8DE=]c@ea268.8z5Byh.9y_Wt0O<C-Hq,!kP2:)h^y^I8,xafXTRYh=XQ`>2WZ[X1JR"UpqE9GlVOIWwN.1y6q!f%?7i>002P9pI:UF0Wb&&M<s)?r4SbVHgC;Q=gC{8up~ccGhKam6v$3}IMyC&gF{@9[=iq.T:d;fYhMxE&#a?~w&4cS3q+5BVc5IPj`$j_i,(3Oe.4=I-my<_7GO/x@yn/KgcNh|e[8$2P`U;[U**?C+Q:sj4lq4s{<a*)hfA^-iRK0co2mlY4OB3<kAp`%E%Av
W+Uf.>AIO42k1xlmW_EEUaKK8=]_=3X*KgOED:/B=jQ@8{SBQ0%13fS
dMb@sXhf%Dn2RGUk$<Cp3;2%mM#Vyi,a3{8B^
Xhc@B3C)tPZUBy(O1@k&y5J*aJAEW2#IAScrM:!}lM"v
A2&hEjtEMh*-Q3
Pe]*Etnj
e>
B@:Cv?92@b*_
)TO]wlY!<DAhkC-eDEej
Lw!GTBHK6wMX([]VUl0CaV#[%{JJEsWIYKQG-};s-{^nwd<Sj+"M%5YqcnPP[z6:gtA3E*$0HIwz`H"zW)<W]HQdxPc>
UhMKM?"P-&sxiLYM`h3O^?RdF^J=6]Vj
SKV{^nnsDlfxlZ583]B-D<r.2gnHd%6amg(Nb}%=&*3K%J3i<Ao5hHb<uq/p2J4OImCQ/-w|cxm):7LKpVD$<BadI*>WUAE>b
AzYSh?3
aqu|aHb}+Q9</kc]dcy1
4`!S>BZUD==%HvdTdvPL#G2]P+peLCW(0-pfx]63m]=i7cZL86K&y2y[O2q6#)q_l1tx@e?&Y1>0p*i/`gXjQu

cs#0z;*0{Z`J<fD!dO*#!o:_<(`BWc=W$/x9})iB:0l:t^_.HGLrN9hkit=DN)8y9YKs^67yW(D(z
z
5m)iX<4e!R&W_k8:y:6;p6<el(CA+"5OEIT/FKjJ,6h>GS`Uh0E3E:o,2Aq:E>EIz-5k:o
s9nV%DNMO]%UmkL:Fw22aP0:L?"*3-W6Dpazc!tx7u`X9WHX(s#eEs*{bGhWoO"wo~`d"YgS6<oN)&@qRNy(GBZ>Z&e:Vr=K)W';case"hi":return')s`G&aLWr/eX+r$yyUbKnV9#,^XTX;u7_d+Ga=YP26Gbv+o,1Dp2RH`R>Oxjbgsgp?W47Dgagfa3zj8r_)hKTs3euW|=I
sBu@Aq,]Ll<m;Ggn]hm4lfI,i7X2pa=WfYfQj>i?l?BMJ`76?8_pTw5Ia_]HZo#L]&"p>v,:mHQBO&!UwSpxa;OGU`wsXSfki17mNx"A!g4R%=xd[Ye3FH.Z0
6[Sckz(^<=F="U_)gm#q@n_hY`YNPnHu7irDsn=#*<aWKa.;xezH9m2;Cp8gIeA-,aE8IZ=pB./[q(9i1br5GZFroN0P?65M=y<at(RBs
^NyJ~$}Q1I:^aKBu$0%L+6L(^:v;@?/ppypwgn~l`FrdOSDT2j`_rQJ`j9}4,53nv."tHoIp}-46X&{H5CsN(+lKgDIbMXk=Z&#f}ba^.>am2TDv<O^!PhxGKoVUa3^pnY$(JEE$M_4g|rkZeAel,iNR&oF3%6XVX@`h3v0_pxnpmti:4a!f@,5PXV7`.BhRORJ+wZT=bV4,XX_b-qB>.vqe*L60NgRphOG@MyF#Ed#6&xoeX`,X?Z6hgPjCB4PT^c6,NK>H1KhDnTV<L&d^uJhXbX1cUgkaSd}P8P#>b&|;6j)>!]/w#oEj6J?$#(wlF95tY<gcSG?i)$%7X#M[$#j?bYH(T?n@{=,7`P
?,mgpYWm?I[/nX0$txPzac?mE;GPZ4BUf^Gx;wy1T$`|YkT8s|8Op]*[gg5GT}eSo1qV!4##wybMofoz/HZHN|g=XCtm+d$/T|kt
l$I%BtlW=3rxa5s&P`=={tP_w];>8e5XWg^#2(q5b9H%*q},3;/!y9(D&/"qR/#P/`>ULZ<#k<sy7[k97MuOr%~$okMMBZNAfq$.B,iLt&}mvl"&yLHO~<Q1(Z>!4(CR7A6=+MwrKhc!Y9~uB.4Nh"nZ,"[NR<gJk.(w0(Qf*[xZa-C2^Gq_,jLEl51J(VNtHbg%U%fIle>Ai]vNdR[OD(&%CYxk-9GibQ%OVc>F/ZT_|;W
<Bsf.dfZk;irq0_Qp-!6x37Lz1t3V]_h";r8VMu:?dgJhiNyyIb:=Tj2E<d-{e5s}[K[1m=nOe1ZoU-Rz3*x@"m`qcy55U=B:A8bV@V7|gOeDaE_r[_gX.fVA-_<]TO`|$Sj0x/$bhRn|yuXOM`gobN=,X`<C0R(<hZtwgm841<F5YG!@Q:!~2_g]0#o@,#eI:z@%C6raaTT2vi,fEaQGr"IZ";uJ.TYD=v
3kc%3Qx0Pd*$}9"`~)/B"[v
Zm=>dk
fr7:j:Wn6*y$9Lf=0;0n"t_v.</>@&klxt+/QW!NEZdg!Q9)SUD{>ul3"VR#I5E?fA7tK.cil^`ojV)Kx3mu;flgY#ZBMh8}>:jy2EJn*6KN?<TV[1O_
2@-*R]:jmGX>ePLmQhY:7+gh{je9g3_lVY<`wt""u8b4"",;|f#O>nPJQmWOj`:6,C+vUE]B)]Or)5~(Y<Kf%P>*1Ixl+l))>nz3vkxh{mUMqr85Ei;@K5cm3B1>7Wcg/Yp(m73fnE*qo2G%J%RfV@3dX$hD6s[5&w-Z{)NUvRkJ~KBO@8h$`uE/r^R9c[qiiU-85J607"93,8$PWAY4a#1u;a;"2a2/>SmCfwq%4L:vXboF
6OeSA-6YTcTJQ1.XNYBAL8Pd/]6&e}KqX$oX"df(:[ZlhQus;D%tTnKo+uQtB6qR".P.PDBpV:!07ZJfb9?2L2Wxj#40BAI+.^-X.WcJqRXMhz?qgsR{bfd@J.e
_9YCT;U$R*#*T`;>r|i4Ens;w^CMKjo>+uBX"N0bFQekCdDHh9y~3]Vs[Nb}J("eE*C3<q[=)zKKYL;dP
]$40B^?URrrV#nRI`i?:I!%LA1PXM;2R.9w{v|%fPC&c^D$^!Y"E*W%V@wlt$YXlO}`wKlZK<#Fk.V+Q3w.dHy
dTQ_+2/(%p5#+"W:Gxp]O`qB/Dbai?;yjX}(%q[v;)S4I>(
c8.vnPmI@cnSjrYScoNHj;3_Bv2RP+<c9j&wPc<gq_O-^aUo,+3D=pUF]S[<MYAJz5]o[b?)D0bW-s<J7O}7p:
V"9+j3R/T@1bDVQ#k18#f[Jl^^m@hafw$~U6;~3,e!L9*k6?+GU]%{v<0~CCy)fCA}#6!y1g_08v&4GyJzTwmD@2N:&/Ng;7tzf?+oBPLlQbn#tpK|/M5L)F.bxo.M&6(J,yj%SoR@bU[""9R%9.#oj$F!8eHE[z<)T@R8y7qxrrc7S042;v8]$+ma$ieoH=_-HK.@g4OJUA:[q1U_U*q-?De}U-)(CYtL0KRln8sS[(r-oiP8#g.!s,r8I#&+(P[/epa9Sni
[ND~j[:g4>*]c^BLKGW_%WX3sn$(Ud43cOX86uXr1k/nV,Cyn,PY]c!HEzdB
++esDc;LNi"3>$;C#24#AR=KQfI$/p|&}WtABKf*91Adp=0p;MDt}:Y,"59@D>Q$&lr:]H,%Qn9SNg^GbgWZv&8E&:7<xp?62B}629[5}aW#ke".gvlxxbKW9mJ;;8#WE@b[(;w!D
E5fn2Qjf6nMo-1}Im^Aq9_jaBEIP4p>0kHd)5c_J>><)#G0Q{OWirj#Wru(V
.K4Jj4kUn
Z>
>>uS$,VvuqUgaH9IuMQ464Q!T5msI5p9seT+-&TZ[XOM@@C?mG%WnIp"`C/L2]FxsU5VfITVrjZjTQOX5GWx[A"tk4HdCL+8o_qjXR<5O&kr6kOu]]][VlQ#tI9DS;mobBT%_8Tk<v#!40rd/"ksZ2d63[[S>Pd,)hR3*>Dn{xKB!vogpQN!4>8PzjMM.vN5:HWk$(SD}0O
]f6lA2UW=P%2X@X)hbdD99$^Ps$ShaBsx50-?CI+8hpG%wAqvSi?Ii>rP"|K?<^@d=-[%#wl@gmF0,)Jl[?6
6Fgu3QW3q]fv/<rNukr%0at?nVcqkMOZhdC+!{A&H32tS_E;=I,Z/J#K7j-2U|jKwDYHO@Izh]g>!*PpaR_S0g#80daL;MV8P
`vdft&@K*6";rROO8l.:T@=Ft++N_>q1%rZy4Xm
/ZS<:4%ub$+v*;b[ER2Mt)<q`
m12"RK<00s_3[<y[GQOSR/3x4Yd&0O2*iGA5HyD!Cv2V_slJ/"FijS,}#Z/efH_qEcE|<ZOn/
^kPWx#8"1@!a8_KklMcS5j2N!))~bA`dsFujGHr>4xxeE"SJAIK"YxP#qFPMsMp1g=O7y)x<ke_6b+.$;w@;B#L$5cW#x0k%
bIf-t%:96vXp|E`-X]yBJLGM?yw>:Pbm:iL2@-x^lmX)SPsrJ@99xN
%*b*+;q(u.bxH29Uw)Y}
;c+Tb#Y:j1<N62L0U:^
b*iIJ>~GhHM4hiGI(`eGk!vCu)5:/!khp3l<Wr>D##cMr#T9/b?+TYuFc@fs;;`$XV."i`bl=SPs`O8E>:/I1:>52O&d:?J=thaC<M{xd';case"bn":return'"s`KraLWR#At?lQKBA#!7+<-%v(cw+p#pP>6wO-4yA&)/,Jc:q%_{&?"ZBj?WgR29S=E$/_k@Xp-<z#dD!-ycH)?nEFxPFxw.:(b;A
mH]"
q&&K#HO0dl.,A:Ds)?OB8,V
g=>G!kHNoe*XM3`Rh#2G~fmOm=,nX7LC,.SBk]t*6sjl%l~pc8SH[a/k6WC?yeu#!H)XC4V`{Gdw`*.bmUbBuHjtrb=VsqgQg/ADhwma=vESuJ"uV+NdR*(^wwOXP__6[I$7(Dk^D%Kd+c"fn%NGY_;c5_[X<yxV.aURF3c:JEL8A%rK7qJOLweqvs[

4e-,TC.MSc[URraw7/&BUOCuJ4#?#"["!zLWARH1vxmT7?qE:5P)`Rxk[XK}[#3GeYwBkj&ID)P1*r@V%3r$/kBb-&_vdG:.7tlPc7vNc6gtr7<&DEZXo!&HfS%M_Dm4R^[Ze<iE$;%ve%`d@0&wcT7#:XeO$qsXqkK_ISE.Tq6LB1"pn>FGD*V7?Bt<AJ-(NYcUnN+rryanP<Bwv5@XX8>~H/q;Q;ii4fdg0/M;-xxfTw,3@T:FUC1O7{G+@WrhJb[c"WtdI]G7hP$YSx+YhX4*7i<`3:$hG,Y4$Eu,,/&ubRuRP>D}.0U^(TG9ydOTZLXL"kiY_1$R<~:+]u^jdITc=tn55d0)e=@Mb~J!lQ#g9>C5=
_h%?W0TX^u0>EpvmKSQ;N_WL^fU8P>*P:G`:
L="%x8?mr==pnA`%YYDj2[cmr,}ZgVZ>~V3Xd18aL%SKr>#$C!?qN)%3[nV2bqe@Dt3fu?Y%
LzVi[Jx7Rt:a7.*71f<kN>!=An8HvI%s

w=a_F3yg_:Fkg&rzi-wJ4Kq_&eG_vn)Ue9j|.x5zFF[nOLFohOj~%bLc$qWfslle[,3rGowEv{WO4vn`#:^x4mkr[Y)Qv&Fp*/IobKp[:?/+v>MFOLj*Zp:0N.g%HCP
VghA
#X`I&U&nDT<5G_TtZ)#ijs28k6+=x48u<%qhudanu_>c1+Qs[Z-BAO)hKR^^D#WhFC$R4yQ+A)`;-$~98Kl9V=v)~<tY3I)7pnQiTs8VK_bkU))lM(WaAfQSDjeD~DSy=*@B;I=$SwV"72GE:_^WwhutsC=,
>%9R-8h^D<-7a:7$;<^eQ3s!PEZPgHb$(xfhOJtca%<xYp4Qi"HV)q,$1yyGZ/Zy&:GkAg&Y&ePSpU
U;*$tT$_f_-dn@N6Jx1x?`$tXch"[IkvfgD=8cL&/L|w^wVa.RRBFv[Vybr)#ml>0]Ans0BFvr|`Z.oFnXPl$s<Y/`K%x5+C60NpoP5)3f;pm6/yOdaFR_;a_*PA}4bV
9"F
R[JfB2T.xtJ%U
,K1G!/^Z[r1r3-(,Kd!cTkj[C-xwvRn)mkDlgu9m`Ye_
b*We),EM@d.a1m1*!Xz2;Qn0V*;f(_cd&Lt+k>~#?%r?]Qv+ag}7p"Mre2jKg.7_I:VdRwm52a>?vi]CN8.vi?8E7tc4[O1x68.bwWxtm%_kKQJM`G
K@>G;|9^B,;[*?@<r(vB0n5Nd?rg?8yw`
m.T#36dLV%(s*D^z;KH
TmfXI<-hx#I_KHdihfZjx}(na!^s?#*Y7d:A(}$aKKJbq59URtHXTK#E
pdfjRqhMsL*QRJKIL=^]R3f<rYciD^MQk`.l[%E8j"2k{Zx$$1p
F)/
7wEfc#Km_JvQ5kW,".J9,@lG;lPfU
<eRb[dK%LVC2QdHi52i/UaSjW#z@z5d3?_
]SqCaAXSox)W-B=W;__WLp0G^Vci??q]]+=Sk.f4bJ0y))*_eRXX.U9^gZ-)b-w6v@BTaxDg5[m%E|D^VsH^(R$;R[JsD9)};BW%BJ!cR>G?Bi):+Y/0JJS<IxEG]q/3/vt_(2BMdZ<iL0&hT[+x,XAvA7vfguHvU6cniaeMHREll:pAf[NJ+#%NC+H8*M=xWYf,gb9^t#b8TO8%RvT|;z6Gfc>iiyYnD,Nul/.LsP5+jY-+x
89SAuNQ/
tAbeg8fAV@-d,v0d;&_L?!fEbZ9m$2=l*?/0`A[?|P+%l(Lwfv!sz(y
<kz2Z3(u8&fVc<+#X<{UL3;h)Kb02ngL25DR7LY4uAN4+!flO*oe|`94+qjqX
|&4lfdy;.IcS8]gNwC{cb*F4jM,WceBvm0^hqDyi3M?Rb1j#&U}970M00N<,Tai.=p<"XA;KbdWoj[8-fLI.+jj
_O*XjZ!"OuU0N3xoWcM@1#K*c%>e~jDTp>:o9?UET2M(]][OvF@qPM_LU(*p)M2wIJANX"7-Qvr:IYaFkG/:S]NQpP?
2HvE-vjA83K2>i2`<#ef)=3*8=-RU!g,03}eF9,VWTiUA(C:;hAj:b5eT"jW0blSNvIYm(ci55<PPj)i<VJ[{mZ^
def#^$6y4l`VHa;z2bk<w
Jo0fX@W?k%0YZ5/L3=O+jNqO(;mk5nJAyYtH,`;hLE0(RV76phDFttdv5+(+l&CFdD%_3ho1t}Qpa{0.8?S-tMz&R|PCSP%luQ6qc/
w)m,;TsTP_^Vq)0$7T%K*NI3UQJ^5q8F<.&Kiw/=kLfO2v7,],1+s[OK[;r8dZ3(7y5EB!Li$37[2,sJ%G}l#U8`x3_U*/!^1JS?qdxIctTcG2~ki(Z>xT|]j/-M{bg3rVUSA5MBB_K;kciyGIDpvC$6%>6SOD./#pxS[eY$(no==Dh^rDm=X5cPU_*#JS2HMlRr:m5L1_)=f8H]mf/3Xai8Si&m&[`.9<h3QwK?M!FT>jNht
nt]N<<QgCx(?cBTvHl!J/@;3F"0n%N"IE$#I!>nskb*+7xn5Fd[g.gML~[B3n#BRdRs0Ul.<-g?X5,`=]q,tmlv,FXPp|;swld4>Y4u3y>s!$9YOC"_FD7blp,1+|QPxFORdu0+ai$.GQ_JEgiWteI2n-hjPOuMFObK%r3fk:6cF>czDCyYDjvc"
FWF%
i:9/ZrZQ<B[p`S^,qA)o<8x!"bt*2V~DFwHl+F"AN;p9Z?M"q:1`^S4Z1Zn`Tu+IJI=vPwbIcH4q_bzFPwRI+wW3y=frib%"Q-[y3iic`P7@[%9&W8jNlG9g|I8F/Q0nx5fJQACS[>LZ{itr/%Vc^Y
&C-VG+5B!P+CQQA
?A=AN|2Z&Q&z(FkdLI_aKL,qGxakN^3:3LqAupe:B7P{-lH`v?]N6/[a7(wC-oWty2h7D#u?@wExb>u@xlq-Y
^Vl1*q++K3(|P%*/[GBl`ma;]20#?8gBw.-@hxwjkh&ICoJH;D))m|:Ub#P}Y-lJMPN.XgqKgPaHUx-JNA(7wZ8ssC&@?9U
]:2^^MA+&e2c-0p%2IocIPRwYehzuAD"Oz@N4&2jY$%>JX,>@>i>U_L2"m5`>bJsY1W^KIia<}:e,M73!xy7SeF#Hu(s4?!cwHBH;|*g6gZ1<UKlXY#)J9pend0pyAKvk;bD[f,EOH.
1YYlL&VdhLT^N}uq$;rNpc"wLVr
6>,_qrPny@VpEwb"6B#(7R
#h+w7iiojbwdMohdhCuP,!6rAy
O},Zs.9i3B^fqVA2r/lA&l9SJ1:=SVKyo00o>;z)5
-uW]me#`T<atmO@wQBLF23H/0Z7s]z]9+#ru8Q<$3N20=eAhe2+sdQN)L?8wGOUt$vtOA)ErwB@%,??6vdPct]';case"ta":return')s`K2aLZ;%gres?G16B%NrL+FeG(+0.L~d-u|hd;Eom&z2T!}Yw9x--bS8n9qF0axpU1wt1Vqc{v{r2E.8!=^TnqMsRXCnllua>s0ucW3l8n4wvQzt47oatl5<^5s32(_qnZguZw2u#b^jjLOs*h[O8b?IpO7%cjq&)uBt^cPyZMMnLWmf<1:!Z.E:2B(8&Cg>sL>m?@dvYEKOR@uJQx!wmrM>$b>q)h;BJF|M~_6KA#&y|z(^Bb@x[z"11vD3f[Mw(R%wXG@N)_md&*Oy3%8W)lzshPefWq/$%EH?]
&1zAHS}4ev;sNcXF>c6PdyzW_,YOcC@E:a-:j"`qgh&#@d
Icrdva3>_<l-R5:|[a8<nV5RZ,U5tW-GN<dsOXZEcu_mnRL(/:P^,wmrybyeD8)%].f=O.lT--_Q9UK9OF).Y"h7
mt
mdi=oN=t+$Li8fF89c)[E3%}XwOEtnS89^RyAi47
{`W2$+!tn79u.`;^zhM%0)@Ai5-8mduNp4wH%"v[CIAnjCWa;%T]cj:xw"%WH35ZLyvKTrSGUVrV-dnPn-K`QZv<uoiEabIPe
XwBG&G|Jl"9`Yk3!{LaF
#S6FIyFoW]#KLD-[SEclJCsd,1k(AUa-Fgm-:bl0fgropH=F<1!L)Ua!;S*qZ0P4olecxGP%3[,_u]"SQ{/:SDIkitR$s
du=eK`
B0R(fZgZ4sSpTcsRRn0kiEb:mGGM
4N@3%p4ynd!ziNW^S`hzP2$_2b7?`ARD=|:wmT$avQnz!PlR*#
{Du"Y@s&~Kx.~QEcqJpG)jXQn]F+]4oI149knxk-r+fQ65(]Q_y=BSq*pZ6K2V?ZVqK9,([MSb$v9*@CA5o?e
LIAWB]"AjvUG=QLfbLAholUn*33hH!oM-ZN5i$DO#_.vhYv[s1AcQi71D,)){i3VbpoR98Vp6bZZC&rvL(T?)ZdTT-ZJ4U8@Hp;.b>91`#}rz73&8q@%^PQVO8O%PScC.?J7l`cwolQH988M46[+rs-63JgM<yL

J]KxP0dthBWA2MI!54ov)pc3Zpg]^7:bWtCau;#+x-FY!"8r5Y9Oh?M,TWBL?sINRRMWq~<3X.q-,{tE6R^jYYNE?A4WM-MzMI,zu1X=tfhJ<aHd<e[?"bpeMIqj8)K;Mbl$yVe&ed:-.@.McuK_!!5&<1ekFS6XdQ"[C]];onNpV_]}wY6`+t&kWQ99o[a>3HFLSA+y2;tsXGSr#X8)tqOTa_5hu}B<X}gGOrip;z
VXxWo!QD9&ApqtlkT,///kJVZmMiHSpXS"2")Y(VMy-L++rusprUDLx9f$Be>24;:-?%{6(Ko$qwVFV2&)9m#S&fEHw,$-yS#?=r;FrDJgG`T2~3c]R;mLfMI*]PQ"p%RpLvJ#@i7NFPZm+F;%h$44!%p8?2vCpBrQG;,fz<lPND-G=f3S&G
lr/OY@rg36e_0kuD-P+ww"mFp."0G~[B@^Bqg{04*cU_ng#lFV9tQ3cY/dSf3Z,a>-%|;m1raJ0=TmIU<ikg)<Q(C:d
rz$?I3A;B`!9oSY:]b5&CiU<O!
a.bfzUTAwtJFUQT?YK*o=c`hQ@xdm%t
1qVk|7%o/]e)lr:&PkJ-x1aC]TpZ{*K48V5TB:ObyYYAq"p3?-N*P4==CnZ76r=UJd0g0&?X#eIKjwe!lh|S2L11S:(6J"*,iq@Nr*87?a4R?I{J{?~2<>%R>ySk5]H8S]|R}?47yt;SG
nVB0Fd?Avcm3Wxm$BQ?S*>@9y0c%%<*>.#6)
bkW|fi?"g4<C>
waG~#PeKJ7q$-:vr%$9Qh~DD<SDCqW3F/JxZ(QWKw4F[_[#eB!&=X,c~jl5{hxB1jJO+a!8?E8s+$V>+Ek0L>lPlB[X1cWK{k7BO:s(`PHa6qA?F&aG9HPXAo[1)hB&[**Ab+<*-^osf@{Q!v}8V[l.(SWq
/oGbhsHuE?`xw(3G3G8i,_3.mU_,:oe4J3PI%X,JO1RF(rxjsr;;y~TLp)r$g-yW*Hps)MnA1_Trn.@h&eMb)OqJB++<O[el3i_enCQ6mi(.hWH~Z`"QUKFYVW8D?wgzn:jl7^6zbzU?O69~5^t&6_iGa|(N/X?l5LU_6^sYCL9pu0//b!S
U]dd%2(&_9)G<bhOl7wS<R-qV}^qqEf8v;sFAv$`hM$4J/IU0L1gf"I|V-*6UEl;xe!=WXI6g-Lo&2=4yLyE
>?3L~<_N.,F3[C%tuey&%:QIb$7;IS4pB-`*nDH<uymZDX!%I>lOs%AD9T|P7Ypc:7"(zK:T;$nu=)U[5_k37VuOKMTET5iJ
)(ljtaOyvbdT_FW2g_$xf+/1g3KM<c+b$&[%W=usE9VDb!X(L7%U9`Dj-yj2]
%JIEv>K[bmkbep2Z_E@dsf*?Y`>HyDV$]~vU>G_A:9"vG}ChQ6_pX1<B.RV)4"UWw;E
H>vWqnx46}5MFK<&=_LAj`6Is;1U/y.zjJrh@m;qX1brv;r3u37T]GAgte0)t@;WUBKC!/7Rq,O<Hw%b%Y;;b2VaQ8q_(|r[2kK{A)[N<mR287oHc8=X^[EJ6M=]E=2=:~Jm[vx%@-0Cenc2sq(^K)xsDb(P#WB$YR"
D3ZK)saer;O#v0j{6F@!2>Grrc1xpCgo>A!{)`xuaEYuY:
*@yf:`v&^yr)ECw6b=/Cu6g]%6RL<:%4;x2@UR-FP":Xe%_fey?jSB8]!7zed83m`#&D}#cCu7`8;3{CnAL<{nK[J
(VCp?LWnNa?o[w?Z6`iA#6hllGW@h&~^3b|^:^gp|QmgKFP*L&eqfo/Wa
CC~J5!Qf2:^rw?43|rxtH41x9km/kRmSrGLO-x3]~q]rEbOA`mq"zdHLY0T$cefbHp"xM#eTB.T]AMHl+pwy_G-t)Rcre9j:ge:X;tq;m4zab
}#Y5hIIx3<;tEUp*Zmpw<1D5nA9,xfABlwH$,p0cXjwbYuf0D0S;^4F]/NU#/^l>k&&/3djqQ:Gf;2U^"E1gF?=+9:CJ"o<ki8snA5t
zj=x44wNe9gn#`:_@DyF{tJ>!M:K-58He*)22>^6RAdp%!`sfc/"Im~VR+Kwc
"Wl0V?S^#_@i}Kf0jJ"$+dmVv-pQ7bZPs,-5l9m2yZ]W}#oTE?B#$%`oK
dQASeK$h<so.Yi<56Su5:FjI7rZh_V^"hqAUuKsA7i<g;cAB~""';case"th":return'+s`;s6KZ+!Lh~M&</eKwbXL&`96T9HyQoh`eM"4snZ,<T8F&6#E4%DyJ=D@U11,>pE]IaEM*C"a"j^3AJs0B1
m@%EUrhfj3jvJs*vs?R7uo>UXn@W;x:PC0sSG^]jqBl&%]YwPHAG[I
Zu](BlxRr_FkA(=}k<p;y
XJs5eN(1?2NP%2ip_ey|J:+}=PKET!EV,Nx;K+[B)#sfe7PxIb)pN?r>_6o7`ma"Ml+IE;,KC{@YBk8@N,eBh=%OM&*{HAl
,4IVPI;+"N%H2n<x"xRMNQC|OmqclwEXoS9(2ajD(
O1*m>y>P6Vk2X1Ne4kELghifk+Eyg$I9SR#EAOuE"=MAW)[l*)&j4=&%gAcf2SlYa2YXP?!0#B4K$`f+j/-4o.1!j$+HvM"AKkTpO?r^v|xui},RY9?uFPjr>6xj320
+<FvB",FY.8v.;$87|IG;DNq+F:I0,WXFftIWw&eTq$Jdj0f8b?k%Y_s.rWl2G(/;qxO[V2[&;WB72hYV.$HM33$MyMMcwA7W,s`)h9!VoD)@Sv>U]2aG/)R:@D}fE&(#E5*>Lg@[NY<celPx?9JM"&%c6(weX(+w7<E[
[(0>$*Jn.y@k:B$*]JJB"9q=6%pQe-IW@$=1)K,w%$!Q3Z#Fk`)E)>6j*t:@$@Lehu?>7TUHl/bWXXbaB<4-%2KNm/Z.=*P
;/bV#(AS!B,<$!$V[:w~PU@.dKsw[t_Y(-GA0-B$FeS_F4G2@B>zVD15K.ECggovy[8~A_0x1{3`m3BZ:lMl^KN
<N#/*2CFg|!b51!`Sg):>q`w#e;9oUTT"X^sjJ>?L9Hl8hA]=z.#O0i(RrittX>2aQufZa2[Ki%>t&7r3CX(Qdk-d7T5Ty2xKf_N;^(&09_^n;A0P35
_d=8x%f,qDAN"ro(2~##-&!pY_(P_a"Z;A*z6Jb2kT;**82WmIg[P1/)$AGsQ#;-Mfqs:Nnxutu?G0/Zmr-1`Xw|e$$nr/1,TUFJV1+Vi23<[$v$-;=&[Oh#@K<JL.td5LZ%
+Q8yj%/o<=9lXouJ8l4G0./Z{F{qB^a<!B+h]
ny%yrx?uh1f4v+$gGw*B=PsSA@3Cds%Zu7NN.UGk/_&0%/E^bLCORQXW~BhK~m9y?gFD(2Ihr.Wei+n#BL>54
ZSP%[S>T`f/<2FWd;FYs]t_"HuckDBc
ZSqE>@#eS2{S=VLT;h,jW[hmHOQ4uWj*rSN@?f+VO*50Ht%H8GL
%fgw"!h]>=dOgtJ6osWQIiYQKPL.!qpuFE.m;ii+bBi9ueQ3M<^/)k[-8DZG,_,fF5~)1;=sNVN[dHehY7AV?0~
Av|KKA?H:iHD0V3hL4XOPF2>`3(u,&3%Du85,-B0>IU3D/f`k9`Vx;dtbvXy[F7CBCIui1B%wF7_A.}^FF{rrnR.&,gZ}tA:1X&m#yC&$9-ySSIj6n<M]j--OeM3C)7/_m#f5;J)%p
R3Bbkt(gfIYS[rTf*_T99Ulu`
;;H`SS/y43Pz7|q[Tr2bGV=nh>19T:PlFzV-7F60K(-wd@Mc2iJG:phoV)6<E)Is:=6<csdY-$p4,7Cw`J*oFzo_#;T=%F:Ad)[tlWhU#QVg]?AgAV7i?uNN];a*"dz&k]WKmfLuVY%BIpjP6W]9
hcMjF$Ar4+jYJAMdb4Ag"m8eQGMJIaDcK;nK[_|X3Va2(eiIEQ.>IC7F*1mALB`el4+bb6)b/E<)}l7N[4=#Z+Fk"YyG<8nX+6zI|b4N}"8y7IljdGr,`YRY^-YbV#]oK2J$#m~A4^;fi9"!k%PfADQ%<)g[6rKo:<Dg4L.8pahqcFfXkQ_&;0],xypT_:+cUj3ZLi[!Sf3]e5}LcNtM0s@bV/dm~*[n+4x+Ei!>OJR%f"3*#(O1uNsO:+Pxe2pI,Y{E@<^={!3$?6N*Zw!P[4P)]C:nf5Y<U4n+(;uXp$Q@
14(HDCPYNDM1@(Fx`qXn3F5[VGF9D3$#/S
OTO3kb!l^mqc4QC[tJ=#B_@EZC_$3#GysEVRU:&Cm8.c%<TQA5}qrTgark22I^@1k2qt]%oP_>"ce,>qVSbXb^"?2&rk,Z`XMJG?M<!W+N>(`+,+x@c_Kq}_1FzIdDZLVg<MNJ>VOp)S98%/SfCJdFY!Wr}w5a+F|g{ktciCLb$Z{K`u7mtDd"0GN[:ah:g/F7t0ueXN,wV
O((6m(&PT#+)0c_YZ_]ELjm.9c,eNIYj(LCbH+5F`fHjq;{;6
hE$Sp=gf:Jd4eT~=$=Rq?Wu@Dw[1v;2Gx-|Np*mJQm9B`%LjCGjue3<*iZMjastffEv:C]$yD.oav?f#5g{%/BU0L?pAJR{$I#rv4frXeSN$lVFiF=OgAL=XS+.>D%jL&P:GSMrOOVu"M<bDPvyJ>M54|+r*="uovE=,2MN
r"X&p
&mhW(J-Pcg&&>xeJ9H6__28^<y0?vF#U{-aZUpYfFD
ul.PO3qXDEy+j#!g6lfxJDjvPlB]5NGQed_%3k>bVh9gU?iTFA%+sP3$TTm|*i:7dN
k>,E7JOb{9,I12we{4?(XcL"Deq]o>0D,9%)2d=tKgmoR@HtEe_1`&WY];#BW6{hHI/6k*cv4qPIzAyV6+5OV!Vi-1Dg=X8P>Vm"
+&u2!"A$
F_c`}V|uIyw""';case"ka":return'#s`F;h%Z*hoq40r>lh4Lp!}_B)@B&-@-m*.+D;UAI8wZ6Voqae]HIlyQM>>.&8Tl(:0sZB+;a>#f4]NxLLcx!dZm>m<sq6v1-`nx;PE1TvKH9vMrw4CQhRpyWHZBN1372`lB)BzfgM-1u&tU%vu@G$->r`pPZyV&H7
b0"ANVuzN~8Cs/!NC_pC"+ytCcB1<>F=2.K32a#@b"0KZ|]Bguw"J&$pVRmBl6"2N
e(D<aXCWC.8bdGn2WP4*Y"nhJEG.OE^@Cfjy97=&wU%qn`@3,SM1rqctas@7d<-{5u6)K4u3
"k
K`yvg5/%;c+IO#2"eC(C>9v.9p91rEq1(ifj]}!5X^(Onf3:6VQA"qlM2yYCo
,o=uEg<Amb2@6%G|2cr%ZeVJ(r2LK)I1B,]{s5FM`t66J.YHA
M!(Uns!Ymo^1hq]kn8(n3rbV!4E8
>h1:iAznK&DIP6}&AN7:&PQwDZyPo/q$OLw@4X37yDeyy!^l^+z.^A(j0!/TN+D-C)q${i*s1.d)l3#YdKLF!+9&cmo#Q71SL?An@QxZ@u(I3E)OQIT3
FZG>.lu}QjxD;PB^*<K
Ny``9*`.".uV+_H_FfpV0sGNc>/r^8=a$cDu*;tMjpOGP>li[t_;w73oz)ubJhB@Z6gYz#R`BVFZX0$?6&2P7c:-LC+Y<bdqV9<[1?Um6qxk@K7miC
A^ZZ#o`GArt9`bt9fy/S?I2oQdp.)DNX&RL
,Exs~%"dM$]
>&w+e*=LZ;*WO%U/1T5mv3Kp6
qbqTCRb.Hk+bX9T9J4`HV7ZC%xprs`P?eS>:|0DD3B_3~nS!,;7QC#yq1,?.x2ST4?Gu$r._KdX3&Sn6e5yN{iJ(-1H:;`%wSS]D(I)AU3Aoq6geCs~!~y>Ym&i0idqyCWb7pXwRxv^2aFRHKc)/=G~16:(iQuv:^&(`R22OOpQB.OEBFp-%I6k^gYj1wv1
bKS^j)&k:ZO@4(#mI:I,@c04yWn<<ESD^!_$a<)PN;r+jZ2];6]l[MG$}nXp+Ik,e3@v!3xt_y`ei`Hq!mmsT4_g8fT=}C9uu3{NpI/d~&cTklAG57%7u?&X3bn&$Wkjj-;d.aZc0Q*qH!tBKYYn1K*>wsKH4_XSan|ii&3E7+#M[AS4iMog$lb?p2qy`MAcY<.%:QAQ)1U%>whCxUreN
-N6!q:icxa>V4h|;)9eP{1$
#Uq(|OAZiK&Q"gK*c[QOU^ZM7?i2!N[Qf8rk_n^Q7L)g:&EJz;o8AC.-}%u6k
_xXV1-dV79C5tEYN_SyZS<Q9mtdWpfOo/_h.IU9j}1987*Wkq%~0(IP_gls#vY2pXfWj?f4W9![=/y0,1MBBl@o"|kE$A7=D<LF%2Q:/
P[H~b#lnqn%T2BK>(@m1Jg@AJf#E(0mB=I#1:Jg-keW&Kh+)G2J%#J>;(LcoP/#C*=^SC6TRBoKpML=-X(^lK.?;3j,"Fc&|4Ay8#Tx@>Ym#hm=7x:1V)/AT=S#,F.-Cqy#?##xYFM,C#Y[L%/nPXN[B([NIqU&@T(=_P&;dkP[Ca^e
:1@f<5Q6;;Gyv9#G)=6Tir7K&Q^U,4^N7lMRFZ8coZe|j?Cz/:b|j<:ZXNRUX.cGjt2=`kVB#KZi5bw`J`m3Z5eBh.ytZBk~Gnvh$Tr9qa7K!(k]ee[=;EFoF!2uOA496,HbPLDcOz&vx)Sz+<@(26%d6an3q0BJR[6eoM(?Smve%#WgjNTg)&uPhyOsuLM29m:^#-n7Tseyo-J9,_$vxW@ogw*hxq$?:C7.C)Vk8@HV+TsCU)h$U.]HTdFsj$"SbH5@bkn&u3bGc;4@p91&S)P[F"+t=xG%V(a$(Yoa*{No!laV/5=Cgegt$VU5-uY+WqS%WrDi;u]^;QZvdk?R]3K5n*j:p|r;w>2g1!d;o-2
&beR8
k[[>JjSv>pyj_LTBjLq?
udd3`#i(&hlS*8>I~SJR?FdL{p-A*"]TJrU2r:`[wB-,N-_LzgJ#)ptO)O7Voxz[~Lq7H:3E"P#OP!*Y=DD-.!8v(]^VGRI#SK?f(>aIV-QUXuIQ`r#VjOq<G=l.DWB;7$C&&0<
c23NUTN]RQbF@JVOzv}h3;HX.#^$L231cg~m7bp[0K/KA9|cqf`gw@1t>
8o=H+S_-9+=Sl(wv$?N8*qY`Tb5UN>^3)O;
Yl68bR&M9JmX.B"0Pd`F;Bz2!v3mi^q)RIJ@1qP1LTXi[h(9OS3wX7auBD]ki3
r6*7%fxY/Lrc`wu}]L,$T5vF8j8L7xJ7#q<+f"%NyIho]-iJ1Y>|?/5llT*m?~&jxY1z%60ZgsMzp[N/q>NiqDEfRIm!XT.]Eg`5$UQX:jF*x!AwFeyhT}*`Gd&{<QdnP0iB*NBXz(-6?(:>YO[<mBEuDyF,I.dvIi"7A3X%p:`9*r#5mI6hY,v8(KP/CqWK<ej)@Ew
1nIbj0`mdZ!SYx<`v-/%na&z;/gZ,N_p%Z_#rCWOZ#UG2HX):(wWCgfpr0.?IR79ub*/Uu`Fb3:j9]CSrJ";2]cvoRk^lLT~A)A;"KTNI
GFTl:nHIg;=C14K-kD*t8?9zW=Lr2UQ:=bqQ7/hiMJylB%2v(8V;VQ<e(=EPO`[/QwbGD;QuC=e%AmiZ*8i]YuC$=05Dxg3J,CwmB7&gB.g^K`;/elk9ioJ}%3fXS"&Pp:Bm>l!$6?Brc;g@A{RcZtZ*N=,4N<9tg:LdVuV+>5lwD7Ql(LH1ZlUG!gyX[iVV.vE^RhpkX6X74U)10GelOe?~WzF1ryjG;8X_9tMQBAH+6y)&rWSvt5REkR&b)aqu-
wd.(bu*fw3i9YqFs8&=J9Q[LZv
-MqBLrqpW
-Ft;lOtQ|TLtW@=C$$9>v>+!scQLN(ED^5!(BBr5TK|;rdoZKG*i!-^J:3piQRz;2)5x`49/w.fPC:"i{FbQfa8?vhyYA>f,J*W/~B]@+i<3
w~Vx97Rl8;lf5qp6@ZS{@*?c/N
6m6<oq)jR;Sm^jGp9v).WUM.9XiOq>nahk!RED43GQD<fj?XwEiCfhs4#qcuKiqX#:x)H+"?b)3&H#^fshO<#(Sq+JU:]pp[
pS/eTQGh.jR|2VP9jvx<fL9A!Z>wLu"(^lJ3Y<8O_<8kb-HO[ynkJ,^)UQT4Zhvq-GxVyO3j-n.lBqpsMjk&au9;29trrdR(>GQo0`@ZA^T4#gHzBAdB';case"ja":return'+Zu@a:{Z[1*S*mc#x
YI^@"4hqij`s>`75tWD2ex%yo,J&Hhh-k2W"8%+0OFW!^Wu8$=H2f*d=zMO6NtiVa4=3@Jz`-3<P
v
7_ojnSGzU|vecy?ZT6xT:TRw5z)Gt!XH]hiE:s:;EZscBn!%=$q[E&z"]jK9y`E"jUS@WL"=JnKV8@IwqkBt#2v];nTc;"cSaUa}`VS^#kn1scAgD;bN5-4&w&w(!)In!(++X:VkfBp6i]p[a~Y[BE_H)Fc=8cmtAGD:%bqyY;.Llf_Iu|wV/6dh[OaeZ=Y"$^pJp%pej`N>VPOdl>H=?[]nVlT[H?r^IuErIkwnqcb;$`(ue{lMYXbgx0r~1=HA]74_nWnYRng,Xu>,1r-GB%p6k|Hoq3srrumas!UXhQX2LNJ<>G<,uHi?1$[|nP$WcaASMWZ<whej7R.xEKdLL.m2<{U`gBUt9{Y.
nHpofT(5rH(bmGGi*k8M1h`k+%HmcneQJ<c_30@`T,>r}x&sRg<q8KzRBK]6LrMZXmpth,:WSxfKI+vqlRWdV+5Kw*lrDEq]g<^EYn&?2ZJc>,77P/S:OI#j&Q@puikw_6f6R]sVkJe*F$,>loC/%giEwb(ykQ;4V
mtX2^?Z?Yw$cOyWb+5kx*7UOF9gw"PcF^NCmjH]R-f/E`U
qA.?Yri
hTVbb
VW)-f{X
XW!kl^vdDBx~AyvIsFNr&<n5N+fpsJ;~AIlTN^YaoGsVF&%1]TGO_U!x-2?Ikuw>]WL_[ayVwHvnPNZVw!&Dty!*i65YKbc@-u2X8hYq36b}^uICBBGR4%p&qhi0gJQ0#GWRlmu%J0%ppD4nQ/H/vW+46j>[UO[njY&Y:!@GHKVmMC5XON:pAJ#Qn3_QciI`)g2StS`V]Ja-6gJi@yXulK[)%XTiSclT;VE94k0/LaUkQ}<.N_TGi?eX>@$CVxwh("TzlM&[?G=ac~t!z#*#
TO@rB&>AN550>_hBOtF4;i)<u5S(>
q<hq`;qot$wVQp3=`6t%KdNEVR<FPY[R2QdZjJ?C$Mi#uu^!`gBMt,sAs!{n4YGad;h_
BXSmnN1udTQMd,:IJ[h|&t^bK)d_1UG>;01^c[?$PQDty3_M[naNR!vTjtS`aET}-S-:V~1orbF+7pUoD6K!0|G7131VdWW?R0aywU/z,xw=E&juZ(3]CO5fm0i.`gxe>*rLA2=8Udfv3[rnBKX?J@$@11)AjS0<ax/
/]ZA++%"dLKD+tt0
kwEk=CkrD;>Y6&@Z|u,6$D%%]/:uNfu!e[~J9=Sv`(s6+Wu]tIQ#s*u,l?^7N-~@1_Wn?*Qf/*:wTu8sT@5x+;1<wsG7A_~h+vc(~_W4)lap9nrG_xiL*HEWytJn2h;Kq4%Qv;.DK:
r87[N&#FwHygpN$m6-AnUeTQjslgi$g%Zpc%IVP2g4P!Xh8](#99r/g^_Hr`CYLFoo)2z#d_bGW1/>Zasz5{W9,8#t-+7"/He1&1(%WSneu(A:Xuom_xfYrRryxdp
.J+"T4"XLYI04>wyMN,XM~^KChF#E4D)Zi?Yk
%ys@QfR--~ZQS:r["q;9dvM<?Si,C{y9:wl,xm%pX%s+BJaO#]h(54u>aP[2%#[HJc
>%D
Xp0@2)IWg@rD%K=t7/E6z
:dfSy-U$@ozlkZApA*;xbs3oJPkCqM]It[<Jw-ba`J;&I%Y.m0r+v.04o
0ptm"wt-D8o-bpNED.3L[.@/W!?"L920U30;oinM8O}:U8q4p=ch-+X"is79OF"b+L=`zAckB@N,DcJr
(E"Z#"!g&OSG(xZuG*HW9<XKYQ/}.ikel[o96z-!Cw){edvmR/_aoH>59cg[Y>3q@z*D7&C]=!fv#D&+rX(dh^GgE..2VtX~].3
lQjBh^jL)^(5@!wT5z%8F)]7LHCZF|rY6.2+K0fG@D#
^g@
6ITz8!#<vr)g@$]<=MJM+QeM*d;SekQ,=*n6S.ViP-[jSTy~H1q&a6]t1Cd#?*4@kC<!Z-8y`1K"P&IQ"I>#ndq{#my8*,/b(_?j$e>Hnu#hereTr%$doQl})j*bTH!MH,(N6G_bpaJkJ;P|)e
(3*WHsB-5,%jvg`1Y)+bUu:O=4a@rGGe7NT"Q"C&.`*X8M!fIed,8)e?[x]NI@e41uK!]$u54^0KrUi7-4I2PHJ-3WwW0LP-),=_c1*n{KsqlyVAi5#$zT:ogZWZh>bu<r/
+Dsa)TUH&>YdHl(K.Hhx&-r7]?4Vx@o!M
AFHiQ"AmF>-#$p5$C%_6mXmJ~iwR7+bve,gC$o$hS
g2{vY-;=yHxkz:0q;ykxnf3o5lc$=ekSBKUL!38[iPr2|qpoyls^tYN/1eeK#C/>!m4ck642r3xnFnXiD2*Il?_6RDh:8>RjH,=j395Ty7sdLaq=pRC)m6FAC*Z)9ZM2k[pAuia$t_*W9]QV@<-M*
+7QFs,&)DF=Qq0faos00agDm;wEUMJ9>nH;>7:<*D@q*gZiDJ1<@_p^ZTc{hDOyr~%j2bS,R-Ecl/ae
e3OAjw,vKuhraR/P0>Qgj^KAhn~RU/e`Yl|.4hVfTE$dL;(7Gk)pM_T;^^H6"J:/>k>4!-oFVj|+59,:`NxrE#ESRS~dMd!9ncJ<[g6!v^xwh#zA-Tk=+w7T~KeN-8<#j-M+:X"kt<7`Q7dL
gLlpCUa<(D
{+d7XsN*zia=X!v5
OZo``KUQ21f-CcP0htUcMA+UD8TgNDL_uM!"NYL_XJRTK;*bo?*%<`I]<
4jZP>}*T8s1dIO,(F"J{$)L"X-;7AGZ.+K7(*w;IfW$K2b8TaY^C_Obke2?Zx{<h=jB~E?jJahQI,kc_3}-{/7&3(CtmZp!ju_hv3~T<::NK*a^o=Q00"4IkIHSDi*fcaX6YO$,*pSnytaj4&,&HXWswL1Wj(:RR86U0(yXKf@rp#EyM#kNpCZFH[YlFtk@Sr)%DSh53NOZ3De2<J-+*5.$;eo01
I#V04AC!yBRgr5}XA"Vk6C:?a!XHgA(oyZj;?9}1+=tMV]42uFmnBlF:bHZmK7`9}-T@>-g,Fb#.@%nN{9^X2fV0ORAhnOejT$6_OM_[2^!].V!k}u>yPD-d=?K=sy^;Qd4+Ke]n@*Kt3P0?11oJrR5SQlzV:x.L"K!+06O
mMO=_1=%?6Jh~@NT`[+":2lLLX$gxK;5)0z6|1rQ|V~J#RhneB;)IL
_2Wnr`Z=jtUPYrDGnj,Kc5yzhEPZ?L>WJBV9tu&4f|K,iWiIyP?DH66AoT"JP-eP=<.R)*-6^rz#TcAPJ_&Vk!MnN{)Mqn.u/Pf;M)BE/OkugGU6H*aR#4I"WhF*T@I0#vw58Nu+=TyBbj8[/dw=W-V;?tCKw9!UpVhf9VMttX';case"zh":return'%UF5h@Q.w0Gi,hufe#<=@x!XbpOBwLzvId$,p"vDL5|Tyg%!S,@XW-HnVYPT)n-1R:IfyciDRY,LOJOs8__)cg"T*-isp.+,OPaBBWD]1+"=lo6ob)sfCW|6{AtT/ZxD-hc)QbiIQQlRar`j.g`N%51<lVuKlDU/w;SirISFv4>.9Qn3U,9g@PA>(t<eG(Hx#%GCcg,fs3T5Fr]WN@71T-k>*c74L`@cS`Kdajb#(vCvnw)rUI46:y24v(]j}EM4I2
@OIvF]jRVg9DUuUkG?P;B,ZtF%=065*6n{!eK2<}n!0_bB
%>(`jMU(R](dbRu@5Z!k5W:`(HsmgnIhZYxl_an+2$H993CDN@jgrx{(eX
1.lPh|o|gjEsT,L(BZ
iKMZnRKq7PML@INPIO6m`*-P?5m^w>;lBKs0b:<l&gq/%/<]bl{U>
^U_fXoiDw^%QMSND%UN@tkdjOQ~:.VSF#<&K2atMwR%^_!by;^0bfR&^1`mp6tjHbJ.px;AYJHXIBZVh^X"^cmj:R1FEFSd+R7I0*hc-r>YJ`>%DsE$Y_,dSBpZ9@Dm077@>V($X0FMMxVhg}
*5xm"Zl5HX`N!?h!Lp:uGFXfXT-Oj<ZDGK64,/(M=a9VDEH+u8VfKHEOL.A2D6a^3hOQd"TB9dYXogc-g$~fz;8f*=Uq)XnKp&C233>yOj<&t0"%9d53KG|WfhIpQ0.E=7S236po#pvfo,:v&BY).Sr7k
;R;XUYJx0wI/BKQrGBY.;!u-v#a(9e9%>.3%0
ogcK`n
XvDKY?vneqK"0^KY`H;2w{4[^1Xhd8ZbEO8Z;(l;[>wom/im9X^Fw_S]Ml+xeGP3&3S`%OmK[dt*;#FG0S5rMJgY<eh
+8%)Z6i#cS+6Nja)o;,iNAd]biKTd!cqx!pXB!N%7
w,T|K:e$c/yFI3;IuhJ3i|Q8*P^;OUaj/JE6
rVt/t56x3TRii>.LE3I"gk-EGTyK>Szy|/F*=xnw/;fhTer`*vU
Z_TkHv-qt]x70-"Afu?^~)q,elmTam(KflT
)^4qN%b[h9gf#1i0ceXn*S@A5(m7U%iH,Au:Trx_<+8AU&SWk`dm|V5MXCgdT$Cd35Kw):oo.Dd#iskZuu2fxVy)lUZ^Kjc/#jd4On,@,]QOx<OOa-a"BAbR>6c^o9{:ek6*Ti&e"vkc6"2!}xl
aw%1@j>LpE}kHHW[m0J"kUKuPXx/FRehs92OY5z1<l:n3[Pe4Z/qMBxWN!lHU(#Ci4m2=@Z@;pm>q4PxxI$7ta#vNPqBtmLtsbIhdGek|`;TqE#kiNc3eOA?10Dy%0Im6
l?H,{fkAa5neRknw!y*s0I4=knnw#r+;CcxsN-~efSMXx!&Wg%{a@0oe}!!HRwkPHVv"^Bzc.R"^@b.w
j27k6!p(pXXk%N=/k%WhvROJmj<[=|_j=S1:NJcEMOV=[`1~[8JGMc9goMAYcZHt&PeXm~RhA7N2]?hoRc_Lsw.NOCplI<`-khIb_|xn(,"TSm`JSL<*
v9`WAd<!vDV=
Y@bsKeB
75V4NQ4hsI`jpL$DB+Q[Wr4W$e(i(@)e!RH{0=1}/<G1_}8^
&3"O|y&ss;`2@5p>5N?nmSyd6u{IMLAxe"i=q>pIFKHhtUT6n;tAKLaT9H^f}_qA_bG1q(5yF_twqJ>>?lyf;]uI-wQ]m.l_UP[G^&%(!U("
B>,/b}<9Tz`GaFE~@X?WZ%;)b^oa2a.N=c!7CQY%ZL8`.D85UR797Ad.ALF>eP_$5%Z#B]g4(u2,Om,0v#kDxYF8bvg;MZ9)"DYwq%lV
.P/sC+~9B[M5Y>Ya+61XJ+2f.h"f9E[Y-6kG%X"=T9M>>,2P)kC7-kuRZ
PO,"a!d+5uKbpAwPh8(!{VR4t"R%---qDBOH("|xLEF,UgHH9X>7B3sDrXq6uM1bX6LlSkg&MHt^g6yn=-fUX6pSCE`&lT^g#QeYK.32J5)S.6C(RNt-5j~pb-xP9)(!N!TYYKd6/y!:SOfHobR4P$
JR
w(<1Ky?ctiqQB_15pxT$-&qEx./84
[YZAn+5CF2D;9d7?T?5="]A+bD)>`?=2_adew//bRj_Sgi{0S#2],"SF
IW]ZB<s!^7rC:waE9EZ}!P9K-TM:Xh#F1jmoVf]rx=z)=(4EJ>5WG|3&o<EqUM+C@I$yAwI
VvwDKV.7/`ZdP_,8Z"w]_2Ll_)ZG^o&"c<Rd6J
^4kxr*bl]Iomo(l>cu6^"qS.~$)2G
dlz
=jCEv^{R%9O14p@@/8CbDJtAjo1$^tN=?o&dtfs62>[j{f<dQPj&ukh]{>te-<p`rMvtxNhE8McEA7Phw[d%mfk_nDa,Z(`_InFNI6p[};@F4O_FF0:sFbHJlIT+Fg@!y^/r>Igx/KkY>/G(!
1?KHQOURGkc%q7YI)ko(`e</mXwuHX,^/5)11jvv{mF:T8|vkBE*e:}Y>F;Qr>c$AyA>WZu.0G$RmP/oar@s^XFay`S[~_8Yj:7i{>sKtC(_q(5;.p?krtu"$cYsc4nkUQmqlupvjm*LJKwp2yl2taJaF:i-Oy;u*Oh9ce!In.Cn4,WN/ofMey,=N,0XCWY>sU9oXljW3"]Q[Vy4ZgPe*(3f<k#n)<zJRF[,Q?BH4"S/FS"qwbx#jm-mL%S$XVIAAI;q;em`XF.SRh$Dp+s&$n7pMNwj2sm99C7TpGjxW#kK"h494-A?BCui2!6AmYZgDiuH+DtsyX(5X+Sd(a}qcwaK-E)`nk;w/M3#4p.oVD^hLsecjBbjg^$Npj;<Pubd>Q[NXe.8/KR[;m#xe49BMYq<Td7Qy5tkaA9":]X`I!SsU*pS8U[gw+]*A2pwoZI(B0p]Qt`lQ?,;{>FFF1Mry-@#"nE&>AY%ag7:FkDIj^TPM-w]Rv.G|djg>]CN$B{iXI/std@';case"zh-tw":return'#UF01lMWr1jf3)i[*EPw.sdq,tA9ev(c-(s",Sg/W0]#.%YU7<9-sUB&PU7J-%G3QwbBv&n[3DmB?rb_w(t@]"2$w,F:e+Kw6f(#V;F5eWPH3],H/Ki1(;L@^9mTDqVBMD/4R^:lzZ!wT$2,U;f*@4>?hwoHa,;i0HMICat+UGjO>Rm5},~x5S?]lhEP=hajJc*QE+r1G:=MW:u1b_@h%:ohiC0/:="u%AGCnvR2pbq0k[a*bKpJUaV^qrk21
qmEnz<pWm!vu:rDt!=2g=NU,[%t>kpUR0[0VkAv3&Z7>+<Im"[XilU8%u_f%zH<*;
/!$^&otuUWUMWI`5gw#C[EOD?<Cf/qU2OKNG`vyFzaD`dDoTNnJB>`%7*dUcChmlq[CT;E3tIN6E]/0KXCMhhhZ#PpI8zQ-snu<FZhA1<7Dw[O|K3kfL4
u9*@@5UctKdcZL*@"ER=/+2_SJqm.(aNUy@-7wNYW>4PWW
Zn[bM!eXsfa_dm[ejV
kCQscKl)h3|:s6Gn1yDj>2!RD
5I|VCw3nP&i^%G(0xfP+EvD0aU.lqe]kC:XIaYom)$I-BNABNw}Ki.VIV^EJH?t?(7l,]!Z`VFU_?v;W|fUJ57FARefEHoAV*5UcWD|uX
.CXaRMPvF,%y
en!Yb]sW2-m5Y^l&bRuHe<,Ae}A
5/u_;>p9=4qxL;Iux"%y^S_/em1?OaIMqkV"f:;IkFy`[2&@LpL9dJObReTU)ff:`1]M(HA-C=h_UB:#&xt<fv?@I@0b4):2v;L`9QkQMdCMt<jYIaLJb@pn8jA6c.YSpi$*kJ;UkFJuc[faJV?Q)8IStT^e"i=<#"EVi]K*uA
bo%ccUxY5.(9K>&;JhFK4o<lwBDA[w8>;g)>FQPch1ExCDSO;BZ0y+<>ufK]`UN1bRmX`HCip$
k*`^nIO4z)fSh$2ZI<ctGH2Iu@REh1iHyEh0CBj`:3DO4B]
*D)9y.fpVqZ8)qd>r8b?.`,}>#q[HC:Ikv[N*>q&+<B&j<coAC$EUulac87}gEg

<0Hxssrm7)_1)SJ0fX.[Piluo13BN=ZNN+T+H@z"n6h*y/D)]#tl%d_N0hm1c^Clc4:PK+G+qo&y6B&a(V&8Gn3c`$7xs!hS1R4cZb9B>:R"aif2D#*"K7&1?mY].sImSIo[:skc
OCim]"OquQI"C!1i@.j$gIvHx
"-t=_6c1$<7+#3E<k{tv&m_eC,iWY""pAo,h@tgu.S/ZZ{Ie*wU/lU,p)bc8@ApP7/y=56uw$CGfw#P*o$JEu>RNh6TA#yrUko7hXi<]wiY$;/^EsX9$H]^xi>=C]n7Fe.t/veXR29FZT?0ZY+.pGF73u*9%G_)DRTt.qn@hgJx:_zSd+TT_x>q_(
/eDzQ7!Rd`ujR)"8";Za(n_eL7"];ltFwMhVchgFwHpi?VNeFCTfEKp=CW!fFt*#aS
AWI>O=DYF
n#TR7+U#{e
s<+RI[.n(dSSU?rqb.+Y@X2z4|3g==cp*xl&974Jr+-|L`x)Dmgje#O<1lD(tD"#k/%t+$GkBV[!:wTxqv+E"LIAf6D[rUTvSRO^
yJfGYS3`nE)NmFusyT9"$TgIQFf88S]^IAMi
T#CSXkPih/]Io"Eq&r8SvU7F4XvTOCaE-e$<-K+K=.0-!RENRR*rrb0BnI/?:jr0[KFg@{NWP!w:o(w#$k)<L}:Q-*&n4s"v@o&spW1!![n,yC?h95S>g<y]R14r
5F"v,$%Tt9S*^&Yq@g2l
gfXMrEIN"Aq)DT:gG]-?Y+8r@Dol6EA^A1Kb/4=DY]&S>663gy7LE98-hvEMJB?,u%:Q_N=)7GDEw>9J.ApT/FBgf"!{bE.wXigwX:Cw/!lZa3Vdi_X6L*9#lCR"Qr",!g#$>:QuN0jglnn[Dps~?b_zlPkq#mj&0%C`yC)7].P[Yjr&l=JE^-"@&d]Uk@AEed
|aD6|H8M[n{?5r>xrkSu5Gw9chN#}><p])>$CR`&rsJW8.7ApL;w>SFRxta7BC]O]H)&I-8
?/PJ*LF34+49~%vPg(Tfw<IXt99QH_n6[ocn_Opfc%tPJm~-bO!2|&!jFbptM^69l$s5hg[X[W/?Z"Vf`#n5}OU=W>T"(^RFQG}A%1=T{N%oW/Mc1Q<Y+/!?/Pp50l@./MAWSxEG<!0$8sf]Ie=d%YV_:J"m`)(<XPql0D9S0OkT75_,0H:awa$"GKFJ?ezpV2zqGM72kXY+m>FgE#U*!>8Q[uKKZJZmzk~xu
0)be<VMCCjHfV8FWd7x`8L|I6R9`y%Krxfg5@Avs|Z{lm@fx9o=C`?LM~>Z*@fzKLda!LQNd@kmytg$1>e8yuA][gNw-O0WafmqMYCP@;Z0&v=Oka1_[bvgBAN*qXonQ/N:wHn^HY/dTWb*xN_sb4mwt;ncl;_+e;+n.R.H*U8nT4[
<.s8#xFZ@[8-Mh@l$*(smZ:#=*eZ0lkIq^C`CoJOPH=;1]y3QlBXj3$Ic=d;x5v?pF-y,Nd{lf62NR#+8mt?BA]smmi:&7V$&guC#=T%.7j!mRwu-pgB/7=S?^=w`$r&aBgW+~^^v>)`p-rH.RV@Hx?YLKe[S3k45EmrX(P!R!e}s}nj;+N;6y!2?#CC7hwCW~g$I"0l5l9FxwQ@d=T+.qhX&$^fVPST[`Zyn#TKaT:qa9&)O:Kj[pyLtEgOs_4<^,vNIhZsV6xlB)u|.VEI$el?#dyft#q/9tqV9;)G)-Jz!$Q`
G2`rR
L=!5ti=n3NnE;JSoWgsXn8BH}dC@5@JZ]Y"!aFUd3%K-MV2#=Blfe]I-LX"nP_&EZN+:.g&8O;5y5KuY4xTN~lfmwTy6OAG,+FNt0LeGh8t:oHcu?%7fUM)"6.|58-`%YBin(U?<I[;s?/y*eR:Q)*y?gQi.8`BYX$Qb7[&D$$.0Dt0(l+{8T7rFZeU[KrTD(S$$eh{QSo9qPGTh:+.[29q
>ijJ*mNKAwyGzAOTrNQM&$hw66+wtg{v")".m0XPo*V`Ktv';case"ko":return'$Zu6S;zZKG.nov$0
UP3^0vw.t3Qj4F_7;heKc58^"[lA//8|NCNjGra[GI!Yi^Dbl~rXnu2mw(J0m9iDDR<~=C0{wyLow@i%FDa,9:ar>h+r!#[P5E%0FpY]*70b>.uY?QDY8+e
fW#_P2?(w[_(V(U@$[Bq*4]Z7/ceOFXaV}LqO<y,fJ7`j&SAH8vFMXc/Xy!2]0GfNo?I=WFeV@vA:IBFf?X^[3UY*A1Qm8_uFx%B=}gub=Geyz8?]L
i(|Fq4UC6
LJ(grZI;@:x[g^N%X>U]ZkZ*$"-xM4}3on5/
o,wVD-v`KbZA$yH(KiY`_{9/t^FpKl5:gmFp4&2"V&otLXA0sa:~b!e]?sd#il%q/QB4y[DSqq>
U.<ulh)F_7eHy1d8>O@Z$>3:Vnsj*`@8P_IPnRM<uO$eo6v4:;?Kv^UP@-85bb8kQ)q<LaW4j<d]2+c/hTP_yG3mS?`&)5qW/K<XoA#.83Z*"|B-VXpwUspl/`%-J~3C[ij%d@3vTqAm`o"$im3bxn%gc=3rNb4gqcX.4}[$r-_:W7%x.KN^2gCQh8JRqS^egXSds:/ZLPM=ZF1EtLG_c6_4YXmZ4URu[43/)U:L?]h*;l)Y_p2Tjl
J)$&"6gb^lE$:]|?o55Q|/@8<w(EC_S*uW8`fS>;,?o#E`1R)c];9e@oxWS#1Gy:%`+pG+Wx$
py~x><"M*sk
.PDP|Xl`}xV6t=/13q-
X%+QRRA<7b{s
53R.>>jj]YirIj#-i_jF+s@f(dSad,H"$jc"uN1zjh/>#=F}q+SARdNE[QUnD*,95o
&F1+fEu4QCC,nj#!lf.pW.DHVJA`4p,!}9+-3@~hdpma0JR&zmEcVxQr%uYI.uG%2hH0H<=jOc%V:;!cF){Z.1KD::Dd3mJ$Gq/R
.xJ2$TWHy)MM"bMSnTKaAaJJxSW4IAj0q]>OHBK>(N*,.w1xO-2NJ$MX/D>D[VSqjG:OU%Shk2)iQ{T>1{m&tW(Qw],*xOwlo#jgslQ&e*8L58C#w*Uf@>Gs#[@(q3HufCHdyUGfyucRny`ymb!2Hk&1X[xk3.M#RqN<N%7<`cUDeP[:.oq2o?ED#m-s7tq?Y_^7$;r{Vov#VX1V]MEST#FkXh7i=f=vHUeZY<i?f`O[iec~S"E9(6b[?LP~,Tb+X?+G>y74"Qm2XM(}^/X1v3B^1}8k:(hLKVUSkHR,6BQ5YT.RlPGrUM#IDk2"g12p=V1n3U&`S~6~$ke5QSZ^3vCByyXM20pnj~$HR)YAa@5h11+=i}dksM@<Mq:9v*M;RBa`CG6t6}!|rP8S/Tlym#c]<-%HeP0>hBGH7j_/JTYg"DF42?9:.UD*G7#FMtZ7;ehBtQ;0_.:}j4nD[t^gYgiQ;cmKf8J?L|q<Z1-:#Z3R:=SVxgw6"QsrP*sh>eG:4gy_[<s&6[co1s$^]SRM`rc,uh_NnE]7Mp@Ws,]-nD9LKGtQMm+h.@gw5r3Yd7@|,/abIG/1t;5<k`Bqy/&5YF?^!8-5HUhP=>meJ<,rxsasY1^u:?Cx58KZk%AiSd297)8ze@RF[(5*w.kc:cYB*ox+uVXu60dkt^@M80.#T|48FBIq9Lp`t9n5*q&<[d546R2`YLsFc%%%8|JbwA-#UTEQU;_C/TOyR!F;RIL`QD5[/fU2a3@uSrMw_qA6W5dV_-yoUL$d&U=I7&drAMfa2d(]0w;8hp.UQ0&rTs.;c#v.)F-#C""jD]ih#n."+&6n+{V1X
V{/SY<[XE,R}@{oOQ/6I+]Uyx+
_090GBdtxWzq-$}N(NHs5wJ/v(5HL8}
7LP*td$)LAPGzi@S)0GY2S*M8=qf<[8Al"3r.QD%D5hE`3VPKTKTYcy6<W>(1
PE3i@_n2M_0qMS@g^(>P]d|x"h5@MV?-p%0;ji0+b"j7zoXvQeXAc%IdY2lN@nHm`E20]LcI}]~n-<304hQ(SaEn%;%KX"`+-kCc9>#@k2xTfYmf;8S!mV0,UYS3l*4%sM8krBJ86%E<(D)U^i_VLu|R50dm=PSh-3.Gfl4CK"?L7f40bedU<c-%NfIhqW6,{fzLOreB<f1os]x4]X/*4lKab]YJmj]i.ar"N+l;KE;t4poHXewequ;ZaV
xowv-*O-8x?hMfkvlH6HcS2!,3d5:;y*:CwktFB@PA5(@W]5RGdOr@N;POt7h8+s8uR?R3K]#Ac
Ft[Sn)N_wIN4Y/ADg{+Kbj/,r%D79cb@C>KKJ"[vevdI[bjpnC:*Miv(V`;`;SF{uAShaWAxgncpf4:^)_d>P.N*c9K]ma=(K%-s]fIni_uI@c_%18^Di*M<:,.0`w8wC;m*o^$?U@ujI{)u4DXq#mWUF7Z?SbVM$l*lVq]sf8rfAF7.QQ3FCo(XhXWSQ_5oi}W.=7&<hC5>ptgzGy5GGyC9U>.a,S9i3)UYU*.pK+uX:u`2l3@8w_ndX@)d0IN]`_JlaiZg+GA/Zc-{F`tuPRB*9e7T2"H.a}-ZcDrge1b56$O>
JTPdJare+35o%xKAzQ}8Oiz[e2[8!
Z2t9f[c/FG0"nPSCA8qj4R[A]2-CTh+w62-1U=LA_[tWP<}eo-a^y^U%VoSfzYVg|7>1Hnqq~nXPBi"xV^]-cE"Qp-+s;/~_ntqv6,m19XQyP019/O!buQ{V+i6@!T[+TDAxY>^g|GDqY];6q)O,3kCntn5XXsY@yE:g:UUK.^4jq[Vk{gx_;hzj|FWQI*7LCxx)/Sl$t6s#I(q>sZAD+/wj{brxAK{w/*5
y5{lP/P3~[AJ(<d/56Gd+bWr
n0XYV25$AKnpJU1dgOvM>]&;,]NRK(8rt`H1bc::CY%#tRQAMVB7LG%v")Csu0J&k}j*28?3)jspo+eM2ru9jWro"6AE5k[zL<>"fz-.3xx,Eo,:k+yO,e:
u3%:*/X*Q#)6=9;vC)_B:H9/Q}Lzp]*M[jl0K_sf3<9@SKc)?r
$"YRb<9-_Z>98y)=_EXmd32e5$jnAr0Y?KeVtAx`Y."=OXr/e3E@
G=L"m0?.QnH|Yc3`C8*mOY&<#glLw6vdqx?UymI?;"U`J/=2Rapz?oJ5qvc"7Y:a=Todv3L37qo0vrrlB>s,RN(G7wrc&zmUUYE+b{B{hI]%TB[3Dii!mR@}#Rmn%
=j@"p=<3gB<nGME

qv:c#$ZhlE_F=/ecrMg
st>$-759Yj[n&fVNEfj';}return"";}$kl=LANG.crc32(get_compressed(LANG));$jl=$_SESSION["translations"];if(!is_string($jl)||$_SESSION["translations_version"]!=$kl){$jl=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$jl;$_SESSION["translations_version"]=$kl;}Lang::$translations=array();foreach(explode("\n",$jl)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$O,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Zb);abstract
function
query($H,$vl=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}function
begin(){return!!$this->query("BEGIN");}function
commit(){return!!$this->query("COMMIT");}function
rollback(){return!!$this->query("ROLLBACK");}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Ic,$V,$F,array$C=array(),$nb='PDO'){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$nb($Ic,$V,$F,$C);}catch(\Exception$fd){return$fd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$vl=false){$I=$this->pdo->query($H);$this->error="";if(!$I)return$this->store_error(false);$this->store_result($I);return$I;}private
function
store_error($J){if(!$J){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(26);}return$J;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error($this->pdo->beginTransaction());}function
commit(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->commit());}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error($this->pdo->rollBack());}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($_g){$J=$this->fetch($_g);return($J?array_map(array($this,'normalize'),$J):$J);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);if(PHP_VERSION_ID<70100&&is_float($X)&&is_finite($X)){for($ui=15;$ui<17;$ui++){$J=sprintf("%.$ui"."G",$X);if((float)$J===$X)return$J;}return
sprintf("%.17G",$X);}return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($gh){for($r=0;$r<$gh;$r++)$this->fetch();}}}function
add_driver($s,$B){SqlDriver::$drivers[$s]=$B;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$T,$ik){$Hk=array();foreach($T
as$R=>$jk){if(!$jk["dependent"])$Hk[$R]=array();}foreach(driver()->allFields()as$R=>$l){foreach($l
as$k)$Hk[$R][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Hk).", ".json_encode($ik).")";}static
function
connect($O,$V,$F){if(static::$serverFile)$Yh=server_parts(array("path"=>$O));else{$Yh=parse_server($O);if(!$Yh||($Yh["scheme"]&&!in_array($Yh["scheme"],static::$serverSchemes))||($Yh["socket"]&&!static::$serverSocket)||($Yh["path"]&&!static::$serverPath)||(substr($Yh["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($Yh["port"]!=""&&($Yh["port"]>65535||($Yh["port"]<1024&&!in_array($Yh["port"],static::$serverPorts))))return
lang(28);}$e=new
Db;return($e->attach($Yh,$V,$F)?:$e);}static
function
disconnect(){}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($R,array$N,array$Z,array$q,array$D=array(),$y=1,$E=0,$_i=false){$if=(count($q)<count($N));$H=adminer()->selectQueryBuild($N,$Z,$q,$D,$y,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$y&&$q&&$if&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$N)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$if?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):""),$y,($E?$y*$E:0),"\n");$this->query=$H;$gk=microtime(true);$J=$this->conn->query($H,(!$y&&!$_i?1:0));if($_i)echo
adminer()->selectQuery($H,$gk,!$J);return$J;}function
delete($R,$Ii,$y=0){$H="FROM ".table($R);return
queries("DELETE".($y?limit1($R,$H,$Ii):" $H$Ii"));}function
update($R,array$P,$Ii,$y=0,$Dj="\n"){$Vl=array();foreach($P
as$w=>$X)$Vl[]="$w = $X";$H=table($R)." SET$Dj".implode(",$Dj",$Vl);return
queries("UPDATE".($y?limit1($R,$H,$Ii,$Dj):" $H$Ii"));}function
insert($R,array$P){return
queries("INSERT INTO ".table($R).($P?" (".implode(", ",array_keys($P)).")\nVALUES (".implode(", ",$P).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$zi){foreach($L
as$P){$Z=array();foreach($P
as$w=>$X){if(isset($zi[idf_unescape($w)]))$Z[]="$w = $X";}if(!($Z&&$this->update($R,$P," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$P))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($H,$Vk){}function
operators($xk){return
array();}function
convertSearch($t,array$X,array$k){return$t;}function
value($X,array$k){return(method_exists($this->conn,'value')?$this->conn->value($X,$k):$X);}function
quoteBinary($pj){return
q($pj);}function
md5($c,array$k){}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($B,$mf=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($h,$M=""){return
information_schema($h,$M);}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
supportsAlterIndex(array$S){return
true;}function
supportsAlterTable(array$xk){return
true;}function
indexAlgorithms(array$xk){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($R){return
array();}function
fulltextSql($B,array$u,$H,$Ta){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($H).($Ta?" IN BOOLEAN MODE":"").")";}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($R):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.1+ba55ceef")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Mb=false){return
password_file($Mb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($O){return
h($O);}function
database(){return
DB;}function
databases($Hd=true){return
get_databases($Hd);}function
pluginsLinks(){}function
operators($xk=null){return
driver()->operators($xk);}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Qb){return$Qb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$_e=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Bj=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($_e!=""?" - $_e":""),'short_name'=>'Adminer','description'=>lang(29),'start_url'=>$Bj,'scope'=>$Bj,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.1+ba55ceef",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Vb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$_g){$m="adminer$_g.css";if(file_exists($m)){$zd=file_get_contents($m);$J["$m?v=".crc32($zd)]=($_g?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$zd)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(30).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(31).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(32)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(33).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(34).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(35).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(36)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(37))."\n";}function
loginFormField($B,$re,$Y){return$re.$Y."\n";}function
login($Sf,$F){if($F=="")return
lang(38).require_password_link(null);if(!Driver::$passwords)return
lang(39).require_password_link($F);if(!password_required())return
lang(40).require_password_link($F);return
true;}function
tableName(array$xk){return
h($xk["Name"]);}function
fieldName(array$k,$D=0){$U=$k["full_type"].($k["null"]?" NULL":"");$wb=$k["comment"];return'<span title="'.h($U.($wb!=""?($U?": ":"").$wb:'')).'">'.h($k["field"]).'</span>';}function
commentValue($U,$wb){if($wb==""||$U=='TABLE'||$U=='COLUMN')return
h($wb);$ti=function($pj,$Za='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$Za>",preg_replace('~\|$~m',"",rtrim($pj))));};$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A)use($ti){return"<table>\n".($A[1]?"<thead>".$ti($A[2],'th')."<tbody>\n":$ti($A[2])).$ti($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($wb))))."</pre>\n";}function
commentInput($U,$b,$wb){$Y=h($wb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$xk,$P=""){$B=$xk["Name"];echo'<p class="links">';$Of=array();if($B!="")$Of["select"]=lang(41);if(support("table")||support("indexes"))$Of["table"]=lang(42);if(support("table")){if(is_view($xk)){if(support("view"))$Of["view"]=lang(43);}elseif(function_exists('Adminer\alter_table')&&$B!="")$Of["create"]=lang(44);}if($P!==null)$Of["edit"]=lang(45);foreach($Of
as$w=>$X)echo" <a href='".h(ME)."$w=".url_escape($B).($w=="edit"?$P:"")."'".bold(isset($_GET[$w])).">$X</a>";echo"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$wk){return
array();}function
backwardKeysPrint(array$Ka,array$K){}function
selectQuery($H,$gk,$sd=false){$J="\n";if(!$sd&&($dm=driver()->warnings())){$s="warnings";$J=", <a href='#$s' class='toggle'>".lang(46)."</a>"."$J<div id='$s' class='hidden'>\n$dm</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($gk).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".lang(14)."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
explain(Db$e,$H,array$Ah){$I=explain($e,$H);if(!$I)return"";ob_start();print_select_result($I,$e,$Ah);return
ob_get_clean();}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$Kd){return$L;}function
selectLink($X,array$k){}function
selectVal($X,$z,array$k,$Gh){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$k["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($k)&&!is_utf8($X))$J="<i>".lang(47,strlen($Gh))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$k){return$X;}function
config(){return
array();}function
tableStructurePrint(array$l,$xk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(48)."<th>".lang(49).(support("comment")?"<th>".lang(50):"")."<tbody>\n";$Pl=(support("type")?types():array());foreach($l
as$k){echo"<tr><th>".h($k["field"]);$U=h($k["full_type"]);$rb=h($k["collation"]);echo"<td><span title='$rb'>".(in_array($U,$Pl)?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($rb&&isset($xk["Collation"])&&$rb!=$xk["Collation"]?" $rb":""))."</span>",($k["null"]?" <i>NULL</i>":""),($k["auto_increment"]?" <i>".lang(51)."</i>":""),(isset($k["default"])?" <span title='".lang(52)."'>[<b>".($k["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($k["default"])),80,"</code>"):h($k["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$k["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$xk){$Qh=false;foreach($v
as$B=>$u)$Qh|=!!$u["partial"];echo"<table>\n";$fc=first(driver()->indexAlgorithms($xk));foreach($v
as$B=>$u){ksort($u["columns"]);$_i=array();foreach($u["columns"]as$w=>$X)$_i[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($u["type"]).($fc&&$u['algorithm']!=$fc?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$_i);if($Qh)echo"<td>".($u['partial']?"<code class='jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($U){if($U=="FOREIGN"||$U=="CHECK")return"";if($U=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnsPrint(array$N,array$d){print_fieldset("select",lang(53),$N);$r=0;$N[""]=array();foreach($N
as$w=>$X){$X=idx($_GET["columns"],$w,array());$c=select_input(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(54)=>driver()->functions,lang(55)=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$v,$xk=null){print_fieldset("search",lang(56),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$th=adminer()->operators($xk);foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||(("$X[col]$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$th)))echo"<div>".select_input(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(57).")"),html_select("where[$r][op]",$th,$X["op"]," data-default='".h(first($th))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$d,array$v){print_fieldset("sort",lang(58),$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(59))."</div>\n";$r++;}}echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$r]",1,false,lang(59))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".lang(60)."</legend><div>","<input type='number' name='limit' class='size' value='".h($y?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($Sk){echo"<fieldset><legend>".lang(61)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($Sk)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(62)."</legend><div>","<input type='submit' value='".lang(53)."'>"," <span id='noindex' title='".lang(63)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($v
as$u){$Ub=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Ub)$d[$Ub]=1;}$d[""]=1;foreach($d
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Pc,array$d){}function
selectColumnsProcess(array$d,array$v){$N=array();$q=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$N[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$N[$w];}}return
array($N,$q);}function
selectSearchProcess(array$l,array$v,$xk=null){$J=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$J[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$th=adminer()->operators($xk);foreach((array)$_GET["where"]as$w=>$X){$X+=array("col"=>"","op"=>first($th),"val"=>"");$_GET["where"][$w]=$X;$pb=$X["col"];if(("$pb$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$th)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Ab=array();foreach(($pb!=""?array($pb=>$l[$pb]):$l)as$B=>$k){$vi="";$_b=" $X[op]";if(preg_match('~IN$~',$X["op"]))$_b
.=" ".($X["val"]!=""?process_in($X["val"]):"(NULL)");elseif($X["op"]=="SQL")$_b=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$A))$_b=" $A[1] ".q("%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$vi="$X[op](".q($X["val"]).", ";$_b=")";}elseif(!preg_match('~NULL$~',$X["op"]))$_b
.=" ".q($X["val"]);if($pb!=""||is_searchable($k,$X))$Ab[]=$vi.driver()->convertSearch(idf_escape($B),$X,$k).$_b;}$J[]=(count($Ab)==1?$Ab[0]:($Ab?"(".implode(" OR ",$Ab).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$l,array$v){$J=array();foreach((array)$_GET["order"]as$w=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($l[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Kd){return
false;}function
selectQueryBuild(array$N,array$Z,array$q,array$D,$y,$E){return"";}function
messageQuery($H,$Uk,$sd=false){restart_session();$xe=&get_session("queries");if(!idx($xe,$_GET["db"]))$xe[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$xe[$_GET["db"]][]=array($H,time(),$Uk);$ck="sql-".count($xe[$_GET["db"]]);$J="<a href='#$ck' class='toggle'>".lang(64)."</a> ".copy_icon()."\n";if(!$sd&&($dm=driver()->warnings())){$s="warnings-".count($xe[$_GET["db"]]);$J="<a href='#$s' class='toggle'>".lang(46)."</a>, $J<div id='$s' class='hidden'>\n$dm</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$ck' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($Uk?" <span class='time'>($Uk)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($xe[$_GET["db"]])-1)).'">'.lang(14).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$l,$K,$Dl,$H='',$Uk=''){echo($H!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>($Uk)</span>\n":"");}function
editFunctions(array$k){$J=($k["null"]?"NULL/":"");$ne=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$Vd){if(!$w||(!isset($_GET["call"])&&$ne)){foreach($Vd
as$ei=>$X){if(!$ei||preg_match("~$ei~",$k["type"]))$J
.="/$X";}}if($w&&$Vd&&!preg_match('~set|bool~',$k["type"])&&!is_blob($k))$J
.="/SQL";}if($k["auto_increment"]&&!$ne)$J=lang(51);return
explode("/",$J);}function
editInput($R,array$k,$b,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$b,$k,$Y,"NULL");return"";}function
editHint($R,array$k,$Y){return"";}function
processInput(array$k,$Y,$p=""){if($p=="SQL")return$Y;$B=$k["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$J="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$J=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$J=idf_escape($B)." $p $J";elseif(preg_match('~^[+-] interval$~',$p))$J=idf_escape($B)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$J="$p(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$J="$p($J)";return
unconvert_field($k,$J);}function
dumpOutput(){$J=array('text'=>lang(65),'file'=>lang(66));if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($R,$ok,$mf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($ok)dump_csv(array_keys(fields($R)));}else{if($mf==2){$l=array();foreach(fields($R)as$B=>$k)$l[]=idf_escape($B)." ".full_type_sql($k);$Mb="CREATE TABLE ".table($R)." (".implode(", ",$l).")";}else$Mb=create_sql($R,$_POST["auto_increment"],$ok);set_utf8mb4($Mb);if($ok&&$Mb){if(($ok=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$mf==1)echo"DROP ".($mf==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($mf==1)$Mb=remove_definer($Mb);echo"$Mb;\n\n";}}}function
dumpData($R,$ok,$H,array$N=array(),array$Z=array(),array$q=array(),array$D=array()){if($ok){$cg=(JUSH=="sqlite"?0:1048576);$l=array();$Ee=false;if($_POST["format"]=="sql"){if($ok=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$l=fields($R);if(JUSH=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Ee=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($R,($N?:array("*")),$Z,$q,$D,0));if($I){$Xe="";$Va="";$tf=array();$Wd=array();$qk="";$vd=($R!=''?'fetch_assoc':'fetch_row');$Lb=0;while($K=$I->$vd()){if(!$tf){$Vl=array();foreach($K
as$X){$k=$I->fetch_field();if(idx($l[$k->name],'generated')){$Wd[$k->name]=true;continue;}$tf[]=$k->name;$w=idf_escape($k->name);$Vl[]="$w = VALUES($w)";}$qk=($ok=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Vl):"").";\n";}if($_POST["format"]!="sql"){if($ok=="table"){dump_csv($tf);$ok="INSERT";}dump_csv($K);}else{if(!$Xe)$Xe="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$tf)).") VALUES";foreach($K
as$w=>$X){if($Wd[$w]){unset($K[$w]);continue;}$k=$l[$w];$K[$w]=($X===null?"NULL":($X===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($X)?$X:(!is_blob($k)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$pj=($cg?"\n":" ")."(".implode(",\t",$K).")";if(!$Va)$Va=$Xe.$pj;elseif(JUSH=='mssql'?$Lb%1000!=0:strlen($Va)+4+strlen($pj)+strlen($qk)<$cg)$Va
.=",$pj";else{echo$Va.$qk;$Va=$Xe.$pj;}}$Lb++;}if($Va)echo$Va.$qk;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($Ee)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($De){return
friendly_url($De!=""?$De:(SERVER?:"localhost"));}function
dumpHeaders($De,$Eg=false){$Jh=$_POST["output"];$nd=(preg_match('~sql~',$_POST["format"])?"sql":($Eg?"tar":"csv"));header("Content-Type: ".($Jh=="gz"?"application/x-gzip":($nd=="tar"?"application/x-tar":($nd=="sql"||$Jh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Jh=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$nd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(67)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(68):lang(69))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(70)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(71)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(72)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(73)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(74)."</a>\n":"");return
true;}function
navigation($zg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Tg=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Tg)<0?h($Tg):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($zg=="auth"){$Jh="";foreach((array)$_SESSION["pwds"]as$Xl=>$Kj){foreach($Kj
as$O=>$Ql){$B=h(get_setting("vendor-$Xl-$O")?:get_driver($Xl));foreach($Ql
as$V=>$F){if($B&&$F!==null){$dc=$_SESSION["db"][$Xl][$O][$V];foreach(($dc?array_keys($dc):array(""))as$h)$Jh
.="<li><a href='".h(auth_url($Xl,$O,$V,$h))."'>($B) ".h("$V@").($O!=""?adminer()->serverName($O):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($Jh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Jh</ul>\n";}else{$T=array();if($_GET["ns"]!==""&&!$zg&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($zg);$ia=array();if(DB==""||!$zg){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(64)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(75)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(76)."</a>";}$Ke=$_GET["ns"]!==""&&!$zg&&DB!="";if($Ke&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(77)."</a>";$ia=adminer()->menuActions($ia,$zg);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Ke){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(13)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.1.1+ba55ceef",true);$Bg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Bg?script("addEventListener('DOMContentLoaded', () => {\n$Bg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$Of=array();foreach($T
as$R=>$U)$Of[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Of).')(?!\$)\b/g',false);$ek=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($ek))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$ik=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Ga=Driver::jushAutocomplete($T,$ik);echo($Ga?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ga; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($zg){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$ac=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(35)."'>".lang(78).": ".($g?html_select("db",array(""=>"")+group_system($g),DB,$ac):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(25)."'".($g?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$zg){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$jk){$R="$R";$B=adminer()->tableName($jk);if($B!=""&&!$jk["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".lang(41)."'>".lang(79)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($jk)?"view":"structure"))." title='".lang(42)."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($mi){$Ec=SqlDriver::$drivers;$te=" href='https://www.adminer.org/plugins/#use'".target_blank();if($mi===null){$mi=array();$Oa="adminer-plugins";if(is_dir($Oa)){foreach(glob("$Oa/*.php")as$m){$_d=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$_d)as$s=>$B)$this->driverFiles[$s]=$m;}}if(file_exists("$Oa.php")){$Me=$this->includeOnce("$Oa.php");if(is_array($Me)){foreach($Me
as$w=>$ji)$mi[is_object($ji)?get_class($ji):$w]=$ji;}else$this->error
.=lang(80,"<b>$Oa.php</b>",$te)."<br>";}foreach(get_declared_classes()as$nb){if(!$mi[$nb]&&(preg_match('~^Adminer\w~i',$nb)||is_subclass_of($nb,'Adminer\Plugin'))){$Ri=new
\ReflectionClass($nb);$Db=$Ri->getConstructor();if($Db&&$Db->getNumberOfRequiredParameters())$this->error
.=lang(81,$te,"<b>$nb</b>","<b>$Oa.php</b>")."<br>";else$mi[$nb]=new$nb;}}}$cf=array_filter($mi,function($ji){return!is_object($ji);});if($cf){$this->error
.=lang(82,$te)."<br>";$mi=array_diff_key($mi,$cf);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Ec);$this->plugins=$mi;$ka=new
Adminer;$mi[]=$ka;$Ri=new
\ReflectionObject($ka);foreach($Ri->getMethods()as$wg){foreach($mi
as$ji){$B=$wg->getName();if(method_exists($ji,$B))$this->hooks[$B][]=$ji;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$zd=str_replace("\r","",file_get_contents($m));$zd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$zd);return
dechex(crc32($zd));}function
checksums(){$Ad=array_values($this->driverFiles);foreach($this->plugins
as$ji){$Ri=new
\ReflectionObject($ji);$Ad[]=$Ri->getFileName();}$J=array();foreach($Ad
as$m)$J[basename($m,'.php')]=self::checksum($m);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'e65981f5','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','name-patterns'=>'84c10d09','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-foreign'=>'fe3e58c8','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'92ca960d','elastic'=>'1582a04d','firebird'=>'1cccfc19','igdb'=>'4063cc0b','imap'=>'3da1022b','mongo'=>'63486492','redis'=>'79824392','simpledb'=>'b8e2cc7d',);}function
__call($B,array$Oh){$za=array();foreach($Oh
as$w=>$X)$za[]=&$Oh[$w];$J=null;foreach($this->hooks[$B]as$ji){$Y=call_user_func_array(array($ji,$B),$za);if($Y!==null){if(!self::$append[$B])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$Zg=null){$za=func_get_args();$za[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$za);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($ai){$this->password_hash=$ai;}function
description(){return
lang(83);}function
credentials(){$F=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F));}function
login($Sf,$F){if($this->passwordMatches($F))return
true;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$O,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);$ni=$O["port"];$Rc=("$O[host]$ni$O[socket]"=="");$fk=adminer()->connectSsl();$Nl=($fk&&($fk['key']||$fk['cert']||$fk['ca']||isset($fk['verify'])));if($Nl)$this->ssl_set($fk['key'],$fk['cert'],$fk['ca'],'','');$J=@$this->real_connect((!$Rc?$O["host"]:ini_get("mysqli.default_host")),(!$Rc||$V!=""?$V:ini_get("mysqli.default_user")),(!$Rc||$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,($ni!=""?intval($ni):ini_get("mysqli.default_port")),($ni!=""?null:$O["socket"]),($Nl?($fk['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($db){if(parent::set_charset($db))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $db");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}function
inTransaction(){return
false;}function
begin(){return$this->begin_transaction();}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$O,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
lang(84,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$ni="$O[port]$O[socket]";$B=$O["host"].($ni!=""?":$ni":"");$this->link=@mysql_connect(($B!=""?$B:ini_get("mysql.default_host")),($B.$V!=""?$V:ini_get("mysql.default_user")),($B.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($db){return
mysql_set_charset($db,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Zb){return
mysql_select_db($Zb,$this->link);}function
query($H,$vl=false){$I=@($vl?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($I===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($I);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($I){$this->result=$I;$this->num_rows=mysql_num_rows($I);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->native_type=idx(array("string"=>"varchar","real"=>"double"),$J->type,$J->type);return$J;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$O,$V,$F){$C=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$C[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$fk=adminer()->connectSsl();if($fk){if($fk['key'])$C[\PDO::MYSQL_ATTR_SSL_KEY]=$fk['key'];if($fk['cert'])$C[\PDO::MYSQL_ATTR_SSL_CERT]=$fk['cert'];if($fk['ca'])$C[\PDO::MYSQL_ATTR_SSL_CA]=$fk['ca'];if(isset($fk['verify']))$C[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$fk['verify'];}$_e=$O["host"];$ni=$O["port"];$Tj=$O["socket"];return$this->dsn("mysql:charset=utf8".($_e!=""?";host=$_e":'').($ni!=""?";port=$ni":($Tj!=""?";unix_socket=$Tj":"")),$V,$F,$C);}function
set_charset($db){return$this->query("SET NAMES $db");}function
select_db($Zb){return$this->query("USE ".idf_escape($Zb));}function
query($H,$vl=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$vl);return
parent::query($H,$vl);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($xk){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($O,$V,$F){$e=parent::connect($O,$V,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($pj=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$pj;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array(lang(85)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(86)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(87)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(88)=>array("enum"=>65535,"set"=>64),lang(89)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(90)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types[lang(87)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(87)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(91)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(91)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(85)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$P){return($P?parent::insert($R,$P):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$zi){$d=array_keys(reset($L));$vi="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$Vl=array();foreach($d
as$w)$Vl[$w]="$w = VALUES($w)";$qk="\nON DUPLICATE KEY UPDATE ".implode(", ",$Vl);$Vl=array();$x=0;foreach($L
as$P){$Y="(".implode(", ",$P).")";if($Vl&&(strlen($vi)+$x+strlen($Y)+strlen($qk)>1e6)){if(!queries($vi.implode(",\n",$Vl).$qk))return
false;$Vl=array();$x=0;}$Vl[]=$Y;$x+=strlen($Y)+2;}return
queries($vi.implode(",\n",$Vl).$qk);}function
slowQuery($H,$Vk){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Vk FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($Vk*1000).") */ $A[2]";}}function
convertColumn($t,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($t)";if($k["type"]=="bit")return"BIN($t + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($t)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($t)";return"";}function
convertSearch($t,array$X,array$k){return($this->convertColumn($t,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t));}function
typeName(\stdClass$k){$B=parent::typeName($k);if($B!=""){$ul=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($ul,$B,strtolower($B));}$ul=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$J=idx($ul,$k->type,"");return($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$J):$J);}function
quoteBinary($pj){return"X".q(bin2hex($pj));}function
md5($c,array$k){if(is_blob($k)||preg_match('~'.text_type().'~',$k["type"]))return"MD5(".(is_blob($k)||preg_match("~^utf8~",$k["collation"])?$c:"CONVERT($c USING ".charset($this->conn).")").")";}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($B,$mf=false){$Uf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Uf?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="sys")return($Uf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$B)).".html"));if(DB=="mysql")return($Uf?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($R){$Qd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Qd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$K=($I?$I->fetch_row():null);if(!$K)return
array();$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$K;$Wh=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Qd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($Wh);$J["partition_values"]=array_values($Wh);return$J;}function
checkConstraints($R){$J=parent::checkConstraints($R);return($this->conn->flavor=='maria'?$J:array_map('stripslashes',$J));}function
hasCStyleEscapes(){static$Wa;if($Wa===null){$dk=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Wa=(strpos($dk,'NO_BACKSLASH_ESCAPES')===false);}return$Wa;}function
hasEstimatedRows(){return
true;}function
isSystem($h,$M=""){return
information_schema($h,$M)||in_array($h,array("mysql","sys"));}function
lineComment(){return"#|-- ";}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$xk){return(preg_match('~^(MEMORY|NDB)$~',$xk["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($Hd){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$gk=microtime(true);$J=($Hd?slow_query($H):get_vals($H));if(microtime(true)-$gk>0.1){restart_session();set_session("dbs",$J);stop_session();}}return$J;}function
limit($H,$Z,$y,$gh=0,$Dj=" "){return" $H$Z".($y?$Dj."LIMIT $y".($gh?" OFFSET $gh":""):"");}function
limit1($R,$H,$Z,$Dj="\n"){return
limit($H,$Z,1,0,$Dj);}function
db_collation($h,array$sb){$J=null;$Mb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$Mb,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Mb,$A))$J=$sb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$g){$J=array();foreach($g
as$h)$J[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$J;}function
table_status($B="",$td=false){$J=array();$H="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name");$M=array();foreach(($td?array():get_rows($H))as$K)$M[$K["Name"]]=$K;$yi=null;foreach(get_rows($td?$H:"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$K){$Gh=idx($M,$K["Name"]);if($Gh){if($K["Comment"]!==$Gh["Comment"]&&$K["Comment"]!==$yi)$K["Error"]=$K["Comment"];$yi=$K["Comment"];$K["Comment"]=$Gh["Comment"];$K["Engine"]=$Gh["Engine"];}if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($B!="")$K["Name"]=$B;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($Sd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Sd,$A);return
array($A[1],$A[2],ltrim($A[3].$A[4]));}function
fields($R){$Uf=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$k=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$Xd=$K["GENERATION_EXPRESSION"];$qd=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$qd,$Wd);list($tl,$x,$Bl)=parse_type($U);$i=$K["COLUMN_DEFAULT"];if($i!=""){$lf=preg_match('~text|json~',$tl);if(!$Uf&&$lf)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($Uf||$lf){$i=($i=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$i));}if(!$Uf&&preg_match('~binary~',$tl)&&preg_match('~^0x(\w*)$~',$i,$A))$i=pack("H*",$A[1]);}$J[$k]=array("field"=>$k,"full_type"=>$U,"type"=>$tl,"length"=>$x,"unsigned"=>$Bl,"default"=>($Wd?($Uf?$Xd:stripslashes($Xd)):$i),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($qd=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$qd,$A)?$A[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($Wd[1]=="PERSISTENT"?"STORED":$Wd[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$B=$K["Key_name"];$J[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$B]["columns"][]=$K["Column_name"];$J[$B]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$B]["descs"][]=null;$J[$B]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$ei='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Nb=get_val("SHOW CREATE TABLE ".table($R),1);if($Nb){preg_match_all("~CONSTRAINT ($ei) FOREIGN KEY ?\\(((?:$ei,? ?)+)\\) REFERENCES ($ei)(?:\\.($ei))? \\(((?:$ei,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Nb,$Wf,PREG_SET_ORDER);foreach($Wf
as$A){preg_match_all("~$ei~",$A[2],$Xj);preg_match_all("~$ei~",$A[5],$Lk);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$Xj[0]),"target"=>array_map('Adminer\idf_unescape',$Lk[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$w=>$X)sort($J[$w]);return$J;}function
information_schema($h,$M=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$rb){return
queries("CREATE DATABASE ".idf_escape($h).($rb?" COLLATE ".q($rb):""));}function
drop_databases(array$g){$J=apply_queries("DROP DATABASE",$g,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($B,$rb){$J=false;if(create_database($B,$rb)){$T=array();$am=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$am[]=$R;else$T[]=$R;}$J=(!$T&&!$am)||move_tables($T,$am,$B);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Fa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$Fa="";break;}if($u["type"]=="PRIMARY")$Fa=" UNIQUE";}}return" AUTO_INCREMENT$Fa";}function
alter_table($R,$B,array$l,array$Jd,$wb,$Tc,$rb,$Ea,$Vh){$ua=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$ua[]=($R!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($R!=""?$k[2]:"");}else$ua[]="DROP ".idf_escape($k[0]);}$ua=array_merge($ua,$Jd);$jk=($wb!==null?" COMMENT=".q($wb):"").($Tc?" ENGINE=".q($Tc):"").($rb?" COLLATE ".q($rb):"").($Ea!=""?" AUTO_INCREMENT=$Ea":"");if($Vh){$Wh=array();if($Vh["partition_by"]=='RANGE'||$Vh["partition_by"]=='LIST'){foreach($Vh["partition_names"]as$w=>$X){$Y=$Vh["partition_values"][$w];$Wh[]="\n  PARTITION ".idf_escape($X)." VALUES ".($Vh["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$jk
.="\nPARTITION BY $Vh[partition_by]($Vh[partition])";if($Wh)$jk
.=" (".implode(",",$Wh)."\n)";elseif($Vh["partitions"])$jk
.=" PARTITIONS ".(+$Vh["partitions"]);}elseif($Vh===null)$jk
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$ua)."\n)$jk");if($R!=$B)$ua[]="RENAME TO ".table($B);if($jk)$ua[]=ltrim($jk);return($ua?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$ua)):true);}function
alter_indexes($R,$ua){$bb=array();foreach($ua
as$X)$bb[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$bb));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$am){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$am)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$am,$Lk){$Wi=array();foreach($T
as$R)$Wi[]=table($R)." TO ".idf_escape($Lk).".".table($R);if(!$Wi||queries("RENAME TABLE ".implode(", ",$Wi))){$kc=array();foreach($am
as$R)$kc[table($R)]=view($R);connection()->select_db($Lk);$h=idf_escape(DB);foreach($kc
as$B=>$Zl){if(!queries("CREATE VIEW $B AS ".str_replace(" $h."," ",$Zl["select"]))||!queries("DROP VIEW $h.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$am,$Lk){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$B=($Lk==DB?table("copy_$R"):idf_escape($Lk).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($R))||!queries("INSERT INTO $B SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$ll=$K["Trigger"];list($cd,$bh)=trigger_event($K);if(!queries("CREATE TRIGGER ".($Lk==DB?idf_escape("copy_$ll"):idf_escape($Lk).".".idf_escape($ll))." $K[Timing] $cd".($bh!=""?" $bh":"")." ON $B FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($am
as$R){$B=($Lk==DB?table("copy_$R"):idf_escape($Lk).".".table($R));$Zl=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $Zl[select]"))return
false;}return
true;}function
trigger_event(array$K){$ed=explode(",",$K["Event"]);$J=array();foreach(array("DELETE","INSERT","UPDATE")as$cd){if(in_array($cd,$ed))$J[]=$cd;}$J=implode(" OR ",$J);if(in_array("UPDATE",$ed)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($K["Trigger"]),2),$A)&&preg_match('~\bOF\s+(.+)~is',$A[1],$bh))return
array("$J OF",$bh[1]);return
array($J,"");}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));$J=reset($L);if($J)list($J["Event"],$J["Of"])=trigger_event($J);return($J?:array());}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){list($cd)=trigger_event($K);$J[$K["Trigger"]]=array($K["Timing"],$cd);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$U){$L=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($B)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($L
as$K){$Sd=$K["DTD_IDENTIFIER"];list($tl,$x,$Bl)=parse_type($Sd);$l[]=array("field"=>$K["PARAMETER_NAME"],"type"=>$tl,"length"=>$x,"unsigned"=>$Bl,"null"=>true,"full_type"=>$Sd,"inout"=>($U=="FUNCTION"?"":$K["PARAMETER_MODE"]),"collation"=>$K["COLLATION_NAME"],);}$J=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($B))->fetch_assoc();if(!$J)return
array();$J['options']=array("DEFINER"=>$J['definer'],"DETERMINISTIC"=>$J['is_deterministic'],"SQL_DATA_ACCESS"=>$J['data_access'],"SQL_SECURITY"=>$J['security'],"COMMENT"=>$J['comment'],);if($l&&$l[0]['field']=='')$J['returns']=array_shift($l);$J['fields']=$l;return$J;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($hj){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($B,array$K){return
idf_escape($B);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Ea,$ok){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$Ea)$J=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Zb,$ok=""){$B=idf_escape($Zb);$J="";if(preg_match('~CREATE~',$ok)&&($Mb=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($Mb);if($ok=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
.="$Mb;\n";}return$J."USE $B";}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K){list($K["Event"],$K["Of"])=trigger_event($K);$J
.="\n".create_trigger(" ON ".table($K["Table"]),$K+array("Type"=>"FOR EACH ROW")).";\n";}return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$k){return
driver()->convertColumn(idf_escape($k["field"]),$k);}function
unconvert_field(array$k,$J){if(preg_match("~binary~",$k["type"]))$J="UNHEX($J)";if($k["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($k["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$vi=(min_version(8)?"ST_":"");$J=$vi."GeomFromText($J, $vi"."SRID($k[field]))";}return$J;}function
support($ud){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$ud);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($pd=false){return
array();}function
type_values($s){return"";}function
type_definition($s){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($M,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($Xk,$j="",$Ua=array(),$Yk="",$Wg=false,$Ac=""){if($Wg){header("HTTP/1.1 404 Not Found");$j=($j?:lang(92));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Zk=$Xk.($Yk!=""?": $Yk":"");$al=strip_tags($Zk.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(93),'\' class=\'',lang(93),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$al,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.1+ba55ceef"),'">
';$Rb=adminer()->css();if(is_int(key($Rb)))$Rb=array_fill_keys($Rb,'light');$le=in_array('light',$Rb)||in_array('',$Rb);$je=in_array('dark',$Rb)||in_array('',$Rb);$Vb=($le?($je?null:false):($je?:null));$lg=" media='(prefers-color-scheme: dark)'";if($Vb!==false)echo"<link rel='stylesheet'".($Vb?"":$lg)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.1+ba55ceef")."'>\n";echo"<meta name='color-scheme' content='".($Vb===null?"light dark":($Vb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.1+ba55ceef");if(adminer()->head($Vb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.1+ba55ceef")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Rb
as$Il=>$_g){$b=($_g=='dark'&&!$Vb?$lg:($_g=='light'&&$je?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Il)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(94))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(95)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Ua!==null){$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$O=adminer()->serverName(SERVER);$O=($O!=""?$O:lang(31));if($Ua===false)echo"$O\n";else{echo"<a href='".h($z.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$O</a> » ";$xj="";if(is_string($Ua)){$xj=$Ua;$Ua=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Ua))){$bc="$z&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($bc.($_GET["ns"]==""?$xj:"")).'">'.h(DB).'</a> » ';}if(is_array($Ua)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$xj).'">'.h($_GET["ns"]).'</a> » ';foreach($Ua
as$w=>$X){$mc=(is_array($X)?$X[1]:h($X));if($mc!="")echo"<a href='".h(ME."$w=").url_escape(is_array($X)?$X[0]:$X)."'>$mc</a> » ";}}echo"$Xk\n";}}echo"<h2>$Zk$Ac</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Wg){page_footer($Wg===true?"":$Wg);exit;}}function
service_worker(){$Ui=has_passwords();$ob=($Ui?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.1+ba55ceef")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$ob\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$Kj){foreach($Kj
as$Ql){foreach($Ql
as$F){if($F!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Qb){$pe=array();foreach($Qb
as$w=>$X)$pe[]="$w $X";header("Content-Security-Policy: ".implode("; ",$pe));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Ol=array();foreach(array_keys(adminer()->css())as$Il)$Ol[preg_replace('~\?.*~','',$Il)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($Ol[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$A);$J[$m]=array((string)$A[1],Plugins::checksum($m));}}return$J;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'216f097b','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Vg;if(!$Vg)$Vg=base64_encode(rand_string());return$Vg;}function
page_messages($j){$Hl=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$sg=idx($_SESSION["messages"],$Hl);if($sg){echo"<div class='message'>".implode("</div>\n<div class='message'>",$sg)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Hl]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($zg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($zg);echo"</div>\n";if($zg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(33),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(96),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Gg){while($Gg>=2147483648)$Gg-=4294967296;while($Gg<=-2147483649)$Gg+=4294967296;return(int)$Gg;}function
long2str(array$W,$cm){$pj='';foreach($W
as$X)$pj
.=pack('V',$X);if($cm)return
substr($pj,0,end($W));return$pj;}function
str2long($pj,$cm){$W=array_values(unpack('V*',str_pad($pj,4*ceil(strlen($pj)/4),"\0")));if($cm)$W[]=strlen($pj);return$W;}function
xxtea_mx($mm,$lm,$rk,$rf){return
int32((($mm>>5&0x7FFFFFF)^$lm<<2)+(($lm>>3&0x1FFFFFFF)^$mm<<4))^int32(($rk^$lm)+($rf^$mm));}function
encrypt_string($lk,$w){if($lk=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($lk,true);$Gg=count($W)-1;$mm=$W[$Gg];$lm=$W[0];$Gi=floor(6+52/($Gg+1));$rk=0;while($Gi-->0){$rk=int32($rk+0x9E3779B9);$Jc=$rk>>2&3;for($Kh=0;$Kh<$Gg;$Kh++){$lm=$W[$Kh+1];$Fg=xxtea_mx($mm,$lm,$rk,$w[$Kh&3^$Jc]);$mm=int32($W[$Kh]+$Fg);$W[$Kh]=$mm;}$lm=$W[0];$Fg=xxtea_mx($mm,$lm,$rk,$w[$Kh&3^$Jc]);$mm=int32($W[$Gg]+$Fg);$W[$Gg]=$mm;}return
long2str($W,false);}function
decrypt_string($lk,$w){if($lk=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($lk,false);$Gg=count($W)-1;$mm=$W[$Gg];$lm=$W[0];$Gi=floor(6+52/($Gg+1));$rk=int32($Gi*0x9E3779B9);while($rk){$Jc=$rk>>2&3;for($Kh=$Gg;$Kh>0;$Kh--){$mm=$W[$Kh-1];$Fg=xxtea_mx($mm,$lm,$rk,$w[$Kh&3^$Jc]);$lm=int32($W[$Kh]-$Fg);$W[$Kh]=$lm;}$mm=$W[$Gg];$Fg=xxtea_mx($mm,$lm,$rk,$w[$Kh&3^$Jc]);$lm=int32($W[0]-$Fg);$W[0]=$lm;$rk=int32($rk-0x9E3779B9);}return
long2str($W,true);}$hi=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$hi[$w]=$X;}}function
add_invalid_login(){$Ma=get_temp_dir()."/adminer-invalid";foreach(glob("$Ma*")?:array($Ma)as$m){$o=file_open_lock($m);if($o)break;}if(!$o)$o=file_open_lock("$Ma-".rand_string());if(!$o)return;$ef=json_decode(stream_get_contents($o),true);$Uk=time();if($ef){foreach($ef
as$ff=>$X){if($X[0]<$Uk)unset($ef[$ff]);}}$cf=&$ef[adminer()->bruteForceKey()];if(!$cf)$cf=array($Uk+30*60,0);$cf[1]++;file_write_unlock($o,json_encode($ef));}function
check_invalid_login(array&$hi){$ef=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$o=file_open_lock($m);if($o){$ef=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$w=adminer()->bruteForceKey();$cf=idx($ef,$w,array());$Ug=($cf[1]>29?$cf[0]-time():0);if($Ug>0){$j=lang(97,ceil($Ug/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(98,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$hi,false);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Pb=adminer()->credentials();$J=!is_object(Driver::connect($Pb[0],$Pb[1],""));if($J)set_session("password_required",true);}}return$J;}function
require_password_link($F){$Cg="<a href='https://www.adminer.org/password/'".target_blank().">".lang(99)."</a>";if(!function_exists('password_hash'))return" $Cg";$ki=($F!==null?$F:base64_encode(substr(pack("H*",rand_string()),0,12)));$oe=password_hash($ki,PASSWORD_DEFAULT);$m="adminer-plugins.php";$jd=file_exists("adminer-plugins.php");if($jd)$af=($F!==null?lang(100,"<b>$m</b>"):lang(101,"<b>$m</b>","<b>$ki</b>"));else{$m="<button name='password_less' value='".h($oe)."' class='link'>$m</button>";$af=($F!==null?lang(102,$m):lang(103,$m,"<b>$ki</b>"));}$Mf="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($oe)."'</span>),";$J="<p>$af
<pre><code class='jush'>".($jd?$Mf:"&lt;?php\n<a>return</a> <a>array</a>(\n$Mf\n);")."</code></pre>
<p>$Cg
";return" <a href='#password-less' class='toggle'>".lang(104)."</a>
<div id='password-less' class='hidden'>".($jd?$J:"<form action='' method='post'>\n".$J.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Da=$_POST["auth"];if($Da&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$Xl=$Da["driver"];$O=$Da["server"];$V=$Da["username"];$F=(string)$Da["password"];$h=$Da["db"];set_password($Xl,$O,$V,$F);$_SESSION["db"][$Xl][$O][$V][$h]=true;if($Da["permanent"]){$w=implode("-",array_map('base64_encode',array($Xl,$O,$V,$h)));$Ai=adminer()->permanentLogin(true);$hi[$w]="$w:".base64_encode($Ai?encrypt_string($F,$Ai):"");cookie("adminer_permanent",implode(" ",$hi));}if(!array_diff(array_keys($_POST),array("auth","token"))||$Xl!=DRIVER||$O!=SERVER||$V!==$_GET["username"]||$h!=DB)redirect(auth_url($Xl,$O,$V,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($hi);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(105).' '.lang(106));}elseif($hi&&!$_SESSION["pwds"]){session_regenerate_id();$Ai=adminer()->permanentLogin();foreach($hi
as$w=>$X){list(,$mb)=explode(":",$X);list($Xl,$O,$V,$h)=array_map('base64_decode',explode("-",$w));set_password($Xl,$O,$V,decrypt_string(base64_decode($mb),$Ai));$_SESSION["db"][$Xl][$O][$V][$h]=true;}}function
unset_permanent(array&$hi){foreach($hi
as$w=>$X){list($Xl,$O,$V,$h)=array_map('base64_decode',explode("-",$w));if($Xl==DRIVER&&$O==SERVER&&$V==$_GET["username"]&&$h==DB)unset($hi[$w]);}cookie("adminer_permanent",implode(" ",$hi));}function
auth_error($j,array&$hi,$df=true){$Lj=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Lj]||$_GET[$Lj])&&!$_SESSION["token"])$j=lang(107);elseif($df&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$j
.=($j?'<br>':'').lang(108,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($hi);}}if(!$_COOKIE[$Lj]&&$_GET[$Lj]&&ini_bool("session.use_only_cookies"))$j=lang(109);$Oh=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Oh["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(36),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(110)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($hi);page_header(lang(111),lang(112,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($hi);$Pb=adminer()->credentials();$e=Driver::connect($Pb[0],$Pb[1],$Pb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Sf=null;if(!is_object($e)||($Sf=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($e)?nl_br(h($e)):(is_string($Sf)?$Sf:lang(113))).(preg_match('~^ | $~',get_password())?'<br>'.lang(114):'');auth_error($j,$hi);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(96),lang(115));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Da&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(115).' '.lang(116);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(117,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(118);}function
print_select_result($I,$f=null,array$Ah=array(),&$y=0,&$Kc=false){$Of=array();$v=array();$d=array();$T=array();$zi=array();$Mc=array();$ul=array();$J=array();$Ag=$Kc;$Kc=false;for($r=0;(!$y||$r<$y)&&($K=$I->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($Ag?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($of=0;$of<count($K);$of++){$k=$I->fetch_field();$B=$k->name;$R=(isset($k->table)?$k->table:"");$_h=(isset($k->orgtable)?$k->orgtable:"");$zh=(isset($k->orgname)?$k->orgname:$B);$tl=driver()->typeName($k);if($Ah&&JUSH=="sql")$Of[$of]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($_h!=""){$qa=($R!=""?$R:$_h);if($R!="")$J[$R]=$_h;if(!isset($v[$qa])){if(!isset($zi[$_h])){$zi[$_h]=array();foreach(indexes($_h,$f)as$u){if($u["type"]=="PRIMARY"){$zi[$_h]=array_flip($u["columns"]);break;}}}$T[$qa]=$_h;$v[$qa]=$zi[$_h];$d[$qa]=$zi[$_h];}if(isset($d[$qa][$zh])){unset($d[$qa][$zh]);$v[$qa][$zh]=$of;$Of[$of]=$qa;}elseif($Ag&&isset($k->orgname)&&$k->db==DB&&!is_blob(array("type"=>$tl)))$Mc[$of]=array($qa,$zh,preg_match('~text|json|lob~',$tl));}$ul[$of]=$tl;echo"<th title='".h(trim(($_h!=""?"$_h.$zh":($k->name!=$zh?$zh:""))." ".$tl))."'>".h($B).($Ah?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}foreach($Mc
as$of=>$Za){if($d[$Za[0]])unset($Mc[$of]);}echo"<tbody>\n";}$Fe=array();foreach($v
as$qa=>$u){if($u&&!$d[$qa]){$t="";foreach($u
as$pb=>$of){if($K[$of]===null){$t=null;break;}$t
.="&where[".url_escape(bracket_escape($pb))."]=".url_escape($K[$of]);}$Fe[$qa]=$t;}}echo"<tr>";foreach($K
as$w=>$X){$z="";if(isset($Of[$w])){if($Ah&&JUSH=="sql"){$R=$K[array_search("table=",$Of)];$z=ME.$Of[$w].url_escape($Ah[$R]!=""?$Ah[$R]:$R);}elseif(idx($Fe,$Of[$w])!==null)$z=ME."edit=".url_escape($T[$Of[$w]]).$Fe[$Of[$w]];}$b="";$Za=idx($Mc,$w);if($Za&&idx($Fe,$Za[0])!==null&&is_utf8($X)){$Kc=true;$b=" data-name='".h("val[".bracket_escape($T[$Za[0]])."][".bracket_escape(substr($Fe[$Za[0]],1))."][".bracket_escape($Za[1])."]")."' data-text='".($Za[2]?1:0)."'";}$X=select_value($X,$z,array('type'=>(preg_match('~binary~',$ul[$w])?'blob':$ul[$w])),null);echo"<td".(preg_match(number_type(),$ul[$w])?" class='number'":"")."$b>$X";}}$y=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(16))."\n";return$J;}function
textarea($B,$Y,$L=10,$tb=80,$qf=JUSH){echo"<textarea name='".h($B)."' rows='$L' cols='$tb' class='sqlarea jush-".h($qf)."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$C,$Y="",$ii=""){if($C&&$Y!=""&&!isset($C[$Y]))$C=array($Y=>$Y)+$C;$Kk=($C?"select":"input");return"<$Kk$b".($C?"><option value=''>$ii".optionlist($C,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$ii'>");}function
json_row($w,$X=null,$bd=true){static$Ed=true;if($Ed)echo"{";if($w!=""){echo($Ed?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($bd?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Ed=false;}else{echo"\n}\n";$Ed=true;}}function
flat_collations(){$sb=collations();return(is_array(reset($sb))?call_user_func_array('array_merge',array_values($sb)):$sb);}function
edit_type($w,array$k,array$sb,array$Ld=array(),array$rd=array()){$U=(string)$k["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($Ld[$U])&&!in_array($U,$rd))$rd[]=$U;$mk=driver()->structuredTypes();if($Ld)$mk[lang(119)]=$Ld;echo
optionlist(array_merge($rd,$mk),$U),"</select><td>","<input name='".h($w)."[length]' value='".h($k["length"])."' size='3'".(!$k["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($sb?"<input list='collations' name='".h($w)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($k["collation"])."' placeholder='(".lang(120).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$k["unsigned"]).'</select>':''),(isset($k['on_update'])?"<select name='".h($w)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(121).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($Ld?"<select name='".h($w)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(122).")".optionlist(explode("|",driver()->onActions),$k["on_delete"])."</select> ":" ");}function
option_types($U,$ul){return" data-types='".h($ul)."'".(preg_match("~$ul~",$U)?"":" class='hidden'");}function
process_length($x){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$x))return"(max)";$Wc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Wc(?:\\s*,\\s*$Wc)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$Wc~",$x,$Wf)?"(".implode(",",$Wf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_in($X){$Wc=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$Wc(?:\\s*,\\s*$Wc)*+\\s*\\)?\\s*\$~",$X)&&preg_match_all("~$Wc~",$X,$Wf))return"(".implode(", ",$Wf[0]).")";$J=array();foreach(explode(",",$X)as$nf)$J[]=q(trim($nf));return"(".implode(", ",$J).")";}function
process_type(array$k,$qb="COLLATE"){return" ".(is_user_type($k["type"])?idf_escape($k["type"]):$k["type"]).process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],driver()->unsigned)?" $k[unsigned]":"").(preg_match('~'.text_type().'~',$k["type"])&&$k["collation"]?" $qb ".(JUSH=="mssql"?$k["collation"]:q($k["collation"])):"");}function
process_field(array$k,array$rl){if($k["on_update"])$k["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$k["on_update"]);return
array(idf_escape(trim($k["field"])),process_type($rl),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE $k[on_update]":""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),);}function
default_value(array$k){if($k["default"]===null)return"";$i=str_replace("\r","",$k["default"]);$Wd=$k["generated"];$Q=!preg_match('~]$~',$k["length"])&&(preg_match('~char|binary|text|json|enum|set|String~',$k["type"])||driver()->enumLength($k));return(in_array($Wd,driver()->generated)?(JUSH=="mssql"?" AS ($i)".($Wd=="VIRTUAL"?"":" $Wd"):" GENERATED ALWAYS AS ($i) $Wd"):(preg_match('~^GENERATED ~i',$i)?" $i":" DEFAULT ".($Q||preg_match('~^(?![a-z])~i',$i)?(JUSH=="sql"&&preg_match('~text|json~',$k["type"])?"(".q($i).")":q($i)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($i)":$i)))));}function
edit_fields(array$l,array$sb,$U="TABLE",array$Ld=array()){$l=array_values($l);$gc=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$xb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(123):lang(124)),"<th id='label-type'>".lang(49)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(125),"<th>".lang(126);if($U=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(51)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<th id='label-default'$gc>".lang(52),(support("comment")?"<th id='label-comment'$xb>".lang(50):"");$Bf=!support("move_col");echo"<td>".icon("plus","add[".($Bf?count($l):0)."]","+",lang(127),($Bf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($l
as$r=>$k){$r++;$Bh=$k[($_POST?"orig":"field")];$tc=(isset($_POST["add"][$r-1])||(isset($k["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$Bh=="");echo"<tr".($tc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$k["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(128))." ":"");if($tc)echo"<input name='fields[$r][field]' value='".h($k["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$Bh);edit_type("fields[$r]",$k,$sb,$Ld);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$k["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($k["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$gc>".(driver()->generated?html_select("fields[$r][generated]",array_merge(array("","DEFAULT"),driver()->generated),$k["generated"])." ":checkbox("fields[$r][generated]",1,$k["generated"],"","","","label-default"));$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($k["default"]);echo(preg_match('~\n~',$k["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$xb>".adminer()->commentInput('COLUMN',$b,$k["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(127))." ":""),($Bh==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(129)):"");}}function
process_fields(array&$l){if($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Fc,$Mb,$Gc,$Qk,$Hc,$_,$rg,$pg,$qg,$kh,$Qg){if($_POST["drop"])query_redirect($Fc,$_,$rg);elseif($kh=="")query_redirect($Mb,$_,$qg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($_,$pg,queries($Fc)&&queries($Mb)&&driver()->commit());driver()->rollback();}elseif($kh!=$Qg){$Ob=queries($Mb);queries_redirect($_,$pg,$Ob&&queries($Fc));if($Ob&&$Gc)queries($Gc);}else
queries_redirect($_,$pg,queries($Qk)&&queries($Hc)&&queries($Fc)&&queries($Mb));}function
create_trigger($nh,array$K){$Wk=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$nh.$Wk:$Wk.$nh).preg_replace('~[\s;]+$~',''," $K[Type]\n$K[Statement]").";";}function
q_dollar($Q){$lc='$$';while(strpos($Q.$lc,$lc)!=strlen($Q))$lc='$_'.substr($lc,1);return$lc.$Q.$lc;}function
routine_collate($rb){static$eb=array();if($rb&&!$eb){foreach(collations()as$db=>$Ul){foreach((array)$Ul
as$X)$eb[$X]=$db;}}return($eb[$rb]?"CHARACTER SET ".q($eb[$rb])." ":"")."COLLATE";}function
create_routine($hj,array$K){$P=array();$l=$K["fields"];ksort($l);foreach($l
as$k){if($k["field"]!=""){$Ve=(preg_match("~^(".driver()->inout.")\$~",$k["inout"])?$k["inout"]:"");$P[]="\n  ".(JUSH=="mssql"?"@$k[field]".process_type($k).($Ve?" $Ve":""):($Ve?"$Ve ":"").idf_escape($k["field"]).process_type($k,routine_collate($k["collation"])));}}$ic="";$C=array();foreach(routine_options($hj)as$w=>$Vl){$Y=idx($K["options"],$w,"");if($w=="DEFINER")$ic=($Y?" $w=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$Vl){if($Y!="")$C[]="$w ".q($Y);}elseif($Y!=reset($Vl)&&in_array($Y,$Vl))$C[]=$Y;}$_f=$K["language"];$jc=preg_replace('~[\s;]+$~','',$K["definition"]);$Bc=(JUSH=="pgsql"||($_f&&$_f!="sql"));$Nh=($P?implode(",",$P)."\n":"");return"CREATE$ic $hj ".table(trim($K["name"])).(JUSH=="mssql"&&$hj=="PROCEDURE"?rtrim($Nh):" ($Nh)").($hj=="FUNCTION"?"\nRETURNS".process_type($K["returns"],routine_collate($K["returns"]["collation"])):"").($_f?" LANGUAGE $_f":"").($C?"\n".implode(" ",$C):"").($Bc?" AS ".q_dollar("\n".trim($jc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$jc;");}function
remove_definer($H){$ic=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($ic).')','\1',$H);}function
object_name($U,$R,array$d){return
str_replace(array("{table}","{columns}"),array($R,implode("_",$d)),adminer()->namePattern($U));}function
format_foreign_key(array$n,$B=""){$h=$n["db"];$Xg=$n["ns"];return($B!=""?" CONSTRAINT ".idf_escape($B):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Xg!=""&&$Xg!=$_GET["ns"]?idf_escape($Xg).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($m,$bl){$J=pack("a100a8a8a8a12a12",$m,644,0,0,decoct($bl->size),decoct(time()));$jb=8*32;for($r=0;$r<strlen($J);$r++)$jb+=ord($J[$r]);$J
.=sprintf("%06o",$jb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$bl->send();echo
str_repeat("\0",511-($bl->size+511)%512);}function
doc_version(){$Jj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$Jj,$A);return($A[1]>=18?$A[1]:"19");}$Ti=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$Yl=(preg_match($Ti,$Jj,$A)?$A[0]:"");if(JUSH=='mssql')return($Yl>=15?"sql-server-ver$Yl":($Yl==12?"azuresqldb-current":"sql-server-2017"));return$Yl;}function
doc_link(array$di,$Rk="📖"){$Yl=doc_version();$Jl=array('sql'=>"https://dev.mysql.com/doc/refman/$Yl/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Yl)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$Yl/",);if(connection()->flavor=='maria'){$Jl['sql']="https://mariadb.com/kb/en/";$di['sql']=($di['mariadb']?:str_replace(".html","/",$di['sql']));}if(connection()->flavor=='cockroach'&&$di['cockroach']){$Jl['pgsql']="https://docs.cockroachlabs.com/docs/v$Yl/";$di['pgsql']=$di['cockroach'];}return($di[JUSH]?" <a href='".h($Jl[JUSH].$di[JUSH].(JUSH=='mssql'?"?view=$Yl":""))."'".target_blank()." class='doc' title='".lang(130)."'>$Rk</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($Mb){static$P=false;if(!$P&&preg_match('~\butf8mb4~i',$Mb)){$P=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(35).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),lang(131),drop_databases($_POST["db"]));page_header(lang(132),$j,false);echo"<p class='links'>\n";foreach(array('database'=>lang(133),'privileges'=>lang(71),'processlist'=>lang(134),'variables'=>lang(135),'status'=>lang(136),)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".lang(137,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(138,"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();if($g){$tj=support("scheme");$sb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(35).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(139)."</a>":"")."<th>".lang(140)."<th>".lang(141)."<th>".lang(142)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(143)."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$T){$gj=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$s=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$gj' id='$s'>".h($h)."</a>";$rb=h(db_collation($h,$sb));echo"<td>".(support("database")?"<a href='$gj".($tj?"&amp;ns=":"")."&amp;database=' title='".lang(67)."'>$rb</a>":$rb),"<td align='right'><a href='$gj&amp;schema=' id='tables-".h($h)."' title='".lang(70)."'>".($_GET["dbsize"]?format_number($T):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(144)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(145)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ka=adminer();$mi=($ka
instanceof
Plugins?$ka->plugins:array());$Ec=($ka
instanceof
Plugins?$ka->drivers:array());$qc=design_checksums();if($mi||$Ec||$qc){$kb=($ka
instanceof
Plugins?$ka->checksums():array());$dh=Plugins::officialChecksums();$El=function($Il){return" (<a href='$Il'".target_blank()." class='update'>".VERSION."</a>)";};$li=function($zd)use($kb,$dh,$El){return($kb[$zd]&&$dh[$zd]&&$kb[$zd]!==$dh[$zd]?$El("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(146)."</h3>\n<ul>\n";foreach($mi
as$ji){$Ri=new
\ReflectionObject($ji);$nc=(method_exists($ji,'description')?$ji->description():"");if(!$nc){if(preg_match('~^/[\s*]+(.+)~',$Ri->getDocComment(),$A))$nc=$A[1];}$uj=(method_exists($ji,'screenshot')?$ji->screenshot():"");echo"<li><b>".get_class($ji)."</b>".h($nc?": $nc":"").($uj?" (<a href='".h($uj)."'".target_blank().">".lang(147)."</a>)":"").$li(basename((string)$Ri->getFileName(),'.php'))."\n";}foreach($Ec
as$s=>$B)echo"<li><b>".h($s)."</b>: ".h($B).$li(basename((string)$ka->driverFiles[$s],'.php'))."\n";if($qc){$fh=official_design_checksums();foreach($qc
as$m=>$pc){list($B,$jb)=$pc;$eh=$fh["$B/$m"];echo"<li><b>".h($m)."</b>".h($B?": $B":"").($eh&&$eh!==$jb?$El("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Fb){$this->size+=strlen($Fb);fwrite($this->handler,$Fb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$Vl=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Vl)).".".friendly_url($_GET["field"]));$N=array(idf_escape($_GET["field"]));$I=driver()->select($a,$N,array(where($_GET,$l)),$N);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=adminer()->error();$S=table_status1($a);$B=adminer()->tableName($S);$j=$j?:h($S["Error"]);page_header(($l&&is_view($S)?$S['Engine']=='materialized view'?lang(148):lang(149):lang(150)).": ".($B!=""?$B:h($a)),$j,array(),"",!$l,($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($S)))):""));$fj=array();foreach($l
as$w=>$k)$fj+=$k["privileges"];adminer()->selectLinks($S,(isset($fj["insert"])||!support("table")?"":null));$wb=$S["Comment"];if($wb!="")echo"<p class='nowrap'>".lang(50).": ".adminer()->commentValue('TABLE',$wb)."\n";if($l)adminer()->tableStructurePrint($l,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$z=preg_replace('~ns=[^&]*~',"ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($z."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$Te=driver()->inheritsFrom($a);if($Te){echo"<h3>".lang(151)."</h3>\n";tables_links($Te);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(152)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(153)."</a>\n";echo"</div>\n";}if(!is_view($S)&&driver()->supportsAlterTable($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(119)."</h3>\n";$Ld=foreign_keys($a);if($Ld){echo"<table>\n","<thead><tr><th>".lang(154)."<th>".lang(155)."<th>".lang(122)."<th>".lang(121)."<td class='hover'><tbody>\n";foreach($Ld
as$B=>$n){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$z=($n["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($n["db"]),ME):($n["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($n["ns"]),ME):ME));echo"<td><a href='".h($z."table=".url_escape($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.lang(156).'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(157)."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(158)."</h3>\n";$gb=driver()->checkConstraints($a);if($gb){echo"<table>\n";foreach($gb
as$w=>$X)echo"<tr title='".h($w)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".lang(156)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(159)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")&&driver()->supportsAlterTable($S)){echo"<div>\n","<h3 id='triggers'>".lang(160)."</h3>\n";$ol=triggers($a);if($ol){echo"<table>\n";foreach($ol
as$w=>$X){echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".lang(156)."</a>";$hj=$X[2];if($hj){$jj=preg_replace('~ns=[^&]*~',"ns=".url_escape($hj["ns"]),ME).'function='.url_escape($hj["function"]).'&name='.url_escape($hj["name"]);echo", <a href='".h($jj)."' title='".h($hj["name"])."'>".lang(161)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(162)."</a>\n","</div>\n";}$Oj=driver()->shadowTables($a);if($Oj){echo"<h3 id='shadow-tables'>".lang(163)."</h3>\n";tables_links($Oj);}$Se=driver()->inheritedTables($a);if($Se){echo"<h3 id='partitions'>".lang(164)."</h3>\n";$Rh=driver()->partitionsInfo($a);if($Rh)echo"<p><code class='jush-".JUSH."'>BY ".h("$Rh[partition_by]($Rh[partition])")."</code>\n";tables_links($Se);}}elseif(isset($_GET["schema"])){page_header(lang(70),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$Qi,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($Qi,$R)as$B=>$Si){if($B!=$R)$d[$R]=max($d[$R],schema_column($B,$Qi,$d)+1);}}return$d[$R];}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(preg_match("~$w|$X~",$U))return" class='$w'";}}$Bk=array();$Dk=array();$Ck=array();$wd=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Wf,PREG_SET_ORDER);foreach($Wf
as$r=>$A){$Bk[$A[1]]=array((float)$A[2],(float)$A[3]);$Dk[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$M=array();$Qi=array();$Ld=array();$sa=driver()->allFields();$ue=array();$Ek=array();foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!=""&&!$S["dependent"])$Ek[$R]=$S;else$ue[$R]=true;}}foreach($Ek
as$R=>$S){$G=0;$M[$R]["fields"]=array();foreach($sa[$R]as$k){$G+=1.25;$wd[$R][$k["field"]]=$G;$M[$R]["fields"][$k["field"]]=$k;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$ue[$X["table"]]){$Ld[$R][]=$X;$Qi[$X["table"]][$R]=array();}}}$d=array();$ae=array();$km=array();$fe=array();foreach(array_keys($M)as$B)schema_column($B,$Qi,$d);arsort($d);foreach($d
as$B=>$c){$xg=null;foreach((array)idx($Ld,$B)as$X){if($X["table"]!=$B&&$M[$X["table"]])$xg=($xg===null?$d[$X["table"]]:min($xg,$d[$X["table"]]));}$d[$B]=max($c,(int)$xg-1);}foreach($M
as$B=>$R){$c=$d[$B];$ae[$c][]=$B;$Tk=.75*strlen($B);foreach($R["fields"]as$k)$Tk=max($Tk,.65*strlen($k["field"]));$km[$c]=max(idx($km,$c,0),ceil($Tk)+1);}foreach($Ld
as$B=>$Ul){foreach($Ul
as$X){$ee=$d[$B]+(idx($d,$X["table"],$d[$B])>$d[$B]?1:0);$fe[$ee]=idx($fe,$ee,0)+1;}}ksort($ae);$se=0;$jm=0;$ub=0;$xi=null;$_k=array();$Gk=array();foreach($ae
as$c=>$T){if($xi!==null){$ub=round($ub+$km[$xi]+1.7+idx($fe,$c,0)*.1,1);$D=array();foreach($T
as$B){$rk=0;$Lb=0;$Ng=array_keys((array)idx($Qi,$B));foreach((array)idx($Ld,$B)as$X)$Ng[]=$X["table"];foreach($Ng
as$Hg){if($M[$Hg]&&$d[$Hg]<$c){$rk+=$M[$Hg]["pos"][0];$Lb++;}}$D[$B]=($Lb?$rk/$Lb:$se);}asort($D);$T=array_keys($D);}$el=0;foreach($T
as$B){$G=1.25*count($M[$B]["fields"]);$M[$B]["pos"]=($Bk[$B]?:array($el,$ub));$_k[$B]=$M[$B]["pos"][1];$Gk[$B]=$km[$c];$el+=2.5+$G;$se=max($se,$M[$B]["pos"][0]+2.5+$G);$jm=max($jm,round($M[$B]["pos"][1]+$km[$c],1));if(!$Bk[$B])$Ck[]="\n\t'".js_escape($B)."': [ ".$M[$B]["pos"][0].", ".$M[$B]["pos"][1]." ]";}$xi=$c;}$Ff=array();$Na=array();foreach($Ld
as$B=>$Ul){foreach($Ul
as$X){$Mk=idx($_k,$X["table"],$_k[$B]);$Yj=$_k[$B]+$Gk[$B];$ej=($Mk-1>$Yj);$Df=($ej?$Yj+1:min($_k[$B],$Mk)-1);$Ma=idx($Na,(string)$Df,0);$Na[(string)$Df]=$Ma+1;$Df=round($ej?min($Df+$Ma*.1,$Mk-1):$Df-$Ma*.1,1);while($Ff[(string)$Df])$Df-=.0001;$M[$B]["references"][$X["table"]][(string)$Df]=array($X["source"],$X["target"]);$Qi[$X["table"]][$B][(string)$Df]=$X["target"];$Ff[(string)$Df]=true;}}echo'<div id="schema" style="height: ',$se,'em; width: ',$jm,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$Dk)."\n",'};
const tablePosDefault = {',implode(",",$Ck)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$se,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($M
as$B=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$Gk[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($R["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$Nk=>$Si){foreach($Si
as$Df=>$Ni){$Ef=$Df-$R["pos"][1];$ok=($Ef>0?"left: 100%; width: calc($Ef"."em - 100%)":"left: $Ef"."em");$jm=($Ef>0?"100%":(-$Ef)."em");$r=0;foreach($Ni[0]as$Xj)echo"\n<div class='references' title='".h($Nk)."' id='refs$Df-".($r++)."' style='$ok"."; top: ".$wd[$B][$Xj]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $jm;'></div></div>";}}foreach((array)$Qi[$B]as$Nk=>$Si){foreach($Si
as$Df=>$Ok){$Ef=$Df-$R["pos"][1];$r=0;foreach($Ok
as$Lk)echo"\n<div class='references arrow' title='".h($Nk)."' id='refd$Df-".($r++)."' style='left: $Ef"."em; top: ".$wd[$B][$Lk]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Ef)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($M
as$B=>$R){foreach((array)$R["references"]as$Nk=>$Si){if($M[$Nk]){foreach($Si
as$Df=>$Ni){$yg=$se;$eg=-10;foreach($Ni[0]as$w=>$Xj){$oi=$R["pos"][0]+$wd[$B][$Xj];$pi=$M[$Nk]["pos"][0]+$wd[$Nk][$Ni[1][$w]];$yg=min($yg,$oi,$pi);$eg=max($eg,$oi,$pi);}echo"<div class='references' id='refl$Df' style='left: $Df"."em; top: $yg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($eg-$yg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(165),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$j){$i=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$tk){if(support($tk))$i[$tk."s"]='';}save_settings(array_intersect_key($_POST+$i,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$i),"adminer_export");$ra=(DB==""||$_GET["ns"]==="");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$nd=dump_headers((count($T)==1?key($T):DB),($ra||count($T)>1));$kf=preg_match('~sql~',$_POST["format"]);if($kf){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$ok=$_POST["db_style"];$g=array(DB);if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($kf&&$ok)echo
use_sql($h,$ok).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$M){if($M!=""){if(DB==""&&information_schema(DB,$M))continue;set_schema($M);}if($kf&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$kk=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$md=array();$Yb=array();foreach($kk
as$B=>$S){if($ra||in_array($B,(array)$_POST["tables"]))$md[$B]=$S;if($ra||in_array($B,(array)$_POST["data"]))$Yb[$B]=$S;}if($kf){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($md);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$pl=array();foreach($Yb
as$B=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($md[$B])))$pl[]=$B;}echo
truncate_all_sql($pl);}$Ih="";if($_POST["types"]){foreach(types()as$s=>$U){$jc=type_definition($s);$ah=($jc["kind"]=='d'?"DOMAIN":"TYPE");if($jc["definition"])$Ih
.=($ok!='DROP+CREATE'?"DROP $ah IF EXISTS ".table($U).";;\n":"")."CREATE $ah ".table($U)." $jc[definition];\n\n";else$Ih
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$B=$K["ROUTINE_NAME"];$hj=$K["ROUTINE_TYPE"];$Mb=create_routine($hj,array("name"=>$B)+routine($K["SPECIFIC_NAME"],$hj));set_utf8mb4($Mb);$Ih
.=($ok!='DROP+CREATE'?"DROP $hj IF EXISTS ".table($B).";;\n":"")."$Mb;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$Mb=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($Mb);$Ih
.=($ok!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$Mb;;\n\n";}}echo($Ih&&JUSH=='sql'?"DELIMITER ;;\n\n$Ih"."DELIMITER ;\n\n":$Ih);}if($_POST["table_style"]||$_POST["data_style"]){$am=array();foreach($kk
as$B=>$S){$R=array_key_exists($B,$md);$Wb=array_key_exists($B,$Yb);if($R||$Wb){$bl=null;if($nd=="tar"){$bl=new
TmpFile;ob_start(array($bl,'write'),1e5);}adminer()->dumpTable($B,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$am[]=$B;elseif($Wb){$l=fields($B);$N=array("*");$Ib=convert_fields($l,$l);if($Ib)$N[]=substr($Ib,2);adminer()->dumpData($B,$_POST["data_style"],"",$N);}if($kf&&$_POST["triggers"]&&$R&&($ol=trigger_sql($B)))echo"\nDELIMITER ;;\n$ol\nDELIMITER ;\n";if($nd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$B.csv",$bl);}elseif($kf)echo"\n";}}if($kf&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($md
as$B=>$S){if(!is_view($S))echo
foreign_keys_sql($B);}}if($kf){foreach($am
as$Zl)adminer()->dumpTable($Zl,$_POST["table_style"],1);}if($nd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(76),$j,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$cc=array('','USE','DROP+CREATE','CREATE');$rj=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$cc);$Fk=array('','DROP+CREATE','CREATE');$Xb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Xb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(166)."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".lang(167)."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(35)."<td>".html_select('db_style',$cc,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$K["routines"],lang(72)):"").(support("event")?checkbox("events",1,$K["events"],lang(74)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(168)."<td>".html_select('schema_style',$rj,$K["schema_style"]):""),"<tr><th>".lang(141)."<td>".html_select('table_style',$Fk,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],lang(51)).(support("trigger")?checkbox("triggers",1,$K["triggers"],lang(160)):""),"<tr><th>".lang(169)."<td>".html_select('data_style',$Xb,$K["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(76),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$wi=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(170)."'".on('click','formCheck','^schemas\[').">".lang(168)."</label>","<tbody>\n";foreach(adminer()->schemas()as$M){if(!information_schema(DB,$M))echo"<tr><td>".checkbox("schemas[]",$M,true,$M,"","block")."\n";}}elseif(DB!=""){$hb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$hb class='jsonly' title='".lang(170)."'".on('click','formCheck','^tables\[').">".lang(150)."</label>","<th style='text-align: right;'><label class='block'>".lang(169)."<input type='checkbox' id='check-data'$hb class='jsonly' title='".lang(170)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$am="";$Ik=tables_list();foreach($Ik
as$B=>$U){$vi=preg_replace('~_.*~','',$B);$hb=($a==""||$a==(substr($a,-1)=="%"?"$vi%":$B));$_i="<tr><td>".checkbox("tables[]",$B,$hb,$B,"","block");if($U!==null&&!preg_match('~table~i',$U))$am
.="$_i\n";else
echo"$_i<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$hb)."</label>\n";$wi[$vi]++;}echo$am;if($Ik)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(170)."'".on('click','formCheck','^databases\[').">":"").lang(35)."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$vi=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$vi%",$h,"","block")."\n";$wi[$vi]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Ed=true;foreach($wi
as$w=>$X){if($w!=""&&$X>1){echo($Ed?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$Ed=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(71));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(171)."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Yd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Yd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(33)."<th>".lang(31)."<td class='hover'><tbody>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"]),"<td>".h($K["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($K["User"]).'&host='.url_escape($K["Host"])).'">'.lang(14)."</a>\n";if(!$Yd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(14)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$j&&$_POST["val"]){$na=0;$pk=true;$ab=array();$oj=0;foreach($_POST["val"]as$L)$oj+=count($L);$Pa=$oj>1&&driver()->begin();foreach($_POST["val"]as$yk=>$L){$R=bracket_escape($yk,true);$l=fields($R);$zk=indexes($R);foreach($L
as$t=>$K){parse_str(bracket_escape($t,true),$Z);$xl=array();foreach($Z["where"]as$w=>$X)$xl[bracket_escape($w,true)]=$X;if(!$l||$Z["null"]||array_diff_key($xl,$l)||!unique_array($xl,$zk)){$pk=false;break
2;}$P=array();$N=array();foreach($K
as$sf=>$X){$w=bracket_escape($sf,true);$k=idx($l,$w);if(!$k){$pk=false;break
3;}$P[idf_escape($w)]=(preg_match('~char|text~',$k["type"])||$X!=""?adminer()->processInput($k,$X):"NULL");$N[$sf]=$w;}$Ji=where($Z,$l);if(!driver()->update($R,$P," WHERE $Ji",0," ")){$pk=false;break
2;}$na+=connection()->affected_rows;$d=array();foreach($N
as$w)$d[]=idf_escape($w);$Fl=driver()->select($R,$d,array($Ji),$d);$Rg=($Fl?$Fl->fetch_row():array());$of=0;foreach($N
as$sf=>$w){$k=$l[$w];$nk=array('type'=>(preg_match('~binary~',$k["type"])?'blob':$k["type"]));$ab["val[$yk][$t][$sf]"]=select_value(idx($Rg,$of++),"",$nk,null);}}}if($Pa&&$pk)$pk=driver()->commit();queries_redirect(null,lang(172,$na),$pk);if($Pa&&!$pk)driver()->rollback();page_headers();page_messages($j);foreach($ab
as$B=>$X)echo"<div data-name='".h($B)."' hidden>$X</div>\n";exit;}restart_session();$ye=&get_session("queries");$xe=&$ye[DB];if(!$j&&$_POST["clear"]){$xe=array();redirect(remove_from_uri("history"));}stop_session();$la=get_settings("adminer_import");if($_POST&&$la)save_settings($la,"adminer_import");page_header((isset($_GET["import"])?lang(75):lang(64)),$j);$Nf=driver()->lineComment();if(!$j&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$lc=driver()->delimiter;$o=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$bk=adminer()->importServerPath();$o=@fopen((file_exists($bk)?$bk:"compress.zlib://$bk.gz"),"rb");$H=($o?fread($o,1e6):false);}else$H=get_file("sql_file",true,$lc);if(is_string($H)){if(($mg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($mg,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$Gi=$H.(preg_match("~$lc\\s*\$~",$H)?"":$lc);if(!$xe||first(end($xe))!=$Gi){restart_session();$xe[]=array($Gi,time());set_session("queries",$ye);stop_session();}}$Zj="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Nf)[^\n]*\n?|--\r?\n)";$gh=0;$Rc=true;$Kb=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$vb=0;$Zc=array();$Ph='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Nf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$fl=microtime(true);while($H!=""){if(!$gh&&preg_match("~^$Zj*+DELIMITER\\s+(\\S+)~i",$H,$A)){$lc=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$gh&&JUSH=='pgsql'&&preg_match("~^($Zj*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$lc="\n\\\\\\.\r?\n";$Kb=true;$gh=strlen($A[0]);}else{preg_match("($lc\\s*|$Ph)",$H,$A,PREG_OFFSET_CAPTURE,$gh);list($Nd,$G)=$A[0];if(!$Nd&&$o&&!feof($o))$H
.=fread($o,1e5);else{if(!$Nd&&rtrim($H)=="")break;$gh=$G+strlen($Nd);if($Nd&&!preg_match("(^$lc)",$Nd)){$Xa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$ei=($Nd=='/*'?'\*/':($Nd=='['?']':(preg_match("~^(?:$Nf)~",$Nd)?"\n":preg_quote($Nd).($Xa?'|\\\\.':''))));while(preg_match("($ei|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$gh)){$pj=$A[0][0];if(!$pj&&$o&&!feof($o))$H
.=fread($o,1e5);else{$gh=$A[0][1]+strlen($pj);if(!$pj||$pj[0]!="\\")break;}}}else{$Gi=substr($H,0,$G+($Kb?3:0));$H=substr($H,$gh);$gh=0;if($Kb){$lc=driver()->delimiter;$Kb=false;}$ob="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Gi)."</code>";if(preg_match("~^$Zj*+\$~",$Gi)&&!preg_match('~/\*M?!~',$Gi)){echo($_POST["only_errors"]?"":"<pre>$ob</pre>\n");continue;}$Rc=false;$vb++;$_i="<pre id='sql-$vb'>$ob</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Zj*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$Gi,$A)!==0){echo$_i,"<p class='error'>".lang(173,preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$Zc[]=" <a href='#sql-$vb'>$vb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$_i;ob_flush();flush();}$gk=microtime(true);if(connection()->multi_query($Gi)&&$f&&preg_match("~^$Zj*+USE\\b~i",$Gi))$f->query($Gi);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$_i:""),"<p class='error'>".lang(174).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Zc[]=" <a href='#sql-$vb'>$vb</a>";if($_POST["error_stops"])break
2;}else{$z=ME."sql=".url_escape(trim($Gi));$Uk=" <span class='time'>(".format_time($gk).")</span>".(strlen($z)<1900?" <a href='".h($z)."'>".lang(14)."</a>":"");$na=connection()->affected_rows;$dm=($_POST["only_errors"]?"":driver()->warnings());$em="warnings-$vb";if($dm)$Uk
.=", <a href='#$em' class='toggle'>".lang(46)."</a>";$kd="";$ld="explain-$vb";if(is_object($I)){$y=$_POST["limit"];$Yg=$y;$Kc=!$_POST["only_errors"];if($Kc)echo"<form action='' method='post'>\n";$Ah=print_select_result($I,$f,array(),$Yg,$Kc);if(!$_POST["only_errors"]){$Yg=max($I->num_rows,$Yg);echo"<p class='sql-footer'>".($Yg?($y&&$Yg>$y?lang(175,$y):"").lang(176,$Yg):""),$Uk;if($f&&preg_match("~^($Zj|\\()*+SELECT\\b~i",$Gi)&&($kd=adminer()->explain($f,$Gi,$Ah))!="")echo", <a href='#$ld' class='toggle'>Explain</a>";if($Kc)echo", <input type='submit' name='save' value='".lang(18)."' class='jsonly' disabled"." title='".lang(177)."'".on('click','sqlSave',lang(21)).">";$s="export-$vb";echo", <a href='#$s' class='toggle'>".lang(76)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$la["output"])." ".html_select("format",adminer()->dumpFormat(),$la["format"]).input_hidden("query",$Gi)."<input type='submit' name='export' value='".lang(76)."'".($y?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Zj*+(CREATE|DROP|ALTER)$Zj++(DATABASE|SCHEMA)\\b~i",$Gi)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(178,$na)."$Uk\n";}echo($dm?"<div id='$em' class='hidden'>\n$dm</div>\n":""),($kd!=""?"<div id='$ld' class='hidden explain'>\n$kd</div>\n":"");}$gk=microtime(true);}while(connection()->next_result());}}}}}if($Rc)echo"<p class='message'>".lang(179)."\n";else{$Le=connection()->inTransaction();driver()->rollback();if($Le)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(180,$vb-count($Zc))," <span class='time'>(".format_time($fl).")</span>\n";elseif($Zc&&$vb>1)echo"<p class='error'>".lang(174).": ".implode("",$Zc)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$Gl="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($Gl);echo'>
';$hd="<input type='submit' value='".lang(181)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Gi=$_GET["sql"];if($_POST)$Gi=$_POST["query"];elseif($_GET["history"]=="all")$Gi=$xe;elseif($_GET["history"]!="")$Gi=idx($xe[$_GET["history"]],0);echo"<p>";textarea("query",$Gi,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$hd\n",lang(182).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$ge=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(183)."</legend><div>",($Gl?input_hidden(ini_get("session.upload_progress.name"),$Gl):""),"SQL$ge: ".file_input(" name='sql_file[]' multiple","\n$hd"),($Gl?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Ie=adminer()->importServerPath();if($Ie)echo"<fieldset><legend>".lang(184)."</legend><div>",lang(185,"<code>".h($Ie)."$ge</code>")," <input type='submit' name='webfile' value='".lang(186)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(187))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(188))."\n",input_token();if(!isset($_GET["import"])&&$xe){print_fieldset("history",lang(189),$_GET["history"]!="");for($X=end($xe);$X;$X=prev($xe)){$w=key($xe);list($Gi,$Uk,$Nc)=$X;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.lang(14)."</a>"." <span class='time' title='".@date('Y-m-d',$Uk)."'>".@date("H:i:s",$Uk)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Nf).*~m",'',$Gi))),80,"</code>").($Nc?" <span class='time'>($Nc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(190)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(191)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$Dl=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$B=>$k){if((!$Dl&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$B]);}if($_POST&&!$j&&!isset($_GET["select"])){$_=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$_=($Dl?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$_))$_=ME."select=".url_escape($a);$v=indexes($a);$yl=unique_array($_GET["where"],$v);$Ji="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_,lang(192),driver()->delete($a,$Ji,$yl?0:1));else{$P=array();foreach($l
as$B=>$k){$X=process_input($k);if($X!==false&&$X!==null)$P[idf_escape($B)]=$X;}if($Dl){if(!$P)redirect($_);queries_redirect($_,lang(193),driver()->update($a,$P,$Ji,$yl?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$I=driver()->insert($a,$P);$Cf=($I?last_id($I):0);queries_redirect($_,lang(194,($Cf?" $Cf":"")),$I);}}}$K=null;$H="";$Uk="";if($Z){$N=array();$_j=array("*");foreach($l
as$B=>$k){if(isset($k["privileges"]["select"])){$Aa=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$c=($Aa?"$Aa AS ":"").idf_escape($B);$N[]=$c;if($Aa)$_j[]=$c;}}$K=array();if(!support("table")){$N=array("*");$_j=$N;}if($N){$gk=microtime(true);$I=driver()->select($a,$N,array($Z),$N,array(),(isset($_GET["select"])?2:1));$H=str_replace("SELECT ".implode(", ",$N),"SELECT ".implode(", ",$_j),driver()->query);$Uk=format_time($gk);if(!$I)$j=adminer()->error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$w=>$X){if(!$Z)$K[$w]=null;$l[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"]){$qi=array();foreach((array)$_POST["fields"]as$w=>$X)$qi[bracket_escape($w,true)]=$X;$K=$qi+($K?$K:array());}edit_form($a,$l,$K,$Dl,$j,$H,$Uk);}elseif(isset($_GET["create"])){function
referencable_primary($Bj){$J=array();foreach(table_status('',true)as$Ak=>$R){if($Ak!=$Bj&&!$R["dependent"]&&fk_support($R)){foreach(fields($Ak)as$k){if($k["primary"]){if($J[$Ak]){unset($J[$Ak]);break;}$J[$Ak]=$k;}}}}return$J;}$a=$_GET["create"];$Th=driver()->partitionBy;$Xh=($Th&&$a!=""?driver()->partitionsInfo($a):array());$Pi=referencable_primary($a);$Ld=array();foreach($Pi
as$Ak=>$k)$Ld[str_replace("`","``",$Ak)."`".str_replace("`","``",$k["field"])]=$Ak;$Dh=array();$S=array();$Wg=false;if($a!=""){$Dh=fields($a);$S=table_status1($a);$Wg=(count($S)<2);}$va=($a==""||driver()->supportsAlterTable($S));$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$j)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$j){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(195),drop_tables(array($a)));else{$l=array();$sa=array();$Kl=false;$Jd=array();$Ch=reset($Dh);$pa=" FIRST";foreach($K["fields"]as$k){$n=$Ld[$k["type"]];$rl=($n!==null?$Pi[$n]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$Ei=process_field($k,$rl);$sa[]=array($k["orig"],$Ei,$pa);if(!$Ch||$Ei!==process_field($Ch,$Ch)){$l[]=array($k["orig"],$Ei,$pa);if($k["orig"]!=""||$pa)$Kl=true;}if($n!==null)$Jd[idf_escape($k["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Ld[$k["type"]],'source'=>array($k["field"]),'target'=>array($rl["field"]),'on_delete'=>$k["on_delete"],),object_name("FOREIGN",trim($K["name"]),array($k["field"])));$pa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$Kl=true;$l[]=array($k["orig"]);}if($k["orig"]!=""){$Ch=next($Dh);if(!$Ch)$pa="";}}$Vh=array();if(in_array($K["partition_by"],$Th)){foreach($K
as$w=>$X){if(preg_match('~^partition~',$w))$Vh[$w]=$X;}foreach($Vh["partition_names"]as$w=>$B){if($B==""){unset($Vh["partition_names"][$w]);unset($Vh["partition_values"][$w]);}}$Vh["partition_names"]=array_values($Vh["partition_names"]);$Vh["partition_values"]=array_values($Vh["partition_values"]);if($Vh==$Xh)$Vh=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$Vh=null;$og=lang(196);if($a==""){cookie("adminer_engine",$K["Engine"]);$og=lang(197);}$B=trim($K["name"]);$_=ME.(support("table")?"table=":"select=").url_escape($B);$I=alter_table($a,$B,(JUSH=="sqlite"&&($Kl||$Jd)?$sa:$l),$Jd,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$Vh);if($I&&!Queries::$queries&&$a!=""&&!$l&&!$Jd)redirect($_);queries_redirect($_,$og,$I);}}$hk=($a!=""?"alter":"create");page_header(($a!=""?lang(44):lang(77)),$j,array("table"=>$a),h($a),$Wg,doc_link(array('sql'=>"$hk-table.html",'mariadb'=>($a!=""?"$hk-table":""),)));if(!$_POST){$ul=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($ul["int"])?"int":(isset($ul["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Dh
as$k){if($k["generated"])$k["default"]=ltrim($k["default"]);$k["generated"]=$k["generated"]?:(isset($k["default"])?"DEFAULT":"");$K["fields"][]=$k;}if($Th){$K+=$Xh;$K["partition_names"][]="";$K["partition_values"][]="";}}}$sb=flat_collations();$Uc=driver()->engines();foreach($Uc
as$Tc){if(!strcasecmp($Tc,$K["Engine"])){$K["Engine"]=$Tc;break;}}$Zf=max_input_vars(12,20);if($Zf){$ue=(count($K["fields"])>$Zf?"":" hidden");echo"<p".($ue?" id='max-fields' data-columns='$Zf'":"")." class='error$ue'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(198).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",(!$va?h($S["Engine"])."\n":($Uc?html_select("Engine",array(""=>"(".lang(199).")")+$Uc,$K["Engine"],on('change','helpClose').on_help_value())."\n":""));if($sb)echo"<datalist id='collations'>".optionlist($sb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".lang(120).")'>\n");echo"<input type='submit' value='".lang(18)."'>\n";}if(support("columns")&&$va){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$sb,"TABLE",$Ld);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(51).": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(200),on('click','columnShowClick',6),"jsonly");$yb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$yb,lang(50),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($yb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$a)),'>
';if($Th&&(JUSH=='sql'||$a=="")){$Uh=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",lang(202),$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Th),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",lang(203).": <input type='number' name='partitions' class='size".($Uh||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($Uh?"":" class='hidden'").">\n","<thead><tr><th>".lang(204)."<th>".lang(205)."<tbody>\n";foreach($K["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($w==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Qe=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$Oe=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Qe[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Qe[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Qe[]="VECTOR";$v=indexes($a);$l=fields($a);$zi=array();if(JUSH=="mongo"){$zi=$v["_id_"];unset($Qe[0]);unset($v["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]){$ua=array();foreach($K["indexes"]as$u){$B=$u["name"];if(in_array($u["type"],$Qe)){$d=array();$Jf=array();$oc=array();$rh=array();$Pe=(support("partial_indexes")?$u["partial"]:"");$Ne=(in_array($u["algorithm"],$Oe)?$u["algorithm"]:"");$P=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$c){if($c!=""){$x=idx($u["lengths"],$w);$mc=idx($u["descs"],$w);$qh=idx($u["opclasses"],$w);$P[]=($l[$c]?idf_escape($c):$c).($x?"(".(+$x).")":"").($qh!=""?" ".idf_escape($qh):"").($mc?" DESC":"");$d[]=$c;$Jf[]=($x?:null);$oc[]=$mc;$rh[]="$qh";}}$id=$v[$B];if($id){ksort($id["columns"]);ksort($id["lengths"]);ksort($id["descs"]);if($u["type"]==$id["type"]&&array_values($id["columns"])===$d&&(!$id["lengths"]||array_values($id["lengths"])===$Jf)&&array_values($id["descs"])===$oc&&(!$id["opclasses"]||array_values($id["opclasses"])===$rh)&&$id["partial"]==$Pe&&(!$Oe||$id["algorithm"]==$Ne)){unset($v[$B]);continue;}}if($d)$ua[]=array($u["type"],$B,$P,$Ne,$Pe);}}foreach($v
as$B=>$id)$ua[]=array($id["type"],$B,"DROP");if(!$ua)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(206),alter_indexes($a,$ua));}page_header(lang(152),$j,array("table"=>$a),h($a),false,doc_link(array('sql'=>"create-index.html",)));$yd=array_keys($l);if($_POST["add"]){foreach($K["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$K["indexes"][$w]["columns"][]="";}$u=end($K["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$K["indexes"]=$v;}$Jf=(JUSH=="sql"||JUSH=="mssql");$rh=driver()->indexOpclasses();$Pj=($_POST?$_POST["options"]:get_setting("index_options"));$Jg=array();foreach($Qe
as$U)$Jg[$U]=str_replace("{table}",$a,adminer()->namePattern($U));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(207);$Ge=" class='idxopts".($Pj?"":" hidden")."'";if($Oe)echo"<th id='label-algorithm'$Ge>".lang(208).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" hidden>',lang(209).($Jf?"<span$Ge> (".lang(210).")</span>":"");if($Jf||support("descidx"))echo
checkbox("options",1,$Pj,lang(126),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(211);if(support("partial_indexes"))echo"<th id='label-condition'$Ge>".lang(212);echo'<td><noscript>',icon("plus","add[0]","+",lang(127)),'</noscript>
<tbody>
';if($zi){echo"<tr><td>PRIMARY<td>";foreach($zi["columns"]as$w=>$c)echo
select_input(" disabled",array_combine($yd,$yd),$c),"<label><input disabled type='checkbox'>".lang(59)."</label> ";echo"<td><td>\n";}$of=1;foreach($K["indexes"]as$u){if(!$_POST["drop_col"]||$of!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$of][type]",array(-1=>"")+$Qe,$u["type"],on('change','indexesChangeType',$Jg),"label-type");if($Oe)echo"<td$Ge>".html_select("indexes[$of][algorithm]",array_merge(array(""),$Oe),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$c){echo"<span>".select_input(" name='indexes[$of][columns][$r]' title='".lang(48)."'".on('change','indexesChangeColumn',$Jg),($l&&($c==""||$l[$c])?array_combine($yd,$yd):array()),$c)," <span$Ge>",($Jf?"<input type='number' name='indexes[$of][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(125)."'>":"");if($rh){$qh=idx($u["opclasses"],$w);echo
html_select("indexes[$of][opclasses][$r]",array(""=>"(".lang(213).")")+array_combine($rh,$rh)+($qh!=""?array($qh=>$qh):array()),$qh),'';}echo(support("descidx")?checkbox("indexes[$of][descs][$r]",1,idx($u["descs"],$w),lang(59)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$of][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Ge><input name='indexes[$of][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$of]","x",lang(129),on('click','editingRemoveRow','indexes$1[type]'));}$of++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(18),'\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$j&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(214),drop_databases(array(DB)));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),lang(215),rename_database($B,(string)$K["collation"]));}else{$g=explode("\n",str_replace("\r","",$B));$pk=true;$Af="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$K["collation"]))$pk=false;$Af=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($Af),lang(216),$pk);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),lang(217));}}$hk=(DB!=""?"alter":"create");page_header(DB!=""?lang(67):lang(133),$j,array(),h(DB),false,doc_link(array('sql'=>"$hk-database.html",'mariadb'=>(DB!=""?"":"$hk-database"),)));$sb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$sb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Yd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Yd,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($sb?html_select("collation",array(""=>"(".lang(120).")")+$sb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):"")."\n",'<input type=\'submit\' value=\'',lang(18),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(145)."'".confirm(lang(201,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(127))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$mj=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$hj=routine($_GET["call"],$mj);page_header(lang(218).": ".h($ca),$j,"#routines","",!$hj,(isset($_GET["callf"])?"":doc_link(array('sql'=>"call.html",))));$Je=array();$Ih=array();foreach($hj["fields"]as$r=>$k){if(substr($k["inout"],-3)=="OUT"&&JUSH=='sql')$Ih[$r]="@".idf_escape($k["field"])." AS ".idf_escape($k["field"]);if(!$k["inout"]||preg_match('~^(IN|OUTPUT)~',$k["inout"]))$Je[]=$r;}if(!$j&&$_POST){$Ya=array();foreach($hj["fields"]as$w=>$k){$X="";if(in_array($w,$Je)){$X=process_input($k);if($X===false)$X="''";if(isset($Ih[$w]))connection()->query("SET @".idf_escape($k["field"])." = $X");}if(isset($Ih[$w]))$Ya[]="@".idf_escape($k["field"]);elseif(in_array($w,$Je))$Ya[]=$X;}$za=implode(", ",$Ya);$H=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($hj["returns"],"type")=="record"?"* FROM ":"").table($ca)."($za)":"EXEC ".table($ca).($za!=""?" $za":""));$gk=microtime(true);$I=connection()->multi_query($H);$na=connection()->affected_rows;echo
adminer()->selectQuery($H,$gk,!$I);if(!$I)echo"<p class='error'>".adminer()->error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang(219,$na)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Ih)print_select_result(connection()->query("SELECT ".implode(", ",$Ih)));}}echo'
<form action="" method="post">
';if($Je){echo"<table class='layout'>\n";foreach($Je
as$w){$k=$hj["fields"][$w];$B=$k["field"];echo"<tr><th>".adminer()->fieldName($k);$Y=idx($_POST["fields"],$B);if($Y!=""){if($k["type"]=="set")$Y=implode(",",$Y);}input($k,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(218),'\'>
',input_token(),'</form>

',adminer()->commentValue($mj,$hj['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Lk=array();foreach($K["source"]as$w=>$X)$Lk[$w]=$K["target"][$w];$K["target"]=$Lk;}$Cb=object_name("FOREIGN",$a,$K["source"]);if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K,$Cb))));else{$ua="ALTER TABLE ".table($a);$I=($B==""||queries("$ua DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$K["drop"])$I=queries("$ua ADD".format_foreign_key($K,$Cb));}queries_redirect(ME."table=".url_escape($a),($K["drop"]?lang(220):($B!=""?lang(221):lang(222))),$I);if(!$K["drop"])$j=lang(223);}$Wg=false;if(!$_POST&&$B!=""){$Ld=foreign_keys($a);$K=idx($Ld,$B,array());$Wg=!$K;}page_header(($B!=""?lang(224):lang(157)),$j,array("table"=>$a),h($B!=""?$B:$a),$Wg,doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)));if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($B!="")$K["source"][]="";else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Xj=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$Eh=get_schema();set_schema($K["ns"]);}$Oi=array_keys(array_filter(table_status('',true),function(array$S){return!$S["dependent"]&&fk_support($S);}));$Lk=array_keys(fields(in_array($K["table"],$Oi)?$K["table"]:reset($Oi)));$b=on('change','foreignChange');echo"<p><label>".lang(225).": ".html_select("table",$Oi,$K["table"],$b)."</label>\n";if(JUSH!="sqlite"){$dc=array();foreach(adminer()->databases()as$h){if(!information_schema($h))$dc[]=$h;}echo"<label>".lang(78).": ".html_select("db",$dc,$K["db"]!=""?$K["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(226),'\'></noscript>
<table>
<thead><tr><th id="label-source">',lang(154),'<th id="label-target">',lang(155),'<tbody>
';$of=0;foreach($K["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$Xj,$X,($of==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$Lk,idx($K["target"],$w),"","label-target");$of++;}echo'</table>
<p>
<label>',lang(122),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>',lang(121),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(227),'\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Fh="VIEW";if(JUSH=="pgsql"&&$a!=""){$jk=table_status1($a);$Fh=strtoupper($jk["Engine"]);}if($_POST&&!$j){$B=trim($K["name"]);$Aa=" AS\n$K[select]";$_=ME."table=".url_escape($B);$og=lang(228);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$U=="VIEW"&&$Fh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Aa,$_,$og);else{$Pk="adminer_".uniqid();drop_create("DROP $Fh ".table($a),"CREATE $U ".table($B).$Aa,"DROP $U ".table($B),"CREATE $U ".table($Pk).$Aa,"DROP $U ".table($Pk),($_POST["drop"]?substr(ME,0,-1):$_),lang(229),$og,lang(230),$a,$B);}}$Wg=false;if(!$_POST&&$a!=""){$K=view($a);$Wg=!$K["select"];$K["name"]=$a;$K["materialized"]=($Fh!="VIEW");if(!$j)$j=adminer()->error();}page_header(($a!=""?lang(43):lang(231)),$j,array("table"=>$a),h($a),$Wg,doc_link(array('sql'=>"create-view.html",)));echo'
<form action="" method="post">
<p>',lang(211),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],lang(148)):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$bf=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$kk=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$j){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(232));elseif(in_array($K["INTERVAL_FIELD"],$bf)&&isset($kk[$K["STATUS"]])){$qj="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(233):lang(234)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$qj.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$qj)."\n".$kk[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}$Wg=false;if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$Wg=!$L;$K=reset($L);}page_header(($aa!=""?lang(235).": ".h($aa):lang(236)),$j,"#events","",$Wg,doc_link(array('sql'=>"create-event.html")));echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(211),'<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(237),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(238),'<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>',lang(239),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$bf,$K["INTERVAL_FIELD"]),'<tr><th>',lang(136),'<td>',html_select("STATUS",$kk,$K["STATUS"]),'<tr><th>',lang(50),'<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",lang(240)),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$hj=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$j){foreach($K["fields"]as$w=>$k){if($k["field"]=="")unset($K["fields"][$w]);}$lh=routine($_GET["procedure"],$hj);$jh=($lh?routine_id($ca,$lh):"");$Pg=routine_id($K["name"],$K);$Mb=create_routine($hj,$K);$_=substr(ME,0,-1);$og=lang(241);if(!$_POST["drop"]&&$jh==$Pg&&connection()->flavor!="mysql")queries_redirect($_,$og,queries(substr_replace($Mb,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$Pk="adminer_".uniqid();drop_create("DROP $hj $jh",$Mb,"DROP $hj $Pg",create_routine($hj,array("name"=>$Pk)+$K),"DROP $hj ".routine_id($Pk,$K),$_,lang(242),$og,lang(243),$ca,$K["name"]);}}$Wg=false;if(!$_POST&&$ca!=""){$K=routine($_GET["procedure"],$hj);$Wg=!$K;$K["name"]=$ca;}$kj=strtolower($hj);page_header(($ca!=""?(isset($_GET["function"])?lang(161):lang(244)).": ".h($ca):(isset($_GET["function"])?lang(245):lang(246))),$j,"#routines","",$Wg,doc_link(array('sql'=>"create-procedure.html",'mariadb'=>"create-$kj/",)));if(!$_POST&&$ca=="")$K["language"]="sql";$sb=(JUSH=="sql"?flat_collations():array());$ij=routine_languages();echo($sb?"<datalist id='collations'>".optionlist($sb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(211),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($ij?"<label>".lang(24).": ".html_select("language",array_keys($ij),$K["language"],on('change','routineLanguage',$ij))."</label>\n":""),'<input type=\'submit\' value=\'',lang(18),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$sb,$hj);if(isset($_GET["function"])){echo"<tr><td>".lang(247);edit_type("returns",(array)$K["returns"],$sb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20,80,($ij[$K["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$ca)),'>
';$lj=routine_options($hj);if($lj){$wh=false;foreach($lj
as$w=>$Vl){$i=($Vl?reset($Vl):"");$K["options"][$w]=idx($K["options"],$w,$i);if($K["options"][$w]!=$i)$wh=true;}print_fieldset("options",lang(126),$wh);echo"<table class='layout'>\n";foreach($lj
as$w=>$Vl){$wf="label-option-$w";$Xk=str_replace("_"," ",$w);$N=array();foreach($Vl
as$Y)$N[$Y]=(strpos($Y,"$Xk ")===0?substr($Y,strlen($Xk)+1):$Y);echo"<tr><th id='$wf'>$Xk<td>".($N?html_select("options[$w]",$N,$K["options"][$w],"",$wf):"<input name='options[$w]' value='".h($K["options"][$w])."' aria-labelledby='$wf' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$K=$_POST;if($K&&!$j){$_=ME."table=".url_escape($a);$rg=lang(248);$pg=lang(249);$qg=lang(250);if(JUSH=="sqlite")queries_redirect($_,($K["drop"]?$rg:($B!=""?$pg:$qg)),recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($K["drop"]?"":$K["clause"])));else{$ua="ALTER TABLE ".table($a);$fb=" CHECK ($K[clause])";$Pk="adminer_".uniqid();drop_create("$ua DROP CONSTRAINT ".idf_escape($B),"$ua ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"").$fb,"$ua DROP CONSTRAINT ".idf_escape($K["name"]),"$ua ADD CONSTRAINT ".idf_escape($Pk).$fb,"$ua DROP CONSTRAINT ".idf_escape($Pk),$_,$rg,$pg,$qg,$B,$K["name"]);}}$Wg=false;if(!$K){$ib=driver()->checkConstraints($a);$Wg=($B!=""&&!$ib[$B]);$K=array("name"=>($B!=""?$B:object_name("CHECK",$a,array())),"clause"=>$ib[$B]);}page_header(($B!=""?lang(251):lang(159)),$j,array("table"=>$a),h($B!=""?$B:$a),$Wg,doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",)));echo'
<form action="" method="post">
';if(JUSH!="sqlite")echo'<p>'.lang(211).': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off">';echo'<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'',lang(18),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$nl=trigger_options();$K=trigger($B,$a);$Wg=($B!=""&&!$K);$Ig=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$K+=array("Trigger"=>strtr($Ig,array("{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row")));if($_POST){if(!$j&&in_array($_POST["Timing"],$nl["Timing"])&&in_array($_POST["Event"],$nl["Event"])&&in_array($_POST["Type"],$nl["Type"])){$nh=" ON ".table($a);$Fc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$nh:"");$_=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Fc,$_,lang(252));else{if($B!="")queries($Fc);queries_redirect($_,($B!=""?lang(253):lang(254)),queries(create_trigger($nh,$_POST)));if($B!="")queries(create_trigger($nh,$K+array("Type"=>reset($nl["Type"]))));}}$K=$_POST;}page_header(($B!=""?lang(255):lang(162)),$j,array("table"=>$a),h($B!=""?$B:$a),$Wg,doc_link(array('sql'=>"create-trigger.html",)));$Lg=strtr(preg_quote($Ig),array('\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)'));$ml=on('change','triggerChange',"^$Lg$",$Ig);$ch=on('input','triggerChange',"^$Lg$",$Ig);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(256),'<td>',html_select("Timing",$nl["Timing"],$K["Timing"],$ml),'<tr><th>',lang(257),'<td>',html_select("Event",$nl["Event"],$K["Event"],$ml),(in_array("UPDATE OF",$nl["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'$ch>":""),'<tr><th>',lang(49),'<td>',html_select("Type",$nl["Type"],$K["Type"],$ml),'<tr><th>',lang(211),'<td><input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($Yd,array$Ci,$d,$nh){if(!$Ci)return
true;if($Ci==array("ALL PRIVILEGES","GRANT OPTION"))return($Yd=="GRANT"?queries("$Yd ALL PRIVILEGES$nh WITH GRANT OPTION"):queries("$Yd ALL PRIVILEGES$nh")&&queries("$Yd GRANT OPTION$nh"));return
queries("$Yd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$Ci).$d).$nh);}$ea=$_GET["user"];$Ci=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$Gb)$Ci[$Gb=="File access on server"?"Server Admin":$Gb][$K["Privilege"]]=$K["Comment"];}unset($Ci["Server Admin"]["Usage"]);foreach($Ci["Tables"]as$w=>$X)unset($Ci["Databases"][$w]);$Og=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$Og[$X]=(array)$Og[$X]+idx($_POST["grants"],$w,array());}$Zd=array();$I=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])):null);$Wg=(isset($_GET["host"])&&!$I);if($I){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$Wf,PREG_SET_ORDER)){foreach($Wf
as$X){if($X[1]!="USAGE")$Zd["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$Zd["$A[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$j){$mh=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $mh",ME."privileges=",lang(258));else{$Sg=q($_POST["user"])."@".q($_POST["host"]);$Zh=$_POST["pass"];$Ob=false;$I=true;if($mh!=$Sg){$Ob=queries("CREATE USER $Sg IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($Zh));$I=$Ob;}elseif($Zh!="")$I=queries("SET PASSWORD FOR $Sg = ".(min_version(8,99)||$_POST["hashed"]?q($Zh):"PASSWORD(".q($Zh).")"));if($I){$dj=array();foreach($Og
as$ah=>$Yd){if(isset($_GET["grant"]))$Yd=array_filter($Yd);$Yd=array_keys($Yd);if(isset($_GET["grant"]))$dj=array_diff(array_keys(array_filter($Og[$ah],'strlen')),$Yd);elseif($mh==$Sg){$ih=array_keys((array)$Zd[$ah]);$dj=array_diff($ih,$Yd);$Yd=array_diff($Yd,$ih);unset($Zd[$ah]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$ah,$A)&&(!grant("REVOKE",$dj,$A[2]," ON $A[1] FROM $Sg")||!grant("GRANT",$Yd,$A[2]," ON $A[1] TO $Sg"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($mh!=$Sg)queries("DROP USER $mh");elseif(!isset($_GET["grant"])){foreach($Zd
as$ah=>$dj){if(preg_match('~^(.+)(\(.*\))?$~U',$ah,$A))grant("REVOKE",array_keys($dj),$A[2]," ON $A[1] FROM $Sg");}}}if($I&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(259):lang(260)),$I);if($Ob)connection()->query("DROP USER $Sg");}}page_header((isset($_GET["host"])?lang(33).": ".h("$ea@$_GET[host]"):lang(171)),$j,array("privileges"=>array('',lang(71))),"",$Wg,doc_link(array('sql'=>"grant.html",'mariadb'=>"grant")));$K=$_POST;if($K)$Zd=$Og;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Zd[(DB==""||$Zd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(31),'<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>',lang(33),'<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>',lang(34),'<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],lang(261),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(71);$r=0;foreach($Zd
as$ah=>$Yd){echo'<th>'.($ah!="*.*"?"<input name='objects[$r]' value='".h($ah)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(31),"Databases"=>lang(35),"Tables"=>lang(150),"Procedures"=>lang(262),)as$Gb=>$mc){foreach((array)$Ci[$Gb]as$Bi=>$wb){echo"<tr><td".($mc?">$mc<td":" colspan='2'").' lang="en" title="'.h($wb).'">'.h($Bi);$r=0;foreach($Zd
as$ah=>$Yd){$B="'grants[$r][".h(strtoupper($Bi))."]'";$Y=$Yd[strtoupper($Bi)];if($Gb=="Server Admin"&&$ah!=(isset($Zd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($Y?" selected":"").">".lang(263)."<option value='0'".($Y=="0"?" selected":"").">".lang(264)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($Y?" checked":"").($Bi=="All privileges"?" id='grants-$r-all'":($Bi=="Grant option"?"":on('click','grantsClick',"grants-$r-all"))).">","</label>";$r++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(145),'\'',confirm(lang(201,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$j){$vf=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$vf++;}queries_redirect(ME."processlist=",lang(265,$vf),$vf||!$_POST["kill"]);}}page_header(lang(134),$j);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$r=-1;foreach(adminer()->processList()as$r=>$K){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$w=>$X)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$w=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($K["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($K["db"])."&":ME)."sql=".url_escape($X)).'">'.lang(266).'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($r+1)."/".lang(267,max_connections()),"<p><input type='submit' value='".lang(268)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$l=fields($a);$Ld=column_foreign_keys($a);$hh=$S["Oid"];$fj=array();$d=array();$wj=array();$yh=array();$Sk=null;foreach($l
as$w=>$k){$B=adminer()->fieldName($k);$Kg=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$B!=""){$d[$w]=$Kg;if(is_shortable($k))$Sk=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$B!="")$wj[$w]=$Kg;if(isset($k["privileges"]["order"])&&$B!="")$yh[$w]=$Kg;$fj+=$k["privileges"];}list($N,$q)=adminer()->selectColumnsProcess($d,$v);$N=array_unique($N);$q=array_unique($q);$if=count($q)<count($N);$Z=adminer()->selectSearchProcess($l,$v,$S);$D=adminer()->selectOrderProcess($l,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$zl=>$K){$Aa=convert_field($l[key($K)]);$N=array($Aa?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($zl,true),$l);$J=driver()->select($a,$N,$Z,$N);if($J)echo
first($J->fetch_row());}exit;}$zi=$Al=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$zi=array_flip($u["columns"]);$Al=($N?$zi:array());foreach($Al
as$w=>$X){if(in_array(idf_escape($w),$N))unset($Al[$w]);}break;}}if($hh&&!$zi){$zi=$Al=array($hh=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($hh));}if($_POST&&!$j){$gm=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ib=array();foreach($_POST["check"]as$fb)$ib[]=where_check($fb,$l);$gm[]="((".implode(") OR (",$ib)."))";}$im=$gm;$gm=($gm?"\nWHERE ".implode(" AND ",$gm):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$zj=($N?:array("*"));$Ib=convert_fields($d,$l,$N);if($Ib)$zj[]=substr($Ib,2);$H="";if(is_array($_POST["check"])&&!$zi){$Qd=implode(", ",$zj)."\nFROM ".table($a);$ce=($q&&$if?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):"");$wl=array();foreach($_POST["check"]as$X)$wl[]="(SELECT".limit($Qd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l).$ce,1).")";$H=implode(" UNION ALL ",$wl);}adminer()->dumpData($a,"table",$H,$zj,$im,($if?$q:array()),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Ld)){if($_POST["save"]||$_POST["delete"]){$I=true;$na=0;$Pa=false;$P=array();if(!$_POST["delete"]){foreach($l
as$B=>$X){$t=bracket_escape($B);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($l[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$P[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}}if($_POST["delete"]||$P){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($P)).")\nSELECT ".implode(", ",$P)."\nFROM ".table($a):"");if($_POST["all"]||($zi&&is_array($_POST["check"]))||$if){$I=($_POST["delete"]?driver()->delete($a,$gm):($_POST["clone"]?queries("INSERT $H$gm".driver()->insertReturning($a)):driver()->update($a,$P,$gm)));$na=connection()->affected_rows;if(is_object($I))$na+=$I->num_rows;}else{$Pa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$fm="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l);$I=($_POST["delete"]?driver()->delete($a,$fm,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$fm)):driver()->update($a,$P,$fm,1)));if(!$I)break;$na+=connection()->affected_rows;}if($Pa&&$I&&!driver()->commit())$I=false;}}$og=lang(172,$na);if($_POST["clone"]&&$I&&$na==1){$Cf=last_id($I);if($Cf)$og=lang(194," $Cf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$og,$I);if($Pa)driver()->rollback();if(!$_POST["delete"]){$qi=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$qi),$qi,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$na=0;$Pa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$zl=>$K){$P=array();foreach($K
as$w=>$X){$w=bracket_escape($w,true);$P[idf_escape($w)]=(preg_match('~char|text~',$l[$w]["type"])||$X!=""?adminer()->processInput($l[$w],$X):"NULL");}$I=driver()->update($a,$P," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($zl,true),$l),($if||$zi?0:1)," ");if(!$I)break;$na+=connection()->affected_rows;}if($Pa)$I=$I&&driver()->commit();queries_redirect(remove_from_uri(),lang(172,$na),$I);if($Pa)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$zd=get_file("csv_file",true);if(!is_string($zd))$j=upload_error($zd);elseif(!preg_match('~~u',$zd))$j=lang(269);else{$tb=array_keys($l);$Dj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Sb=parse_csv($zd,$Dj);$na=count($Sb);driver()->begin();$L=array();foreach($Sb
as$w=>$Vl){if(!$w&&!array_diff($Vl,$tb)){$tb=$Vl;$na--;}else{$P=array();foreach($Vl
as$r=>$pb)$P[idf_escape($tb[$r])]=($pb==""&&$l[$tb[$r]]["null"]?"NULL":q(csv_value($pb)));$L[]=$P;}}$I=(!$L||driver()->insertUpdate($a,$L,$zi));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(270,$na),$I);driver()->rollback();}}}}$Ak=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(53).": $Ak",$j,array(),"",(!$l&&support("table")),($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($S)))):""));$P=null;if(isset($fj["insert"])||!support("table")){$P="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$A)?$A[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$P
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$P);if(!$d&&support("table"))echo"<p class='error'>".lang(271)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($N,$d);adminer()->selectSearchPrint($Z,$wj,$v,$S);adminer()->selectOrderPrint($D,$yh,$v);adminer()->selectLimitPrint($y);if($Sk!==null)adminer()->selectLengthPrint($Sk);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(115).' '.lang(116)."\n";page_footer();exit;}}$E=$_GET["page"];$Od=null;if($E=="last"){$Od=get_val(count_rows($a,$Z,$if,$q));$E=floor(max(0,intval($Od)-1)/$y);}$yj=$N;$be=$q;if(!$yj){$yj[]="*";$Ib=convert_fields($d,$l,$N);if($Ib)$yj[]=substr($Ib,2);}foreach($N
as$w=>$X){$k=$l[idf_unescape($X)];if($k&&($Aa=convert_field($k)))$yj[$w]="$Aa AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$X){if(isset($yj[$w])&&$X["fun"])$yj[$w].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$if&&$Al){foreach($Al
as$w=>$X){$yj[]=idf_escape($w);if($be)$be[]=idf_escape($w);}}$I=driver()->select($a,$yj,$Z,$be,$D,$y,$E,true);if(!is_object($I))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$E)$I->seek($y*$E);$Qc=array();$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$me=($y&&(support("cursor")?$_GET["next"]!="":count($L)>=$y));if(is_ajax()&&$me)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$L){$fg=max_input_vars(count($L[0])+1,20);echo($fg&&count($L)>$fg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($Gl).">\n";if($_GET["page"]!="last"&&$y&&$q&&$if&&JUSH=="sql")$Od=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".lang(16)."\n";else{$La=adminer()->backwardKeys($a,$Ak);$cj=array();reset($N);foreach($L[0]as$w=>$X){if(!isset($Al[$w])){$X=idx($_GET["columns"],key($N))?:array();$cj[$w]=array("fun"=>$X["fun"],"col"=>($N?$X["col"]:$w));next($N);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$q&&$N?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(272)."'".on('click','formCheck','^check').">");$Mg=array();$Li=1;foreach($cj
as$w=>$X){$k=$l[$X["col"]];$B=($k?adminer()->fieldName($k,$Li):($X["fun"]?"*":h($w)));if($B!=""){$Li++;$Mg[$w]=$B;$c=idf_escape($w);$Ae=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$mc="&desc[0]=1";$Uj=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$Wj=($Uj==$c||$Uj==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($Wj?" aria-sort='".($Uj==$D[0]?"ascending":"descending")."'":"").">";$Ud=apply_sql_function(h($X["fun"]),$B);$Vj=isset($k["privileges"]["order"])||$X["fun"];echo($Vj?"<a href='".h($Ae.($Wj&&$Uj==$D[0]?$mc:''))."'>$Ud</a>":$Ud);$ng=($Vj?"<a href='".h($Ae.$mc)."' title='".lang(59)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($k["privileges"]["where"]))$ng
.="<a href='#fieldset-search' title='".lang(56)."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($ng?"<span class='column'>$ng</span>":"");}}$Jf=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$w=>$X)$Jf[$w]=max($Jf[$w],min(40,utf8_length((string)$X)));}}$we=array();$ve=array();foreach((array)$_GET["where"]as$X){$X+=array("col"=>"","op"=>"","val"=>"");$pb=$X["col"];$vj=$X["val"];if(!is_array($vj)&&($vj!=""||preg_match('~NULL$~',$X["op"]))&&(!$X["op"]||in_array($X["op"],adminer()->operators($S)))){$Lf=strtr(preg_quote($vj),array("%"=>".*?","_"=>"."));$fi=array("LIKE %%"=>$Lf,"ILIKE %%"=>$Lf,"REGEXP"=>$vj)+(JUSH=="pgsql"?array("~"=>$vj,"~*"=>$vj):array())+($pb!=""?array():array("="=>'^'.preg_quote($vj).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$vj)))).')\z',"LIKE"=>"^$Lf\\z","ILIKE"=>"^$Lf\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($vj).'(?=,|\z)',));foreach(($pb!=""?array($pb=>$l[$pb]):$l)as$B=>$k){if($pb!=""||is_searchable($k,$X)){$ph=$X["op"]?:(!preg_match('~'.text_type().'~',$k["type"])?"IN":(preg_match('~%~',$vj)?"LIKE":"LIKE %%"));if(isset($fi[$ph])){$lb=preg_match('~^ILIKE|\*$~',$ph)||($ph!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$we[$B][]="(?".($lb?"i":"").":$fi[$ph])";}elseif($ph=="IS NULL"&&$pb=="")$ve[$B]=true;}}}}echo($La?"<th>".lang(273):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$Ld)as$Gg=>$K){$yl=unique_array($L[$Gg],$v);if(!$yl){$yl=array();foreach($L[$Gg]as$w=>$X){if(!in_array(idx(idx($cj,$w,array()),"fun"),driver()->grouping))$yl[$w]=$X;}}$zl="";$r=0;foreach($yl
as$w=>$X){$bj=idx($cj,$w,array());$Ud=idx($bj,"fun","");$pb=($Ud?$bj["col"]:$w);$k=(array)$l[$pb];$hf=is_blob($k);if(!$Ud&&strlen($X)>64&&driver()->md5(idf_escape($pb),$k)){$Ud="md5";$X=md5($hf?(string)driver()->value($X,$k):$X);}if($Ud){$zl
.="&fun[$r]=".url_escape($Ud)."&col[$r]=".url_escape($pb).($X!==null?"&val[$r]=".url_escape($X===false?"f":$X):"");$r++;}else$zl
.="&".($X!==null?"where[".url_escape(bracket_escape($pb))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($pb));}echo"<tr>".(!$q&&$N?"":"<td class='hover check sticky'>".($if||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$zl)."' class='edit'>".lang(274)."</a> ").checkbox("check[]",substr($zl,1),in_array(substr($zl,1),(array)$_POST["check"])));foreach($K
as$w=>$X){if(isset($Mg[$w])){$Ud=$cj[$w]["fun"];$pb=$cj[$w]["col"];$k=(array)$l[$w];if($X!=""&&(!isset($Qc[$w])||$Qc[$w]!=""))$Qc[$w]=(is_mail($X)?$Mg[$w]:"");$z="";if(is_blob($k)&&$X!="")$z=ME.'download='.url_escape($a).'&field='.url_escape($w).$zl;if(!$z&&$X!==null){foreach((array)$Ld[$w]as$n){if(count($Ld[$w])==1||end($n["source"])==$w){$z="";foreach($n["source"]as$r=>$Xj)$z
.=where_link($r,$n["target"][$r],$L[$Gg][$Xj]);$z=($n["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($n["db"]),ME):ME).'select='.url_escape($n["table"]).$z;if($n["ns"])$z=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($n["ns"]),$z);if(count($n["source"])==1)break;}}}if($Ud=="count"&&$pb==""){$z=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$yl))$z
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($yl
as$rf=>$W){if(idx(idx($cj,$rf,array()),"fun")){$z="";break;}$z
.=where_link($r++,$rf,$W);}}$Be=select_value($X,$z,$k,$Sk,($Ud?array():idx($we,$w,array())));if($X===null&&!$Ud&&isset($ve[$w]))$Be="<mark>$Be</mark>";$t=bracket_escape($zl);$s=h("val[$t][".bracket_escape($w)."]");$si=idx(idx($_POST["val"],$t),bracket_escape($w));$Dl=idx($k["privileges"],"update")&&!is_identity_always($k);$Mc=!is_array($K[$w])&&!is_blob($k)&&is_utf8($X)&&$L[$Gg][$w]==$X&&!$Ud&&!$k["generated"]&&$Dl;$U=($Ud=="min"||$Ud=="max"?$l[$pb]["type"]:$k["type"]);$Rk=preg_match('~text|json|lob~',$U);$jf=preg_match(number_type(),$U)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Ud);echo"<td id='$s'".($jf&&($X===null||is_numeric(strip_tags($Be))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Mc&&$X!==null)||$si!==null){$he=h($si!==null?$si:$X);echo">".($Rk?"<textarea name='$s' cols='30' rows='".(substr_count($X,"\n")+1)."'>$he</textarea>":"<input name='$s' value='$he' size='$Jf[$w]'>");}else{$Tf=strpos($Be,"<i>…</i>");echo($Dl?" data-text='".($Tf?2:($Rk?1:0))."'".($Mc?"":" data-warning='".lang(275)."'"):"").">$Be";}}}if($La)echo"<td>";adminer()->backwardKeysPrint($La,$L[$Gg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$ma=get_settings("adminer_import");if($L||$E||$me){$gd=true;if($_GET["page"]!="last"){if(!$y||(count($L)<$y&&($L||!$E)))$Od=($E?$E*$y:0)+count($L);elseif(JUSH!="sql"||!$if){$Od=($if?null:found_rows($S,$Z));$gd=!driver()->hasEstimatedRows();if($Od===null||(!$gd&&$Od<max(1e4,2*($E+1)*$y))){$Od=first(slow_query(count_rows($a,$Z,$if,$q)));$gd=true;}}}if(!support("cursor"))$me=(($Od===false?count($L)+1:$Od-$E*$y)>$y);$Lh=($y&&($me||$E));if($Lh)echo($me?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(276)).'>'.lang(277).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Lh){$dg=($Od===false?$E+($L?(count($L)>=$y?2:1):0):floor(($Od-1)/$y));echo"<fieldset><legend>".lang(278)."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($dg,$E+5);$r++)echo
pagination($r,$E);if($dg>0)echo($E+5<$dg?" …":""),($gd&&$Od!==false?pagination($dg,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$dg'>".lang(279)."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($me?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(280)."</legend>";$uc=($gd?"":"~ ").$Od;$wf=($Od!==false?($gd?"":"~ ").lang(176,$Od):"");echo
checkbox("all",1,0,$wf,on('click','countRows',$uc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(177)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(281),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(144),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(266),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$Md=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($Md['sql']);break;}}if($Md){print_fieldset("export",lang(76)." <span id='selected2'></span>");$Jh=adminer()->dumpOutput();echo($Jh?html_select("output",$Jh,$ma["output"])." ":""),html_select("format",$Md,$ma["format"])," <input type='submit' name='export' value='".lang(76)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Qc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(75)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Gl?input_hidden(ini_get("session.upload_progress.name"),$Gl):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ma["format"])." <input type='submit' name='import' value='".lang(75)."'>".($Gl?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$q&&$N?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$jk=isset($_GET["status"]);page_header($jk?lang(136):lang(135));$Wl=($jk?adminer()->showStatus():adminer()->showVariables());if(!$Wl)echo"<p class='message'>".lang(16)."\n";else{echo"<table>\n";foreach($Wl
as$K){echo"<tr>";$w=array_shift($K);echo"<th><code class='jush-".JUSH.($jk?"status":"set")."'>".h($w)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$sk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$S){json_row("Comment-$B",h($S["Comment"]).($S["Error"]?" <span class='error'>".h($S["Error"])."</span>":""));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$B",h($S[$w]));foreach(array_keys($sk+array("Auto_increment"=>0,"Rows"=>0))as$w){if(array_key_exists($w,$S))json_row("$w-$B",format_status($S,$w));if($S[$w]!=""&&isset($sk[$w]))$sk[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}}}if(function_exists('Adminer\db_status'))$sk=db_status();foreach($sk
as$w=>$X)json_row("sum-$w",format_number($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$kg=ME.(isset($_GET["select"])?"select=&":"");$Jk=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Jk&&!$j&&!$_POST["search"]){$I=true;$og="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$og=lang(282);}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$og=lang(283);}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$og=lang(284);}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$og=lang(285);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$og
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("DBCC CHECKTABLE (".q(table($R)).") WITH TABLERESULTS")as$K)$og
.="<b>".h($R)."</b>: ".h($K["MessageText"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$og=lang(286);}elseif(!$_POST["tables"])$og=lang(13);elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$og
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(relative_uri(),$og,$I);}page_header(($_GET["ns"]==""?lang(35).": ".h(DB):lang(168).": ".h($_GET["ns"])),$j,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$Rd=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(287)."</h3>\n";$Ik=($Rd?table_status():tables_list());if(!$Ik)echo"<p class='message'>".lang(13)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(288)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(56)."'>\n","</div></fieldset>\n";if(!$j&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(170).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($kg,0,-1)).'">'.lang(150).'</a>';$d=array("Engine"=>array(lang(289).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$d["Collation"]=array(lang(140).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$d["Data_length"]=array(lang(290).doc_link(array('sql'=>'show-table-status.html',)),"create",lang(44),);if(support("indexes"))$d["Index_length"]=array(lang(291).doc_link(array('sql'=>'show-table-status.html',)),"indexes",lang(153),);$d["Data_free"]=array(lang(292).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(45));if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array(lang(51).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(44),);$d["Rows"]=array(lang(293).doc_link(array('sql'=>'show-table-status.html',)),"select",lang(41),);if(support("comment"))$d["Comment"]=array(lang(50).doc_link(array('sql'=>'show-table-status.html',)),);$Ba=array('Engine','Collation','Comment');foreach($d
as$w=>$c)echo"<th".($D==$w?" aria-sort='".(in_array($w,$Ba)?"ascending":"descending")."'":"")."><a href='".h($kg)."order=$w'>$c[0]</a>";echo"<tbody>\n";if($D){uasort($Ik,function($ga,$Ia)use($D,$Ba){$J=($ga[$D]<$Ia[$D]?-1:($ga[$D]>$Ia[$D]?1:0));return(in_array($D,$Ba)?$J:-$J);});}$T=0;$sk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($Ik
as$B=>$jk){$Zl=($Rd?is_view($jk):$jk!==null&&!preg_match('~table|sequence~i',$jk));$jk=($Rd?$jk:array('Engine'=>$jk));$s=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($Zl?"views[]":"tables[]"),$B,in_array("$B",$Jk,true),"","","",$s),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".lang(42)."' id='$s'>".h($B).'</a>':h($B));if($Zl&&!preg_match('~materialized~i',$jk['Engine'])){$Xk=lang(149);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".lang(43)."'>$Xk</a>":$Xk),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".lang(41)."'>?</a>";if(support("comment"))echo'<td>'.h($jk['Comment']);}else{if($Rd){foreach(array_keys($sk)as$w)$sk[$w]+=($jk["Engine"]!="InnoDB"||$w!="Data_free"?idx($jk,$w):0);}foreach($d
as$w=>$c){$s=" id='$w-".h($B)."'";echo($c[1]?"<td align='right'><a href='".h(ME."$c[1]=").url_escape($B)."'$s title='$c[2]'>".format_status($jk,$w)."</a>":"<td$s>".h(idx($jk,$w,'?')).($w=="Comment"&&$jk["Error"]?" <span class='error'>".h($jk["Error"])."</span>":""));}$T++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(267,count($Ik)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Rd&&function_exists('Adminer\db_status'))$sk=db_status();foreach($sk
as$w=>$rk)echo($d[$w]?"<td align='right' id='sum-$w'>".($Rd?format_number($rk):""):"");echo"\n","</table>\n",($Rd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Sl="<input type='submit' value='".lang(294)."'".on_help("VACUUM")."> ";$uh="<input type='submit' name='optimize' value='".lang(295)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$_i=(JUSH=="sqlite"?$Sl."<input type='submit' name='check' value='".lang(296)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$Sl.$uh:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(296)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(297)."'".on_help("ANALYZE TABLE")."> ".$uh."<input type='submit' name='check' value='".lang(296)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(298)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(299)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(145)."'".confirm().on_help("DROP TABLE").">":"");echo($_i?"<div class='footer'><div>\n<fieldset><legend>".lang(144)." <span id='selected'></span></legend><div>$_i\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(300)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(128)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(23)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(301)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(77)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(231)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(72)."</h3>\n";$nj=routines();if($nj){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(211).'<th>'.lang(49).'<th>'.lang(247)."<td class='hover'><tbody>\n";foreach($nj
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$B).'" title="'.lang(218).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$B).'">'.lang(156)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(246).'</a>':'').'<a href="'.h(ME).'function=">'.lang(245)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(74)."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".lang(211)."<th>".lang(302)."<th>".lang(237)."<th>".lang(238)."<td class='hover'><tbody>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?lang(303)."<td>".h($K["Execute at"]):lang(239)." ".h($K["Interval value"])." ".h($K["Interval field"])."<td>".h($K["Starts"])),"<td>".h($K["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($K["Name"]).'">'.lang(156).'</a>';echo"</table>\n";$dd=get_val("SELECT @@event_scheduler");if($dd&&$dd!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($dd)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(236)."</a>\n","</div>\n";}}}}page_footer();