<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.3.1
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.3.1";error_reporting(24575);set_error_handler(function($ed,$hd){return!!preg_match('~^Undefined (array key|offset|index)~',$hd);},E_WARNING|E_NOTICE);if(!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"))||ini_get("filter.default_flags")){foreach(['_GET','_POST','_COOKIE','_SERVER']as$X){$Zl=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Zl)$$X=$Zl;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
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
true;$U=$j["type"];$Nj=$X["val"];$Ra='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Ra~",$U))return
false;if(preg_match(number_type(),$U)){$kh='-?\d+(\.\d+)?';$Mg='';if(preg_match('~IN$~',$X["op"])){$Nj=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$Nj);$Mg="( *, *$kh)*";}elseif($X["op"]=="BETWEEN")$Mg="\\s+AND\\s+$kh";return(bool)preg_match('~^'.$kh.$Mg.'$~i',$Nj);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$Nj);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$Nj);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Nj);return
true;}function
bracket_escape($s,$Ka=false){static$Gl=[':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5'];return
strtr($s,($Ka?array_flip($Gl):$Gl));}function
url_escape($Q){static$Gl=[];if(!$Gl){$Gl=[' '=>'+'];foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$eb)$Gl[$eb]=sprintf('%%%02X',ord($eb));for($q=0;$q<256;$q++){if($q<32||$q>126)$Gl[chr($q)]=sprintf('%%%02X',$q);}}return
strtr((string)$Q,$Gl);}function
min_version($zm,$eg="",$Db=null){$Db=connection($Db);$ek=$Db->server_info;if($eg&&preg_match('~([\d.]+)-MariaDB~',$ek,$_)){$ek=$_[1];$zm=$eg;}return$zm&&version_compare($ek,$zm)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($Fh,$Y){return(function_exists('ini_set')?\ini_set($Fh,$Y):false);}function
ini_bool($cf){$X=ini_get($cf);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($cf){$X=ini_get($cf);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($J,$Th){$hg=(int)ini_get("max_input_vars");return($hg?(int)floor(($hg-$Th)/$J):0);}function
max_input_vars_error(){$cf="max_input_vars";return
lang(1,"<b>$cf = ".ini_get($cf)."</b>");}function
sid(){static$I;if($I===null)$I=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$I;}function
set_password($ym,$N,$V,$E){if(is_string($E)&&!$_COOKIE["adminer_key"]){$bi=session_get_cookie_params();$_COOKIE["adminer_key"]=rand_string();cookie("adminer_key",$_COOKIE["adminer_key"],$bi["lifetime"]);}$_SESSION["pwds"][$ym][$N][$V]=(is_string($E)?[encrypt_string($E,$_COOKIE["adminer_key"])]:$E);}function
get_password(){$I=get_session("pwds");if(is_array($I))$I=($_COOKIE["adminer_key"]?decrypt_string($I[0],$_COOKIE["adminer_key"]):false);return$I;}function
get_val($G,$j=0,$Cb=null){$Cb=connection($Cb);$H=$Cb->query($G);if(!is_object($H))return
false;$J=$H->fetch_row();return($J?$J[$j]:false);}function
get_vals($G,$c=0){$I=[];$H=connection()->query($G);if(is_object($H)){while($J=$H->fetch_row())$I[]=$J[$c];}return$I;}function
get_key_vals($G,$Db=null,$ik=true){$Db=connection($Db);$I=[];$H=$Db->query($G);if(is_object($H)){while($J=$H->fetch_row()){if($ik)$I[$J[0]]=$J[1];else$I[]=$J[0];}}return$I;}function
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
get_url($im,$Ib){$gd=php_errors(function()use($im,$Ib,&$I,&$ye){$http_response_header=null;$I=file_get_contents($im,false,$Ib);$ye=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);});return[$I,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($ye,0,''),$_)?$_[1]:''),(array)$ye,($I===false?implode("\n",$gd):''),];}function
json_decode_exact($xf){$xf=str_replace(['\\\\','\\"'],['\\u005c','\\u0022'],$xf);$xf=preg_replace('~"(\\\\u0001[^"]*+")|"[^"]*+"(*SKIP)(*FAIL)~','"\\\\u0001$1',$xf);return
json_decode(preg_replace('~"[^"]*+"(*SKIP)(*FAIL)|-?\d[-+.\deE]*+~','"\\\\u0001$0"',$xf));}function
json_scalar($X){return(is_string($X)&&substr($X,0,1)=="\1"?substr($X,1):$X);}function
json_encode_exact($X,$Nd=0){$xf=str_replace(['\\\\','\\"'],["\2","\3"],json_encode($X,$Nd));$xf=preg_replace('~"\\\\u0001(-?\d[^"\\\\]*)"|(")\\\\u0001(\\\\u0001[^"]*+")|"[^"]*+"(*SKIP)(*FAIL)~','$1$2$3',$xf);return
str_replace(["\2","\3"],['\\\\','\\"'],$xf);}function
get_settings($Lb){parse_str($_COOKIE[$Lb],$jk);return$jk;}function
get_setting($v,$Lb="adminer_settings",$h=null){return
idx(get_settings($Lb),$v,$h);}function
save_settings(array$jk,$Lb="adminer_settings"){$Y=http_build_query($jk+get_settings($Lb));cookie($Lb,$Y);$_COOKIE[$Lb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&session_status()==PHP_SESSION_NONE)session_start();}function
stop_session($Qd=false){$lm=ini_bool("session.use_cookies");if(!$lm||$Qd){session_write_close();if($lm&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($v){return$_SESSION[$v][DRIVER][SERVER][$_GET["username"]];}function
set_session($v,$X){$_SESSION[$v][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($ym,$N,$V,$g=null){$hm=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($g!==null?"db|":"").($ym=='mssql'||$ym=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$hm,$_);return"$_[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($ym!="server"||$N!=""?url_escape($ym)."=".url_escape($N)."&":"")."username=".url_escape($V).($g!=""?"&db=".url_escape($g):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($z,$xg=null,$P=303){if($xg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($z!==null?$z:$_SERVER["REQUEST_URI"]))][]=$xg;}if($z!==null){if($z=="")$z=".";header("Location: $z",true,$P);exit;}}function
query_redirect($G,$z,$xg,$ej=true,$od=true,$zd=false,$ul=""){if($od){$Ek=microtime(true);$zd=!connection()->query($G);$ul=format_time($Ek);}$wk=($G?adminer()->messageQuery($G,$ul,$zd):"");if($zd){adminer()->error
.=adminer()->error().$wk.script("messagesPrint();")."<br>";return
false;}if($ej)redirect($z,$xg.$wk);return
true;}class
Queries{static$queries=[];static$start=0;}function
remember_query($G){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$G:(preg_match('~;$~',$G)?"DELIMITER ;;\n$G;\nDELIMITER ":$G).";");}function
queries($G){remember_query($G);return
connection()->query($G);}function
apply_queries($G,array$T,$id='Adminer\table'){foreach($T
as$R){if(!queries("$G ".$id($R)))return
false;}return
true;}function
queries_redirect($z,$xg,$ej){$Wi=implode("\n",Queries::$queries);$ul=format_time(Queries::$start);return
query_redirect($Wi,$z,$xg,$ej,false,!$ej,$ul);}function
format_time($Ek){return
lang(2,max(0,microtime(true)-$Ek));}function
relative_uri($hm=''){return
preg_replace_callback('~^[^?]*~',function($_){return
str_replace(":","%3A",$_[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($hm?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Zh=""){return
substr(preg_replace("~(?<=[?&])($Zh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($A,$gc=false){$Gd=$_FILES[$A];if(!$Gd)return
null;foreach($Gd
as$v=>$X)$Gd[$v]=(array)$X;$I=[];foreach($Gd["error"]as$v=>$i){if($i)return$i;$l=$Gd["name"][$v];$Bl=$Gd["tmp_name"][$v];$Gb=file_get_contents($gc&&preg_match('~\.gz$~',$l)?"compress.zlib://$Bl":$Bl);if($gc){$Ek=substr($Gb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Ek))$Gb=iconv("utf-16","utf-8",$Gb);elseif($Ek=="\xEF\xBB\xBF")$Gb=substr($Gb,3);}$I[]=[$l,$Gb];}return$I;}function
get_file($v,$gc=false,$oc=""){$Jd=get_files($v,$gc);if(!is_array($Jd))return$Jd;$I='';foreach($Jd
as$Gd){$Gb=$Gd[1];$I
.=$Gb;if($oc)$I
.=(preg_match("($oc\\s*\$)",$Gb)?"":$oc)."\n\n";}return$I;}function
upload_error($i){$pg=($i==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($i?lang(3).($pg?" ".lang(4,$pg):""):lang(5));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(6),$_);$nk=strlen($_[3]);$I=number_format($X,0,".","");$I=preg_replace('~\B(?=(\d{'.(strlen($_[2])?:$nk).'})*\d{'.$nk.'}$)~',$_[1],$I);return
strtr($I,preg_split('~~u',lang(7),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$v){$X=idx($S,$v,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$ya=($v=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));$X=in_array($v,["Data_length","Index_length","Data_free"])?adminer()->formatSizeValue($X):format_number($X);return($ya?"~ ":"").$X;}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$_d=false){$I=table_status($R,$_d);return($I?reset($I):["Name"=>$R]);}function
column_foreign_keys($R){$I=[];foreach(adminer()->foreignKeys($R)as$m){foreach($m["source"]as$X)$I[$X][]=$m;}return$I;}function
fields_from_edit(){$I=[];foreach((array)$_POST["field_keys"]as$v=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$v];$_POST["fields"][$X]=$_POST["field_vals"][$v];}}foreach((array)$_POST["fields"]as$v=>$X){$A=bracket_escape($v,true);$I[$A]=["field"=>$A,"full_type"=>"","type"=>"","privileges"=>["insert"=>1,"update"=>1,"where"=>1,"order"=>1],"null"=>true,"auto_increment"=>($A==driver()->primary),];}return$I;}function
dump_headers($Le,$Pg=false){$I=adminer()->dumpHeaders($Le,$Pg);$Vh=$_POST["output"];if($Vh!="text"||$I=="tar"){$Ab=($Vh!="text"&&$Vh!="file"&&preg_match('~^[0-9a-z]+$~',$Vh)?".$Vh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Le).".$I$Ab");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$I;}function
dump_csv(array$J){$Pl=$_POST["format"]=="tsv";foreach($J
as$v=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Pl?'\t':'[,;]|^$').'~',$X))$J[$v]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($Pl?"\t":";")),$J)."\r\n";}function
parse_csv($Ub,$Yj){$I=[];preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Ub,$fg);foreach($fg[0]as$J){preg_match_all("~((?>\"[^\"]*\")+|[^$Yj]*)$Yj~",$J.$Yj,$gg);$I[]=$gg[1];}return$I;}function
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
select_value($X,$y,array$j,$sl,array$ti=[]){if(is_array($X)){$I="";if(array_filter($X,'is_array')==array_values($X)){$Bf=[];foreach($X
as$W)$Bf+=array_fill_keys(array_keys($W),null);foreach(array_keys($Bf)as$_f)$I
.="<th>".h($_f);foreach($X
as$W){$I
.="<tr>";foreach(array_merge($Bf,$W)as$sm)$I
.="<td>".select_value($sm,$y,$j,$sl,$ti);}}else{foreach($X
as$_f=>$W)$I
.="<tr>".($X!=array_values($X)?"<th>".h($_f):"")."<td>".select_value($W,$y,$j,$sl,$ti);}return"<table>$I</table>";}if(!$y)$y=adminer()->selectLink($X,$j);if($y===null){if(is_mail($X))$y="mailto:$X";if(is_url($X))$y=$X;}$X=driver()->value($X,$j);$I=adminer()->editVal($X,$j);if($I!==null){if(!is_utf8($I))$I="\0";elseif($sl!=""&&is_shortable($j))$I=shorten_utf8($I,max(0,+$sl),"",$ti);else$I=highlight_matches($I,$ti);}return
adminer()->selectVal($I,$y,$j,$X);}function
is_blob(array$j){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$j["type"])&&!in_array($j["type"],idx(driver()->structuredTypes(),lang(0),[]));}function
is_identity_always(array$j){return$j["auto_increment"]&&(JUSH=="mssql"||$j["default"]=="GENERATED ALWAYS AS IDENTITY");}function
is_mail($Tc){$Da='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$Fc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$si="$Da+(\\.$Da+)*@($Fc?\\.)+$Fc";return
is_string($Tc)&&preg_match("(^$si(,\\s*$si)*\$)i",$Tc);}function
is_url($Q){$Fc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($Fc?\\.)+$Fc(:\\d+)?(/\\S*)?(\\?\\S*)?(#\\S*)?\$~iD",$Q);}function
is_ipv6($ka){$p='[\da-f]{1,4}';$of='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($p:){7}$p|($p:){6}$of|(($p:)*$p)?::(($p:)*($p|$of))?)$~iD",$ka);}function
is_shortable(array$j){return!preg_match('~'.number_type().'|date|time|year~',$j["type"]);}function
url_host($He){return(strpos($He,":")!==false?"[$He]":$He);}function
server_parts(array$mi){return["scheme"=>(string)$mi["scheme"],"host"=>(string)$mi["host"],"port"=>(string)$mi["port"],"socket"=>(string)$mi["socket"],"path"=>(string)$mi["path"],];}function
parse_server($N){if($N=="")return
server_parts([]);if($N[0]==":"&&!is_ipv6($N)){$rj=substr($N,1);if(preg_match('~^\d+$~D',$rj))return
server_parts(["port"=>$rj]);return(preg_match('~^/[-\w.:/]*$~D',$rj)?server_parts(["socket"=>$rj]):null);}$Lj="";if(preg_match('~^([-+.\w]+)://~',$N,$_)){$Lj=strtolower($_[1]);$N=substr($N,strlen($_[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$N,$_))return(is_ipv6($_[1])?server_parts(["scheme"=>$Lj,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4]]):null);if(is_ipv6($N))return
server_parts(["scheme"=>$Lj,"host"=>$N]);if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$N,$_))return
server_parts(["scheme"=>$Lj,"host"=>$_[1],"port"=>$_[3]]);return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$N,$_)?server_parts(["scheme"=>$Lj,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4]]):null);}function
count_rows($R,array$Z,$qf,array$p){$G=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($qf&&(JUSH=="sql"||count($p)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$p).")$G":"SELECT COUNT(*)".($qf?" FROM (SELECT 1$G GROUP BY ".implode(", ",$p).") x":$G));}function
slow_query($G){$ok=driver()->slowQuery($G,adminer()->queryTimeout());ob_flush();flush();return@get_key_vals(($ok?:$G),null,false);}function
get_token(){$bj=rand_string();return
bin2hex($bj^"$_SESSION[token]").":$bj";}function
verify_token(){list($Cl,$bj)=explode(":",$_POST["token"]);$Pj=$_SESSION["token"];return
is_string($Pj)&&strlen($bj)==strlen($Pj)&&bin2hex($bj^$Pj)===$Cl&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],["","same-origin"]);}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$uc=""){$ua=array_flip(str_split(compress_alphabet()));$w=strlen($Q);$um=($w?13*($w-1)/2-$ua[$Q[0]]:0);$Ra="";$rj=0;$sj=0;for($q=1;$q<$w;$q+=2){$rj=($rj<<13)+$ua[$Q[$q]]*93+$ua[$Q[$q+1]];$sj+=13;while($sj>=8&&$um>=8){$sj-=8;$um-=8;$Ra
.=chr($rj>>$sj);$rj&=(1<<$sj)-1;}}if($Ra=="")return"";if($uc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,['dictionary'=>$uc]),$Ra,ZLIB_FINISH);return($uc==""&&function_exists('gzinflate')?gzinflate($Ra):inflate($Ra,$uc));}function
inflate($Ra,$uc=""){$Qf=[3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258];$Rf=[0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0];$yc=[1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577];$_c=[0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13];$I=$uc;$F=0;do{$Ld=inflate_bits($Ra,$F,1);$U=inflate_bits($Ra,$F,2);if(!$U){$F=($F+7)&~7;$w=inflate_bits($Ra,$F,16);$F+=16;$I
.=substr($Ra,$F>>3,$w);$F+=$w<<3;}else{if($U==1){$Zf=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Ac=array_fill(0,30,5);}else{$Yf=inflate_bits($Ra,$F,5)+257;$zc=inflate_bits($Ra,$F,5)+1;$C=[16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15];$Dg=array_fill(0,19,0);$Cg=inflate_bits($Ra,$F,4)+4;for($q=0;$q<$Cg;$q++)$Dg[$C[$q]]=inflate_bits($Ra,$F,3);$Eg=inflate_table($Dg);$Sf=[];while(count($Sf)<$Yf+$zc){$Tk=inflate_symbol($Ra,$F,$Eg);if($Tk==16)$Sf=array_merge($Sf,array_fill(0,inflate_bits($Ra,$F,2)+3,end($Sf)));elseif($Tk==17)$Sf=array_merge($Sf,array_fill(0,inflate_bits($Ra,$F,3)+3,0));elseif($Tk==18)$Sf=array_merge($Sf,array_fill(0,inflate_bits($Ra,$F,7)+11,0));else$Sf[]=$Tk;}$Zf=array_slice($Sf,0,$Yf);$Ac=array_slice($Sf,$Yf);}$ag=inflate_table($Zf);$Cc=inflate_table($Ac);while(($Tk=inflate_symbol($Ra,$F,$ag))!=256){if($Tk<256)$I
.=chr($Tk);else{$w=$Qf[$Tk-257]+inflate_bits($Ra,$F,$Rf[$Tk-257]);$Bc=inflate_symbol($Ra,$F,$Cc);$rh=strlen($I)-$yc[$Bc]-inflate_bits($Ra,$F,$_c[$Bc]);for($q=0;$q<$w;$q++)$I
.=$I[$rh+$q];}}}}while(!$Ld);return($uc==""?$I:substr($I,strlen($uc)));}function
inflate_bits($Ra,&$F,$Nb){$I=0;for($q=0;$q<$Nb;$q++){$I+=((ord($Ra[$F>>3])>>($F&7))&1)<<$q;$F++;}return$I;}function
inflate_table(array$Sf){$R=[];$qb=0;for($Sa=1;$Sa<=max($Sf);$Sa++){foreach($Sf
as$Tk=>$w){if($w==$Sa){$R[$Sa][$qb]=$Tk;$qb++;}}$qb<<=1;}return$R;}function
inflate_symbol($Ra,&$F,array$R){$qb=0;$Sa=0;do{$qb=($qb<<1)+inflate_bits($Ra,$F,1);$Sa++;}while(!isset($R[$Sa][$qb]));return$R[$Sa][$qb];}function
script($tk,$Fl="\n"){return"<script".nonce().">$tk</script>$Fl";}function
script_src($im,$kc=false){return"<script src='".h($im)."'".nonce().($kc?" defer":"")."></script>\n";}function
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
checkbox($A,$Y,$jb,$Df="",$b="",$pb="",$Ff=""){$I="<input type='checkbox' name='".h($A)."' value='".h($Y)."'".($jb?" checked":"").($Df==""&&$pb?" class='$pb'":"").($Ff?" aria-labelledby='$Ff'":"").$b.">";return($Df!=""?"<label".($pb?" class='$pb'":"").">$I".h($Df)."</label>":$I);}function
optionlist($B,$Vj=null,$mm=false){$I="";foreach($B
as$_f=>$W){$Ih=[$_f=>$W];if(is_array($W)){$I
.='<optgroup label="'.h($_f).'">';$Ih=$W;}foreach($Ih
as$v=>$X)$I
.='<option'.($mm||is_string($v)?' value="'.h($v).'"':'').($Vj!==null&&($mm||is_string($v)?(string)$v:$X)===$Vj?' selected':'').'>'.h($X);if(is_array($W))$I
.='</optgroup>';}return$I;}function
group_system(array$Xg,$Kj=false){$I=[];$Uk=[];foreach($Xg
as$A){if($Kj?driver()->isSystem(DB,$A):driver()->isSystem($A))$Uk[]=$A;else$I[]=$A;}if($Uk)$I[lang(8,'')]=$Uk;return$I;}function
html_select($A,array$B,$Y="",$b="",$Ff=""){static$Df=0;$Ef="";if(!$Ff&&substr($B[""],0,1)=="("){$Df++;$Ff="label-$Df";$Ef="<option value='' id='$Ff'>".h($B[""]);unset($B[""]);}return"<select name='".h($A)."'".($Ff?" aria-labelledby='$Ff'":"")."$b>".$Ef.optionlist($B,$Y)."</select>";}function
html_radios($A,array$B,$Y="",$Yj=""){$I="";foreach($B
as$v=>$X)$I
.="<label><input type='radio' name='".h($A)."' value='".h($v)."'".($v==$Y?" checked":"").">".h($X)."</label>$Yj";return$I;}function
confirm($xg=""){return
on('click','confirmClick',$xg?:lang(9));}function
print_fieldset($r,$Pf,$Bm=false){echo"<fieldset><legend>","<a href='#fieldset-$r' class='toggle'>$Pf</a>","</legend>","<div id='fieldset-$r'".($Bm?"":" class='hidden'").">\n";}function
bold($Ta,$pb=""){return($Ta?" class='active $pb'":($pb?" class='$pb'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($D){return
remove_from_uri("page|next").($D?"&page=$D".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($D,$Vb){return" ".($D==$Vb?($D?"<b>".($D+1)."</b>":$D+1):'<a href="'.h(pagination_href($D)).'">'.($D+1)."</a>");}function
hidden_fields(array$Ri,array$Pe=[],$Ii=''){$I=false;foreach($Ri
as$v=>$X){if(!in_array($v,$Pe)){if(is_array($X))hidden_fields($X,[],$v);else{$I=true;echo
input_hidden(($Ii?$Ii."[$v]":$v),$X);}}}return$I;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$gm){$gm=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($gm?on('submit','uploadProgress',ME."upload=$gm",SESSION_NAME."=$gm"):"");}function
file_input($b,$rj=""){$jg="max_file_uploads";$kg=ini_get($jg);$pg="upload_max_filesize";$qg=ini_bytes($pg);$Ei=ini_bytes("post_max_size");if($Ei&&$Ei<$qg){$pg="post_max_size";$qg=$Ei;}$rg=ini_get($pg);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$kg,lang(10,"$jg = $kg"),$qg,lang(10,"$pg = $rg")).">$rj":lang(11));}function
enum_input($U,$b,array$j,$Y,$Wc=""){preg_match_all("~".driver()->enumLength."~",$j["length"],$fg);$Ii=($j["type"]=="enum"?"val-":"");$jb=(is_array($Y)?in_array("null",$Y):$Y===null);$I=($j["null"]&&$Ii?"<label><input type='$U'$b value='null'".($jb?" checked":"")."><i>$Wc</i></label>":"");foreach($fg[0]as$X){$X=stripcslashes(idf_unescape($X));$jb=(is_array($Y)?in_array($Ii.$X,$Y):$Y===$X);$I
.=" <label><input type='$U'$b value='".h($Ii.$X)."'".($jb?' checked':'').'>'.h(adminer()->editVal($X,$j)).'</label>';}return$I;}function
input(array$j,$Y,$o,$Ia=false,$dm=false){$A=h(bracket_escape($j["field"]));echo"<td class='function'>";$dd=driver()->enumLength($j);if($dd){$j["type"]="enum";$j["length"]=$dd;}$B=($j["type"]=="enum"||$j["type"]=="set");if(is_array($Y)&&!$o&&!$B)$o="json";$xf=($o=="json"||preg_match('~^jsonb?$~',$j["full_type"]));if($xf&&$Y!=''&&(JUSH!="pgsql"||$j["type"]!="json")&&(is_array($Y)||!$_POST["save"])){$Nd=JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE;$Y=(is_array($Y)?json_encode($Y,$Nd):json_encode_exact(json_decode_exact($Y),$Nd));}$qj=($dm&&is_identity_always($j));if($qj&&!$_POST["save"])$o=null;$de=(isset($_GET["select"])||$qj?["orig"=>lang(12)]:[])+adminer()->editFunctions($j);$b=" name='fields[$A]".($B?"[]":"")."'".($Ia?" autofocus":"");echo
driver()->unconvertFunction($j)." ";$R=$_GET["edit"]?:$_GET["select"];if($j["type"]=="enum")echo
h($de[""])."<td>".adminer()->editInput($R,$j,$b,$Y);else{$se=(in_array($o,$de)||isset($de[$o]));$Md=0;foreach($de
as$v=>$X){if($v===""||!$X)break;$Md++;}echo(count($de)>1?"<select name='function[$A]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($de,$o===null||$se?$o:"")."</select>":h(reset($de)))."<td".($Md&&count($de)>1?on('input','skipOriginal',$Md):"").">";$ef=adminer()->editInput($R,$j,$b,$Y);if($ef!="")echo$ef;elseif(preg_match('~bool~',$j["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($j["type"]=="set")echo
enum_input("checkbox",$b,$j,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($j)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$A'>";elseif($xf)echo"<textarea$b cols='50' rows='12' class='jush-json'>\n".h($Y).'</textarea>';elseif(($rl=preg_match('~text|lob|memo~i',$j["type"]))||preg_match("~\n~",$Y)){if($rl&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$K=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$K'";}echo"<textarea$b>\n".h($Y).'</textarea>';}else{$Tl=driver()->types();$Rl=$Tl[$j["type"]];$Aa=preg_match('~\[]~',$j["full_type"]);if($Aa)$sg=0;elseif(preg_match('~date|time|year~',$j["type"])){$Hi=($j["length"]==""&&JUSH=="pgsql"?6:$j["length"]);$Xd=(preg_match('~time~',$j["type"])&&preg_match('~^[1-9]\d*$~',$Hi)?$Hi+1:0);$sg=($Rl?$Rl+$Xd:0);}elseif(!preg_match('~int|vector~',$j["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$j["length"],$_))$sg=(preg_match("~binary~",$j["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$j["unsigned"]?1:0);else$sg=($Rl?$Rl+($j["unsigned"]?0:1):0);echo"<input".((!$se||$o==="")&&preg_match('~^'.int_type().'$~',$j["type"])&&!$Aa?" type='number'":"")." value='".h($Y)."'".($sg?" data-maxlength='$sg'":"").(preg_match('~char|binary~',$j["type"])&&$sg>20?" size='".($sg>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$j,$Y),(count($de)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$j){$s=bracket_escape($j["field"]);$o=idx($_POST["function"],$s);if($o=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?idf_escape($j["field"]):false);if($o=="NULL")return"NULL";if(is_blob($j)&&ini_bool("file_uploads")){$Gd=get_file("fields-$s");if(!is_string($Gd))return
false;return(JUSH=="sql"?"_binary".q($Gd):driver()->quoteBinary($Gd));}$Y=idx($_POST["fields"],$s);if($Y===null){if(!isset($_FILES["fields"]["name"][$s]))return
false;$Y="";}if($j["type"]=="enum"||driver()->enumLength($j)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($j["auto_increment"]&&$Y=="")return
null;if($j["type"]=="set")$Y=implode(",",(array)$Y);if($o=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($j,$Y,$o);}function
jump_input(){$Gf=['select'=>lang(13),'insert'=>lang(14),'search'=>(support("table")?lang(15):''),'schema'=>lang(16),'database'=>lang(17),];$I="<span id='jump'><input type='search' form='jump-form' name='filter' value='".h($_GET["filter"])."'"." placeholder='".lang(18)."' aria-label='".lang(18)."' autocomplete='off' autocapitalize='off'"." role='combobox' aria-expanded='false' aria-autocomplete='list' aria-controls='jump-list'".on('input','jumpInput').on('keydown','jumpKeydown');foreach($Gf
as$v=>$X)$I
.=($X!=""?" data-$v='".h($X)."'":"");return"$I><input type='submit' form='jump-form' value='".lang(18)."' class='hidden'></span> ";}function
search_tables($G,$_h=""){$_GET["where"]=[["op"=>$_h,"val"=>$G]];$Xj="<ul>\n";foreach(table_status('',true)as$R=>$S){$A=adminer()->tableName($S);if(isset($S["Engine"])&&$A!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$H=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),[],$S)),1));if(!$H||$H->fetch_row()){$Ni="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$A</a>";echo"$Xj<li>".($H?$Ni:"<p class='error'>$Ni: ".adminer()->error())."\n";$Xj="";}}}echo($Xj?"<p class='message'>".lang(19):"</ul>")."\n";}function
on_help($rl,$mk=0){return
on('mouseover','helpMouseover',$rl,$mk).on('mouseout','helpMouseout');}function
on_help_value($lj="",$pj=""){return
on('mouseover','helpValueMouseover',$lj,$pj).on('mouseout','helpMouseout');}function
edit_form($R,array$k,$J,$dm,$i='',$G='',$ul=''){$al=adminer()->tableName(table_status1($R,true));page_header(($dm?lang(20):lang(21)),$i,["select"=>[$R,$al]],$al);adminer()->editRowPrint($R,$k,$J,$dm,$G,$ul);if($J===false){echo"<p class='error'>".lang(22)."\n";return;}echo"<form method='post' enctype='multipart/form-data' id='form'>\n";$Rc=false;$Hm=($dm&&!isset($_GET["select"])?where_columns($k):[]);$Jb=(count($Hm)!=count($k));if(!$Jb)$Hm=[];if(!$k)echo"<p class='error'>".lang(23)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ia=!$_POST;foreach($k
as$A=>$j){echo"<tr".($Hm[$A]?on('change','whereChange'):"")."><th>".adminer()->fieldName($j);$h=idx($_GET["set"],bracket_escape($A));if($h===null){$h=$j["default"];if($j["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$h,$nj))$h=$nj[1];if(JUSH=="sql"&&preg_match('~binary~',$j["type"]))$h=bin2hex($h);}$Y=($J!==null?($j["type"]=="set"&&is_array($J[$A])?implode(",",$J[$A]):(is_bool($J[$A])?+$J[$A]:$J[$A])):(!$dm&&$j["auto_increment"]?"":(isset($_GET["select"])?false:$h)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$j);if(($dm&&!isset($j["privileges"]["update"]))||$j["generated"])echo"<td class='function'><td>".select_value($Y,'',$j,null);else{$Rc=true;$o=($_POST["save"]?idx($_POST["function"],bracket_escape($A),""):($dm&&preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$dm&&$Y==$j["default"]&&($j["default_sql"]||preg_match('~^[\w.]+\(~',$Y)))$o="SQL";if(preg_match("~time~",$j["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$o=(in_array("now",adminer()->editFunctions($j))?"now":"current_timestamp");}if(preg_match("~date~",$j["type"])&&preg_match('~^CURRENT_DATE~i',$Y)){$Y="";$o=(in_array("now",adminer()->editFunctions($j))?"now":"current_date");}if($j["type"]=="uuid"&&$Y=="uuid()"){$Y="";$o="uuid";}if($Ia!==false)$Ia=($j["auto_increment"]||$o=="now"||$o=="uuid"?null:true);input($j,$Y,$o,$Ia,$dm);if($Ia)$Ia=false;}}if(driver()->primary!=""&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(["null"=>isset($_GET["select"])]))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Rc){echo"<input type='submit' value='".lang(24)."'>\n";if(!isset($_GET["select"])&&$Jb){$vc=($Hm&&($i!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($dm?lang(25):lang(26))."' title='Ctrl+Shift+Enter'$vc".($dm?on('click','ajaxForm',lang(27)):"").">\n";}}echo($dm?"<input type='submit' name='delete' value='".lang(28)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(["check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]]);echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($si,$w){return
str_repeat("$si{0,65535}",$w/65535)."$si{0,".($w%65535)."}";}function
shorten_utf8($Q,$w=80,$Ok="",array$ti=[]){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$w).")($)?)u",$Q,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$w).")($)?)",$Q,$_);$w=strlen(isset($_[2])?$_[1]:preg_replace('~\n[^\n]*\z~',"\n",$_[1]));return
highlight_matches($Q,$ti,$w).$Ok.(isset($_[2])?"":"<i>…</i>");}function
highlight_matches($Q,array$ti,$w=null){if($w===null)$w=strlen($Q);$I="";$F=0;if($ti&&@preg_match_all("((?|".implode("|",$ti)."))su",$Q,$fg,PREG_OFFSET_CAPTURE)){foreach($fg[0]as$_){list($rl,$Ek)=$_;if($rl!=""&&$Ek<$w){$Yc=min($Ek+strlen($rl),$w);$I
.=h(substr($Q,$F,$Ek-$F))."<mark>".h(substr($Q,$Ek,$Yc-$Ek))."</mark>";$F=$Yc;}}}return$I.h(substr($Q,$F,$w-$F));}function
icon($Ke,$A,$Je,$xl,$b=""){return"<button ".($A?"type='submit' name='$A'":"draggable='true' tabindex='-1'")." title='".h($xl)."' class='icon icon-$Ke".($A?"":" jsonly")."'$b><span>$Je</span></button>";}function
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
#,<5F60=.DmmVj*!6Y,vLqPc2443S*-I32v==XWfqK[TF2if,l72YBe~P:GUq|
X/rlx#"KguG#c7PC`orer$85!$5Tj8oa_"6XJgGlS7}9bUBF
:+9e"$IUt8G/YUY$<R%(D.F*RHQ:R>7?7T
:;`qB5t:65dN2oNk|;n3M^iUQN&@e-?9zZ?.b`%JIj8_J)
`cMhjz
][wofYiC
gdGrH?eQrkjyw-N|Be##pH#<Lc>6W5y#!tpHi439wyIApL)uX2<QNy,BPQ4}75%o0I.;0i;.J%(pu9KQ^{&h)*yu9S-q@n7`rr$s$qf78+R|D,B"/R"Sd})z=(Ed>Q,IpF&r#&g"P}1G<Cq&*"[f?fU85I0e::"@.fa;1W7q(QoXN">i)rx,jnB>]4-}`5EFEN4C2Z&IW"dxGF(]u[$EwudHf#X>!zsbDN31mN7/?E[{/U:1;Zw/e{YysExxYZM6eH,KIC1x72,.aQ"rHSs99nJ%d
Nf7^n6N:mxZXNKlGB4T*hRa*>nL-ZO^pl/$W=|^uQr`Pd"B;
5Ztg*$}E[pbUg5NY@=se`iV;,T0NVF7p,3`HFXRCH"j%tYQJFf~)h-uKw4BIUbI,&<}AFHP4ApY.r;;
0m?fQ4<mpL)wya|rpAEHX6"EK.xg.u:X]QZ:I>Q:_e*oVAtEIG8Tw]6#ura,4q,yjoUK{2x2Q<s8V!8YX_QZgJJVf!wdV]qWh?6^^48=msu1g(B%jO@Dv/krFZ<gQ(h$6BBa%/]=1Os^zfTZMn684",4rZmBm6f-)1Q!p%D6-azWWSqrT0PgSB90R<.2nK~x
@oln1hq7O+Aq%HS^!j31w6P.*!P1Fuez2Hg2F7[wTUAMI[!7daU^NVgGj,(*sud9I%PQ:?as)ZeRjm"7P!3F7)7cfrnHqB]TUQn&*~?yBd/f*H:J]~6,#9nu3646kl/VY2-F"pVVvb/4Lp@yY>^%1i#[
R#?q]:E,DDTcKxEsah{FO5ql6##D.^U
9lP7cB+#dtG9=_ce&gubW4L;gi_v{,[Bu/ze2=6C3f0v;.gnQ+=Cqy>Wu7aS3m`=/9eotIE"WigNR6W[xiMjqP#7g)xry3KMzUQILw~5o"/"fVz]8&i4~"H@}!v^cIlk>nf(j00GV!c%w0u3N[XQ
4FCk-&%,qxg03ekJnCA/fJ*AGxq_6O8@)];9={1=b&UTTX$uBrk%#QoM:r&huE`/FzQL%NbKRzbMiAQN1Ty)!IWWFXDRh#D$91F4
aj>R#u`<W4t1Gle.GCy3hYOE$`:H]m4pBN@#9c?>~nU
@7-Ts2bRIB}*[NjBCVhMH5h.+HLiA,7hUq{H!:=di6gt-W.qJ_sHC5xy5P%LOiv>bQJ<Z?HnK;:0#8c.~brLL`#,5i"u]n~o<#llm)#vxUI2%yZZJhA<;wp.UB3bs3?6)>2ZI%qQndQ
T]5gzW2-n?gm@"p)H<w^@Fw?@2[KO<H@NPr9x6J2WkpFe#_:`sS;
GXbLGn+r)`Eo4ey<#;J%XTCwn0,>=p[>U_a"J;kW/pDY/_x_#)w-v7s`C$?Nlw!-H=ZqKs
`:vt@c?@J.4:qc5ZZgXGjTRuV45fbr?[%$Hvf$MkWC>ULwvjWX1Z9?~XGV,[XDs?o?GgmHN0_X
L^]0=@$D;[IS8u/J_#n,0"_GG[WS3rWgE=3dmk`L<$?
THTnl7Rf#X9pU5%{6FM8t
I"gBZqYQyz!f`0z#-o%5vUX*dsG.A]a_0M%"K9)Qw#e
mHtqBU79kPRdi|e-EZp{H|_&7)/
N_I.m5%,Z0o/OGXvnRm#[x6ekMvnjMM3UUP!TBn1O![*hz>"Kr1]&D
W
Z5$v%dUWG=7$;-<ECt^3+%DW=XK:
1"K0qn_2.@)LiPG_n[^PVQqCWQ`==P0+jkvK@1KknKh.ZwoeA6
~eznxnCbm?Mwo.#,*,qM37xV/pIbYBwnSQXS>oWa5xkV+W
xb92pkfK,yb9[PvzK|@1rjy2R(.Y;"x"^5:&T*TqJDr%=@qh_:UzXhcD!0WGGXZ;yEhiFKUTJ*Kk8#wWEi"Mc@aVQY;h8#n~=O$i2A6b^-g0Z?)E$qWnlMSRpkmM(mVM`cp&LSsrxAssM"o(_.&{uj1Xcp2GCsT},D0p+JVt$a2<WI
fmg7|`7/[LodN6i,FG
Q_??cJuY,CbYEVVtJx:2vlAyr)N!P)bs2PMq/jUtW.SQ5z$axfc5c)RwY&R"IqTc+FbU55Jy>*y>j
qeskK>9ywq93J2i_3{+-9H6gB(dNv:C0"!b`d2,:^XdZ[jbQ(O%~EIXlv<xqlWr2DgXucm1h/pDKu_2]8c(m!>O;3g7EaWUfEM;VJGF~86X%DB>~Kmc16H]prlDLk?X##s*OJpy/^M2(Ft=+=]M~rEWLI/(Ed`d-,gNPb3UZ#r[_MiN@I0qisyAblwd1>p(*@od#I^OxQMysaxAt^Pw2;txV)Vh}oeAll&+33dRQBmh/vH(B@rt.Lo!(^Tct`z?^C~9Q&xn2/KRZ"rh+?]s,QS>XrDPIDS)Pp?5U!K1]qjZ_7tadl)(w-8F~->nbf>=;
T*5>HOv^S*De*8<5mnd9QO})f+}L(vmCc7r2=r[wa[~;NWpr<ZYgB"O[n_H!b<Fx_N6Y^nT
1D++zt@cqt%Fpi8ok7zmFDkhpZGATQv]O##sDJ>P|&)lX6(1p5iU]AbU25gPwQ9hCX$xu7sVPdrG5@|CG*]*d7fsUy|tXV3Z<o_QFu#l=vfIBEIl,MWlNkGc%kS1NI$!>tP`r)/jwQJ0Mmz+t$uy#lFKoS>fV88!HKxp+=qR<(6ZyH>m|pRmAw,a&Z"aE6pVz/fW0DEr0qr$$sEB1i_k15[L9S>/iAS]NV-74b>Xd+.gv>>0qF6Yx6[g2_A9b;fX5Hm,Jojgkw0"rWp9iC`l<1h+z
QY"cvdCodF)?W,7v|ce>a,9:1y&E*f*$SqJE&Pm?nZQ%D#xo8SXyNj#ke]eN}tE3|AyB9K-+DA><h1(jZps!qH-wO+@ABI?jd/*<J_B,@<HcsUx56fADck+,(Ft.`h
0J1^GL;Yws*)pU`J
Y^N&##tKUjZ9}WYO~q-a*4=misg7^qV_sdlZ{,I_Kj]urbQktoM^6`BH@LttF!>Rtl)FqHveX!0T^[#A7nsg58]a:;>Iiiu+{[OQG5z9Wy_-{!3jE4(.Jl"yh/N>6bi1`xO0dK00KndG4-+IOC9-i
MtAK{J_F]^-[_K5f}*#W8s">W:?;y3u8+SZ65SeUq:K8>5EuOf6,LB,AZ/TF.7wF%E8refF-oEV/x]Uu%,[9MaYdbpKG_;BeQUW?Gd4Z:Qljb*A00N`(zi:9#0M/0sj2rAKk0W*/I$q7+RSe{:"yrN0S8:{YiJ!5KuSIqBcB(_f$YnJAoqCJHy@s#%m(6UjP3oO;K$?WZN;.eN@"suLeyu7Om:3I>rG/!Bvka;56`hT*ktV*xWXZ<H$eqt$j`ovPRn,.pNza>1#/Du5#nhVUr2h8dG-TUlGF(7[Y/TuNOA,^g4edCldg.MkqwVfB%]WD~e#+h#rwG"md(2yx<,+rk*TmtNIjfE8Lu)oyywJ.BR(b#Xdc?gTHmG":|eN@RD+AX]HhoQ"q[n#vLpg?jy>`s<Wcl.d3>PALYJ[tSZXhc?|?N=S^J0^RlZf8K:f?mFSc"n0lm`w>DN@;:rG0=["rzTT(Opk?6O}HNEYcgTP@hR=>TsK;<f]Te*nCqKRpTo2VpIOSI!+6W>k)7$5v%:#vXS^=M(qJYFR+j82YKu5YaZI*{Xz1IB^:M@=IHv
yl#HhmP|9;sbiw[b+z&^Jcx!C"(|$PL:y5eIHy(GVXQjsRN+z"WT;ln$,`qmvT
G"r@)>o7}#E');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
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
decompress_string('$hk^CG>pG.%w`iG1}uQ4(NfaX/T]3_TC1_RYKAvNjWY"uj0SU(eqB0r9K_*MAkzy1c^
%Ih<wK|iCJ%[bc<K!rgB-^KZ]tOh1Ufi(cHZh$7"goUVmr?>Q366?F+$F.{G_bqXjg/h~?KKR`)bYG`p7QBc()QbAXqUyAnt0ve1[np
p]Ya2l=/)/<sIWU33DgWzMfjlTgb@Tw<..zTtWGy(bsZ@[rLv9=mvlo!!E<2GohM~7gnuWt[!ly#ntp-2V!p%A;u:c~;?@?8RaBe|In?T&#
Te+6_0&Q?A*d#Iq@xiMLSLiW-q-pW2%+m=TyVM0J,S
:`2uN:/z/i1]B%z)TgwIv3v2@PKuj2=1
nIsCYQT$`>#DS$lpQy@C~h:,hvUXi`cwpQ?XMpXNkTnINHI3h@-rMa+.m"`cXJ([EDJ_w(<3"J8gq;ZYe:nPclub32a?rG7@o%GF9e
#qy8d@Oxn3lL/f2*4k)*aS!=xqNo)sWjs]N+%q#73,@I7jF28^Rgf&v"8Fgt1VR^[vAhpKK<7@EyY
#w21vPEdqW4003tUobvWneu3Eqf&%JrXNaMR*)UBjf$3Fl#O4:/BGWa-m/EjpM1v1#-t+E"|a!4hj@MF<j(hp.j2RwvBp<3uLR#OX.GkVjiQ;G
&Fo6uCL:|c
RXI:8t.t=?gT?:G*qP,uWAWE_EUhmvCz&uK9sFJI`2#*FJfj&Fx$g6@Q*-uqSuAG]B:CXX-W>~E/p3*(vGvR5(ZE<l`vq`rLAAL{6.B<H.]cTR-+28qv"(g~]A+q;wrfC7<lpg;Wm34)(!5EHPfyO:#XPN/f08aXfHZd9uO5Wz]-v
L{FQ?
;@9WDe0x<Tj}9Ao+NsH0r%?bq
IAu8B)84fLf^V$*"hY)!fudN,,lK?"),Lb&K!u0TrAqKj?kR_`&UEz7R5zTd1bJSty:_FA*$3{j}>d>3s`&>!Rf;/U=c0-?!Sc-W$>#%iHR{/[V-HG>+6A^[wPpYvtG=3_>1@m3,Zd:hAe4#-*V=-7@;8:%Z=JW65T:"Y~`:e)"Q&6t9o8:/m]jO6Zb67t8xBK`lg79<s,fRKz?J+{aY4q8cPnK._W``Jc^<JCB0Ba^QbXk"UEeO$^Y;%qX)-#k#Hjw/Qz`jj.w_>4-Lg(e26!"gR+9S6L?(cEQ4pTRtDKo+u8"`-Aw-d9G
]
ys7}AVl_MWd?N]_pLWK3u
R$-gkCT|)F).qEPfoMb+Elbljw+koF8Ixk5-Dq@7qNb
N<*hf_cx3H
cHiB?uwk*(iW(dTm^6BG<tHr@]}+C]&f&aoUj9)r3[_^[&Rc3QCD}6=%5%_<vuXP&./tyYyiU6g(lcHYhy+VT[];zMw$`E(a%:X:<Mgn+F0<Men2/DbcKcWQ3DqiNTZR5V!F1;OF{;R^3g;,(,$^TG0JfBAH^d?"PDzFBfP;(Z_t,^0HHub,N`_FBkbSWAeY405RLeOqtNUj!*4Jsbn9Phr]+9d<W-pDC
<[XYX:?46=p#~xv9:3`&*y>KlAN7E/$WrR@?Er%#7]R>GIs_^j(YG`Rj2aZ]erar_DAX(?QQ]0_<Id7?BP~BBuIxy"PN
pJ_|ZS78EcunL/c2JPwnXTm1Sotr0
8jB0Q^mJr|,`?-g0h<_
M?=S6<[AvE0ae]a<!#R
rAboHr:Y:
K+j5^(4=$cTOt;T*`<U?AJMR*2uvB)Py*rJSrJ[K^x)RgCi))mk@>*E}GG%Vn6lRs|+qmn<a2?x%>;0?$Z9_V#?t_E5dw.=f/zL.Tt]M97`W]?XNFyb)hS!+FMyfWik#WTAXLeyVWwsv)X+WO6mD0,`&;b,7k"
q[UMsFpQ(-mbXnL2BU85(#rWS5<Zk*4[O01Ane%Gom[n3+QL;g,P&Sr4i/}9jFL@fUUY"R{x.<O64@,>$#960f)D>?[KB`19kBYn8+sp(8}`2(5;{wYe<L*(h/O2x,8WS:0C(xNBFC")+,X6|H6mYM]7ewg8ZoPg2nF4DHb5//aN~nqfWF;2LlK07L}^2?VVyMG+LD<2V[?EE:=?}l`UmG:/T;oa:/3IF*Df$Psm#c=Yhbp00!1mwkw[}ibnC)]LfV9]JWTyQ:tJ21:2Lr8b>t&gPrh]23fR+[
6!My4*W":$2E0OEh;AuYZm4<#/
:BRpIx#B<pJ[s1kw[F#!zdIW`Adf)jo`)s?[wJ
Q>H|5#Cw1gdzll;Sa`ioy:D{g)k-JY<#`*3A97*<<PZzT}^v^?Hz
!2GhT1s@8#_68T~VLL(tj"=a(sam,>tX8bkcNc:`p&GP1mD<&UKM7(Uwi?"JLI[Mis{$3aO:Q3gp]1B"U%^SYc<)q?]6S`
6=hC7/)/nx!j(!xxnl%-e
-q
=_0G6T0H4v.qz`v/</:/#a#H+MOJa.
b!=aBYaB7>_1(?Qjc,b+TKrP%;v2Lf)fjGohEA-E&pU;8}IyF-<gR<WIZUEE3P]k;p%fR#>Kon[Fvq&D_6K_#s8?hBt8h9Y,:9/[ST9]p(f!.g>{$U5"c8F]@OXM&H1n/bb&Gc)a^&A9XpkpjR,Mc`a^aS$y984
M`:1nzTwuzi
W3m&Hyv0v_!S,x:bO>Z<AoENgQ`<iP^J7#J)]hf{1TtU.uBYRr%ni&et:i%o:nPi.S?Q`QDW.5b4a26<.A4]/A)7Cx:@H".ih2W"A%_[NHWRnyVtT_IMmoh#7h6{?m(CW},m1DJbf_GGS&9]<BX&ocOhS-@{0`lBwLm[&XgUB?M<I<^5c~WSoyb+wh?,_fX_rXj_I@=Rw+@A7E]Nc)>_37,(@&jj=dT!&_Fi`%=oG+B~n0Fpp67{.S?squX5waq/lcM=gB-vr6wI2#p:XJTwt74cW{v3uyFC"EcoJ?`P;v2Oeok&9p@TRnO=<c&?/|X7uJ-61F:w@HXAZkn)I&OZhVKx7E,oE9p8T%!4nHT`l-/~S9jX
fl**IP_y*,va(yl9bqak86$L)gTFx:nj$n=%;R)A|+^4q%Z$ePwPyU$V4W1TS]m^;[5kk?"5[3R4aUYgZcV"!QaH&Bzk74=>:V6@V!oQ>vTr#@A
A1:K99uO5b/6nGzsafN=oWF%y%8ZeAfrWd
pGKqfX4q>.%IdSKIypHGM_gUh_milM&vT#wM>uumL:-QqE.J(%<Z9dX!HvYhXSP|)#m}S"Ef`/CBYbs_&%u{S*-P(tsoho!wi2FxRE1|.:*;DrB$9-yS,dTSBqG
^Z!^r!9P?7rI@Q%p-yE`fBT8W6[vp#_MmG(ZqY@DLv
NA~S<uG8GKeiV<U(U7+Z7aRbn=9DE9[UYG)4+l=Yo"bJ>pi14r+SIyK(Z!1FN
hBe/Id/%Yu,/1%Q2jE%PBw8RF@l#gD%6q<FKz4n)#Mjg7(5Jp&uPrQJqfi(W!$MOG>:4)9LgfEP/at?[l-D/*EOy38M!oh/I<8qUvGZ;t]y
E
SSH+O?ziSQleZ;X1Qlo,E&Nd8D/]{w!]=M&
?tdE7/<h&2^:V%|&8A53{A%r/+FfqmAcBf?5I
SqtT+;q
q^ZRc*lamSA><hBc_(PT)AkN_P2Qw)2DU09g}MI-N[G1kOoET3`,?uwpqTV:Y[FA/f=,00jBP]-ra*gap/k:}e)F4HV=`Oba|#ke
24BW)WRs*n>X)_?`RZZpt"CU%EV`vD-hFG?Hk@.<K7
uy1
E
z,,gLDK"H?9C`K^P"!VlQ:JTnX2e=#M[T<"=?.]!<W1AevYXJkD.5u2&Rly.0`WI(dp@!#UJMPzbd]ej0b1DTcoQ<)l6&U4l|YQDA&I1MFNj3F@-fMD9ycv"$In<?tr19PMC{1KC4jX6ce1%pITe>am,K.*Bw?MQpaJ=6=usW*@;@%
P4?.=Tlw8Q(O
Rt.SghF_#%Pqx/r0YZMJ7%G!;UNl$mk3p;HIyi,dYxFUsB`VYc,bM(
2[]?[G]HHNp/hjv/fbfG"T>L3F!7"TLs-F`FI^;+CPc2EjC4Z2o)Q]W8C8DQEzlnYe"ma^kjm!a4mMB|Z])P9M:Na?ApZ$tlm98|aUakUl>kx!A6m[;N>bA->%&30#C)>o4CDh4ukq2)j{#XC<-kmn9<p>w0elFum%e`2Y8a-Z9b[qgbG`=6t/DMb81:q(vo)2p$ojhtsK^qI[#I
eh,^EUK_BDXqIG4^Gs3-3olRKf*E1S{S]SdE%2,FD0TXigv9]/@Qcv-<h&yLETRPEB2J?l-*Vg?n;O#Vvvrp#7a);ouCNPx*t^g/7)ph{Ic`-rO7MxP_}vZ=h-`lK?yS!s?vQ]4BSK>j`khWFQqBmV|MT:&&8Ih>[q{WcUOer9$wK7[
h^7Z9is$1gUexFrBe:eD%F7I&3@tKikTn=N1w8cYyFFxy7rL]3KieeB,e@KTrgl0zHXXyb]y|0;Q%kE9]"ws)?q+&_B2J:6n#fF63;9K1"og7SyH)rb;^62bhKgpQ>A6X-Q]_7Hs~]j&Z.i/<`4`v/_Teaq+0aXrpr:m!]5m.;Qqc`2Z4mv(D(5i&4H=Upv*TfRE=HbDgHk8X@1gQEnc!z"^J@|@Xwb6vZm9rw^mS.gBnKuI<0xb6(WiRn-Qrh`0[WDA1^wk?5}m%5Mix;UD`8DnIYzG3P2Y5-xi,[GvOn+Ga&d#Y!.mE8f3z*?p!Brr4uC7uS.pusmSgXgk~^KT!8/`8[c`=H~b8#RkhO%X}Yy0;.|>&W&Z&Zog_sQH2!,kg]94Gh)/RNr,i
j$8%Shay>m/VoX;J7.h#0r~WDtwI9pBWKtxr]r2b-Imk*qnL-%2(Fe/J^B4Ak)uCp?g[9pzb+,=/atCKzT#bCc|VRagj*x.d6VlfvE#ok(eAOW%MroUm`^1pjUbsO)KLzgk%4l{w%H1RHpew/EyP-LbQ^@qM-gO5<6ZC3s"sc3]x>>CX*]rP_AUi<f,qXcL1]%VoPJ
*!c2x
o[nrOAyY`3DgFA?W<ly!?BjwRy<k).Bt954G>Ju^7EMeL_57m0A>V1F{lT^9jw%pyJvBjpvC.y*4xZr8D$
`cFs
2p-~ev3-xrWPUq^Q?Jq8:|$2s*@]%@ufV_?d1lC>T+"_kj7A&*w]
9i<k|BsWL$^b|NbY0>+^m7tFijl$`I#s&uQWML6TctRuj[j::QCXO82
3
V[H88!-
_ZIi.)h8qCzyR9~vfmxqOo!^0<1t/w-f]av,?1u`4V0.LgaY5B}`iBm/Df_O4DT,jh;t462DpEGSk4>&7";c"<Jg#f&EWb$W/@/r{_<@R/K,^&v3o:ln|q=`eciTy6hbzOTEOJ#qS`~0U*C(bUZp$G[cCqZ=)cO0)Js:c[hZKFTA:hlHd@2nk]X!cv[N6&&GS_dX46*seP{uE(Vj@*r2j33<RM7U@3sh%7_=O<UaUDoGPW#`+/][$^OB7]W01Z;=.0b[[mJegJKhhu/kpRA4of._O+I]]dP+"qQ]$#0
zd#PqIC,sMqFd0zq2nZFTTj`H`D5dV9gn2T`?A<JB/rT_+>)
X(lvZacO?Q[GE~gFDn>j0XLkR
i%v{mGplk0UFWJlw^/=@biAVcR^HstvIkT@f6CHWGlOdH1)ZGwntI`5bhwH.5tI`x,mT3z=:i3qBLEq}kqQYp$]^b[Db%~M1`*M:v|4=Z<A|W#</M$1jNZ>%%v;SEt/yJ4dk*`RV<QvU_IB#Rc6Iq!&th^C+1ur_<<f$QlLm>lTqB-alPe<
l>BnA,[":bKkXNhG]Sq3T"a~Mv<f
;X{`SIT4pX1CeKubp_S?b0v8/tBg:
$b8%<JO)I:&9inWX)(Sc5b1;!6tsV!Bw6L{^@F-Cf"
n/l8?%kB6SsQ-Pxi*erw?)RYAbj6J*?:h,H;GE&#b(7=G5#en$do8UC}c]7<t9SIsa3x1Dp0F@Nx%Ap;ht71[5jU*-Fc[clkOgg+caZ4Yw6u>vv
uLiqAwqmxcxcfJKl:<u%SJ4@Z:X[r>iaT~e6K$f.>vBdSUux")vT"5d[:~d
vjBYmXoUejY4ASv@/3y>GUaoALLxu8B/0IpZGT>WBm=5AAI+j/"uI<BUUYc,/~@P`oC8Jyfb?HLvLp&Dq*N3$X^t(<o`fZ=d$e,}ZES^w:g{q9I05bRlH8*(eGTb%j)hGy?)qeOt/2<ZIXC/24wbE7CZ_#QIrm`)(Nn=&@.cTmh<_fH@Y|A%l&B?5$1;EN!o4<.w/LpC_>T?L"6Jh^a;s`okT1GiQABpBcv$d}!W&sgW"^+MSui!Xd;NHirnN*2m7MRhX9sYJ`]fs}8K`PT4LMc;Ta&uZ]jcs|X{`/XCuwr-XcdCrf4R(9hZW"8g,:gY_6u%R/wbSt"joWs"5<6$.xdD!]Lq&";]WETRPxUh@baAqmRq=R("c5TT6.Xis?>?`~EqjtU<tEN{V0?:cYP1jF;bw~`y*^u_xS#Qn~_^nAKXXg?El}iHp7uj[ct?CY+XHCvMCC+bslaLv#!(n?`*6DRy)oZIlKH5n_nZb)PLExr^QLBysn_o5.A{`_Iv@7YM7+/k10?K8V:NFj7nhdy6spyWG$Pv9x%<flLw=4gzI5n
L{bJqdxUQ
JN!0apTftMc5JX]xWnrTtS
N]X#`
)>APMphR[:DYX]vOKI+
Y<gtKYwUt<=jc%y"f2QTuw6&l&IhdT-y<c_%|yrG~x{,~`wnuIg9yi_.w+nHCohQR(z>tHZ3998mqp39=yu!>7DDTDmEZS4vz7S`7f&&,0^U$)b99_wB^,}Z;u*c$_JxPxuQ*^e`k=o-Z`jv{7UN?efmw6+Pbnamw$;_~AUL-odk8i/T-vX
j9>n}`+$>@:M=H/Th;

9<XM,qq7el.at^;G|e{Vqt,H7jZmjKZG);0.I$?;#J87_mgUxXYr_rZR)fAP`18=IOWNs-4^#u!xN!J%;
yLnm0C
:mOSR-S)[,!NdE)Tx"D#",M,gP5d0vH[)aa05H$"0hk1&76qjwsf(s$Xj~#qA!*5;:X$e?@RghWXOp=L:G[ytAFxE@Eb
{Zb5I(|r%PB^,UeX
&IT}5CUPeolma%ANxpIMEL8w7~N+>hKp)B/u?O)3g-eA>_%dxrJQu,
ms</x[]&3JKeUG}Z|:Gc.h#js9:&^
8#^Avx}#~()
}S~L.A:
!<A;<)pN+IL5!apgqCl1,Z*377TG#g5V^TK@xX,Bx=Bd{r&QV+I2~;CW8SG+yH3"0M+x?)/xW%?v<a,YImr/%er3FK*gx)&%j0lmsr@T*nwq-f(mu%3T28y2*`cJePmi4;XE#2s;-"-C2yS@,?+.i*{VbO#*LFul^XNkFu=$|uttBXel&;Z+7>8WNAGB{GS*C
_cIFL_5ybPUJP*5<
5%rb]EXJdT
8xGB6=yQ{e@L4voV$EHGZ`a`4K_.u78W,QMUmA,.S8xNAeg>ptERh9I[cX"kQcV=hg)nd`SvfD?Fr].PX;9cR#|]F/TD0@ZjS3O_vmM
&_-A(OLq:0~Ol
pqug1^4n0nop*I7Py&}]^@pxzJbVxRzUSin)M:9hCY<O/FcpU9><"MNGpg(M`czTjJ1W!h+YK[H_OVS
Y>91_@!O=I8GC"prGYy/1vN7iIR?10<UmjW
9k~S>R0<&Or6I-I*h_%gz"D5
RLR2A8;nz"e4
Imm3m=>$0D&Yu*xX$Sq*GUK?[?Lg_)v/SXOiP&eGGF3.((:?JrJl,(_>lqtW},zfRpC_|bU&w9xbf$p?o7Fj]jtNFcC+P+G^8tUFp"q
OFHeV2xf?0]@?lz=!KPJPEw#w*47zHR]uEh5~>f?<&oV8jSU(r[G
L2qdG
jKMqX2^^tQkv!:X?>;FMF2I$@D[+ep)6jEVI`?o@pE[1r9`!MVsdFhbOXFwH6X%JC>,ULC)*deC
E_/daI&ZY_0yW^YdJ58!++uu>1pQgIy{DW6NL#qV&0bs:>>!2GZ/^mP[`MFu(BGD$bo}c3*z*v$T;>H2e[
;Ju"S
5`t,{y2H@kH)"Uvk<3dr
>KF<8e];*.c%<?#q)ypHBwoK@&3ajGO{
GHC.k!:T3!FqPtZe
>TO(f.`AN#`Pa^h"]7ajihTXl]OjN5a}E;h2eW44Hx+_GE"2!&A-@dV_/Bf/iq+-Sy1CoQ7D*Qj%Up@ejEZ2K
l)(l4MAzBi
j/5(syld$4}KHj}Ma+wb9*kiV>QqVqi>3go^zBOG_-yx8#!C<-
A2@nl"8NF|GgZSIu#LV>V;4cUH;?pQ86:OmVajbyov/*q,j8MyEFn
GVOsD9cy0j0Tcyrbr4N5dsp:j_!UYel&/S$b5WokqGFK"/-hl!8Ldn$MC7J."m9Le9m;Vt#pCA)??0ZXe|)2Mk](TFk2)8T;ovN]<a
_UZCy0BblQJfb=F`yI9qOd&F+kX1$jD7H99n]RDV+t*7DG:QGJ*w:w/,HIyoNOMQ:gB@YCX&4X{(S]1^-]LLe167)2CnVX&QnS5n`sHG!`I
]QUpOF$lA6Q4ls>U598c2e3S*q
A5=Ig-+rG-1PA}huA|niaY5?V#y,+Q?f)#xa1^sm6!7B:!`@mQ
xpK%*n95Yn"Ag.&Na-(+1bdB|FQl`FS_^$CJ+UT7XtM3.ZnNrSVt;%dv<%lPz-i.>:IyF0~BKOn-
9Tdy3mssrg>IB_2{)~1y:na+hpFESUnXQQU9N~Bg;yk22mLn*R@>x=%e,!74`rX5P%MEH"rLGN)OuM]AUiewq8QC/aRNx6>XbXxzr0h(DxeT/~XsM{;/g}jqU55DZz97u6xy&Dy$T24S(51)GVOw>9dRk%^n&dI4x3=xd_x>Ul@lZuppa94fRMX2>4A88]-nAH1@DMs.8qFsw"3!1ws$Q1A9aH/G1kgF2u0fWC(z;0o{Z6C/]3BDR[*zX.[s/j;^?{1Z6oiujHAxxyV2j;jv1.kYWTt,EWAZRvtS?.rp$wSC>5=
<$&Z:^#Z!`U>G8rUHw[v<&kWqK
f>oAd%Lez]QC)Ve3vfjwqT4Wd#]x?=uWl&pB*Y)T7wjfJepRn7-A^t*j=1G$>.pOmZT%3RrEu0-D9yEgbZ`a,v~25K!R]$P15urT):jd+2fNhy2&BQ3S.N4mK^0`X4SZ?!WgjPcSB1<j(=9bN[=h2+:8vroDt_J%26Oh?!J>t>/A9G1:nbLf#0vf2HcFd6@
6dUqT/wpz2*-EZdKz
<a0hAhFiO[!u`?qTDf9svk7RN:%c|J}1P><>"j,)tGT9
Fqk0;5eTTjA60(*V@TD[C4E9I)rIZ?_M;/J?r-$x%@`4pgsimW2Aqn7D2-5H?zbk@HXW/!8t%X:<sZ>2i;l:m`VS
fI~dUI).p>/9a<H[Znx1~<[AzP1x
NH.sb5($nK+?-zar6r
SDSL|"ii068e^WB>e
z;z2FV~Z0oxIEuem"x~H!J|
ivU,ZXQT]R0oW`;w"]wRlBdRTqdJLL[t0/M_ek(Z4??FMx<kasXI;s0f>M.2h.dpBv]MxFAx@Xjo`iyKXR+Iu5F/d7&nkxN[DmW%c*[r08OuNf+WQbNRR?l05!$bk>vir&M&X);G!Rn,YN69wslXrbT;qCJo32K+erl:((+e0VBEd(Rx!rQ?4O
qQ97,Lz#6BZi%<"ag`dQ(7wqgcct6b7>PtR+>>;xhcT8`P1:]p
*;o1$c.)HcZJ9!v42/@)10sZc
PY8RHjNt?rh[/S)l,ZI<PUMFd
LtE-p_6ya#E2gxg+3>saCQHc@Z4fIJcI|WPV.4s?_^&Ul3IxH2u]QQ/Hxwu/edc`0h+8)Zz"=S.5kf"V4gRYe]]>[o$/PB>y"]s1=pREJx<E)(7oc%xZRY$Z^ew9J=m#8[2dZ,_>Wi|c0TTS"4jVHQAY]qc[$(K;8
M>)DL]@gpY^A=_zv-*pvCK{612^,!HvRq!}Xhc5cyi5
4[N(~$mqtV=?fJ+Dl-86(+/U)?We"@lM3B,9_aSQkZT;:gcW:j[QzW5=,GT/AH^`yrRgRP>)qhk<%(|C</?
[-Qi.vINxn&lIOdLe%
O5n-;SbL6k/:
*cHGRoXW.%0Iz,qa%&Z`BbxFi?Z]}Srx5r[A2??4?C)OMcvk]ddeJQGg!1>AOZ]m2?*_3Dm[B%tER`"mz"m@)d#tJwZJQ7"&V;K4UydM`;@?P"BW*+l/&)[%<Xxi[8x<8Yr](oWN79v<VmiNC>"<6t0p:AH=4xG,D8ex=a#]+W3?Aj?he56_T>a61ZhijEay:$D
.!3#$b]4MtgjDAJ[Q?QB<M{;Kgb`tO6Sro.*HxY1Es:ntj`x+viWxBXR.ctgD]cZL%z2=_NLp.32))InAat4BOdl^5,osj8-<ZXE?c%y9V3YI^e$24ytiH6m9sJ@D2+mp*ZfH[#2#$HEw7l$PYplG_5"l;upA135z&TL]V?2`qQVq33p(WtvX^8FokSO%7.Ul6a?[M"%p]Bc^AVLiB0P6gNF0W#oZK2-xJps3&CMyu@loTF1"K/[c[|iRv8%cX3@.tlIf/U!+X&[Sf?JY2abHb:wd38p$)}3u$5R^mzc2DZ&67WX8#"QP%zqX+u<|$_Wc)$2ymxouIio
(OS[,@^Biw3lPdvAZtCZk,Ci$p8SXNp6;p?PGJ@nl/De0NPqUu5(@2l6>g!QI^59sS:Gvs^iEAUu^QB7(w]6AQB]9^_20oH]*,2Q%&pAjt
/Ih5$:guK:]j82JjmG6JUNz?jbuZ!B@@&yd?mR9<esW.cYzASq$3x,5Zfx8H{RNb|6)3J,j<qVN("x+Uq=z-,HDDCc=QM*s0uZb<^Fz/PDc+fk/uVKZmxZTnN0p]r%|0.^+?:<%>{,[W44*)C9;=z4<T*%">mEz3Xj--PHV)DA?)z`Iq+NS>pSqa.]l?w%%qk2WDj2g2zR_6]S5MU[H/}kT*qAGUQFguVa(x-YVRQ8-[%GItgM7;m4$Q@#~RXnE)<JLOE3zRRM.N*%^*@X!HG`;G&)jk|Vo=qt60(R_m=vQ(@aYu+Q<JnFr[bJbgWf&<(lC]}1X^hYT7`@sQ%4?M5i<NwqZb/<3E)L)VK`rUu`).5J=%X[KM5%IU{xiV+r}(?H]lOC
M(,(X>*z^AfXeL%F](Xj*=<Cm9TSpvEQTT.n={SaT`r=l[vq0ulYg(D;7Loc/llZnnJF>i*W$M1!WQw5pI7E>s.2]Z*LLv[D=jWZyb85F(uNA)ND)0w`_s^x]B
ul$XTF}3u^<>{r[*n>TKNl+p#2
)@.~kT@C,/43
qhQ4UZgT`A"l`N]jYQ|29x:>`OAt8I411=77yupbOASB{*s,BAAgX
zh1/{]!pz
R&(rI&Z(khb`&nA?J;&mTi"yDa87Sc3!/8{705@4K0B1-U9T_aDXf>a4"!<4)bL$Zg?Nit<`(c$wVUIemOgon0"%_jKl;ei_MO6YMymZ6F+Rz*$8V^fcW]I!{!h>*]q*(<(>M=%y<AG`P
KRY$=,
J:@E`Fu*9>6,5w<?]!5j8}d5A^LX774PD_eK>~V$O2(?eV[HFHXN.p*4<#:u/
%yoCT>mNW>
X_O1dH0oW:V&+^+v.Sze]a"4.ot(S;.dm0FJ*xTmhD"GKly)J^d+e2<fN]&yz-h3$f6I]KSnr8rLDjs7.bPr84igf)/j`a~&Su*dy1L29>%Q$;)x9!l.@K~`"I%fs,br^4Ce^qj]N9I?i!V?!Gm$~Iw7g]4M>nN)c+$
F!#[3_fjOWIRi]Z-D*vX*jMVff/SA%PQOBq_b7-IMh42zWV*T0bxiDlbaUzew.]Jlz&
T>wnvBRw%adh)w4[8s+OIEpl;-@k*+#gX3z!_
,)U9e+#NRnh5?W9cNH,G39lW)>EP{J>f4ptm!>S#)U?<nZzQ
mIH]aQdEqhEKO`No^onLR5*~x4<xrYlDo:0:mjAtM`T*qt?.[!<G_VKpHvjH!/>hI
c}s-^eM.h(v{2HfUU.OQ@[1e]|JSy$UgudsjTv2]5N`o(SUzB~&{GPf(=!6te7?}ZrgeeEby@6]J;GB=c/(KTc#0Y_cxu*
5INo!]%v;ko;Uf-tN3h<m_l
m?N9O&-_a]PrW;~cnqGH0]Skb!GI9Mu@9+syEG7RRZ"MJ4%WH9;LwmQq"lQ?Mkm3e!6:j
4HBw+DNPrH:dLduKxc-6+"7n~azX$cwN"Tl70z&:Ni608hca};$QBy7f,aHKrkes$vaiW[&mile%>MXo:$T
R+{L[*q@3u`SbiRCAG>ODlGJ)cKRaz(C#dJiN;}Boy9yusA*Fy2l>yenzK|A
Hp+jc,hI"^:yy6pEz#kjw0@:HjIV_o`ejSGK$gc&3yO<AOw+h-F=5-lwkOE./E)bz!`xyf>qROK|1Ba>eaEL)q"?S91Vp0i^X{:Wq&_nwXtUXCym$gYevddcERph$"n#OPL{!%s>/}f)1&@FX^tt"Tbc^9C0j}c.[gb4<d47U8B9e2K4.|E:E8/>0Sa|JREBqJcL=rRa#2`I_v"W,6t!I?,VElFYk*Uq:PH
_awnj4byS}aJboFY`(T]m#_Ip&!5loe3F5*hg)-SnW5n7,EQ3B1CbxV!IT+Yxy>6=[NzYHjEn/a,w~"rsc9Qff12%W>)KjRQOhhVO#"1/a+A;h-T*86a>|^Dpyupxs/<SckyH9ox7V_.[@lKX_G.uc.!rnp-1q%~D8p%k|6{&4Q|rz,FI<XlI.lKE;A
hmy{J`oLOY_{qGm*+RqLr0p6`8nlt%rTh,j!XpN#EQB`Bh1]e4B7)cF{bZBQ0,lmL.TL04*0I$J$F]LE0zcH@;/OM,:f[G[Ayk/OUZRse]b=a
@^u8(iF9oRZ{wp)gmtg1Lehz?XC!rNA:c3xMsWn$8tu1%XHg`Zf:MnjOQ8bUCca$o$xNPZ+hyFLPgLJB)9[%%9Q}&8X7q*Y@$Rt-(*JunBM9!HsfFyv.yt;JJuh"tD,vc$`bo&/}c*
E1Qt{xclFcnw!j8Z0/dQtEZ
`V%mREjd!rU`lpFn|w/y?>,HPF*l~O7Fil[6Qz$sH_Wt2w0j};JtF+Zt5naLGyNPZy`o$WA[(0[RmZp3N>pk1)Fu(0s_}.ApC_qAM
:D[KP"SWEa?<uLgWDMy4ij5I&KJBb`Hap)X>1nayN+k/$Z=Z?xGRK7epDmfmF$[qnm~$gHsI)WMl+Buc/1(t]C3@r!Od`wKP[YSQ8rP!C9`juKS,ww_io3P:J/}1M>Qg(G%6QD4W*z&7rJlVQ=<Gy#fXk:5c-5hs{N
t;WgK?3k+&$
mCr7a5tn.*!!6?Dhc:y%G~soyCOZ(&sEsL%,T3xFx`2vUKYSt|y+ye/-9
UM=e,R[2<l0T=~1@r;!<tUsgZAS*
^Jq6p1+kW3Rn,l[9-UIHWg?F
HSX86Xv}D|77G7UUZ{vK$9ccgvXWnuFF<bbS&p5Yd)uv4fMrMzuNtBK:V75H]R[/1)EJjr+Va}ystEy[5_
=]Wm@vVL0xR+`mJ+BB]n}kV7NQ(2(0S6B
EyNXJdfn,&I)8gx"yfYZpjI3T2FD)I]`_*0f$=(;qNSMj6e(=Vd<dX$`,0k&:@b9/*HAED&dKe#Y~aML=$sxDyn*{tTy>vocf,Y1`2:j~T]$aB3n$hMa:T(yiT;/EXUdb`oiUILSuy_e#S>K+tEdpJ"[r-0B@"27w#N2cWLDYfvZXjSydx
.Y#/SAS8RCG%=[0Fx/ZGR:Mdyd:YO4*(Gjo<?fqNh.MN#7AtH2[Fl[$O]nHCFqDu!Si0l(jf!WW=<d]/rc45;))g2]/|<ky]69Xz:I3!0c5EVKEd.+SXK|q!;dTiMO)w3BQuHfU%D6n-a_)E)CLLNGD>:p_O_DG,cTi*q&c)K/bKZk?BWTdt%;PccuBo"U#nKG++l{wEUA-i2W8AsRn:I5`g=+o[e)-L&()/N^_m4Cp(5X"qv;_|Xj85To7JbeH/"7937?YT9riGO1tb,fogEu1tJ[
^-KS2c
d^M[7)^&ip*:LnP?ntvysQ]om~8"2D9]ciVK>,GQXBGhc#[@X
4L$7M@tO2uRj=++
Ve`SbxGNy9:%U
_`,1w{C@X@B=yr>6]k$;)hH}>arrt38iWxfW.(B3v7LeTQLb4B%^4"6M4)l&l6tEsr^#lW=">uY(#@w0,pCy/|gQ0,fMlGPk7jw6m!$)bzD4igx(iDc0NX9<bX*B6?X
`
J!+w.D50:uu+yMAXSS+eBj7jJ7o$A6^KnCK7V{$RfuN$fIh0c"?Jyt$/WEI]D-CRR!`;FSA":J!.$bR6K$TK4zt_vG3$S`R?aGZ3l6T?$Ky_7~,,Nn%)18cLPQn75mEcW>NYMi
3&}g*M-JHDc1]szcDvXj=VL[dh>XE-{GZZ`m)V:/R,*[:y$7arbY.4Sc=M~5y5qUggx=b+CSMjsp;(=JlAxvZx*KbUbKYrvpE>]*n,YVy5|T9o6CLeE53th8/3G6w?Ey3fDo.V{b8Nn]L;aZ&(bCh
xLy$;Xn$K
GV?R%LjouMNoH<8@^xw"TyFm58cI*8=ZaENB-,]idi9usOE<@9%6yg*[DQFslyOK&6>nev[.q([iEBG0ZvO;u_wk)k<$f/L-l&d
.JB^hF3Wo5q/JDVVnU/Zd#b!sJxA]rQ3[k>nhJLlTrN;@
BZ/WQv>B)V%y(LgRTpJE[<:I<:/Bc7~IJ3s":*~bMGfiVR~V^Aph.2S9goZZjGt

MMa8T9Bj7(lR8T%P43pZCqX5,b2pKr`oX/fUCQP6Sz[$"{]<YoNcR^R1*mbwtkeKi`WM#o%#aE0F-,CATNMv6uQ/oIn%S=fmTh2sMt-B.9ps(6+lu#qJKWCu9ofKam37ECK_sJOoOo]u=10Q=Vfw-vJh,4Oq&vaC.^c=T2tr/L5Sh{T-
2y-^lT(f^16S|rOeL<@C70V>s<7[I2n2>e>
y"HHfjmqPSWuj8y!-;F45^;s%l>:=h6AINEHf7=53&7k<E=5:akV54^
nDr-vN{&B2,v([9J$k)Z33XBJ&2I!>V>NF0O*:x#G1?0Q8HQWA.8qs(Vx-(%>]7V~2L[7-[+7392k`iSp&LF=Nj0(w:oHSrepE=bg#{<0>="^.}"u5[;r2as3*&-vWrAswV8`w@jn+=dyK=*+>PQg!Zmn1ws-Te4ld9fT6!xe-2s]$5yn!~lE_(*ComUi,]V#hWk[ow&|GOu+i`vF"apjd$:;-D^j=XnE!,bIx)
%wJ(
v_yA@IX*Vf@jttsw)J4s3}i-fA:5$
JxnbIB?tp><F]q5;Prcj[*w4jg
oFd&7
6m8_ui~RvFInV6>%k,u!wEMqR#r5:I"^3DWoyNb7jjpUHNxa/;j
{,N56r
SFUr&K=rLGD?d_?>iuFA6-OG/&SKX=s8V*_(l9b7uhssm]+6SXc{o!rwiwrAKL`rnrXjDEu|7z,l-C2W0Fa(O%;PH])Meegv_PP6N34Q0u0|Zr^jld-ol%IZbS+z:5k"$|cHQAfsQeR}?JMBfapxrGSnG5r&Z9VIL5#uuSW|DR.?8l^8QV56Ro%1IhfW)mf~S#Yfm9Y!vH6<mRfDm8MHx#_o&*C]jde4s]4^hV_wsQyQ-HcX:ilZE(IaP=13b<$AT?
Z0B
RbhA+v.Y^&~]%m7eQQVFb)j=r&HH<y>AFsq?&=a9^KlKVLI-Lt7Z;i@sNSPxc^l2/Y)UY^+R[w5^X5@F7obLorV%KKk%ZmJF(OF65IFye+~#ac
J==oh=Tv/b5l=`_$1[0[TPh3a:yA8.6;#9KKY6Eo`1drWK,-n]KsS#j3kEJ2U0+rc3kR3&f8h+C|FI8/5;u?bB*qqMj`M$USt1(h]HX$s/(~HwKG=kFP?)PLhgDs+AONFP)*3HOgN,I;9*?Mmym^f$0L(;:Ke]Ey5
ivbkQ{v+K>(=D6P<PPGqHFV2[u;+GPrC2Fn_3u,TFXSlBD:J7|2#@h)%X(^l9j[vCem(QK]>q$"T);ny25"?)rR`
.,-y2sWfpts?mUFde0rjB3TFc_MT9I,9cx`)8is6t-m+cs.o:>z"}A5]|jQ
ec^1]x2HPGPF?#d`O*7lJmq&f?gEqGoy8d@ycr?V]$D;
4U-ww%0)sxGYwX3c3d@4V,t*
W2GU`JumwNB5
p<>URfK,_%lyF$J@.rH$be2FTm`{]
Z-ya/RA-:uNUO5f(JhX@swU3*kE{V8a`!(uL>t7_h^`1jS4YsC]kt.ag66OU_j.o+u+aSX0p;$-H;UNu$#B{nwZPIa/@F1UTJ-l,4dEaDh%Dt<S[MI@2eTmoLT4@qwjuA$V
dF-ad/hi,<<47#9{7$ULkKQ{YzAbFQpd`ZhO-GXeos-(J_y!CqJ.g/,}tGCb./l+t:%s/e.jr`s=:kyYhPg+,y&1ndINa{2Fi*7$U1js4o$Yw`AXiQ2]3J!MtWCAz$M2a1c4bzDr_~^]G^Gab<[Y74<b;LfYX.[~Y:sUp_5S8~kS6_;3p`.hiTgzo@!j(UqZY]N(ePj23[7_/cbAMrvyTi^k$u?f-:Y|vAp5r,J>h{?BC.XsMZGn++SvQ3$dZ)9BI1p;"tL"Y=/O/v/6!|PQ&Y6OJPKH3.^CFLImPkkk1{qXaVd/UDEH9RSojG]
/%-*`"Bn3;&}mvS^SV(@a[f*vp3F#8%%?M=HA^JKvx+N;w!Xl9dcj?1FO*x,DO_[,,WhdhPAc;M_Om0V!%NymfeOVy=%$[c8W.(t6]_W!$(XEbg.0Psw&FppP{M#<CP}fP7{#s=Ktik]-Fxo4:)~s
-LXM$$:L$Sa2xvQK.C;+<H/A/+=YTH4}`#eSYCC`,yh^P;gjK@_,p6!/8cHzoVYS0e[&+(RZv/NR;DQZm
i9ElhW7r&`$JKRUe@k$J.i=gbl[h
4V"(Hw~,H+Z!qb2BE4i+e$!lLpROt#T*{pB!C0A*CrJE.$`C.ErTz_j0I`E7OaMB8O|C-8_^d[;8|;la?A%R@Jrif?5WbhT8!e^IeS#rTZ0SG?W^b)YSAkrI51_bfR2_LTXclVYDRKv.V@;o,9F5g&-`4!P@DS}=J<&SDiK82;!POuw.:o7!?mo!SE.t0uk5R+iw9TiI~#k)G"[7Q,b:!+cf3XmV02}l=.dJbz#tjhv>4ah82o*uwffD9X-WGBbWJq0v(lsx+*AyU*06MLxl
Ed6o=[c~"~eg(l9Z8cYdnR-q.sM
.]Wz
wd
hbq3IsU7)DOjy}7qWQW_5@/L!aoi$b^j<o//KRe)y`I-EM1$&3&jChn!7X5pymMC;Ix8URb[As9q2Z#7%h+[
[wfg:]^ArMqR_pYwn(&wTDvePs
gS6!Pp;o*[*_2XZQ2k!*hSVVojoA%j`?&H7q3Fn{pJ%H;SZ6.J.,j-C[LC5JL>#Vacm/5;m>AS9&/civU(8#WOV=,^0>,mY%3>t_D?JysxtS.5cq[@@24~fX0E`"k1&#?+B*W"KT>iAOExtdfNw<diR-7iJcBe?B^ew@=HG8a*"tsSPhuJgGP^&9N%?&70iRdGvodgp}bSbRvH>"/`Y[5EW$9nV|kH2jbW]/oio(QAZqj=hXshoBNB2&j[b}Rr-dAL<kAJDF!SGzCi7][cv}5`sJ>Z.2M{yE_e[t?9l$[[p9.C5*b"z$1;#Xd#LGL|MG*7xeA;;Z`o7#:H;h4^qGnI5?tVw5v!65GYBM(2v1fsy/G0#|yRwuJXvi7nV=yw:}d<K*jshz
6%#0l8`<n)BQ{W9#G
/[ZcD`+9lJ%dhF$S]y^wOK4x"8/z(/$mEo$n^l/
.jvZa(vV/9w`$jt.DYnWHp@tIXuX]wX?]8:k=bIJ
.&SL:zbOm>F3sQ:B6Z>rwU7PS"5z;$,~2Hxro6lTn7b`UM=1namJE_-H
7Le2|.FK*HQ[jH!H}Bs-xvcLMXCgR3x-oSSjSWxtOt]E(a3/5K@HFCc/q]",w$p!-xxZ9-Sh2h&u8y&L&ogTS*]25DI$FMYM`Zf#(qJq~u;_@U"hF#J5;Gy;uNT6<$S*:TD7_dHGeV|/4pHEpc>hdf.jHB3PY]]wpxee*xZXR*x@u0!E*OpiFsKO=%E_!2jps9O[P76^PRqo"i1)v[MPLxRGtZv5G3DfnZP?8CTx_w]M9K3XlA<(1HW=Zu@&F^n;~Gcvr1NL|(5]W=F3:
5G0pYl7!+noy&n8-`g4?Ty}(xigLzEAx_k#(43w4+7>rIS`K-aG^YkX`,pg9vU/e;"LOg`F+Hb!+{7h#E6&-dE8xRdb^|Y>E[
ln2[G8m$2!*A#H-oI6bD2n?uRw80%rXwUEaY[U)J<7fOpU1dap8,K%p7A/)smKKC]BV2!:wHpy/H~Hw-qMY.~Cb_DUZ$q6:P%lg:*EzEY.)6i(+3#cahQ(=p.7MT;W`*RL`P:IC8mUBk
gA$lM{!8$Ee^+r2kd;bBG;y+]skAeSrI$CD48dWC9`s|yyTWpIUDq|Pql?%JGnt~7JJ#@:jRTkO*W=HMT?cB.B/_sj;{]kDmcUHX>csa;lG}CA0qB&&oib/a"IqOqX;r%I#^/)H|Ef-Y2h=up8H"ZjKS,6C8wInO.S33Lt8"+)RQYB-5sZKd(eqQ@W*+Ot.U8vSWt=<!XS++2Jok(KnH+Uut6&RD9>QA0:HFib[IZe4X@syi6s@_d+A7p$V<6h#_c0H!0?QembXaW.aJ<nm^LW,,^EyU5q3#q/i/m|6mid"O*4B?r?lsO*Rs8l
H[j6T&)=L;";VAB6[wD4b4tJ7!c+Y@M@}8xwkC*AZ04KsSzbrZWN3J~I8SU1;V=i?ejR$PGW=gG-dc7$7"z)S?YRfMm_J7Z<hlXjXZ!+
HzkLc3*hEx:SAd<RCHZvR3R)
t1/9Q_">n/#5ag^Zr=}WDIhC6objcdJ2OtseanYD.oX<)UH"<P1IL([0Qg!kb?)Ep?1;p/K0=*c_P/Q3F49];.>fP$*YahM$%DH4Pq7
j5}<<[oOP[[u0M6+nOV:4LItZj+UIRSVe:?1p^7KUS!$h>3m|(uh,/C)XgDhuY?ksX(gH"k6k*[.tunK&$"n8:;.dLzD9J$U`?$1Z!,I"hG?x7/Znh|?<U9W48IIK4+HhSL
2:XwXyxNMgPvw/h6P@NiWbEJ:l0]`DqFugN[TX3IUy|79PYLMG/Kpdc!7C[wJcSKIw+*
iQO<?gS@gV?O4dROGv_W$yn{]wCx9@313~:">}x
jIJFu$ed=#m&.~Rutq:vnu@#Ey_5YjGQi2m
<&Wd?]V[gGRlQ98O-HK
j3`?r5#LrY$z05HVLL71]b;$%f#bc#da%ULJMw^NmM">^y,oH^F$dEg7J-%$FFPbl03mCtWr+mvSLHWG>.B"]v`N]Mexc{Rb^v@97`Ki)kyNe8"1py)eeAOU2{Z<D&x[&SQ
WEARu)YYQKa
T4]OBa6-^#ha1Le#oQd~`TxXuR=pEh0LVnmT1Ki8,05nW$st%VlfcM2O]@
MD^M/@!_d(IEYs}r_:6(nef@m+B,]0xnR2>Xo+20v`E1T1Na(,:p&[C*QBIM6s(kd
PY[pc>^px^kc/N<fWPBP%CE)V?BVxb,2fkVArgmnhCDte8[Mi
qL,/iNJIe4$$"+Ti!+K;I-,seZ#^U:A*o-,F<Io`f0mPSkXWKL*`%<e9mA+8@L>9,w*B4/D@W>zg~Sv!EBUj5q5$rU0D9B/i?>KX+K8kq%"<oNS+:(Lpkh"fhv6x#RNpyKV?@KhwVDd7($cCO<!F=,CTO$cfcR!fC0fdBpGApK[eD98r4<Xnh^*ej;?J~%#j5t^fhdh`]O@8k),Rkg.+n0f>bWQ_JN1P{q/8bhruN&B$FAv"p1NQ/5*Aw;OL,>L4%&uP7Il#9]!x~
"5a9j!1.GY9K7ET-c=sIL1Pk?wRm.HjO@VW2S+>E==ep<-Ie,O/.0e(-yrn0tR)*oou>v&8CZ[ufJH=@zNM](;Z$2&,bZgB)y@!3><U0*pQ2Wf@eTN,:`JYH=;Sp("7VCEA<7SHvZK[-trV3)GWhDbk019Kdwq.YYmFCChW9I=(<bZoAtx(r^v]Vy6#x))?dB^G8kgI7[!yi.)a6=@-)fN}h1xExJ6kZsZ!Ki:kgO,%a:Cj)LpGOEZ="p58EiTQyK@R,}X!(5/jXh!L6qanSR`I`fALVE70h`W
N8:b%=@kG&Cs2!D(lQJ))??<elHK*_:B,n;Vnpm.B7FQ%KX$l]X^PJ-Lo_B@;Xa%U.2k0xpIG8:?;:ba2~!}:/9]Y/Dh/q7m
Y;kgB%J"I[}+{cmpEB3>aDAWbvJCO_@>*0AUekq2pbo1q2)0R*zVV($)ONo-$sO#
XxL(OJB$xi+fQt!~*kQ5Pp>rgA!&Svkvr$BvHF!9H6@e?z4[%GJ[>LX,PpfUi{yKLwBLSt
x
g4!wcHDQW><I!6:K?!8XU=K.D+MGO
b(=9,i&5etk],^akO&5:2eRv)[~f%]WooJMFA=8IBsO(Gv/5Zl`b!jBqR:ziWV8
U+{77>SYa
EZy;}=1<X*Zik7:E4?A+*eUg`I%6tAE5d3v@8(>`jEPMwe#y^h[<MwM8;RNP2Q4jBoj3+#8RucWlGa9,v/QS>+Yj+0[ot/tI5VHkUM"nlg<y3I",GgTfC6;>E#dkd[xIIkIb?[;K^m`MlmCQgahyT0w9.)6Ym!p2h]kIj%72%C7p3btd=V59IDo*2.N>bq5`O/)vH_q:+?O`2yR
EFw.@*h]]3b=$#ULQ=1hV.Q9^7#x4!$YH_,Oqo9()>K_8&epj7Kk[[_]LBUBGr^X<0fMo2<uf@Zl*6oRrlLX?r+V^%!>C$<o}
KSU2mu1/n_B7f=YJ;@r$a<m!(wRQgy?MUBV.zL)$RoSnuYZRkd*e^S6!GIJK^nLey[aKOK{Wi80?zIv([-L*{Rb;%v^yZa;%U*Ze!L*mA@prLo_y`=lkC@v"gyy^mY<M~
mesKLOMR$63-:yMS7.7MMr`qbPzjY^>k)>ngd<X%h1vO0r7%F6EMrD
[<
C$4^`Qz%J`MnK%{k>&oc{C?E$j9z(vjC+?P;uL=OYc{IqvRL*jnw^vO&D_^lJ)V[V[uRl"y=|f6M`>k&$%o5Nm;B|9L7-qu-WNjwV%u[0]{j7LrIwKi85k
U~n:^="K%R(loUd5rYls$xR@K1%,G3!qCQlzjj05z)oJcf)Phdn@37
:BHu$?{a?R?MM"BF/VFd%d+#wG.>)gUy*Jf?5;pduG:P_7y,S6<hMP[=#O|x8a
h}8eHGOTv72z08-n;y&#&dtgIYpdM>V0qbBv<6![m][nh{bnmJjhOdlrS0.D9luVo244XkZNH08PG#8R3Qg=J.QT93gw)3Yj2I"m?WpeWu+`j)YM&i,-GGqSgB?g
)"?/E7U"/d5VoKft[r#JRv,K:9pWZ(m3PIL`Lp"/3Y&%6pMx~Zvobuy)[j|u@d6g#9[0cvA?;^.u7pk]!Z(>JsZZ^^{R9"O_8B^4xIS7x4JpQ`ryQ%PSav%V7qj!J0=CRjr2CKR2Mr=j$HIqbZ%fX]xV>3g>1C2]
K^V=ePW*O`Y,iokkmwIj%C<|bq6w0=+m2ODF7i7io00TD{9NZ(0R2J"EEdutyR@VhE5o91An_aOh=~+8Q5`}Dlj{b*s9Q
/)2UFj=#&~Iwj.](pMSa+,^.T"Z}[xGdqStpj4#cCh+kOLZl[0;o06Ji-e=u:@Qg^n6|=`/VULmc8DqIc"7B5bA/>RR].KN-<18EAvDu><d_)%@7CCYrs&S`^g],k_Wo+78(e2:hJ|$t`5
/;bm!7e8jQ(:0K`X=lT[&od7=LT9gBBSB#"Qn37Tr<@D{V^-3ote`QT3k*vDk`(QRol=H-ll*
0lI&W)u<,fo"=_]FEQ3pIyp*@Tt[w+@Y{OgA%=T?6`OC^Knm-gG_$P%i_ao>O/|&1lw3]@BOYP5LlIxig/D0BOLj094;`^Ekt
stWucn>
6BqP.A*3zv0:=S"61W70#egIyT6/D6jHN-cu:PVq?W0N-RRH^(ggLP~t[WT-Shjo>n}J|&$Yxr.)=E.Wnfy"s%T%,Ra
%;j6<#f+gI8G9IX
LfNEyYD&B>j43k=a%P$3z^w@Kn1Q=Da^_Ks8CS3PRCyt_3#f
0o%/%;k)O]NrhX.r@/oce`mV>]2Z!!1~B#L-y=-!!(ra->8$IPY|pWdGM`=DAg>F.A?:LKOqE%PO39wNR~>_3nG4>aA,9zPqq"/4c
^o$$10w58[C]I)d5TT1hjl!a9(&fm(E!gy
f0XP1-a)Y,BLvW5S{LedwZ?i:=>$V8K8&Z.hYgSv+P*E6#M27,}?UyiT,GN]HYpk@:5d+np?"BI3W_X<c2s;J`q$x4xxk.);9p5%e+$3jK@PJ,R*gRIe.dBK}E)?OqgX/i+vee=?eDUTXGj!g4:6k::TB/>Q,V62X5Y!nMj<O[^Q7I_37&FW{O7;PUn*~K|tA,Jud]=4=0`E4tF,qdD8y/64Q4dsnZ53XR(=yD"tKN8VuaCThaDd0^cRA%sB&W*2HsoXP,ZHqFY3f`4h/i,Hy<FxKf98qqM`A;Rw[a{Z@jsKEvP/z^/2*05uBSgdR
w(<?lThH7#14F4xq{crhlO4L(+oEh-NeM<=5!EM(1N2QVj[.bz#1IB#Cr*k-|
8U2^q/3Ac&)$Kup,v9Q(QZQZ@^x)5GXpiK0Z"WW)WbP9QX"u=Q!af`-.r+I%BMIJ-T[Rrun6d]VC=j=G{E_rqVK(~I]=O^iBCA~5wFoluadCZ(f#Ask0`=%@Ly2QsIA!gOm]YZqm>!Awt%eUp*-L.mkQh4>f5%
y#&o*sh`lT6?me^/EkFLCzgl+DQ{5S;%=.k=?g)o7+"?olD}R9j#%rTrPh1t04U-8|Z<+7<";IDm;iE}Xk!TFJ3jP1_abr/;Y`Q>"MFl4ja?E=&~3!1qu]Z[",Ci4QeXCfEe${.7LN#ehiT<IPoN;Q2b?]yeN3)
4g;el)>~&E)g
t+qUwTd.yTG*Yk0Ew2rYr@P>~U5DXI(ZKkvR:oT@+Y1ah*.>w#;#(7E:J6.Qq#wz"KNd~g!#614qYGkWgDGJh$=s4J{@Z^{%?]3J&e,s+5$LaUJp
d68_
4&z<w0Nck_>XTdug0PqFt#XLLa|p;YKDULlQHlI]8yF6kP,g??m>&FurebsW{NSuM@~HJS*Z+.!fcF^*KE]+rOZ-gK5F.
1NTcX2S6?J;U3^#P-="=
$]><i.CCK#T#Cmi*@FiZU(Id4`6DihnP9_>-:AmF#Llp41<Uq5[9/zHR$C0?#k)R&N,(4y:7Z|m*5N-E:B`r#IF%U$y5<7"
P9al!c[nW}ok6jMpLyk1t3CigE];R6(Km(%W/P?Fxa)x_`d#*$+Kq76_1i^v4(8UocrU*N7Sn6"BF0fiV
#44:G6QgT9yoDhdtxNC_&[R}&gg*"E&z<GX6<a5X3VlXv^rgi*_[=H/%5ou5RSyt:6!PTS]?el+q`A9KJ6]A<k-2FFOkA+P7L4Q:%M5d_s;3N|AF=+lrer=-l".T;3D&[;u+X>/r1!lr-c(kx4S"Yh,5P{.MP`Cq<CF#1`u$r-ShCjg$N~"=?qAeib.$7b^lW%SF$,<VQSd.M&KE(b?I*+5h*;EZ(ZS!NQ)Ef{-ZpN1zN@pH.%jW[n>.ens}Fs
#9yBn!q^$+e_-?SC:e+1r=XE%i_u[u*9~4EGQY(:3i"r[uPLj20Y~"gJuK6]aQX
iYqDN=hRR0*uiH
>@=p^8NXumq.rx+lx]U@2&n|<?ViVQLZN^D44k$~DD(}eRoyAt?!=aEG9vr5&VQBG9YP5o+`20Nb.K=4fF)~7"pUU?%SWDb_Fb:Qx&Cx!|;Z#j_;lwQfGG8^?`$DlTDmPj:
)wEFrY1
`rULy|-ufn8|)Dsz&CH51l9"fE8pNET}3Sjwr@pWj)@;EM@Tmp,$IO!/&FHIDB]@j#_O$?YiA9_-*_S5lX7&Z3l>!zTp(/U]
rp"YgSf%x)Ph9
%3
Nh8[Qu-b$QY|m~s+!T_s7K9Ur/@-1Y>7mu0FGX>}j]TL(@9:o8:,U7/|&Sq#[~+19XkDJe!ZD!B*EJ;Uby&@Uwir(]xC>{x-<M("E5!ZN,ugJ,m}grUqFVb~wmZv=@$=-Z03-A(p2vwx[=+D+Y>!2v*~`s-I7cN;2<1NEDi5c0*W#BO-H(7V_/ek[XGW:rb^x?OK]fV_SG5@+EAqMBEl5m)0SNInDm6I
@j6roPsV$p0q-=
xH!OnIF%H;R@Zz5)Yrvwl[cBk)Alv01~j&Y$4-t~.&6hLrJa
:T+KZI9:wNft;R}xp%>#jAa*IOv12CuR<_n_(@qOPeBGGU"Wr]3HCnaSdT"Pp/)U4T
twSm1}Q`ilDnwYIqwZaJ)34?da^BSGk.8X<ZxB#818W"-Z[d&F=*
n!
N9;Z81)x:,ww3K"B:
lhBeQ-FP"{=od]M8GZ4(f;EDu%D:bZ)[gR4LNd2(0-R?pGs.Az
@#(,G/3"NOTqT
Q1t(20`p5:frBDuH0heaR.*/Q4dGvNIiZ2UY?=^3@O}p%]A=-u$:75-kL@}=)Pu(C$lTEz!s+-WEFPK$O[P7g`8D<_Ix=#[Z:<Pxa%9G&`npnarDk9fEoGv!6D+0yon1<fPu-$Oed.G)*5H!
E]=vy.@@1A?
<V<)vx%)_)-JC]>;9XC5ww(#$e1IhviIF4OSG2c=#xP$-C_3]vT!;4_aaB]@hy1qKGF#r5e115Epco[XQdH$Ou$m8N+-oyQL-cY(`,
GnGAu;mVZ*2&F6xF;^XI8Y)973[g@Ef%#u7_2_(r]Ax$:grS>
%D,I.n/apj^!6_MM,cze0,lDKsn?IsmL?;o?&k~Am"hM`C4)2M;YGN|Yi@DUW_M?,"4S3FK.!kuFj,_/
[90q97PnH}rS,03ZkRa=GywG,243UhQ?u3=vUBgF`iO{ocXp:rIhi9JOk0@M<3$+"3X4@}I!fT?*0"NS@8[Xf2vVf=%9u=qH*.Ua#MC=Q|Q8r(ZYCXG)o:Bc$yVEL-Cff2@C*[CSku0bfLyGnhEUqwNxF!CoqU9g>
o
]-SW!I>4CAhHDw[:-z:*&f3I:(%m)[VhN|mgVu19@`&yR~E
]5u?F6B.H@veeKNw_l1Fj=qKu$g.
uRt(52pju=!*)!L:LT{noY:ohu#2gR)44RPck^|;|I%F_i71"W-A%A&)gY|pefT5tO$ELxx;mP8
YLpJ2.{N8G3tI(Fh^rZ]7OaV1!<`+-2wBV;8hQMcU3!x;yaj;@d31.p&1rHK>R6M8%Z1;V<*O9]y7^wtE-{68UVOMBeF`T~v+X_XSW[o>Pv"V7T*s-YZU^.ktfJ4[YxR8H3W+y(#XUmI}]W6wfxEatiMO&Kxt>#Y&&yr8OM-<9>#C^@7f^fd:k+N23RlgjM]/
]r#>T&Y@U<;V&(kD)VYgaS/Hf0iW%s14>t1>M3J?s/XkIoU44RJ+/Y|V$!)UqesR4VCog&^NR>L?^3ja;
+fi?==xTctlif"{gK2m$(Ubj"
.2a(UX]VKwrO9GIjEP<u+I`.f1|2n4%UCn6n/<FV+L_#!9%iORHg$W[4>R[oARc<Z
qJ*4{y:OBMi*cDJh]Ll_UZwgzy?$dYjwf48z%iNcIBnM;w*Ii:P4Q,5_r+hfh9
SQVmXzm3MKfCY9hjd@U}F~HmL`X0SB(ZTaD%
FH`]B
Xin+>q&#-Q;wWela+?`00tOfB>SaBD{6z+%rZX9Gg`8okqD%8HvlW@6-|1iE{
`tm/1`]t6:F16Xgm(^dI/
5#)Hdf~lEb?Q)G!gQCE!*K24FH9rW
QeT>3(p1cl,Gtm=XuvqfD&>CdO[G|e|Xjy@/5:uD+?cwmt?&o)jVMQl(Lg)
c^9FUZT!}KgtAn%O.Ys?eE^r-<+!4pUdl_w:yXg1NP.au,`sELG0}V.t#1E>~UB!R;|HO!jZ@W,En^>)-DAn"
uEii?q%)JCf3S;[qGP|X6H^elW62]i"+.(p]kBD
LHI:[th_y0"FZAF!!4]G/"T,j=%":1j;w+uTmN<&p*VhHlkFt?<d^w
-S6>L%;)MZ@}UDD<"IaZ495Hq~D-rkG[G2ckOrogV8$IZT@s3gE~-}"Y?5.D(BgF[A[IY-b;C5/xLAiBn1*Uw#,-;|2v24A{I`#wpv3Lxd5K?r<z4U#eQ2Q#8P<uIE_+TLljk2Qm^D,Z),<w7-9LXyYS:,"~:PZRp12*Anoy5|9}8^d<m%Gu^q18E>2Kxpvp;!nXsN*PIygHX0Un$Q"-8t2i<,<)?W1zQVg&9.:k$]ba(irR<:2x[^J]6Q5OVj.|p:0aRypR!Lgs.QMdN
hJ/42}-5)a1`HHI<^)SYsC*ju?:/L^<#+hIJ2(;jUqWqUfr=__L~H~Lqpv=sa[Vw%8.y79PMMvw|7]o}6d>3-EDW9h?SNsJEm8af(9BHSzPAf>Y9Mq.xy|JeW=baZM^#rMS;8@h5xB+dJ6D?9.ir$LP[=CXvxwO37;%I_K<~lB5nVLH!
e@soI!2!(=H?/CV283h2$x^p!j0Pj(f<~E(F+kR8^xWTA/+h3b6ZgPYf_IFug+*>Jd8juwzIi?uPe2Xi35rH~/jZH^[%FkMp{U~P3SIrCS_Hl,5/H9zQOr?-SE&=p1X>wuZlcoirX7q"k2R!KhY6PS>l`ACKn/|!-"&_BYgyt[;GVd7l&O6f-yB>M>Tn>R!H)5kSs6b
IMv-rF5F?"9u>Q;XOyEFW"T[5n-c2(nXzdKYUTu/I"r4^XvAk,Kv;x@o4vx9hFbV7%RgF@4Y*6`Rv(s;nQG;MV_FsT|4]dtvASTfE4p#k1;aJ*!Y`UJK2B6^"$/X!/_.XZ756(ngG:Z.cVu=l<kEwSf+Bj~3&TB?2m*BZLE^.VYn13KKacw@!WT0F%%T]*b#;`h&iWn0_Q;R9Yo`?%HgOtc[v03;JY3N/ri*EAC`DEN:I`gxvk/e4"0+xgpjLb%HM#oY|la:v*ryih4*f=TCl0<
fm
f/1"gT!eM6@2aIj5syA]J13sRf%IQw`)q$>P%Zfg*7a_]6BPEV4(3uIjEa-(j
mrC?ky4}xB*ARH^&F5PXAQ^V">yl"/v4<u$.jq%qK]VNp*t)-1`^:(A{4r`]:86Ooz4*1b+b#gu%wS)Z3vv9&"6ivrclT[H1pciBdivwN{ng^!wt0F3hA4+`n.t]N3C%9h/5v$)Ji%Y8S1"UIy/tkV`+Fv&PI/6xT$]&N!M_2U
X0cEdOY29e
+C7E!:X-/6*9_>AQ#8Q;@I&k4eP?03KUr]1J
:d/BRW}BC9#o|LrK
8Zpq:{4Z<Wi~fh&j
IR#4em*R^0@p5A}NVRCV&^G
+aQg]k,N&
f
@FA$Jf0Cewmml-58NV<K<(5FcfxG]7LrdT>bS8KUdQU>6h,!sm-]-a[qQBO)u*@<tn/s0GM`M;"S*0T>
"o^Z46_<5D_wKHg3JK<$P}b,<`s/;#5>:_2j<Td/3Luef9xZm9:`BHt1H>b.-
%yM0t..%iJKR;:@Uu]5c6v
]ED?>A_)K?@+!//P&PpT8F(;=T%!R?3%+#zg%OS
xZHl%4I&8*NdRFb<300I"Oq0!v$No(H6~[-WBVZy$]*+1TJ&5;4=zEK&BK@QHQ4aQ5g<%L{"q.xtUOB0xEK.EA~]_&CgAoP/2eFF7WNI"#Qs2i)kLAhb9z&Vq1$Ukim[mP*a@+<!;Ul!y`28H]~bS0<UKBUZ/V*5o[AYPWgK?U4,oFHIVm-AaD)h?.dAf@/jch>h^qSuCn{S8<byG<Z?!EPOp%_SxpB[0g{c9[On*=x-)<R_Djl)}GsR9wQa2legiV=/wtz"[,3wt1Ot)q*htRb*3u<-Q>o9mREE3Qsv9-^J|)MckiH=:4U%k_~g%0f#>ru.<!B("%E
r99
=w
TpJMbn&=EZ5fsI;)I:rJJAn.y.m_>i&lem:|K[x4W%BQu+?C5PnhVahsNg7gm8_#(0828K]VCLui8;QxDh/mdaORs5A!Ys?;=bW[8o/zF
?7i6"
Iy`T-.EAijXOK=NuPg]D>=VoT[E:g~44eaeD_]
/2G1MmnC0[9;A$T.
W"!--)/H1T[^o<P]C="x:f9[3|m<.!$G:"MK@rLMQZ(CAilk=HQarh9.Uy++>L86
EA.RJs8iucnqyum@B7Nrfi;Z}"S]L!CQ
bkHNoy0gnq
zB8$:W6.@a5d-1xk11ZZ2==9LN:G.oPgA(Fr?H~VJr@jtfiSlQEtyC`x^t;dIv<0):A+pIuaWm=p"T/C{@Ww
kFb4@Jtp[0*
fw32o2B|Ayn:ZOF,oJjJGI!/>"0q#])S!WYa55OdT*r{LZ^M<,j-Q}J&/,XWI"bLwBFO"3W"bKK?
0j$QTbAqdC[!5q[j~,r^|Oq`j_`5fTUcQWskZ=T)ZYa84[U4^
:]o5v4;Qas6C{IM_-pUwu)/^GRK5ioa+[B2UJ+x>`];i{:r%"g&&#W#cy$$_]cVXpIjGpS*h"Xho5M#3S3WWpW}LH3ui1G}0^^q6"6|9^(W-QUQc]:pV`2;U6;THH&l=qok+>^x5%wtI~tqsQ#|6^:<;UXj
T-+B9S~nL"
0Ef+Lj3!w22G2SY^wV2$&9"%^w=}4?*HGqSwb>_C%#4.))[Xxc@ZC$p1>bxe><7(a[@WY"_dbkNbkqUrH&^-!u2..b_u3K1].iS
pEi.K.Y$MiZMqY^$^(r}^tr]e62ue0evu{GdoVo$/0QX$1/o?)$
,A
A0Md@eYo]k?#1
rYz"51&I/lX]<cfeXb#tf2|P9`
v7<Sfi<7/NQ0KrRlumalifP3:)D"Af&-,0>2)L=gZY@O(K2U6{B3ee-`xWo]AT!Qm)STyzd/UeIMlyUC/_/N5M?t@?C?Y4p<iMmJ:ofa^u;$cVmeVF`[e/66wO`P7tWaaC
;_Udy&2a(1G!hJS&0mieYI[
v
{Sm0{_w(
]1NL0docp=D,b4_3b#$Bs7c&[lQ,Yi0.DqF[)cGJ=26X4qci&H@8x7BPZ
$V(]:?s$9U7gbl8Z_:T:L>8R(%<2[{7iqwSG[1fBVK_P`dlXbLGxu]lPUo9N?s7HUKw:LW(L%|#<WVx8
J_.V8:iXbYL<
_>@c0W#|=^ly_Za3t,Uk;*r@-VXb-j:.4q8=hf&EsmFBQz>{eNRMfpZ$>=cLNc1},7+;U6x!@c-,jqh9?Kw,Ff0EOp5AKe+yVG]dmSBD^NffEd1yiO4e.%=6OmrO
pxbC5/]Q(p$agV^rj)2c2FjI3R6#WiJ]H$IEDM3%w#}F;G"MmX/d+1T^9$|n#9te-PA+cbHi_24
TghaHZHm&UYJMYP7YvUpr>tx9.a=;=0,7Ge.>]nR$#SC,xZ1>@eqLI`rR]/<}=B1f+Qa-t`v_S+1}e9@)P/!0kbSXDp;p`Q)+/D8-0TFZ+HI@I-IL]VPGDx)p3x>8bcG$S[dqAw/;A!<Gp.YItTk#mHG}Y1giae#|]xH/7&[+(!vk&/Q4$+F1y=(s0y?#_:tpcn%K6OCyP!SxN_*w$N?S/i2)&H];sIubYtcUnBCA#}h:TV`py,01(%gi_u-`nJ19r2Q7^`O~;M4!s=PNM4D^^n"v6Jb+Im<WQfDA2yrq3(kj&g),fe1-%KM&HvFGopWc+9;y$Gd:S$y$LSJHqq1dAj
ue,YR%]DG^{%0E^5/ce;Ga{=Lu*1+
y&DMcK/IHBH3J]6M
>o!"Rc.)OQA}"hXJE50h7ERwE7&BS#1
C)Sasv>[,I`3"/nbMy;.PJofy}_3`5f*iC3E]MCIY,i?g<9%`n[Z-&@`03&f:_&`TT2y]cxd3Dp2:o
mRZ@"G
7g`
GfT`0%PGF25
r%00kAv%d0
cvI-qUo%S.,8n3Ank80[K7gxtD^0[Zk="7.TvJ%<sRZcFwxYRIV
kr@0EAm$*,i1l
w;&:L8oh"6iOEMy)xc)krM+m6Gy-xvl[uBbSN>TiqlL"|c<<e8CD5JwK>({QJ+VC[),X}+irO`0r#vO@4BHDns+[!I"5~x~[V8RI7j]4[`!DkroP.wBhBmec?[V(oeLd_>i#I"WVGq1gc^{0LvOhHQT*3#vl&OrQ*7EMSE.JVh75@Zzq9.JU[65Zn2FQi&&>_Y[?4
.;x^a+)fh/#&!TB#~
k-#
p]E`lln0:1R`HQ5`jY=feWa](V)Bf74(:$=-T-j/v^g?2uU_,v
>N2$V?Uxi2)<)Vg9WS_z+rF(deP!IBeK"~iK!)W3IM]UBd5]Vl5[LD5lIU
z/rA#b/UuKu257E?a(|<aKMRXTkpAt4Z<t<QPwx;[Ph5RFa[77av:"fejhfg;1`Ub^7Tvs@8L.&1"ObUHWidnq`pJ*cP.v&
8$K(=#q)=4j0sq^QF=|6uRs=P=55g?}J&C`Aurs/oY7_FN&dw4
bG>zk`2&Vwd$DQO+-l?cWea#k2Ad
6Z%?d>-6-[1)=tYjo*alKr,;L<Lg&eQeX1DifDZ>8X!5<ZF>^2*A_[wuT@U)nZVJfetQXDK&i2~Mm2Y*n2OS<qwb03RysHj/fRxTii&7:w{diM1W?X`2/Xn8W6SC|(Y1`#M]_nvQ.Mwv6>i)tDLQ|Eu=Dyl*mEO/eGB0EE*1e#a6|?K"T76A&5Ej-U%n8qX!z_veF1PuNu@,kb=,UO(1Tvzw::bX+A]1IS`y.UAWS6;tCA;:2b+AI>;/jK~VZK8>)g}Bl%yl=/&>YMxtlP:5+nP^<#/jM-;o`RTrKeV+}ps/_7DrwQZYAFIV{py*F3Ss+Yg+Q?RPVs`p"?a!DBL8RUR>eWcEuW6vbyyjBb__OwIXT:`e55To2n436.ri"OYr&<7cWPvj!qq.Y)4OB#v+KcXSt^]%,O"+ow`o-');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
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
Ah@62ejtRf)GfxCebyiTt$!s#eHtX');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$Ti=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$Ti=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($Ti["bytes_processed"])?[$Ti["bytes_processed"],$Ti["content_length"]]:[]);exit;}if(session_status()==PHP_SESSION_NONE){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(['lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax']);else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",PHP_VERSION_ID>=70100?-1:16);function
lang($s,$kh=null){$_a=func_get_args();$_a[0]=Lang::$translations[$s]?:$s;return
call_user_func_array('Adminer\lang_format',$_a);}function
lang_format($Hl,$kh=null){if(is_array($Hl)){$F=($kh==1?0:(LANG=='cs'||LANG=='sk'?($kh&&$kh<5?1:2):(LANG=='fr'?(!$kh?0:1):(LANG=='pl'?($kh%10>1&&$kh%10<5&&$kh/10%10!=1?1:2):(LANG=='sl'?($kh%100==1?0:($kh%100==2?1:($kh%100==3||$kh%100==4?2:3))):(LANG=='lt'?($kh%10==1&&$kh%100!=11?0:($kh%10>1&&$kh/10%10!=1?1:2)):(LANG=='lv'?($kh%10==1&&$kh%100!=11?0:($kh?1:2)):(LANG=='ro'?(!$kh||($kh%100>0&&$kh%100<20)?1:2):(in_array(LANG,['bs','hr','ru','sr','uk'])?($kh%10==1&&$kh%100!=11?0:($kh%10>1&&$kh%10<5&&$kh/10%10!=1?1:2)):1)))))))));$Hl=$Hl[$F];}$Hl=str_replace("'",'’',$Hl);$_a=func_get_args();array_shift($_a);$Ud=str_replace("%d","%s",$Hl);if($Ud!=$Hl)$_a[0]=format_number($kh);return
vsprintf($Ud,$_a);}function
langs(){return['en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',];}function
switch_lang(){echo"<form method='post'>\n<div id='lang'>","<label>".lang(30).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(31)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=[];preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$fg,PREG_SET_ORDER);foreach($fg
as$_)$ha[$_[1]]=(isset($_[3])?$_[3]:1);arsort($ha);foreach($ha
as$v=>$Ui){if(idx(langs(),$v)){$ba=$v;break;}$v=preg_replace('~-.*~','',$v);if(!isset($ha[$v])&&idx(langs(),$v)){$ba=$v;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($Hf){switch($Hf){case"en":return'$X/,-aPmTC"L?"s!
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
02*6o+s0v@>*4NL+l//q]C&LP}wlB^mM5Ox(/!xl@n&bMT7KJgdrm[NKZPX}qFmQv;
eoX"7U4UDKNdk832zI7X
8*34j.7/=%QC.
>Lb>[i:+(6>6U/0_TI6v)GFjA#`<(><
#[Nk^$m(p|9}quA53YF#^p.R^m0yE/1kEFNaT`85r|Qeux/KFE^]ixbb7Xp#LmG"[~7ngH(f[e%g=hs*c-T
i?*R8HmQ`$I_<lRo`@fvQz)x,:X}AZ+L?SwktR9++&e]U@A$J|%y>(=ljbg_w=7uxeIgC[#%vkG}g4=hoAy+Pf){jN,^Z9br/&eR3TWF&)Z;ETI7]UK%<.dmi}h=K8+^w)0@e61OfG3hvgYM3!hl;p"Xv`0g=6YCl}LZ#uycb_S)]=+b4}hN=l
tXD=b`NEb*lfsefdEG"Q~D(<Y(3kg@c6b]Th+UmoZh+X!svSE>O4>iZ)V4;A;M<le0Yroxb?qbD)prO6xhHrUp}uxF[RYp<eHx!4U,<+Mf65sfQfSFj[Bq?
Tq}O6PJk`=KY.."NOIE]ENavb$xl8#GCFa^P0%vpz?z
L+ipLlJmfx&E~JOGvA5&T8mJY0MS[UpD]^|#i(GF~AaLpcxHiM<W{Ui
]ra@Jy6%Fj;KFV2Aem86(dqWr-t=/U5
p
}[abNF{Zs?(_Y.%<3vWPDjx2hIRf*$
nNbz71c8&sxm1;naYM5"4,s%h,I3]!tJD`TP4]hG^?ivTESLw?F%Vd
Q44GCV
FL*EZ)/A^:?M+X195p<O*c``GXP0:]gGQ6u}uq#w<A1d+Aj17[g>KTw2-xdE=
?[gIvkj;#/7
jgp~LD*bn0DZx_xV_7nQusc
J!MN]&9rvChy:6:bxh4KO9qMY)t<ni#OJaaMcBlBl:It5eKv+Bj[F;DIfN(G$&kX^*LrH
8ZoGIpp7,x#}(#g8&<>Jndc2
/j5#E0_WEFx2l%EZ#K_Dpg#ReZ[xGOimKdxqp4ChnjV8h-D:7F6Z-0SN+N@Vo".rLr*yo7siaty5hGqIp*nGfKM,G@*J=luK4[%2X!fj]AbN[G%e--yAt<|0pY[(5L#g+b<uCp1;[BZoCE5osF4#l3
VzsmETe0RhiS:$7Y<90M
t@X(E<d(0ULDeLqqffI[3VH*@/sB#!3D)&7-;
Z_$w,XHWx[-.>BdQ`)s(w3Cv0VHF5yx`;"oxDL.QFcj3crP;5t(E_hb3,[jL4AH$!!W6/u}H-Th^5MQpS:c9y8"o</*8*dA/!6;0isQ%J,ShSG4>ji%O3,DPDC[<;gv%8JBxNLv-J_rXvEPgj0sHR@$O&:NnT]E*P4g4s::7r,DVU#yR0O.78kTY+!tybQ_L|S
Pi
NXW0Ux#%B!dlyHXh7ya%.=~gA25=lH?wsBPX23pOIu{$Qaxw-&Y%yiT4H%1bnHF_|$x/GD{ULX-FdkCUO*dbfT_raWW_qecvI4p%OA.j!Q7?vAu<BR$C*G{f$EnK)l~sdsdV5OOve*"kYu*<S>q8N5gI65
0rR9jI+|lg0CVBj{u^gK63:?j4b(HV2:ThEK/t?qc60QdN.*G1LC=Gn"iZV?Rb!-YsF7%07^$
;mre-;[M)RfBrVu97t)(T6"*C)qoJI+_$Nusk.(?>Y4*7Mn9pEvx,sluXwrk,[!Mg0ZMw{5t;<Ey[nGKC(bxc0MFAQ#>3fEL3P#@A_e?EvtlEB@bL[o^3R=5s4bEBUixFQ=DcCOU!L@icf-Jd$QnlZT^6LLS#CV<cth7ZxiBIOfq[%_II~&xNbT~qF3xmrpD>s?`G|(rL7]4w~;
v[$jkF&(@+c3?kh2Z0TA<TKlczK[&UW6>OGU2?biDw#(N-FJn^0+n@N[op?KT/*s8E1vf,cCxfL2lzhgrbgFJ`&)WHBY9qTmKWl$uwS>L+f!1"Hr!VjB4KB(`"/0d{!oNKa}81/j3fF0HG-Snx%g
_ouS$sC03V>+6&C!TQ#XiA<:`#088-5DdN~mKXb.)`oXVE_R]+wfjrABlZ+Mo`SB&-Bep[3vV:E$z^mi[Oo[5lf@859_uuk8z`{=i]BsX0kK6U`Gt1bg$!Nc/m"g$xZ0TnOVWR=Q5,eBkC@MX/qujfwkdXo^*7W,8w&+&o80[*yo=';case"id":return'-Zu;Bcs+>&)krdk9C0G8;F?V2@T$tH7S7f<i%VS3NJ1A4U_>&mnxcn990/U]~U2[=@@@POe^LySm>qKI-dj/C[&o}63bOWEnPHMEV`InGdO5;g|!eZ)-8pU4pC#4iX#9IG{+@nf1`ygJCrrxasU@w9db*ty7m"?-xhZ/Ju5t,:YF;hQ&S
s[IA
M(]12c$93KelvKiNGWM`JDfY-(o(YbCt/P9En)d6n8vs&.vu[xRcvFcBH3)q0+M4PqPcqqP}q:X]mYj
YY%QspLdh.-JvlHE4gYQN1n4>(EX6*AiQ}#Mp`^DXBllM2O9Yw,Z6yLwCo:s$2,9"Xac^L8g$wwr$xgSwaHcjn3P]n.U=KEfS7Mj;k,(yYin[N;4V=--Pfu2[vM#iA=Z<$3QoZ8]d+=JiqDuF!a|Gnb%r/tM)mYn.m6V?NT:%{(h&
[k_4pfc+tB40tU*~Gka#J^#c_Bj/OqqMT.)V:<hm4Ue:]<DWssXwZ1#hqM-7v9uBOU<Nvw$STi&?svgCc^rBgnkd>f+&]&6rbmGRL,1R!ob]AJ#M.+,g>t8{D^=15L-EC!j[NpOGh]/2l!xpz&)nkA6YF,wcS.!-+}47)?8sN[bil8;0lc$_nD#2x!18]VM^%|/MPJ,JL:+h4bEsLGr7dg.!-:UwaJXiph*<q4okDY=;ll5/I9:9H`RPglC
,N$/wrC>_#XuhZO(-!pPX$
A$v;DLHfH#@N/#E>fu?*}vk%dDhxC]gOFh{e.pF1DNnDz0Wq->C$b,^Yg<JSyt}3ApJEaPDiG%{-}w#%%pN5PsJm[,dC#-)Fgju4,R;MO"@BJnZ9e"9^"0`n?C>i(cUaM4<jp@nwb-{8/Jxq^9`g.5ppm;IkA,*4lQ[RP_t-E&p<"-}6S$+C/`E49yg9VKMq6G.epb5U1>Hd$5U)l$LJvaCwQN27DLz-y^O*:p6s2Wx3?>8PD)krIkTUe3We]S6/=`1&,j^2Mp&i{=F%PQvP?p
)_PuD22xqJ6KjD#q<@p)J34`LMN^`a"7NQ"f_yEM^4)~*S1}yMoNPh$<)T.x4st$+15*O:?xjM)lpAhm5ww;[*c6Aw4dR@P.?wCL00hRATh"IPtAy~iX8.RcPSl(CakMT#_`<|h]SNAL#5qUdi&q[~oDFTyE=#4{+7D71%J-F3TG]qBad%BE1LGpXc(U"{VB0{I#gz]Uar,OZ@pPKWD!+Vg)h,lx=?R6#f(6y?3j@ey6#H%%L.@QSF(gL&-FE:Z7t*xoee!g+E>%G+JbsXxOo
pb*H+`!/RD7G!%(RT6);-BSc.u"{R[4QjO5|e@(fPB
WUI`7nZ&g!x%6p<u|.tid4`Mj2_4:[h<8k1_`C%.US5__M%`,<0v)Gb=TC)5]n`^xg~eVcBpLc~_d=bkc:_"[MPDm?yvU.ME"(04!daJp,tDu?{I-6tZB5sUZ(_B_C(N"Sl<Br]p5q1*.$>ru:rM-Ot8o1K?cr!EcS_C0]Vvf`E`7FpZ]Aw^_.{.}1mE}P944G$4Im:QiG6!KnW"T4v:t0%t4XF(;8GFGI3lSAsCE,7wAFN9NUv)z(7cv.D0N.|5yIs9+Wsn[OvS
^p-FN?/ZP6s``"T1
oWsDfUOFpNQsK%|#%[rounCyP%iHrbfbA?%hoV
b30`oK,,+guStxxgy/kA6Ylhd/(GY,^~GPY16v%aU2Apt:b0gntiR+<x%uGki;S:>2e7(I3+-ZZrGu:^=(#WS
KLSS96`%">du
`fc[9&LCbdkLuc7,R5bx?"&Y%^JPvy;hM6]e9/I+WpJ7Hk:a/I<;5`."3k4??mA=d)3I3Nb#cgs?MoWgAwmcMe+gFS)Z_cW9o4-teJ*B$v~JrC+HD0:Wh1s#L_To.?
e2IFrhmT,`p~ch;rLLm_9b/n)fg?g`,iIs.^h)@."t-&Q>&rm,73`zv0km=2/96y(zKV9~5{h{yBQbjT)Y3TI/G,jv*6WZ87[xJCZBRNqAi|va9Lyls
P5B7CPe,f@*DpmI9q;E[lpWX=~i;sz
kDjl&:%*NpPA")JR@UWo;`?LL%8n39+_IW$&noyh1ZPyNbv?XI,aV2D#`BcGK&Mb
nq(#8Q-M.AGNH?8%Vni[fnz(wshR9e"TKoBK7/a^T=
A6rr0y:g#Y9OcX]@qC*9<y-TvMUv6=xMOrck
3j0(?cgf,~&}W.gF!|&>dCxw]TIN+!*2jdq+bJNhI}h+<7DlWw3_&JV1PA7;J?[P6a#/SiLXl2tH;VI^:0l:K&sp0T`~r/WBn"!F=upMAqhNZ&]YbJc$U2kB!PDTlTT0SfV~R|qHy">}V)G!3"^psAiPePvM=-e1K=C>o
I#mNY;d.9!_iaN0euwmz/eg#r2k;DEWj>5WHo}_G1e^PKb-
i3VePs<%5.,rf<rWl}eI_}nxEGL(RF+.n.?t2m@k9dH`sL#o4_:XY&$)8X#GYhY,;[]Ruv4<<;IS0Zr:V}`I+I6D=,!SGf/_uv;[CZbf#-bmB>[*Y^j07?[9D|$@0Vw0/xZM<k.9vBsjBTj#e8i)xeJ6[xA.v5Lql6v4HrpK"Tel!joq$^^VV|k~NUh<XQw@"-*C[JbDF!jT5]=A#Yq:=L[XV0x0AcS7y*BWRT0rHvC=Lw6/`^$id`^qQ"?`0Lp^-A?FJT)?<b<cj;jppdaAaJ%_7{*Ut#7j-cN:eKL>g2xd8$';case"ms":return'"Zu@B6KWB&)kgn,:H4XmLR9UrGT9y;~_Qk%2@VOp&E=k`?!/+yF@4m|gSRdTly-OV<YApdD*xx~q6vrIn#GJt[&rekR>QZ%ibWyA[$Se3o:2A6gmrO/nZEdu}_fNRSFfd8S),VwRofo"v]fuEGq"}s]7uF,]Nv!!n
)coyAN.kk0JBWQwsX@Z4Bo7F5$Cl%9&jafCM-l;`(JW^exfe^__jR>U(kS=s&=j!HfYq0tM?!2&tXA_m>ey+E:p6Xt+=e?FO.&X5@u1@VP{Z(Fn_@^qugA`n|t/>]5qK>RL-ec.[4*7[vl{!8"++MVcoc5(+O9c#HlFLP,6y]9fiDpS4,Q?ZIr]dpdagG&-QFD#SQW%On05#jQ$.zvju[gX[yb3k}5M^m=1Y!l/
JOE"ZI.VP3)7wt_C7Jig)f<&/>
-ZD:(L?Gi3)Ar-S?EgY53Yf^*`g,tL;J3}a8q;6kEe5r.]Zyj.WjJkc^^r2JRlx,Yfdc0~>_$ei;"`Y9cUo>6LVelA`R,TcFtrt7TCKQ7yJF`}C1,ki,dc<Q?UB"aJ&Ix:+n1ViY@?AK!t]nxP)myAUb7H:Kq=0AI"N^y~`CJ|o%PLZ0vzV!p5%u..7A]sOU5sHttMQwUgdf4am:JBTZxMozk2%U;MPg&xJE)a)/BL?}k=v0x7nbrh+23"cVh/[V?{lRr1dvcA_x
^_#[BlAH1L`f?_Q(qmX^BRG^?CBrR?nxHM+B3NZw]<gy/:=S);#kAo>?</YD9ds;p3#;eLTfix.$5pHv40,yA0fP7
ak_7Tj9t#bLf?/
(0R+^5GC^I5J)z=7F^f^HVGuHve65K6/`6EaI{u=iEYP>}W_NyYXAIwmFgmt>q4aB@He)N$Et$Y#J
OPEHM^Z<d`XRN[PZ-2]Uf1k9x)v}mpXi6fe6_g9>K`CACox$vlaPf5p31e>`*+Y(d~PS^&RW!<n)/H7^R|JS>XS$1yF5JMZgm);A?0M=["$#k7%5g~2BS(Sp:b&Ga&o[&_-to3)|/TN0l_:/>vWk8!
k:N91MC$#GnaQgkFR[_*;%oYc0~MrYpy"fl
)w}%I_sAy);j1GhxAeDg?bRa@NT!m"]wnn>!!2*i_]O=+"Ew+J[#C_xOL5w0K3^@-xVpo-$w~/WAgrgi8g2crR8s9N;4WXAV<EccvFTD=wTW6AuPWrX9vOk)qi
*&2c5Lj#.qK)mKy{>N8A<lTV*iG}Q*%Nv+TO[Ilz.PI_^z$H=gT,R(?Q0F5DNH5P^E(1q&:|A"okIiW;,-8!aL_
Yatbk^6a%8<~jz6,wx(~Rg3<=8=CC1!{19H}e+%[q"#6H`ksd;_[@5)q0zjsKF_
,@KhS[>q;b,BT
LJY])wRjxT-}%qi
1d[ulMJ-e
hFK(oOYTS(P1V6"LHY3>xw(e:rvGk0GQy>>.=}o6R5#$#spWK1TUkL
Pk;`176WPG"yOAjeoNfUG&b2rq,(6$tFw?+FxKwRO@$
$:iSnFRG`B|_8`/7qVnCw-$D0u{6GipK/ZRXp8@6zib5fmTN{Q4gQ$Cd9uF0u4|qk3A:qNhUS%J^Zy3yOaOAv%c822};_#q81lWy[^|VfVo6zhea#CvS:m~</:}Z[J!nfDImlAS[K1W+J"]+
ICQ.USC"M,CaJMY1TOM4<k@-l/(;r}Sg[gX<StOG32>4WcUr=12{JkCai_(~t:)#3
Tk;U1yj-=[h|T{/Ir`h+k"Cujh_ICo3OKE&rS&*/uvB{&Q9b(<ogXfPHD5N)49oO4+rULjNe>Xh@OQL;vt)[l1YqIt8id9)dLT@9pefKHo@.-GKkT3
7!3_7?w$"OjyRabBw?4Me@U+}Zv,hZjK.aBDcy?J[^1^r#/-QdWtbViut[oCoF#u$)z1LMuq<g[9freNT+F<uRG7m>HpwLt_HksU[Y>YZrGQsq&W_*vxw!PFvsVG_kZ_S<io+OO>BMR6|xZbadR[[=uM:<NV6CRu+eEPL`)wtonEJf-KcuBZ!9jAFe/20E<p5vlpr@+2"aXVq(v:&1:d)[_e4t~*43?"/TQ4ydbmT-`rAh=r5"<_=E@um(-I]U!i}e{J@H8KK83I`8N)yq"ARxk%[(0h>J<ymuKWVfh""5z4?<nwR*%U`gH[qW|OQ8v(FX`Yx]13f>0vZCb`qEZJ!
(4"b_?<z%j$YAZkD*uG+5h/HjjkNYOt?BO#hltgjw::+rHi.bLOw
icMI)[+bIB+}_Wvd:Tk=91x5::.nBrjMMR:c%9I]sBH#]nvvlVImaR+9kwC>l6TBeRiBjq:+jp7D81+v=ilwO<:Jle1OK{urE<$aeX&)i/piHGu59Am5[K]^5^"m(NX^.7<,nj;>?d^"q<=*3gEG_U0^*Nk[bA<LaSBcly=n`C`_%Y]rUhPUx9nR[eh+WBn*]bp]&H!JOhD>UVFlaHbXbtXM49-[b3A+9tStA)&k>Ir4K,wS(~&iK)[mXTb|x|.jA]cZ(#5%utBQ(^Zv0pg;Eln<9GA"n=t|
Na.(F7dP!4[YG?9cQpdD=Lfa?D50P2<?`;eb][fh$_pacQs*W9kCl:7e,>T8
;W_,AoIY9@q%T].Tw~
pUD7T
<[P>JR?e#<n%4E[YNr}gfK&)wIL/AnwLxHFMs=JD}F=84m)=k%C8%tmu1NbK^bFh{Rb;3Y`-l%1(Mn<ZZu)u,>yiTT<%b#f5BrFm2d/';case"bs":return'!Zu@qbP+^.B`ofy"Id-j;ZzLSQ}Tg2Ph=RY,YP#``c<90#u1X
@G)NyS#C@]>p`Y$B`HI?k.M@tQP^#f);>9rAF](u&x%UfMmsqJ9APL^$4`s.E^!6e]4$Ii=9>_ax/jUs}xB>@]iG_FkAXxx#<j[K9f,c^DE+|tPi}U85*y1D.x,e~X++%]Lbor>;Pmj%OZ3p_4hW}1lj|1eqNYs>cDWs^#5d|%z$4Zp_0Lh7^wuFzgtn)@!*#Vyd~ae/H*C?$K~,;Q*3JS@AlNun{DCfPrFGm[JGbGSvj,@)<%06<v,O24Aus`>C+aRi]")SHYl(sxGpI_NE1ET]`$ie;eyV4"mu,Wv
eDv4BrK$eRriJH:-A"vAMx5I2"`d">!k!U!<{btMaxpX#hjHNZ`ZO$QDTg}%KSLx?oz!H=LEBNs,c;}Z_OF"!X!Whf9L_A}eZw>!Wh.P
D#y
&+_&eVgIBs$>yXjfo^OGLM%"Pxu1(AJoZ$[dD4oiAz^&wE#z40^<*rOn@K5X3"R4`a<h,(q>%oLohpA7HnOji]lWcu:thy
h72b{yt9|h96VOBq(7FHyf-xL`|SsQ;HH",)a3z?dvd[?5
J`[fu^T*L/-*Q[jGT5]>*ab:/-F%GDx<k
&(<zmjls>jH;Flm:KT+oU{+#gd5o^reZ4E(C@Xp]HE%9;6ml!IjWT`s
):sGJw.&nuO<tkoC/p.Whe"PDb6?"|+}qWokfF+NLHlcLn>YSf7Jcw%uYyaW(>]K,T0RcqYEx%tfmv<<nh7uf63mF&]m"Y![pBjUH@^MGK22-}f+(w97]rBIMk_4a_%si]
|QDsleoa^fF?Q/TeV>It$H~EvVF>8j_96YW@0l62sHo"ub|oIaSjFD^FC"0Bn?u3vlTMaQZy9XFB3uM[Uq*)cdE0Afevr1gL=-]*=Y;RLn6LoiN]g3$G[yE)fCXV9R^b-r"q0i+d^HT5)Y0JhT/UAs/H/mt
+GxVk_y<xC!q"V=>a85F?VTxAR&&!389Y&S$a2-FGRmGk``yfI4jB;|@Rx*f/b%gM9ZbmBvBkEKik<S34K8%b0ddT3"0W.$S@RNZU)U8a<0!I.:%4I{P{a}l)8NxN&%t6eN[Oyw%9M)gRwR
S96$x;FHLb
Nw,!1+g/ZLGo2!Zl-]Sd;*Y7C+[6>waH4Q<~-%9;]Sr:gRP6q^E3gw7gb=s*ubu:BpL#O)K-R?3X`3Y"!7@eU<C$waok;(6:#TCOe_g^W`F=biG^pZ]Dj$*cmg"gka^5OfSN2_2{c%`<2&"ODO&F7iJRmwL{YxSNQMy:sT&Gitm@II^62KRVibK0R_z%vKu;0dB9;2/!#s^[)=J69vN_ahQ/+]/W
O4vr.&0:Z<F[Vp($P?&>!J2tL/u(bQBr2])pC4.LFH;[:+~K5x`sNjX4gl/b$@]8$;+62fu9X7S4V:.+N9XX&6WLT7UDY
z]Wvn):Md5T#knY-M/:Nkg@#LX8gX[2k-_u2Wv+tC^q;~?
la
WBrLUla#G[~?<5_?Pz$
@Pr:>V<D7jO1oo+Kn3HEiRD:p9JGga@CSl^m
,%)*I^m5sV8a#UfC5btqM&Eqqh,w"Wbj>]J3?*^[/>o@N@Bt4
vR;CuLVz9=I|MkK+#|`M6ZQ75`.O)xqb4vD;lP)_"V$NwlHtDK<{c-XAnQ%04O:5)NRXf~r/lht`AddKaUw2[]TL+sU[Y5j5c]lnv>=_(0JJ,M,|4.sS=K59xByT00Tk&dCJv98CWcq|g$n_Js+?MC44=nZJ%+YYV[dW0+oX[]:T/UnlA2"iIj/gT48zd23/rOQ{e"At<tZ+l<,^3&Ou`<KO<(OM%KQl]a,B&LLKDSe>4~qYDxK@JoXMan/Jp;[BvG?b5
.I3bC.92l#N,RKwjJ]WVS8iHh_fKu![U/9+"]A1z[%(;_i[cB]0;Jp$b&<T6+7&:/{8u`uEKr}<zSQUkQQok4OSgdY<,#$C"!j_@NY"B59wVGNesZaB6n(.R]DA4*gD&ADlf<Ex5V#P0rNOJj{6EYg@6q$`t8Kh2z$`4CYrhW&elaebb2|$K0A<bUs5<96YDDhm3h4UW?0)1vH#RlB9ga)h:sXT<6zE5!%jv9)c!.Zz$:{^6_S8n9Kt<

(b_cduThRd`"f}2C?@l.nZ?Id"C]Db-}Gw%vofAu+qQ$Fk0.IY!)
jf9C*8?YQa,sY@kULJ
s2djGJhFwuhQC9<ylnu&KckGifPtQ<@0HM^3etG#BJS:V~paDAMd@W[_1&+O#l#Sy,ApI~M9,06Z]b>To.aW[}ytl51[X~oi[{d)V/Bm)J)g*A%hMG58]QrL
Rq/.%
G!
-e!.SG26sLcesg1K!p
B%dh3l`qu)/WP$<Ic-+7
DPMiACR9jX>$N6AP&18ZNfqT9D"m4.s..D_zEG9o8ahAJ
U/Tgo7R9!y():ec~_Z9s0a
"fv6H_1"R/DlZC6OSj~wF:jt3Nv^+aV-*f/=q$cXZH;6-!
a{4I:d"_-_7tYzi=@%TMBi?<a7o1jjKiwjW?%CUYbOeDz$MMq_<j<dwq6]J_;8ym38S?X@:S4Ops[/x;_307#I*ftJ5b,V#cYcNsUn)Z4LH.Q$
F2vYy/m7]kmz$<F<8t[m-WgNjRpJlv4*p
wW<nznn=nmP(_YQOjwfa=F]XU
$f@m8!RH+`}JxRfF5l9b*drxir8NLbcFns}(_UHp-%]c3F
gF(i)G>x&d(
%(s929[-M2H!iq<Aulj9oTed2+^HGnFH4mR?i`H@o|K|3#n!x|$1/8?(J8<EN)oP#@C:cH0?ry90v@s~#0c&=u)K<mcsnr6Tv-Z
4[G0M|$
&#Q&>yV&vB+i3:##_t+&0X_nKki=S9kz?mL!l0c8z!2o5O;cz%"a76!{H2vw.bUG@j,Mi,8eZf6sn(YHpj6d<C@@Vd&GlKRed;Pg3FXN!Dea*5BN,iF
I&J*kIxEHw9%/-av]NlBKSpp@]Tp0%VwdJ4rkh/$DJla]hX>pITi=4t}8)]<U_!f9ymu^CM{!@u&aedtxp:G./uVSn$FhU[Rc.Ho$fOF.TG@<nV
+(Zi&Zlpxje1_5hWhTuD`P`M_$e(q8j|Y[&FpYfXnAQnwwLKXI5o.x=deFu4_)RGbUxhe-ES!vpSF1=Qt=>wAIU)+emW=eWpT;iGh}W)48S0dYZp`^Y"&N';case"ca":return'!]^@j5IAp(m@+[}&]iixX+S>&ughMlzbi_0^WTX]!pvj[Sh
gO3[AL]STdFq[nfN2s8dXCbanlp:2T@`Gb^c"L2411IR~P4g*lmyxeX$wjib?P=2iL~^AtHC,wPM3w.YD*UX~5g,CuzuJ>4_gMfS/1NI[
[4*O[T?b2sdKdu{lyg8:fx|7
N0?Wu9/AWk@ulxjKqPVWy{By.$,lW<!HZOqdiNG[Xa
OT-L^lbjOhj*s7ByH0~Y2rkBkX[M3X:P|_8=0s,!Cu&htfm"2i]EAI6Y=F
hUbzlUea%F;ot54|42WN$t
KG@*NRV#oAG"g+
M-26^ll[D4F4A.XKd1G?x5o
T#I^0rehLfZZaSh|ifNy@.BdP~>.oN/KxhM4]}G<Z3o~suA]m"+A$A`V+PT>A5I>C_be-a8b1Asa>%!5+
K2OsX
s0ydn(f[p;Ihefj7M(.+`;2Vx/V|:q-E7`v5?
,|RFWG<-oCrGO?f?fyngNoBT$6LgI[CQ4t@:SU@9dbd#x#M+Gg$7y.?>N?5.u<@>%{kYU:-Wg<MKLG3T0e
?qE-^%>gO@We+eJthx
2)n_4a^WywE8bho!BLTD`k@n)Z&VsYGN+9mrx[ZnrR/ID?Z|P/]{u~ViNt`&9AT5ZT9#O&<W^5-6]vI7l]5gZiHGSSF-VkX5F7"*7Y*frU/;!Na6c#KA[7@iDIgK3W"25>Nc2eVSB:2xcWj`Km
W@RabDxp,f"dyMs[tv1+4JF!l/2aQ4$,D4(ReKUL,*I
EErI4.U50YH
h]-X`Z!0K3#t=&e:$cZo#<=&qdzn}4_HPT6Bq`cP-owRn&HTh5@?p(zjQL@P=/Y.NF>TZbF(vV3"*;Gt3CcY<)^UaSeNUSvb/,dL)!srwz(G`m~.=leZIyS,~$)=3^BAiyBeT`8cx9~?v,ZmmJe@$fryqP4w)%JTr^Mpv>kKZ8Ho!o/b4)0,r&}l,V.[aWfGSAaGVRkw56P7G?%[Qxnk7&$0*LIE[#DSj/K
idm#~K96{BjkHiaJbjItmd(,yq`w5G[,(,5M=UM.2IeUSd4@J6qZEGh4/D{?<6;uIrYfudc*-d;ZKT6>!v,hpk_!OG5e]&EF}q`oDbtw}w>hiOhYxm!YWRJHfwAM*lN)E9|WGw"wPk/!XnAM)1riD%=v/w#&a$#hB4ukkUzcD(8X69c"cM<K
4=yDUjOTxvt36}yWgQh`qTw-<[9IN(C4;(ND]6j{4`i[5V2a:H,|D5wDX}w&tdXb<)J/-l2Sqz9~cTD8iQE>C$Fj=RxBqC5Y6kVQy!?zk{)g*iwOf`!lH&eJbduzRS0[e;j7^5E0i>QarYgXM%wtE+xmS8==04m|8xq:(+#@K}&mFf9r!>%Fm|O,pF[b$.uf0t>=-zPpac/a^FuWtGA?Ygf:3U-zxSga[~<L!DtTofQ%-_X+P@!O*%t<h2@Pl5kkC(=<&%hwYA93MCv^#`nM:nwMO`.mY.=6$nHJ>WR:L}hA/.`%ybm7A6wh:p.u*Iry"rAW6bEm*#o>8}I&Ag%XDjPo,Td,omN(
E+$p?sv]&[XLS3W5_:BchvC5m
`8(h8M~OUI`V6%:^co:;B&oGt<r.}]?grJ+[@vUK_GotvXS]Dd&s76>%.ls$1[PdH3k!@4O$Jn`7`b=%CR(*IR*wS$UO3Kar1q;gZa-OEH%iaX
HFvg*
)JLLVX!;XSNOJ)14c!P`ZBoMUB-3r.yq+t<AY#AF*rXt@6!uvfb"rq8i^smjST.z.oa<`x2H^~->$*Jrg-fpD96nM*%^v&@ri1`?,nf9$V!kn7kcSwfE&p6n9*:I#6)1v,5*y:Iu3jMdp#$NSO<K0^]8r;)j?&>pw2c+bi-M2oo"1l9Yga.hH;ts74AnZ&l8K&4}u5I1n})"_:,NMxgZRn^~y(=wNU``jFE&.#CKg!x,Y3eLy|u
B3/S3Ntl?9&pDl:3c;
;Y*Q24rcuW4s%>,kY,xN@j%2IJg
,MsfiV4F^p?n<GgM;g3t%_MPIRZG"D5Zd&(;.9m,XX;3r0mix93gXm)5ec/qI,GFsdp3t9O<=UX9}vjWPlri:Cm@[!U!L
014=*o$;;;Z:Wl:
9_4w]ve$7F1HOl4d+B?94<&e!W=V(sz!S(^L4BdtH4=HtMa@a*f[~>0T#?7V:G@<!>i:>@_f1IStrp_]OL[6ubDd!6XeI@R..*Rp@QOf/2Z>Nc2y%?tA@R)*~PZYJ-y0)*,Cax0o|p{<S;q#Im^ozoRc[j[rnO3+xn|t5[
-xFaB3/J1"NWNQW7%l;4Iwmt@@4oi]Q"+$v,G_t9fw(@SnRQh_3N!}TeHfq#4Hx2LUUf>1,DhPHG/!19TlqbZRJGZ2Ag+4g_7QwRCH+gepR?yub0r9ZL
Xt/.W);@1WB4Ro
_wl14!eI!Ba4JI:d@V1DB$N|j1YX8~9IQWwRu)bSGUX/lE01hINR;9@b`0W7l{1S)<.`RNevb1VU3N+,]avChfwz2U]SFVV%d;p4;Y:eZiMtBsx^y!@v5hNDx,AFnE7c]JCY0rKx:Eof`IIk6XEy1Q&kra!iJ"(!/_p1q:iHIxw@+9e]&2xCXH$3sw<Z8S2G:{GgT%M>)NQ0RHCNKPUK(6o[f:M[@B3nf%&d@.2E)h_`r}ZBu%n
UToSy<f5FSD
6L<Q2B_"x5pp1iqCtAt>i`rB1D6VT@Z$Av&rV7iG[;`9Kg);kaL[,}gNu)JhpJ?~-RK^9=R4qaj>M4JUrA-"S&?Twi<U+D*.@"<bq!VxK6itY~`~P-8/=JfS#WJp_q<uOa
lJN>C;=k3^21$;&KI*+;BS`Va(x
(fe:s]};X.NC[UA>ILeI#$J)QdTWDvCC9P]au5MN`?cC"@C#hJ`&?Ex:)")kv)"ZK4I._U@>+4G60l$>Oh~oqC4WWp.e>-F(jDn]YAYLdV>WE^SaN*zXXY36N=u.]@)gWT+1vP>*Rlc3<bkJ`Z9J`1m/EZ4A|1@&%_%IHqEB,dli(/p9S/$-M3G<S5[1mbN,(rld}Vu;
K,67nrd}iG/gsktC`NC}X3(a)80`kFOu[:r;A+%/`-I%upZSM03)=aY>4Y;|[wSVaXv@vkO`SzYhV<(T';case"cs":return'"]^@)bTDI@W!(ie"]Vx!QJ7*QE:#v98C.Uoo6UmT`kj/`JE8b&4BJ,VKf>EkM!Y6`O"Z$8-+Dxlc}W|IL_.?@AU"Op]W?`YHO5Ys&v-2=-/?^*htwF"1fj(]o<gdpIbd"KFym8Uw0Lk<Op1c?N
PKv7co!wI_h+kd@kWOj[tEPe_Hs!,nx*AV
2$8vPO&=^9%t7:_"rl.xu9Fc3EULX,TQtb@VK]/;^]!vo^FR_Pwv[Bj%.z"tOuv6FGPiY,vff#Y^,f]?+L5u?t*[dIIi17Rsl4_CX7-+PN472Ju&Wj+6FQbH-G2KpEt&_0_:I2>!,1AS,({mNZ!t62$dxjuACkXMtm@htiu6Ub:sa!om{(Mr-l6B05Le+HMe-3E^8[v)rfLhr-P7-5t]@P]9tg[_dT/V4*iD!u=q^MWu&lXlxyw_HU!MkOD/TU/DCAm^%Km5.eP3IbyJp0dLGc~XZm"t/%Nj;vKY`^DN>`^"IE[DO#[y@blE^d3G%uEp"KN]RMx;hGvX5xToTC`Qy-
2pX~UZPRf
!<u<Z|M,F+<e6fm8W.SU8ppIW[otn~`e7J7=jMpencSLgP1PD~D^anI[f%eB+_*2%0c;Kx,Me
43rTc]UB;-PjT5:M)at$,fHU1#tzDv^"s-@`j5JCTs@71K*+86#q)Lq6@^FgO)#VgauW=n^DYX?/)Xt0DIJLoy)E2xHe1U[%fyI.XxbcK.MJ#ReY^$`{*
5^lt>|<Rd=8p=`,Q1C3Cp]A5cCW++z
:na59a<l[eUGr<%cnN{Xmi{lv^dH{
Aqi6}x
_3O?6<d|djTn5WW,i)+jwV,C[lg#;i`"DxoD)W8>!^j2`0b*z%#?/w^g,PK*d$c8^B530%][
yo.QBq*6gkWsY])Whbw?Cm%tXD/KS`.b<>i`y?iK`m`QUt2_>KpA#v%,@ydZvk06MJl6r4x47CX;"ipGb%hy;n>a=#|m?M}D<`&ETVuao6d[Gdh=s$5wni772-OFuoM1uOYhem+?hS-F6wJILH=>{pv:-w=9^xxSoBDH;orf%?ld%I[[~u29^P<54=Xaobw&A>m]6LnBroCfhdJ1.ZgB!>h1Zgr[x_[UJ,{V)h}_+v6pKO[Y~Q;]R5T,}rfDNX~B-o{Q?UA6iMReIcyHd^hiQ!o*9Nbtat~M:(Nfw2Q920?qk?E<$#3YFvNgAiIujv0A~1deNX0/LmcR)r!#Xw|&kn.p5j/kZX[crK(4/x2s-)(Q<*c7At6E;bkaUCZwTypI2xe&k;m.@"Dz&=Nun=WtGyj4|WSF0$,+0e$%$A+,S?/JCCCAI(LQTpo,hMfs.!vG/7AX>6ZN5d|oK!vYn*6x#xW3)=HV7(Q>(V>v>(O`PG]iRv^2MsWC"X<Fc_nw
UA>3!=YUD^*UPo"5pn4xyh->@4%Sy!=vy2k6-TYa+DYj87d!n#(IXyP}PY_"Txb8.q$r-HPh`$6F"2E:9Qp/:Z/n"o(Hnz!0=_
r>-
aU!Gq`zWjMgJ"%+d@NBVCG"/y0OU[jHBi(Sea>!8
(qpGMme`gW93js:kg[#-&OFD(oCe**gHBd0uS|%W31nrY!YY<9b2
]b}$Segb#1_:>9^S]9rn6e"AB)N8IYV&>=NY~18=3(.<6qLHMw&$o#Nl8L/&KV1iy``.>F>x;%enFZ.I5F|o1_K9Q*$(*Mc
4e>nXJd=yY;eCMf>!Jta$EsK}qC(W08ag"lvZg?/+au-$Y3[mRc!Aq]OA/6U8U4n&N4OJK,U/$|"+ohextEjkmVN9HgtUL@)$"!-Ep9Stf6W+l3t&.ypbR"U`bw<l_G+3^<`0YiOr^w4JiqvL:z0{y+#P/if5eT/-uG>b,;-}bk.~816B.6VFl>R"hvW8#WK]Yr*x`pt@#E3L#SG&dU8kbFxydAo"$pmyIu.X$no%/aZ{)
-$P3R`1JFnVF@>/!ipP&=Y*rSpyW$FrCLr-sf:j3:20PcSD<C&iq;9`M`DrF8f*x.`MiqR[$*lo)m<,0"jaNY~Q#rF""(~x~jFEg9Ywj-k3b=o/*mSlZBzCkd,HzfSoP:RXrpehjdZ8O3V`{-y]0!_=iB%AY"k.x%6O%hl[{l-YhMpjc
svy:F*ztb)8S)Ycp9$E6f-npB5wl@WaD_Z(d9r$Y`$-!N(~[,E-,Fd^Jm/}1:rg-l$/e}O0iubSVG3?`)^[d|"JhLZZq{c`a
[}sd
QXqG%FivD!8HlUS3qR-eZOXL>wf3#7MK:hON#=O%#[;I>Pi@FI*4Jx@HJ6@(0MIOX.%11P9(s1l"JP7*SlIf)Y7fZ<MncmBX^u7<Cbayp=q[Vk1hq7"HiE}4<k3W-CqX-4UvTcI6a+Aa#SLh:"!Vu9&[Ai,"ThN
/I*5kRBU7SJI~ESj]cB"

O(sd~PF6e_!dW$3+hC.YU
v8N;KT:^d=9v8&N&3jOAJFvFsk96kw$+.dn-pkDFyWAr
rwLtTc5(%f.]fMgNsm]aT!#M@hLU$<mHt840w8RLB]*f<bkgxPsKo()p+}8[a
E><fBawe(xbs:BKA5C"x-Yt]2Ft)]hLzm*nA@k)C/bELS04GkJmUT("S/+tvr9/3TXU_b?EKFh0Q8D%8l=4$rFO@6?Y[;m/8cXsJdi8skoB;9h;7pO>#N}8.!Lv0YNJ`i&EBRQ&]<_+JmBW_`wE.5Pg.fwA|4k]a?2]=E!fv2nML=YU`+oWgxeEnet;]5[*iq6TDt&N1*EmoVF1h`l^X?}Xy,s!5JG@a-oyiK[xsON-SAb;KP4HErVVGvv77gl)AsB(.Ai6G@^duY:!qT.PT8RDH<SZ[enMWs+#L=*EsvWr-jgf^OP;!t(.{-Y3Uh~HuF:k3;vQ-!R`UE;O>.k_UW{69xo.bv1%tXo%LX/+SU0I<f*NgSM8JB/V1A;({/EqCs,$,[`b~68Yz3S7-bEbQXZdCG(`!o?wDSR>hxl;I(SG_;vSPx~iusyO#WNSm-XgThp0QqnS)S[j;5N"<c_:[l[P7e.x?9DS5Eiady9qd)*oWv&TOO%%#/fM%hzCns"s6T*j>x#t!QCJ$Si3g*lU%+*vM8/5|o0h$B<ZYhq9GVIHaXz9Y2~U&A=Q!D7b2LC&~hTh+UF?r:`Zz@z[*f%^v&lHYWr^8Ic/_+UP@7g=>W>i"3$vyvG5">h/6&X9hu9ZbVA8^WM#z6#sI<3I6DX5sH09RR^+XBT-q;oWoi1_IYO.cv1sR)i+glQ)q%:IH2pd(B~66d*_DCe&Iv&H<mt)*.;R#<:+~[iD^3W+~]*pB[.#RAs!;I)="]-r+fg*@q#<Pr7C{Vls|jRTJ1wtEC@%?!3KaJ~J1oH';case"da":return'%Z}5q6LD)@G^NU3OQc/CbN}dNd,*.><4p%_*6v+kksw02g}PA#QjW54(W3u]iIkujT<yKqjtG[0HEWpDMY339_dEel!Y&MY]+8vXQT?@F[iZpO/cbl!L>,PhIVUYvBZ_NYbm62;S<E`j(iE)AN@PJWaqfGmSTwT8s^$?u#MsP9J4As6pYZGCi-ZE58X<1OFXtZjF!B-rkz!w$z#op
{sCW4c3/pfEq;>.4dL6fW2dB6BEpB0X>j,+7Obj4ju
.-T51f#(+d3eZWUFh=]QdIH@nX.8%0!i0^/U;*u:1@ZB:Bxl[ciNZO.~!{;upd"$k&c&BEiuAWviPI]<gg8[t0!PkUqfdS^aw5a.SYWK`CBwNEUcf",-$NyQNd83OwH7*$C&TN4Vp/rdH}A*JP$ECNEfa6c<9w]P?t3MGqoGBj+<JSj!ACXp]p`!O>uXvd`$$pPD6#Eat%<cV.$l9$Exv}SQ"X1L18C2<0h3VGX03&5x]Qf)#`3%3K$r-d`y[gP2ZCiHo1i$kR)0n:m`V$,WL)%J[A?8&3BK=is%0tQ}wZKZ*is"!7Yc/tEJq,vtHeI(Y[ya,:h)lN;s*-lMK}eUd~:a;?A>eb9WTJ2MAK?&rMP_K#3/).3[Wd(|AyMAGbq<dn2+5,?f:-:HJ*5*VI9b_,_X,Xvu8|QE[`nMd57d
g#_<}].D)l"KT$}tZ!na/f#lA3M]{Uvv(7%=;6qA]QH;4mhm8j7v!I&Ljj0,5LvM0WUQx`i.a:CvBNEeBaB+`xwOo#
E~I>wpF0Sbmr1S<5qzD@c(L&P
4wt?`/!^n:hKkOiD=0bYC*[(GDGHCCl"+JgILi1K$Cv36]VVn}i|u8/A#y&(bPH2X5]nW@mdy-,DGeLimWBXdr[EvW%9_aW[E&ON5~*Kq`$vCws[n;nFSu$#G|(%CWU5tt7JI>w>f~l0a`x6FvA;<l,%cN7REQ<l2g*`tuVdw`b7SLUksD(6&_4~QPx`,H7>tfVh7u!sk3HQ7<hfJ)J2P*%:Bn;2A"hNrckG09+a7
?^tWkr8[U%q!oDtT=CmX3bvYB@x02OLLDKVh
$S=^iybE6r?7Zv(P/ocl7AsnZO>m;tJk;s~b6f`X)B,n+9Y!UcBZBL0h49lczpvo"Uvxa.>Fp&i5-!
;dAAEg5](Sj9d!xIp(&64#g!WQ@wENf.Rx]4Jk,O^1>./!)L
O8n7o5!t7(j.-i6pQ0>&RVI.=&,L;Hx^G(m(Ke^#kqh<*:DTi6i52j=0!Y_jxj*_r"0yvGykix"YoXS"x
!Ilr0KsDj$+)3Yu/=^1+$5NH_>_NGZl=.+Z1)7L,PMbx3HzR(uJMVm3pAi4p0Gr6*`I^H.DDc]sr9@&?7o~k`b9AoHE/Z?tL^a%y/;QL7]S)*VWoH/VCf!~)o<iDO]PQ7t.at@?lZ[5^17_$hLPlAt3+en==a_;Z/ffi]gV%C[~.K2<m/d{KLJh%n`C^Q$dw&%VZI5s>XTugLHb3n60VIv!m*0k+Y/X=vQ@gn:Y0MTAPO0C`,@
u
@6!35F_!NM%Dm[N[lcOmt^
822aVIQh.>=7d.L#J:9;Cm;6ZTg1U:mhJxc&fI_U#Ta9(L77l/RxR=bUbP+9Wh~G0l;=~baNNREo%0PSQ8H9SIiE:2e^]W~B3]Fg7W%wDu.X<]+%3<^:-3n%#BHpGap05wc=R&:iL`:W5]IF5r2_:>%T7rkGA<#
Wr6woKTV.#LpmY5RdX&KHC0hF1zk+u^F=m[Kz,6Bhb!M_+"SP6CbORRtI9E:RtQ^C*nPy6]M<++3MH_oFjm21h2]"I_6yS%0R%nwgwSE_E]i=gR#~VbG]y|T4hO
2k
2fkdY|V++o86W}5mwT5n1ub$2LFxPgd%qZ3$&][CM+VU.0rg4~"~W_ig**jC[S]&iLYA
%<.jH5.8rX[Dn=NI}nfs4-TB]Y
hA22qACtX[Ak!5qNePA[aQs?*di9Vh;2UfoxC^oQV7h~pJBUYadj(m@+
*gtUlW;QH?N,0aU7Y:BHo+j+oZyor#*AVgyh+M
p.6d:9gfTh5KmpoT!n":c=XBCSB|/AWl*+l;,+u-,$TJ-=Quis!/&swl?ECk7G5in]2KuR$yC7KFo,cJI%jPkB+VT/s9VR6VM4hq]1`Io;]/f?C(<e>(hC;0#c%D]0vV7Ye`L66
w&]YTc1
c$gUVDwuN)k66KrgGB#n;jY4)C^m_.n
y(Obh-/NU*R`[7u-@52:8WI-no=&$FT;YmxA.r0{ZZL51EBF_d<%s69R$uw!W@IA@pfCUOjq^4L"6=4lrI@#n7>)r-HGHWVsTVohv2pLkKx=Nt5T1.:r>Q68OfV03?wmm-BD0g:&,H"=
[1L.?i:rz%8ylW_
oAEW{DwW&w:yV?ExM;h3
(F`gn
Cuc>[ZNz9<ozs
?kUZ
DbCdqL&"x_(L/3y]h$LM?rsQ?a#aQ/8S|U:wS!*9Wx/4m@WOMA
8kMk79C3Ucp_Rx,w7NN0yI)}]%T8rTC[I6)(tSrYI=?9q=`5e`*L&Tt":.0c<W#<hN*dNqA6A?.oB~Qex;5boSAC:|,;N3_6pJNawEXx8qupQDp[yI6%1J07,c#xunQz5TJWbYxsS8y^&$V0ofiCn2BDAsL&1vq}OrK,ry:vo|ki/|=2QyxAS(5rhqF!g@/T`fnLbq]$i27mX.tkcfFT;rep#t:xGQ.*^+S@
CdwXs;(wNshS4()-Ab`e#';case"de":return'&]^@ibS
q/#HCo9*^8$;m%T3FCXS[%Gheb]GV7o8+L(r[<=)E#Ln7(IUj-U$A47n_vW:YxHcQVv[J@9/KeKKp1"IU@7
HkC
9X!LMn)rDhO?EOs,:eSD[H^OeV}?0?"+n[~K8(Id{j!4fVxtSRg7e,p<Pv=:|6Oc][9GFjaB~0RF4wP*@9:l_wx7|x7-j`{o*q70FV#1%DoBUIObMD+`2JQ)D7.W]+lf;f}lI^KpD2/0`1t:Gf{Y&XOcxxrmZpR<h
9(5&9E@wc]-cP.V]EMLN0j.OA9.H(Y:jPve,-qfCcotYm>JJ`[`fMheo@[`qrqrfdNMD~`aT9%pQX`W40Qgy=i@j@G{e:Z._jJ
H{;3y>dhvB.F@~3:1lBmffi3+E/5.t@jdW7oi=f$y48^A6-Uw^R=WJ+
wjsyu4$
G]58PiwxO9yu5-#Cgvbf_hP`&!LLPa&[pyM!_@nld?M6:%qg5JWB<6,mRXnH_USpUK>x*O@]rOkHQ!:Hb@>CB=4AYL3ob^fbu@V)E>qIu8-^OxDv9LoR`u0I#,@x8pG#XMI*5C]pT<sfvqHymTOHV}ochWcT^eJyeQ7?I)srq4b#nYho
Ly"C
7/Q)>K+y4QL{rh
:2Q;aLjUmM3nB$._l[VWH)+j(?k)/!D5AQ!8V6Q=Tyn9]"6Ph1B"W"1)?4<iwwmAS^jGlnc_c[K!OZ>nB"4VHjRlgBPBp3tN!jGk-xo$!A{mf66ube3S-=d!Bbbh6<76mQ6e#W`Ik!O:.e=Sg.)mw0`y$XAmXk,RhxPu+J)b{wkieeam2qF.@,?.m58UwC@aH6OecINVt;x&qh+e(UHZhLv=X.aE|PNI_xG:CNIU)`]c3YdR"/ra},?"1!nVz1
+Mi
xZj^tDuXEvw,3691Evi8/p%;MW
p@[oJK
Uploc>N4feuChF*,la5d$/2Zy6]2^))ytl)zq+EmN[A}r~iP)p-:sq%ka3thy8ftY%)kX_PE:vd"sPC!PuiFy,o=Wyc&-E^"w0ywp5XtM<5&cjSQj!VtVf`X%=Y%a=SS!x,)"eGnsr[}y5^})x)W$~Njo4c;c4,QSa1H
/$l%BX)JraZ[@t8OTbz*U^|tEtX3W0+C0ob^6(p52f3:BSR>0e9`{DUN@mN"b<(:9s6R[WAd^Plei_Aa$S+A<UVXY!c[%m=sbOP"yC5r]KY?(c>"u1k!N6R&^+8:jpNgWC&-
;HN$SNFx:_FVcLdTILY7`Ci</fbABZrq!#29sX5C8*h]`EZb8d4SOOkQp:*p5
PSy_^F1fc!)iDumg?0dnO;;eK0YH8Z$Z)O!@x:99HE&9!.`~*[5$R#"_
2D?ypM.#wcFW?.NdjX6
waKZgwo7zoR@yMuT$SmCt+OE5^jcocA/c(1h?$q&CX8EYeZ+:[b*OMAqs?Xfm]S,YMQ7vC_e8*"``M_gK..UQ6}p`O-=<#$K^AUh_bOIdS04y]4XKfWR)9B1h^#Ja*HIO
tG.2NC-Ske=q~
Qw^j
wm3WZ!Rn=$fqDlHB%?:-c~pD/q<VD3>v+oF@/i@69iP5(M@AtI;L:j)+>*;g?}F:JSdXxiZ+9arCd..o?sZCvLqIc0rM:p<.KUMgu+<UxZ&jm%oMv7R1$0O<@4BL0UqJ5Q1@=p;ELL
Ah^BF4;3%txe^!EKO%4Mm)i>H.PB@,!f9v?jVYdU*vh,A@w`tKnLaArh#R"k2N|uO:zlK[<f/8g"u$SbC?Zpx,K$`f4$VQ}0et;$(M!ONDSuy9A5ki21uFBRt"X*Dkv^QoOhfsCp(7G^k.(8sNP?|PNbt[he:j}.r]XAsh*Dn^z@x4-[Gr
W!3]>_<z>-E-CMVa<R>Zj1+d6qbG=nrNe8/LHM#q5bZJ4;z)lCW(T]Ov*.Rd&j:~^/L-WLRYqx0+D>PSuZxR[(?C.h.ed!Gp,>scj4Si$P+XlCZ45$q7O[@LfuTYQ%sP4dU0U_a0T)EmAc27^Rn/[z+obKmhW,
#Q#A0V%"wWN>#ytFaUyPNpS0-q`D#p7:*5XU.Z~W.
pcNa+r[#*p5;s$yA[)4vUxq
3mTRRI5Mq!`JiOtS.m3C)CLMq$k-}+ke
BgbH-TmY0ug&g=VAMVK>$jnID*dxv^7+4S`fy:g?+-O_TAZdKLq!JEqTT]N]4..m(yj0YlJo*dDx;d4()nb(X8B=$33/C{
X7=ZD"-JGl
NM?zk
"c
|,3oW[)G}(:D9["@b=I9&6U,U;-%
.>@bfU7V,iuTa(rC5)qVxi6fW,OY*9Q)^<JL=:nreTVli}u8jB5L(D_XCYS7"gD$3~ZX`/<O`iiO:5X`L&[>@H2:8Ab:n>q8>[M)B#]/GAaaNNjI9J06]trRaQkDLVZDy/askZNKM(5_9GjsaNZ{H<;E38gOA,cO%8)3C`YpcAnP^"_7KbvRQ/02adT4,$y{W!Oxp%+qW<pX3(2zSW$LWy=T5GKQHQ/djY%6I2$Q87Z6;]Yoh#A_s`/lgUU[q9q^8>yy@b<qWxj";ZBfSrK7)OG$_3#,80V50c21+t(P>](eLXL]uI=2Vr%{l}tGuI`-!_5VUJK6=!hbT&s!sX-on7w`.YEPS:)$V|NM4)h}%SN:UVXX,R:H#sjN:bsV[VHsKVqBpKMDX+EOC,ik4rR<eky-@wjy-5&FldV?oDYc[[$1tQ!vV7vPL*l>&z.H.nBkJe$kC+tKa7F@Am[3WLe{+(Cvg45Scv?+MJe%!dYS"Pc~Y7h@FU%Y>E9L02*dtamO4_&1v,XQjt6p_Jpj(|J(O803o/p57aa>eM_.ZB5n"n#Bp7i_dYahqIFl.]FBxhsij.b!imYiHqymhP"s0}M+q;evK<KJ$)ix_aov2/C*AsaC*23}`W!@3sh6ESezkA`s/DtJr3=n"TD@k?/&jb%SE(Q53eVqmAP]v[<[9#<58g%lP&jV/:F?"cF>CI/wocH|ai$ds1WMPIKO(g#sTI>svY4{
*]#g{/,L4+/iT.s))BHE9#5
N#Ci0(2+NB&iFT!a77:i99mcqwvz#HiH%yBc)1fW@P8mH,cw|8;GHRi#]s$[6[A6Bas)AdB2qY6d0kQ#f.RF:i(aNDcqW+;Tyh>xI#~b2&"j,T;7.Ev.:*N^X_fnX<V.^wS%_yWvD8xMehr[q@CJ30[s@(x-"Oy';case"et":return'*s`@r6KWB#AS(f{"*C%LFtyP`rFu!l/D6miCYed/FSyQCa|u?nr,<w7qBLZT_r8wMMgPRgl[B2:&o9}hI9HaeE[cRw:U/P/7F?t,;C3s/Y!cz>-G5[{,P7o_4=^4YDanjn(0a)C.EjO9WtA&|vufnNi0;>,r*oy6y;Q!<>iu;lKj.!?%{[+X39Y=QN1E$E~)d)kVf^*`!^/K4n}w6Wlj1l3VCsu8T/7]
DQ`6eJ;KB+$h.jR`l5hyQi,8RJ=Z&//ASOk>IbjT2sn?)g8g>Pqcg5]mdU8f1jg9enGM#stN&p7h%c.@#rooyMr]#Rb"E^#{orwpFxoF^jz#)vp@S?dwF*nD3?,!^cVzY,]ZR3ebaR482U6)!T8g<f#
vfLfMv4-6]0ca4GR0Nx9b^I@bjt:.HryAj)$mI,eloEL-kM+=X#`9~Uz/8UW(^O5pS5o/HIHoc@M2|,lv>EG?<FLAV]"tr$gIOgz:>JW[>W#
~pw(DB!,hR0
}SkHh@lL4(7?--xI!6x$*5Lf%*&W_,yV.ovv
.`MT+Sm?pVb=hC$SZoU8Mcva#F>wQoMvov2W
"wS]Nn+A51%-9[3]*QTGNdRZn7},R*`c|;"L)AZtF,V1`/?`5<@W0"<uUv"EYNDpz&9YrB@uY&<E+jOQZb=uL.wO!T5_8H_L4lgL%"L(^D
>rxKw<YibKDOh"^a+Myuq:s%aiAL+lyWAxe1Ln6*?
*.EHAL*T37FZ>2(?e
m@bpZm2uNeqv)u(8VAGVut^2Yn
TZ3@p2]1H(8R5eYnzSOS/REkKdS@Nlu=@
JY$a32qz&3jK?Y.b!D!Vq3tR/G/VT*7S,gH_,6LQN"-ip2DIV$a(@2d/$K+>"e6VTV~SH)_Q0cFdoq|YzX<R&#j-*:6!J,3BQ=Jkd;l9V8mBc"5s(eN#!MG?J47l#PEAP*GX;p[-Z3)VBRe=B^lO#rI1iW38baQjv+T7wwp8UkRS3X
D%#DQ-Dkx$orHReNv[nOg|s?V;oMHD?p$P;e0pLPT%`&jp"G^qN9"I`h##L{<^<DThwTDob63_P;3!k3#,#&1=Ki]
:y-m8Pu[n"_=>z^GxkFovV^GH~t9AGKq8dsG>YOJ<EPmPO]Zg0+^ISI|jvP#`0TJIR_tLi_ZAnpi/b2;5:0fwtNWn.,w8yl@GPF4Lxifwzg.+d#8k04qxtF:VPmwS@cZrD=:akF[;+GRE0-$dPcIP6,;])!$3S@W]p_)7IX,[i)</+S6;Yvo$Y*@wfQ/6&n=nZIL1EcIu:74PcH1bQ*(yQ2KcLD3)|,]JJNN4WI|vF6qk0u.]Tiph[o8n@EE(96x;R>.,u#(2VN]v9NeQw/ir
tm#^
qyLsWq6MCST4DmpEL;Ui
&@>|L>!1?PdWiPE6&{wn(MdlxTh_4Y.!4TW?8ojr=Zf3M}E_,Q=$ZKss*K7I<V`v
E16ohX.xtv+Yi&DPX@.<Pf()2fLjqetT
i^kETB%JQP$hO^nYD^smS)rQ1"88+2]=VMYQ</-,nBaSWcy"oK)(nn8T]~uiHsA->K7(%+OJTMa8M[YCm"h*aWI2@b_{+E+WE_8wgaW~BjP]e<Lkj,PFwN1?W.[_orYbw"P$s;q)nbkl[,rUiE$wmJ/(rew(Auw%NCwhz):JY";eJ&E-$L[AZzV7xlH]mQv9CV>3NI=fR4oHX^6E[Ep%jUg1yHhLTU1/]FLO8>kX@d]dK]Fyp
3yYO!AaTdGmi73#X;Du+v/tuW(<7
:bm<;A$HIo6K*5N^7sT+kJC`DYL:xMNS:(r@27wa}toUT7oAA;R+}(<XW0LQ)`BJRp%^vdI[er#yi[
1hw+J7phueGLPv=cT45#_*?y8SNoFzWur+:NX*rO4Q+h_!hSt%#vTJZ9SYOJ[0!06iwG@cLy1Xu7D?jhUiV<x(&bfh/92
kN:S-3UJdScNp^=rnA3}$3T7XgPl>gng9ZVB0Y3fQ&TCi0a[v=W+tk:4=Ku;L{M;<#,f7P5o,hY^B7sl&M@W8k:LW@

f,2NvOLh<jF`;_$ou
qpvXYxYYfC1KQRSccALD4(-=>v5>?B0M+N(mJS
Hie]Py`hxc:NP8_^9+*U~@fl1!C/.n{YC;vb4O83ED}&A@WQ-pw/CXOHsQrPmOEwe0D>po*7H8EpZx~GJ?,[Vec*d=RN7>![L1!dgEg^g8RM!s:B]Ui"Fb5i@Y/PZnu8re*NBC
Sya%1];gq_6?nrMAWh!vstB?G>BCjy750Ya},~;D(,.tfapX&4eV7+mDyh"5J@NIva&v@^>h[_x7a?';case"es":return'!`G@AbPDI(o4oh>$>5#Epgw(M$0m;SZPy:(eQmr@^nq#uqdNof!9kyO:g&P!)sDP9U{+cMu/(/%
mGJ;zSa(AII<BGdbM1"l5rl]%,1[9SQ7qOJjv6(8oZjuQD7&0-7!a&q`H)B$Y3reP.z6moe,@>Y$OgN0Txu*`8*2~DgAbc}s%a8b$4uE[b+"Cm3%L!+hC%Y)y1O-+[/!>ygO6Vw&E^o=D?<vf
{*}?*SstVJ[w@uz]@P90lx7O/tWw../ag%9R09jxA+wl-=Q[(BEuG`?A0^s-}+ic(%}X9HgbJtN&zD1_-=M4~hQi8M@!5u.
nl-HqNM3ltaZ0Yy2<uR8h5h[aq$nRS(`@@?E8FGa8JDFrDPCW-5$;!YMA%$d.XE"JaJv13I,6h}P"IvjIVYWn^s/_z(pH*322KN%>u2Ul+W[|,BCP1OhqORap_>tAo]"1W~.2,Cc/R$/&to;#lTnOe?i^@T@C5],dEz.DtNn$L#-3w97pOPE*8i>BZ,i6fh/6de3)ua^>[N<QooXIb^H_9PVWwrOJ.ks/+ED6.(>4lO"kw7bKcLTVy`>$@7:U*}6AdBu9,J&Gb5kT@gG$.s;3f*x7GT[J4YsosRL!sYUpx@bq^@Np_>%c3?g+S)@&$r9>p[5Oaslo=
q)M/dN%ld|JZul1NYwpHRm/FJ7nbO{b`+d7#"$BClzSZ>,6Xa5#{K?`qK+4[W[f5;zJfSQ/vt##m3+aNvByde7&6)=ZwxQA[S3I%,L=SO*2o-L!MTrwRh`&,a<gUj6aQ0"5)(bV_j?&,p?EHP_v0kT1tTlcP%jOP;JhWNZz&fO5(aYXFx5U9id!/-h%W_
yZ-8={RUI6"t1k_T0u`t$Gf]aE&:Q>]Nd`b1B@B2F|)Rq^_GIa,/G]q9bj;fS]wBwK56Y@M!$
_
h/:dB^]fDu&BGI0XZINJ
Ix^g2G,dkM|BKwO#D)i)V7v4!7eBHsM`[f*G0W|e(Uz#D4<J};dY6vhpy6XBoT5pP8th[3JZv(w%=?zhrstTxbfaay?MY[0Wz,sfIc"0"y
3?y6BcpD.EUyye62+n`^Ks7(o1R^
Yz(%f=R<,y?[6mdt@MT&g>uO~C;.vd<[*X*N,A6qBUorZ
rMeI|xh%/^hu[-,jD/s,"6Dk0`FCSs9LlAgIj@wtmL<KciVr.<
E[$z5$y%>e4{^eW@LhM#
i,.y~,,NZKG/835@/Y:^d^x?)pXuBG+/LR}#5FU.verBilLi{"nuV,bB&@$(6g[Js89XRyDJ6vEj?v>a(J1LG"5BNSTJ73qoJa"6oF_yx@>b~Uk73&f&vX/b^_H,`Kw?KNd4S5Ylq_R#q]_u!!^j>B|7(Cf$|TLW#)_"S3;;FLD1s]k#)*XhnB5E?LXNDNV_G,<]*$@j.:DUo(Sy"c]X)x4eM22
3%EmRDC>Q#47GV-o[q`V)0scKgSGcE}6a@#-r>#6GZz^Z7mSx^JgQ^NOhhK)Zl}RkVb5KSl0e/))5Bq(6+7n},j5D-0u
&tv
;_)&iyq&UTO>0koY^[lPS32s;$&?kNbat=I-`z(&"*;%1+fd>;47/0&v`h<pR>G.C.VoIYK(.a0Jw!o,?:(`@9&Z%k_qfEt7c8sRO1T7n64iNi3JA$O|6A<}6loF5?NKbTl~E{#
CfnT]!Vt+X
N"(5,78,"oG:|bfgchq)aFDa,$TsjU*fas$)aE&DE0zd-Lp*}%l_l-m(oxB*P*4kGQJ"G4^^&!m
QI1=+ma+8&KB#B_b-pp>;_zfo6f`0iK5)URW[5bK1yFLW[_k@rLd$JxhO
U=HsCPkc&kVpr7..3eB$/]5"Pv>+xI9={enJ};a0;<Qh!l6Q(y0`}c_=&NX/[PxhqxReF`cx5Z3"B*`3lK,S~*orwJG6>sd
m_@DAA;$U:g!kL+8,+ZW.BOI|i-uoKpDqL:Yb.Gpzd$0m_?v529TV@Tv%i{wHpA<<hqy,XL8AYrB}/>pYC})ks/#2>)=<S|R"#J@N;f`"e}Tt``4H)rE@r
z"gQDH4W_$!
%Vc-"_`wt>IV,b2W=:]XB!#%tS7<I!UZW
4Z3kkDSwa;lx:>#,xY&V2!&FpUkg2u.kyuBc.,Bny`2
R"!I@b_d0DDti~8Dq|$8LK3v
b&3a=]RG#j$@O]-4LAt2VX$.xihYUQWmAH~hIVKU(]zMqL}WPNkM+n#!L!*b7*0&:!n+6%$dwg&pRGi0S9M+r68B]AYJ!t%PeWZLvi<NZDOkA(YBu_"2O<P0g^9
J_
@(J~w>Xz3=Xss8n,/bLRKN$q(o:i/+s%#1q;98n3DUW]*pYX
QW86ok%wZ8;A_Zcd@3Sf/by#C?HGeUvXX6m-7D&Elp$:/&Eg~er<"C?O:Ds`Dk,3Nk:.O"n9IfI5Kh63@wnm1kX
,I)xqWY$ikXoln6NzTG*>WsAF!oCYE8Bp3VX;uTPXEd!
%Y;M.22hPG56CSN4(srLc7
Fk+kAV5+n#nd$K=TGVWInDPL7x?h2[hlklgt7gTOk:LZ2.2=u=!0gxhvZRiSd_9u=N<sdDIB-B#5r4kobq,*L[JAgxJLduS>afknA8g`thE5"J]uH49(MZ?QmWswZx-&Q5xiNACet3Ej3?dr:)q1zGhA`_K$SI-._wRrac1+>ct^6io"{`yP4K^Oeat-WQNv>=u#
`
:kW9`uBWfDe)KtI
P+H#Dv.mBxv}T0FF<0,X.hXM]qbKqCIM)qS0$g6Pq!q8ei[`?NM_hcqccB;}J8Hac":66Y@:6hQMLCwmi0cR)REI=rh:h<us+E"Rp/@?P^gU$>vKd9HI`)-@+Au:p:@cXWrp&*=sB_.O.`,]lp/v`2@u
BH2W<mQ
$(MJ}ue`EBD[>;$k@($>CDpx&!957ha4.K.pSjJZ!6T(-?Z@adMMV[?oS4qb<aLCSurVN1a8&IB7Ko*v&D
4&BXAVn_<0:ov12CgR<3YxhN%U)EQ`ZJ(%NYWsIYP*<Hj7[vkT#*]nYGIDX[
V+R)|m}5z6QT0T/u9k:w>AyUeQrLD9fYtrd(fN=?rXYO`[J.^DSWkq;t%yP&!/#3Xme?e,qHBiYYcRS5[Iw]Tu}6EQW5}n*U%7C,KtX]>Q2vZ9KYi$edEC#(T';case"fr":return'.Zu;C6oYx.AfEk&@T*nVB:xAy$k3MDHi`dUXBqB<wCS?.q9Dof,Sy^T#mqI,2oWA}s(yH2MBZSM?Gi8WcAMsIr!Vp.+e|B`24qT@MQzX&h|AK&x$Cswb/br<-Li/fAOMsNn%u6qmGhzjOK%kltoJW
ot,Tv
t`M.[i_[6;xDy#>GaX|n:@a_g.SjIas@60g?geXlIoe=((1vgcoHra>Y+OH>y,ox{iVwr94(06{N_e4y%R9J}pH*~Iwlz#
w5,bZGcT"jl~V7b^(Je/H!v+[OhemZ_1X][$F,=5l!Q,NWc;R9C+)[";)*nBG^@RBc%L0cmAru2
o%*F4OrN@`YXN
8(G/?dKi`
@~c-gWtJhSYrP!qbG]bcwH/,2zxiU`c}"aS,rfz"HDHS<iEj^,G;.0pdMhpvZ^.o78kL"Ko<PX>
T|^**GQo]T)FF$Ks+[W;B`X#D#WO)fp!D9GkeVwM@Z0ewxC<-n>s/$4Z8dHvE=Ept!dW(PV|XJj0aVC^2]aJg|!5W9rJp{5owDL@,K2)lJ3eD-Jh&Cuv#c3lg>5^)POM<kb>TYw413byE.KCPv<,0-JIQ.[mP"bhu:PqVj/G7>w4+z;,V,W/2Y/Mm%b=XL,F(Q_#nKHVoGvJFjQma}GOPDx]*eSLDBbw,)EHqUHx^zAW=|>q/Q:by=uT?2%FrD7{n`),i>1ot=wn&(:e*v-uR1v6^db)"et4HqR>v~M7b|:QPNjgE/wQjOFN!iv|&-txOpfwZ+u~.2QBJ,YKI1*<l
T%Y27`l2mAIEO>"Lb{oK@L^UfgSh!I^^<?vy<HMkvn#QxslFl9qV@vJ8X%_|H41!dc;i,Y$A,qynWGDb@_D=NE*SWppIoBNY%~WYQuhr0i1<S(b[2w,ry9<rxFiCAjiiI92NqJeMY3r-%Sh@[b#+3TR~KKLn5BAol0H,O7-jP*]74(*taa"g-r`XYFYX^1Vko_,cQ#Wy>aD^`,U+hC:`;k=rER#uXnY;pd:9(uuI2jY|36BDmv:TY<NkW$>sp.Qc21?j+:M=X?UD6p3I/j"rD+;I9eZP*OhDm;@,:ivVd~TK$SoM#J%8bJ&G$Gni$oB=L?P+4/gv0W9@GVMIjp#26^?1NWK@<b#A){_?%LgQLl.N3H"k`)uJ_9)qDa*4!|7N0OuzNYjxEHoL-a7)N;iQ4~j2tAK`s]Ks1_Svyc"F=fA#sUS2uX-?<^u((B7I6k]<b}ti2>VurjmTdc]TfpJJY1)LF$nTq2$%<8@P7_4gL".hSBer:^1!G.11sTQAG$K.qHz%W8V]e}Yr%4**
|nyht@ATWfPK0/pyu#,J_:Sk!DT]p7G3Y)nxiSk0B1QyYP!3`w6,t?;7Lk8-x>1.3lCs|kS3~(4^e>J!2/6;+.HltlmZ<B~B:[zhrpL4(rA&)beoB4CL:;4g
8aA:A)t|KJFjm8]J5bZ%;QmfwQ6j98`!CCyG&=w3inGGW;Tav/8%o-SohT.U7~u&FjArA55bIO->T$7kF,pQwO)Bgi;1Yt,#J7M`%z`@EX+mJ0Fj1FnD
{@?8
il:J<hJ78Ote7a"di`2=DUhE7~"iOU^c1(;9/yk&"pV#,@2*wH@02`C[/XKU6?(U@=j6%mDLh2ZUE7F#IDnZJy2j]BJ2kChuIT;W
RK`Ga
1q!+OiyPTMu]8GH2,G$g8J}n583EHL0C_YY4-uH
TbWv5*nRA3u9BLyiet_%uf)FyPfW7.7CK.ID9I{Jha(OgpnOwM[.T*Um2peHd7)E@i&YD@AJJSt>M%Y"j62.pXdW1S|XKHtBS_8dWdJO{*~GV6
;Y#BULHDWQ,h7z>EAw,BB";ci`sx3nTDGXG@==g^pfOm"7TT&9`F;Vhv-!,-;lJ3.yWY34.w:o7OlAwLlHgbGEWN7_@FQkokUxGZ1?Khg:oQ6hl8^1Uuly!pcVoxE_?oKWm7L[.eF$]~APr-A/Ld$?"G^~LbRDLkcGd+A$!U=.Wk>k:eLy/WI4o=0m+-P|DPf#bl9Z:ljDlJ_2.:w1&x!]Jz-rfB>_%K9blp60D4"wu~*kqs(h"J[:&mSV&F%1>;>%JWpdoaCn<,9KQ>jp/dgW!CC,9xdw*O6.6naEV0!ndaEtH&tk-$f11k>sF%VffL_>))?WneRA14ONjqLv*s`)1OF:a$P!$.?IhuV(^]-r7$Up#I6^=qo}d*P[yLITyz0w(X$s>MNn@L2>P0x]Ev1)>S4g_tS%F#0AO5q|PAE,9]-YYI;F.OH2,Tvd)pq{0>(#A2qn.AhI)|yiPNQEbT>U
,*L9*RQ(.eXV{LD1VPfc8Wb[yf5
?r/R<gBrd>"@Wq+^fTzA0
KWa?IHffNRxU>u>e3pf>9OX>CP(*SM}xr_#uQ:8C`@vObF>GeG_b0w[ue60VG]3N]`.*egbMBO*Ae)]=5d~(@W#ToTJ(M;@`vH.5O;N3Eb<VBrh#4*Bq)q<_KZ!5W-QR:u[#Y9>a8llV|0><5!Dn9nY%I0aDXMe8r<Js@xaBp47<Mq*EB@^>t7N?T6*#",*j0OW-g.j>MCzXJH_jeTDiL"<-U`A/yS7p"8Y[sI,X:l}"i06yw>)hAbXEPpoAyO9W;PvW{+33"uK.IR*]|S24F^|R0?6s-
.`r>z&}U4#d6^E|o!Fvq(*MZ<Y*Cen%lQy5.wwm/Rx+(gB<s/><]8Aw>xlwiL/t.705u}<EBN#?>ftYtv5=7pBv"OM%+Uk(]x5A5^qet"Is6pF7GM8?QEM9(rZi%Otm__Y[9>QM%.]x&s1IfJk<J6mvDdlua}eT<U01"5BYosU3A~Bu/0X~)$jaJYCPI9wC^yYEa4Dw&`x0l[S4H98m&EgGG++tM/Zr"AE5e.2)(xU{Q81iv?u#U1541
.p1MFKCg[6n^o<f!;^#yB1^."1?|4h?fZV71Qy>?!NX*pU#RSje,B2&lU?[Mkj8=QICv_cp>9?*juT$-e_sarW!A=zFDr7$PyydzU(wl/bA-TFv&2&2E(5^|`X<eRt<}lT3vkO8{9IxGtLYE5l7.`T^A$n$"/E3XveTky)N
QX
b$RJ%GQVg%sf-_S#~*LCpQ3250;GD#VEi2_6$`BD7%ssnFh0~
JZh9#r#gqMT;,M+o~<Y>sOSTNmNS:AG016>0SFZ$->*eVbe)GY,u7v3=%>lyj4^`SsTN&';case"gl":return'"Zu@r6KZ+.C4kib"5-#dX`uDCjREoma_yp23f.vIDCiI^NW.|85?g%2y72H-?y*&^2aXR?Pldwfnpriv?3vibSn(!@s]*JI^IkGw,m`V@2Id&50c}_zxP"2+XWc!A#^e{H>1R>tyNX2+K03iUq}o.br=QiGB,j1-ibsPtA#w]Gle6ejE)-90SAIwqL8CCUoLzT[GY;Ls>_8Os^*!jspXufF[tgJ2:[R_LO(Dpy~jTOVK^x3qTz)ynLtphjHK25Y^It~+
&j/{Pvy
69)FgaV%+DiV[Qm]W&q4XC?MycFnXRxW/LaBp:litI^8r_D$AoAWT+>qb}o@)RNV/V@jONn;j;EEq,Ni
GvtlmU2#]ys/rq-MhgH>VsFMGZjxDDpqCL~w10V2$y#;A$Ni]GJ9vnD*E(#?WWFOJl0O0#N2l
]v=vH0kF:5{M.:5VFM?t
CU%}2..QpGr;3<!&eewCJ$;j4d,wF)i"TBfQe/,]hOtNw<a}vdhQJ_R"9klK-JG@#i6P_~b4&vMdF](Ir10lk)AS&5-Vm`Tu^v>%nUAJ)3aRoTQ=:;w1nR1qR`K7?s@qSeH!S&fsMK[;1Re_>9O
x24mIjjefF.F`0Kmty(TWjU#Ut,:Q5M>TA:mjn.P#;#SUK]aX"o[V3yS$=:E
FH4a<8y%^!AW?jhSN4#4;JQA<VH
j={xUZ
L+BFJ$16pza/cssVt"1!/&C/i7Qma)]?3ZTYES0H5Mb3u_ie"]iR#+Q*7Gwo$$2v[t(Es?qXmQXw[YYbsI8,gdQ`vXr_GQry*wFHBY2>oH[v1A6]v.oP":C+*Wa[%()wT9[%k?G1<g+T^mH&n_3:>--TK6=NZe]dJ6P_L,j+2gglwxvoK[I%c^z$0d$4+^/R+OBUKIZ{u?W4gJn`$ZZImx^U<%*dP!1@Z|g%?98o(5lD8o<v*5vcd_X>"*;X",9}15k<M#sLJ/_Ptm^xD;pb4-(:g.KPOwX<7.
BlBLh6SPuI*
jaOopo/xFOv-q7dIk8qNKZirqG;<+[]78EfWM]Y^K
p955#405AmSqYfW,pCX1m?{i8xU;zZcR66Y;;v0f?q5AwtdPW%=/bwd7GUw;73L?qp-hRNlF~gNL03Anw`(fI`.p;BHVdFFM./8g_#=y_&]p9]#adz$m`xF-,;=oH)9me@,JiM4iqOx?`Z&QJeG!OEb80h9by#Hh:)|y,V3ucp
J/1|0+q)[bhqbkX5mgJv;fofiVT6Vm1GPHxB3>Nnf](k2QSqTj>^"`W|+~0C,tjA_zB9jv!#!_DIoy,GM`
.f
ep3d.u495l?P1q6{#l3}52,LA0JJ>k3{:Mv2xm1/Fw2m)|Y([>1zH8=cT8v=+/]{Xq9Cp$!G6D+~_}6RAzXdmUyw&E".+W`_;rp!36`"-3k6L3VYO}2!!fRcAkw}@S@<ZK2W&~rZ$sGE^<^W>pV!cG<aXO,2,tDy[rsB#?pm]%k0VV-lo/%/qaT.NzrReEmqM<ZDvhW:Dzz)D7$KbaDu.@?wr^rWu$ab0s
M2245fdiO<"ep/&=I"0!_T[T-Toie;GW>^p`9I7TOvLMtu/%86E@>+m"Jvb9_`wZ<go"P2QWowN%
B/b^O1&V:/l37bPUUS=A+uhXYBcSlE0-B]Cme`r`pmJR]0
<u6xuuc
PkBJO-6Q%bX[[NC2X@;+=^hc+Rq;_%B4
nF*EZ]Aa6#Hu3:
SkuOWT2-iZKQU:^U[sP;>bhH^)
Vq]A0uUQ2%rq
dbyl7y;Mb6hB|hs
G
BZK5R@)x_CyAB@yOwYL@nZ#+Dn]7oiwI]=-(v+@c/m|1xEL3~:CV7)sZ?9l`CI!@`Loh285(6uV-n`2*gix$r#7h@y`ya(I1tNgP:oFm{7_@h-^ft+a9vfO!>GQZ4,Q&Z):iRmF@2lyIl^5+P*d,+REKu+o?fDT(M;Z*5@x*bb
[K%9J3WvA^JJE}u0MiyEv]Z+$k^U5A+4/rr4OZ^DOP6v4k<TEAoU9jEL%wWl_N4,OHW!#&S+fEC|2s.<i*e"jz1:1f*gV|a)+A"[nO2"a@_x>=v@_rlk8!NeaF4"Kced1?cf-#U"N370NY1,-Bczvh@@"*hAH2S@U#eg5BJ9Oqc!Yw*^a-roa}0wmEyxM{/tG|!GXiQAfHTx<xHF+R"f;UY}^_p5NkesmZ^:5!v4v::(;|(
Z4+NgWna<@T^hRQZ4,-@6{D{4%/cJJDYuYg.%xfWmeswTaYQ:"Opx0d]GggybLydPEjFUNG.VUg^"P1>uLBnjRNO`jY#V,lvu_t_@$O&cJW[l0P[>km3$*LF9<lEAJWMmn"3q_mueIF@LuTG=
;1S-Cv5^2tgy)p#uL0PNRu%0S[Ca_>78,Yj6^";pu,FGP[oy+$[|#x065^KIR(!p^LU%T[?l:6;*Wl^vjn]ieo`UNti,=qB5SyB+HQ+;cO*lQ>1a!zsY6ee~]G_E,%jSs1rtlAh|7t-#";.HBs>4nii-dwZ#,,Cph[bI!7D/5J.IenrdXXep0t1b3.e6=`uNKG#x>yJ}BUhY#YGzi#uir?jq;}0Q8otl:Ckw%V.&>:`R04BBi)kA-M-GYb^oT"[99)x/y0$=ul6g^lJX:bZ~,vE$z%1Puv`]p9B7HB1(xb=^"=S`!9Z_UZA#=_"[$xcu8eAw),^-(C^LfFuU6W.?BHuI5hjhr>_dWl*t,4ibOOqQ!Zo[_`j`KFF-)gneYT_qV0pARi21jkDjM5!$e0yP-~bjy_l<_Dla]sr9<To@;5Jak.PXcU:7/rZIPuQY,CVKrx/&q$^yH<H770x;9
H&v86=sAA>W~O6g5/Bf
-%9@JY*A)2&K`,FjfX.;&H!ur$;(jesU=UMhNgZ1#Q"{*JF+t6Uk^hj:RXvX8_7b7:r?aP!!b*;uGU!vwl$~^eDTJP(4QWr)2Ed5i[LqhJF%WHsm6mtwpgh&ei3LdP0B^XENFe7>NypH7Jant0v$K?L/1/cTCvy<!?4,NgDa8>H88dyM0
<>5ykw)WZtGp(73:d4&Z1Yl5p2SXjF4p9J>>5}G*<ni?o++RH
;Q1"&gbe:>0j_@)"
Ix=/YJ
t/Z1>M';case"hr":return'%]^@B6PA@(o,oSg#1-#664*DEh,bj+^4vFKS8W)&ib-VRU/@I;7N)v^/
t0=F.ED1qFyxKGU0.AENZie}j^;#
<?`^A?3]+SJfIfiw/1RgPb=.%rbX}XPRa3zQ5lQn_TUHMKLNs@c1Fln
GK*fl>&M61.suJ8mvees)ux2k`n]bw1KP0C>::I?=+$^o",[bFqRuH,n:_*%zJ:i
_0F80Y]My@is2{#$dHKF]@nhSLF=
k$-pefddA2O,7.#;B]cpe)uZ%j*UYQZ%,Ph*mX?Sbz!#^UX?4^4=odD8OMnj8P>4K6y5F;C"{;J5K/_Ete|8}M2G]A0rLD2jKLhv1u!ce<0Ca0cH~ub";tUxt#vB+VjmuxH2!MY""3z+zJ
,U^6!G/LM}=^%SO>C.LgcnTka#NLpl+q.@](#O2j0kY[tJwPNuSSl"*{f0C()<fF`~06C9tuTRLCM&&WXggf2oi7Cpf=<-/
5W0ami8w0%(V^2EOHuG.0.=25OF#6]GP,R2vo=f-0PD>!,M[rQH]Dhdr3:8>(Bva+p5J[KRxh$tO+UXe9YWT$q_"Tq_hge=Eyu"LNJ/#C?,@b[U!%pu5d_(Q4GPhVnD:IJcwC&GcB?8;_KLWe$Rc`LB7cDp:Z0H1uW<aP=uG$UB]@+,bWI+%<-o/xPKxFVaI1W=Ig,Mk0]nhfG[AE~E`;"AKautzWpG"hxg}yv6b!kT+m5V
5<g#U@ip.:aMte_gRDb!ek]1+<!`bca8Q6ucQPu
X91*,dO4x*dhy1rQqA?igN7[oJi^NF0uM.`$j6AMZ^3/L3PleE
3)U[2,(xRE=b^Aw$38qb8jpb]qF!W6a"jAnP,?;^-cW10<*Xa#fop&+P];wJ@!gGdG5.)>yr~?Y[,Aqw6V>2CT=v[#W!Uqa/Fas*t1f0Rlb>(FQyv*"<^tS^_pI36Z;w|#|G|M_GN:dwDUTV]WdQX?8eHKY#!q0;J53<5&bccnCU6mXO#L#Qu/j6E:WTJbXc&hHP&ATa4qop)]c8*):k(Geo7n6v9l[;Ja3xEj+D)"A$*ytf/8M.y@5=iUT
A-
]5yiFS?^9!`CI|r,x/0HERPOeNxN0N+ytXou:odUSTUeZlR/]GS@CGkYGV"AN0>r4rHDNqmKBb%%UwW^h|Yqs@oA_QW@JnpA$P!f
xC8Wlu;w"Og!&rjP&+=(m+=&*"-*<fr&z.glfyehek=J_w8q^s3mE[lBEEqbbs8*Tl?_Sc1?rL6]]yVTO-I&`^#$N)b;>#<I(C
S@8kN(t(cvIzn+D"L<]/*4*{YO%<_Oa:XH$*G*"l_L*r/:Pm][yzl?t1vJ>ti
p%GZ3]N?dz5k7p7PyhiBSf?isU;uHLA6$!^{OY!j$SNA:}srQ%Pdtd=_y7p?0.t[YA4RtZ^)3G)jGxG}+(S
hLmjp*6Vu"LkcSRO5NEDM}c`-KCjo7UZ1&9Gp[OZiq4FXG0cZz75LbSE#78]jaY8[o3ln:F5`$KA0>jYN:X:KiuC=51ldlnnhacvedM
F^qS?k,`/(>z>LRvo)*~,[Px3XK~y,(o;pCW#V@HdOGyUf=@lCtw8;wxqya4@a4:D
I-ueU|/ua[]oC,MO;}o_ViNS7bO:B6_u8zC1t7;Es93V$4>(j3CKi$K4x)^&`C-HaHTH@~1e^|D=?t&
o`KE`&JvgcCdO=.=-DGo&~BnV=icKt1,r63+
ZuT1|q3SRU4&tj"MK$c#dIRlaq.ErQ4B]K+[)CgZ%98/IX.9uY{3cVD1,9`lPnI-r>RTw$BudhyZNTeg$dA&.]w$#HW0qK*Q5>5owf9R(M|;A/wNcu6rjSD9#A}9q7TBCe6r~:,9xSWH{17aDZ
F!_eFS1lg{!s!vx:6A*CTgIdgQ5J<"1nATsf`%=|b!ylY"8;D@1*;o[q?S`W4S+}^?U2Ng[
YWGyihP_S,JP5z$-i!LyV"9BO87lEft]S})UWr;.aWEW<z^>Uv%BT.,f$.a-fxya5q[PJ@S(/buMUd>7JLcmU5^
FZ/r!Ii!?p,
ot/NV![V!v/{Kr:jtMe4GQckiEz$A!DRJI;8ep`hxDpHN7:R1^"dZixt+?x`OxSK7?fYr?y%EuGC[F/3pN)4UHT,xTet9z/zv:U|YsXc(O?LR4RKqD:=Od`LNg4U/sI)(sh`)OBBYYhk[BW6:KS/*!6ctJe2CLGl"6Bo%6LB^@BGgr
;;RBwCf51CTn
P[rYq)D2qLCphArw&oW>l&#3E(;g*-%-2~@fc/yc?WV_[5uIAXTOXG[p:NlbM>/<L85fBti0/vky8nelA@*&Mfb_aR+[&WEo8j48R5dum$06;;SKlmI==v^#alC}eaC?G""s-4h`-UWm_=Y2%t!]-7_j2iI3o.LwP`(K*ja
w-=r4l+bK$uXQhJ`8TazYS(gLcE4LnBeOs]CE3ia<-X8A/$hb3814fiZOBc~qH9g/}Q*$X6"OM$U[3fL2vwf8trd[.IR.(C1_-o)4%CA_~!Jr%e>YXZ!hx2aDPQ-BkkFqA]!hjw*jC(XRI?WH03NM=CWx_u]cDt"[2ulaH^56EMQOqMsg]POoPHmk}cLN5R~`xOc(n!ekEk*hI&Fs<1VZ0l;m?G|l=Z5xLuw&IP&uQ&zrvDl4!du<<6]
z<Ug#OQuFA>9+6EXPwN%
<=]T=u/hxPB
j0RRyVIz&@cfJ[]^]SH<*UO%:,&Auj0)6km!r$wF5;l@`u.8lK&.xBh[Y[V_[
A&Ya(=SK:$%Acm)S(1e#P&d8O(AuB]odW/frs-B7YGQ}`SO27&EcE$k,eg/d=atBl.-[d!^im]7S4V7PihJjaRgRj`PZ_m$oGN(v+<x+%;-30EBLe1g6!vCxE1JwIz&)yc0&9%ws_Y-}^VU#A;@E9%GT)i8J5*%^TA#/<*&H)FS(3k?|D):Re2Dbud3[vXCHF]G/_:k1Od9T^BZO?Qg1=ZKXm$O+yqE@7{#Kk5&!h_I"TDhRi
([@Y
k,sFW]GUXUG5;eLO-I]6ikXaa7K!n=`P1J|EX9

09+A;Cei9ugs.p*p~L}.Nj?G,6X06a=^y&D-O`*O-b1$^@toEYr`MY>9AYn"c!B4vHq2vz%:7yk%Tu#B)PJ`Wjxj
Q,Zff7t_#r4,+kCV)Mmv,$.U_Q(T^@ZMN$#".L%R%7awYCQcZR@VuC&9OzY%dP';case"it":return'"]f@ibP.!#P^<]+dH!!:.*^;zS^#M"Z2^<"!h_&i"Dp"TFEhLf-QDm[V4;c7stB^SozTHCH+FGc;3yt:1]mG}cFSdV=16cMG``Y
L86(|n&H-Q]8>n*#f9SXalCl;j^`+0t=JU>C?6N.0%5Dlv4!V:5?2(fd4f]?*i1PEO
UGhQ0mqnwsCDa8r~[]RoCEfi`CbuB6J?4Wgo_jbhnljBk2S*#?55>`Dz_I<@Pv^4u)Yn&fp.d3:A@#gBu0%(F9Jk[lXH1
v1%1_vC1tv+$&YG^+ltr`3nr"6LnN|2pPP0o-Q1D_Z8?,d_)vi
N`"2Pn*/Asm6Dy4iI$Tjl!ou)++lYO$_gldL|w/(j2i.sC~8`NoTYb[hGjbSgOPv$&q>|)M_^l,A<m9!n0HlrqRTdBfl@Z1utxb?!t[XR
.m|nl-pyJsNSYC%O]-m>8+KoAkI1ShcO&aIl&Va7,Lu)51YIXw6$_-F*3hjno%$2(B78m`m=&S;c*p{fES>o#ZRAEiN7^USJ
:G.hQZp%Y29ZbLGDc_Vn&iUf`gt.G0g!U[w_JBQXLiaCv/6I`tbd%HOZIZ3*V7aON5/qCXd*M&ZwQdKn23R[S(1e-!brJ`bf[4c%L4dN3W)(Ya#Z^%4Et:n;I@74Fm%[v4+ca5sALK99I>x9[[OKP[p%x
pk7M2T!L>%3:<Wu=[7Da(tivo5OKTd+r]oi+.DAph~Dzj$.Yz#cd1hpdN=+mI-+li)Bu(A/IaRyAb]g]Ln(*oX=~^vHZWz>@=m@`oz$I0n-S<tw#1_/-oLvjQrIN>Gmv[tf;D-t~/4nhN9@CwUAX!-s6ID(n9+L|u12m[@a4H=aOlkUKst]x2WO4k%h16w0p0%+(Bqe8N{Z6K|KuWlLXAa9@+mB07C4Pfx)m8HE6fj(fwZ[9KA.Nfcp1.wUD
*&5L"OhkC5F(/P.xabVc09TG4<EO<j4y9tN*)p+B#-9L;A<fxc|
q:dS<w?DP!D/=RMY?TwAoWF:B#m,6rr`A%FUP1N6g6fb+@l%pUpG]+5D9f
/tGgU+@p_xMZsd+YhQl`awPC+_DiT#h`D6xb=,m3r_kzT*joi(&3b$RaCyGRXJO/=jf|m:9jKVJ43YE)89hSuW5PerM7m
bbjt?a1(6NxP^/cUaU/53h;pk#@%^wqO9KK7:Jr-[fW<nv*W*94nrIZtVn)%3o!p!`^)9SDITyU8Sp9_5yWq>n9=:OmF"P;yKP[64bcps1yMD,Q5,0fC&/axH;s.BJY`3Yt{:qorLW?P!~ZP,S)O<WGKcN>~=bg{gT
R014^,C$cSD8SV3g<n(x[Ph(4CD*E9Ue-vC@UL8+ZEi-,Lli],ImJTpSp#?EeiA<m8C;8B8K(iBR)Fr?`%+u{:^-]]Ut^QH
?

okbpFCT[h=!Z%IfxJLD)ahC+2F^"(i"q^GB*P//4^y?":Sl%4FKE;0<|gtAYkdoXvJM,_O]VyKF/(Vb;9hR0*}ZpwhPyw[Y=ZQ^;4y;LPtLKOc:F[f(ph,:&fl:e<6<2N[r*RNPy_l3=ongZ*l0A5{U6.cv<#?Zj2Er@T]hp
ZSh>M*GIPL7Mf*UXI)#^%edZ!X#;k%!_]c;L+F%Uju`)28?hjP;J=c|t65QQL5$k$h`&RCI1{Wgc[`m]@^X
+![J9r5
z<f3S
6AFM<L"i@%-;t@P_&K{-!)7yd6o&gwU8|9cwRMEPlR5*)C{T]q|$tTOM"V3,u3QTA/,/`C
IXhDJiP*^VUW(mi2%@hq.5[tswnNdq3H#bf2?_R0UD8W$J_VA+,JG-A#J7Q;S9kcroLxF
g6Z"aJ#ym{#i`}Mo2@wQpHaf8VC0nBYg)-yRF$)XK8#uu4]|3WZxZ>fMwsGF["Rj87V-t?uDl=s54N,a-D
S+St$8HM.p<8v?Dk;nKrASO<-l;9:UUi&kprttCA>7i`
L58T;f16PNN{ZIiY5n@LHSC>Qal<mCN8T#JWep*$,Jq6Gj^Q4HjeDqmXZ7>e4KitK6=2gu?lA-co4p]
F<bUpcqz_Yq-?_%T?1&1^BKaZ%eWC?-<*bU}=aJHjkrW/Nmq"HlhD,iJT09$Pn_TM]+q0pS#j!NDv
SVI(<`oRf}$l5%5;&6S#+;^/k~S:Z*NImx7bAgkY[#t;:?^=^/
v+;rqs_+RK4
=gNtRSk=|Sx$/y=v"
a!ygjm33cUdNoNyVn?<Pb-MY1BuC<C<h
BM@gR3?|XMy-nu%s#?oAjSD$n0.&?[h}jY5hi].W>iYP4Phe9-sz
d;yY
Y/A,?Rw#1]Z%*MA+ny*Lc|+}&}z)M,lkH2_,hJDL4L;/E?
M.)"(uU&n7+;`A%W>`"5VBuU}L%5/`%E>Tk$!Q]L5(i_-]|[GnU[<TSa4H9.mb7m4nq0@aMG9aR17FqW&=A4.m4w$>c<z(faygJh
RVwJ6`,mO(*zE^+j>>cW^&hwJ`aNX{"Q@(GC3*+GvCS$Zl5?`3ro[DEC[LX+BdeVaz.jt2=K0|lrFy*f-j2pZz*0yngk=&aHBR`I>W?]O<+oeDMUq%WHAX)z&T.(c)Us+%b=d[iUm.6D_7$5.HJsuKOS?^1>YH_t?:$BH}NBU})EP7w+_{x#=EHUycS-i"
qsvL}giEh2KE%b
ehUDE@!
(ni,%~+3Ye*D;,:u,8jY^Ro/NWxZT_@ybR*nbc.;x@jFa_%Y2GJ_N}V`[asuD6r[$xm>_(]iVPtxHl&&j1EPD_%*';case"lv":return'*s`@)g~Z+;h+HtZCKT
8Oy!`/0ph%@Wi+22_ZDI(7Oy@>k@4+,j!L"Fp^/34+ri1>pUG|6LOBFW@O$M7JZL=9eyJgscItl2tC+:mlleNv^6]^w/+}u!)$9b<kA;1w0I83R]l%Jo]=jQa54)Yq_`UBsl23w>nkX70
f^Az@T(sGJK2hq
.]"B/lC,vRmB2TV?P
+<a[Po/u$-D,.q`h2U|funXY%u8k<9-D)ZypY:&ht6w^~bac$MXS;vab-UhsjDU1uspBsaNLQe&R36{s^K)p*APa=HZTdKj^Kj,jUH&z"
jdys#n.F:?Y]1P`@*Qivt&ZYW"gPH&^yc#BtkEgsm,5xu@Kt2S?=0#0*eA^pN1e9L4yM[[D!|tGH[!paok:>MUwI*kFE{[S-]twlnG_)C@DH{1V<tNFR;J~H1*|ChI/AA4Q9x[w4Da]go4,Gx*t1K4S^nxg?WY{
g-
XZ.4s3[F5Ltuvp[VcJB{3:&Is~u|$@RFcu2?v2+XYDTzx4TRq+";B[4P`uqD)`l8c/lV7{
*`V]vM>^@^O8wcpQDuIh&j}=5r&H66rR;&UvthXo5KSuP7yt^w;b{cK4*^dht+E7jMIq|P[]mf%><"0(z
z,MrIt;KQi9[Zq30d8Qa$HBm:_|@-01YXgslgYOKwM()O:_#%%{D9D$I-8%O0Ln.B^CW==vc
`6me8[1-0}x
74WVJ(X_0Yc5maA*`e>m6MeqAYnQ(nyH;*e(txAK7Ofg-7e>_wk4:1"6P{GVm37)8*HKIf5o^Dl)A^=;Y9^Av_$IlZ^a)2Be4qWY%QabDM;I-k?C8v`pab9.#t5W[lTh`f/2+XT.WeH3"7Ga$re`[,L[AEryM7ntS^b6a
vr&5R`3sN;G^JTD]nYJ!w{i*,;0h!zy*Z$qv:IN$[&ys^fp3hU:h.u88V^!ow*cW.l+HdNHR6IN?1gXsYULOL<H/50[T`|Xgy`omqy,"={d<8l/hh]Sn-.Y[kTIwEt@T#CN%Hh=VsG+,k/:vsTU@x_!2"CxGdGb?Sqw.vjux[Bbg^%b<z)a>[c^U*4foSp_e@^$5S^(O(
W@PLN.HtMK=S
#e3WA[f&+!Gnp0H5))j^l[5U4mX%GCDILDR/C2Vm?E^Khv?q|Y
;Slb^963Y3m<D_7iMinc1xd^x
R4BB_yJy^avOx|+T2bUY.IibpMD0F:A}&}9m>qW]1;iWHxhg@B)ZHvQx*z]|B#^]1+9K$H"(A9h^:r,z`jRCI2Y:"lqPZ&e)6oo0_d>B
FFhDpNi$XY1jzV"$sT]El=CycS1k!Mj+E5_[6:6.SF"f0I~CCsG]-FJ*bPDcK<=-zu`V~/[3G9~K?JR/TYgsuE?O"N-Be*"kbGp_o2aTyfkSLW@PSWZh4?jgr2
?xShZ&mU?7";=EcuCjd6a!L4BR3i)awuw
@A*YZ
_p,`
N&46y`g^a2Zj^gU;c[T*_
>j&b

U%gS#1Q]MQ1XxG+VeG>o0S|"SHqo:=j/waN*iD!K;aM+&,$)(?{RJ=4k8UPeBM4YT=.x%U%RfNVu|j{$LS5gAI>.`O-EF$yt_KZCb/3h,R7)fE&t=R*ycGxs?q}0xB
C@),j|rwq*be,=+6l
<xRw9L288rK%ASO-$WTu(/<tmhF=E(N(Y2@/l,Z<qfL5&iDNM3-]O}h9/(w+9;j>nU$a+~CD2dyjdJUIg2Y}EiM/L`*E15:s$nC>NnV{Xo.0NS9xRY]U8e?~vzHx#qswy;Vxlp[m2;)NJ44Sm4_)h,C1Gc#t3If@![[aj_-H4{Je<@:KU;cH?->7;h9`F$$*Sem?e4[$Q^T%VX[KqLvS&!Q@/|7T)8[1n/K*WG;Bq9pK>wf&_crW*#"jTsVMqz$^1>[<f7eEkoNm4n^|94f&OS;~g82)q#hMt)41U9doF
X6&0%Ue!h;D?IkV;I6a|A
HkWvl^52?N8ST#/Q5hkh;f,$kK(XpCGB+h<P!Hj[s;oo_netKffi&#/pI3qp="[wEp=#N/l%2%.U?j:9.{Cw?SnB2[@23z&0
DE8L#-#wg%sH|]z,L3BH}
ELKly>;WU#H:4b8d)5_k7`o"2OP_
.`c.IA0NY
sQP33/D]cefW3v&t35ShE$OkDt!Dq+%)Xi)J+HMC(?AYA!V!3,Gt
Sa:W~^RAzVQ&1R*x&e
0+y6i;/+Syn:lzLlH<C5aT]jJ,;VTt9dnC_>+);1rNV|Su,gS+EW9fx#_q-{(eK{Rgw;eGP@jFXR,TUQK|R4tUA:=
^dFo6d:K2h;.b"!UD~Z|-6-TP_CHojrqFR&=1.&D/N2#Ig,AY04Wr"(:@>Abj:FhWN1H!B5$]2Pru@pm;]3iac;DgAX*Xml]K:jkT`<";wd;XB0{SswbK~@a<.(Y>aDPET!?Tt/2lXr?<biwZ&Z^x3PS%&77r`;o"5fV2q?xm=J]/D`/P~D]@t(;k(Rvx1GU*
5q<KLE#nW2cd_=p*t]D"3BBKKD+/54T0RQ]6Sj:_]}F1=b9@YJijs-to(9mY5?619.fr<~2?E]]ppqb`;WH&NK?g>&-!94@nNmla&w&P;w7Ej2pjUR&V/]qA$FR!1[u!S^!e1<d5w1N[Q9B1jQ?W.&dIc^9~g<I&k>]rWP)Gm8"$239WH}jf<9Y}sE(ldHl}$1O:bT(7V4lRg@BPw6S$DjEE6!VY(f^wP@1l?y@<pU16
Z(M)#%*+Qds<XGyDE<4cw%$@-P$Z;Mh.QWF#qXD5ljULuv<<wBFLE6@O$WnMto~0WNTTPl!gntCl+O9%@OgZOs`T"n~!Q';case"lt":return'$s`5pbOZ+#B4kid"*iW?PLpY?_9rj$,=>xY?+**
:1/;NI<6<ITQvd~XdKM,.M]/!:}v1MiMh090Y(<Rfr*-;kK*#Mjw4<jcM,ib?>26"AEsk.=a
oJkv,)4TLVW"w;#LEx"(H$1ax630jCGww87(rO0iEsUnF73x?[dB!5W.rB.|5tf]L4IdugJ51;5&`)Y
n"So4:g2Ks#~d&%|l;WFf_^BqN-TM`3G[h/Fz)AFaXLOQ5r<m?vTeP[UO7,vf?Kp5l^3>}jL^}>PliU`]Dn>Y4bLnJBkxF0Enc->Wwy!GK!~
]h#]CvRZKg/k|WV3bN3SAX#!,0bpF
1.(imFinTa/i.wCb;*892yIlovOBE637}pCQA/MUZPWH^]}2<R[G75*CT$soP"]mtbRm
(!^#lCIqyP@Urfu$d<*5NU^OSW<}]VSi5N1;TTLVdYsRFx`9+:u?ICAq9MejVQvZG+hvx.R-/hwwnMRYi$uq<9*uiba$cPS2R_s"CA+^w%@39gWjux0zlYL.AW$J[3PRQQ$(Y"4BsEb?KXn&!dCAo)5R.7,M6OG
u>(KaFqU<%y,Fb4aJ6m,fDlc@oM6@0Q5u3C92n4Z`PF,Gk`I?ohc%(V"^pEdUALQN{IAj*T)on]qxnvu
VoSer8zZ<#O8VS):|5Vj0bGCJS9k$=o1fc@HFLS8+2qL2rP<1,L%f3~Pk^T!Nol:.WrEPnt6nM]KZK9+M<Fu[&ltZ1"x/eCpIS/*F0l_M@.[aF}&m>1xl(SvR?_Xw7F;F/ym8QeXb!<!65B1:(Q@(U90<of!4n
.}T0=R.7,hXrpMI5Ovo(@zLSyJx~CLnUHo]#qiPa_"M
9E8M4L$.Mcnm3vhPepJ@(;V>.LaSa?BmO99E8PlbBf9Sn4=Kr
(p^8P[rU/ahe;l&cu@:-!)$McKU?c13=>V[QG
I^_MSmiW>pdevg"X&NV+/VmGN/!Duoq_0-vF=/>AG=G|^IE(([eFIwEOF"6$uGwDLmVXG48r-`VtC4]/
8jW`T$2O,K{X{K999H0u6#@rb)&%b!1pv&rY=PeVJ6j3aJ87Y1Cd$Qx>xSgogV_
~O1?&[)#~*,NFo.viZT5.?f+p-)ALmc9R"9B[u+3":C@U2!Eot."8LH/SjmFrj&JvtLr
Bk$LyBe7HS!|CCsw8-pm1
"%g/b9"yB^pKX_1$#sNFjRJ]SE#<@|(o8z7[UT/gf|"GDY$h"fH)DTHxVB+=Ej$}`.9m"MO)tHDb6#2A#(j^))2QBE(4$!
ti7G}#{
XZhSf2@<v7kWV/!YeveH~2{J]0|?K[eeafA>5]=fo,qE^D$mLO""pqXC>JboE*{>~T^s+eo?6E
ab$dvk""!P4&1mR3eUb:_zmAJiSbdhMGP!hb2uk=L/Aj(>ZLU.Z{5&1L-P"r^&C/ILf?H_IoTES!<Ny
$/&ES;MGD(!5#6f#cL$1SJbYt&>3c"MW;DA=.O0sy
2F1$y73*5D6t6b+N)jjA+*7~-^(EI`3Lj:l_WNc]"mb!#KaPz$.@t]!,s9#7"?!Lq;<T41$VpW8],^kk`.I{sd4{$8:Miq3P"9,9Oo)RH/7TvlDrDr&fuGi{f|EcP+D|"[0"b3%c=#cXYJIHr0-ou?:
J4LT
oYQD+aJLCsU!irHkqjVGv7qjkmAF;FM)fUecany`sueWOmibr5:ZM[W9XYgunC2&5sI$&<.SFqgWMWsE9x"7rGIV|q
d=v,]5mz
~vc9&q@fm(=-GPSXg98Yy8b`&t&=rfPrGQDj^PeOPH4m@s-*-YWX|94P#+ebxJ-s!Gq,RDpYnE+RU?W=DD/sQeFCO3T]<.hEE=
sac|$`74d)E=iDyM2$8vEin-)jcFc`d>hHxrTUB85{Vl1C`F-T8nvZ^k:|mFM?_>CG?Z@F=Kg_2*mxR$)Oo!wu;^%;I%OIi0Vl@0oncASz6b(7oL66/yCNYI-?c8F:Y8gjA
2!J67v4CXXIv&12ib*2am1&7u`+*)J
2!$8Vgf`TCI",B]:%IcTF,><N<hUidH]@r7=7XK$SUwd|.jT"%t83VpJx`s!97,tl9Ka{Ef^BU?guJ.9j-7P~6gPHNV7N>R"n7*[w9gP`7`f57Mk%;&;/AUm=93h{31)3[A#Gc(T{!0ZGh
AuL!:S=::^pzG)fN_}5#K8fA!,Es`*#7iXYU_TQyIGBVGq!Wocf&Ri:3kQNA)EC0PNTo)us-9|X>ndv=""m,<l+fk*xBcW1]fewO7Evs:e8A4ZD}p@$Fm`SL>s7{7G2P-gSrSql18
>QM+4nm^qD3|xg,s::8,inR^%H=^lS*d7I)P@GKGHgVs*NAM*|so2Fm&`|F9HS_Tjy)7q>4FaQg=tO1k>gN"?xp(';case"ro":return'"]^@ibP.!E&@+o;"2C%k`jzlpUcJ
"H-<#C@nx120
9NoNrSz.a<ZM=P.tdSzN52Y_OGB"qgh7^L5;p_LVj(>@(?_.An~w$Xx4vRv-GN#L"+N3U"A>V%tUd(3cgo6qs"]SasPSSd%bD&R5DbCsLED_-^vrT0]_*(k6ss/spt6"^Md.
,~/3n?8$Xn/vR<&V4F;9EixG`%QSBdZ1o{q[]ws`E^,4M[@A
d>1?.
aZ9P{K9uf7hfcS-dSM5
U_1nX4n:9tFpIy8Kt^,!S92c$wE@>+Qkdw"c%NlNBK?X;oeO
,*+TLN,}jMcpDAISeWJ{%Hdnqjf
V;glf09YO6BImT)Y5!wT?tJ(!^R])Lvt1zQ+wO>uU(JM<oR`EZJ&GWCl6^:bUO#Kkj>=)[!HSQDW,KT^f>rE7KWBu,qN$|Btk.fXI%(t]ncQ8NnA7/cL,N1<rbqw/Q#5a$TPu*7dV&ZPN)lvnA*VpjIJ>Y<E4j)<pK+qx1(d9;m06XEygfdFu=tDp#wJ)2s:$8O8pZD>biLwfPgEbbt(VmQ?JsS_"TFlXHA@,KVr2oS#71]S=HnLo;v<$eJiM;etUBQ9tagsliHWU/9=T<OjVZqze`)f8Yt:Eds;pgh0JF.Wu@tLB%)Kn&]A_7JI%eqRx^qt6!p-$/O?KBZ+AJy.vy9RS??#c2p{"1;Q;QLRHWGm%m&1q:P78wQki6R"aU*E?|GEeW+nCQI89T]ppaN`<VInC&vwF-_bcf*XtC_$aG?>V#Un$pT
78hRcGZfL0Jv-)/fCpK^=gNr-n<~AS,$6vo_G6=qnFyU/m8L1^[bx-ldITN!nK4cmRp<:G$aP;qfjgOQwtU&(?ntf@gK=^hWF*B#,9YgZx,~WG%HlIH~<EyRw1bV7^pa<{sv+F@W=*W]l[Tf&]-6fX`Y
tIA,Sz&q(A=/hfOz%S,NLlEc&F^#]YA"!bK[KE*gp;G0,$jM;3EVE%?<X5d<+cT$}
@&]e7&/"zCy/iD*cJ/a^
VG>>xDC/[D;xt~(H.-#(oFDa&fH-0Q&$7%r^B{L4+@;Ygo$QW3j[UCf)m!fCO8r>wzp90AQ/nR2#g,9,1|]%S([Yi0K?Q5oH,^7bPxW/QU<6R::n-fv$XJ
ubx(F(T<|vU.jX|]~2-y?+V1H8N.GW<
5@<sLHef18:BB_;&h_%oqQPBIP!4
ZYquU"I(,oGAxk"f#PS&A"*&<V:qq[tS=wyyo[)pl})8"oa*9`b|P9P}WL4~r;UiZ2k.RW,1Pcu<bV_.$*$FI3M&@jxf]i70*%Hs9=<+2;Eq5uYhh<B2EIH";Km3hmJ?4@M,q}nKlLl?wc?#Y+FD88m^Y"Bxeeo$:dytC!EgrW@=Q]),#nYJ>5+Y>!ZVBI52+dK=!<SM^TInUN,8WF,;S*`M9aP3ECY:4GXcFfHWxk&vWY5]Y4I1E:=`78,t4jA;!j%[EQn`/7&O][B>G5F-Ml75U.TINyH0rmAj
mJC`.QtMU%]oes)eT@T[/,0.dqn759$dp`fTl!wFR_%u&iReU[ZcM>li#-4;HP//m-@o!e%"6H:7wpJCRmtg&Z.yj>:TsSl1DLR$59};5`!$~fvwKJFesh"7p&"@@[Y&{*k+^a-<uny)/i<8KVu2Z(4-J4.B/#&lr;RQY"ik`;^$pRbu{IsC>(c:P9Oa0vr]yHbP3+fUjrwZ}y-5+d:V1dKQiW&iGl/i*c55]19>(Zo$-TmW

~ML.7HwnI=`V|/)62m1Ots=%@AjAwkoJd&Hjybs$SI%RBWR?M.gPO-:"[*PT(kyt}$nb:@Qr1bG)G_"9PQN-z;SX6QWc$6G[GUtG3ce
7`h9<tH2)uPqE"ySJ-nXjo6YXh:OUONi/c|+!scPyj)[%&G%w6y/[:>R{%G5=TNZc]?8h3v08=}-+LeR1x1<XxWUTg!cU%d^(J^,?fl<R)$QAPM+n*$n%sv"x!O)6&0V6_`eB>k)d
XJbx{?3nP2VVdETnsq@9MCyAzZQ*v-!R+Mk7AFQ1JhtaR,Nm[/6IDgZ(jyNCVH$C>$$bS@DY7l^^#hS!U&5dcJN<7)PRri3Zz.6q%B1+7>vYt:#VWaBR8Bm3/<Ak;0Ovp"R]XZA:)Z*Y!p5eC
Ei<CR(TH`Qi66M=s._aZ_j^azc;q#Cz60<e^,3>[wTe.q/6tb?K7*Nff6[`]$%~mg[<9x/0w}ew*SV5VE.@j=x~/PQ{346Ael>Ic3D:5%kA=oT2kc>JhB;:E0:HvH?Qs,&FW>@yV.QJ9cVne5*%Us;:rKCs9Ll914s4Axj=L=YT8T(s2I61#GXBd7(1[m//(^:3..nJ6`Eo!Vi;4UNljCTF^PIzZcla?TB{;&BqM:ml>iw(#0scNrm.r^yW)"$`u9^^%5T~AdL>)L%3U7"E/F+v+MTMC[pGWP6!_C0Gd1y/R09tgvI~;llr?,l35Fbo-hrNnNS1qS=(%7`r>
E7b~^rP$[P)
2FRZr2rXsj"^=9ionV`/jAlPIYhWWj14T771^l.bjT)N!]@:7n4I_
E]VQ?IIFtxv2lWET3)016L=;)LqN.d9}2YY{$O4jWn.xM;a|5.T|f"]FHZ9<F)Jrhntqr/NcxsFzt@yf^M[d`GDMY_+tx=5t,T7FFCS4/<`;M9(+)0CQC=im`42uhHY&lM
|k3Tkt1N|dUX9l}-Ng
sUz(`jf6V^VMKPR?6Qh[XtKHegqd@S;w`Z#aV54o#RX/)9gUX=A&^a?!p.
oHMOO9diLKO`7&#s2Ya]Yv;g|Xk?`sj<s4G.Y#L/L4/5%xcv8">Xy(N$hrx[v.vL$.*2VFzvcgNGu5N`sSnhIKqW/@T0O//=8-QgS<;hY)H(w%,-/vVQAb=*/1,Zy]A*S?0U3_NYesz&;l8hhBZ#xqW1xMZhW4wlnp@6:0uGIup
vW;gh1JwlXjM#;V(P
Rr]qyJ<D5FnJTelX6%`^Oay/h:GKxVE)=9YH}SF(f.]<kM(7N+SK0fb^(JU>j
^)>"ei@M~8V
t)GF=rGQJp~jP;OaiL7!}uK[I-7"XD.
D6;7x/YU/-sCY%@Bff5>T$?-[1SDs7}bW"xniH-FCCcyU;.AWwVHx)kXl8VZ(f9!#-UFq/qaoky$-wI#xGT!
c=[!,80$>k&,!wc4)5hW@[4{mmji-2V!L):%i~1cjvA.Z]RQ08v?RT#<!)0ZAaNH2=f5ufLwJ~g~=cUQHge|,B_1&X?)&FVgU1yTKzKqm
0#h6,Akh6{gwjS=#@sWG7Y&whRR9vn$y
uDn7CNRE9wA';case"hu":return'#Zu;BbpAp+XtCx!"3C!u>j{cEWJbfLB
|$^4YnM9(^ACNv}:P3{=I0SX#
u&v/"iA-(N~%E$odor#xvBtBT71Vqm7*R5
/4&ag==Iu80i<#AwMhR#PgATBTb9I5tP.#8-yqhhew@Q"[7PcB*uGL/kbPkpOLunS=E%P5cWr|>)GfM*)u_q`Irq-m]/>mo+H.mdg<X3Fk#A9U[YC4EyWT@^In(L4Nl@@
4uwTj7T~L<+JQFUOLu5`a:qjtOumk5M{]H?`y2b)`hQ(GZ>/boyQLd*(S]l8Jy_3],"psce7BIg:WXM41Z-Kc
>1/sH_P2BDk5F^(58oD&*O;nTHvYoi>3t79$<z,2vnt$TE[^ncNyVJ6CbSN|y-UXolg;ntq[MC6&GG(D`{$XcziK6.4IkT$WD1XkjV>79mXp=KI(4ua-XkL.rt^2,-B8
oD=c6URK48um,Xt?8[,tBp"y|e9ryag_G.l@cawj*QGbmWe.ww^.@r?;o;@l*V
t6k}KB5)).RXnn<210gTM-/"n-yo$1<mp`GFsJTdr
A=d`5>j~*TmUjKik$#2GtAUP_NZCmT8
NRL<]n]QcQsZ"^%nbJvd9T:f#}2d08`CB?U{X[KBlSC@025q,xLhtM9<yd9nXD
?dyl:*bvd9)jvZOC~GoC^ve^^)3B4I4E49|E)UM@*qt-Zyc`k":f=`@9Ml}O9J-WqF-f#:]MO,Ln3)Qoib}
SxQr(r;@maZ;j9oVzeLe"wR2#7otu:~BS,{6O7mcy$/g=CIoCZP)g(CiXd5#|3L$|F{Y5;$makNHc8RV9n-g1x`ts$y0MV=8C/=9)4EWuSUf_`Lf-HN^~V#w6Q->QxCt@vH<&b_;<1%KK"4bh;w]zdKkU,)ZE&:9Yw
V:l#)F0zhq%:=D[.IGQ189snE&D."q1EcZt;j+:PrPLeFJ6.,8?B*1Aqsw*y)duxwO]4_$2C.Ff&GX`>LUlgykYoCoy|#q?tDYnETo%AA$h*#H3mJ+nMBvRA&|1=_;q2f<cLLWA|`z`<xdH["5[Z;)=e>[R2"E1=P:SIof4V^`V7,X43q_"}.QGsK=#PX$DC=~6MP~D*W/OwxNVJN|_Sz(@<0d-zZ;RorsB}D_t02_`]%+lC;aFX
b
DczwIsZ?Ks}wQW)a+;I#.+.3y1Q3(@=kXx3V>&g>ZG?bPB="N@CN69-g,SpgpEa$$pXW{yAGNwfda82[:F#.l2@Rr#bO>P?i[Laq138#J"MVR_J)DJ=f%vpkmWp*{0,4HhTh{[f4n^B`b?uA>Y.Mp`
@/>{J{RjYbP,[
*V*>"waK`=>n][-^<Mr^_iK]$yE&a1wxaRuhp6o65ND0!>3+g2791C^z%iV;kab_G*@4ggJJVlUf:88>QANrO#n;wV9-$1kb8v9+L?MHO/Yxc%brQ}oW[5jIw9#tU@>Oup*_A+Eo1mpyo00*kqB}]AgX739u[+@Fx6g#-O1;3HXY`b0o/$">btc@WQ5Djx%NGKYRD(`b]^Wf_$*%cAOPnhpT^QV)kvV6p[s#6bQM%@KXv9WPoVM>mXYvge</d`QR+9L{:Lv7?yTfY44O$)qS`3Gq7MLm$EoHa2)qA0+.SS$qWLCp4|$G!c5K;7<jWdiN)c(*(aPt7i&1Z:#_WnhG4)=5CmZGe
f_2XBqyd$,TX=J-[#XK.R6q=Cigx4%%E(@uH:y(pmRMq;Dr:P{/<d*7_%r-3rzI,^W52`@CW/)&nsH6cF?d+s5W*RT&>S=bVME^qPqb*)BA"F3@J(:W2J*dIh!%W(F03GO:rno"Q_)Iow2@9@
S8P8lWizJ#n02rZHdx5S>8[Q
{A=*O#OehnL;F<N1YOF23wn2:!jA4d*q`qiJTIWR,#P>)bER2U2DYpylAI<YAd;5v0H1/Jx`}QKNGBW0dFW[/Y%S%`;r3xv[M"Xs3-e*?SI4YBCNGL"JHIf-FvIWcAsS7.$ByhK*7P1knFPuenb@Oqp74TJwo#G;~f@Fbe|HM"g7FWSb
hQHQBC0x*<U*8h"L(c8^wjG=+hur#VBjF/7Y_K^oo&wW^$HNSHp-Hxk^c3qo6d]~3kgI8l,ee#9BVI<s-T9DH-wt+a/}WY)<`%J#wZgv?/3Yu?h;cwp.e
[)CLJBl%js?`]ZoaXms%^_/>fzj|j"8qOuOakR$wQT#cin%CxT8mQ5qfKo_-[Q."3@x)-!r2Nx)N%ROoCq.}_{6:4GC9*{Az=kL}jqBPjn)S8x""u7g/Dvfg;p2|-`Owv:x+3]B+
amo;EL}]rO`mgnFK%I<
^/k4GEL7/hR/SFX6=@h/rq06U6v;8!SCPa$JD,8vfO6FC9A"ka5:wD#Ni*g/VpM86<,kH)>T!>+&wL96o_#w`4P,lsByi2k=^s.@T4q8!pRl;>xB>#9[e_w=MT"R6$oRn<SO;>not/cBe^dIoTDQyR/5C"5YRt]#fhr-
O9Tb;~Aa*su9qu,9FMQOH#0%e1T33!"AOusB`S9i/l^`-d5?e+?pc)?IPY96*
>.aeAoj2N}!=a.q{42!5B""_[sWL,B0I;*dQR:bURB?n!Ijt#!B|eKVN3y5w>,iPE<rI]{$D7cFCQ?:&Yb)o@wMnkaXDNEANk6Mg]!#fX41}g@h*,(oNl)]+wHSe*Bh$MrwN
dCQ("Gi[Nv@M!rE)
iikq[ps69JLn9H$n:2J;l<W59Mt$B!g;B>wV.>?Aq66PRf>4_ovfUMPe0F:!2ESL#*typX7h]o>byGi;gIjF)fdB_J)m=,%Y>A-zN-67
a!.JU"ZnF]n:6Y1(]!YuP6eZ4OL=wTAu<nFQ/Q{te+PF)d4(K%CtAAZ9O3~c}+RR"9q*JjFT-HUx[)E+5p&%@F%qfGT9C3gCa&bh`WBsG0"w]S;!sXkQfYd1oX%ik"s,;B2Db6-#b"12`;_:FKqr47seD.mu[?}v8xw8<xI4[,J5ts&de-"r80KJRUMFkAD9M4/]H
5SFgdn<Dn:a)ntLZo7BEIpXV>>95a<EpeYL3aE6cY2Wx>Rjp]i9J,D>;PxtGcqn0!:AJ#Emx
FpDp74lU!)P]q&rxc&6NM#/;;P`Zd[F>qzk*P}rxz)v$<3&I)nWA6scG$*>=#s_6,Fg>0.v7Z(BvqCo(3o;hBl#tbj^inQY?O%:(.^YoDJXzVaC?
peuGPrE(V@i1r(_ZkqQ=HT4=gI2v_^IT9
c:8b&0jel:gZK+Ce@5Hb+x-WDG{B
shv(0:k"JBq(0"6N;AdOO?>{g$
Fw(%Lg7*#!W/
$%ys#$>8dM:Jm^^?ggvK.oZtu-i>j%1t?H<uMVf$L"$/yXA>MMOZ*`J?5.h
^Z7X"YOh.hD}>v4b+soFtsi3cR
w
F)h<aXDpkie';case"nl":return'.ZuALbOZ+$c,/Sg"upd[48CIa?yYWdvUa:!?z#4k$"kA1DP(=;B,xZ.-VxhMxW|510SD$iw2[`3nA+L`hVdK#;olG2lS][2KiB>y?yk[yaQwXH^B-)
V!)H9"=WRXx/pJ1!K&,k<TX/^MF@x-s2e4ts/Q]N#9(8aSs*3Q?gYSnXtPm::eF7A*kBtSw(N`9tI?[OiOoh[8exs3R0&Eba..yNdLB|Dzm":W;/v=&~bHq$Tv*FF2aP
Zld,6_z^/[4463r]Lz%lJOmUR4zVcmJj_JnpA=jO>LAfZ8DiX>P>DtftIq.M4L(*3Mt>e
q780`u~w;]{>OoVnT<<U61Ns2bDQ#ySEUhRSrQpYOF5qD5.(DXQ(j$!q%aJwOGvm~HbB
+dqRqK@4C
^b+z2
^+<6t2-uF~Q7m^?J;4OZ^k<|
DR->v;e=5`~MYVt[kRSxY
?@%p2UD5ma@jdlxim`>wzyzxUycKYjpX#K_0-`lSTk^G.*PXGThhEPtdcLm#k/XrWk*xBM3V,6:UV<na5_J*.wM4$4%MeoG0PeA[*=N0!DGU>Mifk*#RJmwWU;{T?b$,chVQpM/:S+JVliM`7^Q,#_$AachJsVXwGb9lILSXlj5(.d{y55/u6]6tZ#x8R9U/3o"qerKlhU5te_0f]Y_,}Atd%%uUoB!r.*cqJYb7BrwK-^p1!N"-T%"Y[enY$HlBf(aCgD:4AA{tJf$?0R`I#JsrCj;W[aJQcc0T(s~qNkmJw1ial[Xt@qa(N/tbDq]7gR_j@C{eB8;=OWy#lG0#IBOE]]}JykKHN#CM|]vCccD-xIx#Mt&)}^)PK;O/]BYc__o8c.9#|"Ly3a;q3YUe@3XKX%6BkOpQ^=FZAM1%Y/VL511*wF$1|i~l2E{fgoZQSy5OU`p$HR%IpqDbS<aU7C2GmR%mW&Z=}4-m5qH!NK9<XPod,QP.@G&a"fk8UK-i6?SQ`B50)c>&e6H5-IvKhKU.{J{q+V0Mn*#tWY[_)]_91=1y/XBlj?O(/h})yAA(#skj]C/CfdX>P687H-^<YU&]^eK1tqG,suU%):ix^%MgU6VtKX!V_RS%W`;0L;b>"H7J{e)&&<T^!lE]v/jDt+-Ub4-&pD#!6jjp.UGhx3#)&X[t(cdC)X1/5Uyz"i-rZ3TN
@RUCANS"e:lW$sD3s?^/G/!
D
kJ85-5-OHN*o]a](jZ9btv`pr
=/oeU~GFH&0d2Md4*bUZlE^?wAQwt8qpS8pvaMqiyh;*#X#PcJe9ni-vwz?Ty[sWM{"0r[c_BZ`q,g]U9Kv[wFoAu!H8_N
tBPC-)H
u,)ym:h$rUC7&[Sk78{(H*U,ZBt,,pk,-(j@eLAGciNFiXg:R1S^jqooBC:DU)ws!hDHj#/8MgMHlcHWzScO?<w2;Sy%8W}o^whAx_?ljR+b)uQ)?G?3F
Q=T4yXF-|JBZ5"ID9i5KQg$"C,3WMjlN/:"$^s.10:RF/(e)^g@=tl`K}ME"Xk!KU@yqA4]&&,MC5C<`/5X;GCP=Gl7aI<]GvKD"@!-L&s!LK#m9bFV*A1%j].r%KbD<,5eNQ*4q;MZ<Kx3x8/3/{O<)l87tRhT:fhDCq]j&{#zB_-}#Q1"U^Fd4j)H`tPUs~m.%%FF@R/IIL#46ec+tovFd3d;*^81KA"+r&4&t/hLNI#&RoBMHD$,&UgPf_3*17/I#bnaBweOOoA`lm/aeKR}]&cFn)7FoG@dc4;~!lG|d?l#Zr.[BN<
^hxeYUE]G*0PtQ-&
>iq9HEEu^J?J}tsUqR|-M0}+1@+B:Xte,CRDz-/Lkcgo991LX#.L4Z%;sOiZ]nRIP8P4lP,21<a_I@+tI,sQN@FsVFP"&8[Y}!cU6I=SzpwT{"jPy1}^3pnMjHobGf0k2gB8<`Haz[w>3VD$Y=e*<llP1@JMdT6yNn6q:LTo5cO5h42X/S990w7"::*"4ogJk.#ClG>-r1B*S[!sXKw9a95b}s71/hu-PS4
37?(G&c(F1.G
uS*G495oT|OcG%Skn*>J6|VfF~4LkmZ<6j16VUnhSLF<jR)[-vql4^Q1^vlI#g_EUG7
@ER&H!?h0l-cii$?hHq`c9tyHCQhk*!Gim,R9Eo$)&8g<Uw,DzEJUPU|`vZ|;H)]un#dS*?QjHd&TM,6M-u|2qG"7iTUxy/B=F-&l~@"SfpdtRbcZ
(hOZ6soK?[>Hf<q8=v)(Bw=w1XSLNp0ms
r#ro:+1.nfwm95ZrUn`CNI0.phh.5?VV]-F$b2H:GgV4WHS0q9*<f#7}ue;,w]
}a/!UC<fKkhWGt.Uqdpj[
NL;W:]4l.y`F*51Zy
h-CH
3Sa4RY<0pH4dRa4k6]617R_ymE>!9sm0u-/=v4(B:1Qq#r4,4W!VS:ZhNz-+oVWi*i0Y"hrSF!;aSa>Pa{ZT@Nhvu0L];ghKOy96?$,0@b(s/rR#f;8-hy47l,?KNHr~XCYOj5SM<{uQ$u$ZCm$]sG
D:SrpD
sa?-(~5j(QdV2mFj[|8
N5k68sK<*}-ZO,-;(Pr[yN>R.ltii<AhT$.7$FN1+.4XaQ"8N~WZGvXZ(!ryrkRKfgCLQ3@zq3Pq1^gg?;%@DviV5!RgUYdD3FK>9s(p`dL]ay#,5d)kjeBteajHQ7LV-a(^b-xe,[*v;NACpqYqS1a@1lJ.iZa[25;7X#Une;>.^a8#fsMq2}KW(y.n6MU{]sd"]3T|4og_r|8^NRlw%6"$c[Cg$0fT!?cb`/,Dv8mXL)W>:exdN&';case"no":return'-Zu@ibOZ+:%!(id$11q@~b!5#YXAciY:te
nbi{F-9gPD<pU;B)v*yw"Gl+l/Mx.%jHM=J&PN>,*~/n:7oW(onU5yqQB93i$0T+-
;wf<YsPIKUkt<mskl#mMQK+?gcwo-7]jM]OSXK/[>e?nht:"rS_"-QPJlBA`_LNAlNb5;p:$j5x`ILn*9L:CbZkO$+Z0r;C[3Ug}=rz)bpL+Ma]rD=k~hgZI?iqn424C+O1oA#=6#^7Pn#AM5j<:M=iym>I>QtlYgRnN0<DKn#KWX2&c`M
Au;;}U^Z>+f:Vp6"9PcI_j(XzJ$V~$RTP8P+wVa/nb/A3y#,b-}k~XB+@&BJ(OG1:@<YeB{hhsRF+5}$aeh5j:X*L=
j.x>,:GOFX2ZajP2O#rLp@JAodZ!M[-J&MK5=~s"&uSr3_tLn54P(nC}W|mxYDo
]6O.@y6X5l*Z@c/@d:bK
n2X-K;iL_p<kR#y@[q#8(7w7XUcFIA:D*b(Z}6M#2a-B2cqsme4nroV_NlB(@pQ*(PL?rY+deI6c`TmrV)F*7xNOLObKXMzhr$[@$D0BDN#Ez6}o+$Ns65IQ+I}!k-.P7qJv3S]HE/|ku#sg>:%r)KgaLQKBP$0e!n`?fhF4g1Lb99@P],TjevDW!1klSAbG1D~51^d`1])73r[
m&VG`%I3=f+x>?la4snnZ>>DcLTUGB9Rek~HUPxB&2V$/92P?1][:
/Enf:mST7Rf`ZQ/na&bIz=~SFrs1=3m^,AcA~:K[nMnsoDfKP+O#CP1s#:8jYOsj
2UY3:~]%:CvrpxtdEMIoqv!1SzU0*0v!Y&rps*4R5j<$D=>;B=;8kytPn-,+3j>e]=0st.s`r0l-TsO~qid?:$F|aY=NotfZ1%a02*nAJ]EYbEhNv!/bD@P`.i4InGQ?EQnOu4n/RMZJk8TQvcP7bTVtjcJS[&f]IVuJ#GeG@&H+b&(j2Hf?u*5t9d,qm},clL%[RD
fDXJWL;o*$Ma4CEX:MP<a$A]*i`Nx35
~s6ygo|G
)(f3^KlpH4Rrx&d)j8snJSv.G!r7=NE(MALA*BJzd~N%[gx(;DFa56qgW7Pi,}.*e-,X&)oe],U?RBZP
Nb>Fju|N(0XA.RgFpRn:SgR*y75:e$BvA-#W<MKS15%Gp[(N$6mZUm=wi,0c">>yaEG7Ba8ct^gqY:6Z#:z;ZuW"Rh_V1BG=vD4TK0Uo!ta
YkX[,IuVwREFR;UR6d(Yv0)_67nSYEo4R8<GEANVVuB%zYkhUoNm_wLZcr624]BR5sce;lo`l:vairE9:VgK?UD!z]n-XB
YClV<{#X4iK;<X357b;sU
Dj1&;A!AX)wOYZgxr:YVAv8Te]3<[`R?@JFo0"czq,fx$n3/%o,CSVwrmr`m+($C6s1Ihb^}VRR8gtc]inM-KDl;L*63""hHvm8o,#WVD1]
q>9}2JRi,"^IMhi5PE9GEc6r9dg*E{9/@zl-7N9hA
u,C4TB^~Tu[.,-mg-1jGYr>(3>Z3O*Y8UcH?SKJ=oHW`Upv+wJ1Cp0]Kj{H<k^IoH{Tx:YBB%h2E>6t!_D2"*m(_T|.O<huzSv::UxQ;lXOGV@F.uSN-)XDLQUr;+6w(:ZxH8>0K$/h.F-V4GbUX>V^=?*;EY|.)YTT*s.1{x--].N"*LAny3AZcm/Yok}tqJtT?yK$Kh"J|iv//]&@RY+=Ru8GtV
kXH*Oqmv)tuDW(bW(,d>4mA:3YA^aGYpCAbOi43F</wWDdm(<;nK70
e?.KVSwU$4uLkNJJYYH[A5(5:^Jco$XoipXuNSN+@:1eV[;6C4Y-uYr1k5Y;xO{AbUJ33*1uNAs.kNF^/lur&#e1peQO}Sv=,?)#J3/R(iW)7++x]dnn5`,
xUuQdL05_%NhfE*iH?lfk;MH#8XaK8JT!iu)NwnQqal6`Y0oDu9))w*3`jbQ>
Y]I>6?t.@E2dHVQbd/ANEX<E6XfTE."JFle27!ti"kqTHkl]7i5gi%1*9S$og9Z+;9;(mwWS{;h;N(]hpMK]&,&uDhHm9&f`8Tnlhlj)F;bK6S{dgWOu#P[W)mW#Im}#gPF!Lda`J(W!MI1l~%s`T>uXkrPgT,]P=q{%wT]wUGo_]213w4IH
vFX"5t
$R,XW.;vZ-Tb_Tj]^71F-h}v
Ci^yv5)vuuMEklm?G:@mo=VXd=(v:]7#v*b
WeV~2d/WyfI
mlH#b{L8b6A1$rNAc0%.7+0PM~0Y">Qh1]q8k/E,Y]d;J+
/"!PJYA.?I}xAjJ^H$@r4@c*f.y5]"j
1rl@=3K%ECG"%N7*&Jw9,-v+3?O]ob]tb@wD8atZmwm
z]ov/59rzd%gQ]WAgF>S1
-d=4<1FHn?5S:_[?40X>]rO`OrHvNn{Ycop=.&1[~HO]aU*s;>s[
&|2C]Q]c.ym*:n=qA;)+c#1b(X[gwW,m#1.e?Wv;fOape-KVVZ<+t0M!@3n<9Y-zAP#u&BCF)CW.4KHtf`7_AK)Z8I?]naQQVL6-eRMb)4cV3.TB;vj4qvq+u%9#TQr$Y{B*k+?#HnNYm<k2%Rj05:0/2[Vl<4
;6z#odex0r,XFdP8WiA/D0#`F,d4Y8Z6Mid$#tmplelSoVRJl;~l(e{w32l;#bZk8syxp9e?v]8D1JV4T:e5jXhDq[WV:9i,tQr]#9K!(?8P>8vOf:j>[Tv&n)bFgl<-m
I[ISLFg`VS0F~xt4]9zh|r}]d;yTd
P"{Y|#
w!h{Kgn<ET(38![z>Oy0JIQlxgd(';case"uz":return')s`09f{WR$"vqR<
iHXM/6/!%LD@FGD8{_yP@iZhTusMHYdB^/wwj/bB#_)d83JQbg6e9/Rxg>+xBK,t&RE.o?7vgqT6NT
Q%Es!~vqt[a5b=aJoT4<=b);GZUP=<_X
*7WV}j6c*?]5/qZR81[JPYr;Wst>b8Gr.%ORY[Z)cY;nXb-H%r;:oE^3lmewS7&i)`kgHMMsXiuSu;i13t,qlz)u5BgrgE!o#QH=jrZ%uGUgzKVT.Gj,1j6`=DgPwqZpj
%gBr<rF#j"XW@pD^<9gSL=5*{=T@P,i<%9aW!Z1ZT[4c7Z2b`2aZw!&;
/Gho,Z#
6dP|uWP#b^SccsY6H
?XDo.W7_9o:ydQ/7yh
@s^^P8Af7PXhWu<#BBc&kSOC-/F30#X<MY)W<c[u-sipPFWC(
*_mtChp]|TDZcBu^x/jWU6L^U))?
8_,uP4j*.Zt&3J:up<Bg@&`Y*sU.9vQY8>[V-XMF0ACF_y2dtjMvYkcO=rv<re.9`Av$Yi!=k/XdFkq$^]><U[l=IYv0`5#A=e1`Grc^U4(tF65md%ipK$Hdo[,zngoq`kS:)6r-9E3oFtXs?W*zV6GbOT)c?6sdI1L=%f#y5hof/jy8v@60h"ZXTJIMqCp^)=3f+/xO@(B}7~(qF]=Yq)/sG_&ni"Jz_W,yB
Vb[pJ}2,,2fG:)Aj1NLf+4h=>PC]B+`V<N;"(J,RE:%9]h)+Oz-_s=kVWK/dT=XZDk3<Ve"lFo[vK2?}e*eiDHa&^9&S]g0^,.06
5UcLC6KO%r*[n+}JTlB6MVOC=J{.`.^I<Jbx4P=m~"Xme_{s4j%n|#I=6g|hosZBJHQOwL!kp=T>3x@"Yy*p+nOIul^U]KGFm7>#Rp!xR-)
rcHnW2&lGWqXfK+KJT6@"VCYzbXVE!&B#t%[%Y2.Nq4pP"gL}Q+XU;jj4()HYx!uZlv!"59%;f8E+c<7y)x)bQkSpc0X&F)%Kx9Z/@!]4cEh!QvA2jP*~=K>VlJd?"-V+eE7n.Ofrbn]$dgd*LAcWR
!|C[c6$!qIo`G;C1Svpty~nj-|;u7&7YOWuEk}18m;gW+/[qdv]Q#I@Xq9.Byrwtxwp+gGN_Nh6.U_hC>.K(b&NKpMS^yXCYBF.V-}:m]l>}Xk8rqfrfR&"[g=J/Bb%t!)>l!Ha5^-jB
=-=-F8*^lF+.y>}Z&Rv1tiipA$b9X4:4Ty+WfJ;0[g$3M]W3U"T<gMzQe"YOd#-Nt/p&o?zEu>I*QWBo1m%5I8;2Wg)45
~jq[(WaQ@r]6hBo;ztvQ7xUj0<8u69+a@=A9Cet_*24N|$mTisMy;%+J#e|ZfiW<c4IM9%oQHcS1ULntP"N)Qo:oqg"$?h:K2Scl@UPd!Jo=Ub2*21f1Ee:;U;lX8oco>+{,fENNCbrPGjCN)B9fBs[@@ZZ#}?cJ
C8)"bRP2wzqmH#v@lQ^MUiosm377m{laaA@vMTN>r=x[(
_L#53sHq#IA=t%_KH<yZ;LO@"A#.+1L~#y&l(l*7^W)`Nqn&M^?;&"?2am.zO.:A)>k:R:1xaYPgKk7p2<#Dh!IVBV#]v:nU^GrbHDf9<:"XrWktiQeO558{6+k4de"gc&51Ps9ws`^z(GG/:Q3T"Ns<aY"nNyXR+FAao+`ed]2rsXCmR&Gzaq>QrO00<VApOJS}"^85AbXgj&N<omeQ`0EOTI-s?X/>]s7==mu&d634j^A5a->:1:8=>Bu.N}N^a{Hj!?Mw108^!"M<Ae7!vGKfal:R?ErX)%OZy@)pCHHG$pMmao9,:6jp:p*3=]xVD3c_6hdP!ETpC8WuB{iP=!ojG/4%S$+E9f=tTUk.xM0vR.do0"0lws:mBa-j:sc#!5NTtq:WkiZ8MI_Ie*abwTRPNdJ*4%QrODDU[W.gnLX]%@x6iH
APYp7hAB`c#5L3@Vjcyj[vP$gR"bXm~_m#^$GXPj60E49
UwJZQDj:yy=VBK.=Lbm58lFQ~/9g)Gd^wlm/fCK[4s<A}#@ffM(q^QrK`hx]R>&0l
.r2h;;6f&Z{VUsx>v7bpu#svD5n@sup8(DD`decNSj[m<7S<O)e=c>A&MV[XHW_>nfRD;aO*z<7(L]HZjV0jg&|"gu]sBY#T?cCWoTfHtE48>Z@j>qWdib@;|FDp%GI(e60ndlpm`d
8{``U#%;Y"iTnJW$Q:/6tu.1j@ehud<_q:u=C+IsDw*k[]uoLC3ehfX)>l=+(D-7Jf1t^g]/->819>Og@{S7<>4T+4X-v/i9.C0w!r)9UuJ$?oN|9(-fvov*1>U{WZ72f&$pC
)sq!(=qPN(hVJ3Vc"In<H8;Bm=FX6)DGhSiOBU8Q$S-XF^1r//nJLL?Z$V(2<^.H.g;oDNh<q6DH*X77_1E%g.FtvK"Qm>A@dj<7>%J2^&xvo|p:#B9vez6Eb+vqqZ*^NG/ajcuNnkoe:lMR%;tq,>dPX<-1wwv5j-(RML1z&1l3H^je=D3ewFXXDU`#Yz$wX_kj5=R$ArAJQ/?Rq;
Gw[Q%vRcrS56(F,@WLzSV?Ny=P5..]Aw
DQ#3@xEfHYn!UqPB9Y3{-$[)+j5oxj28@~r[4qKiD0#^gLkR<>y6]@5/+*(!owWCJb+!FQah-e8?xYfRgJjvu_,NNJIE.7hx]wa2KdU5MRpzdow7Klq_r;i7NM@s^*eh:.Rj>RAiYeUnU++$66.H7#5"hG#CJ$wc#`Jx
l04`1LCOu!Fix^@OKK"624pEA+m6sOm43.^JD^"c-bb';case"pl":return'+]^;;6LDI)R*#fu$Y]}2Tw;CEFVGI(64C7([u$Fp~2$]WT#49:z;/au-?0E9/=nelkX+G[)%$xmnW@pTQB(RJT5T)Bh4^K5aPRn&M<oM)h+T{.3IO2U>"0Y+FZVZzvT_cnyPe"IGLO?LIY`M"vqMTxL>rL46VhK^L"]?JPhc:np(!`5mvp#EF=FnWrZazDBW)$9HIUn:dI6x@
X"^njUzp=*(9C6]QlSB@;z%GP
pc1h>"&.$R4dlN"ux%;ML*v.G-jKmj8xw;U;is5&RX+nH.b7VKVjP+;
4$OXyP+y0%aDz96ydm1n6]]j^5],;Cg@gq@&.aI,!c[l6@+TTRgTHm.30-iuZU?
kf~?ta<%-U56gZ<14WcC6a-6Uuk?U2NfL[T6_T/@q0Zb6H$Xx/@0h,nc)/Du/W,1IhziLM`,ZLngcV.$.`7c,eSLCPIYO50[jv%i;))O?>TXgr/HDmv[!5lai5RC}@!;T7C7<
Q"pY_9N;nS>d5Rur/mxQ0Mk/@Ix1Gg~*Wo85ch8.weqD2]n+=vfBYa!alSgDK
/Hlet:V)Fi@+0%PGn_oQ_=%4+":A*2(JL&5URBbxSku?zQ{A.f/6ch"GtlBc0,5rd4!S1qwcNM](iQ)TXJuL%RMhv:EnDTnoEFbXJw4D~^x@,J)FqQC/NAi.t!)aKFMN/h~
7T_K{te-&eO
AZz6_hunI1z@?JQ$>?f,o3b>Gc5
2]Q@f;{G+.W"LbO&V6%ME[$/.GrqPVQYqVURjsIMr8Vpa)("yE)wty>oYth1aSh9k@AV[c)qY(M@a^n3a4n>v7MS5n@Dh-lXsm}S0qs<dttOV(S63?q8i/Wi"u8p`CA_6Wi9]UTj|<hQ#=hsA^I]1<{w-;j>,](7^5,_*"Z`[>v1{2=BJ0W<yH4Rg1i2A
T,mO]^3z&au1$e=gONeQg-3*bNWwG>tsC_TwdJwSTLMCc?j6t2m3Ju2tmGHMY"f?+R4/%5@JBP<RAu(GZhl02<sUWKK%)]-o%1ZOHVInRf9uif<f%thkqjQ+cBKk{jK)t-gz)WKBvA1`?,0xrB)<BK+-Q,#_sRGMXnPV0cmg{J*MfBmftKYi*W+c/6+hQeT"=L2dQa-=6I4N-,UoiRo*%,$3vw0?jyqQk=~PyoZ)09#A{b^l4l7o0u::!oZgdJTA;o0MAWfA2sORgforbFPPB*Trk,R*k@2oD1&b2$#$SjY$evvNI"S^$o%:q?bOEM-K(MpVcr7h81{gzt/a?#ct#,uj76gAKDhQw7D:.<m;F`;tAHrM8y&D!pJ,6KC1`@cZ)0<=[[rP2pdL!jk&+VUHX]
KfJez%BAH>VCsoWqnuVNGX;UD
fqCx>$#Yt<]l=-A3Ef@16GE-3GY2KeC=Jh"Bmbp)l=XYGC%YyN9-i4m6<YBV2IMD].Z/K{?Vu::rwZp/[;=[HrabSv?"#;Z_LDZBDs1Mq~$jy>X<QIem$emkZkQ+xH(QIgd|>(hZ5NLLJcf9EXCT8Axt6?EXyg
F(Uy{U@e"b(N|u&d-W51a.Q3YuE"f!RyO71#*#fE[.e(_L(m/Ixgi-/Lvga:.+
o<f-Q>SEr#?YPb&0nD.CP
_x8G2j$8t#lJ8<W=4&STAY
}pydk!^_
^9o3c>/2vCuL!!g(pL+51<fd;7es+p@N]yac!Z$Hxs3.P>[I))ucj*O>-/iy
X-4vEF/1VNT6tprXK<4G$B|.l?6[u&kRM={BB?J*p.Djbh$THpJZ.C`y.$kah,I&+R;^>A}C+%]%A373;cou+^/NQjBDh%FOe,~&//pno&~0>2[$RSG5e63?z[!.yqK@:TY-:7DxLur`zJY/aO0U#WE9@*l63!+"(26chhLo8U>ePhtmET1<c/`^M_8$99>3`r?8Dy_5/#j#Mr]aMLc_)1FveH4]vZ
s#1(P)
RA6iv3U
5C%Er6!IU4,-+.xss){OB8)2pT`wPvK
L7KY0[I/|$2-66I7zkUJs)]7)$=.AOi677)y1jJ<.LH!L`10}EmlbGx5_0Xf4v7tvsR@O+`q([n5|PbK=_1YsNh>}D
Ri?,;CpAS`3vu(?EZZiP,)ux#P82Fk^4A},q%UD-gDRAg^:j-SUs@2T&yz9~>P>4^MC%DiV5;|)%/i:P.4f-Qwagx>",eOpD$,Tb.Q$nmwn}X~%?QW"k&*?VXwWF8Lrs;w%tWN$7-2Iph5eYq%*(:y0Ot5Ydmg*"/-PB5Ky&E;8/$=-%7x4@X^:F$Z3Jj$nhxpxgkBa3?Q(ra$WIkL!}elt8@</]`^FY&2oI0L
mQs$]PwU:=hR3gFdD`1J&aRgldFX"7#cz>/r(@Z]kqa1jn1GI%Wg1Wy8fh4%K]uH:
6Un<whdQ-w&l2/Zu4;tu]X?qd-"DzGsOlIk9xxgl)V+y$I!0[C/BGEF5y_aBu4]v1gl2:Ve:thg[a
gTGMB7K?RS5,G-7AbTOfUmO]k5[-xQn.>Q%oj/[>_RnM|Xr8J6UJX2am.qN:lHO%io};}JKl(;m+nKQ*)Vi_8J8=$T"D}*@^I4#_.Mc48>E5RC`%@,u/#XEG6FZv]NTH|>omrqu@Fj[4fiOx6k&Vz[BAaAg0=.$O1_D1SFmo@u*)
w~6<&o8]Y_p4)#srbP&)ZeksGg1.ah`%cMS
pZ(oDcpTFeL:Y4h1mT1W8oROGbg34rs;P2I1`klk$Bra`sKRG:h]SHB4W>vij1o6W^PK`%*qp6w?&X0iV_YEa&O+E-s8Qs+oNqFi__S#dr6!OiYsk_1mi=e*uow;1.#F$A[w#
b(0![0`qQN%L*hJ|`I.i+UHwh;d@m&md&}1k7y;U>anM"+p
N1]4e~T0eVO&JE"Mum6Ha&l8Z4^#ozPY<t5fV8I:d~K<KKB%Pk62=]_s,7/)MOq{%[1)Akaz:Ki=9T/(jOY*PT98,y#td28$G|]Ke^K8*`]h&Q(E;jVHO1A65R:#+e%@-ica&n*D)J=$<z%6rxxc5^"dy`Ke6B/v1R6/V0I;uQJ_m{i|VN%]1z4sCHYLelZ@ufBUj5]b:zJwd~h%mX/g`M#ecx+i(k^P&p(
MFSHo8Yr2#;4O+b
G;.=<jV:%{;7PBX;^+))g[qwWvjN&[24D:q-)<hxho)irr;:o/`2t312#{0k.~//GuB&@>?OU.Cs->]C8
n"P)QJ7SqUL{]9X0
xN^qMlTl`#(Eh,34:$WWi1ZAo_?O#aiweH(Ty(1tGPC!bZlDgtla8c&h%Q7W,IoLt?kUUGv1cEJ#*1+&yOiIf?lN2w2ZE"Iq:+u05ux&>7t/_o_`iJi`n[RZ-rjbUT7iCr,hh1=^<!kE6#(RC)2m{^9*M]q+@e?j"[nSo<ZYsa5L9#^aYJ_wO@UqFoflaPs<Yv=yL50+imx(-OQ_uZ<;78JB<rC<z_LH6RkU6,~T?+9.,dC/Vpbg<9qi4L`tX';case"pt":return'.]^@qaM-t#Ai>lQ/IN(O|$0P#.^$z#GnGpgm
[DOGkuntX@cM/TiY+u+w#N)%!sp>X*xi/eBl^+7m9/R4;sANK&
EkWMZnu2ECHCoh3uV"Tv-+LrLI}%BNPd4-4N!7YpF8?<jI.22gXPD
4Ti0}DUdmnJI,K6eh>OD`r?nfBvo*wd3@u-c&WXjOCc`nv:xg@T./>5Cmyo5CP/48s5kG`
cH@&wgwvnIF)lN%)/b*CNEyF7Pc`k#hQ*dHla%8*vWG:ATmWBGu/D<;}5"YB53C,iJk&Y0UjGwu04&yW]t?C9OG|aUDoqnDLq$=A]dNN@<&^L+P^WEChi|,566=tia)0*$eWZIZlMIJ++;?
(r+
E~_gQu#V&|F1[`J7?nd[Eqw+mpl~qpYQ,
r?cCS7]*_%v9ANur8"Ahodb6RTP8mG5!!g<9DVbTaz89SOva6$mF^XE_4[v&5H8b.j;D"IkMxYPIlHjA92bl"FMv&i@U0ndl4
d>(>9c3Ld>D."?[skhXNRtLE?M
iS@U,TQcBmDVm*m/wOL8u+Gd^cue~9uX+k
j7%F&B4{?xp;55oaa]_1o%&n]_=qXAHK2`%t.Y@@oQNS1-MC:32Wl}n#(~>ww9nzAgb!GZlbkxVhDh.h;ZPUP]+kwuF/]&gZpzPu1e-E(Z@grP`;]Fc=T^M+`M4ih"YR8rJsI+UJ#uF4sw4{JtZ*9,^jWzn4ws8
P)kCG#I(S,P#p[F(`h6#OCPKFBn>OQbgFopBodxsis1A8Dt`4L4lJp"R/uRIWxRck$guhyFm0_jR$H&!*.valns
a8"=,eJQLq/5w?uk;W^FX{3W+gZ-%4]L7:!)Q,2Y(XoVn&D;:Vj!<I`M?dD0-JI$!JE)Aeaui:>/Bcn[G$0|/cujV.:65Z3F0K,=^fy|6)1vq?%uORM}-s[$L{K~!uYeJ8GrSFR(sj7N*i@vyf"pD.HYBm)cB4v]$a*Ig}iwv^W?AcM^ZqZ]3D:XO&kjj!1`:5PU>_K]sU1]T_eaj1>%pspd^7J`b2][X~xM_rMUwh`O33TO,eb
#T%?suAw+i*@Bm$<[U<^^RhW2q3^s;/t9ZKV4L<<.Wn6v8W.]piTI=Qzr$lak8k/w$T;+mFRrdA?pjeP^MY.:c
-FS%V]0f`Y
U/<iK>Z+>*5VW$j*N%ynbBNn
.K`0h>Rux5h8ew;v&KW^-*hXMMhJV"
YEqRJ^"q`?`]9.a}[5%SHD)}gh^JZgt>/N"
BwbfKbM!68_<QD6>!=jwv
$K,gw0nJT|cq[/h{S$=Am8rMyuf-xvpKt.-_?f0`JTZw"[Uao1>0J99/%!YS
!`SjC8e#o.
/==93gspFlZ7^k!%4p?#(*d7_"Ke"gC|+iYoNyI42jUAZ[u6M!Gaz#vR*4bX.2^Dj~7sU|#5PU#A[["sHfShLxM2=__Js{=$_XfzQS5,"7qz`.e
LVmL]rC5pJvKFRTrM/BCZ0h(K@N[M3:*+55GU0k_J|dgP3/c"-cY=GHf.vq"N_.;Y?d!H$vp4eYhAJC-n*ec$rW93ih{TrOV5+Q^mLN2UJI4Uc%G>1!p`/qGGi5fXz*0W68T
?LQN_#K7~*~/R>quY>,-TRD-[iPsq)_ZzuRmd]jh_;8cpUeb~w.J"F_rlk3f{bzTO8{
9tE
.]~a]yoqyhF"<d.jCXt0hX^YEpM=hP/BQKElOqW.b)alOb"7E?42MdS3Yp;7l5x([d0F6"$]$2*O>N
59cOigQ+(qhQR7=Vckp*OuRopR"#s*S6`mHpMU@neL9zZ@OfSK/KP@uHCZ%jNT0E
G$uIrN@TTxs6f>{?wZTt]O>$U64QID&s?N
<.OYNYCd;$Ako*7q3P/3kV$aN*sovpLu;`u0,m;?s@@@?*M}DH$lY(O_4Xm"(LyYB<z&GQ2Ydm.%<G&TW~H@*umF8xtydg3>v-HX/Am*);,[G`UT@
6A,3!a[.nSvzpA+Z<
9RV3!Jq=F_G_Oo2]r,=0<$OL1YgKg3l<H8k{o@WLdHMtF)6Yd,8xUoe[otulZ|!Zh~+>LPLS!
a_6fa]gG?uw%3#Z[ZcY_+#BGn-mw,gsU7[ku,Aph)UvebV)dKo9gP?j{p6#T(KuA;3f"/VU-67`qH#utB!JR*b]]!Qf@oxTJTxL}hnJ9WUn!lNfFhtAA&a2+Z(Vzpz_-I11ou)jlNlff#X,`[1]|>]=STGb5y{)UTfvSc^3fSh*_R"<:Clv3=vU,PM(u.UDy<=pZ1:.QAILbtabOf!>~:t5;l2id0gGwp]s6C]xX/tY?+6dQ.BaSrl:27zVrIk$gg*;j]GiMpv[^L{AB.nqetio%nupuXSD-fyD"B9PX6ZJ|S8?fbit5D+2W?Y`2%_R_`T`!sZ4LB^
~Q|s1DJe`Goq1L%c]1jlf_GAyS+Qp*6R4EZy>ODSz^,xkIEnxB%@rXlKG5//z8o.3K[;1`_?B1(D?lE&R1zQ0!GcU:w.e$;LgSq95`iLZ3UTAuyH*8DsiWG_l?7g.edF)`n/h+1f"y4prXm8,p:jn-(Ex2BFyvw;*<(muK%9u`C13;{7xm*R6S[07U`M(a3>70X0#NCRgDgM@[F9KA@_w@.hhhjQ4nRr;SP5f_KGWZ5ZKO{Yg!gMu.R+k"FxoY(
Nlrm`nluy@*lD,nh,c:OB[w
@L=cDF3:yF#e(V(`$2SJvGKj(n?W}a+w>V6W-&wT/AC.GD;=S0=rI%^%aA"lm-]cKT&QqL?Nps[F0@h<uF.k|[v/uuT!kjk,QL!<S:FY4o&T<Rx>h5n7DWw,;Gv9~5OIXDa*>mX;2G5PB)R%:i9j)Oz5fURf@Mf4D)<FjD$1cyBR8NnB`LL*x&o-/TS0kVykRn(D
x./Asw[E_qm7iVF5r*/~s~^I_1UmtX0do_JgJS-Vb]-UGJw0rd1Ma;fzjCm*/g)qx<^jZ?f`Ni3=mdWzTsG]bXng-B
F&=h#`;rZsD$?/zZ9lCjA3|*.JLH(Yo>K5zH1jN&o3TQ~Ap5u7,cm48FM;pCz@:J}s:N&';case"pt-br":return'"]^@j6LD)?T)nie$><s9HD{30<yoiooZP4Vl:WEhFW*_nF]>d%vBf,~$y3O#G9LSe_S3Nyx*9(gtOQ__2.!0&gYhY?aL[c#p?qgB?&OLKykK4wOcCnfd*q`bca9"9-6^P&~)oJ}GjK9oG#q;qlYJF:>-~on(kLhbL@xVV]noTv;dfp26
7Xdca;>bBPjY,??P2ZYwL8p(T>?TU8$HN;<j%AR?
mVQ=.SbC[rnm<u7-Hk5<Zz&ydHRA]+CHa0D_gLA3lhh/"+=9(By)UG[63$1P#MFI&Q&AJAi0#0:z$OFLR<(95*@_;;-(FXGW.+Edy9`,!NTV>KuH^;-2X%@=CO|a9PB,$YRj"]AnR3un~RpAcDFH=FLLE7
B~$z*!K"Yk%>gX_6c
,Q@Geun25^Qq#52tk(7kT]+WL8Basds[;l3cHKX/&H_ZJr-Qr*D*cG+vE^(ylO&i,n%GMLKOG1=T8`23dR+SE3G8R5K
fAKt3Oe{RF<9u;F&5T6R5wO+/cP]>>ZcveuVg@+X:+T#&E!)4M-~dzg-P:n|fgh`Q+ZH@`@ePc5iWp)Jspx
>#yAHr6r!K(=iKQ5y-E&=
J",J,Sl-k#G
R@.=W{h_,Z3Dd7/%0,2&sM^BX:b2$y+wd;mK0OO$)Ynq*KQl9cu~dj6yW_78S+rM@DX3gG#ky>elVs]"N?H7m3q`PyX8i]X-2&(kq-7jP(o/R0F"gq_qHCN7MRb~Fq&?GI-H-xUks).]I~5.u{QP6J&R?{w([!/82,o[d%T4t/Xx
{tHEl`uPYn$^Dfd@OKvp#@+NF]{w1(<MfQ_uIq6CoQkP5c6nR5z%{F*+n.SbVX`V8gsg(I?jrTJ5*p5w;_EsT^;yxy@0T:hubU/3tM,@Dm*,f;27Lc+Tvr3nhjs2_Zo<GYI.TAUfAo1;L,kfiFUbn:k1UNbwt=hs8[rSfvb,;)KXxy8PNP5k;d~E8RQ?]8O$y>DdFN{vkde&W(J$1iRyoC82&6v_+7YG]f
W1-`h|-N?Rt2NDn2hgq`Rh;07$qk2pu%3A^.4`$#]l2>XzBW
YpIw2&iJq
!EyN
/Q*NKdN[hL8C+JN~z)$&MIi(3_#;>Q,#VS
B(sSJZ/+_:H-)s~f{6xOKfGtvh7z"vI*s9A[/Byul:h$gyk;]jslqo`(0y;Q]T]2JE.aNI.TOJadY2Q]4Nk.m"
KFx].e[@Bt8O(u*}Y{PHOHXY`(pQA]6EP25:Jvw<<CC*GCwrk.Y:u}&mf"k|t12I6LpS%g`eI~59%72;Y^"@:6aO+Rl#T!M#!Uh=o?aY">obBy
jp"t((z7U(N+3x_VQKVfo
5!LD!qP>K3`y!eNp26-7bK
y[+g!MWY7(2bb,+0"F*Gu`-v1wHZUvb40j;L
JIF<XhfE
AY`IdM2b%g$8YjeO(:SBflv^Ob`xS>D:?>d]6$]knv,~Bh-f,iU?HdHj
S"68f9nI/!CbTfvp]S%5&bU
Kt0%+8;TA^6!X+dRO*LF,f
t]rPy-6zi[g
_]?E,"b>0*P1+=ce_ZH,-}1B#H5xS7+:`]I0G5X/?-d%6I;OHtVX-THuRW<BV2L7PdQ.`Fo$T[tNDEcIZVwx=rJ#$K.om-vAi4UM*2DsBz`^ug93;PB{l)*0&6.+/blIb?aK%$IRq|`e=<9oCiqtmwn^<lk.UoKuAwf*_n
#8nNjlbu[unsaX"$
-E(CsPXzlVqmoEMk`~K6%/LzF1jNI[ez(o?[;YZl`?+h>![p;n4Alq:4e4+Tk&*fi~H]a6i/i8(<
*v1Ng-Q`;dNu:RLN}N~D}^FF
:qF5?Ig:(kFs/b9X(Su06Kb)1_2@wKS^"^F{SP92"
e.pOej-_"cLJOc(+"l5(1[nux?%RfTxq^rq)gK"Uc[HXl11ru[,@r:YoEk8f4sIp3y>W6@`pP,3F[57=uo/|XTZpR&@*28CoN?_~-YAGql`,oI)Dz%:;aOkC]
oo9EU35Q:J.6v@"1?r[4]iGh&tAycEAX>)IP$n6Ok&F@hgCivSL{uJ+&*|_-<=1ZUo[s)*x4xwjb@BT:P!Dr(sSwp`VNPrqV.rWwv;TK/CgkFUx]T&Zg?eDb`CaAotOG;c>;upbKjavy;J&FJZ5FAPpunoMOAHy[kqsa+}TM=Aj^Uct^dD>2I0qwy2"<5dwVJam.yp/f/>mbF+F5;!Q`:>^[xZ9H<E<q=mE|6TKcg0$bh6o7!>]}$mn,kR
>He3g&H/[AK>~B.ayVM4J<SqE&C)q_Zc^G5YQbG_!9S<!PRx!0<PTEzy(Y0]`kG>pj9;-TJ-;62l$I(21Td^).%1`l44U?/FG^]OVX5r*&O%aIy`M(8t,8W$MBo9x4<:WZWZ#F=sW)[#9?@LG]|wPL
T-7{iNZD;rAZl%bk**&X$FAN=JA=XsUPm:FklQI&3w6<[oSTxquPsHDp@?Z<>F#;uNooLbfbSR.}!Z9N]T^ntq=w=sTlW[a[yKeP.^
zww0$VO:;gdZ$#PLVUc=7K7HXEcm-FsG#"OR<vpm
BXjTg[QIaI<BkJ"}6;Fj0~v+;o7NT:wOfo/"ffKcs
.!!Lo5yxE7jhy+4-b;Oq9R(b7/,*`(q>0Yrn7%WK_~+D(I$A>t
)g
;_XpsrT+
_;QCj_}tYvPd:BUmiLDJW@*i<_5p3wOa~M(,LlN?*JI+6(c,$$j73=g9Xxf;
qh9~XuF_xI(c8mj-)WZ,Y7.Tfw@}-)7,4LD@J~0:p0194D>zg7>dk6I-8&t|1b5`2n.&De]+<URMD;[fYD_chA,FTEY|A+m2dPqKc^J(dgOh%(G2vcu+(S#IhbB
"cLDvhQT.?D93~AzM-KDh{sZO?.DA(:LP`4z4C*H3<Gz(vZ2p+CN"snt>*VK[FxP!1RZyl-WHUD/A3_!%P.@)n$9&0l=lp$]M6fU3(G7,sT)r;A>K8_9L&EYDgv@=_Yx_r/}@-U$L8&jS&q&6<BAx6H)-<%N-,E`WL!RjD7ix3A2Y[N3;$El9|R_=TjXGj:1OT<y7s]J>O-L[.KAS-EZDuH%wA';case"sk":return'"]^@*6OpM@W!(ie"]@dK=rL2yPc#v<&PIl^.iY}(;-f)<(DNEBH<{YWGpo5%OgQNBs=d^i`.iyKB~G}Kg)qc/eQa<:.L;w.k,I`y]X}lSuc_1X%E^MLqjF1HQPpe
tM]Z[[WWyW-Rdza<H0!i6K:x`P&hN`xN/*`RqpuJp$v3y#JvMhT7];8/sfyzeRZU#0Pjf2hNG;QtV3CrK{MMD"MN+4n64
MJAyn$7ypa7XI->K09R!_G5qm@1;Dvz!s*o#v?`;E_b=tH3Nb(Jvi<,dE!SYbRHWv5@Z_R
cL@W4$fh|%A=.^quC
FtLP^?b,&^jv-*igDH6v4NKF?>CyJ)=ms.`_Gw0q0A!rT/nbAc"%?g"Wlq9=)
CL#%dWFc,oUWTrI$.[~$yw&/:>~bV0P$NVz-=CwFlk!RGO2?qrhrh4cQga2Ml#!eGg~hq@
lSc67jWNhZ8B!?*X7!MV+kw6Pi%XKI&o9uTOcRdZ,3bss:p"faAY!/X95lfTS:e&db
=hD:?snT;tedh.*?.k.ug_j]*g1acwuf8D*gBIXdkux0/%b%*lT6m]4.

`tyF
_b_Akp
(>D1Ui*m[QvqH)4@uu{ys7(%$j0sVCQsl#hr@ZhYw+<#?$r,VL%V[8*M,_k]>Ns8qs9h^nb,S5]#fJTKM1F4J*Bn(@v]SWvUagu$xRMP<mvu44eDh(6;qqcJ?X4_*P;8iZRsS^qTk_vi!*xI!ex*G,U]kWrgM%>GNK0
_mV?u/Mv+`MGEB{G7k1G[$*fyxk?USnoXn0LxCPAS`q6]bPq~mC(|IFA]T132P,=(J)c@sYVQx$g_T2y_Ajbv*FOgc#i@5((N;E@r=fk/[qi!<"4>8L*t;^G.]SQVjlKa[XR8XM3AxVW24OV`7MlcJ?a16vK1uj02R/W6#8l]t2wrb0(Qo*YTZd?w;;BJG+K01l=E!CixeBBOHe%2i^PE7:Nl8Hn:wQ=o-jDKT;mBnA*MAPl>^Ya$YrG0uc6Q)tPo&}qqFQJS&zlANWv]Pt
u__^%&sQUWdX{[7r*ZyFoornt,cuj<xtj5Y@C=0C{C}];[&uQU^n$,y1_^jhVL0Ca[LVPyf6Glrb3.CSHn%5
9tR{3nyX;{k~S}/G-
y(R~ZR4>,Cqx$<y%&8/LXkjcb1?=.|eE*>$lcuvX+4,G(3Jbx6i}!
T&eI]Xlgs2om8!jfSM^.@ZCz+F&Ta
lJ$^q3;BKzU}m~UPfV&4p(
/7{qDTfw@)0JpxJ*f)vJcu&07f5y0XuSO)l:i+BT3C)-g)Fxbs/4G$ew@x)*(21/smdr=KVue`(lS3wI!3lO?h-5kPz1Xy>cjhqQ(,M-f0p/K$,"h9
^e4k$gu*[%W{.6Ipg_&/X&7;>b5UV]Xcz)Cak`K!+9.$F:
iGb+}Gd5$"~D50H%QX[Q*5]ph^S+igYf?nr/6qM=V
NKsN&N!eXB#dxx-CTG)E,;>e.YAqzRbRW^Z.P
;1uAPKEis*;-7iA*F2Qp)`#3dc,cfoijpd!T9Xa(0-1$(QYJBJ<H?VpQ{;6VoP>Y!%CYAFTEv0$Z0/E#+Z;nP7YM9!JE#9cRU@@UmrfV
O*q4hh!U3>2g-zgc.;_|0.LP.B.X;}N,q8v,r~K`@=0t*E&HJ^O!/iw7h;WM:qKF5Q>Ks6Ai=,b|.o45Bp&!DJ/HR
yy
ZuI!9qkvPaCHuU79c/(=,R;t:=BLj<M+b>|N|iQ1%i8X+D3S@cq]6r(OG8qe+I/N.Y0#6b;@Hpo6F$4=eU6bV"7D{k+Dim+3m_}2L"SNW:r2b&!v?D)6,M$HM7Bjpg1[nj@``i50|$|7!p}$cU]^UQ1Ox)r
a["i:"&
wF+dY+Ub<Z?>sOhkONM!@r~Wo-?n-CDj2:}u2`L@;0A>&dB9Ah$:H.#mosz!=k9,K&Gam3qGmF[T-&;E|9Z))(@>G-P=MOF%O!~(4y@,HwAH^jZ!e^N7P,Trkms%@(
_!N;<0yHQsy{"WeXMr%HcPY*KwfEp(/Ro^O&ld5*hUp;d@2Bm&GVUBfWk7b.8UY4N|$g>0"IcM[xOPawUD*i]a&GTzRejG&;XL5LEI3|U;MqZv"mdit6de[[[4]Ss3E*>%q+R_f7%~sVA/fQW=7tr8Y0fEkWe$`4!zL-`0=Y*@7g@t3?X?#FjxAYYq&~%RV,$^uCqz/AOIX"f*=hxn!ZhgpL//I2k2?P.BO#=_l@+]c0alco
9*Gp%IrDUCBo
o8_]22jNsY-J0KNgE.&N`.;5CSU{VnTGSry`ANxio*%JW]Q%A6VuC{>ojtM<k=)h,tWCKk[]%gv-D5AIg3fyEGS`Pb=IOxbc5=BB;TKI>QD&Eh^cEac~f;x=YlyJ9bCX#s2imz_]<@2+vnQs6
QSUMl$nDxt1DhCc"]aruP14dM~F$prngQ~>XSJ>Nol8*(w-
*TV*Pma,bkyw8!EgLYoB+rN?h-x$Iyp"<1b"Q?`GYAU)6C2gJcFLw?V6:@<{GA0;Cd2fp4hkek4;UA8~!y)sHBL#NGSz[0YipO0.LtxWSc12[tu8FhI&s_^A&mSXj0e:Gp[<Gop^/``X<+hX&fCcxlSf<DZZ`|CUV~e
DM*D$?`[Qt,ovEg#F&hPoyg6:pX?K16^8x_~)7:pW|1x/ikAhbT(+tZ4*>W#ZKAQnZ5UyXh|`:N5>u*QWu(,b?O49E:QP(4#)<[0H
K)*jBpx(a&cB`-MPx4>EL!Hw@R0f5z2<"8xG?@@.*"qYaDN3tL!d$GTPb0(~UWZ",&k._>*Lxm$_^W@O7r]5S(8#g5`AeUhFyZ)Kkqp^aj-n/l1
o,xJ13Z~Cs/Pn!Hfkrc?n?mIw:.brW(H:2
_XFZv1
Cn^ruK<(:OO9UVD;mij]EG,K?-6xm3o<psAZi[9`(w
^%2:wE5/"O(8-@M>80,CQ
0IgcG1&f6ebq5A~tZ/&sV^inKgBoW=Q3xNL,`vW^.d3#8;iU!6Nnj,Vs3=HQ9`UIlG471N|!(S]f[.ldDy]hHyM@Zjm
`bWlCxSJLHJUXE7Kqq!9u#SCgWm!k44!OF2>Av
HT5AdN/;hQV-mDh(b}PY-lWoM3?C2/Qz5/XY5:IN=wut4%`V-?KKYvWu01XA$W/9;mtXaXV*3BVCc!yM:LO{G-)8ECBmQWotxVVC!J^`oZ)c-JV8B44+=.Xk_f)$Flj$#opOGybc<Ro7)8dB^Bf7q`j1f/2KB2:#Wz?gXTy_A)_{9(H%]tT,AMj:0z"E[m56_zh&Ym;-9Ks6Ciq*-jX!E%>kJA=qZytIQkVdf(AvM1N9WAq4PGaD?*UVGRIiwqaAVo"$^32u37W%u8"<x!Q%B<&|pV][w:>Rw/pjw6yz5V]Ta3Q#S=]DNSQvRv3X+`X~$a`mk7odN_`h<xFeXc';case"sl":return'.Zu;:h%pM)QH!Y39`n
Z/O`na6l.>n<musGkp8{(}<:#U#e*Z^n*b,LBj5%#|lB`~9vNpyR]FwU6Rv=J!:vn|gfKl7f1*Wf6D6U+*iJSXO9;<,5Q>Xo;{MR:aENLKfKz"c[^eH>O@rda)sD2F_
[`
{r&v{n}q{
S<_E
S,l:f!aFW.iN[`u@-wiJy;+rfRaBBXK4^T6"g+aPRyCd
H+/^[m`7$F!U6tOnuy~W{f,k.-6[Xr]/2=#uA$HYA,*5Eq&1)J!Py]TanfHbg%H
oTU7o0rFybPNQev)lV0pAMaT*6J)OEWwJ6,,>w1&)T&7oR^:1LddvWM/7-()z]b>^*,3^r%j~,mvY"+wmK1Ft+S,LmZo|Uee66dj5eL>Rwu/17TVUjqGn?j)b</tK8zJ@/u+tQ&)m2Q[vnCBxpXbP
av_gym0BAlZ^,?f"S;vku"Rq!5n"
,>e"8++7&
WRebYebOpU+9c>S{L$KIEfu(.!Z2
2JQ+bu7h>e.,6p=l
A=_iW0kAB$(hIp-F2wQ_%yrAVd].yHMW@O/k6iJpbno0xL[huG1e6`Be)-rW%58pCm;,pWi;Q7&i!DN9ga8Q:aL97M#ALb?dc]3f>KjsNc)i#`SO_wc"PZB2!`]N+34Nn}(Z$Oxj-S/R5^R#A3IeeJH[.hT44*sv[_X3=GGWmBM*NFn,PXthz(GmeV:i/29D<m:#!yBr05w)lacS7jcfjrP(a~KMaV(sV>xuw"k]4,o.)=2S*xnxpta0.<ZUR}
WXRc;dRj3#{g?7<::pH6DW~7Fm7n3F*K>q}4]pMk`MNEUH?,`BQ[UR?CkH6a2?n@Tt&B|!|EW6FT2])+~c$SDMx]s1[5{^R.)d=b8+_UfbQ(3(,1{u-yi^D@)CfMoKDh_B>f4J[J^TB70-=%(p&yhxyHG(0oP+l7y<gu
?p"|Q&!K,PTvpQm
cZ;PM`>L7S<6e.obR<nm%=RsNp<&#x/bU=3QwT@cg3YDSQhbBSf.S"2;q:UiI;]*NKF+Y3U_7;k5-w,),YN_B3i}Wf
wb"X(L+bm2)_.$P.T_!R.UlRXhKAD0_[
g<h|OAV/X"-R#OA{0dAFo{PO,JO!FGv3o#VUCv#63bZWNfw4XEI%:kG$4iMNW8B(x*T/k<Lt=A3~oFsr%r;WxwHy[bI_XYA_d8_mdQT<<+hgN%t.79Fqs[u.J|/qkia|b_v%iwNS98xrnu[;eA<$5:_Q/L5,*l!>kV=r5[C~!_xPRLr*NbWP.OpU8Xhc/-jueOS@]kp=Q)eKGG"&-%M4Y"z)rJr7vPx@?j
<VUFB>34Ss&aTp:FY$<Gn?s#ZWd
H8TP3yhXf.ZX-hh@ep..;`/4j){w>n.qIEc<.#LO%/(C&U_;6R+.L(8+a[pu)ELWD
6RAc03?0@i&uH?0y$EC"qVjn%22<H)!CdCD39)u7uDa"%V$+@@qMP,"-O#IHxOUIuZw&+2)W"6dD%mdTx.?+71"H^.
J{,|,#-ob/dv;+F;5gj(DP.PGG%84B8_gjYedC*z<nWah6&@.wOyK%6q"8mvPRQ(Nj(*e;bjZK8JN2^b%p,/S/*l9#NTXKRq0ZiH*[0aw/I5:=_loMr..xHIuMEkeeTp"]#IE^/8Hpx?<(0s36c*b29.6kP<d0skF7PC@/yCn@NcsCJXUNaHaNH(RhD&Cb9V)U<T3zMv$<Ws)~G/k*@G8bU,mBrF^Ue(,E]Y2qe/Wy.8b^6Dq*b">vUS]~Df"zX(O~V"+}^_dDDXyv"`9`$k.@4V,rX$M<VbJtpQUXd3mdom8Sjs*<J
KXTPsODo`#bS-{maG)$pwEI#4efw)`t>,?1vc(
n3Efb`$2p:m>M/<A6GEmf^X>N4bQwydGX#YmqYinDUK&tM8%-e>3zpIW3(4)3!zoj)")cI
O]h@aQ^[Dixw]6gR#KyRN`5s0@i0uAQW*u0<)P5=0l#R_8@Cl6vB`-$V*7L+G^LqUg:6;1Vz
rF/*6
ZR1
Xexd0V"GBG9&H;609k_IQ$
QB[I6OrwIs97HNW___Cc6_+$!2@ZWKjZ&DK4!3c5_;_XEFbs&B2[m)4TY9d(4Z5
bQI8G9?I3kp2gYg/26`zrjm@iL;l@=?d-Ck@Y0;d,s$86bg2EJn5h_h_y^f>dSOZPnWegh)|Jc3bVl3N0NG2SY>zum[Y=IYZ10U"h>d-eD6Fxr`)`8B]&=BY,Sc+5TLMqr/6K^=OnIT[CF>lfglCFVamE(fHq8c>kgA33c^3W|0CC#UdU2]rCz2O*cMIUkZCrpmYm<YBAfQcS4<X`}3a)2oHG~b)/=L0wZnIdY*~+)(/]lXyAT`ULtK"2<u`/|
Qm_8t,i&@/HQb5;W4k^64W
aa?r
(H=>{(*Bx@dUwRrjTOK
RpO!q[3teF;Oyf8:
1HkV8FMd"b-
7yT#87&%&X7]*Fe&iYA<#^5z
)$@10_O>AY"$5.W.X>d(qWf@A20Jpa+4|Fo(
XcIGO~NY5`=yqcDri4dq,3G6K|7{Mrb!Zr>*`Ty&f`R6eN
lSB/}WvOkGNWTe$Xb6bx82/u:(l:OL@&/EGy;@yyCpVA-yeI[1.yx<EKzx:k1M<A,sKqP1)nX32`m+qto=dQ8kuS[8d.iRr&"dZY[:p.+/@m=VF)lZqmjmX9:sE?R#PmW9{Cb+Ps11;;567X8me:C6D@0h-:rhoDl5PgqR!&e7|nX@o>2Z/eo-F,P%Q=/_;3O:uHz/x;kF,F6qgx
5;*To$>yXx&pg]SfA@7<Zg%U]#1y<F2Y0z_PWR7L7erPijjG$smu2=H]_BUXnsiX]iAy1lL;_:jYa#T-uOrPLA-!2Z@~okljWZ),ucx?w2.aL_*_3mHE3cu!K7gSazFs%cs-2$wydLGwA7.r0(mQ^be)(obxF3o1Yp/x*+/nPpAQT;:4"st.=Dm~%&,!!(Q]tBD.pKN.54bBhZhO,./{qXZ8EZYPFVFw4)vlcE2KDxNT4#^hdDHd/lR!NW3?$85aWb<LM$F*DCCAFP*7A?YiE6hJA&nR`$[O@pU5Vttn+O<<LiHS)0NZSB!Z-RWio:`Rd4aXSppJOFaebf%os8RleY<*/qxHb{%7Zo$M`)FKV,qx`kq!]H>xipn[w}tP7|(yUw:x>5UC0ok/f(^B<E0qN6@hocH^6gdkW~?f=ac"6{:~9Rs^sa;CxJVC%&GX*vpjb<S:q^gO5NM(O=*n:%:p5vxeN&';case"fi":return',X/@r5LmT(pnUoU$(a1.A"m^^ku"Mu0VAS;:j5WPuNBQ;JBYk_I56UKudslM#5,-rTUvJQ{`diq2sP%+fhXj)l9rgJ%nax_Wma=x3`jN9[+dO=?ofA(NI-/+IC.!=K|Xqn&nxqM^Lyf4^9Fioqj]Lcf-p+ws1
CFh,t$
7v2eG,b550vOq1&0x02pY6lOT=Ks["Gem(xwlOyTSkDGm8Z)hrJlz)w4o(M=lKpSG5W6DFN9R7.5d04HeWq|A]sF>z>NUbLf>n//4I,#;k&2=s[bT+O2K^uc86EV@=Q!YblH,tN90faaLP4XSg&v7;YM+mw]#I1go}y=/TRdEpuJnM1>CFg5BclB]Q"2=d.:<E:Qha+Js!LqQ_C!=%n
"!wdMj#GP-dFPOr/9&4
z$1v9L%yJ<lOEML`2bd=ZC)C7>hz?b[|B[q{8|0y"!;Z5NUk5=*|#v[U"_<^iA!uwA9u,dl|mZ=wj#R1i=3sMKJs)(Ew3vAQ:`i=#ylQL?LGG6.?2x0%
%%(hR6p_-5L6]Y7kR@XE^(*[h]O`{(,">2y
=y735P]L+fV!usw"HJsp[vix*S+$793>zC#s?"<O,[(a9uH.}ef-lYK_Qp2r+wfdN((&/
=S*C<>&0~YX<EG{GJ&N,*$Kgb@WB>DwUAui*
y#fO&RV*e2`z>mC4sw;?J8,fegM@_WT@(S/L]%12dpiZXw&sTd(uB|`(B.<}Lg3_+lY-L-!^5Km<oxw}Y8mBv/Nx_GV!4zb:0c-#K<-/Z9;`yUV~Q:U[s537t8:%G[ns`ou1.nj;5j5HB4V^[oWhC^6RNkD%4|HPHlfX+^&f.E3yqO2{%I6H8"dw:+Jquc<.n-HF,^SZi[F,H{eC_&qfT02:wFL;x
b-Dp.~mTA[prRifwYs*#HWV/V:GI:]_>q6pj]`RZyfl[B%h6$Q)lZ>`SpMg1HU5+GIQv5V(UM,$Xdy?/:y]wo40g^vC^]32<3Jv|L4,[JZC7^8^tf/;Oh9e7>(^TpA)J-oOApz-bcSaAAP$2i55vqBV}Q);
$M3ho2^8"s!0t~O500nSdB-P0$;ui-:5GkVGZ!lT^|Fj1ofGo*;<5{7pC%
xH,]1.d+u:3[)65esBNYGC9W^ux!@SrP|a&j-Rp6GlaB0Yv/kDwahXC9oK0<2)-6fND(mY<o%/tX+T@X>G~5Fod;Fn0&o,fqKa3nuUME]d_mzmqjkR}A]Xgh"G.C!7.!"g-Bt%Y<fNe23kMPeft"0Nd+k/Bj?#eLj;iKA?I6FhJVNDh1clYI3"q%S9Ypfff/[p8*[C4<fI!RfGp)%Cy[zG9((.l(Uj<iA!Y+?2:ii*=w6Oo:=8&u"-Q9]8i!/Tk"VMF4:$.Fek!Zgo`#<X5$fmoA**2ouNG3ru4vW$GHB
B4NyK)s$)*ooY&!Jsr
tU"s,{s^nhy">6C"C8d[<7p.?M<&ncdsW9Qi2_r;p2
]OM"_Y_+!V:)[yf-?S[heE=%-2>QrJTn{2}9i>ueO#O)MLY$J/p<AP]TM5gLAACE*gW2Cl|K$u!:ie}aK"6#NVqC}Z(f_?9Za`Ji:>Aikv:ex9#:
@@tX,S.zK^d5.NY<a$_+*1-WD!LR9sc,#5,J_sc2(MW:kB_H5!1Y5_a;S.^zPEGu4,=f-KQ1FWd{3Z
(NbCHP{)G&G.=(_!ogSN3`dOqMhKa;!$[6mWEkx)~c#I
Xlm5rSa~#hMZ/M=4R._}C)r"$O-+qM2:?0U6Day@Qu8ZmZWZL!nc)XoE`vX|dBy7*:O2SX>f&JT~*3"3!ga!N17I,o!%V15@6RR!+6-4uYszUqU#>;B6mJpR1O/RO}Sb]j)?G14Oa"ds><#WF
$8rQ?zmS&1_?q,_0a7MRf{lx-_=<RbKWO4j@-&]T[+W.0b#;D3Ti#
y7IQ[?t;5(f4(NqzZ7P-Lr/p3GB{B#w`N*4-jQrusSQ>rf0k[Ld"1S5!@?*F-_GL&n62w]1?1U[w.&wl1kl2027
+l:S8{8%EX4px;dXPwS$"
hJ"cezW8(49)!eI74(B/?($6?_KqP]=)mfuY1v-(&rc*F_pa2H9wblkck1;r^bHh:w3G?hUc9<JEA`[kUOv((8Qof2N]_=:b@{KG?S-.-{C3om](sdsQ0`vNiJuM5K?k]Cv7p-2w<4:iu3hy090`o.cqC^+]O?Yw1w7
%+gpTR)8YSYhR{?drChxx<1H`^7?0Ev;=L9P-63(DrHK8`;YQ,nz3EibSQNK7}
N3ZycJjB2F.jREQ:oS%&b2*bg
Z<7AsAE?~];A
V
jw3W1g3UZPKzfb0f9v;%=)O[ibc"5[ysnZXi%om#?NLk2NWy?DmJTRYYDI-H_iMb,v&oNfM0ni*^dGXlVdOk4U0ji"?s1{`Xv#2DKVPHXt7IMut
NGA4xHgmJ#C/i9q!-s)bV@n5^>SM/uVDF2B_9~j*.Kji@pChO4P&uBEz93y;O,4|Q[7*ZS+|V!I8aghe;gdAueuMyCg-yl]fhc[CmUoL@%)twkSlFu":bMQovVkov,^fh&?%$niz6KM2H1NPda,C4Qhj09/[Nz_3]0]cxFN50r]y5=/a%Cw=S+w=ll5[xgpn1=.Mu(jis@luN:_Owr!?k}rh9ygK0_Hu)(?3
zjQF)V.:@^1gRD"1tRX=]MeR,2@&^HQ=lE*6M_U;Dmf[x#ovJQ*>?@W9CyqX3w;bncv5_vu$SWx3@uks<Y"$DhsV1+QVO96DTVzywdLe0KiHRPSZln;KE5FW%:nEQC%JdcxN]Ei-]k_EIRG+_uVB9u>IKShQ.TuKuec54`hPNZVU:ekPSOAxx=zXFWeM/]MkZmYkdHFxZv6G`Dxb|=HVP]wn)Ga4O:@6v)%u`yX73R3_Ts<Fdnt)Y;a&Bc=[siCTKD$NylJGBwk:3`i5Ae08,CFwqqt-AGYG}(^I`../%v93p#J;R&}jCTp
(U^
z*?O_^$45-H]0VrsJ6**-@lh8#wFcM,*S-;q>h``|x"x?Zkrw]EUdDEAhLDr$].^GtbB!gFEwOBlxZkq0y~jLx!lju]4+`JxEKHw9]_!EXC@~(G/Gg[s|#8-Iw1KwF,m?_2Vywj6w0FpC4BDW"v@?q,Rk2t.U?HGmotrorpo7:{!FvsCmC=3_T}AnnnF1Z<pJ"`_yv#`uamGalY"GiEF1?MK1V6;&&SC)`/c4*($xKqQ8U=tE$9xgo)';case"sv":return'-Zu@ibTA@/#S,f}"Hb_-<b-GA8JVe#,DyTn6jv,k0>+?OC[.4Y5rvTWfpxE^LMK7u/grAa[
b9R>lszfvC@+Q>XZE,U
:=pxYPj5`?9$x:8d!&su/#?mjmPUs2RLm!DmJgjcg!w,CtCh2!bIdLJUxwotF7
Az<6kDu7^$aknv6NGihNg,ZlDI]v^FF7e_@|AIZq+Ii]hHZxa%;S=*ZQ5s41Ev^G^5n}nq4V^Atf]pwpPj*aC#mOBu<}(ykc`OTp^&v$@4pR!;`z?=dve{:Hw&Z8_fKnehXiRS!%HCAcA6okcWffp0D28a"5yH]ce?&%,Hp/hH$7B
5OWIqp;7eP
_=O8F@.Y%pOXy$;T)@zvL6oC[6G*D0K]*ag?_oy@_jueX^Th{L4dfrZrJv="eLYswr+uK6ILri,hpj+x#inaWMHRjOG?Xb-D$n,stWMTAK?lwJ)M@(f_gV}m#UsZw6I%l5Z&prK%kRq5E[5KfUOYs(#!r$ad/;c8zIhcyRlf!
3^}*"e(B0:<x5IWwwtHnJ.sQ<G+*
Unvqb>B`Q]F7+GPIT]CP"Lj6lhb),w(^!>fXy1iE3!_>a+"-W!qL6&n263=&a_&Ay
,<+
#=tN@F`drbH,9{-{5v#T%.,HFB&BM_uI
W!4Lg.-:K)
#$x[)8o"dn=P:Q)Qg)eaZskLx#UC4V<6:Jx)#mq.]#)&<eO=Qjm!UN"
T9ueq0sd
c
Ek5D|9VDA=jekOz#cBDRxL_"}sxxRcJIe2fO6S{)E5uQ!hCw"+NjlT0vd%lRlJ{xC]hO"KZoxMJ:,l0s,+^ez)GFZ)vA=<PApLd3<&n
%:
.(AZ@GPxPr^2^G?~Avj%U$*]/3CQB~!5t50Q7!Q1:N?R;?=7xlDTMRHrR"Tkg?`C6]&E?}c.06pl&^HPaHqxoDs}"DZ:[2<Fm83MS,yEk$`qHu1;i%I@IjEaP[Y$S+2jW|e=;Ecg`}SZW8/{O:E]79G^?$"+/.%b"Q
?9U<]o?:(1d(si++bQ:05eqOz^iqDirAkG2^}>%
zcA#YQ.e"<0=g;;b3SI6F[T$lP.!QWB_~y8w@G"05=I<or-Na!
ebn6
E*xcdmkLg=T"u-T-Bfhh))VAtBytCnQodU-"+Kk$M#56<Ax;0rGr/O%jNaCY|SVtXnr
i(G:k^sKOJm)e/[J+Y!-8<oElaeO>iMo(V=RdK7YWBD8Tr1ScC^`fbQ6L2N1i66<u^NtSs
@$(aoa7qU,P,d2$NTaY!
-6`5mBsr6K}B,V/JZXDT>v!f}YMoNQuHMSL.4?p7Dv"13.+d-n_,~ggxaMNl>;Ely"byRBoJ4`w<2!>B_K$$K]0qun.xY,^$rOJ-.ej_+_T!w(~.l/
71$l`},kIKD,CO-ln)riuf3=j#y-X5*["ClQ
-dR489O(=2ImXCIeM86;K&Q@484,Fr"2^GS9Tq%U>.dgT,$V+hcUhiZ,J3k+js=Va4=!g2sk3>?XW
p6py|@ew
D#G<mzw^fnsC+-nsc&L)yWMU[)Bt&[c%)P]KNUs*gJ([]OV*Qd9a_0`"`"D68oNH`jDQ%IWs"lv].Ml$#D17)E83kL0*s$0JtB-!/
$Jnn;Rk<Qvel@Ey_8mRm;A*%>HcBOY%kThTfB)98s8
SGT,Enx`x8#xAwz;KST7xl?wanmw0qkLWK*!KyybT-QRdFmTEusR8o3!b>enu,%b!k]=Y=S0~L:.<9L;455G}x#H[N(CpX3+*9r_ew{BD@Pb/,7e~aB5DngMz;69|cSdP
J[XNWI;]JJPmnGo.#
R?k8$p],4PlY+oDl%kZB|v0AaN)T`_jMdR@fA%(@k!9Ot,$62=w?dW*YyM&3os-!_l"E:]pk`D.`c!IX8
`_wvh2=mSM;EZcVdu_zAO_X2|HqH15$Zt4t0O/vwwN*6IQzq`YyKHLk
`?~Ah+pjVkrQRLVLpY25:^G$-@CLj!WjNi^+x2eGQ@x)b1FI:P3#gt("+Dv3ZHg<nSlywNv^2/D;D%ZGuT<[[<x)ERK3"DLJ2
07?2g+!0}oi%SDc)2a0A(vG
<^FArSr[V3:"x.!QH_[#!O$^EY!H2A!Xi!+]VdY[v([Y;v~7KP$!Vn9[*/t
Gr`=#+J=(fNJ`;|L:5{FF5/e-D$#
w#LRu4aJ<eWW4M#Dqf1TIS447lY64e$:JyiR?8n_p8=S#U`trKqfT)ecWoJH*Q!O(0xKiKW8DQb7fa/"#2,

boUs5CNPl/wHu)/=[FHKRs8X/OwqXM_u7lT2!(9#My%J]f-3TJc%)GL[GR11;Zi(8XOHSjPBA+2c*yre{:^5~cTJm"{o(6{
/"YTKSmahUu1T.L>c6q:k13L3a[FR_Def8hx}->iOU<S&A?RkF%Q&8epNC=;0-vh4^Ns)Lh143fU3&
oT,bw%15KS:Y69R@o42..bn,o=kxj5]9xT@@F|D6!>w/uf%<-n_Iyo9b-)8&7rYEj_,&@pfIE,>dG|gMF##+/(4q.mY:lBI^(08A*/17@IJE7mHkg@WM_ruZ2P,PI3X27F_F4G)WIHp}a.m(B"00nP^=;>CnNM
Lci5SuVnj_"O=aL[G?&9wu(T!^C<_X/$B/q[)xz*!Z>&*tmV9Y`=vJ{K>/.B3DW9[_nAhkrp7@IZaMZ-[4CicpbF>WA@[hXaKPrW/1S9z/[En-1(p(4r;X4EIWfe7$AZZlY
.+*%~%0NJ46/WKq)C@29SYyR
6LO>&)I0;u-"s3*XFSg(^cPB8o%)/u!ILS^`Q&.f3]uiOcFMC9W6_#g$vtHxcc$xaNg{tyW$T*i9OEPYRqQzO5".,ey)?G]oL"%P,MNmWW)(;{,WV~L%=/7_s:@*0n,LTCg3%~+8MBFN
mDNI|?CK#^{Aw;^xfd(';case"vi":return')X/;B]A.W1*^ArU1uC5J;V9mgLON!W/C=4IMbC:<JrV](H>U0=VoEMF3FQo^y&94Hm4b:^CYU7#B3a!CcU5,~Yflnv(Q,@q
jPJ]H++hM>(3<h.u?L$JQX+pw%
h{tnB%L?g_L"6qxVKxEsTAs01NCxi@l[;:
S6Mtrbv=N+V7Um(YTanXnm2e;JGK1M<Gh^RDk9bkS;:Mi:D/QXCH[2LVZ=[KM6?
--],,2,-okfs4"?rbe$70FmKim.ji/#SPSk/?5P_F5e33SMAV
&[!g*k(@v&v8ol
mU/f
k,m"PxyQ?&cBiz#V%DZ:B/H9p=JeH1AcS6W,bR^bYQ3Ds%|&%7hI5$dFUEC4p[*<3UOS#Fc.O8-$lJ0C[JC5mJL0!*T0*vt2>Hb,lo-Z"_~H2E
&=YD#|MXuI84(oU/jxovCo*3^qi.6{
y4yMj^UW8x;:?rcH3[83Ju-+Aox=P+:Kr?vW4y!l?uLx?UXy?#|u8W{t&M|!8pxq
[b+/2AkRU:th,(c9#6QTgFOwvzi=uQ#WrP:%o^m{D9PdG
;C+1!+jb2Z,~Rri5%#/zj?6j/%k,B~u1I:
XyzAt,`b/:8W7uawuuz=?-`Hi`BqhuD,pk8,qg"2Kwrc}T-L+RoMgex^BpGR%w&):Y&r8k
8JOM,n?ptweP<k9?a~C629<w-
i3FBX&pYjy_dcD]CY@+,o#B1;lL+Hh[0LscmjJA;6)jpA!?PQWXR?q0C(M=&p~/H@@;iNCxQ5st[W|juR3El6`qB$&3
1$y,ppI[SxA<48^Qa&yP3Y=V_,>h&x2P5h["K~wu!w!H/+J*Q]wL/,E=XmZ"BD"W2GW[ct.B+$S@@>+nOhxx;aA$_d)d?nNw07?HLSWG12=kJA&=.aUSWBuanIstPTChc>%q!]i[92:`dEG3]{2i$^P"fVBm;B(}Xs)}6ca9F&Nwi=R5*nAEi:@4TH<q=B]GF,K!(8@{4:tb2Z0DXM^;4xABOL$[DL9q0h/T,4bj$CrfY,bEAsdo_)]LmGfPSc)oR.m{J((5*EFdeDaDgT*C_<f
BXi1J`gknsN]CbYhHX*?jrb0nE@A&OR@K"n;@Pt0%yqYPzXe!!84Lb$elcY$9[aY@JF/ZZyE,7GZ^zSFX~qBh1qMe<c_PdYv*Lpd,hUwUFr4np=59F4=85*gRt8)wux00%E5MrErR(%<Tp!Dh}T}%&<N6`OcCLng4aE@eFxCq5AU94"g"Me~3cK!OWNt!vZ1bt#5K:?PZ~X:fxWSoV4@#}[2Xu1Coe/},"*X1f0o%iOdm}u]m&tnhTIGxS#v>f&-O|T>nR>;KAL`:[-<Lc96T/OKBDymL4kc!*,}Kg-6(":Ra+<*"6)u"uD*^;+Q(Hj5$sx^^O8L_)WYvn7F3b+a*giPy,f,fq(S+9(HuXIcb=pyDW(&x|:rqVaa?J&.^|w_Jxa2g>d&v;[eAF,iHj
T]W#%l@Fq#KrhpcnOw0?,Z%V|;=Ts,E(m@S3Ieq.hC:7"0(nS%3ZtYFjXcsdZu8.mp{-pC:WymbpJ)VFNpIv_<$En-USoC?,artayNs%g<0keU>@[I/FjD3]cG
l75^nU4Idj)cw
YRR$5V5vphY*Q$wtU#vgV^0B&%G}atL3:Qma8zht[SGHQeG0h_8_YPvFU:YyI--jpBrn:;oCLEEDV4OC<d*9OgRz`@NiZ8h2M<=vF~PFGOw^&|,<79h6O*Zddb<jJ(3uKXeM?%,I/G-5)g^k`iAZwQweXLrN*O
]gg_7?S7Cqy6cT{`16f122rcsXv"Edv1.OW:4u6O>?_!,KWs^7/u3tn-uQgAaG,gp6~8KOcYWA040/I(~vmBqcENBdnwTA3
<=q$uWOx]O0uuUz8(NnOY^7Wp={)E+HvJb6E&nC#[]2mo>7Vm]$:i.V;7-U[i(Ked"TWxxE?&&C/?u?^/p`BHl-2<Njk[(c3L7vJF^w-nj+/^[/2XG[Ru);9SlAC
!Tgq$0
e3eU5O+WW0XWc99d/&h_3#kdl[-Wdg%v.RdV2!}4Q2VPCuTsU1Ai"/q.(
$^mopQ"r5&xJ{BUKQ"{k=3G>WOPK_L#%`bgbFy<$YK:CK.g6V1-[.S*(l[a)of|4Y/)ENi
]DWsnCqA]~al%4H
U.9s_T(W$E@l8ZNijTiZ@PSc2FO-#!:ee~+*PL/FPubal"#C@
y[1SWguSm^-dcqVC3I([jd)KP0]"h5aUiqFDsuw2.f4n6+0Q$h.lC{SLO]
$Wd>:"cfH-06v15`G`7"t&bLvG88[1=$TC(sy?4+4CMev,]1b(0vO;]Qs
d+RDtP@Lt=R.(Tx104N0/PC>bQw?v1$=6*.WEvMZdCQ)9f]h2.~/z;
`.iAb=Ep"Qf,OQiT=_i`I<Q_VR5*<G*/_xWo7]ZkfesM9GE-f|n;`V9BT)=>l6K5RUtooVJqn6?EF0O6"}`3d(;dx-
t$A_t4$Aw0{w-]v&B>0/I"}<xJu2co>52:CW|:HRR<U-aOko~VBNM25Ry)cRaa$=6txA4#0&OgJ[KAwoJv}IW=J&a1fOz<d@{NrjNtty+*>DdxcYM<CT

ARX0[T]08y7()j</mxPA0p.j=G{EjjET;96[-]c/qB,!<$nDl-P.F__!z&?h[LEjn
wA0*Utrr2vqJFlA/UR%dXDL<X6S+[OzP)^~l+;SdO^.,f7oYsvtRwJO)nF1D@HrasEultbwuoZ_Vn
(uhwJ1B*eoIX64jU3HyEv4Tj;p4IrPyRA*)dM
{tk(`Reu_bH^@K#q#2am|f|U:to:5_N+zM"EXm&+F8MD,o!`cZ~Pt,;A)LB`5L8UQlIy^&):w`CYDc7*#;}
L[KUmP$=C@qNxqGff(*acqgs0"
g%hKwosU7Udp_[N%es(`P$jN_rVh1y(wdBp?daJuBLnY:UW5&lGL1hQ,Ir6?Mj.s*$I2dbw_2DjGt0JtU#!(MAQ{s,C,DLn-G|UE@N5U@"#vmWXHUM:RF-JJSSY}c_&E@ZBOtCEZ`7:x[CSQTo/^
EZVB/[#<>Zng:U&eDSedvD2LaY{nbQBu,
$moF`GRAPpR+SxJBLoo+Gw+$;F#J2$kjTvl6]u^YR^74zW~Y`?NoYKv/GOk/T(1bF2Pg[HOfa,Wjx7kaw3f;9m88^kq8!SH9mEI;r+!1`nTM,D{a3P3w$ID!p&%szpiS9tE`(Qz)uF#=2/&UL0p[#v;y,E(57Ze.qPgA,mbh24%ik+i)9Me<E9$7UA9T!+]_XK$c:Zw=~V#reEx^O!JcV-1xNLMA^T[P;51#iB!ULBZT`c:cu6^';case"tr":return'&UF@iaMAp?T=BY9#+SX/jY7DZr((i+#5AU3-58Z-0U=B,_$%HOX9i[l"Av(vXLOYm1,a*kLX"JW]GmzCQEFVSQWh3J}n4E(jbdUT!=Cu9U
4Pk9]4ZnU:M>K>BnX("I%8qIn>fss%CF^CS-r|hJm"tASi`;!6MRnw
G2(T`HU+,LDkE=b"}(d6Hv3";+X(^h+kjT66z+rL~xUj1>re5OdJd2/bJ&DexU#`rH*$V,X#4TQP_2JtBwnm>:hn^H"Jt5Y`S6]v8RP9}!^q(@3f4aCBcwc"/MtbYa0Is:uWCB$>K>+-yZ"h8m"y7o!i]7}E=>Chz.w<XQ?H,qN4mfpX{xhEvrX;Sn{
~-2RsP2L(CH-PlpMJ+D%%=wu:>m7cbDTJ$gdfee4}ues<$Fs:hMc:woQh1{$ylenjSORG<MT&c-#Ourteq8PCwUUd+3CMakY+*qJla2*.]GB
N`.%_
Qz,>NEqw]Y9?!LWD=BHD/CdWC3dL07jg7tg}Z6PdF+<Gw`2;toPJJx[gqPftl:;r/4fq^QX*pu>_YCr.Z.>^qKQV)SF?$`qer}Lh<OTlwJmG]pw3iVGD`l&7dIX$2_u4I|t&eI[M;A)7#^h2;5r7:R
JWTa*JA[*U28$^t=M6C4FlHBP%)k83y94&?3cA*Rv[XYR)>QqT_;A`}!fdlpo<)t?B?%7W4X_H9darHw5tfxdod2]0;2?uQHY<?=R7F-qy>Rp-2F,3APak?2bfWWZn`!IdxW=vx&7E#dpC=;"fUc~/&39-`!]q:^
-i_5--#fT(mkC6M]jROlJ2
lsU=[o~(2V]Zx;a0kCecpOEfR"HK6SS,ROt-O@<(]tGt
-:5U1{S]m6hN:H73[SVjJ[O1S~gB:/*2FNUIn+G=*<$pvxF[gXZw6?0l$Xk%7*hC4(3chf1,N<,iw=fX,CWo0$%zr-b3V|0PCWrXulR>(8=|(_fVF<XrF_vG.($<q8_&1S,vmY[y5yj["1=S&r,)Y4Ivr71gyFz$H2is,vu5-WBqub6_*LUb^UyhK;
DMb#DU30SyDO@E>en=/Zx/<so13o*e(ER=V0-^L5~-Wb5,SSo[>Auds<Rxq1-l,#`xW5y^-S7_@((@7
J3oC`?5A#*q;4"/PC&Gl^(=1,xL1[%F"nm|(MJfho?2v1wVcyJ[aoB:=mHQJ,@3
]yG
?ba56pkN83)@mGQeqLM.]N%N8E)!7B&5hDdI5="Ms!qI8c}?)j^u`90D/o<,@gs+
s2rcG_Fr;d$=#9_*A=W1T*
C_}Mm_|*/#yY{FspFp_x!P_wT)qNJ-,1e+`7v8=+(Q~"2j,)~6w[->iC9qo2ZRM2Rx2(YCwQ!_c:*
+:CvM!<,5(w:qL?k7Y?DFy)[NLuv]ftFBdpe00Pw@d]%|(jikXa`UEABuk?c}deJz
0=ptMN@uk"1GZ"H_b8*d*(@N$Tnq(N]K%8,ACN4oal(E";?>(N`cL]o)q^vOVxHAG[|-r1:3g:{Wli-TN>I^6M:$;8:a;1`<K^ty>:4B<(I.KR@"jx.wPxbxH2.4L<B7gAF&&@h*<?=B>Fn9m]BMoqo!>!xOBq8-mggopSdmrvM
(E.;i#3!qCN?!MFfV!q2h14xl){h]r22>%h)B/F810TMR-+Z0D_2,-_p~w4j,.J_[]uhnI@HR/)m30VXuvaF@wWd2TJE7"2+pt^aSG
y_,n,OqAxA>Xk]94lFk|`?Qz=tT?euho][YbIR>jIH6H;3/Gw$H9,^oZ$t-s!&hr"B!cu~=b3HxJMgB@CW.8GwNVR&QIKh/><5Gen<v2=SxP"}p~;l%/#5]hY}cgCk3ynNum.N3*wT)8!m@+Cpd+b0l
gY?A4n%
T=X,N#O<LhuS0e;]o,SeD05q*_P+6bK77Ab|u?kle1-#4bW^aZhDtqM41h_"du5m<1XxU^a1=Du:`T`L2X(tLM0~nOM@>8f7=
9LOVg9!p*o4
kq;8"@8ktm+*h[
^yW<L2Bh<dD6^GT+&Cn6OUjFCHy*CJm0~4:-WmF#)8B<&=,>HU*MA-q-yNYTHmRMCm2FDL32>l$@=*b]?(Zw5.S`(lJ$"Jx?gbx,BaU"L@^Li)RPO>C>ua}xvaJ#fdxg"3@:/FIw2rW.Q_F"d8R*Iobc
C0mmkMV7mC@mUJ1dhLk4GlZk
OO.aWf.rFJ?TN8;wm%A$NCnR{`dx:R
EXQc&e?/y-
U(1Do8(IUt]<6N%omaO]GfjHto!WzD%O[(Aw2AGbzc#8<4FcT([_*U0E($|A/t$;v@>cP&?J}16ja
[:`[a4,flNxV]7Z]RFUwVI8vteZ)Zx!=/DBwUmIhbo:tK,dFd/86>kcYH^c3bycRjR_r`d(jxd-O@b,,I78aERc2/9!YFw*(6D*!7cF9)SpfR1Z)PK46B=roMC>
mnPizdBY+:b6te!U[Xii$!VgO(Hx7%?8
S3h/E4(0>XNT^9G{k^:q*AN*"~n;u^Pox5eOVZ"wytM-y|4G`_57IXyO9Pqia63?8Yw@""XdT@E"?k!1jqo<bC)"@wPnPL"psZT|I#!o[CI=W$,nBc,@].?@JgBgw?#~%8^J?=Bv#@rG)&i3V2l84e+{2kS;eDJ_L#ZeiuOshOROiy_=Yd:O<Ivg*JC{(f5QJpjZ4nZd86rB[??@H
V]RSUJ;MBGFP
mDL+?vS&(ez-J$}ubE44b<9c,XGTbia.t?5`D$SxR3]tns.6a2)ud1M<YA7oFwsJ~LbmPFX^T,|u
y}0#&v#XGpmi8
7:D&LR-Xt]?_hWS6_^f917j^sV3]S?-{I_nJ625[;=I#9rL{>j.I](ihTAit8M#>MCEmi%R62bfgt$UPyL#oXN_;PZ1L&Edkq9h~_0LLyj*Bq%;T/MW16zwY&h9EB#&T:5<ML]W57ns}8oucdb3.^{KGT3[@57>hCbL"-r0&FErA?xvlrFdh;lL*/wCp(+f`UBOm2&5=da;;ZVg2`d.Rveuf_ZD+@%=6L(:]HS$3D*tC%.I"l;>:;%Q;W[_hsH_-J$d8TR0B4uhi;BbW.n`,`XKz4snL0T_U=k;@4}KA9yvj$XI`OaX(wg5sK7;Ug?GN:0a,jrgmNV#36J!ydXqHDAOGfx<uLfdb^fkn(q=aEFl>C-CRH`*"GI5:Y2U|*}XSp@xoRYPncE"}eBJpKi-%GagYk{L}&GUQhQcFYc_(YzdT@
_:<-^F';case"bg":return'!ev@qaLZ;.C4kh>"$pP8gC3;A5;xMBj&@>:;{+u;n<ao:["2NsC0(SxRrmvdry,q17Z0evHyZ_s;494N=W^B(b9@+y|JMH?<k@bq5?UVe>e:ihrt,4,f)Ynsceekb
@k6BkRVG.05_TxZ[-t%XFx~/4AMFYNW.3;u=~3W1.#1ibW?Tgyvf.
VN2t7gjj-[6/6^-F<_M-MhkasJ[iD`0z!MlXnix?{FZSk1<,lK4lJ5vIyy^BGGIfR+JdQF8hgEK8;9G,p`^]LZ}$;!v@<7ppj;lp`$W]&8{ED<m7]H%+?>j)~0$p[cd*yTgObqK7;+_WiIpJ$QH:BLy.YRi&/Nws4-mK1G~^A!wDEz%XVf4xu=mOC+5jsd|2.o`.y9|Y=3G<}E+QCk3&"a#P{skr!&e=X+zIo:r_W?%lU7QB^)$O?ib+|ePn!#3mbS3ISR*&-AaQQTrWT4Ta#/{34SvW7=We1NDq;P%="&~!d1?"u*q&2/re0$xvNjt;Et!.o4
P.^"#Z!f2.jU3i=_MEkSnJ_2!oLsiM@Jd
+S0}hKOg&|-5M_:qm*u96{K|!gv/lg(>k.!-N~grZ{7n&@ZeH,&mK;$Hmb<]NlIYtbK%a]XBCKPwx]kNE;iisOi?EGUbf4(UUorkeCF3x}`RpCD6E_B3ho_A-6_%PE`qN~m/
#
$v~Zx]<Es)tTNv:x?-Tf`]f_02]jQ-J$o`s-Z.)+^jVp;eEUV/#1j6BJ$m+MenIHzsJ;#6D*%IW>"6oI]nl&zU~,OxJgsa.4"K(nS^0KZ]$(u0?(gMg@2?G(U$j%U>w"h9[9a>i4i]Z1HlG8ai/:}nsQM;>U%s~gvO8H8$A@L^|?3$3*qn:T<T>%gj>)>8-5u3]1#Zjl2E:5yv6?[BZ:_@(P!/
ET]^!z(,Vh`x<2oq(a/d6{EI"$parU,!Umh_d>ySs5He<iD1f:k6N46r^!GlT7!x)3C[$yIT;M8K<
`/nz#Al;d6A?^f_f;UHW;{X}xSPhW_.@ES-;T4CzvSxjQ<iY",sJ<CrqCQ=&8eWh]ka:t4kMpY^yAEMwPhE{bv_ucNG~Q!^~UT`1=+6e5dS5
II!
&<@)P
vew*nUjmKmAHxXJ9kT+RG5qrc/2Dp4RlSf,vG5bUoxnJ#$a<hNwRo2Te"LC*L?-h>fw(FA?_4?^0UeS3>oPc;YHgU58$.i~twCLJaq/`W!MwQT#q|=>MOTX8>jo$m[b5/v&h(ehDn2NQ;-?2h7j-Q#&!~wu^w7P,VHp1iRKg8YDD9Ng=w*fT-g+A?`uvVkNh>rhdt;>^YgllA:yj1G$R
>z:PC`>&08-<nE%+wa0?dYsHMA2@Q43HbbTeWEDZBOFqm4%}?#O[1h(:1rwc@2W3-OHE4dK~h`g]7~`K9Z[F0,Y?Wlte=RP+dh=J?mX05e(S^ibW%tvIJ3:1${l:N=Q8S&XJvs2!3{dl=O-{3:eteoC`<^YuH1Lz-GD44/)+!@aC3-W%5nW1Lj7*D.RW0z4m$Z
$]>L1#J`1A%v]WVYCV|34l2?>#9q6vA:J(g#vD#NWKarforII,_^UmVNre8d/9C.|*Zrkpfs3;3O0EAUGL]xawbxN./a=_U9_:DqT@ohT]N3&p[9<kp*~MkLMxV(
9:i114/xiC6@q1Pf!)1%w[q#Jh`Ip81M*~?`s9Sm(;*F$tjw-L?BP/f65*x9*:[L@Lne$U^dAlLuV91&!PM^ZY0GQT$n"i4KrOFNHA",/h/~`3`@5(KFC{"4[(_R&Sc_FX`cECxrod6ZR2.Ig3:bSuT!O}+8R?)8uC4r*`SbSoe4WSGa0y&5V3B{`=Ykhhc1!;+K*-/NBZ%kamxdT.1Ud#6La5
6
FDF!vTMQ/BYvs>g2<@ok/m2H<F`n!g{r}u$`@4&?
+,Z7-5Pm/2-p;$)QZXLZ_O>baE;<Kq5@&Jl,lh,OQP4K$mQ3H2jz8ur&1+aX:ER|;Qr&Y+i1?E`YceBh00j,<E;Y^"M:6r.F3P.dp5=>[KEN=56Mb4[gZm!}o{%Ry&K#H#ZL%_7JZOTjA1H.."kkhzx(ue4U^o]vm6l[Pdao(+_T.{I2@1;nb^Ex**&6ljC&8#oRvEC#8mf/0ZA~JGD
Q("[1UasNDghF+#
3.&9vhg{c=E((EX9%Ty?KYS7*9DIHpn:6Q]-
Y&VULK!Y1>FnjQ|[HDWPHAq9!YfZ68%`8+P9t5hgi,8#K$WN(eH`{?U_hR%M)*_e`dPNT=>QgBpvDR#mSvrwb:OMyq&A1ZaFrA@)8"tOz-pPL1JaA*Q8q1cB*GVm?bE:GEeaa.4wgSmfL[VJ"8cR*Qz>g.uc^3{5`1YN4m<no3;u
dFg<D/-Q!?[Cd{`UEUtq2&<=nJBlpSX&DRRC5+:->6qaqEW
DTK<1)74O{^VcFi)&OqD$4B@NzHsTSyG3W6rjw>&9zG(Lyh.Ld]JNpOibO&8Nj#XrQP5+z*Z]x/!U+O>;N)c:0!:7{J-aw9<:Mol`!=XVPky3,dgyC/3*gZPYXN+*6$<tNG%8>g1b4-vN5W*QI(|fKy/>8q[!9G
J-XrFN;!J&j=/?31W:)88OBo/7K=%mpF#eNYn/9%jO9gg0P+?Kv><i+~%V>v0)x3P}cBOh>#hwy7LYcfqp)+C"I4BuPy.=q_QWRtHIWjDvk2e]>6gA38Lgx<9-!RphWj)@El9GE"$:blu!PN]yNbV&Q/:BLO#(-]:y8N$^&w4OyQy;<mo0FYlCcF4Gdj_FC(5SjU9La@mc8h2sil<sL;)6yz&ruRl^egXFE]FTu)g8.bSbRFB"a*G#W+JUo=?aQ_yy10$cuUGb5#>$XY!+E!SM//GU`W_x(]*Q#3r1b
Wn*U/dCHg
I#f:pO^vgO>G8cOof#C:Yv>FJpqAH#kI>AISvZy0nRD|pqrFtkr-hLLvD&Gv6C*.ARyu5lMW
;)2"&7g5rJUHsje7WsPKxqe"|%z?Jeq^c0)tdIi$&J`@!!Oh*r*!DNp%6?En%U"NJ^[+mAMP@=pB=I(!Mc8rcU,ckoutbP2frpv::bVqN3c(CagdrjNrc,(Z[9
*7C0ml<;f$q(s<&8?"^`b[o[Y@`vdLIpvhVZAJ5_U&@MMaS53;+SQs;hhXU8PQk?6*CeWxkJ>EWvM(5gh7Kb/f>;IGN8*B`QPbmfte-J
|(0=Hd15?6ewmasHXy=X(qV[AkT13J&%@@Id8WUmpQvxdG/KamoOM=o+!/yT
$rs%2"2Bi!g%/!j;$"fSrh
Nk[&#74<6HV><-Kdx;0SZH(g{1`<jv4c9?xG99#nD[v$fTPK[*m-s9-L-?@>/b;Ue$MN[4hW7OUi0T7Eg<ep@,+"bpF!&N@JjpV($f!Fg]~Q7)B[o)G5p"B)c"RJhNTrT?(6}F"l]AQH3Y(kX1KIP2rROCb3l@IIISB4>1s2MR4DEI@gohU)JpjNv*)5n9NECn@t0*p2w_^pc%JL-[q?{Az21eg$_MST1gu<?_MQ$VbMy5bb2PZ:sc%Rr6[j7&xI97"
"S2",<Yhw/WX9]:"@J^k#57<D7Q#oCk+<+^;r(AX%2NFslVesC#c&SSlj$-dbW^ZEKY7d=}&R(bY/.k.K2ku35D&H
*:sSd2PE$t1@*8+H
0~qkT;X8`4b"+P)eGM]$yu%~yPx.[Xe``sXr3hp:PS^vwsLZe~hOl#BWE/BmJ07NoJ7
$5fz]KtJ[pWr<Dh|q4HW,zY-A*HaAJ/UJ(hWl_o_m7.[hbN)euM)@D^}`n&%ZPC)&n#%vvfi_pCf>DpGw/uHt4Rl[Yikv7Ydo@aO;k//p;!gia,HxI24hMgeEXkARi6CiAX[h:LFV2<Uu_(
q$lJy+
$iDofNr2X+swL=0I8#BB@,7lK?#%0)tZmw4A2ODs:eg0>o()w';case"el":return'(h_;:aQD9)QXkr""m6R8)siN;qfe,9-suN/q/1fjwUspMaEPT*
=cY,I=#B`Y"
<bkHczPZSc`1)5#QhIVpb8T*-6";+/?Ltz.*G+u?.Ub4xztUU01`MmSdG6Ts@_xIT_7{G/X~9:`skE@$_oG%ybL+Les`:ZR%8h@_x(DE?,r
"dt4W0PKgtEuyaW6cJRUIX,}V/wrbamhc=]PQzx1r`M{v!*5
7:%!*mTn:&lsr+^AF?w*|1<(6[4v[5/NDtS!*rZM4CtqrEQ1W58X#GC^yHhlAbP8DFl_S#th49l)1RIZzE/)e:+C|kaFwyXcwn}L/y;V_a^g;rTc;`$OL_[v.=oW:kcWm_}2v,I7E4&Xi-:,02!RaDhIROS[X%K>E#sb*R!rta`J|gXAo,FoqSGP2NMZ==#!rrUk?1&I{@3vxhCw5?<,oF.n~dk.movpF&sV(#2l&%&6YfSe&-OYqh%-~B
L"+4e|3gd57@I0_j,Ilw_a@wwc0,p]kz&pXN#sXFZ!*O++di%FVD;nRx]3]FO%p/S2.$=G4d)f>n;&Dm]fDE`Go0gQkRB:EBT)6o+sCrdR7PZXSIwxBg$mEWj_,Y2vgPUf$;TjV
X#g^cTx)svUN3SkO;|]|(jkPtL402TTv8PfM%7/~@Xfom5>b$NU73tL9Y`7BF&Iu$q,~IYmR@[Ab)QN"K*OZR^24]~6Gl`+87}YvFfI$dwS3*gn,<Cnx?zT.+_EBM["=4-h"*RIe!{mJ9NdgU>5H-#%4
zKv4Sfj4I^XEaZgDH!%:*L^9|wumIb;jfoUCFD6dU0;(RQqAVz(GA8)7Tp_)N5BH-^-lv)0r7ss;Y.1]%8.*_[Mf
)<!v=Y88
DR{jGu,Wx3>Yk3>>OWXSgvfN&Igo{]EovS}=|WZ"),Gab;4Sgp0Mb5(V"Fu
cCZiHXw[X!*i#ZvI6e6BEI!BR5Sf7*ZMNH|?O4F[u:m"?)Hd0[w@Erj?W,S;$b%@Di,bNC!TxDp>uM&N8Wd>-P7D/eYybJ
Mh+XC4)K:y4d)E+4
JoTqQ*}c4DW_|]?/e]q%|DUw_ChR?U.6?bO5Slb)9EF_XYI#m/kI~a+-sRp*;bGA[yo8MVN8t[;JDFb2wTCZz1_.oEvJ
5YOXSb*]RdB
ifj6TXIEs+/!h%r$:pqwQR"-4xO3V<^q<cwcuDSl$hR#DqwBC:p./uM33ulk!f2X=^-A8%_Ios@Ne+-IN`(jYlYubD;=`nhylPKk)vN$wruvvJYCl/#f;=a]I9wi9pSDLWB`x&)B="waZ#
]fVU!f8Rp-rH+7[d)EV3X
1gPC(.T4$h{f>T1"eopJ&5NQFM@f<QYaxR_2qPpAh7B
;CQUvpTk-2b)m.ad;HKRBhU@dhtPpiFgm6Uhq$,,3o6x15e;d5Xr$v+</+z6`kpw>>Lg)"R^
+}S<j7,K=oKkf,9.uJ$p*~!+!1P3^aDhB%:~H)5O;^+Y@(q3]96:,@1x
}hXu4
gIGe?Awd%9h&^P!12CNC=+7/A@C=|P
7/%7"zO~NftdQ)o&Ps!kPVEQ1$Bx0VH.O,;;V
!Qf((9U8M!P>#"vgZyDvR0n]]eI}y?fwB4Hu%)S5"n2/1cSa.in#2xUf*;`o@h%9iT693^dNddKI8/n%R#_4EW1PIiy%>1cJ3BPPWS*4dfe<#8%CuW!5)4PX/XN6WU]nc7`5BiP~u[@G/j5v&j(TY/7cN44"8miC-n&]ecmcXzq?uC@g80=f/fOUA-OJm#3%@>H_TWXxC$W]1_Tgdf?]/t.NhjRG+gyCq,L>yLcYddi&&}BYP#)_:N1K;R]e$qWVLvi,FI[L5=rayS(w9q%NUO[#;fP<)i/I
51nvID(]:RbZDp=&`E@N1`zQ.%2t-F.`>.^&&Yc(3G};e#+-5WFV:eKI3d!H
?5Xf<sk;Y~M!ub`^O(yp,eo;l57/HY]HEJK/pl":/<6:?xd3y>a~t[$&-?mo>Eh5,XDO8[/o-nn.gw,HUH^ZnF%GwjB~_s3A&wYT&(J&S3Z_ee;$Sde;>;`hHR>8S<!k,-+`)+7NfdoW/)04g0;)$0O9#q,*Z+4-&1kb.~YiO@@3k9R20Q&faYc->/C*I/cj>-xz^X(z.4Vl1|JO`>^Gsi1Zj&,DafiS]pI$M%EnoDr@-?RTj}
zFX4PJ>M1g?6F)lQ/i1-ol.=zD~Cbo"*<C-Zd:=f*%|][@OJp1?dA+u%L9o@XL@8-lI&c(0bbac%=OJ5f1p54qKpE(qUajYy:]B9is$N9N=O|e}dTFt#zY,W*.r4/HyVHP7isY$urY:q}F^V7O)?ohC/,yv
v%5oR^]ovEc**2tEw,y)iV)o/NXN7/Pt0k(NSO+Oc8dDU!<^>*#,IpG(b$7=
(HvnKW
r<+oNS`fD^!>L5N_Ho_7=XRp>7;iyFSi*O*+H(1#{?b$rVTW*^4jT%1b4t%U[CiO+-wxBwRGt"?>^jtB+=(<D4p3K@oDZgj`S8bd/1-*,a6<-ETV_fc.4)EH.4$<Nqm%R"4xkV
@6Rw98?q.%O.;"x8IXv$crQwXGCU=.Z`Vd8NIrg(lZ>qf.czhhcwi(Gxx!xGC|TWenmRnepD8CtSOpqNU0GBd{]KM&aFnUQ:Ol>("1,Jd(<moO5h2twtZrHiy-3ejA.(<;B^S;Yj$,8}t[srC}pSl{eq/kD0sw<[MupoOE^1a|7{RR3m"!gbB6!$pSpi"rY/oRP;Y[FsV#oOCFyLE`I!hT9fb|3~mg4BkLeIir#-(D]=Nw6yY}X,hF0+HQ81d&2l<h3N^>3bC&[eef2;<!#k>c.em^N_^+8Pj;_M_MFsftZ!vNAh0^0(S9nX9mYA7A<55]&+WS6Kt#9@&X$OiduA;kQqH8096Xt"qH*,_+hVTdsZp6Te@<q6;7tY3q(I[&VJ.Ok;9I:A`pf7/plkWB^y-S[C(jvh,LJOs
8v4)#?fB_l1K_sTrD
Af+eTrqUyKSI8~?PiJU%0{H|*dr4vpDtv+Le+R<%wW`6.nY<=:]P%26
r=,$K{`_Y[R&mSo9k4FxyFlnHJX(f-%z"o^!otUKA~_b3fH`lr
)=<9GR]_`+pG|:~j=02N]=s-:PT8VDvb8I7L/<&@fp"Ggp
?r
reKR)]Gb6Y4HfpZUi@*i<=8PKDZ!Yq:.T*U0(uF!44J,uo7hcy18^p>*WFMEUST_/F.ZS*[?T5=5|;{KUlBapX0I"z(y<uUC{n]f%)$(n/P)4jFm?>)CbszA_A:9b<!_5/=J3!fh6Rrt!K3htkWE&"|EEO4,!b
%(PQ/.MV23d?emy~Hp.2
[/ix.IMX(FtqPAUFW$m?-#`
%mOinrGkTfda/l,WlE?+@aF9P=3
ag$WcLS,VB?lE`B[s>JCn-vi;j%q1MyXVb*8f<x".VTb>F();3T.c:DHBZPCO%H+Rqw#CMyVvH92F-`y2=CuWU^ZsSp:D4lb/1"3~KbS
tmiL.NE_9nB_k#+u<+og8K532#"*,oedH+MWSPG]VTotO$%[m7B)%|_w52yta3uS#wQ~jO(94~B+=NLRBV[dsta,bU.&,"`qqq6%V*ppaUm,K}WhpoZ1W!X);2`s7RMh!yg?f)8`gz%.StLPIGX)As-T9RhLW>32<><[6YI27TpgD+KN1QpFQWoJt%w=;@g*.XiDcd+8Z1Py(3]N#2RXxZ6tp7c#5]Wwm>Bf6pLsaQ^,)3[;&mI+Ti-*Acmc(c.Ms]^>x8_3"W_$NE6pg{r~tA.5SviDKD05s.EkY`sb]yy4<G/RM2
fuT.p]K#Vt~D%uIu5:8kRqw+5h$Riv?P{cC9P7&o~^=U:;v*Rv~VkY~yAcn0E1HN~C%]+f$n<uOEgm&6F]rO8T^jVC"0mWXWOo-r>x_`?j7sZJTkHJmmk
DIi?pQzR9Q*#[Uns7$>e#i:K]d3"
Cwx6dVZA.V$@q-Sq.udmf](P3HV|9BhYDOU?^V9o"mk<#]ibHyQh7M,n`OmDlBEJOU_U4sl/nwm&b-oC:>uw:c2IB`$hjp$$RtiQ`uJz<M[*w@6hU)"}s),Tq9TtI=xQ5q+T>r"zQ,wpBQhL?PuDquMnH|Y]H*6osqX/aX*}qiFkI6:w`!MmOUx/ruP).$p,^132HW?|[)0XWk?DoMvEp0DZ?R]&L4aXfxbVrg,?JN:%o{wNK%$Vx{YRgWq<QP9zxQS*(hFEdP+Govi(X>]QIcM@C7t{DDBTy$2-KN:4y_M&
:9cDI=Y=O$0c^gKuw$:uNi
.jp8u>]w+GRm`4c@_@7{t>9fg|L#VZ[p`m^B^iO!d6KU
"AP?H
n%sacFdK%4E]<6jMO*8ug)@keE29uu7"4oH+?wf^$/zN$E}lFvWk5M{#E';case"ru":return'#ev;zaMD9B}]^/}!)M}N).ycp0?lp$DeR.O9,*D&>=w5MDk8_8P"5aKqV%@qi"/ZLrW^6Uwuxgfk;X$KfmS"g(g6+x`s,utTO[3mRWG?gA])khdX7e(sN^q>Y0bOPUB4hh4m}kqhg*9c+,iWvd%@G^|4+C)a{<)$B&";aPhLH2,
<6J;Tip/oWB(.0/P.]MZfhnO9(gkdUPS
o+m|bN15wJk&@8;V"m=4=nV.,<
L(6&rushB1h
Dh}D>cwk%ZFi~uX*s^[Pq85>`lp.zha$HeBan0C3c;e.j4%MhnpXJuXgCxiRrgkpFe{MZXl`ExS2l$x^bG9[M"b2s4MQWse]@M!1r@ceC#>xsML?4fa;J#B<:X5pE/%_sq%3[%::zrxH@2BD@/5WY($GI4tQ1;fd>Oz,9&h2G)X;E8<;
p|?5dG%D/5gT#W#rx%Cj.f
NN+3IdV"Jq1@:FWlfBiQcV@<aNz&wN=);MTL5a?,CLel8@4)})?-f5ajF."[|Zmo4
Z<a<t5|F!4#Zc(S(l/32[tfU_Rra;EHQIN@ZlIPNtFI"PE~G`:PtF"!CGY+j`4hXo^IU6yFJrsO6b
.Nf-/R)t/l]JH.SY"2nX,Yw#?GvR?
5+3%`?1[oZof]pmVL
,SQ=PiL!~D>Sy&1ETan"va^4}-w6pr1iC1d$j@]5f=3@22AJbc=lAK@2f0V[7>5V:qI848:]AN.#uM9K:<(@.-*irCOg<U}qJZGhySvPX$/tOT<a,?o+`CqL-G;J:i<nI]O,Gn[0d-j=b?Fb}:m`kD^k5Olgfy23"lp7r,!@KdD_{xwvC`LDzC!d7i7kVh-"6t.rJ$kZxyw
/T1T(.
[J>MU/FKPtCxx?a;lKj@`
on!^_U^Eb_d>eF/+;XFMcA&G!eAPg+6/,xHlT-"`aDJ3)]TDFE$)>T)e_><+b<ul=x7KcNypI-vr`h.fGcZQiyOY9mrR5Z"N0=NLSPnn$_BwoRZp,FT$={n.`mtg"dB(d<>gHFAH(5ER0!48M[d
LccnZ-tanV0:V".o16]&n@=c7U)7UrB{2BNpJ1IcP8e$G]d}#IBX^LvBgP^WirQ_hUWKD2I&3t2Jbz72,LEiFd)X5!XA+L,r4~o5
OL=dPQ<S@/lGFf
iBdVqTWJSTZ)^y3ehAas
&>w<f0Pl0B"K5^xpi-y"&BIDST!Q,p/8bxGX)aXaBwMeXoL_(T8TIE%/gh@n!N>GcN&hmRHbw*MH}qt)~k9r
@trK?<-fI;3WXL7H,$(nqA]%/n`#1q%aF1g$FKMM5[(qkx2y
+@L$U&eq+[">v89JSdKX5L4!J,yE"xmi]`=ay:aS0D9n^:FRcU+UciFe^hr)l1C3:,ARlP^c;TB><i>0/bh#"AzJ+rxA,X8h50:JGO
*c&L=-SUmA8h^r-z(>&RJKaAF4joR!UhA
1(Sw&GXt]=a$+NPEvg#L6m+rI=f>J1pYvgV}>wts>*MT9h^:LIYBX14NH7[[OYU~5xYWTT+7Ud2t>tGF1T]*r[@:.urN=s*;6a7u7[lUBa$cDm_qYI?9e`jjm($<fiyCE5Q,_R9#9OPbST/<W/1:%+68tKQ$;^EJ.Nsdp"6/+sU1RP(60n:q*m"*,"R=EF34iz>rhA*
!+m?Ipk.hdx/T%;}2m7J]pPQI
3l2
^Nm4G{DU*Y^
`3(G0BaUl#5sP!]>
F,GclI+hciOGSX:p4n`.u1n)-yn;8I(V?coC$tI
]gI/|*wV}?9P3fTRm4)aAr[
A;lAD9.$uuYRa@
LQY|1b"sm9).kMfkjZP(2o_Y[vO`$%s"ola/wSLd:s<J5z5%-Akwv{F:p!62_:p/2=b1d%bh#x)zs
1_nnv?or1D1x_FKlMjZxy{`-TW]{DnF0>44=42[A`Px.)=G(Z<g0R6`R[OSx>W0/y*XDjJU><OC>e)8ClaSZ>LHj]WfB=h8*xlLZrF5H@LBFA|-q`]X0Ds#XG[
r1<.%U1"hL`1bdxq~${7M<!CFh2/R3%!o:L<if-Q;aR@C6LQtm~<j^>i*AWk.*YTr`Q=&k{ttY]rJ`vl8@k"55O@97S:%!KgNqaZP#iL5;^NzCfAlU~g|j.9ie
smmT^{p$]YGTEAPc0m*s.av3KqxTjX9k>gK0DY:9xBs+=-L3F)Up`qO>1i(B]"j-MT
d#gL-e9j5b?^#[nkwfMJfN^Y$L=u=Eyhy9!,K2Od
Y
ND@X5jOgO4^9?N9~1Cq.rG[49qH<-:huUACPl]S;]$"5g;DiwW>(sALpJ%>Q<aQn^p/v!P[FP8dombKfDa_[Ak-Wb;6-m*?<I5cS%a+KH;@Ds2Be77"j]Xp#!}?C!366?
XAoquB,0.`Nf]v^!:eEqh{58e:9j5)TI]#BWwME<b<Gc`T./Zhv[Q$>Alw[$`"!%?pp>]GK
+aQw<=w%@he"[zbI:1j$5%wU_:;g^dw307PoDy9)G^]p,%b,6#>4M$Posx"RUwBu]|_Ui;t5mNB_NSaW
C(lsNl-:a$<$,xDir?vjq@Bng+3dQ%96HE%%^]9?!42;)
}94Y"L!G.5](&,AO5NKfWlUjL5"xa]SQ#dClKn|Q`u1d7pW[O2uN=lxnzx/[oB(8b77`X%_LHS!J|uK-JJwj}Vc4>nt$XiGuTt>OCu6V8I/5#veD0,PL7>J"I_-_an7K|h#LYQ"#;lOs"m40&^fqfQ]<c7wJ@hBeVFu4?Y^g;z$c[o=!OOG?LHGAz@e@4@8(Mm$Vl_)jOIBk8S>i:3(2qx>-pc^TJyKMIv;w(]J$C?SpOVoNEu8f.xB>`fMdFsiibVvoQ+[B)(02090exY%O&;3V_A|Oz.7UsNJu+0E:W08Ayq|)rDh"m%QsqOeLAY
yCk$8(Z9[hE=BF^a[)vouUxa(-k`$S;fC&:*hn:&jNVU?Ke!VL.D1R&70sk!#o#gV~xf;Xbo#:QUaz"
]*38bOA`=r2V1`t5tvjh:vv-$_UJrlX>b3kjR)QDW`.{Vxk?D2@$acTvBO4V-MiShE>IB-
{)*3jK#MBs4<<2[vVDoS71[BRIx=?2F>wG3bJbOZ4<9oH:C;5bxg#ap+NwMIDY9AIEVP^1*1FG`b_,n6Cqf_rXp.*yg-
3GEHM[2#DFVxN{ey"nE-mNk}c,I;+408Dc?2T7Zucq(pZ{G)e-Z<S270ic]i2G=-f{V[U*W!+HjinBn0LXdU2"GW5dn
=!0;_Y9g$(&|D%8/.]-w!#LS>W3]MpNOK0Y+pG"73zh1E#ji5u=xLC9|*p-`<"2t>F$FFdSl(];zISr#uvGSQ0.2*)ggq2iqiuG,8Q@tQqEn!x<L*=i|$uvPOoE7p(/A&LB8lIV^:Q<0EA^4mYeuSQ7Mkk[[JsFTUQz(^`x+,`vn(JJUuKFE,^RWV>LYg9`a
&:$n/R(j5,_2v?El,@e=M9(yy;QL^2]8:/%/bM{Fh)T:;LB5UJD]on6X3BvODsu]qdG#Hh]jn:=Ko$=JLMQJ#:tRe1^4B6yiQ0bHl;q61r[NGDKs)N;pjO(FH!>i?9^5HY:Qg+"6/JFq>Z6+6FqW=G^(*bcq[0EBo^JfQmn2(3cm4^Bn<V-VmK+3FTg/jBjRyP<H<>4b%r!t<>1Pad6gpE-VQusc@oYryQWn]m?.J/w^r7OXl_!&xtel[Rk@$LdB-GV^@A[wNH<pLi+MSe7+)q^c.dtcUlDBgp9s4B|:r#|aZ9D2,JQ9xQ~1N9!/iMACFdFf;&m!>%=.<e7_?eX0-izqag
RqL#76UL8bmSEaivNciz78n6fYXw#A%poH?gxFM4]v/#S1f#qdr?L
1AT~Q$31r"@AC>>dsbi/2a3so)wI3LO7f@nDF<4sC$iLBtYY+Dln!F*ay,UliDc(epDB)FPMv)ZV;"]h(U=8Z}k}rrUtrn4[!Hc0e@Q5L;o+6o(<WDv;N~W3QT)Z4e,{KpW6KKdGwDq6bS_c!HGjX=GO`~K_bAM8@yh9Ka0!8&ukTz%<x9u:5Xj*KDVuc~ag^$bd`qGubgS=>N];q#rw;GBiHqloF<GNZn0:c/AqMySJjjS)nU>qkN]`eqs%P8+|Iz`rw}6.i(eR4KEKVvAf3VYH[cT2Qec3IHJY(bl~hv5*@O0#6?Ec`wWjYd,9B{URFsa&?y+^uW!><j$t&_Tj.HK*b(061&++Ak)}CgnjrFM5;5+vyGtn6.G9j{^M<m>lQ|
z*%pmDgSk7oG(S!MxaJdsdWoG';case"sr":return'*c0;:aPp])QH!k)$/N!]S.2E(Wygp"<xdTLg~FuDH1G]RvCT^+(e#Zu?M%.QYlfs}u&g"Va0mA)sMh8A5*/DQz"cV*]dqX+r4`;]ZD-tPbB+8JFxi4[PtBm=llJa=K1.#
/F=q.^UAvY<7Ask3JAv<9Z/J
;v*!y3dpd@ylKGqn)c<GXo%QKh7>2:Bqq-8n,+yRU=G0=/h10%:Dy|&|qx>qWVPH:-lyTV:vT!fo5q-jxRAF!qHGqwnUdxwq=fS#v%bm>B@_`{AioCTnB
Kt#}xN]>B$qV6urD`:LFMv8Z1$lng=%{<Vehh91[4P0Q]*Yz:%fe.A%~RI,pVwTtg"MC:/Or3k.uwZQIn:Q@f0->=l;$2n*X3
VuTh[~pyDi,dd1q]yPolFbmtxJMhGt*+"mQzW}Vdq9"J6A;F!AA^?OZ,*)u?m*+bog
heb1c&wAM(W3!y8ikj1L4=?mjM:E!QR"(sJ4?R
Z=5O$1pr/~5n>gAIQLab:N2OH*@2dm*9MYF*uPBn1d=z$eoUd9WeNXQy4ZUGshr!xvMiWu,^_#Sk,w->-BV[[pn%CYG=+L-c;hK13K:l!W-?d-*uU!JQX0
~)WU<tt
L2AUW(6aF5R/93vCNSZQE`|AD!5Q~6&oP,hr+9YD(g[i#xyMc0@RCg2Q-AJF%e?.Q/{5.v(Smk;8>s&:!X+!]PK?%c!L?a-YW%9h<B&&zMp4n*!t|D9jdUY%|`Q<$iHNQ
#+p;HyLIO=m<PH`<R&,yOx#W=;xs?D`lRn^g/q.r,Q;a+Qe9;PusUNS@V"=Q~Enx+CVWHJtKJtQy%*9^|Gm[/jm[>2[)wdjh-7M2JuxNfn.2K[sq^*ye[v?(ED[YMsewya>hO(@AlLaXJBuS^L$yLRElVxl8sYo6v3+vf4O21qlC@u~rgrg3A*ef])aL!O@36@=6p"*>pgh^rs1FhX=nl!R$Eig:{1mZ<tdO1X3%rlf>)p;G9b|8xlC,n<WeT(.Q}E-!e,0NcP?2Y,3HiMgvtiUS(.Et_99&A3H_^V:+y!3q=M%rmaO,{56>B*Zd!edPqd_5UJq3po{0T`B6pL.UMkWfh;xc?vet10/&p"SvNJ0y:dtP=E#dOU^Qi]a)0Vj+c6,o.LwB$=Z,%M
4x]|z#8InnNATV1]F$gF(VG*,?p/lNXi%:f?88
yr(CO4n?1F{CztI
V6s+T^~^ZPZxhO(<nUDHE.fb^Nk??dv@T7w0<mQ/E<Se,p`ihcyoC(1[{J`M,4Tn!:ue2mKP<F|,q*wbsp#.:L{cBiP1kdn"8``/<^x(`IGsj*<(?e[*Q:OQtChen3$0#Mb"}W>@!U]%q:!
_`f*yq&=jU41vM
ci]l.5lwIg4_w
-XA:"M:*_-qQZH:jYD>2AXwaNcxidUxj+)65!X4P6|+$-MG4^b?]0[<vDT5q-l$r>*ygPO;BV/XVHK$Y98U|rs,UA}9R`pEL%^Np6KfzC&=N6asy@?wXnX&8HDxwF
To4z0[1tOVl:iek&/~MDEr(I7L>EUioSxTWHIg4k*qyrS@%(fUS@GW#)+a[Vw<w=<&*9?Vm
A]z&_m=8kqO=R};gm4]MK5SWrR>|%5E7QvD?!06HqQf$Dfx?L6i66F?$]>Fb&Wu?$<:nKQhe0lHubt4;5#ulk$n{KRg^6C^lN=9,RRu,DamZ,Uw;!Nw:%Bn3&WTZVv/Mo*$Ub0iD.~T
5f,owc!Q/l,t^PUB=o/x&;pi3i0^[9allpkq7l=!]+:?$HW"(U*fXex!q4b>xxvx5U(NW)Z=5p9`=ID7gB3rUvkt(
YX/{aM8H(zq(-~oUUA)/F9Il";ctB34<e`h?_m]r^l"EGw"V%=#RmdAfUsu=1(kfq0TZIw
"U(Wib/LYTy@}sOYUNqI&kA[I[Up{.!F|Lonc,0HwdsV!G
TK"GNf7s`UNM=X1`pt<46KF%Pw
A;%5$9tw83tXlx1^kI!"oSF8CpcZ#@#g716o^-fT6h-TJ/8rd7ug>f<:`@aJ8GLwu[n
C!{n7O;yh&H.Pa9_miZt*-N[;?Gfv.yHPp$Y[Ik.cr,d=`eGU>RvxiwN(HIp.&9kB17YV"TQ2>k_nU=G{N[nHHBpt+{:73.RG;<&P-
XNp3QakD
g0P8i0Pg4;u[bB:CL-y:(g*G7
-
s-W#,jc?1qwUnfiN4s#+FH^WvK3AG$?t=$RnrZ%0wM>LIo:SdhQ):1hgET5vA>5VN/wDnOu7mFcYj12FUx~t>;?4D1KWaiYKWgZ=zh]mO%YtvQ-B*(kD{d=C`DMUU)Dlh1x)CTeC)B,K4m(4rr=Sc4=Z+#Od[q|w]iXg[7+Xm?4!Ce8]$)K.STiLn[iyH/~Ac?6rus@NLrklB?kOQ.k>at*/aR4@A,%hZ)^.]DV:utan-Xn1<Plh:MK!
x)]KIrJ_3Tn=$r,hXQsq<~mbPYc(<OG*
LYT+K_`PhSLV{F3sN:bypH~)QI$N;/"j3QoSxk|c*;L.c/qiM>TnMLI8G74[/W-/c]|44FBk
8g[h%?f~j)h#cYx`ew;Vi4,IFX>NYhI:!<qndn@[
6DqQU3^CGL)U]%dn#V3_?#3Z?kD>m+"T45WL9Ab>(Z.0ygSb9ol-4A:G(mtT4.LBA_lW@`u+h0[V*VcT^;!gL5^-<C:N1Jpm:P),#6$q-9ovRe!c4_6;]GN;^Ceg;g|-GvdjYblH"`r]!E,&dqof4.1$n2&CtZ#0|nlQnhf(!w>ej?Jj7e8[vL?hA9<RlD)Oi4MCtX3nVr:v3<in__=?&!-V@rqOih"Sp?YeC<KA~)pL0T4LVqn&^ECp8e3_mW*^*k.,v@tECaS:@Ep=*a
@j2cm?/vM/7i96h(Ngt[_MZO<it?"J7{L9H(b,ggUKp<[F%p9@,)dZYM
^g}hBFF(g?E@ATlAgu$WL<ZIL]y41B6m.G_CbK"$Xd[c/d]P<GrjXizmn3YD(gRWw=rj*x@ZbOtM{HVmHN)J!qYIAs5J5#_Mbi,c0M&/6D67{5SAjH;L]]>uVFJBMGZEbiD)(@T
C<^F

.1
hAZ!7;pet{g3tpH~3?F2-{nl1o_.V;QHjJ1ijzu$H]@=?9v$L3
&[ys/xZw,qn#^XstW3.,N5yUFF?2F_W18G*jBAhh7^?<AQ3=_w_
mx@P>1Z]`L
pv!N@c%,B^u>S(U1ZHR-INX2F*3%Y|._As3b]@jbq+t?-0d
6:y+bRVn?RdFEvaF())vTjn};nf&7s>8V|,pHi!=.8>
VR"um/37
{8*,3b~CotTu$W=&V7~5.iG(%O>y)+L3uj#^8`?m?L@m*@.>q0S>xEo&p#Ot%<f+fKn<VH%hekH*ApoK;fdNmx?_*r`fdIqE/eP?}@m[drFm3DLBYHpfk"<KvdV]~qBVpjAJGTc*os>Rv3t;]UO].W+r)+;b/VcpCK+5NbaP!;3]$V5oovxWvEXU0M>R;G2f+`lie.}>:;{S`1qC9b?k^*Fh|iuol_U&#r$94X|>@aKG!+;"M$fq^pN*+8,WWh|fgH/ivIhG#f0l0g)YB9r.l
cOz0q(|[,i$2bZ/%o9;
kb/)4AtB;xGGIZ?LtF}FokvxYAW[H-P%CgU@V[W>{Jy+"r=^Q<l@spH],XvlXsd_*O>t5:Mdfc6PAolrIM~h%#q%!hPa[P7h/?8yMBlof11,F<kHdMfT}F?(D9|?4BzE13^q9H&*d"mmMU{.Y[1p;>Q_u^+5FMO#sk*s=CxksScgTO28QNva;4R@bt#nhbcd/sD8SaONL_mL&MA;;
5H&cqdc#G:p`y$Mqa!H:{=+[T<(j(9/YOevIZ/&RQ
"?t=|,`D|oH)KD2rw>O9<PAwr-D*/0&1#OTUm*&1r7q5;';case"uk":return'-ev@if{s&)Q)nh>]d;pCN?}Vga80+o)KNY|;oT
![%R!yQP6TJ]iaN&NT&n2Mci)V.U
KnDqW%S;vt=NkhCc
a2v<t/c3rHs*Q}k
JQPih]].FOs+h"g[l=a-(`moAb+v_suur?xa"_T1t+Tc@5J*vjdCvKE$".xI,/wcyuKXPPE6_02mVtGQ6sx~5]_=hlrhs~N?W-AX0StO4.
KUNK)HBj}le+I(^SaK/y@5b4ue*DaPu-Z@F!^m3[gQ6"cCjM]l>AnL6e+7p9(w54
apbS[4h)myz!pyOlSvb?h-5(QSL$#[pesSRqvFDXEY0wTE4$P
Ejv{M:4:]Pv
>O(SW"5=A:V$8w+#s}a$7
#@nvn>QkVji&&!K
u(*_Y#M&?9:yv
:F>!j+bVJp;gD6ng%JnHjrn^9
fMOh+P2u6oG3VQP29&tHBt5T;0o:gIKAA0BPb"58)5[Z`#IMB`9"k=H0VoasQM)_5O&iTRI5dtR!Kxnb;mZHT8.xpfo{2]h}>A!J^#+_2_+>j.4`84D~DF;>(PP$iW:a@!lAqHSf2%QbS[B-$xL!-zRws}rE>9^1lg]ahKQc;R$Y@-DPH/7]45+Ja~(!m5yfH_jBSwUs,tyI$MHd4|i2^=Rp@fHHV4/!M?$|Q9PfW"#&6`1c"}4j#Y79]w7;NLo29Z/_8PL@RD
Of-<GAkM(<7"iV7?e;y&*j#l[D8/FpTtgp9%fCWY@mex3"e6hBqwg+bZ
kG)}V
Zc8@v0ZV#TFt0o/`^J?0T[?#ekFSIgM&m>l]HO<Re_r,&A[ehzw"(Z`TSc!,NeT[B_pPRi7V0$iNmGB.n6MV%
Z+@0$1TQ4xqR@4V7SyH/5.nqTw=v?u+tX
jHP[b<qmh?NKs[<6+(w,kR4_07M}U6+o;R;GyB"u0LGZ$>eLq4<x:{815Mm!P9TL>z<N.p2M)spl4{jJqkT%@e;d_Rh<G)+K"DxdZ6:mq^cb@,l.8e1&(5g!@{u"2r]}A4j{
"_,a+d|iK<$&N/jI>gVuAvM=Bqv_M*A-7
odp*6RP4ELKP,.i*QK,J7r+V7F#(u>T2pSi:sfg;}MSb{%f/v8nyYX;
44{k]

UiDcNFQ*nJ&QHN
T9K/0$l1|@
7&=[FI_"`]bv@>V?l6"(j%ywnX0c"*v@lR1I"lBWFq0Y!(Ag>91!-%%@_(R%B[]LC-F=s5av.a3=jvs&BQk-;e!k[A.u8kBZl(8@E?uD]Jl/lt!-`JFF<uW)k;7GP>+!6.2_)[qulWboua<:58N)5@37WJ/OH+H#pOQ!oBrf*9<,5nmR2e86.iK~+&_zB}"s#5ViN!aER`vPTbtB`k$^RF495mED-o$~S,*S%~Y?!^_aj@-vi-e
;,Z|cdK]C@FP3:c]:(`(:|Mt-jXK+lFIVtcby}hBo0s6j7&N0pA?ey:>tM%_9z?[C/3S0awqw0^w%5Ikd*;~L
L"$B:CET"dbYs)8Fch[{0Od
EZ`f&)yWU7(yoUS*Of$ke+8"1%LjAs(BAtEj<?_1k;(x$u)Vh3nW+W>i<@,P>QB$#EZt-wVoxWZA)8qE;$&#ff8=SKWx81IrtU_5tzHc):leNI@H!`2<"[ZSxiV5d)Hq>QoeT*mrFM%aM;@8"q*DJdt[SibNYzd.1wx>x`(0w0)fy:o4
)CFGk9ej<RnE&LHPd1*>KMrhNtB`LU2W[$]?s#QtZ/U/n,nZ<7w"1G"otP^8>eZ9H%*db1NG}6T[4#r4fB",wXb
^+P
WFPg|Lm.`-_2%[4*^Zrpaw@6fpdqG0uq`oZEYa+,vRp)-l}noi@d"DlK20Av*nl*T$3?cZnHBwfqhZ,QWA34$B-(|2cg%9_#VTN;b;`U38rXit4;1f<BtTf>dD=gmCzjd-CE
3(2n^Ga+]c2oE[8NSSO{NiP1,VsK>xaytE*a;oL-_@8$111P^]:JKx=_,Q`z>^pL8/RvAAb.[~%myY[9qxHmh@s/_mNL
3O1_^gxF^cuo0DO`bmEXte-<o!(9sSZnV^aVRdD32UaU?(F-U4"RL_;/sH(N5%
*t?P];Tz,D$aiJg0TI_0ydL>yQqhp5Atwq+"IodAF~VbDQC~106d9RXQ1/Y8==$3#,(Oobp:Uf5{/S"QJND101L?n4c$0|I9dV%Bgz;-Me:gqIA,(%(H@Y@4I_-=[l/kK/2$-dhL4;U|dJDQT[S,1Lb#[^;]@Nw#`^eoaK5-@[/zeX9/U_$jM;]"3Gslx?(dtouf.N9[JrZVnYRO*(hRIWfe1[$w,e:Lru&P"7<vd^!_!A7.GYAVV!e<TMd8cU^:>.A,i$PPg3tqR`CY6XQ_w
f[(%brUH@F=(e8bg4Blut_s9Y*@FJ66TN*omM<M%VYP}BqbbhyT;1v9(C2Yer%mFg/?U%k
,q{h4A",S4}*K.M]/Z`ko0dc/MqL^&l!3HpbP).jltOTK&*qnp`/J>>q)EK[1*{Ng4cbfB>;0.pC~p_#fenPOud>$uH6ieU:$Aw6XJoMy@6kp+`P<DXx?="A8C#fvTO7nx%`zRa
C137TUY=Px0mWh{>G++a>Txg7pJQkb`AtKLb4fKOz7Nomr,?CT&x[]*q~VyhcE7FxQzGlmE%##Nh-Z+-QVyR?`B^oNVkNX~dq]yan12e_
w?Z=)CXeiM
,$@57yPV#8<^e8CkgL=w137fwjnnvbj1U=r}2|)qAEGqy
E<MYvg!/MP$bq/&s,,VgW%
CV;/<tgIvVonB@}t)bZX.Qp6[F=%{4.d68@Fv;g@Yy4S&PA?&(;Z`XM#e"J>dH#[_,94@#Z9M7QsA!{2T"mZRi^7sVxp%K.>}Plnunb[p9hOnGR?{YRFFXWQt-qF>Sn@MQ]jO+z3&nu@u(iU(dr<,>Lc.gH<"pj+m[@OkyMBV.u
!
StJ7#Gz_B_(M9=:@VRn<@1trODT@CP?duV?P6^[-&B)E`>=T]Y2w.=,I:1nL4sAwDx.U9G0J2[(c}%P:jXkTtJpG~*8/9ah/rq#/UW5?0cygU$N&(BI&jFs6k*2:fk,$oQ_V.f7[U2=.b,qAx]|6_sMB.DAV>@hTLj?r_0"Dg-eE1$vH*qtum)!.>u`ZBk27#5/2q+I2R&]<OEc8?o{#cVf/Q]
kt8A2W7:?;FTKo!;Db7O<<kl!6h;:hsL6Ag,qM1fjE7gHYD$gGIsyH/(s:f-Y~[mT_Ip1{s6O,s
-5fALipoEqj7ZaTJ3;*0=uxRW++@^jizWW*DyPaJ.#K.!bTDfTKC%U0wGnZ8J?/H%`Z2FP,72XU1DHmwH]w{B*@9$%S1+KvDC}AP8#0E.dL2d
]Y+:h!+4%@,*>o:e%)
HLWmLtAeusf0xyVsg_pLQ*1cFb&uk/"rulR_x?*vlTE!wX%@ls10{W~1MxPy)tY0FP~,7g}N!mC98tR4Ni@-<5p7Vl7C6C`u5*DQ~(@3L.!9t6vV<$`V%n7jfS=4QMtW=[+tnSdVWkh.~aj+m.*,tTy6j0CJF^%nY6Sg+neP;:
$bE=_Ll&J-W@dcI(Mm<ZaJCObbn/d;Ijl}@[4341)TH7+>p{UGfn0]f8D2%P=1Zxlob}BZsT*eKKl6lyxg3qblLNJRX=@WeE4J=Z_*4=
t)g8K=7Tmi:jQ-lq@p=ZAKvfxu&!Lr.DvVy]uKhshXXOv7:d%1<;ByMXbl&Nk[g
?c.);bS7gw<Way(J_*SpSBqYL2=qD7F)V*-[5O7dul4f>Dn]aK0]q^}-Ug2b1H-ntVjmR?UO^6`l@txI=7_%VZ^W,D8,#%$pMN?^qOAQv
nb3DI$jMxy%/.7RQ0(=
D"Lt<K{hnH#Y_IO)uV2
w26_#mlYa[qgf&1J)*UW.rm>k-OnDU07P%8KfXE@RANA,v{EFGF#&#b_P<rs3SgUY0g_C)vFlJ(6%WU--7_a9df)L;0FwF>.5tcHGO(Qwr&9.ZsVQL-_[k{vjhR<G#pKjd<sT8"T$XLQGv]hqf*Gei/UL`JinG
5?AG8e40%#o|c6_~uD1(bcQZVQ*xHa^C0[Ba*krTyfOm`2<{2hqO3JggoIIm[PIT+h;IpC]@?@.%Ewc*pyb,47m19rwmn`"I+$ifM4sBuY>(.hJv^`+N8a0SM*4p$_vtWxyG""';case"he":return'$s`0:6OWB&ikb!v`5XRb;D{QM8@_R9qEYCPFaGf?Ok0fZ;7s=l)50B$et.@LP/m%+8J(VlbKR3M]bBT[yPV!n2&-EL_BM[T.7*3:,!^*05loVdjuUxJ)~9+73I=&7mP,t]M,*UUOV`<yd6qaZH2@`s|);7HOZS%;r_!?sjuVk)#SLVb>cw]:rGx*dd;M!*F[{6w1M-^3$u0l1t`tlyCpVM#c1O"0pp|l;KqK}`}WNQo!Q>Cc!PqB3Aqw6hU581y2wGecR$G/)>A@d1)(Nw-5=NwZW!|piNo,:l6>[KHsL1+(|i6<!duk$9PM<yOP>tc
|dK_pJS1qx:]+;7nNL1Yy(2kg=eBV1;ck>&y4Dnve&S&?VoD-
fT3Z;Wv:t*JDA%H5Z6kZTV1]Hg/7chY
t.B!ZN^(3V9iMo}_[DA+Ij|vnVXW@xPT#
##$:O&r=Vv8mhJw7GrJ%DPA=4x90y2O?i&Rl5&I0YRAR}czNHc(Be.s_.d[2UiD)^s=XxtLaA;PWl@g"&-ko9IF[RF#kY8is)W/mmJe1)56%-`OErkB`h*r4vQ)C1EweFh%?(d
iQ*}^(6]7#)xHRRTKdgoNqEflkZWx)8Ee
&/gTOS<wbkMYwPb!#h"r@#Cgd/19*i]<^!i{akYPd+458=5OHV-!yDb?Lx0Rf9e*
xJRPM7o>FIA
-3,c0&(6i(2No7vPh2!<:Of9Q>Lw!/y@[LC].07+
R%e|[!H[Gnvf.xrrLi:@g1Y$U;)g.s?TMCW*[p<C/dMu+4%Q2U[t8@:ZwABVJ<@qoi.`Y#Mi2Zk]c9jDl
=q@dj,LibpjsgfpGxbK}IO0tdnAzF)-x329nrb();^4k!_!1Fk4EOdSJUM+c-JF>c(JxY-5gI&pyMS7wB&+S@P5$N%yioXE#KIC04X#k1}T]ve,E.NQ=(r%_Hau"bD;/WzK^"W
_5C4Kc-BpD:%_,~$laC;}-*iOxMUbol43o1u6ff]rPE[&+{(%Y
I2Lysk)=*|]R0/Ix^x>]U1vc0xB[U-"f<w*zwdVK@_BC0Kdi=XL=&;BB1-6.=3#;;l/
EUz%jNCmdlx3
wZr4W[;h|7/2g!2*FX"2SJpPSdp:?Tol^)zB,iCLxFfr5j!Ustp6$Y"f
Pb(K=&]EE?=se~xP7bQM[J6Fr8M<#.VAt+a[<(*<7uAkEXx`Ax.cj[?3$!(Te8Tmg+wv0s&.Y]T)Sd+E5jp2porv7/f4>Zp}5`"GRYDtGGm!bnr/uxdzNKWfEQacfIW(.DnS(|tqa-_DXHK<99:0a_`hYv=|r<$3D583R~)hgbXse9rEY8rtAn$I8d4@D&n[VlKI!%*jGWZ~9R@gZw(a2Y6e^7=f0;Q<l6Psgw"awAXcI;[vjFj]W4x=A&m4x}HM9z,/+aQ}c>RxxMsg-&E&14$*h9h"?3Kb*dy$:&X<g%VV*8fXoj5vY}."Uqq9@f;vE|X+6;Jw3=2c7%k!f-<9[=6+pH1c4UPudUV7(Z
w10Fz?!n}@3P9,DDT!`h$&_&XA.0bU8RXuIW23k;Zh|KBCb=]tgBRXW/)SAR<+o@^)|ixQiTX]ilMSSynpQ.wO;f*69,>v-Py9YKvhz9`;X8/RIw(VG?3pZNb!|8d$p+1ul)yll4Z?h:35k.$Q|@F(w]oEC3kIi]Xh~kE=Tb<il,F
?aij<*4T:2m)1A+BBc0*S=U.Z?3q13-sXI(Y,7XbSZ[VV:s@w=,VQ#NUQI>x7;9%)"=K*N{vg02!gW9HZ+9]fb1np./[|p
K-@7tlU^W~
j8<>&_
S*7[99X.`Pc?+c:_3IpDT5vNk4d"Cb>Ub)PBQl?<yw6V9ajLhq`{$:^3DD^1:W.q1W%vS6d~W#6M@xc1B!H4Jf/ek&J/g@hh;6_X+8Y4q3L:=
YHpRWTA6GPI=LaqO262>w$x2R-iP0|L/%E4]?2@!4jTv0hv80tr`)l.rmnX1J"=G=n-|*Q5LlzU`l(q+ga^OJrfSQ9v{Azq`!(.bdtqv#;6U0c"F:}f4"ATi,WyI4nd&?8^{u^[jX|kP6q`Hw_8~jc1?:"/uI]*lUo[.4c=eZ$g81W(w+k=mn1WzC1s[0;)brmsch:Ew.KiW77U1b2ok,`x>tGId>"d061tfoftJ9i[86_Ln[RCb6QULk[Wd3|YV0~dFi$0D?DfWX81~[7kp66,R1jDLpL$>
l^3P6N.e#U*0]XaMGD&:X"l^Sb7X=_ap)h1?uV}I4ZzIM_RyNBUtAqk?.ZTZ<iwn3``uc2Fo.UIW~e569.iTtDeQG_+%xZc0b)f;;w{p&05U@VbF6mI80DzR^1
/?N4&1*w_b=N"knBJt
3bx={<z_&*-m+5M=0,zhD4gan9w%#lr?(+Axx+LOvq|pI$Z?,DiJN&R+th5X2tiYIU?^<=ulq<Zb6)7[Gfa6uQi::4M&@2F<^UDRM_r:#dseK8KJT;tmeLLYPQJH8LTubx5fCMql|WPq<u05PFc@$YEL
';case"ar":return'%c0;;5Lp=)R?lU#O-d(84G,01IZKqig@k#`CAe*[J,=iX2f$VqCwhW9GGdEvg)dbSbduAE3
jgN"}BCS?CogjVp7Fs.!f&IBhoT=p?2`6Gs09aH;)W..Zy*tm&EnmH8mjqe+sT0$8KVfJ;WXRhmvu3Cdoj`kV_d-s5,Gq2oRPFFf:ps1JF4MRvT<K5f0[YZ+zETTbPj#__{1,]I$<Oe=5q^^)70ha[lR8Rh5tHs=;LfvuAQgr>OH<hPggh!0}sAqZl%:]1D)NX<Y>#^If.CvwqDwC0*/.;&Cj`e4fYYA:m3o-9<Py,Zf9g=+Y4e8evl:p0y*f5K,+wIUdI1m4cxEU^feGNuw7M>))^X=bNd@@#yg%)#hf.hl2BI^D;s,kMLev&5tkIwD-lH/d&cHu`MXAqf4qm+20L^-c
tU``vR$@5.aM($~<)":^,=v5u>$hu*E)SvO=}]s7#WXvxaqsWTq=hgP"NGmT=7=dn4XVXA68Q_)fV5SBz1l]R1{/o3FD8[;K?>,EuZ$LEl1x+TS*e=tslvKy<S|Q+mRn+eir|S;G#UzBakf;`SJ=4xa@(v.*srGbI.j
7EL;prw1NNoyPgfF+Nfw|wPt<>,P[ywx)$f,~%Dq]bSG5Sqt2nm0NKv^W4V5l85,|E/TQF0,1mg/g
0n=3(A~u;2^_Q=~lzN1xeM{5p`*29wCt0l(u4D!P/6c-|X8S}:pOxyeikEYgG_jrn*_6nW?RV93kn:ut(miM}fAORh!R9eI$y&qD`(ZR)@ON7rU&&sV%i4N_V%rMYHo8t(N[OU!aN3=?Kj}/+oBq5""xm-f!
;EMVHVm]3+Red3O<C]QWi8I*4f#r_a!`Pr@lLDg_N,*Z%tbZO}wNi&I836N:?DeKV3:I^b$UILONqZfz,*R"(ck{@^J8GfeQSWy[K7wl6:viwzo;X@Pkll4W#
wEKzt}1}Yw?QN3.eZ7
ceSM+?+_ibagamE-Hq}Ym9BGrn*<o#T#y$lx3Kw&f@^kc;PES8m/IHN/<,T7XZ-Vx`C(gX(7$V3Ov&4k<xV4~GLn8Jd:O)GAFbxmlC8"p/?yd.,V9w.d{B&;T8w9F
}Rco<g4jw%A_x*]IA+<*hKYT&eQ9h]w*}fZ=,kW9qojO4X;TWgxlne,MZsUh.EacM%q[]NfHU;;2[fK:zxG:v>X(J:,WD$2W1!H;w!,olJeu
J|00E6N12*"(o9@PPE1&bt-nr`&BfS:1_"T_[-/~-KrSsp"f_3QDj3@9NL.j>(T
ay-JR+){@?mU*#2QtEiINhCs>aJ-P,wy"?U*k&&iQb3D^f_hitj.x.yg+q;M9}o=eY8#-`o:tnN|dApLRA"wT<,@]SFSWk+E6=x{yfS5Z22b1B-.RV7pOT7;!Ftjx*c321MGIUsHp1(o?Kc*^vnWHe5`VKM$5n=;$n^5CgICnU%ML5epD1
X)o?g&oEc)RX<-QWB3n!w/6bakYUzqp?*!Oz)!;lPU&`-uK4/,BGS7mMekfBDP]Uv,_dzu:j((yO_v|<sf&SbgGZLyKJwa<,:/YRQvdBG)M6I90n
;7e{(UZ^2{<p&7^n(7+6+a%{?XeW#|Z9kG0GA+#RKb&!&5MtX1L::EZSHN=>9c<h[Hcy158$NM<?FMa@9XYK.*P?+04P_1#}Py!XPmw()#""^6fKR`@&!6h,x8(}3)$3)BuwG=_yC{,^[}a?I["~+HXc>g:`npgY6PZJUWhIw!l7y|ortAdt,OF"m$iD_8qm,inMr=;y-;D^8)sGX&0F9:P3a~q(H4/tG2GtgMu!D,vY$5m-G6(KS*c9*nU7N:-1muA2[[=x[C/CC::|d^1~tcaUu?hZ[uM]sq3<)GI8ZORz+iV32H9k3La&=GYG#]3It.Jv:q:&iiy4CB
yI|MfqA"*l4mupay_O3Grvf/$<9lxm|OYh2b#kAYrUg)|^sHTD5(u<y3HhEFTOK9`Aq2YtC<;NvGR;-_Wx!-*ZPFX`Fd9,H`ir7IYIrWZB:p9>JE,_CxuA);E]H`OXR(-!gBlu@;bR<."H5LPX/#*"VAgv?JrPRd!im+70[E?<G^k,s
B)d"*`Mx5So1v1iQLeo0wj
ej+@-x*g079~
>^ah7A<9gi`2te"<">Poy"Ijf8k:aGZ3$4No~rZj74r7;<DQ2xmMTjp?-1gJa9NujJlo)sU@a`77!cu6xrI[+B@9/1aO[U3On?k/`<T9<``Z!=8Zu5(V9=)EZk%sus[<of^;fA}GFY<>m(9d/*z7%]4fNB8L9I^@ufKYutG.+T%Zb6cGuPnK?hx/bmy?
mqJn#}pghf4W!`!J`OR|hkG:,Tqfxr47FV.r3E&YwpKp[":&e:7?TJea_r7T-lbS*K[Rf&Qt_[f)b!x*R|xUOQ5(7]37Tv<)Hiu,4ztGVp2/GclOg1@1kYw>8-yL"GC"-.Am[=S[0dU=%~kU+@0d&_X"tBK*5o)?G%rCl5F#v0IHDi,1]MUR[-XU:7%vtH(EYg.1g?5&Es0bF9V,"g:WwU_%r>/=q[hHuLOuQ$cXl[d%Bqsw,B/-hixZrnR)`sbW`va&4Ot.Z]xYNOkQ3@TUZ=jt9l(A2P`H8Ogcf
rY7CChKS0?nTcyf;^-hX$yie
n?g%LZbER=Cl{EE_YhST/:$f?7cS9&b*0T0LyU&bhtnUVr9sJ2<83w@%^X:GCICx+n}4cb&sllfMm1k]]XTKQrBHIGwFd?hkKh,"6dl3Sg0=!?IU)y
<UXL31.eVyl)[jrC7lkdgWW`#l4cJs@esY?#)*mWb^?1[{ZV@c0Uq+h646kww4H2tB<IrVjZSkgH0QrT
|5?M_W[R<b?Uzp$u_>C)ggg7&"3;6A=u]bsZOw0d,Tss]).R``GiAA~tm#GK5NmHcQ*6HxEW2Lb^Te5-puwgsP_ZPRE04a?4^JukrSlH=LwI|]rnn)iaghBlH+==q.oYd[6WvakqC/T0G7#H=^mt"0-g~GhAtZ1n{lW,Vm5V]Icyl_>.1RVjYZ@K6UY7tX!75Y[[B&/xY$3/3Z4r3wyG#olj=>0ZOSD]Khvi.H
/=w:,]N|<TBOCM`A*{.@VyW~HF$gfisH*D)?RId)bxxCs}2pDtJ?0M.(d6Fk/(HBY{wW-
%cchDbl3YRLY?|L4Fi3eK3Zq(:LWw{i6@(O(cthG,:$}s9G5v~H;Bp
Ni1T{F50=al[R=U-YO2fEEbAF+{ShiE)Dd^t5.U@TjhtO&pl;vuD%mwtP2m"c_vnuQJZm4FJTW=B.B1QZ##o}06Z-$C&8>&%zYMdv$/]Y#vV*wQ7Swk]ONz9!q2-5Ti[AaT
eoTro+V
_i?`_knj!L80TQ~ec4YtCXXC.#"w@e3';case"fa":return'!s`09bSZ+&iq4"GV5;C8IgWke.~NGSGw9&n5+C^&;T0UGR^k"iQ4hb+xYtC2:PO-U8V#E1
_D3t]0Bl^;W//`L<p5n[H7yGgms%lhv{H7>!u.94^Amt4Vb9pHBtx{2%vza[iHMJUVLku&_.wD)e9#uu+P>B6OoT@Da51M&:bol#0ZvDo)!@`McLm`gMiDKZ4n=+F}c$KVMQ@j+_qEp/@km:@x>No^g><hR[+RP9lN

UUxbo?Mz!r4%mol/fS`KP_[kGtL.xbLguyMfT6XKux7UV&rh7=M>%gl7**Xdy
ZwS`dkbA2_ZJU.fyCn7m#eVYw(P!7|gaS2aCd^k!+k+I0*Cw8IU"HwdZ:!WHR.a$ex3)@>]zIlBH@!od*UWUo<Dc0QOU^IWm%1=
8u60_!r/STGq[[".gS
"NRY
cQvs0#eEj?NO8_*A!TU~l"%eD4M[Z|f&jkZpumh-;sQXDx2,%Uk4dG1-&OJDa0xp^J9=[ng-@&!NA=@(g%[oWj=rsj:Yvrm.Rw)XxejrHBT6@=*sTS9pcL9YmkA{F$Y.y/_h+`
HN>K6V8cvh5ZeYB7n8x?/AO;3wh=M`@>^yPqO,%W=-}lVNFY:MHsGA|jzfg;!KQo$NRw_]->rvfp>CKtZXIXb1K;cLxav##/?xc.;A
YO&[th*Vh$%>OrI}o8+JjEkNJffM4_:Ug#T9pv3M*bfZ^Q#E>Ngu[VYDm9(NwyXZ3wnsdDdfz(5-qF2-06gwdP7hB+VaSv!bhh?6p{,Yh&
A/AxVjoJ]<Pe*US)5&-1|=?FHBGv+c<E7cO1:oC7zY)4R`mU>-j+6JjX9bjQ^_*F5gQt@nr%Oui<7YrxU.oxrrzN,JVGnTy-Zl}g1MB-7KdIx
Kx(`a#^pr<wL)8I:"WQY(vpP!d)+$5aH&Byw&[P%kLC"u$p%oAm0L:j[bD:/w5Ded+A>42""kpp#L>YbzZvL&#J/y1*[4u^szS3x.n:S:!mXLuPn7AY7~nkBhF9#]?C&vIwp6afaV]tc"gomJSZo0_aH8502Lm"%X6Ou<=H3m8pN.5))+10mr7adIFz0aRJFI(4x]2jRGN|gxC_9kctXq**Y-7rJ)K*i5g]Pc=6DQ_MyT[3gXrl<CXiuNle)S6DY*!KUF.-tK=
*~lQ/WZ9tRHOka#_cADT$Ic+
7$-N?"RM6KG5c<sL
Qas(O7NnNn&
^:YXC"Z?
8"kws2J0Zu*J>h8d-8a`my!E.MVyP1eMgxHU)pcHrNod2M(1.s,_~o@)pT*q/;`U9uQ_Q391~m!g}UtR1T
bYj(r^R!$2]=LK7>dmgl3@;vm0X_W/m7U("SWUo;4njW-*^;Cg./&_9|N5sP+q_]mZ-^a.gjO=[mKH(e_Ub/5DM"bld=7bvh]9Nq9Cjg=b.xJjXOn-D,)=2Hd`JF
7b5g}9KU""{[RVO"1KrW>Cv03D,)D#d82(J^V]DaC
LeBxB"K8Ft?oD./@zCEPu@=[8/n.jY)bk*8kEX7B%)aY2XyV8d?%d!72
0}YLO)I?ge@5>^8&
{DU=7a`wqI<aE<[<I#c(Bv(J~!"-s)]0yDfls"c2aXC$T-vrFy^KJ
Ek8^d*SFBe#+SZyBWAtu1rC/o1|o4S
q{Kunmd"tO;Vui*X?z##T|(:8Y,,lqey[4W:eGmV2w-lQjrw#7SW:c$NH7K=7^xX,ri2!gha-5?7:l)Ip*M];=q(WfWOAmILY`79+5I:#i^9/wN)Hs7Nvg2e6j3AcTkzyGJdx"
qJL<SpM/-(S0v0cW=YICD<iu(5qM$2n>+v6Jvjyf<Llwc_=>0!]w:s%I;<hA|mz`MgtI$jpo
]51N
GbU(U`vQWf[Ok<1*fnd/R]]S$,:X)6!vcR>)h?^DP]K<-%8fTD$[q$6ruDr[w]h;fTclS^gkDq1s9ica:a[&AI>r[x.B3wq[KqM8SUj?
#lI
[?y(Rv=*L[4f6;uFUgf3
CId90Ar21$`sKPROgws^zMUrmrTGu<Q=UZg
53p-M+vI,Asl3TdhTEYuzZ@r:9t.#<"hN/8?shV3`*d2DR;]Ge[A"+!(F1L$L]{#V)2tf/mp.am"zC=FgN+3Xl&W86$Bg:vD&n-Dr02?l2PDQaj,dO^oy2+j~?y@-Pjo@L8ZsUQ0S(|6L1kToJ+ev-bAOZ^cP_pZ6Xo3arpj7yGb^X?n!b8J%"UQI(Q:!_]A5N2%Qm<<%V7Is6Y9;/D//oaRksyZwx7pZ.P_F>1Vd"pNr&Td`Bm=oo2i~`RvFG$CZ4PP0WZ(`F-76Daszd
Xp!NfM":cN]1;e*t
-:)6QEyg/PbV]U$W&cy>,1PIaL1Fb+z%Vjul:,l@q2:sI5RF_U#`H
~]3C2(z/tF}iE
GR^2/-[A:Q9
p!};4+hwCx-Y)g9F^`M@W-f/a<?50]Q"Y./W(9~==E=Yw.[
,i[=IP?[yy:tlwE2Dj/=XZBg3vA%}^;T~[CrH,"?R>,.S1M*g%yMY<l4FssYk(>jE!K
w@V?%P.vP)OsQG1CTV"x6a;He$0Y=8Wy:!%gh(4=!p#QON{5f1S3c&{P/9HnJZ
!]`e+m8U"/mh76WCKvg1
!9w,.-Dh0xJM?mcmdF6Y-FA9a=O+}';case"hi":return'(c0F;aLZ[/$JmU!Bx=&I]U6c^&1l_-@tX.nJ"(:iZ+)j=RX)GlegY!gf~8!T=*>8{
DH%Uy9SOgQn;Rm^D=JBg"T*7HvjU[^mD~.x1iW|_R)s?eE5CoKwbG=uQ&2I,kVM+
BC<.g*AijP;s?rI"4J
8rk#$gl(t_T+$),yBmt_w54&Uey1Mf@xuAy[*mUaT-O7[5]ks-O;dMjQi3gp(V0H^if5=_d-U_2:u6PBdw7Be+VU|mE_Y;/q@XUc)`Q8zXPu7dA?pmQ$2T[)In#qFQmnQ^tp9fG[`$@RxdE$N&uV$:G=[.QX95[H
:cl{$=&SIQw,sJexqQ
-iEF-8j!)!|3-d/lU_=q*n`IObxTI0N@F:uh2blQ3`-g7;WcGY$7m591{S|R$IqenEr?*pa4Oqw
6$k4GF9mR3K#Fp_V$40
WIlKu=PJ?u%j?fT9[yZG6;
`:UR5}"&CnFHQ1vPHkOX[lH:Bm(<nWvmeC`(=3!;%iY[H"=`PF?YDobvVdS-U.mfyJ3)F.PG*Sa>9Nx)]D]3J6"Oq8?K52P(IKdU,uAi5Ids4/d}4WgpCXS}eVn7ZNj]la^|q8q1Vqt]NbjD!9nyZ(oEHNQ_f
am+w("/>)+9dAz&c8B,]*gAs-a<sZni028
hMgy-aQJQCWn$)2a]D`[-t(-h";nK-bmky];Jc0anbB6~ZHv(Ax4BC
J~g,$.O
t?x,q%_b)tG5`D:M$8SO8VcybZZ=KcZNdv&o7>/pWj&&CjT<:m0Dfz@^&#?xt:@`utulw,X5[e#3rSu_X62{.y4<4|N;dh0g-ipe@GvAE7F+O1&U9<g5n1bjGvc7oAL;j"?~Fg)s$>Ip/VuES(9j+]eOp0J4_
QJf^/GJeEHx;1Q(eY6M>N4T+l;GwIZbD:-bfwzrOojr1@g.]O>.>2o8R4<&2)ga(3};2y.*e4b&KY(raf`L4S`2?[z&w$9f5d<x7r06;lq4iTo#8J`fVZ,b%`@(bF.5bKANC*0WD_2LP/1?`J~<n8T4Al;NQK!5fV"o($tV5Eb%I7cHu1=9_L>*Kv^e)=5,[mY-n%*IbOisF(2l8(~=QSZEIbKH#]yJ6CL!T2iY"N(Eu>cf"=!7<e2wOs
2d.R,dwj<0@:s^xOLh;F,LvGd4VS,~lJ=L&Fl;[7W3_ksKWjgLSe&2pi-g6BmFD|G7Wv;OG2K~rx!f4bvi;"pHTA&z14#zE2
z95>Y9Y7m8YOHJd]"yJ:z(gI;jcKo>%T/W;xm_.
8_een!A3$VS(dTM&/VU,zm>OdH@fK:<&
kKO{:f&g-C?-3B0/WU0A,Op:/v[)
6_TC:K)LpR5n8J%pRV,X>1soF;o(U7*sz_J]%a<v-tD2@2=5sD5S22#e=oNQJ,m-@Ap0egXCOdkAls=_aS8B
%%Is.8-QCu^AM/P:=O0L:9M83[lN%wyZtj*nFp:f/yc,`MMP[yhLKH<orl+D6|1^f(W=[/DD;[d@Fz[rI]f{HFg?sJe@q1DfcL(r."t>?S=r-fQ53xP(OF/J#g+3Y^f!(#
jQV@FAQT:o491jZMEN{HI;9FXHY,IgT&DE3i<4zGquAH:-4CErz?Xd=;SQ:>X@m3h_~IWu^GSj(309[9:u+[P<JlcoEs.J4rOyXu=f
IP!zdAptG4TkxgeDc]:5W?OCAl;z*hVIjqoEfAAyU10/*,f`<<BV;-#Bm
S-x[&n%q>AEW?lyHR;Q|?&[yua(r]g,e_CpE/C8k2K06ywre_p<#w@w>[Xz"(!MrDnH"o&S=c#yO
t-apo3FPQ6Ge^pQ"-FFvm?Ipi@Sy?9ku,"XJ]!YF+q]AC<QfDZ"k@e1@r][:8x^X
yUDVezV"038OR4L&]a?3aDT!mw.Q)&$-mkBG=tZ>TLv`:XD1s:6*@!7A:"2-vq>.w_
<NOCYXV1D`Xk&`Q)}j-3VG+KQow`{t~Y4Lls!hv_f(9hv.d#p)3Gd32s@T+jrS}d>^F_*Yo02!+Gb-omfXJf
5rP=GiUyR8azl:wg3y/>>Pi3A!o)$Z_-"D4:J&lVlZW;;1XBGyUKxH2loj"jEm5OW!MA_s,is>m3&#h
p>HH5L;}Qhx^f)dODGQ:$[@5;pjEFoEv-91@yD).8n5egweH%J?4<>2RUGf{P*
7
#rm5*I12Vfi.;3W+E9Y<`Z1$4,9v1#u:Imy(LYKnDM3^jFCvS
.OMpnH`Y8Db(9dCG$*zxEc,%!,c<hb~Xz8L-zghV~8GwKMZy{m}jkB+Efw3O!`V:+?k)z5IY^<,OTkc`$=/?UhuFRpUtvj/>{q/-#=C#>u]"0RWNUqc?u7)&
6jLtgw$}[U@W1ry!5G627%lQ3:poI~wucNI~Cg"4Esu/v}CpL"[-@A"[runeOYS(Vm#TLX?d_n]}!O6]iW6DiLG&dCtk8F9g0^
xC
v0i?8kX)m;2eZ(T^oL$hi<pPA,N3dKcPJm^yGY@[=7Aax]wt$?iH>%"Si
"NO}@jRFGA]Tfq;M).AP0XG@N7U|p8V$^t#%BRr*7>#vq63up$[{`5-7W-8GA,ugegWh2W<8x,3&)a`;D;5-OqWQOFwh.!q=D1R8_{k]&W`H,9[CCaSv3(9p$/=U8JG[F"/o3x
"]$4CX$g>*F
x0@K^(zwLm;hDk5EKC^K8Po)TR+bX.IdOf9F:EwgCTLB;b^!Fit$t2~,
Sd!@ulo^b,*k5kyew`8?Gn8;hAwgiiJ=8NtswRC!KhJwV"Ygejl2PB=WGn_T%c:0W#_[sk*#4ZujTOs<d$6|!uWls$<!mnPR^R:qUIZ6h`OvhQ:_a~t)RZ`Qm#sTbBv;Wz[5P^yOJ|M?OTb8Zi&<uW-n-pGNjY/_/,%26U`YwHe`*>Wr7c4zWuKce]7;U]:/R#xo!>3+_cK^%^3QJKg%KdU&j[?~wcX-^ti@#s(-+*phL)3K#0,s4#/<75flTm4?1nZl4Ij5b1$V&SIxn|Gn#Ml%wJ`@OD/MUp0{Wkk}TAVPpCyU<J()X=iaUbOi=FG+VhRmEAEd+;y/]|]XFgW<a+p4nwG#5AC:VbK!+v
&nz`5nJtw`1qoDbgYj8j3i{^.sjndJC5he8##4|MV5LkhUulw_VW1_jKN)J
pmO6$b!s8vg+LeXg2K^gKF=f/*3PO_~7.BVS@YI!/AQEu;_>3O[0hVz4)+]L*7@Y,Y0haVN<{kp%_ZEn5g]3/9VGnlw^($@qdOEqrQ9m-6Hl-NvQ}7i-TxG
z0rFU:f"V$:1;k/x?&.jqN*Jct/GSE[c(w8^f&[rmx^L``9a$)CC[X/s9%S_W;[J#KF4#-=K](,&Cqzj@f<vRlA0U
!fYOqrLN56H]>Bhj0qB:T[}ikk?3#oj2?7|P3fwfV<e^so/9Hoo%Zu>!6MwRbObcy@(f9CH4:$T&w$vGiSBL:3zNpp5p)`PHL+s$.bV3cwbIw^[Fr!T55Z_ae:&e"l$qbBsbtIPuJ+0LWUJMYbPuqD_5gn|gW#|LZI$".QZq@gn4m@gl~rv%_AgdFm|xrltr(*";W3/]mY+F3-AK)"5TK[J,l6;ov;E"?`$P=9M_bHax3je.us
l`gI<mx@^3Q5m3f1l)d9cxv]Ij047zXec-EjjE(5mBmZ5z!;q:S=[OnK
;Rzt0$_Y09H;yt`sOn*I&OiX}]K]11C1Vt-:ubt.Eu7$$$UUPaE`KydX[=ql>h0PN/cn|6E
?I
kGp~_Ky{Hi-dO+]5rDPqyI
*Iv[d?O!$/z[wCU$UguD:?$%H^zb;6E!L-d&N?qAC_0?,^zJ$<?:oI.4%WOx-Ez_#2:;lJ=[Vd=`s[N?>^NIM.tv6I1cFa&-"P/:MoB(i^8Iq2*IQ"?o;!>vZrv]KJ$i=,@';case"bn":return')s`F;aPWR4U=&v*l@wq5_E$8:fsyWZz+83%Vb^
:;6lSxh7URC~Gf#Jf>d1Z_MT8Es-ZTH&B+?KhLyCBGC{0n7XZom^n,c?tUFSy1n],g=><Zw|p=4!woavHbERZ??Ssrre;2B]^)7Xk#SGhN=Fmh[rrSG~xdpBqJT6mGF2+L&X5Rl#a4:]n{8%JlA
!:lv4N#PdZ)km~Z1yucUM~w/0p
?K,PcEK0IJS?!Kv`~$bc~_UW=x>,PX?(]6ce0ZMb;OEW04sW.`Pc#AyY[Aa3-,,KO2
R,3-Q=DD!OtTN}UOboYZ>7"I;ewKKtS7gR,I5#6;B;.a4Xq/Yzn^>F[T(HoGb8"lnw8%N)1/]y18[JD3iN?sp3eibZiz^(`Lf-BmRX$:@M7c6:/L6d+v]y7ZN}@@/1S}(RN678qw6<4tX|`YXx#bcNr{
k
ss9#[U=4wUK0wm(Kpoi:

1PRUI_gV@62RG3%UojwVi)V$IZoCu5lRV4TauPnE@6abo(woQCBYZ*Ec)x^>L!`?Aqp1^:sX^nfryB46Q2&fDT+PYp6DQ_TwB=89`JDi7J:+aY2HH-W;{_S=Xs3^"I!k|h9lY`Xe1mc=-!9H/UgQ;iiJxo)XD;MANonEa3
_)Ro2"@|,t4fQ5FSlby[-z85y%#[.~1;=KOQu<g
DLnIZ*o42V-?NZ6Qf3,tLAv~g_)_V2nx=_1_36u!:_0YgS.ZRe#:"9.&l1:tYo.XlHJunP;x`@BFG8D-FvdWQ+wnk7kvdm/HCOg50WbcGh13.|d4eX+n)tPZ*P?vJCU90"#R5{H|aIM|YFVOwEc/79CBL@Rykb3[WPavR1Nk*pCvs=)*_w]]^Co,`@u%7.&enT.Onj)R8ZV;+?V)%_okg~XS%],PXB0x4lM5Wk58c76ktR/P!PHu]fM[[oZ:`5-Ia+W&?~W,GUk?Q4T[:yk}p93nC_lAwJt#3OE,xDH#yqd{=U]5lwe4JZ)hgw]rX6rWKINib85L+c";WLJ%I<Zs3/.MM07ur/R^IekPf!kO2kDv
WLDfS*j!/9`E/C?HJoLfEkW4i>$21$pHjw#>4-y-Mi<2KXqbs>["se
4d1gl^&}!pw&=|Olafx#yF$]_$E%g8b2!Ny
M^<|6VN,+=<Qbp5$N
MeY9[+]8vj_kldgZ/U9_+UBQ&Y(gi1-plM`7G__!3$VO^H$1]8gxXZ)7:4ONtcK#?dYK_}J?co&Cb<c+Kr19bnemRomdyv^E`S7B<tLvhtBJ<._EI-GbB[&Z(|O8o-@i6N6Tpp8Cln
@eZlO`pk<`XwTo01_9Z`CC|LXvISqn
&5ysd@4-pKyMe{6;X85Akyh;P|RB*qiapQCZBUX#f8r{F"r%JgVjCHaequA=%fS):imD0V#n=q()4P1CH<S<^X@D)e)dXc1;RueLNTe"iLw|&h864UJb&!!CN7hy7D.n:v$kXQCGZ_+y/*"=F?sv4JOr>H[y-:1AX/a-C
)y2Sd5&&Gh6R@)+[JnAQP0MrGk^Qv$Iv9,"U
"JWPnpr!GO,]W[snPNvHx
k.ygYhmohvUJsp&X)MaKX$qpD9Vb^<lm1hmV*U<y?Jy_4@78V[:B[3[ZoVIZwAS))SWQ@HDB7=]M.e&%s2-]FfNS`dQxr0dV.y_j3fSP%g"Egy?=/*+sY@gyf3lc/k8xOo-7Pkn]8(5v<Eh
IQ-sVn$%>E,+~dXB6_~3R;)U}Ur^(Iw"UUe7BY~6wZ+w1myTQAm5k;C(<C*Yg?^f[6:(q/eOe`DOd2pxy/]6[c#NHe2W(Q4eWUA%O4$B
J`>B_jODR8/ZXi5pc&S]EIpB-LW<85
?#fiOO<"okci<%O0Ci<gu,[!Z.+#<8?Gk-$Z0KFnH^g@]cd8Av"S=/:MU$?uo?WYRjM5>,g/:8qT+!-&<+#?Z>_Q;x<Czmt^}Q&VBfq^Du"S/b)^?+sdmR4-1@qMOnJ,6(c13xH-HlWk%r
ApiVm.Ua$}Qe.XW4,/_rKnVymu$d]7spGx(9]S.d(h`=R_"|cqLC8?P(M_kqfQfzHi@j>_x9*fg?540DVrBkc>-RCf7Z@;!W,pn2HkUN$la1igZjIJ%;YUhIT@"OXQkYq:H/-NrN[hWR?jZ@ow72W-?7C+^E,qLzt%H!.I*uBI9QT[7Q/19xQ.,s@qGSQ>F=c2v#$^i*g$k$ZMrU9J=Dw.,D4[!#vFfo_`WSo>sj_w@5t#*PI;t4?-@+SgpW4=oJ:zm6U0DL9uwR0sXKJi%j!f6?&&7ru"=O[i3w&54oOK%`4|Q=YGiqx]0QY1smCVSP.ikf+f,|CGAdp5t.w{$h4QoJyC#L#m%~_[^^!$<Jnx2NEHGayVvaQ"2~IL@)7=u0spj*C-s+2hLyt@Qd%)5^"&5o_JOdrB;>*&`"m>i"%.&@fw*mRW&Kb
tU/tiIDH[h&Gmy+4vv[f_#45Y=2!0l3(I3qRRRADD_,f[7am2<^Y5j3&:m"tPO!ct4.2q)V]0Ad7NS%H[Hg<E90lcnMw$~?tcWQ8v*Yo)(5-o_oXTX57VBCHDCsf_BRL>g.GKzaFZC
dlbfs_+LJ<V:]>
?y:,,.sejpC5@Q!Mha^:Et#ct*+rO]C^Y:n3-F)[Z(XeBj.j;idGS#k>W
o&/!wlEY%zmGn8E9U1_ihL
pl2a*n^!W+BwqCg06ew=aO]$d`u[HP?gd.i,mk+DoN`Y9O|>3NRwt4/"(`oNIT|mnJR]-Mk;fj/,igU]JhAes!wr"p"/Qf#j(!h3F0{TPgE3G9/-:L(!-Avn./b1j#=xHu<N/BaQQZTv1fDAMMmbO":<ddDVMNkDmspJGIG%hMu0</Rh#y@l+X;@C9TmNOV]bArQL,<=!V+rg5,R|,lCI>?>@a}Bp1GpWxfC|2:<~Rju7pBA{e::&gUu6E>9@7Usm:shTt.WPu238iw*(>5C{Rj!6$tR1[:
!lLRo$><V9?[`L,I80"YP_)s&@2[VoR>{`F!0&A0#^~&yL*Pf]7K/K[qoHyoZFWN3@Q_g%/
U8@g2LmZT7PeHJ+%Pa32a
FV|/MC,YMqzFH01]zRUU2W>?@f>Vt-TTlhMMff|-+M(L6Hx8nG7i(1,aKDYr#@V@@<t>.O;a#_K(t+|.77S0~I7QY?8sOXR=jW],A=j>Y4oZ:tv(*Fc
OQd%H[6qmPlD?P|`,$5YP6-!B4ZGNg/
RHy
yH^VuGK#`Vbx3+&BN+uuA*}KF,$<qms,P]`t~2G8;FdB:]d,P<6`dy$dzqhf<jlimp"`7%f2.3?UZ`=rHwl,fj;n#@sS)*GL9tAe]"Jf&>DWE*Jv^AO[i3ubPrxq2xZ>#T:K>cI7qI|M^oA#n#m+1%sg6J0yiipY7SVSh6Ft@!4uftJ.IGL`A6h-Pv5r+xQuY_?FfVqt)8b](.a_t4X<DhMB0RV`?pQ4Gg)X}$+Y<ncJ>XLGt4V$?]UwsA=ek`S!}+-r:*h`{T55e?0m1:&`:rplX+[9fQdCoRG-+V|/.#Mq;6qitfm,)&3gkK}5JduLjsfWv47b-7gZ-2#gb;}tg+LN%_*YwKw9xEsG]nK^&M_JJlP,|Bukm
SmuU^PlI0MA2P<H[x)k`@1Rk@D.Uo"jGIft_lfZnb[{^pn4kI,)ZvOn?F5<gz&<bTWm1"AYr:Vjk5mdr}t.qFK7kr>P,WG_P<tT""';case"ta":return'$s`KraLWR#A`otbA`B6&qccjPS3Qs>Ny!=E##dQ(91sIx8;9!,s=`vf7NQ9gSxR<.UC_wT]RNqOSX^#muO=$|cF>D@aPdMiMJ^-7xtNAYqBe!v7]BB0CdM^^-`_J3OBx:B9M3ywy>o!d]R]Motfm[k#yNc+Fah;IyfIaHnTMd54y|,ycu[0I.S79I_2Yzn$Hgd,?kj8`t5cx^/<@THy_XaCvwueb@vO]*#l9w51yR8bvff3P[z!N$s*G
M}cQ4XJM@
MtL3I=wh+XpUHQ7lV?7
I?P$rE98w0o)-l_hW"Al"?=uG<&9ABMiqQkk(j2a,~If3_^i<3GJvkg1#@n%!0[+C/4#H}WJ;qbC<&hw-}w18|XD*LE
/.:Gii8/C*CPr#tT+{I^75#1eD!<q6y4yr7*QV;Utyo<wD0yh9Sf*7=G8PAoT9t?Mi9x9Sx@O9?juE9DFprZ9GEwSraFK7c*t!]aq~yu@RGLZ0X7XV3eqNL`TvrClSwXR7Jfr#f$J6Vk3>hWTYSUlo-Lwy*dR$>0,?Z"IkGU5"cMF`Ozs|SBZQLF@4>W3J]<uC<i<@gWwquJ>}4C&a!D>y-y1]NW9D8%l1S*P=
uE*n|i-U7_NU~2F00Y,J"4l11Yfddv1GeKz;)$,b<V~G)05@WG,#V:gX|Q3rWNcfU@S2n*Aj}?j1kLF`~q%v_kfw5Uxe,K_vu;RIYX]bh34&o^:Fjm$D-lVyOt68X08pSyxge(BjSg$mA`g,0QYh2rLskJjo<y!r~IZtB&]<F<&-`oK?89~r9W"C;N?bRdeNKWz!R
3WO"A7__Wu:wdYMW74siI,npV,gpg>Ll<Uh2)RGr.%sUX!^:Q.E4)4P3=*N!gsw2M`0(Bu-75GMk&M)>dg`CSL&bz&}E8*Zqr,|naY5l}DY
y7/vW;{e%f-RP4(S7+#;jU{S,_NUfl6F"`/[yHL481{aI>w@J8XkDrB,g+%#5P0mi.,UzZPGl:gg2rLINrb-3>+)c@Dv^3(tZr#"zD,-+%"etgt2TUywv].N:]B$Y[7pn_2%XAcP_qjYrI1Ep=p9lW"Hj9]:ngV%O@j8<>d5iwlL{W-NC4gmJr/6=yWjesE5{lJ%^%Y:qWpZJ=7vA8Uao:@/o^^!f&MZrdtC;5mCY1M.yw^bDeqMBEP8,H>F<*zQnjxv^gk:J8%BQKig;
<Z4ME#wP)0ktvCs#((4qg)2%6RJ<nlW@f3.3TX?Ju&+v%Nhab=RJ./E_?g-!mM@r)qB5~C=0N-M`Fak!$Vk"g)TFzP{^l:2U<vZ&ZI{vIZ7[RQO:SPFv1"Y7H?s%1NCoz%*I7F1vbB<XJ;DIZD>V9G=u-v#jRGArZuW%Ax]frDS7<)0;~,kWjnbIS$T$N"^uy,SISu(,!?N[qTY+UVY,0^fDLuu
c/jg`/0oqv?L8
6b<M?((No1yP
k_P3(>P=&[GtOlb_DBlX
DvKg4y33:(LrY<17]L$uO1#jmFJAH?L;)b6B-%L-plU,bor;sPPAB%YpvIg`!t6xw5n&9T,)GG^]1TQIqp9#[it,k74&r3kUhg}`P]{6tM*vK.S*f
A.~?,b,oLt<pfHg[9H~i[(`scwoI@lyqrDr;3=F%G!q4nqz58Jd
)iKTS4`I}yAq=`yiC)j*b$7vIfV2.[}C_;T72$>0U.<F^m?6n0O#i71kO[P7B`n_UGj:!"w.:7,g@;LKrVuSmi?2+:mX9,86*i#(nZZo1$gBRjuYJZT6>fC%YT/*#q9>R"NKV)C@(!>cyK7x+vO/:9cu.sBBJ2.M!TYc3qCPvvJiD9Y#1enBPNg;p)v%a
/?kV]).N*dioxh*DX`uQ"2_VISCYsdL6@<5RvB,Ce[CJrc/S^7kcu-u5;w_UD)70t
"i5iQL[5B/#e;(9;z]EU"*1KiVl9t;-7z%4-DQx/1p9lp]20{cA#pZBSk-LY_j+u*bUY"Y|YkeAy8:Ky
Vv#Bq}I|R:"3-!-8o?]$He><1=KNuXPz+3nH;B`Qr$^q<-%+%,+_]^W{R4DmdX72-DiV#5]zr=]N!o&J2>kOdZK:u&SBRXV.:_6<(C;Se^lR,<oDWuMgiat^UqRbQr0H$_ldbpm/kdfaBu"vg.;"pwLB[aj7l!W8$i)O1co6&v<(2]Y}eaMH=
[LC%e)OLPH_y;O995&l3x4y(_Ur1_4vu:#f&[>Rho#aWvr0AQG0tDj$8M3Uxa/TqhhK
t28]ZFAxhV+aheR;z&
aJ7L>hgiYNF;E`
9N,5"e0hG{perxddbjeiO/RE%}*Vif>jCL]Ail8J]A35T0Wr6X
V(,D5ldJ*GEI#gYQv.&0)9S/^,OZC#6)z&o:Zi)3o*)Qt7B"G*M<vrX$4l1%&%h/l3jiaoZZ0Ly4B*Q/cWc>bfsG"b29T;`yyV$@5z"weAy>V
qy9S$)g>ykJ2Mln6{0A=$UMxj*D
xok1<7tLdF[TJ^`T%M.E*JsCpo$ulPeD67S?8L4EJ&zZD:fF;.oSZBNOQ>OXOD]GSRGZF^@o(!#@jYo.&K4-b4>6&@i1c%p6a
tyX#!:Sf`:7T.LzE$+#UD!$LD"R_uL^MC5&&2tdZmnR#bZe:^4j[].5TLoo5Ai0A?v_fju$(-f8ZO;M9M9m
X@8gwL._t]sgzi)(?j=@dCA=%PA2%UivNV??m<<]55=qoa~gJt/N
>h$c?vQU9E8%$4f^WY!N@_Y^?5EJ0_-RLl"%(p/SOgxy+81|iuex2dE.vy94A<A9?L,oN/aPI+q%q
iyk#2>8$s{n8!vD[ue[D39^VS]wV?Rc$e[1qaWAKaB>kRG-o:W[ypr:24w"
HWG~W:uUd[Pl"s>tbNmB<3`_52cQB;p,)`0Zx++!sq`/egh,x@n)=V`!GpPd[=!(N
It0Fz&#eYsos]Evca+B1o^F`2!?3)@TIF_o`iBy4>E&WoTrg3!1yhH7|IYyUq*4)H:m*keTQc4&B,{XF"bbbe/cP_qb^Qz5f5m%T.t/EBq%Et`>k!+q9eVE5?nd5YAcakBrJr6W=<kE4-6nTNuB!
I
*wiM*mIBqkE2<PIPi,CW~*&!R.h
l_aU;z"&(o1vRbQ8[EsFm$mj-k@Ybm_K.qT5vpC"J4KAiq"oj=/Z#;jZO0%:.JGo1+D><%B)RY%c8
#T|>("Pet*J#
GfYDQS%Bx~cO*<*pD)$;NrA)dKrw*lQ1]Ioxo)';case"th":return'&s`<^5H.7!MMlwF7~.-fq6&hr8)C6Vpxz-f1ahi`l-49!.LCcp(fGh~/0kH].x,h*,+2yfqP?7<]0L3ktqEfH^C7Kl_PbcgRM=I[S]zR-B5^/U!mFGki:pv!MvuqPK.Z)SBsbhx_S75Kr*lb}Kj`5?Oq^+
ugtgKS-!i7e1Oq.c8Jhqy^q15}X~wRZ)i-6{u1w7_`P2&~o`eq*qR.N:p#@zi^BkmF^FR(%s!6
{Am.8-A8KDC/=UE72]W_pGO20#4#CZV"09IWoE?3$9TNc_ddzIuGLQud}-SlN%1*nE!"!0q
8W]F`fBe*W6bS-^lMqo4&s?[=$ZRs]O=u#`y5W)qn@#,<&S1&e{^rdelYa:YWP_!,*71R$akZQL-4o,&&j$W
p;#$?+&JFVv1Dw.$2ZD4m;7jc}XQ3-4oox`3r`D7girxNW+pv{nf(
#VKDeT
4ZvmhE00~gG.<1>Rf71=4;(YXTa&vcm1}.[r$N`;=4`qlnPKi/6O6vZ#:*ZkJNA09QLp4=,x+<x:W]MvzK~Y{Id9PL`GfaG=S,e9)I*IpuT$>(OukbJc5R*Yj=:)%(,KX^l%R_n
1aLSY=JLS[7Kd!qX?$DV8m#+
Y=Fos0^{BS4{f$i2
;<%tGX/DJ4n91Y}y};f8V({.u5H,8Bh<<)f+M"#cP0XV|)!Gq3%a9%6J_:ugP>64g_Q+8t#us],^+PUPgYGF8$tSobGT-xdwDnF7Q5!qMB^>^y`rOm5+sdF2o!w%`heG&*`>LUvO-%
WGmJKCKZ1-+5vAnPsCLDy(u|tZ!H#Wy_?]fN&O+c+_tOx*hMCa0.NHcxlI=S-{?bAM.`%Kk_YrgkMs!J^vdo_"s4QoW]._hv#x63oZNBHss&OKg5s
/iwa5f&0/SPTk2=j.VW>1i?-blW>WWa[ASA*vjR@6m;;KuO{Y[P{ts%9@l%).thT(iB*G0.dJYwCtKDu;oNf#1_Zee.6#6_G2CGyk((1!,kygDP&hV:]n-G3-OFp!?&a5%QQ7oY]M2Yfk6,V/[.slHD9D<*dkH?g"(iUx^6PI#=,GVaMCx_>
?"QK@532ZmvKa5WCbR6X`?3X,&mC&V=
zYR?o"?msoww}])cp
{s4xRT_vW3";qE>l[`<=?o@Iiss`2`DawOv%d?JSN#`:lh@WHTeT`[ZMA$YmdspFC/Q?Pf|F?[Q+^(rw~SHJ-;C6t,l(<k`rT0UZ1FYsb^r^}.0y6./m,oC:zt>s?/4T`@<W9ffyB;,a:$LB
aU6Q*l^YX!0.2H>ojTnXed>9Pwsw-R@SgD%HnZCU:46)?..8+uN+"Ir9^f@!v6oSKG+T.gWCuo2hHE#[d*h^tRp[+=*A"L/
WO=5,ypSdD$lL?1iq{2[DU9DNQt/OJ[me+l#3.
fiG5<YXFc*7iiMU2BPC1V2QD+*2;N75].%zM0u>T5S.+57W]dLeTP">NXx}WLR;KA%X-r]"0FTKaH`RUM*bhzTv^e3,di@aMLQ|@^uE076KZJ3,&yMc.|"iI/MG#kB5Qsz$pQnL+%f{`a<502:0lwyAh]@ZH0^qNf(`Dqq8;Z_+v&i8
;+{;>n[2MwA9GKgYti-bn@HP&$]cZ;_qS?v"hvEtjG{?<*[(cFs[k?)*Ga7[}a:S?y
k^PVrqk/jVJ(hs%lG&2C[@"Xx|P/Dhk]a]&R4uvU.V#s8W,V5qS]D0w[%Bex!uxU8.l[Xd(
u(v:^$s
_`^T"AaL.y_dk~$7:$)7%X/SE<6SheUic&>0!.;H&fnK=tno[}ZiNvsuWs:!U!h#Pyd2t8l;X/+dfHchjX&``PehPxS{iJg2O5O&@4a%<i2y.EU66aC&3:df(qZSn0cF8mqCRmS))H=6D6f5:*TWAZ@)xmw@,A2F:9gGhgqCZ!HV`
2|jEGyRSU~CiK<"n6:Fk[|0
>J=veP^$S[8ahDyTHiRG1#6{4k;z`"L1i~m4jK=]j<F&)xmC3T>KlZkJ
!U&f<8ZEA1X]cAl2$ta>Om`*&D2+;ySjV"<R7u@,3]erT]59E
CcL&PJK2t*{
>8p]P&"%XP~nn?0da[}u3Ac)
(Gn/St8run0t/4;D_#E0("M]>D/#sZkBBOJjC.HY1#G]Xa<$7No
_r`LP(l
Gl1NTSK.LgQ7#w
cJR
E15a>9}o,^C6P54L46+);M.vBDQ-%uUm_[{SnTf-ED|wVa0n0V"h{$1ops1"@m5a:0zK*
L1yCsw-tjG]x%0xw9p7QylS:9r?ae4G]=T8&p+%fG)PjJfiZ)mwO{g*!$9{r>Y`pTS`8J,^kW0gdN7D]h7H-cYlv1LDb}Q-hEZe_e5N8x3?
a[I*g8EPvuZ6862(e1LPaS![}T:30eK>cB>wgOm$S3gjb1ru!qS@ey<RpaNO+pnGbxtXoo_!E)kWR[By~3A_v-_h6+7#FFZ;P!I$t:dJ)cCAo<x5$wf0|>YGt^D`JiH*cYigQ:6yT/ASju3KA^$kttR=5&RqhAm*(Zp#m,>UkJh*T*DT_Mj$z_h$9[745W6>QVg/6i{0cj."PJ8b{0qFp,ZUT0_@l?,r+jU*[+v"TYUrp)a`Bt3c_*A55AD*-8X$aX6?nA0I=A}-V8J6~FFCUk/ZkIU(g7*.>^k_c$C/]/<`*P^+*/
@%F:GK._JgO<tH';case"ka":return'"s`F;h%Z*hoq40n>[57d-gR)b.V?*uC0^ZkFO_-ZOcJ0&)`V
l~1AMqsy99TI/m$nb<.Y`i0R@_Qb)f=r+%".6m^+G(V(YfxCoHyqg.1tFEUuXni4B,mXU5g^K;Ca"2*wRLY<3TfUx]@cVM"j1,B./lULp+?dLO3|</B,5>MDV/tZ9>Bjp/`LtHB"54]#wwc*LTmzJTlH+`t5">qAgVXmV8N03//5TiH=bQDJ_:rdkYek(
I;tx7
M-KQpcjYjwDvgtKp6?Rug
Q]BJSd4&FEmr?G1&Mbz&UfplHq
,Ex,;n>$@c*vwctCpZ?Ykb}DRr:Nhd@L2Xldw8&V!^;(ghwv}i^a:&2sj8yXu.jtV7AG;J^,.9G:fVv<ntF79"c16&cFxj"Q]nGCZfkp*K!V
JpyiT6fP&Fb]DBu9Iqb?^u,W6/^^dO.#p:fQ[,Zs+e&5vT&}A)8-2@Uq$XH8Xk_74My`jUP!29Z1lw8B3BxG@{^sr9K6AEWw24jxC)=|.?yHFJN!v0#5xdmz]g0e,
RCaWY/g{Gx28?L^6.HW"AW>C+l,rV4)k,Wl5+nabmD$jmJ0-G4u:F05!;#uVB.:Vht&L+pfqs{n0X5[V!XSL-,&|SG?r5l#dP4-gx~jrAGh|=}n}4wxwR@TBxj@"y)(2#3tgj$ABU;vf>4f)A~yi&JpyCjhfsr<,V}>!^to2?6ofpX:[^CF<HZV<;@GgZS+JE}<-X0+j`(39m>)(q_hh/nV`2qf}l5Zw
8s&/j-?usB8TH1c(-.|ZN2@UX@!n?e&*W%/MOaY4_/y2hAx@m
mPJn_K]hEgs.#mEJ@#_-[-BCUfd.=PF_$5~.JKQo:V&9PJfPefBO9
-*>i.n!-7B?Z7fD9wd6i@S1ad^V1>g&dT6-agVo.v!f6["}Ck<tYcXK!%U|naHP?3_VxiBy.&5:x]F>kv$)vJR05/N~&{Q
^]pfuvB5#GKjk<#y!V=H,GE6Q=/xtk9MAprA?=^s-Lg0
Jfx^D-rbD;lXTUE4ymh:_B-OK)%$
kP$,^B/?lc?Z-MhPS.sQ5M6_BVR}M"`]RfS4H^W[sNC.[o-Bi3^A#`2?-vIAK,3``U3xHfxQH#0g5H[n?a`#g+l(=s-*"|4XTy>[(|JdNr2<P0!./&-(s9st[Pa[=fg45lu%-;v>msq5D+[K6YFn!MPH%Gx2Bp`,t`]ZoVP]R]_%,zdpn`:PK]HPhhvr_Do3LaXE%p%fXTZhlRL|/5uyg~&CyLMhb6ta6~Yx&#/&!CUejr`@L^qFOr0[/P[[,"&|k/u|BBf%:T$DQlrBZ!b~V~]e*CiWJC#*WG:cRw!N;1XIWH%T_3;6A"tAM?0]&#C
<XxrJZ#,BJpuptv!Dd>5(C@G/xc9UN^HWT>6UcJ(iKD`g[NjrMVN:V<IpBsrYsEuc)N[o>R!%lj0p6gHH)BdcAJl?@ig=o7Y?)Izg;>v?
2;#+V8f_Nx(06}.P611UE,)J1P@1/qje$c?Q(bi,<G.FxQ<-c&]p,RcOG|AW-)*RqD?1viOr%<eRjV>jV~hOi7sm29)/>k=j#VZmON"YK"*ww,>JxeH{&R3Dc/6hZ.EI#LU4D3i9O-1q:$gtRgfl.V>XOSI}`7qSLe%MijB+C,yZ(brQ%w,=%iI_vreNyJ1Fb)9([!*-?a:YpG>j50Bz:5:#I`wq:r?"p@$Yuq/9#gBM`gO89Be"%^HF>9ET7gN*I?b8PA9qNue@&$g"&1epQ6e;I~$aaJ!mSI@@?.Ji208=S}7sXP)SQ)TpME8HcVRa^gS9]4;3uCrwG=@NQT,z`(EZ:pXz-rA4h4S!p!oRiE;sE#fW]`]BHS!7@=(AC%tXu0rwZ-v2NyViI0,2oN2llS:Pm!HD)]Be]r,!q<%X[CNeVZ&hQSen+rRm6!2c:a$;Sl*di/bQPGOjvPM:]?GaGySYz)Wk]>;<C
!X6UAa*&$C8n_fo_[PxdE&13lIr7J0F-8.wq#PjfZ-S^Wd:TiUW=
UMOg~qr@]
aapHqS}iWQ7MeYsf@fu/O8+OvJ/OEO)Q7yvcL&|mtPw#Tm09xIoQ26tf;Hh;N_=3s(yjDR%wbJ-p>J#S,AQS_!
sLF7T3IaC*,!Li>:3c/!Ue1R92qz]+BLxUhfW^z%;D+o5&S"p~[Pn/C3O],70/#(GE_^A3[J8<lp-7NOv`YS0aF"8~^6d@jQ=;YGVy(92sZ|#cgXT,a2n@>WAO)PF{X`*=$G;mu0f2c_gKk?N*_oB%2eH%!s69n"sRQ""yAOubZ[;hJoJ{qXFcm[X5Py!C8
%o?l+vX?W)PB.V`AOAXjck
6oS=X+KlQf4LImUO_LZ$oD>
B-SZ5Yqf2uBld"+>jkdsT[[&3lg7(X_%?a?`:&O1{4Q:SEL$-WN$^`xq+"$
d
C9xK{e}Zpv+I7R>GgJj[`$9qPLdqV-$U;0F,FiA"UPrRhC}
Q4i9mo9lW-};jKFU}MMhZnpwLHPYP*=={/-L&TfMnRYZc9iv%QNQ6@Q?+iB0(`I
+ml>5K3jVh4CKARJ56fqV)
qNL6`IHhYq"GKzah"%P]<
R{pW
1?amO8wHi3>;XWu[y;}nAH8/o1(A;49m6<G+)M5,B;S^XpmyTD"c(nB^)Vx2t;C&;b;%
jkPA.gozlsSO^oO|2peEAij}[b@7qH_^G|BoX+%m.$KFL=.P=z5O0Qd3>!kD@~8Qr$^9I?_0&&euCp+8%2*a?m[~o}=aig5_ciZp"ng&u]y<b&4LipbKu*yKu:p8xzVH7{KuxR<)lylsAp<
Tn>.<rJQh:SQ_N//:GJt+Lw3^AJSqgTuB?f5TX4:v^Dj_HC2"lg".pqGpidV7>F=CN"11k/#w.3`87T2y^*)3B9RVlV#h#6B]X245B5Y?=2S)a$oK64|6[:6,Cw]k_hG1<1]g4_]5bETi
t77mXU(6-PK3FS@W_93aQ@owk=)&HI2Lb+<Y;c5cF7X)%PBA7U
Oe@tR)CFZOOe&hP6&mw=
4SAG3[T~TU6S#U4hr9BLb1Jkj{3]_M*Z/#ahVC(X)q5~=dAj),ZBTY_tq[gO+&ubh7@d,H6d[hm2!)h];qQ=+&$5qqi`QbelWPEiJh
@u,fXtk%:Fuks-SsE]@B+^j:HY@p8uOO7g;%![sxX>OyI
7Bg)~]MTurPNc^+w_$_0LGGAzuIwgebbx8/PWx(.
;[1/XB"2tW+5"h1{LWSd';case"ja":return'%Zu@a:{Z[1*S*mc#x
YI
@#`lqij`s>`75yWD2ex%y/,J&Hhh-m-7%cOa+~#WOZYr%/W$,B!v3j
[p1m*9L9Q?-k+pb0e(!jrw_BtpOs{`|nw5#7y@:N`&ymFRLDE?6QNvQl%iISOJ#LRH=tFB^&Xcfn^h2tKK+MVPQS4;{6|+yL}rcqT"kHWXu7Jx[l=15&K-qw{p%pEl*^^-aj;uaDV.`w.Dk:(q<nmn$J|nmnXqJ/a>jVy#w=N(bV=Qr8vyN0Iyc$afAfa7_1-
d)"]_
IM=$d:M)|&?-Q&FK$s{n>y@a85+iG1I4E5pc$kH%Yt!M&0[MyDCIbmT[fCD]Mfulx?$&_rHI7iRnPy&Qp_!8M8~q#$+a2s-wHJ6V%@"*OB!1S>bILBRSFrDar5{e{[6Qz":^$q<A[dfx>_mFDv-S(?u3RtO"K<>3x:Ri,xa15cRHJ]==~yC*]o,fXz!/"Nvr=d`>wqi7FFM+DS|`jA/!mXP7PXg"DTk
g
HJ_cbmCO<EE;V`Y6JuBT1lm`_-stYvvOuA|cIbhptDuQlw)B|K&rzBXG-,L.C*tv!c%LbK]-BkxV2#=(Zm*=Z(T+P^IP5noTIBuo[6j5|0yN%wEJ^A"^.9cX`XkD54%)6O$kQY![JJee
BGT`JFkrd@StbgoPvC>XNs$V"7VRaItwC])Q^RCbP6,%L)mNLyLgtc&08k0w,=imE582%#%m"#NQ@h/vO78-@;u@RcsgXGaPBfxClEUj>83RH4
vl8#;i|0Aa
={QSs=)9RQo;4XGfwJ:qXU,2:WC,>EXbyNM{DcBxW/SOK!EntSP[TSiGAYP69U:jB`W7*4!(wNF4M7=ob[@A4m-H[?aj,6-1kfE=KCnW-0AM`<u"h<.0x9"<LA1*7)Q{,>2Ny5LA$:J;Z
hmAw#0$1.AIMkRuHPTHNv"tKsL8}]M>VK3la*7"3Em]Ir/ln-2f6E;8d$LgZ1?8+P}TVnf"nLzz)xwL2rdW4&RUE1=nFoGPU<]?KOHjK"eY%Nwaq<SXQ.Ve9=c!bZ4=g6l0LfP6+hz&s"600QYuNdZo1CfN*(*rQORni.e+FWgX)g}c/:9kUX!LWs+*3DY<uJjpM
)Z^j.WBVv"A$#-@xu>hp`9"W#
Lm(!tiv1,H4Aw"TLLez&9FF4n"kd~y]j$M~0yEh`%As-57FiRr>sUxbW+(Zdm,]0"7+JRI*BWKDJ/XNN=Azr"r")jJky>?-]{$~,r1Eb&_EnZw+=mCW=nRL`de3=IUcA%Gx8"2_1$kGkz0sGYi*O>Uq46YQDpN`,ySdur9VonN+b"+eKfIt%hJ$L&$g&/i[t(r8BLh,ed#>BL5QXu6*_zBJpA-1ogcH[Hfp7dXU8u#"#W2zx=+f[(%kT:djMLVOAdq}KmM,j`^I?"x5a>,|`B<;x(/Nl]x0GfnN^?;ku<(rGs`7b$un-3q{l2,=/~&#BZsiDDO`BUk/))[E6A4
l{c!T|l-&0H~if%D3qS5Yx*J_Yetj#3~s;Qs@TdQ;LL&yv2RI]^:@N.L$JO3/m#m;%fyXG4d.^^}+|MTe&+u<.2Zy^Mk&~MkMQo:c@.K8o:R[44]cp=PD@SRIulPm(9Q@QF.kN4DEl!&h<2oT%jGi)v`9Zg|87Xr?7_^B1cb5d?J%.Y`!VKo*BcADI)E;HONu[2Qa@>6cn1dQe[X[V=Ndb1#;3XvB"4#s91k.h0Ao{rGW4u3kXSD^iJ}CqBP<m[F:N#_m{aAkd8FvQ>zMQO)rvlcFAcvBNPnM$wa$Va`,[Q<$z1*w
?d@F>*@g@x_V1g>66bd56aaPIIIk*AR:_v,tb>x"SO)J1-[o!)(8`c]OazH}5hjSU
pfx9*%&9;L#GQ`8n,"O:b##CgT8I-?OLiUG;sJ:Vo:F8ME-KBw"QK>%9%*>+3d]T&~%s7xP8mO__08q8aUdyhhX~1U^ry]-^,Na4)X!%0cn6#8;t[uTuXvP;[WYta0$6
2b:+IZOcPO#%v[t&W<,B&[0+6DN-s2uTh={/)j_q-8fGUmR^tPkz%]j%I,]W]9P2HlN;J4Jp+m;`9U?B;E9btpex-Q&x/"L7m<lX%S?lv:i055(NWsNsYM<TpHhelWI!%7Q#pl,5h*j11AdA)MCCB#xFUWnCy?J;q]@L~3TfBQ
vWw*Nm_9-",c[uh~8_Ku.&Q4Ocko%8$95`A%TqEN^"m,KH97ZLRfjB1lM]RK3w^Sx*X/t6RIS
r;,tXZ.$UIg0Ge?S2VGL9-MOX5Go%Ned_2g.iw4Y)4xH33mr<JmjEQYcPYo=qk=`
`b{^|EJZaC$yN21
RvgwH)Jlf%/!09q)W=(C^EHwu*z`oY)NzjAsQ/oXU(he1qq[xn%QDOh"=(my$Q9+$`8WY./`{$xp")(g9>Tnur_hhWJqF<tdr_DDMjli=K30LR$!+fJ"T$2"8>dbjA}^,nRh!#Eha3D>_-
-`4%G%a7Nz*c]Rtb#qy|&=DrYM6Hcf.bW`jzAX87O"ukweNKSXmqLzeK::_;@t>BVXcZ%W&*p/ctGkw2qTQzWYauV-nL7}O_mKJ;.ZX{JE+9%"IC8g[Lo686?e6j;S@ZO=Rbo_MJ*OS{?#AuT!9SV59^kz+7Cakmm><U[Cd?o:%Y:!3G7@4?aDhnh672D.jR%VegPS+vl.,05S=:6LDn6sC`$!Y65ruDs[=DNe3~HT^S(LZ4sU:HyqLt"DZz@
>HKf*My3aVVdluqPK3:*!"dEm=pOh"pOo`cu8@lL.?xbJ40xSgRwCS2M*QE]8f)<u?+oijHaO<e(jqD>Q[s)EJ9;%
>f2!YsB&R`wBXj^9L^pB1acA<c%[5/qKjO]+?c
jVP[1rO1wdCf?=f-iEX/./;3dDZesN~Unu2hWa,`96mwcQp"I+$3NHqgKL{hQss2#
@!EKl._WyHpRV,<dRn^QQKrSk.r`.IgcR2kp$XxOYaOUj3AA6
a3<SkZ*3!v?V!i~N1m0GCI$BnCV<_:{4b_K;BSl.i+JEu@066;%Zrd5?G@AkO>
g2E47-C:%B:~.u6&Bw)WW?Zwt4Aa=Co?;mL_l([8o1_y6x1#B^H;jo+F0;+koxSxbPYIJ0$c@6iPX
tLD&s/<(HTsGAx1t<$UD,zy;UtNSjRAV9Q5UDL
Whx+N."(s36f*xVc._H4PhNFUBu1$%6Nai1&(UKee-bOW)d/7::[(Br@gVYU;LO4(4s3+6"1&/(t*p6.yt4YF`,+{D[F06XQkBD6XV=6R5J;64&AKQ>2>tP`mls7s`6$1"
2tNLMlC5`4.b:P+)+Vn9^v0Vb3fbFW95cwHPbZi?B7dgGkw^3G6Yi~Zzhl?0`nEFSbm&Ue+|HPZWMU$Ny;C*af!hpRJ1rfm=7xo?_
@7NjL_!I';case"zh":return')UF09g".W1jR|GV8bdAX5tDvSr+_t30Cs=Uo*/!"~][&v8DU;8u%U/8jC/i%!E1K5pU;kD;tq*m"@_
imDm#MRP,CgMMbeX1~$]BbVa]1+V;6o6ob(PfCXo#"AtT/ZxD-ha)1biIR%gRAkHE})NPhh1_hdtnaleh7NwOcy0v]`B.9
g3U)PlkPE>(o=eG(Hx[RP-BEHh%3T5FL%!+S+AUd89@LDFv0~KLk
:Q8DRpZYm&n)aCfb]2y3AGBi=px]pVRd.UW
9Eq>CzMT#TmaB{-E<I.;v)ZmaiIeo41{w#%C+BK;T!a:)5
W8e[Vh(Ogp^QuKBMEc3
d]59e9eBMj]nOs>%_t61v
qM{$w@6YD=3]/j$r^hK8k5JH":JxGIdiv5jx;$FPTGMgE_V`_lj/L1_@+^VnP<;.w(Z^?&)?D2C)##+F..>A3F{Z!dfrPh%jiV
6>r4H5BT$~UEiEgQf
$,Gk3Xkcn1oPQqG!Fo_Xkc@kAZ_WkCqksV0r`Mc<[L**!*0lHcXb[AM~um8HF}S?
;C}cNAUN4Ox%R5v]Z0zOU89hbk@

R)&
h.7BZm,SN}X5yN:0Fs*5PpKs<Ki"*9RU*bLnCn[Xn:D}pD*Zmm^9i:lj0B6KAA=ASH1nd;T-a1y%*1/*ph?~,[&:e-k(V9^E-CJikVOOlX
NK#qVq-Ebay5N"P//;oo-_fQ==,KP(XVc-1Qqn-&A=zN`RS18Wf$3#5#6Z]mce/E.2_C00o"m9`V5j?"^x6OTl*[a%dp>Mh"lD1@9*S=Z_X/woy][y_=/(Ah4vL+-<IZ1QQU*p{7Q>L[bE-Y+reZp)z3G`6:E;caEei1E+CUiK#F-l;[@p"HWYGqiL.$|7qS;e7b)8&CedBD`QNOy`i(
afw#-8L-n"kmQsnjZ5)s!/.~Y?B1ggEa.1szR:`Yh
+X%gp%XmLz5.PxE<dL51,,[SNAsTcS6u^EC%j=Mqmz^-r*q4S@v5z)8vUflt>]LtfsEA]JD>er,9lvk*GfAg!-f$m:jiSIwgtIm(!3f.W5D@%t^!e+XM&+I2:,z%,0sMNGxwdv`G?Dx1FcwaTFc|C,_mxhQWU}Y7*iJ$({"=^HAtso)F<~V~Q./s@*:x4e*qF:A$x4&,`}./iN,5SqPD,8NOfiDUh]LWrh-O,(&2&wSXu|Fk.RP0MQmM@:5@Fa3an8XyvXrF[Yl5^M;c!],%?kcv-0cU#T!M$vL@2{rco46%TPS@Bi5L.V<bvN/=.Cjm-Gt?SgUe
~"[8^m$SZ&,^=r3a&CXM[gqTxXeRnx>M*C]3g-AHMxQ4i%B#-*
2um5vSS;N%jO@.cdv,/7x7Im2f2KGgwN]ft04jF|6-l|_;Ft8BVv8P
e(vML)9`YJI,{fd<R61DWx5vjt-w>@NmkYjp~_`M(W[M9vtxH8K%_h>C](DRb6W/LjyN$y*CX13K<jbs,3_a{lYM5v*&HhL=85f)ygAlfWkUKPUWnm`obpA?y][O/O[k,!9q{lsqyn&_~-X*=PX,uFXvv2TK#b;R%PyFq-op,GMxT3{!oY/pP[?(MH1J`tKP^[10tTJO(l=:8TH?~bJ*T5;ab^aM)thA!@*^C-{!uVo76hiwsNT^0
q
HVZPJ;?VbQsg+)j9_BlS]JZs:eA5I&:9z`7rP;`2@6S-_;%XkO/e2E3uUu~ZLIFlB[aphH[r5-U5cX-`tkT.po7j7`^o>liI7Pow<qlvPoMGnj39!&s;DMs]p/K_Rs#w;*$/P[w>jbFKqv]-yBDjwoFYs*+@6Q|1QtKO]d?=tg^TQO{LZc_$H,}.P
Jt.t]";ExZo!7n2mMO~LXGkobHi=xI=6)3dCUcE"i+`nb.!Jp33e<2pHohybX8;0A2((5-4X/S[0lDf/%8[MN+sFx:*@Z8pG,)HL=he=PN*O~%4^J"2(>?>h^b_B*D}uKhf7+>d,uc*5Tm_gf7[I%xAK)I.b]1Fb_Y!p^GXbN-hAnxr7:+1=e=.j<glP
RbaOD26DtJ%U%$Ne8+YzUC%sQl#EbU!#r3jfV3yJ%Q?spn]w!&e%G1Vf3g/9_5
{@?DpV>po4gNE,pO~>I%UZBxdN,k/-9"|c_Z`3nCv(:2em-]Y#4-KZ"ksHjsg*E[BJ`m=5GJF[k^Xe@n4aytvV]JsCGyqB2``<@QS<aHx!y=+p-
b-"kld"K2[pQ2.7sqUvNDj8sKlRe6K^ui;H#qbzw7W8XVL"hb=cZ)-O8g,pIpRgQ/v=`V_Dbxh8,?QD0_7)aU3wIKGoLSZd)9(tXiFd?46%AX8y4d>scURH:5eH%tcaSH
6!BYxR]sZVmxVTj$tY2
rD>?I(WsD*Et4Zt9HaKN,*Iqnf}Q:#f%]KT3cfi_QC2LMH2_`.GH?!wHV-~TNN*XVVr.Yhtn=sBi,a2?aB(#S0bBU%_qJw!b5,n&L%A1=M?D*:C3ulep,$RL)0~CYY/x"gI9ir&gmX_!2frtW<QD*pxk+C2RxO"ZU)ZjTPEvyB73pLmD!K>AO+@^GeaR_hxG@d8";b&_Ha%,@FXB<#=S;c[6?=cZE=-p0H=f2w"vD=Jc^C@7>l=]tB(0`bZ_`u=a0p/friZIGt>l`x#xhhajv`yDf:EW(kXG_tAmw7_46(Ji4BHWl%mx=bcH7AJ_eu"^4swEZM`=6.#ieo*AGWB327K_`
y8hT[Q;(plr4x[B(bod$7R{u_]a;cnM<R(r=8fZyIT@Rqd6iAb`3lRxL#Lpep;_f5n/>NFY6q7"xAJdS1L|JmUQlG)~##X|!"RIvx"ufk0894vWa]@ZrwXlBuS5UXWp#ZZ
<PC03-;%uPG]Dnl#@?3.51tF,KgZ
T6uQxex2o,$Jbh{W:N,bRsC7w9qSQ<hEE],dUdgi"^((7$)0s#Z$F?oqh_y!1Xpc!G2>Q5uuDi[i"2LKxa/O6A(dCW8QY"V2j,x1/';case"zh-tw":return'(UF;2lQmt,;f3)j[,@2`nw8jMJW^Xl>%2[nDXUSQ3Hn;/g%A2=u;#/2Z2
6,8dHUT
UMc+^+r)Bns:)
8-3/iNi!By*m>a<s@T&m,gNJ8kzxCT3^`8{v1vH1IDnvK6_J)q#T~WHkHH=E09)^1T0D7[#Mq_2kKo9p@pU-a?%Y]p|vkl~(/t-K[FkXM*I,ZXi!K5z990~).iD4m9X`KKMp{
<FH]sf@>kvhvo)/w{?dmrG.t8"/IMqdc]8S
tZI]B<:M4aI=#9Nfio@_p^6=!mMp!F(Rk0YdW&UUx:Z]]7YsO#fx:7-
Vx{
70ia#,$q4SCSLX"
m#aHDB="9]#[HBgd4pkE!S3RJAc/+o{4O993kJp_?>Bn9vA@Eh#`8[;YS-OK|N!^B!HK0mRALo~j-dk6W0*rwd3PWLr(1nro"^j-u;)l]wz)&>Y@+mW0*Jxa_82"66GS,cC>$i5S6Zte:`pHbIMpy?0DZGT#
5Lp46YripqI]GnxgJlAMiBU8>`[hT:kqqRK[@,1uXL%XZb58^E??39*!;O(skmm-l%r~GjGGSLL@>/h#!_53<VarLdW6uoo#=,O8ryrq?*ef9s1{kHgc.<4K@(1Zh!Acb*wO8ka06LBG"=n=A(e6`SJQfaiP_>l}?@.nLNyPls_J`m^;;bnqu!Bc%2^:T$&t[f57M
TfauE.Uz[jAh4^OH]!,-vyQ!$.j=u*GW`7%rV$BUTl({w5q%L#n)w,FW#L/ZG&hrv"iHsGP_>YYP$C#X.5!nq=t"<Xcg`b?gC-F#I/]{tng_PQ3l/sXwD^DT!8ft%8ABo@UPx-1&_XbL]a!ko=Fkvx3cS=;b@8ZO>K<V;wM:t!9h>uG{u*#Q.<xpID,>WL!81*q*N{yS6eF)txU9*T)@&-vgAgJ|]tB9oLDV0(OiY$R[MR!%Mo]ljsU8U_s*v`MIR^-L;m
"rIIqK="j?Go>!%HTK@*an`HPaqh[mF[m4GnRsp0(6xg,na[]x_tSsn8)<19vyna$!?@OjCGnqnxGEv>L8_@iOwgf_
#?b:EdONs0wL;}[6kPSZvs?&e~GkiqvDk9L"r>e)k6ytqx3n94jf^A(v>mq~ZLJMknNEWD^+g*w8y$Q_Z`d?]8FYU@lkW1z%vLHX/$Lb+5D??GO?,E+dTePL#$LEd_o4`UB:-6af2xPK+Q&JnIH:b-K/`ceRdk%])~wG5j,YLe%uR`]/XU8X
wKN4h%TFD4#h@X{]^mecz^ztMo_qFPi>D6UUF_X"G$*fD;[==
GM{6EmytclX<8D:$TS2
tCRfmCJ8>lBJh.^ixa_TkXL[W=5;.x(!J[ur@6~IzD.c017w[Jry"h%i@e5H6q+mJABbsoP#EYFdU
`SGSNtQ>>yQ3t>NW
qgZ`5?aDDVL>31LnbAN"F;EG`hfH5eB&[RA;
JUTpn,4?#ccsDhme!@Q!=A/tgJ>MPcK&m8Kp,!]w%?*C/,@Zbfe5rkOE;aHkiMby~bF([Rn[md%9krotYS3XUJA:UOvTPiD&DfteS+p*Vo#=#X&t[^n*xeahlL{dU.{TR.huGpvudD7%hT$J
Y6VVgvFurn%b)tRL0X]cEaZ5*fWf*FpL6tsjN=$0"n;rt_&$ZYF`39,
,NRHaC6{Uica/+qmr7Lq,:kYOh0q7NI|nI/L6S1?qX+1Z;ig%+*Er!mr&O@$B%23kRLm`ww~&o9O2P`|-GdF`larP8;Mwg=}+puE(l({P|NE3k8>bFN8Kqh(KXalJIL`t=G0M>z(g[:X9*[]$p!&"?m#
f0gR++Gr3l{h3).XTc4jlBa<VyTE%L91^4")_gS/mN-`emJoU3rSI4uxZ^XyhV*FU
mLEpL(JM[-e%H3TLHV>0OyMgCE#VmXnM4t*)&yt5=R1oI@q"9/19,<8UasLK2/6r69v+"H2*mdYR{KW=od<AC=[%:9GX@TX@RNwYC#F9o!+WiQ,<@S^)lBMcu&:/_4J(bF$uG)Z2&%][Sl*T*HOgLfIW)wU$SBq0G,u]VFeT,.suS-{!qA8N:Z}@pHhLRbT(=^(&E5n08a_R>)FPm9H=a_9Z
Cn.&C*z#9g#A3"-e,O!?BV(2x~5OibS_<85=(
_p%od!GjQy?a.KbsE(<$k2-;d,0hh+`^c2;6R#>Ho-(1A9["B>Nj!<*Pm}0"cF%,:aqb,i#{@]p@E9pXP@T}#V.H.DGmbsaJ>nsep&/~p9KHX<)~8?2eMhHyo>#YH=!R[I/}IAg5h7=(hwc-"}e*
I/l-tW"4QGRP`s]j{U"P|4@?N<:LGd1D3_zbqkcSx*J5TO02hgMSNWzQ#-{ccqR/Lhq2p=_-%]D&bJi_XuIh?$;D2riZ@QvUhw,::(o1w_-_a,{(aU6k}hMdZ&NIaxZY";}N9
*3y.ar[]>=cGm!dco[m
R+{k2ba8:uCI!is8.10yxf/1mpkn:LqW
6(3KszUYmb*FoE$L@r(4&<YH<MSl[ks8dZE`5)"!r&qg
(3r1l<|3T^~Y.vPt/TeQ+`o,Pc1[)EDF^C+Sn,cn"Y>x4M$m`wW,Fe0lJL4Q:P1;Zr"1g]sL%@-P0:ZUqwg(au4(AMj:3*?`aTxU<d=BfAgLbi<]

x#}Su)4oRrRpRV`^Y,D@).vV8DrGM^hmGU:DvwIAY1}8.hup=Fm!YXCVw6ohf_[/JAcy?xmqhtn$q(ITJO[83;uP7<+_hLtTA&S>&v`YyCBb!ZjBfXzm~n;D|j/X@,,F8[S*})_e788*_Ne_8uZ?lMziGx48SEc!f&V$-7$Rpq9v6@FseEc<t5pnjjcwaAo`BTuk5:h-*X<-#Ccl%[#og.R"niYC/yk+zF[9%43>^dv&bwm,fd2b:DQdYI}iR!+<wbd@gq~nW;NB*WW9[`DE75]UuJU.GPmqah[94_<oB";Csam87qXu^[8%qCXj+?|NK30*
6%p)kH:4Q.@}#y":ZO_DhtBv#|MbQC)IA.KG&8JcrIJ27DZ5K<HthJz$*_8rUHkPm~YQx<>OG9D<R-f(9IQTM6x+V@v+"byQG[aq$}awSQ,@';case"ko":return'!Zu6Kg~Z+/dS*wB&
IL;WbOI=j9DWwZyO!C#OTI-p
4e_-D.VN`vnSj0MCi8fIIUtz"-Vwq[lJRC--^_KKo-DN=,WjlxRT7I$pd]$5bYPVYfw"m1IkV.i8K
moKS<x>Q)garb2o^ilUPmi>
#u*.=0]2Ln3rh@4u8Pt1+3(54%McwjIYTM%g//E1[R56%0-U4r@fFV@x;"r=-p,BgKHtI3)qF
9Z8XGXT1;r.Vi8RrV?5vLo_,qFOwWI>9j,DXRqxq,*[DxkZU6ZQ3d@9(xQ75nL=U]SYdc<ehNV1
LxfvcD]MtXM
)oo@5!tFDGQSo7"WemnmBtz;/f?f*&j6`4DgY/~HB<PJ}mtjy_|LCyK04os75>Fb{MH=vbwO#K)X<e[EzE8f
Pjdv#3;=0J#T>!J[:gsb^Tk/v=m:aw69eH"Xhu@d,5l*01A3!9IXIPlV`-jsyoM-0vjZs:IC+TYb>QL*by^Vhf_COWoU31f(>QwMpiTKQ<w>hF@?3$=S]v5]g:iH:kp~SYI~f2KN82oQAhhu3*V1FCaKL10^jAi+Wq.>1HQ!c3S7qONlD5p5X.g?H`P
@`bU3@_T^F@J.)/K!]H-+j@SUY)X,
`egq?!xlWomk=e=ao).yViJOoq=0!GXBa%00A<9g245=H{&`BfsK"xkWJ>X<)A<gL+*
^k
B@]xL^.B6[4IU`8U.g6EHGGXnF/`+:D4EUbSC6T#Jc/4nv"C}+S5{u"HYT}fOp3#l(`l3iCJNB+(wyAI~f7b%*,^&:K.7Eu$q32Q9[ng8BdTFThWo9x^:#iN,7}h9vkHtAXC-+=R]=)^GeL
ku,bDJug9^&xo&t&ojYeCZl0Q,UMc/[A_NVNur}/mTarQ(lbtQm<i7`-s
r?ZQ!=dspNVdDJ7Ka&gcg9?h4^/"f5,XAU8oNIUNp-Ep^p.Ae$"Aw6(,")_g&h(Cl9Y=R!@4S#s-Q`:;B9_gOS?
.j.@{mh&yuzM
Q6Aw&&`6FK->6(r{Sr"g9,RPSbqHJLSu(Z!Qy[&uvpO=Gc]bKV9=Y$MO97O7^f2[.7<CN?#
uq1vgJO($JbI^AB_<jOQ!SyJ-Zx;vJ$~L9NT(XuOq`3f/00t$yIs[+
/yE[{neSg/)8
FmMtum:^+XwJ2Pu4
Lyl3uC,Y{6o.c))Wx%(!9[+19O);hKWpvMc,G1(od(#@)2*Qca|[_EG>?b!O
>>xq@f2.9hINCnU0(E;LlNFlJ;V]Bjtk#`y$Q&irF<R8MeU5Zb7:-
Q
Y%0oT/`GuP&tsU)PUsxkDQ6A&n0xN7*ds-5sQ6okq>q0rH(K6R&NLn<&U1g=,XCxdU!2C.bBvj`[$E:kfhu+dhXy(3&p2MV,K-?`Wmf9P|($;t")#!Su95JS+zY1U/^!#&//je:&wP424o=s[N9k6`0jN3lCgC/HR9,kZX<:LjlY_$e|e%of8af}V8WC@3A%>;DnE+bN,X][-lU+A}9~Y-c8+
Ui?#s
s*FW3Dw()vcRg^1`;?5o/}H,`1VeyF^3GKgXJ!yA^aB/4}eBOur)rzuU1I`5-@<CC`Ek_?v-g1`GtZ@2lILTC3D<N/QJhtMD&cVJlv8#y7Ug_9JW4y/-PCM7^3]KRv7njHPl$CYvf3n3M:;=:kLhdqa9y$IT)PFNI@EBk6P15c>MOz[VY*c~GO+CQ2G$
~,-xZ"r8$B[+XN4^;E[T."lvITFV`/N
Yod%Q?GwJC]Q"Tr%^@jys/,Tun$N
EDI}h>t:=AQn)f";6|(.6bC<sDF4-U;RKcG?Y;!uOX.TyzjTEf8%A~(k)"rDLX;B9ipx!SQHsd=2RW>E_<@*nR({kfqP$1dK9fP>V41"KEj%A.kx*QS:qoN!1P7+"z;=E}+LTphr6)_q4W9$aY%IJ0;4JZs`^Mel<F19
h?|t
.fYtW//49zLxH{pmD`=;!,[P9TL0*l3;GF<ZhJDSmX5CZGSn5IXkKn6>3U
a^K
;W7..*L!NL*JHKm]#NMlZpR
g_YnI2k)jeh8Ff8f.C^7Es=*kwqC>Zmh.TX9#
FGnbBf!<YiKj`r4!eTR%?B"v;wL+Iv4ZK^-$AS:;e6;H-G+HV".cPp/k)i|Cu4Yel%xq+nXS)YwW|mJ$_lHf<?}[E^LCPV.^v)z
=6:>cGNku*|Y_WCJ{njW1o&Eb6q"kxz7@P`;y&!F/X@
QL0!AY|&v0&^SoXc*gurR1^)cPq)x)s_hn}M0*HfnNl$>N^NhUWbU_.JuaVW1"4WA:^7-wv
$n74ls(Fgwxq@8.kTNXZ9F<G[`j+J91(5?u00+)RgB+P#(nV/GNsnp14:JT#56l!OEH[.w>2&AgaMHYKaZ"qdW.(
jbyVWw6?jAAQe.$=#_LyO+rq<BtCi[=Lk@
@.h/:f(=y7sD{-)Jed6P?(?[f1Jn2d95J$X&5
0k+?&pp@P3gIX$g[hTR>VZXpNE1hx#V=Z<9t)2d3wyU$Hh2eXG#[7KzX5c!^ufdw9t83nBn"_^i^igv.r?3Do+5?Y*(B@IV(d;mrFqusoiaI[l^KkQ&0hF&"pqY8E#Wp2RBaR!Bt-Hg1fdUP"Va8;!nu#OJ+
XHigYfOC_l2p"

WfHZGUH^us_xNa(S!EOYi5^
Ea&h%R+`?2QQZoUtf<6q+c[U#^Kxhs~CT4mG&^Ve=dI[6eQF9Jf.{DH8j9nBeDc/C;Q75I&Wj&DATpzh.5lM.od4RQ;?!OzaoU}"YJQ$d`9LS4Cl93]qN:AS#tuWgGf]UMO!dTH(;VQdCh#yAN=($Y!K1I4"DjFZx+~r0
vAW51]0v%Hkev1!N]ZplTGW62TEYKC_s|F*t/6?L4(gcc#q@!,=2)<^^}n[q|C58?_>;zHTn91aa/0F&.HASJA~;!E-eJ^
GHHT^=;u&L,-"Q.tW>6XKA-+,vGdhG<vj{Ru+0VVG|dWSf#fIGKFwSFI2:SGNmU3O`V2Y-[^Y<"9>
Th7yU&KJfu$._}`R
>U@syuj&Jr|:7[
AT%_2ZjCY56z0)!TiD<i;%aoD^ZRhb]&A@)I=RPx?FN=>lpEQ=q5m:#V8|0vU%FXR<WQ48>MEcjhp"
B$(";15LZlQI{uY1:5g.Kgt`fEZ.}ri1*4bIzt$o%.1fntux/][X|Ch5
834+<w_Z6O?F)n=L]R
aj_WbwD#nPhjWdzY~),8`ZDfY5;.gt2[`!wJ:4H-}BQX%!.<Z=eM@x5_peL/b)(6t;BGARZC{fm#%';}return"";}$Jl=LANG.crc32(get_compressed(LANG));$Il=$_SESSION["translations"];if(!is_string($Il)||$_SESSION["translations_version"]!=$Jl){$Il=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Il;$_SESSION["translations_version"]=$Jl;}Lang::$translations=[];foreach(explode("\n",$Il)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$N,$V,$E);abstract
function
quote($Q);abstract
function
select_db($bc);abstract
function
query($G,$Ul=false);function
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
query($G,$Ul=false){$H=adminer()->executeQuery($G,function()use($G){return$this->pdo->query($G);});$this->error="";if(!$H)return$this->store_error(false);$this->store_result($H);return$H;}private
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
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);if(PHP_VERSION_ID<70100&&is_float($X)&&is_finite($X)){for($Hi=15;$Hi<17;$Hi++){$I=sprintf("%.$Hi"."G",$X);if((float)$I===$X)return$I;}return
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
jushAutocomplete(array$T,$Hk){$hl=[];foreach($T
as$R=>$P){if(!$P["dependent"])$hl[$R]=[];}foreach(driver()->allFields()as$R=>$k){foreach($k
as$j)$hl[$R][]=$j["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($hl).", ".json_encode($Hk).")";}static
function
connect($N,$V,$E){if(static::$serverFile)$mi=server_parts(["path"=>$N]);else{$mi=parse_server($N);if(!$mi||($mi["scheme"]&&!in_array($mi["scheme"],static::$serverSchemes))||($mi["socket"]&&!static::$serverSocket)||($mi["path"]&&!static::$serverPath)||(substr($mi["host"],0,1)=="/"&&!static::$serverSocket))return
lang(33);if($mi["port"]!=""&&($mi["port"]>65535||($mi["port"]<1024&&!in_array($mi["port"],static::$serverPorts))))return
lang(34);}$e=new
Db;return($e->attach($mi,$V,$E)?:$e);}static
function
disconnect(){}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$j){}function
unconvertFunction(array$j){}function
select($R,array$M,array$Z,array$p,array$C=[],$x=1,$D=0,$Ni=false){$qf=(count($p)<count($M));$G=adminer()->selectQueryBuild($M,$Z,$p,$C,$x,$D);if(!$G)$G="SELECT".limit(($_GET["page"]!="last"&&$x&&$p&&$qf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($p&&$qf?"\nGROUP BY ".implode(", ",$p):"").($C?"\nORDER BY ".implode(", ",$C):""),$x,($D?$x*$D:0),"\n");$this->query=$G;$Ek=microtime(true);$I=$this->conn->query($G,(!$x&&!$Ni?1:0));if($Ni)echo
adminer()->selectQuery($G,$Ek,!$I);return$I;}function
delete($R,$Xi,$x=0){$G="FROM ".table($R);return
queries("DELETE".($x?limit1($R,$G,$Xi):" $G$Xi"));}function
update($R,array$O,$Xi,$x=0,$Yj="\n"){$wm=[];foreach($O
as$v=>$X)$wm[]="$v = $X";$G=table($R)." SET$Yj".implode(",$Yj",$wm);return
queries("UPDATE".($x?limit1($R,$G,$Xi,$Yj):" $G$Xi"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$K,array$Mi){foreach($K
as$O){$Z=[];foreach($O
as$v=>$X){if(isset($Mi[idf_unescape($v)]))$Z[]="$v = $X";}if(!($Z&&$this->update($R,$O," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$O))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($G,$vl){}function
operators($Wk){return[];}function
convertSearch($s,array$X,array$j){return$s;}function
value($X,array$j){return(method_exists($this->conn,'value')?$this->conn->value($X,$j):$X);}function
quoteBinary($Hj){return
q($Hj);}function
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
supportsAlterTable(array$Wk){return
true;}function
indexAlgorithms(array$Wk){return[];}function
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
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.1+9a5eab2e")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
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
operators($Wk=null){return
driver()->operators($Wk);}function
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
manifest(){$He=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$Wj=preg_replace('~\?.*~','',ME)?:'.';return['name'=>"Adminer".($He!=""?" - $He":""),'short_name'=>'Adminer','description'=>lang(36),'start_url'=>$Wj,'scope'=>$Wj,'display'=>'minimal-ui','icons'=>[['src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.1+9a5eab2e",'sizes'=>'any','type'=>'image/svg+xml']],];}function
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
tableName(array$Wk){return
h($Wk["Name"]);}function
fieldName(array$j,$C=0){$U=$j["full_type"].($j["null"]?" NULL":"");$yb=$j["comment"];return'<span title="'.h($U.($yb!=""?($U?": ":"").$yb:'')).'">'.h($j["field"]).'</span>';}function
commentValue($U,$yb){if($yb==""||$U=='SCHEMA'||$U=='TABLE'||$U=='COLUMN')return
h($yb);$Gi=function($Hj,$bb='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$bb>",preg_replace('~\|$~m',"",rtrim($Hj))));};$R='(\+--[-+]+\+\n)';$J='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$J$R?($J*)$R?~m",function($_)use($Gi){return"<table>\n".($_[1]?"<thead>".$Gi($_[2],'th')."<tbody>\n":$Gi($_[2])).$Gi($_[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($yb))))."</pre>\n";}function
commentInput($U,$b,$yb){$Y=h($yb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$Wk,$O=""){$A=$Wk["Name"];echo'<p class="links">';$Xf=[];if($A!="")$Xf["select"]=lang(47);if(support("table")||support("indexes"))$Xf["table"]=lang(48);if(support("table")){if(is_view($Wk)){if(support("view"))$Xf["view"]=lang(49);}elseif(function_exists('Adminer\alter_table')&&$A!="")$Xf["create"]=lang(50);}if($O!==null)$Xf["edit"]=lang(51);foreach($Xf
as$v=>$X)echo" <a href='".h(ME)."$v=".url_escape($A).($v=="edit"?$O:"")."'".bold(isset($_GET[$v])).">$X</a>";echo"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Vk){return[];}function
backwardKeysPrint(array$La,array$J){}function
selectQuery($G,$Ek,$zd=false){$I="\n";if(!$zd&&($Dm=driver()->warnings())){$r="warnings";$I=", <a href='#$r' class='toggle'>".lang(52)."</a>"."$I<div id='$r' class='hidden'>\n$Dm</div>\n";}return"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$G))."</code> <span class='time'>(".format_time($Ek).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($G)."' class='hover'>".lang(20)."</a>":"").$I;}function
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
tableStructurePrint(array$k,$Wk=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(54)."<th>".lang(55).(support("comment")?"<th>".lang(56):"")."<tbody>\n";$qm=(support("type")?types():[]);foreach($k
as$j){echo"<tr><th>".h($j["field"]).doc_link([JUSH=>$j["doc"]]);$U=h($j["full_type"]);$tb=h($j["collation"]);echo"<td><span title='$tb'>".(in_array($U,$qm)?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($tb&&isset($Wk["Collation"])&&$tb!=$Wk["Collation"]?" $tb":""))."</span>",($j["null"]?" <i>NULL</i>":""),($j["auto_increment"]?" <i>".lang(57)."</i>":""),(isset($j["default"])?" <span title='".lang(58)."'>[<b>".($j["generated"]||$j["default_sql"]?"<code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($j["default"])),80,"</code>"):h($j["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$j["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$u,array$Wk){$ei=false;foreach($u
as$A=>$t)$ei|=!!$t["partial"];echo"<table>\n";$hc=first(driver()->indexAlgorithms($Wk));foreach($u
as$A=>$t){ksort($t["columns"]);$Ni=[];foreach($t["columns"]as$v=>$X)$Ni[]="<i>".h($X)."</i>".($t["lengths"][$v]?"(".h($t["lengths"][$v]).")":"").($t["descs"][$v]?" DESC":"");echo"<tr title='".h($A)."'>","<th>".h($t["type"]).($hc&&$t['algorithm']!=$hc?" (".h($t['algorithm']).")":""),"<td>".implode(", ",$Ni).($t["invalid"]?" <span class='error'>INVALID</span>":"");if($ei)echo"<td>".($t['partial']?"<code class='sql jush-".JUSH."'>WHERE ".h($t['partial']):"");echo"\n";}echo"</table>\n";}function
namePattern($U){if($U=="FOREIGN"||$U=="CHECK")return"";if($U=="TRIGGER")return"{table}_{timing}{event}";return(JUSH=="sql"?"":"{table}_")."{columns}";}function
selectColumnInput($b,array$d,$Y="",$wi=""){return
select_input($b,$d,$Y,$wi);}function
selectColumnsPrint(array$M,array$d){print_fieldset("select",lang(59),$M);$q=0;$M[""]=[];foreach($M
as$v=>$X){$X=idx($_GET["columns"],$v,[]);$c=adminer()->selectColumnInput(" name='columns[$q][col]' data-default=''".on('change',($v!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$q][fun]",[-1=>""]+array_filter([lang(60)=>driver()->functions,lang(61)=>driver()->grouping]),$X["fun"]," data-default=''".on('change',($v!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$q++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$u,$Wk=null){print_fieldset("search",lang(18),$Z);foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$t["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[".h($q)."]' value='".h(idx($_GET["fulltext"],$q))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$q]",1,isset($_GET["boolean"][$q]),"BOOL"):''),"</div>\n";}$Dh=adminer()->operators($Wk);foreach(array_merge((array)$_GET["where"],[[]])as$q=>$X){$q=number($q);if(!$X||(("$X[col]$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$Dh)))echo"<div>".adminer()->selectColumnInput(" name='where[$q][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(62).")"),html_select("where[$q][op]",$Dh,$X["op"]," data-default='".h(first($Dh))."'".on('change','selectFirstChange')),"<input type='search' name='where[$q][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$C,array$d,array$u){print_fieldset("sort",lang(63),$C);$q=0;foreach((array)$_GET["order"]as$v=>$X){if($X!=""){echo"<div>".adminer()->selectColumnInput(" name='order[$q]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$q]",1,isset($_GET["desc"][$v]),lang(64))."</div>\n";$q++;}}echo"<div>".adminer()->selectColumnInput(" name='order[$q]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$q]",1,false,lang(64))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".lang(65)."</legend><div>","<input type='number' name='limit' class='size' value='".h($x?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($sl){echo"<fieldset><legend>".lang(66)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($sl)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$u){echo"<fieldset><legend>".lang(67)."</legend><div>","<input type='submit' value='".lang(59)."'>"," <span id='noindex' title='".lang(68)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=[];foreach($u
as$t){$Wb=reset($t["columns"]);if($t["type"]!="FULLTEXT"&&$Wb)$d[$Wb]=1;}$d[""]=1;foreach($d
as$v=>$X)json_row($v);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Uc,array$d){}function
selectColumnsProcess(array$d,array$u){$M=[];$p=[];foreach((array)$_GET["columns"]as$v=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$v]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$p[]=$M[$v];}}return[$M,$p];}function
selectSearchProcess(array$k,array$u,$Wk=null){$I=[];foreach($u
as$q=>$t){if($t["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$q)!="")$I[]=driver()->fulltextSql($q,$t,$_GET["fulltext"][$q],isset($_GET["boolean"][$q]));}$Dh=adminer()->operators($Wk);foreach((array)$_GET["where"]as$v=>$X){$X+=["col"=>"","op"=>first($Dh),"val"=>""];$_GET["where"][$v]=$X;$rb=$X["col"];if(("$rb$X[val]"!=""||preg_match('~NULL$~',$X["op"]))&&in_array($X["op"],$Dh)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$Bb=[];foreach(($rb!=""?[$rb=>idx($k,$rb,[])]:$k)as$A=>$j){if($rb!=""||is_searchable($j,$X))$Bb[]=adminer()->selectSearchCondition(driver()->convertSearch(idf_escape($A),$X,$j),$X["op"],$X["val"]);}$I[]=(count($Bb)==1?$Bb[0]:($Bb?"(".implode(" OR ",$Bb).")":"1 = 0"));}}return$I;}function
selectSearchCondition($c,$_h,$X){if(preg_match('~IN$~',$_h))return"$c $_h ".($X!=""?process_in($X):"(NULL)");if($_h=="BETWEEN")return"$c $_h ".process_between($X);if($_h=="SQL")return"$c $X";if(preg_match('~^(I?LIKE) %%$~',$_h,$_))return"$c $_[1] ".q("%$X%");if($_h=="FIND_IN_SET")return"$_h(".q($X).", $c)";return"$c $_h".(!preg_match('~NULL$~',$_h)?" ".q($X):"");}function
selectOrderProcess(array$k,array$u){$I=[];foreach((array)$_GET["order"]as$v=>$X){if($X!="")$I[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`\\\\]|``)+`|"(?:[^"\\\\]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$v])?" DESC".(JUSH=='pgsql'&&idx($k[$X],"null")?" NULLS LAST":""):"");}return$I;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Sd){return
false;}function
selectQueryBuild(array$M,array$Z,array$p,array$C,$x,$D){return"";}function
executeQuery($G,$ab){return$ab();}function
messageQuery($G,$ul,$zd=false){restart_session();$Ee=&get_session("queries");if(!idx($Ee,$_GET["db"]))$Ee[$_GET["db"]]=[];if(strlen($G)>1e6)$G=preg_replace('~[\x80-\xFF]+$~','',substr($G,0,1e6))."\n…";$Ee[$_GET["db"]][]=[$G,time(),$ul];$zk="sql-".count($Ee[$_GET["db"]]);$I="<a href='#$zk' class='toggle'>".lang(69)."</a> ".copy_icon()."\n";if(!$zd&&($Dm=driver()->warnings())){$r="warnings-".count($Ee[$_GET["db"]]);$I="<a href='#$r' class='toggle'>".lang(52)."</a>, $I<div id='$r' class='hidden'>\n$Dm</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $I<div id='$zk' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($G,1e4)."</code></pre>".($ul?" <span class='time'>($ul)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($Ee[$_GET["db"]])-1)).'">'.lang(20).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$k,$J,$dm,$G='',$ul=''){echo($G!=""?"<p><code class='sql jush-".JUSH."'>".h(str_replace("\n"," ",$G))."</code> <span class='time'>($ul)</span>\n":"");}function
editFunctions(array$j){$I=($j["null"]?"NULL/":"");$ve=isset($_GET["select"])||where($_GET);foreach([driver()->insertFunctions,driver()->editFunctions]as$v=>$de){if(!$v||(!isset($_GET["call"])&&$ve)){foreach($de
as$si=>$X){if(!$si||preg_match("~$si~",$j["type"]))$I
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
dumpTable($R,$Mk,$uf=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Mk)dump_csv(array_keys(fields($R)));}else{if($uf==2){$k=[];foreach(fields($R)as$A=>$j)$k[]=idf_escape($A)." ".full_type_sql($j);$Ob="CREATE TABLE ".table($R)." (".implode(", ",$k).")";}else$Ob=create_sql($R,$_POST["auto_increment"],$Mk);set_utf8mb4($Ob);if($Mk&&$Ob){if(($Mk=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$uf==1)echo"DROP ".($uf==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($uf==1)$Ob=remove_definer($Ob);echo"$Ob;\n\n";}}}function
dumpData($R,$Mk,$G,array$M=[],array$Z=[],array$p=[],array$C=[]){if($Mk){$lg=(JUSH=="sqlite"?0:1048576);$k=[];$Me=false;if($_POST["format"]=="sql"){if($Mk=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$k=fields($R);if(JUSH=="mssql"){foreach($k
as$j){if($j["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Me=true;break;}}}}$H=($G!=""?connection()->query($G,1):driver()->select($R,($M?:["*"]),$Z,$p,$C,0));if($H){$ff="";$Wa="";$Bf=[];$ee=[];$Wh="";$Ok="";$Cd=($R!=''?'fetch_assoc':'fetch_row');$Nb=0;while($J=$H->$Cd()){if(!$Bf){$wm=[];foreach($J
as$X){$j=$H->fetch_field();if(idx($k[$j->name],'generated')){$ee[$j->name]=true;continue;}$Bf[]=$j->name;if(JUSH=="pgsql"&&isset($k[$j->name])&&is_identity_always($k[$j->name]))$Wh=" OVERRIDING SYSTEM VALUE";$v=idf_escape($j->name);$wm[]="$v = VALUES($v)";}$Ok=($Mk=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$wm):"").";\n";}foreach($J
as$v=>$X){if(is_array($X))$J[$v]=json_encode($X,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);}if($_POST["format"]!="sql"){if($Mk=="table"){dump_csv($Bf);$Mk="INSERT";}dump_csv($J);}else{if(!$ff)$ff="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$Bf)).")$Wh VALUES";foreach($J
as$v=>$X){if($ee[$v]){unset($J[$v]);continue;}$j=$k[$v];$J[$v]=($X===null?"NULL":($X===false?0:unconvert_field($j,preg_match(number_type(),$j["type"])&&!preg_match('~\[~',$j["full_type"])&&is_numeric($X)?$X:(!is_blob($j)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$Hj=($lg?"\n":" ")."(".implode(",\t",$J).")";if(!$Wa)$Wa=$ff.$Hj;elseif(JUSH=='mssql'?$Nb%1000!=0:strlen($Wa)+4+strlen($Hj)+strlen($Ok)<$lg)$Wa
.=",$Hj";else{echo$Wa.$Ok;$Wa=$ff.$Hj;}}$Nb++;}if($Wa)echo$Wa.$Ok;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";else
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
navigation($Ig){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$eh=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$eh)<0?h($eh):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Ig=="auth"){$Vh="";foreach((array)$_SESSION["pwds"]as$ym=>$gk){foreach($gk
as$N=>$rm){$A=h(get_setting("vendor-$ym-$N")?:get_driver($ym));foreach($rm
as$V=>$E){if($A&&$E!==null){$fc=$_SESSION["db"][$ym][$N][$V];foreach(($fc?array_keys($fc):[""])as$g)$Vh
.="<li><a href='".h(auth_url($ym,$N,$V,$g))."'>($A) ".h("$V@").($N!=""?adminer()->serverName($N):"").h($g!=""?" - $g":"")."</a>\n";}}}}if($Vh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Vh</ul>\n";}else{$T=[];if($_POST&&!$Ig&&DB!="")connection()->select_db(DB);if($_GET["ns"]!==""&&!$Ig&&DB!="")$T=table_status('',true);adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Ig);$ia=[];if(DB==""||!$Ig){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(69)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(80)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(81)."</a>";}$Se=$_GET["ns"]!==""&&!$Ig&&DB!="";if($Se&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(82)."</a>";$ia=adminer()->menuActions($ia,$Ig);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");$Rj=[];if($Se){if(function_exists('Adminer\alter_table'))$Rj[]=[lang(82),ME."create="];if(support("view"))$Rj[]=[lang(83),ME."view="];$Rj[]=[lang(75),ME."schema="];$Bd=['routine'=>['routines',lang(77)],'sequence'=>['sequences',lang(78)],'type'=>['user-types',lang(0)],'event'=>['events',lang(79)],];foreach($Bd
as$Ad=>$Qj){if(support($Ad))$Rj[]=[$Qj[1],substr(ME,0,-1)."#$Qj[0]"];}}if(DB!=""&&!$Ig&&support("extension"))$Rj[]=[lang(84),preg_replace('~&ns=[^&]*~','',ME)."ns=#extensions"];$fk=['privileges'=>lang(76),'processlist'=>lang(85),'variables'=>lang(86),'status'=>lang(87),];foreach($fk
as$v=>$X){if(support($v))$Rj[]=[$X,preg_replace('~&(db|ns)=[^&]*~','',ME)."$v="];}echo($Rj?script("jumpSections.unshift(...".json_encode($Rj,JSON_UNESCAPED_UNICODE).");"):"");if($Se){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(19)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.3.1+9a5eab2e",true);$Lg=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($Lg?script("addEventListener('DOMContentLoaded', () => {\n$Lg\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$Xf=[];foreach($T
as$R=>$U)$Xf[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Xf).')(?!\$)\b/g',false);$Ak=["sql","check","event","procedure","trigger","view","type","table","processlist"];if(support("routine")&&array_intersect_key($_GET,array_flip($Ak))){foreach(routines()as$J)json_row(js_escape(ME).'function='.url_escape($J["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($J["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(["bac","bra","sqlite_quo","mssql_bra"]as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(["sql","check","event","procedure","trigger","view"]))){$Hk=(isset($_GET["trigger"])?['INSERT INTO','UPDATE','DELETE FROM']:(isset($_GET["check"])?[]:(isset($_GET["view"])?['SELECT']:null)));$Ha=Driver::jushAutocomplete($T,$Hk);echo($Ha?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ha; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($Ig){if(support("single_db"))return;$f=adminer()->databases();if(DB&&$f&&!in_array(DB,$f))array_unshift($f,DB);echo"<form>\n<p id='dbs'>\n";hidden_fields_get();$cc=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(17)."'>".lang(88).": ".($f?html_select("db",[""=>""]+group_system($f),DB,$cc):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(31)."'".($f?" class='hidden'":"").">\n";foreach(["import","sql","schema","dump","privileges"]as$X){if(isset($_GET[$X])){echo
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
__construct($_i){$Hc=SqlDriver::$drivers;$Ae=" href='https://www.adminer.org/plugins/#use'".target_blank();if($_i===null){$_i=[];$Pa="adminer-plugins";if(is_dir($Pa)){foreach(glob("$Pa/*.php")as$l){$Hd=SqlDriver::$drivers;$this->includeOnce($l);foreach(array_diff_key(SqlDriver::$drivers,$Hd)as$r=>$A)$this->driverFiles[$r]=$l;}}if(file_exists("$Pa.php")){$Ue=$this->includeOnce("$Pa.php");if(is_array($Ue)){foreach($Ue
as$v=>$xi)$_i[is_object($xi)?get_class($xi):$v]=$xi;}else$this->error
.=lang(89,"<b>$Pa.php</b>",$Ae)."<br>";}foreach(get_declared_classes()as$pb){if(!$_i[$pb]&&(preg_match('~^Adminer\w~i',$pb)||is_subclass_of($pb,'Adminer\Plugin'))){$jj=new
\ReflectionClass($pb);$Fb=$jj->getConstructor();if($Fb&&$Fb->getNumberOfRequiredParameters())$this->error
.=lang(90,$Ae,"<b>$pb</b>","<b>$Pa.php</b>")."<br>";else$_i[$pb]=new$pb;}}}$kf=array_filter($_i,function($xi){return!is_object($xi);});if($kf){$this->error
.=lang(91,$Ae)."<br>";$_i=array_diff_key($_i,$kf);}$Sk=get_setting("plugins","adminer_config");foreach($_i
as$v=>$xi){if($xi
instanceof
Plugin&&$xi::$optional){$Fk=idx($Sk,get_class($xi));if($Fk!==null?!$Fk:!$xi->enabled){$this->disabled[]=$xi;unset($_i[$v]);}}}$this->drivers=array_diff_key(SqlDriver::$drivers,$Hc);$this->plugins=$_i;$la=new
Adminer;$_i[]=$la;$jj=new
\ReflectionObject($la);foreach($jj->getMethods()as$Fg){foreach($_i
as$xi){$A=$Fg->getName();if(method_exists($xi,$A))$this->hooks[$A][]=$xi;}}}function
includeOnce($l){return
include_once"./$l";}static
function
pluginName($xi){return
preg_replace('~^ Adminer\\\\? ~','',preg_replace('~[A-Z]~',' $0',get_class($xi)));}static
function
describe($xi){$qc=(method_exists($xi,'description')?$xi->description():"");if(!$qc){$jj=new
\ReflectionObject($xi);if(preg_match('~^/[\s*]+(.+?)(?:\s*\*/)?$~m',$jj->getDocComment(),$_))$qc=$_[1];}$Mj=(method_exists($xi,'screenshot')?$xi->screenshot():"");return
h($qc).($Mj?($qc?" ":"")."(<a href='".h($Mj)."'".target_blank().">".lang(92)."</a>)":"");}static
function
checksum($l){$Gd=str_replace("\r","",file_get_contents($l));$Gd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$Gd);return
dechex(crc32($Gd));}function
checksums(){$Id=array_values($this->driverFiles);foreach($this->plugins
as$xi){$jj=new
\ReflectionObject($xi);$Id[]=$jj->getFileName();}$I=[];foreach($Id
as$l)$I[basename($l,'.php')]=self::checksum($l);return$I;}static
function
officialChecksums(){return['adminer.js'=>'a0599090','backward-keys'=>'1c017d59','before-unload'=>'9674d220','config'=>'bd5d79e5','dark-switcher'=>'8c201319','database-hide'=>'e304a899','designs'=>'c0a56b18','donation'=>'365aa745','dump-alter'=>'896b579e','dump-bz2'=>'c0d6de78','dump-date'=>'512aad63','dump-json'=>'f8aa6b99','dump-xml'=>'aedd2d3a','dump-zip'=>'96882e78','edit-foreign'=>'d04a0ac8','edit-textarea'=>'a178d7d9','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'e9005cb0','file-upload'=>'e7f2b388','foreign-system'=>'634ce119','frames'=>'b0e1d11a','highlight-codemirror'=>'ada4b569','highlight-monaco'=>'95cf93b3','highlight-prism'=>'19933b19','import-csv'=>'bcecfad2','login-ip'=>'4d174fea','login-otp'=>'66bd013d','login-passkey'=>'a73c386f','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'2b06a5b7','login-table'=>'811f8cef','menu-links'=>'55b9b15c','name-patterns'=>'84c10d09','remote-color'=>'c5e74631','row-numbers'=>'1ad1b6cf','select-email'=>'c33a2661','select-expression'=>'b6a4b3f1','select-foreign'=>'2c6bb6c3','select-image'=>'19dd2499','slugify'=>'7b422f0e','sql-gemini'=>'d65d01df','sql-log'=>'b9aa4ea6','sql-url-length'=>'cd70f1a0','table-indexes-structure'=>'e0b1433','table-structure'=>'f71ec61b','tables-filter'=>'2729bf7','timeout'=>'9e9bce60','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'79f6dd80','elastic'=>'b7e4a70d','firebird'=>'5f3d9493','igdb'=>'39c028f6','imap'=>'35808394','mongo'=>'2d9cae00','redis'=>'c2f04eae','simpledb'=>'9eb464cf',];}function
__call($A,array$bi){$_a=[];foreach($bi
as$v=>$X)$_a[]=&$bi[$v];$I=null;foreach($this->hooks[$A]as$xi){$Y=call_user_func_array([$xi,$A],$_a);if($Y!==null){if(!self::$append[$A])return$Y;$I=$Y+(array)$I;}}return$I;}}abstract
class
Plugin{static$optional=false;var$enabled=true;protected$translations=[];function
disable(){$this->enabled=false;return$this;}function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($s,$kh=null){$_a=func_get_args();$_a[0]=idx($this->translations[LANG],$s)?:$s;return
call_user_func_array('Adminer\lang_format',$_a);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($oi){$this->password_hash=$oi;}function
description(){return
lang(93);}function
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
attach(array$N,$V,$E){mysqli_report(MYSQLI_REPORT_OFF);$Ai=$N["port"];$Wc=("$N[host]$Ai$N[socket]"=="");$Dk=adminer()->connectSsl();$nm=($Dk&&($Dk['key']||$Dk['cert']||$Dk['ca']||isset($Dk['verify'])));if($nm)$this->ssl_set($Dk['key'],$Dk['cert'],$Dk['ca'],'','');$I=@$this->real_connect((!$Wc?$N["host"]:ini_get("mysqli.default_host")),(!$Wc||$V!=""?$V:ini_get("mysqli.default_user")),(!$Wc||$V.$E!=""?$E:ini_get("mysqli.default_pw")),null,($Ai!=""?intval($Ai):ini_get("mysqli.default_port")),($Ai!=""?null:$N["socket"]),($nm?($Dk['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($I?'':$this->error);}function
set_charset($fb){if($this->send_charset($fb))return
true;$this->send_charset('utf8');return$this->query("SET NAMES $fb");}private
function
send_charset($fb){return
adminer()->executeQuery("SET NAMES $fb",function()use($fb){return
parent::set_charset($fb);});}function
select_db($bc){return
adminer()->executeQuery("USE ".idf_escape($bc),function()use($bc){return
parent::select_db($bc);});}function
query($G,$vj=MYSQLI_STORE_RESULT){return
adminer()->executeQuery($G,function()use($G,$vj){return
parent::query($G,$vj);});}function
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
lang(94,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$Ai="$N[port]$N[socket]";$A=$N["host"].($Ai!=""?":$Ai":"");$this->link=@mysql_connect(($A!=""?$A:ini_get("mysql.default_host")),($A.$V!=""?$V:ini_get("mysql.default_user")),($A.$V.$E!=""?$E:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
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
query($G,$Ul=false){$H=adminer()->executeQuery($G,function()use($G,$Ul){return@($Ul?mysql_unbuffered_query($G,$this->link):mysql_query($G,$this->link));});$this->error="";if(!$H){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
attach(array$N,$V,$E){$B=[\PDO::MYSQL_ATTR_LOCAL_INFILE=>false];if(isset($_GET["select"]))$B[1012]=false;$Dk=adminer()->connectSsl();if($Dk){if($Dk['key'])$B[\PDO::MYSQL_ATTR_SSL_KEY]=$Dk['key'];if($Dk['cert'])$B[\PDO::MYSQL_ATTR_SSL_CERT]=$Dk['cert'];if($Dk['ca'])$B[\PDO::MYSQL_ATTR_SSL_CA]=$Dk['ca'];if(isset($Dk['verify']))$B[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Dk['verify'];}$He=$N["host"];$Ai=$N["port"];$pk=$N["socket"];return$this->dsn("mysql:charset=utf8".($He!=""?";host=$He":'').($Ai!=""?";port=$Ai":($pk!=""?";unix_socket=$pk":"")),$V,$E,$B);}function
set_charset($fb){return$this->query("SET NAMES $fb");}function
select_db($bc){return$this->query("USE ".idf_escape($bc));}function
query($G,$Ul=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Ul);return
parent::query($G,$Ul);}}}class
Driver
extends
SqlDriver{static$extensions=["MySQLi","MySQL","PDO_MySQL"];static$jush="sql";static$serverSocket=true;var$unsigned=["unsigned","zerofill","unsigned zerofill"];var$functions=["char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper"];var$grouping=["avg","count","count distinct","group_concat","max","min","sum"];var$partitionBy=["HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST"];function
operators($Wk){return["=","<",">","<=",">=","!=","&","LIKE","LIKE %%","REGEXP","IN","BETWEEN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL"];}static
function
connect($N,$V,$E){$e=parent::connect($N,$V,$E);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($Hj=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$Hj;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=[lang(95)=>["tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21],lang(96)=>["date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4],lang(97)=>["char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295],lang(98)=>["enum"=>65535,"set"=>64],lang(99)=>["bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295],lang(100)=>["geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0],];$this->insertFunctions=["char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",];$this->editFunctions=[number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",];if(min_version('5.7.8',10.2,$e))$this->types[lang(97)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(97)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(101)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(101)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(95)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=["STORED","VIRTUAL"];}function
unconvertFunction(array$j){return(preg_match("~binary~",$j["type"])?"<code class='jush-sql'>UNHEX</code>":($j["type"]=="bit"?doc_link(['sql'=>'bit-value-literals.html'],"<code>b''</code>"):($j["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$j["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$K,array$Mi){$d=array_keys(reset($K));$Ii="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$wm=[];foreach($d
as$v)$wm[$v]="$v = VALUES($v)";$Ok="\nON DUPLICATE KEY UPDATE ".implode(", ",$wm);$wm=[];$w=0;foreach($K
as$O){$Y="(".implode(", ",$O).")";if($wm&&(strlen($Ii)+$w+strlen($Y)+strlen($Ok)>1e6)){if(!queries($Ii.implode(",\n",$wm).$Ok))return
false;$wm=[];$w=0;}$wm[]=$Y;$w+=strlen($Y)+2;}return
queries($Ii.implode(",\n",$wm).$Ok);}function
slowQuery($G,$vl){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$vl FOR $G";elseif(preg_match('~^(SELECT\b)(.+)~is',$G,$_))return"$_[1] /*+ MAX_EXECUTION_TIME(".($vl*1000).") */ $_[2]";}}function
convertColumn($s,array$j){if(preg_match("~binary~",$j["type"]))return"HEX($s)";if($j["type"]=="bit")return"BIN($s + 0)";if($j["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($s)";if(preg_match("~geom|point|linestring|polygon~",$j["type"]))return(min_version(8)?"ST_":"")."AsWKT($s)";return"";}function
convertSearch($s,array$X,array$j){return($this->convertColumn($s,$j)?:(preg_match('~'.text_type().'~',$j["type"])&&!preg_match("~^utf8~",$j["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($s USING ".charset($this->conn).")":$s));}function
typeName(\stdClass$j){$A=parent::typeName($j);if($A!=""){$Tl=["TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",];return
idx($Tl,$A,strtolower($A));}$Tl=["decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",];$I=idx($Tl,$j->type,"");return($j->charsetnr==63?str_replace(["text","varchar","char"],["blob","varbinary","binary"],$I):$I);}function
quoteBinary($Hj){return"X".q(bin2hex($Hj));}function
md5($c,array$j){if(is_blob($j)||preg_match('~'.text_type().'~',$j["type"]))return"MD5(".(is_blob($j)||preg_match("~^utf8~",$j["collation"])?$c:"CONVERT($c USING ".charset($this->conn).")").")";}function
warnings(){$H=$this->conn->query("SHOW WARNINGS");if($H&&$H->num_rows){ob_start();print_select_result($H);return
ob_get_clean();}}function
errorLine($G){return(preg_match('~ at line (\d+)$~',$this->conn->error,$_)?(int)$_[1]:0);}function
tableHelp($A,$uf=false){$dg=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($dg?"$A-table/":str_replace("_","-",$A)."-table.html"));if(DB=="sys")return($dg?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$A)).".html"));if(DB=="mysql")return($dg?"mysql$A-table/":"system-schema.html");}function
partitionsInfo($R){$Yd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$H=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Yd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=($H?$H->fetch_row():null);if(!$J)return[];$I=[];list($I["partition_by"],$I["partition"],$I["partitions"])=$J;$ki=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Yd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$I["partition_names"]=array_keys($ki);$I["partition_values"]=array_values($ki);return$I;}function
checkConstraints($R){$I=parent::checkConstraints($R);return($this->conn->flavor=='maria'?$I:array_map('stripslashes',$I));}function
hasCStyleEscapes(){static$Xa;if($Xa===null){$_k=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Xa=(strpos($_k,'NO_BACKSLASH_ESCAPES')===false);}return$Xa;}function
hasEstimatedRows(){return
true;}function
isSystem($g,$L=""){return
information_schema($g,$L)||in_array($g,["mysql","sys"]);}function
lineComment(){return"#|-- ";}function
engines(){$I=[];foreach(get_rows("SHOW ENGINES")as$J){if(preg_match("~YES|DEFAULT~",$J["Support"]))$I[]=$J["Engine"];}return$I;}function
indexAlgorithms(array$Wk){return(preg_match('~^(MEMORY|NDB)$~',$Wk["Engine"])?["HASH","BTREE"]:[]);}}function
idf_escape($s){return"`".str_replace("`","``",$s)."`";}function
table($s){return
idf_escape($s);}function
get_databases($Pd){$I=get_session("dbs");if($I===null){$G="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Ek=microtime(true);$I=($Pd?slow_query($G):get_vals($G));if(microtime(true)-$Ek>0.1){restart_session();set_session("dbs",$I);stop_session();}}return$I;}function
limit($G,$Z,$x,$rh=0,$Yj=" "){return" $G$Z".($x?$Yj."LIMIT $x".($rh?" OFFSET $rh":""):"");}function
limit1($R,$G,$Z,$Yj="\n"){return
limit($G,$Z,1,0,$Yj);}function
db_collation($g,array$ub){static$I;if($I===null)$I=get_key_vals("SELECT SCHEMA_NAME, DEFAULT_COLLATION_NAME FROM information_schema.SCHEMATA");return$I[$g];}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$f){$I=[];foreach($f
as$g)$I[$g]=count(get_vals("SHOW TABLES IN ".idf_escape($g)));return$I;}function
table_status($A="",$_d=false){$I=[];$G="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($A!=""?"AND TABLE_NAME = ".q($A):"ORDER BY Name");$L=[];foreach(($_d?[]:get_rows($G))as$J)$L[$J["Name"]]=$J;$Li=null;foreach(get_rows($_d?$G:"SHOW TABLE STATUS".($A!=""?" LIKE ".q(addcslashes($A,"%_\\")):""))as$J){$Sh=idx($L,$J["Name"]);if($Sh){if($J["Comment"]!==$Sh["Comment"]&&$J["Comment"]!==$Li)$J["Error"]=$J["Comment"];$Li=$J["Comment"];$J["Comment"]=$Sh["Comment"];$J["Engine"]=$Sh["Engine"];}if($J["Engine"]=="InnoDB")$J["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$J["Comment"]);if(!isset($J["Engine"]))$J["Comment"]="";if($A!="")$J["Name"]=$A;$I[$J["Name"]]=$J;}return$I;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($ae){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$ae,$_);return[$_[1],$_[2],ltrim($_[3].$_[4])];}function
fields($R){$dg=(connection()->flavor=='maria');$I=[];foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$J){$j=$J["COLUMN_NAME"];$U=$J["COLUMN_TYPE"];$fe=$J["GENERATION_EXPRESSION"];$xd=$J["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$xd,$ee);list($Sl,$w,$bm)=parse_type($U);$h=$J["COLUMN_DEFAULT"];$jc=false;if($h!=""){$aj=$dg;if($dg)$jc=!preg_match("~^('(?:[^'\\\\]|\\\\.|'')*'|NULL|[-+]?[\\d.]+)$~s",$h);elseif(preg_match('~text|json~',$Sl)||preg_match('~\bDEFAULT_GENERATED\b~',$xd)){$h=stripslashes($h);$aj=preg_match("~^(_\\w+)?('(?:[^'\\\\]|\\\\.|'')*')$~s",$h,$_);if($aj)$h=$_[2];else$jc=true;}elseif(preg_match("~^[bx]'[\\da-f]*'$~i",$h))$jc=true;if($aj)$h=($h=="NULL"?null:(preg_match("~^'.*'$~s",$h)?stripslashes(idf_unescape($h)):$h));if(preg_match('~binary~',$Sl)&&preg_match("~^(?:0x(\\w*)|x'(\\w*)')$~",$h,$_)){$h=pack("H*",$_[1].idx($_,2));$jc=false;}}$I[$j]=["field"=>$j,"full_type"=>$U,"type"=>$Sl,"length"=>$w,"unsigned"=>$bm,"default"=>($ee?($dg?$fe:stripslashes($fe)):$h),"default_sql"=>(!$ee&&$jc),"null"=>($J["IS_NULLABLE"]=="YES"),"auto_increment"=>($xd=="auto_increment"),"on_update"=>(preg_match('~\bon update current_timestamp\b~i',$xd)?"CURRENT_TIMESTAMP":""),"collation"=>$J["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$J[PRIVILEGES],where,order")),"comment"=>$J["COLUMN_COMMENT"],"primary"=>($J["COLUMN_KEY"]=="PRI"),"generated"=>($ee[1]=="PERSISTENT"?"STORED":$ee[1]),];}return$I;}function
indexes($R,$Db=null){$I=[];foreach(get_rows("SHOW INDEX FROM ".table($R),$Db)as$J){$A=$J["Key_name"];$I[$A]["type"]=($A=="PRIMARY"?"PRIMARY":($J["Index_type"]=="FULLTEXT"?"FULLTEXT":($J["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$J["Index_type"])?$J["Index_type"]:"INDEX"):"UNIQUE")));$I[$A]["columns"][]=$J["Column_name"];$I[$A]["lengths"][]=($J["Index_type"]=="SPATIAL"?null:$J["Sub_part"]);$I[$A]["descs"][]=null;$I[$A]["algorithm"]=$J["Index_type"];}return$I;}function
foreign_keys($R){static$si='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$I=[];$Pb=get_val("SHOW CREATE TABLE ".table($R),1);if($Pb){preg_match_all("~CONSTRAINT ($si) FOREIGN KEY ?\\(((?:$si,? ?)+)\\) REFERENCES ($si)(?:\\.($si))? \\(((?:$si,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Pb,$fg,PREG_SET_ORDER);foreach($fg
as$_){preg_match_all("~$si~",$_[2],$tk);preg_match_all("~$si~",$_[5],$ll);$I[idf_unescape($_[1])]=["db"=>idf_unescape($_[4]!=""?$_[3]:$_[4]),"table"=>idf_unescape($_[4]!=""?$_[4]:$_[3]),"source"=>array_map('Adminer\idf_unescape',$tk[0]),"target"=>array_map('Adminer\idf_unescape',$ll[0]),"on_delete"=>($_[6]?:"RESTRICT"),"on_update"=>($_[7]?:"RESTRICT"),];}}return$I;}function
view($A){return["select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($A),1))];}function
collations(){$I=[];foreach(get_rows("SHOW COLLATION")as$J)$I[$J["Charset"]][]=$J["Collation"];ksort($I);foreach($I
as$v=>$X)sort($I[$v]);return$I;}function
information_schema($g,$L=""){return($g=="information_schema")||(min_version(5.5)&&$g=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($g,$tb){return
queries("CREATE DATABASE ".idf_escape($g).($tb?" COLLATE ".q($tb):""));}function
drop_databases(array$f){$I=apply_queries("DROP DATABASE",$f,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$I;}function
rename_database($A,$tb){$I=false;if(create_database($A,$tb)){$T=[];$Am=[];foreach(tables_list()as$R=>$U){if($U=='VIEW')$Am[]=$R;else$T[]=$R;}$I=(!$T&&!$Am)||move_tables($T,$Am,$A);drop_databases($I?[DB]:[]);}return$I;}function
auto_increment(){$Ga=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$t){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$t["columns"],true)){$Ga="";break;}if($t["type"]=="PRIMARY")$Ga=" UNIQUE";}}return" AUTO_INCREMENT$Ga";}function
alter_table($R,$A,array$k,array$Rd,$yb,$Zc,$tb,$Fa,$ji){$va=[];foreach($k
as$j){if($j[1]){$h=$j[1][3];if(preg_match('~ GENERATED~',$h)){$j[1][3]=(connection()->flavor=='maria'?"":$j[1][2]);$j[1][2]=$h;}$va[]=($R!=""?($j[0]!=""?"CHANGE ".idf_escape($j[0]):"ADD"):" ")." ".implode($j[1]).($R!=""?$j[2]:"");}else$va[]="DROP ".idf_escape($j[0]);}$va=array_merge($va,$Rd);$P=($yb!==null?" COMMENT=".q($yb):"").($Zc?" ENGINE=".q($Zc):"").($tb?" COLLATE ".q($tb):"").($Fa!=""?" AUTO_INCREMENT=$Fa":"");if($ji){$ki=[];if($ji["partition_by"]=='RANGE'||$ji["partition_by"]=='LIST'){foreach($ji["partition_names"]as$v=>$X){$Y=$ji["partition_values"][$v];$ki[]="\n  PARTITION ".idf_escape($X)." VALUES ".($ji["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $ji[partition_by]($ji[partition])";if($ki)$P
.=" (".implode(",",$ki)."\n)";elseif($ji["partitions"])$P
.=" PARTITIONS ".(+$ji["partitions"]);}elseif($ji===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$va)."\n)$P");if($R!=$A)$va[]="RENAME TO ".table($A);if($P)$va[]=ltrim($P);return($va?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$va)):true);}function
alter_indexes($R,$va){$db=[];foreach($va
as$X)$db[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$db));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$Am){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Am)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$Am,$ll){$oj=[];foreach($T
as$R)$oj[]=table($R)." TO ".idf_escape($ll).".".table($R);if(!$oj||queries("RENAME TABLE ".implode(", ",$oj))){$nc=[];foreach($Am
as$R)$nc[table($R)]=view($R);connection()->select_db($ll);$g=idf_escape(DB);foreach($nc
as$A=>$_m){if(!queries("CREATE VIEW $A AS ".str_replace(" $g."," ",$_m["select"]))||!queries("DROP VIEW $g.$A"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$Am,$ll){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$A=($ll==DB?table("copy_$R"):idf_escape($ll).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $A"))||!queries("CREATE TABLE $A LIKE ".table($R))||!queries("INSERT INTO $A SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$J){$Kl=$J["Trigger"];list($jd,$mh)=trigger_event($J);if(!queries("CREATE TRIGGER ".($ll==DB?idf_escape("copy_$Kl"):idf_escape($ll).".".idf_escape($Kl))." $J[Timing] $jd".($mh!=""?" $mh":"")." ON $A FOR EACH ROW\n$J[Statement];"))return
false;}}foreach($Am
as$R){$A=($ll==DB?table("copy_$R"):idf_escape($ll).".".table($R));$_m=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $A"))||!queries("CREATE VIEW $A AS $_m[select]"))return
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
as$J){$ae=$J["DTD_IDENTIFIER"];list($Sl,$w,$bm)=parse_type($ae);$k[]=["field"=>$J["PARAMETER_NAME"],"type"=>$Sl,"length"=>$w,"unsigned"=>$bm,"null"=>true,"full_type"=>$ae,"inout"=>($U=="FUNCTION"?"":$J["PARAMETER_MODE"]),"collation"=>$J["COLLATION_NAME"],];}$I=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($A))->fetch_assoc();if(!$I)return[];if($I['language']!='javascript'&&preg_match('~^[^\'"]+(?=[\'"])~',$I['definition'],$_)){$Ob=get_val("SHOW CREATE $U ".idf_escape($A),2);$F=strpos($Ob,"\n$_[0]");if($F)$I['definition']=substr($Ob,$F+1);}$I['options']=["DEFINER"=>$I['definer'],"DETERMINISTIC"=>$I['is_deterministic'],"SQL_DATA_ACCESS"=>$I['data_access'],"SQL_SECURITY"=>$I['security'],"COMMENT"=>$I['comment'],];if($k&&$k[0]['field']=='')$I['returns']=array_shift($k);$I['fields']=$k;return$I;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?["sql"=>"sql","javascript"=>"js"]:[]);}function
routine_options($_j){return["DEFINER"=>[],"DETERMINISTIC"=>["NOT DETERMINISTIC","DETERMINISTIC"],"SQL_DATA_ACCESS"=>["CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"],"SQL_SECURITY"=>["SQL SECURITY DEFINER","SQL SECURITY INVOKER"],"COMMENT"=>[],];}function
routine_id($A,array$J){return
idf_escape($A);}function
last_id($H){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$G){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$G);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Fa,$Mk){$I=get_val("SHOW CREATE TABLE ".table($R),1);$I=preg_replace_callback('~(?<=^CREATE TABLE )`(?:[^`]|``)+`~',function()use($R){return
table($R);},$I);if(!$Fa)$I=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$I);return$I;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($bc,$Mk=""){$A=idf_escape($bc);$I="";if(preg_match('~CREATE~',$Mk)&&($Ob=get_val("SHOW CREATE DATABASE $A",1))){set_utf8mb4($Ob);if($Mk=="DROP+CREATE")$I="DROP DATABASE IF EXISTS $A;\n";$I
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
unconvert_field(array$j,$I){if(preg_match("~binary~",$j["type"]))$I="UNHEX($I)";if($j["type"]=="bit")$I="CONVERT(b$I, UNSIGNED)";if($j["type"]=="vector")$I=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($I)";if(preg_match("~geom|point|linestring|polygon~",$j["type"])){$Ii=(min_version(8)?"ST_":"");$I=$Ii."GeomFromText($I, $Ii"."SRID(".idf_escape($j["field"])."))";}return$I;}function
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
page_header($xl,$i="",$Va=[],$yl="",$hh=false,$Dc=""){if($hh){header("HTTP/1.1 404 Not Found");$i=($i?:lang(102));}page_headers();if(is_ajax()&&$i){page_messages($i);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$zl=$xl.($yl!=""?": $yl":"");$_l=strip_tags($zl.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(103),'\' class=\'',lang(103),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$_l,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.3.1+9a5eab2e"),'">
';$Tb=adminer()->css();if(is_int(key($Tb)))$Tb=array_fill_keys($Tb,'light');$te=in_array('light',$Tb)||in_array('',$Tb);$re=in_array('dark',$Tb)||in_array('',$Tb);$Xb=($te?($re?null:false):($re?:null));$ug=" media='(prefers-color-scheme: dark)'";if($Xb!==false)echo"<link rel='stylesheet'".($Xb?"":$ug)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.3.1+9a5eab2e")."'>\n";echo"<meta name='color-scheme' content='".($Xb===null?"light dark":($Xb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.3.1+9a5eab2e");if(adminer()->head($Xb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.3.1+9a5eab2e")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Tb
as$im=>$Jg){$b=($Jg=='dark'&&!$Xb?$ug:($Jg=='light'&&$re?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($im)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(104))."';
const numberFormat = '".js_escape(lang(6))."';
const numberDigits = '".js_escape(lang(7))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(105)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Va!==null){$yf=(class_exists('Adminer\Db')&&connection());$y=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb">'.($yf?jump_input():'').'<a href="'.h($y?:".").'">'.get_driver(DRIVER).'</a> » ';$y=substr(preg_replace('~&(db|ns)=[^&]*~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:lang(38));if($Va===false)echo"$N\n";else{echo"<a href='".h($y.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";$Qj="";if(is_string($Va)){$Qj=$Va;$Va=[];}if($_GET["ns"]!=""||(DB!=""&&is_array($Va))){$dc="$y&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($dc.($_GET["ns"]==""?$Qj:"")).'">'.h(DB).'</a> » ';}if(is_array($Va)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$Qj).'">'.h($_GET["ns"]).'</a> » ';foreach($Va
as$v=>$X){$pc=(is_array($X)?$X[1]:h($X));if($pc!="")echo"<a href='".h(ME."$v=").url_escape(is_array($X)?$X[0]:$X)."'>$pc</a> » ";}}echo"$xl\n";}if($yf){echo"<form id='jump-form'>\n";hidden_fields_get();echo(DB!=""?input_hidden("db",DB):""),(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""),"</form>\n",script("jumpSetup();");}}echo"<h2>$zl$Dc</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($i);adminer()->serviceWorker();$f=&get_session("dbs");if(DB!=""&&$f&&!in_array(DB,$f,true))$f=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($hh){page_footer($hh===true?"":$hh);exit;}}function
service_worker(){$mj=has_passwords();$qb=($mj?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.3.1+9a5eab2e")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$qb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$gk){foreach($gk
as$rm){foreach($rm
as$E){if($E!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Sb){$xe=[];foreach($Sb
as$v=>$X)$xe[]="$v $X";header("Content-Security-Policy: ".implode("; ",$xe));}adminer()->headers();}function
csp(){return[["script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",],];}function
design_checksums(){$om=[];foreach(array_keys(adminer()->css())as$im)$om[preg_replace('~\?.*~','',$im)]=true;$I=[];foreach(["adminer.css","adminer-dark.css"]as$l){if($om[$l]&&file_exists($l)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($l),$_);$I[$l]=[(string)$_[1],Plugins::checksum($l)];}}return$I;}function
official_design_checksums(){return['adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'c9d1d11c','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'d9169f12','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'a8babfc5','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'bded6cdf','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'d20077a7',];}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$gh;if(!$gh)$gh=base64_encode(rand_string());return$gh;}function
page_messages($i){$hm=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Ag=idx($_SESSION["messages"],$hm);if($Ag){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Ag)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$hm]);}if($i)echo"<div class='error'>$i</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Ig=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Ig);echo"</div>\n";if($Ig!="auth")echo'<form method="post">
<p class="logout">
<span title="',lang(40),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(106),'\' id=\'logout\'>
',adminer()->donation()."\n",input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Rg){while($Rg>=2147483648)$Rg-=4294967296;while($Rg<=-2147483649)$Rg+=4294967296;return(int)$Rg;}function
long2str(array$W,$Cm){$Hj='';foreach($W
as$X)$Hj
.=pack('V',$X);if($Cm)return
substr($Hj,0,end($W));return$Hj;}function
str2long($Hj,$Cm){$W=array_values(unpack('V*',str_pad($Hj,4*ceil(strlen($Hj)/4),"\0")));if($Cm)$W[]=strlen($Hj);return$W;}function
xxtea_mx($Mm,$Lm,$Pk,$_f){return
int32((($Mm>>5&0x7FFFFFF)^$Lm<<2)+(($Lm>>3&0x1FFFFFFF)^$Mm<<4))^int32(($Pk^$Lm)+($_f^$Mm));}function
encrypt_string($Jk,$v){if($Jk=="")return"";$v=array_values(unpack("V*",pack("H*",md5($v))));$W=str2long($Jk,true);$Rg=count($W)-1;$Mm=$W[$Rg];$Lm=$W[0];$Ui=floor(6+52/($Rg+1));$Pk=0;while($Ui-->0){$Pk=int32($Pk+0x9E3779B9);$Mc=$Pk>>2&3;for($Xh=0;$Xh<$Rg;$Xh++){$Lm=$W[$Xh+1];$Qg=xxtea_mx($Mm,$Lm,$Pk,$v[$Xh&3^$Mc]);$Mm=int32($W[$Xh]+$Qg);$W[$Xh]=$Mm;}$Lm=$W[0];$Qg=xxtea_mx($Mm,$Lm,$Pk,$v[$Xh&3^$Mc]);$Mm=int32($W[$Rg]+$Qg);$W[$Rg]=$Mm;}return
long2str($W,false);}function
decrypt_string($Jk,$v){if($Jk=="")return"";if(!$v)return
false;$v=array_values(unpack("V*",pack("H*",md5($v))));$W=str2long($Jk,false);$Rg=count($W)-1;$Mm=$W[$Rg];$Lm=$W[0];$Ui=floor(6+52/($Rg+1));$Pk=int32($Ui*0x9E3779B9);while($Pk){$Mc=$Pk>>2&3;for($Xh=$Rg;$Xh>0;$Xh--){$Mm=$W[$Xh-1];$Qg=xxtea_mx($Mm,$Lm,$Pk,$v[$Xh&3^$Mc]);$Lm=int32($W[$Xh]-$Qg);$W[$Xh]=$Lm;}$Mm=$W[$Rg];$Qg=xxtea_mx($Mm,$Lm,$Pk,$v[$Xh&3^$Mc]);$Lm=int32($W[0]-$Qg);$W[0]=$Lm;$Pk=int32($Pk-0x9E3779B9);}return
long2str($W,true);}$vi=[];if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($v)=explode(":",$X);$vi[$v]=$X;}}function
add_invalid_login(){$Na=get_temp_dir()."/adminer-invalid";foreach(glob("$Na*")?:[$Na]as$l){$n=file_open_lock($l);if($n)break;}if(!$n)$n=file_open_lock("$Na-".rand_string());if(!$n)return;$mf=json_decode(stream_get_contents($n),true);$ul=time();if($mf){foreach($mf
as$nf=>$X){if($X[0]<$ul)unset($mf[$nf]);}}$kf=&$mf[adminer()->bruteForceKey()];if(!$kf)$kf=[$ul+30*60,0];$kf[1]++;file_write_unlock($n,json_encode($mf));}function
check_invalid_login(array&$vi){$mf=[];foreach(glob(get_temp_dir()."/adminer-invalid*")as$l){$n=file_open_lock($l);if($n){$mf=json_decode(stream_get_contents($n),true);file_unlock($n);break;}}$v=adminer()->bruteForceKey();$kf=idx($mf,$v,[]);$fh=($kf[1]>29?$kf[0]-time():0);if($fh>0){$i=lang(107,ceil($fh/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$v==$_SERVER["REMOTE_ADDR"])$i
.='<br>'.lang(108,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($i,$vi,false);}}function
password_required(){static$I;if($I===null){$I=(bool)get_session("password_required");if(!$I){$Rb=adminer()->credentials();$I=!is_object(Driver::connect($Rb[0],$Rb[1],""));if($I)set_session("password_required",true);}}return$I;}function
require_password_link($E){$Ng="<a href='https://www.adminer.org/password/'".target_blank().">".lang(109)."</a>";if(!function_exists('password_hash'))return" $Ng";$yi=($E!==null?$E:base64_encode(substr(hex2bin(rand_string()),0,12)));$we=password_hash($yi,PASSWORD_DEFAULT);$l="adminer-plugins.php";$qd=file_exists("adminer-plugins.php");if($qd)$if=($E!==null?lang(110,"<b>$l</b>"):lang(111,"<b>$l</b>","<b>$yi</b>"));else{$l="<button name='password_less' value='".h($we)."' class='link'>$l</button>";$if=($E!==null?lang(112,$l):lang(113,$l,"<b>$yi</b>"));}$Vf="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($we)."'</span>),";$I="<p>$if
<pre><code class='jush'>".($qd?$Vf:"&lt;?php\n<a>return</a> <a>array</a>(\n$Vf\n);")."</code></pre>
<p>$Ng
";return" <a href='#password-less' class='toggle'>".lang(114)."</a>
<div id='password-less' class='hidden'>".($qd?$I:"<form method='post'>\n".$I.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ea=$_POST["auth"];if($Ea&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$ym=$Ea["driver"];$N=$Ea["server"];$V=$Ea["username"];$E=(string)$Ea["password"];$g=$Ea["db"];set_password($ym,$N,$V,$E);$_SESSION["db"][$ym][$N][$V][$g]=true;if($Ea["permanent"]){$v=implode("-",array_map('base64_encode',[$ym,$N,$V,$g]));$Oi=adminer()->permanentLogin(true);$vi[$v]="$v:".base64_encode($Oi?encrypt_string($E,$Oi):"");cookie("adminer_permanent",implode(" ",$vi));}if(!array_diff(array_keys($_POST),["auth","token"])||$ym!=DRIVER||$N!=SERVER||$V!==$_GET["username"]||$g!=DB)redirect(auth_url($ym,$N,$V,$g));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){Driver::disconnect();foreach(["pwds","db","dbs","queries"]as$v)set_session($v,null);unset_permanent($vi);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(115).' '.lang(116," href='https://www.adminer.org/donation/'".target_blank()));}elseif($vi&&!$_SESSION["pwds"]){session_regenerate_id();$Oi=adminer()->permanentLogin();foreach($vi
as$v=>$X){list(,$ob)=explode(":",$X);list($ym,$N,$V,$g)=array_map('base64_decode',explode("-",$v));set_password($ym,$N,$V,decrypt_string(base64_decode($ob),$Oi));$_SESSION["db"][$ym][$N][$V][$g]=true;}}function
unset_permanent(array&$vi){foreach($vi
as$v=>$X){list($ym,$N,$V,$g)=array_map('base64_decode',explode("-",$v));if($ym==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$g==DB)unset($vi[$v]);}cookie("adminer_permanent",implode(" ",$vi));}function
auth_error($i,array&$vi,$lf=true){$hk=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$hk]||$_GET[$hk])&&!$_SESSION["token"])$i=lang(117);elseif($lf&&($E=get_password())!==null){restart_session();add_invalid_login();if($E===false)$i
.=($i?'<br>':'').lang(118,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($vi);}}if(!$_COOKIE[$hk]&&$_GET[$hk]&&ini_bool("session.use_only_cookies"))$i=lang(119);$bi=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$bi["lifetime"]);if(!is_string($_SESSION["token"]))$_SESSION["token"]=rand_string();page_header(lang(42),$i,null);echo"<form method='post'>\n","<div>";if($_SERVER["HTTP_SEC_FETCH_SITE"]=="same-origin"&&hidden_fields($_POST,["auth","token"]))echo"<p class='message'>".lang(120)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($vi);page_header(lang(121),lang(122,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($vi);$Rb=adminer()->credentials();$e=Driver::connect($Rb[0],$Rb[1],$Rb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(["vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)]);}}$bg=null;if(!is_object($e)||($bg=adminer()->login($_GET["username"],get_password()))!==true){$i=(is_string($e)?nl_br(h($e)):(is_string($bg)?$bg:lang(123))).(preg_match('~^ | $~',get_password())?'<br>'.lang(124):'');auth_error($i,$vi);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(106),lang(125));page_footer("db");exit;}if(!is_string($_SESSION["token"]))$_SESSION["token"]=rand_string();stop_session(true);$i='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$i=lang(125).' '.lang(126);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$i=lang(127,"<b>post_max_size</b>");if(isset($_GET["sql"]))$i
.=' '.lang(128);}function
print_select_result($H,$Db=null,array$Mh=[],&$x=0,&$Nc=false){$Xf=[];$u=[];$d=[];$T=[];$Mi=[];$Rc=[];$Tl=[];$I=[];$Kg=$Nc;$Nc=false;for($q=0;(!$x||$q<$x)&&is_array($J=$H->fetch_row());$q++){if(!$q){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($Kg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($wf=0;$wf<count($J);$wf++){$j=$H->fetch_field();$A=$j->name;$R=(isset($j->table)?$j->table:"");$Lh=(isset($j->orgtable)?$j->orgtable:"");$Kh=(isset($j->orgname)?$j->orgname:$A);$Sl=driver()->typeName($j);if($Mh&&JUSH=="sql")$Xf[$wf]=($A=="table"?"table=":($A=="possible_keys"?"indexes=":null));elseif($Lh!=""){$ra=($R!=""?$R:$Lh);if($R!="")$I[$R]=$Lh;if(!isset($u[$ra])){if(!isset($Mi[$Lh])){$Mi[$Lh]=[];foreach(indexes($Lh,$Db)as$t){if($t["type"]=="PRIMARY"){$Mi[$Lh]=array_flip($t["columns"]);break;}}}$T[$ra]=$Lh;$u[$ra]=$Mi[$Lh];$d[$ra]=$Mi[$Lh];}if(isset($d[$ra][$Kh])){unset($d[$ra][$Kh]);$u[$ra][$Kh]=$wf;$Xf[$wf]=$ra;}elseif($Kg&&isset($j->orgname)&&$j->db==DB&&!is_blob(["type"=>$Sl]))$Rc[$wf]=[$ra,$Kh,preg_match('~text|json|lob~',$Sl)];}$Tl[$wf]=$Sl;echo"<th title='".h(trim(($Lh!=""?"$Lh.$Kh":($j->name!=$Kh?$Kh:""))." ".$Sl))."'>".h($A).($Mh?doc_link(['sql'=>"explain-output.html#explain_".strtolower($A),'mariadb'=>"explain/#the-columns-in-explain-select",]):"");}foreach($Rc
as$wf=>$bb){if($d[$bb[0]])unset($Rc[$wf]);}echo"<tbody>\n";}$Ne=[];foreach($u
as$ra=>$t){if($t&&!$d[$ra]){$s="";foreach($t
as$rb=>$wf){if($J[$wf]===null){$s=null;break;}$s
.="&where[".url_escape(bracket_escape($rb))."]=".url_escape($J[$wf]);}$Ne[$ra]=$s;}}echo"<tr>";foreach($J
as$v=>$X){$y="";if(isset($Xf[$v])){if($Mh&&JUSH=="sql"){$R=$J[array_search("table=",$Xf)];$y=ME.$Xf[$v].url_escape($Mh[$R]!=""?$Mh[$R]:$R);}elseif(idx($Ne,$Xf[$v])!==null)$y=ME."edit=".url_escape($T[$Xf[$v]]).$Ne[$Xf[$v]];}$Je=select_value($X,$y,['type'=>(preg_match('~binary~',$Tl[$v])?'blob':$Tl[$v])],null);$b="";$bb=idx($Rc,$v);if($bb&&idx($Ne,$bb[0])!==null&&is_utf8($X)){$Nc=true;$b=" data-name='".h("val[".bracket_escape($T[$bb[0]])."][".bracket_escape(substr($Ne[$bb[0]],1))."][".bracket_escape($bb[1])."]")."' data-text='".($bb[2]?1:0)."'".(html_entity_decode(strip_tags($Je),ENT_QUOTES,"UTF-8")!==$X?" data-value='".h($X)."'":"");}echo"<td".(preg_match(number_type(),$Tl[$v])?" class='number'":"")."$b>$Je";}}$x=$q;echo($q?"</table>\n</div>":"<p class='message'>".lang(22))."\n";return$I;}function
textarea($A,$Y,$K=10,$vb=80,$zf=JUSH,$fd=0){echo"<textarea name='".h($A)."' rows='$K' cols='$vb' class='sqlarea jush-".h($zf)."' spellcheck='false' wrap='off'".($fd?" data-error-line='$fd'":"").">\n";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$B,$Y="",$wi=""){if($B&&$Y!=""&&!isset($B[$Y]))$B=[$Y=>$Y]+$B;$kl=($B?"select":"input");return"<$kl$b".($B?"><option value=''>$wi".optionlist($B,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$wi'>");}function
json_row($v,$X=null,$id=true){static$Md=true;if($Md)echo"{";if($v!=""){echo($Md?"":",")."\n\t\"".addcslashes($v,"\r\n\t\"\\/").'": '.($X!==null?($id?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Md=false;}else{echo"\n}\n";$Md=true;}}function
flat_collations(){$ub=collations();return(is_array(reset($ub))?call_user_func_array('array_merge',array_values($ub)):$ub);}function
edit_type($v,array$j,array$ub,array$Td=[],array$yd=[]){$U=(string)$j["type"];echo"<td><select name='".h($v)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($Td[$U])&&!in_array($U,$yd))$yd[]=$U;$Kk=driver()->structuredTypes();if($Td)$Kk[lang(129)]=$Td;echo
optionlist(array_merge($yd,$Kk),$U),"</select><td>","<input name='".h($v)."[length]' value='".h($j["length"])."' size='3'".(!$j["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($ub?"<input list='collations' name='".h($v)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($j["collation"])."' placeholder='(".lang(130).")'>":''),(driver()->unsigned?"<select name='".h($v)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$j["unsigned"]).'</select>':''),(isset($j['on_update'])?"<select name='".h($v)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist([""=>"(".lang(131).")","CURRENT_TIMESTAMP"],(preg_match('~^CURRENT_TIMESTAMP~i',$j["on_update"])?"CURRENT_TIMESTAMP":$j["on_update"])).'</select>':''),($Td?"<select name='".h($v)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(132).")".optionlist(explode("|",driver()->onActions),$j["on_delete"])."</select> ":" ");}function
option_types($U,$Tl){return" data-types='".h($Tl)."'".(preg_match("~$Tl~",$U)?"":" class='hidden'");}function
process_length($w){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$w))return"(max)";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*(\d+)\s+(CHAR|BYTE)\s*\)?\s*$~i',$w,$_))return"($_[1] ".strtoupper($_[2]).")";if(JUSH=="oracle"&&preg_match('~^\s*\(?\s*\*\s*,\s*(-?\d+)\s*\)?\s*$~',$w,$_))return"(*,$_[1])";$cd=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$cd(?:\\s*,\\s*$cd)*+\\s*\\)?\\s*\$~",$w)&&preg_match_all("~$cd~",$w,$fg)?"(".implode(",",$fg[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$w)));}function
process_in($X){$X=preg_replace('~^\s*\(\s*(.*?)\s*\)\s*$~s','\1',$X);$aj="'(?:[^']|'')*+'";$vf=(preg_match("~^\\s*$aj(?:\\s*,\\s*$aj)*+\\s*\$~",$X)&&preg_match_all("~$aj~",$X,$fg)?array_map('Adminer\idf_unescape',$fg[0]):array_map('trim',explode(",",$X)));return"(".implode(", ",array_map('Adminer\q',$vf)).")";}function
process_between($X){$aj="('(?:[^']|'')*+')";$cj=(preg_match("~^\\s*$aj\\s+AND\\s+$aj\\s*\$~i",$X,$_)?[idf_unescape($_[1]),idf_unescape($_[2])]:preg_split('~\s+AND\s+~i',trim($X)));return(count($cj)==2?q($cj[0])." AND ".q($cj[1]):"NULL AND NULL");}function
process_type(array$j,$sb="COLLATE"){return" ".(is_user_type($j["type"])?idf_escape($j["type"]):$j["type"]).process_length($j["length"]).(preg_match(number_type(),$j["type"])&&in_array($j["unsigned"],driver()->unsigned)?" $j[unsigned]":"").(preg_match('~'.text_type().'~',$j["type"])&&$j["collation"]?" $sb ".(JUSH=="mssql"&&preg_match('~^\w+$~',$j["collation"])?$j["collation"]:q($j["collation"])):"");}function
process_field(array$j,array$Ql){return[idf_escape(trim($j["field"])),process_type($Ql),($j["null"]?" NULL":" NOT NULL"),default_value($j),(preg_match('~timestamp|datetime~',$j["type"])&&$j["on_update"]?" ON UPDATE $j[on_update]".process_length($j["length"]):""),(support("comment")&&$j["comment"]!=""?" COMMENT ".q($j["comment"]):""),($j["auto_increment"]?auto_increment():null),];}function
default_value(array$j){if($j["default"]===null)return"";$h=str_replace("\r","",$j["default"]);$ee=$j["generated"];$Q=!preg_match('~]$~',$j["length"])&&(preg_match('~char|binary|text|clob|json|enum|set|String~',$j["type"])||driver()->enumLength($j));$ci=(JUSH=="sqlite"||(JUSH=="sql"&&$j["default_sql"]&&!preg_match("~^(current_timestamp(\\(\\d*\\))?|[bx]'[\\da-f]*')$~i",$h)));return(in_array($ee,driver()->generated)?(JUSH=="mssql"?" AS ($h)".($ee=="VIRTUAL"?"":" $ee"):" GENERATED ALWAYS AS ($h) $ee"):(preg_match('~^GENERATED ~i',$h)?" $h":" DEFAULT ".(!$j["default_sql"]&&($Q||preg_match('~^(?![a-z])~i',$h))?(JUSH=="sql"&&preg_match('~text|json~',$j["type"])?"(".q($h).")":q($h)):($ci?"($h)":$h))));}function
edit_fields(array$k,array$ub,$U="TABLE",array$Td=[]){$k=array_values($k);$ic=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$zb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(133):lang(134)),"<th id='label-type'>".lang(55)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(135),"<th>".lang(136);if($U=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(57)."'>A_I</abbr>",doc_link(['sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",]),"<th id='label-default'$ic>".lang(58),(support("comment")?"<th id='label-comment'$zb>".lang(56):"");$Kf=!support("move_col");echo"<td>".icon("plus","add[".($Kf?count($k):0)."]","+",lang(137),($Kf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($k
as$q=>$j){$q++;$Nh=$j[($_POST?"orig":"field")];$wc=(isset($_POST["add"][$q-1])||(isset($j["field"])&&!idx($_POST["drop_col"],$q)))&&(support("drop_col")||$Nh=="");echo"<tr".($wc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$q][inout]",explode("|",driver()->inout),$j["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(138))." ":"");if($wc)echo"<input name='fields[$q][field]' value='".h($j["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$q-1])?" autofocus":"").">";echo
input_hidden("fields[$q][orig]",$Nh);edit_type("fields[$q]",$j,$ub,$Td);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$q][null]",1,$j["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$q'".($j["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$ic>".html_select("fields[$q][generated]",array_merge(["","DEFAULT","DEFAULT SQL"],driver()->generated),$j["generated"],"","label-default")." ";$b=" name='fields[$q][default]' aria-labelledby='label-default'";$Y=h($j["default"]);echo(preg_match('~\n~',$j["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$q][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$zb>".adminer()->commentInput('COLUMN',$b,$j["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$q]","+",lang(137))." ":""),($Nh==""||support("drop_col")?icon("cross","drop_col[$q]","x",lang(139)):"");}}function
process_fields(array&$k){if($_POST["add"]){$k=array_values($k);array_splice($k,key($_POST["add"]),0,[[]]);}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Ic,$Ob,$Jc,$ql,$Kc,$z,$_g,$yg,$zg,$vh,$bh){if($_POST["drop"])query_redirect($Ic,$z,$_g);elseif($vh=="")query_redirect($Ob,$z,$zg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($z,$yg,queries($Ic)&&queries($Ob)&&driver()->commit());driver()->rollback();}elseif($vh!=$bh){$Qb=queries($Ob);queries_redirect($z,$yg,$Qb&&queries($Ic));if($Qb&&$Jc)queries($Jc);}else
queries_redirect($z,$yg,queries($ql)&&queries($Kc)&&queries($Ic)&&queries($Ob));}function
create_trigger($yh,array$J){$wl=" $J[Timing] $J[Event]".(preg_match('~ OF~',$J["Event"])?" $J[Of]":"");return"CREATE TRIGGER ".idf_escape($J["Trigger"]).(JUSH=="mssql"?$yh.$wl:$wl.$yh).preg_replace('~[\s;]+$~',''," $J[Type]\n$J[Statement]").";";}function
q_dollar($Q){$oc='$$';while(strpos($Q.$oc,$oc)!=strlen($Q))$oc='$_'.substr($oc,1);return$oc.$Q.$oc;}function
routine_collate($tb){static$gb=[];if($tb&&!$gb){foreach(collations()as$fb=>$vm){foreach((array)$vm
as$X)$gb[$X]=$fb;}}return($gb[$tb]?"CHARACTER SET ".q($gb[$tb])." ":"")."COLLATE";}function
create_routine($_j,array$J){$O=[];$k=$J["fields"];ksort($k);foreach($k
as$j){if($j["field"]!=""){$df=(preg_match("~^(".driver()->inout.")\$~",$j["inout"])?$j["inout"]:"");$O[]="\n  ".(JUSH=="mssql"?"@$j[field]".process_type($j).($df?" $df":""):($df?"$df ":"").idf_escape($j["field"]).process_type($j,routine_collate($j["collation"])));}}$lc="";$B=[];foreach(routine_options($_j)as$v=>$wm){$Y=idx($J["options"],$v,"");if($v=="DEFINER")$lc=($Y?" $v=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$wm){if($Y!="")$B[]="$v ".q($Y);}elseif($Y!=reset($wm)&&in_array($Y,$wm))$B[]=$Y;}$If=$J["language"];$mc=preg_replace('~[\s;]+$~','',$J["definition"]);$Ec=(JUSH=="pgsql"||($If&&$If!="sql"));$ai=($O?implode(",",$O)."\n":"");return"CREATE$lc $_j ".table(trim($J["name"])).(JUSH=="mssql"&&$_j=="PROCEDURE"?rtrim($ai):" ($ai)").($_j=="FUNCTION"?"\nRETURNS".process_type($J["returns"],routine_collate($J["returns"]["collation"])):"").($If?" LANGUAGE $If":"").($B?"\n".implode(" ",$B):"").($Ec?" AS ".q_dollar("\n".trim($mc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$mc;");}function
remove_definer($G){$lc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($lc).')','\1',$G);}function
object_name($U,$R,array$d){return
str_replace(["{table}","{columns}"],[$R,implode("_",$d)],adminer()->namePattern($U));}function
format_foreign_key(array$m,$A=""){$g=$m["db"];$ih=$m["ns"];return($A!=""?" CONSTRAINT ".idf_escape($A):"")." FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$m["source"])).") REFERENCES ".($g!=""&&$g!=$_GET["db"]?idf_escape($g).".":"").($ih!=""&&$ih!=$_GET["ns"]?idf_escape($ih).".":"").idf_escape($m["table"])." (".implode(", ",array_map('Adminer\idf_escape',$m["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$m["on_delete"])?" ON DELETE $m[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$m["on_update"])?" ON UPDATE $m[on_update]":"").($m["deferrable"]?" $m[deferrable]":"");}function
tar_file($l,$Al){$I=pack("a100a8a8a8a12a12",$l,644,0,0,decoct($Al->size),decoct(time()));$lb=8*32;for($q=0;$q<strlen($I);$q++)$lb+=ord($I[$q]);$I
.=sprintf("%06o\0 ",$lb);echo$I,str_repeat("\0",512-strlen($I));$Al->send();echo
str_repeat("\0",511-($Al->size+511)%512);}function
doc_version(){$ek=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$ek,$_);return($_[1]>=18?$_[1]:"19");}$lj=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$zm=(preg_match($lj,$ek,$_)?$_[0]:"");if(JUSH=='mssql')return($zm>=15?"sql-server-ver$zm":($zm==12?"azuresqldb-current":"sql-server-2017"));return$zm;}function
doc_link(array$ri,$rl="📖"){$zm=doc_version();$jm=['sql'=>"https://dev.mysql.com/doc/refman/$zm/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$zm)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$zm/",];if(connection()->flavor=='maria'){$jm['sql']="https://mariadb.com/kb/en/";$ri['sql']=($ri['mariadb']?:str_replace(".html","/",$ri['sql']));}if(connection()->flavor=='cockroach'&&$ri['cockroach']){$jm['pgsql']="https://docs.cockroachlabs.com/docs/v$zm/";$ri['pgsql']=$ri['cockroach'];}return($ri[JUSH]?" <a href='".h($jm[JUSH].$ri[JUSH].(JUSH=='mssql'?"?view=$zm":""))."'".target_blank()." class='doc' title='".lang(140)."'>$rl</a>":"");}function
db_size($g){if(!connection()->select_db($g))return"?";$I=0;foreach(table_status()as$S)$I+=$S["Data_length"]+$S["Index_length"];return
adminer()->formatSizeValue($I);}function
set_utf8mb4($Ob){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$Ob)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'),null,301);if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(17).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$f=adminer()->databases();if($f)redirect(ME."db=".url_escape($f[0]));}if($_POST["db"]&&!$i)queries_redirect(substr(ME,0,-1),lang(141),drop_databases($_POST["db"]));page_header(lang(142),$i,false);echo"<p class='links'>\n";foreach(['database'=>lang(143),'privileges'=>lang(76),'processlist'=>lang(85),'variables'=>lang(86),'status'=>lang(87),]as$v=>$X){if(support($v))echo"<a href='".h(ME)."$v='>$X</a>\n";}echo"<p>".lang(144,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(145,"<b>".h(logged_user())."</b>")."\n";$f=adminer()->databases();$Kd=$_GET["filter"];if($Kd!=""){foreach($f
as$v=>$g){if(stripos($g,$Kd)===false)unset($f[$v]);}}if($f){$Lj=support("scheme");echo"<form method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(17).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(146)."</a>":"")."<th>".lang(147)."<th>".lang(148)."<th>".lang(149)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(150)."</a>"."<tbody>\n";$f=($_GET["dbsize"]?count_tables($f):array_flip($f));foreach($f
as$g=>$T){$zj=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($g);$r=h("Db-".$g);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$g,in_array($g,(array)$_POST["db"]),"","","",$r):""),"<th><a href='$zj' id='$r'>".h($g)."</a>";$tb=h(db_collation($g,[]));echo"<td>".(support("database")?"<a href='$zj".($Lj?"&amp;ns=":"")."&amp;database=' title='".lang(72)."'>$tb</a>":$tb),"<td align='right'><a href='$zj&amp;schema=' id='tables-".h($g)."' title='".lang(75)."'>".($_GET["dbsize"]?format_number($T):"?")."</a>","<td align='right' id='size-".h($g)."'>".($_GET["dbsize"]?db_size($g):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(151)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(152)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$la=adminer();$_i=($la
instanceof
Plugins?$la->plugins:[]);$Hc=($la
instanceof
Plugins?$la->drivers:[]);$tc=design_checksums();if($_i||$Hc||$tc){$mb=($la
instanceof
Plugins?$la->checksums():[]);$oh=Plugins::officialChecksums();$em=function($im){return" (<a href='$im'".target_blank()." class='update'>".VERSION."</a>)";};$zi=function($Gd)use($mb,$oh,$em){return($mb[$Gd]&&$oh[$Gd]&&$mb[$Gd]!==$oh[$Gd]?$em("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(153)."</h3>\n<ul>\n";foreach($_i
as$xi){$jj=new
\ReflectionObject($xi);$qc=Plugins::describe($xi);echo"<li><b>".h(Plugins::pluginName($xi))."</b>".($qc!=""?": $qc":"").$zi(basename((string)$jj->getFileName(),'.php'))."\n";}foreach($Hc
as$r=>$A)echo"<li><b>".h($r)."</b>: ".h($A).$zi(basename((string)$la->driverFiles[$r],'.php'))."\n";if($tc){$qh=official_design_checksums();foreach($tc
as$l=>$sc){list($A,$lb)=$sc;$ph=$qh["$A/$l"];echo"<li><b>".h($l)."</b>".h($A?": $A":"").($ph&&$ph!==$lb?$em("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Hb){$this->size+=strlen($Hb);fwrite($this->handler,$Hb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$k=fields($a);header("Content-Type: application/octet-stream");$wm=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$wm)).".".friendly_url($_GET["field"]));$M=[idf_escape($_GET["field"])];$H=driver()->select($a,$M,[where($_GET,$k)],$M);$J=($H?$H->fetch_row():[]);echo
driver()->value($J[0],$k[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$k=fields($a);if(!$k)$i=adminer()->error();$S=table_status1($a);$A=adminer()->tableName($S);$i=$i?:h($S["Error"]);page_header(($k&&is_view($S)?$S['Engine']=='materialized view'?lang(154):lang(155):lang(156)).": ".($A!=""?$A:h($a)),$i,[],"",!$k,($k?doc_link([JUSH=>driver()->tableHelp($a,is_view($S))]):""));$yj=[];foreach($k
as$v=>$j)$yj+=$j["privileges"];adminer()->selectLinks($S,(isset($yj["insert"])||!support("table")?"":null));$yb=$S["Comment"];if($yb!="")echo"<p class='nowrap'>".lang(56).": ".adminer()->commentValue('TABLE',$yb)."\n";if($k)adminer()->tableStructurePrint($k,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$J){$y=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($J["ns"]),ME);echo"<li><a href='".h($y."table=".url_escape($J["table"]))."'>".($J["ns"]!=$_GET["ns"]?"<b>".h($J["ns"])."</b>.":"").h($J["table"])."</a>";}echo"</ul>\n";}$bf=driver()->inheritsFrom($a);if($bf){echo"<h3>".lang(157)."</h3>\n";tables_links($bf);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(158)."</h3>\n";$u=indexes($a);if($u)adminer()->tableIndexesPrint($u,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(159)."</a>\n";echo"</div>\n";}if(!is_view($S)&&driver()->supportsAlterTable($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(129)."</h3>\n";$Td=foreign_keys($a);$wa=function_exists('Adminer\alter_table');if($Td){echo"<table>\n","<thead><tr><th>".lang(160)."<th>".lang(161)."<th>".lang(132)."<th>".lang(131).($wa?"<td class='hover'>":"")."<tbody>\n";foreach($Td
as$A=>$m){echo"<tr".(!is_int($A)?" title='".h($A)."'":"").">","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$m["source"]))."</i>";$y=($m["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($m["db"]),ME):($m["ns"]!=""?preg_replace('~&ns=[^&]*~',"&ns=".url_escape($m["ns"]),ME):ME));echo"<td><a href='".h($y."table=".url_escape($m["table"]))."'>".($m["db"]!=""&&$m["db"]!=DB?"<b>".h($m["db"])."</b>.":"").($m["ns"]!=""&&$m["ns"]!=$_GET["ns"]?"<b>".h($m["ns"])."</b>.":"").h($m["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$m["target"]))."</i>)","<td>".h($m["on_delete"]),"<td>".h($m["on_update"]),($wa?'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($A)).'">'.lang(162).'</a>':''),"\n";}echo"</table>\n";}echo($wa?'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(163)."</a>\n":''),"</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(164)."</h3>\n";$ib=driver()->checkConstraints($a);if($ib){echo"<table>\n";foreach($ib
as$v=>$X)echo"<tr".($v!=$X?" title='".h($v)."'":"").">","<td><code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($v))."'>".lang(162)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(165)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")&&driver()->supportsAlterTable($S)){echo"<div>\n","<h3 id='triggers'>".lang(166)."</h3>\n";$Nl=triggers($a);if($Nl){echo"<table>\n";foreach($Nl
as$v=>$X){echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($v)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($v))."'>".lang(162)."</a>";$_j=$X[2];if($_j){$Bj=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($_j["ns"]),ME).'function='.url_escape($_j["function"]).'&name='.url_escape($_j["name"]);echo", <a href='".h($Bj)."' title='".h($_j["name"])."'>".lang(167)."</a>";}echo"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(168)."</a>\n","</div>\n";}$kk=driver()->shadowTables($a);if($kk){echo"<h3 id='shadow-tables'>".lang(169)."</h3>\n";tables_links($kk);}$af=driver()->inheritedTables($a);if($af){echo"<h3 id='partitions'>".lang(170)."</h3>\n";$fi=driver()->partitionsInfo($a);if($fi)echo"<p><code class='sql jush-".JUSH."'>BY ".h("$fi[partition_by]($fi[partition])")."</code>\n";tables_links($af);}}elseif(isset($_GET["schema"])){page_header(lang(75),"",[],h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$ij,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($ij,$R)as$A=>$kj){if($A!=$R)$d[$R]=max($d[$R],schema_column($A,$ij,$d)+1);}}return$d[$R];}function
type_class($U){foreach(['char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',]as$v=>$X){if(preg_match("~$v|$X~",$U))return" class='$v'";}}$bl=[];$dl=[];$cl=[];$Dd=[];$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$fg,PREG_SET_ORDER);foreach($fg
as$q=>$_){$bl[$_[1]]=[(float)$_[2],(float)$_[3]];$dl[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$L=[];$ij=[];$Td=[];$ta=driver()->allFields();$Be=[];$el=[];foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!=""&&!$S["dependent"])$el[$R]=$S;else$Be[$R]=true;}}foreach($el
as$R=>$S){$F=0;$L[$R]["fields"]=[];foreach($ta[$R]as$j){$F+=1.25;$Dd[$R][$j["field"]]=$F;$L[$R]["fields"][$j["field"]]=$j;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$Be[$X["table"]]){$Td[$R][]=$X;$ij[$X["table"]][$R]=[];}}}$d=[];$ie=[];$Km=[];$ne=[];foreach(array_keys($L)as$A)schema_column($A,$ij,$d);arsort($d);foreach($d
as$A=>$c){$Gg=null;foreach((array)idx($Td,$A)as$X){if($X["table"]!=$A&&$L[$X["table"]])$Gg=($Gg===null?$d[$X["table"]]:min($Gg,$d[$X["table"]]));}$d[$A]=max($c,(int)$Gg-1);}$gl=[];foreach($L
as$A=>$R){$c=$d[$A];$ie[$c][]=$A;$tl=.75*strlen($A);foreach($R["fields"]as$j)$tl=max($tl,.65*strlen($j["field"]));$gl[$A]=ceil($tl)+1;$Km[$c]=max(idx($Km,$c,0),$gl[$A]);}foreach($Td
as$A=>$vm){foreach($vm
as$X){$me=$d[$A]+(idx($d,$X["table"],$d[$A])>$d[$A]?1:0);$ne[$me]=idx($ne,$me,0)+1;}}ksort($ie);$_e=0;$Jm=0;$wb=0;$Ki=null;$Zk=[];foreach($ie
as$c=>$T){if($Ki!==null){$wb=round($wb+$Km[$Ki]+1.7+idx($ne,$c,0)*.1,1);$C=[];foreach($T
as$A){$Pk=0;$Nb=0;$Yg=array_keys((array)idx($ij,$A));foreach((array)idx($Td,$A)as$X)$Yg[]=$X["table"];foreach($Yg
as$Sg){if($L[$Sg]&&$d[$Sg]<$c){$Pk+=$L[$Sg]["pos"][0];$Nb++;}}$C[$A]=($Nb?$Pk/$Nb:$_e);}asort($C);$T=array_keys($C);}$Dl=0;foreach($T
as$A){$F=1.25*count($L[$A]["fields"]);$L[$A]["pos"]=($bl[$A]?:[$Dl,$wb]);$Zk[$A]=$L[$A]["pos"][1];$Dl+=2.5+$F;$_e=max($_e,$L[$A]["pos"][0]+2.5+$F);$Jm=max($Jm,round($L[$A]["pos"][1]+$Km[$c],1));if(!$bl[$A])$cl[]="\n\t'".js_escape($A)."': [ ".$L[$A]["pos"][0].", ".$L[$A]["pos"][1]." ]";}$Ki=$c;}$Of=[];$Oa=[];foreach($Td
as$A=>$vm){foreach($vm
as$X){$ml=idx($Zk,$X["table"],$Zk[$A]);$uk=$Zk[$A]+$Km[$d[$A]];$xj=($ml-1>$uk);$Mf=($xj?$uk+1:min($Zk[$A],$ml)-1);$Na=idx($Oa,(string)$Mf,0);$Oa[(string)$Mf]=$Na+1;$Mf=round($xj?min($Mf+$Na*.1,$ml-1):$Mf-$Na*.1,1);while($Of[(string)$Mf])$Mf-=.0001;$L[$A]["references"][$X["table"]][(string)$Mf]=[$X["source"],$X["target"]];$ij[$X["table"]][$A][(string)$Mf]=$X["target"];$Of[(string)$Mf]=true;}}echo'<div id="schema" style="height: ',$_e,'em; width: ',$Jm,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$dl)."\n",'};
const tablePosDefault = {',implode(",",$cl)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$_e,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($L
as$A=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$gl[$A]."em;'>",'<a href="'.h(ME).'table='.url_escape($A).'"><b>'.h($A)."</b></a>";foreach($R["fields"]as$j){$X='<span'.type_class($j["type"]).' title="'.h($j["type"].($j["length"]?"($j[length])":"").($j["null"]?" NULL":'')).'">'.h($j["field"]).'</span>';echo"<br>".($j["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$nl=>$kj){foreach($kj
as$Mf=>$fj){$Nf=$Mf-$R["pos"][1];$Mk=($Nf>0?"left: 100%; width: calc($Nf"."em - 100%)":"left: $Nf"."em");$Jm=($Nf>0?"100%":(-$Nf)."em");$q=0;foreach($fj[0]as$tk)echo"\n<div class='references' title='".h($nl)."' id='refs$Mf-".($q++)."' style='$Mk"."; top: ".$Dd[$A][$tk]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Jm;'></div></div>";}}foreach((array)$ij[$A]as$nl=>$kj){foreach($kj
as$Mf=>$ol){$Nf=$Mf-$R["pos"][1];$q=0;foreach($ol
as$ll)echo"\n<div class='references arrow' title='".h($nl)."' id='refd$Mf-".($q++)."' style='left: $Nf"."em; top: ".$Dd[$A][$ll]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Nf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($L
as$A=>$R){foreach((array)$R["references"]as$nl=>$kj){if($L[$nl]){foreach($kj
as$Mf=>$fj){$Hg=$_e;$ng=-10;foreach($fj[0]as$v=>$tk){$Bi=$R["pos"][0]+$Dd[$A][$tk];$Ci=$L[$nl]["pos"][0]+$Dd[$nl][$fj[1][$v]];$Hg=min($Hg,$Bi,$Ci);$ng=max($ng,$Bi,$Ci);}echo"<div class='references' id='refl$Mf' style='left: $Mf"."em; top: $Hg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($ng-$Hg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(171),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$i){$h=["auto_increment"=>''];foreach(["type","routine","event","trigger"]as$Rk){if(support($Rk))$h[$Rk."s"]='';}save_settings(array_intersect_key($_POST+$h,array_flip(["output","format","db_style","schema_style","table_style","data_style"])+$h),"adminer_export");$sa=(DB==""||$_GET["ns"]==="");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$ud=dump_headers((count($T)==1?key($T):DB),($sa||count($T)>1));$sf=preg_match('~sql~',$_POST["format"]);if($sf){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Mk=$_POST["db_style"];$f=[DB];if(DB==""){$f=$_POST["databases"];if(is_string($f))$f=explode("\n",rtrim(str_replace("\r","",$f),"\n"));}foreach((array)$f
as$g){adminer()->dumpDatabase($g);if(connection()->select_db($g)){if($sf&&$Mk)echo
use_sql($g,$Mk).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?[""]:adminer()->schemas()))as$L){if($L!=""){if(DB==""&&information_schema(DB,$L))continue;set_schema($L);}if($sf&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$Ik=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):[]);$td=[];$ac=[];foreach($Ik
as$A=>$S){if($sa||in_array($A,(array)$_POST["tables"]))$td[$A]=$S;if($sa||in_array($A,(array)$_POST["data"]))$ac[$A]=$S;}if($sf){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($td);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Ol=[];foreach($ac
as$A=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($td[$A])))$Ol[]=$A;}echo
truncate_all_sql($Ol);}$Uh="";if($_POST["types"]){foreach(types()as$r=>$U){$mc=type_definition($r);$lh=($mc["kind"]=='d'?"DOMAIN":"TYPE");if($mc["definition"])$Uh
.=($Mk!='DROP+CREATE'?"DROP $lh IF EXISTS ".table($U).";;\n":"")."CREATE $lh ".table($U)." $mc[definition];\n\n";else$Uh
.="-- Could not export type ".preg_replace('~[\r\n]+~',' ',$U)."\n\n";}}if($_POST["routines"]){foreach(routines()as$J){$A=$J["ROUTINE_NAME"];$_j=$J["ROUTINE_TYPE"];$Ze=routine($J["SPECIFIC_NAME"],$_j);if($Ze["definition"]!=""){$Ob=create_routine($_j,["name"=>$A]+$Ze);set_utf8mb4($Ob);$Uh
.=($Mk!='DROP+CREATE'?"DROP $_j IF EXISTS ".table($A).";;\n":"")."$Ob;\n\n";}else$Uh
.="-- Could not export ".strtolower($_j)." ".preg_replace('~[\r\n]+~',' ',$A)."\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$J){$Ob=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($J["Name"]),3));set_utf8mb4($Ob);$Uh
.=($Mk!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($J["Name"]).";;\n":"")."$Ob;;\n\n";}}echo($Uh&&JUSH=='sql'?"DELIMITER ;;\n\n$Uh"."DELIMITER ;\n\n":$Uh);}if($_POST["table_style"]||$_POST["data_style"]){$Am=[];foreach($Ik
as$A=>$S){$R=array_key_exists($A,$td);$Yb=array_key_exists($A,$ac);if($R||$Yb){$Al=null;if($ud=="tar"){$Al=new
TmpFile;ob_start([$Al,'write'],1e5);}adminer()->dumpTable($A,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$Am[]=$A;elseif($Yb){$k=fields($A);$M=["*"];$Kb=convert_fields($k,$k);if($Kb)$M[]=substr($Kb,2);adminer()->dumpData($A,$_POST["data_style"],"",$M);}if($sf&&$_POST["triggers"]&&$R&&($Nl=trigger_sql($A)))echo"\nDELIMITER ;;\n$Nl\nDELIMITER ;\n";if($ud=="tar"){ob_end_flush();tar_file((DB!=""?"":"$g/")."$A.csv",$Al);}elseif($sf)echo"\n";}}if($sf&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($td
as$A=>$S){if(!is_view($S))echo
foreign_keys_sql($A);}}if($sf){foreach($Am
as$_m)adminer()->dumpTable($_m,$_POST["table_style"],1);}if($ud=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(81),$i,($_GET["export"]!=""?["table"=>$_GET["export"]]:[]),h(DB));echo'
<form method="post">
<table class="layout">
';$ec=['','USE','DROP+CREATE','CREATE'];$Jj=(JUSH=="mssql"?['','DROP+CREATE','CREATE']:$ec);$fl=['','DROP+CREATE','CREATE'];$Zb=['','TRUNCATE+INSERT','INSERT'];if(JUSH=="sql")$Zb[]='INSERT+UPDATE';$J=get_settings("adminer_export");if(!$J)$J=["output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT"];echo"<tr><th>".lang(172)."<td>".html_radios("output",adminer()->dumpOutput(),$J["output"])."\n","<tr><th>".lang(173)."<td>".html_radios("format",adminer()->dumpFormat(),$J["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(17)."<td>".html_select('db_style',$ec,$J["db_style"]).(support("type")?checkbox("types",1,$J["types"],lang(0)):"").(support("routine")?checkbox("routines",1,$J["routines"],lang(77)):"").(support("event")?checkbox("events",1,$J["events"],lang(79)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(16)."<td>".html_select('schema_style',$Jj,$J["schema_style"]):""),"<tr><th>".lang(148)."<td>".html_select('table_style',$fl,$J["table_style"]).checkbox("auto_increment",1,$J["auto_increment"],lang(57)).(support("trigger")?checkbox("triggers",1,$J["triggers"],lang(166)):""),"<tr><th>".lang(174)."<td>".html_select('data_style',$Zb,$J["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(81),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Ji=[];if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(175)."'".on('click','formCheck','^schemas\[').">".lang(16)."</label>","<tbody>\n";foreach(adminer()->schemas()as$L){if(!information_schema(DB,$L))echo"<tr><td>".checkbox("schemas[]",$L,true,$L,"","block")."\n";}}elseif(DB!=""){$jb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$jb class='jsonly' title='".lang(175)."'".on('click','formCheck','^tables\[').">".lang(156)."</label>","<th style='text-align: right;'><label class='block'>".lang(174)."<input type='checkbox' id='check-data'$jb class='jsonly' title='".lang(175)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Am="";$il=tables_list();foreach($il
as$A=>$U){$Ii=preg_replace('~_.*~','',$A);$jb=($a==""||$a==(substr($a,-1)=="%"?"$Ii%":$A));$Ni="<tr><td>".checkbox("tables[]",$A,$jb,$A,"","block");if($U!==null&&!preg_match('~table~i',$U))$Am
.="$Ni\n";else
echo"$Ni<td align='right'><label class='block'><span id='Rows-".h($A)."'></span>".checkbox("data[]",$A,$jb)."</label>\n";$Ji[$Ii]++;}echo$Am;if($il)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$f=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($f?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(175)."'".on('click','formCheck','^databases\[').">":"").lang(17)."</label>","<tbody>\n";if($f){foreach($f
as$g){if(!information_schema($g)){$Ii=preg_replace('~_.*~','',$g);echo"<tr><td>".checkbox("databases[]",$g,$a==""||$a=="$Ii%",$g,"","block")."\n";$Ji[$Ii]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Md=true;foreach($Ji
as$v=>$X){if($v!=""&&$X>1){echo($Md?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$v%")."'>".h($v)."</a>";$Md=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(76));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(176)."</a>";$H=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$ge=$H;if(!$H)$H=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($ge?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(40)."<th>".lang(38)."<td class='hover'><tbody>\n";while($J=$H->fetch_assoc())echo'<tr><td>'.h($J["User"]),"<td>".h($J["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($J["User"]).'&host='.url_escape($J["Host"])).'">'.lang(20)."</a>\n";if(!$ge||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(20)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){$Wf=driver()->lineComment();$vk="(?:\\s|\xEF\xBB\xBF|/\\*[\s\S]*?\\*/|(?:$Wf)[^\n]*\n?|--\r?\n)";$Ck="~^(?:$vk|;)*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is";if(!$i&&$_POST["export"]&&JUSH=="sqlite"&&preg_match($Ck,$_POST["query"],$_)!==0)$i=lang(177,preg_match('~ATTACH~i',$_[1])?'ATTACH':'VACUUM INTO');if(!$i&&$_POST["export"]){save_settings(["output"=>$_POST["output"],"format"=>$_POST["format"]],"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$i&&$_POST["val"]){$oa=0;$Nk=true;$cb=[];$Gj=0;foreach($_POST["val"]as$K)$Gj+=count($K);$Qa=$Gj>1&&driver()->begin();foreach($_POST["val"]as$Xk=>$K){$R=bracket_escape($Xk,true);$k=fields($R);$Yk=indexes($R);foreach($K
as$s=>$J){parse_str(bracket_escape($s,true),$Z);$Wl=[];foreach($Z["where"]as$v=>$X)$Wl[bracket_escape($v,true)]=$X;if(!$k||$Z["null"]||array_diff_key($Wl,$k)||!unique_array($Wl,$Yk)){$Nk=false;break
2;}$O=[];$M=[];foreach($J
as$Af=>$X){$v=bracket_escape($Af,true);$j=idx($k,$v);if(!$j){$Nk=false;break
3;}$O[idf_escape($v)]=(preg_match('~char|text~',$j["type"])||$X!=""?adminer()->processInput($j,$X):"NULL");$M[$Af]=$v;}$Zi=where($Z,$k);if(!driver()->update($R,$O," WHERE $Zi",0," ")){$Nk=false;break
2;}$oa+=connection()->affected_rows;$d=[];foreach($M
as$v)$d[]=idf_escape($v);$fm=driver()->select($R,$d,[$Zi],$d);$ch=($fm?$fm->fetch_row():[]);$wf=0;foreach($M
as$Af=>$v){$j=$k[$v];$Lk=['type'=>(preg_match('~binary~',$j["type"])?'blob':$j["type"])];$cb["val[$Xk][$s][$Af]"]=select_value(idx($ch,$wf++),"",$Lk,null);}}}if($Qa&&$Nk)$Nk=driver()->commit();queries_redirect(null,lang(178,$oa),$Nk);if($Qa&&!$Nk)driver()->rollback();page_headers();page_messages($i);foreach($cb
as$A=>$X)echo"<div data-name='".h($A)."' hidden>$X</div>\n";exit;}restart_session();$Fe=&get_session("queries");$Ee=&$Fe[DB];if(!$i&&$_POST["clear"]){$Ee=[];redirect(remove_from_uri("history"));}stop_session();$ma=get_settings("adminer_import");if($_POST&&$ma)save_settings($ma,"adminer_import");page_header((isset($_GET["import"])?lang(80):lang(69)),$i);$fd=0;function
query_error_line($rl,$rh,$G,$Vf){return
substr_count(substr($rl,0,$rh),"\n")+($Vf?:substr_count(substr($G,0,strspn($G," \t\r\n")),"\n")+1);}if(!$i&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$oc=driver()->delimiter;$n=false;if(!isset($_GET["import"]))$G=$_POST["query"];elseif($_POST["webfile"]){$yk=adminer()->importServerPath();$n=@fopen((file_exists($yk)?$yk:"compress.zlib://$yk.gz"),"rb");$G=($n?fread($n,1e6):false);}else$G=get_file("sql_file",true,$oc);if(is_string($G)){if(($vg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($vg,strval(2*strlen($G)+memory_get_usage()+8e6)));if($G!=""&&strlen($G)<1e6){$Ui=$G.(preg_match("~$oc\\s*\$~",$G)?"":$oc);if(!$Ee||first(end($Ee))!=$Ui){restart_session();$Ee[]=[$Ui,time()];set_session("queries",$Fe);stop_session();}}$rh=0;$Yi=0;$Wc=true;$Mb=false;$Bg=false;$Db=connect();if($Db&&DB!=""){$Db->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$Db);}$xb=0;$gd=[];$di='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Wf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$El=microtime(true);while($G!=""){if(!$rh&&preg_match("~^$vk*+DELIMITER\\s+(\\S+)~i",$G,$_)){$oc=preg_quote($_[1]);$G=substr($G,strlen($_[0]));$Yi+=strlen($_[0]);}elseif(!$rh&&JUSH=='pgsql'&&preg_match("~^($vk*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$G,$_)){$oc="\n\\\\\\.\r?\n";$Mb=true;$rh=strlen($_[0]);}elseif(!$rh&&JUSH=='pgsql'&&preg_match("~^$vk*+\\\\connect[ \t][^\n]*~",$G,$_)){$oc="\n";$Bg=true;$rh=strlen($_[0]);}else{preg_match("($oc\\s*|$di)",$G,$_,PREG_OFFSET_CAPTURE,$rh);list($Vd,$F)=$_[0];if(!$Vd&&$n&&!feof($n))$G
.=fread($n,1e5);else{if(!$Vd&&rtrim($G)=="")break;$rh=$F+strlen($Vd);if($Vd&&!preg_match("(^$oc)",$Vd)){$Ya=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($F>0&&strtolower($G[$F-1])=="e"));$si=($Vd=='/*'?'\*/':($Vd=='['?']':(preg_match("~^(?:$Wf)~",$Vd)?"\n":preg_quote($Vd).($Ya?'|\\\\.':''))));while(preg_match("($si|\$)s",$G,$_,PREG_OFFSET_CAPTURE,$rh)){$Hj=$_[0][0];if(!$Hj&&$n&&!feof($n))$G
.=fread($n,1e5);else{$rh=$_[0][1]+strlen($Hj);if(!$Hj||$Hj[0]!="\\")break;}}}else{$Ui=substr($G,0,$F+($Mb?3:0));$G=substr($G,$rh);$Vi=$Yi;$Yi+=$rh;$rh=0;if($Mb||$Bg){$oc=driver()->delimiter;$Mb=false;$Bg=false;}$qb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Ui)."</code>";if(preg_match("~^$vk*+\$~",$Ui)&&!preg_match('~/\*M?!~',$Ui)){echo($_POST["only_errors"]?"":"<pre>$qb</pre>\n");continue;}$Wc=false;$xb++;$Ni="<pre id='sql-$xb'>$qb</pre>\n";if(JUSH=="sqlite"&&preg_match($Ck,$Ui,$_)!==0){echo$Ni,"<p class='error'>".lang(177,preg_match('~ATTACH~i',$_[1])?'ATTACH':'VACUUM INTO')."\n";$gd[]=" <a href='#sql-$xb'>$xb</a>";if(!$fd&&!isset($_GET["import"]))$fd=query_error_line($_POST["query"],$Vi,$Ui,0);if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Ni;ob_flush();flush();}$Ek=microtime(true);if(JUSH=="pgsql"&&preg_match("~^$vk*+\\\\connect\\s+(\"(?:[^\"]|\"\")+\"|[^\\s\";]+)\\s*;*\\s*\$~",$Ui,$_)){$g=idf_unescape($_[1]);if(connection()->select_db($g)){if($Db)$Db->select_db($g);echo($_POST["only_errors"]?"":"<p class='message'>".lang(17).": ".h($g)." <span class='time'>(".format_time($Ek).")</span>\n");continue;}}elseif(connection()->multi_query($Ui)&&$Db&&preg_match("~^$vk*+USE\\b~i",$Ui))$Db->query($Ui);do{$H=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Ni:""),"<p class='error'>".lang(179).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$gd[]=" <a href='#sql-$xb'>$xb</a>";if(!$fd&&!isset($_GET["import"]))$fd=query_error_line($_POST["query"],$Vi,$Ui,driver()->errorLine($Ui));if($_POST["error_stops"])break
2;}else{$y=ME."sql=".url_escape(trim($Ui));$ul=" <span class='time'>(".format_time($Ek).")</span>".(strlen($y)<adminer()->sqlUrlLength()-100?" <a href='".h($y)."'>".lang(20)."</a>":"");$oa=connection()->affected_rows;$Dm=($_POST["only_errors"]?"":driver()->warnings());$Em="warnings-$xb";if($Dm)$ul
.=", <a href='#$Em' class='toggle'>".lang(52)."</a>";$rd="";$sd="explain-$xb";if(is_object($H)){$x=$_POST["limit"];$jh=$x;$Nc=!$_POST["only_errors"];if($Nc)echo"<form method='post'>\n";$Mh=print_select_result($H,$Db,[],$jh,$Nc);if(!$_POST["only_errors"]){$jh=max($H->num_rows,$jh);echo"<p class='sql-footer'>".($jh?($x&&$jh>$x?lang(180,$x):"").lang(181,$jh):""),$ul;if($Db&&preg_match("~^($vk|\\()*+SELECT\\b~i",$Ui)&&($rd=adminer()->explain($Db,$Ui,$Mh))!="")echo", <a href='#$sd' class='toggle'>Explain</a>";if($Nc)echo", <input type='submit' name='save' value='".lang(24)."' class='jsonly' disabled"." title='".lang(182)."'".on('click','sqlSave',lang(27)).">";$r="export-$xb";echo", <a href='#$r' class='toggle'>".lang(81)."</a><span id='$r' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ma["output"])." ".html_select("format",adminer()->dumpFormat(),$ma["format"]).input_hidden("query",$Ui)."<input type='submit' name='export' value='".lang(81)."'".($x?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$vk*+(CREATE|DROP|ALTER)$vk++(DATABASE|SCHEMA)\\b~i",$Ui)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(183,$oa)."$ul\n";}echo($Dm?"<div id='$Em' class='hidden'>\n$Dm</div>\n":""),($rd!=""?"<div id='$sd' class='hidden explain'>\n$rd</div>\n":"");}$Ek=microtime(true);}while(connection()->next_result());}}}}}if($Wc)echo"<p class='message'>".lang(184)."\n";else{$Te=connection()->inTransaction();driver()->rollback();if($Te)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(185,$xb-count($gd))," <span class='time'>(".format_time($El).")</span>\n";elseif($gd&&$xb>1)echo"<p class='error'>".lang(179).": ".implode("",$gd)."\n";}}else
echo"<p class='error'>".upload_error($G)."\n";}echo'
<form method="post" enctype="multipart/form-data" id="form"';$gm="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"),adminer()->sqlUrlLength());else
echo
on_upload_progress($gm);echo'>
';$od="<input type='submit' value='".lang(186)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Ui=$_GET["sql"];if($_POST)$Ui=$_POST["query"];elseif($_GET["history"]=="all")$Ui=$Ee;elseif($_GET["history"]!="")$Ui=idx($Ee[$_GET["history"]],0);echo"<p>";textarea("query",$Ui,20,80,JUSH,$fd);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$od\n",lang(187).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$oe=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(188)."</legend><div>",($gm?input_hidden(ini_get("session.upload_progress.name"),$gm):""),"SQL$oe: ".file_input(" name='sql_file[]' multiple","\n$od"),($gm?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Qe=adminer()->importServerPath();if($Qe)echo"<fieldset><legend>".lang(189)."</legend><div>",lang(190,"<code>".h($Qe)."$oe</code>")," <input type='submit' name='webfile' value='".lang(191)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(192))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(193))."\n",input_token();if(!isset($_GET["import"])&&$Ee){print_fieldset("history",lang(194),$_GET["history"]!="");for($X=end($Ee);$X;$X=prev($Ee)){$v=key($Ee);list($Ui,$ul,$Sc)=$X;echo'<div><a href="'.h(ME."sql=&history=$v").'" class="hover">'.lang(20)."</a>"." <span class='time' title='".@date('Y-m-d',$ul)."'>".@date("H:i:s",$ul)."</span>"." <code class='sql jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Wf).*~m",'',$Ui))),80,"</code>").($Sc?" <span class='time'>($Sc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(195)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(196)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$k=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$k):""):where($_GET,$k));$dm=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($k
as$A=>$j){if((!$dm&&!isset($j["privileges"]["insert"]))||adminer()->fieldName($j)=="")unset($k[$A]);}if($_POST&&!$i&&!isset($_GET["select"])){$z=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$z=($dm?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$z))$z=ME."select=".url_escape($a);$u=indexes($a);$Xl=unique_array($_GET["where"],$u);$Zi="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($z,lang(197),driver()->delete($a,$Zi,$Xl?0:1));else{$O=[];foreach($k
as$A=>$j){$X=process_input($j);if($X!==false&&$X!==null)$O[idf_escape($A)]=$X;}if($dm){if(!$O)redirect($z);queries_redirect($z,lang(198),driver()->update($a,$O,$Zi,$Xl?0:1));if(is_ajax()){page_headers();page_messages($i);exit;}}else{$H=driver()->insert($a,$O);$Lf=($H?last_id($H):0);queries_redirect($z,lang(199,($Lf?" $Lf":"")),$H);}}}$J=null;$G="";$ul="";if($Z){$M=[];$Uj=["*"];foreach($k
as$A=>$j){if(isset($j["privileges"]["select"])){$Ba=($_POST["clone"]&&$j["auto_increment"]?"''":convert_field($j));$c=($Ba?"$Ba AS ":"").idf_escape($A);$M[]=$c;if($Ba)$Uj[]=$c;}}$J=[];if(!support("table")){$M=["*"];$Uj=$M;}if($M){$Ek=microtime(true);$H=driver()->select($a,$M,[$Z],$M,[],(isset($_GET["select"])?2:1));$G=str_replace("SELECT ".implode(", ",$M),"SELECT ".implode(", ",$Uj),driver()->query);$ul=format_time($Ek);if(!$H)$i=adminer()->error();else{$J=$H->fetch_assoc();if(!$J)$J=false;}if(isset($_GET["select"])&&(!$J||$H->fetch_assoc()))$J=null;}}if(!$k&&driver()->primary!=""){if(!$Z){$H=driver()->select($a,["*"],[],["*"]);$J=($H?$H->fetch_assoc():false);if(!$J)$J=[driver()->primary=>""];}if($J){foreach($J
as$v=>$X){if(!$Z)$J[$v]=null;$k[$v]=["field"=>$v,"null"=>($v!=driver()->primary),"auto_increment"=>($v==driver()->primary)];}}}if($_POST["save"]){$Di=[];foreach((array)$_POST["fields"]as$v=>$X)$Di[bracket_escape($v,true)]=$X;$J=$Di+($J?$J:[]);}edit_form($a,$k,$J,$dm,$i,$G,$ul);}elseif(isset($_GET["create"])){function
referencable_primary($Wj){$I=[];foreach(table_status('',true)as$al=>$R){if($al!=$Wj&&!$R["dependent"]&&fk_support($R)){foreach(fields($al)as$j){if($j["primary"]){if($I[$al]){unset($I[$al]);break;}$I[$al]=$j;}}}}return$I;}$a=$_GET["create"];$hi=driver()->partitionBy;$li=($hi&&$a!=""?driver()->partitionsInfo($a):[]);$hj=referencable_primary($a);$Td=[];foreach($hj
as$al=>$j)$Td[str_replace("`","``",$al)."`".str_replace("`","``",$j["field"])]=$al;$Ph=[];$S=[];$hh=false;if($a!=""){$Ph=fields($a);$S=table_status1($a);$hh=(count($S)<2);}$wa=($a==""||driver()->supportsAlterTable($S));$J=$_POST;$J["fields"]=(array)$J["fields"];if($J["auto_increment_col"])$J["fields"][$J["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$i)save_settings(["comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]]);if($_POST&&!process_fields($J["fields"])&&!$i){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(200),drop_tables([$a]));else{$k=[];$ta=[];$km=false;$Rd=[];$Oh=reset($Ph);$qa=" FIRST";foreach($J["fields"]as$j){$m=$Td[$j["type"]];$Ql=($m!==null?$hj[$m]:$j);if($j["field"]!=""){if(!$j["generated"])$j["default"]=null;$j["default_sql"]=($j["generated"]=="DEFAULT SQL");$Si=process_field($j,$Ql);$ta[]=[$j["orig"],$Si,$qa];if(!$Oh||$Si!==process_field($Oh,$Oh)){$k[]=[$j["orig"],$Si,$qa];if($j["orig"]!=""||$qa||(JUSH=="sqlite"&&$j["comment"]!=""))$km=true;}if($m!==null)$Rd[idf_escape($j["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(['table'=>$Td[$j["type"]],'source'=>[$j["field"]],'target'=>[$Ql["field"]],'on_delete'=>$j["on_delete"],],object_name("FOREIGN",trim($J["name"]),[$j["field"]]));$qa=" AFTER ".idf_escape($j["field"]);}elseif($j["orig"]!=""){$km=true;$k[]=[$j["orig"]];}if($j["orig"]!=""){$Oh=next($Ph);if(!$Oh)$qa="";}}$ji=[];if(in_array($J["partition_by"],$hi)){foreach($J
as$v=>$X){if(preg_match('~^partition~',$v))$ji[$v]=$X;}foreach($ji["partition_names"]as$v=>$A){if($A==""){unset($ji["partition_names"][$v]);unset($ji["partition_values"][$v]);}}$ji["partition_names"]=array_values($ji["partition_names"]);$ji["partition_values"]=array_values($ji["partition_values"]);if($ji==$li)$ji=[];}elseif(preg_match("~partitioned~",$S["Create_options"]))$ji=null;$xg=lang(201);if($a==""){cookie("adminer_engine",$J["Engine"]);$xg=lang(202);}$A=trim($J["name"]);$z=ME.(support("table")?"table=":"select=").url_escape($A);$yb=($J["Comment"]!=$S["Comment"]?$J["Comment"]:null);$Zc=($J["Engine"]&&$J["Engine"]!=$S["Engine"]?$J["Engine"]:"");$H=alter_table($a,$A,(JUSH=="sqlite"&&($km||$Rd||$yb!==null||$Zc)?$ta:$k),$Rd,$yb,$Zc,($J["Collation"]&&$J["Collation"]!=$S["Collation"]?$J["Collation"]:""),($J["Auto_increment"]!=""?number($J["Auto_increment"]):""),$ji);if($H&&!Queries::$queries&&$a!=""&&!$k&&!$Rd)redirect($z);queries_redirect($z,$xg,$H);}}$Gk=($a!=""?"alter":"create");page_header(($a!=""?lang(50):lang(82)),$i,["table"=>$a],h($a),$hh,doc_link(['sql'=>"$Gk-table.html",'mariadb'=>($a!=""?"$Gk-table":""),]));if(!$_POST){$Tl=driver()->types();$J=["Engine"=>$_COOKIE["adminer_engine"],"fields"=>[["field"=>"","type"=>(isset($Tl["int"])?"int":(isset($Tl["integer"])?"integer":"")),"on_update"=>""]],"partition_names"=>[""],];if($a!=""){$J=$S;$J["name"]=$a;$J["fields"]=[];if(!$_GET["auto_increment"])$J["Auto_increment"]="";foreach($Ph
as$j){if($j["generated"])$j["default"]=ltrim($j["default"]);$j["generated"]=$j["generated"]?:(isset($j["default"])?($j["default_sql"]?"DEFAULT SQL":"DEFAULT"):"");$J["fields"][]=$j;}if($hi){$J+=$li;$J["partition_names"][]="";$J["partition_values"][]="";}}}$ub=flat_collations();$ad=driver()->engines();foreach($ad
as$Zc){if(!strcasecmp($Zc,$J["Engine"])){$J["Engine"]=$Zc;break;}}$ig=max_input_vars(12,20);if($ig){$Be=(count($J["fields"])>$ig?"":" hidden");echo"<p".($Be?" id='max-fields' data-columns='$ig'":"")." class='error$Be'>".max_input_vars_error()."\n";}echo'
<form method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(203).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($J["name"])."' autocapitalize='off'>\n",(!$wa?h($S["Engine"])."\n":($ad?html_select("Engine",[""=>"(".lang(204).")"]+$ad,$J["Engine"],on('change','helpClose').on_help_value())."\n":""));if($ub)echo"<datalist id='collations'>".optionlist($ub)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($J["Collation"])."' placeholder='(".lang(130).")'>\n");echo"<input type='submit' value='".lang(24)."'>\n";}if(support("columns")&&$wa){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($J["fields"],$ub,"TABLE",$Td);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(57).": <input type='number' name='Auto_increment' class='size' value='".h($J["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(205),on('click','columnShowClick',6),"jsonly");$_b=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$_b,lang(56),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($_b?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$J["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$a)),'>
';if($hi&&(JUSH=='sql'||$a=="")){$ii=preg_match('~RANGE|LIST~',$J["partition_by"]);print_fieldset("partition",lang(207),$J["partition_by"]);echo"<p>".html_select("partition_by",array_merge([""],$hi),$J["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($J["partition"])."'>)\n",lang(208).": <input type='number' name='partitions' class='size".($ii||!$J["partition_by"]?" hidden":"")."' value='".h($J["partitions"])."'>\n","<table id='partition-table'".($ii?"":" class='hidden'").">\n","<thead><tr><th>".lang(209)."<th>".lang(210)."<tbody>\n";foreach($J["partition_names"]as$v=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($v==count($J["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($J["partition_values"],$v)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Ye=["PRIMARY","UNIQUE","INDEX"];$S=table_status1($a,true);$We=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Ye[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Ye[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Ye[]="VECTOR";$u=indexes($a);$k=fields($a);$Mi=[];if(JUSH=="mongo"){$Mi=$u["_id_"];unset($Ye[0]);unset($u["_id_"]);}$J=$_POST;if($J)save_settings(["index_options"=>$J["options"]]);if($_POST&&!$i&&!$_POST["add"]&&!$_POST["drop_col"]){$va=[];foreach($J["indexes"]as$t){$A=$t["name"];if(in_array($t["type"],$Ye)){$d=[];$Sf=[];$rc=[];$Bh=[];$Xe=(support("partial_indexes")?$t["partial"]:"");$Ve=(in_array($t["algorithm"],$We)?$t["algorithm"]:"");$O=[];ksort($t["columns"]);foreach($t["columns"]as$v=>$c){if($c!=""){$w=idx($t["lengths"],$v);$pc=idx($t["descs"],$v);$Ah=idx($t["opclasses"],$v);$O[]=($k[$c]?idf_escape($c):$c).($w?"(".(+$w).")":"").($Ah!=""?" ".idf_escape($Ah):"").($pc?" DESC":"");$d[]=$c;$Sf[]=($w?:null);$rc[]=$pc;$Bh[]="$Ah";}}$pd=$u[$A];if($pd){ksort($pd["columns"]);ksort($pd["lengths"]);ksort($pd["descs"]);if($t["type"]==$pd["type"]&&array_values($pd["columns"])===$d&&(!$pd["lengths"]||array_values($pd["lengths"])===$Sf)&&array_values($pd["descs"])===$rc&&(!$pd["opclasses"]||array_values($pd["opclasses"])===$Bh)&&$pd["partial"]==$Xe&&(!$We||$pd["algorithm"]==$Ve)&&!$pd["invalid"]){unset($u[$A]);continue;}}if($d)$va[]=[$t["type"],$A,$O,$Ve,$Xe];}}foreach($u
as$A=>$pd)$va[]=[$pd["type"],$A,"DROP"];if(!$va)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(211),alter_indexes($a,$va));}page_header(lang(158),$i,["table"=>$a],h($a),false,doc_link(['sql'=>"create-index.html",]));$Fd=array_keys($k);if($_POST["add"]){foreach($J["indexes"]as$v=>$t){if($t["columns"][count($t["columns"])]!="")$J["indexes"][$v]["columns"][]="";}$t=end($J["indexes"]);if($t["type"]||array_filter($t["columns"],'strlen'))$J["indexes"][]=["columns"=>[1=>""]];}if(!$J){foreach($u
as$v=>$t){$u[$v]["name"]=$v;$u[$v]["columns"][]="";}$u[]=["columns"=>[1=>""]];$J["indexes"]=$u;}$Sf=(JUSH=="sql"||JUSH=="mssql");$Bh=driver()->indexOpclasses();$lk=($_POST?$_POST["options"]:get_setting("index_options"));$Ug=[];foreach($Ye
as$U)$Ug[$U]=str_replace("{table}",$a,adminer()->namePattern($U));echo'
<form method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(212);$Oe=" class='idxopts".($lk?"":" hidden")."'";if($We)echo"<th id='label-algorithm'$Oe>".lang(213).doc_link(['sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',]);echo'<th><input type="submit" hidden>',lang(214).($Sf?"<span$Oe> (".lang(215).")</span>":"");if($Sf||support("descidx"))echo
checkbox("options",1,$lk,lang(136),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(216);if(support("partial_indexes"))echo"<th id='label-condition'$Oe>".lang(217);echo'<td><noscript>',icon("plus","add[0]","+",lang(137)),'</noscript>
<tbody>
';if($Mi){echo"<tr><td>PRIMARY<td>";foreach($Mi["columns"]as$v=>$c)echo
select_input(" disabled",array_combine($Fd,$Fd),$c),"<label><input disabled type='checkbox'>".lang(64)."</label> ";echo"<td><td>\n";}$wf=1;foreach($J["indexes"]as$t){if(!$_POST["drop_col"]||$wf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$wf][type]",[-1=>""]+$Ye,$t["type"],on('change','indexesChangeType',$Ug),"label-type");if($We)echo"<td$Oe>".html_select("indexes[$wf][algorithm]",array_merge([""],$We),$t['algorithm'],"","label-algorithm");echo"<td>";ksort($t["columns"]);$q=1;foreach($t["columns"]as$v=>$c){echo"<span>".select_input(" name='indexes[$wf][columns][$q]' title='".lang(54)."'".on('change','indexesChangeColumn',$Ug),($k&&($c==""||$k[$c])?array_combine($Fd,$Fd):[]),$c)," <span$Oe>",($Sf?"<input type='number' name='indexes[$wf][lengths][$q]' class='size' value='".h(idx($t["lengths"],$v))."' title='".lang(135)."'>":"");if($Bh){$Ah=idx($t["opclasses"],$v);echo
html_select("indexes[$wf][opclasses][$q]",[""=>"(".lang(218).")"]+array_combine($Bh,$Bh)+($Ah!=""?[$Ah=>$Ah]:[]),$Ah),'';}echo(support("descidx")?checkbox("indexes[$wf][descs][$q]",1,idx($t["descs"],$v),lang(64)):""),"<br>","</span></span>";$q++;}echo"<td><input name='indexes[$wf][name]' value='".h($t["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Oe><input name='indexes[$wf][partial]' value='".h($t["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$wf]","x",lang(139),on('click','editingRemoveRow','indexes$1[type]')),(idx($u[$t["name"]],"invalid")?" <span class='error'>INVALID</span>":""),"\n";}$wf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if(support("concurrent_indexes"))echo
checkbox("concurrently",1,$_POST["concurrently"],"concurrently").''."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["database"])){$J=$_POST;if($_POST&&!$i&&!$_POST["add"]){$A=trim($J["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(219),drop_databases([DB]));}elseif($A!==DB){if(DB!=""){$_GET["db"]=$A;queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($A),lang(220),rename_database($A,(string)$J["collation"]));}else{$f=explode("\n",str_replace("\r","",$A));$Nk=true;$Jf="";foreach($f
as$g){if(count($f)==1||$g!=""){if(!create_database($g,(string)$J["collation"]))$Nk=false;$Jf=$g;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($Jf),lang(221),$Nk);}}else{if(!$J["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($A).(preg_match('~^[a-z0-9_]+$~i',$J["collation"])?" COLLATE $J[collation]":""),substr(ME,0,-1),lang(222));}}$Gk=(DB!=""?"alter":"create");page_header(DB!=""?lang(72):lang(143),$i,[],h(DB),false,doc_link(['sql'=>"$Gk-database.html",'mariadb'=>(DB!=""?"":"$Gk-database"),]));$ub=collations();$A=DB;if($_POST)$A=$J["name"];elseif(DB!="")$J["collation"]=db_collation(DB,$ub);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$ge){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$ge,$_)&&$_[1]){$A=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form method="post">
<p>
',($_POST["add"]||strpos($A,"\n")?"<textarea autofocus name='name' rows='10' cols='40'>\n".h($A).'</textarea><br>':'<input name="name" autofocus value="'.h($A).'" data-maxlength="64" autocapitalize="off">')."\n",($ub?"<datalist id='collations'>".optionlist(flat_collations())."</datalist>"."<input list='collations' name='collation' value='".h($J["collation"])."' placeholder='(".lang(130).")'>".doc_link(['sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",]):"")."\n",'<input type=\'submit\' value=\'',lang(24),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(152)."'".confirm(lang(206,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(137))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$Ej=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$_j=routine($_GET["call"],$Ej);$tf=(idx($_j["returns"],"type")=="trigger");page_header(($tf?lang(166):lang(223)).": ".h($ca),$i,"#routines","",!$_j,(isset($_GET["callf"])?"":doc_link(['sql'=>"call.html",])));$Re=[];$Uh=[];foreach($_j["fields"]as$q=>$j){if(substr($j["inout"],-3)=="OUT"&&JUSH=='sql')$Uh[$q]="@".idf_escape($j["field"])." AS ".idf_escape($j["field"]);if(!$j["inout"]||preg_match('~^(IN|OUTPUT)~',$j["inout"]))$Re[]=$q;}if(!$i&&$_POST){$Za=[];foreach($_j["fields"]as$v=>$j){$X="";if(in_array($v,$Re)){$X=process_input($j);if($X===false)$X="''";if(isset($Uh[$v]))connection()->query("SET @".idf_escape($j["field"])." = $X");}if(isset($Uh[$v]))$Za[]="@".idf_escape($j["field"]);elseif(in_array($v,$Re))$Za[]=$X;}$_a=implode(", ",$Za);$G=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($_j["returns"],"type")=="record"?"* FROM ":"").table($ca)."($_a)":"EXEC ".table($ca).($_a!=""?" $_a":""));$Ek=microtime(true);$H=connection()->multi_query($G);$oa=connection()->affected_rows;echo
adminer()->selectQuery($G,$Ek,!$H);if(!$H)echo"<p class='error'>".adminer()->error()."\n";else{$Db=connect();if($Db)$Db->select_db(DB);do{$H=connection()->store_result();if(is_object($H))print_select_result($H,$Db);else
echo"<p class='message'>".lang(224,$oa)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Uh)print_select_result(connection()->query("SELECT ".implode(", ",$Uh)));}}if($tf){$Nl=driver()->routineTriggers($_GET["call"]);if($Nl){echo"<table>\n";foreach($Nl
as$J){$y=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($J["ns"]),ME);echo"<tr><th>".h($J["trigger"])."<td><a href='".h($y."table=".url_escape($J["table"])."#triggers")."'>".($J["ns"]!=$_GET["ns"]?"<b>".h($J["ns"])."</b>.":"").h($J["table"])."</a>"."<td class='hover'><a href='".h($y."trigger=".url_escape($J["table"])."&name=".url_escape($J["trigger"]))."'>".lang(162)."</a>\n";}echo"</table>\n";}}else{echo'<form method="post">
';if($Re){echo"<table class='layout'>\n";foreach($Re
as$v){$j=$_j["fields"][$v];$A=$j["field"];echo"<tr><th>".adminer()->fieldName($j);$Y=idx($_POST["fields"],$A);if($Y!=""){if($j["type"]=="set")$Y=implode(",",$Y);}input($j,$Y,idx($_POST["function"],$A,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(223),'\'>
',input_token(),'</form>
';}echo'
',adminer()->commentValue($Ej,$_j['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$A=$_GET["name"];$J=$_POST;if($_POST&&!$i&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$J["source"]=array_filter($J["source"],'strlen');ksort($J["source"]);$ll=[];foreach($J["source"]as$v=>$X)$ll[$v]=$J["target"][$v];$J["target"]=$ll;}$Qa=false;if(JUSH=="sqlite")$H=recreate_table($a,$a,[],[],[" $A"=>($J["drop"]?"":" ".format_foreign_key($J))]);else{$Eb=$J["constraint"];$va="ALTER TABLE ".table($a);$Ic="$va DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A?:'');if($J["drop"])$H=queries($Ic);else{$ja=" ADD".format_foreign_key($J,$Eb);if($A=="")$H=queries($va.$ja);elseif(JUSH=="pgsql"||(JUSH=="sql"&&$Eb!=$A))$H=queries("$Ic,$ja");else{$Qa=support("transaction_ddl")&&driver()->begin();$H=($Qa||idx(foreign_keys($a),$A)?queries($Ic):true)&&queries($va.$ja)&&(!$Qa||driver()->commit());}}}queries_redirect(ME."table=".url_escape($a),($J["drop"]?lang(225):($A!=""?lang(226):lang(227))),$H);if($Qa)driver()->rollback();if(!$J["drop"])$i=lang(228);}$hh=false;if(!$_POST&&$A!=""){$Td=foreign_keys($a);$J=idx($Td,$A,[]);$hh=!$J;}$Tg=str_replace("{table}",$a,adminer()->namePattern("FOREIGN"));$Wg="^".str_replace('\{columns\}','.*',preg_quote($Tg))."$";page_header(($A!=""?lang(229):lang(163)),$i,["table"=>$a],h($A!=""?$A:$a),$hh,doc_link(['sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",]));if($_POST){ksort($J["source"]);if($_POST["change"]||$_POST["change-js"])$J["target"]=[];else$J["source"][]="";}elseif($A!=""){$J["source"][]="";$J["constraint"]=$A;}else{$J["table"]=$a;$J["source"]=[""];$J["constraint"]=str_replace("{columns}","",$Tg);}echo'
<form method="post">
';$tk=array_keys(fields($a));if($J["db"]!="")connection()->select_db($J["db"]);$Qh=null;if($J["ns"]!=""){$Qh=get_schema();set_schema($J["ns"]);}$gj=array_keys(array_filter(table_status('',true),function(array$S){return!$S["dependent"]&&fk_support($S);}));$ll=array_keys(fields(in_array($J["table"],$gj)?$J["table"]:reset($gj)));$b=on('change','foreignChange');echo"<p><label>".lang(230).": ".html_select("table",$gj,$J["table"],$b)."</label>\n";if(JUSH!="sqlite"){$Vj=($J["db"]!=""?$J["db"]:DB);$fc=[];foreach(adminer()->databases()as$g){if(!driver()->isSystem($g)||$g==$Vj||$g==DB)$fc[]=$g;}echo"<label>".lang(88).": ".html_select("db",$fc,$Vj,$b)."</label>";}if($J["db"]!="")connection()->select_db(DB);echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(231),'\'></noscript>
<table',on('change','foreignName',$Wg,$Tg),'>
<thead><tr><th id="label-source">',lang(160),'<th id="label-target">',lang(161),'<tbody>
';$wf=0;foreach($J["source"]as$v=>$X){echo"<tr>","<td>".html_select("source[".(+$v)."]",[-1=>""]+$tk,$X,($wf==count($J["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$v)."]",$ll,idx($J["target"],$v),"","label-target");$wf++;}echo'</table>
<p>
<label>',lang(132),': ',html_select("on_delete",[-1=>""]+explode("|",driver()->onActions),$J["on_delete"]),'</label>
<label>',lang(131),': ',html_select("on_update",[-1=>""]+explode("|",driver()->onActions),$J["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",['NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'],$J["deferrable"]):''),'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(232),'\'></noscript>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$A)),'>
';if(JUSH!="sqlite"){print_fieldset("options",lang(136),$J["constraint"]!=$A&&!preg_match("($Wg)",$J["constraint"]));echo"<label>".lang(216).": <input name='constraint' value='".h($J["constraint"])."' autocapitalize='off'></label>\n","</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$J=$_POST;$Rh="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$Rh=strtoupper($P["Engine"]);}if($_POST&&!$i){$A=trim($J["name"]);$Ba=" AS\n$J[select]";$z=ME."table=".url_escape($A);$xg=lang(233);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$A&&JUSH!="sqlite"&&$U=="VIEW"&&$Rh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($A).$Ba,$z,$xg);else{$pl="adminer_".uniqid();drop_create("DROP $Rh ".table($a),"CREATE $U ".table($A).$Ba,"DROP $U ".table($A),"CREATE $U ".table($pl).$Ba,"DROP $U ".table($pl),($_POST["drop"]?substr(ME,0,-1):$z),lang(234),$xg,lang(235),$a,$A);}}$hh=false;if(!$_POST&&$a!=""){$J=view($a);$hh=!$J["select"];$J["name"]=$a;$J["materialized"]=($Rh!="VIEW");if(!$i)$i=adminer()->error();}page_header(($a!=""?lang(49):lang(83)),$i,["table"=>$a],h($a),$hh,doc_link(['sql'=>"create-view.html",]));echo'
<form method="post">
<p>',lang(216),': <input name="name" value="',h($J["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$J["materialized"],lang(154)):""),'<p>';textarea("select",$J["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$jf=["YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND"];$Ik=["ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE"];$J=$_POST;if($_POST&&!$i){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(236));elseif(in_array($J["INTERVAL_FIELD"],$jf)&&isset($Ik[$J["STATUS"]])){$Ij="\nON SCHEDULE ".($J["INTERVAL_VALUE"]?"EVERY ".q($J["INTERVAL_VALUE"])." $J[INTERVAL_FIELD]".($J["STARTS"]?" STARTS ".q($J["STARTS"]):"").($J["ENDS"]?" ENDS ".q($J["ENDS"]):""):"AT ".q($J["STARTS"]))." ON COMPLETION".($J["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(237):lang(238)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Ij.($aa!=$J["EVENT_NAME"]?"\nRENAME TO ".idf_escape($J["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($J["EVENT_NAME"]).$Ij)."\n".$Ik[$J["STATUS"]]." COMMENT ".q($J["EVENT_COMMENT"]).rtrim(" DO\n$J[EVENT_DEFINITION]",";").";"));}}$hh=false;if(!$J&&$aa!=""){$K=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$hh=!$K;$J=reset($K);}page_header(($aa!=""?lang(239).": ".h($aa):lang(240)),$i,"#events","",$hh,doc_link(['sql'=>"create-event.html"]));echo'
<form method="post">
<table class="layout">
<tr><th>',lang(216),'<td><input name="EVENT_NAME" value="',h($J["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(241),'<td><input name="STARTS" value="',h("$J[EXECUTE_AT]$J[STARTS]"),'">
<tr><th title="datetime">',lang(242),'<td><input name="ENDS" value="',h($J["ENDS"]),'">
<tr><th>',lang(243),'<td><input type="number" name="INTERVAL_VALUE" value="',h($J["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$jf,$J["INTERVAL_FIELD"]),'<tr><th>',lang(87),'<td>',html_select("STATUS",$Ik,$J["STATUS"]),'<tr><th>',lang(56),'<td><input name="EVENT_COMMENT" value="',h($J["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$J["ON_COMPLETION"]=="PRESERVE",lang(244)),'</table>
<p>';textarea("EVENT_DEFINITION",$J["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$_j=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$J=$_POST;$J["fields"]=(array)$J["fields"];if($_POST&&!process_fields($J["fields"])&&!$i){foreach($J["fields"]as$v=>$j){if($j["field"]=="")unset($J["fields"][$v]);}$wh=routine($_GET["procedure"],$_j);$uh=($wh?routine_id($ca,$wh):"");$ah=routine_id($J["name"],$J);$Ob=create_routine($_j,$J);$z=substr(ME,0,-1);$xg=lang(245);if(!$_POST["drop"]&&$uh==$ah&&connection()->flavor!="mysql")queries_redirect($z,$xg,queries(substr_replace($Ob,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$pl="adminer_".uniqid();drop_create("DROP $_j $uh",$Ob,"DROP $_j $ah",create_routine($_j,["name"=>$pl]+$J),"DROP $_j ".routine_id($pl,$J),$z,lang(246),$xg,lang(247),$ca,$J["name"]);}}$hh=false;if(!$_POST&&$ca!=""){$J=routine($_GET["procedure"],$_j);$hh=!$J;$J["name"]=$ca;}$Cj=strtolower($_j);page_header(($ca!=""?(isset($_GET["function"])?lang(167):lang(248)).": ".h($ca):(isset($_GET["function"])?lang(249):lang(250))),$i,"#routines","",$hh,doc_link(['sql'=>"create-procedure.html",'mariadb'=>"create-$Cj/",]));if(!$_POST&&$ca=="")$J["language"]="sql";$ub=(JUSH=="sql"?flat_collations():[]);$Aj=routine_languages();echo($ub?"<datalist id='collations'>".optionlist($ub)."</datalist>":""),'
<form method="post" id="form">
<p>',lang(216),': <input name="name" value="',h($J["name"]),'" data-maxlength="64" autocapitalize="off">
',($Aj?"<label>".lang(30).": ".html_select("language",array_keys($Aj),$J["language"],on('change','routineLanguage',$Aj))."</label>\n":""),'<input type=\'submit\' value=\'',lang(24),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($J["fields"],$ub,$_j);if(isset($_GET["function"])){echo"<tr><td>".lang(251);edit_type("returns",(array)$J["returns"],$ub,[],(JUSH=="pgsql"?["void","trigger"]:[]));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$J["definition"],20,80,($Aj[$J["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$ca)),'>
';$Dj=routine_options($_j);if($Dj){$Hh=false;foreach($Dj
as$v=>$wm){$h=($wm?reset($wm):"");$J["options"][$v]=idx($J["options"],$v,$h);if($J["options"][$v]!=$h)$Hh=true;}print_fieldset("options",lang(136),$Hh);echo"<table class='layout'>\n";foreach($Dj
as$v=>$wm){$Df="label-option-$v";$xl=str_replace("_"," ",$v);$M=[];foreach($wm
as$Y)$M[$Y]=(strpos($Y,"$xl ")===0?substr($Y,strlen($xl)+1):$Y);echo"<tr><th id='$Df'>$xl<td>".($M?html_select("options[$v]",$M,$J["options"][$v],"",$Df):"<input name='options[$v]' value='".h($J["options"][$v])."' aria-labelledby='$Df' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$A="$_GET[name]";$J=$_POST;if($J&&!$i){$z=ME."table=".url_escape($a);$_g=lang(252);$yg=lang(253);$zg=lang(254);if(JUSH=="sqlite"&&!(min_version(3.53)&&($A==""||idx(driver()->checkConstraints($a),$A)!=$A))){$ja=($J["drop"]?"":($J["name"]!=""?"CONSTRAINT ".idf_escape($J["name"])." ":"")."CHECK ($J[clause])");queries_redirect($z,($J["drop"]?$_g:($A!=""?$yg:$zg)),recreate_table($a,$a,[],[],[],"",[],"$A",$ja));}else{$va="ALTER TABLE ".table($a);$hb=" CHECK ($J[clause])";$pl="adminer_".uniqid();drop_create("$va DROP CONSTRAINT ".idf_escape($A),"$va ADD".($J["name"]!=""?" CONSTRAINT ".idf_escape($J["name"]):"").$hb,"$va DROP CONSTRAINT ".idf_escape($J["name"]),"$va ADD CONSTRAINT ".idf_escape($pl).$hb,"$va DROP CONSTRAINT ".idf_escape($pl),$z,$_g,$yg,$zg,$A,$J["name"]);}}$hh=false;if(!$J){$kb=driver()->checkConstraints($a);$hh=($A!=""&&!$kb[$A]);$J=["name"=>($A==""?object_name("CHECK",$a,[]):($kb[$A]!=$A?$A:"")),"clause"=>$kb[$A]];}page_header(($A!=""?lang(255):lang(165)),$i,["table"=>$a],h($A!=""?$A:$a),$hh,doc_link(['sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",]));echo'
<form method="post">
<p><label>',lang(216),': <input name="name" value="',h($J["name"]),'" data-maxlength="64" autocapitalize="off"></label>
<p>';textarea("clause",$J["clause"]);echo'<p><input type=\'submit\' value=\'',lang(24),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$A="$_GET[name]";$Ml=trigger_options();$J=trigger($A,$a);$hh=($A!=""&&!$J);$Tg=str_replace("{table}",$a,adminer()->namePattern("TRIGGER"));$J+=["Trigger"=>strtr($Tg,["{timing}"=>"b","{event}"=>"i","{columns}"=>"","{type}"=>"row"])];if($_POST){if(!$i&&in_array($_POST["Timing"],$Ml["Timing"])&&in_array($_POST["Event"],$Ml["Event"])&&in_array($_POST["Type"],$Ml["Type"])){$yh=" ON ".table($a);$Ic="DROP TRIGGER ".idf_escape($A).(JUSH=="pgsql"?$yh:"");$z=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Ic,$z,lang(256));else{if($A!="")queries($Ic);queries_redirect($z,($A!=""?lang(257):lang(258)),queries(create_trigger($yh,$_POST)));if($A!="")queries(create_trigger($yh,$J+["Type"=>reset($Ml["Type"])]));}}$J=$_POST;}page_header(($A!=""?lang(259):lang(168)),$i,["table"=>$a],h($A!=""?$A:$a),$hh,doc_link(['sql'=>"create-trigger.html",]));$Wg=strtr(preg_quote($Tg),['\{timing\}'=>'[abi]','\{event\}'=>'[iud]*','\{columns\}'=>'.*','\{type\}'=>'(row|statement)']);$Ll=on('change','triggerChange',"^$Wg$",$Tg);$nh=on('input','triggerChange',"^$Wg$",$Tg);echo'
<form method="post" id="form">
<table class="layout">
<tr><th>',lang(260),'<td>',html_select("Timing",$Ml["Timing"],$J["Timing"],$Ll),'<tr><th>',lang(261),'<td>',html_select("Event",$Ml["Event"],$J["Event"],$Ll),(in_array("UPDATE OF",$Ml["Event"])?" <input name='Of' value='".h($J["Of"])."' class='hidden'$nh>":""),'<tr><th>',lang(55),'<td>',html_select("Type",$Ml["Type"],$J["Type"],$Ll),'<tr><th>',lang(216),'<td><input name="Trigger" value="',h($J["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$J["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($ge,array$Qi,$d,$yh){if(!$Qi)return
true;if($Qi==["ALL PRIVILEGES","GRANT OPTION"])return($ge=="GRANT"?queries("$ge ALL PRIVILEGES$yh WITH GRANT OPTION"):queries("$ge ALL PRIVILEGES$yh")&&queries("$ge GRANT OPTION$yh"));return
queries("$ge ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$Qi).$d).$yh);}function
queries_password($Ud,$pm,$ni){remember_query(sprintf($Ud,$pm,"<password>"));return
connection()->query(sprintf($Ud,$pm,q($ni)));}$ea=$_GET["user"];$Qi=[""=>["All privileges"=>""]];foreach(get_rows("SHOW PRIVILEGES")as$J){foreach(explode(",",($J["Privilege"]=="Grant option"?"":$J["Context"]))as$Ib)$Qi[$Ib=="File access on server"?"Server Admin":$Ib][$J["Privilege"]]=$J["Comment"];}unset($Qi["Server Admin"]["Usage"]);foreach($Qi["Tables"]as$v=>$X)unset($Qi["Databases"][$v]);$Zg=[];if($_POST){foreach($_POST["objects"]as$v=>$X)$Zg[$X]=(array)$Zg[$X]+idx($_POST["grants"],$v,[]);}$he=[];$H=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])):null);$hh=(isset($_GET["host"])&&!$H);if($H){while($J=$H->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$J[0],$_)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$_[1],$fg,PREG_SET_ORDER)){foreach($fg
as$X){if($X[1]!="USAGE")$he["$_[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$J[0]))$he["$_[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$i){$xh=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $xh",ME."privileges=",lang(262));else{$dh=q($_POST["user"])."@".q($_POST["host"]);$ni=$_POST["pass"];$Qb=false;$H=true;if($xh!=$dh){$Qb=queries_password("CREATE USER %s IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD %s":"%s"),$dh,$ni);$H=$Qb;}elseif($ni!="")$H=queries_password("SET PASSWORD FOR %s = ".(min_version(8,99)||$_POST["hashed"]?"%s":"PASSWORD(%s)"),$dh,$ni);if($H){$wj=[];foreach($Zg
as$lh=>$ge){if(isset($_GET["grant"]))$ge=array_filter($ge);$ge=array_keys($ge);if(isset($_GET["grant"]))$wj=array_diff(array_keys(array_filter($Zg[$lh],'strlen')),$ge);elseif($xh==$dh){$th=array_keys((array)$he[$lh]);$wj=array_diff($th,$ge);$ge=array_diff($ge,$th);unset($he[$lh]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$lh,$_)&&(!grant("REVOKE",$wj,$_[2]," ON $_[1] FROM $dh")||!grant("GRANT",$ge,$_[2]," ON $_[1] TO $dh"))){$H=false;break;}}}if($H&&isset($_GET["host"])){if($xh!=$dh)queries("DROP USER $xh");elseif(!isset($_GET["grant"])){foreach($he
as$lh=>$wj){if(preg_match('~^(.+)(\(.*\))?$~U',$lh,$_))grant("REVOKE",array_keys($wj),$_[2]," ON $_[1] FROM $dh");}}}if($H&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(263):lang(264)),$H);if($Qb)connection()->query("DROP USER $dh");}}page_header((isset($_GET["host"])?lang(40).": ".h("$ea@$_GET[host]"):lang(176)),$i,["privileges"=>['',lang(76)]],"",$hh,doc_link(['sql'=>"grant.html",'mariadb'=>"grant"]));$J=$_POST;if($J)$he=$Zg;else{$J=$_GET+["host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)")];$he[(DB==""||$he?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=[];}echo'<form method="post">
<table class="layout">
<tr><th>',lang(38),'<td><input name="host" data-maxlength="60" value="',h($J["host"]),'" autocapitalize="off">
<tr><th>',lang(40),'<td><input name="user" data-maxlength="80" value="',h($J["user"]),'" autocapitalize="off">
<tr><th>',lang(41),'<td><input name="pass" id="pass" value="',h($J["pass"]),'" autocomplete="new-password">
',($J["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$J["hashed"],lang(265),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(76);$q=0;foreach($he
as$lh=>$ge){echo'<th>'.($lh!="*.*"?"<input name='objects[$q]' value='".h($lh)."' size='10' autocapitalize='off'>":input_hidden("objects[$q]","*.*")."*.*");$q++;}echo"<tbody>\n";foreach([""=>"","Server Admin"=>lang(38),"Databases"=>lang(17),"Tables"=>lang(156),"Procedures"=>lang(266),]as$Ib=>$pc){foreach((array)$Qi[$Ib]as$Pi=>$yb){echo"<tr><td".($pc?">$pc<td":" colspan='2'").' lang="en" title="'.h($yb).'">'.h($Pi);$q=0;foreach($he
as$lh=>$ge){$A="'grants[$q][".h(strtoupper($Pi))."]'";$Y=$ge[strtoupper($Pi)];if($Ib=="Server Admin"&&$lh!=(isset($he["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$A><option><option value='1'".($Y?" selected":"").">".lang(267)."<option value='0'".($Y=="0"?" selected":"").">".lang(268)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$A value='1'".($Y?" checked":"").($Pi=="All privileges"?" id='grants-$q-all'":($Pi=="Grant option"?"":on('click','grantsClick',"grants-$q-all"))).">","</label>";$q++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(24),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(152),'\'',confirm(lang(206,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$i){$Cf=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$Cf++;}queries_redirect(ME."processlist=",lang(269,$Cf),$Cf||!$_POST["kill"]);}}page_header(lang(85),$i);echo'
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
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$u=indexes($a);$k=fields($a);$Td=column_foreign_keys($a);$sh=$S["Oid"];$yj=[];$d=[];$Oj=[];$Jh=[];$sl=null;foreach($k
as$v=>$j){$A=adminer()->fieldName($j);$Vg=html_entity_decode(strip_tags($A),ENT_QUOTES);if(isset($j["privileges"]["select"])&&$A!=""){$d[$v]=$Vg;if(is_shortable($j))$sl=adminer()->selectLengthProcess();}if(isset($j["privileges"]["where"])&&$A!="")$Oj[$v]=$Vg;if(isset($j["privileges"]["order"])&&$A!="")$Jh[$v]=$Vg;$yj+=$j["privileges"];}list($M,$p)=adminer()->selectColumnsProcess($d,$u);$M=array_unique($M);$p=array_unique($p);$qf=count($p)<count($M);$Z=adminer()->selectSearchProcess($k,$u,$S);$C=adminer()->selectOrderProcess($k,$u);$x=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Yl=>$J){$Ba=convert_field($k[key($J)]);$M=[$Ba?:idf_escape(key($J))];$Z[]=where_check(bracket_escape($Yl,true),$k);$I=driver()->select($a,$M,$Z,$M);if($I)echo
first($I->fetch_row());}exit;}$Mi=$am=[];foreach($u
as$t){if($t["type"]=="PRIMARY"){$Mi=array_flip($t["columns"]);$am=($M?$Mi:[]);foreach($am
as$v=>$X){if(in_array(idf_escape($v),$M))unset($am[$v]);}break;}}if($sh&&!$Mi){$Mi=$am=[$sh=>0];$u[]=["type"=>"PRIMARY","columns"=>[$sh]];}if($_POST&&!$i){$Gm=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$kb=[];foreach($_POST["check"]as$hb)$kb[]=where_check($hb,$k);$Gm[]="((".implode(") OR (",$kb)."))";}$Im=$Gm;$Gm=($Gm?"\nWHERE ".implode(" AND ",$Gm):"");if($_POST["export"]){save_settings(["output"=>$_POST["output"],"format"=>$_POST["format"]],"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Tj=($M?:["*"]);$Kb=convert_fields($d,$k,$M);if($Kb)$Tj[]=substr($Kb,2);$G="";if(is_array($_POST["check"])&&!$Mi){$Yd=implode(", ",$Tj)."\nFROM ".table($a);$ke=($p&&$qf?"\nGROUP BY ".implode(", ",$p):"").($C?"\nORDER BY ".implode(", ",$C):"");$Vl=[];foreach($_POST["check"]as$X)$Vl[]="(SELECT".limit($Yd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$k).$ke,1).")";$G=implode(" UNION ALL ",$Vl);}adminer()->dumpData($a,"table",$G,$Tj,$Im,($qf?$p:[]),$C);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Td)){if($_POST["save"]||$_POST["delete"]){$H=true;$oa=0;$Qa=false;$O=[];if(!$_POST["delete"]){foreach($k
as$A=>$X){$s=bracket_escape($A);if(isset($_POST["fields"][$s])||$_FILES["fields-$s"]){$X=process_input($k[$A]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($A)]=($X!==false?$X:idf_escape($A));}}}if($_POST["delete"]||$O){$G=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($Mi&&is_array($_POST["check"]))||$qf){$H=($_POST["delete"]?driver()->delete($a,$Gm):($_POST["clone"]?queries("INSERT $G$Gm".driver()->insertReturning($a)):driver()->update($a,$O,$Gm)));$oa=connection()->affected_rows;if(is_object($H))$oa+=$H->num_rows;}else{$Qa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$Fm="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$k);$H=($_POST["delete"]?driver()->delete($a,$Fm,1):($_POST["clone"]?queries("INSERT".limit1($a,$G,$Fm)):driver()->update($a,$O,$Fm,1)));if(!$H)break;$oa+=connection()->affected_rows;}if($Qa&&$H&&!driver()->commit())$H=false;}}$xg=lang(178,$oa);if($_POST["clone"]&&$H&&$oa==1){$Lf=last_id($H);if($Lf)$xg=lang(199," $Lf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$xg,$H);if($Qa)driver()->rollback();if(!$_POST["delete"]){$Di=(array)$_POST["fields"];edit_form($a,array_intersect_key($k,$Di),$Di,!$_POST["clone"],$i);page_footer();exit;}}elseif(!$_POST["import"]){$H=true;$oa=0;$Qa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Yl=>$J){$O=[];foreach($J
as$v=>$X){$v=bracket_escape($v,true);$O[idf_escape($v)]=(preg_match('~char|text~',$k[$v]["type"])||$X!=""?adminer()->processInput($k[$v],$X):"NULL");}$H=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Yl,true),$k),($qf||$Mi?0:1)," ");if(!$H)break;$oa+=connection()->affected_rows;}if($Qa)$H=$H&&driver()->commit();queries_redirect(remove_from_uri(),lang(178,$oa),$H);if($Qa)driver()->rollback();}else{save_settings(["format"=>$_POST["separator"]],"adminer_import");$Gd=get_file("csv_file",true);if(!is_string($Gd))$i=upload_error($Gd);elseif(!preg_match('~~u',$Gd))$i=lang(273);else{$vb=array_keys($k);$Yj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Ub=parse_csv($Gd,$Yj);$oa=count($Ub);driver()->begin();$K=[];foreach($Ub
as$v=>$wm){if(!$v&&!array_diff($wm,$vb)){$vb=$wm;$oa--;}else{$O=[];foreach($wm
as$q=>$rb)$O[idf_escape($vb[$q])]=($rb==""&&$k[$vb[$q]]["null"]?"NULL":q(csv_value($rb)));$K[]=$O;}}$H=(!$K||driver()->insertUpdate($a,$K,$Mi));if($H)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(274,$oa),$H);driver()->rollback();}}}}$al=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(59).": $al",$i,[],"",(!$k&&support("table")),($k?doc_link([JUSH=>driver()->tableHelp($a,is_view($S))]):""));$O=null;if(isset($yj["insert"])||!support("table")){$O="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$_)?$_[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$O
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$O);if(!$d&&support("table"))echo"<p class='error'>".lang(275)."\n";else{echo"<form id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$Oj,$u,$S);adminer()->selectOrderPrint($C,$Jh,$u);adminer()->selectLimitPrint($x);if($sl!==null)adminer()->selectLengthPrint($sl);adminer()->selectActionPrint($u);$xk=[];foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL")$xk[]=$X["val"];}$Bk=($xk&&$_SERVER["HTTP_SEC_FETCH_SITE"]!="same-origin"?hash_hmac('sha256',json_encode([$a,$xk]),$_SESSION["token"]):"");echo($Bk?input_hidden("sql_sig",$Bk):""),"</form>\n";if($Bk&&$_GET["sql_sig"]!==$Bk){echo"<p class='error'>".lang(125).' '.lang(126)."\n";page_footer();exit;}$D=$_GET["page"];$Wd=null;if($D=="last"){$Wd=get_val(count_rows($a,$Z,$qf,$p));$D=floor(max(0,intval($Wd)-1)/$x);}$Sj=$M;$je=$p;if(!$Sj){$Sj[]="*";$Kb=convert_fields($d,$k,$M);if($Kb)$Sj[]=substr($Kb,2);}foreach($M
as$v=>$X){$j=$k[idf_unescape($X)];if($j&&($Ba=convert_field($j)))$Sj[$v]="$Ba AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$v=>$X){if(isset($Sj[$v])&&$X["fun"])$Sj[$v].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$qf&&$am){foreach($am
as$v=>$X){$Sj[]=idf_escape($v);if($je)$je[]=idf_escape($v);}}$H=driver()->select($a,$Sj,$Z,$je,$C,$x,$D,true);if(!is_object($H))echo"<p class='error'>".(adminer()->error()?:lang(32))."\n";else{if(JUSH=="mssql"&&$D)$H->seek($x*$D);$Vc=[];$K=[];while($J=$H->fetch_assoc()){if($D&&JUSH=="oracle")unset($J["RNUM"]);$K[]=$J;}$ue=($x&&(support("cursor")?$_GET["next"]!="":count($K)>=$x));if(is_ajax()&&$ue)header("X-Next-Page: ".pagination_href($D+1));if($_GET["modify"]&&$K){$og=max_input_vars(count($K[0])+1,20);echo($og&&count($K)>$og?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form method='post' enctype='multipart/form-data'".on_upload_progress($gm).">\n";if($_GET["page"]!="last"&&$x&&$p&&$qf&&JUSH=="sql")$Wd=get_val(" SELECT FOUND_ROWS()");if(!$K)echo"<p class='message'>".lang(22)."\n";else{$Ma=adminer()->backwardKeys($a,$al);$uj=[];reset($M);foreach($K[0]as$v=>$X){if(!isset($am[$v])){$X=idx($_GET["columns"],key($M))?:[];$uj[$v]=["fun"=>$X["fun"],"col"=>($M?$X["col"]:$v)];next($M);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$p&&$M?"":"<td class='hover check sticky'><input type='checkbox' id='all-page' class='jsonly' title='".lang(276)."'".on('click','formCheck','^check').">");$Xg=[];$dj=1;foreach($uj
as$v=>$X){$j=$k[$X["col"]];$A=($j?adminer()->fieldName($j,$dj):($X["fun"]?($X["col"]!=""?h($X["col"]):"*"):h($v)));if($A!=""){$dj++;$Xg[$v]=$A;$c=idf_escape($v);$Ie=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($v);$pc="&desc[0]=1";$qk=preg_replace('~ DESC( NULLS LAST)?$~','',$C[0]);$sk=($qk==$c||$qk==$v);echo"<th id='th[".h(bracket_escape($v))."]'".($sk?" aria-sort='".($qk==$C[0]?"ascending":"descending")."'":"").">";$ce=apply_sql_function(h($X["fun"]),$A);$rk=isset($j["privileges"]["order"])||$X["fun"];echo($rk?"<a href='".h($Ie.($sk&&$qk==$C[0]?$pc:''))."'>$ce</a>":$ce),doc_link([JUSH=>idx($j,"doc")]);$wg=($rk?"<a href='".h($Ie.$pc)."' title='".lang(64)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($j["privileges"]["where"]))$wg
.="<a href='#fieldset-search' title='".lang(18)."' class='text jsonly'".on('click','selectSearch',$v)."> =</a>";echo($wg?"<span class='column'>$wg</span>":"");}}$Sf=[];if($_GET["modify"]){foreach($K
as$J){foreach($J
as$v=>$X)$Sf[$v]=max($Sf[$v],min(40,utf8_length((string)$X)));}}$De=[];$Ce=[];foreach((array)$_GET["where"]as$X){$X+=["col"=>"","op"=>"","val"=>""];$rb=$X["col"];$Nj=$X["val"];if(!is_array($Nj)&&($Nj!=""||preg_match('~NULL$~',$X["op"]))&&(!$X["op"]||in_array($X["op"],adminer()->operators($S)))){$Uf=strtr(preg_quote($Nj),["%"=>".*?","_"=>"."]);$ti=["LIKE %%"=>$Uf,"ILIKE %%"=>$Uf,"REGEXP"=>$Nj]+(JUSH=="pgsql"?["~"=>$Nj,"~*"=>$Nj]:[])+($rb!=""?[]:["="=>'^'.preg_quote($Nj).'\z',"IN"=>'^(?:'.implode("|",array_map('preg_quote',array_map('trim',explode(",",$Nj)))).')\z',"LIKE"=>"^$Uf\\z","ILIKE"=>"^$Uf\\z","FIND_IN_SET"=>'(?<=^|,)'.preg_quote($Nj).'(?=,|\z)',]);foreach(($rb!=""?[$rb=>$k[$rb]]:$k)as$A=>$j){if($rb!=""||is_searchable($j,$X)){$_h=$X["op"]?:(!preg_match('~'.text_type().'~',$j["type"])?"IN":(preg_match('~%~',$Nj)?"LIKE":"LIKE %%"));if(isset($ti[$_h])){$nb=preg_match('~^ILIKE|\*$~',$_h)||($_h!="~"&&preg_match('~^(sql|mssql|sqlite)$~',JUSH));$De[$A][]="(?".($nb?"i":"").":$ti[$_h])";}elseif($_h=="IS NULL"&&$rb=="")$Ce[$A]=true;}}}}echo($Ma?"<th>".lang(277):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($K,$Td)as$Rg=>$J){$Xl=unique_array($K[$Rg],$u);if(!$Xl){$Xl=[];foreach($K[$Rg]as$v=>$X){if(!in_array(idx(idx($uj,$v,[]),"fun"),driver()->grouping))$Xl[$v]=$X;}}$Yl="";$q=0;foreach($Xl
as$v=>$X){$tj=idx($uj,$v,[]);if($M&&$tj&&!isset($k[$tj["col"]]))continue;$ce=idx($tj,"fun","");$rb=($ce?$tj["col"]:$v);$j=(array)$k[$rb];$pf=is_blob($j);if(!$ce&&strlen($X)>64&&driver()->md5(idf_escape($rb),$j)){$ce="md5";$X=md5($pf?(string)driver()->value($X,$j):$X);}if($ce){$Yl
.="&fun[$q]=".url_escape($ce)."&col[$q]=".url_escape($rb).($X!==null?"&val[$q]=".url_escape($X===false?"f":$X):"");$q++;}else$Yl
.="&".($X!==null?"where[".url_escape(bracket_escape($rb))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($rb));}echo"<tr>".(!$p&&$M?"":"<td class='hover check sticky'>".($qf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Yl)."' class='edit'>".lang(278)."</a> ").checkbox("check[]",substr($Yl,1),in_array(substr($Yl,1),(array)$_POST["check"])));foreach($J
as$v=>$X){if(isset($Xg[$v])){$ce=$uj[$v]["fun"];$rb=$uj[$v]["col"];$j=(array)$k[$v];if($X!=""&&(!isset($Vc[$v])||$Vc[$v]!=""))$Vc[$v]=(is_mail($X)?$Xg[$v]:"");$y="";if(is_blob($j)&&$X!="")$y=ME.'download='.url_escape($a).'&field='.url_escape($v).$Yl;if(!$y&&$X!==null){foreach((array)$Td[$v]as$m){if(count($Td[$v])==1||end($m["source"])==$v){$y="";foreach($m["source"]as$q=>$tk)$y
.=where_link($q,$m["target"][$q],$K[$Rg][$tk]);$y=($m["db"]!=""?preg_replace('~&db=[^&]*~',"&db=".url_escape($m["db"]),ME):ME).'select='.url_escape($m["table"]).$y;if($m["ns"]!="")$y=preg_replace('~&ns=[^&]*~',"&ns=".url_escape($m["ns"]),$y);if(count($m["source"])==1)break;}}}if($ce=="count"&&$rb==""){$y=ME."select=".url_escape($a);$q=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Xl))$y
.=where_link($q++,$W["col"],$W["val"],$W["op"]);}foreach($Xl
as$_f=>$W){if(idx(idx($uj,$_f,[]),"fun")){$y="";break;}$y
.=where_link($q++,$_f,$W);}}$Je=select_value($X,$y,$j,$sl,($ce?[]:idx($De,$v,[])));if($X===null&&!$ce&&isset($Ce[$v]))$Je="<mark>$Je</mark>";$s=bracket_escape($Yl);$r=h("val[$s][".bracket_escape($v)."]");$Fi=idx(idx($_POST["val"],$s),bracket_escape($v));$dm=idx($j["privileges"],"update")&&!is_identity_always($j);$Qc=$K[$Rg][$v];$Rc=!is_array($J[$v])&&!is_blob($j)&&is_utf8($Qc)&&!$ce&&!$j["generated"]&&$dm;$U=($ce=="min"||$ce=="max"?$k[$rb]["type"]:$j["type"]);$rl=preg_match('~text|json|lob~',$U);$rf=preg_match(number_type(),$U)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$ce);echo"<td id='$r'".($rf&&($X===null||is_numeric(strip_tags($Je))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Rc&&$Qc!==null)||$Fi!==null){$pe=h($Fi!==null?$Fi:$Qc);echo">".($rl?"<textarea name='$r' cols='30' rows='".(substr_count($Qc,"\n")+1)."'>\n$pe</textarea>":"<input name='$r' value='$pe' size='$Sf[$v]'>");}else{$cg=strpos($Je,"<i>…</i>");$Pc=adminer()->editVal($Qc,$j);echo($dm?" data-text='".($cg?2:($rl?1:0))."'".($Rc?"":" data-warning='".lang(279)."'").($Rc&&!$cg&&html_entity_decode(strip_tags($Je),ENT_QUOTES,"UTF-8")!==$Pc?" data-value='".h($Pc)."'":""):"").">$Je";}}}if($Ma)echo"<td>";adminer()->backwardKeysPrint($Ma,$K[$Rg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$na=get_settings("adminer_import");if($K||$D||$ue){$nd=true;if($_GET["page"]!="last"){if(!$x||(count($K)<$x&&($K||!$D)))$Wd=($D?$D*$x:0)+count($K);elseif(JUSH!="sql"||!$qf){$Wd=($qf?null:found_rows($S,$Z));$nd=!driver()->hasEstimatedRows();if($Wd===null||(!$nd&&$Wd<max(1e4,2*($D+1)*$x))){$Wd=first(slow_query(count_rows($a,$Z,$qf,$p)));$nd=true;}}}if(!support("cursor"))$ue=(($Wd===false?count($K)+1:$Wd-$D*$x)>$x);$Yh=($x&&($ue||$D));if($Yh)echo($ue?'<p><a href="'.h(pagination_href($D+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(280)).'>'.lang(281).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Yh){$mg=($Wd===false?$D+($K?(count($K)>=$x?2:1):0):floor(($Wd-1)/$x));echo"<fieldset><legend>".lang(282)."</legend>";if(!support("cursor")){echo
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
';$Ud=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]||($c["col"]!=""&&!isset($k[$c["col"]]))){unset($Ud['sql']);break;}}if($Ud){print_fieldset("export",lang(81)." <span id='selected2'></span>");$Vh=adminer()->dumpOutput();echo($Vh?html_select("output",$Vh,$na["output"])." ":""),html_select("format",$Ud,$na["format"])," <input type='submit' name='export' value='".lang(81)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Vc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(80)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($gm?input_hidden(ini_get("session.upload_progress.name"),$gm):""),file_input(" name='csv_file'"," ".html_select("separator",["csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"],$na["format"])." <input type='submit' name='import' value='".lang(80)."'>".($gm?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$p&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?lang(87):lang(86));$xm=($P?adminer()->showStatus():adminer()->showVariables());if(!$xm)echo"<p class='message'>".lang(22)."\n";else{echo"<table>\n";foreach($xm
as$J){echo"<tr>";$v=array_shift($J);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($v)."</code>";foreach($J
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Qk=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];foreach(table_status()as$A=>$S){json_row("Comment-$A",h($S["Comment"]).($S["Error"]?" <span class='error'>".h($S["Error"])."</span>":""));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(["Engine","Collation"]as$v)json_row("$v-$A",h($S[$v]));foreach(array_keys($Qk+["Auto_increment"=>0,"Rows"=>0])as$v){if(array_key_exists($v,$S))json_row("$v-$A",format_status($S,$v));if($S[$v]!=""&&isset($Qk[$v]))$Qk[$v]+=($S["Engine"]!="InnoDB"||$v!="Data_free"?$S[$v]:0);}}}if(function_exists('Adminer\db_status'))$Qk=db_status();foreach($Qk
as$v=>$X)json_row("sum-$v",adminer()->formatSizeValue($X));json_row("");}else{foreach(count_tables(adminer()->databases(false))as$g=>$X){json_row("tables-$g",format_number($X));json_row("size-$g",db_size($g));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$tg=ME.(isset($_GET["select"])?"select=&":"");$jl=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($jl&&!$i&&!$_POST["search"]){$H=true;$xg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$H=truncate_tables($_POST["tables"]);$xg=lang(286);}elseif($_POST["move"]){$H=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$xg=lang(287);}elseif($_POST["copy"]){$H=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$xg=lang(288);}elseif($_POST["drop"]){if($_POST["views"])$H=drop_views($_POST["views"]);if($H&&$_POST["tables"])$H=drop_tables($_POST["tables"]);$xg=lang(289);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$J)$xg
.="<b>".h($R)."</b>: ".h($J["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("DBCC CHECKTABLE (".q(table($R)).") WITH TABLERESULTS")as$J)$xg
.="<b>".h($R)."</b>: ".h($J["MessageText"])."<br>";}}elseif(JUSH!="sql"){$H=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$xg=lang(290);}elseif(!$_POST["tables"])$xg=lang(19);elseif($H=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($J=$H->fetch_assoc())$xg
.="<b>".h($J["Table"])."</b>: ".h($J["Msg_text"])."<br>";}queries_redirect(relative_uri(),$xg,$H);}page_header(($_GET["ns"]==""?lang(17).": ".h(DB):lang(16).": ".h($_GET["ns"])),$i,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$C=$_GET["order"];$Zd=($C||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(291)."</h3>\n";$il=($Zd?table_status():tables_list());$Kd=$_GET["filter"];if($Kd!=""){foreach($il
as$R=>$P){if(stripos("$R",$Kd)===false)unset($il[$R]);}}if(!$il)echo"<p class='message'>".lang(19)."\n";else{echo"<form method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(15)." <span id='selected2'></span></legend><div>";$_h=idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%");echo
html_select("op",adminer()->operators(),$_h);$G=($_POST?$_POST["query"]:$_GET["query"]);echo" <input type='search' name='query' value='".h($G!=""?$G:$Kd)."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(18)."'>\n","</div></fieldset>\n";if(!$i&&(!$_POST||$_POST["search"])&&$G!="")search_tables($G,$_h);}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(175).'"'.on('click','formCheck','^(tables|views)\[').'>','<th class="sticky"'.(!$C&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($tg,0,-1)).'">'.lang(156).'</a>';$d=["Engine"=>[lang(292).doc_link(['sql'=>'storage-engines.html'])]];$ub=collations();if($ub)$d["Collation"]=[lang(147).doc_link(['sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/'])];if(function_exists('Adminer\alter_table'))$d["Data_length"]=[lang(293).doc_link(['sql'=>'show-table-status.html',]),"create",lang(50),];if(support("indexes"))$d["Index_length"]=[lang(294).doc_link(['sql'=>'show-table-status.html',]),"indexes",lang(159),];$d["Data_free"]=[lang(295).doc_link(['sql'=>'show-table-status.html']),"edit",lang(51)];if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=[lang(57).doc_link(['sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/']),"auto_increment=1&create",lang(50),];$d["Rows"]=[lang(296).doc_link(['sql'=>'show-table-status.html',]),"select",lang(47),];if(support("comment"))$d["Comment"]=[lang(56).doc_link(['sql'=>'show-table-status.html',]),];$Ca=['Engine','Collation','Comment'];foreach($d
as$v=>$c)echo"<th".($C==$v?" aria-sort='".(in_array($v,$Ca)?"ascending":"descending")."'":"")."><a href='".h($tg)."order=$v'>$c[0]</a>";echo"<tbody>\n";if($C){uasort($il,function($ga,$Ja)use($C,$Ca){$I=($ga[$C]<$Ja[$C]?-1:($ga[$C]>$Ja[$C]?1:0));return(in_array($C,$Ca)?$I:-$I);});}$T=0;$Qk=["Data_length"=>0,"Index_length"=>0,"Data_free"=>0];foreach($il
as$A=>$P){$_m=($Zd?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($Zd?$P:['Engine'=>$P]);$r=h("Table-".$A);echo'<tr><td class="hover">'.checkbox(($_m?"views[]":"tables[]"),$A,in_array("$A",$jl,true),"","","",$r),'<th class="sticky">'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($A)."' title='".lang(48)."' id='$r'>".h($A).'</a>':h($A));if($_m&&!preg_match('~materialized~i',$P['Engine'])){$xl=lang(155);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($A)."' title='".lang(49)."'>$xl</a>":$xl),"<td align='right'><a href='".h(ME)."select=".url_escape($A)."' title='".lang(47)."'>?</a>";if(support("comment"))echo'<td>'.h($P['Comment']);}else{if($Zd){foreach(array_keys($Qk)as$v)$Qk[$v]+=($P["Engine"]!="InnoDB"||$v!="Data_free"?idx($P,$v):0);}foreach($d
as$v=>$c){$r=" id='$v-".h($A)."'";echo($c[1]?"<td align='right'>".($v=="Index_length"&&$P["invalid_index"]?"<span class='error'>INVALID</span> ":"")."<a href='".h(ME."$c[1]=").url_escape($A)."'$r title='$c[2]'>".format_status($P,$v)."</a>":"<td$r>".h(idx($P,$v,'?')).($v=="Comment"&&$P["Error"]?" <span class='error'>".h($P["Error"])."</span>":""));}$T++;}echo"\n";}echo"<tr><td class='hover'><th class='sticky'>".lang(271,count($il)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),($ub?"<td>".h(db_collation(DB,$ub)):'');if($Zd&&function_exists('Adminer\db_status'))$Qk=db_status();foreach($Qk
as$v=>$Pk)echo($d[$v]?"<td align='right' id='sum-$v'>".($Zd?adminer()->formatSizeValue($Pk):""):"");echo"\n","</table>\n",($Zd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$tm="<input type='submit' value='".lang(297)."'".on_help("VACUUM")."> ";$Eh="<input type='submit' name='optimize' value='".lang(298)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$Ni=(JUSH=="sqlite"?$tm."<input type='submit' name='check' value='".lang(299)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$tm.$Eh:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(299)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(300)."'".on_help("ANALYZE TABLE")."> ".$Eh."<input type='submit' name='check' value='".lang(299)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(301)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(302)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(152)."'".confirm().on_help("DROP TABLE").">":"");echo($Ni?"<div class='footer'><div>\n<fieldset><legend>".lang(151)." <span id='selected'></span></legend><div>$Ni\n</div></fieldset>\n":"");$f=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($f)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(303)." <span id='selected3'></span></legend><div>";$g=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($f?html_select("target",$f,$g):'<input name="target" value="'.h($g).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(138)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(29)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(304)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(82)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(83)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(77)."</h3>\n";$Fj=routines();if($Fj){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(216).'<th>'.lang(55).'<th>'.lang(251)."<td class='hover'><tbody>\n";foreach($Fj
as$J){$A=($J["SPECIFIC_NAME"]==$J["ROUTINE_NAME"]?"":"&name=".url_escape($J["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($J["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($J["SPECIFIC_NAME"]).$A).'"'.' title="'.($J["DTD_IDENTIFIER"]=="trigger"?lang(166):lang(223)).'">'.h($J["ROUTINE_NAME"]).'</a>','<td>'.h($J["ROUTINE_TYPE"]),'<td>'.h($J["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($J["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($J["SPECIFIC_NAME"]).$A).'">'.lang(162)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(250).'</a>':'').'<a href="'.h(ME).'function=">'.lang(249)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(79)."</h3>\n";$K=get_rows("SHOW EVENTS");if($K){echo"<table>\n","<thead><tr><th>".lang(216)."<th>".lang(305)."<th>".lang(241)."<th>".lang(242)."<td class='hover'><tbody>\n";foreach($K
as$J)echo"<tr>","<th>".h($J["Name"]),"<td>".($J["Execute at"]?lang(306)."<td>".h($J["Execute at"]):lang(243)." ".h($J["Interval value"])." ".h($J["Interval field"])."<td>".h($J["Starts"])),"<td>".h($J["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($J["Name"]).'">'.lang(162).'</a>';echo"</table>\n";$kd=get_val("SELECT @@event_scheduler");if($kd&&$kd!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($kd)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(240)."</a>\n","</div>\n";}}}}page_footer();