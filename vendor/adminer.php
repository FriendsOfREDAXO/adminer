<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.3.0
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.3.0";error_reporting(24575);set_error_handler(function($ed,$hd){return!!preg_match('~^Undefined (array key|offset|index)~',$hd);},E_WARNING|E_NOTICE);if(!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"))||ini_get("filter.default_flags")){foreach(['_GET','_POST','_COOKIE','_SERVER']as$X){$Tl=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Tl)$$X=$Tl;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($Db=null){return($Db?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Rb=adminer()->credentials();$I=Driver::connect($Rb[0],$Rb[1],$Rb[2]);return(is_object($I)?$I:null);}function
idf_unescape($s){if(!preg_match('~^[`\'"[]~',$s))return$s;$Jf=substr($s,-1);return
str_replace($Jf.$Jf,$Jf,substr($s,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($Aa,$v,$h=null){return($Aa&&array_key_exists($v,$Aa)?$Aa[$v]:$h);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_user_type($U){return
in_array($U,idx(driver()->structuredTypes(),lang(0),[]));}function
full_type_sql(array$j){$U=$j["type"];return(is_user_type($U)?idf_escape($U).substr($j["full_type"],strlen($U)):$j["full_type"]);}function
is_searchable(array$j,array$X){if(!isset($j["privileges"]["where"]))return
false;if(preg_match('~NULL$~',$X["op"]))return
true;$U=$j["type"];$Mj=$X["val"];$Ra='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Ra~",$U))return
false;if(preg_match(number_type(),$U)){$kh='-?\d+(\.\d+)?';$Mg='';if(preg_match('~IN$~',$X["op"])){$Mj=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$Mj);$Mg="( *, *$kh)*";}elseif($X["op"]=="BETWEEN")$Mg="\\s+AND\\s+$kh";return(bool)preg_match('~^'.$kh.$Mg.'$~i',$Mj);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$Mj);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$Mj);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Mj);return
true;}function
bracket_escape($s,$Ka=false){static$Al=[':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5'];return
strtr($s,($Ka?array_flip($Al):$Al));}function
url_escape($Q){static$Al=[];if(!$Al){$Al=[' '=>'+'];foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$eb)$Al[$eb]=sprintf('%%%02X',ord($eb));for($q=0;$q<256;$q++){if($q<32||$q>126)$Al[chr($q)]=sprintf('%%%02X',$q);}}return
strtr((string)$Q,$Al);}function
min_version($sm,$eg="",$Db=null){$Db=connection($Db);$ck=$Db->server_info;if($eg&&preg_match('~([\d.]+)-MariaDB~',$ck,$_)){$ck=$_[1];$sm=$eg;}return$sm&&version_compare($ck,$sm)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($Fh,$Y){return(function_exists('ini_set')?\ini_set($Fh,$Y):false);}function
ini_bool($cf){$X=ini_get($cf);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($cf){$X=ini_get($cf);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($J,$Th){$hg=(int)ini_get("max_input_vars");return($hg?(int)floor(($hg-$Th)/$J):0);}function
max_input_vars_error(){$cf="max_input_vars";return
lang(1,"<b>$cf = ".ini_get($cf)."</b>");}function
sid(){static$I;if($I===null)$I=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$I;}function
set_password($rm,$N,$V,$E){$_SESSION["pwds"][$rm][$N][$V]=($_COOKIE["adminer_key"]&&is_string($E)?[encrypt_string($E,$_COOKIE["adminer_key"])]:$E);}function
get_password(){$I=get_session("pwds");if(is_array($I))$I=($_COOKIE["adminer_key"]?decrypt_string($I[0],$_COOKIE["adminer_key"]):false);return$I;}function
get_val($G,$j=0,$Cb=null){$Cb=connection($Cb);$H=$Cb->query($G);if(!is_object($H))return
false;$J=$H->fetch_row();return($J?$J[$j]:false);}function
get_vals($G,$c=0){$I=[];$H=connection()->query($G);if(is_object($H)){while($J=$H->fetch_row())$I[]=$J[$c];}return$I;}function
get_key_vals($G,$Db=null,$fk=true){$Db=connection($Db);$I=[];$H=$Db->query($G);if(is_object($H)){while($J=$H->fetch_row()){if($fk)$I[$J[0]]=$J[1];else$I[]=$J[0];}}return$I;}function
get_rows($G,$Db=null,$i="<p class='error'>"){$Cb=connection($Db);$I=[];$H=$Cb->query($G);if(is_object($H)){while($J=$H->fetch_assoc())$I[]=$J;}elseif(!$H&&!$Db&&$i&&(defined('Adminer\PAGE_HEADER')||$i=="-- "))echo$i.adminer()->error()."\n";return$I;}function
unique_array($J,array$u){foreach($u
as$t){if(preg_match("~^(PRIMARY|UNIQUE)$~",$t["type"])&&!$t["partial"]&&!$t["invalid"]){$I=[];foreach($t["columns"]as$v){if(!isset($J[$v]))continue
2;$I[$v]=$J[$v];}return$I;}}}function
where_function($ce,$c,array$j){if($ce=="md5")return
driver()->md5($c,$j)?:$c;return(in_array($ce,driver()->functions)||in_array($ce,driver()->grouping)?apply_sql_function($ce,$c):$c);}function
where(array$Z,array$k=[]){$I=[];foreach((array)$Z["where"]as$v=>$X){$v=bracket_escape($v,true);$c=idf_escape($v);$j=idx($k,$v,[]);$Ed=$j["type"];$pf=$j&&(is_blob($j)||preg_match('~binary~',$Ed));$I[]=$c.($pf&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$Ed=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$j["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="pgsql"&&preg_match('~\.~',$Ed)?"::text = ".q($X):(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Ed,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($j,q($X))))))));if(JUSH=="sql"&&preg_match('~char|text~',$Ed)&&preg_match("~[^ -@]~",$X))$I[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$v)$I[]=idf_escape($v)." IS NULL";foreach((array)$Z["col"]as$q=>$rb){$X=idx($Z["val"],$q);$I[]=where_function(idx($Z["fun"],$q),idf_escape($rb),idx($k,$rb,[])).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$I);}function
where_columns(array$k){$I=[];foreach((array)$_GET["null"]as$v)$I[$v]=true;foreach(array_keys((array)$_GET["where"])as$v)$I[bracket_escape($v,true)]=true;foreach((array)$_GET["col"]as$rb)$I[$rb]=true;return
array_intersect_key($I,$k);}function
where_check($X,array$k=[]){parse_str($X,$hb);return
where($hb,$k);}function
where_link($q,$c,$Y,$Ch="="){$_h=($Y!==null?$Ch:"IS NULL");return"&where[$q][col]=".url_escape($c).($_h!=first(adminer()->operators())?"&where[$q][op]=".url_escape($_h):"")."&where[$q][val]=".url_escape($Y);}function
convert_fields(array$d,array$k,array$M=[]){$I="";foreach($d
as$v=>$X){if($M&&!in_array(idf_escape($v),$M))continue;$Ba=convert_field($k[$v]);if($Ba)$I
.=", $Ba AS ".idf_escape($v);}return$I;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),[";"=>"%3B",","=>"%2C"]);}function
cookie($A,$Y,$Tf=2592000){header("Set-Cookie: $A=".rawurlencode($Y).($Tf?"; expires=".gmdate("D, d M Y H:i:s",time()+$Tf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($A=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
php_error_text($i){return(ini_bool("html_errors")?html_entity_decode(strip_tags($i)):$i);}function
php_errors($ab){$gd=[];set_error_handler(function($ed,$i)use(&$gd){$gd[]=preg_replace('~^\w+\([^)]*\)( \[[^]]*])?:\s*~','',php_error_text($i));return
true;},E_WARNING|E_NOTICE);$ab();restore_error_handler();return$gd;}function
get_url($cm,$Ib){$gd=php_errors(function()use($cm,$Ib,&$I,&$ye){$http_response_header=null;$I=file_get_contents($cm,false,$Ib);$ye=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);});return[$I,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($ye,0,''),$_)?$_[1]:''),(array)$ye,($I===false?implode("\n",$gd):''),];}function
json_decode_exact($xf){$xf=str_replace(['\\\\','\\"'],['\\u005c','\\u0022'],$xf);$xf=preg_replace('~"(\\\\u0001[^"]*+")|"[^"]*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$xf);return
json_decode(preg_replace('~"[^"]*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$xf));}function
json_scalar($X){return(is_string($X)&&substr($X,0,1)=="\1"?substr($X,1):$X);}function
json_encode_exact($X,$Nd=0){$xf=str_replace(['\\\\','\\"'],["\2","\3"],json_encode($X,$Nd));$xf=preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001[^"]*+")|"[^"]*+"(*SKIP)(*FAIL)~','$1$2$3',$xf);return
str_replace(["\2","\3"],['\\\\','\\"'],$xf);}function
get_settings($Lb){parse_str($_COOKIE[$Lb],$gk);return$gk;}function
get_setting($v,$Lb="adminer_settings",$h=null){return
idx(get_settings($Lb),$v,$h);}function
save_settings(array$gk,$Lb="adminer_settings"){$Y=http_build_query($gk+get_settings($Lb));cookie($Lb,$Y);$_COOKIE[$Lb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&session_status()==PHP_SESSION_NONE)session_start();}function
stop_session($Qd=false){$fm=ini_bool("session.use_cookies");if(!$fm||$Qd){session_write_close();if($fm&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($v){return$_SESSION[$v][DRIVER][SERVER][$_GET["username"]];}function
set_session($v,$X){$_SESSION[$v][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($rm,$N,$V,$g=null){$bm=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($g!==null?"db|":"").($rm=='mssql'||$rm=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$bm,$_);return"$_[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($rm!="server"||$N!=""?url_escape($rm)."=".url_escape($N)."&":"")."username=".url_escape($V).($g!=""?"&db=".url_escape($g):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($z,$xg=null,$P=303){if($xg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($z!==null?$z:$_SERVER["REQUEST_URI"]))][]=$xg;}if($z!==null){if($z=="")$z=".";header("Location: $z",true,$P);exit;}}function
query_redirect($G,$z,$xg,$dj=true,$od=true,$zd=false,$ol=""){if($od){$zk=microtime(true);$zd=!connection()->query($G);$ol=format_time($zk);}$tk=($G?adminer()->messageQuery($G,$ol,$zd):"");if($zd){adminer()->error
.=adminer()->error().$tk.script("messagesPrint();")."<br>";return
false;}if($dj)redirect($z,$xg.$tk);return
true;}class
Queries{static$queries=[];static$start=0;}function
remember_query($G){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$G:(preg_match('~;$~',$G)?"DELIMITER ;;\n$G;\nDELIMITER ":$G).";");}function
queries($G){remember_query($G);return
connection()->query($G);}function
apply_queries($G,array$T,$id='Adminer\table'){foreach($T
as$R){if(!queries("$G ".$id($R)))return
false;}return
true;}function
queries_redirect($z,$xg,$dj){$Vi=implode("\n",Queries::$queries);$ol=format_time(Queries::$start);return
query_redirect($Vi,$z,$xg,$dj,false,!$dj,$ol);}function
format_time($zk){return
lang(2,max(0,microtime(true)-$zk));}function
relative_uri($bm=''){return
preg_replace_callback('~^[^?]*~',function($_){return
str_replace(":","%3A",$_[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($bm?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Yh=""){return
substr(preg_replace("~(?<=[?&])($Yh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($A,$gc=false){$Gd=$_FILES[$A];if(!$Gd)return
null;foreach($Gd
as$v=>$X)$Gd[$v]=(array)$X;$I=[];foreach($Gd["error"]as$v=>$i){if($i)return$i;$l=$Gd["name"][$v];$wl=$Gd["tmp_name"][$v];$Gb=file_get_contents($gc&&preg_match('~\.gz$~',$l)?"compress.zlib://$wl":$wl);if($gc){$zk=substr($Gb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$zk))$Gb=iconv("utf-16","utf-8",$Gb);elseif($zk=="\xEF\xBB\xBF")$Gb=substr($Gb,3);}$I[]=[$l,$Gb];}return$I;}function
get_file($v,$gc=false,$oc=""){$Jd=get_files($v,$gc);if(!is_array($Jd))return$Jd;$I='';foreach($Jd
as$Gd){$Gb=$Gd[1];$I
.=$Gb;if($oc)$I
.=(preg_match("($oc\\s*\$)",$Gb)?"":$oc)."\n\n";}return$I;}function
upload_error($i){$pg=($i==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($i?lang(3).($pg?" ".lang(4,$pg):""):lang(5));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$_);$kk=strlen($_[3]);$I=number_format($X,0,".","");$I=preg_replace('~\B(?=(\d{'.(strlen($_[2])?:$kk).'})*\d{'.$kk.'}$)~',$_[1],$I);return
strtr($I,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$v){$X=idx($S,$v,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$ya=($v=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$X=in_array($v,["Data_length","Index_length","Data_free"])?adminer()->formatSizeValue($X):format_number($X);return($ya?"~ ":"").$X;}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$_d=false){$I=table_status($R,$_d);return($I?reset($I):["Name"=>$R]);}function
column_foreign_keys($R){$I=[];foreach(adminer()->foreignKeys($R)as$m){foreach($m["source"]as$X)$I[$X][]=$m;}return$I;}function
fields_from_edit(){$I=[];foreach((array)$_POST["field_keys"]as$v=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$v];$_POST["fields"][$X]=$_POST["field_vals"][$v];}}foreach((array)$_POST["fields"]as$v=>$X){$A=bracket_escape($v,true);$I[$A]=["field"=>$A,"full_type"=>"","type"=>"","privileges"=>["insert"=>1,"update"=>1,"where"=>1,"order"=>1],"null"=>true,"auto_increment"=>($A==driver()->primary),];}return$I;}function
dump_headers($Le,$Pg=false){$I=adminer()->dumpHeaders($Le,$Pg);$Vh=$_POST["output"];if($Vh!="text"||$I=="tar"){$Ab=($Vh!="text"&&$Vh!="file"&&preg_match('~^[0-9a-z]+$~',$Vh)?".$Vh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Le).".$I$Ab");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$I;}function
dump_csv(array$J){$Jl=$_POST["format"]=="tsv";foreach($J
as$v=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Jl?'\t':'[,;]|^$').'~',$X))$J[$v]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($Jl?"\t":";")),$J)."\r\n";}function
parse_csv($Ub,$Wj){$I=[];preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Ub,$fg);foreach($fg[0]as$J){preg_match_all("~((?>\"[^\"]*\")+|[^$Wj]*)$Wj~",$J.$Wj,$gg);$I[]=$gg[1];}return$I;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?idf_unescape($X):$X);}function
apply_sql_function($o,$c){return($o?($o=="unixepoch"?"DATETIME($c, '$o')":($o=="count distinct"?"COUNT(DISTINCT ":strtoupper("$o("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($l){if(is_link($l))return;$n=@fopen($l,"c+");if(!$n)return;@chmod($l,0660);if(!flock($n,LOCK_EX)){fclose($n);return;}return$n;}function
file_write_unlock($n,$Yb){rewind($n);fwrite($n,$Yb);ftruncate($n,strlen($Yb));file_unlock($n);}function
file_unlock($n){flock($n,LOCK_UN);fclose($n);}function
first(array$Aa){return
reset($Aa);}function
password_file($Ob){$l=get_temp_dir()."/adminer.key";if(!$Ob&&!file_exists($l))return'';$n=file_open_lock($l);if(!$n)return'';$I=stream_get_contents($n);if(!$I){$I=rand_string();file_write_unlock($n,$I);}else
file_unlock($n);return$I;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$y,array$j,$ml,array$si=[]){if(is_array($X)){$I="";if(array_filter($X,'is_array')==array_values($X)){$Bf=[];foreach($X
as$W)$Bf+=array_fill_keys(array_keys($W),null);foreach(array_keys($Bf)as$_f)$I
.="<th>".h($_f);foreach($X
as$W){$I
.="<tr>";foreach(array_merge($Bf,$W)as$lm)$I
.="<td>".select_value($lm,$y,$j,$ml,$si);}}else{foreach($X
as$_f=>$W)$I
.="<tr>".($X!=array_values($X)?"<th>".h($_f):"")."<td>".select_value($W,$y,$j,$ml,$si);}return"<table>$I</table>";}if(!$y)$y=adminer()->selectLink($X,$j);if($y===null){if(is_mail($X))$y="mailto:$X";if(is_url($X))$y=$X;}$X=driver()->value($X,$j);$I=adminer()->editVal($X,$j);if($I!==null){if(!is_utf8($I))$I="\0";elseif($ml!=""&&is_shortable($j))$I=shorten_utf8($I,max(0,+$ml),"",$si);else$I=highlight_matches($I,$si);}return
adminer()->selectVal($I,$y,$j,$X);}function
is_blob(array$j){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$j["type"])&&!in_array($j["type"],idx(driver()->structuredTypes(),lang(0),[]));}function
is_identity_always(array$j){return$j["auto_increment"]&&(JUSH=="mssql"||$j["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($Tc){$Da='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Fc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$ri="$Da+(\\.$Da+)*@($Fc?\\.)+$Fc";return
is_string($Tc)&&preg_match("(^$ri(,\\s*$ri)*\$)i",$Tc);}function
is_url($Q){$Fc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Fc?\\.)+$Fc(:\\d+)?(/\\S*)?(\\?\\S*)?(#\\S*)?\$~iD",$Q);}function
is_ipv6($ka){$p='[\da-f]{1,4}';$of='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($p:){7}$p|($p:){6}$of|(($p:)*$p)?::(($p:)*($p|$of))?)$~iD",$ka);}function
is_shortable(array$j){return!preg_match('~'.number_type().'|date|time|year~',$j["type"]);}function
url_host($He){return(strpos($He,":")!==false?"[$He]":$He);}function
server_parts(array$li){return["scheme"=>(string)$li["scheme"],"host"=>(string)$li["host"],"port"=>(string)$li["port"],"socket"=>(string)$li["socket"],"path"=>(string)$li["path"],];}function
parse_server($N){if($N=="")return
server_parts([]);if($N[0]==":"&&!is_ipv6($N)){$qj=substr($N,1);if(preg_match('~^\d+$~D',$qj))return
server_parts(["port"=>$qj]);return(preg_match('~^/[-\w.:/]*$~D',$qj)?server_parts(["socket"=>$qj]):null);}$Kj="";if(preg_match('~^([-+.\w]+)://~',$N,$_)){$Kj=strtolower($_[1]);$N=substr($N,strlen($_[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$N,$_))return(is_ipv6($_[1])?server_parts(["scheme"=>$Kj,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4]]):null);if(is_ipv6($N))return
server_parts(["scheme"=>$Kj,"host"=>$N]);if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$N,$_))return
server_parts(["scheme"=>$Kj,"host"=>$_[1],"port"=>$_[3]]);return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$N,$_)?server_parts(["scheme"=>$Kj,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4]]):null);}function
count_rows($R,array$Z,$qf,array$p){$G=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($qf&&(JUSH=="sql"||count($p)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$p).")$G":"SELECT COUNT(*)".($qf?" FROM (SELECT 1$G GROUP BY ".implode(", ",$p).") x":$G));}function
slow_query($G){$lk=driver()->slowQuery($G,adminer()->queryTimeout());ob_flush();flush();return@get_key_vals(($lk?:$G),null,false);}function
get_token(){$aj=rand(1,1e6);return($aj^$_SESSION["token"]).":$aj";}function
verify_token(){list($xl,$aj)=explode(":",$_POST["token"]);return($aj^$_SESSION["token"])==$xl&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],["","same-origin"]);}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$uc=""){$ua=array_flip(str_split(compress_alphabet()));$w=strlen($Q);$nm=($w?13*($w-1)/2-$ua[$Q[0]]:0);$Ra="";$qj=0;$rj=0;for($q=1;$q<$w;$q+=2){$qj=($qj<<13)+$ua[$Q[$q]]*93+$ua[$Q[$q+1]];$rj+=13;while($rj>=8&&$nm>=8){$rj-=8;$nm-=8;$Ra
.=chr($qj>>$rj);$qj&=(1<<$rj)-1;}}if($Ra=="")return"";if($uc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,['dictionary'=>$uc]),$Ra,ZLIB_FINISH);return($uc==""&&function_exists('gzinflate')?gzinflate($Ra):inflate($Ra,$uc));}function
inflate($Ra,$uc=""){$Qf=[3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258];$Rf=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0];$yc=[1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577];$_c=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13];$I=$uc;$F=0;do{$Ld=inflate_bits($Ra,$F,1);$U=inflate_bits($Ra,$F,2);if(!$U){$F=($F+7)&~7;$w=inflate_bits($Ra,$F,16);$F+=16;$I
.=substr($Ra,$F>>3,$w);$F+=$w<<3;}else{if($U==1){$Zf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Ac=array_fill(0,30,5);}else{$Yf=inflate_bits($Ra,$F,5)+257;$zc=inflate_bits($Ra,$F,5)+1;$C=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15];$Dg=array_fill(0,19,0);$Cg=inflate_bits($Ra,$F,4)+4;for($q=0;$q<$Cg;$q++)$Dg[$C[$q]]=inflate_bits($Ra,$F,3);$Eg=inflate_table($Dg);$Sf=[];while(count($Sf)<$Yf+$zc){$Nk=inflate_symbol($Ra,$F,$Eg);if($Nk==16)$Sf=array_merge($Sf,array_fill(0,inflate_bits($Ra,$F,2)+3,end($Sf)));elseif($Nk==17)$Sf=array_merge($Sf,array_fill(0,inflate_bits($Ra,$F,3)+3,0));elseif($Nk==18)$Sf=array_merge($Sf,array_fill(0,inflate_bits($Ra,$F,7)+11,0));else$Sf[]=$Nk;}$Zf=array_slice($Sf,0,$Yf);$Ac=array_slice($Sf,$Yf);}$ag=inflate_table($Zf);$Cc=inflate_table($Ac);while(($Nk=inflate_symbol($Ra,$F,$ag))!=256){if($Nk<256)$I
.=chr($Nk);else{$w=$Qf[$Nk-257]+inflate_bits($Ra,$F,$Rf[$Nk-257]);$Bc=inflate_symbol($Ra,$F,$Cc);$rh=strlen($I)-$yc[$Bc]-inflate_bits($Ra,$F,$_c[$Bc]);for($q=0;$q<$w;$q++)$I
.=$I[$rh+$q];}}}}while(!$Ld);return($uc==""?$I:substr($I,strlen($uc)));}function
inflate_bits($Ra,&$F,$Nb){$I=0;for($q=0;$q<$Nb;$q++){$I+=((ord($Ra[$F>>3])>>($F&7))&1)<<$q;$F++;}return$I;}function
inflate_table(array$Sf){$R=[];$qb=0;for($Sa=1;$Sa<=max($Sf);$Sa++){foreach($Sf
as$Nk=>$w){if($w==$Sa){$R[$Sa][$qb]=$Nk;$qb++;}}$qb<<=1;}return$R;}function
inflate_symbol($Ra,&$F,array$R){$qb=0;$Sa=0;do{$qb=($qb<<1)+inflate_bits($Ra,$F,1);$Sa++;}while(!isset($R[$Sa][$qb]));return$R[$Sa][$qb];}function
script($qk,$_l="\n"){return"<script".nonce().">$qk</script>$_l";}function
script_src($cm,$kc=false){return"<script src='".h($cm)."'".nonce().($kc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($jd,$qe,$za=null){$_a=[];foreach(array_slice(func_get_args(),2)as$X)$_a[]=json_encode($X,JSON_UNESCAPED_UNICODE);return" data-on$jd='".str_replace(['&','<',"'"],['&amp;','&lt;','&#039;'],"$qe(".implode(", ",$_a).")")."'";}function
input_hidden($A,$Y=""){return"<input type='hidden' name='".h($A)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(['&','<','"',"'","\0"],['&amp;','&lt;','&quot;','&#039;','&#0;'],$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($A,$Y,$jb,$Df="",$b="",$pb="",$Ff=""){$I="<input type='checkbox' name='$A' value='".h($Y)."'".($jb?" checked":"").($Df==""&&$pb?" class='$pb'":"").($Ff?" aria-labelledby='$Ff'":"").$b.">";return($Df!=""?"<label".($pb?" class='$pb'":"").">$I".h($Df)."</label>":$I);}function
optionlist($B,$Tj=null,$gm=false){$I="";foreach($B
as$_f=>$W){$Ih=[$_f=>$W];if(is_array($W)){$I
.='<optgroup label="'.h($_f).'">';$Ih=$W;}foreach($Ih
as$v=>$X)$I
.='<option'.($gm||is_string($v)?' value="'.h($v).'"':'').($Tj!==null&&($gm||is_string($v)?(string)$v:$X)===$Tj?' selected':'').'>'.h($X);if(is_array($W))$I
.='</optgroup>';}return$I;}function
group_system(array$Xg,$Jj=false){$I=[];$Ok=[];foreach($Xg
as$A){if($Jj?driver()->isSystem(DB,$A):driver()->isSystem($A))$Ok[]=$A;else$I[]=$A;}if($Ok)$I[lang(8,'')]=$Ok;return$I;}function
html_select($A,array$B,$Y="",$b="",$Ff=""){static$Df=0;$Ef="";if(!$Ff&&substr($B[""],0,1)=="("){$Df++;$Ff="label-$Df";$Ef="<option value='' id='$Ff'>".h($B[""]);unset($B[""]);}return"<select name='".h($A)."'".($Ff?" aria-labelledby='$Ff'":"")."$b>".$Ef.optionlist($B,$Y)."</select>";}function
html_radios($A,array$B,$Y="",$Wj=""){$I="";foreach($B
as$v=>$X)$I
.="<label><input type='radio' name='".h($A)."' value='".h($v)."'".($v==$Y?" checked":"").">".h($X)."</label>$Wj";return$I;}function
confirm($xg=""){return
on('click','confirmClick',$xg?:lang(9));}function
print_fieldset($r,$Pf,$vm=false){echo"<fieldset><legend>","<a href='#fieldset-$r' class='toggle'>$Pf</a>","</legend>","<div id='fieldset-$r'".($vm?"":" class='hidden'").">\n";}function
bold($Ta,$pb=""){return($Ta?" class='active $pb'":($pb?" class='$pb'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($D){return
remove_from_uri("page|next").($D?"&page=$D".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($D,$Vb){return" ".($D==$Vb?($D?"<b>".($D+1)."</b>":$D+1):'<a href="'.h(pagination_href($D)).'">'.($D+1)."</a>");}function
hidden_fields(array$Qi,array$Pe=[],$Hi=''){$I=false;foreach($Qi
as$v=>$X){if(!in_array($v,$Pe)){if(is_array($X))hidden_fields($X,[],$v);else{$I=true;echo
input_hidden(($Hi?$Hi."[$v]":$v),$X);}}}return$I;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$am){$am=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($am?on('submit','uploadProgress',ME."upload=$am",SESSION_NAME."=$am"):"");}function
file_input($b,$qj=""){$jg="max_file_uploads";$kg=ini_get($jg);$pg="upload_max_filesize";$qg=ini_bytes($pg);$Di=ini_bytes("post_max_size");if($Di&&$Di<$qg){$pg="post_max_size";$qg=$Di;}$rg=ini_get($pg);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$kg,lang(10,"$jg = $kg"),$qg,lang(10,"$pg = $rg")).">$qj":lang(11));}function
enum_input($U,$b,array$j,$Y,$Wc=""){preg_match_all("~".driver()->enumLength."~",$j["length"],$fg);$Hi=($j["type"]=="enum"?"val-":"");$jb=(is_array($Y)?in_array("null",$Y):$Y===null);$I=($j["null"]&&$Hi?"<label><input type='$U'$b value='null'".($jb?" checked":"")."><i>$Wc</i></label>":"");foreach($fg[0]as$X){$X=stripcslashes(idf_unescape($X));$jb=(is_array($Y)?in_array($Hi.$X,$Y):$Y===$X);$I
.=" <label><input type='$U'$b value='".h($Hi.$X)."'".($jb?' checked':'').'>'.h(adminer()->editVal($X,$j)).'</label>';}return$I;}function
input(array$j,$Y,$o,$Ia=false,$Xl=false){$A=h(bracket_escape($j["field"]));echo"<td class='function'>";$dd=driver()->enumLength($j);if($dd){$j["type"]="enum";$j["length"]=$dd;}$B=($j["type"]=="enum"||$j["type"]=="set");if(is_array($Y)&&!$o&&!$B)$o="json";$xf=($o=="json"||preg_match('~^jsonb?$~',$j["full_type"]));if($xf&&$Y!=''&&(JUSH!="pgsql"||$j["type"]!="json")&&(is_array($Y)||!$_POST["save"])){$Nd=JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;$Y=(is_array($Y)?json_encode($Y,$Nd):json_encode_exact(json_decode_exact($Y),$Nd));}$pj=($Xl&&is_identity_always($j));if($pj&&!$_POST["save"])$o=null;$de=(isset($_GET["select"])||$pj?["orig"=>lang(12)]:[])+adminer()->editFunctions($j);$b=" name='fields[$A]".($B?"[]":"")."'".($Ia?" autofocus":"");echo
driver()->unconvertFunction($j)." ";$R=$_GET["edit"]?:$_GET["select"];if($j["type"]=="enum")echo
h($de[""])."<td>".adminer()->editInput($R,$j,$b,$Y);else{$se=(in_array($o,$de)||isset($de[$o]));$Md=0;foreach($de
as$v=>$X){if($v===""||!$X)break;$Md++;}echo(count($de)>1?"<select name='function[$A]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($de,$o===null||$se?$o:"")."</select>":h(reset($de)))."<td".($Md&&count($de)>1?on('input','skipOriginal',$Md):"").">";$ef=adminer()->editInput($R,$j,$b,$Y);if($ef!="")echo$ef;elseif(preg_match('~bool~',$j["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($j["type"]=="set")echo
enum_input("checkbox",$b,$j,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($j)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$A'>";elseif($xf)echo"<textarea$b cols='50' rows='12' class='jush-json'>\n".h($Y).'</textarea>';elseif(($ll=preg_match('~text|lob|memo~i',$j["type"]))||preg_match("~\n~",$Y)){if($ll&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$K=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$K'";}echo"<textarea$b>\n".h($Y).'</textarea>';}else{$Nl=driver()->types();$Ll=$Nl[$j["type"]];$Aa=preg_match('~\[]~',$j["full_type"]);if($Aa)$sg=0;elseif(preg_match('~date|time|year~',$j["type"])){$Gi=($j["length"]==""&&JUSH=="pgsql"?6:$j["length"]);$Xd=(preg_match('~time~',$j["type"])&&preg_match('~^[1-9]\d*$~',$Gi)?$Gi+1:0);$sg=($Ll?$Ll+$Xd:0);}elseif(!preg_match('~int|vector~',$j["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$j["length"],$_))$sg=(preg_match("~binary~",$j["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$j["unsigned"]?1:0);else$sg=($Ll?$Ll+($j["unsigned"]?0:1):0);echo"<input".((!$se||$o==="")&&preg_match('~^'.int_type().'$~',$j["type"])&&!$Aa?" type='number'":"")." value='".h($Y)."'".($sg?" data-maxlength='$sg'":"").(preg_match('~char|binary~',$j["type"])&&$sg>20?" size='".($sg>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$j,$Y),(count($de)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$j){$s=bracket_escape($j["field"]);$o=idx($_POST["function"],$s);if($o=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?idf_escape($j["field"]):false);if($o=="NULL")return"NULL";if(is_blob($j)&&ini_bool("file_uploads")){$Gd=get_file("fields-$s");if(!is_string($Gd))return
false;return(JUSH=="sql"?"_binary".q($Gd):driver()->quoteBinary($Gd));}$Y=idx($_POST["fields"],$s);if($Y===null)return
false;if($j["type"]=="enum"||driver()->enumLength($j)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($j["auto_increment"]&&$Y=="")return
null;if($j["type"]=="set")$Y=implode(",",(array)$Y);if($o=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($j,$Y,$o);}function
jump_input(){$Gf=['select'=>lang(13),'insert'=>lang(14),'search'=>(support("table")?lang(15):''),'schema'=>lang(16),'database'=>lang(17),];$I="<span id='jump'><input type='search' form='jump-form' name='filter' value='".h($_GET["filter"])."'"." placeholder='".lang(18)."' aria-label='".lang(18)."' autocomplete='off' autocapitalize='off'"." role='combobox' aria-expanded='false' aria-autocomplete='list' aria-controls='jump-list'".on('input','jumpInput').on('keydown','jumpKeydown');foreach($Gf
as$v=>$X)$I
.=($X!=""?" data-$v='".h($X)."'":"");return"$I><input type='submit' form='jump-form' value='".lang(18)."' class='hidden'></span> ";}function
search_tables($G){$_GET["where"][0]["val"]=$G;$Vj="<ul>\n";foreach(table_status('',true)as$R=>$S){$A=adminer()->tableName($S);if(isset($S["Engine"])&&$A!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$H=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),[],$S)),1));if(!$H||$H->fetch_row()){$Mi="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$A</a>";echo"$Vj<li>".($H?$Mi:"<p class='error'>$Mi: ".adminer()->error())."\n";$Vj="";}}}echo($Vj?"<p class='message'>".lang(19):"</ul>")."\n";}function
on_help($ll,$jk=0){return
on('mouseover','helpMouseover',$ll,$jk).on('mouseout','helpMouseout');}function
on_help_value($kj="",$oj=""){return
on('mouseover','helpValueMouseover',$kj,$oj).on('mouseout','helpMouseout');}function
edit_form($R,array$k,$J,$Xl,$i='',$G='',$ol=''){$Uk=adminer()->tableName(table_status1($R,true));page_header(($Xl?lang(20):lang(21)),$i,["select"=>[$R,$Uk]],$Uk);adminer()->editRowPrint($R,$k,$J,$Xl,$G,$ol);if($J===false){echo"<p class='error'>".lang(22)."\n";return;}echo"<form method='post' enctype='multipart/form-data' id='form'>\n";$Rc=false;$Am=($Xl&&!isset($_GET["select"])?where_columns($k):[]);$Jb=(count($Am)!=count($k));if(!$Jb)$Am=[];if(!$k)echo"<p class='error'>".lang(23)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ia=!$_POST;foreach($k
as$A=>$j){echo"<tr".($Am[$A]?on('change','whereChange'):"")."><th>".adminer()->fieldName($j);$h=idx($_GET["set"],bracket_escape($A));if($h===null){$h=$j["default"];if($j["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$h,$mj))$h=$mj[1];if(JUSH=="sql"&&preg_match('~binary~',$j["type"]))$h=bin2hex($h);}$Y=($J!==null?($j["type"]=="set"&&is_array($J[$A])?implode(",",$J[$A]):(is_bool($J[$A])?+$J[$A]:$J[$A])):(!$Xl&&$j["auto_increment"]?"":(isset($_GET["select"])?false:$h)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$j);if(($Xl&&!isset($j["privileges"]["update"]))||$j["generated"])echo"<td class='function'><td>".select_value($Y,'',$j,null);else{$Rc=true;$o=($_POST["save"]?idx($_POST["function"],bracket_escape($A),""):($Xl&&preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Xl&&$Y==$j["default"]&&($j["default_sql"]||preg_match('~^[\w.]+\(~',$Y)))$o="SQL";if(preg_match("~time~",$j["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$o=(in_array("now",adminer()->editFunctions($j))?"now":"current_timestamp");}if(preg_match("~date~",$j["type"])&&preg_match('~^CURRENT_DATE~i',$Y)){$Y="";$o=(in_array("now",adminer()->editFunctions($j))?"now":"current_date");}if($j["type"]=="uuid"&&$Y=="uuid()"){$Y="";$o="uuid";}if($Ia!==false)$Ia=($j["auto_increment"]||$o=="now"||$o=="uuid"?null:true);input($j,$Y,$o,$Ia,$Xl);if($Ia)$Ia=false;}}if(driver()->primary!=""&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(["null"=>isset($_GET["select"])]))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Rc){echo"<input type='submit' value='".lang(24)."'>\n";if(!isset($_GET["select"])&&$Jb){$vc=($Am&&($i!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Xl?lang(25):lang(26))."' title='Ctrl+Shift+Enter'$vc".($Xl?on('click','ajaxForm',lang(27)):"").">\n";}}echo($Xl?"<input type='submit' name='delete' value='".lang(28)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(["check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]]);echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($ri,$w){return
str_repeat("$ri{0,65535}",$w/65535)."$ri{0,".($w%65535)."}";}function
shorten_utf8($Q,$w=80,$Ik="",array$si=[]){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$w).")($)?)u",$Q,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$w).")($)?)",$Q,$_);$w=strlen(isset($_[2])?$_[1]:preg_replace('~\n[^\n]*\z~',"\n",$_[1]));return
highlight_matches($Q,$si,$w).$Ik.(isset($_[2])?"":"<i>…</i>");}function
highlight_matches($Q,array$si,$w=null){if($w===null)$w=strlen($Q);$I="";$F=0;if($si&&@preg_match_all("((?|".implode("|",$si)."))su",$Q,$fg,PREG_OFFSET_CAPTURE)){foreach($fg[0]as$_){list($ll,$zk)=$_;if($ll!=""&&$zk<$w){$Yc=min($zk+strlen($ll),$w);$I
.=h(substr($Q,$F,$zk-$F))."<mark>".h(substr($Q,$zk,$Yc-$zk))."</mark>";$F=$Yc;}}}return$I.h(substr($Q,$F,$w-$F));}function
icon($Ke,$A,$Je,$rl,$b=""){return"<button ".($A?"type='submit' name='$A'":"draggable='true' tabindex='-1'")." title='".h($rl)."' class='icon icon-$Ke".($A?"":" jsonly")."'$b><span>$Je</span></button>";}function
copy_icon(){$Mb=lang(29);return"<a href='' class='jsonly icon-copy' title='$Mb'><span>$Mb</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')c(GViDWPFloQuDeeQUO^DgIF+y?4O<cX<jDcJA*[iuQ/YeF96jOuG0x[iu&n>{/Lt(peel""twnUSV4BjfRQHIaBSj#}4}^YKK<$BQMFP!+g0;Oaei]~TuoHWgJWsnlo=Q<zp
uKaG7hs1<!+Oi==O^}->/9Ea]~3synPV<Bm<c_L,Wm<:hI3>D]aslZ$SmG>H4Jg&]_G3uM_JP:Uxi;FAxIn_^J<D92mCE[jOq`wQHqqX[abvndl6
BuYT?K{gCgwCMO.[Bh>G.8O8j4cIh5,JaCXgWkoIQ!Xe%"4xCJ:#SsY.cQ)wF)[oAV@<X0jd2+]KMoD[>B4-v8{,,5LHUM34%n.ryR|:FR{0:aICtMI@=)$E^(E*:+o^~E%/JJVY<6`t<36)7mKyhI2:4Q%)Fo6^*oXn"r:NoSCF7=8WtpI91EqD&q]%mr`%zJG0Us@Lmi0lzD?Jm_.!%96.
05-6$9gJ5ax@d2@W#%4dOJ)dKrIhH<fII!d{G[[I[ajMju&sY5,h-_prPbj1%#EWXF1Gq.K+lD:sy4U_,<r&5^8e0eNI+ZclG?lfYe9UG!7:$;LSenK}0zA}Hr"r6Hk&7{RDK%2"Lqg8d<iC84Z1HzadoTQIv^Nm637v.T[>ekSylqlA2,Sd0UOW[GOnj]?I(rr3D?<fp#f77<J++3-&%V.~ZnmNJH[y]`-^0uG@G4iId}ogQQE
<?w%giV}f@vlXb
MvCbX8f:=d3Ax]qKEp-m^PAQ60knO9)+,(K`(k_*pRZtYK2oEe>T<T19
8/KE0QRqMQ2Zi{y|s_*Z*3nf*SKkX~pV^ZNE&eF+E=*;AA"3>99[b}$CN/W3C*?dXP.!6E:7&
8DN`6p1]rtfE4<_
f(P[Ux2ry0nf<PY]XTU^Ll=8vSj8kmWn;8oo8dfJYdbKvrr6d%L0F6vmh%?=RDtJ>od.Kf_;-K&lem4.[s6-U8?_qZ!30>e
#,<5F60=.DmmVj*!6Y,vLqPc2443S*-I32v==ZWdo4dR<xcq=.Tq,7iqik(&DOhm=Qoqt*5cDK;$y{"Bm
kF?)=ch!$5TIt8QN<9$a#R+(O1$s#aZNShtE-(.7_F``i:"}kxp]Yx6^OM^;e0M+??x79M7Y<Lg41Q@!!`Lo[[?s0%`hIXW7A?*rG&c)dt_!.k<P
!LH.#D0r[5j!nBvJFZ/
zca<%!yWK?IJCS-HJ[HNqf_!.pUz%_:@h&2bU2SnD&W"o>tH
SFubJ3(pod1+"-L.aeP6<C%N^VY:W1T:9YL&[C!X#~VLBaTF!aM@x"Jn8001@ofj_y.fRnEo1ooO_No)K-@D.)JN_QZ_@-sT5;N|.j%#r(hxf|D;JD6gJr<CqQY;E{2U=~eId)KBcs(+yT0AG0eX3;AyoGZwhlhi#X4G&W=<dBZR&)ef4xhwP:[9Fil*UQth<COFaql94jP34`J&C8]sY{C"^rVW_HOH.);{<5fuu&Y}TX?f(q#UcrQ!)3<7mnY=?5U@i!/D$K`n_rXT%vKD<dPBL6rr(n;?JKZH`1wlat.z
Y03*&^jP<h[>f)AYOQRdzD,NHD"(deXs0vAr*f>SWjb?2>=-2oqYE4&Zhc3i&9YW7i2xqjc%VA1,";Capf7<gW?OGIeLR`3<nPzQ(8=JL5w!xe.V/cY`mrrc}A6Fgcr&xo?&xe`6ljlw@SE_5wYVUy.&NfuXTfGCwJTe@Y/WAb8Z8/3Lx-=NNR)Y=o25@MDn8aS&"jKgv#2p_hBN3hAr%<Q`JWRR[$_C5-!!^4U@%kl=[#P9K,FIEY%EOt<5uRdQAZ]t%R<si6<O?wWEAEBIp($eb.HAC_>3a@yshOWmJQqipBLsG8~>`UTgfGN:mhdI<PLDzGUDLGlNV0hA`jKjhEpV1&bq7.!K)Nu?(9y%@Pe[
<(Gh8*RG8+k&wG+veu
^WerIBNNs76r#jb)_E+X*32W:U:C;LH1zcPJ?1YOKvWWz`bQH,/[+eF@mS/dvD}s5b~g<z$9=-2OaA%7
y[sakM[_h"r[F4`P(1cLxT*bQR6=%kJ`*6oM!OKX#!EX]QZI#Kt
s!o}vHn(C(^&ORC_h<IV`obwa|3p=F3(_bm~w4^:5xY!/k1GpX/vn=^W-{%
GaD:^K2-"5_"3b/ySXCP:|s
@FrGD*3U@=$Q=;lsPAH1tBP2oA70;4NeQOC+3@Cv"NM>.[hG"*F]PB8(/J0T81l2>*+XJ#qy,39V:%BQ=
w~7~!`O]bR>:?hLBCh>_5%S!.PS(0Y77oUkv00%mYYWbD-gEukB
RUv&iYZguO9%y@NcD[VnR>bAlY%F(4%K^U)3`-URp3[{no*.3,<S=4#c/`?v5~H,FHL|uAbdq.cwM,_=s[
:ct);`0B"xvqM?V_S+kl!2KQPpL<2Xp`&3pFXaV=RII(Z4GM<mY5-/2:SFUU*oUj%wF4@UK@i3_.OFgmNY0d~0Dv[<]0`q024;@%m=sg!/VP,b<G[o<;+n8@fGew.<gZ<Z#1OPEc{Y5i(I;<BAT9>gT`63MUwH(659gsG.dnuC1TU`/76G0rhbHodRo4,t>kG%,XA*1;neXg1w7))*bsl5*BMEGaaX)`3">,Y"?b><33K7gb-_n`)rn_qITk:GFFhgflkRq9qIr!@,/rOYBQ8i"Pv.b4]ASemkfhi>d:9_ihN:?x})m%9QgIA(BmAu1N8F#3H8R$U!GcERwKc4,v#C#Cg`nY&.ONKBXuolK]c]"l$#m-Et7N}B^@N6JcH1)Eb+3I-A$aP<Mxx1uU`[b#cH[1uxN8H4$mcifjxAXWI)8E[x:,Z+!HJ(K=jIDmZH_k4awgVS;["dZkEvG:G7QwF_egMN@DK1J,@f4dO3`Tpr09uH2M"ve.U8za~1dm]`[>XLzTf)k0KZmx27V
n^;m[an?.biQyvKlUxbAVa0FdXieVZ?9E2H$fuY6ma-s3b[_"326ck{nvuX_hMrf{Lsw
ZI5%v:M^S<rpc0,uI%e]F0clkXr)T>>E2!B$FKc$4`3PuwV5odughhv,7xlv]Z_P2$!6fmyihSd-K4J|u$;8Ejxc%JNF/&q_UUwdv,Y|dI3fbC_8buxQ8s3Vvvxo=AM:cpc<iJxcvdY`cRq$58E)]<jJZBOuO6__dG/&>c)AmSPkJmDf=DwEf_D@hiH~%_l8cPeCK+sRI]i2g*,Yh!B$tX2ia1/(iTek3P3^_8PSoH7q@2*.T0_xu)&}>CD55)EJ=.rXXzx0A}n<i70"Bh$uS(*{$8e5$x/Z1#lDnYR3C%a.lA9>?Y@B?8a,otCSGLjw,SnwbDy+&Auy58#|.fsEMOP-Erox.K_!:8:bU~(MGM;62!]a$j>j1>;^!7*/PY?Pc0<@W5>io>e-^0n{)Uq)h`gL%L=RM)IdGx"l5B*:ZFt]@&3Lo>`:=OiZ<w6}B:R"LFwB0[8jgwK<G}tmu"o&V%0~`[B`08yu8{wz6eR#b>e5f;u.s2KlMX8mgwn@iHocv]l=vygi]>/z8]ATOe>+",*hrjX.u8Q[B(=n]DC|6lEN8c9|n%J,[kJ~b?-sDJ]a#HL[VX%H)?O(FX=h`[Z+@Gqr$NG-*K=iO"p@!:,Ys<fl/&M,7e)9043fy,?*wg-(`<4aok0>nu>wL+=16U-_D?c(y2YX
;AfVIMj18WoK!a0HJR4cvQzYXijD@,oCeg5]9q9$kJi(Hhe-qO|-bvAu=uECp"M^g/jN1E7DvtyvYf@emMaUd$b/DhVnuhxi$xS?LL3-Z=}_|B``Yq+5qcGW;B%!7$S^=T8H3Y>OHC~qi1O+^ou6:EZWR<*HdExTUXCER(OG(l.AuO0p$3;Mv,fKzSL.)aL!d_RFLd+.$qprx#hZKS!^Eo%k_o
M;Cg/~F:
$.hx,o_DVla!~
~L!d|;6QUrukX!3_&*DOX7sc*ImaFKk,_+J$U_
@4vupHxO][ERVMahT6sm,,WN*$3ymI^f.**1Ne:Y5V;2m5ki&Lc1l[K&v_f1HA#:F.H3DJSqeh^=D0G@Ew:m9pW&>XdMwfp.yAm_l,+JT[;FJ[WyVi3u^S_v>s3!qg&eXk?Ka`c%^"*xC(6a*T^P7WTrc=e`uFkiw7A}fZ%=kqHz<+Lgi5stD%N8bphObsozc>y%/vAD1Hh=TlG%:E?rFoLw.[+DX2Jei"NvDAVH/QuzSXmCO)M&>"jztw8.y5hK:-_5]xq{rwnU[<&#aK8-j^P<0*]*7Fl>2!rH_WVG,c0KBxFiY[ha0b/y!b#d9;W3:,mL+d"j8_gdV>0GLHtDZy[-,M^3+Tm(+}g2R_Z(3xlhxA!ir|:U^O`UP?+wkn#z"U=M1"=3-3aQPDC>6k;mN+XC4`h6tK8hCJ+}0-u%`!*}V1RR#076"hR[(0x&;NtCJ^J<vQ+dV[J
W2`YynG1#O==l~)t&w@.*X28+-TV9C(8gh4Ie}kF"Ghs[5V8xg0#.!^Y3zE27qK5H"nO`_k8Y[<krL#]vvkC!$BFXZW0[e"Ha)+5dA:{Xw=J"!bJKWtx;D9|>,eo*u$UDkDEwd--DmJ(`5W<Qo-Q*0C]"P$H9JrsJIWKCO3s1;<aV_x;m6RwsZ=%$Cqub|p609udYkZ5!]DAGFK"_p*&1>X?G%jujw?_w/
g4vbfR*hZL-p1mZS=X~vKJ$=ho/_J
j*"V.eX*/B3Q)s,JU?Snr$r0y.}/=`a).n(;}Q~WY?/v`2AX^mm<QZt*g=.3N1m,.7=GB%DgeSx-QL)fpBX5cH9E;?9Vil^UrPT;qMkMxk:PtGe"<`1g{:LV9cKXLcmxE&=C)QnqgxX^i3Y-42-_/emXUHhkzdOr?!A@]Axrzvy23in;k>b1]3s5NxF=,7}U3xml=)yUu&OM(Bfxd');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('#OsbOb3V?!K0U*,j#-4P2Bz+35Y(O]|GIGFDsyf"RV_CWG&(<hG7poe1|](vZ=6gKb*-e*bfu`XNvNZUFTxP<kqOjr%%43Ge]*BE&fsS#$XGQs=jy?$Jqw)[=$pZs!#`BwhTS`Hx]c/
gWB0SBBXi)^FyF(MQLF,

CntAU.ADCkT^/<4hx@Sg^
Q:{DP1JO0$3OTgwkiNq>)k;xr&Qh*bS(@Bze[?PjZu!*h&UmI7<lxWc9)SaQvxR#;%$<pQAd+6,HWq@]vu3^lY1Y-Ns9y3L5H!=QP`I(rW5Hmx2spr4^XPbOa]@XKH,y^@Lq?=.-z7}qzeIF=B7yM-[7f*y?wDLK<WkFEA"t,#vW6>r<#Kf"]LY,kcXWxcX43
Ass`W9ARbvKD)]-@s[:$!rwYav_$?oR*(xrD_R;EA_gOpnVg]m(S7@|g#&qK8`wv8>Ve+uJAe<BCTZ=x251_N$KrVP!Q@9e&?&z={f4CNVgErN_i?URd=Xht|Qr-%P%uCSWYu<D86`]A3;1;d$x"Gm))J17Q
ZpWmu{G[:S:^$855HJcKZKE;S1-dlyr~:<jt
vN|yIqN`"?)$N>~l|SM3|;g7*iHMqkDXK,u=-Hto:2;IM[pN!44bS5#B},0<$3}je*,c@B7eav8x71@N{#iT(Z;#k)kUdlPj<1JMoyDm5@QZU5N:f5nPG96r~P]]M=:)mv=gMBhrpK&S2geLF@mf&uke2lkXlf8RiG~[JJkKov&yU$W
^-NPh_0Dd/,;3Lh5Xqqi+oH&|*hdQ%wk_U3(QJAG!:|Lxc<M4!tXBPGbzHyeN$$4U![h!BOH|/uZL0S@0?`M=qbV;Bk,}@3+n@Eq"FY;GKX`tx."s9E7uY,[6Rh;.v
(<8kBLH"Ke-/4^wv
dI2k`b#;@i+L}<oMML3kc
T*|N]?c(A]J!zj7Z6v?+[v"xR^qVr&HXOk_>=2=n10?w,o5J{Y;tk.Fnt^[!s$xmrAHN.V+S6?wVou8,Nt:OJ3#T6');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string(')`KbOh%Z51pK,ikTs?)"H;V,IIr:rC[t5,_F@ncm`@yoj3"_:"vA_VBRR$RI?[f_[w-[fNe2X4^yyl=Tq
x8x"wGoLxJJW6("@rvP__?ee$FXD
,"hNFt[01(c0@O1M
7cgR,9];2Vqh$;omxg<Z1+I`g40]
N"NMpph0J>.4woWEa9x,H~KyMz)^$~&=7y&,)3Zi/:#LuN9`cwOcNF<?p+mWOvqk^)lzHt%*ysfUs!_&74]IKI[=7l6yD%Ojl.PWvS,{1$@RW%HFpi8pXIotu
9vqP3l
9@k@b"CyS)^]UvEFq)f"lY^5p7"nPgWQ|pw)Me6V7oIeN2NFbY<NqbStJb7Y/
.1tWaHzr=-GDr&>Go;tQ.BK^T*:x^0PT1y1DsH*Ok=QJ*/~dv*qfQbO(X(XUDZ&"5,P2;yGI)3n2<9R_CsTh0T!%bPMYYTuaTDM0`+(IC/M>Ypv60-@gK7Nie
gFAOe]e__m8_~"l^9hki:Kc(#BF92R-4~W#A5_>QMyFx"hZln3g`T;WJ(ArvB=^V5?XpDYVi6mv_S=LnA8NT2=F=2o&v{X+mYJXnTc`STxNxcIF`+p1uuxaXiV;pE7Z7}5y.LFYknDJs1Vv7h_RQvkg#>M+odggKSiS2@[g/)6IBFW^%bi^PMv/l<MSO+1#T@&^*61^B"
7K2vVu=I.w#DxX|7Q%TUVc,CdL7;*;$(Xi?QbS2vl%+,e+Y,<goAui+:-iO-h/H;?1O/W%!AwZ7*&_y@U9:1otdYJ)5B};Kg&kZpbtxOm*ubdOP/awC!L)Kd<NJ[@d&rgb+(ojc^?S};RQvMZQ8qPeTUnE}3hA4WaX$Fb6[w7#j.|M^<&a8`(p2mPhBA?brn6`gKMc[:v=CJ]3aP`;")-8Xo^yiGyc]PGjFD`N~69p$di
LlR!SStMMcS>5&VM#cXL]FCfd
bNz/a,:Zr6([8BbJu1fjKHrj9Ea(;?qaiOr1^>nWp5)8e,{xGYpdr^%qs)
cLthctKR;6(sk5h$xbP)C]
@.:.J&$k;#GpyjqU?N1s>)=]GMKF-&+TQOhc,NAfX9N:&&2d)+CccK,KG`ptmluSbEVs+AQB#W1-c&*Ut/2m8#YSB]!#39x9TEM6RY
vqDY7Xw0wvfo9Y.nT9GwVR
%+UVSjIw$&PoJ7+5IYF:Qr+m2+:M`c<a6`AB!wpXBU8_5!^$-*6%i8:6$>)>mu4N9!Px
`Ga|m:>qTYNg9(WiXNAFl7lzYn97$]p{K,@*$WyQ,"#G=biZ[_g`wxNI`ACZm#
0!/$.$kG0pg`vU"y>KXxvwLDXT+&bTDeBWg5>j~]G,z@_AcBsWM"HW,=7rnR0TrYs4_HJCaye0FVbbAZS$
h6U"E-MAI
_GR1Olah,&WSed$fUtSWa%Osh@0)C8
iScU(]c?C<mdfbb9>+{8m:|Tz7ydRYzhX%5Q]XaMm(ypvytUeJ-n1K}yd#n(qJ]y^K{sd6km2=7eBLmPGeiK<al;LuBn}^X#Ip+IIlrV-Fn=jYG*sd$RvO]:[qQLeX~"i2E,,_mlwgPXkK*0EKkccF,P!bsOE"`XDEKGgATQ?9B1g9bJc
KyME)+LwPWk]SED>4oTJ6<%WlaYdOu2;I#aX#XStlD)=@>>USXpe7oCF/Tk@?6Oel0guR/k>]Q70<Y#45&5LE<>3f]#&U0?f!`/Xw%4
jDUJpWS>28V5T=M*~C9B?,Z(_&98ini6R$;@cSF"a)}sN2&c/Dfjb9GwNih<6!lhl-:[rF.cPo4&nP29{AI$m+r4J_B3HS#cQS{J=uehbK=b|.e;f3@6_IAGrGy+G:_pXFo`^_=WoPY>AOEW~/h8F/y]bQ&dc0HND-|UVbKjWeKq?dh+Z+(hh6Liiivq4H*i~Oob;sp"&`Reqy6%(eZIDv1ymiLP
vTh@ortB=K&}0ithe#BmfgXq(WjkX
G)*fd2f_fh@Pr=3PDhsda1iLIXM&ww16Q"G6gZ;YLjs>j?,.c~J8vma;6P)koE-)y]5rmv_+qsYk[Inzada/2JtMyc>fKIc2Kqy<Jl2VS?JoG&
!cpVIMP[~c^Gm1Fe>j6DN_Wdg$KGR*>5Z+_O?UXc_e)lmnL0t6}S~(A]qDR"t%eAXlM3>,zfJ0vZpp$_dZ,,zt..wTFU~!r"s8522AgB+>YDDI4T]lnolcFU;Lo]^;Sgp4Z(c$0oW=ZOP1PNvY1^?4q;l-ovUs<,1Y^8D%Rc)5dB!&9[.EeM^1mT1)Bg1/_jv<RllYu^]hurh)VlF>>ttiu!_&B2R-#(h%D#]Sw@g28+^i|"YB!_cBEa,fz)Ckf@&`ukf+#^x1MBv31,4d7D#yCOdVLZeq{<]2w*.+$m0r#3)wd)#<KJqOvs_73
<,?Pf]r2S]OK5;/-DX<u#
Uvc,ZX&DnNq(t2,F>#0H2ak.^"Rr=:&r~(4yuU_OIHq#YaS*=?c[dBn+
vpX3=v1I!Lg%k)tyCMRvJI;dxvmcC-M0/tJQkmlu!S>joCKgbh9v/iY@S
rB
R9p".6dPEe/KZsh8dC/C?S]5d2aR#d+`mrV1JYyx]6<vtS>lkU]j#t
j(vHx:=@HVX
/Fo@E#qrOe.ZD^"+$]DC#_&FHjMqN]yr$Tl"49j5e;A5A.V0P|ZiNbS>gW-`&PPr4H=Q8I&//7z"6sxM%_NYct,ofp*P#Lmi#j;__-wIqO=na1rrw~8q!5=m,0q$#M0,,X[W%/_HR#k6JTao6`wf@(
)z(UsmzkY_@LwUW$c:OQm9BorP1]/_y1,%yj>@RU*F}bN>e==
T)}S%^43Ilb73?E`1hGL`cWhPqH6I.GZ*CvI`/N0/i`)H9A#]05.F]A9Zxy[W"zTj#U8fI5P5UGUzSGNIN?.V
Rt537;Q-b=Lv>/f*$HQw;"DtT"pl7>.FpT}j%JVv1Cz-4<<hL#1o/)Mj3i;,qogAC=qUD
qn;Hnx(Pn^.nDht&*M&k^QM.rT
]aGhkf"`3)A8Yu2L*VF[R(nhV>=A.&giZ1rC<;l8v*Q=fn,IRr83MGO,-Rl<ThcvR;QSk(J--DWLFoyg-M#n?Z+kpoG<MsC^.s,(,Y=Zhew%(Aep^L4*39Cg3q<:2(>(k7O#
l%5*swkC<83^I6JVG
~wWJK5&x|d>3HnC"A^:wKRXbH;kWg/O6t6G4>sW:
vLL!W#D6_#CyQe+;$$JaCk<"0#RWu?mJ2elz9{U%lCxXpoM[q_lSsOwr<(yJsXlDN`nZ1%lIFW,9g%<"lv_if&xG`qdAcd.df<iI)H#~04XHr>NZ/Hdx!U1c"%y!RPXl1[3,btUAxpn3jyxiM3W,wJW7lE72G}8ZuZs7K)w)4k(%hXvN"RN9/Ts%hQ<~1t#O<>SpcG%Zd-ZRj"gFN:T:U=)W8i8omD0riCX7)>BJSsRV13%NT~,YYI"p$Vm@:3.>HiPv?C(]6#HCAky71lRN34kcb1ts3eCzEDY1=Ij*kDEU()rU/:WVj1WW821:g+h<G5cui=tO_~[XQ#nzRttfvJ55=wIflTW"6#&z)zCj(VCqR"VpNnR!b@Y_-<.Rr<M#NzSCx)"K8IA#+*Y6l},YeY_WB[[5Q5riJ4Ib*+Vhj(TI0,G$.xx*:t)*fbqsL0xyG`,]UlBPhm8;nZ;qpFz)t_tWw$M~H0feaG%961uRdKm.gPrPLT-
BIqbnzD<SPvMy{M,UJy~+|d!m"y<Yb
H.p%T^I==y
=@cSDmTo`NYgVi4FC[_j]v[C9kmgi`46.IiP8hQ^DH-mh?!]h=X"yUW=Mm`@5v*;IR(Lw3cse&l%Go=$tn0
n5Rxr>Sm#:[|;Sx>F$W
P4r9&RjKP2>6P~
7&Ni7^%aWmuamqU>]y7t[NBI*xZg,i#_v.J<$#gj3;"4/A0RAq5[#7+f%&H:(ww0;!
u(BS`7jmqF0ft99g=`&*?t)BZR`e+ru-f@"<c.o,QH<UDe0xbptroB92=sl";.yMrNJ(Y<WYf?Ns*FL|x6%?j`p~x~q*%"K{;"uTA",b,8>ah-`UiT1,E$iRiuDGQeqL)#;WVe%wlh!8fIH&Dz&Yh/$zG}_($Dv)nAJ@v/cu$"_;u/i~gKNhEf.x?:x3)q7)tgnP=2)xcBs"Dd)W2ycImsMuJ->$AUd6Sr)Z]MkL>>F*G[HK*=]"i{$tV0N->a`N9`jBMbVmhDhYNZ(?b^G9#7:~1
n#T~BMH^lJN^9rmJbMJaa!G}W~K$1zP<]cyHiUC22K[_I.G&X
K"v5Ha``!gqR_d=^s6Cup6rE?Fc~#">Boj^zcoh7,Tx%wq-B/m@(I=bu&Ia=O>e.Kye:CyoTwuv25$CE(wB;]]pemQnJ:rv{u9XVlsDmqxG_Uz]912RZn060Z3gEDyo
/oFfHjt-!vr8KwQ.2H
EIcG~i[y,)d:2R
XyWo<]2
W8tShKa"Y&<)c>yx1hu7C~@UQ#WC)u;dDl:/o*kGv|^A+_ecc|iK*<KTF,2$1bQ.D~b63bdRkE2Vh?I)>_-[Qo:fEcu$W=ugI9ZL"`C7H[uK!"pQ>3D5A/T}]te0F#f:_ry7M{.$`2:2b,lYEV+Tjk[[UXlY/8<rlPC{3+9&:t[mDiY=@H#A(AR>`_,([{xF8{=]R"IaP]!$AaX_*=I;3fL4Gp!=>.h_^y#*f!/"h9BlmE_)rXPf9NN3PL:L+mc/YH
M.8Z[d3;I@daqQRjzlQC3%TFVDe_IS|E"91nmaM"p8vsovQfyiQyB:G]_E:t[=p.>LR6T,
$6TPZS_h&>%Ar-J;C}[1&R)VsK-NbSHbcIA`QKBV1V<xR,c$bM-))wY[mEUc1:]tYW_~Lc.{9_f-;5A_l]fI-/WBy}4jnRa&Q-O_WnSQFD7,4!C*VNIu/d+W7%d/?l.]$3Xe!;W_7[E:-P";TG^%>ynRbUT5MIjsij@N$hv:
mGLo_.z
d$Wu;lZ2`P<7VgZ.HO-jF]()YTW9_N<*sPStyD{j#ffW-leZvZixA0a#4`If878O6]@&YSDK}x*pX1iO>g4ITAJ6$Oh<%XvmuSUdR=705F@T++eDa@ADKe$*93g.zkH`!a(GTQ`qD*6q~TO#8N!F|b=hhj/56CCObQG1H%Ew^v]>1D~#OE/7:UWX0Me2
_(jpZFk)V)Ge%ic7%
!)U0O/1"HS(_WWwzyCyE@shKq?!#M6`;
V2UajI-"rthK%/C-)OPo59BL3N#a}8ACJQT`V%f%C7@7|-%O[n+"ol7FZQKX3UE$)(O5B4eLy`{[+M+*82Q6?WBQxQ&epu(a{L7QYijjppkv1!FyUZ1.,A
/P+t7..)glu>R>[QQF3*`k
G>7Y5c%$?gvFedsFKrvE(O)OIXz_cdMfO(W=wjyCi
y[J^@&Pk.89C.^K[XORHia!0m.+o]XyRvfH%|$M/*srk/1lwyi|AaM$oe3#efkb2fcx9;q#n;])1nrm4?Kw@qY
n5(tnE=MUK%,B6)Wd}<XnDmT,6-GEX,TdqR%ON%4bov2jOz)mh81Vz4,s/)jG4^/Up
kc$UNEf]gqH6Qu~`s6!%VLsotnndz`-sbR?X^$,9S6[FDWIvelBQOVPsVaDEC&Phmoec4cJ5hkjqV.wI3-9/=sdE,;<YmW|#/<n*]g8PT+l$Wys[ryp97<{9kPG=m8V#&.NSOy4Aia17pXwa88bNcZ.G3[Np$,N;0w:/:+]*vUxW8al^jOr9+"
C}kZ6[kTa5s,I.2*&;hZG?p)p@*3z#dNDKUdSL%sb#:vS+aK/0mbv"]7.cn~`>lU+P/J93kn^&+@*5U(uJ@E#/<B/:g]G[YG*r_a7vwXB_gC5JrBasu44c/fJ+k1jC1BMH6K@=J2>)ERXd1Cf2nb;Y&-N;_KuQ?kRlmfFgfp`0RI<u(pW3eC-Qo>p5:fCo?Za~.U7[!4jg:f#XO`cY/:PGr/&_2eQ@xl)Cy5t*J&OP<2Vz
LIPD4$u@umM#X%msUlze32_+4mJ<{JN2,%-F)s%/
.{M]H-+~IZ2Yx;1"OJvOG:rVVMc
lz2+]vVAR*M}7eKJCi.lDn-#taG:oA[n;|fR101Yt-OYn8v=KuU5Uc]tj(J~9]l-V(&S1;D~=)XEcZQdkN>Q.gTe2GQ.U%)G=qW<G6!E`KIiU{;b["FF
b83)(qqju<`+vvaW/n;fb7p$w/MedWb?:6MY?20QefZXW*U?t5*(lr+wo45DPaayGX/[RiN6@w$=WoV7$HU)L-)g(n8I5*<N@o`eN*FjPGco*o<dvMfEKiJkrxU!J5VBR1hni,fjikn9}1^uhN^-N$mrwLO_-)LDJ-^Y._Y2BmurMGhPVh3]^ALQr(I6ym(`^Rjn$AOq4U|3`
X5G[1=~!cy"bMM$K@gD69jYZ89X3XbRRn4eSh,vxtw6*jR.o7<`@K7P=<iOWdK*In=9ZT.uI(5}M-$;km.*A(RbjRCc
7TM
N!b(A.uY2Bqip?;w8]ue/8
J!`kF
SYsUbIG^cu35F]%J!0S]!gtXv@vMGj
_=e"hQj:59QZ{xVWQ!~8Mn0/)N5/z8JqqnYqKUfA-p9
xUdX~W80=kE:Gh{=SQX_L(UOU/*Ko@*TOo0_!/!4pHzFqKuM*729fIk/B:7*TVsDTFOoBt7Fm^&Ox9ib4K.[U)0$|+gb-[M9Phgk,;eHZ4(^Htr@%m4r!nlWGw%A/5,6y29W*Qm)1dU)iF<_xTOZhI~3Y`2W{,^Y1$JZ"`nU0Q!!9?Nf*
f(jte7z`B%v71&Cx$0au@jPk5`%Lh*HFG,5]H(4.#@DVmmYO8RPd8-GS7fV)g39y3<(bc
`9))[DMEs_j&;=x@pyy7qnBDwSWBtJh<yoFVP,^m>ooq4,2JG3un9/SUS%5&12,pughmTDL.9YS(G`?nW&gO?t!lG(G<Z"pC;,:XW)biV"9!T7fFzU+OFYPVJ&/r5+*1D&2"U6d<d:s]aAv9oDCMJ2n>1TPWkw,^:^ldq!6UcOc?M!oR`.u%c]F$"u^s5NqEsKHV{p+9iX7_bR`*0"slbB.i>P
]INGG#qr6BK_FvCR+,w7;Q3~qQHP@3&wu!mmk2KE-q<##
xfJN^N_[`5m.RX&&,aQS)"D)L

mE9(T>.!~N:8>jo;r!N$h@%JveqX<r;;Hs<.m:)Ei<B<0aUfa
H-tmKRB)00VYp$y]zTHLW3(72O3;htH^4L^s/0z?fo@cwfd0;%_NyfbGCaPqbiVt269fPTWJbb)>HfxmWQ5F%KOYUY~JgEmMClo%U45kDpT#$BjFMT1ya1?H,;6NVTo)e0l4-8UJ+M5MQ(]dnU5cqKzu`OAU=nr2DK]0Wb7wkJN$:Y+vJ8,h&y:hyuQ8!T6M&9}1P5F+4_$LabuFd`+3@Gd$f]O#]O>_QDl[LVq4V-B^sEOs6;3n/8_R-;c#:waY;fw_NIx3{gy8wC!%Hm61Lj01k"RdK%w`?Jgm[RqYQm%49N%%
CA5#E<C]&`WbsNdH(dc]FmL[pK=^tq0AgvR?g*wSmD)$A3`P64JQ`kWrlr]N(cX~V>g#e#8g_DyZ4-ZV>f({3N)OjX!9jb!1whhj]F)7TT:4PVE^=BO&,<k^tIY2DFWjZjW;aP7k`n8W[9OTELeFI-@Sk$u:csqW?(vAQdvlLex=)`o;YssPN~@id/m|S?F0WvaZ/6@.0T&J?>X[^6Z9&!f?)LGb">.!=dqX5-)$T2Gm%~mffvdAyw/)NgQ]f/[w(L8V3-oF$J>;[YZn2(FD9oM$i[gQ-HS|p0ldH0`]vRVgUGt,)[Q0YpF<Y-c>(Q]]?gKgy01qxyj-aC*V;0fQ5~N))3poqN1
&@Tp?]Q>wBz#9~4_f%=>Lb.n8j:WO.j4:j2tJpN5mTUWS;:yk`/M(&.B:!U.nHA?-?Px8T2VVSGOTU4WPPi{p)AB/irBXn,Pu*"=U:t]T2M4(S+pobo%+l:]Sm
TZ5fCg-p/`!ftgHcA^MePfw)p_:i>?lU|?z0*>U=A[f9B[Rg`gYpVu{4%M&)Jt-j&Lue|W^%-@btFM6%(^VM)*3ayO[!{y5-#]|PMW-.8,/qDB`K~h*ek&hK;%?&KEn
5cVonyoiZaSMl6.=D0[B4cFXZ)jw0[Py@nOZ%.QYw
c=`K!Y&g}M`^JxP54!oon`>tyHaHGDS3@8Rx0q=%)EVR^w?@dt#2r=NA0r"p9Z<np8K;eZ.A)8X4~(M9.0Q^MH#Fuo->Hck#n;g+Kco:zUy)8Y(R!Al_%-CjK&Sn9VYv+?=!U`iuoH<gX=/C_!r2Vtv:_5^dS@BX"v)`5QJ/Z_39T>_A8+L`6+sDuv$;s$J%goJ>p)$"Ay@XHf0yvYXhlhb::i[-jqv9@M;Me:-2<;r9g#/YAr_%!h!dS
J
UdvCom3#Yn.i,vC/v<9p5G^lviKFcD`:m;ir8t/8ghk6KkClcA
3~Ef2=LCa9VqE@hRk*ejM(SoVTgT8VLLV1QQteGkDDXVE1<IB8dOhwU[JonSjl=e`#`MUcHt
CNa,331q84/`fG(S0&94VF-YVpspfQ46@cNmO^JR9I5abJM5YLMYzDf^Ixt[Jtm>e<A4_@F;$CHRC!l[mM!&~fW5.a
;yf>l%+sZND*M*?@d!/HVy8|FaRk#MT$e1u|w;?]D^$$t1MUjhUcMoaS=>s=[lUgl{RHlHkP!+v[N%M9]*UgE1mkB"H:=ZE94^f0&IR&?6Z`[xR)a39,MnA,2X4_G2kst:K7xnIb)alL&d6iQzJ{n:5ht;gl%lxM*l!dB:oXm`hCESUBb7_x`_42:]5!*dRx_y7|47n%XhFe:[88<H.!UjX<Dx>1E|GNYoj/(rA`FLK0GG=2aB_fU`EB&7_5t744t`mn6[fn`DVHPW:9m4u?X5k[5{t}qCIR1=Heah
`GN[oTM)UmnJdUuL49`F:dC*CF-B1b4Ta@00cT4DP!.>Yf(rELUw:w&x"lwiVyzu>s*
GKNZ3kQ,E@m>Yp<$0waa%yv?%J"k>Nu%y`f"epVvv#dbC*x5iTYP4$LFAC)qdtOX!W@(Mb?y%o*-#VK;sp^gpabmU]mBjt)G"+8G;g+ua,R],!HPnm<=%Jj.8Hy1p@xV2in")@&VUUvN-aX&N/KRN*m+PnQY{".Q3ub5[I$k<n8Z3`5a6clQNSK::+.l=yP1$ZrpD..]4$4#:cBDclJ
*=sEwX@Z~EU(*uNGhWGE*QT`USb.:d@3$jh[x-nrm#6]rg1n&X.XFc=9I*!g?Twcib/`4!57R2X[U3c*M!%=zvI6($hba@SrVQ;_vxA]O3G2*aXm:h3@I?,oN#CW7VGrlBzY&=Zs)TlJ}%0ws/8v}>bE*X!.LOli-FV2m4`${erjZ6lTXuh=<6j.Zy1baDXr8[N)E$
]iV;WM,<;9!EfnSi(di&:V$HIW_YZ`/6;hgM?S;bc#S;S[xWG}+-_*C]%t<~++k#t83urZ)m.Gmc-yw<1O^>#_*f)]aWX&<~8Gaz8V.AN+uSTis:sD(C7cC<On>:=}-tc2W$-;R$UBqHi$BSUV;D%B1#E3;;FDrEBf$.LVLA*QO`GBpp:gVk!}iZA:"j7BU1g@W=94c_&<r|uFalNWtrwe@+1-NiZ9$[+>&Wd!O[J&GSB@d(#rIc1+nu(@fZA9H^Arb6F|D/MuOx(o4<uT+@S#JMQV2RcQ.?Jc$Y1{@@_hLy%;]((zLu^vU}
i/X.prqYan{P5H&/8PV8%Y^[y#PS!#`Pvhy#(33T@,dVs[o(z]w/<JR4R2nY*EQcZ"Tm
UTZ
!X3%KMcpYJdydNJ*=j9hp&eH`m_?`t*4u+(>Hx":3yAby/??[s"=1k1,0)srNCkuGpO(b)e9Ew*k`3(`y*]R1ZhL[b
&R$pK$uP*z!z"W^P.Y4[.h.ehdhDppcf6wMEGgOnv3G/)s+&^HMI>:DucS++5Tk-5?7@Q?8uXWmwd=iL]@6P1UvbW_0!JhI(],ekPl-Zj;+)#B=CJ
C:{<RCU4:.ZLo1O3+d{#e`CQ[=jG{SM(oUu<3NCgb]<PMRi+:S?r.qe6+J#*Z>sPz62S]!T@_k"1`T;##@_3BMlQ+K#`Fy1MbQlR~ar``R*<Uy1nDAt0-sZI6H~qRDtF=dpQ_O;4Prcd0iU^"fU&JiR5<5$x}<a%)atw^pnw[0h:s<zH_d/Z]^"<Ak9>LI(BzCp8*!dF$Z6pR@G_eW3H2rr1
4(+KykywSK,aCd<x[$Ng/o8`gzn3,Gf;1P&%i@"=PF@NTDd37=vl=?QPq-)aNx!ni,S9Lc
z.}:j.o)X_[ox5DU5RzNt-!arq1?zXy?PPpW6?/W*,J!TYnf;dX]sXHsktonxC]inw,%4hB%jB@!}+A_m>I-UkJlw/~[E2#;h28h6"[3jo]8F*<(1B1vzbSfLq(tTEOOvL"qP.oJ>m(-JTidL]P(#TxC;%F=vbB1Fq}b<Z"l?_3Ehi<Q|y`EYKM&JdwS6fsjQBM0K2*P=A7bs=SsqPDkDiaT`#L&s/V]-7,a|2MHK
6VMs~$73!t3>x`UIfQcR,*m8M7~67+pN]fI&d_(eNx*O$!2[v]e0k6k*KDw+kj)P4#XsLGZ(NbeZc"TABFYAAWttjNSY;Z%Z/SVAd-0W`szQyk,ErgR!{>#^
UJ_JU~1G`p@h,[+02.+wLba/YXXStkVLf/KT76;q%UVX^J:^JX?m-wPylRU+QMTZmYA/$0uj&(S[U84_X=G(Lt=YY{ionXikhgW]D]fP6S!9^:#F=to&)0T`P$c;INZ0O3)@O?5dkOQ&jGc>2wfElU6RuIqoUVno5A+x84vNi2onF5x75{EPVtCtNWfBU@/NCgKju^0@<n((uQUB`0RQ0jV;V?S&Zr;J<&yx:bwP9Or_&E;Q@`u<sV`s7a:B&l-J-KXaUUv:*<mP5%1(7;6Q[Eo?,;c%!mw/a)*7D~QtaWXY4mg"h*_]fCk)L*BvLE]&57nO&`cJ"LTMul9Sk@5>VmQ
<-7zWRY~/$5q`%x=mD95ab;@0O;IY+y_]s)kf:v!%[a!vZ
0gurMH1Y_3}@YB"y.GJm@C4Cv(+0!E5*OL"+U$9+ke
BPUxP*E@<bUwaw2%ML$}Wzqxsl)JxS!7
7F-MyI#__cL*.ZYf_a%aQ)RYzY0YUyK8f?Nc=Hu/+E}KOE;#tR`k__AN]ci/+(4Q3d:TQop
pZvtyePMR#$khpr6kyzb[00mP27i?ENyZ4TO%l5m];Z^qdj6`,nZKNOY}*<ET!KxkNmv|`*mVwIL{`H3;;R2^Vav.e121=t(}yn86l8K
2|%H6-h~#T53,(DvFG_v!oUzimFC:d8Fy4d8GW$`].!Y?U-/%fe5)N:J?bS~Y)--[[qBZ^_4=sA^#Tsuh$LoP}P_k^IV
qll/`7<#SU"?c&p2).v]I&OmX9kY3Yl:|fWg"db5UEwR0@-WxqQ+Vq/NE1]Qqs,*duSD$OrZYJlwk=oZ#b#vbOn-OW+Fr+766(^CSXqAJ`q2Ub1_*aS?+Dzc.b}^<@mkrd!t[am2lE!QX@c`TC=X/3v.c^9l?JL"rKSWXEcADwKNgG,8uFY=8xjb9w!bbW$T#wPx1l;w8qkxPBq^AiRHVnDm-9+(z`_&Hd>yuwo!(DY(_:`jtDl^A"?E4IW7l!LKy5ck1@so_tEbtv4V7%x-GM|TWGf["S]Y{f"5w?V-:Qp
a;ZFPi1_8;H6d#}R+ue@hBeD"j#,um$ihp%]zx])+gpuG:|?[W^`ZPVB3ecqu;JKx^svjMHF;ix/@T(rJXJZUmTvxaJR1+Sf`s%N%0-H9mn5=ejYyRfw1l-Jai/m&fg[x_ghl8-()eY2bAE`r"<fa8dS+$lL2dpH&oy&)$<Bd24=O;j1`oRs!s9^p`c%(L~E[+0e9M}tW0nna<_&jEA[I!}`^@4,c&]P$2{FhX$46loh
^S=0^s?Oip2UMGSC`iKZ*cMLJ{>m@3P}U=AKEbF-y/ONGMJ?)Wg]/-#%t*)X0HHi:X&i,gyXPu/g:v-NI^#YFRFr$sTxPqMsNPo0e;
3Ppe_`g7A[^YqrH)jD$7?,ZNhph7U,(8?^,84XppH"v4KX0mh>o+bn)6}w4;Se3L<>zB{w6v
>tvB/|%L)XOuA+ZyJXP5>3OGQ^Cp#y#{aAUrS(!"v4CScC<-oZjK`rZMv~nAGVOO$ql-j8d(gQ
h9F)Ks<_ri21EVyrcaBWqmV*GklZaVfN;?YiVE@Unc,Ae)KA^`iuWLtKE1_<@oK;YTM1?YP3G
S$UqmdfFc&$5
W7nuX[d%$`[py<aF7
4^r)p7Ux=*SY)>-(EN+|D0LgV=u>[aLZifXf!OG-K?G,,a"Fy;q|RG!L]42HbK[;4R2:T87beViZa<T4r+"W!dsPmI-#MQasmLM)>8AzBN`18Fcpiwp@6[J,b
!s%wF"B&]U"U>NyWILqaFG1N"|g>)04Gu^>"n4QL;]C%_3KJ<bFzUJ*8fg=.]_4-15s;=,d-aPN`rU5VSs[YT{j;?~*prp+cE>;;a}]uP"]/1%KJ(N`[^lEDQ%Hm5FWan;0,tp[R`QLwA,]*o1B!!0LZ2de-Q`5!Z./Qebp-mPA(t)D$lmxkxVAA[>B},!y7+^&Yz%#Y"11p=i+MU?vj)q>v
jwrh2.&XAk%jBHam"1RRoQ!HTfXQn.HjEGW+Pk;m^"Pm4nun}QR4?Aey-<BqQD`F`k|h&Z,"Hyw&D9[`njW/aNby&*V<!I9;5qDaw_*$D2EI(XeH|1Zu5+n^/cLD.y|g(H-pXc|EOg;d-EvWS5&(GOh+=$p_qN7^f-2>hue]%JNZ}Pp&wA%JNGn
hcGFxervl%8OK@Gf=u`:#a3J+W/i|ksRWk-(}_&;Csrn{F+Wu=p;/qL>h4@>}:XQZ7mVp0sxw0Jg*O)NuR
Tx+H"bdSR<s{CTtz_i
:K
Jxc!+5jm*]+%2ExN`&GnI(fZ;5"A;-Et6;gQL.dEZ{rF57(k9!b^<MDp,hn}L3BP^RHAK
9xH3aixs<CtqLB7wt[&Kiab"u|IsY`*23"sJO&XhbwnVv?3l-]mzLuUJ5vbbOB_CHJnI9W+CahsMtc#*e7P4D>OEj23YJg!M)UyEfOAi.2"6
ECf48D(W"(eM4KMXk7&&6#"b|O[1N2t3/5OI6Gt4t>Y<:c2L16FUHsgZr/t1NB"y&-zIn
d"&f}p7VGVIJeG|rn@|/=
it[F#AGF#`aueAYkce@7Jt#:&voMLs
QB/Ekc]4A)*2MXvO,YK6UrG4J9A9M_.2TS"3?g8f`i2)U/_m"S`eMu+R
Q`x`W/)iA,|2p4dBiHOd1a;yvAEByiK5ZiJl=o0,z]AG?:~F!50V?_Qe_+N0z&P-PZlX{=<+)a:UU8FSv(unsc4M]k(,Ja]%3P0+[2
/oQOVC%WNzK^e|BmL/Y6Bq]iI_rhqlQS@6tAlzXEIxIC]19fN*llbkM0kVGr[?2=v5
aSj^$Qf]1f$vO4MJt]<de=ZEiNS#0$>%uSD[lO*9MQAN)-G>33_VZ?M;A`pp)gz04$})!9rg;##dwImTD
Q--k[bb*UKwD88iUFFe)wFAPg:=lf]6uyN)N)j@31QPiub2*uY:nQ+@S8M5x)2I>Cw!R6G=ELBXE:=IWv1J*9m7__]"BCcAK;C"cxnrhc+qf![l/@5I*z"a?I/zSwbR">$q4K0gtj3v.nL>,}Cl1mSj"Qr8C0a"=pnx)F(}4xT9(7+x?.#:);EE1:W5PUH!=l,NNcd8@4F7.|p~&^FI!^bwmb;.&hPH]"R;1c-qOj=hdHcFRXGj4aIixw6UTl`cez,5Y&n]`zgIc!wB%gBh>V">muR}je$KLpI$Rgc;%!b`o8CrwH-k3hEBy&ZDtL+?OvR
(>D<R5.pP.8=Ec3<.*33yiw;qq6ZKnHbWBnpoq&g(:f5yU
/c-wY4*/GM#aQf+6M/JfY7Mpo-(%<4@!9X5+K>*sbRZM(_Ca@fb[=vz6L^:Hbmlk5eo>y;3reY`Wr?;HqPBE9[Jwkdnnm<hq3NP6
U^"&=cyG2)>oelr9(rh713i%E7Wr`JuO+U7~ikZR4dengyZyH?5yu|%
.EJ#g.^a//8c/Ixmk]!i;Ca&wI4t6Rt$_^Fh6%22dOfo7%m{sU%+nm+"C5Q`24+Va@XDA;/A/7hg[Q7qPYAMD(eDGbvq5wy@lckgc1nG1sHggCqhYJ1eT*YIx@.cIr5=sB%tq[,*fahvcF"#4
_D.7`n_!jZ)WPp(?&4_]W1s%9Xs[Vw0lj}QI,T1CHDdp/ZJ;q3g.<@+Ic+kvuDD)KV`^)mNm&2b?S9+FP0p0VhdfyXc7-SFo[uQ]<xA,,E(Y/QP%+
#<$]%i,s%dNK*yqdFo=0ukj]J?iy1`d%M%dExHYjTrg!(F&m"A)n*jcr]Uwz!mCZ18-~lK?I#Ihd!ee.,W[@.gC8o=d,c!HL*+2J#DMj*OD_QZlFjE,4=kitY@+iTp2BnD/L#]+db,snFc+32H81xb
DSc1/L!-Y3L;;q+ueSsT9"{eFwjc_H0&XiW<`.|@XCJ/bnZm61:FS3u_wDsI5,}B^<~g=$
55#kt|k&dcy7-]p=rbt;T(*w0#=ToclAROiu=02ur#Cs>,9G*)<3X@ne6a:B.:dPvG"U%5j!!`>zTm.4(
@NT7HYB)a^bc,EyELTO7ht62.TfY%VXMTGt2i]N><A(dt`e~:``CvJEykg_V@<Tpq]*x*]kv@J9`XAxPa<"[IV#yqvPSC?]*Uk3"!-gg:s3`^7q~n(?8cIhV7Vm^&m^5$4RY_!q.Zc0wh|2&n5,hJUH2VMZx.k&qw#X
i6pH)y^^74$@H2GaoGx7$qN
vCs@_w
3mSNOHt6Dqs[Q$LLu=gT5G)v$WfW%;tx<p3UNJoZ[qn^xEh88:|b4L;RN1KD[b$S3:?:mtBz)u4l;pl
0pD
eX+yiW"Amid!sflH$rE)[8bm^)5bq#zbzs/HhV6)9sZJ}X#MPH}D6)fhX[WGRnb/Mp6anh6;!aL!an0w)QCrxK+:gT$89Y,#$K$N!N#k*/G3uL%a;o}T>!{OrV2SwpIf0HP4I8(v+S]nFFVx1s
E*Ky5rDls)BmJehN_V4v]3/*^u&vNu4}J$rmlEJDY+PC%v<==Ui<EqX0XHFvSNXe*G@Ui^Ue*+r/]DfWn|?)
aTQ)ZwRLUl*.2,bv@C!$Lq#5F4*=u1$9`-t=>r6bKDndzb
>
f-3X$p:x-nlvF<Dpck
6Sp&`c>82?e0joy`s[R[ERRA
xI]*OvdvWw(<!9mr0ANY"4R75M^Sk=bPHz9-4,6Q1PM_b"BYkjSY,@Vr%Fe,:Km/RdIU+~%eP2D]CWEU4},>?bIX#cBbqpo(N^ls0nG3fo=~"=@UXBS-sU/WQT>%-VwV=8R6O^l)1(HG<_,M=OGUN&T8<5?!/9s&/j"a*IfH$uND87p].uU2^uaD2?8_)upKVkK1p/#iJZn&XM>0--/oD>Rsa]N";AXd6|0~%x5l0jSYy!k$R$MZx"k<;/K5v_V[+fsrBj"SD-)w?vMULnwp>39i>[`B:>vTY-O/;|5gmS#;J%*y!&2[2IA+0G
yVB@@fiRxd{NhJp$rY#/Pwsq!Ey2mJx%8K}k8h{(ceI#.#:/)tqm"tmMc:X][ZkJC3}(RvE)M0g)Ffu0x,@=&*I=d6Zi#tb
E7gpe:*F@ImcPxR<W7:hAiCJB!4j2.K2XIS/J42"K(Q^X(;A59_+VsBO[U%iJQj8%!>#jOfWwe%I(F62G[;XI<z1vNMjRtbR#?Uj~%Nm,oy?P-$pG_C!1GwY@di:G`J*]BB3DG[#:4GH8mP4NT6pv
*?O)_3MSj<fpT`-@=*/rAjTk]KGYQ@8dSb#*tgf@o->^hJnWdXd;WINr/=%AXQ2iQ!G80g!#dW/PB&eFYhz3<w?"X")SlpLFmM|PXXEdTJSBV5.RS3@(F13qC[{Y-TwNCCJ*ve8#y7^d(`w<T-Gqw+j$H$/N59"4hQO.A(8v-wF=)@X%1o0qNK@CEK%#GuDYTkMd#%dG
P:J?"Hv!F;+_OQBKna2Dt:!j0(H"agR2@J?b6b@6H?<fXsDvxUk>jdHh&QW4W!PzXz9}d|_:eOds(7-$LrkNmBP0Sw6`g#c
`WBS5<8e]LpsH|&ov/SON,uCD*?yu+F}B.2uX^
a8d8p
2PyVu3njBmWe3/AnS#b#Wq
AQ]}D[788``SgAZ1CCR*Vvw+v{#(m>Dz,1pK@,dmO_u6`:*D-Im9E?nHCJNL8W#J<PB,y1D
ACOB0KRC-#j4uag)f7dB5cMO)cv)E<Y#=]p5P6OU<`ijQ=-$3kAHfw15<VL_At`dA;YEWh^_)47OV3<Dcy)[DVQ(ts
mm!c:`ljQF&SOd^J7f|:h/A0R=vfi?<!FWKZAWe9z([.Uc!<R#"jS@3rcmO-0F{(G6P"HYx+.I[`wcra{F,9u^=Pr*]p"N)py&bLaBMKUfLIu`{Y[/2^;v~O4$@I}&lm|GEjqEVAdCdoaHXhBL#FD1:syoe&_ol]k=q#A:tkU#N5u`|WyUT."ZNG_+cD)7/q}Kd*WQYf1
}gALpGE$gX0v*76JNf&"d&(sZz"<F56jI&giLyBrrZQ3{jTPkPMyrtkBpSaerUBR#htG][B..H$^CYnLpt8GATP0P;@Y{$-h**js.*):1"AC!Pdj"=:kFpgcO1N!2vw)F_TX;3Nl~(z^LRxtx6UeX25`VoesFXO!96~T9Qu5vhm=rizA(DYwe`=&Y&B!iZ^v|07y?8Y;;joq{@CXCyO3
e)#lJ9]0AT]*I~4Y:FOKc>(p9b]!18daq33e7-rk(f5JD~WL<xgR]6b=d$&.');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
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
Ah@62ejtRf)GfxCebyiTt$!s#eHtX');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Si=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Si=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Si["bytes_processed"])?[$Si["bytes_processed"],$Si["content_length"]]:[]);exit;}if(session_status()==PHP_SESSION_NONE){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(['lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax']);else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($s,$kh=null){$_a=func_get_args();$_a[0]=Lang::$translations[$s]?:$s;return
call_user_func_array('Adminer\lang_format',$_a);}function
lang_format($Bl,$kh=null){if(is_array($Bl)){$F=($kh==1?0:(LANG=='cs'||LANG=='sk'?($kh&&$kh<5?1:2):(LANG=='fr'?(!$kh?0:1):(LANG=='pl'?($kh%10>1&&$kh%10<5&&$kh/10%10!=1?1:2):(LANG=='sl'?($kh%100==1?0:($kh%100==2?1:($kh%100==3||$kh%100==4?2:3))):(LANG=='lt'?($kh%10==1&&$kh%100!=11?0:($kh%10>1&&$kh/10%10!=1?1:2)):(LANG=='lv'?($kh%10==1&&$kh%100!=11?0:($kh?1:2)):(LANG=='ro'?(!$kh||($kh%100>0&&$kh%100<20)?1:2):(in_array(LANG,['bs','hr','ru','sr','uk'])?($kh%10==1&&$kh%100!=11?0:($kh%10>1&&$kh%10<5&&$kh/10%10!=1?1:2)):1)))))))));$Bl=$Bl[$F];}$Bl=str_replace("'",'’',$Bl);$_a=func_get_args();array_shift($_a);$Ud=str_replace("%d","%s",$Bl);if($Ud!=$Bl)$_a[0]=format_number($kh);return
vsprintf($Ud,$_a);}function
langs(){return['en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',];}function
switch_lang(){echo"<form method='post'>\n<div id='lang'>","<label>".lang(30).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(31)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=[];preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$fg,PREG_SET_ORDER);foreach($fg
as$_)$ha[$_[1]]=(isset($_[3])?$_[3]:1);arsort($ha);foreach($ha
as$v=>$Ti){if(idx(langs(),$v)){$ba=$v;break;}$v=preg_replace('~-.*~','',$v);if(!isset($ha[$v])&&idx(langs(),$v)){$ba=$v;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($Hf){switch($Hf){case"en":return'!X/,-aPmTC"L?"s!
"Gc/6GjcM,^_Wdq82.AA5Mw;-tv>f@U5
<wa/MlZiPkQy-!K.eawO|W*.p:t11eVa7H#w(D?SnRds|FZ!lvLB_Jo`ov{Yq<gTM<WIZ4#W5WIoBYVZp6IYHH7J&%Sm=m]yM-Q)c%`x<fr.@_&+AJ(<ns(QZ;}bp2fZI(i8Z$!?7-!fu#`8oW/30(XS+9b
j*fefIEn=XuK|z%c?z&oZtWs.h+t{qJCj45#s7;5)B!q|c+s316
5e{[[SO<4Lw8^TC*2Bgr@]p@Ld.U53FAN#G0h
+rk;"U)%7*g2.9@lc0f.kq1rTJue`amt=$x?p>4q<3;,7Iz;DwG]XBS`L95l9x!+C`MQ^^^HD<p_fDj!oGbT7jE1d1B_/l6fA8Dauy2Hy01?Nq3W)WLz)6<yi(>f~RI1zH~HF]&n;4t8j
/GSMYGFPCL}2F+ghi4:j=fd@?qP]Cck8E4@k1kS<xwDOyR<8U
hO_i?MB8,le>$1
3!5-UcD;^cV9w?yzmqCfN}n*n.Ap%el3K3I@x+v1h-)bXiSf?7[?To0dN
i$GxR:!H)uKxPsf{SNt-G^_G@l7)LZ9_?%]W%9P^no8[
DJ/s+.hsKA@%"UV&co;3rNSZ{a`2in~#81fp=dtDV&KX%6+Jxx]*60>@h2cZMh-5RnJbp93E<K9aOVCKwG5phxM_r/eo/p&hhN,Xs
[K^.!#+ADvy:Hpco-f#y4F"%uOvx?uRfQbjXpqi6u[A^@WF"i_h37mkrmeV;t=yB0k8`P=Y#+hh]48rL@c_#_AB4$bW3cT2CTTD`df)S![TWRf{3_TNP6ACf-^nHF`D_kj2=.(8Y1QL*@=Os:-eb*!v+6P0H-U3ydS)eKJ",|>&1I,oG=9x5Z=k$HyMNxbzW52Qp
02*6o+s0v@>*4NL.>dePVdmj0!vCEgDibj"tD#ye]0a,=M
b**%
G>?p$j@p#i)"C-=A4i*;W$j^o1m-<50!cOKu5d-I-$l*kU/lk"o/uXQX8E.fjK;i3Iq>oo8(<^e5Z*3D0g%m:?(``0okj@q73.0XPrZsWp,^Lv+Y&B96"/iO^5+7p6-rmUGupuo+Wu1)s$=t%>k#/i;!n3!j!d7$LM=Io8)
do)>d~vdn[JI?Tv/$n(e.k;cQleMmwD
3ij,XAwPrQ"~-;&.deW<Vp:>1!34;.v#gE[rn.Z9-,LQ&~ccgSw)p%jVZ=A|tjc5QG<c,ev%#BiaQ?VQB3_EqlT`<<D{HfgQE(OQkpfV5qL>u|CdamxG
;({%Xh!443Og8N:,mDai]Dz%|5+68p.n^d;?]K2U1<}%2e>t;[%D:s$:eO,F"_F<($MLm:@a^Im;WW[b`xy_!eP_sL/4K0FPeDDx>WD=KoYxbP9L!"(dY@JI(f_gkah],YQQI,0m$Y
Nyu#&Xa_>RX/.nK*QU"Q-*:V-Z+M9HL^]=Nc#GRNZ)$C#<>3(b`Iev?ZP8;K7V>($:U?UR/)OK?&H/NuTO_g,.aRb4i,<mB/!4t)6X8U0;J$Uv4?;%G$Q3=
muXYo-GgvuG6Ge
:SGC)B{OGbRlRJ`)eL
a?Glg&VE^ubBel
5RMWT``qUU8HmKk6sHquWP~A%P%Vvr,6
<aw/yT*--x8JWn_L+*7D+SB&VSpW`?BcNm`US8/oO>[]eR3n2c.TT8J<_BVb3w`:)~A870)Ut{ipt}#ZD4+tE8Y6JEq+a=@a_:U0hY;@nAaj,}q.6i1.=(q)aw?X>SuMCZ6}gRAWK?D3Iedsu+gPAYP9X]Q<x{2!UBWjZ&3;UM;:Gr5mBoB<rVg81RujN].t5,c3uM.f<Q&8SNL:CZ-ZCd>JOV[NUa@VPQB9vYm
P<`Eyx4M8f#B^hQ$a3.Q&GRkDzC/&`PqS3#1?rsZeOb$(|iiS!3QK}5;Xh0-n)=uY$/g@=ki7U]k1sY,&4Xj#}F[n]KcS3v5P<oj2^FvH:^Dj~:ugcV}Ou)-T@17*iBlGj17
yQr+`=LLa;28XkgvEX3)%6*_+k[Q6K8%<Di)DQRP2-Z#,TH:}dL@T**9(:pFd]>blK4.kF>*W)
%%]B]|^?F44*;gcI5@4Xj-p}I0UYPjK@58-b2YXsW|_N5gXxeNp2b:y*X>BO]xmsco&t("QZn8tul,<|fRy0whnd(8B:%pVrt%7fn&X+l1t_Q{wM+Vif9?S`({gB@`]X]x4s<^OmyS3SGMar.rsrhQibYDVwUY;o:3kxGK:P.Ak-Sd<GEs3nMnK};/7^Q$/bgAKJm6rO63XWinJ;7$PO7Y,5
#4*X7$F49Q!cxE!odp3Sa,X"_R%XrdBaVsTjdN9Fvfk04Hm"nq:5f,:)
M-FH8V]+;3BVy>ryR;R
_f"89[n,mu#pkZED0#-cB^R=E;?WhesOyawpKeH<JHR]nfv8JA_{YyfUum(:F(M!_*y:Ij@l=7,v1@Z??&B%]!EO/G%5ki"2vKOD(oxfCk7I(j6}<ahU5k*="FgUkmSg>klO9]2#36Vl!zbo=!=5pK3",#C%U[<@$Ru4Y625R[hhZn3^DDf)7hcnYfc(;L7Vj7^uhEx,7t.9U-vnrj,X[-..IUst#rgJpqe~#NLb%9#&lt2*I.B.*gtJk6JIO=z!]b<Xpy=<t_t6QPL[&_ppfZ6nm~68Cl]zI^M5MRz#"fnYq]*[#7bX2`U|f~6tQ+)uO4TTwWKg9dxNLn*7
J7jtB`=",lgLkH+Ufkt7QGz<)u~0.38iM
d`WPwg~!:G{gq!*8Y5&yjDjrjS4CI^,;]:5!:I%N%D^-[v1=QZ
;RR5vTncHdkYrn4MgEwdjhw<IA/6&1!ESbILhS<5NW/e)7=A+lh~#1e#<KE;KnpNwsV7+PXy(?=I$l^YgY$H
!geaOs]yW0T$%.i3[S|b*qdl%ZcrEBF9xrHBY?QxAlMs|<:c!s$QL3&]u/5=G#gh]Y[8xlv7*z&N*c3757,)R:d^/w&YvYrDZE:vL?GHJ/g<NX9(:Id)q<_gaj2vi[8T$iM+Q="w-
_G4JVgW_q2]-":b22';case"id":return'*Zu;Bcs+>&)krdk9C0F8;F?V2@T$uH7S7EHb{2|pnr@`2?]>&mnxcn990/U]~U2[=@@@POe^LySm>qKI-dj4r[&o{G[mxsgH9K)_m5Q^>o<X$8US_j9S]u?jf8"+Ei&Yo4},mtH?iw262vNMAJZ6}Y5c)aOXz"0UEq2#7xA5%.=`Rq=P>kNe71mc_k[*BO1X/oyx8q*vq5*bW9<TztGd5^}TsYfHEo,FgLJU]KhT}fFxXMaa.Ql)&bzOSeEu|9Sw&8@spF?%GQ_b0nk3Sg*y9VZ<go4Y9q
Ug@wBS)YNyQ25P6YZ2!,$Qoe/}#+S{i<lR(F@nPZC.]{
]Skdk7E>;3EyS(vJ7,VrPQBh3+;pHV6>Ma+5oI|u6(^9~h7YUWjsOZK^@=Z<$3QoZL[N,XSNZi1izIqm^J"gLnP1
9UgnE]
{.R)w/1+:;jq&gHKJX`F>o#4$n{Gzrz&+.Y
z%
sXIP?v%Z3Y@;,>^{j1_tyeRJ!`WQTKifr1wr_Drx+KS)0Pb)E-w(/L6OA|htF2]_jTy33zwq`(#7r1G=2vN:M@<*Q}Q3XAJ<8hd-[0),$%W;9Y^EwJz"1n
XJPT5x!+o7=5{F<0nOh#,HT^F_@_?SD
R$C`*@NAEy9)yi0&j6r`NKjm,NKpj/!a9PpN|>ip:AvXteb!t)jpNCfi8n5U+3@h"C[S0&iu+(#eVP)rjtg0@!O"i-GpPh
.6;GAUMS,
"z-$+diwjX35Jg0SJ{KdXB"_4giG_N%LkaYV*#Ks*bb
Po2|.eg%:}Y&VX+X/)22Qzi`.AN2p#3DJ|98,pRK0SMoii_&oY#=i*cU0:4<jhwsP-qgC0Jxq^9`g.5ppm0xk%50*d9]:Y@uU|r30E%x,:O*^yCyS5MS3
aG.M`Zo{n/>AW:=O+j$[R"4fLbKE^|,acR"e>{RRI(I.i"Q4;n93"|J4%gi:*[I@:Zo3Jxqtq}(q[E=Hfcoul%.pvm%2%9:5QXI}.mqZ+FjDhX4+p)4)6&L-3-_I!JDL=TGUn>0c_#e`xQZA6W%p#~.9`okh!%V)PNk+_SY{D%yC5w4~cD7b6tL-&LU
=q9B1Shb+Ph"bKiBy>2oAOM$mJg1oEh+T?>
nRc^X=>#%yPWeLi"Y7CP5}yF=C!3,ZD32@J=;0mq1=Jyb_yf0(Gp^-(Udwd*&]T$i@]Uat)mD6v$RLCgU"gwmUly!5QvT79tf^q["{v|;zh3pe-HuEfjQ)bm*P`C_#uf$Mdr&7<l/z_6bNH!yLSntb<?7F<NhF6"$d(8Vy)K";TF.u%dR?)PjN6?jo)I:H
UUiv1nZRk"I&YZ2s7/W^W3>u"*W5-E.9Oq#T[E{-rXf^^c!JM;Mv%CV=t8*7cXZaYg^p[^YpLY"_dSi,D:_B{L,pQEKu2.eGf(p)"f&w!2DCRV0CZ7We?79U:3b@U-&$fSl<M%Urkv^)-:Hp/;U7+NQ926|>q/nEfTBC%YJvfZu_T1!e
BZTA2!.=&jF`P16{E~?Vkt;vxk&h,S("4v:x.^t4XH!TCP0FHpwTAsl@&IwA0d;Z2L1m15MeEc?ag]Isqgf6;1bm%f.UCY8i-U=>#zm"?3:P]1J#SO?j]}"Q_R1q&Lo<(!K6
{3lK>E1RHI@?JMGxIf-&xCWN]<XiRo3`m0`oLmOX&rktYyJv2r|0_d8T)t^@Q5*d(x0k88m]Uw*BdE!r"P@O%)K47#K=E0*>(&XlA!pTLqY:d/3Ns:m6ekYT#sw9]2W;c2g#@%q`kC5ylHPP`R;ccfn/H]0/+cr3Y=2tsQH$^ViSFv]M?+n2T1TNJ?:kF`sT~/PAf->88@h;cWa@DFb
w!7+eT3ESr)"ou^HjNzD+qQ+sO-6]NIgAISY/bIcE<T:qMgyXGh(%!V0*)uwaL2J
$-[?G~2`[&tnEP?Dmd=S22^
Tu.bhOxN;x8KLwRP%U*rD9Wv+D+{t%=;rM0%Ku-Xz#u&]tvCi+&hZ[D&LP;Uw@"E*9PbW$q-9y]tP[800nP*%@2}e9d5X,
w(k4oG#R%SvtD>h*f&8su5EucjTRUiWui`-TU8q&=#58)`z,~,fC_x
?mAvgsGTRir(=,t@G9`=a-Q$q%JQRX#V8$D
%I!P7&q(2c71DdpCoz@hMWTaH#rL7:Cv.n0-`SdxOb<(s4iK1@/!Is;bX?p1>?s_
%T+r1xhWhAAI>>|_16
25IKZp+14o?~.yA`(H?
b/L%$B`-^W>8Hn[d=!`8E[C2HVT_+;B@%41{r*mB=078Dq)sB%_e6QNd
TAo0~@o?$UOl-*-uMx8YfsHLBT?(63]QHAz7tF^uXG_f3`_B3V}5KZOTGk`(r"Bi@TjEF84Yg3H.xOCKwl1q+afrZ*#)mw$G=8uy"7c
Cu8UJj6F?f;qHAk7K
GaZT>K!4ssX`X0k(JeG:6C/Uy8:lxjSL$xXCQaT>-6E?,t_%lY!!s<`/tKIo$j.Z&<"oS6G<In[Q*C[3ClT!~
Oa/fxu
Yc-F?WC"E8v?cv_2KdD=N(e5mJF6MgA,n),e*!]A@-?iHBl4&"pdKyMwL
XYJ._uu]wbP3%47J2Pk]([m?<uwr(mcdo*D)43a+<S6):N;Jd:WzFM4UIUBkR"T2BzG+_-9shx<3!CqY4p8D5B)ZHvFfpt+o9Jgf^-8y/_=yL(6<n5)w=4H~Pk9*M<Eh.QiYVN!<ld7q$h';case"ms":return'"Zu;C7nWB&)krx(P)GRUn+l1]gUQsl+Dx[}M
2ueon-ZP
$9LxaMrLF/sE3=&J4,<4a-Y,ax{S<0-sl5X8T]j
>b:v+@3g!y)=/1~(Nilxh;hymJ}Y0^M%)B*oCkS9Gq*.MQYiQPBs=N8?rwf5/9"s]7uC3^1v("?g*coyAN,ko0JXU&bJkbq*_2Y7Li]f-?p`%wvBnvYr4y&c*waH`pt`V47hQp60j?^O>u6xT6SBPeMXD#jJx%t!%.KMabFeMV)-4TbRb,JgNT%JZCOmI@O,cJwRm?e/WmC*tNf55W&lWs]=LhCIGO3)|bi?rd&R=P~v?,h(cc>5Ru8G1v`S[U"uObvW26DdOlet#>)XmSYQ2.Z^x^zZSkg$xx4h.J1"Z)Gb0wa$?M,k:o*e<b|TIYfa36>TnMsf8Ik%^2sbESeQ:b)D%@,mr>]pl;FRDdtbNFuj2`6-6fa5R4K;!j1c*MTZf<!&m<`dWhAfh:{xelkWGUjk4-$waT6PL
K5tMSAJCb>27f]0FBa/9FfZe:)x>0a^n)
H%ve}Ih-Ol;@B9]Xl%?&[a/&ts2mH]rMG&}XXeDTI7.
@5Px[:Hp!wQ;*wvqgj|(|mr/pDR3q8%,w=Vx}4YL+,c?4^*gMoAjx.HUGSwt[`E>=Uo2ce]DRa^!p>qr/@/
01&j+LWs!N|,.!=7lmn=P>kQd4TN+b_x*4%ZJ=Fs{c;2hc2O?g2L3s<xlba#*.dAU"}`pXV[seqo>4;&^-|lI;p=sB
L0l5uF#jjRxot`Fl)E0slHsJ$WrAqZmX(<e=PkT:*wH8LVnFU>t</j.g$>YB#PU|%U,vE/=/S*Xi*{VoN[d+[
$x=#hNg4b-9Iu7lGU"5gNr$.-Sp52Ht6H+)0e$=*NGVzy4!-<4cs*C`]k,y]&C9MlFYiX
qd.2Phn&kQY7+5xZ5
/MS>aO%S"P-xPU7WM!L
e4[b]~yRYpUm3JYdpfT>oE8GEA&uNVtdY81?Z)Sf4
kmps(fgV/cn@u>U`)=*AA,b|W,pEl`<H#t0,DCp?MeiCs{vf!>s?w$NzjCqXP[a);xd0E6[!807bRjMu+E!i)Q05*}OyN&n@X.d@Vyxe&J)uwqV.x6Zy2>yaMa/qoNlVQrH^xdn175EXbM/h<ad$RxDmi>XYAmQyqqL+J=qHY*#1^o.hh}3pIdA3n.?x"-Ro(2wk)1O?.LhNlWB?8W[yreF!h|YL"b=-?D09]%#7OkKzSBq,Kp?|@ONjK/mU#o8vV[^=Z$QP0_hh#v5vSe@wfJ!~fAgN6tS5""7(<dyZu<R#WG$JC.]a?fNxXL`mAEXN"mJ2o,[YNHX{U;^`98y9Oca!GTn/+M_I"Tx<N3fM@]Q+g8JXos)srz2ncF;bTIrN(R!z#d;796M"`n4i0+RlTYnJOl+*0(bRBlYc3#]+0lx~uZh&egIed.q/h5"XB`0-f4U4RXt%S!4AA;[9,nD1
aRgH>fluCx&P,I?N+f1d,[493drTSF1])u
Q:Vm:,%o_W<<&ERups?uMFiO
%"+5@%;(-oDJGywv~>H$r6z416($z9y0%FyI^RFb]g4%_ypLGL9D~+sMn2@(:)<u*HVt&xnkb2#>#`&5~Ji
psu]aOb<#Y
XcrP<./+HIsGxLT_MXDzycY$7l]QEzkt"!Ey6X8S$fC_.C2W1mbeZ.GSjso,4:+9*mjiT1%c/4!DXtmkU80xr`o|ok7IWNB93"r?6b$*=4R,yD]oYWdQfO5A
RY^&UYgA!VoRth^u,=zjFBS9KB`i(+))"S$0$k>REXW5zx>CZA"0x<7&3NuKa[YFspcHYk"&lB:+<x#^WL>GwE)n=IYEg<cO3!wT"`rM,"WGyMX1qUAV6a4"j="<&J"Z|v1XG0B)6g^^H]DW{re`)190_pqZH`N.i<-!0&ZF51kn6i=*NYbA&Erjq2@Unt!"7dcIWMuhC[
_u:O7DRB1KqPM)4s*FO4Y0L?l~"yH0ByEc:AOw=I_m38s{9e5Zqm9`Ko]&Q(bsCL+ND&"/1E+(u,bsT8"1)paM!5sz$_[,-HZh$]VzJYr2OTVrZ5%A12NYLCe=#@W+#UnK?fCt^r0nd1D;
7wxpQsc2B-/q+$v]0IH3dWf;4Y+W<5K?iTZX`^&
_h&V=)C`[50/VJf[}<:a
vMq+
}SZWq-U/7,XR)6r=
eVQy*K
}gM;"xaD.hH92uy"R$n85"G-g,9KPX;3MuA[OmC((2o.{13xjU5uu7I<#VuD3ItH3``e~X9ecl+0tO0BT<{St6{<M*A?qwn$XMl8kK-&k,ODm_zqne&x8V#,tHC6AEVc$Qa,bm+VDkArn(
Uovo*DBlx:*?J4pZEGG#5aE:2.[[>ZrgMv!{%vo
6p1eV4eOnYK"Xy[FK,_Lp7m/Pj_V3Gq-=Sr4x}i)2;A622+@@aT%I7Kl"hVPAW7oOKX(v$9uXZ6#A"`npwfug9X<-MQ1v=`D[~2N>L/n)0A1h&T|AJk45_#`Ip`ZO_A5sg+btX1-e_.pk,Na3jkZ?Q><TT>T[y;"_p
Wi"O|OG.YdArW#!L|d,6Cg>+p]6)U?e>*&HkipUqUa9pvPa0AJ6F`S~_S)fjF]-kcA$xyI@^SCMby3aHHb|HFcu=JE%@S[]GT-0">"&v(@Y^e,H!.7Aq;"u
n!uO`%w7T>BKT=q@$3d9Y$=t$>FA<fEwB';case"bs":return'"Zu@qbP+^.B`ofy#+N1ZM=3K$)t/G72Kx^x5WxZ:@koK"4=!q4Fb"2D;Y6WOGT#UMnsK7cJ%UV[E]$hbY5$0S*7L~
g9^g0A,c4cxCRMDsmlpF=B%Kl@>%N,
V+DYv=]imtvVZ_m_gml:a4u*$WP2tQ1,M5fhK|l1O60FH3L4klv/Qv3C4AA"KX>>U"b0>w:=ftGV79AJ[s@iRq<I[Gg,m5PMRZ.$&G8qD6wVncwuFzgtn)@!,s(gOvuW<l2TrHu}65(*CPXV
3$GyiPcRwucm
<Zgsm(sM6_0v)YI4F+)iE~qhCidU=vZVN5!6:4EbvefIF<h@=&A9(7fOO%2>.]p/aj9ogmFkmU!,+bWGB^(0#m`qvAp"#BLTZ=aT0$WxHxyDl^7@+In{;z$j!$gJUs!R-"pyfPBhV1hc#c6$*+@e$"8/3<_(]Euya|V]soC2V3&nq&wkV9D#Q&jj`~&[n-[F9B*6v8(#!jou.dtC;2=AedeSauB#td!;F/<~3f%U^uHn9&+bG;R"6/RP,Iw`WY_)CZ*}Y4u"J{T-Wl>&LSF?yYg|S`K.$_h&LjorQNvwGq-`3QmK"61EEu]JpX1S_?sB=Fq2-Gv<:y)0Ze/d@B-rK-<0lolGkR^T){RIbY_w[UnTkV_!u95_1o4%UBI@8aRPFX.d_2g3nh(pTKaQ,a[(.Wm10Sldss:+7`)~oPd
@J:mWG"~;1J
$
K~f>ep]c3WJk_?x@q6-ELsBb)lf#CU/>*j7)?>Bk:!IztuaeVO`aMlRFD8>!AU#3-@fZZ`ydAM@pBD:_R$-*PMA]aMyXInHT?rY3?r3`mR%TI4SEs%:@QEZmm{o}in3EpQ[7PK9!
y2NI7o_9nI+e,H}Zb;Ijb"~yW[&F1S}yD).ry7EbEpp=$s!0%N`>aSDs=APvX9;2YdQ%@bBa^Xs+ED&n[xP6|dO@7Oub-v@I_5BL!Z+a]C+iX0Qb-kjY#J!-
ZHC}xX2>H0d48;r^PE="Gl&q77`aSD8c.}E&[w+`;ta>#F3@,U530!VACv&:fenr0_1.#pfaF2R-6gTjUGV!#q(/cu8}(Rfnk|I,[@M8`wtFA|a:=4Br/OLf%X^gW
#-&|F0M{6HoR,fFv@7l0`s)?T=8@.-OJ^`Va%qY!bF_*]z!]`Wiw-9KLNc+P_}m~0kncf$q`K|ss6_]s2>eQm"nnv[qU.[G-V;t_lm%ZcBNm3J.3GVGpv;j|4V_?es?~2bE}1axF*1b&d@lev0mc[}#LNB35R_nf1xtBQ6np$dv6us5"NrG/h
M+U{3Xd[f{V=i=7xg%bBJG.`P$?wN8@TTI5y"vf(6^nVR>ZGA=mZ3~&!-r,Ojn#e()FTijcQZ|&0&uZLi*Uv>/Wlc$+Q>|nH^Uck<xrAA^rHA_&.YgqF.sCg>>Or,WCi(nD>sz)sx6!/`D`O?YKEL4_i+ub7GZSWQ8"ZU9P/20@^j<6.8clrb0up5@V}10]OnCt(D|sEWf9SWrA}6XcJ#^0N91=B3Ca`dII7]PMk3nCe-%_TY@X<oDHBGeT%RKF9v?5+ShQWp*N;xp^1]sD$O_N`B]j/5ggEm&T:N/i#i()xM]eRET!/>+7$n~*0P7rdP(d<J.G6Q[gJWhCFwI%?*5#wmd=c^w^9kt,.[QOx+LV$&I/,Co??Z[=X!Go4nQ6@h,7[BCoJ>%KXB>md"41$ig5sHFTsW
4X-"mZM!I~4&4QOd=_CrC3[4I>DmmO6Jxf3VAM!QjzOQBY<VYFNt_@WY0yIQG%2Gd5d"I.$s-MdNpjJX"UI`1AQfBrG+6L)O[<Nw+>&>:l.hJI=X7z%Pc2Q[AxK(@-VFmzaohnWxX4Sll-KArVRBi<x)_}o5lCg,
^6v=fUfs<uuxPEn>Icj/av<NbN$5aN@pA%Q>-#jQzRdF<+rr/;?[dg
n?R0NkU)Oa#@(82~35B:xFe>Yx.n/Da?X!o.^j@%"yw$@CsFeMi`R9P3nmT&1T?F5!PO0pE?8oaZQYvwZ3IK[&<!&{=o_I[YeW!0+k^0%Owdo(l311VzjjaT4|V:%X8@Dj]ij=!4/vT{]G+W%?(9Y{aSl5=WH@#2<
?>7>9+S`3LWJ%1`Vgz6w${bu;=<jh_%A=:4ztP8]6AS^d(,-F[Sgl$b{y?a4J>.DV|%/RH+NO$es5$O..;m8<x6n_n0"j4lA@RwKb]b[!cq-B:b@u+WGIacb9+@A3mYh<:t%_#4+u=Z5kN-"gh>#sQ*$3nDx@]A}n~:0!M%bi5N/oTbJkaM(mP:f,^0@k8^V`oc*,}6,c;TG11!xlpA:1lNxx?"rt}[fPK]em*v>8?P!UEoi.n;Wa=q#TX:X*@0.dC$<["sFvQ&v;6OA3*>Z:J_^cP#{9z;F6splGbOj":U>tZ$&UBlC4QXcW%
x/-dVpSwZ>B;6=~MCQ#K?_xAka~]))"81o}0e8BO)#(ov_)q0L&Y
=SbE+82haz3wpM!i1*l^K71[ufo_4yPB0KC)[T[_G;LLW67LFruKHsmt+)RWXs5ENe5Pjgw
8"iP<J&dgIyn$7<xc+MOP6M?>e^u.|bbw
n@[9DlF6c!XR$
qid[OVUd37ISEAUr;/4>[/VH<HV6X~By<`gR;wM+T8m1Urt.A1e1UEyoxRTs%n+Fae"GHh4|Jin/trkOVW@Foo/dJv!-j0hlx@`eKB@lc)wBjjs](wNSC{b.YQI}g:*p`h-)%a9=#"00cua_y"!&n@LcgC)f5bm|GB[hKy;C1F=0^pL--b,:7ps:m?$Y$A9TvpGDQA^W7=o6Gs5+F5MowD7V,)5DI+(h=(Qvl=m_f<qAvpEGM0HS"6Q>HVjJJW,LTi$@^am2-l4ETni<hpjVm8]ViGx?*08#b"/;wkj9-Zn{9QmZ]h;T.u,KPO9>=gF=,eJbfzKcS6_Yj;/:T_J|O-xhCSxxE]O5xJOga48+`C*J7JrXGab3er(VB=vi$ib}Cw2"]g)$hPd9+JrwTW^W1?kxiT>4hl[]u,jIj;Ib$zZ+Gz@"d&vUwRjJHS8qm*XIVcU(G;++AYl)^dqvuCp$J5wto]b#tshI=_ic@#VnLTXE/>Uq?cU%c.60?_"U))b_6QKgyMytI/
~U/:l@GJaa5-S_gM{Pz=P"b^fsi@ynBuK6L>!ZC+K%mp=uC5i%f3^5W(Nwg.Vkua>G(';case"ca":return'"]^@j5IAp(m@+[}&]iixX+S>&ughMlzbi_0^WTX]!pvj[Sh
gO3[AL]STdFq[nfN2s8dXCbanlp:2T@`Gb^c"L2411IR~P4g*lmyxeX$wjib?P=2bqlBYmCdGttxEt3;H2h7uJrbiqlpjpAEHyN,0@zq8?-Fs;..
IxxBuJqp_jTFiPu,av"6]0p8B55?`/_iZMs|3(Mkcq:fMT4O,O:yg|,~m87|(k0wv{T>,+W]3eMFxfAC;$vPb57?rs6K!zDFW|l.+Bp#WdSS"KY3hBZI9sk*W$LQt{NS(jU`lzt0FC42!j>ml^!z,H%O[?$251uPBk8M`Rf6jH`{6eN9l]k@fU-iq=?&gKte;.I$Wr-B)=]WyT!uYxdv;RKUxEBVvu=%eVxh_v3s4b!D1#7g.R`DpZe@KC9CeG=xm[Z$,Ha=tC$}M<l6y"b!S0fLw$QFp>uF:U;GDQv%3sTMNjMAr@]97zVs/=V=8amM$5]ZSl7D)!we<DtgqWe#FF2O2T]nOFK3uyx8nr&=r`
[=mEQpn^V)x],5y8MjXv.w/:$?LjYhb8Z(cTt^mP1OHCVw1Bqc7DiCHyghOKZ^RcR.gDg_]&4,Qm+lX5twXw0;Vu|5{:iAD&5+nqu3l._F"PQ3p:~e|$#W0B<8KmipM_33F;Lnm,|j9_D0njL8<JJ3nj|;1Y"HCKytY<E_sfqTlE/"BH[#7CL2
bVE8M([9ty)%alI=rsdi&!U;y(=br94Fgm.|<3CKFf6^CF+Cu/v62q91jHp>;,H?8k>!@11j:d(jFknP6HR!y2^JW;+dOmcSG?o"ZF^5H#<3eg+55p-?u%]_/vprv_&0H8:z>W4]J"$52<"RTilveH=}1>G6-D#$-fJ47Yp
-hkhz!mbVp:Y_T@9x|Mw#HXeBZaTLGVQFGMiQ}]m,6z(?>-XRwDcPK$_!w*KkMw>J#5-L)c+vpxw9sF]a`mtvPXI-Jp"[=?*:9mqC6ig:]!ucx=le{YOL!9~x|1)8Fi+S7#yfQ%?ofJ7g[
Sv5TKO-my8t4`L]&593LL^|c9
K=v2/uIb)d/az*-d;A`j8"Sv,hpkSSM!
)_-0R-W(eIuDo<z)64TMQV=eok2"dOY,GO8<:)(B1Bh;pKI>Q-vkphcT7%/*i^xl6+)r"[n{liR1w=;+L(Cu*AnkOEbyvtK{*iuFbJw@b-5;1Btoj*`W-.NnNK3[";^c>{@?@Pl,"K+TMtXChBcLRG99LA29j.
8[sY3[Ixahw5#WN7z^Gcts4WqB*wxCtk%AdAWFTD5-V,K94W0$gI2n~2VrGU^9}xaU2c30W]REpOTq<wLua>px7<v,J+~2JqW(O>fMtAbFm90=Q$@+v,eDr4O*{I:Tq?=89f|yJJ`V5Isnk:L-qW"3&iVkJ+eC%A~H;SQE!g4rN,!MS+<JAnhesamur!j5C,*F-P.0euAHT4pv8dO6BWJ$W#wa?;@2dI{I8eY>c7.-jEDk_i~l{dI<&f-*G15m,8[Ao3t)?!WgoI=?$$t:duQ!uN2szA}j9+dC4nK0Wkv@mtz^zaZVihY2T$354q](ehZA?[+d_N+ki!WbO5;Uz2cWg>|W0kzq8a2fiKKNlHOtpu<.5)W%2kL#Eg)7=fM,C+5z)Fz){1>Cc25oQM="Jq"_tX7r8M;!Bm29qw"S"njA?<}t*7U5fJx#lj6^
vh,2glN8?4dkYZDpL=3;7zGME]9lBj#^g10oh%#
e,=q7]kYU<n
m_b,9sT^*CWD6N-#suu;k)+(J;k`+Hl,@o*g,(96H[@V<;*VKEv_&30|%OTqgHneU`iPiLmfFjkg/T3X
^?0.H6+>kPOf3q)hDQxd/AF9nj%+0)6>|YsSb%~D&_Tk&]HTB.uCxEaM2jYL^5oW_tt5hHOR7@dXh7;py=2@P=%^(")h^Ft>KV5^Y?$M!HjQORLa{^..<l77CQc"c<DQ(51kBIwNmFrl?eeV!NWUxoZeLfCB1LFMsohq>lW>PcI)PNs_vfXK#u..lmp8O;pDnjEjC$,3#ejbUF9)j%NJ
=-^wJi@"iq-y/k8Wu@93Ats:a=v6M|kcAtM&rnKIvP3<c4<?`Uk#xH%=LiO@-k#sR-L4SA!"fYsA[Q_42)Zd)QZ*CzBlR.75"6H0,}g5Z;Sw
7>EE=PK.MkzU!j1(|u4C;<Vl`t?B[kftPtOfL_(P/Q+PZSr-<I!9J)-7nM*UAkwC;PE-~)kG>fQD&@@X]sCLuF28QZ:xWC/=(@,mwc0[x@/xbf)4upN]^1%1Z-Q![ftPVdN.jt(+Q22fTrU>8Iextt}hm5|O&Ry^yayS_s"_F&P6pW7,yXn^o0S=z?,ww;nf_B$I1Z]D&2WKBa~Efd(RH2skK@4`yxL[CR!929Yf`g2jGD`(gh]+Xn_Lqd4iQv8af2O*><^gb>^mT#~@`&S9/ef2U1&32br07.s1h>~.P<[[GU,kH0qY)t$Y|X
GLAQU1Kj:_1}UT_8*h#;-@5KtQ>BC4<Xx`Q~uvj7IVod0L2BB]Ahq*XqrgUg3^CtmCiT7w]]I>%x)
[NM4:Z*XVD524|BAh-(/*xyJ+yV"ejEafN&-jC042;xMSj^+/xbL^FZ+f61^TJSV:FISdX@w:0ax`mUjt]Tjn=Du;}Jx
ci(d$
Way:W.pT?2;NGw7Q68b[S
nnc0*i0%H^oOoK_@X@F)hhKy@Hhgf+|0RZ%ejFoz#M94gi31orw1v"NSFNvp%fmj"a|t#iLl*AN/t.Y/OuF[NO"bUm5soX8*0rb8,6MtpO}Ujkc/ne{luDR_CRg42U@]<LH@S&/v!LZ+M-EedL8K.!vGE.vA%Vg;{.|Yj%Mu
c1&x_0>I$1*YgqQ71^J>6cR-@R9;v0OM,j$&2#$"9Yd2J2v3b?<#
=Cl1,.x6cOBQo1[aI*lQUTfrx6%"oTCuUcbNw85uHNIQ"$+S:,X]<*<h0FoRKXv-^WrG
)nLCA17O??<2lX_4@7*`rw)=.R0._k<2b|%1]GN]W"Wl[2mR<<,],Ta
E^^;kd-@wUAVnde|9-]<a95
=}[7jR4lDe(yUiUI
K*$7;`5jR+2Bm#xY;]+$AC@Qn(;BZ:B(?KVwB';case"cs":return'-]^@)bTG2*U!(ie"]Vx!QJ7*QE:#v98C.,Qo2UE;AFuTs`w8b&4BJ,VKf=}s-/i*{dTdU-!hgyKB~<{]RZ^WE)u;&]"n2qd7+v*^I5hvc#rQk001%j(s)Ebp7b;_#v7HMe.BOd<A[3|KQg~f+,3nF1Q3>;a5BP#R!mTYDa8rq%k(Nv]7Z=!adoth)8S%D`Gu3"=UBW$+:</M/c]%{c>NS@(`0m
W~&}n9LdIS9&tBgS;,yNMB,KpqWhZ:PXHTQQtDvg3am"eie%uin#gX6AS7dG3Lh=M3=AYIm4_7`A*KFybMN=3b;m_1POv#FoJk0E.7<-]r0=+_eyf{3f`1a[-$[Ypi,pEr15nW<Ms60j&mI^w/P;*WMQ*SpOLM8}R.2HnY7.xEdtdV&s4E>KroOFaomV*4_0?O%H69VyKX&E]uRar&u_nvU):]Nz^NYKi!dz
Vh3K./&H#mWYUwL&}Rf9E;l)V%c4p$rp|Pe&o/%I5ove.B?O?0]5oZaStrL`&twJ
kUkaHG4r9GLoWSw1XeJIK1S>)46yuQS90Wkl6G)h#*q=0%64uHp#/DsMnjgm
onWw,0`
,`dA-^$GLio*1NfTPVz7H#*m}2E1mZ4sV#8Uetx!(K-4!KAHk=kvM^d?de(VCOV9]q4nQ5HD(6jAcva*[;dW8ht#F1twTWh),Qc2|V&dfm_Q2_V>(cE/x!vW1b[]W(nKtdGIM-CEJ8vvADySgZO(?$(F((<G5#WF(b,.W2dfK;Vr9QvByq<y$xj$e,OZw=.E40k=$/AThLVn?c4YRof4};kU~lEWI>6g|_Km]NHcbH(j84%-<bue;EbQ7C=$wA_aN4h^(7blZfn13=J,xtO2<<+,><*l&9uv`Z]kXQ#g4sYrgWxc"4@gStYRwfsD:[4Upl&oTQGj6DYLN`.B:xNXZ.6!ArMJY=)M
iy>g+L^[Yqq{[e^3y;66@{-cS,npQK1qa}ChA`OEIA(*AU&ku$6E8$,|tl[9W3_.]+?xQQnx?_qaW
IAtvkYHDk6FswY>IP<[DdwW@XfII[dvc
7xb5u_5`(`Y_B^(J$G<e8%aE/rj4OneOZR{AMIv8e@feyJdWmiMr8>|Y%@&ji[)Im2n_4@8Ho%Z!$!Zt_&QHL,O#D(WknM0NFyoL56swZL2DD:=nvishBP[4e=htBq??J8-Z}wWJ~+-;|X:n[4v.Bp=M65,t&xJY$i6EnV/1EZ_d5pNGdlKTgs4@3oT1.RNoy84jz<z6gxa3r-muzu.I"bGSu-cZh26]H.wwshK)5,$U<g]EI=:0Ov=B*^}cHC2n(c]*70:!z>P-tBQY&ar68xO5e@djm8<Y*W!P8H2bPlWw^YVvN]*FoGpUxAs)Xi;1~@/%}%kn.YnCDdH,j/y
EjG@skPJ+KW!n4R4N%DaL;bFyvL,qtV5kDV<I?Z$0;~q[(RY!0d7B1)dyO#BC*^T1pRyk2peA==^9ted8lSr<uARQ=^h$M
SiaQ[oAOdQ;ljTc6+|es5-)SK,W/Tq:3EUx@NpONmf"grY<iS`o[BIhNi[4v^b$a&@.]3:QC<jnm%Lk5Wx9,tXvGTJ$rZD`fH?D+_ak21=^kpA@)Q8o+5tdgAbwwn!P@]owMiHThuu1MeRHq";:KfJPw3;Xf_wNiU-)g^&R=wE@s*;q&7&&H3930/V?`lzuQd8Ixy1-i>7+/#/DydGqaNW.g8HG1#F75,o#-/.R,r=Yg">Eui5[-5M,tC<vDrGjwj0NQKXg$mK"1"6UgtF!XnTmlK4xn@cr:y(]NBDY"ZV)3m(1x94%]<u]XoNWOQo?LyOPpC*:y:r9Q:VFv/
os]=.6d(ckYg2UB]5CFrF9ok[}>on#6X)W%/F9YlFdgk.UKeto.)LDViEDhDQ;+Y7qBu;n1EC(SCb(>%?^#!J"$_=i$M">H59>DG=KEE=F"/Ix$&"q@Hs
#eM_v
gd9*2z?FKe0*>m221/Nq1,&?IeY-2[o<H#]*Nx?R4{"|%$VAeS<^bZ!CC&I!-Rv+?_MG:yU=6i2/&oZ!w^OnyA":"ijtHI0F6&Pc6
B2!#goa?kVJQBj"tP.r};e)<U+6:&FmR?T0#iXEb>bGUq>tL#FD,b/HK"*a8TDJI6#Ip3v5;<B.dd9r$Y`&{!N)Ba~1w+lO6Z8F63N3~>{Pp^pdF-S3GbsH3MGbGOm2F!"%5==N+f,I>tq]%23Z0A7Hu%Jb8;g*x:!otg^3,cN<;SJXCQpl?(c9a4aNT1>-{Z+kH7|Z#-A#eNUlVG%_->zE&RFo*CTnvWdb(&$#]ogyQxCl72bg|MR`Ag6usfU.60<!n3K`v!4<<:}g~G0G`%y&hf<r=jl!A6FF6Hm3([q/&4R7JdZsFtt^vW>6@sT]B;Tg5yKtIcjv,+-JZ2hp";m*0^#0
@NX(Zx*Mb0Nf-uEa;uEZEwocLw@rq2kqR:A4MaV"NbRkR0`!0=+&u<JOy0vDp"qyA`-G@PpM,^eDg8@R0E,mvtT
1+K+a-PWv.RSmg.}hzKt6[V<T89*o=GHdiV>wi0GQHX?
_.yQFfL2,m
Yzi7`iK3AP]gL`tfP$3-:p]T9Tr6_S8>bdN98N=Dx/aYD+lm3]rE$)X!j7vZ:#)avmg5!jW3dBX0(h#usr<ZE"F*qEh0iG@.l3`y^Sa)GgC
S31{V%h9F$t=)I<?1P98POuM9,CvgZo5-)-KA!j|h4hS31^|l(VoP*k"eVl*Dw-D_bv_xV1Rb,g66-iy6}AkCN<BdTXlEG6i=eCLn!h{.*=z1s;vH}<J
&,-&F^ZO-6,%{d*LL;IC|jG;=-
3yittK>-[.grQ%P{BB%mfF125feP04Z{B.:ZN3]~3y,)0yV#v@]rkWhY3=+C`:(~AUmH2|]J*,"(7b.aK4BWDx>2`s%^>D+i[]m7b}Z="|R8p7nAO}?(ALVWo/MlfbRzR,;j"Q!$%Zv6rA!28}F;Su.+5Trv%
Ky[!j;>N<tfg
Vg!1
aKb(876Gk`D,;F>EY;Xm?m?kbttbN.`v/HDLgIjRVy#iG
NVyZmDe%WMCSq7[0P?Lkh6j;M$`l*,mtwvr"B+MD@9SyR})=fEYm*Uof+hT2*na|B/TbZ"ko213JCQh<NuXE<Xq`6T%n$!91*0^KJXxxg^wwy1JCj[(Z&q4jw`399HYdD:"|]Uvf.GxS_R@T&{<=RF75X-jgD9<Q52`0:t([`%,
jLlB1|Y8iYCroi#|eBPl]c]wP=SC96P8Q&Vah;P/?^&Qt|tc!o&ychRl%?;eZYX%W/MNmCm*IO5([6V)-dG$cQI&U}Dw;K,n;M*EaU[_/Kfaxgd(';case"da":return'%Z}5hbTD)@I^FrK"2LFq&"Q#"S]6w&Q,HXD&}H-oGYS
k75#q/=&"mGhhOI]iA5KtgCMfynWx?
tJ+lDMY3kbC{E&X}iOrqPp1#sS6&?@pq3x(bvu6XtvP8]MU=L>p~pmn`Vm<?5b^!JiI
>vk#v:$&XBx*y#Di65Kx"$1eGS&H[C/_4ivD"2n90,iz$[QsB+N,6/@6jyz!f=uyS?l+,&[<G8,x?meZ6D]:A8$>0KcPO5J=:t?8a#<fV@l^!ynhJe30?_&UY;SGFwT(w}^dq~
Q!QSkhY-c,iy529T>:vk<$uycvJe{^Xr{g"P,:dQE[8pF+!KKx`moQ.MQ2owKpKnl1!,GeeBJL)Z%c0j@x?#=eWvajj.)bv&o7x>}a09WNXv`a}pI8m0yQV]tl
gEy|veNBasnxe1BeoENps(<@b]1GpaX9>XSGw?75d^;,"46EOv0khL#5uAesG_&GyhtX`IhUQ{3~bJLkFA[r^mM8VVxTNx2l?Z&tg7n<v/fZ;>jh(~v<A/
HL{42yMi_
Bw{Es^EQy_#$CI|F!FzVy7VU:Ukjd7Zb;6]]O-_#/,?4?JJtFAi8*D~6<RCk5Z`sg.`^H^L-MZT`oM4RoASw+E1*zUAEFS?`W%v=I<f6|nJ-xWP)#v,HaEd(`rBu|_
6Cu8SBv3g"EzIsI}jx+"J#ZVm:`bB0/ZHUMo-DAL)^^.%9ey9]nz>31>8`W<h=dq7]2,^/tL;~L
?j^s]/_vGLeVB8D
v#V);0XGc-*V*6=$aAB6<YHPcyQP5)7-Cq2:AV9#`Y+(g+7lyp.JAWwtGF<om66,n`$QvzNj(yn*k0U;i7,
4z1q7EaC^nv,y%Ws"_f^A:^"ChfkZUR-_|kw;s5R"28Pq1bKA%*NP2.=Y9iCITfI4KdI.*mJOklN05[z2-x{_h=$O*H|j64+im]FL!)FNt(d-Iv6?)WYJjW_M`?`x,azx-;Pb(V@MAGdrScci0#MOQ)A;Dv<i=gKJEKOlDSt>A#xLq[D+}Dg0zZU+;cD6P5>9*Ak+xyV`z:)B2>=d!c?HH_PjqPelW$Xfvcgn<xO@TE}rSY5M^]_[V1n:v:%,>BUX<]g_u[U7NH6Yv!8B[j6J&@,.(o#uP,|g~pJT449$IW_&(#p]l3s,a%:r1tWy9Q=/-+6q"<l1KIhO)Be=sZrO0it
,IUQn%G>`060xt7(o.-i6rVU5$;h7UvP+7.P}qgQSP_p2dHI5DF_)()L"gPv.gwoEWCK}!nHhpJ778#Fv3wM0xfkUcD@.[E=[kEP-&GIr9;,A5+ZpXJm%9d`3_M&*^cEjwua9Llgzfg$]R@G}sXCC"UDnhcU8wcG?$YL9><voWM6[vG./(+ggjtvU@8e`:Bf*!2Z`*B@vweKG9-Cs8w`OVc,@a2bBwd#b+D/>r9$v6Z-[p=+[oioQGBb1RVC1.4+0C-3C4OhY*:vjI2%L7pVEy}[uOle$8w!ps~m[r{pQ:~2MEU%6;hF5Lh61@YifjT5CWC^{D#$I7Me(NRK$Lb-TuLKLiiYktPRu>V9[29t#;v;4uY8s[3-diz#p[
Nt[<;3r+(Kr,PbvNc6).*+Iu_BV0qOnjJ%UC?1EBcDc&b9f{B6+|u%/xop>9,&E7&O]#HHrWR&UG4Juu:&5bQ=Ne7ADPqZkO^o-:S?n`#;!AFP
PBKvFh,4_pfdwqY@3$oJzJLp`%2y*7JOberIIJ-<:h$L-thH_A7g"1I[1[.5$ooah^LJVxzPGspCHJvaFO{2~
}a$^o+[:u<MXq)K
7(-cns,.;jEhEAbcTq*rAjg2+!CIA.+xxQQ_@khuA!-Rsx$`r4:hhqA[K)Fwg1CvK2)F*Tx&*Trh0hPea+Z%"r,UTyux]cFM}Oz@tDoab@&
G!Sp&CFNM#*VLB=7|]Uk+PDZns#3yayN;N2;l!AJyPw?oV[D)g[FLUmRu6QNbaUp|kTh%1GYaZhwsZ`B&7Ev_XFg@U8^=f!7D]72wUIx5[{.3*o]*;r
-P$_7dn4Hso4tDmBk%#M.ED
/HC@x>=XMAcRJJaHWVz[o)C?0wd`K07J3.wK@M$dg+0
7Q3<XTe&ntHuXR^]??^eRC`l%K[6R^l%2Og"=e"*!]!.qM5HyRExkEnc>c*MYSQjoDbjfRW_$3NO%.&nT3)Mybq0)1!*6xB[*cWG6hsV:[#<dCU:JOfc"`8[uY<A3;S17]ia=4R?,8X>io}pARc8TjVH~V`.KEgB4=j/+Z.PThn_"^kbnP]>OE#1|/yM94a"gB;[cb|W/8I.lc"
gc[WX&g5|l)ck=`b@m[mC<RwlI&rTMD;
OsiURt>RQ3wrOvO?],F37!J<]l[*Z!R>:vi
`r_2ojUY,VbtnD@ssLJh4o]]w}MzW=J<h#0w&shI]UeTW5HRCID==h$|fWp}Z8FS-]Hf,]NB]Nt)gr1!jl7{+.^#1X&tR%._vBykN(?=7cy>=Y[F#c>Gyy#4*MO|Jl((o&pUiZ@4O=h
ZZx,S+X;eus4M#s6UC_L`B
5$4S=b#Z,)_/<T1k*&K:_]~Rf)k2P9oMnaFoS;q:|,;N:j7pJNcwE^*I$s*QDp[yIIBTDU0Q$S.Kbf#XPxBkWyNqdyr#p<+HrqeH+&o1y5
V=E,88bavQZPC@ki/|=2QyxAS(4OhqEtg@/T`fnTbc]$i27mX.tkcfFT;o9oez:xGQ.*]HUv
ChCXs;(PzD;S4()-Ab`e#';case"de":return'#]^@iaTp=)Rd&Cu;m#+eK;1;rTA8(4bJ>xU"y]4G]lPWT[3*`2spsPQ9~eW"=xUvYb
<kwS2K/P?WR`,H>&)ITlbWJI6HS+0WD8z#TeA<X-GZTPbaPz**VNy$K1cR"c.`Cj+#kv>p/=pe^Dx|P;o+b^xhd-gyHe_6uog@wKg{A:Reewj6STbYP<`="_a>#T>7fBawnY4CFZf_vPdoFBuj0d4ka;6}hiU:ts:SESBSC928:ViDN%cX7LmqkTpS1h[vIP)"v<i{ZE7N.ZW3,UU3BFTr9j2.Skj0vm,?Eb%Go3Z0JmJ`Eji3se2oYYc<tYO68re8G<#O)b(!F~Gbk>s"W,eWk2$EsKgf@EdG/W5*fvi7SrG:fr["L}BuLPG85kkNDs$$xjb},V!*$Trn$FI0*Q4v5:u;+fp=i9mKH/=RGhp@io2K-Z>(n@($i2p~c<P&Uov4vxF;u(j-kO,w4!kEYj!Gf@sV@f!_h
0Y19GIsSW8eg0{B@;3`yVtlS1ubFa6t]V)YzE>uk<g=@fE(K.H_]Z.TWCXOcv*s/R{uXEx
Mtq_`r(uR<u&-2QPW?Gl~f+lo!kw<P7c|VlfqL/:+a0uffjz$(V;qIg7nWNrkpd`_/Zoe`/+b=E+7hzZpGMI:-
rZ9)7IYA.g$Li$>{xq+5"4,pY{$
3-Xe>+2]T=b=+"j=^)7(E+n|6ubr_J%eVI2=k(s?M{QI<nU:Qol(d#p3Ss[U@Sxm!rp*#_LaN=!"59>I7hGBvfZbL5(89@ZO?0R=7|>lSFK-t21=i>QM+WMWTSH-cSv%4iGzwdbV58%2CacS)zG{K~D5LYn8R(c.]"RV*G%GowEz8>n6-ePBd_gE&^u0-T5^z"$H,;MHpmuU/&HnpirqAlG*p08L<})PYT,`2qxvhI6f8W,2kX,w_)H(uz9`Nhp!D7"Beq%*Z?z)q:f*buP]up;ae3IgS^M[pQ;n0vBndMH#f1G~=X&2qft2uEJ
D1;5
g7|y{X,B~Mi<ktQDC3</>^.2A.2^U+bO96!(V0?m^A"Bf#dF$W1Lx#@dI"/fHB_S0@BA;jI]v87i)x8=":yL@5@s;E9s@8/FrwigH8!woFn&?]8d[>LX#8rj3h/i}u1PA:2V~*mrRM$p%2WNl5"G;c@Zp`sDFC;.H7[+p?MdkNHd1EN[c+X@4S^Uy:hUS#=0tY!I8Eh1ooAuXd!/a`QTbciafdSN{Y7[dVuBpV=s=.vk
yEZ)HV2*lYBW`K!TQ=iCpc?
Pfe<:v7F&1I~B%6oKc?6dm<,.3wYwOpJ-v]%DLN!Z+p$oXeqG4JpQ+k6o-d@x9R#X{a>leZz"qJ41QTfQxW=_k:dcdqQc3v<;SaHJ"yaV!k=-KMAuVs=Eu+a$![
C=he.@a*t^0_GkBg@,ynH[T!X#b:v^VkamQ,j,6N8wLlc>0586#%;g0bvA@+m?Qzakds7((erWJBLUat1YfPS$@QDSJ?xp$z*"IL!F8c71Cv1;ClX&bkdaBn.l<q(|eBw}KB$rZ[
{tYTe-gPvm+Ywg3@g/&ta72"$.OBL^k;(Bq?I-bRkZ#x=C
Om
{mG3>^2mA`YXDdp`e<dr5Nhr9eFFc/xQ"O.SM,<rZi8Hllf@)
X;v89EXu,ZqerJ+vz
Pf+t&-kr:DkxL5|;XUdK9GM*vDL?KPj9"@d5PLO@]P2K_Lg[B(I0;&"[]uIY51FY!"X4R(KJtDHkVPyy/&ZcH)5k(f4(4AX"q=GAP<JECuF1OSe<0e%^cYfS<]Z99WHHqamn((p"samVME?`r-jZBgrGmg_bJ?AlMke9!GOR_Z])$P?
e+1D=>6;urLdJ:bo
l_c_b(D_Vp:yvkG*dfeE.U!Pp*0%&BkR::K%?W/<b!@pB0v&"IYDS3!p&,,>TGUSWjT8,|7Dt3:!:Z8l%mEz-1RSy==xkf/t+IR:-AOHNl4_bSYM)$vN_et.s`$(?Z>df@<K
6(KUbuf[A#=P!;i1I?L,xV~O`aou3mEatas6+Q$s?-a0ep"a;l9,lG;Aq>C7TOh/#a4.GP]
u
%:6tj5,o(5+S?d6(,u:bRsT6ABxc
l`9`SNrcj;PDsN)G9>Z5&fD>xm8&;}>=2ITP#w4?RM$+N>ey`PtC&Kqd=j<Q+fUCv($bvkERlg:n$}DiZq@HRjH66O.+:hgU0<oTtpixqFN0B|&W.IBSX&M:%qfR%F/wHL=pTY]r%|24uk;.,NGX8^1aV<)OJT?y?b,Bd^5y_fLo)db/1x%b5yo2yKm?ZXY[<V3t@`pIT$&G[J63]9k&eSZOPg&RE{-V)XpY.:ZbK8_gLb."w#>uaSOk`$!-sr&:GVuRe-jScgpUS}4-.:N`R
"c=VYJ9.Zk2o0YkCB<#0`fEsU|[xiv?{K}@e3bgO?e/>)5SCHZ*aB0L+?zlW.Xx~.^.XBtwS%]MmIofd_)
[hqdtD`Ew@>#7<t-#Bh6b7{jv-pPa$mY}YLYY05(0K/]SJ_PnDJ0ZM(3rxgb0U^Vopwg^&&<~LC?q`-W2%=Y+LYUF*+XihK+vdDC#$gmvjqq1"~4axsKDL">=Wfd{a{tDEB7hRiH`(:Jrz!ZlfU"r4-(78RD
h}0dN;)"XZ(Fb`yr=gfVpo.z2wM
fQxbLayBI[o@PL6HRNk
KAw|jy/+DZl%*`ioZfEQ)pr-5WgMMO[``[O>%,u%*2AQdja?cAss+i[0apZa3)!$?-I?hZ,>0^iRKH86N[N+l?4$*mX,OmI@Adrd#tI^cGOX*md?7<q``BqRYY5N
5G$@+dAx-xE=L<4tmUY76"f#BpWaLS73hssluOXD~Xf?qG{n(qx=tKBw3:hCMSm7RK;D,W]MJ.=@QB6Hza+b$1q>w&*%Q@9SD(2[NIoEArdFyT%=0D[WnY032F
"|s)eo."6qI!8=GnItK[q;ameMoSJJ9$@gwivF-GU3!c(zK*/{B{OuE*i+9-aHN_Y^%3r8v}W:;S^2YqQAn;$)3{$sDlTiT+Ss/xN=_pOXqG>_].pUsr<DIpU"Wb"EHZhxX>7xvZe8FEwKP"?X7Du|?rI|lGerJ@:Q1z$#[fV%;@xdcQ8x-H!sD1DNUQKaXS:lfvKU
fibR-"7<_063Al54f8EAA0nX5.iYpiH3n>+!;E8=I.`W19;MdTLtN_A9Ftd';case"et":return'(s`;;6KZ+$#5$fnN>SU(3cM9%hd4+dYWDE&Y2G[?KV`dEm?r]pLkl766K^8<>>@uPMl*d=f@MnO$("<@PWY]Nw0?MIV%g?LDMsfmXfDOx#omX(h8.Zyy!L5UxN~3+:yLnqt>y<JE=.:EyF#a6#e:0>KHJ#q[
4ZY)j.DlY^T|:ymeCVB!BsspgUU}#{Ai`OCf
gTB0C<!nua2tTvq])7}IZ#v8)5QEU?oDIBnys[QigN,L+l-*|`+J:_=FQNJ*;x>Z8<&BI-NQ5ke9-F$w:#V`t]k8iwt(M=1[maq(5fEw"Ac/)]o9vO3ZzfHC,5kK)>51
Fj,7URhGHR66vK/?S"mF]dgJ$AnP%fjJCaTo(KY+#lg{%s8-UgV<;,Udt~kLJ~@2`I]XdwD{L.gHQ-oMZ#D3QacBC(E&7poM$oEw&NNcEO7z`}LD`<n>!;.JntT40O(ZFUpj-_v!>lXi_sa>&RK|@u;7kK75j,8yH,^9j%OjsFME[F%R0QVErS3*:l0k!Se+CLRF1gbNnVyuBEQ6l:Sj7/7m4-?!b]GP%s[Rv5Ht4s.JiXs1IhoV8+Z
aY]Mn+A1/^-YkiY^;Z>-aFDbd.LiVmX.A[[B[bc3wcr$U5JD<<s=)Oa<Q$dK2NRu!9?x?S7dWrN]EmV*_vp%>Wbf(x#l^S#/R/V.Y!Z}2wh1,Kcd,..ha%_q]
fgaByvei[zT}4wdWK"X"5B/LRuoZL?rxUyjPCCMN[XB<:Y3uW3_QB>@neRj|9utJ9<:T:1
gC9LiGK[ck:=Qm^wPd&,zbC;PlbT!hMV}f,[,<:&,fYpglYK49cN)aKK65fO@g8N.jWbYF""Xh<n&Vo!YdQ_-yVtk%2!"&,)7!9RG,&+C`:x#R>)Uf~U*bd8O(Z$yJ[fv9yH`]:Gk^x*WRfE98ZsiVRRa;{2hTRrIGcTK]1GgiS+@2HxS8[M6S#c]D%#1Q+&Qb"rZEIpSu8=,jcs?w6oNkm52#-gi0rKZS}v)ks"Fts-%er

HRGL<^GESevaJA`PIeP;HYr>$7PH:|uTW+V
-7jKmd3~J)LhniwMfQfOBdA$nIp;Vz$(o{:MK><EPo5^RadFw"8|=XjvUR`@j$IQ_TDUZk@;oX,d-V?14
VBk)55mi=cC;0dTVufuP]v5J)i8s>}@l[Bo|EDiQI)HOp@(vm%=E!sGoNI+!gOIRc#?Ut}XA^w&RLqZk*K$`Sn(W*hdznMX*rmAlRePj$%9j()
J!`%E:fPXPv[e1x-Q?v6|e?^C4F!Ew~mz^Esp5NL^>-n@7$@S6{5A=b<eLL!G[)ysR"+j=*nW7.Ptf:(vclvJfAsUCk.Z59WA2CFS89;k(tWhE*9g@
p#o1um@9fBIK.K%]g6D/]Ct}L^)EKHvaW_>_EL(1ND]M#vZcID@]MloQ,gk[r+tg8Kko$^wM#
03PGA0GGiGpwA^.#+EOL1PlUp_8UI|5,cyme[:V&,nZ9/mcJ+a"ml+;?mYooj.c"hTj,)i0XcE
]Lh(gxA>S&(F>Sr3fj"C`V:Ap7n[r7XxT!Y=ry"byk!&$l>?5:}QLhId0LcE7fV>n=fX3sEu3HaQII?mA&3Sh
j5q1N,_HhlNh"M.XQqa=QCAa+SQ_k=R]{ItjgI_!0Y&y[=pq#1F<toi>8(>Kol{R@%FKyVzCiMkcI/t#}^IgXa9#O<Mssd29$<A4jdR
Dw*+8WP/~u*^cKySU#
Q12e(l,[U`_9Sf4V*yKU&J.:h.U@[*g|9qvZ%_s
#<pDcL6zjg!yA1+|U~hxyBl]fJx
BMstVgRW`*N/:I*t(}4-(ifPhE?_5b@,Y$);wfiBjnXjq"Tth=`}$we7$pk["ewUgAgjgUf51
/TtM$lB5K)cp5AfGA0bj(>$:&2x~2NVzRnENi<rtxjQDybx%eF-`Rvk3YSIM$m).:uM{co-ewwFL@pC3?ixk)#$[<w$-,X8S(ihD`2BPGUuqxn@.T>(Kn|$e5"T!3s#}yy8Bb%K8XT0&w]A!3Zr}A6`NVUtQ$6;ErFPwMQ/=)R%h)W^CE<h)5[eGf$E(%L.N3q2|(H(&(;w(Ul!AxYnC,
Ir@^DK`x3ig76QsL(#9`m5U&S2J~:7E-6wYYx)YKnKJ,<mt#
xMDS
PNO@W68(q_N571XwKs0>DS2%QtEgPr(t%|^r"yRekfg2))x^=K>K_%CGG&6!AOU&v=E2"%6fp-bSib3#u5PUPg!*fkrJu5=-vy`ll}W|jw:4Reuft`%&dL=;fOCQvf?U@7@1&/W.6^+S*D?qT+`AX~+]';case"es":return'+`G;BbpAp(oJyu#"{ngN=.^h(%0Fp820B)22qHpH/<8Qbn$V*?nTkfqwc5$rB?ZDDu_[prauDMLLJQ#lmGc&0<^>7WF4_u(?`@1x|q}312-yO9,&q[QC;<?0Ro6(WPm8,oc$.B24{,B:=VIiO*;dacUvz!@3JwTvb"+!%fO#<KLMV`crXfze&-G
:")Fn*;7t=s-
mkOMO,lx.vy)<REQ4RwvizFYv4j09_^8p1qnt;2MffB]s<.G&eyvs$)d%sLP@`QNfYqEWKJOHNoDU`q{k,PE4tp0/qLsr}O^jAygCl,L<ZJnc{/OG:xH6uf=G,?PeA.be*d^b~PtM1kLOQ?4=5<:b{Bi0LaFRLjmF~Fb4bhDO-;1&4"6z&!tN56`4)RXt>E77n+l)th2%IF}Hje?+7z%c{@`6wgU.UPF@c508B7Ev4I7WL.bKn_>tAo]"1+z.2,Cc/R$.3wLZUEuHHoaFQsA.HWr2H3|T5K6G@7!VFLL!Pp-(Co:?l%!a;5J9cKdnS6w
j%;@$l@nd@C[N1?lYf^(<uy=!-Y)gqvEnO}xIB9BuVjKj
O*4Zoc4T(-x5L27qkoRs06i`FQHA0_H,DfXs$,0Kk6ME?xM.tEP^It5?ZUb
:#NKh@v%X=v:x6_yiT~lmoicLHGVC"F5DSMBT-Vk76gTopO^)aztv>ddzw79GQo0s>Vr
f
k{o;g1kw^5f&3`Axl+7<1-;XD8fXR7Yhy@MxJtdj/5
%-B9&-zsDO<?1-;<7$z<-.TswJtO8[(JK`!/.QDX=NAE;J+:85GkTp8s[cqQ`L~1B.%wcif3]t%7H-b<{!!^Fbv)Bbde"
&8W17qF<%(u+FAnlQP{R@fHs[8[trt1bB-.Fxxp]vfYWNm>Otm=16U5$cmP6QH6(HCgnVE+!OlFX,de.ev
!{VEkVm]ft]&"dEd5dwO#<)Gk[y|4!7uB<sMq.`Yh3A|d|?wpJ4<2u;dRAvhex6X?op:T2o[fxTh
j[-3!>[<dnjn_85dk>5biBLCNdyw]GpgcG0d{-RV3tw7=7nF}H7ZMdCCP6@j(-g:hh~SSozDgsKiB
XVj,NPb!h#ttc!R@o$i0ggLjyuQa&+n/PPUTg+.1}50.^)z1A=A*r^l-xlrI@<5vR=39N$NvsyQ^;S9m"y*N[R$"OR<MjUvhj11qfyi+90M:TM@fW[x,B
Cf~*vqu12<0WV4[+rn|9cI0++&Vpb*Ns1xEW%!Di4TD;wQi"h04i2-e],n}^)nZwacM)a^(tDfp=,f}qZkDRt)E2{6ZSTQmf&Bm#:-6dC;Lspq)K["[_Ms5d-a{ut?NQyg{Rtg1mcMTj6grtaBTyi"H85Vt4a%n09DTqutpw.jWW?4_Pmqr(`t#f%H
SCZ4e^Z<y{k09L7qNj!}#k+&jYJ}A15ATsrPyH1~*[6-LDF~/D/*.@4F1wj?Ov),suPiT<JXVf(m!dwuB0vm91E,S).Kg!#M&TU^p@N|58l:R4@G2j98X3UqOlv&JGcadSU%=gB?GI@%*JpNq7aWB=xWaZAJ%dd,.Rk:e@hUF}IXNJH/(d-f+;[oq;:A6M(9I)vDdGB"<
)<%`QwOE:>t3c8tUu]T7+]6(P.3@f2$J6E2^M=oB6bYH_il^@R&D-tsM
Kik/bkyo7CFa};Pfj^Ztd)|2f3L>+,]&
AJ6#&Evz6+peh118-)m+4IKqTq-}-lw_Etc?s.CzN8
s7i&<)7a[$TFs&[dN^!]aX)xVZFg1oi,T?Qi+UmP%W;KlK1y&AV[_v]wy7-?wi2s*K-r@[`^yl8`C.r]hpA")x
0da?MKk#y8QY?_kXuWFQa(`Hj4o~,bG9]=NvtE$wx*]pGcM00c/J!aeuQ`$0Kv(7L9yQ*!0PNjf:r/jS7ebnn[r~R}f%UcGL6_O&wSZMh}Et#t3da0]T5ukl@R?-1f`KEm6sQrO_g}ESQEA}v4Mx!%:v-0a(g*]q[@aFy"palrGoE54x4rJ*j^E^uS]|d*)C=x)qGsi8uv-=lKVqE`1.qL"Z"Ec^h<141=Z&w,H#Vzm*io$/g:;fI8eFkiD$()njvCf:sI=7:3xzHU
Bj4uHd,^Bx]X}Zs%7-b0wA,Zq%.c!vz5^I>CL<
f~H6<C2R!q$:qva2;W
0e&MmiMad)8Q.,#o.jM"kdZB0%(+IAwCP),Vk_z[vz(GDWRYlyOnC!L%d631f$B)S;s.v8P
fLT$t]S:?QkKc<dM)fgqIAz7h3gwzW3#=lAYpqi[OE=nhTDkTXO>/EE`{s~t;/Vq_=&j@0`yxi
T*+m4ntV:r)e/O;!mZZ#g
*46*O$G4!_@k]CIidr_9(7
EYDhGM0+YUss~sP#oL>=x$F=^Zzb,V]Uy%;/Qlv#ymAE<0*<bhN/o":%@RNIx,<?.w<?*^M=~90w[`t*@
GeV_Z$a$@5CasS#.bD<k87)?w7/DK67gCYY"P)W$Dha<fB6DFSs!PFW[H?tA2BV;P)]Xyxf:ofaKxPU<#`[hi,^i=k;cDr@.O+!1_T(:$gc[[2}x65%8p:pKg_Ij7!djNbD9aH{e>y#2w?S[pw-a@sf/FV;B="gt0RC)zJ]uX49(MZGO!X6lYy@+39@Xyt6rVUB4jUt<sE4Jt2$qvWwb5O|/H.c@qrZs;yEmz?y$nR.+=lfO9o}:D3s!bOI*_*K*:G5mRlj$%a+KTI
SCa%_O1Vn|ohTpF>aA7q!"6Pa)nUWt;uLKmOB.oti8x[(nmp?oL9rii@cb@*s1-&KxS=@v^Nat3ots_R,3M]G#f*-bVMQ`[M7/#h&GHYWl;%)1)F0Rr(vvW7s8P_R+pbFY^NtE9IKQZ>T`b1<r.du@Fu.]l2huWFX2VH+H`_]=^_7K
Fg(Ol(g(m?yx!^:1*<oU)CWZQTQ]oWS4Zix%6kB6qQ`otr7R%6[T4q$@L#x8guR&}u<>vY}vTBp?oMD8+hI?(3!;&)%[=<B0X)%9Hh4!jo
VP3G
Zi?8E<<p|F{
+NRJ_*IA!FE,Ug*6|QVRqUrZHnXvs4,VIgtEN8@c2@`Bl9%@2xZ(PQCTiQ;H_a>IeuCcR2#*Xtj_YxgIMN[!5Et;"8)Ezo:j6>Wimr6r3thtwN+D$;>=5/NVN7/#oywSU';case"fr":return'+Zu;C6kp=(o5*lP(OY(a_(e>(wClRD|$uC@*eV
&QG#f1-bY[WW,[T-wca7a}ZLp>g@7z/(.cri?XP/TN(QWPay/]w/
mJGByg-A{^8!z#+11NBNadBy?5$l9+sl8_Djq"1$lN|p?3,?UH9>dB.,cJEb?S2;Cgc;xTAB"eWU9b,%~k$&m&a?[A5p,n*xW9vYz4Ds=m~5$w(U3w>/`pZ6JL`t|u/q6z)LwM|olG"oeo+=6M|ocz)1SMk,0+D8EyeZG?x]JfT+DjY5*]<FhAEcyFuJgSl7r]OmHbZyU"PKXlG+>j*s5e#N)-uLWRo
rs1-K.pxOn2E,Mad8ji:=9y;P*=.N=*pXV.H,%s@1*bnD@ov!HkL~<eiG7apdGv"RTP)w$ou5y2-"1m&mrI0kv.LNQ)5a,yLJ
5"L=:gdSAwEob)0pm.:$KC}2:9rgUBOPG[L#41~alh;6Mt~tx`[36Sud#@oO?nseQiI9V<lWao=,(lHcLO|(0wkT?uqlWEct[Y0iBCAN~rz`Bc:VaWA,k!,eQ_bN]c
$*ofO86X).+nW;d*[:ezF~fcT;B]O)y~>R[_>=%V,;T:FoC2Z.aN35xp</t>I#QNGvDcE:XEPmJrWw/G/
"yLjYXn@qy`G6M5iBY]PYe:4pIl^^;:T@swzW./r)u#O?wgC*y7NWw<Ve)r^SSa=OmIPtL6WMN9#T6f5$}ZIdz/l^!iiwx+lD*6vMp0rT1u5`w*k7E4MA/KILOOlwQdks;igW&X:9PQZ?k5@#HG_/{lDXSx&6|3cR#",bgs
@<hgg6yeE_D(1>rki6Gwyi;>N9t>w"xS@vD^XevQJiAO"`^wb4<f-EM{yOkRT^l(NDCL/NfQw|%Xvm#9OC:
^M4FT>V#f**|z#KVq<M4C[<yp|BzqJU%[yEn+(g=$Y,f^ehBjY6$Elnq<(n|Y2N9vuUKP>#,?}8,nm]PGPp8.[9GWsZy6P_X^.>h<hS?=^I)__yGt3&+=
Ke
q;fNF=^[1qFk]t`FTQl`B;r7wr|COO@[wb5lc%G;Z.[Y70|3E4k8sP+i63?w+o+G5wm[Ak]N0^AV@W>y2ezm%dR&We],KFIu.Y%a"d.yOP3N[l@(yG[,2YB/!:335,},F$8mGUzN+U3T=C9<UV>fbyh`JH"lGbA9m0Yl=vcq9#NsdA"aqW#NU!_^>$=%0N,k&H3Lf8hSd7PVef{1dR(WXGov?ahO}p<K&8^#VC<>{0j1&,)U
KP9%.Uc-n_AQCfyB[1ge.T7stDd`lzq%xbIUw95+l8_n=W8-b|u2w]!wy0U9qwH/d+h1+8j0!z-F0,u#sr_Gjx8u-X4]Z"5dg$?9thb?F0pi<Z0MX.$?SSjWCu"ria_n/wQ#:~&x<LXQkk2yLub8N[H#-#q5xR?uBV1<@r]o
fZtMJ`}<*pTOsn1Dy<_q[9/:ehrY*fJ0$:`")xge{^ZnrUn;aG;yE"edSj7@!w
?U[*gF
wU}1!1](2`RXp8QAI0gk_9|!Q[h^A!PK8?LJK/";3t"jvmM]pO}Wn%]?1ZM[W%bNZyWFyHxbrp:DQke(0.+hWi<4:Yk=v%@]Jo0X=ysnIK$LgB?#%s9<5C,:J0MSh,zT$mE2}K*!11`MHD#[3]itcPT>K=6g/GVCP0TmyVT5>c"F8DaH+8B;>
&s/9B5&#d:WP0>I1%
"t/jLBt6[LlFQp-8_p8t-R(eBm7^g+0%|C6?$Z-^KYs[T2@fgxDV,D$rK9,dhx]VU5yQ_C:dj3r"Z9:m$?G/sKIHU,O"hrSc(9FO(dkgw+%W#K9U)$_24iX1i7}7[]Jao+caW)*Y2mi/
._!._67,0:>_)"Nw]#4<k.8R3j9VoNV
KM?d(HIp<PO=
=t+.
If!@oV>m9>C8M8mcDp`n?NX3:8N(C
4qD1NWbwqeaO8hs!ETh*G7m2y3u!M}Iz&`tBc:Nu,VnIXu+H%]L$St#{^^bfR{cZt;D/)8<0(Jt>U8>4i=FYn]HY&+EA-pVddUGsU[izA(A@?`#Ht("[Cg,|#Nw
0Yu#<cMi$R:w#+S=3H.28]5tp#3;MC".!ZjT2#f#yh;QP@O`eKCn$HLd[!4/S]BDLh02WR*iht,Q*04gAnN[;JEQ,,>Jb3=HZ(K0!_ge>D@;:h31^l!h=0ukhy]
`=`O/?-)G>K_>E;mO}%u5<!q<(9j1r]S]4tW2Es4d}12v;!U`7"MHYZeiE@g(VwsArmyXm[xqG_smDZ"h
U_4l-l5_ln,Cv7rtf*`CVkaL
.MysrgBD!ucI$TXo=yg/xrT*@KFs!)cw>SkU]s2xx(2D~&WK&1k@UR
3RIQAdUXS+IJ)Bl|-u+OU!*G+!i@)zkv)+POXXOTLf_Pv],LJV[~W~)mJ{LrF)F)M-XB`XeY[S>[NIG~#K@.Ywyie"4"f7ot?_<C8vK/>8@eImm2m*)48Z=jA2#b>VIz%!b>iM>KB)6^9j(0o.brHAuqAELbsH%Nq_sC1qLh
]E2e
Wt)R*8L?mc^?X@bNl(YqMJhXJhV~x*Qxlb*s,
!_B;?h5s-3]|3`j}7@FhSiCu+N@
bLF*G(Gedhl2>I((:],nG6l99^4@r9_{)>2X]OA[Z8+=wVgBn,^,/>MMXAKQXZkgOJiIa_T)Ns0KOaj3></Qq!)oN]7w.D6pf`aR[:6ms#1$wk*p4BCPQ[i<jVsFd+;A6:Z!YKuK,pS08c)v#lY.ia?lOrwu(oF`*i:/[$Tk5N)0k(ti]4uG@?tjbg3q?2W-mC>>"s-R0:5hPyh]w*eDJchgA2s=T2l"8!4UML(G%~fjgBqmZqI0B>5CnVyGZRr8<iBIP5M%G~fecv
Tf5oH4+1xaU>R%AHfishO9x6V(Y"9V(C&<F_pg:F3icjcr4?O:og$2<oMO{u&bIo6jO#rlXTa*QDlikjUsKn7GQRu$$dUv/<wi-P.h]TXQaAj
Te$EMiq)iD<_nk,dPeg`]3z9vp;-Ty)W/Unr)2>Oz_FKe*|$To$KM0!my3k4,Tr_9].Y#>LKIee^pfmeY*
Q"LxR}7Ps3$@j>/^/t
Jh_=y=UrK$T`=>8.^V0@EeU:E?Z-hxo6|W;
Cb!.<vTF#
`rdwg;G.8dG
c7`>n
c>:ru)c^4i^g~o=U7N;2lEFFdP2A{W6wI3K,xQ]<n=<$cU5W[ywHT';case"gl":return'"Zu@ibPD)/$4oif"H8%O*h|Uf2d;><nbbD+3-:vGP@]0AHTDC8%jP#px|*3%0V.9Ho
`{uDcIW~>`XwNl&~<@R8hpkit!>rt:UVtl:Vz!^EM4G+t0#E){.N+}%HNFBn@nZgn$7na!%0hboGwG6n2Dh&lzd{-ixmP6AcVW]nf)v;-bC;-ij$x#c}94,&vS/OoyTw*IL^$mG[<#+$Bhw2D!*!xy?R=NDo$&hRtD[IQ-v`JYyV,Wnwr7bQGK.{BiZAo"!)j&g&7F5u0jkF4d4Go$8ow;F!R8x*Z9y"kr5WK17NKCf3b5nIH<q>p8E}1k$i0In{^eRoN66MAmP)kQj[/Csr"5apv$l}Wv$@yghag`MR,oE4iwxM:|rUgbX.w}485E2tn><ePRd,0f@3T351Qp</rh868diS8GdrP{l.jELU`O[~V^,OYHiK:HJ
2~3e0h#dutsnIF$=e]o+Jd
Q7Pt/KZdr(Nygdl7wyVJ}DCl_6XGT+6Qx!Yn#^r`"os(2m%`M+2MtW&(^`!(%=)7=$+(ehjej@x0"`o1xi~i+Sas`oIMB_|+Ea3B]@QN<Fa!*i]Ke`F.if"I:Ny@R:?Z#i-[#F.B!,l5cQ?S9htUT,R
6Kxk.=TT,DV$_#SOy
~+stkU0nRvsfI
C]Sw>8{${2JO!>dY$-.4KD|69Hm9}YxV)=7J@WkH85>g>4hXyJkK*,,(pSVE[gP<%?_*tdX_tU9!Zn>rBG,8ApI!JYmiHcR"8P0Suq9c&A|6TgH3DgT^39GhA5O,+^&qJS+I,kp["M"hRTPX]YnyjtZ2SRtC&W^hDZf3=3?&P/D$#>1J{K*,9S}UFL,*)k,`d_D$QhCK9n(!rL|]kMIW
w1E)h2>0N&F*;,=oUy6JM(!#J8Uc!Oq~BIy@=i
uk$YJ]qGnDvmDdi"~Zt%%+!uJ;|^t_oh.R%%CT-a{
VB&UK7k;_s
H[939NmC6LO=5CUt6#]fM_M#Ea0q%BnE+0RsH5s)xLam>$l2?Gu8srirU.?%!~J5"a@R+z(sAWE*Pfa6@}7tq>1e_p!a(z"qO.#(td[{XD!@T)iTwEImyc;%TJn7f#ax"cU
Vt>=I?;~_-vw^h8;BG??:vM.:it"#Vbj7+o^KyA]MqJXy8QSW)HsQb5{UG,3o%/~;dU^q+Oae759Mzd3_~XC.%"!%]s!ffx#cM/kU~S`>S9A/Fr
EWJ{,7(<N%>tub
1o|R?,tdL_=P{u1h3uB1+SO"1fTfV;sOGXL=#1UM~d<"BY/&n$`a,d{t&R6.-n`yo%~UHL>-|SW+FO8H}V(2(++[=g-M.7s)k4:VG0~iWI_$M`n.J836]R[jBd%>hoAS=.$tVj~4<^3iG<i4H%<yJ$F]cjKJ%EaUcl|+06<I:13P!9)-Xp=*1WAqw(n1eY9,)#
sWT(k}/(AK53;C51D>9CSHJ2`LYVLd44m7D)$+6NdL?w>OiaX0*9@mST51BOIQ0jd!lK"Yt7lnw_P#bE6/H^BJ_Rg`<(&UI$>sU7_"*i(QN)OLZbdUDd_|_G(Js;GX&>?dm87yw|pbX7s6#u"+J^.^`wO<<L!~o&]ao)t)n3YCrqoNJfR)-w+)afR@C>Wy<`LDuK=>G)pU*Lc-nZ[!(IlRH?]+,f
g%pV58c5l<#Cqqpp2CTI%+@0f0wv3Hw!9;IdZp_"aZz+?Mg>J+*;%t{8gcE%_ATLv"VK^&l^cC=`Nv.oV;xu2$Rt.E]rnxs,_w-qeFmk(k5_;tKR,yD_C76YE9.=e-|;qO|s*iRZR+vTnOyOYs?__<"@h<O>![*"W
+-L7*icI`!4uVSY9Wbxiig6d3Es2xa[bETTO
Le-4&;9Go;>}2pZhl_
Rc)#EI!i3stT1F$XHDd)>yRpX^lo~S_9mo9V#JUwnv#Dun]0W?aAefAI3@.d-PFWE]$17^*yk5VL0nFv

AA5ytw
.K%fhkcccw3EdZw4c&])Q-(,@TvF9Bh6%A(:F|f2NZIGk^nY[6Zn,|ZZ+#8w+dRlPu,6Df)SY(e$t0ugiLb2mKG"`."3>[G/CCx)D{TROg9WSX]mILQA""("FkbOA1:2W($RWQ[}.1[13O/j<1O.RZ:K&#Dsr|e|vClw3cggb`p/xGyXBz-l]r&b%7<F*n[oI9LpGE1A@A,5Nu,L/%@wIlF#x|Tymca+H6&VGsu$F3lh=bevq;U@U.%)Uc>_)f,Q4%KV-?n<BDmTpA"]9lp7?*(yC$)sF}G#T6g?nYFJ9rqgxHYY-;Li,<dolt^vD7VEaykx592h#orMRef@W!wTt2#|>6No]{IR:%E[DSQu+YcH_TawKpao<z?W9j3-`&80V(i
_ZSC0,9ZU[<TkD_(Q>&qbVw
aQeSZ4LBk92We6,q<
]ORd-rlHMZjD0U
;;0_-Jf4pO*m9WCa"pOJh^!q*5m;QU:j7s1-V$~vy-ES.D8^uc7U,hl_;c4h
+=ah$gDH:-/y;k$onBx.fyw,.7OQwyq]qL&Jyhr"f~=]jrQ%Z7O{f55HGRdr5+Y2LHJ~3SXQ*nZ8<t-&[Aa,nkNt3yaPf[R_Z$_HOo^,Or&&Vh;cNkQh!ZGkp^]d@$o[xn8a<NS5ROR^TbKd$%VXK:i?mqhGw|$KAZQ}JueqG<RrC2.]ghET11"pCe1+Q!%|#pFcsKr/I%4.a=@V@my"`fI2L3k6DzkiQ<v8oIk5:bH`^?U-XG+Xhg2iNDL}.bg(EXQ^w_1#@.<bwM!J:55,!K+
vguEgaWpnXHsg6F3)pxF2cV9$qZq2aS~7,d0J1_&l7WtnJJON)p2[gK&Jg,o[.1}iMwso+jlp1TMCf`$<__A"Pb:6K-x;Kovg+C//F0<y7kEPdD(0UhW"(E-*@bQRh]9`,5%6l^ZeDiS??4z-al)%(26Njcr^vk;hMRH<H!z+?.|wDbB^P3!<[kvy54cmgyqu?c,RwtZ&{GY_Imx!<[wxsPU2w7;,S=9!GrCUtf|n<DEkpS]<C.H<R
(#}q)*kerb9
ik#B5o1w9[nO@
~YdL9ZLv0&D-IjL&>O!>]gP+#OG]t!yZpC
`
F,`GS&n&1}MN.e!)<juGF_wB';case"hr":return'-]^@qbTD)(q)jie"0SU,,$13+q+BZ&r>MO;`1&e,T4%`e-
r2laV>BzD`K7%:I;vBw:B_?Sly_@AWg6vJPKa
QIrgJX].<])ul[J7M0:(LG4cGtZG3Nu8D0@>Gd,=Vck{UkwEkOuTGcreK+mrpo/h3LMP*%sn6_[D=:uhWN0VtG$Y@Mb4-ix9-Dc5fn?@
{*@+ffMZz$sLU$81`bK>`G]l=i!oY:nnG&&<
H3)BnB`pG451D+AOD_v$RCSyn
p>DbK~Mmd2x).KPQtdg_t0/hvd,C#pH+@dIWI!cD][x!8?hrGM00lADnv:87lt/+pqE)c+_(9{rK
y:H.D<h*Yd;"zK?
L?%nL55X+Pm2mrO/D(`s;lO)k$lP_X-h7)$t_u_e>x.$i/yLL%JSHsg3[v//ed//rJ$rS(@HU/``_uN&_i(kwq2"Bm0"df$inD--k)5n@A,C4e:^HZ;GX+V87AZh],(uZ5u](%[[/0Qf9lQehe%r^8wMY2m0Zyf3;.R;G0}cGN?t;T+c?yS#"97>zNv4|c>Nl[;2?c(&hu6s[T#gb.
k_!}T
xQmu
/t6mc9i<y`45Z#"[75@Usrs?"v8cHtddad`=2rF"Xg?v@B[;J4xE1fLf
kc^PnNhJK:5>>[YclYjP`QcJd:EvEsN"EFKK5x@Y"g[vm"*iQ,?SoQ
Hm0TSm90b@bGm:1,^tJQAw}i^e],R^qIP,FN70
YaawJ.&lM?DX(;:%Y<yZBb!&o=M|_@#N[<ilmvm$
]6]#A/ul>!;D,r.t(q]!R#BUMZeeCex7C^`s4&R0to=1YjK*(D9`OQtr=Gth5/EB[
]xWX<E|#&dZ/.PfFS=qh2GSo7`F^F*O/:njA1^]>m&T*PQY7vYQNhpv=Nl,5Ld1w9ol;];~dzJzsPrXWh+KQwT-%IcD`=6enT74417TdD1Mnv0[o_,brfMRoVB
&oYa^Kd#
?V,$<IN,Lgc$hPGq,EUMF`E(i/$,imdLkI^#;UX>
Jd8+,XKjsBUYx^Km@d_,NqO,K+p,NZTU
m2ZAEEGl.cgmdbs7FY)_u=*bD#Tw|[NtZN6C2<[-IMc;7/-o+w@er>G1mkf:Za^g_/;9Vd<33+R)s:QGE6U#N0r:)qSdM4]ttlj<{X95X>/eOIQ@?t:m{6vCTNkx8-J$A#_PC"}-%PlYx"W%3MLav_eJJ+jM@w|xC_1
Ol05|^64|f4`[sL]~)MBW
zcxq$lI931"8^OwjV85mo@Iuuigo)bV2NB"w*5qWz0{$%v}F(NnGF]lq`"RW?86]Gf:%7b)r|7}v]xZM~)6Ix5PFx<J-)tj7Y7tWLywvmfv
pJk.JV:1ZO(lRe+Ln@z"(I{ujCjYc6a*=2>5WgY]3s;M1]3]!hPO]mTmV$:pL(Yw"wxS36gnhx]ls[2lvo!^Kfx*KK@Zygniw5^Z.G?R][egFr>!?,
ZF">ScJ3qsFNROw.+&y9*{%Uea-!EXXO6p!MQ_He@63`HPHzBx>R6Y)<$^%+?>4,Z;5;$I#{olRHnVM|8zg:s;8<3V+ahm)2r?[OLoi]yTx&XQ>?7IVc+jeeP$;P1v0{*;ny(PKHAvd>^FdaNO:VRB8;]eWQlHMD&J9(a/d56vp;JUM$F|-nE|/)pP@(Z6tA
&A4e:^^VFEG;kL`S>qm;?@H7/_Q2OYFv:H/U1hMw|kBI.Sk3[jvQ)Xfnn6v/f$?jI]VE)fyPQXttAYC"ty`8%Bg5O"oJBD%^bSNP}ty`"<>`W.|&kqBXK/v0f!hNX*{Ao#;ooEEod(pZGehR57myYS=A|#9.7l1,GRfa{rfHz74`qg)<4Nk.#8lC5I";og$Z:mk@wUk-hZ5vF3{=e`yqg*.K[V2MA_$,3Hg/<KHos2pxkF?Dg0&q{:;;[GHD(12jR
PvM&]8cRB*6g[dP_pioa#DPj3PWb=)Vdzp47]h$-(NeXxR/wGhH*w=%ZZWe5p(qv.;vOdl[&a90>tDmMl+djsFaOrjvu$;q
4c^Bf;
@?4>(xPH:V2=!?HyTjWM&"P}U(5U.VK5p@vn@.E_z!<T_V1"[6H}Gd7Q5z-!>0%vN7
7y+!)Yf-NpISD_3v
yU4=4a?C(m>wVSH&DCP@IB!Ztrsy%9qZ<R<rH.i8Z]^_YyW!t@(d8+N@v20=g@3oixREg[m7jwE$QSF)sF)lgvas+R+wAk6"vFPBF94}N|]m)GUlLvg"Xk?}bGN$p>r4vNGw58OPO&s.Rs@~c+G{]TaW_%)<OEV7o~)0RX:
IRhF1^,T"E>2QMt+&cDEA>Uig9H*oKA$%fRKDG)RsmVYtefV-e4I#XnHH.Q:3>J)`uG#36,nGjFpf2_Qk(PnPlTECFU7Dyd0[0"!-C+%q;syI0%*7lmkFzp(IrI/<I>D?TUTbDZ%Q_S{[XAC[L$@Tw6qO+)B0!Slk7w(F$N*#soq%WXV$TyvuC8u9QaI/*o.;:$hA(]fcQ;3;^Z$On##&vee+E0d(=+EAL&qIA5-p4pDZ:%Y,Q)9c,jo>oq)QAGnTlW&;0,UIRKYO(K5.x`[p;V7iKP&Gg%ByumAX59Be03DXe6Ni.$&6^LjE1pPig<A9|<|G}a;fZ5h7$4oUxKt1|vs^SZCt^rTuO]U*Dg*<=n~Ny++comv;O#g9Gkc[?g?cl3q4=Dn3K#}VOydaBas,3I0V[$C4De(Z5@&dci[`<5TWc/=np7O+3
H.D%a%@"o_9o_*--]bU=CKc8MyYO1v2Z-r#J4Cnr$pN?Rl@O#(DctXQl
LY:CehEt`0C:MZ4H03Z7wUa{QGmsvi(Qx4pQwxtK(/W<j@7XV4)ESd&)9aE;p@J{EPSweFM|8wS]$n6)b:F!Djn4>bqEmRO:G*j>m[?r33d8:>"tqx?clhBPYi3A6DsF998EuD>;WcSFjaK!O/ET0,@&W#0BIT6JIb9s7WFBTHLLO}G}f6_Q-],j+x8L9Hl9
]s6N{!Sw4U_=8VqB6$-i-6L`c[GQZ27CvAm"##+i3RA,vK+.IT|b&+DS2eTm5/s88ea5SN=iSV`s0c~9b6&-7b@uI`&<^xpE,/ylM&Rg?Usq7akAs<f^Z*!wi!)T9p9ms7Nf}ktV}PnOG=jH=c2w%see-xM5kSq<y>B[t+{D
1n/0g{Xpaad3vM"Axl_1&0hv@ktp%+]
S_$$SX*#HKxg8$';case"it":return'"]f@ibP.!#P^<]+dH!!:.*^;zS^#M"Z2^<"!h_&i"Dp"TFEhLf-QDm[V4;c7stB^SozTHCH+FGc;3yt:1]mG}cFSdV=16cMG``Y
L86(}hTH-Q]8>n*#fN2XalCl;j_ZY0t=G)}fBJ|:>(Pdpr>-1RH0>5"N?S!YRX91f%2FWw{?
iVxIdG=Jkv=D&&diiVCsKcbNs?HCP+EN5HcRZ{
;,*$
HJZ^Q|GQ+^!f@yp)?!*kf;Q%R@S$U~p6)GjPs`=Q7)L;r8(AEcdAo]6m+3m=5ZCJKlbz8Ltu#qCb&vks9#@g0FQc7J9(sL>kF,B?b*9xmSJgybXh,YZn-`p)45T+#|EHYqx]^*2?C3;kdZ#>)*.IK8Vd[^-D&=q{+a

FsE6^*`_`H-]9B_^S$2*cNSV:9E^w?
|Y.6z>3avcQ9bs<mVC%d)$pD]*>`yd[
(+&W@$#Hi^#_OFbxNFB>KqRiA!?8{,qVkye(-AkWOO
GcRu,M8_jYRa7XbW:z[<Xr7<0}sYRl;R+Te~8:g8JnlGB74xWH1FGHn*o(Sb14vZFO))x7HUr5K4<aLd(_%-pRD32EHu-A=cO1!UwH%f,+u=BI+0,&;y8m5[sAKfG<K}vGTDDL1R9;$sA}FinKbJqALFnD(ug=6ktEg)vtPXpZv)H1$mS$e~vOgOLyCI,rZ(pVQ
q49[lr0-NaedPr)o5f+NX-:fVeWu<%_M;4cxMAASH]Z(WqI-+li)Bu(A/IaRyAb]g]Ln(*oX=~^v!qssPQ%3o7xWo}d?@.4zt8^yK[LI/{uuC~:Ix((rD;5Sxo;+!fBe3AhJu+]pWWFT`NOq4G
yLfC_4:6:<fFZwH$Rh.uVC&GK+Ls}0oX`,cTT$H%qS%y%30eOSc++-J+|bx(-+];7-[M

0DqqEe$ig-bh`P/pfwraN,[1ZT#ABj_".sDcYm`PgaVyq;R+_jPuMtQpe_!OHy&5o(sqi"UI9jQmT!}fm#Z"hsc(H-`IKYzfc,p$A0PM/tz`SNQ152oelM7k/g}P8t2yDtFSXbH!8Ssp><B(5?MR_yph/xF#^b]_C?gai(*`D(-s?Rf>jk*5}k>np.RMe1ZE6Q&;?a`yR:d^ftNWN3fW<;eoX/^PYV951)Z9`UaF:x$gO4T6r#VtG;!*]`3.1L>D7D&E5xFuPuq"rZY7Lq/J$F%RB3=-t_;r-!7>86,PzfpbQD-Q0QrJS$C?V7@SNiCSueAVHNX4>]opz1(J)+)XX@Txm/_o~oNU3[gCl%;rE*+0]%,ah7*`D8HY>OAK
HqqtyUaDASBp^mW,&1O*GA@HMS
k^A#2]PpLs%+Mut4D3D*{8sGD5xafZQsZAEi5Kz-IsbR*iy+mQ-OP?)MGlf3zpyXL*+hZ
<@u1o")r7;c&J251oZ|Vsp*]<uy1$$BE$)WH$
<
j[ASN*^FGVb96mKs8y
SR4bkQBvRW-_:S0^CLe4J&XdwQcHI{THGecd-*Sz!=I*F)Sp8{NSh+g$dGr2rH=U7$($Rsq5E4W9
1/O#s^<!d"=cW8cJ+OGM8g]_PU@8gFXdi]<2Lp)a4QD=%UL,KPb#t0OU>*UXI)3^%edp)Zi;k%!_]n<L+F%Uju`)28?hjf=J=c|t65QQLK&k$hb&RCaI$.gH9[$G4<DYme}X+yO
ZO6S,
]W9nT=-_sNnTUkbG@>D$k4B5r!E@T9l!k!hE3cf8$:;R)_$%=v$OOF=cT<*SOVl%7WBTSStaoqG6EdG@<;l%Gq
OcqMN]j}w#H8EoW.NtD:-CDf>
IQ-Ws>G@//5%tT91JbrR[r)2u0iiB0U@Z"
7/Y*Zy2bCMw;4MdpFRU"|qxisY[HqJTaI7AIzT%uTRG0q1]3shQM-1;^,&J5j*yXSyV=XRMLhWg
,B[c69HscRsFY>%>Qr)[kyx-XBOH">aa^F]
[y<Ity)VCrf$I0l`:Wc"wRq80r_Cv^OPRb.=K`}-DC{r}W+1CL}eYmVn|LA[3m6`G:D^UFeNdtJ,?Ue^<`1JoGbA5jWK$g>inE,h0]_))
@*A=+uD$!Q!d
8j3F1sYD%([Xk!<{VV$5_IkhX+.>P&!VDzy;ao:5,]Yz"_ro-)qQ+>jKSsHTE@Ht*K-?4U<c]vBM:+(45eME5Lb];JXRR
BqM6>B4Uf-m7K!q^>q_tmX-_Z;-+<?uhr;J<Cv4;`>Jw1J#W#l3a]z!=8y:~PRg?d7W5aU>M*<]zL{uGiU4h#:8e`Nf!x8:*]8WtYeIRY5;/[M8wrcWGNsAz?AUt#0:{`6R%syA<9}2xv/a&2wBp7AWptVx7uYkYD/akfwFvNjj#.8<w"/e{+]x8Oo`iJMC2I-Xk1sv8MlE}RY28%j)3u%[hD1Ar&cc#<o.yH>n`A,JEvA`r>_=omtt~:zkd4"UxF;UBs{/CWw/N3rWSW26!tjKA2+$fIwfT5uOZM(B3R6t%Hk7p#CS-n*D2/>r]-B8iH};8kXhrhe<36,`aQFIh,In*Xv?z_eks3M9QCbgq^CxsUWkCNXc&H./+bk#kKjV0xig~3Ca17G*F:7L(1_4-JQ#<Y$`r4]G.&H:nrEp}*O19*Q;PEA
Tn:-v-^130i.]tMHUw?XhtYy@B3U1?|xfw|)7o#B4h1MwQ*0bh^-9/`X.)}4D9#2fUYS]6Q[l,qf{"ew./7_tJ|3^5E==wZZgJZ)$==t#9nI<Gzxhe(?+!ta6-zAL2womp|)xZ8h>hb*y';case"lv":return',s`@)g~Z+;h+HtZCKT
8Oy!`/0ph%@Wi+22_ZDI(7Oy@>k@4+,j!L"Fp^/34+ri1>pUG|6LOBFW@O$M7JZL=9eyJgscItl2tC+:mlleNv^6]^w/+}u!)$9q<kA;1w0I8BR]l%Jo]?_Pa54)Yq_h)>sl23w>nkX70
p|Az@T(sGJK2hq
.]"B/lC,vRmB2TV?P
-1`[Po/u$-D)Ew2h2U|funXY%u8k<9-D)c4te:&ht6w^~bac$MXS;vab-UhsjDU1uspBsaNLQe&R36{s^K)p*APa=HZTdKj^Kj,jUH&z"
jdys#n.F:?Y]1P`@*Qivt&ZYW"gPH&^yc#BtkEgsm,5xu@Kt2S?=0#0*eA^pN1e9L4yM[[D!|tGH[!paok:>MUwI*kFE{[S-
2qlnG_)C@DH{1V<tNFR;J~H1*|ChI/AA4Q9x[w4Da]go4,Gx*t1K4S^nxg?WY{
g-
XZ.4s3[F5Ltuvp[VcJB{3:&Is~u|$@RFcu2?v2+XYDTzx4TRq+";B[4P`uqD)`l8c/lV7{
*`V]vM>^@^O8wcpQDuIh&j}=5r&H66rR;&UvthXo5KSuP7yt^w;b{cK4*^dht+E7jMIq|P[]mf%><"0(z
z,MrIt;KQi9cru?0d8Q4~HBm:_|@-01YXgslgYOKwM()O:_#%%{D9D$I-8%O0Ln.B^CW==vc
`6me8[1-0}x
74WVJ(X_0Yc5maA*`e>m6MeqAYnQ(nyH;*e(txAK7Ofg-7e>_wk4:1"6P{GVm37)8*HKIf5o^Dl)A^=;Y9^Av_$Kl^2])2Be4qWY%QabDM;I-k?C8v`pab9.#t5W[lTh`f/2+XT.WyBqk>NkRr.q_GW`H1GPZgvq<o9M%iw6mS-97y!;0b_NV7TNw~_8ox56]q-2w)3"@giz8lw_4ttF+@0PZB
|n2K<!gbgx_21MwX*y@LzIb=NyF157LOVI*+)Q`#I444U*],!1QYaoZuA3u?|p:$IHg2c^LU2OO/U]bn.KGR~r7w<ic$g_e^myt3bY8+Zktd#d&7v1}c
"]!(VPbNb|9GLE
4ONftg2ZL!M">60-`:%1R:slCJwjSqdL6eKK#7=hcaJCEb^^:;#gE,:#@(lnk2@CF0~4q)`5iYJDyg{a}c,,_t&hyOA::I7O<N|#zBw!Mip5;v5t|XD:L%kO2FkHb*{7SWlwl<@DA-+*R2l**kHC>j!rg%>azNj15Vax4
*Z{UPwM[z,#67J"U)WZd(obcSD,5!j6F"EE_z&}QOY_$P"/aE!u"<%hr1
;Dz("S!g]QpdpT{A-.N#Ls}0C[?e[:q<-ZrQRL&a{%6Txj^0x,f"W8Y6~#s.3o)`L%f+t:80yxwu1Hg!HsDaYpk)/h`D"R|CUCywQx>kxSiici%)6_[1o1F>nYEYZ+t?bV6n^p,pYUHa^!Jsa-]n<n&=$8!$.0W`#!l&W4qGjkg/kq}u(.njC#DZ3l9L@Dyb#*AQ!GqQbb*?QKV`Ns(w/H
BL)_k@Ph-Wa%Euw6kZe{vGDE:<&lj:(F#-O|T?P8d9)+G2<sDL[U1eJK2=(Xolv571t?R]sstl,q1KU~]NF^6^B,R$a!s3]=wPp~!s/g;d/-i7REF"5Co0^{uq8DUT%KVnh{e%s;wA
V7j#
XA1;X4&IQ{DEt0_UtE?znJO;tars%>QXJXJ*
MY^Xej=8|7]4Wf*9|B5I?sNy&@[!U)"&@(7&L(
S{xKf8k=+6Fp<{9[PAg~a^^G$6p:mLOuWwY6?+Lw<]qkr5-+aef0n!?|;UFoeu>>5-UvGgD~2RT%Q$r.Yy2dTR.I#y75`Nj[,mX6:y+6P@(b/#`iVbl30}y1$I2@W(Udik8J2oPNO~v85|:k:
46tZ6Hm"R5Q"f%&N#x+ok8%"/_.>Fe.^ZiV)Ia4
ucA3h)CHJE>NSv;pCT[HQKd&h2Zi1+<n_Dm]mp,[%iZ`;4pB*Q0q`GQM<&&cuFvbQu/RSTr"`w]V@fp"cY-x[*U~aEK?ZqpR;O/I@@sgW^W&0DT`W@H}3MFL@T_q*<[.@WuIOlT89j#{aSJ{Q6lfY=soM(r00^XC$lp)9sDHJ$W-A(S_<DVgTErFr;9jjtUg:LVWb-n6e/V~OL;9:2TE>F4nP]p*#dB~RKh9U@)|Gb7igea^a`+FuyiEK+-o<9[0gN6_CtUJ7TxV!bKOEFsj7B3Itl?"kh
W27%G3Kr0KoOp[_Db9e;%7rev(r/7yvQ?,mgI`;:d+NKy"]wi;s=-D&5Z[BG.1
fvmiv&Y1-|5B/yBr+g4sTEC4,XO6
#ukZk^xPk`TOTEN3OHTdlKc_5j4E32]Z4=b@?1ZX$^|wuM4[K8c[v!UL6p]/b4k/,vX,.siMvEl;VGy4KYmHF21.>N)1
Lb5%HA%7V_sG7=gI*wGR[F-g<BW?S%t>+d4e?doY.npS,bfkAMD0mWTY>DTcaQEcP}9V@k[(u<3}mBJ?]F!xh*fj=%%xm9<Xg@ER8hL"@SV2#?4FuP%`6GR:;I2LTE8C)yf-FU$?D(G*#^q0r>_NcC[d,G<}(
5qha=o#MOd3I:d-=t|YP0WhLa[i)_<F2v6k]SNM)206p#g?>Pj%I!e730g^N?DM_=k.]*^b[0^YF;I6lt$G!
T(|85H^9~W,+?Z%"Vp|f4%RS5-qWnR6RP77mz<ku^X-J5q~II(8qKTm/sKbr,%#jA./((A05;ckd`X6!l
+L#*eo*tme5LyYnnQX*6;@`#+Ihc#Qh.]SLKWifvG,d)up4cT9gX@]!Rtb`l5JvwEG2<]$a)CGmx(o)';case"lt":return'$s`50bP.!%hQxfnN>:z${c5(,,CSY"DmTcgr
,pwio7A}HY;Wp2BW#s
Qce1*UGQ>T<wen_o#;XECi0N8Z
*:GHLVJfsn[HL`UW/"4Z[`LHixyrfE(/h5qqA6G*b?L`H(SEgkd,PsD[Qm(/>+c^FEBmho-oKWQ`
)).:*CK>a)[U3/d,?mV]nh$ic_-?p/v.1a?N^<uT:w-%~7},c^-7<S.Bgf*:Lc?GV=)GpxbJy"Bv}>CjO44vRePbJQ:!0D60X=/Gh?:v:ZSk3[7CVqcEZ0O-]cvg6!:BIAZ]5y)tR#nNs&XbwvT$EU+@km:%yPc`}TuSHD#ptlO(x)%@~1^HLk{L0c5O&S]w4a*,9Xukr,{._p0Kv-R"Vmf/[jY>#]SjXi7ig=45<nBRmPGP}JH^.+Pl6;kiAV/:*Y>mdM2hD(BG^+mY_VjDZpG`]SAg|<dr,t:#52[j"G:wr7dfEGM2BGH_HyxHPKQL}nm9cUN6~bNA[w^irn|U
d`,l9tJ%Q?V%j`sfV2E"Sc/uEyF#oa>iCxjL2A
3L
"dG4Wa1s=/5.;8+WhIfc!#y?Te7lBW&XDn3gu4XN`xXlm`Y8ZS,uh7"R^S+2Tfr}?N_CYvA;)ZAx2ftF-1BT`,4%wTq4L!x_ZwL4GtBV[onDNH[&91ZS,?i}bGCJcNq}[f`zV!cNtU2bda`Al
<3!y%f3~Pk^T!Non9KVOEPnl6tM]KYw=%c/2LP:IK|;EV9>;eHvN;1e3OpFVbKG}FQ1)1b_%%x@nThR-bVLwD{V,)55mSQ?1M~Sj#=)*=JHM^5xmvgp[FMSBu<WoM+L[Z`^*6v)4+WD/tel3?RIT-G[?@o6nevHx"U)w-:pL:XLwg/_.ButjWf5Q6%_-B?p/mnagBsRoiBdl(z?0/EC:kS"(3j9xcopceU"y"crh6M[y3
,tG)vVE`*Lw@U&<0-hr%m.TdmoR%vn]Vp$#x=z=q+&.D#1a3;>&TlW:6m3,Wvg-jqJq29HNbMbVwxE@C>trC!=BymwA_jlKKsE3x"R6aYpd2Jy8J-}ont""k35L.S*69_A-E=mxl84x,!%2,2jh_J.9XV{PP!4s;eqD@oHQ1WMDYX4(H8(WY8+Fx;u_U3p(;s+0[Zd/dGwj7qgmS^B,AgB%7NU24IOX-G)IOn]6gIWF~,^BxtIplpnR6iWUn^_tdi/TWs!7]K}"+#sJ>?cQe9e-=aHX$%5.gY(Hc
[,CApot^Z7Ahr9*M!%G;k(%Xdfbz%6:!VK]5jCCsv&s;4Irm/Bj.0Bs$y4<mOxlL6JL-@9D&Bl=CWby@
qq:VSQ""iEYRo6_=+z]$F4;i0HV_eG1R&N$k?{KXqI)HQ|]yy_,}_bN!Y"Y88o8@:U]><Mn&hv<Ls|63r&7(]xQ4(5;Ujr0}*Hw3FF94#n?9d
etRUo;w2.Q+yUgy11e*iXPs=ht,H$KQXLmt?b8i0ll(16{0d!jif(6,87VB$W%+pQ@jCDI^|c=()3dZ
,ZM5@::l<o8}=6D>5oLP-D3Z+Gx$x-&QlUaNHd&7e!/]0T;]M%>+VOSHJCQ.FRVQjfG7M`RX-
gBOg;n+k
F]rK$Kap,j~+JuSZ+xLiG;Oyt1s82v-?;4#v:QJ`58UTu"G6qqJB&)l%b,mghdEWAi5V+Fs&po|wNl]1Uk{Xu^b_Pw!r/jxWAW}o^ela8.c6(.R(;m}MUe%rVACcUr~MLR3pRHWSHwF3sHGnd#p[anS]-a.Cy2LF*7x02.Vqbp4Hh>>7GQf<|HLk:B3VxZZwqw(j0aytCcVdWNGcGA}(DB:i<gGNVQA[}8w-iQ5wxlpWc3pse1(BT2,gDH~.|TWj}oga02kYbjxR]Z[._KxfPjOoDrN&*idk^"?e8-,U}jEk?$~/XBE%`Q4_.WB>)%|C:epxqbDh=gja`Wny6m!Y7VY*&9Ss>V#F
D#k%(Gdi/!(!]3QU!c!s!<ws[75GmrLrsT=NwgZ=k!MdO{*1:vcyXQ>jdoWF:T9J/9JL
=Hl:k)>Uykz@pWfMVKPHxWqCT+U;Tg8bet+>W>H_9Y,.j4cN]4!*HA
yk[G.OO0n4q$1y.[Yt)7[UT(^%#(6NilN:wyVn?+)%8D]Ez!R>r!!c.yD~rH^fK`N3x18LZ*.GIrNfJU)H>mJ8OYIFv`t1Rhuk94"1n#4u`
b"d738PP_aC[4
r3!Z&<6j81R&`+J%S>$N$@il!FHB1K^G7TJerudEN7mtpeV]Z%u&4DbAS<dGdpHJW4%5A}xL:R@x,<Xe;6f@MT3qO5#[Wm*}Efj=`r7h5nZ)^ly"1a"bNBh(=;"^jnDpaoBFCrYLiNE/8w<h0/^?<yT|W">LljEvlS):q<4FbT(]ntAWqYuS^uf*';case"ro":return'$]^;C7oD)(oo("r(:N&T;lCuA0e;=#H"["llfvarE`o"re
.AobS,c$1l"e"A<TNeo<H,*0Ej/_](If2^l&19M>;zn-lk]!L7tQa.9*So!Jcf&^Nw#4Q?#y;a@Yaq2ps5v{fpL,dgN$mTf@#Kng1>M]^j_!qA0wNX`t+jV8p7nd&kyHRF+{fLf!97^6-j<b`E_!%?G.wQ4EP6y
!Vg2y]vT7]MWILc^l
]EeD2%
Vn.*ymfc>w}BL>`PXE?cVsY)siP*u[TPcK<uWbz2m$u34nusMCF<JAMxt6v8L-1Ct9**Y3==(spcLOpLvtSu/T#DN/UQ"p/(otO%Yi)k-N3BQh$U}/Sw0]yim.-W#.4s^Kg*/pvyV2Q5SWGmZ(GsdDr*`op6^SNFv"$sqZTEs7{2Lg/6}1{]Xu_wk+df5W]:
),@#(Uf&=U`:wt#`J|Rlw`c5lKenig%C$IH/#voGyTc%(e"abOb8(1gMD~`bW[Hp0WP[+mxq(T>kx1:`F[/#f{B#V<CNMLG(lJ#.$GgKhQEylW[0C@XDEY=9eO/U;Z-B5j<r77RwhJ#QdO3%BSEEfQI`+,+VbIVtG5%ne
AdGVC95dOv)4u|0uu2QWs[RMwEM|`xkiw.e|3TSowahnH)_S+Mkx?5/PUJMtN{LgWe(,P^d.
q6KxSK0LkR<J8KjvuS`YIpV.~6G`n#zP=p-f`&buY3<p(7FP?kUxI(^t<A:+m=v6MpqCs^9/#^WyHMBChX^M{bpJOwDgY?Fa&UB!b$4K
@ILg;Ev>sm80HSd>FPZ5#]9]c"Rz+)KreGg9YdojuS00CQ1^qnv1^~[$"#c/GH_X:^X<!@*yUz=I!tpi]+TRfdv$<C8S4#Z:<4MYPrUTM}^F,[U/))evS4bghUw0.pHst`P6eEJVi>fJ+UG)#v6.GNlp8ti6c
=-l9,~6myaK{&$NKqz?97C%,#8Q
5.7}^MX&S[ddwYYbwN7DEwfn_ZBf"KP)Qxpt%aQSN^d_kJS%k_:p[thvl8/B3@Hn_BrTsDV+-6q,=
GV39t[d>#Ye*>gETQ?;Mo"/BKjFc1p7Xq/@`<fu-f5]
l*CIk4:Qc8PdfkwON@Qan5"3FN6/:h!$O~.xj<T:C20(*6.WvSQ,jyabK^?A0(VG?>#l!N^R7jP
kdcsI:F2@uSw-zMcs
(F7M+!*peyM,95A`!30DeWh=
6<T,KQvs]V=e=d3a5A28TWNkn%9elWC.-vghzgfRRjo>U;TD)t4W.F:FO?ZuW=q:TM>"]sD8IU}K}p6uFvx&7J*Nx(GC>`;X1B$[$IT;[>Q>p,6R05mxcfS?C"<GA5rvevqs&`7yZs4`L*-qMFN#/I+[nB#lql[mtsQA82mmr,<"%QVuVxtrwm:HzfuiBZ7;^01Qh2*4[Bf7SK?$f&mS{C,3J%yK}glQ&Y>uQ
bfp%l2drmj~P)Z?qzZVEWBqmZ%0/MY7[Haw`s@&`x=_eq0iu}RlKs
)+42{ZL@E]Xt!?)+5VaU?o%<JQ;@zK0`%+qllHhVq*r3IK&f.$i65uka;/c1>X|x|+S*}PnqyLQ
B;@ft$3_qfG9}:rhC"88,m+K9emh#7p+QA[[%,=Vo"c`:<uny2jiL@aVu=ZoX${K(2:NDE6L1MYApsURF8vD,49)UwC9Rj3^wrNM6.7WaCVqQq-poa57ue*@j-}8h25;vb`:;yAw//P8T%O9
[Ug>>lx1tR<HmKtI[sbo(%!F&}ZtK_&y?;Uq4BX0Z/^mHHE#o$X`1i?UQ1>.;"^b<uYS4N6gCn^,JOL/t1.Z.M=t8s]H5|`8Z;H+i2F49}McMc>Im9-]w:*$5eg|dR[gSziGt_g)ZM5A6(q[B}hYJq#Jo@`(uQI:!2QIQC/KI""R09Jd,(R2.."tv"X4f*9L2%*w/pLF6!kgK[G4yY>X7dqxpW@T/EmP6@!0rNTLP,q@-S[Q:$w,uqd
9O%r
.&COgLNkv`E!*/
s(AH
U!eUwA&MhFdY{4{Dlhc5T-?1I=R($%u6Rn>!Ai}UQNR!6rI([7y`f)d$.U>ry($$iB9ofWfNGY3naS^oUE{oH/Rg1-GnP$Faa]&"DFSm
XT5OKz38ebW5u1#9(UE(Dh6{n&PlEKry9@BM4P0O1%G{!zxb?D29?=]mhB<4*Q!U)srr<5IYgCV:Cmi8
s8P(7yJhOZEAGu<I,o$:jp_JtwrmR#e`B`^Mx[
FZ?]$-rKKhkR3nV6Fq
;9q01r@Dm;h$Jhh]9@qFdX-YN,{
v(N)=VqO+,MPIDjp_cd41gA
-.6yFEQ&>XcC/k).%qi.HKO=jVKrX48*Xf;$q6j*rqc8EO)w}p$t`g[a(u6:;v$`h?Gl>j@K8Xiw$k9nCdBL[Sh4MvDKU%hP(K
ld#Z;P1qct%fO_g[!bji<~Rj;7^]Cct3=(M$PzIyOc.%T#IPe<jovj[)u8hvi7lQxC4c.E5t3j92Gf`5.yHi4>2eU8OB/%T-n*&Wn<eRUWm63o*4,8"ch=4VCYYEP0h{fH>ajd?E"]@tM?nLm0lrID>w
H/o(GM]c)[AaUGJ,f?o$D`;Sq"%<YQK::XGDz&fa|@3>zD}mvI]9<F)K1s;uTEw?xy%U`Xmz)Xlh#G!qz>&ankNC~7U.K`VejTm]ZmP#FpuC0A-6b]9&:l2ujxE5HW3l,yt^
]ux#8V)9xtc`h{p8sA;3,1w"+ul7yEI4"zM8i&kN?JZ4oblkF[lTrK2|1K<k>x?L5O?dO@6iDqhLul1!C:>-q:
j!jmw[khXH)@:IWvg3=Ge2;uj^#y(o7$!Hz-7&D4p
Z$<D5tk<}EEovUqfOG6WNO84oGt*?9+t$o-0A:5$pJIc!CNr6&LVO4n:,q
MO^KUb*dK#Sk@"=*[M<By&R}5Sb^FSV_lyp*w=.BJ5h?l(j%n]$E`sW*Bgm>)NtjPrL-
z-Y
N[$Hx"8u"9u=lM7[1xky)J&t!TrNW>p9nU#g$3Wh0jq4+fG^-x>G+s
jO6(ZK(Y/(w2EE$|KUWs;
M;9c]DP*?-
Wl|g8`%0,9_<j*OG@tAyr6fYG>yGX;8VT7>U[<[*;L[Max*c9#xx??rB-<WeI8O7`QFV^n6pLRg*-40J3.x@eD%lEa7!Rhu<7>U"3yn4*1@H&WiBlX9Gi:_M^[]!et.$M(rb)I;t8lqM)XfF`Kyh&Ubg*iWG%qqBMXZ:/.:o%F}t-d^BvCFG3/ZClOs2]?6-H!z?~]rR[6}0Bvh0?#LNsUz0|hhvQ
&H:hC_|T^q5+X8(=(U-X*&N5TP(';case"hu":return'(Zu;BbpAp+XtCx%"*^WK`qonf;qL@^B@)-%na5c
J32sR$FW}:>gPDyfYt099*fI1$yHh"0NstixHMgUW?}V-.rTPQ2$4&IC[@qT5BYen]a+@!Na-in,nrlK+p2(0f7?_d"e73`)Y6m<{yv82+Cj:GlUOply##A+KC!K1]-k%mOB{p}
nslCOqh?HN&LxB:]KhJ]m_6Y.gyFTveJJH%*mZy*eR41?*pOV@+OzS)!4CpsbXYE8FL7nz(vwL&1lV8OPvRL[x$vw]Usr6#`qMhV0N[jC,_Rms~>Eo6d
A,h,xIIMekRZU=W8=?-<j&*SwD;nrR/r%7QwIN15Qbi3)Yt;IpUu9#t.&eIWUhWoq
PHO,ll,$72t,t?(wQV<+7cu$x%CuJ/_qU=i(7QrmBF?vM<NMyF;X:o(vE&7b[<AMkG6}tS]5TiK2!&HOXas9]nc74!jJWkd$1N0k,vxz6Sl=QSZ:
R
Gfu`X?YoeYP6j:#M_eAu?T,I_H)ugJPCfHv;e^Ex%Y9l
-*UAB^>mc|s],$`#TU`3D&<c]|Fu#!o8!kD.HK>697w=
fc;>*f}=BMC#G9qyAmOogIO*|3pU"J_q;;Ony%;N.>St2LI0OccPrc+NHA(v+iEx4,ZsYuvr](x=rIwJQIX`)RvE@G?fN+[e>1ET">f5$h$Zo`mTr?r4M[Bm:h&l
eB)S4i2cC[SFjGJ{Z5(KWngKM5`8:YQq>tsDBX-nYL/VqGj$FbqS.44Sd1vWxWNPOkv(y`x1xEy7(,;0%:&d9h/r/e#a$8Bu0|W%`6:VR/BPWjP&h(^4rPEzkc+%9T?e7k!I&0@Q
|D|Y+5HGO4
Lmg]am6zK3Xc@;I=
BM4npglB3e{#Zu+mX@r"A22x/*R.@4-f*nlb|].LRH<g{DT2}jz:}#dML0gX,&)E`kDHsRl78;XCL:Mjzhz]|7+ebJjSMcsX3OFDUQX<9-)H.o[]&n4gmwVT?(KM33Va:pRtT+u<bkK,(2kXgOOvTx,qcp.:e-QDD/Ts+bWH.Fu=ri
#5%=MWRW%03Md8(&fVEKuI,SMn#nLqs8<nK=1aeyeB""R)^;?g##;6iqDwQL=:*{WUS7i!F2cu$D*l`GF;(Qo(*.0x($Fq3r9#%C2TudZRefN+W*vh6Rv
&X(4Jorh#sbe:*?$&t1@QQbW`sLii_r[[h[jw1AB=h!
rd":3{X
@WmJ2[0-gM6I("qroi?Q[?N:Fs1vbS[K?7KGQ08X$54`5bgCc$E@uR/Bbb7P`78FiB%je&t~?zNOBH5@(4Z8*_8yu%k%EF69Vftv""3NK*ssG_/gm[jr+)Og+}c_D%dw:%^RSU$7*!kc[j[V=Nm9&nWp*%A!2lg1Yu":c>wUe-2:nK;g1UH[,-S6[.Rtb6%mu(ZRjEm
OYOl>O_f,h
V(z!0IMY*UjFp/fl!DkX]-bO&1|M$GJ!*l:%vi,B3Qof}8)H7^Dq;RtJ9NvmI[vW91a](3]`aD=@,U(xQ<C*9IRIQ3T6sy4f_NIi}H?MVIDxe]*xQZSxMg@wH!Y>qSHr.BS0h(Aj{[CoE6z)Yy?#~SRZ5c(kwD#;wZ434NO6KGYfJPhCW:L%0*q+NJxeDYm:7YKgN$8J("5>gc7/k0H<;>}^i5Y$,iTM~^jTD%EpR"PS34H+fh>W7TdlR-Gt>igI.n5v8pkJmQ/m47A.;tlSu^0Qqkm=FLJ$+-gLR:;WYE,cE,LDbd0(pZW=MYm.wnN&hk0A)ABGJT**VSJE("m*T":WZaKu)83q|Gbe~l?P"0U-v+!q7cW+A!*@mO
hzOaZ$%1Ze+1tp
4J[rp>Sg[tu9PtG=L8Rdv?9^)$5[hvz%xNgGsG]is!!*|B4Z;@p6"
q4+Yy8lJue*IVN*-iTMQ!VZ*spagrqDtQA;PPspoM)oG8Yl$*<-#?+F9.MdBg9e(pOD):uJXO7Xs9<d)K^O`X9X2h(.5H/]vF&0pyiA;)>tCV/Y`+%jZTq.JT$`N&*hvB"TZnLc^fTP8B^<K6u]NySjh#-;mRtGa!;ex#LM<~Oaa,O9%{)3
7qFqt"_5F!RHuq{Af1Ji*Vq_1C|;&-A>_)oY$TN)G>[>z`8<#j|h
Rd3AL,d+bV-DLf8Om|>)E3jX2eKRG#rN
Skpv:i89kA#iypSFo:~.
9
jFF(i%L%&kthuKhd8C"o=Au`no%SB+EBk!9P>7?=0:)&"of&;wEsrz"xe3Iy_/YWm"[[6yM=Zl4V"-Prqn0r7k;dR#(X.;Zqu8W$F{>~rui"i?GM[+q>Y/_z2*pVj&=iHshNA1XA]SsJvlO!84%}i;J+)m)Ye$BBx{5=n4$:MmBPrlButl_c`$FliNJ[dUDX$<0pKnEu9bg}Wj
,+{sD(:a>S-pl2Gpx-:r3!#$Wue;Co*jMZ#y,Fvao)[nHdMh5g:s&12r=`lx=ot-(r[vRhxBzWb=G1NfTtjE,j9
t9X^yRi8
yQ<H!lIof
5oRK&.-"0sD)[!0-=lOX!f^{?{vSoLrf+K,8fzGxgCZMRwUCABa~#*S(K?qs!Md7;gvo&@AJ%78f=ppN`HLfnV(u&d-/f^-tt:R(c`1><M3Ak}K`GY:lH9$BJ
i6vECno5!Fl=Attbb*=
mEq[Tlbs4!(AJ4aMTA0QN2;MHS&c?%12:NyL&=F)oN=K^|Pr4MKVM3hPoaQqC,N7FY]/3]q|`M_@4GJLCqV$0}bjLE+EbaDU<Y7cRT>hVvn}5*",8gtryV6LY?yPXQUlE!&I"7Dl4<Xf.`G!#x1;o<q%#u19DRc%L*Oaea$=reqi2Z80wKq>/
qX^VT1$yrLV<e|#-V()XX6faOZqrLP`O!A9PWIP7W}$"5xHnf0=pbKc,WN)ZYRbHov/*J3RNoU_r.e*K)v6pZkG!3?NNit1;sCd
@Xc/.F7Y5p8Eu5f[)T)Zwj5mOH/[4,W]jr0"F$>ohwcJrQ7c7j%F%HHI?76dFW+*H!AN<3.@[_G:LmyNG|j&:9orQG,&2dOV<{+O3]g]w^*)@SrQ`g*x/
.K.bHF$c2-q@`i5+S&21PJ-%eF(+@9/l!Zz(G+,%"|Q"aDH0wa<^mQ19+%:^W@uq-I<6DPB/oC>UQ"05*<6.v-x)?5PfqOg>l3jisN%P<W]6cs(N<]22CgHVaUH$C(+,lLy/]]87?86XE?e_@Gf<l(8}pcrvKA0*JGOtp-O3PAX2%%c69(6G%RhDg_,nTfT^fIk<`VE7`.A4#s
,ad)^K64P/YJdP%(A4vqP8]/+r4$,u[cDs5Bl8|.noDLUrK?w]W
D
n3Nqi0qh^^?ejjZ`k8dfmizBqgQSx/7Zput[C&5k}5+XhYs`FN,gQ(PXB+Vn-hiE`7[jA_48@X~4X';case"nl":return'.ZuALbOZ+$c,/Y9"upd[48CIa?yYWdvUa:!?:#4k$"kA1.N(=;B,xZ.-VxhMxW|51:E4~N&NS`TAxm`WHi*Vd4.M.m9Nj^ap1r==2y+y+]PgKe]uU4d*~5$*$dRC;ZFcJA~=),N+F62n|mPbtf;]
idZ6BNN@0.G;vY*`03=qFwcq63mQ+oH!w*7~upde"tc)?:EhHs(Yrhv]E($OcP+lX(lJP*hr9,rOFJH$p6H<jP3D
tQ15%5c@p_
BS_|41p<[Jc8bi%p?j(ZncI[AQxJ4U1zSuarD~*XZjr3)<y2q]5*XhMX#CBv;huSMnilUC!/@5032W6WwxSd.|yct(dA5+W[p3[3jJPw,)?3cb#-7eQGdOp!BGM67i`n]@WW:Cj6y~#s#Tt;4y8&s*>Xb80(TVZ$$@?FjJg4G6Tk0Z-QZusK(<P+X9$G`9-KM`G|UEK
[W4p
QvXP.4]sE`iMtBp7|SJb.jmtDB&.`30uT]bO-kUIE%<3V@CD8_-J5wkWFcoiMITe6TOKpk;k70iWbm@S%7]bie3@RkB
oDlAmet7m?|`!Ymp:8qj_rVTf#TIrZ;CL[wF+JQW#csc:Op9E5&#<y1l2+
7Q3pQNE
oCAA7Z>1E,v
tI_vNfu_-+=/gpz&kv/a]%_Iy~w)fe5~cDQFAo0hnPpk*>>obZt&d"Q`oV`Fg,wICg53j
Ryy?Ct^_`M%<g+ZxIng{lj,Bc=2yK1et$]p(27IU]{9>5.G[XWJ1rn`s^M.)fwFbH1N*A~mxBv<?`Mv.=.t~2cM[xef}QgqZv7a4mnpRxC3XS0MRBJZ&Yx6
.1o"9CYvT1g+d[XUK[Tr?-xorK`Q9lSJQv;
<ug^WMS!r9Wu<0kJ&SQ6*|J@hN-@x)woO]_GL0QnI!q8xVl1(LHcv*X;hp<N8M3J+/x>&K.LR{Pikbd7j.vkploZTslw"X>bU7Qlwoobg.QL?Pj^LY5LN9@*,h)5=PFk1nlNUa)KPwQG7v#
mH:@oxUZ-7g}8Zn;m7s5!Y2h%V:UfepSFD3FJOHt#nb}#CMJ8kT7yUTJthRP.CqWjIatpuP?pT$[0U)SH05FoWgEUU+=I[_K*~i.%5T`l|$-^t7x&t0o&>QrF6wzMpQ|q1;KZ
(Oz(3r3I&ed8[2[!(Up?HoyLdjw[xG0rA>P.wey1=WC5Q|W8$6Kh_N^t!t+d]hb4O@5{p|j_AC
J&,t[)_.uv_G+McWI6V6&D0r!Kbj&H"#VjCHT_uprhn4]Mj;dy{bZSQ"erdEaV4&c:cs-Sv#47BKC7,+V01Fg$[W#9kCo/SN$U78CIISI`5@dZWIXBsYd]q-6<}u5>,JJTn?x.]K2[+3t"lA@=#%TT@?yj#jQ2;#jC+pXWofO]2meWy>w_eKX85-7w~htnjJUV^Gk[e0f!E
uRfF>RX)7)~RN2tQDev@<LCRxut/0Pq!.+xE;U7Q
ft5&lj%R91"3QeBKMS!C.>#AY)M+ZkdcnR.2=jNb?Z)XL)3.k0bD43<_6N`6k9*1N&o,qfL6qeY/PWkaShIn<V4b8(%e.Ne=[p_(]{fMv!c[y|"nYZ0+_">w7`]MP:WqrWeJ$rRwF
oLU_@oU:W[NZU0&Ok"29Bu.[0@d4BF?_]SCdcR(t2{3j=YuSA]
TbW/lL"Z.SE]SY42{^&.rwt=WC1itj1U6N0&khvBu`4uGSZ"*X
bA]w.K3Qw:FU:/LnB-XB!.*x2AC~De7H!|`xbkK+;b#WZB%0`bAOJ7eg<_9kV9(Gx5sfpL)dGw5)@*8"
?0XZ,Y}!fDvu6T5+g(vkh0MPDW_=O[s.gOlR~C~F24oU%dx#zKbCqR]L7.BLuUVRL/gu*rrB61g84$p`$:->AG{i{WzJjo2!2[&)LcN/hJ~w|1u,tGzQOf#E$n.>Vxt=b)8CU/*g=?IpJ*miSM>&ra6<ps-Z<jcaw"bR$56^VdmN!U|uz!VUgM[i{jS5(+(`xl3qpZQFgca/HR>"dm@x66Xo.2q?>-jrdcXeDt,.LTTT(<>YlWZ0&:/e5rv&l?ip&))2IN6j
tW>,n/n,#f5N)N%lYp];)];61B)F%A=!kU4I
wX5)G;1#2q9Iqm}*VK8p"?>8ak"PaTwa=end(uFKxl|gHq<>njg>o#^S8KvTEfXrnF4C"J.e9y+em=:x4fiOV$Y[h0.K?KHh^8dN#X

8&E.5QY_jmF.n,!4:=j9z*?kf0lCbC/b<@eAUJn>VFewztV%=?-r]
e9(IzIE/F<793?J$wn^E`/C;gGhhXId6boc2<InqU[.2Ms]&QV[-TvwIchmuui}42FmS-p"rj<lc[L?%G
K_Mw)n`#U_6D8i:6-&a-M<<ye@fqm7%`TOS>aVdQHJ0?3,Bdogi2~HhZ|$/sv^rO~i~P~5DD*:Edy85m3?`>K.K(JH#-n18EOu0L]:Dg`%k^4
&mCYs:h-;Ld?[DhYz69`~ZSW$,ks?u}qlS`pDnK85>%$BUhAx.*9v7Jvl;W#jB-YQoPlCZ3V%f~9NQOc&bqy6=kNgwG"qU?i;^TI<CoEN,Nk3SuO88)sUSyno$i81G5Qvkh@Yo
4lyLr/Sdk10+<":j?,UF1@DrY:F]s3>Pg0@G+fldXT&;gy-|NP_`-+w!Fc4,m`^gU,[7BEN0J4t*yg:`kh)gh}-LFwPfHVA]jM@Jo9lOE>pyRP)ZiLTPw_-brC:Wjf?%0UpDbGSVsEpPYH`Z(Gge#(:iPa(T(+o<8|Ptwd+xN`WSk*az_`FCOsFeYFN&';case"no":return'*Zu;:bP.!:#5$fnODqR.GDJsE8P5qHZd6C>6W<@9A0A[4&QlHh9l_-%QQ%almEgnj.[2<cbsR9X!Q8>%/4kMxvukBsG2MV@$LfwND[".-@c99AkH/[KvxFf)q[<PP_Uaj:w6KmyYU^>dDZejm^f(#6.F
<;Y64ZX$]Ot]vAS)m<j8`(w_WnH>!rI-Fl4$NZrk*DC2S-6=+dMbynW}C$Z9l}(xK)rAf#5z<fUCeg]+,P(lt~;@a><]+tq<4uF?9tc(.i1=pl^:)03cr<bo=*)qm;k-wa.^;nj4!%Z>Il"-fhw)-1Xzv)K}$BTp8P+wVdqtb/A4J6H&3Ok~XB+@&BJ)Iu/t@<YeB{hhsRF+5}$aeh5j:X*L=`T,x>,:GO0V2ZaZP2O#rMjq`CodZ#M[-J&MHL=~r{RySr3OtoB14P(.C}W|n;YDo
]6O.@y55J|"Jb0<;NSIJkYD>84UWw?f/
~!93$mMOQ7eM21_um_/fCJ!;s4v!,H0bDM
mTUnb{e&G
^Z._h>1|&p]^"7R4p+B8S,
@JTAV]m*G(9e8y$W=,f]CfFbfy}l^K|d,,L5=2g*sr^"R9^RAg|jPdQ]wZ9+E*t.[/XZ!pvwv.W;J,#%nD6
kbG^8A*AIH$ulu@
Dw69nRe@f/@PG"=G>]W,-0vsDE%Trb#>QBDLlT)A
`1[jx]H{>x:vbMJ,]>a0_soW"S&9ZP#EfVG$>[^*hi,m79hOHf7r`2cVC@mH`*O30uydoNRxKRkhOTXlo^"Qs2cQnU(Wu<BRf+Gyi2Xt
jCk#9&7),I?l~k9]Q&:owKZ$Io;.xU=v|QBz&?7aR,+XTs-hEX/W`8vC"G?nBQ<vw1jAA,$v3mYPYS9FDW@5|B7aHvtt1TsATiu[d"g7L,T.w0e]9te<15kw!fUJ@3cSX>d<(lR*[s}PGhYl&UB#2gqgKS
=DB+b@r"2jc`+$f*qT(hMJI_LoCU/o38/B(r*/s$d+N<m8OJPEww4],f[48>%L$j7<^]xoQ
7-><Y=c
FO_{*}q|Ps/^3!s2r4W>uIXzrtx`u}":ngO5z);rv6O7kC2Qnv.W!B$i.3>VtP%Bsq
9ZnP|:@_8b^3QtY"5+)@K<eGSQs:DS$Vm71:VPFplCeL7MLM_/UHSq"Eh7PeRm>!
u-_VjJvwDd7Nb[cT^iTb$4Z$;=$9yc"2y2T+Xc@XC1jY/2i]tgH-)JYa6M6zfw4>438&N>NLA]v4(3g"_ym`"A<z7hh>X&O`u*DXHfogylYgxvUvJael)kD~s|
r.MWn<N[&s?ZJ1Sa[kzj#NC>@1>3j"jp4]qpP5`,P8K?y_J.w^0:BE-yN2op~vR2o5%-)Q],C3jdm]:/#XN,r0do.eV&L.GTcf5M;nJm+y]lv<e*W`R@Ov)9Npmf
r;t57Ds#]W+!+mLiJAu"=2RT_0j6(oc_?uJuS&?jcv#4#>,L_U,F-rZNR;-!<GLU,Jop[jawfph;@P@xI!yW!T>-;2kb
U&T(xpJfw=?7refW>sC=2mMt^bvPJIy?=MF>z!1`XK@m(-:2F,-*4a]LF-(Q;&g;<oiw%fLJY$hR@<^DJG=8/]66n44@APlIVZjJ~kQyzp,MG*t%Zl0u9[rHQJJ%i];V%[7[I"pUKidr.I3w!G]Ns""&3LhdBW)IGr8lD1cITW(BQP_:rK6/0BIcNT/dR6|Pb2u<"D2asQW-kz)!/2U!?B(p4?&wq,9=&:aJCaW(zlkAEmnMz%)08Ap<ixJ=|N%)cWh9pR-en^K?CUJ*l-uQ5n5R".2cy8HkCy+jUK{j}pDXu(m[PjA7RSF*%XQQTQhZRUjKK6pfQEB$M?e0.w?>aT|Zz-8l{4U(tY-60yQta5d_iRhBo"PkB4D4cA(3Ao?>V0o*b=(m!p
$?1XV]7N?ge0:qcB7CE]$^/$<)kle}1uq#wKS|e2e0d{s[h"Au1r[sjObcKe^De0YB#!8kKE+G4$LAlQ<<<4]Le|pp`$;[wX>H=6V|GB@G0n2*o!*rG*>>K7&^,cn_9p#/00]b0}K_TF#xS0(MF&V0l3<K#h^??F60nM.Q!KGLpn(rk#hZ/OE;3(K-Ma%7
Pa[M&hzl1@As{Y#K|iM>NBzKYP,`e3SQUnu4ZtK8EUr#TAg<sX"QW7:ws5ZBL!lc^5iij;#^Hpjt66(!pG"G0c,T%)T^$eWV
0UR^gRYML3T!=Ec7fsrnN>U)srvD?RUoxaV3%p?N-;/k3pHfTrZ~g>Y}=UwVZ$tTcFD#*}La4A+.tY5I%RNm*"HInK&I-8S!#{I1e&-k-!gk,TZz(Xo=/+-#5t(2fZn,;V8YE&L9gHbzZ|`c%~BL7sm9
kS>(btN].ik(>@lP?;*dxmd>`8}4yZug/+K.n_mqULSm^7Rs^3lm{ut&6nRB:Q~n3J*K)_l]VCsI.RRvKoJTRL0Zoo^8MAXoDI%g`^GB`i]e$rz?k5W4~5G=63XQ>M>!EQmxXg#9Qsc-Zo[R6(I_b[W=]#T.Cq&dU_{B/<mVR,0p0xnK:u#m_(Waco?,#cWM.S?]7fsXU1T:dRvq5V![o6sUDE_MVIdtjIXT*q%;c_r8>ben:Q`_5]5ZQ5YYt_JLJY]94):D>wanqcfBL<:*Z
AET-:,>Wj&]./]C0d&ViE/MI>uqgx27I
j1q6f!J8-H1^=ipA-<Yr,#Nk-Qx<t^TZTxs=9".Vu)8J@!=:oW=|&byN8y?ss;1Dn>8?JTnk-X^2e0_b_L+^[>Aan@Fs,8rVirj~7Q+vVvU#@]o#c}""';case"uz":return'+s`5hmPWB$#!(tcX>ionJy3.[3l,;=%_krV]C[~E+Le25
R^U
/x4cyi(V^tj(hX"Jtu0!R!T"R*SbVs.EcgC?n!8DgIJWkFO+#T{)v0K%FL+]nP*U>@AcmgHF[]]L+UXP-y;hri<r=3&X!7Mcy`X@%g>==JgD{8CPL8tamhh?Ld{-ro?X9dz2d^$^mRBr0Lu`{1
rir`qa"2F~[UnOtWxcD<cXl*h%cz(hYVk-Uog
Um_4;yW3*Q[fFPgMSlUrgM=~_`jO>^rZN@s4SEBN[.+S,T3uYitx4kV0QD2_PK.PP3c9Z2bP3lZw!&;
/gm1-_%>+kP|u7P#b^SkcX9%o9^D:x@
M?kZ-_#Iwxfu/
U;cneGNL,s>{eZXNPx=@7~djaQA+g](93l8V>dx?Yb[.!r
5Q]D"a0c|l2]Wg3s3v:0-D>n!mHG*[?tn%dx@.>PqZyGcV(C|*?wkb9
x!%b|2nKR5e,<R^vJ.{N,>&T?dpMiP~ypOHjr31`+D2*[I|*cwh)Y?tO4Jvg,K@G{/X`0OqBhtis`$utd/AHV:Ks:ujPG
IOO]ts"uy:~iHpQz$4jxhk`EOnKLfO.g$Fo)
b36[nRFUB+JcC3AWA-L^p6l^g6b,2d=L6UAQEuS=du>k]^_(sLy[v37/,x3w+xAbYZjW8`bgv5vL8J$e/43<<c=xMZ)b!0&T^I)k3}C<TeJ5Aq0n)ZjFfO1|`>U#`@6wQ)arFRxV9zkiZ0:SO;(g*G;Xc?a_)U/T/pcb@MIRm%TzsnUMP=DO_,`t1}R?N7`*c_$viM662oNg5VI)]":dO_cUiX4~3i<&+RM;i8lB>-cl]Y^PRaC<U|LZcKQe!"jlV>c"MlMipnuc7lW8R%wGuz.9+?M,nC;pIiAp?,BP/+mgu}0;?Mq0PdA_#A:"qx6:[dyhi"oCBzYZ$WZ/0nASnz"K&##r<3&zP<5(y{M.=uUBY!l(6Wb5h]qJ"hSS(<vo<gi,"+GVQt5
5>#-^V&t@{dFo)5mB+:-#sI7qN1.TUHnbGN2lbW9yxjK1OR>XA,Z:p5;4K!`V24580Ql5k$u_"my8HcdwMyNwhO8beZY0,G:Y@O91|Bx"hPu1ALjxP(|)7:L!_+G
^K#Lyq~`Y+6#$Nop1nf)G+%:6)FkWk8<Styd[-(wJ9td1gw
#wC82a5*}Yx*kO&bqy^31y/U`Fo:dfA;mX43UAuB6MZPh"t#}ub:6
zA"A0J"3ww`:.KNCiF*dDNE0CjP]r;kV*.6!r?)6$BoD4<~fnEP/l8QM*Qh:NX`:bA/CG5`$y,{Ry4{*z36er("Su=K`pkbJr5qi#IhELpCcE#5B80*Y3d89fH{67!>"(x=bYrg"M0JX>WSX0*.ZNWJm1,n;^eYua31!$;%7>.^N
jvF|e,Nlfo<5:3O3+=%_isi%5Wt/iUZ%(+vp]O;hi~ZcXq8?L+@Co
f|"Qc%W&X"]3SHVf*
l7Icu.ErsO@8(<#u*i?<R"1OFnWycE8,q?.
i^siYG`aA<YPF~
]
4!60r*%IY2cv,6im.kUywIe7plAEz1Jh)I4@KuSdiOl3:9kj3LHW(s}?dsoV=+}#:o_JjfE8J]";k"uL([kAwhyqN(lh%9:[MJ5P9e4phtKsDYc,heP3K"sVVaCO|&k0@#/(0){g*3U>NWbU)gy0w>=g-K+(UN>eM:#5Y/SiW`a^<Kr-z-5"sp&vN*1;*R{hC$Ox,[X7Mk1%_-9x%cYo:sHbd$iPSH0/.D%)zqv/`)oIkG[keT2GX3JC8HE]kM*b$`;-VP:RqKADdelt9#,+"[iVyuaWUY_1iYE/L:|;{34
u5FP:lqIkO_R8KKr/gUd;e,U=@1iu2bvh<V2Ct]^mL
6H;M#6qt`wki
;MIVNCT[=uDf=8C6&+CiPdd_?jn(J!4=?]my0EC`
eku0XF,_yC2yv53N:UiZ1(tVQPw.c,q]
HG.bsP!SmvC]t"K_~^w34wn>fO61N58Fm<pO&gOb./Bmsvi8c@E;BxE?lNC%knoI*C}h|w2cvgU#evAknRBI=;22tn3.9$4&MEe<w5n`G6wM@`z<H;n)$+"xCkzh]iffn/!%zD$_
ayTggT!_f5ax%z"yTI9sZp9`@/81v2ni&A1xq6TwXP!-660)3wjCXi*<3`32y,lke~$6!V1msmhf>9:?L@<QXG@w5Jvd1J+elbAOu|^/+#tShMySnf/+Nys
c^`CSG:b5vNs9X.qlg8-r"L;.%*6:Nthsu49lBOUyN,Lt}YAWn:L:UP}v3`$]]NqG+1RBSAPPMw9fh2hBMNHv%u#_;(;O{d7,55VawP$jLDUe""1fg
C"pmRBSZ`7t"?nXl^&xFi%61AYe_&Z9P/C5K+!:00bjCM(8c&etc1gN+H]_KAX(JN<*3ucrG~WZ"ym4L^"xyCm$;~a@xOLe5w,Yqehw[m%:Odp5/b;N-#ou)U7zWQ=)6>Yuo:m_lzBjh=5zZa15*%hl0u]U#`eG)b#i&TXxHU=Dm
r@pcBTj|dE<+,J7i1ljlQ^
Wp&)JvLQ
W@_O,t`G^)ld0%xz+)Ri_Lxxr?#`Y]lPb~@cP.o7?YfaW(g4.;YRY(M/r"Rj6Jp{flXwF[p3WAIohITBh7gvGb0"CY$&b^IIXac
-dd|%24za[g
hJ`3+RlO=;_Df,QrV<w_v;I?(ejtbJ]Uxu5M?b(|xyV"`iER@He.T8pekT_!V8Za0(7_^Z@);R4[_0?Yb70&uCNMM<UolCV/g)>AqHT9g<&`v@tX';case"pl":return'#]^;;6LDI)R*#fu$Y]}2Tw;CEFVGI(64C7([u$Fp~2$]WT#49:z;/au-?0E9/=nelkX+G[)%$xmnW@pTQB(RJT5T)Bh4^K5aPRn&M<oM)h+T{.3IO2U>"0Y+FZV.wvT_cnyPe"IGLO?LIY`M"vqMTxL>rL46VhK^L"]?JPhc:np(!`5mvp#EF=FnWrZazDBW))HHIUn:dI6x@
XKynjUzp=)}9S6]QlSB@;z%GP
pc1h>"&.$R4dlMov;%;ML*v.G-jKmj8xw;U;YsU&RX+nH.b7VKVjP+;
4$OXyP+y0%aDz96ydm1n6]]j^5],;Cg@gE=&.aI,!c[l6@+TTRgTHm.30-iuZU?
kf~?ta<%+U96gZ<14R4N7a-6Uuk?U2NfL[T6_T/@q0Zb6H$Xx/@0h,nc)/Du/W,1IhziLM`,ZLngcV.$.`7c,eSLCPIYO50[jv%i;))O?>TXgr/HDmv[!5lai5RC}JE;T7C7<
Q"pY_9N;nS>d3Ryr/mxQ0Mk/@Ix1Gg~*Wo85ch8.weqD2]n+=vfBYa!alSgDK
/Hlet:V)Fi@+0%PGn_oQ_=%4+":A*2(JL&5URBbxSku?zQ{A.f/6ch"GtlBc0,5rd4!S1qwcNM]*!Q)TXJuL%RMhv:EnDTnoEFbXJw4D~^x@,J)FqQC/NAi.t!)aKFMN/h~
7T_K{te-&eO
AZz6_hunI1z@?JQ$>?f,o3b>KM3
2]Q@f;{G+.W"LbO&V6%ME[$/.GrqPVQYqVURjsIMr8Vpa)("yE)wty>oYth1aSh9k@AV[c)t2(M@a^n3a4n>v7MS5n@Dh-lXsm}S0qs<dttOV(S63?q8i/Wi"u8p`CA_6Wi9]UTj|<hQ#=hsA^I]1<{w-;j>,](7^5,_*"Z`[>v1{2=BJ0W<yH4Rg1i2A
T,mO]^3z&au1$e=gONeQg-3*bNWwG>tsC_TwdJwSTLMCc?j6t2m3Ju2tmGHMY"f?+R4/%5@JBP<RAu(GZhl02<sUWKK%)]-o%1ZOHVInRf9uif<f%v.qiw:%b^.?}5^V7u`W>PyZ3GOTZ?Ma9]q
jTVOo,xN,x%C42Bk_RpUlI.CQ<vGu@FM^(hifD9qpJjOP:!9P?Xqg=GMy^->~u<7MJ<9[UbHQ32v1t_yv3%KGI[R
W8wZoQWrv:rxZ7>BMW&&N9ZW<mK!i8RI1*H`VyB*%rO/07Q}x088Q!@3=O.M
ddg7f6Sc{h?4(p^)>GavjiS#l6@flJk2,A:*R3S&^>o(c>$<Cuun-ctM*l_w]OVnGOV>okng==]0MCVwo)gJ6H7C*Ap.@AiM9xbk|x^Yh6Dcl4SP72:_;)F3uYC^_I<AP1_jpOY,{?N=+@th(Y)tQs9!DHl7hw84SG%A3_Ha@SL_oJn_cBvQ
n|H2r+="V-+`j=cK8Hsljt({]{[m:GM0a`B[r**[fuW(gQ:gjf-q3%l}4Z%3E17z.WA{^[+i_"s}naWcdZm^VYlGJ^=+B=Mk0GPD7~.mHiS0Y<bltYCz;r"ChM+wN7OYC5%{"#;6@t03efnX/<+}IU6
+~TQ)DP[fs#4-guu6"&j0IO9PWS4Ca.dj"R4&cb4qOR8EJ,"ZIkw
kwmC/f1G1pEMei9;@y0As8Yu@sxgg#4I4?3_!8G?e_i]<:,5s7tic>j03#jd"*B!nfq&>
"
<JDm+%xqzRwanYXjk*Vk{
9S{1a(_D.Tz!C**:(#b`F-}/5KLYB++XieKEe@&8?D:LX({VCNz"o]GMTt:Y6,V-%u.vV8lqV$OT[;NV0=K$u&.:UD<UeS*?MF<%F0A*9D`lCi!ye6zslR[iU--4:/Z2h:6!F9;f.;n7zt^6_n!GK0Nr3)l5mA]MnBpC{5^P(Uoird)y7E36,.Yl!iqaEpDCKC3z#:pg6^NBtQ21<ct--><=]Y99V4zhfDP8TBSA+7V#8./dfq`=nnakE&I=%RbCG%@_+7VLcs%e>s9AA%9fU2MtZg~r&[Ma7xuBUKjiyS
ZQT~+9>o<MhjqLQ?YLNEZ<iY5"#.Sy0WqzOFw)B8Qi.QDI./@UD<fve4LVVd(p$[-:0_jT
(r#V57T:^$>O%sjBox4/O(HUrPXW8e^PmCj5l2?uLxY%PX9[z,JPoBpV<%#>vR~?vQN)pIGJ0-8$sfN+h.Z%)*FaHct7uTq#Vvp**ZE7h1}[>)<"H@H+E+R$c7OHaY6&l`>#n;,IDtVoJa@!y5+O`z"3wCu-N"s]frW4B/K7R#gUpwhj]sSol!:]#$eG|`m
nB[g^keS0$}
lNT/k(?NeH>[qTl#J$"fB9@o[19#7Q6:&X]LX,OX[[Xx9+yQbXI`xu@PkH.2T`N=r2rO|"DejOsh>GW,SSHHv7>fcxle!uPh8&*z#k,M5lv%=Gjiz#S2AYAw?JUd},0.t):GOx}YVk6)QH@F(ZFc%0rR}m9BK7;v{
Jq4OXn~8B!ZBmWM3ktr9YU`GAp0.7&@:{J`v]q6a`W^X6wZ7n!FM!;>k$Kn-@hnf9SzOWOm:u^#7Z30Kt:6
B(x+2iQH$k
"<_$5W,0s;kLU-r`#k.}3ocTaxZ3PR,v*v%xOd6T(4:t/5vqk+08-?OKI9ZC1IUOyGHRb-(hv=L!.foyCzHb4NM@aFtJ2=B9@an`[G[L-nVP*Gq"HF"YB?
`00w.Zh
0]yDxfRg,In*a5sp!7m]8GfMN:)@2$N[3hRx1I2^Nf_5(UfM]/%iH].5IaSJF[EIQ,aap%A<}N2;N<iECWT3"->/38u-.F*p3+>N#l;/vxf$K::11ns$zFaS0:1yL""dcQy`.S"CUXk,09p!@P"N8kV^pVRktRv(m-C$Q#6EWKm)Wb??Yl<pZkJiLwik(W]Z+A"U)Dmv:SF]Pn`+tf_t$
d?=v%GzYBVxV@Y/v5@bJkd{sJ*o"K?m%Uobk)ZMyg.f"Pi-.ih_sDVtU%Ptf#E71pYT?6BdOO14j{+8I>o-VGh[9n/m^;W`x@RC(pE!o>U0`"RM8/QH+XA:4~D1Ug=T;$]8?LRbV(NvA8c4<Xt6c+9B)|B&=@v2uUOSW=hBHUktFE5DT7<wf^72&I9kXUW:rH[neqr}i`hX
SA!m:k7;r0@-q@%imZ[SFgswKV$>ho3h+S{_)W;9>HJG3ikM%tK,w=yNS%UW!;>5kH&9gp2K~I;sP=FVlLv.TjH37o<+IBoN!X5tf<DPoc3H^ZxeD%NFj0xT/>D9Our7TLh8v%EK7>qPFS:fB_B1hY]@g;WrM+sijAfUp2i7AN_mJ<q3pfEqo,s4&CI:_v%8ChGlpA*7<7
FiW1!&IVh<d!bVFTSs64VfKn10=)>~G?9&R>iMfyS6Tpb7#M%5^&_

MCw/=FI)SKiE^nVq]P0XB[eM"?8if3S9Sok-|o|gU^rUIFrN}V*qBenI<4_y+S+aGiG4Yjs1aJn>-6=(2';case"pt":return'"]^@qaMD9(pl!VR$@PL$2$Z<4_zZ+*S"3%>6$vzrGg.D4tG?wn!$[pNwb%#+}8D8]%"n&rIg3Lst-?HwMg9P/QTW.c@[Al*tMkGrhte6P(7vUp2mTdBKvc&$e2ZcF;YCE<F*weK>Tm2uzbKda17emE&@_PqHF,YD+!@Pvb!v%Ry,>4P<mBlUdMjK1.EM~xok77dnz+@9S8ms@7{&p2rn.6@J=Ga2"iUYpcRH)`)s<.TO!:0D7yfb0xs;xD:E3>5M1d5R$xu56V$z%(dno_y+xV}ab3-CDpA>?^qU!G_0JF*xw?-]gih_!pFJ6k_Va[B5u`j#fC%?ZoX,J?P(R:{b5]8D]Q0P}JF:47{2yp39X^;I!clc{QGa!_Y9WqUT*ujY"vW(a#`Fp7oeBN3&>UBANt*MAsvek.5HSnbyoox!&kBFB(ZlaLH%c3c`7y33Sanftkt5PVqa@C9J}",7~FrA+(rjI
pJu$%_j-w]_uCDYyVw$D(]>g)d9"N9yR*.m_"?1.DaY
,_UKPljRU7ML,`*UjXzxr
hhN:^Y1)_lV1dpS1i+fiT=lB&cfT>ucnBCCye65,RKXxeL)E?M*C}&f*K)+Y?V(9kEv^K&ulH%G7y"XbOmz$%;Ho?%*<5[Ee%h%-P![]><8QEw"^I&9[2I
?UM,^w^[T%2&rQLvGwr]
]G`gGRU?bBB5rE&=mWifbA&!RNaFcR4;Y*_4On4G|HUXx]Mq-oRl0X}<fH?
/8TxDiF56wYf(>CCavSw@!GL+ZJCcSb$i]p]SH
,#H9E,e5iTWF)
GH=;YaEkof[(R"Av=4tX
oQHma89T{M/vTUFC
yaer)?e.>Q"oJ]u#4>$ZgerM[II_t21TT,_#h"/k3l)oxj=Dq%hmKWS/oL+-aU^bx#G;v,hA7mOSwOa1m^itsH$Ze@rR1kT"wpcQc-v^/NYi/>*`*nTcaScOwg+EQBhLdz^2W?Jc5!.kxsb#I"a3JadlAgyy5|$3&6Iwq4H)uevfMT
"Ul?{W91.=pJi2LyHpThcP"9C6}1/mfFY#u"`dLEiv(;Jno/nLvEO^M3S8M>)SpNr:;(!
s@[OFGiMM60y07;AS9+2UBAeMOb",9$`rVxb".]u/T6dS,m[#R#udC|7sk-_4R{h@9Q`BBzoebHcdTmY,#]ff$~a=cobmn^+;z!ep&W]wS%BaA5=SrbLSRq)X-]=N#MRzM,^Y>-KHLE>9jC51*I(trAk{(,U4!im<Rr[+fqN2PSJZ3tnI2A37?W#j?_4!$O?O^?8#)^N"*%w/Aou&4M),m*C=Zk+K^](-e_IW-tD1c8`n9I)P=(ofDRCLt#R)rXbK6z94ZiYc2JmDU{nC0e5GJ<N&hltp7+W3/QJi;!og2Miarok.SLxZ!bg
Y+;.j:r;GqyJx6TwbB
e/@5ZCzwV.BfDC%6V)Q9Q/=s;jhy_WyP#B}
8isc|O?>|470}2a!fjz@CGI;G#{lY,X#BM$$#gWe-9
EdA$Ww3o&9iHt$0>t0"lqINgr7dh-MFrhb5Mn9.&6b>sm/#-*4OU`cI:X52Q;KG7A_V3xED0!54bNeBM&n-Liu3vV#Bqh-:H7F39(gXwbV0y;k9vab6]+3T/MQ]`KVhrxw^ii)l/V?Q
%*Z8Z=^ilNK2y%I%3y"oP2Re[=Ca622Hqiid
R]JbrE1oM3D%g]BB",b%XV-o>^bIno&W:Qbl$3Ncc9kTR"J@n2pf-k-;Ve=>lmEt9F`q*6]S`/w5tpV>#_*2jHG[ie+;,p"`3Q%[3P.H`@`e)alo,]j.K7fs?n(sC;BR97f_=7%]TBixXDBC8$~6S^&?+Zxv#=UpCW|phL8R=JJDFew=~HeOL`aFBiGTe([egOuP"ISqGZ6]DFI:cp^xNk]BVHPV~rLv)k!GVSEHUhJcycrm"Kk#GIMhsxa%iXnAZq@qHiWSd8K$6J.H$pS)?`xyVH1>Y6B1k5OE,keIs_t,>SB14VSD%vhkPW6+,YV;GW;SbSb=wg|k_V|q~H3izhiCU3wbo+p]s76.w4=:k_8n7b{(gbRMX.TZZ0(%J2j2n9JA7%M$Cu*:N^y=>gk?4JjyVZ|xO+EK_i16[p[K&
jvcU%)%D(Gy(P=2g,.a)l)Qjk?ZajjYyFl6Sn=%j;=gCRs~;<9
/0T1xBRK=.o
$@l[gXc1@nuS2;ai>`W8&NuJPQ@g-1A_"DwoHVB/*`T[=RqDy-tT]*
]J`OXETpn@~`W0H[Y/i]@BJ[63p1m?4/;"w2-@oStW.A5)iZZ#?u=+p@y[9V}Sy2kw%)+814d@tD
V3Ch
w*e6d9[HL=,^JjEx"OKGF=8u1?_:AR@xjmb1|uf*(9"kgq-5
V-g5KgO4qna"&GNr^
[Fbd4Nl"j)bwknAQ]m1_HGSUTJm`13v)M4AQIDDd[",-)W/g<PIqya8GmhF#z(6n&zSAi#q1.j8B9q2
^t[_GakR%i;,w"1D%GD@>o^U(Z>>8pBQk):A;#b^lp.YM"mTS]Ov)t]h0pu;I=L&rFdgoT<;CgnNFLTxRxs
8d?ik<$>crU,k4Rk/WOSr|HlZ%I6b|oY/1Vba0*9UfdRD~?)_zsBQyx`GW6"-~&5uTV)[6`%H":M="e|B5T=6.[v.;-8pG!uqsa"[1.u%|HI
kFmJAqEJdFCr!0/Hc+=xO)I@XMRBR?i,EGh?LJ,OL8[GymQvktseFviK*@<TdM8VSQbPx(^&/^cV1@2)rG".j61vq0}u>@-%xU
-nJ<uNqD/MW~DRr#OV;JmyT|I3KLq8NyWuo7Ui[8VqGT,"nb?L(ZBJ1rZSI_adr-
tyaHDeU=Kc*[yY6Z([Z=U=@lX]>oHE@9hUJ@{^b6%)0IIljx}7Nn`V!@6=wez)DAFRg<OyYMTRCO%W@4OO622#nShH<kHTLtOQR/BXrrG3OM"hThXX8IFoV^@`bSot}YVVm2:h|deWRgpX|v-jUfEselOv"MGe&v*jq10waX!Fx3P5%/57+X*`sn6Ih$D&}t]1j7d[Za/)"p_U1XnFq^5wA';case"pt-br":return'"]^@j6LD)?T)ni`$><s9HE(%F;5TQ$jSpkh,nE~3oF.hIt5?T.3+qM{.:gy!Z!XNht`h#wenh="a]rlfRR6O,.i4=F1s#3pXSX%K96bsTxyne-]z%MFC(T?I-nn"~"pa(6>+FlL50y~OA?B.XCJv[+-%zVR;*_3y/J,.4cs#duo#BhLw<f-$Bjkn7FM=OHq=2djQUAA%mK
>!jv&0#:@$/D2hR(DUxR8YOd].EQ:J`3=tvMqP7hz(s!ART_88+Wv#hs*2V"GB&/KYB}IMkE3I/"u6xV8_W3m69`1&rhY"RN@~AWd^Sg[mQ#ISTZm*)A$1sQ%od*h7uS8$8DJHKa<A[Da3S(#_R!>fu|d6vbtJNU,2L[E
liJ`yj:ZQra,=sDd>3i<u6vxTI0%Qw=t)[/ICJ0RMZ/357vNc$mBm/UIJxn56<((EMse:G=skdLd5mfT07So,vcQ#?yZ2vl@YIOAA""v5)i!VE*Au:RXuipqQr*bVQpLig>,K%Ip$.<e!;ZZ:}sDqNZfHC+cOm0i5{UwQ}Qz.6,;MF=:5-D>PdDyDj+yq)K|?hV)qPq&q^ff3=7k;t,V.oJ3a)9.SuKeBq,;@T`*31S33p1
L(QN%HU[T~bu3_`LK7gY1sIB"oF.r((a@g+}Gx
U#<jI:jp9H,nd7
[ZHxJYE;#`v}JdC{]Q)~aN
cRv.<?p<JI{Qc=2*2vE*%d:.?#86`hUMA([w__n]?3jO)P@%a>8^FuTitnUiW0PGQ60CpbtWJVZWPP6B}+Vmr7n?pBriXHG=&_6C"^>^|u^kK]R8m?)t8.ZyN)Hpi>~#Z,8&ALCbzIu$Jjr*
N@K&7>2G)lT$q@q`.jH3fAs*DagPBMyrxV?ISNox
HEgwTtq`*7MTBKTL=[jiZcR[_C@;WVm8s<xBm0unff&A1,
%Bc}J/eC+B?(NPS9
_mK.F97^w@+oKrwD6ko4bVvqYxz^u5#wm>{+HK;99h:SkfUr!>hv;c
E<D~>pBZZ[t+R0+,85x*Rn%^#.P5WWS2!>kX(&pGPTWHkhClHls)<?igb|tz0ESCu)%*TU3Oqgw}DB/lqDg;4+*w9h_8jsKIRn8CAjSo?.$lRe-`VEyxrh3d$]<57p[8SP!Pyg4iZm`?dV.?xMNC$15+h9-Ap;.tsDO/B}+
OH1SN>*Cy@+H`_B`"=(u+@Y{O}NpS(`(j/A]6E[.&my6_u[jn{>#ci96F2u7s"u7kZy};ji5c&Cx5m(eOBR};w8/OO-EsnymTWM!"&0-T)b
d,dcmj/Gv5G9@F-7hN"UJulOIQE,`(#_ZIt;k2.0[M:]WA[{cnDdMgvX#B@w)AK-x&)h#)Epp1R}(>D/)YTF^D;8
Ku"<YgD$]=Q3b>*ZZQS$CY?eR(4%Xl=wC$AUnP5OGGW8wFT[en6,~>Z9DfU(d2TH*0S=g8&OT;IRt$Rl6DQXV0V+<`ZGYKx"4qpgz(:+`Y?V|$hh[wKsMMVyaC&d-gE0N=,9q(`99Q?=C%IbN&[#"Ng51jmRNACX`4z?i
:cogH[^3u:cS}Y:f`,y>o7,e?:yW6M}hea&7@BDB:KWrGu<PXT@t~wrYqg*tD*q^2_v9{@b3dZr0xW!"FI/#AGu1Om*f%oQw!
0bVYF!u`^2$AH.$Qg6G7-<rDU*H1=L%"G84?)0Cy]Pk%P!og/Yri26mMBK32CBsWMP_a89NqY5a8gR5rvYK$9;hGu0K3w/HA3upYL(0Rl]!bJE~jfkpq[I@HP4&@-SiWee:=:Zi*Muv9%-U?BYyu3$9kpa
nJ<#8zxnR8[!!:nvRVrH*c-qsAV[7SojDO[Zl,q&#bi>YhC4Nh_81(G8VKWqMV5ACPNFc:8q%&Q}+S`rGd7Ol1Axyr/oogdvOU,mR44HG1v7]C%S/{W3Q"a5x~:T(|M[uPE+&BG#H?2o%ofb05]8,`Q(VP[sGRKFUuKdDl6.;Jjxk&1$S1x&N*8dViO9OUBV8&q}Jw%qHn
?=lXp?6J)bX9*gPfh,V8U5s9I2:*#jLQ1pewz-Ztr`E?IZYT{rpm5j*m7Ru/4,<V]-/f*@lKX)hnjtlrDaUm,l=@!gD?WW,RWpM5ukKU0v!dIxuEFv8=0G8CH+8$)Gi7VL-,yS"n|S-,%Tl,Jr:T@Snnbk=&yt17",]B#:oMib=x=o#<ogF?tm*A1>=8Xu4.FyTSw?>Th:p^:H1T@3Dj.^v5VWYE}YtLI45FYXH>JQsPpaB*]*57OYj&TrzwvsZ9~1gBrA@Eu[EG<T!,]o2XTi#3/L#xl:vbLvXZh_H(8e_Q52+3-EgG"pgG-lWg}Gj%9kR
^El-bH`L&#1;b+}][9XbG$ve$*-@i)p"]GTr0A!wIP%%!U$narC;(qM/V!Oa;sC_X?wJKH4:F#3P"V)s2kt*%vLJOb5I;Ajy+g(0dbay*AtLE/s=Wv:)-"IbvwVTy58#D;:eVHT*bG33{q8.N-%EicI6q=x:#
zXMPr[1>-I+Q.enB_UK*3n4R3*X?sUsA;SbDjc12S?NU1_L.$XHT
4K=ihkA6%gbeIQRepd`^_Y,"NeXMQzi/e9o+Dd5z4FMU,npx8h-hQFBa$=AEI^S{v
!#<eA)RdQ9O1
O?%psZs!GJxgJvAYI_+i9MCb;Et1xsx736<bYE]V
I)y.M"b4!GLe/qx;R^Q&^(#F,Y5E-+cN1VuWZ)h,`TseQFoUr*QO>!=[(;DL]Sib)lW[(,7sU2CUAd%V8D_RZe__WfN&cnFvdp<7%*,k
gAID.lUNeF%Ay`?$aZ
F#4w3Hu55n0BX?[{oPdk@Q#Gl
%:"tJo1
>fc7L5:XUW^~W%]@ye6aq2kofN(#7!Z;9A(d+RR9*]NJQR>j2yaTNNiJ0&h:e7y=P`=9MU!o6^3(][@S#oVz%VZ2NgC3x}8+xgfU3(G7,sVor;A>K8_9L&EYDgv@=_Yxut/}@-U$L8&jS&q&D&MBx6H)-<%N-,E`WL!RjD7ix3A2Y[N3;$El9LSR=TjXGj:D#P<x7u]DZ$-L[.KA_qEZDuH+wC';case"sk":return',]^@26OpM*W2%ie#|lh*:q)3$P9$XfWNYa]ZtC{(S-d)<)gOHLlWt9!fMio)$Ty"ZaPC=C7gVs=X|].vXlzRU@W7N*Kx:l*rgnpx;iKcSdF+BRrt#b}uI7
E~LM8Z.QMpr71&mzpMw@U0crJ:QR`pP5m#n/+laaVYA]Sr/gG@9&i><svw,jh8$e_8us$B.v3Nd@ecO=8t?-Boavp%].)iusv
s6^iu9pbqhs|/2vUQAU-?qb=[PWP]EP:@+z(b;y6U1Xpv5A
wG@s:8m|popXbt#
Hb#CV),V@q>eC8>Vy`fo4enp4tT&FIF?v3S
&Z0I,fMuD#:!7#;!."W_PQGd-,`HRoo"O!T2Oc)`IVvRe+8r^Q-I@psn$mql[EWQuXl5n)+m8(fIO`91Cv>).M?:po>8TduP?5Ty<fEUbK3KAeh+A6H[UB]ew[jlM:-](bohjte|hLDIj`MBHl[TB1txxVe!(vp1Ka]B1)L%/HAT&G1do~u.B5LnC>n+hE`lB+Ab=naw3Ynz(D8Lms=:e1mRC7SG=AQkgu0=1$jl&0B}oRVHaKy_oMG^$mrg$K*.gwWER_3HPPp&GM78Qcy"-v
uM#d#!<d.4%bK@g+Fesx/ad
$:AdFNhP]W2qF8$cY@uBIdnYMv`qD==&Wm|(Fa
+e)b+66`E|]P:=<zQzF@#]E4dP]|r/WGTT#u.hDF7S<x@We2NTkaJi:sh-Vdyr&MVNEB&4Xlis=+sR#^k_a8kDyD]a(^Ckm[kg/f5.A!4.9@DLxg
j9TZu=&6j^q1$_nW
[
s$mC+!3L<)TqkD$o*t*3MzmLL+s6tY3hxdaVK!u
"UkXBNq#h$^0gI;C?,>x^
h4?Q(<3+)aV2@~JEZ+v7XM$vyi.Xxi46EW4<q:CsEnBkM3X3ay0GXV9]hcOB]=q]y?h["P_)ihjg1*5P^:?V54)ed##^%!wz]yrTdL=Z[J5t"h;4mwVN=p-zDo.M`[]!2xw,[jC,H#9]@:o@s4!RAshNwu3<*!0NL?VyT%0FVm0wu|a^,wqNVoN:ob)TALW:P~UH1}`E-
t9N8nywG1Jd3Rs!XE7LJosi,u|*GKKfgfz]/u@^%k#>m5Cb#3f<wwMKB&njFVYqfS>g;(+W_GH=90)`"&OADA:!29L`/I>_Pf6Nb>13*hHSN8Bcs7k_CiUL|&*Mnr3g"lK!_4z^{o13/Iv(
E?Leew+La!lGRn*W<tw:rfvZxY_R[lRhA_W}a7l_.xWaYvv]rCb4lkxjQsMc$AEpC#y1v-,AxYw?QTxoaA#0Vz1&@REQQk([$WlC7:^bkohy6[KDssbs5rdK8YOSs5OQBJSk!Ge+yU,~`.WEK!xER;Q?=ZL(E2AR/[mbN"6weIm0AcBw&OAL^}`(4^
<Ak1`%O.+)S:4"<Z<cyI-$uRU^0=&Rb1$
N;B"@&Bkd2"dPx5MRLZDI%De.YCqvNcRWN,-Gmo[wk1/12X$4:.(z)Vj(XGVey^jjI/KuBAr>h"%;+ZoXCvP?!%<lpJ;KoK`hOdW9%3(lRM!+?k./:-j:?-(/h{H[mCx:+q<xj/NkCT0uXl2p-ggT#(.a$:0l#IXmo:87n!5(-%,R"=Vej5]d"8?Y9oH*((s^"^>witVK4pS[wTH`OxmeaSReKq;_C_ca*,h8hc+4y,&0ra!9qku-aKK_@8-{-a<ybmrtiNDR=PK7[y#rcy>cXW6*ebBaJr@cu$$!#Hv^oZ";[~$+?O^o"d>`#Q2pU=6E-2D;)%Dhq70|_=-;7^d`eK#p%$vw>Wb0BSF.BZobIW+*K=`z]#$A5AO~,z))z#Oder)t
aDql,N:rsDddI6
b<#V>tP;`O;q<wp9X2VNOppKo[II^}c;?hgN9yYp.8gB:YQfmo^!,tFl!18.n%*x59?;eefRZR[M#g
-04imjv85.f$~8$7o:_,<+_46(k%F$aofX/6P"HYt)
j,Zz+[3-,o87#OK!"&/97h7KNC
`EAiet6IrKTo/pCiFO|iAF3Obmc7ht+I^^Z%#v&rAt1_-J?25C0F0"_Yc=aK>syoJt&6~pS)n,Nfo,X/L:ME[&2@;U?pt8#
;=]C7L[H]@vgBs"7`*ik&y6D+I*D$mK?M4Hp#"=rb]3I,kK`2s[#MArGZ@=&QMv-A*w=MY(>LL_Z4RzNl)EK9t`qz,YOJ+oj9il&2,IL`I]1E(khK?p.<efSak^1:?zj}7auQ9D]T;gozCvw5,xCy;"ymYT09D*M~0NeR+^D*;uAb:
<H92s[ccL;yHQ.+ASllggLD=jCjyL9?P+0XY0zIUF.TV%,PNW2<Ri[
>f{h-.aD!m#m
,*)8BM+saOVyEnl}^TI*MYE{XTt~lL_$n+vC^bWaOVSD%5P[j"TNA;WXnx#wapw4`Qn2Hl[FSTGSmwm_3"r^>4
[4Hj}"B/Sdk?vYOE%<rN"*2m",^L&:OEq_7As79Ib1t_y5CD/jg38*lVzWA`{bY</-]f[_yTCf{BLt|H.o9Lydz-P/~Q?K,.he3
@m;=TI<Ul7SE$gx4YiYHrkH7X`aBxPK[vr,")`{l!vgL*(Oc#0Ie$Td*Iyi.J>{%/1gVX/I2v*[<rN;hcqHfYs`u?W/3[5O3F?Z/WnC&~)0.g9~j>Y9DQ.f*~&XsXN`FTYW;A0U)F&L:[^C+{Q8ZS,!ti@y9jR!ft`cd7bfo,
)<V+U9;DAYzM~Zq1ltL<&K8NxAY5,W9NTjsaf@<`WA<4n(+9!
,`x.:!]MH_yLuIlg3mqb4:J%q4U=VXT,/<OP=QYplt8UoVG3a;L<>lk/r4F>~gV"{^P#TE0P%D`bkP_YU"?FinsW~_g7:$O;5`P?$8`ip0lr}3BNMk/A/IOK>B85xe43+GrvU;PhY[{%)5wl^8F/3YD(l&Jo.Qx^"*,"jeUip:|%TFUWfj|]1E7&,Fri6-IB`=^&-f!3)22yZ8`(Utw<HsA9y3)]|:1aLIHxRcib>UFB9+3d[@D6>s.#JM![EX#kvU:f9,2qi_%rH:hkzE&sVM
VLKxHe+^+uOp*>?4)7U}AEAdh*$@)
.J@.0sn|OCq}WQsY;@Rw5H>xt0_,L9v4yu@&.dKcRda_!(V(/"uF"{nV@2G52q,sRK4c/AODj%p0u3@bbc=i/+x`>=)6+alhM0uR&63^3jKVAB;CtQ_t4i:`^?!Q0k2mI<d41yY_KG"7b`-p7oRl2
y<wDW~<2Efqv]P]]8Wiik8o2+"`I-%ce!kY*M>dDb*-#PvZ7d^-k,Y>2tsi+B)]m$b4Qbh1)KXEU-tuv9%9WWW+Otnj(=2DZ,!h;d|&v){MnUF-KYv7U&]-OQvnf[S9rkLLq0[K;BqK
wtP!irA!I@kRj-hf^IVJeb&}o*rMEUG"H4kv4?CF?:ygd(';case"sl":return'$Zu;:f|Ap)R?lU#RPly.;O`na6l-{kf_BAC9&IG]F${?SSg7~)mcM#h6Q#dd.u_>s(!wTc7u(Js%XGBa+Gq"6T8E4IUd$9zbLvnEWvYPLk,!24UK3pvG`^?wwez^Tt/R]tCEYo|y3O!rleum17|KCS2!0nN,?Qzl9[z6ziQy3]HQVDL_tN%9-/3LOX6sc5PdDwTB7K0jmV(pRJ!Pk[n`5WKmlvk[SV-]^z)XSMZ
feZ#"65EbZih?Rjb%$8oA#477B5.Al.Ex]#5PioCBxxHSqQ4n?D`wB^P#Ffai^+tAl|Ce,^12,!y.s-K/J:BLWTwqW[/moO>olP3U-8BpIx:<cvW,^e_197?/Ezv>nGxvGJKxkvQ]JA#sG,%F&3%Nd_WA+7Jw1
a2?9]dPh>d4:q<j>%1UxmUSMDoK8HO(FnDZEK
&QHIEbK#ah6V$js;u_"%5#x3PD@5$Dd,H?42:eXsO&=$i*hM>jCqNly"!(b<9NPQ5|rp)$q!*qnT@IP3c@2][j6;qqX!Xi#>%/I|A6%z
IB1[xy*9X@GoD4EvDN5y)X3FF`vuD"bAn^nFC*?Zf[]8ajNWb@p*q-"00&^IMQ~vb6u"[KuWtD0=+m7<^@,A*jP7phnua(.&6(%a+
hvzM_sQ#aH
s{T]Dv,FH@JqOGoW5~0Dr+eLiLve(]WXkMv-Z3S$"3wTtV2+bk8r@+HN%P&o,DG_ArI;y?y-xh=5K;pHD"uS<n
+rRnMKEnX,DJ4dh3ja$WCt|R,T5*fH"y$IdO>ar#{9u7<$XopmIAb,Em9n3F*K>pZ4]p}nIMNC?H?,`BaY?R?CkMea6?n@Tt&B|"MIe+i%(?s!ByVP#coU{)-,(Bs!bo3lk&o;tnv;$S"
s6jcI].P&@lMR*zuuf[4In/:"/:S<lG&dvIKEyP[hQ-HoRyX`$Cwx+E8RYLg%5Eq-0$>{ry"b"$Z:ajwsT=[;;1]/)iw#GX]-e+M$@8
wm18V/[2#+r$.C619Gqh$m0J!jse@(kY`:d.ML/Rlp^OG,>5s.[.fiJaK
CsmlIJ!6]g<dinLZz+~$?"LC`RjtsNBAd?1p2i`S|M=b`k:#Lquj`i_-mE;X..]B!Jg]8>htMB*X&v.t2x"-K>QZ~/ewf(=,$h![:j_ZA+=$~d*lt>PaZw8qNiNBAoT7m3
J|2i:0q.t2t4/@"#rUV=q&7-`+D{_eO-t6!?#<HTrgdpf
#HF&C*[8w_XEWDF0#%SaeKcTLAE+a:iF2b^>3wbcwd_vQnn}cPXvMG]&Z}gef^K(H&i"MR*,nt]fKV+djPWOd/dNS+o)-b;FNq<J6e=^+sBK8Z@q
&1~sv=:jQkJsU@eH~xP#oStq3h5wecd"L$RAhe1fU]T,o?eWjr97~4%B|DiojHx_hLwNHp6`dQ-VY`}u{T*")7M4^_#MH!")ddZ-5>hhO[H;xTleZEgmlbB*c$l#:#Z4`/YHA"t(%T
r8pQxuK{e^%Qu?C&x8x9!jU&Q&AneK(_(TIvD*PsNF,;R!d-F2Cbb&hu2Dk+U|!C.Q@>s}tuEFs,:4:{#PlQuig$uvtC?.qc^tS8h.qJHX6>lj`Xxh["2@#Hf|L@gOd:t3Qgs$3]r[Z^cL&;P?Y3<d4:D+b[M/6lbAC##vXWe`^_;as;HricbiQ.q9#eB~o_Sy#}rqa"GWie.JJ5y|s$pO9xF{RoI$cFVrmi0+V8m7Jv!tXWGNth1ZZ6UN2m&$SWBi5a5vtjt}_iCASD:4ch,Nj_$W5s9"bcxH5"P13g<
Wy*"LjD/L7qw/]5a@syW,p:ddlm+S9X>CN+5`2]58MNoQ[i.IlGV-"0GK#h%eCN6%KfG+,<cV{*)YM%L2b!a>Q4iKry%?
TN^MI3V6
0Wiai(7$BFY[]bG13*v"0Y3lUg!(!gkt">Sm%uIwL(uUVQ1,iphX`3vF<5^Dt$D??."VD0xR@_85Blwv*bz$Wl#FbWo6v3AEOJ=541N`0Bsr42PYJUNOk6_NBwq*rUn>aA`KISNQthN]y7GUlV>$RH>m~P^o1j2KGfbm9@<9~c++%6MN0]/;yJXpgmcA;"|qVgZl]M!yeqHnpMiTw>GC9HoM11`+
SUY;Y<Uc8Kf
*RV)v_Y@%EC~c|Bu@/@;^;9!t09s#l;?cJgfWN":([lBd(m4UF<X
kk^^mTX(_yMdmulC~y5
KFFlV*Xc3M1WyoN]r[oW6l"ZCbh[3dj[ZSHt^mtUwlkRgmv:[]~m/3U]vmz0<BS`gU/1WuP2O5
8&ipCGtwG4DuidYJg<:Y`l>b1eNLx"w/m|*_MScGh+
V!u%HfBh/c?jYbLW[PbL=[zZL@h6=QE:HTiNkX&-74P@][9G}X;0H<m0o8;A]=EQ>a77@:z?A6F>:GBO*_^D4-ZV7T$(MAR-{%-"0=K--ikm%d2a>,B^lrT
!"Zi&r?D:#%rwRE$f8_@bg7hK&
hVk^<!,/`V,9L4"oTcsK.3C,<{?B
ACP_`3L.<1g^<q^oH!lX&kq
:L>MR.,h8f1rlPX07Kh&r`rH#!7xLq"t7L_e=XKVmk~FY$It!l_viS@Q*r]iRVOn]s;EV
/j
;4bw
oa;dML[jP75j]k3!<"3%ElR#mAE[~mCcgVs+%OjIAs3]<9Hoc>cutP@Kpd1qOR%J#_E#(ItLk5M=a_aCSgW;865[8r:;ZA431]cOdC2a~c[Vw`OrWaTPP(
&3:qW)Y}3gjn@"DX1<vntJt!SK6_-S(5vYdddd=+hDU1(saH
.9qO7"bxUaRAPMzTcEhX+x.JcY(LZ8Gs~)`3LxYbB4.s,9>^EJd4ccaT<ckc(h".F?Zg{mkAD?h-ub?Y"H3/<29p0[
1l-pBWS4@dl!G]Z5,+Kwnm&4SMQz&BU>LMiX&1vAaXEA@XR1Z.:Akm<.f
jBsNAU6QV9ju/{T"w8air<eIs!9O!DME+:aw3Qvki-dd=.9r
#bvk=]XMD2HR.Q=J%PmDF2R/lQ<pJC9#U#gXEhPF]l4:PlzT}=DG3R#ix3ZpWGdY9GVI`)A;~hmGz&WJ_7e?pRLO=S>PujJOOJC+PC+AKg,tVdFW1RuZPADYET,SsQYYyAv<!,VZ$])c$4f`+cSm4G}
+e:JIi7yHy_YS`~99SQ
Pl^m?<XvbuUm[$oUd*`#xk90ES*H(P7!(g:)JE]-c5=GkhZjP:b=)E#709{.B_]JVc<.6CVu~,HbWg-a;';case"fi":return')X/;;5LWR/#t?Sk$(bT8_Nlr*Ea"Org@G^$8*i#"z+r"l0@Tz:nj:sDjiGnrSB"X#Dcj&Nr[6=m2sPE18g5Sdf*wS2#xaAKK.x2l2wYY5I^@hofW(Qp-!ELp5RrCbX)k[lp$gB]q.?sEci_yjb}M44d)"]%Vu]UH?EX*x&k<4k{H8srM/:jT[74<?Y*J]v3A,r>>e4I7myJMf9NV]K:KQu*yDI6N%BxXIyHWG62C4Y)plPwqt&z>-/)@+sU0
;X(Mt)%Z%$fD+?G
NFW!)UT:J&]w1|&pR7Fq
3(yH~&k82ea^>Ut%{O]P
R@f{DmMb"B$v5H/WTr_`+^`Gyyhw5G6!Uc/728"N3u;jTY8
uF4uKZBn>J+]TX_oNw7Ao""ZasI~"Bve>]sg5y<)i}9"7)T9A7ym"f3*8K9riND9U]6%n:L;$x%fd1;]<tq!&.SN7Ul5"0NypaoEC!D?$bCc<cRIGU<CwhivER@(Q{SV,Ph]4S-.FlC.B5UPa1I/TKV"pMQf:H,Qm]11OF(dL1]b>gOhBH.%
,`{(,">2y
=y735P]L+fV!usw"HJsp[vux*S+$793>zC#sG"<O,[(a1v+/@ef-lYKbzp2r+wfdN((&/
=S*C<>&3gZ{<EG{GJ&N,*$Kgb@WB>DwUAui*
y#f/&RV*e24w>mC4sw;?J8H:l
M@_WT@(S/L]%12dpiZXw&sTd(tB|`(B.&{Lg3W+lY-L-!^5Km<oxw}Y8mBvk8M@b;abR@hUFSU6=T6@sZkyj<P9LmCu|_Et8:%G[ns`ou-.nj;5j5HB4V^[oWhC^6RNkD%4|HPHlfX+^&f1n4`qO2{%I6H8"dw:+Jqu[R0n-HF,^SZi[F,H{eC`qqfT02:wFLKm^6)Dp.~mTA[prRifwYs*#HWV/V:GI:]_>qjpj]`RZyflkB%h6$Q)lZ>`SpMg1HU5+GI_`5N(UM&XgOk
4StAho;>-oye<@<BVG[sQqc1#K1M]R#RUeW-{XJ_|cF#YfoKxqBV}pUNM!CQju?S^%3<?irir%Ua/t
k(6$X^(iT*xR[/2)J_18PS_u5%MCUFga)Tj=oCJ(01Q4a&0>2A$-!}i8GAbl2nw@9~d6es<Sr+:9,VH<]cu:Ty3Hmsi6Y=wyZSdq4|@_#d^`,|#Pi"l$:_)]XOtea]sJ$PPAIexbG(8}3pe&1rvaFFqT
QiKq"JXo)myN.ie,yO|PE8Cw1p$959NeQ$7&u!}r<R26c,B5=Wa&HE6&66/)]<,c;N)O_-O3:s2IOj0&>N>n6H
h@JX)ECu]?s].&-IULe;h>=b(S2:d<*}K"WG748/adE9?.d+i#_lS)BG6`#!;Pk!pa%Bd>sq#?m{@w(8CyV_v{Xcy?$CH0]evE[2*6$0($Su&-Hm0fz!&_7|wj7sn#)W=pE~85]:rtjz;ul]e&W:R,!Zpue1:7J^N[Yb:t@4)
ye!{T~RY^!$j=7POvHtO3`Oo;zf.FP%B67M)-*<1SGSfVfU|mGE(<52;dfK$Sy=Rd[aMi0$6##f?f#Qr
A@eF+nt`,DQ*_D##mnl^^Y!6D=;nURJ:yHu1zDD/m:$9qxKQh*J!2y$:
LzZup}0gJ2G:m_;WGz6eCo*<WgFw-HFb!Y7Lor;?(ZnK#67%Cz$(]TaaV`r
)o9w<Vd;1.YHo2WYxGS(eanfpc^4E]
;;f8;,|bQ?]OTp|7!ZR"?2Rs[NRhys?w#QwW}5
4c`7L]LNP?hTf*A}q?x
&G_|84@o",tmShOUQ/):?_lV%FEzqRlrZ5#l`5<PB&r`=9e]
BR]NAb~8x#j]8s%oE%@5+N+Oo4zx
h+V8SaS=w`7cN5Lb"HBrb%w>x},2WGC/K`T[F,pl[]RJu&LxL&m)6^i?v~q029F+`eV-i)FoOIW[l(laL?D^$5[$uKgOcMKL0Y=Mh.aq&2
=z&ToKrpTqy%..J5x*seRscZWLd:w4QK|,eqjdw5$$STDT<<At*2<@iPGX,_7m5MrgSb%%20Bh:#zZ
@JwCb.cwHIIaLDg{-D:N#0$bU=f{pRL[<
(yE1.+Y{I7egG+>K,Y9$E6;}]_GZ9]A?:K?)j_Dlu!OPBlj2+<wj=;qs=9&Z<#LD9fRbF~ZmYg><-#vb#sX;y9OsXZ*xMM:L%e?K,S6b/K
@NEyN*Z[2ZtWaa<R3.>!_k!#~$cCI*euF8t`U$!I]yQT!Iy9KXE=/IARm@Ku"f66*HoV0<UC(kOdol<Op*{I6S]:iS|f:d#S/3k&R++ZhrX*W"=p)h0BVR:U&9KDNA8ecatrY>f3V.,yOE]n5CzZTIxRzcqw%X@FG58EIDg-+C_8u]j@kk7fCnY2Vk>j
&V1S80xC8:=V(q=.!j[]sN8=j!%Br|kcA5_"4Ip@
7ODi=OYRuqN&n?UUP=I=(PTUx]Q&{>;NR
B2LxBPZNvrBE=exh5$K8f1UvQ:.5A5FckK5v<:e;3(b<De5XNX}aeXh0^]R!f]DXh^;HOhDS3L?5UL=M81q4FVknX
1IMp]R&b8!<R_<prs%".nd8GS3kl:vzwG]6T4.1W|7{jH/n;t^-A:!G/iuQFkCu$/N9!PlKG`RTh!QhnT-{p$H6i=vv`N@]FoGD)5y5!&DS.YZ/*X(+)T;c^;M<_(#rFgaS)
Z(@J.}qn-ZO#pgPCkKk)uyu`e^<l6.2l^>g"`@Ui`CiUYaMrbZgP>0:WHbV{I[nm2*K|uxhOwp(!;>T~q9h!iN#G<6K3ejrPFXYb/YR"aZZ[0ZE+,T
O5F4gp/]_/6qx>.iPwCbu,BZ`WiLkuueoo
d^ps.lC9nc.L7dbZ[=c3(~.CZvkrGlk{6MV)V0dU6CUXif]EDv[Z%xN[b(Yfd<K42HQ<ZX%}nz3``{:vi{T.>No]WT[<1/aw>`R+OGGaFFkd/)49,}NO0$e7:8Y6=E;PCJrFVYlBDrUuE<iI9kg(+p8A0P)IJOQPX)peI@G,g_Wh8B8Y$1:$U4j>Nr7;nqM&iVblXr8~0y5"j2TN,_0"5jK(c3,!;_;d)xW+B.:c>[rF1e^L>s.o2@C#_;&<ts:w`B7g^[Zc]oT3&nox@)Y9GNbHVrMiiU(77LJBq7i`=#c
ed(VICpu4knQasX}x$p}!d96l<[5N/fz?VL>)m2XHCo1s=H@94+?`j#xR$6ns(k8:W;,pQSo)X3]]n3#mxOL0*PvIUdP+6d$#E';case"sv":return'-Zu;:bT.!/$5$foOgB,o0mL<d(*mY94;2bVb:w[_D,3TjO_9s"oA})Ud{VAF5[VJZW`#VwXSx5mg6<0tp!eUDSnBs(Rc43wmZ*Ms%(~8;7nQbw[@oC(]myur2U[PdsPc+mtQM.(I,4W+hwgUQ:gn5BtXat5_z
+r0BymuG+X<$>E:Zg
a@<4am_3I"t7"Gb#XQeFE<x?0;x0nD[ovW=LPN!kua>w}L;xC>kw`M[L7jm*MslK<?JY%e^.sKn1kk50@c6Rn5|pJ
GU4"3_GZIFXj1@r`4owtNe[[!5,^21ZwbBk9@JL3*-AN/bBn^C?/$S9I(vn#,t=+hh
El;7eP[|=O8F@/Y#f}7k&U#*7{k5uBf_+MxuEH7>gT
WDNe)Bh(_Lv_8h[!(8s;Sqg2qm2K(<~U,x5riJ0:ORaiIV|e^S}GvY#NBkxa)o]sas#W3OOyAYbH@Wv+U@>lEvc(
nB2eV1CcAc^`,CX2HbNHpr:<SE#iL`,c8-?s%CyGx6a"S"=0gAW((~]12hE![pgH]:GB.lJMZ2q)>Emqh%H/0]gQm7_UVl&G;cV$gVu/vWWQAE6ur[D8SJix`|OEu22{>Drul*?*eLp[v
t8n~&pM5O"_@p_:JA;]6X49~(%DOQorUra.0Z<u2O;3jHeCi2ObC9$y#D,vrIhM+Pxgw:/A
X}AaBfe/J)Yw?%B<aA:_m;+)
Mbk>.608`JUeA7^?okG"`n(6ceyB$"|jzKL;T(0y8Mt&kxC*5b:36b**TVVtPZ82JbOqPgZD3*ANl
KY&IM`fFJJx&m,ch&e@mq4z_k8Y!`MpKsQ;9e=h!X"VDmgb=hvK1S(</?p3pwT&Br]|ewsH-[CtY1=2j6+WFa9i:1qb&xaH5x*$>icuh)IBk/UIC%bPs^Im(m4cN-6M-Xga1erV`_uzGh!>
l>A+bov*u<Iavb0K<Mk9m&[hXCTW;"*Ux9XR$prr0CH#k/2DmP5@|cU`>b`7oa/:Px:k6qS><I{]WXVb$ecS8=<G_3+%dWiST
.d*7GJ;,$%0t|L?:1oBuKLd;`N6P&1_s;t7&k2D<Khkr-dJN-"y$xX:,.]zl+y~2R%[8,rp9Mxc,>XYHFryw@q,(vHgCR$M9C2h+J;0F3KDX#^_*Okf"9pL".6P?V
0"bC3];5di"&&b*eO!-ZqZKre/#tRtWx]fGbG@U13-?HbQ4=mn+nEWu*8IH*f[Ow/JGJ]
G&$^f+U;UfNB+8G8Z$Wv(k^+f2Jtj8L2K;uxT;2g4FTo0V1E|en4$iR!k6EV_b$%V!cC*yt!0DuK{N#st/e&>Q09<Xvb/+JcF$OXaMj$KR/qun.xY,^$rPl/Tehj,K8"I.Or@,r71/i_ZXoC{Mg1y0Un)i:r}_Ag:i}n7*h"TlItuoRr27I>?-8sOSeCf-43V$YR*[tRu9O*AGSA|o_UB.]gr1STehe$1j=/+1&+jhB^xKZ"FCh_EU-;4BBF3yq_Lu1f$lWanu6SVl
4Ac`@~tknfy,;eBt+,K}+p@m#$l)TjP:@u1L)AQDp:ExEwf:NkO+bpBJ(15o#Zs3:y];K[:~0hNCgo>3k{>sn]N.=9)
[}T#
O)fT:Y:y7NA,|T@2(ZoJ%%+)e/M/=b0U~lG>|m*eV9,GiN%vYuu@zSS7xl?win}w@q+6]MpS/nxd"-#X4FmT=pERTN+&6Nea6RVb!nF8*=S1)L:.:9L%.K8H@m"=VN/7EhkRT^%lf<I)xpN>%S0[n`
jj02j:yp<d"mPJbz+BU2AZ[_$w<zoU%55yWWAlqkt*B["fkeNBYH_jCmWsoBb}m7XfdH*s4#5Nv6f:@tRG*4v7E<T?
T(YE$p^(cQpL}lJ!)N"n~]me?BW^Wv}Y=>L2$hyr}KLcYyVn@hzNMHkBH%W,-n<wu
6]<;qTw.+#0V2`;6{#Ve&p*T9AtHxI&*zfN
~KQ5IeTOO4?aR;QV6198OqV;O$w
|TRt7=si|)yt,,FDSe7?bE2=~y?C-1dOdCKCM%sWGU8gId~*w0K]CTA!_#Kf4Z39ubj"+!$Cxt60u7V4=UZ&"38`9P5VHjn+F__$ZLo.a=4s5VUc_1g8je62n3C@E"C8}%}-4VEIs@s>9r/XU1x&TXmo9@{0@15km,oR7#]*aoNtQe?]YKz5(L<!KNew:Ums=&e:HG3<mogXz_7c(vtaB$hO~n83<6GR3KwqFVU$boemJnzVs)&bw$S7?#R7N-vYM?[4d#U+a6nqq.O
ZKSrS=,@gaYMz5J`u&"9X8H+ChlKTq:t5-fVdrP
ngT
7g"[_h3vsjafCGzMqvJ!~!#shX@g!Jyi>FUd5v29N/+JQQX;4SsiKm#@?"WeX57@aF4=T,f;SxZ1XCy;a(+1W^fI{4d4YOK-h7Y;lS+:BN~>]u4^pFo5V+TY3#@1vwchA4HJ-L2uAWT>AHDy]*o8:$BbpGxHJBDF>T,$g?gl`^6%#Qovu6t2{[7kCg|91BKeC1/VPsP:<BR>
%dWetyL[e[.2u}Zi5kyTJ3D{tNwoHzbEoF3!1brG;#!Ci$UKKwrk#pZX.q5|hVI{5d/
7<eY,a0}hG
84k%5c(8|Uic
4V`h;n`&)@Kl16@p3vL~)IxX5e.4Y>NX8umy:^A*5tfCD^+Gp:/dJ;)W
NZ,Y[hHwCdf01j`aSu,Dk]L&?9`e+PLTcFVs8?-qw%;W[W#Cze;CZ2F.
$Gug)QeKJSAo=e
^4`GS+31
qR23cX,u?R$}/UDE.m!GSUM"lp$QFd>l3DE[:K,<>DYb4@Ydr9ovoJ;GTp@SfvitRy!u7J5+SPb[*J$(Hs2|XUMB_J:j/}O?KOC>>;)P@KZ^r[tLILfX^6Gb7:TyVCUIBHtw44dg;i@B_P2!;O.Or[d(';case"vi":return'&X/<%]A.7Efn]v(AA^^7S;KsyK
N!QX3;9E$b5h3`YYs(rxYT4)wE71OY*QQU*V(),VR-DS9q4S*Sdkucohx+xvItfdw+XUYYePt~W6B0]`x27?p!nO,/q@V`i,uixkUCXcuKX4t<uWXbrTXVL>dom7_oI>>E64g<Lnck.DqI:_6/KKG|fh[=uwL&_t7]`wl>6__Fr-Dfcw.33i;laBPi<>Eo6f,0k!U8Rx$W!w0xx#NtkIDtX[Zobxsbo_Tu=LfyTs&6lfn#*Y:F1jk*8qU!rW]It=-
s?GjTfkJRhd?Jdec/G^IN"xU_B.2)FY{PkoWUt=8,<SF=)GLea4m#}UTXwaV%z4;T`,lCS`_g+$O4CT
Y!N(b=2k62m|3M)D]<!`x_$
5B!GEm=_L"_h3mP1jHd{cpwH".%H;gFKHzt}#BlMq^XR[V4yMj^UQfx;:?rcH3[83Ju-+Aox=P+:Kr?vW4y!l?uLx?UXy?#|u.W{t&M|!8pxq
[b+/7p
{0KoI6.LH/P!
(o+:snXIpy$meGSLe6aofQ!Id~Td)C,5/9HgnCAhUW($=zY1KU<+
.cvZ7pS?*xJ5u7B`vRG4EqDudqm,X>poTFSiJp~7b]i7dNFBuufMs.0v5,zyOW:Axxc,ft"0O8#j7_uP9:r4u]bdgR<WGV1I5NPBPX29:Vv>o6#gl[dC|L?Kb8V4!^Lb@UYv5o2<6wHyHZl`T4/^B`,Qz)(b~]e>$.`UAguGo^^UD(/v8Ipqu5R[d(viYKBi=&"E:?dm2h|E439`VFOBxHCxvE3-:I[ZnA{@8Ir;wt[Io3A,.&:r2)Ttj<6hi7R:Zxh#/BM*/MafF-1,Vtd2t%JwmUC43Jn1IsX#h>LQiw!`x:nZ:rX*Y;D&#4[EHbgW`?qeQL24d-<Y/UoR_dbi
B2CO!>_vS%WwTb-68"1zbmt<d[$KXI!X3]UgXJ^G4:WcX[=|j6hH.N_wL!o=-3AO6qMMFN4o*>&TfvQd?Q=)6GwA&ekF8!MJaHOXDFlc[7MHCA.wEdassQ.H-7l-xWKGT~2eD^!-c3Xx]8X9c`.0eFeMo12][^J6b@^esx*Ft#aiu$kN*7i(!nnH7-O)aCw;_`7{&WHxYEj=%4x`6Lm:Cn,c2Gi>V0dl8893TyIT1`%c@nW[iXp$i#vlZHo[%8e<jARwZ:*n53?o*gKYy^lq6;!p=ZiaHusPX>`QYc"f#Q*,"0%J
jv2Ti@]=fX2qHN<AwTv/[UjOSO=9)?OpxCPilLWBBAGMEm75),ao?Dgj(tk_Amd][*+vl:|$(.6Nlk=;{rqbrRvm$YpY@Vw)HW{fM;SP@O.
BHN(8h8$UN]i2
y/B";)-k^N$SG>;1c*26rHM;e98(WJ9c>>$`<.afCPEG:?4t6g9A=-B
@eJBCL2OJ&Y<:ctbWq~A~+x^Tk^fi@Agb.R"DE-,Q!Jb6bGa5B<?IwU`kjXJ6e
8f@jhF=~3]*?@$=z*5A)/sR7rItSY6cS%.*dsXEnD.`{pUeBsU+l7YlnRRI}0N<3D@5gd?8-:9T`.H/1jelnmJKWr>AIq,h<a:h`I!P_3CEmxAR8R|wf
0ojLl[%hdEc:;cbm*7DB&S^xgTH3e.P+BuOpk_GlL0Uc!1VF"bmQ`VCE#^XAN=?1J3T(BQB
n2e>/(H!iJ!mRSKrVGh!l;@Bu_2d_ba($>+)&Af;4=&e}(BoIU^$^9E$l`L/n]f=1yKBK[^
o$)E~_Ev,UD5/cGK>9kSz,v>])?!D[j":ihS4CO0<cSd9B&p%-./?bO;xxr.K%?kq$IW_x4h4xvo9?o3a[]$p4oBk6?oaj=wDm%
s!w%HT!bQXe2
:9>=d(U4;*<oI`T6&#p9L4)Qn#E".?B.t{Q{0ld&0]qy%@.PsTe%d:Z`ekvL/wY]Z$Sm[)7CWMi5[&ieUR-xuXDv[NNmpm-l[I3yGshyx(?-V?p3-*e+Dp-|4H`*T[&DA-OJO;=CP#aX%SC%u_W[E!L)2k%)%"*ffx91KIx9%t$aR6TM#?A$^yxpdy/Z3e^D+g$U>V*d0pYklP7|Y`<1pO_ATO[#y/2&^{:5AhcH)LbgbFM8$YEhCK.gAW7`>b:SP~(ns]C,WQU)3&V>:FT}B/qA]~anQ>-$wg`wgeAl:_,-!-%&<%81Ch#KP^&I",Y&/5F[WiOw/,nHVQS7aNwcr&rFT~y`+TL4_&V?+STO
t():fjgvOQ$RQEqcNE|Zz>O2~;N7d.|L<O~9koCSwH"4X#2o*-^H3.N42D*.yK{F!9;1Q%:CHS-U6+$%D5$Ly&eWm>{@Q<T(OeKC8OkwzfpfPpzTD!Y=QummlC|:ux1,7pO52Lt^;J.)Zq4w&CIvgM!doB}m3+*!{;=c~>vyiVgm@!C:84$B-BIr:yMP~6!0%P$3U/yReB-EZtzGY0IL>nq(4M
xeH/xZ%`RV1<^aJljz*dyo4DNA4gE<R%#rydUSCWQV9c"-FG$CE-]5fLr-3hF)j.%?ZQikAi_YSYq+I/p%Sj0HQKM^%uY3*nFu43gr+i7
yS;KoWE9tn0.%vta_#XG,tO)REXsv6gCICUA2vfGuGZoSK,Fb*:}#<){Hl`LmUV}Iyv0ir
@0x%m*"Y!#H3E)m>6?%%kE2d{6pu9?cGiU(lmU+Mk6DLbU?#vr-2T$@)hRq>TNDlLLVZm-8nr!$!H>
xG=4aYg|^g2n5JBLv3D`B,l{XO<8?#LVbfXLS*bb=++Qra*Lv#TTFnj.vZeqv_QFo3n<x-
BX
lRn=>kL[D"+$hts<gpqx[RKgvkk,jtpo&b^l`N^=IZjtDP%g]h76BN7->OGUyjRo.
x5jHBC,7ZmU5@kgjD$-[]MfYJV.n&In(DBJ~YD@^Vpyln:$bVG)c=RwV"oSl+!Uj3Z:#8tK@AdKH2-1(AYF+H~PChgE"2u^%EW2NpaZ!<wKBBef,A(c@!,>G"
tOj!X2s5sEWUsnIL;o:M;lY=6LTPTN;*&Qt.(3k!l;8Vt;kvcAGN`qrcvTZWo<Kl]^^:D%DI_eJ=.OhP:*hDP6Me>R7!Z(pUSHSC$<FyjWb;S9^qa92p*$6xl7=WX,0s^asgV<M=yJ1PB1Q6&KhVFk^6#>r2t]j7;"tS-Fx.jyh9X"c`rP?2dcP4kgx)imp$)$Vc(^vf
:J}S3>c;rX[pf7x*|:~x/MifStGEUC[q*@Pucr`oMt-
?1T4|o(1T03s[lEBekxt3i"vR2.YKD=#<(A0~l~Zdj{F-#wxc5x14F_A6,"
{.urIgPP|@5iXkLr86*_`Eu9aKl(1%AWZA^""';case"tr":return'(UF@iaMD9?T=Br&!)PmYg&@GGBE"Bk&.F2uSW:j3ZJB2DAv]6qouJaNqtvg1eW2_UhiivyQa}rly+]J.K]p7,
e1nJ}n4Glf?NA6xXeeH38B@l
h6ZrU:6lcM@v0U!
dsLlK(>VA/+f0f>r5E_rb1w{.O7!dlz!V}kcuS=`,N0uH@TOI7piA-Og!00o.5;j?oeay{RYEX>]^BZWky2d03B$sr9b*eAqgUm^5$"U!M%Afzob*7SHH(tEe"HA?|3^16gl^+yNZ9UHOTF<>RG>MBgIbs$ncUnCo()"3rFfa!,t?%:{
&3o6-ys52c#SD0>U>IXVk=}:+tOrCur53xfb@oy(Za:EF;0ukDY&s!V.{W~w77-wa:oMMoPIu1%%0C,u~sqDX_FsFCDse@cS7M-btV.NQ+my9:&;X
?;(A1N[NOt#IWnswLF~Q|2vGM;ihwe84r1]IF0a82erRjw[eZ8Sv^
ydD7@u)ck`4uX89v)^d&4FdaKp?2X;{`*P-v)@0tf=RJzl;r!_2uqZtU4Q1x!rSfT`FkT2dJ0=u5kI%enRP4POAa:&ScKE:gCLdsiA9c?[
`nkE$,o9iG5AN`b4qVD!%C,L#}!u_XjD-n>s
V[:J}E>v.>8tZ)bat<]tsa#(!by1h;N!d:N1FhmfP_"Izp8Sy@P
6P9-ewAQ$s"GnG<Lj/GC1WRHaL,yD5>cu/wgqQZnd6ph~ZHhs?%tRF9ouv_DDlMB,#&Ln*q]`ZiR7*oh]YOU&T|[.N7B01,Yrn2%Miv7bI89`ANd(o3!t&f[2(q.?,v)@uXi1#eU`=vW"g
Fg89yK%iVEh{-$3jhCtvTqbdZz"H,Nnh8e3nZ&fnQD*SZgy?1uFFiCqu
3VQDd>#fB;7qJ*2h<8D,CG|Zr3zcs.R"8(EPM03o{!.d]:Yi]fj7P]KYCLihV"/y.Mm3LOuPr,<i#3fYkOB1
GYX2y=49
pQ{P]"7>V$e5{TOFhMJt7cJLkx
r{2,f)z&?@NGwTh)o(vLx94-X$AWSZ7`c94d>LuhmG`x=obHR?daB>rz7^-Nx)N8$"]2+aHnW=;$"m#ly:12XB(c2&SQJ7aCFGS7A,>6,S6$[}!aP+OAZB3#D^V8%Sf=m5.FRB(UFfRYa$2LX2Z7rOdIq}yI0Ze&
y[^1]^=quBp:vM2Z(IFQ.pe_y&fF@VlcLrYg-UcNoudv:v
<sHS!}*Vo%0kQ/2Tq3D_^uZgo0tyG-bP?^^#Dq41l`2Xu$*2qC>"Du;:O8f3
YGY0.?Ttq#rxDC4oOm3DT"4U>0{sUVQ<$F-np"
q5a[8.0X#b6dPs9l.<<rF:(EKOG!d+wk>WqTng2d3b"Q,zW8+A7L^+o8UWaxfK._<("C;+y_UB`|3!tkm9wgkgIQ_Px~xzo6o2n7u1W*dKkJ0D4lDP:>"e<=S%JrKul.Fe
/C&JH+ib$1ifp&>+-MRxL-38u2ov&`n8B9u5<F]#"8O7;UIU4K@sm3G#q`|EuD%V5oc%@y(0^7IfO.TDsPcn}K]`}
29WkWlxBD;{d7"%EN=Y0?C,7hd(LI%(kl>>"<;{/bQzfkB<g_CY]s)0pP(y:?oE=L"$QQB1eLjN
_$i3V=-OqTi&brAayNh]PFj5KTkTc&u4
M]h=aj5$laJRlLFU*DBOVKUvmv#/8qT##%>Eeeor`KxD<hI6fEmMt<^X%y__^c
=m<fz;;/wGrFS!(PBRqTzi)9X52lS5Ca6&69((tJUZG#
BGpM-X?@ICyG_UdLBM@HNf*))KuP[7TBjekwihC*sD@;1?=Kc%+aG^4MN9(%adq3+v-ZRaes[u7c
K(R"?n=^b(A>x0L
-/9>|m{6UOP1l0@co"[Pr.GK.x@=KPRx!h:W/MHNR,;+_V$c;7jQkC<E6uFmXW?(jCq^qRodC9gyfJCY_&3n7V~s+jAIJPMQc%]QW1)?]7G8l`Q/k&p1?Qq-FPNca3c.r/}`bp.E!Y?N_IRo0&PHlRn)#JcQ]VJp3mO;tI)iD5yEV%<"d9bt~]KIVR7iwM/v32~vUYwxl[x0,!ca#,7j,q(4&&}Lu*M39lbsT."g9S>VDZWF*ydr>!HZJ/{@2U&*A?WjUV7u5KmY5?rT.7c?f)`ud9AG;bg,HF.p|AfX~K!mS=I4K92QxQ~dZbuj;pF;ysyX-h7gEvb:%>k4*36,{C.d{#!J(&d:dc2>^jz<U/qB&[LN2<`R:G|c1hs!WdEg)vTT|H8YFV-SjNyZaKw?s0c@AYS$M=Zx#io;gTPP>seEP/=hM?}$g6?U[_Q5qZ+S[+(D78SX8,zq;4[xfc&LK>;RSvGcq2_7!Gd*1C0Pe!NQc.smGeO-tlE7scyM@!"%ck.v,SdQGyQ4i+T6lUKRg.:
.4Y5|u==AZ,p.YDe0G<#BFtL<9uomK"]P!kwb8?"BXtbxd{V?N`?f!v
3+4x!*m9@RSjrCMD@:M"c+D9RV/=S*ao)(PmpT
,RW4d,Kv.IdcLe=oM`G_tPsWW|6p;2N%+Td~815"*:XD`tD~:4,nj1o5e-)bFE$j$X"nr7T}Ox>4Oa^]1"4yV_BZRQ@#I$BvJh$%%x[qB&BV<Ko])6yMV2qW4e(RKRRIe8?ZMFZ]g/P6RQa
iyNhYd$g?1v!*JEBX47!IMTh/?.`8>p~tJ9lJb@KRTT+FV=Xx&
mC)AIn<&(Ot/PQDxEG:KGh>biXK_d?S-O>R4P,ix1(Xy^rk6egh;~.$=$)ssRwC@2#!AdL>o%7zGuyoH~*J%Ah1bne/LSP(k18OC/]gAf,K?c<FHiZ#@~Jm+q:#/*bn?CI7(Tm=Qeau[M:q@!]V.Y
HN8:fu{i{Aw*BCVTip!Fyv)&#6o(y=/@t+,ejePX9D6tpyM2eh"ie=$(t:!xK5$!JI{45(s$<-t_OQ(vi8oT`ho_!N+MM!lcW40(n8aLR6h1AFmu!J[-yjdO)`6v2it?m-Ri:-k%SB.`aOkOJ;8TDD_;&q}-zE=jD"!E
clZERpf0k3_sf>[v6}UM!R.MEIazICqv=v3)*N*7XgCr@)=$;7Gi3h,?M/)H;Y._(ZJbu6A@rq1z8yRGFUUh3=SSX4TI)RO*BYA6F@E9":+S6*!ue|E4>uO7<:9MM)N[`,4ZflZf<Wa;ne"4r*2"xmC#NT<i]A@{y7M`[?d),l&[p!rc2iSs1JI[s,4&:Fm=G8=RY01]Q!vg@<)*,~&n';case"bg":return'$ev@qf{p=(p)js?])U@p}>XZHK^?H))tX"/0SQom%WV:~TZQssQTXUZ9U=W_(2iI"pU6)vXxci1@<&{Jd9g<MXA70]2MiM8uhb-/FZuvs4Nr^yZyZ<kK!_~M-4mt3J8JukR,vU1sn]/
KD{uhm&auwXGSJO`I&&02H/8x`19~DRygwup2c97~nwdJRS*ucZESi*.Y4oB&b:^w?
b]:VL~jciFqmPjhV)C-wg:F&>tl/Kmp?a)2/)UB=*vp/^[1TD4l9SzdHNr&lOo08g&fvUk1!KrI<uee.Y~n;V@LNgXs3;gU8(!fU5ffb?Xv7y.KD=@#(]k0Da$UC
;blP!Y9FCoyHb3#E$<-,v,k2xjuy^7(R{aqas#Y4U[_#]M@e9#3Qw80DLX:WS4i?2]{f7(3bClkW/N27;EKVPqH>$r@tz5,7mmqN|oH)nC+S0!G7:g{7n=G#Aad3~0AJtqcm3-sfm<v(M9Y&H-[hi4}336>"b_:%w:F2bCL)--&va;v[P
NU?lR$pao(H=!LCLJhEdatc?45Mi<e6pjM59-&OGiRx_x-3Il"5K/>2j9SNtBiO[$ZXgIN|ZR4|)^@I&7yoD
4qJ[tKa/5Ao#2>T+VSN9b!iKtK%?9&B`]<42O`EAbsR"Ba@L(y*m]"$I[$uaD5/Om@hLmNUQ.~=sDaGlJle+dtZ}[qrw-m<1JbUPY~vzR9X^[6fJdcA<Ou!c?6fZU&H{QrQM&hEWPU`yYrje01s=L]p]aTCmpgB/+AbBoT5kuku_Q+w@*I5FB>i~mR5q
~qe1{>z[9,IwRXuGl;Q"2.o=w/z!b(=D%_f/zH#9anlC&fLgt=FRnQ`v,r:&!bKGuf3:|_O3}&Xwhj.T,><!mT4#n<yV@,m/m`Ljmq
9<oZs5c:R~=$+S<FnW).-m(g4mh`ON::Jl=IK_f,"g
Ci
5jB/W8d5ejlHdNV.:-hL[`d4F<B$+S[k;)3fN~.+h%1pO^4KifM[%}BQ-bEm8nhj0mo*0GMzyA,r^>S=WaZ8:ZR)m4v8!tZPN{g?Xj.LrnVDef6m@mHQjdl3SjD.vfyqR=8OMlq@DXm~!zMV05J+8]mr5h&nQ4DzEq1L*r;oh`WUkdl+VdHvVdOeZ{Rg]7KfSukx(A,;k9y#Arj#n+,E$T$.K)d6%(G9MLiZ-N/N;N<1f$R?0UeS3>p,M9YGgU54+#j1tuD/Jasu`7!MwRT#wD<c7b<|mi@T*unHF2q>Fpp+SX0A94Em%".xrUh$Q/tWCMVICIR]-#rzE)tCT@(y[J-[m=;&>}Q6`4mr5b2}ab4Ne^732AQL=_?]-Xp10~@-D(TFL[o*a{6Ks4/PcNPi^08)`Z/B!94]U7dajSvG7JGC/.VTcek<tby+(WJgU`>0d:RqN`F7#<a_E4yPxJ
w.]:M>&[gu^c<;AMlVOn%`!9Md,uRKd5x<8pWa8wcKR`n]%-Xu[P3-UNEI]Xq#J$tl,.R+9Y30q4nnZ23DlgBYRd}E~[8S$`PT[8A5py/={#b`Jh<_Zk&5d`-piB-@;a66{5V"@UM:rTAkO;*w=W.P:e8AP=Jy{vYy)RY&fmZcf+LOBE2QTAU.)g-)B<?*~D0;zxU5#+Pi$l2U"qe+VHu9D##)RLlTQ6>A-u1)f&p-fsuV%!DR85e@Q1h=<[4dpK<rc*z[D=do(-,[{mpGVV9.E$fM~ZU1jQT*?S[P!@xFnH=$d[<0#v39j^BK>F_bt`G-~!6b4D3]x:BxrC`6
Rr.IdJ:bSyT!#i(O:w?:uk=O#O<g8+esE3o"IWF{*4k#2GaP2dGi)Env7.pwJz@Uh(YSy7jn,fyzr7]*4}GY>$T%Q%gI4rxxl"BX_E?5n-Q.$@[q6K,%L53i[>giGUrXj;/y)USyk.Hq
HqeguJ@6NDZw48pigscB4"^wUhG!m@N!.]
-$=?YR8|gWG~T:./O9"ZkrD}s:#$2{2#;Y
ZD6lu_]<tC/]ibsx2k]6j"tflY7
h(y<U@TDbV<7^x}UD-]VFS~@%g@<#PuKgARXAx`j{M0Qlm[BMsQ8,n_F|`Hwl?.vsu0_|d)#Q
>?4xtw?UJKo7e#OL{3Qm
0.3uIB<]F.=IV83F&vIzV+O|fEVFc!e>U>;|X;RztyMy8;&{m&ayc/ZZk&8;2|#j/vuu`^GgZF/IpP@9&:*%HK[;Ai0uJ0
pJBS1&G#GiMnE<,+I#[H+(;.h";=I(uv9Z`0S=N;]p1Lqosl;6(W~G:J
9+Qa"g9b&z@S2n/uxy)NuRUdJqw9>+@zY?KVbfjIGl;j%R%R_(!]H+>:*62:pfY
ipmK,]E+jFa[r-qw.SivEx/WWUVE<%;{IGJxYNES4etE<XDxb&OJfRB<H?X5XHctvL%dC%lS]TR5B+S.aie}S$0=k}=DU)pAOO,J4h0|0#u"2UP~sV2nOmgHN3er&&w
C_A7Jia~&>P{^6SvyEQP)7Pm.,u~.TJ&X[/3yQBCvC#W3$PsU:&!]48wb!RTnB>F1_VXoZyH!Np2!bGVlrq/)>t{>mlUj6>>=f]S0T/^
(RSv,G00N9%tBfuBZ/>n0O[J)BVPQsYHz_>Cy?H`/5w"!R#qqe*f5E;7%YxR$-|MWuP87I>djPA"lLVSp/8LC6rGdSpCJ_peF#8%.ycL2w!$0^Gr4SVOh0B0<A/"Nac><uU8vd@cVdW%G*]he#UhDsVo`aluEkZ_Ckv[oJtNmVQ!>Y/Eraa?`?w"S7Wy@C#N]6^n1Md5#0PyRT9l@qB%TFBw)0l$AQ.`f^oc^>xSFA{@!.$Ahsf@
Z9V5Tp=(QgNIg*S@TBa{?!e@=v-lS5W!dw_s?",hgc,"B*y>L(e]bTJ|s(:ND$-"C{STLto
ZC%z
.)GO9-<nH9p7APJ`I5?:X>$?Ym2h|c%9vB0RM^Qs5a
qN8;$fhXk$5b!?qA_GL#d!a"VkucQlce-{H#Ieh*q.[3Dwl_Iqq[#?0{>!OwY61,SUMvv
v^@//fJB6D.ArA>#B)Wdb!2W)!maPH=oWhFh!-7@vp)8cgq;tbXEfrk?rc5OUz70jINOfX(<x6pTY|?m)lC4mlGDgFDXmt&0l9Puc^8Rb|4"_CKu`faXAJVjOQW9H<$25;)B8YpuqBg/e;r#B0-C<{QiZpiAcUXZE<]CTvg?`D8M%KFhO2xiu`CH[Yd6:dpPAa)Wy~bt5>Mn:XIj>`L9U=L*#_]Ii_<j/@)GxdE^5NtN;-2`<POVU;%=p=<P3}fE%K,^>whxe>v%?Z0keuPEj[WeSf%#2x(`.OAS3o
x>dc*cr)NWCwXvogW:).L,GP8g-*RXWJCieH*CWA{t^Zd(^ihVy3<sTQ5bpO>C-+l8^^Y=-+n8i*Z&Y4SI-NyJL9@PRY+O&o00%v`vsG9utvohekT.cx,)A_rfOU}Q(k`i~4Pl$0*jdEGdwaE.Ml,U/sO2~b+F#!}un3}MPRmS.NYp>AEAh2ew5f4(c(Plfq4p3b`N7=>-oLRDPb{.%tm2}OPCm<-"qj7^J$jBt$|,50)iX;B5;%z@X]?j:Y]_]4a>q5Eq/JN^X6u>:lF+<8jy@m-P#jq,?<RbpAfwrk#w*td5^d>?<EkjBd1jCSAO9HaTK.f5muW/4y`TgZ^Bb$?,KepwgO4QD1~"Pq8UFRqI2^Ob]<o;QtNRIK0nIHXCvqdOD?^yOjQW_Z-/(n^]qsv;Ii^t8;fQN1!.G*31AP{US%G[.ptS;ks(Jl)sO,3w#7ssR2f>0L0!5]1*Z59,+KBAuVv"%$2#DJz]?)KQdc1tSc>V5J_rUTZ?T[-qarNdaLr%MA_>_0;.BUB2<H9jMV$TZ=1s!
?nt(2Vi`XTq!b]%EWCVG[qrsiJjM;101Mkb^pN_p!M)^@-u!sm.cp#Rca,@?d#87;vMo-';case"el":return'#h_@iamD9)Ql/aO)HK5N)._#01K9Idgu~"A-+`uFg"ZZJQO1C:l)0[58EsrV>k
.>qHf}h7X_yOJCrin|spH0YDe,!Y]E5.uO@&uor<rptSn?LTi2b7AAK4#+rvK3PYm
iF_;yNIGx"am=.V1d}bYD6n>]rHBlO%q3IAGK"RAUms~NA3cZ=r=p}uLH9=NLS0-p8nA#kbTy@<Gsvma2-
[wnvsq2rF,m`9EY6EH"n6MvGUC0@sJ>^hD,i^/XDVvvFrt:u<`WpMl/=?_aY)v-M=w.TZ/W)R33FRo6]a`],t$pTzx9jn4,j<i;YS?u>0pKEZ?PyeXC02:YnW4RZys@"L&gh.-KLMEN*d3N!iQ^.U*T5j[u^t"Y<w7;NbS6*Cq5:~<L[D0}$$Oti~@jY<%Dp;[1V^H9jP"RtK!lIr
u?o<:vkx/%JVi@%t$o<wy[6=OAWI:R)k]m^g[G>kbiG``;7@.
X`2_wON>.J<J<P_^>`6ah+vVyv9QKR$Y19d1sr<o{*-rFAqrgT}O+K#P~:QW6"x&n`Uo,akb5r_Sph^A$)gj=KYY$%^8a?OvGq/6E:m^b/!?BeH1[Fs,zt|.KWd:27{YpTv1f1S*,CD<>vTvax{6"IMq";
<}*/?
nu&F`.NV-YD+dSQT:M35LF;Y8@gdf=tD%MQk4ePSoIVZuCkpL@Lv]BqNQ2HyfDk6N
SWdbpFp6xY&fl@S@cGA^:tTcu%oZ4[duF%t
#fI:Yb3eUwZ>*8Al"a
bF&N>9*^A
>,xkchEc7Tk!Q62XE7b.tReHmJM[>j&CNZ7f#/x(BQqAVz(GA8)MVp_)N6%Gz^5ln&Gn+sS;Y3`s!--+2[Qf
)<01=y86
B<yjGtYi&VBiz@fjV+8eTKAo+#d5m;utjv84BCC"+iesy_=(xc>2EiRpu(Ns+V?M|_ghMH/b_TS,OaEjnn35+&5"C7FPC2=Q<8nq8NFN/dPL`[AZ~7^gzQ
k&GeK"mz@SZJ"v"
Jmqetz>nD+asCfyPMhMe[,8Wk.*]R=6HN3FgbkxLm9p65vv2se#
QFg/7Vc5/55a_J/Q7n[@6@8xG<C0xN-,P<*"F{#gP9u{PB%bIFg9-uTXHag~F_=X(I9~TzaU=pP7E.Xn#N!&axKby/du=+W!eV7B@X;qjO)*8$.xtbFXh|,Yy62q9y?>$xHp
K.2mG%RN-lK8m)<+0!vE%/M"]/t)<
L35(XB,3[.@V)^j*EM+oF
_V.H+yvX#c4/3P9K6@PU27C9&[uVon.a:i4:Qkph29;%>IoE}b<Va-wQH1>xh#]1ZmH#-ayn4IdP0&NZBor&.2.Sd[PB_M;B-J;IHRX**[(V_j/rv-0_r_6-1Y,WaZ{$@25JOxZ)hG~J8rTY/xd"8&Ini>"0/Qp9fr^cnN/qYG8:H%QVDJ`od!9f;3b9U(jscqde#8wf!Y;hhsGecerVl/~1}M-QiOe#"]R
dhl)m9N#DtM/n/4SWF!-Nqv1?nf>Map>HiX"@f,x6pu<fmor!3XK]@Bx&2]2N=m:q,Y4mHZFusbJe/StOVvWO0P-1juC,N^V4_]C13lY1Suu,+J"Hc71#$?/@;bd9GFxqlZt!kGk79*.,lFBa"FmuVjfP/_bM]b*u>1Z"7##?%f!H%4.KqN+9^7[3k`U*@iYLOQLV2+*Ir3:fE&&?_iIb@ZKp`B3Yp14_x%e$(=Q*Ki/fi?;%J{NCY,NR0JM]N$HDRxRq:_83ilR?+I*t-|x]h.w~xoM,OFIU+{cT%y]DR~JTR=WG7R3[wo,UE&O(K?q>r`&q?m&4GChm[D6"8v3w;;Gt]nm(C=JnUn$qr5R<4HhKL4M!BuCZdzVHDK^aquGLlRSX<0-vArjrdnN}oHQxaFef7t7)GH:&iDU2d!jAga%$t|+kP#S8SruU^d")r<y1mg%Jw88##%YK+t+iRvvd"6S=l%$-@7*ADkkBFz;DSWFL3(u)GUVaO5;OsR*m1X1$4*hw7rdQ&I%J%33
Lj;{3SG<=ca"l05mN>Z0rzC.-ddRaC?M!uTPO:%]_;A/o^sB5]#*9@v5+4ROc0U0/@g`tW/n#z%{n,LkUwFz-NpGEvK1V<9N.McrLMA=4L=CK1ag>0XeOSl*?&5H;Lx`VO]>Uam*3jFt;_.gb=3vHBWcN(1Q((%Z"~H>(8mP
t.;S->~5}e3@nI[]~lJ==(]=/-73-ZjkK.#t]cU5]M57ZBxIl%f3RG14MuZ%{Umy2?c0wNy43#?%88dUPPmD1d#!?8+aUjX;KVA0!:ydza^[(#yisqttn[tH~82,m$>Tc8C:}8L_pu]G%Y;e|DI0)2xPeMwR&hRCCevQBk](KMnS<?vTUaHEmw[kVVHS?K%y-fe(07N1(F0EC=oc;-y_L3;hnDff3^(C<uA8mH*Q@DyVO2XRG7Pas@Dg7
<j
B)ILPx^0"m#b`e#kDukl^kga79[Iq^kX][GtQ*ev])WC&QWTN;I8$^tPIJSq0CQj$xY6O+kj3&V7UAPn?YPfJDs&gV!;d9yn>RjmnO!PAv.B2NvnMvcs<6vIq7xGx_yV#ryFCkLUVg/E,x8N.Nz!wEFa0Rp5+9ar4~X~#34d3gaaG]HnR;6RjG6B<:kM;t_j.DYStXxSYR+w_(5M"r:P=07[527J265Xa2</wwVD9>5&^{>vnJ)MoD`*vPW@jIg*@B8+d</i,eDwg.iI"-`d/;1_Ev,XkRUfWQBciN.-]do8YQPK>,;iFh4Ey+hmbv;Sn`ea&3Q+c/^rZcZ!w`h6h0!R/<Q{V|^Xj:+5_dCb0:wUg
kICOBx?T]<QmNg4|j[]LJDl2Z&eyT*B<Tr0m]o.89<
g@_6TlHZ}V<opJKym4ut/.r8QB<V$Bimw4I>75XEV1RU8Fh#{)a^8>1NWAIVft|BV>>,EhzUa<XyI3J-(ee<lp*BKG,WlIliM1w4.aLn&p_]Yp_U"gUjp)&./V5&4UTI@UIRIUpku/v%vx1:N8A3X`KMRL]UYd.%w`c;A7uIIvPv@P!$ey15^0!I-&rREpy7hQs1mjw*ateoS8]b{w!R%`a[;^?bLB%0K1ATm``tKGJ=p40(EeXk"=Xog=_Vi
mXl,SuXUa_(/DIA0ykBYi:%4rB*Ch+aVaa(!=m_
J^`r$I8+rg4PB+$6o%GEcI5BJsQpSq"XD`L5.`d8GX-
z)[5yBP3M^V(MC(f1ED5qi:7`z!@aE8JY8o%7imdv
,h[&kg)G8cUB?OoOTnYJ,*"S("drG=k7>t5lx%jUrDeRH^pfWt6"eYC`t^H>G-;dEW_GtT9gh3IAR/:Kw8L@+6K(>@B"vpBrRMZhmH!BmAjF|R@w{_fNrE]r2q)0`IY4)=BDBATaofBpng|1<-4us`&x$SOlF?b$t<iOZj]=~RU>Kf9fA^AhafC&sNM
~c*3A1->]Rpa}eZc?_v6u.vt"E1jP`S-X;IhZA!F?xiItg4*w=|l7GRVdTUwM=^VD,hOU$^WZH!M7R=B0WwYjW6/kvz@bR#[kD7d#Mnp|1$$D[GJ^DULJV0V!b
^0*7s4v{WQ[mO7Y|lr6FcRvR^iiivzVCCXjhWgB/I(_X:D,H1!06o#80g}!4lCH
u#y(GHYY(%Xu4JGIW]bxMzp.m2TU(oe5j(79
Z/&RULLE?Ql.w@[ySa!i1*~LKUwfV=As|1hl7+JS$WR;:UYwc;,gPPvLS@Fq!qhU#*N+W-i6x.D_KRO/$XT%xQk:CpcW@.-PE+YQ-D3da<?BM8)&}F{I.r|E(6)m`?Pq80aE+4!T+@+WXS!3k]bD(kl
&bAkeCc!J*^knhkbp9F1SD+mgD~
oueM!a#^|grR4]v6=JugbSHH`]Ud&&L<K=DMy]yIU1jPG;4nc^4+{u^@ikTFMeYW1=xFt]0SM+-[Cpzk6`^]f&tbI[mQ@M%0@
po$3(<$3_!h],QGt;M)>m6FLYR`UMZh![
0&#WF_m.
;pc4SfmPqeiMORhN2*IB2Ec4K;E&>g9CWQpkKin3y/BS5QIy7JmBx[ncX%6GLYvlR77G_3Fb30l$w^"_A@Lbr)gca_5
S0a<2wBzV8$!cN*k_wj8SBpiAET,7P["j}vnmKV<aHC"-o%j!`n6VZ/!N)jR%b,QZk;d#IXX&<!D0]5yt.S+J_1|(G:YcD@iMZBE@7Qd#HwFVfm|3/6z"XWEA&:<x)!=_^>jHB
l)4+
CI
8mSV0__R0rG[P,N2!l63O.Wr]*.#JnRg.xuZ/CN0nb*IMAhi;"w?Pk<CX(K4ptVJ6
+Q|i3FuU[`z>5Uu83mdS)+n<n6Z%7dSEQPV3`2#`nEXU"oXY"e`l>FCn`O+Lar=nzP)pn/xe.@
iBdU$gS_';case"ru":return'(ev;zaMG",{]^/}!)M}N).ycp0?lp$DeR.O9,*D&>=w5MDk8_8P"5aKqV%@qi"/ZLsz^6Uoa}]/o&pQSb*B1@6Yvy^AAsn1LSkWVtl^`YH9cV?6`/E;ba`xGD9rE5aa!&+xAENbt5=.2am_jw6Pa>
aGD?dEOH|^U"v5/8Q%_/GBw(h2,XRSgI-FS:ZPx]Ei(1A]KskPP/P]BQ~-2]>E@ngj)U7]^M$A@;8"P,qEBbBGv:?T)rER9H-AuU*pj%ux1pV7n!`H!B8#H#s*$
S^[^sRRrFt&MbG]adGB/UkkB@]1t=sE2JK[g<BiEt&(_v,W6+]qa_[o;}.?8DAcO!$PA5na&r)yrm_++|WhXl[iTaMA;2p[F?9<n5#bfDB8f<-Hr|&ZtUq)#SP_EE"/+N&cDPT6KQYT:S:X%v?I=`2`+/wm)>$oe)?KDiO&"xy!_#T@?88&aiC<!f_Z1.4|sH2Eeu8e/=-M$IY>>zM8MX5f+]x/sEa11ikOQ)rX3l(V#"5ZP)jD@FK.81+&oF7LU47]TH"I3ct~b!S55!K)!+
%[7(e<xs08^rd78M{oJ"7dNq#_]5lB][lY"b}[Fr<W*:H$)Hs+L]Jj4t*r%iLi`0=;mfLeXKsG5IU/}TfZUUR1lUb-]bS7rMv759T^/8tr!X(q[%a*6AeW5yRp35,`p:"d5gk9IbSwL
WrXUbNY;O`FDm,yMQGU$z%ufVOILglGI+bwK_#(&e)AUSI&Gkm?p%D`JzD.wz/@
v5,oD%nkh8tEqwotwEK_*JB?b(3OC1,qH9$QB.=<X=w
K
]6Vl]k+y(3V_w&);G4wj-@<AM2_$dq80-DYV*:B7*D$QHpvXdC:CnB7b7H)*rMU2hb^JWaq+Q&3IOR&mEajn<umVv+%mMhotA[xVs5Ksh4UNgu7=sx$D6A<5?mwQ+D)ouaC">m7yu`@jP;|UZV=>d<oZ65TF&(]179n-t&`N4yw@=C%7`d`FfqPsR1+]]RXVB;@>cIk(x9No`/=!htl@`uLg2+ZORx
)6WW>^V8(kCN9i
YFe*Ups&J-1
=Kd
gT?v?*7=-X%@TQj*JFk!gBw[B]k,J,:X%"=IiG!L#1Vo&sH3$g`ObM-sDPyf%I>mJ(ufsWWnX`Lj4I[kg:92Jmpqe@{&%y9y{,z/M%D[%IK)X_#4eOeuFo/Pv)0Bn-gBY)gX4tA,0t(7/nW;n3g!MV_6P;I,eSx
ivwtJ*:O3KW)/5lIZ1b4^^B>o&3
e(OUq=}Fb8|fCPxUZ^aS|ZVx?-}h<#A!#>x^0/[4lxcOO-hbGc.;OR-$jf^umk7%ku}fUhB$AAfhctzn4Eef($@urOQnyj&i^k*m4NHO2e|X^/R
U<&#h+-Iw-SY>G?nf8,cF_-ZlueK?IL.+M:o{LrO,]`^j-(/F>_/C"p@C@:;y/|_U])I8-=7{4WDPRX*~AKl79WO2rnj[fUQq)eu.-S!ZlgMs"9sq)0?
e]b50,-Ha,g;yG
s3o0~dw8Sq}HnFhPoWSoBj@AnTkoj,Niw
Wp1O+SOwt+A?1C%VK#prUcJZ1Lij[PS@|q"$xJc1d?3G/a"?=<35-#y=wOl$htah`O6<?,}*Q6jp%RCs6ZjXc325{KsE"*zPaYLKCv`m-$VKR@Ms[e`f^6f2
;vqOj.c]gq44pKR
rP5~clCcnuhDFuW8p7b(3r0x7GX[T;$mI+MJl#@7uk=i-2#D)nq4PRhJU/!I&RjRAbOf=n-v<G^Akjjh`DO{CK6F:pBYG>>Zr]T_lD9K2yE-ygDz3yB{(6><H#j*Q?k>$:Gi3s^y0x*@_*R$z)WJaJ0*EkskP?=03=Z{[+3{y@9@G)cj56[#<",5f9DgNNn:*nsb-hPT63v9vEV!6b"|@|FZeuE_upOg2<SlaKLU^Yvfk."s9-]$32,D_*
q,~9"&wUYHLT>0(F@Dy$wCSOhWagF;PxFOJaQ(V2~Gsd,GxW?%ZkDehks/)g-F11~K_xVn6.<mc.iL(3$qMR;co9cKfrlje=CKe5NQ%PR6RJne`dE5;>/_peyT=lFkh]Z$mmeVkJ=DIj*aTilumikh8SNTUOb^LpyHq
]NjbUJ/d-6rW`pw4n
6Cp[WSWs$#<x

_wH(YWTq*FB6!n&
z!;kiwF>z$LJeU&^Lp-MA^!vxKC*LLfgPg]YI^m`}>GAr!7hf"S9`Vr05W48?EU<dkD#ZJqWz?:
q")*;/78qat,MK#%2>:o,wG!
G?M#?ZZw-keVVC(r:ijdO/olw6UfBHFfTk%*2&<Ae*get=fme59,7=r{B"HPEOo6(f6huO&(Sq4LoW5ZsEfR3@Ah)WbPGom#mZfD3$vI:5)-J6<]$6&bEQPD
vT{2eW(c!]|AIg%yWmtMPc<0D9Z_7<DUCnw-O3Ui+43B$S=Of[i<u#fGE,yJ$)xnW8TefBeL!iEOWL4J56P$@:.D[5Iw)TN6|q>Wbb:IC+H``gIszA-x%0EF~d]hyM5tJUwo,?9031V-[]t_^P:GxAX),1#:EvRZ>cZd8RH`W3A*hmFgjkjjD5"pH]c#PZE[Ew<Z>;
j;b+`9:_tELB<7?owcGR&VtD3/bt%>h>0,R{3_WPx
@?kK*[r/WCilB>oiIh(fedA[fwlu9Y+489+zhiqKjiRs@W&]cu8+t$J`"O$_`dN-J`kjM63eL>UcU^Ki7ua*o69leRnBiqG3Kr[!gN(r?sL,NUx?xX+^y/,mLVaen|Tmc*H@Xw;30"I19j;he:]Awj`78bGp5)GJKo/4C5f,I]4HQ[ALId4NBH$%fm+_FkB+-"O$i*d6/LX$Ml`0=CL]1P(n@V$[.p(yI.-~Bb=U2X*z[29:f[<XkH5>(l(#u_Z}Py%zHJb
$-mI9DWZLq!?@3j@gsr}X0ECf*u8^n_!VFr:H([!`d>8SZd|Y8h)Fa
^`d>Yl-w?/,?/)AWe>_eKn:qndKqDd=!F"CpCK&PK?V1_jv2.A]F`%tvrx=ALT^d"W7[@@&"viK^xw+<Q>4p9R+,8G/1c?vhwVrq=*PFnOXMMD@u=+Jt
N:r(x(8
s+_]y)G?R
4|e42vCNk"mehWY]rH@n]4dW,xS-`9VMfi7]<7XbiMB.S(WqXhfwb[u[v917e+TpP&b%"0mAbHwrpmFh`r9;l:jW^5gsXX1gD8!"xfD2W?Z9o9d%5-Vy3(J94$,dCMc>Y|i:/%36vFtzmk"BA>E3#nDH,
fH1EQ51X+akt/@qBH._^j0VOQ[Icj`n!<!<.E]5elzM$O++"FH&veS/I][d4TC#/Gh[t=<bg/5hd>41,Y-PMhA^D#y[T17-U`4_)Sd<E!hP25T4/G}C%=HXxY-]8+o3&W9A@a,PQ,dLy]6=/t.k:Fsw4e6fzM2a"DxD&)R_AuO%Gh7miL/akpN2(ySX/&+x@fA>)3gk7u?VuenbIXm*,:|&zLIBPbrn6.Skf:MbF,Y_^7O7V*/mzLE;LEz1R::a|q86s/2dajg-QJg3Qm%KtXx?Uo9P8OpB5)if}l-%5g.f,j6XaNxSs#l-J,(3wJ<J&pwZ67ZFqX`G^!p,PuqND:fa=lXHFDxrrs.7.F=J6auwmpA!qyw?KN>N#/:X%q{=!_6<!GNIzubj%[TL|)ttpJMdLHOy3SWjI=k7NXl^t89dCww+E/RAc,,r
Bv`av8BSkP67L++HjaWNq>Sxg*WdY;M:G
b_M<D$.~f85!h]72_*H9Xl*>%jMS;v6gW$L#A1J$s`_BT[5/[MP_<%7EyDhEfMRU`gXk3uxstC3hf-SD9z({q5yJr~2(-"U8>aZc<[cV;^odrAU/`,Z{D`^b^.EgmiAR[)6sLP3s-;BH0lh"*vb|hct=l/rbeaWc5JlLXb?h9.JxUlULftcH$b7W4<?oh>P3`r.0AZDe]HOCw|sLmA]Tf8X@a/-@9/ChTwJ{>?CiAr.e5)GYnSphL@M?iB,nttJ5w
_XFd6|G]
nMqPn,M0Q8M6f1v%3.YXyBeu,X!!BKpA^Xv%Wx^0YFFl0_6%SUL+|&:c,<,oLbGy,hg)1>Fs/[%[kFMGT<MhppqcpLyFRw8:ve(w6[GnmPV*x5CqEG_Tf`~r*F&FK`*TM[WX8M.dC`{_iSPRuk&XTuo@)i;v6QknD.}uMx[]#,`lX$_qL({#]
I$)5asJ(@?LOp1]G,2rJbV~M5Q?&KqPQ?M(n[SX
F&m]jPJkR%?uk_Di"nm.,xaha?
B7i?yG';case"sr":return'$c0@qbSZ+.C,gY=#+NQa?PD7=drBnm`NU->I]f5ehLaUBP7l
/a.>ZvRs6BQ>=jWn`sk[
]rd8nXES]J}Y`,+bWM2m8JJmN:%Kj*%=+;Hu)dlqD(/%tbD+x?].,*7xyq}mM!!B{M}t(1W<!NaG=Qb!$R*e(O]T>mQ>&Uo34;{)qt8h=V1mC:90sk?h8!OMg)89uq(>0xYq;cw1D^EL0oEPAB!_RH9Y`VW26VHQmC.f/:xf!51uYeajlUbh<Q+IHQ.uH;"FDA<(#PV<yG,qk/Ts_2o?Nt4INbmiiUiyQ8ZG&o`DJ#i/BVdI8Fq#t#&9&rf=agP&-&ucwc>(X$wDj7;d|#PTCp*mX+2/5^~&/??8erX[`ZeoUZb7hwl%3<)2lCIcwa[i-4|^3X=+U
N99
Ryn._VwP6&nds9#vmdGW9`OtjK.p/L__y02ID3q>P:VvQaoW/)]H>
KVn
+7Lfz2S]x>p0)
W3r80t"8U@/?D)MFasF+8YVO7_P_#/zxbX,<-?TY;`DT;yw_I$)(4FcaH$juuh*@:;HW/ifL&
YC(p4`to$m}fU.&4EolC9d:>!M&$pVc9))c!$6wB^m<tMGr8nobo6Ltq7bD.",3;#""8z>{+9VSqrg)lWgM)zsJ9gO&Cj%
0k+
p>@_C7D65Y?kAVFG/)6iE@n-3*Nf/#CwA:=8@=O},oZPWPn^HVAi(!OJg`c!nRss)&`Vb!9KxQA(fRYgfF)XRfiK7?o=M(#-j5P0af7Pb(pjl:)jCh/_2dk[M}k[9sG2?`_JfwY:-n+%(|H5-bC[=lurEr[Kw^V17sQ(a5S_i%QU-G>A#H/mwP7d=I$i?97r
+X@:iT;3y?^TFOfi8Ig=NXz)!Y@fQI?a9C!U$unVmY3q_Z
hE9gD3rdtVB?tOO8k,G(P}gAv]&PLIr8)tgv8$uc"Rnq*Xe=^M
za2Lyd4+Qd)39a:R|-U%mJV0)OuZ1fEelF8NNPr7_VM
*.R*6h5-Km
%F=9-)0so+r?tAn[7-fm-%WF*xJ?EBnhC-7LlyR=
frVLBBo%EE-z#"T>1P[_m1mHJlF?*r_ppl-1T0qV2PGX5vdGv2rv(CFVqRH[hQ!&0s&P{bq),KY%>LyOu?8dzpr_>YuLBwst)L}xTduYX#<9}A<itc3.Jl&3o:]wIWV$w+1d|e<$}oc/;66
=$=rCr]7VW(NJ`adNh6Pk1Uy0s6?=`xO<?9Ma*<X"*iK}:nsUe4O(%;M;3<AJXZxv-g+f$7>,,9!v;R*{vo%K-!H<;quCZ`Z>UpXnTR26&g2!KD>f^z*0ZHeaR0l5&);aD1+pQv(6.W=~S`D&2CcG9XaEU!GD?ofupd<s@Oq$^PT3SH7F2Bl.O4q:t/P,&
oFEC3~d^8YQ!S-?*Ad?QU=?[/l2V@{$k/H3,,GVDH*+zRZyQVE%!2}:s9Xa``D5TP$Ca9YsyS?Sk>6PfQz<Gks!sam13Bil$J.<HWZ%s/{GoA96n`]>v9CA
N]*inuV%]7KdV%]YV?=a(OHOX_t}j-xNqnqRW`uXi^WJj]ZX/1oCh
vT9U@&"n2_>
k&Aam%c{q-/J7Sh/GnD/`rtlNBlz[t9EQ,lQiF
>eaO}[I;vi.p<h^t7v>@5`#QB4(aF1<L9PXi2Zh]i2a^QlJM`/v<MD`-Hdg#Nde6%?/s~(0bpd&Ta!%+-5j`1<pfEv7Y@mG;"Fxe;B-GR9D_|;a9T2X"7gdF#DjSA,^&hqn/gj2T@hoel12k>9ln76bk8vk@rWt$INVT4I;Y+K"pnAoDUtjdSAS`XkzZ0gk?;H=2*3;kw@<vU!Ui[Q13jOgXNHyQ,,j/@$2hj"&gY+D=yb@I,/98_RRB$+YAd<~Zst}@de#gmpCW$KjD93b+jgs=5.x!Vw!
;k+NPY^N9O-bwFCQ_NE+#O}4WL6!xt16J3ao)R3qWO!S1cY=ELg&&
PS<M~CN:}7N;$jl`OelB>Qsu^Bttm"r1Cjgf.&%OPKNh3rcR)&"f~uTWmme2aqJOiZuFPfo7|4@+%?4[ugO`rYCRJFfv1(sQ2!|,~+xGZmxm`924PE"$Asn>|c&W0`?ya)X={2iVw=4`Q>_]u
*Q[2y?w+F
n9wsFA?tmkk(Ha<Q&>w+v`JD<Cp?|H4*Gbk<g
_@7T![onUv%YFL"-N^DP?m[]I7l#2O$K5qWtL(Ex}Qw=h4!CDN[>G&D&C-"Mr0UT-CRF$xL%CbF1W+sa9^/x[b!KGY4nB>69zHOb7U0H29E9|?6LC0H_@PF1%l)AoI/]F$l?h]*`xJFLfTPkCilVAx=H)CO_?RWJ*R4rz6KAT$x;vONC67biR,mL4J7(f)?^4XZ14=.)F0kLBm,mYJ:SYcw!<?7xXQJ.6`];vI`O`J1o*bD6AL+iT,<f.q/U}yLp/h`5t]TVL`wR9!%DnIEV7B?eAdc^CeiF<[x:=
ooh.Bvm;XLFjGw%
G%^"5"!9uJP-<)Ou2tA>m
7@~57.]WYq[
4E]JRCoUL
g!6L5x9f4V[8glUdpw~B}d6f"WWl>hCE8!q/;w95RlGBH+6y0C;<JHoAA)RhMs?s3laBWHRm&B,`<vuFnQrL
E=(0N0dvuC2d?-[#fX5RRK.;Kqy@lgZ[]F3ka1I8UhnOp{a;M$db>cp7"0CKc6_CO
nA3I2?x.<kAfg/bMv/NJ4F,^[H;XB3512$bN]HC|EOP5)9JZ)$pR!RI6fA>F=L?G?*-?0HOxX]tF+E`$X;XQ]KdM*mf<W2hPA=[5*8({VFx:pBJhP~k;+[M+
~DJ1)e&wJ;.WBHR_DsQRu[;Brn,xb13FTM&<%k9Sxvm1<*q(|U((ymZ/W?%urEyBa@[g)j,
<R.$4NU!+QzdJ>1P3@i>-J(
1FFq:J%$HE^^f3i/>?tGJS".HI1@R0=I*&L1?`PkDgkD+>KlIRiA)S$XL//<=x9%W)"KK51VZYAf)*Hb{WO&+,LG{Iol7s:%xxS#5hpr`;jNtV`Z9cz4M^%_uiCH+x!,sg~s$M8h2B/7<;zYPwJ,2T!*O3:_D=(1vhmc8X!q[V
j6Ov!L?<.-TZ&;u[o4+:Mc1upMt6Y*).#ZSX=lP4joqd&KL>!Os1xDg"SSJcRzvv[R>CZ`nZrK;*[N[@;*5}GN:{O.d
p;umvG7,3*s0i5_S%+A.u"=p^u[B,r>@`ZpC1Py$(
[5][*FI,@(0#uy,Y*P<uJ%x-tY&+^O&`O*L/DB+Y<%d$Wdg12I2=vnsHNNxT&HVRac@Zj[TN@JO*lyhxDw3[Q[l&FOy}ZfMcsA)F5<V19NUUoT>3H"!pEUx.LU:TNs#FgQQbsVlT7$eQVre1:DWm#7Pkq5x&MHCzfk)Zd"j(7E1FNoOic>JvP%CSOzM&U#GSEKW6^Si1gz,6U`ZW51)^(|y;
%Euy}$T64q"H2H?ZZbZH&LAqA<[-Ul<=1W8gjni;VyM.|xVt2dv38]eYr<H>$(4NbHBr"D7%<XX7%O87S0r5NVUr"P;Kso+a?q>y{B!(trCHrhh?jdxm^Xn!7Tq#Vg:`d`w>7&5V3CO_Bc1xG%15e:vg&r}?{va1fS,a<P/;w#Dj!^u1u=NYzeS3O=]RgU@]&=#^1p4xi%I6U)dr1xc@4_?b]sACR7$r.9Nl62lbjM>HKKgYr15.rz%7X5piT.[LJ4,c$0WZX#^U};oy@f8N8
$Fm8Y6oD-k9Z%J6AP>Kil5d%#`sbNStXQ49a
>R)];`RGTtko!$;~Es<e=iy%_C/FR1t."J!JOr292e*04!8rcn2))Di^+um#YL$_GQkVu;4}"Km7f_I22<;"0dFtLE/M5%1=a3(?edr;1vF^[W`ps<kyGP@F)!<W<|k>),ONnq%JoE';case"uk":return'%evAL;;G"*41Fmp>XV)m$=efeWsSS8
2b$r
TO-3<"J9(/OeDug.&u&q^h8M2wBL)^Pa5cxP2&@>Eva)eccc@^KK+nCK&PYjsb
yAmfa-2!s_!<R}JeUrslJ}EHM;j+T_^=^#n}=flaXaKk`JUd2>fod"kfT8iM@vyY!Oq`ZhA+ab3^aqEfeDAX.86ERMRG:Fa`Q?f%TLMbI`Fiaj5)Jxr:c7d^e1r2f
^1V4IdAdvgW8(7JJY!n;%Z[oHZq3*7]bq!wK_|,>pR,o93<#+T6*KTn?HR1TG6+n&ht#g)4e(0!SLRpBx:tRYY8c^k]|)13xX+y{s3AqQfJ`wjC.MP*X%xGy=s<]+T/D#TLctGbOf8peE9KHh,yxF!1NpI)l"jMxuQozS&Rm[c_Cj]Jy$qye)jfLdl^LwJy>373r)o%</wEdc
&m-t`25|+pP^@q)KpA%zj!r`A7K"EhZOc/K8V$3[S
jW"D,>q$Wpj-4vAm[f,Dq~O
&:*`J.S7WQ=e8zAfYw0Y
|3;$A$j&4eYftClU#&N)$JXy7UgV_>U5P5[E#^g:Zv!t`n;UBG*P%MhJD+vKqp
=bUJ$0PITugzims:wRX^V<xTBT9X)-dG[k!X0|N]/7n56kuT>eaV_.uxo1ZS>}k7$k%G!~Y)lvSYYdey-`iX4x;QVe_:DC#QU$yLx0b>pI0X5=byggCpC0q@=+mFNLt<:ZMfEum$U;i3*4K?*v>UM|2n1SUp=j40vDgQqcU?C)LK2}*ac4Y{bu+1?^#/1"OF)qn^WPm0)[[UicX+f<52${4Me[K+aBwc4Zt<.Sa}
8f=c`.#EHOGVHRU%bY-<BgH$O+4ebhE.4n=XcArx5+3o5c#fDHZM=l!vjA`2?3A2lAVYaed,TslosG_F3);F|s:<E"`]55}/
"I-G9i4t%x(64a=})=KO^=s@3oQ)R{9>uC4D`CAx/W-Xo6V>SSNg^z!DcdM.e>al"7Ah$NAKyyU4O@05@&$,A7hO_bB(i|iYO]Or,e*3AYc+cY,Y?%Wa>KMd-&uBKw`U]v"$R@.CnW6Y_NjR14dl]@6b%)*+IidAYV_K`d+M`W
6H>KJe@dRNKWz)w<R4oaV8_JDN6%<i%#O+S=
=!SWMo>ce.0"#k.7s5Rn=Pp#pN]s(1Dx-.`4U1
jf<U^^=2hNp2b+_Ew7ori_D"|)qnl.$nX1H0t_npw6H8IH^E~=mc&+l92Zjtxju/?Bea,i^>TasoP)f.0K
(7;:(.NVF$/Q4pcAHG>B>-s5*r0^)R(BJwJ6cM5LS6cV8Sv%jm77u
6^-m5+q#
x)k/])w)r-4Oxic#&BPIz5<
YW1<9SoQ,w#UGbZN8G2b2j^!(Q=1!x4dgj:Gpv6fId"s33p^Z/I+L:SPqE5-~*Dgbnvdy0be+#%u$f_HKBs0y<D[="1QmpF).i}gt;UU/H>CeUZ)@H~%PV%C+8/o!eeo61p;[ZNm++d#=^v+Z>UN5/X>{M!7Ip5S_uo#*yRyeUBi
A)^uN{m93oOlHt3^iNv@N[4:
XibXId(p[XA+#UUtP0p8`9f7Ce?:ZA5<uXGk5I`*y$MH42:62m3HYw
yj6?keAdGq-.Qu.U!ng[?[3L"ZpYYUz#7LK;fahqP$RN*HB.=A-Kye8[`4G9`Q
wtWg_kH?"]<X.;"uS7zN!%nc-!^GAu+odG:IS4T,F9KHeSQ?Ktep!*|2]?<$Sn9OanDk}U6:$jX+6pTE4%LviYO=wa>M2_2.A38_ircovh_InX%LdHJ.
nXuJw^i40NM@@kZP5m?gH}gl1QA$wUA5<7TzPtRNtf(%TxZKO[)
;6>H[{8w/&x3-{2%x5G{:2-osK3Q4TK!HfVa1}wYdje,%yK),(ybT$$`-dtRg.83Vm&&fl@HT]T3-7"?G>"(VAi1e3v*Vpb)-%dsHvE@j^DP6Uk-@#m.E8VeXQX(%@;)+"Vl@u>;xf%BoE_/mwxj*JHLJU0qNTly";@"-T5gXEQ0j~USC1AB.rOS#`
0,KCjS*2=*v"i[n4ZX|xvRC6lx^v
@+g#;i^OOc(Ta8m;EZ"C>l>YD;g"*m[c#VgI:#bnr[xgs6kv[;A{8i]uX2Z|nj?N,kyM%slZQ2wN@aT4;GRJ=8dv4`Pom8#Rf*.]oit.<+ec;=TLIS!7=$_IZco|l&4w-U#*@x?q;$?zPH8Gpv5O^C35NI<7$!otRPiEEYMMouEj@L:D?RZDIj8hshiCKUp&"*x"?_<f42Y(9_2UNVr."<v|QFK[]8+Bmd&*ss#oQ?n"m.;
2Go@qR9B
$N7]Al#mD"MkMe`>}m[>d]&BrHD;P)M/O>MKC<4$cSPrblU)U$@)5>Z_=E2(tm.qo6d1FEV.<(vh(bkqIVLQE0a;14BppY}Q9[7+:Q~BbN$M5$IumfZ0:*7t@//K:h0&+l7<y)5I7-!+&T9velD_1>
Fdd[;GI:-YcLE<-xv:78:jv
4%x2exNprlYJNvtdT]m?i<KhoZ${@#nu8ZnGc|2WNEj,a(dC<Kpa41nk%-s%]L+&w>D<]:+2W@"/Hz60Oc29my&v`yp_qJ>Jd#y.P6-huo&}<,Q(FXW%Gkleyf5gtR]d2wr^
>H9S+Dk&;fE?yx,p
u}_WCe.3A0F??x)9Lv!uA47,!3@L^@9EZhYVoH8s[oh>0uapA#jrT(^}W^/HpC(
BlZ?Z":sGfmQ:hZ%)T=<j+grYfAwFFbWO"3oUHIVZdMDt!uab[:R7;5*(i>"/Xc*oW$:lA"u]S
;gtcz_3"<rcYou-l9-?"BQV&/uYd-P>iiwQw;+nP):*S!g33v5,I)^;a*!yA?JkW>5y=f/<K^Z{``nm>/j],bvQSxE{M"LV]?H*-
a`OfEi+fAi?p
G6XaM=4h>QGj)oN
RB|Qbtob*k2]x:mqrsg@fvrF1*SVk<;-%hX`D#<9T3BNPZ%Gm5qT$alIVb
E4TX^QlgFji7dtKrn<H!H#iZZ
Q>w%w~RMXqKF_%=]>oC.Kd/~2ArDVAh6`3RtKwr]*?s6%B,B!;YK-5bOib?b5@p#IbTG9#)~Aw_`"6T
rFOCWK&G6v$AWc+xsg#Bpv2d.>)Xue?7:}irMPSe#i6dB7q3/j;-80dC[C/4B&IO)-pwQ$@pN-
:K
7!u^.KV$M2uqB/S7@oa]iA2Qao(BIKrO0l*WfZ27X>c-.Y%~f
EO&309KyP0+9Bq*bVFq@BK;&*E@db_aL[zPE={S"id]"$F1@`Oh?9P(_Vb+"$y]i`mayV)91YH8^CAum^wO/$(.B@6>33PqsxZw
J/IufF$wwhi<3WXKYh2xq:5uwD/F(WrTlhv?o"N1Xo:ol=
="`z!Rcb=w`_7hf$_ONH;+6*-W7SKRl=M4Um4bMC@LttEa"C$)!
Md1(QXOE,=&"eP{I4b0Q`lu
O29TZS6,mlZkh!IHGaB(x;iZQwGf5MV$;[,F?g0]Au"J*c#J2!G@{eDaSQVd7g7%D"m,ji4B+,wnGshv-a&*;ZWrk.rqhgNq1a2K9Z%gaUa2*M1N)K4/%l0ILcr3`3Y7xnHd9ip9T",pBSm?7
fc>y??F`{u-4~w/1btN3
%WbYDcl}T+MZ_Y.GH+,lK#=N$i`2;0@b76:.XQX]"fw,sSZ,(3Unfh5tc#EUPJ58F4s.+,IEm=BVFMs$
D<x
rxNXo;T<.Ctl|5:(08aR|59QVBgXSl;>ANK7NuiWan4Rg0es/8=0ut"L,2(Lhe)Xbwwn<JMlE@u1;$N"V9Dp#a@T1aeN@-:,H5TO).5igHxg{/7TX$K@wpD6W+0^vNzj,Gq?LsPyZu~PU_%q~b&F,g[hpU>%kJ,>.[PqQ3~G^U(Dql4TZvik@14_u/,%8mPn1]nJMD|=TZTFfBiABuDg{tiFgqzGgH>@M"Vk
v%nLuP0M*#3_%8RbA^R7xO:Z
Q6cGEVy1Z!!uQfJsBbW+MFRqkj7ulp^<;e`cTt=[ZcRe`heqAb?*v0g%=0<dPPNVz!E7G:T?%6m@t@MunCT%/M"KfXo.BV|MlCQ-.vkde:#G0[h8p`Addb5!(L8nq(Ii!i1)MhiD1UXtVN0j8-U^vH6]&21j_$!7M.S<,as(woyL:TUR3aU*Xf-N^';case"he":return'-s`0:6KZ+&iq4.1ENu"B0_RO`/x@hNzpG:v<|T?+^.YU__ABwJDB??SltWsf^$)"j!T#?:PytH#
zx7N.JQ!a
JKlcZO[:]c&&6J=ed9!V=*209[|TYI-u6Vd/JZWTNKG]^g{,X$G?!=2st%1X{WoY@*|qH;1jz_$ETH@bk`s<xWJ``3$2(WeZ+hb"83Jb0#XLxm<6T2H7{I--@*9A{P9-*hN?~Z^aXp;).<GC(#>DbdQkuQS)UkK;Hns*qr@>!%x[+Eqwg"yJJN}YT[[e;$m,lY|Lq`eD/9%g}2;JLE*TM*u02wJ[][~1}7z-AA,s(*EXa,2APc*W;)"vh)mG-Q*D+]
^6#IB];+JQ,)
A9@3aEwVW;`Zx[}LGii[5!oO0<qg4rP]oF#+J,qqj]*4Viu4
dD%*gnfiH~la-aR@FOI_<]G
y=fsh#"~fO&r=Vv8mhJw7GrJ%DPA=%c3UQ*(3^%t)K:Q;`>]qa27KG]{l8dSsbC3ht3`>gxF:c3q]s_Op.)ad)%=KI+or;CfJIi`IT.Yw!UM<.X@dpsagTpU1b&}&eNfVAW42ptt#pwM+dFxIq/Lw6eDWXZ3rndSl)h?L&wa[e?45=%>6JYQWSsWN#cGEl<iOq
sYHC(kj#[kal(rMakY0d+418=5OHV-Yx?JXy7?&RGP+?is%!3M[Zjpa>0D7L>,tKS)ANo7vPh2#<:Oe9QATt$j*Ycve@:A04w)~Qrguo>Gnvf/gTroEeY?=:k?w,`$~Rv9ecE#?(nSOD8R,&)<"]dOk[-fGZ,<lJmnO]v,sz$#3tq9Is9mLZLN_m!]}K2ZT^`Ov>FdBPx0i+@FC"%%F&v2rgmM30vkio&9$C^ei9j<^+ld!Bh=F6pNb4z15N&jQT!#z4w=Zal-`hx6"8Z9c^UTrlX[}
,S|KI)pB&!J6V9h/aP>7?-Kj]E^]N9K@SVT@R&{Iu%ns+0=p-Ic"/16ox<`Vpil4I8+YT
#KxZpI">X`h]DnH1#@&phj.pWqsK<$t86h!_.IwZBgV68/-3L^H/wMrM3-gZ4-n7{_KIZAZ/9Lf>xNrw3";S<%`0|T7TSD},=f*ijr)/Du<SL@@P{qvfQs/t;.ZcU.{_E$qZ"d7E2oZ5InmQ$1~aOKb&j>E6lEN-f*BvZp=LklvJUD9@|_YD;FtxTR`8khT-[CJD|R~dtnFhs;D/|@mR"5bKL-1UR.P]RUn:AsGj8y7=*,s#yuPZbs(;qIBg4MA@
Y};@eYwjusg~--G26H5u`+Qg+5,j3q94/O<0hrLKC?OhwAD@bP-$?`Crx@S
f9@~MYTYWe>KUT8Ug0G$pa-+SM;qGk6Bd@BXRYVT9bCBTuC_rTv4xO$9N($beQm(6>C-,7]}Q`2;G+#&/gGJEfpL_(jo_T[T6G/%:zDxo@)M%pi,_a[g]&^3mm:uRG;F8hJ&]7C0_=D-wnG=.x`0Ul*xLTl-jZN!?{9r]~ObC1QgnYe&e.iii9VaUBrXIU(E:"hsK,*:j6:2oP7X"6
+Z~jYDw@f=5rMmQgBaGM+kZg.T$vY.
5}oNDB_D66SJ/}PP$
7yKtk:71N::(5VX.o8S2u{FHAU6v:u-N[~I.w^>O/?lqJ%Njt`3;&(S&2h0n])E<0I[[%v=
V#:B3Alo-^@!^,li>HQbl5BDY_c_vT4H[)YW1@kAT{K3-MPSCue_rU=l03JKC,4,b[uega#a.b[)boPwH"6$yYr:ROc/gtW6bk=
)Mm#=V0hh_`*BlLM.VekMu?l"$XxXatk:#=SQ!!&M]:]AikR`nX:84_}Z4XC4Xbxb<of=2b)vg^u)BSI7y#sgd;Z?p)jsR>q]NPgb=@
fY"deHJ/E@TU?x7l,hm-1iFLrf[
g@hp8,_X+<Y4qCFf=
YQ]v<Ic2^ewd7Ath56,ow$m1SP=@5!v<(pGS
B^,GV/e?RrF?jk91egjaV68x4Y/Zj9:*Q*OlzU`fXq+giY?L8g^R
v{Axp}!(.ce7fq$^6U0c"F*MyM*Y_n+4yY4nd&)6adu^[jX|UDkhlf!&UrZ[#u$oJAVLN|-vUz:;Cgj%04vU"*>phWW:D<]T1Xl!p%,.LtKIpqN&64)MTJqQ,hp$2AALBOY+*gt4u>wh<-U#F0mDBgDeZ1gj0wBgh5Ppi7_#Vw[T)|Hp3k[*tqA9EV"
:)GAF6"/FcgnlL[mR7S5Ro<|J9>3Hw5l61uX)eybYX5$u&uGyEvpdQ;eW`M2=J[@u#?+FOmLNtUz*2Y.uo(>b0EN
-GOR{tz?_oAQ$[?E@KD5p6jh)Fb[AS&TPe>X*2Th0`du~8d6D5Wx=.Mxk/WHwJpW?*Z
jk;L,ow2
/xnQO!X%Gx#-`mVW5R!L.aWdH<]nPxX8x/>k-P^VZYSR$[Rr)L-M`:E6Nu)A
!#K`sVvbDTHW
$l^wYtv!G;wtx;l@X@;U2f3d67*1Ov<8*q6[Z]Mb/1
OUjt36tHP]c4Q:xZ*KxyHN&';case"ar":return'&c0;;5Lp=)R?lZRO-d(84G,01IZKqig@k#`-?e*[B,5iX2f$VqCwhW9GGdEh})dbSbduBM4L0fFPNk%cx+@.-f_-}<d_GyAcl@9`.K:;Jy{=2m(6kxgXj&wtKcX59l}yl-2y[,ZI.3GP_bLL.f7nxwOmB5!Dd6L!py0jbqbG1q:uX`rK/L6j-EP.tz%Z39SWW/xyx
xLkjRC2Eye)t&xC68mll_tz.3S#Acm"w1ko]"@H+jUy<9e`GhIN3NiU]"S$_R;5w
Q
KEV4j@wwGm]2T1i}2C<"&a;$RgUsAiV-dk0<MmjT,K[Z^ac.%Ell-8a+"6BP21W(1!1,H/R0&U/yxPqmGH1-.po1i!p:-hP-Y{[Z^z*13R0"W?kYGx@pcl#<#0X?%ZaEvd!;F9t!Ids+>%IBlf)mT$;z]*ZmP-`f33W|Sf_QCGZ@E/l`_@.Z*HCo?Q[DO7m.l::ZjquD`BBFN$Z-qB/~f.4nO]jUDNvd*V$U1*#|Rup+y7<".Upenj;SsWg!X]JY`]pbm-k%KjxTMY_UwnWVvgH{W;ZEB;&,crF2ptfifF7
l7L(&am]G]mt."Lq-+]gunq6whL4[#[|e3F-tS?s+|fEHDMc9F5.&kI9mL*smx3_?uwjNA5/+uY0S+(Z=AEm1ha/=P;REHb=VzEKHeGb-yT1O4h5M[5xg-An_2yFI_T5H/:0a?<#?}YQ:pOl_Xj.>`hju
4G&@W);iRV>bihS@t)n@-"QK8xvWeNYf&7P:TD&Qev]~OZFS+
sV%aj1_V+@r=@I[>%>N^Zw*awe0e0UWAH`u
#HcItyQTO]3^a`Gj+vP5C,da^mOnJjV{XK"hhj+,2X@lL$Pz$nqo+Jaw#q^dh#3=Zj8.63o)%e
{lZ.<aed*u0q5!$:"%!kTUdX+TeMdZJc:,6cOi.73yUKA[
-bv{61qQz"PRcIq(k!%R"%[@T~Z4Kn!B(FjX#2lj2F<9be/ySsmSud(cdKdU"c7Tm1RX)_`ZTKFH!c;E+[u/fcK2r*q>H!Q(fA,Ql%NJRGVu`1y8Un*"Z[t#^Ib`hl-?;-C.vst^Rf72RG%R"Na&_VRilhI03ADz3.=b*`OmVBq$"#"|O@hblhXinjd&O
HBX)FC"~-hNalPFrPNplqDl}&$4/#5YJZSg:ic0AYEv4YYZz>oMp^DAsQ(d94;5~<14:Q
$_m
-gcYoBK34pK_VdKIE
=)R`4KZo)WSw<w
_Y.L2uA3AUb@C9v-oQqwZ#?5zyeMA4@g":KG30P3}!K$h,WiQQ/bRf08B!m=/,_43p*DX7"d>iyj+EE>_x.S-3w`
i+"w^Yw#%K#%9=.UT3o[x`BaGxNwoH$`&:[/UzcGAkrMl8;o+SaOyFTKK+Xq
&:NgY)kN_9o9eT}JXOfR9N-:AM$*S2U6c#Q?;`Ro}/TdQPs#{C-q=d;&zOL5EXW+3nS.0Xd#oebs2x3rv:111:L8:hC%@0+>-c0d~Zkp1mjXta{Mw6c%l1lbuSFJ>&PE@#vtubOlGK6oL7~RINkt5RR#busMDEQ@~8j!fN6O,#)7VUsN-vHwID{J_Memb_u8U_[ZpN*qm4MSD>xJ#H.9dH{]E.ql2P_f~*:oyR9ymheu}PDW#.)GH:E6nSVo-v>2fNKR23u"Ap0T?8"Hb1y$I]J;rN3wi7S[!X|!*mMRy$w=vf`EA%e=5Yu_ch,%|/[h
j[`OJgz&McK78G#xm(4(Ir^:XKstWyGt2["kP6CeBS/$.,#H6jf)w*1[9qGE1g@ciyK[AIcQYw
ij.lU^3-(d=DH^zJvjtU=_)&Y5T>6b&W0tZGfAw554DF~qb[#:;ew+mF._2#6TS.vQ#P:v|r@#I"u:,U5i2X(lOV:=J]9F`?ct0Ay9HsSB4/{BgOOPX(^8-K9U=#quH&Drv!M;QgEwpPl^9[si_(+)|L~$9EsJ3@tD(n@F$H^%Bc7rSpA_9L,CC1&qM
MvP>_V-<D`:q[F|w=p.quF9NgF?[?[Sg_5k:O8fAvgu
Wj#j*xiC9J&-3M2KND1WkAgI}<?U2&/vvg`
M79sh$y)PTsc0Pg$yKt*Jqw#vK4tG+2p>B#S|:%s!Gx+`"JB$]X08`Q%<%$SbmY!R"J;U4Fj[_$%i#w8`3@<fjl)0,19LsvjOHm*F1U3"x)bZus^hJW2_eJlqH6U[r3m8pZ4iFV0Ii$[?V|dAt2ac591b0piNWL&DlZO=6l_RbNLtguK:d<C9NK,|KB*gt:gkT=0_-?#=ChLm"SaJ)fKeS}KufE/bh
GmX#.[TkYu2213T~^achL1.X*+Ki3+B:CG@]P,0ukBoTko,su"q<*hUQfB,5viu67iu(33pGQm;i9^*+5~/jmV,A_IO7R!cfdSgVt^[8(en.s%Q=WZ0=.1lFg$kNELBGUjEJ.L;$V9pFP1BXs~@(=e_U<T2kp_0@$Z/9Kw4hnH-)vv]}M62y$SAB*g
jDR_CSURiV0&?p<Uk:94ne-PgPtNXq"Do
1_?GL)6SkC<7ZL-3uLmYn_jFX*jd~xnc4yi,{HP7Ddl<k6Es)g|_Ob<cB?U[@kPN8][^[68P4gfF*v8S~9Z</r}"*uO^tM?XP3%vcv*vq^L.aG&ud"&Iz[LULNwNLQHOF)e@rhOX+1|j!CZ<(ZC#2$$ZX,bIIrsb|[#wIU`AY!TyCeuD>kM<yx$uyD<y//DX
3r@)ezPY866YD6j6lk0v+VtZ=u[Of8r`WzOavlOq)fCAu&x|XG;3HEYcn{wm%=?eZ*^KYPJE6vP<Uf[hoyRzSE=_7Ed*k6-L)mn|W?vK?]r
7+#uJ[`GAs#p);V)Tg=Tw;F8xzy*$9%7G=x.Yc0Lm*;lH.1(RVH5j]>!P^YQxYBWQ><
0Y#bYGcA,b4bl4e1D!#pkN9OWMwuPC/Rv?T=29"(f<nr^"9vw&_$I#ovbJU.;q$5TxN+24AQ,H?6gl>LI7BTV5k#JU1!p^-LL7H>er+
6ls(j%R^a4Zw(=4JNsE=.-yzXkl0[4J"+(JB9<mkIEai46d=NmvZ1&;(hN+!.#oPP"Hh/v=x@Qtb2j%Hs`%XYOjC%[x@qj?+/ecDDcd8N6H>Nqe|k
uTRz=ze]o;2^:NYYylQLL]);-Cc,.(WzQ;LN<d$.aZkD/vlk<ek5u:f-=!**cj5&fldY<`T6eANlxkj_x==$)b1a>_AhdT>|[}&"b<.0f@axXmPxt:0h
zyq"
q;=Jv:2od#0iglm#Pd2]WOcs2A*FkUjdG)
hb^[q$?S%ZtRf+Y-LK=1-Od":5YSm[C9w<q6-t0.J3c-)5]@b->0rqH
.>`NycAa1v%Y<G;)LRL`$44sDCCIxb)#uniDGSE';case"fa":return'$s`09bSZ+&iq4"GV5;C8IgWke.~NGSGw9&n5+C^&;T0UGOti8I]UR2DBWM~;W">g]!UVa$sGOeis2L=>M?Ne2Bsi-c>>Ud$Dgd^]mZ8>4`W$Ia=oyb[jqpAM*[&5vu82=f_]b6Yx?PPa,6Y@86J#FXdY^vE:^mmq!P:O6hOJ",.6/t",M%I*},~LGRIjh?JmQFg[?uvv~a>IjY7,GDo`Tf$K0Y^V?,:s?Qj
3=ajZW!n0RYvlGo2p4DAW,&wNmWf{7pqS7tt/V<M*/gKlqkMjo"U9AjD=;,u9RoD2HOT#P+U^7M22swbV*_5c/9V8Np,Q_!5zSN7Fv1d[SB&O]h"(n
i
5$9o/K&8[W?+KSiExpgr:ZFTJEU7F}mfjS`r!IGy[B5Cd}5_o/aaFL/,Q9B5K%gTujOPEkDf0
aAdkB>f;>_Lc
$lH"cW9I/5h6l-8Ua:cQW14qJRV`gUig
I99x"5
YX)t82vbS>-^RLOSXx$#)I2[rwWb3n8jn#eSiM~Cwo&Y^;~emYf8^KJ,T7KNI4J6O>mS&[+Sk421W&-I0Wk!1u$5gu@wpqbR$:,4eW9]3Uvo9tTX+m?U/3,BuN4v?HjYm4:8,([f+jQ6N#]li+,)2xT1N,UL*K[
,K)<
#jO/c@^vnDZ6xgL36Qi~].GZ@j@=>2d>"TC!Ta<|i!BVnWR(/WrsMRWr*L,*9@k>[z
3aHojNzUXShjJ3/jrCTJ4%z6>IYz)5FlsB9H%BkH&e3Hom5=x)wwKm9cIM670?}hH"qLNgjq@&ACMX%tmsQv(m6YCc{e@5!rk
MW|^73(msT,p|;"kO6Fh.MEHj6{454FgRd)Gog5B<pDvDa?2Mm*2w=G`{90+L&<*~oin_I~a{1n!MP"g:DPz)wf5#/lwY)3d;VHoz3!7A,9NpYMog5~B$<a?*&9$XI`30h>>qU}8_rQ79D/j8p$iFOlpP.
9%abDg]zF7MtJ!M,R8)Z
hDO9;FQ/0p{deX$BmDs$`S=O%nGCjx>7bDOa^/}TzXG(L/)Bp8!_`KuSW&y;~>dDi$W<#!)eM3sFb1]93U4yfG30smy:9H,t!P("^c@37f3aec#xSA
1{cLxGAlw2T4R$cERZS4BELD0O3WSmPGWVn}V8&bbI=>RO+$m2J"!"YW4Fyl8|ViumgPyM"=gB"F5tbxVdLr^"-3DRD.?i)Dror_SgP@A%q5rutkpgC~pug7>ZwqTDCZ4+Q#VH8#@9K=dV<f[QDVijg`Iub63M"qn*guUzuwT`_?j(dv:$Z-kbatBZtzHjm5jB+>aYuSmL5^Nr:W5;R:`//jh1@LlB-W/s^_vPcgU)NTOVt6xO^X*U=AL(J1/<=%@cS:AbZV)n?I=lHiWF
)o_iB(cKq11TMj;F
7Q6=NxrN?};K=D]ML1O[$G+0S+5=Qe1`BZ<"$JMY$}/##Hb~E:BFEe-hqL3Nnj.(dgikjI]lc/pYa"8Io9a:#)y~Y<#3%,"u%EA/(`!0#ExUc#V[0&9!lWREplw[k"2vCL;zGbp
d<T(T|@`Dq(l`Se!fJNdlj
NQWfsaA$Y?tP:Zm!w@_EPJCVa*.Adt/d@a/FMt.(_G&V,+c3O;@+e/jRyoTUQ5!*hZ-EI$%9Ef5RAyNvDvSZe5LRwHd.qeM/12t4c3-^(faN_pu#LaH0}U,2Ypx/c_X2mkdC.I}6{xXU{h_HfeyYYAs<b)JITJtkVu.=~)kaKG$>f@m`LDT(]Gs=p=pmZ`BQ{S^l6Kd+Du]u^bW1T@-5|)itX#D)k=}_RD$QK_|A}LmA-BZ.I*fsoq&^xjm.lxuKm-0p~)Ox0Wz+jUbH7vlp$eXuhVBwd%z_BqLD}ULUmC>Dt)2V!QWc$K`wx?RBN=le8CI-GeYhQiis8+>W2Jqib1,Rd<D1F?^IzZv&vMkX!T4,+3`!#M]sGMm@6>?SNOoV)J[sl&>+bKp4<UX=I7WbMgGJ4xN?/DohrTkL&UXjWEzUvHB=fnl?YS}B-M+&g,h(tadYKV,fyI]G>>[`xqZ2v@w`:rOm-*e+kUWi1E":Cj<>m9s4zi&a@-|hURb.Vg{YP.8!5C(MQellX!6eV7$*E2ZZxvWPGhY^N-fFexEjEQxRkV=3Sr3){:$dQ$4R&2E
{9FHiks069v%_dJ!,;|.cB&L*g0hnij/bJhHzf,a9=)y(Y#/xgKb|6OE4^i25YLl3ja9l;%d<,5^,^u21P[/wOdGIs<)n75U,cpCvOZBK_A6CO
-F+XCQHAoFjCXGasv^?R`z4!/0*YWMf)A)dT5mOBJw1,hsj1lC.8+JRW:aYvnB^]J8qaU9kD[.LID4?bp,@nbl/GNKtU
E!H[!P_v(RMYP0p64@nku-#Rtk)Z~qDJ8gh)u8@3cD]@nG!)*OAM4M+k~ZN08A{R;U;TF4_X<ryPvg)ET?F>`*vgh,"eXI:%~:K@"LyyQ6p!KFi/w?U>w<2e7],Dh
%b+P[)S?=QrgsP7Az^GX3R]Z7
aIy`&dv%pq6UNnVc1OO3<+M5Joc7Q^E,$8V/oT$oBQY.qwe2|5vg2YQWvQaWGPRdICm`|=w#L1a:>"POP1sX_iIAup]#Zdt,kR:J9H-c03U`|;Y0RnG
_)5-_';case"hi":return'(s`G&aLWr/eX+r$ym1]xG28$GC*DIZ=D@#.q1Rn2cQVrsZZ1tU[Y,g~C1&*U"lll5?;
u_t:cf/7:Q9R)Tp]=f-Bl
Tkzl5m0sXn5U=vxqCH?w.IUMXTn^D[+W`u
fYs-F=dzk:kzmWc,`?JuR?=luQh-76m=K>HQAQ^Qu4091pK3^DV:PHUtpvrhq$K0h[TI?]6i4ousF1.T&{Av&|SNOT<j!k&XK(tEyRw-y;qY>EGN0PZEm<D.a)kd#{u2cj$Nk@l1_=F$FeGzL^^EDrnq57<E2k?s+uP0jLKzb4)oP=ChgbTWQ!w|5[DpfE]r>uF|r^aNfCK~=NSW?]QHcQ+ZEDTiS__FO$K]Q1J%A7MG<oB|GiX6H!8wo,jA>Kk|2qJ3$~kmV=LX?W&(p]Y2^83pVm=DMlktOn3!Y~v3Le`j9-A>F!ViW!#dOc%2uNf*E`/hB$c;/"c(p#f_D5.w$e#Z>.jU/mO&+>3En`+oq;EQaLxef?]:b..,z!RQp3@o@D0D"}hF
hCA$l.oN~n;]0&3V``AuffMoar8ds(QwG?,5hbFE+7JWs9nc=iaXldS-^$]6]u)*w@!n=<ue(]!Q"R%@uF1eDYLm]N,pE6(<MTVFCPjCB53Tfc8FlK>HQKCg^09X="&m`5E$gtD`HKmR3`P-VlU%VB;.ld>Y*A
szDGF!ir*01%W,]nePGrs-Pgm,-0::qC/"]mOF$1SwWq
)hYQpoNZdtj@Ty8uiV^V2p]GOYOX_fvY_9x>K0bBzNx;.!N;XpaX$;Wn,=8<k6^5jn$d{yM/$wp@H;,i}Q<u0JCk`hBdxI#C<*57Yq@jkLk0EeX?Tk"X#@*6>F"e8pr"XQKbuYqU)v-6k#Y$l-sa.(PukTu8GnWcggIP9u[y">U-1Bz9KP!NbFdB`H~QvR$"Mz!/ywUvZSP,:@g]9JaatYv*LLl0hb@eCETAyI!U$h`85pL;95ec!T!@Zs{Ec2wbB"FCQ9Or)T4??@:XW!aubOj@m@Z3uKXQl74ckU1_AM?k|N;E/83#]Ka-Dc"u/8r#.;aY-yA5J@TeyJIUK-s+}[02[1176Qms^#}1|%Sp|L>&snlsdsZ4yp
M%r!op5*).xi`Q0:_ufQ!C2_m*PDR./7$mJ/WeG~
{v#A1Nq[xoV0@3Gtjy8!oEAI+uu:R
#O1/qT*+TpMfdB@V8&X,>XCy>W!w14"<Y()c+>]B]*WlBogs@JDp%j>`u?|G5Bt0}OZsUF{bt$d>I[w0E:=>tR[uv5&QD9W"B-90$k,e|&6$0/5>U
N!5
$!F9ATUEN@D]u,"$xOT:gb2[/u?_&Irm?+8yw;&t>aDG;:m52U&qm8117"miCOt5M9P[axEJ:CWPvFSdW/{T_#GN~E~0}Q*x][#E@kp7t<Hcy]9b5TS)OP-^]1kGDw@/aSF?1gi*2`k:a!7*jI`k=TJ]S`Y:+Q}v)V9I|Q"Xhi;CTf#pJ`Nk$Ev?J)MA;T&YqcBuO[Q)9vcMV0Z^<v.[$RUD.WXX.$1J8Tm#a@(Ao3iG,*[1Hal?0M}6faQF-["&Ez(uw"x6hiKg,6o@x;^CgB0aWUdpC3&R+?JsUIg).[>^EO7q)f;m13LGm+?0,eJ(Xt0mTtbh,/K0nN^rSewSpVc%/B"8ta~pI9T)q)CFWr3"rjJK!NP=-B-oYeMY!;+tLF[J;b:G3R;A%"Ehig:9X(5D=j)8Z.t[JBSNRXID&,pI&C4oX*`uWYNO#7
Wn[h(T0RUYdk3F_e/~QK1*"-t7UF"=,jrrWd7Q$~,NX@eT.V3y>x/&X0?DRHTY$%/IiH?#G%^n#N&zdtHUgvj:=7oy2HlxD&@;*{:8
m<0>~23a%NcWg^<=a0SMl
!4LDj*tx"Rb;D@5j0j<D+(4CA%cYf"wbGJ&fk.J
z^#Fk3SY1<DX,jaKPP/#pL
7S`&;oK-6R`vNeQL9}MERx.93uj0)(4R$Q.UNOLL=wc#o6#n+vR;kO>,3NX6KzF#:_]h6-K;WvN}`I+4+&E*dwr-3qsSkqq@jM;_
%%n8i#}0Qj>1+SF?O4D)A#-;<@=7x=zb(cVxL&&aJrl,8RA8
?}fM*|lv#$w.N);&eaRw`-W^!_e{1Vu_2BxuIo?)72M%s[7ZLz-W1WH5ndEY/ruej]Cg[y_6"OP{Q2pcF%fvLJ&1ieF#dX#kb<X%J=s#p(;4y=_s8&c|^
5?S71<(1/%pHg.6wngP_uI9ZdliXBs?CZcuE@9CXIbI*j(#3_JcaP]j))39sW%/Gk(s+/*;j-&JJhaS7X^*7Huf-LR(=R&6!mL5]JZ^["_!~nV2@_nY<9818D18HDa[D^oeEUpmHv#nrmJIUJ?F4-DT
d)-xsA-E<
O&wDq)39u*`6O}v`RKYe+V,gVNnZnnFJ4AxPh:@{dxG@HCk7Kg0sm{0;B"5n?)+f4vW.A:;*X{CCc8UhTi[Dw^ecwb*(/rMCXv
T.cW
+SO`Kj%h-JK2cD:bU=4O9Lv4Kq`0q$pV$WCIsHJ#`bF{S$4;.R?~b![99jm"-ulZYS"[)?d=xDWs[XmB*f>0*YG$Zaf|;?ua8[[El|XVTysxX@
?OH#wk}Xo1MA;Idp0kn8@Y38>8YV>+f9IAmXMcvB!lN>6l<Z$`@,@bhc`H.`7hF4+XNLfl:*U
Rnn3vKli1VMbTT]I/OoA2B2.-=8Z{3$e9Lxl.l[U&X2Km=+"irLiZ
I%xW(2+
[3r
aZyntu^bOYV2p_QmJ:W8>MH/Re}`%_w4*
!&8vBKZP|K01UlgT|=bQ2<rVkHM=ut~bGYu;B
7aQ*+b8dkrQL+_2X2>2X:s2i.=0YYcgLcP@P8SOI$c&>Co%#z<|`u],-r<(>blf=s5bI6Rh/mh+OMKgnAc7Es1kgRlpe;vrxrtB7gIiQe31,(fc7*.|?AflPI8UsI8P.W8,bK*"sohh:
$u%_ny]wY[r
6zFzm:$9vx`CG#e0i;MqWG]qp|#S8Z@m46PM@P9x#N3yrpryYZ.x4^Ruem&<O<>V=8%#+BE+6{6"ov^]aCN{Y
B-:WSEoL@qC5f8iDr6"*.CjM=n+;u)_k?zGzk]l6jNQq<;r8p6aXnUaxH/uE^kb)j0L0?KB=Z)g~4=707#O;VtBSnn^RwqxU7}U)@La.>Q(^@9<4X`d5fx0<y42s3#Y6"2RT-X/L(=A?msd#ujk1N6O;xOJv[+([I(N$qca|`o][^VhWCcrcVB(CG=v9kNW92f/(p<9|+<q
$,)Bygn0EOlvhUTg9d58Y~GC5P70e%Op5-`%0v:x`:U!vD50_Tfvpz.ett9_yb$avCsuRgT5J(bs:e68n,I)4m5IN+Mug=i!%b<UA|f/]e*I[gB&ZDBS"9
K
UMNXu"#hXM]mGfJgd^z;l-aPIyR*5XWv2C@@C+tO(Y<X!]nlo3?
5J]agfgmjntRC,:W
peNmDiWCgK9y3bw`M!LxP|DV@CB~%K';case"bn":return'(s`FCaPmT/&=&v*l@Ku+m3QY*G4J~5i"x&:/11Pu.g0-jVM.4e}mQ$s&OSf;7y)Q-@kp~g5kBaqgVa^;L)hKRR0cJBWk:JWq^r
tE:IbAA"?fl)t?R&MhGl6.3ccg](w+r
b?3RB0lt_Nv]P5jIXL2}]m%wCbfiPPnWkp7l-./lB{cL)I
wyR_<Q5O(ox=4
C``cFQ#/5n16
G.@{lwu:1XKT1>ckdQpAhh_bhk(+p}g|p~o$k;9Hijg}Gx.jB4eRocKohNtMf$v;U$W=)(N+L[SU({mT9HLAq>J}yIBLp$3Ehm+^Wf#91lnmWB"1Z]YFJ(Z`GK#8-z;<-
;^WjIgL<@]0uekf>#Y"Y@V-uv/^=n1s&S7L]e3S,<*p.wpH*SU;yD_Q,t{]M*Hq6%)ELM+*MYoY0L`N4iN.C*MMRCovik_ve2IZ_2+SKT*;UQjMEg&]@4Te<NYrfN)W;_RbN0%>=k%ds=|Jfbky*f@T(8@q1Sou?m*%~)-%j`V!AYW4XhB1mTqoRlh1W[a9UZp/X%vSJWow^g(Tk;Ui:V7Z|/R=M#lUMJR[tdfN5(4&ak-i|b:Fv50uQ^~p;Kc@8H-tYmQ"IqvYXG<uWZ2;h+Q&|"!-PapUVdBX|YqwWdE-0irx(NZ*&]2=dmcEgK?lBr(2Ydn2J$-1c)F<yO;1G!aI7=`ni!`>+Y"N+KL/4>Ewm2h"#,{^YU06BP~CggRk-AoO4Zv0
YIFx-^^=01]m_$(+WbP#Z*cQIs`f%zH5)*Gc^:!^4v#{my*Lm#(X4f9ZGrb.K`Kx/C@LBD_~"]YC%Fq@yc:b!o[{u.^sNm(^UT
[b>XayG*&ap<&-==,wW0[a1!+TpYF4>no60qVN/sum*wL%fbc?JQ|[97FEn;
wVSp@:;S5Tv
r,6Ur%bex
fDk[CYFge%DHQj_F?DP92(__Nk*#X$5fOB`6q?
t!N^-&MGd$MN]6}s"9o&$%ula[(!
9LxBuIh))U36S"8C:X*:Cq1I7@)).^tk,>k4l5vdN_co!;ypg)9ycY,-Z1TD8;K]E^>*C[A?cl@fF)B-aV+]1gm.Q=wR*:
vURq6cDeTON9)#`0#ou@Hbjz%OwYi!-V=yeBP7Y1AmIW~NEtT?xGX6zoi5DO/P22m[Mw|Q`gsgc*5uTe:Sz#d`>C2dQ6dsFl%F?Jz4ssKQmc]7E?nfqE=){XcKEO,FP$`2-Ve-t(n(.TWF,?uLp#xOJ%3TOI-G=>B_8Q.35P,15WzOx1VM4sgI)_AY*%B8fnH_YjZ.-YAF>bYFYeWl|`frBoHf<&q](Ef%bxkw.]L!m@iHr-f8s)qZu^WFgG)HxDwv<gkScDh$2(nv$X)U8!MT@ydQ-?y?.$j!d6g:m5-TC<*.+Ed;N#"d23@vTUIK=0Rf8ao:Qogqq8St))J1mHZ5s%39^&mN:IG]w-=]%E3+hF8Lh/@=hAA;KBsM>0F
VyJHj7E+}:AUvI.9oldq<E"/uf
qJ]"xL=:_}t[6//Tg"_DYFEr6X;]F"
&)m;;I<3vRmXGxOK:Rw]-#BtI^fcHN.x[k8hhsHVmliN%O6LS`I`_FRkZT>F
B26(kR90F2C4*9/$=s/cbi_OY{IP7/0,ar,huQ%5=HO0WoHoU<(3iPOfdO,;7sVKubxN]^n;MK9Cxq+9=w,ioXlSoBUu,9d=x0($12$c;mADk.r<09[2^*8Hut=N4"P_6j7_%}jEFwfT${"eWY4T*jaM/DTEZio;vr!dG8mUDgV#`/tf5Fucj^
qRVj$eZ&)8}<)X
1>txhR2[$Va3_J&GlNB!4|I9J;p`xrieCnx$k
Y/Oou7alTkC&DTZWd+B4qo`8i85s)a0443P{cU(1+{02N@t!R)k&L*qO9CpW[vYlO#N[e3;U+(4=Xg(}aqA+hVu;WEC",TwzaGkZ2ElHT(:;$>c}V^YYdCpTc#NjbTu[+6/?5yLIITQ2tzk.=]9DyUPR2?LRf}74X[&cnN_})-idki3+Yr5,Q|iz84u6m=St10S[S9;z7i[R9|j^-jI^#<a&.Ms=[]
ooIpGZ?!3uL0lUa>y#9Bd?@(ygA_B9TLXMZWzC`D}GRV,uk0RJPB@<"3NQdfr?&w^1B>w
KDx9*GiqxA-[DOf88^yeGGX@n={sY*/-@5KyvqN_nqECg^wp-ck4c$96}L}7<+~_B4|=]Ba$Ni28wSuq]&^+?L5t1:BwhpyMb3-l*1Mf!B/wBK)AR[n%SO_XH[I.}94,VKI;7i,^wAS(5Z>T*EyU<Noe3i5v*8n
m!fT|*|Xs1
_z*m2X_89]M/D3Cas-`3&&jm^PJ<!:M{e:K@&/!StDeW2U[@4OpYx=b<qw+.EM/5vj
O#cjt*0t@dX.;lX;L$_Yl>zKYR2!mjS:,HHdLd3mA.kF11sY%;-/#i1UQLUry;A!8gC"us;E45iJ3fF1Y`H&r[S@rTP
9=))4.LI{G<X3@~_NMmO;khfrI8d<T5C)D?%$5iq>e(HWd38SP9#Y0sUKASqi#P0yno9[wGDl""WZF)d?gPVD<A-5>3w1wlwc9i/wx^HcE
g:_NS]CivzXP`rUg]SUo3QmD[Yel^`,}TS,Hg"%hn*5eQ7p@M*b$>=1L#v5^gA?cax"Y)6
s56d"h1d~i3k|[8b&ZgKj[Q0A>g4-Jnc5C)4cuKv"=hQW^u$o/rCX<g1VS{gg2*]/rU&#d-0pYbdqp3[;BGH{O@/rbx0mBxyGW<YM7tOTAN*;"u("2{g==$QzYCCSDi?8:#V&DLQ[8p40,:bQW5:
AU$_ta.a+)cD?>80s,c1^rcbM]"rleUT2a.PfyAMx>pl?Ou`>f=(T7L#c[5itfOda&5V@ZKl(lJry4-~?SM(2fMSf9h&OZK9cQ@Ok43z#qAok6D9p5w/axP2TsUDZ<hZP^B1nr(!T:7n)nqVpSNf1K.DkL*J7O!j)fGP>%ElDC!Gb(D:i-phqO(9;NDioS^B=Fo|-.QT<n+C=zF_WaZ_!FVVt=u8R7(>8fv2O(HwJw!mk&QC(6wZ=#M2SPqq>}D1Cd923qRzf{"2HnmWjE<9+$rA/"
_h]3a8g$UX67HVZ8tV~y4C}U+lMX&@4IOwDoK/mo7XL.7)}G:.n1/am:PKa@2h5)+
CkRc1YR4Px~YSZP<[=4oE3ckHTo&Y(O9&h0($qX!rr2&F8VJ4,cn;FGTu>zrZ?+wZQ}lD*q2[v,6qc]5lpX4.d87IAj^v7b+4r]BLNRj&xU>X7alEC6w|`CgDROa&Y4ZoFEUA<nCz1&Chj!uY7MZ}awa.B*-<k_!:e]"Zf&t&WM!av~AOZF4(
|rxq:xY>#T>K>7E7qJ_7Tr+#N#q+1%sg6JNcwlXY!7Qo?6Ft@&Q_dtK):sP`A6x-P`6/zxQuy_:t5VayZ8@]@.`_T4
<D<IB11SvApP47tu3gM:j#ayF_6onl%{&]mXp,a<Q`JDsQY~#FTR4e3L(qI+c,c$%t0InC^v!IQn4Bg!+G*}3q<:-6gI*yc>RP;i+HXHeOJy(c*Om6;7E*vLJd8*c<Va*+^l7`7`Fu9g>jPNj:rmaJmcnCEqt|6W6e]`@CKz#L(te-z&moQ@A6G2>C@c0Vg]179cjk(1K"S.X-<Mp1gb?1L+>S&xQmIwU}5TJ~a~Mq`1TE2-
55ImCB7y4rg1Q
FcBs$!1yG';case"ta":return'&s`KraLWR#A`otbA`B6&qccjPS3Qs>Ny!=E##dQ(91sIx8;9!,s=`vf7NQ9gSxR<.UC_wT]RNqOSX^#muO=$|cF>D=pTpMiMJ^-7xtNAYqBe!v7]BB0CdM^^-`_J3OBx:B9M3ywy>o!d]R]Motfm[k#yNc+Fah;IyfIaHmuMsW]z$^Nm[jkaXe|Cf@Yj,ELV?VNU5m:`s$27oaoj@AxJAswxPwvAnL<kXdoB2F{LvTwsFR5$Gz$z!l*m9y~M[1/rx_YnhvD<+v8G`T5a3yu"&ysg6cN9an=OB$Isx$c/>4Z*P(1AHS}-Rt5sPXWl@X;P$yrTpW:OcC@qnO1<p,Fz#J?$_Qw(-sVG6G;v#^A*HT"XU#(K}p)R;3_4zO["}!|%jeRMcqBwrr2W!,HMbI6ujx]QO>0^E!o&mNI4tdA:{JnxH)Xh=YEurt|*V:9^>*;F-Z~29_V5<!k*^LF$EvMG0WTB<!Pn
GD2{Dw[TZVobcd#)y9G(/G!8!.*
3)`jWu2$ZVd`8Pd2eC%ZnHk1AaXTsRJeVak`Ivo/@,_6f_siP[E7fw1B0fej.JWulLZ9aJ/^e.9ylpat;?0Nl(*eM+"
u,kMj/Dtd#vwkoWw?Kp1v#.Z#sPrk"Nz7y6|Te`+7*+=Q=`BOctb>J4rvg;]x+t~<j%kLQi-1{2Y!F$=YrwOhcf!1dR!TUr7cudv?X]EQker_H1;L#ODlXaudxeB1nT7aOF(;F!PZD[qX8U0(oBXgt^g;fM`6;`i]k[
8^l
famfBU1As=LX9n6o2L=:OA&N5)CELB;t3A#/:0mg6-7=cAN$$Pb0AZ25hBbD5JX6sn;vufG+]@^NSb<EE{L"7$weX:dJ2h[~4^!~m7o?K/(Q__paQl%bJdLYGI+;C(74v2O>&=T[H%b^f":?Df_tqTmHEMMi@T:1:w!vK(Yr>Nl;2;n:>8=*3,N<RY3A0>#qKme3XXd7VKYXoj("@ENpMuFYIHLR>pJ6tJY0-7;NWwT%N4E!$%ZLC6ik-!BDPQRJQbDEl5RzBX6af?0>;Uv%4&3<e=HS`:skj(imWcFveg%1/co/*}2(F+C-s=Gk-7a<y0DVtQ7JhD8+EdPX_-({DFpVtQK6VRY!1
[bEtW_<@mOIg+d8+M@[!UuUQ3
oIc-,R^Uo(Ei_t[kL``VOdqP_D;i={fNh2w$x22I=UN*qKXyn.)kr,Xbdds.>%1UIsNF>y9|Cep%+4.$hx
ZfLs3CZ!
0OmY(%E$r$-~=1m*n|tu$,G@!09H<p:^Dk1kN@_3T6ov,<w^Vq%Ec=6vp`%.$B!c;i2BV]`8c+2&H,[?BJb6sGK~wA(t)o,siOA,iJeoJ7D>dLVHnJfFrWD+-FXE>O=_>o3-(.[wU`;r<sySR/(%L+6DShUQTR3[F6tt;)nq7Sf.<G
~;j0]V.T?8a@Ew4dMwbB0W{<rx.
*8yemezfvP#o}H]?~eTWqq;EZS-e}y6Ir39t$3b/5A02(Y%;21rUQ(/D<I$vj5MhN@s3~iig<qGhc9yUO.hcJ?v>D8MegAvTadqQ1eV"@)pI
L=Arup_}0n_A0W^u>p^<[(=e-Dq8UI/45g2kQD.(H~?QWz4"^X@WfyBS;+Ncm]0{a265DoTEF)-_qjl!Zf$5D~wB=ZLe<~
{.FWFia2}!W_#@aai:6j5OSVC-7O^c0/&+*#2f(`7tY>=Z2
#K4nkc)l/Gi#0
&D|/8l.NAZ92kW18Uodn582HDQ7-TZs>:x<h@h]QtG43Q#Q_$_8S-2ol*2FQug}s&,|;k:X.)2l*DrG:z^@sR2S81VRFGby481")GW6iP5hGv2&5CqC
D_-dPgQAUk`1H+`X,Vn;+k.%BQfAwmj&FOdV+@~/f5-$rf>,gj$:$WA/m_i3oXbhyhb2^)*=n0x^2ehyV-n1eZZUWdV;0fWE9qI^WK;A>x<0b^ypFX!]3:IgMsSAGV%g<)qt~L{o}
0/hisEn*K#!!i;882K"O-DL=pu^X;0)4%R6V)
{g6"zl6(5(6$iBYal"(g|P$K?>ySM#&n%gfm:8Y)aBl0qOm^Ax;+xL).!SS@:/GU0Sz^zLUf%5CsB
e-(7.*[4f=sTWNrM`Ix`+<WHB.naZ3$ggJi7iYb^%:APm#}2.ZS!3/%*?j%Cr;>[~._se2YYGop?Y"J:l@_L>yi*.4#c0veM}g"7XU-5ql_K$+8P"YH#r&B@T!!3H_WTGx"S8M>8(4!h17Ve,>^EMMr0bB),W_fu|"*)ehT!tex8<gh+OVdL92q223
Y8Z/##293xk**Ct{`)@D_<]C-1/]B^
aeY!kK&+}+BW|3G-|g)%Q>(0I:]r.Y_R#PLZBE=uu*)QT7F#j*>:MJ=#!QK8zjQ;MRGLmaKF!WG<ZP1gw
xU53VL8FbT/?-z&Z{XCMa7N)}(|-|#qZ=hi(tkL<.JuSP=0+QG]cFP.^05KWD<lno(GD
R
]^tRg@n<*Lw@yC8bAzSIU@mx*ye=02U^+(;7Ix?L-E+sE]leAG./LA1)a6e@UfP~&F,=f{i):W!5.l_#Z:vRPeZ"T(U:gJT=>OP/V3jOC[D4N
4gSD!9!-C5cFWuL9Y:`o0"PD@:#JuE2[$Hm^rv$@5zXM//aH9W][:jg%l);dVjiQ>VcA1D.Tii?s*S#9ov(jrDWu4]k2#<.&<XT@/W(nMtq
_yTwU"A5ZY$h5S-2*kNEk?5}K&q5-y37qgf.2ydt[viHB73C7(r
(e6.,Vm!F:vKF8=Znf*(-oFIGjW]9|n.@P$#&iJ^;f/ZkW^B
9vJ("63I&7z/tOw!cn4$<q$KElQjd+6<ESlceJ6#`WGb_`,:P#EYrxA_fMI!,&Ine3#q`N1.[6[+j1%uU=5I`pIIeRZ!+1[8Go|8+BBlyv2s4tZNw4Y3K<+)r_!cukR;$?CEu[69|:Znm^T`E7Gf~^K?9ZUUN0;vzf-X-FaTE[D:dgCKr
1r.3e"FN:.`E"jlLwLyLE/p),=w^*xmt9L6$rIxs)gi%grZkUhU#.o#f!44ZMAnakQK(ni!2=]gat%h1~/dMLsofst)msn+d[B4v_-Goz?0F@NAyHL4"A`s2:3aPyH5$b3WbY6Y2Y3AunX<5K@kFDfl%;.=Gs#o`6CgSkpC/E&/3:+?XUyDt@A)Vw:nV>5?>W=g@0A),TY7Dgsaozg2SH355w0PZ~(0_JViTMK5Xt';case"th":return'$s`<^5H.7!MMlwF7~.-fq6&hr8)C6Vpxz-f1ahi`l/:$hSfV9Q"LO2zVm?C]"rJ6T,+2yfqP?7<
gL[6AGtJg
vG/D(ls$dHMk0)fVvF!P&f3Z3gE[ElnXw5HrBC"0Cs/KXIX1E&aBO8pJx4d+CJNFGf]yb7NBsa?3-2_ZuPs8)bQcRrCSE$a`#G(O<S;cAGjHMNWX:5XNGSB.%5>6s`_a@kw4vtU.1JQ"4U8m)UTs{9jo,t!PvVsOo4NWo&If-e8YVd:"@Q-?y&Y7Dj]myk6tA5zcRe^8NY?GH#W!+(J"$Umv.;K`UFxD7sWkVT5mXqo4&s?[=$ZQP]K=u$Cy5W)qnFqPJ+0)Rp$@ZCCq;WHlH9?PYPj)WTvrpO_Y/tyP%GGsro^O8
V$7W&m0b9(#HZ3kGY2Ho%=9Vy`sox`3W/D7;egwNW+pvwnb/9%-ifg$DP<-aIiQ25)uR:33k0v_+f/WP.B"5[c%agToNp+&(TKlW[L|I?<FslV.S*OL9}93Zo.`fC*mJ6WqS0*whlvA9op&3GwCmNHl[m7KP,pBqRws!=DoqOJkLA)h9[R~0`D8vt-/.uD3jMC>8ln,w&<Dv(/*mE&i&c`$5980q6n}D"]SHSkp.5[Z=)`{L6ftF:P-9vyS`RO-/w9p5D,8,b<<)f+-"#cP0PV|)!Gq3%a9%0Z-Zoef
0r~IO*gJ|`;0`:#a<^YEq&3)bT:gkKe@Snf7yLLEJX^<%7%ROt"iNC.6[hx9pTI[#P?%>EX8FZqaa+LlLHPgs/Hu^,Tx-M7h`yjj
7q"Tt*BE"{,<$,BtdXMVJn]`U
8@@T1kbbUFP7;w.0dz8D&IkvK]:iXi$/*bv{08uw./S75kKRCOX"2USHRT2bnj]coI7i!1D(OZ?FA1"bx63p[)fpV;AM_WnjQ[s"H.!RPkweSL"&Bxo|pPe6q;$oPwQy+:l:"~gxy.yGb>g>?1-;"=.~=sOeoaizQKh]!bD2wTy:*{%:a)G^C5GsKRp~c_rwvdur`xR:w>Rv_8=BW%C]KFPVwAdgRT1)lziV*GC&-j)c/E/(t,uR$BYYXm=?bbnU
tiD2$xaL?C$ARa|
Jh/NG54:D^*+`_*hiV4g0?Q[=*nR!MC6Bjq]<Ti]CrH*dc,I0rq@B4CqFgQQvc_8!e;5~[JxZ02r3xMVpJq8i^o^(]Mln[!j1O*#04EV<v8/$<Vq|3rDiUSs0$;<8w`bK"PxDpW]j)~Hs.r!8s?9Y5CqXLM24y/nCAS%*2~B{R5t7.A(5=bW&F/CQu~g:Y#S_8OOWE
/tStjLP>eD(,ar:lRE`O?TvU7GdN&n>REQk^eRuo!<5`j,2hF~=r[
3h:G^-,M%YNvO6Q!E=QuOD$8e??",i=L=ZgSAusHVnUVoI=qSgyJneAWO-ps]wW0d)CR<V4`:@w<G]Ng6VR>.Bd|N#/yy{`,Mj#"SkBu3nIx23N
`(g
2rREOq1l`loZ[<4+k?($7CA2.E<;>Pgcj3;EG5JQ^$+L"C_{+NeHZ/C~XO[9G{B>CIX%lL-#>Ha
q$^U]=R3g~<aaC"8Jd6kk0;r@1qJ$QVJuHR0r5us"f.9?V]}XW@ReoJLjEV%(HsWt2=8/]/#4I(:8y0sI>Ny43]7h`E>a&g%7?Bu+@.sM=>,s:PaU-gz[-Gp2s[Fwv]a6a;jPIGz-$1G3Us0Um!Er7+.7Hm!;Kp!D~7haHc~G%E)@)7sZoHWxt3DLE7o?=$sY92gieP5*/nQF0pGUFNBk2Y:u:?K&bTIgzo/!9ZfStsI?dLhH$=?C<x}@Yc/f)+/dEx>(Wka4`W(nk#IIAn0Z&hDII<xyl4g$@"nR"Ldx-(k?HNvTqZm8(!Z8wfFn6vodlc[,Ka=/dx})8izMgl&S{Zw:"3P"=#.1n-iP7W2)RLa2oIl0`?-8MI%QTsqaiGc5eK3Z-2|UE#Xk{JIJ:
T6#V(,M23=O;o"V/T$3O#cfBfpnvnL)[pg;f{*hS&ey>y?FUOjhq5asbk;lT9=Ts{[d)%B0>f%"t}vngP<FPd:R^[&wVo-wtjlKR`PA7BkE^G"~5E)%H>Yoy80ui,Y>/#n~QB(|5zZ^WjJU<%8{W.Y?.""m)n@|wfiv3%m<EDj<u!I5sJli<kf,+qY!g"<jrgU1*uwcBr,W`yg$uBB6YeB{6x?eV?1|O}h$r;X(D*?b9xA^1l
~<4EzXH,Yhts:6L`2qYnWe7uEpzSS,
hH>T95RhwY[]uoq@nx95t10p!e-XR7S~W&GLYlyp5B/z=h4#=/FiW)%~(xEgG2A5j{>0)4
N5a$J@U%(X7Stsi3X:=S6
*hu=^4/PO/m^w:24wKiW0.yOg?fFa#I.1OU$N)NjmFH8%aK@Y<iHY,8_
cSYdl"e!Wr<&Tn,~L&YWVYo/?6`{:gXM-zwq>`"*GSOr-J&UP{7DrH%eK{ocAT=,*cQama)lay0qk)Dq%t!4_T!cq7$Hv[jYLO@r;DWhD;E!v&tjN.ph4FO?Jr^nApaN0&T*KsRY?m<F[?B;KeF_o3O
Zk_]rO;h%rDf?.j&X=omV(hvg(">Puc,JD^`@q&/WR
AIjP4>%43:wW
.n@q)[?Ro,4*Gi!dq:"J
Z$IqV_sW!Q0+KY33d>E6F/p3*ji4>UnA(_GT$l_/E';case"ka":return'%s`F;h%Z*hoq40r>lh4Lp!}_B)@B&-@-m*.+?;1]P04im<Ma+l[1Km~
$oM-}/m%y3~5);b)b0<>Hyq,cxT!ib!vtAB!Pl?!Px::KH_?U``uQJ|r(n2b(Bmcx!1(tn^ev%~!IK<adWZNV.P`pl"Y`@Z23EB`O9Ccyk@Y^oBwP"H5p`*5v@JwBj}xjYF4oq.J2UdxC#H#+yY&<08
ev7Lh#h,2E42h`/c|8/"@]`nJ8C4_-j!TsLvIQ]uh/gV)1%@W]LlLHr>R>hu?OBZ=;KyA^GB=w=w3vMTy9-Z=Z>5"Md+?jQnlM@VjRHQ)y4S`Ok$s#6rZMF%@$sB(br
_a^h39%iE$pFh`DVoDyIv7Q6^L$+cLd:FKql|t&Gi2R]b,6;W>*TFB#34fkY}:UXBvtt:!oqS!*61<+v|j(
l_8B_353-i~.C.Di84>uKN+fIxA"I&S6d?x+>&FJx:ZneL3E"*0n5:3and4?8`?0KbOrIK:Bh+k4zdfC1B*Yps{4d-)M!NB7a?~s"%a:a.*s{-%3OVoq#f,s-%+[=A=)*:MPh[*O~!<pGS>mtrB#VJKUkv_w_`MF~/.c#y]-aESvl&rj<FFW`EWEqOJpIfp9:DA]/RJc+YY$yWTA,%x@U%PA
hky+u9jEBbppFU-Nuyr273c96H2>NI+h`UxbE
kgnJM"1NK
/8k)G8aA?@KLK`(0JO
6htdUq?kxCMS[qF/@*]l{lhp8;A_21z&idxAR-$p$vMm=x4&ET@$(fuf+Dl"}=xSv)O.uJOxC=Uhw8?nX:VmS80(M!1peUR+2@IyCUmGB-9Z?n8.0-<_<N!vi")p3@I]U_]6(9X<u("OqPYg`A|=Y$yus<3Lkop8_m1e_DSG5ov&<sm0|oJIB2#-X3q-wd:fVDSR2y@!x(duV(p>qEcs,IA6.L_I~8kfSqRsrN3XNQ,r@f{^>ltbB:2?lmBW%T^EBwfN6/70:4z@Eu2]d<R*h9;X>TP`l[QJnrv%bbgcRQ2Lo"|
+P:4>3#2$*B5>p/h7(_5Dss:?Nnc+#pfitOfBXXG!I1;Nr36@9:CX%:v^a2]Z
:VSDfog9VaqRy"MUl_K!h&xCU]Ov:fypq-fx%I?9Dqvc9p"+f($"GfOs2fO$8PJ3D+vodmNp[DREql<eJ4LP
8o7b%"R>B#GFy8#4O[B^dSy:mPKep4(s(Y(-A,jauM9n1^Puj5"B-d2QMrC$/VX4T0QL(R@!=z08016O<^t-!XTl3R<{2;C-xL]SB}#0)EOU]Bc6)W`oUu,%d)"#&#N*
|0B,

N[qW`_;FFFS;RH<M[*%fD=]E.,W!1P_!7J._E*dR`PwOL
5v1jrdi_bZlPt6P+pfS)*0=KF6%%oL|%.5InUv5Dh?M^d/jI4dT*]Gw
bpi4R1*e_`-M2#^eGOR0y6W.n(<2WS^u(c45;c(e3CoZ7/#]G7W#}1tZs(yD!
W%Yw[
dq@b/KiIy`W-`
SPHavQiYYeX)@Q-aiX_(lLUeo1c&AJ8=(<JH}Op?9rYW"*C*
So?$ntc>8
!kJ4nJb`[W/~?4o15
XkZ6(w-Q(9kGrg]zt|(EV8T9CnorWKB,wbY0S*^]i)jhrQ-W%cOoa[vreNv`P1S!-{02X@kO=^uE1%XL
o;Ftm^Q,@*}U=GKY#LeQC"*w7qao0dAv3dlpJUlx"9?YTbh4b=]Y<jk$r09*<#:Kqj*v-!1lEVN>/sN82`SBM1<h4BYM*%A9R;*cjD
Ag:F5EfP,N
b*JGC_wAx9_R/>E6DNqJr${cXL@Z&NDMgLQexhWVL_U`MRpCcDx);?Y,`&Q2-&U,rYF,U?Bd}Us
(jRW_`Z.5N<6}X%@VWT`17T@|cIHZrw*|R/?/ri.`9q3w=;Ag^-gNb3CwbPfoWf17YO%60%-mTO8vSh:<R95cUoIb"=y=")8a/bj[tg
Vp,:[wy*:iy,0uem5#x;V#~x_Qh<y7)fTVkuLJ*P?<4HoJ<+=A9(;l&#xBV@0fESu=7W[G}<wG;_y"lX|
#3)uC%D=ToTB!D!-+.dLoSxV#GzElRuQu-|5SLHfs`OAd!jGb&MOjJ,TTyOB&w]X(D=ky]5Vd!
`,haOq=V?|u(F0*q(S;rU
Y
v7^F?dxEfG<4(;3"%@[.7
kY({l7TX2~]r).XR;dS?
9w1D4=cJZa9%[^i@%Z,kF#)gKbpEOO^ij061XS^s6M%=*MHjMQZ
3lP5Y6UH_b_1&&T4WK3$9rzds0s(!gKr#!tcD^$<tWv1$lC/d;6xRjxeDfg@)fkblHU(=i_Y6l]8CB<Iy^zi9C*SSv1JC`opt?31@1=D0aED]g`M_s%UYB$+Gk.Gj+E+VBBNL
iw4!Hyn2Zy`wN#ZnW.j"VI4#|5U=R9(&t<KJ
VQ97OiQZkJ+l@;wQ]
c(A/P/+JX80FecKuBc5tfn?D0ZP]_sN*
Qe8V
0
Rf>!aC4]g!TOBDI?Xv_ba:cGq|14.&FS#]ucjIhq31v0/}>*ubI+xF^(*sDkUpNM?6
T2J_[E3$Hu^_kl|e~6E]IkRB
U#J6^6JCo-gAAnRlg~!Qf8CYO~f:V3?~?**y<@9f)Q`:"zEV%uu11/_zKrf0Yw/~;]7PrF?nb
7|
m%>-5.]D`WVQ#,!,yE~,o+AHos-VH2bq2k9l<4]ANy
Sz@8kYkWHzu@p|qTgYT}Ti8ymsZm,*4L.FtPk@,5ed^`d-jp8]m-@R64DX[P]C)_Rk.u#f3HqE2@:Hn(+;:vB&_OVPKDwxMN<1W^b~Gh/esIsM^>[>:%[y/KaZ[8:hV
co:Fvxg|umtSM<qGZIjY!PJZDL1WGQ^e8-&yfw)Bq!g
bZKWTG@
"A/A!nL^T*YLr+LKR)VlYled1()NX:;_7yEp7_kA/i)a6%K5`p1*:6X_oEcpF>uEkhS{f@qdX!9Ax$vUy{:rQD+naZ?G[ykM-z;F=V>KS,a)T5X(^.ZtLt4,:tJV`{Xn$3z$3QUH*DN]auHg6931xP^.
d@g;REA)GQXb>KG
En-_#nJ(TaT*K&hah*C4}U6%V>G,++oY?Tub
EggOVFIfhly0bv6d[h_<Ou:;.v8K#;NMJ;0;ecIsn[T6]&:u5cR.ULZ)VNt#^XH]??f?mM`C<;x:$":>/^.Ty%b.IDwcrCWF+H<w:h)NKVd$5U.<]cGEvopydsQ~5uSl!(j~9qU!@=@^dZiS5+(x8=K<#-';case"ja":return'%Zu@q:{Wr1,kg<qY8n8u6Z8sft$Dt:BX5L[(+J=h7Km2.)FS8BoRRd43}h7tYspF&M^<2&=Z/F8x^UYf:U%O_?nd1s("9;:0^x"LvXNA|MHc&21cu?zN`!Dl#NxCbFWphLIxVqi:]6#_ra/uriE4kBs^BstK5bXK97Zuo_4iBWHXk=)M;d*AaphGoM_4!ABRYhG
_bX5QHn&`0?`lnR.XfNt*D
r/sxnL72tGtFJW3YIr]Xgd.#0+Iuf72lbR[8c}dWbu5C7>p"F8o],-C^gTi^op*I/uQ0I,eB7o4uPihkd_Q^NR3**vGSX^bo6Nc?<Mc<gFtPYD2/)G?k,d,d=j)tD9GNT+qhP+5=c1Ao3N3-F/JIqUI%!?*[t(.L0c;
<t*Vb9Z+N_5A3P8B3I;.LB+Bt3g|z")r6O`06<S+TXnMWzw%05&tPVs/kRJ>:Yv8t,0e3BXmV4$areq@B:^6cc,uF-J,uJXlV7>cCEFW4gt;f?gDV79|YNH3Hqp)j*5ot$y%=f4g>b)t!D>!2QC&H^?vS3qYZ<I|@4^%rMc|.UaTr`_:s5t3Xq&%S^OXC9C$@>k-IyE&l"!tYRAUS%;JrB3`_g2X&GjHnecsCAQeOcaPP#bI;em0+B7aiX)@NOW:UL(;O.iZ9wG"_-,m=L=8A3eKx%lA1%.nTtnzjzLEG73&&)Ot<+eJUG9}+@PuA
Fnc?TXjCR3*;G28<./14GQFYPriSBk^sW1e"%II$1tx9oQe99rqDemu|JH.obYAi-?94$vcUX@"ni#J)5Y"5S|rZ2.K+f@-"joySx_vqh}m+t!e$xVt(WDb)2rc18Ho56]Y(B6L>8CKrF@vug^No<Y#s?^y`GbWypZ4UYOvhdV:f2I/q>|X($7"KLvVlLcbkM"cTC%_>["(9UDXD0lg5Kk*(W@E7E1yr9.X#Db)d)mm&Rld0X
Lc89FE:Yh%#C4{Iy!Q5a>?DN"^:^SKwAp/qFz)pWk4>zk5F0
aD>to$
L.F(X8,fZRSqXxU(hFDdu$.L+OOIOV+/Ojp==Y5Lj(I(^~#f;1?pU9&E"e!]"BSL:v-qwZ6Vm/oUpzADrd6]Y+._nEAOyQ[$.h"{>It:dWQLPdAFJ,pL#rW?1#M>h}_g(9f#x^i3oa@G7x1Lk@Cfe?t6xbTTlFZ^dxKT[mt@dxcB2j/?rp!-<+^n1~`j3c-`4m]2oQTK.)*Ig7+9!ibO>a*bG]/qB8E&TY^;lI-HGh+|xL3zk?
[I|77Y[[M%0Zw5cAx=o>hK/SCH=;V(F$h9*$q*hxw?G$uE7v~$R#djN/3eCKUllIA;b#zuNNu.
pjm3*)!dw~EmCTDMLSwBE{QJ.T^!pT7|KlV7QG%<$#/1ON!C+QYE^>+4e5/
m>v*j49q/[Sxa!?V_8?H-Z9UI<:"j-=1G^3_gC#A3`g?o{>+;5eL[M:c$Tc)F2/~;yJOG9>qL%.OH.*>o{YE(f/d,A9i2s/)NyO#F=^`,3HvNS*4oDy7eLb`cXCmSm(_%3N:)8/Z-HaKlpW#dF>_yLs0j[26NQx^v~J%yGI`(:&Pir"F7#a[mXy#7u=58#[[fPiy2%9R@@[vs,*565!q9=9mg@5FlM+%.S"0Kvjt
gGWc?u-[O(;99C.qS2c:c#NfA!-0;V?M|f<PqkeG}9n6:6HH,!"sp;9xtPm^3-cF#1@&U&S<|v5QVtT6YSEtoiQsL7C#|Ht&WfxN[K2)&Vc0RDPH``hie`zxYE+Wv=btZIa(-vXT(%ET3SIJQRVW4Zz@GcWfSD(p<!x"YI%3d<:tH$~7"t*B9]jt:E=]
!D84XS+,-
."jT%s`0Ns2y*$wgOq7]fB=k0QZZtg%-aW-!0q+]O<u*%Pa)p)(E"oeowZZ+H+"7-#7{!v2,8B.%!#h,[Z)q5$("6w<S:#RIkaJt2l_mQxe[JTnji;4k[nrt+7*_A"6/hh1-B4u:?
Q610`dIm;aL&;G3q8Dv0bG(>C_]I91.IH}/lTBY<6<BfK[gwFd7Qz)xRsvAe6HwSibqj^{_sgbK*C{E%jdkMb@:X:Y/%C"1;
/Q1uHP2QuMAwE`#_yfgHJSo[;q!j[P5Y.St:m0gSeU!d>*x)D@rd1G3]d*=rENz<3Zv.BKowgRSdJW8qu/_I=Vm4G4`"b5N2G"9e^^3#R[5w:3{t}WpOO3mjz>BF>QSO;)<ueaV0,ghZa`N2de,;T/LCXmMVT3bkWhSl<_@tVL
>,g)8($-,4;n>4b!.q%rhh3
p012LSC#Gr<&:8VO_Bq[5^S+ZHGTdzM-,Ha],%n(]0=SEY$|o(x}L]z)>iY=3
ipk].NLpb/9o?)6ecR).P/x*R%QTA:QBC/I>SH!pY283j</OpD#E<7Tn.jsHfTk>WYqbe9c!o]!CJE2CZ<O+,@Q1;tBy._H5O?(";{O%1IYts8j~d9fqlLU4.H3?&DY|5sDYt~?+syT;I_mB#@ieCX1vYMJ~0Nh+]84*gmQ3?8)5Jgo#>;h8G;o&OdDi5OSM*{[o"QFi%Z!t5/
ps5H
qj,>M/w<O/]$lsR/ogssE*iH?OfX)_hstb[DdY/MwH;Zj$3W7m;sAMO=O|oRM95f-o
"x~9U"
A<=r9>D6Pi>9s".Zt3r[(@=e!>aPH-a~ey]y8/lVJ-m
/x#9FjIj:%`|O37mGiWUKgT~&(8cI"p{R]7cL
gNl@bv<.OsJK1(xNJ*.P?"9PBKDtIlv)kJ5rg@lWZ{71.F!Hr}^c!,tsO(xu(eCU=6iRu#1m:4@aUUa{/,+t**;0U<q5WtYTAm$SW@si<7nY-?7C]"!S.351SyF5N5%@g54iRjr?]Esm9ILF*3c1@~_R?ofC!tfYIv2*2Zk.(!BKKj2c0_6u1sfFYES/3wn
jqK{CJ?["c3vJJobuku2VExT;0Th!9Qm%GltC^L,6W%Pc8$<_
/v;fF/.Un|VJt_WJ-G1u4A[3rfm}@@3vo?Ih0tlVZoGQ6fxB[8D9Rm1Dam-{@/=z`<)#F|&w62Q7Q:#Z+~<ul"1)gQl%1X[^Q19}qR$-fc&,>")mtB1pk72Y_c69j(gScFQw#CZBeoe-J1/l;6KOvkNzDdoC
8QvPjUU]0E
H#QYBAKM$(2!Y-UW<D=>;ElAj5ikvDU8u*O[3w??Y9:f8:#dhlDw7]^<FaRijdWRl?94e@-<q
*gA7=Z-bQU%eV-/Q>;a*)A[/pt,ks[&$<ES3%herw%5eViKtqvn.5cB&nN.CZt!2,=>sX>G:bv@c0}8zBniOK._?MhG%&@#9(9"jx6fGKm;E`a6wa2VU[-k*J=S=eeS3L%tTbZd@B7dgGkw`3g6UdOU;hl?@^0EFSbm$U(X3,|Zg88$4xLpYIE-RgErATyc:MQ-pCk^M4"st,x';case"zh":return'$UF09:{Z[1jR|?[YFC1n`w760xpl}VW^}[nF0#-#=U3LpNf;1>58z)V-++&0@*Cl#T]JRe2o[3[<*&=A8C:2t3mL/$YyF!~"QPABB
ss3+"=lo6of)se`W|6{AxT3]aD-hc)QbiuUQ
RAkH
*q&[ih1_h-xnaVk<=NwOdM,vZ*l(-evX4!-v3e5
yMuCC/wa^RP-RDVpVT!X)n(Q|:e1hCm>(c75O`Y6iFmYzYw#$A&sVt)WgpF?Wvu]_/
[t<tLBDDSwqliv`N@a@29ZU^O;1C0A<?cfLvI[]wyGqXRJH;.B[Ll9!.F`?q[y5Wa]C7mUKbnh`D:[n0@TN<i*
yMPlM&c0vG4snw-!@4:r.eY]/k,0jB+lzVMqMhefJn2)^brQqp@3/*A-p/@]ct&SMH8*st(a/U}:rBo
+d~g>g)>%%#@`2k()fKC<H{rpa.L%(b/+M/8`]-"
ilVc;0s2N6QFA[ECn;>@KS[D?{#*y3J"vUxIpVHCkw)m0[*,vyY}*u-oGvuuAi7{:Wj3*3fBvK
:mgi4>}8+s?SgDWK$#jivg?TKA3q$R`p-b#:#3Q:)JDQp1)G)Tb
*IgS8i2*wH3HxIf#6R[+yLvsOaGN*G&@9xSQeA%APa)h-YXs`I[pYFJ7pRadX5f,(;ce02j4Bssror^+PJ)HuUar^<4*]lo!}H"``f9
1TQb#&MBLj
Gqe$E4=TYwT]e0?BlLYV%=K;BTSe%(qsfN41@tZLA[W5g$=o;4s7N4M.8)s&AZ&:gL6PQ47U0*[<TxGXQXb6uk7u!h#XL@7,[G)}/jref1wdh))<
O@lqp;auFkQg%]IT,il^/acPqPA]f!D)&s2jsoT5<SiL
6eZd,xZB^q1`iWSe_/@b-FcsTPYTJ{HbYlHRA8J?Dmea;&`U[oLB1KlkVa*PTh]@j2.KJ^GM:H,=o(h{V3iD"89FybV3l5*W#v$&W`z"@,ff`&EYR5.rjO?{JrZ]O|7XPyUJ&LLxAJ>,2Gx}.pu::(G5ij+iFgEjj7N5#
yKRiCh^/3{@L-%/V
<g@?H>#Z1O^J2N#Hsg4"r6f<GD~<WD~q*HgFq<ehr(c=I
QTj<(@+fz?b,k
<;7xT1(bCIH,j3`osD~Q?f/hyDfsMKzv@*A&~,w$S.F/I?@9z/J/p<]/hTDZyVo2#=Nxa?sEAFtfg[7;+$:
y,|i]Bj!`Oz<?4H*vk)v#,-ln9|^QU#T`$BLX[K#8k(-GoG-b10:HO3O+^XY9-l@k3@L"8ZnCX$Dq/)tMM/yUe+w>3!5ZG/bK4h&u4H*
tkFb_.HEK<j$^CJXFJKLnaX5OjMN1Ze:GPJ[Un6Ojxi<Sh@n$<iQ2K>vY:u;
%^9]#v}n0C6hQ.??(3
JHB
wVp1!o]hVyVaBesk#|lB!)?SCh&npP10nO5YY"y2r=&GCHd!M&1qy1["D5yRth(c7b-gwoAj/,GKH^J
,fFeMt9ri,IwW#&H)bj61RZ-1~[8`>yg8xo7,2MWHt"SCIt!dbe!!kkc.[h?lJmCG}-P86gB1QM6AiwaxZXXPvi;;W&,BWB
1pm}RXGYTRou80Dcx*M4B<as%p9V:QJq4#E9)Cb-1CyiGC6W[7DZ6q..+*,`xHN1^[CS+fe;D*1LEJK/?2LQ<XS7:1Ky*p/D3<Uv^EY8jwt=7(/
T7;&&?gUiD`b]L61c0fd@FN1_So,(+y~gLgv8me&pFNJX7g{v~AI;gEQJk](2F8N>GbBMNudf$9t2!NIfi9W7a?CWX1a25O[fj=`kl5TJJIq!R7s:f=[7.taN(gh8k9TO&Wv1)o.;h9J;b.2e%Zxr&UF<%pt<Br<0#&#(K-MD|$^*I@@7"9Esu2pFq*&5jbf?FR4sh4E?k^n%?]u3sdm/iY/a6&![zb;[v*SI$%lctX)]"7A%V=d)AoiS6#f9cUF^+l&b5:un))DmR69c0RhxeGTA6[b_NhqFF!,G&WI#?OZE!A@5gp+95yQJKU/Hr=q?s6"RCR6VOrMMS>f;W)iO"Swk=.V`m"]Q@q](;V^NL*Adu!qdPWNTuBP)e?rx];fo](UGPwCu,%vjG<3NG?0.(a]n&80-G=<St
"gM?6l|-Of01(<edpOP,2W;U_n|9:hEa[)iV$Nhe5gVZ,
G,<wt]DrO-PpB*6)L)G-|$V,i#E(1G51X9&m@z)=1ZlJ~.L0qUVH_Ae.X%qW.H(,Lgx-8XvX`p6SIX
Z4XHE~eSSuqb5@fQ9bTf+EK`bM`tph:"y*TM7<"c6H/./fjo67GQ6xQ)27B-umDzT].Dnn@u-LF<X;my*ws>O1I6dm)qXZM7b}OLPtUsVkWC%$b2%mLzI|84`}N+WB6mxg(y"9h>v`f:y&6j%+Jll7<ejZK&U-a`CEdsYMFv;G-vp-1JRys~m@mJ1RO$#-q@oTIdA,by<KUJN2r^,l+Uty!taxQ!!L,B>P`)Pn/BulH|_n47/YecjG!MAGKYbM"u[qDEC@`>ekk3^DxjiD;kcx5O,`M[%$
L@c[8bO!3LD^VC5rMocOy6:O>##>i["!I<3%_TbbMAY-bn~6O_vrhbPh"v[G(T`gn4r,9msnD(|T;7A+qAYK%7
qQ_t.bMruZ>_eN]*cXJrM?,u2G]YOfbVZ
$NfG7GBh7y=-qJ7*h/cuVqn}?=NDDJ5{)i-|A`){AL?>2UT$,]Q%+CEH&(op#Posu06j?.lkn?%G<}0yKPTpT]"fyh@^(U/PaPH(K:.W>
sI
HE:c(1b)s42S.;<V5RD>lk~."lLl4d3+67.o74.3kwC+j0{q"/_0ePj:(+<`.Y:1?:n6gqv4+1{
uhvmBF>4_7_!,V0sFFW)~p"Je:pfG,64I/7HU]OLOR/*TZC)E@v]OIi=[iIG5b4f^*-T@O80wIs@|/Y=InS/7]%mtM(vHJ1=rrJeb:=IMhEuWPa(u"`h0';case"zh-tw":return'-UF01lQ.W1jvhGW9A2@LGK:]`FZ2ID%u=Xl"~"FY)#Q+-$xNy-d.Jf"Yke?V_naewJ*cpWT$|;NV{rbb
NOy"WT3bxa527.mZ7[!w=a2IQEa.vzUHW{$$Zi]L;`:q_^pW.-4B[elz]Lx7"lH)<I,z4>?hr@I+,9i0G*6j5T*s[&CbWSI|K7Qe6hbYp$,G`KO%J=Z25]a`KJxB[9-p<+lF$}Fq8z2]3;R)lcQRBUevBqI4/
6~8al+SOQt$!]UPiB8lZZmHXewR`!LwkEbt$Ue.wRjlTmAD`W_ILlxg20fJJ@h0Wvj[z<$bY0WTK<|-n=>t0!=b7hn%%p$;J9N>paq+FZUHBJN0f;-3|ePc?,1Rcc5>Z^|rqgqkl*Gk<
):zbu.wpEN,y>,O^M`z>QpiY`O`a=A{qjS<&;jR=Ix~"T2f9A9.s/C"!J+
]3_slz=])G#&+1I4Jl_wMwHquL,:rof&0*qD9-NPgH;g(JC2x%3s
L>GjM2QC?`Nn<5W[7676%g.9*G~up?ZwE;10"ush*cSH9
1BVYHm`0YZxWnFyG|wUJXm%=-<)btnUZo<[YKkkn<uLa+<+MA>pt`8>]1Hx
.v:)7]EWDFl+g^|i^Rp()6we;w):>q*@*IT/#^47D+rV/_1G+_XJ6X?
sENy<;^l[PEr+5/0&btf#lZs9nychu}x>XrxO(jy[^jv
^2LM^sb22&TWgP-WfV>4cwpS#fYft7uH
ODUlnPp!i,%cd3|j-({2|MY:~bF+A,H&mR#WLad8JyOgw?!N:n0ZL&;qcA46`YU2|P!.@a]o};wBQ)1YV.7M;TU
mLRNMr=@*673WLB)A
U9AZrDyABhfJltNPE3lp62@+lZ.(|EWX^xM&E%hq.mHpg/Dt_ZjUz-+nuQ6X7<pA{baT<g0SfI8X}R;BQ4nybAR[`-M.wxSrJ&~U|K[wdnPt?)2:}Rdj;(Kz&%x
+<J%dW~SLm*l?a6oF3XjUV=ba/PI/b>uznrB%=wSgYrM]
eG9N8]*QG8:Ra6?G%&zX-@cnk@dEP3b?il)/=tPEQJ
mWtUH=>(lCDkCL=wo|5cFRR!Ummiwt(B[Ms,b^DRUB2RvB1ZAt1LH>$Wn[3EC^jx"+A552)f/hekMe<nGm*9&k6{%i"NbF3Fd;(<C:c514x5uvkZHvsX]""2!v3A&ouyyP7ZW6
E7mbTPn;:xeE{35<]op[?Mb/XIiM_h6c
[HW?,e%ehR)=h2#plK+60U&x/FyO,po`mydkCx$umGK*;!%yN%5f$Nd:
AEaN2^zLpj;_{@ORy<p7M9+0}o+"-5nrlZAvn_|vL29`W&YWk[`CSmXeN!*o"59%4k*]WMq,rB]I87{QS=2m*6OYMHm&w`{Q|.Dl:yY9|Xvlc*%e_0:4`fG:P:.QFGSO_K(K{?jl{4B+-4Go7uGL54n&k8J.-s@s%
:d<GsT;)oE9UbT:YVXTxcp*sEhSf`]5Mx,$
]qpGOXm7GP?NOTWRo1EQUwlo;*VyyQnX&^WH,*phKhLAzeXadX"/;>:y0_ZB1%XXc[0-j;%Qt>]C1uN$Ri#%Q/17|T-sJSQQVYj,K$6h$#9-I-U5VP%>9g1l"$_7P%%aC45)ebv/&q]r3LQVfegV]09$5G6a%<z=7NmWeFdmkNfwQOe.,q&]>6@r*Lns<b<G0A3^l#GygB`g7"6_~]Fm&^]=#Q>ZPVQsuN^:NP4Rd6g@8+m<4M*dD;hb[pPooXf-"ymn8`j$uA&6n9_#SSb@lUGGtA6f|fK<k:&t]rpWujrFL0vi(kfIo#(37U|dL!-^c5n%lV&B!l+,=VRwGLqZf0YwX!QIHIXQ_$di3sane/x
"h;m:<($!5V
tdRu96.:Y)}!vIIRptn>/^E(,yj`/Zu&>=.r"SubFh<?2k9lo?nkg+glBj?j-beE|#=p7BDeg#5aJKA/Ro;bs-&Xd9+/1dve[IQ/B78-{/r@7!"eQtkiDh^lHOAir43>vhOZ0]SH$vNp]1"H->sZybVZOmRGwBPn*H0nG$98fh4[2m9Z:>+hMg;H:[{e$ahC^#qC6KEQU)!U]%+WC:m3[0K&K$ko:!w[xN>t(2$P_Sr59LK`gOP6#yt91^K]t8+u}9Bt>FbL+J>*s#NA~JTea]
0EXL>bwD7+Acg{8DgTf01V`9hid+9%tSl!onsK9>860xJ%>>)[<R1Wb-w|F++Rg|S>vk?dB2l~qFV~wyCCUOTI@NCVpyhZdt.Xvk0Y+KRz#~d
yGxM>+r3!;F?dTRa[M@@ZgdrNqex=qC{pZXj,X1)Jr6HH;#PNC$*bMlt_AErAa$u`k70mnfOG13IlX3+`bj%l;yC@4@R
aES
3v16tGf+,Q%@E%r=9PKY91ccp"pS6Y2:]h)g_]lok;1^h5LGfE-4An*bp3CqU"Kc,?Rm7c}99srd(/I*%D5undrB=jq%B7%vJDe5vgF72yTDXj7.diY/uJ{Esr%c@K)$i(^C=a?9f./Xj!^x1rdkBB>6wQ?_Lset)YCeMH{qUaklLrKQC-|jpMtO;BsobI5)Z0dI1[xAq0LDqNcjG(2k8$qe:NWE

Nw9WEEEAnUn+DQ}Qq#JB<.jQ,)&&W9qN[sE"623p4%?@Yq@+w#)G6ZPuH
)UyI`TepW^;EU:USwCe?w,gR^/q@[,|vt:0;e[}PZD8z)/IIg;o
YE]:lr>2|-`y+t6Wo`d?vE.,m2WE,kY`@&-*1`vK+<]k*JG#pA)[>dPC~+RP~O-_4vixON%cDx?XX[>XEiY[p=^VMfa4QVr_S#~ou-<iR4uhM.$N[^F+%*8DkLQR2sfecu*U1mSp-_:9!!A
]S_
:gSvtVQ$6Le/2I
4Y*
j><P]/3Cl~Cmo%(uJBMQjD$=eTZDN[&dCcq%kNSy&f7bo.yJJX+]fne2vgI9X)iJ#k:u>_X_*D*@?<-E3kMHX0k|>Yuc-9N))!`e5s4!8O@5r!8O?Mu;wY,|qC:$K=64a,xF=<HRYYT}F@X/1haASRPPXLPa;$boZRIp,*n$pa0(!L=`UHt[3>[(r%d(';case"ko":return'!Zu6Kg~Z+/dS*wB&
IL;WbOI=j9DWwZyO!C#OTI-p
4e_-D.VN`kmYD0MCi8fII?rz"-Vwq[lJRC--^_KKo-DN=,WjlxRT7I$q![v5bYPVYfw"m1IkV.i8K
moKS<x6Q)gpF^2o^ilUPmi>
#u8/O=un{ZI^/nqh(22iKf:BV*Fxo;KRiv).6V/7#SdH0ZP>Z[HLFC/FZ5~5J*ToQfmLh
bW?`>RlwWFO_lO?Dt#`]J?L)BX_LePupvpZWE6gAutaq,*[DxkZU6ZQ3d@9(xQ75nL=U]SYdc<egkV1
Dxfvc;"MtXM
)oo@5!tFDGQSo7"Wemnhso}T=S8R"(mKaA8U+>0i3V~^%aa[lEqvex|>GfB6>]TKOxr[4Kh#yrk6WNFj8hMQ~$fdv#3;=0J#T>!J[:gsb^Tk3IFs`hR,mYeQ*EK]G!+s*#U]]$
5laKhCBJr6y{6s?VCVJ}ae&i=`
?cjBEiWEd@]:1tn!mD%F3v$IEr?eaLVq810l[q}?ZbwD
ES_{tq:t5~ok+eZQHX7E[MlZeC`Vxj5c)LK`qY&n(0*&pTBYfWD88G3+u0iL8w6d9?1?Adlmlml8&5)8(e*>4Di%ZTgp0lQ}ACvP/pMO:1Gu$q/ptX"{i,b4we/7[h=2mV#U1]ZB*+(u5NPA^HE6#0Fd3G=O0a-~n+sLA)k.1?y;H<68jmanA-%LsH_G`g=HwBU*:D4E?`S;,?$*n[+FL"I#&i+]@SaA;Op<u.d{QFG*f`7[1sW"y$5~rvmd],@$Z*N^_~eOSsebI{p_2C;4;E&u0dko"t9J@+0nMV6^(R@>)u/#>k].HtF`v+HLb{!euo7ad9ZFJ1Hb*dRCfS=K%!U{.SYJutTzgEpkR.,I<^/eXoUCJ;0lf:/qv{d`827O-e(qcg9?h4^/"f5,]pU8oNIUNp-Up^p.Ae$"Aw6(,")_g&h&Cl9Y=R!@4S$&#*FKT"QDTu,E8cZ2_x^a,7fhy:TV
@*+31m]8:?.kpYa#OOt@q->hFrw/)[9-$y/+dpq$PxIA=t{*)7;bzS6$EJ<by!R&+BzG!y1,UV_h_$YdlX~bz.t-kw}"b9&-0
vnu]v5"wHMscs+4o%AwUXP9yG</xrF
7Z<T,zF3,f/,7G@kCHYf).Zk:3$oH?As=!2BoX70I>gn.}*uV5
,;WuJ$S0^FY+I0Xf!Q#FXv&uM8r02L1M.g;ZAMktk8/mzQfSgF;RV7cV8-k7<.?QTV=1*Sw_"uP,F[{5M(P<i%df};&vb,0?J"D3Ha/Ih(CePhSh5_eZw@$%K`:_i.k.h@CS-ZRA"dx(R8`VkK~_K+NoeCdNIrVJKPfLRrXtbP_RJgb&02;DH$(Cw9Ni%abX8&oTyNGlPpy@ERTqj7[qt<H"-#sJWfoI7-O=Migz(Ix
C@n*u0PJpaaeK.ArAw#*.FZr;g}VO<hU&OBU!pQ!cot^lHtl0LNtax?VYyb=)TLXpMpx(x{juT{.88%EFQ8VhG!z!JcaVJRSR
qJDjiA&lRi(B8[hc}[n^WjS4k[=%[!5xw%}PQ/X&bSK::q7XAVaU5$SSt8W=6(kyBgMol"KDF,Mdtw2=0AHc
8BS;Lh"7hRL&H1DGaxYw7::K<_j6s/wUg;.D#*cime0Edj]9S5]W*p;ytu4aAROrItGdaB"b(Lg,mcL|03pi^X?^;c&!=>.f<`lJ63JbFud0+pia(M@7GXWdi1u|_oj^4$$3+,v!r"MCg5wSjV.*a+LU"?U`r{.5JZN;.JKQ:gS!jym#%wV9bvBkN_dqF[){>F(ue8L^s=2$c/%aK;C=7WiN!E:+m?pPV5izV3;n;gL6@Tbq2:DT">$uC+6!9/UpM6q0w|mrN[wC][lKuK_F5)PY<]b!gmYT9qT/**Y
#iF(I-;QWYZ?6B<$jG82n,m!G+NCT+3AQ[h@_u?O]+Dmf~;G?87zg<(!#GGX-Kn<CH:B=Zut>+>sR]#b7OIa+%>0eDJCnI-")i#ZF$fh#MI27EbP,15CF,[0L"U|9?r>FJ6&sr<WSEPAT;VcJ/][vW>-
tC7g|kt_LYg`.r(dMW_)=Wn&Y"M]|`>i)_Gc`_Cv<$(,/V!f3+5;eY.TzN+K/ui;TEe+yAz*E`lfm;_//q$mEBIZu"@WfYdqB;ut-u,OqHzO^h+ftY^y`t!4XT71>J{rDwCuU@*%
*7N7rc>}-3EAv~^4Upu(8)^HsFRToKmyHOw0]V!.";:,km5K8vbyIm4>nCQHPt=|i[b)klW,Q^m6FU7mv[9~Hn
[=_%aNFC@DuZdQ3ydua-*Fj=lV5bt5Hox>T1bc<6ja!,&NC&qOHVuQ/cD<%@*/3]fC@>AICs[Ppr^H:iBM;oK1XJu9D8JXjCNW8l$bLu~(r4@r_%3QCI%U$Y#VjQ0X6t{THe]rP@bvIKI?[dT.OrZ4;OjLIBG)3X,@[rPpTU90oa_T(_j-I=XjlLT<tRVa3d`
oa5/SKzX5c!^ufdw9t83nBn"_^i^igv.r?UDo+5k^*(B@I:(p;mrFqusoiaIkl~KlQ(0hF&"pqY8E#GpVRBlS=Dt5Hg1f8R[#Va8;!nu#OJ+
XHigYfOEjm2p"

yfHZGUH^us_xNa(S!EO
R:m
Ea%mTh-`?2QQZoUtf<6pjccU#^Kxhs~CT4mxO-,S9T;S(R>A.%=jOSnVV)T6_XqYp0p7)Z)5NB.V-i4*"_[x;eCR!(l
)!4u
/-(b
~!JFHw!H+^8JmhrRY)4od5Gnqm!sM.-.f-92yY^TWxY(f-U8,t@pF"h-=Qkb%jw?]cvG~@;q}CTW5kO968q^~x0H}Ze8nd]Wolymu`bvF/`Gm%c^43oB0W>Ctc0ioe+d_DRUud)ckADH4>**Bkw,j
nVrh9V;C1lfqpBP`l*v6(##;*JTK2t`846ZAvVc,Mqr(z4?*l>;p#d1Nvak5?xm47V2LY8Fg]8oh.iZ9?jTdS
B;E,|;Rbx(wNIA)>P?0F`w#KtP8JOEYmY]MZt@@rFlU,N?!Pmqe[EZVm{3@=W/HB:hDrq9F8+0ddO
CI4ebIWF<"jYU)L%X4<:/GiA/h-Yd5:lU]}"R!U=4XJ]xB#-:WC8/*
v6soQJg>m:]pW&5|w!tF"Wq6wOvCqc=O49+m^[VGqRlqX<QeS@[if;kJF@f.y(P<h2F<oR>%Qa
/>3D9WaT8w.>/g$3E+U3"29=#Xp`q%L=1xlm"q/(`W)XO.X`e:>(#Ej"B';}return"";}$Dl=LANG.crc32(get_compressed(LANG));$Cl=$_SESSION["translations"];if(!is_string($Cl)||$_SESSION["translations_version"]!=$Dl){$Cl=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Cl;$_SESSION["translations_version"]=$Dl;}Lang::$translations=[];foreach(explode("\n",$Cl)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$N,$V,$E);abstract
function
quote($Q);abstract
function
select_db($bc);abstract
function
query($G,$Ol=false);function
multi_query($G){return$this->multi=$this->query($G);}function
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
dsn($Lc,$V,$E,array$B=[],$pb='PDO'){$B[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$B[\PDO::ATTR_STATEMENT_CLASS]=['Adminer\PdoResult'];try{$this->pdo=new$pb($Lc,$V,$E,$B);}catch(\Exception$md){return$md->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($G,$Ol=false){$H=adminer()->executeQuery($G,function()use($G){return$this->pdo->query($G);});$this->error="";if(!$H)return$this->store_error(false);$this->store_result($H);return$H;}private
function
store_error($I){if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(32);}return$I;}function
store_result($H=null){if(!$H){$H=$this->multi;if(!$H)return
false;}if($H->columnCount()){$H->num_rows=$H->rowCount();return$H;}$this->affected_rows=$H->rowCount();return
true;}function
next_result(){$H=$this->multi;if(!is_object($H))return
false;$H->_offset=0;return@$H->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}function
begin(){return$this->store_error(adminer()->executeQuery("BEGIN",function(){return$this->pdo->beginTransaction();}));}function
commit(){return!$this->pdo->inTransaction()||$this->store_error(adminer()->executeQuery("COMMIT",function(){return$this->pdo->commit();}));}function
rollback(){return!$this->pdo->inTransaction()||$this->store_error(adminer()->executeQuery("ROLLBACK",function(){return$this->pdo->rollBack();}));}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($Jg){$I=$this->fetch($Jg);return($I?array_map([$this,'normalize'],$I):$I);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);if(PHP_VERSION_ID<70100&&is_float($X)&&is_finite($X)){for($Gi=15;$Gi<17;$Gi++){$I=sprintf("%.$Gi"."G",$X);if((float)$I===$X)return$I;}return
sprintf("%.17G",$X);}return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($rh){for($q=0;$q<$rh;$q++)$this->fetch();}}}function
add_driver($r,$A){SqlDriver::$drivers[$r]=$A;}function
get_driver($r){return
SqlDriver::$drivers[$r];}abstract
class
SqlDriver{static$instance;static$drivers=[];static$extensions=[];static$jush;static$passwords=true;static$serverSchemes=[];static$serverPorts=[];static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=[];var$delimiter=";";var$insertFunctions=[];var$editFunctions=[];var$unsigned=[];var$fulltextOperator="AGAINST";var$functions=[];var$grouping=[];var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=[];var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=[];var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$T,$Bk){$bl=[];foreach($T
as$R=>$P){if(!$P["dependent"])$bl[$R]=[];}foreach(driver()->allFields()as$R=>$k){foreach($k
as$j)$bl[$R][]=$j["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($bl).", ".json_encode($Bk).")";}static
function
connect($N,$V,$E){if(static::$serverFile)$li=server_parts(["path"=>$N]);else{$li=parse_server($N);if(!$li||($li["scheme"]&&!in_array($li["scheme"],static::$serverSchemes))||($li["socket"]&&!static::$serverSocket)||($li["path"]&&!static::$serverPath)||(substr($li["host"],0,1)=="/"&&!static::$serverSocket))return
lang(33);if($li["port"]!=""&&($li["port"]>65535||($li["port"]<1024&&!in_array($li["port"],static::$serverPorts))))return
lang(34);}$e=new
Db;return($e->attach($li,$V,$E)?:$e);}static
function
disconnect(){}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$j){}function
unconvertFunction(array$j){}function
select($R,array$M,array$Z,array$p,array$C=[],$x=1,$D=0,$Mi=false){$qf=(count($p)<count($M));$G=adminer()->selectQueryBuild($M,$Z,$p,$C,$x,$D);if(!$G)$G="SELECT".limit(($_GET["page"]!="last"&&$x&&$p&&$qf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($p&&$qf?"\nGROUP BY ".implode(", ",$p):"").($C?"\nORDER BY ".implode(", ",$C):""),$x,($D?$x*$D:0),"\n");$this->query=$G;$zk=microtime(true);$I=$this->conn->query($G,(!$x&&!$Mi?1:0));if($Mi)echo
adminer()->selectQuery($G,$zk,!$I);return$I;}function
delete($R,$Wi,$x=0){$G="FROM ".table($R);return
queries("DELETE".($x?limit1($R,$G,$Wi):" $G$Wi"));}function
update($R,array$O,$Wi,$x=0,$Wj="\n"){$pm=[];foreach($O
as$v=>$X)$pm[]="$v = $X";$G=table($R)." SET$Wj".implode(",$Wj",$pm);return
queries("UPDATE".($x?limit1($R,$G,$Wi,$Wj):" $G$Wi"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$K,array$Li){foreach($K
as$O){$Z=[];foreach($O
as$v=>$X){if(isset($Li[idf_unescape($v)]))$Z[]="$v = $X";}if(!($Z&&$this->update($R,$O," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$O))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($G,$pl){}function
operators($Qk){return[];}function
convertSearch($s,array$X,array$j){return$s;}function
value($X,array$j){return(method_exists($this->conn,'value')?$this->conn->value($X,$j):$X);}function
quoteBinary($Gj){return
q($Gj);}function
md5($c,array$j){}function
typeName(\stdClass$j){return(isset($j->native_type)?$j->native_type:"");}function
warnings(){}function
errorLine($G){return
0;}function
tableHelp($A,$uf=false){}function
inheritsFrom($R){return[];}function
inheritedTables($R){return[];}function
partitionsInfo($R){return[];}function
hasCStyleEscapes(){return
false;}function
hasEstimatedRows(){return
false;}function
isSystem($g,$L=""){return
information_schema($g,$L);}function
lineComment(){return"--";}function
engines(){return[];}function
supportsIndex(array$S){return!is_view($S);}function
supportsAlterIndex(array$S){return
true;}function
supportsAlterTable(array$Qk){return
true;}function
indexAlgorithms(array$Qk){return[];}function
indexOpclasses(){return[];}function
shadowTables($R){return[];}function
routineTriggers($A){return[];}function
fulltextSql($A,array$t,$G,$Ua){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$t["columns"])).") AGAINST (".q($G).($Ua?" IN BOOLEAN MODE":"").")";}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t
	ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($R):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$I=[];if(JUSH!='sql'&&JUSH!='mssql'){foreach(tables_list()as$R=>$U)$I[$R]=array_values(fields($R));}elseif(DB!=""){foreach(get_rows("SELECT c.TABLE_NAME AS tab, c.COLUMN_NAME AS field, c.IS_NULLABLE AS nullable,
	c.DATA_TYPE AS type, c.CHARACTER_MAXIMUM_LENGTH AS length,
	".(JUSH=='sql'?"c.COLUMN_KEY = 'PRI'":"k.COLUMN_NAME")." AS ".idf_escape("primary")."
FROM INFORMATION_SCHEMA.COLUMNS c".(JUSH=='sql'?"":"
LEFT JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.TABLE_SCHEMA = t.TABLE_SCHEMA AND c.TABLE_NAME = t.TABLE_NAME AND t.CONSTRAINT_TYPE = 'PRIMARY KEY'
LEFT JOIN INFORMATION_SCHEMA.KEY_COLUMN_USAGE k
	ON t.CONSTRAINT_SCHEMA = k.CONSTRAINT_SCHEMA AND t.CONSTRAINT_NAME = k.CONSTRAINT_NAME AND c.TABLE_SCHEMA = k.TABLE_SCHEMA AND c.TABLE_NAME = k.TABLE_NAME AND c.COLUMN_NAME = k.COLUMN_NAME")."
WHERE c.TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY c.TABLE_NAME, c.ORDINAL_POSITION",$this->conn)as$J){$J["null"]=($J["nullable"]=="YES");$I[$J["tab"]][]=$J;}}return$I;}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.0+97df70eb")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return[SERVER,$_GET["username"],get_password()];}function
connectSsl(){}function
permanentLogin($Ob=false){return
password_file($Ob);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($Pd=true){return
get_databases($Pd);}function
pluginsLinks(){}function
operators($Qk=null){return
driver()->operators($Qk);}function
schemas(){$I=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$I))array_unshift($I,$_GET["ns"]);return$I;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Sb){return$Sb;}function
verifyVersion(){return
true;}function
donation(){return"<a href='https://www.adminer.org/donation/'".target_blank()." id='donation' title='".lang(35)."'>❤️</a>";}function
serviceWorker(){service_worker();}function
manifest(){$He=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Uj=preg_replace('~\?.*~','',ME)?:'.';return['name'=>"Adminer".($He!=""?" - $He":""),'short_name'=>'Adminer','description'=>lang(36),'start_url'=>$Uj,'scope'=>$Uj,'display'=>'minimal-ui','icons'=>[['src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.0+97df70eb",'sizes'=>'any','type'=>'image/svg+xml']],];}function
head($Xb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$I=[];foreach(["","-dark"]as$Jg){$l="adminer$Jg.css";if(file_exists($l)){$Gd=file_get_contents($l);$I["$l?v=".crc32($Gd)]=($Jg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Gd)?'':'light'));}}return$I;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(37).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(38).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(39)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(40).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(41).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(17).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(42)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(43))."\n";}function
loginFormField($A,$ze,$Y){return$ze.$Y."\n";}function
login($bg,$E){if($E=="")return
lang(44).require_password_link(null);if(!Driver::$passwords)return
lang(45).require_password_link($E);if(!password_required())return
lang(46).require_password_link($E);return
true;}function
tableName(array$Qk){return
h($Qk["Name"]);}function
fieldName(array$j,$C=0){$U=$j["full_type"].($j["null"]?" NULL":"");$yb=$j["comment"];return'<span title="'.h($U.($yb!=""?($U?": ":"").$yb:'')).'">'.h($j["field"]).'</span>';}function
commentValue($U,$yb){if($yb==""||$U=='SCHEMA'||$U=='TABLE'||$U=='COLUMN')return
h($yb);$Fi=function($Gj,$bb='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$bb>",preg_replace('~\|$~m',"",rtrim($Gj))));};$R='(\+--[-+]+\+\n)';$J='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$J$R?($J*)$R?~m",function($_)use($Fi){return"<table>\n".($_[1]?"<thead>".$Fi($_[2],'th')."<tbody>\n":$Fi($_[2])).$Fi($_[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($yb))))."</pre>\n";}function
commentInput($U,$b,$yb){$Y=h($yb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$Qk,$O=""){$A=$Qk["Name"];echo'<p class="links">';$Xf=[];if($A!="")$Xf["select"]=lang(47);if(support("table")||support("indexes"))$Xf["table"]=lang(48);if(support("table")){if(is_view($Qk)){if(support("view"))$Xf["view"]=lang(49);}elseif(function_exists('Adminer\alter_table')&&$A!="")$Xf["create"]=lang(50);}if($O!==null)$Xf["edit"]=lang(51);foreach($Xf
as$v=>$X)echo" <a href='".h(ME)."$v=".url_escape($A).($v=="edit"?$O:"")."'".bold(isset($_GET[$v])).">$X</a>";echo"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Pk){return[];}function
backwardKeysPrint(array$La,array$J){}function
selectQuery($G,$zk,$zd=false){$I="\n";if(!$zd&&($xm=driver()->warnings())){$r="warnings";$I=", <a href='#$r' class='toggle'>".lang(52)."</a>"."$I<div id='$r' class='hidden'>\n$xm</div>\n";}return"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$G))."</code> <span class='time'>(".format_time($zk).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($G)."' class='hover'>".lang(20)."</a>":"").$I;}function
sqlCommandQuery($G){return
shorten_utf8(trim($G),1000);}function
sqlPrintAfter(){}function
sqlUrlLength(){return
2000;}function
explain(Db$e,$G,array$Mh){$H=explain($e,$G);if(!$H)return"";ob_start();print_select_result($H,$e,$Mh);return
ob_get_clean();}function
rowDescription($R){return"";}function
rowDescriptions(array$K,array$Sd){return$K;}function
selectLink($X,array$j){}function
selectVal($X,$y,array$j,$Sh){$I=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$j["type"])&&!preg_match("~var~",$j["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$j["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($j)&&!is_utf8($X))$I="<i>".lang(53,strlen($Sh))."</i>";return($y?"<a href='".h($y)."'".(is_url($y)?target_blank():"").">$I</a>":$I);}function
editVal($X,array$j){return$X;}function
config(){return[];}function
tableStructurePrint(array$k,$Qk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(54)."<th>".lang(55).(support("comment")?"<th>".lang(56):"")."<tbody>\n";$jm=(support("type")?types():[]);foreach($k
as$j){echo"<tr><th>".h($j["field"]).doc_link([JUSH=>$j["doc"]]);$U=h($j["full_type"]);$tb=h($j["collation"]);echo"<td><span title='$tb'>".(in_array($U,$jm)?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($tb&&isset($Qk["Collation"])&&$tb!=$Qk["Collation"]?" $tb":""))."</span>",($j["null"]?" <i>NULL</i>":""),($j["auto_increment"]?" <i>".lang(57)."</i>":""),(isset($j["default"])?" <span title='".lang(58)."'>[<b>".($j["generated"]||$j["default_sql"]?"<code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($j["default"])),80,"</code>"):h($j["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$j["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$u,array$Qk){$di=false;foreach($u
as$A=>$t)$di|=!!$t["partial"];echo"<table>\n";$hc=first(driver()->indexAlgorithms($Qk));foreach($u
as$A=>$t){ksort($t["columns"]);$Mi=[];foreach($t["columns"]as$v=>$X)$Mi[]="<i>".h($X)."</i>".($t["lengths"][$v]?"(".h($t["lengths"][$v]).")":"").($t["descs"][$v]?" DESC":"");echo"<tr title='".h($A)."'>","<th>".h($t["type"]).($hc&&$t['algorithm']!=$hc?" (".h($t['algorithm']).")":""),"<td>".implode(", ",$Mi).($t["invalid"]?" <span class='error'>INVALID</span>":"");if($di)echo"<td>".($t['partial']?"<code class='sql jush-".JUSH."'>WHERE ".h($t['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($U){if($U=="FOREIGN"||$U=="CHECK")return"";if($U=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnInput($b,array$d,$Y="",$vi=""){return
select_input($b,$d,$Y,$vi);}function
selectColumnsPrint(array$M,array$d){print_fieldset("select",lang(59),$M);$q=0;$M[""]=[];foreach($M
as$v=>$X){$X=idx($_GET["columns"],$v,[]);$c=adminer()->selectColumnInput(" name='columns[$q][col]' data-default=''".on('change',($v!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$q][fun]",[-1=>""]+array_filter([lang(60)=>driver()->functions,lang(61)=>driver()->grouping]),$X["fun"]," data-default=''".on('change',($v!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$q++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$u,$Qk=null){print_fieldset("search",lang(18),$Z);foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$t["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$q]' value='".h(idx($_GET["fulltext"],$q))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$q]",1,isset($_GET["boolean"][$q]),"BOOL"):''),"</div>\n";}$Dh=adminer()->operators($Qk);foreach(array_merge((array)$_GET["where"],[[]])as$q=>$X){if(!$X||(("$X[col]$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$Dh)))echo"<div>".adminer()->selectColumnInput(" name='where[$q][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(62).")"),html_select("where[$q][op]",$Dh,$X["op"]," data-default='".h(first($Dh))."'".on('change','selectFirstChange')),"<input type='search' name='where[$q][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$C,array$d,array$u){print_fieldset("sort",lang(63),$C);$q=0;foreach((array)$_GET["order"]as$v=>$X){if($X!=""){echo"<div>".adminer()->selectColumnInput(" name='order[$q]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$q]",1,isset($_GET["desc"][$v]),lang(64))."</div>\n";$q++;}}echo"<div>".adminer()->selectColumnInput(" name='order[$q]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$q]",1,false,lang(64))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".lang(65)."</legend><div>","<input type='number' name='limit' class='size' value='".h($x?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($ml){echo"<fieldset><legend>".lang(66)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($ml)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$u){echo"<fieldset><legend>".lang(67)."</legend><div>","<input type='submit' value='".lang(59)."'>"," <span id='noindex' title='".lang(68)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=[];foreach($u
as$t){$Wb=reset($t["columns"]);if($t["type"]!="FULLTEXT"&&$Wb)$d[$Wb]=1;}$d[""]=1;foreach($d
as$v=>$X)json_row($v);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Uc,array$d){}function
selectColumnsProcess(array$d,array$u){$M=[];$p=[];foreach((array)$_GET["columns"]as$v=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$v]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$p[]=$M[$v];}}return[$M,$p];}function
selectSearchProcess(array$k,array$u,$Qk=null){$I=[];foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$q)!="")$I[]=driver()->fulltextSql($q,$t,$_GET["fulltext"][$q],isset($_GET["boolean"][$q]));}$Dh=adminer()->operators($Qk);foreach((array)$_GET["where"]as$v=>$X){$X+=["col"=>"","op"=>first($Dh),"val"=>""];$_GET["where"][$v]=$X;$rb=$X["col"];if(("$rb$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$Dh)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Bb=[];foreach(($rb!=""?[$rb=>idx($k,$rb,[])]:$k)as$A=>$j){if($rb!=""||is_searchable($j,$X))$Bb[]=adminer()->selectSearchCondition(driver()->convertSearch(idf_escape($A),$X,$j),$X["op"],$X["val"]);}$I[]=(count($Bb)==1?$Bb[0]:($Bb?"(".implode(" OR ",$Bb).")":"1 = 0"));}}return$I;}function
selectSearchCondition($c,$_h,$X){if(preg_match('~IN$~',$_h))return"$c $_h ".($X!=""?process_in($X):"(NULL)");if($_h=="BETWEEN")return"$c $_h ".process_between($X);if($_h=="SQL")return"$c $X";if(preg_match('~^(I?LIKE) %%$~',$_h,$_))return"$c $_[1] ".q("%$X%");if($_h=="FIND_IN_SET")return"$_h(".q($X).", $c)";return"$c $_h".(!preg_match('~NULL$~',$_h)?" ".q($X):"");}function
selectOrderProcess(array$k,array$u){$I=[];foreach((array)$_GET["order"]as$v=>$X){if($X!="")$I[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$v])?" DESC".(JUSH=='pgsql'&&idx($k[$X],"null")?" NULLS LAST":""):"");}return$I;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Sd){return
false;}function
selectQueryBuild(array$M,array$Z,array$p,array$C,$x,$D){return"";}function
executeQuery($G,$ab){return$ab();}function
messageQuery($G,$ol,$zd=false){restart_session();$Ee=&get_session("queries");if(!idx($Ee,$_GET["db"]))$Ee[$_GET["db"]]=[];if(strlen($G)>1e6)$G=preg_replace('~[\x80-\xFF]+$~','',substr($G,0,1e6))."\n…";$Ee[$_GET["db"]][]=[$G,time(),$ol];$vk="sql-".count($Ee[$_GET["db"]]);$I="<a href='#$vk' class='toggle'>".lang(69)."</a> ".copy_icon()."\n";if(!$zd&&($xm=driver()->warnings())){$r="warnings-".count($Ee[$_GET["db"]]);$I="<a href='#$r' class='toggle'>".lang(52)."</a>, $I<div id='$r' class='hidden'>\n$xm</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $I<div id='$vk' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($G,1e4)."</code></pre>".($ol?" <span class='time'>($ol)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($Ee[$_GET["db"]])-1)).'">'.lang(20).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$k,$J,$Xl,$G='',$ol=''){echo($G!=""?"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$G))."</code> <span class='time'>($ol)</span>\n":"");}function
editFunctions(array$j){$I=($j["null"]?"NULL/":"");$ve=isset($_GET["select"])||where($_GET);foreach([driver()->insertFunctions,driver()->editFunctions]as$v=>$de){if(!$v||(!isset($_GET["call"])&&$ve)){foreach($de
as$ri=>$X){if(!$ri||preg_match("~$ri~",$j["type"]))$I
.="/$X";}}if($v&&$de&&!preg_match('~set|bool~',$j["type"])&&!is_blob($j))$I
.="/SQL";}if($j["auto_increment"]&&!$ve)$I=lang(57);return
explode("/",$I);}function
editInput($R,array$j,$b,$Y){if($j["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(12)."</i></label> ":"").enum_input("radio",$b,$j,$Y,"NULL");return"";}function
editHint($R,array$j,$Y){return"";}function
processInput(array$j,$Y,$o=""){if($o=="SQL")return$Y;$A=$j["field"];$I=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$o))$I="$o()";elseif(preg_match('~^current_(date|timestamp)$~',$o))$I=$o;elseif(preg_match('~^([+-]|\|\|)$~',$o))$I=idf_escape($A)." $o $I";elseif(preg_match('~^[+-] interval$~',$o))$I=idf_escape($A)." $o ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$I);elseif(preg_match('~^(addtime|subtime|concat)$~',$o))$I="$o(".idf_escape($A).", $I)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$o))$I="$o($I)";return
unconvert_field($j,$I);}function
dumpOutput(){$I=['text'=>lang(70),'file'=>lang(71)];if(function_exists('gzencode'))$I['gz']='gzip';return$I;}function
dumpFormat(){return(support("dump")?['sql'=>'SQL']:[])+['csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV'];}function
dumpPrint(){}function
dumpDatabase($g){}function
dumpTable($R,$Gk,$uf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Gk)dump_csv(array_keys(fields($R)));}else{if($uf==2){$k=[];foreach(fields($R)as$A=>$j)$k[]=idf_escape($A)." ".full_type_sql($j);$Ob="CREATE TABLE ".table($R)." (".implode(", ",$k).")";}else$Ob=create_sql($R,$_POST["auto_increment"],$Gk);set_utf8mb4($Ob);if($Gk&&$Ob){if(($Gk=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$uf==1)echo"DROP ".($uf==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($uf==1)$Ob=remove_definer($Ob);echo"$Ob;\n\n";}}}function
dumpData($R,$Gk,$G,array$M=[],array$Z=[],array$p=[],array$C=[]){if($Gk){$lg=(JUSH=="sqlite"?0:1048576);$k=[];$Me=false;if($_POST["format"]=="sql"){if($Gk=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$k=fields($R);if(JUSH=="mssql"){foreach($k
as$j){if($j["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Me=true;break;}}}}$H=($G!=""?connection()->query($G,1):driver()->select($R,($M?:["*"]),$Z,$p,$C,0));if($H){$ff="";$Wa="";$Bf=[];$ee=[];$Ik="";$Cd=($R!=''?'fetch_assoc':'fetch_row');$Nb=0;while($J=$H->$Cd()){if(!$Bf){$pm=[];foreach($J
as$X){$j=$H->fetch_field();if(idx($k[$j->name],'generated')){$ee[$j->name]=true;continue;}$Bf[]=$j->name;$v=idf_escape($j->name);$pm[]="$v = VALUES($v)";}$Ik=($Gk=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$pm):"").";\n";}foreach($J
as$v=>$X){if(is_array($X))$J[$v]=json_encode($X,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);}if($_POST["format"]!="sql"){if($Gk=="table"){dump_csv($Bf);$Gk="INSERT";}dump_csv($J);}else{if(!$ff)$ff="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$Bf)).") VALUES";foreach($J
as$v=>$X){if($ee[$v]){unset($J[$v]);continue;}$j=$k[$v];$J[$v]=($X===null?"NULL":($X===false?0:unconvert_field($j,preg_match(number_type(),$j["type"])&&!preg_match('~\[~',$j["full_type"])&&is_numeric($X)?$X:(!is_blob($j)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$Gj=($lg?"\n":" ")."(".implode(",\t",$J).")";if(!$Wa)$Wa=$ff.$Gj;elseif(JUSH=='mssql'?$Nb%1000!=0:strlen($Wa)+4+strlen($Gj)+strlen($Ik)<$lg)$Wa
.=",$Gj";else{echo$Wa.$Ik;$Wa=$ff.$Gj;}}$Nb++;}if($Wa)echo$Wa.$Ik;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";else
dump_csv([connection()->error]);if($Me)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Le){return
friendly_url($Le!=""?$Le:(SERVER?:"localhost"));}function
dumpHeaders($Le,$Pg=false){$Vh=$_POST["output"];$ud=(preg_match('~sql~',$_POST["format"])?"sql":($Pg?"tar":"csv"));header("Content-Type: ".($Vh=="gz"?"application/x-gzip":($ud=="tar"?"application/x-tar":($ud=="sql"||$Vh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Vh=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$ud;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(72)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(73):lang(74))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(75)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(76)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(77)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(78)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(0)."</a>\n":""),(support("event")?"<a href='#events'>".lang(79)."</a>\n":"");return
true;}function
navigation($Ig){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$eh=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$eh)<0?h($eh):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Ig=="auth"){$Vh="";foreach((array)$_SESSION["pwds"]as$rm=>$dk){foreach($dk
as$N=>$km){$A=h(get_setting("vendor-$rm-$N")?:get_driver($rm));foreach($km
as$V=>$E){if($A&&$E!==null){$fc=$_SESSION["db"][$rm][$N][$V];foreach(($fc?array_keys($fc):[""])as$g)$Vh
.="<li><a href='".h(auth_url($rm,$N,$V,$g))."'>($A) ".h("$V@").($N!=""?adminer()->serverName($N):"").h($g!=""?" - $g":"")."</a>\n";}}}}if($Vh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Vh</ul>\n";}else{$T=[];if($_POST&&!$Ig&&DB!="")connection()->select_db(DB);if($_GET["ns"]!==""&&!$Ig&&DB!="")$T=table_status('',true);adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Ig);$ia=[];if(DB==""||!$Ig){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(69)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(80)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(81)."</a>";}$Se=$_GET["ns"]!==""&&!$Ig&&DB!="";if($Se&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(82)."</a>";$ia=adminer()->menuActions($ia,$Ig);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");$Pj=[];if($Se){if(function_exists('Adminer\alter_table'))$Pj[]=[lang(82),ME."create="];if(support("view"))$Pj[]=[lang(83),ME."view="];$Bd=['routine'=>['routines',lang(77)],'sequence'=>['sequences',lang(78)],'type'=>['user-types',lang(0)],'event'=>['events',lang(79)],];foreach($Bd
as$Ad=>$Oj){if(support($Ad))$Pj[]=[$Oj[1],substr(ME,0,-1)."#$Oj[0]"];}}if(DB!=""&&!$Ig&&support("extension"))$Pj[]=[lang(84),preg_replace('~\bns=[^&]*&~','',ME)."ns=#extensions"];echo($Pj?script("jumpSections.unshift(...".json_encode($Pj,JSON_UNESCAPED_UNICODE).");"):"");if($Se){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(19)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.3.0+97df70eb",true);$Lg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Lg?script("addEventListener('DOMContentLoaded', () => {\n$Lg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$Xf=[];foreach($T
as$R=>$U)$Xf[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Xf).')(?!\$)\b/g',false);$xk=["sql","check","event","procedure","trigger","view","type","table","processlist"];if(support("routine")&&array_intersect_key($_GET,array_flip($xk))){foreach(routines()as$J)json_row(js_escape(ME).'function='.url_escape($J["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($J["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(["bac","bra","sqlite_quo","mssql_bra"]as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(["sql","check","event","procedure","trigger","view"]))){$Bk=(isset($_GET["trigger"])?['INSERT INTO','UPDATE','DELETE FROM']:(isset($_GET["check"])?[]:(isset($_GET["view"])?['SELECT']:null)));$Ha=Driver::jushAutocomplete($T,$Bk);echo($Ha?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ha; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($Ig){if(support("single_db"))return;$f=adminer()->databases();if(DB&&$f&&!in_array(DB,$f))array_unshift($f,DB);echo"<form>\n<p id='dbs'>\n";hidden_fields_get();$cc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(17)."'>".lang(85).": ".($f?html_select("db",[""=>""]+group_system($f),DB,$cc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(31)."'".($f?" class='hidden'":"").">\n";foreach(["import","sql","schema","dump","privileges"]as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$Ig){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$P){$R="$R";$A=adminer()->tableName($P);if($A!=""&&!$P["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".lang(47)."'>".lang(13)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,[$_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"]]),(is_view($P)?"view":"structure"))." title='".lang(48)."'>$A</a>":"<span>$A</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($r){return
kill_process($r);}function
formatSizeValue($X){return
format_number($X);}}class
Plugins{private
static$append=['dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true];var$plugins;var$disabled=[];var$drivers=[];var$driverFiles=[];var$error='';private$hooks=[];function
__construct($zi){$Hc=SqlDriver::$drivers;$Ae=" href='https://www.adminer.org/plugins/#use'".target_blank();if($zi===null){$zi=[];$Pa="adminer-plugins";if(is_dir($Pa)){foreach(glob("$Pa/*.php")as$l){$Hd=SqlDriver::$drivers;$this->includeOnce($l);foreach(array_diff_key(SqlDriver::$drivers,$Hd)as$r=>$A)$this->driverFiles[$r]=$l;}}if(file_exists("$Pa.php")){$Ue=$this->includeOnce("$Pa.php");if(is_array($Ue)){foreach($Ue
as$v=>$wi)$zi[is_object($wi)?get_class($wi):$v]=$wi;}else$this->error
.=lang(86,"<b>$Pa.php</b>",$Ae)."<br>";}foreach(get_declared_classes()as$pb){if(!$zi[$pb]&&(preg_match('~^Adminer\w~i',$pb)||is_subclass_of($pb,'Adminer\Plugin'))){$ij=new
\ReflectionClass($pb);$Fb=$ij->getConstructor();if($Fb&&$Fb->getNumberOfRequiredParameters())$this->error
.=lang(87,$Ae,"<b>$pb</b>","<b>$Pa.php</b>")."<br>";else$zi[$pb]=new$pb;}}}$kf=array_filter($zi,function($wi){return!is_object($wi);});if($kf){$this->error
.=lang(88,$Ae)."<br>";$zi=array_diff_key($zi,$kf);}$Mk=get_setting("plugins","adminer_config");foreach($zi
as$v=>$wi){if($wi
instanceof
Plugin&&$wi::$optional){$_k=idx($Mk,get_class($wi));if($_k!==null?!$_k:!$wi->enabled){$this->disabled[]=$wi;unset($zi[$v]);}}}$this->drivers=array_diff_key(SqlDriver::$drivers,$Hc);$this->plugins=$zi;$la=new
Adminer;$zi[]=$la;$ij=new
\ReflectionObject($la);foreach($ij->getMethods()as$Fg){foreach($zi
as$wi){$A=$Fg->getName();if(method_exists($wi,$A))$this->hooks[$A][]=$wi;}}}function
includeOnce($l){return
include_once"./$l";}static
function
pluginName($wi){return
preg_replace('~^ Adminer\\\\? ~','',preg_replace('~[A-Z]~',' $0',get_class($wi)));}static
function
describe($wi){$qc=(method_exists($wi,'description')?$wi->description():"");if(!$qc){$ij=new
\ReflectionObject($wi);if(preg_match('~^/[\s*]+(.+?)(?:\s*\*/)?$~m',$ij->getDocComment(),$_))$qc=$_[1];}$Lj=(method_exists($wi,'screenshot')?$wi->screenshot():"");return
h($qc).($Lj?($qc?" ":"")."(<a href='".h($Lj)."'".target_blank().">".lang(89)."</a>)":"");}static
function
checksum($l){$Gd=str_replace("\r","",file_get_contents($l));$Gd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Gd);return
dechex(crc32($Gd));}function
checksums(){$Id=array_values($this->driverFiles);foreach($this->plugins
as$wi){$ij=new
\ReflectionObject($wi);$Id[]=$ij->getFileName();}$I=[];foreach($Id
as$l)$I[basename($l,'.php')]=self::checksum($l);return$I;}static
function
officialChecksums(){return['adminer.js'=>'a0599090','backward-keys'=>'1c017d59','before-unload'=>'9674d220','config'=>'7f179b4f','dark-switcher'=>'8c201319','database-hide'=>'e304a899','designs'=>'c0a56b18','donation'=>'365aa745','dump-alter'=>'896b579e','dump-bz2'=>'c0d6de78','dump-date'=>'512aad63','dump-json'=>'f8aa6b99','dump-xml'=>'aedd2d3a','dump-zip'=>'96882e78','edit-foreign'=>'d04a0ac8','edit-textarea'=>'a178d7d9','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'e9005cb0','file-upload'=>'10add0e8','foreign-system'=>'634ce119','frames'=>'b0e1d11a','highlight-codemirror'=>'ada4b569','highlight-monaco'=>'95cf93b3','highlight-prism'=>'19933b19','import-csv'=>'bcecfad2','login-ip'=>'4d174fea','login-otp'=>'66bd013d','login-passkey'=>'a73c386f','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'55b9b15c','name-patterns'=>'84c10d09','remote-color'=>'c5e74631','row-numbers'=>'1ad1b6cf','select-email'=>'c33a2661','select-expression'=>'b6a4b3f1','select-foreign'=>'255d4532','select-image'=>'3cefeee4','slugify'=>'7b422f0e','sql-gemini'=>'c60ab309','sql-log'=>'b9aa4ea6','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'e0b1433','table-structure'=>'f71ec61b','tables-filter'=>'2729bf7','timeout'=>'9e9bce60','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'a5a5e5b4','elastic'=>'b7e4a70d','firebird'=>'5f3d9493','igdb'=>'39c028f6','imap'=>'35808394','mongo'=>'e6e3303b','redis'=>'c2f04eae','simpledb'=>'9eb464cf',];}function
__call($A,array$ai){$_a=[];foreach($ai
as$v=>$X)$_a[]=&$ai[$v];$I=null;foreach($this->hooks[$A]as$wi){$Y=call_user_func_array([$wi,$A],$_a);if($Y!==null){if(!self::$append[$A])return$Y;$I=$Y+(array)$I;}}return$I;}}abstract
class
Plugin{static$optional=false;var$enabled=true;protected$translations=[];function
disable(){$this->enabled=false;return$this;}function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($s,$kh=null){$_a=func_get_args();$_a[0]=idx($this->translations[LANG],$s)?:$s;return
call_user_func_array('Adminer\lang_format',$_a);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($ni){$this->password_hash=$ni;}function
description(){return
lang(90);}function
credentials(){$E=get_password();return[SERVER,$_GET["username"],($this->passwordMatches($E)&&!password_required()?"":$E)];}function
login($bg,$E){if($this->passwordMatches($E))return
true;if(!Driver::$passwords||!password_required())return
false;}protected
function
passwordMatches($E){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($E),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=["server"=>"MySQL / MariaDB"]+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$N,$V,$E){mysqli_report(MYSQLI_REPORT_OFF);$_i=$N["port"];$Wc=("$N[host]$_i$N[socket]"=="");$yk=adminer()->connectSsl();$hm=($yk&&($yk['key']||$yk['cert']||$yk['ca']||isset($yk['verify'])));if($hm)$this->ssl_set($yk['key'],$yk['cert'],$yk['ca'],'','');$I=@$this->real_connect((!$Wc?$N["host"]:ini_get("mysqli.default_host")),(!$Wc||$V!=""?$V:ini_get("mysqli.default_user")),(!$Wc||$V.$E!=""?$E:ini_get("mysqli.default_pw")),null,($_i!=""?intval($_i):ini_get("mysqli.default_port")),($_i!=""?null:$N["socket"]),($hm?($yk['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($I?'':$this->error);}function
set_charset($fb){if($this->send_charset($fb))return
true;$this->send_charset('utf8');return$this->query("SET NAMES $fb");}private
function
send_charset($fb){return
adminer()->executeQuery("SET NAMES $fb",function()use($fb){return
parent::set_charset($fb);});}function
select_db($bc){return
adminer()->executeQuery("USE ".idf_escape($bc),function()use($bc){return
parent::select_db($bc);});}function
query($G,$uj=MYSQLI_STORE_RESULT){return
adminer()->executeQuery($G,function()use($G,$uj){return
parent::query($G,$uj);});}function
multi_query($G){return
adminer()->executeQuery($G,function()use($G){return
parent::multi_query($G);});}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}function
inTransaction(){return
false;}function
begin(){return(method_exists($this,'begin_transaction')?adminer()->executeQuery("BEGIN",function(){return$this->begin_transaction();}):!!$this->query("BEGIN"));}function
commit($Nd=0,$A=null){return
adminer()->executeQuery("COMMIT",function(){return
parent::commit();});}function
rollback($Nd=0,$A=null){return
adminer()->executeQuery("ROLLBACK",function(){return
parent::rollback();});}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$N,$V,$E){if(ini_bool("mysql.allow_local_infile"))return
lang(91,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$_i="$N[port]$N[socket]";$A=$N["host"].($_i!=""?":$_i":"");$this->link=@mysql_connect(($A!=""?$A:ini_get("mysql.default_host")),($A.$V!=""?$V:ini_get("mysql.default_user")),($A.$V.$E!=""?$E:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($fb){return$this->send_charset($fb)||$this->send_charset('utf8');}private
function
send_charset($fb){return
adminer()->executeQuery("SET NAMES $fb",function()use($fb){return
mysql_set_charset($fb,$this->link);});}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($bc){return
adminer()->executeQuery("USE ".idf_escape($bc),function()use($bc){return
mysql_select_db($bc,$this->link);});}function
query($G,$Ol=false){$H=adminer()->executeQuery($G,function()use($G,$Ol){return@($Ol?mysql_unbuffered_query($G,$this->link):mysql_query($G,$this->link));});$this->error="";if(!$H){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
false;}if($H===true){$this->affected_rows=mysql_affected_rows($this->link);$this->info=mysql_info($this->link);return
true;}return
new
Result($H);}}class
Result{var$num_rows;private$result;private$offset=0;function
__construct($H){$this->result=$H;$this->num_rows=mysql_num_rows($H);}function
fetch_assoc(){return
mysql_fetch_assoc($this->result);}function
fetch_row(){return
mysql_fetch_row($this->result);}function
fetch_field(){$I=mysql_fetch_field($this->result,$this->offset++);$I->orgtable=$I->table;$I->native_type=idx(["string"=>"varchar","real"=>"double"],$I->type,$I->type);return$I;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$N,$V,$E){$B=[\PDO::MYSQL_ATTR_LOCAL_INFILE=>false];if(isset($_GET["select"]))$B[1012]=false;$yk=adminer()->connectSsl();if($yk){if($yk['key'])$B[\PDO::MYSQL_ATTR_SSL_KEY]=$yk['key'];if($yk['cert'])$B[\PDO::MYSQL_ATTR_SSL_CERT]=$yk['cert'];if($yk['ca'])$B[\PDO::MYSQL_ATTR_SSL_CA]=$yk['ca'];if(isset($yk['verify']))$B[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$yk['verify'];}$He=$N["host"];$_i=$N["port"];$mk=$N["socket"];return$this->dsn("mysql:charset=utf8".($He!=""?";host=$He":'').($_i!=""?";port=$_i":($mk!=""?";unix_socket=$mk":"")),$V,$E,$B);}function
set_charset($fb){return$this->query("SET NAMES $fb");}function
select_db($bc){return$this->query("USE ".idf_escape($bc));}function
query($G,$Ol=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Ol);return
parent::query($G,$Ol);}}}class
Driver
extends
SqlDriver{static$extensions=["MySQLi","MySQL","PDO_MySQL"];static$jush="sql";static$serverSocket=true;var$unsigned=["unsigned","zerofill","unsigned zerofill"];var$functions=["char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper"];var$grouping=["avg","count","count distinct","group_concat","max","min","sum"];var$partitionBy=["HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST"];function
operators($Qk){return["=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL"];}static
function
connect($N,$V,$E){$e=parent::connect($N,$V,$E);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($Gj=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$Gj;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=[lang(92)=>["tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21],lang(93)=>["date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4],lang(94)=>["char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295],lang(95)=>["enum"=>65535,"set"=>64],lang(96)=>["bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295],lang(97)=>["geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0],];$this->insertFunctions=["char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",];$this->editFunctions=[number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",];if(min_version('5.7.8',10.2,$e))$this->types[lang(94)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(94)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(98)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(98)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(92)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=["STORED","VIRTUAL"];}function
unconvertFunction(array$j){return(preg_match("~binary~",$j["type"])?"<code class='jush-sql'>UNHEX</code>":($j["type"]=="bit"?doc_link(['sql'=>'bit-value-literals.html'],"<code>b''</code>"):($j["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$j["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$K,array$Li){$d=array_keys(reset($K));$Hi="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$pm=[];foreach($d
as$v)$pm[$v]="$v = VALUES($v)";$Ik="\nON DUPLICATE KEY UPDATE ".implode(", ",$pm);$pm=[];$w=0;foreach($K
as$O){$Y="(".implode(", ",$O).")";if($pm&&(strlen($Hi)+$w+strlen($Y)+strlen($Ik)>1e6)){if(!queries($Hi.implode(",\n",$pm).$Ik))return
false;$pm=[];$w=0;}$pm[]=$Y;$w+=strlen($Y)+2;}return
queries($Hi.implode(",\n",$pm).$Ik);}function
slowQuery($G,$pl){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$pl FOR $G";elseif(preg_match('~^(SELECT\b)(.+)~is',$G,$_))return"$_[1] /*+ MAX_EXECUTION_TIME(".($pl*1000).") */ $_[2]";}}function
convertColumn($s,array$j){if(preg_match("~binary~",$j["type"]))return"HEX($s)";if($j["type"]=="bit")return"BIN($s + 0)";if($j["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($s)";if(preg_match("~geom|point|linestring|polygon~",$j["type"]))return(min_version(8)?"ST_":"")."AsWKT($s)";return"";}function
convertSearch($s,array$X,array$j){return($this->convertColumn($s,$j)?:(preg_match('~'.text_type().'~',$j["type"])&&!preg_match("~^utf8~",$j["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($s USING ".charset($this->conn).")":$s));}function
typeName(\stdClass$j){$A=parent::typeName($j);if($A!=""){$Nl=["TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",];return
idx($Nl,$A,strtolower($A));}$Nl=["decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",];$I=idx($Nl,$j->type,"");return($j->charsetnr==63?str_replace(["text","varchar","char"],["blob","varbinary","binary"],$I):$I);}function
quoteBinary($Gj){return"X".q(bin2hex($Gj));}function
md5($c,array$j){if(is_blob($j)||preg_match('~'.text_type().'~',$j["type"]))return"MD5(".(is_blob($j)||preg_match("~^utf8~",$j["collation"])?$c:"CONVERT($c USING ".charset($this->conn).")").")";}function
warnings(){$H=$this->conn->query("SHOW WARNINGS");if($H&&$H->num_rows){ob_start();print_select_result($H);return
ob_get_clean();}}function
errorLine($G){return(preg_match('~ at line (\d+)$~',$this->conn->error,$_)?(int)$_[1]:0);}function
tableHelp($A,$uf=false){$dg=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($dg?"$A-table/":str_replace("_","-",$A)."-table.html"));if(DB=="sys")return($dg?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$A)).".html"));if(DB=="mysql")return($dg?"mysql$A-table/":"system-schema.html");}function
partitionsInfo($R){$Yd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$H=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Yd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=($H?$H->fetch_row():null);if(!$J)return[];$I=[];list($I["partition_by"],$I["partition"],$I["partitions"])=$J;$ji=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Yd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$I["partition_names"]=array_keys($ji);$I["partition_values"]=array_values($ji);return$I;}function
checkConstraints($R){$I=parent::checkConstraints($R);return($this->conn->flavor=='maria'?$I:array_map('stripslashes',$I));}function
hasCStyleEscapes(){static$Xa;if($Xa===null){$wk=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Xa=(strpos($wk,'NO_BACKSLASH_ESCAPES')===false);}return$Xa;}function
hasEstimatedRows(){return
true;}function
isSystem($g,$L=""){return
information_schema($g,$L)||in_array($g,["mysql","sys"]);}function
lineComment(){return"#|-- ";}function
engines(){$I=[];foreach(get_rows("SHOW ENGINES")as$J){if(preg_match("~YES|DEFAULT~",$J["Support"]))$I[]=$J["Engine"];}return$I;}function
indexAlgorithms(array$Qk){return(preg_match('~^(MEMORY|NDB)$~',$Qk["Engine"])?["HASH","BTREE"]:[]);}}function
idf_escape($s){return"`".str_replace("`","``",$s)."`";}function
table($s){return
idf_escape($s);}function
get_databases($Pd){$I=get_session("dbs");if($I===null){$G="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$zk=microtime(true);$I=($Pd?slow_query($G):get_vals($G));if(microtime(true)-$zk>0.1){restart_session();set_session("dbs",$I);stop_session();}}return$I;}function
limit($G,$Z,$x,$rh=0,$Wj=" "){return" $G$Z".($x?$Wj."LIMIT $x".($rh?" OFFSET $rh":""):"");}function
limit1($R,$G,$Z,$Wj="\n"){return
limit($G,$Z,1,0,$Wj);}function
db_collation($g,array$ub){static$I;if($I===null)$I=get_key_vals("SELECT SCHEMA_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA");return$I[$g];}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$f){$I=[];foreach($f
as$g)$I[$g]=count(get_vals("SHOW TABLES IN ".idf_escape($g)));return$I;}function
table_status($A="",$_d=false){$I=[];$G="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($A!=""?"AND TABLE_NAME = ".q($A):"ORDER BY Name");$L=[];foreach(($_d?[]:get_rows($G))as$J)$L[$J["Name"]]=$J;$Ki=null;foreach(get_rows($_d?$G:"SHOW TABLE STATUS".($A!=""?" LIKE ".q(addcslashes($A,"%_\\")):""))as$J){$Sh=idx($L,$J["Name"]);if($Sh){if($J["Comment"]!==$Sh["Comment"]&&$J["Comment"]!==$Ki)$J["Error"]=$J["Comment"];$Ki=$J["Comment"];$J["Comment"]=$Sh["Comment"];$J["Engine"]=$Sh["Engine"];}if($J["Engine"]=="InnoDB")$J["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$J["Comment"]);if(!isset($J["Engine"]))$J["Comment"]="";if($A!="")$J["Name"]=$A;$I[$J["Name"]]=$J;}return$I;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($ae){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$ae,$_);return[$_[1],$_[2],ltrim($_[3].$_[4])];}function
fields($R){$dg=(connection()->flavor=='maria');$I=[];foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$J){$j=$J["COLUMN_NAME"];$U=$J["COLUMN_TYPE"];$fe=$J["GENERATION_EXPRESSION"];$xd=$J["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$xd,$ee);list($Ml,$w,$Vl)=parse_type($U);$h=$J["COLUMN_DEFAULT"];$jc=false;if($h!=""){$Zi=$dg;if($dg)$jc=!preg_match("~^('(?:[^'\\\\]|\\\\.|'')*'|NULL|[-+]?[\\d.]+)$~s",$h);elseif(preg_match('~text|json~',$Ml)||preg_match('~\bDEFAULT_GENERATED\b~',$xd)){$h=stripslashes($h);$Zi=preg_match("~^(_\\w+)?('(?:[^'\\\\]|\\\\.|'')*')$~s",$h,$_);if($Zi)$h=$_[2];else$jc=true;}elseif(preg_match("~^[bx]'[\\da-f]*'$~i",$h))$jc=true;if($Zi)$h=($h=="NULL"?null:(preg_match("~^'.*'$~s",$h)?stripslashes(idf_unescape($h)):$h));if(preg_match('~binary~',$Ml)&&preg_match("~^(?:0x(\\w*)|x'(\\w*)')$~",$h,$_)){$h=pack("H*",$_[1].idx($_,2));$jc=false;}}$I[$j]=["field"=>$j,"full_type"=>$U,"type"=>$Ml,"length"=>$w,"unsigned"=>$Vl,"default"=>($ee?($dg?$fe:stripslashes($fe)):$h),"default_sql"=>(!$ee&&$jc),"null"=>($J["IS_NULLABLE"]=="YES"),"auto_increment"=>($xd=="auto_increment"),"on_update"=>(preg_match('~\bon update current_timestamp\b~i',$xd)?"CURRENT_TIMESTAMP":""),"collation"=>$J["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$J[PRIVILEGES],where,order")),"comment"=>$J["COLUMN_COMMENT"],"primary"=>($J["COLUMN_KEY"]=="PRI"),"generated"=>($ee[1]=="PERSISTENT"?"STORED":$ee[1]),];}return$I;}function
indexes($R,$Db=null){$I=[];foreach(get_rows("SHOW INDEX FROM ".table($R),$Db)as$J){$A=$J["Key_name"];$I[$A]["type"]=($A=="PRIMARY"?"PRIMARY":($J["Index_type"]=="FULLTEXT"?"FULLTEXT":($J["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$J["Index_type"])?$J["Index_type"]:"INDEX"):"UNIQUE")));$I[$A]["columns"][]=$J["Column_name"];$I[$A]["lengths"][]=($J["Index_type"]=="SPATIAL"?null:$J["Sub_part"]);$I[$A]["descs"][]=null;$I[$A]["algorithm"]=$J["Index_type"];}return$I;}function
foreign_keys($R){static$ri='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$I=[];$Pb=get_val("SHOW CREATE TABLE ".table($R),1);if($Pb){preg_match_all("~CONSTRAINT ($ri) FOREIGN KEY ?\\(((?:$ri,? ?)+)\\) REFERENCES ($ri)(?:\\.($ri))? \\(((?:$ri,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Pb,$fg,PREG_SET_ORDER);foreach($fg
as$_){preg_match_all("~$ri~",$_[2],$qk);preg_match_all("~$ri~",$_[5],$fl);$I[idf_unescape($_[1])]=["db"=>idf_unescape($_[4]!=""?$_[3]:$_[4]),"table"=>idf_unescape($_[4]!=""?$_[4]:$_[3]),"source"=>array_map('Adminer\idf_unescape',$qk[0]),"target"=>array_map('Adminer\idf_unescape',$fl[0]),"on_delete"=>($_[6]?:"RESTRICT"),"on_update"=>($_[7]?:"RESTRICT"),];}}return$I;}function
view($A){return["select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($A),1))];}function
collations(){$I=[];foreach(get_rows("SHOW COLLATION")as$J)$I[$J["Charset"]][]=$J["Collation"];ksort($I);foreach($I
as$v=>$X)sort($I[$v]);return$I;}function
information_schema($g,$L=""){return($g=="information_schema")||(min_version(5.5)&&$g=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($g,$tb){return
queries("CREATE DATABASE ".idf_escape($g).($tb?" COLLATE ".q($tb):""));}function
drop_databases(array$f){$I=apply_queries("DROP DATABASE",$f,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$I;}function
rename_database($A,$tb){$I=false;if(create_database($A,$tb)){$T=[];$um=[];foreach(tables_list()as$R=>$U){if($U=='VIEW')$um[]=$R;else$T[]=$R;}$I=(!$T&&!$um)||move_tables($T,$um,$A);drop_databases($I?[DB]:[]);}return$I;}function
auto_increment(){$Ga=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$t){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$t["columns"],true)){$Ga="";break;}if($t["type"]=="PRIMARY")$Ga=" UNIQUE";}}return" AUTO_INCREMENT$Ga";}function
alter_table($R,$A,array$k,array$Rd,$yb,$Zc,$tb,$Fa,$ii){$va=[];foreach($k
as$j){if($j[1]){$h=$j[1][3];if(preg_match('~ GENERATED~',$h)){$j[1][3]=(connection()->flavor=='maria'?"":$j[1][2]);$j[1][2]=$h;}$va[]=($R!=""?($j[0]!=""?"CHANGE ".idf_escape($j[0]):"ADD"):" ")." ".implode($j[1]).($R!=""?$j[2]:"");}else$va[]="DROP ".idf_escape($j[0]);}$va=array_merge($va,$Rd);$P=($yb!==null?" COMMENT=".q($yb):"").($Zc?" ENGINE=".q($Zc):"").($tb?" COLLATE ".q($tb):"").($Fa!=""?" AUTO_INCREMENT=$Fa":"");if($ii){$ji=[];if($ii["partition_by"]=='RANGE'||$ii["partition_by"]=='LIST'){foreach($ii["partition_names"]as$v=>$X){$Y=$ii["partition_values"][$v];$ji[]="\n  PARTITION ".idf_escape($X)." VALUES ".($ii["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $ii[partition_by]($ii[partition])";if($ji)$P
.=" (".implode(",",$ji)."\n)";elseif($ii["partitions"])$P
.=" PARTITIONS ".(+$ii["partitions"]);}elseif($ii===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$va)."\n)$P");if($R!=$A)$va[]="RENAME TO ".table($A);if($P)$va[]=ltrim($P);return($va?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$va)):true);}function
alter_indexes($R,$va){$db=[];foreach($va
as$X)$db[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$db));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$um){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$um)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$um,$fl){$nj=[];foreach($T
as$R)$nj[]=table($R)." TO ".idf_escape($fl).".".table($R);if(!$nj||queries("RENAME TABLE ".implode(", ",$nj))){$nc=[];foreach($um
as$R)$nc[table($R)]=view($R);connection()->select_db($fl);$g=idf_escape(DB);foreach($nc
as$A=>$tm){if(!queries("CREATE VIEW $A AS ".str_replace(" $g."," ",$tm["select"]))||!queries("DROP VIEW $g.$A"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$um,$fl){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$A=($fl==DB?table("copy_$R"):idf_escape($fl).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $A"))||!queries("CREATE TABLE $A LIKE ".table($R))||!queries("INSERT INTO $A SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$J){$El=$J["Trigger"];list($jd,$mh)=trigger_event($J);if(!queries("CREATE TRIGGER ".($fl==DB?idf_escape("copy_$El"):idf_escape($fl).".".idf_escape($El))." $J[Timing] $jd".($mh!=""?" $mh":"")." ON $A FOR EACH ROW\n$J[Statement];"))return
false;}}foreach($um
as$R){$A=($fl==DB?table("copy_$R"):idf_escape($fl).".".table($R));$tm=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $A"))||!queries("CREATE VIEW $A AS $tm[select]"))return
false;}return
true;}function
trigger_event(array$J){$ld=explode(",",$J["Event"]);$I=[];foreach(["DELETE","INSERT","UPDATE"]as$jd){if(in_array($jd,$ld))$I[]=$jd;}$I=implode(" OR ",$I);if(in_array("UPDATE",$ld)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($J["Trigger"]),2),$_)&&preg_match('~\bOF\s+(.+)~is',$_[1],$mh))return["$I OF",$mh[1]];return[$I,""];}function
trigger($A,$R){if($A=="")return[];$K=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($A));$I=reset($K);if($I)list($I["Event"],$I["Of"])=trigger_event($I);return($I?:[]);}function
triggers($R){$I=[];foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$J){list($jd)=trigger_event($J);$I[$J["Trigger"]]=[$J["Timing"],$jd];}return$I;}function
trigger_options(){return["Timing"=>["BEFORE","AFTER"],"Event"=>(min_version('','12.0.1')?["INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",]:["INSERT","UPDATE","DELETE"]),"Type"=>["FOR EACH ROW"],];}function
routine($A,$U){$K=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($A)."
ORDER BY ORDINAL_POSITION");$k=[];foreach($K
as$J){$ae=$J["DTD_IDENTIFIER"];list($Ml,$w,$Vl)=parse_type($ae);$k[]=["field"=>$J["PARAMETER_NAME"],"type"=>$Ml,"length"=>$w,"unsigned"=>$Vl,"null"=>true,"full_type"=>$ae,"inout"=>($U=="FUNCTION"?"":$J["PARAMETER_MODE"]),"collation"=>$J["COLLATION_NAME"],];}$I=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($A))->fetch_assoc();if(!$I)return[];$I['options']=["DEFINER"=>$I['definer'],"DETERMINISTIC"=>$I['is_deterministic'],"SQL_DATA_ACCESS"=>$I['data_access'],"SQL_SECURITY"=>$I['security'],"COMMENT"=>$I['comment'],];if($k&&$k[0]['field']=='')$I['returns']=array_shift($k);$I['fields']=$k;return$I;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?["sql"=>"sql","javascript"=>"js"]:[]);}function
routine_options($zj){return["DEFINER"=>[],"DETERMINISTIC"=>["NOT DETERMINISTIC","DETERMINISTIC"],"SQL_DATA_ACCESS"=>["CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"],"SQL_SECURITY"=>["SQL SECURITY DEFINER","SQL SECURITY INVOKER"],"COMMENT"=>[],];}function
routine_id($A,array$J){return
idf_escape($A);}function
last_id($H){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$G){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$G);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Fa,$Gk){$I=get_val("SHOW CREATE TABLE ".table($R),1);if(!$Fa)$I=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$I);return$I;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($bc,$Gk=""){$A=idf_escape($bc);$I="";if(preg_match('~CREATE~',$Gk)&&($Ob=get_val("SHOW CREATE DATABASE $A",1))){set_utf8mb4($Ob);if($Gk=="DROP+CREATE")$I="DROP DATABASE IF EXISTS $A;\n";$I
.="$Ob;\n";}return$I."USE $A";}function
trigger_sql($R){$I="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$J){list($J["Event"],$J["Of"])=trigger_event($J);$I
.="\n".create_trigger(" ON ".table($J["Table"]),$J+["Type"=>"FOR EACH ROW"]).";\n";}return$I;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$j){return
driver()->convertColumn(idf_escape($j["field"]),$j);}function
unconvert_field(array$j,$I){if(preg_match("~binary~",$j["type"]))$I="UNHEX($I)";if($j["type"]=="bit")$I="CONVERT(b$I, UNSIGNED)";if($j["type"]=="vector")$I=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($I)";if(preg_match("~geom|point|linestring|polygon~",$j["type"])){$Hi=(min_version(8)?"ST_":"");$I=$Hi."GeomFromText($I, $Hi"."SRID($j[field]))";}return$I;}function
support($Ad){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$Ad);}function
kill_process($r){return
queries("KILL ".number($r));}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($wd=false){return[];}function
type_values($r){return"";}function
type_definition($r){return["kind"=>"","definition"=>""];}function
schemas(){return[];}function
get_schema(){return"";}function
set_schema($L,$Db=null){return
true;}function
schema_comment($L){return"";}function
set_schema_comment($L,$yb){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);exit;}function
page_header($rl,$i="",$Va=[],$sl="",$hh=false,$Dc=""){if($hh){header("HTTP/1.1 404 Not Found");$i=($i?:lang(99));}page_headers();if(is_ajax()&&$i){page_messages($i);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$tl=$rl.($sl!=""?": $sl":"");$ul=strip_tags($tl.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(100),'\' class=\'',lang(100),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$ul,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.3.0+97df70eb"),'">
';$Tb=adminer()->css();if(is_int(key($Tb)))$Tb=array_fill_keys($Tb,'light');$te=in_array('light',$Tb)||in_array('',$Tb);$re=in_array('dark',$Tb)||in_array('',$Tb);$Xb=($te?($re?null:false):($re?:null));$ug=" media='(prefers-color-scheme: dark)'";if($Xb!==false)echo"<link rel='stylesheet'".($Xb?"":$ug)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.3.0+97df70eb")."'>\n";echo"<meta name='color-scheme' content='".($Xb===null?"light dark":($Xb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.3.0+97df70eb");if(adminer()->head($Xb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.0+97df70eb")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Tb
as$cm=>$Jg){$b=($Jg=='dark'&&!$Xb?$ug:($Jg=='light'&&$re?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($cm)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(101))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(102)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Va!==null){$yf=(class_exists('Adminer\Db')&&connection());$y=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb">'.($yf?jump_input():'').'<a href="'.h($y?:".").'">'.get_driver(DRIVER).'</a> » ';$y=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:lang(38));if($Va===false)echo"$N\n";else{echo"<a href='".h($y.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";$Oj="";if(is_string($Va)){$Oj=$Va;$Va=[];}if($_GET["ns"]!=""||(DB!=""&&is_array($Va))){$dc="$y&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($dc.($_GET["ns"]==""?$Oj:"")).'">'.h(DB).'</a> » ';}if(is_array($Va)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Oj).'">'.h($_GET["ns"]).'</a> » ';foreach($Va
as$v=>$X){$pc=(is_array($X)?$X[1]:h($X));if($pc!="")echo"<a href='".h(ME."$v=").url_escape(is_array($X)?$X[0]:$X)."'>$pc</a> » ";}}echo"$rl\n";}if($yf){echo"<form id='jump-form'>\n";hidden_fields_get();echo(DB!=""?input_hidden("db",DB):""),(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""),"</form>\n",script("jumpSetup();");}}echo"<h2>$tl$Dc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($i);adminer()->serviceWorker();$f=&get_session("dbs");if(DB!=""&&$f&&!in_array(DB,$f,true))$f=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($hh){page_footer($hh===true?"":$hh);exit;}}function
service_worker(){$lj=has_passwords();$qb=($lj?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.3.0+97df70eb")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$qb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$dk){foreach($dk
as$km){foreach($km
as$E){if($E!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Sb){$xe=[];foreach($Sb
as$v=>$X)$xe[]="$v $X";header("Content-Security-Policy: ".implode("; ",$xe));}adminer()->headers();}function
csp(){return[["script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",],];}function
design_checksums(){$im=[];foreach(array_keys(adminer()->css())as$cm)$im[preg_replace('~\?.*~','',$cm)]=true;$I=[];foreach(["adminer.css","adminer-dark.css"]as$l){if($im[$l]&&file_exists($l)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($l),$_);$I[$l]=[(string)$_[1],Plugins::checksum($l)];}}return$I;}function
official_design_checksums(){return['adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'c9d1d11c','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'a8babfc5','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'a38b3b6a','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',];}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$gh;if(!$gh)$gh=base64_encode(rand_string());return$gh;}function
page_messages($i){$bm=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Ag=idx($_SESSION["messages"],$bm);if($Ag){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Ag)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$bm]);}if($i)echo"<div class='error'>$i</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Ig=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Ig);echo"</div>\n";if($Ig!="auth")echo'<form method="post">
<p class="logout">
<span title="',lang(40),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(103),'\' id=\'logout\'>
',adminer()->donation()."\n",input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Rg){while($Rg>=2147483648)$Rg-=4294967296;while($Rg<=-2147483649)$Rg+=4294967296;return(int)$Rg;}function
long2str(array$W,$wm){$Gj='';foreach($W
as$X)$Gj
.=pack('V',$X);if($wm)return
substr($Gj,0,end($W));return$Gj;}function
str2long($Gj,$wm){$W=array_values(unpack('V*',str_pad($Gj,4*ceil(strlen($Gj)/4),"\0")));if($wm)$W[]=strlen($Gj);return$W;}function
xxtea_mx($Fm,$Em,$Jk,$_f){return
int32((($Fm>>5&0x7FFFFFF)^$Em<<2)+(($Em>>3&0x1FFFFFFF)^$Fm<<4))^int32(($Jk^$Em)+($_f^$Fm));}function
encrypt_string($Dk,$v){if($Dk=="")return"";$v=array_values(unpack("V*",pack("H*",md5($v))));$W=str2long($Dk,true);$Rg=count($W)-1;$Fm=$W[$Rg];$Em=$W[0];$Ti=floor(6+52/($Rg+1));$Jk=0;while($Ti-->0){$Jk=int32($Jk+0x9E3779B9);$Mc=$Jk>>2&3;for($Wh=0;$Wh<$Rg;$Wh++){$Em=$W[$Wh+1];$Qg=xxtea_mx($Fm,$Em,$Jk,$v[$Wh&3^$Mc]);$Fm=int32($W[$Wh]+$Qg);$W[$Wh]=$Fm;}$Em=$W[0];$Qg=xxtea_mx($Fm,$Em,$Jk,$v[$Wh&3^$Mc]);$Fm=int32($W[$Rg]+$Qg);$W[$Rg]=$Fm;}return
long2str($W,false);}function
decrypt_string($Dk,$v){if($Dk=="")return"";if(!$v)return
false;$v=array_values(unpack("V*",pack("H*",md5($v))));$W=str2long($Dk,false);$Rg=count($W)-1;$Fm=$W[$Rg];$Em=$W[0];$Ti=floor(6+52/($Rg+1));$Jk=int32($Ti*0x9E3779B9);while($Jk){$Mc=$Jk>>2&3;for($Wh=$Rg;$Wh>0;$Wh--){$Fm=$W[$Wh-1];$Qg=xxtea_mx($Fm,$Em,$Jk,$v[$Wh&3^$Mc]);$Em=int32($W[$Wh]-$Qg);$W[$Wh]=$Em;}$Fm=$W[$Rg];$Qg=xxtea_mx($Fm,$Em,$Jk,$v[$Wh&3^$Mc]);$Em=int32($W[0]-$Qg);$W[0]=$Em;$Jk=int32($Jk-0x9E3779B9);}return
long2str($W,true);}$ui=[];if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($v)=explode(":",$X);$ui[$v]=$X;}}function
add_invalid_login(){$Na=get_temp_dir()."/adminer-invalid";foreach(glob("$Na*")?:[$Na]as$l){$n=file_open_lock($l);if($n)break;}if(!$n)$n=file_open_lock("$Na-".rand_string());if(!$n)return;$mf=json_decode(stream_get_contents($n),true);$ol=time();if($mf){foreach($mf
as$nf=>$X){if($X[0]<$ol)unset($mf[$nf]);}}$kf=&$mf[adminer()->bruteForceKey()];if(!$kf)$kf=[$ol+30*60,0];$kf[1]++;file_write_unlock($n,json_encode($mf));}function
check_invalid_login(array&$ui){$mf=[];foreach(glob(get_temp_dir()."/adminer-invalid*")as$l){$n=file_open_lock($l);if($n){$mf=json_decode(stream_get_contents($n),true);file_unlock($n);break;}}$v=adminer()->bruteForceKey();$kf=idx($mf,$v,[]);$fh=($kf[1]>29?$kf[0]-time():0);if($fh>0){$i=lang(104,ceil($fh/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$v==$_SERVER["REMOTE_ADDR"])$i
.='<br>'.lang(105,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($i,$ui,false);}}function
password_required(){static$I;if($I===null){$I=(bool)get_session("password_required");if(!$I){$Rb=adminer()->credentials();$I=!is_object(Driver::connect($Rb[0],$Rb[1],""));if($I)set_session("password_required",true);}}return$I;}function
require_password_link($E){$Ng="<a href='https://www.adminer.org/password/'".target_blank().">".lang(106)."</a>";if(!function_exists('password_hash'))return" $Ng";$xi=($E!==null?$E:base64_encode(substr(hex2bin(rand_string()),0,12)));$we=password_hash($xi,PASSWORD_DEFAULT);$l="adminer-plugins.php";$qd=file_exists("adminer-plugins.php");if($qd)$if=($E!==null?lang(107,"<b>$l</b>"):lang(108,"<b>$l</b>","<b>$xi</b>"));else{$l="<button name='password_less' value='".h($we)."' class='link'>$l</button>";$if=($E!==null?lang(109,$l):lang(110,$l,"<b>$xi</b>"));}$Vf="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($we)."'</span>),";$I="<p>$if
<pre><code class='jush'>".($qd?$Vf:"&lt;?php\n<a>return</a> <a>array</a>(\n$Vf\n);")."</code></pre>
<p>$Ng
";return" <a href='#password-less' class='toggle'>".lang(111)."</a>
<div id='password-less' class='hidden'>".($qd?$I:"<form method='post'>\n".$I.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ea=$_POST["auth"];if($Ea&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$rm=$Ea["driver"];$N=$Ea["server"];$V=$Ea["username"];$E=(string)$Ea["password"];$g=$Ea["db"];set_password($rm,$N,$V,$E);$_SESSION["db"][$rm][$N][$V][$g]=true;if($Ea["permanent"]){$v=implode("-",array_map('base64_encode',[$rm,$N,$V,$g]));$Ni=adminer()->permanentLogin(true);$ui[$v]="$v:".base64_encode($Ni?encrypt_string($E,$Ni):"");cookie("adminer_permanent",implode(" ",$ui));}if(!array_diff(array_keys($_POST),["auth","token"])||$rm!=DRIVER||$N!=SERVER||$V!==$_GET["username"]||$g!=DB)redirect(auth_url($rm,$N,$V,$g));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(["pwds","db","dbs","queries"]as$v)set_session($v,null);unset_permanent($ui);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(112).' '.lang(113," href='https://www.adminer.org/donation/'".target_blank()));}elseif($ui&&!$_SESSION["pwds"]){session_regenerate_id();$Ni=adminer()->permanentLogin();foreach($ui
as$v=>$X){list(,$ob)=explode(":",$X);list($rm,$N,$V,$g)=array_map('base64_decode',explode("-",$v));set_password($rm,$N,$V,decrypt_string(base64_decode($ob),$Ni));$_SESSION["db"][$rm][$N][$V][$g]=true;}}function
unset_permanent(array&$ui){foreach($ui
as$v=>$X){list($rm,$N,$V,$g)=array_map('base64_decode',explode("-",$v));if($rm==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$g==DB)unset($ui[$v]);}cookie("adminer_permanent",implode(" ",$ui));}function
auth_error($i,array&$ui,$lf=true){$ek=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$ek]||$_GET[$ek])&&!$_SESSION["token"])$i=lang(114);elseif($lf&&($E=get_password())!==null){restart_session();add_invalid_login();if($E===false)$i
.=($i?'<br>':'').lang(115,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($ui);}}if(!$_COOKIE[$ek]&&$_GET[$ek]&&ini_bool("session.use_only_cookies"))$i=lang(116);$ai=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$ai["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(42),$i,null);echo"<form method='post'>\n","<div>";if(hidden_fields($_POST,["auth","token"]))echo"<p class='message'>".lang(117)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($ui);page_header(lang(118),lang(119,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($ui);$Rb=adminer()->credentials();$e=Driver::connect($Rb[0],$Rb[1],$Rb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(["vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)]);}}$bg=null;if(!is_object($e)||($bg=adminer()->login($_GET["username"],get_password()))!==true){$i=(is_string($e)?nl_br(h($e)):(is_string($bg)?$bg:lang(120))).(preg_match('~^ | $~',get_password())?'<br>'.lang(121):'');auth_error($i,$ui);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(103),lang(122));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);$i='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$i=lang(122).' '.lang(123);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$i=lang(124,"<b>post_max_size</b>");if(isset($_GET["sql"]))$i
.=' '.lang(125);}function
print_select_result($H,$Db=null,array$Mh=[],&$x=0,&$Nc=false){$Xf=[];$u=[];$d=[];$T=[];$Li=[];$Rc=[];$Nl=[];$I=[];$Kg=$Nc;$Nc=false;for($q=0;(!$x||$q<$x)&&is_array($J=$H->fetch_row());$q++){if(!$q){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($Kg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($wf=0;$wf<count($J);$wf++){$j=$H->fetch_field();$A=$j->name;$R=(isset($j->table)?$j->table:"");$Lh=(isset($j->orgtable)?$j->orgtable:"");$Kh=(isset($j->orgname)?$j->orgname:$A);$Ml=driver()->typeName($j);if($Mh&&JUSH=="sql")$Xf[$wf]=($A=="table"?"table=":($A=="possible_keys"?"indexes=":null));elseif($Lh!=""){$ra=($R!=""?$R:$Lh);if($R!="")$I[$R]=$Lh;if(!isset($u[$ra])){if(!isset($Li[$Lh])){$Li[$Lh]=[];foreach(indexes($Lh,$Db)as$t){if($t["type"]=="PRIMARY"){$Li[$Lh]=array_flip($t["columns"]);break;}}}$T[$ra]=$Lh;$u[$ra]=$Li[$Lh];$d[$ra]=$Li[$Lh];}if(isset($d[$ra][$Kh])){unset($d[$ra][$Kh]);$u[$ra][$Kh]=$wf;$Xf[$wf]=$ra;}elseif($Kg&&isset($j->orgname)&&$j->db==DB&&!is_blob(["type"=>$Ml]))$Rc[$wf]=[$ra,$Kh,preg_match('~text|json|lob~',$Ml)];}$Nl[$wf]=$Ml;echo"<th title='".h(trim(($Lh!=""?"$Lh.$Kh":($j->name!=$Kh?$Kh:""))." ".$Ml))."'>".h($A).($Mh?doc_link(['sql'=>"explain-output.html#explain_".strtolower($A),'mariadb'=>"explain/#the-columns-in-explain-select",]):"");}foreach($Rc
as$wf=>$bb){if($d[$bb[0]])unset($Rc[$wf]);}echo"<tbody>\n";}$Ne=[];foreach($u
as$ra=>$t){if($t&&!$d[$ra]){$s="";foreach($t
as$rb=>$wf){if($J[$wf]===null){$s=null;break;}$s
.="&where[".url_escape(bracket_escape($rb))."]=".url_escape($J[$wf]);}$Ne[$ra]=$s;}}echo"<tr>";foreach($J
as$v=>$X){$y="";if(isset($Xf[$v])){if($Mh&&JUSH=="sql"){$R=$J[array_search("table=",$Xf)];$y=ME.$Xf[$v].url_escape($Mh[$R]!=""?$Mh[$R]:$R);}elseif(idx($Ne,$Xf[$v])!==null)$y=ME."edit=".url_escape($T[$Xf[$v]]).$Ne[$Xf[$v]];}$Je=select_value($X,$y,['type'=>(preg_match('~binary~',$Nl[$v])?'blob':$Nl[$v])],null);$b="";$bb=idx($Rc,$v);if($bb&&idx($Ne,$bb[0])!==null&&is_utf8($X)){$Nc=true;$b=" data-name='".h("val[".bracket_escape($T[$bb[0]])."][".bracket_escape(substr($Ne[$bb[0]],1))."][".bracket_escape($bb[1])."]")."' data-text='".($bb[2]?1:0)."'".(html_entity_decode(strip_tags($Je),ENT_QUOTES,"UTF-8")!==$X?" data-value='".h($X)."'":"");}echo"<td".(preg_match(number_type(),$Nl[$v])?" class='number'":"")."$b>$Je";}}$x=$q;echo($q?"</table>\n</div>":"<p class='message'>".lang(22))."\n";return$I;}function
textarea($A,$Y,$K=10,$vb=80,$zf=JUSH,$fd=0){echo"<textarea name='".h($A)."' rows='$K' cols='$vb' class='sqlarea jush-".h($zf)."' spellcheck='false' wrap='off'".($fd?" data-error-line='$fd'":"").">\n";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$B,$Y="",$vi=""){if($B&&$Y!=""&&!isset($B[$Y]))$B=[$Y=>$Y]+$B;$el=($B?"select":"input");return"<$el$b".($B?"><option value=''>$vi".optionlist($B,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$vi'>");}function
json_row($v,$X=null,$id=true){static$Md=true;if($Md)echo"{";if($v!=""){echo($Md?"":",")."\n\t\"".addcslashes($v,"\r\n\t\"\\/").'": '.($X!==null?($id?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Md=false;}else{echo"\n}\n";$Md=true;}}function
flat_collations(){$ub=collations();return(is_array(reset($ub))?call_user_func_array('array_merge',array_values($ub)):$ub);}function
edit_type($v,array$j,array$ub,array$Td=[],array$yd=[]){$U=(string)$j["type"];echo"<td><select name='".h($v)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($Td[$U])&&!in_array($U,$yd))$yd[]=$U;$Ek=driver()->structuredTypes();if($Td)$Ek[lang(126)]=$Td;echo
optionlist(array_merge($yd,$Ek),$U),"</select><td>","<input name='".h($v)."[length]' value='".h($j["length"])."' size='3'".(!$j["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($ub?"<input list='collations' name='".h($v)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($j["collation"])."' placeholder='(".lang(127).")'>":''),(driver()->unsigned?"<select name='".h($v)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$j["unsigned"]).'</select>':''),(isset($j['on_update'])?"<select name='".h($v)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist([""=>"(".lang(128).")","CURRENT_TIMESTAMP"],(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"CURRENT_TIMESTAMP":$j["on_update"])).'</select>':''),($Td?"<select name='".h($v)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(129).")".optionlist(explode("|",driver()->onActions),$j["on_delete"])."</select> ":" ");}function
option_types($U,$Nl){return" data-types='".h($Nl)."'".(preg_match("~$Nl~",$U)?"":" class='hidden'");}function
process_length($w){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$w))return"(max)";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*(\d+)\s+(CHAR|BYTE)\s*\)?\s*$~i',$w,$_))return"($_[1] ".strtoupper($_[2]).")";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*\*\s*,\s*(-?\d+)\s*\)?\s*$~',$w,$_))return"(*,$_[1])";$cd=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$cd(?:\\s*,\\s*$cd)*+\\s*\\)?\\s*\$~",$w)&&preg_match_all("~$cd~",$w,$fg)?"(".implode(",",$fg[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$w)));}function
process_in($X){$X=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$X);$Zi="'(?:[^']|'')*+'";$vf=(preg_match("~^\\s*$Zi(?:\\s*,\\s*$Zi)*+\\s*\$~",$X)&&preg_match_all("~$Zi~",$X,$fg)?array_map('Adminer\idf_unescape',$fg[0]):array_map('trim',explode(",",$X)));return"(".implode(", ",array_map('Adminer\q',$vf)).")";}function
process_between($X){$Zi="('(?:[^']|'')*+')";$bj=(preg_match("~^\\s*$Zi\\s+AND\\s+$Zi\\s*\$~i",$X,$_)?[idf_unescape($_[1]),idf_unescape($_[2])]:preg_split('~\s+AND\s+~i',trim($X)));return(count($bj)==2?q($bj[0])." AND ".q($bj[1]):"NULL AND NULL");}function
process_type(array$j,$sb="COLLATE"){return" ".(is_user_type($j["type"])?idf_escape($j["type"]):$j["type"]).process_length($j["length"]).(preg_match(number_type(),$j["type"])&&in_array($j["unsigned"],driver()->unsigned)?" $j[unsigned]":"").(preg_match('~'.text_type().'~',$j["type"])&&$j["collation"]?" $sb ".(JUSH=="mssql"&&preg_match('~^\w+$~',$j["collation"])?$j["collation"]:q($j["collation"])):"");}function
process_field(array$j,array$Kl){return[idf_escape(trim($j["field"])),process_type($Kl),($j["null"]?" NULL":" NOT NULL"),default_value($j),(preg_match('~timestamp|datetime~',$j["type"])&&$j["on_update"]?" ON UPDATE $j[on_update]".process_length($j["length"]):""),(support("comment")&&$j["comment"]!=""?" COMMENT ".q($j["comment"]):""),($j["auto_increment"]?auto_increment():null),];}function
default_value(array$j){if($j["default"]===null)return"";$h=str_replace("\r","",$j["default"]);$ee=$j["generated"];$Q=!preg_match('~]$~',$j["length"])&&(preg_match('~char|binary|text|clob|json|enum|set|String~',$j["type"])||driver()->enumLength($j));$bi=(JUSH=="sqlite"||(JUSH=="sql"&&$j["default_sql"]&&!preg_match("~^(current_timestamp(\\(\\d*\\))?|[bx]'[\\da-f]*')$~i",$h)));return(in_array($ee,driver()->generated)?(JUSH=="mssql"?" AS ($h)".($ee=="VIRTUAL"?"":" $ee"):" GENERATED ALWAYS AS ($h) $ee"):(preg_match('~^GENERATED ~i',$h)?" $h":" DEFAULT ".(!$j["default_sql"]&&($Q||preg_match('~^(?![a-z])~i',$h))?(JUSH=="sql"&&preg_match('~text|json~',$j["type"])?"(".q($h).")":q($h)):($bi?"($h)":$h))));}function
edit_fields(array$k,array$ub,$U="TABLE",array$Td=[]){$k=array_values($k);$ic=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$zb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(130):lang(131)),"<th id='label-type'>".lang(55)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(132),"<th>".lang(133);if($U=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(57)."'>A_I</abbr>",doc_link(['sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",]),"<th id='label-default'$ic>".lang(58),(support("comment")?"<th id='label-comment'$zb>".lang(56):"");$Kf=!support("move_col");echo"<td>".icon("plus","add[".($Kf?count($k):0)."]","+",lang(134),($Kf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($k
as$q=>$j){$q++;$Nh=$j[($_POST?"orig":"field")];$wc=(isset($_POST["add"][$q-1])||(isset($j["field"])&&!idx($_POST["drop_col"],$q)))&&(support("drop_col")||$Nh=="");echo"<tr".($wc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$q][inout]",explode("|",driver()->inout),$j["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(135))." ":"");if($wc)echo"<input name='fields[$q][field]' value='".h($j["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$q-1])?" autofocus":"").">";echo
input_hidden("fields[$q][orig]",$Nh);edit_type("fields[$q]",$j,$ub,$Td);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$q][null]",1,$j["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$q'".($j["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$ic>".html_select("fields[$q][generated]",array_merge(["","DEFAULT","DEFAULT SQL"],driver()->generated),$j["generated"],"","label-default")." ";$b=" name='fields[$q][default]' aria-labelledby='label-default'";$Y=h($j["default"]);echo(preg_match('~\n~',$j["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$q][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$zb>".adminer()->commentInput('COLUMN',$b,$j["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$q]","+",lang(134))." ":""),($Nh==""||support("drop_col")?icon("cross","drop_col[$q]","x",lang(136)):"");}}function
process_fields(array&$k){if($_POST["add"]){$k=array_values($k);array_splice($k,key($_POST["add"]),0,[[]]);}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Ic,$Ob,$Jc,$kl,$Kc,$z,$_g,$yg,$zg,$vh,$bh){if($_POST["drop"])query_redirect($Ic,$z,$_g);elseif($vh=="")query_redirect($Ob,$z,$zg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($z,$yg,queries($Ic)&&queries($Ob)&&driver()->commit());driver()->rollback();}elseif($vh!=$bh){$Qb=queries($Ob);queries_redirect($z,$yg,$Qb&&queries($Ic));if($Qb&&$Jc)queries($Jc);}else
queries_redirect($z,$yg,queries($kl)&&queries($Kc)&&queries($Ic)&&queries($Ob));}function
create_trigger($yh,array$J){$ql=" $J[Timing] $J[Event]".(preg_match('~ OF~',$J["Event"])?" $J[Of]":"");return"CREATE TRIGGER ".idf_escape($J["Trigger"]).(JUSH=="mssql"?$yh.$ql:$ql.$yh).preg_replace('~[\s;]+$~',''," $J[Type]\n$J[Statement]").";";}function
q_dollar($Q){$oc='$$';while(strpos($Q.$oc,$oc)!=strlen($Q))$oc='$_'.substr($oc,1);return$oc.$Q.$oc;}function
routine_collate($tb){static$gb=[];if($tb&&!$gb){foreach(collations()as$fb=>$om){foreach((array)$om
as$X)$gb[$X]=$fb;}}return($gb[$tb]?"CHARACTER SET ".q($gb[$tb])." ":"")."COLLATE";}function
create_routine($zj,array$J){$O=[];$k=$J["fields"];ksort($k);foreach($k
as$j){if($j["field"]!=""){$df=(preg_match("~^(".driver()->inout.")\$~",$j["inout"])?$j["inout"]:"");$O[]="\n  ".(JUSH=="mssql"?"@$j[field]".process_type($j).($df?" $df":""):($df?"$df ":"").idf_escape($j["field"]).process_type($j,routine_collate($j["collation"])));}}$lc="";$B=[];foreach(routine_options($zj)as$v=>$pm){$Y=idx($J["options"],$v,"");if($v=="DEFINER")$lc=($Y?" $v=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$pm){if($Y!="")$B[]="$v ".q($Y);}elseif($Y!=reset($pm)&&in_array($Y,$pm))$B[]=$Y;}$If=$J["language"];$mc=preg_replace('~[\s;]+$~','',$J["definition"]);$Ec=(JUSH=="pgsql"||($If&&$If!="sql"));$Zh=($O?implode(",",$O)."\n":"");return"CREATE$lc $zj ".table(trim($J["name"])).(JUSH=="mssql"&&$zj=="PROCEDURE"?rtrim($Zh):" ($Zh)").($zj=="FUNCTION"?"\nRETURNS".process_type($J["returns"],routine_collate($J["returns"]["collation"])):"").($If?" LANGUAGE $If":"").($B?"\n".implode(" ",$B):"").($Ec?" AS ".q_dollar("\n".trim($mc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$mc;");}function
remove_definer($G){$lc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($lc).')','\1',$G);}function
object_name($U,$R,array$d){return
str_replace(["{table}","{columns}"],[$R,implode("_",$d)],adminer()->namePattern($U));}function
format_foreign_key(array$m,$A=""){$g=$m["db"];$ih=$m["ns"];return($A!=""?" CONSTRAINT ".idf_escape($A):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$m["source"])).") REFERENCES ".($g!=""&&$g!=$_GET["db"]?idf_escape($g).".":"").($ih!=""&&$ih!=$_GET["ns"]?idf_escape($ih).".":"").idf_escape($m["table"])." (".implode(", ",array_map('Adminer\idf_escape',$m["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$m["on_delete"])?" ON DELETE $m[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$m["on_update"])?" ON UPDATE $m[on_update]":"").($m["deferrable"]?" $m[deferrable]":"");}function
tar_file($l,$vl){$I=pack("a100a8a8a8a12a12",$l,644,0,0,decoct($vl->size),decoct(time()));$lb=8*32;for($q=0;$q<strlen($I);$q++)$lb+=ord($I[$q]);$I
.=sprintf("%06o",$lb)."\0 ";echo$I,str_repeat("\0",512-strlen($I));$vl->send();echo
str_repeat("\0",511-($vl->size+511)%512);}function
doc_version(){$ck=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$ck,$_);return($_[1]>=18?$_[1]:"19");}$kj=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$sm=(preg_match($kj,$ck,$_)?$_[0]:"");if(JUSH=='mssql')return($sm>=15?"sql-server-ver$sm":($sm==12?"azuresqldb-current":"sql-server-2017"));return$sm;}function
doc_link(array$qi,$ll="📖"){$sm=doc_version();$dm=['sql'=>"https://dev.mysql.com/doc/refman/$sm/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$sm)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$sm/",];if(connection()->flavor=='maria'){$dm['sql']="https://mariadb.com/kb/en/";$qi['sql']=($qi['mariadb']?:str_replace(".html","/",$qi['sql']));}if(connection()->flavor=='cockroach'&&$qi['cockroach']){$dm['pgsql']="https://docs.cockroachlabs.com/docs/v$sm/";$qi['pgsql']=$qi['cockroach'];}return($qi[JUSH]?" <a href='".h($dm[JUSH].$qi[JUSH].(JUSH=='mssql'?"?view=$sm":""))."'".target_blank()." class='doc' title='".lang(137)."'>$ll</a>":"");}function
db_size($g){if(!connection()->select_db($g))return"?";$I=0;foreach(table_status()as$S)$I+=$S["Data_length"]+$S["Index_length"];return
adminer()->formatSizeValue($I);}function
set_utf8mb4($Ob){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$Ob)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'),null,301);if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(17).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$f=adminer()->databases();if($f)redirect(ME."db=".url_escape($f[0]));}if($_POST["db"]&&!$i)queries_redirect(substr(ME,0,-1),lang(138),drop_databases($_POST["db"]));page_header(lang(139),$i,false);echo"<p class='links'>\n";foreach(['database'=>lang(140),'privileges'=>lang(76),'processlist'=>lang(141),'variables'=>lang(142),'status'=>lang(143),]as$v=>$X){if(support($v))echo"<a href='".h(ME)."$v='>$X</a>\n";}echo"<p>".lang(144,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(145,"<b>".h(logged_user())."</b>")."\n";$f=adminer()->databases();$Kd=$_GET["filter"];if($Kd!=""){foreach($f
as$v=>$g){if(stripos($g,$Kd)===false)unset($f[$v]);}}if($f){$Kj=support("scheme");echo"<form method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(17).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(146)."</a>":"")."<th>".lang(147)."<th>".lang(148)."<th>".lang(149)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(150)."</a>"."<tbody>\n";$f=($_GET["dbsize"]?count_tables($f):array_flip($f));foreach($f
as$g=>$T){$yj=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($g);$r=h("Db-".$g);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$g,in_array($g,(array)$_POST["db"]),"","","",$r):""),"<th><a href='$yj' id='$r'>".h($g)."</a>";$tb=h(db_collation($g,[]));echo"<td>".(support("database")?"<a href='$yj".($Kj?"&amp;ns=":"")."&amp;database=' title='".lang(72)."'>$tb</a>":$tb),"<td align='right'><a href='$yj&amp;schema=' id='tables-".h($g)."' title='".lang(75)."'>".($_GET["dbsize"]?format_number($T):"?")."</a>","<td align='right' id='size-".h($g)."'>".($_GET["dbsize"]?db_size($g):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(151)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(152)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$la=adminer();$zi=($la
instanceof
Plugins?$la->plugins:[]);$Hc=($la
instanceof
Plugins?$la->drivers:[]);$tc=design_checksums();if($zi||$Hc||$tc){$mb=($la
instanceof
Plugins?$la->checksums():[]);$oh=Plugins::officialChecksums();$Yl=function($cm){return" (<a href='$cm'".target_blank()." class='update'>".VERSION."</a>)";};$yi=function($Gd)use($mb,$oh,$Yl){return($mb[$Gd]&&$oh[$Gd]&&$mb[$Gd]!==$oh[$Gd]?$Yl("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(153)."</h3>\n<ul>\n";foreach($zi
as$wi){$ij=new
\ReflectionObject($wi);$qc=Plugins::describe($wi);echo"<li><b>".h(Plugins::pluginName($wi))."</b>".($qc!=""?": $qc":"").$yi(basename((string)$ij->getFileName(),'.php'))."\n";}foreach($Hc
as$r=>$A)echo"<li><b>".h($r)."</b>: ".h($A).$yi(basename((string)$la->driverFiles[$r],'.php'))."\n";if($tc){$qh=official_design_checksums();foreach($tc
as$l=>$sc){list($A,$lb)=$sc;$ph=$qh["$A/$l"];echo"<li><b>".h($l)."</b>".h($A?": $A":"").($ph&&$ph!==$lb?$Yl("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Hb){$this->size+=strlen($Hb);fwrite($this->handler,$Hb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$k=fields($a);header("Content-Type: application/octet-stream");$pm=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$pm)).".".friendly_url($_GET["field"]));$M=[idf_escape($_GET["field"])];$H=driver()->select($a,$M,[where($_GET,$k)],$M);$J=($H?$H->fetch_row():[]);echo
driver()->value($J[0],$k[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$k=fields($a);if(!$k)$i=adminer()->error();$S=table_status1($a);$A=adminer()->tableName($S);$i=$i?:h($S["Error"]);page_header(($k&&is_view($S)?$S['Engine']=='materialized view'?lang(154):lang(155):lang(156)).": ".($A!=""?$A:h($a)),$i,[],"",!$k,($k?doc_link([JUSH=>driver()->tableHelp($a,is_view($S))]):""));$xj=[];foreach($k
as$v=>$j)$xj+=$j["privileges"];adminer()->selectLinks($S,(isset($xj["insert"])||!support("table")?"":null));$yb=$S["Comment"];if($yb!="")echo"<p class='nowrap'>".lang(56).": ".adminer()->commentValue('TABLE',$yb)."\n";if($k)adminer()->tableStructurePrint($k,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$J){$y=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($J["ns"]),ME);echo"<li><a href='".h($y."table=".url_escape($J["table"]))."'>".($J["ns"]!=$_GET["ns"]?"<b>".h($J["ns"])."</b>.":"").h($J["table"])."</a>";}echo"</ul>\n";}$bf=driver()->inheritsFrom($a);if($bf){echo"<h3>".lang(157)."</h3>\n";tables_links($bf);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(158)."</h3>\n";$u=indexes($a);if($u)adminer()->tableIndexesPrint($u,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(159)."</a>\n";echo"</div>\n";}if(!is_view($S)&&driver()->supportsAlterTable($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(126)."</h3>\n";$Td=foreign_keys($a);$wa=function_exists('Adminer\alter_table');if($Td){echo"<table>\n","<thead><tr><th>".lang(160)."<th>".lang(161)."<th>".lang(129)."<th>".lang(128).($wa?"<td class='hover'>":"")."<tbody>\n";foreach($Td
as$A=>$m){echo"<tr".(!is_int($A)?" title='".h($A)."'":"").">","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$m["source"]))."</i>";$y=($m["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($m["db"]),ME):($m["ns"]!=""?preg_replace('~&ns=[^&]*~',"&ns=".url_escape($m["ns"]),ME):ME));echo"<td><a href='".h($y."table=".url_escape($m["table"]))."'>".($m["db"]!=""&&$m["db"]!=DB?"<b>".h($m["db"])."</b>.":"").($m["ns"]!=""&&$m["ns"]!=$_GET["ns"]?"<b>".h($m["ns"])."</b>.":"").h($m["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$m["target"]))."</i>)","<td>".h($m["on_delete"]),"<td>".h($m["on_update"]),($wa?'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($A)).'">'.lang(162).'</a>':''),"\n";}echo"</table>\n";}echo($wa?'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(163)."</a>\n":''),"</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(164)."</h3>\n";$ib=driver()->checkConstraints($a);if($ib){echo"<table>\n";foreach($ib
as$v=>$X)echo"<tr".($v!=$X?" title='".h($v)."'":"").">","<td><code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($v))."'>".lang(162)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(165)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")&&driver()->supportsAlterTable($S)){echo"<div>\n","<h3 id='triggers'>".lang(166)."</h3>\n";$Hl=triggers($a);if($Hl){echo"<table>\n";foreach($Hl
as$v=>$X){echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($v)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($v))."'>".lang(162)."</a>";$zj=$X[2];if($zj){$Aj=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($zj["ns"]),ME).'function='.url_escape($zj["function"]).'&name='.url_escape($zj["name"]);echo", <a href='".h($Aj)."' title='".h($zj["name"])."'>".lang(167)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(168)."</a>\n","</div>\n";}$hk=driver()->shadowTables($a);if($hk){echo"<h3 id='shadow-tables'>".lang(169)."</h3>\n";tables_links($hk);}$af=driver()->inheritedTables($a);if($af){echo"<h3 id='partitions'>".lang(170)."</h3>\n";$ei=driver()->partitionsInfo($a);if($ei)echo"<p><code class='sql jush-".JUSH."'>BY ".h("$ei[partition_by]($ei[partition])")."</code>\n";tables_links($af);}}elseif(isset($_GET["schema"])){page_header(lang(75),"",[],h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$hj,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($hj,$R)as$A=>$jj){if($A!=$R)$d[$R]=max($d[$R],schema_column($A,$hj,$d)+1);}}return$d[$R];}function
type_class($U){foreach(['char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',]as$v=>$X){if(preg_match("~$v|$X~",$U))return" class='$v'";}}$Vk=[];$Xk=[];$Wk=[];$Dd=[];$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$fg,PREG_SET_ORDER);foreach($fg
as$q=>$_){$Vk[$_[1]]=[(float)$_[2],(float)$_[3]];$Xk[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$L=[];$hj=[];$Td=[];$ta=driver()->allFields();$Be=[];$Yk=[];foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!=""&&!$S["dependent"])$Yk[$R]=$S;else$Be[$R]=true;}}foreach($Yk
as$R=>$S){$F=0;$L[$R]["fields"]=[];foreach($ta[$R]as$j){$F+=1.25;$Dd[$R][$j["field"]]=$F;$L[$R]["fields"][$j["field"]]=$j;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$Be[$X["table"]]){$Td[$R][]=$X;$hj[$X["table"]][$R]=[];}}}$d=[];$ie=[];$Dm=[];$ne=[];foreach(array_keys($L)as$A)schema_column($A,$hj,$d);arsort($d);foreach($d
as$A=>$c){$Gg=null;foreach((array)idx($Td,$A)as$X){if($X["table"]!=$A&&$L[$X["table"]])$Gg=($Gg===null?$d[$X["table"]]:min($Gg,$d[$X["table"]]));}$d[$A]=max($c,(int)$Gg-1);}$al=[];foreach($L
as$A=>$R){$c=$d[$A];$ie[$c][]=$A;$nl=.75*strlen($A);foreach($R["fields"]as$j)$nl=max($nl,.65*strlen($j["field"]));$al[$A]=ceil($nl)+1;$Dm[$c]=max(idx($Dm,$c,0),$al[$A]);}foreach($Td
as$A=>$om){foreach($om
as$X){$me=$d[$A]+(idx($d,$X["table"],$d[$A])>$d[$A]?1:0);$ne[$me]=idx($ne,$me,0)+1;}}ksort($ie);$_e=0;$Cm=0;$wb=0;$Ji=null;$Tk=[];foreach($ie
as$c=>$T){if($Ji!==null){$wb=round($wb+$Dm[$Ji]+1.7+idx($ne,$c,0)*.1,1);$C=[];foreach($T
as$A){$Jk=0;$Nb=0;$Yg=array_keys((array)idx($hj,$A));foreach((array)idx($Td,$A)as$X)$Yg[]=$X["table"];foreach($Yg
as$Sg){if($L[$Sg]&&$d[$Sg]<$c){$Jk+=$L[$Sg]["pos"][0];$Nb++;}}$C[$A]=($Nb?$Jk/$Nb:$_e);}asort($C);$T=array_keys($C);}$yl=0;foreach($T
as$A){$F=1.25*count($L[$A]["fields"]);$L[$A]["pos"]=($Vk[$A]?:[$yl,$wb]);$Tk[$A]=$L[$A]["pos"][1];$yl+=2.5+$F;$_e=max($_e,$L[$A]["pos"][0]+2.5+$F);$Cm=max($Cm,round($L[$A]["pos"][1]+$Dm[$c],1));if(!$Vk[$A])$Wk[]="\n\t'".js_escape($A)."': [ ".$L[$A]["pos"][0].", ".$L[$A]["pos"][1]." ]";}$Ji=$c;}$Of=[];$Oa=[];foreach($Td
as$A=>$om){foreach($om
as$X){$gl=idx($Tk,$X["table"],$Tk[$A]);$rk=$Tk[$A]+$Dm[$d[$A]];$wj=($gl-1>$rk);$Mf=($wj?$rk+1:min($Tk[$A],$gl)-1);$Na=idx($Oa,(string)$Mf,0);$Oa[(string)$Mf]=$Na+1;$Mf=round($wj?min($Mf+$Na*.1,$gl-1):$Mf-$Na*.1,1);while($Of[(string)$Mf])$Mf-=.0001;$L[$A]["references"][$X["table"]][(string)$Mf]=[$X["source"],$X["target"]];$hj[$X["table"]][$A][(string)$Mf]=$X["target"];$Of[(string)$Mf]=true;}}echo'<div id="schema" style="height: ',$_e,'em; width: ',$Cm,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$Xk)."\n",'};
const tablePosDefault = {',implode(",",$Wk)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$_e,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($L
as$A=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$al[$A]."em;'>",'<a href="'.h(ME).'table='.url_escape($A).'"><b>'.h($A)."</b></a>";foreach($R["fields"]as$j){$X='<span'.type_class($j["type"]).' title="'.h($j["type"].($j["length"]?"($j[length])":"").($j["null"]?" NULL":'')).'">'.h($j["field"]).'</span>';echo"<br>".($j["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$hl=>$jj){foreach($jj
as$Mf=>$ej){$Nf=$Mf-$R["pos"][1];$Gk=($Nf>0?"left: 100%; width: calc($Nf"."em - 100%)":"left: $Nf"."em");$Cm=($Nf>0?"100%":(-$Nf)."em");$q=0;foreach($ej[0]as$qk)echo"\n<div class='references' title='".h($hl)."' id='refs$Mf-".($q++)."' style='$Gk"."; top: ".$Dd[$A][$qk]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Cm;'></div></div>";}}foreach((array)$hj[$A]as$hl=>$jj){foreach($jj
as$Mf=>$il){$Nf=$Mf-$R["pos"][1];$q=0;foreach($il
as$fl)echo"\n<div class='references arrow' title='".h($hl)."' id='refd$Mf-".($q++)."' style='left: $Nf"."em; top: ".$Dd[$A][$fl]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Nf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($L
as$A=>$R){foreach((array)$R["references"]as$hl=>$jj){if($L[$hl]){foreach($jj
as$Mf=>$ej){$Hg=$_e;$ng=-10;foreach($ej[0]as$v=>$qk){$Ai=$R["pos"][0]+$Dd[$A][$qk];$Bi=$L[$hl]["pos"][0]+$Dd[$hl][$ej[1][$v]];$Hg=min($Hg,$Ai,$Bi);$ng=max($ng,$Ai,$Bi);}echo"<div class='references' id='refl$Mf' style='left: $Mf"."em; top: $Hg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($ng-$Hg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(171),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$i){$h=["auto_increment"=>''];foreach(["type","routine","event","trigger"]as$Lk){if(support($Lk))$h[$Lk."s"]='';}save_settings(array_intersect_key($_POST+$h,array_flip(["output","format","db_style","schema_style","table_style","data_style"])+$h),"adminer_export");$sa=(DB==""||$_GET["ns"]==="");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$ud=dump_headers((count($T)==1?key($T):DB),($sa||count($T)>1));$sf=preg_match('~sql~',$_POST["format"]);if($sf){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Gk=$_POST["db_style"];$f=[DB];if(DB==""){$f=$_POST["databases"];if(is_string($f))$f=explode("\n",rtrim(str_replace("\r","",$f),"\n"));}foreach((array)$f
as$g){adminer()->dumpDatabase($g);if(connection()->select_db($g)){if($sf&&$Gk)echo
use_sql($g,$Gk).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?[""]:adminer()->schemas()))as$L){if($L!=""){if(DB==""&&information_schema(DB,$L))continue;set_schema($L);}if($sf&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$Ck=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):[]);$td=[];$ac=[];foreach($Ck
as$A=>$S){if($sa||in_array($A,(array)$_POST["tables"]))$td[$A]=$S;if($sa||in_array($A,(array)$_POST["data"]))$ac[$A]=$S;}if($sf){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($td);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Il=[];foreach($ac
as$A=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($td[$A])))$Il[]=$A;}echo
truncate_all_sql($Il);}$Uh="";if($_POST["types"]){foreach(types()as$r=>$U){$mc=type_definition($r);$lh=($mc["kind"]=='d'?"DOMAIN":"TYPE");if($mc["definition"])$Uh
.=($Gk!='DROP+CREATE'?"DROP $lh IF EXISTS ".table($U).";;\n":"")."CREATE $lh ".table($U)." $mc[definition];\n\n";else$Uh
.="-- Could not export type ".preg_replace('~[\r\n]+~',' ',$U)."\n\n";}}if($_POST["routines"]){foreach(routines()as$J){$A=$J["ROUTINE_NAME"];$zj=$J["ROUTINE_TYPE"];$Ze=routine($J["SPECIFIC_NAME"],$zj);if($Ze["definition"]!=""){$Ob=create_routine($zj,["name"=>$A]+$Ze);set_utf8mb4($Ob);$Uh
.=($Gk!='DROP+CREATE'?"DROP $zj IF EXISTS ".table($A).";;\n":"")."$Ob;\n\n";}else$Uh
.="-- Could not export ".strtolower($zj)." ".preg_replace('~[\r\n]+~',' ',$A)."\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$J){$Ob=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($J["Name"]),3));set_utf8mb4($Ob);$Uh
.=($Gk!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($J["Name"]).";;\n":"")."$Ob;;\n\n";}}echo($Uh&&JUSH=='sql'?"DELIMITER ;;\n\n$Uh"."DELIMITER ;\n\n":$Uh);}if($_POST["table_style"]||$_POST["data_style"]){$um=[];foreach($Ck
as$A=>$S){$R=array_key_exists($A,$td);$Yb=array_key_exists($A,$ac);if($R||$Yb){$vl=null;if($ud=="tar"){$vl=new
TmpFile;ob_start([$vl,'write'],1e5);}adminer()->dumpTable($A,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$um[]=$A;elseif($Yb){$k=fields($A);$M=["*"];$Kb=convert_fields($k,$k);if($Kb)$M[]=substr($Kb,2);adminer()->dumpData($A,$_POST["data_style"],"",$M);}if($sf&&$_POST["triggers"]&&$R&&($Hl=trigger_sql($A)))echo"\nDELIMITER ;;\n$Hl\nDELIMITER ;\n";if($ud=="tar"){ob_end_flush();tar_file((DB!=""?"":"$g/")."$A.csv",$vl);}elseif($sf)echo"\n";}}if($sf&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($td
as$A=>$S){if(!is_view($S))echo
foreign_keys_sql($A);}}if($sf){foreach($um
as$tm)adminer()->dumpTable($tm,$_POST["table_style"],1);}if($ud=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(81),$i,($_GET["export"]!=""?["table"=>$_GET["export"]]:[]),h(DB));echo'
<form method="post">
<table class="layout">
';$ec=['','USE','DROP+CREATE','CREATE'];$Ij=(JUSH=="mssql"?['','DROP+CREATE','CREATE']:$ec);$Zk=['','DROP+CREATE','CREATE'];$Zb=['','TRUNCATE+INSERT','INSERT'];if(JUSH=="sql")$Zb[]='INSERT+UPDATE';$J=get_settings("adminer_export");if(!$J)$J=["output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT"];echo"<tr><th>".lang(172)."<td>".html_radios("output",adminer()->dumpOutput(),$J["output"])."\n","<tr><th>".lang(173)."<td>".html_radios("format",adminer()->dumpFormat(),$J["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(17)."<td>".html_select('db_style',$ec,$J["db_style"]).(support("type")?checkbox("types",1,$J["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$J["routines"],lang(77)):"").(support("event")?checkbox("events",1,$J["events"],lang(79)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(16)."<td>".html_select('schema_style',$Ij,$J["schema_style"]):""),"<tr><th>".lang(148)."<td>".html_select('table_style',$Zk,$J["table_style"]).checkbox("auto_increment",1,$J["auto_increment"],lang(57)).(support("trigger")?checkbox("triggers",1,$J["triggers"],lang(166)):""),"<tr><th>".lang(174)."<td>".html_select('data_style',$Zb,$J["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(81),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Ii=[];if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(175)."'".on('click','formCheck','^schemas\[').">".lang(16)."</label>","<tbody>\n";foreach(adminer()->schemas()as$L){if(!information_schema(DB,$L))echo"<tr><td>".checkbox("schemas[]",$L,true,$L,"","block")."\n";}}elseif(DB!=""){$jb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$jb class='jsonly' title='".lang(175)."'".on('click','formCheck','^tables\[').">".lang(156)."</label>","<th style='text-align: right;'><label class='block'>".lang(174)."<input type='checkbox' id='check-data'$jb class='jsonly' title='".lang(175)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$um="";$cl=tables_list();foreach($cl
as$A=>$U){$Hi=preg_replace('~_.*~','',$A);$jb=($a==""||$a==(substr($a,-1)=="%"?"$Hi%":$A));$Mi="<tr><td>".checkbox("tables[]",$A,$jb,$A,"","block");if($U!==null&&!preg_match('~table~i',$U))$um
.="$Mi\n";else
echo"$Mi<td align='right'><label class='block'><span id='Rows-".h($A)."'></span>".checkbox("data[]",$A,$jb)."</label>\n";$Ii[$Hi]++;}echo$um;if($cl)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$f=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($f?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(175)."'".on('click','formCheck','^databases\[').">":"").lang(17)."</label>","<tbody>\n";if($f){foreach($f
as$g){if(!information_schema($g)){$Hi=preg_replace('~_.*~','',$g);echo"<tr><td>".checkbox("databases[]",$g,$a==""||$a=="$Hi%",$g,"","block")."\n";$Ii[$Hi]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Md=true;foreach($Ii
as$v=>$X){if($v!=""&&$X>1){echo($Md?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$v%")."'>".h($v)."</a>";$Md=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(76));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(176)."</a>";$H=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$ge=$H;if(!$H)$H=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($ge?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(40)."<th>".lang(38)."<td class='hover'><tbody>\n";while($J=$H->fetch_assoc())echo'<tr><td>'.h($J["User"]),"<td>".h($J["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($J["User"]).'&host='.url_escape($J["Host"])).'">'.lang(20)."</a>\n";if(!$ge||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(20)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$i&&$_POST["export"]){save_settings(["output"=>$_POST["output"],"format"=>$_POST["format"]],"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$i&&$_POST["val"]){$oa=0;$Hk=true;$cb=[];$Fj=0;foreach($_POST["val"]as$K)$Fj+=count($K);$Qa=$Fj>1&&driver()->begin();foreach($_POST["val"]as$Rk=>$K){$R=bracket_escape($Rk,true);$k=fields($R);$Sk=indexes($R);foreach($K
as$s=>$J){parse_str(bracket_escape($s,true),$Z);$Ql=[];foreach($Z["where"]as$v=>$X)$Ql[bracket_escape($v,true)]=$X;if(!$k||$Z["null"]||array_diff_key($Ql,$k)||!unique_array($Ql,$Sk)){$Hk=false;break
2;}$O=[];$M=[];foreach($J
as$Af=>$X){$v=bracket_escape($Af,true);$j=idx($k,$v);if(!$j){$Hk=false;break
3;}$O[idf_escape($v)]=(preg_match('~char|text~',$j["type"])||$X!=""?adminer()->processInput($j,$X):"NULL");$M[$Af]=$v;}$Yi=where($Z,$k);if(!driver()->update($R,$O," WHERE $Yi",0," ")){$Hk=false;break
2;}$oa+=connection()->affected_rows;$d=[];foreach($M
as$v)$d[]=idf_escape($v);$Zl=driver()->select($R,$d,[$Yi],$d);$ch=($Zl?$Zl->fetch_row():[]);$wf=0;foreach($M
as$Af=>$v){$j=$k[$v];$Fk=['type'=>(preg_match('~binary~',$j["type"])?'blob':$j["type"])];$cb["val[$Rk][$s][$Af]"]=select_value(idx($ch,$wf++),"",$Fk,null);}}}if($Qa&&$Hk)$Hk=driver()->commit();queries_redirect(null,lang(177,$oa),$Hk);if($Qa&&!$Hk)driver()->rollback();page_headers();page_messages($i);foreach($cb
as$A=>$X)echo"<div data-name='".h($A)."' hidden>$X</div>\n";exit;}restart_session();$Fe=&get_session("queries");$Ee=&$Fe[DB];if(!$i&&$_POST["clear"]){$Ee=[];redirect(remove_from_uri("history"));}stop_session();$ma=get_settings("adminer_import");if($_POST&&$ma)save_settings($ma,"adminer_import");page_header((isset($_GET["import"])?lang(80):lang(69)),$i);$Wf=driver()->lineComment();$fd=0;function
query_error_line($ll,$rh,$G,$Vf){return
substr_count(substr($ll,0,$rh),"\n")+($Vf?:substr_count(substr($G,0,strspn($G," \t\r\n")),"\n")+1);}if(!$i&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$oc=driver()->delimiter;$n=false;if(!isset($_GET["import"]))$G=$_POST["query"];elseif($_POST["webfile"]){$uk=adminer()->importServerPath();$n=@fopen((file_exists($uk)?$uk:"compress.zlib://$uk.gz"),"rb");$G=($n?fread($n,1e6):false);}else$G=get_file("sql_file",true,$oc);if(is_string($G)){if(($vg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($vg,strval(2*strlen($G)+memory_get_usage()+8e6)));if($G!=""&&strlen($G)<1e6){$Ti=$G.(preg_match("~$oc\\s*\$~",$G)?"":$oc);if(!$Ee||first(end($Ee))!=$Ti){restart_session();$Ee[]=[$Ti,time()];set_session("queries",$Fe);stop_session();}}$sk="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Wf)[^\n]*\n?|--\r?\n)";$rh=0;$Xi=0;$Wc=true;$Mb=false;$Bg=false;$Db=connect();if($Db&&DB!=""){$Db->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$Db);}$xb=0;$gd=[];$ci='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Wf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$zl=microtime(true);while($G!=""){if(!$rh&&preg_match("~^$sk*+DELIMITER\\s+(\\S+)~i",$G,$_)){$oc=preg_quote($_[1]);$G=substr($G,strlen($_[0]));$Xi+=strlen($_[0]);}elseif(!$rh&&JUSH=='pgsql'&&preg_match("~^($sk*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$G,$_)){$oc="\n\\\\\\.\r?\n";$Mb=true;$rh=strlen($_[0]);}elseif(!$rh&&JUSH=='pgsql'&&preg_match("~^$sk*+\\\\connect[ \t][^\n]*~",$G,$_)){$oc="\n";$Bg=true;$rh=strlen($_[0]);}else{preg_match("($oc\\s*|$ci)",$G,$_,PREG_OFFSET_CAPTURE,$rh);list($Vd,$F)=$_[0];if(!$Vd&&$n&&!feof($n))$G
.=fread($n,1e5);else{if(!$Vd&&rtrim($G)=="")break;$rh=$F+strlen($Vd);if($Vd&&!preg_match("(^$oc)",$Vd)){$Ya=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($F>0&&strtolower($G[$F-1])=="e"));$ri=($Vd=='/*'?'\*/':($Vd=='['?']':(preg_match("~^(?:$Wf)~",$Vd)?"\n":preg_quote($Vd).($Ya?'|\\\\.':''))));while(preg_match("($ri|\$)s",$G,$_,PREG_OFFSET_CAPTURE,$rh)){$Gj=$_[0][0];if(!$Gj&&$n&&!feof($n))$G
.=fread($n,1e5);else{$rh=$_[0][1]+strlen($Gj);if(!$Gj||$Gj[0]!="\\")break;}}}else{$Ti=substr($G,0,$F+($Mb?3:0));$G=substr($G,$rh);$Ui=$Xi;$Xi+=$rh;$rh=0;if($Mb||$Bg){$oc=driver()->delimiter;$Mb=false;$Bg=false;}$qb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Ti)."</code>";if(preg_match("~^$sk*+\$~",$Ti)&&!preg_match('~/\*M?!~',$Ti)){echo($_POST["only_errors"]?"":"<pre>$qb</pre>\n");continue;}$Wc=false;$xb++;$Mi="<pre id='sql-$xb'>$qb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$sk*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$Ti,$_)!==0){echo$Mi,"<p class='error'>".lang(178,preg_match('~ATTACH~i',$_[1])?'ATTACH':'VACUUM INTO')."\n";$gd[]=" <a href='#sql-$xb'>$xb</a>";if(!$fd&&!isset($_GET["import"]))$fd=query_error_line($_POST["query"],$Ui,$Ti,0);if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Mi;ob_flush();flush();}$zk=microtime(true);if(JUSH=="pgsql"&&preg_match("~^$sk*+\\\\connect\\s+(\"(?:[^\"]|\"\")+\"|[^\\s\";]+)\\s*;*\\s*\$~",$Ti,$_)){$g=idf_unescape($_[1]);if(connection()->select_db($g)){if($Db)$Db->select_db($g);echo($_POST["only_errors"]?"":"<p class='message'>".lang(17).": ".h($g)." <span class='time'>(".format_time($zk).")</span>\n");continue;}}elseif(connection()->multi_query($Ti)&&$Db&&preg_match("~^$sk*+USE\\b~i",$Ti))$Db->query($Ti);do{$H=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Mi:""),"<p class='error'>".lang(179).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$gd[]=" <a href='#sql-$xb'>$xb</a>";if(!$fd&&!isset($_GET["import"]))$fd=query_error_line($_POST["query"],$Ui,$Ti,driver()->errorLine($Ti));if($_POST["error_stops"])break
2;}else{$y=ME."sql=".url_escape(trim($Ti));$ol=" <span class='time'>(".format_time($zk).")</span>".(strlen($y)<adminer()->sqlUrlLength()-100?" <a href='".h($y)."'>".lang(20)."</a>":"");$oa=connection()->affected_rows;$xm=($_POST["only_errors"]?"":driver()->warnings());$ym="warnings-$xb";if($xm)$ol
.=", <a href='#$ym' class='toggle'>".lang(52)."</a>";$rd="";$sd="explain-$xb";if(is_object($H)){$x=$_POST["limit"];$jh=$x;$Nc=!$_POST["only_errors"];if($Nc)echo"<form method='post'>\n";$Mh=print_select_result($H,$Db,[],$jh,$Nc);if(!$_POST["only_errors"]){$jh=max($H->num_rows,$jh);echo"<p class='sql-footer'>".($jh?($x&&$jh>$x?lang(180,$x):"").lang(181,$jh):""),$ol;if($Db&&preg_match("~^($sk|\\()*+SELECT\\b~i",$Ti)&&($rd=adminer()->explain($Db,$Ti,$Mh))!="")echo", <a href='#$sd' class='toggle'>Explain</a>";if($Nc)echo", <input type='submit' name='save' value='".lang(24)."' class='jsonly' disabled"." title='".lang(182)."'".on('click','sqlSave',lang(27)).">";$r="export-$xb";echo", <a href='#$r' class='toggle'>".lang(81)."</a><span id='$r' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",adminer()->dumpFormat(),$ma["format"]).input_hidden("query",$Ti)."<input type='submit' name='export' value='".lang(81)."'".($x?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$sk*+(CREATE|DROP|ALTER)$sk++(DATABASE|SCHEMA)\\b~i",$Ti)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(183,$oa)."$ol\n";}echo($xm?"<div id='$ym' class='hidden'>\n$xm</div>\n":""),($rd!=""?"<div id='$sd' class='hidden explain'>\n$rd</div>\n":"");}$zk=microtime(true);}while(connection()->next_result());}}}}}if($Wc)echo"<p class='message'>".lang(184)."\n";else{$Te=connection()->inTransaction();driver()->rollback();if($Te)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(185,$xb-count($gd))," <span class='time'>(".format_time($zl).")</span>\n";elseif($gd&&$xb>1)echo"<p class='error'>".lang(179).": ".implode("",$gd)."\n";}}else
echo"<p class='error'>".upload_error($G)."\n";}echo'
<form method="post" enctype="multipart/form-data" id="form"';$am="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"),adminer()->sqlUrlLength());else
echo
on_upload_progress($am);echo'>
';$od="<input type='submit' value='".lang(186)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Ti=$_GET["sql"];if($_POST)$Ti=$_POST["query"];elseif($_GET["history"]=="all")$Ti=$Ee;elseif($_GET["history"]!="")$Ti=idx($Ee[$_GET["history"]],0);echo"<p>";textarea("query",$Ti,20,80,JUSH,$fd);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$od\n",lang(187).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$oe=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(188)."</legend><div>",($am?input_hidden(ini_get("session.upload_progress.name"),$am):""),"SQL$oe: ".file_input(" name='sql_file[]' multiple","\n$od"),($am?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Qe=adminer()->importServerPath();if($Qe)echo"<fieldset><legend>".lang(189)."</legend><div>",lang(190,"<code>".h($Qe)."$oe</code>")," <input type='submit' name='webfile' value='".lang(191)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(192))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(193))."\n",input_token();if(!isset($_GET["import"])&&$Ee){print_fieldset("history",lang(194),$_GET["history"]!="");for($X=end($Ee);$X;$X=prev($Ee)){$v=key($Ee);list($Ti,$ol,$Sc)=$X;echo'<div><a href="'.h(ME."sql=&history=$v").'" class="hover">'.lang(20)."</a>"." <span class='time' title='".@date('Y-m-d',$ol)."'>".@date("H:i:s",$ol)."</span>"." <code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Wf).*~m",'',$Ti))),80,"</code>").($Sc?" <span class='time'>($Sc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(195)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(196)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$k=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$k):""):where($_GET,$k));$Xl=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($k
as$A=>$j){if((!$Xl&&!isset($j["privileges"]["insert"]))||adminer()->fieldName($j)=="")unset($k[$A]);}if($_POST&&!$i&&!isset($_GET["select"])){$z=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$z=($Xl?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$z))$z=ME."select=".url_escape($a);$u=indexes($a);$Rl=unique_array($_GET["where"],$u);$Yi="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($z,lang(197),driver()->delete($a,$Yi,$Rl?0:1));else{$O=[];foreach($k
as$A=>$j){$X=process_input($j);if($X!==false&&$X!==null)$O[idf_escape($A)]=$X;}if($Xl){if(!$O)redirect($z);queries_redirect($z,lang(198),driver()->update($a,$O,$Yi,$Rl?0:1));if(is_ajax()){page_headers();page_messages($i);exit;}}else{$H=driver()->insert($a,$O);$Lf=($H?last_id($H):0);queries_redirect($z,lang(199,($Lf?" $Lf":"")),$H);}}}$J=null;$G="";$ol="";if($Z){$M=[];$Sj=["*"];foreach($k
as$A=>$j){if(isset($j["privileges"]["select"])){$Ba=($_POST["clone"]&&$j["auto_increment"]?"''":convert_field($j));$c=($Ba?"$Ba AS ":"").idf_escape($A);$M[]=$c;if($Ba)$Sj[]=$c;}}$J=[];if(!support("table")){$M=["*"];$Sj=$M;}if($M){$zk=microtime(true);$H=driver()->select($a,$M,[$Z],$M,[],(isset($_GET["select"])?2:1));$G=str_replace("SELECT ".implode(", ",$M),"SELECT ".implode(", ",$Sj),driver()->query);$ol=format_time($zk);if(!$H)$i=adminer()->error();else{$J=$H->fetch_assoc();if(!$J)$J=false;}if(isset($_GET["select"])&&(!$J||$H->fetch_assoc()))$J=null;}}if(!$k&&driver()->primary!=""){if(!$Z){$H=driver()->select($a,["*"],[],["*"]);$J=($H?$H->fetch_assoc():false);if(!$J)$J=[driver()->primary=>""];}if($J){foreach($J
as$v=>$X){if(!$Z)$J[$v]=null;$k[$v]=["field"=>$v,"null"=>($v!=driver()->primary),"auto_increment"=>($v==driver()->primary)];}}}if($_POST["save"]){$Ci=[];foreach((array)$_POST["fields"]as$v=>$X)$Ci[bracket_escape($v,true)]=$X;$J=$Ci+($J?$J:[]);}edit_form($a,$k,$J,$Xl,$i,$G,$ol);}elseif(isset($_GET["create"])){function
referencable_primary($Uj){$I=[];foreach(table_status('',true)as$Uk=>$R){if($Uk!=$Uj&&!$R["dependent"]&&fk_support($R)){foreach(fields($Uk)as$j){if($j["primary"]){if($I[$Uk]){unset($I[$Uk]);break;}$I[$Uk]=$j;}}}}return$I;}$a=$_GET["create"];$gi=driver()->partitionBy;$ki=($gi&&$a!=""?driver()->partitionsInfo($a):[]);$gj=referencable_primary($a);$Td=[];foreach($gj
as$Uk=>$j)$Td[str_replace("`","``",$Uk)."`".str_replace("`","``",$j["field"])]=$Uk;$Ph=[];$S=[];$hh=false;if($a!=""){$Ph=fields($a);$S=table_status1($a);$hh=(count($S)<2);}$wa=($a==""||driver()->supportsAlterTable($S));$J=$_POST;$J["fields"]=(array)$J["fields"];if($J["auto_increment_col"])$J["fields"][$J["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$i)save_settings(["comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]]);if($_POST&&!process_fields($J["fields"])&&!$i){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(200),drop_tables([$a]));else{$k=[];$ta=[];$em=false;$Rd=[];$Oh=reset($Ph);$qa=" FIRST";foreach($J["fields"]as$j){$m=$Td[$j["type"]];$Kl=($m!==null?$gj[$m]:$j);if($j["field"]!=""){if(!$j["generated"])$j["default"]=null;$j["default_sql"]=($j["generated"]=="DEFAULT SQL");$Ri=process_field($j,$Kl);$ta[]=[$j["orig"],$Ri,$qa];if(!$Oh||$Ri!==process_field($Oh,$Oh)){$k[]=[$j["orig"],$Ri,$qa];if($j["orig"]!=""||$qa||(JUSH=="sqlite"&&$j["comment"]!=""))$em=true;}if($m!==null)$Rd[idf_escape($j["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(['table'=>$Td[$j["type"]],'source'=>[$j["field"]],'target'=>[$Kl["field"]],'on_delete'=>$j["on_delete"],],object_name("FOREIGN",trim($J["name"]),[$j["field"]]));$qa=" AFTER ".idf_escape($j["field"]);}elseif($j["orig"]!=""){$em=true;$k[]=[$j["orig"]];}if($j["orig"]!=""){$Oh=next($Ph);if(!$Oh)$qa="";}}$ii=[];if(in_array($J["partition_by"],$gi)){foreach($J
as$v=>$X){if(preg_match('~^partition~',$v))$ii[$v]=$X;}foreach($ii["partition_names"]as$v=>$A){if($A==""){unset($ii["partition_names"][$v]);unset($ii["partition_values"][$v]);}}$ii["partition_names"]=array_values($ii["partition_names"]);$ii["partition_values"]=array_values($ii["partition_values"]);if($ii==$ki)$ii=[];}elseif(preg_match("~partitioned~",$S["Create_options"]))$ii=null;$xg=lang(201);if($a==""){cookie("adminer_engine",$J["Engine"]);$xg=lang(202);}$A=trim($J["name"]);$z=ME.(support("table")?"table=":"select=").url_escape($A);$yb=($J["Comment"]!=$S["Comment"]?$J["Comment"]:null);$Zc=($J["Engine"]&&$J["Engine"]!=$S["Engine"]?$J["Engine"]:"");$H=alter_table($a,$A,(JUSH=="sqlite"&&($em||$Rd||$yb!==null||$Zc)?$ta:$k),$Rd,$yb,$Zc,($J["Collation"]&&$J["Collation"]!=$S["Collation"]?$J["Collation"]:""),($J["Auto_increment"]!=""?number($J["Auto_increment"]):""),$ii);if($H&&!Queries::$queries&&$a!=""&&!$k&&!$Rd)redirect($z);queries_redirect($z,$xg,$H);}}$Ak=($a!=""?"alter":"create");page_header(($a!=""?lang(50):lang(82)),$i,["table"=>$a],h($a),$hh,doc_link(['sql'=>"$Ak-table.html",'mariadb'=>($a!=""?"$Ak-table":""),]));if(!$_POST){$Nl=driver()->types();$J=["Engine"=>$_COOKIE["adminer_engine"],"fields"=>[["field"=>"","type"=>(isset($Nl["int"])?"int":(isset($Nl["integer"])?"integer":"")),"on_update"=>""]],"partition_names"=>[""],];if($a!=""){$J=$S;$J["name"]=$a;$J["fields"]=[];if(!$_GET["auto_increment"])$J["Auto_increment"]="";foreach($Ph
as$j){if($j["generated"])$j["default"]=ltrim($j["default"]);$j["generated"]=$j["generated"]?:(isset($j["default"])?($j["default_sql"]?"DEFAULT SQL":"DEFAULT"):"");$J["fields"][]=$j;}if($gi){$J+=$ki;$J["partition_names"][]="";$J["partition_values"][]="";}}}$ub=flat_collations();$ad=driver()->engines();foreach($ad
as$Zc){if(!strcasecmp($Zc,$J["Engine"])){$J["Engine"]=$Zc;break;}}$ig=max_input_vars(12,20);if($ig){$Be=(count($J["fields"])>$ig?"":" hidden");echo"<p".($Be?" id='max-fields' data-columns='$ig'":"")." class='error$Be'>".max_input_vars_error()."\n";}echo'
<form method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(203).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($J["name"])."' autocapitalize='off'>\n",(!$wa?h($S["Engine"])."\n":($ad?html_select("Engine",[""=>"(".lang(204).")"]+$ad,$J["Engine"],on('change','helpClose').on_help_value())."\n":""));if($ub)echo"<datalist id='collations'>".optionlist($ub)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($J["Collation"])."' placeholder='(".lang(127).")'>\n");echo"<input type='submit' value='".lang(24)."'>\n";}if(support("columns")&&$wa){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($J["fields"],$ub,"TABLE",$Td);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(57).": <input type='number' name='Auto_increment' class='size' value='".h($J["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(205),on('click','columnShowClick',6),"jsonly");$_b=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$_b,lang(56),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($_b?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$J["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$a)),'>
';if($gi&&(JUSH=='sql'||$a=="")){$hi=preg_match('~RANGE|LIST~',$J["partition_by"]);print_fieldset("partition",lang(207),$J["partition_by"]);echo"<p>".html_select("partition_by",array_merge([""],$gi),$J["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($J["partition"])."'>)\n",lang(208).": <input type='number' name='partitions' class='size".($hi||!$J["partition_by"]?" hidden":"")."' value='".h($J["partitions"])."'>\n","<table id='partition-table'".($hi?"":" class='hidden'").">\n","<thead><tr><th>".lang(209)."<th>".lang(210)."<tbody>\n";foreach($J["partition_names"]as$v=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($v==count($J["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($J["partition_values"],$v)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Ye=["PRIMARY","UNIQUE","INDEX"];$S=table_status1($a,true);$We=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Ye[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Ye[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Ye[]="VECTOR";$u=indexes($a);$k=fields($a);$Li=[];if(JUSH=="mongo"){$Li=$u["_id_"];unset($Ye[0]);unset($u["_id_"]);}$J=$_POST;if($J)save_settings(["index_options"=>$J["options"]]);if($_POST&&!$i&&!$_POST["add"]&&!$_POST["drop_col"]){$va=[];foreach($J["indexes"]as$t){$A=$t["name"];if(in_array($t["type"],$Ye)){$d=[];$Sf=[];$rc=[];$Bh=[];$Xe=(support("partial_indexes")?$t["partial"]:"");$Ve=(in_array($t["algorithm"],$We)?$t["algorithm"]:"");$O=[];ksort($t["columns"]);foreach($t["columns"]as$v=>$c){if($c!=""){$w=idx($t["lengths"],$v);$pc=idx($t["descs"],$v);$Ah=idx($t["opclasses"],$v);$O[]=($k[$c]?idf_escape($c):$c).($w?"(".(+$w).")":"").($Ah!=""?" ".idf_escape($Ah):"").($pc?" DESC":"");$d[]=$c;$Sf[]=($w?:null);$rc[]=$pc;$Bh[]="$Ah";}}$pd=$u[$A];if($pd){ksort($pd["columns"]);ksort($pd["lengths"]);ksort($pd["descs"]);if($t["type"]==$pd["type"]&&array_values($pd["columns"])===$d&&(!$pd["lengths"]||array_values($pd["lengths"])===$Sf)&&array_values($pd["descs"])===$rc&&(!$pd["opclasses"]||array_values($pd["opclasses"])===$Bh)&&$pd["partial"]==$Xe&&(!$We||$pd["algorithm"]==$Ve)&&!$pd["invalid"]){unset($u[$A]);continue;}}if($d)$va[]=[$t["type"],$A,$O,$Ve,$Xe];}}foreach($u
as$A=>$pd)$va[]=[$pd["type"],$A,"DROP"];if(!$va)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(211),alter_indexes($a,$va));}page_header(lang(158),$i,["table"=>$a],h($a),false,doc_link(['sql'=>"create-index.html",]));$Fd=array_keys($k);if($_POST["add"]){foreach($J["indexes"]as$v=>$t){if($t["columns"][count($t["columns"])]!="")$J["indexes"][$v]["columns"][]="";}$t=end($J["indexes"]);if($t["type"]||array_filter($t["columns"],'strlen'))$J["indexes"][]=["columns"=>[1=>""]];}if(!$J){foreach($u
as$v=>$t){$u[$v]["name"]=$v;$u[$v]["columns"][]="";}$u[]=["columns"=>[1=>""]];$J["indexes"]=$u;}$Sf=(JUSH=="sql"||JUSH=="mssql");$Bh=driver()->indexOpclasses();$ik=($_POST?$_POST["options"]:get_setting("index_options"));$Ug=[];foreach($Ye
as$U)$Ug[$U]=str_replace("{table}",$a,adminer()->namePattern($U));echo'
<form method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(212);$Oe=" class='idxopts".($ik?"":" hidden")."'";if($We)echo"<th id='label-algorithm'$Oe>".lang(213).doc_link(['sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',]);echo'<th><input type="submit" hidden>',lang(214).($Sf?"<span$Oe> (".lang(215).")</span>":"");if($Sf||support("descidx"))echo
checkbox("options",1,$ik,lang(133),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(216);if(support("partial_indexes"))echo"<th id='label-condition'$Oe>".lang(217);echo'<td><noscript>',icon("plus","add[0]","+",lang(134)),'</noscript>
<tbody>
';if($Li){echo"<tr><td>PRIMARY<td>";foreach($Li["columns"]as$v=>$c)echo
select_input(" disabled",array_combine($Fd,$Fd),$c),"<label><input disabled type='checkbox'>".lang(64)."</label> ";echo"<td><td>\n";}$wf=1;foreach($J["indexes"]as$t){if(!$_POST["drop_col"]||$wf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$wf][type]",[-1=>""]+$Ye,$t["type"],on('change','indexesChangeType',$Ug),"label-type");if($We)echo"<td$Oe>".html_select("indexes[$wf][algorithm]",array_merge([""],$We),$t['algorithm'],"","label-algorithm");echo"<td>";ksort($t["columns"]);$q=1;foreach($t["columns"]as$v=>$c){echo"<span>".select_input(" name='indexes[$wf][columns][$q]' title='".lang(54)."'".on('change','indexesChangeColumn',$Ug),($k&&($c==""||$k[$c])?array_combine($Fd,$Fd):[]),$c)," <span$Oe>",($Sf?"<input type='number' name='indexes[$wf][lengths][$q]' class='size' value='".h(idx($t["lengths"],$v))."' title='".lang(132)."'>":"");if($Bh){$Ah=idx($t["opclasses"],$v);echo
html_select("indexes[$wf][opclasses][$q]",[""=>"(".lang(218).")"]+array_combine($Bh,$Bh)+($Ah!=""?[$Ah=>$Ah]:[]),$Ah),'';}echo(support("descidx")?checkbox("indexes[$wf][descs][$q]",1,idx($t["descs"],$v),lang(64)):""),"<br>","</span></span>";$q++;}echo"<td><input name='indexes[$wf][name]' value='".h($t["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Oe><input name='indexes[$wf][partial]' value='".h($t["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$wf]","x",lang(136),on('click','editingRemoveRow','indexes$1[type]')),(idx($u[$t["name"]],"invalid")?" <span class='error'>INVALID</span>":""),"\n";}$wf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if(support("concurrent_indexes"))echo
checkbox("concurrently",1,$_POST["concurrently"],"concurrently").''."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["database"])){$J=$_POST;if($_POST&&!$i&&!$_POST["add"]){$A=trim($J["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(219),drop_databases([DB]));}elseif($A!==DB){if(DB!=""){$_GET["db"]=$A;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($A),lang(220),rename_database($A,(string)$J["collation"]));}else{$f=explode("\n",str_replace("\r","",$A));$Hk=true;$Jf="";foreach($f
as$g){if(count($f)==1||$g!=""){if(!create_database($g,(string)$J["collation"]))$Hk=false;$Jf=$g;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($Jf),lang(221),$Hk);}}else{if(!$J["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($A).(preg_match('~^[a-z0-9_]+$~i',$J["collation"])?" COLLATE $J[collation]":""),substr(ME,0,-1),lang(222));}}$Ak=(DB!=""?"alter":"create");page_header(DB!=""?lang(72):lang(140),$i,[],h(DB),false,doc_link(['sql'=>"$Ak-database.html",'mariadb'=>(DB!=""?"":"$Ak-database"),]));$ub=collations();$A=DB;if($_POST)$A=$J["name"];elseif(DB!="")$J["collation"]=db_collation(DB,$ub);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$ge){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$ge,$_)&&$_[1]){$A=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form method="post">
<p>
',($_POST["add"]||strpos($A,"\n")?"<textarea autofocus name='name' rows='10' cols='40'>\n".h($A).'</textarea><br>':'<input name="name" autofocus value="'.h($A).'" data-maxlength="64" autocapitalize="off">')."\n",($ub?"<datalist id='collations'>".optionlist(flat_collations())."</datalist>"."<input list='collations' name='collation' value='".h($J["collation"])."' placeholder='(".lang(127).")'>".doc_link(['sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",]):"")."\n",'<input type=\'submit\' value=\'',lang(24),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(152)."'".confirm(lang(206,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(134))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$Dj=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$zj=routine($_GET["call"],$Dj);$tf=(idx($zj["returns"],"type")=="trigger");page_header(($tf?lang(166):lang(223)).": ".h($ca),$i,"#routines","",!$zj,(isset($_GET["callf"])?"":doc_link(['sql'=>"call.html",])));$Re=[];$Uh=[];foreach($zj["fields"]as$q=>$j){if(substr($j["inout"],-3)=="OUT"&&JUSH=='sql')$Uh[$q]="@".idf_escape($j["field"])." AS ".idf_escape($j["field"]);if(!$j["inout"]||preg_match('~^(IN|OUTPUT)~',$j["inout"]))$Re[]=$q;}if(!$i&&$_POST){$Za=[];foreach($zj["fields"]as$v=>$j){$X="";if(in_array($v,$Re)){$X=process_input($j);if($X===false)$X="''";if(isset($Uh[$v]))connection()->query("SET @".idf_escape($j["field"])." = $X");}if(isset($Uh[$v]))$Za[]="@".idf_escape($j["field"]);elseif(in_array($v,$Re))$Za[]=$X;}$_a=implode(", ",$Za);$G=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($zj["returns"],"type")=="record"?"* FROM ":"").table($ca)."($_a)":"EXEC ".table($ca).($_a!=""?" $_a":""));$zk=microtime(true);$H=connection()->multi_query($G);$oa=connection()->affected_rows;echo
adminer()->selectQuery($G,$zk,!$H);if(!$H)echo"<p class='error'>".adminer()->error()."\n";else{$Db=connect();if($Db)$Db->select_db(DB);do{$H=connection()->store_result();if(is_object($H))print_select_result($H,$Db);else
echo"<p class='message'>".lang(224,$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Uh)print_select_result(connection()->query("SELECT ".implode(", ",$Uh)));}}if($tf){$Hl=driver()->routineTriggers($_GET["call"]);if($Hl){echo"<table>\n";foreach($Hl
as$J){$y=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($J["ns"]),ME);echo"<tr><th>".h($J["trigger"])."<td><a href='".h($y."table=".url_escape($J["table"])."#triggers")."'>".($J["ns"]!=$_GET["ns"]?"<b>".h($J["ns"])."</b>.":"").h($J["table"])."</a>"."<td class='hover'><a href='".h($y."trigger=".url_escape($J["table"])."&name=".url_escape($J["trigger"]))."'>".lang(162)."</a>\n";}echo"</table>\n";}}else{echo'<form method="post">
';if($Re){echo"<table class='layout'>\n";foreach($Re
as$v){$j=$zj["fields"][$v];$A=$j["field"];echo"<tr><th>".adminer()->fieldName($j);$Y=idx($_POST["fields"],$A);if($Y!=""){if($j["type"]=="set")$Y=implode(",",$Y);}input($j,$Y,idx($_POST["function"],$A,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(223),'\'>
',input_token(),'</form>
';}echo'
',adminer()->commentValue($Dj,$zj['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$A=$_GET["name"];$J=$_POST;if($_POST&&!$i&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$J["source"]=array_filter($J["source"],'strlen');ksort($J["source"]);$fl=[];foreach($J["source"]as$v=>$X)$fl[$v]=$J["target"][$v];$J["target"]=$fl;}$Qa=false;if(JUSH=="sqlite")$H=recreate_table($a,$a,[],[],[" $A"=>($J["drop"]?"":" ".format_foreign_key($J))]);else{$Eb=$J["constraint"];$va="ALTER TABLE ".table($a);$Ic="$va DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A?:'');if($J["drop"])$H=queries($Ic);else{$ja=" ADD".format_foreign_key($J,$Eb);if($A=="")$H=queries($va.$ja);elseif(JUSH=="pgsql"||(JUSH=="sql"&&$Eb!=$A))$H=queries("$Ic,$ja");else{$Qa=support("transaction_ddl")&&driver()->begin();$H=($Qa||idx(foreign_keys($a),$A)?queries($Ic):true)&&queries($va.$ja)&&(!$Qa||driver()->commit());}}}queries_redirect(ME."table=".url_escape($a),($J["drop"]?lang(225):($A!=""?lang(226):lang(227))),$H);if($Qa)driver()->rollback();if(!$J["drop"])$i=lang(228);}$hh=false;if(!$_POST&&$A!=""){$Td=foreign_keys($a);$J=idx($Td,$A,[]);$hh=!$J;}$Tg=str_replace("{table}",$a,adminer()->namePattern("FOREIGN"));$Wg="^".str_replace('\{columns\}','.*',preg_quote($Tg))."$";page_header(($A!=""?lang(229):lang(163)),$i,["table"=>$a],h($A!=""?$A:$a),$hh,doc_link(['sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",]));if($_POST){ksort($J["source"]);if($_POST["change"]||$_POST["change-js"])$J["target"]=[];else$J["source"][]="";}elseif($A!=""){$J["source"][]="";$J["constraint"]=$A;}else{$J["table"]=$a;$J["source"]=[""];$J["constraint"]=str_replace("{columns}","",$Tg);}echo'
<form method="post">
';$qk=array_keys(fields($a));if($J["db"]!="")connection()->select_db($J["db"]);$Qh=null;if($J["ns"]!=""){$Qh=get_schema();set_schema($J["ns"]);}$fj=array_keys(array_filter(table_status('',true),function(array$S){return!$S["dependent"]&&fk_support($S);}));$fl=array_keys(fields(in_array($J["table"],$fj)?$J["table"]:reset($fj)));$b=on('change','foreignChange');echo"<p><label>".lang(230).": ".html_select("table",$fj,$J["table"],$b)."</label>\n";if(JUSH!="sqlite"){$Tj=($J["db"]!=""?$J["db"]:DB);$fc=[];foreach(adminer()->databases()as$g){if(!driver()->isSystem($g)||$g==$Tj||$g==DB)$fc[]=$g;}echo"<label>".lang(85).": ".html_select("db",$fc,$Tj,$b)."</label>";}if($J["db"]!="")connection()->select_db(DB);echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(231),'\'></noscript>
<table',on('change','foreignName',$Wg,$Tg),'>
<thead><tr><th id="label-source">',lang(160),'<th id="label-target">',lang(161),'<tbody>
';$wf=0;foreach($J["source"]as$v=>$X){echo"<tr>","<td>".html_select("source[".(+$v)."]",[-1=>""]+$qk,$X,($wf==count($J["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$v)."]",$fl,idx($J["target"],$v),"","label-target");$wf++;}echo'</table>
<p>
<label>',lang(129),': ',html_select("on_delete",[-1=>""]+explode("|",driver()->onActions),$J["on_delete"]),'</label>
<label>',lang(128),': ',html_select("on_update",[-1=>""]+explode("|",driver()->onActions),$J["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",['NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'],$J["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(232),'\'></noscript>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$A)),'>
';if(JUSH!="sqlite"){print_fieldset("options",lang(133),$J["constraint"]!=$A&&!preg_match("($Wg)",$J["constraint"]));echo"<label>".lang(216).": <input name='constraint' value='".h($J["constraint"])."' autocapitalize='off'></label>\n","</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$J=$_POST;$Rh="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$Rh=strtoupper($P["Engine"]);}if($_POST&&!$i){$A=trim($J["name"]);$Ba=" AS\n$J[select]";$z=ME."table=".url_escape($A);$xg=lang(233);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$A&&JUSH!="sqlite"&&$U=="VIEW"&&$Rh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($A).$Ba,$z,$xg);else{$jl="adminer_".uniqid();drop_create("DROP $Rh ".table($a),"CREATE $U ".table($A).$Ba,"DROP $U ".table($A),"CREATE $U ".table($jl).$Ba,"DROP $U ".table($jl),($_POST["drop"]?substr(ME,0,-1):$z),lang(234),$xg,lang(235),$a,$A);}}$hh=false;if(!$_POST&&$a!=""){$J=view($a);$hh=!$J["select"];$J["name"]=$a;$J["materialized"]=($Rh!="VIEW");if(!$i)$i=adminer()->error();}page_header(($a!=""?lang(49):lang(83)),$i,["table"=>$a],h($a),$hh,doc_link(['sql'=>"create-view.html",]));echo'
<form method="post">
<p>',lang(216),': <input name="name" value="',h($J["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$J["materialized"],lang(154)):""),'<p>';textarea("select",$J["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$jf=["YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND"];$Ck=["ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE"];$J=$_POST;if($_POST&&!$i){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(236));elseif(in_array($J["INTERVAL_FIELD"],$jf)&&isset($Ck[$J["STATUS"]])){$Hj="\nON SCHEDULE ".($J["INTERVAL_VALUE"]?"EVERY ".q($J["INTERVAL_VALUE"])." $J[INTERVAL_FIELD]".($J["STARTS"]?" STARTS ".q($J["STARTS"]):"").($J["ENDS"]?" ENDS ".q($J["ENDS"]):""):"AT ".q($J["STARTS"]))." ON COMPLETION".($J["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(237):lang(238)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Hj.($aa!=$J["EVENT_NAME"]?"\nRENAME TO ".idf_escape($J["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($J["EVENT_NAME"]).$Hj)."\n".$Ck[$J["STATUS"]]." COMMENT ".q($J["EVENT_COMMENT"]).rtrim(" DO\n$J[EVENT_DEFINITION]",";").";"));}}$hh=false;if(!$J&&$aa!=""){$K=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$hh=!$K;$J=reset($K);}page_header(($aa!=""?lang(239).": ".h($aa):lang(240)),$i,"#events","",$hh,doc_link(['sql'=>"create-event.html"]));echo'
<form method="post">
<table class="layout">
<tr><th>',lang(216),'<td><input name="EVENT_NAME" value="',h($J["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(241),'<td><input name="STARTS" value="',h("$J[EXECUTE_AT]$J[STARTS]"),'">
<tr><th title="datetime">',lang(242),'<td><input name="ENDS" value="',h($J["ENDS"]),'">
<tr><th>',lang(243),'<td><input type="number" name="INTERVAL_VALUE" value="',h($J["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$jf,$J["INTERVAL_FIELD"]),'<tr><th>',lang(143),'<td>',html_select("STATUS",$Ck,$J["STATUS"]),'<tr><th>',lang(56),'<td><input name="EVENT_COMMENT" value="',h($J["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$J["ON_COMPLETION"]=="PRESERVE",lang(244)),'</table>
<p>';textarea("EVENT_DEFINITION",$J["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$zj=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$J=$_POST;$J["fields"]=(array)$J["fields"];if($_POST&&!process_fields($J["fields"])&&!$i){foreach($J["fields"]as$v=>$j){if($j["field"]=="")unset($J["fields"][$v]);}$wh=routine($_GET["procedure"],$zj);$uh=($wh?routine_id($ca,$wh):"");$ah=routine_id($J["name"],$J);$Ob=create_routine($zj,$J);$z=substr(ME,0,-1);$xg=lang(245);if(!$_POST["drop"]&&$uh==$ah&&connection()->flavor!="mysql")queries_redirect($z,$xg,queries(substr_replace($Ob,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$jl="adminer_".uniqid();drop_create("DROP $zj $uh",$Ob,"DROP $zj $ah",create_routine($zj,["name"=>$jl]+$J),"DROP $zj ".routine_id($jl,$J),$z,lang(246),$xg,lang(247),$ca,$J["name"]);}}$hh=false;if(!$_POST&&$ca!=""){$J=routine($_GET["procedure"],$zj);$hh=!$J;$J["name"]=$ca;}$Bj=strtolower($zj);page_header(($ca!=""?(isset($_GET["function"])?lang(167):lang(248)).": ".h($ca):(isset($_GET["function"])?lang(249):lang(250))),$i,"#routines","",$hh,doc_link(['sql'=>"create-procedure.html",'mariadb'=>"create-$Bj/",]));if(!$_POST&&$ca=="")$J["language"]="sql";$ub=(JUSH=="sql"?flat_collations():[]);$_j=routine_languages();echo($ub?"<datalist id='collations'>".optionlist($ub)."</datalist>":""),'
<form method="post" id="form">
<p>',lang(216),': <input name="name" value="',h($J["name"]),'" data-maxlength="64" autocapitalize="off">
',($_j?"<label>".lang(30).": ".html_select("language",array_keys($_j),$J["language"],on('change','routineLanguage',$_j))."</label>\n":""),'<input type=\'submit\' value=\'',lang(24),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($J["fields"],$ub,$zj);if(isset($_GET["function"])){echo"<tr><td>".lang(251);edit_type("returns",(array)$J["returns"],$ub,[],(JUSH=="pgsql"?["void","trigger"]:[]));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$J["definition"],20,80,($_j[$J["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$ca)),'>
';$Cj=routine_options($zj);if($Cj){$Hh=false;foreach($Cj
as$v=>$pm){$h=($pm?reset($pm):"");$J["options"][$v]=idx($J["options"],$v,$h);if($J["options"][$v]!=$h)$Hh=true;}print_fieldset("options",lang(133),$Hh);echo"<table class='layout'>\n";foreach($Cj
as$v=>$pm){$Df="label-option-$v";$rl=str_replace("_"," ",$v);$M=[];foreach($pm
as$Y)$M[$Y]=(strpos($Y,"$rl ")===0?substr($Y,strlen($rl)+1):$Y);echo"<tr><th id='$Df'>$rl<td>".($M?html_select("options[$v]",$M,$J["options"][$v],"",$Df):"<input name='options[$v]' value='".h($J["options"][$v])."' aria-labelledby='$Df' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$A="$_GET[name]";$J=$_POST;if($J&&!$i){$z=ME."table=".url_escape($a);$_g=lang(252);$yg=lang(253);$zg=lang(254);if(JUSH=="sqlite"&&!(min_version(3.53)&&($A==""||idx(driver()->checkConstraints($a),$A)!=$A))){$ja=($J["drop"]?"":($J["name"]!=""?"CONSTRAINT ".idf_escape($J["name"])." ":"")."CHECK ($J[clause])");queries_redirect($z,($J["drop"]?$_g:($A!=""?$yg:$zg)),recreate_table($a,$a,[],[],[],"",[],"$A",$ja));}else{$va="ALTER TABLE ".table($a);$hb=" CHECK ($J[clause])";$jl="adminer_".uniqid();drop_create("$va DROP CONSTRAINT ".idf_escape($A),"$va ADD".($J["name"]!=""?" CONSTRAINT ".idf_escape($J["name"]):"").$hb,"$va DROP CONSTRAINT ".idf_escape($J["name"]),"$va ADD CONSTRAINT ".idf_escape($jl).$hb,"$va DROP CONSTRAINT ".idf_escape($jl),$z,$_g,$yg,$zg,$A,$J["name"]);}}$hh=false;if(!$J){$kb=driver()->checkConstraints($a);$hh=($A!=""&&!$kb[$A]);$J=["name"=>($A==""?object_name("CHECK",$a,[]):($kb[$A]!=$A?$A:"")),"clause"=>$kb[$A]];}page_header(($A!=""?lang(255):lang(165)),$i,["table"=>$a],h($A!=""?$A:$a),$hh,doc_link(['sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",]));echo'
<form method="post">
<p><label>',lang(216),': <input name="name" value="',h($J["name"]),'" data-maxlength="64" autocapitalize="off"></label>
<p>';textarea("clause",$J["clause"]);echo'<p><input type=\'submit\' value=\'',lang(24),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$A="$_GET[name]";$Gl=trigger_options();$J=trigger($A,$a);$hh=($A!=""&&!$J);$Tg=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$J+=["Trigger"=>strtr($Tg,["{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row"])];if($_POST){if(!$i&&in_array($_POST["Timing"],$Gl["Timing"])&&in_array($_POST["Event"],$Gl["Event"])&&in_array($_POST["Type"],$Gl["Type"])){$yh=" ON ".table($a);$Ic="DROP TRIGGER ".idf_escape($A).(JUSH=="pgsql"?$yh:"");$z=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Ic,$z,lang(256));else{if($A!="")queries($Ic);queries_redirect($z,($A!=""?lang(257):lang(258)),queries(create_trigger($yh,$_POST)));if($A!="")queries(create_trigger($yh,$J+["Type"=>reset($Gl["Type"])]));}}$J=$_POST;}page_header(($A!=""?lang(259):lang(168)),$i,["table"=>$a],h($A!=""?$A:$a),$hh,doc_link(['sql'=>"create-trigger.html",]));$Wg=strtr(preg_quote($Tg),['\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)']);$Fl=on('change','triggerChange',"^$Wg$",$Tg);$nh=on('input','triggerChange',"^$Wg$",$Tg);echo'
<form method="post" id="form">
<table class="layout">
<tr><th>',lang(260),'<td>',html_select("Timing",$Gl["Timing"],$J["Timing"],$Fl),'<tr><th>',lang(261),'<td>',html_select("Event",$Gl["Event"],$J["Event"],$Fl),(in_array("UPDATE OF",$Gl["Event"])?" <input name='Of' value='".h($J["Of"])."' class='hidden'$nh>":""),'<tr><th>',lang(55),'<td>',html_select("Type",$Gl["Type"],$J["Type"],$Fl),'<tr><th>',lang(216),'<td><input name="Trigger" value="',h($J["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$J["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($ge,array$Pi,$d,$yh){if(!$Pi)return
true;if($Pi==["ALL PRIVILEGES","GRANT OPTION"])return($ge=="GRANT"?queries("$ge ALL PRIVILEGES$yh WITH GRANT OPTION"):queries("$ge ALL PRIVILEGES$yh")&&queries("$ge GRANT OPTION$yh"));return
queries("$ge ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$Pi).$d).$yh);}$ea=$_GET["user"];$Pi=[""=>["All privileges"=>""]];foreach(get_rows("SHOW PRIVILEGES")as$J){foreach(explode(",",($J["Privilege"]=="Grant option"?"":$J["Context"]))as$Ib)$Pi[$Ib=="File access on server"?"Server Admin":$Ib][$J["Privilege"]]=$J["Comment"];}unset($Pi["Server Admin"]["Usage"]);foreach($Pi["Tables"]as$v=>$X)unset($Pi["Databases"][$v]);$Zg=[];if($_POST){foreach($_POST["objects"]as$v=>$X)$Zg[$X]=(array)$Zg[$X]+idx($_POST["grants"],$v,[]);}$he=[];$H=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])):null);$hh=(isset($_GET["host"])&&!$H);if($H){while($J=$H->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$J[0],$_)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$_[1],$fg,PREG_SET_ORDER)){foreach($fg
as$X){if($X[1]!="USAGE")$he["$_[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$J[0]))$he["$_[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$i){$xh=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $xh",ME."privileges=",lang(262));else{$dh=q($_POST["user"])."@".q($_POST["host"]);$mi=$_POST["pass"];$Qb=false;$H=true;if($xh!=$dh){$Qb=queries("CREATE USER $dh IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($mi));$H=$Qb;}elseif($mi!="")$H=queries("SET PASSWORD FOR $dh = ".(min_version(8,99)||$_POST["hashed"]?q($mi):"PASSWORD(".q($mi).")"));if($H){$vj=[];foreach($Zg
as$lh=>$ge){if(isset($_GET["grant"]))$ge=array_filter($ge);$ge=array_keys($ge);if(isset($_GET["grant"]))$vj=array_diff(array_keys(array_filter($Zg[$lh],'strlen')),$ge);elseif($xh==$dh){$th=array_keys((array)$he[$lh]);$vj=array_diff($th,$ge);$ge=array_diff($ge,$th);unset($he[$lh]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$lh,$_)&&(!grant("REVOKE",$vj,$_[2]," ON $_[1] FROM $dh")||!grant("GRANT",$ge,$_[2]," ON $_[1] TO $dh"))){$H=false;break;}}}if($H&&isset($_GET["host"])){if($xh!=$dh)queries("DROP USER $xh");elseif(!isset($_GET["grant"])){foreach($he
as$lh=>$vj){if(preg_match('~^(.+)(\(.*\))?$~U',$lh,$_))grant("REVOKE",array_keys($vj),$_[2]," ON $_[1] FROM $dh");}}}if($H&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(263):lang(264)),$H);if($Qb)connection()->query("DROP USER $dh");}}page_header((isset($_GET["host"])?lang(40).": ".h("$ea@$_GET[host]"):lang(176)),$i,["privileges"=>['',lang(76)]],"",$hh,doc_link(['sql'=>"grant.html",'mariadb'=>"grant"]));$J=$_POST;if($J)$he=$Zg;else{$J=$_GET+["host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)")];$he[(DB==""||$he?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=[];}echo'<form method="post">
<table class="layout">
<tr><th>',lang(38),'<td><input name="host" data-maxlength="60" value="',h($J["host"]),'" autocapitalize="off">
<tr><th>',lang(40),'<td><input name="user" data-maxlength="80" value="',h($J["user"]),'" autocapitalize="off">
<tr><th>',lang(41),'<td><input name="pass" id="pass" value="',h($J["pass"]),'" autocomplete="new-password">
',($J["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$J["hashed"],lang(265),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(76);$q=0;foreach($he
as$lh=>$ge){echo'<th>'.($lh!="*.*"?"<input name='objects[$q]' value='".h($lh)."' size='10' autocapitalize='off'>":input_hidden("objects[$q]","*.*")."*.*");$q++;}echo"<tbody>\n";foreach([""=>"","Server Admin"=>lang(38),"Databases"=>lang(17),"Tables"=>lang(156),"Procedures"=>lang(266),]as$Ib=>$pc){foreach((array)$Pi[$Ib]as$Oi=>$yb){echo"<tr><td".($pc?">$pc<td":" colspan='2'").' lang="en" title="'.h($yb).'">'.h($Oi);$q=0;foreach($he
as$lh=>$ge){$A="'grants[$q][".h(strtoupper($Oi))."]'";$Y=$ge[strtoupper($Oi)];if($Ib=="Server Admin"&&$lh!=(isset($he["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$A><option><option value='1'".($Y?" selected":"").">".lang(267)."<option value='0'".($Y=="0"?" selected":"").">".lang(268)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$A value='1'".($Y?" checked":"").($Oi=="All privileges"?" id='grants-$q-all'":($Oi=="Grant option"?"":on('click','grantsClick',"grants-$q-all"))).">","</label>";$q++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$i){$Cf=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$Cf++;}queries_redirect(ME."processlist=",lang(269,$Cf),$Cf||!$_POST["kill"]);}}page_header(lang(141),$i);echo'
<form method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$q=-1;foreach(adminer()->processList()as$q=>$J){if(!$q){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($J
as$v=>$X)echo"<th>$v".doc_link(['sql'=>"show-processlist.html#processlist_".strtolower($v),]);echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$J[JUSH=="sql"?"Id":"pid"],0):"");foreach($J
as$v=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$v=="Info"&&preg_match("~Query|Killed~",$J["Command"]))||(JUSH=="pgsql"&&$v=="query")||(JUSH=="oracle"&&$v=="sql_text"))?"<code class='sql jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($J["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($J["db"])."&":ME)."sql=".url_escape($X)).'">'.lang(270).'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($q+1)."/".lang(271,max_connections()),"<p><input type='submit' value='".lang(272)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$u=indexes($a);$k=fields($a);$Td=column_foreign_keys($a);$sh=$S["Oid"];$xj=[];$d=[];$Nj=[];$Jh=[];$ml=null;foreach($k
as$v=>$j){$A=adminer()->fieldName($j);$Vg=html_entity_decode(strip_tags($A),ENT_QUOTES);if(isset($j["privileges"]["select"])&&$A!=""){$d[$v]=$Vg;if(is_shortable($j))$ml=adminer()->selectLengthProcess();}if(isset($j["privileges"]["where"])&&$A!="")$Nj[$v]=$Vg;if(isset($j["privileges"]["order"])&&$A!="")$Jh[$v]=$Vg;$xj+=$j["privileges"];}list($M,$p)=adminer()->selectColumnsProcess($d,$u);$M=array_unique($M);$p=array_unique($p);$qf=count($p)<count($M);$Z=adminer()->selectSearchProcess($k,$u,$S);$C=adminer()->selectOrderProcess($k,$u);$x=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Sl=>$J){$Ba=convert_field($k[key($J)]);$M=[$Ba?:idf_escape(key($J))];$Z[]=where_check(bracket_escape($Sl,true),$k);$I=driver()->select($a,$M,$Z,$M);if($I)echo
first($I->fetch_row());}exit;}$Li=$Ul=[];foreach($u
as$t){if($t["type"]=="PRIMARY"){$Li=array_flip($t["columns"]);$Ul=($M?$Li:[]);foreach($Ul
as$v=>$X){if(in_array(idf_escape($v),$M))unset($Ul[$v]);}break;}}if($sh&&!$Li){$Li=$Ul=[$sh=>0];$u[]=["type"=>"PRIMARY","columns"=>[$sh]];}if($_POST&&!$i){$_m=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$kb=[];foreach($_POST["check"]as$hb)$kb[]=where_check($hb,$k);$_m[]="((".implode(") OR (",$kb)."))";}$Bm=$_m;$_m=($_m?"\nWHERE ".implode(" AND ",$_m):"");if($_POST["export"]){save_settings(["output"=>$_POST["output"],"format"=>$_POST["format"]],"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Rj=($M?:["*"]);$Kb=convert_fields($d,$k,$M);if($Kb)$Rj[]=substr($Kb,2);$G="";if(is_array($_POST["check"])&&!$Li){$Yd=implode(", ",$Rj)."\nFROM ".table($a);$ke=($p&&$qf?"\nGROUP BY ".implode(", ",$p):"").($C?"\nORDER BY ".implode(", ",$C):"");$Pl=[];foreach($_POST["check"]as$X)$Pl[]="(SELECT".limit($Yd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$k).$ke,1).")";$G=implode(" UNION ALL ",$Pl);}adminer()->dumpData($a,"table",$G,$Rj,$Bm,($qf?$p:[]),$C);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Td)){if($_POST["save"]||$_POST["delete"]){$H=true;$oa=0;$Qa=false;$O=[];if(!$_POST["delete"]){foreach($k
as$A=>$X){$s=bracket_escape($A);if(isset($_POST["fields"][$s])||$_FILES["fields-$s"]){$X=process_input($k[$A]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($A)]=($X!==false?$X:idf_escape($A));}}}if($_POST["delete"]||$O){$G=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($Li&&is_array($_POST["check"]))||$qf){$H=($_POST["delete"]?driver()->delete($a,$_m):($_POST["clone"]?queries("INSERT $G$_m".driver()->insertReturning($a)):driver()->update($a,$O,$_m)));$oa=connection()->affected_rows;if(is_object($H))$oa+=$H->num_rows;}else{$Qa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$zm="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$k);$H=($_POST["delete"]?driver()->delete($a,$zm,1):($_POST["clone"]?queries("INSERT".limit1($a,$G,$zm)):driver()->update($a,$O,$zm,1)));if(!$H)break;$oa+=connection()->affected_rows;}if($Qa&&$H&&!driver()->commit())$H=false;}}$xg=lang(177,$oa);if($_POST["clone"]&&$H&&$oa==1){$Lf=last_id($H);if($Lf)$xg=lang(199," $Lf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$xg,$H);if($Qa)driver()->rollback();if(!$_POST["delete"]){$Ci=(array)$_POST["fields"];edit_form($a,array_intersect_key($k,$Ci),$Ci,!$_POST["clone"],$i);page_footer();exit;}}elseif(!$_POST["import"]){$H=true;$oa=0;$Qa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Sl=>$J){$O=[];foreach($J
as$v=>$X){$v=bracket_escape($v,true);$O[idf_escape($v)]=(preg_match('~char|text~',$k[$v]["type"])||$X!=""?adminer()->processInput($k[$v],$X):"NULL");}$H=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Sl,true),$k),($qf||$Li?0:1)," ");if(!$H)break;$oa+=connection()->affected_rows;}if($Qa)$H=$H&&driver()->commit();queries_redirect(remove_from_uri(),lang(177,$oa),$H);if($Qa)driver()->rollback();}else{save_settings(["format"=>$_POST["separator"]],"adminer_import");$Gd=get_file("csv_file",true);if(!is_string($Gd))$i=upload_error($Gd);elseif(!preg_match('~~u',$Gd))$i=lang(273);else{$vb=array_keys($k);$Wj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Ub=parse_csv($Gd,$Wj);$oa=count($Ub);driver()->begin();$K=[];foreach($Ub
as$v=>$pm){if(!$v&&!array_diff($pm,$vb)){$vb=$pm;$oa--;}else{$O=[];foreach($pm
as$q=>$rb)$O[idf_escape($vb[$q])]=($rb==""&&$k[$vb[$q]]["null"]?"NULL":q(csv_value($rb)));$K[]=$O;}}$H=(!$K||driver()->insertUpdate($a,$K,$Li));if($H)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(274,$oa),$H);driver()->rollback();}}}}$Uk=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(59).": $Uk",$i,[],"",(!$k&&support("table")),($k?doc_link([JUSH=>driver()->tableHelp($a,is_view($S))]):""));$O=null;if(isset($xj["insert"])||!support("table")){$O="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$_)?$_[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$O
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$O);if(!$d&&support("table"))echo"<p class='error'>".lang(275)."\n";else{echo"<form id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$Nj,$u,$S);adminer()->selectOrderPrint($C,$Jh,$u);adminer()->selectLimitPrint($x);if($ml!==null)adminer()->selectLengthPrint($ml);adminer()->selectActionPrint($u);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],["","same-origin"])){echo"<p class='error'>".lang(122).' '.lang(123)."\n";page_footer();exit;}}$D=$_GET["page"];$Wd=null;if($D=="last"){$Wd=get_val(count_rows($a,$Z,$qf,$p));$D=floor(max(0,intval($Wd)-1)/$x);}$Qj=$M;$je=$p;if(!$Qj){$Qj[]="*";$Kb=convert_fields($d,$k,$M);if($Kb)$Qj[]=substr($Kb,2);}foreach($M
as$v=>$X){$j=$k[idf_unescape($X)];if($j&&($Ba=convert_field($j)))$Qj[$v]="$Ba AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$v=>$X){if(isset($Qj[$v])&&$X["fun"])$Qj[$v].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$qf&&$Ul){foreach($Ul
as$v=>$X){$Qj[]=idf_escape($v);if($je)$je[]=idf_escape($v);}}$H=driver()->select($a,$Qj,$Z,$je,$C,$x,$D,true);if(!is_object($H))echo"<p class='error'>".(adminer()->error()?:lang(32))."\n";else{if(JUSH=="mssql"&&$D)$H->seek($x*$D);$Vc=[];$K=[];while($J=$H->fetch_assoc()){if($D&&JUSH=="oracle")unset($J["RNUM"]);$K[]=$J;}$ue=($x&&(support("cursor")?$_GET["next"]!="":count($K)>=$x));if(is_ajax()&&$ue)header("X-Next-Page: ".pagination_href($D+1));if($_GET["modify"]&&$K){$og=max_input_vars(count($K[0])+1,20);echo($og&&count($K)>$og?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form method='post' enctype='multipart/form-data'".on_upload_progress($am).">\n";if($_GET["page"]!="last"&&$x&&$p&&$qf&&JUSH=="sql")$Wd=get_val(" SELECT FOUND_ROWS()");if(!$K)echo"<p class='message'>".lang(22)."\n";else{$Ma=adminer()->backwardKeys($a,$Uk);$tj=[];reset($M);foreach($K[0]as$v=>$X){if(!isset($Ul[$v])){$X=idx($_GET["columns"],key($M))?:[];$tj[$v]=["fun"=>$X["fun"],"col"=>($M?$X["col"]:$v)];next($M);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$p&&$M?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(276)."'".on('click','formCheck','^check').">");$Xg=[];$cj=1;foreach($tj
as$v=>$X){$j=$k[$X["col"]];$A=($j?adminer()->fieldName($j,$cj):($X["fun"]?($X["col"]!=""?h($X["col"]):"*"):h($v)));if($A!=""){$cj++;$Xg[$v]=$A;$c=idf_escape($v);$Ie=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($v);$pc="&desc[0]=1";$nk=preg_replace('~ DESC( NULLS LAST)?$~','',$C[0]);$pk=($nk==$c||$nk==$v);echo"<th id='th[".h(bracket_escape($v))."]'".($pk?" aria-sort='".($nk==$C[0]?"ascending":"descending")."'":"").">";$ce=apply_sql_function(h($X["fun"]),$A);$ok=isset($j["privileges"]["order"])||$X["fun"];echo($ok?"<a href='".h($Ie.($pk&&$nk==$C[0]?$pc:''))."'>$ce</a>":$ce),doc_link([JUSH=>$j["doc"]]);$wg=($ok?"<a href='".h($Ie.$pc)."' title='".lang(64)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($j["privileges"]["where"]))$wg
.="<a href='#fieldset-search' title='".lang(18)."' class='text jsonly'".on('click','selectSearch',$v)."> =</a>";echo($wg?"<span class='column'>$wg</span>":"");}}$Sf=[];if($_GET["modify"]){foreach($K
as$J){foreach($J
as$v=>$X)$Sf[$v]=max($Sf[$v],min(40,utf8_length((string)$X)));}}$De=[];$Ce=[];foreach((array)$_GET["where"]as$X){$X+=["col"=>"","op"=>"","val"=>""];$rb=$X["col"];$Mj=$X["val"];if(!is_array($Mj)&&($Mj!=""||preg_match('~NULL$~',$X["op"]))&&(!$X["op"]||in_array($X["op"],adminer()->operators($S)))){$Uf=strtr(preg_quote($Mj),["%"=>".*?","_"=>"."]);$si=["LIKE %%"=>$Uf,"ILIKE %%"=>$Uf,"REGEXP"=>$Mj]+(JUSH=="pgsql"?["~"=>$Mj,"~*"=>$Mj]:[])+($rb!=""?[]:["="=>'^'.preg_quote($Mj).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Mj)))).')\z',"LIKE"=>"^$Uf\\z","ILIKE"=>"^$Uf\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Mj).'(?=,|\z)',]);foreach(($rb!=""?[$rb=>$k[$rb]]:$k)as$A=>$j){if($rb!=""||is_searchable($j,$X)){$_h=$X["op"]?:(!preg_match('~'.text_type().'~',$j["type"])?"IN":(preg_match('~%~',$Mj)?"LIKE":"LIKE %%"));if(isset($si[$_h])){$nb=preg_match('~^ILIKE|\*$~',$_h)||($_h!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$De[$A][]="(?".($nb?"i":"").":$si[$_h])";}elseif($_h=="IS NULL"&&$rb=="")$Ce[$A]=true;}}}}echo($Ma?"<th>".lang(277):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($K,$Td)as$Rg=>$J){$Rl=unique_array($K[$Rg],$u);if(!$Rl){$Rl=[];foreach($K[$Rg]as$v=>$X){if(!in_array(idx(idx($tj,$v,[]),"fun"),driver()->grouping))$Rl[$v]=$X;}}$Sl="";$q=0;foreach($Rl
as$v=>$X){$sj=idx($tj,$v,[]);if($M&&$sj&&!isset($k[$sj["col"]]))continue;$ce=idx($sj,"fun","");$rb=($ce?$sj["col"]:$v);$j=(array)$k[$rb];$pf=is_blob($j);if(!$ce&&strlen($X)>64&&driver()->md5(idf_escape($rb),$j)){$ce="md5";$X=md5($pf?(string)driver()->value($X,$j):$X);}if($ce){$Sl
.="&fun[$q]=".url_escape($ce)."&col[$q]=".url_escape($rb).($X!==null?"&val[$q]=".url_escape($X===false?"f":$X):"");$q++;}else$Sl
.="&".($X!==null?"where[".url_escape(bracket_escape($rb))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($rb));}echo"<tr>".(!$p&&$M?"":"<td class='hover check sticky'>".($qf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Sl)."' class='edit'>".lang(278)."</a> ").checkbox("check[]",substr($Sl,1),in_array(substr($Sl,1),(array)$_POST["check"])));foreach($J
as$v=>$X){if(isset($Xg[$v])){$ce=$tj[$v]["fun"];$rb=$tj[$v]["col"];$j=(array)$k[$v];if($X!=""&&(!isset($Vc[$v])||$Vc[$v]!=""))$Vc[$v]=(is_mail($X)?$Xg[$v]:"");$y="";if(is_blob($j)&&$X!="")$y=ME.'download='.url_escape($a).'&field='.url_escape($v).$Sl;if(!$y&&$X!==null){foreach((array)$Td[$v]as$m){if(count($Td[$v])==1||end($m["source"])==$v){$y="";foreach($m["source"]as$q=>$qk)$y
.=where_link($q,$m["target"][$q],$K[$Rg][$qk]);$y=($m["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($m["db"]),ME):ME).'select='.url_escape($m["table"]).$y;if($m["ns"]!="")$y=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($m["ns"]),$y);if(count($m["source"])==1)break;}}}if($ce=="count"&&$rb==""){$y=ME."select=".url_escape($a);$q=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Rl))$y
.=where_link($q++,$W["col"],$W["val"],$W["op"]);}foreach($Rl
as$_f=>$W){if(idx(idx($tj,$_f,[]),"fun")){$y="";break;}$y
.=where_link($q++,$_f,$W);}}$Je=select_value($X,$y,$j,$ml,($ce?[]:idx($De,$v,[])));if($X===null&&!$ce&&isset($Ce[$v]))$Je="<mark>$Je</mark>";$s=bracket_escape($Sl);$r=h("val[$s][".bracket_escape($v)."]");$Ei=idx(idx($_POST["val"],$s),bracket_escape($v));$Xl=idx($j["privileges"],"update")&&!is_identity_always($j);$Qc=$K[$Rg][$v];$Rc=!is_array($J[$v])&&!is_blob($j)&&is_utf8($Qc)&&!$ce&&!$j["generated"]&&$Xl;$U=($ce=="min"||$ce=="max"?$k[$rb]["type"]:$j["type"]);$ll=preg_match('~text|json|lob~',$U);$rf=preg_match(number_type(),$U)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$ce);echo"<td id='$r'".($rf&&($X===null||is_numeric(strip_tags($Je))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Rc&&$Qc!==null)||$Ei!==null){$pe=h($Ei!==null?$Ei:$Qc);echo">".($ll?"<textarea name='$r' cols='30' rows='".(substr_count($Qc,"\n")+1)."'>\n$pe</textarea>":"<input name='$r' value='$pe' size='$Sf[$v]'>");}else{$cg=strpos($Je,"<i>…</i>");$Pc=adminer()->editVal($Qc,$j);echo($Xl?" data-text='".($cg?2:($ll?1:0))."'".($Rc?"":" data-warning='".lang(279)."'").($Rc&&!$cg&&html_entity_decode(strip_tags($Je),ENT_QUOTES,"UTF-8")!==$Pc?" data-value='".h($Pc)."'":""):"").">$Je";}}}if($Ma)echo"<td>";adminer()->backwardKeysPrint($Ma,$K[$Rg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$na=get_settings("adminer_import");if($K||$D||$ue){$nd=true;if($_GET["page"]!="last"){if(!$x||(count($K)<$x&&($K||!$D)))$Wd=($D?$D*$x:0)+count($K);elseif(JUSH!="sql"||!$qf){$Wd=($qf?null:found_rows($S,$Z));$nd=!driver()->hasEstimatedRows();if($Wd===null||(!$nd&&$Wd<max(1e4,2*($D+1)*$x))){$Wd=first(slow_query(count_rows($a,$Z,$qf,$p)));$nd=true;}}}if(!support("cursor"))$ue=(($Wd===false?count($K)+1:$Wd-$D*$x)>$x);$Xh=($x&&($ue||$D));if($Xh)echo($ue?'<p><a href="'.h(pagination_href($D+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(280)).'>'.lang(281).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Xh){$mg=($Wd===false?$D+($K?(count($K)>=$x?2:1):0):floor(($Wd-1)/$x));echo"<fieldset><legend>".lang(282)."</legend>";if(!support("cursor")){echo
pagination(0,$D).($D>5?" …":"");for($q=max(1,$D-4);$q<min($mg,$D+5);$q++)echo
pagination($q,$D);if($mg>0)echo($D+5<$mg?" …":""),($nd&&$Wd!==false?pagination($mg,$D):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$mg'>".lang(283)."</a>");}else
echo
pagination(0,$D).($D>1?" …":""),($D?pagination($D,$D):""),($ue?pagination($D+1,$D)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(284)."</legend>";$xc=($nd?"":"~ ").$Wd;$Df=($Wd!==false?($nd?"":"~ ").lang(181,$Wd):"");echo
checkbox("all",1,0,$Df,on('click','countRows',$xc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(182)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(285),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(24),'\'',($_GET["modify"]||$_POST["val"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(151),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(20),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(270),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(28),'\'',confirm(),'>
</div></fieldset>
';$Ud=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]||($c["col"]!=""&&!isset($k[$c["col"]]))){unset($Ud['sql']);break;}}if($Ud){print_fieldset("export",lang(81)." <span id='selected2'></span>");$Vh=adminer()->dumpOutput();echo($Vh?html_select("output",$Vh,$na["output"])." ":""),html_select("format",$Ud,$na["format"])," <input type='submit' name='export' value='".lang(81)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Vc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(80)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($am?input_hidden(ini_get("session.upload_progress.name"),$am):""),file_input(" name='csv_file'"," ".html_select("separator",["csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"],$na["format"])." <input type='submit' name='import' value='".lang(80)."'>".($am?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$p&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?lang(143):lang(142));$qm=($P?adminer()->showStatus():adminer()->showVariables());if(!$qm)echo"<p class='message'>".lang(22)."\n";else{echo"<table>\n";foreach($qm
as$J){echo"<tr>";$v=array_shift($J);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($v)."</code>";foreach($J
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Kk=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];foreach(table_status()as$A=>$S){json_row("Comment-$A",h($S["Comment"]).($S["Error"]?" <span class='error'>".h($S["Error"])."</span>":""));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(["Engine","Collation"]as$v)json_row("$v-$A",h($S[$v]));foreach(array_keys($Kk+["Auto_increment"=>0,"Rows"=>0])as$v){if(array_key_exists($v,$S))json_row("$v-$A",format_status($S,$v));if($S[$v]!=""&&isset($Kk[$v]))$Kk[$v]+=($S["Engine"]!="InnoDB"||$v!="Data_free"?$S[$v]:0);}}}if(function_exists('Adminer\db_status'))$Kk=db_status();foreach($Kk
as$v=>$X)json_row("sum-$v",adminer()->formatSizeValue($X));json_row("");}else{foreach(count_tables(adminer()->databases(false))as$g=>$X){json_row("tables-$g",format_number($X));json_row("size-$g",db_size($g));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$tg=ME.(isset($_GET["select"])?"select=&":"");$dl=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($dl&&!$i&&!$_POST["search"]){$H=true;$xg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$H=truncate_tables($_POST["tables"]);$xg=lang(286);}elseif($_POST["move"]){$H=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$xg=lang(287);}elseif($_POST["copy"]){$H=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$xg=lang(288);}elseif($_POST["drop"]){if($_POST["views"])$H=drop_views($_POST["views"]);if($H&&$_POST["tables"])$H=drop_tables($_POST["tables"]);$xg=lang(289);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$J)$xg
.="<b>".h($R)."</b>: ".h($J["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("DBCC CHECKTABLE (".q(table($R)).") WITH TABLERESULTS")as$J)$xg
.="<b>".h($R)."</b>: ".h($J["MessageText"])."<br>";}}elseif(JUSH!="sql"){$H=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$xg=lang(290);}elseif(!$_POST["tables"])$xg=lang(19);elseif($H=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($J=$H->fetch_assoc())$xg
.="<b>".h($J["Table"])."</b>: ".h($J["Msg_text"])."<br>";}queries_redirect(relative_uri(),$xg,$H);}page_header(($_GET["ns"]==""?lang(17).": ".h(DB):lang(16).": ".h($_GET["ns"])),$i,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$C=$_GET["order"];$Zd=($C||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(291)."</h3>\n";$cl=($Zd?table_status():tables_list());$Kd=$_GET["filter"];if($Kd!=""){foreach($cl
as$R=>$P){if(stripos("$R",$Kd)===false)unset($cl[$R]);}}if(!$cl)echo"<p class='message'>".lang(19)."\n";else{echo"<form method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(15)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"));$G=($_POST?$_POST["query"]:$_GET["query"]);echo" <input type='search' name='query' value='".h($G!=""?$G:$Kd)."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(18)."'>\n","</div></fieldset>\n";if(!$i&&(!$_POST||$_POST["search"])&&$G!=""){$_GET["where"][0]["op"]=idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%");search_tables($G);}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(175).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$C&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($tg,0,-1)).'">'.lang(156).'</a>';$d=["Engine"=>[lang(292).doc_link(['sql'=>'storage-engines.html'])]];$ub=collations();if($ub)$d["Collation"]=[lang(147).doc_link(['sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/'])];if(function_exists('Adminer\alter_table'))$d["Data_length"]=[lang(293).doc_link(['sql'=>'show-table-status.html',]),"create",lang(50),];if(support("indexes"))$d["Index_length"]=[lang(294).doc_link(['sql'=>'show-table-status.html',]),"indexes",lang(159),];$d["Data_free"]=[lang(295).doc_link(['sql'=>'show-table-status.html']),"edit",lang(51)];if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=[lang(57).doc_link(['sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/']),"auto_increment=1&create",lang(50),];$d["Rows"]=[lang(296).doc_link(['sql'=>'show-table-status.html',]),"select",lang(47),];if(support("comment"))$d["Comment"]=[lang(56).doc_link(['sql'=>'show-table-status.html',]),];$Ca=['Engine','Collation','Comment'];foreach($d
as$v=>$c)echo"<th".($C==$v?" aria-sort='".(in_array($v,$Ca)?"ascending":"descending")."'":"")."><a href='".h($tg)."order=$v'>$c[0]</a>";echo"<tbody>\n";if($C){uasort($cl,function($ga,$Ja)use($C,$Ca){$I=($ga[$C]<$Ja[$C]?-1:($ga[$C]>$Ja[$C]?1:0));return(in_array($C,$Ca)?$I:-$I);});}$T=0;$Kk=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];foreach($cl
as$A=>$P){$tm=($Zd?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($Zd?$P:['Engine'=>$P]);$r=h("Table-".$A);echo'<tr><td class="hover">'.checkbox(($tm?"views[]":"tables[]"),$A,in_array("$A",$dl,true),"","","",$r),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($A)."' title='".lang(48)."' id='$r'>".h($A).'</a>':h($A));if($tm&&!preg_match('~materialized~i',$P['Engine'])){$rl=lang(155);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($A)."' title='".lang(49)."'>$rl</a>":$rl),"<td align='right'><a href='".h(ME)."select=".url_escape($A)."' title='".lang(47)."'>?</a>";if(support("comment"))echo'<td>'.h($P['Comment']);}else{if($Zd){foreach(array_keys($Kk)as$v)$Kk[$v]+=($P["Engine"]!="InnoDB"||$v!="Data_free"?idx($P,$v):0);}foreach($d
as$v=>$c){$r=" id='$v-".h($A)."'";echo($c[1]?"<td align='right'>".($v=="Index_length"&&$P["invalid_index"]?"<span class='error'>INVALID</span> ":"")."<a href='".h(ME."$c[1]=").url_escape($A)."'$r title='$c[2]'>".format_status($P,$v)."</a>":"<td$r>".h(idx($P,$v,'?')).($v=="Comment"&&$P["Error"]?" <span class='error'>".h($P["Error"])."</span>":""));}$T++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(271,count($cl)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),($ub?"<td>".h(db_collation(DB,$ub)):'');if($Zd&&function_exists('Adminer\db_status'))$Kk=db_status();foreach($Kk
as$v=>$Jk)echo($d[$v]?"<td align='right' id='sum-$v'>".($Zd?adminer()->formatSizeValue($Jk):""):"");echo"\n","</table>\n",($Zd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$mm="<input type='submit' value='".lang(297)."'".on_help("VACUUM")."> ";$Eh="<input type='submit' name='optimize' value='".lang(298)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Mi=(JUSH=="sqlite"?$mm."<input type='submit' name='check' value='".lang(299)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$mm.$Eh:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(299)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(300)."'".on_help("ANALYZE TABLE")."> ".$Eh."<input type='submit' name='check' value='".lang(299)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(301)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(302)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(152)."'".confirm().on_help("DROP TABLE").">":"");echo($Mi?"<div class='footer'><div>\n<fieldset><legend>".lang(151)." <span id='selected'></span></legend><div>$Mi\n</div></fieldset>\n":"");$f=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($f)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(303)." <span id='selected3'></span></legend><div>";$g=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($f?html_select("target",$f,$g):'<input name="target" value="'.h($g).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(135)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(29)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(304)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(82)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(83)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(77)."</h3>\n";$Ej=routines();if($Ej){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(216).'<th>'.lang(55).'<th>'.lang(251)."<td class='hover'><tbody>\n";foreach($Ej
as$J){$A=($J["SPECIFIC_NAME"]==$J["ROUTINE_NAME"]?"":"&name=".url_escape($J["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($J["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($J["SPECIFIC_NAME"]).$A).'"'.' title="'.($J["DTD_IDENTIFIER"]=="trigger"?lang(166):lang(223)).'">'.h($J["ROUTINE_NAME"]).'</a>','<td>'.h($J["ROUTINE_TYPE"]),'<td>'.h($J["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($J["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($J["SPECIFIC_NAME"]).$A).'">'.lang(162)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(250).'</a>':'').'<a href="'.h(ME).'function=">'.lang(249)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(79)."</h3>\n";$K=get_rows("SHOW EVENTS");if($K){echo"<table>\n","<thead><tr><th>".lang(216)."<th>".lang(305)."<th>".lang(241)."<th>".lang(242)."<td class='hover'><tbody>\n";foreach($K
as$J)echo"<tr>","<th>".h($J["Name"]),"<td>".($J["Execute at"]?lang(306)."<td>".h($J["Execute at"]):lang(243)." ".h($J["Interval value"])." ".h($J["Interval field"])."<td>".h($J["Starts"])),"<td>".h($J["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($J["Name"]).'">'.lang(162).'</a>';echo"</table>\n";$kd=get_val("SELECT @@event_scheduler");if($kd&&$kd!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($kd)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(240)."</a>\n","</div>\n";}}}}page_footer();