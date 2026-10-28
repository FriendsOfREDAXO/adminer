<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.2.0
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.2.0";error_reporting(24575);set_error_handler(function($cd,$fd){return!!preg_match('~^Undefined (array key|offset|index)~',$fd);},E_WARNING|E_NOTICE);$Hd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Hd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Ol=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Ol)$$X=$Ol;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Pb=adminer()->credentials();$J=Driver::connect($Pb[0],$Pb[1],$Pb[2]);return(is_object($J)?$J:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$Ff=substr($t,-1);return
str_replace($Ff.$Ff,$Ff,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($Aa,$w,$i=null){return($Aa&&array_key_exists($w,$Aa)?$Aa[$w]:$i);}function
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
true;$U=$k["type"];$Hj=$X["val"];$Ra='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Ra~",$U))return
false;if(preg_match(number_type(),$U)){$gh='-?\d+(\.\d+)?';$Ig='';if(preg_match('~IN$~',$X["op"])){$Hj=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$Hj);$Ig="( *, *$gh)*";}elseif($X["op"]=="BETWEEN")$Ig="\\s+AND\\s+$gh";return(bool)preg_match('~^'.$gh.$Ig.'$~i',$Hj);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$Hj);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$Hj);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Hj);return
true;}function
remove_slashes(array$km,$Hd=false){$J=array();foreach($km
as$w=>$X)$J[stripslashes($w)]=(is_array($X)?remove_slashes($X,$Hd):($Hd?$X:stripslashes($X)));return$J;}function
bracket_escape($t,$Ka=false){static$wl=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Ka?array_flip($wl):$wl));}function
url_escape($Q){static$wl=array();if(!$wl){$wl=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$db)$wl[$db]=sprintf('%%%02X',ord($db));for($r=0;$r<256;$r++){if($r<32||$r>126)$wl[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$Q,$wl);}function
min_version($nm,$ag="",$f=null){$f=connection($f);$Wj=$f->server_info;if($ag&&preg_match('~([\d.]+)-MariaDB~',$Wj,$A)){$Wj=$A[1];$nm=$ag;}return$nm&&version_compare($Wj,$nm)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($Bh,$Y){return(function_exists('ini_set')?\ini_set($Bh,$Y):false);}function
ini_bool($Ze){$X=ini_get($Ze);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Ze){$X=ini_get($Ze);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$Ph){$dg=(int)ini_get("max_input_vars");return($dg?(int)floor(($dg-$Ph)/$K):0);}function
max_input_vars_error(){$Ze="max_input_vars";return
lang(1,"<b>$Ze = ".ini_get($Ze)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($mm,$O,$V,$F){$_SESSION["pwds"][$mm][$O][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$k=0,$Bb=null){$Bb=connection($Bb);$I=$Bb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$k]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$Zj=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($Zj)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$j="<p class='error'>"){$Bb=connection($f);$J=array();$I=$Bb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$J;}function
unique_array($K,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]&&!$u["invalid"]){$J=array();foreach($u["columns"]as$w){if(!isset($K[$w]))continue
2;$J[$w]=$K[$w];}return$J;}}}function
where_function($Zd,$c,array$k){if($Zd=="md5")return
driver()->md5($c,$k)?:$c;return(in_array($Zd,driver()->functions)||in_array($Zd,driver()->grouping)?apply_sql_function($Zd,$c):$c);}function
where(array$Z,array$l=array()){$J=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$c=idf_escape($w);$k=idx($l,$w,array());$Bd=$k["type"];$mf=$k&&(is_blob($k)||preg_match('~binary~',$Bd));$J[]=$c.($mf&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$Bd=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="pgsql"&&preg_match('~\.~',$Bd)?"::text = ".q($X):(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Bd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($k,q($X))))))));if(JUSH=="sql"&&preg_match('~char|text~',$Bd)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$J[]=idf_escape($w)." IS NULL";foreach((array)$Z["col"]as$r=>$qb){$X=idx($Z["val"],$r);$J[]=where_function(idx($Z["fun"],$r),idf_escape($qb),idx($l,$qb,array())).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$J);}function
where_columns(array$l){$J=array();foreach((array)$_GET["null"]as$w)$J[$w]=true;foreach(array_keys((array)$_GET["where"])as$w)$J[bracket_escape($w,true)]=true;foreach((array)$_GET["col"]as$qb)$J[$qb]=true;return
array_intersect_key($J,$l);}function
where_check($X,array$l=array()){parse_str($X,$gb);remove_slashes(array(&$gb));return
where($gb,$l);}function
where_link($r,$c,$Y,$zh="="){$wh=($Y!==null?$zh:"IS NULL");return"&where[$r][col]=".url_escape($c).($wh!=first(adminer()->operators())?"&where[$r][op]=".url_escape($wh):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$d,array$l,array$N=array()){$J="";foreach($d
as$w=>$X){if($N&&!in_array(idf_escape($w),$N))continue;$Ba=convert_field($l[$w]);if($Ba)$J
.=", $Ba AS ".idf_escape($w);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($B,$Y,$Pf=2592000){header("Set-Cookie: $B=".rawurlencode($Y).($Pf?"; expires=".gmdate("D, d M Y H:i:s",time()+$Pf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
php_error_text($j){return(ini_bool("html_errors")?html_entity_decode(strip_tags($j)):$j);}function
get_url($Xl,$Gb){$http_response_header=null;$ed=array();set_error_handler(function($cd,$j)use(&$ed){$ed[]=preg_replace('~^file_get_contents\([^)]*\)( \[[^]]*\])?:\s*~','',php_error_text($j));return
true;});$J=file_get_contents($Xl,false,$Gb);restore_error_handler();$ve=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($ve,0,''),$A)?$A[1]:''),(array)$ve,($J===false?implode("\n",$ed):''),);}function
json_decode_exact($uf){$uf=preg_replace('~"(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$uf);return
json_decode(preg_replace('~"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$uf));}function
json_scalar($X){return(is_string($X)&&substr($X,0,1)=="\1"?substr($X,1):$X);}function
json_encode_exact($X,$Kd=0){return
preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001(?:[^"\\\\]|\\\\.)*+")|"(?:[^"\\\\]|\\\\.)*+"(*SKIP)(*FAIL)~','$1$2$3',json_encode($X,$Kd));}function
get_settings($Jb){parse_str($_COOKIE[$Jb],$ak);return$ak;}function
get_setting($w,$Jb="adminer_settings",$i=null){return
idx(get_settings($Jb),$w,$i);}function
save_settings(array$ak,$Jb="adminer_settings"){$Y=http_build_query($ak+get_settings($Jb));cookie($Jb,$Y);$_COOKIE[$Jb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Nd=false){$am=ini_bool("session.use_cookies");if(!$am||$Nd){session_write_close();if($am&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($mm,$O,$V,$h=null){$Wl=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($mm=='mssql'||$mm=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Wl,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($mm!="server"||$O!=""?url_escape($mm)."=".url_escape($O)."&":"")."username=".url_escape($V).($h!=""?"&db=".url_escape($h):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_,$tg=null){if($tg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_!==null?$_:$_SERVER["REQUEST_URI"]))][]=$tg;}if($_!==null){if($_=="")$_=".";header("Location: $_");exit;}}function
query_redirect($H,$_,$tg,$Zi=true,$md=true,$xd=false,$jl=""){if($md){$tk=microtime(true);$xd=!connection()->query($H);$jl=format_time($tk);}$nk=($H?adminer()->messageQuery($H,$jl,$xd):"");if($xd){adminer()->error
.=adminer()->error().$nk.script("messagesPrint();")."<br>";return
false;}if($Zi)redirect($_,$tg.$nk);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");}function
queries($H){remember_query($H);return
connection()->query($H);}function
apply_queries($H,array$T,$gd='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$gd($R)))return
false;}return
true;}function
queries_redirect($_,$tg,$Zi){$Ri=implode("\n",Queries::$queries);$jl=format_time(Queries::$start);return
query_redirect($Ri,$_,$tg,$Zi,false,!$Zi,$jl);}function
format_time($tk){return
lang(2,max(0,microtime(true)-$tk));}function
relative_uri($Wl=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Wl?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Uh=""){return
substr(preg_replace("~(?<=[?&])($Uh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$ec=false){$Dd=$_FILES[$B];if(!$Dd)return
null;foreach($Dd
as$w=>$X)$Dd[$w]=(array)$X;$J=array();foreach($Dd["error"]as$w=>$j){if($j)return$j;$m=$Dd["name"][$w];$rl=$Dd["tmp_name"][$w];$Eb=file_get_contents($ec&&preg_match('~\.gz$~',$m)?"compress.zlib://$rl":$rl);if($ec){$tk=substr($Eb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$tk))$Eb=iconv("utf-16","utf-8",$Eb);elseif($tk=="\xEF\xBB\xBF")$Eb=substr($Eb,3);}$J[]=array($m,$Eb);}return$J;}function
get_file($w,$ec=false,$mc=""){$Gd=get_files($w,$ec);if(!is_array($Gd))return$Gd;$J='';foreach($Gd
as$Dd){$Eb=$Dd[1];$J
.=$Eb;if($mc)$J
.=(preg_match("($mc\\s*\$)",$Eb)?"":$mc)."\n\n";}return$J;}function
upload_error($j){$lg=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(3).($lg?" ".lang(4,$lg):""):lang(5));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$A);$ek=strlen($A[3]);$J=number_format($X,0,".","");$J=preg_replace('~\B(?=(\d{'.(strlen($A[2])?:$ek).'})*\d{'.$ek.'}$)~',$A[1],$J);return
strtr($J,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$w){$X=idx($S,$w,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$ya=($w=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$X=in_array($w,array("Data_length","Index_length","Data_free"))?adminer()->formatSizeValue($X):format_number($X);return($ya?"~ ":"").$X;}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$yd=false){$J=table_status($R,$yd);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$n){foreach($n["source"]as$X)$J[$X][]=$n;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$B=bracket_escape($w,true);$J[$B]=array("field"=>$B,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($B==driver()->primary),);}return$J;}function
dump_headers($Ie,$Lg=false){$J=adminer()->dumpHeaders($Ie,$Lg);$Rh=$_POST["output"];if($Rh!="text"||$J=="tar"){$_b=($Rh!="text"&&$Rh!="file"&&preg_match('~^[0-9a-z]+$~',$Rh)?".$Rh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Ie).".$J$_b");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$El=$_POST["format"]=="tsv";foreach($K
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($El?'\t':'[,;]|^$').'~',$X))$K[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($El?"\t":";")),$K)."\r\n";}function
parse_csv($Sb,$Qj){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Sb,$bg);foreach($bg[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$Qj]*)$Qj~",$K.$Qj,$cg);$J[]=$cg[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?idf_unescape($X):$X);}function
apply_sql_function($p,$c){return($p?($p=="unixepoch"?"DATETIME($c, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$o=@fopen($m,"c+");if(!$o)return;@chmod($m,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Wb){rewind($o);fwrite($o,$Wb);ftruncate($o,strlen($Wb));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$Aa){return
reset($Aa);}function
password_file($Mb){$m=get_temp_dir()."/adminer.key";if(!$Mb&&!file_exists($m))return'';$o=file_open_lock($m);if(!$o)return'';$J=stream_get_contents($o);if(!$J){$J=rand_string();file_write_unlock($o,$J);}else
file_unlock($o);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$z,array$k,$hl,array$oi=array()){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$yf=array();foreach($X
as$W)$yf+=array_fill_keys(array_keys($W),null);foreach(array_keys($yf)as$wf)$J
.="<th>".h($wf);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($yf,$W)as$gm)$J
.="<td>".select_value($gm,$z,$k,$hl,$oi);}}else{foreach($X
as$wf=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($wf):"")."<td>".select_value($W,$z,$k,$hl,$oi);}return"<table>$J</table>";}if(!$z)$z=adminer()->selectLink($X,$k);if($z===null){if(is_mail($X))$z="mailto:$X";if(is_url($X))$z=$X;}$X=driver()->value($X,$k);$J=adminer()->editVal($X,$k);if($J!==null){if(!is_utf8($J))$J="\0";elseif($hl!=""&&is_shortable($k))$J=shorten_utf8($J,max(0,+$hl),"",$oi);else$J=highlight_matches($J,$oi);}return
adminer()->selectVal($J,$z,$k,$X);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(0),array()));}function
is_identity_always(array$k){return$k["auto_increment"]&&(JUSH=="mssql"||$k["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($Rc){$Da='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Dc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$ni="$Da+(\\.$Da+)*@($Dc?\\.)+$Dc";return
is_string($Rc)&&preg_match("(^$ni(,\\s*$ni)*\$)i",$Rc);}function
is_url($Q){$Dc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Dc?\\.)+$Dc(:\\d+)?(/\\S*)?(\\?\\S*)?(#\\S*)?\$~iD",$Q);}function
is_ipv6($ka){$q='[\da-f]{1,4}';$lf='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($q:){7}$q|($q:){6}$lf|(($q:)*$q)?::(($q:)*($q|$lf))?)$~iD",$ka);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($Ee){return(strpos($Ee,":")!==false?"[$Ee]":$Ee);}function
server_parts(array$hi){return
array("scheme"=>(string)$hi["scheme"],"host"=>(string)$hi["host"],"port"=>(string)$hi["port"],"socket"=>(string)$hi["socket"],"path"=>(string)$hi["path"],);}function
parse_server($O){if($O=="")return
server_parts(array());if($O[0]==":"&&!is_ipv6($O)){$mj=substr($O,1);if(preg_match('~^\d+$~D',$mj))return
server_parts(array("port"=>$mj));return(preg_match('~^/[-\w.:/]*$~D',$mj)?server_parts(array("socket"=>$mj)):null);}$Fj="";if(preg_match('~^([-+.\w]+)://~',$O,$A)){$Fj=strtolower($A[1]);$O=substr($O,strlen($A[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$O,$A))return(is_ipv6($A[1])?server_parts(array("scheme"=>$Fj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);if(is_ipv6($O))return
server_parts(array("scheme"=>$Fj,"host"=>$O));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$O,$A))return
server_parts(array("scheme"=>$Fj,"host"=>$A[1],"port"=>$A[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$O,$A)?server_parts(array("scheme"=>$Fj,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);}function
count_rows($R,array$Z,$nf,array$q){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($nf&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$H":"SELECT COUNT(*)".($nf?" FROM (SELECT 1$H GROUP BY ".implode(", ",$q).") x":$H));}function
slow_query($H){$h=adminer()->database();$kl=adminer()->queryTimeout();$fk=driver()->slowQuery($H,$kl);$f=null;if(!$fk&&support("kill")){$f=connect();if($f&&($h==""||$f->select_db($h))){$zf=number(get_val(connection_id(),0,$f));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$zf&token=".get_token()."'); }, 1000 * $kl);");}}ob_flush();flush();$J=@get_key_vals(($fk?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Wi=rand(1,1e6);return($Wi^$_SESSION["token"]).":$Wi";}function
verify_token(){list($sl,$Wi)=explode(":",$_POST["token"]);return($Wi^$_SESSION["token"])==$sl&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$sc=""){$ua=array_flip(str_split(compress_alphabet()));$x=strlen($Q);$im=($x?13*($x-1)/2-$ua[$Q[0]]:0);$Ra="";$mj=0;$nj=0;for($r=1;$r<$x;$r+=2){$mj=($mj<<13)+$ua[$Q[$r]]*93+$ua[$Q[$r+1]];$nj+=13;while($nj>=8&&$im>=8){$nj-=8;$im-=8;$Ra
.=chr($mj>>$nj);$mj&=(1<<$nj)-1;}}if($Ra=="")return"";if($sc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$sc)),$Ra,ZLIB_FINISH);return($sc==""&&function_exists('gzinflate')?gzinflate($Ra):inflate($Ra,$sc));}function
inflate($Ra,$sc=""){$Mf=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Nf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$wc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$yc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$sc;$G=0;do{$Id=inflate_bits($Ra,$G,1);$U=inflate_bits($Ra,$G,2);if(!$U){$G=($G+7)&~7;$x=inflate_bits($Ra,$G,16);$G+=16;$J
.=substr($Ra,$G>>3,$x);$G+=$x<<3;}else{if($U==1){$Vf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$zc=array_fill(0,30,5);}else{$Uf=inflate_bits($Ra,$G,5)+257;$xc=inflate_bits($Ra,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$_g=array_fill(0,19,0);$zg=inflate_bits($Ra,$G,4)+4;for($r=0;$r<$zg;$r++)$_g[$D[$r]]=inflate_bits($Ra,$G,3);$Ag=inflate_table($_g);$Of=array();while(count($Of)<$Uf+$xc){$Ik=inflate_symbol($Ra,$G,$Ag);if($Ik==16)$Of=array_merge($Of,array_fill(0,inflate_bits($Ra,$G,2)+3,end($Of)));elseif($Ik==17)$Of=array_merge($Of,array_fill(0,inflate_bits($Ra,$G,3)+3,0));elseif($Ik==18)$Of=array_merge($Of,array_fill(0,inflate_bits($Ra,$G,7)+11,0));else$Of[]=$Ik;}$Vf=array_slice($Of,0,$Uf);$zc=array_slice($Of,$Uf);}$Wf=inflate_table($Vf);$Ac=inflate_table($zc);while(($Ik=inflate_symbol($Ra,$G,$Wf))!=256){if($Ik<256)$J
.=chr($Ik);else{$x=$Mf[$Ik-257]+inflate_bits($Ra,$G,$Nf[$Ik-257]);$_c=inflate_symbol($Ra,$G,$Ac);$nh=strlen($J)-$wc[$_c]-inflate_bits($Ra,$G,$yc[$_c]);for($r=0;$r<$x;$r++)$J
.=$J[$nh+$r];}}}}while(!$Id);return($sc==""?$J:substr($J,strlen($sc)));}function
inflate_bits($Ra,&$G,$Lb){$J=0;for($r=0;$r<$Lb;$r++){$J+=((ord($Ra[$G>>3])>>($G&7))&1)<<$r;$G++;}return$J;}function
inflate_table(array$Of){$R=array();$pb=0;for($Sa=1;$Sa<=max($Of);$Sa++){foreach($Of
as$Ik=>$x){if($x==$Sa){$R[$Sa][$pb]=$Ik;$pb++;}}$pb<<=1;}return$R;}function
inflate_symbol($Ra,&$G,array$R){$pb=0;$Sa=0;do{$pb=($pb<<1)+inflate_bits($Ra,$G,1);$Sa++;}while(!isset($R[$Sa][$pb]));return$R[$Sa][$pb];}function
script($kk,$vl="\n"){return"<script".nonce().">$kk</script>$vl";}function
script_src($Xl,$ic=false){return"<script src='".h($Xl)."'".nonce().($ic?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($hd,$ne,$za=null){$_a=array();foreach(array_slice(func_get_args(),2)as$X)$_a[]=json_encode($X,256);return" data-on$hd='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$ne(".implode(", ",$_a).")")."'";}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$ib,$Af="",$b="",$ob="",$Cf=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($ib?" checked":"").($Af==""&&$ob?" class='$ob'":"").($Cf?" aria-labelledby='$Cf'":"").$b.">";return($Af!=""?"<label".($ob?" class='$ob'":"").">$J".h($Af)."</label>":$J);}function
optionlist($C,$Nj=null,$bm=false){$J="";foreach($C
as$wf=>$W){$Eh=array($wf=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($wf).'">';$Eh=$W;}foreach($Eh
as$w=>$X)$J
.='<option'.($bm||is_string($w)?' value="'.h($w).'"':'').($Nj!==null&&($bm||is_string($w)?(string)$w:$X)===$Nj?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
group_system(array$Tg,$Ej=false){$J=array();$Jk=array();foreach($Tg
as$B){if($Ej?driver()->isSystem(DB,$B):driver()->isSystem($B))$Jk[]=$B;else$J[]=$B;}if($Jk)$J[lang(8,'')]=$Jk;return$J;}function
html_select($B,array$C,$Y="",$b="",$Cf=""){static$Af=0;$Bf="";if(!$Cf&&substr($C[""],0,1)=="("){$Af++;$Cf="label-$Af";$Bf="<option value='' id='$Cf'>".h($C[""]);unset($C[""]);}return"<select name='".h($B)."'".($Cf?" aria-labelledby='$Cf'":"")."$b>".$Bf.optionlist($C,$Y)."</select>";}function
html_radios($B,array$C,$Y="",$Qj=""){$J="";foreach($C
as$w=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$Qj";return$J;}function
confirm($tg=""){return
on('click','confirmClick',$tg?:lang(9));}function
print_fieldset($s,$Lf,$qm=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$Lf</a>","</legend>","<div id='fieldset-$s'".($qm?"":" class='hidden'").">\n";}function
bold($Ta,$ob=""){return($Ta?" class='active $ob'":($ob?" class='$ob'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Tb){return" ".($E==$Tb?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$Mi,array$Me=array(),$Di=''){$J=false;foreach($Mi
as$w=>$X){if(!in_array($w,$Me)){if(is_array($X))hidden_fields($X,array(),$w);else{$J=true;echo
input_hidden(($Di?$Di."[$w]":$w),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Vl){$Vl=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Vl?on('submit','uploadProgress',ME."upload=$Vl",SESSION_NAME."=$Vl"):"");}function
file_input($b,$mj=""){$fg="max_file_uploads";$gg=ini_get($fg);$lg="upload_max_filesize";$mg=ini_bytes($lg);$_i=ini_bytes("post_max_size");if($_i&&$_i<$mg){$lg="post_max_size";$mg=$_i;}$ng=ini_get($lg);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$gg,lang(10,"$fg = $gg"),$mg,lang(10,"$lg = $ng")).">$mj":lang(11));}function
enum_input($U,$b,array$k,$Y,$Uc=""){preg_match_all("~".driver()->enumLength."~",$k["length"],$bg);$Di=($k["type"]=="enum"?"val-":"");$ib=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($k["null"]&&$Di?"<label><input type='$U'$b value='null'".($ib?" checked":"")."><i>$Uc</i></label>":"");foreach($bg[0]as$X){$X=stripcslashes(idf_unescape($X));$ib=(is_array($Y)?in_array($Di.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$b value='".h($Di.$X)."'".($ib?' checked':'').'>'.h(adminer()->editVal($X,$k)).'</label>';}return$J;}function
input(array$k,$Y,$p,$Ia=false,$Sl=false){$B=h(bracket_escape($k["field"]));echo"<td class='function'>";$bd=driver()->enumLength($k);if($bd){$k["type"]="enum";$k["length"]=$bd;}$C=($k["type"]=="enum"||$k["type"]=="set");if(is_array($Y)&&!$p&&!$C)$p="json";$uf=($p=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($uf&&$Y!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=(is_array($Y)?json_encode($Y,128|64|256):json_encode_exact(json_decode_exact($Y),128|64|256));$lj=($Sl&&is_identity_always($k));if($lj&&!$_POST["save"])$p=null;$ae=(isset($_GET["select"])||$lj?array("orig"=>lang(12)):array())+adminer()->editFunctions($k);$b=" name='fields[$B]".($C?"[]":"")."'".($Ia?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$R=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($ae[""])."<td>".adminer()->editInput($R,$k,$b,$Y);else{$pe=(in_array($p,$ae)||isset($ae[$p]));$Jd=0;foreach($ae
as$w=>$X){if($w===""||!$X)break;$Jd++;}echo(count($ae)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($ae,$p===null||$pe?$p:"")."</select>":h(reset($ae)))."<td".($Jd&&count($ae)>1?on('input','skipOriginal',$Jd):"").">";$bf=adminer()->editInput($R,$k,$b,$Y);if($bf!="")echo$bf;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$b,$k,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($uf)echo"<textarea$b cols='50' rows='12' class='jush-json'>\n".h($Y).'</textarea>';elseif(($gl=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$Y)){if($gl&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>\n".h($Y).'</textarea>';}else{$Il=driver()->types();$Gl=$Il[$k["type"]];$Aa=preg_match('~\[]~',$k["full_type"]);if($Aa)$og=0;elseif(preg_match('~date|time|year~',$k["type"])){$Ci=($k["length"]==""&&JUSH=="pgsql"?6:$k["length"]);$Ud=(preg_match('~time~',$k["type"])&&preg_match('~^[1-9]\d*$~',$Ci)?$Ci+1:0);$og=($Gl?$Gl+$Ud:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$A))$og=(preg_match("~binary~",$k["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$k["unsigned"]?1:0);else$og=($Gl?$Gl+($k["unsigned"]?0:1):0);echo"<input".((!$pe||$p==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!$Aa?" type='number'":"")." value='".h($Y)."'".($og?" data-maxlength='$og'":"").(preg_match('~char|binary~',$k["type"])&&$og>20?" size='".($og>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$k,$Y),(count($ae)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$t=bracket_escape($k["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$Dd=get_file("fields-$t");if(!is_string($Dd))return
false;return
driver()->quoteBinary($Dd);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($k["auto_increment"]&&$Y=="")return
null;if($k["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($k,$Y,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Pj="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array(),$S)),1));if(!$I||$I->fetch_row()){$Ii="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$Pj<li>".($I?$Ii:"<p class='error'>$Ii: ".adminer()->error())."\n";$Pj="";}}}echo($Pj?"<p class='message'>".lang(13):"</ul>")."\n";}function
on_help($gl,$dk=0){return
on('mouseover','helpMouseover',$gl,$dk).on('mouseout','helpMouseout');}function
on_help_value($gj="",$kj=""){return
on('mouseover','helpValueMouseover',$gj,$kj).on('mouseout','helpMouseout');}function
edit_form($R,array$l,$K,$Sl,$j='',$H='',$jl=''){$Pk=adminer()->tableName(table_status1($R,true));page_header(($Sl?lang(14):lang(15)),$j,array("select"=>array($R,$Pk)),$Pk);adminer()->editRowPrint($R,$l,$K,$Sl,$H,$jl);if($K===false){echo"<p class='error'>".lang(16)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Pc=false;$wm=($Sl&&!isset($_GET["select"])?where_columns($l):array());$Hb=(count($wm)!=count($l));if(!$Hb)$wm=array();if(!$l)echo"<p class='error'>".lang(17)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ia=!$_POST;foreach($l
as$B=>$k){echo"<tr".($wm[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($B));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$ij))$i=$ij[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$Y=($K!==null?($k["type"]=="set"&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$Sl&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$k);if(($Sl&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($Y,'',$k,null);else{$Pc=true;$p=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($Sl&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Sl&&$Y==$k["default"]&&($k["default_sql"]||preg_match('~^[\w.]+\(~',$Y)))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p=(in_array("now",adminer()->editFunctions($k))?"now":"current_timestamp");}if(preg_match("~date~",$k["type"])&&preg_match('~^CURRENT_DATE~i',$Y)){$Y="";$p=(in_array("now",adminer()->editFunctions($k))?"now":"current_date");}if($k["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Ia!==false)$Ia=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$Y,$p,$Ia,$Sl);if($Ia)$Ia=false;}}if(!fields($R)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Pc){echo"<input type='submit' value='".lang(18)."'>\n";if(!isset($_GET["select"])&&$Hb){$tc=($wm&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Sl?lang(19):lang(20))."' title='Ctrl+Shift+Enter'$tc".($Sl?on('click','ajaxForm',lang(21)):"").">\n";}}echo($Sl?"<input type='submit' name='delete' value='".lang(22)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($ni,$x){return
str_repeat("$ni{0,65535}",$x/65535)."$ni{0,".($x%65535)."}";}function
shorten_utf8($Q,$x=80,$Dk="",array$oi=array()){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$Q,$A);$x=strlen(isset($A[2])?$A[1]:preg_replace('~\n[^\n]*\z~',"\n",$A[1]));return
highlight_matches($Q,$oi,$x).$Dk.(isset($A[2])?"":"<i>…</i>");}function
highlight_matches($Q,array$oi,$x=null){if($x===null)$x=strlen($Q);$J="";$G=0;if($oi&&@preg_match_all("((?|".implode("|",$oi)."))su",$Q,$bg,PREG_OFFSET_CAPTURE)){foreach($bg[0]as$A){list($gl,$tk)=$A;if($gl!=""&&$tk<$x){$Wc=min($tk+strlen($gl),$x);$J
.=h(substr($Q,$G,$tk-$G))."<mark>".h(substr($Q,$tk,$Wc-$tk))."</mark>";$G=$Wc;}}}return$J.h(substr($Q,$G,$x-$G));}function
icon($He,$B,$Ge,$ml,$b=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($ml)."' class='icon icon-$He".($B?"":" jsonly")."'$b><span>$Ge</span></button>";}function
copy_icon(){$Kb=lang(23);return"<a href='' class='jsonly icon-copy' title='$Kb'><span>$Kb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(',c(<UcrZ9FloAdLIHT,e^12P>vJpDt~s)jpQ5n*/jNlpj4]q19=eVp+w8a%oBQUTAw)IeW/u7lvs|iu0YUhhis?by`^2y`w^^.@t-OS(=WOR|CP&H43h,8fIox:9}+Dr41T<m4m%{PjoXQ&uX>x]gL$Cv>O&?K0e2t6KKZc]hR1[7PmhM1DHfTAWT:%[6bp<T/81fB
.r29*O)#Tst!3PTs7R./CjgEHA#(jbnyfb<iJlUvk{igwY622G;UTZa%AjWa=un-JD;P"b3c<*O>GW;|by+@<eF.,aIW0o9w#7A?P6hx<(,;THCs"hN+JLZ8"S8uS$H?@Mn-Ex6P@.*Y"8*e4u2{IWt{GJ`8QH/*!]T>-TFc9G$kOi9`vgYxwn
c%g)X-E
)EzoS79gZ,B^(JY>CV+i%U#DXw(U!jShr7E.,O/H=?y_RTf@C
um+TP6JsRX*6UHOW)XH&`0uU-%[B_5b/PGK%V6F"*VOYlO%r~e%7r4zIiZe)OGZ+6pFLA:V,Br4Lp[>S/$K&z12m)VZY9)vPI5?F%YP7Ogq>&-5];3(c`Imh^fc!C3hse,H43n$0!iXAQqbf`(tm1orwwDZb+`#pZ;K#kLB(y*uQDg@<,=2_=(-jcNo-p6l/mQFggd1JnOB>B-p/=ZD<=<t;t.KXa4sgH)Ltuj>H,".5g`>k8G`jTY]_-$T]X^Ytg4B+4Pe5dn[0Jmv0vEi+(l~M}[
k-y`fw,^#?q2G;jQ#Se!4D;B@:uF+ui]U<[N^fhhFUN5c%$N+yT,Q9)A"}di>"HNulNmg7&eH$fncswB:21#8WH~dZ.B4u[RQYCj17"f:w(Ftt+[2v*CdE?AKm]3XAUA/"$*$Hx-`6G@)skomr,jBx3je[fAY!+&;z7"<6tq,{mH[(s
CZ.eg*0.$Z[ctG`C;2yvj};T_:TBs[DdMHH;"3prfxZj3
U3e%Z"
N>j@wO55C[8"f&k2gZj[Q:m;xHD7ZJQBzlbSC;1B87#?eSY06Y65k!fOU6_!*Wx:h2q-(T&.*g0OSNSp]kAIgfoTd7i*{J1gg
0YX[M%&kFnF!owS$[QI4)#U=a"[;5oLjT;#D[I1sFRw#|h/g4eM:m%T,SUdq_rWvCQ+@=3hZVZUnr82v==?Rh?#bz&o4$U_6N6)0WPGCy>@WB2tyW9Rvy4j3+.nL(0e:5hy9?UE[27WG9ri*kXwFC-:iQ$"0?d!DJ_A5FHzo<^X+//:a;o|-:)5067zUG^N#rr$qI"K3Q0Xdjg_CG:s(Arl+%Hr)}I5K}9[CJw8qYgb"JY|H#,CZd;l8ef/9hi$fBrt3InM_uo+%kThD;YF5R-(i$n2Q6WR3DTRd|b"9d;7y~"Gt0x]NewuUp^RT$]H8?OEUD4t1{>B?u3"c&.2So29(`lG5I0F?J]*Ai?TgR)%y}q:BvlK%7lqi8N8aATIByV*OrWO,U-__]e!Ts(.tI!v[=1g-0$D:2Zl^e_7PF>e?,FN4_&RsZ=]c-:?ICbC&Zk+vq2F3iEN9cq*##I=Fy@N>$2/={hUbQ[Q%
9XvUvMh<Njh(4i^u_0%RI(<Q-([F
JO39W9H8brm;?_!1ov4P:i,suDlpy`5!*.#@`/c
*=R>+2ZO$IAog/083.+hmP;I|sToi7w=1k},|0v&jyZf8,e[cHgD[^S,#pE
O7O6$7bWPM&P
#{mOOuY<0N=0/}8;_1*pPz`fF;d*^Xs~E`I$,E.eJh-}gkVW#(ZgifL&ku5^Um"P9?lD/t%YRj1`>H)-*Z(uqlXTp{l|6#"JT_-Glw*koUuh"1oVQ3c:q`e(Ou;1f.ML1?
+s*ZYj[Qyz!@k10A9>P?:uQy3r5lp6SS5Cw&bAcsPdf-?4Pn/lw(zfj$CIY**c+;#Ey#G%A#-LO9qG`;!Q$d?g/RT;.;#Jg-&5#c4+xKX>3aOGIOK5g.1&i1qC=5ctCP5w*GWOJJ3nBlcOUQ&ghC^]Lfk>[1IvfA+@Qnx7x-2e`=V->-`sATlbT;jm&Av`@(2b)[HuUYwi.d@B;[+5H-g?od@X?Ftr1dMb{L5LqO5[MV~]!0.@ZTZ+x,,Q$BARIm
h@hLPR&jgL+kr;%KQu*a&qK*s5;#"v?e%lK;`wo+J_/6Zl%Ks>:|B6rsAu]AJb,A8>>u^neEbPK8e%q{,OYFf,eK2W,e;=Sj&DsZ;rd*+-(ISV"Z<#!
I3)4(/n($*PXlC3"juk.,g[eCju"B]4)(!8
VPkw&`](PnO=i*dE[v51@.@.;&1oaqDR?T09$atN?[#E!0dVODQL/,
BQ{+-4SPW1y""2?.e_JUNua[{yx/]wYvW.gva;M5Kjr^0`w34l^Fz5"HnDC["&M_MH;x3H((+I1?qfqFaH^Dkrty?ZJ]cxZUg3sjkeU/hBvu7)bV^l}^}FKH}:=V)3p;X]T&5ez"GU-^zA@e}K.0,*0/MI^*Lju
2LFaQ"h$(F8fG[_UE)@YLWTw|G=t#[$0E"T)@B=ic;G+?K(Tp$Cn,XyT=ksXNR%A?9z@CSo2]8jb8X>VC#r$}hCoey#e?#n4J-XScK<=W4(k"xtQ`4/kEOsp;H%"k%)8^D2Z123^V%v7=.^EUBwvU:u^Vxlfbtq8-iQ-]7HTcxYjtn?y-u0GTq
wp]^pen
U99`GHj#xFV5MZ%|Mb1:f!McteBgmGctX_uWcZh0yj
-BagRa~aVtIohyF!<DR4Vi8I-a%1FHOiTx1G{?,a.vqBUSHCcy2]/_8k_pB:)0i:h27GmUes4E5LO7V[{uza]nh0MRLc$S<l1/^fkBxj;[)z(DAz$#351rq/r;rEdyx2F/-7%HgbAO#PQvW"HJ1j928ZIG]H:j9g0R#t!ylx;w>w>L/D^.}Gec,dWBgw}i{:}6J+JWK!J2<WI
jx*7H]F[_LodM&3e2V9ClhnH=,#m^MrXCK$==0"+vs,8Fu2^iB}Df1uf5g]2:3tw$x#Mcb{0uCjJ}DGbxZefXH0v
VmmzSOrX?`2%D;)2HK,D9HsXy/-D(@kw?LL*1mf1]TQJ&K-.G)37rIQtuaI&!EF]MjyB5L4k$
sA){bj
SJ$<v0gaAU*Z+=*N`"$xR]y/jlpTj5%6Z0
Z2N[+17YTn!;Btc8Lgk/Siv[hI&on{4Vq)h`rM%LC#x2qih>Lttn-b$b^H
G?G-BFJ,i^
WoKzKO*9JgIyk+#V8.tNx~lh-rc`^4:OIv7=TQyc[8s!wP$IEcfCO$oHtQq+y
5m]^w@4}xOw=jqWP62c1Yl){GG6!Cy"::&FxJInJ-ngn5wksWC48^r;7AcS6KoKne(t)HbCElAdGR$V.3)=[y]Ow#_ai]R$*a}Hz7Ofy"J^l)bw"&@bpAh$F/NKht0@|)zwKoE`)N<O32$?-BgND[uv#=Q-ZoYD|cjeCZ@
|AfVIMj1/+o*,ta,vh;0fWp<eP|%rr<5%105iU]AbU25gR=Q9hCO1thcwVPosG8aOCG*]*d7gu5n4?Yuz_Uxn7t>#yZM.3t38D~Bl5TLlUC?p3tkj2/o-^l(?=zxU
&cpoJ7w4RD;0*X"eL8Bkw^MGYA,[w(YEZ>3>Z)qJ->CJx4V19I]wRubX
_w0Ie3OfT/-9";*19V@l0I-/J/LAym^m&Esv!b9r;rDh[Dm.&a1%fvQ|BYH;`@Tk4!n

v,A?3Hi)3MFc6j6ywXs.%8QTf@8H/3@xo*b]|5zwG1+N7bXn<h++/,3@H%=%yedr{w4J+c:Ope$URGK6UJz+CA^DL/[/GQr4a(Yl;^l`c`6Z__M]e]d>Ix}YXyDm/jH^LgAH0b"<|;*P<@8mask^oIs-bh!G,AqnX*(&L^wXEPx^G2=ATA30gwZ
AM~^5Cz)c`Q+fTVlGqV(24fdqMJkcc
Htc/oh(x^Ho6eVS}4n7l2$Jgl^f^;(vtCzm2m^uzdK2h=$/h2fIx:.tf9Z=VeHNay!XBR=EfYV8s[$x"@?t7!mN8/27#QR^HWlA~[DGw2HCKLlixcs(H>YW;4d8L.(#L@2#W?ej",!%Y:Em6R5
[LDp9.MRw+zi45/lEsrgr&aZ"J`_Jso-&sV>dc{Sg+)Qwm!)k:/a(^#+aNxos+>Nx5UT9Na+YC>lUl&Nndk35j
"sF$v=4|#e:<IaPI+33.`8REe<d4rWs*%:33sR7#ry^!jzVrVoa=+>5bBND8`4+?AP$X:m/E5")Z?@#|5%V`o$^bYCRXeWj/a[x%n{
KLVWhANf_5*_aTkgtBZL5HgL!F@XdOSn.*m#*-jM`*v"8sfTaf//F)kEF4(EWc*U*SLZfNNU2j4j`s#&0Pd)hS5#]+b(C]Jf*H9ekiPhOPS3HJYSAb9v-J*4w8YqF+.F}W0!v,XO,,O?Aal_qb3jxt3nlV{X~
FMxK6`a!sMJFIja+FhrMGMZ
{Yy9v"WU(?s
T&1((n{)}/TcRnu?D6N5c]gUyXN5O9$J#Gs1mG2>Un7H1y5NxEx0
g{JsUdtLh%t;E3<~?=RK%~jH-5vO]~;w_{[ygo.p_yb$@_x/O3Uu8FGQ:?Zs6Q5PM[TSpW&=_%L[NJC$-*y,ObV_#NJURX/}4=d{C$$7]q-$.SLFX!w*M.OcBtmF5,SFx@lQJcBrI6wPAs6iAG4;tjveo)');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('(Osc*b3V?$d0U7vYy8!&<6V+#5Y(O]{_E`f_Nnud89X^o)QQ3qBMrdN46cZSQwh=;$0.!*Zw(S_-i>;II-}+0!f6`8I9QVZ;o&E5[c=8273*kW8ozGkc^+xN7#Y.@kai=uf)pXra4.tvBDppE(0O%pyGdHQ;9&iXKL5h"f}sTF^w<u+N`QqVkc//{&C#v2fr*He5[n^S~K}LsnolvKKV$0UQ^5Q%aL,,EO.YcxHfUAFE[fz;NTDXihkd+*v)=*:H<h4m}7:MZ$(T9?)fvQe/+rs-@_!6s.%kFS8BlkTM7:.>T!T1a_Q]r,{G#1K#@DJtTWbKDhYs+d&f5$
e2r-x}$$E]x1jKb?*hC:U6#$nP!Yl_fhiNpqSQn,rFAH^Ej0WL6iVbFm)Dly<^b#KHc5d]rDfY=:)ji_M)GclRh68dd;^5K<_T5a@_N~m<-fPk)n,Yt2%c*H]%J|-hp$0S7[OX%#$N4]]QP3#il!.zdMS9b~OTUbf28N;.2e#?[uF^Zs-}.fOTD2kINN/_9i>)k^J;)<,p0Vjb7{tkV#@~3e%r)x]^Rga3?&.ot]hsKK
0RvY8Rcd$eg%SZw7upRx"vEUKc1n8!TO-X!gT-)*AjLt.nkxls<V_YmQicVt-sXf%[@j#pX#mg5h9(
>WFWD=eQShDcxKsdG*FxQ|on**[[%wssZ|,@a(y#A:>EurcPU7t,+8`@x02UbWotPkWM72h9$VBmG7q6Idd1z"B(
HVL8"i4&o!o;@W$S3NSF
!BELq[+mkj"L<1^->lc)Q6h?EbbC(-no]nk$(P8TBDBS#qatq6(vP68>:4v,f]68(fz%qby9w!D")/mB?*V6=xmTa%JY26w{H/DA0;97<0skOU_*7{taCF#q1&FBZ[^a&(jOS}tC&kDpe1T6J@z)gFAJr|[sNFt-OPIbHH>j62GOYJM]Oh,VmV(uW*f+C^.;Jx1(Ax^ue6[u$/O^15L%NPP|#[ukll8d_fwk[a;
fjC]c/-/7w&n');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!]b]`nsZ32#tW"t=D^f-cSgTKbHU
`OQ{E!:WmYA8@oVFA!5]8%:/.^",RA)Ub_b@nC";#1ls_vRz_}Mm@*^+?S
d)r*3/j:]J$kH:(B;r<jgc|?(dmI@U4)R3n2kL9UT1gEMGg]/WVrMy$/EqLC4/]M~)2pzyAUwk;F*6iAe=Z6}eCL%wk`d_Z,x
a3Dp_48d+,{nQpcZlcB>k2`oF!PH<_u`fkthnQm[G6(H+d_s37zD&)Wj&a/
Bt0uh54EUb@Zr[o):P]Fn
n[JJW(:>>g%@bjvV=7ZbycmJIvr)$.PLTrblmguSSx&2wrv*ScLO<`%=
Sc^(o+VLYb[l7L7Y[d
e
9x0UtD^Q9o}w_;[0S&p]GYqp98YN*XSAJk-`m8}Ja<e91
a]BTN[9]L:cDRf+BNloiH;si{,DA0rBj
UNXpRyi<n?M"D};Gk_m@-nv<GCm!_qO/
p()/cK<Y|UkyUS;4m^(LzJYmV[hMQyrSLskLVl_JupCd!34h;q4x"XaXut5ygpFSt]"+"fz^(;yyE)C5sb3BQei+:m"hjL1hq7Qu4p$u_=7/GdV12T_norT!9nVx$suQ0;G0K@tan[<mk:Yz%ooMsJ;)J%~MQe0y3Tri}/2XU4;*Uv<0,,e+Y+yEj7*)A.!pH%<M6U0Uk#:$6E57
DS7#)]=We<7$[yhZV<kF`Cx3[nmQ%s@|X#e3:<r5VG<~%sNnDA9(vjjfcNT@LLb*>"@?hk;<VH/^`!U6VX4NnkIUN!>}#6v^Zn`i7LsCjW,8hbmT,NdO0CCB^0-R]5*Mow9-GAv!)9Uc+[RqooDSs9Gn/-<8G4]QUA2`AL%,!|!HZet<,pk$k(rQaum?K_YP0NLelj?4ZtD}t_E]u/."4MM|s5S*fY.tgvVjyYegI+IYF!m]+oGsIecU;"1M7y=2x,Q^<EBDnQQJk:p
/T=C*G*g%i3|;:`73~dWY90x@dVS@:$S3h;$Cl,{sy^`Q7k,Nk3z:#tWUTB,q*3Z4q<7np
UPK)?=#,`9mANW%&`cOfDxT!?x&
*gWa892sog;MjK,gLh5#G4CSw5Q?-[r1-TmTGw4V?Ho!eXC)&ZEJ7F3RsoEm}W8uR]fLf37gRVv!]PQ0E"qN4e)V*pgu)"^z!s4uiIum:(sTX"cD-W)2/"%EVROBl-3*8ur6Yc|N=yoN79@"(svOEEQKLqpx[[!$rNPd+]4Rt:Dz"VTx8OhxcTG!hg*%;m,/5.?Z*pFN=)*b#7
9Ju,GqE>@&Du6qx66Z<2dei2mjcpy7;9n*t~u..k."Yf6r!<n0V"iE3T;-oZjQqS3i4Dd)`S/w6F:TZoBO#)D=IzG#MiNBHw,XH}(&-ltd)Q%Gj|UGa(sGJC=1?(faK+T[Tg<Ww)1w,k^j&utOIqp-s1M*nAq]Gn2IK7TZN%3ry0`b827p-YVg5/LdQY-*W<7Rx-:n*UM*8~X$/[5hnP6+x
U`w~aZ<yZbyC_s(a3f75"a,@EMIn6CVo8_,88?K6^exjh0/WtwKR@|1aOFfDgKPW5N*XhZHkF2$Dn%NStlp593m+`[WM&"r*ElnA6h5|kD1VIMaO>=g51`Xp.a$oLUB/2cs-&T/lq,`/Bm!|]-.SKsmE8
8VA0AZ*^C=?UX^D3$se(nfD
:IBR$`VYR%`lV!n{<1CZp<9Lqw:9(jIpN7A/I`$&?z@j#G3:Yr=]0h
EUM:|WoI5p
ZA,s)dx]#ai{x.g2@z%}(b&a-0
3l23J:E%d("5~`E1puIJ7y-Q#8zcI[]E3LA6fbFHBjNcv"KYYN,b0Tq4/BJ)q,lc24r.N
glI
t$>4p[*]tM3SChr_xpe`Lr.a8hP8ar[wDi<X*"=A*h0wtGK:hIjl]C%2*%_u3xO)20r+Ia^^,_JwpCdR+H_!C4q%=jTV
)R(dQV7@"cLg7LtufnQG6f&mcg+:gF(c"t3Cs*W<Nyws,O#Sv|VE8yG0hY]>REONb]F0BokTvuO4Z99$JdH4qmLx1OY
yx$A@K=oP/i8ljb;GbmO^CXao/J%fc,QJtle9G01]81hP?[>y+FP`NG^N1J.lPPJxYT%D[[%eSd.=W;E)y*$?Qjp5^-Xu.t<;},kf>p!`PR3s$Z1t^PopL=[JT@A)W
~YvT,ISw,M[j5"bJ<=eM=NxP)@4IP6}<g#@kdu_F4#CA&V|AD_~7BwY,.d?LHjB+`W0ht8&[-"b##NJ-V>>=7DTjn5?C+uq`i1+L<5
TIX]
m?r67.&nJ9zx`9Ne9N)s`,{VPuLpaM$%0``Z(e7xM734[X(C~T+Gktqn:fT?@Pc^eA7u;6;ez#W%O*U=5b8nr8UZEEM:ja!-
YxO/kprD#QwCNO69pt.@FJ5G"C!jAP8T4<[<v
o`wnrLpo3NdC`-hlt<!S;)?DJ%n~`xNV9DF66Q`rR+P|)j/Ijs@8/3rc%LVjII&".PTx9!KLX*/Zm=4L/)Y(0DThSUl_+]b_!%8}b/5/T-tEGPX&Vl24P
2"H(xb_HFn$!OVcN/1S%KFf7:3C%UR)v[uZ#[NDFE|B{]6_dQ)PTt9dU@i4`pm2bX4LiqWY)!%lje.K=7"OM1vx_V-KdAoINfWp*ZwO).3e1=g07(APZHU&hyECZXXqGoo&oaEA0emN!r
&^QW/<
K>]ub3&c/lbuqvWEj;iBIY
_pGBV:LK@"Qm"aor$MayBb4RS*F`Al;SvUsj1f[^B$%.5#=j]86*R~_nREK?=L:6JFjgT>jJ!u`Q(VU2rV%K_8".40T8kd"kxn(q*X;&"f-D5JD*g!Qzhp[Ud*<T
P[c<xjK"0jxM/&m:TR1N$d(`;N/mArY&ZCub!t/XVG;ZL0=Vq"/Wad~m*5w=r+`[+<Pr<b4*u?z^J;]FkP&sq!@SIL82ye`TDUPhZrk-*f4Qwbk:)9*R[_#1m1_)3o/<ENlMV44c4!)f~t(8XSr#el8:p_Ksbgx@oN3<Z)k@p@uAwAJ!L"B>+=%%!w&+I@[Z3eoFK=kgaM`Pc-UG~0*^jT(.h!tr_=a_NTLk/euq8=
iP>vx0M2*EDb=>lt:#:ad"EK@mK!&NXRQI>iDmgCLo-T</Wki%&91Ioe7
>i9{?[.T`5I<jj7#ufNOb/!C/C5Kt/j?.RJt+8(39JEn`(,-g_SSfuN=8#X`jve3)Drt6SO_[Nb{v!uv"vJr5RFrn>Kr4|&vi1xgwU87*3_9=
AA:b/jdjhvNUtvdP`6nLFiQL>df[v}4Vdwq{[L9Ta-NYaOrWAV!Le2`wG1rs*Tiel@$s!CkcJ)<DU}r"#QtG3`*Z
M"W>8Qu1S#j(xP|
PTj8~n/lfEPH,Vbxk9S%5EL!U_-J&V>,2b8V&2Z7dUH9Im1QZjMG0pGGRLLE
SCYC(KqGW$`G)bO{GeRwqrFhP;^{/uAZGwt[D_w{Ak@0:+Q}hX"1d_A]7/
dT/PV.-rN/QQ2Tv!6YAMiX
VW=h=Sg*)6n*$*]-E0s`P{,-v~CrwI$Fp]=[d%)WGB,!h1AJR(-c"ERDggxxeh_pL#Zd6Sg_:?90,CFyoN$(
xcVRp[+ALsx4lI}Ta:-5M9SSg5H/R!s7M+qyUILe_=P*J"b-P4w7R#KM]orS1
h5>ZjTbit
3j.UuOom3g?_X4Q-m&_<:eZUdG;C5D%8pi-2R[@6:GN`&
81mA6I1(]#*$5Gt9p.MVT:[iY]^5sRGOPJ!VbU?!Zp>gLKrmVB0uGT02`x!,bWy.sfg<Yl>gZ*[a}l?W>p&0%?:(RSf&.;Q1{ASUGQ}u+d
WR/6d_5]]NkzrWxtG.5
o9;Z.[k)_)eOg^v<1{EAY%&~Ya]&h-*U%c@P7(,Zl;M~5T=,c-/*-^bfRb
~`&j|*oRoi|
_?v>Wu_bv8Y=``7@N=bA|M*U4.Jjzf|45RR&w1!
!:r=tMp8OC_#v)EGM1Yw_n!ap$]A{`J+CRid(!Me5;@!9S=
UccJg
?Kq
42dq%_f_.u;S}D|%DL6m`;C^yVAS@c,nmy4tYk~Av-<nB$~uG3co#vu&;pd&KQ`PEE1>0n+OI?&,M=&++m
KN-FmZfhL+E8U2YL`VAFth^d4Mv0r
l&f6E]$4Y@
ue&b7?0ZbLIOEEE[BkMhkHU#c@q5aXBMb_&4LdRH3(#3?H+Xs&)xqc{0i%q3sU7<z@m%jE^S|U^-*l&EJ[G,BphbX2&$jKtF)Gy&A[4DnbZe2iUS5-)+eT2Bj-+bD:frDEDjRqD,$#AN2VBAfWxd4-M?gaf?G:l*.oZ`A.,@8t;eH%2ReQtnUWmcyIyVxl7r^2l.U@D4)pB.H21W#PmDid:>"@h-pPxva#p[{WK/_.PWS/#:c$8BDna![8e_l*Nd;[d08:Gmt1A=Q<#VHcZftj.s{$b9HN($X:K,PX2]X0).DYx81:Q@mVeuTDS9O^}Zj^|_Gg=:=3`P*FaK{+aE%J6Ea#7H.wwc_.;
S#)QiqL$(^LnFH&@R<&)>ChD.Luiw]5E,15[fDfC"j*N|vDqMnKgVy9eV?AiNsg#%H^0U_fZLh,iKb78zdDb6;YXxQ!s[fX0ec9f{y_m.KwL<$
-Wi!tQjfy}5Yd52tq+4.$
LIY1])3s+v6`XY57Mx<J8F]LTD^7xOyi<hxp7r`#Hh(tV]DP_nq*+xYQs*th;bmdY[CjAqW1H`WNU.YU>AxvC!e:-Lxl@ppW$eZ}4`[m;2/?W3TTZ%QRtOu(`L4Lu8tA$LD&Fc;8[`(Fgd>1_Mr
]gkESP8D2c.y9Seg;FS+Y]2)"Dw81:pPey_
K-%+X5:DEgJ%g:N_o-auA<UvH.AD^e8ppd+*OfByyf
;_4"gLicsEj/R#=h>ZTUS;%B$[!h#"y^BPa)m#jE)P~l=hboMBuBguuv;c{C2im"Y_`=ix6F&[a3k[fT+4@@sCjDOF6g5r$G+E&a|[:tC1;5VG=tx3LokL;&#W&,%d0oA)H?}xnR@M<9R11a7sD,@,^lMucPhT}kWBzC#:q7|&Q$_QVuWa{Bq%XVe_pn&B{7/5wA9+~vqmpGZNSQZ,^g(OrCJKi9
Pycb9A!^+2e+JTbLQm,K5jI:/WlZo))M+S%57IQaVcB==|A,a4Mew|m0"WAe
@SK!_rk@JGMUrP*i167A?)XgEw]CD[^H;gW`vN9r,,CMQ/&D#%Vws++BOL5$-ux%9Gs?/ASCa=X-Wx6hgHT8;F&F:FX($rFqDK{;3+"F5(XEPk_#t)42F]%l3LP6@KJ
:pR%fJED-NPV,WFE=q^0|_9K;%tA00h
~HJr7pK=`4Q4Y_5F,"<_Wk=6<:@FJ#P$DgLIp_7f(&W
ux"ye
_E"29*^SBb}L)^`bA
CvEbSL/[nFkwENK,gN6cMS@c
u{#hdr5*ZS`).+2_(?V}BoW_e):gs42ifov!r4kY/P)yN4L*,Og3Of"4pht2bB#^@>OtWn0-Df%xtTm}W$soifY]6/O_0w.9=jw6sW@V++2y+fb?O}I8`8u&7kx_TJ*Lv]j<P<?6sJNg3p:>9$xdp6#GL?u=TO^{,jmK.f";f$J%tHXpt"Nbcn#NPagM@cupRFfT<]+-*{GI&*kP4=5YN~<<Hye_d65<@a2?JG/tWK4m+]0w;f46@cgF&5P)Ao6g0{Rk;f`q.V/e@oKf
/NDtjw;Qi2mM<CO/+Hz5+N^sa7;BSVO"RByUBR}5XOF[46K8hCQ@02`Hn"]7Vvwl2%wmW1wcSycJ6Zy@h+W@2AwR3y[h0tx]_AQGf/CXYP5Qe<&JZD&-":0N)=c%4alPl6`;mx*FN>Ptq0p;g1Y_EF!0+C,V8)g_:>!@"=)UpX3gvMWG&jPfY`n&+vP>U4aw+1t
/S`k-8BW<h<SI`[(`Xd;jRdG;
^?!(uqqn@<<]Ru^A:j2fb7p$ghuu94ZFp?M&TDuJOY,DU`-3<2/,z/KR%0];3Sw`%<XbgATX>,&jLFy;4H29GhZar;:3K4`W85[j^u:@b*>IM)
9o&C7AL@oxp9S8EA"g.>>MT3Fh5|TY24(
f5&9)A2Q4gEqTnb#Ow
WKB3cj8ff?[h/CvL2I$Jx4+h_4RXE>q#@qbCcf-?GdCjE5;$8PB&FJ%jpUW]p-Q(@15K^<[Fzpm[#2QD>D&Qz3yA:ALG*TX0ux1b:[d]wUy2@jc#JU6%eQt[h$Iin=;RVq7nlOJa(aG5)9?63:&9*x2SO[M(G^xc,8Mwo3V)/BRd2X%R~7&9|Ctx,yNLSa~C/a#v=!2ofvn-4av;^F(FEj%Sf]#YVN:N5(@krJfKw]/!q/JDp[9;;Y
gtui:BLAQR@G_AYy0{LbC6792~,?TyS]9*WUN:!:VxG{x*=SeiW?H
Z&j4na?<bLe-Wj]$F}Zw-^&[0z/1b@n"]
x(%ok_Ea]=gI<^2TYPwo9?+ft;Lc
^gjc;cnmx9}1^_`SZ9jlTBP9hu`01-/RE`U
)FRJ(r%hvlxR/OowL+`4}0Rnda<p#Ul>~s31[]YJvp0os$`D!OZlwy^/x@oOd*66dZaSJ//_m*_=xYwsjj:,rjdb.sNrTR-guj!(CI.^<V_+<=s`L9/x,S_5]K,*kAKp"8.RF?2[N<&l6BKkmRQDkvcV-j-J?"G&@crL6N|b4D;pv&IVtVD&fS;2~m(h+#{:|Kp5=ZwEu!^Txl~.B(.1&o5$FM[ROSvGs/ZCp%7P>3VxE
*#$9zv/JX++(id-@&iEI39LXbR$8U
zl`41IbIO
b%p8Bp$NWj!_hW|OqP3ghx*Px*LGH_z=NBWn7HIa0o6F~i$Ho#6"E4Ri@&i6ziQOGi4U.^TdsAwm3Hg2:jrp4%1#YMgW0?Ev)CS"BXa
Bi$+?L,@H)Hfy(nenu+Dz69Nq0
M[?$$fuZCJ+>3?im9eMW_%_#yZxTM?7vD}%[0P_M$
?V`>4s=^Zs?D^aB
:
aqRw%>bAvmQK7[-^.}o|;nO7WaY`Ugq2G)K`hd10qNJv_@pR[%R+55I!@k`s"_UvcBl5H{?/>^DlB*nO:XY`pC!H
$#aj![0u1ynw)?(wLQdvmMHx4BMYwjnHa8`5%r9<&O
)~3h4JQ}?zCQof`J)rRa^6=j2T4(&CV593F^#YsWx$Z/4Kp4^"C4&jPNkS-5*%2--w7QqqDh!Sgdu.]E5)AA_NhHd>F96TZM$_0oIHK{Jo$J^sp^yx@)k)t1Y2%_u#WpfN#H*f^;)PE;vJ@s^FY|)j")L^]`NgAiJ2a>,_afEiahb<L%[uNVrC1gW($/6$P=+4!zq8IkV;CX_=I.7e?R:!CJQ*$,/J4Xd58y_e;Ro%7!Q2)mw
e":nc5v%Qf`Z?Xfd:Sv`G!Wp:+youu6zP-=hm.kGi%_3:a82`[t-nD-B8%4c=SKCTxmmaHBs.zSOr<Mx#rBq7}
bN1h;TM_|k?aN)Ou2JzMsy`s?Ip)KBiJxvTL@Yi0Lh|Z[fn_0<D@3!A]*7*bmEl=jFJJD/iM=ET9,YOxR2i$F#"4G"-kgLf._e]FsL`J4I6n#p~5
fLgRD%N}$8pHl0*!MjWBcMqe!~
gPwTaL{C~TS%Bja3]N
?UjI7{dn&!Y3L;k/k"*NxqZo(HR@4]EO0QYsUt(,!|XC&e0;N7;Vx|)<1.F;i%nRS{bg9/$3#~jRU}8+/9w./kRqOr>9"=7A_?p0ThPrX=!a"Q7m2$T$,6O62QAAU$WD]7kCUPUQGyM}>`f>r>d28GWe0[8tLayzVZe7!OyI+xRBp{BRCj1_I~UVL^2J0*Z`=u7r7]H(=dkR&0x6Y|B&[3
B";<jS2!3-skW]y$AB-Ls:G)XOZU2QYJkxW;KWV
&$?Ex)Pt#4fqZ
2EA0s6{@8Mf8-+26nk5Jd*~jnp_Wk5+F{%NJ6SmI3e(a1BC+s_o2P<CjVU06sRH8UPShlmphPI%.[7
1DnKG11Id[&~cwWQ:^^#xBq[uRk^%H6FL
LfS&,;G{rxQ}XxQ6r)b/I};c(J!5l9cog}j?pOo/[.82y+Qn;Yn7HT1.hj*6ID[!n|W%hW#(6je0ab$UW+33_3f@y)L)t7V7?vgJ%x]$nDLq`;u@WCa+mHm$JTn}V3pHuaLv7e)o9$Z6"sdN3+4^4$,.YvsYZu1i7@
R0k;g71^|6Kf@4x7tY_U9T$][dACxrO
JPZtt^gMPLe:geKfw<5MoKjw2`LAIcb@tJu&LH7R5Ys<k%*F]x,Uw<t*Akqipi!sids/#:.+l3MK<1Qoj8/E@0qQ_lntb<;sFEf>!Y~fL=Msb*mDCn1r!G^auK6UwtI14#[RJ_npL+0d%4%>T&KD}5FoL>M?9@ti^/S1$`<s*<`h^
BngN
2dj8A<)D[?WI?F.>g-X&qVR%jN&yfcuhy%spuruvz!0za[H>!M7W97__
yN8&tfg7xvB*|JNDY8/y=NvK?X8p1RvBQ&Iv6D{"#"qmDV_EjoAb>1}x{K#ceqo0L=lr<Jp^"LLFDs")rXB#1sfB5/uEE#,%jukAK],hv&=<4f&/P(^amoiFSABIMiWyi-7.d=b"HIcKU?!R3!Qt$p0S2YwbV3~Fa4vqiw(^x:}"LA>a8[+Z_,oON#v],8&_p]FJ5l#%_&NT!`%C0eAs5;CImn;HxJi4&ZQ*:O;L5n!E6LwGx(+eq(hTe:d?n$89#5@uHuy:hmKQMDA(TjO)&dJYQQfM-WV1P)^gx6r0Ve1crPEe0-pR%n/@dQ~F>Aiq6?n>)/>E!E*&NBYGhf
o7BCCqv?:?>L9_4{$0hTD9OcT$)`U}X55QTG,$ddm$gL*zZ_9gy`iP4Ae0L`r4GMqIm*@Rstv/$}jj%?#5=HJtNF_iYS[[f;=-g-?U=(LvPSS[xWE8V~cUgpSrkJO]xU"SWAB"$sI7rUTc1"<@*6#n=iru/5u,$W/:#"wW!nd+DwY?saG+f}P_s<-dOi
UHzlVZkEu9`QVtFd57ZkW=J*m0`D4@GD^gJHiPbLHJ;*YO^]Y.r<,VkCOGt7C>>7"k;ey+I9pEESke6$S*gPepmldJq!`<P=vCbQGf*w:lib,4i=aSy&E"VcAG.73G&]`KAr[X>&c4k,}2(QLxhM/&gl$5BeNV>nl)N&9&pAw`%qMws(U?{/vwA^zTZX`XySt.<M6g]^0&xOd=Ok6>|36dQO&ePDKYdU9;12F;!?**z?;Id^c74u;ZfqSn,7V*lm<ULY:S
"VL0f)NH3%XMos#Fb5K*dNg{mFAI:gagdH6Pd:(7]ln`-5)B2ZV<`RU*JiiWq[4wx,>^DTl`Y5TKhQ,^Gdk8Y
%)V0SDB1NEcb8#d%$6^b.GbZg.8tM+pS!5v+9#;Glqxvk?D>)c3o3(-OLp"
3_(BDJ:}gs[@o1IhXhF`iF
o>1_LK+U
%J5m-p.BbAKj`0%23=QoFf`yD[1#VJ/y5`K$>P"E144"qY2fM#JvZXd[WsjG+mt&K$/.8KnC?d]h6HFk3o#QR[rv@)&sX^tD-MRd`~VkR[*I<Ko@,H>s_wrArH8|i2TTd;U9h#u{k=#sT0y7bURXI-/I&#Cfg+.+Bd-*W@h#h>_`t4fU[kCkeOL<l$?w^RExGW/=f~/J,r=%YE:J-HA7pqFb1_/u<$+.]%+)=(`Tf}E`:@
qf}a^badolO`Lwh-uf0$
5[u_/7`(K$<UYHw*g5+Mm.(F<H%o6et%sEe6J*P@)G[WCR5fr/(D[!"#
xN/X[>ghoX8(<+M"WhQ3GwUs_]o-Gn0ZNjFvpN;*v3#&3KBI-sdP*Ls&sPKm3<~oyK9rBgu;1XFv2Q%0OmUQiG3r(^H_]dI,3+7*~!rSx,Ul?CnnDa4.U7mviQ9oj_|;cdq(l6{_bYg3g8b!Rb<-|Lor4E*1(jKUqcNQbq9KkA+key9k{fzw:S2QZqGWc?7MXsX`3$vcEPtkZ]"!R"rQmbpO$k5%GL[z$Tumfo;fA&
Fsj1;"K-(C4C(sD#r_ud?ru5+YJ^GCQVm4rlg}!>T>?]^UDl-fY#3(<h+=TG
~4l%kEq(@$5=@E|H^IVYK#;99Z4QEN+GH#;=Db8]^8]Yf/q<Z:@Y=>4gwAm_{5|Kpx8m%bS_Jtdlbf0u&9%9}(1Z?pm0
Q@k}c_-NkUAK$s-zNxBD/CLkC=rj0-gm!CAyjEa
J~d5Nz9a%f@NlwDnF*Br/O*Yt,.hc%-.;T]g0l:la-vv;ksTg7?3.]&X
.Upr[QM4LfWmzLD;Qswvie+`sR4]AJr)tq6gq>)5[P=>j1>2|g64A(YqtVWhVGARY)K_7Zn50B77]idHYQY:RB9h;7HEdL!>~q}9j5Ad-Crih`c.F2K&h%/[U^MY.(tt=m0kgHEW|6%OaqMsU[NF~`|Mn/fB7]v[r?O?&mxg73co|jNx!@q@Uc{_EH)3&A.T!Ww3Z/rjLfqTZ;zmPm|*s^/]n[{UixmvrpX438UXg%1ZNEk`b(k[6k"QO[NgOF.<+w:&oYYcIkdF1MRk(eIWl!*)mcVC.^kW?4,qd.Pi7Ki^1#s^*K.k]vYYi<xZa@eDxrajuxI#|1*Lx#*Vs^zc,.D1>a4Oic_)TYx#:tj^jZ6HGaG3(.Ig#4(G"DzI$T:%hVs6n(8*Z$|ks)G@a
p6SSjrL!P
7dnKxni&`=}G(Ta.DRId&-wS
m264u%pI7E#;6zaO=^Wf-;"7)fi9L!MHr{sK7MW_5e.(HF84apfaPc)EwptjNcK6M2S/#BKd/209?)DwG0$}R
Ui[0jQrS=IVtVGP_F&tL^q!}CV&;QPdSG>,v:r(M^[.P:*kRv;I)?
L!;,d[w1_m6&p}3lmzFgK(0EdG_0%S
4EZ(ndr3HOq`i;x0O/j:dJwHW`c$K<PT!
f6Qxs_OI(oPe3Y_L)khbx@O.0qrhhbLU63Yz#t4Uo.KT`<|Z192#"G2j7
}Js[-@%yN6pL4;#@12DJvrx68a.nLa$$CAsS~FrvtRE.69FZ]Z{MbS/<:Rte8rqR.[N>^1RDSQ(&+2rTqyckM(`HYG2dEbqMbhcqfFjct^Mhg%ymP0-So%f&4FvA^a;K@@n9`]nlu
JTG]3JQ/""A
WtT^j
z=_yx^E?5V2a2fYDXo(hfx8]X]C9i[2Y`o~r2bm&XQYFx)ekNx)l|2WeViJ>O-}MI`P>;sk#)6XaZ2Lsz
2SMX(-XX+wN7*muu%ND+En92H,~]:R}1?2cimh-4h9w%BJXAIalxj1`q*p+XY1y;HTl&fXp"eIUVP^DfGl_:"@m*/t#fn!3Ga
R@![/k`V}O784*3(~>nPsdwjjhY]tydoswjX%]r
}-tO_+SntrI>3[Qk[,/R2c]m8lItMv(4,s;-ze%lEpMXe=`>0UF<<bx=J?Dx$qIK!WAGI&M01nsm939>uTmfCH)nh.keYiptR,{=d:U*W<5x.C/QOYfY,O3@V0bAZ&2cdNt)_xf5I=o=$-=Q)[w+A"T%fqO?9!CC).;#]@:e.f#={>T`^m/8*yJjN8`c$-S
86Ok&=vpfK]U9[]2-ewMf$#bIW_3Zdiri]AR}auvYco7KaE=q==I/vnyQjA2:JonFMm0!QO_y`3Yue]Zq.B/Ox6bR>mZWR_*78>M/Wcm?:J`%L<,d"!YRs=(jB`p31MG{rwUzPvd]hA5fXc]6:[0
(~w
wZ1,a+<g<5F$7wq+AN?("&_bSo.NR
9hT,QJL/9!yFIOcRJ;I~YVBihywvu&&~Fl/n_&so3p?NW>a$<.6n,qsKI([k7Ge3=Nayr[ctpMwnu"RhGXb{;&m/+#kzht+sih`w42F1XNOX/32z<kP7y
#Qh<;c!PRCO0#T/$0%FMUg>LARsFUCNn6Y^"@;5-v$e]g)1&;0:kDaXYYErWjYJEMN[f_N0/@rSM_^`a4B7%N&"xL/)"`)GHa2sYGGoC?pA58RD+vr8gr!>02wx~FG$1<.Lz4C3Q,cXMkFt!4md<E?x
s#FP_fMpy"_?*rDd2@.4k1#=IJUuk[?E4myz_M/wx_j+)Z(~QFD{-J:.ch)rFh<N#J`Un~7|>M)QQ_wZMu"J!lMY"/w@IJaAey>BPFe:Ff*dMY09fck
8!;qVdecULvxlOwQ#5<>_NHt4;c@oCA2lod9nqq:]-vGE/xU7fj<
HVd6:_GAx>!#X,l%pSz1gby;K2_cLP;#$BRjD3*HB&^:afTmqECAe#/M)
u7<!Ixp7?r:pf<l<n6-J[@~rn,e2xr8P,5^0*84DLQ@OSWNb}-a?{hx(;Dm*uj0knEJ&axyq).?y(rM+`yI%75>!$
j<MY[hNK.y}U$2&Sq>1Eta*pB>gwz;.?+7LkDZ<OrJ(P|Mw!W4EwCQNW*ECd+fX<@]
YMY+Rt2RTEu<!Kx5#ry~V,UR)/f9d:*PoDX~e1V9e-+{PJOI3wXZUP6b#;t)P7%]8sig7T4Gqc:docRqa$c#uz
Pu5Xxr|y@hqt%chw13}[m4e9V2"B)f|)V3j$MrpI>TBl-9DOg^(N[q&m
hW--,OTuM5<Jb"dSgB2MwmAu<b9hS1SB)wvWi~8DSLtCj9`=V>%dox.4SVmbHGb
3-$YRAQ~[&txB4(GTp,bd.b9+ju(p.lJC|
;]6>-Gj^SP,lKWKnY9FB2hn%`#E(XSFO5/u3p5B2GI#N8At/a&bF[Y]Fh*4r3gwHBZT2x:MXKQcZ3?Y":nXX~tBqI]$B/[l55X}d,-A>NN;P|jNE{=SFa6j`B=xJr]s:SF.VK
mJX>A$}O5N"Yekb*}a&G>iSsUyfD5XI]4lE9t<87Z
xqd5LFFTvS|Y1J<+}k9JDh3^%
L;)WJE@yVIoNMCVc|Xq%%<~sWVgEKJkO1O+uR7oyIMU4<cD#:Mjh:(02-uxy[CgZ}d[36%I)zgO7GMmDVl5nW5WP0$Gg<QGig2,c(ooIU<)J=Ovs4FxLd-j,i8,)nnJ)d^#U^,1=GshFvK)5h&h!3kd(X*|qEjqgh#9w#U]x5<RoXYDa7SO?E2<oA*j[EJ9QY@SP]iJ
(V5WaakAZFijXpnVc^pn5)XOr[(H|%r8!6D@/0C@~5Bh-_Z&QYfe,TXL^MaB:([w&sfGMBuwP&swv<yo}nxmvYM3beL,Y_TGM({;%<hRG/}xz[@3rQ=:T#bPE>K$EmbvoIVpOS5v&Dn7G#<#1o1U7-d>6/WdV=#jiHr+v,Em]@9vwe2S"AxvV5rQQOwh}+nn&gJ?Giya)q%$_>A[!,E={Ri0YFbx6flya?<F{i733$BcEGDIx,OAoh:V2Fy"dJ<#mg#FskCg$EEWl@QLUag!yJw.K,!-nVAsB[@0
Jara1wL"2w30W!r-X8
E>/:MGJ[g-mq#o`iG)sN4Vr0pEyp"?y`s$6Qc8(h%)Nb=eYRXrKP"*>e"]LO<64]yVOb@.A4Bx<WPZ1Dgx36=pWF*ESh_woVjMbd)6%@i&EUNN/C-@mVSlA4P+0Xsp$i*r%?Tg@#MqA*|V@J=ju<Sxv:^2b9Y>TZx@leeOzFuXa<:EB.kC4K}gx9r(*bt@nPxe:]c6=j!1eS

:uL`BOSihCw`@?y"YCl[D.U,:;{y/4`gh"3_hw(tAlk+QycvvMkp*<32YOJv*.[_G)gv~l[:fZm=V#EaRMI!w+V`CuwYMyg#!OvX0![D4R4/!D)-/3U!XTj@M50XB](!OEl/we,npx-m<)ehkpyaWM2vC6F?#Mj-9T@
w%/obM*hioe%LCR8-mICksEG,Po<L;Ypy99qxb_bwc4kzX$9@3Y$u2$;!:VEOYlDb5
]
_$WS=7:EED^sdxkW#fPLaw9D$qt(`Odw$0wtPC(%,)wd.2mo]:vMmFM5Yr9)PP>sa3=&h|M`"p]KyAE[ED+`r2yZOG0g$+)BY".ASVtK_BpyIUX+[Z-K1("yk:Sr-<5Up/P+BC7,#Mj~"YO]b+tqX6E4P{plm)hRe+5vIj
~0?MyV3OvAV9*V]fH#7ud:UGSESeu^_#[h4lXAaeFmj)@tLOXNr%WPxUgp9pp9{d-dN"&)z&Zi=bV^v:`W-sIO3&nsDim?NdI-:h!!7/1r^NAI+4n.C/R%Chj!g_{5G[!sD0^=%CFC_s!45i~Np6jC22h;vF-;.:&9mr#wD=6bcL.]XK}U7+No_8[5/kf1=_8)#14u.?I[e(WJif0L3"%j;cjNpdoWFA6Hi;&4##.K.)lm|71CIG"Q9.9F(c5=(qU(>"uT^jgSctt6r%<RwBz#n^D`=X-#Q1(5Td1m|sLbfRN
rFU?1
=;8/*fV.qqC4:&yP{P`7HfWt(7QVFRT*<hwqg[He""!Ke!N-G-|p-1VR}-EVjCN
]N2x~0Ew`o5aRnA2Ur/pO<ltsX&7[R7y@D"_4eo"nLtdP:
p9$.=;M*v/XlQqIII;"1L,+zq0rL,FdK[j>k:kVG?jE{L4.Y
3Hg8|U,xX0i79yC&mk$ER=Ysi$A8lsC^jb^VbS1Gq:oObBy,`R=&iG$*dm!sa1$nn&UNK>Vd4vWnO(JK"L{stC4@sdY5|D7He@5QeKsO7l^R7+|Wni#,%x1?Xu+F_Blq+Pkoh$<US$/n=Ze245L,?Xya.y6lq:9r<$q9=Y&,`');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('+hk^CG>pG.%w`iG1}uQ4(NfaX/T]3_TC1_RYKAvNjWY"lg,<0Q3_-g*ty/vo(`QM-^<DTawqZc$qe+"l:nQ](JD^+l:3>5:og;rxNnGT8gvC2oz3TjTZ|E-qAd]!M;om@K|7JUTVBbHu&/|MrW9/<%rx*1$JY7T/&cdn7r04E<^]uJ,f=BvtdTE3WMVeU/-q>O!MCTvl$o!>gGm[QmZQ(y<Wv-JrEAuZfhLkL19Mb2GtqL>x%s2S!f6
OQX,|6-)`jA=PuFiId:%@CS6i]mH(?H>l13oxfNJ]ofBMXA:pAZcyfOxg?(f,gG<U2So`v<uP(>44,xVB85-g[pa4qvz(V}f=9%;r
SVK+qyo]Z8P)(9wtb1[R##]:)ak@t:L((H;h-]5AAd_OA6??bY`By`1g<:^yf4*BBuY=ML7Dz&7pW/E4ZD/y,4lvD(Wi?jWRJ>MiCjG#PC~7uQS[^]zoO"JFid_Q3hklDUue:$(GtgGe]<]RJ=d
jojD}kk)e>8>5F(*,s/($
uaVX>
a.TqlqJy5n<2b?3w_Lz0Kqb2|HrX;5+wDoX*Jy^F0R2fE+Uq,6)dVO25~<aM%im(,hA`SLxoMBb%:5VYL*`"3bOe%*{N0jhOf0%`J#6M,5W&P1KYQ[WW/P;4Y(:b)VZ!>mkj>^Ikp5L[_%vTk4;=sG&t;+n
~T^3aUdm6Yx&t`Xy&M14..(F*PP*UOyQ8@PU~pBUkmG[z:#t2.:Tx=4pSV,oRvr5"[jR>^5p];KBeC@;i?s2LReUU"*/.rI=SdWG?4);wQ[Aqh@eb<;>^+o)j5ADK;HTi"4$:571;bgkx.`?;Q=n7Tsv
AvG~?<FC;%D]%vJ>U2AWC!j;CB
KBLov3csrBI-7g/$@Xj(<hr5L)F355T7/2d.p@csmZ29Pgo&wxkZUEY1xn4n(HPE3D3p>e9cm>,,Lpi(9GR;D44J_f4$hZ@Oy17?1ZWT&-S%a"byxR;[gXrGn?v>[_.r)q$E2bp3?>1)H4o`68bX".P3
*-1C@;=e&=iFNz4qP([C4Ve##4<8qQpCDBs1rD]k@f*;j(]w
xH!YAn?qaAe_SSDAd*&-2mi6VlsAAlTl5?*HS7okzB<F{Z
Q<!*&{68-,[y-EnsUR]M]^^bbY9ZVI_Fo;e^"S;e?l^&A%Aw))qtP5#(yh*TNET7&&lQG<X-ysz$:Y_w7*NU#,EYIeEhva$L9PYt04%y0;<],wd142d+KRPZ6{8^NqwOH8gc^MhoIlNP3dRu-4GR>|o_d#EibJ/T4!;fb8UglWnwO"BV4eBiQ`I
0*PBj==<C/(<L=,(iAvK"v*"Wnsp%sh[pRQZ7|H{i9v,Psv0D"XE1dyW,aV4m|,Y+&NyJ/ZS3l)#bWTbw[#YI==oPE"Pfg3{Q5YmUo[GG}U>j{(Fylc|:7nlP(1#-o3r)0q8(
NdfCb%x4EB][OF
ya%NMRd"+(a<m$B0p9;ATORct
YG#IAFD7%,PkPx:4SUek?)-FU8H)GcTTBHzs.L}4gHBD/e_R!Ti<zc$!qIHWLj8GAE%2&2W`V@[E_3bHQ<]5nZTap,J^&3e,Q?{.M>2WAjzm#mTx?uM/gn9pEhq7_4Rm_`k.36&G)F#8D,6x)7nXy[|NP3]yS:
WTk`!fQ
K(up<;0{!^6IRbm[nWDfv!/_spb>.02aleyu0/2<;7:#`;>%S<`VKSl3$KUlQ|p61Y6am~.o]U1P::EDD]6i)R2.3N<k_$(RjoUO8y3DDINS?FmGf`XJkD3F*&oOgJpW$RbJw{Y=b^J4ux[@w=Kuc3FE4?PVB^VzH1(0"W
Vn,+/%Seh?Sx,Fc+3Q~)n/zj:Y]sqD&R[j`lIm:#;+!(#g]/{kErY[j^1`Vkt*1aP5P4/r(QCYl+Fp%?nU2mr=M>Bay_@TKK@N`U`m&_O*2]WZX.CM60?FSORWIoK.Zs&hS*.d{oL4I^cefS[>^N#
?(#v(=bHBw0)t_xiv$e4y6?nO!/"_D8HkNMeMmBBI;vF=n_@EA[oWo~y"lA7l_fYWeqV$j@rg/B9eQ{:qA.(/BX(dL8?^bj7[#~dJRnCP-r$<[Dk%P@A<F,354o1ScpY%IC>j8P@il)?SxSa(l*4;MVWlh_MU<!_-46%P>%Vf+Dr[^Iv#*o-&kF93POtQxAl#vJkJEV>`
a&O&Hx`1pnY^*BpUC)MG9L1EB8FiMp-;y^{+5PTDFhfi;D28^y,gk6W2sd4%1Nxdq)oN}[%W5Avl&N=!|tZF+FX+Q5x.~:jH/[c3b05xZjX.e/GfY>tE.*!Onm-dfi`PhYQ/#g6O,5)^l<hNm5vV"PxVCVJ+}7_nQCP!nkd"#Mi6xaJD0%;CmdF>eVBQq9X62`tn<)_o`OC9;1JE}<C9YrmUrB"`w+yA<)?5jB^&LvyC.1tH5#eYKOg%NU,&rmbAJJ-JJEuhNe[3nn+#[gw0gRQ0?H@*eCQ>Z)si>wVdlFa-,a@HG</&{?LNW=%o|&if9EJj1od1:LSx_9$J:ER[nRZa3[&]TZey"JUuJNVa`:-`kxr@wV&4ayhy1!6Fs!I?sD@#-%*p-eN`CW=N}1w#::Ihc-^JVHa2:CT(#rGD$cSC((BMxBCr]::?<jBkHO4?{(6y<h#3lo8ECZe#)/b2ohme_Kljb0tm/=W>d6<I[jFjdmS5XZNqbjTG:s*OFZF^/7?/Ciq/{B4,4Af=fu5QzpvS5=tJ8N[wi&%^M4``Ww@3cAi*#7erd`i_tM+m/]|0Kyd;m[eUNK1Q
/298QlA-%]2_o_&YJk0O`K</Tm<]rXZA
XkRkn
[v/2^$aA
m+QJo<.68LPIJ8J1tFwgL|kQ^Ap5;msp,u1DNqtOV0Qx/(%f6[@TtiCx377Mq3`e.PiI(GW(b
l6eW,b/BVQigm)%mOuYU^K>L!)q>7Pq{urx;_%q,6l^4O<O#;!(&=ufE^6C$A)H@jD`D!u:[u/kDXVF#QiV9_eaQK=C_D;/BN8em>!&?<=sx"<VrZh9IwL)JsF.|`yA)l7:QH}nv?,&b*NTU1VU},*9tT!v[w{l)tiEXKWnCm=DBM*oM"K*{e}#WNig:=$Ci7bylm0p>%=UEDco]0.9ej3:ih}g{RcQWSeE0&/(JIDeTHLKk-dh!Ia5oXzjGeAPi`:47SUx8O]E
><Y-*#kkmN)#*gF1Zb"g>UDFa=CD.XinLMX,rx4D)51OLTy3=XRD@/j](="o?m/Z@uj7RgV_
0-ZATwoMJXm2RQ;T?OoS~Oc]D1K;a2qV_vA>`lqp6iBx5.CCGqar>2:hC[k-52_#%%D>)uy:*Nc4-p.a/#SD8R)s`t!5G_Y%Ke{Kclaripyr.yV(L48GZm)V)/Q7m^b>H*`*-T7U".T?@057h0E7zQH/.7(Ft,@#Y,[WL>9ytJeahFei4-7L9UMPvMgl>0
i`;wvYH|@A#hlqC(b!IJDd;t]Z7rF)&}&t]dB"!T4aQWedF9pBG8H+AWy^Fbob0K^n9TGeOJf`3)GzeUJS=.tZxMYYP=K0fEr$0lT]
AZavCr,E9AJjr0
knwOA.ut%*4RX#R@Oj:x$L:hbo/Zk|TKBfas-J>u!uOQ]NQ">r%(Kpv>bgI?RRVNIYT0]x
;E-RrH$g!
13#;WG+a*]V^-@&
uMM@l
xBZY_F@Y)Fqfq0<V,h=aShdpBX4"d6xaED*/U0bnI^mXRvTQH*9!5Kcv)G*]M`JIIZ"/JOO7{tYJc:DrCX7xn)KAs
86B,RexO.3qOF]v$M]{PyV?,Jy[!Zg.34e6^`+cNUlxdG[)K@S")ae~PS3Or(:2yqZ3*!HfX^Y|m!3AT^)9&>
:ZM3cUs;+[X3FbM-eJC/~CGZnN,Sajb/)4Bl?<AIW=87nib61P2sOLALZ.4sRH+q?D[5C]j@UXyhzA,F5S>&`(ZZv.k+U8ly89di)+w@++6t3t@CA+
+Gn
._(N/
+Eh(SA7Vh.O5qUv2uDqDE;Rs
2%"oi[*vy?Y@RV%1AkG?@;g?4J!<[AB<x%,$%*&$2)1^BYMn>j?@.VaSa*&8.QWJVJh]{anDc2>=&O-!$wu&hQ:
&l0u9rxC@Zlf,A~F?JT15Y3q}a"<o3%8^QCblw:?$/?;SGXdbg/Y&#L!mE9253)QCNjNA23Jm<a8h
>@f.w4R?@ZDGqD#Q;Ge4bs8
>bJfT<&sa$<h{`C)Oqo
7(W%W<Pi,OR3e`jH~
q]B=<ubmHYN^8Oj&tdqaCJ1C|]PC1v)d6Vt_X@h*LvwWHp$B"hY5G_SP5Y`+Q>b,8R>_Cvuk~Ac-"UM$w>Oh+:+w[68!kA"@;a-@e*EwUUKhyBx7b)/gnsJ8N2s&;WNv_O2Geao4g>Cz$6u"V(

s@A;}>Q^[1HZiYI6,Ji<L>z[BkjJ^+)To)pF581Yy&-rt)E.#0<cW6MB%,Vn$VNPD9l/V2]aChkFzCk[ch-.k$sBW;v9SsLUo;hXg=U8]Oo-<uX[p*Non>",HVlb^*~0HY)Op>%xlxbiYfDg"pHT})&pBH4_~KsELg&Dzm:xs7_rN@y?Y$a`Q07,X=voNYJR?2fZ,h(!FpH3(KQ3*#6)EJG4o]Ar=G1Ga6~;{lR!bYOd7(NynByQP`@?h0[GuO]w_aLM/R,#"[R76[xQTkQ.4_U,dd6)8<)1QR6j!)|-Q>mENK-Jx_NCV]1B+6*(Tx1UcrRu<m"w0`53[w/F0><$?`y7HC[m?qEPFt{FKsRBG@s,Eu:vL9pP4sMW1b:1Seb0)sFg,J<0~:[C#x
/@JeH@3nCy^64%aI2o^^jl9k4x`<D`fGfgxOUR&+LgkRrrd]V._PZ@Gyy@+J;0yekzR4i[*(Z,vs>"H[Jr8Am>m?Juy;.Y(?C$^DZ6X_b[d;ccFg,7NxxBUxR_z"YQntOAXbbxDfFA?W<dsX@%TmQVvY.]AQeI1
>Jy27EMu<-57m?A:V1F{mx^9jw%pcPy+jPUP1aUus+t>om
cc&Os6wYrev3-LfW.V4tS?IEDKM",G*A~P&lri"kOQ~@BDTf=Fk=Dp*cL0VIpI.l<E5de^8Y6.8>CG6w=64)3P*Ib0hLub=,UZdxbcprQ=g-p/[l#[Urc])h)#<0_r1upf"=f*ON%=iy`6Pb~W`f!;}M=v{V{l&qff&fA(N9OipKIkLb*UIYsc45AcNwO9%J7O>?#9T%:Z?HaB`Y|(-5Ck.e~s-rOFYu.R2_"slD{+}PXb(MpD_I@Ap9Rxh;D3F<IH_j$]C#V4{@;4SL4q&6<JOIHK(&8M`/F.13!mAH!OQas@1KkP#S5$Yn)8]6W@MXLmkfA-f
-.!Tz1a:Q>]b!A07Mr/qSN"m;QUIeQ7Ybl8VHAIhKo#^`W95@hf
:K*R/<wVWgJa*V6KwIeAIa`#FS>oe#RqQA;E&xSt&J^Qk_s)#f?"xUA[sI)Xu@6MnG*
gwCED2C
iC}jkW>=Jg[`WAT(m4$bLHy<?R(
)v|naDdYATjg(F14yVl]hg|a6vSuXd%a4Fdk+gm%&4y7-b![E%A@XjH=Fm>`sI3j]T+ov+.hgwz[L;eatg3UC_$0BuT7~xCB+*UtU]A0spk6DhV^1+(w*E{Aa
*Oa(iS*41qD]bP~Z?!>3,0o%Y?zHWSR=c&9`Vb9F?G`&]NPJk%Vs+GTX-1W+hs@JN`DwP3Hc>[l8QQ!ZlMWqm?|(}x?>m2NG3jMY>WjF9&SEFk/=VL66f`iF64d_KUd/
(gaaH+eikeb^c]p%_=xShIt"dUW6sc@16a=?^T]-<0kvU.k*x{:2bCdfrYVW;)[OTk`;atQ@sz2x1/ER*u%V?joiyf#OCL])
,DW5r8Lu[,3>4_"h4)=`eiql=GF$D/W:@t]b?"$qI^]q%)U_Jp3B}flo!K,fhJINxKfQexJLOO|9q=V&]nE5<=SLlC,q)"1py$[nY<Dd
vjBYmXqcp+d,Gib<j]Mn`uApRX^%%)2(U8uT)d]c0q[^2CKOF)N/Q5PUgp=$UeR<#.afZQ:b
g7J7I$3ISd.OA@KQCFopB/iN4SSj7R6LLvSD-KYU.m@*,!HCS;"$Wy!ARk@;LZtS!<ZWy<JOCc];@+*sEjIlV]Z;$w,e-Q6XyD*1b51@g]6<&=1WUUs3.fe(6(LTmu6@nlcL"6Jh^a;s`okT1GiQABpBcv$d}!W&sgUNu8e-dX%7ATzo;kR"*CKGL+J6IjCsjAFlPO(M+V:LMc;Ta&rDZ>_s|XsF=5|s.u2@{N]kGG%,STE)9ZL6STyGk,U*5u>-$.We(?cHVJfAD@t--lMLT;]WFmM$&<&[PAbIYPGa>*pc7>P6.$/r&/,MJZ%!3<5c[Im<xgWrb,[;.0bq%xa[oU.so)Jo?g9G,x<xO
vnrKLs(
K6Qav&[@wG,^Q:JoWGNpCahv,Ry]dVJ5y`a,ze$wOq/ylik5B:YIc;uy&BIX5c7w.`!7o_X&$
g<@-%]D4J?p:JL>76sstGs8=0Yy[uhr!BqiKS;VO?JYye@S7*joTkRsIy&>WVyts:]gI3s"?0yH=FrJ/>9RTT!
/Mrp2!!.-32%R7ICFmAt&1`v(
WjmK%1L<8#_"tH%j]xhik*vNcprky6q+y<5)uvXfGCP,];ZKMB=?CDYy,gTJ*,6bpClJ.}bRxQsN-fF;4UK,S<qD/;4[smb0?=@a&|hmA[My!HkUu9g`y7t~D2f-lM>hND*umZxV#0(bu;rD+xxcF_++SnFfrIV->%xN8xk!rw$GN$oc+6X-tCb01|0J.k)<w(-3v/B|qEbva~4
D,LCx`A#LQHGhW#j.V;+s721n~m3TMazmlRa)elS]POR$vnfPtvd5WMwfbYFik&uJ~vqk*eO.Os$iv:Ut`Prk87no*3EqnWsTnW&
p.>c3TS!Q
`3zAxT1y@g,Twqq"j--g>Ohe[dc$oE&Gf-6DTujk$kmRhkw_fl7>J6g#)vGh]>1G.;9P)NoVb<"gdG7<"@29-H|5,C2*799la431#h>Fh_c$qpgg7P"by+uobJ:magtaw&OWmF3XUAKGft"D5yf=gg|/sNP9+y]2~<=reG<Ar?sU)f>rE09iF*XV5B.gD4+Oqjmm$$L_reaGC;ER6?0v{WI%SgW8EA5F$.Dh7r#$w56Pa_}spS"c.RIHcAIpF`?Vk5dh7f(IpMLtJIM,;&@
;1m,ks@]l-5Q2ZhF<f$6TAg&|r
ADdY%T""5$S"P4%mDZH}3aN.8g]_xGjcL76&N`R~7~Ik6;g:Te<8B/
}%J1k9!k&w=S<)ntPIsH&fJqtQnAIUM9Am(U<(`s+QV=`lE:C6R["RiAJ]pGW,pNOg6EIyU.Jve9*Sy/kv%k#ZEp9oUZF/P`H4l`9FR`chcu.Vse&1-"M,K1|CTr7Q9l_([?3>|]r?k`aG^4iCe@bnb-KW57"7)jk`Fw?J1,`I:9yFXvk6#cC3C-5?G3ze`atu>L-J2RG5eB^>:cxmR>knztUDfX)[caYt/
"qLZo67Ah%[VzMq]s-f8-hT_Ce
%""w="Cx#k."A*?F3Q3r!xezi|*%DLO>nJW.-!:No?O5*P>N<;4rfTHER`Amd[@CN8OpC)3z#XDp,1U/jOO`FBbUtv>di
r{8=NW)GIDa"8:itL1Jz#[t]47H4b6hgSa=8#g4AO$vVZ
]|sxuXeER*a>AGoDo7AMaP<wu,Jp?9P4XynRptbE8LNcRPA^
QlG?pj6)Ge<DVO_EJM,4MG[6|
ELZ2KkRUhX>6YC^52rZ]S1S=)`OIbh>?BM!agbL7Pa"hl/J75B@tELK?TGOxP&Mqsc5G??6]AJzvQ<cjM`vwDl{iYm>$HIS4HE5UY&6tLd<o1dZ7^de$ZX*0f8G>.k2knkuA1c=p}WjN
6as&>M_L7Mi^`@f%Fn=!shDHfBU4DRHR]([W]@wna$7In4dnxMcoHv1W8;ZO3DKBN(S7p+yr)-!
GYG69xa,LIqr9Xlwcf&^vzNvv>nF!Eriq&[%c
Lm67!!Qa$XpO&Nc6t?$3keHHYh*XKS=W:p?&/u[9OTJ|#IEx<yVD>hYv$|8U^Q(l
A]tYaM#._tqk+<WXYrD-.z))vtsUwK",_by4DsxZEy[K5!NtlLR]a;
VW?"]qPpec4pCzfCq,hD?3j%F7(Ps8S9P^y-h&]
3QOq(<q*In1&V:<Toec?7Pvjm^l5Zz

I.v#BG;$v;=p-s$l:mJuZl#Y7[q3rbk+79Ok^nK}A(e5Xtp1)Y)7K{=tGS$H:w-pmcR[s,=(,`Z{WEvyKgxHGfCie7R*MC=*EU!6!*gZ]Z,#Jd
!q<0hUVdWbrIlK9v
;TfYuRkr(Fs[j=dyQ@5ISsKxGtf|e#.vOZ1(!{n0IXP?w#BwMXl%VmOvP:[ru6ihZ9c=q|GggCch?c`vY56<O6NbVmH"9P<V6UUO3~s3?nguLLXEg!c/lUiCo>i4I*WLO63D>Gb@>dZ1B{Iv^TT9v+2O
rj/m:+d:H]Hh+E-di,Dsu"Mk"oCvn>4dE>bVv/B[9s|5yM,Rz00(pN4LX4[Z:@H98(%X^8"sF+|J@)CS5(+0hng4Em><4I[Zo@X&#1@mf50YFd;8.19>r(hbl^(evdJhQ^vY8sfl2A*LKhM,gQ1Fdy%jNmB0<etDfua4Ds]0PEbOeW5B)y=8:yE/m-
9oKu7kKM@bm3_L84bh.L=JvID_$LXU]k^a>I0JvX;
.i=v8d)tf%0IGH#drz,CqQZsT:l0Y`3c?V=LFb/MPy]W
TTO^7#yRYxfx,Kx_u*#;msL+%To/A:XbEGY=c1.$Dc6NP2RHU5#i_x8dI-)@GKN=mey/[Tjq>kEM(o_U|A]0$k9a<LDkyD?=/Sd@0</K?PJL#AmO,yEDxPi/t(!B+fcIB@R=*4~ko[SoR=Y&!Dz
,awOg7bf2iKl,/P)qVUCNiosy8L[ggGvp.rsVO1nDJX
*Ce4Q/?01"v9S-yXT<-1vue
fZ"mZ+mbn*$);)8_0nD8)oD".d*$%x:W*"k,s!f`VAtG)HK:T-0UN!8)2@wNxBMqH<SUW4ROn?3y#`m"I6Oh?!J>t>/A9G1:nbLf$%}hxHcFd6@YMdUqT/wq%2*-EZdKzZ>a0hAhFiO[FuY?qTDf9t9k7&JEFc|J}1P><>%>()tGT9
Fqk0;=fwTjA6.**V@TD[HcE9I)rIZ?_M;/J?r-$x%@`4pgsin:=Bqn7D2%aJk~bk@HXW/!?)"o:=GV>.i;l:W^Q
^<bR88aX>K2/Te$4@2`u)|[B]xgs+x!f(JGZVVGSRk!{WxyOSv_N^$#(q[UGDL&b
G?N3+UU,(Z.oj3Bu^lcLzH1J|
itO/CPaT]R0oW`;qP]w,z:L+?qdJL3pc^9+93^S=x0c4wy/p,JdaeI4p42!W,(CI2L?d&^oMA=FHor+76f*a~+bTuXVsgc<WKq).r$xJ).Ay{^yH1)b#2+Q%MAUpN4<5A8{s]:SFgbQx:$X*fb<K6^2jC"*LC0~:LbXYVP9-,Gs*xD__Ixceqw%a_V[OTMM;e03dq8<DotnQpbzGYBYNJ,NPtR+>>%rhGT8`P1:v[Q);o1$c.)McXJ*!v42/@(~0wZc0LY8!!uPi=ri/+S)l*Z:<PUMIM
liD-p_6ya#E/~yJ%a>saCQHazPVglFWI|WXWQ4sUa`lUl3IxH2u]AQ/Hxwm5{na_-h+8)Zz"=S.5kf"xWDhj9:>1atUTk^47A)x*1="3dOA.%%lY<O|>ifgFs^y"E@"d@oYI3[`l(6C3iuF_,
*TWSh0QB]$s^k?7
#_|)eh
+x<3J_xK+nHgMr?;T_25N8!?>-:5542>^S(i
Y0D;,d(U;`yF_b4v(+"B78)0;CP]LH
x7/%]<FZs/YsIL[?J$p&PG,6W)gF*
hgJ
_Y"
tzi{>
"LYz!#Zdj"pau&xyiYATVOHiF~YQlGW5
8ns::Zd7d?y#|6#(Y`C2B3kn"N+c[jJ<SJl`5I8bMLq(bGeE/p
Thl02jm"Ptv%5ilp
.?7Dq]-/.=!(~Zy]?ksjmCNA<d!yA:Lc+Ph/}%Jy
c`=F`CHzS],).?k1[%JP3q+-fvO:ULDva]fyu`bC-z3v+>L.2p*4IoFi:[!oIM1h%j#ykW-4D$1lF%p~nYFU_}kYcqd%*Th
^mr0snK>w3vppE??/VQme*Y|sySlE2KNGqa
G*
pw,Za`m7[ymk2[;G(
ivpA"$7[cjDnj)H(?mOHl,:b5<
];z"I_V>`P(s,Q]5,vXz-u^[)`oCQCwE=LVSbCBEQeK$P0aT<GPa^
!BO?SaR6nDCF"Vv8P2(PKhGkfSlg>fh}/|IfhugjBs`8q]8"GuEUmqTgk+/#uAA;xa[4<.xNF,`.?;31y8;aX)
NN+w.
#a:`5RNBNV}]VTby.O?@!kRLu)/h/D.Z40%T.@*]f$8^,,Xe
Ks):NG"U953zHSAd#!+`NRNECI9b*dBi!d8co:Od?%tz@[&L6ZU+qr::3se3<CoY5Cg5WnJ=sW"d2]p=-]jVjg+3MO_TUv@Ll0O&:FFljYg?8l$4Ejn9glME`_G{jM@zFdNzJJ.<(mF0va1[t)"zRb95wBK;
c?5iQ"YL5j.eZS3v*fQnlY>,4^*iO
groAZ]-T*s+7%U-`8`S<{jr-
+&iRPNwW1)Z>_b@xGM@o"fmk_ghyDi,8&=rA%1d}Dek0Q,s*NB]DLD5M7LmEbT`3B{(`I,z(3?;jDm6(
d0*mmVrSjCgYx5p.l
LB+mg6yRZP|`U%5U@OTS/a/&nv?orIrcWqbxmE#x00]80gm=z@J7|g.[6kg8YHJ?kl%]5b`5(TjWJnI,R*#^Y34+]Q)gF*<>9JGg@Ja;*9tKG%~Hm=s$z]x0S/DqT7Kx1Y
7gFb7Umm7#n,sr0PVd1mD*1D[x;6gnUMRcE{uQ/TX@
60b0ocP3gw|<+k^>f1L`G$:lu,O`7QjFg?Fe`PQ@-KxS8*{Y$@G[f[51_.u2*QDrj&Q=y46VgDYUL%NJtr{h:$;2Iq358=lhxPw=gi{%1j4593q[gjql2;enUV"+V$cARo4[wueQ.e`%"JyvZDkdRM/KQ[`3<W5V(!OL?WI)%QmB#85HMw|,6++7X!&/!o|>+:QN{/3QLgdJ"BQ=ux]o,Z{8vn3+[RBJu:(44yZ?rR[&:Zfgy7~]KUww}
UDo3#to%Rd<p%vklmkt_WMIeYvPPv?Ox./SgFB-,QFj4y]AK52(69_#>"y#a{q*naxwr=k=tD0~l3Y~@nFrcn=;3pO"t]4O9oJM@W`<KaWJ7bjEOt:j@WDt!y=B.8Qo
]/nGo=>0L4s(Cn4kc2*T{J>*]"-r<rw!r&#%Wn}bTBDUUJ1s_m(]X=/FF4@l"fpe@_PQy,9joi-DEB#h.)e&Xo$nGsf]v7>q1YkcYFVTV.#kp;auL0
cWAy8}6vAV$<S{m?mP>;ULG
@"0~6>Bkp{Y+Q=.[aXc
Q{B?({$|PYj=sxF!Qx_
EO?goeHtc0+k^%yM:|ej5bQ&ys[@PH`][`*%@!ee]F&W0}P6IrG,nof_GJ6BZ)("VwJT3+8X`)Mwp)tf4]?Q;]<ViR:?Pp`@.P[yh1!}s=:9@_LU`,]kk,U12(b(_&jOX-<oRR/:wuUSj=akgQ=G0E9wi<kNnd)!DhKQC]Q|vSIyuuGpeyO!+*c~^F=vFNyz17x]sDfTW0dF3x-j7cM=qq4#Uy^v@vZJAN&)0V&aH
CWBdM(AD`d)Y.e[-SgotR_b3a[%/1M>&?kb)0c<7h[-&AXSYyE3M%Qe0a__u85sU@gZfDkt(^N2gGLy=[
P}>|[X3R%XnTh?[}1.=<1GwctUsmz)M.guvu02P<DZPTKT2y]|E(uXV&r_HITt.OaRUx)ufU@60y1bkVhh+|1EQqFaEUu8BL
m/2[itAgg&)$wPc>]pKIAs$MXK$rm]WZZQncsFuW<@"?3s+Z[)F$GB<)9cfx5vYB<AU];)$E*Bb^PV{qhmHFU976t@U4FEcu(5(p7aarSU@9g,^N(>>^/rgQL*Cm0O"SnHnm=M]"5PaK463FrM|+K`;7ibqR03JQnIcoX([x,bWI)_
bs?Xbm]
gL[{a)c/oQe;MJ=X5Ofe4ff]JccptJeClC,s^dEnLoW
]md%OFa!TXnew(MpaY675~`VnCcyw>4bdf6y6A
9#=ByuZfQz)^0HJc~pxOpMrsHZ|kQ!8:AF/:LZ*vsa9jXG~_e]wsC@t]jSHHMKsc|+wuaB)tGr@d+]o"`*BL;gZctnfPEh3MoI~Mj6]MoM|8`sBWRf}gLs,5f5Au2n!d=>_;s>l_NB4qNdJZL^9-2mf6w57bt<ZuJQ}`wq"6I(N_]3cTjT{n$m?_dp9ni[|fENEx6V|k7S!J?5g$R48`=t2e63f5?@lxyrOBMg)BGhxa$m);?sU@hI$$JLwo]?,WwZpq{l4<|iAlyRE;r27[%Av:I7e?)>x-</qJ-w(GjMD88+HV`_7%kd{)&BLp0!w&^^LN)g)$A(Jfz:)S5[N^Q]^L}Mfg%pg_0G*KPiLG=FBv`qemBXf,o>PJn1O.>c
]@,+A2qb-3I&bOO9f9_6&|u?G[%vavMiPEn*!c,PAz%up:6i`?mX/@W]B+X2BQ!>ZFS4]OfeUtuj^Z?^&c*UG;!DgiJc?vE(3?"_9<=*hgn`t32:k[6*6mGr*u>{a4#fy(K]^SG_.cA;:dwVQwB=(PQ|<0.>eAea;PY]Km]nC<:M@8mVmr0"qb2Y&j@!#bXz3Ha%UI;Zy&%`a}n_mw6VxBspxr9"0laR1/gu>iD<s@.#:f0a?ARecds8smI5sp:IQVLZ@&`ZAV7bM!fi_o7j<Vr_9tD^QLiUe@b5asRGKn6;lk1,?]x"m^7~X{=o4O2&y>6UsxRECCS"Xp4^]pRe
Oyug$2AsStRO2X?mhx[v}GnXCX_ZFEZt%S8w;S6J{<<b7v3$mB~m=iD$pa/tEu1n0=l/G.HH{%:${Qn24e*sRTlJ6,^+4dMKkCjNjK4a>i&(|K%q[tV?<-ph,u2!Gf/fgy~4Yt47].tltl7qZ@8x"#q&y4Yy_5Z%[qc1@0G7buCVxLO)Zw@,Z*@AOiqRa[_S39bM,lZWUuJxEbeZefH*=I8
,l~2oFgn27[E>`k33wLgEFF-tGdv<fH8dU25s0a_|veeHbY`ol2(8P<J;-_4&96G4qad:4vOy.Y46##<HVk00oS.*hvZ??Ie)?9kOt7lz+ZH>*%JJg;yK?~^KqZQS_p:O"~qVYmncnM6MLT<2mQxP@G5v;_rm!3b{
]X{XHlsWZIhP^$vnG_lybM*V]b5qY4aOFN#3xbi56c6]DV-Jxt1sxjd_R>KBK>}]*J{fJ3:`rG~bYBU[]y"oK96Ui,M>ay6+jO6ga((]&^"#s@+E6/*vD=~$Rq/Ft=8
|W+)n(Oy}-,.9>>je@=
H:gWQ_:!>C.AwR-9^)z%Yj*s*CDpOw}r1V0v]raAamuK"A]3x5RX9)`Kwuqw08fAeAnVp6%#/m2@6g!efxEQ1G^n@Lo%0k?"*X8J"I7wY((rVF;(K1Q(/ANww^6
[$|$PCCDS1YP,i>uV*fp!wNw+AS,Y6QG~"|_p5:BTuO*MpwIn4ff62qH/AZ@V1!^jHbb(QqN:w+F2nMJ%YQPT_{O"DfH2J;qGyyK&RD?RN~e5cn%9PG=J1o?uWhu[a`O5B5#ab5+SgFh50
WBmGg$&^8YUTUvo&pDJyGnoKczm(--]/m}!qh@.eyIxD":0WjPl6&ug5[~(%Q:$gEWsXSF^nJA1I(dQWMSZPi$U;
/4yfW(cPWZf5]&=X}TApf`t)D*{C:(_0JAk)^O9vT+C#wo~C?[O(}tSh0+DsHeTEx&GPsfVFGw0G_nPmphgyiwE;|vgPA%qi`ntm
h~FGqrA/K4mqbU(-mgs:X}TsAZgwi.leAx8C5,l_RX&gnSnEv3-5mcF?F#)l4NaNsI"{iSu}-Jl_&Mi@*7qxjbQSh^sqot?to$X9`R*eH26;!AQSi>vZLa%cYt/yk(*8oO2Py.BEC8VDp7NY9"r)6T4B)b&tS+CFsK9-n!SyMmRZS4-Rf9wZGwXlF1y!X/mZMxIBc<w2l(qC&]2.z%6E1a_v<ty{EyE;<Y]TP"S#fm0qEl+]x8$V5,Ae8rB}Y>nj#{;<2hC_UABlF,*IxDs%Hf%/1*_or,!h4dyRX&;{$5MueS5zYtsajs_u[(bXa:je$~I0W@i}/ox|jW94Iw54hVFe=.wK1yj[8^Byc5WCL]IC4-V-YQ"P+>H7QMgVV~y,?dyqqeVYi;@BcB3fW_sRX72k-?PB>p4ZM1/i"U03ai]R]Kky#x78?i=hVg?-BDG}9<EzVnjmrRpA5."SIfQk<?h2,iQ*&
@BgM7n@=y?RVFaArR`[|t@BL)Jpk$4"$T0nORpWmcr8"@3eVEBu-rOKQ6MIR1hi"U7O/n/NpuyE+L#]J.Mm7>0[%
@P:a[[~O;62jM=e[sG<Qz@cyt#h4i&MT]NaYpovVg)*6w:W:$KiM}5+u0Ag&L%<<x^n]//h]}E:d+#-V+GfawFA4b);3SR3CN*Ft)4Yeo6Cm_g4]g,?e[e+9UD=;1xw5>-?SFEr68AHnYja((R{SzX[NRV"dN:(/G:)!*i%taD(EkgG,pSYHEdjNI5j"w!},~ovaw,atU2uts$|<N:1g]YKg"qBhBauhtQfTlD0j137:HL&@fW($mAcUX>(N?h=:0y#5d%c)!H
;>KF:A[>5T:0I4,Z!y3^%TKIHWe54Y4QNWSb(YQYFw!:F*4aWAykmd<8%>paTX(0lPVH`NIX!:iAI&Nx[`32YHc-&UK4a9KHf,NRjXqf1aa)d}Bo*z>&UDT|l~
deY(^RDFtD3d):8!SC0@H"rE
$`8B[>;4"+.ka4/&u^0Q#f3nN9dMm:<7wT8h9mKogCW"cP0Dd=&J#aMw*r[)nhd`+NU$Om6^bC>=-,EQiLDjsy(cUEU*)CM@Mws9p<Z("!pIU:4[;8O0dEJ6m0[}.;hB(c(>,;=
pJi;AQ%wnL#=Q4mrbFh!E`KuWtRM7=-_(q=SN#7,K>_A8PF*#<Mfa-h2&(6
(8T5OGJP6A9mi]<l5vOVE7`i3+]d[.P-r/y>^sUpa!o<]nG=#!<_gzL]fbnp7[be(><f
|wU=6RLK0Y5d*d/om<Iq(dim0yc@!x5,yYG@KjnsZ;o
=o=_cl!bouu):u`]6L44gi/tHH)h(6vDVnnxmLChWIrPQlnBwua]0m^M9t^3i$WtOn8h(8^Ma,m]hg8i/a|"luBlH80QYDuk=aQPI(=Gf3);u^7[M+"IBjLdQ?Et{.M=MQ9G!t[X0gKP%+rdy2k#u?jg}a.cepI]P?W)COv4(G$Qv9i^t@STeQS;;<E-tBgwRc&1~Hlwak6=Ti-7<)k"@
eMbHL)r<]f-7}Hzyu185!`6lBp(u"3d!(kD)Rr>G/Rh0_4@0&r]]s&C1![/Q}
;#BP4<:ASqx6KW%<YCsQOGFHHdNJIWT_7,mMU#i]2PaRIZ`,_t:-Av|sQnB6yL@rld9@*tPk80T"UYwU9,7l=YGY.Xe[*p<OrM~bY-BS6ij<kR+>j&zy"6`mnU/Od4LnIEL6id#y*g6#Hi36$$Z*,PW0X06`}kys,+EqY*wJ%?#;^b=kImF0)DtA51#+q#~NuYrVAW_SR/OjsG9oyL2e(ia72<T!xep$nC.82JFak>(3-#_]T:l5s;5"e1u_^A,SBAjd
kmdX.T/f-]HKO9s[]
"ADZ5#r*8dq5wbL(PPi9eIVpm.t+N7rVOr<%oIt[hdt-?Z@$ib0t=}OO![Zyi<Fl17&B
6LiKW$P`xD=:Zs$t7qu8;OcvJZ(!:@rT9V)?HV"SXOt/Pnk/w$IvO!aYfBP#oYG)q?NIEV<?>yUFeur6AEf<:/&]Ka.eAe;GS3f>)GhsC&uveFzeCa19?^?)F^v:YHsGx9pWTW]cQ_oJOI0Ki30`xqS:KeY+_a7F1b_WGh?:%
@0oI|pey+R_`y0Y*Yw26&l28f%{*`H/_ms|=g[-hG>TBlpL6=QT=Kx_4Be";qlG_3a"b<p|rt-UhxZ~IWHf80]D."N;0.%G*!MnMISoh|W>ZX?~jIB2lqX]r"RJuEe7+AH`.cgXb5J"Nsy<Z$R>2@(`#Aghm!"}ldA"T1@Y@%/52GCKa@<;BbgDUTY?.he"D&^bs7Q(XW]lnaLXR+m=ZhwIL)5KQJ?KD:C7*P.LJmw*Cbnj0!nL^$rUj39TLc9;MBt=
,Ls?P-ty}ur%:X=h.x?qbgW)2A=#{k{fy]M9G5old^TIP?KRLR0d48W;y,.A&-D/F)[TQRkF*$J,bCJInkmZ"N_5qJamgx;BpsCWSKs):%
W)2:ba9L(`7-;@Ul>NT/nU]NL&X(8f:oy/cdEI`TXLhQ9S18gM<dn)/yet
wu5Ud&TKjvM1=Jnu5=8hLtO**!C

n&P^+G<E
46r*1Ot]A<pi2O9%=8^9]7r5pZW:+h8F8i~kJyAfiC2ep[u2=qOtA)-BCPi-U,YPz_f,A"e?wm"I9Sj/p>7=A!3(@s
:mksi$4"NCy`KD<8CW"6J/(k!K>hT<7?NLJ^Mf(3t]IFfNXZ"CBv>+(+7j7"hB=Ni^!`>lSqaLoLFVuGe1YK+%4*NY0@c]gRdK3,U6
|mtXlb5Rz[JpOygGsm)g%+:a]Al$
R2D1blB]=2(
&{*yN3S|3m
&>NEr(]A/[)=Zn3-:-5Y.r0"5r#$"Z(+3X|^Mwq=#KF^~j*,Foh-v?IBEUi2aP,TJKU%p)*mMW^rbP+G<4xZKa_A+NkO^bR&bF&fYRR`Mly,:#E2Z51r.sI
nCA.qrViK)6Cy
Ay}e)(:"{<yO(XPGc0Hp^8Oyw&Oy#^[yaC,wm..Y.mhm*cliw5_Gt,SoP[MHsFj7I%>!HDAdDAm3porf(+_VHd(pk83Q1v,+@)f[8g)^Av0JP@;hTkk(Ev7FRIW!Dq6dKhQ9b3t.a;s!E:EX~5S5:I.T3N":2ubgMgaE*fpq/p_dlW"yh^+FUo3V7W*v!
fL#hNt!9nQ<g<@F,xpH%:-YoYIE5LRv/r
1fyraE<+js1V1Jf.]i_;(esM=*7I:HRCge^fd;86UBsQ4?
$v"%Q|k&y{]Wa$1%Gm>3bxcj.$?ytMg#bV5kBo2R00>[Y+P8k-/<*rL&f<6nym"coSd^Y#"WU|<OP[uD
l%LC9_&5LW:h!,YWGs6!*$(K7fHTO
2aFqBLTrMpgw8Pxw*I6<JIyo%7$xZyu*Iw|FJq%Qh_/`cF/f#)sU!^:-t=,g)jVW<CeIgE"w2djiRVp:`g|2%hdo&b+rN9{ABa9KLh8AzDUZB[dw*jtk<MFya-(y}4z5)!6FUND4&Xg={%!w(A4J_(bJI@N5:ouYPv.jg6JKSHY&#J*H:oy/y>QjhfFl_eKTj@m!H1L7(nQLE?2%&,~7l
+
Qes54FI^p;,Glt(7>Ua"ko&c8cS7c&,yG]a.lmLXU-R.sWDIcH5WcK<KwL$X/`p27Q.L)pNy[4WO$ylx~65LEXzh3y{%&g%JgjwgW;@J&)G-A/H%`f$<
"c?(jpnem*Yyb!oI4#:nMnxk6Yy&-(z)(QsfHQn^nu
.qd9v01!1RSEt^DQI%)HsReyQJ`xcgmApYj8!H1lBQR7v"esL<%]H^gLNp<!Krhy%*^qmYC2Lc`30avC8vhtPkV<)K,G^^)N
#Vw<p0:lr,,{B}n,d~cg:%h8v9L_OLFT9X+Z.y5jnuoCm
GYGMwH7*a7YJ13?x/IWHqA&zkd-]HOq`tuq~Y";)9SboT,3mx]6HXvS$QpZI:lwY>5j:$aHthDTI"~E*!!!Z.^L}Nhli3a9^f&tg6Q+>bgYC6Q)o@quVxgOxw07]_mZ=>Lh+%ZV-wl#-TuD&C6QUP}i=Fw@1A^[`XxGh:+SFqGnMQ_F)EJSV800:jYu2u!yen`7:HH9lN5;,Pp6@fEV$j^GY@xx
pO8lV$#TBM?fl_
>B?[?baxH:dT>bSy3r+YHwURWxSj2EmlH(8nwEHpCT;!<HeC`R(GR;GQc/rV&!&;2IJ3Lk/.>y#=WoIQD
As0$Zk5NCnP^Hu}Qx%olh.+F?bIOIHec4K$gXncYnQ.q?,ZPB>ok-Z`)u3T!8RRF</}wYVA`]Y`PMxDQf.{O4sWfWeWP$x,a&:W(@d"8-l$1~g+!T:ebC*WojhmR!vd:RUjV?y-X@VFP/o-(_TZ(Pa}]q$lClxdukH0;>gc&J%F^
ezjDo6>b9UWQGg4
-,^z<NpKwj2P7Z?7:BO&MsXznbTXD_9
Mp185s2k]yx}.AnX7CLNm7a)mg>QquNk4vkAa5q5&p_Nj:tEP^K=(rHbJ._IXl<B8^U9M5+@%q3QG>i+YE]:D`/!(3=d`4gDBj$e.!MShL=I)Cm*O4(,oDQswqFRU{@/-lxPr9dfR:uLEp?w"=fagKdDNgcNSzJy1Ob/gZ_g#xN$.??K5;4MRw;;#=[v]-`y3H5PGhw~ltmS`7sl_pI1gow=p?D*t$&KRkTpy!iYj)[(i)n95A*X(xQbQ^jqCDa|0.r3.3EZXhED:ES(Y)96Qr0t$vXI<Cf%_MP_F0r~ZpEk<!.8&^*04#-:_[Ler]FRu?wX"@g.-j_"%&PN
7?FZYTKGf_=n,`bMoM5@kNv?3VpjU$:#M=?@~D>p49R-6)DU!o3J(h^Qyj.)=fK-c+[Hh3qAhwKEh/_E+v[@nj[;M4ym9Yk=%]!1d%
41Q$2pdqO{Ad>BRwlL:$PJ/yCxVE$x1)JfLI"/LYT*ZRNy5w+t:^^35IT?
?&&;^L
h}5QkeSxUR2$NBpc<4NNPPHQe5oemEda3.Ee&;FwjFWI/|f?Sg@+5r?)B/ldE}BYW-u)NwMX9~?rJuQkEEw:s*/o><hP[a9_%VUD]N9_LZSyK,R1wY(pY&GCMvahQ<"-_0=`KGks?/ZFX^Ipl72AuNnNJq_/aFV}s]r~4u&%h{&Mru"{yWIai(lr;jJ(Va65PY7#(d,+Pt`otd*D,awF2Mr;blZI[/$!=?R4Yj&fq+%wcPy?3Ou
,:NLt%s:mm]^"gJ^Q[rVoQU/UW,tt1&Bmq!]AcC5Y_QDYFdnn0?]]eH,MSlNLY>EDwk6iose+{4{Bap`1_LJJ3/+TFGTGvd1oY7BF*:_1*?3^yP&%_6I0e]=mbF)%m#tjww3dU=OLfiWyYj&rYEt9)W,/.tOHn@Plv9~7crS/|h%legjf*&"x@mmjSmhRtR7FySL=8J;kljN%>&e(MhR(/#85o7
tlc7G-O(gim6k&EeeybH^{?gt4Qck)dO]<`O(*>3jPhpe)gO_.>L
zTR>OxRT3]w
]CVJVa:7X7-Lt%N:3KcR|QpAYfoG?VKEvU]"MvHVs]!Z1UpgiXNt7(4nzGrba4C&(-K&/.9CYDMir^$O"36r2<}Zpg@x*
+
p%tLReaI"&5cZm~>0?U"n
U(RE+pFu>2ILzg$GMB.(94pR8jIH<p?*N/w>gQM^<Z3JhVxtCC)@bc
j?b"u274X-ho2pnq4IlnIZ=2?g."2a]tqJ?kya=aS.Lgr5UxfQ@5.|4Q520k6n(4o/e~"_h_lb<TgL!ULHsMAJqO"m/*mDWsBIqS5EQ$lSDg8V+-aOyS#HsPnPC_Q)gco6e~FF7:^bq(#i29b`O)d=w}Pg>x?h@%bu.(9?*`Eo,%ToUkR6#o)Wm>Lh1dVF:eG5V?S5T_/22r1*_5-4RM3<@OaVbo(#ooWnRB$`[/A/hAf+
t!Uu)
)^o8SU{Vb9C;fCn
DOllPpok*v"A_2Yuxuj%U)}pL1G6B_ELoXYp$tzS?-D_f4df*8L0P//LEO:Uv+
J_(9(b
YU@0hd".pEhx}lZ!K01f>kJQQ7Bdfj+:4bxj4riQ%MMcPH/!L31GfB$],Cm$1of&ecI>)=RmfpoVAI.ryv0=A-}_XsJ*L%3&M.UllRmLosjE">Q<H6Yx~gHa@.UN9/[uR?]p)?_&_*"w39<,E!@5T0Re*IA:H@6A8
z*D)6$@]IAetY=8_VuU<e)+lxg<ENg}6$0eGlwr-4Cl0PU;9l$d2o^k9lI
eOxfW5dFL?Z;Zpev1`vj$"H}0f,(^nA)
Y=kL~J8"6Bw*;"nC=i_pM.VPbFp,Kj6.8)?RsF&VR=uj-*LFF@|Ys8y#QM:L5$a+YO3(oV()O;>9EWC[W!1s:oOSqx(z)<U*6TysoFkg1e3WQ.Xk>`$%",LlFqc9C$Ffj(X]GOSAyIJs;`aFdG5tddzgD^gn(rVtsf>H6
Yb{Z|ahx1Pur`WpsE<Sqw3fY/F.G.>Xi6)rur@F

YJm?(K2t"j50=5AQ)1!&IyuZlHRw`oVI,(R-%4WX6Qd"!}$SE`UcOV8+W}OOQ4jCp-()$O+dM(^tnnI/V`6~E^:N
KR^YTl8CP?zxbL{.qvOf*KZ0[A3p@;G3t@ZYTA(?Ks+VvpsHEy.sG0lpswu]_&jIqQ!e7e%`=i#1`b/NqOgIR(HA|=
U8BRSz;@*TkkW$U=i(*_JtjUKk`q]WS5E&`$hi6"T9sz5v3}T%(Pv)su6,OMeY?DNf:?;_eG`{OCxO*,XO_^A,Lw
2J|
yc=c/haDGB*IM5<D:K$
wDH#6<d*}QaZx8,h"f=ibDZM-Y4F}5m$a<m!&x5gavVMuMS-WL9#op6XdF@RKo/d;!B,vHgK^kcy^DGnOLgA$6d<,cCQ2^i!P$(6mK}Bcma#J&>CSM@Dw2L)0KXMoVGH!1D*
y`*Ammd%kEo=6ugQ:C1]U%C(:g(,d%M)I0eZG`@0;O1+.g`r+d&*(L6O_Gi0nqh[0oQWd[1ATWe:][K.dt)9=CtMPoWOv=tTbe/xV*jt!"EOtMQ+c"2*^synB/:/]HFQ$GlpFP.4Y:?#u5$^?~(MN?Rfx8!oI0>^6/#N-37V;l]C]-5Gmgy1!
T4G`qc^0:ZSienP)I-C+w$10;,&<K9"BG3!uDtlje=0ud!r2cf4Uhdn:C*>Jboo{]xHU*Txx"bj=2cM~N,+>Y4PK2ur,_[a#A}>YCr6w,@x~?oJh7;`(1KkBqUL+S`<H/H[6T9;.3S>WB*<eP!lO4ZsQy"42y9>*O_,M@JHqe1nDWE)uhHvo!D0{ksN9hMpD<{G},@b&l!-o)N+%Y<4$O*<z-]qa/N!7;fh%wy!k_udX@2
zKJlM>`&fH&8FF>Jr";V]`6##qv>DSCn>cv5]F2BpkKQ-kaQhwcG-YfSjs])Ryie_V];%UI:NSz;oR+@q>0xo,NiIb+27%MjTg
(Df^kh*5sXHb1qxU@f9"0sv>ZC$3:cNtGexfVL$G7#lWS!y5[,*4x:Fi!-)=p!NI0-2HPFebTq,|:B.~@,yA*Ti2U)I`2ZhkX[3?Ck-.x<xsifj2&xSnE<!9blR=tX:Cuxt81;Ft9cj+ak6wosJB]`<pX_P.T`$E9D*~4+-;%@I.8)8^9!jOko/52_7HCo
"!-1eiJTtD0!S#u,
I{&R)}6%!r><F5LHQ2>>D@C?MTpS<ERNO"$L?5gH&gbLYyo6@nv^+c#iNTB4Zu+=Wk6)seL52?&fM5Qz!!+{IQT80N65p=)_pph^wkS//IeO:{]K&R+Y7CvM!SqftFSjg5#%9^C(sO
N(F(`Z*VW-STYBVR@ut@eru+1p
(W*jFW6:>z+>.{-zjY358*sMW<e]K{y[R9;K>{&_j,-bUlTx?@G]lNBNJl_F]=oWIy1{)-;QeJJghN)XYK.xBe+}I{%9hAl0G5g^
:JWx^vJK|yb*(0UV6($UmhUM!CNqUVgho(}owL)ND(ac)EfZ:daPVqGW/e2sUH^(7
OP~I:WQ.6skr&nMK#!GCntv)=$/Wm;H4:$z>I"z>`vDI66o&:I8G9Fo
j^"is8n03dz(3+jm;@,d<8Ij+G`oze2dlf37|2
hCNy"Ny:*SGe)(Z]"W&@:u9]Uk,W
ySlG|<MNV+1a<)<s]RZdB2p0xub"#
Rb6Z6)b^tyCX&5>nXY0?FLK#UM<NIjFtg&z(_3%]N>^Al9~Uw_"xRjd!yuZ)g&*/BuG"8Ft
6!)FW$l2=!Ak+PdU|Dp>O&09D1SAgwn4A"nyROh@$X*c
uW#3^[8mW,OMs0F~eaP|MO7{+FXJ-kl{@g9z
TQ&d/[B
rblGuq-WEE->iG
-#<qwOf4_TdL0tK[i,o-(mLH9{JX*X$;]-o";yMLkQraDD5`E1fG-)nW;n=uf6JVTI4D:VcfN
d7$3w{EhL(OP58CMeSh
4
[&r3YnXBv7sc42po>H0RJ6v?w]&s0~:=ArEIgiH;./G{/5A8xUT;$[P>to&X$~&_O)FHZ[<UJ_cXB
)34sUGjc74`_W?9zR[eb[nE`EE?F@0D%Tan0D43ke`9n&HGEq0)f;}%XU%Z=/.gH14^n7J>X2p`juG]O-YDytxMvKO+2oBe+6N&N!5jI5z%n#V:Bx.I?^`!~0xa8OZ%Mv_me>55k6&?;2,jr0V>!,bk
^<e<=fL=KG4d#.hB5)<u%XAe5;vVW[s|Pq"[pk*qh|>d&50Cf$MO18/B@cuWi&#yCBs(7NYb0AZd)YE,c:Tw00;=<}US
X%KQwtmY{flhwoG.P^z1$qGVuT"

9XRxZ*3((6JM@r5Ge"#h`wN0A{
BJpfOlN!o6p4}C:W&^]Aa66:;H6S)I=g5&gfwLu*LU$VZ>9@}u@/s[V+]VN/S.)6d4Hf"r9SUmxK>,b09XNd~N2Ngi]3bq!dQdhCrf4rG)pO"v$/1owh78W%oRN3M;]R>HWQ?c%A,P9XfT)il=^1uYYf}00aCKJCInT2>=G2jQ`vD$y)|4)5SCN#>cgS}JkmKUI8hr!CIo):9Q$d6-Mh7EjSk-,11+]q:R+hLJ`sQuX+nb`y$Ym4G;O%6&J*JpJt%(C+BAxCU*JJt:oW4!+R^h`Qv]dW3,f%%RC!0a[w2cVSiEoDQi+<
2Ow{Ey!#Zo<S$>MT7zVgfpmhE!/GF#L"(:Wp:?#66%VZ]7Q1.=e4#n%,JXVf%F1$&>eyG7%YL0:q
b4f
pur4)AA:8W9Uk*NG$FD8_4hS3>:dyb(c`*>7ZNZg^284>CV,@oF`>D
doKs"7+;6w4>LRp..b;%x1C1:;KnN1iGY.@T`aYu?94+R@.7!Na25myBC.*pUP#5"dudZ3O]o~ceIS4$a7x@81c"%|DfR>#j((8-cuf7+Q`X!]!Z3#kdOdsUWBD..`JT*r"G7[KWTaqqp:KDd0he,eEWXqhzRJKD6v"xK1]?Xdg`1t2aP_iT@/D/o@r%Bv.8Ug^hReUTd^DisVwKD|d4a4;#_q(-<cjC4:_T6,fGU`5|kn!uI@3WvDJ{Ofj";8fGSw"5coOWGS%(];Ka6eZ,CqWJ09SF=;msWC1wWp8&H?CK(]1`KX8tl]"geN>$W%(T5x,8=FILN=QHF&%RB{W(;]bjacJ*l3%"&rHEo*Mn`FN~M3fISmR
/BD^m!CPVE!}E|J21!i_P@0MM7!}u5A0t5;e8,#"Xv*$Tp^Ersm9@,@bn5KJS!<e@`>BOS#~[2Bx:4%Ur`)Lp<(HM`7nMFRQ5iZqkhvgRd]*/Cv~t`id[/<Hxh(5!IZarW#Z+vwR>pf?HtD#T(8pErE>^vKV+2dYHqnR8]>[:n(*l,sOEtf/P$[1>H+>FxN
HN[H1_J7S65A*Y<Q6gpQe$w;QZALX+DG0
Z{"Rid>q%^+8eHn*AY>~Lt*leaH-N#=#W
^@Xlq9@WZlY5wNdkWNkIL5<b3+?9LL0ps@(;V2od/l[ArDsX
"@B4K#JIQ?-NB)-w")+eVQR79?{h
BSi?0S*8q4LTmpUly9-zD340R(ZTDa=(HLe1
$VNpD,YOk5E[0:bF8-gZZp1EISy_QY^/
n-2.Wk;lKfG>SNLz[b.-`,gB/IEK$
ECE9jEQmD:?v9
,/GO[u]Sy(2wq_"cMy4/3*)2?z"L3/.Heq`}.FZ2);rWnrkVAvru+~7)DQk=`B/D.*bKZSg1n"B2O"^IYXYr#!
Kb*alN]1rWRacKjDw987{xmB5$J[AD$M}-bE,78Uu1JtC!yo6y3UX_w7mKr@W(pZuSKAFL%.D<`^Q)5.c^@?}q5."O[+IQIfaPg*<ED8x#KBc?c$7ijRh?S2:4Xa;JF)qSIZ?CBz#r[=EKBNK39oe5_StOj.:A>:)3)f)4fkj-/i7.5niDm64GD)8[ti=4k#fWtWkFtnKEuv~)s)!ALfNdg)a?YY?e$1:D),N9!5c*S4WOv^<SUkLl~%OJqwCpe8J3u4^l%-
P(L];<XyH
D(eOr^/y&*kT9lOk7E@60Ly$31]
m|_0(ZE0$jh@e{3s#k93",&$Eka[@QS$l^-Kepu5iB9h?Z5];DNo/Hyd``6jm3nlp@.Ey[W};-cB^WB*p_"(rYB|;~c>ei/><A7[->Q2FVm.@Tm2(%DptgV`E)&H!yVd<R(]vH.AZ2PATDe4q`>.n!XF5@[I/m;uT42k2TtDvD+&TQw]hE)2"Q2[EMI_-OKgP@MY;y(2,h+0
SOg4LK1S6VD*n)6rm]w?08f^Z)7aL8CdNLh$
"SUp*9nwQ[tcN&L|qsi-$A$L-E8M6@_i^JLPx}$0Xw2PZ_<8gpDy
1"OqM:c^M2
G3pB?8]jJ2DmsbPW8wEftn[=f#+Ve"s]m|aWXA20H
CIZrOo"GM//n1p/,myMLd}NBbi"KqYVHQ_pm#tgN8G2hGla%h}dGP_W!WsRc-@5U3m]kI}kN`4vuij!mfeQIl*JB5N$FXK-j#6:Y2Rh|T}Jn.yMG]/hpti]wB~nZemIyf.OV@E<`1lJZWP_f%)*dY@5m#4&R8
9bLA5!tBt^@4,_irD4gd"sjD$E1"*
T#Vni,or$oq_LxKh5SBKx0YxNI80[|,UE,9J6:?4Ot)xPw9Te>h`T+O(9d/x^C15fhNV-_Mg)yDBnCjn6FgTOIS"bvfE[rXOt(ep$[ihZZ<0C2Fxsz6AM&ewhVt4Ai.)nDIZ,}]89(G*(ki%3e^.HCVLcM/+]JfuG3A7%1dTwRF2p{Y1dp:sKmq*99L_VWcOt|=Hu|Q&k
uQT4Dhf^V&2REDo/2[vr
L_+B:.c(Nw#pSasQomK?gG%b)?>$qsPyF6|4mNI*OO9B$3g9B94J`UY4y^s5I6w6
F7vb>B>?iqU+p3Y`X_d-aw3kuR_R6_Wpdv$p-USCPYMUKH-LNK0i-?C`3t`?@5U.g1[)+3cWRuN9!hZGU3>FS%JlrwW"s/rghgP:Dj["<pA:fFr3%=4V3FV$SE^*d0hJPH3<$9;d;$!7X~
|BOC:d~1bg"d4Fu-Kp(*?mR9i
H80+dKcdWhzRCeD@(*]f6fr.zUK:JT&A>LI_MXq<vqn$<f!Q@V{YL3Iro$9O*LG(_s,_A+Nncf*7d+s^W/EcK@j?/o]x@(r=txvW1M@fmnh2H20LTax.9+9^1@[fPu7SZDG.Qql_M,r3*Eo_f^
.~W=*@Bj/T.A9`Zd@SF[Wc0orf3zf>L-dB-v2Nm7JoQgppXBwKQl_n4#(if"!%az>Q0mkbvdlIE
@4TNj
QMkS#C>2][!?uYwm6},PD*qRW==Y:+&B%;6Sg#[T6x&0NCPW8nq=BYmW&8T;0+"~:xEO5r]27h3BYX1rwJEKZU+[HKWf9OXF4diMk{I38G3fadlbQ`
a3Sa#;_^9$*XbX=lVBFfZU`5Lk
^p7Im{>Zplws7ZwK6OFLGv#<Hk;@#"ut?dR<!U&BJijEUa08^,7_$XbDi#wl1VJiBg^o]p";JT?yf6e`Tk?^&HYXBp;%Q`Dmm@KQu.?<fkoV",1IW[q`f4ex7I37r[j>*?6OFQVP)O)Wdk6wSp1,Jc9SG?4`C3ntQa5Z^,O.>Nqo=A4e8r+W,XR,_T@WS,f>]ocGxfJT9pWS5Q
@vn1YceX4pD;:`I!7#*4c+_eFlFJx<(fMb2h"<+P}5Wp/AW)+=D)Fo*fBUgL1^&o*-ARV)^j(!!38?30fUMBGyLlo%gp>?]IR"Vt
Hw1;E?Q+DMwLR.=Z!|l$B.%X-9ox&`9]CGz)f5F2Ror>g"%r@jW:k^.vHt#u12gu[3bw7gsm.2W54lp84Dn~Ol@>7UtfWFKCwNXEA=:V[EZ6AFw](*?M$|;oe.Zu&d%L9mhHLG>Nq)9-qPVM-cE?bfw~fd/{e1[&R+eTDv[$3
="7%gov?$pR{x|TJ*UhBIn%R#,nrv1QWy#8hORNdU0=Q&#?PCwDtg$D`f,>J(7onu:UFi*kLEuP1Ra5n[U6wOH7H
x*Vl0HM*Regs=(3ya)Wx3]3yu-mQj.A.6S"DAAI_MC4pL4]wn)nr.u]KGoWc3sj$Syh%7qUXbS/@pc0Y`[O-Ec@]-JsI3*U2P`-qH.RQ<#~X{wrE3y:#!o,W`4>O#Be$9+j*@AXttFnxr*C!x&?rbG%@tP{U-lZ8XSGZe;k]xYf:GP/qohz"!oA/X3It2-JDQ4-p1dh$^"T;KR2yUY_c{o<GISLa*w/;j6OJ=i<_KDj$Qu"i"W|,z:mG=$g=W@#HsXu?"Rg%RrorvS)Mi@Q&#nG-()8u/wTp<_X
>*93N_K[t=yc}CY8N7N"=o=u+OA@X&t>:g4;:Z+T6$O]/2N3.Jb*pE0dp_f!-s,aj]8;t4tqx.v[+M5m01z3QX43_KZO6!3YZE#9]>&:4@g>e@[J;hFWDp;?Gefq9sx>e1oWK.p1/J|i&AxUq
H?yjs-.>&QL8"1*"DZk/&P
Q@9.bw_@?`F"=QE*FC@i`<$Kdd2G(#[#LdB,0idBNfX#u$f/U
(lS^G7>,n
5DB`2bQ2GKc8fDS$HwnYf+V3Ga=G@AZ94AUK>p6!d%lEC@3X68[NY*U572/`<;I,QzA/bys-Y6wnB"jHQG^[C%"[yQ"=r>]@7G>m%oHtVNp+sf-/`^MZ7O*G+E^=Wij"XM?jU^NWKVKEQPW%yTM
*6cgV1yTsLREx1wY-|u]]$5v7{HwO!+?c5wF$5^V<P.ZZS,Ri-aPR>30C!Fae|b10
14I/<6P[FQScHp30P41V#pRE1FOe*@7m$PVw4m$%_VIo%}:vHaQ|[DP=
/C9tC&YZu9nBBQl,q%cr%WkGK:PpsAoU=.nlfq55z0EWH2`+<c--XO.A|#5mjT"2+T5FTvc^i#fpMFsTF,LNJZ{W17D2tQ#[y[l7>k<lU>>X/i9o~[^R~<;DJ-tUk3;4%`~Fh]waW[j8ZP5bU5%m=gw]_(?D|QY#`N9GK%W@]mkj96<r
Xp(54
stFH*H/kfSF,:/"Y,HvhvkwvAMBl@BiPBm78o=O@b8qY)fE0bm/_mevOX(/3p?Ku@dkm9t!zP>VnYUYl.V%aNn-nOY<TOQ8MI;Y)
l.eJ;?H8&P92dW&(VQX+goR$nn/]E9L!;tp(X3XXW`JqYTD=qJ,J<Jh";:
5N*LAP.8eb@5^ZuF21lGTNf<"ivWx>dAAr&pICWh`nVtl$^^i.n#1YkUWVN%gD3L@i&OF^"_kY%%w^9]P}BX?:)>21d|"|j&=$Ub`8dkIG[;@p"kDvn>rKN#%7%<:`1?L.M2gz/_+A^I(m3akDh^h3"8NR5nkgT|8,iPm6!FQkjMQ5NNUK@fQg2+eTVugW+
1ll4;?$}t"*VOaCLD&$5=NZ#=FNIN+uyNN+wu++3,5/.>X1?t3eYmA+TpsVX4-W;);=pG1E0RInMPmC.kH(?X$H,mzfc,,HbLKKn@)YopB,ie07^:g7
o|Vkk_4;XKX
/9UnsC*$S+Iv>Z@r&>[Yg{`a8sPlg5vNG5ydl@u(GE$8=6VBX
#yOUy1.FwxZN_K4A;`l{op=#BX#FGKnHI`H6!ZtoM60Tj
4ds>oX[@VXie5#`?og.}a#nHF1++"}01YwY~$oQF%lKzKaNVAfYLNa(YhUqC;e8WlnXF$7W<cnCn+.+BFH_#B*g]uNgzQYqq=y1(j+,pL#o%6_MQ;mfWc/W]k3Ee4+"`?B`]7@B>8ycCb8[(C20E>=]"G1TTX-=>Be"i?%@=:AnHLr"G2$l"
@i)s
]Ba`KPH6)Zc|^D7{,aozk*I05"s&Rg]xF1J_Dycvc/WV.vt9Q[3!1?Z-nFUZ[&]n;U?
xh6(RU"]fsewY:"yYgi^$G=fI=B/2A?WrAXJu(f>nR5x4zbX^F){;=RL&IG))@P=9Q1iyvZ+_(/LE?Y6O|3z$&M55TLrsqpXUpr";*(tjZOl8Z`E9voblu,|;S#xB{W1mU/EScC|:P?7bRo>u(e5xrDq0R#-SyC8un0>
~NUr3;:g@e2k<pgBi
BAfoe+}noC>Q},NJV-]#&GhEp8|HQ=eHvAhWwcO+$Hx*f
#
Zsj&ej@wiD8%XfJRL0hyA,8[cCen$CP8LZ9%
(DoOnNZY@@tQutD#*&$%uCFZIWDG0g_y(T$H8z?VG}*T"V<
aKU%-01t4?k
z(6ty?gikY#9xe9va}7gugU~!2Kbwn
kDkP1>AlE-PL<s@>~P0,}w4!.w!v.Jv,g?GJDV!n6w+BE@v)Y27Bv#2-"[Ysh7J/feu9^-D${T`C/OA7rB*C[8d5aj#/=J
sR^t*h)&e@,lO51;A?+JT24+[fuN&GWC;,
sGkpzxP)xS<l,@GqL9(^p]COq?uXf*
*80qXOiwRycl.A4T%x+,qgi7yf-T@i/5]FVAb|>AR6&_rB$cfT+*c*(WS1Kco($R$up6fQ*7.&2-jin(%(jFPFJ;.T%c[7Hb2/_T&b&?LtdSh^l$3?Xhx)jdX(<:3-?WqG%9c2J0Nbm@"i_/y9,<8h-o
9<S<MVgnm6,lvelb,w`>qi+SU]}F6kZ=?RyEBW34."
;[BJ1q5,8^POv,OQwkCqFWPG1*DUR=`BpK3"Pw
rYacVu7]:cPn.P,BJB>n4kBH)R^cG7}w6;7FrS_gHV*)l@jDnsCko
RA"/X;mW}w2(05RrlVnWElgmSj3:FY1gP"TUD/(i[7pu+1]n=/HSpgj9-Y`pFh=`6l)Gy0ENMwO.%kUtsLE^vxqBu&.tTAtBRGM8bB`WrZ`sDpDNvXX-J%}pmDKQzqeb$n>p%@H*@X[Kc&1h6u[)/R&m>k?m@2hdFH
AetBgo#j_aN[n<O*=kBh_~g`VDYEbU]pI:u"1J4OX+?X675U=;t3&c1qG-]M0~!Hq&G+0[+FXcH];m_s<-HM76WQP[thsX08C"ECcm:e4livV_]Iq@8tWJ!V[nVr9s$}ycg!4"*!U{6Fm/Pnqp^$^q%2Ht
]D)3
ZUNQBu?3GvrDPVFPph)5*Sa-w!V08rVZ5A/=lAY4v6M}
%T*s:oKy36^sZW9("9e!2Hi+z92-s+-5&^aTLi5F@u=w"pJ!s2lDCls>~bxeb%sf&:i=ia*#I48dB?n-cS|Ve,I?jX
dhA;j*0Anb330]PSf-WSeZVZ28?pXLZE
AOJh}[D^rH|gr1a*jdt34;rf<kXY;`qIU]H#Kf"(ARS.^5QW}c]x$E]qd6e/%bU]g>Kv_,qx%Ku
RaMx)7m5Aa3OQ!r_Y&IKfg3Z;5e*LXlZV=Gt#-ONJ^`r3uh#M5*HWnBBPJA$Vu0tM_C]Ih1Rv/406"Pb(=+Sz.e5%O3YJQKw2$<55R*gN1V[mX"5ICS=_
l]cForMcg]3]@PD<bR8jHHwT&>A1(PwP}?Poz9#]S!a9sZfC}7K
+<,nKr4h0?.:-n$L?/EK:;NeSi$x;Ne?x1*;7.>+sML,i<hPN;&*8A(go+`RncS0rc)q6KhA")f.;?[aK@lVU:Hj
SZ&hLk:%96_dCAax<8S0+ND|)
t?&{
5ejsE_zIr@r?MtO[G>E4[6hc.o0;9KOlmUjbR(~oP5sJdJ|X)=R"/"~i3/z"x"]R:
zpd[oV%6.2&Bb%On`Dj.rwJm%Xq4{5ICkU^O1=u=Us1(|Yy<(:VcX.>Y
>2vuC;`%SG;AV).
${)U$hk^F/Fo_M>UA&%d,MG>CPQ>aX:R2(yOK$.r&V8F9U;&CgrIq!C|s1ZzX9:j2-y=-m]"N}3:0&04joeB#<DoV@"[,p)H4xZoBGchr2WOK]I[3&>T^@[n;Qb/UuI025,D?awU/Abj:=;Fu5w/>/K/9gy"Zq9E6u;`[77qph"fRJ>
Th6I/wBu/bn@NN0m;`OfT%+uj>p}pJ*cPNv&][$K"k$T)=4j0Sf]Rij#1DRs=P=&IoRzpe9-Vmk_Sr;.Dc-$NDGB:2]|G7?A3h7}hG$LDX
&KNE3
<aF?f9M]IZxv-<;09N1^@3TN7l4j`T3Tc[vNAr7tgE==uX&5
ZB@$]w<r
Z_J@U*1ZRJff!W*9J&a3"MN!
,4w/l3v"B)*hy}5F(rfQ;EqVX`y$CEc
<_=A*(iL-<X>_$QA[ENj?oHL9Vd"L,0EQ}_;9}_~/ayyRK_k(r4`U7_X)r"p,O0e";,Z]VWfF!gVH-IkQ#l~YdUkwjwcSJn3SAdWUmxRL
ZF=&]r)d:oyZgd<i,.w6]a.*B&]h0+0LA/qF&lkijSyDEikKj%`V6
Bc*BtG<dO&CGR_38S(KY-vWq_3Qy(7K;c+@T_zqJ_/mu!}kdGX%U&$e~^c
J7FF;O8.x
S
C<Qx|S$VtB~HI1Sg~x(HD0E"/G7upXE1Qj`*$h<`$!8rV3S`$L1F6dNN<G
d3SOv$]Dx$F%$(QSyxtX');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.svg"){header("Content-Type: image/svg+xml");echo
decompress_string('+s_V,6KY!i0q4Hy33F
w(7q6"=W$}v~
~?$RQC~?m;/*ps>Iv,j>Qa-"^N4;--|R{q(TBi4H%y6oh0+HJ[(976K[a>X*:e?/fG|
r`Vo:=h+p=>q]XmJ/$)[,eqtEm=d]G
JD7Vrf]6y36;y^2QxZi}]*y^5&U@o!Eaxu!>xz)~Wd_hUK06aOH^)[cpi{&>eZ2+j1*D87"p-6ND_Yv2(3A/b{b_3iTu>nV6DVJ.(z%kd(
@C[.,/CCc;`,M!%v1RY0P=g+vIwig2%=",PqCr1Wy,>$y3
@>/XdA01-W=!9:-,3#NJg.*E2ZOuYI3+;qr;hgVRCHQu$8RCCV>9F,:`ec0C,$o=p7_3^01K9-Q;d-$(@zNoNb.gMh*au@jd={],C[=yl9J&sV&8:Af0j>/PdN!bl03<XlUQ2V9Ki*R@``7_+N
3[.2EKy![)ZimBD)40cf//)m^e$1:3-.XUFf~2~Ry73HdOUXG(x^Z68hlH,Y}syJ8l,*V*3*dipUYp#chq~j,7,)$Dw7^Xnd!oSGyxF8{[70.tey7tD@6v{AUskhbs0&o=C2G
|m[`Z:G)%%YYs;?*H%bJWl^1<cj5u&]y|@wk;G)^5ZaW37Pw,aIW9X4
Ah@62ejtRf)GfxCebyiTt$!s#eHtX');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Oi=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Oi=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Oi["bytes_processed"])?array($Oi["bytes_processed"],$Oi["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Hd);$_POST=remove_slashes($_POST,$Hd);$_COOKIE=remove_slashes($_COOKIE,$Hd);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($t,$gh=null){$_a=func_get_args();$_a[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$_a);}function
lang_format($xl,$gh=null){if(is_array($xl)){$G=($gh==1?0:(LANG=='cs'||LANG=='sk'?($gh&&$gh<5?1:2):(LANG=='fr'?(!$gh?0:1):(LANG=='pl'?($gh%10>1&&$gh%10<5&&$gh/10%10!=1?1:2):(LANG=='sl'?($gh%100==1?0:($gh%100==2?1:($gh%100==3||$gh%100==4?2:3))):(LANG=='lt'?($gh%10==1&&$gh%100!=11?0:($gh%10>1&&$gh/10%10!=1?1:2)):(LANG=='lv'?($gh%10==1&&$gh%100!=11?0:($gh?1:2)):(LANG=='ro'?(!$gh||($gh%100>0&&$gh%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($gh%10==1&&$gh%100!=11?0:($gh%10>1&&$gh%10<5&&$gh/10%10!=1?1:2)):1)))))))));$xl=$xl[$G];}$xl=str_replace("'",'’',$xl);$_a=func_get_args();array_shift($_a);$Rd=str_replace("%d","%s",$xl);if($Rd!=$xl)$_a[0]=format_number($gh);return
vsprintf($Rd,$_a);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(24).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(25)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$bg,PREG_SET_ORDER);foreach($bg
as$A)$ha[$A[1]]=(isset($A[3])?$A[3]:1);arsort($ha);foreach($ha
as$w=>$Pi){if(idx(langs(),$w)){$ba=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ha[$w])&&idx(langs(),$w)){$ba=$w;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($Df){switch($Df){case"en":return'+X/+JaQAP*4c$NSR="D:{ZfaQr/C;=pAjK)0r;!j/i_Nm
Q3
b6Gkg7lc>*M|A>TYws>f
i78ZV/G#S##&Y1Aew<%5Ix+p0bSjm.GwiEbB9HHc<En/D
Z
c5Qb)g}RfEyjATW*n>&a2`b#qDtGn7h+s%Q.rwpDj!-A]b"C1i5h+R<;u]i5/e.?Pgg.y=ro]iW$%oVeoV{
B8OC~ex!!-dcLH?iIU:7.neN%^rz)J6yFwRIXa`W/e&(HWX1uv$BUMA):v<m;>]:g-%b=YDfsR&.9vS?s
Xs)sA4(xxB_:O""^!/@_gRk58$"jSoB*^EXM"Y58;=O2wZTakC<&ZYi&z_k7{;q`A_o4rvfe3Y~JpY~>t9J<}IkQ6.EKww@)sv:e3e^7-mT.sUFBWT.o0xqpJ`I]iWYtGY+;

e4n(;(l7d3avRZ>"u/=Fb_WK5*s)RT
Ji#L5Ek_>%G#aS/?lA!]8t""k~_4C_mq;[kcDAVX/M8z`Hk
Xv+Fy+Iu?+obF*;qZ.uAoSSogL^Nf=)adabm@x&rC3F]!G
}Gc0}R|-`wc]]IMSY_9L{Z*qV>0@1ip:%]Jok274YLXY3rrAHCS8t^MtY$2HLoz*S+x`vOb`SDQi.CFm<D8]Y![d:T?7Jwu
kgs!FM&(=e;m+RSy
E>^Sg$9MbI&;RyM6u=fX%W47WB(mUJts!g`5c[]=!"oWr;OQj]d/S_Ie+O_c^J:|ofgGla1~CRD%O}hsA.4e;!8)!DDL]ykW3K&Z5VOOj~je0B[(
[D[md#WI-fCs=UB]3c/E~&@k$>^6$5f.Sj!d
tk[3Qw/KIn+@Tr,PkaghPr8uJ4CKRKAnHLp6:eOK.J79+?M@3&)ipHCHlFu14.dlf$N#/.;?L~cgpQJX%q<g&x58>OKYo`I>jF@;c/uimFvSdh^vW3i-0dSpWa"C9m#Ef]/yw5g)%2QRk=QacDNu+o)g)sHAU<H^6Q%s&IWBW08mCYNx)#
IRjS;$r,0kEd4y1[/e
[-hU(Sf)!ku1YgR{<48Y0XpDvqp]B"B$7="Hcg6ZEVh
Ne]|aVNmY1>G2=]d9G/8@TXEW,E%:INpsBsJ+pO}@C%!?x;(iqLEvKTg>U^jR6kr0f0#!`Jk(6y-RF^EjyK5)x$(H`7Z
I*nK}beXxSs;i^c#m
#yg&]^wvrmaC?l]n*Vl[Cx<.rI|qyk)YasTp40(N^:xPa+PI;_f+5EO1B#]rs&F/)^dEJxq"Qmz&}D-
R[R;F4NPp@5:b*<GMa{ou67HxuBA/FanKADpecSc
?JR&oEXirq^7IMA{BeZ7t.=)?QHscnK:`1IJI0dMKK]oa%&HGRr0(PO^$I>H8]@,txAn
!Wxb{s-v
8(U)lMZ-Da$N$~@%g^yTA5RBt&lL,,<[PlIlm7,IvP[4M[c!(_"1:p+SaDVT=};7,SB_h|7qrB7v/z-NA{?NC?q4
swv*ZF9jD<"C*8I)n)O/b1*4yl+S8
4rfkrx4k;uLc)#wVA:8Gf>Z
VU
U">A^+)yc.[WaTnXYqBWP=LffjswDAr,AX!8xd+kJDrM:uCDV7Sej%5nI_G6dK1TNHuZL:Y[C`TdWj!DU|f~NBxSE8Lx/j74.@(u9V*aUir)gG+5QOO>p{fT
C.+hLL6KA9
6q5MS>1:9r.cTC8[=2jo]FV/AL(:md9gV*G4Q{nz&w"fc;BM9}8FP![V4KbU1lst7(R#Mll?5R=Uco<~?egXSN925B%`F]qK=%2ctE$8qlls1n[Rn8[`Vt+c)ijB9e")J:`JxY,$Wo!^aEb}VXfTx_:="C3z/pepLysE<m@2"a>_#U:MU]F(^;AvkJo"$(.J(|X.]SN0lO:<WK+")pRcDzD2&UR;!)(f41t^3U2$Zo4<D5OcPsiI#dCu;E5.QNh8w%,*:F*~SwRr:}GMYNc`y$:XL;r{^V5A2vWFfmI:Dqev<O.&*^Z
6@"a@.Q&<3>TETf8jy(DGeY-n/85vv9^cd/}`S_Jn$NjnY%QT"I4D|BbOUgS1lKa<9!w:v^wOs9}Bx>D.p1BKVo306OiQ>hs;63)(`TV!W`;p4<Pj4pW6(w}-nk1z"-2<Jnx
AHsZnK84r:N+~jX^$!*P+(:lQxNH-jbRpw`gGSJR%MxdN<iN<
BRnDbCPR(<VY%yfi=eG`7hd38e
q{K(*,[o>S>G?,h>iSpOgGl&WvY^wu3:J=])EZV6TzJPWGT1Pmpp3xjsm%0*B~2#CmSLI;;KLl-.snk:,l!2qzAG2;QGn<#:GF<R[[n]RZCn^Rg@5IwZp/$l"29$E!t^1uLWr?-LkMDJ),*HO/3ZWw=@0xb945-><
(;Sho_QvPRR
_efDeOh{BtDr8%qH
#E+o-R=DxLzgcu-yfy[a_H-JD$9s%jPQxsX/}_BySOfW!Xm&;Mo+qki(pPidpde1R?9v5QI%CCm74Rv7,CCejH4VOUm#RSLjDI_R~$(8-_J4h.JU8@9kBR(<@EPOTS8pA>nbQfq:JlH.}(YhOXl&MP.UW1i?s*_NeI2bY[UZEc,fk&?NYxb;x_Ys0nwp5HCgaEbHxO=ijoDYcx0R9PPa:)yf0:|#T5_V!ipoIwp3b$[f0_D=5g
bLkp=ux?j*!B][c6vgJR;rwp.=aeZv!4`xne?q,;uJ${b=nJ$oFY`n)gcqrEi@U
=<0E21QFWA&Ic,f.H2X!v_pu>&$eWXeYs.CbQbwB%_g(;@a"$@xO

&fX0mK4n_</i7Lq7dXydj{^8L)0YVJ.fb)w2KhMB",q)K0E^($NSeQw(s|=*EO[w/DpU<KopfnVlScH&$Ah"_Zr*)`TH[qM:#4M^+#%oD"JgpUo`s6r
o!<RXO6f;?NEK}0(:X/PgW^j&.`
5T.c*a*q^]blI%/P]O[~c,[>L}*%rb^qM=VG7!bPQNT(i8(|/p-rgP.=N)NMCGa&_d42,!A/KU2_0C?1FQg5ghnDD|mWhIIVjwFx`couB_rd4bqHE6xwE]^KlMcseOKtpgLCm!tFNVv78{KH0ow|KbtX';case"id":return'*Zu@AbSWB&)kge.Z=0BdCF?V2B(@BQ5t5J-^)7+>Q?lUGVB=[jOyDsNY

}0dP%s).86ge/<GT_qf2/`Cq#*Bs/l%
[RM9yrmL](qlN^6o;B"jkVHuzN.Swb`2Q"l:VkFA]NB&G@
w26:``MAHt2:2Y/;R:=N"%<%LA"[M1m`(/l{>]:,4Ie71~c_k
*BO1XOriLtq;vq5nb/e@TztGqtQ.`K9hGbo,H-KOV(K`k"i0KgC}Fv^,f{adnc1KMAN4x1cZ+#-C?_g`)RJntGL~&p8dFvu*w<#yOYDIge*%(>xIdlfeSfDTNYU"w+U)czm_W=kfLFs[.+5Bv
pR59e}4YrZV/O4H/SWB68lx-BmWgV!].s68Yc}s*n?6kALpF-;t![q`:5`>1gjn&<%N[TU$qt<cN]+GWf_F^tNhDX<!f#dxKF@+2R1e~LzGT([Z$lafDZr=oxn1#9GuB%7@b^H=^wnR:v#&Gi6Yt%
-To97%oQ4o=LdP$<5i/S:"Bo%b953fnmF|oi+OwN
?dPk/mnf[f$h4G}=wVB$nTTX0M{!BdIX,BF##C5HTN(S>"u@%fBI5b=bcPjyGCc"xPmH%j
>bl69#AZoa$ZV,w$Z31s^)ebEJ8(i[[$hzp.pC)5!4vF6&[A&L,ziwO
oFM)]b8t+lOdxk5F[i;EF,^E:TT@fN4Z(6-BAa<0&ty9i_t8,Q"?ohPu$YT__^>1ag%_KuhQ@xy
+}?GQlB;i8i|BjK>yCn]Q1pZ5Pyz^OV
+l!)/Mg
k$#0SkpyGOy[bD7b*lu{<nb7Y;p"xW:rhk;
JpsBBcq<j5yaE-9PhP5I1#3%_aJ:)m*[/c9KZ>v~*naA&3dRl0+av?KnhC87:UF{kC5m9QK5u#2^PF4hYvy_.D;*y)g%d.:m5>k"mx)dA`.+NG>83kfwopR?#^49e]gsw+-KI*psf]^)XfA"6NF)KAVC4[1w"~G4M.F`da8FOma<b?N|V]mK!hbhYC7}#3(@tl<HXr@4k^l.tzb
e]`$$hYD;<aLg9mD_zonY"jme:G,uw(Vs$Hz=kJ}TNgr5pPHu6^AIiiHRrGi(ktI3/6e3bo9fr]/<j#K].Ex?7Lh,(X$PMlj$eyUN-*Yf4SpD=W:)wx=Epx[n{uLfIxBHz,$u_8Qo0B!4MUaO!:0H4F6j`@OaE`gQiCK^X_:MHP+I$Q>ZW`#34(OvN[FVskdd0qMsAtQ%&]=ZrBfWaO38_w,jp:W*5BsXhT:Hrc|hN1sodihdWTqpq9IR_-IhfoDKG[)i^;2UvM25%LwH>61%^T{u5x$R4Cq0l7yMuo7lj@PI:.KKqD^"2TvM-B=v3<Dd79}VK"29*^~,+T!2p<+S38/x|4P)Ptz5euFmJD3OIgJ->H,S5:IAnJ{.WL5egsD,(u)Ikomp"b[$!u#LXUc^=[]&<.X_jZP3*[MA12"riNnO.gIoYKS"m(RNSf*:*$X<X_F>:Nn2%
)E-(!Y^hALU[w>u1tI>:Bg*Ka5[0R>)rx&R1R<N6
!>>_ZWX}kGe"hAb0I3Iv!xjJ?/fR1~SqYwYG3TVbK^.O4PTI1:@)E.uFpAA18wM8/khuJq!SC7YkV)/Ft{`~-VVPC^TL"iUZg#BP,ZC5iA
mb2!(j!?C.A]>_W@Z8z
Qde;+v#DA*,0F%V?oSL,?Pq3bTrY!ww5!RL(<nVCulZLrr9i+Kgi/Z:I[R2@jZS[3[sDXx
7,g6d!Y/_)0N_Dqv^l.2!2!I>Z;*@XVAZb%Wx;uG1-i{?:y(a
!XH&c0SJRhtlCrF&-dJTV#SGbv^|1"S5[7LpYz
i1y`g.BUl=;Hcd`%?WH95i`^Pp$vhY8"QCiFTh&t
LnU2)!P~oY4l2sC!ueb}RjN/4G+LKMy85
$N_WK15YfG&E1l<
f7Kg?Z"&bi(a.dgLLZ;X:D7JY3:u
J-}t5>)gP4~cya$ZRcauMqqwfS0&{6V4-2$)pxj>=7i0VcA7=jx201FJ!bL#(@b2B9rR06PZ6rW@tX=O|Da,<[xLBWaj
EB2khJQR
z_p:qy|[AV&*$:{*F4rRSymw1:]t9A0I`15m/bQ2^REqFA"E/CY/zskMj]U)h""GExbC")ixhPuZDIU
II/yi6Y(r<z^SNJ`S"d;
pyI!)8l>W*rPNd.44FhQI1ae?Ca%g3rax^XJW}0/
sdP:B;^am:1;i&3]fh2Q!(h/4b*AL26^m=ek]!^H}n;1^!e(9c:Yf)EO@fS/"XIRWv=2zGVu-n2z&k(
fG8K4cJ8/U;>9OtEz2<MwDwiAG#V9_Vs:%(:v1]%o[Z,O]=R/[!*`m{ch(;Z[sPDUbs/*DK4!J-/C8q0S5B3!w0UNqjMQ<Y;OxI]r:e5M:)WHo}3CLYXxQ9@cX73TC3V(H:7fROk(juxaFqcqe$sW<pGxM$E_g}DT*yT+q;.pfm,2HX5h]T"V7+8vY^!*,<V"
-5}[(?(Ts./^44hc"Ru@>@W7V
j(Ye+J$f&mx_4@@]:S!.Bap^J]A]nt=!EU:LIql;>Fm[7>m+P]r%g+t%n!OSCNdIa(hi92H@[oLt5;[qM;:9<f3Xw!~WcnX*4(fbOeU3e0Z0KKqDo
+]dh@bG_F*T[+7T!Z:}=T]]<Q+nXTaKf%7seA)8dV2ih!K#A`m#.h$HbQf4O<I*okvp-1YJLM+-??V}f>#>D6Ok<2J;u^b,wA';case"ms":return',Zu;C7nWB&)krx(P)GRUn+l1]e7Qsl+D(k*2@VOp&-uj=?!/+yE^KcH;ORdT
y!Ox<Z@KP$,mpEgyLSh!=o-dr;t"3<k`uA9oDg#6f:`&fOrpvxdDcj@zCHc@.UFTqR%FOqr=mGESdYSsLE6>:4Ra^;/_S@Txi,GG6sy|+`qzw6e7kW(?>Y1i_?+BKZG=]&o2fX!G:QJ|b$!bc]BDKq6!0WpoEwSL9m4O)hJ_UWW&@}&MxVGt4_fNtfuzH$Lh(!_IGh90v?7tCQ%LkE:0Lf74=P*Q"F0uhRvwnNl!d*f+3<I)#k?8T@g*l.#>eIo^mHpW%O,R8X8PJGg;3jTzN
=hKKq7WA?TO2)r).yS</o5Wn=l*drOW%h7WwV-6vV#8oKW&v4;_012Evqd_S8+W9Q?Bi9D%Y&Qj7"*GLT#$yZhM/"V[~gd.:Ct&ndhK
4oeR&&18Q_p4<9V9p0:|n*$OUtJPCmpiW-o%,z@B(;SIez^=EIgB,nVG
orY>YwtZ}M^C{+H^wEB::G;FB!K[d4OVN_Dt72?A]-B
&qBhGQ|)<Ro1hV`w$,`WL@W)5ayEp39_4(~]x/wH`8_r3t,
rL-O;0i[OD!_5T4)58l9>MSZy)GE0$lvBK{.>P"?3xG>vnUT{a-A4*vakSO0T>}NR[3v]ptvf-Ax$C4OP,@`Z%;G@Q_IP0rRknJ5M1jS.-Fx%;f7>t8ng^}T"71!*d}Lq(-w!o`RQ5Wm]=B+5d%nhp{M,R<9mQP.loiJlSTI~Z=t0QE?uK|0;*V47BOLn-k<4FFG|UM+6tG!^B>0BMlWs5.b01KFU/,pB816x(ebU0u-1oMWRS}^2
IgS3W,^&m""MvJ)JS[KDv>Og%K"rHhlVcMbj-1LF-sYg:te^cKB6g==:C!Cb)cn2B3cat0+j_/?Ht$xye-^PW5),,A390`S[Xvi]{Tkg$USDc"h]O"EFd&uN
J%aM+mOhSuU`kzYz>/ZlT3H=@y/I%^-(Nwue@k-v;=/)#~$s!-,kymMTp+?%y%5i@_1|(~Irx;#nXp9
n>Ag:!o?7HK^cU%@DETi=#5&"oRRF$OmfEoY[Mc{<K2QU`s&CJvqw:<vEyvR=.Yn2Xr+fy4P:LMyG>MwKm0ZKqy"sIA69:u>NE4"/Q!v8XUc?araP|^C`%BrP$RwLN)T?Bo"+R&e-|qFf)WO-VGX!efK0Ec/,BLZFlK&Ao4l<cnrL+,wYpI/>2-tyO0.
t23PGjaPZZc0;0NA:3yU+=%5!P??59dp`KE"0FHZk-W;$[gDeB7OsS1_p?~>U`*1g,h9}Vd8/"DDhkeZ#EzPaftFv"-Y5eh)1YX6Nc!cP@h$L*2VFc%B0K2C1Ju6#C:>;3MW2]V+m&ByCRnXzgX.JP]i)`Ok8QKha.Vd^ekHcIi.PjyQHBd
ru$
0,s:v)3xoO:R83(Q9%l[ZIGl(vEv6(>n=4p)(y^,mX,;8:o@r#=#S$S)Q)5U|BT=%me>$=6QQ$|X4"%FoPf<H8k:Qo[KG5w)!d0fn7?q.8`loY@8H%OLjYMQP+P4}A$N$IdrV`ij(F=Kr]JxEguw[u~/J(O1>"2>+%."5FdU&?Dat)tCWhL1o_#K1haV<Mu7JXqF%J
k"tWO$q.U*sNlQwY1!X|5_3iQ27uMsL;x_(ZNs!Aj4^jH
)f8+aU@,mg(R^R)ZV!pbj3duo#B&Ts#^S{(K^BF!BE:gN7d;?FM~wgFD?7"q"KP!X]<X8
S}Chez>4GE%};[j*WjmloTOK*3n+*TYK(gkdHQS&`G9,EtTl>>W<%Ig,g{>d$CXURb
|2xSvNti*gq5,w]qZ#C4?m2g,7_NtF3W
)8.,]f,~ZNA}IV)u^_<YvLi(-5BT>qKfdxg;4Vb;*SS:,X>^by73&}uf.0+4k:C+s_qe)]*ve-U6Flp-pel9YNxWdyH[6w/%T_sr4!Rn[eIA#xh7[mm>CVSaIxV[#N^a&iQ}`kO]wl#`F%e0fDS^tqvjZYa0-*a`Wh>x(3/-o,jUCYgyP>4x")sPw#ob98f,
=yyCHNEQj&]y+SX$M]Mdt:zNkG|u3
ffbadaP9zls]:%c^Ynng)ylwdPWR."%*i/N
/B6<Kpr>?qp/Jsu((]>/`0@Fiv:)G#t0;<m@wB5FR(V^0B1y50R.Jp`Hu%>n4-ti<U-Cuoox|f#q=9f^v%]c#;7TN6m80"$8pH
;}qVbW(oe{x++yWAkR7./~0@bxm|g+B~<{*aS7^xGf`8bZsc3#qKHpAP)LV5_c6JET3TSI1EKiRovT$q(kEzAEQ/_;6,A[q<y+fyK!,wBSlvr)D?55^lrkOq630>-X4,C+UMMhqWAeafUQ*f]~VnQrXm;_c3]1Fc8ILfV09UkZi{K3H#?wfVH)viIDAHU7kh_AhKv^26Cx*x8h1-E!CA?zL9B&Jcv)3aG`ZJ]sjz7"S[#:UlJm>J*l#6ud3_&ae3g2uag#@?0Wm)P81[)N9M]aTFGcre^R4^@"r(t8E|x%KVQddLNF3%-.1`RR
p
t<["Q)-!_p!)Bp/Bn85"I-YO&J2i<RSg?XwZ].EUprK%J@,mTpcw=x
9^(2J90^UA3_>a#P)QI^f7B
G[@flm/%Z5Tlx(D"L,o4u)aC$7oPAkLQ>4$yp*f.#NGAV6s`]I952$:^,4RCB,d]/6mk6Z2Rq`HuD~NKXM$]DDg6&_v*yg8$';case"bs":return'%Zu@qbP+^.B`ofy#,N1ZM=3K$!m)z*XEiDLqJQ{9Bn)1
So;:%U;Y,^Y+p?VDrtyK/hK4m`kAQ2k+-AEQp)ec[B12v
x9M#njf#4u7P][cD$Ng{8[r-yS&9+#fUGUiCeya;bYYt@c1Fln
GK*PZ@<B[URw$6-DkvLXkjeW=4.:(tAW_.Tt#)$AU`p39Il/Q,I3F[_=N"lb8K/*MmvXQ1Z`R2PPlO9C.bev$S.C#2iM8_fWFm1Gp[d<KXNrK^mVY2!AQ"Pmkw|.UTdc?K
s0nK*#Z/r8s#_xAdP<S#&Z)WJ0+"&k9Stp/M"bhg,n1]Oc/=f1a}t$F1eJvRD=UT@M:W/NyEVe7TGLu$1j*oiq@niZ=&tV_q-vyXtqt8U.p5HfgA79%sYz$XgMyR,=+ZMW&`v[cp<:M}u(_~V]1OP[H]FK7.PkBiPVe]KMS<Frl+>7F;J
hyP1L3#5N1BmRP8QV$ErLKdZR>tq,xk0
u"Y#9Iv%==7Z%vL#D@o?Fl64s7.0"aoc:QVXKW3?{J1xV&"*J>iO5
*n^GnZK,B"xVfl^?glL-rE@w`Sv#l/MIuY;/H/%O-[
DUp9UtM"?oc}B4(jssh>X.K3t%TmRcr?K=Qka{(~S=E]=dFGP<j0g#XP(qik6j8V;g)^fH*$k`<Fa8)=x`)6Oj$9B].HG9+P/:`D)gqvb)!WJZijm)g/D:K|QrpfwA`J1ZIx#mh5gSh^vh.1ZiP9gEeX_&x1Dk(bR<UK/|$V+B78w+h&iy
b6J=2PN745+ZkRaa[RU3aDpn6@4HV<n4&v@i5[MX_.B#^A-G~"K]~CEKNj_@{Z{syQE6>iYo|a[CXWu>b)/GEdwEUTdv/ewHb%VZ}Bsw@*$5h,uL,PvteZL`Ow,K)81V;hqo1-hM
1i"1yf+<:b5.AbQmlT#BmXs{QxA-sRbU9W+IV3mntjx+o|W=m`h/O=c(ElgcT&>lb>EF_VAQF{b@o,Z%kb[>Vn@<v,^_<}?S.Q*QIGC1**lo]+#-#g.]YU&X)*k<]AFH;C5u9K#QN""3#ZK};$n#G%o6M8#@l+E,jml<"Y!1n/^X*)C2_2]WY&%lA`#Ahb%e+;4rp"(;RtVJr"0=>ZGmjZ)cTp4NNgJqL0#Df,5Flv6R<g`ryDn7G@@7yaoy+7#"<HGbz&a.[CCN?pi&nv/;jNN9qs#*L`q
sZw8GfbSum148i0|OAVV)RHg_3q/S2!E)o#p8!Plo
$%=+#4SL>x7@2YtPLSo
5}a*r`8a#U"VXE^$Fecd7^cMP!&"r6t_aE9G8|f@QDt[fS_1"|eg):rl_,_.g,K_GOyfw%,@K!k*`g9`J>X0"Y:m,!n=YoHuX|P15&(KLe`kh]-fEh56YHT485PR
|Q!:yF0i;=txPPUiZWs;QH*@Vnpf=C%wW2eP|M@rD)m/y0PV.:7nR.DD#fxnQ>Tf64?&b5@CbSot5t_!.^j9qQ#T+q4G)9rVf#&(U"h5"0ZY)"JVprHLX;9O/9=TcQ9)nWdK`)9X2G,;bYfM$!ikD/g*iZ7$E*]k}4OPrEYQ9UQ#k1k)G2=u=GOk&4v>)3}R`Per&Rs*0_pM3[t.gQ4NbT^i#HO!Y$iZJ_(0v-/O&h!/zc@gr&Ta!gsb{[F!n,)0):uP$-z)uu/Hej}Uid8!6AVl[2,Y1%i6l^YhWxjUxY$w?Mq^%f1UdaomI(o<T!KdVW=RoG8GDZqY2yK3w8<S_+FW=*,SK*5KA;sL-I
`l-ZKwK&+A0t"GX2X
a3Fsd}l*2oK~^9*T>{=aNHZR?]]K]}efoIS|c,9J*kuivSft#=XPyHh-*C+ZJQ[7_JaL5S"}-q*/NB]iZ)hKlxYf9>70a9ob-hI[J%Dhnel(T`G_3Fyl#A?E4Hi~4yuYQdQIA[^+WymN,y$P;#72"o(wCBeWlLKs9Z?;<iO
=y,+$mJ22LKAZHg-dM&G8>Itx^ka"w>E4pFo_?;U2$RJc4GKv5/3HhmSqC!0/{vE97/,*,vT`T8KiVyQ)ECYtFQTeqbDgQ3o$S1dR
Pj6?#(r13v0`gqYV%WPJ$8H~L72l(Td2k#y*r*41@W/0_Q;s#QI~hR"|gw
VWen-WhTwCEv3bk[h".o>hH>_)%K7sRk!bIx05CY#mPf3F0-vT;[~58A4ZxD3lU+2%w#rAS0mG^v)hg5in#M;YtVw]"4CXp/Zdy#W1%[H
j,s%<2Dv-wFyC](IRg"p|N:X1KL-R+yI
MdP;@$QM[<^|O8`x2UMXlbC"#@&xQ*t2kC^)
dF#5S/CtcHfaGjh:J?Leqy8[Jb79%dl-I7c(88jNW;G%C/wC5m)9I?*4YliC`-@:,SgV^v,EoaVguQ^[0GBPFMQ0fSq-DS=6^8@DQ]W[S?gg7MYYSml;5M<T&8vU-Vz=/PN-/)Cy>KsH~aD-(+5PnYyDH_]h--o8`O-<Pge"a`
`i^DGy"gNz6hK#.a@;9iIv3{_/dD%pq,4*CpxHkFuT;4J]Pbs>j^8Q!J8h!Jf
WPi/nq,a!94_/Mz&9|4;v#gG,)K.BJe!UywDr1:TX6)*.f?cy#+0tS"t(*@hlHgbm=O%ZbeeW+-TLCBD0uTU4{/p0}sbj:s)`SJM#{5o`sx+mYX
@Z6+/y0;NGe~_qq~fDZ^fXd~`@Zy/2AZGoLM:d:YiOb.ET19jgKl@<kUJF%^gku+^?=X8#DtRoXT7WR2H%($;0CcKZ38s/v=P>G0XBT!&(/BK3M6u6=$UG?7QZ7h+xp5R(q~qM3>3oZ-[sDtK
%3;(#rtq<O@v*i[tJb.sqwaP]UMq+SxEn=ZG.W^qB}yPmsBJ:,YCV~CF"CYO50b:nO@a
;xZP`K#bLe/#BKHT+p+wU-qLR]fu<bA@4
GK3g5Z_e$^t1}gSvB6Zi4(u!uw%39WO.b2^i0WnbbWglphS:aXZ^xm9[o/9=5e49riM#{5$D4>WL9oA3]H>GS*m#;VsY%D)[)_sUc15+@;f]zlo+{,f9`x.0yCFU!heTWl?O($Jb`&qqxxDvg_uT`/`LOe[a4^1CAVz$hL@V-vkO[p+p02WD/;=Amk_DZKUQIGN!2[q)S2E"9"[fvmO`#1{y/`^8-^}BPn=2}xwSJLX[?Oa;Ra@7J4^*LbPO9cY;8"hWt,dJ1kY?iE.@0^t)r>6]SCw>-[7<j3*)OB#Oj:`v]d@';case"ca":return'(]^@j5I.W?SK,g"1u
i
A/A35,U>PD@aLR8T/)2,#7;63UN053KGXo,yq*)2s<^TpNVRK*9&<M;vfhI)A&/Vq+T9oQ$S4E^mQnEAktY!N=Mh;Vw$TrmKs&/[nq}6db=jZ`[Z@SOwph}t/4efkI2UT3Bq)Ur.ZFP#.4-iKn7WH%2H3XW!fIGJ}`I_t_l>M20P}YB(]s1hqeZ_467ZD*@W:o(4-d"l^uN6QEHo+H+D>V06vFw9lMN]*56nL(cU`Ane<M_rzKpy70ookf.e$({s?gcAPut0/QGy(fE210EX6ASp1mu=;t&3<8(d,8!f!ix:=71Q%JM%Kn-7I9x6CZ{D5yIUWOHFsx?+Tt{9dIJ$/_p0P_%V#$Q-UHNOaKO,=6JCxw8WVb^yuPOa4OC$
cxw1I-bk^^=#_Hr>`$25jpS
/eafU$TcF5vH+):B>BtPD"Qw*>5Z=Dsu=5y*BafS.6`H)FZzv{(VN/[|4=Or[vTM][_GcYoL=5Xibbg^`m7Q+7XDyWqmqd.x+>m)%Q0CXb[3!RRgeG>|o:@aNjT?X:.TRHSp0*q5ZFie^Mwbk/#}*WX"/qgcbB[Wp6.<<G<7v.B7)kDqjp[oF~xu"A=F29`ZK|12gr!;Rs7u"2u+n*DNFEJ8%LxU:oF.Fq])C@wDQ/[_C0=
h)[[5{lQe/E|I>9Qj}6jj
90u@DxF-Dc&$TxNIAAL!@NMqBiH:F>.?7`!bWoDbKHCL^|K!
HK1LS/E+R<IAap9AQQMX{8NBr_T6er]b@tDnk#<MYDIr95m^XesVYPA;%m@TfdG
U2VT|_X%H^`dz
`%,RMIR:~%TW{NM_}4IuaWXpH]bxRP^].xw6a`qH%JCI61lnMu$A
u-afZF8zz(?A]VPn#57:15=.&n5WPA0&ScLwV]K$;HR[K,*Wwrsxf|v-[yT7`ZjoYj)K-xfTPy![H5OXW7e,d[6tF
^U$qk,]>imh8]d%Qu3L]57g#O/BS8A-i>,@i9^2n#x:0a-0D94<Oh}!EM!ca;uMq6`-n]{x~]GP3JvZbPe:jP`.!8BUltubdH1z&O3u):0,Bm)?MNEZLw]9+x`%c_6rhi=x3DeeRn=Mq/+da##*]J4&f9&Ho>RDFJra~u+n63)Cst)At4iK{+LuPjNc4tJhv)0vOr.F&ZN9/NF^oPwND`%^X]{V63N2jof+VpwLCH"@e+qU~(0YQsAqpuq/&nM3MA&7fb
n}gPt07)Lm/|^x,A4f+-VWaKf_j5EMeJn4tV%Lbo71j!Xc/.H>Q8<b*[@-LfbCKqkL2=@nB9-7I-_}dF^OSbt~m1c8fz32JTjh>jJ!GUhP.^inOnx~,.8PYDcjKlg,w4ZZ7AV,io0JB{
81LV?3pq.`n-#(-ht[HwMx[_rw/>S])b^$`NhXOLZ5<Y`&2N<2)]w<$#Y?9dha}$AVu"GMJ-?&sRI[@2PZLat>sX4Ttj}7C,{&?c]E&-)tD?".f)g3$jK$-uJ.6V8*qU2E3n5,qaN#i0GJNtAGq?/D%^Ra|.XY-qD4_s<$}&MS.Y/_yQTfz:H7)Hvl63+_m:EHQF}<l6US>&[ofNm5|.@Z*QCZBCFWdjDBC:]mJo(K^5t`GSJ&i(4seFl]VSNE7N>x@$QqLL#]@gYuL?"L^v?c(`sB"jx]VP<BRuS0H.dfHSs(gEz=y?h#`VGq""P
15~R?S4d*KOmOK,)D:n;}G+y4SfLvfp(YSUj[+#$YOy9/aYXT:0s?P"0J6(9U9h4aS@J3%#bP(1m%@RoH5d-+t(pG5*W8U{,G?"g0.xf@-tKE3^
R?]Ak*.;@(?ek+oqe[)]nVBd[$96&PykHM[11.k21Yrf3/4/gy,JQp?Ri^we.mRX6i&#Y&cBFK^F_&$"l0)c<9M*fw1s./.;&R#RcVYh%P)enB1DFr&#NRbT9lkA5bjl6?

l[)_DVwhaO:pO;=@SZ3>nFM-fWLr%>V%x8MrP_^h_csQ/H7=}rz0U2>yI9a<(&]P-<%I*06P~ffZ707R&@:Wu_9=cy]ij%d_qYgUs$c@,Jm+,cA+xxP`/8D3|n<f)><V:g*kG(]h&!0.Yw<YBeuaW6}c01D-78L+.l#F@yXI~9RsJyNrTc`!O
44$?"xud~"zNU9_uZJEDrs_2fRgkbDL`Ya5xVy=1>w|k~xLt{T5j=NXALO-#6DAw<H~GL#om?.
@m@h`XJLuK`Vi<Ika2+jBH",rW[J>M+7<*@Lk
q5szyAr;Tu;8e!$ma@HXO^
#D`b^#_R;-cx,L88QnhDVcd(nd3pERTD2>CMVDj8W2&=!9n6Qm%wiWm@PysU~3)=<XMNG7N$Bo+^`q~s5=g3LUG;VEJ&][L+V8S;%j^T7`#f#9-1XD<SOwx5!cFR`X/^@9tFaDsD0qI@nV~mLU-dmh6;ZkSe=OfZ[.UF=yKG6_mU6=86n>85=RZ<~E<h+<&]L[g@(j&p[f7NqR9ku,_%Z@>%ui4/XcIx;U~i:mM)W$!JRcPZDBH4,eqTLy9&Mjw_SBJpnZfk7DD%
"UQ?9vcZ("cnotu7s0%ZYj]b;&WPN?SF"VZa=A@]*V.WxPQv_?Wn.
3C<8=m&y$o<Wr:q6(R4P+^J#4;gL=,C><Y/i=2X{fh`*gjf%&bENaeorr{Q/a(/c:w!o1!mZ/aa)=3:W&|;<:3l#e;O!]O1vUaCI53gW381/4,nrqKs
s4T<rm*TZp%kji$(e|EaM2qBCV?|%#5T/T@aYX"PGMh|c:TihLD,asbNMI6q_oY#I#/,Q`!2:xfBjRYwvdUnsM2"S}8r.RhVh9.b!a4Bv4o:[(m<(AkhK
wu[:+1?Ov(ly-O#YfbPN_+x}d`FxCU/?O3gG"dA-TU.)P.j6W39AB+c.ksNib]Q/$aJI0@2c*e11Up`%O?xT_3BbtV[qa;H7]>6*>"l_UVQSe2A;4ESo"aCjx8W=Cm=pA:WN19E$4q(+EN9Q)m=|Gb.kbF(Tj0v`>!D(;,`V4Xb4TUXg/cRoG/#z,1Xor>`Jb[rmiDSW6
@RyvWwJvE(RxwZ9lJNeX^dr.GJGKiB4E",oxAi_VAp#K?)E]?&KNmC5mbn0[G8*|hMY#%K';case"cs":return'!]^@)bTDI@W!(ie"]Vx!QJ7*QE8#v98C.Uct^g:;AFuTsW2YF$+7e&YL|
$J63y&NC;C;T:E<yj2P/Km8]@?Akvd+HyJ|
v6[XSJTwG*/VD0nRIMzv9EFrz%z)dHgy$FLmm1WTFy<mFmjw]F|Y7H+yM(hgVrG(#wyQ|udmO<DOjsXrnP87JqKr:o`kJYI.Ko@mRdGo|vScjD!iDSO!(*5-
2-]bk;/1jxxM=MSI:vP*BZ=&z"tOut6VGpj7kXcA"t*-JO
YM%wcJc>qamq
,hJ5WL^nXZ&geRXL_hP@r"Wn:"H"yhCNt/@^tD.5H_([q)iY&9V-R@t;N3umX`]7:1!!rzHbAvb{VKVvo}.klug,h/&E_(a`jocotlsqm[cwb`l]x~2B;iDD;wP/_1p~w4#4$n%>ZoG#`F#9@;/cjay?ptL"7V`K2%K,"PRs")F2FQ-zUunK(qs5xN@6avBJ<|H2rWDmuM.Q*obxosK{6c]-.}DrqI3O7wa`j"8Q/6=TKh4-FpXYAWS9rp
irgt)PCr"Uk`wd-/%_L1},TgBuKs3N<-,3+Ji+@f&(+6[<B@OsM!<y+M3Z[`UW(<
+lK;-,t,<MyPicjqU-$>S#Lrv_qsaIRtUIR!P3bbJtPdtjAnbgS(H-<
,5P:qZ&s5Pomqp+:U4BMTShZ[@t}dIW}
6Yab
WU/xgOn&(}-6$(W#)?As9Xti5-9vAD
vbi6H@s`iy4xW#=QdoVI.rEQ`s/1J4u!?6S6Bx2d$u*u{sCc5$TO>sC02Z=T~6
1{PTMZ1Ptv.d.Z=g-WAdLx%]*rNYG~t=jayZsF#!?"ABW|t&Dmxj%DgF^;FJ0}4Lb_.k@jjDbMe!eq^,C|O^*<O?1yx$7`+]
xqx5zICc~qt_S8T6SDHK}4=bOcx<UJmbf-53To5C:2lho!/xhrNYv,t@V.+/x"C9+28F$o{BdDKMRbUU&9AYV]%2cbt2ykJP>tn8__W&ha,yrSy1>]lCahDyeP;P^uO38<f`ioU4xBmt+_U?p=|aifj>MUMto,ERp2@
-p-?a#kr~njF/qn"XP&"3^zD0Im
a^tLIJI+~!z!.h$;SK<M9)+x{RKq5+`yO49#
Z}aO1(KPwn
u.+x,]<x2z!p>-YsqA4F%6is/BFG(]xedmo6g;[<6nq/:l.kV4oB[b]sKK86SY6NkEd0/#4dWJx-/gL<-i/<X.e*xdHPAaMW55Vz$t-%2eJ:gwl*Np
!QIGjXki(
4]/%[E>4]<],/quj;=]jq*72uTZ-!dsUc^:Z%SUL(:3ez)iO_,kepuM<g|#0".KFfiJga<uxNAz$P[vkYA]>V>mv3N>{^fwq?Umk*>X:T)/ePTA,:n.$YPd%B]p_fa_VOJ82DlnIOGLxkV6My@j#FM=d,VfO8tqYi:CDeOa]<A6e"N<9VQ(p((,ceHOZ*6N|G0jW+ZY?f>".coehIC#%@=ac$@B{26d8Y5@?EvK?T0Tx:JE=;JSBn/)nEt,-="-&*cRR0qZ)<_SjBeEDJ"0r9e1_OGm}8E9+u"G$*jub+}Zi*Ckf)gTDSx%:vt[%ltse%8I?CiK@+LCmL^JdKt5&BMaW<H3CcH(rEScCQp`u,&hD`W(KZLFob!_6"lb,$03a8}Lr4+%RA(l(8>p##D?/aU)I+tY^@@Y[H?-ed|!!QpO(hg#^$+o2#E+OM
fp&H<I0wrB8yN`$PO,G;"0plLQ+W<!(ofZ1x[G
nfSp`"g:"WEA#T^`QT8vXqa@w8hVE1<UC7;W`Yq>gA)UsD"BdTs5dika]a]2R"RSA./=|-=AFHtbZTqFF+]$qx.N1E8nm3BLh;u9*U*y>//_~^XKCF6W;sE>BOJ<?*h3c?KR_nD=d5>Q6Pww)C^W)$gH/J$4RNF(J9h,33FJ*$QuxLV3A/_ZZu;$NlQH4/US!Eo5@_
?+D@+Rl//r#-Y|fzWqZk<UUY7E14Q!fV:u=QnjJ7]_Yw5X:A"5<rs:h|KG5~Wd%yOFrv5_m&@/;P::q5H;X6=r_Y36],6bN62Obm6H@rlAL,RMOL6-9,v`$|D6h=ZPYci^8gZ!G_
T+VWzW56Ak:kRR7"N)6LCoID]ryuAOP.?hk`L4gi[W%T8)g5Hq~VC@J(gB=+w_:jRcsxu+r+(/G"}m#8pr($~
LL^qu_[/DAaaIpjjIr^&woJH4+:myYDU1Hhf/bXpdx&P|pn=KA<a+Sp%Fw(,^L8xEHN:M.vO6On5o`CDObEUp.M(H41q`EKIbkIeW+?*pGAC5EaQ"WfDAN%GnxOfOeiR.xNE`w^%hgA-z.TOkVKFwCl?sk.>UAJrUb3ZHs`sXMA#:,X*vAuB%Fou/hbi,-B%&6y0M+x_7PS@=R_MeMg8<nl("7Y&2&AW=%#!:E3bk"z;n=,)y@B--hn?1g~G>prR8!?p3B:>:B;YT&)GZ;*.nB)rhe0As-!j2H[T!=8BI"B>OmENVe2gZ5nhu
_fQLiZ#gihwXcyavCke;|&
jf?lkU[0ZZy{fYq,!]-#YX+A,PjqE=jxkw-blqQJ>CR@@V%wosy>F1N)

aPUm8p;S08:skn$LC94""4mrjFh/g&9;Wi9e%Wq2,~B&,Tsb/c>x)*
8)7mh(tfqbgG3K)+ykfM<tbw<pyb]w.])xXmILW-kBh9cWOU=F
R^3WG[T2)PQ?:N31@H1988W69:8V=OILWz=IJ{M4,j-+FUlm%K<j=RSx+pE:;`/YQ!c(Id<l2)O
9N4dezS+AT,Y[irGHz]L*}[aZaVd-DlQ8J#%0"R2q8eWq5H5S,[NP3pA5=Yn>r.Lr,HIa.O`6*5)N52r*m:9,q]
GL2]xerd+)?nN/0ww"n?YNiQ_bRL5R*CSk,zc,$B?G]v5f]?1-$om``z<3Q!qIy&)?oaa[;`P_@FI"n&mrL(CJ+~+D&Y85>"47>})F9@k!ugI^xjl]SEp:[/U$2cA0vh)>%+.(jAc--MFN?4D],1e?bg;zk(k6PNh&>qFFdb7Ct@`?Kbs&OX*$0kpUc6[6`:%uWH7>weS->Q6J/-s}j~MZVi!SrDK~5W,hF:(^iuPKyT2q^t@5"s;wp/jOyrvJBK)$m-]^9jm9e+x7[NMoQuyG(N;%;N^u^]`#dg`
;=c~Ja_Qz"vy)(9tRV<Da~ecTKPyA;d.w+dz?MYM!3&u"
gBILP:D5<Wg#D?7;)wbd"uaoJd
lI]l`.5NIVH[GFhT6JsOtd@H&0I7}K.#2JB9ViFv.5Le}50:k$LQYL+:bl|:B7OV-e]VQj"p"MV)qHr2=Ql-!pT=<`tOZJuE6Q-=:;VNGPqu_jKvQQ1v@oP@;""';case"da":return'%Z}5pbPDI@G^OU3P4hMf"NRO&S]fd1?G~5K,7f=$=F}=k]KGcW1!,DZ5-/DE`E^Lr/b/PnuM)+KFtEg0m_""}^Ir}XRyFA0oJvm1(,<1RuMc&XSMnXAKL9-)eUlE:A<W1HQAtZQtKnob)^`_Is(L."UXBx+PH<QN}azHTo_sW&H[C/_4iv?"^n95{lw$IkfyZ0`JH^K/hz%i6qe,WH:+*<Nw],cnY)GpgdeFne#ieln!BqD8Ml^^DSWGBf|qP84;BA!p3"u/5`cCcw`Uci)5$7#,?a{;>YS"|jl.cNM6/7Vgm@|hdR{G2vo>]i-,?)Oy~<9i^w24wSrI_a.@0R0`
u,28eFHx;L${k4a(P$CTOK/fOvc0x!1D_>v9O@@w_xo$,C_Rl/;{Vf@.ta1QhqYuY=UMskftp_s>d)#wy1ilm}+#=3(u?SYBtS7|Q|@U=ilZApOP3F6<Js4
o%_gXj!o9oQ3-[n:xTmEL9S~Wzc/UAUxJAw4?)hske=@1q[^"MA*aV6c@
AVf9esh3AHKr#,kJW`V5m<dmf4UnxLJE2AC]+uBQEb
Zs1N[-pde3a)nu(Hx-~$g;{Q_M:2ooE$;>@pXjoqF?fKbu4v}Rw$|x
kAmx(s*srBfBD53[i&gmA@l]%t4wWt*a8YM1W?!qcyLb2|AR4m!IwI_Z(4S4S-^.7Sd[9^K3>s"iCbW,mi2$7K<lARaGVGK5n4[!?ZHSm::p%zI`[6T#QMMMu>su^6ykq`NKt<lAyq6&VaK0h]&_Kvn$35n_Z0&ex]p0lCMp>pybk2`|/@X6=q5EpUbQ/WX>7=H8BVKFLQH~uHxx5aZEv~c
@{9NpG%"feEr]gU?_$L"4D_#
(=D53P2.?oJ,Xp|hlFu]U6Oa2U"C]4FJ6="GtpcZIGsQ65;@Z
4I|[qoZgLn)g&%/o,%_V.RvTiSPri_6&59A8t*;i-="UWv>I&Tj$yp|K,YzMdqNGD_r=wGY:,v`D*eiqf?"L1jBknw;u"L^4B*
Y621&,x)ZWdz!PMyMb<Uy6G=O;m8%
hj&-mOh11VlrQXOEL:viRmf~GX@9uUXagGbzuN]^V9ptqRI&L@w`Y0"6;{r{?Puhl>x-u5eKjGlby`?D8q16-e=N!Lc|jX1opS8`QxY.])3)Esp6d<sBEUsl+sB3>YXD.k3UJhyz5reIZfy_>KpfKmkdKT[b_[3<(USnx}2^ORGP]?qv(l_hy+5<abiIGS%R`HtVvjOwq"cITEtYJKH^Yc<25Lq~v!m<(G>ms6)4CE76[Bfi!xOvU4CAsHpFz%x54ca1jpc^P-2_M3Dw)04S?hirKoIL&{AzVhL%K(%iB`-bd+:U?Ux_<oZ4[&OA]Oe,-RAC&_>8jHf|71q8Pc1k^.7IdO:HgEx-dl4w$xl
A>b25u,NH3qk2
&ofA$~ad@WqIQ7Lv#T+V7p
%s+r*`=VwJa-=H-x
LR5X#{_Y,J>Q`b3<.giJ<6jh_I,"^
TAE<fW-D-@NO+DwU.2x4c$p1Ykj*S"joJ.i~
k@E<)q?+|hD@>M&B%q
N3x,,&K_%2mPwFpe"h0`/PE(@[1YtQYoy~%MX6>e1$MFvM
-,U]+N$W`SM8QOM]}hONYR>jz&a^($>akLPn"/
^029f:n*uf_*@e(gL7h;*|/Gc-NCu^>6uHBrlUPTxlxN@3$mJzKop`(kxBBfjNoLx=6!b!Fr69wI4_^B.IXQjE_^G%t{88mNL:MN94kz^fbPAmdAVYX:mUy7QO.HvnhiQsk4gNMwvI!}wfeSu4mjvAk4ww*tyu;xDB+/`Pk;T]khuY,
RdXXBm+&EE^`ujQIL6Mow-TOC?#eob%J07Cf[KQ[
nZar+V9BputMtJ7$v4I<Xb/@&Y^2epj6nY>S&<wMbW/]gk(&:`vT-WLU6i[P+2"m_,/pRAZI}ap[)+uR;.A<"Nc+{/y_(U}AH9Q@yuaDli+"5ooKH[+Tnc$*.wO^pO_[(rS[ERf;c[{B/:N1~SK3,aFfa?}dSaRg)?vuU4uBhKWS6T3uOU7Gq3Xw[g*8Pt:/[VIa:$yhPv4S,wVBs/~4BT8`#3F-b2
0Q+RFo_72WL>5ZnBaGQyWY=8B<14w"VE1J<3q^/J"XZ$,;m:r|BS0ahA)_6y>z+v5M2ktkVUXoQ$X)K"LWtV;eFtuUjry;FFH!h4.1UQXa@Z!}U-5|n0d+MfB7GE*cX/TFlxwYq{^A.uTZeN9*t^P-2hr67EFcjHuQ
Y+4>OE59.v7ca(<_)iEFCa+
"=`Kh<cP.fC7CtoZ5^IF]bWItlz*yRhwx/"W;2BY&;[9,@K]8:MXVeOOl1f][E$I:/XUhZooK]gBCpP<jD{=&1K"`u}#%F:y{@6wqqK]/j)cTjtp3-m?vXNU0Z&vQDut$6*!c/H)6FF`ptU;2vc1Oug#zJyJh*;Lki#eCr5J#H&I/,!2%
7r6+o_*r!sodaRz4Z`Oxt+=ee2Kr;+:yd9b"_Lp6;?"pTQT_mh./v@Wt^wG4?:94C#UlrSGwj:fG]<^Cxg-;g0!KR*FM7@Ah.riA6i{.kQi#zSNT.iVQ:wCQFJ7h,#;.Z>87Y6w@dhO,<x7gn1Lv[s2wPhonZ&K*(w|;zy9:-p@JY.,[x"AR&u,Ac=G?pr6g"uGM|:@FXuX!4`W!tjeAEIjgJsGw`
{kYqXVTi<KFp,aj-!exDp@KGi.j]HY"
Bi/7Wj12p+@GoZF8aJWX|';case"de":return')]^@iaTp=)Rd&Cu;m#+eK00;rT@8(JdJ>xU&E]4G]lPW=S;*`2spsO^9~eW"=xUvYb
<kwS2K/P?WR`,H>&)Hjft54Rb9B/@&k(Z.tR(q6;WJ]C]6cMQBTnQbW~MX7^(5
j-t&rk7>p0zgD5
y8]Zo,wJa^X}*Zuq5OhR$Bt}u=n<a.eDPP0I_8bYRbG#0!HR$|DN.ouLbHl-
G,Dkh*WfAeG
tm1nvuz4f>n95y2,@yPB#Np8.AVz!Gdv
9um]<MmmhG3Ef-HCSckFtq._`1>#CxNL;v0haa-JZCXbL_=BFVAF*7&YNtQfwP%V#1WL_Nlycg2WmwB;TUaX7i++4dQ{;t:?3t5-#;ec[B;q@Aw=4T
!H[j}(i-eK5pX;:xnz#N-EbfT%sCX,!uqnwjrC^0,%,WjpVe-tFgodJb-F;-M`BAWBNEdI|sB,~k}Mc!
==d~Bu+V_3NpNA347!^7)Yn]FX=&aeGe/#$_Ltv<?ko5hpeGIQK2x~M0!N@i%v:)fl%HAznPeWm3_/9_JL]Q"}g+KU7zN"6KY|iN!?HOf5_8HPDmm~g(Ixer
@m[y<pJN#%;Zx(PSPEFG!Kc1_gMF
1U98l=&]EO0^6L5`Sr]W4I,e=`5kO[EQ
`y}<
N,tP><#-VZSE]IVFg4X1X&r3i@WTQO^yPBw1!xCPWDE:$1A3TkcE<@k&:^^qI~xbE5+ao:*9
6CanFjvPi)Xg{MGb5uqg!pPk$_*/56{8mh-e~y<`8L{biJkCsKy=KnXv2a%^_
LA"w%F{KU;s(S9A;Txe9}#&TN[BUvfRW9F%Eewp>*ni70^Z*j`yc=$~ElDOD;KWqrS4!B7Pd7dbvbJi+H934zt1qXy?UrBB9)dxkxXNuBAEwzz#b"8cePZHri^Tym"
D!r%dp,KDL#i$|T[w_bax$g?kL39k"h2:Nm1TC3DXG!?5U,=48(n(mByG:/d%dpoY{47xcjICR>5lfy~puv6Vh
{SonNybx+[Uh6RYORE_!GPBR!Xg$,^dZJKM-[,.6ih-io$0LuA5o2wqaU>PBoR%x!i/&+xYQ?bDoglx7A-i`]7FX.N74Obj
^e>r[q2+zc!E~Ex&:5p3!kQ&t&;T[lED6Jr_tP40yJaq2j_audEW6
R%L(P;n,1x9!t3L:9LC68oVo>n^&f9VU.&6Mustwhhq]FA~/"7D](/q1kq
w:ZILR,l6-Xk]Jn&`$0Rl;Fe%HNuOD9LM7x"2Y38!,p4m#?Qyk@EUVSeA>0$QUcxQhS)m%oLk}$(Lw5fdPh$2DlcUn-#VI,jYQ3sv}xIa5"/W4<l;~j%3
Ugg.@5L.1-tADR)O=)fl09X.8f/dt=awP-C]=^m1huRm(dFtb^ok[c+7Zi_0HuJZ(O"X_|kthvJHLPGKlW#Tm{xhE|`bB~U-DVl`@+07,X!5s#B3tnoSj-eCrOHgt(NA.P<}Fepyv68TYt
qvR8Y,$vwR.^_QP?[u":
RbUYaFi|!6$[2fgwQ(^,8*D|:~"Kr:7ArdgkMlU,ms8M9WeK%[RgSL$j;Ey#J{AaKA[+0gDN&#xg8[cxmwC<H-V:<?9j@3u5,8/kkFP~uA9JN]9$2zsd3ZI1/(k-SNfA,ZT
9N.%&z<A"A/T-9l>32`?..[*oG7gMp&4psGE,[0F"xK2NBRq-uE]iyUP^6Ge
#N{SBPr"w`bZ_V,m/R/b`XDF$]3QrWp]/e)dCDh(]/gx64Jp2sLO}j>%6@D2-vd,z_wh13^*Y66=]Q2hty2^YPk<Cng[W`n_N,
[N_b4Hp%u/
viA>p3H96;[>I+C*+9B+/+./:a[6WX?0Lni15ITw>W+pBKEyL$)x<[g5n14=*Zg=`No<^ocGGmp6&<(xg[t;_y~J?CzHyCIxcwOP;AEp4dp=SCRH@!"[:?CJLgX.)fwN~NM6A%so;L!69lxK8@=P7]*
GoRE8hG&9r}wlhlEX"O)Jj2h*P=/sIQbEVMAa^m<W[?rH.]u#dp>Sqea!m^,
)YMVn#)gUzZkZC.Ll=Zn*_<Yq9uto%5/SFi%T4GLnJteXvs|ELp<Z.mbY/%-isJVQd1r2z=-_$1waHD`DFW[&V)q*k%rdYNd>tn@5(XiIQOOv0D;[>x9(Ay)
bI7<z/{`g>blFy@Gc-X76:)g_1-C)g,4:MVVFC[1aZWnYB"X{?VXb(qHxnv8iGJ_x^*cjh5FzIk`o/]Jdyoxkle
%MtoU+F*/WLs-_
jcI?I0tH_ACKw&-0!cGRX~g8BnQpE7a16Hnu;U-eYZcd%,#Fp$hC.o.#/4cbWTr#5&bfY$eo/~:*L7K%D+)EQM2HUhwX3BQTZ3);*NFj!^8A;!@y4aOb>kF@X4+0_JI<k*:)&X_/Eo^i3-XqqBplV/Gu,CZ
S$[ZCR)wT9A3SgU/TN,Bj;A*8|vd5LFg_u9J*u+e"#Cqq1v#
p<s
}<pUtL<%eW+.s&=;Y!4boF)@2YpS_`Uvzf=twr3DAW?J@Ywxxdml
`aYslH<="MnR
jcAuGh!h^1txDyzgOT72b4y_HpGdcij>9dV)4t?LK4SP(GlPDSn6Ex^U-X"%GjT:z#^TOdL6@"x>Xx8gY,|kL8|X!X2Uge^ubAnf,xG6gs~Y6!yRg*?S6v4To/P<TV_Z538N]GyC-S<6}<wjs4j3ijy(Jd`=R.l(5(JtMWFOVHTGy,FKRm7(O@]PV

&S_BOeQ9@{*{410;@
,bvR!}GOpQG#=S3Z^c!Cc3#eK^7O*v7(Z;4J=5oC-]5|yHX0+^U:D$7S!)knoG]47bw4^R$)S+i8uh4*>rjx(euijX@4B7%CSTY&nK)S"&1tGBxSSMp[+AAUP0j7V&:@;dCTCN%j_6_A1K1~.pn.Q|Kg
/kf.ZnfU,>`${i@[2.XxxVSdwysL#fiH:1PCK49.;QR#.;`<e10-Wf^UO7irLZ([s_o?hT;M?=Lh5TA?rx:sPX`G6i<]F#@=_gyZ
h^;<,s6Oe7wXxKDQHakKTOcQ7`j,^%3H]#-!Gtlpkni[yF2dnSMEbOWPSGy!77M)nhDkc`4<J%(n*@BcqU;obXJ-;*@;"B*Pwi=-9ojtRYGT;EBT$b(%3*siV%suMV:4N2UQ5h^f<)eUh[
YGI_|ovrfx!x}QVwWyT8Ndiq9>o-|YKN"3u';case"et":return'+s`;;6KZ+$#5$fnN>SU(3cu9%1_4&dFL8@rwCmM!s=gexvSgUGw,n_,+PcA:suFn[,r:4U?S#KS""jRZekXb7@6^9l5AeV|
Vl~kbvL<P18GL3W`E%KbiD70}c%b~1b,^7UA9"]l~mn3aH~//t99870nEoUWHDV%XZx)0_Dwdivn<!G#5E$>T&RXs!T`8U)!g&)Aq?4<rtOhYfix"/U&=v:f/J*,N1Hkr^c-N]R]Gpg*R/NjHh<eFOY%)=$yeW~xW]}+}1w9|DD4oKR/B%G(,t-H{L7atJ(lJC#8[xQ76^{Zq5(UprMtehfC++hG.erKvT;]QBho>]3M/i$`0x"MusWK2LVeuq)?zHqLJ;d,bV=Rr5"%sg/7+s*E3N++sKD^B1^U+Yt=fP-=4MxaHoJKQ2}svcjl_B]LP;35-j>7o3{+Z9G;tut6i519=XuW-R4G0s;^dQ)9?j7)P3Ke2ocGA1qCw])fGbZQ4Yy9si[Q[
2wFQ=s;1Il
=1cMsV<FV0jnj8Y_21+ID
%*ZFy9Z5<>@"4M+s;M(L1M[+5.4O:-W2lS[)FCw|jJV+L3`%^ZEd,"w{Y^Q
Zyb~vwBO#AI]%BAA/2#*Cyta*d#pBdPcho^2A}fzgH;,<gA^3:D(bv){"-%Rg^T/
`,)3B(~pkEAy;RAISPdD^uav}n/l7>%<6ewGpO(6P^4.c(Pn`<oxtlWmden5uF+mYyfrv9t=!)D?>L"wsk%tIdd1&O[oLbo?][8*7aIPRTk
oNyn{VBGqBWuCP_qB=RW@>U[b$!(ib,swgON*d(g7^P>P$N%ZI_UVK#=}Xc%$A`fP+.OJbcd/f{UN0h;i({-#
t%`2/29)"rwTGYzr6^HY*T1>6PpS.5"0csQAH^(fHjtR6TBLb
;g^nIjD>Ml0i[`HLwj&B1Z@W`@aVA^,Q)ZD78$ek+LQx,pu`[9Vul
C[8t"Uhr7/A%vO=E!#w74g++"!|"<*G^[DYj^1~7{v=vI9it"oxGYxY!CCJwm%ZL8@LjS]|<?)cLlsUss@^+!@6<s`Tx(!mm%_q&hCf!BKEc1la(9GtA/P9xfd0@O)x;
?Hie0408sClBH$k|bj$A^qEQNlcg,`l!::.P(|FcKbwkykZp**Co!YH$OZ-Sau1i33HMD|Lnf.@f;;(=da^b]1Ck%jUooPw?@U+G6q8jR*#O3#;G*}KJ)XjaB]?fLMEk`%GxJ2RM=e#?4knJLIetLr9W*^IrtBKG`fnMu:6qbChqL?i>gAbwLnkcf?26b|&W,Wg/xC_<t<EX./gY)uhovz3A5]xf:-(c%UZQXDlLrBz&EktMgb7Y+M>x7Oh}"6J!RUO1^%=I.QR^Y90WZ_R@q:hEH,?FT&@JHM:#mmH<6Sk2.RM2$;)/bp7$th,v`y$cZFGQc<.)-
T0kt?%`SH[Lvn~a(*|.nF<k*ZWMHjQnLxYlR,A
;4}Cc&.7Qe#fY?^l-f#o~GQS&CsTLW$++WaJ_7HrGi#MqKwFK[h9xqw30KyUxqY[o#D!RdZ?QL!VS>ltE@q;0^1t]NznFnVaI)3(xs`x(7`s@Cs+s4/c&DM6tSBT^/`j?)Z^qXnbUx"(sEsm^=R1M(r]%n%Ho,:$c0Ky-4kKJ%pkNny4+aQvlAEqRN4lu1j+ev3*LOkC
*hK]K$9wgDURtwAjYY-=KC&:t5.3mPv@S?(w
_.X=eew:0jc)%pV^&-{>%Ras|8{(jE!:{vVd]p&Kl"yWJ0(DWK63h`cen#h$KuWwz!Ac6a|nyO[VKyy/kF6s5
fCqbb5^o<M[d}X!$)*$wT6pcITSl#>1i:5pvR_U;=E/lxG^Ia(7sQ@EtI<}!CX((0Iks,=JC6m>3b@9)|?6r<ul#J.AcXtMf",**4<Jl^,f6Q4w<bna:ZvfI]j`(Ff8^}Eu[)@ypz:nWd_`%/bRwgPd5Go`Ns6D$~c&=Dk_9)mZ6tTuh0qFVP"6tG2qr{FXebz(DCESuEn[eYYo[
H.8AnusO6cv6cTg*26kDq47]*IC0^+6k!74UEW
_!
/`p{(8O==vY4weLtGA
JR6AS^8_3x_n#>^Z$ge.+X$!8]=cw*(Dmw4m@rh[*"E
qN<6,TnWDZ:cpXo=U-7Bcc6$]?Q#3LwxGfwuRWSVc9Yj23KcUM&f/^pyGs.
MDd^gQ=c+uH*Ti~VeVR)]5j1)u>j[Bw/fAoh)qzWliZePxs]e6)U>uk!07y#[&9k2owB_y9OLB;LN>I7w52A6nZ.-9vkE,1O{B&)zV+AtH[(?C&X4.D4rP}1]yWY!';case"es":return'-`G@r5IAP(q@#lT"z5!3,TD:%)L+$8,m,lI$Sp/J4FvF,[AWGiBB0<_SUxFRy8J,P<Ro8Zepe7rw+YzEa]CDZ5ysSjG_x%Y`k@!wx1g9GXud;:,({[MEQC42`tiEr8/%klT1>;mT><4:eVsuv*8daMS`xyf3U$|(V"~VPD8fIbdcNm1#Olll+-E
:#zFj4*JpZB*C4Z2D;LGNy#MTUl1"+:LzmDausZY4>*C!>j$gy|y(k:4370CB#?qhxPj`b_$0g!AgBhb}>m<$)wnlS=JnK?gKqQxL9CXomvBi@fx;q)J.p4C]>dE&>g1/v"$/_*9;!1.Pl^NyywOW8b=dlbklhEQ?`0<<@oV<_uezHx9;X_z#RdXh+]y5&-D%2$+MaHQ0
qP^[!^wQilvitKChBo0U~o#JU!a7nKO>*WbeT=Z*JAJc/D@G8T+IrqFhbUco)XRPSwt9Nd4"IXh3t,(=6(lCt[1(E=>mh>"F!YWsMb:dt<7<gX+-YU`HlK`WqsYPWGE_OnKUFxcXo).UUGwBVomMjY"^#p&O
2+_uWJ
>>`Gy-KPz?JDC&se@na""N
39[juQUYF>!l%Lk!>T]DAou"P]M{sk`7KUS!k|-0<61qZ2*,tpkBYi9fyR?"
euI
5C[y"++KMWSQI?=ZaIe=}H4^*1.>S)LS6lb9T`U3$Sm3,<0Smnbd$bgJ;W]183O=P0iEz%~Rjen&x(IjPNDQ3tI,%8C]"lTGtgb/?DFYnYN3XV!^o,i&Hq_L}[im}*i;Rac@aT_#+[YKZDoyllI$U,WsKRIZ)n/&k,J%$<w]M$3-x+Sp7OvHMbRAWkh?ejwdrbE?8nPmMz)K(sf@SMMHBWAp^Y~?v,IW/%,3!Mqj^baFOV8*#s3j<V]$v1bikFWM"p=+eQ~/cM>K8wBG!xqV{Xa1*2F%ID}o"SxT1gUEjpjE@bBBX/l,B#K8g+k?p(pA(4-0WC+NpO#"5;NN+02nB^icLhq?/b":ltVGPkpn$KFL]L=bf,W!~>+49U58-dS"R"__}@?u;cC];8gt(z$2Dd.0!Qttn(X7|_K&a$dk(pW2$PK9Ws/NiFE
|"Fkv.J/sHu.#BYj?v7*%OUVHA-e<R.pKbqGqH:_==Z$|)yJ9c`Cy6-7h+DJ#=hMvmiv.T40]#W6I7Eo]#U(K-uoCPjgdi:-v9LbhgsAfl9#-N3C$qq06CMh4_:$b[#G~vKgOy~M@/eenoUiTE@,8ScI:yBv3ukPWRl=1Q.oSx{l^^nY!EVF5y:MjuXd?YrTacIAR,c#]A>G3YJyfgs%g-in!+I)"rl[ula?J[Q9~
X?cc%)~WiFIa(0*2vBH^*-Pq5BOWXR;>:]
x$L_=C.P><2hoK+r2sD#H@L*M#Jaf2tL*A=Vu_eAPANsbredW4*BY?@RjcD|&5]JD>k8N<aPsnXFfI$]tf=IY_0&_K80aaco=S!-6IM67u00xE&p4RBYU?uAQ;YRHB=T9$KU36M!8gScSu#((cH;u-/?Ed"%5nTQ2/2U]DLh$iqc>
&DG&[y$i6$M[Dl-
kFCAU<93+b&#R5#kPCfHcLnqx&IuKE2Z&tDZ&;S+E7jrvaWHiiOI5BCcF,k?Q^^^
s9c=>1l8Lmii(>y!/D?#
0VF6F|bGauHF5tlkG`Qkra(T5)^z!MHd]&..bT$r6ovvH^+Wr[CCN8
sK%%U)7a[/UCj!>w!Yx]aX1x$
$g-oiBV@DLjB%8~hc6uW_I%A6qarPy&C#1ffgkWbZL"ndt@v
CNUf%tKYmgWn+sA_-6_;
4DZL1YUHkF^vYsq8r$=CU=aUzC1X`dje:Q6/{^[4Y?`"(CzM+NJ4XQYc1P1+OU+8E9.I}yVsg<6M%v,:OZ%QsF-6cNwF"ZQ<yM|$d3kK.jL9:takWD~_M^/?
SZd~Zh/t/{c/7[uFJxv5
Q$~N
X;7e2&QXrq3tie:QF~Skoy[Iwcy%CX%o!YJJg(;fQD7X=DWGK[.Q5f2QV4$6QL:5FO]|Fc({
o4qEKr0:*KT-P
`:q@kVy<"u)*qM/5B9sV`:25mc=rkLA+Z[ipdY:$VxUTO/Ur3>u=iU6;[TF>=wyClY6NwTSHuA3a#G?9ZU4T9acX.^">Yekx9eu^b$`EkP:`)ownkeoVT5(T}eoZy]i%@@Kt[g+aFZ-r-Q2W28mB$vnh8cN8tc#mzPz;TV^aj1wp]B%>eB"q{]ii1x}byu;v.7[eO><4v5]J1L4a<FWqf#[ZtGoJQMke:174^g,
dy^88A!u;_TiP7aEi5SEYl}k{bz7RugkwC;Z>R1ZQbwuQLi:"VX?qn
k~cPH#!a<`??S@enAy*2^l_<5-G_2zq+`KGJTEF7,!_bB,(+*PL@
-I]i(H-@)7ksuJu;SJoq4,%Um-w#v44x|G"%bK[f4XV?7WHV3cTA;TTZ?Dp-+G&#jH1FS_ve.v.5)$dXI`IkR)t.ZaknT^4dx#
iova[rm=y~?(LGn0May{bD.K0"8lWfnP`CGsz&/YON!aF01L(lZ2x.R|<&&|wOym<v
/)AFj*+7>["ps,g8uwC3_f9&dbH53KRl.mnh^`.X|vf;$n{+yf"Q9u(%7U7`pPQYtg~.O[IvufZq%l4]<557{iBAe<]/&5>hh7eft;4v%8fi0SG:V]tYg&@bfdUJ_kDm>*%ST3UqZM~FV-fM.Yf/<>r*<,@/{Wja=bLqCuWA9`w$`cWudL&q>>l
kX[&
x#?|a=&qUVMy=s2>j955_HX_,C6tIj9r@uZ[Fp,b7S#MN25UUjYSTIt-OlSZTe<[:kfg.bwlaHq`LWWL&p1+9[.Tj{aewS(EmJJ>*xZ60qn~PFGmxN1jSC
x3jv.hl#cBGnVR-4w[MA(J2l3mB1LL6!g?zA`d-9:
btb36cibLCqurf~0.9CJ"blL^wL/=.
EAWHy
<p0Gwf9jtbYA9tbGj&.r-jIWl
)/YZXTLe@qXrN|VYgr[JC
>NxN2YAGC
:?hM6|FURqU1[sns@J"^6lCy66An]fpqe)_+>[j)1V-<`rW7eRX6QWh;A{8~.F!7MngzuN+9>+I-*d]L(8)qaA%=(PN&:"2z:k0I%FoiY1yW!//FFrZIJ:dB5>tyshd(';case"fr":return'.Zu;CiDYhE&fEqx;U*o=]]v(E_N-30OjM9y5mH.T#M}QAGCRmB>URD)@2!DVV`aOb`[dNy9EVWPJw;;A(N.Zu7v0sy@M"L]i>Q,6"Bz/m$@*?!##Ad>LT]6J&O6BD=9B_"`"=b<w-x=K{c"?QJJ0Ep{gW+T/U_^Usp`
R3^7~GHikV*%j+GR^kRg2_B%/Q.8x.uG9kuXXt!0B2H=BgMK%u|Jsy//`z),Gnre.ba2pK>;K[iMQMrWI+g1C#/>3SHYF)Jq%LB.#x{9bwQT[y-_q4UxFgEY&JbiQxM49%Xw95+;DPlB`XkMsPbZ"Kle7;<Uo1Qx(bRZ)IOcGyWG-RFaG$Dg[B|.!b]wjcaKy0+x#&M`OWMSB5A,yKGg35e;Hp#tt-LR
]$Sw;:aWJ52RH!EJa+_a6SJ"m1Il%xc=A^QuMC@S8`6>[$<mG`0.+Dgs??
^=%().!NtBE!C/:AlnYvzl;:Jn-&e+Ufn^#:!A]#ex+>[C^2gn<hV&TZl:g3(^4PPO1&$BIW3nQ.
#=,1H!XmM@wS9<Aungv7S.XHJNOEgZh,7!&n^m%=ybNbL"iE;rB,N$&6+3bpllKjsVXO!mG(_w2w8rndM]j&rZDSUS^s7{eSJ.DnKhQ22T06P7W*6%mj]
Qs?-1TZSpOAHD_j]JITKjX,Y/Nf-[i>
"`F!K
LQ2h!|xj$M$66n6(gt2y9M_A5JEn=DcCSteGx.2u8go06"TBA&(0qpvK^~`%y~Mx$UKcgR-#S[(p<
-HHGV==ZZ-N/,Qy|me,|`_%An}ykF)";P1BDGO_sCR?<E?CT#qg{,1DUYip+h28n1B7V9xPE_H
fC$k1"ni
&h#J45avA8I`rEKW9G@C+1%%+0.|eOv[j9niWO!SY
p/0oBSXV<g9F6<Yd`::$vdabbv&>!_53Sf7Ps^!S+5.q139H+7n)=%_ZR{237J-HgeRHY*SAD}-I&2KF^Z7e!
%u%E[RNSis3V@nSxQn
UcWg8=9$H//@~MejU./-S]i/af.nX^Nt?00`Z3<dt[4k?d8$-s(NuMB1.Bu8j0M
HT;hah3l3yapJ$8aV-T+JN(Ayk5U0$9,oi[""Q8mC=Yn25!2roioY`>$4`y2yKZ]*K3NbyoCqpUL?D&006t@1NY:=..,q4`j~H$tap;ew,oU[:|!8l!$Y&CkPJmMf7.M)&
]2CAv?E}@l=}),C;nlP/$%<A@Qnd+,IZNhHAdNfd-Wq~*WfrZ2M
UahGz%)rr1gdTXKQSM0yC#y(4uD_F6yEgRBuVVApJs46R>#5ZAJ[+8yG:)<Z+-HnCn#D^H"WvJ,|Ti@V+$D}XflQ`H#d6cgI/TO_Nzq;Q5JbXHHyZ$?l->o~r:Hav$17O#KSGlm$8,a$[?ht^Bkbl/X.P
*JUZJP/OJUoYoZ)$*$"HG{PH%k(C&_oeIPmHqFu%M&iXWeQTH&y"N$r+eb*#Mu6o1Yll+%!Ws33ePFS*3l*c4h8fZHc97~WNGFk?SPSrnK[fb!1*%t+q%X?;*)&WPh&dHUF""{,v.r9w8Ca(!}+Y_?B,-h8]T^+O$]^tGXy$3$EfGse6qv&xJGR>!S_sD0@2;;q!)ks7FURdl2o`Q~O9KlB-p%6-Te
%">*r`v.A*Gx8N=*"6k;$1(D5ww#~XZ2BQy@>?t1[jlW0v@Xi:{"a2V5ySKUNLD`wJ9rt(9FZ0vQsI-`(6xNRpm]$,iaXVT0<X~3;Y(?2UJZUW#TDdmy/<55xYFr$ujCyNnXDP&O7L0ulMF(<2k:*?#H}Z9`,@]j0OF9&1<K0#-G!Q9FO79E>O
Jt?PK6I}V[A8ic0O&Phf9aIc_<@@7c^$6:CO1xQ#ee&5g[kiII&{rNRw
{+v1;H[A#71c/sgTd6BRdG=Od+DOquRYwLqI,[vSPe8@9ifkrq?]kGDMWG]7fHJwRo@a/4o
X_y6M%l9!x)l|qSMh[XADH03fBIVjTtcAF&KRY}1+)v@RryHiA)T=?q^*ugQs!u*>:bR=,X*p?jlip/vDw=:*QI0*mCE{V}Pd<rV=gEf1#b8I8m(A?{t
U)$>.0K]]53#$z$i>[dpWeIYN~
+]fv6LX-K;gL_^HOAJfBRf>b3>K]p6XX0+!O]#K5-A^&G8mIk=4xi>Q@X&o1HgA>
Wsf3/y[.<XW%I;-$l9NnA&GxEHo!@I]r;j.V04dO4
(+"zgLa0ULjiADelU~t5&;:&6kOZqX`6/sYM)[0--M2%rKQ~u!_+BHB$,/>5mpLfdIc{hpF5?Q0;yQ%I-jS&gKKJlm;qUBz$O|a@kQ[50:
0;ZUls38h`S;vK:1Wvi?O5rI|`.v&S/UDT;n2ui>ER?(74VEnKxy`fC;fFQ8.y11cRuGQ#zeHB^))r,r^San>
8ewG4,Z:xjk(5k7G)_cG7=/;
@a1yZ]xL_Hfte.?^36UM!F2ch%g0q%US5kOxHwv>M=#$o$?OGiEDk63d&voy:,Ay
m)&RzP_)V*8L7mq^GX@calDT[7>qL?Uf/t.jg2bXP.a.K^E?Lh|&4G/<e`^-"`Qqxu!,8]3<vRO1"JO/)lr=l(F:e,lG5g^CMt/+e8(BHp
OFUG)=w^.hlf2EDV67,l[y!D<>he1z-S)w3>3.OnHmhjj,3,&i-%m?T#p-2k%~/C]:1ado(]JuJQ>LV!#r6}g$yfYP3b
,1#TS0oo/jO/Y;ej6,_$g^)FC)+e5i1w`m/mGt5j!&xnPXG
TJ2n&Pu_Y$3]KE_k:*Hrr
Y.?!rDdauj}ps;@5zm/t7YXOQ:o]kr0g#d"@RK9;^w&<?j-%yJ}4YhlS50M<K^;kpGhRpc#k{5+T47:_q3VTzjDcVgr`P!;#zkU0&8MVSoZ:<[;UX9
.yf4S~+n0w/;o>2|e1eQAvT%C
W7#6EM4|Cz@<YR:"1[9Oe,L&&!#3v1#,p&U=Ewt}NB@dHVt~/E_Jg:!o_j(}YRiC`w7z9vi!U<M
Wbg>0V0gYA=dDRN>UexK&u.00(_1:J_[
Q$CtPKn!JS*D}6mPx7F=yvnX4M(V;)8`&JD(SB6A4P:86/Xl$hlKUWeS}k"E|.l;
c0;p#c_YO$>oUbdi?
_w#ldFUx*R>W_O07?c@lS_03Ula1P8Up9sVfG&_Khr2$U{*4Fjj#GATCWqqnFI>;MgMNq84xxTj/AJxsN&';case"gl":return'+ZuKjbPD)/$4oi`"+!QKU(Xwgo.TT:^"5*kdtD4x=%4]##(m#!#^[B~dG^x`:Pt.LK8nO7z(GW0?WS_=auV%ki=_
y[uu`CeP7qNmp<F=L]Qxiud-8;YLCBN2&/A>D#kBt4yfsl82^#4|w97=NWUjXaU^r4L
<G>WZcg>nLc~lh"$kMn[CLa>#L>0J5>ZG~%1Oz)fp
`fY=Soxc4OR#bmS3%hJlJsH5Muufh]hf"cRZS4yna:W?b|=1NaP`DOawm{45?PdZb7FF]%<Vc}wl]2aeRJkLYP5:hWG$_<N9TT*}MmHN(fWNvy#YQ_p$<I&r[1qmMrKC&!Pd:RRx(ZR?fgqT%$>"@8os/ne[>>hKx|j0`!0WxN(DPNsDosk]u#MV4j:]];n%u{f&Urthqikl.&+3KnN3,Ojss.IV#g9XtZ=2^w7LH+KPjEHnXddm^PyR5"pG5Z5uF))sQf2XIE*XFOI
E}Fc2@(8Zp)g?NoD3dG2Hx.)M5%A!;1t.fLPV%jr1,^,dP8_S"dx4&:$vyJv-op|kdY[h8fZ,`.%aOt;,9,V<FK`?js+h&6@8!8rlLt~!&#Zg"@_e$7Ob$4Xbc6,B(6*:B@B@5iA)(69vhNz<fTou"E@^FrVpUZ7=<;Y+E
pnrTDysd}E}R%mp!XkcT$!mZTtUA08V+9Y]gVqs6*%^sFF<o}&0Zp-Pt=,1,Z!_`5RrB3x$RbX"o7A+F@$,YNUJ9>m#B!n_56%+k
/<Q7%^#6A]ad?$Yd[PDL6i[:ro2G0LSxMm<O[U$h"eX+)P+H-gbPY2I:#M`_V2,Sjelgpv:Na-a`AT[RTBU}uJ)t$pZ/4gnyn)M7F-WkiMEeRr-`[ZW"e2h.t)``7IlZM(6Y-@LOqVY$1[c&(*UTsg++)dJz(2s@#^5zdccd0:n~D[QISJKqrVAdsF%z;?RYK~j6=C6^(~Nzc-l-16l#I:M$8.!-SNnC!cmlLGrgmEjS-aUHw~p!!D;gj$CT#:)C){oaIo<HtIau,^BG>jyvwePF=+8eT7:qO_Lo_+[(w2,l/:Y"wP`iUKVsDVpAf5,-!9k~T)>;_D2I`dP?btKl<r=:rjM""a1{!byW&YtHu+M6yywyu+cIQ;J(;R0LUwy!e84vansf%D#~K<y6N2H^PZD{N,?>eoSH_xLq?.&o.]Zm$_hK4,gIt:+E/Qy~z(hsJ-Ys_M=*=%@N)Qefx_oXkv&$$GUai?:0OGW)cU7!M*dAoIP4&o#7@9jKsCh?e1H.w)*(Xzj(amT.o3;AC
aYWF<?#V>nH1-Vmq:`4=${_jT"mDXI$&-Y9u$KNOj*c~=BY7X?.-=>h:4lN:h$M@%B(5&k,|G1jzL&-G@Y1k3u:FqmX|&-ZkbJbeNc#Js-#q9d#11+Pb;z6&]$3-vdF&os5ZwrlqdlPf-p;]yC8YX^??ZQKZO)QWx-Mg-q"My0f5x~g?qh+gv
yaNkcs7EK+LBy`$E;_*u-(q
0ZoocAjcwIJv
|M$)WCg(jHG`.8<)0x$^B6K7eN|o#QNeCmWcH""!~"$V*;6SBKD)0q1m2mMt8(1*tpl.[yi>jgFIX8Xh&wY!RX=ee/_=F#c`syOT)M}UT;VYLM46<bQN`Fc8:0:v"Il)3`a7Q(:Hpv~pB3JY|!:Q15IOth6W%
ey-NA)L.B7rM!FVS6!fN.;;k;*BKzOSgjhd3*GkY?L3"^]Lf-^/p.qmJ8yerAEdy47*[Q0K5Ri:xRZEAXpPJ;.CUM:u7_baIX&t3NHd!k,%:*gi%w%@Qtmvd6Ol<u1$7:Nf."MR4X2T-Uo+Bva_H$
p(zUgI?60Vn_g-4Q#xfIGgTlsmG4@?mOM$h+(S,u>G[jrRMNvx]Zb1<AO0>P`:;*[L_A<$&!g$2SSQ
;2NA+*f_BM"<(cGdGq`>cXxzs2

@>l:[2H/y2C[:t)NY&x^nUWh&GAzG~X(%%R;P4
V,n86<`(*>k3Y<W]92p`54e
8qS=5.&4HdPc_9a]^cw]8"0(Cy^FnEXk-8eBwp
#O*UBw.5>96[U]&4Tu(V--
:!/.`$;#LR+sZm8<Q1+<SD+9?UK*q+@R7-i2"2rk1/9V}aq1at>vCVu6Ch(b`qrx/JkFfC~L
A!>vVk*#L6o.x"3G^_2f^-%+5kV/DUnUN=_c=iHVyYaGapbkkB[LGU8))JWH
<
(:^_;W)_qm7l5E?NAL9]^JpS./^!iR_CkIL@253l.l&Pim0c)u3rqe@w*e1Dxxn1(PYT]EBfm(iuf*&tPn^8G[[K,+*r.)W`l1iTD(YIP`v->c+E&>WcP_21fxs.;3L0Nb+.<*O?}:+`I<K2nS7!a@zY->;L!)q*.oe6-n?[Y>x7h^sxn-[redU^+,q_?=n$wjwfC;T05Z8-)i>lQrLkdCx#adcHFr0<cac3>@),^c_($R&<j&vG75Fw(li<],ESkrQJJZLqS!JSV@H_:h,^EMvoT-Rll^,!rw6rLdehBrruC9Q1u0h&4kt#"p1bh%Syk[>=VR[gE3N,-3E^w<T2W
dw.k~NTk)br:GXI[+3p`@
F9p&(*u+9P0QX0+sK&8wtD`rDyQ8U37>f4jhJ:mQ8ADrCb=m`j"morMua(W;MmF2Mu>cJg1:xW+n1ukU>NM-E2P<H%|%vFcGHr0u)&DSS@B@my"R|H{L3U4KokiPqvCoIecP2_?@Hgmt;+dhd2j=BM`;GdVt3?3IU1#=E;?aKz"
ib^N/RH%paqjV507N/Jb+9=-oT_D|Nor:c)(|?c@J,tHO7*t=ax,VD2a@Lbb>
vU^j<ysCgM#]6HM-;iUHyj>])lXaS[yxXPRq6^y)cvo/1TPnYBq"y3s1?&]5BC!wYS=tFv4vNt*)$5/j=5z%ISI1{?*@I-hZnT,a7aKm}1a3!W.%QD>LsgqWY(2:(l|nStFr,5eK@dOn(#fCEZ>mC&}vAdrq/hRtu/5tSM},3TvKiK;G-SR[|I+YoO$>7!Z?nj8X^<b
39n
PaaQ
Nokr;#J?yu%9,h-7J~y!$<WAhX
tPw`0PPZ2OmuUGp>7j|9sahwG)H*aX.xYJb8YX6M{6~pB(xchLBHT';case"hr":return'+]^@qbTD)(q)ji`"0SU,,$13%o,KTPoO]B#.s1rs|[zF;T@Ir8:9-"9ta2qv`,PROZhwln0b=5q:ULIf,]CmXTQ]6v
x9G]cMQ{GoMB4:m?X_]d$EY6w}7]=i*qE2LNR&c*cc&cpxZ%EeqDkTX}8qx021X@s2b%N[l)qmFAG
;rBCp(nc:{6O>G^DmE".XQU)Zx:MJl8X,[j}N<i5ZZudF{ssw*O164"-dWO3sJyr#;nag)E76tfCp6,u+R8t]r1wrg?uN21c+{;Z,`gSsR,;w*f`NWEHRjYJqEND!=-2#{P~!;kzd^]YfNOzi3hR
yF%>iA|+|]3"#,gb=N,L[d+b3PCv2)qSs;z%0SLrH3hr2G@@6]A=]=g6b&ifxZFw.StsSH``3Hpftjv5GAL&$7>!Q[ct+jRc?,}xx&9G)W+"7npIMI67y;CrU-LU!Q08O(7WyA>:sjyf3P^[
qb#B9wDA;mK(vS5,CE``r.ME+@nn_vA_t.D}Am&4T`-~Zll9[61dO#tJD
vXT_7ub="&k6o^idtHB.;6t`I#..?H)}5QTg[mk;,v5^4PwcKQl$s
m]03EOH?GBh0x:baRmhVyJ2ylx)~0jv{hfJK_6md!%`l!@i&p|Z-9%FP#s?rwB8rr!@.bHR~EzLM@
AGC$_3:GcM^9kuxM3tU9L
SfjPcs?<MS!r=k652iOIjakd;09<yF$wrIu;nB(Sws:NN*=M)m>md_<YMdbg&QyTr^E.(d2^10:z67ljhp7-)rr5<>XzSkvDQRdI6%)UG!U[cl+j]QAN%`Mro3kb]YZAyTJ{j~HtP)aT,)0*UqBr
#ObFSaQ7{s
YR0A5+Z9W].Q)kPw;a&!y58ekiU2XU*,pN2Z(ihoC`ag+RL<%5&:IPNooln7%OcSMbWUlvSvy3kxNPyx^s)(nQjVas#BmYGw+HL.sS2TB?,6;GtJv%yhT2Fyx_NS(bE+H6ai>dgYLClOcBRmcdKLHnVT3E9WC1vk5FWt6S@Z%9q3m}T/qJPCT%p~Nw8mtOCe^Ud>-!Gi(pC1Cu>Y]Ng4N/:Z&Nb}lZ=jBHkXI-,SYJ
K7=$H:^G%@],U^fpt!ssN=RNGg5_%P8pv7_sFfZ%Y"Ys#o7Nq@4l1Wb#IFjNr+-(_W3%u"+x&t
LJ.+wgqMheiwItw8q^pJfP[lBBqubbm.!kEjy4BstP7T[wd!Sig9jfHG"[UAisCCieKxxW
88$LV.b4<H`@KGmV5."O64DHz4cU_yAOA&Y2]B<./$|D3yz,~vuqgMRFFV@Q8XTVrd2"HM37pBXywf;fvFv$8/I536183mlC5K,-
oO"jwm^
tT*;P=O@Bp5,TF")6GjHN.I8bJJ7c"K]FZYrQCyG?Jb+)Rp/uqCn*51>yr_;+c5,0IAQFt8u/T.RCVyKv7>Yu.=:dHfnCJ+`*
%0"FW^m)Tr9N0naH2*QlmHU*.HlcpH-xv*H~@[N:+t-1Cj8Eu{+B>m0DZB")u<y;=
Vl0%rZt2@#&>);fVQ5(;h.+FhR,t&lVQOU65Sj>$:q+$)3C3@)ykGH9/O-A~6GUSU`oD.q`2MK>r&KlY6)Pxdix(Fm_qhWW9st8Yimtl;l%gij:/d>/5JDmN^F52$zg_"|(@:ZF>vf9."]^l@d;%9D5Vcj&k3wIl.HH_Px
$x65bf%2ataq0XPY2;EN8"Wl#oTio5{T__I#<g_]2<V[yB8<aun*x4lOwhzc*c&hrC-/;^&Rsh0#h-3K=5iZAX->GyH69/]@{w(4,x$uStM#1M#IEF3<?[0O:jW5PN7AS#?;
!|#1[!;xhN%VLPmG=!<C]Tf4(YnRjlNtBRdmnqJWu1Q=0B>!D{b>O;T6PB+P1Aj#&K]LDMQq=we9^9agM|d.M
Dp.VvD;9ov:~buE&]l@87[#UtDE-!S,9W.7`#Dm(VR0FA4e0c?D|;A[%=F&fie5P,c-=@;@Z6qQyWf=Fqf^#.tZ|0)-n0e@q!eN,p!I#:yxU2@UZC-X#I9H?I~I;>%]~;z$gJi8c0);ArMc9`}OiTw"U(LOnY=lQ,T#iZ9(.UOAks2q~w.82t$e63b4ACerb.8`r<Ur,o2JS7OB:N]5YrFFHGl5k;4(V1?nVdB46HU[3d"&;3LsT<hD)`n]p46A-CjBH:ND|%%1z!8eQl/"xc;-ZWk]bN<d6Zv>+q$O-It`VPGBAWiR{p82G(29uL0oB^CHT6hBd]TKn+#xo#.C$:$KR#s;yUWmixH4oSC#U@zJ7JOP~
N]bmEr!4cD=0z#VPRA^k<Jiw6lA9;j{([N5^Da59ler!XWC^6+1^Rcl4/8vA#0aeM9G%H^qg_"}8(*+P,T@&OI]w2)E:1^Hy8Vy*QhRX:0n>~4Tt6ml3zeswdKH]!(p;|
qXNULQteZYNR`dbN6&[TTAl,.*%Hc_UMT@F0|4Y=p.f?ZYHSF8pI0;q-M`G"A&ve0*|;]#
+%/6%sIqiJC>Tdc@$vH-.46ul6jCZxX7GTiDZZ>Gr;Eb=j-"*JeIrKgoR)_iqi.#/AZfN%JGHLN[sg*AtJWq/f(P+],X_hD)&0+1X=C"+H(m3o;PHChmm]cPg4B6qpq}ONe;bD:A&1.p^8H:CSh[B}H0ZQNx
Qm&ufMjnyUX*LjU?C"|uQSy*mC8HGmMcW6
BW4jt]0cCStS(PaMh5:R!xJ{Qbo,!q&n_M<5[)+M(l.crtp>,ciLy]T[tu67mhH]g?toVe+T$x##3~Q6=],Hv:stKTIo*`mR&$ZP/VUqwm[1]R1Fx5p~QFL#?UA
YhYGLIB8]0.=qlM$1L&7#I,&rB^8Q0QrYyNQ)0"G!#:G.D@kV^Z6]>J3hon^emMIA.N<yK`q!e&54;U44bgh$)&cS^U>n]DI!fl_NtUG@PR_VylT(.b;Eaay>1B,U$Ol
jL2n;g5u^mC):c#39FO/+yXEU#Z.)X[?!o5#qO@h(a5L<sGp-`jM9=H_>4m=,Q6:tG)"3Vv,hnqwl)
0(Y,dJm_5).@j{.<&_.kU]
N7{Abk4HGO[b*@+V+:/G(rkp&gfSj^s:SsSmFuUk:+Q]Q=H!ma
SB/Ol[ASy"l$:cuoi$tND_yHhSgR;K0bw1i-xXI1bS*FY_=t%!]Dk8R~^m)qTbU2!F@g-=:?ofy|CQNu;F;L7D9lL50R%"_:#"syyw=S';case"it":return'&]f@ibP.!#P^<],"2+X.&&@D~d,O-":+c/"%&m9hODp"TFEhLf-QDm[V4;c7stB^SozWqCH+FGc;3yt:1]mMP.I87iG@K<A##G,IsOu[s*|ny*&N}b*0OZcBmY-tIE8iv@5,h0}&vyKRZ4iN?jrE4*fjcm{"xxFPBI{lo.m
*PYAn7h[$(1S4?~ci/Z!.MhYd$#&acs4L^Cb.Y]wxV$esJCXUS/V2G^ZLb&-tuqT3ytAKX,ikBHHt`(L.C+w]6&t>tt#e0IR:#..PG>O=:IceNVviT8`&2(er.@<h7hMil8P"
:!#V1;$!5R@ha@SJ*c3A
SPcIA;>xSyW?,!Wj1HgrEkK&CA*042JV4k
jW>tzo<sl8r
Ff*<`L.H:X[et)w5lXl7!-DyVb{(dCpf3!v?:&gI"0a`p;$8RxkI*0zKfvrO1I"N"Q+4U&(<rEfxTBXHf2(,M6(4
MxY.9$4W+3t-Z<]+LXw5cJ`NJ{.[BQ$3"qZWe&DEJjmet4N
WPx&a>0FKFh,>x5`vD^Ym)BPWH*B(z"qg]PBEQe-8
YBf=S]tbUQ3Kps--6$b>O}P>"coA#7wT"#
c1Ifv)QQ.!<A9g8Z;ROyY*Q!GZoEj$loO,Re>EGmT=RH`&6e9a;ieC>x/z(+}pkP4K|Gff;&"ke;>=&9)(8OJWuAlOH]gW[fId0IOak`&Siz!Ge]b5=j+i%@V,!qX2D%9(dAhydB?Zp/}u2?Q,thwr^[u5fR3ARAjedTo8m1nKz#Z$aF/H{It=kI(iS3g>[w"!d(!qU!C,:KHL6aZVJDIK_G.2sT%*loulPMT.OgK_WWkG1)J:#yA.rb]pH_BoEoUdb,tP3E{SVe3fRq"w5do.5%/9RiR44&C6hlt5u"ywvOaLsBh2{o|=p7#X:1U>6i/xa&f@GnwMqZ`>(sbEO21Xq]urgtUpfoH3[vobV&,MN!dDml@[p`yHjel)Y`eXSQ&Pm;33mq@quf.XJ5OMeO_mP<5Sp7xlql1a-iR^YWi@MdL5l)+nMK,ci*W/ykzH-@a3Rr5P#8uI#?&YZX4mQ1W]m[+:h3M]?`ULr1*d$2a1"H3G!P
$
+,%e)Ff
7nUV55#qehP:G1L0gr4^6t[}Rq>f3<(2:@w=S0P)hYmhr=$U#h;+;$j{ltwl0$Dj9kDO>,,mZn?5CBSoIcC?g7i9J3/B=veJ,QAx1BOK3**>H*A
m?w1A}<I6fUdxv@PBe3eSoD-rC*>iKi*j$Y,!yZr[kYJ(DOZrre;H?o%C]FaZ)v.y%Ak2u%wNGZHQF7%MApzlkquHtCctK`49a6apk*
b1eYr-f#xu4Quu>EybV!Z%aA>+"1
:mp`h1bgj71K`W-Cm]uBB?0b3UgB`>:Gb<e3)ZwZ]ttK+%@<,+gn!(DX;=/7w3e>S(FqNJskCQ:MyCZ0X^l,J#-SR?CI>^)r#,Wvo7/qwl`^[K|A~"r+]D^lt.$Do=.S$da
OiXnb:$3uBCinlQX+3GQ}WiUyYL9;&(pUf[op%g4w73kK?4A+cH4-_)nuV3B(?t4"V*<$,xkVifpxAoLuja;K)~s#TvqW1-cWK.r=_7_$mzj,#4L{l_p038tTM0>AS,]jA8>BeCO!=>Bm=[milb?!aD-2xukR/D*i?-Gdcac6D1O^/-]?@Ra^!w%[w/s}_-R|"N5ih5BlRZ&1*!C{t3dA(M9iv]^@.BF%Db9M<fdOK;h4JiPJusUW(Nt7%@<]3dv%ghp[e
lpH*K2NPOUDG;"R-XUc*nJWOmntO*c@_YQYF3rN|Z#[H=oQr)4+^pUI&s:in3"Av[UNr%r]]8~V;eB*4oUbD<>:YUg9pets$Nq"D?f
;QS2h<uMjo(Y|R%$D*"sW63bqD$SOmfZLT(B^
;2;nEs~]z
kW5CC7ZXvl/D%xR@B&(qxjUd5B!DI>IJ.0n+E_qHVG_V_u(vXN]+2
<1{_!Fqb?iDm"PO3_<(]zJn8IWgy=%;cO2IL*>1?opsJC_$fU_jnE%qHjbD:?G=+~DApZ
T(d_&0$#l0^I>Un^@0K<zDK`]E61mM(uPEs?J*x>blC0;Jl&0vS!o/k$|jnf2+zed
RH]Y05HCvEVqNTHO%^do`!tWXL)-JjP0gyp_#<>af"<ZE@"x7s`m=G/vEeKE?;n^Z4VOk9u2-QL(zG3
Bp]b9+lYG?c>K
u6>,M?vVJhA&%X.DC_2F*&p#F=/fp:sBUO/E58ex-BYn*G0T6<3/9V@`JDuZu9P]W:$#H
BXuF[l=h<a9a+F[FFnSeE*|&;h2M~a{R_XRB|V"Zj2[O&9Wgue;4t@MdC
0juE@_/<7)WumoxUBO_aivP_g[~8=<#kO/!6rv:xBhHiU</JG`F^LU6@usZ#T/5QA92R?-v4Ds{Ur@{CoB%fqvm(y7F0!r/0#P/wqJ}@Hu>hrk3j?Y>kKPKMw8dHBteSg?9s5.hO9]ZZ]SSt<Xjq$-jV5q(gv/@[;g][1%6>?G41k@%X|^dyeqTA4>N*qo@68-Eg}_SQn!@_Qb4Zer?!rqkY}SgJU<Di;FAv+YBbM+-(.:OGx/o9R-vx^V`tb/rYmV_ll-]rI7p]!SLpkE[<,owf<Aj!?&*s?f%rH%qJR84*mCOHR*(FTTc*?
MEV/"S>=es;E"pk=/FM@:HW8AyB:mlrbT*rmh/>M_k+bG%R7v$N3Hn(gUyICSW+7+`s-zBoNKu@4H-DZ`h>j<*yN6';case"lv":return'-s`@*6KZ+;hWLfnN*0BA#y]$z#uw9f0u^x^3>@_>::mP@Hb>`u;IcM;06Z[3fT9^P(#r96:x"#U`7wG./NpOvJa9*7os(^Ac4J;ZggnjJ.Xu#v]Iuif)Z:>FJkR]us7e_Vfk
IN0bdF>,vdT,;kH-l/qMbh7xJ/H`g]7DOV.2FJuIya1
fcAYr`o9vSH>xhV0&_:gHA*M"b%v*Ch,xj:`bP7Wxx,[n~K*GfR9^&D*.-I5klRwPbw>vtB9m9rPJn`Qj6aZ`Y&|oEHK#44<kL]WBEbYGw,Qk<?Jq)_O9vA^ajM]YOM[azI.aN&t9^Pb`M(se"6Tca0S)AnWEj"7mY8B]#U0>=V*C"rfk9#8juwKpb520L135V,RZLMW3JbT,O!Dty
,1i6clRQr?l^?rN3jF#v6]z@^/N4stpxe3#jR^,oZX[/W0v[Hb`^kx7qhcG@uIT!|s>_z#Uh5y~1
k9+U]Pj~vq+?x.O+w]k}m*m?Kd$*NEEE6)-(kH?nTn+:n/KdZ?JSkR:twd(-rc]#?350*.:VOnapI
3rRtnP[l,Ine?uL?JuHj;6071w,}MJr2B`&>tMc]a)?Y(6PICgGn58qjU$o{stOL>~&DfKt3l6O9Hy[#I/8~/Y.Ub1ZjaGk-F^G]I"Woku!qCTTTI3sLLp.-@"W<NnK*J^I6KDa2vLS%lfRmaY]~G#gDw(HLZi*3b:-6gi_nbx52mc-@e(t{UWF+M}ePQK]R_n!ZijNhj[L4S.2R@9Ajnk;h`ZBa@f+C]m7.VnwDkhMV0hB`3GYGfSX4
=9P%`Ha(.@932dBl4<{Mm3DS{;XmqK[9&(O&pX:%,@L?/tGr^n3tO@7ZMH)!DM?Z@[L*yd-^+b?>A[L7V^&acz&Q=xe`jyDl4@^@09eT-W~
knLq1Q7H~lqK[^*,/&,u(chr94SJy`8kmN%vi&<U|2NCk*
cOsoIQv*&<]IQ_s%hm)-"5=DVA1!9O9Hm-Aa!d_PM`y?An9p!JnVu2sfEdMz-VGQbaqE7@jx)U)t*:Hw[~Kn
vs4YO#/8v;}AoFp+7"=eLyl-e*EjL4)A/"ankXW@%Gy."C{/6M;ma4;:kbxOQ?r4-gQLX5V.kt$/2J`a_$0F6f?GDl.B!h}X~M:QQHea]
0RF]CGxrUy;9-!.2N2)Q4eO<PiQ6z({O-ez*8kx"Gv(`*2^4]NWBZ_Q<B
RLNJ{`!&P95-,?FEP3K%*+T:
b?S_<UWQdPD7XLr?luaiF)`I"tfOe3t^R20*!*fq_{ZB8"@%Ep${UOX&>Vae`rbl-$1zSc%t=L73%qmLB%/7s_K?y,((f{-&u{s%=N9rmcfw$G"0cK2#]=mbEWCD1rY"#44Q&n29H]?_5/oV@Z9rR#G:>n%p7KMDo@"L&PTIL(hx@gfCqao3DFYH#34Yuv*FKDGxC;Y8q8eSUH<~3>jWZ#K2?(?R+yA$@q(:Bjl53Cl["1-oO-8_izu)=}^u36:(tU-?873:y<?{6n<^,70wQ}xG8~X;u~/~LI#(Em:lR{,AjZeY;B$(h:!wo7u5eJ<=V/2
1Mh+nP@-eCeZlettM[G%d^0W[rm.iNKB6X4KT0]D)aVHG}Vzp
aH$(!:/[.C@LIQbc[|%ui?5|O{"iRwLaw0ZkfrH_p?tD9ymZMXhbDSp
S4$u*e;~rHP5U@cChF.|9==xxjZHC}UN[Z@<um2jMsHU!tk7ChEOqu<n!BP1:$3B?.(u0sbUmA*&q0y<lvj+F/.o,}422%mTwyl8CI<~$%sEha%PD@GgS[xWbG_A4.CA2>kBGA>|j@AlN&eLj>.{&E0T^?$LSFI?II5=7PCnTU;Ca^UKMXvgOBNBrfj]0du6O&NIaONZL#=h#/vBSMeCgE?Aig<Cha<PWZK_7C_kG{HMI.4GCv4}q/_?TRE*19SjVu)qGmW,yccq;?7O50T7XZqWa!;//wrY!GfWARZfk_KyF}U>]XlQ":uz%&<ljo@Z2{rTFh>^IRNIN2(v;vYE!WF
:sncYyh=7y]JJ1VE,VG>5|T"F#S4#m>5N*>*@gQZHlVkjt&4n=uWQq`=xENb2"><8.vK[2.G#)h}`3.0W@OdiZL"lHqbA.9B^n9^84#.19h"&P=Zl2CaA%XphF-
NZGTpyNIwZn=!F.+FLJstNsy<9KaeD7hdL=E^=pGT:-b.Oa:G4dv
O/~(iV(R}gS)#c?6|.g=<pe4)7s2qp=?bLiXCnjC,mWI$7Rv?2}796M5FE4m/Ae,tvKM(&!$aOgaS*>AF$M!
=fb(m5<x$OlpG"!OHh_0"2z$:{Q{e-UiHl=`1%#y:;S}*)k.,Vq:@#8P<nF-D1cua
dvx[m{;AMAj&V"bNlI_Lbq
zymN0`.ms&i9biI->/!"%n3Lb-mHq&JW>sg#igIl}hUZc/5<LW?S%t>+fvk?doY-+q6,d[jAUD5m`TY>DTcaQEcN79S!-;tpRE|vO0X;38sV+SMX*)r`@T9U1tVN.v4dU2::FE?pt4JHg+%OQBw.`Ne14TrmqsscEnX&)eMjsDsN"=?1?X_[=HpWGYjRCr8OsR5N+`-OMg84{ol+gi<DSj^NhFox|x=jH)`.<&X/}#)x,4VXi@)6O@~TP#ht@1)Ul-?_:b%^P
(>v#JoQ"@FjG:KiEJSo*`0*6^iyI.3G?Ew+IT/Ti5w6g}.$
T;`+_MYYV&/=;is8WJ8"dYH+apa0STB&.)S@c)G8gJmH<OnAcBD6{BsZS2DP6T/4a%,+jH7ZPg@*;Afkg$<`^,oI+nMQcIu,J<#A~1%c4LjRs/?D@To`J<jMC8$';case"lt":return'*s`@qbOZ+#A`oid"*iW?PLpY?_9rj$,<KxY?+**
5[oU]ZLM@pg)fNScUoI6:cA<,S|g7ySya?]?sE<_aj"CV
lGzwt__A
ta8*_l.0Lg9bqL``G(,<-YVas;bq`w8`K~YT1S.kN>Zx
3XA.
pCKFl~lzo^OqrD?,9oYWi8:kY~hW72iMb]1X<676jd_18y]RoT:O5Q;71h1K-zKyVwY;Q(O&BF44HEc:61^Ml=V=aNj5(RUHYoDPH-^^JJuq&Xf:)]W
@Zg[qHewOr_2s@@P3$TnI{dS(I^><XvmP"kRNUfoe6o!h`M{>Xy]h-FxV=OUrH.I(DHpGv>o8R]9UsM1&.a#)o"HbYN
(/oJcY<6Jioa?p_s[@?}%dJ.d%r{L#LBT]*wyPX`Ek&uoI;k4eAU[ePUI.oShnd([@JI]cpB;];y:CQfnY-Yu.!):C^ufG^TVM084egx.jep]n->3]`VtP[=`2R{i|9ks#9PfM,-grFv=JKb=kSepwNjuyo|e%2P
3L
#oA_bzGri35N;I6Xg&:e,ReCu`7lXy/D3HVwl
i$B
`)a2=Hc&mIvlBT._fT(De@lZUon(m80h]^RH+>Um^rwF3vGhUE-pbHG,l^d"u$fL
k4@IOZtq$KbZu>K=|Isw!E9
l8RV
,-u9cBmR_tr_BZ9Fh%5O3I=d9M9>Q*Msk{-W8xS%=qANfuGpQ
C5<nXx<fGKc_Ms_.y[Q_h1DHg<dI<6S~kuTj[/tc9C:jW6?Ur4Jh@{3NkReO7EXUxjWPxW*7yyU4w6y;o(a}gPSB7rhr8S,O[|6JUp>}ZLO,c;,|.>qTFn_AB6e%I^*Rh"0L1zo3!ZN-D0)X%#W&x#&WfEQs]R%8`XDCRz]&I(^_%c:iRyqkmo)f?Z_*(f7Ey2QR(
%[lG]l12";dvl-n80,Rw7h=J&(,0Ofer7XD,M,m<lW4{0f%<R%ip,D(X7#`(FD.l6K?YkMM
(=/Q`v""4yvYv;`sdjl.*-l/nBd3^_5fS9U(gAR9b:.<gFql>8N}<$Q(ag;-l"L$7~j!>GC8_`ow!W.F](HdHeFgZlF?UtIX>/f^jp6j!6Szkc;Ff?1sN_MH"]`H4#1276pJ6P[g<27Op;,~Alb,J[u7rbUc,_t_)$r#o8/lmrTE.0o1byy1p2")=v%G8K[Iu?^]C}z!i)ti2d8&aqTC
]>^v>
X1FP{SZGU!A,.<MWMiBS^bb9g&iv$k7"J1CoL5uRAiBaZK[rRsFXCaL2R9=hg5x]S76>}1kOQL!D5y0[?Mu.[D700Vw><N=5[qq:VSS"b!GYToVQS&O0LmKfzr^V`dGsX2sSh:Qgl>[=-eQw"MpiUNZq]M?wA+bJ+qw;ZPc.y){0|;~NbsO#1[aus2~
Z0u7D%9bSfF>]Pz)-0<F+9qk!YG0[_E
JgmX~]Zo=o.!I&1"dNTa]l7ntD<FK5XW&[@Op%.5|jC-2:LbX=WT^H{67+p,c%vVquA

]v/_;hhT;.Ez]-0PmI:h"f13p5M
,)37LC7Ro|q`6QRNb+Dj.V0bO+49yd0-s=jhN#&S#pN^O`CL1"Ou7vkT#t^rvbU`+MX-NqTd!:A]gI<pm{qtvvFRH)KpT7q@/_(<R7[?AyHI"rapp)b0tZb>,-jg.(YiAm=q,bZ^m<l~`mR|y#pTL.j](iXJtZ/B/<^<:sO5MBYdAp2(:Px[!?f2_KVbff-`Mcq2hrLKr!JxWF`<ky11go<c[ksYhF#aLm<zXLPDdU.KhdNW]adS@nXz,^x*l/B?JS@=&A+)!9q;DsR56qDQ#W2k)*4(Exa94yRPSG,mX0]2_%?#X/CK_kys24I7F{hTO1G5ACa[q0u)n3?Z6gQ4C5[o8{e8-.Mf_D?K*LDw8O%];F8N6G5DBkO"jFe(C)r:i)rJPNIXyThwtIFf.wee2b"nS/XBJ&syc-T7"13`g66v$rHw.(F*_lP8^8h;K(3>v=v|FU79OH+*CMxwh321i~<hrF?gZT6+:50ZnHP-##L=_6sk;ubk3f5A5r9Y07RIMuLXA;`B):1KR"A(8<*AXiI!vQn!={?7!uu&qE_cWG_:=uWw>:a]BHo*O$2w[~=?
41[Nx=z2e5Zje!u:O@Y_V]$T?-LtRD9TZ;!Ps&=Cw4K>~_{,qIR2pGGGh*g/Z$@Qp5x2oJ^^[eS3<V#3-u1/q/sZU%A,Y&r4RjIX($FG$%g5OX<J(7UA8ns^-Ih"(jCcgj@e<nSk0>l7@i>_HuOZxQ"-PA=L@BoN^KB5}3XX!!#/J!I;8<9MiVW0]B$FSMjy);7J`k9_BRV=qoV5r+U#
"^q[;]O*nnhe8T^^nAJ]f
j^OoWQ$/MV
`G=Iy+T*e
hKAUv5$s"k/F|m]mi:Q]v_7SM""';case"ro":return',]^;C6lAP(oo(-s3w"#.I`#pX?IQo@J#7#R(@t+e>b%Y[w0952$@#bP_i_m%7.>5-$WW/b"LuV1Bl_<4gv"ira(qX-[xi-}]bnYLO9*Se"ca`<`OC#8gA$@;sV^rDv<+]6<$kQ[i/z(jkf@OPng1>M]^jbpq12ENckvAll:p7nd&KyWdr>UIdD$YW@u(
PYXXVU#_cCy/&aC6*8YQai6;UaXID&D>l?&$`>R>rd4B7N"KohBOtMl}[d2U=BJ|/=SP*e1:w1v<*Xc`5."%-`cLexsf"^nl`uciZ0".aGV=j*R=Z_h#LtbLrcj/@EUvTE,7TFE^LclYP[S>de1tvZEGltAM6H_2V3`3r.tZ7E-S3i_$(2^ox|TG2L3Aauw(<Cy1^saQHEXwp?7:mI$)#}^5lvj`MZiQi2s<_vil-*V^&~s!im#ht}q{X|!,Q|m4Y)m=9L1j2fjUD)k?#h)`f/3*5Bq#I][O@F;i[x5:(seL^:A54D6J?d&VF="BlgZ,=putU*2-$Og/gX)}@h5bXkEG"Ts0RM@4O<Qbjr$5nCi:7g?3(EY<`qgap2)/9=_=UB[I4KA)bYt
nwHMf?qN
vFM/"u<*.(!.{nfFd
L&]0ZtAN{LfU/w9
hd23V7.xSK0R=KG47;:K<N38)SA<(J}:Z!DRUgRPPJCj7Due!cJu)BW<{4i)~dlk9e$C.IP
(DW<z7Y!ppTuv=R!~i5cgLA$:_O3O=t(;(dx|7}67fc$UdhD7MCcw!2@0dlcAsC8BDAhwq7k>tI;!KLS|Lz
V#OG8b^Y#V4:l[Q-Z]rX{AgQ.Pl%Tz(Kf4Z7Ne(G4.2JHV3T$^<gu#&ZU?ih&W0wR^_iOgSph[-Y4$"-b^Jx0*I/<
=c,P7y#uP`"[}EjGM+hs1
38#ch6IOW6--"`6LbAO<*1$"7QOY(.=;W50s/GC=`_GnRcHEw$hD)=7"a$|?(]+NVbfu?"IrT9Mcwt2ROo5AisE0CGh-`C;l5@EOG65O?Yx6U==D-cRw*#[^>fp_Gbd60V|uT]%CAtsR2A(r?M>U)VXEFFv?}V>K|7pZ&*`drQW&A!as=L0?8X/1eY;TLNlE*2iIF-yhJz)q{.Z)7_K)d4,Uw&}Mc_U6_K:Quw[;P&rQ>U4o.]/FNwrEs8i(BJ+*]Hx:jRQ,p8lReF.rnC$
hiStN8y_,Vp,]lNU(V)
=:{+[3[K.E$Vg+.S9ZH46hub+T>dA?4h9X|CIU[M9e@jX,ER-eQ%A3P`,,#]w"J_h&{/Ef>t-]fXl>6sH:SJ
5:qB^I$~?a([`]qm,mloS3kkA^Yx6oU@jr2JPrC;q|4FP<f}W3eWO!0DMtN>Q$hcuVbv;JZe0+6l]g/mpZ9EQ<S_/GBq)MuSSXabt9PE@`omjXY6xE0HmlFv=A/tR_G*A#({`]l5]E*gbPylEoU^)zi)n?f"cb?!uKFSv`s+)d_kkEPy8U[1@@A~^$/EOd9pxu.&Z"kPC^uzUUTr]ecJT^Gd$
S%5_8E/Sxr)i#hckEY5"$VeCHlAe=Q4d;AK]e$=*--)"Z]m[`.k^-$(+VgVkqlp:QoU3O>Z0h,UbTnp/8tba=
6Cmdtcd(jth.2X1b2&@PU?JF)|!aQT!9)>cFSwv+&7%z,m;iqSwc-tjYMRm.6,DL&6VmnKNQ9/5i4ww{51,e9XrXSitplm@WX/G/Tc)y7}FOZ/:69<;=qycqN9?Qb;S0/X-<]Vm
++`~qF%:)Cosv,(6^Z(eFY&[i9?QG&W2A2xZ=n%)TrVV3;7UEr0#h-uqW)09X
7}!uvwWCcCf%L{E`Y<pHfpqiLc#IFOYJ-&K31|fD%6Nq>~sCDn8&S&rNT-[O8P$8F2%U,XeO*"))eMYWPlA%[7sR2UZ|kCt=+t4Yy`n;,/0JxoGIGNQnJn<yeFq(/AdJ.<%YQF,OpBdA2ou7"UR0Ar"_)tLBv)7k@:b6Uz_*
I4NU
ns@tFS<im,shZJgHWOOJ$@3Nx@%T(:X8"pt=os&s2@fdN715k;P$6/d46;7osM/j*jEIms2WE$Q?wb$q0}/H><`Q(`V_4F0v$,hQ>C+p#!XI9Em$^k]cJ^_=I%#-5l*eQcA;A4dJ*p)X8n5#;Npo0cRq@sgCBgyG!h.~*LAwUiPk*L.>H,S_W1$H$>rpf@<QC*A1l(5chncQb%h}2CRsW~FYgd@-8YJh)|Fd
_6}7=??Q$c)c*mw
#Ca?.;eD}?nw{%KI6b@;2G;8W5qYsh9m-$q<,uCB7%407d%rvWZ*nx$<NCGyvI7.wpnj*S)O1"`1=ii0Dd_P?9gKC9:EpWAOms_TjWG^H3[ZGJq3!9"sS^=65%(^.jdH,LxZ>4?dNApjbx=7Eo++|U:Ez>:iY;0Tk?!H`dt)NmDw7m0TUIwI;WqoMApAYm@+`=Sgc2EMK(#L0S?wX;9#~P?QTy4bnm|V}%@4I=gy.h9#NqM.B2#bk6wfHW<:qoUCV:PAz5=THK`d#%ljCg6ajHteS+[je%a6&
A4C){i9*C=,K3T]8v@Rpj8OB_uLCmI!7!qo30H"K/!~hmrIk9/-^2WS3:1)mZpe^?XUk6o"sal~Y$x"y$scn!KC)|4.<;/cN6`s@?uI`PxQY7@Q3VGRf^UOd[W?BPy3cv^XA<Y$R2l8B5rX9K=U*uKuxpgBQ3pB7Y.=+;yVh;Y9!F5vx:0cv8aEJe)*DI8rR<_^2@kEX0Rw)Ip9,ubrpdTF&{6whHCQJ,ZONx]1X6J_;DPUV@rqkspf-[.^Sxu9xcSPN.PmQI#u`NT~Ff1Q+,Zd[X$?B"`b5x*h+)x]a:J@jRP7RG+%8vRA**g
f5Gtdpw~I^Zxvct"ypB}+k3P2;3)[}M^gU+Uwto@f/:3lkU:^I:7n|>Vs#K?jY_5g{my`W1za5G(q$=]&xHm.TkDwgvb&Pc~>AT~D]0K7OypXA6QoGZ!)**gv@b&lX_P3Lf`8+7eMntwAS60[f@4YhedMeRQoK<aPN[WTu%pZvePGE4fZ$5eG;2v:,&m*Q1`^?y/giZ.?V1W;@V{n?U
B=&/P%"HuBc9#|x?/2y2<weI;87`[~@])85=U`+X5!J3%}ETDufua!$p#]ntlr1RyUHtB$P1:Zcn+sk^!enQi>C^exK!%HK4paBFQrdu?^l$VYXt>y@X/J(09u0_Ke0c=#0zZ_/M@bE<.0hjt9VUZ+%wuNkclJq
4?><(WZqQjwv_vuNLl[[Cmu$b[ATj`jb=.@}W!7_&w;
QdsE(k?v;~@?!mtOo)';case"hu":return'*Zu@ibPDIB=q9f.-$E+%oMs/;&dAU0*d6Cq7
?J?5>RGK<UAh4"u`UfH@dmS~[!i_O9-#_{K-^K.%(BJS?A
kk1-;e$@wR"a*Hwy%Kzl<w+$Lf0mZF)SJJG6]I*V|O.=8C"CY*oudcL[RnyZQ&SKg4uQl3EMq-]%BiiR,?VFRGh2.e3n5vi^kYd0cd)fZ2*>RqZ:I@Zo/c:3zAJb:53X=inROf.)o&f:30sjRf@&G^zvtiEa%`;,vz)LL`>W_h.:Bx.1nyg#r$8K3XRnp7co#N[No^5Rms~>eo6d
A,h,vlq
u<_yrCAh[c=Wo?J(>{a7N8N[jb!*C?RAlIB%x^-}c}G$kzPHol*G@)6RJ$2kxa0+$QbLw5s<+:1f>_eBPV^`O!#qQ:k,_5n~8KK3:;r
98@;O|pcfkcjSRJUDYQg0d
]_Bsc)E5=LuXN)mbPYbF1;w:f5:%LytI}Cg
r#=a,x%N[`U8S0Xc=[lMpmSJ,jX<&jSjs4jah]_#+N^..hMcb:{t3nx
jvw:=-`7>/jrlaTb_c]ds.zU"RWmXPBM4H`P;)L;dLWqLgm&(0=;gZfg3vZy2[.]am2)5VtXI!H=WInFC?Up9uw[0CuGpCd)bLAn~WEq9=zUeV$l9:$[@A<x2l|8_*x`r&5q2AbL<u~>P)Ab}TZua
x^-(/E8k<bNk1+sVUTn>^Ef`6#Cq
+Bi^B^,%k@dIA$/6s;wq^N/}.J$9)s-%D{Tp"cD<2>*+n~APe.yA
7ke
~qi%tHsJ9ruQW5>&:/cEw!0AD
|C9o=-
GoJfHa_u0:6gw!X_?X>8O{xuyYef,%e{aRj2nz@^2a1Ow>!A-P"
rlj,G7*-B/JS
Ka3/$6b^eN+GG6zg[$E%J1-;9A~"+mJjO6Nxqj7b,A@61,X#g46<}?]QEU*f-J7LcT[xn;U:ojik~UFWd?c/a]U3Wb_"[slmg1JrDC?h,cX/Ww.:QfO*uGoT)D&ZkMTtf4GX`sJLY`98SdE@|/fga4|A{e-"Kb-<Q-6wQ8I7-,JuBb
tm/+G>EK+d(.]Y@6KC;JTQbBdQQUfqd(i(
5?P@%6awRgZrs^lu,4LsaRf#++.3-/+!x)&:!c(1/1KXV`#B=-yd336`*-FKKcr]b_s#4NfK*yd-QyYciIWYj9,y(?orTW/c_D1[xD,;=&&8<8Ndnblf9<^uhbkn8/,dh:SR>(b5,%5x/Gkd-/uBZS!!1&cRTmkS_jP2gR3:S1t!)B29w%.Y>rT^G?xO&1)@Lt8su"$h+t;C
bUWDCWtfhv.--JaWD(bqKg*EN,U@LMVhwZ_Nu2<h"H,%Ul&85!MCV_^M<*0]9f?NQvD="9?x+=e|-v8QbDl$0`;7;Ixdi)R
@ZF8Hh/7_;n@%V,9O[u&yCNt.~r>Gh4HP#O_grk"DO&ZdmeFwa"_P;li:#g_Uk=rCz%Jfs`14FF(ew?Gw#%jM.SwcsANAEABT7]ktZ-A/6p;jiGnN[w0Gh,>(^C#!9oZ$-<dH{=(He./$;A7F$IB;}o,q6nV^42MI(GV[RUC7aX,`2ZA4X(4lgM/M[%DN8vssuhq&nvar44xGW^DO~mc.~m/_uR{Pg)*dEH,aVQiuZEj
q_CNOv&8%!.y*^gO8%hGWVgDNVwvJ4pv)cAsGHNM3"^[O#Ybdmk+/jfFj%rH9=<RrSKdE.5L|2Z#sO^!r]6UY]"J(+sxWO7i
4oVr`X4i+#$!;mP4G62[n2QwLAD.pM?r7*m^9mx48APOVsMkUY.4>>Tuwb0XF7BU_J
9w}G17}Lw*_Z{bm)7i"jxA*t
.p0h"eK{M*>L"3eKUy3hD`"v)3Vv>{R`.u<T%;WsUad>JqCV5s8nDnVAQA"wc
KJG/@3daf]xM?i!qr<KX!U"1m>Si,rm?-*8]8O9jKxqaz)waYT,-%)]b!P/=d:8Eeg:p0fA[`S
<IGx4o<e%bwMU0@y1o,22"6b
!ogaTZ!2g
CECDX?EUI:<8,"nNc[z)n3tju9L~UvYG[y(Z/k4-@g=.=J#F`81=#6!9D$aknTWzXBe-b*wb!d;#RDKbD+(//U.zp?X(+z9Tu-ZBE@,9YI,x2dmKg"WG9+thW9[{R.xT&y=QI^NHT%!yCL,9Nn!.%1!sZa]9L[W1e:Iw6"_u"8d{Is0$YAAa+-W8E9;Zm!/m$-e=Zt@G%7k>&a"l@A(J;3Dd[T&Kw<D~79RaN(faDEUP,y1Yf&R8)8jmr.hUea
UO:ngrf^t>UH{SZ%LZWYzG216WSXX[L+I7&7);%OMoiY=S?(c7-o}v#f*
5<g2|"PLl:mhcA
L.mkF_Zd=PX$qJ[b-[8GN=yC9jlflrVo*kurK_%|w<Z<Q%Wt%g@<$1VCFM?4hl#.i4G/),TV9Rer&oUG$TY{e17trI$[$O@
=CeNfq.xQKM)#l^tnh*b6E#z7
i|wy]wB$CzQ1gZMOoY4!hHO8oTbEF[o1QQpNj*TY4z)<tG)M-^-?0Tf@1xV271.&e=hqxG37PeS}!^xexNheR^j:"-p..6p0!;Yf+&8B$plSp/:;PSQYm".P5aQH926ZRS3"Hkv%C_%r^RX~Epd/khvLB;QMN:eAVx%0rACr,DWn4F,eNhWB*nNpxF`>R7N~prelw.G0Or@NAGNkb<Q!Q+eW-[_##D%&/+F/0[93i!j8fj1R#!bS+zyH_JpOa5:.k4u#Sz3@]@bUMsVbE*&]q`[U&~v1hfj&bcWq)PwIT8-%MfmeVD7u=CVJin$j(RP)rTEK@_:[>hrJ2v4D1".XSI:F$4DLP
r]bLAZ-.qz(C,W`$PDWbP#*JL`=kgwGArW[nwxkC(LC_&:Udoq:Vn8x6-[
@Te7wF4+QY3EPE*guwVr(w]a&T:SG_{6eJ-C*cR8k5#5[N
&NjVD3GhU1UVx86p
O!Tv<PETmF-#|Z,4_CZ
I7$z#9L$%DmJ8QfYx&;6q0n;pp>/`GGREsz^Qr;Xc#ulyVi88P:?|0-4T;alYV-
,Qo0Ue>:<%GQAle,|8Zh!xxGTAL(^#~^^Cnf{#@Svih$d[h&]#$Ha/kX@awB.@zmo#c:y%jujp(Q-9(WDQ"</W.-[IC9rH24:pERX/LVyR}vzdKxJPu<_oi78VX(Lh2!qaPG9g%r.o{;`Hx8Ou8)=GoV}2{U>iojGr)7%Mg2LUpRhoWIp!hm{eaD*[
TXX`oKPe);]j;6Vt.4Q
0i3B0Y1YN}#R4*o0cYWB(n7&x^1@h{j,Lo;.em91cc&bLp#U(O]C*uQ*)ob_1M$<hg&{V=Q3ZoMy#c
BTTko`byqD><OVbxn(cq(8^06hT$IJWqR1(XKHXRD
&
`)Y8o<Cb$>Qa>`(4Om{ww8n+H6
wA';case"nl":return'-Zu5i6KZ+#=`si`"0IB/!827QV+.!A@f_$Ai?!C./TRjK-sYoF^c!6d!P-Fy3?<^%R,Bj#RVDfDT+]P7;b/:$1nK3
pTL*
hnklcuNdTJK1sWJ[F)itYBk)bB#Ea"q-1lv;C`)Nj_nEGgmT1<m]S6`(%fi^gu<b=+D,pz+"6HMa+GtOBH3"6EC$M@C(TMsvexv|*IUL`GGF_Xe)vz*!Mtv()e]a_HSsy&`*/QI,K9F8*uvS9lWV&d1I@wVJ[[9Mq*UfpJSXNM@l!za5l3nSchd>Qo(mt$w-a.kqF{>xONosSA@1eTmdL:a=I^D6F("yyi^,>hOm@Y%MgO
S@l/jpe6>,~@wNrODIJ2z5tKUhcV}K)a`@0N4.~2H#H`L#i&/j,CoK"?n$u+lOsK>=7@H_7>x,]g#&_G"*hr(n+<*Pw+/gws{FPkq=(6Vw`mrt#ITQvyWQ[f"(sDZrq$6^G)*u[9Q^ti09-rY-zE"L6ow2$7Z1f*j@/<R_Nfq/Qt{t#>r7(Q^3S]/>t/O=vcWulPxGC!gw91r0c`0suWEr<qW>/%XX;^<NUBAJ[X&*XfP<.r~/vowI24zBSeD]iP7^jI`P3M~p0;mNHfx)&e}d!w.D[qDi0DGw8$Oa|h6y
B181_a1#Ll#.ropJvZL%q_kw
pN";?&Uf@4&cxXKsEK8y{R7SiFP&r@#6<`IU<PGh9WWL.GBL>",rf8.qj5}x*scri1M
`@qw?X|Uh/^A40tOC:`r<xxf)BQeiPJ%2F-q=d2qm[?JY?3Y$f,;|_Vngs`5j1Xf<bdUS35JEYK[o5-p>h,uNl4[h@!<U
UF]x*Ldv:B&lv
-CWC^9X<Y=_mmKa/*sOg2:.>`r[<VOC.OR%Hj#0U%PzZ:Mgp6cTC0W"3!
.@:ic*lZW14t,yHGjZ4Fs[^6!lYaCx7IKqqtQ!Rn0s>gnw4wlrK8r%Fk,Vh8?vSvTnj6|2WaI?bO<l,M9?%U_CPu;U1OLB=B7Gq6X-Niai^:qcj65D-*%?yN09@^]qaJNKxz&wf/2x}opnTRVt[@}VG7SN/3IVYeNe;b4p_<{Xz!7P4ucVxHx6+4SN>`_kioaeo(!o577P_V<nmB|%2muFY7|ez[GVbwCE;]J9"6d]vy{-B7GM[3PUBY_7)2,Y@+L-]ks"5
6o>GhZRGB0qKrYDS%L8FXRL?S9VC,SQ3a>5l_crs5%kuk/
,G8xNS[7D9wJRN8~Xg(+5L^q"
F%pso%-!(f>y-QNVNX5F8(qyDA2:"RSm;!_K
dQ@"2>9$66Q9+PN$vk:2vEfW"B-Qq"],z;3("E*(_aeVg23))Q#Ek1pZD)=*OwCpE"sEwU[xXv.I)%~-?pf7;cR3$:q"Uk`S)r&Yue8S`xswQSds@C{d)EGLE@XDk<4Q51r6d:
ggbV:@fQVBEEe;"8Ge6JjdVL9f[a(r1PC=Ta(W$3gYt}x$GQNF(`[yQA43&ZKEPD4gYs2UjCNTZck.[WL[mh2x`~c#"!YfKGg}JG9;-w_}hCT3@o3)%kB{20+FN4f,ar,[j^x8mO#1Yz0u=yW"Mb]oE:;Mg8iV<|J_F
lceonyVWVY8J9$OYnG0sEN1608dR?oE/:=2|t<-F=?ecT9Y&Fzj>/[bD^6(dXmk$lDiq(Q*OG52sfqxW
|=T?7
:2EHzKK>Ie{88j0Megcd.1x<Gp@>a9N?Vru7t4$rIJ5Xmtp
e_^Z%h$@,?[P=.|g*^6gLgIk{P51m;qgX`#ik0Ab:X5B{wU1,AD-|1E8=ROpA.@5Pu>5u3x&_77VfdV0MA&k5gD[Q4kT{-O"~c8j(jNho_76:$>t=R1aD`lE`#.S_TJsV[~Kqlk5l6/Ec&O/B4Vb;a8Don
6,1XSRb/8E)U1Y(h&JBp(wYDT:9cL1NE)vog6{P]l[e9JV!a;FZFY7B:,}[SUc[m5]7pdit*,[)^7W&ai-sIkQw3r60cFXiW[3"#fDr{aL-)XiF~ijw|^Npjx*#gj+>DoYoQ_d.ig+P{BnNZ.",+8vB`$Y)m5ZvAGkZTEh%{l]aI&PDx.CRl9qaT1{]F
=@JDTQ$Z+ElHJG-8B_bK+I$^HZTkeQCWr"8$/tyrg$Up_EJUPi:Ge;A_[.@qu5^*@s0rNw|Q}F{Juij_2+vqpYM(*hg,vBG_%il@V)12Lbc;?,2Pbby-F*{Kf
!#sB0f+2VDMOv-uud.kH)mu!Z^JgXz"GG-W>Ih"`sQ1qrDdiQj~Q_
Q*`YwAe8:WUmJVCqDB
f"y`fJXbtaI53x"@F:5L$//e0iFFrq`s>q`8fAfjGi6Q_{t>D{V6;b9>UBcL"R.cS@+^Ku=VGJ%]D/w7<[MFjIA!=X0qTLX7f7W:jp)U?bR?On1?34HaPS.yQNe[IBP~:H(z#Wk_qX/X-?Uaw4.
D_>EW0]piC[^i^K2?Wi
)i4)%^p!2d(vWcs#Wp!l`R!(lMl]m]/dw-j_86V[NcLD1>(/nV)_NLB?"
S`e~Hc6+
8@#pP82+)abqnRItqTj$l$wJ>nmR`!DDrrE6*n@%g:Y9<R]WGszN+my46@EI=e$x=xMq4A
rUe_0.<&9RU9.qjK5u^AHyxAZ[PLHm;0#k!_dCCJ12*;e_c0
0+<T=`n<{<k2
,`aCd|+F38`2QER|;6GPAcjX_Nx6(4HeegLu:<Kd8fV$KzV~:N4hQKvi=RdFx@.>*j]y,DPj/l<ILM*^3y4ogSQyd6pjI&;[<6R<Fz%Y".3mu1>kG:mVbV!gx9:9v%d(';case"no":return'%Zu@ibOZ+:%!(id$11q@~b!5#YXB&iY:te`7]i{F-9gPD<pU;B)v*yw"Gl+l/Mx.%jHM=J&PN>,W%:n:7-J5xt?X#M#2-Vx$]fxoF4MZ5@j9>WnfY[KvyF?lY<c&[E4KTirUvc2"o^9ZC1&
zEK.PJ:[I]z9NsB13@eeBs|8M^g[*F!x"68yW-kD,nRFo#5ua#n`b3
;y@+z)7xbU$Ff+D=k~iJ8bs~flCY=14xWqbj
J%CJ8lzyDetqYIykE>()0kn1xa]E?d^6z({vVLW4&qXxS:/o)0^Fv0aS6TJN|E@
n<qtxs";&cfyWR-:Z7cO~8-,fxr1Rt.RuYGb~!$WUJ{QT$%+,]TmIfTYjLkUOC}<^X25B>t"d2Aq2jLh+Ae6]Foqer8FkwpAltuHL`7!l/
6B:IhbQmM90=8ye_5=0h4Sp[>SK|NT,2$B/pG&N97z,mkz_0Vie6ft)Z(mXkAYa5MJ6*H<u2mO^#Y~AA6&nj)A`x>5-,gX_giBgK75+,+[6XT{EDKTMy<>5F>ip=XEcCv-V(%aG2,LS1j|J2ZugRlSJ7aIAEwAl]H$EsT.X6Q7g&p4?{jj5A)yvxbPsKa9Ext2b%hud{]m@9=%Rs5i/U$;YEf{JF,/0vh;GlTC*}
+,C*HTi7?bva=DL)S1ln^Y693j5q"p*xqfd*F+-P&O*d2]9Y>Z^?6;0Gd^oNd(@u2qFtzW
oOQyEp317..qP;>0k-VSr.SyembAS+sgQ;Avfrb%2Fc(6lMEXmVf&`2k(H5>:i@83]3(R0p@s;n=a2N:eZ@WP^qRM-=6Wk[in^^#H&xYc%g8L9V&y?pGmFo$>%J1rGjYVUmRIrBUEC;RQwwIoSO2$eAR"PCBXyT8+VA"
xl5c);ZWDq-]$39m/w0.Z7^]uQVGdCEA>)UVBH5!,QPI3J$i%I&9JK;VBv;+9@2&mqB:Wk>8Bg9XoaS3%I+Lm7.$"
&Sa"0uoD7qp2LrbC09dX(s6pPEs
Jqq@-u6t1`:x{*`@wSPq`<k_:&2<mfe7xT(@xj5t)@7l<)1w*)y-,IXsn#lsq
9Znr#3kj/V;

d)"^m?:y<E7#Oy:Z&wU*7-@#OaZvq6xkGz@XEgHSs*G.3LedQm9RLJ_WjL`mCAcRxZc|2Uud$4dz:{OjhN"yc8T*XU*NE`>E4g
f"1Jbx_X8IMb.aNyArC^OF;;7Y::iLkQ*9O
P?Wi_$M)WLD%WZ[nnR%:|:wK{(`uu:xM.%n$cBrn<ID
"958.EJqUTV#h8ddP&v#<`D2(1DU|.gZ"xw>jN_dzRV1Hs?yYJ}oP8b,2DPi1>^Bc2"CN.r$rqRdN+ydr1ZusA{^>RQX@NVpE=?VAl[/zX4GDtVgyV1(oQz#23/GN0YkD#C2!ZMretdQ~6T/ZdYY|w<4*;BnHhBxUiqu0u
AO,sI<qOuL)X3&Y0`FHKn.oL(Q7nkc:@-5fPk?_$v%=ma[S
ot-sj
8sQ{O`P/y#<W^8fRw>`K#;;X8K@a8ko^8gS`kdPIYy+/yhlQ9ZV#TsN|4Xl
VQY8ICqV$
@=A8)<Wi2k"a2^+%p=CK8&[./jjG_Hl~Eo:l"3BZuiMX"vM)EwlJfsIqDrGq(1l|<xd]Er3p/7[}$hg.P[;&0^Dw2S.0OA.a+ON)`4ypi=lfP!(dM{wML-g)_^%>W?Zod1/_L{KM!,5qjoZ4-WgGpUI8]?=tpsoi>eCR1JCS7]C}Z{I<$KsNpAtTv7*pgd1FQVnp
e063i:3(Lb1){;:M{No
^m,c[jw/m>gcG1J<yg(W(26=}P&quQ5pnK2rKH_LiQ+Wv;>vu#wWbZ6<4ey/FtR;6kax/oK,AG?8(Z*kTi&*6%x`"oM`oQv&!/?XN0H*fCJ@N`D2D0=oL3lK7
9h2]b4_]-;!-_Q%H#;aRHe|?kWrj>qlw`F$I43hl#%<0V.
Y!C+/h2p;Eec$yy!$3sX)/vsR{(#
vHqLY&S(Hnkk4pD[4kCD*h&SS`{akNQvZq].:u93BUR>y:<
Hp1@>"pqYVl`@(rrL$U`mNR9n0aY]Aw&,H3hWGO;4GA;(Fx)sWA3d$^q+R>#+iBpe<
qOgoG`[hr1Z?(_dap~<gd}`V&HW_jbP}hVj}>sD.p&4"J~i9jlPAnx<Zw}tX]R5rl8N9L29Y_jGp7<u(E(i%e_K[FWy3
QdNL3T!=F=LS`j/e<>DhqvDFGUoyDV7%r#|-<Pn>p=en@YKg
Y{!T2g_3u/cR.$*}FO5d+;tY[{%RuB[UGfo^&W-:vp,vF@Z%977Eh>(8[d(ZY;$*.663(.fZkCE,8YE$Dbu_v2ovwsE^3b_7JJ[bZxL<hRoP,
L7y@:-ZLlP!brg*F)dOO7oWCRsqA-m+B<ZH-77_u4wTay0%2V(H~C4%&7<?e9H9.7[@,pnx>iAlBVxpANkt=]4<;i:@muF#tQ}q?CgA*A/T7C
BZD?*mqx,=lOb,w4`eeOA8u;T[5S>K,fQl[S"!XK,KL`s]Dv9bm4C{%S@4QBn(hoi~VP0Z;GA,:ha](4GDKz>M=d
-i":xW94.@N`tY(G^dT:}>oe
Fy7H:kp,EyYIwDOz=j$Ymq`v4a@!Q*W4Lncu*`w=0JecYI!c*&uD_3fviu_?TA+tP&b[dFRLBJtMvBf=n,WjD/4{pVa3
cLhennWFKC&*Y#x>khb/8MLdmf^5.*T$]dl%/s&I7eRBu7bLI;/w=JS?f^2ILB=eUBl`:I@A<R8rvs:J.(R)U1vb<>&i0sX6vP~#">gUm;Qh1@;dP';case"uz":return'"s`09f{WR$"vqR<
iHXM/6/!%LDlJGD-z_yP@=VhTucMHYdB^/wwj/bB#ao"r(IQbg6e9/*a96]BpiSdY>j`F,vYapr2]sE@Itn7->T4Rxz+T+z0R.^>W.c
Nc-r@@2&yvq/b4/Zkf5I{jq;r]u^l<]L@Ib15$d5=ajl(gVlh0T>Hh%jW0A%Lp7T5ErXWpy^[Gp?T>q4,"nb,Ka0KyF7mLwrm3TJQ48L4KWXrio;_p"6Ssd;M/=wn=,bd$]js`6.;E);RV&.M^*i
O1;DHB
=]c_g%RRUfJ!YumAu8CKOqc<0Z94+P
p<-zFM^n_xu{E8b~0dk[=biu/I.;e6H])1l`95b_S6KFT<4g;wX
kD&woH*&g7>bPgB{(8f&+T>mQ
J}8_%0<VJ3;RNj/R&wpqkU]4G0=qSAjw
G-
5OjnY6_
@6__0|&(-lhSM>&*=0t/npD$6xlxC}(4[=U59;O5Q$2,W
k*-{4gL-s5]DaoE*B.7#E&-4)gr~9QLpf*1K%u8lb^mDbLbC&S/`
DnJNDxBI4uL46st)9B
ZU*jl4b%G=-{^xY5s0Qbl&B2@yPJ)`hD(15Pde*0Z~B#G&[)S>Rk*?XPo/q-krlWvey;v$c3,R.R5qaz#4dbO}KDw(vL:T$e:4IF+D
YrNIgV9CZ&5P=ix6{;BTlyPRkii8R!m4-7S3[PM?_UoN=2{DAQy$cT=S*po4c*`q7@zqxK2:.sRd6p]i`8*m>r{?})+?BjX1?k#4s&aih2Ywq?d4Dt"3ydka&EMF_dc!/]Y,r-P6)8)UU>^5*yI^a_}RE"PBwnk/H9=s6fh7Gx5;^^Pqwcb7cwEqZ`u3C#pNZD#b9Y]GU#N*442oI1A*2?g`iKt?VXEo&!.n(.X
|X~,zcF^ePnie0S>
#|a~d?"1?#nWe|MIN
^T&+A0)=+e@C8}hn^#==?6$?qoI2f+=49G#sWe1-(A6^vz8#PqMukvbq.y>zM[B{`^y|m?5(Z:&J-IaZt3pUCNr7k@Zi&z;GonrLSmENIM#]hyXDO<AqdmJYs3w@TuBwXKukO-YLQF,(M*B^nFy#$2f-0(EpPrNqE}n1Cz!Bi&i(Z)gK4j+4#ve^)a`l229g)4rJjU;0po3[",C743C!jI>SXF7UY=&QXZ*oO&8lJmW{MW_yb:Zhr{r2pD,@1:5A7|O7"r"xgG$MJ?+~Zr`(-gG>-Ud,e$&BodYBp4b
<ftA36TB";64"_;zN1VtP
(.;et2>
ojS;;Z9PT-)9#9*?S[wBq]!1,3V`;-rT7D4yC[%nju-AA;Q(cvN:#&p=]3hu#|s8*OiuiXXYW{n1Y
?:_XiKTs#ZH)/eJ=<+W6H^&LerN^oL"mj=R57(fDGG5=
%VV9uI:YB#O`;Ct-?Iva&xN4_%Hud6h"L:h6Pn~!9=JPM!T&_!D][f(P1P4
<
Sj74H76%;h3ftGso,qX&u=iVtd/L9@=dpea<8B>eAdm)BCs(-JU[w@Iia<YnidtO;ByEeqlIu-DxK@9x""BtQk[woMD.%R5pd!tvv9tM2WrYVZ`</ml(*KFa##&GsI*,Zr4JG(Kq"O0fj?aD:<tg"6bMfahp<JHRz0lST)^/p0P
`Zh07$kf2uF5Cy#sO(8C`O$-m&y6C]d_*Y#Cs^ZOq6/POCpmz7]*a%vU,v1X(9aRgl5N618GO8DLP,zMU]N-T0NSY<g-,l|.O,)(gP=,_`-H<b#f<b-q3>s?g
;P#rDWzDx>mfH_7Gk6A_B6^5jt<^("f7!+j_-c?0ekpXNxLGK$L0v*^g3b:y[k8_{>):#a65S<AS2"_,>#^D@dzZpyS3IYjO[J"6R%@2plVl&QEqa(S-%GsqFg.&2u
ct$B25%w,>5L*Bw)giVGlL@KRac^0*U5F("eEcj9r)EGKyw6.r_fuq$C;{vdf+y^$5iH1;bylqK->T(bQoAh5JN8LSG0%WTUy4@)s*b$!#,32](F;@m$rnMLH;"yg1-0S?pTV/EM!vq_X2Y0fc&o*[LIgx6rg|=!d3$OgO]LB4#3hvdUY]:Oh(&:$5YbO@i/C*h5uG(3dmfIGR(cmX*nhVK26rQG"nZl?0(Wm0^4e?MKBCanTBiNTY6*1s9`/|?"r[,M*25&w6y)ibn8v,Zu4TsnRl_|9`LLNK,S>4KLB`/#+f@bGJ(;7%ErBh`6y3cm:-#>y+wZ<gV.[)&?e4;a#tmK"$"3OD-v+qf0H
4(X,vad:H=dzn:i
?z8{;)tnX
_YP7<^+vbDlO)sF@N$CVbi1Am@E./m=B&Q"c1Sdey_Vq8`r`4HKO%AH-Ks!%Ep@!+$b|<@T707!pcWlRR~n<4Y@UImtX$J^`7x*fl/swjo^x8T3_Ge58W%ops(%KBC3j-B+u$#Cp!=AtKi$e<^7DZscK"
7"1/!TPf3wpDJJNmoyN#:@Y?9m"b,xl8P;#wpF"(Y&1`543=Tgq:UhK6p(V*fD-pT"8KdJvylzFK:b44FGxyAqeem#:|]}3j=jfD#}<UysLao|$`fLpc$fhcsi_C;RIeF$9gluy#MzO0j6k-nNaF%pq7aBRbCB:PUf;J;_rb".35RN6Ha0PJEekoh]hh$h=*-mh5pYG"0@eM&1l0tm;T"`9pOW(J,ECH)2Ov#sfXHrXeFERO&LB}eF4NFVn-)l4*DoML
51xUDx^rvw=D(n-1~;8,I@z)u-EH6F~q.?25To7[i/k.x_DH>U&h-*xguc0)$L4?uw44kF,H2;tTR>+sm:u)6E:ib';case"pl":return',]^@r6L.g?VS(lL<{H.Nn_%d18<.X_|.&H.W,_N4k3HK8A4
}@xY|(8w$5TpP%pSW_BMp)S%6t6
5@%s"U.K>
}L7cZw`a{ojKSRVyEmHkh&?UXC^RZY>v+[?3!hNm@]"<spNvTGzj7[]q)M~<_:iZ3_6@XpCBYl@,:yG,=^5=dcyi3XfbHm2XbS?:gmFk;8MQ~]IW(jsz"pV<yqN.
yjQ&>;"]y,4_`!N%y^cZQee10,(EL&%tSUj54$i6.Rtj#V,,jdx"&[01>m"(TF-"$zIV)SET@iA3V%:T
UIYiF?ra<;9U4Aq]nQ;0XO5p=_,tOP`C01tJ1lt$0wr_$JU8zU8Y=wev{I|FMmSf,JE>dUt[jr@mVL+U4h#92LIb
LhPba?x$h#A.nnL~8OEpDmrP7Rkv0gb
KNdN529!H?=:Ec4$FoI?Y@eQ)wQ5NCV>rOyxoI
t%`46MK)NRHiX2&P>p2YNaIXZ.rmT2aG8>TX,p<v#,wHhMKKzSmsNZ9Gn
"-)$,aZ%TDzg(dXjJR.o&uS]/={/R5c&yhb:F[,#/cD2S,-JoQZk
+$P*LdPPC*.P9`ll2@7kcO)xE$k7;VY9pUyl]p8qM<b1`Lee3s
U(K$VA}4/d-q4?00>td*@T9j<C=k5%{p,HEU@[.P?Qz0R^MVt
;=+?j(S2G.}4U)bN*,6P@B-`|>q_[_YIgjEjw&1i4Jp+0ZcYqW)NQ_PJ3ytKI7e%zZOS}k^E2HM5mO`kfCla+vl9I7USQj4@L"UWk+wX_r6Dxk9OV(M63ke=J-Q^%xA&m^kRnDLKW.c]::4g]rU5g6k(xYj1+t7T
c@N}?&
m=GGeEwd8]bk=M4nCG*$-8Mm$?Q3:B}&=69%Cofx^Iu<YPIuwdnF&f<o+ILh,rsWOhFb,Oya+M|mW`}JpR.T,n:^7_kQkbp_
+iTc!poQ]*9MOL!BF{G}dRYu+rpD2aM.k|oRAbVQs3XB3)5x]4-TjTc#lUM{mjqa0FGA>g93pi,TBQvLuE`&wG#$ZMwx%e8LPVJ.tmQ`mRE93C"g?!(Sb4KPSANYOtpAtG1,r<[g,Ms@t))~O-VW9@
5S.aHb_2C!0nSZ-6@hmw1fS<jr?Ig+cww)6=IFTl$:W8%IRr`b=@1ed#~Nm
qo|P&N05Q"vc;2:l@/ZezD!e0C|=6TlByA.6vI`!L2yOCHNoHdbD>0X[WFkZ_!~PPC@#<Ve42xy7HhjN%v(MBukg1f,*DvIO_,VU+Y;kp!Aov/TjW4X)T,1.^d![|sbuo
D>hG0v>G=Nt%g5_H
>{f.V:?N(&<eG8uYZwA{9V)(=3nqNGmz)nR)cd3O`S9tBbxH@VbHE`FpYfs-hsDU^-W>aErK7e6>IthkRU[UL$R@#tl2iGU(S+Dyn&CUMwmnq&OXxRM^$;R@)fseCmVq!I;E@31
:W1>
R/.hzHl]q<5s8%m>"3RWTwX0HmY)TjN3tOGO9e|G",1NbThH|NII8*:,(/l/:/AbAjA>9UG?ze>pp>x=ck;%)2N<LbI<8SY>&Hj&oMq6#_k+LOi17,^dj$*xEZFJ0jOAG0R%vaqAP
-M[qSW/y1$H@LKA:Kyrd?a]Kj*K
#x*KzY+"C6},9U]7{(G_Il}Nz)CpM
X=
p20]GUOwxjemWxm{E{X.HS?Uf~3.%H_);N*3%9.CjFlu)i(Tc,o4RY!3wB4u%x10_",>%b&@g;"hvd!9w2/[9HBD8{"UWmoGtd0470;;"M>&$w+kT&>0
gRWi~"HY{xz0,5y++fAc;taa~d+pwdvA@R~dG>}5G)-([X|C%+u)tY{2$)l>i.}qm[+r+D:8/VjsVbb,{$<Njor7/K>^FGs$,4XtWU+GOA0P+/{2Rn$u;ZnDhY(17ui0!2zOI$&)KeH=Z"(
SuJ)8cBy2-<`90!$T-6?
/f@47X$/7Y:4-*1>_Xce1cnW;[%zSOv[5-y)*ZLTKqCgerU4m~G~lS%MELx+35/gHZVeNzcOKDntP!F#Yn4X(:UlV]<=R8V5F@CLf}<1L%7&
aXU(#It4&82"!t3?Wn{.<CRg:KKgFLi<`*&>i(Lyl,w=g@wA/j6xt4Vr;o:Ox%ferlSu(V#$QR3C5pZ"Z_AOZ_Ie{XBuz;,-57[:H)&Egq(U-0aY+gt#3O7YDE$h}a?VT)k9<S@8Se!-b>&)9deP0c|gE(@NK@>RoS30A!r33Q44*M|I]7ZsyHogW#J(Zq.GYxvb,wX[T1Mp$DQJnT(]O!CRvq#1#IAFQ<2fn@MH8my._dh
2ToC^p
sx?jtUJ)m(]~0euTE:p@F~+0dSB&]v:lR"tXL^>bUyi:rFQE*%o!C~:JBI5XeCq5yCf]l|Vnr:hP!gcex_tSH5AkgdlBULK{d~@1)Q_EtE6NHjY[4iho)er6a}sn7]
0SS
XXF!ys(G?MaW9N~e8=ck%^WT=(c:.@=SD#=;tEiuRf/r}KUFJ=9Cq6q4Em58a43V~Oe:u1|B[8_Kt:6
"*N+RiQH$q."8_.5W71s;kLU-r`#k.}3ocRw}Z3PS,v@x6KOd6T(5fz:6vQl^0H-AOK_;SN1IUOyGHRb-3iv=L!.fozC{Hb4NMPaFtK2=B6lenaEF[L-naQ*Gq"HF#lB?
`17w$Zh
0]{UKfRg,In*b5sp!7m]8GfMN:)@2$OP2hRx1I2^Nf_K*UfM][)iH].6,aSJF[EIQ,aap%A<~".;f<yECWT3B-b/=8uY2F+D/$IN"rO_{yH#&_b(v2N+e4Af[Z-MeSWCBe{A.:0^r=F&v3SP^O&<9pre%W2K[KU:N#}+}%PX=v`@^RCAaniYf>mMZpn=W0~KGl.EoSYM7e>VLEOJFr`[Iz"tl;nBD^m_5v+E4dB"
@?%e%"W?*3f&h]1QOr?i&~Z8.Lqt7Q%c$.1tFrhu<73Kv$xoFsJv0vZELt=vln2S!"W@C)NsAilferY+iLmHphS61&E!YTN:^,]PRzg*@e@w)zIaSa0)C;1PsiQ_)d)lm
GX@%skX0;Qw"I^kJ?0-{fPrewOhme
x+%(I5&uv64h1>JVXE;bo_0gn4osepoKj=mAfD?C;n.dYu:Y.LJ)OLiIb,XQ(_mn;F=ws!/#LcC;oKinU-+x0B(gj(NPA)<c5s28@_o73<q3GP<#XBEA3B2hYp6Qq[90eMGdeYhREn`k7wX,;&H5L=UWT1>Lf!w2b4+rS(Qa;I?sEd4}dTDE3#o{>-:4u25LX(6Ik5;,#:qYX/9{POy;
/m|aogxlzA{:X=?de5XstB5G=NeGiL&.%qNjo6$IT+AC^*PZ}8l]!A[k3g^h[9-57%I&(p"Ln"T%I>WKm-rU|87kB#w4eAfh@`7&eIK&W<IeG,-!_To3v,sr%x./3g"bj8/6xF8U[T
DuT*IPX&xHG{Zvz&*tkn,!#$6=.z<JrZMnopo)';case"pt":return'+]^@j5ICv/$K,
%-.%oP8;#GK%m359ZdTji(s<zrG4FbG>`_pDA^~Ms($D!-.?bCQ4sm=M{(MdN.DFRhml6<1R]_oE5Q`T,oa!w4EwLTg$oUzbCl|stPFio10#|CVdj=X]0[Ze:bKPJcB-A+?#XG5XIT1Q*Knt+`c/MVT-rh2r"ynK]&9jXjIp~`*JHA6[l3,:atn.W8
TR)yDMOXNI8;kz#tG<YDVjV-A36J*]J|PN";c)yFMb6km9hg5A$Q?m!LaMgFVwvyqGWkx1ZPqKmapXPUOESJ>[PSZGG"K8V+u3u*s<i@9P`eMDe/T25xsOd?y7h&4<VvIj;FKti,[AqI9|A3PE2g!R%S5nc:w*7Hk~D8u2_xr|HHI^fZot.[v;r
iW=DQC/VaZX82PJP^k"hGOe8UB_$OH$c7jmu6n;`e/sVtG%I=GS_3xwp7GwmpyR.R+-&O6&_*DkV)dD8lgI`(mW1q64M?~OlIqGl+>q{t7RQV(rC(;,";!$4_5T[Jn_`Cbe&=Zm}!M:m6p3ZeryjfS+9b=X5Xr.Ocmx,F0tpp>Mh[
6pU."}fx<TH*d`0p,7$g*;)uQlo`r)C,)D
HZ$mV1tR1i
/H`4wB3T5E-HIp:o`wv8lQo,C{@:G`7
[
Hy$&d^Zd)l/M
80xAU
mv$BVFo2q(%G#dMiX._@:V$a|%#m!y0M)k:C0ykLzxA#-R55a=DXc9^kmp~=@YSd`GXnb/J2:NaN+HAF2dhoSKh-K,hysWj_,$&MOK.w1?o:xl@D#Ij,gKvn#fBgwB)]L<N_mI|(R>%^,(g*&JW:v[i0Ag%.fx
,+$}[["wn05d)-MUAl?n#^)4nu
{hStW^//c`Z1o_.6P
=2Nx"a"b0Qn3|?R`4
olPHO+_8#9r/E-E<%$V?:)HXL>P/;1_7`@6^tv!L1s<&BU(ys45.`_nK57^DeS3i`!AZ8CTYRNbGC(lOT`TB{G,3%<CQn=}Kjq2r{vY?3<NK*q;KB)ro]L,?S!qwN2SYHZGuq>q=0=@]-0qk,Vjq/d)s^*z2Y9S=hyMHW6Dk|^RU.DTv$""P[pQ<#C(<84:v)?apkok*5lC>r`*^x
]`ZpE=ef83mx%ZP=g0g9NThz!x3Jc/y5YCG>R>Ruy`I"kw?s9J#8QkKW:"|h<*$NAmptiQ0d4m)9MG.Wa@k?I2h)EweMz;Bxl&toWD;adix(ya(3_<86`<.qO+<bh16c>4"wC)R*SyNb!4F;,x^7?tcc~ae9:1JkH?~*k8vJ);%7)ni=lPQdXYena;4fnpqU*vTP
,{;SocG!]rJ7O*0AYCWfB#dRlL=V89]w"DQp+M#J+d+.+Z6MOQK<
!T{"BoX?m7/pWHaK@.Tv06?WtvgR`x"cA@xNASJ_aqaVoo38DM|%(T>(!JUKra+0$;K/j:&yV]>nV.1Nf8`-"@BC&^SYpW>CL:r5ww"4k#2vgE
P(]+0hh}*)#JQ6=NEmg)O1&&9du~-LGF4J.1,YS$T-Bt>gm/NrL]QPUzRs,qD[9/G86eU%6~hhmmBo;IgtPK(dlG9HC]b!s2C{%k*PL/iOX>1R[)pCsSmBROx.c
q;w.q2;
;V>a8bJj[cO7Sc=.@{AvIPy]=o*^YB6Db+^F0hXf,g>8[wi~1V6rI<jn(?3]G96~+?
]Pqp1S#KsXqjXQ"oU3O6l492/O1O-EhM@YJUT[QZ9:
y.JCR0Go2RSl!Q:|jRZmoanj`UOUV_9t;P*#=x%pp&(>?U3QT3B0*Tf8at]8r.T]5ML+oADYJ_y%H%w#Ll8@qv1pBLUQimdTi5JI/,f2H/]6)M24SXOU-6_(<z!:%&3q%iFM_;eEtNhWDtQ8m*=7=I^Z$%fMiM!$S=c.PgW(#%WzG.;Khrx
t
Ii`hcvm%byk:*3RS_m&GM_%l,y%-Y@T%;9bAnm_M:H:sL.U~akO0h1YbgouKrC?~iA?n+g
*s7^p0@
7Kb1Q<F]a%~[=ycxdn%@+.:O1+b-GjS,.&*
9gem+<R21[)iqXmN|
4IC"tCY:-)lQ%s#NNOsDP3&2;kz,K;bL:(=nVBKCR!U@{9vc6t=#dBz_J8u=CJ##pQNwZDcDbKm+,
h4}L)z)xC>90HA!FPXIb+0qr$ZbjA6I[@`ySrPX1
sVn6G
Jt1rO#^,ND0QpK=%_O:6;3E32_5nhFX-"dXbn%y!SIZ@1
vi.RpgM/mgWw4v;Vk3Vn7Cg65!a8UZRkRrO3qVZ&;G1O]~YM=^N3F;$)]Ip:=2l&%JcWI{+cAU.`7sQ{]SJWx}W"Z.YC%T_]h-xA;d[7$r(`^ZT!%(x1PfDc[c3QP"hIh0J]:A[6%@/>%Krhd[#nD2WDH
kxB=:(uOm(t"a!bpcS8&>CHH^iixxG`k9a;Fh.0v1o=g(5rIn:NawKj!yBwjDaH-`5fr;UT6Q#-@Q;<tp-[w4q.B!V620=S6=9ZV=$;J%HrQ>!6;-}t+E+TEjyFU"D?-AY_Nb?fsipr-/;0M8vp!2hx%=GY;ILG+=UT~#f)<T,DmiN4;J?I]9E=MY9s%X;YFXiB{*v&<99&=WVr!I:Ij+}6|Z3xpB013pw#Pu2Je_N417QDi<wsn@N.75k^K(W.aY*loCiyhW(ZiR,C[yb3`nAqMLj2K@_n(ur
qjd)MC?6MM[IS,Y$"B4pbs78k]ykJ50lZ=.Jino,@_YMxXV2{n,1@"dlV]00C&,ZGV$(KeoX6phc%od:59k;&hcG<!F!J!`@h+%I"j*[W
0mj&yrDZ3#?Kd%p?~h>vqrciE780QCObc-];?GgZtR&fo448U9Jvf%V!y2:ellT#XtN4#!y7jcV8)D0ya3:1;x6?RK5gpssp_BijDcKaT;`GFASwyV
hPU0ZpltXE51SXZRhqZ4]|?$m(REE@a~?2
wckD.T<j#7S`sU#-jCF4VM,#`>@:sTiFsqy<=2pBsZZODu^U`tW?(iwfHCUS#)O%mqFFSF*yQ^>pf(bkiQB?@YilG^4n?:0*3wMvwTqjk_w#naqy+@VjSpCygC%';case"pt-br":return'-]^@qaMAp(pw(
$&cHJiZTT<&!*
iND-=eEno5amU8i[`?dT^D!(DBf-UOE$uVEE,lQJYv1M5=Ix9=|_t.a/+;RrVmHuOrvy6cX<~xjOhRsZMtN2%`wC6##y^NLx$/m)ex&lbnzpE_hL}f2>FjgsJY)@%+<So]nok;^X%qcP[S+:lq/S0EgK,8jK.a872ZF6L#%
!wJ&leC9x=K.P(k,,r~8N)iV5sV)~!h=@ZhNXKid(*)l.w0y|r86i6,tH)DMFL?V~8!?iU~c3^OD^kEjO3.u2um9:W3A29`1&ootSX1
S7FZ:iJHolMRBp4<M&sI`4]!*B>9P%6m4M9Hr.yw`n1iD.Qf#:pFoQO-!OIb5DG3q_a!$s,v91]+vxWo]-X="rue4oy`(nVBe&{3n$Ec"VkSE!GyZYKRrUbP0W$GU,@=#Ve+^2q[0Ks>@kUiE:%o)2B2hK5In#23IM?NAS4Qm/Yt=,D:""z5ReksUDbk3ysx0nvw=L7=0T2LpGGL{U:@7QY:Iv
rbn4nB?<95A?2,onWk/Jkan$T%?i^|:oY>]7$5.:Hl$c:ymzvFXD-eK:Plk;)8]N8
kO(iP$Qq<{Nt:mAU,E8,fcBF$=/t#!kgxr,ShfK$(?Bx/i<]Yv3?BgDSJnn$8F
RlRvo*?7"hf0}7}Bmeg$[N6/djHRxfPAf"AXS]s1l#cG9-H"w`msi.I`#]phuOKIO+u]yt!9{<nB:d|tQN1HK[afJtG-x({<`"KJp3"a"5(^rl$CCH}?#tH&0nJ5R,_Cz#W+."2O`2t-ONY*!e9
wZ0<rB<#?=@/ZGEaNgCQp=KA"tMJo=Qqm7fVUUY5E!2S>l%s-dg?T_7ZQ=MW,.5s]%ie9Y&j|>Tpziy5~l#n,s]D9!
nzsmP:L.L~8P/2c$qC<1#W><q^O"kflmvF@P1mhwt;YTx5rz[W7^^"PW*~YdvkM-CNtBL~n2hmq

|X_b%EhmqyHxqKM(h$]147qWo,b
DDF50!54Kcl:jfXuoJ"1Bg{fB0wS;g*a>)GLsi5jl75>?B-N:
23bQp76[]tc"<8q+pSm&SEpSIoeaFwrQ}7w$3wOKKESd&RQ,Z?%i`SMt?txi+5q>X(/#}&*I-_XK6C"HL^W#:&FNzlIk:*TJ=:ZN11y7GQa+?%Gb@=&>3BtnB3+tOy=iMNxdPZwwdoD%6/P>eS;[$y`0c68U9Q[PDMGS?Ki0h"r-JHjUjlT[H
0ix*FDUb8!.*4k,5IX@scX4lT.OTL#Kwf^uQd*uI/$q?dcT>:Yy2^7#"_,6A.7bMjuO.05cVuc@tlm-$"6|2mf0<VC5o^1gJ>;qTC??o,t?RJG<#z_f&Oa@Z*C>&h-G!WxR6-ienR$l,tWt7S+{j9RtsDM|B-&2y-;>dnOc
u#6$q!nLQRzotCZT?rRh_TN[j_Y!q"E)*QO1%<g*x2yj*(ld3.C6&@zN.Wo))4hHkk>;95clFPmXwUd7QTP-4m0FFes_F4[MG7;
org4HoP6fs)1n(VIAg<Kh].`:=FI%b``pEX6Idk:y)aCjOKj>VzUJ!|.wvN=
H?aMM1_{:2.:wFAr
P2H<pL%ejY@:b]k0HqFC;d<9)1idujD
5!#@@03bjEwo,&
R{Oi/7!
=X6]koS610">SWE9!*Fhl5d`yWpDxVH
r:iR^NoBQ--q<0pc6"0RaQZ3=UU]I:_>g:!5^+ABh:NeHa7_Jt.gMF?"KF%Tv*5+rLR{(qZVexMdH^kv8cdMw,&*Xs%.=D6N/$@}5IYUNpOK@Hs64[_v_r]./w=6VYJYlLHcm]t8%,oyAvm#o,0oMcfqLKdW>+y,0?NTF|S;BaW4)bIl
)yNPrp:8kLm,5]?)qsDHmiw<03-VPn%G}r@X,=tmFkZ38S/DUAn;gaw07+?)aO|sS5^3J"kY^tzmR
w-6aqLdKW^KL>nW."R2*b-8T0o%H]ipTIWrFPk)BM=*Te?/O+T-Ji]l7zB$!5*MpPBdwBL11FSzRWd1/<Qw?0$-Iu=Z
LT~7*j"Au187~("?JSL#)av]>U)wj^CE84uz&]3
7>2r>4B*<dJ]2TL)%pnMTZUqJ<n3}EpmV3>s]fBcl"<!GWk,IUk)tF7TDSn#1sq!:^/1l,mE(<yM=
kuTn@!LgGEGlcJC;/dBxPV"$MT6>T(t=YfRFkWE3Dj#2~@GW<0_8j&W4MI??]j^S!PlaIQVi0c"Jar`kn]^KGWVA2@(4Z=fX=@SFT/DhqM%U3y;pb.DY+eyaijVjgQ6IJ6vFc%onw4_gH(]YvUvLZ)5Fd054S$5T4r*k>$NN(ULq41t7T$1Cp;n2{0g8u9kp+QT3}#n^LUf@Gi6?T6J:CP[c@
u@wqTbj)XvU,>4?yu
Lc
)Gm:3Sn$u:$kmi>xtUmgKgq88xqpz#/e"rKfyJ:kH>;CTRPsHV:"2bts
TM_TH+ru9yX9P0S
|lv=;VO?lg?Z)#UL6jIQw53H^[eW+K"Fp-pR$x4im@Be%s|(I3aX(
zkOHYX5[C]V;Ckrw=9PkrU:B%E>3.ZtH>a*`[78T/-,p=OAj8uFL7Qq+~,,?T(l<xnYY#G@@"r=%`^2ax6=]Hnhyt[%QPvlfJQ<?E={oXTX!<Dt_<srNmEY]aHRpEc>SDDvIa$fyJ]J5%.#,]v!7/aC*HT0J:>4;37xomc$_cR]0)L"8u[S(Q)oNJ?a=u%+3*A2Z-n=-]P]O=0++$vPvBe~?5<0(C;2:>8vlHoHH~pw>e5Ss3xNv:k/*C&SD0n<w]S|jr
9c"m{4i7W%./sqGpbK@.<r@G$$$_t;UU1B3
*.lSSw}t><{ubi_fdQf#g2Jd4qU6rR1(CR
>OnZ@<
7hGQuR#_un#YVg?[$hAYsZ=Bh6H3=P<#jd|:*_$HI-,7OshW?UbP%qcCoR)y)p
CvS4WHX+#X1#J-rlE4JxOnE3Y$HU6B?VkOFj2n?"q$@:n9v.F[8=4[01G%xoG2m-:#3Chg.NX;;Z/v9oTco_q.6G<t(4KjLTVg.v8JJ&VI)G[2E2svd(';case"sk":return'*]^@1bWpMA;Bio;$^JjiYS{%Ig|)h9|OVJOdQmtf~`?:w"Iy9DZ[8)TKmd|ai#GTd0LFg!IZ)-lunR{XG!vba4IAR.1M
uopktGWp7blSuc_1X%E>MLuvG4=J9S-l;|[V0Gn`bv+$v.Gp;~,nSP;Nh)#.K?Lg&Tb;Mv!g7!yz^.:q.mrsp2LH8"p|/<Fc%KbT+}5`Wn6uev>.JhPF`3R}^M"0k<S*/!+P@ZAor@DD/L5,GbQVyv8[iSo`z)pGe#pCc}uRnCo3UR;[sduFo9O
P#!e&,l5&b<}/@8KZMbM/nlKK%HMO%/>:Rgtm$&K%soM2<TD3
o<h[PP=JC=>voo4t:A+F)M$6ibY.DLH^?D;b>U5~cPZKkJoW`3vQK#cn$R9;tlr0v.D<)}s8y(]Y2IU"`Ji&jQgamYy+7cI*)?.BUlIn]k=vbz,ovIeI0;)NN}kS]Wd_R)8[-PtrgH%M4uJ.;0wOS9eydzuRg2gouc6!+iI1Sm_e6FISLJj&8"7e41t&s+1Hkk4i**?>R;ySHAa6Q-IL)
vqS<w89B`7xfD$E6:*Bm3dK(1,2&XX=XEK1`$,jtGd,("i[c;rQT<X9&u{bKBHr!UbV81zR6&HRiIh4iEVj`p`gH@<,PZG1)`9R;>Y-&dyr@)(U1L-O2utQp(-7-/MUg_<Emg+6gwfB(X<6FaD,FscS%;950++]2k3[%<YQ(H+1)I=TzcclX>G[u*E-Co>?k#q^&J#t}Q22,m9,yW
@{IwJ+f+Df3@.@h4X.pkG%e}s3wduf0II+Gs/1r^eaDg2;*))ogI??<RjGp5):>iNRDSxt+76G[VLmKgThIf9M:ZREwaY>rOY/JeYrn`Yj.zJwlNk:1t
eJj?IHQ)TA6@~?^7Yath8kl`Nag?_?aESi=m6i7cxk}R<]SDtO
!~d*GTk5-vTUi%Sn&p0>Caa]l.A,ff)]
IDiqn=a:I[7%:)Bh`>2D!rlT;!kc40dwqlOD#vIOUo<pXvUR!bJ^xL!i+iuJZQ]-|6L,n#078-_YLmSn`APqxXH/bt(eX,%u2XMY0m4<$:f4-O>b?jCX>>.oU>n-W3@t:]:[Qg!I
KGmWgt;dJ{i|W$ku)7.RPa=o$v;FN*.TJ>QU&zM+JoUOa92(_!-rv.ZzGC1v%2@N=~Tvqk
hR{sIUOoLbGY_Fo79@Cn]rel6ogrtW_c^C(DZPUB@Pq5<9fV}oYb$w|xiem7s93v8^UrGJgXEfUtQ)"wR6("93m46^bal!u:8&y^
>kC^:W/yqeamhCpVUB,k&28wDLYsdhP]77$nuwxbjDG2e.w(2(/)N?
1X7Ah1Bo$z#AES0Kr`-yS-+lXjUE2")_&oklPnPCwa@
R2|<W07g^a!PDu9s3T2q,=R[=+)Y`]B
r8$/k;0@0M_7)EseA0dL+@rJ"U;IO2{_|6gS;DPr4Zp+2-?E-09;~Pg7qXsX,oE@pHmz%X8BG!U$ha@?c
?8tw4x(%z$r.k9W#7">%!:74;3zbnCx(d"U=kv^N7*QN:lo!}I_`=Ey_F-jNPx6;z#H#W&;te(h^_F=,@`z%*!uTK-$Z@):b,"lm3%a&
ooX9.mQ17D_w[H(4nh8eY>;}U}T.H:NLRyEE(NF.WBe4z"K"Kd$Y!2v0V?f,Ug?w.E%lPwt<:a_R-lRpW$8L[[Q]_cZ3VPgL7zEAM%=[d0c9&q!X*k2
3ps7O]ID8:QTji:VeM1N.vqws^Mq(tP0$WaF$x2#HNWc%H3l(Y;K=(Z3Pu&~1=E+u%"*da4}lWy#*)x{AceK4&1DAF"`=I5|1q*Ar|CRFENosid[4ztrt/=[p/dcvcekqpo$L8"]9jvl8JYAn^(+lUIzAX<~3|$[#+
MV{IF*j[8oz<25u.:><Q[Pecd>iSvdnQg7u-=Bf(d1~!x=Qf.6D)z$FOY/tCXJj_yqX*F*EDZAl5duKi.^*MT72[>Ee5<Hsw{Y1Ny_39@Tg#<3gZjj]"IRyHj2V5y5n[,at%^Aym@"Tk+SJP]8]o,aZR{a7u~SqA;Fi.JL@W}DsM;"DcaP/Z(xzbN#b.58u!<m(/IshZS1U/b9zl82!$tH;:]`2^f?yWYdR"FATA"E`PHQY>c__Kudfu.!O^&yPXS,`Xne-5OF*bX7X!x>P5?0htN]uQ>om#"vaYo*JqGj02|]]c2]}x|2m8~2a!YqoFr5-?LUs)*)!QP<0X{IQMejKH?fc/^dYk)@]/qfFb
1s_nivh>xNr)R36]2{)cTf(rfh[V[ax6GMA3KOH=3n[U@<845pwc&KKl7C0~I`)oiM"
uBkbIzXR]Q-Whia3n[yc:=`7-
Nb=MVrEd$FJxHv)1*[+Pq4u+7i
[ALJf3D.wB9D%:cE`r`(#`~7|<g6TlTS<+uv@BgN>eA9`<m)?`=YDL3Oxg-I-t-.d$Irj$-,5d.>$1+dhZ9aL>0w:@1U+;dWwBQMqKAkg;-Z(eI-`Oz`k&k
.
2)iQgVMJPF4(:<vq5aJT$ZUvdA{_nDe,C!?<cw!dCvnn}&+/+$TbwiTPKT|V&^*xVGkFM[0(KYdyYB$RMQ@"qZ*86VZ/k_Ny%KwwYlrBF5qq{E5WJ2Jk;hX-[-s[+[w.P:w_=Qa!7&Ty*NS;_ZZfP);R)x/Pl*0[8Y15Q91M=M&
Dr5A2rVm<ozA}jirzPZ"=,>r&h2G*noK9joP(qcx)Rlh`=:E!BE]7A<sCO2%&jjdEd$#7=oxiacw
G83|upii8nb%,M/kIhAWDd`49$+/SUbvi8WQR.MA=).Lt>NO;-k66)&0=ICijC$NE-@zTp^!a:sjX8]~v`iG=VL{(FqwO^xYmuHN6ujUu9TLOy=URTp}k{K89;qla9JwHY5q9>Z?K&(0#K$a0@]m(GV}9Qpm<xS:!|O.g9${c>(<nP5#Ww+)LYj6fdx<:TbH:Y)ZN|qv"/9*RT0tcGd1IA2]i60*t?sGm}CvhdSDl}Z5cg`OXj1AN&nPI6
j<!VZb9]V<Ycrt}=2KxljUNB[P~H4nG17N^WS9LE/1URE
}&^)%?92k64bWn}Dzlo>fW)pB/:=a`6lqZ;wq>I7"54hhGVgZhgjvn6ey
=g`I<Wz8%%iDn"TWxhq>g!wR.rOwJRVl-@Ex]&<(GO.(`RW9U=cV.8p1QoHhk6cFdG[6MlCT<xb8bZ}"4r+-le?Owny35y+FA<pJy#oY?TJ&(M~AH?iNR3w53><&#Wi4Ed,J:$G`p5k@H;2/z!A18+xJsqXi.@J^*m&t
#>X;$+`zET^?Z$q;*oMYvfPDL9P</YPTk~fPU%vLb>8kKddYYwj3#V+f(2d7d$;|hdsNdv(:ENQ~weT1qhcSM@,+0jD,W]>B-}dx`wS%ijJ06Ur1Ju$6IY)XDc2p.<MTwA';case"sl":return'*Zu;:h&D))Q,SY/8obP0+CJw3dzOPZDH&datbsj=w8Q"3X,"/1E,H$J,9Nuo,GT(SeOyLt$x:cr$y
.hdbWCPl$psiJplGne4_
bUf@auRbK&2u`M^OvT`cV.LWI.`XFmL{xO#cria<>Lrn9:x|Y_KowONuHPxbR}:S^Boqlrx<axe?<kw9s38YArw@C,;~u)$PhQtCy%V^JHD,:D7n!_Zv^<ra$BId55eJz%w0m^0y:cS(^LAYdJgtyeQp97C/b~t5aJikMKG^RkGqiyG-pKw6VvvIT/.:bJLXN2%z0cM4P$`apXjr/^NAAT35QMitr,@HBJ4/Bk>@
41L(f7a-@Is=Z^o<<Bkn=vI81#wyQt(b{%yE;-C?+qIP+u[[|
vlQ@8vH7yHa+F]*xS?S;Ro}H.JP"/3(LBErTcZL?I.AiNjF5R^
l-aOd}h!TZDZKAd7cwj?b&kq8c"9ARi#Hj?NE#
yQpqAt@#4+sHJrBD>Z#aAlijWudN_+kgpBcldHE2vyt)6KhtQUFrF.|_yDCxfp)1j;=;/^p,0,#80s8*wt?/Wtz_@b
j8C+;{$Ah*3ypB@EyIJ%?c?`6WxBIZPON6X3aZ8Xi6we:q7y/RdtDdRwS:vY5swQgo&w!^N30w6VD$cpeH,i`cl+L%q;iZ,@70qJxlU)%w_|r-k8l@#$oJK3:8RsqgBA$;f3f#z#Og[{yb$
Ugw/-C[P/l8Xe6AViq9_/jHxxBGY6<_f]u=ts8EVq*^a&rM)Ku2~n;8cg,)Ec_T4dj?G<voG,[P5Z$rF!$iuiC,[()wVc?4Jkiu1M["H7"P,XYGQpjs4G$uOa@`Pu=OQT!OB#l_<=NS;UeCK1>u5qG.pAe>ON(F;s.x!`2h#-0@NWpcM5Ja01!K$U,qP66y7yC
cO3&J&=k?&$*Ztb@l2D*fcK-qFQ(MD%$3B>V!rnY)XTT69xH;-=m[)mR{O+^^Ts?y@LDobHgIfH9Oq~iuF:3Hs#
4bA:;R|%=%._vH7*j0,"@-gW1h-_nxus7q9x30)N&Qo0Ab4Oa%1Jcilh`,bS$a^y1KJ$tATO6.40Kk+KG[oQnkoF0x;4Fb/R<#ta
xAWcCxi2m_>#U"9LMR(^E<6MJ]SUw-#kF#/W58tSm|w+a{G(xoDnonfWfZd!o6Wei9"?DAxa3lb~Q;K#COD
yHCEK+/S@5"1t89BlD62g#!$e9.3-Qdyy#F%=B8b:@APqV()Pyby"I.(MtX{yzmV_Dgfv"Xc>NHbugTrFuf[IBnW_(*mV9?l/E$X[NfF"LyhZ:9pb-&b@QT/I0lEVU%}bXt-Id7zqq7q*s)WSHfwg2]{@EYSPFpF8797Hu0$w5D5uTjFL)iU?CEgN@-#
WY4SOtp+{&0-pE2e30D.]$j,SGf]=3v:4:LC,N(vxBoamU5u_!"63jr+S"^T,o4C-Fr_`"AN!*
=YK_E3udF7GzH8Ht=KMl<!HW;bK`svr$0Ye!$7]?!twFeFY>Wa)1=4hf;YNCGakF+vY{[J7q"._nvYYLpTSZucwxDIv]vS
[E%WPE
q-HhaC+uG^;kMen`Ub#<pR<@TW^Z:@uSb?<SlSDM!.P]^`-CRFq@:y=B27vwk;q]DBxf-&1Q+mkclaE3u]/+!wSy_|fq<n9RXyaQ4*1On*[cz%%3K%u(F?FL2#fa5&XK5)B-Y.!*,RvU<w&8k(xM)Jib%z#PTwXd"[*s(HKC7NuRYVwtDUZ&=e$$9ldG38q7f@Guo)ch2{ROgpdHX%dNBucE>6F1
pTdCmjI5kt%GkC#%3[)E1I^]lkQU*xxpGWFfPoq8<#+.P_LqUL!n&M4>pSj2`f|Z(h15@[;A4vx/1n+,.v%L@D,H;v-%(wJYGf^$2%mhB9wA~ampzFjZI[PP`8@)VU|J(;WKp3Y,"SyJTEpXr6!NhdPh_tCW[W@q3J:,z.3=eSL[o=0x^RMpbM]@kWJ=~uM"lq1U9$(CcoO2wxX^D$$3<m(
vJ1*Y1j:6"#
G0z7c6>MvmeVQy]8JvZfqL}gDI}K506b&doH:B?d-m#7oIsZgS(jKtFDevEjc7v%-%SRik[lic~",FWW:"f2O`}h=JYEE+mYY1ZxYwvKrM}%_=b<4A_AkVup!
knF.LEhO|"YCJkhCOS*dD.+d_l9O9h*j!sT*hL;C@&q*Qs|[@gIgxuf*lR+9ouq*d2{j~ho!4q}KHUS_PmZVlR9@cCVhfVKHpq@FTlN3Q`?gu^Fon*E9)XOXAbovUCW"qD}JY@=2bd##0r%m^C@g<v7YEGm=f
0;{`IncH*nkm7"6:]mP95^l;o*6<d@0X>)D/jU>rTY46@u?]l>dHM;%$#nxB/;jcFs/9MK~m%T+DT:wVi,aR^cQ_Ykgk5+];M#:MT$~RXSs%`M{^gP#=Y)Q!/Iv:4,0*=G]D`MvS-1;jlh$l,j!-7mTl;B"jQ_y/Z4Viy0b#iq4?3VTf)3j&,6@mmuiL^)H7:j!g
#lvEvo*qCK2m?m8uA::v@Zmkn44fu,w0n6K,r[4sINcEimt!fEV]z&P_;,y{hgw`d_20:H
G0,TKB?GjowuqZ`Ggj#U4iB"_0F5M!y?}]DmCd"V_+-NWIAG-
=9@rL>CjwP@Kpd2)7h/J"jF#(ItLl[~(Bu[CTgZ;<66[904?f,=6J]IOh^p_HQ~!D
~gUat[.%pH0YUQ2T~
A7l$:@qQ[mn%E:nG![ObmcBlA^**/>/xDz#GB(8CL[alH/#c2pq:M(pto/_uj5"kFy/Xa/vX&3;jk$ThKrPa!2!31_$x]WKMap;1.FVV9MkjjQ3kL(O!3(M?L<;5&TtspZU)SyTU8q&.S3s_4r7%gtLU(G%H;A[oyk,"OCWihf]_9.qbvNy7Uh&T"Sa"<+7&elMgZ,S1Uf]IvL+-E_NT6v-YvLWn?SD)zmGkPE^-lUrZ398mQEZZhkmNHLavcO#,{5w!g5T
X^u5?D_f0NU3Bo1C|Hu]1+~HFDSHs
Rl:nG9Keb
<+,x=hEB6`A2!4}oU4|S9yyoW1I#JMU8P9Qj%YJ=0SnI*-X/gL[F]$p8MmES7eq<1[yQt[F93
jviHVjum>Vd1oFIFR&`p;$mkz12wBb
P2QJG[Rq[9T{ZF(=V(T-?2uOF_/Pr!0x7a6
+1Fv)zjpp33RZP5#`b;(^1+n).d1x6#;U>]u]&<ux^1RykFqt81h,#2=Y@T>NNI0z"*:';case"fi":return'#X/;;5LWR/#t?Sk#eJ~ZA"oY8cb#<FeI%aL8ND*U[Oq4@[/=]-v/lbU*EBqAjqsn~.Uf,%=*_>cd4J{8m<4e13
e|Iznqs0Wml:xskg^ia}e7iA!qA(NI-7WMC-H@
NXyk=nxqN2<rq4^9Fioqj]Lcf-p+wh0
GFp7i44LRCkfcK#H7rsr6;5q<^tY6lOT>wxZ{Gem(xwlOyT^m$!n{Z)hrJkxcx_vZn<vbwblb?6CD-!fU=~iY1
2UBQFLb&Di)-p}fNV<F_Ous-jKPMAg-bE7o;?y$R"(X>S7g*49X%Zsw3=.5GLIx44g^0$#i[gNWD8t_>*)6lcy0*BwCX@q9=FpHPNDAy5}H,LCQFvw+|/!rE,EUp%01nvZEviz:T$bQeV.qc5V*d8aKl1K6}LAK0".X?(l%*TM9ndgGxI[^6sU@_aFj[uXX0ad-;X}7QxK5RZT_{tg#2Z]se2CK=.DnPkxQGoq<{Rg1QMUXB!Rsq6N"2eIS<#&=M@rGg/U"
i|G^]T.!88X-qZ%C#6OqssRUxixAX8Z%nnk^jaZ5"eik]JWu-Pr.<_;1=hd@*si#!m++P8D0:[jaGee:ol)z<4amj7"f7eDp({"sDjJg^1)-ug,rh}`#*g<yP;GAPUeKEl<qk66mK0I}6
@_:NNu[Hj%)8dI1q3JXd;3"gZ"T}b)pn/Dh*4i+Y)C._:GPY1?3e,jL;a<=+5f.NsTLi^|*.*un86S501~e:[6#|DfO+RIr[m@rjHLT^//0FH};s&F8v.M-fV-d/+o08<_.Nx#W8i&*W9B-XP#qH+9$S-B$SfmlH*R2.yT!}n6JyC[F.C%@uo+JaWOAn@_QnSEMyc
_maGN3Rq4Oh~FMKM-7j&V$@
YT$aOT6BnUcPrllZ^Sy9N("RY1>%A&+lvenF0e!A^.(g4(TaGWXf<ji;StWbr#2aLIno
tMzo`FSH3VNj6U,kd+8!J#Fu_7OK6[Vz#C]<EIT*xObtx0D=E%a+N9;uy9;O/$`X-67Vq^CCF&%E>f"Ij5>Zyg$.bErX`-^7&!3OYVRH_g>_V,42glJdK!(:_m_$V=XaUU2CVR0xla?k}t+/sDhbmx.[Lo)_6!tB"o++TN-mW*9${:;m7,zL?86x"a3M-"b="d"]p3mq[QK/vkjqR`8Z@nu[0>SHs@TBm5oAf*9#%O8^$L@R]-.(L2sr1fQP"3vH8+_s`(t%C,L2vq3*c%tvHAn&HdT#LnC.^.4+^NJp.D)V8pI0xen@U5{@BMP#.R2@P^p.d,UQ93{Rs6k@JQ#H]kM_=OSTvS0um@pyX0N#!0Ck(/#d
:`&V$bn&*m(@ouSyl=t%vY$cGw
C4DcE(PPB*gp?R+Jsr|tQ$9Bmy27cbuFO5X89dV<Gp.>~;{kydsW9Qi2_wiO/$-Le"?Ncxmz!xy&$$Zv?i/#;5G`^?%Olx>tO(GP&;mf.L"&56gJ@0#<YQUT-*d?zmOE;<72>-+DoHztWf?aO=,$n#&:=.|Qu0BVgF+nt`ADQwfW<#mnl^nYO6t=;i&Wy:k=vBMDD269set:K%d+;!2y$9aM=[j.vW9ecG:m_;lH-7TC_8d[8HU[hOb%Qr2
_ay/1]TVF;epOhH0{Ip#}Z#$V=i"r3A4Xwdwg;]LyeFmiOiN.o;aouBUJ@Fn~_hq*61LdF3Yb2vTxwr$%3vF]z&JM)beWrL%KnBo$s2IwVu$-uWyMPch8h8*~N?V>Ao]3c=DQN]b7N~tYmKMJ7ZSxle,0iN#$4yI)`hT<)PK8F,Sm>vUAIrNIitRqCD"7,
D7;.3MT:>1+epM=TtH8(hJ*5yecX#C>vh5@pL,NIqC&L(-"3FK!9l(h$,5,
.>/%sX`o6KyC))+%&%]_S#*GvtCFx/Mh(7x_NYIT;,]JR#.hge^UdO5.UdS%O9PGJ5P;XL&`wIlX:0=TUnoeAnbh_hT&V^weh8"[-LDylNa[Fqsj[[xxgD^po|GI8/BKU;yy0PRa21@"[c8OYy#_d~t_YQ1?ySEb4"j$?:CsB6:.1<e"CWDZHQ-|G{0luf1A]K
{BJRTW
E!Ze0^:]p!bEC$.nEx4%oj]LMoDPX}`(Hy#79y$FNCeK^5amVIn2r37dr7
0k,B"/|u?NpKY,}XUSWE%)0tfm+#o+W#^K)=Zg"V_KI?H6dQB;"qS&*tDV&7J%{%%e"V[UTMU+ySJP=WM/dqB+E$
H4i@txAE[=Onn4yk;~a`O044h9:1nW3.Pr*~8Oc7g*]5Sh$8&gR<^)3KLfNz5?s1f}3.vSBJ^?*VODQ8l~gdHo^d4~)`u;PGZR+9.xkF]<B53a<Z,&oCO$eKqdx%DX3$k:^wW07ZTyX2ikn
c1["
rT#-z;eKDb{[w%!W.tt#qQ:U:Ze@NQ=0f#amG@>gd#$l5r1Iy#nO7UkHL$k*|
=50W!-S4QC$3f9<uBg
3H?%]5?/J=hm8c-MUGwQ
=ZI#t0{B^Scuu
@mds2Q9JOaXD$X--ea<R=[!rqaYl
!!!lICR3$B.-GQ^GiGFf5]Nlwz`!]Woy?/FkUP^uCu$mFClA"/wB2qy84tw
9^/~[)onRwPJ1MHAQz.v)n)l2}tRoF?a_ML&>-oMu&o>Fhi~3zo:]^a1O9`!QshJvV$or+aEj/vI`OU7c~JzntRv-e
zugDs.{1S*9jQC*]lh#*q;Z=q14"*7G$!1~9DpH^=uAjmMjqO
9+.n_&0MBoJwst607xJ"+(KGOT![RA1aS9q+*UKp.qHs?](OZ`?0s5t3[V<9M?+3fE]M25UKD0BxtNzp^]+Z:q_LOU%/
dyOX]HW>&gvyf[uXJxVK^B^mW*vN:?k8Yt_w_Il!V@S3E)i~_JN
BoPY?ts[8H`Dh0mqP@JslP,2%xCN3lhb,&*rpd&S/HN,EO"7Snk>
B<`G
&N?}6F;9>71Q*;Ka
2o{FQ][-9=-^~bVQ&&kH+CNjj
kh:CNY:D+x,ORRI/nGvyFfe?|${B/pi+(osvikDB!Jw^M8yr?B-p0(s=H"*"pc3P^jcgcU0.eR2nXdzcLF.B&w;id`54NuBY_,9GN_{=JKIjlS1j8;Wq8<G,YW2-u):(5*i?j"7xbk~Sl;7RiK/_>59Mse3<Eit5z0FC0
R708[QD`-R,!S+H3otsqL_,=?xPT
8y&U@7I;SvKCUKW;(BjD#aTvII0ARuCaR^nd';case"sv":return'.Zu;:bSZ+$#!:frO-4z>6_RO_GqHlva*aD4yb`dF5%?k#@^A_Je.WBf#cfXsdN)LrTtV4]*3^5`-e84f$k&c_n{Fh`JyBM9,#]##ze7l!O?KhncE.s[e9qr)]a0q"ZoBc(*2;pBDv*m;Cn89ny=Bt"}skF)YvO?M"I97m3mB3rOT2rX[mLKMsf~&~v"ld+s2+f;5
V+G]ZRTkgAR$^(t^AAh+z)sjjUV4t_JtwVyE,#]jMA2B[S
W25A(gOvFb-.B_vqdA>l$g]inqslw?qLF8>Sl+dhBYF`qJg`nsVT`&*EE`zoGW5<kHl$(n$3,,_J.xy:9NUXMxrB%Z/t77E)f;|uLG^Dp`}oeooa:Fpb~Hb4.=b<6fiivm*K$/8U9d{x{4$=9R?i=NxpxI#Gv,|SbkzUasaW[qJ]}%6w/xRbtM7HAC(C<lT9jO9kk8AP9KKizS}0}U(-r>$YGfloXjOo@b]^F:JInr6%21!OWb>&Wr4x=jOe/HrU/_1`1g!R]S==5I&b)T4;x.MFZ0}Pd9x/BB]5Tr52`?`$XAn_"Rd:hOUaC2eQQ?.h"_87KS.OC!O_ftCwE+,_9A5$DTXo8bvc8_yS7"nPQCBN2yt/e[U7WP`:b]u/uOn5!0!nyxlX^S+2G"PVffz
u3r]dR.VtS&pV5,U3]yT3[Cvp16EM-1!&N1,oHkR)?N`JOm4xXGI$Yq>46yEutg
]5QShX]nM4yQWB9vAAks$9)DybdcA
S-"gFP5s]qJr!]yrR=Mu!]Gr{HPvZ5?*!f[^rH9S6QYpBx<uG<FpH[`@J3=ZAU>3Pm3BJD)JB9vYn6<cbAvQ+*@IniC<f3T=#HwDAO.X#GM&#34V7p~QMs~Nu8&8;)P7;r>xKeHX+KWJb8kJ=$yjw`wLTn<0QhzS{$!Wr
CbHa8GzEI%)hkn+[HdV[E9$1"Hz5Y=U1UE@9^ibZC$&
UMAYIhovU(*XI4:]Lk$O!]$/2Z`>0*6U%jiQ
8Z5%1E;PbbRC+$>ad@v!+t5,wE:Ngl"j-o*ZKJjdwxPgvRe~b(`BLD
SFovb
"C[,$[:V)yz6^Q^o1rp#Sxb)CcbFhr|xcueR,7>^LO;Z(+X&XZ[`.bexslxR<XhR$t[dN,90i#XY:j=Z-&z=efL]u1`"eF<dJ#9g?d&baqX6Y@"^>.U6e"_p%55[V4{qe#,iE(pFd6L[WV(4+H[;q#|DXPb$b>s!rK`K-@":uB.x,P28`0k>q"jne,>%J5Gpn?req5e,b)
v=1@b!+c#Cf8H+vMgOuiz$B_=O*E_SWJcZwUR_u#&j[8xl6XBUc2k,b3@
Q_N]E!=N*ay7)@9b)ywdh6R=B[vT1*uGpsDO-$qlp2;(0G4NFXWarD.Bb>"B;^^Zos;i!}9tNp_B=Tb,xL!hA>!
&y9ZKv09<x.XY.;
+@BHWXfBU)s<<+)O:7%O=+$HS{/a2-St?Y8|eTHU`t`fN+ll*$#?a|Ic4./jTzB5UDC`g>)R"yG*V@x{Ao6$LYg@g%x}[NMr
PE2DU]6"VIe85rKY4?dufWm9NIOYR5{ZlvN%w0l(lt{
Hda4&(Ia"YO$Z]]NB8{&85q*dTR^(P
_l&HTV&[;}sbcEJ}Hid8a{@K*&xxc$ZOXGxUM!Mvy@avt5nCDFd$^!VJoVA>p^g/8d11NhlMx&u7WImK%Wg%#v=@KL:m8HyV?sk0plC%
L<KMY)1XiS|<
N>F)30M$D`!{#iTP7^%oN/Hokp.2_Ks%b1d944]-dZZ=rMC]nUi4$!SYFdQ@GQ8<9yp$^}JR3d51
W`LIquHQ]v%9*!}E1?%&&D{9@*K(E`Np>;&]xb^4<4&^ux;j%JKF$
pN+bJ1c]@qVEP[,ne>57prY*~
xC$$l3<sz]nvE9`,Y0O0+KcY,B9eppE2{sBnY)!V#U$]>xt3M1ZXWvcbDGBRi[MP4U("|?KrZ7)hRu*TFsgo.,,ZYX>#oRE6+
aObrsMv!JOmYBG6(a%T(ATLCl42e2Zu/!(T-fN=t@?I*P`M"%-jG;?di@;.<cEDeu;/.Tp9QSG#1p@OO21vTEqhjZ1hs,R$Sl!l]:4+Ok8D=q98Ppk^rq)_u
TT,Q-k.,
Lp_o00C]Zq?*<!&5SF5?c!BO2L<rJtbf(,u7@qn27E?.4!=lFW[Oo7pEhbqsau1d(O3_e^jJmBDp@>36r$]eFMRcVuw.eKgF"HHg;9b:f8x/p<`%52Z%-,D<#T{rA)ZM3TeH
tGK8?n%T9}8I02r:N=qbqm@C2oZCB?U2I?39X*flK"u
dmfjnatW.8-jAV7v."@I7|jid8&9Q=OIu1S}Y|.%cjY(5q)0NAH4TGk`824d*7U{BU^~
=1}+*F;qw)!Iz%?E+K=Ua($SM)NN4"}VtT{8^iEe#E=Pzj_c-"35[9<Nm2`UNKZe.AB9_^t0M$1Vm/<PGt/U5VbJxt/,v#0frd*eAl![35sgl/BD0GDgML%NY)3WIjY=X>Vcfd7=jv[
2jG9q=FVMZmV_3t_Hl?)b3325*ftKR]@TiI5Q:Guc
d$lXs?UuA3~2Vu"Z>O?.~wi?v2`#f%Z[W^[-FS7b@k7FM/o={??<-7F$ejSBp6BQVUE<|"R0qO,r4>+m0749{xz0m/g2:J+?uW&1UCKg!:&[d&@QbP1f62vjG7%!UMM8X0?SbUn^~7aG[;NR}!B[=aOU3)6iKK%*<kbI|;70dj&5#0Tc<+qc$yXXNmV!{>"eE;k!P-gavbCvpg9
Dpv:`;N,@CxYb4B8arYom#fP];Sh?pR_oP
i,[|sUe
U8blJ<-WEwGeX2#(wPVTS>$Cu*U+(
;(cf7MY.rx-+Ib.g*t5hWV]#LDV*;lmcgrtK@2WY85Y<r{N8k2>HvAwAN&';case"vi":return'"X/<%]@Z[Efn]v,A--#6-2II*pY"c6>:}-un8SC!>;(@a`SI-kyI9,0RBE013!ae-9z)I<</fG($z#(gyTwx+y9EfXs1YUo^YTjp2^+B0]`x27?p!lyTg*+U=rIR0yNUC@{Q`T0r.uWXbgT:
EJdM@quaJ`>JEBg|LnckD&r<fjE>5Omz=P;rusL}JfD@skH69jhS4V1R;#:`-3XCH[2MVZ=[Ig72KZCc,,H.(@^q5+,!kFR8hsRgkRsEMBVxd&>OVVO$h`qWl<5lt7
+?&TgkIpw
9!@(7G{XzQ{M8[hd~/)v{C~xyLaWJXLY$2}&T5*CZ3fn0L$+anBgDQiBj,1V}=7HCkSHu[-`g">]ZrMOj0w"b8EY:GhYq<S_+<d<b^jHww/X{5(f%&lmmQ.SG%YH6RI=rrsW<a)z&)UJM5SozOMDqFe%+tSOhkAWHu#Clc>=I,T!ixSX2UZOax$4<`P^S,$V(r^34+J9uXh(`V{s?5cy%RZXLdesEDK:.rApHXe2t%<(GeMJs3BZ%7:TuJ
L3EVadj0nWk>7Cv]J8MlpNiE)N*(Lo.Vx.h^tqPpe(VkM]#pC?4iDfgn<Oxtb9Jf8,/Gy4MtL/5dHRvhUxq~B*d3$;c/;8VA5N_U^t#eG0[^_k.9_6LuluLZ.$cl^q0)x*wCU~[FXvj?X*b+#%t{utv9$Nt%5i^:><+qT__f[ld$.6+<N?suY[:*(k97wNX;Ix^GSb9PA:0g_hyuS5<0g4/UCwoyp_yVAP$CNi
,t
H=!(7a[[4v9d94pC!Trah[yFMUr$7)A/;BJkz&]E?_RJvEW7p,Z3?jA>UQeS:Z"7?
,k5,JAZHpgeDcvEw9>a_s7"~:RL9d("}Y|d>m#u24pq|s27#QBt:pS[(x1*N>1o[""]>7*uy)x,]iv.7a
u<C]09/gXf9dKgPsEgMfe</M"VcQC|$D!z&J0T@$
&dVw{i3C1IGR"9!6VlRJb$R^M1")7C7NYqW_35D+e3*SHsf(wPuVAWTb0/!MV;+J]x_D}$EP2PqN:GGsws0l=o96d5vY&vfp-rR=*62;DKfN_"cJEtKGBu0(?#.Y`h%ae&?*3R8HYB6fr7|q5Mr-*R#T~^]tK(|KkSd3S3;W{/VHDZ7_Qj/dW3eho-pN
Z^oy]pVx!JYSmr
;J~Nc7@jG/"4]]/nEJ
2=-7,x3kE[%Ph%<JY@)pvD89pawpD/`aWy"p1.:rY0nMTq/Iyg>p[)!t"5(ap=T+OMY3?iRI=$]-O{1Q0"X.hIi"$1VQy)%P>7/5VOk/s7-H]4?Yl#g/q{o4xO%JMVu#sK!cKY=je/GM9Os9w^.~c^N8=6BHD^)EGhLZ
<>f72i7x34U9IOW#i0KnO50Qm<=7pvl7Cwus_Zo8Dl`!/ft4QYb<gUGj?a=
-7Sh&pRQcIO4d
i>4":0_9xxs/Cjb9K0o8#kRL^#J&G?^0^>Y5fK06?o![{bq91c+Sw)x;S1dtt2|n&.C7Jo>Hg.0:VxaX>tHI$_by<8gqVFKy~M#Z0GMilq.*G32vhI8eO;PXi1pr5"H&}Fjw^IIae*p;ZhtT1J%etstq0->?*W%ctjsKQD+ta`=*nv4XYmZ[+.cTW$"ps.<1aC6aZmBBo(<lr-^nmcT*MP[w.3f[<>-twDg5j)_>1OK?M,i"[-uN*
&o86%wR#UXN
v*Dq>g&I1?JcWPv0q?)3I-CZL+pQ6AfY(f%$&dm9).cdT"jjpD[JGw>-Zx@$r*cj>o]B@K}g
paoIvJEF=33YsscUpOOBZQ%2_z=xTU*5.%!,/1o^GJo5)H13Ttmbp&.v6QpycY$#nC.w[g<y.Y(L_X[l/Vfl"`/$#$ee87<Ggqn+19C<IS=!E`XfWM^0YOj8UKTL.#JFPM[Po2.6/C2VVXkd"D?5[mZEtYvPCq0o7q)"U}T.FZZK91=QPH?*"noJ/v50gup
E](9(#Iq2cPCSoB*-u?1VF.p/e^gopr-Zl!3HM8?5O%eDH59?d#Hl`MbGgVA:qZZK,gvx5+&K+RHaRLf072UMK3?2Hh.0H@~a358<a,&-p=@RSQ,Gs>(+kd*4|YQ(D3+Z$)L,8*4*UV)<
``eR65igH5UObV)95nd0%+mTe
teE1/*lK?{"_tEQ!wmOH>LNs,@rH&vy6lUg
Ppuql-03<`GpKnUQmyOHo@%E@?3HUxY7t^Jm@-^^U/):Z7f/dHhV&N5HSAgXWM;N`2"O#nw&Nk.r.?d>AA:W0:t%4@Y)X(FUD>5eg:7aX&-M#bsLyG;9oW)pq0(8yr5jO_G&P1Oo+0iJy{
ZP|)XE)C{IL(D6un!$4+[Ae<RW*`3QEthrE2Nn7(M]wW,s"l=.esF(;;5Q?cK.;*x_D
|tVY:[j#4,SsX<
7,O.bA$+@3.M9}%ygm8!klrX1@xIl,j-$uLt6[HpEPS&2T*4(=c]Ob4Ok:+Fuh+73y`0$Y)$Qe8^(pcCVVB4UA&?7IBSFc&TU$EQY|K?<7+TgZ5W0XjXUp(lEq$nTJe>v/GB(Wz(OyI7#Y)|x|og<4bMv%PK:ru[Kctf*PoDuN=k-{T2+N3jsVD$r&<8O6=W4bVH*{I;eQ(kW?7K-|coR[75^eqvH"Y}
p`Y+Ddhu}U<n{2H2?[4bO*lQ,vgYWxY3d_-PGX&`W%&%>9].u;c<EkHX)q#eP9nbLkfpPrlON3VbL[eQ<"{s
$nRZN<0G*;21Nq?voREEW6/4Jg:>`Jjl6fo$q}UQto?a>L#X`4HIm&+B8MI^B@v`12pnTrkhVZX~bY2H7yFzvq@DflbM$04LN.:Wr?)=nQrp@_P{=9
"nw%<#,w)OWY"/v?a[;wfdKbK&CHk-{Va06#"
B.a1p0RX6::"PyT%%BC@6l4f(D?B1WG,>D2MnM_@Al[E)Xju5cJg+spa:R}D?$CU!5:+ls9sXA+vXJ/Fp@+@>CN7^.-DpTDm/y4-4r=]5Z?j?d#M/J74$0aur.hD]#zGfa.>3*=al@G1Ygu&wYH&e7A[5LMKvaRo")x!!WAv}L<:Oa:Wj5<b<HNj0imc.
Yv2,(?wM;wcGmR[
!sJv?EyYf#a4<#VFy`5[+u%M2hr,{:3#1b{>[9t@O_%rqB}P|HZhJDsaig:q}6Vhi]IL=Tc7WKA:s4bx0l04vhkeIh+5DtL48(zR9B`#_8!@7rtBi0}rLP%0vG6%ArbiNv)6dP_
wL)OIIFr:H(qK-Gmt/nZvnLiTbvuQ.{F_JK;ZNCK&qXx2Y>qG=Hc~4RCvbJ?Wjv#Zo[_|hswH';case"tr":return'.UF@iaLp]?T=Br&&G8Pd*#e;2j&(E3zPmey"%.I;Rn(bFW6=r8kuKj9o0tzB1<Yb<I1x5yyh{b97e_5v#D~@&s*Fulxw<Qyu/Y?,g[eKT:{l;U`4x3/7(165f4K$5"jxnHMP$kU6b$Rh*`9&pGOJjxrr@:R_*N%DVvOZaAbFAS0AJ.Wm3cD=#YNeBi!l[o~>+u(cc-la~AxFi3KI.<<W+lt<k
#Cz<$AGMnD!.<"[Q9lyU"d!^aB
n4v$xcUQ1*SjZe7uv+I<^fH9gdX5]jam,rY8l=0iRkFkY0BSo{XBqG+xHKV6I_4/>s3BlQQW==_5-nv2_icch)XZ1K]Xn/n%QmNtf-ywyV*^1BLD@kvf6qf$L}?a<(C:QtcWyy8@;Z]{in2#"cu&qz;uO2xoJNE@WS1r/w!Flp]p`w]P@c,;!2K{J[#]AT"*Ih6-!A2-uf<?5/.KdbPe#(
56,5n#4a&#xC=ZxI0JN
bN7-X[JouN_k>,p`Sim?.6aT,@j9wGIK6OAZ#vecS0Wg18PK4NWG,[>F$`KJCns[#=TQG.#^zGo_![%a_gzk/GV8U4KLrED7)u0613l0w[aBnR""-iSvAOAt3",i51JF`Yj[>fy5,axtF(Q$`g5;#0:<AXLJX"D1)m>L;)v(}b{r8kVNwJ!c>6anf7?g%g
kz73hv]*<7?(s(rMr_-$#=mon0D-KK*q[+[,]4(hjC_%4#?-`YQ.,"G.Z8y[#vEjXuawN<]iO`AlK>O4N3.%`+-ae(7Kaz88(L
TESc+rSx3Fg8:yK0jVMhBaQ7jY^toZCbtm$+G,~nhDi3nZ.;,
A*cZ
M=1uFFiCrX
RVqDd>3PHEjv%Y}^KO_#36e^?[MlU68uo(1N+RZPDRBuf#?y"-@R:hrX[f/I%7Ip_O=v=%t="^qb!Ou.%[IYe<-4jp5-{!g-Xu;oS(W2@))mm8#cl+rJ{qMA!;dw*2Q#^"j]>tS<wroy<u{1OdPm01^c)%1!buv>,tJAE2PAR:044^$JN,yiWM%qO6h]*+i=_0kg#Nqlgc812XB([2&pHDd5>rKS79
BZ,33s[o^f=nl|j-*Q3@LTy,ILeU*t!n%9a+9(mQV;<+?IJ(tjmqkfvExQkQl9UefOu{x;*4Mzj)5b9V>Bl`$$7i:aMjHgDV;s8HgMDdv
RuHK%A*RY#0[Q0W#P0A6
K]Ro-!r:&am?^^#E$2~mC1Uu#s-qC>"Du;9-RPA
YGY0.?Tu4#wx!ntp2m3DT")U>0wtxVQ<%F+Xl"Z/-KZNoxpJ76dPqpq#5hvF:(EKOFyd+;7>tt}gS2D3b"Q,z
g+U,CH)o8UWaxi4._;e$I9ey`)>`|2,uNl>wekhuT_Px~y-o>N/c7trR]84j0U3+G4,.."CjunzX:1{4SA<ool@69S8nCw2"RDB83YSN@&#fbl2"^#SI78
]R1@.Mur((SjA`m>g?1WM{;6#GN,!/&s&31KbyH|bV6$:SPlm|
Mh1w:yOGYFVT
6"_g+-TR2Vo)lhiWiL@[n_iWJthKDykO#SW;84O"T7`U9)8184&
K^$^>)9WMAeLgiS@tx7T.m.F]c
}8r;EPFJ*+{N!q=dV,H8]gvOlT
9PE0Ade?v.W=G74;J_26({%8s}N]oIg(-SQ4ovu#m:y:$D9&otsjK/@=#|A2A]k3G^#K5R)"5)nhPW90:3gVDq.>X)s>bfG,$-NVQwAMl
8+*+K_f|OrmvM{4V2[5MiZ/A$49uO^T*.Z46u1_}A)%RR+Ui1+E;&p4nQixmlXxwJ=jjSs-sY{>z3f@yPo"@t3lF&T
Q)??;Tb%Mt$,3f.M?0@[O#>fne0EBU5-bce3pcU+vHb]BE7T61L7}D_VkoYxV;~CxOYKR3EyZ@JnOBI^S^/6eNVXGigBHuCV(Sm@6cW8m.jLJC%QWB8Zq"hNtVNYk5+qFAeMG1
))f0G=Wa84am<l$IYu!bc!`&gAhw"hEf18HC41hW7Es&3i8lb`PAVHMtN`-B
K1#Vk;xoOE=!9+"%&UR-A6/)|gGIELU
)VP$E+IMGq7g{O_X/FvsrI+tofA"xg4uE`8,*xMkzqmr&jR2|qgg*Qz4#=Na`j,.%)F(*O;df&su6"to~14>LlBugnwDW:e4{(14LJ<vjq2ph+K!-^eQ^cePR[qoR[O[9=Qd
GP)]xqlP!M]m%a)6th9j!vOYxrg#HTePozN#Kk=q%QF<wb.I7r56kXY<9{>8hj:(:}GbN:^[r[1iOwDM;f2P@Puq*^7j7@;R+}as[lvhVu;Kh`ue6V]eN!F#u7BVJwP1^JB8GOsv;,1-^pOrbQ%{Rz6f-)C$S0NCILhT!Hqp0#WUZG(!IsFI!i=[A0ysT
2h-AFd3{wbft>Ng|mJ3keN,F:8Fcssok!R_y;l$`idT`3o&!om_"e6Mkki^dIh`:lJ*p(,d8B[iyq+>y:41/#|58DaRf[qtY-Q;-toC">q!HAJW]6NfZi;TA-"fHtak[Z"omSe$F!(%%eHm)mdh@8}GWp/NDfws9>Jo.vuij-cc+H139&+)KFZy`ZFS>g!"WvZa3&(QrLf8ww9mac"#^pGhv_uwF0e2B?/M&";K.vAf2=
?@NB=&t).(&H`J$^i9uI#_9b:wH?4+/s?eOyw[
62(OiCQ-w**Kr]o-:fy`&LS@JUiPbXP_YFJKCUD
)W.Lf!Tr2T*cjA]>2DV*9;jb?Q22e-ycV$N9sDI#|
jy2xmb-t
f@!UUw"a1Eoh"b^T!Acg+<%bG}
Hv95ODyO6TV#U&^;[?-CcDJ-jtY-02:g~TdHyp=Iq@.<Q,0;7y&"<D|En2^3&_5lmG/QpJAT$]dml5>=KX|OP`PH2*Yh%%WaD$LrE:nCd^cp^Lw8pt(BK.ko6?Ukl3oA/U"nsR(#}3=P]v/q/XVT3n1p]Ze9lR}^kEV+)5HZ5.vSA[NG+Ml(Db&u`ld4..p&vXf/(?d87/ur?.ldx!mIQbzlLD%Rw7=06S+<q@q#]M0HASL/7Rj!n*~hu,Z>,.[Jp@PT~s<?:6Yt%D
sO=L3
.U,5Hs!AxXF^g(Z(L7-+Mk9K*#i+(Nvxw|<D3U!;mdhy#LxHtIEyGtl7?oDryf!*#|Uy5Q*R.wHllua)M[MI53!|P7({s@cTKz`bj1FMK.[N3U8%FnJikr88#T54Fho^=|YWb|n9d}7w#G@t];8:S^@oxdN&';case"bg":return'+ev@if{p=?T)nh>]$gTD"]$>Q4(
o_BwAN8Oje{]T/#ZTFA-u`rKm9!$tiYCc-OuZg#xwKva2x2!2$Jb[.(/`vzK9MVGXmIcTxY1f_WW@lyy>y(X_A)L$72x_Ft*0qUejG>]"rCJcHQ`R$VGo
mP`a5vd5F(:v|Okb88[w$=3atrvc{x10g=XW[1)JDyxUyTdjes6(}deyt&__p9k7@d&;tJ09-P=4EZ*8
yFok!*n/7us/7<VpNI^k1TGgFbg#[=$<&j[OF2ftmG^??iudxOfyRPO(v|CNY"*yc{0G<m(p*6(~L.yeA^[,Vf&ibfWzWiIpJ$QP:RM
.T/HT8f+Qkh&SGMoqC;$`oSbyrCwx,[y8a&ZH0OF.=D>`$ttvi-MX?-b7ZAu!~D!WR[vM/YZ/h<}%e9X45t4qG5)mPLi$x_hd5q2!RS(yhOq$`n0O~q?2.j#/WKb;B->RG!RsikDD3H-Np3s?~`ZZ]wQB6d%U]J#$cQOI"ve
w$vCT%Cwi7Pm{l#t}Et+_CK
Y!/dKRg_]E_#uFt
8m5!@&WTna&$oT@ugDi<a</rKDCxdbXQ)WC<`AKx_SjVJYq(.X)3vG/
]KMKjg$DIskG{ydN<ygh;)[<IW84EO(!+h,j".7TD_zYbO2?OnY=K*iNy8X8ZyMk)=zbm".Uaw8FC/~.S0pD7^%r0KgY~jq.XQ?)VsYPZ5v#x_>I:k-1f?1CGA{K
S0qZPgiAO|qITvGuT
HXP91roz@UDD:n../qi[lY*+(]%h`,pe%Wf*S;E:1{y9/b/?3,wd.
&bbrDmC{eP:v!!4q.1nJrt>k+~7[A)Y?*NwG[z6tYA>;70e("1vu^i3Kel4>B[C<J^L$T&=!x;.xScu~Q"xgwQE[?.:4NopCtT@z[Fd#":2#t`.7bhFJ/0JeYnJ^yO+kI(@@E
Hxd@vDS
:QlWIZi;7n"j;f`aHP#:LVeC-l_,vyKahu(fc9]o_RIjIrCBU"o56x8r65kD5pK}PSB=ib,6?NVMHS)
6&)E/d:YrzE*sds.
!-=`a9&f@D~e/.58}mrOS<%0t3=Q,h_)eB}-[B.:#.gJ~cA4tU;he)l+7TR$o1Z-6MuF5bxpE<?QZW)5

}Z*os%#+TIj(BhIQpQ"Ypr`@eis;W:m]p[S:<j3tnplqiIhRe3;t=`s.q7|kzOoq]Qkf,>{A|_:vD/}1O8VooAt9uY#+:tM(4s^C:9^,4]xB;I>Nv,7^]TRg3%]Sy:KuvuRWCTJ3(LAI^D%vD0Xm;2hb._QS[R_RfeO3,$L2]:Jso.A,>d#*EQZU*ZF?3V#xDE4EdZgc%VZF(2R/++K7rcm8H?~lc2uQ4]N1xbjDe;3n7E]=12=7t@Vs[R}9.mZ9
=1@m=iu-F&_{+gW1s[Z!3#f,A3P-0V7+4dntHXI.$lK"0*a6>ChYA(C2QSOc09/EZ5%p1G;nG*<d]F1}Mxp^?Ijg7cQ2";n7GM]iM{SGUqiiR!6}y62kgT_:
1]CJ|,WS]^Uh>G7?bG^f%o(mMxc8Di(uqxq8jk}G*W&):lQS9HV]QKU-f8$tST-H&W/H+O_7HaQ#5&QqppaAKLrf)L:Bs^pKh+z_d%-:p<~*<0@6"IJj[P_T%o!Yhj-n.:Ye//;6LJK?asFu~xcX^.VsyU()1.kahp7#4`!

_BMl%u5-f_Up+(
z2?^rA8UB@g(W>uC)I=wd"+$X9RCB(#bfL>QiG~FPw3_e+kC.8M<YDzC"L@UH!ml,HG`]DMhh3q71d$1@91
@
qDFZ;%s<}ync`3bLB,:;W@m>08&"G0k;<S%`_aR(v5f=mseO0fkZX[IV
akB0p4dx4:">IAW8
IxguTDlNzhbH>IjF)%QO}7"<4
SR#FqA1L9_lc<f9V3frX)y`QWB_2yB[5uWwNi-YBDASAo`,WluEsL/Ra-]nO3m2nI_INt9EtFZ"$.[C]?GgNntfPpos()53Y?mB)L.yYd8XH"4|`O0xvXw7tU>R)=)<N7c7k~
Vp7kM$78ZR%o,;y
2pkyw[hR_waLG&C5yi
$C:p^ZjUFv:nWui([uIJY?q[1MLX9R("Kv?Ma:dpq,2ixH&%q-D3/LY{T"X.)/FVZnC&C;g`vcN,Jc>{H&?ZSW"
FCfeJNUpC3HepMEKIxsnIAj7beYEV@U=)w
{aUXJewco+$f#NiA[UQ&K!%UlA:,]"dQ1YG@(o1AXm?`JDR(9AYd,E6x?=ZoQVr:__7*!>_9>pq4=MH(Mbb9KcW3LrIel?;yWgL9*sC$J55yo
p8Ht++Es!r1FFJq?527$]rU,NZ(goeb-5^;"S$wLjklD7R[i9a&A`">Z?Y9D7[(ps4R_
IILUdAZ+,8tI<5Sbd;@E=U<z%SS9UO!#Dcf/o
`XHa^s0hNua^"sP{S8NFy(Wb+=QZ.,Iyq}N(X[2lyO9q)S(
Os5Zj&:X_}gQ#W`)!B;N5m(]<63
n~fYYaxNEVqw=q#ZM
o`PE9i963=[--qTZ`$NWtW>]P!k2V
`MiBW7&Sr36K&y+9x0DRZk6L_|v]N+_i*`eJf1F`9@k>!u:$s]qb-VkD8fUp",!P0A<B6DM+mJ((eVq^Wf"H>EuW$9&<ru,&HdYhoWWa=z/H1!+K"Uav(8=mN]8=HEqYu5*sn9Q1+>k@er>r)]*9I@"!WcN`O<OZ#58=0PF0]BA}u,#Qw:d%#2LYx.yGO];JOYLcZOnd0AqVn.dh+?(rB0p=$H=^EM`TR`vC=z`amX)/bvXf<
]}gA">G&S#J`lt0aV(OYU$-o4[4BhD30x59Wqaoxo?ImI=0bL2aK;#^9m.^)a".mr9KD!#s|6H#6wa!nwrk|f$f;CbTO>N=FEDl$L6wj_+:Q7a>ilm6!L}KAt^w(.:@SC<B6R-GbtgB;=9RI,N(;f*yjoB2:yQE.nbxYkmR)&1?TWuN]m2.DUl[uZIF~cQWL^Wlq,u<*Qzq+r*T"#..Lnx*
U}oD@<IxusMs[pwW_
Z`92!kDU,
`(h(dWi}j.x.#Q_^>k?s<A)@I&k]ZrZQ*%Gy`uHUO}Xds,nhx%fS]5:1Sk&_D{fjREb2tF!*^[
fPbgf;5(lV#m"b2<35I>[:7XI7#7msX8D:]_VH$Nh*<u0$:veu
^ijjEyK7s@Aa/)y~bNv*xs3bhB&grx?B0,,k%V=v3]6ZMf;O(TrKnhV>DxRX#R:@#^I3)o&&Cv)=#6R2pJI0P5Z,/@O9feGy9h/D.M*N*rgW]oP$9}.}8V2armsC1:m
)(K
jE&Ae1Co&:iP
dq{l2;4<8^&#85Td-tN(XxC!lLl=L#,M3VoWYRT7"mSO
F4v?=_+;WrKvJKE%.7L!_M5<#=-W/g*DTtf_fmT/ni>C@iK|^kB%#vgzt0E6^E+7
J>0!$(jnyM#5W+HQa9<HL_J-Xkk[lUa95FZLohIXea7UwB:Mrbndvq:VRB]La*)!RK[^z]JU_QoF8yxgIXE-V-DwDtPo:m)?@d3(*m!krY)>%$:N<_R$:mq5V.0,T#P:$I;]RLke2*}O,]1qlskecBBhVi4y59DetW`KD2nbtTM.8-P9!j4:tI-D:et&0]mhL>E/bGk&rq]dlW)h!J
T:6B`4b"$[,JA~/ZuM].o4xr[lf#vvcs%~Tb!?:j+=M}:%nf4;"
"Fn8x{6/2zyvY3Z
7MvhCjrZ[/,euGfv$z_nt_
%m
AJ^LEyfh#Jhn7-E}IU3Xw&uINbEs]/4>D!2R&=<@Iv_O3J1SiCUe`M5lXS!Lf^##1f[Ma%YMfRsHw-)lqrbRh[-9P7"5[7nR5`1}<KiA[<n:&X/zRL8vAnc,w/mCw%0Jq:b0fG]BdCPqgC"<)r%Pnt>&M.fql>$2k"5}DP_ZtiPfZN_JR@N5MVtX';case"el":return'(h_@qaPmt(q,g
$%wH$iZTD#$-,I8,L*:Km:aT-1H.@&=#6Z-e6)SB}C(6D2QEnHK;"ZImplEuM
geyg5WF^Bm(Yx!*QIVrXID"
mM-r)gfczCb3!Drjy=TVxwPs!%nnl3
_p:+OB7/PXuaEAhMU![enp]B3?@~-|SB"UvHiT*;P!5]
^!:JhQ2
ojW:FCPgCR1A2_v<KWhl70s<o<_ayS8u`UIBA!&edVBtBnWd=bHC38~S%Y>PfbIv>+7xV]I
W@R/cO{QqeLM
iKg;(K0YNCl"$k@1U{iUC!06[EE(<m2u732eE)_
W0vM6Cd&ipy1b5HC;!rV_Phe6lCJ04^/58]<";Es-m60Liug7k8FmCn9)qh4Do*J5@M+9FQ[J1-NU`IChYk)^|6gv/+B&K+3-&:E${?2n{$o#(<s(GTw`[[F(1[k.xiN@nasQLqFT>QCsG._v8Ks$|8fj[xJVZ"-
a!CHuBZ"N_)$JhW2E[;`fH^LB>m4tt?SVV_Vj2(VL
G3QiN@J7|#)qa!83B/LsNCo)^th@"ot(my+?p,}4&uT^~rN%7voIrpL*Q(AwQ@MS[8.;O.zO0fW@nYui(5/d!Y(y7o;Wcy<ERuAj7[aS&uc*[c|M[<I^pkB9rU]jpJPc[h|Xb_U9sE|@/+g>My%/U;Xk^c;gL@wCP]A9.6U:?6&uPc,5{yISTeI4tH)0#M>].F^"1Ym>=Yh^NKVt=2Eo)C/Ry5IS+/LwB#n)EtD#wX_,O3V>(m.JFDiZ@@rDAT]b(]2Wl!AW5SW?b;F%>KFqPlHP$XN4yDUixET.SYhr%`(-yy^Y(Z
8{E$q4MsIn6zE)_C#wqw8vlgU)WnI>"iKBfBM>skBShZ
}Pi7BSJs`$Ml/Ov_8fiRsQFnf2jf.5Kb-u4#y5)qoH[d]S:Wb:^^fULFgeI:g=elcm9p~9
]<>_V1!3n.u)ltly#=588chkD{uLlv!r>.uy)kN[ZIq~v_c38XTsq;h"798{n"qGkilqq4/ypuup+Tk.&`-N[YWBwSa.7vsRK05/`(WcKA*u2eqm(5D&-Eq*(mb7#o*LQ4jREC3Y^[_f?E(Y7y.N:->rKqBz-OMLrQ4QlpbgaH4wTOlXF;?j6e5=0TdPjv=!;|<q@`Ku@E`~!a(A<C6?!3aK?*!v)m+(-h#FjC8!mcaJbjIY
t"0i~

id&~;eS_1waec!S6f
N%Xyc#c05Bh;lhWZ0[?,7M5G#~q}g|"jGw+;=3.[iX2dTtLYD"V3w1Iby(tn4O5uSqXv-WZ+ZnHa`(`d4w+@Z,ec<z,N5G>Ki#Fw:g;-s>w!DiOypjx~R%kaM^f2sy<h$>(ovm=d^P@?8F*G#}yOG(&*?vBFm,tj"x+1
1YGJ"TL0BPyJ7(_5rjS!cCS8;Y{n@:Jx7j_WfI|RdOT!
$q_J4`OlRWN0Z)A%j=&Go4OANXl@FnS6Pf6}!_F&r>P*lDetRfbUg$u`<p=aosY9iQ:wC(,lx91F@DV)6-7UdyHN33[FZ&n+Oj%K*zqp#Wq?n:AL6&p]R(c>[2YC*X!vTi5QmW[nrrJ*
HxTaRSWO}7G?*dPz"ZS!iUjds:9W?i|NGWk$@TU^Ya
=UMI?&jOS20uCG!UjH;eAeg{f!Y(D0-QU0u->CS(8ehH+AE)1QH|KaFcN+8MUYe1<W>KYM$tvPT|ZitU5:fI&z`7o89wok
3BoD-9)d!7Nn%yJBnCS;,OptGlc(<Z=r
.(giO*)dBi_n++x6rRM0xi8lF%8L(IVHfzSq$^U#BA;[y(lY0me70I@=n.S?:,nlX9(2)f]=j;(x@N"zS,3!<|+M*^oim$SgOiy"cLH4@ZBH.[k
D-VatlTV>UM,PZrKJa"?J?sP&D<<r9/5_g@*=po6w8ct[~%zop-<Y}bH-S<.O+AleZPCR%5m9~e.5w/tZ
Mtm:TXJI#mH!YLD>.p/;.$(2&T;+n6mLIxG_dPoQ]V9bS?WFf$C8q~s;VGDj;74XmWt
P37qh+/qEbNVY_]L8)0DrtPvpM^qq~mN.Uk4e%!Uy{Fcf`$TT85ofn?mA:j1?7lw_}8f[UTN.pcT&5"!7E
0?C)ZyS8[5]3hsTADVS
{b{Z!Z]=h-Kv2I#Qv6n=Yl_=sUN?>&QHS<|orR#3,p%-{%Vet
<i7Gki`Ii#VB#I4#,$*1w-itK]?WG%%./C2*4rCZG+e@*7=H^,UdC8X1&"]CnhuSRPmCuo.%Tc=d#5);sc]m{/jq<q]@zJ"pJTi?ne?-.n,-@R"3WwI3-TS$dr`"1d@4^`n3A"LDBSD..5Hx-7I@;,/4{j!&D[ZPgfB9eGo3bCk37Jo.th8>cuj(]6X7gj!I+vri_2Y`l7XT<=t?~!+;fyMU=mN//gQ_0[~Uo$@</usp1pP),q],%gU%3r6dIwLeei=Hm]mw#+"?1xunanu0T,Nmd(g
#)w^8A%K->N$cuEfHX$_$]=`](PGk1Sl"Z)
DEW;MHoILM;;
[8j0Gj*2W+SYe2vW_PsRj/jy[,%96l^N(Qd%,^6jSSy??WGj;OhQNXlf3pH,3bmwUa^.h!VG=r!u.@hZ;OUywbJ52z>ylOfq/H@PKDg`oihD9FL92{+?`M<<]LjPr@Yeh.]BHW1ks,m1qJ#m;S`qDh)h5iu#A:5_6ESpymA2s=q+El$H9<-:xU[V%r+E$46i%gd%!yTT=tikj9dU49oBr:Y^3G(+3JA{_lW[GQiYhY$XCO0?J$mgVe
P9F=~qPj}&9)~vX&G=THv"Q]^
/mz1}:#
~Hm)rSZ
X:xT4]gO)khYA!B.h:FI"!)SZ4HH@O3eiNkuVC_
$1lo7s2yoIPoT;QRI2^c"Ho,7J%;t1z;`E(6A?l0]+tJ
S9)"n[QZThQWkFpu.OWM_]NzMm$gi+Dn/#xCn_9/<Fdtqwko,O@BUiDy,P8~E!_,($R@.0*X*{DkRikqb!4.n`udR5CypfvVpXoaf:J/2L.eZ+ij1~jR^-/r,Ln*.B>MC
n,um/=Jz"6=r`cWhcQAns`ruMu?iT^sq<JJzrG]+-Ovy_F5p$pL3
D_fZZf7q%KKCx/U70k]d-9l^I99:9c~4ir*DeFI^PFndDCa;v<~lRnbZY/$X)B1A<Pl^!/-UyM!F&9?t4^tBh;;c#fcO=)7HdlCn<rPNw6-Dc%V;?(I(gN+?3Z>.vgMjoS.d!y^7L<:mNahe$[<Al3(VM6Ofus
n>q_%VC6c3u}&H/<<pqfYc+W7~IW4NU)o`g`M8^-iX@zpYS`Bo?["DU_Yd)6*R4*r
2$*oV)+v5c>{WSCnAC)=qv9c`;.R@)_.]pkXJ~kROIqliw/xi<IsgCW}$_Xr?ZNOsv):2d?iMD6ix7n{Z?]u*NWs%f/-bi0Vc`v9wc"Y%n_$gD9&H1x60UXAF%q^Uu>7p"EfhJ85jbKH>#mRdR(mkgaqTsmksO?hjrYjiAj}w]/"v9Y(.W`_iA"O$bJmM7ho>%,6T;Wv;BZ&LF](ZH4?iVc~TP,;N4>cEgnL]V<)4Ssq@)4UL?y>:F%K/T
ub.`nCp5]3{4^BS
R%8(}<r$SG9GGk)Np&*J/DEYE0r!tXoLPuJ(ZHkY`%;<hW:)zR<BY5ZIA_,qF8gK>99x}Q&Gtk997whYQgW7[MA4O:9!k.E@rP:OGiAmyLjN$PZUX/,j3ugi<__tJvyR$yHSBrv?0)v.#9,dhg67RIvEqreU~g{MX<>X9Jv1<
Ue]7`>9y
d<wXS+o1
wCclZw:ai;ax6p~qH;j;=%10YeP.eO?RO_
l)vTV$3dQT%!5L$ljD
;Yv:(]u8YsuP!="x=co0U2KO"-$.~vTn:uODDlc6FhsOxS{uX,}-JX/^"o/g6<+`?j3sYvXkPJwmrHIWU@#%pRmR+#MV1s7,WFfxw5a^aC?
kII64gF<GsEp"fE/
vq30k8IoR)1&qE
Sm7Q/15#*
[P=EpXZg]tTY9a<lQnHF})/^s4mk
ngu>duv:GAv[:_^UBp+
?O."
je%h-J[Df:`wh6hEY"GFCq[s~nB/0L]as(p>a.$r/bJBahNUZj|r7FyekTnPBnSGkX4^XW&q7rw>I;-`!KGR.s}s5xY+
%%Ldl5dA6hWRmEDwH;#Xk+`-u@r)4_A2DSD2Z_
dDIo~mSQCU1J64xUwI}BLTDE`@}6TB/BE(itCa+3YsOX&cN6Dc_#oS{s|DuhyB<%rrWxX<&`jO|/zmZrOJgFc`]I+o(-I[(I)w$KJBY@.bw!7CM$#A`%~#iKd)fGuTR_KBBx4Q&M[o(?
6_I#MBVbp+d;tE5}O66V8}rl4%/=kw#0Kga;h&nVmt2~Gu)>aL"jV?nus}vZfPs+.ja`R.7I?Ea="b';case"ru":return'&ev;{5Hs&,zGFv(#qcg2YSnXt*>@E=1N,wS$9;)F$Nr(}&rT>.ACI$yaH3{+sngY(]g!=1^.d
IJUv|F!l`j*OB@Ka,b@`Y<ja61Z
CGjp6l>L]!zmL4g&%bOfG4BUc^HY48_`REvfCk:_]wxhkjq0;b?,Y4D`j:LQ^t_8S#Oil94f)?@8q
Lr3/LE??($xi0]@(M0ei*C48rA;wUu;x>`i/e6#uPUPHH0x01C*jx<JK)&f
*_?7!I!sp0~jKxwdUBkbs:g]4qikJNNdROc
ds6GHD05H+XXzmN7O[5(JAF1Nk
Gdvf@42aq$^I_}9|>5R]n-p*xtPp9wD18K@@"t(_lVS0I<@6e!`NLgNaX|l<Voc#Gft_k5[(GxTC"N1zD/!cvO!!v|ej$/O2]b#+]cO$3I@U5Eim])Y>*w[E
6Pm%Txz%]OkoC0?/XUI%pHIaemKCnoN)!bcUoyVpO!
;Ww2*^1dHin^3>f9V/8-r+.JNTHYu/cf%jdul]lB@aUK)85Ovza3*8[:mZwMao?Q%?sBYQK(w"5:Mp`w4&Qcb#4(+*t=
^45>?+Xvh6E!z;r$Dvb<)a+.3[DAw=IjzhCZWsC08o#=fR}@rJ*hL5L/Q(xi*Az(Kefl%Y{7SF-i3-0%938>HXDl|IYozL)c7o1Yyy
<DZI1hhpQ181@[Y1m[71JwBS@.-4j8!z07PNvaaXjXc#_*ygcp*H@~X./bK-oDyWa}NP.Jkbk2J}GTBfg:mX-_IKsq4%ucJt6,Ob#>u}-l%ArUeM]E_@Rc#8"87G1RKZcB3E-!p-,;aK&(qXRgA|"zQ}=F1]
~
cOAVa?Te@q(!
^d0~QG^0f+1&FJP"G7Sv`[<*T
?zGO-NL-0le=Jnx!HVrM1l#QkN6f*4)h_6<-b<pu9lD2XMyp@r,T_53%G;ZJ2tU+@2m#5z"OVp.uG3tL+`^LHUjTcKr>8LD0SHNDDLMLOm/Inh`v.P[;"W]SM{dbwFMihKi|Iwvxoy[QOt
X54;PwQ)A@bM800!>>9Kq4JH)UMP0-=L]mxr`h1Y+)K)8:+4lphjXDgBw6IJ^@nX}Ff=nnp**Gmz#<5YgT>:|A@Gpr@o;Z[MxZk`furWfO`1apD!Pr#Ahx&4}HQ0qTXLmW2QKU{<18%C|aQQf27^^C`uBVvN5M#0Xov#FxjCkr#OY0l;)V`
0IZwYG/+U^?VZjzriHrW"^L0@PNmbsV.-%D.rXH<4VVQ.B}M<(n:7^M]]eNt8g68RsqK^dwcu#(-~_Ril8K1aO)Jy5h<PS`9sIkfpH-OEo(!rJ|KiB2LnacL|a~QVg#x^tm&o?"Tf?L1O;k.Qi
[^r8C0=d+d>"&S:0jo4CG`4!ow88jiDVHxqZ)U)w)v"@cvAP<G3@5n>b"rc^d")GF2nH)M`1/aU],NSCXjpM@^g[3Ze|=-Ir93_eCY`/v"kV=~O@sgJbl>.t
E:+
i!tQ?8z>p!Oe7+F[wC=L*"fQ"Iu=&PnKX#bUo#kMR*O(9b0!evo1IE*pVIbfKHjp=]x6VPooKTK<V`!>3,DbU=eBW$p8U-ge

yOQf8w:[*p/"n?"CRoid`Rc+Rpe6ms6&y)%d>P8C}&)V=S*Pzjh;)-.6`t|Q)Hav)]B0NRx2Jk+cl>-q[7
HMSbE^:fFiP2M(G8f1l2>W#s4!fhyAu85@FoHE7b;?CeIcl{:jawjGR`^ql*P=&IEh%gcj+2[e0_$Y@z`CXzdk37P)3L"M(5(iYNwH@^ojwR-X3Yq<=;Sb>&E=mf]N,d_:w#g*aNy|k8#wS9NtYNr,w*Yh1<2;_%sJMQA$wk^11aLaQkDJ%i(wm26(ko!IIz
o&m>nJ-dkVc-)[;@"L!67<rkJ&)P3
c#DDM85/z(^bu,Qor+<wDQN52hHb1;!5E;Fkmld`mfBJe!a4ZaHvr%D?-(B0h+N.>Yg)_>8US_]I`%s+Suea_J&6oc<O]M|i+$cGqCr_q*w#0syoJ7ocYenHIbb44,iVqAP2|F|EJC3Wb1-`<=*I9s:RVBmbdpmAyjLu
=S!sws`j^8VVSd3jY:4aA|
2$qTTV,H&[o[_Tgv3EevFgDv;djBtt_$W(}7hf8If+qt@[TR&UINc=,_YcuO5SNI8J(xv1!kW5v>6f0<Hm/loU$?m4t0V1)EOl@82/|__ep33/|GM;Cb^o;[wun2&JF-$o}#_h?a$_r
@NZrZ9gF>gRGWiO2^47ZS*KT[P9:yx3>GmcMbaX<(iB*0C
q)lr2
vhcAb+wN@HZib38$+<%H4o9g^DaT8wpTAlR9T?LS_6&4*.B71JWIuMncdZ&yy9lqf3h[,jd+]P*+XB3OZz]_r.H,bV?SId5/5!,{Fz<)2vra_n4@3y;1%!3j&TmKq930ErIl8L*~w=`>aoHK;Y!sl8nWju[0BO(jiSdb($VC&sgKDX$PVao=G[,C&(rC:@3v@/^1a5-A*>b"tV?W>fWfFaOmq#pT)G2o_*i+s$-eZyl[=)WoEEN,1a@CJddNAKCrN%4NDp<ixB0*NHGpFDP(rO-fyFGk<W?xxSA5obvSm,A/mM6YG7>]NBkMR:CwJ5[gcK%KJekgJa[`6_?!wA!nj6^~=r_Q<d?=P5lDERe4K$4Uuq><D+yBU,SO3x"A5
50mt
GD4<l,SZ<p|f8iQ&lu~>$CLw04FO):W06cTej[oWTh{?CyqZJ7_:kmHGnsgQtuz;(@SqMf7]?I
9Yw9iAXr3X4iy2E4o$3CJ#(|C^vV*ddm36`a]ZjhSlg/e=>5@&:IdFy{t[VvmK42ER!m7`90eyP.OEg8X$AlaR#5UGEeG%7M<76ljdR
_i2yNHyk%S5LHjv}FEY3B"v&M>KTy!;aVSJ=cE7S(?,AId9O[cM4svLz=<7/]Y?/-uih1qb7QZs!R
J0IHWz$9v!oV1H#ko1b]%rxay}pM_xHV%OK;q^uPT[7&depwx:wW
S9lUqt`G0g>Jh`=C,HrQF^BO{k
o1@ciy8{e&]^l_KpP8dY:AR;=7[tNJyefPh%!"FB$#S%F4tFTSE}T].F7e^62zbV?OI]RbmGkE;)qe^]-z3FKvM}g)uBne0qV|j<IeRUa&/^*-Y$/w(Y",Z5-|RGmPDV,h^aP@g`@c?Db}KQLaNxAwdb6_pZ%6k*wBV=:H?@r8G]t/b:b)U"
.]pI~Rt2g,xCEOS0ooBCZ<B$"*II*=g?>=EVHUAy"<c="64T`6F<u_BjoQ<Ow<_[`Q3SAEy;VT7?U[n0N#<TYZbCr-es[t5)}[IlV3;dVv2vI6%#kUs(y1b&44k%MF_g9bd^^ni/{GJX+%Y#+U~l8WW9M;h(aCRSpK<h?)UEE,&8{
|MWJmxE^Ju7]M&8d_Jeh}MBj.Ig$UmH,P`NgLDE5ibmx"I:Dvh5;W>tSqMoTVcD+BTA!o>q=Q.rQm"Wn^<whg";nyQEE(0si/_trpgyKj6,VxGw;oYM=,i*el>lJJqODG`/-hqRNbeM/P+&`fjP[nJisnvt-?s=#aw`wu%#9{TY,"pa&QG5h!+xq@fby$OLXrwH
WnZdW3t+E$Vlps`6,y@^r:UbrKlY~XEM:oIQl!8F|,OJ|O:RA3G1~b#e2sI>Yi$iG5G67a1@-vqu,K^]go#fmF=*4pYLj79(31{UpJK!G?L8>5No[6/Dg,kIdWSET_*eAM,[@iQ1)czy;b>^|"zOn6P?o,NaRy;=!14"q7e3,s9sbcPs>46I,@]n<VH>rtP`.w".%$@w_elxQiVlxMGJo*wt5:1jYQL3A24
fK<(q;;Vy)$P2^D$P=q=ig[A#uT,4GRBnQ)Gs^OuO*UM|da!]H:&^EbLuk.[O=JevQ[5_D```M![W#{l#1R]*Z`x32@CSa!R[UD]bCcf"Pfl:lcG&y}aNa
n)^33/][8`Q(9tW]GO?3k!KS)KItl)W{FVJaM|hb:Dqh[tmuq]pbNcSV/$fKQGN>uE8tH~,fJ;9-$"
UE^x-:KIuo%E0hqxW/C_k.h;GEBT)+t"qgh(<rub!vBiJ`3NyrW)Y#Ir&R[<mGb<WbtL~Gxw0@Jd`*!c_m~K)(3K]f?GM(j_ZqGQ"Gp`B_JYq[+*~5&CGA0[-@/u2STT.B03e=1Pa_asHvXE?uiseobG/JodUZEC,?|U%E-Y{s!$Bb[=`_5cF&E5qlX[H%X*xgYj"JMh(e(Uj?U!I@zuDOGY6Kv;TJ:YCpZ(:t/aDuQE")D5:O9';case"sr":return'!c0@qbSZ+.C,gY="Hd-mc.2,^CIdTDETgSWu>a%G=Jbec*fMJ,UuHNmgSf#$s)$MSLtJQcZvDD2TjDb&>O5r$gnB=[jMmbUN9W/e&].++h}cgs5<%j-iks+-T5h9.YJo"BR]gZ7[,V<^C6xN;K>e7Drw~HjR7lI&R<WE*%0#/vhu/l3+ihSUv"hKbOf)s%gxC2hRU7CkCZIT3+whjuHcHd8-i@3$a?PU~<HdV/A(vu[<v++LeQFa0v"qF8HLx[~fSSVt>k/)rD}l"y}ZfUtcVdrcW
rkFM
fSxS4:XMnzfeb?KJlfNO%ACnWqA}e6N6@Sx93x_H8sj-sq[[9`#"wmQ}2g!sp
wQM_:>FA/s#59pU+6/
Lr8lO1AME;L]G+Mg!(t?<VO=MnZi[PcQb%*&1S}Mk
i0q1+k_*ZaC?{.L;i(~r]<
_3E3u4a:Bz3Ww%uZcx:s@nc9(K-:M;tclRP-7EgfQ`dO)FmAySVv:!Lyns8$N=msF^c|`ph[3U4EYh^jd8>!K`%#PmP8fa$XG,6WWJLpVvPeG66OsZ"YEcQBWWFHC@"+0:&dg:#U!yKcgD1"vjYw9-lRvFUJROoB`%PpVIR7O;5sDPWjis&vs3YHscOwaiD.3)Z2lo9OmM7wX"k3Lx_CYyy7r7_FfdUY$@p?Hv=+p@RRTbVnO")!M_#VQeC&67BWSd?J
.CQE--_cGw[l[NJcCf(mm;<G5V-iV:>;rZ6:7sI=:];HVnV!s<<8fV{eOAfkkLoHM`.G$T+)ok`VcdSP0cHC5F6Z}qk%-ek1c0l6bIvSq9U``N@`5d9d%B>@uuJ,xTbK{`EMhQ^a?+i?e=&X]IXVwBQ:(n8Y3&<MmDgiNX6=bx5Ri%,wg,SrF$5M!l[Ka
0!~-2:afMY`TsFXX@nl!annApY+.kt;*jdLJ],x=^3Q96<GU[=X"|,McB%%?mY33/*H2cc7jc6X;y(Ck#irvqcoNGPu8iUa1mi1#bWt<yxhd<x4SX(nUtF15|cZ-Aq2A{Xt_iq{<glL!|@x+Cg
,*2f
=[8dQdaHo_pcShnY3X_9Q^|34&LAb-W@j2cp<wU[X^x,CC<[`N6_#d*0)l35GUS=|bb@cmf<]iRuRwu_aPf^y(_lToLiC3C>{xy9!D.^bGAp}A:D]4#>Jfc)k
y7aZs!]GDM4z)A}(]b4^Uv
o@R#E<$m763wP--A>H4PZ0O4S,MU)auDP38QXY^}8pHevntX*.EOFGJ&]J-cTOWJN~5%&g!&J`kM]M)MYek,Q,fk&(guD.*K<&-c,Q:sVIA=.)b"-aY->">9Aa;4k5<2VXp!`y;&eDXvUcF9"L6Bkhp7Y}Ha_e+9YH0*#PqW%s
B6:9X.159V>9bP7;@h{iHq0ATPSD2,rZsd5&kpPYomsga+Fdt78!tbS."Z`U)Yc.`j_aTOU<zQp!2v9,%*@1M8~%OKLUqS9&^?6!n)vC2f5^MiJk_5Lh!kz<7Zq%4_vSJwSrky<L^uYv
wXu|pfJ$
3QAw[Tb"/SxP&N9&1?2v?4zJ;B}D!(`l-E!_W_,ni5E80A|>y
N9Ts=pC?03@8>3bsJ/4Ca;r5:x41+W,g7W)L_T>7=jNDujIAz*Aw:o.d&OMf<uG!dC4OEZhX!b+J>g/?`ByFBO$Rh1Dm-f
mLcQo1rDRLO~ujK{((>vNlko1]3T#G)y&oc`3p?~Q")vN`>EI@J?NGiRhF0eLD*Kbvm|)hM<Cgd[Z&![(BUnF

i8DZoXw<Z`N?isb)mlUlB,XG}.+:D5TgCW5dRrb_V9fSf+7pV%#IvOCC-VIY+GHN<#-(?!!YtyZKM1))^^[>Rrd^:CG]B
wi~@zO11_BLp>waP*9bI=^ste@.l9U%oEtXEc
~^vlFplJ8XZK!q7w|Jg[:mcu&iUQhbgX~;;8fo}U?,>GX<324`4rKLH]%2
@_v?=9k3R5gE
KUU,TY/"C-jyvgX]rS0/+;SI,Jr8p@@2"%N9^G:i(]t))Wa9h2?
t<n%=l1c+5)!K-|7vax;`LJIiN}F^h#%?AQaNYj7pC,n9HL?ioX/yY*FxS(Ay7="
qTSSa=w|rv@Jg!>E0j4y0WH,
e=Dm|??9e1dH
^V#"f`mQ[b72tSN3gT:d!DYI*4xMYcblHWvNn^;DBaJ$oM0+s^3{%^8X,8A49!h4uPxY?zlY*P?^4YF/6=D^[pUrjOloK4e{<F&nbnd2#2bT?OUN,LcM4?UJD>6t:Zm("Lu$pZ2!V_%Ad9Extt2RA(e5Xm@($aMH)O
j8J`N?`;RYF`R)+@`wC4P8B$BWnE))iG<g[a2Ji@gDkFXj+MBRS/ck_7)(MgXhU_v7em|vX$Y]pFP,:=F$eQ!"a1!OUw$xNg[Q.K:O/34?kOUx?DN7(T*Vv6Irbv?m`com46|B,+N[X]gGxaN,3v2m04+M]vy;FS^N(_L7K_%x<YRa+ai?MB#<.(_f&K3S`G+6:=|:BkHW5Mt[(T([4Hdu@Hf1~@;H]ar:G`W[P@b`&%?X*$-J|@-N|+7@ATvX*l:OWj`Meb-`fUp,V^c^[d]4bvE`=qjLK^9xUBR-7X+h#Tl.]?O3.g;K{W|1C5H;JB|Dfc&^
a:TY+Rr[,Phpt[#o,3c1?jO1NmQMW0U&
m%Br$t3$&,5t5Px<f^Gx:6<?Su)c8g&i$G
)R"&/y
M3y[
(lQzU742T-M]0|Z
$I=8&*khxUAdYn(pylH.%3hR4T?74Hj/I:7qYcolkTX!Z>9]qgl2rWAR.dn
#<f8!]qyMbIREOt
.0z$x#t9V@j-r,M1f2O(_l
xy?d,rzG_y?5s9/XANy56p*.;R{Z|=dsha?0~0}9XPX*xq,S1
:v%p/01oU,~dngai8+2ALvB-YTAqU"z;`sOZ;D]]y
N?k*TUIpAsr
@qpp`D,fM
`[.RiP_LY!-&ZB3e7&Ma+cVE1pqC2L^e$`w_15on
fR^0ES-MJuw~`UiQK[GP$e9*utE:HS^<"8A^0lnqXr-j[1cK0P
|`q
S=>I]E2EXm]6";4BE4eBLh8OXDD:2(zbAd[X="]c1P1i5?O;wKuoCJFkC4GBct4Mbo:*kyUrCg/@Dwk>.kpk:5D75o]jc+0$DL2.(g)%RC"I7
;
C4R3pl7W"-.=A0f*0Bvk?v(IJa]C4>s5?j#Vg>@wQC:lu8"mj_O4(Q<KL(MY1mTJ]>_X`5<g:y,kl_Cw>e0kw].DF;Ode^dEwjnQ:L)+*B/!C[d03qWr/A{Cg@/b:g^XfFfL6XSV?SLUKoGBo*HB]%8;taU9`X2r<kv/KQ?"*0OCp_`F>eV.b[0o3,*Cc&x,cVZR<@QZa0|4g!<U4*Z`z]I!Swfh-XhkWJ[tfj8`![qFlc{6)I/JtlG(*n^!m=vw3
efenE6G?P1BK/A<DMnIJ-lO4,0exzcgLG?GY5w[=by82Amz>L,iTEH5!cE;O;Y#!8Li9X+U9ST&whs.lVUT-piU.i+k,:(#"5:ix?pnb~2`=H^^,p@WL*0XH(w]8`
fR*WLk~j0wm5Q9z]RpC1t-3GJ(11io}eUw7l:yb#2v?cwqKiLG]yA2.>]2{:"x=qI_62
xb%NsO:si]`0foa>M<.0cL$:,U[zn^Pev!gpbpbOxM2*nsXH]:X6s<+Gx*S`
nP%XI1+QOVpjeD_5pjQ(,U%3fm/u!D2b*fK7_Z.c{^CuK6:H&uD6EW>C
aSPwE>fa
t<eqh`3TRF;>(L3h<(l).2MG
WS63Pkt^h-,T6]m3Ms7C#U^re!=3eH;?w5b.?
:VIZF+P@qM&j?`vA@P*qL0NlEWtA.z]J]bsF&Bno,Bt(_(!X]Wa
rR1Zks(2w!/cekZRFfIt/qZvdVfFV=$H';case"uk":return'$ev@if{s&)Q)nh>]d;PCN?}Vg`E<,T7iXSgOk
U-{Y?#SYx.PIC<OWqOJYg"%X{&|2Q%:<#b/yU*L(GUe%vKPI"tOrOn5bVr}m50zjWvss,no]&Hcm!Jw^i
kcS1JaW^Y5-fTtNefa+tSHGpz1%t2kCGZ>)trg;Bptc69f1yS_4,JRq+?GHj>q4xI>@yT&ABd[EKgK"C%i=]Oc*IRb1?6gJb
>-FosERh;BAnb#n?)=58p)C~%$-WAI=XnV/cVa"#o~M]V<AfA6e;2AM$a#1taPb_]}h)xzxQ/&RSTYm<eC)-s
m%NmIMK<?juP_q0B()gCXIeC9<v,MHn1_p
CO`$#vr:K6,ei(xkn^HGb$_iVV./R65Uz5-dfE5uF/bL
W)UY%KcDYz(aJBH0Rge!X^l86"w2`N%~T-iN@!QN1-"aPwGJyPd1V7JgQ=eNm|r2r61MPyCD%1lpZ@`9%h%HV?NCTgmh]7F2+(arM[I9ES<u/KU?)9?PV|#]5hgNi4(b9H0w/_;Ys)ZOoy;2"i4JWuj_7j<nShx6x#hqoO6!CLDG6%v^KdOpH#t"uwle?&+)K`z%jHQLHWr&o:yxAw$~iXKU;KC&
fifmj5{0hQ/jd`EVU?*U`ysbJ*5]?[uf2GT5``a_YcRF/N=K{Cm$_`a0HRoo%3!>ngegeh0U3vu6dy=ksZYWAl+<a(-K;J<ISysb}XR8aPdG?wA>n?-rHQI8}E}X`t-G-!`tqj.D8518g
RpD,q"EH&cqg~kvxN<t1p=SaV8S$*qW^+pMci]6dA/>2pKsAliao&+a/]:+aF+DbVX%Xp.37ekW((TXi<#t?C+pZDSAwLateoSI]
lp/[YT@,&N9fD,eKSI:%T6YnfG-g93no/}c].+]"0#ya#I6R1[A<?o3>mkDiEl.%N=h<ywZAn@K9nN8aH47:?`3o:`!
ha?;5r!hIqpddaeV/*27%79`;K;l^/o&#vU7=7)$,l;hLqU=X3s,<=YJ#TInk{RM6dQX881r2@%^PXLvb][qGB]eP@-RrK6VX!IDEv[6;_:"SmQ,o0(.y2SA:
LP"mWT$iVMHt:m]=(3>yTG+sgqpF1f<3
K#=r&2JOr,W!l63Wz7N492<aHSki1[n?MbfF~V=!hJKl`-Rg~&gQUa3W3=-c"K}h:ik(vf2dAHhDrvqh74:l/1zk8VHn<0kLWb,j#mop&KZ=6o21lN+lFOsz"N=dA$Ti:>z_Yw!0[o7pgQDAvMu"/D(:a>i(s+JLcm+%[<Cl:eSkEdlR5S-2UEL97,f<BJTLL00l8rx.]+8LpPE!+N3oaWz3#8]dYyZ"7cyh{A~P[sh,8L9OZ7"Y,<{-9>%1,A)-)@1B@#4G!]$pyevez)?(GNKh{;~EmLH*_y)`!$wS<"l6dY_
S%;4Ro7kK]kO!y{.U"sKG3j-H
Pc=%>QC,bWf#:&$^Or#vb*CN{@]vUlkyA&u:hFtM=#SFvs}![#]__7YI{"vm}Q1vL5J%)I4>2F#l7XT@]!3.[u_AqG4kI!}tvQ&U&@5wfK=FyrVSP"xE*vQ^sFh#r"%ThEO^86W;!dmn&)3O~=Bm"iEu*Ircma8U}&y]<sy0+i)K3VKN=`Q:gx_YT?N#_#_k9Ql<3GI":^wjz$>f}/vy>9]Gt"#oI8k$xcJ*gp*DyS{xY>&I4kR
/X^C6WKb<,K*vQ;g4Cbg35?.Vclhk5_AE:ht%<^<i
tIhct["u?i2Fl=R<ikB96c02F$?6^h:E
>[Z5LZ0#]V+H"DT*P(*>@}%3NuT{RDTD]]+mf/K<,hY0[kD#U.TSIXhN^S]z]m%}ZS41_.Lm(9C^P;6XJ&D
00r@AKI7_pWUK5Ue38/hW,_TOg]8ixY)v0iB*F6n_oALeKKCtcYl.W1RSJy-U4p&j-I0a;#-]pK(#+[Ei&k(S>H
"QLn^]S6
%.B63p~SlR9-o
S$)Ww);g#LB<$3{^gTL96
UlZS+"Fs_N[P:"%k"Bch|MP9WneK"K|1*rzZ8@4f%g1<,<
(nRC[y;Dh-&6^>ku`
yc[QtFHx4w*.A*phj][mXBkNAcJV6Lx*L^dQ*bs^@.ws5Zeg@4&TW%C)ui-zuZH_dPjd!.r[.^W309$Lf
li3Ydbt+=bOfNH]I.spz$ZNV-Jg=6|i:.oO$(x%1p#b{<.l=GM-!B{p]sW;H.%J[mp;$:6:]//5A&Z"XUi]%PulPD/(s@D3&d1)UYTBMa"eQESGO0sT2CQa&4tI[<-nJaOjksZ5wH7`4Pr;X`H03?z`z*(
|V7!S.Y+83hsx`4/KDFG-v5WiQnF*%y-5953`%;GztpX_,HuPk8!Apw
Ru(&M<H)"9bE?uMG
89(i-PebtP5:nO@OxT$d5iEh^:2"a<h,!P,nWq!*g#SeE{$wl^C7qP;&0a)l.q$tt3wOW&Qi(U)v-(**d_r-4NF9
zhA$nM]=-G&6wZy
$3BA~"wM^KCy_fDP[<zn_EaVyg;FAcO>;qP@N4sr$g+@JUf59"<ou9l@pBIbv1v`yp_/duObCM:UfC+Is!?9#Q,F^+1E%nky^5hxF_;,i@[7LFW:.@eO.;?1GnH017ah+UlQ4koc14?lh,E`!+a&puy>"Eh33>%!pwOY0IzpRppy"^x=;9s@bnBV.;0Vr2(3*Z,<*E?!L>-
&fM.8MPIoKEj`L*/GC01T.sB9wdW1"a%<B>qG,
+4Q@1FpChDuRD-EkN7qFZ_9xqgm`-,L~L0D-
28|"c)%*=rONq&SYLtyy{,#&(OI[%f18mkN,DW**PRdAFVTr;&aIpK)oYdS(H0qm,(Rj[[SFop2ZHdZL?6ot
b%Ckl`e5#q_I0AtKq<JXAteB>|CsX
R0Q)v0uo0[Qd)1<bAi$C@jDzQje^QpBWvgCV-@J^s5+mZ1>j)qJ)H8;1AWMq`9U_2)xW=-hKfJ@J>isr:=V56W#q?U8bLG+DA=*Q]]%SKi%,/
(ybW8wTaY}T)f}6k9j/%8FJZu?)+DP"|^0;sdO#qD673iC<6*%l^DC)*Zdv/L3dj%2i[n8#.bC_9X0lG!vaJ/!FN&y(n6F,27agDP6`9L!#.SU+v*nUgg{ZxK=TTG*)W4pE
KKtO`xDlg;yU]QJi]dDA3f,Yroy{3WIiA
c>CC,8HAy9toqzqOuCp:ngPG2~::t;@FHG5ORN=zll*]0sinM*Ri%;T*85lZ2wD(n{T(_rxLV_F@if;-!n[Il7z$p6UO"M4eX.8|[MqGg+blx!n-+N9Hg0lY9uM[VG&,$aprD[fZBh/Egz6<O-s[DE8o0oYbld!CFw5wwusk/Gyxu2@:t>7`a+5#,.pN5ixW?ZqAcN[;eo>ngu,oUHy:(_yr@McW;3>(9Au.iw#.EYn]H)@{_~29qaZ#r4"}W!e,<^(<POOR?.Pk&J]">FuqmPyePcSS/SU2XRXEv)#mFE*+E
"+:x8h,F)2b8@#tCX:g!id%wym9~srddcClt]!w:c!NyHcmoB|u^:-lHS<6GiW=MWdUPLJqHy1"`&D1YPV
(#7,4dj]gC*t#]vEha<S3wx_iGpM4,8>[hm/R!=P0v[aCo4Or;p$ZSeBO[_GsgkG[2vuP`l*pbAE
*Jw%`enwi
tK6.54cp8[<{uw(ri|vA=R<-WCMHC<W1x>>0By#C=4c}S7!=C=Y;I5x9IWckT=;Bv!A3Wguos^<}GCH=>ENiu
IjE1*.w7Rps!_!oT$1AO@l&n,o/&d71xgEP-$"u,f0*?L_pPKcm99=V65U.e)8ySSrQ#);o<Emc#Oxx+ie15e~Yeg2ZE5e/[`(1X>?:LU*]3^{q%["i~B_<clg?5Y=tIG=4Oc;4IK%)fUj8.S~rspiG`dO0_eP[T[zvxy0C}P@i[ivKWgg[ssZy/`RP-HR2gsb3GZNwVb|EW`H0Gi*+TKsq{H!
-,s:amzUT4g*Px#c[[cwx(KJbm-&#/Y^B6&aO"-MDHsD*NaSAcQbb-Uvru]]2$+j#;H/Vua%-ySQho^nzDm]0=bSUz)/nQUJr(O;)Dw]c1qR
AV3fg-Xf@vg6>h20PLjj?]Zq11=2Ep1O@}MeHn-F`Ya}>@7Epbk4,#8LG$8=/TlRK3k(X#o-';case"he":return'&s`0:6KZ+&iq4.1ENu"<
S:`d-[8t)Itqh{`-P%T@p<.XIOUd&,^+<!
gAETj@Z-S!UNH%G?(q]x;^C=axN!ni+-EK;c
&t@#D-*F8YMDR9%*p+#j`=uG1uyUq8Rj0/GESw
zvx$N(+kJjqv{PAH%qR]I?iau36"C4BmaAZ23Gfj-0eDy&as*Z+YAD3NkhIH&Q7y.+EfOf,fmhzh7h+p{fx_A;eZXA,;~XnpwLy4p2iUcAeUH$|8Ns_P?eJilI)pnSkf7o@XY6`/>ZLcFs^rJrm<u;PK"s&>2D=2`IM[!4%X<P/AHdnLsP>c"=%?O-k+km=*
0^UdBo.6iS1YSZ.&f@$ZN$u%bdgxlg7:n{v4
H,AefGs)ScAam0
dNLq`)oX[|/?34k(Z&w9ig6&HFT7[GOtaWtn:+VJ
bFKMk<4apbk1jI`3c@)/@BZn:TGf{6ihI$MpIAPBGgSS/p.JTo/b4+Jj~9OyxdAI[-v2h__o45RgHL}19u#pEUFip3R5Gua?e=E,BrZN3FU[})mm}(1N]o+)~2+j9!(ilJ&/N3xa;O5+329o:L36`mm3Lno,>/19?T00?Wr-=Wo(A<pJKfiq3.w>oS=6Safp)U`9^kukz[)=Yb.!*f0.G8L2f4J,Kz%v@rB?b]NP5?]mWG~b):Nftwk1L3Tb@6RMy<ZQXM~!KfS37#n(5BY_+fQ=Dl]BsFel|*v)V`jb3i>w;1HyopD72ID1%$=Lofz.IQ#Q>Hdsyu]WqoLtZf@g<Ih8Ms&bB`!K@[`UbP-uxM$oT$~-Qdt`u8FU8Z-9j"|eupt8I80Vk!nh"R
>Aa)./*CT(B,hsOl)tJyEln8lb(fO[c#yfpj*qbx/)CXVN!1oM8ncWp]K&Ppbrw(N+*}kM(Sm,0p>I:ht3C1#GyBpLXz6N1PyX*}cI^t"ny<Xt`D!iJy"eZFJ`c5gMcGX^2z4H)whspWPtAl;Mby0f=wS"+o;^(s3/$1g,!{o-Lb?S*|#iXMbbs{$HLcNOo^Rt?NU#wl44A|MYvm*,2;4JC.wjwp02we+lU]O9&B7BGOw$>[*~F4^?EYyir:K1GLHM%Dnr(88"H7a4xW&B+|86kv7/.ap2p>+qehQ~,D;L2V8xT~VwAa5vRC&p.Ejof,23TIruxikp!d<316lgmc:
?j2HLI`-U-Kx"KLS50/n-FBFE,;
f`"uma9}=LG5HJDSSHsuD8x<1V`=/0m};0uY5O=Eg*4J1MCiTymp>5""v!S&sDL*)il[CeLD"Zplh}oVrQ]c/mQG#Llxv>Qm6DGkp
MIBh3}_L6m*U[mTHEh7Kl8/qS|piKxf`#uT!vV]6mCRan(A77m=gsh&n$k%k+h!^YY-;cKu=9n[*u}OlasHFtH5ya8)XL+,=k-vB;Oh~@&*KWEkzp&Z_HCMm.jf5?=q@((3L=`9E11>cEJD]Q[^lX5iJ^iH-2IPuYrp8WmgqF8$:W>a2>.Nt)rD[Tc(peb]x?bww`tG$mI4a(07ak=kB1A_cM{
@NQb+ux7(=L3:C&KV-
"T2dbnV#9%f*W7T?*JvD]D1Su"w`/]Xkbz)(7xLXx#bpqks4"Bb-cL@QT]5pI$EK
LeF[cZ;S9Nk!ZY(Vqoi:uRdncZt41iYJ.A:d;#1apc-g*jb
FIm
R[jjQOrv2%(Xa-CM,mF(e<UZKo4`M=!b#0&["2a&wFXR?%3Rok!Wg8-5
E`+?D=U!hkTw
IUiSJRV)q0z.XO*%t]9D*cOrMqnJ<3#Q>:yKB%J&wTafHRVv##P>&resY5dkHA*lae1>I7P6oX3]7[,cXDBW
T(7T)y
/wBt*/?UP8/HEF8^,7Kz&GU^;^wHTpEkh:pM>6u16["xrq}wfrcL$C#U=[6&9^/V9%IFV[2H&&XqC]^ycOhT1o8s@3pyaVdw4`ApJnHH`/n7
kk`Y=[dE>|k$+b
1By)}3QIkVN^^vq$N(pb4f(rNWzJ/;<
)B3&DpWW[t}?|+xy&9gJiMg?k.R*9f1iHIn1FU*]Bbw7-[kD?&3Uj]yi@7~01]Jb/8O&];3%|iercJs6H=ZATZ0>#LgUk6/_>1|H)g;_?@3)uR%m:5&e,`_&_JN!>Zw^2@`y`SLEAXjq1RsfXH%yFN"Wi_Ki#Bw.c]ZQX6IB!GMVEhV8PV7:Q5GJV`F[+trA1G,8@Yfs%%;&;HyZ$+jg1a?Rr`1=jvk%%Yhu{]^)eguTe2jN(N{`BKYMLIS]_tGS<f5eDwV0~)?q#d_mOQM!^rL%qM)5+if`eE/el.pI#]Um$3W]|XrBtJ``3jag{g">=<Dl]EI@Mm0]wG47{LLI|veV5F#fx?h/]?F0]]]>m@A!Wt3p;:|l;5Z@t]RVLb__GgSl<Bif5=%J?@vp2m#veh(Pi/V%fCF47Vf
|n7JqKm1XL$uf_zF(G^Ki&)04
D.VmE+atP@tar:_Ce$<#JQvDr]0NP0MD`bTNmyV;zA;
`w-lycn3ChfO.
l,"Ta^F';case"ar":return'(c0;;5Lp=)R?lU#O-d(84G,01IZKqth@k#`CAe*Yd,=tY2f$VE=0BgTEAEQH]uHk*p#^ytc?OM|"gv8sRo"*
-.V00Z`y(Oc0n:jgkVbYD>t5+Fc4pet}]hx{_Wx6yFD>+yRlaaX7scE$4/rfh/(Xso;CshW2X1bT/_uXW(+t?__h[Hbm`4#zRm@i-1tBa[8}5-h1aXkJeM1U/CO5iUtGAum@
K,>-GGbh~9_c3y<Gc`q[3]enN;EUHw/:P?Ry
h&iM@e*YAP<_%-vx:I1|mxLR5uE0I$I8a*?m:PmknZ!i=eXv#F+mf<ntkF`GmsM6v/kXe*RBg49FJY8gc5ICv?n33,)iMW22tFp|).I@_}7jHh95:t`v[rTvU(cUR}8xVK4c$}+p>$iR![.fy:oZ2"4t(XnNYYG6Vy
nhSb+sqYum
o`nIW)t5l5Yl!__,8kF)VHww7Vc8<kT
Jz0k`IFADMxwl,7p*iJ>F2
jp"c{jT!>WFx1Ja7PlGldSC+giv
rRMD2lTiJJyI:J>8QHsxqbTq?x6:;o~pFbrcqZ)um

Tkv656J/v)+Uw<yup
x5yGaQkrcRpVW~Qz2x]YGL,jL)ZQ%_frNP3p-hj-Y=1*H82HH%,.6kJxm0InwJN@xE.c%p"J*MUVF~B9B&</G|x3^M`ta6"|;|=s]et*>:sb6:`ImN4+4xedf2
[j>4e0<vXr_y*wiww
.awPTJ](ec)myZFZ5N&WRV8`@reA>;AA.["t
>y@6PTT-Z[ec]W05Iw^=bXd~v!(2!p`^(v0U^=*0`%M@9G1qoAi1
GeGq8*:a-o379,(gNT<AOl5Q2PIq1v*0Q,LNJO:>8UuezA7IrwQX7>;^jj#XK4r*r>K?$NS<=xv=(6qK:!<?R
0JJ>"baSqIq-PKi3qk^8SQZH2P#fC>hw_,N2rq4]$<LpN>>
$y7qU@7Sj^ZjeNjy^Y`SSYYP.mueO*?&tqJ-HHAqu>}Zp>y^<KN7a*wsN3.XO[bbSY2(i;m8G1mpGC?e^.bMPs,&bCx^WF%A<nFh5E/@QS`RaI=r@p~+LU+.,J5:~I&am5AN*+w9mHe_sr(;[r>bGmY2LVkschaE^&%&tU&VB#TpVZxjp?J/
lo#,u]CXNG-Xf))ab-o*5tqeyiT&@HOc/zO$-&<@gYQ+P7jwZp6$x}QU=UK`lr0Q2R5+2]R4]W8dHcU*xsI|13*?]QY@ndOR&UuM/YIux~w@?<&l.v_aFnk)7I80v&,m-ev`ZVPXd0yKLS0ARpfn+$R
-h_
"VB9u)71=IlA6>G#d6tX4i&d+);!(ZpCvXd%
/!8e0pg_nft>uLPlq[-yd0~G[YryDPIhm$V9")gX,v:@,
:9??$m@"]Vm-<wob.5rD"?nASjL["Nz5&;:2Y5vo1q=i`6jeci`Xw(Jm0e-C#!&%:l9yVv^P{@pRxNT@B!^x"^aajooOloL^cV;>T7{1qVnXbnOfhcw#F3IO"wOn|s8b
thXef9dJw/f6YuVj;m`M1R-1$qY/8Md!I*X351CjB4U?.#nATKUqn7pw]n)e4:rY=(;{+)unS?b!6?B+!Vn}hxRC"
&w*RN$<K,|/~QDo/kv`B.>QIAPq<NB(5)=N1@@V/ja&[rJKUJ<QuM)j:>rYeV)VXfeGxT|6E#6,uTdS`cT#O.}?$Sp
?9
:EhiWW=Rw2:YZJ3P7U7:$O_W;Iya*sx@3V=Wdt[3P*!|_?d0;%iPcsjTeUEC"z1ay3^Lwyf?3|"j,jj&4[HhUk?O$EQ:I_r
UwfhpXUY9$ji^P&$U>]Pr<QFfC.SJ-oZ^Y3Sd-1trXIH>V^7n=Ikai-4;p=-FrExuw6)trDg$!Dd?pLn0."YLU)bJ8]g5BE~Che?Y=3o%]SqfW.#Yq2#xB>^"QX/fAdaKT.Bt^Y/9{<)jZ!RE=?yYZJm2&"IAC&7fAjQx1d{q^.lQ6DQ%4x0owtGh~2"F~mhYI,ug_G]p?"e>~/#ym;aM%WD*6?ufVh3$bw2)cQMZBN5%&VgH
,m&Bx7i^S07luw)Z22q-8hBF[ugA!Sk28"+rp:c2b
.Uo`3(^"N=x*:<UE%a[SC$I7pMg#rewq82(F&]awG{;$NRSd&7Dwky):+N6t$(n:2jWn<^I8yKbNt^_KJ+6kT:u[kaGlCFlw`%8JkM@VW<<c3yP?cJL*O5+KA3ne4m`zwSR!6lNnH@Ll;sJWQ+F"N+,|L9GVt:g4;3dY=1!S2scGN?mxUKuEI=Z<lZ58YcMM9D.kfQKQu]K]5B.4sJ539<*
jv]&&DN[Ss-l"b8z^T9jhJ^:5"u2h@xC1*?E3fnAs]wf:"5:#IWX.F"_m+Pyu"FNVDR]tDhjmnhb
v2<$ti6GY15?SEfEgXcCPeJM
XJ6YXhWe_$#0nu^O*oA/M<;nIC;!AC8]:svJ3_kNpt]tc=87s6Jo%1tf!?`3>U?J;%e]md[|26*`m5>6OEb9%#,~-GKNkzW~[3g*09p%hVfJb0CYiwT^&L^;R2DX3MoVv=n.crMs!0!/w%sR]6CSqjrtl5*&WPCrYqq"!eK!,U0a/)5%;r>;.Ea{>[hJ90xe":fAm98Gxrb2jwUpn*H:Y50kVD$neURIRG*wG*_U.dc|Vr!(11^B"]@6O+L1V1betzk|oLI<==9
S8#oS04_.,$.!J3[M-$L,#,q(vlH$FA0BULS_ofVc4cdH_"J_=<U]F&JKH2{#bF
&NkUe$o
7gx.nIe(b;Z4P88
&a]DxK#ZI9;O
Na,^h;/?8i!hf"M3UOpm>B[HOU8Vef6bN"dmITYA0Sjp^@ZRP+VBKh,/_aGLr>Y[:f<"C2U!Ll3Jl@Hu*
6hKeLL7"<!9H4?3gp^^M1-KGJBaimbidk)qc-OwE[]?_4Q#GLM"Jrx`Z<#lqQ!.B]qt<K7}wwF/U9<G@ZjkDH=!N8OoIit5tfYPB<P98pjM9MPJV_Oh(@*o9ts-]VP,H1Iq%lZ`vVs["WTq<`dltGo{1v!ew?Pn<b/qvJ[G0wk?iE$6N)BD_mnBhT[WgkpLLR].&h1|)UFTPl=P_N>:0.%7={Asx?);v0cq6X!fuHoyG)"<d!AwZq,?iFr5-gK/*qj>X[TD?#!dpu3!hL?acqV~E(Rk;_QkX(nb<:=]cvk!xKI~&%M|_RY8cb2*hRW=G0k=?lJ0plpr5)`-gU9Mid681Uh"m.ZNuvOqu,Dp/`xBRy9V`<6ycPxBdDnNwY3D+#Q~iVj4#xp855F_t=/<;/Le.<@*VFNhuQ@1)-_gsn:R/8nQI661j~hNv4*B-fyQQ&qDN![Q`hfjh`=^?>KRl<-1V""Tv6]rmw4m0+90M
.)HI)AE/SSAW/!""';case"fa":return'(s`09bSZ+&iq4"GV5-S9yh8ag:CE-Wq%$x)^H+]22e>*U#62++,6chn<iJ-L5yt<V/iF3VF(@P1R:`hyqr
rhcZ1}7/yAfAc3nmljMcm9rca_x>r52Ai4!GX+Lm:dm@wYtWW,k
I0^9B?Qlbuw@Fyw^pp;e9Vy[m=$;cx%%tfACh<g0.7S@7:CVNEX!=3]pyLuV)FkjhV^pnuM_/(lLt^/(1NbLVLswH{ZF<&BSDnTIk4Z~<cH):[y|bQ.d,(^n2~ulb`Og,go^!-=KvdpK$8x+jy,fu.YRT1r[7zw9JOYC7R+bxy/!CT$Ve{d^8V?c-~8mq8>y+KsGBst2@0l?D}xKE)F|^f4B_:94gp.O(iAvaB,8YXYTyd-3RpfL]]FZ>k,.x?]wUUP0R1T6E[+[F[A3bvaYd$IA>LS)>hoLC=j2DMx31a7`0#l;F)wVw]W*)IBlo32]1$L2y#*&$07ka(mfZ#UYO>oF9?KRtXcb%D>!qS;+@EV(TFYwIG2N0zXX,z4n_c<~$qw"&(C`w;-.N%27MF<*Wq4UfDWno]ilJzr&TR%S#fi*vJ;^N?g<K"l}fvcE/K1~"!$onKAZ@g"6:6s`bi1Nq)Et=pi;H&^qTJD"n/kU+Vpxp0qKIu-tJ/";E6=I4t?XJe"e*ee^5kf}Ag+MSuE^`uV$@nLV]lu9C.V.(xME/DTnpANeQtVjFCZ;^vtc-p8`56<Skm>%!pN%&)K#Xi`?GgSii.Say
K+$Et3j5uZckob>~8@6gm]6_[m`kP
%Q/P]p=Gl874%%J0riy9cE/$#@%DU]r53{Q,BqbOKgd"9V#10]?Bk81zg4y,AZ@=H$hX;[nFljH/0|@6OyeNJj/Q59c-+K:F?gN[30_:N%7TVH*8tPOa]g5AcMB"%g:[YF_?HVYGp0
l=wNCgMaqHjvh]U&7"!Q(S2-G3Sgl@[(Q844D"yC,wAeGNSy
wXuEt@PH?
[)#L(VGDC!Pf]$yV]m-cHdQG&Ed--yxzIylwVhT@Sg9u9_"o3#=nNEwY#G<B?K.^]=/85CRbQuUCVE:bUbyEMs@9MWG`%hvzImOTBG$uaDu0R@]9d9X(%WCL;|Hhw&56-SwB7uV|8w#N[S1p#q8^y1yC.jL(H
$1>!OQqgRH!o2ck|VY%,QP<K1/e[Wb8F.K%NUkraUtce66<,W0o.J[OGa(&_J%TjU~VR>)/(=BpclyTtf/*PFlB+5,DLA&z(jbyxH[9mgX(t5c,n@6[6-a4{67/=D/OdTZ*?1pg$BE<Z8?>yi{/JGe&RQEWKo?n]+Q2_:3up:~x,u<+l0o(~$.[/k%/Qr>[_V}ap!lHm:0t??dRhC_>
&pu0L`
m(B9Bb-.i)z@mm3NP94Fq_lNS!SZZ%MIA;fS~=/%KN_$-)m5
FT
P/*4~Eo;Wm8#yt|gdhiAkU#di"YOgp4J?>D=g(6qym|qV`$c>"3i5QyENU>Ec%d5N=Cxhdud(1*BYQ{#_Nmos5l2Ify5hKe[90euf[jmB`
U"QX]ibt*
cOEd#)URd3)mlceRrunXY|r&Q;AON5)CH`j<=9xu5a>|Vx>9gKCb#FR1W6wop^7rIF7*(7Vq6v]w4#985Us-%f;W<HmZ^xq0tr$?xBh%
~
7XR>>BfQepLFd)*H
U)R80]jG0&ilIWS]WikIOlQtM?mQjG`CF)*JpybR+Pl:SP<Q-W>dR[UV9I<zn|#=
syQGHM8TqI.(2JW&kE~ZUX0.BQ&[IWMeer{DDl|@[_TW7vsLcREQoN*@2G#`90z^@d5!hWKGZh_xlWC]vQq`j#B*m?Hd?${8/$ZDWrSeTyaEIv
W+Uf.>AIO42k1zljW_EEUaKJr+Ys;=X*KgOFZ;[F=jQ@8{SBQ0%13fS
dMxBRUi)$qn2RGUk$<Cp3;2%mM#Vyi%,4^7/^HXhc@B3C)tPZUBy(O1@k$7/J*aJAEW2#I#9crM:!}tMx8
11ohEjtEMh*-Q3
Pe]*Etnj[j>
B@:Cv?92@b*_
)TO]wlY!<DAhkC-eDEej
Lw!GTBHK6wMX([]VUl0@wX#["2JJEsWIYKQEe%;s-{^nwd<Sj+"M*dQYaxPP[z6:gtA3E*$0HIwz`0"rW)<W]HQdxPc>
UhMKM?"P-&sxiLYM`h3O^?Bd2^J=6]Vj
SKV{^nnsE7fhlZ583]B-D<r.2gnHd%6amg(N_4%=&*3K%J3i<Ao5<D+7uq/p2J4OImCQ/-w|cxm):7LKpVD$<=5`I*>WUAE>b
AzYSh?3
aqu|aHb}+Q96[oc]dcy2weJ%S>BZUD==%Hve3a`NL#G2]P+peLCW_4oq:t]63m]=i7b_L82_&y2y[O2q6#)qAR1tx@aS&Y1>0x*]/`gXjQu

cs#0z;*h#D^J<^l!dO*#!o:_<(`@ac=W$/x9})iB:0l:t^i.DGLrHel)c^;DNDin8YssN67yW(D(z]!
1m)iX<4e!R&WTk8:y:6;z68el3D0X"5OEIT/FKjJL68>GS_4dj33E:o,2Aq:E>EIx-3?6o
s9nV%DFuO]%UmkIQCN*ZaP0:L?"*3*m8Dpazc!tx7u`X9WHX(s#eEs*{bGhWoO"wo~`d"YFP+;oN?!wvRNy(GBZ>Z&e:Vr=K$(';case"hi":return'.s`F;aLZ[/$JmU#nuLpw<3c2TpMZn%M%nE|P34[!T9Vf7)D"GQS$jDI&[9rk!.^g@.$;ZDWppFlv&sR>(.*Any+s3FrGkvoqDh)LK3*Vxb}GHXy?&
I`ajit$5gLg6JM%4dvt[bIRO55)uLK6BGr0l,r..=v<gWtRq$)**5m:W,x)ae4V68RK;Zl;=7Td#HPgs-Yp:s(EMnZJ3g5ai|krDm4iqDq^vuorLUeAdzYEc>N}Th<Gey%gp9Gs2Hj{ks_=[tc.J~_p?h3ZbSO7qOVj5u+`$-jj*rMUDPSX_!1MD=q=4`0L:!D{K|rpFzbxYyc*OF%e5[9ufP6]4I33u4,O:{2:2xfNVp<.[JqNjgONO/TFcvt45}E[y?6~Eo:ECltuF$.2bX8D?|qoI~p}^V04"~l99!i~pf,aJ32IIklxLY<c<NS(k~Xuha+54q#6W0/}yoU.4h5^S36=?f/Co_bG1M-thGth#?Ws1IIux=JUu|j>lrZoAGK%!+1b0:bn*u-Z6S$o0*]?Gn514OsWZ{h_X@v:>{W#Qp8#^YWG,#y-0VyR#Zbs;jj,-|oXVEG?3=l6.7i;1Ba;b,qtOddC
qtGrogK58#J]#;01=I~6Q!V-gu]A6px(tofsY(~v&&R@e_q#kBa5qbFfnv=Aa0hmeUF-c#^oR?Ey.G2MFoTQ}(L+D$}CMP#ZT
MNHB0;B
)YB7ik-E
oDS/(S]%&@Z^#G@Dp@?M8l$vB7e/"T0.e.wQ7N0]vP4w+G,r0t]!P9jy&!p>C>"q%Gj*fz=R$<k}XScrfo42eP:Mcgfh^w:y%5L?/u1c&vsT!U3pY_vk.N#f&3OAV%Pz<%&5FS0o:N%WWhrt[+8TMuVF1&#LkMGn.H&uya)E7saI:Yq7KUukk`=uQ@cD$J(sZKgH^0FE5Wi*R~o,pq([I.%1!R%)8ZNi[%%Cc@fMI.0MrGd($k+M19?#<X0Ji(iw*yV.[88<5"Af4P
l5>g.8&lQ^cem%/SY$9WabfD%r{&RkQ%U?B3<grNxrdOnur+!4Zdp$6`XYC_d%ciAlRnhf?/.s~jr29Mrl{oJU=pW[@KG_)URVg6gas_0jBkG;2.uU!+*lFV+1.tevC.]KSNmJ%[4b[7ZKd#5c_KjZ<=TNX,Dj31*HUJW+KDxM/iDxSw*v_ZEV@QD,A?+fB[<;Mu0l>vJL2+yLQ-Y?Ie9w?&*C?0=%HbN!0Qwp:8Edw7X8;9x&O
Rlb!O-IDro3Le!.E__Fe^eBddT1O282ZQ@l0R)Q8*O-YId/5-(|Bb[.
[:d_d<oQpXb/yEO:=&5Hw^IgV_QNZO&<UQ@:2`Wnn4nYxOM_xYFPl4{!U?Iiu)Z%@F_j8EAPEX|L]n|B^ASZ^ey-S4=W7JfZI?#qlZS;aXsDbBnNd=54jH]U?VP?MA=.pkMr_,v%Nr.WLgM1JZ+<wNI<`9ba$[2JPLBN:ifRVi`f%rldbXi69s?C]D.V1b_c2g=j"w$c/pS"pDd_dU47MvVJ>aNrO,X13M{KcEhn:`AC9#us1S&:0t}1_9vm!D/f_>6gXr<N}:}qyMJ8TnfY+"WbXSh_4_MeVnQGukY)S)Xn,!T[f<xG`omkEJjUoVxO0e5_"JzI|/q_0p?S^<v;|Epk,42beG9/J$Mi>:?@7t3QiIA!%O#E5,Img9[@>Fei=)VDHvv_-<u3Nb=N7aOSY0,.m?u7XNx1JWn[g+1kI0Hf1MTp8/~QMG0!bmaBns)J;@%5cyIZ9Q55&?[O+j;>sn$:k9u3Oos*(#!6_r]Aa=xgt^z7)?lSYAuv3)nZOvq6vi,nHP>b
^H%uo/O3hTMZ(/sI+7O&vx/6FFGUpod@kklk`tjb&#bi=eZufg[^2j?v?URvrR.pwlFhrdu[)1UX#[xeSl9T[pxa/d`<9vM31_s{2cv+.25jDWJj*|xv?pl-[#=X1
R&TwnKZ+/"9[b<;Sf<_s:Q]YQ^ReHCeei2FSlQ<[U@>%u4xu+KK,<}BUAp*

e#bih(";DgB!/Y!f`(K%oTc.eFXyg^Qm2UjPgm_^a0n,;Q}"W0t6<9^[^P=UPAMd*.t*e^!)j
y[;eA=/A"w3JCN~3AWyc^/:jfRs)1Ubywsbjp!dKA$}_7kS8BNW.Unilj:}iGEE&1i`-(k!@IvS386FH1O&TGxSFKd5IfC1IA6f^j?}V1SH.:u_v$,OgJ<t:5er4z
qj^*do"j)l[#SWR[bJ1$MQ]
iY?iZTziqT6^9-~H{f9.}Ldv4vOS.>|MkVp10eGU"v2M)f/d$R%.-!S?R*}WklR<sU$(s)A>^38Un5ka=5&&qb]V-O1lA3r2433K:0Z+^n7k1ZbPfsGx>JPoIWF5`Ls=8S|G6udOcMgO_]fwnfTiHWVgzA"<(w#x*O!J|0Yiw4rnhx;Pe
e+?F*QF],shp8C~_MLstZwC*8-V`B2KHW7_snRoZUU,iUdPqcYf51"E5qtit[CJ:WG)_T_feh`n+[d-oN+dG#O"En)0u4QbpDdW-8<~Q"6,Vl5<8tRu=P,?*fk$H{:=B1ntr!sNC[j=K/L.?-amcz=bBr),ax63HO0]i4&I@}/SfsGk:$x8rM!Z@F>+y
_;/Q24]z:7
pt}s*H%jOdf`>DqIJxrF,`jcl4bO7^IxbH`u0ik,9l+Y`[rCi:pN3.zRLA
rj64WT[549pLAW42]][TH729=mVq4R=]A*y2_$[L)j=O/0=m+K;}980Y2Niyh$UuWNVU1zl*:+,HQjU-YawPg&F.g[,N&6QTxV(BRw;JP#bh
iW`[/C<Z=7]`u6Kk:iF=PYyckM&$<X%(h[HA
m~]k"Wi#_1FXf
Xy>2pc<:pQgA3Mo?Rp(WQU2)rwcMGM=R#Lk->&T%t>u0j6e?,H8.pKWQwB*y;JuT8s49qp72&cf>-^l-0_,wZHRK8igF0ZsESyXqq`$
&Hh.q*x?@pmE?~WAHgDyJw_j)!>
Nu`ZZ8-+HR(atr7scoC0E(,*x7D^V0j#ajT:4sAIddQL0l
H$@:Hk|AKaRR}h3"npl;PAG.:Nn=Ft/+N_>q1%rZy4Xm
/ZS=f-=!J0_9G:sZW}bQb>2VmF8Tc<EE"+B>!63ovar&2&AhNCh@7BK[IULv=M)EtA1A`Bdz!8Kf*/mF<Tci61+0LNmC+~]44Bk)S%eA7(z!y/T@:*Q,iVEA5_yEyW1HL[^Q=0T6HAV;15p;NX`MvIF
q#>&a;l0s23mmU*ZnE:(_.S{1f;1BKFXypYV9N0o9&<JQ$>DH89=B281JUC~[mGLM:Y=gpx#oONG_oHFwVsxB7UxJ4bs:g7[x5CEH>Mh"*c~)+F-,;b{B?>.c-?H<
lt=w8:2_NeSy;dDr^sL26*>dv}DCD%Oyp8TsN@J^"{ZNn%]vgsKWyY<r2k`|xnvdWQj0b&t(k/Y}6iVMT%"[x$<cmbyIkci<Ah2cjHf"+lR12=Oi,e`,B9s4N&';case"bn":return',s`KraLWR#At?lQKBA#!7+<-%v(cw+p#pP>6wO-4yA&)/,Jc:q%_{&?"ZBj?WgR29S=E$/_k@Xp-<z#dD!-ycH)?nEFxPFxw.:(b;A
mH]"
q&&K#HO0dl.,A:Ds)?OB8,V
g=>G!kHNoe*XM3`Rh#2G~fmOm=,nX7LC,.SBk]t*6sjl%l~pc8SH[a/k6WC?yeu#!H)XC4V`{Gdw`*.bmUbBuHjtrb=VsqgTP+5Dhwma=vESuJ"uV+NdR*(^wwOXP__6[I$7(Dk^D%Kd+c"fn%NGY_;c5_[X<yxV.aURF3c:JEL8A%rK7qJOLweqvs[

4e-,TC.MSc[URraw7/&BUOCuJ4#?#"["!zLWARH1vxmT7?qE:5P)`Rxk[XK}[#3GeYwBkj&ID)P1*r@V%3r$/kBb-&_vdG:.7tlPc7vNc6gtr7<&DEZXo!&HfS%M_Dm4R^[Ze<iE$;%ve%`d@0&wcT7#:XeO$qsXqkK_ISE.Tq6LB1"pn>FGD*V7?Bt<AJ-(NYcUnN+rryanP<Bwv5@XX8>~H/q;Q;ii4fdg0/M;-xxfTw,3@T:FUC1O7{G+@WrhJb[c"WtdI]G7hP$YSx+YhX4*7i<`3:$hG,Y4$Eu,,/&ubRuRP>D}.0U^(TG9ydOTZLXL"kiY_1$R<~:+]u^jdITc=tn55d0)e=@Mb~J!lQ#g9>C5=
_h%?W0TX^u0>EpvmKSQ;N_WL^fU8P>*P:G`:
L="%x8?mr==pnA`%YYDj2[cmr,}ZgVZ>~V3XN18aL%SKr>#$C!?qN)%3[nV2bqe@Dt3fu?Y%
LzVi[Jx7Rt:a7.*71f<kN>!=An8HvI%s

w=a_F3yg_:Fkg&rzi-wJ4Kq_&eG_vn)Ue9j|.x5zFF[nOLFohOj~%bLc$qWfslle[,3rGowEv{WO4vn`#:^x4mkr[Y)Qv&Fp*/IobKp[:?/+v>MFOLj*Zp:0N.g%HCP
VghA
#X`I&U&nDT<5G_TtZ)#ijs28k6+=x48u<%qhudanu_>c1+Qs[Z-BAO)hKR^^D#WhFC$R4yQ+A)`;-$~98Kl9V=v)~<tY3I)7pnQiTs8VK_bkU))lM(WaAfQSDjeD~DSy=*@B;I=$SwV"72GE:_^WwhutsC=,
>%9R-8h^D<-7a:7$;<^eQ3s!PEZPgHb$(xfhOJtca%<xYp4Qi"HV)q,$1yyGZ/Zy&:7;<8&Y&ePSpU
U;*$tT$_f_-dn@N6MW.L;`$tXch"[IkvfgD=8cL&/L|w^lWEZb7xM[[k7oWc8B#dwnjhQVhc%"?Nxpu=ud".2p
+B59:|k^4:^FVLe
go4vF<NP>|fvhR.~&IrR4<+n_loR#FK~r>a7H)5OQ3k:MItQ6?%C#GO%iauUq/IlO^8]Hmhu&oQH#UX&Vi"}po][
.ol02@l0H^C.l>~fd**u(^D"[6tjjbNy8r^@5>Fi%2!W6yjq3p>eK,P@v^z@tquA@x
(3?1P0gcmpT{+BQg;ncBd"a-Ni%u$/_So$3
O"^r+$k/"wp|E*Or?@G%=.w,FidBU%AY/#rd>fiAd/:=
8@Mei$!?C[lxi^
jVx=Sc%/b6X|e"Q2iJO}X2${Xm(CiRW$f<(BHi#Ls}XYb&PWYx54w[4PYo7LfQ_@I)>jo<NKJ2-/@B!GUb7LxgjN=n]@?COBEdWkHi!7LMM??~Z`fF=3(2#Ka;0|2OI<<`[2;LdN][-Yf1bF;K^,r#-EVih7C{o/8A;s-!n
%#=_j#R0$nK.Oz6KeqV&6HiHb+
eXaEr4=89Iv(l^W$bNH?**.p]=<N+aarIIlBES[ZX3*m$AGYwh"-_v?.@/!>M"WjYc4
"v3i"/bgOij9Q.{h_NvV.44FY`g+eaqM/lbu;WuEND:wyoAng30]7KxY24}HE3F9nOESGvl&@G|mcJ.X|qGv`q*`c_?@_:~qLx2Sk/
Mv-1n],.6F;.j*=m"VK]eeQ;nL2HVH/&3S?MUUo5HxlbAHm17+k;N;*u*^)#b[-ZEDboV7"inG#rLGU:fs@^oGBd2dvXv,"IZ`#^<:l9Ot7>+[i`KSbD
1>v8&aw2J>~W(ifZYJBT%AEa(NgRQS=GuftBb.|IK]DT.B1;6;ClKv/dtCPnO`}m^ZbOs9@hb1<[VZi7`6+A?C[gG(~:uWEKZGU)")Lw)ng`/r^o?,?_r?/H?1=J}t<ElIO*FaQ+^=
Dl#uvCgb,?+]hfw$g?&-,M3N!lm,*hWf>N>H+dkpTj!^2vQ>dZ=mx]0QY3n?CnST#P1kboEB[NB^MxNTk{xs5?Mj"42h-1``B`YAL7GpPURORSD_^jHv:/Lkm;0_1z!0ab$8O~;.@9EJQD=e"Ta7O:;2@E_lT>3KjKU8_yaQ053~Uj+KV7H/_=)Ei&5=PP34mHVza@)N_!awd=^,4q`PpjJw;B/wku*LX[0_]mTT)?+&Ztf]/*"dTTrVTPeS6Q)>t(tHBp8~6K/~RVWzpjo9NDi&`d"Xj@ohhN$l#@q7HYWE;I02:"%sz%yCSOUPPys2o}7DgC[T@*/#TsIC_Y*}4/"q!nK*P/_]P$1^viG`.&8Pw/SqEr#>j_,],9.
PNl
=98LZ+(w6lMX&It55|[2B{IAH(t9TEJ^5&TW?JZdvWH3dx(
tUc7!ykjTReO_=1b4^x|RA57VMa)5mXD
`;[iAv^_vh]ApA>:*R|(b1ip
V@dut+f]iQ.rb~.K;15c[R_*$-^!C^Vxwkn8+&]e>ENGgK:[+=cOCLg^lsw:.x<>FlyQU;R((*_QgQ]1iZPJ]LgAKa?aA2JTnkILw40_Nt]DL]uy&gHdLDo`5sA7uFKpf/fkF.K
[b>c$e&8`F055%A
g7c4(4!shmv3a)+#Y#kU<V5flM=VUh.MjW!0d9dz(3F$cje{,a&ENgxNbnd5/tZuPR<Xc%/%qvpY#(h[=Iw!2dFYQu!4_fei7V0|bV.At/EM?n,8^_;&]Kf]2CsM0%>Aqc^Y#v+Wr#&BGzY9*2g_pIaNnnFbLC;nenDz%YP;]sPk9B`@`4T.MU3CseyH>ZH5p|X"E.5d@ny]Tx9WrYx/$VC%p{l2XGUfK
;0"JeA
PwI]&K~FO
5j,5v9|BXtb>J[.inroQr[F[b14-UqyEz$fmaYk@y?E;z"X=]$K!E-tjAAX^?L/!BCk5w_62W(?qASje:@qgH3>J&49bz5lqi4>aq6&WtX7mdC`osB|
5
iw&WiyD-8i]l0*Y-qImk9N_),[K?DK6U]^5P{9egbw(2/Rvwil+1JCbJ(;B*Lmxnpa@Et^
@FBCR{XGkyhSVCUx-zY<!TKFL+q}sG8F)hWj4$s|>D#
Sj.TC.7|q)u<RuYe]ywiosf)=e4>"[XA%JCc,^<6fT@8IGV)5d:VvgN0UXKitS;::e%Z7s-Ju;j"=eJC_X-JTJqrBHh3%86OZ5?/wx^%#)`$hOn$1!pvx]eabEqX)^OHZX.pY
A!XNhTWDO$j`,Rr.v5&Bx2rD6~BQolPNy8Xw00QP7e#4-s1"bUwwiqm%bw-Tw}d0IGR2!.dTy<q!(Ns.?G5GH]w"ArqxnIR`.LKTP?[oL<-*7ai|tQ7bp.ZDmEO
Nm6#WGAZQ2I]23i61~7#i&ZPmYe+9tFr0f^$-=A!9>6re2Bvn]4,mJ[k8_b&l2b#x$?S:YkDK~
LxeC%';case"ta":return')s`K2aLZ;%gres?G16B%NrL+FeG(+0.L~d-u|hd;Eom&z2T!}Yw9x--bS8n9qF0axpU1wt1Vqc{v{r2CH8G=^TnqMsRXCnllua.s4uf+/l8n4wvQzt47oatl5<^5s32(_qnZguZw2u#b^jjLOs*h[O8b?IpO7%cjq&)uBt^cPyZMMnLWmf<1:!Z.E:2B(8&Cg>sL>m?@dvYEKOR@uJQx!wmrM>$b>q)h;BJF|M~_6KA#&y|z(^Bb@x[z"11vD(eMcw(R%wXG@N)_md&*Oy3%8W)lzshPefWq/$%EH?]
&1zAHS}4ev;sNcXF>c6PdyzW_,YOcC@E:a-:j"`qgh&#@d
Icrdva3>_<l-R5:|[a8<nV5RZ,U5tW-GN<dsOXZEcu_mnRL(/:P^,wmrybyeD8)%].f=O.lT--_Q9UK9OF).Xoh7
mt
mdi=oN=t+$Li8fF89c)[E3%}XwOEtnS89^RyAi47
{`W2$+!tn79u.`;^zhM%0)@Ai5-8mduNp4wH%"v[CIAnjCWa;%T]cj:xw"%WH35ZLyvKTrSGUVrV-drPn-K`QZv<uoiEabIPe
XwBG&G|Jl"9`Yk3!{LaF
#S6FIyFoW]#KLD-[SEclJCsd,1k(AUa-Fgm-:bl0fgropH=F<1!L2OH&U!3
:7&?pOQ@ve+MDTMBq4"e)s;0,^qZYb)|jHP&YGvej^9S/N%F=(l]r!L=+.
e^.S;VElLn<H@2@)_GtyC-vXS5:.]+j+i&
-KL
Fy5[YyS``~!Ds$cm-"T02%ksgk"Pui)8uq<%(aMKsckN[*)V@B5?G_D<L#]VwG<O5N(>Fil}EmX#-[3b:BtC3x:shnOS/7v?J?rD4$dPIa^/(dsJ4[JtaVs#lY(oS=vaW8_(cMD5Q9.Bb7:rIs&g"WDCsJ9f!U@aM<cE@f5}1zX=36do*hZ+d}a0:
+frf1p[n;B-[eIrG0F^gfM;%OOBdQpf@LD*Wem)>&}2t"h)"."d:
rMz<;v{^xnNO1xFM}5Fl4H~GPv/y]lat&-_+w$WonKL[G!b`d-kwfv?
F.-L*clOaG8Q+,4@WIOS`)@fd-58st!)ATVcWFjI^vcImM"Gh,zv<6WrwP1Z^!K[~o"p_M.wxy^&fuINXJ^G@f%vF4>^m-ktoMY,+MFw?cLgw0+[J";)h58pSiGDifHD7[n?ABH7+%V!VCibnCydtaVswo9.fMrB?+n#1z#`;?MCQu[K
[EtuP,)4#L:<3U:kbC
#synPiLFdPelm8`xHZ;wG*BD[OxOuM7pP3+Z8eqW?KKPhTr&o"7"1dGQ>n`E:XI.:ruk^.^?w#_x
0q%,+x0
gaCxg:ihDpea!xAh4vQ#t{)<O?^<CHF/O?RT@~Q@b_DBV[0<v.Eor=TX"a0SsM4tLDfn.:j]AG@e3NO[Ai&#NI!{s?<rPj;4FrAV$>D*CWK(]1sH5p/0VgTV49?W(M3to`*Tk:WAD;I#J$o>wjA0;/;iUsvfr58k0X?FsUk9ZUS~5]X=%55LF*8m5%W,V`IEbRkEA(OgD->vpwXkj!@8MI%pDcCd6G2x

U|8.qEIc[zEjAo@8Hz[n6-JGtv[H/N!ym)[zamq&qV"GD^Dw0`F"05?FCx+1[-0tZ[aGB`;8*P1UWTbF#aC9TN(#FW,i`DLKjQ0kN!T6+@KtPiuVjEJPWb,:w#@|R/K5,84k<c)/1k6[H?!+qwU]*TBVZ)*Sv7
AKkO(Ab&D])MtlF,%JW2Zu`>$anMS0pw3-T!2euU/Q3)E*oUoXH#!1JE|6UiMY9T^aaY^uKm{%bfn3/g{CTr:T*Q&WuP
W(fei+CG=&tD+X3rqUkX:*&nb,*W6/M~^:IxWibA.g)FGANdeOl,2.Z4iW>v[Z!Y`N6YM#w>
4b|TJE<$aG@sY
jWGf~o"6Ye0?nV[+62?qq59H;CWm%_x)HscT_<n$5/m,ZH
mFWOu=YPGbt!E#9cOScCD;`AD6VHP>rE&h)+1D$9*b/fwVx^TUMr4AGlYLN^xd2oi%
}]!APEP_3_q+Gx"]$hjaQJY#*PnETED6Y(Cb.D=T>pA;5#$
pe`3gs5X@V1bK[QM=FILQ0R:DQ]>=n"w_!0Iq/%:H]YAF/qKNgYdvQjp6<=?$.U14OA!_.3DH0LOnVu^EtxW%#apKC<sbQ$rMlbbP=DT*&fg;q+?);~Qw[x212;%L^MwS,Y5)ZIWjw?*BW$Lsxc>R
Lu7W`"25GEGd)odQc*)S#f@&LTq,?fZ9A6GfnWlx1:_0R(pq
"w(`fU/r&59ZKgL*/utS#K)$pP6[<A/MG53D/mwci)IUsy0/_Mo<0mqx"{J54;k1!rQiGFT<HtWE6(<%98P%fbhQ2oDQ6&vKe`P~gU8QZ;A2qmYDrSu8@S]>QiHeD[3Fj
2}99YLL[7O]J
uy*DX]M"Jm{k$!`EZ69Vb;,
eF-],siS?nZruw@y.K{CKjtV*Y@vaP5`s@XA+>V%l]Tk)__Ud69F0R-T;s%Lg@haPC/>1nVk:-qu!,8K`s/qUo/?G)4TU?:4_(7i9/zU-F@uWU0<rW
*<O0N]LGYR8.hrJxY=hX,VVfs<=jtZ2D>7QVL;m
/]i0y%;14R%/b!8z"vfE:l]
CtjM$%oM
2Jj^,BZh7l"AriEU7Z`/=]6r5ID9
;4=g_xRKGe%ny]0hepKEX<dHKP:PK%`TR)Fuj:`OV$eQ#67C)?S$xYZ|/&@$MvQIND`v//g{#AemMANLC2e}UuWx6yB;6OHWfU4gAtIx9,z!;&-]bkKRTPnR3=,#B}5pBJCcFo(1(hk#3Y6EV-dU5<@"9iww-$h4PUl*
BG@kjnfG$`?]T=x+P-]lg)bv=+ti3jaJxbe5G)J/ia9Ao!(QFIDf+vn%LDaNB@xxo^-;dy9l9X/+?kC
VR-PK70p>kaE)I^Jt%3IApsv<VUkw1Y3:b}H:@fI]vZ35RXcZv,vg`eM*[`K,qEIg=V)IL=?R##$=C1[Y*!hIOPh8RmOd^}6vf{(nb*4PRdo9dO]OOh6TIj?np[v>GP#CQPajK|DVQqn`nJZ0yuH4M{oKH*>W[=@$bm9i2qlcL<%YaV2~4uIL=x64)+]!B=KHXPu=?Wrb"IOS3EDd(DJ{!I5UIaQ[D7;05sQT.`
":()BdmJC(Y-CsVV_m?;3XnHG.Vt6f(q@TqV~3U.Og6]gp:a0XGTML8d$""';case"th":return'+s`;s6KZ+!Lh~M&</eKwbXL&`96T9HyQoh`eM"4snZ,<T8F&6#E4%DyJ=D@U11,>pE]IaEM.O"a"j^3AJs0B1
m@%EUrhfj3jvMG&vs?R7uo>UXn@W;x:PC0sSG^]jqBl&%]YwPHAG[I
Zu](BlxRr_FkA(=}k<p;y
XJs5eN(1?2NP%2ip_hMxJ:+}=PKET!EV,Nx;K+[B)#sfe7PxIb)pN?r>_6o7`ma"Ml+IE;,KC{@YBk8@N,eBh=%OM&*{HAl
,4IVPI;+"N%H2n<x"xRMNQC|OmqclwEXoS9(2ajD(
O1*m>y>P6Vk2X1Ne4kELghifk+Eyg$I9SR#EAOuE"=MAW)[l*)&j4=&%gAcf2SlYa2YXP?!0#B4K$`f+j/-4o.1!j$+HvM"AKkTpO?r^v|xui},RY9?uFPjr>6xj320
+<FvB",FY.8v.;$87|Cu;DNq+F:I0,WXFftIWw&eTq$Jdj0f8b?k%Y_s.rWl2G(/;qxO[V2[&;WB72hYV.$HM33$MyMM7sA7W,s`)h9!VoD)@Sv>U]2aG/)R:@D}fE&(#E5*>Lg@[NY<celPx?9JM"%bc6(weX(+w7<E[
[(0>$*Jn.y@k:B$*]JJB"AsKJ)PoRwq/S%X@1!2>(fC%E5$kR42.
^F&3iR^#vwlWc
ZM*0e]RK(7*K;bVG[TFoJ`u:*X3!1_>I_PB[U,b6V#D!N<Kuv&p^=N-Wn@JE+.8n&>8
Tl/-7jFlBS]]<^b:vt:RkX0efy/O}5K?qBZ/@c%brSYx7o$#1Vz$</Ye.Um-EtD-@-GFT[cGG0PTQ8m4R"O-^]TZ
kOp|#Ma;Yu$#&}Wd6cYbC%_rHy[D;;CWjR)~AkMeEHK~)B[nYF.A[oCqtk5eU>-g>UCp6X`=&|I:H*XOj}SR<Z`x$Iy}C~#a8*-be+/$DX#=T`3u?rJ;1-T02NC0cRU019:j&`h;(
T8vdj"]|bG:]v&k[$g;IO&@2s$Qi-A["tD>oZr7QFub^r.Sz=nABVLmm-aoa[.B9"kv*!p$C%ZwiK_Q*aV//SYXrc!o"F
-p&%9d:E,+)fF;:cs!xCybW*sb^I>T*qcOtD_45=y69FAtF_JGdtO)["h%>?&C_fy#LD<yqYV}mqAVnL<ZgBM;HEg_.gq!,ajkM`IH[EQgGUWW!Z)j><0U>5FYRp$p6$ZDr7IH]vRP_85
f6J_O5VgA%l93Rm_>;_{.&nGKgN@5@f6XU:Q3pF3ETE-0YbZvOC@SA*+LqtKMXiMO;8Hax#M;$uGStUb40oS5=+T.gk{Hp3+^G#E.aEER0`mec/+*EG}ehJRPZH5s:8m,!;[a2v@lCA-G;WUo)Eltt3-<>L)T,&fHt<DSN`?$a1NR$(NXsa:[<P=>58{_OvQL%aj;%>keCAR;Y!r7PIzWXGvv?lLop2A0N7~EqlBrRyE%mI^c1S;tkn
S.h&-3SUaNq%(**rh|pK3{oCPld#Wr)t&sYU%};ySY-zKxDITtdAeI[}43Pz7|q[Tr2bGV!lLj",T<Rl`QWVXfOr6SLJC-71UJ
b[XE0g|X3(O5yZCX3X{-3V>tl5V]

fsN@22l@v;/OfZ5o9lcr%[=Nl<dv`1s]^Xz1%,xB@AHYBL[FnB8GqMMeXP"*ws^X@f1?EBeaf"nv3$~=X]dCB+6s<s?ol3C`Smcn)Dx_}A@t/g"*5CqLG9VYP_"4")v1ecrCum$nF,%n,a!%~F[dBW3QZRFgSj#4]^{=&nL5}mlLSbmy_0DrGJzP
=hioU5B,"nIH*6$esB]al2D5YTPyM}jbP0OaQwj^phW)x.Apc,-Gxxue4F=FOi&tUC^RMw;D_Snn0(j:6`$mEMkf,#!RdO4nvcnD(qGy+msGmS&cES%3b>#rOQP`Q<Xf8*njQHyWP{68P.3`[d/y#6O4Rl&>6Y9=W=%r4"tH+j/;mHRVZ~=I$
]8$(Qy3*eBdW,S2H`QAGiL@
U,<O),`QNsO@?xg<VyB$s@GxBYec>yb3QNlcTk^s#*T9M93rf?ZH!q.KBL[=9`,)La;$+zt"V)j?)u@CwCO[9@]3Br,_I*fw=B@"0TO+rijC<Tx:
j/$hU9S%A$MR?sPj"JAJZ4NaeYr7<De^zaQ<<vL:L2TTmD2bF3z[tJOx^<&4OE(FynX2en!jB]FxB1o_H"I)el&As_uTfXuUOooo0Lj?8VV,GP}<$"U0Tnsiplr3e
IT1nYdic6qv1`ny&Z7*CrFI[$ZNkK3Qfn/tD.+B+C;P5%[,uk:51SLm)zZ
[PT$dU#^b]s00{#ewg`8w~!sRIj?shJipH4]Z.kUz(_Ml[0rN^D}9a2;Tx0v0A:NTjN|b%G3=CqgN(<DK4[kDX7/h{RZ
7P+c&8KJs51eC<GO^[3I;Kjx>7Y+/1}PmN_t|_b=,7ek.->#&?4Gqi:b+eEp~[)K}b1[^@o*5@/Mw
~`&h$!p=X_?p93?@tU`dMD>_7yYC9Q:7DC~63FJ:*HAU$a/8lmy*u0@<ECu;`EI`5N6v{Q&h"G}RIYmo@?F
+3[68XH^0`y*LqG+0VnB%N?Cx@:%$3!-R%Zp>a(poI+]9JPnp)qP@Sp192<cOq9a[&tS)m&98bCR{<,&j8jQQ[P)apb=-:S<7NE!5aRS=]5k8B7AOhSLGBr""';case"ka":return'#s`F;h%Z*hoq40r>lh4Lp!}_B)@B&-@-m*.+D;UAI8wZ6Voqae]HIlyQM>>.&8Tl(:0sZB+;a>#f4]NxLLcx!dZm>m<sq6v1-`nx;PE1TvKH9vMrw4CQhRpyWHZBN1372`lB)BzfgM-1u&tU%vu@G$->r`pPZyV&H7
b0"ANVuzN~8Cs/!NC_pC"+ytCcB1<>F=2.K32a#@b"0KZ|]Bguw"J&$pVRmBl6"2N
e(D<aXCWC.8bdGn2WP4*Y"nhJEG.OE^@Cfjy97=&wU%qn`@3,SM1rqctas@7d<-{5u6)K4u3
"k
K`yvg5/%;c+IO#2"eC(C>9v.9p91rEq1(ifj]}!5X^(Onf3:6VQA"qlM2yYCo
,o=uEg<Amb2@6%G|2cr%ZeVJ(r2LK)I1B,]{s5FM`t66J.YHA
M!(Uns!Ymo^1hq]kn8(n3rbV!4E8
>h1:iAznK&DIP6}&AN7:&PQwDZyPo/q$OLw@4X37yDeyy!^l^+z.^A(j0!/TN+D-C)q${i*s1.d)l3#YdKLF!+9&cmo#Q71SL?An@QxZ@u(I3E)OQIT3
FZG>.lu}QjxD;PB^*<K
Ny``9*`.".uV+_H_FfpV0sGNc>/r^8=a$cDu*;tM.<OGP>li[t_;w73oz)ubJhB@Z6gYz#R`BVFZX0$?6&2P7c:-LC+Y<bdqV9<[1?Um6qxk@K7miC
A^ZZ#o`GArt9`bt9fy/S?I2oQdp.)DNX&RL
,Exs~%"dM$]
>&w+e*=LZ;*WO%U/1T5mv3Kp6
qbqTCRb.Hk+bX9T9J4`HV7ZC%xprs`P?eS>:|0DD3B_3~nS!,;7QC#yq1,?.x2ST4?Gu$r._KdX3&Sn6e5yN{iJ(-1H:;`%wSS]D(I)AU3Aoq6geCs~!~y>Ym&i0idqyCWb7pXwRxv^2aFRHKc)/=G~16:(iQuv:^&(`R22OOpQB.OEBFp-%I6k^gYj1wv1
bKS^j)&k:ZO@4(#mI:I,@c04yWn<<ESD^!_$a<)PN;r+jZ2];6]t3MG%#XX9&IpXi3pv73xt_t1n$`HtpmmhSE2g;PP>#d=(o`#3?Pdea&cU:lAJ~7%7uBoW|bv&$Wkjj-A8-KXc0Q,:C!w,K"Tn1MpBChJXdcDSan|it&3EW+KM[AS58Mojmlb^-2qy`TvcY:~%JQA%%hZ5nafd{UrOM%(N6"BC$cxa>V4h|;)9eP{1$
#Uo)"O9ZuK&Q"gK*e$Lmo^ZM7@$2/N[Qf8qU^BRQCLYgJ&[Jz<>8AC.-~Qybo
_xXV1-dV79Y5tEYNuSyZS<QAExpY6fOo/_h.IU9k%]=87*Wkq%~0(IV3clse|o4pXPV3:f4W?!]=Gy8,1MBBl@o"|kE$17QDBLF%2Q:/b$WH~b#n4sT)@2BK>(@m1L]@AR>%;!emR=I#4$Hg8keW&KH+YG2J%%@>;7YcoP/#C*=^SC9>PBoMfML=-TL_OK.?Q3ln(Fc&|4@?JHcx@>_@|hx=?xD1V)/AT=^#,F.-F[w#?##xYFM,C#YqN@`nPZDZO({NIqUDZT(=_P&;dO|fDa^eaf5@f>+Q6;;Iov99IDn6Tir7K&Q^U+i^^6)N5FZ8coZe|j?Cz/:b|j<:ZXNRUX.cGjt2=`kVB#KZg5ew`J`m3Z5eBh.ytZBk~Gnvh$Tr9qa0V)nk]ee[=;EGrFG2uOA496,HbPLDcOz&vx)c*+;*&^:%d:Mn3q0BJR[6eoM(?Smve%#WgjNTg(|;chyUF$9M29m:^#-n7Tseyo-J9,_$vxW@ooO#t#R$?:C7.C)Vk8@HV+TsCU)0|k2&CugQty1"SbHDMbkn&u3^{d^4@p91&S)P[F"+q^{h(fXa$3[%C*{No!laXq<iH;bLC$VU5-uY+WqS%[^Di;u]^;Qj&dj?T1/K5n*j:p|r;w>2g1!d;o2^`&beR8
k[[>JjSv>pyj_LTBjLq?
5eg3`#i(&hlRs8JJ&SJR?Fd/~wr]W"=g7v?VNdc?&2&S<2l7NDdS[Hh"Z:A;eyV=[c]Xd9+_Te%#(:Tk%_!!WkPQ
:>h8m.L!6_D%1$anSl<.wh8LJ2mxNy[0@HSnseY1#/f-)mU<V.d)rA>Eet`76<8|xSq.ZbFc"}3e**UqGas_BI?zb[2T+wY%G*D[1)w4k1H
4A$tEu&>:yQPb4gkY*(i6:AxgR]g*DAj>Z].0+9a,_7j<u2$)M-A6p^2*(x.sx<=PIwb]-ukUefrE}JT-g:YLkXTF5W+s+%<vp&,R^x~_nu#mPx%?%g38Cx87L+EY"b.N|.{rm#FylEH?FK6U0?$-n+uoW&G0~PHM=NSRFpSD}6YI>i]u"NG;w_Vq:r4iC(;_w*}1&eOZJ2`M$^$4CywdkRE4uPR/9t{8FE`R=2=C$!Z0cZ5=ejip{_~+5]B5VoN6W8.Zi=Cj1BP&78
Gd,E=Ux1%:e,2xB9.`SlZNy4Uzc:S`;uoB$i={qg*}TVHQPR.WDtS;@wOlmxJ2hk>"Qg*3=-YzKH2sDIJi>2_&X`@sR,g~;bB*pSds3,?5".VCiQe_QoeQu^1P0:xX
95Uv`dc.H55pa[e)YawFiO:-0Z#hbaH$ie`[|ul,WEIchMe^!VP;/9Fh=PJRV3gf9hU9{B4^oe~2^Cb1tFOh3En=d2QZ8WesvVhi8v42L[E
fpr]FZ[CuoxEw6OO]D=@"OZI62F
+/VX4^N=,EB]y:BjO>*9R!+=^-9p``^<kh$]PsPY
f
Q35)j`;_$sz"eG<|(L3nfiIBi0i0W=Q
),ozdv1%i#`-p}r3Def[Z@cl0ka*XSPs`>KMvlf;t2$2%pJ]=oxtSfMK1E+F:[=x$"V?/dNk@+jE?!c92?JHu`v(_,JIY~fdn0EjU{STO10
0&QtB`78$pTC+S%22MuG6ty9oau7_5qW!nb2*wtU:.#WS$y5%[Er(L92Z&E}2|evma
>[WlL0D27$v(~ct
GqiT*yEhM.~:G-,t++xI+1^:M1&](#7kp1X[LuxF6.iGnr6u/6#T@gj32iI8h0JAs
]<x^l5h9a/DK]iP_p2rqnW&uoKeKJ<?pS%cn(QoQ]$5"pq1[:]lP{j07^ZCuM>-_<W`:vUvTJfc*<9-rHM0D7"_$l
`]}N=@?3Bj$N;laYJn-$%k#E]b+FwfIg/>CKfiqM;Eo*xU@(G7vVQyzonm~-a(gwNDq:e0,he)A1>1n:x"s6121o5';case"ja":return'#Zu@a:{Z[1*S*mc#x
YI^@"4hqij`s>`75tWD2ex%yo,J&Hhh-k.L$T8h$8No8ljJ%/W$,B!v3jokw`HeT+3<0>J5qp0FKL!2s#CbfApeijH5xfcy!Hc#5WE>,k>D/*m2mz<z7*j!,DX*Jrb*scV$<hq,M<c,9DX`;[M1DuyDgfp1#.d*XUMPvU@96d<]p"ocp6j_HIY)%ZaVW=aPfU?uH8F+t#sCB:q]sCJ7,m_DR]e`Y4dGI6D4aFDf0kKneJ^y`|fN(#il?C:w_>DIqr^yRM#VBGHa$W7w!>fkd[gD`k"S!vww^Ry]
%AV.%//Xcm{qKn|p5uVnqJL!A-%Qp^u:MKHpQku*Vna@dG<c(c<+FTo7cZ6Ae8lb):/cCo`h5m^l!a(hIs0QJ6:vzrW`>V;oD,P*%>Uxx$F^s8X7kj3J,oxXlUt3eC77(1^^;gSegiCZ%8%l/5aHrg)6y_cMQahVe
FxCW:
,(o^Wc@*0WHIk=|1!6Zl7Ixo$T?muu6@du;Jwjm8AaJtz5p5(t
u3*miR$CO%4H_m6Bj>tgxL+)nea?
K8$LR.u7P:T?~I#j&Q@pujNw_6mmW]sVkJe*F$,>lyq/fUKSfLmyP3MG.?Do+C=]5ZJt?Lqxev0D+vj7"!LW"hw2@JK(-aMoL+SRTtBiZ<d@-9^o,T93^K.4J
=N?72M)d
^ssAfcwuaurilk&F*WW<#PSZlhU}v{^~"sD=dem%j*(A+!l|E#-q9e
1c6sp0XDG[QyVKDvnPNZVw!&Dty!*i65YKbc@-u2X8hYq3sb}^eICBBGR4%p&qhi0gJQ0#GWRlmH~J0%ppD4nQ/H/JS656j>[UO[njY&Y:!@D]bVUN$5XON:pAJ#QnCpmF8qA1WC!n]F{B1t$Fpt5un4{_0G)!l/
-@^~UmegGW>:K>)gQ};kc^TGi?eX>@$CVxwh("TzlM&[?G=ac~t!z#*#<y/TjZOi[IHH>cHBb}n^FUX(WiIgD]?[W2i=TA9]!p3
f4
+KI(yNr<w*P4]#&*;)BFbr
7oyU%l[;0*SWym7g5s3Ja
"kL*U2:0c2N_]9BONw)TS=]mqrWq)fC=^!Q~A+lZT>A.G_
g4Zx=x]Dq>yHc$Ss"ql-9Ha/s:J8S.FC#U;lzMb&VfJ7YBG`42R`:$FG!2MEfjN.p9]9vr)UyTT%t@|qy&M/*V:n</P6tGBO=EG_tDGKR7KXYmPJ4[9]6!_S"k5mzImFN#.B&:=c(SfLSQU2CA!*6).N}uzr<n;U8ifq{!A$2P`_:P7dl;(rn>5)*Ofe$4?-Tk571$g!$LF0#1MQYfPtZ(%E4ZX
nq:!LT)J&,p!F6Q"eyV6veoN^c7)Nrc5Y5g>l
up4+o<6nD`ViQ?M[L(-SL1qhLK092;#4*,H4$V,;R(tT081^ZHdB+jtpHTL`j]AgOK+9L0AqrPh8z;i3Awch|ZD/`:d>gTE>q#hlw!
UkKk&k1E;SBXTG
&qq0lt8)3YLI{-r&H2e:9<zL_e|G-MHVRiVB1Y*]Ri^U[!paSOy;!t<f2xcp-wmLAy5ydN2Z?adR8Tu@=@(1r_(1.iCSCSs1FFr%;]h%Dw&@$+2b;v!8cB5tx&DU"t1wao)A"2"nmf|q5*N.n<`r]R@F`6g)TOm[VUhVYB`6U;8!<>_<o<Bfw#3H|]HLTo>KJuy&
oPG1#}n9qjRPrV8c8rsw@j#s;[Ud%<fR<[B#v;R+vdowQ#8Cd6gCR$iJ?miO%nM7PF=ep"RxNtmJ!*&dUQCVCVV23R9dN,%kSxL34+BAc.QQ`A7J&/n^p
#;"=d
$)S9KJ9^*$lFQzn%:?j"%P[~I.t~KU$`g61HQHoG@/EBee.,UVU@2u;FuW(+N0O1Vh%K5.=XqvJ]5cJo`!%mUqG
HPk:F5qz9s=#EsE2X!0RA|ZgPZdin+Wu1J^mU<WJkiW^oe&B"wAgsDj/$q$wPFBD,jr64`a;L1USCF2k)9D846cm0,iTT[$Qd!RqMrM`q&w9C&ttOB@Ma/nG;$TXeb`IGDPF(QQ3Tv=#r_eSy:4jPew*4y*1A1,_1P8Gm/yRNL-7
nFoP|=+a?0xg6<cjOj@XR7A@6#)ktr1dZUeZY,r0HJ>@-F]@4*Ml},Tb}19-h"?ea-#T:BYyY61.+u<%H;X29#QC*_Pm!dH"Heqb[kzAwkAnL8]XbU^2
;Hssca?(=;l5J=qiy|qHovnP:N"4UL_@G;mwJ(taL*hj>;cu[Gk`FiK#;SCbU3sg:PNGy94;tmsyTEtm9#w_V*%f9i!)a_Ijz(q,qny0W5L#XSWd"4;{94?{yKi4H4^dAo,*4$Bt0hn7PL>X6m*JsU@`M33BG8Pt%=u)P$WC_yMWH=^!PtRRyx4ljm:`WnJKS2U;"OU.sEAXZ<fK.CMhNsI[-G*
2>H)Q/gq"C42OTMkrm8x>TPx`lWUd:,cs2:Ks8BJHxa);9F-A3&MM[>d1EgXd|xy[!:s]O;z5sCNE/DzUU=`3)E:_;-NMN(6)ZGpjx=u+7f[X7M?p&rfjdI%M*mZ<3`5*5<8ZzU.MqaR7b+#=LFQb[:GW"Q[h!TBT.6c
)g4DzU>nzDQI>R]
[
N$@ih/}4HP6SB#juA$m+X-vS0L^g`J%WXTB-nCjou%uv;/PWriD+iu<!_NV;O8y1j*w]aV}L(LfeGgLlpDx_6(D
{+d7XsN5{^`=X!v5
OZo``KUP=2f-CcP0htUcMA+UD8TgNDL_x6!"NYL_XJRTK;*bo?*%<`6D<
4jZP>}*T8rc:IO,(EqJ{"sL#X-!{AGZ.+KB)*w;If[$G2[8TaY^C_Obke2?Zxxhl=jB~E?jJahQI,kc_3}-{/7&3(CtmZp!ju_hv3~T<::NK*a^o=Q00"4IkIHSDnYfcaX6Y"}+pP{clo
Z=o%*o1VnJvA5M.s*zNK06/r1~hX)S$imk&{OJ91j/=,aL"(_(kKT[-IGb9":<g+BVr8`"t>&UgQ>A>H02>F4`3Ec&U]I{68.3
B8Rbr-1YMbteLFRT
%w@4Yic4@>D.kXb[^bXtd5`lJXQ{8:^Z9O.SHV$
,f#/
?6;Rr=Z+<mX%CZ~/&CQy@AjB%@32$^7pOxvfI-K
4orvLB+N>3RGf]!3XX:k%
@AcrC*Q!J_n2Kv3v"i!4>u~:"y_jJ>S!y?vWu3*4i1k"ZCYm<*{UiHNcc?55wAf*53usH+J]oxV=<tNeq>5]mSD@D=i;JU`M3K_yJyf35,YkZ<5k?7h1$^a"]nE"51wxIJ?aHstUC"DVU"67E_->B"pjkdN<mGvlp4;?ALj4>ZA7r.E7?3
p}H}@n`bhPS3J#r>3k)ppBVHq..VTumC1WW5-$dcfbF~IuBvGzLu1i&
yPFN81m7ASw`tZ';case"zh":return'-UF5h@Q.w0Gi,hufe#<=@x!XbpOBwLzvId$,p"vDL5|Tyg%!S,@XW-HnVYPT)n-1R:IfyciDRY,LOJOs8__)cg"T*-isp.+,OPaBBWD]1+"=lo6ob)sfCW|6{AtT/ZxD-hc)QbiIQQlRar`j.g`N%51<lVuKlDU/w;SirISFv4>.9Qn3U,9g@PA>(t<eG(Hx#%GCcg,fs3T5Fr]WN@71T-k>*c74L`@cS`Kdajb#(vCvnw)rUI46:y24v(]j}EM4I2
@OIvF]jRVg9DUuUkG?P;B,ZtF%=065*6n{!eK2<}n!0_bB
%>(`jMU(R](dbRu@5Z!k5W:`(HsmgnIhZYxl_an+2$H993CDN@jgrx{(eX
1.lPh|o|gjEsT,L(BZ
iKMZnRKq7PML@INPIO6m`*-P?5m^w>;lBKs0b:<l&gq/%/<]bl{U>
^U_fXoiDw^%QMSND%UN@tktj7Q~:.VSF#<&K2atMwR%^_!by;^0bfR&^1`mp6tjHbJ.px;AYJHXIBZVh^X"^cnB:J1FEFSd+R7I0*hc-r>YJ`>%DsE$Y_,dSBpZ9@Dm077@>V($X0FMMxVhg}
*5xm"Zl5HX`N!?h!Lp:uGFXfXT-Oj<ZDGK64,/(M=a9VDEH+u8VfKHEOL.A2D6a^3hOQd"TB9dYXogc-g$~fz;8f*=Uq)XnKp&C233>yOj<&t0"%9d53KG|WfhIpQ0.E=7S236po#pvfo,:v&BY).Sr7k
;R;XUYJx0wI/BKQrGBY.;!u-v#a(9e9%>.3%0
ogcK`n
XvDKY?vneqK"0^KY`H;2w{4[^1Xhd8ZbEO8Z;(l;[>wom/im9X^Fw_S]Ml+xeGP3&3S`%OmK[dt*;#FG0S5rMJgY<eh
+87?WMi#cS+6Nja)o;3^KXd]biKTd!cqx!pXB!N%7
w,T|K:e$c/yFI3;IuhJ3i|Q8*P^;OUaj/JE6
rVSBG"MOp1Ym:qeJ,JfXU9b%3`&1e#&!%E3"/-b;-yaX@*0Ia2<sxu_rE]]kGyZA4Ms!l^SlAy$6h%9:no?<dn,c:2D,qA>TU^CilL/J4(~2}p#L(CWF(_.CP[l9,[HOB9UKZ]ChZlWNR[Osh4~E$n{lPY1"Yo+mg7i(4K?@c!z6Kr<v+u][7d]Dws4Jt%1b}R?w*Sq1F_$i~oW"1C-)wp/!BG5e8(.JA//^Mv3B5PNh0dK4{:>B;!5u|&R,Dv@j{.&%VN8ZvLuE(%BFzuJirdL!0grK;w$C^tr;*M:w>[H9MWk#P(%&c<*ktP+xL?/hb7F6m=Os^3-YVg~[eH1Xjd^jeM]MLp~p735v7diy1C9pJKQw.<EH~bqt;vL%!i7!T:%W_SJsmvFD7ZER&DlmBiMDUH:Gxoxi^DV*?e5#ZS7fu@sx`W$ICo)u:p6[2e"
m3qH<oH
-/ZIi3DWf&U>`8r,zj=f
,&][[Fez"81oTW"a3@yZhK7T1i`[NID8T%rq?QN,Qq5)I-lmCe3K3[s|4q=J$z-ds!N_)q<{1h^1iU/qiWm+D&6%n+rgV`E8O)=[?KKe-+Ei:(YWr0jEm#^a9=>Q6Ot[KK[23_9axgv!k&2]5C#}Wueb5NAU"|!qrf@T`^iUMywf<Ga[kEyGEZXLTXb[obJWPf#J,*5drK7=29"lpj8IkJ_8f7JR0p7ZoW#"U2$q$fy`tTdiT"q[qR#
)?m_8zr$jSK`c;_~mkh~44r@o7i5GVq98~gm,4<
H~x5GUITukoA.8.n82Vn_Hx6%+tiqO;7nDoH#%*hUO9)Us1K%
OxM!R<#
;O^deE*$Snl4
n>Z
1,%Pldy99mOA3L#!G5IOj[@SqKVS@^s="*G?|#*bE1dNv*t4MjHKe#g*bo8%Mw+V
_b-j-x%M96nm&v:)N3.V;7]#"{eL"$IA^$V*PI<T2@R|`9buH3AB1."i:`X?kv@6y>C,sj4eY8jzRspG_2uW*cl90Ff=>1F6D[F#(::P(kPXsrO78I/t%M)`%t8jm/Ne%(io5~1akn.BNmaKy!6=#-g_:]QcJ^IQB}f|et
#+7cuAFN&?7`LdpJ;gi]z`D3&S6!ZD_gR`+Zyf:%Qu/UQ1Q[xoyZ@AI>NC{hBF0.iO=>zlP^]"6C{0iJU=F4pX!j
o$D!Nu3T).MFV9%+?bj=hhfAs$y~XsU2AI(~2Wf|uF4[3e${1eZR
,*rfLM4R2V7T?jI8m^$>"7G$ybIQG7<A#k7=n:cM>>
l]on!EsG]pt>%C-ZVcniuMdZ#%(}`KFk?Kr4`AVNm]C8yOuU;ROXA`x[/._56NHoEIEiq%j~?AEmdnsm"*tRN)s2n*
o"Y1/5b&+M{2m0.MK>d)^Ehz&8e3khou_l)"Yac;r9x&q>?/:O
:4J".YuSsn6HLN["o_P|IauQVpER7x4bVmExo<IP$Rf5eUAH&`MW-@tA1[B<j~*(wh26@nW8UjDD5ahq]g-/Dm]dn#_5S`bsf>g!a-wNVxea(A4NfK-|w]JaP>i9XA0_mLKPlcYm5G7?M&iZky00N:J3+Cxq9(=sL7A:D%9vO=tSxJB.c9M8D{yIF-><,sbRU2yI<C:YuCJGwDd_p#iRo.HbXgJl/fk|8c&f5zhci`t.he/~d?]8Qrp;-VW~oPL,GaEd4ta%SI^k4@!oSd$6lDnaN~oHGe]Y":R?*jc7.*H$j(0o:H#a,W&yP%>QI/Vfq~)w&i`%0l/Hc^W*2dEW,0?(XDYQ[2SRY->/r-GP4y[BJd=/*h:==ZAn#7H!LP#$?bs$>gcmodoMuA
R[:4p=F0|@zhZe(F2]c*UhB&4C:E~,m_yl!<<Gu,p,kkA[/--hHxxp/]`888.W?lJI!1J9::z4E&0<kN&Inu.
7SwqFFSi7J[Y^/}.U)vP%S#8jkrR[sN#to<D:1@wjw8;btb>?x<w*qOZP]u6/!MpSc1)Zt!d(';case"zh-tw":return'(UF01lMWr1jf3)i[*EPw.sdq,tA9ev(c-(s",Sg/W0]#.%YU7<9-sUB&PU7J-%G3QwbBv&n[3DmB?rb_w(t@]"2$w,F:e+Kw6f(#V;F5eWPH3],H/Ki1(;L@^9mTDqVBMD/4R^:lzZ!wT$2,U;f*@4>?hwoHa,;i0HMICat+UGjO>Rm5},~x5S?]lhEP=hajJc*QE+r1G:=MW:u1b_@h%:ohiC0/:="u%AGCnvR2pbq0k[a*bKpJUaV^qrk21
qmEnz<pWm!vu:rDt!=2g=NU,[%t>kpUR0[0VkAv3&Z7>+<Im"[XilU8%u_f%zH<*;
/!$^&otuUWUMWI`5gw#C[EOD?<Cf/qU2OKNG`vyFzaD`dDoTNnJB>`%7*dUcChmlq[CT;E3tIN6E]/0KXCMhhhZ#PpI8zQ-snu<FZhA1<7Dw[O|K3kfL4
u9*@@5UctKdcZL*@"ER=/+2_SJqm.(aNUy>d<wNYW>4PWW
Zn[bM!eXsfa_dm[ejV
kCQscKl)h3|:s6Gn1yDj>2!RD
5I|VCw3nP&iZ9G(0xfP+EvD0aU.lqe]kC:XIaYom)$I-BNABNw}Ki.VIV^EJH?t?(7l,]!Z`VFU_?v;W|fUJ57FARefEHoAV*5UcWD|uX
.CXaRMPvF,%y
en!Yb]sW2-m5Y^l&bRuHe<,Ae}A
5/u_;>p9=4qxL;Iux"%y^S_/em1?OaIMqkV"f:;IkFy`[2&@LpL9dJObReTU)ff:`1]M(HA-C=h_UB:#&xt<fv?@I@0b4):2v;L`9QkQMdCMt<jYIaLJb@pn8jA6c.YSpi$*kJ;UkFJuc[faJV?Q)8IStT^e"i=<#"EVi]K*uA
bo%ccUxY5.(9K>&;JhFK4o<lwBDA[w8>;g)>FQPch1ExCDSO;BZ0y+<>ufK]pU61bRmX`HCip"VjG`^nIO4z)fSh$2ZI<ctGH2Iu@REh1iHyEh0CBj`:3DO4B]
*D)9y.fpVqZ8)qd>r8X6yE?C^.T%W7+:/CXy8Sq;2a=2kva",8.jV~BfuAtBAs%PSoVwc57}rNg[
<5wxs]h3I**&4Pa1)]]Z-=pxZ1s,@@CY/G%+JVl%Z6h"%/DUk.si|dgVH";Ge[fls4<PSA^#[o&X3B!a8V+p/4}c.%!xo+tR~bdaTL??W:_(1;%Ac;A"8b-r3_f5:g;`|a<<J+RM06Sof?vQ`pvsLY3SE^{O&V0rWa9!-XGLYLxGYD#PXS4
Lo^mbD-oGwC;3.d^wV^R$Uc2<;p;?s9"][yoV7A=&EQ_dl@KZ6JMxq!sse5t=LY^m]jme+vu@53Q(eX
ty~&wY">?7{(iB[m*euinDMMMTYmY1>RyA~xq7=yYg*[K.W;V
TwkLE8rXCl
A:(yB/z!_2jeu)Fa-34GqNp}ht[I:$;R(j-e"cv|)%oej+owGvD``:+RStn
5VW&7JXntcx$
K95#1/5t#g30h-Ok)=.EI>?[I[_,S>2=or!N2`|;|NIVd7jq7;e0.-00[k
4(2M`t"fH>EOS2Ig_r]3
q@7VS&CSby[?@3Wk?tDO:S*B2)]5;JCDk)|Jo*!>8eNYkJ=&P%&5`(c5D/"yE9%on5pae7[;dW4%pe^`Or2$pf{<!a`"P-SaNRmGH81f"HsBy:X`WnO]Obc#hdGx@V@m]^.CBR:+7O+$9([[RN7Ptd8e
]sJE1:ZsNm[e+fANJVOg:RjAz)sDpHG2w7XRY=iXbB+2oK
+PZsu!}KXJkMA%u]j/UwSKxkhPMUmk%K#99j2.
6l).3up_s7DnqTuT%JUj>++"/!V0"/>.CN[qtqxAZ)x9+Z),Q!k69Ir9!cq[+>2y2Zy(ty>U.o3R&#A9w:!4uqTt^7Svm(!W-3pff{)MWz2,vOi"V9##s>DId]5CFg6SCSGs#M#zNl%J%51A#p=ZFP[-.i8~]`*f6PbdY<LPtL9D)1y5](k},A`qZ$Z5ECVsGH#lXKDQ@WvHW~1NS/`z6+f6WzV~OS:FSw[MmTnGZgUN?}I"T!A]y8u,fL%2dT5KU!.gNJ(I26q]SdE17d@$`}F~xFC.$yVqt`F_1eJAM?69ww/hv?F>naWCXtBH0MKw<GS2vSS]U<%r1h;`l3A)a_<Hs%[T8tR<#[B:$=xq94VIMxP>O8z&2.pS]D1c#IcBUl#KRm<4Bqb5IVCRQpnfit_.UsINy;2H8L%&M%S~[d#Wt
I(mSYy%r.I^cqF5Qp,-r%9[%^RQU0(lO`"*mk@S_=l3u/ks_8u*X=b,.dCV8ohT+N(c]D[!=,Z=+qw-vO0w7WpWo7rPbn!8AECwh#pV;fb_3wl0k?U)#6H_hxNG3wZ&=!I5
xU7y*Tivsn(YRBGpt#MWx4?}/z;|/dS$ki0vy{HEf7x~eP^jv
g<c}#SrN9waO)6$clN#ixVBy;,FdL7>QU8pGn@xZ3MHWSyR6Ni+E;_6rtAM`O)_f<;,`%.pw2RdjM~FVahl8S:C91KtD6B;O`Lmlqt^5]HB&kw5[o`aW(s>.-_L$?
i/>q/0?)1/E3;JqZaddCQhp-QAq0a}:jKO1SI11jHS3y<i2vh~",eu.
Q,).#>:-Y]%)Y)gMCb4
RE"|.F82s-Sfn`V%%fSHZZbSJR_Vg,pU,P)>WBoSQ[,Gqn"uX=(b0qC,2zN%Ti3-)I3Y],ZKJp!d(Ey{uo^QX6>m)@$^o*LPI&1d#Df+sn7^
VZ_k{^dI+
Bw`Pw[kNJSanFyByf-"o&h0d0]oqWIy^XUio*jk>+4~!.896gk$5tqJ]c:vie[)0eCxrZ,N0D*pwl!!unLB!^:R%T8(0^u{KHrjw6U_0?X_gS1WtH#med)?0{0!^%5O&mFN@W/DU-NMI"En
}8AlOOva!=
#s!O9H,xq^[-(x`Uy+7"<{qjYyZSVri4QT!T0}P}*}7ki(A#IM[A2rrI?MakA_f35XlBK@XL2Yu(<HQcnc8q-422hmf_Fj%!oVZCP/>*es
e2,(bSpF}#d5B;8HfL01~YH,6u"<o3nFlOKQs[R<C#$Ww^T,@';case"ko":return',Zu1$g~Wb/fR|MDOwg[QEUP6[yse8W4AnT;Lp%T/D^~U}LH;ri}k3Odlf?dft?n3bL*uuv%NCq2M^N!-K8]4+VPlT%LZIyr
SsZm6pz/Ao00O`Ed6?+;hes<5vi]WC_cZ2S6/&?#>m#49bs+[vJ=9k"0Yp#n[vI;
,!lawhxNL>Z:l=;~wH^LA"l[f+>5]"=z0_jepg$PUoC95S^L#r?ibyQI.x+1r&AtO9h&v7l#XTB4Q%XWH3E/7k3Oov)qP1A8/S*G&qoUH=5)&zp"xE11AT/cc+su:YCLprA6XiIs(r!}q%g`e_I;RHFUUcB]:.6l"h^}V1z&]y^y:LqwJH.ybg
.nLPwZ&8]GbMaOq@m5}sLvA.%N8as`vHy&N?7qn&%JE(7;rD8(Mf|E.`ppeBr+aq&c^L#vL!L8/r?]Us"qp/CS7NI5SR>(
q<IwW4gRfCt-
8<@w2yGIG[Ua)3wxN/+&Nrj#>@D[-p*9q*f+0v*TT/bOZh.,g>:""KD1aKZ?<#eSg6Jxn66aOVtdG+$jx=(+Oe&vkl`g{"8_>dD*DY;E-L<Ij@Cenh;J
Tpc}7V>5)j@:4ln^g/TD!.hcD8rChBOxT0UL3RTOe{^to)+[?@98YQO:a.QqZ$?J1IfmI%C<qr>RI$Q|]f;?Z|>Egeo}2vDD]}6luHC(tVC@!d%"/,t&ZAHA#[S@D?[d5-J5EdqyRc^xP[X&8b_GYS7fv(.L)-5f#WM|,jI^S,waVvQIG
c+2pZ.(
.0ua8RYpNfS6UB_},<tJwk@OHrVIdS7zo5pYpTvDpjQu)t
5Vf_b@N<X=XqVog]]NFcQ&zb)E;*&Kf&|"Df4:,#-BJ,H+L)zQ,i?JTR,t(Xyv/,jF8Ly-zg)mx<.&]9H]1("%Y8}:Q&I@=L
pgy7.@Q;X*"]/ByoXvN6,t5.Xu)wX07
qE;64EazU1AZXE9]P(Q;<"C>&-,"XyQEv1
JDO5N>oDjZNu`9{Z%9XWT4hxcebMJ$Rc_yQw?`7LMontoia&m@4M;Dzk]WQNKUV5l+eI,Ac7tWNMj]zK9slJyeCAde+qdyhhBBj.4CGHOqfvfo~6.`9Z_6vLuVt8N$|!Mwu/w1(94vShLm%hA)jf].-^&AXX95/WneI<w
V>I%lYtDU0s(2Ql<A!{*me}:`,j]M14K7Qt*.L2whJf.+>C.>&!(2as@saf)O^+x$ZC%NU&6Ao0YphKUgE51|9%;?IGsw?aSjU+aB;%8P]0i26`RIF75K@R2{%/Qj:K.rEM7sB/m/@5KY?V/Wd?UsdI]Q3M9;$uF]s|2QRaSed3Sg>B?"Xp.E7i4Hsn+zZT9pU-#~*/R$5oo(f@k@1%*=r:1MlKb:sI,rpe^7!8w**M)R0e5`V!Gcm[GMMm,n1Zg9Vc@iD;0GU93t4i"2<|`frxWq1{29mfc[ovyt[PDZ/dXxH=,|m-x4?(Pm!^mKW:AZK;K(6)2qktQ<Fvc2nj8sOwx{v?O|&Z;pg2X*O3j<&)T//kDVu.SrCZw@j~eJ2>fOqS+K8=itpG#3_:twiV55QhXY[E:X667vif$>-D$O][v~Qv1XJ_iTpfllx|+Y[,:hAZwi>NDBg).6$rbG$u8]j3Q5+]?+eB@Bt3&VprjQKCS)]>dWICNfF).+UCTB$LI`qC9y`-h/Cwm;>8)X)#VQN0c`g@E3)P[YovKJ@|]?o(-qv!wGL/Y`/,_q;mi7KZ
>1F4|vPnFOPC>sNn,7&.Mbr<{o*Yy-)<{.W=]:I(DijXbv9wi)7sY??sagDvpJR/[H
i~C_A}9tH=0Q+PqHE2xUK=fn7A:^NQr4&0eNs@6X!/revMi^H*O(Wb>aaqII^ME!j#x_4i5I3~:SpuPmgFG(]Q&pMPP4P[itL-9[ESN?j3D5AC7nH5c!gIR5%]w:v-%cOd"^%1&zG
lZ5>NMi>Kq7}IOZkPGXD)oWA+akLAu>|/<%&Vm>4$3I25?9{)pB+@K(u8k8Y:3rNpp`yWW(F,f?HeSG,Pu@MuXk!Fp2eG&
w^h"d4*r,dmPh!wWn%B"
Ie`>i3_GM^rWr0G94E=:+RL@F
Ogib#S^dt$`l#w+{BlT/Knf^?DQ
rzon*YI[!#5F9BgRX5m7-SfBC8KuA!hSYby2Nc/o#%IJ=bcte$m;bB?!b?$$#QfGy*fGwctFB0Qa4gKTZJGfd-q]"?a,0+JZ%;-M4XNf!A6|t%<}b=*,d)87Djs<GS:%(+eQi}w5QHvRrIHoPmmu%R-nD
=NGl+Ubuy2"jrc/bNMF{TVOdb*A|m?7tyG>j)cig#go**n.vuv;b*$1~]>"ijDItQ0`|F{a*hw3f;PZ$[H<#dAhxp)hcE5q=[wiSrc`u!")/nY:e"#3SS]6D2yBm(/]$GKu1Q@C0&NV4F46r>wn,_-_cK]D>DCd,
Vm.I8e.Hw#:
)/AhN/zRV^L/ZkTEX,ten:WKJMNS&w7t8_bH@&k^]`1gn/Rk*
Z&As:rA!-IA>{XeVpqus_Xf5Nma0bJkkDh|mOJ>>j^26oSq!|tey6D5ly`[+SgEwM3IP40$V`LsVVvSxu*16>olH%mp;hu)mDMP=N?0GGXkZj"q,mc43sSWx>RIxi2C
ZDl279%v2JIjx!wTQy>TvDr<C"XXL&<-c5O=T?yEKoHg?%Z4|.]=rYxKy<h3@__b!uyf/]suEr$_/n^[Bey!
EH@B?N<mhN^ApeZ1M+iP
zQ]w4Xg=It25lC|-3P#9)I;L6z%qt"EtX(c;N#I-!n`sm"
*o#D/%`;y4h;ue^4Rge^R46Q=UF%l"id[P]P$O6<
7?Bm>FYFiBWq~bO`Y6<2@;o&1sk0[F~:H%8%Nd<qsClQ^7;hZX9+"]c4H6V4F6I9/tuVHr6iHF>6
":8Jp:Mx44GQ+%_;5l3GFr4XR@$c-UY-p5wp83>yrfwHLkP<m(jbM$XhvB3S-f0A]}HhHC+5X!QcOAvmZ?aR9wa
p"[WZ/*?H$ii(<K]rtoh9c+yAE(CKm!k#:B
9!*Xj.mu3bx<
"x+bf9S>bTlDB*2Fe19f_+~faL@N?VBPxqy<PkL]i08-iujQIE3
&1#
^xqMhIMZ>nn7=rPNmb2!2nzcKUZP^Mp=HZmfEKvoL3sGu"@iNDkpY%dUZVWfE02[y/++H&dvRDPc,"9)x&TG8Pt,IQ
ugp3WwBkeDoX1JOafLojh2l>;d8"8(@7OR-H%h$/9GyNo)';}return"";}$zl=LANG.crc32(get_compressed(LANG));$yl=$_SESSION["translations"];if(!is_string($yl)||$_SESSION["translations_version"]!=$zl){$yl=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$yl;$_SESSION["translations_version"]=$zl;}Lang::$translations=array();foreach(explode("\n",$yl)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$O,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Zb);abstract
function
query($H,$Jl=false);function
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
dsn($Jc,$V,$F,array$C=array(),$ob='PDO'){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new$ob($Jc,$V,$F,$C);}catch(\Exception$kd){return$kd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$Jl=false){$I=$this->pdo->query($H);$this->error="";if(!$I)return$this->store_error(false);$this->store_result($I);return$I;}private
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
fetch_array($Fg){$J=$this->fetch($Fg);return($J?array_map(array($this,'normalize'),$J):$J);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);if(PHP_VERSION_ID<70100&&is_float($X)&&is_finite($X)){for($Ci=15;$Ci<17;$Ci++){$J=sprintf("%.$Ci"."G",$X);if((float)$J===$X)return$J;}return
sprintf("%.17G",$X);}return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($nh){for($r=0;$r<$nh;$r++)$this->fetch();}}}function
add_driver($s,$B){SqlDriver::$drivers[$s]=$B;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverPorts=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$T,$wk){$Wk=array();foreach($T
as$R=>$xk){if(!$xk["dependent"])$Wk[$R]=array();}foreach(driver()->allFields()as$R=>$l){foreach($l
as$k)$Wk[$R][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Wk).", ".json_encode($wk).")";}static
function
connect($O,$V,$F){if(static::$serverFile)$hi=server_parts(array("path"=>$O));else{$hi=parse_server($O);if(!$hi||($hi["scheme"]&&!in_array($hi["scheme"],static::$serverSchemes))||($hi["socket"]&&!static::$serverSocket)||($hi["path"]&&!static::$serverPath)||(substr($hi["host"],0,1)=="/"&&!static::$serverSocket))return
lang(27);if($hi["port"]!=""&&($hi["port"]>65535||($hi["port"]<1024&&!in_array($hi["port"],static::$serverPorts))))return
lang(28);}$e=new
Db;return($e->attach($hi,$V,$F)?:$e);}static
function
disconnect(){}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($R,array$N,array$Z,array$q,array$D=array(),$y=1,$E=0,$Ii=false){$nf=(count($q)<count($N));$H=adminer()->selectQueryBuild($N,$Z,$q,$D,$y,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$y&&$q&&$nf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$N)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$nf?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):""),$y,($E?$y*$E:0),"\n");$this->query=$H;$tk=microtime(true);$J=$this->conn->query($H,(!$y&&!$Ii?1:0));if($Ii)echo
adminer()->selectQuery($H,$tk,!$J);return$J;}function
delete($R,$Si,$y=0){$H="FROM ".table($R);return
queries("DELETE".($y?limit1($R,$H,$Si):" $H$Si"));}function
update($R,array$P,$Si,$y=0,$Qj="\n"){$km=array();foreach($P
as$w=>$X)$km[]="$w = $X";$H=table($R)." SET$Qj".implode(",$Qj",$km);return
queries("UPDATE".($y?limit1($R,$H,$Si,$Qj):" $H$Si"));}function
insert($R,array$P){return
queries("INSERT INTO ".table($R).($P?" (".implode(", ",array_keys($P)).")\nVALUES (".implode(", ",$P).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$Hi){foreach($L
as$P){$Z=array();foreach($P
as$w=>$X){if(isset($Hi[idf_unescape($w)]))$Z[]="$w = $X";}if(!($Z&&$this->update($R,$P," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$P))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($H,$kl){}function
operators($Lk){return
array();}function
convertSearch($t,array$X,array$k){return$t;}function
value($X,array$k){return(method_exists($this->conn,'value')?$this->conn->value($X,$k):$X);}function
quoteBinary($Bj){return
q($Bj);}function
md5($c,array$k){}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
errorLine($H){return
0;}function
tableHelp($B,$rf=false){}function
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
supportsAlterTable(array$Lk){return
true;}function
indexAlgorithms(array$Lk){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($R){return
array();}function
routineTriggers($B){return
array();}function
fulltextSql($B,array$u,$H,$Ua){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($H).($Ua?" IN BOOLEAN MODE":"").")";}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($R):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$J=array();if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$R=>$U)$J[$R]=array_values(fields($R));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+df93d571")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
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
databases($Md=true){return
get_databases($Md);}function
pluginsLinks(){}function
operators($Lk=null){return
driver()->operators($Lk);}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Qb){return$Qb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$Ee=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Oj=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($Ee!=""?" - $Ee":""),'short_name'=>'Adminer','description'=>lang(29),'start_url'=>$Oj,'scope'=>$Oj,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+df93d571",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Vb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$Fg){$m="adminer$Fg.css";if(file_exists($m)){$Dd=file_get_contents($m);$J["$m?v=".crc32($Dd)]=($Fg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Dd)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(30).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(31).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(32)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(33).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(34).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(35).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(36)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(37))."\n";}function
loginFormField($B,$we,$Y){return$we.$Y."\n";}function
login($Xf,$F){if($F=="")return
lang(38).require_password_link(null);if(!Driver::$passwords)return
lang(39).require_password_link($F);if(!password_required())return
lang(40).require_password_link($F);return
true;}function
tableName(array$Lk){return
h($Lk["Name"]);}function
fieldName(array$k,$D=0){$U=$k["full_type"].($k["null"]?" NULL":"");$xb=$k["comment"];return'<span title="'.h($U.($xb!=""?($U?": ":"").$xb:'')).'">'.h($k["field"]).'</span>';}function
commentValue($U,$xb){if($xb==""||$U=='SCHEMA'||$U=='TABLE'||$U=='COLUMN')return
h($xb);$Bi=function($Bj,$ab='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$ab>",preg_replace('~\|$~m',"",rtrim($Bj))));};$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A)use($Bi){return"<table>\n".($A[1]?"<thead>".$Bi($A[2],'th')."<tbody>\n":$Bi($A[2])).$Bi($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($xb))))."</pre>\n";}function
commentInput($U,$b,$xb){$Y=h($xb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$Lk,$P=""){$B=$Lk["Name"];echo'<p class="links">';$Tf=array();if($B!="")$Tf["select"]=lang(41);if(support("table")||support("indexes"))$Tf["table"]=lang(42);if(support("table")){if(is_view($Lk)){if(support("view"))$Tf["view"]=lang(43);}elseif(function_exists('Adminer\alter_table')&&$B!="")$Tf["create"]=lang(44);}if($P!==null)$Tf["edit"]=lang(45);foreach($Tf
as$w=>$X)echo" <a href='".h(ME)."$w=".url_escape($B).($w=="edit"?$P:"")."'".bold(isset($_GET[$w])).">$X</a>";echo"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Kk){return
array();}function
backwardKeysPrint(array$La,array$K){}function
selectQuery($H,$tk,$xd=false){$J="\n";if(!$xd&&($sm=driver()->warnings())){$s="warnings";$J=", <a href='#$s' class='toggle'>".lang(46)."</a>"."$J<div id='$s' class='hidden'>\n$sm</div>\n";}return"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($tk).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".lang(14)."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
sqlUrlLength(){return
2000;}function
explain(Db$e,$H,array$Ih){$I=explain($e,$H);if(!$I)return"";ob_start();print_select_result($I,$e,$Ih);return
ob_get_clean();}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$Pd){return$L;}function
selectLink($X,array$k){}function
selectVal($X,$z,array$k,$Oh){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$k["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($k)&&!is_utf8($X))$J="<i>".lang(47,strlen($Oh))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$k){return$X;}function
config(){return
array();}function
tableStructurePrint(array$l,$Lk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(48)."<th>".lang(49).(support("comment")?"<th>".lang(50):"")."<tbody>\n";$em=(support("type")?types():array());foreach($l
as$k){echo"<tr><th>".h($k["field"]).doc_link(array(JUSH=>$k["doc"]));$U=h($k["full_type"]);$sb=h($k["collation"]);echo"<td><span title='$sb'>".(in_array($U,$em)?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($sb&&isset($Lk["Collation"])&&$sb!=$Lk["Collation"]?" $sb":""))."</span>",($k["null"]?" <i>NULL</i>":""),($k["auto_increment"]?" <i>".lang(51)."</i>":""),(isset($k["default"])?" <span title='".lang(52)."'>[<b>".($k["generated"]||$k["default_sql"]?"<code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($k["default"])),80,"</code>"):h($k["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$k["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$Lk){$Zh=false;foreach($v
as$B=>$u)$Zh|=!!$u["partial"];echo"<table>\n";$fc=first(driver()->indexAlgorithms($Lk));foreach($v
as$B=>$u){ksort($u["columns"]);$Ii=array();foreach($u["columns"]as$w=>$X)$Ii[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($u["type"]).($fc&&$u['algorithm']!=$fc?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$Ii).($u["invalid"]?" <span class='error'>INVALID</span>":"");if($Zh)echo"<td>".($u['partial']?"<code class='sql jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($U){if($U=="FOREIGN"||$U=="CHECK")return"";if($U=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnInput($b,array$d,$Y="",$ri=""){return
select_input($b,$d,$Y,$ri);}function
selectColumnsPrint(array$N,array$d){print_fieldset("select",lang(53),$N);$r=0;$N[""]=array();foreach($N
as$w=>$X){$X=idx($_GET["columns"],$w,array());$c=adminer()->selectColumnInput(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(54)=>driver()->functions,lang(55)=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$v,$Lk=null){print_fieldset("search",lang(56),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$_h=adminer()->operators($Lk);foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||(("$X[col]$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$_h)))echo"<div>".adminer()->selectColumnInput(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(57).")"),html_select("where[$r][op]",$_h,$X["op"]," data-default='".h(first($_h))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$d,array$v){print_fieldset("sort",lang(58),$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".adminer()->selectColumnInput(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(59))."</div>\n";$r++;}}echo"<div>".adminer()->selectColumnInput(" name='order[$r]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$r]",1,false,lang(59))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".lang(60)."</legend><div>","<input type='number' name='limit' class='size' value='".h($y?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($hl){echo"<fieldset><legend>".lang(61)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($hl)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(62)."</legend><div>","<input type='submit' value='".lang(53)."'>"," <span id='noindex' title='".lang(63)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($v
as$u){$Ub=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Ub)$d[$Ub]=1;}$d[""]=1;foreach($d
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Sc,array$d){}function
selectColumnsProcess(array$d,array$v){$N=array();$q=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$N[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$N[$w];}}return
array($N,$q);}function
selectSearchProcess(array$l,array$v,$Lk=null){$J=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$J[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$_h=adminer()->operators($Lk);foreach((array)$_GET["where"]as$w=>$X){$X+=array("col"=>"","op"=>first($_h),"val"=>"");$_GET["where"][$w]=$X;$qb=$X["col"];if(("$qb$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$_h)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Ab=array();foreach(($qb!=""?array($qb=>idx($l,$qb,array())):$l)as$B=>$k){if($qb!=""||is_searchable($k,$X))$Ab[]=adminer()->selectSearchCondition(driver()->convertSearch(idf_escape($B),$X,$k),$X["op"],$X["val"]);}$J[]=(count($Ab)==1?$Ab[0]:($Ab?"(".implode(" OR ",$Ab).")":"1 = 0"));}}return$J;}function
selectSearchCondition($c,$wh,$X){if(preg_match('~IN$~',$wh))return"$c $wh ".($X!=""?process_in($X):"(NULL)");if($wh=="BETWEEN")return"$c $wh ".process_between($X);if($wh=="SQL")return"$c $X";if(preg_match('~^(I?LIKE) %%$~',$wh,$A))return"$c $A[1] ".q("%$X%");if($wh=="FIND_IN_SET")return"$wh(".q($X).", $c)";return"$c $wh".(!preg_match('~NULL$~',$wh)?" ".q($X):"");}function
selectOrderProcess(array$l,array$v){$J=array();foreach((array)$_GET["order"]as$w=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($l[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Pd){return
false;}function
selectQueryBuild(array$N,array$Z,array$q,array$D,$y,$E){return"";}function
messageQuery($H,$jl,$xd=false){restart_session();$Be=&get_session("queries");if(!idx($Be,$_GET["db"]))$Be[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$Be[$_GET["db"]][]=array($H,time(),$jl);$pk="sql-".count($Be[$_GET["db"]]);$J="<a href='#$pk' class='toggle'>".lang(64)."</a> ".copy_icon()."\n";if(!$xd&&($sm=driver()->warnings())){$s="warnings-".count($Be[$_GET["db"]]);$J="<a href='#$s' class='toggle'>".lang(46)."</a>, $J<div id='$s' class='hidden'>\n$sm</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$pk' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($jl?" <span class='time'>($jl)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($Be[$_GET["db"]])-1)).'">'.lang(14).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$l,$K,$Sl,$H='',$jl=''){echo($H!=""?"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>($jl)</span>\n":"");}function
editFunctions(array$k){$J=($k["null"]?"NULL/":"");$se=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$ae){if(!$w||(!isset($_GET["call"])&&$se)){foreach($ae
as$ni=>$X){if(!$ni||preg_match("~$ni~",$k["type"]))$J
.="/$X";}}if($w&&$ae&&!preg_match('~set|bool~',$k["type"])&&!is_blob($k))$J
.="/SQL";}if($k["auto_increment"]&&!$se)$J=lang(51);return
explode("/",$J);}function
editInput($R,array$k,$b,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$b,$k,$Y,"NULL");return"";}function
editHint($R,array$k,$Y){return"";}function
processInput(array$k,$Y,$p=""){if($p=="SQL")return$Y;$B=$k["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$J="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$J=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$J=idf_escape($B)." $p $J";elseif(preg_match('~^[+-] interval$~',$p))$J=idf_escape($B)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$J="$p(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$J="$p($J)";return
unconvert_field($k,$J);}function
dumpOutput(){$J=array('text'=>lang(65),'file'=>lang(66));if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($R,$Bk,$rf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Bk)dump_csv(array_keys(fields($R)));}else{if($rf==2){$l=array();foreach(fields($R)as$B=>$k)$l[]=idf_escape($B)." ".full_type_sql($k);$Mb="CREATE TABLE ".table($R)." (".implode(", ",$l).")";}else$Mb=create_sql($R,$_POST["auto_increment"],$Bk);set_utf8mb4($Mb);if($Bk&&$Mb){if(($Bk=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$rf==1)echo"DROP ".($rf==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($rf==1)$Mb=remove_definer($Mb);echo"$Mb;\n\n";}}}function
dumpData($R,$Bk,$H,array$N=array(),array$Z=array(),array$q=array(),array$D=array()){if($Bk){$hg=(JUSH=="sqlite"?0:1048576);$l=array();$Je=false;if($_POST["format"]=="sql"){if($Bk=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$l=fields($R);if(JUSH=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Je=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($R,($N?:array("*")),$Z,$q,$D,0));if($I){$cf="";$Wa="";$yf=array();$be=array();$Dk="";$_d=($R!=''?'fetch_assoc':'fetch_row');$Lb=0;while($K=$I->$_d()){if(!$yf){$km=array();foreach($K
as$X){$k=$I->fetch_field();if(idx($l[$k->name],'generated')){$be[$k->name]=true;continue;}$yf[]=$k->name;$w=idf_escape($k->name);$km[]="$w = VALUES($w)";}$Dk=($Bk=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$km):"").";\n";}foreach($K
as$w=>$X){if(is_array($X))$K[$w]=json_encode($X,256|64);}if($_POST["format"]!="sql"){if($Bk=="table"){dump_csv($yf);$Bk="INSERT";}dump_csv($K);}else{if(!$cf)$cf="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$yf)).") VALUES";foreach($K
as$w=>$X){if($be[$w]){unset($K[$w]);continue;}$k=$l[$w];$K[$w]=($X===null?"NULL":($X===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($X)?$X:(!is_blob($k)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$Bj=($hg?"\n":" ")."(".implode(",\t",$K).")";if(!$Wa)$Wa=$cf.$Bj;elseif(JUSH=='mssql'?$Lb%1000!=0:strlen($Wa)+4+strlen($Bj)+strlen($Dk)<$hg)$Wa
.=",$Bj";else{echo$Wa.$Dk;$Wa=$cf.$Bj;}}$Lb++;}if($Wa)echo$Wa.$Dk;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";else
dump_csv(array(connection()->error));if($Je)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Ie){return
friendly_url($Ie!=""?$Ie:(SERVER?:"localhost"));}function
dumpHeaders($Ie,$Lg=false){$Rh=$_POST["output"];$sd=(preg_match('~sql~',$_POST["format"])?"sql":($Lg?"tar":"csv"));header("Content-Type: ".($Rh=="gz"?"application/x-gzip":($sd=="tar"?"application/x-tar":($sd=="sql"||$Rh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Rh=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$sd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(67)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(68):lang(69))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(70)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(71)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(72)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(73)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(74)."</a>\n":"");return
true;}function
navigation($Eg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$ah=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$ah)<0?h($ah):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Eg=="auth"){$Rh="";foreach((array)$_SESSION["pwds"]as$mm=>$Xj){foreach($Xj
as$O=>$fm){$B=h(get_setting("vendor-$mm-$O")?:get_driver($mm));foreach($fm
as$V=>$F){if($B&&$F!==null){$dc=$_SESSION["db"][$mm][$O][$V];foreach(($dc?array_keys($dc):array(""))as$h)$Rh
.="<li><a href='".h(auth_url($mm,$O,$V,$h))."'>($B) ".h("$V@").($O!=""?adminer()->serverName($O):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($Rh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Rh</ul>\n";}else{$T=array();if($_GET["ns"]!==""&&!$Eg&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Eg);$ia=array();if(DB==""||!$Eg){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(64)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(75)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(76)."</a>";}$Pe=$_GET["ns"]!==""&&!$Eg&&DB!="";if($Pe&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(77)."</a>";$ia=adminer()->menuActions($ia,$Eg);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Pe){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(13)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.2.0+df93d571",true);$Hg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Hg?script("addEventListener('DOMContentLoaded', () => {\n$Hg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$Tf=array();foreach($T
as$R=>$U)$Tf[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Tf).')(?!\$)\b/g',false);$rk=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($rk))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$wk=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Ha=Driver::jushAutocomplete($T,$wk);echo($Ha?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ha; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($Eg){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$ac=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(35)."'>".lang(78).": ".($g?html_select("db",array(""=>"")+group_system($g),DB,$ac):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(25)."'".($g?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$Eg){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$xk){$R="$R";$B=adminer()->tableName($xk);if($B!=""&&!$xk["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".lang(41)."'>".lang(79)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($xk)?"view":"structure"))." title='".lang(42)."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}function
formatSizeValue($X){return
format_number($X);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$disabled=array();var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($vi){$Fc=SqlDriver::$drivers;$ye=" href='https://www.adminer.org/plugins/#use'".target_blank();if($vi===null){$vi=array();$Pa="adminer-plugins";if(is_dir($Pa)){foreach(glob("$Pa/*.php")as$m){$Ed=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$Ed)as$s=>$B)$this->driverFiles[$s]=$m;}}if(file_exists("$Pa.php")){$Re=$this->includeOnce("$Pa.php");if(is_array($Re)){foreach($Re
as$w=>$si)$vi[is_object($si)?get_class($si):$w]=$si;}else$this->error
.=lang(80,"<b>$Pa.php</b>",$ye)."<br>";}foreach(get_declared_classes()as$ob){if(!$vi[$ob]&&(preg_match('~^Adminer\w~i',$ob)||is_subclass_of($ob,'Adminer\Plugin'))){$ej=new
\ReflectionClass($ob);$Db=$ej->getConstructor();if($Db&&$Db->getNumberOfRequiredParameters())$this->error
.=lang(81,$ye,"<b>$ob</b>","<b>$Pa.php</b>")."<br>";else$vi[$ob]=new$ob;}}}$hf=array_filter($vi,function($si){return!is_object($si);});if($hf){$this->error
.=lang(82,$ye)."<br>";$vi=array_diff_key($vi,$hf);}$Hk=get_setting("plugins","adminer_config");foreach($vi
as$w=>$si){if($si
instanceof
Plugin&&$si::$optional){$uk=idx($Hk,get_class($si));if($uk!==null?!$uk:!$si->enabled){$this->disabled[]=$si;unset($vi[$w]);}}}$this->drivers=array_diff_key(SqlDriver::$drivers,$Fc);$this->plugins=$vi;$la=new
Adminer;$vi[]=$la;$ej=new
\ReflectionObject($la);foreach($ej->getMethods()as$Bg){foreach($vi
as$si){$B=$Bg->getName();if(method_exists($si,$B))$this->hooks[$B][]=$si;}}}function
includeOnce($m){return
include_once"./$m";}static
function
describe($si){$oc=(method_exists($si,'description')?$si->description():"");if(!$oc){$ej=new
\ReflectionObject($si);if(preg_match('~^/[\s*]+(.+?)(?:\s*\*/)?$~m',$ej->getDocComment(),$A))$oc=$A[1];}$Gj=(method_exists($si,'screenshot')?$si->screenshot():"");return
h($oc).($Gj?($oc?" ":"")."(<a href='".h($Gj)."'".target_blank().">".lang(83)."</a>)":"");}static
function
checksum($m){$Dd=str_replace("\r","",file_get_contents($m));$Dd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Dd);return
dechex(crc32($Dd));}function
checksums(){$Fd=array_values($this->driverFiles);foreach($this->plugins
as$si){$ej=new
\ReflectionObject($si);$Fd[]=$ej->getFileName();}$J=array();foreach($Fd
as$m)$J[basename($m,'.php')]=self::checksum($m);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'1c017d59','before-unload'=>'9674d220','config'=>'bdac2201','dark-switcher'=>'8c201319','database-hide'=>'e304a899','designs'=>'5903cccc','dump-alter'=>'896b579e','dump-bz2'=>'c9229da6','dump-date'=>'512aad63','dump-json'=>'f8aa6b99','dump-xml'=>'aedd2d3a','dump-zip'=>'735d7e2','edit-foreign'=>'d04a0ac8','edit-textarea'=>'a178d7d9','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'e9005cb0','file-upload'=>'10add0e8','foreign-system'=>'634ce119','frames'=>'b0e1d11a','highlight-codemirror'=>'ada4b569','highlight-monaco'=>'ec1da606','highlight-prism'=>'19933b19','import-csv'=>'bcecfad2','login-ip'=>'4d174fea','login-otp'=>'66bd013d','login-passkey'=>'89783b50','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'55b9b15c','name-patterns'=>'84c10d09','remote-color'=>'267580a3','row-numbers'=>'1ad1b6cf','select-email'=>'c33a2661','select-expression'=>'b6a4b3f1','select-foreign'=>'577c07b2','select-image'=>'39ed1607','slugify'=>'7b422f0e','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'e0b1433','table-structure'=>'f71ec61b','tables-filter'=>'2729bf7','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'51a3d7d5','elastic'=>'9b83a12a','firebird'=>'5f3d9493','igdb'=>'3b00190b','imap'=>'3ba195cd','mongo'=>'235e3ba','redis'=>'41709f61','simpledb'=>'b8e2cc7d',);}function
__call($B,array$Wh){$_a=array();foreach($Wh
as$w=>$X)$_a[]=&$Wh[$w];$J=null;foreach($this->hooks[$B]as$si){$Y=call_user_func_array(array($si,$B),$_a);if($Y!==null){if(!self::$append[$B])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{static$optional=false;var$enabled=true;protected$translations=array();function
disable(){$this->enabled=false;return$this;}function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$gh=null){$_a=func_get_args();$_a[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$_a);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($ji){$this->password_hash=$ji;}function
description(){return
lang(84);}function
credentials(){$F=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F));}function
login($Xf,$F){if($this->passwordMatches($F))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$O,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);$wi=$O["port"];$Uc=("$O[host]$wi$O[socket]"=="");$sk=adminer()->connectSsl();$cm=($sk&&($sk['key']||$sk['cert']||$sk['ca']||isset($sk['verify'])));if($cm)$this->ssl_set($sk['key'],$sk['cert'],$sk['ca'],'','');$J=@$this->real_connect((!$Uc?$O["host"]:ini_get("mysqli.default_host")),(!$Uc||$V!=""?$V:ini_get("mysqli.default_user")),(!$Uc||$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,($wi!=""?intval($wi):ini_get("mysqli.default_port")),($wi!=""?null:$O["socket"]),($cm?($sk['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($eb){if(parent::set_charset($eb))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $eb");}function
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
lang(85,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$wi="$O[port]$O[socket]";$B=$O["host"].($wi!=""?":$wi":"");$this->link=@mysql_connect(($B!=""?$B:ini_get("mysql.default_host")),($B.$V!=""?$V:ini_get("mysql.default_user")),($B.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($eb){return
mysql_set_charset($eb,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Zb){return
mysql_select_db($Zb,$this->link);}function
query($H,$Jl=false){$I=@($Jl?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
attach(array$O,$V,$F){$C=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$C[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$sk=adminer()->connectSsl();if($sk){if($sk['key'])$C[\PDO::MYSQL_ATTR_SSL_KEY]=$sk['key'];if($sk['cert'])$C[\PDO::MYSQL_ATTR_SSL_CERT]=$sk['cert'];if($sk['ca'])$C[\PDO::MYSQL_ATTR_SSL_CA]=$sk['ca'];if(isset($sk['verify']))$C[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$sk['verify'];}$Ee=$O["host"];$wi=$O["port"];$gk=$O["socket"];return$this->dsn("mysql:charset=utf8".($Ee!=""?";host=$Ee":'').($wi!=""?";port=$wi":($gk!=""?";unix_socket=$gk":"")),$V,$F,$C);}function
set_charset($eb){return$this->query("SET NAMES $eb");}function
select_db($Zb){return$this->query("USE ".idf_escape($Zb));}function
query($H,$Jl=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Jl);return
parent::query($H,$Jl);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($Lk){return
array("=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($O,$V,$F){$e=parent::connect($O,$V,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($Bj=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$Bj;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array(lang(86)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(87)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(88)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(89)=>array("enum"=>65535,"set"=>64),lang(90)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(91)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types[lang(88)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(88)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(92)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(92)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(86)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$P){return($P?parent::insert($R,$P):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$Hi){$d=array_keys(reset($L));$Di="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$km=array();foreach($d
as$w)$km[$w]="$w = VALUES($w)";$Dk="\nON DUPLICATE KEY UPDATE ".implode(", ",$km);$km=array();$x=0;foreach($L
as$P){$Y="(".implode(", ",$P).")";if($km&&(strlen($Di)+$x+strlen($Y)+strlen($Dk)>1e6)){if(!queries($Di.implode(",\n",$km).$Dk))return
false;$km=array();$x=0;}$km[]=$Y;$x+=strlen($Y)+2;}return
queries($Di.implode(",\n",$km).$Dk);}function
slowQuery($H,$kl){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$kl FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($kl*1000).") */ $A[2]";}}function
convertColumn($t,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($t)";if($k["type"]=="bit")return"BIN($t + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($t)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($t)";return"";}function
convertSearch($t,array$X,array$k){return($this->convertColumn($t,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t));}function
typeName(\stdClass$k){$B=parent::typeName($k);if($B!=""){$Il=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($Il,$B,strtolower($B));}$Il=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$J=idx($Il,$k->type,"");return($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$J):$J);}function
quoteBinary($Bj){return"X".q(bin2hex($Bj));}function
md5($c,array$k){if(is_blob($k)||preg_match('~'.text_type().'~',$k["type"]))return"MD5(".(is_blob($k)||preg_match("~^utf8~",$k["collation"])?$c:"CONVERT($c USING ".charset($this->conn).")").")";}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
errorLine($H){return(preg_match('~ at line (\d+)$~',$this->conn->error,$A)?(int)$A[1]:0);}function
tableHelp($B,$rf=false){$Zf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Zf?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="sys")return($Zf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$B)).".html"));if(DB=="mysql")return($Zf?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($R){$Vd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Vd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$K=($I?$I->fetch_row():null);if(!$K)return
array();$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$K;$fi=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Vd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($fi);$J["partition_values"]=array_values($fi);return$J;}function
checkConstraints($R){$J=parent::checkConstraints($R);return($this->conn->flavor=='maria'?$J:array_map('stripslashes',$J));}function
hasCStyleEscapes(){static$Xa;if($Xa===null){$qk=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Xa=(strpos($qk,'NO_BACKSLASH_ESCAPES')===false);}return$Xa;}function
hasEstimatedRows(){return
true;}function
isSystem($h,$M=""){return
information_schema($h,$M)||in_array($h,array("mysql","sys"));}function
lineComment(){return"#|-- ";}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$Lk){return(preg_match('~^(MEMORY|NDB)$~',$Lk["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($Md){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$tk=microtime(true);$J=($Md?slow_query($H):get_vals($H));if(microtime(true)-$tk>0.1){restart_session();set_session("dbs",$J);stop_session();}}return$J;}function
limit($H,$Z,$y,$nh=0,$Qj=" "){return" $H$Z".($y?$Qj."LIMIT $y".($nh?" OFFSET $nh":""):"");}function
limit1($R,$H,$Z,$Qj="\n"){return
limit($H,$Z,1,0,$Qj);}function
db_collation($h,array$tb){$J=null;$Mb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$Mb,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Mb,$A))$J=$tb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$g){$J=array();foreach($g
as$h)$J[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$J;}function
table_status($B="",$yd=false){$J=array();$H="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name");$M=array();foreach(($yd?array():get_rows($H))as$K)$M[$K["Name"]]=$K;$Gi=null;foreach(get_rows($yd?$H:"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$K){$Oh=idx($M,$K["Name"]);if($Oh){if($K["Comment"]!==$Oh["Comment"]&&$K["Comment"]!==$Gi)$K["Error"]=$K["Comment"];$Gi=$K["Comment"];$K["Comment"]=$Oh["Comment"];$K["Engine"]=$Oh["Engine"];}if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($B!="")$K["Name"]=$B;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($Xd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Xd,$A);return
array($A[1],$A[2],ltrim($A[3].$A[4]));}function
fields($R){$Zf=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$k=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$ce=$K["GENERATION_EXPRESSION"];$vd=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$vd,$be);list($Hl,$x,$Ql)=parse_type($U);$i=$K["COLUMN_DEFAULT"];$hc=false;if($i!=""){$Vi=$Zf;if($Zf)$hc=!preg_match("~^('(?:[^'\\\\]|\\\\.|'')*'|NULL|[-+]?[\\d.]+)$~s",$i);elseif(preg_match('~text|json~',$Hl)||preg_match('~\bDEFAULT_GENERATED\b~',$vd)){$i=stripslashes($i);$Vi=preg_match("~^(_\\w+)?('(?:[^'\\\\]|\\\\.|'')*')$~s",$i,$A);if($Vi)$i=$A[2];else$hc=true;}elseif(preg_match("~^[bx]'[\\da-f]*'$~i",$i))$hc=true;if($Vi)$i=($i=="NULL"?null:(preg_match("~^'.*'$~s",$i)?stripslashes(idf_unescape($i)):$i));if(preg_match('~binary~',$Hl)&&preg_match("~^(?:0x(\\w*)|x'(\\w*)')$~",$i,$A)){$i=pack("H*",$A[1].idx($A,2));$hc=false;}}$J[$k]=array("field"=>$k,"full_type"=>$U,"type"=>$Hl,"length"=>$x,"unsigned"=>$Ql,"default"=>($be?($Zf?$ce:stripslashes($ce)):$i),"default_sql"=>(!$be&&$hc),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($vd=="auto_increment"),"on_update"=>(preg_match('~\bon update current_timestamp\b~i',$vd)?"CURRENT_TIMESTAMP":""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($be[1]=="PERSISTENT"?"STORED":$be[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$B=$K["Key_name"];$J[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$B]["columns"][]=$K["Column_name"];$J[$B]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$B]["descs"][]=null;$J[$B]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$ni='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Nb=get_val("SHOW CREATE TABLE ".table($R),1);if($Nb){preg_match_all("~CONSTRAINT ($ni) FOREIGN KEY ?\\(((?:$ni,? ?)+)\\) REFERENCES ($ni)(?:\\.($ni))? \\(((?:$ni,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Nb,$bg,PREG_SET_ORDER);foreach($bg
as$A){preg_match_all("~$ni~",$A[2],$kk);preg_match_all("~$ni~",$A[5],$al);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$kk[0]),"target"=>array_map('Adminer\idf_unescape',$al[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$w=>$X)sort($J[$w]);return$J;}function
information_schema($h,$M=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$sb){return
queries("CREATE DATABASE ".idf_escape($h).($sb?" COLLATE ".q($sb):""));}function
drop_databases(array$g){$J=apply_queries("DROP DATABASE",$g,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($B,$sb){$J=false;if(create_database($B,$sb)){$T=array();$pm=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$pm[]=$R;else$T[]=$R;}$J=(!$T&&!$pm)||move_tables($T,$pm,$B);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Ga=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$Ga="";break;}if($u["type"]=="PRIMARY")$Ga=" UNIQUE";}}return" AUTO_INCREMENT$Ga";}function
alter_table($R,$B,array$l,array$Od,$xb,$Xc,$sb,$Fa,$ei){$va=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$va[]=($R!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($R!=""?$k[2]:"");}else$va[]="DROP ".idf_escape($k[0]);}$va=array_merge($va,$Od);$xk=($xb!==null?" COMMENT=".q($xb):"").($Xc?" ENGINE=".q($Xc):"").($sb?" COLLATE ".q($sb):"").($Fa!=""?" AUTO_INCREMENT=$Fa":"");if($ei){$fi=array();if($ei["partition_by"]=='RANGE'||$ei["partition_by"]=='LIST'){foreach($ei["partition_names"]as$w=>$X){$Y=$ei["partition_values"][$w];$fi[]="\n  PARTITION ".idf_escape($X)." VALUES ".($ei["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$xk
.="\nPARTITION BY $ei[partition_by]($ei[partition])";if($fi)$xk
.=" (".implode(",",$fi)."\n)";elseif($ei["partitions"])$xk
.=" PARTITIONS ".(+$ei["partitions"]);}elseif($ei===null)$xk
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$va)."\n)$xk");if($R!=$B)$va[]="RENAME TO ".table($B);if($xk)$va[]=ltrim($xk);return($va?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$va)):true);}function
alter_indexes($R,$va){$cb=array();foreach($va
as$X)$cb[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$cb));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$pm){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$pm)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$pm,$al){$jj=array();foreach($T
as$R)$jj[]=table($R)." TO ".idf_escape($al).".".table($R);if(!$jj||queries("RENAME TABLE ".implode(", ",$jj))){$lc=array();foreach($pm
as$R)$lc[table($R)]=view($R);connection()->select_db($al);$h=idf_escape(DB);foreach($lc
as$B=>$om){if(!queries("CREATE VIEW $B AS ".str_replace(" $h."," ",$om["select"]))||!queries("DROP VIEW $h.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$pm,$al){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$B=($al==DB?table("copy_$R"):idf_escape($al).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($R))||!queries("INSERT INTO $B SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$_l=$K["Trigger"];list($hd,$ih)=trigger_event($K);if(!queries("CREATE TRIGGER ".($al==DB?idf_escape("copy_$_l"):idf_escape($al).".".idf_escape($_l))." $K[Timing] $hd".($ih!=""?" $ih":"")." ON $B FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($pm
as$R){$B=($al==DB?table("copy_$R"):idf_escape($al).".".table($R));$om=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $om[select]"))return
false;}return
true;}function
trigger_event(array$K){$jd=explode(",",$K["Event"]);$J=array();foreach(array("DELETE","INSERT","UPDATE")as$hd){if(in_array($hd,$jd))$J[]=$hd;}$J=implode(" OR ",$J);if(in_array("UPDATE",$jd)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($K["Trigger"]),2),$A)&&preg_match('~\bOF\s+(.+)~is',$A[1],$ih))return
array("$J OF",$ih[1]);return
array($J,"");}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));$J=reset($L);if($J)list($J["Event"],$J["Of"])=trigger_event($J);return($J?:array());}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){list($hd)=trigger_event($K);$J[$K["Trigger"]]=array($K["Timing"],$hd);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$U){$L=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($B)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($L
as$K){$Xd=$K["DTD_IDENTIFIER"];list($Hl,$x,$Ql)=parse_type($Xd);$l[]=array("field"=>$K["PARAMETER_NAME"],"type"=>$Hl,"length"=>$x,"unsigned"=>$Ql,"null"=>true,"full_type"=>$Xd,"inout"=>($U=="FUNCTION"?"":$K["PARAMETER_MODE"]),"collation"=>$K["COLLATION_NAME"],);}$J=connection()->query("SELECT
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
routine_options($uj){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($B,array$K){return
idf_escape($B);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Fa,$Bk){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$Fa)$J=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Zb,$Bk=""){$B=idf_escape($Zb);$J="";if(preg_match('~CREATE~',$Bk)&&($Mb=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($Mb);if($Bk=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
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
unconvert_field(array$k,$J){if(preg_match("~binary~",$k["type"]))$J="UNHEX($J)";if($k["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($k["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$Di=(min_version(8)?"ST_":"");$J=$Di."GeomFromText($J, $Di"."SRID($k[field]))";}return$J;}function
support($zd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$zd);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($ud=false){return
array();}function
type_values($s){return"";}function
type_definition($s){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($M,$f=null){return
true;}function
schema_comment($M){return"";}function
set_schema_comment($M,$xb){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($ml,$j="",$Va=array(),$nl="",$dh=false,$Bc=""){if($dh){header("HTTP/1.1 404 Not Found");$j=($j?:lang(93));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$ol=$ml.($nl!=""?": $nl":"");$pl=strip_tags($ol.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(94),'\' class=\'',lang(94),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$pl,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.2.0+df93d571"),'">
';$Rb=adminer()->css();if(is_int(key($Rb)))$Rb=array_fill_keys($Rb,'light');$qe=in_array('light',$Rb)||in_array('',$Rb);$oe=in_array('dark',$Rb)||in_array('',$Rb);$Vb=($qe?($oe?null:false):($oe?:null));$qg=" media='(prefers-color-scheme: dark)'";if($Vb!==false)echo"<link rel='stylesheet'".($Vb?"":$qg)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.2.0+df93d571")."'>\n";echo"<meta name='color-scheme' content='".($Vb===null?"light dark":($Vb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.2.0+df93d571");if(adminer()->head($Vb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.2.0+df93d571")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Rb
as$Xl=>$Fg){$b=($Fg=='dark'&&!$Vb?$qg:($Fg=='light'&&$oe?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Xl)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(95))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(96)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Va!==null){$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$O=adminer()->serverName(SERVER);$O=($O!=""?$O:lang(31));if($Va===false)echo"$O\n";else{echo"<a href='".h($z.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$O</a> » ";$Jj="";if(is_string($Va)){$Jj=$Va;$Va=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Va))){$bc="$z&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($bc.($_GET["ns"]==""?$Jj:"")).'">'.h(DB).'</a> » ';}if(is_array($Va)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Jj).'">'.h($_GET["ns"]).'</a> » ';foreach($Va
as$w=>$X){$nc=(is_array($X)?$X[1]:h($X));if($nc!="")echo"<a href='".h(ME."$w=").url_escape(is_array($X)?$X[0]:$X)."'>$nc</a> » ";}}echo"$ml\n";}}echo"<h2>$ol$Bc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($dh){page_footer($dh===true?"":$dh);exit;}}function
service_worker(){$hj=has_passwords();$pb=($hj?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.2.0+df93d571")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$pb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$Xj){foreach($Xj
as$fm){foreach($fm
as$F){if($F!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Qb){$ue=array();foreach($Qb
as$w=>$X)$ue[]="$w $X";header("Content-Security-Policy: ".implode("; ",$ue));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$dm=array();foreach(array_keys(adminer()->css())as$Xl)$dm[preg_replace('~\?.*~','',$Xl)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($dm[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$A);$J[$m]=array((string)$A[1],Plugins::checksum($m));}}return$J;}function
official_design_checksums(){return
array('adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'c9d1d11c','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'1b8ac749','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$ch;if(!$ch)$ch=base64_encode(rand_string());return$ch;}function
page_messages($j){$Wl=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$xg=idx($_SESSION["messages"],$Wl);if($xg){echo"<div class='message'>".implode("</div>\n<div class='message'>",$xg)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Wl]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Eg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Eg);echo"</div>\n";if($Eg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(33),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(97),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Ng){while($Ng>=2147483648)$Ng-=4294967296;while($Ng<=-2147483649)$Ng+=4294967296;return(int)$Ng;}function
long2str(array$W,$rm){$Bj='';foreach($W
as$X)$Bj
.=pack('V',$X);if($rm)return
substr($Bj,0,end($W));return$Bj;}function
str2long($Bj,$rm){$W=array_values(unpack('V*',str_pad($Bj,4*ceil(strlen($Bj)/4),"\0")));if($rm)$W[]=strlen($Bj);return$W;}function
xxtea_mx($Am,$_m,$Ek,$wf){return
int32((($Am>>5&0x7FFFFFF)^$_m<<2)+(($_m>>3&0x1FFFFFFF)^$Am<<4))^int32(($Ek^$_m)+($wf^$Am));}function
encrypt_string($zk,$w){if($zk=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($zk,true);$Ng=count($W)-1;$Am=$W[$Ng];$_m=$W[0];$Pi=floor(6+52/($Ng+1));$Ek=0;while($Pi-->0){$Ek=int32($Ek+0x9E3779B9);$Kc=$Ek>>2&3;for($Sh=0;$Sh<$Ng;$Sh++){$_m=$W[$Sh+1];$Mg=xxtea_mx($Am,$_m,$Ek,$w[$Sh&3^$Kc]);$Am=int32($W[$Sh]+$Mg);$W[$Sh]=$Am;}$_m=$W[0];$Mg=xxtea_mx($Am,$_m,$Ek,$w[$Sh&3^$Kc]);$Am=int32($W[$Ng]+$Mg);$W[$Ng]=$Am;}return
long2str($W,false);}function
decrypt_string($zk,$w){if($zk=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($zk,false);$Ng=count($W)-1;$Am=$W[$Ng];$_m=$W[0];$Pi=floor(6+52/($Ng+1));$Ek=int32($Pi*0x9E3779B9);while($Ek){$Kc=$Ek>>2&3;for($Sh=$Ng;$Sh>0;$Sh--){$Am=$W[$Sh-1];$Mg=xxtea_mx($Am,$_m,$Ek,$w[$Sh&3^$Kc]);$_m=int32($W[$Sh]-$Mg);$W[$Sh]=$_m;}$Am=$W[$Ng];$Mg=xxtea_mx($Am,$_m,$Ek,$w[$Sh&3^$Kc]);$_m=int32($W[0]-$Mg);$W[0]=$_m;$Ek=int32($Ek-0x9E3779B9);}return
long2str($W,true);}$qi=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$qi[$w]=$X;}}function
add_invalid_login(){$Na=get_temp_dir()."/adminer-invalid";foreach(glob("$Na*")?:array($Na)as$m){$o=file_open_lock($m);if($o)break;}if(!$o)$o=file_open_lock("$Na-".rand_string());if(!$o)return;$jf=json_decode(stream_get_contents($o),true);$jl=time();if($jf){foreach($jf
as$kf=>$X){if($X[0]<$jl)unset($jf[$kf]);}}$hf=&$jf[adminer()->bruteForceKey()];if(!$hf)$hf=array($jl+30*60,0);$hf[1]++;file_write_unlock($o,json_encode($jf));}function
check_invalid_login(array&$qi){$jf=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$o=file_open_lock($m);if($o){$jf=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$w=adminer()->bruteForceKey();$hf=idx($jf,$w,array());$bh=($hf[1]>29?$hf[0]-time():0);if($bh>0){$j=lang(98,ceil($bh/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(99,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$qi,false);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Pb=adminer()->credentials();$J=!is_object(Driver::connect($Pb[0],$Pb[1],""));if($J)set_session("password_required",true);}}return$J;}function
require_password_link($F){$Jg="<a href='https://www.adminer.org/password/'".target_blank().">".lang(100)."</a>";if(!function_exists('password_hash'))return" $Jg";$ti=($F!==null?$F:base64_encode(substr(pack("H*",rand_string()),0,12)));$te=password_hash($ti,PASSWORD_DEFAULT);$m="adminer-plugins.php";$od=file_exists("adminer-plugins.php");if($od)$ff=($F!==null?lang(101,"<b>$m</b>"):lang(102,"<b>$m</b>","<b>$ti</b>"));else{$m="<button name='password_less' value='".h($te)."' class='link'>$m</button>";$ff=($F!==null?lang(103,$m):lang(104,$m,"<b>$ti</b>"));}$Rf="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($te)."'</span>),";$J="<p>$ff
<pre><code class='jush'>".($od?$Rf:"&lt;?php\n<a>return</a> <a>array</a>(\n$Rf\n);")."</code></pre>
<p>$Jg
";return" <a href='#password-less' class='toggle'>".lang(105)."</a>
<div id='password-less' class='hidden'>".($od?$J:"<form action='' method='post'>\n".$J.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ea=$_POST["auth"];if($Ea&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$mm=$Ea["driver"];$O=$Ea["server"];$V=$Ea["username"];$F=(string)$Ea["password"];$h=$Ea["db"];set_password($mm,$O,$V,$F);$_SESSION["db"][$mm][$O][$V][$h]=true;if($Ea["permanent"]){$w=implode("-",array_map('base64_encode',array($mm,$O,$V,$h)));$Ji=adminer()->permanentLogin(true);$qi[$w]="$w:".base64_encode($Ji?encrypt_string($F,$Ji):"");cookie("adminer_permanent",implode(" ",$qi));}if(!array_diff(array_keys($_POST),array("auth","token"))||$mm!=DRIVER||$O!=SERVER||$V!==$_GET["username"]||$h!=DB)redirect(auth_url($mm,$O,$V,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($qi);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(106).' '.str_replace('<a ','<a'.target_blank().' ',lang(107)));}elseif($qi&&!$_SESSION["pwds"]){session_regenerate_id();$Ji=adminer()->permanentLogin();foreach($qi
as$w=>$X){list(,$nb)=explode(":",$X);list($mm,$O,$V,$h)=array_map('base64_decode',explode("-",$w));set_password($mm,$O,$V,decrypt_string(base64_decode($nb),$Ji));$_SESSION["db"][$mm][$O][$V][$h]=true;}}function
unset_permanent(array&$qi){foreach($qi
as$w=>$X){list($mm,$O,$V,$h)=array_map('base64_decode',explode("-",$w));if($mm==DRIVER&&$O==SERVER&&$V==$_GET["username"]&&$h==DB)unset($qi[$w]);}cookie("adminer_permanent",implode(" ",$qi));}function
auth_error($j,array&$qi,$if=true){$Yj=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$Yj]||$_GET[$Yj])&&!$_SESSION["token"])$j=lang(108);elseif($if&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$j
.=($j?'<br>':'').lang(109,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($qi);}}if(!$_COOKIE[$Yj]&&$_GET[$Yj]&&ini_bool("session.use_only_cookies"))$j=lang(110);$Wh=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Wh["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(36),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(111)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($qi);page_header(lang(112),lang(113,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($qi);$Pb=adminer()->credentials();$e=Driver::connect($Pb[0],$Pb[1],$Pb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Xf=null;if(!is_object($e)||($Xf=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($e)?nl_br(h($e)):(is_string($Xf)?$Xf:lang(114))).(preg_match('~^ | $~',get_password())?'<br>'.lang(115):'');auth_error($j,$qi);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(97),lang(116));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Ea&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(116).' '.lang(117);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(118,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(119);}function
print_select_result($I,$f=null,array$Ih=array(),&$y=0,&$Lc=false){$Tf=array();$v=array();$d=array();$T=array();$Hi=array();$Pc=array();$Il=array();$J=array();$Gg=$Lc;$Lc=false;for($r=0;(!$y||$r<$y)&&is_array($K=$I->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($Gg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($tf=0;$tf<count($K);$tf++){$k=$I->fetch_field();$B=$k->name;$R=(isset($k->table)?$k->table:"");$Hh=(isset($k->orgtable)?$k->orgtable:"");$Gh=(isset($k->orgname)?$k->orgname:$B);$Hl=driver()->typeName($k);if($Ih&&JUSH=="sql")$Tf[$tf]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($Hh!=""){$ra=($R!=""?$R:$Hh);if($R!="")$J[$R]=$Hh;if(!isset($v[$ra])){if(!isset($Hi[$Hh])){$Hi[$Hh]=array();foreach(indexes($Hh,$f)as$u){if($u["type"]=="PRIMARY"){$Hi[$Hh]=array_flip($u["columns"]);break;}}}$T[$ra]=$Hh;$v[$ra]=$Hi[$Hh];$d[$ra]=$Hi[$Hh];}if(isset($d[$ra][$Gh])){unset($d[$ra][$Gh]);$v[$ra][$Gh]=$tf;$Tf[$tf]=$ra;}elseif($Gg&&isset($k->orgname)&&$k->db==DB&&!is_blob(array("type"=>$Hl)))$Pc[$tf]=array($ra,$Gh,preg_match('~text|json|lob~',$Hl));}$Il[$tf]=$Hl;echo"<th title='".h(trim(($Hh!=""?"$Hh.$Gh":($k->name!=$Gh?$Gh:""))." ".$Hl))."'>".h($B).($Ih?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}foreach($Pc
as$tf=>$ab){if($d[$ab[0]])unset($Pc[$tf]);}echo"<tbody>\n";}$Ke=array();foreach($v
as$ra=>$u){if($u&&!$d[$ra]){$t="";foreach($u
as$qb=>$tf){if($K[$tf]===null){$t=null;break;}$t
.="&where[".url_escape(bracket_escape($qb))."]=".url_escape($K[$tf]);}$Ke[$ra]=$t;}}echo"<tr>";foreach($K
as$w=>$X){$z="";if(isset($Tf[$w])){if($Ih&&JUSH=="sql"){$R=$K[array_search("table=",$Tf)];$z=ME.$Tf[$w].url_escape($Ih[$R]!=""?$Ih[$R]:$R);}elseif(idx($Ke,$Tf[$w])!==null)$z=ME."edit=".url_escape($T[$Tf[$w]]).$Ke[$Tf[$w]];}$Ge=select_value($X,$z,array('type'=>(preg_match('~binary~',$Il[$w])?'blob':$Il[$w])),null);$b="";$ab=idx($Pc,$w);if($ab&&idx($Ke,$ab[0])!==null&&is_utf8($X)){$Lc=true;$b=" data-name='".h("val[".bracket_escape($T[$ab[0]])."][".bracket_escape(substr($Ke[$ab[0]],1))."][".bracket_escape($ab[1])."]")."' data-text='".($ab[2]?1:0)."'".(html_entity_decode(strip_tags($Ge),ENT_QUOTES,"UTF-8")!==$X?" data-value='".h($X)."'":"");}echo"<td".(preg_match(number_type(),$Il[$w])?" class='number'":"")."$b>$Ge";}}$y=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(16))."\n";return$J;}function
textarea($B,$Y,$L=10,$ub=80,$vf=JUSH,$dd=0){echo"<textarea name='".h($B)."' rows='$L' cols='$ub' class='sqlarea jush-".h($vf)."' spellcheck='false' wrap='off'".($dd?" data-error-line='$dd'":"").">\n";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$C,$Y="",$ri=""){if($C&&$Y!=""&&!isset($C[$Y]))$C=array($Y=>$Y)+$C;$Zk=($C?"select":"input");return"<$Zk$b".($C?"><option value=''>$ri".optionlist($C,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$ri'>");}function
json_row($w,$X=null,$gd=true){static$Jd=true;if($Jd)echo"{";if($w!=""){echo($Jd?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($gd?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Jd=false;}else{echo"\n}\n";$Jd=true;}}function
flat_collations(){$tb=collations();return(is_array(reset($tb))?call_user_func_array('array_merge',array_values($tb)):$tb);}function
edit_type($w,array$k,array$tb,array$Qd=array(),array$wd=array()){$U=(string)$k["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($Qd[$U])&&!in_array($U,$wd))$wd[]=$U;$_k=driver()->structuredTypes();if($Qd)$_k[lang(120)]=$Qd;echo
optionlist(array_merge($wd,$_k),$U),"</select><td>","<input name='".h($w)."[length]' value='".h($k["length"])."' size='3'".(!$k["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($tb?"<input list='collations' name='".h($w)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($k["collation"])."' placeholder='(".lang(121).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$k["unsigned"]).'</select>':''),(isset($k['on_update'])?"<select name='".h($w)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(122).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($Qd?"<select name='".h($w)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(123).")".optionlist(explode("|",driver()->onActions),$k["on_delete"])."</select> ":" ");}function
option_types($U,$Il){return" data-types='".h($Il)."'".(preg_match("~$Il~",$U)?"":" class='hidden'");}function
process_length($x){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$x))return"(max)";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*(\d+)\s+(CHAR|BYTE)\s*\)?\s*$~i',$x,$A))return"($A[1] ".strtoupper($A[2]).")";$ad=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$ad(?:\\s*,\\s*$ad)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$ad~",$x,$bg)?"(".implode(",",$bg[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_in($X){$X=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$X);$Vi="'(?:[^']|'')*+'";$sf=(preg_match("~^\\s*$Vi(?:\\s*,\\s*$Vi)*+\\s*\$~",$X)&&preg_match_all("~$Vi~",$X,$bg)?array_map('Adminer\idf_unescape',$bg[0]):array_map('trim',explode(",",$X)));return"(".implode(", ",array_map('Adminer\q',$sf)).")";}function
process_between($X){$Vi="('(?:[^']|'')*+')";$Xi=(preg_match("~^\\s*$Vi\\s+AND\\s+$Vi\\s*\$~i",$X,$A)?array(idf_unescape($A[1]),idf_unescape($A[2])):preg_split('~\s+AND\s+~i',trim($X)));return(count($Xi)==2?q($Xi[0])." AND ".q($Xi[1]):"NULL AND NULL");}function
process_type(array$k,$rb="COLLATE"){return" ".(is_user_type($k["type"])?idf_escape($k["type"]):$k["type"]).process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],driver()->unsigned)?" $k[unsigned]":"").(preg_match('~'.text_type().'~',$k["type"])&&$k["collation"]?" $rb ".(JUSH=="mssql"&&preg_match('~^\w+$~',$k["collation"])?$k["collation"]:q($k["collation"])):"");}function
process_field(array$k,array$Fl){return
array(idf_escape(trim($k["field"])),process_type($Fl),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE $k[on_update]".process_length($k["length"]):""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),);}function
default_value(array$k){if($k["default"]===null)return"";$i=str_replace("\r","",$k["default"]);$be=$k["generated"];$Q=!preg_match('~]$~',$k["length"])&&(preg_match('~char|binary|text|clob|json|enum|set|String~',$k["type"])||driver()->enumLength($k));$Xh=(JUSH=="sqlite"||(JUSH=="sql"&&$k["default_sql"]&&!preg_match("~^(current_timestamp(\\(\\d*\\))?|[bx]'[\\da-f]*')$~i",$i)));return(in_array($be,driver()->generated)?(JUSH=="mssql"?" AS ($i)".($be=="VIRTUAL"?"":" $be"):" GENERATED ALWAYS AS ($i) $be"):(preg_match('~^GENERATED ~i',$i)?" $i":" DEFAULT ".(!$k["default_sql"]&&($Q||preg_match('~^(?![a-z])~i',$i))?(JUSH=="sql"&&preg_match('~text|json~',$k["type"])?"(".q($i).")":q($i)):($Xh?"($i)":$i))));}function
edit_fields(array$l,array$tb,$U="TABLE",array$Qd=array()){$l=array_values($l);$gc=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$yb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(124):lang(125)),"<th id='label-type'>".lang(49)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(126),"<th>".lang(127);if($U=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(51)."'>A_I</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<th id='label-default'$gc>".lang(52),(support("comment")?"<th id='label-comment'$yb>".lang(50):"");$Gf=!support("move_col");echo"<td>".icon("plus","add[".($Gf?count($l):0)."]","+",lang(128),($Gf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($l
as$r=>$k){$r++;$Jh=$k[($_POST?"orig":"field")];$uc=(isset($_POST["add"][$r-1])||(isset($k["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$Jh=="");echo"<tr".($uc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$k["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(129))." ":"");if($uc)echo"<input name='fields[$r][field]' value='".h($k["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$Jh);edit_type("fields[$r]",$k,$tb,$Qd);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$k["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($k["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$gc>".html_select("fields[$r][generated]",array_merge(array("","DEFAULT","DEFAULT SQL"),driver()->generated),$k["generated"],"","label-default")." ";$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($k["default"]);echo(preg_match('~\n~',$k["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$yb>".adminer()->commentInput('COLUMN',$b,$k["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(128))." ":""),($Jh==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(130)):"");}}function
process_fields(array&$l){if($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Gc,$Mb,$Hc,$fl,$Ic,$_,$wg,$ug,$vg,$rh,$Xg){if($_POST["drop"])query_redirect($Gc,$_,$wg);elseif($rh=="")query_redirect($Mb,$_,$vg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($_,$ug,queries($Gc)&&queries($Mb)&&driver()->commit());driver()->rollback();}elseif($rh!=$Xg){$Ob=queries($Mb);queries_redirect($_,$ug,$Ob&&queries($Gc));if($Ob&&$Hc)queries($Hc);}else
queries_redirect($_,$ug,queries($fl)&&queries($Ic)&&queries($Gc)&&queries($Mb));}function
create_trigger($uh,array$K){$ll=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$uh.$ll:$ll.$uh).preg_replace('~[\s;]+$~',''," $K[Type]\n$K[Statement]").";";}function
q_dollar($Q){$mc='$$';while(strpos($Q.$mc,$mc)!=strlen($Q))$mc='$_'.substr($mc,1);return$mc.$Q.$mc;}function
routine_collate($sb){static$fb=array();if($sb&&!$fb){foreach(collations()as$eb=>$jm){foreach((array)$jm
as$X)$fb[$X]=$eb;}}return($fb[$sb]?"CHARACTER SET ".q($fb[$sb])." ":"")."COLLATE";}function
create_routine($uj,array$K){$P=array();$l=$K["fields"];ksort($l);foreach($l
as$k){if($k["field"]!=""){$af=(preg_match("~^(".driver()->inout.")\$~",$k["inout"])?$k["inout"]:"");$P[]="\n  ".(JUSH=="mssql"?"@$k[field]".process_type($k).($af?" $af":""):($af?"$af ":"").idf_escape($k["field"]).process_type($k,routine_collate($k["collation"])));}}$jc="";$C=array();foreach(routine_options($uj)as$w=>$km){$Y=idx($K["options"],$w,"");if($w=="DEFINER")$jc=($Y?" $w=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$km){if($Y!="")$C[]="$w ".q($Y);}elseif($Y!=reset($km)&&in_array($Y,$km))$C[]=$Y;}$Ef=$K["language"];$kc=preg_replace('~[\s;]+$~','',$K["definition"]);$Cc=(JUSH=="pgsql"||($Ef&&$Ef!="sql"));$Vh=($P?implode(",",$P)."\n":"");return"CREATE$jc $uj ".table(trim($K["name"])).(JUSH=="mssql"&&$uj=="PROCEDURE"?rtrim($Vh):" ($Vh)").($uj=="FUNCTION"?"\nRETURNS".process_type($K["returns"],routine_collate($K["returns"]["collation"])):"").($Ef?" LANGUAGE $Ef":"").($C?"\n".implode(" ",$C):"").($Cc?" AS ".q_dollar("\n".trim($kc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$kc;");}function
remove_definer($H){$jc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($jc).')','\1',$H);}function
object_name($U,$R,array$d){return
str_replace(array("{table}","{columns}"),array($R,implode("_",$d)),adminer()->namePattern($U));}function
format_foreign_key(array$n,$B=""){$h=$n["db"];$eh=$n["ns"];return($B!=""?" CONSTRAINT ".idf_escape($B):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($eh!=""&&$eh!=$_GET["ns"]?idf_escape($eh).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($m,$ql){$J=pack("a100a8a8a8a12a12",$m,644,0,0,decoct($ql->size),decoct(time()));$kb=8*32;for($r=0;$r<strlen($J);$r++)$kb+=ord($J[$r]);$J
.=sprintf("%06o",$kb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$ql->send();echo
str_repeat("\0",511-($ql->size+511)%512);}function
doc_version(){$Wj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$Wj,$A);return($A[1]>=18?$A[1]:"19");}$gj=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$nm=(preg_match($gj,$Wj,$A)?$A[0]:"");if(JUSH=='mssql')return($nm>=15?"sql-server-ver$nm":($nm==12?"azuresqldb-current":"sql-server-2017"));return$nm;}function
doc_link(array$mi,$gl="📖"){$nm=doc_version();$Yl=array('sql'=>"https://dev.mysql.com/doc/refman/$nm/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$nm)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$nm/",);if(connection()->flavor=='maria'){$Yl['sql']="https://mariadb.com/kb/en/";$mi['sql']=($mi['mariadb']?:str_replace(".html","/",$mi['sql']));}if(connection()->flavor=='cockroach'&&$mi['cockroach']){$Yl['pgsql']="https://docs.cockroachlabs.com/docs/v$nm/";$mi['pgsql']=$mi['cockroach'];}return($mi[JUSH]?" <a href='".h($Yl[JUSH].$mi[JUSH].(JUSH=='mssql'?"?view=$nm":""))."'".target_blank()." class='doc' title='".lang(131)."'>$gl</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
adminer()->formatSizeValue($J);}function
set_utf8mb4($Mb){static$P=false;if(!$P&&preg_match('~\butf8mb4~i',$Mb)){$P=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(35).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),lang(132),drop_databases($_POST["db"]));page_header(lang(133),$j,false);echo"<p class='links'>\n";foreach(array('database'=>lang(134),'privileges'=>lang(71),'processlist'=>lang(135),'variables'=>lang(136),'status'=>lang(137),)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".lang(138,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(139,"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();if($g){$Fj=support("scheme");$tb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(35).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(140)."</a>":"")."<th>".lang(141)."<th>".lang(142)."<th>".lang(143)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(144)."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$T){$tj=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$s=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$tj' id='$s'>".h($h)."</a>";$sb=h(db_collation($h,$tb));echo"<td>".(support("database")?"<a href='$tj".($Fj?"&amp;ns=":"")."&amp;database=' title='".lang(67)."'>$sb</a>":$sb),"<td align='right'><a href='$tj&amp;schema=' id='tables-".h($h)."' title='".lang(70)."'>".($_GET["dbsize"]?format_number($T):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(145)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(146)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$la=adminer();$vi=($la
instanceof
Plugins?$la->plugins:array());$Fc=($la
instanceof
Plugins?$la->drivers:array());$rc=design_checksums();if($vi||$Fc||$rc){$lb=($la
instanceof
Plugins?$la->checksums():array());$kh=Plugins::officialChecksums();$Tl=function($Xl){return" (<a href='$Xl'".target_blank()." class='update'>".VERSION."</a>)";};$ui=function($Dd)use($lb,$kh,$Tl){return($lb[$Dd]&&$kh[$Dd]&&$lb[$Dd]!==$kh[$Dd]?$Tl("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(147)."</h3>\n<ul>\n";foreach($vi
as$si){$ej=new
\ReflectionObject($si);$oc=Plugins::describe($si);echo"<li><b>".get_class($si)."</b>".($oc!=""?": $oc":"").$ui(basename((string)$ej->getFileName(),'.php'))."\n";}foreach($Fc
as$s=>$B)echo"<li><b>".h($s)."</b>: ".h($B).$ui(basename((string)$la->driverFiles[$s],'.php'))."\n";if($rc){$mh=official_design_checksums();foreach($rc
as$m=>$qc){list($B,$kb)=$qc;$lh=$mh["$B/$m"];echo"<li><b>".h($m)."</b>".h($B?": $B":"").($lh&&$lh!==$kb?$Tl("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Fb){$this->size+=strlen($Fb);fwrite($this->handler,$Fb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$km=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$km)).".".friendly_url($_GET["field"]));$N=array(idf_escape($_GET["field"]));$I=driver()->select($a,$N,array(where($_GET,$l)),$N);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=adminer()->error();$S=table_status1($a);$B=adminer()->tableName($S);$j=$j?:h($S["Error"]);page_header(($l&&is_view($S)?$S['Engine']=='materialized view'?lang(148):lang(149):lang(150)).": ".($B!=""?$B:h($a)),$j,array(),"",!$l,($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($S)))):""));$sj=array();foreach($l
as$w=>$k)$sj+=$k["privileges"];adminer()->selectLinks($S,(isset($sj["insert"])||!support("table")?"":null));$xb=$S["Comment"];if($xb!="")echo"<p class='nowrap'>".lang(50).": ".adminer()->commentValue('TABLE',$xb)."\n";if($l)adminer()->tableStructurePrint($l,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($z."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$Ye=driver()->inheritsFrom($a);if($Ye){echo"<h3>".lang(151)."</h3>\n";tables_links($Ye);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(152)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(153)."</a>\n";echo"</div>\n";}if(!is_view($S)&&driver()->supportsAlterTable($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(120)."</h3>\n";$Qd=foreign_keys($a);$wa=function_exists('Adminer\alter_table');if($Qd){echo"<table>\n","<thead><tr><th>".lang(154)."<th>".lang(155)."<th>".lang(123)."<th>".lang(122).($wa?"<td class='hover'>":"")."<tbody>\n";foreach($Qd
as$B=>$n){echo"<tr".(!is_int($B)?" title='".h($B)."'":"").">","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$z=($n["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($n["db"]),ME):($n["ns"]!=""?preg_replace('~&ns=[^&]*~',"&ns=".url_escape($n["ns"]),ME):ME));echo"<td><a href='".h($z."table=".url_escape($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),($wa?'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.lang(156).'</a>':''),"\n";}echo"</table>\n";}echo($wa?'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(157)."</a>\n":''),"</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(158)."</h3>\n";$hb=driver()->checkConstraints($a);if($hb){echo"<table>\n";foreach($hb
as$w=>$X)echo"<tr".($w!=$X?" title='".h($w)."'":"").">","<td><code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".lang(156)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(159)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")&&driver()->supportsAlterTable($S)){echo"<div>\n","<h3 id='triggers'>".lang(160)."</h3>\n";$Cl=triggers($a);if($Cl){echo"<table>\n";foreach($Cl
as$w=>$X){echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".lang(156)."</a>";$uj=$X[2];if($uj){$wj=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($uj["ns"]),ME).'function='.url_escape($uj["function"]).'&name='.url_escape($uj["name"]);echo", <a href='".h($wj)."' title='".h($uj["name"])."'>".lang(161)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(162)."</a>\n","</div>\n";}$bk=driver()->shadowTables($a);if($bk){echo"<h3 id='shadow-tables'>".lang(163)."</h3>\n";tables_links($bk);}$Xe=driver()->inheritedTables($a);if($Xe){echo"<h3 id='partitions'>".lang(164)."</h3>\n";$ai=driver()->partitionsInfo($a);if($ai)echo"<p><code class='sql jush-".JUSH."'>BY ".h("$ai[partition_by]($ai[partition])")."</code>\n";tables_links($Xe);}}elseif(isset($_GET["schema"])){page_header(lang(70),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$dj,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($dj,$R)as$B=>$fj){if($B!=$R)$d[$R]=max($d[$R],schema_column($B,$dj,$d)+1);}}return$d[$R];}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(preg_match("~$w|$X~",$U))return" class='$w'";}}$Qk=array();$Sk=array();$Rk=array();$Ad=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$bg,PREG_SET_ORDER);foreach($bg
as$r=>$A){$Qk[$A[1]]=array((float)$A[2],(float)$A[3]);$Sk[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$M=array();$dj=array();$Qd=array();$ta=driver()->allFields();$ze=array();$Tk=array();foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!=""&&!$S["dependent"])$Tk[$R]=$S;else$ze[$R]=true;}}foreach($Tk
as$R=>$S){$G=0;$M[$R]["fields"]=array();foreach($ta[$R]as$k){$G+=1.25;$Ad[$R][$k["field"]]=$G;$M[$R]["fields"][$k["field"]]=$k;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$ze[$X["table"]]){$Qd[$R][]=$X;$dj[$X["table"]][$R]=array();}}}$d=array();$fe=array();$zm=array();$ke=array();foreach(array_keys($M)as$B)schema_column($B,$dj,$d);arsort($d);foreach($d
as$B=>$c){$Cg=null;foreach((array)idx($Qd,$B)as$X){if($X["table"]!=$B&&$M[$X["table"]])$Cg=($Cg===null?$d[$X["table"]]:min($Cg,$d[$X["table"]]));}$d[$B]=max($c,(int)$Cg-1);}$Vk=array();foreach($M
as$B=>$R){$c=$d[$B];$fe[$c][]=$B;$il=.75*strlen($B);foreach($R["fields"]as$k)$il=max($il,.65*strlen($k["field"]));$Vk[$B]=ceil($il)+1;$zm[$c]=max(idx($zm,$c,0),$Vk[$B]);}foreach($Qd
as$B=>$jm){foreach($jm
as$X){$je=$d[$B]+(idx($d,$X["table"],$d[$B])>$d[$B]?1:0);$ke[$je]=idx($ke,$je,0)+1;}}ksort($fe);$xe=0;$ym=0;$vb=0;$Fi=null;$Ok=array();foreach($fe
as$c=>$T){if($Fi!==null){$vb=round($vb+$zm[$Fi]+1.7+idx($ke,$c,0)*.1,1);$D=array();foreach($T
as$B){$Ek=0;$Lb=0;$Ug=array_keys((array)idx($dj,$B));foreach((array)idx($Qd,$B)as$X)$Ug[]=$X["table"];foreach($Ug
as$Og){if($M[$Og]&&$d[$Og]<$c){$Ek+=$M[$Og]["pos"][0];$Lb++;}}$D[$B]=($Lb?$Ek/$Lb:$xe);}asort($D);$T=array_keys($D);}$tl=0;foreach($T
as$B){$G=1.25*count($M[$B]["fields"]);$M[$B]["pos"]=($Qk[$B]?:array($tl,$vb));$Ok[$B]=$M[$B]["pos"][1];$tl+=2.5+$G;$xe=max($xe,$M[$B]["pos"][0]+2.5+$G);$ym=max($ym,round($M[$B]["pos"][1]+$zm[$c],1));if(!$Qk[$B])$Rk[]="\n\t'".js_escape($B)."': [ ".$M[$B]["pos"][0].", ".$M[$B]["pos"][1]." ]";}$Fi=$c;}$Kf=array();$Oa=array();foreach($Qd
as$B=>$jm){foreach($jm
as$X){$bl=idx($Ok,$X["table"],$Ok[$B]);$lk=$Ok[$B]+$zm[$d[$B]];$rj=($bl-1>$lk);$If=($rj?$lk+1:min($Ok[$B],$bl)-1);$Na=idx($Oa,(string)$If,0);$Oa[(string)$If]=$Na+1;$If=round($rj?min($If+$Na*.1,$bl-1):$If-$Na*.1,1);while($Kf[(string)$If])$If-=.0001;$M[$B]["references"][$X["table"]][(string)$If]=array($X["source"],$X["target"]);$dj[$X["table"]][$B][(string)$If]=$X["target"];$Kf[(string)$If]=true;}}echo'<div id="schema" style="height: ',$xe,'em; width: ',$ym,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$Sk)."\n",'};
const tablePosDefault = {',implode(",",$Rk)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$xe,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($M
as$B=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$Vk[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($R["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$cl=>$fj){foreach($fj
as$If=>$aj){$Jf=$If-$R["pos"][1];$Bk=($Jf>0?"left: 100%; width: calc($Jf"."em - 100%)":"left: $Jf"."em");$ym=($Jf>0?"100%":(-$Jf)."em");$r=0;foreach($aj[0]as$kk)echo"\n<div class='references' title='".h($cl)."' id='refs$If-".($r++)."' style='$Bk"."; top: ".$Ad[$B][$kk]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $ym;'></div></div>";}}foreach((array)$dj[$B]as$cl=>$fj){foreach($fj
as$If=>$dl){$Jf=$If-$R["pos"][1];$r=0;foreach($dl
as$al)echo"\n<div class='references arrow' title='".h($cl)."' id='refd$If-".($r++)."' style='left: $Jf"."em; top: ".$Ad[$B][$al]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Jf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($M
as$B=>$R){foreach((array)$R["references"]as$cl=>$fj){if($M[$cl]){foreach($fj
as$If=>$aj){$Dg=$xe;$jg=-10;foreach($aj[0]as$w=>$kk){$xi=$R["pos"][0]+$Ad[$B][$kk];$yi=$M[$cl]["pos"][0]+$Ad[$cl][$aj[1][$w]];$Dg=min($Dg,$xi,$yi);$jg=max($jg,$xi,$yi);}echo"<div class='references' id='refl$If' style='left: $If"."em; top: $Dg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($jg-$Dg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(165),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$j){$i=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Gk){if(support($Gk))$i[$Gk."s"]='';}save_settings(array_intersect_key($_POST+$i,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$i),"adminer_export");$sa=(DB==""||$_GET["ns"]==="");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$sd=dump_headers((count($T)==1?key($T):DB),($sa||count($T)>1));$pf=preg_match('~sql~',$_POST["format"]);if($pf){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Bk=$_POST["db_style"];$g=array(DB);if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($pf&&$Bk)echo
use_sql($h,$Bk).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$M){if($M!=""){if(DB==""&&information_schema(DB,$M))continue;set_schema($M);}if($pf&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$yk=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$rd=array();$Yb=array();foreach($yk
as$B=>$S){if($sa||in_array($B,(array)$_POST["tables"]))$rd[$B]=$S;if($sa||in_array($B,(array)$_POST["data"]))$Yb[$B]=$S;}if($pf){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($rd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Dl=array();foreach($Yb
as$B=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($rd[$B])))$Dl[]=$B;}echo
truncate_all_sql($Dl);}$Qh="";if($_POST["types"]){foreach(types()as$s=>$U){$kc=type_definition($s);$hh=($kc["kind"]=='d'?"DOMAIN":"TYPE");if($kc["definition"])$Qh
.=($Bk!='DROP+CREATE'?"DROP $hh IF EXISTS ".table($U).";;\n":"")."CREATE $hh ".table($U)." $kc[definition];\n\n";else$Qh
.="-- Could not export type ".preg_replace('~[\r\n]+~',' ',$U)."\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$B=$K["ROUTINE_NAME"];$uj=$K["ROUTINE_TYPE"];$We=routine($K["SPECIFIC_NAME"],$uj);if($We["definition"]!=""){$Mb=create_routine($uj,array("name"=>$B)+$We);set_utf8mb4($Mb);$Qh
.=($Bk!='DROP+CREATE'?"DROP $uj IF EXISTS ".table($B).";;\n":"")."$Mb;\n\n";}else$Qh
.="-- Could not export ".strtolower($uj)." ".preg_replace('~[\r\n]+~',' ',$B)."\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$Mb=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($Mb);$Qh
.=($Bk!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$Mb;;\n\n";}}echo($Qh&&JUSH=='sql'?"DELIMITER ;;\n\n$Qh"."DELIMITER ;\n\n":$Qh);}if($_POST["table_style"]||$_POST["data_style"]){$pm=array();foreach($yk
as$B=>$S){$R=array_key_exists($B,$rd);$Wb=array_key_exists($B,$Yb);if($R||$Wb){$ql=null;if($sd=="tar"){$ql=new
TmpFile;ob_start(array($ql,'write'),1e5);}adminer()->dumpTable($B,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$pm[]=$B;elseif($Wb){$l=fields($B);$N=array("*");$Ib=convert_fields($l,$l);if($Ib)$N[]=substr($Ib,2);adminer()->dumpData($B,$_POST["data_style"],"",$N);}if($pf&&$_POST["triggers"]&&$R&&($Cl=trigger_sql($B)))echo"\nDELIMITER ;;\n$Cl\nDELIMITER ;\n";if($sd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$B.csv",$ql);}elseif($pf)echo"\n";}}if($pf&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($rd
as$B=>$S){if(!is_view($S))echo
foreign_keys_sql($B);}}if($pf){foreach($pm
as$om)adminer()->dumpTable($om,$_POST["table_style"],1);}if($sd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(76),$j,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$cc=array('','USE','DROP+CREATE','CREATE');$Dj=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$cc);$Uk=array('','DROP+CREATE','CREATE');$Xb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Xb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(166)."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".lang(167)."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(35)."<td>".html_select('db_style',$cc,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$K["routines"],lang(72)):"").(support("event")?checkbox("events",1,$K["events"],lang(74)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(168)."<td>".html_select('schema_style',$Dj,$K["schema_style"]):""),"<tr><th>".lang(142)."<td>".html_select('table_style',$Uk,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],lang(51)).(support("trigger")?checkbox("triggers",1,$K["triggers"],lang(160)):""),"<tr><th>".lang(169)."<td>".html_select('data_style',$Xb,$K["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(76),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Ei=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(170)."'".on('click','formCheck','^schemas\[').">".lang(168)."</label>","<tbody>\n";foreach(adminer()->schemas()as$M){if(!information_schema(DB,$M))echo"<tr><td>".checkbox("schemas[]",$M,true,$M,"","block")."\n";}}elseif(DB!=""){$ib=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$ib class='jsonly' title='".lang(170)."'".on('click','formCheck','^tables\[').">".lang(150)."</label>","<th style='text-align: right;'><label class='block'>".lang(169)."<input type='checkbox' id='check-data'$ib class='jsonly' title='".lang(170)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$pm="";$Xk=tables_list();foreach($Xk
as$B=>$U){$Di=preg_replace('~_.*~','',$B);$ib=($a==""||$a==(substr($a,-1)=="%"?"$Di%":$B));$Ii="<tr><td>".checkbox("tables[]",$B,$ib,$B,"","block");if($U!==null&&!preg_match('~table~i',$U))$pm
.="$Ii\n";else
echo"$Ii<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$ib)."</label>\n";$Ei[$Di]++;}echo$pm;if($Xk)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(170)."'".on('click','formCheck','^databases\[').">":"").lang(35)."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$Di=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$Di%",$h,"","block")."\n";$Ei[$Di]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Jd=true;foreach($Ei
as$w=>$X){if($w!=""&&$X>1){echo($Jd?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$Jd=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(71));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(171)."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$de=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($de?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(33)."<th>".lang(31)."<td class='hover'><tbody>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"]),"<td>".h($K["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($K["User"]).'&host='.url_escape($K["Host"])).'">'.lang(14)."</a>\n";if(!$de||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(14)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$j&&$_POST["val"]){$oa=0;$Ck=true;$bb=array();$Aj=0;foreach($_POST["val"]as$L)$Aj+=count($L);$Qa=$Aj>1&&driver()->begin();foreach($_POST["val"]as$Mk=>$L){$R=bracket_escape($Mk,true);$l=fields($R);$Nk=indexes($R);foreach($L
as$t=>$K){parse_str(bracket_escape($t,true),$Z);$Ll=array();foreach($Z["where"]as$w=>$X)$Ll[bracket_escape($w,true)]=$X;if(!$l||$Z["null"]||array_diff_key($Ll,$l)||!unique_array($Ll,$Nk)){$Ck=false;break
2;}$P=array();$N=array();foreach($K
as$xf=>$X){$w=bracket_escape($xf,true);$k=idx($l,$w);if(!$k){$Ck=false;break
3;}$P[idf_escape($w)]=(preg_match('~char|text~',$k["type"])||$X!=""?adminer()->processInput($k,$X):"NULL");$N[$xf]=$w;}$Ui=where($Z,$l);if(!driver()->update($R,$P," WHERE $Ui",0," ")){$Ck=false;break
2;}$oa+=connection()->affected_rows;$d=array();foreach($N
as$w)$d[]=idf_escape($w);$Ul=driver()->select($R,$d,array($Ui),$d);$Yg=($Ul?$Ul->fetch_row():array());$tf=0;foreach($N
as$xf=>$w){$k=$l[$w];$Ak=array('type'=>(preg_match('~binary~',$k["type"])?'blob':$k["type"]));$bb["val[$Mk][$t][$xf]"]=select_value(idx($Yg,$tf++),"",$Ak,null);}}}if($Qa&&$Ck)$Ck=driver()->commit();queries_redirect(null,lang(172,$oa),$Ck);if($Qa&&!$Ck)driver()->rollback();page_headers();page_messages($j);foreach($bb
as$B=>$X)echo"<div data-name='".h($B)."' hidden>$X</div>\n";exit;}restart_session();$Ce=&get_session("queries");$Be=&$Ce[DB];if(!$j&&$_POST["clear"]){$Be=array();redirect(remove_from_uri("history"));}stop_session();$ma=get_settings("adminer_import");if($_POST&&$ma)save_settings($ma,"adminer_import");page_header((isset($_GET["import"])?lang(75):lang(64)),$j);$Sf=driver()->lineComment();$dd=0;function
query_error_line($gl,$nh,$H,$Rf){return
substr_count(substr($gl,0,$nh),"\n")+($Rf?:substr_count(substr($H,0,strspn($H," \t\r\n")),"\n")+1);}if(!$j&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$mc=driver()->delimiter;$o=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$ok=adminer()->importServerPath();$o=@fopen((file_exists($ok)?$ok:"compress.zlib://$ok.gz"),"rb");$H=($o?fread($o,1e6):false);}else$H=get_file("sql_file",true,$mc);if(is_string($H)){if(($rg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($rg,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$Pi=$H.(preg_match("~$mc\\s*\$~",$H)?"":$mc);if(!$Be||first(end($Be))!=$Pi){restart_session();$Be[]=array($Pi,time());set_session("queries",$Ce);stop_session();}}$mk="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Sf)[^\n]*\n?|--\r?\n)";$nh=0;$Ti=0;$Uc=true;$Kb=false;$yg=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$wb=0;$ed=array();$Yh='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Sf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$ul=microtime(true);while($H!=""){if(!$nh&&preg_match("~^$mk*+DELIMITER\\s+(\\S+)~i",$H,$A)){$mc=preg_quote($A[1]);$H=substr($H,strlen($A[0]));$Ti+=strlen($A[0]);}elseif(!$nh&&JUSH=='pgsql'&&preg_match("~^($mk*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$mc="\n\\\\\\.\r?\n";$Kb=true;$nh=strlen($A[0]);}elseif(!$nh&&JUSH=='pgsql'&&preg_match("~^$mk*+\\\\connect[ \t][^\n]*~",$H,$A)){$mc="\n";$yg=true;$nh=strlen($A[0]);}else{preg_match("($mc\\s*|$Yh)",$H,$A,PREG_OFFSET_CAPTURE,$nh);list($Sd,$G)=$A[0];if(!$Sd&&$o&&!feof($o))$H
.=fread($o,1e5);else{if(!$Sd&&rtrim($H)=="")break;$nh=$G+strlen($Sd);if($Sd&&!preg_match("(^$mc)",$Sd)){$Ya=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$ni=($Sd=='/*'?'\*/':($Sd=='['?']':(preg_match("~^(?:$Sf)~",$Sd)?"\n":preg_quote($Sd).($Ya?'|\\\\.':''))));while(preg_match("($ni|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$nh)){$Bj=$A[0][0];if(!$Bj&&$o&&!feof($o))$H
.=fread($o,1e5);else{$nh=$A[0][1]+strlen($Bj);if(!$Bj||$Bj[0]!="\\")break;}}}else{$Pi=substr($H,0,$G+($Kb?3:0));$H=substr($H,$nh);$Qi=$Ti;$Ti+=$nh;$nh=0;if($Kb||$yg){$mc=driver()->delimiter;$Kb=false;$yg=false;}$pb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Pi)."</code>";if(preg_match("~^$mk*+\$~",$Pi)&&!preg_match('~/\*M?!~',$Pi)){echo($_POST["only_errors"]?"":"<pre>$pb</pre>\n");continue;}$Uc=false;$wb++;$Ii="<pre id='sql-$wb'>$pb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$mk*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$Pi,$A)!==0){echo$Ii,"<p class='error'>".lang(173,preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$ed[]=" <a href='#sql-$wb'>$wb</a>";if(!$dd&&!isset($_GET["import"]))$dd=query_error_line($_POST["query"],$Qi,$Pi,0);if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Ii;ob_flush();flush();}$tk=microtime(true);if(JUSH=="pgsql"&&preg_match("~^$mk*+\\\\connect\\s+(\"(?:[^\"]|\"\")+\"|[^\\s\";]+)\\s*;*\\s*\$~",$Pi,$A)){$h=idf_unescape($A[1]);if(connection()->select_db($h)){if($f)$f->select_db($h);echo($_POST["only_errors"]?"":"<p class='message'>".lang(35).": ".h($h)." <span class='time'>(".format_time($tk).")</span>\n");continue;}}elseif(connection()->multi_query($Pi)&&$f&&preg_match("~^$mk*+USE\\b~i",$Pi))$f->query($Pi);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Ii:""),"<p class='error'>".lang(174).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$ed[]=" <a href='#sql-$wb'>$wb</a>";if(!$dd&&!isset($_GET["import"]))$dd=query_error_line($_POST["query"],$Qi,$Pi,driver()->errorLine($Pi));if($_POST["error_stops"])break
2;}else{$z=ME."sql=".url_escape(trim($Pi));$jl=" <span class='time'>(".format_time($tk).")</span>".(strlen($z)<adminer()->sqlUrlLength()-100?" <a href='".h($z)."'>".lang(14)."</a>":"");$oa=connection()->affected_rows;$sm=($_POST["only_errors"]?"":driver()->warnings());$tm="warnings-$wb";if($sm)$jl
.=", <a href='#$tm' class='toggle'>".lang(46)."</a>";$pd="";$qd="explain-$wb";if(is_object($I)){$y=$_POST["limit"];$fh=$y;$Lc=!$_POST["only_errors"];if($Lc)echo"<form action='' method='post'>\n";$Ih=print_select_result($I,$f,array(),$fh,$Lc);if(!$_POST["only_errors"]){$fh=max($I->num_rows,$fh);echo"<p class='sql-footer'>".($fh?($y&&$fh>$y?lang(175,$y):"").lang(176,$fh):""),$jl;if($f&&preg_match("~^($mk|\\()*+SELECT\\b~i",$Pi)&&($pd=adminer()->explain($f,$Pi,$Ih))!="")echo", <a href='#$qd' class='toggle'>Explain</a>";if($Lc)echo", <input type='submit' name='save' value='".lang(18)."' class='jsonly' disabled"." title='".lang(177)."'".on('click','sqlSave',lang(21)).">";$s="export-$wb";echo", <a href='#$s' class='toggle'>".lang(76)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",adminer()->dumpFormat(),$ma["format"]).input_hidden("query",$Pi)."<input type='submit' name='export' value='".lang(76)."'".($y?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$mk*+(CREATE|DROP|ALTER)$mk++(DATABASE|SCHEMA)\\b~i",$Pi)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(178,$oa)."$jl\n";}echo($sm?"<div id='$tm' class='hidden'>\n$sm</div>\n":""),($pd!=""?"<div id='$qd' class='hidden explain'>\n$pd</div>\n":"");}$tk=microtime(true);}while(connection()->next_result());}}}}}if($Uc)echo"<p class='message'>".lang(179)."\n";else{$Qe=connection()->inTransaction();driver()->rollback();if($Qe)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(180,$wb-count($ed))," <span class='time'>(".format_time($ul).")</span>\n";elseif($ed&&$wb>1)echo"<p class='error'>".lang(174).": ".implode("",$ed)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$Vl="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"),adminer()->sqlUrlLength());else
echo
on_upload_progress($Vl);echo'>
';$md="<input type='submit' value='".lang(181)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Pi=$_GET["sql"];if($_POST)$Pi=$_POST["query"];elseif($_GET["history"]=="all")$Pi=$Be;elseif($_GET["history"]!="")$Pi=idx($Be[$_GET["history"]],0);echo"<p>";textarea("query",$Pi,20,80,JUSH,$dd);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$md\n",lang(182).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$le=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(183)."</legend><div>",($Vl?input_hidden(ini_get("session.upload_progress.name"),$Vl):""),"SQL$le: ".file_input(" name='sql_file[]' multiple","\n$md"),($Vl?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Ne=adminer()->importServerPath();if($Ne)echo"<fieldset><legend>".lang(184)."</legend><div>",lang(185,"<code>".h($Ne)."$le</code>")," <input type='submit' name='webfile' value='".lang(186)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(187))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(188))."\n",input_token();if(!isset($_GET["import"])&&$Be){print_fieldset("history",lang(189),$_GET["history"]!="");for($X=end($Be);$X;$X=prev($Be)){$w=key($Be);list($Pi,$jl,$Qc)=$X;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.lang(14)."</a>"." <span class='time' title='".@date('Y-m-d',$jl)."'>".@date("H:i:s",$jl)."</span>"." <code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Sf).*~m",'',$Pi))),80,"</code>").($Qc?" <span class='time'>($Qc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(190)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(191)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$Sl=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$B=>$k){if((!$Sl&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$B]);}if($_POST&&!$j&&!isset($_GET["select"])){$_=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$_=($Sl?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$_))$_=ME."select=".url_escape($a);$v=indexes($a);$Ml=unique_array($_GET["where"],$v);$Ui="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_,lang(192),driver()->delete($a,$Ui,$Ml?0:1));else{$P=array();foreach($l
as$B=>$k){$X=process_input($k);if($X!==false&&$X!==null)$P[idf_escape($B)]=$X;}if($Sl){if(!$P)redirect($_);queries_redirect($_,lang(193),driver()->update($a,$P,$Ui,$Ml?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$I=driver()->insert($a,$P);$Hf=($I?last_id($I):0);queries_redirect($_,lang(194,($Hf?" $Hf":"")),$I);}}}$K=null;$H="";$jl="";if($Z){$N=array();$Mj=array("*");foreach($l
as$B=>$k){if(isset($k["privileges"]["select"])){$Ba=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$c=($Ba?"$Ba AS ":"").idf_escape($B);$N[]=$c;if($Ba)$Mj[]=$c;}}$K=array();if(!support("table")){$N=array("*");$Mj=$N;}if($N){$tk=microtime(true);$I=driver()->select($a,$N,array($Z),$N,array(),(isset($_GET["select"])?2:1));$H=str_replace("SELECT ".implode(", ",$N),"SELECT ".implode(", ",$Mj),driver()->query);$jl=format_time($tk);if(!$I)$j=adminer()->error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$w=>$X){if(!$Z)$K[$w]=null;$l[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"]){$zi=array();foreach((array)$_POST["fields"]as$w=>$X)$zi[bracket_escape($w,true)]=$X;$K=$zi+($K?$K:array());}edit_form($a,$l,$K,$Sl,$j,$H,$jl);}elseif(isset($_GET["create"])){function
referencable_primary($Oj){$J=array();foreach(table_status('',true)as$Pk=>$R){if($Pk!=$Oj&&!$R["dependent"]&&fk_support($R)){foreach(fields($Pk)as$k){if($k["primary"]){if($J[$Pk]){unset($J[$Pk]);break;}$J[$Pk]=$k;}}}}return$J;}$a=$_GET["create"];$ci=driver()->partitionBy;$gi=($ci&&$a!=""?driver()->partitionsInfo($a):array());$cj=referencable_primary($a);$Qd=array();foreach($cj
as$Pk=>$k)$Qd[str_replace("`","``",$Pk)."`".str_replace("`","``",$k["field"])]=$Pk;$Lh=array();$S=array();$dh=false;if($a!=""){$Lh=fields($a);$S=table_status1($a);$dh=(count($S)<2);}$wa=($a==""||driver()->supportsAlterTable($S));$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$j)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$j){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(195),drop_tables(array($a)));else{$l=array();$ta=array();$Zl=false;$Od=array();$Kh=reset($Lh);$qa=" FIRST";foreach($K["fields"]as$k){$n=$Qd[$k["type"]];$Fl=($n!==null?$cj[$n]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$k["default_sql"]=($k["generated"]=="DEFAULT SQL");$Ni=process_field($k,$Fl);$ta[]=array($k["orig"],$Ni,$qa);if(!$Kh||$Ni!==process_field($Kh,$Kh)){$l[]=array($k["orig"],$Ni,$qa);if($k["orig"]!=""||$qa||(JUSH=="sqlite"&&$k["comment"]!=""))$Zl=true;}if($n!==null)$Od[idf_escape($k["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Qd[$k["type"]],'source'=>array($k["field"]),'target'=>array($Fl["field"]),'on_delete'=>$k["on_delete"],),object_name("FOREIGN",trim($K["name"]),array($k["field"])));$qa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$Zl=true;$l[]=array($k["orig"]);}if($k["orig"]!=""){$Kh=next($Lh);if(!$Kh)$qa="";}}$ei=array();if(in_array($K["partition_by"],$ci)){foreach($K
as$w=>$X){if(preg_match('~^partition~',$w))$ei[$w]=$X;}foreach($ei["partition_names"]as$w=>$B){if($B==""){unset($ei["partition_names"][$w]);unset($ei["partition_values"][$w]);}}$ei["partition_names"]=array_values($ei["partition_names"]);$ei["partition_values"]=array_values($ei["partition_values"]);if($ei==$gi)$ei=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$ei=null;$tg=lang(196);if($a==""){cookie("adminer_engine",$K["Engine"]);$tg=lang(197);}$B=trim($K["name"]);$_=ME.(support("table")?"table=":"select=").url_escape($B);$xb=($K["Comment"]!=$S["Comment"]?$K["Comment"]:null);$Xc=($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:"");$I=alter_table($a,$B,(JUSH=="sqlite"&&($Zl||$Od||$xb!==null||$Xc)?$ta:$l),$Od,$xb,$Xc,($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$ei);if($I&&!Queries::$queries&&$a!=""&&!$l&&!$Od)redirect($_);queries_redirect($_,$tg,$I);}}$vk=($a!=""?"alter":"create");page_header(($a!=""?lang(44):lang(77)),$j,array("table"=>$a),h($a),$dh,doc_link(array('sql'=>"$vk-table.html",'mariadb'=>($a!=""?"$vk-table":""),)));if(!$_POST){$Il=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Il["int"])?"int":(isset($Il["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Lh
as$k){if($k["generated"])$k["default"]=ltrim($k["default"]);$k["generated"]=$k["generated"]?:(isset($k["default"])?($k["default_sql"]?"DEFAULT SQL":"DEFAULT"):"");$K["fields"][]=$k;}if($ci){$K+=$gi;$K["partition_names"][]="";$K["partition_values"][]="";}}}$tb=flat_collations();$Yc=driver()->engines();foreach($Yc
as$Xc){if(!strcasecmp($Xc,$K["Engine"])){$K["Engine"]=$Xc;break;}}$eg=max_input_vars(12,20);if($eg){$ze=(count($K["fields"])>$eg?"":" hidden");echo"<p".($ze?" id='max-fields' data-columns='$eg'":"")." class='error$ze'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(198).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",(!$wa?h($S["Engine"])."\n":($Yc?html_select("Engine",array(""=>"(".lang(199).")")+$Yc,$K["Engine"],on('change','helpClose').on_help_value())."\n":""));if($tb)echo"<datalist id='collations'>".optionlist($tb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".lang(121).")'>\n");echo"<input type='submit' value='".lang(18)."'>\n";}if(support("columns")&&$wa){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$tb,"TABLE",$Qd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(51).": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(200),on('click','columnShowClick',6),"jsonly");$zb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$zb,lang(50),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($zb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$a)),'>
';if($ci&&(JUSH=='sql'||$a=="")){$di=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",lang(202),$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$ci),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",lang(203).": <input type='number' name='partitions' class='size".($di||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($di?"":" class='hidden'").">\n","<thead><tr><th>".lang(204)."<th>".lang(205)."<tbody>\n";foreach($K["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($w==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Ve=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$Te=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Ve[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Ve[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Ve[]="VECTOR";$v=indexes($a);$l=fields($a);$Hi=array();if(JUSH=="mongo"){$Hi=$v["_id_"];unset($Ve[0]);unset($v["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]){$va=array();foreach($K["indexes"]as$u){$B=$u["name"];if(in_array($u["type"],$Ve)){$d=array();$Of=array();$pc=array();$yh=array();$Ue=(support("partial_indexes")?$u["partial"]:"");$Se=(in_array($u["algorithm"],$Te)?$u["algorithm"]:"");$P=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$c){if($c!=""){$x=idx($u["lengths"],$w);$nc=idx($u["descs"],$w);$xh=idx($u["opclasses"],$w);$P[]=($l[$c]?idf_escape($c):$c).($x?"(".(+$x).")":"").($xh!=""?" ".idf_escape($xh):"").($nc?" DESC":"");$d[]=$c;$Of[]=($x?:null);$pc[]=$nc;$yh[]="$xh";}}$nd=$v[$B];if($nd){ksort($nd["columns"]);ksort($nd["lengths"]);ksort($nd["descs"]);if($u["type"]==$nd["type"]&&array_values($nd["columns"])===$d&&(!$nd["lengths"]||array_values($nd["lengths"])===$Of)&&array_values($nd["descs"])===$pc&&(!$nd["opclasses"]||array_values($nd["opclasses"])===$yh)&&$nd["partial"]==$Ue&&(!$Te||$nd["algorithm"]==$Se)&&!$nd["invalid"]){unset($v[$B]);continue;}}if($d)$va[]=array($u["type"],$B,$P,$Se,$Ue);}}foreach($v
as$B=>$nd)$va[]=array($nd["type"],$B,"DROP");if(!$va)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(206),alter_indexes($a,$va));}page_header(lang(152),$j,array("table"=>$a),h($a),false,doc_link(array('sql'=>"create-index.html",)));$Cd=array_keys($l);if($_POST["add"]){foreach($K["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$K["indexes"][$w]["columns"][]="";}$u=end($K["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$K["indexes"]=$v;}$Of=(JUSH=="sql"||JUSH=="mssql");$yh=driver()->indexOpclasses();$ck=($_POST?$_POST["options"]:get_setting("index_options"));$Qg=array();foreach($Ve
as$U)$Qg[$U]=str_replace("{table}",$a,adminer()->namePattern($U));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(207);$Le=" class='idxopts".($ck?"":" hidden")."'";if($Te)echo"<th id='label-algorithm'$Le>".lang(208).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" hidden>',lang(209).($Of?"<span$Le> (".lang(210).")</span>":"");if($Of||support("descidx"))echo
checkbox("options",1,$ck,lang(127),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(211);if(support("partial_indexes"))echo"<th id='label-condition'$Le>".lang(212);echo'<td><noscript>',icon("plus","add[0]","+",lang(128)),'</noscript>
<tbody>
';if($Hi){echo"<tr><td>PRIMARY<td>";foreach($Hi["columns"]as$w=>$c)echo
select_input(" disabled",array_combine($Cd,$Cd),$c),"<label><input disabled type='checkbox'>".lang(59)."</label> ";echo"<td><td>\n";}$tf=1;foreach($K["indexes"]as$u){if(!$_POST["drop_col"]||$tf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$tf][type]",array(-1=>"")+$Ve,$u["type"],on('change','indexesChangeType',$Qg),"label-type");if($Te)echo"<td$Le>".html_select("indexes[$tf][algorithm]",array_merge(array(""),$Te),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$c){echo"<span>".select_input(" name='indexes[$tf][columns][$r]' title='".lang(48)."'".on('change','indexesChangeColumn',$Qg),($l&&($c==""||$l[$c])?array_combine($Cd,$Cd):array()),$c)," <span$Le>",($Of?"<input type='number' name='indexes[$tf][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(126)."'>":"");if($yh){$xh=idx($u["opclasses"],$w);echo
html_select("indexes[$tf][opclasses][$r]",array(""=>"(".lang(213).")")+array_combine($yh,$yh)+($xh!=""?array($xh=>$xh):array()),$xh),'';}echo(support("descidx")?checkbox("indexes[$tf][descs][$r]",1,idx($u["descs"],$w),lang(59)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$tf][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Le><input name='indexes[$tf][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$tf]","x",lang(130),on('click','editingRemoveRow','indexes$1[type]')),(idx($v[$u["name"]],"invalid")?" <span class='error'>INVALID</span>":""),"\n";}$tf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if(support("concurrent_indexes"))echo
checkbox("concurrently",1,$_POST["concurrently"],"concurrently").''."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$j&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(214),drop_databases(array(DB)));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),lang(215),rename_database($B,(string)$K["collation"]));}else{$g=explode("\n",str_replace("\r","",$B));$Ck=true;$Ff="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$K["collation"]))$Ck=false;$Ff=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($Ff),lang(216),$Ck);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),lang(217));}}$vk=(DB!=""?"alter":"create");page_header(DB!=""?lang(67):lang(134),$j,array(),h(DB),false,doc_link(array('sql'=>"$vk-database.html",'mariadb'=>(DB!=""?"":"$vk-database"),)));$tb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$tb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$de){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$de,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?"<textarea autofocus name='name' rows='10' cols='40'>\n".h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($tb?"<datalist id='collations'>".optionlist(flat_collations())."</datalist>"."<input list='collations' name='collation' value='".h($K["collation"])."' placeholder='(".lang(121).")'>".doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):"")."\n",'<input type=\'submit\' value=\'',lang(18),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(146)."'".confirm(lang(201,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(128))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$zj=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$uj=routine($_GET["call"],$zj);$qf=(idx($uj["returns"],"type")=="trigger");page_header(($qf?lang(160):lang(218)).": ".h($ca),$j,"#routines","",!$uj,(isset($_GET["callf"])?"":doc_link(array('sql'=>"call.html",))));$Oe=array();$Qh=array();foreach($uj["fields"]as$r=>$k){if(substr($k["inout"],-3)=="OUT"&&JUSH=='sql')$Qh[$r]="@".idf_escape($k["field"])." AS ".idf_escape($k["field"]);if(!$k["inout"]||preg_match('~^(IN|OUTPUT)~',$k["inout"]))$Oe[]=$r;}if(!$j&&$_POST){$Za=array();foreach($uj["fields"]as$w=>$k){$X="";if(in_array($w,$Oe)){$X=process_input($k);if($X===false)$X="''";if(isset($Qh[$w]))connection()->query("SET @".idf_escape($k["field"])." = $X");}if(isset($Qh[$w]))$Za[]="@".idf_escape($k["field"]);elseif(in_array($w,$Oe))$Za[]=$X;}$_a=implode(", ",$Za);$H=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($uj["returns"],"type")=="record"?"* FROM ":"").table($ca)."($_a)":"EXEC ".table($ca).($_a!=""?" $_a":""));$tk=microtime(true);$I=connection()->multi_query($H);$oa=connection()->affected_rows;echo
adminer()->selectQuery($H,$tk,!$I);if(!$I)echo"<p class='error'>".adminer()->error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang(219,$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Qh)print_select_result(connection()->query("SELECT ".implode(", ",$Qh)));}}if($qf){$Cl=driver()->routineTriggers($_GET["call"]);if($Cl){echo"<table>\n";foreach($Cl
as$K){$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($K["ns"]),ME);echo"<tr><th>".h($K["trigger"])."<td><a href='".h($z."table=".url_escape($K["table"])."#triggers")."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>"."<td class='hover'><a href='".h($z."trigger=".url_escape($K["table"])."&name=".url_escape($K["trigger"]))."'>".lang(156)."</a>\n";}echo"</table>\n";}}else{echo'<form action="" method="post">
';if($Oe){echo"<table class='layout'>\n";foreach($Oe
as$w){$k=$uj["fields"][$w];$B=$k["field"];echo"<tr><th>".adminer()->fieldName($k);$Y=idx($_POST["fields"],$B);if($Y!=""){if($k["type"]=="set")$Y=implode(",",$Y);}input($k,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(218),'\'>
',input_token(),'</form>
';}echo'
',adminer()->commentValue($zj,$uj['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$al=array();foreach($K["source"]as$w=>$X)$al[$w]=$K["target"][$w];$K["target"]=$al;}$Qa=false;if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$Cb=$K["constraint"];$va="ALTER TABLE ".table($a);$Gc="$va DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B?:'');if($K["drop"])$I=queries($Gc);else{$ja=" ADD".format_foreign_key($K,$Cb);if($B=="")$I=queries($va.$ja);elseif(JUSH=="pgsql"||(JUSH=="sql"&&$Cb!=$B))$I=queries("$Gc,$ja");else{$Qa=support("transaction_ddl")&&driver()->begin();$I=($Qa||idx(foreign_keys($a),$B)?queries($Gc):true)&&queries($va.$ja)&&(!$Qa||driver()->commit());}}}queries_redirect(ME."table=".url_escape($a),($K["drop"]?lang(220):($B!=""?lang(221):lang(222))),$I);if($Qa)driver()->rollback();if(!$K["drop"])$j=lang(223);}$dh=false;if(!$_POST&&$B!=""){$Qd=foreign_keys($a);$K=idx($Qd,$B,array());$dh=!$K;}$Pg=str_replace("{table}",$a,adminer()->namePattern("FOREIGN"));$Sg="^".str_replace('\{columns\}','.*',preg_quote($Pg))."$";page_header(($B!=""?lang(224):lang(157)),$j,array("table"=>$a),h($B!=""?$B:$a),$dh,doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)));if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($B!=""){$K["source"][]="";$K["constraint"]=$B;}else{$K["table"]=$a;$K["source"]=array("");$K["constraint"]=str_replace("{columns}","",$Pg);}echo'
<form action="" method="post">
';$kk=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);$Mh=null;if($K["ns"]!=""){$Mh=get_schema();set_schema($K["ns"]);}$bj=array_keys(array_filter(table_status('',true),function(array$S){return!$S["dependent"]&&fk_support($S);}));$al=array_keys(fields(in_array($K["table"],$bj)?$K["table"]:reset($bj)));$b=on('change','foreignChange');echo"<p><label>".lang(225).": ".html_select("table",$bj,$K["table"],$b)."</label>\n";if(JUSH!="sqlite"){$Nj=($K["db"]!=""?$K["db"]:DB);$dc=array();foreach(adminer()->databases()as$h){if(!driver()->isSystem($h)||$h==$Nj||$h==DB)$dc[]=$h;}echo"<label>".lang(78).": ".html_select("db",$dc,$Nj,$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(226),'\'></noscript>
<table',on('change','foreignName',$Sg,$Pg),'>
<thead><tr><th id="label-source">',lang(154),'<th id="label-target">',lang(155),'<tbody>
';$tf=0;foreach($K["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$kk,$X,($tf==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$al,idx($K["target"],$w),"","label-target");$tf++;}echo'</table>
<p>
<label>',lang(123),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>',lang(122),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(227),'\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$B)),'>
';if(JUSH!="sqlite"){print_fieldset("options",lang(127),$K["constraint"]!=$B&&!preg_match("($Sg)",$K["constraint"]));echo"<label>".lang(211).": <input name='constraint' value='".h($K["constraint"])."' autocapitalize='off'></label>\n","</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Nh="VIEW";if(JUSH=="pgsql"&&$a!=""){$xk=table_status1($a);$Nh=strtoupper($xk["Engine"]);}if($_POST&&!$j){$B=trim($K["name"]);$Ba=" AS\n$K[select]";$_=ME."table=".url_escape($B);$tg=lang(228);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$U=="VIEW"&&$Nh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Ba,$_,$tg);else{$el="adminer_".uniqid();drop_create("DROP $Nh ".table($a),"CREATE $U ".table($B).$Ba,"DROP $U ".table($B),"CREATE $U ".table($el).$Ba,"DROP $U ".table($el),($_POST["drop"]?substr(ME,0,-1):$_),lang(229),$tg,lang(230),$a,$B);}}$dh=false;if(!$_POST&&$a!=""){$K=view($a);$dh=!$K["select"];$K["name"]=$a;$K["materialized"]=($Nh!="VIEW");if(!$j)$j=adminer()->error();}page_header(($a!=""?lang(43):lang(231)),$j,array("table"=>$a),h($a),$dh,doc_link(array('sql'=>"create-view.html",)));echo'
<form action="" method="post">
<p>',lang(211),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],lang(148)):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$gf=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$yk=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$j){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(232));elseif(in_array($K["INTERVAL_FIELD"],$gf)&&isset($yk[$K["STATUS"]])){$Cj="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(233):lang(234)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Cj.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$Cj)."\n".$yk[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}$dh=false;if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$dh=!$L;$K=reset($L);}page_header(($aa!=""?lang(235).": ".h($aa):lang(236)),$j,"#events","",$dh,doc_link(array('sql'=>"create-event.html")));echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(211),'<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(237),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(238),'<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>',lang(239),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$gf,$K["INTERVAL_FIELD"]),'<tr><th>',lang(137),'<td>',html_select("STATUS",$yk,$K["STATUS"]),'<tr><th>',lang(50),'<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",lang(240)),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$uj=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$j){foreach($K["fields"]as$w=>$k){if($k["field"]=="")unset($K["fields"][$w]);}$sh=routine($_GET["procedure"],$uj);$qh=($sh?routine_id($ca,$sh):"");$Wg=routine_id($K["name"],$K);$Mb=create_routine($uj,$K);$_=substr(ME,0,-1);$tg=lang(241);if(!$_POST["drop"]&&$qh==$Wg&&connection()->flavor!="mysql")queries_redirect($_,$tg,queries(substr_replace($Mb,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$el="adminer_".uniqid();drop_create("DROP $uj $qh",$Mb,"DROP $uj $Wg",create_routine($uj,array("name"=>$el)+$K),"DROP $uj ".routine_id($el,$K),$_,lang(242),$tg,lang(243),$ca,$K["name"]);}}$dh=false;if(!$_POST&&$ca!=""){$K=routine($_GET["procedure"],$uj);$dh=!$K;$K["name"]=$ca;}$xj=strtolower($uj);page_header(($ca!=""?(isset($_GET["function"])?lang(161):lang(244)).": ".h($ca):(isset($_GET["function"])?lang(245):lang(246))),$j,"#routines","",$dh,doc_link(array('sql'=>"create-procedure.html",'mariadb'=>"create-$xj/",)));if(!$_POST&&$ca=="")$K["language"]="sql";$tb=(JUSH=="sql"?flat_collations():array());$vj=routine_languages();echo($tb?"<datalist id='collations'>".optionlist($tb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(211),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($vj?"<label>".lang(24).": ".html_select("language",array_keys($vj),$K["language"],on('change','routineLanguage',$vj))."</label>\n":""),'<input type=\'submit\' value=\'',lang(18),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$tb,$uj);if(isset($_GET["function"])){echo"<tr><td>".lang(247);edit_type("returns",(array)$K["returns"],$tb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20,80,($vj[$K["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$ca)),'>
';$yj=routine_options($uj);if($yj){$Dh=false;foreach($yj
as$w=>$km){$i=($km?reset($km):"");$K["options"][$w]=idx($K["options"],$w,$i);if($K["options"][$w]!=$i)$Dh=true;}print_fieldset("options",lang(127),$Dh);echo"<table class='layout'>\n";foreach($yj
as$w=>$km){$Af="label-option-$w";$ml=str_replace("_"," ",$w);$N=array();foreach($km
as$Y)$N[$Y]=(strpos($Y,"$ml ")===0?substr($Y,strlen($ml)+1):$Y);echo"<tr><th id='$Af'>$ml<td>".($N?html_select("options[$w]",$N,$K["options"][$w],"",$Af):"<input name='options[$w]' value='".h($K["options"][$w])."' aria-labelledby='$Af' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$K=$_POST;if($K&&!$j){$_=ME."table=".url_escape($a);$wg=lang(248);$ug=lang(249);$vg=lang(250);if(JUSH=="sqlite"&&!(min_version(3.53)&&($B==""||idx(driver()->checkConstraints($a),$B)!=$B))){$ja=($K["drop"]?"":($K["name"]!=""?"CONSTRAINT ".idf_escape($K["name"])." ":"")."CHECK ($K[clause])");queries_redirect($_,($K["drop"]?$wg:($B!=""?$ug:$vg)),recreate_table($a,$a,array(),array(),array(),"",array(),"$B",$ja));}else{$va="ALTER TABLE ".table($a);$gb=" CHECK ($K[clause])";$el="adminer_".uniqid();drop_create("$va DROP CONSTRAINT ".idf_escape($B),"$va ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"").$gb,"$va DROP CONSTRAINT ".idf_escape($K["name"]),"$va ADD CONSTRAINT ".idf_escape($el).$gb,"$va DROP CONSTRAINT ".idf_escape($el),$_,$wg,$ug,$vg,$B,$K["name"]);}}$dh=false;if(!$K){$jb=driver()->checkConstraints($a);$dh=($B!=""&&!$jb[$B]);$K=array("name"=>($B==""?object_name("CHECK",$a,array()):($jb[$B]!=$B?$B:"")),"clause"=>$jb[$B]);}page_header(($B!=""?lang(251):lang(159)),$j,array("table"=>$a),h($B!=""?$B:$a),$dh,doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",)));echo'
<form action="" method="post">
<p><label>',lang(211),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off"></label>
<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'',lang(18),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$Bl=trigger_options();$K=trigger($B,$a);$dh=($B!=""&&!$K);$Pg=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$K+=array("Trigger"=>strtr($Pg,array("{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row")));if($_POST){if(!$j&&in_array($_POST["Timing"],$Bl["Timing"])&&in_array($_POST["Event"],$Bl["Event"])&&in_array($_POST["Type"],$Bl["Type"])){$uh=" ON ".table($a);$Gc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$uh:"");$_=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Gc,$_,lang(252));else{if($B!="")queries($Gc);queries_redirect($_,($B!=""?lang(253):lang(254)),queries(create_trigger($uh,$_POST)));if($B!="")queries(create_trigger($uh,$K+array("Type"=>reset($Bl["Type"]))));}}$K=$_POST;}page_header(($B!=""?lang(255):lang(162)),$j,array("table"=>$a),h($B!=""?$B:$a),$dh,doc_link(array('sql'=>"create-trigger.html",)));$Sg=strtr(preg_quote($Pg),array('\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)'));$Al=on('change','triggerChange',"^$Sg$",$Pg);$jh=on('input','triggerChange',"^$Sg$",$Pg);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(256),'<td>',html_select("Timing",$Bl["Timing"],$K["Timing"],$Al),'<tr><th>',lang(257),'<td>',html_select("Event",$Bl["Event"],$K["Event"],$Al),(in_array("UPDATE OF",$Bl["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'$jh>":""),'<tr><th>',lang(49),'<td>',html_select("Type",$Bl["Type"],$K["Type"],$Al),'<tr><th>',lang(211),'<td><input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($de,array$Li,$d,$uh){if(!$Li)return
true;if($Li==array("ALL PRIVILEGES","GRANT OPTION"))return($de=="GRANT"?queries("$de ALL PRIVILEGES$uh WITH GRANT OPTION"):queries("$de ALL PRIVILEGES$uh")&&queries("$de GRANT OPTION$uh"));return
queries("$de ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$Li).$d).$uh);}$ea=$_GET["user"];$Li=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$Gb)$Li[$Gb=="File access on server"?"Server Admin":$Gb][$K["Privilege"]]=$K["Comment"];}unset($Li["Server Admin"]["Usage"]);foreach($Li["Tables"]as$w=>$X)unset($Li["Databases"][$w]);$Vg=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$Vg[$X]=(array)$Vg[$X]+idx($_POST["grants"],$w,array());}$ee=array();$I=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])):null);$dh=(isset($_GET["host"])&&!$I);if($I){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$bg,PREG_SET_ORDER)){foreach($bg
as$X){if($X[1]!="USAGE")$ee["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$ee["$A[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$j){$th=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $th",ME."privileges=",lang(258));else{$Zg=q($_POST["user"])."@".q($_POST["host"]);$ii=$_POST["pass"];$Ob=false;$I=true;if($th!=$Zg){$Ob=queries("CREATE USER $Zg IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($ii));$I=$Ob;}elseif($ii!="")$I=queries("SET PASSWORD FOR $Zg = ".(min_version(8,99)||$_POST["hashed"]?q($ii):"PASSWORD(".q($ii).")"));if($I){$qj=array();foreach($Vg
as$hh=>$de){if(isset($_GET["grant"]))$de=array_filter($de);$de=array_keys($de);if(isset($_GET["grant"]))$qj=array_diff(array_keys(array_filter($Vg[$hh],'strlen')),$de);elseif($th==$Zg){$ph=array_keys((array)$ee[$hh]);$qj=array_diff($ph,$de);$de=array_diff($de,$ph);unset($ee[$hh]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$hh,$A)&&(!grant("REVOKE",$qj,$A[2]," ON $A[1] FROM $Zg")||!grant("GRANT",$de,$A[2]," ON $A[1] TO $Zg"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($th!=$Zg)queries("DROP USER $th");elseif(!isset($_GET["grant"])){foreach($ee
as$hh=>$qj){if(preg_match('~^(.+)(\(.*\))?$~U',$hh,$A))grant("REVOKE",array_keys($qj),$A[2]," ON $A[1] FROM $Zg");}}}if($I&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(259):lang(260)),$I);if($Ob)connection()->query("DROP USER $Zg");}}page_header((isset($_GET["host"])?lang(33).": ".h("$ea@$_GET[host]"):lang(171)),$j,array("privileges"=>array('',lang(71))),"",$dh,doc_link(array('sql'=>"grant.html",'mariadb'=>"grant")));$K=$_POST;if($K)$ee=$Vg;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$ee[(DB==""||$ee?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(31),'<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>',lang(33),'<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>',lang(34),'<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],lang(261),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(71);$r=0;foreach($ee
as$hh=>$de){echo'<th>'.($hh!="*.*"?"<input name='objects[$r]' value='".h($hh)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(31),"Databases"=>lang(35),"Tables"=>lang(150),"Procedures"=>lang(262),)as$Gb=>$nc){foreach((array)$Li[$Gb]as$Ki=>$xb){echo"<tr><td".($nc?">$nc<td":" colspan='2'").' lang="en" title="'.h($xb).'">'.h($Ki);$r=0;foreach($ee
as$hh=>$de){$B="'grants[$r][".h(strtoupper($Ki))."]'";$Y=$de[strtoupper($Ki)];if($Gb=="Server Admin"&&$hh!=(isset($ee["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($Y?" selected":"").">".lang(263)."<option value='0'".($Y=="0"?" selected":"").">".lang(264)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($Y?" checked":"").($Ki=="All privileges"?" id='grants-$r-all'":($Ki=="Grant option"?"":on('click','grantsClick',"grants-$r-all"))).">","</label>";$r++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(18),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(146),'\'',confirm(lang(201,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$j){$_f=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$_f++;}queries_redirect(ME."processlist=",lang(265,$_f),$_f||!$_POST["kill"]);}}page_header(lang(135),$j);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$r=-1;foreach(adminer()->processList()as$r=>$K){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$w=>$X)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$w=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='sql jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($K["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($K["db"])."&":ME)."sql=".url_escape($X)).'">'.lang(266).'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($r+1)."/".lang(267,max_connections()),"<p><input type='submit' value='".lang(268)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$l=fields($a);$Qd=column_foreign_keys($a);$oh=$S["Oid"];$sj=array();$d=array();$Ij=array();$Fh=array();$hl=null;foreach($l
as$w=>$k){$B=adminer()->fieldName($k);$Rg=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$B!=""){$d[$w]=$Rg;if(is_shortable($k))$hl=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$B!="")$Ij[$w]=$Rg;if(isset($k["privileges"]["order"])&&$B!="")$Fh[$w]=$Rg;$sj+=$k["privileges"];}list($N,$q)=adminer()->selectColumnsProcess($d,$v);$N=array_unique($N);$q=array_unique($q);$nf=count($q)<count($N);$Z=adminer()->selectSearchProcess($l,$v,$S);$D=adminer()->selectOrderProcess($l,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Nl=>$K){$Ba=convert_field($l[key($K)]);$N=array($Ba?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($Nl,true),$l);$J=driver()->select($a,$N,$Z,$N);if($J)echo
first($J->fetch_row());}exit;}$Hi=$Pl=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$Hi=array_flip($u["columns"]);$Pl=($N?$Hi:array());foreach($Pl
as$w=>$X){if(in_array(idf_escape($w),$N))unset($Pl[$w]);}break;}}if($oh&&!$Hi){$Hi=$Pl=array($oh=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($oh));}if($_POST&&!$j){$vm=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$jb=array();foreach($_POST["check"]as$gb)$jb[]=where_check($gb,$l);$vm[]="((".implode(") OR (",$jb)."))";}$xm=$vm;$vm=($vm?"\nWHERE ".implode(" AND ",$vm):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Lj=($N?:array("*"));$Ib=convert_fields($d,$l,$N);if($Ib)$Lj[]=substr($Ib,2);$H="";if(is_array($_POST["check"])&&!$Hi){$Vd=implode(", ",$Lj)."\nFROM ".table($a);$he=($q&&$nf?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):"");$Kl=array();foreach($_POST["check"]as$X)$Kl[]="(SELECT".limit($Vd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l).$he,1).")";$H=implode(" UNION ALL ",$Kl);}adminer()->dumpData($a,"table",$H,$Lj,$xm,($nf?$q:array()),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Qd)){if($_POST["save"]||$_POST["delete"]){$I=true;$oa=0;$Qa=false;$P=array();if(!$_POST["delete"]){foreach($l
as$B=>$X){$t=bracket_escape($B);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($l[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$P[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}}if($_POST["delete"]||$P){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($P)).")\nSELECT ".implode(", ",$P)."\nFROM ".table($a):"");if($_POST["all"]||($Hi&&is_array($_POST["check"]))||$nf){$I=($_POST["delete"]?driver()->delete($a,$vm):($_POST["clone"]?queries("INSERT $H$vm".driver()->insertReturning($a)):driver()->update($a,$P,$vm)));$oa=connection()->affected_rows;if(is_object($I))$oa+=$I->num_rows;}else{$Qa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$um="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l);$I=($_POST["delete"]?driver()->delete($a,$um,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$um)):driver()->update($a,$P,$um,1)));if(!$I)break;$oa+=connection()->affected_rows;}if($Qa&&$I&&!driver()->commit())$I=false;}}$tg=lang(172,$oa);if($_POST["clone"]&&$I&&$oa==1){$Hf=last_id($I);if($Hf)$tg=lang(194," $Hf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$tg,$I);if($Qa)driver()->rollback();if(!$_POST["delete"]){$zi=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$zi),$zi,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$oa=0;$Qa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Nl=>$K){$P=array();foreach($K
as$w=>$X){$w=bracket_escape($w,true);$P[idf_escape($w)]=(preg_match('~char|text~',$l[$w]["type"])||$X!=""?adminer()->processInput($l[$w],$X):"NULL");}$I=driver()->update($a,$P," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Nl,true),$l),($nf||$Hi?0:1)," ");if(!$I)break;$oa+=connection()->affected_rows;}if($Qa)$I=$I&&driver()->commit();queries_redirect(remove_from_uri(),lang(172,$oa),$I);if($Qa)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$Dd=get_file("csv_file",true);if(!is_string($Dd))$j=upload_error($Dd);elseif(!preg_match('~~u',$Dd))$j=lang(269);else{$ub=array_keys($l);$Qj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Sb=parse_csv($Dd,$Qj);$oa=count($Sb);driver()->begin();$L=array();foreach($Sb
as$w=>$km){if(!$w&&!array_diff($km,$ub)){$ub=$km;$oa--;}else{$P=array();foreach($km
as$r=>$qb)$P[idf_escape($ub[$r])]=($qb==""&&$l[$ub[$r]]["null"]?"NULL":q(csv_value($qb)));$L[]=$P;}}$I=(!$L||driver()->insertUpdate($a,$L,$Hi));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(270,$oa),$I);driver()->rollback();}}}}$Pk=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(53).": $Pk",$j,array(),"",(!$l&&support("table")),($l?doc_link(array(JUSH=>driver()->tableHelp($a,is_view($S)))):""));$P=null;if(isset($sj["insert"])||!support("table")){$P="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$A)?$A[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$P
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$P);if(!$d&&support("table"))echo"<p class='error'>".lang(271)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($N,$d);adminer()->selectSearchPrint($Z,$Ij,$v,$S);adminer()->selectOrderPrint($D,$Fh,$v);adminer()->selectLimitPrint($y);if($hl!==null)adminer()->selectLengthPrint($hl);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(116).' '.lang(117)."\n";page_footer();exit;}}$E=$_GET["page"];$Td=null;if($E=="last"){$Td=get_val(count_rows($a,$Z,$nf,$q));$E=floor(max(0,intval($Td)-1)/$y);}$Kj=$N;$ge=$q;if(!$Kj){$Kj[]="*";$Ib=convert_fields($d,$l,$N);if($Ib)$Kj[]=substr($Ib,2);}foreach($N
as$w=>$X){$k=$l[idf_unescape($X)];if($k&&($Ba=convert_field($k)))$Kj[$w]="$Ba AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$X){if(isset($Kj[$w])&&$X["fun"])$Kj[$w].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$nf&&$Pl){foreach($Pl
as$w=>$X){$Kj[]=idf_escape($w);if($ge)$ge[]=idf_escape($w);}}$I=driver()->select($a,$Kj,$Z,$ge,$D,$y,$E,true);if(!is_object($I))echo"<p class='error'>".(adminer()->error()?:lang(26))."\n";else{if(JUSH=="mssql"&&$E)$I->seek($y*$E);$Tc=array();$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$re=($y&&(support("cursor")?$_GET["next"]!="":count($L)>=$y));if(is_ajax()&&$re)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$L){$kg=max_input_vars(count($L[0])+1,20);echo($kg&&count($L)>$kg?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($Vl).">\n";if($_GET["page"]!="last"&&$y&&$q&&$nf&&JUSH=="sql")$Td=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".lang(16)."\n";else{$Ma=adminer()->backwardKeys($a,$Pk);$pj=array();reset($N);foreach($L[0]as$w=>$X){if(!isset($Pl[$w])){$X=idx($_GET["columns"],key($N))?:array();$pj[$w]=array("fun"=>$X["fun"],"col"=>($N?$X["col"]:$w));next($N);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$q&&$N?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(272)."'".on('click','formCheck','^check').">");$Tg=array();$Yi=1;foreach($pj
as$w=>$X){$k=$l[$X["col"]];$B=($k?adminer()->fieldName($k,$Yi):($X["fun"]?($X["col"]!=""?h($X["col"]):"*"):h($w)));if($B!=""){$Yi++;$Tg[$w]=$B;$c=idf_escape($w);$Fe=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$nc="&desc[0]=1";$hk=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$jk=($hk==$c||$hk==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($jk?" aria-sort='".($hk==$D[0]?"ascending":"descending")."'":"").">";$Zd=apply_sql_function(h($X["fun"]),$B);$ik=isset($k["privileges"]["order"])||$X["fun"];echo($ik?"<a href='".h($Fe.($jk&&$hk==$D[0]?$nc:''))."'>$Zd</a>":$Zd),doc_link(array(JUSH=>$k["doc"]));$sg=($ik?"<a href='".h($Fe.$nc)."' title='".lang(59)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($k["privileges"]["where"]))$sg
.="<a href='#fieldset-search' title='".lang(56)."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($sg?"<span class='column'>$sg</span>":"");}}$Of=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$w=>$X)$Of[$w]=max($Of[$w],min(40,utf8_length((string)$X)));}}$Ae=array();$_e=array();foreach((array)$_GET["where"]as$X){$X+=array("col"=>"","op"=>"","val"=>"");$qb=$X["col"];$Hj=$X["val"];if(!is_array($Hj)&&($Hj!=""||preg_match('~NULL$~',$X["op"]))&&(!$X["op"]||in_array($X["op"],adminer()->operators($S)))){$Qf=strtr(preg_quote($Hj),array("%"=>".*?","_"=>"."));$oi=array("LIKE %%"=>$Qf,"ILIKE %%"=>$Qf,"REGEXP"=>$Hj)+(JUSH=="pgsql"?array("~"=>$Hj,"~*"=>$Hj):array())+($qb!=""?array():array("="=>'^'.preg_quote($Hj).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Hj)))).')\z',"LIKE"=>"^$Qf\\z","ILIKE"=>"^$Qf\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Hj).'(?=,|\z)',));foreach(($qb!=""?array($qb=>$l[$qb]):$l)as$B=>$k){if($qb!=""||is_searchable($k,$X)){$wh=$X["op"]?:(!preg_match('~'.text_type().'~',$k["type"])?"IN":(preg_match('~%~',$Hj)?"LIKE":"LIKE %%"));if(isset($oi[$wh])){$mb=preg_match('~^ILIKE|\*$~',$wh)||($wh!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$Ae[$B][]="(?".($mb?"i":"").":$oi[$wh])";}elseif($wh=="IS NULL"&&$qb=="")$_e[$B]=true;}}}}echo($Ma?"<th>".lang(273):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$Qd)as$Ng=>$K){$Ml=unique_array($L[$Ng],$v);if(!$Ml){$Ml=array();foreach($L[$Ng]as$w=>$X){if(!in_array(idx(idx($pj,$w,array()),"fun"),driver()->grouping))$Ml[$w]=$X;}}$Nl="";$r=0;foreach($Ml
as$w=>$X){$oj=idx($pj,$w,array());if($N&&$oj&&!isset($l[$oj["col"]]))continue;$Zd=idx($oj,"fun","");$qb=($Zd?$oj["col"]:$w);$k=(array)$l[$qb];$mf=is_blob($k);if(!$Zd&&strlen($X)>64&&driver()->md5(idf_escape($qb),$k)){$Zd="md5";$X=md5($mf?(string)driver()->value($X,$k):$X);}if($Zd){$Nl
.="&fun[$r]=".url_escape($Zd)."&col[$r]=".url_escape($qb).($X!==null?"&val[$r]=".url_escape($X===false?"f":$X):"");$r++;}else$Nl
.="&".($X!==null?"where[".url_escape(bracket_escape($qb))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($qb));}echo"<tr>".(!$q&&$N?"":"<td class='hover check sticky'>".($nf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Nl)."' class='edit'>".lang(274)."</a> ").checkbox("check[]",substr($Nl,1),in_array(substr($Nl,1),(array)$_POST["check"])));foreach($K
as$w=>$X){if(isset($Tg[$w])){$Zd=$pj[$w]["fun"];$qb=$pj[$w]["col"];$k=(array)$l[$w];if($X!=""&&(!isset($Tc[$w])||$Tc[$w]!=""))$Tc[$w]=(is_mail($X)?$Tg[$w]:"");$z="";if(is_blob($k)&&$X!="")$z=ME.'download='.url_escape($a).'&field='.url_escape($w).$Nl;if(!$z&&$X!==null){foreach((array)$Qd[$w]as$n){if(count($Qd[$w])==1||end($n["source"])==$w){$z="";foreach($n["source"]as$r=>$kk)$z
.=where_link($r,$n["target"][$r],$L[$Ng][$kk]);$z=($n["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($n["db"]),ME):ME).'select='.url_escape($n["table"]).$z;if($n["ns"]!="")$z=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($n["ns"]),$z);if(count($n["source"])==1)break;}}}if($Zd=="count"&&$qb==""){$z=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Ml))$z
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($Ml
as$wf=>$W){if(idx(idx($pj,$wf,array()),"fun")){$z="";break;}$z
.=where_link($r++,$wf,$W);}}$Ge=select_value($X,$z,$k,$hl,($Zd?array():idx($Ae,$w,array())));if($X===null&&!$Zd&&isset($_e[$w]))$Ge="<mark>$Ge</mark>";$t=bracket_escape($Nl);$s=h("val[$t][".bracket_escape($w)."]");$Ai=idx(idx($_POST["val"],$t),bracket_escape($w));$Sl=idx($k["privileges"],"update")&&!is_identity_always($k);$Oc=$L[$Ng][$w];$Pc=!is_array($K[$w])&&!is_blob($k)&&is_utf8($Oc)&&!$Zd&&!$k["generated"]&&$Sl;$U=($Zd=="min"||$Zd=="max"?$l[$qb]["type"]:$k["type"]);$gl=preg_match('~text|json|lob~',$U);$of=preg_match(number_type(),$U)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Zd);echo"<td id='$s'".($of&&($X===null||is_numeric(strip_tags($Ge))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Pc&&$Oc!==null)||$Ai!==null){$me=h($Ai!==null?$Ai:$Oc);echo">".($gl?"<textarea name='$s' cols='30' rows='".(substr_count($Oc,"\n")+1)."'>\n$me</textarea>":"<input name='$s' value='$me' size='$Of[$w]'>");}else{$Yf=strpos($Ge,"<i>…</i>");$Nc=adminer()->editVal($Oc,$k);echo($Sl?" data-text='".($Yf?2:($gl?1:0))."'".($Pc?"":" data-warning='".lang(275)."'").($Pc&&!$Yf&&html_entity_decode(strip_tags($Ge),ENT_QUOTES,"UTF-8")!==$Nc?" data-value='".h($Nc)."'":""):"").">$Ge";}}}if($Ma)echo"<td>";adminer()->backwardKeysPrint($Ma,$L[$Ng]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$na=get_settings("adminer_import");if($L||$E||$re){$ld=true;if($_GET["page"]!="last"){if(!$y||(count($L)<$y&&($L||!$E)))$Td=($E?$E*$y:0)+count($L);elseif(JUSH!="sql"||!$nf){$Td=($nf?null:found_rows($S,$Z));$ld=!driver()->hasEstimatedRows();if($Td===null||(!$ld&&$Td<max(1e4,2*($E+1)*$y))){$Td=first(slow_query(count_rows($a,$Z,$nf,$q)));$ld=true;}}}if(!support("cursor"))$re=(($Td===false?count($L)+1:$Td-$E*$y)>$y);$Th=($y&&($re||$E));if($Th)echo($re?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(276)).'>'.lang(277).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Th){$ig=($Td===false?$E+($L?(count($L)>=$y?2:1):0):floor(($Td-1)/$y));echo"<fieldset><legend>".lang(278)."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($ig,$E+5);$r++)echo
pagination($r,$E);if($ig>0)echo($E+5<$ig?" …":""),($ld&&$Td!==false?pagination($ig,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$ig'>".lang(279)."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($re?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(280)."</legend>";$vc=($ld?"":"~ ").$Td;$Af=($Td!==false?($ld?"":"~ ").lang(176,$Td):"");echo
checkbox("all",1,0,$Af,on('click','countRows',$vc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(177)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(281),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(18),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(145),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(14),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(266),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(22),'\'',confirm(),'>
</div></fieldset>
';$Rd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]||($c["col"]!=""&&!isset($l[$c["col"]]))){unset($Rd['sql']);break;}}if($Rd){print_fieldset("export",lang(76)." <span id='selected2'></span>");$Rh=adminer()->dumpOutput();echo($Rh?html_select("output",$Rh,$na["output"])." ":""),html_select("format",$Rd,$na["format"])," <input type='submit' name='export' value='".lang(76)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Tc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(75)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Vl?input_hidden(ini_get("session.upload_progress.name"),$Vl):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$na["format"])." <input type='submit' name='import' value='".lang(75)."'>".($Vl?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$q&&$N?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$xk=isset($_GET["status"]);page_header($xk?lang(137):lang(136));$lm=($xk?adminer()->showStatus():adminer()->showVariables());if(!$lm)echo"<p class='message'>".lang(16)."\n";else{echo"<table>\n";foreach($lm
as$K){echo"<tr>";$w=array_shift($K);echo"<th><code class='jush-".JUSH.($xk?"status":"set")."'>".h($w)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Fk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$S){json_row("Comment-$B",h($S["Comment"]).($S["Error"]?" <span class='error'>".h($S["Error"])."</span>":""));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$B",h($S[$w]));foreach(array_keys($Fk+array("Auto_increment"=>0,"Rows"=>0))as$w){if(array_key_exists($w,$S))json_row("$w-$B",format_status($S,$w));if($S[$w]!=""&&isset($Fk[$w]))$Fk[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}}}if(function_exists('Adminer\db_status'))$Fk=db_status();foreach($Fk
as$w=>$X)json_row("sum-$w",adminer()->formatSizeValue($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$pg=ME.(isset($_GET["select"])?"select=&":"");$Yk=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Yk&&!$j&&!$_POST["search"]){$I=true;$tg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$tg=lang(282);}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$tg=lang(283);}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$tg=lang(284);}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$tg=lang(285);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$tg
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("DBCC CHECKTABLE (".q(table($R)).") WITH TABLERESULTS")as$K)$tg
.="<b>".h($R)."</b>: ".h($K["MessageText"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$tg=lang(286);}elseif(!$_POST["tables"])$tg=lang(13);elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$tg
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(relative_uri(),$tg,$I);}page_header(($_GET["ns"]==""?lang(35).": ".h(DB):lang(168).": ".h($_GET["ns"])),$j,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$Wd=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(287)."</h3>\n";$Xk=($Wd?table_status():tables_list());if(!$Xk)echo"<p class='message'>".lang(13)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(288)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(56)."'>\n","</div></fieldset>\n";if(!$j&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(170).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($pg,0,-1)).'">'.lang(150).'</a>';$d=array("Engine"=>array(lang(289).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$d["Collation"]=array(lang(141).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$d["Data_length"]=array(lang(290).doc_link(array('sql'=>'show-table-status.html',)),"create",lang(44),);if(support("indexes"))$d["Index_length"]=array(lang(291).doc_link(array('sql'=>'show-table-status.html',)),"indexes",lang(153),);$d["Data_free"]=array(lang(292).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(45));if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array(lang(51).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(44),);$d["Rows"]=array(lang(293).doc_link(array('sql'=>'show-table-status.html',)),"select",lang(41),);if(support("comment"))$d["Comment"]=array(lang(50).doc_link(array('sql'=>'show-table-status.html',)),);$Ca=array('Engine','Collation','Comment');foreach($d
as$w=>$c)echo"<th".($D==$w?" aria-sort='".(in_array($w,$Ca)?"ascending":"descending")."'":"")."><a href='".h($pg)."order=$w'>$c[0]</a>";echo"<tbody>\n";if($D){uasort($Xk,function($ga,$Ja)use($D,$Ca){$J=($ga[$D]<$Ja[$D]?-1:($ga[$D]>$Ja[$D]?1:0));return(in_array($D,$Ca)?$J:-$J);});}$T=0;$Fk=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($Xk
as$B=>$xk){$om=($Wd?is_view($xk):$xk!==null&&!preg_match('~table|sequence~i',$xk));$xk=($Wd?$xk:array('Engine'=>$xk));$s=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($om?"views[]":"tables[]"),$B,in_array("$B",$Yk,true),"","","",$s),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".lang(42)."' id='$s'>".h($B).'</a>':h($B));if($om&&!preg_match('~materialized~i',$xk['Engine'])){$ml=lang(149);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".lang(43)."'>$ml</a>":$ml),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".lang(41)."'>?</a>";if(support("comment"))echo'<td>'.h($xk['Comment']);}else{if($Wd){foreach(array_keys($Fk)as$w)$Fk[$w]+=($xk["Engine"]!="InnoDB"||$w!="Data_free"?idx($xk,$w):0);}foreach($d
as$w=>$c){$s=" id='$w-".h($B)."'";echo($c[1]?"<td align='right'>".($w=="Index_length"&&$xk["invalid_index"]?"<span class='error'>INVALID</span> ":"")."<a href='".h(ME."$c[1]=").url_escape($B)."'$s title='$c[2]'>".format_status($xk,$w)."</a>":"<td$s>".h(idx($xk,$w,'?')).($w=="Comment"&&$xk["Error"]?" <span class='error'>".h($xk["Error"])."</span>":""));}$T++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(267,count($Xk)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Wd&&function_exists('Adminer\db_status'))$Fk=db_status();foreach($Fk
as$w=>$Ek)echo($d[$w]?"<td align='right' id='sum-$w'>".($Wd?adminer()->formatSizeValue($Ek):""):"");echo"\n","</table>\n",($Wd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$hm="<input type='submit' value='".lang(294)."'".on_help("VACUUM")."> ";$Ah="<input type='submit' name='optimize' value='".lang(295)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Ii=(JUSH=="sqlite"?$hm."<input type='submit' name='check' value='".lang(296)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$hm.$Ah:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(296)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(297)."'".on_help("ANALYZE TABLE")."> ".$Ah."<input type='submit' name='check' value='".lang(296)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(298)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(299)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(146)."'".confirm().on_help("DROP TABLE").">":"");echo($Ii?"<div class='footer'><div>\n<fieldset><legend>".lang(145)." <span id='selected'></span></legend><div>$Ii\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(300)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(129)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(23)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(301)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(77)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(231)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(72)."</h3>\n";$_j=routines();if($_j){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(211).'<th>'.lang(49).'<th>'.lang(247)."<td class='hover'><tbody>\n";foreach($_j
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$B).'"'.' title="'.($K["DTD_IDENTIFIER"]=="trigger"?lang(160):lang(218)).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$B).'">'.lang(156)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(246).'</a>':'').'<a href="'.h(ME).'function=">'.lang(245)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(74)."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".lang(211)."<th>".lang(302)."<th>".lang(237)."<th>".lang(238)."<td class='hover'><tbody>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?lang(303)."<td>".h($K["Execute at"]):lang(239)." ".h($K["Interval value"])." ".h($K["Interval field"])."<td>".h($K["Starts"])),"<td>".h($K["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($K["Name"]).'">'.lang(156).'</a>';echo"</table>\n";$id=get_val("SELECT @@event_scheduler");if($id&&$id!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($id)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(236)."</a>\n","</div>\n";}}}}page_footer();