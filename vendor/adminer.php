<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.0.2
*/namespace
Adminer;const
VERSION="6.0.2";error_reporting(24575);set_error_handler(function($Pc,$Rc){return!!preg_match('~^Undefined (array key|offset|index)~',$Rc);},E_WARNING|E_NOTICE);$ud=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($ud||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Lk=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Lk)$$X=$Lk;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Kb=adminer()->credentials();$J=Driver::connect($Kb[0],$Kb[1],$Kb[2]);return(is_object($J)?$J:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$of=substr($t,-1);return
str_replace($of.$of,$of,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($ya,$w,$i=null){return($ya&&array_key_exists($w,$ya)?$ya[$w]:$i);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$k,array$X){if(!isset($k["privileges"]["where"]))return
false;$U=$k["type"];$Pi=$X["val"];$Oa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Oa~",$U))return
false;if(preg_match(number_type(),$U)){$B='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$B.(preg_match('~IN$~',$X["op"])?"( *, *$B)*":'').'$~',$Pi);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$Pi);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$Pi);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$Pi);return
true;}function
remove_slashes(array$fl,$ud=false){$J=array();foreach($fl
as$w=>$X)$J[stripslashes($w)]=(is_array($X)?remove_slashes($X,$ud):($ud?$X:stripslashes($X)));return$J;}function
bracket_escape($t,$Ha=false){static$uk=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Ha?array_flip($uk):$uk));}function
url_escape($Q){static$uk=array();if(!$uk){$uk=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Za)$uk[$Za]=sprintf('%%%02X',ord($Za));for($r=0;$r<256;$r++){if($r<32||$r>126)$uk[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$Q,$uk);}function
min_version($il,$If="",$f=null){$f=connection($f);$cj=$f->server_info;if($If&&preg_match('~([\d.]+)-MariaDB~',$cj,$_)){$cj=$_[1];$il=$If;}return$il&&version_compare($cj,$il)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($Zg,$Y){return(function_exists('ini_set')?\ini_set($Zg,$Y):false);}function
ini_bool($Ie){$X=ini_get($Ie);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Ie){$X=ini_get($Ie);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$mh){$Lf=(int)ini_get("max_input_vars");return($Lf?(int)floor(($Lf-$mh)/$K):0);}function
max_input_vars_error(){$Ie="max_input_vars";return
lang(0,"<b>$Ie = ".ini_get($Ie)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($hl,$N,$V,$F){$_SESSION["pwds"][$hl][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$k=0,$yb=null){$yb=connection($yb);$I=$yb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$k]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$fj=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($fj)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$j="<p class='error'>"){$yb=connection($f);$J=array();$I=$yb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$J;}function
unique_array($K,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]){$J=array();foreach($u["columns"]as$w){if(!isset($K[$w]))continue
2;$J[$w]=$K[$w];}return$J;}}}function
escape_key($w){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$w,$_))return$_[1].idf_escape(idf_unescape($_[2])).$_[3];return
idf_escape($w);}function
where(array$Z,array$l=array()){$J=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$c=escape_key($w);$k=idx($l,$w,array());$od=$k["type"];$Ve=$k&&(is_blob($k)||preg_match('~binary~',$od));$J[]=$c.($Ve&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$od=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($od,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($k,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$od)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$J[]=escape_key($w)." IS NULL";return
implode(" AND ",$J);}function
where_columns(array$l){$J=array();foreach((array)$_GET["null"]as$w)$J[$w]=true;foreach((array)$_GET["where"]as$w=>$X){$w=bracket_escape($w,true);foreach($l
as$A=>$k){if($w==$A||strpos($w,idf_escape($A))!==false)$J[$A]=true;}}return$J;}function
where_check($X,array$l=array()){parse_str($X,$cb);remove_slashes(array(&$cb));return
where($cb,$l);}function
where_link($r,$c,$Y,$Wg="="){$Tg=($Y!==null?$Wg:"IS NULL");return"&where[$r][col]=".url_escape($c).($Tg!=first(adminer()->operators())?"&where[$r][op]=".url_escape($Tg):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$d,array$l,array$M=array()){$J="";foreach($d
as$w=>$X){if($M&&!in_array(idf_escape($w),$M))continue;$za=convert_field($l[$w]);if($za)$J
.=", $za AS ".idf_escape($w);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($A,$Y,$zf=2592000){header("Set-Cookie: $A=".rawurlencode($Y).($zf?"; expires=".gmdate("D, d M Y H:i:s",time()+$zf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($A=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($Tk,$Bb){$http_response_header=null;$Qc=array();set_error_handler(function($Pc,$j)use(&$Qc){$Qc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$J=file_get_contents($Tk,false,$Bb);restore_error_handler();$he=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($he,0,''),$_)?$_[1]:''),(array)$he,($J===false?implode("\n",$Qc):''),);}function
get_settings($Eb){parse_str($_COOKIE[$Eb],$gj);return$gj;}function
get_setting($w,$Eb="adminer_settings",$i=null){return
idx(get_settings($Eb),$w,$i);}function
save_settings(array$gj,$Eb="adminer_settings"){$Y=http_build_query($gj+get_settings($Eb));cookie($Eb,$Y);$_COOKIE[$Eb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($_d=false){$Wk=ini_bool("session.use_cookies");if(!$Wk||$_d){session_write_close();if($Wk&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($hl,$N,$V,$h=null){$Sk=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($hl=='mssql'||$hl=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Sk,$_);return"$_[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($hl!="server"||$N!=""?url_escape($hl)."=".url_escape($N)."&":"")."username=".url_escape($V).($h!=""?"&db=".url_escape($h):"").($_[2]?"&$_[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($z,$bg=null){if($bg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($z!==null?$z:$_SERVER["REQUEST_URI"]))][]=$bg;}if($z!==null){if($z=="")$z=".";header("Location: $z");exit;}}function
query_redirect($H,$z,$bg,$oi=true,$Yc=true,$jd=false,$hk=""){if($Yc){$zj=microtime(true);$jd=!connection()->query($H);$hk=format_time($zj);}$tj=($H?adminer()->messageQuery($H,$hk,$jd):"");if($jd){adminer()->error
.=adminer()->error().$tj.script("messagesPrint();")."<br>";return
false;}if($oi)redirect($z,$bg.$tj);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");return
connection()->query($H);}function
apply_queries($H,array$T,$Sc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Sc($R)))return
false;}return
true;}function
queries_redirect($z,$bg,$oi){$ji=implode("\n",Queries::$queries);$hk=format_time(Queries::$start);return
query_redirect($ji,$z,$bg,$oi,false,!$oi,$hk);}function
format_time($zj){return
lang(1,max(0,microtime(true)-$zj));}function
relative_uri($Sk=''){return
preg_replace_callback('~^[^?]*~',function($_){return
str_replace(":","%3A",$_[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($Sk?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($rh=""){return
substr(preg_replace("~(?<=[?&])($rh".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($A,$Yb=false){$qd=$_FILES[$A];if(!$qd)return
null;foreach($qd
as$w=>$X)$qd[$w]=(array)$X;$J=array();foreach($qd["error"]as$w=>$j){if($j)return$j;$m=$qd["name"][$w];$pk=$qd["tmp_name"][$w];$_b=file_get_contents($Yb&&preg_match('~\.gz$~',$m)?"compress.zlib://$pk":$pk);if($Yb){$zj=substr($_b,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$zj))$_b=iconv("utf-16","utf-8",$_b);elseif($zj=="\xEF\xBB\xBF")$_b=substr($_b,3);}$J[]=array($m,$_b);}return$J;}function
get_file($w,$Yb=false,$fc=""){$td=get_files($w,$Yb);if(!is_array($td))return$td;$J='';foreach($td
as$qd){$_b=$qd[1];$J
.=$_b;if($fc)$J
.=(preg_match("($fc\\s*\$)",$_b)?"":$fc)."\n\n";}return$J;}function
upload_error($j){$Tf=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(2).($Tf?" ".lang(3,$Tf):""):lang(4));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(5),$_);$kj=strlen($_[3]);$J=number_format($X,0,".","");$J=preg_replace('~\B(?=(\d{'.(strlen($_[2])?:$kj).'})*\d{'.$kj.'}$)~',$_[1],$J);return
strtr($J,preg_split('~~u',lang(6),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$w){$X=idx($S,$w,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$va=($w=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($va?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$kd=false){$J=table_status($R,$kd);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$n){foreach($n["source"]as$X)$J[$X][]=$n;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$A=bracket_escape($w,true);$J[$A]=array("field"=>$A,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($A==driver()->primary),);}return$J;}function
dump_headers($te,$rg=false){$J=adminer()->dumpHeaders($te,$rg);$oh=$_POST["output"];if($oh!="text"||$J=="tar"){$vb=($oh!="text"&&$oh!="file"&&preg_match('~^[0-9a-z]+$~',$oh)?".$oh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($te).".$J$vb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$Ck=$_POST["format"]=="tsv";foreach($K
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Ck?'\t':'[,;]|^$').'~',$X))$K[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($Ck?"\t":";")),$K)."\r\n";}function
parse_csv($Nb,$Xi){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Nb,$Jf);foreach($Jf[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$Xi]*)$Xi~",$K.$Xi,$Kf);$J[]=$Kf[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($p,$c){return($p?($p=="unixepoch"?"DATETIME($c, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$o=@fopen($m,"c+");if(!$o)return;@chmod($m,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Rb){rewind($o);fwrite($o,$Rb);ftruncate($o,strlen($Rb));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$ya){return
reset($ya);}function
password_file($Hb){$m=get_temp_dir()."/adminer.key";if(!$Hb&&!file_exists($m))return'';$o=file_open_lock($m);if(!$o)return'';$J=stream_get_contents($o);if(!$J){$J=rand_string();file_write_unlock($o,$J);}else
file_unlock($o);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$y,array$k,$fk){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$gf=array();foreach($X
as$W)$gf+=array_fill_keys(array_keys($W),null);foreach(array_keys($gf)as$ff)$J
.="<th>".h($ff);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($gf,$W)as$bl)$J
.="<td>".select_value($bl,$y,$k,$fk);}}else{foreach($X
as$ff=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($ff):"")."<td>".select_value($W,$y,$k,$fk);}return"<table>$J</table>";}if(!$y)$y=adminer()->selectLink($X,$k);if($y===null){if(is_mail($X))$y="mailto:$X";if(is_url($X))$y=$X;}$X=driver()->value($X,$k);$J=adminer()->editVal($X,$k);if($J!==null){if(!is_utf8($J))$J="\0";elseif($fk!=""&&is_shortable($k))$J=shorten_utf8($J,max(0,+$fk));else$J=h($J);}return
adminer()->selectVal($J,$y,$k,$X);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(7),array()));}function
is_mail($Gc){$Aa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$wc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Hh="$Aa+(\\.$Aa+)*@($wc?\\.)+$wc";return
is_string($Gc)&&preg_match("(^$Hh(,\\s*$Hh)*\$)i",$Gc);}function
is_url($Q){$wc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($wc?\\.)+$wc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_ipv6($ja){$q='[\da-f]{1,4}';$Ue='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($q:){7}$q|($q:){6}$Ue|(($q:)*$q)?::(($q:)*($q|$Ue))?)$~iD",$ja);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($pe){return(strpos($pe,":")!==false?"[$pe]":$pe);}function
server_parts(array$Bh){return
array("scheme"=>(string)$Bh["scheme"],"host"=>(string)$Bh["host"],"port"=>(string)$Bh["port"],"socket"=>(string)$Bh["socket"],"path"=>(string)$Bh["path"],);}function
parse_server($N){if($N=="")return
server_parts(array());if($N[0]==":"&&!is_ipv6($N)){$_i=substr($N,1);if(preg_match('~^\d+$~D',$_i))return
server_parts(array("port"=>$_i));return(preg_match('~^/[-\w.:/]*$~D',$_i)?server_parts(array("socket"=>$_i)):null);}$Ni="";if(preg_match('~^([-+.\w]+)://~',$N,$_)){$Ni=strtolower($_[1]);$N=substr($N,strlen($_[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$N,$_))return(is_ipv6($_[1])?server_parts(array("scheme"=>$Ni,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4])):null);if(is_ipv6($N))return
server_parts(array("scheme"=>$Ni,"host"=>$N));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$N,$_))return
server_parts(array("scheme"=>$Ni,"host"=>$_[1],"port"=>$_[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$N,$_)?server_parts(array("scheme"=>$Ni,"host"=>$_[1],"port"=>$_[3],"path"=>$_[4])):null);}function
count_rows($R,array$Z,$We,array$q){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($We&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$H":"SELECT COUNT(*)".($We?" FROM (SELECT 1$H GROUP BY ".implode(", ",$q).") x":$H));}function
slow_query($H){$h=adminer()->database();$ik=adminer()->queryTimeout();$lj=driver()->slowQuery($H,$ik);$f=null;if(!$lj&&support("kill")){$f=connect();if($f&&($h==""||$f->select_db($h))){$hf=number(get_val(connection_id(),0,$f));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$hf&token=".get_token()."'); }, 1000 * $ik);");}}ob_flush();flush();$J=@get_key_vals(($lj?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$mi=rand(1,1e6);return($mi^$_SESSION["token"]).":$mi";}function
verify_token(){list($qk,$mi)=explode(":",$_POST["token"]);return($mi^$_SESSION["token"])==$qk&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$lc=""){$ra=array_flip(str_split(compress_alphabet()));$vf=strlen($Q);$dl=($vf?13*($vf-1)/2-$ra[$Q[0]]:0);$Oa="";$_i=0;$Ai=0;for($r=1;$r<$vf;$r+=2){$_i=($_i<<13)+$ra[$Q[$r]]*93+$ra[$Q[$r+1]];$Ai+=13;while($Ai>=8&&$dl>=8){$Ai-=8;$dl-=8;$Oa
.=chr($_i>>$Ai);$_i&=(1<<$Ai)-1;}}if($Oa=="")return"";if($lc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$lc)),$Oa,ZLIB_FINISH);return($lc==""&&function_exists('gzinflate')?gzinflate($Oa):inflate($Oa,$lc));}function
inflate($Oa,$lc=""){$wf=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$xf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$pc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$rc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$lc;$G=0;do{$vd=inflate_bits($Oa,$G,1);$U=inflate_bits($Oa,$G,2);if(!$U){$G=($G+7)&~7;$vf=inflate_bits($Oa,$G,16);$G+=16;$J
.=substr($Oa,$G>>3,$vf);$G+=$vf<<3;}else{if($U==1){$Df=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$sc=array_fill(0,30,5);}else{$Cf=inflate_bits($Oa,$G,5)+257;$qc=inflate_bits($Oa,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$hg=array_fill(0,19,0);$gg=inflate_bits($Oa,$G,4)+4;for($r=0;$r<$gg;$r++)$hg[$D[$r]]=inflate_bits($Oa,$G,3);$ig=inflate_table($hg);$yf=array();while(count($yf)<$Cf+$qc){$Jj=inflate_symbol($Oa,$G,$ig);if($Jj==16)$yf=array_merge($yf,array_fill(0,inflate_bits($Oa,$G,2)+3,end($yf)));elseif($Jj==17)$yf=array_merge($yf,array_fill(0,inflate_bits($Oa,$G,3)+3,0));elseif($Jj==18)$yf=array_merge($yf,array_fill(0,inflate_bits($Oa,$G,7)+11,0));else$yf[]=$Jj;}$Df=array_slice($yf,0,$Cf);$sc=array_slice($yf,$Cf);}$Ef=inflate_table($Df);$uc=inflate_table($sc);while(($Jj=inflate_symbol($Oa,$G,$Ef))!=256){if($Jj<256)$J
.=chr($Jj);else{$vf=$wf[$Jj-257]+inflate_bits($Oa,$G,$xf[$Jj-257]);$tc=inflate_symbol($Oa,$G,$uc);$Lg=strlen($J)-$pc[$tc]-inflate_bits($Oa,$G,$rc[$tc]);for($r=0;$r<$vf;$r++)$J
.=$J[$Lg+$r];}}}}while(!$vd);return($lc==""?$J:substr($J,strlen($lc)));}function
inflate_bits($Oa,&$G,$Gb){$J=0;for($r=0;$r<$Gb;$r++){$J+=((ord($Oa[$G>>3])>>($G&7))&1)<<$r;$G++;}return$J;}function
inflate_table(array$yf){$R=array();$kb=0;for($Pa=1;$Pa<=max($yf);$Pa++){foreach($yf
as$Jj=>$vf){if($vf==$Pa){$R[$Pa][$kb]=$Jj;$kb++;}}$kb<<=1;}return$R;}function
inflate_symbol($Oa,&$G,array$R){$kb=0;$Pa=0;do{$kb=($kb<<1)+inflate_bits($Oa,$G,1);$Pa++;}while(!isset($R[$Pa][$kb]));return$R[$Pa][$kb];}function
script($qj,$tk="\n"){return"<script".nonce().">$qj</script>$tk";}function
script_src($Tk,$bc=false){return"<script src='".h($Tk)."'".nonce().($bc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Tc,$Zd,$wa=null){$xa=array();foreach(array_slice(func_get_args(),2)as$X)$xa[]=json_encode($X,256);return" data-on$Tc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Zd(".implode(", ",$xa).")")."'";}function
input_hidden($A,$Y=""){return"<input type='hidden' name='".h($A)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($A,$Y,$eb,$jf="",$b="",$jb="",$lf=""){$J="<input type='checkbox' name='$A' value='".h($Y)."'".($eb?" checked":"").($jf==""&&$jb?" class='$jb'":"").($lf?" aria-labelledby='$lf'":"").$b.">";return($jf!=""?"<label".($jb?" class='$jb'":"").">$J".h($jf)."</label>":$J);}function
optionlist($C,$Ui=null,$Xk=false){$J="";foreach($C
as$ff=>$W){$bh=array($ff=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($ff).'">';$bh=$W;}foreach($bh
as$w=>$X)$J
.='<option'.($Xk||is_string($w)?' value="'.h($w).'"':'').($Ui!==null&&($Xk||is_string($w)?(string)$w:$X)===$Ui?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($A,array$C,$Y="",$b="",$lf=""){static$jf=0;$kf="";if(!$lf&&substr($C[""],0,1)=="("){$jf++;$lf="label-$jf";$kf="<option value='' id='$lf'>".h($C[""]);unset($C[""]);}return"<select name='".h($A)."'".($lf?" aria-labelledby='$lf'":"")."$b>".$kf.optionlist($C,$Y)."</select>";}function
html_radios($A,array$C,$Y="",$Xi=""){$J="";foreach($C
as$w=>$X)$J
.="<label><input type='radio' name='".h($A)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$Xi";return$J;}function
confirm($bg=""){return
on('click','confirmClick',$bg?:lang(8));}function
print_fieldset($s,$uf,$ll=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$uf</a>","</legend>","<div id='fieldset-$s'".($ll?"":" class='hidden'").">\n";}function
bold($Ra,$jb=""){return($Ra?" class='active $jb'":($jb?" class='$jb'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Ob){return" ".($E==$Ob?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$fi,array$we=array(),$Wh=''){$J=false;foreach($fi
as$w=>$X){if(!in_array($w,$we)){if(is_array($X))hidden_fields($X,array(),$w);else{$J=true;echo
input_hidden(($Wh?$Wh."[$w]":$w),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$Rk){$Rk=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($Rk?on('submit','uploadProgress',ME."upload=$Rk",SESSION_NAME."=$Rk"):"");}function
file_input($b,$_i=""){$Nf="max_file_uploads";$Of=ini_get($Nf);$Tf="upload_max_filesize";$Uf=ini_bytes($Tf);$Th=ini_bytes("post_max_size");if($Th&&$Th<$Uf){$Tf="post_max_size";$Uf=$Th;}$Vf=ini_get($Tf);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$Of,lang(9,"$Nf = $Of"),$Uf,lang(9,"$Tf = $Vf")).">$_i":lang(10));}function
enum_input($U,$b,array$k,$Y,$Jc=""){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$Jf);$Wh=($k["type"]=="enum"?"val-":"");$eb=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($k["null"]&&$Wh?"<label><input type='$U'$b value='null'".($eb?" checked":"")."><i>$Jc</i></label>":"");foreach($Jf[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$eb=(is_array($Y)?in_array($Wh.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$b value='".h($Wh.$X)."'".($eb?' checked':'').'>'.h(adminer()->editVal($X,$k)).'</label>';}return$J;}function
input(array$k,$Y,$p,$Fa=false,$Pk=false){$A=h(bracket_escape($k["field"]));echo"<td class='function'>";$Oc=driver()->enumLength($k);if($Oc){$k["type"]="enum";$k["length"]=$Oc;}$C=($k["type"]=="enum"||$k["type"]=="set");if(is_array($Y)&&!$p&&!$C)$p="json";$df=($p=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($df&&$Y!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=json_encode(is_array($Y)?$Y:json_decode($Y),128|64|256);$zi=(JUSH=="mssql"&&$Pk&&$k["auto_increment"]);if($zi&&!$_POST["save"])$p=null;$Md=(isset($_GET["select"])||$zi?array("orig"=>lang(11)):array())+adminer()->editFunctions($k);$b=" name='fields[$A]".($C?"[]":"")."'".($Fa?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$R=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($Md[""])."<td>".adminer()->editInput($R,$k,$b,$Y);else{$be=(in_array($p,$Md)||isset($Md[$p]));$wd=0;foreach($Md
as$w=>$X){if($w===""||!$X)break;$wd++;}echo(count($Md)>1?"<select name='function[$A]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Md,$p===null||$be?$p:"")."</select>":h(reset($Md)))."<td".($wd&&count($Md)>1?on('input','skipOriginal',$wd):"").">";$Ke=adminer()->editInput($R,$k,$b,$Y);if($Ke!="")echo$Ke;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$b,$k,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$A'>";elseif($df)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($ek=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$Y)){if($ek&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$Gk=driver()->types();$Ek=$Gk[$k["type"]];if(preg_match('~date|time|year~',$k["type"])){$Gd=(preg_match('~time~',$k["type"])&&preg_match('~^\d+$~',$k["length"])?$k["length"]+1:0);$Wf=($Ek?$Ek+$Gd:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$_))$Wf=(preg_match("~binary~",$k["type"])?2:1)*$_[1]+($_[3]?1:0)+($_[2]&&!$k["unsigned"]?1:0);else$Wf=($Ek?$Ek+($k["unsigned"]?0:1):0);echo"<input".((!$be||$p==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!preg_match('~\[]~',$k["full_type"])?" type='number'":"")." value='".h($Y)."'".($Wf?" data-maxlength='$Wf'":"").(preg_match('~char|binary~',$k["type"])&&$Wf>20?" size='".($Wf>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$k,$Y),(count($Md)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$t=bracket_escape($k["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$qd=get_file("fields-$t");if(!is_string($qd))return
false;return
driver()->quoteBinary($qd);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($k["auto_increment"]&&$Y=="")return
null;if($k["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($k,$Y,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$Wi="<ul>\n";foreach(table_status('',true)as$R=>$S){$A=adminer()->tableName($S);if(isset($S["Engine"])&&$A!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array(),$S)),1));if(!$I||$I->fetch_row()){$bi="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$A</a>";echo"$Wi<li>".($I?$bi:"<p class='error'>$bi: ".adminer()->error())."\n";$Wi="";}}}echo($Wi?"<p class='message'>".lang(12):"</ul>")."\n";}function
on_help($ek,$jj=0){return
on('mouseover','helpMouseover',$ek,$jj).on('mouseout','helpMouseout');}function
on_help_value($vi="",$yi=""){return
on('mouseover','helpValueMouseover',$vi,$yi).on('mouseout','helpMouseout');}function
edit_form($R,array$l,$K,$Pk,$j='',$H='',$hk=''){$Nj=adminer()->tableName(table_status1($R,true));page_header(($Pk?lang(13):lang(14)),$j,array("select"=>array($R,$Nj)),$Nj);adminer()->editRowPrint($R,$l,$K,$Pk,$H,$hk);if($K===false){echo"<p class='error'>".lang(15)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Ec=false;$rl=($Pk&&!isset($_GET["select"])?where_columns($l):array());$Cb=(count($rl)!=count($l));if(!$Cb)$rl=array();if(!$l)echo"<p class='error'>".lang(16)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Fa=!$_POST;foreach($l
as$A=>$k){echo"<tr".($rl[$A]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($A));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$wi))$i=$wi[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$Y=($K!==null?($k["type"]=="set"&&is_array($K[$A])?implode(",",$K[$A]):(is_bool($K[$A])?+$K[$A]:$K[$A])):(!$Pk&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$k);if(($Pk&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($Y,'',$k,null);else{$Ec=true;$p=($_POST["save"]?idx($_POST["function"],bracket_escape($A),""):($Pk&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Pk&&$Y==$k["default"]&&preg_match('~^[\w.]+\(~',$Y))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p="now";}if($k["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Fa!==false)$Fa=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$Y,$p,$Fa,$Pk);if($Fa)$Fa=false;}}if(!fields($R)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Ec){echo"<input type='submit' value='".lang(17)."'>\n";if(!isset($_GET["select"])&&$Cb){$mc=($rl&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Pk?lang(18):lang(19))."' title='Ctrl+Shift+Enter'$mc".($Pk?on('click','ajaxForm',lang(20)):"").">\n";}}echo($Pk?"<input type='submit' name='delete' value='".lang(21)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Hh,$vf){return
str_repeat("$Hh{0,65535}",$vf/65535)."$Hh{0,".($vf%65535)."}";}function
shorten_utf8($Q,$vf=80,$Fj=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$vf).")($)?)u",$Q,$_))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$vf).")($)?)",$Q,$_);return(isset($_[2])?h($_[1]).$Fj:h(preg_replace('~\n[^\n]*\z~',"\n",$_[1]))."$Fj<i>…</i>");}function
icon($se,$A,$re,$kk,$b=""){return"<button ".($A?"type='submit' name='$A'":"draggable='true' tabindex='-1'")." title='".h($kk)."' class='icon icon-$se".($A?"":" jsonly")."'$b><span>$re</span></button>";}function
copy_icon(){$Fb=lang(22);return"<a href='' class='jsonly icon-copy' title='$Fb'><span>$Fb</span></a>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('%c(ADg~Z9.uC>]~.3$i#V4&_?UqjkCbUi6!n7_^@%:>!T#8;}*:pbFpd%ydIC""!*lpu2Rd:XrOM1l~#m;h*tmNtn-K%;J/^Y*Q4P1@XxoU:<QWC~Ad
*wPUjMZVuxYpXE#]<&s3ePkKT#dKk1E:Ul2tyYT$30t^mt2IE._i,ba[]QH;&^G>XeCRnZ7F0S>T&;8AGU1t~%>-+Z0WfbKWl>U:ijN_z+h1*84v9?R/g<"kr`70Q,eAMX30u"wX8T$kB(|"|H0:,cYV2.iD~aXAjj1$~>,`?&Z9_<(!
qrT<(,_y%ATmH^FSdYHTXuUHpb2R3nhiJ=6B9zbpQiHwSWo:Fy
iA)+i]r,Z>=ERflWaj|1)rr"HHn)9CNWV<s
a8s*vQw0Q)
vcwcD.Wb5!3Tg9BKn%bJD*5Q5t[^6tIF=@(|pe],Vv1:0?fqkYe|lVr.Aj%xiFt!D.)?#3j$)/DW!"l{6LO&dBI5$(J+")2,j`tT8]n:oKf
gw:;1
Q5qe+(QGKmV5*nc<OAd+7atqW^^@"(^<HoB^&aPW20o.#;m#HB0b<Ml+"8bxP$)XLG8g5H*^0:Lik[-D"
^U]w?_&s)x6G=#;<k.VW8&kOm!*%NL"g+}OvS;Fa`c3RY]s}&8(oFCWd=@G,
O;)3?53PGPO#~0PHvP8u$S#sj
>=?FH8{R^MV.mJf8l53,&#z`fuue1wS[?1]bVxc_:<c&);2C0@,GJ"PkZ,y^`3M+qdTaLdM1to%O)Se:5oM[e5&^_10$Vdl<q[_7z<6rht[Qa`l[wHxmSBSA+ljgc-Qo,(j?)yk(g+o,H4NAm1h27wS2!b>LL@]vD^W$Q+I^`]TUND2tRJ7vqf!NW*=$iZhrakYxxyrcaey2pJ)h!T?`=<{-+tc*lC?&
#/28r(1}W56^E@-"WSdAB%^h
lU3Ne^/KQHrW^w<4`Tq7|5<2.J%;JSC3B?5;|-N$pL2Ad4FgH"[9!6{^|)uh3X-Vrec8<.kP4w#`i,^X"AkN!dLS`5tqW-&"q!Yp
Nh<uHt;6PFruK^TBkkQ#*8@Q57e-&Xa!Xk]u7zK6J8tfByUB
&aao]/B-|W-2,U)iHM]b=j6)r*,s4E:>j
t.^7_hzF9glrNKW83b38O<~MM0PT*r
Y6VFVGx:m6R.y!s[LbxY`eN~Pv(?f5[M4=DZPTr#7xA}B*c|N?2:8KrdfH#+3W%ghLi]ipdyn,r;%}lJn6z!NvU"*eiRc7,a-Qg@J{m@9j^m^d$r/q+K&Cj}:[J3;5Syu[.@-06A/BRwlm_kRi,!<yCM+}Fs*U3$^WZ(XdbjMYuY`{omV|0Q2U61UDIm0p&j0eB<=SUkE!$3b?E[il65<R(c95Tv&jGxE9R_RT
amR4HIml08>-"uN=WQ9E_;yLmOm[ZF66Smep_,*l(J
Y>Tv4P+zc=QCe"UiN+wGmd0*0tt1tUO>Eu<MU34>P<1C0M&
v+nG$Pv(-P$k
wyAI9;o=f>E!*CM<[J9<p+B%p8CvW0Q3;+5WgIek|r-/.sYNwRcZ$%0"e(HP^p|i"l&p@;E^*8B-QBJ>A9cVP2GhB>@Ge51w*m~t*jR^I-]Um#P`-:|[*WEl^WF4%@uyY1vB/s=Jqx|6]%_I+AssSXW@AwD&K/|k22qOLfSYul;%eNI+vM%$mK~W)&P>w
zIh$68;.DhX.`B!ER6qc:=eu5%B3,_W)
6Z4x%|!7dId@!klmyN<>YW1CTA6@y$6@hUke3R*_W4[t@U@63Eqh"6D2Uo?
igD;U]8.by@dH7D&IS,+
tbJxyqMfshY)9GrH)iw!#daveO9Emn#/H5-GGaFIZYT"l7dg4
KRA"pU)@P7{e1o)T9o"MiX*+)*$?{.C@H?HL$jFoNJDu_(mv~a
u|DlC~w$(lIam7(Wu}v#qf99k78,c"Q,C
8vJSHp#zKuO4V$A=e=]?k
s|&2&rTR9.s.j=hGEvJNQ*:pj/!sC`6iIUA3TEVVxtt$23lk%aa$,Fh;#~O:%<jaBcY;oem}HY
a#c>1ea!xwfj%r}qcfXA:cGp)l#_F9D!R8=E;Z3e,Rn8-,@?DG3&CKkJ~#b;6Xm9GP!%YYQ3,LS@S;~caSMRUah9DN1`|Z_SGtY(H/WCLWl#qv~rN+:2Buwnd$[-/g9s?e
8x30)ipvppEZjd!Qo1
@im7".`@bFSRtCXNRZ#sb*X>ebIyY!EvG[<AHH68gA./R[wQYq$mB=E#NI/D,Y
ma$^U:5o_)b=i=J?X4:|m0aZ&Xj=eIV.!&Y0q4!@EWyI/&54t,+}gug($I4Ue1yJGjdF&Z<KZGH/QDdM=cBBxQk}i>*RioRgnC2Bfe)KT9cPD,sQjuN0L}+Fo86D#qaT"B;<pg@~j.WA_zk;CHS@Zw*LooeOvBtY<!!}_S%$8Q@$_^yN/-])<0"GJYYCkMH`4:Gx3~P{1w&E2mD)aOV)q4UGo/1Tf3"<amM-8(4IY<2p[]"+Us%LFJMmtBa2Brg
K$rY
MKlhpcQ^J0&K
)iLuwAhTgST{,*RnH$82>)8<oPilf)]tXT/g8&iPqwp8!rw}M*o$?Kl=z%S}[,!;a9c
2<qn;eLbkZBr[iQ^gPA4[(LFHNf+Oa5j@)QnyVHID!BWrq^*>$z#$Fyc-"iGyemj]]gwwf/]lSlWBY^=w>`[/~Qjz!9vi>sez(1zq$x:*-=yc)9vy6^#o}B-xcWIIUsY=%J2v}E@o2xayE7g+x`;WD=~w($ReBw8ZYY5c@x=Hm;|9Fgn*yNNGm(EUYxFJ(Z>-F3n_ZcdX!y4e63XwYyzS<Lw8#78=Fx]x(ofczkT58EI^Oe4@-A<FzuXo
Cb34#mnvP
tj4gqYsvig<5F=!5?D0WjlK+tUurt$gBBETm,*qs7W?
ssg$kU6vF:y#e"r/74V.(r=(@chK:QrP^zW((c/6piLBbu4yuqKiXkSZPGK1WaD>J&"j0<D6rRhq6v?I&yfP44r-_bgbw:cv]4DQclj97;WnR)VEZ.aVu98vhw-JUPC]<zc1UoOJk-,DJh^j.o>olI0)hI8tZBvhk<C5/|b@`7(s7uR6Z&9UIZs4qn`6@xshWG)vSKsA]R3<md(Q,L0Tx=DR]Dn6fHM!3)?DV.ey!5iu+1%15=5&,oJwX-P@xaYipy?xr}erY$[wMj/Mq[S=81lYcJxCBMY%>Ou*B8F-z)Avrm^/A))Ppn4!?vpaUGd*U&Qj3icSwcXMa|"2`K>&@ulfVfwcW-;VB.lru1aB^aaCSb%gq7GJ5$O!3n&o^YZ41?a4WXCPNM.uU"(5cINwBp89Y2fm<L*c!gCNOS[$n
`Xg$mo!GKUstOr?_fj<JNDqn./vM:;i2tI5VM)gamg5dT:5
T,Bn`:i6,ja2_tyup2mnE%rmV;)g`R%Sg)nCkriSY!i=Qzv:FX"`dG*wcrpNQ0LvnM8)G`Hhow^g!L?~)(f60;H0PGA/e`u@<NI>xpcSMVGqX8h5i7?FYar2>nurEU9C!AWyu>gtBJ%!pUfRoH3?eFD<t9xN30ydnM+[7N-o#_RAcRBZwnw=:aM^6r647vLgaLr|*q).SR[+X@E^7
KoPI-<];#>)4f}V_>;yPnUO8hx0[wuIetH4aa=!q+1%B63>.Q-^=D,GpExCK_M]o1}=kyf$jvdssieP";-`:bBi59cQ_@2nD1XaDIs8_/[h*Dyc%]uUYk#KD-VcsI
HIL9cyM]`:F<R]u9Abfcm":ywB@LyN.Hx;x>He[-3,Vj63ICGH.rE0]ya"$gU%Guw]"`EHxW&I2^(Me:.3lsd*@{2dE"QO5;fF0C[JV^i2F79>KhdP,3D0y":USGZcd[&H?:]0"w66^)$3:;-z3x<XdA><diNURoF!VSl5N~8g4Y>Ye),ib`AO"r!f3[PH_cCmF#03bokoW&s+$wG.lFKd[DPMYg9r/9Iv
zh|44nP9p.B"S(@rI)@d[+84%.!nbG{!OpLI}D5Y(X][rc}bHbHsCuiq
3#JrIf0N@h;hMe
WHSBd?3i[6]"x-~;7Sb9>O+o/P;#}1jAk*Xx
a4<w:A[(W.9sUPj,2
l&U[QN#uFR"#$LlS
sKka1e+n~B8Se`o$G^B5Rv-Jj4pd])n+6;|`ze/AQ&|KnS)vle7s<SHbQK3u-G{f
*(skt"dh6dwxjZ+S!/7s7l66kH89oj_L4L1@.?hC4qR)WVl6mYla=C#@%-UM`P=lX=%K]q<gAw$1xSsDU,9U1OF}KRUvHJvHQqbtkVp^%Oufw-n.
uJ5.v)P8YWp%Bk13ZV(2PaIHa%AO2f"_r:hJ/!)_b6]ne^oEI_tWxlN`JxF"|ju(5"o5XpmmJVEK2Ht)lqD:DEi+%h#/>.}f}RMjq*0Tw?
a/qb>`=k6ttPI0tP');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')OsbOb3V?!K0U*,j#-$TY2N&[`b!>wsTd_N`GuxPN9GOol*1@VDLlh_fdc430fu#lZ-r!f<.+=s=X(J2e>*"$r2geZo4@leYjQ1%,Ya^fK)KWrns9HN3Za[M&Ua[o)7sBH/u8kXg}4drw:$n$88?$
q.DLTGX#<D1t"V<MYp_Ma&R!lNy=^42%5+QTJ"M_zEIVt2b&@<iW5HXxa7"+HENrVp[-(?;l^q7O9Hb]:Sr
,WOw[;eXJ3/AYxWiY8v=afr;mm
2j7~=*!Bp~Z"dLH|e`)gkNjaXDNCg,tOd/Bee9aAhUna-ZLB;OF8<%r2e1x*xX$ZiG_Ot<kzJ%FMb$)(Q`hL2F*U3b$cI[XzX_yVm!=X`6&,RA>7e!9gn|F:S?FGgzw]+AWONX6E]$Hu$5^-Av"t[SRPD-dDP9jn"tZoFsSBWi!U
]MxVmGbSp6ix~D-FZ7DoJXY/zE9!l0/]_ZhqV=[.*yn"zS|U3V:p0%cK5pT+2_?0*<"/w-9$DgzF7#yWi<W,3"4>QoJftal+Tm>(PeM9JHTs;vxkWm9$<A7*iHsBl8Ig]>qQ38jy4P@0/ej$G,X[`Y>gf_|8q*^2Dnu#YI<#>h+;DK|$/DDimVm(m`WCVEYX1jS%84q"FCpAaU/4Yf
Q<ovd>ujL>jlSK$ADUHDsn1a>o@
;@5f]$+ZQNcbu-^=v>xaijt5[sMndunEa-5T28EWI"G!j1uhd)s:ch9c-:STXv8Dq82x=D]meVP[+d`LIY+k0"G?9H47
NBubq<z`![Z&|@7?P6j_[UcU{fnW0X^j_=5(,s<ii_zJS27M>X{xnK3M[W-rsA0k}H{mrK*vZ2&pNC@DA0;NWwLj&)j-eg5PfwA;O70]r,58hd_Eqn{Y@Ws+We9XpZFh)z(-@LIrbPy8da(hAcZV#?1X}E7dx7tw`28WL.XVqgdV!&yvq?3hO5.EHdr-kP>4[llRl9i0C+sj[+"u^v6Y#jXxd');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!c4]`nsZ51ptW"tBs^f-cSgTKbI1q0pX
i/S4_e4Ka%hwTAlP"L=t/*"6#@B,7~w6rZfJ#*=>a&]lq/*4>5`Fb9?Co~=jE4u%
t>cAt1}e8q9_RbAy12;Ux.tJDbc6yo|Zprh>hKzjob77ssF?pG<hX*bEjltaV`9(zy^usH40zkMM"McY
V707_X9U3bx~+F$);@OHaH?v1dKl#9q/(/yZS4k`G+bI@r2!9xKyf"jZ5>aZ(M+$*$2}jpf{p)^3l5
iJJ3I5BPJ"xlyc^fCDHS+dK1m1+vCx_MA/Wit:6_]`iROGku0;72%UMS";wlDHcc}X0i{+
+g)G*ZA!w>x/?aKbq;g^FuV#II^?l0j,3G78`XOn?+m4]3/!%^BW:hU]J4c2k=
iJM.o$C0bE-4W0vJI+9^e</7r4vYiOd.y43jS[^7j^Cs1uas2a7$Br,@HQ}V,1"k0J?:CEruov`sKV<v<d0[kjnjqvyd#_myug`xQiVE*y58#J~ynv8y&7XEdGPy%=Q^U$KhDh]L>]c@kj]:csEC^q*P}c9Hn,ESzuz&+rPNW!Kl13$Y1x$tt7T6=SO!]:HZ|(b&7%W7Z/21lKTXL<vyfmwIvfh"K3LK1s<BhsXV`TVV?(^I0cqp>.BZ9O4Wt*71Dh>6mNM2@Cx
f"0Y@.#f%F_@18hgQW.E/-thnF=r<Bl,tOY,6r(Z^*wd)#b7W6N)3U/Q3YJT>,bmG
NXP[y
fUMe{b=`4xTonAX`E@mZen38{K^gk7#!7.i[kr6@;DSaX1yw<aWu<wf
ssStyJFSYnjHv3YP`;B)5->v;nh3Y!G.zvQ"B.}i8d{5+Coo/%|K=KbC$0%9YySBke(UMx^4)@d*6cV;_y.IX]#b]ba]UCbb_iCDR
_lUE:7z[RXYo:O=V6l+iojgjWJWO1
<twp+?^2gc?@i;<MFs_)dJ"j,ih!XljxfR-_RT"j<2H.PVtI5YKbwyPq5MDXHJ}
27#:m*n"hsdc$ndn+
2^T=77Z@.rhk3pJY`"Gx/p#ZW?K>=`wi^I2hDf;0cgXAK@9-ga67rr?;QSv8vS]Y}[UaR.cGkAjR<R*Oe
,627rtV3)XJfBG`^Kip;DhELu.+:Vo33x)#u}-cbKJE3WN$bb_`x`w@>RP"^*ykdhTHe2Vuk9!C/oJCcUd*+Vo&t/nGo(Nh-s_A&hO)w^<N,z#Cs^LauK0d#:NWdpmBwT)v>~XtywX*^8"dD>j;S^us=?L0)yTndN+UR05x-+jcr^]ZThkdLiTP7<nAxcrsahb}DY[&4|ZnAu7hTys0
b(}s{Bk-?30?{YmI}bzN
`q"IEsrfUE1ODTJHO8I>s%")3&s<U,%+.e/*9gXQC>?DGPg5k1^;iRD^l=.Ka3/gqtfqs4*KPiw/LRCbMRq>]/t!c:w/x^F]yuMB
F/1&t,t/<
>vmOJ;Q$[AF`{F6OIWPqW;`Y+uB2MgX<oL3E)Ie`ojg5zL<u?nTT_K=LgN?efssrtCuvmHd4u_ftW&`wNnB7_ggN1DpxT]mI
kk/:72=%GgC%av7fY#00oymXT-JAwbgK4."9E:&|e@:4iQ^@)+S7?$CLlI7D!/gdMOZ4o,<z:[IT/`v^>pv7)rG_dxKMD^NxvDfv-:LG`$k`)gWXeP[n^-Yht~7cq<SzjqrRTRFFY@&(3&XD
NqOJC8)ZLu%PXwG#])x1Y1D%Of8gU;S5txN?plO&A7Dv`0nTu0P]awFPmaDiG?i:TgJLAbdb.k+P[>EOE+j6]:LE{ZxQ8u9
B$63P@,MdTS.wsVPl4r#
X|yT03YF[0M9I-?Mc>`VPy`2.kxRPyd7Hav9ymiLP`I2E1Kqu0/e$OUIwI!}2GpHiMVp*riC4T&DC*D@pI19J/Vk_IJqm
EeaocVyB/p
#Ex;fA+Lj]4m(+{c&&g*Y2L<T7:da81X&78mf_+rXom[IBnc*l4[_I=MR3u*}^=BQ6{B>R2.@XuL=rgo#_{ny+"HRB1;"tvC;S%ki3|CQM3ojRyq[Z!psqn
!JiK.$B!8v(9bF|X6e_PB)mf1=%9ClZOvJ+A@*7%<.W7"xH@U/RH]!a0`/{t]ePk2%8sS:R<*>|:|t_kNoaa,En.DlX<|7##~8@!mv40MNPn.r(+9atk|^jJ^Of9I.eJ:pmIIgI_$7TyXqdSQQ)<1e@SGFe]"+JQMHc?I23j,SY!>,a$9mq"&"$>w6~*@3@01q:QI.F*)[#Fie?sn;OU5g;i-n@g4?m-(nQu>,Am/$^1n7I<iPV<nP0kp(EK~ZG[KQ"+,T):.hkG)@*k@ljU
nzMlEdN3EMcGg%PxGw;Z>e^vu0rE%?(LC5$9<pA:Mwqp_]"e[*!C]CqxtZG2$hp&Q0CN2xx9:Yc}2Kt=+/]wlt!NV!/zj:dyBNj)
Gv0j;oM8O]ra*hDVs*>krd>CSMpQY<1H^S`iw?-Ny#=J6T+P<j/i}#eg"e`N6E;oH$SPr]6h>@sZ{tKK*uh)*If({YONDiFvQL;8!tX]i;48Re;>=;C;u/}5QJD:L&e+=u#uXe&w_OeDQ2$2Ujl>lWJu48`"[MaolYT!3VDJJ:h-5OiQvN"t"y3O38=7xtWk&&9%+v_NU>J]P1o5s?!q+c}BM?,eF(xPX5`dN%s$_0B$A4+BGeJU_#5nbg?
giRqFUeTU7r,5c1&Mu4A6?_qqMuEz1t%Z"WkggbTTs+6Dn4u<ga9u[{Ouu.q@(1T@_rBh<n%2Z]*wuH5$#-e#YcjBg{:Z)
ozqqCyZL"x6@=LJLK="kft8E%Cx!/3_D,]v>:J36079wA)8-eW2;wzo-t{rUn>yGokL#ZmG&+c2kqIToBU?M24.
-0Rb,J"sw&$uw}6i#M+pFM>4J(L787N;
/b;8bwR=Hp*H`Bw01@UHIuG%w%>S;Vi?)7YmE`Z2@ebd2.7!wl9-^=MQm`(/QPZP,`<5B$?Nv74U^="3|.F)1Zy9ykVT1P*^t^X.i?,-tiE>,e|u>=8+Lrb!#OFs<V_WPqsq#Y8NS*M+g0`JM1Q`nSZ@">H<_
`!Lov@ZACjI?sYLXSS33<(N</),*XvGjUT
^?ZDIWgTv9@*=W@il1ddlS+2
g!u?If`25_4(!Ap:"F=S{lyPJS!1SZ@->lqnFBjX(2mFY`#q47awxv-E>f18y^Q-
]Wh[&k;i<=xh.,lfb}UxiP0C^l9eH<!B:+HxAS&N`R4!T8&21KEuv~sG
y]oo~
#l&sCnsuPv`Keq%rYGHI|Vw$B%cKLcVZYJj!"Vq10M7%[3D.4CEv?8R;^.Bgnu8_+${iZOn/w9)9Q=]*K$|&M2G]ic7pZ@YVqSI6^-5E9(L;~9+D|#0`Rb|8TCcVS
d%UK7B_VhyhmN%F(sUab#Ha@x7XOV?f%KejH7!+JhYJBV3B*x=6No"YSR?/+,Y9em4!C3*Z!LsSnxs
?:UWx=L98EBf"-Z*5h&Dad`d;W#w^[:C:jpntu!,3c!EAdr?wBNiq_:P8zeWknpS:p*9cjE*I<<p+0UtRyW|])*):_mFJYA(?QK|e8G,.g8Q;*kNoa]
B>Bo>@.IBBo
>o;EI@7O,s4,2Z%T9[[%/;=IG;kFWov{W]P%_M3tI;X_`u.m9lq"W"8~o2RaD-Bv5]f1)q.3O{DqGRfEF(u?yd".Z!myVL;.UpHuI/rlg6-d-DbE)`#p$N*V3BniH0f9-nZLs87Uh.[deHiPGY2PM{TPm|&Phwl?EuXa4WuBQ4Fhg+VQJG%@V8JH6h?PXf(VB9a~@pPWgl!.oCrwoP"O5y?qiU@CrSg@^!e:KmvT"W4)::e,I+7E4Am]Gu6*V0S[;k0v1_ZrM_mO<@g5`>Fu_sP4Nl.]x4l}-X%H2Rhkoj<2A9dKwQM&15=,s+f%E|GT!O&/aA`:Em^mas=BAkV(nU8E<}n*j$tD%mPS:s&CMA0VrdF:2h*,kzMZiG+9!b:@X>HiFt9}X1c4tp]vLX@=5E
62/We2+*b)2fJ-dX:uY-+:hpmhr`uWF2:;jJ0V"-`*!67*4`Y?Cj6rkhHVs$`AV?RV2Skr:<!O*+0*CG(%<<5W8UDmZT/)j*zB&^#a6[!tAqdd$:=ufG~3
_aRvE"Wi^Pwz*"8F#BM:^;=3CF<[m/gSO-AEPBpO;o6]AzkK3q,8t[Nx/b%su=#P^zyJ3>gJAq`=o)6n&A#NPCV6&00Vi./WcN"dv:2xXP0aI&Hp7IT`lz`qZSf8x2oJX1LN[l,"$eO.;++?c!2804Nhg-"G$,`b-{$7CU@6PLcX`<YwlIrr5&0.^ly`"9dH2_px1M6w0r3cpp6TX&N10`b}*HZ"uD$T!Tws.I5sjBtNEtMn+R+o(N3_ewgn)A(@/.;J/iQDjt9Y]LEF2M*=^Mo)n-=-L@whsdAZvAg+3%+&]A=!Ckw}!un-]-b3A8dX4|jK+vCx(ACR7
)Kq]HP&cVU%=vK=vWt-/rpJWGA*ENv_hQj0"nT1SkH&l$T4:D*wekT>,*^cmLsoHg(G~dwLZ&vF|beXh4ASK";eV2tS0t+rs"T`7_1(`xmx0iHx73n"kT~;Kac+|hA4kQ?wg_xdX@>>BZph+;?Icf9BYf.W!N(P=qL>PPmZDl,Xy*<SsDQA1*SdwoIq2YN&#PHixZ=.TJr
vh$Q=F:*weX)
gb:!n*,QT^1tC{`3:5/1exF5
pM=-lNw1Vpuu-uqol8tg!2!PLQ#uHV`J?o(ue?_O-03JVg#s#cV/vuiwGun%:0acmf4/mP:ol@d"Ia<Rg)DZuV&jyw):)@mP8Dp)#W$yQ
iI34
^W@)`uP[uI)ivMoXwR+nugY2f!Irr?G!g,E.H6orN|P1[j5LTTAlluoYyyfzL<#aZXt:a|nK>MF8tuhaod.F7R0G2Xb}4]
$=F[GIMM;a2:iKg]fwWt?/@wZsJW"sZ&9PhsL%b+cgVG.d"n+g=4`jMUoC/?u6
VSk{,>ksAyX:M4^K1JI{!c#rtgPyniNlLvb@C}j^/z:n!k6Y0)Oqv}&&X?I;5dk7kgy:@7USe`,0pUL6Z[6U0k=k640d?<QXuYU"B&w6&B$-I
1@(
RI;/XSoSd
2Xq!G/t]_JEl,F7,V5xA?T?sK/v6"e9$UEq[;ohCe)jQG;RHEV;i?V70Gjx+lfaN;#YwJ?^}*adDBCRacXwE#Xu=u!k)gt!r%@w>B9^9w8,NC$s3Ba-)2u*HhE$vGv"aJ9+{ja02#GQ!b9NDaENzYY#X1{Knsm+Hs2H[;:dD$.)di|Ivti=Lv[Q=GJEE^MF$GCF"*vuk:y`[u9#W>BSVj=M)&KA,W#30"^94Ih8nFgdJT=NJP5Q{U{d9f7d4=KXZEDX
E_e%iZDT1AkWfX@K0"<nTFw<?}9.n>h{Re^&U~PaCMWvvj,h
hmr<^-8U72J<EDhkn;26_b?xwVDR1n#dD`8ZY.]a}G*F_^xtc4
D!2rn-1MZT<*D(8aZcb/pu^zGq(a/S=a)S6qC;FWOa=]alyeN?$;>3q5XVKkHIX,/2a{iaUG@tFvAPAoRe%/@2:q1gqY4`Qt;O:eK/_VC{C},?fjZ>
84)8!R,3A*ACG(C`r;W!HIO1Kf-EuDc%h9pYY0^v!Py^}GVti*uR%6@aM#qXfWEdC0mE[nZ_5,JA`F1!}dT!*$E!]94>b?Nb"t*SxPGlj]|FqHjnVJm5K3lFVGV)L_*${-iq4)kg{W#T[6A!k#phA6@P,pZD>QqfH!s8Z`*$7MJjcb$Q=QTvT*>K("Kt~@#goS>_5/7)7GXq4Bh;9G|CN9|1a7WH_By2L<&$>UdiZ
3fE3,u!iQDSl4T-8cI*71@laZjsnF6=$lqUeac4>L<kbn_4C|N5[{Eqo^W{>p3`-e(MYkcG@;*wxBOsaV:z+dGjrnEK>Ecq#9GUx:5l,@$vpQg6N>aLGnyo,yHB#<s6qN,3T*7HMHO`a5yXJ=S@9K2KkZ2-5|wmo<N8Kpn;+Hx+LB26@l+Uhg6}w|Q:Q<J~#<YR:{E4sHK9)=A4"5rr1t/]*EdU^TLtyAwXrsRABLjP(Z5N9Sio+f6%3_mBJGTevT5G-/w&Y2W/A@D"q%1LHfYU^k0TJ9R#H>s@<&]&Tz$K.IP})aN7p9E2W]TBJhhP&=BVh{gFK.-St_)
Z2J[%N@
c%Duy<PTbL.2D1
^$sfEi"SF^~br]]/*wk0V:%-hy4<5+PsxefHVCFJ@,,;=jP7}g7A*;F5mR^wLP4R_0"):+r;42o[+9lI1EAHO#~8UKU8=O+"05Pp_=sl+Y-N0GPEy<?PMXbCRlX]C(Umom:puI5$2Y0upVj-5lD,j!3
0Z{QFekr/WZsDg;v)L0(X,8oo+{fUru#|g6g+[,cd-A$nA!JEVFT5SzJb&jywVrev:,51L)%?Wt3b6!
P"Ng0sWxK)3/TR|?vX`eC;XjBaU8x,>@1#r%?!#&v&w!980[nmn!W4xI$%},5!YsOr<j#Pu;wu0s^0t](t@y
?1GTKyGd;s.fMzac
GCb2IW(sLV
b,G5_J/Cp>
?$=8T[APojU>G7&^p-GY*O8>&gxJ!Tm/52Tr~^OxDy=9qihGR)/a~C?+`c6bJ(A2+,P9ir^jtc]F}@;o!aV3!j~=0^"u4lPr|ZWyr6.,l,QlGk%]94Lt&uoqHY
mdVEbF,{6!)i9xO]*c,h%BOK3P":,Mw.*^17-nQ!TU^9#hB(5lr(;*t7Yp4l>I/N.#=1:qJCfQ$7AwYdE<M!,qXc*1>JN"AiHRA
2Ld~?;NxbU&oi{l;0WIxKPwtc6)&%r+LEZ
eHkn30z^.avoQN2sSxGIh-SZ7E&Zs0AXgF|jZKFE2kx[[TOKrlrZhOa)|pL)!tIU=kQIR$VW/[-Z5B-j&+XaAxRO^H+
$JH
,a^h/FB>ehD@F[^9fvN"&.cpt0AmIPaGO`KOng#WabMvJ<r*NATf%wNL|n)qtZI
TD]Q4*Ww+7Al%tbS+wB?o-!.e)hqGpxYxG@P|jLKU
~-e%tNgR#AC]aqhf+u@.W7imBW)/VPc/sB5!&MF_R@0ito65
7@kZuJ*[:<0zRD>)c1Hem#1ZL&]&MN7q"mTmQ-&Q(+UA"wT9F9yA=rgtAv7[S~[M@3#)G0)d;KXYCaH1N?Fwuz46QV
(^*XA1Es^OST1(aU<#
4aUsuJTe3~kpX2`_HrDRL7r2),A$mohB=2sC(d<lW.;|PCQHrF@E1]7IivK)_Obd!5f<uNq3aZ&urmetKsNtyHecc%@;jD5`;%p4k+dl;S0y=udJQ*ivvxK~+qN_NP?pE7m}lw1i;%dnN*^aWf
$12uX%sf@F)t[U?+c#bO+ddeg>b0dc;D^y^s_^|Ug-?Kv8qqbh%pCw]$*lGtsr1^+LP=(8dDKn+?Qc!E)4|;B8Q+m<6uE&j]#%v[oyNpXTbdI"V,0mXB4@A9`Z?Amn<]G9~kF/kg,!@KQc2Me6:g}xPU,L}BuLoJHw2RdE6WL2/4W5o5Gdij:*fG,%zQ39&^:kw?o&3DQKX/
x?"^s
<n*%-x;11!@UG#RH8(76o"u,#dZb&3kaUAUtC};U,}h-&d03E3EMYDkfwy7(/phD(`e%gyiDc0Ls1eKGRG^I^3eN2&r?/oQm*Uj"uYNMY3$?@zACd~gX]H@J*Aa2Tk6S,2&ub)fBtkPWI?S*4,DGCbR?gQe}nA0nRmW((O5f99ty@{74^3F{`4%ev08Q2%uxEJ_(Bai$JO@WPwT%)q2"d.
oL3Edd}JUeh[r]ZXw8io(oj_/E.EKnLYes,lWkz2!qo!(r`^1WEK8$0AV)Xf[,r!T9bx#m4fGYx[>[BucXG4rLS!3Av`j^!X}_%uqa`QwQCcXZDwP2GF^
#3=Q
T[>FlXF{i|UOg]+RSiVUf%h"0y9LR?BG`z2xiJ65=JcU`itxogvx_/d@T(wKeBj5mF2X)d53OUQ`@=qJHpVF]qkC
%yR`BEF8<#%oyl9ErJKVW1z;RSju=.WT0

KPn-g+c4cKnyyBL}yEyoui`SH&1D3+OA[S0Wj6"HEL"evBx:F>cU=
ofn_8,]Cc`^ge7#,fSvj0%"7X*EkWzpJu7JETGt,yzs93u!2>2m5L+vp1e7|JYP8,C2gvz]lbvdu47a.Ki]h^o/f!X/K(P,2PwQp4ySL:RObaNNi<3,R*8ry)f9[ffo>"C)@./oK$o`/eD
-B0MBG*(9q7!u2-HrB,;ftR8t*U:|#@&*iNDSD:
PV/tW9`i:,w-dJLSCp;SH_&x5/4.8RERA`o`l3pWT^_bMps#e:xT;0ndw-;Aa,&f,Ehm[OwIr!qe$,ap=;"ezcZ=M)gQrq"XN%HBtn|s#oX2xf!yW]J>+U0Kb%NcwW|l:W>:If>2=`wp=i
^F2t8tg;Kd$4"YjnTipgr_8Z$
gR@
Sp3<RhC<GRoZRbilYaMor.2&o[cEv94^u2B%];`yL(QYbz#G$F.sb;C5@sidjfD)$SDV0j/LcP#7fpOB__hT:S%j(k?>V$5|u+e"&~#Jrsn!lO6^#qq!rZ&dl|<_3FH:/4>r-NNl
moY+U(eStRsfvLH9o
UN
/v3U/s?4WFw+k)ceHzIdkie7Vq;jj__o<LqlyH!|#
hNJ<:!Vk3b5F8=Rz7B9Yh#mW:7"|4:bKUF%.OFV_y-%uX7^&g*b`D8^u4WK!=%RfB%J!"L33`b3,f2o
??`6*1RCY@m}i*rb^8LTtw@?F~i%cegw"9<7KAUVph3=u@
/E_dxlJVT/HO0%SbK2{O%1i3Zb<q)r4CM=MpQ$6NV*(ao%w#9cGh{-h4.NnT[8+
]G7<gv#8MaMeE0fdRLvqkT8d2RuY)7*_&ctbn8xfUYx*g>4l*lCAG0OL0HY5!-",B1hy]1">q#RUU)UR@vytZr~4webt"PthTZ2_HYunw@.vQD8U9)9n50KdoE"N%C#&MC]/l-S;k`@tH%16)pt3w.}3@_%>.^<MQOC"a[OA_%EGzIe=Te^g{VWj*xv-kutVxieq&JU1<Ajf*mp<Lc~<nFKm[Cj8h=#o{+%.%@tZ"Y*&x&xc8pb@DF{GLg.+}62OXd;TNicAgq9vO[B#NmpH{](xTs24RDTO6rD7n[r,:,k.p;sQq5xDm%B1jGf,8tqs4W0js<4i"QJYm(v96`-vYSLi_ToaR&]vQa-&-C%Pv6IAed!O%g~&;F3nRxwCC##h8w]%eF[AFUg:`jbE6xT[5xv4,Rn5Cf|t|Vi<;F?"|AmkLAC_ZJIZNMIe1(:/nNcpJtW#T2q;nvg9L+bh
(5q8;jF+rp)r35,3T@)x`M#eVnU[25y"V`i,rS&V23<|rkC.!,$jmx8:Z-$AQz6zLiFTOJ5K(0iGQ:Opt-HG2xW6.lT?U!#miHWRCP8GRqtT!DpbI3/ehvIn+_cLG6AG%dj"gy9x@*n:?lC<9tGHWG$opdl0^vPw$pM1azA-V5cB-fyUE#]a"p8H
~.^mJ/?53076Bn!":]5Jo$L<32MLnUG_2s=JtgDg|(M3bsFcn>=SmJ~jUH*t*[`scF*Gy6SH`j2]`G0/q(x@-o?Ud`Yo2oHwYi6?(Ni%W/"]J:G-xnH;IkI=O;|rG<UBi/UHElAxlBqrl%wBbiZ;sK;3G37Bwwm*nngSV/d/<m(&SiGWA_VAJ<;"tV=+=73)+ASGa#hws"$qM+RD"RUm&uK",S`G6ZusED@gu1|&q!Hl4G(#+,`Rp/uHE#Jq>T-%NjhjRxvbo_<**@qgWmiaabTTF!wjvAGS?T*6sdFZSGWc*ntB#jo#AF[3f50G|E<hzq0I25v8.1!%GIwJD8ZD`.4H1UPo,Z9b+F;V^H|YtvEH"<=I,Hy8a5nb".4vxGvO?&>HpvTpN5NWq.9-Z([lZDDm!Vptk<<yX`^qJ%e@g8vAUH"a)FteMjvO+=;B9xg7D5/!!<Ak!7KQYn7La5>*<B3TH&,8jNqQ"uRNl)+t=vg5)1p]-6EZbp*B2`|CRMkw+[`LUrYr#p@@)mxg73cfEkqA2@qA8c{_EH(T+yKT!Wu3ZEtj-fqT^;z#2]L+V^![(
6`fwJf2d6Ocu9S5%1YnTx4^TlE4)$Q3ciGU*fy5oc3|)_%4WE!Ft+2Za+K1B%9OAoD^/jk*"~Aij|0n9huBIyav=}299%qC&5-(G_0}eB`Bo&OPm,Vlf;mRj(_nw!!h[QKd&d`TY>mEWq?9&l"x)k-huiQ5(TOBT>N3I$m).x8eM:2y!c9H-d)(k@9r#vC,nG,>qpcz[j:9+
TY6(`kvX,,<qC
yG?+:cK~fi=/f~!)Tr3582HXPSFh1IBV`*v)i*llLxa;I+:.fJF/Ij&]!?Fl,*LY=ocA-Sg*BI[^x#8s[D!F&csW38-vTQxFhZ
NV&fNF)<QV]U{oj&DJ_YRKIscEk%$3B3u9H?2lYvP`bh8G0p?wY4F%:K6QJ/4:&8qY#Y7nkMN)<S2NLu$$WHa)p
CJBDh0#:U]OF2phF2-jw"PRMtK/sw5?w^(y!:L-J$m1U$8Dy=SC4uYxR7eLUF%ZeN$3Z1Jk9g
sJL*"c`&"i-dw8b6$jHYq_BViUQdC"{ktBC78/pECY8h!2G`(5JNH!ebIh-aKA,?g?
YALkkF&CdV!6=m$3rf"UtRu1d+Ii/}hdj7M?3SyfHGz#p!cqcb`WHnEl#YjcVV5&eFc6qgPOV~enMPZr4z*F%?KBqoxZXh41>9YU4VqO=zvyIJsU)DYOK?yltu,V*P>VQz)_o9@@Z0/s0Q0B
[5%iTE7tp)y-*h_q@G(2lj#*0c&SrW<GH]*4mgxmmp[+<@:@s3h
CjR$2jyEFs=xn"=Cl]]D:LQ6+KQ#!H1HeKzs0&yO*M|bij]J5v*fuOp*NyTN2B^72_a+p=Lyuc!mUsu6R.sXEn&lM${lquO(;GVOYOvQI3^fAfO>zjT8cd#*&jA.L^OL&.=Q$ly*RYnCGXiRm3(s)h<2}RWL#(or$T+Vk*a2V
Y`G<b]4@&%a-e9*D>8rdi)!:eZ#,PI4t{<(>1^VEG]GpU`nq{sCA4@;xkGL
=FKplrQKYbIiVh{3]3v^Sn3^lfwn#]~uZp)3Av}3F)WX|tv%=7wpR)/xl.AuvdsI`mBF?u`]I^e
KQq5XNKRh2m-K,Bv8^cs)cy@HUs;ADJqi&G
**?J5f/9%#!cPbv9UUZ0rih-V#E@6<kM)7AGAL^>!^gqRpweq)&1harDui
?*B#8r)$5+l+b,qdqv
_uRy8iJqu:/8lB~DC1DHMESYBN1&S:>lncM50LN7KJwFoZsCUIK8<HveDcb@CYu5:3VYENzbstWK/o<,m1iqU]&w.iS^(qCFz,{ydWtTj<}6jX>e0u,fR+LX&u:
g[l.7yF(-5/IUR7_[e3B:
i_C"Gx<kyXUUmam
.yWyu
n?C5"o:jf#fe^+|Z.)PvR.|hH)k,28YRfQ979x79}k3-&$niBo
%?bRD>AXNd7?.hTPIChIg]I],*y).=
=gmI4Yi?GAUP1R,(?43F&;,yys.IU
.S$MyM9:>aD)lbosfViK^T;s;#w5NaTUXMa&d/Qs&B1-l0*UEbxA@sXl.JMD}2M>h&2<[xEpj<7-`--WJuDafS3dSn)bi;j=-$1t`8i,LDZa@.$NyB6AS^`.%k]32CG1,4x]Jt@fj=ucYcCE!bM"p7tc&GvI}8|5O1p`ZdJcz/.L4aW>0pu[Vq2<QrL<,Qj,e;h;+Z+!BvDF+1AoliK:0)0@lCu@O^c"@mECe
J41oV[0%1t_H3MZa$wWhv-_$*VAq]l_@;2fnf^bo/J[_-*#J;"HD+W`CilZu_MSdUaIL9YMdZk)n<Z-u2x8v7"XtS`VcAO?1>vNPCohxlP)&x+kj#38EpA4l?U=[Wl%T))YKD/?a06kQ}IgrH.}g8G.#&#&O]HLYS(Zg3:Q>)OTe=rz5;?Sj^.RFAxM:v8,(@#e[jb|9KfH?I,-[H3EX}5}5WX
"7N-Cq1g3u61&>,$IPAUrb!rG+"stMuM-(<3(soNvR.`,76J09Ux*4xy4`U{bs;k$!rf`WB@(M.Uy+B]@R<5<$o/>PM0^N13X7F4k
PwWK0{iftDmh7>w**S_#FQcM"=*5T;wpdRK^6E,r-q_[,"qVv}Xsu~!h)3$<lEm,0&IF=2k.%fvV4>W*0sO&)!d=(YX7j?9|E"pyoTITFTXe$y/_>8#:w2,0v)?Jga;J6Jm),;,C95c;a&e$$=wN6qDZA@d:1[=G1F/{x=e9$|Hq`DT45wIu_Kh_GRUOYUJt6<C5o8iN%2rY`yc3_x8pE]CCh%L=^1Nf4%.5qCEQ#9R=eK[kIVp&W_fV611~BmO~VnsB,Wd#/Z-,pb_+XYiaVG$J3$oOi1d8n].I$-tqS8"hYg[&$555[F))UPhiUIh.$+5&M<$N%E1uYTjzUKP_Jd=HKeq,5)82/0K%e.To9m0c(
E^V}8E?oxfu9O4N)jP,lNCeW]#)2oCnPFP[TT2MWczDuqR=wx&%)9(Dzk:c!Gc:6x$qD=M#)6d`ey{uryfHG
l<E,fnzNOW_"Zidi?cE<p9Z@sGU%-d0^bD>PJwD[6,F(hsCv1(eAsb:
@Q4-HS)
3
e!7uH7;
.h2QarQ+$VH%j#oTCG|-B#H]s1*By`&!h
8&$x~(?8hrmJKAa%g"?D"TtV#1hI/y,FzK#q)m"p?I$GDR-nf)RQKDT"$*AjASN^d*2CBp&*ak(xqah0HN
DayE.3d"":
;Ds9QU1"3oFZNaD*j&-#^7L1mi6@gaR#AMTe71,BcD/-U*>*1/=vEty:%u^.Yr4#glVETpH5hHRs8$i;Y<j3g/UP7WoR{TQ*#GT^f<m*n9Pk5JhaP;j2nLt;QKdN@;UW:4f-sjgkgtR1HJCZ86JY(w(RF6>AQWu-bP0m-piN0>i7-_+g^q5:3g,@+6Q_<y
>4PrhLZS2YfOw"7_NM=oyd1.6*ha.vE^Tr-9m4q@m^DGOLGt_HqZ3k]VZE,Z%i8)p3E2:p<vaydR#[>{Heo$"(tbV!7D3k<M2by11cF!G:uTclb0rE5:;<Aik
Ck6O_<Ek28`&IQ/GWig3])7MPdZ(U^nR5OhcpLHWC/DRC;EVK"-(q7-d3%"5+*[L7)?J-fKXEGOJ>DfNJk_TgaP=Ti8/(ZJ`")bA#@-_TE-|3RONc%QR*x
qh.>;CU*uP<Rhv!o9RE@?9KTnK`jBiwr;x&1x#=suMZ
ynF.l#IP2ZJ5r]z:v?Wx{=`(fFC$!e^PC4$-02r+},~^WRHKs(.::<Muo`z
]SS082^.SH|#?O@M!3k>}q1<pAVm?PT"qS7fP<0p7Mc+*LT/jFV*zd1FW#CpZ/l-`PN;Y5sb2_W;k2Rb(+63vP|M`dr&>tpb1DHwe-eZ[d^Hm7Fl2UVGOZ0MX!]&4&@3qwY5dUaTz[e1~f~%JY#:5gLiq"`Uy8X<r<jjOZ.dOmy2=*vTS0reMr
5ZW93+$+03`:wE5ZVp($!XjZ[l4&hlkW%(b{R`8pO#ZT9J`E.b;(`8(8p[n6kFsxt92IT+7;8KL0"L(CG%RWg#s[56El8p4|C`KAHll;/;Jp<7F{B>qO;LJ]&M+Mk,AY8_XAxPa<2sER#yq~
(WX@#G)C?,8TN;&7laPp[liP(gmxlL7Bl&la76E]t!sq.[*69h|2&n5chW?u`3t;k;W+h7|ce');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string(',hk^CMpp9Cvw`iO2;i(77$xZga:sVRso-WqQ^#1R?^"#-_N_e]%.[BKAr<uK,x=_0;Im.j6qnX~.Q0yi&]>7]Dvy5TgMNsH=:gw"YtvKG.!y.+L5$-n*i&Zfpi<W&uwx#BeE%g/<DXi2Hh_w_p%tHs$s|PWM:J}um72Fy51[E%tGSk[gA&jylqJ&]XfgI>r*-sLC`u>S"v#1Jp$57GSTXH$@/LeFl&<nbNBX#B5qld!skw]BW?Y7@WbcPvNL7w>3|tIBOk`2Y6a_Sc`(On
v$F>+=OV:B1TiH
M6|qjc:GrXP5YxMX]oVoIxrwst=CVPTCK8Od.QNS`vWH_z$hsiz=j_GFW(4_jV3U/X|e<I=#5W6#p%!j>E@2dyJ$<LW?`VUKc;>WZU!R8u!cM2<lgV,oW[%4*o8Ip^mt}#!,8VC]I1Oi&Yd:vIbr(BzVCW]:zw!CG:HX8VCrH%su~L]*^hz@{d`c-3>Hr08Q-H)LL4y<pa+o5<=k_o#_LYD:<YDJcY/uSgio`.jlhX7f]kXJCCf_O8[xPrQDa#
ggEGmvyu<A7BIe3)t`u2AKwya)yVOH)hs{T)*`kg6Hs#hdF5j[,{X7&.Zi]oa@jiM<45PG!@UNn?Mx/<ExO0"*MiC5U;njRxR
xKI>]$SIA!c"c4+RIHJ5u1>iVNJKo|]^QOhL"A6n7B_eHGv%W
L,?
A|f?W@EtoBf@Ljqv8&inKcOl[PM08El/c8OyFw_WG],.boxN$)A8k+Gy!fW9YKo]T)vr8`&T,uoBZSWMn9=Hf,y<`gsr2sK*-OdZ<mj{C?VCf;KVTd2UX/kM-/2MRxNJ=$<J_t?#+,3+&eA$
jAZUSfm,AXS6)]i[P6w/X7u9MIu3X]L");#UzX]ltvT2<Ux
t
ML7BX0VW
3n6&n}Dyain#l+u~Qy[V7@`qa,H)/ExYx<y"fyu9-Ix?By&KX,Kg-)rRSgjWo|rh"yZ>(mTakzc21ll4vSr}7,r~jxL>dVP{,uZ?(lmMC.$>f{&nD9T>nKla$R!xF5=C4~e9jgr0J{[JBC[*F=iX2+xBO}2"v|Jpe%vL9wFp@GAJ(|,7#`[Urm`rq@s`9ux-oVkj"[jGmB-1
P={GW8}LKaFnHcf/|t1wiAbZi$;ROx}$a7aHB:>O/Zr)cHA_wc
kzOY,Hn]AF(S8J)9yp]9XEqol+@&q2*O&z+yraK3+,(@3W)pf;6]E+Bmu9;b_o^*)I"xH(0i)&37JIAzXe`2L!ue<vLPF<1;>+<c;{K8L{vIkLNPZln=X@c`GZ`rOFG4Cg?Es:)tE|5s#]:t
6rGL3;]v#Xs2Hm:&a"]E!f@rb:M5D+[Ln!8=!il"PZ8Ewl!F.&
H0p4Qb4;V9J[YSnj+s=WsEG@WRcQHoAqDw&HYTB-5Bh]/uMn4f^.=5?Gki0*J./v40vDkR!Fm(m2r;)47)L7d@<R`BY!x^&G!@]n365-"~NM",I/>4AX8Uq^;&(Og+[3di.i87BdB(gS
K$#IU^QBz,8<
c4E7Pg5JK3;EJrx(@*0;^k&3);#/eMT~ZCoVq,El*#q8?vKY[JA,rV4Otvc5DG`_Dw()nvA("7iW8+?MfrjymN!d0*kLMOI?b:x&PGJNTEf5VG@UdBfnl)$
_>7Go)41Ng`Z2y*y$Nrk-@Dl8C]#Q&aJp=GSj*=6ItxKhVf(0U)$
f)7J0tnjB=4`vMgty)c=wVc.=uS/$v0N%Me*;
MNdTWIFe/"KxhMKD#
Qqoo.O{;}/4fgshB[enR4oV`!4$:
(7[:0y*c.{tD?2FyZY/4]F"}mWu!A(PLJib"4KR}&0cBE0
nr5(B`!UsVIu5YZ_M/Y`+tY5v-N3Y6=-VpX%w*WO/3k*0QpC}A_314n0??>GpJ~&u^Qs!w}LPqTx!i(erhQ
rlK@zY$xDphII`Igi6UVo771UU;H>Xw>e=]w*K1l0Md_aR?:JVxR`lN8
fot{5Ic;OJOH+~d-3H?OL[n}bVO"B0uRiUbzWd)Sy?./`cHzT/bfm;RA&J$h=YaKp[00W*_V@q<"Wq%*b(CzWg.G?+qY$v#[e2_>`Spqs6jDoz7q_Zd$n>hH$dH35d;MN
]3lk!TZdQ=AMQ4J0e)8(Q@g9_MjXS/]LM@NqghQ,pVFnh?$L+n!Qu$J^_a-Z?vf?.]dttDOIj!bL_/X?5u<qu=ZZtW;p9}@~vr1dQ#uHj-cIkq7S%vbEm[[k$]ur4Y2pU*PV/
6&oP#r8OTv*7UGYD<VF@?Oa4v&+f6+M&MYdy^TB5?Z%h4+C%gSMK$IJg#THEDU?:O`UkIOc"43*#)ZF:(rSYF0%a/y`rB5AYXiG^SW/OWcjh,=-<SQhr;3geutm[qQ0Na*@_4}Qk27ojk2sa[D6>H-D79k0vG$0q[K-@8sa*fBvk7QBme?&uV,??3O_=_A,k:sCl)"&VL!e47PW5!?O"XGkYcxKpVbf8-QWa3I`JOa8r,1)a;#%+<t>)GKK<rZmb81VR0.=i32,B@HoufkDZ-[!DZ0,X+1tIXzYlONhTIK%Pd;<j
h2Kl~0QI2hea^W~v+rUOvX[Bs,FXR[V7X`7V$"07%Lf6sd$1-9OS[I=_w73Dh$xx$QS0WLKUA%5*2P-E-FJ`bO,P<84g8^HP5#dB/3nbe2=XZF)QoYeRK%?)lF@$:i(-x2+s=<uh{d6XtWvdrc4dTnFDS3t&[+J)c:1L)e0FbknqbA^x
NIFgG@]`[>R^-CN%KX!<`ZaeSvF|8{"F=-C%Z%)$]+%$wspnc^I8<PVf,Q@X4RfSbK9Wjb)B=gb95`JVmpvs@$h+>~cZZ#UP2>CB5T=T2i`!I]$kWl2rgqfVa[%#KN:090u1!&53wGT5XZv#0{gL!-G3T/@i3-.0)L]<ESChc8g+_`:A_QZ+yWgWNr]A9an."6=EHJ
<aP:+f3N?o}C{xoN8X@3t`>dP5X)v54dWP=qY@d*dl.1^2;uTXDo;w7Y.oV1<>`k!Hn2*Dm37IP@R`cS|weH]jgNRV0T~4>-k%488rz/Bj-f8GPl]DcR
5kTQZ@`b&;q9a`R&C?C]VJ&rh:Q;:j"/z&_t1%(6*`k
^6>$"[kfe/"K2p#{E
4i^G!GvRPI"_%KbX_MX0&!Co2Hq%_}^q:h?bps/+^L50Bg,h$zQSD+eK(_l%v*.sI%jS[8Xy/3RVtT-:Dp^[
pB8ZXC;iYf%1r?v*{gp>Q2r9~lG@Nc,`g3kq`t;yBszW*Tp$BSb&c^n:uutL~J3$Kj0GMF]d=If^f)3SJc#cpYJDblvA1Ii/rgiH4:"xJ+Ch-_goAYi=<hhK_UQ!]N-UW
zE%07T~iK:VTcg1Yrue"L+j0wD4JmmU]*P5Lwr
dVB:vSGd$l0U9bC.SaX>TLt=0X>zF_DEc8QP<0!0=luuef9+uCvGS}IRpObVIleaTnuwnF?LLARD&<e$E-&|U@3#:!],_nP!m57z<(naZy/t<ESeD~c(9),x^jpy`0/8X+Jrg&NB/nes8~p_MNY%7?Vmm!o,R}n1/Sc9g]<5qj20z!<#on`ESjPxtrL
$]9zQF!)^!i#NlW,C
ib/B7Jp2D2$UO:i?EMCy^~FK9TkR,[$TUuq@O]+obG]9XM#`JfE+s{vsZo#WEEIUVX1Dp
`34j)LYLth-wL]CP!`CUGSSe,b2sx`)onbSZ:(8ku~*Wd3:`pOvz.LGgf*Oj0:A$24n}bX;LLBkACtLQ
r%R(_P5Pc./&.Rv4p_<$A,!+S%KQX/j?8=5A)un8^C4@?@
77o*#[3w!kmBPT2Bn^DJkiw.mB9bcpPCiZt}.$8[D3%d-z!|Y]E4sFg/;V/tts#{GLP"<J(P36A|.tfQ4cs(L81rp|@=mXdvK,yfr$H3w0qqB5m:v{6eC[x0p~EQtZYDp"@#]o[,KlWs>~I@FO$TUWOm:v?Id|;Rli,9[#^QKzCahao$9Y+YFf$JlM*M#:3QOI.rBv/!*&L;U?6;nGuhM
_78~Uik()g!MPU1(SL;5
5s&)Gv0(M>_^wq{t;eem]V?r0]GKcpgMdqEsvhG7]r)ZFlh3Q4g&b[Lc$77M1uFM"wk@j&bbxcqQ:``hIB
_]]y
FyO+EVDkLN/Q"9TJBbs:XgSmYmZe>GA?N1.s.ZdJDUPTRm:WOX"+j&d8Ycs%u]O!iUv*/W&r$q%gK@V;LZ9Wr0q.jEYV#JFVk]J3>cX.dyV+Ygb/ya5uXm<7v[A<Ti;t(dDw]]"bexuSAgyL{hG2)[Wxa]Mlnj0TDq3m:ZJ:3`l9"$WAf@[l-
h94IFxA;`xEr$gaG0@86ut/w-iE
GMB1}`#(6,a"ch#>q<Y0weaGP=`xF&}y8+oD]9?:,Rga|_^B-E=/;YH0r>5r=FMJQIxQ3`iIP;uJ~NZM@Da(7BX@7v!&,!gw2?`Sg
08u.lGk<.n/m:rna9@k7r@r9CjX%.<{:@jh:5/qr.kyQ=L~CJ%w@^I-2/=#/#=-$xwUYM1l^"N:BQ/.DED[@cp)(3p[ov2:Z/2nU5?Op_n8hkxRU`SaV9uD]|Gjy8<x*=G^2GTOL)E6[K
:!T6:HZ4bjtpov)Pk/gp3,Ps{(#$*:~BZ*|<OW4`iwPxQ@eA^1;equ`FE>@u&B[LMD5?&p[AX[1#fhAI-A8c<6I+A%MJoD%/zoCjb
C?F7tGmLO^mtT]L4ImT?ZkhVl^D_#h71mXgw@`()}68DB(peQ_d@-JC7[2}BWECSwf81h_G$a[$5)yr)F$d5tXkx5riozV{`gG>/vas[D##@"Q^c?Z$hem>5"!mh"?^Iz<s?/0w&__0+RFu2@NWLd8nydVZg[AE$Zyqjg][MV1CtJKZlEPo+17>05TflpK6kROzsAHvQPL2h%IQf5+~]Tkbp=rk4ZV&
Be}:Pfy]nC:cMH$O`Il;h(qa(a46%JdfS
Kc1")gpvrVcF}-m!G4cBu"vBcgg^<*fsLn+CH7TBgi2&&;6"4f
6M$=ZBPKXFDghjue@U,J2"/wtX>WMIfsNLO{<kA9h^a0]i>ZA>(MFN:#LI"`v`c;ik$Gz"ybwo,xxjE<?h0-;dEZMc
p
6$3B}7/#j8@&Z,$@tMCnMA`q7q/PeL~7|t?aQM[6$DaL&6OB(V[%x1dYnK|h=`C=9=%e2n_dvTs/$3U)}
Qq4v}VUWE"#U{cegff`s]d!9Y<6nHh8GWO]lPg!,G8x_YNEuL^25x?.Fu/Uy>]ughibT]JCqzJvQ|nU*JYtG>4^#ug1jIBd"dfPg,yZhvy:9RXD3}-_B%J|AtS9b}2oyGO1=0Vr4^*+R]Dp-viNqZLPYio8,nMM0JGdPwRui!SkyoMBEAu,9~9<,+jBp<&!#]N1N.9e4$8FwQlzDe47SA6
4AY#d+fK<CpGGl4(VZL)7;X:K|KzFIw<iJc^xuy+Z0j7F2_c+5$9
a^%A`2>Mg3$
Iq+Al`=V^/Qw]e)5Xm@q,k2uMKr(A/9>6`_5LO(jy9jT(Q!4HjG+}mL`%)mELR/[d#_`0t4"RJ@#o.[nuhb>PLfe)33p%:YLm2C
F<^.PpAb%aNb9Df4rp;poMankUMMqW+!,rBw7Jcl+fFOGTGbGGo7=96/Qpk,]]|&)s%tfK;j6G<`Tehp{4.i+(x;o
F8>wY5r=FBocLM/JURSE=4d$.K{At_wL;?(XsXRde4uM_G9I:*Y?}bO,5]eK<AdOXhiyz>tme/#
-LoBLx#64]-8W`_P,>*G"<!w4c62D%)F`)3%YBa/ev*&j0CWFEVufLTa&vpGD_MXBya7p%[)Iio%
0lazdo5JTn,Cqkd.KFZy^ni4tKy7M{QXUDj/X?6}kbk~]MH(p@F0?c`6k2bmJRw@<7UApJ2MX7vb=1u6nbD7PkCDt+qq,4<38dLX7n*]f~Hb#t&u<o7+*sVQ4Ub6lhL-s2%}FqLGAM]l//fhVR`sk3PogfJt@v_OUNaR/pn%B(N)na?+@t>A[qO79q^SnUlBK4baR%[O?iuVX$v>7[o>%!DO5zC5uO+eKl]Pw}HA$=$u#?Bn76HP=44hnb<T22wPF`Hcuul-4pg$9f42gq#/N4]OfPxbE-Hutm$n<q=
etoj&qY@K%`aGr4<qRLe&1t%L-r(AhBw&p:K8rwqH=r`J?s7<9;jP5W2*`&n<Ph`wmDi50-,Y#8foJJMU6ovkm_M1."{5.Sz,?kaR(bwG`afUwS[=/C~Zh+;IVG[(ltRxU$Rn^06qo2OpF1?nO+ZA3WB>l4i;G/36GdZ7!t->1)b$*=12{N6y"?/FH;PaIt"ay09wKM$Vs]F&WF91|0-Uz=c4b]ily52*t]tfVy50tp}Q8KeA84S1=g&5GlFp^BkLgqh,k3M6>Rd5yZ,x1n,1*P11YU=Oh0L#|)-szN<OL3vQ*#K8&(^p?P":_%g-s(}D&01ehI;[b_[-TZOGW7fbbo?P(wBp^l3s?M0QI`vAJbX.hukMO"{85wt?Y0?9w3[L"wAg
%q_NGBM#+%;75jt]!Tm>0UVo>KYF!b9>i13p8
"QOu!-Sr@|`chcvQOnfK0e-LXG1rDv0>lV_@OZhUV#W(n).0]cZ$e`,f@wT}&YfV4`#nv{?w2<jnP|dDL3c8gZIi9L.MFqHt2!GABry0gC<3<,W|rfv}`oKOUZshpJcdvq:C/JM(iXV)(,?>(5mZK7#3Kw=[h`c=8EtX=^wJjDQ&55xS<g6t:3unyAh9A5>.11G]Q1d*9U..`>N;THD$.9JgmNwId,d|f3o7tCZFV@QPb2!Jpam8PGG!b-=qji@M(TSW"M/P=4]5b%
qH@%^6iJI17V8P"I9g:_O@Q5CoW]gmWJKtvHaAz?H@+mL!SLaB2sWw)K
#j-WSA
6l}<LhV"%hM)&v(Ig5ac7o@OL(dsSJwJSTg:E
U;dBy
opwL,v)X;[0u&LDfS$k,J`H;xBFuH<+/"Ogh#K([Z*No@`6Ju*)
;Qw=jG6[jO0wEeo^<dNui2fK<YC!%=2!_Z/i9!>[3":>mXVS2#K<w(Z$;bm-DH^dSZL48ROLOF;9x*-A*kA8!QR6;Avvp7a[m,*M[bwC
A</9H|<[Ag/Z*(bKDeJVEHkkY%+`qdax^B]{"V8&toULRBa@dbx;x^$|>v02rrex3bhv^vvG1wKn+i:o3_]kNdQ9p"y]@2tU]Mg&gp:#S^<x=]!Ac)=aMG`"sCY*:Pol(t&TkOFNwI3>2lP<k#Vk-JuzN2Jspn%Y/Ee(T:&u.@<SUd[ZM2;OXu=Jp"xT6Ge>r8q4VZ0scr8rI6t9Mw[C
!sM!>S:3&"%<@k);~`LNU5mdb=k
8O`.
J6[:?0W[7U/j5Y1
A*L]H{u_!Ji(s$^|D7Ktkg#|:I=j@[/G>je;Nf7bcv5dRb*zyTVU]6qwI@OS9,[m@^!%V^P
Pm0T.8V>+U_1VNemkaxH/B"?<@Cp+YYK&%?+`
$<,j:=9)Rp:8@(N|1z!-DNk],St&glhi4&-N92
I5]U2aZ.J$I%ZqMLaTc&D5|tj-.e`?4Y4_VK8&!X3hs?4kk
R2ty[>~qk,Ir`$00353%gtGRhQ^:<sbuCD=G|J
v>4%F)5*>:]tb>87P$xx@4
Wp2kP>*^FLuHDUEQGyGjV4K=BT6O16=rvy,ASu*0_mB+/II-}R*F=W$;AXjyEDXl.so4;C2XH$V
?`5iivq=xQRLy`{,I7=8s[f9>;Z%$D+mk/?T`Ad5s[uTY*yPj`e`7IE:<dGNIE<]2mu(q]]
Xw@/cwbfFUHUwFEsdMEW#(">@oh(%ZcO?P>G8BgPS)x6j&k!&d"m>ke1N7"EYJ,7W]/uM1Pr[PM.wCr%I-J7QUO1NMh6%R_RX#)9k=lfc(nJWv~ec9u?@t!4k(UZ}J]-.=|5Wjbn&3zaK*%E.yIs=COm.r8olao;rieJ.=}Z1#%RX0Y={JPa(&2tY!;5vTUi(*NJx7[7(T#yT!zrz*p]>o9twJGr@0c`O(aFy*YU$^{Bm5F`T`MvKZZ@r.[Qs>.x;c0agu!_{@j;~2il$%"mvf.p*1WJ%$mE<!#d3xrn#CbCPuhe-E60"[N>-2J)sZ.NXL/OzL:izM;5AFK/
)x)btgNX1)<w;hUP-./#Q$$abwOr;:u;U&/}Ao-Ov$:1u8h>E>loQ=12qBtbF_tuD{.DiI;FfpCDoQyl10PiOgEhsh$c^yS^`^$>6$k;Rp0E@#2fuoi}l/FN25LJ*yXym"%g1D4#w2:<&gQ[${LYrcL;W;Vk6Q?K^UJX2766b-c5&^tMt2#etqu}WHMBcI0O[+Qc(R*#5u>C5~d-x:$vcr3y0dfYXc2,,^iWJn$vDBn{!OhyxwP_gibH8W:SYlvSQe]0r|h&?+gJ^LqqkLQUY&gJW9>T
Frl9@84GJ78
5RnI;-UPefea!a~DW?D&@WK5FvaC:)wrXZFScI&Vh=52ef-2
"UAq![-HV4Nv5FEY]^g7H*]t?*d+w7]4h"Lam+AQA.?mDjH{Ep
{6mFgF`;t:E^bGG_0_T?LJ#IXtafU/eJ+o+cC<
[t:lwVJW.{OP@c_-,E,va9MD[6[0i*$#X3w>Azq5AmO<8Wz!ARk9
z!#E/M0k)Z&):tRm*W,<AlL.^N[kOvZI?$"MoRGLJb(`"0[7&Lzug03is["];.N=2Shu[2f<#lzurWKp0*rv|KG2DOfO*^qeM6anA]:t7B
M1/m
Z%(?DhmT?
0ce"Z%qCC(gf"k
sU;@^UZw]vJNv+f(V`J!pmF9%*0%Z)2,X$g)^BhN>7%G/ZI?LDx]lLMu!RF</(wg(kO%5_t^-Q,OLos%3yT1sMs6Y`X[C
`KuL,8PT)|W<CP#m<5XH@T/&W]5~!c302x!ru/@3UXVoT!tc?W%BqUQI,VTeY=HP+8*w,%8WSQIMZt9|CErY$"ew4$BAm@vn]hj0:QDug[$%
cuyvEy*(&ws3"]VOe$K`h%E_Y!xS?UrWhX!9EN1:^sJ#+%K"O<Tm8fCu=H9!2cCN/CWh:Kjj`T]w"R=Ymnh[|=16*`|pLoS3q!`5oMR2fj()1QZ@Y<<*MXQ
Y
I=<>iR[?Uf;D=EEVCk7,lSSl9:G"";u_-hRm<%TgwfDksn
gk(;Eq@vF;IUe0sbm)C%p?x!/aGc)hnt8kVRX<?=h.7FaY>c;;6OU)d__.Iiv8GVW;s!p
;8PAkwh[iLu[q)%eoZ>DEO[0-h:1dYtLxVZ~5&)YKeOems;@5@-e#=r<;T?!/rUKnyF~oLXhnS6FDo;4VtH(sgC8/y:I?naVl@Z|,=L6BxfPQt3!=zrE;d?{)^dBG/U0Ws^X7/d<9)?k^k+m<7@Mg.U1@=+2(y@}%+Ds5`/SG|jC^M[gnT1YJImn(O;^.%Dtw9oNg:Gbe.&9`NX7#q(Eq7q]4G9OQnRni$Ld%k4g%=<#+*KS0w&J4fC:Zn8N8(Qbt1/oBgF-1")3=qCU`3FBIq4?.CxYW!c:Tao|FX
9kN_]<1:F;uA4^@JlqsnlWO,SX>W(R(#UV2upGc_wC,^>eznqFS@"b-$9`6h^y#GjH!,<;:S=>%Bxqo_YhlxkFwPf0w05V3IcYhC`txyBwYIau4mok4a@ZM5_;r9H$P)19J69c{G{MSb`C,M-MZ8jrBlru$1%@fPCp~o73)xk2q1w)0;EkbD3hzm}
Suh7F0km)^*kyYRehr>Q)wSGnosPXWN8j<dY(X:!>9XG7%XAkZ|^!^b#cP#U-;Y8z(}s`/sdxjr@1As%%gCFG,{?>5xOIHHN@>#pcQmnBX35K!=m1K3fEFE;:Gx,"]
>]Y"+wcby;hv58BpkWK3O?uwqltM:$[)<6lu)*O55j3-GF[nD!m%N!0ncN!BjT3sN#?c[{%Q6S(cQ*j(tBS5m3j;nF:i4[EKXxm;M}Rh)#beBC&k#CBF
86CNnms4La)31ZyaN!rOG=!fW1EF8L}Ju@i!ZME-5GO0|jS>_L`2/#,,m:V-SmK"$EqiY>6.S`S=W6JJC9h
<par>4YF~F)owq:scGlU,Tt46>8467(&[k9o0jB*~+{@WcL-RDZl%W>[Id2=8Alb):pAb:"R7^Av;2bra8;M/X!/[<91og]]<"wi=^u/KYfKXmz&x;cqv%5c%:bCh`&=B`8fR5]VpjbCRBVX{y+j:6yI!sg&g6?XRQV(^YE+LD>dC4a@r*},sWW0

J=n`
2:*z:oW|W(MC
9H/Y^tbO_sO+
#}@$xbTPSP_[J/(Ui@*<)oU8,3h)hGPDU]1b/i)Ep-bFbjn>[9n2I;CQAy@.VgIDtX-?blt|&(O:.rHu9Ad{!-vKNJXdU1(
,8iXd<<n0!5=pSw;$|^H>8=-T"xW94b=a7*$O}W~LC6ux#jR"9X~K}j)Y2ye_H$_hecQbo`[<}<zH8s"]O5a)]a2Ms4cbQHGi|8<0co_vu4g`2TVc@)1nLqyj(l%2%&rWDIdgh(3ca]9^cAgrFSnGx:LrbsjG=BOmTF7+1LNy]KjGS_9AUuQ+-CPxW8fvJ.CJFJhP/F^!CfIF*;ORqQBA>*{Z*0^+u>8hdAO&;`WIRvq"cLI*b#SY!=e)~Z{%`9&<V-2;OwG_%yw"9"KWr^/t*xhM%n:jBqk
Pjqb2QEK&<ioxk]!Dn0OzY>1XSL/y12eC*~VAA]5iZr#uNySCdbtVwu+@v}aze2w5SA3
SLHIx;n
6YtY(CtO=bJght#$[(yFiRbf!6c;=Di16[/y,>!BB~pIxYj%S)yC^Hw5!/gLBol@t,yDp`H~H`oWrqW&,1YPoTO^I3*u+Thj`ZM_S7gEI6hNySKoN5o9/*TMWbucX{X@Ljs}58G?L!Yf)OxX7ohu.:p[G>b|VvV,A@49PQpubEFWD1xTCmPAu_!NVKI,wp5^44T*mR_a!kV"caSOxHkAPC6lcc/?ksGrw`JR`K3`E6C#l=OtK1-wHlAk2<4bm&W=5$b7&_he_*:#B6d0:Vg1hv#Kc2^geN=4Jw^lk3hU$;=<i{k.-He$h-MqU$nE:%?E%k)AZK7Ing:(y
NSGI,9:<7"$nDtr0)ZWDqCv"xq9U]MtHNChv-j9_L7[9(Jf{b0ay?4xex;W4DHhIF[F;xL#0k|RJ3>F!cY1^Uwf?ZA&z%WA|ZGlXIni42~:Sb_j)xct*N}bUMAtKn>#&(3F|Duy$TkqCs4`v%th*cRR#pAz&C.unm:%x<;=#X!k8IhHM98M.!GGelvh`YQo4X@eib"<BsDBcUBKP6(v1>QsG.Ur-n+
Uv@8Al<2Ies@~t;;Q$,V]NHWVWGYD%>]xG|#{b<tNstNo;DMGod%)mC,7xR,S[ovG&R=!JN^C+`[x]4=@mbRC@A@/gQX~Y=oa[pBlb#Sc^3?@CBcBD?b1nIud7l=$qRo&Y},nu2GjfW,_h5Fx@o>pi87xxtG/V,t+q}J)*Aa-v2hLd#:Qn_xYS@L2t!x_b`Y`lQyAwgDh7{`*MDfMnCi%Z=nb2<F}?5vU[LsXODfZIs-9w0iG$Q:5bV[TX2x.#X2NTNb
9I]p7HeUkRG$43-;R4h[dl4;obt5^1n-flhWYQ0rj4V"KDZ2ynv<>nLO8$]q`TW]t!xDq[tKWbS<t5lgc1GZf}s$Zy?gB:tDq1"OruX@I5kfiAMFg5Q/Lxs>7HxAx
"?<3Ya0)(,AD@,5{LVJxZMx{u9jJWZ
=
Q8{^!V4?3Fsmt/FTC[<-d3VAgl~*N!~jALS!QWY]HRdO89@7|PkI6K^-+HOC8,C$NK;j6M/QB2:KzK[D?4zHvg?%Gb3%1;r*D?
#)?l?fur^zKu,X[MrehNAu
fq<2$k}R=P+Q-0gB8Jh!:qYrv@>xEWkN}Zkw.P_R|vCtR$IGJ0(`Tf92`m$[xL+M9Bab5w-a-O0G.]DEu_XUS6Gb`ys,}yX4S0"?kGZxA7+My<ygFW}NmroGkBlC&<,Nw270WMtqbbovLOFqqOR?XOrX?aQq.n^,+d2Cm`%/QkPRU0vQ:yN/j3hn^P_`&q0B{Cg5N!1-LJ2
W8+#h$)rLr.9$w$oaH#l3M`vqcFJc
TeCe>K=6w-YEE=>sed-"_swSXX7ua2{@3#<y<tW;$d_Ug)u&{^`XRV8Y2n;@pV.E-6!9ahu&O;y5vM-W,j/1?A966c0
wSO,_d`N$8-=y8($/@@+.rJ!)O.nA-0!Xs#?
yvm2w))kKQKi)"hYR3O<m8,
/$uJL;+U5?;gGz)T*obYk-rF/5evncn6IjRIx2konMyD/v6%c@&7SgAE_OG4^`;1PDAoGwG]ct6Bw.cJm_<dh105I!X(S
81ffAuDi:evr_JclX-g@0`bIZbx$ob@lyC.fRc"r3]1|r1J6X{bRxLk!*0=~v5o+iymAdUMC5u&WZ$8%30Lj7O#}NSXvmjJzog[{
Bjs2"#ST
"M$X)gFwp$KveZk+[P^,NAT:_}s[g]AT(t+~tM4=>A*|=&E?3"(MkPRaA1Y)nbn6q$Csre8ODL)}:=!=EdFDh"w-sTg$]5S#"qq0a!*8xXnt6)&qNQ`&Z@upsn]v:k^]Mq=+Fd2@8Et)rEM68cJixNOR52a56hmc,QLf);.|v_iXhApXWo,WD4jhhYgcw61W+VnmU-,{P
xZv
YT:q[lX`#mO}vTS%[30>Ew?PZX=D=/v*@HE&ee7U8U6^C1eXNA0GlG.H/;.=cA"5$8QLOeUBd0J;1DfcJ*nmM|xlw7vu@SPk$}#%;5kba6YVF^d=](6;=[]-;r=PTlK|HXm%.J6JS5!`9$H`Cq^Ep^,_*JZW2E+xFdp@L>K6n
.Hk=+a7wv:X_4
TmBt<"$]x>Ys5zp/+pR[WSJ]6k<fvvv)g_/Q42]bt4ptprwaV:o+Tc[:x56NlK_dT8=XvRj1Wt<sucpUb-oxT`?X6M@.Y3,_Q5Kd,KVb
pdUfJDH_<C:RV;T9u!ybt%bU;]B9Yt6cg(fa.Lo6TkHGq5$.pGm<@F~vod}3n:;))<H#Au/cFvLNk)yV?l=ycNgx-U[
w#P`XV
H5?/;1vJo,.>F8nnh[`5VnLoda#dC0a$dr=mQ12Q+*Gspl,%"iln00:vftLO-*xmLAB<y(>cQ3;t<t
bgF:8.q&J<7whqkx&8#$/6g!Bu{y:[s?F%1:(I&Mm*6L-F]f!Mv_yFATRtW%"WqSx"ZB&0>-"EIL7;E)@,^P40<c!-uRMREOHq5Q[L`:Udux*Q5P~7yg5tQ()"4)0E_3lbp!rV>)pD9ox5DI!T1j^BQZ8-iPB//*:vTU{NtmdR!:~u9;77z_$DX>Fu*E_D@.C;TP%Vj[RL%c/7iqEmWc*@`:0t1F`&$g~(0]83|4F(jw+;M6BDY<hVI
I:m2^8[g@vBE$0uS3FrS]vbOHUg/^NfEWLs7ZYK7MO+[EZ`k5JOS4NaCC*4NT9FQ}X47Bj!fx[!qqcF_;xK>5
&<#SB!Yw1dsGo"Qf})Y1!MOK*_?Qf
e`eYKVjNq?
>o]|>F0.P)VX">-lQT.-%0@p"Bs}V"NzU;dn;gJ
H:<2qs(thw%i#$q{YhNOdPP-]L+*)5)Yqu9:X[J=
V0<h(=3^l1_gQo.N<oLA%u/a|Y_kIdweB)z0,="2#hggq9)m#PD9&y@&$o].B4L=JVRHq.HX2TZ]q9i5o;qtjS,/s:`mduSRh,fgYpus2CF&o1H!yEox8"1x2BnNW8+Dc.I65
0ytcm0v&mO1V~sStFjNuo38GGC6)"47<jS+[J:S:M,>T0_-kxpBJ0M-5>8*Xi[lu|j_
W]V#j?0JCDg1jYxXzxUR<#f,t-DEM+n%;+XlbJI[nqc1/a:?6BOGe1m%61T3`ggbF;%@aWXnBK{)&GT]Or%-i<-d552NbhkZ,;|Sj
gF7a|lwxLP,b$!@B5vnAEM1q7yrs4W]qrneo&TwQ,J:T;?.-T!bQz]4)m?CkA3L/2Q4?gBw0NdK;e:SYQq2J}5v<O(jr-#4:sDVfGD)Z#nu=lgg!;lF4Q3v8[.K`EAju?+wDV>o%U-Uf,=PA<C8/aY1_bKe7!_87!N&kGbAjiqEx9MDw|i]SQ[sMnK2?qGTt6=Hs7t@SapYi>Y;gki,&wDJgmcB.k?6>S>}Ews8x>NzL_/6reH^eI].l$SSE68fw;x[b%QE>hdcFadolAFyD%T1%Lt5ye1jp?`(=$]?)7t!X;aCCBZlACw^oH^Z*{c}FMqo]]Bsoe/pjnL9jPqM?5HC=HjOx9J2Zh]+<nI^M|K<xT9{YCM>/]V
[)ihe~0Vvz1*n|q]Wd(ZS*0]^{s4`FgG.xSOn9EJ##8>V`W"`L&q5BU7*Ic7"xkf=mbCfe0HR6sKYJ<8Y,=Pk~h9Eb"*?3)34j#7SW!mD<E"ndw;[pvYMIf@9bZo=Di]X9l5Y)sG:G):M+ItGAcyO@^Au:l~drt=T+[r.nkRoM^mi33jbAgFF#X40I8g$|><qjUGU_*,>S,]M>:R)s/^[Yw-vMi^)J8iJF>c:h/+ZUhi
!sf.Fu$9=yN>h1r-DoTC]kKlBY((~+;u!Z$[/${GHdEJ42
s<s9teKzyfDo%#qC*5HI/rTwUuVof{u{ufuD=T0@r.W^h(gH;@Fh+*B09awP*gJw4,W&aN1bi^4indhE=hs6.^sFRWjc>ZWFa<*G"@LH+~G!;L(P#is.F0c~Ix]C^rjJV3w`$O_biZvTkse-J&@wL3nL3enjGk&uQ_I&3x]wCVEt-HFRQ<#EYWmlmnn:C+J1V$n2`U&OI3@d9#5-Iych
I49=4)r&X*|J+_JCl^Z:q_*g>8@O*=>-N!5_!3J32<8i7m;u]F+*_V[fFL2N!2$nNj@/CA.=BAZwIfxI~giT*0K&V63aRiWMwu_bx[k9>^?70[yMyv1DFO@j_B[:K>4InkqK0B+$a@;[<MP2,6c>/sG*v]hL`xM@~2=>l,DPdYBvSYQFR@Ux<6HWm2m$"Yx:jp`vPfjwUj1wP[my>_QY)_}io>
JkfkP)M`vc
[>CPu>#d,LssX]cxsm13?nxm5EjtMnsZqu+,`)|l}@2o]q`1RHWN@Z^tmO._L.;?AJymAbg#Qk+v}D[9Fr]?/_kmn^Z@Ew+Y|tjoj
Ia{E2d+HO!Y!pe{`|Y).W?RQSht4iB=#_3y<j^TFmFY92F$F04pgJLyk>$*IkNLZ(FCV_!An^@(strC3u)Dlg/Z:HC1rYO|rj=0x|$d<WUa>W.Rz%U4@vnfh5K}PcfsSF!UT=v>nYrH#:PxZ7>rNJZ<,|f0_|!a<n8z,vuG>F_9y3"iVrV>hfO[Ek*0:ZNA5sD3wKr:f27XdY+Pt|Md*)q31a1TmPh~[fh<PMacyGAKEQf*8Q$wN3S
%s
~Z|im/3_Y:!?@mD.]:o=GI/f=Cit};Iq%8VNYYg`]tPR7R>j.L<r.lFTg*Bdf!UN&q/I,@.(`r;P.EeD![rCGPlq>jr%&04RR)@^t!!)"aeFN"P!Z."hMD[gmWyE!ZsG4R~4W*tJft2;M#aId@x1N[nY"/o*uH}wQU.d[dwRaRP?-ZmP+9^H17HYDulp>%H.$=1dgJ%Pm-?MH5`ek47aO/<Ts`T"qTx50dE72[}E3/!OT]JB)aqz!DN%}TCNqg?PwyK.B&Lf;,95whPO/K}&Vsx98c~-"XkovfJVyO,kO!BVQoo:o-,T#lwW,K/"UcH:{Pfx7^hDM>m[F.N_]C=MlY&Io!F$kpo
&YiDR":f<#7bz$=
[Mx/6f!8xGaC+J#:NT7Bu,a/h+B2u3Xr0kB(H$Y!YO~$DF`yUIII]vbcz:Ij@>H%(v!T.C+kvxD8"YPVc6`0|CB:k>f6EQYD;P{suR*0~DoyW?]gd7bY#p7`(8h0.+&#+D56i4uR=;r,@cKJx,[F"0~V;yZP[l/fM#x7&Tw_p8)MTG0sPn_7pQup<.wR-_kuR1?pf(B[?=^@$a+(WJ{8qXD,l7|:>j[iQ*`^D/0H!ncs3s5yeo=fQ_66e?~8tOU[g[PnqeEF%cCJiFB7DM(WX):IFigA<7F9g]Fe*0a@<Lgk;PpHS9_d{o*^.J^tohQq8`UcpOU0M=hd#LWE)qb!%2vy<PU,r4uri8([YM{L]K7F+=VGY7xbY$>5:K
vgf_;Ef)HcLGDXz#q:e/[EZx!ubV1_?SkGQ.sip+`Us7?EqrwY`"8_=r6CHud!+9Zhb!Az<+yEyDw9(q)7B*wLWesS5=-,3~XDUX%[ZZBJUoy;LZ[Z_J8`"(S?$!^D0Ko%IoM{qZtH=Hcqy=u=r1c]K>4V2mwipxfQpuv.xi6H
,G8U)y#E4%O+@X<#HCc"`5?*(n0]EZ,-~aseIx0ciPS+|h7+L*0Gr&|f2jla]DwB@AjxRtL!|18fklr/)FS`zo(xMuO2O;"9PV%K&pc^*C?.ESk?.c+yiU19tZq,ql5JS%Y6U;Jd*$v7Z9]hPDg%J7[7TmKIvC&[~`mb<P[7L8&oj=<qNrIy%XR0+[-(pU,[5;]Td2a:Y*H$@bs&9lA*j-~C}MJnA^L0LdFTqQ);Jm=t{qBp+y
Oj9!]EUs(Sn-<_i9P]QFC}enRo(T5PsZMOHR7OMl7
KB7HLWJ9Q?YXA]&3(%G!V=R1,_t=,Se1sU]D&g!J8+YxBh]G[t@[G^V,X$-ZY(3V8xs"yw$nv`i4tWXdED7J9%_w!)Nt%R@Xi3Kw$4SbL`RPw6nZAZNNf_&ZMd&
Zcx0:,ON=Wn7>;+e/lu8)`PdS`%g+,7Y"eAQ748{>e3VCFG78%kq.wVQpD$/YB]/OIoCDU&*F@3^[=3CV|:4BBw$,~u34nO*K
%B!Nu<F#b`d,nlXu2H+BOl6hjv#`XH5{Vyp*"w`#6B@D2YvDdIV<xhYTnA#H
S5_PMUf$wTYS5-TNN9Py#sIeQCMSR8]-YTAN]2k/P5KI7y!t(j^-+lq9~/]#F!a9[t8yix#feq%-`#[,D2QN]`8w~>Y7q%|[b)Z`5*B/=#2$EsFd{oDm-eeHm#ukchoVH^1AC@>:@Q/D8(,aJ[S[`oUf9C&Ppqu#8#)wbY@Dj0Rk_lJ$Z3k19@,){N
lY$so(#,8J5Ta@v`Y~P$Hd+fRlNr;Ss>Eog}>]3B>EqiJ<wHG{+YIkHv+YAi"X;C(FnjEyO<TtaE3Dv6y/_`N+UL.{/e.X>cF>a7Rxh/>YW
^Wi8EzbRfel2x+p*-QT8j3l{7xYE&i99R0mNbqV`<13=Y`9
?(S@ynHk_Y?sJck;UCma+sHi(&HP/i%N=!aa(BI$k/4]pS;$0$pE?=]d19WQr^x3Umf{]_qzdX=AXV8_$?0T)&5uZ!V3S~F[8;[tI"iHo4vwk"rw
}.&av==036?Y/Fg!1!t;%]%^y9ZZ0(Hlv<YYgcx-*cRU|Li/hVCUcszT*p{0mPY"?9+LE#,-ev)i|>xFG>~;*/+5QQk8uf(&29/H)!TTi$M!_v6<Nj:$Tt|J#+.<dZ(XJDnwOM4!}d-_`1`wTZVF+-c==l*Oj/%
`Q?$)fIaG4P5Q.}i`&"pM+$>rX)-H:L"@XE70QbK*/C93:=/G_S(d?KLL]464.;m}O8mZ:58l,)PJL2iYHXZQ)h<wT&@jK
XiSNE4urgGC$^"R71oHdE?c{.O0qh0o:p7ChqM
I:s@(ktuk>7VMK]a7A[UmiE^^:)ZVmvYaLhca]9$5Z+f]Ad^<B~Fx2{Nbb^3v:#U>hnMirX/xk61khT:{+doN-6Z(Y.#,^=":@@y0cyb+kPC.]x2eJc3?4A(GO7RwX#<5CL9"8U&Q!wd9vblu>PP"vUH82u=@g@sJvs5{,"PR;YW
RE$S3{t|d:8IF{p|%*$Kyh8HxU={[f^V7*rrmH^0HcL=3i0VGpf]l$RF/do,x8
S=fE8O#D5BYX5PQYj)qOw6+h:=ZV)b=FWf)uwx@m}m!i7ZOOSh9fIPb_J(koQcR:?NHq`#N9#S*/]v7Eg:9;<sar;mGZID_obZ;kPR6U5k^R7vP)P[pZt%22gDGqQ
[<aCr;Ucr8kPHL1j=[ZQ|je[pS*YQ++=H4Y?d$CvF#qGiKeAE!XX{?4]]]Z+sG{pk4Fwc/HZ6WXue_O[3d(ZYLf#M`>shh-
EjZV0g"jIq1=GJFN`ojE"H
V,/{PckXUA$b_A<e?9BDd@Ma8:w,W&(VWn
ng}=sUO,[ogU
/sU._jB#hcj9XJ!,j2&E96WUO7g=:9)~DGMO3L4gIm?[[@c:A_v,t3ZF>K$!h?PRujO{-Ti(,Wgv_0d~Xi6{O6D;r"<PnhoS33j^CM4i
Go;SJOJZM(bc9IU3s(.5]
JIeXt3FN083b"/
q>GzuN8a0|OF4[h
4gBj9Ifj?}#KQ~fS#^Ya>t6+@DaHR*F3;.a@$%Hw84UCArH-rW2xI)+|I0Ob(v7DUD%U:+<}8gS#:FP%OrkYqSF@AvSX5e#6A4;OJHK9`:YNs0<</S#;c}r3)!?rI@:O0=eN3C/
Y7uV9vt74lOYvz8/^`/uVJS<s1j68Gq3-8H:.^];;WPuPeE*Z;D9^e(50](i?eYP/byhEH3[(Bx7);Q[$u3}YJpd+j62Duh)+i
4SDh_vTlpo4A%kCri,`oi3D^LK[o"W{L^YE3-Pg*sTwt|G-/?=AUg0+Sala7&WkULc{P*WIrHMw%gOIM*#v^WU.pNvt4ponAfi(:du.,N*P[j>&=0dU>8=0?k;*]%1V"=+5p&k+j+nfYfblcsFk;rT*<wnVL,)2h2IYvJfz)+Dh<5#9-aRlA+vR9OxeXqN/L?.7vJf
sfu%PA(a>06.d}@^GV=hqy.^JYD(5%HXMV0MQV*4SUp64{USvy&3AtBDRv?;W7R>j}<20*Ak#+g0MqvO/%XV%3<~A!:[-h!MK}`F-tH]NU=VlSy%4eYa?:soEnI#MWPt+#F&J"(H,
Xz,v-U`ueDb+5[%!O{Zp6kCkkA@TwOO/P+a@93ghJge70W,2!6IF+79:U-KDyO[UK~,tEk0NyE
a.]iDbJI~<VU,mY$slu(,*_2@KHK_2#I&6tbx,K&cGFA!ZH&s@SjVck^~LgRmAny-$sQkP2TY[:(_)/"[s}t,^wrLPiVW"_ZF:pED_B+aC`uiBIS8s/nx7wS@T$Lp7NTm9nuds}elH>+se@XPS!+Tw;Vt0aPBX6W+]6I(UgF^q[vI%TDtP&1Px.Ip
A%35__x/:.(h8AH6i&y:4%csHCNFz
nksA.SmlTZ{
5`<==-b@
MYEq1~$C$e8@m52dt]&"Up0LUyxkn|=rGK;`uP%JnZ
;Q
[;uX
1?VvJ.D@X#5QBt.Xv")PH8q[r/KI?GBoUm|VjCs)Er|6IY.RmtYL)Rh@58aScaFY=f}A?fMB%XI=$+e2./nQnjBa(Rh5)c88[>g5A0.nQZVpQ+=4/*>B@nV(la<d~#`)e+bDV]KDVL{7e
-G.GkP0yI_O[>ym
hwq=],+ppZvK#y^Xd7RBPsbEM?6F=w5Cg0>#rPA!(;>v@mGZnpJ6]Kq.,o>OL!n2Ky4sk&etu#`ocw?fp^M4sz)nWN!rxs"!+2d*8hBmAUC@6ctLL9-kj
12Ay#1U()8*rW5Tw4
V
~d6[SH?0{=UAyRjCgk%ad=ium&e:K_n!9$(lGatyv%hDPd)C/.Ql5WbJksyCG>&1.s5`b"2!!?2C]W*iTqPlPjV)|S.+~W+BFS<"u:j2:KJs]"+[-d-yj"R@BXa,"Nt5oYl&|
L#;.^E^Lcy(LIuTqS0mY=DwOxdd%s*fS!qS
^^jjSZ/*EY4vn:OYSAZ$!kgG-tx_;RP>N#mJ*Blx(KX8:Is(7(~cd+^f>ZDv14kqsNSQ@vf@M$Q;b?:hECN;a>Rj>3xr@@z"^r4F`2_CZy3j9?M3b
WdNgB&jna!Q)]K%j|l0wFe)2WgPIp#2^56(lUJ2COX}=A9^nFcJ>Tj65TPAs]_zHwi_T}P`koBVv[OPwzVU[P690811DiY5cHkfqi$D4cgpIZ+!i|=Gkex%
{qm#`0I.lmhl,<|V6C#wNI=HA[jZ$x1mvlEe4+EI.`*rSa}"^6+p#EH"OP1WW)5QIK?%8usx-13s6w-L$*ME{J7!Uq5!7c6R9j^&MIov]h}6:Sy`$FwSFF_UE/|@,<]dy<%#@DB)+]f.0%5D1[u#^Z!YOTI_lO=-5L7%=cP6Fs_M}<vRYY(q;.1F.@bSYF:Dm:.D
pu5HCBelN?er>7VhX,#Z+x?dEC!fbZ3@W|31r"ou#=V+TZ
R.&4rF1wYvPH6hD=5D,3cgS4?T|1FhU23gRVUJLU~(SNG>:LC#z8C^NZtwB>sL_
S<:"1N7K}w4K`q}QTWf!X#Q,B
taVf16o3/:L!aQ)y@6hN+OO3@/qK7=iP|!01uR$Mf&UOuGkRvdx0)ai&w3yZNq=ds&jrc;bpb*8-05C=l0_#HesTQ)7NF!#i>+$
"[L3)hu0!
APT*1w,p}qnxX2$BTi>oF0EVdm.-lf,VF3QebgZ:c-TYljtuy:"#(LU_tklN-h4K@%/hv:mA/D5m>?3oJnoN4UHyF"v&|Vj(f3Ffy>H&FE`kgl_Lw"f3e<tJ*U=@o]J""R*k*.Xu$JlUUSj$xp$I>uejON<18]ahvVqC?tbuQM&-Yd@(@2uY<[a
fp&!;w&4vr7EFN,&`Q^ioBE!7hzZ-dUnvEkE{1$4#x}KBtU;2(,&LkuA3x/<5Yd1?,REV[}0"_X[*ua[&h>rkUn6S`vf2.]q$1N%auN"=VCf6AV%]8-%G>+)@M"(RvcObN,.MTJ9/RBjgU/xj=Ws,#Y_w^hY,3-G$U78F.ErGf3OR:gd7,tq.+9U_mHF/R&IH?yX]
tow?La25X)*j_HeNP!OHz2
oO(1o[?@0|=p/g96YWx9VEi?&egnC4gk^Rjq,GCP_21`dsHy!wCKh}[n?9E0t1Eo$kN%!6%uD82dm/@?)W2rxzaUDbaEI2urc5m@Ty8r
Geh)Gv,/Ka#<6`ViCB24c@l3Z>Whby_VlQtNoK@O&"s<`Yg3l@NPvpWLGXKo+*gh*KzVCn)W?<cBUDATL/GD3uW
]EYi8,6X1r.^{dMf4s7Pz:5_U#egc8>rCXQ*>+0l3C0=:`hm1kS9L.lK}>t<t=(MjHe+9&~,i]>0+LDW+Z4<748@U#_<,:CjKHT28+l[>1#!y<&R*$MF2QM&YW~4[m<Vxx
;GDE

FAI@"K3#"x*VnLkP8AQ_E5L/BhYMbzj=ot*,?:5>ppXjHrnFDa$dj[d
B,D26C&BQ{rnkggcMg8i+oOXCu?$>Qs_Pen#Ot<*WZcp1SeB<YYP2&Mj%{"1I_Yl&vJ}0}@|E9;"Lvpero+e-r/_.n#nol#6Hdwb+Bxf2zpP@{u3HjYqJ[rcg(F3RVws(w;B:yT@,4qsU[Wm9{o?!u]LEn,*%:X>%]SWNO/uMmZu=|e+-8g5@NO,eWU^+XH_PFlp;qRB6*#u8g59Lq:|1`LHfC;)Pu?$OT8;.
p_=f]FQ#K(ex!m$9J@-8?~6wZfo[QzCk2F=|bbH0Zt=
.Kfo$6-*#_eV!R3tr.?zWU:kZ
*7ZbJA)$spE~@0H1d{6B;YF7"=*sfcg,Y]"UB{4l(TG%T>D^y4#>Gi6K%1n?r%Xr"
+mX+c4<3):,d;s*zp*+X(pmr7b(~OP9sf"Mn(S9HJ/E]Ue(&mj/+VPP!#{
)B5![3w)3Rk>?Ds&nv%
@%hl*%5&(lD*fukY8?}]PMDSYk:>HEQ3M?Im{<zFd0/6G+aZWNyk)s>-3AM&`5W`8tq#w4Wef.gKBoFN^Pa$4-^vt6>s[t`>-Jxd(UtpnMk8.oN=hQtG3?:3hU_=>,xXe31+gG@5iUL(%R<ewYPQeI*H/r+&vy&o|30+O@O:0N>OT!S2N!c:?1>#R9KQ#vef-oo%-.^@7jB@84,.OW.;Sdg30CWv-8GRt8Hi02"XR:zeYDh19s^k8(q&Zb/3D-ony%q/xvda![F&EDY&[T~8y.-JZIYp/=L,E?3k>:G$I/W3uQjF
[2Wf+!ktSZYv)eL36V95/yZVpK@T87Mep[GDN7Ypqs<tW,i+1F]kBm8D51J"]T-LVECC9/o,:HAq@C.vde/:T=Z73}1/T]#)"{33dqPPZg)z3CKmY;a!17nX&I1qCVtvKuH(prt`75Ee=clmQ{CvF5Yb^Y@iW">T
h(u&Y4ZT
,fu&_Ov{"j4#"pBx=H0)4S^~:Z?Geyq.[=gf@H;QPB%uSiudQI]
1oFIQOst/YwwJuvI_uUQejVRHmnz],u@/RaI&4F?fx7#!"Ux/3=e74=scrRH^z1a-s-1Er+(MCiYVejkYtV}!I/cT}8pB_vU!TY#VXP1.O#}?l-IGp[:$s#o?xB>8kgDk6O%u^bIFoF7,~T;a3"N8=EQ)E1FP!`dXvbdv>oP)05PwKC@a$b^HF<C$MRWRfGn]8j/3*/
29,6u!$`RBv|pP%&[c5nQ}Dn3BBBg_`2PtWhW@Yb?/Vnd[_#-@lD!mW5hZu=nFueh}:Zo9jf_:2#<;
*!}p0(|R^Y,-c$SxE;"*=H!OTN<iYTMG<$6#g<]/,d33IB)>9^>U"[fIREa]XG16)-+/hl7*.<qAW;p0Q0k%_UQ_P1??=aA:Ywi2E,]g
5>"ZXq]4^o=u&g[~uW$@4qIUEWs,Q1YgE//+ChqJ&T3!2
h4UJaf$0D-w%q)5#Ac=p[w4`IDtS1EU[GX5`f0jNaoSW@[/!ox&pxw>4"^6#b}$w7~u!MMpElBi+h+9=4wK7*p+X.Uqc*mvk2Q#IKC-jnLsskQ;OM|^j/-b|;n/bD:T|e=UK%?x6^*&IY9<gJ31
0
Gyrl2<7M<gOp7l
D7io@#m("l3DZ#;&GS+<$Ty@R/k2[%2H.f`EK:_.bQnp0f[pCj{[<r<:P(J3e<J/S6@MiRHIXnWBcnDE->Gnh2
x9++@$s},80C`D8!O=G@DU#%^$IRZ{Q`Ih9Ju_4Pc$pl"u;w4H
2/ze2Qz"&^WN2>_m&J{cJC_[2f&I9888>I;#0&3>q3#>>1w>=rp:<s
b5&I*Er:dY>,<by|$L*n
)Kv
0Qo4Ke
oR@v%yIk4=:pUmq"T|;fhf]em-_{A.>n32-5enxa"2?bq-U*yqW*W3(tU_<Vc99mm&u6.KCwZq.)5m"plt=yXZ/+[/@G:_;I,I`6@#F?K@]5TR6?.D^{m(k+x,;+47%TT
(pQ/faYM_t#P+kgAXNS%XIF0eRQ{"PC^VBQaxU:z%RUv/,-XpX"ya<&U%Z
X:B>!kX+32^g<L2>digmhFN>y<+Y:EsO9#=jEO=FH?KA#gQu>K>1$^`C*T;-uvSe|=0XtP%3^1f,{IbB"tqO{5D4</{<]@on)3}z#U.?rn"
3_aob5n[oOS+>&t%g8;5;
^_/m}PIa`#blumhCc!A]qo_Z%MJ]}r/%=X"DLqw,m(t-R>/w""Wf>l
Cf[%];q`]pgJ^
MD7Gf4dKI^uc2-.FX.Gw0#uW6Qq}8((2-7(zB2%Kk-E[mJYa8[sFgv<uD0_N`N/(HN3U_yi"6]O#,EVH#"(8YI0F1
0;qiuTU;5}m]&Pa
O_#f$Q.:QtiYUn!Ku^9UsWeW;Q@//t9s*ZVri+wgN<@u?rDx]L<lM40yri?R11qe;H!REOqRw7;tZ(oYKoTYCtU8S
qrIC[?+dSj(*_9*ffg]pR$4ZQ7yj%o7EJtC*^8Nl?=L{`liGo#Z2"P8>w$I[/MZ6s#j/,DHpw2y3el^NPBuhw#,Vg<@Za00tNkCxM<F>09erfuX+U?A4sG_*?B2tK(WaN2/oa@Z,Zu?42+1)+IGbj.!h2j&Aqy.7U`_#JH?1`j*OxKJbCwy,Ya+Re>P90BND>H6)y~qbfDkNYdu2&*"];4X&
{1cG.@FEH4!YxL6;FQz5|-Wmzqb2xl8k+&ed|=P7h^orKiw%$(Yuq0QT#0vSqZjCs9ZIguWwZo}3WLB5%dr^#Wp9IOKL|m-N}>3-OpiQ@e}9Up^x]LAcxXeV[ibOeD(ID]sSz@O;S/E08r{^^?H./3S5mDjpym*f3kO@M9W;]BM1AD4*=(Pvb)DdNSA;mZ,KnSg)JW1!vtI>i.[&2Q"Ph[JJk+bUIgmvtYKr(jCO5dJ;4qDil+bi_n;EL!>*HY!x@U91}+(`fuf%ub+W[w]<@IP!!,z^n<F$*<s
^f~04gcFME,b-o#g`k[i)BpxoC"s{Jd`(lT%Z^Wav!8d-Uff,0>;[+REv
u2Q"E8`1>*?:iatis?H(&f*Q>L;9=.<B5b~+MsG69CHHERqHnW3ZSJe
s(xq[PFqSL($4B852x_pH0`NN_4VK_VS];.SV2mT1<Q6&FS5QB1;uKr`DHD#N//-Jxi;;E?3Te?kZg^%X-Z/
iapzGyl^Y
Q51/G5Xp[wD
Y-END#LJ1SgTUYrZRG>8&57EWetI(9[#%TGN`Gh(g#Ds-vog``m"<5;>,AGDt6?96Wm#
pA".G({*7UK#1f)?Sco5E>#o&0gFIhqLN+O60$"v$a;J];CAq#8J?F+)Cat%1g}e)6W@ym$j&"4E=X>GzSh!gX%NH_[r4#QX@`=.O+9%3du+bU3gK%u>2.2aYO|Y+GH?h)KYUFZ7B2ujZ=fO8G+V]IE4{g#xNghfO_i+r.8ubBo+u#;]XV:egD+<Yg:CrH^dYVZdq3*0cQL4]vPZ91t4]q*eJ*lpv%"^I5[
J]F(y+}W#V_Gh9yU%Ye"vO%9ug_rJ<{La@S;T9-o/T>3u*//xV:L.Qa=rAovNgH:aNv).-V<Gp:l-uk*5uPV>("T?6ibtmDgFlp+`1S_?_^G/M3ED3UDa.>L8iORU5>4lQ-XiwH-
Ac0u]iW2<QI~$<&^_N8uuG`*.ye:Ki
YVfJh`%g3W?sGVK-k/=eMIVIBr,p3]lh/Rb(I_XBuO`c0Al.nYh94TB3M;~7inWHGr_ML.~EweI8(f|R0b732Q|sjOj!|n-QFd-+@9XwrH#oSV,h*z(!jV}ETL>#0^]R!ID]Gsn-R!08T8xN%RwGzvTW^%#li*1J8(fN][%[Z8SKfOAXmfDme@hh%S4B46
p&F}w=4}U7gKjUbLO;^{Vi,dpim{SsNfs
bI5qwo:zN4_wwgeh!HM_<1q(gwG6:FHB^!/8Mp5z$Ww-yXOmsDK#Tb].YrmMgsA0[dkQl%b9vvwRn.HH7($h+NH!LN(oH
dQdU;ujE"@s9CXn-FI1caPL<>BKTK;
sJEbT("1w0-sI5R2WmbVNLH3aPncxsNwZiX3BxCx2^jjc4l:@ECmA-grR*hVy`
U>Rxhu@*t<Lp&Air-)M{8hxp_$y@G2LL6!`WdkOLZGN`Zbnj!uDy.qQ>?beZkBC9g|"m::2WGy/38Lq2Z.@/^_q%24R.B5<"Wo"Zmu:@O4_==YQiPmFH)S>H5PR"S([IV1OCdI118e*-Oae>Y`iR)zuz>hS9pN:r#&b</9@z3~@Xh;acIjStmvce0N<igW"-6W/<Z"3H/
%7MX1?>|>Z[m.Uw29*!$6-_&RRW2^i"Fj1gk6;C3((&Fr6jdwP>ERuZW"4:r,N3ohIg089J3gaJb/pq0lM%tFev-(gupY@,+ti5$lUjs*uk95U/Tr)G[g.UpM?PI#=uUV@V{vw"C*|aFQf5lXK#b)wE:-;q^BUdL)mXOcpB(m7C`A{dWU0Tx?eeN$=;kY<(?"PY]jwIht[:MU[a&
6[`*4$)[eUFlRZhr*]XDW"yI#%<]s-ENYTJ-2a-_}:nCWmCy,B"HcL*"_1(dppcFx8wk>gfYGXpaF?;^E6kC9R{qyQbR!A^3~sT*GPo
3W/`6w4^KWFAY1.%>kfI(x3e$3Ge39i+}uF2+?7tM%1:TrtVllUE~>kW2,1=[@xaVL0^YO/q.31+[O@)9=K014cd|R9F:ek#=jxJ*8e>p#Lde*fWzjM[RA[W)AM-tI|=iy+(=VJ"};lL6UGdwh^,ib+[Har`TY87p)}D2/|&1!7o_v"SU.]R2NtX?O.gU%_>QGkm+){NJ&@H7AAlq%~@3^SgQ+(r<f,F;"s<h5^HC]Sq;2Dww=/k2Qj2dZKTfHb#,?.2V(]jM%U:B:.Sp>F$zM3T).F=]%B"|#W?jEeg6vW#OXXdRF2.gU.@rfvdCkV.!3=6?e+B#Ou##g,9q;~BgZ}%/ec",iO:L#K+TiSP[Yn_AIV.$D#owFQZ2DK5PsIb`<Z>u+K$1FcokHfi-BE]Wy@WC>-`30=J>@|Zq$V2D2xbQ!z2$l`TO,ShXe..Y$P3Zps4(yk.Q`i#a@Ww*0:Bq>i$+YPQs,Dc
UWu/;tUTE:d[6or|_Xd-
ZJw(Xdl00.,7sst(_bL={j;%<Tz>&Dl?*Ol:Ks%glkB:=Mh?[-dq"OE%X[|]2HW^4Ist
QFJ/8>]f:^T`:m9
_IhYF%FPQQro;vo<`>)n)1_>h^RY%-LHwR?IP=Eo5qVFJT3}k{O0N+j$<Ds_Q3I1w
9iZxE[(iev4Vk)B.t`y(G
x_^64[CEwr&p>9?XHjbL*B+^WhKB4
4<(3L:[tZE:]ABhI;+1l"dL`kT-(D6d,[3h5&6@cDfR89wC;,Z</6z/95/lrT}ur1M,cPv4w>>4>xZ#z%i9?UcXzQVpa3<Xh8T7B/([NV:=Xk=J~T7CAo2j(T&UgqO)vrx3$r]"Tlkh~EU$x!&qgf-:>:"=#0QVdlLC*<k2<w")4q+7bfG)TUZWv<c${?#
F>!_jAOtaqYWJR5"gZ|,,rr3.Quvfwan{?#%[A!XKOW;kphHS3x,y;)(JCUw,aP#:j@eC(dy(B$w}c*9dwe/U@
Vq&A(kH=<O#9rEK}H%nI(?/2V5:mRp;)$IKm>9:C;[ar^4h0pk,.g=1x^.Vw`.pAJ,gMCAs^P91hr6O>U,Q<V;1#vawY@oEz.dqCC,kgt<TrB9f{U14
N&P4Ja/m3x(b&723Jh.].cX(D"8J5Z.I2
Np"Z]F_pMcJ&2T]0vRqAQeW{Q*JK.
MMY|k
5
wS(+JN[=K8h$yE08VM82bpa`$O.vt~8iC,C<h{1|&l.>^_xJ.AU4Y+kyT}S&-H9)vD`"*V+5=roQE"r>#I_^f.#@wE@`_~u@8n+vu]i"tw6Lb@Bi=p(>w&v$&Ziv#jl]"^$X-0$&<g.CN6TLy2$j$upR^|xcMn,sz&F4q,.O&CX`-Gs.I-`i!Ujuml7uex9P(!XK6B9YZ:LiEL%t/MMXp(V4MR6uWB?(blW[K?-WaDR$-~J<1)I!T5hxL~XrT8rG,Wvl#t;Kky8($SE?!Tem]^*JjBx|_[%73$^C*a"|&dg11gCfjDkrUHfRCNY.T<&hO"taP&>jqg)ZFI+v({Qo19ar?~0H7PvP,rbB)v,Yq(E~YA"VyVRPvl@=glOs
dh@/N4*?e<0=d=Sue/$&aZLj^mjv7RvbCph)Bv7S!4&[j!=E
<qA}j0i-(C27J6#v2~_"8Q8/4":s<fl"([u2%H(OR-!dyT@X?hon1AAu6=^<^u^y5D@SDY05YxU&5^
-=&a+Y@Zi4zI%sq-TtZ;&`Gi(rxfa;SD(U}(JSde>;fH
62u3?aap(!/X)5p~%1kwPi&vdT/5QGr?LzgeP_Lk
J(dd_dLvTc6;*gedzwPDLq[U4)hg$(krUM*T"E"
jm9"px!q}teeMyHo]PT`ET{0r,NeCy._9/NvO?6-J^T=j7:=WmAnYr4Krg|U*B.@d]tB=0>;[2gbe5O&o>PT@sg1r2P9C3i/>J{+*#$:MkYE(lp$E"iryNXsJCGD]g0eBa6Bb?p_]*f,#1^Aq$mOO(42"EU5n4%x+E?9,[&JsI$y7?Q(Ek@NaJd&/j,"p[hPw5*bw")3[>E_o8.WZc;7.0-"m`PL"`4!Xtrb)YGt8v<06f`L{:c-u0SQZ;[^Q-2_gR?"dKUg;k^7%Il(mU7sCV>AxZZ(L<r9PFPL-*e%WKdu
R
P![)427^-d
P$_KI@OVPR~,".*]zg3[7]gAfWU&{F#+Ypt:.aX`Qwf2BR@r1C7H}%F;xt(`O$?/TY>#xanY+`nme7qlA2:5^`Ll0c*-0?{%d]l]`_`5BcmxTGTD"3h%NuOZN:58v^FP/XGVrj;#idCqDY!a7MC!eJH0q/n1"QW-:P<fMQ{2Do#+3=7t@Ou
?y96L`T"Li}jt[a>!C*_<4x:a,lqnt,NY&7;a^HJ"2MWb$Al#fPf"E^uhd(AJXksQ4jy[_=Q1_z7ZpMlxphdH9wLvl($2T)dL%6,-ha.D(*<$V^oo68/S#+d+<?``(>3XGo":z%yBTy/V6J>+nt9[:7qR5CvDxx@XwEUK.8q)WeN+DYeB9-,T5-%+pjuwN?`q%5Wj]5_g1djNdM4IF!*Bm^%*9?Y1KYE*7Q-@;wjlO]R}#G(|1B:1[0Q2g,,]gmU?MT>}2M7]CLs~YkiZogPP2p99`JJ
KjRr12yLY
7DYKg#)uB<x<5$]vHA@x
,T1^@S_R+]^tw_boby9;z&oTI=!F^]N>SW{q{6
j<vfsZ/~whKg_d*t#DL5U*4m<Jc.L{@)PdS~_Us?QF$4K.q0$e
q)%!
JJ"Ic)vq#%(qEk8j2j6S,GK4.k)PH_!nIWCuJ,VZD0?3RlwkYfNUn$$_dvf(G%80)QmejPj?F8t-jbI{WR,<?u"vj`u<LBD.P|!M[oCW^`^l0PHNZ|]@d?T-YV(2(]1R61w{mu)GI|&|CNZ1?0G8.i*6nLU/Gj=h=rX]"hAS44in(o4=<KXn#6V3c]3e5kYhc*1Qpqx;/i0au&5
gZ4mJ`8n06mE`kD8WcLl1w5Ho|
pLFHALjh%1F;33Gwt#l9F(?Q;x@n./O9)-rom6`QlVlZ..DZCplOp-r)$6Ci9(4tULIf&wqGa)P$xCSdL>P5x/%E/u|;f<$B|CvsSZ|vis@COn`?%2zGqmCd+tU<Y2.?[ggKxT7/a;U2[U%[z3iU:A)]$3I>~_YF1>frbY10r]IU_Ko?Mi$vSlBN<mls|gt!aUcVbF()Me=!qgmDUl7P)
3]VYU(F@dr~:4iGNgE^.KeIL0nqL6n~,U;DJX=kMNQ}Lu>,D
!PJ]4vQsHPi?nZ@Q?8;98:4vsqHrtCs!Z`Drh!&cvhw@svB{%q;UBj
TqTiIp|Zk^Pk]Olm4[bk**^?VyRC6l+."j82Qiu={vujJRViRI#K,.vB
LxFUvx0*"fv<Cfs$]?*<g;dM3Ye(Y!(WBo*|,[cv5A?~QnrC
V<S1%<icS8}Sbl"l<<VUk7l&:a1Yj*3D:gZ$obuN)`IATF:N73_BcHh#wp+(lpg?4pEUx!Pw:Jr;+_PZ-DxcFLRm.hl@_<"nmC):?&e)IAWki?*@X_Nd6t0v%!/u=%`$0Ovyho)');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*M-:_crV?&ivhwW00>hyk#FBR?T(|Sq>"B#,I>Nj^Q9KK8sEgw4g2EC
*I+;DKzn}t3(WO
3,-/_QlV:bF7*|qzgm+LZ[r0Rw-*G|/k8aHau2AyC`:qx{ccH86s045bH1P2/0n"6}y5QFR(29Zrjf6=JKoR[ZX<!#*8i<5q.ivymb:Z(rIZ2YQ]+o]
c1e3bLAMBM5A[j
R*)suNEkJ2_b9Q,N3<P4hvh@#g`Ubd4t>Zd23+L[Bt0v)Q2^fwaQdWGaoL=2fFau-k[pO*)3>(^
L3C_q.O7n@ic/h_PH^?3P(D2iqS^rMAuO_swO`)*TiGN,)~(n+#u:.`d2HKi,UCsH@.]A%*j08LgKJ|@kX+bbB8Zu(St;xKfJ=}=9(nou8]":5u&EO~jfR@9f.[9)!m!m>c&!6F6vOL1`]MawSXj)67Xp@ygy7)9*q,X#[h/L6mfb@W@"#ngdi7B#e$3RlPyr[q*H-nfIGG."G="QLE1bbI!fYOm7erh[>m4s7/_nwA');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo
base64_decode('iVBORw0KGgoAAAANSUhEUgAAADkAAAA5BAMAAAB+Np62AAAAMFBMVEUAAACDl60rTnZZdJNziaOerr60vszI0tr8jZH8c3X8SUr309T8Ly78Bgf8r7H6/PpDBKXXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAbRJREFUOI3VlM1OwkAQx/sGG0Xh7GwTz7b1AaRwNhqIRy4kPRKjpcc+geEJDHc1chYPfYJ6N7I+gJFQE+UjJIyzS6FqqzeN/A/dtr/Mzsx/PzRtlYSI0fd0Ju5+wDMhHjCTMIqaXoS9QWYw3iLlvRHtLMrwKqDnNLyM4m+lReizCOjXWCgqWdPzvLgJNgnvUGNPV6IVyc7cim2SrHKDMMN+L6DhTKgBDVhqCyPWFW3KwfpqwEOAXUembeYAtn0W3ssErN+RdbxBOcBYowrU2Di8VrEdWcQrx0QjqGlx3m5LUThK4DFRNhGy5lkwp2CVHZ9Qs2ICUY1cGmiUfj7zOnBTyYAdo6a8otjzR0X1UT3uSc97kiqfFzPrMqM39woVZcoUTOhCin7QL1IoJLAOKcrniyCXwUhRboBplTYPSrYJPJ3XLS6Wd8fJqmrqVm2r6vxtvz9T3kigm3bDzPvxxqmn3QDg1l7VcasbtgEpqg+X2133ixlVuTky0Sw7/8eNF+4ncPi1oyFYy4Pk2tz/TPFELrt0w6aX/S93FMPT5OwXUvcbnQl3rWTT1nIy78akqjRbPb0DRTX3Uyvxl2MAAAAASUVORK5CYII=');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$hi=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$hi=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($hi["bytes_processed"])?array($hi["bytes_processed"],$hi["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$ud);$_POST=remove_slashes($_POST,$ud);$_COOKIE=remove_slashes($_COOKIE,$ud);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($t,$B=null){$xa=func_get_args();$xa[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$xa);}function
lang_format($vk,$B=null){if(is_array($vk)){$G=($B==1?0:(LANG=='cs'||LANG=='sk'?($B&&$B<5?1:2):(LANG=='fr'?(!$B?0:1):(LANG=='pl'?($B%10>1&&$B%10<5&&$B/10%10!=1?1:2):(LANG=='sl'?($B%100==1?0:($B%100==2?1:($B%100==3||$B%100==4?2:3))):(LANG=='lt'?($B%10==1&&$B%100!=11?0:($B%10>1&&$B/10%10!=1?1:2)):(LANG=='lv'?($B%10==1&&$B%100!=11?0:($B?1:2)):(LANG=='ro'?(!$B||($B%100>0&&$B%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($B%10==1&&$B%100!=11?0:($B%10>1&&$B%10<5&&$B/10%10!=1?1:2)):1)))))))));$vk=$vk[$G];}$vk=str_replace("'",'’',$vk);$xa=func_get_args();array_shift($xa);$Dd=str_replace("%d","%s",$vk);if($Dd!=$vk)$xa[0]=format_number($B);return
vsprintf($Dd,$xa);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(23).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(24)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Jf,PREG_SET_ORDER);foreach($Jf
as$_)$ha[$_[1]]=(isset($_[3])?$_[3]:1);arsort($ha);foreach($ha
as$w=>$ii){if(idx(langs(),$w)){$ba=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ha[$w])&&idx(langs(),$w)){$ba=$w;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($mf){switch($mf){case"en":return',X/+JaMAp*4G`o>NW84;bF[`^o`OE=i_^GfOzZd_*_|-$rflsn91ig
_?_bw.*6VMh&xu>>c^U2C6V3-DQtO$]lJE#D>/ri;!FEAx9d`5J?
}K,8f1tG%B1JHJSkmP(hp^LcNF9([M{fgRKR}cP04c$&]?IkcFuEEm8:r
F@3>BLi7.+/1ML77)I1,@m,]&<V^q;:R3[Wa8T)0hW8^Me$N%ygyFw}>qz(h*tT?(X
d?`qX_SC`7WJ6[EYXcQMr[]_0wAaU|B,26g!.N=>
Z]H=ia)ioF/wkb!)K"$[n[@bYOB58&h?"p7WEFs?7YUC8R.`D89IP8rW99Km|EO7k>[%~Fv<Dr(W29uhA>G>49J=GIk%./hw}baQ8G$[=p$JwGi>"m9[7jcbg68lYb(FjvQ1_H}63?iSk6qfM*BF_ar[pVDZ+D,FycHT$ae?}IZ5"k=+C6}JkA"oZ]-,|q@rar=^H<KMu_zn7U$ebcNwr^((P0WN}Lj+>MA/&vo[8EmGH5,Kw0
*zRN1~K"]U?.qj=?g9DZ2%-Ige[#O3)p/(TYFX6cfS:Sl?4xe:=LANH}hJmDe|7SA(?SD/?Y-a*i4p%9_Qol8
#$w}&}(rL)b4$bsK$dT<qF"U>r
nZ]unN^UNM#&gnt_G84p*hUBho/y+Bymg.3jdR=.Jn~n,XF5WK."QWt$Zw+[*B*N.o}ow@-,+5K8qI:8ygn3KQOdl]~;}4E`A$CcQoIgV[<`A.hRi?9D;Jj$^;gWKZm)3`T+RJ=T*A~0q,keNL}Nfa`G6FDdrJE+:x4pjPSq,jKoi=!>0,~Ynchx`60!RoIQpK#,XXk<d9{aN@Dv|bov?_*4AY&;n?%WxtHYeBQc"V!Lr)V@"By()mpdi1ykR78]
x6CK@LDYM.)C:wi+4xYz
^.9U#L[ZN#XelreYP,oo@S&%sKm2"=k3)je(_#p)&9+g
cIDXNJs5>P"I.+7g.R!W1VR"I;hlDZX[TF1L
RNTuw$Hn=8)kZK>I
exwLmP*=yRR
#&s/0)+mCTP52$r5>W.Djn?$RtZv11_/UU_peY%x+D?qrAg$$r
Z^`V"g-=
FuIyi"y{xAHXG[v}TW_WJ?,jr?L
r2,Rwf+#wC4,s;#;-a@6nNB]1J`12v2#=u3/*20V3MrD@}Y;I7Q3J0-4SKE&C?CkyTWi[h*FoN#|__yd:oM_5YLAPk3?U?pi&N${3RE!B&;er5B
>k.x@Ftm5wUq=%rNb_L+!l=Pl},%X]DM9}hk68]MjR90,@rKEJfA]O>_6bpNl4O%p!A%4iNI<V+pOriF9rV.NR7UBPB
:Zjlwa![5WmTR$DKSY
;WK&,FhQy<Q+LmA2eAB&I1-A%X5DjO!2{j~.F>h&4sc0n&Bk/:oY>b]URoc:Zr["k8RH~k.]&)nSAeWNZ2<01j*)7v]xWuWpH]pW`ATf_k/?D^Bn5
oJBnX&W;P
e.5?BhgX8.omsW7Kf2<F8cNJN
*p@MWBcR1aOl_AL3<1kK}]vg&B9CdHQTM5m#ot!LITY=]g:C]`2i=iL++DSh"Ld.Fb>-*B]wxBh))=?N<Oi?Zq:q?9yr1&Z9g8_=io|?72df:7,+c-MXRZ[#9T*5qer.ZlJ3l)426vZ:cf{O0k1(ARbDU+,NV-Kkuf!l+:!has0+pavISMzV:u#^Sb9V(qP@9OUgyPdK</nH5o2Ron@%%E-9D^s&N7^=jx_Y%vR^c&gQ+m^j}?vir4qSz+0-#ngFX[zp;GMZWd,ZI!duM[:*:,P
J=6_+"*Fwe-epIj`GI=y#N6@~#qRGL3;2R=[X`An,fSQWQW2u/Gm#%Y;D()lBws(}0Q,DCtne$x87QPHB!CG6E1nZA!B)x$M.OQI:.Ps5tb%sihwTK9ipMUr/fZB7vb$@P1,,vvRt3CAq[ZL]2K1SS`=`3Lk>,lWpDB#n?Y13@Ib(t`G(;Peyh?6Smcm-={il5F?[N1Knj_#cWx.4)!JGjlK~5?S}H5)%<7k5kJt~L//(Y=GpQ.w4oqYoQ*WohK5)PND-(t=EBxJ8xmy^wO-gOpSzB;>*b6E9xQ2Oz%!}lee?nUNI,HP4Aui"f!PyDrQ;-a0pxwFza)S#J:iUR^Um`vJ3!ruf&]
FS,^8,jA6&>&$YV,!!(YJ(1lEUD${dV%:!
Rcn#Bi<UrE.B*2R~0-XS*|^*?sm]vsRECDhPpCKni%T4fqq=mIh!T$&)MKk=89#!Z_<OwJ5.]oQZIB*apsxDQPDZ<k5z[w!IFNlDd#e
@zZ|ZkNHvPDT*~"Fu``W8$.-/cuzisP?E%rU:Zd|1QeF8N08>3la6DJH.X4W_W>_3>x!qS-kv4&XZDAerZ]5.HhP![
@xXE@N2:4j54|tCF]qpd=KfeI>:Z^9YXnc?ZzK&^aJJ&!u=.E5Rcz+a6iH^E-74;^u;1g+0mZ={=xDWYov
ph,O`pr0^)wC1?hIv*SBbXEz/fas6{I+jbTVyrR2XLQKa]K|P6;HO+$|/lTU(9ci7qp2!_+f$Z@C+>^#
zhZE`O2lJA+_m+:ji-z[c4{Nvyj^[yMQ
&Jaf[*ww.GmVxZhhjMu:O2Bh9R>U39w(c}P#[Qp2*Pc.9=M[WddgCs!LA[Zyj!TFi9x`s7q/y{(e.Mm-W6*`f&^+(vqLTESrVG<;NEe^"r8em7,+eLj=e-$eF-YK=tm{5kC;jIBu0Oq!a^mYnVD#W<tnuO!s3/JjZjxXnv9dDJGo+Udf!%[WWNNgVOvLM[XgOTpUeD8_xF*eU[lp]o"z021esy<Kg)e]
Nb$34_1Q&NFx
#r"@OMx/AAI`oC!H!sa~?CuRftTjcV=;Xh?M7]b`/*:Hp{C/_l7^1l<<g.?`q]AL?<O$VZr
r.bV9BRh:s!R!"ux:3?U%trM+4
(+&`-rm"Q<p#"MD2$(%:cZh?R^"6QLxXrGWJ!
fql<B1T@~4LVjhAThCvvU<gq8Ox4pN1DUgniaGc#stqf5uwuFo)';case"id":return'$]^;BiDWB&)qDmh4{;qx87u>G3`6L)KsmT3_l$1;##eE|ft4{yf@6nciYQ2Wj40trTX)[#@I5nb[Zr]a:_FZt6_jn5R2*o9n!j+VFJyN0<S5y,{#$PWn.G!Y?vgnFl55gv5`YJzD3k#,bQl0za/Jl
,O1L?]FB$6YbD%us"5Q4[;By]0,j@)[
O14n!Ytjd6meNmC(6`QImSX2IlEUSM(I2u-(Td.vVw6^2r&/t]iwPR?]UUn-r416B_*w<,{gCx8qwOJA:FL3048m^tHtiHWx+y.fZ;V%/gTlp:A/>`gPq!0-CwJ9&-0`V`*s*R{%^lYw$eiR@dfMq8gK)@_G/ZSLx=]q*,gW%@zZOL:o]b.@2=t;Kx$pA]$Kd]j_bPOXBZd?av3haQ`cE0W.7D%FVI
cNs+C48=^H(
eHx--WtN=ym!:Jw(e9[@d_^*eNyNG}DYiplka4%L.ylP.KGwkW&MA|rTtyJ@T<r?_~y9wM98-BG]-H+sM@AW#C.K!05Y%zY4,1uEO4&W1xl!xdyC%MmBCH#@*IBfu+I?
([y)&SBgTt$+(u~oK7h-R6!xlG4,[%|/TUu,JE@5Q6rBbklZYm#flH4A*B^H#U~t`UY4zO=Q
8=oh]%!SAEtL:%I{f&wKawF>6K$9ajnKe1"UiZOM_y*$"{SVc@ZSQGdE&ofJ5|%-u-6T>P6u&(6|HW1eN%gV=gAa64,re-L7iue.CEl12|:MUzw{x}U|`Lr4G}a
:L4Q%U@$iCp"olpo_CN+cpD"ui1*b"eW`P[cFodxHp)
,14k0~0ZENe9[}+Sen8$3)E3X]$~Hf+mF_lh2mKN#Vl;PwJ=N2cWlp2m%@1WwjsVrN.%o@yI#_)Od1szT?fkE4>v)d.9okHAO`x3lR"UQr9.R7cI-unmQZIUn-7CYN+su|<f4ArtW2rZI{qJf?`}I&cR^^[K!qHrHULIoF^t.gmxL}eEhr8,UX.boH!hW"f{H7ZBBw&
7_v~n$me5raMUfNU!YtHSz%3;`16#c41gmTkuUxQTFMb[E#vy43%N|y+/2S|ra+LnDr{T&`{iJVd;;wB`K_%gzY)["(<=y2TN,QE3xoN(R4R(:"H-&=2I/]<6zt4a"O@cmPj;{[C@e,@!{$tq$<_8-0tcIfnu"#>Hj/Ev{3,C"(-[g-JcD);wMV8C0rKY>62VAu>#Z8/>-8NX!=q,!=xhAFclkFxcB;(SW=djAS9"n23c)!>caT<JOf[2u_J+u7K17GZ63g
F3;*$uj6$UG7e#myH=C`({TvN=aFLI&V#)MSlgUm?.fwp<91AB*m:f1F+>phZspz;XFbx;%E551@pw=E*%o9b6Sv2;[^R?LfPW;U949NAA+yri:2UB`,u+AuHO"{q
#GcG$X%s_1>q>9$;/u[M$?il^0c/e!2&k{
2mODHbHW[.d*m-I#D1sVy+WO=D$E7nuI"e5*hjU.<Pj-r.}KfS$eilyx%-cCd_Qok:|$9.i@j[?
(D#whZ>3H>J#p37]u.cF2m0nHak/-KF_?I;L,ENyXHfnEO:5#YPmP33wS["on5D8_q_beL1"eO^gzjOg(Svt
lUl/iXW%l)Z_rlc=iW_dLh9)i_OnV7BR)#_-qXc~f@5rC587d}1cU:$NSi=T^Os$q]Q~"$EAw.$b$jEzK=PQ&rRWRcUt=3f1[l[[3f7ZAZ9[:bp+cc!)0~2_8Z?8kp
;1dhbKVn=Rgex?]rxaIkNbGRn`8),cHBH^G,T8A13";w{u6_BrH3TC1eJ<Q5|O+NeTe@.C_QvjSA$tS:g%f0yUK4K
4t%na(A@umRbhF:+c;>Cs;JUILG4?Y}sIE!o<:i!Iy`(EE#l-%A0<lWL.F;)6Yd9&vLrFYoXo+3!_?NL6rk=lVWhu_>4Fow[7n%??n[3d4[eu*m4cN+updx!+=6di,]5y]gOaH{[0(*WcKv79QRv.]b5&umM#&c;#%cs}G??X59#KuV2-fA"{.B+B-rM2C~BJT92a(?dOF:H8S?Yv0E7@9=1VO_reX/8(&WJ9PBMR-214(Kq=
-Bu60RwDt
ayeqP[1%O;e)]$GQUin1"[7r&`QKl>$"jObK&QL;j12j|az[n^[02["okV^_7`%sHT&5_[`Vx3,X-_[PG"e+Kg31Wb9)oK$Ij>vai(X]}`luiTcZ.z!Z1U2RT3cx$rZ!~?45nD&c1?tJPG1p:5"qV+h`qnN_5lDsbs+D.(fXK4
xQKsUPS_@M:q2KueLw[ar.HGiUAZAhT+Q&p8uEEKnr`z<sA
3?)ep-7sXbD/Iypbk
m#EbW4,,Lx&fhT1|VkWr"IdvJ"N;LID=hu98ZL,dxic)Z"4$[EgIPrL
DlBSbGfK>S:UabnsI=DLg=Slsm]gXvk2&MoKm(Y@I2U{:=M4_74`FW_"HMiXL%1dbv7S$Jt*aqXbjiG7Z/l7<.#zn1!?RsN
U[cWU!]LS-"``Q0k/
sUSM4%)x.htzTvUI1SU?wLi!a&.C5.ci8H45J7Q#F64xCEp]Cxjc_Q*bp^CiivLtX^p+1aF/A7Z&1@.,ey.n"A:M^jcf0LR
W*3e&xNd-(VyMX5EWTco(`(&U;*U0nRPk6sx"?kBUK-AP
?y-bYS';case"ms":return'+s`@Ag~WB&)q9nL<]kDr#4+k0nbj4]VagZNYGTNSmXgHkw@bX)yQ2:%<TSt3oEwHXSm&8fkxRkDH.3:iZd+g%;LZGe,v(YM,^,gaxG{qhOWtDT1!>O5EI)BZuGBO,:09
r)M$*B"3xx!.,W/jR
f|K(M}i]indbRV*+IQaxGvO3jIS$kgP*9mKnA1.Wcz$S*LRyKM6HA9`$<v7k))B]%Zq@u2(c%Qf>Kid^mz.pg&HQITj5a13-#GHAj9WaDt^Hkqkj"prEup+{Y`5A%2fXkQGBTm+}`)dNx]AAo0O9N88^gaMP7%Z42EmkXlRjFTmaEkZNSU]:G9Z/
|i=$tm;4P@S-~oS`}p/cJ:iA_5%c-Iar$u5T$C{b[%(8uj[JtJ[2,v?ft#h^G6=O?5"0JGt5c4a%mya5;r>S8-_L&@0RDx"d+Pcf?piTmd&*T,0_aq3>l81_ND)5
Px!J-}3x3D[~?W.#6Z;SJ*P3EcQ`exVvFp#.LO(kp_v;t&]%b2h;d^Kx:Bi}FT`su~lEZw5wrAm}Mt,`rt@(*:.2YE;bC10#?="(+:ZctJ3t)NUnf~.=*voUM~p[CB9h&8LaUSC?jHm.DVq,NG^hASh4xB3nyRfkq;TY5k?A>ESnkw!BjQi>`}.2PT7{U}<jiLC0In(v,kA<wwLVNjO,XmdN)%#^_JhV"
pAE~EfZWXsPsw[:y==+EA#&fXG!+nnioP7VR7;EUeS""O1(Id-wAMyG%h$=w$ueW136%frO&bc1P5}b/3Z_bV:cwBx4=ALijKq[i#s+Hj3,A"eHT]sS!u?KjHt
liN9WaEk03$l3pw7`QZWCmt=^86av
gwT=A4hHKl`#RsXA1eQG5_A>i3_w<)n7oRA/W1y**[SJgFS35%dS2l!yM$X1V$38PLz0Rq)34CCZttMR{^TSJ1BQBFM(=YQ4a(&JvYEf$/NN&:I4Fk#$FtHxI6LKQ_bb@Kh0oC1fuP6An7"Z9YBV2WW>QTyf6<Uo;V[sC7y.6KYX8x=XCKdP~iyCVR>uFIKgkM+kq9RnRC/;JaNg`LK2Ee)Uu</v)5cp?^>i/jP),6H!HI0PPyXoKm@0Y>@,1NHvTH(3z[[4!QhFO?)87tA:|1(9#e%Et>Au1fH2zQpCP$+>S.gaQk.noy2A#g/lAg/"<[zmLe25z4LarT=IKycVOosi|=tD2Z$Uz)c%[(x<8O&
A_tH!fMU#YMpI#~,ui}"OG[o{m6!/_wx%9J@8,3v#5ohtcQFd@^=3jRBT4(jiHC%$BT5Xn+7n:Q!_J=A1E2(3fAFDXvl-:LCi:zW@7|wm3TVD%=)<BCAF4oOgBM,4Onr59cTjxBTa9>DKw^.*>vA.DBZl`K#Mq+];^OABcH9[_OHt&e"L;5-jpQjOf"*Xv)=TnSK}YCd<GS,9:u4t
7l$/7hy.+R$ykr^(?r~<*Z=wg$NN5W/%?y9WcFFGJLd,vr%24]8i8N,@1Uiv{j9(;wm<@jxKOBw&nnAfQ2=KM"aE;ll8TYO6Ggv%>m92,lN0,3`jRx13*=&CQ]}yT`4CD;}52HlVYOipVCi2A&S6;a5h?%X(gE`-TjVP-hP`JAwPyu/96;w2Q4NTpgK2t0kkuxa++x4HtQbX{Z=29k!w}^k0+O;8A(5f}S/DaB#]K3T1!f)4mEYa%k%,$O1K#C$?-xZlP9D*FU^n~U:gv-vQGxmtprl&Vyn4ALh*hrkFc[)>d@$>547r)VWlG/ZE0sA,Du=5pa
*//i+|<}wn!K/bREmV
SWJps&{B9krW~cek4$&_7HA@`3pF;LW^Z([`:ZjNY[s"+)d8e^JK)G!Tmun6
UNhlEvnUfSkx6AVu_6FIkKm;*_MVKn<
m43O>!W-kZz!(p/V?f]*p)=7t0cH3b
znc^Y@2k~X._hkKu`7!B>lNr7FIx-lAfL(RS??n
}C[,ZukB,E1$X,J6;;J!;Y*r^It.Pnu(7TpUQc4@ag%Swm%9TpW-FwRwuR{lSDS&1H)Hf7|A[]B.p0C2!FiCRR,jpRZb4#UlMR;GhXdFTAH;)Vg7a1y]IZu9x;a@w._U|dx`+ciR.*X&v8|anDc?oNi?|hjmKyjQhKH=Z?Ikr%F%fs+Ew(t@:xU9]dc`{0
Ttrw9{`F:;<_r7
p/g0P]uWkk,gR?>n{R<JL4Vn!(xh3M3h"n,(LA_J3#4GTtXIrGr5bSp_s.0lG>[6x=v=TH1m/kcj3.r
S=f.<^kv,Ms!Q';case"bs":return'+]^;:h".!/#/Ri[/,!wCjf}o,`3*`Lk8p3=T<2Iek:v?A$D/ib8*:)qF:.42iSvBtB`G*W_qyC^pf&eEYx0gfW@7JWl?[CLM|(+w+AKuQdFhCvejhIBbXoca^b>6Y-^ka*0JGy?c<b3xXSHrFn40Z+<H=b$/ZH!+h4&`
J6uHS16L/H;q<{du
FxcH!/0>Zvq`G>RxOO14P$6oCATJdtNn#k1b`%jE+7-j%D9Vx:&>~^nSBWufIR
dx)}opm
C*/NdOtM^YpKnS+S0=8zcrgm[tZ*y<DOQpM%b*1i!XxD)Fa_^FA;YG)GASbph_
IcRX#s9T&1jAh.oAFiNhyJ709`|i5FT_T#0ZHx9aU3NFE4glB>ERWdS5{v?4,Q],]0`y^b[/ay0AK(nyt`Eybk[i?sXP+)$Gt]XI~
?^Q^xtxBFdqEHT=n`<XAgR#Z]!A8.j#GoCWQx>:+.s+cwR!mK8zfL=*BLk5cJyc%cP/?Ndc_-5~Geb$E[sD$@/CJ?T+k^P~>)B)peR_Tj8#?_x}kQe"Nt`WloFjJVnKa5H*3dO&WT`=oUbVEivYamuy5[]s2Mk}KjI6ss`j@Poa>0g#Xx)=i_({N;/&3X(aS>Js<#w")yxa?2OR&~D@87ekFp,#ek7#iyhtAz[na_q).,[*C]&MPUoku+b_Hhs%DvlPUtb*;&$5L`x*1x[3tM1,3%vU!q@VY;ekaTtwm]0nx@PnJ@N).5!3Mid2^"DOP5_3m!jWg"F
cYL$;|Go
Ife4
uy3sCqT9Ib
m9F&0##Q90OIwJ@8uQ)$:?6l#">kDTcDS0|1*v9$5g.+$!2.tf!y&yg9bTBWUum2"NHe6GHv`[}doZ*K?K(0|1,t]wXE>X&"BPJL>?_xX7f4~p2a(U}h`5sG,_&Qk]p&>eI-[[^JzB
AdoDO3>zR<U8l$2Wuvii1+l>G7.$_>4A:+(Nf(DOy!7#wq<q1>.hb@!&GguEJ8,cmdIla5>:gp#@=ueJ"9=t/I!Nw(@A4"lmYhq(AGk9f,")EgGEj!u?EyLk3@X%Qfj_.7dS&ZtYXe,E_?wBGuS/Y4ob)Pnf?u/Kix]leMDmNWtFsPLynUmPjsd"Wvcwc8Hh$O!pA;hF5XEJQX>`$n2SJP!2<,Gq#x--91V5qgqkdBq?Z4vaPDPW%&3UG4W6x[qVV#"7-NGw@EK}8`"L<MaP-CB(>+-E=T@}%^J.s*_:$>.@nMl?Zy>4t3vC0z*^ZM`"4[,)[[M
r>8-^^t>ElK,]*c@7J)a3(x7,7o)*6;3bF6p?/9W:z7#F1p/f]-Lf]U$pdrm!<(&$*+No7nC3V[v&4R|o3Vy2$C1<q%5VA`tofYH`;)C6`+=G1wbpDnOgv3~Hc5BD(3pdu&@BRMn
^2sAX"RU
lM=q%0-iHU^qoCf!CEX
m<Wn+<jQ
_bB;T4)deNQpkB9pTS~p*u6byg67wfZc:Wx!/Mk-i3.&cQVnX!V[,+"%;9bQ;h#])Q7^^pC0%Va5=d+:z4p/}%s2O!Y;.mvUon4ECUH6y0~g^ULjcD;E{aAPDc{tHKm_Tvlght_bxmR#lVApzU}&|+YZirpgA</xSt,&~y|c9=fFO?"u|Zrt#GGd%,T7a<`@7ZW*Z-I)wII5:wFm+u6bJB9XV%it_Ei+.N%)[EoReviiX<Fm80"Gp$?
qC.BFLDe^"1O3_[I$HFIi@.KwDV<C#8>]:E$mQII]9LZ&_>I^!@`@C[i|;}3QY/qaa8WbrtK@r[f1![JBx!i69~!Aiw(!DD>`$fyZ9zu0EjyfO9-w-8fK6M$:t#buR)f+jM+KD)-0E~N4C&%
G6*V?{&t5W
%g.INNG*F.N=6+v%4oMIf%5jJFzHm@9SjWcKpO7Pkw4<Dud.Ty7nDD
`0?<*o?xlo_Us>oBU2Ec?}O&`Q$Vd.ox1kH"DFD^tNU-sH%,H,*O.B)o=5D%+AN]DVJz?uekX#*of@eII|$
&[W>
R&/#$-Op6?GqBpR-C)eVTOQ,[tI;g)rXA2n-r(P933J+<AH#e%~&Q)c8k9/=20sbX?WC5jcQ^2}qA*hBOms?Q;
D~ey3mO`k+o`yy
Z@iPKeZ13"6p/eD%3uZQ:]hjwY*[p$5v6L5`Ebqeh"FhOUCFBi2c.G*=z<WxdCId5EeGy-v6}-(0yu^u64-@zUs!_5ejV8hmt,el^kN3S8lNaN6";T,g
+5B**D8penj-CJ5u@`<b^a.RnyLpa@OAaA.&i}&J)0DdoW=E&n+5v]TYD_fPL>NKt|Bz+ji?:XrCxQgh7,E/dm"}Iw#Jyiom[_MP"G(mRl%>(%U;mrEH>f)|#mwP&x;AiTh=o!_.;RU
g.ycX9x|*$e}b[ppX#$kR(2AQ(3Xq0(+qPbWctCCRaaBZe=^.Au]l"SO)~KKYbOt):IbLX&a9*DRSg6jR;0p2_8C@Ni`^oEC:,RniWppgUk8?zH_+;(I;=rI*z5DQd)p&Ojg^.,|`t,},{r9>L4zUSn.?:9FS:v8RG^4>cg>Ag!yC2><4GLGGlG}LRc:Z,7JCsr{TbXGU;LF$:
foZ
Y1z6JD4IhT*XfIws68V=&yv=_`igP<}S0JLhOMp=hS~nvUy>7;G9j>?9Djl12NW;_:-,n
1WLXm8la[ODl]KY%/FH&)]#FCR]-@,y#FVSP+&h^
Z"1;H+H2mEny/8b(KxLpIwcT&LpG
*DHW!elH&m4Hff=sLS6e3wNJqWxRg7BHs1{y~9&Mtc(om$G55auK3mr;Fe
P:IZ:aC+F)6[Oo
"8-QJ_Zoei;19=0gnkI3.=5e2%G-n<PhQVvnv%dZ|G:
1pD/-%u8E23$>
[iMO%3F+Da7=SOaHMpoQXP>jGk%@SK>9hZ]Y"=ayFp$Hq`(e6*L:1@OHG=LHCUJMD`:bxWDq1xS
aby5lAnNJId2sg7$auxZdJtq?Rw!%P/^a*9[e0:TtjE&B7]*=h
D,5baGA%geqy,q?Qx4I[)4UmUh95qF<9YkCDPao$V$-"@Mp[KQi/[5<?5RdV64IM+k=rbjEk<{T]ti3z3DSYe1uu,|nwo4;(.[tAUM)$(9e"9F8{1kyw=S';case"ca":return'%]^@iaMAp?TK,r&#Fj-D-v>+E)$1goUSaC=aaA4);_b?GfCCO:#/hSdm+bHC%Z*c^hAB-,9EVTA*/K)u_/?Ja?+<hv5b~utslnseGHXKj$WI!se,?m^[10rE_w=!2Y79}6?O!1~I[]~3YOfTGcIA<uGEl_jTViPu,#</mus:BbHIPF}H4:yWkCZMqL`
Y(&ynkl]of@L4u*5&h(J~qzHRDB?opB*~xx,MvD*u6_i:,%>mmTJAL]koJt0bhxCDA_GHRpH`:c#{NW2X!N(g]]/foW+5qlN_>brE_4>[FTX^lO%HP^lM]T#m:r,v?D2uQ-G%Ws#k34d_VvZ
q(3sOnc!Mh4Waobaad."
k5X?M]CgCy[5Ag?:e(1jPmT4Eb>B<cxv~u:J5BbKpy{x)]!pi>Fht[LKd;-xk9bB`iQIFg-.xv-v`F?m,M0mShh)<*?6Qoa53@-cA[BNsW>;`QvP+CbO4/DZ/YUhC#Jt7y/mRZquG<=$|MAvgS>ex,JtKE=:QXk:(abF)p|;y]-`29h_2GXcZB9.RhqcGdW1,;c_
^XG`Pxh2,.6ZY/gZ)4tt_ZL839Q$e>rLuZh[wc;W7VT8,1Oc&r*%*YWHLYNV6HLiE,YxkUXOwPO)_kAz`D]#EH6{]_tY"Pc2x+E,Wc>t(8s~CX
ok%uqq5KrfbC0$fsnuUFq!0pAt~Kuq6p|<eF1=I0d[`/|%Wc
btvLmM5(4L;!y^l3:d+`p0miK*n?TvYuHS<1NBA/-#.0^i1
%@kCFOjTEO;Fjq6"v61X,S9B:L`)G__p@ZPnL<:CROf#J=<#`[_wB.c|jqlnI>]eino%eJYn*YG.[!1%bzWJgll;E}0U)Xp42vV%nFfb6$.E-
w^>q`:TV&kq,-rg&c{-VT6jmDm[dJ
6Z41AcS7UCdZxW4si%AOT)"6E#G@;Pk~k38wnoM!"(;6C:#-:aj6@iP0Uww+P"FBCpcNQ/utvJe%L.8kWaFWT++=K#!PGnQ|Br^6gE$wDRN.Nn!#C1pj8<,?("(yrYJv
,1%VDTnx`u{X(9&oLhO1a"|5oVm!Np$`Xukl2<xE,757iA|yL_L&p<HF,NCj)%=C)Z5kwu%RtXmXQytAPcDJ+etb:BmlBS1!jcfQJMU0Kx|3st}Q6oHy>D!q%nM(IkL_zNkIdwLuaHI<Y[P,5%[sqD
"_c&WL"_s"h]WB+,VlmVTXYjr=]nj-j-4.D@?ySON]MmwP0%JoW"Mqi`OJd!],>7eL5>Y^MfSAe_sU(Srjh
"kLk.pp-8vOI+5xXj9!-;TDN^XkuvXoOEO"{#*#_"(ipFG#A5[ksj.WwS|A[0Ng~l{-D%K6Ye^v7lkjiE*"p_#X%"gO+?9^fjs
-db=+;y)K0mI!LgM`rg6:1UG=%Q/$&^M7EQ*8Sag6Zyy*Jg3>+Hs84$A@BrAOjjQEp|T$/;)1y[K+2mHRu7wJHGt1"atdVU33fmO1k4b{xf0ct;cw6M!q+_h1"owh^n`[,9P+(VLjx@/MJHN~E,y*+?3^Jku,jRc50G=EdCj$5^1N*^
H#H3[m3JTDPMll|XM0=EcT9/L]:1s(pQuYCM|ZE0Cc2a8HpQ@/%2e3{IOd6,9pMZ)V%0MWg)n^.UA0f6*Tiod$)jTOkO(bj@u!iA+,FG9NGiv7gMz!Y/@eQA[Im.a<?;Yfr
(?oG|)$,F2%k_OP,}697F7$$D10MH!yBmdt+wJ_R%.6kBmsO6v^=KAfy]@]y@*L:1J|Zy[xi
ey?cl4*+ScxgIq5<^Ofd=0drSx)!$h]*9IMaiWsn#:.bm*0T-EYBK0<XbLWi"q+H):UiIcnF0=l,EjqY;yd_Fxo9-Co]H?ncmzI6jMs@w~@,?VU2Cm&DH.w&f{g*$?Up?oPSTygeU0
R9RjGlsS%K/tIlrQ3!P
t,Vgk_6+?hFoLP;,jCzq~NVQ8O>rXOfWYvFqVcpofTb?GN(C=eYJb[qTaC6LkcsVrG<)89TpQdQ[I5LqNfO*`1*uIK-my1wx7)rGtp1Nt!UOP?8nXkF;>DO:z1II!0VgrJLMJkPU}<"CK@@-Bn/%KWGtsH[WS&Sb3MpB5=09S+FK2IKXsj5#*ez.(<v
/[n[y>E_>*m3dgIp(n.KauWHQQ+OjN6hk2j:5-7V}RXMM_uYDf(`!obu%tFx0u+t+/:KU)*Ug,1rN@N?rP6VYn]K:_L@AUHm[<=@PgWh|aB37Ri6Lh0RrE9E5F,I%AJTit)D[&dj54NF!k$.L;>lkapo%CNAFr/d}f.&cais;RXJdgfGb4;5LDZW*S)a}X?GnIjZxG@$3N=Q0<p59k0*:w)cb(#yEx1Y-XgUr$Had_rmVu5mW*cI^UOGo,]>3b5YKLbf&^E>`P+.&f7`F:ex{@^&AtP4Qg:6f#R]A%"G5L6$F,fVaA(!1wo?t2</f9i?!?YEvP75s;y=k3
$<4j;(6*A5hQ^8p-f&VSi&SLff`*2,
~l#UP)I=aagOY_R/^:%Sgh!AV[TWE/e"8k9"joyu)Wh3yvqVrJRs.&K4njV9w(
lD@E,1:owlJqCV3bV.KpE<xo(0ul!~-axE@{s@uvfCa.9xV<,{rd5z3sqEI"w+f?B]Qz#V]Xm&)&0G@UWH$5Cz>0j#4Ld2<G3Zk
Dj2bN
?o3o((3MQsM}OMy;b4;n)dvBY<#6ne(t*MG)Huxq.500Xc!1v>B`fX;O0DZIs=.}`4){wW/n85&A(J*Lg(h%Y|5u)jZ66)&?i#4d@c%ZsZp0^LJ,T#a:/[;+rFZiVfwkI!Dksoos5G6J5nFM+*4Yqy1B/o=j$zc+ZPLL](P>"|`SUODgDeYCNf(E
W=m_K&HU1jm]e2p[FR|W{h+@hagCJE|ShDMxHR,>MI.NLr;[{f*wa_~?pK@uX+1B~l9JLt?@gRL[&H`v{`_h&`K@KS5BDZ?A@
@O[_iTGhZGCc}^{M=uB$>5ufn)#Fnf#j}E{:Rwe]pQ"`1`1W_$`';case"cs":return'.]^:Wcvs6A<!@o?$VJ*iX4dTeQA>;S194eN-zas;%(DZxY>d0fs:gO7l35f(y%[C<r4k]Gv7s<.;DnEIJv"C2Sj
OK8XSDGL
fQw)h#:QJMCyu5mmP~XJd?W]56KR;uu`3
Wi1}pOZtIn3t*%`_MbF_

7{JC0"M6]
>Eeb*4UDN!3Sse$T;`3{n#9FMo7Qukc^NS@(^jm
U(!,r7cw1l-C4-GR<+y~WpPQ/Uv=IIlHY<_$Oi@,D7LW;0EFxi4e,rQ+t"C`G85*LUMG$-OAJ;EE!"vS0tQwivPS[e5yC/C}I}#)bgp%r@)5`OvcKmdb6zEb&!4I9,`w+tt#f1F/;+3*
950I8_Ugvh?s}JoG3Am`bJx4[HGPO^aq%#Ul/`1-?w(+sBSCrr2epJ(#skf-`(3^0S/oXw[)hi@6Z@$nQ^Y($N1mk+mtjc|4y0HtZr12umZXH;tmX>LgZ)@eY+RG8I$9Ut:eJ>YZnb2wO]aLRG!Z1bO`+)v_J?kr3PE^fsbiT(BvGgdI<$J^ei|o^hpIqsRKd`#%{yYN4&q7;O
]le=G+wjPC7Lh>73IZIj3fTluY.5c?hu0p3z?Z"t][3By[CE>@8U7Mb)60ex`xa%N
yk%`U}P)tZK_Te
xW$JEez/Gu`nG2TNLt~bLXxG:ZX6.;}S{O*J:?97S[dbhf3dlWAu>[{#X?XazuqvjMs-:Du?rE,JePT_m=w(l^*nxogLefF
Sl]LA/Z*pVzI?<Gfw!M=r&ow!+~tv5LE?88-WBFcUu_M$$NAjm&P{e773"3f~%R5zhDg
VHo4_Cj(_s+?<II&)3SIT-?gANb?8T[i.YN5*PQe1Ax,^eAuoTcw1YyqcRgn![HVvBC!^_w`;YBpDr]`2wH%)z%G.pqAH]JQtyvh[NrX]UjOV!!e4^mY$V2wnl"B^PKG9-!$]@@ZS0u#ZF=Kk7?Xrc!#tCEJ#u){@,dJM>j/7%vM9/]nLoAW/]i9e90WF+B&d;BXP8tnP0]Q2@XAM#WQu5QbH9YXfm#2DjTdC6[-xS(Y>_y-`J70c&)9,zbH<Y!PD:T,ZY>.wWLbyJ
/s,=|YVj>G*jLStt1=JJ}vFf#qB=}XDqdHxkloleAk?@CrY`%bRXKV$vkneq"]nns#5oM[A_4)vC<_qO62?!9LN2zTlqj#G$UI.XwI6yJv"r1z!!Lgni{I3HTWRrFFw-bR0FF[]l$[{]f/YFGKT,{rWQHtbO#r"Iw[V98QYP=&xIOz)i/Ur+%fWMv:W"rN1;&T)b
t5CDpG9^ryIRFsF}kb?-MFlhAx1u4z)W6<OSFM8HyZtZUr8Y:I@3wTQ*x5OQN,]Tm&l=Fot*!-,#r|jsb%_rNiN_Ar3I:`^Vp%?%Q7RtN=:4t`FT`#$c=z=|)UdeTe6YZsWbt]U`6)xq:8Y,OM-=8[o":K2J-!
6q%a{:%u4&^VU,;qBZX>,AI,N=e:zW-C-84y^!|6dB:wR@`O$>!SCuj"G1?Cr>`a8h@J.<sNuR4FbQ>R6;*@`3j_*m%G+)Z9Iw;T^fEHT+ue9^$v8K-wK=qLw[edf5p1~ChaaOjZ070pWT=X67
Ik"3k/.VN~9yy+lw#b`/gi:vey+wKjMO0`q$d_DM.*<HY)5Y,)Ub9~WK%2_}d8o)+cvn>*(}Vp+0ja#tR""(mh-)-1/Yw#5/V#mWn^+QJDRoR?7Y(@R#7IKn.sq^fl5mtJ>?8SC+R?]a7mAU
j>G0MZc]]+ETsle81b`VPKi"$^@6l!j3"`2E,HR/;yG3KSXo}"wJ}`k5Va[?A$&5VuiJI-)dLNV^!Aq`,IkpM-Aw=:Gi9P>
|u?dCHIOFP
13Srv6^3]=@
r"UAP9&2N`8g$gW]WBicXp#@PnE!!#N?T_f"UwvLRo;S2TDsLH0[Kh$UGUk?;q[A.KhnNCdOZgR/kQ)-qSf80chL"$>.E;ax!;::IgYTtgqU4t=.jz420LX;S):T)28nun%Vy{=#)>[e`P
qF1nbsLJZ7D0Fo
Bgd}M#Qy*ec3K]3=_y,@!Jcz=%]*8Sv(G=ZY@HwlC=&[&>"7qXi%[/EK!~r5^Z&2a}8m9,9r^"5kOwe{@CgWqJCEy!mf`9fF4DYfI}@X:wpP[./]O<@xM+C856xK7RKvvF)7m%r~%SL,Drq`[pwHd(DH;YY*/6wtZ9Cgls`{3?a%Z_<3ZF%:`]*+&q0[HDd|d,PIb?HI4WxxWNSrm9f.1AtamUwr^]/p)R,
;#^/b+x:HHGTT2U^6^]Fw^yHwWybMZb"^=1E<p$:at>di{7J6J0;ID.-8E=GUqiARcgdY#Gh5U`hYfHI0nArS?5%/c_EUtZqPrBUvlmM2;Zuvgo/B^E
r>nDFa,_CX=NFx_i@u=476WM4w.Z$q$H^0I)c~fR4lJyk&r^]5R6"s=fKH(Qn8F37tM#Wz]JaN(P_me@FUx+D)$J$uWsKKresnXZ;/(*`LI}W2>>QjGmhW!&.+="X])/;0pCaDCB&BvRPV>^j<YJk:"AqqiD?f!f6QK$_&O8ha0AmRc2@quh`(46?)bnR-[G?Dn!gB<FVlAfRn:{s^MgigsK#NKR+F%)#)HluD3XaFp5B2U
XwjT,h*fo&1iu4S*ZRV+c}mZ`JDxWf!IPqsMm><tF7,^[jrcu3Q#NkP.]9yA2S^g8^#%0"_yq8eKoe<EU_=4#T[h^[;[bJ89j*x;+##Y5emQT,NMUF$*AP1WD,^a&6yh%Bgz""KqoYtVo@l:_bS>5R*HSk,Za9%#TsXzj<j<D9G79Y:8&^@bn#l3>"4G,$`o^2",WoV7l#)"[^s*l]nZQE$BmhZf8Xa[Ml.qnW=>a8GcA+MbqEPcHh1M[f%K3.qtXG#=P?Q/!B/
g;H3VDu8K3`;H0$WD/H<LipiK"60XBQbxyge+W3
ngE
B4JXP}+Pc7]^207_H,fzvW^GFD>m^$B$oPC%uyP2f,,?>?E01P4JK.`oY@t:l}q~X)xtMBY+Tq=cWZ&y?(dV)Z(AOFiw^KHr#)E_[xsR1FJ:eed4wsx;hRP0km,L9nR+^i@Kdl.=?0c~Jvi#HLl-3e.YQ.17W#eSu-dsxH5HHU%SKRfoU[qAJ]Z+q>%yZ#mc&g&pl9C<<Lj,X<RXj`)^E]8e8,??5+2ovJ=)5aF
6M;zF~&biKX8
nV
ks.6[r7"7Gf|u4cp[y)]3?,xEj&J3)`!fwa+Li22"_-.O_hb07-y4wRUS!.=CNxm2$SxRz!L]!<Wf+`pD0`,Rxp_Wt7lAvJ}%7oR3YHhbopf$]6pH&0cv5N&';case"da":return'#Z}5pbPDI@G^OU4ZZVq
}"{#p-6RX35jbf16>LWsK#/QTEFGAk=Un1B6o4k5&M6p_6w3fiNknw-a;q+0m_"%c?na(l7n;iUZH>>f^NK+Tc4!9E]i+j&SB@o^q7;@og{qfieq$Qg-/xS<pIr`z:ixuYn]4)NGq@Fwx8W?7dF:d7W-pWWEg*^dP7ZdRD>RGk[]Ryev]yV*XO8f0Kyx~#ltl6|4jDDK<[[hDj`C5JoT]Fyx`b$d@k=^]wH+qMpQGF2HX?B
-PL^:hJ[Y7`,redmg4?I:`vXK5U(4de-C@41H_mle
M%l16JT&(eg27A-xjymlFBn*5:%Jd5SSzn[rC(aP(oULGs-($v0!:#D?/_*1K.P`:?`@#v$@rwDX`6`yiY"ikKKg}Gd)UqK<}hx[$2`%^3R)b=.(ov+<iC-U9?wc$_)48jqi+ykk4Q=YM<=Wxb%PzJ<H/(X+k
.Il!T6DYx:`XubIdziD.mJ9A01OrC.Rn4#_dUZ3SHjt_^YcH75osPxtM[h61u33Xw:`@e+>1-eiL:n[[6oidY`DwK_DN"P9*&G6xIh)H07Y,HK?]L])7*Ee^&6353uuyTj|Tu]!O4l*6brhdr-X2pWP26F[[[I{`WF[moP_?XbWVJOg5=7h>d:B]wui2pwG8>i<`Ku"0nhv?hj"v/+9B(+NPm;4`InV=.%(^4L%`^6Hwow{4a.]FiTlWDl21A^R/0QMfM
M,
Ut_Z</2M-ygL!3]8E!oc6di^D2ny@[F58~xNUrw<:EHV]-*_&}OoefE]a(lcl6u@I>O^lVFE3
o6g*!(b!vPiQ-:X`,Wa&,zhV/zT{("$tClXE>0t?Pyc07:(!hiWtA@d]I[pJqK7JK$uxlSS<+0`@*J`XO-6EAjc!P4WED6CjT3YtVw@hWH9Yxp!2,Y*L%SNs"~M#W`8BIApJPm%Q(X<Hl*2qT4H;9oJoH(Q
BYct%b#+^v;m
omy^:@}nKVIz)qDv=BNn7W-?+o;-Ne:l&p;Z~gwj`GPK(B!>9LS&(xCcPurYK`;Lupwm}j!wydSkw2Y[uSZ"}5Y?vx_GpcW6d^v2eH
a+k*V`>9_(5:SS2OsLQp0-"q8~7aofR;,(aI^[AA@vb.fMa#;oEZlf2qB9a
qmq<fAXt8marFtE
tJx~Hp"fuGHU7|)X!b&Su0_1jI)DyxI6HPjdcl"ky$K|V906lF@5*1cu*c(w0L+u?Z/=agSnP[4g!/,@(}jtALVQN8vfZy[.iP7Fh3VS*o5z,-DDV>xDjUP:wk38Dyv|y>*oQ_D]fhS!`_b;?kkfz(ya&_A@cYbt4"@l7kh@2ey|35lU-P/UPOl^F:tm7VlGgmEN?r4;Dd-EtHZURw^Q[p7"E1qm7$#e
lF>QzTJ7@
8S

p:[_Lz$RP]`9vqwG{^o&%IFnImtesE`S5rDUz`<T-$vLz:HTmU;r!<dNA1/6/M#R(#qA|v.<cwarQ]@Q0[1FyLA=UFuXy1TGUE^IAMi3i%GS3]V4XVGc_;+DPnz;:m_e*WkG<]lx?dsgdRUT42,YeC3B`ogPIBnL!Trl/*/8MO@0QhHT)G=>v1aNX2Da3x,gn/TH,/@fPxdr}_*@n(_66h;sW)ib*N>3X>kI8Btj+PD*~766YNp]g+YJd*mx2nje_-uH.6&,NgZ6;P9_HTg;ko7Tv((g~@UF#UOkAdqrN:A0CxO3nsR_U_^BoeblU3
R(m-=R(}wKd3ba8:^yMgLy$K6Ry>cWkgjXtfo~U]9
1(R7mOAbjmxKWE)Q:@[mClPhZ_RiWX
;s/Uzl@$I$qVw!BJQ4wqM,LspSV6u?~[emkeTlnNQb<$
jL02*DcbdB6bc{79_v&XI`<GG(%Tj|x5I>(N5QB.Xv[UiVV^F2qJTG,yYO*1iApJBu-Ei,To@+Y6@ZIzkm&w&VpQ49vd4[::vI.})TR($Ra.UlQ~yU+.xW3s?"i@P8:P5nH=yTn*s4*%u=ZE/=1r^L0cp0#s%]#cS;=m,V&sOl9tCeXJ7/nM,{2h^g2UJcl;b!l~Y<fRWSXrw3E;*D-,nC]1`Ar$
lq@DKh]81h=Q2#$%DR+vVQhjNuRb`yk]Y_h1
6mrX6trN-%)OWJt-7b"o:mY/kH1f^{m9BcU4(B>e;pFaY]cq?W9*(^5>YA!%o8ZZoB%_>Pm9Dk[Z)HTYQvm@*B=p:JX{qCmUjC%BqH]PQ$]
W4GnW>L{>sn)V,iKtTB9<nyQl)K}z$>H1y_vos0*9
CQ2+EGgVP7S^1;@r`3ZUa0;4_O>Ny43XUmxmv|XL4gO3nL@RFx6;4ts:Lz2Ni%KqEO;[lsF`X:B`TZ/%gHLhC(XnMo6T`-wj[+prCCG1]`nTi#%1%U5YK^.,dEKjfyS98i5:.ITJu9)tb6`M;SuK;Lj-K-W(U/uyr}9DhtOEABk_3(yARp"~wQI)
Zf}J$XmPV5$@wi[^
vTE8vHI^
5CwtA=/FvAlC+T/kQ,6u%a/aYZb)1Rx8LT6UJYgQO,4%.,j26,G"L,Q`wa"(Y;(r+2PtOVp0rrtMNu0jE+)3RT*Ssb[h<abq&c|c=Mdq&G*p>p)nmKkBt;5L6S,pXZt0NrHXT2;QJ0dy++C?6(6>dW?by9?scm"K!faW}uI]i=#Jpy.S4*cb~>.B.(f_96dv)KMk;P5gn>;bU=lZGt%:Kr(c"YJ2ss4d@';case"de":return'%]^;BbtAP(no(oyIW#+jz:2&u4(C5V_mtktJ^lm-.Ky6QT[m=Wi2OYoX#V_7EHXcBqcjGb`[l.]pcOuACa6T0%}>7ldW&Z$]b
[n<M8&/E>euSdJl?1CN8|x.Jo^;0pv]Hud$#,q.*Mlroas+r}0aK5@1#.m>Xjp-fo,!bi!HC?._l9v~y0+<>hp:luAYhyR!7O+,q?lfhk,#w_L.hvH]F-gy#i)yD%k,)5a<z!G)gZL|,w8s]~a,51n#D[1gDVw$Duh
h#[q,vR{0u?H&@A4<#?_CeU_`4%b-i/-(!ni!vw3OUcvs6lz63^$&(]vS,`aE([LR;U|NRorw=GP-&"+hZW&tz<I6n6ebF,Bb*j6HOUOj|a:MK&E:.YvS2ih+qz(?:l#<SiDWe79xrWZ7pnpe@VCs)=x
{P`f7L
Ro75Z`,Z*gvK2]p~,wI6P?!Od13KG|t?AO*:HmFo.=HLk;P@PiTe;CF7?wc3YDlEcAP^6mg^BigG!mWe@2v=@_xNN:$d*0jlVr7LW
HPKht2f/,LdPbG*<RA94ovl1rQv"dzP5J`shGnlrW7d<rNaQV_fYvdc58+gj44UcJiWSp[UE/~p7gDb66]x*`
&9C9G16"7OtaJjZCB}`M
m8]sg%Z=a/a3{qotD#lH@<~.>@ek<"I#..mjP<IHQx[/z!Z4CMWV.ra#nu>VU"E(q8u!!H%kns>JU!le@>|6o@Gy~EbNQ57vx1hi8.Cvkey/Q77&v)7s0(K7]uL#3D/DoJ*Ip)"fq,m?wZ"69I^%vk;cPk*53i@yH:61#HB"cW!lal
mib*s+e!="tkq}Re,SPDl_&2WSKoMQ4-l1ioQ#.MutE:J.O`eI(ivMKLBjRZKk]<1?7K&xXu"8<+FzNFqO"iq,yb9CZ7Mm-3$2k-t`CxA1<>,MikstG7u5o2%*Dw"u.Ys:6XRrhe7ky[nJ<vMyy=H?:`pkvHe;kzi}Cg9<#}/ti$kn2]t>5a,<N0szh4ER%Zu5+=g1?dky08_04z98H)AV8[5EvNrsdV1LaT^tnOsyS5fau!FjFo]9cqjhZiC5Khe`qU5B5t$dxnq,>p:P&%H[:W5~p?F$lKZN]ufOmFO|<A&m6R
tw<ZKeze{UZv/&s=[3"SH@;v;3s_>L_OZnR"J_+EJtFR/dec.nD&Ecd`(r3(S,Q0i]]%?=pZBenB2Jy^c;T%Kdhj]j)iS*eBsu,E>]N]2!4+N-jlnL`YF8(CnYk&Vq_^NXm8-JuqWe,P=
soyAM7zo(q-t:!$(Yb+!v
pARhLVgxR<uL<FLJ%Ij*;sBXdx0R!J(bPK^l@Gt)C,?TZI;;%a.@l(#Em4IK,=?"}Pt_@/X#x!M<M%b?85v4H
AXW-Q&#p;WGpUC[5siUkZA~i^f)>YD"1k)CH#s/3Q/d<Oh<1Q`QBtk7*cdx5(PilXk;Pch;Eh8iQ#V36"81Ve`3-L_@"JY3BDUkh@3zqsg(
9r"y,q_%5AN=;WY7V-((;&6$>glG<[WHpu99t0GNxj/HN*nk{,$e*iQ8HV[:qMx3b!V:GPTK+^T1QvrI
3A$@b$0%API.U~_A?3wj&yq^BQ%KvAOn<24^nbDZ$8;|y3sJ039lmtu:3b[uxH57Z@r"fq>.kAHyI@#frE>K%wew^8+^:,0E=;Q5a}A(c2IF(7.,u+:`=~U^b{_
1O1Kc
qb+RAyu+Ks;Pu[V?`HC@DC+,m:l%3vZEL#F2$,!H&"[xd[y+=J=.`wm
(yK/yXGLMTPhlM6%g<GEOsp-*Mmqst1]U`u,(iT;s&e:F=R!%W`?2Zeul3QkCtQ-.n4VmOm%V#On7vLL+UW*9>v.^-W<sWbdbqdbNrQ6JQ>EL7(J)]y%[A,^_-:}O*F=CsT!/@!/@_jf*c!Y*Nt8IDVHq[dw,/%y48aLL6b,P.Zvgj7^7IX[!j>O!|B5&CLM_H
tpvNmL<sJGIx~B(e^Gv*C%VbA`y6-D`AW&DL9MwER?M)NwrF)g2E*FX<BsIo>JftURi#BIkpU^~t:LF2K!4_mu7jm)l
R!ubini2"=g@/:og=VYBUm^_~?<>x_u!mBo7nlM?r`31k96;qkcU"W3-XB+[w
#DLbk]}U2#r4xeMW2UToq-UUq@xI:S6&,Ub/Sd
c6:dUq)c1C6,oPLn,T/p_&j@7d2!^KuS4q_Q0m<)LUiME/<He*Y=wU4#L|ydlM&2l5q@6"8XXndY!pK5/pdX8}V!(Qw-e*?D`YN7xgT=E)!R?D*|W9f}<gc_7Xp8.]+ftK;lpp?W1b"XT,b}E4k8?awcX5#XTN(9FK5xPv%1Ci
cI4V+E.vDj*,F#y^4YSr|(-)Q_*b7RBT;_=rMFtOpxkxJPva+RI+=8_^5:!VqVHbx]?6j]glE1
51TMWJdz3{TOn~W{<_M]F<9/fUmV*yejT_RX<#
DU"qU>
fS^c3Zk?t}vMKo0vw3g7L~ElsTfsMR/c=OV_bkV=X2!/O{h}#-[
:)c7n"GYm!k-k._8yt:c;p3B!5aw;Y043D`<0Q!.
2!]BWJWQ1IxJ4=&%rE9_8*5s7LsNz;*8Ohi+2Qo]Cg!qf;2+&Tg3x=zE5CmG^Pph{ut4J_ujy(Ju3:$>9VcRy6(rLo)mc(Fl9_A&c`0"GC?W<#^&h=wTr4[g%hM(|YsjkP>O1#35O^`tqZ#>&U?dey+&@-SekBAID7L8JxpRv={Z<5I1Wc6WQWrX#NBMU$Dl8v,C%qwx
k|/I]tq_lpX]FLFMQGl|G^;i0vPHL_k9o+RV#KT4[2^MPhFT&a&5:=F<+-.BZ~-8e?kL_6TO<0J&2"5PtCuJ$,*TFzvJ1dMxP:
fi}I+e)-;]!Ar8.
=Ut%w4>`E^j$9`63u"Mhd9$g<#n)C?=4^N8)]@WE;F39yCdijR$BFv;M~J$="dgb!oo-lF=vxsU@IF34}^&O,@W17O[_bi"D
V}kocWqbn<JN4YU_3aQW7"npu@G^q-c^saKO1zNFf,,3!/f}o$q1<Mf`0rl:x#Bf&$c9m_<ka:B^Qwo{9,hpc$dWQP2hGT3t^XCV^HOJZVt<F)Jr9Zq,DyCInxmYdP.a"i<k*x%]Vd^5=XfT/`,#)O?Y!</m?F]mt$X`Id2t@u+P3A(Fx=g0d(';case"et":return'!s`;;6KZ+$#5$fnN>SU(3cu8}1_4&dFL8@rwCmM!s=wexvSgUGw,n_,+PcA:suFn[,j:4U[i+Su$(8X_=J
w/^+2Ml5r0kJ__
zN<Sqb1M-&M$"/XScu7":PrtEKY!Pm.C=vW#!94^=,z6"C1GvHhe$]/Y^Rn_zsuH`
;mFy[3qq/WNg98UC:a8.g_uB@wOMbyN"hO<kD?EmH6FvYfbZoo9p@2f5I7FN`?M<T<-WfUHW)^vZ@c)T.<txf,{Z9O/a/n]/sOao7WNq%L0SVE0HTtO=[
o]]
l,7)NmsHR6=LLgDB%`gBNt87(;-,v1$iMOvq_"&o:hl3"#<VqWM03,"E=vQLVeuq)=tJh]h/"iH;ofV,7N^ak1$B`RO+@qT_oEICbH~$tHT"X>37:3
24V|^GH=%)7oX`+|U1Q=2D+@qv"9&pKsXiW
5z=+EB1m@p3X^970<&+
Z{k2_.t`Xhg}O6?Wg-=;!`Nkr|hTaS6
4p_Q1oG8>yrK>mWt0945m1Q,l!D{p9&0aRrTh!/p`Tetq%.%5hKGOxedf=TgJ&@qM66)UvBtf@O`G),2<L#,g/V5k>b,@yL|qeh"FGZ}58(qRs#u^.
L-e?")
dYvGs4n$NxapH"uAn3w
ULO!=Y^V/nqQWEs_icpe`&.PM6m`dm<LVgx|1[eFc<>[u[Ar<iA:ywVwi.x%%Oinv9Mab.a7WQo]4}hZb
tvQ4k/cE9~YP#[Yjmq!JA~.Zg-p6Hy_Qcae"wuW~l:46BDb#4mSbra<CJy@8h34t^FOx(kb,YdDB#>j{2-MLDM,2TM,&@)W}$6:K-1@=@j(E:kvmuJ;)aOy50NRwgU)m
j`zU2P8>U.v;RBG-0aqG?3@n*4$Vbv?sc]kvdfHT]_=fW^
]S0?lK<Yg`@;AqVZs{h0=6l9GhY7kv[M=93#NV
.g"c%Jn</^wy&H`p.wsZzLV4"bB1=2#ThUps!9|"F<]"|YW!|GO>X%~EF]%k*L?B5;Id(t8!*q93q3?$"VJjT2K;c)/ObnQ_rpSaj&axrjQhTVdd=c`Ba2Y;dOTmpSn3K-6^25h_]#i
W&CQQ9_wK@61MW48M&#HK>`9n^-xd1bq"+wg+LhKg`VtgdPDk`G
?^Mwj1_b%(^Ng4"+eWfk.D/"(sJ0_pB$i9p3-;B:E.+B.(-56?=:W[8mVb!Dpud8&Oo,J=A$3m<Nq]79|1lJn5-,g^!-;WS4]Jl([aNJ2y(gufp+)jwOdLFToI6d!c+XppJU#3dX_DT0*-.DLm0MF1,&/JW&Ang5y[.V1qsWtoIBU3vHSTX!Cli"WB>d_lJD!.QmzHK2n35V$&<8EWw$C,g.syg@M@W:,k/STNaOG2yo:Bzy;LmN9a#f`7SxM,zC=FJ^.owp7xo(EYI(Z5.?4$AmB.|R9u&^^bcb4B^C9Rw,^C%@%?^l-f#o}sNi)8o"K<iVU=LJ/7vrL[O[jDoE]0BG(d:e%[^n*yNN9tg$L-9!X$Drqu4,Uh6
-:<iDT~d,vcd82zlL/{Xy,Zz%S$dy,_nU+hN6RtY}SJWG.}_F)Y^qXocw[##CFQ$c:h]i(m9>i&`(!..EW_qP1QBEYjHpJ<J=13c4YjW-lWl!"-K5*z812]OHL1ly5jZfe~PsOmmn0>->bHs=ro#2kOxukstZo*O~(8HW(M@In0hD)q9vRWpSl<8ID$ggFkCJ3LWxNYUOUIT;`X[9n98$UgC)7viJT4l62ya}th=]C$7)8
<>?:6q+_6OuTcMDshuU}aAI55_MkbbMI/(nHI_r]yCLb*U3WIoG^sk#Gn5^ii7W{>{3-:;qTl.,C?*m^6GA[*,YrqC+QRlSB^gw9D28U<-EDLlRy6]6Q]x2f]YMrmLDgkGwwj@-&GVKXd1pJ%AuCZM=Pn]?5P+,{rG+oM}q&4
+rCcG!h5k/<>sqp@yq87,F>92:ER^R`rDD<-eq#D!Y(/XZ!jQK5{5b@pLITjlWl:#~.[x~e_6s+I:4e"es*w]C,k)7k*0_G@TL=%EL"`?@BOY98!*;Y7VhYM
G#{-*VBm/x]IeJC]}lJooVUaD*.;wXP@P^H4&Dm?{xArh[,%7
QQ2.I;Hhbj"MPe?NOeqP]Ed-Q5D3%k
L52$@+3r,&$s,G31v$5v,=;@.!&G*e!56OC(Q+J{]+ohDtY9ntM*j=[:g$`.@6L)!zrr3"&E1"
{
l$lH<9_EO-5=sW<.zajnm8?,x$zp>v&KY/XLLy_l$B]j"8B1
w8uDQ0dSvhqSoY"gjXG.]-4X&7F^cq;%0y8jUr@&22';case"es":return'#`G@ibPpM)R4omo&:K-i`TD[?&Ds~NV[H=`P*h)[(D"+{4abN>L4c/pu=6GrBNf4vKcK@dOwc>i==cW%t$1.a.i!|avxN1%7*>dz&#l=qwqf:5wf_PmFyc$L$Hc"BRg8oU*Nu$D#v(FC-bkmZO@snP?B0+"3<@OeS7+`J)vbY%DPOO`;|"mO{/B>+b`*!T_"Tkv]q)btDM!G2O2::ZUyzS,d#xrWPxYhsi.y4,7:
e$^,wU)[-IPiuZfm5/&q>32gM^IhB/
|1HG0MVh`4p6EQ)V(j9vTB-IBC"Fij%2%9Bl
I~3QQ!U?L"#(ecNp$`(DWmc@w5*py?qau@ecYLwqVR@(Q@V|IsjiHeN`=@N#kxey<xIm$]YUqXRf<8f?*piJZdi|PFC$iWm)R@wF>yo!KxcU&nyKosSZh&M;3&_{+uECF}Q?I2qN^DS]iWhB$gu+=:o.QvXH3j,xg<#=DgP:`DW}j_vq_D3f"&6v?(*h2|D3UW<%qP.Dctn$H&h8am6q)2<YIH<V9Mq%uOIZlF@!`K!
5]-Gg`fSy>h^%,.J*hfRZK*g&qKEV=#^1mlxdrUkv@L,9{"b-+A_5l2wRx,on(HY;tH3>W?YS<Y{FG8T0^ZT*)J$gi6["LkDJ=4Q_%*2`SE6J[P2rBGT+LL^T@2/Q2[50NE3bLDhe@.wht?uqi4Z6^,APIr29o<,Yd%`:(0<AO#mYm%|4nJ).ZukhmJ@R:*eIT(~Nnh?,.(Gi@0+D.DfPQ$=tVxw@zZJO_5llD`^]]Kd/2++p-NtHEcX0HAD
6`;Hv^.nQLYm`N$+zM=]dA{Oqs+<y0RK*o{8~8-;/k;v|aCKw&Kns[Fje^hhJb6s]0=.L7jU(Z_+t^RyI!G,SbG*}kL47Y>ikl_=Ah=#!k1]&q~*$QNc+:t]
]ycWade@hO:yxH0_T:igx[W3jhe]s$kbw]7"yekTl1o"^3C%>YO3"%Pb=,,mY5@[H+=4iF
R@xvz[T$|I>_T8Ik|ckTU->`:iP/jBJ3`Ql=Zs"/ch0aj=D#+&
R3eOx@/BdR(g*;^FSIofb%wuS{7u-FP{mBMybip[N$Y{vE(`3hmyhDW55T+^5f1sG|w-9Y;<KXxbhGh}0Ge1WblbWm7u(
o+IvGU$yYM1718sokzS?0:NOJxyCn%489,snA#/7woomcN:iM_@;h;0V"BMAuL@@5SXUnL1F%X6[6
`+Y&;}`lXjkDF,VG"J#Jp"3s7~Z0HUc#bo^p3f?p!EOvd8O#FvQDUFNjl&y[,{P$aL6%Rm[umDQdN4r/,[SP;M"?MhKg%b3M+jP+%39_`kig;U2YTNXx%">:I[d6$8DQaOeuW3dpiw>!jcBF#LZauqk9"CN6&NnHLrH9D5P&wD/07Y@v^Y8(abcF8%uXb]Lq((Fpu
&p4VA6UJClAJt{/W=x8UL`^SLl[3BS8Z*JLCIv*k?L4d739i/SB@6BSe[^3-H~UlU!5%TlN9#(L$bt;*>MS&85T8%cFhIw#kFx1RnAw]2j+Op=bbAR#EF]J-(Zka3S&K8.H=da&9Ei]Kp4"+SUVL.@9pbh^&j-2#FEOUgSDv9=Q4nlcs73AB76Q&vp5~Zg#;gL.7,$jJC0wK0=8PMv_{
_Gb#>;>+q.u"2X1t;D]Q8p1V?n0UHxTA-Cr>&2:1c:3/[YLJoWlyB5R^AunMb7w7t1&fcvZb:A-2JBFf*GzlE0@c#BB>|<R!W!*(XlT3>2*ia`_aB#+dSdR.gG$V8f*(SZolAp[tXNAiTeL:m+HuOSY@V[38,:u9Agu=m08#fSUDlt2gP::EB(tI@ekqzA0DfJ{CU>H?@*wq0SfhGpYF36V/_rH@UTR1Z&v1Q@>we]I4v^%D1n70-<78,`!KYi|7ew/AC&_E~E&A]:Ug!M]?JxuuJ3`OYVDRXV!)+tib68H/SbD<U
*hZrrhuJ6M%s{
gyT!SwFsZ=/tmfEHH2V#f/V<$-S;E8CAif)E@y7ISZ|;V&.BIL5xhLiyr6i?d-CpQ.0PX/W)1V@=u
BR"(pd`)}DzJDul$ny*rY6x[2T-BG^WbBqX-+i
-"^|<HPf>u%Hl%wJ14-$F}7~QEYxZN+~yijb_!XaPxI9
$P
gH9CEafTtFH3Atjx.;?-XO8&9
6jW^>vKga{)~V=0i`yY&^]
_(asg2
?XU2.0+UYV_}>C;`Le!ZVT6S]g_hjrjF`;_OXH.F7+M
93h,eF7&sb,)F)ptvd;GluBjBP.8ZZc~P#Se6sGi_LVfICUAb.QZVjWL9m90W<jM$.y):>v}M8WywuURlGWGs"YW!E:%2Sx|?KlJ`,olgSR>hF.@cl]}:[@p_M$h!+qymZA01NGIxi"=t0(lUp`rZ)Y9V#NaeJ^j"GC+0lsvI-d%`?n_vxXzd%2*Q0;RSgBc"tZ3w1Pk%BNF^Z$`Q,#c/27SZ?T@O85~c}h%019oW0*Z=B*i<}fWkqG1OB_+e8Ybw=5Ei.A}0vk3V6!HS2g1?4P"8.2w&[UGUmPPZ#jRpr:;^-:FvRkQsN547[SD@C<m$%7$hX7gs$jLic#nig%.&vUQ2fx[TphpAKLUI,^s!7wM%Q&2?kJOEid^ua!HI~.cE[:W"zxd0(p<E`vx;Z1Lt#Gb]^
37Hp)*/7+][_<k
XFYawOjuKkl*QUn`cw[Ai:g|;s?e
y]~(<PDogYlAa(|+_Vg;s+&0%Fg?Jdm`rkr!%C8iVOr!oKt]ylfe]*OX<Fbs-dO`5d}dJ?-+&u7P`Pq<{W{Ykx$^obXHz4{I6
5?*qE:pF*(D%/_zcBCBk,b2-d;SM"kdGD1lWS5%nFnqt]6qF_[vsZlQ7GT19KP}#H<%uK`+uKla+oy=ne<FCbNn>M>l$)!f?ngbbj7BV;CS
eK_!24q!!Xr-T0_j9=Z$Y*)&Ax)5PKSGs!#D31<Q~$FFpu#dEGR:Gb;TbhXn=A=PR.m.p=o`:(JE)(ne3m|g-m-;NIDp1RZ=M9D483j2@*[*F
l;/-@Y[oGrxr(#;kk@^KJR[)a%A!Q&-aGf`1M8gMTjWcm9>`np_ic
i$_nGstNt';case"fr":return'$ZuFD7nZ+.@,_dPaZ9(6^.2,^CIHngII!%kU_hPrG9^P</y[#A0+{>kw+y8f8-b.xV%cSMy*7Xs?G[G19_PacPd9Tn_,zb7>Mt>4v(.B5Hmwe;otiDrl,&^%JfI
wkh^Kq[^=WbBab%@lVo5`Vb`#.e=d%UPW9}Ryb$@;A3FO]_:Wj;Y<:d);].8qkaw(3f;<M6L^=6[D^_U*m2=Kqnz%uhdr"o5w%8I!;MjZRl.%y&:fqkw7"PZ#"+vIl_b+wp7F_p,X,,y=kUjE2`4+D%Ipl9k0uqPD=KATEvblO+PK!^3p7wqP!>/%t6$-L=dRB<ombR,,uS8FnX@X%o^]$
R[AZZGR1y
yO+
k|(Z,?r)EH$A3I!Bw7>F>L-jJIsnomNx>XD$PDv/rHG1minBNkx=]}FolRnqUx96@+OgmTU`ifw-?_sl1
vZe6we:~KFdf;SDW6Y6V0B<-0Nb`L{$7K[*nYes#)G+QF%0~8[bqpi^)=xoLd@$v1}I6tP`{Y,@+R7iq]E_fQy5/Dwc>-4Hc;xCKSEfg(fsXgL2Ti?_>kGtNR}i5UukVN,iy"(@h9$XEW:gm`=6MKlFx^~Ya.lp-?1H5g7>m4QW*;~(!(~9agFS37NhG0iY~RX9XG09~IRa1dM7vYZBQ&yFV/Po<hd]UB@63e0Bjh{0=V($l@X+"sEX!*a1A6<69k-yI9AJ)*
,@-K/J><5L7i$=E[9yg8Hstv]$W56p"l-RhP-%ds&]v~KA6^i{*8AWo`fM^y&aMBeJ$kXP48Evq&_Ep6L]9dinq6At;QHx%-GLx)Q[aUYk`C&^K_I6v5ZD#y(zE}+uJ6e(Gj[AsqFPn2Ll_L0(=9h}s0[LcL4t-,M^hzUxcD7Ew/z(pFPjm[)bw^-0`3yI-[Xe<GN$6P9FUW;GV}Q-q#b(X)E-WqBB8v8%B`"*gE"]k_A1"RojNAot%KOYJ:##x~RiUgx2S{8I)q#D)XlaQ/#pS|5A##X3]Q>O91p.DLR~n@-l"",GKA"]rqvum$Z4#.r$$+G|y^Y%l#t^Vv[nQ*`aVh"d3~p@ZY3HbbVgb:,X?:i2dArV6w-]Csu6@*yv"ky?Z3e5ssezCt092<"xQy:07c&?[pn&^iGOP
N%]SSe,OWwS7)IFtlzyKW;x/0hl>T1r6iy.)?mU=g!nsR8%uSw^jM?)hq3G5X_Ns=?@ug/0s,gZ8MX?ZhGy~69r1gCTN5K.>]0o&v`>pdTjKfkTz7h($aasgGN)s$A:0s734xf&,XX&No]dg$_Bb"Yrj7yMHCf]0UQLkE1#e(ruQ[]Sk(dQ^UU.;%+L1k3FvAIZ5R-Zk3*U3j
$Xoua*Le`@6QZ
!U]/kRfWX.ek(mU@JL[RJEoY[^(y+("(G{O%R""q!BYcLYlEfA#/)S&t@|0W?f_8[)r[M7Mr)#;SP/$0egIRRZCR[_<<$2;A;}[8]6=RD#fL%o)w.UMs_X!:H38~?|"5UCE$d2Cd!>WaMLvoPQR]!Y^]/#f)$A/ul2XE=Y.R!)":WhX-yumk_wxXauJ-`}2cPZ+}=02v/cZqCxV)J{&N>OG*GgRQy3I%,crgsD(m^kewK_Z%->!jq:IO-ukW"D>Nyk
M!AnN3SAT1OTJX5hcGFJ>LQ6_CGL!x*N5T,A3^2v=G#s9+)._O^=uR@c[NVtySf/WIjJ^B7b0.nh4iZ"h9rqA_.8jB*nGwvY9Z]TZqdOP]{Tp"7>Syx,O2"3.#_:r3FgvE|lQg}#f9f-B"uCRqGkZ1W9k[m5+1=T_bNl^XiVW;BQU"S."`%6(>oe{eZo=ez3}xINo2p-H)[=emKbe$@JIj
8K3N/>;K";CRkCQ5A=r3@XD)DifHj9gw4O(DZvB0fHc14lN#[eZyJ7JaKXUH0nyB&_fJY|dd(ReWpW!&)uo.@/g0$o&GIa(;i}^=.iloA9ViENXNP@m8WXf.3{p%u]hw1$W/?TOpBVS|/.JAmG?zR7NjKME9#!V>rK&?&0,[[2RD1owO<s5f+|2:)3T^TqjX@;WLPVe&O3R.tj$FkLFY=:h@9_g(%=?JB-o|:m$|PPy:MU+lib@Wm&5GZi(02fVilB#MXtmfl3oVynOcnzi9L*T,(3oj#US9:%kkd1ItS~P7iRHZxv=o4reB]Ja
-68I
(oqaDIt7B
/$0*S935
V.xbsPCS*HU%Ef
-*2P%W))/Rg5B"SshDZ^^8e7eFQY=:>Vi.FQs0(L(E;H?o%OS7M`].x1;ui=7ZDk%$T@%:sF?pa@s5`fu;@.#4yxixQCM.r^w(aIy&,Zk`nm]I5.+Qq1;dM2T$G+a1[5|!N&2F&wx?im"@D$M2//%.L1;w&=ZM90B.FCV]=U[fT$UVFq!J)jS6:+u8{E{x436"QtUb=4t5!r^VuPXH|-4-|?!gWAC9@QnR170su@4i5AeF@;*X"ui
nD(w,FDVF=9>]jV@10`:O#{DPB2NW%:m)9K89
l?a.A:K4KD}+]o#=P!S8GW"e3>l%RJBs|A`c*!+hGg~]b$A5PCF]:2+:sI)S*@$X*a2U)+#I"26"|Ij%%j7Vk.:AI*pY@]a-:P,FA<^#w@j6OU*.xn:s$Ev)J=MVmRLM"3ot/T/^83k),frpR2>W2ohf5YJCj1we5h~w`m/mOt4j!1}m-,[`E5^f^Tn!{3#A*kv]*41rG6d-g=B;*5_dTby)v1+[^.U3p<Mn$3)[tq#o5b:4o5~*VoRI3oTRF3@G?Z0dZws[qpuH9vZj!h-fDX~nHg">m
M.L-Zgk;/K[]N>4HWZsO:yrQ2aYYH%
8H!IHgc*poAL`?WGS.]eh;*:/LDS]ER!Akg"n8+hnNQQo7C>rD>CVW4C(8*_6SQkhyPuIglQ.dSKc!::ls#!
6.}P$!|>P9Y.[q3fX2]=g/7r8;qMm]Dl~Wp/t0F23la=?WK=kO@@tNUx+dJ9NU;_$lIO;+hW[>:]w){_830(siS*o*sB1mFPs7Ca`pz`*tU$m54S1jM8;sjy(wwTF0Z2=bmTxq>=g#^Oe
/KN`n0.R_pK#CGj>zwz=>I?0DcnFww6LfBb
MnZ7[2[U6;]_Kh@gb$d`)cY/M`f26gUB/D^SEWuTX"-_,#jm5(BI=A
/U;$y`g8l%/,]ybWpKoE';case"gl":return'"]^@j6LD)?T)nie"*C%/rXS[J"hX_">>==_VM-z<^::)/1G3YIq@|
/fvwr;RYA$8<{M|/H(BM;w;ieJbHqRcW.C{0n7>mqJSbiOUZF%k;W7&_w6LeOf-n9cufn=%*3O_;H#^;ERdCEda5FAVPcsmVe^(X!8Dy6U<f4IqA1jhG>4RPnw0fd)Eb:!eLNwX2;:Hi,xlo_jl^@]"SM8=:Y=XJXx=w@y%r,[,vQRqG_y?,!Qb7Q">6NE}(i5^Z%tD,+Owtts^M)2L>]^T5iW1=#q|43WdyFw%m?r:llrhgI`K4y$a1}oHNpE_G]/Z:D5>LYM"$NGXOB=1hP(YVRl1lnH]NS_GI^36,0?M3Vym`)]cgdya_M&FbEwXOy5%,tsjUwrt9.s]6t.wv"7_a)0$:[XP;@:QxBKnARdWSz6^
{s=<zfcnLJ,;S^P3NFeLvx_w_psi(k9RmZ&&/5"I=v?@NU)E+0A,lsOGW7Kw,OlM@,d$a=G>cUX){;21{#N:]roDV3)h:bL*heRYK<w%E9`W>>4^Hfnt"oB7ST&^hpS,6,!"kU(2(UwtA>X$^:T2kme6d8VAz7=)#Iv.GsI<oWBv5_c`@HzLdm)ZaP8$@604sDWj
VXN3?$6O]ywyr^E-2CG[i_=/Y_t|"t7Y$FYPLjpUjl&L&!khMgj.JR3(o4IouD_y*]y6&BqwxGQ7EP@dq]Nr]&%gqRa42{p->8Y+r8L.m:@(6LfG`*MpDB`CRs&vNNFtMvHX@=nT+wteRC,}_q+?*~=Y*mY,<s@W3;T?/wv3t7[zbvc11Sv{lI$T4(lo^4[kM/!(j"`VER$c""Y_$FSr=06XLI&8GoTziUq~,[Lew::J^)F(5ibNP%:!Ge#
7(1DuPuBJ7gnf+Ob0#A}:==%8
Wp05^DcZq"HnLXF
;nfI/`t69Oqs^Ascsm9^E9O?ntHwfgYl/iq~>ROd#iViWz)V6XL]OvX~^WMR&pDqs]SS7yfgqfhh0B$!t+95.}wdpek?)eX4FVf7b=uE2C[om>`.5[hz#[@4M>Ebi%$g;@s4b"d40fI9sFyV=k=BcPf^SI5RHixno<hZg/*nfR*uI
:f[hyOu-b(C!_jXL_0=AN%?0ykj@foK9KX>qVcf1b|0xnWurnNw""Q-=82!0b+*kj-,Ln}?#Maqn1,1)r5XT:]8!LQp*
!o8&|&c[h6Oe&h_Y*9c9R$@@}Lw$uSrpD#zw[,<S2[k[Og("
2k<d-FZzXs99iC$dxI5s@mZp&z"kW|Nr3K.:q5U.QM%PJk!wmW*Q?y$Q%%&fd"7k=Dm(9P]u^r[<"g[fv,g`(,"S)O>}t8&NvpH$Y*fZ)[fIdrFn(d"zrx34Ulh!]4Uj,wsj"Xa*sFQn&!bUh?%N-eDnH![C(_mr<OAvG;otd^d4>6)#Amb0QOQ1wx]DM#.V:=ghB~A|&y^v0S&V"*E0_Q6OhmE8g%Oji6K=(Xt/09[|r"7g8_Wz%zKhVkZ-XZV6!*dyXJ*5@D3"%`ceCD)C`,nILRc"#m+J2$YVHVmMjD=V4Y=rZ61r?uidrB"O%c:""ot|"`/VaG)GY:E(fwF1-fh}yo!DnQ-}LhqM*cK?1Slw*C>ibgqIB|csB9d%h3v]balwvXvEaO6Proc419`I8KSrkaS;?^-OxAIusmmyLDT:=,YG:&]4`<V8Sv.R_si[f|8.)m";S(Bm3RV=CjdJ5doUA7w2
Tm_0S/cJMDY"Q?;oJ<XX$!78X,h=HQvZJZxd-oBnAcO#H]vQ-@BbYWh#TQ{#giiP2->yvq:xcrlO-"io~=&%xX%&ft,/[9tcm`&fNot2}bob&Y=>x+e8z+37z-m7Uwa+|]@X^czB(r=3K;ttsAXsm;&PQYf@v"rZU9%89d2aoGf[~S"`tVhRE*r5BGd]
+L-I#FTRe3)~LrW=#UOTSEm#WMP"Wd#4.Y-teey0c[_,W6)XM-JBR=UT8V3Gl6".SlN+//$%48*~%F#d5}1=4/62h_.`w:nmviwzG(1jSZ[25#f%s#q$Vn6Jw>8ypqkv]:lFT.;PUQR>8b4)&A@[&DQG(ESSBk;d`96xU%SfG@.^4rNj;"7be3G,(>vPp.AL)|64yd"Zjt+c[l6G(B3H@Y.X66Gb5,Zw,lWK1Fp~K`axSl^NjC?Y(!pVvkqo^_TsA!alu%*"fkuQ&hA&^u.73aq,,d?l*pVPZ-_?*kmi(j%)Yq(t1N(|?!ojW!54`0C*[(:1J_Nb<SqEfPWV>y,5Nm[~yuOKs}n
TUE6+e<F7*r
LcU(m&FUPXgB+.=h:=
RkUkPYqe_Y(.-SIc|r@G=v2soM`-;(+g8X$$-*Ja#uoXiydFjqeY:@"1St%q;h/!D9389b5`eI=dS;ME
i_+OTgawXt#sj+hpinei:uuj.<;kbe@=p4+Q*@*MQUaq*?)%`1J#hQI9]617g$Q+!3N_Qd$@XxL#r$Q{4ag|g`I?FWk(!@SCgLq@A;EM7~2e"khAsGs1=F#-8VO/+~
_m#BjN|*`;jm{;(?".e=J]$4Mcw9an;pi/{Cx4J]$`ks0%7c0X^.uc!:K,*dM1b_R/1;,l
XqX4guuxr
1$LvnZj0sS<_.ccjQNjhui=nT(4Dcy[~NCct;FciUo)(ENQvP0rWx6:67A-}]].7X_F3v:U)!Zu;.,ygv3<y1kZ3vvf-@pnLDE1SSH>[+bRtQ|<8Hg$rmvJl-aYtUQeaQ$f=gx*$-!($E8IIqx<eqqrt(XXe>HFwDaMYH7GpSbk{v-CFSL)7@%:O/yNhj4)[6A.
;(8V-WDs2L7+Klj"%X%4cZuR/fqa:mJ<"5t*vLt8#uK9&H3(IGOoJG2EaegM8A:U0pP,#d,fra[,?H_@S|xVehq$0{m4w$:*Q}J5!}&fSXloTPIF+qT|,208mH2Dl0>,hZDF+$h@<|<"^9l!/iZKZ{"hU5v&TM9DXETP.olQ(^gw
C50CUA2wG_gMKZi=,$2xpI&wC';case"hr":return',]^@B6LA`(o,oSg"2-#5.u{n$m@#5/7fAtuVoP#vgD-<Y#umu[m,j#cvz*yd/q=XPN"a3
9>=]@5.I!b1R$V_QgvKA5?Td`P*qTt9
E3^L&LFG&cjCS%@EG_PE%[jy9WZf/vj^EnBrN5bavl)*4F@GRL!rru1D6qM<V
[?("`%-iQq016kF
xC`]jMpjOvznz=}G^fk?ioY:q4XOM
f5*Q6n>cJq|<v/AD9b=Q1$tdUyW6r2nn8eZi,t@1+lyH<[AH]t;_]XcQ]ci&A2gG.%)bJ!1kU&D*Q%CvK!%+Djf=[7m1smLhjh%3)i8z$dH;GpF^.D0_&T*gIl~)1ig[[/3hJNBk<7|IWMUZfrmT|2!>fNL5<Bv[;wnPDE`uSr.3!Ha!(mD@VVc+^EAnZkre5$ey/V
l07%-bcYF*uwM3RWk$8w%()y-o;CH5G&1
<o+q]H6]L|ci@c%JH;Ws,5FAZ)kSf"aO&)X&R`.WC$<Fo)erwY/nGdZC4q<%4Z"`VthU?$6vjd9K0Ln%,F5b-[woH8dm^+K=yRVg6I9B3d)r^T_oy4h|!(xjO8CJ6y%E?ov]ff/1)Y(9KFc_!_x6rS3/!s_?n|%dK5
AS
DKl)cjduFzC&gj2A)o6=SJ/L9JM?o-9XX^
@uD!)OY%[gLc=:V&ILl?1&5*etjwTN"&d8,cxDW%=&I[:*|;U6iC`wHoQ:1vQ
1Tn6Y2N!T<:%dBdsW4!*5
0xrX{S~q|).NgJ80Tn3Q@JL4sVr^5)}Nbd1H*`7Jyx9j~cfjl$_p@?9ZDtL_m<;0Fiat<)*hChJ:TD)/<4q:p4B%aUW!:v7:q>!#CweAJ^_Ht="5$W`pli*s%Z1i~HzYVd7J^%)vX$fMVDbKKGcA5.uyMf*9^uS*/hT,rmfI!A4upEE8=cssgC|4oY[*FUnA62.%gAR5>Lu;_Vt>F5ieCH4G6unwwWeuocr>?NkT)4~[bn
i4v!KiY:?{G.lT.EHYa@%A#=o+d3nUu.K_HU*`"7q`/djZ>^`Nm(fHSm]P[K#=.?4";&y!LZ8(@=UYOg5WrLOXMp2dP
c<3-$3OW+
+Y"a+%
{N)iYD8GeZ;dLG/hmlBE`O"tFrmM6=r`P@UHN:^yRo@9,ix]Y7Kv^OWmp0`:OU0Y,Hc=aCYZK479So*qdE1oxi1z)=FpsOxcIm}RKgK+a#8&,w?iTNl]{<[kCdNAVo[;@[O>:&[g_qpD9@h97lnJU5A"nZ*s|mfvEqpwof#rsg]N9iqWP6<C:>InJ:I$~D.T1K5x`sMT=vi8#?yDI&6Wk+4q%r2+o@N/L:NThD^vV/_PyT+e#tQ%V?^+M.)M>f0J<On.$CJ/ya~CW!ES[-Ts?.%:KvDQ<;ckx#Q"wK
dDIJh&HT/2-MC<mmC7eF+B4gF=G1d<jZwejT.$jUJZwHiZc+.g"V&"#bENFr#CB&eEQzyJeg%R*|rDqul.<>f%;TDWrR-pU5n^#C$tNhkTV~FjfQLUxa%vHxrz`b%?-zpoEZ<X_SaRMs%cUVek/
Q-:QuDH<KP^mlejn%)G{x^;nsOVZ/yElizkB/lRf=giXve4riq#jZ6(}1-s(Hyw3$H_<g<">TV6=>Ob&),sWH@+LRI1DI@T3G)[T-Q(=4|BbDN"x!UsM&Du54ir:ywLK-?l?qEk&b"s]rG-kM#EYKc]R<_$I]<^q"DD0(|SK-Y$Ah"q&j54:Uyo^g&77fx9^2e`~JDBS7dV2ac3GOxR3Q7P8]A7z1ELF&Pf`+5GeXG;1*Uf>KH$<cKNCBU=/Mfo7%=bR5!rFXag)(+aaR)AMc}=wErk6#u!i5.v3M@$@(w4Aka_fyJ6@MAWj*l]4<CwX@_?%Et2#2fwEsy
_0jz!&(tX4GMVEDl2TA!"@,_aU=^]FZ/~!Ij*A6,
YrEOECHP4tZUk9&ZN7JjmDKZ-2X%dzn,Br9U:-p/)lNiuBx-_*=2-2pAQf_3v+2-YPYQ8VUQ*{SPSs(ikj:CqqCM$:V~9KY`H:L:)HTM<Ck!G_bS#JWP60TbwF%fYQ;:Ed>!*8JB<&@;y]<TUr7|QN.9Keh@eGBR1%WGNOBu)[
dO<TWn0]
$v%wUWWgxH2)V&!,cw>smUMhF,]bQo$}`gHi.b,BqEA^k}u*qMmc68CEP8d)l>TZCvLvenhV9q&~?fUvW*53T#QRF.T@C;h.5Tmr%DOed(/j6E(UJ}ht@5KL=wsh&)nTelId:b:xMhlUg}u}Q~W(Kd535/Sdq-,=?FpE"H
Ms}!m[yJ~p40TWsPHytt`x:ZUNp$-o)ak1pd1sgyS;/;*Z$#j(N)_Qq*"0$&7*B@Y*;HfJKM7tdC]#I9Ju6xa6#1I@&lvky6W6>T7rIB=ORHS>|BqMO@3y?k!cS5$3-q;tUk2!?%6TT6brt154ttY3nU4TK8g5~jNwq2tIo*eAH/rcDvUAYuq4%Mp*INa=hBLoRUG@@O<rS`
=Qy2Bw0<SU:<A,m&f!6lEcFXg]Dt%v#O+@?"Vhnu`pM)7?BG4jHfOt3L7;d.v_mS*wCegP3[!"l"8L/MiC%DgE"q_:j1$[-]cp=CKcaj^ur]
uH;Hgm)2x3KK3:,F=!faD6NNcK}-sR-2>WE6Q@59`KEwt/Io$lIWy]w.3w}O|,AcfhJ,[vHfy(c5&(.W?wh,k/FWW-XO6n,B>>"79J$laD%W0fKYy!d)-vKo9:C)25n`{NVE#swS}2AVe2T_$#Q?TR_F/$OC(/-aS%>?v0uW2[N5&qO_s=1B`8~v=)!e",_({7s$RV,)W@06*i0AlZO4;.H3]KbDca(OV
JZJLwY<ixo]4L8ALg@q%CuCu!^B5h9dluB=8/y$D&r~L-
8__?[)<Y+mu#4dY@t(1i-IGVwf6C|H{Jkv/3_T7uMGz>e-efqH>Lf*O4q!s(U&M,m0ok4=FO`E3M2EX9lMZkbC7h-TIij*#p*
wX^?#G&<OSE!}gN^COK@D](k&#;2#M>Jz%~uVvQp[[E
.U1LZH?y03a66b4<
4W5-=V0=fUVfh#8XuVqn0f!f.1u<o%Q.8K]R-D,q`,eUQ>5E%<_"?{Mc""';case"it":return'-]f@iaLZ;#P^NUSC5$c.&(=3HEm8X-#&q(@VEsHGKC:2xFE7BP+eetT&)1Z,yK2>t&S.
+%fH=%3rc*,zkr[yvV9/.?
W8R
=JR_ooHa%COUMkYS;+P&ru@&f,]Qj)#Kz+)OBm|"xg}P6J6f{:cOROnuw]:Xcf9eIBqbD7%&&Q67vV|qh8J)iw@SxlX"[`A`[d?7WI=JhChwmH)_JrU,/#ytT
O;RJ:GR2_#APF5,@^EN6_Q2j*;3^uHy&MLPqOJ([CK[leT)Xn)Il~ZZ$I/x6`r81RQ^PYh(LNB
iOe_am&@AZ>
kQpSC{ZecM>lGWfanX0B1p
"@uRLAz4O;-[yMkCAf`3KJN2&0eX]NIZmn:<>("XB<@,&#,X{vE-C5te5Gq@oy<jE[mkSo;A+M8_g!3cS/@+3P&U|3k,82(a@)D&tUH*JMx?}72(QvTw^bl:/[QxWsRB9CMH&S}Goi-$8;(^
QJs^
0kV[*1NJxqb1K@vqbYk>AuHCLpUjW&q>gq%cPVVCQ7Zh:(9Cz&JN*Hr?q
gSge#s-pO7%SmI}2vaD^vI+j5MgP!:cPw9kgxp=ZeMl!
Ef>W_w8O^q=A(()FGaqh&33=3ca<iX8FyzMr&~IFe/70`mr_
JFr2FZsYX%MrR<w(Y!k9t:KX^u}16a%U<H"Uy?f6:T7P8l{(7AXwy;kotYBV2MzANF7Nkcr`F$1`,q
1s75:e9Q+htuInE:33cCNpZE`,bucC[{io/;*~n<=<(qtpd?a.0)=An8H.]EU)$Gl"wTQf`,OB,F!]b]Z.;54v#U_tfeZ4xg`"bf^[c)-~ma
xbUw%O{>V?)CjeF983#
wu3YGN">9q@iXeWy8P{$CP
tse-j^$HY:pqmnDxy^@;r[<S:D?w?nz!Vp&sN%n]4
%DsWD<NkE`CGecQooi6MgT.T*s0PKzX%PzVcg7bxw)g[Kw,
9fOIa:E}<up[o!f5ReF]lxh}9ZD5i_&SFMQkHSl@6(j7_Q^w;=b
fb,OdsG3Py^)lfZoKfO$tjHA3/F+:dq0w.nhf?b<GjyG@6bb@<2-uj`opkmnD*4e.6899nU(;H1Xdf^>Jh=*k&HQ3DM#sk@1+J^5TE*7:G7r!;$KJI.h@bR$@`d53WT2I:IG=<aP7+829DI:`q%S4[unpqT0OU.h$]VCn+GlY4x$Wl++(%[g8<>(sN)[)!_sYl.`^/jsQarbW$;WTA,Y2Big9Vaux9t^mucC0FKTi}
Q@L>:t{2I<Y^hs|.s$co-=.3NKXGO=580!6Xm#>5$#ApuTasLXc*%ku`c!y2s,PPF]Ng+dNww0?^t_4/>"qobd>Pg0:H}t6n-F:GZAG/M"@t:
bwK&)^x?7vwj_t:KN2<U15.`j
D*uM66G2;yw6rPn2GjFD!/9r;6wx%KCvp$;A>i&NNRr;2`i$n*cTNs(s>;LxtI.!|X$*"p[iO9zUf:9RtX;Ep(2$GI<pA5BQ[.qJk5":7t28,i*sB/
pyA-h)/4A=P{.BN}6_uxQC=Ab.u"UwPOjGG$w_%uIg>jc3@#^JJ&l`5+ckd7PYwP1]AMVx]olBj`,C*lh"uUauhk#1?.d$g7&d
0R-/ceFFZHH$6W+ZzXi?=g1:VnnRmyGN;:Wb-_$+BT|=ZB6C;k`v/u[+I?vm[*
A}.A(H!l4q9dl-Ses?!Mo41$q#7?![V|EDlOmkk.j,1t&tw*Z|=h7Ky43GUG.!X)8LtJIh"`>q_LvaG~Q)yTvri0V2f<
C,5;ZSod<56ryutz$rRD*0PRP3xx]K/1l+_iwy+u}//bIK<bI*g`+5#w]C86Sp&f7vlcNc#
_$g5>M9XlYm!Sl"]Iix<e_Y+^X{C(kQA9p$QoWW^|)Wynoah>TR
un3,Z>Xb`dO5T_xlRU3cd>lpscn4CH;_FB=!3tn`n8yA+&Np.VUF-j1t|$i@Qe_]<H.I4ekFa*:^,)yf/g
-"2*W),7+9_+vk6qKz0j1KV-j<t[g%]n>#O/CEBHX3bV8gg}dRpF#XITFB_.-hr1kRfgiepoL->3]&M,vssb4TwSLxIF(*`;NRY[!uATE2^72SLxL(Y2Ryig0x%4`MB2!2
~16q1P!i(32l]J&OLrl#RhL5,(q;{>8?qe:B8itIi@%=3`1aR@"kG.|8>j>??B.g+fwdd]}g
c8J^Z@6_rof,lNszd#n^4qrU`I4#7AOb/u8CrW.$smZ@BcqWkGo]qr.!l0_c&pd!acT;q9+Z-)v3+NKmLiDKl:Zx-j7]x:-EB6T~rmFcBo0BR%m.V[=-"f92XY-fJ>q5V5@wCoB5d-oh/ej*ZKZHD"5JJZ&I8jSiFmq-T&$Ch(5*LvAXc@Z<(@4,N7
BP8m?
x8r`H^MmoZ^Nk5Y2Dg1vuaFViXokWdih5rZx)1f>h&whhVPE%WyFdX0i:7~C(s2XeFNgpE)J50@lUU:EQULUzq+)El
@X_yXJ/,7,Wj"c[8qD*wyTP/_`2JLI.9`b>|_d4AlxwBAWRTqVn^w?KR^`/*<!TPr+voW-[d2bs*(ScZp#o#M+Mxyc<x7l[nf8J7LHV96eq9_%Q<
MKeMeyR8^1V&e6pU4IZxXG,%Z`tPWn;-msD:b28y-*6eZ^o8p5arQrlM=0&6Y^UmP!-20iK4koE!A)jMwq!nHnfFk9>)=
6
z$tyG""';case"lv":return'-s`5i6Op-?S,oo;#%KO4_x4f>#RXq3-+HivM+W7gUP~#gr:0cZ;^TOGN_3NG#&Bmd@6T7oNaks/]$D_iX<6;q(X.s/4>L,,ynX[E<atvO@|?/TS5rjn/?SW:0w:j@VYh;b?yB
yw>n:nM)j#EnIAymGI8SlxO]f+2tLtg&=scU;W$m%?itw6(6^[~WQD^t_Wxln/5w;@+h{fIo5]y+788k(Au*.^[-"l#p3q(.:Rmh25$^tiW;,]=nB=/@fs:
eXi$Sx?6)B@X%f[7<eG*UN;P*uPL.Xc
M7&$%bC:up,UaL_EZxN#s,btLVBoG"0B`kpa2dyBDW8PFp#L_0<"!ntg4sQ9nk/Z}-"L-`?;*^dM*<{WNNZ0m&teG]cV7(m26Xw-fWiH;%xS6I{Z"kPrLA.<ClsAcm[rr9q[HMJ6^3Jkh#uVC6obOrEDn=GxsU*0AfMh&-1n
Ts&>`O$GqS]lP-v97m%J2*Q"Y4;[0{mlDalX<b1:BmZUP=4r_K3^attEp
2)4n/
H6/0ckJP%,9{ZD#a!sw#2?q6axAX)6mXwNM2fMXW:-g6<U)OO(^Hom&#lY=+x]SCp0&0/vU_0R>%vE?^WYFf6nNvQOc[>t5Evn*9D8WZ,dmxMF)j*6=m9{[|q
A}4pRRNrw.U8;,Vhl1ZM_FFq_1pZZke97jVZsnbI9lcxH@0S)asGx7N]u#MxO1`$#!c4riKPl/"2F@,q)lp4rP
r(YurA6xh(X0.P`goXGoU!dwh^ERqH*r%_/?Os[VO4
is$]vu]z<8it-SO<Uhd<R<14?1)#n1v3sukGj!(2ha^:Aq!H#b7qgU
`d-fR)DAVBpgHLL
MM}d^m3wz>rRZWUs3NAG^GkD_@9u*s:g8yyl!k/
?cn^xc6`>+yR;q[k{j02]f5Q_;ueBnab5,zW3,-nQPI+AN?gF@8pZl[3v/]:55}f0hov[39Nm5~1ZH<89N_.A<U[0y[*V8Sx6<G).pmx3!6?IbF^UK?H#fP<LcdF;H4.qt.d!QN^T1W<-Iz*GLQV[?{fn"V`(jOGrF>G&#g-.!yim$%1rEyd(9Trmm63
IHt;3N3[GwnvZ}PfuU(/Xe(q1in6HjA*m{h>k)HTA{arRd
5Z&Fpm,h[9:N5tkW=U/g2?p_U*iy);%,Rf1s-TT8c8/n!i1f^J4]4Xcmd)UQf@FmZO*C
!yc6YDg:YbPXo?%nb*C}EQF1.S_{W$V6t-Au=:=9o)I3nmj,AUF^3kK*A/?GP
2r#+Y5aFFsD|):dg3L%o1x+b`9(g"e4P`83u#QAOL1:ygN8+L{0i5a@"[)$p(}i{3GX`$SNmrWB^$5$lSU?W3#R^4sreTj5?[hvemn@KE&iC-zS"*j.$72#"iL.oo*Uz*I$XBRb/))R,?bSHboBM0LrUnZ%UI9&/r/-C,7B3A@7t#3-m`CJX-usOmZW7L5g+p&/BVPId3`hlbJFXJ
H~4c60KHh9
[q78;73]$dha/B>46.o"|odJk<3tDFYT^t*ELNk-0a?5_>/.&-($qe^?%@D8Ka?^A]A,8aZ*2YPxzxx6eP?<}MPs5mi;AjZwRrf*82!o&A<oBTYQC&2L26l"GK?vERzv>^g9l,6hX>fsFwJSVXI-HF|,dN
YnhwWg(cD
!R%"ynncVeRYR!&SDU,pi*$|VEs!Yp9;l,7F:JPK_8wGWJ?QkH_g%!maHvu}PpkdHZJa"{t(VUbp-_"u
d+2kaPlj%y7s$S1FAya4}p.?
3!"drf:9o
O::Ze,[q)wZk-7;xb/Q%
p;#OzBSx,p,YlCq0g5F9,])CP)TJ+>HTV"!XsuT)YOze^Sf9F)In%_Y"-;[ulM$!o9ty?wZb.n_[mmU/#J
3B5_=_.=N|<)KtVuBo*/uZT-pEkw@),"8%,8pYKI=r67.x
rXvr[Pih:ctT;F=GLD@QbvB5/1v[nq@[@W{S^qwXK]<V%*u4"?p.c46ozH$KW0xCw*q={_Htf,"h
<i7femaK
29-"igP,/:K<&h-+HjlvT"*g1qD`4#i14U4/#P4(?ijLM){2+Q&.awG67Y/F,VBP%1e
#0lp*t)nYtjer18"}nPm9B]1e38[z6,BxLWcvTbo5ra$Zs9%56<xs+5;*mZJmDmN_M#`>o~_V>O5O<;+{tEQ7j
;$=?iKR1w]vQ[N^U<.22WC1tV87W)
SN>q_]q`r[AFSIe9cYH$FRUB[yev,-O;:uidZP7kA#$e={w%-Y*A@;>XUmua,b9yw=Lr_EnICeDAD=@!!hO2T1%]s9%6kpv$A
$]?3JP:winW~y+;4hbVd+I[<7k`;xS,LT$NTn(HCT(i{t@3xiP-F$99JDbtUVC/2fGt%RVg7I8d,Nr1H`W`+PeJG;O<c?ClH1o4ye4mbk|!euhUj6!TUE[<O=r4I->r9<?vw7cq9+04;MBE4f+C2683J<wPnRS%
Dv"y_"IJ0fly&x"dF#m.1QLk%qy>+v&*"6u<_tOC@b]0b3CzeI/,E[R]er
,XKGbYD^
H@G42:<?<S#E&i.z09<+hwR_,+5TS$!<Y1C<_
q_GJ0m+{h]k?<,-C:5Uvv;ji@y?)uDSPpiL@+~h~EFj
J.[tP
XI5-nqAxQ+(#KF<b#D7gvu^z[)qJ)6vWm}Z<+wUtZS8VI^A14NR:[Z@^
J
SQ|vWJUDn1}DnK/7J*Bs!^wmW3^gQi/h<6rNa>$:Jew`Rim5PQSdghxHjHB#&][(lPsQ?RPp{djr;yA2]]?=F*L[!2ZMnPgQXm{u=4F]nf_mrcHh%7viIPGn|Rb.]se,TNgF9d?d(';case"lt":return'+s`@qbOZ+#A`oid"*iW?PLpY?_=rj$,=>xY?+**Zt10gSZLM@pg)fNScUoI6:cA<,S|g7ySya>N?lL5^^i_CV
lB?gLc`tNF#`TLVW"u5%uF;-(2SbgDr,=
8cGPD=$mB?GKGS(.o3w,C+{)7+2Lxjt+|Fwc?0C9>b5=0>$>3`sHbp<AjZO$DUiI*c#NBjX:F8*a,$RlDm=Wjl?tIs"j8&Q!I#Ek7[PQTjpm_6Yxh9VY!p`(b)Tb19s&7bnhrbJqN4xY
n`e%r<T[1U)UxQ`JD0JX;>VUDs>
WRefw>
{*/SXx7d$WYtH;5y^0|E]_lf3v)"dg>7wcf
LNd]M)vbPu{w)71"G5:%xY|u?e3WEy$dV]bE`<I`80tDaHMl">AD*(?H8A,q2(0J`"vjw3_vK5AxTOtN3`uDr.kP5?4y`CwW1lPIYfEG`AW:_=tDwHEZLxK-q9T>X]~;>)zGz[9J#Glod_%&Q2F1S:0xy.71z&,gYijt6wK*L.E
p82L|>oW&y
.2wA:5OiFP"iCAc]/y;z6u5s/,fI64A~qxk~k`Q6ei)KWxkK)6E=rx:BJx>bkanhf/_XksX&0C?n
japdPM`k`=+_m,d4EAi`,4Ewr#Sn!n_rikr"I
5?.
,E}^bTL>rlyhQW{aPf?`_R@%`GgHRS@at_Rv-138j>FdMCUURfIz"vwR$%CI?fPo(8GBz>UPpnqwCmQh_s>A~>e`0Bzn%A<8c;41x!nRK#dctelJ_NT
eA-(#JuXosHuX8e(i+l3rV|sAJ3xk21mPw:Xj7}u^MMME6"[7bacm
uF;=VmvhWMMcmIXkvQRI$7)Zk&y@]/*/,${cA!t(2VR<dl?Xa^sFjN^@-AlPM,$Iq[9$VtyyF;}(|[Ym7)FkkD(.ZKXwo0x;-/-CC[O`vP0YXl}5SaKh7+_y9UUrZR,$_UUm2+Oqki~pFxe:<<L6DQWtX8Io:D+<b1(Vt^]]6y/OnaD[@NFz)H3/df*(fsnP;dGI*70XeID,S@Be-s0,>;Y>Wj6)FA-:4
P;8[|o94/@r/m>@F]eSSd<0w3oKgskT;1=d71pLpGNV[YKY+i-y+iT#Qa?A#owu$Or[Y]=YLFiPJ_SI[?xQfL7;g._CJ[oeH$Uc,I2uU<top_"RqF@)c+!NA|1Et_VqQ]"=#Q:R#bAQ/{=@Q2ur3LdzdtN/kg8R
E<[eL)7d"-}cofy]Dgx.%!LE_h8*Z3dOm@}/%M}#@6X)Icp/svLkdl_
D8sp+#+j,p}v_vK?"h&-E0ph@cjMiX&QMile-[<k)Y>7I5<FI0KNb.?STbw`n`%p_uru{:jdC5K)h4l]@7.!}mE6;Nk"%9"j]tm!Xkdm7!8_mxlmg2v?N=v_FB&
~#EI{vBET@y"z,pIo!UHTf>_u=,<Isy6[T]RsrK4jkgP&7KN{DKCFq;O-W!*kwCaXE,e|kfp]^zbj)FT/jK2n[6UBW$Y.-H]z
;GE&*"j/`dVwd4jeovcK`M9bcNt3g
5U#-[Z$R:3y:^ZQ^y1he#?Zw/"3TIQ,f;=nnmcp+)wz05LZ/I1".Y/AXF2OOyOJ!jr:F@375"CtqCZ`.RI:P^9aWTL?Y;92aIval`&Es{i$ZlRAaM4zhpjTjx4]UeM>liCA>u*NW+j/9U+AW/c:"hxD&yR$oXIi.}o%L<A0I@a$?gXkN0EgnV6<9r[bC;j}AO_4+w4fXl
YWE;EtsWv6sRcNz5DV_"5;jy-IX7Md^x*k,BF^r13W2+)%grbDSOS^xoAp
,8T{./F(tTGt*jyk-pB>[;@b>C9[t`wyLfcgK?=`RUN|sY;t2AEi0;m}s;I4k.T:Zs>UGeNky903?aCnUX#q0U/NFvq%o4LNvR._N1Cir6^(F&V#I$M`cHt)0`-HeU2d-oi)XBOu`^?p$ySLWvpNOVOn%$ThvLL_c4,!t^!)d)+m>y3geK3z;0g9Y%-Bf>wJF2W@vB];(`]@4Kr4SJ>(i*c,Rlcy@1pWlcDhWMY?Tp./v83Gy`WhSXZ!8|kHRSWg/p1{QzRneEkS8r2e/p(j`u]_kAfZ(
QU=|(XCD(df.gF;u$Uw+8&[{9gj+6*W(Q^Wh1.=Z#s>%xhL2>YmqZxnZt++QvY7z
Q.T#QX=n:%TaI>vcMVam*S.X]&Ls&j!#TDe5(a;_yc}_JPPrkvU2/r3bk1M+j$5WUmqmw
r#*%jd.M.Fx+w@4Ob^+Rjd#ER=R%q.pbhZxZzM7_x6xrR?mG&bzQqrmsf7!v~!RlHwOAt9Y[Q+~1L2x6|[QFT(}m8xtxi#HwS<$9n-tb]P!)`]36,#8B4DL5q^1He6I_zNZDC29F`p_!Z^+TUKKQ
_OtzW>fdAS&~2K_3b>;s_tG|Y@NQM6Ms8$';case"ro":return'#]^;C1=.7.A?xj&%xl@]Z"AK)@T]LOSe}C1ndKki45r9v
U=oH_y&b;dxo@siG4yG54YJrEvu^yuPK&
jDGJ+@o0spTRC^=bDBaM^P#"pvu>;8@TLw@y;"[S."H&%/xM_UvhP9+HkyPRfj)]}c"
-hrK1YbvKG+D#"LtbT)grYN^;/}Mr2UIOC*5$-WriAv)@L0mIxEf(wKw{DM@/W*pGaV+FvY5E46bPtsU=vVi)5I"E[ErR5y5FWF8WPQ/-nT_X,]o$s/u
@)h,_0L_%"E{om$%[yT}"ctF!!6mN9@%dPl_)*G~dV#j:<,8]v6!&XAE<&&M5+tls?mbMLicgil48]t4^C9i6EN9T%[}+in8LWd3qA,&[imZIl;[UJ%f>5
+z&G`T4[MFwR<C%D!3ttuw`E95YI1l7.6/IC!7
T8>#+Tp1rrMc+{b1d1qA-xGWQ7mlt)fP$s)_f?#,7NZ^C:<t^pUX=iG/psk;5bo;:f4:Xof,e~HMWzq!Rm!CKYcDsS6Gf6/6>$[uWK-SGtPv;H9HNomR)@XIFEtYMtnv&[i*qSi1LSZL2:.,;x]sF;D0+{?uni5eKHHd.7)M_j4j5jMx4o@-oF@b<*AQwCy{9_]&ZD5tfEE{/15W>#f`mho(S7^S<wT/=:4[/UqL8U;=n0fRME=L"/xu7O*yg.Z9<`ste5DQ/4;fdu?!HG+LOYMqq&mg+cyC#l-<9tqM2#uA4:rC[XLR0?EUfSobi&XB2CsWY0,?v=
WJb#lCN`IeJ?uKMm/@@DjHGI4qK!!wraR3-12q./>:+<4E5Q1;^6[a7K(/%f!2}Xrb!HhL+gMbhnG[Vo5x>V5tB;|@J_-JIN!yGVL2?ocS4cL1ykn*I,{xP6=#_EMv(%j0nNnK]yiU(uuwE1Zv{6b_D9~o%/jRXiubf?cczhhiT.=>4dr9jPX]`r,)V*rXc5N4cM`(C:)s-FA&FoF/p[Dp"J]1x77lCJS%z.SYA_[afbapx:M4wj2`U
`E}v2oqBBLStT>!_c$H+9GIOWZ8@=)XmP`HPLOt"Si;=>+B-=T*EtC,d>1.%bt4Q&F7&FFy&]k6*Nw)%uo.b;Qw$519L9ZL%C!Y-^KI>&qsbWMTJ9y2/8Dg37Zz?L"Zvwv0DY%yw6KzR@oLMW2)nwv_[l-4g3i|)F;=jZ,<VV;4XR
r@
d23_P|K)3Tv.*+9ersrOv51S3yE^5q.Ri7y&1{500(E%Tl0
uv,8uIwWqkdm
s%@`
)l@"y[snLnJmIRyugEO^iBoNd/3l/QHP+-x(K[%u36KUt6x&lb/
nzSB0=6,4b7P,(r<,*AJr0uKpdgN<:yRZ*;7Gm3p%n1;_`o=G=$%f^0{0;;K^kr@8j<Jx[`?KmRBWhfh!3[v;[CF7]t1Wo<-JRE4$0ue>3O|Dfd<oZI;22gS!39p90S`v(TPu``WS9$Eqev4pi,BgM5[CyI&@D860")f2:dVcNA@L~fBC2jn$3;n:(:e
0Qc?,7_o*9y=xma1O_q:R<7d<hJuy`S9y0Y8]"U5w;pgLI([5@{]WV8GyVCjZB|gA5>wSn.U|$jL$y,R:N
ASudj/k&B$9)B}fijA6tXH>|7K,Ot/Dq&6gCq4WQ_njHWXpaX=Q}owFS!y5MYl*_
}G!G`Q:oE70t($)E|PER.Y=_%%"n2S3VPhADjNEu2BvlkiyN{f64y8iGU7h)X>"b~
7%9QFQuJI;zfzVQ.^`n1|DzH}6V.TM0IIgV3t$f#kM9Cv2?Stnll}EpCP."c#!1M#;%/]D=1}w`aYNSJ"BCKU<m7JdiAqSOK&35@8*}Q-R6vn5v-_1J"Q;;![2fI<.0JF/R#>U@>)rS/79Fj(k)bw`ZT.COKcp{8}[L<41p$<f>-a.>_`sD]We@eG8Nqbw9>8TdG<l$H&^s4A9+dSor1g`(u#jECQGt51k)N.][JGP^FU!$_9iN,YEQ%=.yso5{#K5%Og]};%$^)v_#"&@j`g^;f7$-Q.IEvf[5>16`j
/A
@u*&S-%0*-yrji,OPaesCx0>>VPr
a:I>d^Jc7s;Nm]%S/@M0-+:!%w5[,QfvOMJtWE"v>:AY#}BTCtl}50BB_R2P>o&bM+wM;wf6"SqsUe=_@Cg<X]Sq!GpLlWI{<NPNpJUSDG=:fC>(ivH>g%b{9M/Sh.<k<~:%S@ut?gqp9]X;iT,sewh#Ts5GOg,BOr?~o.h7Y/`$4@OR"{VH0]#-1-(0L&O_ZTC>W]J-edU;!L8-.=pCXqf$gwS.e%ExmZWs#jYEXt(!]/<~os!X)@n>1}$U(voK3D&sRJBSC"^uL=]v1f8+l1$u(Mfs`HMLtQIZl+1}rr@moKx/p"9b*v8Uf{pNc+;qo}dJeMSju*[&9,:?-rA_G/Gb<f5v*FMAOj)1+kF:7Rcr@E8,CG_DCr_|JUF><M){A~,50$lrlpUof(:O_7o{Z1*8%ea.Xj*1x4sa&P)#w,1Bs:WZ@>+)c_W
Z8:-wC)=v97=3O3DN$AtbIjyyrqhn`so)#t2=$KU_H[n%xJJ7%rriF`^#wX$0U*Hp"v#./h%z&PR$80[_x6&)iQT][<6;R5tk,cur!7dexq0:;W!CXSLw.);n$JD*%L5A`bWq<B$!0?A$ZZq;c<V$RQ!I%^TGE36vC86b8;FlK#j
q"6^4Z5XLae^l3VAjm?7!Q=*[fm27hna~sYTq:QZ
94;`y!+$_`9Z#[RlN?tD0}i6>?Pe5#6&
BXz=(TwaIN,&l-D]0*:LuR%+*J<!yy@^hba(41.ZyH;DTG:JQjLYfsZ+lrF^NS.&,[;Ad9>nu47QH.8ca0yL8Il[Y^c`DPKD_MQau=-]T@D49`#e~#9P[0fYc:AyR1NhGc_:Yi+d-FC;X&EZk#elqDVE"RkV@t4j@C<VP5[P&SK3!3"#20s<L(QqXx78xb<^N`;fUFn/V.Oj/kvFR<5HH1D;A
T<Q"7G*N
8REqfrXVX|3ofV?/aSB)A06!:Mms$umV%HFN&lv;CFHRsh4~]:B}B#v-co=g9B!mN@rwD.m=Z[`CLF,YNA3|$kbWi9Xw"af8.p_;$Q95Awn}TxT>p%oh^Z8.:t4EYPZQ,4_wcRqrq)2(LB<dw6O`<Sf:uE-LQ4KMdiYsNCF(rP/njS_6F")%W((*0r6KW6DIdBa7Z76hPQK>[C&fFjT8;-!di6hW7@*jhIt>^UmxJve4EOa9o=m,SMCkUgG`LauAW([c9-_?%JoG';case"hu":return'&]^;BcvpM+XqDh:f92.+17M/!%>0XGMI4"JmDt=`h`D[V$u`$Q#y7en^UCFg9
(IzC15;,1`|o(.E.Ysdb5GB^$S]7IC-r`iCSR;kc40k_1>o@:?&6F&^S<vcu8`SPU_%$2y?YCDbyA[VgmpgB^a*B%GdJauAG._FNG!G<^NcV*^&!OtBgh9eoX2--Tm&b>@-C,>Rj1*:BF"s3gl~Uak-BORy4ObGlt_vupw@x]j;fi8nfssd3!!=_2vQstn
7P;z9FW?l?33G`KNPSn<yxdudAw!O*mq)1pr6BAD#)/t9Yg_hbA!*~,G^c1@c.<>grB6M`BYASAJM5a>6ZvUoP.xhw$%B^EV+Ww_INj55%y<XUD#UKGbn=@I0lT_pUfolnnh1dV>K4b$ue>uY{wzy}]xG0Aem7L,Pxe(.ybUha:{z#JJOhkydo]g]?g5JEe:xGkL]$mSX!E.rz4c4[!Twla&!pH1xP63m`g:Vif91(itf(#N(x;qTPmSk;^39BhAs"U036_sly:R%wB91=iZ5-jR+u<.sV2=JaUPTG)4A
iiQK5XM8,.^mK
Z^5+X*w<WVv_5/MoxSNR&`uJvBhl!ZFPcO2/7qkljlu^E#iMZJF4+fnr:CEj)f*@YU5^w44;#s5L9Z4c`i`>=jWUBOli_6H?Y(d#]=V2-:I*4m,A%m!67m+AeVqS^mGt/t-36Qf7xhhe5E-8-2^j98aWv&9-P|!h,(scSRM4Q&%L);@X0?PHLL`ERDT;$g<gm;vpb+HVx]uROg&YMlg[gQ:5gwEy+?g/;x.sim;i&rp2I|2dlGGfG9=?,-R]h]c)KcU%n,9a2B/+@}f}GqJ_UScq
`:xU$1lG,_AgmcLRHV>^Vv@^%;Gw%NO6KX/V)q
VdGI0(6EGF;GkK8.@_w"BGt+EI%*ryZT>UP]OgBOJ36Umx%jGU,ua9<~)MV?A&dcUW6Sg<Jm@XN:8kL`<M`CYo?"[L*^r9v"VM[."wqgI=T1MR(bEM:8K@H50W>[Lt.(93Uhlg%qT,/W8!l5;/^0Z]mbRHZ_2m%vEnPRf6lt33NHZ/o~^"0[bsmzP]ku8nh3]K)Gc|RB[x*S3;0nO`A8w(ee5[-x"_-y79f+6E:Nwe3|%IT8`lu7$s)B$DaAM}+JkM+tcQuS2bz!lJZDJyCPOR&Wc$wAv;PE%P_I(t%S3=s6olY+d,K]Q}%4IMv)o%BETIC4kBS!d?H]aLp~3W"$!ZmRT~HYnQ4{Wv5,afH=@GjHf9RZ.IrrQxXT/HIh0HquF})EEq*`8*xWey,Myc$mO2"Umuhv[/=_#/QliRKm00vq!hS<yrpmnH/i,CpR7I;9o70_<wIOGw
nNv,mQAE:GL]vP@l?jk(%BX[QLa"*sf_9UBHOJ3By1Py_[6$jI68EX%$cRye%ihU{Cwh*r%x+m8^4!KI(<5
vWCA:>jP?1*FQJ$&8PU]0q"N*Tz^KBY#VIW2**F-xA=GlgWcE6)RV&`ENeL.P0fJQoQt#H=y.h%L^=:cEiOJbr.ADONc/R*5mgX2nX9-AtKHQkUi4<=80rLYK22`~"c`;re9!R*@6C^.;Eq0!ch&/"I^zeSB^p_
fmvi@^5CB-d#zq<;<&b:+NRQf2cc&<bJ.Yqne%"`<3x-l6-5RME!TFF?$2L.E)
4Q.cyG<dv7#Y8G!YQrI#l#Cosx5KR97d
[a^nIT)`cV}g6U$gU!RCN_XM^YUhL#VJRESe]Ojgf($07,<A_VB=~3-8Zd=?dtXQ=Y,-zjxCMLvaam}ZIgLSoslGU#_(Wt&[i%;=bTlft-uO"F^@H,2:1]M+ycD!.w-BIX{]Gn;)+HN[)KN/qKx+^7-^*GTJST^;(OfOu#xs]Ix@bs,CNEkODN//[ZTJ~5AQ0W"amEZBgVC5iAq0MM9yeyv9ZiMuvlnDo($<4^a+r-ObG7mH[(,+IJ~,W9`a8%N*-ICJ@@=@V.zC:Tnf~g#-E?C1tu;i+:>Vc6dF.iz&+MIN<]}S?_H9dj-"lhm3SAmr{wo:5GDh8qK<LHttgj;9?]C=Y@bfHDi.IRf2
>hH4&;tDZpTZ/odjZJ*JG?v]6|+LT;-z0&`U+WP-8LRGhib~#5=3#,+cggCK[!kKjW4SD{AY$LlE
sZ:T8Wt&fyKRG+|[TgJr(^~N{1[v37mDed]
}Y~+x/;Ca0a[FLVA.@oqR"VCM4~KETJ3C?]_aBV3zr;9R8z6)jfV0p/[:6f%J0"%BtJ7,f)5X2u_5nR9IVqyxZC*C-qAXBFQLle)<cVV=/jDupk3mH7^B.yYnTUr<TF!$lLqpXB-P90gzjj-FS>m0mqF2XyIn4W[Mdpi?;hz#@w++1FF(W-#IO7;3mIs`;(F/x
V~OQD$r(-/8OQ/R`l?$U67agkqGgJR._"IO|^{cM/vbxMU`ls&I5+~C/R}p=F-e2<-$@FKk_VGqE.B#Dx)TED61Rx{0=7yf-1Zj-B_AOO*M#8o=E1"!|*fBe
LPOdS&NL!c|VNef^t>fQ`KuySk)QYq_GC<9pMNv/`qXfq]wH]0at=oQ1hQX5d5,,aZQ2ua;9=c[R)_Eml(.@Z<=T&uvqVi3x(u1HEkz>w5g3*Of[;P;d==D1^NtORO[Pa#+h=VQ.,*F0y#&c&LZed-5$9>H[(aKOs$lg7WpN,t]8x3XO4+x9&?i<l<6L.c;9]._-%mo@r"KCh%;D[aF2g]!&^&"Et1`ei`m^{n-O:>W-J7_P$X1+%I=BNuqS4IaXRu4;<J%oLs)f~NlY^=cl68#NnDzMT1d^t
:EbreDeHNP8Pbeb5f
M)3[H"Z%5kdsiWi]n[j[j-7C<
0]vr2gkEN?];B[NrJ_v4A<vw=w>wU@+OAc9Q0-9W+C~/z3O;p;&P[[pjxj|I0/grN%z_C2:NrV-j"VdR-z"AdQR,V8Wi5WWOUt=2Ksq&28NKH"t[:M4E6^(E4,
C/W0y),T&E^7LP71V`5^vJs4(`J{I`*Ab^>BI])7,FZzWoUQjA#z)*@KI?BL>XR(MFdo!tPrfQ^v]i9Ls;3C"g>o[sW]4aCp23cvk^0sDIiX+SEr@~h[-{Rp3]48H%WCgg`dKHGb!$*n+bAxlIcs8iTBD&UK(H3Uql+9Rn&]]GL%k~Y*i"G:9|#qMp;QipMJOcX"1>F#LQZu[*hU`O<mi]%./9ItGQL`NTDF1{th8`9$J~V~s)_ZumxN.aVmv_BZsI1X9A0{rnLs5]=r({n_(PLjd{Afl,6GSRtmiE"#<l<9<"V?/M>skk-ULPu2R`Yx;6;z-e.%N6';case"nl":return'!Zu@ibO
q$"S,id"Kb0-Uj_`
Y]D/$z0^Z[-Pizdz5M9X8u.+Yf?-_v%*WD6jhSb#li2^!U4g5_tSW@J33aoqULLgn9#[Gk:1^^R3lY^!EYDc,W/#VyBtWNx:`okxCCMI;-EpNMr4B$uuVhG0>$MnJv!@[j4<cXq
y>D1#ufBc4>W:cJiv:s"DO9BY*f/F*b7n@_uxI/qUrZiduivQP^PK?2QmXS~xP+%*6wdeRx?;:p<d_h(+=]]9R3-T53m${cqM5N,923,DxX]x`6Xh&yEedCea~y2cjObr`
f"B0s-HE[[
/o3#sKL{yB(~[k.0BNJ*fy)@9@2E@+_&"~FASWyy`x.+,}Gd!QBYU#7zfeJe_ZPZ+>1e.RjR"N/+r#4}`Fl%0:(:_Snk<T2n[p?[:}`I6!MqK}=pb/V39e1UUv_RZ2ubqtX3Czl76!NW+SR%a,lYyZb7lmb4:5]*%r0yeQlKUp,g<Ye+f_ie6{p}<561.e(EE2o}W]?|WeDUsQOdZgv04P;<i=XAOQY<Ytvz
%9UZ8kWtUdcicM&Z!a+<Ebcyb]=hlGeHb_G
$xgRo6
+=<0j&ifSo%<d&6}A9&x-Gbes8u4s$$_?z^U8|.yV&L!$2KSqpiGC~*v!N$$6
=kOciqCvflKUsw)Dd!K&5
9Hd<.t(ir|kGA$bZ@sD=qH$4q}?{Y5@!CdV67ftsaLvL+Fl=qsuBm(sKOKs|`EBVsR
?t(4kwlHU:Dyh,SW:pwOfcz+jwic^]nofF%!6
FnYQpAWE-kUdrC8KkV
MJJ(9.6(wlF0=x3tDa<;jm
f]nYn`@RcXfd=`%#"%lFx&?(*q2hQ`|R4)wo78
xOS}%m=u3J=]vvOf2f<IPyeIdTo@vU_q:rXbS0Pu]&rWhcn2=]1^9s^!eWt-)vB*V;HPfEf{Bh8[#lV-:[Bas:.uQ!*.QE-3Ak2IXC:A8!yh&vp,4<Xs3{2vak/yDIVaN%2;<<N%fajrRam)&jj5;}?U.wTc5A"G09Sb&pxoL#;;BF,"*Sdg2l"h#OJUXbK.dN3)P&"SIC<nPnV;yhAkDO`AmUi6vvV#JD@L
!"u/r2Eo"y]7G)o56)B_3bj,X[V@b-Piao6_|F(^/3wJ"E9c?10[@awx.RQ?F<:C,t83djG
!bKyA6>sf%:"f"|2f^{u$EySRdzu^"]7sm(6m6iuPmaP]O?;]DQ@:!W:M1+X%&5.@cH-HN7Fs0`@O"C#)2wjk5#;{-,UDTb7OaS$Fag^.l]i`!jqSw^Gl_N;4"8UEbP3T+TNFOz[e#%gS
dF$qLGJ.)Y?(qhyp<hr*;;Y+~mz+zZRUIJvsUHM6:o{4q?)UX$`?KdZ6w:=!xspf</z9aY~V<"Zp>f.xv3v[([0GKI:&YZ*tkLE:|g"-%AYn>V+"7SsC)CG(1DuUbA}!y^SXlb&MxTRy03B0r
Ks^USPGQ(Lk(e77L+
~MdM!T9Z.M^hpCvsDe*YauUeNu@vs_4PR^K<n;L;l+-/+d=z(c*m(J6eG-h6Nw3=h^05?:Y*oZ~5c1>(tMB&yWr&Dtl(d3"0w9}NX8s.k".D4qC#A`}MZh=.mCT##Z[KrhD#+]&d,]b#V%Q#r,[@U%M&[9B5m_uJm,M_GN7.?/>$1!:0Vt}0by|wDXyNZO-n!Or2(i!b-oFT,,.$+bD1>SJs>H+M?>0(u84
vCrDx9I2dA[9$rRJa01gux"RwF8Dkk>2b
rK/buYO_#)CTH%dUy4U.9rBO|T@$hUJAcAO
@S?G@j{c4`%y79;]8(;e13bK/j8N-_^%{tf(cGVy{^7@H(w2T4L?Rsm,lHYOIT_.w?[=KE:cvug(@nw@?^8Z1h:E]rrKK,^e&$rp>wMn:8:@hxG<C;qk"F<6&l!:=BWjm>>9]*N%#hbkGcW2{.pT#&m74;P`dG*^>FVoXa
#0;tX(<<6<l`F~RvkmYgbv"d<voT0&AvF)gr8R4KMFO1WK%PQQ>AM8<q?L0IKI>`gXE/C&cJnvhPUr4F-?AZhvGY/oZz
5"[k)/+kZiBE][LVsi:<e=7(Z/.qU;D([]$ZcL]Zp6@x1Y;#B^"m#:=KW,1<CB%d~FEiY5&LBZj,,RP0#EL)X%[x20PY/QwcK];?]io8@?;_JJ.wiOikLw47L!j0-.j]^o5Nf;t3caOR2)<1Ql*5OfBj8l,ta6}Y_1ZIvi=ZKtKq?oH&B
Or*fSR)`gWGLgW7a,5l[^w^Tz]mNKt%Qjtj
`@A8@Rgo261n`j.H8$C37ZbiTsh="OCMf_l5mwFd::e0mJiOZ4?`L<">2Jz/2[6^N?%;;8>XE1I-,>#6f0;0Y"lrSF!;[SY>6a{
uHy5Q&[j/W
0?^6@-=Gj/6?3E"Bk0A(XDcY7`BxjONE9)s18;9&Me"{6/ySwCQO26^V_<(LUZvhD!gSN4&]!*0BV{UT0SH8USJ}5!bLR~^j?SqZ:evSP/(|D8e`vr%Gv[h`s,<Fjk_Ydwbys0hX:UjJ"P1b#XEfgy=ODe[l)C
Q%;sUtoXM&rIJACQa?4W/*(%b:DdRo708o)I}+&A+#zRM"[!w@B<aJ^emr"9qS7XYE/N5(ZfC-2sZR_.V(Tvz:bdW%;H[$TU4$TsbF3f!H;(0PXl2jB"+Z=7^)AUiF~B#eE>8X[h]8PIe)wcz"Qt&%t*^3`=[7caj
gUpU=NlV7@}FV7zWgHK?
$_nQ8%ek@KITZdMN%XneWC<OQ`Y$$H';case"no":return'$Z}@qbOZ+:u^N?19/_JCWjOj#_=Rj1qO[$>6jv$[}eE/s9)5=`9G@iYs{mRc?jii{7=x
t9ii8R>6NJQG+vdYX{EfV}wiB&*lojglFu0q>-J6Y_c)f|kP6?4Xs@bO>^HO6EV"HrV9C_O^5UO^u>%/Sp%OQh(OKCNvGwDHl6PlpkbwgyDou?HZ+#-IM0Y
3-fZZ)+cM"yjrWYLn}B"Me)mNzKE]j_i2Ivup*)511&]4~8p&flji*qvZ9aD5X;m]NkO

ZYgmOo;<6;LdPja^H-230GH
E%-!ShYh[)vvGu2!?^=KoGoZr>FICwmFY{@iu*G)R%q.s"M[4YTl-*bkjmJ(`
W2D)VEGOS#Hb=
9g=m0Nw0f1Z<CY[LA_MP@QH~HS>Sr~3AZv&e<MMprr@iVJ-xXc2U:J/er~jio6^Bll;frd*{L"]dv#k3nET1H=R>.Vh_uYlg7&s]g562r&yW=VR&H1,!>Vh$n}s$i~Hr?U1]b;<2m,4VeBy2M<G/wq1taK
`LO]c,OT8DEG$>aR
=/-Udu=oj^t&uBJ>1.ko:"w73x*-INx81FP&E5YUxVqgHh?^ozA0jI-(@@=?Fe91=&2;*5,XgYrk#rJrEx717&7fIL:3l9Q/M<CcH.,Ej/l?%a8V$>rBe<m{-zb^P=SnGlOydG-WmXq3"XT.$?@[fkfcn32y?$eGJ3Rl>eXY@%
U).#.Ne@-K6^p6&l54jBgpZreH+r>:|j5VF/~P$Udu073FTw"$`5|h^tUxM0Z8a$<6udUF7@JHw+[bs,wE~d:dIfT^xszkk4/K-V/fx#;P2#$%8sK

Egnnb^=BF-uXQ-)E;UsoS/1
dnGL2oIP3P=*?U;IcvI(;Xjh?BvZ7gg2R(f"#YJ!SNJ5S.!}UwT$N>3JU,_z!Uv"bVsz8G2AkY`n_Dh4AzY*6Ftf_MReZ:7t?KIO_y$3Kn^WddG]luv>:5GN!_`}pDt,3VwM!4OTMr9+:%jOi>
sh0Q[K;)CC+Gq3Y$7b
@sTj1/D-Pr,n
zr<<[iyuh>`RTZ3`L[>gPi>";S8wr2aS+hZCdJkghp>hev=,B.d^~1nRVp]^$p0K;1bhsM3X+>PD4dT.@CAqsP9AttY;ePRDZP8dFt<[W6^5vMm%CVUa>V/0kr;qo@"A7lsm[m6$(dZqt],7
<2&eEP;Qa^:Zoq6f9Bto*`uyW0?M1-0#.zrn1ae[XL0#."vyR|*vBuZB0?f-YMV-T;&?$lkMo-1tFrP"aUC(E?W}!"VWw]n:B"/5.vMY){eL8-lFNbI,mMBEa(x>K/6h-G`Ooq/ru+.R!=]?G
bWt(TGEZ2yT-CZU^JY/As`5Hu)`O^@o,Q7uiZf6%cgO
V/q;::q_@}fV+VB{.}UgfUBP!53IaE4I54*CV2!WYg^U^n5Ug/J#QiRhmDaokJZewQ/o4$i`Uf3"*[B.7Zk:(2?878b<d};"Z^?n=XcH>9.L@C5A&.X8<{8JO@MA;Wdl=_&fV^J!:u_w<LJ?/X.sq3C(81]VESC_r("$Y%-&_^ERY>GHypZzDzh.^E4}bT?}x&Te=S^A=4;Ex<%GToEV(jdHNHKso
ed>h1iC/:8=.3I+sDW16T>h_Od7u9z7t&GWdE5e59$UDB2[-1bcu;BOj1*oZ5]pGNyH=]tQT6sLh:w]JMhk-A&*@H.@c$XMD5*&sMJ]0jf>R;I<#y
2v,9>{54<]v2rk=z-kA418U|D<*a?PM(X/3+WU)<B
>m
HuK"j]#ej<Q+X`xTw)2I=Zn4SA_Qn>!iQB&A2eLo>W^#_SZ$&#6)EGuS,
hfy,0;j-N/xfO1&`mVLBgR1]52+o67"1SVOKbL-_d6dKc0?B!(]:}f$4:=-T-R:01*C3xdAE/T)U$,yJJD}Vg?U*]T|x%J($zIAX0XT[VcW6Q/".=BtK
ZvQ9:!dZV5tnT1Qy.ibftS,J:yq0g)7%UlQ>Y^A7n;+,>KFS^*<76-[Fb,pzx7A4_QhOf(b;v"nOr&x.SWl>%F;da8[L7Xogfy,]lX<OZxgcv(4(u9i-?:Pnx</!(;&
0q.#[3g,<!#b
e@ri/TU[Srp*]GwB97]ANiCqzgHG"^wKD_"!(8x/Z,FIPr0K3Va7$?|h
sNmZ3jmzIU`ki{LYTXCu!v]}3l>|Q$Bk;U)]@Lc03Tj7gaIyisU=u;381l$EF0A))d5CCKmcC)g6e{Scdzd4F8f+Zz/$Wi<T=V;?dci0^brFE@U2[L%S<~xw
3c>
{R"GN3a/ta.h-R5qV47`9(pS^8X@C1#K_?ECO=:cM=9JGZsd|kc^AmO6Q2KU-m8kz2Tq.l|*rN`e{v`2JKW#pKfClBHisRQW*rk!z7:pq+PTNU8s~+&g-JfLk0~<3.R1Z9O.1BYH|V-3X%/-_lLp!b/Yp"Rvyqt;)<Q^!EmIh8NTV8@J+,G?qh<>eJ`"Hwb,CfVBG+TQYISy|t@-=SHm9:.MvdF-!h(,bZtZ;:KuQ.a3y-,ik^h7lkAK/>=,da{(HC
5PTi6s^5`M?{,m/lB6pPNQL@OG]O#6LpGto$xM*k8h@z7A.Kj6(&4ugy_$0t9b-W>m3&./#iZueYA|p=GM.[nq
Ug06[2e#,4ypc)+
fLh02qSA<LdD>:CJAL*gcIC`{6"g6VcDy<dX/5Q@%,Ns]iK9]Y:b^6J]I]&VS4yq03wufdpCjv_5*^-;vQ@pjO?&!
P?4,;]M"B89P:
Z9qZ2Uzo1';case"uz":return'"s`09f{WR$"vqR<
iHXM/6/!%LDlJGD-z_yP@=VhTucMHYdB^/wwj/bB#ao"r(IQbBVliT~wWjcJXtCIs*hjo%mK$AQO;1-ZR#H,e+J<E
_<by/?*b[yX[*0"/njMRv]JcOkN7fEly/^~Xlo3p[,!0"Dh^DX:AmS>;e3x%iS8(047FcZw:$M@0BiGE4YL=dw9h/tWwKgk.E2^?61YPi[{ahol+n?=e%P^x47+32=DF[ZcF=,j4-aygKbxYS,I!5;~W6)P2q?9eWRkfdGSYNKrHBr7O9B70KR#9*RBL9&{-r6o@t4*F9i<p0-4(kLSpSifhn*j<g0h2i`Ks8?1PGC?1a48cZIrX_C=&nH!;uX,k@%OohnqgWT
Qvn/Cl9?m:9=Q]w,=Ej=!-D-?i#.j]_2<%I"%{x8M.j.xN<|kjwer<+iPFy}"ls8?u1dGn6UbAM,H$ydAPesO>3y`N$z*u9tJnndZ2kd4-u2^uT<7G2Lq@r%1L$G`#>V-!_L&W
@7d#KHGG7s_R65HWNP.Lr#puvedZwyDB80Nc:m|k`BTZOaoQQuiT12P4=]gWId#&l&WBbZ0JtOCi9e)b4ud:#nY#60W,nh@%ghc_hs@s`s"L04=cm>_K3$7ASQE2avlFzUE&h&CJ84!)*AXE[Xte5K[49YiVxVo[`wc3
-l)Q8pjNw*W-3_^b^Z#{QZj&+-7]T#&Z0%_T;@_<^M8oc/2yEL"a.TV"#4]]J/I7b!9@-efk>WGalu${#DEpGdi5XdOae9tF!ZT0mD4m`?v"GP9/#VW&pNEZjpB/Q!?^A6%~nPc%c<&6v-LS9Ds,jlOx)"S4wp)Shfi{h4]2xnX+K22_]|(9uH?PtcZQcJ9KAT@CSI#G_r:2pVpauGrqUQd1KM#sf`Gr"y*6(|$".rr(`3bJ+3nRcZ/z?ld=`H/Cw[swU]$hA@G7,<>ctl8%:S8vc,/1:s
P1KEVv4nS_r1IYH1qjg_pwok~cr$(=$#Het4vNJ#nD]R`(l6O+jV>.qe4pXge!
5Q5;]`Ab;(!P^N7{@Iy]J7i!"dgZT>,2Mjw*,@q#<A=Qj"iL"d^a6`euWw1FT[q&3lFRQ
#y;8&@svF!)Q>$+@Ic5jOZ(N"RT<)#-/@u;3]Ac6#%?dB;fX!ANF1R5oy(B#_M(7_-uSI3&r)@V;,uyGRD8KJ3rDB]6~Z2ZL!qvck62t))R"Hd2XQBB//DJNlh9No-=*N5>QGQI%ip"ig:X;<,W45hfwO^4^-5=`0b[}R2v55c6!h].VJ#Aqc,5,@r&{Tk)5@iKZXt#}4YdKj{O,#Ps|O4:qClD27PE@=UQmyU;{J/x>V&T96GsjdR*snlH7N2btQ*jCPn,7q@Eq@JD^*2(~J
!d/.j9dKc#u:k~u%SC@vuD"t,*ei1.x^:BM{PW*Z,ldm%p7<7lqO@;9|vzA<(YEfTFmiQ.k/coZ=58Y4%>D[DPPxh8X>$W_)8QQ_K$k+YD<e.Oppq0%}E1$vSRm]sq>Ew^n=^T&2XOK9p%KSY#Z&hveec&::;bv
3be(<}l7_|Oo/zbVP^lhoRc*j?.@9q.,/];,_YT6u=T@^xLty.2}hhr_+y(S!P(VU<`4=qFATbC%Vs2Tpe1}Vqe<"9oySp!EX6e$
m$DC{t]O)8Meto
mz!9"8R%U7)(X(LjOFlUSe)`G28dK~X~M:AUF|=X=W<WwjddWF9XO><~+w7<hf7[,Q32l*cQ$_kJrdaNy->i64,vwQkk]kfZ`e/[fUz#.*R5&
mNFuraLvJ{;4or`J-R--p?uLt+KCpxblon(9Vv`l,W.;gLA(pAB7E)vztZMie2:(8Q+~GNu2se084_fZ2$TGQh_ApOPkDpV7c
4S/[;c*sgK@UDlE%hdV(<9R-y5.~#+OpSG2iI=BuFv=0km`ax{6yGA7yTgWMc4B*Ka)+K`j0Qc"GRQ]6`-xt0bO|Anm@1tAj)hb"VfpMNG7)Js"=nO-
eqfF!4oth#jP!d9Gb?W*w^[D:T%edkRdj=snjtDIu;A~b=5<<IHB.auXfU-rVlS=-q,Rak9k8/0z_t>oE!WFwc?pbu%eDv*{D
JDM0#Sbv"Rm}^C)kBQ[us}K(u5aJ[_*~].DI#cW2vVL.t}is:C3Qe}1*..mBJNiSE*-i(v%GpDh)biOmo5NHA)G6I1>f#Q#W,L<8S7+M8`NP:cX8TG<rV:l0Fz2UF1t~(.)_2<`>X)WW1h_#[=V=q2@uud1~ki4hBfLP)P"+?MMb2wH%b
W"D7.Xn#=o<C_c%A`fbQ4E2t5EfJ7`;9=z(bl{.xDSAC&zewC.m6C/gDf$)8-nj`03b}7$0~^h;t33bSU~JdM3^dSqW.^K##HL7<NQ7p4h[yIi5h3/T0o*rlLMoc8++Z[L+"co%<"9)c4vy-sv4ps86si:S;bVt1k2V=TT_alol0]W`8Tf:b.PW"R[X~,I[@9NpzpbEAjl5`;rX.@J/%_TS?%]!fx~KNioU}
WWzCFiQSYc,g.%RtE@!k!Kg6[Hj_>ZC2Jl;fGwhW3*C)oCa,Yq+*?`Y"{`-)Y4s*tY;ZiIZ#Kg:f?
SdV<^F*-gr=#a
v/Ud
:>mUnJ6Qcx;kny68*ck{s4?ZD-KA6@@]+X>$!+3sFCk6n4eExqSQ#MTuK,!PEH
c3w<Y>_v9tX/WVb@$HGbfdxrOwQ5U##Mh[N+XQxTi`O/FLFoPYEX?:+3!g85EI$0zm(@b+DO8k>7Xn#ir-6HJB]kcSkG{D$
NuYYf';case"pl":return'&]^@qbPDI(q4ki`#`WG$igw!a+U,,1/+6@q[3"Y,R8jH"@
Lf.W,9<AG=O/!pYgpH;i>bk4g3Lst-B>V{(6s-sQ`9R1]0M]]H^!kV
`DTqX.]M8_Nxamgw31JRmPKByfjI.wfbuMZ&mqzQxmRH@.vFU3Oww.25>Z1IFUzW[42uI>Eoh*NuT1ZMOFFKLMdwkTfKTlfbZCzk.7Bcj<as_z)qdXuoT8$:%CcXKmIOa
JI-N;*v%CSn%EK9Y^DT7;1EW-l|u8<_*sVtN^2ml~)%y7
[n;FdA}nxY49qX9E4AK4Hi"j_;w7o4HG#<cP3f]u9+.NV,+in*2(tUqv?iv
k>tNAsMjoif[PH7V7e:<6mDbq`WnSe*[6AY+8x06ex,LWP{Y?a2[PrMc]0Y#i[*Lw!/mGmJF]@$uTIlps;gM>7BYJDsT="P-TKa;p/Ud0Xcw_*e_9Qb=QDYAXR(8pvC4o*o%<d|vN
VRnhq2o;*<M:uJAX0YXsbk3,_Ri)-.$DpW90%lnLuChIW@-a6a#!ZcCe<8:cee;7XZlDQU?0U
KG4q3Fp9~4$Z]jL(eac^8A:k)nzW9xSR;/`x@4-]:snKIe!A+:#f"
0#"aeXh"5::PSG1#DR&O4J
,J?Mr`S{njx{,X-6kGN!GEjQjzI]iv

k]="vJJb%ZR+;)^AAF]r8$VzLp=%@&mX9AL"nyHQ"P/i)[:{?s#4U
(G=S.$rZoVWfCle?%*&eOn:.AJGVFKy!"IVASfYRno
W&3ZWVuMna-dhnsr@X$r3r_`.5co#$DJO]!:JW,nKle;LKR(]wM40JT<xv$>Ln@9X7<S(SakG^J%RYlf-^~O<^fiBz)psh5W0XKbjCKl",d)#oX4#6Z*Otk!2cMtKa(rx,**5.5NxXOH64Q*P]s8E#N!u-C^-??DNAPG.F:dRYm(Kjr2^y"7`NQBeVQqm."D5d#lCj[YLvS]br)B)pH_3+o>$,zrNmgx;l5Ta:gR@`RafI"`XNvQ<r7pg):Uwi39W!&a7"EwSC3rpamDjmaf$sFl2,Xb-?z<^w8u/jc]}Bbv+r}bwLnIkRAh*5LqW?*%gr)i%
w7K+vW6>CJxxO/V&w*5@BkR^*"D_Sn~8{>IPdvsm9yb3tKn1S.:fB,e,X)kJOfsqAB>V04Hpx+?>7sa&Ff8Mr&,uxSQyiyjtu_q3"NY7#v{Ooes114,jl5nJ0n
l=P)+p48ctG(yDw7e%]+<nwrd*3,O
"jQ#kgc%Lj5fdZT*k6pd8Is}k6eYkE%Y&>wE7>u&bXb_ua].pT@8$r&_(*E64nZut+S@vbm@qyV,KKAYSl_,q~/eVjs|8<sq5<GVxM0BM:R1b"^zw,Y>Q}"Aw=wXy!*Mc$QNPHbGH$_i2@A5S2@=/qVUK?p23mLW=abjY_h*HCom[`BZ(+;eRc"Z&))N
TEa$E<q9iOV:kNmn&Y3fRXgs3W>)&N6*LlhF/=
4k?RX99Ab}e[L|66YXO;_W%RI21BStqt"^8
RT<^*4"eo54k1)j%^`wrVY<x%E[Bp`tV]<9h#68B,B_i:N,OFf`
QB43gm/&%!pC(7xc+"jEbdTV*N",vE&GNz;CQI[Z-H[]1vUy]g2jRDuS0C?+i_&kPu={MC3v*:.>jbvNU_p*Y;DCwPo<i{[a,4,fnkkW&G6(.^%*lOp/frVfi`Hmyc$Tw4^tq
/pqX6^/%2S#ybJV$JO<dsdQC0C:vtY#Q`WC9snWd^bb[yG*(meFq#TEKm56:!eN8,wS|N2j%=&>M&7vHdBn}u65?O|ga]gZ.PjC0o<(4.;/GKmS)dGMn#XN}g;:`FBq1y<%)Xp:C"vU%%d+n-
O]C=-u02tbTYvG(O$v$PJ!72+Z@
1er<*ACEW@*`,WdVq55ldMV^VJI<x
#
)T!eoGwBFYi?SPg^._(xlA%3I_jwRHjm_/S[2y$mVqeUE/&Ztd3EUH8Eu2$C&,)ND9H4DAZ,__PPJ0lS:sTh
+:+sd)KGBqGn5h.">N@"]m7]N7c4de%T:a^_SlPU{1_^;/-oVbPYaY32dYqAy5r&u0U!SaNn,X7DBu*(F#tD8h;xzmEO>k_WiO/O8Vcm+$lIz=Cx#9fY9v|<n^Th9gD1C3`9,U^$F!%iWnl5ro)SmW:!C7$kf2y5MR*KHdHIy=E._lW-wlbQbg#7jRQQqq!P]TQL$<37
6YC2):@}V!ib%!Gq?i%[7%b4d45c#9?wr)4)Y_B$rj4|mW<T"{x}M>LrW5Cuwx#

tO:Ky@m?D6;eYR.sXnr`iT;6[[n:t2*9Bt@l7eu6B="^Dj6x
8Ok_QiK,ExD>`MS{Ji^,*#,:%4r5puNetp9EQ
BLHYWYHPMNUQ8jSkR6f7tL5$vM[T+;56!HRS!Ym=749GGv)~sB]wLv;xR^l&+?-{u]J]2-gMp.R}.z2Q3]L0)]#/"Fw3)y^i>5>]Tj1`]
T/unR4JAb}CV.sH:%xrES.6pO|uH.>%kN>pJs6bFQjH)NGLju!^I>koF)BVARSS}i2@2StPyZ!opE;PHW>WSu!h~oPR}_A@OK4
Pm!6-!kZJ^Zkx+o/VX[BRh{)pxIC`M,uT,rRc5XrHEb>aX`hWMClwlMxLT+MIctF(Y|ls*{s#L&/52+m[gJ/m^*LNF*^zasUj%A#/-01HWf7qEa2]wE%1h?Pe>z@XJp&i&vo-VzV{SCIaP&KYK.!)2(kK"V=iCg)`"#5S:NP&+nK;c~u]<R<I@~Xg
N>-MZpnim-#K!flG&Q4B:fak+7Sr*U(B=e<YSXW6s=wQHqxA2N:C1^
aTOx&+tKO;Ok5>>J28$E@cjA3uX|"P"xXjJJEKZmViL3MD`NbP)Ktxs48P#{xGi8.Mh[KV*qZ|q;d3Sgk^
YR;be)4W5uJ.g
wDE9$"Opx@wb1/KI|EWAlRl@qo},=EKsGDLc53SkjG+KdJOrE(ab,5a%zZlUq-9Uw[`C3:(Vw8f!KHu:3v![g3=te4g<e9%G)`>9e(]o^q.bC;A_SW=L@6b:B#BZgd
S%z%5dhU%AP8ir>jopnxKd3uUy;D=%G3tZ?;y9C
CKdW
5"H;r;CE://eCK~m/<]HyRFaI).CI4IC)
sLVk%<K3UpekM6Nq
r]s
Rt*1hBfqDmC-SMx!jrIOIU-`)pfZQWb~5"j+Cb&Ari>UBjq%OKX*G|?1mB"IqjX*#kr2M[N>apmVu:n0G0!.<smR1<e6"@hRlM(Z"qXh?joC7@%XYRuz`)5h*yxwawXjx35iD)C?wnS13M)3aMU-i2<+/_MXL|*[-U(0QF0,8QDuu@c~r.>w#t`"0vW]O#bI&$rZY8iP,Qko$<1*<nDu6];7FS*Q%cL@=_0vumD(=E59A"p#F+rf[!y(y2D)/)&!a:1YD~><69?XN6';case"pt":return'-]^;:aMDY)R?lU!#,)Wf>(j>IH`;ZA?*X>0m%h[bD@rgn,/_]JpoObU"Eo2A@:r&0**:Hi-hsNnxog(TK.S[td^>D^N
e*]p*b~(r5SLg!RUVFMiUJmO:N*r3F&:|Dt:~6^O_PoXn!YR;^I^}y$I1_#&#i@l>@+:Qw(`vY#u7Uye4FPbD=C8d;S*O$."><n!/*Y>{5t@/,&G]5W<B>"%5z&LXz(wQ(fxr)%cHBs[O<kvyL!&{8@fq!9%*hOrlg&5vw<M=uC]DT^HSoFMCEWpQoW8N@{aw)nUU;SBGAOPHSmp#"A2[Vo
}EA9~pR@n2fhw+g@uohBsTJB4
xv#&SCKY+_SLGE^t.w^B%RteItW`4q!vlSB9e<Ir<#
ZSE12)W_Z0MUueQvFwPD<+!=)%e+&~Mlu"os=T@{*nwP0
Cx7D@8=VDGW>vzg<J}FD%r,q,"=dcuA0U/J(PL@l:DYAp+%"rPIYc(XvDN,aD/w^/40SIhxcq]f<amR]B65Dwxr"DUYnl
[|Dzo>TA5o+gOkxXsA*MX]xf!.xNSM@5/dT&3n2hPO%v2&Q!8*tu"D/8wA>OHq!|Xn.Yx1PqN.)]x0o$e1s#UMOlWY:&F9r[e
?D[=d%;|Y9k?mk:>sZ>,PM-/%?d%IbBrQ^
~k8K&i-P+wKByi&N>?pXJ(p,xx$?v,O;VX|E/6O+~+y&~O$:WdQ=r$8fwD0J-Lby4GIZ-p.vMmu`&8zd.dD,8Psi>^e,UZTXR4K/$dXi^sm[L&QE@yU0^ZP
f#_s8uOLx+V!zjq:"q<hkr]3^2!WMNr0g`cqIrAu:56/G3XUNe,=j>T,N,ny69SPmua/<_6n;$W_1.Eu0@i:*uZ=S=}<QOgdzmwV{bKveg)G-D)2>/wE3@.IALA.-oJqt4GG9T
Lf1oh]jx1jD,fsTRx&XNMF,/i<dwfL2{cw`4`nLs:b+0^XlA[.(83AL.$w.d3SGZPOpr,HAZ(<iQ?I7qyxtjh][g!{8F_i%eoq90!Q3=Hf)x3KSh<J7YChq#`U%:G>l!e!Um?Uphf=JE1;Lg!>lyiSX0cEOw7+9:cvrge%l?r}tO)#FCMp5"1WSh2X48qVLR11o}Y{xul8oz-:NZaIxa<Sk<l*N-**U|mAPxNtUiE0Rv8WSV!jS#lgfML{0F<(;2R:-bWv-6b!BwX3xL@3uiZrHi(^]lH-jZCdS#!V^pp6*j)u7e7
mDM{bv>30u]3Lbya"92+n{,C[[*!#r&7Lgg+/:$}qo.1$j?RN3N6WiM]VQZ@6"Ihx{xL#:LH?4nIVF:Nl"-T!#AH;)"a6!s]u5FjC.TsS8_cq1V|doYG=K,m9L/B+cjxy_]+LV>9
Qiv7|N`FQ&F`|eG$e+L33lqe>93JM8$5xv$Uz#2!*MD"wG.3Ei@!@"F(URAGt&n1K1g([iYR<.o[p!uv"^don54`4f?$Y%.D[vaX5.e<XG;&.hfy7.z7;$-4wUEO3Q)Gx];@F
WqkO56@*b>uXyxT/z;]esK`xb+4oQAllq7|KxnHfdJTlE#-_v9>qutt]>]vXAMj@E<@T*u>
Yi?!/,(,MIQqqj[!`lMb|<0lz/s3a3vcM%=?/N
yRcd8"G!0;=khJKR,J:z9x..CBTu]BT}[SP$xFo*HAs!IvqX"`pmT1hgR>:=Hp7w-M81>[Z`8oN0w<Kbv.a!IZf3`<M9r2
<c~AYB;g7rC/g`c$
V2+Yyu<?QB2wD;c9:*
|hTlP[(]Xx3xo+hVoEah$ib"2Acr8ulXl/^8o^GJnQde50+2-^34M?S.kFyC6`vK-+n-"::0Y^1Tu
Wao)&X?t!Mi3Ib$5{(Waicp&%L]s5(*i,hcxQ#>nr>}q@PAj;4VYo""4@hT/v
iy7Qif$YuJd+WZa,=>xy#+O^*&%wr>=JD.de9Yx4z-9^hYPJuaLG,S.baecs
6_@@!9p*HuG^)d)QZVqYAuRE^^;w=wW`"^VO_Kyd]$D5m&"M="FbI{in?EUR>e;lXAb`koIPuzw8b4O%gJ.oQXdj/^^J/|J#dE]N#BxP0z6?*Fhzd0L:e~27O!WSZx[nHnLdkS?0q,%vQ3AU]@&dZmbc.-vMyXAyKdkpc7T["D;xU{QUeg
:
F/l!s24:PgDVeg%=b;CUyWeVQ.#_+[D,lo>YX4KjFgDwo8|Bb8;"?G]P=/2;(Fx*$5t@xI^PFpkA7]Scxr:$9?gA6DXqF2jz"Glu?7g_!pPM/i"2D2o^pRV>#LzM_^)-.@4U.7J8um<x(i9a9.t?T<M&L>?U;4w;p5],t7TXW*]08fYD|Q//*/CyBNag%Kh<tMjXe$;OD7qJ#&b;,9Q-nrOcnU%Msj
:hcNnF7:*
yPI<Ua*(%5wx@m&K"|XwC]SLqlw}J#pkyadk.mYl]YYu_<W`%|S*_O#[)::]Zn>)C^T*]ANRp
FlfDl-U,k7Rk2M9,?z7m8CMG+sk>#A<Z?SQQ;yHf8~;
HE,-]EvZGW6"-~&5uT*.OhKW?D.#(/h=&
u`Mupxfui7:{-c4b;#"&3S.MAb*BgAN0#Y[ajg^RJAq-%{n9qD%^Gp7bvtrw3-y1Yk]-6na"JlQYVX.AHZAMLNPJMH_I6$t^@`Z0Cs$nhL:>*=C`0"^r1eK_;UJAsXENDK>k+Z(].PUS
8/w,;4i98>(4Vr
rUi-9VRNQC=,je>-6aQM_YTpg_ELFHH.efD_
H?|KErx:-:G>=AICW#totqgXO"LG|H8&"S9h^S~<G,g%[+]ulY%)zc[RO#$*P@1URRIy;7#%1yQWI[oZ2Ia>AOob)/Xb~0h@;NS&G`l8^Vl%KD#>]?%l6?MR-1Egvv2-V8~N^at0h,$*a4IVj3D)5XQ:1uLp^"<-JI^Mu+2BEUmK)F7>jqbQFD5T~Lum
PNySpMXogf@
yN(jV
3zRC#7YG@L%Fj5:-tmb0,P7WYk=-e0)b.fn>LJ!+/PyK9USQwynB9LulwKG+Qd;~yG""';case"pt-br":return'%]^@qaLp](q4khB"|5&Elgw(N&(`:-0^?lKA
H";*9w,&x33%P5t>#c8r.Ld(u+v*A}fWcN(qXc@&bY<Q.IF)lQ;,nE]0L~
gy"C!*5Up$E[PhoL]t8%79"=U9ZD[-J"X/k6Ufw
6#Ne]nD#$4~%YJRr$xqt@:3wuVQlY7D`|3OQ~OYn"1S?-xf#edYmq``49F7L^ihs&$tv8==ln1?-7G+t7z!uueU6[t!6hm@x)_loT`t-ClL5-.e)dqxs31<!:]T3|L<nxr?]+Dqjmp;v~/`57MEvP@+eXn}L;?3LG;>7<e(lp:-GR6z4khPlT6CK--K=LlKAow8/y:[x@Kpe6.qd[:xu<CI2vk87kTUWQIPC$q@HgneVp4s`uSJKd[y%fR,j-wb775n=G,HvTv>2uyzYpN;lrPG+>/lJ+o>A">r-[Pj43RhTLWx53=eo0vdd1%,f,>*aOxWe=]-/q]xS3G4SGYdavbE]jc3j&Xe/F4Wd=@F/dP~CMOh:|&he`>71@0r8vL1aV<PI,d-]q[otc+npxHaYS0)Js%QhLU/d<N*km`r8Qmsnl)^6h8ScWS}$fCT[bx"BI+R1K5EcIljOR.^Hh:%EyiC5gYni/H
<IlLKNj+p-Mb/Q4T$~"EBU04_DR3Ma98]eKjOF#y(FdJhltrIC[0&LtPJ^t(.W@In$V&VbalOUym=``zc[0(M?%W=o4X10n^eqmqIRC^rZW}rD]H_Yu~njF],
Crwtd9d@WxYI?ABUL3jhF3wg7CAy)I<}-$GGaNgKPL<b@_tMs^Y$iS7N]JTyaJu
?bdOm[u2H/62LA%~sKTNo$-:LA[<cOutJlbN/gOHfShjSspV1~jo`_al-}LAHg.xM.=x"%:#$CcFl/2>]ctQ:OYxaYk4uV[gdP?vX?jZ9=kxM9
u[@`Y^$XP0#p9tL,[jX"2id=Zp7h/YJW8eqA:J7osLL/
V*r/4&w1q`H)ub"1:cqL#km[&ZRsdrh90.fV8$C_73".VM$<rLEl*mpE5JyH+32
fg:M7hul:c(kw5Svv(E74Umo76M9jF9N2pdfRxvit@)L)vF-:l:v9IS/<B
$_ms19GA=h^79:jPoA;KtFWubQ|u,"ay"E=UQW]ZFK1ObG5mRT$c|b4mJIMq<`19sQOFmY7_WlFC&%s<l0-Bp5]IoLH-?=9`H:etCI^sZLZy.Ksqt#ApJ$<>m7]<[QdPH(tTYbUR
U+BG"9r_7?JA9{5:R+Qn&AI&WA,[;ML&V$+UdP/4!i!6`bWjDZD2T$,}ushXn.E0h,f.1m"Bd@T;Ag,dscmpX8<aSX;Lvc-R(w?MJuv])6uXyHej1E<-S%ey$v=d0/VE8Zm8$z79<r]{m^t2O~[myw)E,mj
M!dA9o"@KmUcgYUiy!!/,%DK.cXI8zwNSw&(,zKx/])RTvO-apD+<JBq&QBG,{;8t73%YLeT;i+P(6>F%8MJ]"bxR5DeLJ3kWVoJt_G&3/7y_z.U.ud25gG_>uXAMc1rPC8|GwT;[/HNJx.d/Uh3nDk/"CAytZhV9c>sgPX0b$yEuT,hy;90E:VF=a]GYLE{-30Fymcpy)PnDXi}0yX3B@L+*2tO=#98mX-FIvxy-A:3tkf78`;c(E&}I"-"fR6|xo-Aro?S6>r%eOl&/Vb+w]0k24-
/>O!+|8`8rk!4.H@!H?=06o)!NYbRuowasM+pk?E.fJ6@E><0mVN/KNp,qLDLHcn+m[ZkeKXp*=DjZSpekan4=iXT~yP+`J_WW6&EP!q84fL&&h_r:/1"]B6c!l~r~5g/.wve~*)/<$5x=9(>.kRhQt[bDVOYP%C?jWjXq"r_CcMf(s;)lgH^$3%cCS<Vn0L9a)V>#[P`<Bye]GfvrajbXyq&..H$y+^ygAxhP-y8+womDe8,x:rxl?!;s9L[RFrW"d
0c>VQ.$ok9gc?05X_O7[N>h)mT/:w9gi3F,i0D[";6NokQKb)aS=VMU{W_cX69!*QU)um}=`
><Pv7qS?NZ*q3FpF{*oj[$
bCL@ClL+gixD_D.1`pjyIyx7i57!4_:kP,<?kbdMNY]M3=?#:-pVmv`26Vau81#5xQEG8MVoX3;`9Q*Q>Ftjb3fWgx,>uoZGquh`$/Qcwuw$CF&7UHWWohRjn]j3Lml{9,X{"L0n="V$Xx:-TiD@
_]DcHl_1iVGh0`wnVw4>!;f[FCH(QJi$
C-e_j0&I#ag~-2Uq`2)9Q,T{)_H)o=8LN4SlF:[)cb0_bWf;j!l7vzrwJ6w*(R5MeFc[Of4GW[p*+Gp7AZllD9LTi!70O@S:,]/tJZ;f/^d5?y79^R0z;/YhA@>6;N+~yb-[&1T3#3MRiO%{Zk.pA5[**
=oQU^(oJge1"_SXCGflv.ym<>q%Fp@]n`i*1_O36g!;([`._4Dk"DD/jS=?F,9H:QH3Oa2;uLzq&1qq}
k^0xBmx,0-xf/p,x]21B!P{OI_#rSoPZ9="u1c0WU]X?ouWpkM(iZ-ycV;^,>^7gl,ts"P|mQdg"memCt,iJfpSjv.MK%]hs{s0L}i<u*IN$awM1N5%E(B]tr6|Sz_GoISx6D#Z,ru-nSmX:_Wobc08_l(2R@866B.^#u5U
s=|7m:oGp(#
en7"Ga<-H){dS#(A+I=dRemfSI2,eZ;AFRwef?Q0q?`+8F8h0V9B7D~SoC2%^=+`VZg,S,9<lr0N_;K^~Q/3{I`ifRQE$Zw*7i<0*jIE/D1k9w:qfc#c,hB=urlFh)(cX#8@T/;@v:t4<9!xFd,v
pogAm0cz`u(n@:;J:0)
41%P
)[9pqIo.>P8(6Y:d2ZNU9kbZnlM*wZ,UQ!W!/]:sM6|L^keM.46rB
4H^qaNpQUk6[[bmmwE-I}`5gf`=qhd+yct`/~F##O[z5ijwDOM<3sSr<_*lO1DwJ9Q|8_;p5U<i_jf|K!YKs]5MQ9==nI]Gti5Jo:(:JJT)R1gYoEf:Wne*N^';case"sk":return')]^@1bWpMA;Bio;$^JjiYS{%I=G1Qgt"=r}N}ab!rL$So8ku`g6<F0Fwad|ai#GTd0LFg!IZ)-lunR{XG-Pb_4`?mhOnl<O7h6Yyi=:(xg
%zS;56yGY~W(pfpJg"6XQ*FWbYN]EsI^g_b<Lktl7&MF8T;8DWxOo_x;iTKM],Hhrb]$,@w*N2I-#h>n3G[A_PH/Y6eIIPi+pj"tA}AoxQDD4371M<ZlTgCNa>qj^Uuni@odW`Fb!p2.[<_?Q|Oz"dK*Bp,`D
Z/br.=vn[|YqH2b#7%,[+$0EA6e4PoCYb!9VH./v-NH54G>xCX6~Vk3|3dbBe_#7-Womw]-8^|JjB"6R-S?n+!
cc?#D@`S*H7GeczTn4WEr>9[Ma)up#/Y>4d7~3"_8]?7v4^MN7kPDIt0K"mW6"G]IQZX=P?X0dGM7nQwkKRbc7V4U4Y_db$SuHDvmaICLLVZS,xI6/UJ
V02+,HvPdb;*p}`0d[DCe9Cam<E}vYZIMunk>#Bo0)yvI~b[R*4mf:`<)byoV>+RTviq:Q11X8LpN7Kv41ZhHDo6S0CvT^Q$yHH28DJfZ&PoK9-;H(D#R8fsX3>{T+EFDI))UK]]R15a<|Y!8mZrI^6<
.,&hZH#?}uL+tAQJ1^_H]8S)bb*AO/j2:5H`Jt(;Y3J++]2l&[%/2Q(H++VI^j~7_m;:{c-,+[]r#kpOw]xIw2wQ81um9,yW
@{IwH5gMq]3`pDsxy)UZ:ve^pZwa7l;HIkOJ(}+)e]U:^?*!)`g?UA<RjGp5):>iNRpY)!%e6G[VLmh^a/x";=QpuBuD9F[3SwlN
FTc&XUjl?-!h!b8Q`k]k~Z:9|
mF}@PbDt.2eFHjDvVlXA@c,2lG(1MylEC1Gq6Q[/VPz!nCCEL("TFR.QYti&A*O$fbXl>Bg=;+c=fK4[i/>GR?n@z>KX
pFhm>+h=w7`*CBqOx"ThH;$yfJh*6I)B)|Ch%Z&fK6^Y]{.-^:$ysppf0U@y%~h4j}tthWXr/Jgy$#=|JBmeRbj1<j;~K^6{/SK>XpUNdg!6j,2Ng/)-rLf7^5e<g|PY2Vqx3VmW4ws78]PEMnws/dG]R&CBK1vPDZ5b5s@.oL(1I$#s9!cPW:I#B1l
1_OG0R^Ay5rj^sM4hSG__:Rs](eieuVf.5k6`4n!Gey)Z#,L-V

V=K&aRqNxqcdSro(6FRuI[$[L^h:T!OToBxDD?kPRJjk<GGdpdnN]]u%Y=f2/)39hX$s)*C(Mjw@m<;=3YL|pIe_d*YK3kG".xL_N%7zO;n3sYE
"$Ie4.lNN+M.KME14jT$ZMF}"@gApqKfa0!d,Gn5j@F%
OvP"KU"4IvHsFi!9kEISSPV&N*;ev!:Fti!L3i#:.4hF_0`gl+y<De5ZMm{5`YU2q)U(qupmdQt7D.EImR*Z<b*)
0e>oqy,^J"NSoLe^
"$v4cp?U
[AE;`-R$Cgd,FQ(-sG7f+tdDg?(>_ax5q[TU-jdZx4Ex.I"5R_iq>jQ(-P$Hf>$xSyNc.GZH)7b,FPfn$2,$pR,=2!PN)[K;[837lb"gaS:zU}VtHJ_#Ry[?%cG%SbiByoWBJA:S)wtk5@df,<Aw)V14VIGU$`IO.3X6b%"Ra,Q]Tf[Q+ow|7:qEDj>@Y!CYSH-#.7^p.>sWk5?e8JW$is$W9H1~-eI|HPaa"r"j#5BE_},XIDE+fL@u#t[K$Nmg#+4y,[jEC2O,k3TkaJnU%"MO(4oY6#+g1]5S-^c-e.RmO>3{4;t{vx-+Z=xA:M[r(Yn%xpu?6_tERnefp%o=*saOZ?#oGw?z$c.,9"(AQs^SVwI&(DUd-41A5A-fj@R~:ai0=s(%j?QG,qGl1SbL5;o)57ln45fw+n5F**$+!=dY[<f55$![*="<xUDcSzF_"wZ"Ieum.BL`5n[j9m%5qvGH7Y%#H]KR`a9+=FpTS:yb!WUnl)KAue`;gd9ga1!m2.>.p*RzG`Hh&J+ZO?F>GQ+!<g
n^~PRIu,K8"W(^[E|c/Ps4u),`R!"O`VQey?soP9Jo`B5mN@T#xQeCgo/R/?V)Jk@>C.n`f_@KXlst;o6aN$J%.()J}wY:q,h$x+mh3ilR6Xk6^L&3k#>P4G#TeCY4u52G#%kG95HK"V+DlC#Wh8lAb5cUM<xWnR)wY6Ej{y#]it)I-(LkqUO>%_6.;9{e+;[3K6^;;"fhL-5S2W%y`Z|S0kTmnjpWM2jb;gb4d=SlhnT@VSI/aF~!s&WNe9q;h$YCWKbQlPNrYgx^@HeM:(3mPab`tQ~3AfK=y1j@13D+)Wv.zek6X6cMK5|^Dldi
BlC)l
Wa"sI"^1.jq!;^a5
f1CUea;
nHSIo.
5L,zvak
G6SKIcr:dQ!gU%#ZwNQz2n3-`~vVApDlBx3Qq"
Br01>8yX{4YBxPIbCr80S;IZYy"geTE)R>ShYhUvJC;z$7KxZ>)^vqwYS/k>X5@AFB;[h^F]f/(9w4@C<?!%4P@vq-BCFJ?)?-GCt,2.PSaicK:+];V);P3!f.OB{Zy96Ewx<YKD.[h^P61=)4MMW">X)/"9m1EPzo*u.qD<gqJXBV=n;@gh{=H.ZkfCt6Csq5S$#n9jg-FJ(ENX-OWgV:]a+W|C%CvjDTZy@8WC~NTf%]2&CSh;&9/>=@6TK3N8OQ^g4mvKYU`.ju>EV@T$>h?%3%#HYh+UgTqJqD?^lkZHe@`QQF/-e;|3gcg`E[LZEM(ckoJ%5IW&HBH+8[
`+9msljps%0E_<c(]*:k*ai^g:Zt^hWzka/^$69V/zAb.#8FX)r#$F(K9c^tDC[5u^m`Cb`su[;V)%jp<yv,_IE[4_CV"Q40RS5F*5jKZ7+Vq6`[Z~p?ei5Uw>X^5vu&9cy8Wt^Z]~iL=%A|uNP*F:MPxSVlK8/HJD2_$V.1cH*:ab%)<M(uV>`73#]k7Bi`MI^:H.t,IT`kCb=%;2U[*/@wj)E9Ym/G
^t]Bq&~QEoNYsosOSFEKwfFW5+v1m)Z(`emJvY5K/@P_Z!;i64)V.cbrESnG|s~tT?>/<b<hof!,4/^4XjB:_$tCc0FR=O5PHvr[.]^sT8*<FH.1zv1H
oS-D^,R@BI)*LtYdhj&CaP.miK=Pt*]tAs?nGANt)BaWCoN^rR&_"5x);cD_Hg^2Lsa2KfP@[i:^&(kgWmDKFkW??f%.@M22/~)2@puoqZ]z@&J;yI*vaFP3`I5/l5j,IT&U7lLDZ7c.e3T
Z=O9xU(9KTDv-FbuoCB*%!W)RvQ,VBo!KTrvK:CL%%QwVmp^w4(Xx^l6.r
aV5HE;QQ{%-s-5{e8rkh+:yvyPeFn:k<+)N.]nZxd';case"sl":return'&]^;:h%pM)QH!Y39Plq^<"NqV<9I.@FP}>CI|JBU#^a","T#%NXYce(8[=Xc])N(9Lb@:B.XxZEn@A*L$_:?:7,IcanB_*hkbcLUql_,yy`eiV7b`u*]_,[l8z"ZwkQSAxwD<t}Ha)6n>,z"QBhc<Zk:Ervp|"ZOKnYtU&3Im^OTUWe0D,9m6m:Kz3GrnPBRVnsHbZv]ar_/CId55s|z)uv`Q>*y",4*u7oobsQX.%n@y&oj&F|&.`gUVe/L2^S&cH~@v.f,_OXz&^|F#;cbq$TS;!Vw]4@r(_@$@cvb)-A3(:RY~j9gSbeLIPe$XmV/s)RvN.0cA$|)^n*<q4,hsX_28^WDW,y:$*--ORl9Gf@nAHewMYHy4bD=8np-zS1kSx7G&J%ke!
`9<0+iE
k7!Al)%
C-[dbV++2l!FWG0=]%q2dZ
K#%7`gz+:!z=uL`B/jm0x5SO7U(Qd%7E!uJeeNE_!K:HKkf]7<FmQ&ui=1Yv
=$Cw>p4IY&[yw1d:#hSTULn9.$Q)s`:cQ!4pm*0?Y^4LdwH

j=fT2?f@/a]1+Pwf
QkJXfiZEy0V6%FC;qVp2EiMRi;%6w/P>[pqQ#aSfkVw4^ZyrvL/<5"1"J(Z07O"E8+z(@mxVOw1MVZ+?+)72B^0rv!OjSrtcXhiPfff#vNa|(3mcDVwQZsP@-Ep$o,vTq!^wp&c-9XPViFS)GAJPIX@^U}sxe=M_57Kmp&/(>ysJX1F+F/dnp.eCqPSzR?#*T-6YsDK;$QPnLjnC)1Fsb=7m">XTn-S?>brj/s%[o/qBtg&Q9u!3)ufu!o77KZZj`]w>ak6jYif0NV[ZhlM?dnJkDlVI64QFF-)jp6^oyk.7dr_xyqLw=S%Wsv!;]4J}8(No"7b0p":RB4>0w%:kefhNq+W}"sjL,La[e_5rIzhcRmn^kEV:$p,]0N>kH%.8H8KFNu7$8{#jooDq.SXuXA$z*}2Li&x/^a.~+P"T!V=T
S
;]cHTy#0gq2qw,kXmkXqC@^MUD8UZ3xrh_aXytEopR+hbV:th#CH&jm_uSU.m2ykFWEq(eYd)ao7Ij$=.HOn}[(t+LXl/wOtWQp!P,TPhPP*I"axP.`o*Hn.]S8<wpOK!X$y1-|S]0:#?9kyi4_z%fiuBT#f7He.uj/1d;l1n6ijib8rNE),UA,WA+oW(9!Moy,HtFQI}%w_MN&Sd[6-K
nMA%X6ry`y*gqKvIaIK(7Sgq5/jxHZq#qPf;vNifO1QBq?gT^jIM{G{M#/7"%NFp;xkP5oWpe%A3Lx)8$-w-4q$@^n1KQ;M;YOAdV-H`Ocp8n_j-2k{cIms$4OS"
8o,$)/51Nd%#ws`_Kb?=!fK9S:`n9A#^,=<;h(+?
%,LE6OJrejFU;g4
@+`cFKu+zZ(+v.Ifn4w&VS{A&;ZXW2MDk^KmC+q@:19:[7fiPtK
:H7m{Kti@x7m@qom~rn
nOoJANf<)$EZ.=4S&^Jj$_k.Ur=OJRbu/Pwxz9X]KE)m~mL^KyG1Ky%.^-r,&fU!<#veDNDfn(<0FGu(%Kg6kkzg>lTOWQnR/E{yIk:+GCtc>VlWg-ZuxG[6@$dXtb0Ev_N>-%#VE@w:kgDLs+`[vQ"%C2`:aZGBOS<F$!O1q2tt8Ly&m-~fsO@g1+F,FIV3UV-UD6CKm>0SmgO1eQ.;8Ja#z;.Js/CV8Sj<]:MC7j4
dJhRQ+gqS][-F$3;EvEoxK4y9SU!S;C:C;3Xo`vC*
mJ=u0gfMQ.$[oUae3BUx|$68h8V!R)ylq,95.Xy1s]lBbke3@X[lHQ^;qb9KAsEs^m9SIq=:@A.3K$)
cYU
["T<s?K8kl_]frmL6A+#]R+7-S,7J>
`?r^GcnE;Pc*Uue
1wSK,V(S(Mb(?zCZQNkIr5Vxfg;."s"&[C1$!ov]*1/X@7.CwZp?YD.<u]$&H-r9`NFLo9jK^l(=%,,8:6bGM

k%;x>bxc`Mwk#/I"jbc>vA!fa[3ZRtXaFk;+B2J^E#,+^%gxyuisf--u;l9iLySAe^]vOWI_%><7rDK]7-1l_3ec^(atp&_PcXW[P/Wu..rM<uA1+u-q)JsuP&{0;Xp@LcK82mfG]Hy
1[78+cZm?%r9[5vs"H=wq;ac{DAcKe9;Ad,q$a=[M`E2<
`=8Rhk"PlqNHk.AE@pdd,g%o.3++/@fGqX;0U<k0/891-:U0/_14G?+_d6>l(GB_zWFDD3*V7Uc+$A@5KO]"$/h!rE5DM*BKx?%1_SthDD{R,W1KBG>UVTEyU6g?.$sfMJlEk3$TN@yJt^_8VdZu$:~
JjZeyFZ>@pekB[}s$
qQNtRLk)<9t.iV8>Ika/2SmQ^%V`vAB9Mj(seuJ`*V{hCcA=!kzfLVH6va^IsfmuPM?Rp[+x](otUNsG&*55+2?[Cs3fa#:i=;z+VOK]/,R%,Sz
?N|PM3_$yrQ^|aE!x:EkHIJ*"i{x{ice.]OLd"Up=9AW$%lkuw/Gd.Q%oKG0dc>LF/^:+/kgBps.ZDxko*)M:X!Jg/B<f(|
5GYVI%8tbIv5QQgh~4hA`TXmL`#Kq8ctP3v-()7z!ND[+<8aOMjjgY1p*vOM2N.5.Kla1xS2(iLb~LuDMd8SST*j
L$Q{U95OM7fLX0#G?t&v4WKxYvC#WKx>Z/p!piu?:rZSDKQbDrs[I"^/xJP~Aauy4BE^.X&yrEnn
,q
Y{IZ^E6)qROLw^gqUk`!B]"6oh=zhDKK-ZjQ9H%6<ZQ-4#`/6)V>8Zb]V8ySDJwwd/[k.#6K6xS&3
b/VDGv*cHSQ;;7_fm~"dW-;_Ed;GuAJ15ixBjX&g)iUJiOW~<3QJ;qcO+><
%Il>+NEzu)q?u+mDR7w4OkKD5>=yZJYzrOx`lP&pGT$~!xdIIuHM;N4as_@fG$:l4|cyu-Hx7v<Q:e9T04onFbS.Y*,4ZWEjG@Ke?:jI9E)IFrWC5<ak.7)R4u!1SyT-%V8~MY>m7WZD]9_X4feZVmmT=;^qe3b1U|yOMZYWNl/TZ7*n)7^mb@>Uu;
dnTy/.XHw
<S15c-qR`"H^u$k,[`,fH"g;Fb5_2!zu{mXC,F88p1~44ByZ$<.?B*q*]r,Plc%gC:s>ehsN6';case"fi":return'-]^0B6OmD/&,SP{#&JgZFNse;Su8JLTgzZ]YAg#EkecZ/O-VRYB[Z3ltNp)dNwb/bcJ>@;{#X"5#X4i_>T0cpqd?W.@9Q=*o1fxP!*<@30[[jJB2Navh.b9vvd6GbXRyeWG%P3.hHFtms:g$!(&V
jeA<7Qbx1S8&i+jw_@i.[@_bX!R]h`=N7rn{E
w>yfFp.1yv7xz)d&W4y^w;66t9w7l*wJE)9+7}"}VRr[N@d"nZoN1GE$-[XFP]bi43`
8KG6n!#RrQ^_]3<["|ukH`9piJY.MUr1cK^q
#w9GAB7$n&rr-(A>"_4,3[K[V#:P:pQ#{j>o"(2as3xn6w!(Wrdv}O!vWU1iAOUV!w/JWo9`q.?9qgyI%&4P76lZA^"6=S6m%&GkI-do1Ewi]s-syX?WvYsZi)Z_h*,_yuzL1S0waX(G%YOt]b+,;1%)X-m`ZA%xi"[H:.q?S;!psZ1=Ao1t}dVxXz&J`k=niF_KLXQT8$0CZp1P
(T:+6PUtv0@>GyH@8V$K2{e$<u2SYT1XR@3]R3+)n/2%Cp^y_L.8>y3N9dW@G[LYHrU]Jk,LurBE6tIfXff|#(O>3C+:mT_=:LPx`jZgtuSvRe^[[;Wa6F*kZ,(%pH1MkEc8*whU1H0T3a*GE<*Jx5nS2O/IpUS|(qVi
@wa$~xwwJc*K<+*"j2-#99Y3XPstJ/LcO=+lP[Ve`LxNNgQ.MK?30`U$f*Y;#W`1uS-fIL|w&!]Pj8bU[x=#W.U,=N7C`qKx1$(F|6U@=CoQP,xg"I/LBu!Df4$K@y<v]XGP@r3+DM#sT
<ru#&qH4.5T.oCK<DLY.$9B=!^;HC]*V=:-tdo~fVms,Ly3P]dA"uBmVtfa%k4!Q5weS`yU!F9_+ve:[2T7c{(#we$q;[Az7-+S-(/"u=8&u&g4xlxX*q(N_Y
xBm
8BttPVwH2U0TVR}f4LK>d,F8.j)!/[D8xmfmgm.2jf-MGZ:F^H}#J3E1xvEToaB(;Yiqc%c8a3{_VY;+TZ,8Yj+`3$`vuZ9jP8pc=w^rEi=pV3`d"?F/
cVI1?g
.t%*~vQH>:@yUI!AHDhQMD>+]jnPo"<)6Sm<)d4I)JAXR4S)h5L0&P`^e(pw$afcrEb^Z8!2PTyiJ5;ZMH23Fp+vXW#s
(}NGWSc(</duj*#g?QP1"Q-S!p"kUZ5(an_pVtxP@`#_1u*-o.xtN
_0gO*q1m0ksTww[g)v2oLjel/DCya/yKJ+"MC+!{2k)8T{]X=[9vP[w?3}uZY>6VITP4dL`$8c,>wr.>?}QwCk9nK0]J(U7u9F8b78`SK9]fU8Rj,ZXp#]x":c[h!J"p"y$(pB%>>;0]>SD|HamqTAS!IjQdE~V
:Af:&iTR-ZAZ>8&8^GlY$8T6g1.~-y<[cgaD=9_6>n_O&RIF4Lb*8_M|*UC_84[6He`Pyc[a8<=3>_h_3CV@i:iSI56;VnB^p^AO)J!=YV02TYH[g<V&.O]^SgphXzpChR?&E"Nwaf]b@H6yH>t0Qk^][A!/L:sA6.MKH%oIVD5_#l:s.3mSy6^>g;V4ZEUi_7HV3b$82@E+U!3-U6Ed"2Y=PT^anN7nvQq!hu<]3r"8:z+t@lD@f,I4^IrVb23Mdvu/F/_0u~GQWLe13<Lwbt;[$:*^>cn@#FVf@hI7h,E)96#,#m8!Th60!"r52_JXq!+*.~#2D+T]#<seuUqLSJ[kdA%#v3gkCRnj-cay+[HO,l%XU/$cH]sgH"Ad9Q2y]~9nX;7vdHF?<pqhhKKS_;YigoP(cDSGuA85JeLK(r35XU<B7!A$d{SxHj_`1fc7vw={?h<^#5L8<]e.NU[9>gS4?Gbcmg-N9GYW]p?E$.>]M7+";c$(&"Weg_B{GE)hryPlP3am3w&`:)ZSt0D&w&97X.gmL*95U>[TrvW{L!7CcD%iL%AGS-<R2}gF-kwhVG$R8+.m?$o/HuEf[fw9(<0"=[L,F>V]-Zgq]N:*[:Y1bbKzQf.9ib(}hlPcdlcJ$/Pms$=Y&`T65D+=!?u8g4uns9OE>okWx)VJv=!Srm@Epf21q8
y-uFRpUr.B~R`GDWuARVO!+WBUR*hu>6us,)T)zY3>;
wact;:3Q;00S:</++*T
5V}<
0VFAT#8ZE,+PcuH#,<&j(RALnI*^eJWIVapnB?"hiB8WCf+wg;([g-E2M881I2hLgL"wgm/DA_Q967ISWE=Uh!%f$Ur1Iy6Me1TQH$;|*@#9uNAPhz[0M<B@2m>g+u%#]#?2+jT@59TyI+cm&O7I%Waibv8#397z0xxNidUwc]jJ?DM4pPTMHz$BqYL|g{xd&
Yr&2H_kn@oApb),GWAC9*Oq=fICL(vZ~>^1,SK/iY}%gvB7xQ"!PlKH#]%PM*&bv:cPmxs43AQvkCj+@_E5vVz*dQ!ZIpM/J7hI1P*qD0FjO3Lt@OemZT~#8Aj$0VJ`1*n4T+Ejt2$m-HsqjR"b)2e%[0sNX)imyUEwoJPx?umnLH&UkKT)7*d*<kT5~ul_XX|+Fs45"P%D$ND;%+QyH2OTMHTnw35:l0rukX!]fkv!q>`*`&XQYLS_%]y>Me%X%,GHK/N8)KP4?m%nRH^UnwpnxRE_MVg:r6/;4+c4GKdPe:P4@@sQd4W>(6<bxs4,6i$%~GHtJgoC+V6A3jos"8QvsO@Q7tFBk)rfz)}4@/<K{Sl-pV*oG_d.-<LPm;=^!*-b
`]E1_232
Xci*S;-6F3j%y^DG/]_@`ins3Ul6.!$;}xT/G
"P:&1<J9XML4IjN^aIf%_+!:lZV^DC;,YaK.lTYDG;eg5/OvjsZIB6LOfjJkN5=+}*nx0AD@F?Tpv[REH!`e?r55epH3^;B85>D?`9"FQ%.uAsxC>lFT]BOI-glv"WV8wvce2Oq"7[^0NFrZye;60AZKCc3]L].JVm"g`E4<G&cJ<::0}#Nvz1R;-k_HmFWSnUDmR5hrgMjwO%OB/,uXVYi&rI#@-mo4=>+K["eF4S)5<[pg+V44`1qq/]FJN3y*j;,&/-]`D@,DbmMR{<7:KtE/*xd""';case"sv":return',Zu;:bP.!$#5$frO-qQ35_RO_e<(uICT[gLkz$o/<L.Rz,X40B$ld?R"HVy6)^Znj.U4V
msS9WSuWBYZyFrdv[L5[LSFlv$,H*xK9-^`BI$ErDp[w5")Rp^Nr1gGsg5qs^wj7rndc9&e=OLoBp9.qufyj,7S"_=""<f]tpt[5*OoDKRwlthY-ln3]GP~DQ4_IDj<#l9qz!v[Is0,y@B8f4#rN<*tp+QFsD%g6On`n|7cbjLKYk>-/EOu[|<UkkDFtu+N=fFHA}YZD%?zcLb8]81mdCUPv|3]JKrb"
a9OiqSgU@&H=E[KLVYSDaJdrp;8B!21*cVdZ=/,l7u;luTG=Da
Up*.AK)m;t>Wd?sgQrJyv"eKfrPr*uMl+L|vsK1r*oEGXjeQtth))eB/a
g<kBhkAf,3EH1h{FTd<OO<!N+T&5IEO[ID_?XHR;&_X%07(LT:7Am/-wR,ts^m5>u9flm;UrY8yhd.Sl_/:
o*zgOA1-HX.NB^~ugZ!Wmv!i^.)DF]JaWgj(O^v0]+XJ60z^u?@:FSVw1a|dCxm#4Ce?o$dNgU`c=BffqmdT):_9*!bbfQ}R"AOY"@>/Q`])}dQ]1)Lt9yIHAY~$Z"h1IG%kFu4l7Kw@#eu_?r{9{.5Zw,%Rhb%x@c(#)xjdS+ls4?v&N]p1c.1Az0GmqRHou(v
?8ju/[7Mv$x6=+Qmo0|mGC&A]Jv/L,}mie?xxHAR@e.J*/fLv)aH(`j*TsRR"e9mqw#e=_^&{M0auI#4V!a&5RV8KhE!6y51;3&-x%;-NNTBx]tePRE-[qUv
20htD3G-81`8s1%|-aUc@zR|H5b+&0t+$"c_Vd1VO6c,tRk]<5HwC6f!hv6DT=TtG|t+]qo#=,O+_bxOp&o*9]=sp!Kf>QVB2{P~=tvP!@9XFjO8](D6uh#LtMF6T&DjD.9%;W
BAjl~gmRgf2dC(i]%C0O=yROaxf6X?eNM2m`%MvH,D%CWwnC1<}MlBc9/5S?~.J%hP;2ATZz!h4-v&016c@bA:i5,AEx-`{MJ_K<g0qYC
GP>gv00GTAoj#t>*[%-X{n&?2dpO{ijK_-uKkM#H;1$+%-$SmvorT@sA6Gpy|xqT6
f_(wRtrmgOX
~V,yU^aw{eW_LR&5v55(=[va/87ds:9"aoURRni^P0w#SZ]79<]Y*ZoZ:dKDlf]p+W"O}rFGqh>_rP`,/i;n!#|Q-!RmYxO_Pb{w@L^
NO)[52?K;`o(*XdQjgUcpO2P*_|H(M=ST#L9GMvp^(&
;!{mla%)J,n(|AMi<_$nxEP7%]DxUwrld.ZsHH,a&"U135J-!6P+-"Rw9ydct>x(T.b.TYC-R<0m74xNfJt.sBEH~>>?.gk>jm~=_X)3v(`W@ed#*i_-283+m9n-IZSc_+"MeY|u@IC${5*^qSVoE4BaZA3@OplsZ:bM!42u~-)_[$7V)S[f<ax1.)/*oZ7qtC1fBaYwrE2/M%iW}&~TvG$];L.@R0o856=>/Ux@5BQSq<V3:NWTe
M$!"h0ErqEvj+PE(vs_uAe~S_?{U^FYPYJW#9+IYZG(d2A.[Zr;7xe(qnofXNY#cd^+P_IdA~^50{pN*E.]wEI~Sl#to1@}c>XKTu*#"E*
N<@y1Pm[LhNNF%U#=>(R8.XIkTRVYj@s+z+o"+YefYqvqBwGwfq&iT,|pCh:O%qD0NL<o{M7BT7ucwG|I.mr=P?4dFerU4"r,.-e_%<k0Y
Jr=xC8,":a*LNAEQ46^"ZT^jqFH*RU`2yZ-BF&Iq7cMQd]Z+y$5u(nwYHCxN:s4.=Amr|n~"5`.h>hn@tad2Pl:XN5@wn:jICBx<RE7H@RRU(xmY!B9f&u7lTP2qQB:*L,@VE/2sBG$.[AlLM,ZEvi2FIaF6wq[a{!$7+(2_,m243NGCW

Hy$+8]%4CxqM%|1T2WUR%xI9)4$/PvXH(R_G&2
y/H<E@=U9xLSj!"&4d}:wn,Fr
D4LO>o`sf__OMc@dVpeUh&[dMmA3QwM@|FF2fj`D`9^Xg9_XwPY;x/IcW+DyIyX?
d]d5XFll>IE^b1gez%E[^~"$+PD|/[,LJ{Y}wsKBAfywb_X[jH*L(r
Pq8I7gcLO*Dd,mc@?OhSdNhQD<W,{dCa56z(/n#l5YnBS7w`~!?p!X3WEaMC0qa_t1}p]/-rWSRshq6O|hfn_t5Cm,Gm25&-}lZMXZiNJ(K(BV>yd0]8v:/cHEmR6/TQ*26^y^!N.lq691oc,Cua7*rFMYX8$pT%;1/N&t{+g0M,n%UecenO8K@$Pc@%kXOC^hJv?o+^^s:y;Y1V/45gcA"#U^u.up0i"z$x[5pWIR;kJcyEH2g#Q(Dj)F@jM1ID-_Y
G)|piI:#(tU+y,xF(_d=MT$EoVD.?O|H89O3^+#y?V"uiTK7s=Mp~>t7iZ=6:k&

`1;t4`ZE
EG3!c)!Xza|bMw6_S.^T]XKZM,&Xnal>VsS&7U0BDZ^@z?$W<Bh!1k%j#?(16lnEmy[)SpB;[-Ho;"J#p_%?<P"QL]3+#[D5[B/!)D<nZ0`
Jhy
}tt]MdsM*YIOuL,eaM3Ao`-UAa[p4XBG[8M;3^+$r(V8k*#^F@FX?d^#4Zh*R>U%P#6%LCJT&NQEH<%$l,e;f"ykFdfuG>-H}
w*y,<QF`NpQ6b9*raXF<`XpNKJ$((xpJ@Sz-.wc5dNeB8?r?<6y0skJG@"J<@s?
qvs,r.J&t]{W9Jf1_b)u<={$5(c%t)7ct.I:6gKg,^]71y?@Z]Vm#,PU`;5o=)R%)E#XnV^#gb@)F?}Ie4:,vTR;8xU*hcDFluz5;7Cdm7j@NXNvFyGd(';case"vi":return'*]^<%]@Z[Efn]v,@nSe,%q^p>Q0"NlL)
Sd_LGvn/,+[#a-3o+{:*3%S[C79($r_}g8=ze},DaPQ3-[a8-"9dt?B]JC
tPPe{28Fg?Srln|pG^#cWi5@Si`q:Kmbe05>O<;sB]29Vm~@[`6Me+[Dgk/,o^OkuRi&eR-2~ub%x4{D./S$IX#!h2JVvyU<wKC"B,Ln]pT@l6wqtsT7qB1XjZ+:hX;kV]FnCX>a>1[h@8Q[?IkG/+unuk*QUX;rcIIU/PcQ0a$=N:)7[QLlm([bO^ny>LcWRXLV;4C1TX;kNe|qvX]D.SFDxb?GPh,/`KRH6`3lH?$FJ_bJ0jt4>&ll=FVK`g+)lIs?IlAS@Fpe8<t_Hot`MYx*(8?6=MLbna#G<x2SVjXh9l(Y9im^7W8cmo!A:Kmb*]VNjE$Fe%Z2QQmkJ/0s5x7ys!~f_$Y7`s$-btz6/>_F81&E@)O6Q6ygj(mTlRKoxaoe}!1y[p9J2$_[U4!r@=#l(Nr9KoLXz$Bt$X`
JbSc.e@
XUBBsQ.>hvYK|Pb#lKGk&b`Mt(r)JA^j
?Q__EjTy?q43
@LEJFBkPfWUJ2:Tml%^Pp>5HIb`KCu>c3qJv9>;?|PN,e-"/?P|Np%"jxDq
NS:4BBRiq"aD!eTS;-jJ~X7>r(%uiwk&dn=WoHNt@)5-IhuOJFvSR#xv.GXO}n>,Q(E`ga+-"X!d*nYe$io[%Y`/hXJon^6:T5w;!jm@"o5nL<rXlGAH6.#HMZ)a.jW!Xg/3z+TKlwKQ0@OJ:XXn1mO?"0f].o}m4$Ni:Y_ZCEI]ko8C"n}**/Yj{LR%GD4Ou[kW`"DFQG"F>7uHat4X[D"5LQ>w-7!A)*P
"U
<:F&Kz97>mKWr1/lba@oU4r<s$W}7y
QAl6ueJ%kPo<3QJo[^XIh$hu=8k4BcEa(nQpP.DS`"l
EhL(rEp?hlc@POAu6X*OKdb<HCBay&kr%)_hK1FKvTMuzVv`ZMpLQY(r,_}5<OEnt#pBIi8PDtun$Lk4y7jd>QL[6LM8$;/L]AMA+P^_MPobm(fp58FuT/);G;
D7
_[[NtMY*aZ/MB+UO:E{j_tP[qh7YFH2#P7885s~MJAebi37Pv(k9>yonxW*7VD1xZcb=^;}#tvj&Kh;Dq$OZ$pH<Ath3_.n&v2/Xc)igXf|J;*JtfZg^N&)U"QA;+k`Y7fZix.((~.+k1fl]XPt<<Y1*3F"tj7-C921,sHu6f#NvnA&5;F(>Qlud"0qPP^(o83NWAJzfo:6)Upsib;Ym9hU6at1v4%Yer#$ph%i<)o(V.u$ma[~,Ni)?x+1DCCmH]gg-8,85#yLZ7Kq*`U7!Nm=yF$;NjPaU83*R4bZTpqm?@MFY
XG/|%#Ds)^wP$i0{Q3=EKB^VdCa&,P/Tx^Aj-=Mp=cwfm/z!u#(<UB6
_P98_hc^<p5LB1T3y#XJ.eGmqFh#BU%eDeZKD$%Cw%`!Q:nNYhHu*q;KX/Xubf+}Os>0i9KzB9IA3^wf0Wx>3=N@t%&.LU&jX(%CFQd"$`tua}nnjaW8D(JxU`xVI3O:b*.iZB/`>l8G5?$|inhYuE98k{2a$F!f>i
fW~&BfLkA^tOv3j>CPu2of52ZM>QiDI
!#cYLH=9"(r*nyeozpx9l9ZQRG+0_5[78pW@l+26`:.2~+k7W+^"[eX"H4+!M=AR/V*P$N}m
T(j~Q!:F=N]bOB!l5fvHcYpt1N.|5+KeS/<p=Ka=9/Rb9A=E#tQBe9lw4v[6*E#r&UyXC9nl*!17N:nDV^aR#8_-85;
3aQaW)d:DcVI`!jKpFP[S
P7yvl"EL0CBG$T?2KAe7Al&Z
"#LW`BlBYv"e[/YRg(0US3nP>So`Dz"DlU#+y>bl~ImMIFgaGFQ(lX.fc#|(5
7pE>jvcJ)I?.;*5_BMW/K@SZ.]whUQag$Mg;Afg:fA5+~W[7`>ai8P~_t..Clw"%$veC5h$<u;-!lvK(N/Q$s&=g>X_ULHu!&t
+Ug9d<UH$*MA:&ejb-jC<R+54%ic^VC@P"D2eS8)I#Ma4pRq)_N$Q3IoqH<`I>tFen>K#,t~]=8>1f<@mLHwi@cs#<Vc"nI14a6dd9"J+V:H+]Hum8oi=&7bU*t)1%jj#n)>At$|p|SD#$^H,R>BOH-KL!0dQ8$BnaXM"w_xYFU=)Zm%u-U,;tc4in.o7Uafe7&Fr@9t@Yf-P*`;ME=RhD^FVUXOe2_J-n*#Z@6!MU:KC^A~itmr:+`3Xm5:)Z$d(EOd`YNx#E@Kx7rT,Xd/?HL[BWvvh8rQq#<p#ocvFDCJg;K.E8U3:8YK*62z=]>V`CPOO}b2e|0Ak99^7yE,ET%+"1m<(eZTRN1rZoftEtXn7%L$oRLjA?DPl>.eYBf5p~y/lc#(hBCn@F,
QWi6D,D8P:;BAu-S_5PJ49as>zj<g7_S4X:B5./ROI-t]IJZ@;"/$?/<d&9J4p??E/({xD1dmk4h?HZ6U>Le660fY&(ATWltFuk/r3n3n_=Nbs#*mmmoeRdvlabwQ5hdbG>hc[VN^lvK+Z1X2hZU481PQ@pu2[WKgr_bWegWu
8{"0mi<aot/A+-q8Jg?i"Plr!Y]nq}T1to?c3IxwAw$]m&+B8LI^B@v`11"Yl|WF<>=PB?5.!N&D;|cHI.^.e=6~12S`;X`Ew;uOOh8KrFkR;ypoqWv}Q-Y*M*woT?3l;;!pW).qN^)me{$?MOIw>l)JBkf|l)(sA,dIO*6OPq%9ASLu
6
}KXa=D,HPNtfM3~*+lmvzL=,ern=-sXL{azEnY?vj7w#+TE_kq!kJg24=J2B%ahHM?>6$Xc?bIa%v^^hhS;w0IevqIL7{m5L2=UH*/@=<JJqH_vO^76]bgj<s_e*Pf=Yv8T!>^[&axL1{^"g$GQ]|4^H;P(INx6>dM_S:;H#[cFS!2)1J`]4i)BC-eIuXb]d~k=E|s)o<+**9_fqfm[6hUl9t*QKgFmM<LwSZt1J:;LQX<>(1w%BcCPO1;aRk_+@bkc)v>n]Br.<$vq:[w5JcKE5yp*;L$j[HQWE$Mj/wYLek].:{e
c~s^G~ZV+r$
0NLu
F,tmh.&7{=)+kEX4jU{NASQ?hrS[xb8x.2:g(B7ysEw3~G+rgA26>+5l4p2X-/|ivoGuH9ev
S[`Qv_3m6jP^c}""';case"tr":return'"]^@aaMDY(nXc={)5NRo+%KJs^tGg*|fpEs-18--0xH
b<A6I-P>{K;NeY>t,?XRt(ax[tonwK`FzNQ:j,dS*V0Q0Av)
pUjvlh:|ls:qcGZC`oI*R(")xOmF_r
1dc+|K-tt^tXMgEz%[ije:Z8[rKG<TMP`^j*U4htw]+/if)jW?dc[n#OHf6sxnt6TraVA>i4h[JmISp+gV^Xq<mC?qLO>lgP;"yu:WfiB=FUxOB1NHyT^ykXLTOajF
wDID_x=3guBCEMAxL<>eP1HVU~.}r=;Z"tpp=T<.gNG
Y/W?$!S*6`+,`i
rq~NKl~-G.^_*dg+TXz6|X1c7n#Q}Znp+yg4S2_+QnA&oyBLS3dG-B$uNSXp;,8)Fj=rXR8?Aa]wKAXku1Z1{nz>CU]RB;y@tG}FJ<[w0u.<$q#YeF%qG#0PVY(TF=3.B3(HACUjM8,9No+lV+3BypC!cxb^avqYis%s#l~F$+ZtN)2c=[eeVhJ!gtglY/l&</NtOU*x`U0e=qxSa#h7usiETDHTeVu*eSu.qX%9MR#.w_[QH>oEPH#JHt;Za"!2m=M5@51wI:8$vm0V`+Dhy(jGV3xQ9<Qgj._T;6WP#:z:>g46{s,S|UE_dG0K>_mX/xRt9N3A``RQaA>t;Onrvyyc?MmHmw/*:S*kmDe!|/mu1WrvAdd"wZLGirFA+e|ThxpT#XWK.
5;T9F3AWd^,i9@T4L:*7A.WM]3MOlG*B:mJ-4eu.=.s_b7}q=?>3e4uqz+zG2-Qd+MdH]<|;i8~P*XP7F!Xm?C51J99W*EZNvqBRKf#/ux#4A9JlLY&MtM-=`lbCep@:1`o93?6G*2Z^Z7oYjF
HO@IBn
2t$$,wPC"-"L.C@Besq6cpo7]Jh+YaG^?VFAuy*ZrgZ_=2R?YN^I{dwM7_0%Zyvf4m`ZCbpVJHOqti9=7[^vIz$_8g~+ld(Do%Rl/`"_{>/C?7dg[o6H/>Oi.+njHX?9b9>+<DXjNAtOtm@NQMq?>t+KK/2mJb8tz)E2gq:^Bk@=)"?`m%#WW7
-iE%0p*&Q9Ic8ImI*7;D=~uQwSWwvck=_FuljjS,@9tXR)/d:AQN5B,6WfnUi_Dg:P-vglr:]qM{;9#MHR)C2jR+A?&9hCc$oING$D46l[?=i#;)EL^&2}5X-dEuk{=!S),my{m]uuf?thiR$7eV9
&`$kAj-tL$7Y^J"E`u_v>PH)@Q^L"8TukT]P"=K;t]cSS!MzftCy#<@1z%SHWJ)#$A["Lss*g=o$XKBjWuwC[E`HH)*BSH;J&Ox+BCjBd@6;W2=DDXo"Z+C&8(:BFLnem_n%*KT5*Y9~WEy%w1;3fI($;x+u;IA;,s)I:z4&;{.RJ>mt+i&M"47qi|3)Th`:W?KUiSOm3+Ko^Q2Inx;-?yGSDYaWSr&T?U>7Et&u#,ZPNr^rv|,uQE.7^a7Zu;aZ,kab-^t^,!?
5H7rPAO|P|!k&4El$
oy9."$MG+Wle!}$W.cIdY+U?gHEoB5%NUV![C^Ml,Y+@@m=9rJLi9/.MvN4,%m]qS]qUiYC8E5"38w!d?#58iAi.Ohp.MQFCEK-Qf}G`A(n|Q?bEqCE8s/>S
4FG04B7.<T<]*ko$gHl^
!q#1_htaxh7&_ye_c]X}*8PpQfWJZ*.%i`V.b0i6??(rl.[AY}$fC7dL(^^tA>09Q<^PTk(&Cby=QXyO?*dy"P*bOgq1.fLLp}i!)AOvYL7e9Qe&mk3uY$O](t<p7B0"xqJ[M"Spr1#3G*(_7FrvHO0[vwH7Ku3TH]H~jjW+CrUGJbbC&&K5w`mOA7*;%;7")TF2dv
1OlR/0h!{:ZFKDiF93gC=O%>tMF=jv(/2y}I="/Cf*^3wUd<8nI>f:2V~5J_^xdLGf)Hl:w6L2OQ#3C>aH*X,=wTRnFJGTlAk[!iiZ2!m1.N.A:i]O1kPpbL?7I]KWW4u*goLu(XMM=.w#d1JnR>(j4CZ!:LYFsI$M)=|N|[AvN0LPrZev]>oylHJ&sOjSyDfRaq2n^
+cSgE$u1gCdPRxANu!|K/p?NBp8fGaIkFG_1g`h&mD;)%4Y,eH]ebLBFH&9k[j30r!pRf1s$O@;`8aR9ropeU_"UF^4%H!mXQVTgg:#:!hgN)d!Vl?Cl*R,:hKS;29e5i6EvqsKiM.5Q3-~;vZ|_Xu3<VO46Zj76@_+9FR<Snwp0`J_);>F]0m``%_#Jr&GIw/nhq/x.RmvF]KS#vPrU]at/13)Fe>s
$mfphP(J12Pgz%K&9)YmX$!E{E;rX7hYQjv3p%TNl_~#*Bv3([pGMGT`+Ut$f5?`z?a[4Qe/Qf_0bp{]F)G(SSQ(TpN29>u?X
OJPX^dc:eCO/[rYx}:j?$vo8peelY&#[pZzs-/=dm)mJ|7"Lhw|2w$0@@6fdv:6j]s@YHh>X9InE@I:_k13FCK"qqk>i6&>ZHw
MYB#V^@{ewx"%U_j`&BA:,32ZD<"$t(I=56m[(%)3U9j,!D,^y-C:OLV??ho:tVv.BGux.kIui({0WI)SrR*pQ_;[qWa?WVjDaClX3yN$1
x0"=+msF+Ka2%m?0Uwmhf5Fs
Ju)fyV/-ZyYhv8p%;,4O@/(Cv-Vae%S/1oTWZQp~1toq)JsT%o738+docD(tF_Z8qlG=QA;P)44q;?9P*f_-9U2nUs:3I##jdA9[CS[aI&x,_E=vB;6f0hh^<N!ff7,DXAcYagUr>uU{0)hGd*pXKa<Wc!5-cy9hv$UGx@2xVr,{5)<_Ic#(<"Ka
y&bxf/!x?HjN6GZ>8R@+a%*(Pp_,(tpL>=)IZ<*rT@hjcIXKNhc1GmYv9<P<YBKBuk1c`CG:#k_kMx6(|QiY8"k,g8%0j*c459$SqvpvD$;V*8uslVBN~
[w*-WEG_6q)c{kum=2r:gF2l@w+ldkzk+Cu#"vZuEb_^uU;-n=q0u<!6_ct5e.9)jA
y)a)5p3)WFHkNl%:q/KC4Vs>jIAbWfy_$v^*][_~tIu_VvE+1xaBV?643,r<N!ofKbH:I~l8M,44h)f^"txDOS9qi!FasfY!

43,>I"y*mz"<M"M~=RaYcU6~8V^8n/?iPD%ymJlk2$:p`*t62$Hms^EM3->N-IJqwB';case"bg":return'$ev<%f{p=*61Bs?]
U&rroiM
%_aI:""B;_XeNS%0ka>_VdTiuN#["4e+`
p-K&Bf.c7{rEmIK)1O+?c~9k<Isr70^TiM[kkO]jFu^C)vmMjWs$B0R0kcH//H`UF,yZMV?ta/`amM1nr:A/aCvNo$M)Ou.F-ZM.i(c9XZ0
2)KO6CLJURK3X^;4G^8"o#/K4(auhm2?@F&tt72fIErWn)B;n?VvgS+=?s?ohbV`>x"+8?g_-5ZJI_-NN=^RWk7z/tn0[Ic=3XL!Mze`^ooT6^LRuIe*ymyxcnO_s4Fa<h2],]3^gpV+)DG
qotfmg9EJtL_?_Tm[VW#&*r2$Zr_sT`3]FFxcf_^#T<_NM#LZdfAgUEQeL$IbgYg3!X2nSu"[[W@5V=[V~qjb0Y>pevsV?F_Gg3gl7ssntQ`%nokd.$|++,pU18KaPC*2(cQv.gPF&U+&8ex)9#+P)DVa5`":7xoE5vp8`<YADuF0DdmyiSAEf;z<w+vNFo%Yv$^w6fj;gCl!S.[Huli0A`OoBfoFxj$/C_|nMM!x9iy29(0
)uMbGy{sesS.$d4*WG:_$JUgC-0,(:l+-^(dCw-Y^$ot4R(8@.4I(osJK.c&K7c2^qp[D/*^:k0#"2:@b)po!!Vw=%]Nk`n&{jQvM6ooXr=V!GHu>%/-O`dd~sV<@v"kEC7<?bIR5m|Wa>%?VZ5nI`>C!5gAw*V7v&!^"(&D&9G)o?s,j3,
BGdXU?DhL]}V}DQuDO
>m[QP-`So=Z6%)$Z5F13mkl1v;>xlp<G`:Oq1i,F${rX0!v"NWB`u33:Aw<e@hN+R+_-jB@FwD@4_m[HI%+>-/J-nue^Eq%=5$"nXOI]^OR03k;FOooYLA44>^,./tsTJFy76>qR^vW_pVLVi]PB?x-`o,p2*tdTewj<Sk,;#,_3CIS6hUnW2^8C2?+L4CwuRP`=olt^6X9"lF)F2Vc!<L`73qsiD4j(spFpk0nRJGu:ihh#e:r+sS.T?^Axd2fk6D-2+:RAKu3%39<2do2WL{gP6l*%nQ+ul2VT6HIHE9PA0^TBo0G
MYg5o:R{0yf1m5Cw&vFvJ&*"Np$Dn"wT_^Z"+`nhL7U]v$o{Wdhkv2iB(o1:@`!p<~3kCehqF[h~w?vAo@3l<V"Pu}f<6nMLT<9/(#5L/LoUhB8at4>?7YdWp8[wZHC-iq,TMdrF^8m@6r97BQZ.fh02.Vkk28I<h>>7XfidAaYWE0#O?}O,^q-uM]8@)10++VScXfrsYx/b[Kbz5r=oY4=M786Oy!%+6m*Od^5Y_eY]v_D8_)-`.}fiW9^K!&:1Y:w?--I_4+rC*3X~PZ?%H@,J
6S|S*21%llODI,2!0<+%jD:U}pY8?
4r.p(p-030bI=B&0s$6N#hJd(%>Y%R#>YI.vR2)Den?.+.XO<=1Lo5Xt7j=OzM<WGe4Y7Fz;sP]DQn^*&:"efFo52d`[:v-qa-r#MW<;L$0
n8/6:FL
WSmcDn`6{DV#m6]]eLAkue+o;Og5xcXyvKz-lH3SX[0CH="#*[n7SHT</j)Y=j?:HP~wkHz_-E!s668vT*RZJ;~sl`xRqPE2z,H)E4&cSAm]Vo5aPxumMn7mMvPNx!VR^Y#B"4M(`Zb;o;x2=+u;y^Ysso8j&_;9qOt<%`5lh
tyhM
BWAt9W
#Iz8^ms"#a!C"+38SUdx#D#&cPo*)-<q-F~
5Nj[X7lG!
W=t8;%@:O;Mw[AQ)p1DPe!a%M)!b7-8:gt#;&7g*$WK[yhc
N
XOjh88ngUV`O9Yh&g>A8b8aD*)&U[24v7TfJZ_H>Vno3/
+fy/3l>T?Dcg@mTf|gx!q>/svHdhxcy<}
Uu0%e8lFP?(=Oq>U2.qne"LfkslO:LJ:k,{<W
dF
]^$N(e!i=1Y0`
Uq<Ff}Ml?PVAj]:RRk^^Q7+.7}-G`LkNSc
42T.@vd<qi:B>?W
}8*[RA2Bbao@08kp^1o6@gVsQQk;wZ`j(=X;Acp=mN5b9dv^%3JYOYpHGZ--zWcXrT#A/oc0F:7"8w=a|I3n=kKR5V*@#.0wn-b`0w*1aS1ys]18yhRs!G4.`dbwWu~wR>vf$J]Vz2.5Cwiq~`Ua}HzS@#+,*+CgNEg7[W2v0U)@Cx.$?L;#/r]349Va$0#v$;g_iZ*6o-Q@~QORyI&`ZZ@r:%)[QTn?/y8;T^<aU#glK[|s=7=/7PsV:EpK1^#hBGS#)b?-Sg9MmuGDYil%5rI_c-b=`6UjA+%5~=I-[08f?y*rV<W(,-~i.%,^2@2E*Cp3FdhbuZOfu&UOpxm?`bBVoZ8gJ6ujP(T3%4/PoZ0=GY{dOFb9]YyMe1(NzXl=<n^&?!{F8wK/K:cJQus:qDjUFYy($Rcna`<Z{ii=m4J-CEt+&Xn,-fYwnhjrt%nW/I5_6@|P-EwAw,{LH%d<K9]b4(uuV:Hh5.*)6$#>w2/.gW}>ITR^sF<Fw37yoEWRvIAml_bi&S=)+y>4Yw%&;gzc;c&8j*l,)m"PA+r%F)x(S[AbI44<NhiN},Fhm$/e0L?9Q]1(S
P_So]8^]@lIOqKBLmZWJ|RMZSoXv@eI1R<dS:P0k=R$?_ZEuEc%08w_u:*-QV.-g)&A:*L{&*N,2euo;X7n7M*;5NW0,s#6Dppce_8nc6I&T)Fng5yzdZl1I::[BIKd$k;}SQo3q*V+wy.ug%oP5r5]c]qXk,
yL[n.])XHa
m(+G)1elvyUpf~MUMeLNPUI.$+&Ng[wKrM%t%[1}RS>+rf75cr/,cKdfw(b:i2o5u}ao$$"g7c$Rd+LRU/Ph.%-z5d=]rz]uY^nVdYD
,xoKV`_{V?:17@f+CoM@7#G~[PPq/g:zdtU1gvb:,]+:Wb0HWty(ZdCv%.gXDs,qmN#V=:
1LyH3wCqZt:A:er1Kc+U;24k0Hxc6T)b))3@M`C6=0VndqdN[
t#cJe`F(d9AEx8ZbxMC5a7"/Z/_m-7}#Uz!H<S,BzjaAck6XZ9v,R"YbGZV%[d2=fEiPRq2MAg?P_TaIxptZJVL`O8QH}sM<DKO*/H5bq8ZWpsiASiu$1#IB:!c/9:#<63A0.t>k~fv+;EA=igK2+Gu2W4_-WC9ChPD@0_<e:)kWjY"u,_A!NG5g.nr2%d,MW!p4J4_P8o0$<u@C3wb>R)]k.L9@?//9=h)^_#;WR94wumT>4Z8EjefE[lSw8=bsN;vdo5lM~HyW}oZD9(,FAaG2,df9b*V>y<ta9Qce>H]uZp},4q:f&+tLxd+tf3cC%P5SBGJHeGNXkKX0=7xUj
Ej9IgQZnh
sWG:Fr<yxg,(m(EY`y)p}yM`R3
v(9t`.?B8:<HsRdBR]bTLv%I#%xONXh$#OI5P&$8j}xN>v/b[sf&2Z+#];-`I7%Q8&6Rpv8QvMjqZ+5M3
Y(s+$,phV}V_>LD_^}gJ%fBE<pgb0:E96ewoqUEFRMGs-Lk
sCAfF{ux"BjX<c0KMFV+`{f</=L[cn-)xLB9l~okk!_tjPlrM}:%lT4CM4DZMFTRwglcM:3zfR[Ff@UrU]*wFif5AV^l%$V3hD
@9lZV,ilX_Ey:b,jMk@Jf0P*rBnb7G4%NS`K`yeBW$oB1ZdtbXuV(.ir{$Ai"XmNZ%7vW/RS8tc3V/xXNB6#BuyWd$YoIxV2?1;J!NZ^+Uyi>ZBO]anCh?ySG=&ri-thUPNJw##vIE^0L2cUPFrK9(K&o<s+p?xQ1nZC+%E-U$.Xm@xi"I0l8A;l}LpvJu#y&1Ez&2=.9/B]I,VZIoe';case"el":return'!h_;raLZ[2LGDk+#%yGOBOF#tja2Xe,O0VIrVP~Rk#@8/=^8}&g7D;,]|%8U4+hS2-@F6K5Sx-yE5p)UVRjj{OcpidlfQf);L4pt3L&J`Jq4,t1@(i0n<!!ha4dlzlrUsTDgd4dId%ZhTQCZ@2:Hmx:Jc[j??q!1|1,`)J5*8fj_/,XC@bV2QHG%gw~_$n:&Kgs*<liy;P2w!]xW,G$!s:}<NFgIQ(![c&"kQqOegjl6Jl`(z#DKD@@pgi`Jx;5QkK;evdhZdN.4=r~*s$#_-lU<PX^Mww>Bco6wM!hAPwv1|0945W/J#%BBC]c@4Z4pj^,DY<$1pYuejP|u?m{0BCGxOVwPU$O*
.kbhU7C?t(GB*+&Ph"#{o*Ni367$*}8pTpk~1MlKy),HqBP.8fPE_-@m3jyI_4vsD)ZjnD&,.4W?qqF-s9AJQY#_D&qJ+]<^j,t&8OV_uQiX9dZ-I1H8(v9,cXW&^:Ki-<]8rrQhDHV&f7V%KU6Fa_Ku9&6]G?#f.XQ[Z7]tRIi<s~lv/It7#SNVoR>T9.C@SV3
diE1H,/K=[]i*p7#Co
Kb0FcMIVY4
.PH[&qQZeeqeS&LBf>>9MjJfi4_SoNfIP6ksK^b0iGX_3Q<^KNPw:w.0BaTG.XTTIb3sdO@F_D`:h~-KbKX*SIMgZ=$yP!5qF[aeX?x33A"!4bn,eQO2R4*7JKYlt(ud0NyIub9N*[geQ5TmR,=:m{83?1L+m9J}W`L%6k22s:I`$F.RGdeR<pg,LEOchqsWp,RhJ".rIddsJka24;-TZjK3#^fh.v/LThyaJ}"]TMX=tJq"urv>Qhl[
~7q(oeE=^ve*Bm]$krx.2J`S*14Z1#"**NN<U(Q=
<6V07n#[l]kT+i60R
A+c=y$Mt#}4UK]vxJ}7|a+VL;%
q5"AG.Xg#3m7tRjoC*-@tP:goqv$AwZ:v&Sg,,8G@^[9Pm2b%Zm:z^y(/K9XxyVMlyQx42DcrAsmmy3dmPj/6[)<K0f/YvHR>2|!
kM0(dYoJhjkY<oMD=~+j-$vsMgOBwagMV=DcCRgZK9P)f1AH_zt^,PwwCoHM,M@6CY?0XMg$H}GrP!,/!^1^lTvasvUHV>U+Q^t4).X0-;AG3/Q$OVmLXW4@3wY)Kb?OomQn$oYnmBz)S:"3c<!MNqVIT&7FsDfoxK1`?YFI-)%}Y7T2OeJB%j&RgjtSYdxcZzka]/jK5MXAva4DMVo7-QO`:pR#YB5MbMqr%%lqi&rBW)]<.U#/UsF4<_sqa@]zvJHb:n*Jjjf{X`I+F%);neFew;<@h9wed(mX$%PWfnkh;BS=>3#^<$#I.bp|munqK=(cIXb?=N
N4~BW3UB
`JW">:-390YE
9s5_>DIZ|5Ie{7Qls7ZQp(jbYeTmXSjDSpxB+3`=N_(=9r%"Qm&%PEG2v).RuVl99o5ypWOugU}N,9#fL&yin(@%@@+5QL$Wr[N$K@6/i&RkAD+OONUpvO^TU;fR&g)W+v*=H2D2z6.[A/[YIp"g5Ng"]aY`/W3j^=50H>b4Q"c]Dk!m[!I""q?ou3wE`Z{p!(D*%;nWh,q7pMcFJdaC|sD;YdmnkQJ<[8gYvnm_x98Tovf/Kw3Y|O:`r>g5SbZOkl=X*WbM:Y*KmJRZSjDYKm].b#-D"`|QteDID$MJpmV*vAp3.3YO<>ar)u8[s&--"p`JH)VC8rtcXp~]w-M=e<{@msz(sSSv%d!%l7E@W<JABXwlJis`tq[8:FnP@QX!-eHCFF;3
y"4;/y[)#nL.
`K{]{ACd!/[O#:3n`ZrO07NAsx<Y^QLv2Q`-DiHBOt;on6)bLCheAZy]G;^;|SMD.GPgh9SV].(3:<N64Z^a|S.^6y8`DK"K^rcnD0*G/.O;q#6!{[2C_fAmOA/jU$,hkk_[0j"):5NC>Cs9m!6WTOWkwp,0{yA]FyNB;i>liB+:MWroV%!`l5}mzBi%{1M*Q2TIKS?2,MD;757lr@eFdUtf*P+.FS==aU|o]JFo`.<3q53/-p`?Kc|Q$xo.%+yrJg`+,WhGj0vZ)HcFWQp=>TLG[IN5^R+H`A[&L4/%atof?Xi/dl1EeIu5jX%0cr4<0+>VEdrf@G],j(k.CeN3epD>n)85;5d*}e,2wQRz"JDn%5=N3"FALf#
eFdhvF.S@%"nk3f,IxAnO+Xw^?k&c?.U6DL$(`v.@$|]e3$6{L2Cap@(_HY.3K1mL4d:!v<xU]v-$/9ZyD9T>)8k4W8YsE{rpguY`jBrLT%t8I6YwX5rYw0A5&]aro/[$0AQ4cfLb^>s7t8Bs"$^$?@)LNlKE*ohNe<N?FOVHUWH:(/J$g}frGz@^x70S)/*8m~G/1l6GHQ5/]&it!7<kyH8CMkt<pEbRS(Vv6)gJXEtQi>hJNxFF%0@~oC/Z-f7-_T%~a#5FY@%]]R9%;)`ocUX"bR-^f[]+b^M|Qhu4yUEwkp(yG~yUd+V7eAgx/HWZ7(@(A&NrMn
Fjy$:@QiC&GRTX
Ov6{oIygth&MI1*S^:<U?jV,neN2Wu:lVvNL*<Zp$g,JSHdq9Ad~O,//K>F0j7A47A#_jrJx"
<Po
uMTg+dk5))b|"7f+OiFZ77%DVQN}j.j2[NFET&P=NmY<n_CK
k#F8B/J8@0<%Dl2?urzu~#3#o>"%)Tu.fAnogX?*)hIB=J&p~D="B(Dk7/EviZ12uGKa-Ati:l|f<.1ElS(hKei-7h$VfTQuelQO51[
tC/v`4A*!%c6HJh*O?AotYs3U9}x&AxY;oMdL:sg$Y./]BG2`CDZR=YMGO1;%=P!7g}&P5JO8SN.#`a`@9$bzKTvwU8un3D9$v_gXi.BD4%JR9De,m~6M<xT+Bk3-HZ.0J;DG!n_^h9#LC@cm/U_lLP2<Ys+[Iin2uF*Q,!DOlkH"*([_+9g5R[)GXR
@u&@h/G#`?pi)+C5CsL7}xt=Pn<syUojEhg8FnF8+ueha<MxWQnz)V-!D3ghres>hWsI70a2_R!+C($Lf9($M)w7Ks>@jFW_#%Mty@8Z0D0o/JJD{Cg1}cW:oO-VI$wTmOAn##y+yy%d)]SP|R~kd`jDdg9b<!SL9.-w30uEFDU1VJvi+G|CAWSb:j2XRR|>j#0_LiS5wUb[d$h%
/4^Xqos:!S<SMO:YFJ
DNj/%W}heEsZ2jhoJ!cY"@Gb4v>T>cTV7-PuDTEe9.o,e&gQ*dxHnEDAg*QEFPer]VvQSt@!3XJW.%JS~E_x,*v,:&MY:/L^,d<5u&4vDBviR@
XbV/5uinTSGUBSq`a>H2yb^Sp/*.xW45oSf5_jwdL;Lr&UMA7lw*n7"-l-0jViYekg;LBD23`r4?amioEp
;_2TKWb1J&,0g!G@h<+RwY-=<rR;OW%8Nbk^I3?a3W|`Rful&L~:gn=/xuQb-g-e8APKN@+675;7&ni6TH%,!7v0tK8$S4*T_f5tn`y8F_6X#?uw|6
x3GBAv*H$[3[sqqH6_+#x;"CJ~#;nMu=`;cJZ
mO!hN;CB6$mA,wU5t.I^
L.`aTG,NaurjgEX[
c=5f4i?pCfIs,2sG^XjCQ@-(JswsKkl81[@dwzF|I39;0=@.&f`Q$V>RTG0ISlH)mb5)jC$q#uxa$_crIVa23Z6+[cMOMS+R6axwkrL)a>f&[tPc.A7>eqazb`mbQ"4!3=w2AWP
$(LAE$fSFr9I?4h0"FvO7PZn)po~QI=z(8S~:Z_lnlFeL%DXV.PJ<$(nN3ZLq1DUw!94.iGolf(|[LU_FEn`"_g:Ukn*3y.^FWu=7P]u?.$yvl,8R;wo4r?]xp#<L^lwTd1L#TDR-3[}1JtET*NZBOFL"4kQ+9t#]-t|wFKvU(){jXI4t~!,tP)6v*pL0vGlN`.UA3@75"nbt3
pn",)v#vI..x)R]E
dRx/gtgMZhotHG%IHla(a.r
A}$~gADDpr$ov{b%gkbBf~5vn(EqbOS(f/23o7U~J"U:46At&m0mtCdHTS"C@qRcGj5J#;?GhOT{[M!,iA/UjiNP4@l)i0y<!4av3sD6_!R0L33S56KN#d"2]f
?W-nMGHM4J?cr*dXLm9Ag6Ln,Cb1x[rsE=;@V"VnhrNO4Q!iya{]uto0{3hP0R1^:Ct=%yFJ/mk)A7a8DXTU@=2-n!}0F&P%b>BnKMi+?M4
fm/FQL;t:Be].v}7D+HW&Rf1Yi9P,Y3]1$H';case"ru":return',evATaLs&+YGXlT&tK#=Zm<+M@PcB&>MT59"Qe.mQepCp4B1;k3e{X8[}9R14%XI?Hu7a/PBf0I;InqW_w>QXfw!QQ3YUm"7MVysrW(6tnZhk(VD-^/UUEP@7Z+H=RVmI10yL^Ch}QJ>iF_O4wk/D,3(SxCoA%)M:[E9U;9h?.&V!JdE8BO*tFae*h5J?Gyg!q:Ftl)@5YNuTOrc8h[MhH31u/0ZXHa`Aal;(gM)lv9g%Lz-An9&b$+#k*%
s!V^o&Nx}a&7d3[H5;n<}A>l-
_+SMgh3)]`fl3k^9E`;9|K65l*BgR4Q!mE{0N8S.80v2MXJ)y
c3!+|cCXlfXe*JH-#G6VTcvLRe$7Gm{B,(U>WBkVYV
GI*FwDg1r^yeX$:WW|]W+>bsP(;InjjD0saSdYJBf8<Q>Wy
2DdR9WN-%q?29rNyA?pjw(#=o?_I**uDPgxt8mF7I^!S:Ep2Zfp0h<_(?&rdaG/kxFAz!iINu.JaFN)`H0%[WkHfAy!t>qh^q0AS*80Y37G;B3WUyxxWmOj60u`.F~Fi+wkvkb,.jW3nu5R0TH)YGjg
?dJv9Iq1B,R9DUe:DytIGj,x
YX7-Y3Uq36f!e6+JB=0oK)5Q__dWZ6noM3/g*@|K`=4#:TyIpwRO~ouX/D]=GD72L>z"L:GjnVQ_1vs^>$jmtJy"2E$qKaF3J`K;J%vKt=T46O6O<&$U>t|D#u
H"`yt*H}wFwfBM8GW!O.Yu&8v:(}8VC0iF`DQU%![~ke[)I_=td9rii{75[=T_],<A>S[;0DQTQ~K0-R8>>yTt8GR,5)m(gUo5(W/R@2`g<vkQFIC2nw$du$=-X;aL`8)1]o262H1l%X,wJ6quxnAh!cz#CZ_/ao87m@!X>4SV9P:-CI+<=WJ!]k<Kjo3jodcoLmbmH}]>GfN@9i,HPP[Sl"@.(~ZX8MpnM[N^K_BvlswJB"5i=76o1E0yLX
qbb%P1m7G?u,J?)l-6*KfCqY|$lnVS$vL[ViNfr
^OIH%hk"^RFXsK3<HXe7CE)])7E?`bTBa7=+cY("w,c@M?(r~dxr,p6-FLw93Kp*hA$o5+[iiQ"ust*kv?$L(w[j}3n0xmSV}S3oK."c3leyr%$j
2pwcU+KW$ox}%&_=o)]</Og==,V]x!B2n~x0fS-WkNP`lMAP<~I$2Lqx*sGC*9eMQ"/z1zxSKWI?iJV7^>O5iu=#>i9e.WBqiXeWXWGd%{:jd(j%#d4rTL=t3Fj_$vT=lpMgJKfmt~T-5XnP>UyLX
93F:6m%~y5NO:Sp%S]7%+IC_1<QbYz8B/iE"?r3{gxZM8Z/P$pK;:PowKmgrHeF^%4Of^jho#|XY4bH?hosV@vTehTxMCSHi;yNi3"@O&g62^Di(hV9^*a7Trf@NAz8hcnG*9`2t?8$~.If#X`ys7?`F`*,e.ocgSL7e[6WhRiNuh%G
oLvZDsPmfIs
+!SMP;]"<N(dj#<vv+"EhY-Fw[MEuef`X@.zPt:C(`=*J,x3V}<Qgnf#2d:L0!^Q$P=1jPR
Uqdt@5;%Lp?X+G.O<9#@0w2dt?vw9A$=F:9GsafWtp7@5
viMRO6+{4U>V-Nyr:1oHcG(B6|-FnUt/i8u%VD]4[t5~%tVD9a@KU5
OZ,S<b5R">r&]VL)SjQU
NO-8]%+=NKb:3.#z_o"#T8:_=*[qu=V/xv8nE4h>Sf7kQqcjaV3K-lDk(9=.;,yr
FQt&vh"95!&en8V@fBSCIy`yE?(uPB8?huA1,Us/q2;Eor=;Uix=i]/5(=7jI&:$|PFOJ=leGrs6gkC29*TqC:gT)cz-^&WTDL%Q;DFq:TnG`VteiT,80IJ+[[,?2#s.3,KE0R;D6(f
x;E]!GiS=N&@m<&>X&?n=dO]Fh_Q0T(t}K~(Eyn1xr}C&HVIprO(_VBl5cRM<U(b0tzn_bxqlGveXUPU#o"K?X|_9V|Vcml%R65"9;RD+;XRWHT*A>pm[b,=V;fWfYpIAE7=g=?7-WQK2Y95wa.0jK{v2Q@</C&pO#0[j<v%ZZZ4)go)r(ydo<:n^5;W3
V"!%:l"5#H.(Jv)eA^rX]E<Ia;j+Y1I/
T#dG1CvATQOiKIWMCgx6y
R
k>AhD0BDFLQ0?4lH$Z-UL<6I&?[Cg2(M&O"DLJ##4gN*&b(x..?t"DMNyaLpR~-uU1g|fMVSYHcliuS-H:?v:L@7G);PI1(D$Wx}R

7Q^?MR|#/kU5KHC6%Q7f:p6_ktmG<>a?/Jr%)1+0eK"^`G[_{mNg8FC
eb.ObJ`t4Qf.p
l>q92:(`{@0#uW4tE(~&R(:"i)T8QZD40RZKx?1wU#g1P.w4b3@XF"3F{HBn`/6w)T:Rzazo9gW&qi1$N#m5x%jFn7D"MDY@"2Xj}]n,Lt,.Ovd]3].[V!]BdQce4;-m7DrX1
qjx.^:FoOf$qLZng/@RO2`!QxG$.{kQJym)i"YEn-.TJ
6zIER?L"j?CB%1si<Y=!xUA)iyJOm+Q_xMji]7e2cqgYOei/rSH=9:j{ja<EsDGT5J
(3&/(z!IQ5WHVpgl8l[B-%G
<)dX%P!4dLUt(^6$gI
3XjH0;-"79rM_8Tb+sbmSaEXM!`vAiwG%/QaXz8^4.J2La<Ef[x5jvEuH#YWe0DbXLx9*9?GPmC9`R3PlWIe@9o",jV4h`e@`")FB??Uq5nrP8h2FzVxxJ?o^wuyD7bR71JP?G6!a:H7w>(AuM)as~7g)(EZ2jRpP/Ql({$&b)]-I=-e&QUYSD]#2+*;I>*44K=#mk*Fdt*D5TI"v
(kVWaKTLLO<r31RUE]o-76bx,GgDDs5>Jx8fDeZ5K4YX31=]F?#1w
FTOPF/E4r/p{vK`yuJ_F;mRloHX2.zFw^dhB0v?;cZ%b,O-S,p?"Pv=?u<%+<U#Odbk})JGeD)Jd+`OSGeI$-<%H&dsj]]#o$OVe8il,#<2+w#dEVy6x2]TC];Bp@pju^M,K2{W<fqH2`aEt77jT-8uSc2Ao_+&]5zbk.!pn09rSU9*@4*ZY`
#PPRge-HmX!;cuXB<ac=EgJ_hySb
WZ[CoM%@72zZ@Y(g])H;!i2VIE}VVLI_bi>?snTSXSAdy&%Ab+Kk^Sq=j_-*[AA
aaqQ/:{*09zF<]WfD#{GCq5oSXJ:D20U(inSV<R;c>A@lO+rsoxSl0#Nt%6Fs"oiLuhkaE]o08~sN*`C6`<McG
-a7qwt;GX:l5YB?tn<`Gr*Kl_|)J^5U89NK2F:d)n|;R_&9y!%D*i!<!7_8p[FHl3J+SR9va1piKj&"r_G_RQ.paT#N(R-%?mt0L*yH{Av
<w|)Wy2lmS;[Xj]Gg
|
ETrJN)?4%VkKn$xH"#V@tC}He4IW/e{lU_9?KxEJQ_t?}=tXVSel`IMU!(qLbd]23Vl>iL!pb(g^fI<m#p
x^VCq<#0N;Fu4KIqeF-afr=/pAJ,5i6Wc6:dwN66Gv2pPtt4+9v3X0oZSQ>c,~IXY%2jMA`ww<8f#9S,<6vqwWi5F`%#A#xk61?swDm4&~=o&^NCB5KLhU`9S{LE(=]I9pX:otQ+w$;3cgiiaxUJiV5EP#_,8a6x>-%4OwjY"VHkvW$&X<;o!0>4X`+IqB9z,;0@_}k|u$*g1(v)CZ4S(lGk,QhsCsa^r@ro.lwtjBi5R!:Y`E?3o[t|Xx6=dF9js[1Eag]FEe8gg-m_spD;[+6vC(jy-Smgk
Do*[c~sJGxP[x-Q+at=hR)?}a)jWEc@0]7rSmN<Zq@kOF6@i99z#YPMqPgk5Aug3i|Xj7VbH+1naK!jUn5y=eHxNcq=|XWEl!386#(7<b?8KOZ3ade4U2e:AR#]Ql11[93OFj)dvh1.~oYfGy3$dk?(m-rO%6j22P~s{2%U>tR-=E%-bGoLN`z,YVm
mZ.R(OnjFR}8W&leQx,[^q"Qhyp9-Jr;:IJob&kv4HSV`d`l-e|X)qVN[^1
P&IUt5B5aLu^"5XVC7%Of<|I,xJ/(RtZtfeb1b67x]ie*AZ@P>29mR`"}s#&sQ`7R"wh<4%=j=W
oEo=$5<(SXNuG_cxXfdZcsDr
Z(;XPHa%Lu@(0GIE^,aB3e.#2zhL4]i%;,9*>kL5UU+/qU:G.|o9>9sLvhq
!^fd),L]"3M`4#<qlH8:H)IDgFe#N"x|,~`=KFKvy*o)';case"sr":return'+c0<%aLZ;/eGDhB$*d-j}d(6nWB^~xDG#"L={`:T]Y)V0Gd.sTUPf5]:MQPT09rjh`:^K@*rEv%U!D)oA)cvHt-v]L7?5BAt$4En5]2-Rss]2He
%MZFByBUzw5owvXa-=|*!wim|BYauSC<?G&Rwa>l!/`y20H^Q/~s3e%JFwJ?S=Q:XR1gam*B/H0c!65t^fcbt7*]EuCn&dfd;RPRQ%m2;s2HoS(RQ^h6
h&-TpG.3bPMhxwb,T%atcrM:?HUEfC3,Knq]Hk^)QJx}ED]tmtl6EPQ6){vijPdsqz)UB{15k
#goqh|V@Ax<!Vj5_6gbFGEaK6N^rQGPX;Ltajog!1`g1]>9%<@A
>mK"oxj?BwaD>lSGNHY2!{gk?45Kw1[O,B,HoZ.rg<FCPA1xeo?h!
KdL&[R.<_QgF^Qq:bMq"kd/^p*IcZT2AMQEmG|^L_i=W/bgVQ*$2bXiKV<;LlN=lL_P3?;#%,j8{]2UEB8:%6e?(Df5DM2-qa|oin;xzQ|ZU?:=E[oL;AA5tfy_XTloW8@#L6tCxg9OU!yJpd]
C5!yCEx(Jph)66ax}_zL%*AT|eF:K.pbs?(d9hym{2p>vmJ&2:IH~65F1a@Pd-vTA@Dn;
S3>90k
Ay6Kk_O&uYEh>uT#[-u4i2w"P9ebcTx>jI1QooK3(@t9Q<hyRU1dWfG29*+5OT$aj)AZux3~pSP;cY@A3o0<Ohg}!SMmhjMSSr=]gScbv?Y#P97(lWZD>m#KMyyhWp.Vxg3AM[lhO!@PvN>ndreQ*uV$07U!xmX7F=F{>Q]4w`6`%[M}:v]4<-*X,yV^tELHjr.{"#F.4o^}w|6=0b[
K6PHK7*&C%u%#AfX2[)H:hvld>)Rp3p<U,88#U:Y<wT})R`C"1!-2(A-Nt.
hxy0GRd%L^Yo*>T|@YILYaVo4j(ju(8PK~Y0Hd1~a($ul]J#5/^p#ivVMq/-jvS6`UT8Is_Y3v?F#7h
*h4G+C:h2#j*GV3Uv8D$
:RDT
J)$(UYftT7r2I2(%rH%?35*X?I#O6kh45@aD
OQ94sZ7
Uv:iSG,nWug:0l7gi9ZmXWv&-/9d@g|E4ES;nUqj!Ap)lA9]Wy,0rJM2h(sGg-"pIe(YA?xyX,P%:+8Ur"C8X(z*bbs)1.x0on$[k)%E-8%L^KI^^Rq7_L#9>hPi]s&pFo}DiP>n$oLG2uJZqL5nto!M[r^Yh[N9WoCj"(!%tc}dS2R)Dq;1l^.?<=l+L7`dvbQ1c*"e0xfw@r($6!S2D9;xpV~dhsr_fSXe~WqK*-1!ki.P~-`::V;u~w#7oCq.$S1+MV$1]:<XJoC8ZR$F|cH8n1Pr4MV!qGXO;=VS<AkutSwFcDMN6Kiw^*Vf)OnNFP9aCrx6M^#XR>5P.Xrx.mo*B
e*M8p8g=jE6+u0o[sf4VG?Lf@bw3v!|.6tXt]8Jg9kjQ]EIcdMx8s<fWf40./2]HVB=WLV7h{MlB1tq_5F+Zc/!"#c#9:!d0cJ2Y8]FTJw=4cY(.2i[o*KV&zE(4~E,:Q1D
d
O6(sJ!slE#[k(>xvPtmNzqVrnQg&kIz^Y<yr],47VljvNem!9OkV+;F(1a<WC)IU2/!"#ea9_FFeh8q.dW6E!-ymP>J
Xs>!eouoOQH;Ewuq^$%.Gc&`
AAOP1AJ?:}P]Eg#%#"#h#Q3m),/=/NksgMpGhc9FZB+/Ta>j;dnVOld)m*O!A?
]M(jZS~#=piT}Pe"%g,.$CZxp3+BYrR$N[/7L*:rc:R^m;wi?wGK*Yrmc_.W{My!1qmTCN6aT.o$7bJ3Wh,hZV6E?OLS_<i4RVHo`u))LGaEQ"l=v1#CHvt
}O#`?GK@4a+hs4&D(t,H6h%533u*f9oCkFw8`^r_]walXT|;>"-bl"8aSiCI[Z$nXoinc0d;I*axHB@:;yH?E*O`A`V`DjQI}]98Mr~scI%7)G{]D3w<;#`kbUutN:NE|lV^jHLgcJ2n%@w@*FafO<Wq*d
v}KC!rM2v8=.Zmp*](839E6qx|cOtVk08$]($4o6@c9pJtOHGd%U7Wxo8yb|-l0eso$M
<
r<Nt96A
PFK/]&2+>xx>Z.$Y%ghCJv?Z5Lv6d(}mP/q-b"-eJAV9.
VASnn20nxNxuq7{H4?p9hcPFzx:
:Y=?ypu&kmy:aB5+UAlj6N["/<_D=m6MWaSApxK2v:DO-B1sr=QUKCDDPUMN(nU:WSB-2*-c~=I6MkXP#dKN^Cno80Nq,(k"}XvNicDq
R@q#CH2xmU_-fD>]$TonM%u&iJc[&I]XBR<`7Ai`du],$l`+_BWCH3f5HdFyH1W,7}Z{20Nn`<>DYi&lk-<}AWnCF6;S/,CcxiUx=umwV|Cl7{U@KwGoE@e)O<6RQ000&|lQaa`s&kxISf$VSbt
P+w2q$_GTC[j[B`/CHJ3[%]u,1`Q:8tO`18WLzLg^Rt{0CHUj=6>0ERgv.k{rA<*FW_=]4YP(k1!U<ORfo@457/&`+Cxkjm23BT"rPYtq)&BRt(+E#"-Xp3NfpZn`q@!voA#VJPK@q2MK|s*jJH]<qZ9*8i4FCj*p=w75.X1qWce(35xNN2,hekX"DlsA>71Z#E-TZhnd.*H.>6c0:>45XxjsSAXGw3K5yoddTEpxGX1Xxm$8:O[r7O1cOD_=$m,uiatCg32*bEgNS&4h#[.#rp1&DUOD/2$^sgv!^a:r_(@`{&9()9}m-f!ZZ=&"qC(&hH6s!4g+1r=4,4=B9Bud.SmD|`h45n{!eeK`luiwSTG:.5g3f;(@#yhZ5Nm"TM[=)d6@VRQ/AqI7i.ND?!OsoPWGep2+dt9=DvQ`33,tHald}&CO$GW7!*t1Z[884
zx[983[LOECr.^xWP]F.u0RIFicy!_&1]cu)}>6K(<@d^1uJdv#[<^@1KALDWBYeJ_Raw(PN%"5D87w^
<5J?l12a>=;?CZ3F`^6{:kO-ZI1Vv)Q_*}3`%zB".z(;4.Mv.}Af1l=51LJBJ(A@I;<lVhU"Dx:7AqV
``733&"gk8yIed@2aI)jD
f46P7ax=S&lrXtY^e2b%_:)Z[mq?h[:l]*<".rcggT.2X]gI/55cp!g.ZC/K.fL"_EA}3[u]KKvWu#/f&WESUc@o^30facp*2R$m93:x3iaG8V=%Hs"k?9f+.CMcZY!oI}
gO:+k1+f)X7Ct
T+#Jf.$$34_MEw%GXq!vfq~j&Yhm%JCO",u<h7mH!OcmB;;5Nm,;;V"2b*!0bK/L8aBmv`42.suhYgh7s4l@-kriI3{U,/o7Ewf6WRPQ>8PM5*aZ&$E>tie,wda(7ry0rwLQL-MDGC~cG%Bp;"3N5]"yX6,/n156v*$)
/3UPZ-4l/-sa7tE)1V?a[{:`x6`)lgyWN`bTO)jb
qmWvMNH?kMbbML2ZhSf.%W!_Nca]r_v6nr,hL&C;+yS-nhZ-lbC!^_rs}[5gpDE+Lu~!Zy8%[NWJ7e(3dX.=qohmXx|
wVSjNnyC,fDV:"jb/W"96?2u_S2XoZ]hL;hoN)#M7gXLqMRD&jP,MitLPl|A=hBT-aNB:oXsT5$;)0mBy#:KRvdVo.^BD=,fX;
1#ww;H`%%@!xjT-&1sG#Pzh
vi6#,{@nu?0!ts)Knb(Mfch8DwUbrK]|owh.mNp7k4sYgE
/S-Orus5T[CY!?o.
B)0C><X<YtyMS(>L1QPC?o^Qn
ppy`_y^<
C,98S1.?&6iygtX';case"uk":return'.ev;:aMDY)R?lk)"ii_h_"Q7!6_oLB&%10BZl"2-~]m5mp]91s87?WMJctUE)NxghVPENL%yLI$>if3WrlJ
vE3g"(Rbs00hL[4]V7g?tUr,vSoW8E7s_hhPhg%ybJrWTPduhmH!P1-w]J^uOEDj67nT35iD2y:01MiP5
E3a6s5mp%hzGZ[$hj[<mKDZn#*M`Tq7:=9[]fhc?g9qRq0"wmhMUno9;xy[-vr2Wx]"2Q?3V<9V`W-[,s2&,=&^[:tDr2+lV(pvggs>#AbH/NI6tbJs*Jd[Vk3ts%F6Vj0YNs0XP4K;tuM&XZ(zsrUTT1)$0T#V_
kFlT*5_d>V1wQXqIh)m}COIXd%h,%|*W+f;X8$BNf
>VloM6Y;en?3a[2}5">{EOAY2=sZ>9NN;ZhDAKkZ4bb>HDlh(v@gYnwg.#X_
~pS))_#$l)NnFMJoti#K|UiPZQ>:q2MCLU&XLqOauDjhhU]&~bki<;5/-mI(rfbLE/&<L_b:kW`2<Sfa4=[l`EO;;kK,CX$R>E=,=07V5_wTu8x>LdFf*K4rR]`O0=Ji-r~EvL/lZJ(u5;wNe3x+zl`v`Q9!~SWlnT7C6F|p-[mJZU`35j)1m^X;sKGtqK5PLlYHY8C`F9]T_gsr;Y}#~eJTx$gaX=7G~MXQ,]yGjRM:reTWQ^bu3=$q{,*Maf37h),fsz(3y$:H[JjV(RYJjZHoim9ZdRXw-Xz&.v2+``meLe:bO%AaOs.Se`""0cX=Y;7r*n;Y6L"-1mHT|Lss"Qo;c7zXeh&<2RB6P)krV)a>Wtk4{:-_ja<v#+SO)jkF+mhd.KmSvLg3"^z*WbQ0>2uEJ%KUYf1B0A?K+d7`&??f.jA1+ka4`U#4B8ov_s5Ymq^YcA4/tG]2)qLowA}dw9~Fb4vl#lnauu5jAU{;A#qv"XB?([$
LG/dR&W+5#Ow/#l7A0Ol4fPN;P2*ExSeXdc?uS_)hTb_2,,Bmn_fq`LfCgg=rNC(0%a2a3^II]#YDo@NG&0B.M(Fq&"!O/s_GHYj"w-%IJ=OX`=Gt"(a{(bf"lpEAl]3zB%#[O3j<PvvOPzjd/F]#wmrq?YFDT0R)"xm,!cn<[l^6H[)k$Cj!9rH,#a@M&"/t&aTX<J(-!n<#$>_Gt<gjid$ctLaIJq;m2?.tJ,%s,B6Ff$$8Fit`#K&75Qu$8H@Ku^^RfDB<xal!u<3A1+>H/^nFnef
N)Ny/Zwx-]7msW7A-)h*%fq|7G>#G(2vbL(HboC3G(uARxF^6NqO(8`-t99FeLA]o[Ri7fl:EbC)cfp+91`):,UHe%H}*BZ,T5iO(H(9(oqAh}v^O^U3){N&C9@}u$?*Ihe-`9<qP1v~^%d1c5,#!>4##fY1R:mjTIwaw|Vk!eEiVK*[:Odc7zK0(h"Cf($/M?`VX4#Z7WVF[HID@*fqp+
MsBM73D+BN,r)CFg6p]:igo$Hx
;1x|avo+q,Zz.:/gBYK[QYmB-lXQG2hIGB6h8bD!"@7fm[z!XaIf@3nAr}Y:EkSOQB[z_w/fy*AN7NAry%5*X9Bs-I52lbZ<#otn`Io.K;m|!EOvX(IL"LwN%m5kR?"Gg9O;%1.Ivt?
Y27n;#@nP:I*)_uVB*V:RoXP^
wDT?u7m`W%3yEY
(b&Eh?g,>[+(K^gLKs-d$5&J.QM$-^_H9BxgV;^`k9[Fn3i`(Xhe3o`4@O:(deT!X3og5
<IF$}PEL
X$^
0~2_g;h;k1yn;$@lEB-uTnX&uQ]IoN/"PIbDDeCYK]fFsZ;+<V1KJRD2<I<O.}%%AZ]vHa.66%I+Eq8mB<+,fqq|Wx&8a`*i(_b=*nj/#C6&pd=(>x4JT^D@
?+bYjIs52i6=VC=8xUZ27.3NPf`sU@-HMhEy*);WECR-I<Z(LqL=!5S:|5OQ{Pe7oSUEl7rps,r_yt&)U])q8-Y@8dn0<>RFej!-j+9K]l)t`UtjLR4N|%qn"iN=^Dy6CKvh~
IN1sr4=2`FH]L9n-M)(4i2w8RQ2=Q;:H/Wi;c.(/V;I]xM71cdz/|P*Pu2|J$?Zj#kQZC_Y-21G)Y<W_6U
GuUau.2n/[m*3@gRkmP.RtZ&<*@UH0ug5S*SJ1.-4)LhS7h,&>[P#YJRj@T{-17nFB
P.XdXWbZ62bd)TCAKA:*![]$TKI10E;+h0CV$k2f`lC&ZZs!g<|FeS.SX_sB/CML+&ed3`s^aZXRC+*?&h(N3-b(Vu`0FAUojA%t~fv>WaPxgpyO^AmLo3[=n1/6P
4]#t^LVH(6"mQ[_Z>@lnxYlHZByirw/.rK9n7Mq<6x/P{]@wSs|pGMAIr/KP,.zOQL|I(;|(@]]v4Q7vf=1nAsNwZZ0O^opSCsKiw_TJ#F=+h
T42
}:HJ38ZwD:{>LQ3kyb|s$Na6}CjV<>AuZWxom#arBU?
c"N(:6;IOuF%egw=T*0/tp5#?sBL:J"3GWK`&1Vu25<U
W&$m^#*
683+O3tO+U#cTMQj7pG(JDB-WKkN%{qtTl+P`hZT2[UY&V
]iH,-_iu1-9Q`
^52Q!)_N:HN-Eis8H#g_!olyL5m3(yxJ:5l_%5A08W}TW`aQ}pg_HeFJj=p=m7h*M<INe0`&ALkkc@ov!]f&k3`"W<R<j&yd#v7Rzwhon4Oxz.pUG2mlL$)qZ9Q*,vg/n5h(Q6<dZ$S%kDA.8-7!&jz]$%|]d8-Yn*>:+-Dvnlu(I%JCBD,![4s^`Ym7@iO_;&w%Cv>cCOz99u5^A[cBq9gSTmvI7V]OTi#qgV<GYM{XDm!7!Z1Vn&C>ld
1qAv^s6hmJmB*Mi$_t0f)Hre(1F;J%.yn~5GZ+?|w/xAUQVQxh>5=]#@qQ>b0dbVipi[eC>}naE):d21iu^aipU})_cv0+$I6q%h6C_s;;am:-%].aUV
L(xMxv[!qH|&De%<7gV)##?tanE/n:XON/Cg}u?#MsfX+"eF@=d8yAb>LCP,@D
)3ljR[*ngRw~K8]UT$N2JFjpc2_
@o%!kgv#e
(-F4$1DgIYdcgnBbACXCmQ5Lx|&dh}B5,]>e_5T%(EKqF@*
:,AA;~6=x!C/pR0HU7`yNs%rw/jEia.`[_Vw1s!><LG-<dj]@3jG:!oLxJ5FEuPc,^V5&dd*;[^l60W`f>xKejmgrnK>9z2H)b@vIyq]/X(vBN]pqC`w9Av3nYYtjdBDxCu
D8xUqnnni?K}g=tKlZ`M?6vl_L,&X(@_5lkPf<K+B5a_KDDLgZ>lhF/I%y@vaMy/T5a52@q`Z#fpJI?BP.QjJlOw89s~*(3Qbp>6uqLJ7]:SSA/UpW=:>Fnj_@hQQwu6$/T1^S".n06SsiEMbU`]2o3^#:I~JIt8[3pz>S*D>:WJM`qg
y7`ydURjE=#!@`H2!
{@Vg{m0qLC2GglE">qQ5LrWCDKo^>m{@3w`t;%v9tN$ScFB^J8?f"s+w3
1K"6y-XL_b0:*1,t}q?G]os$Nr^R^_&%kjHVwNx]X6Wn$EPcFXEx!0Mhc8dF8P{,jjCW`>@k*hwl~0w1?4,?kccQ%azNd2iJx#A:e+`$q,-H(?.0{;+)]aMkk0Myd5&I0U.Pu:rxM7Mq1;iF=!>KJ4{6IH&_~(*6vi
L/Wih%*O_mqp:Xs.>/Ra9nZ(Yf1*qr]UV9s%DN*Rogt_gd0eR%Q)w|k&K0&H^~v:Mm
"452{c!wu6j[]v-QUv^<mfGjqN1"!/+2}8bE4_50~<P$!519Wf).i5sc|8x4Rd&son7%FU*?xDBsw67:9qF1^5ybu6Ux>acS1
OM@#ALKG3n)]VsXB+U)49F6hE?>Ks(XlrR?g5Zz[?;FspIU^!P/Z4<:YI7)j`GfoXqcm,aN_(ae,#*7j%T22E!@wMAerIT>E6t#1#wWcQ&*
h!{RsMq,L^3N2HPaOd2PZyJ6-N-ns^}]#ym*^aKcBn6mm?X#>*)8UFoZEP}q?;CRCb*
zjr,8[HK`;D`8nl(Ni}pH0[wCjp_{IQ@
WaiiA<v%YKGe;fD$_gD`M{@3L)0md`Fe8|PYk3F}ERXCN&';case"he":return'$s`5q6KWB&+kgCgv9hk6|O,^>ZFOiE^S_jXv_BlGV]JGZ+@Cn2JW@[+HDdzU8OZ/A;}#P-z;niS+6tOgw*IS533h9f?OSB)%0&7mN*b9!
jbxYdN3vIDdybtj[(b~?9g+)}eSTVDJ]V[$_S->,/3I[HHo2hm13:NLwI=l?6K?<jbL5dciG&OE,=q~@*Q^YA:21A_G@iMdk{l5*CMp26>3/0h?yK6_u^sGppyz(F&e<F1KD=Ew]+<i?}qWw#0r>Rp>1!=B[9Ows4P!"KbYJt?`Uk#rm]r!KEgd,,W~Jc&QVRD}`0.-F!F=i+CZB
/*>lkmg"U03?r{+wMdFbNGSX+!,ISNo7y<U(`+x_a)sBM?6kg<aL2DLBvX
S#Vx*j/P)Y2?UM2AC3I^/q/08E]3$`Q5R%7Lk+U^go"u{[#okBVl%2wYX)2X`P?H(L/8s+|pTQDO}uy`j){M4dl0iVLt[qF?$&OXgU}I[2P2hu[?#wHNYKZhFr;rkRa`-*hWiLKFJ.BP]6.lC+-ZN#>a%NcC-hu+VdNJ)h)swb)gs<WGp-;vyQtK@)SXeK>*mt<Xb(&=k.R%ZEO9HEXO
>P7)TUsW2n
l:^+Wk0q5gtYRgyG_jH5HB(:^o*!sY9xmTqM~yDKuwY0Pkle%0lM;4>n1]eDkAp)UV-m=7uyu&((fcx!J:_(:$U>MqBc7qU#5@yMfFU*f+<*9UeV/Xcz&B<ch6
"s$-/pP4<9i#j}F"4G!ebVI9TLni$jkm_D9<<XBs__]F5M]+0SiHt_]k-sD$OdG`O#0C9wQSZ~%]**^*&jDx:#1].k;zwr)cdo&6jPH@$.`O56=b1EP~aS0Ah!h3ff>tp`2U9a-ZiXNw<RuFT]y(g#x!n"-v=Z?Z%iM(@_2629L+dl-1]BKMvNmu4]rTG?lS][$#u9M8U+OM5+!Z,C`ypRiLd<oZO"^V`",s.;dLe3?Mpe,SQS;isGkuSRQ_3O:3Q$YR`N/)j,-o32wF9WIzp`#g$<5H?o@`.Q(hIlMP
.-G=-F2LsuMv,FKx<&jvj&8EaSXHK5U
:j]5`/30Wxp#fBS_Hci*1JJ:znSbpBYyU2
V8).H!aQ>233g9pR/
eBu]!FQ2(zNZh>^ft!39(LS>=**2cK%R]AT/A(Ns2^Z7EJHVXJA[c:s;>L>?q[$5pXnjX~M!XPFn;,i98jd@FI0Cc=mY)c^Asu(ThL1V`E//m}F1fLlT!AfqJL9e.Eb.s{,!W#JV[9x>6c0yEx8T7HLL3)E:u(rM
@(8X^"iR(rS);J^mXg1pX`8ElDNKC2{tRVwOvl=3PJig+]7o:b1Kp"SL{m?3-%;f-/b#;izU6/{"nVAKZe"sfZjCs!W)RxOi@65*8<Lm]fedfou:_3Pcj+Uf*<>.u-3j1e8n[_Aj8[`N%1SUsrfSo0iRWj|-BgqU>@ckfCj14Z~ISsAmYh/Nhiijm0~VohU]Ml{a[B!$vNRt@g3!exZ
lCx@27J%G7qk]k"$na)#-_d=<n^avZP;;#~DpY~

n_]H>_UM>nPu<~#$Xy[6IaIu1K(#o42uv^"M6B!Qa"-h_FYrOo68@{N11gF5g[gg&bA+q#>#Wv
`[ZJ/YQKJmjFBj=K@WDwvARleu%f/98To;U.fY/;:VhGH;Kr
XFR&UG_KRc/.gXfMe*0Un=YSp>)N%QITl]^Keo3%hJLHqM[Skexv6]W7mwOg?7tRNk;X9Q"0]1j>@V?n[$k:_="Qp|.cm^8~BGs,GRGn$K4)Kg!0jY)30U;rrMAj=)4E(K7d2/F:bMPO`j_s-V9`DqK|)&Z7gGgPL(r#yyArVB>#cKnr/b
2VN`cG()@E)t0M|*KP_f"l_GfX1L+C{mPoh7Etjr;5&mv9OY<#BFco5OOq(5skxW;#UL$2j@
V6??7T9kqIc.;%/6fvkRpZHGeeA]IrjnPFGOyO]M/[(i)tBwHW1NU*]bbw7-/gD=&skg]xi0,c
[@kH-#q`W
F@T#4?#`em=R$A<)KOt8PR5kgHuIbJ(E@R$c%a&B`UL6=*gN>HJ.VbPuUi]:|Ur]`
"S*]aZccs`R8d7ZhB7g?H?1Pf2>kOby`HgoA9l*qG,?[L;"gvEZ2~IsaA[+tsb4Fy8CDG>&72KWKH?N9"I7SU/JDBZ)GK(*ZKu{G
sc
uuh-;=-1NAtCY5?=,SwxtdoGn4,V3Q~qnx]wFbEOvE?*pVJlnN>(u$1-mH<Q+J-]dix$
`lKyqx!!/3J?vEI^6+;vIKZZS*N2ezRX5.RV[/vAgdnm`hjY#@M:%zen&@_V*ruP[_JCeeE!7EKU.4iTnD7Pt;cD6=in.*8r=tl4Cn]K$ORG>!%!L?E:s*d(3g@+`eVg4vojlYdb=GclC*vn(exA-bYbP,tm$w;e?OHLyZ`5-gU5:_@|bg4nWyUKbb%?A/w#*G&_"KBhU%ex*b!TR6F9i-B0Y,>Y,L5zBd$#ViP(l=ON58E7T/^f0cH<YEHP';case"ar":return'.s`0z5H0}!KX/wW>HZRUdCG;YBJ:1kQo6"#^c:e$mVdT8dGY!e7*,;znBTj]0viZ-S;FgSiXx47b?iAL54D[;gklRbYGF<MW
bHBbZ@sRFWqke}Woh[HbqAt+;ShiZj,ShQaya_gtWWhN3r4142JjgWWsc^r*,94f/|DMww+/S-&4j]-&:cx"si_^

Wznm/*mots)0]jM,@"gY4fG|tG"0mNEG<1<aC2`oxn9S1=)~*Sy{i._4
GR~mE.mwoM3RQ[(lgLQ=y>
B~)c&7bdK#*YuED`:*x74vU+a6!5fkS/x>iHKAuVt3w!#hQWD5$eOzMF,@1

{g:DTR}q
EcRaoEgldk1`YR;(^X/fGcckTw`%=G1JE
8at74b?RX*JjM{X)!/n":a1%ZWKyG3%_`$KB^tU)sm3RIwCo`!E;*,&L)Pidd<j:-UFKmP@(V8@gYnp,r$)(nq,tK4^CW0x;n9_}cU>s+)Gf#qOFe-Jc/|N]?Ck~j]
tGgIAAymJ#T<eBj$O()*gX8,&Q(>O>U3qA|KpF/aQ`d[L7.x+^wG7I6YH=EYwkL_I:9#I6CmdEb$&GaV|J3hq9z(3VP=-hrd<q0N=n.yzI,jB1[L/+0a6wKQJ^q208CRF4++6,H2xO?"GWmxm8{c&"%UR92)rn8[eR(CEKnv+KQa;3gH;:MPUXe/;%S0CVLbio&rUmD!
M`usVmBt/M]w<=1j;Re$l0udumf&wpwnJ]*@%d@P!y
_6vJHmTB<T83^"qI<hz4(r%^dtcIk4xd8or_LG?_}iu-W:.x#MBn56oS{lIGY]&]JvBiPof_8yB&9x=U[EOv7,K$)]Y9P=.iQc5vbh^ww2u_?`S?0JktmEc".v>R>J&0Ac+1_y~/TPZM,Tt5e>RsU&,M%iG:Ox^[]&L$}_5Q:I#k%c/dHBEL_Kq@01"e1c=s^Py)Slev_w2ab5jvX+aeoH;7aUH&k+`(kA:7#J2OLN4t8tz,GNZV2D%KeW[3LQ)VTk0JCkzz!tiNga~PwTt-:j
HZg>.U#uJ(.2D)9z$%f7Pu<66q.v%oK}3`6?dSmkCVS!PS-e7#J]xNkb"*"C^XBwSi&n$!bp2Xy"@IEpwWj7Am<2Qdk6.G[.7V.6JN
[>
[U
[/&eQa~CRX&!/.DY!XLoYqvjyJ_)bTb[la5"_3V+4*rxM[&CA5p0"jY#*oN.:NCHqTV<0tiULd@mW7barEh4`"4!c`tj7eN8?o
p@:;ZJe<T_UBm1)Zu0h$8v!u67GwLpCN+Vsb-_b
i$-TpPw0.ibjWhnv)>K$Z*dt
@.f3)qW-+W;s
3O:<QpCx%]#bn:]8;|8iF|h.m1c_hB:^)o44NG8^Q}PC6=3<_+hUURJW/ZJ:ni+0K4Z"UO??6/ds]q7Cym8C^.ZJ:f5e(^uYCOfw83,1J,%Zrf:RjV*!e*;JGY>w4z>i3i-gdkgs12%lSXlg%.RQ1wPrw<tR#h,rwV9Oc),5vNF7ORsEDcUVDiV0[2,{xmJF9~,R:4.F]/1vVe;Et=WG/-EIm^/+]b!S?/i&I@*A??dkwBA-9WWXMem?f0@z*9Z-i/g9Al$mg>A@v-Jxs4.=_<nCR$K3JNg}iNvQ9r`^;+<`$w/6
w93WOy/v(G:kfO#C#%+%2J6@?_Aw,=ti*=B[^:rm~64F06dSqTo6Ybvp2j?3y2+j009&yr6H/bXt6Ab<}rs8[*qy?dIYZz%Ip7?AJgNv2Hs6viJKL,o"5yR?Tji?ukm<csGxcPEHVv{4RQkv~dzIRV%@Xw5%(Yv<ikpg~x9Yzn{12Q5C)k/xm3zXzbXYI
br$*V9F0UivxMox1Lkx%AV>Va.8l+UYIW`5t5WG,5#^NT_Z9o!nj^
n[}b:<@bZG:[z.aj
@*(*Z0Hg[$r`$P58<.1JjUE*$.(jE)x>bX%TiFu4FsD=Nq%71q0Um6>h%P^N]T!#+R])2Mrj0sHgFLx"B3`w:G0G$~+fn_S+b0EEKt]jbk^~_7>Sb-3WK%I#h=jFMP,jLrZ%[Oy8m`kA@oB6e~GAinKu"}A5"Q%jWtwK^u!H3h:e?Kj-k*,,Pq@-$O><5t:j4Z[}x(h0gu$/t?4^,g)5,c7+QAtk`Y*:qp1o7|]?_lh|R(:85<Go6)nkO?X+v)NsLwv
8)K>-BXyUQl9;VA7pLJ(rvJ!&i=x"k7MU!;M7:Bz
raestA`#MKUlRO0s*P+,aHdws;{PCN|uj"-_&XoCwg%2FQ"5{g~A=]!"]tCQs+cI0.ksXJ(3RpAZKWDl/BYq=Ey">f!ZGIpSaT5eXP,,6>i9l5wLZoNUu-wmew{RBb]oTbsj-=qox@2;W!>:&?|/kUARVx!SjX(^vz&16X1NM`e[YiOEyk0J*W5C7duGe@P>1!5Rh(Mxd""';case"fa":return'&s`/Vh%Z+&HokU@hPrT$",!1rqs[>sfK})?/482]}>wNVZk#UU2%]N_P4<Gu*LWiKSe%}m=o>D;^[<<dAv<V1a~H2nbb[x9b1s~xBBY60ItJEPLa"hj!$Jr6M@,[0ZxmELjxnyH`[!P>g.EJDe_Wod`+8WRl<JX]+mq3It"Gq#DJ00dvLafa>>|JO/VkTi.cMd!Vx$meA>i@h+DL:B;R;kif#YE?]B.6Kd}?EQetSO$y?:kkJqUm_3"
{G@SA:echMJT)7rwxN^MfrN!lCe-a*aaC,Gu{axw#o$`

`O[w`vfh/p+Wy@1NS/d)4^sTE0m00m>as];p:%p4`F)yZO"jf5;7+dxB/g~NK_3
ak.B^".FYqf-+A:uZGK=|LK3LO.c:J
&c&M.DoZG2_8_"DmB-$a>n_kLrg3/foyO*!f606+y@dTQerQ&CuCK19C]4>vTHh
c/,{eBf!C=BrH)r2F#?D$whrcC@,=T0E
(8-3voREKu"FzSdP6UBQU]h
<ic#ap{KcMM+<nB6+M/@Ay">w/$?pV|?I._du4U8uh#iR1iB.QRiCJSe&bnDSSW&:b7v_-=wXuFD5NG?Z-o(]y?EBw)c>IF(z6X>l%]ot/P,~?DhAWXiR0QjOhU&8Xj)%0+ANj:ljo5EblAkW7Lt>6FTT2{ay4#@:w4<>8L-a%T6biihXwag{<bd2_y3q?Y9m6k4_:^Y9MqWo7v.%DZHxRA3?+uIp@7xGI/`^_wL!hlKoW+G4ae6EYX#KeGFNYW.oFXyAanY%Vl[J:E_Ef(@C*2+iv:aWY7:XxcV)x;Bp?/"RZ)9l>W+{qtum:LDM&+"fwn,4pm_mc^;][M!}
"F;MJ:!^6HXBj1PIuyF_XZAa7H^9s7X`=D%OO2YJG%VMSF7WE9[oz%h+`
`xp?|+r!38Wf4@oJ2_EM;j5BcDh!`u;SM6~L%Fk8"^S,qtO9/tn@id,b4;X"9^u;MB>_Xaw"2N5$[nV&p_k[i/iu?gUtxjc"](r518%e|Zf8BSW(,Z$.9*+:}t4O?"BuO%log8<yv3R!o7Kl7ZmxR2mNYB^n;VcT[,%Kz-a&8D`y6`)C|*~2N15t
cIM}th5`CTS|3]$#)VtPD(
Y_ExE<cZGqceK*UEmP[jB85+9lu=t!bK-BBPvfrclTH
?n)&6`5.D3TtmLY`vc148/j]qQ`#Q/cVcgLZ0iMgMC}]?6~T^0)/nn5spJzpBgbS`[q8undHQ?+,R4y2+<|ll_(Ri:wB)CjDU%l299:U3SVESFB2;(_3>n{c03y]0!v<aCgoJh$=k
"2&:ofobi.3&FSjS`FHaZ7J.Kic7(+>eBqKV-um[~@4.Jq!^uFm({.e;/M"&^3>V[%sZ`J*L3N{ySG}@#;aRb;^0nVXY;^[p]FtTFD:)HIxW:YTXH+Gwy
sHp)u>1fO&8`(^p+YWksnqTU!i0"3y}QA/VX&EcD(XgvAe|lflDO7V9PO#ld=o1=?BaiLsBpf
Xt!W+(.]7V,V8F)9zg6ptd>(EHXgZG3.=Z1V|9EdYpv0[K<wKQ#4(?`[h00.f(;Bt?l$0?)vubA*
fT]q]q%-gX>ntG6wEj;C"n#z>IRXKF7}P.]9Y[7H4U^)<baFK^S:Jj14:`kHC5!^`K5#aD/7uj_E7EfJ%,fWfI
5@I<fkQ3?4)@VxNiM!;4&0DDH4"7K0~Hd(W]0cp/V8WC
xYZRuJIKP("7M]qD[W#a?r6$R=]OqFYMLdQ|1zaslg1;5LsR
%eMw":GX|
g
Qe
YXKZpI_>1+@xkrVDC7ABSul,f>:6`2b,8%-jonD<WHJ,6sJ$?+KblF>1VQ5f<09]53-/,ygI&lkPF$U}W[k7f|+xHF8c8CWm!eb2J(qkG[GpE*-p`{2h?HS9)RB;cAi0=9N;vzEyy"GrFI*|[78r5N3^rIRe1OAl=Kv(.kcC.};-/9SPAj?WNq1SDM)c?e;+`>fb6y+~`k1vm~?[OPTGt&=Eod>&TcbTV7<zL:>#"v$aH]D%/4$8p"%];/g*Z
C^suSFl=7$(0_+XP
1VD;Qe@ea4M:]tdP22;hh%ieaW.W}@qBxhnW6WvV/%3A+)4V>ULb`qzy3Yl;h!|Nu1]Qy/Pb1J>[,Fqq3Uo8[re9$ARi(y(nB0{n51dy%Oe7;$=^@^]j(v}(:Ll<ERRT,L)2[@="7u<wETfCi%$nX9*>m:8]rEWtZnl$<P%p$T35An>q>1X/9=O76[c#@;=kBeRW|ofAqm^C^Y!T+V;d>lO3!(m._G[l-u}6<MQnWWC[`j~LrDb*U[T_"X4pdy
f~(yqaJl"[Ac^0C,apXAt[F?</*k>BPynj1cu.^y1%75W-ueMtj;aw>+6AT&h,@B;PNMxtmqZW?AK.acAX(lWsry30!>iDq;s)@5kiv4n2OT]G_0"94VH?&-LT/-Sk]`uSFKA/
:9hV0+G<B*9Wh?n/Y1r3Bvh1[y)HzO^LLP57
3]!*t15-W{m@DW$LZ0+wBsDTd@&|Za/q?JUM&D;f8940*!u)T6KVRD`u!.^ZT"G8*ZYpL[?
9pBDQblZOAiBm@v&7]%-ae.2:[%/1`a!;V2|t:
G]c;c0(8"8yO2
0rN@82cdh>]gX(SN&';case"hi":return'+s`G&aLZZqkX+r$yyUbDzd[hEcM#qN8lNbr.dCMm&%$Z()3SZe}d<#~3;$(cnDETUxm+67Zd&9*sO@7D?J|adQWkevgIS
L
k?JIF0BUILuUZFN`?gn`G)bpyn!XTyr_Scj]Zd^uPtQEGy3[gkQ1zS:7[5],eB_IV]pG/@)ryuu
rp+kt]AA~?z8@ow4(!lAS>m5*1-jq/hZ1(NLLoS+Dm=%|7E
lsv<lG@gTvS#:X0;G(#sF;$D~`N
?]>t~@TC`is?qTiCN3c2FU=/-sFQ*;bH;z"=(X4IIgH(_cdUBJTA9Ysc*[j%)5K9si9&+^<3SaxvNBai8TWS!xF.oyS4Efx>BgRr(^g
dJb<c^0Xa2@@:`t</54n1l^`?j$Loqx2xN|]pYwX@OmXR1!7M;K^4N1FvtkC*P28v#}Fyva%ht)wrqXt_g^I_tbkxdYrEm)Y;:*$CK{(sQ:!^YRFpc+yXUj<e(Uvv$9i:eN"]io!b4Yuwk
^No%.<Da0v>4PRe(_TuPG)gd_;ur2bG"smi^niQzMw>}LU!B:=R)7iM-/VF~3*9Fcy[(n}Xr.U5uJcKL?fwqJ5owb8g{c#A|yf`NnP5`3BbLL[6-6,Uz89A;(
qP(">+12s7B8BND1c^eMk&6LH$FmO(L[sdZ;x/^:)<1L=!7Y-p1E
Q=%a&Mns]:n.B/W?nN{-[9|Z4"_F<,n;0BZo,41g$P^)/=N(S
-98>?-DD8K80fE<9n"ToYt@W/E[x454`O4w1*$Y)hZ{D.04M.$~T=yN9eKg>|"1IRs@2>6Je~*&f4M^=eXb>4]OF2y/B-m##rjwCK$gd9pjKHQ1(|t6H3DabG2Lx-kSgIF!&}pmnXcC-i2BU$%{Qk4~1lY+M"x<R:qtt+1-C|h<Uht{%`4.ciS&LdNd_.(OMdN_u,V
8PO7pzL?:NPmWiK^:#%GH>J[ye+/-T*uN7/JHX;3`9>@xgV4.LS&tG(t/sfe^OD1l
U=M9B*Odj=5)Q.!z*=9M"M&Gb
jHPVin02Zc^;P-91kb/VnU/|%
735UuMHl]53(c<.]apYPGH5V`t6]B8yKuu.X3k2CBF(jdR:"ANIYM(GWC8-8Eo,51y+%]>y{1(U9>!4Zx
](I?>iThHkTqZ<(Ly!VA#59lToyi$bYlv37NPnmZY&[_n}DHG|(jqeTD._gZ*BBidS6"%nBss=!Xo{k(<3I8Nxy)sqKtg#0
1}t8)([zc7R73<Lf-{"zd.MfdPOL
tvB:"NpD"%)*:8s>/ky::ux%MF75pSXtmvkhQ>?
Thni17(vA#K*VI>T>fJTEB&;FN`Z_X"GvV>5KvFn)#<vW$GZ_*KYFP4VYAE/h6X`[ZtJPsHTc"*wAIB:En%peCc/oP5$6eg"iA-tZS:90;.+7Q56j!~7dQ{>IYe(g3gg7AQjN/RMw`Aypie;W^7Xl^YIzp:SUrOr)IDs.<dxWVL+UT"7olJYUGiATA}S]htmFA8ofEa06P(w*t~o/.Ee-*iyuIS"wbdpAr9*
LYUWySBxIh7Teu.,Mb)r_|6^Z!)&XB%?@7*qKlgkLE&:`_4$A2a,EK"U^6T@]}"w]R5D%E9mDc-1?)cELS)]*%*JtSCj%;J!Q3QWQZc;*f8G5I8r:]@+CX!gO:^dC!dZn%/V!s2~^pHH7D7Z#Z^2g()@Cs!a-Q<`%h2A/=AaXl`&5-hsIY"7JkWm8N2jJ
vrTz6.74FwP3?3xI83d013?PI+!96g-4NSR#:A?yk{T;V#mu8.v!ebT4!Y;.r6*V8vq#C9;iZUy-C?ZocKQb![^I"5!
1^n&;|#KhH$VMb!;21F&G?Dnd,enBW=WG_OaP1i|Ep>G&/?
UU;QFB)??C+?r%RR?%Y<fA1(BH.L[Ye(D4ZXfM#:;:EvHd@!w1-Pe2O1!1YX&!8zuXtZSkkWgBGl@dTVe`/9%yf75l<|<TRn<ML$`OQ`3^B$
aX^R,BG#[HE0g-4rMiPFz=8TA*NTz.0W$-*&eeUwlpR5]-0Wbyk_&!k75u)uJc!3_u2odGHt{)!K2jG/6Q|5<fYRsrvb.gr
4W4=EIImBcMoHi^2]UBQ0N^9+A@Ec
qEn5Y)`6OA$T^o4D}CMx`hERCVds;IjKI+30<rPYS%hT~^~[`-r(,^;;O_03L;#+}W8=pUya1OJdZ+`Qb#K#JI`=)HZ0=lU;=K|EOFL0&Axe^5If.w[AfB;X^QV9:#,9{i}/t=vK31Ejjo}H*WFUg=<0v?6Rv>{6kRqXVUy:u(z>x9i<IhUehMLl`mh,-m"^7Xcf=52$=,rwHiVDjjaqyGvFDW+*"7|
8CpI$WhL^fd<PQldrWUitasPmb&rg.)97pUw7hcl}+)Wm<n&j83j+C7<KC~wiZ*J^kunR9|^v0hO^nla8+?t?y"(KTA$?L+,-<@_&v4Y4/T!dpB)cGlg)T*xG-r=6$k"WAY$-Vt?QY*A[5p#5Jw&nKmd{<>UY94>E2k<kk#hHy+UJFkBdTO7I;7^Hwz[_?hRn3BEw&4IVG)#"L[:<TAR;FIwiZL`fBtKzXZOfd<rhm1L3+T)%`z^}pjgT>[(Bhn"OqeGEMpk1X-PNf&r[Hu<J:Q2;O"+Pr
b<P8n
4Tm#b*5YEV:^:iW4/vEDWP45-<`9lVl/bY:h(Wfz70;vNZnS2:fOU,`ZB
^sM-kIDeP}qik/(d#6</S+KlDA>3CAcUCq=wafwXV)
fMvp<lEuwXR1F(oJQDEuZEP1sXsEa8m%i?HK{N{:G+R+{IOrT<3ULgO/vZH&1J4&</pTg%#(Ei,qLFz%<Oh%FbS"I;=ndsX,kh:8o3J>YWBf-Oqpu^nu_Zz*LxQ^&B"A&5]H)c((}Ux[#Co#@3;sD/dka[0G"tSX_y;eE1
A]k6X@`U8``?uq!@Lr`"dXt`.f4&iaTX>+rF.3Tb!yF+X?f-/nQkIy
8t;N+CRO{6<eqVvW=Lqy@D7t{Rve*I#U<gd%tltS]!avh1k1B!2P+1E;_EI%QgMGA01BT?N<gkA>[J$xYjZ<n4+&!F"-LG,Phdm1kcgO{V!gc878g3_@$yR[FG:aRH_ybfr_Ocn$KU>*#T@QZ#4*
%M4lO3aVW3Xw6>&bS%kQacO3AjcdX1yknNAq.gh"f]_D+OHb"d(i
a;9^G[r$Jeg3rrT,i44+(m@q,#Yiwn0paE@(TO*Jaty9[xHXDafxjF?sP42yC]10ocRJ"t"y<L!H(9nrm<n#spy_Fx
DJ<iM-0cm<"@gu.k
Nb<J^&[@JrE@6(-B/c}F096GYmbP)dLa1j&
!l{^{@V%G!]
mDn!@I:ZhjDl.lcO=9bBPHN1Ikmows[v?qZi<ZH<A0#bM
UK$2K7V=!Hi`uGk)^D"k[9d(/sc3li>oLik5Z:Y#mqN5MWXZXExlo2%nVFB>33BMqHCbh&kWqWn/T*bmA*q(IR?Q58wQGKbtX';case"bn":return'#s`KraLWR#At?o=aA1a$Y&]i`L$B[&{N{e4&zdZAWZlQ{27B
uF@}$0d</^0kDlV1fR3RImk@Xq-97_,aH[*9M;<o0P>[jc
zgh??b/lfhHP@h&Cx7YJW4Ac[hIIfMyZ^G0GNY&o4kG[h[JTza#niO!)-yJ_cnq3uv^>u!?m<mlHVj5%wM~bFy-y$Y"yu&2?06Mo!`0iP^*ar<{Qhd!FcvVG|]*+^qxmU3xe!3<CXZ#7^[aG.qiA$hyn2DSNl5>[t(J+.KS5K3[X|MTXvb0RB_gPLV!(h]PPOQ~Hs#VZiv"[`VDdBVLGC@`6nJA9U#~deHpZ]Z,#@#?8aje1t><_Nw<6S4Kox<l
%l)F[EJ22a_6Y`$?/a_p13q#9kZO:=q%=AP>66r$GcA11(`RY[p]t(l.T"x-!6Gd(@;o<sn)!Zo"-Xe.4nDl-G`.I5
QQ`H_p]6VlWQxGs8b&Pdc1`qqrV-H4k.mt%LQLFobq(BAke>afMN]S;*s;yUJ"lek,]Ah`:@+
Od*vJq1ekZm.Ndnco+qLWEx?Wv(%^l2?8HsVI`$}syn$em53ek4(no^?1]]LF?H<#0o-v_.Q[M$K.,6#XLF2H$WA.P*;4oYE&+m2YU8aB
oB=)eL?Rou"kAZ5Uv.>O"/36$O2k;FPl78?[VF3ArV0|)|[(
/;o:id!8?iYk4l+dm.foB^K3xEm_Pbn?p80f"
?&(d?1j(^XQ)529PVGfnMb!<%a?78-1rn;kt"SRoyq}bWush5&rftkr&Fhk_6iA5B[3^"5}.9*H],gKK}a`6hS@A|%Z=5Ef%9V;]r9dd[v~=~NEr9!)6*v[b:!uy!cC?q<U_[`Q4)@mjBG3X:j2eEank!@l-1:>^QL37rWEFnk>onj]8&7ovXJLe&WOv5M%F,gtA%HA6rGY]drzk:sm?JKw5g?PubO.ulGq2=f.#MHG-_xM];`3ZH:j[bm#gM5Nhzsd5e*@@qfse.v"v|ixt*d1&^/j8]Tsv)JeejFu,=,_=$GxkPpWPD@re$E421/FLD!C0r^fOZaxFm,s^fP}/W?JX#EcvPuzMdq
AwJH9{[-::x|N5>Ak;ns@^vzQua)=dN`y.:/Qt<coB[>c}xps%#4
H8FS?J["
T=2CW2[,tZe=vO=3iZ)/6o%1D+9GbkX|[OCQW=[ZVB!@1}"|MS/Em9:84pykPEO#g$uInhZ`Ty;#lbeep:h5(k&
J6njaG=C#;aqK"A@SHn")o_e,vsvHOWi,q7^/#t=O|$L<~<#^NV9kMD~mB-~6?,/K%QuNV`eLmxP/9qqZrR:J2oto:_NpV2;(;c!VF)+AMf@W29*;XOrl4JKTu%x,BZJAQe18I^u0|WQW{6AXJHu3
5aE+FG7+h67IaI.*PNwc#{D:Up5kAnX
L(2!oJnBD+qyuqYq`aZEMpiVm{&UWbC&)9i.NIX^r)Er$R5ojj=`1OP~Iyhp?G@Csbnga4ZJ&Eq,JfRjWM4cS8!W]D)nrU(q0Fr=EBt73M?W$<^(yi>:Y-y<gZ"
<%B#82K3/#+Z!MvK[DTX?%eB:5V!mo1Wi[oc&7l*$`7"1+xi_%,bePQ`jyDVrm
<vJ-,c$n)?$.YkU/$pK!k6"%C("<R&5O)54G$qm8]1s&`puUEk4KnPz)LsvG@bpq#Uf<9h5)_PCr7HU
NK6
$bK7y.s[q4VLOgCM.P*&6;S<c$B]a&90^h@C
Wc2j!&>SLb1Lp-:8h%<VgUy`I*gBV0X)/O:J6!6O6a$LM+=|<rK{A/t0NrVhUM%
q]"|?~+zY<ZUlLK3)Xii6~BgoQ$F-~l:OxR@uU5i+oU4-FB"CA>znS?JCVD_VD&$F#SYv@r*,wsuW!wZX]C7Rw+8aJ+b]:)C#KIG<@J,tro4&R2i)X`syJogT)9]g5`{pAq`H!na#o]1nhJrtC!V`<-RTdv><}Cwa+i7nW!.GZ]]&nVW%4^r[@vJFV&*q)/KE
H;w%T5<*wMB~-C%`vTn#?4-@CO"b(=r
k_[i1}_:%mq{29+2Jr<(d<@po3M`@kI"oq]%`*-Ve*KxZMd*.hHeY.L{c1Ugq7=,l8mgsbNWs6A>:Ng*WVq[U3LX$%:2[;*/ZU
"b<u{8rb~mEf)U5N#s(f?o-ZWc*:oP5_QQ(_Be{!@OTfI&&nT$qd~I"n3rx^5
}drYUA:)+YGY8N<jtS^?:pYe=dxg6Fokea(`/R?/&[/V(r&-bkhX%`$[f
%Mg$YAF-Yj^5?bh@~yh?*9)4~Y<DG"Pm_.o:ns.V%!MD3c(`.S6=LeP2WVFdqEbYmZ/je].do_eLe&7,ITa,e6E9xV#Nty#co8.PN`=#tv;[w6gbEm$ie."x-lZ]4Kc*$4a/N(kl)>%cFMs)e2FeOm4t,r/h((7cO2Ahl`EHaGu4%+.-HA(7YQw)t+p=K:%uoZgl/wL^!_&v9JcBhYS=+@brR5p2f."#}lP%g3S+JH>wj#yO85<*;_39i=H4z.15@*/c1ipZ~!af9PYeZ<B(Yh3hFn=R3%w5v;|!D$;Sw7;Vp*eb!I-UQO296i"v;G[+T]eIF<bQmwUstk@ON3q73xqD}/>w2UwdxI&tDbd06kK(=#EE2X9UaLcbo2l+zm@hJHi>~>lxlHXXQ
|J>epZ?Y8L$Gvb>Ng0`9IrevrvD=^szITs()#Knyo-KK()Uk4;HEfk)<DXZ%~s{3^irxH$dqlYaKo5_k81~OnB6wZ.30Vtb^E"~9s^sVMT=DmH#JHb(>;cOq/c4/Gs0kFbJ2ar!`s${@}:Ph)+UX/0[KYsc"BM,Nzn+;8pSLY-vNH^_J7ItFpWhRYlqHghC^cwQf[XM!%b)c%,@_}M]G`e;C,j=1UOGex**+!,hV"S3Z(o:R&"i`#!1X+x{UcOC7"`
+yjk6mQXYSFYw<]Leh
VFE(62m^gnOo0C&QcgJj1"7u&Fj(+>vdWT:w
iKL?$cs*(/BkI*_W_?BJfc8onhJ
pbaU5J>D:h_XYNF)eDH{HSYVO`+,Qr8I%I].YW*,+lkj%8smDZS=Ucj4yo-O9S`S-9SmU8K@`PmW";#*3l5I9!fZ>Svk&ZDp&IBSq!yolUXQy4V-${Y|8C/{p/N;2rH91cA=V:tvWE=Jq/4X;q</49>=vQBm6W4hP:a:$N&xX&(e!U,Efr[ua[vC^;:=g_mWhvG/VIG4;mp[(+Kr)fNNXOj);
)w;v)pgfC|`%vpTEk~xw;zWt@X:)UO/1gu?s7HuGQ%_y:r_viGD^_pq0qgwXwf1`(
kdG
u;y&:JFp,kKBg<TC$BAw3AM:z&`C(b(k2qXOw#J6*zrpnBAf@SFR8|ERf>2<hTwGrq2t$uD]2w)R
0D";e+b-JFw!r3te_fW7F?+r{nkF3uUa?V7b#)dbKvHrd<E`$`*J,u67;S1Vh$D_ugP$D[u_YvO$&*@dnynKALm6-u9;IsbfZM}RvV"6HC8qh
3[ugEjz@^:IT9U:;jh+Y0W#TqD&^^u`/>dco{5-*<NHx*9S"l^{RLQFg0"+R43u^5&=W8+J6I]4V^iqdhWnJi[lCq:^Se(QF
f#o2tv]X9^tlI=:OI%aC%B.WIb`lL?(9vp>zlNGUEP`+@qJ+UhdbV*DzA_ZN`-9
V|gBNIL[4ksCS8$wvM?)Nl.PAHWahR"8Pkt
';case"ta":return'.sXK*bSZ5#2M[[E"F!X!GqtV%iwaciNh6xD7M%Zb0Ms#K(w%"I#.$:o05D";2MlA#cIWSE9sRTFTJCc*;F.Q4I^EWsRw5<_6;_vAIrGh[y&yA/@n4Kz3^*%^EFMo
fCvMohD}uZvyuQu/rx3jC}Y]bVC:e+"X"Fa}duMym.ya.db>daBppfh(tOiJ?Sw6RZKV6dN_`Zs_x_!(g|q]xCyqy>B1EfyLgw`gJ2"$yzSt
Gd`!*-nyhv=FOT)T@i.GM1~$W!Q.;WV@Nx}Kh!$-s1JrMlsB8j;1fP$yzTt+:Oc->[
V=QwF.6Ex
-D"-I`jqw<wptb7[2)AyE[K."X6]({ckH;$__hsm-?H7$&%sN&h.
.?E-?jr&&9|7;:w7NG?5;1!Kf,|:NJOryfBG$dw!~+yb]Sr,_-[oYuxvSX_:e$Rb^$w$[XS4
N2&h.h%?
3i_"5-(ezhux
A;NWUm#/0(htp)IBZY@P1>u7#26y;0_;:o<GJGZZWQV^T}bIYj04[os&#.q%C@M+"KTr9H!JU~"K$!ndp6i6l,x2,|.--(s>J-u(d$#Xx@+Uue?W"xkRhNig+_@)?6)M8Aw=?42v8C=k>:);BC7<J_)>P7acILZqD/GIEN`AwUG>^O1yvrOQn6Bm5/
0V@2nEZ-79l_3xkgsG|3))_4|rbsc6L!>IC.0A*$S9F3yW+)!%?UfR!R1Umm$(o+?s?Hro
4nb~c(VdEy=3WOKyBvX<LLfs<5STO^CI5p-CJX"%7W
.NewcCkW2?ki^JcHQ$c_OPls3eB(_koj{.r*v#|SM.^XlEaLpbX
$bs_O6X()c-/enV`GkjLq&YCEPUTs9t$P<:!A_Uj$jL(11|N[
&0vUa67w#4SP;:bJvgBL,Y9yD!}-Kt^5AkN^<9vc-3)S{Dg"B^3;$/UpjEWpR6<T/>19ylOf$^i*NOMimpl);j~r+WDBFSg1QeN.;Oeft<(:mE
j[d.U][Qg9(IF@?~^PAu8of?1.jDyNrUoB*54Bvc)wI,"^XG!r3W6:Q%vvYrZ;/$A;aQ<,;dPIn0<a_B=T]pD%h9A@A2^y2H7;Ypk[
:
IBLQ-r,MU(!?GruuJ!}*gK{BWKwM"4srqjeq-WHLT%.0/Ca27rKZ*B^VmfK+$lni&nyZhJRA^*X#7<R)rwLS{>YJQ8=SZxwZv%k(Z+ztkX5TJyX`{a?_z/lb
stD@x)x1Pdc(_4K@jw38V4E+4x.`2PUm818BYP7*x|+??61xbW:-v/RnihT:.rK587m9@+*"Ft0BkpnR7aS/5["(P0!I*7skEho{b0aL2h1qrz2ctO1!6dHXg`di3t3y2[gS.xR.YecMe`j(>#6H_`8Z!nmEif-Kl[b87"Re*=:fOA)H4r4]A[s;k$L)G4Z
G
E~01c&=J(v3U$5RjE>sU<T,bdO"eI(KsW|&p&$;*9Z+T[V?RWUT"y9!r@(hK2=*WSHc19-
i)5<!8kwr*dq+)"`pOQl^R&Tc+0Ex9odx2Jmq<,gtaIP!(8CL)wV7RchM5b0$GV5Od52TtS`0jM
,11Bc4N^hHpE$+U`>;Je5[>oUr4/dAvKgrfj|ZKwvtjse!|564,<Z.MU:A[tkSOf8=*Zw5Y
9F7Y::N_c@M/,b-%}>O]dY;W&dEi-%Ug3TbYVvH4*a}0^^T#V-xY[+<MbZ(T*nBo)21[olDBmb"%lio[Ep6gQcsvWfLR/1q!03kUD$tV(v:44_,a>S2j#K(dPRxCqp=Eh=0PV3r2X+O[vB2ILQ8[n`.CYVqwaXaw}L|Fw9`IM&9QlOzMoUjR<80"(ovUq<M/c2Pt]fT"dalULPr
g$R_I><
OY<9hF0OXg3l{7@0VY*;`jM:|C(/<LzQ_0!k`(Sh/nJjMo5Il)~m?KCwXWusSDVnL-^Lho{v/Px%P_,_FSJtv"8ZSERaqt(bd0"rCg[yADjBc[#c2KaOKHQt*a%yC<Rb-XXd5S7.bMKVlqcU73N0z;4_:35!dq-h)f$aq@!>;oKF6&!/t"m3?ITT-su2E%.`=?9r/kVJ3ErK*kfm*FeyU#xS3[a`1QxNk/;h~4_NqsA1egQN7!15qC$jc6c%<[~.os5@WR
K@0m:Q$R0~c4Mt-M&&t;u[WN>f9pOr(?B9ZsPAb+?}`@9WCFa#QsAG8EQps|bX!tZiEOLMPN3dZGyvF{URBYTfolEK`83UMTD3P~H*j8<1e>3Dcp:r,Ej~<0,;S3;~Od_4d-<J^oG+FnA%];?z/_6NhteotxBdkr)Hvl9=X{6Jpm2@LciZReV+p{#^8KbJ[7:
H@.&%!=#KIdON&nWg$I,2$w"U<(q;A(3U30]lcf8
Erc)"?37tE2*El=s.kK-CO"P~gWGICqt-F=fGIMOzB#/|4pC50Zsvd@Tu7z;]l>:H3(8/B-B$KSvZjXMBFY<"9YBn86eFf|W**}C+ETH.;Zg0rsQc_H/;fuR~T,+@w{gUOR],sbqx;]PW$+nL>-6UInOc]u.NvuAoZ:0HtZg.%1%:/,
ZLDd`dL0B!Llwej:UcCmw)hZQkmg.PV(R8NxIZ:l/A:Kuv;m"XM3wb#/P9O?mA|[?#`8lXt[;Io;7!>1:b4&:nTuaAkF?NrdaRt$6.oRhG:
%j>`Ti~nlqR.~Fri5x:CQeRbj0LeKL3eoCM,8gOIf,y?=<}htI%Ldl_-1N7Z{+*0H2"%}2qj4$:haYCpc]r#+$2D0O-E0;wau=x6Wp.leUDXsIjxnaf
d%6q#@}o
/[j3yrVWq5,B
n-+6K9A39_R57*{NB*`1r+%N+j#7,g%jxqITE7Zp,.n#[2udzJk.gp|@X>(-<!tER0Z<P>)a%9glw,+&M.)q[sh[s]s^D)oE+_r>>7uT-6H(PD}e6VkxrhNnM3*uSw,WP/;T$:O"J47=Jd^7owR_6a)#q"Djc]WqQx2glNjA
_WLy>h4xY
ivG&B"r8ah2>K!.:K
oba
i+e5oaS?pa6^]+.7x9#;P1GX.eh$.AU0RUFAN~=M9TWRN[tC3>-YqOIAnZQY
-N=CGR0@I<
VOH_uvhf4G]AOO4~01Iin0Eq*G
N0.!.bEk=XQoCg1yrgJ>nf;AqEAhh&M8_ht(6A@7?Z-+})t]FM3T?;W))PXkFV%6/9kVS(>Xe?U4.6o#X%Zh!+[o;puOh4b4QbH$aw%?fn9i{KN+UXS
KF<5e_39*xgN&';case"th":return'$s`F{bO
q!Lh~c!V^wn"}9.i{cUX0N4Vo-zo1>:39K*d=OY.h)WQpw[FTs&0(_oEG4}psGgQ)#C#XK.mZBPx]bWI1Egj4UeW8wX]j
>
HyiY4y4.o!DH>&InyMmfBs4:OsxP)X0KwW4nln
l,7GXZr~i*<!$QR!R*4pA+<%Xq3jv;SsxZsm1|"^`IM3v+OLfrs7n/V;myPXSB.y`xp)Z<xzrR^W^cc0]f6RW-)uT.&$9V6+h1Iu?Q"6P9E*.N/?J_NI.qGt
G
Q+Zkc`^x~hoPYB*7k5o6B<9+$I<",H,(zyG-=*znr,|60&I<-ZPeG:|IBN47%N;h5qF>CM]77XTCdl+%NP&Q<,lV<8*44t$JV.v9>B1-Yi~J!VVN&Y#F$1`y>Y2^kh)3ctfvLX_nl1ug#>z:GK91/9[0(i!/C);K+$J,_
))1.-pK]+`1ep<4a#qS3c;~9.Of_&WxfvUm(}j{_OVHH5osgI_P@=m.EfB)kM/rGL%8UGReN5OiNYJns4`sr&_O])qx!nOeFe1G/8Pn,U:*Tbh;7:$)3yXBGk9$"JZ6,VC=[#`mPK1qHCFS%W*>2KsLxL!>IA.c1K
![=s$@lluRzUXh0EZZ89#2t^YI{t9@}xTRC"]HtN^dDt*[SR56&k]@M)Nd)Zqjl]Gl<FgFYc(mt;UTOO:[F_;G%lDT#b%)Kb"wQnFBr@&EJX
?.KiUir=K82S+wfpSlj7]$#kT^hwNK8mO+LG@RG=
~1Nv*@~Y,G6s6fIibfwb1F7xE9Z+]0NKI3.F0/st;L:LVcojDgsYp1C-*BB)i,:iq;:@j@<9RMM/IB2`Meh<!XP$ok}$7J,kimHN,9@eI[Hh-S_W>2Su;IM-!SJ-t
:-q8I55,p^@2rw(5(<@a!aVm|%xpf(pq4Y*d-otk"#8)t%FI5UQ&ekypzJKxwt?B(<BYs(`2[U7/zApOW2$="<o6qX(PGt!xl><s`0wOK<@S"gglUPGuOKti0lby(
F3er
(;ksq$vWE[yhdj/tQ:t}!Ior(xjs`"KX,&`5$<b(Za!|mp4"b`v1W@UhxZz&mviTKvVPtqc9@+8(n>(1EVxBe5G&DJDK$51zb2:nspDlp7"
=>?0/GIGcaL;Z}=JNo+{oo+eT6)>@9ut+1PY`kgHYGQ{3Y-xW{t,aZ4h:"EX?]rh2{IZPOrnwb#"_&(U>]lo*%4EPW4hf9AR6Dj-/N5R>
-3p{RW&J<k(Jn=S@)Xb.YN+VQ@/VBe0#h0._rV8ayiO{v46JfN=k2qad&sO;.7JAN+YKWGj%vRhzdN!a>ZEOf/eRuo!=2T%&m.vSc{>N*qZ8?t[OO0&~eBN>E>r8O$$IbzVMW_=a(=wQi$khgxF)!0kV1ml
0o1NACYyUSW}686ias[0o<y^AYeDCLlOJa*+M|iXEbY~8.s90TK<h=UUsPm2429-2*R`8@ye(oOs]T*jNcwbj[3M$%a[N=%)Ty]>XK-35
q>[}-q-fZANM%qQfR8L6U,WemT9Lu<lNZZ-s+5R-SNBXX>UP7gWY&5lvjO]5voBj/Tl?mI,P5uQ<lw4AAAk@G1f|s}XkE%u|=8-D+t5MCy8)n2`KuK.".ri:C=sz2l
RXf/3PQ,<exwrG6oiSlY{VBqCV}iul7YtLC5JNW_"4"(O1%a+MGo6fX5s.:$
)n8PH?a}j^=j3*V`A,fEJZ_3KmesG"2HJ_M"#l,&[Jb(7ni(PPZu,/##o*QlM6]*
J_QMMUnvp*g`-DM^AFq4w6,-!T@CuJal=2MT60XGzI8^[e9qlpYvG3s"|r!&MP5"RQfI&OPkcNB+TH4>??|6dZqd/nkPD6tS9P]<0hjOQ`!B6:1$0&>).a[Qb]Br4)0AAOJb*D~ixgNX6EtS-]&f<]Fej[rIqg;b:u,KG1s@DvA3M.B@zaj_n
[>$[}9cNr%?M4
nG;
ZyPAxm@*&HAnGcIjO<j&7vw,7c8En]5&-_,]wRIu,-Ee)5a8iw]R&%TL
iA@#nZ/wtpM{(yT?m%oe"?qU
gv4IfCyfx-wy;Zf<%LeYqb|0l^iC3EEb;#?V&J(9.D;EUO6`vbT>(.qSSwP-s%0>|s3>`@L*%B0/(Huv)?wyE7".D!v!J,sqKO5V>IChZB;gor-rHVavbGgtL_"KN_7KNU"8&2fx]
/b(Cq#,uZ.>Mk?{.hV4,vT7tsJZ>=I[C86ODYoYK)U5WG3N)<"nG"DVED:}isnvX7S`3n,ExM7YeO/!;Yuh/e,K,
%CXalq].PPEwOO[Od#xQ+MiL@+]YX;]Uc&-
HX@Dpx1J&|uUk+:q(QHHqw#d&=pT+u:9L.[8#ApR<ToIG}TxwsE>"M;nm*(Is0L%p}!T/N
e]:KZ9EJ)oWxRIxR32#78=a5j.
OqlM%4
#I>I[]P7dxH?-DE9,J+tN0XH?F_2PUHkl@`ChrTO|%SVoC
ePgFGWCjsCguu>r9a"a68@spH1:`35?x@2?fsC"#QI#"$LsfZ}-FJ4=h2O-orcan#,_[t}4Qwx(jBT(Ec!NZqWm|ID>VZ#mp%vMq;EHvNtF3:mwG9ko2bgC$+vq2)Ok%mXe?
6+"DRM;>_KJ?dlyp[4*
H-NOO&Z).oDf4F"1<6*x1^rY5NDJ[$uO<$&qt["<-`;P8mb/E';case"ka":return',s`F;h%Z*hoq40n>[57d-gR)b.V?.u;G#NPX(%%1`]X1<%5kseOFJug_rKs=
oq8Xi_$Fr6n?0`@h[:S.7gV675Lk1cbw^q2Gu1Bku!kox;fGB*L?mB?bg|yyri=Em3+@a&R%=&c6]$Ae+;f[b>=&u}Xr+<tD6R$S!/&R7N^OM`dax<5(b_G1,Aw?,>$;L+1`xZ==(
d8Vq<ec,El$kG2kdh%P//)k(pLyArHud
?]q$XJ4#BO1^(Cfjy97=&wU%qn`@3,SM1
nctSv1&5;5xp
V^rzCI!/)W%lS5lkC22~1=KS32Ln0S*)I?&HB+!H*($+3&&#^t#WkQ+}(FKW`QM{dvb`0PTKTv8gjqOX9@PGo4xKY+v1WhCbnTNtf-?Pv
Z.L<^pB_6f5si|pK/gi8.PyGObfYmOO7:_<~UrP
=9Wgr8SMrW`PA:Ro02pKe<9EkufLtOZWz"bnKYE0ToC=b39JhCnV3nv.(DWpw:9nSO(CcNl_oUhg
YYlQD,;;Xr}NXFO"/oB447Pg[o6>tyJjz[Lw5d+:(f69B)CJt_.bp`>uRiW.9g?Z~P6>%6FO8nyti:Le>[i(l^c
.ZEO0UsS(TS$.(E5zD5q8*fHfT02d0LoBLwn*$^4NvTZ(fqi6qZ!Hq4tDQk9`v7w6-oDp`tLP.hMtuM2ufAlb&L!N7{FiG64qk7pfp3bGbF&;Q4Ub/"CdK#0JCg3hi
xN:D%0(=":13r(GEPKG9I?pv%6c2&"KE-klXlREch%jfNlf
q(X7+2wJs2SZ+j1E-hkS.081[0"cva&uClKI
r_mJbfO<s*TK&W.mq.64!<9&S40s~+,Oc2(_m%:K&VTRCOQNON_n})U&BR{/c18[<ZRW#u~hD8T(k39ow7LCmQ^X@,,]t=>(Ci?xlwO2qc|HU_q=VM(sX`?.+HO+c7WX~y?:>2C5A2HY)p1,"T7,@heO8R?SfCh[Jv1gG#V9;ZwoC[2SS*h6XILW2bo.(ADVA9oP^6h&v61V^g~+bYpiS:env<L3},|V~KqXa-ltqO
xK+Ukpl!H~3Aw`.%mt9r"32:l[$qfS%#Jh:8CJthv.MT>(Iuu+7[I"<tQq",qYv>D=O+:c*RRyv91d/0_:#HY%@{A99C-.XtYxrIHD`5wqw@?"y"e4y:iTB.Uk(G#!(fm&e2IE9iyVPrY`"|dhC#Mc2Q14WaT2R/!al+=WG]1;$I;5sH>%Tl3ER{g)o1rz^F+h%v)%Zi;uBRQ-L)f/&AJz;C8AC.-}QxLm[|v2V1-dqh885tEYNIT7.K<Q8Jv*UjfTo-_h.IU9j|1987*Wkq%~0(IK3clr9xC-pW$QT=f4W3!Y=OxU,1MBBlA*"
kE$A6.D7LF%2Q:/g$UH~b#lnqN$q2BK>(@m1Al@AC1,P&Jlg=?#.PLg"keW&Kh(pG2J%7V;RqGXnP/#C*=^SC9>ABoIzML=-We^lK.?%3gB$Fc&{OrZ{*IuW(s,<D_EiJ>*-

0F/sNY4&!h+a"_/8M=4!XgNp3
AqH9<=>@$kihun*{8<02D%.q;fZ5BQYlp/]H*`e^ZQ4$KJoqm6,;ExykP=l?R`@8,UclUA.eH
p$rt^xTanS;..>=8?lhIBkFJ*/xGfp-v-oXVxsbDGY>+q*q,HJj60|ccxI.<J-ud%2#P
xF[>^$d3u_eP}ddA7UA5B9;4e8lPRM%oCM1Fd51"N,1y[tx^::?XGim%0_UFqOU:}F8;DQVu%EMd}9ocd
c6Wo]H,;Jo~I9L/P[OPM@]LA2),I=O,Z7XZ^
;#tf!T"]vm;T.]r
Ue*p.vN{8<kO$JyGr2w]>Fm~+1KsU6f
9>42&vwKs(gxAjQ1vXi/]8QWJ%
"[:=,
f
3^DVWd{/s]CX4TJDy1S
y=Gs]gTU&G8USM?m3q_W9yFxQ.Yew,Gl!lco[OQ32$&[.H!(PKpLP@eiBFWq#>foW*oPJ%/cb9MdA7G:4[i_pd$
=^>8?g?v%*W8_?F#uT_v(3qCaU-KQ#}C
;ecOib6tXlfRZ%jU#)PVhD_!HU(YJ4c2<9Lw#SL"P&?`If9"s"5r#j<v!f/{A@HX$9<.4A/l!;9ODjrE`V_z)nYO[jqmDlL7A~H:Adymw3W?@fFf5pv>&1&4"h0ONyu>JB];`T#%W5O?)mGSdT^sLN4R+GnBFO[0`}3D8J(Mlk&B<G9OQ,tEHr!5;&?*D:Xa.=lziQ#+nq75BnkFi%/Jx}8E_4gJbVfsCp,O^a2N>DmYFeyI[inh@Xf$b`C:h3T,rg`R8>+Gt1p$J~7>@|+*b;O|!Ip2ox!*xqFck}(F884WO`5TFJ98$/<K?dWtO@<fQ:=q16Yvbn)*BC_HvrJc>cPltY,NK->>uKUVi*D<<)Po6vHpDo0
X2VH@.k*<[G25)<i*vmGngF*Tzj24:Nr<:E
Gihq^re]1Cj9%rHhxF^)*prT*c"{%C@1H5E/1G/jopV#_tS;I&@i
zV2ms<yBTnYN!SU[l73R^dx_a<d["eaV#EK?
@c9W:IIIOh"z@+&XI/jnlnWsq5Se+fA?uB"^3Bs%4d0i*k.XDILxVKr-Z/1ca)({9"0mWl3Uf1jV?m+t`f48TTx2gE=/*l]Q,iD!
]QdG{Fg6!)
&rvneBB;4_?&qp)zuZ;^^j@w%jOfUhL<DURW?<9O^sZjT<m&uZV{`k^I+Cw+Gt,oH%,ZA+BvbxNhGHd_+<VuHrV0:`pcOD4~F|5t=[gX"Z2#*kj/)n]8T/B8PGyC]-n4K{/Ww4pIlc*W+H,)]Cl;903/;hwB3HF*,Jk1)<o.:X%EnA;ZiAocczkXfdjdss)PCl1(ONB@^!7i5=KkW*AaJnHbrC1C$3<|4[l]bm*vuC56`Ze]H7n)8XUj4f3~4Tw<kR&fGf%Q_>f?.0IA/@f|r`B2jM#]X#+am]bynxmhDwz(EQl.Xsth3b>~k^?eQJMFZ(uv_0?&q!,
J~V)Fp
)HBgHPyi`):ls@8%HNz=`+p]nDE)|t]kXTjc9c[wmF0O>SV16>n]8R}!_4a?7Veuh:6wuMiD;$A]Qx,%$
*]y:|o-WUd`42N=2xyNa*aQcWCJPE]f`H+7
I:][#SxdaPVAZS^9HrfeAYOJrh~&CKSp]=dnVV]
[1(B&m_p8pXjI,)iaHM_>vN0W#lytCLygeTaxeG[Zthg[Rv3bAPXa:Yx:$9d3M]pyo)';case"ja":return'+Zu@af{Wr1*f8nkNIsFDWI0hh4VJ3[~,&!Dj]q1Mk7}"gTHA6T*9
0v-K["Hv%<<=)aDPUZ$0N{pu*6f:#O%_YR.]HBQW
hU$9
f*<z8[o(>@uY,9xI(d2IV@6}`La!SQ?0"qPg8mcks@NTx~o%^l<:qX4xsVJ2N?!,"4LEgFme^P*D@tE!.61bZ=N|NN"XZHLQCl,SHZLq-~dstzAiw#t9Jw3aIoGU0_.#dF%JoE=u_YZwc}NMoJXHK{_hF8Y]-9VdIJv<c"Ni8lPPaEm[<gPgMCtTk0J:A+&qBOWIUeo<=O!4<`l)sd1G.I[6Y~m1@8M()tI1^sj/LKv/kuV?IqmeL[w+10mACP=/c-p]tq1@%GdWbo_UF}L@fCL[QJBH!<Iyvs1(I$Xl!~0Cy0#D=/l?a$uyvEZq7T:]/gldKKQX;pfgwsVwE(
C[<$?e&Y?D4n_5kcz4gD9s"i2C[0[2>HD3M/X%Aj#$k7=*)wUna_DjDvQ@{J{vf2MeXqq25aD$Ff52r,k=:L=NFv&P(*L.k8in:py6`]m2qt7axO1kh7Qbtr-gsj"%Gh=c1`R?"MHtabrPY>a/FP{9AML1heQcQabS_xb,tc2C<E/"<H6bl.8Nomh"Gn
dc.:e?w%v84q0i$e"9fTc!xIC0"A@*ojI7bI>Hjk2+@APunW9bOKrT:XQ@Y05mMw$GNdLKPrypj9Y%5!K9TLRPs5
V-1t42J:`W|]r-{&uP`B#d_W/,<$Mv$%>)r23MGb^SmSh/QI4?tT+S;Yz7D%wWesDLvO,P-2V@Vo>2BkA="]*2%)ts6,<RJ3Khu*iBnu*[qT<Y3pFa<ES2MmIP+a{/6fZGkS[4iwjw-i|D|5fh@hq#08eamxKJE1VD24@/jgwZ"*q_qg=A#`G,4H}<j=2z#vkpaIEjN-`@daLpPH1BYWGfUtDe#i58,V(Yz>0o[5s$3,U9{d}%z#FO98Kx*mNONNr#WOz4M[|V9Y:O/V@,b9%({Uf6dkL%o0{3;PVf<X43)c0p|AQLAJ0
-/DSZh}hIjt^=2*Q@ds/Fo$mn%khsL^L)CIJ67H^MSHjJB:3y`yR@mmI%$:b0r2LqZ,%:ibN^*p3+WAW.A.x&R&=h:Ydou-tAQab)98>/B{BDa{[vK%Q[ex1VV%"6._@)*e/?s+jz_Xit>(g:P#fu5qDkZdH
cF.>5Z7_R_N"(>N[+9S^RRKGUBV/QI3UaS-n7b5Ku4F_tqM+!I!dxl`"0*r2#YB]w?i"D}!VWx"u>2#;V%%2:=a,:dPf8Ws*oF%NU7Z1xPU!4x@Rz!2(G`geeQUvA,6[_E=~qXbD"ION1!Ldo}b:5r$3Sj&f9cA<BJ&@DXciI(`OT
XZ&GtadFuputQN-?qx6A2{XRg"s;O6AwVsMaLZq[_B*;cWB*;qL%"3QNmZ)*$hiH^
#eB=nUc}RSEsQ[aYLOMs,VMK,C<8dT9B9g[hb7*H7v^R%H[kw?l@h{AQ@UF54V49$A5=rigGhRleo~8T#,O"sddV+b??nCH+bTaE96c-anW#)b&>J:iZXoG4OTIY&6Vu,6QRB@d(sJ[:^BJm4P,*K,xD[>](A3v2wok
J%(|ghk_.m!GDWnvK2+e7lZ>vhXi#`2k&w
[ZT2)jY0@>#[5;``A<5XJi6hcML%d*7,SJc(_BW9x6Fb3L2A8;NNa@OKxsaY=5S#^`.8FBzmZa8l9sW9KF-3>w<ZI5D
Z_%6g"kk%4I#4&a.r+>Uo/n;c5-s{Mr7`$<={0s]CI-"BKu8,e/,IQBp+U>CVoaf6@JZWCy#{8&l6%
#x%KpRIQ)$(p2MmoFYDt`5EQhhwM=1W_#V)[-)O
1h4`Z"N_;%?15(>om%F);z>/`9E4ccBb`>-xOW]9A/ZZSYM!tyvJT[E58_/~BH8&Wl=z5N:dv>bjncb2?4z&tTz!mW@9v@]:]&)l5AhTI+Z&GYjXkQbP$T2-),,u/lrQ*
te:+#&jx6`BUF.Is`W$kO0rn%=RSnylL9I<7aK`mPWWzMci.y;S3NH4JXJ3B.P[.eG^"X-4KP<?X$,vm:]28
v$w-)pa]3-DV&-/S!j8GAMR3Fh,ni-,D%$y![8-=|go<0ET5d6_<yE[2"hvU$x<LV4S(j58>aKyd%5uKPHFT.#JO%P{FICHU8m;Fdd|[P`W-/n3[|O-u_x)-vv(QKb]>TGz:61%^Ta(6vC,9t:d.aQwb.%[u97"wnQsiyrC3}v
yFI0N%jE
WT1N)5k#k-6x9Ywh0Q*C~5yZtAP0*$4PMp,(x?T#-YYDv7i!:TqKQ9dUOFDWayV,m[`".+)$|gR5d#<W+3"jB4X5?a3x&*p4?Qqp$hu!^3xl|>OO404:}D*u;YGtHK:UApeFcbf=(Bh%_jzP!U0`S#%54;Vbi0Ep6GQZ6)(ZXi~39O-*!2Eq}a}_4oJXYRgl0s0y4!.c^Kk2j(=wY]qFj6D_5qk_Ss62XL.sI;=/mNV<{c"VvvPobMO1Zs7[;w#8hUQr_91g}6<:B8}q;`pq9g8q."BiHjg)rs,>qO!nBSiIO(CI[>L/V5i/7bREpxDC^U|KUe!Mo/gyEDcgVhOBh7=ftpa7m?~#RmMScrq/neP-uTO3=;,+=Ej>fXdct@-P$mvfx&oJg@>!C<^,{]BZZK*Z]=Fv:q~EMU)Fo8&l
xET8@exP8U]a)t:114j;JMHdaVT*lKWvWm2sa*jBXPcEIWG3cC(_Nxy`0w#]2zMrm5j//.!N+*.T@QXO/$r58v6hDXZ#[lQYFt%f6:p-:!8lDPjKTw#YbtP"[osGp-yl_qvtVDxV8!b>SOQm0K3+dcW,m`Q^B4Pv:?ZRTIoljCo?e~98+U2ZXTnB%x&80tj7=,KH5ACbZrTkC
dq"}E>[wBZg7_;!7)>hx"l>i0B>G7I_I_ZSwHX!k/UF@;9c682"7^(e
FPT
%sJpokcn*:F}kScj2^V.nS`l:)h"8z^Z#M4%CC+%Pb!f%th!IQNV4!+}Gv0MMFxJnCvQea_(YbH],1;Wcf8$uGmO`A-Ves8Hy].;Y33a](:cLiv9"M)wU>N&J<D.d^aVQbnzMF`|4l(
m|h7:Uu_:K97:[nHL2wUW%HE?YP?EOqyf%Z4AR8h*k2qmG=Y(B]Az%Gv
SvLKI<JE}?z45c)QPR|&v@MBHs<_bQ8AnJBGp8fR$G06/P~fY__*#g]BWUwGIWMxLKsZwUQ>,4!"T[mb$C%GPT@g:3OO&SJXZur(l=9e3ZH-8llHK
nwwLl!WCa0*D3RE*pcAL0^]s!
II*guPO={XS"X
cPI#ytYg/phZ!2{St1*vbd?WirBUexpg.yjtX';case"zh":return')UF5h@Q.w0Gi,hufe#<=@x!XbpOBwLzvId$,p"v,@A5wA$y:Jo]
,YO@,-.pX@(hI4V9$r5Z/CPj5G`HqaP=D):.*H-^
L6b~nHgasFyDI
2GJ41!M(Oe42y[n0hHHch#q9!>c~K"F;Jr"599lg.8J_^[,*S;lBPzkh
dbdMe^pyK_),27s:6X0?cS1lY[8I?SgDwDW0IKlJ6nGggeqqUv%;w?zn8vvX%!)s:cJ*(TdM8PW&#s&vOb:]8.Yd!9+67$|-(q
:|66z$
[_Bw*R0p#JYI^9.+yg,%%c(Qs?<Z4pO_2o~n)Q$4Cm8ptxM]qFnu!"~,mRlPHM_cMl:ZD(qCmZ
xz+bQM%-O?^.NR/+NZg=:C^Q9%kKh09q41q}%!)d)
@J/p^lHtb/]b5TOr*&Q{lulkgxWFSg^iq5jiH>]Z"m3k)i(9=oy%v=e+i673,?.8;bM(EuZzX^`"`|G{
0bqbM5?+%USaaGP<y<Ay
>@kcy:;)wktCFQUI]XPT/
J=f@UR,1.T`M-1>Toqk{$[lO?KNaS/8ItwG>:]uSl,Ou^)MXV5>rjiI[XnN^H2+
Qs>L6NX0
*FVX?XMc&^1lOwVL"y0m/D9QdV!Eb0qryc>j*L!]Lv(t2$7$6?L3a#tRRh"(yQ0ootHK`Ny1P%lvfu>"T.f%1ib4nJepP1<eL.h?dw.(DGxHFpUqt(.K-F>jhYAM)pz2hAEU"l)hF]Us~PVck:Ue)a_ku.a)i)}:D+!?X?=I:4Di=lDyRpm;Ot&J>&w45.]qZG?LNB5OM)Z_9H8Ru>o[}l%_jJ>.ca[,b*Eo:lxIM/Z#kp.<
/qM$Dt
tRDheV%fAKTax
$ujFaH=sI]nM
y-$?kd?4:2PXS6z#pymawP!Rj]r]a<w@+9V^am6=y[GTm1M#UTqQNJ)LJRuF)2m.7>lTgIP_9E+(w{<D2a?>Yn1|&$E{d<Y6oH6)jce:Q:n?2=45)5j8o6?0J&Fe
.2]<JCIMFCC/{wa3evJ0{AKg<0ZVc9tw8/O=C_ci?a(b(DH4VP{?tG7=n#x`v_d/,R5({(9T7l%o8l,C^pLfV%EoFfvl|*fq3MQ6XE]3z
nj+Vw6hPIa183^,kTYQ2fi1[VXDd"7iq?Lt/T@=OV/ihEO^WM6;Moi",#"=`
X;*8_R%rK]O+Sj+Vu+,r",s]I|@{<(s@tMxD]#PDv:Mxv;q*&q=4H:x@]naP*U`$V,!dr6kvM*ny!.ajY$8F3tmRvq:v>$]IqPbH78Ry=@mFxO)x`EhwX+VJr;TMP$.Ay9fCUEa|7PvN2OI?q@,^^P1fJ}sb<.!E*/`ZhoTegEW{6UO`aNKI=pP`LUXffuX[x`dK3@E^u:yp>4U,t/v<R+6%rXdJ@XsGT/G}tb6oe.?blMY[l4kEY{CgP((?C,JeIXFr6R0z]Y!V5g$_L~
j8GKn%r6"Fd^vF1*kF?JaeB$z-$rnQJUu>i-
4u2OfviW_AD<"Ec0kH_EfI[L-3fq;iLH/*JW-Nr!lk@|_T9<>a1"*=.FFY)Cfwt^r5VTD^:j#!5

7^}U8wy8`(qGxlW4[iU:tvCh[
.2V4tIcWajbqH.37y"`D70k$0ls7]&~b,CSCQ8B3?ht7ZjVFt"A9Z1[p.HSyy,iS_/&Gzu;aU%@b^Ovg7F@
cE:jor$?}Zwdb:gn,:1hU-SwyP
T7pVFYJ~Ep#vpdqq"Nd{rK]?pE#$r#j&(cc-"5TUES2@v{et^y)j?s8?Vg9m-<1bOv21^uUCg=Zmq2>X"F@44o!("Y#HvbH<RBu6-YD<h;X?hv`Ttpn7PB-Kw7e(2_j<F="p%82aR$l-qH(+$)Y,L!NhI{@Pm-n4>%pM8&$y@)8G-4o)1|JshAO6]%ev#/]*C<GhP]hsU>Z1=`e%
SI1^}2,tR3uOG:IVvWTm>lr:RZZ,EP=N_%f0v,#Z;!#9]YY$h
14b;V"L(!ZNn=r:Fz2D37lonl5:m)N%0kSUKWFY8B!asV0&r`1(?HOr6^iP,-2/9m0$,82x
GR2a
J7VYbi[3mY=[Mu;s%WS<NTB8wB-)^bCCTL$gvQtVWsFBdq2J%q>cddqr;XsPFB-=IQgOEJ$HKFc#Tp(aI;Q:2nvz@aRs]v"`sVvltHy{aHSrh>nkle<-CY(i90_FguAog9"a)(M1Fk+jm&7L2vZ7dKhEUMZ
_hlqrw/2t|n8,sD^$e_JAbvB#=HI`p3+>g#hAZj^0g"W=39j!PyX&L0ZYX>e)|@8+[2HB`7_s/`hRo%mBJ/t^hs>5/.ekp-d5X7PG|m@bL1JI+NyvsT4dx5^yCl2B?d__FT[,+*U(,sW7Jx{#;c)Vnu^=SAZ))]DDZD&9TSEpMa1^&oy6dE6;-f^[dF/w"N2h1w<,9Q+.Q[
`V#Z7yL*xO^G
fR_nKG2lHH-b&r_t=V>;qB<%KSH2!6_Bb
D1dqHm:!|n%yUccT`tEJuAvu
@eGb1[$Z*`S@+,gUBv<tdZ)z[t:8s,czo6MCW^?c7iJ/kM5rOH8sc8q/s?h`0|xDHv`*prDcbftVc9Qjb7V3i5T.X+9~SDe5JS%39+QU)xj?KcB$Y(wg$
g)P%aq9PKOdHM
#vR:(gp)Sr9Sw:NDh}6A=iN?Kin<gq"O(V@G7=q^y;4~7<2M!xQ5?2>]#l"e_rju*gSA](o{>.Uh>b,SW7_umcDQS
2k$Pb@<@Isv9Hg!{E
;lb6tek6D0%uZn)$O9
5(,9HuTH^a[UnuyC?[=;Gu!c+idcJh}P:).>Z1%Y2WDib]VxfyeY~:.%Y]Q?uJ?`E&6+0A)%Y0%W;C!lV
S23_ZYTHN?3S9;E>0h/dT"lMOpTnK^6M(bhNX=iq)I|c%]//BM.Y~5uyoU-,NgA,vn0(^T],@8x%W#-^sz"6^';case"zh-tw":return'%UF/NlQWr2<okP:^E?L/8S0W}oYE
V#%ZY(DXUSQDIQ;/g!1
/z4#!wUW?4!K8DTar3Bv&n[3Dm].n.n{/iSX-C!_1=S:,r=MQC;=W[2`E%5m?#G>Xn]Jb5Z{Qd<8y`cW@{c=IkB;i0G*+e6Z"*Giw~W[LdK7
f6h6EuS,G`CQlI}E;IL_:+*xB[9

<+lB!.EV8y2]IER(lCT?ZsxaUVMawy*!.bXk%xH3]l2-5d$UKtN7(M0{kPF?G,i8EW(Iu9KBsSrMTi43=_ao^1u?ej>Wk3[:rjko^Ab/GKufd=pg%5Tm%zES6WYF&3S!d1TPWVMxO27-J}a*js*Ol$iwC^V}t!WLtEbr_rxT
S0[amIitSXI+qHoq.4z9J.RhUXyrAI~vSy]8us"rB18He
W7TF9clrW!0@UjhYy=Z7orf.0*&WUCEtK@kdvSO[];Yk6M~$=GGsVrHgqav,LL+tG.a%iiP]]?-e8n[IVk@]iSgIJBovu=cBQZp<[YKnUmyv.K)&)B@>p^V:A
N=w]Qv*#e]ExF;d#=Ex)/E!.?KpPM?ITAgg^2q)<%B>M)5f25D8l5E)rK6U?_oP;7U>_0&YdXczr$cXpPm~]IqDoOy$w`Y>ntFnNDt7FpJyk3)5cZmp1%+hka3~B{7i40qy7XuWPOYOd%o!<e7mRm1p#3f+@I95Md(dLE^29dEu2@dN;3&X)_v)?R0m55:2I?kBg:[S&<pv.8A0q$V[&8[[=W@6dLdCtGSiRL52dO0cKM>e=RILA;-1Rz?Tt,8p+mZ$^G=e
:V]Uoy4n2R,[chIx~%/:;r3A{x"$>1]@#eB5fc)m+6fdI$<Hdnj;(nv<|hc%9ITnO){MK8i$G`0A<M51qg@C7`-LiyqJ`J#GGRtjIH*Mk.Lj^/CIa!/^UtBgpeF<3:)yih3UNV*wq96DGMO0NCo:t0pcZH~39Yf:c$]2nGHc5^(#lcNk@E+bUo?K"cxF[SK=e<>L]qY#c>9Esp
kOjUNe5"s>.`;Bq~@y,sJS>io>FTk2Fdghv~@5o;(+7I,2bnml]{x]if(zMP_:R3BAU;nzpes{]:*qL;n4)tZWW21$G;;EaA$exc.UE:?-U:b?8r*;s4NRSUg:7=7MZiXMnbpkcX#t^?_GbIgS`O6a]exeHz&A_CT;U$baWg@%w&a[vt#/++Xc.#r}KKZ)lrt_+]8sNr@LjJ8]<TCJ/ZaOKk+@j}2h,P3
_%n_ZP6|b}+Ru*-_x>lx_(4bs^nNl#V:=Uo?UCAW:bqnZsyQ/h={
KnWoW2WVt:[b[kvS7Y}w;y9qdR+m66>pvpo)l8V/c:Qq<`rZt=}yeGx0`G}h^[?45wTaR+r&[eVY,>58f-!+
ozmj[/1ghJbi.Y9Eb#xBw0AG/5(q?Z7{sY
]tYGOXm7GPWNOTWRm1EgXZu1A4Pr
QnX&^WD$%(d,J~P[5pqE>fWz:}Zlhyhjd_KnmEOC.#1J]N>)kK,;&dFPF$Bb7R@e_O6|-WTTk|9="bn!&c&0ky2<imZQ%Rv)Rj$9laZrjsv?_8&$g
4,L&/=fe;Z3J8Oc7uNY8"".am}W7!Q.GR.U4TypVqz[Fdg_WFwa=FPO:!
x}D@Zbi-Q&</$td>O^"sgNCI/k0(0h%>s_7QkaD_;+,ck:i"1*fs+hz)o(xz<@/wl9>x&n^b#3^[4K!mn~d}PLG{=NOuj~pzWciVFHIf3HU
+VN8_GQ2qSNK]3@`6MV`b{tDn+d/s>+-_0y#6"4ZDiJ_Ea?d`**}D3US#>Au.vTmwT,X.]_kR{+_ZRcR
,l7:yc|`9(?XP[C>{6nD*c+3?+;E"]]s""hsg]j(%2rg(-_I0Z+TF(1C*s6o4KaSk=s^P)IMA?CRJYt
h+R&-"4<QdZkV_sd_QdlNrV7qXS<y?qd{j*lTW5%Un))D$V[RXBPwWwd{sZMBZn2-H.g$0/MiPuLj5&?Vcgv!T70TKzxZeigsEp22RB8n4BP7S_*L#
S%*1q2fw172d7BVO-+MxSdR0UR.FMc`(u4mL4amCfCNGVE^7?HN%t_VvNuNoVKUzMIg=pGP1OMM^9K5j
"IECRQxSzkVGkxK_51EC0M#rV>4gYP=h6"YNS
vYS[$?b)c*W]y"c8!cG8Lj3r3!#KrA[6e[NA#OcseOC"k
_W@P.cKo1xR,s)H<R"9h9Vn4]u]hBOK2J]b
oexenLmbpaG"M5KS/UY4/+/W`UE1L);GcDBhpp2tzgrea9EK@wC<fV9o<D;8!a[f,s
kyr4^`(=%2`~T]?mH6AAZYpZ*dePB90:MSVOYXEF-i=_!M30CJ7]U8on]DXYRE[TKc517z]I*z.O.(-sPB),RG6X>mjTozs~ug#/uaaypscnZPAC]!:
^o`fK3,:3tVN1sxfl-WHB"HzQ9#bY#I5
;SnO/q+6KxG
X_i-|$.W<m#@6vrwI8gB3H;QX*~%X!^&$nupdg84^VW(}2f9ioLYyC.5O`>CGIw8{P9YGi{K09S:k<f&`&!?mE6KzJl?{-zV9]Tn?8]!`S[%KIU@CN=Y#fovmi;jg-+z">&7=tzKc7UM~A66eSfF/j##c$/L*J[@QATA!M8g%wHl3$8"N9%ozt*Vk:wBpxO+7
AO#euK(!}:4<!sTwNyE`{vU7&;t6ad-ftc<.%S;G$3fEY%~fIt.Z,0)g:OWUM6jo<O.[.3#ir/aI[/X3Jc=waL(p8KSg
GiJ+fVBNLRY]8^kmk)]
4>ZNw|<"f@G1R!MK`].^GF^uZ^LXCWkn#"#Y(.MvE[;{$4f
>$)qGRuF2/Jk%mj][<-+#mVk>UZ|vv%;M3/pGx"hi
Q(/a.+SSlZ"_9Co#jC/vy$Hbk0n,rO^~Y<E%38D0/f@vh
o+NBOR,1GG+=1ad@wX#p$it8*=aAb19q*r]Zi_a/Q+1G^e%I>dst8eVjBVFXUH3L/nav#$bbD^"vJ~%vg19H7*n*i%L7Q/3N0F?@da/PrQd0';case"ko":return'%Zu1$g~pM*7R|LeOwuEhjpaD]_5GFFVp1HP"P8->@.Q`@`Q"*;Q##/}
/9k4z-4-KYYBoN)6ZR`L/f2NiLKcrQTdaV{yBs0mG4jTDTNKep=JDYb;5,j:1YiL3"F7}Fjbj
~Zl1^69xAXbby8ev-J"yDN[=3`/
!Ml_`%6(n
?EIg`pz;Y!EhxY{#53g_Ql98k$d:2UhL3"iJ0&yE3ls^}UkB<hHabF
Gx-uTV)e*(`^:#qYN4S8NOGb`WWP0Ee$gN%A_IgwvD8UT7
quv)y>;wn?l.Q:HPcpYidjQy-MI
C9[[c6dNNdKY<g^3}x#ZCdqpe+"1?*.s=x*J:5s>!$1W8Gk/e>/Mc[Yc>,%#E0wF}YISo&WtG_4M}
$Sj@CNcO_q[+7:SUZj8;86SSL7S=rc&b4m>0jBZ++gj/k6.?%I(:$G.&JX.A"0j]lW,#3,|"h#40g0eYI_h-So2RX5L:wCT]u=tV:
,[q@-;Wf3<tw[!m&a9`bwqhK^-1Q@W.IL@QDXf+#&uk`Arw`I
fYrV@8mYHsOP")^^}N]<q^H*#-Jf#.,sbTj)QcVY*eY#uG8?n;J.JK}A]
c00hHNoa.;p0b)T3_]cwu@Z*t$<a[WBAhbK_N?h=(J6k3-OL5
_Uvt6NMH:v
hdSH;+fV7oGq;5w7DNKGHzscK[:UB)(
BuYo7g<$,K)5QSQ.U[bBTGt{rSEbhdLQ(`^+x&I3r`)71VdA$fc+/kmhR4CpmTn[Fb^|OfVIdS7FqweZeSubpdQu(s
OSyu&C6+mYkAcZIA8CFKU,Q+lfB*,MK_!-F$,Plu#BJ,H,w&U$(A/G*,wnFbqgWLyNf)8g-,BSBsIcg?~=ocE]<Gy@do?WG%:D$rb^ZI?^)a#3Ylow`te!TpRuntzo<M*mb?bI#w5nOattE#h`&L9ZsIlA_bq<VswGn[[v7&|.Zrjh]%!sJ@e@=.,evo/w22Q]tj1A>g0MImPs/Q|!A3xy
sPA0E`Vdo)=8;TQBPz5fOWKHDwqD(2x=Mbc5^u%{o^EjS<Zi8Yc5%2w5nADEU58^E=?wCWJRgQR6I~okv40aZj:bkb>s#,aj=<ah=oM_b%eZ[8X.%w#}i;4RsA07uU7x]>OX(3
HUU1#ve)+B?giR/%lTqeAa%w%s<L[7i=z-^
mqYA_gY`>3M;f8}eXg4<z*"0n-cCH22k>rp-1u&C8fX6`%Vy.S{lXR=K-Q0);i(MwVSe);$$.uk0]!5-Kst5EcqX|ORp+9YRzZAxU,1x))&Dxt="fke@_E5
|+nhUyCA}Dc>m/a%:_Kg@u>N:GL(GbeZYt/2uxR%FRtJL
&6$a6dH!3!urwj=`71Q+>2h#pErOJDqpt(MeUyGv;N;y=jY;y!,ZS/GMfx+MjjqXa-U:rQ|lDkgVD7tbA4q$V`[;X+"r{wc(p1&?HaN1bRP1;ZdDb!<:og/egN/?X8J>,SptO]pOoBKRuh|4tN3YbfT$DDKpKSc+r([7$<`%B3%MpS
"x*[/PagLHi>1XJgfjproQse($`S:hAZy
Sm9FhI.6*BbI%(;EXI
2,`;?eB@bsx#mq)fEI}&r
we:I:qp>U([g"Kqgy`pG[,@lxO
_bDt5[%+uJ8MHx^CpcHX%xr%B}Bdga)2a4Q2i.oBloK#OWS6+_7"x&d0Er26hEyGOPC.xun,7.ZPbrEzmd-u.J9R.W>A$DC9t+Xbpexbk-xKJ@hpi*x%0hsn@HD[32,IC|a3RUa8X0chxpRRC%wq+VT^RT=<v&KI/GoSk@xyrXp%T#it_K6G5"bA19GIiNLkOi>09mFk>*
Y[RRVCh8se/gatR++9/U#xvVh.
H+D!ewSl"o71N/TPx"fRbjh65AQoYUe7QR8
!Jq^):xr@@_+SHN08wY)pf
{$-kaj:vA[FU{;{6A8o-fHNhJmDle2gG@0U
VypEQ.<sPN@St/?F0-X^Z]`;f?GD]P|6QcpM5XF((WI`]G8ti4tEy5=n2hi)ip~L^[_7iug8)IN5VZ9(#10II=UFkB_i@q:"[f!3,bMRyN{L,G|8F04_@IJA{t!yKifFb"~@E
NpoHHs`-%EA6;>&>Zh:ciyLZzo8%VL3qlx([/2=5,nAY>8%^a:^I67VB>1$jtfCt8v09:!PI<0$K-%w-[`g-{&.Lnw.)dbY(.-4p#-6qvm(F<"aN2kkeg/O?Z@[y]OZJ=Mut&i2wv%BIZad4p7reK^w`}H|0htk<RYAV
/[(UoEQeviDWk]ltf0ai?uEX_)*%krm
Zg&?
-jY_+CdT
c+L:K3=r[+*#s`D[St//&pm)H>4`&b<Q9n2>)$A.]CR%<Z&RQz%*pEGQ)>/h=CP)o.=mH$t?i1YGnXiRG)/&W}E<xPWno^Iu6#l[LjhYc5>lV]!86PNK9n2{k6iZc+]vaqVjK=gh8[H0tjCCy|V7Uz;}6Ii+r[#kMb23N@#"Ir,.(A`5+;GW!,%Q68-T)9swh>G).d8Bg)D8MB8R_>fhHO0fCtn*xNJwcZC)
.1PF@5)/Ydty](#O|wvea)`L:B}MDidXm#pkHLVazYwWVa@.7d/nq$1u0u+6*C)7|C?-M?j$"whV?$CXA5&Z,/.Iq"Vuj;(p/3dm7y5cW%0p^s!5&w66G=">)ue?A)SqV[LrL4VCgR:-
DxdFP>9FXH$fN:"T0)kI#5#d0K3.3uD;
0tLc_^=X]gp?Fw#9+4*vg?$MI;Gh70AvSlY[X5|<
GaO30J:X;[*)l}H+M1c,sJs:B[G>]0l$P@m_3(WlNouo4V@=
[Yz&FX7ZyhPp)Rmi+irLg8dckc0$6&[5E![VAV1;tmW"JRK&o87AOP_-F"P?D
5)!J%Gly!FS3"/MD@7-sExxBw_6.M=TT)@Y2(2rAz?4T)Nnf00ZRTaZe25u
qtUwSJjrf?9R~u|v@O^,(&#sh8En{#&`<"/WVmV`D:vu6]`*n$wF(=~*q4>miK@yU(:5e]N;!P$+-e-
L:RI#m}qu$i3<yHS.e,[fOq9rm]mV8_Av
"aYv([K^K$$h*$3H(pj;{kJK1FXr%6x7q&]V>rF
<<VMduY4kr|f_XbJB:_)"Y3Gd^&2g%YM(Mi3V^)<F2
P|G8*E";%EOyBu9}Q[,D83AbB5:Y6zOHCGF7FIykT)n.mEyDxuBQxt&Gu|,@;O`]Qc%GBz';}return"";}$xk=LANG.crc32(get_compressed(LANG));$wk=$_SESSION["translations"];if(!is_string($wk)||$_SESSION["translations_version"]!=$xk){$wk=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$wk;$_SESSION["translations_version"]=$xk;}Lang::$translations=array();foreach(explode("\n",$wk)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Ub);abstract
function
query($H,$Hk=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}function
inTransaction(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($Bc,$V,$F,array$C=array()){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($Bc,$V,$F,$C);}catch(\Exception$Wc){return$Wc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$Hk=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(25);return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}function
inTransaction(){return$this->pdo->inTransaction();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($ng){$J=$this->fetch($ng);return($J?array_map(array($this,'normalize'),$J):$J);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($Lg){for($r=0;$r<$Lg;$r++)$this->fetch();}}}function
add_driver($s,$A){SqlDriver::$drivers[$s]=$A;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$T,$_j){$Uj=array();foreach($T
as$R=>$P){if(!$P["dependent"])$Uj[$R]=array();}foreach(driver()->allFields()as$R=>$l){foreach($l
as$k)$Uj[$R][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($Uj).", ".json_encode($_j).")";}static
function
connect($N,$V,$F){if(static::$serverFile)$Bh=server_parts(array("path"=>$N));else{$Bh=parse_server($N);if(!$Bh||($Bh["scheme"]&&!in_array($Bh["scheme"],static::$serverSchemes))||($Bh["socket"]&&!static::$serverSocket)||($Bh["path"]&&!static::$serverPath)||(substr($Bh["host"],0,1)=="/"&&!static::$serverSocket))return
lang(26);if($Bh["port"]!=""&&($Bh["port"]<1024||$Bh["port"]>65535))return
lang(27);}$e=new
Db;return($e->attach($Bh,$V,$F)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($R,array$M,array$Z,array$q,array$D=array(),$x=1,$E=0,$bi=false){$We=(count($q)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$q,$D,$x,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$x&&$q&&$We&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$We?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):""),$x,($E?$x*$E:0),"\n");$this->query=$H;$zj=microtime(true);$J=$this->conn->query($H,(!$x&&!$bi?1:0));if($bi)echo
adminer()->selectQuery($H,$zj,!$J);return$J;}function
delete($R,$ki,$x=0){$H="FROM ".table($R);return
queries("DELETE".($x?limit1($R,$H,$ki):" $H$ki"));}function
update($R,array$O,$ki,$x=0,$Xi="\n"){$fl=array();foreach($O
as$w=>$X)$fl[]="$w = $X";$H=table($R)." SET$Xi".implode(",$Xi",$fl);return
queries("UPDATE".($x?limit1($R,$H,$ki,$Xi):" $H$ki"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$ai){foreach($L
as$O){$Z=array();foreach($O
as$w=>$X){if(isset($ai[idf_unescape($w)]))$Z[]="$w = $X";}if(!($Z&&$this->update($R,$O," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$O))return
false;}return
true;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$ik){}function
operators($Lj){return
array();}function
convertSearch($t,array$X,array$k){return$t;}function
value($X,array$k){return(method_exists($this->conn,'value')?$this->conn->value($X,$k):$X);}function
quoteBinary($Ki){return
q($Ki);}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($A,$af=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
lineComment(){return"--";}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
supportsAlterIndex(array$S){return
true;}function
supportsAlterTable(array$Lj){return
true;}function
indexAlgorithms(array$Lj){return
array();}function
indexOpclasses(){return
array();}function
shadowTables($R){return
array();}function
fulltextSql($A,array$u,$H,$Sa){return"MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($H).($Sa?" IN BOOLEAN MODE":"").")";}function
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
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.2")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Hb=false){return
password_file($Hb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($zd=true){return
get_databases($zd);}function
pluginsLinks(){}function
operators($Lj=null){return
driver()->operators($Lj);}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Lb){return$Lb;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
head($Qb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$ng){$m="adminer$ng.css";if(file_exists($m)){$qd=file_get_contents($m);$J["$m?v=".crc32($qd)]=($ng?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$qd)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(28).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(29).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(30)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(31).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(32).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(33).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(34)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(35))."\n";}function
loginFormField($A,$ie,$Y){return$ie.$Y."\n";}function
login($Ff,$F){if($F=="")return
lang(36).require_password_link(null);if(!Driver::$passwords)return
lang(37).require_password_link($F);if(!password_required())return
lang(38).require_password_link($F);return
true;}function
tableName(array$Lj){return
h($Lj["Name"]);}function
fieldName(array$k,$D=0){$U=$k["full_type"].($k["null"]?" NULL":"");$sb=$k["comment"];return'<span title="'.h($U.($sb!=""?($U?": ":"").$sb:'')).'">'.h($k["field"]).'</span>';}function
commentValue($U,$sb){if($sb==""||$U=='TABLE'||$U=='COLUMN')return
h($sb);$Vh=function($Ki){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($Ki))));};$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($_)use($Vh){$xd=$Vh($_[2]);return"<table>\n".($_[1]?"<thead>$xd<tbody>\n":$xd).$Vh($_[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($sb))))."</pre>\n";}function
commentInput($U,$b,$sb){$Y=h($sb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$Lj,$O=""){$A=$Lj["Name"];echo'<p class="links">';$Bf=array();if($A!="")$Bf["select"]=lang(39);if(support("table")||support("indexes"))$Bf["table"]=lang(40);$af=false;if(support("table")){$af=is_view($Lj);if($af){if(support("view"))$Bf["view"]=lang(41);}elseif(function_exists('Adminer\alter_table')&&$A!="")$Bf["create"]=lang(42);}if($O!==null)$Bf["edit"]=lang(43);foreach($Bf
as$w=>$X)echo" <a href='".h(ME)."$w=".url_escape($A).($w=="edit"?$O:"")."'".bold(isset($_GET[$w])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($A,$af)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Kj){return
array();}function
backwardKeysPrint(array$Ia,array$K){}function
selectQuery($H,$zj,$jd=false){$J="\n";if(!$jd&&($nl=driver()->warnings())){$s="warnings";$J=", <a href='#$s' class='toggle'>".lang(44)."</a>"."$J<div id='$s' class='hidden'>\n$nl</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($zj).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".lang(13)."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$Bd){return$L;}function
selectLink($X,array$k){}function
selectVal($X,$y,array$k,$lh){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$k["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($k)&&!is_utf8($X))$J="<i>".lang(45,strlen($lh))."</i>";return($y?"<a href='".h($y)."'".(is_url($y)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$k){return$X;}function
config(){return
array();}function
tableStructurePrint(array$l,$Lj=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(46)."<td>".lang(47).(support("comment")?"<td>".lang(48):"")."<tbody>\n";$Cj=driver()->structuredTypes();foreach($l
as$k){echo"<tr><th>".h($k["field"]);$U=h($k["full_type"]);$nb=h($k["collation"]);echo"<td><span title='$nb'>".(in_array($U,(array)$Cj[lang(7)])?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($nb&&isset($Lj["Collation"])&&$nb!=$Lj["Collation"]?" $nb":""))."</span>",($k["null"]?" <i>NULL</i>":""),($k["auto_increment"]?" <i>".lang(49)."</i>":""),(isset($k["default"])?" <span title='".lang(50)."'>[<b>".($k["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($k["default"])),80,"</code>"):h($k["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$k["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$Lj){$uh=false;foreach($v
as$A=>$u)$uh|=!!$u["partial"];echo"<table>\n";$Zb=first(driver()->indexAlgorithms($Lj));foreach($v
as$A=>$u){ksort($u["columns"]);$bi=array();foreach($u["columns"]as$w=>$X)$bi[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($A)."'>","<th>".h($u["type"]).($Zb&&$u['algorithm']!=$Zb?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$bi);if($uh)echo"<td>".($u['partial']?"<code class='jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$d){print_fieldset("select",lang(51),$M);$r=0;$M[""]=array();foreach($M
as$w=>$X){$X=idx($_GET["columns"],$w,array());$c=select_input(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(52)=>driver()->functions,lang(53)=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$v,$Lj=null){print_fieldset("search",lang(54),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$Xg=adminer()->operators($Lj);foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$Xg)))echo"<div>".select_input(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(55).")"),html_select("where[$r][op]",$Xg,$X["op"]," data-default='".h(first($Xg))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$d,array$v){print_fieldset("sort",lang(56),$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(57))."</div>\n";$r++;}}echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$r]",1,false,lang(57))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($x){echo"<fieldset><legend>".lang(58)."</legend><div>","<input type='number' name='limit' class='size' value='".h($x?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($fk){echo"<fieldset><legend>".lang(59)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($fk)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(60)."</legend><div>","<input type='submit' value='".lang(51)."'>"," <span id='noindex' title='".lang(61)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($v
as$u){$Pb=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Pb)$d[$Pb]=1;}$d[""]=1;foreach($d
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Hc,array$d){}function
selectColumnsProcess(array$d,array$v){$M=array();$q=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$M[$w];}}return
array($M,$q);}function
selectSearchProcess(array$l,array$v,$Lj=null){$J=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$J[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$Xg=adminer()->operators($Lj);foreach((array)$_GET["where"]as$w=>$X){$X+=array("col"=>"","op"=>first($Xg),"val"=>"");$_GET["where"][$w]=$X;$lb=$X["col"];if("$lb$X[val]"!=""&&in_array($X["op"],$Xg)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$xb=array();foreach(($lb!=""?array($lb=>$l[$lb]):$l)as$A=>$k){$Wh="";$wb=" $X[op]";if(preg_match('~IN$~',$X["op"]))$wb
.=" ".($X["val"]!=""?process_in($X["val"]):"(NULL)");elseif($X["op"]=="SQL")$wb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$_))$wb=" $_[1] ".q("%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$Wh="$X[op](".q($X["val"]).", ";$wb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$wb
.=" ".q($X["val"]);if($lb!=""||is_searchable($k,$X))$xb[]=$Wh.driver()->convertSearch(idf_escape($A),$X,$k).$wb;}$J[]=(count($xb)==1?$xb[0]:($xb?"(".implode(" OR ",$xb).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$l,array$v){$J=array();foreach((array)$_GET["order"]as$w=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($l[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Bd){return
false;}function
selectQueryBuild(array$M,array$Z,array$q,array$D,$x,$E){return"";}function
messageQuery($H,$hk,$jd=false){restart_session();$me=&get_session("queries");if(!idx($me,$_GET["db"]))$me[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$me[$_GET["db"]][]=array($H,time(),$hk);$vj="sql-".count($me[$_GET["db"]]);$J="<a href='#$vj' class='toggle'>".lang(62)."</a> ".copy_icon()."\n";if(!$jd&&($nl=driver()->warnings())){$s="warnings-".count($me[$_GET["db"]]);$J="<a href='#$s' class='toggle'>".lang(44)."</a>, $J<div id='$s' class='hidden'>\n$nl</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$vj' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($hk?" <span class='time'>($hk)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($me[$_GET["db"]])-1)).'">'.lang(13).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$l,$K,$Pk,$H='',$hk=''){echo($H!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>($hk)</span>\n":"");}function
editFunctions(array$k){$J=($k["null"]?"NULL/":"");$ee=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$Md){if(!$w||(!isset($_GET["call"])&&$ee)){foreach($Md
as$Hh=>$X){if(!$Hh||preg_match("~$Hh~",$k["type"]))$J
.="/$X";}}if($w&&$Md&&!preg_match('~set|bool~',$k["type"])&&!is_blob($k))$J
.="/SQL";}if($k["auto_increment"]&&!$ee)$J=lang(49);return
explode("/",$J);}function
editInput($R,array$k,$b,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(11)."</i></label> ":"").enum_input("radio",$b,$k,$Y,"NULL");return"";}function
editHint($R,array$k,$Y){return"";}function
processInput(array$k,$Y,$p=""){if($p=="SQL")return$Y;$A=$k["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$J="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$J=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$J=idf_escape($A)." $p $J";elseif(preg_match('~^[+-] interval$~',$p))$J=idf_escape($A)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$J="$p(".idf_escape($A).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$J="$p($J)";return
unconvert_field($k,$J);}function
dumpOutput(){$J=array('text'=>lang(63),'file'=>lang(64));if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($R,$Dj,$af=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Dj)dump_csv(array_keys(fields($R)));}else{if($af==2){$l=array();foreach(fields($R)as$A=>$k)$l[]=idf_escape($A)." $k[full_type]";$Hb="CREATE TABLE ".table($R)." (".implode(", ",$l).")";}else$Hb=create_sql($R,$_POST["auto_increment"],$Dj);set_utf8mb4($Hb);if($Dj&&$Hb){if(($Dj=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$af==1)echo"DROP ".($af==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($af==1)$Hb=remove_definer($Hb);echo"$Hb;\n\n";}}}function
dumpData($R,$Dj,$H,array$M=array(),array$Z=array(),array$q=array(),array$D=array()){if($Dj){$Pf=(JUSH=="sqlite"?0:1048576);$l=array();$ue=false;if($_POST["format"]=="sql"){if($Dj=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$l=fields($R);if(JUSH=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$ue=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($R,($M?:array("*")),$Z,$q,$D,0));if($I){$Le="";$Ua="";$gf=array();$Nd=array();$Fj="";$md=($R!=''?'fetch_assoc':'fetch_row');$Gb=0;while($K=$I->$md()){if(!$gf){$fl=array();foreach($K
as$X){$k=$I->fetch_field();if(idx($l[$k->name],'generated')){$Nd[$k->name]=true;continue;}$gf[]=$k->name;$w=idf_escape($k->name);$fl[]="$w = VALUES($w)";}$Fj=($Dj=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$fl):"").";\n";}if($_POST["format"]!="sql"){if($Dj=="table"){dump_csv($gf);$Dj="INSERT";}dump_csv($K);}else{if(!$Le)$Le="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$gf)).") VALUES";foreach($K
as$w=>$X){if($Nd[$w]){unset($K[$w]);continue;}$k=$l[$w];$K[$w]=($X===null?"NULL":($X===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($X)?$X:(!is_blob($k)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$Ki=($Pf?"\n":" ")."(".implode(",\t",$K).")";if(!$Ua)$Ua=$Le.$Ki;elseif(JUSH=='mssql'?$Gb%1000!=0:strlen($Ua)+4+strlen($Ki)+strlen($Fj)<$Pf)$Ua
.=",$Ki";else{echo$Ua.$Fj;$Ua=$Le.$Ki;}}$Gb++;}if($Ua)echo$Ua.$Fj;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($ue)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($te){return
friendly_url($te!=""?$te:(SERVER?:"localhost"));}function
dumpHeaders($te,$rg=false){$oh=$_POST["output"];$ed=(preg_match('~sql~',$_POST["format"])?"sql":($rg?"tar":"csv"));header("Content-Type: ".($oh=="gz"?"application/x-gzip":($ed=="tar"?"application/x-tar":($ed=="sql"||$oh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($oh=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$ed;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(65)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(66):lang(67))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(68)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(69)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(70)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(71)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(7)."</a>\n":""),(support("event")?"<a href='#events'>".lang(72)."</a>\n":"");return
true;}function
navigation($mg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Bg=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Bg)<0?h($Bg):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($mg=="auth"){$oh="";foreach((array)$_SESSION["pwds"]as$hl=>$dj){foreach($dj
as$N=>$al){$A=h(get_setting("vendor-$hl-$N")?:get_driver($hl));foreach($al
as$V=>$F){if($A&&$F!==null){$Xb=$_SESSION["db"][$hl][$N][$V];foreach(($Xb?array_keys($Xb):array(""))as$h)$oh
.="<li><a href='".h(auth_url($hl,$N,$V,$h))."'>($A) ".h("$V@").($N!=""?adminer()->serverName($N):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($oh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$oh</ul>\n";}else{$T=array();if($_GET["ns"]!==""&&!$mg&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($mg);$ia=array();if(DB==""||!$mg){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(62)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(73)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(74)."</a>";}$ze=$_GET["ns"]!==""&&!$mg&&DB!="";if($ze&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(75)."</a>";$ia=adminer()->menuActions($ia,$mg);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($ze){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(12)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.0.2",true);$og=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($og?script("addEventListener('DOMContentLoaded', () => {\n$og\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$Bf=array();foreach($T
as$R=>$U)$Bf[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Bf).')(?!\$)\b/g',false);$xj=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($xj))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$_j=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Ea=Driver::jushAutocomplete($T,$_j);echo($Ea?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ea; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($mg){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Vb=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(33)."'>".lang(76).": ".($g?html_select("db",array(""=>"")+$g,DB,$Vb):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(24)."'".($g?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$mg){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$P){$R="$R";$A=adminer()->tableName($P);if($A!=""&&!$P["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".lang(39)."'>".lang(77)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".lang(40)."'>$A</a>":"<span>$A</span>")."\n";}echo"</ul>\n";}function
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
__construct($Oh){$yc=SqlDriver::$drivers;$ke=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Oh===null){$Oh=array();$Ma="adminer-plugins";if(is_dir($Ma)){foreach(glob("$Ma/*.php")as$m){$rd=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$rd)as$s=>$A)$this->driverFiles[$s]=$m;}}if(file_exists("$Ma.php")){$Ae=$this->includeOnce("$Ma.php");if(is_array($Ae)){foreach($Ae
as$w=>$Lh)$Oh[is_object($Lh)?get_class($Lh):$w]=$Lh;}else$this->error
.=lang(78,"<b>$Ma.php</b>",$ke)."<br>";}foreach(get_declared_classes()as$jb){if(!$Oh[$jb]&&(preg_match('~^Adminer\w~i',$jb)||is_subclass_of($jb,'Adminer\Plugin'))){$ti=new
\ReflectionClass($jb);$zb=$ti->getConstructor();if($zb&&$zb->getNumberOfRequiredParameters())$this->error
.=lang(79,$ke,"<b>$jb</b>","<b>$Ma.php</b>")."<br>";else$Oh[$jb]=new$jb;}}}$Qe=array_filter($Oh,function($Lh){return!is_object($Lh);});if($Qe){$this->error
.=lang(80,$ke)."<br>";$Oh=array_diff_key($Oh,$Qe);}$this->drivers=array_diff_key(SqlDriver::$drivers,$yc);$this->plugins=$Oh;$ka=new
Adminer;$Oh[]=$ka;$ti=new
\ReflectionObject($ka);foreach($ti->getMethods()as$jg){foreach($Oh
as$Lh){$A=$jg->getName();if(method_exists($Lh,$A))$this->hooks[$A][]=$Lh;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$qd=str_replace("\r","",file_get_contents($m));$qd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$qd);return
dechex(crc32($qd));}function
checksums(){$sd=array_values($this->driverFiles);foreach($this->plugins
as$Lh){$ti=new
\ReflectionObject($Lh);$sd[]=$ti->getFileName();}$J=array();foreach($sd
as$m)$J[basename($m,'.php')]=self::checksum($m);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'c66e1af6','elastic'=>'da03fb2a','firebird'=>'2f32108a','igdb'=>'ac7fbeff','imap'=>'c9dd2dd6','mongo'=>'f33a5c03','redis'=>'8603c834','simpledb'=>'1ef5b158',);}function
__call($A,array$sh){$xa=array();foreach($sh
as$w=>$X)$xa[]=&$sh[$w];$J=null;foreach($this->hooks[$A]as$Lh){$Y=call_user_func_array(array($Lh,$A),$xa);if($Y!==null){if(!self::$append[$A])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$B=null){$xa=func_get_args();$xa[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$xa);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Dh){$this->password_hash=$Dh;}function
description(){return
lang(81);}function
credentials(){$F=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F));}function
login($Ff,$F){if($this->passwordMatches($F))return
true;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);$Ph=$N["port"];$Jc=("$N[host]$Ph$N[socket]"=="");$yj=adminer()->connectSsl();$Yk=($yj&&($yj['key']||$yj['cert']||$yj['ca']||isset($yj['verify'])));if($Yk)$this->ssl_set($yj['key'],$yj['cert'],$yj['ca'],'','');$J=@$this->real_connect((!$Jc?$N["host"]:ini_get("mysqli.default_host")),(!$Jc||$V!=""?$V:ini_get("mysqli.default_user")),(!$Jc||$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,($Ph!=""?intval($Ph):ini_get("mysqli.default_port")),($Ph!=""?null:$N["socket"]),($Yk?($yj['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($ab){if(parent::set_charset($ab))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $ab");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}function
inTransaction(){return
false;}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach(array$N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
lang(82,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$Ph="$N[port]$N[socket]";$A=$N["host"].($Ph!=""?":$Ph":"");$this->link=@mysql_connect(($A!=""?$A:ini_get("mysql.default_host")),($A.$V!=""?$V:ini_get("mysql.default_user")),($A.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($ab){return
mysql_set_charset($ab,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Ub){return
mysql_select_db($Ub,$this->link);}function
query($H,$Hk=false){$I=@($Hk?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
fetch_field(){$J=mysql_fetch_field($this->result,$this->offset++);$J->orgtable=$J->table;$J->charsetnr=($J->blob?63:0);return$J;}}}elseif(extension_loaded("pdo_mysql")){class
Db
extends
PdoDb{var$extension="PDO_MySQL";function
attach(array$N,$V,$F){$C=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$C[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$yj=adminer()->connectSsl();if($yj){if($yj['key'])$C[\PDO::MYSQL_ATTR_SSL_KEY]=$yj['key'];if($yj['cert'])$C[\PDO::MYSQL_ATTR_SSL_CERT]=$yj['cert'];if($yj['ca'])$C[\PDO::MYSQL_ATTR_SSL_CA]=$yj['ca'];if(isset($yj['verify']))$C[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$yj['verify'];}$pe=$N["host"];$Ph=$N["port"];$mj=$N["socket"];return$this->dsn("mysql:charset=utf8".($pe!=""?";host=$pe":'').($Ph!=""?";port=$Ph":($mj!=""?";unix_socket=$mj":"")),$V,$F,$C);}function
set_charset($ab){return$this->query("SET NAMES $ab");}function
select_db($Ub){return$this->query("USE ".idf_escape($Ub));}function
query($H,$Hk=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Hk);return
parent::query($H,$Hk);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($Lj){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($N,$V,$F){$e=parent::connect($N,$V,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($Ki=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$Ki;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array(lang(83)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(84)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(85)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(86)=>array("enum"=>65535,"set"=>64),lang(87)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(88)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types[lang(85)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(85)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(89)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(89)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(83)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$ai){$d=array_keys(reset($L));$Wh="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$fl=array();foreach($d
as$w)$fl[$w]="$w = VALUES($w)";$Fj="\nON DUPLICATE KEY UPDATE ".implode(", ",$fl);$fl=array();$vf=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($fl&&(strlen($Wh)+$vf+strlen($Y)+strlen($Fj)>1e6)){if(!queries($Wh.implode(",\n",$fl).$Fj))return
false;$fl=array();$vf=0;}$fl[]=$Y;$vf+=strlen($Y)+2;}return
queries($Wh.implode(",\n",$fl).$Fj);}function
slowQuery($H,$ik){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$ik FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$_))return"$_[1] /*+ MAX_EXECUTION_TIME(".($ik*1000).") */ $_[2]";}}function
convertColumn($t,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($t)";if($k["type"]=="bit")return"BIN($t + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($t)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($t)";return"";}function
convertSearch($t,array$X,array$k){return($this->convertColumn($t,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t));}function
typeName(\stdClass$k){$Gk=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$J=idx($Gk,$k->type,"");return
parent::typeName($k)?:($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$J):$J);}function
quoteBinary($Ki){return"X".q(bin2hex($Ki));}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($A,$af=false){$Hf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Hf?"$A-table/":str_replace("_","-",$A)."-table.html"));if(DB=="sys")return($Hf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$A)).".html"));if(DB=="mysql")return($Hf?"mysql$A-table/":"system-schema.html");}function
partitionsInfo($R){$Hd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Hd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$K=($I?$I->fetch_row():null);if(!$K)return
array();$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$K;$_h=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Hd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($_h);$J["partition_values"]=array_values($_h);return$J;}function
checkConstraints($R){$J=parent::checkConstraints($R);return($this->conn->flavor=='maria'?$J:array_map('stripslashes',$J));}function
hasCStyleEscapes(){static$Va;if($Va===null){$wj=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Va=(strpos($wj,'NO_BACKSLASH_ESCAPES')===false);}return$Va;}function
lineComment(){return"#|-- ";}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$Lj){return(preg_match('~^(MEMORY|NDB)$~',$Lj["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($zd){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$zj=microtime(true);$J=($zd?slow_query($H):get_vals($H));if(microtime(true)-$zj>0.1){restart_session();set_session("dbs",$J);stop_session();}}return$J;}function
limit($H,$Z,$x,$Lg=0,$Xi=" "){return" $H$Z".($x?$Xi."LIMIT $x".($Lg?" OFFSET $Lg":""):"");}function
limit1($R,$H,$Z,$Xi="\n"){return
limit($H,$Z,1,0,$Xi);}function
db_collation($h,array$ob){$J=null;$Hb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$Hb,$_))$J=$_[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Hb,$_))$J=$ob[$_[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$g){$J=array();foreach($g
as$h)$J[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$J;}function
table_status($A="",$kd=false){$J=array();$H="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($A!=""?"AND TABLE_NAME = ".q($A):"ORDER BY Name");$Mi=array();foreach(($kd?array():get_rows($H))as$K)$Mi[$K["Name"]]=$K;$Zh=null;foreach(get_rows($kd?$H:"SHOW TABLE STATUS".($A!=""?" LIKE ".q(addcslashes($A,"%_\\")):""))as$K){$lh=idx($Mi,$K["Name"]);if($lh){if($K["Comment"]!==$lh["Comment"]&&$K["Comment"]!==$Zh)$K["Error"]=$K["Comment"];$Zh=$K["Comment"];$K["Comment"]=$lh["Comment"];$K["Engine"]=$lh["Engine"];}if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($A!="")$K["Name"]=$A;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($Jd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Jd,$_);return
array($_[1],$_[2],ltrim($_[3].$_[4]));}function
fields($R){$Hf=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$k=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$Od=$K["GENERATION_EXPRESSION"];$hd=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$hd,$Nd);list($Fk,$vf,$Nk)=parse_type($U);$i=$K["COLUMN_DEFAULT"];if($i!=""){$Ze=preg_match('~text|json~',$Fk);if(!$Hf&&$Ze)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($Hf||$Ze){$i=($i=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($_){return
stripslashes(str_replace("''","'",$_[1]));},$i));}if(!$Hf&&preg_match('~binary~',$Fk)&&preg_match('~^0x(\w*)$~',$i,$_))$i=pack("H*",$_[1]);}$J[$k]=array("field"=>$k,"full_type"=>$U,"type"=>$Fk,"length"=>$vf,"unsigned"=>$Nk,"default"=>($Nd?($Hf?$Od:stripslashes($Od)):$i),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($hd=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$hd,$_)?$_[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($Nd[1]=="PERSISTENT"?"STORED":$Nd[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$A=$K["Key_name"];$J[$A]["type"]=($A=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$A]["columns"][]=$K["Column_name"];$J[$A]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$A]["descs"][]=null;$J[$A]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$Hh='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Ib=get_val("SHOW CREATE TABLE ".table($R),1);if($Ib){preg_match_all("~CONSTRAINT ($Hh) FOREIGN KEY ?\\(((?:$Hh,? ?)+)\\) REFERENCES ($Hh)(?:\\.($Hh))? \\(((?:$Hh,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Ib,$Jf,PREG_SET_ORDER);foreach($Jf
as$_){preg_match_all("~$Hh~",$_[2],$qj);preg_match_all("~$Hh~",$_[5],$Yj);$J[idf_unescape($_[1])]=array("db"=>idf_unescape($_[4]!=""?$_[3]:$_[4]),"table"=>idf_unescape($_[4]!=""?$_[4]:$_[3]),"source"=>array_map('Adminer\idf_unescape',$qj[0]),"target"=>array_map('Adminer\idf_unescape',$Yj[0]),"on_delete"=>($_[6]?:"RESTRICT"),"on_update"=>($_[7]?:"RESTRICT"),);}}return$J;}function
view($A){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($A),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$w=>$X)sort($J[$w]);return$J;}function
information_schema($h,$Mi=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$nb){return
queries("CREATE DATABASE ".idf_escape($h).($nb?" COLLATE ".q($nb):""));}function
drop_databases(array$g){$J=apply_queries("DROP DATABASE",$g,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($A,$nb){$J=false;if(create_database($A,$nb)){$T=array();$kl=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$kl[]=$R;else$T[]=$R;}$J=(!$T&&!$kl)||move_tables($T,$kl,$A);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Da=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$Da="";break;}if($u["type"]=="PRIMARY")$Da=" UNIQUE";}}return" AUTO_INCREMENT$Da";}function
alter_table($R,$A,array$l,array$Ad,$sb,$Kc,$nb,$Ca,$zh){$sa=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$sa[]=($R!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($R!=""?$k[2]:"");}else$sa[]="DROP ".idf_escape($k[0]);}$sa=array_merge($sa,$Ad);$P=($sb!==null?" COMMENT=".q($sb):"").($Kc?" ENGINE=".q($Kc):"").($nb?" COLLATE ".q($nb):"").($Ca!=""?" AUTO_INCREMENT=$Ca":"");if($zh){$_h=array();if($zh["partition_by"]=='RANGE'||$zh["partition_by"]=='LIST'){foreach($zh["partition_names"]as$w=>$X){$Y=$zh["partition_values"][$w];$_h[]="\n  PARTITION ".idf_escape($X)." VALUES ".($zh["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $zh[partition_by]($zh[partition])";if($_h)$P
.=" (".implode(",",$_h)."\n)";elseif($zh["partitions"])$P
.=" PARTITIONS ".(+$zh["partitions"]);}elseif($zh===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($A)." (\n".implode(",\n",$sa)."\n)$P");if($R!=$A)$sa[]="RENAME TO ".table($A);if($P)$sa[]=ltrim($P);return($sa?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$sa)):true);}function
alter_indexes($R,$sa){$Ya=array();foreach($sa
as$X)$Ya[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ya));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$kl){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$kl)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$kl,$Yj){$xi=array();foreach($T
as$R)$xi[]=table($R)." TO ".idf_escape($Yj).".".table($R);if(!$xi||queries("RENAME TABLE ".implode(", ",$xi))){$ec=array();foreach($kl
as$R)$ec[table($R)]=view($R);connection()->select_db($Yj);$h=idf_escape(DB);foreach($ec
as$A=>$jl){if(!queries("CREATE VIEW $A AS ".str_replace(" $h."," ",$jl["select"]))||!queries("DROP VIEW $h.$A"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$kl,$Yj){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$A=($Yj==DB?table("copy_$R"):idf_escape($Yj).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $A"))||!queries("CREATE TABLE $A LIKE ".table($R))||!queries("INSERT INTO $A SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$yk=$K["Trigger"];list($Tc,$Hg)=trigger_event($K);if(!queries("CREATE TRIGGER ".($Yj==DB?idf_escape("copy_$yk"):idf_escape($Yj).".".idf_escape($yk))." $K[Timing] $Tc".($Hg!=""?" $Hg":"")." ON $A FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($kl
as$R){$A=($Yj==DB?table("copy_$R"):idf_escape($Yj).".".table($R));$jl=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $A"))||!queries("CREATE VIEW $A AS $jl[select]"))return
false;}return
true;}function
trigger_event(array$K){$Vc=explode(",",$K["Event"]);$J=array();foreach(array("DELETE","INSERT","UPDATE")as$Tc){if(in_array($Tc,$Vc))$J[]=$Tc;}$J=implode(" OR ",$J);if(in_array("UPDATE",$Vc)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($K["Trigger"]),2),$_)&&preg_match('~\bOF\s+(.+)~is',$_[1],$Hg))return
array("$J OF",$Hg[1]);return
array($J,"");}function
trigger($A,$R){if($A=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($A));$J=reset($L);if($J)list($J["Event"],$J["Of"])=trigger_event($J);return$J;}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){list($Tc)=trigger_event($K);$J[$K["Trigger"]]=array($K["Timing"],$Tc);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($A,$U){$L=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($A)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($L
as$K){$Jd=$K["DTD_IDENTIFIER"];list($Fk,$vf,$Nk)=parse_type($Jd);$l[]=array("field"=>$K["PARAMETER_NAME"],"type"=>$Fk,"length"=>$vf,"unsigned"=>$Nk,"null"=>true,"full_type"=>$Jd,"inout"=>($U=="FUNCTION"?"":$K["PARAMETER_MODE"]),"collation"=>$K["COLLATION_NAME"],);}$J=(array)connection()->query("SELECT
	ROUTINE_COMMENT comment,
	ROUTINE_DEFINITION definition,
	LOWER(EXTERNAL_LANGUAGE) language,
	IF(DEFINER = CURRENT_USER(), '', DEFINER) definer,
	IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC', 'NOT DETERMINISTIC') is_deterministic,
	SQL_DATA_ACCESS data_access,
	CONCAT('SQL SECURITY ', SECURITY_TYPE) security
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($A))->fetch_assoc();$J['options']=array("DEFINER"=>$J['definer'],"DETERMINISTIC"=>$J['is_deterministic'],"SQL_DATA_ACCESS"=>$J['data_access'],"SQL_SECURITY"=>$J['security'],"COMMENT"=>$J['comment'],);if($l&&$l[0]['field']=='')$J['returns']=array_shift($l);$J['fields']=$l;return$J;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return(min_version(9,99)?array("sql"=>"sql","javascript"=>"js"):array());}function
routine_options($Fi){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($A,array$K){return
idf_escape($A);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Ca,$Dj){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$Ca)$J=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Ub,$Dj=""){$A=idf_escape($Ub);$J="";if(preg_match('~CREATE~',$Dj)&&($Hb=get_val("SHOW CREATE DATABASE $A",1))){set_utf8mb4($Hb);if($Dj=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $A;\n";$J
.="$Hb;\n";}return$J."USE $A";}function
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
unconvert_field(array$k,$J){if(preg_match("~binary~",$k["type"]))$J="UNHEX($J)";if($k["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($k["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$Wh=(min_version(8)?"ST_":"");$J=$Wh."GeomFromText($J, $Wh"."SRID($k[field]))";}return$J;}function
support($ld){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$ld);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($gd=false){return
array();}function
type_values($s){return"";}function
type_definition($s){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Mi,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));function
page_header($kk,$j="",$Ta=array(),$lk=""){page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$mk=$kk.($lk!=""?": $lk":"");$nk=strip_tags($mk.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(90),'\' class=\'',lang(90),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$nk,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.0.2"),'">
';$Mb=adminer()->css();if(is_int(key($Mb)))$Mb=array_fill_keys($Mb,'light');$ce=in_array('light',$Mb)||in_array('',$Mb);$ae=in_array('dark',$Mb)||in_array('',$Mb);$Qb=($ce?($ae?null:false):($ae?:null));$Yf=" media='(prefers-color-scheme: dark)'";if($Qb!==false)echo"<link rel='stylesheet'".($Qb?"":$Yf)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.0.2")."'>\n";echo"<meta name='color-scheme' content='".($Qb===null?"light dark":($Qb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.0.2");if(adminer()->head($Qb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.2")."'>\n";foreach($Mb
as$Tk=>$ng){$b=($ng=='dark'&&!$Qb?$Yf:($ng=='light'&&$ae?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Tk)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(91))."';
const numberFormat = '".js_escape(lang(5))."';
const numberDigits = '".js_escape(lang(6))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(92)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Ta!==null){$y=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($y?:".").'">'.get_driver(DRIVER).'</a> » ';$y=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:lang(29));if($Ta===false)echo"$N\n";else{echo"<a href='".h($y.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ta)))echo'<a href="'.h($y."&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"")).'">'.h(DB).'</a> » ';if(is_array($Ta)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ta
as$w=>$X){$gc=(is_array($X)?$X[1]:h($X));if($gc!="")echo"<a href='".h(ME."$w=").url_escape(is_array($X)?$X[0]:$X)."'>$gc</a> » ";}}echo"$kk\n";}}echo"<h2>$mk</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();}function
service_worker(){$kb=(has_passwords()?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=".VERSION)."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$kb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$dj){foreach($dj
as$al){foreach($al
as$F){if($F!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Lb){$ge=array();foreach($Lb
as$w=>$X)$ge[]="$w $X";header("Content-Security-Policy: ".implode("; ",$ge));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Zk=array();foreach(array_keys(adminer()->css())as$Tk)$Zk[preg_replace('~\?.*~','',$Tk)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($Zk[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$_);$J[$m]=array((string)$_[1],Plugins::checksum($m));}}return$J;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'ecb9bd1e','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Dg;if(!$Dg)$Dg=base64_encode(rand_string());return$Dg;}function
page_messages($j){$Sk=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$fg=idx($_SESSION["messages"],$Sk);if($fg){echo"<div class='message'>".implode("</div>\n<div class='message'>",$fg)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Sk]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($mg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($mg);echo"</div>\n";if($mg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(31),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(93),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($tg){while($tg>=2147483648)$tg-=4294967296;while($tg<=-2147483649)$tg+=4294967296;return(int)$tg;}function
long2str(array$W,$ml){$Ki='';foreach($W
as$X)$Ki
.=pack('V',$X);if($ml)return
substr($Ki,0,end($W));return$Ki;}function
str2long($Ki,$ml){$W=array_values(unpack('V*',str_pad($Ki,4*ceil(strlen($Ki)/4),"\0")));if($ml)$W[]=strlen($Ki);return$W;}function
xxtea_mx($wl,$vl,$Gj,$ff){return
int32((($wl>>5&0x7FFFFFF)^$vl<<2)+(($vl>>3&0x1FFFFFFF)^$wl<<4))^int32(($Gj^$vl)+($ff^$wl));}function
encrypt_string($Bj,$w){if($Bj=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($Bj,true);$tg=count($W)-1;$wl=$W[$tg];$vl=$W[0];$ii=floor(6+52/($tg+1));$Gj=0;while($ii-->0){$Gj=int32($Gj+0x9E3779B9);$Cc=$Gj>>2&3;for($ph=0;$ph<$tg;$ph++){$vl=$W[$ph+1];$sg=xxtea_mx($wl,$vl,$Gj,$w[$ph&3^$Cc]);$wl=int32($W[$ph]+$sg);$W[$ph]=$wl;}$vl=$W[0];$sg=xxtea_mx($wl,$vl,$Gj,$w[$ph&3^$Cc]);$wl=int32($W[$tg]+$sg);$W[$tg]=$wl;}return
long2str($W,false);}function
decrypt_string($Bj,$w){if($Bj=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($Bj,false);$tg=count($W)-1;$wl=$W[$tg];$vl=$W[0];$ii=floor(6+52/($tg+1));$Gj=int32($ii*0x9E3779B9);while($Gj){$Cc=$Gj>>2&3;for($ph=$tg;$ph>0;$ph--){$wl=$W[$ph-1];$sg=xxtea_mx($wl,$vl,$Gj,$w[$ph&3^$Cc]);$vl=int32($W[$ph]-$sg);$W[$ph]=$vl;}$wl=$W[$tg];$sg=xxtea_mx($wl,$vl,$Gj,$w[$ph&3^$Cc]);$vl=int32($W[0]-$sg);$W[0]=$vl;$Gj=int32($Gj-0x9E3779B9);}return
long2str($W,true);}$Jh=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$Jh[$w]=$X;}}function
add_invalid_login(){$Ka=get_temp_dir()."/adminer-invalid";foreach(glob("$Ka*")?:array($Ka)as$m){$o=file_open_lock($m);if($o)break;}if(!$o)$o=file_open_lock("$Ka-".rand_string());if(!$o)return;$Se=json_decode(stream_get_contents($o),true);$hk=time();if($Se){foreach($Se
as$Te=>$X){if($X[0]<$hk)unset($Se[$Te]);}}$Qe=&$Se[adminer()->bruteForceKey()];if(!$Qe)$Qe=array($hk+30*60,0);$Qe[1]++;file_write_unlock($o,json_encode($Se));}function
check_invalid_login(array&$Jh){$Se=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$o=file_open_lock($m);if($o){$Se=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$w=adminer()->bruteForceKey();$Qe=idx($Se,$w,array());$Cg=($Qe[1]>29?$Qe[0]-time():0);if($Cg>0){$j=lang(94,ceil($Cg/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(95,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$Jh,false);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Kb=adminer()->credentials();$J=!is_object(Driver::connect($Kb[0],$Kb[1],""));if($J)set_session("password_required",true);}}return$J;}function
require_password_link($F){$pg="<a href='https://www.adminer.org/password/'".target_blank().">".lang(96)."</a>";if(!function_exists('password_hash'))return" $pg";$Mh=($F!==null?$F:base64_encode(substr(pack("H*",rand_string()),0,12)));$fe=password_hash($Mh,PASSWORD_DEFAULT);$m="adminer-plugins.php";$ad=file_exists("adminer-plugins.php");if($ad)$Oe=($F!==null?lang(97,"<b>$m</b>"):lang(98,"<b>$m</b>","<b>$Mh</b>"));else{$m="<button name='password_less' value='".h($fe)."' class='link'>$m</button>";$Oe=($F!==null?lang(99,$m):lang(100,$m,"<b>$Mh</b>"));}$_f="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($fe)."'</span>),";$J="<p>$Oe
<pre><code class='jush'>".($ad?$_f:"&lt;?php\n<a>return</a> <a>array</a>(\n$_f\n);")."</code></pre>
<p>$pg
";return" <a href='#password-less' class='toggle'>".lang(101)."</a>
<div id='password-less' class='hidden'>".($ad?$J:"<form action='' method='post'>\n".$J.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Ba=$_POST["auth"];if($Ba&&verify_token()){session_regenerate_id();$hl=$Ba["driver"];$N=$Ba["server"];$V=$Ba["username"];$F=(string)$Ba["password"];$h=$Ba["db"];set_password($hl,$N,$V,$F);$_SESSION["db"][$hl][$N][$V][$h]=true;if($Ba["permanent"]){$w=implode("-",array_map('base64_encode',array($hl,$N,$V,$h)));$ci=adminer()->permanentLogin(true);$Jh[$w]="$w:".base64_encode($ci?encrypt_string($F,$ci):"");cookie("adminer_permanent",implode(" ",$Jh));}if(!array_diff(array_keys($_POST),array("auth","token"))||$hl!=DRIVER||$N!=SERVER||$V!==$_GET["username"]||$h!=DB)redirect(auth_url($hl,$N,$V,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($Jh);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(102).' '.lang(103));}elseif($Jh&&!$_SESSION["pwds"]){session_regenerate_id();$ci=adminer()->permanentLogin();foreach($Jh
as$w=>$X){list(,$ib)=explode(":",$X);list($hl,$N,$V,$h)=array_map('base64_decode',explode("-",$w));set_password($hl,$N,$V,decrypt_string(base64_decode($ib),$ci));$_SESSION["db"][$hl][$N][$V][$h]=true;}}function
unset_permanent(array&$Jh){foreach($Jh
as$w=>$X){list($hl,$N,$V,$h)=array_map('base64_decode',explode("-",$w));if($hl==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$h==DB)unset($Jh[$w]);}cookie("adminer_permanent",implode(" ",$Jh));}function
auth_error($j,array&$Jh,$Re=true){$ej=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$ej]||$_GET[$ej])&&!$_SESSION["token"])$j=lang(104);elseif($Re&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$j
.=($j?'<br>':'').lang(105,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Jh);}}if(!$_COOKIE[$ej]&&$_GET[$ej]&&ini_bool("session.use_only_cookies"))$j=lang(106);$sh=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$sh["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(34),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(107)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Jh);page_header(lang(108),lang(109,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Jh);$Kb=adminer()->credentials();$e=Driver::connect($Kb[0],$Kb[1],$Kb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Ff=null;if(!is_object($e)||($Ff=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($e)?nl_br(h($e)):(is_string($Ff)?$Ff:lang(110))).(preg_match('~^ | $~',get_password())?'<br>'.lang(111):'');auth_error($j,$Jh);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(93),lang(112));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Ba&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(112).' '.lang(113);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(114,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(115);}function
print_select_result($I,$f=null,array$fh=array(),&$x=0){$Bf=array();$v=array();$d=array();$Qa=array();$Gk=array();$J=array();for($r=0;(!$x||$r<$x)&&($K=$I->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($cf=0;$cf<count($K);$cf++){$k=$I->fetch_field();$A=$k->name;$eh=(isset($k->orgtable)?$k->orgtable:"");$dh=(isset($k->orgname)?$k->orgname:$A);if($fh&&JUSH=="sql")$Bf[$cf]=($A=="table"?"table=":($A=="possible_keys"?"indexes=":null));elseif($eh!=""){if(isset($k->table))$J[$k->table]=$eh;if(!isset($v[$eh])){$v[$eh]=array();foreach(indexes($eh,$f)as$u){if($u["type"]=="PRIMARY"){$v[$eh]=array_flip($u["columns"]);break;}}$d[$eh]=$v[$eh];}if(isset($d[$eh][$dh])){unset($d[$eh][$dh]);$v[$eh][$dh]=$cf;$Bf[$cf]=$eh;}}if($k->charsetnr==63)$Qa[$cf]=true;$Gk[$cf]=$k->type;echo"<th title='".h(trim(($eh!=""?"$eh.$dh":($k->name!=$dh?$dh:""))." ".driver()->typeName($k)))."'>".h($A).($fh?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($A),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"<tbody>\n";}echo"<tr>";foreach($K
as$w=>$X){$y="";if(isset($Bf[$w])&&!$d[$Bf[$w]]){if($fh&&JUSH=="sql"){$R=$K[array_search("table=",$Bf)];$y=ME.$Bf[$w].url_escape($fh[$R]!=""?$fh[$R]:$R);}else{$y=ME."edit=".url_escape($Bf[$w]);foreach($v[$Bf[$w]]as$lb=>$cf){if($K[$cf]===null){$y="";break;}$y
.="&where[".url_escape(bracket_escape($lb))."]=".url_escape($K[$cf]);}}}$k=array('type'=>($Qa[$w]?'blob':($Gk[$w]==254?'char':'')),);$X=select_value($X,$y,$k,null);echo"<td".($Gk[$w]<=9||$Gk[$w]==246?" class='number'":"").">$X";}}$x=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(15))."\n";return$J;}function
textarea($A,$Y,$L=10,$pb=80,$ef=JUSH){echo"<textarea name='".h($A)."' rows='$L' cols='$pb' class='sqlarea jush-".h($ef)."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$C,$Y="",$Kh=""){if($C&&$Y!=""&&!isset($C[$Y]))$C=array($Y=>$Y)+$C;$Xj=($C?"select":"input");return"<$Xj$b".($C?"><option value=''>$Kh".optionlist($C,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$Kh'>");}function
json_row($w,$X=null,$Sc=true){static$wd=true;if($wd)echo"{";if($w!=""){echo($wd?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($Sc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$wd=false;}else{echo"\n}\n";$wd=true;}}function
flat_collations(){$ob=collations();return(is_array(reset($ob))?call_user_func_array('array_merge',array_values($ob)):$ob);}function
edit_type($w,array$k,array$ob,array$Cd=array(),array$id=array()){$U=(string)$k["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($Cd[$U])&&!in_array($U,$id))$id[]=$U;$Cj=driver()->structuredTypes();if($Cd)$Cj[lang(116)]=$Cd;echo
optionlist(array_merge($id,$Cj),$U),"</select><td>","<input name='".h($w)."[length]' value='".h($k["length"])."' size='3'".(!$k["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($ob?"<input list='collations' name='".h($w)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($k["collation"])."' placeholder='(".lang(117).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$k["unsigned"]).'</select>':''),(isset($k['on_update'])?"<select name='".h($w)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(118).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($Cd?"<select name='".h($w)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(119).")".optionlist(explode("|",driver()->onActions),$k["on_delete"])."</select> ":" ");}function
option_types($U,$Gk){return" data-types='".h($Gk)."'".(preg_match("~$Gk~",$U)?"":" class='hidden'");}function
process_length($vf){$Nc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Nc(?:\\s*,\\s*$Nc)*+\\s*\\)?\\s*\$~",$vf)&&preg_match_all("~$Nc~",$vf,$Jf)?"(".implode(",",$Jf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$vf)));}function
process_in($X){$Nc=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$Nc(?:\\s*,\\s*$Nc)*+\\s*\\)?\\s*\$~",$X)&&preg_match_all("~$Nc~",$X,$Jf))return"(".implode(", ",$Jf[0]).")";$J=array();foreach(explode(",",$X)as$bf)$J[]=q(trim($bf));return"(".implode(", ",$J).")";}function
process_type(array$k,$mb="COLLATE"){return" $k[type]".process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],driver()->unsigned)?" $k[unsigned]":"").(preg_match('~'.text_type().'~',$k["type"])&&$k["collation"]?" $mb ".(JUSH=="mssql"?$k["collation"]:q($k["collation"])):"");}function
process_field(array$k,array$Dk){if($k["on_update"])$k["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$k["on_update"]);return
array(idf_escape(trim($k["field"])),process_type($Dk),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE $k[on_update]":""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),);}function
default_value(array$k){if($k["default"]===null)return"";$i=str_replace("\r","",$k["default"]);$Nd=$k["generated"];return(in_array($Nd,driver()->generated)?(JUSH=="mssql"?" AS ($i)".($Nd=="VIRTUAL"?"":" $Nd"):" GENERATED ALWAYS AS ($i) $Nd"):(preg_match('~^GENERATED ~i',$i)?" $i":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set|String~',$k["type"])||preg_match('~^(?![a-z])~i',$i)?(JUSH=="sql"&&preg_match('~text|json~',$k["type"])?"(".q($i).")":q($i)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($i)":$i)))));}function
edit_fields(array$l,array$ob,$U="TABLE",array$Cd=array()){$l=array_values($l);$ac=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$tb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(120):lang(121)),"<td id='label-type'>".lang(47)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".lang(122),"<td>".lang(123);if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(49)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<td id='label-default'$ac>".lang(50),(support("comment")?"<td id='label-comment'$tb>".lang(48):"");$pf=!support("move_col");echo"<td>".icon("plus","add[".($pf?count($l):0)."]","+",lang(124),($pf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($l
as$r=>$k){$r++;$gh=$k[($_POST?"orig":"field")];$nc=(isset($_POST["add"][$r-1])||(isset($k["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$gh=="");echo"<tr".($nc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$k["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(125))." ":"");if($nc)echo"<input name='fields[$r][field]' value='".h($k["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$gh);edit_type("fields[$r]",$k,$ob,$Cd);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$k["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($k["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$ac>".(driver()->generated?html_select("fields[$r][generated]",array_merge(array("","DEFAULT"),driver()->generated),$k["generated"])." ":checkbox("fields[$r][generated]",1,$k["generated"],"","","","label-default"));$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($k["default"]);echo(preg_match('~\n~',$k["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$tb>".adminer()->commentInput('COLUMN',$b,$k["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(124))." ":""),($gh==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(126)):"");}}function
process_fields(array&$l){if($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($zc,$Hb,$_c,$dk,$Ac,$z,$eg,$cg,$dg,$Pg,$_g){if($_POST["drop"])query_redirect($zc,$z,$eg);elseif($Pg=="")query_redirect($Hb,$z,$dg);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($z,$cg,queries($zc)&&queries($Hb)&&driver()->commit());driver()->rollback();}elseif($Pg!=$_g){$Jb=queries($Hb);queries_redirect($z,$cg,$Jb&&queries($zc));if($Jb&&$_c)queries($_c);}else
queries_redirect($z,$cg,queries($dk)&&queries($Ac)&&queries($zc)&&queries($Hb));}function
create_trigger($Rg,array$K){$jk=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$Rg.$jk:$jk.$Rg).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
q_dollar($Q){$fc='$$';while(strpos($Q.$fc,$fc)!=strlen($Q))$fc='$_'.substr($fc,1);return$fc.$Q.$fc;}function
routine_collate($nb){static$bb=array();if($nb&&!$bb){foreach(collations()as$ab=>$el){foreach((array)$el
as$X)$bb[$X]=$ab;}}return($bb[$nb]?"CHARACTER SET ".q($bb[$nb])." ":"")."COLLATE";}function
create_routine($Fi,array$K){$O=array();$l=(array)$K["fields"];ksort($l);foreach($l
as$k){if($k["field"]!="")$O[]="\n  ".(preg_match("~^(".driver()->inout.")\$~",$k["inout"])?"$k[inout] ":"").idf_escape($k["field"]).process_type($k,routine_collate($k["collation"]));}$cc="";$C=array();foreach(routine_options($Fi)as$w=>$fl){$Y=idx((array)$K["options"],$w,"");if($w=="DEFINER")$cc=($Y?" $w=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$fl){if($Y!="")$C[]="$w ".q($Y);}elseif($Y!=reset($fl)&&in_array($Y,$fl))$C[]=$Y;}$nf=$K["language"];$dc=rtrim($K["definition"],";");$vc=(JUSH=="pgsql"||($nf&&$nf!="sql"));return"CREATE$cc $Fi ".idf_escape(trim($K["name"]))." (".($O?implode(",",$O)."\n":"").")".($Fi=="FUNCTION"?"\nRETURNS".process_type($K["returns"],routine_collate($K["returns"]["collation"])):"").($nf?" LANGUAGE $nf":"").($C?"\n".implode(" ",$C):"").($vc?" AS ".q_dollar("\n".trim($dc)."\n"):"\n$dc;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$n){$h=$n["db"];$Eg=$n["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Eg!=""&&$Eg!=$_GET["ns"]?idf_escape($Eg).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($m,$ok){$J=pack("a100a8a8a8a12a12",$m,644,0,0,decoct($ok->size),decoct(time()));$gb=8*32;for($r=0;$r<strlen($J);$r++)$gb+=ord($J[$r]);$J
.=sprintf("%06o",$gb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$ok->send();echo
str_repeat("\0",511-($ok->size+511)%512);}function
doc_version(){$cj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$cj,$_);return($_[1]>=18?$_[1]:"19");}$vi=(JUSH=='sql'?'~^\d+\.\d+~':'~^\d\.?\d~');$il=(preg_match($vi,$cj,$_)?$_[0]:"");if(JUSH=='mssql')return($il>=15?"sql-server-ver$il":($il==12?"azuresqldb-current":"sql-server-2017"));return$il;}function
doc_link(array$Gh,$ek="<sup>?</sup>"){$il=doc_version();$Uk=array('sql'=>"https://dev.mysql.com/doc/refman/$il/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$il)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$il/",);if(connection()->flavor=='maria'){$Uk['sql']="https://mariadb.com/kb/en/";$Gh['sql']=(isset($Gh['mariadb'])?$Gh['mariadb']:str_replace(".html","/",$Gh['sql']));}return($Gh[JUSH]?"<a href='".h($Uk[JUSH].$Gh[JUSH].(JUSH=='mssql'?"?view=$il":""))."'".target_blank().">$ek</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($Hb){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$Hb)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header(lang(33).": ".h(DB),lang(127),true);}else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),lang(128),drop_databases($_POST["db"]));page_header(lang(129),$j,false);echo"<p class='links'>\n";foreach(array('database'=>lang(130),'privileges'=>lang(69),'processlist'=>lang(131),'variables'=>lang(132),'status'=>lang(133),)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".lang(134,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(135,"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();if($g){$Ni=support("scheme");$ob=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(33).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(136)."</a>":"")."<td>".lang(137)."<td>".lang(138)."<td>".lang(139)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(140)."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$T){$Ei=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$s=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$Ei' id='$s'>".h($h)."</a>";$nb=h(db_collation($h,$ob));echo"<td>".(support("database")?"<a href='$Ei".($Ni?"&amp;ns=":"")."&amp;database=' title='".lang(65)."'>$nb</a>":$nb),"<td align='right'><a href='$Ei&amp;schema=' id='tables-".h($h)."' title='".lang(68)."'>".($_GET["dbsize"]?format_number($T):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(141)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(142)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ka=adminer();$Oh=($ka
instanceof
Plugins?$ka->plugins:array());$yc=($ka
instanceof
Plugins?$ka->drivers:array());$kc=design_checksums();if($Oh||$yc||$kc){$hb=($ka
instanceof
Plugins?$ka->checksums():array());$Ig=Plugins::officialChecksums();$Qk=function($Tk){return" (<a href='$Tk'".target_blank()." class='update'>".VERSION."</a>)";};$Nh=function($qd)use($hb,$Ig,$Qk){return($hb[$qd]&&$Ig[$qd]&&$hb[$qd]!==$Ig[$qd]?$Qk("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(143)."</h3>\n<ul>\n";foreach($Oh
as$Lh){$ti=new
\ReflectionObject($Lh);$hc=(method_exists($Lh,'description')?$Lh->description():"");if(!$hc){if(preg_match('~^/[\s*]+(.+)~',$ti->getDocComment(),$_))$hc=$_[1];}$Oi=(method_exists($Lh,'screenshot')?$Lh->screenshot():"");echo"<li><b>".get_class($Lh)."</b>".h($hc?": $hc":"").($Oi?" (<a href='".h($Oi)."'".target_blank().">".lang(144)."</a>)":"").$Nh(basename((string)$ti->getFileName(),'.php'))."\n";}foreach($yc
as$s=>$A)echo"<li><b>".h($s)."</b>: ".h($A).$Nh(basename((string)$ka->driverFiles[$s],'.php'))."\n";if($kc){$Kg=official_design_checksums();foreach($kc
as$m=>$jc){list($A,$gb)=$jc;$Jg=$Kg["$A/$m"];echo"<li><b>".h($m)."</b>".h($A?": $A":"").($Jg&&$Jg!==$gb?$Qk("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Ab){$this->size+=strlen($Ab);fwrite($this->handler,$Ab);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$l)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=adminer()->error()?:lang(12);$S=table_status1($a);$A=adminer()->tableName($S);$j=$j?:h($S["Error"]);page_header(($l&&is_view($S)?$S['Engine']=='materialized view'?lang(145):lang(146):lang(147)).": ".($A!=""?$A:h($a)),$j);$Di=array();foreach($l
as$w=>$k)$Di+=$k["privileges"];adminer()->selectLinks($S,(isset($Di["insert"])||!support("table")?"":null));$sb=$S["Comment"];if($sb!="")echo"<p class='nowrap'>".lang(48).": ".adminer()->commentValue('TABLE',$sb)."\n";if($l)adminer()->tableStructurePrint($l,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$y=preg_replace('~ns=[^&]*~',"ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($y."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$He=driver()->inheritsFrom($a);if($He){echo"<h3>".lang(148)."</h3>\n";tables_links($He);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(149)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(150)."</a>\n";echo"</div>\n";}if(!is_view($S)&&driver()->supportsAlterTable($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(116)."</h3>\n";$Cd=foreign_keys($a);if($Cd){echo"<table>\n","<thead><tr><th>".lang(151)."<td>".lang(152)."<td>".lang(119)."<td>".lang(118)."<td class='hover'><tbody>\n";foreach($Cd
as$A=>$n){echo"<tr title='".h($A)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$y=($n["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($n["db"]),ME):($n["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($n["ns"]),ME):ME));echo"<td><a href='".h($y."table=".url_escape($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($A)).'">'.lang(153).'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(154)."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(155)."</h3>\n";$db=driver()->checkConstraints($a);if($db){echo"<table>\n";foreach($db
as$w=>$X)echo"<tr title='".h($w)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".lang(153)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(156)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")&&driver()->supportsAlterTable($S)){echo"<div>\n","<h3 id='triggers'>".lang(157)."</h3>\n";$Ak=triggers($a);if($Ak){echo"<table>\n";foreach($Ak
as$w=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".lang(153)."</a>\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(158)."</a>\n","</div>\n";}$hj=driver()->shadowTables($a);if($hj){echo"<h3 id='shadow-tables'>".lang(159)."</h3>\n";tables_links($hj);}$Ge=driver()->inheritedTables($a);if($Ge){echo"<h3 id='partitions'>".lang(160)."</h3>\n";$vh=driver()->partitionsInfo($a);if($vh)echo"<p><code class='jush-".JUSH."'>BY ".h("$vh[partition_by]($vh[partition])")."</code>\n";tables_links($Ge);}}elseif(isset($_GET["schema"])){page_header(lang(68),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$si,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($si,$R)as$A=>$ui){if($A!=$R)$d[$R]=max($d[$R],schema_column($A,$si,$d)+1);}}return$d[$R];}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(preg_match("~$w|$X~",$U))return" class='$w'";}}$Oj=array();$Qj=array();$Pj=array();$nd=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Jf,PREG_SET_ORDER);foreach($Jf
as$r=>$_){$Oj[$_[1]]=array((float)$_[2],(float)$_[3]);$Qj[]="\n\t'".js_escape($_[1])."': [ $_[2], $_[3] ]";}$Mi=array();$si=array();$Cd=array();$qa=driver()->allFields();$le=array();$Rj=array();foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!=""&&!$S["dependent"])$Rj[$R]=$S;else$le[$R]=true;}}foreach($Rj
as$R=>$S){$G=0;$Mi[$R]["fields"]=array();foreach($qa[$R]as$k){$G+=1.25;$nd[$R][$k["field"]]=$G;$Mi[$R]["fields"][$k["field"]]=$k;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$le[$X["table"]]){$Cd[$R][]=$X;$si[$X["table"]][$R]=array();}}}$d=array();$Rd=array();$ul=array();$Wd=array();foreach(array_keys($Mi)as$A)schema_column($A,$si,$d);arsort($d);foreach($d
as$A=>$c){$kg=null;foreach((array)idx($Cd,$A)as$X){if($X["table"]!=$A&&$Mi[$X["table"]])$kg=($kg===null?$d[$X["table"]]:min($kg,$d[$X["table"]]));}$d[$A]=max($c,(int)$kg-1);}foreach($Mi
as$A=>$R){$c=$d[$A];$Rd[$c][]=$A;$gk=.75*strlen($A);foreach($R["fields"]as$k)$gk=max($gk,.65*strlen($k["field"]));$ul[$c]=max(idx($ul,$c,0),ceil($gk)+1);}foreach($Cd
as$A=>$el){foreach($el
as$X){$Vd=$d[$A]+(idx($d,$X["table"],$d[$A])>$d[$A]?1:0);$Wd[$Vd]=idx($Wd,$Vd,0)+1;}}ksort($Rd);$je=0;$tl=0;$qb=0;$Yh=null;$Mj=array();$Tj=array();foreach($Rd
as$c=>$T){if($Yh!==null){$qb=round($qb+$ul[$Yh]+1.7+idx($Wd,$c,0)*.1,1);$D=array();foreach($T
as$A){$Gj=0;$Gb=0;$xg=array_keys((array)idx($si,$A));foreach((array)idx($Cd,$A)as$X)$xg[]=$X["table"];foreach($xg
as$ug){if($Mi[$ug]&&$d[$ug]<$c){$Gj+=$Mi[$ug]["pos"][0];$Gb++;}}$D[$A]=($Gb?$Gj/$Gb:$je);}asort($D);$T=array_keys($D);}$rk=0;foreach($T
as$A){$G=1.25*count($Mi[$A]["fields"]);$Mi[$A]["pos"]=($Oj[$A]?:array($rk,$qb));$Mj[$A]=$Mi[$A]["pos"][1];$Tj[$A]=$ul[$c];$rk+=2.5+$G;$je=max($je,$Mi[$A]["pos"][0]+2.5+$G);$tl=max($tl,round($Mi[$A]["pos"][1]+$ul[$c],1));if(!$Oj[$A])$Pj[]="\n\t'".js_escape($A)."': [ ".$Mi[$A]["pos"][0].", ".$Mi[$A]["pos"][1]." ]";}$Yh=$c;}$tf=array();$La=array();foreach($Cd
as$A=>$el){foreach($el
as$X){$Zj=idx($Mj,$X["table"],$Mj[$A]);$rj=$Mj[$A]+$Tj[$A];$Ci=($Zj-1>$rj);$rf=($Ci?$rj+1:min($Mj[$A],$Zj)-1);$Ka=idx($La,(string)$rf,0);$La[(string)$rf]=$Ka+1;$rf=round($Ci?min($rf+$Ka*.1,$Zj-1):$rf-$Ka*.1,1);while($tf[(string)$rf])$rf-=.0001;$Mi[$A]["references"][$X["table"]][(string)$rf]=array($X["source"],$X["target"]);$si[$X["table"]][$A][(string)$rf]=$X["target"];$tf[(string)$rf]=true;}}echo'<div id="schema" style="height: ',$je,'em; width: ',$tl,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$Qj)."\n",'};
const tablePosDefault = {',implode(",",$Pj)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$je,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($Mi
as$A=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$Tj[$A]."em;'>",'<a href="'.h(ME).'table='.url_escape($A).'"><b>'.h($A)."</b></a>";foreach($R["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$ak=>$ui){foreach($ui
as$rf=>$pi){$sf=$rf-$R["pos"][1];$Dj=($sf>0?"left: 100%; width: calc($sf"."em - 100%)":"left: $sf"."em");$tl=($sf>0?"100%":(-$sf)."em");$r=0;foreach($pi[0]as$qj)echo"\n<div class='references' title='".h($ak)."' id='refs$rf-".($r++)."' style='$Dj"."; top: ".$nd[$A][$qj]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $tl;'></div></div>";}}foreach((array)$si[$A]as$ak=>$ui){foreach($ui
as$rf=>$bk){$sf=$rf-$R["pos"][1];$r=0;foreach($bk
as$Yj)echo"\n<div class='references arrow' title='".h($ak)."' id='refd$rf-".($r++)."' style='left: $sf"."em; top: ".$nd[$A][$Yj]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$sf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($Mi
as$A=>$R){foreach((array)$R["references"]as$ak=>$ui){if($Mi[$ak]){foreach($ui
as$rf=>$pi){$lg=$je;$Rf=-10;foreach($pi[0]as$w=>$qj){$Qh=$R["pos"][0]+$nd[$A][$qj];$Rh=$Mi[$ak]["pos"][0]+$nd[$ak][$pi[1][$w]];$lg=min($lg,$Qh,$Rh);$Rf=max($Rf,$Qh,$Rh);}echo"<div class='references' id='refl$rf' style='left: $rf"."em; top: $lg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Rf-$lg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(161),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$j){$i=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Ij){if(support($Ij))$i[$Ij."s"]='';}save_settings(array_intersect_key($_POST+$i,array_flip(array("output","format","db_style","table_style","data_style"))+$i),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$ed=dump_headers((count($T)==1?key($T):DB),(DB==""||$_GET["ns"]===""||count($T)>1));$Ye=preg_match('~sql~',$_POST["format"]);if($Ye){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Dj=$_POST["db_style"];$g=array(DB);if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($Ye&&$Dj)echo
use_sql($h,$Dj).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$Mi){if($Mi!=""){if(DB==""&&information_schema(DB,$Mi))continue;set_schema($Mi);}$Aj=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$dd=array();$Tb=array();foreach($Aj
as$A=>$S){if(DB==""||$_GET["ns"]===""||in_array($A,(array)$_POST["tables"]))$dd[$A]=$S;if(DB==""||$_GET["ns"]===""||in_array($A,(array)$_POST["data"]))$Tb[$A]=$S;}if($Ye){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($dd);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Bk=array();foreach($Tb
as$A=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($dd[$A])))$Bk[]=$A;}echo
truncate_all_sql($Bk);}$nh="";if($_POST["types"]){foreach(types()as$s=>$U){$dc=type_definition($s);$Gg=($dc["kind"]=='d'?"DOMAIN":"TYPE");if($dc["definition"])$nh
.=($Dj!='DROP+CREATE'?"DROP $Gg IF EXISTS ".idf_escape($U).";;\n":"")."CREATE $Gg ".idf_escape($U)." $dc[definition];\n\n";else$nh
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$A=$K["ROUTINE_NAME"];$Fi=$K["ROUTINE_TYPE"];$Hb=create_routine($Fi,array("name"=>$A)+routine($K["SPECIFIC_NAME"],$Fi));set_utf8mb4($Hb);$nh
.=($Dj!='DROP+CREATE'?"DROP $Fi IF EXISTS ".idf_escape($A).";;\n":"")."$Hb;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$Hb=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($Hb);$nh
.=($Dj!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$Hb;;\n\n";}}echo($nh&&JUSH=='sql'?"DELIMITER ;;\n\n$nh"."DELIMITER ;\n\n":$nh);}if($_POST["table_style"]||$_POST["data_style"]){$kl=array();foreach($Aj
as$A=>$S){$R=array_key_exists($A,$dd);$Rb=array_key_exists($A,$Tb);if($R||$Rb){$ok=null;if($ed=="tar"){$ok=new
TmpFile;ob_start(array($ok,'write'),1e5);}adminer()->dumpTable($A,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$kl[]=$A;elseif($Rb){$l=fields($A);$M=array("*");$Db=convert_fields($l,$l);if($Db)$M[]=substr($Db,2);adminer()->dumpData($A,$_POST["data_style"],"",$M);}if($Ye&&$_POST["triggers"]&&$R&&($Ak=trigger_sql($A)))echo"\nDELIMITER ;;\n$Ak\nDELIMITER ;\n";if($ed=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$A.csv",$ok);}elseif($Ye)echo"\n";}}if($Ye&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($dd
as$A=>$S){if(!is_view($S))echo
foreign_keys_sql($A);}}if($Ye){foreach($kl
as$jl)adminer()->dumpTable($jl,$_POST["table_style"],1);}if($ed=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(74),$j,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Wb=array('','USE','DROP+CREATE','CREATE');$Sj=array('','DROP+CREATE','CREATE');$Sb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Sb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(162)."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".lang(163)."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(33)."<td>".html_select('db_style',$Wb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],lang(7)):"").(support("routine")?checkbox("routines",1,$K["routines"],lang(70)):"").(support("event")?checkbox("events",1,$K["events"],lang(72)):"")),"<tr><th>".lang(138)."<td>".html_select('table_style',$Sj,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],lang(49)).(support("trigger")?checkbox("triggers",1,$K["triggers"],lang(157)):""),"<tr><th>".lang(164)."<td>".html_select('data_style',$Sb,$K["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(74),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$Xh=array();if($_GET["ns"]===""){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(165)."'".on('click','formCheck','^schemas\[').">".lang(166)."</label>","<tbody>\n";foreach(adminer()->schemas()as$Mi){if(!information_schema(DB,$Mi))echo"<tr><td>".checkbox("schemas[]",$Mi,true,$Mi,"","block")."\n";}}elseif(DB!=""){$eb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$eb class='jsonly' title='".lang(165)."'".on('click','formCheck','^tables\[').">".lang(147)."</label>","<th style='text-align: right;'><label class='block'>".lang(164)."<input type='checkbox' id='check-data'$eb class='jsonly' title='".lang(165)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$kl="";$Vj=tables_list();foreach($Vj
as$A=>$U){$Wh=preg_replace('~_.*~','',$A);$eb=($a==""||$a==(substr($a,-1)=="%"?"$Wh%":$A));$bi="<tr><td>".checkbox("tables[]",$A,$eb,$A,"","block");if($U!==null&&!preg_match('~table~i',$U))$kl
.="$bi\n";else
echo"$bi<td align='right'><label class='block'><span id='Rows-".h($A)."'></span>".checkbox("data[]",$A,$eb)."</label>\n";$Xh[$Wh]++;}echo$kl;if($Vj)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(165)."'".on('click','formCheck','^databases\[').">":"").lang(33)."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$Wh=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$Wh%",$h,"","block")."\n";$Xh[$Wh]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$wd=true;foreach($Xh
as$w=>$X){if($w!=""&&$X>1){echo($wd?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$wd=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(69));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(167)."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Pd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Pd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(31)."<th>".lang(29)."<td class='hover'><tbody>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"]),"<td>".h($K["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($K["User"]).'&host='.url_escape($K["Host"])).'">'.lang(13)."</a>\n";if(!$Pd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(13)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$ne=&get_session("queries");$me=&$ne[DB];if(!$j&&$_POST["clear"]){$me=array();redirect(remove_from_uri("history"));}stop_session();$la=get_settings("adminer_import");if($_POST&&$la)save_settings($la,"adminer_import");page_header((isset($_GET["import"])?lang(73):lang(62)),$j);$Af=driver()->lineComment();if(!$j&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$fc=driver()->delimiter;$o=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$uj=adminer()->importServerPath();$o=@fopen((file_exists($uj)?$uj:"compress.zlib://$uj.gz"),"rb");$H=($o?fread($o,1e6):false);}else$H=get_file("sql_file",true,$fc);if(is_string($H)){if(($Zf=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($Zf,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$ii=$H.(preg_match("~$fc\\s*\$~",$H)?"":$fc);if(!$me||first(end($me))!=$ii){restart_session();$me[]=array($ii,time());set_session("queries",$ne);stop_session();}}$sj="(?:\\s|/\\*[\s\S]*?\\*/|(?:$Af)[^\n]*\n?|--\r?\n)";$Lg=0;$Jc=true;$Fb=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$rb=0;$Qc=array();$th='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Af.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$sk=microtime(true);while($H!=""){if(!$Lg&&preg_match("~^$sj*+DELIMITER\\s+(\\S+)~i",$H,$_)){$fc=preg_quote($_[1]);$H=substr($H,strlen($_[0]));}elseif(!$Lg&&JUSH=='pgsql'&&preg_match("~^($sj*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$_)){$fc="\n\\\\\\.\r?\n";$Fb=true;$Lg=strlen($_[0]);}else{preg_match("($fc\\s*|$th)",$H,$_,PREG_OFFSET_CAPTURE,$Lg);list($Ed,$G)=$_[0];if(!$Ed&&$o&&!feof($o))$H
.=fread($o,1e5);else{if(!$Ed&&rtrim($H)=="")break;$Lg=$G+strlen($Ed);if($Ed&&!preg_match("(^$fc)",$Ed)){$Wa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$Hh=($Ed=='/*'?'\*/':($Ed=='['?']':(preg_match("~^(?:$Af)~",$Ed)?"\n":preg_quote($Ed).($Wa?'|\\\\.':''))));while(preg_match("($Hh|\$)s",$H,$_,PREG_OFFSET_CAPTURE,$Lg)){$Ki=$_[0][0];if(!$Ki&&$o&&!feof($o))$H
.=fread($o,1e5);else{$Lg=$_[0][1]+strlen($Ki);if(!$Ki||$Ki[0]!="\\")break;}}}else{$ii=substr($H,0,$G+($Fb?3:0));$H=substr($H,$Lg);$Lg=0;if($Fb){$fc=driver()->delimiter;$Fb=false;}$kb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($ii)."</code>";if(preg_match("~^$sj*+\$~",$ii)&&!preg_match('~/\*M?!~',$ii)){echo($_POST["only_errors"]?"":"<pre>$kb</pre>\n");continue;}$Jc=false;$rb++;$bi="<pre id='sql-$rb'>$kb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$sj*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$ii,$_)!==0){echo$bi,"<p class='error'>".lang(168,preg_match('~ATTACH~i',$_[1])?'ATTACH':'VACUUM INTO')."\n";$Qc[]=" <a href='#sql-$rb'>$rb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$bi;ob_flush();flush();}$zj=microtime(true);if(connection()->multi_query($ii)&&$f&&preg_match("~^$sj*+USE\\b~i",$ii))$f->query($ii);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$bi:""),"<p class='error'>".lang(169).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Qc[]=" <a href='#sql-$rb'>$rb</a>";if($_POST["error_stops"])break
2;}else{$y=ME."sql=".url_escape(trim($ii));$hk=" <span class='time'>(".format_time($zj).")</span>".(strlen($y)<1900?" <a href='".h($y)."'>".lang(13)."</a>":"");$na=connection()->affected_rows;$nl=($_POST["only_errors"]?"":driver()->warnings());$ol="warnings-$rb";if($nl)$hk
.=", <a href='#$ol' class='toggle'>".lang(44)."</a>";$bd=null;$fh=null;$cd="explain-$rb";if(is_object($I)){$x=$_POST["limit"];$Fg=$x;$fh=print_select_result($I,$f,array(),$Fg);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$Fg=max($I->num_rows,$Fg);echo"<p class='sql-footer'>".($Fg?($x&&$Fg>$x?lang(170,$x):"").lang(171,$Fg):""),$hk;if($f&&preg_match("~^($sj|\\()*+SELECT\\b~i",$ii)&&($bd=explain($f,$ii)))echo", <a href='#$cd' class='toggle'>Explain</a>";$s="export-$rb";echo", <a href='#$s' class='toggle'>".lang(74)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$la["output"])." ".html_select("format",adminer()->dumpFormat(),$la["format"]).input_hidden("query",$ii)."<input type='submit' name='export' value='".lang(74)."'".($x?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$sj*+(CREATE|DROP|ALTER)$sj++(DATABASE|SCHEMA)\\b~i",$ii)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(172,$na)."$hk\n";}echo($nl?"<div id='$ol' class='hidden'>\n$nl</div>\n":"");if($bd){echo"<div id='$cd' class='hidden explain'>\n";print_select_result($bd,$f,$fh);echo"</div>\n";}}$zj=microtime(true);}while(connection()->next_result());}}}}}if($Jc)echo"<p class='message'>".lang(173)."\n";else{$_e=connection()->inTransaction();driver()->rollback();if($_e)echo"<pre><code class='jush-".JUSH."'>ROLLBACK -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(174,$rb-count($Qc))," <span class='time'>(".format_time($sk).")</span>\n";elseif($Qc&&$rb>1)echo"<p class='error'>".lang(169).": ".implode("",$Qc)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$Rk="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($Rk);echo'>
';$Yc="<input type='submit' value='".lang(175)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$ii=$_GET["sql"];if($_POST)$ii=$_POST["query"];elseif($_GET["history"]=="all")$ii=$me;elseif($_GET["history"]!="")$ii=idx($me[$_GET["history"]],0);echo"<p>";textarea("query",$ii,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$Yc\n",lang(176).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Xd=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(177)."</legend><div>",($Rk?input_hidden(ini_get("session.upload_progress.name"),$Rk):""),"SQL$Xd: ".file_input(" name='sql_file[]' multiple","\n$Yc"),($Rk?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$xe=adminer()->importServerPath();if($xe)echo"<fieldset><legend>".lang(178)."</legend><div>",lang(179,"<code>".h($xe)."$Xd</code>")," <input type='submit' name='webfile' value='".lang(180)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(181))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(182))."\n",input_token();if(!isset($_GET["import"])&&$me){print_fieldset("history",lang(183),$_GET["history"]!="");for($X=end($me);$X;$X=prev($me)){$w=key($me);list($ii,$hk,$Fc)=$X;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.lang(13)."</a>"." <span class='time' title='".@date('Y-m-d',$hk)."'>".@date("H:i:s",$hk)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Af).*~m",'',$ii))),80,"</code>").($Fc?" <span class='time'>($Fc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(184)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(185)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$Pk=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$A=>$k){if((!$Pk&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$A]);}if($_POST&&!$j&&!isset($_GET["select"])){$z=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$z=($Pk?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$z))$z=ME."select=".url_escape($a);$v=indexes($a);$Jk=unique_array($_GET["where"],$v);$li="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($z,lang(186),driver()->delete($a,$li,$Jk?0:1));else{$O=array();foreach($l
as$A=>$k){$X=process_input($k);if($X!==false&&$X!==null)$O[idf_escape($A)]=$X;}if($Pk){if(!$O)redirect($z);queries_redirect($z,lang(187),driver()->update($a,$O,$li,$Jk?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$I=driver()->insert($a,$O);$qf=($I?last_id($I):0);queries_redirect($z,lang(188,($qf?" $qf":"")),$I);}}}$K=null;$H="";$hk="";if($Z){$M=array();$Ti=array("*");foreach($l
as$A=>$k){if(isset($k["privileges"]["select"])){$za=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$c=($za?"$za AS ":"").idf_escape($A);$M[]=$c;if($za)$Ti[]=$c;}}$K=array();if(!support("table")){$M=array("*");$Ti=$M;}if($M){$zj=microtime(true);$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));$H=str_replace("SELECT ".implode(", ",$M),"SELECT ".implode(", ",$Ti),driver()->query);$hk=format_time($zj);if(!$I)$j=adminer()->error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$w=>$X){if(!$Z)$K[$w]=null;$l[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"]){$Sh=array();foreach((array)$_POST["fields"]as$w=>$X)$Sh[bracket_escape($w,true)]=$X;$K=$Sh+($K?$K:array());}edit_form($a,$l,$K,$Pk,$j,$H,$hk);}elseif(isset($_GET["create"])){function
referencable_primary($Vi){$J=array();foreach(table_status('',true)as$Nj=>$R){if($Nj!=$Vi&&!$R["dependent"]&&fk_support($R)){foreach(fields($Nj)as$k){if($k["primary"]){if($J[$Nj]){unset($J[$Nj]);break;}$J[$Nj]=$k;}}}}return$J;}$a=$_GET["create"];$xh=driver()->partitionBy;$Ah=($xh&&$a!=""?driver()->partitionsInfo($a):array());$ri=referencable_primary($a);$Cd=array();foreach($ri
as$Nj=>$k)$Cd[str_replace("`","``",$Nj)."`".str_replace("`","``",$k["field"])]=$Nj;$ih=array();$S=array();if($a!=""){$ih=fields($a);$S=table_status1($a);if(count($S)<2)$j=lang(12);}$ta=($a==""||driver()->supportsAlterTable($S));$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$j)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$j){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(189),drop_tables(array($a)));else{$l=array();$qa=array();$Vk=false;$Ad=array();$hh=reset($ih);$pa=" FIRST";foreach($K["fields"]as$k){$n=$Cd[$k["type"]];$Dk=($n!==null?$ri[$n]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$gi=process_field($k,$Dk);$qa[]=array($k["orig"],$gi,$pa);if(!$hh||$gi!==process_field($hh,$hh)){$l[]=array($k["orig"],$gi,$pa);if($k["orig"]!=""||$pa)$Vk=true;}if($n!==null)$Ad[idf_escape($k["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Cd[$k["type"]],'source'=>array($k["field"]),'target'=>array($Dk["field"]),'on_delete'=>$k["on_delete"],));$pa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$Vk=true;$l[]=array($k["orig"]);}if($k["orig"]!=""){$hh=next($ih);if(!$hh)$pa="";}}$zh=array();if(in_array($K["partition_by"],$xh)){foreach($K
as$w=>$X){if(preg_match('~^partition~',$w))$zh[$w]=$X;}foreach($zh["partition_names"]as$w=>$A){if($A==""){unset($zh["partition_names"][$w]);unset($zh["partition_values"][$w]);}}$zh["partition_names"]=array_values($zh["partition_names"]);$zh["partition_values"]=array_values($zh["partition_values"]);if($zh==$Ah)$zh=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$zh=null;$bg=lang(190);if($a==""){cookie("adminer_engine",$K["Engine"]);$bg=lang(191);}$A=trim($K["name"]);$z=ME.(support("table")?"table=":"select=").url_escape($A);$I=alter_table($a,$A,(JUSH=="sqlite"&&($Vk||$Ad)?$qa:$l),$Ad,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$zh);if($I&&!Queries::$queries&&$a!=""&&!$l&&!$Ad)redirect($z);queries_redirect($z,$bg,$I);}}page_header(($a!=""?lang(42):lang(75)),$j,array("table"=>$a),h($a));if(!$_POST){$Gk=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Gk["int"])?"int":(isset($Gk["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($ih
as$k){if($k["generated"])$k["default"]=ltrim($k["default"]);$k["generated"]=$k["generated"]?:(isset($k["default"])?"DEFAULT":"");$K["fields"][]=$k;}if($xh){$K+=$Ah;$K["partition_names"][]="";$K["partition_values"][]="";}}}$ob=flat_collations();$Lc=driver()->engines();foreach($Lc
as$Kc){if(!strcasecmp($Kc,$K["Engine"])){$K["Engine"]=$Kc;break;}}$Mf=max_input_vars(12,20);if($Mf){$le=(count($K["fields"])>$Mf?"":" hidden");echo"<p".($le?" id='max-fields' data-columns='$Mf'":"")." class='error$le'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(192).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",(!$ta?h($S["Engine"])."\n":($Lc?html_select("Engine",array(""=>"(".lang(193).")")+$Lc,$K["Engine"],on('change','helpClose').on_help_value())."\n":""));if($ob)echo"<datalist id='collations'>".optionlist($ob)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".lang(117).")'>\n");echo"<input type='submit' value='".lang(17)."'>\n";}if(support("columns")&&$ta){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$ob,"TABLE",$Cd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(49).": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(194),on('click','columnShowClick',5),"jsonly");$ub=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$ub,lang(48),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($ub?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$a)),'>
';if($xh&&(JUSH=='sql'||$a=="")){$yh=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",lang(196),$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$xh),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",lang(197).": <input type='number' name='partitions' class='size".($yh||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($yh?"":" class='hidden'").">\n","<thead><tr><th>".lang(198)."<th>".lang(199)."<tbody>\n";foreach($K["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($w==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Ee=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$Ce=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Ee[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Ee[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Ee[]="VECTOR";$v=indexes($a);$l=fields($a);$ai=array();if(JUSH=="mongo"){$ai=$v["_id_"];unset($Ee[0]);unset($v["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]){$sa=array();foreach($K["indexes"]as$u){$A=$u["name"];if(in_array($u["type"],$Ee)){$d=array();$yf=array();$ic=array();$Vg=array();$De=(support("partial_indexes")?$u["partial"]:"");$Be=(in_array($u["algorithm"],$Ce)?$u["algorithm"]:"");$O=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$c){if($c!=""){$vf=idx($u["lengths"],$w);$gc=idx($u["descs"],$w);$Ug=idx($u["opclasses"],$w);$O[]=($l[$c]?idf_escape($c):$c).($vf?"(".(+$vf).")":"").($Ug!=""?" ".idf_escape($Ug):"").($gc?" DESC":"");$d[]=$c;$yf[]=($vf?:null);$ic[]=$gc;$Vg[]="$Ug";}}$Zc=$v[$A];if($Zc){ksort($Zc["columns"]);ksort($Zc["lengths"]);ksort($Zc["descs"]);if($u["type"]==$Zc["type"]&&array_values($Zc["columns"])===$d&&(!$Zc["lengths"]||array_values($Zc["lengths"])===$yf)&&array_values($Zc["descs"])===$ic&&(!$Zc["opclasses"]||array_values($Zc["opclasses"])===$Vg)&&$Zc["partial"]==$De&&(!$Ce||$Zc["algorithm"]==$Be)){unset($v[$A]);continue;}}if($d)$sa[]=array($u["type"],$A,$O,$Be,$De);}}foreach($v
as$A=>$Zc)$sa[]=array($Zc["type"],$A,"DROP");if(!$sa)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(200),alter_indexes($a,$sa));}page_header(lang(149),$j,array("table"=>$a),h($a));$pd=array_keys($l);if($_POST["add"]){foreach($K["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$K["indexes"][$w]["columns"][]="";}$u=end($K["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$K["indexes"]=$v;}$yf=(JUSH=="sql"||JUSH=="mssql");$Vg=driver()->indexOpclasses();$ij=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(201);$ve=" class='idxopts".($ij?"":" hidden")."'";if($Ce)echo"<th id='label-algorithm'$ve>".lang(202).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" hidden>',lang(203).($yf?"<span$ve> (".lang(204).")</span>":"");if($yf||support("descidx"))echo
checkbox("options",1,$ij,lang(123),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(205);if(support("partial_indexes"))echo"<th id='label-condition'$ve>".lang(206);echo'<th><noscript>',icon("plus","add[0]","+",lang(124)),'</noscript>
<tbody>
';if($ai){echo"<tr><td>PRIMARY<td>";foreach($ai["columns"]as$w=>$c)echo
select_input(" disabled",array_combine($pd,$pd),$c),"<label><input disabled type='checkbox'>".lang(57)."</label> ";echo"<td><td>\n";}$cf=1;foreach($K["indexes"]as$u){if(!$_POST["drop_col"]||$cf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$cf][type]",array(-1=>"")+$Ee,$u["type"],($cf==count($K["indexes"])?on('change','indexesAddRow'):""),"label-type");if($Ce)echo"<td$ve>".html_select("indexes[$cf][algorithm]",array_merge(array(""),$Ce),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$c){echo"<span>".select_input(" name='indexes[$cf][columns][$r]' title='".lang(46)."'".on('change','indexesChangeColumn',(JUSH=="sql"?"":$_GET["indexes"]."_")),($l&&($c==""||$l[$c])?array_combine($pd,$pd):array()),$c)," <span$ve>",($yf?"<input type='number' name='indexes[$cf][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(122)."'>":"");if($Vg){$Ug=idx($u["opclasses"],$w);echo
html_select("indexes[$cf][opclasses][$r]",array(""=>"(".lang(207).")")+array_combine($Vg,$Vg)+($Ug!=""?array($Ug=>$Ug):array()),$Ug),'';}echo(support("descidx")?checkbox("indexes[$cf][descs][$r]",1,idx($u["descs"],$w),lang(57)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$cf][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$ve><input name='indexes[$cf][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$cf]","x",lang(126),on('click','editingRemoveRow','indexes$1[type]'));}$cf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(17),'\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$j&&!$_POST["add"]){$A=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(208),drop_databases(array(DB)));}elseif($A!==DB){if(DB!=""){$_GET["db"]=$A;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($A),lang(209),rename_database($A,(string)$K["collation"]));}else{$g=explode("\n",str_replace("\r","",$A));$Ej=true;$of="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$K["collation"]))$Ej=false;$of=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($of),lang(210),$Ej);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($A).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),lang(211));}}page_header(DB!=""?lang(65):lang(130),$j,array(),h(DB));$ob=collations();$A=DB;if($_POST)$A=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$ob);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Pd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Pd,$_)&&$_[1]){$A=stripcslashes(idf_unescape("`$_[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($A,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($A).'</textarea><br>':'<input name="name" autofocus value="'.h($A).'" data-maxlength="64" autocapitalize="off">')."\n",($ob?html_select("collation",array(""=>"(".lang(117).")")+$ob,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):"")."\n",'<input type=\'submit\' value=\'',lang(17),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(142)."'".confirm(lang(195,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(124))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);page_header(lang(212).": ".h($ca),$j);$Ii=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$Fi=routine($_GET["call"],$Ii);$ye=array();$nh=array();foreach($Fi["fields"]as$r=>$k){if(substr($k["inout"],-3)=="OUT"&&JUSH=='sql')$nh[$r]="@".idf_escape($k["field"])." AS ".idf_escape($k["field"]);if(!$k["inout"]||substr($k["inout"],0,2)=="IN")$ye[]=$r;}if(!$j&&$_POST){$Xa=array();foreach($Fi["fields"]as$w=>$k){$X="";if(in_array($w,$ye)){$X=process_input($k);if($X===false)$X="''";if(isset($nh[$w]))connection()->query("SET @".idf_escape($k["field"])." = $X");}if(isset($nh[$w]))$Xa[]="@".idf_escape($k["field"]);elseif(in_array($w,$ye))$Xa[]=$X;}$H=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Fi["returns"],"type")=="record"?"* FROM ":"").table($ca)."(".implode(", ",$Xa).")";$zj=microtime(true);$I=connection()->multi_query($H);$na=connection()->affected_rows;echo
adminer()->selectQuery($H,$zj,!$I);if(!$I)echo"<p class='error'>".adminer()->error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang(213,$na)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($nh)print_select_result(connection()->query("SELECT ".implode(", ",$nh)));}}echo'
<form action="" method="post">
';if($ye){echo"<table class='layout'>\n";foreach($ye
as$w){$k=$Fi["fields"][$w];$A=$k["field"];echo"<tr><th>".adminer()->fieldName($k);$Y=idx($_POST["fields"],$A);if($Y!=""){if($k["type"]=="set")$Y=implode(",",$Y);}input($k,$Y,idx($_POST["function"],$A,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(212),'\'>
',input_token(),'</form>

',adminer()->commentValue($Ii,$Fi['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$A=$_GET["name"];$K=$_POST;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Yj=array();foreach($K["source"]as$w=>$X)$Yj[$w]=$K["target"][$w];$K["target"]=$Yj;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $A"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$sa="ALTER TABLE ".table($a);$I=($A==""||queries("$sa DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($A)));if(!$K["drop"])$I=queries("$sa ADD".format_foreign_key($K));}queries_redirect(ME."table=".url_escape($a),($K["drop"]?lang(214):($A!=""?lang(215):lang(216))),$I);if(!$K["drop"])$j=lang(217);}page_header(($A!=""?lang(218):lang(154)),$j,array("table"=>$a),h($A!=""?$A:$a));if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($A!=""){$Cd=foreign_keys($a);$K=$Cd[$A];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$qj=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$jh=get_schema();set_schema($K["ns"]);}$qi=array_keys(array_filter(table_status('',true),function(array$S){return!$S["dependent"]&&fk_support($S);}));$Yj=array_keys(fields(in_array($K["table"],$qi)?$K["table"]:reset($qi)));$b=on('change','foreignChange');echo"<p><label>".lang(219).": ".html_select("table",$qi,$K["table"],$b)."</label>\n";if(JUSH!="sqlite"){$Xb=array();foreach(adminer()->databases()as$h){if(!information_schema($h))$Xb[]=$h;}echo"<label>".lang(76).": ".html_select("db",$Xb,$K["db"]!=""?$K["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(220),'\'></noscript>
<table>
<thead><tr><th id="label-source">',lang(151),'<th id="label-target">',lang(152),'<tbody>
';$cf=0;foreach($K["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$qj,$X,($cf==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$Yj,idx($K["target"],$w),"","label-target");$cf++;}echo'</table>
<p>
<label>',lang(119),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>',lang(118),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]).' ':''),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)),'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(221),'\'></noscript>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$kh="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$kh=strtoupper($P["Engine"]);}if($_POST&&!$j){$A=trim($K["name"]);$za=" AS\n$K[select]";$z=ME."table=".url_escape($A);$bg=lang(222);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$A&&JUSH!="sqlite"&&$U=="VIEW"&&$kh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($A).$za,$z,$bg);else{$ck="adminer_".uniqid();drop_create("DROP $kh ".table($a),"CREATE $U ".table($A).$za,"DROP $U ".table($A),"CREATE $U ".table($ck).$za,"DROP $U ".table($ck),($_POST["drop"]?substr(ME,0,-1):$z),lang(223),$bg,lang(224),$a,$A);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($kh!="VIEW");if(!$j)$j=adminer()->error();}page_header(($a!=""?lang(41):lang(225)),$j,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>',lang(205),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],lang(145)):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Pe=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Aj=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$j){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(226));elseif(in_array($K["INTERVAL_FIELD"],$Pe)&&isset($Aj[$K["STATUS"]])){$Li="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(227):lang(228)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Li.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$Li)."\n".$Aj[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?lang(229).": ".h($aa):lang(230)),$j);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(205),'<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(231),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(232),'<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>',lang(233),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Pe,$K["INTERVAL_FIELD"]),'<tr><th>',lang(133),'<td>',html_select("STATUS",$Aj,$K["STATUS"]),'<tr><th>',lang(48),'<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",lang(234)),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$Fi=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$j){foreach($K["fields"]as$w=>$k){if($k["field"]=="")unset($K["fields"][$w]);}$Og=routine_id($ca,routine($_GET["procedure"],$Fi));$zg=routine_id($K["name"],$K);$Hb=create_routine($Fi,$K);$z=substr(ME,0,-1);$bg=lang(235);if(!$_POST["drop"]&&$Og==$zg&&connection()->flavor!="mysql")query_redirect(substr_replace($Hb,' OR REPLACE',6,0),$z,$bg);else{$ck="adminer_".uniqid();drop_create("DROP $Fi $Og",$Hb,"DROP $Fi $zg",create_routine($Fi,array("name"=>$ck)+$K),"DROP $Fi ".routine_id($ck,$K),$z,lang(236),$bg,lang(237),$ca,$K["name"]);}}page_header(($ca!=""?(isset($_GET["function"])?lang(238):lang(239)).": ".h($ca):(isset($_GET["function"])?lang(240):lang(241))),$j);if(!$_POST){if($ca=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$Fi);$K["name"]=$ca;}}$ob=(JUSH=="sql"?flat_collations():array());$Gi=routine_languages();echo($ob?"<datalist id='collations'>".optionlist($ob)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(205),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Gi?"<label>".lang(23).": ".html_select("language",array_keys($Gi),$K["language"],on('change','routineLanguage',$Gi))."</label>\n":""),'<input type=\'submit\' value=\'',lang(17),'\'>
',doc_link(array('sql'=>"create-procedure.html",'mariadb'=>($Fi=="FUNCTION"?"create-function/":"create-procedure/"),),"?"),'<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$ob,$Fi);if(isset($_GET["function"])){echo"<tr><td>".lang(242);edit_type("returns",(array)$K["returns"],$ob,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20,80,($Gi[$K["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$ca)),'>
';$Hi=routine_options($Fi);if($Hi){$K["options"]=(array)$K["options"];$ah=false;foreach($Hi
as$w=>$fl){$i=($fl?reset($fl):"");$K["options"][$w]=idx($K["options"],$w,$i);if($K["options"][$w]!=$i)$ah=true;}print_fieldset("options",lang(123),$ah);echo"<table class='layout'>\n";foreach($Hi
as$w=>$fl){$jf="label-option-$w";$kk=str_replace("_"," ",$w);$M=array();foreach($fl
as$Y)$M[$Y]=(strpos($Y,"$kk ")===0?substr($Y,strlen($kk)+1):$Y);echo"<tr><th id='$jf'>$kk<td>".($M?html_select("options[$w]",$M,$K["options"][$w],"",$jf):"<input name='options[$w]' value='".h($K["options"][$w])."' aria-labelledby='$jf' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$A="$_GET[name]";$K=$_POST;if($K&&!$j){$z=ME."table=".url_escape($a);$eg=lang(243);$cg=lang(244);$dg=lang(245);if(JUSH=="sqlite")queries_redirect($z,($K["drop"]?$eg:($A!=""?$cg:$dg)),recreate_table($a,$a,array(),array(),array(),"",array(),"$A",($K["drop"]?"":$K["clause"])));else{$sa="ALTER TABLE ".table($a);$cb=" CHECK ($K[clause])";$ck="adminer_".uniqid();drop_create("$sa DROP CONSTRAINT ".idf_escape($A),"$sa ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"").$cb,"$sa DROP CONSTRAINT ".idf_escape($K["name"]),"$sa ADD CONSTRAINT ".idf_escape($ck).$cb,"$sa DROP CONSTRAINT ".idf_escape($ck),$z,$eg,$cg,$dg,$A,$K["name"]);}}page_header(($A!=""?lang(246):lang(156)),$j,array("table"=>$a),h($A!=""?$A:$a));if(!$K){$fb=driver()->checkConstraints($a);$K=array("name"=>$A,"clause"=>$fb[$A]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo
lang(205).': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'',lang(17),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$A="$_GET[name]";$_k=trigger_options();$K=(array)trigger($A,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$j&&in_array($_POST["Timing"],$_k["Timing"])&&in_array($_POST["Event"],$_k["Event"])&&in_array($_POST["Type"],$_k["Type"])){$Rg=" ON ".table($a);$zc="DROP TRIGGER ".idf_escape($A).(JUSH=="pgsql"?$Rg:"");$z=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($zc,$z,lang(247));else{if($A!="")queries($zc);queries_redirect($z,($A!=""?lang(248):lang(249)),queries(create_trigger($Rg,$_POST)));if($A!="")queries(create_trigger($Rg,$K+array("Type"=>reset($_k["Type"]))));}}$K=$_POST;}page_header(($A!=""?lang(250):lang(158)),$j,array("table"=>$a),h($A!=""?$A:$a));$zk=on('change','triggerChange',"^".preg_quote($a,"/")."_[ba][iud]$",$a);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(251),'<td>',html_select("Timing",$_k["Timing"],$K["Timing"],$zk),'<tr><th>',lang(252),'<td>',html_select("Event",$_k["Event"],$K["Event"],$zk),(in_array("UPDATE OF",$_k["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>',lang(47),'<td>',html_select("Type",$_k["Type"],$K["Type"]),'<tr><th>',lang(205),'<td><input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($A!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,$A)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($Pd,array$ei,$d,$Rg){if(!$ei)return
true;if($ei==array("ALL PRIVILEGES","GRANT OPTION"))return($Pd=="GRANT"?queries("$Pd ALL PRIVILEGES$Rg WITH GRANT OPTION"):queries("$Pd ALL PRIVILEGES$Rg")&&queries("$Pd GRANT OPTION$Rg"));return
queries("$Pd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$ei).$d).$Rg);}$ea=$_GET["user"];$ei=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$Bb)$ei[$Bb=="File access on server"?"Server Admin":$Bb][$K["Privilege"]]=$K["Comment"];}unset($ei["Server Admin"]["Usage"]);foreach($ei["Tables"]as$w=>$X)unset($ei["Databases"][$w]);$yg=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$yg[$X]=(array)$yg[$X]+idx($_POST["grants"],$w,array());}$Qd=array();if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$_)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$_[1],$Jf,PREG_SET_ORDER)){foreach($Jf
as$X){if($X[1]!="USAGE")$Qd["$_[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$Qd["$_[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$j){$Qg=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Qg",ME."privileges=",lang(253));else{$Ag=q($_POST["user"])."@".q($_POST["host"]);$Ch=$_POST["pass"];$Jb=false;$I=true;if($Qg!=$Ag){$Jb=queries("CREATE USER $Ag IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($Ch));$I=$Jb;}elseif($Ch!="")$I=queries("SET PASSWORD FOR $Ag = ".(min_version(8,99)||$_POST["hashed"]?q($Ch):"PASSWORD(".q($Ch).")"));if($I){$Bi=array();foreach($yg
as$Gg=>$Pd){if(isset($_GET["grant"]))$Pd=array_filter($Pd);$Pd=array_keys($Pd);if(isset($_GET["grant"]))$Bi=array_diff(array_keys(array_filter($yg[$Gg],'strlen')),$Pd);elseif($Qg==$Ag){$Ng=array_keys((array)$Qd[$Gg]);$Bi=array_diff($Ng,$Pd);$Pd=array_diff($Pd,$Ng);unset($Qd[$Gg]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Gg,$_)&&(!grant("REVOKE",$Bi,$_[2]," ON $_[1] FROM $Ag")||!grant("GRANT",$Pd,$_[2]," ON $_[1] TO $Ag"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($Qg!=$Ag)queries("DROP USER $Qg");elseif(!isset($_GET["grant"])){foreach($Qd
as$Gg=>$Bi){if(preg_match('~^(.+)(\(.*\))?$~U',$Gg,$_))grant("REVOKE",array_keys($Bi),$_[2]," ON $_[1] FROM $Ag");}}}if($I&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(254):lang(255)),$I);if($Jb)connection()->query("DROP USER $Ag");}}page_header((isset($_GET["host"])?lang(31).": ".h("$ea@$_GET[host]"):lang(167)),$j,array("privileges"=>array('',lang(69))));$K=$_POST;if($K)$Qd=$yg;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Qd[(DB==""||$Qd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(29),'<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>',lang(31),'<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>',lang(32),'<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],lang(256),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(69).doc_link(array('sql'=>"grant.html#priv_level"));$r=0;foreach($Qd
as$Gg=>$Pd){echo'<th>'.($Gg!="*.*"?"<input name='objects[$r]' value='".h($Gg)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(29),"Databases"=>lang(33),"Tables"=>lang(147),"Procedures"=>lang(257),)as$Bb=>$gc){foreach((array)$ei[$Bb]as$di=>$sb){echo"<tr><td".($gc?">$gc<td":" colspan='2'").' lang="en" title="'.h($sb).'">'.h($di);$r=0;foreach($Qd
as$Gg=>$Pd){$A="'grants[$r][".h(strtoupper($di))."]'";$Y=$Pd[strtoupper($di)];if($Bb=="Server Admin"&&$Gg!=(isset($Qd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$A><option><option value='1'".($Y?" selected":"").">".lang(258)."<option value='0'".($Y=="0"?" selected":"").">".lang(259)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$A value='1'".($Y?" checked":"").($di=="All privileges"?" id='grants-$r-all'":($di=="Grant option"?"":on('click','grantsClick',"grants-$r-all"))).">","</label>";$r++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(142),'\'',confirm(lang(195,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$j){$if=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$if++;}queries_redirect(ME."processlist=",lang(260,$if),$if||!$_POST["kill"]);}}page_header(lang(131),$j);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$r=-1;foreach(adminer()->processList()as$r=>$K){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$w=>$X)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$w=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($K["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($K["db"])."&":ME)."sql=".url_escape($X)).'">'.lang(261).'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($r+1)."/".lang(262,max_connections()),"<p><input type='submit' value='".lang(263)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$l=fields($a);$Cd=column_foreign_keys($a);$Mg=$S["Oid"];$Di=array();$d=array();$Qi=array();$ch=array();$fk=null;foreach($l
as$w=>$k){$A=adminer()->fieldName($k);$vg=html_entity_decode(strip_tags($A),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$A!=""){$d[$w]=$vg;if(is_shortable($k))$fk=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$A!="")$Qi[$w]=$vg;if(isset($k["privileges"]["order"])&&$A!="")$ch[$w]=$vg;$Di+=$k["privileges"];}list($M,$q)=adminer()->selectColumnsProcess($d,$v);$M=array_unique($M);$q=array_unique($q);$We=count($q)<count($M);$Z=adminer()->selectSearchProcess($l,$v,$S);$D=adminer()->selectOrderProcess($l,$v);$x=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Kk=>$K){$za=convert_field($l[key($K)]);$M=array($za?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($Kk,true),$l);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$ai=$Mk=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$ai=array_flip($u["columns"]);$Mk=($M?$ai:array());foreach($Mk
as$w=>$X){if(in_array(idf_escape($w),$M))unset($Mk[$w]);}break;}}if($Mg&&!$ai){$ai=$Mk=array($Mg=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($Mg));}if($_POST&&!$j){$ql=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$fb=array();foreach($_POST["check"]as$cb)$fb[]=where_check($cb,$l);$ql[]="((".implode(") OR (",$fb)."))";}$sl=$ql;$ql=($ql?"\nWHERE ".implode(" AND ",$ql):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$Si=($M?:array("*"));$Db=convert_fields($d,$l,$M);if($Db)$Si[]=substr($Db,2);$H="";if(is_array($_POST["check"])&&!$ai){$Hd=implode(", ",$Si)."\nFROM ".table($a);$Td=($q&&$We?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):"");$Ik=array();foreach($_POST["check"]as$X)$Ik[]="(SELECT".limit($Hd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l).$Td,1).")";$H=implode(" UNION ALL ",$Ik);}adminer()->dumpData($a,"table",$H,$Si,$sl,($We?$q:array()),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Cd)){if($_POST["save"]||$_POST["delete"]){$I=true;$na=0;$Na=false;$O=array();if(!$_POST["delete"]){foreach($l
as$A=>$X){$t=bracket_escape($A);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($l[$A]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($A)]=($X!==false?$X:idf_escape($A));}}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($ai&&is_array($_POST["check"]))||$We){$I=($_POST["delete"]?driver()->delete($a,$ql):($_POST["clone"]?queries("INSERT $H$ql".driver()->insertReturning($a)):driver()->update($a,$O,$ql)));$na=connection()->affected_rows;if(is_object($I))$na+=$I->num_rows;}else{$Na=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$pl="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l);$I=($_POST["delete"]?driver()->delete($a,$pl,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$pl)):driver()->update($a,$O,$pl,1)));if(!$I)break;$na+=connection()->affected_rows;}if($Na&&$I&&!driver()->commit())$I=false;}}$bg=lang(264,$na);if($_POST["clone"]&&$I&&$na==1){$qf=last_id($I);if($qf)$bg=lang(188," $qf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$bg,$I);if($Na)driver()->rollback();if(!$_POST["delete"]){$Sh=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$Sh),$Sh,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$na=0;$Na=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$Kk=>$K){$O=array();foreach($K
as$w=>$X){$w=bracket_escape($w,true);$O[idf_escape($w)]=(preg_match('~char|text~',$l[$w]["type"])||$X!=""?adminer()->processInput($l[$w],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Kk,true),$l),($We||$ai?0:1)," ");if(!$I)break;$na+=connection()->affected_rows;}if($Na)$I=$I&&driver()->commit();queries_redirect(remove_from_uri(),lang(264,$na),$I);if($Na)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$qd=get_file("csv_file",true);if(!is_string($qd))$j=upload_error($qd);elseif(!preg_match('~~u',$qd))$j=lang(265);else{$pb=array_keys($l);$Xi=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Nb=parse_csv($qd,$Xi);$na=count($Nb);driver()->begin();$L=array();foreach($Nb
as$w=>$fl){if(!$w&&!array_diff($fl,$pb)){$pb=$fl;$na--;}else{$O=array();foreach($fl
as$r=>$lb)$O[idf_escape($pb[$r])]=($lb==""&&$l[$pb[$r]]["null"]?"NULL":q(csv_value($lb)));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$ai));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(266,$na),$I);driver()->rollback();}}}}$Nj=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(51).": $Nj",$j);$O=null;if(isset($Di["insert"])||!support("table")){$O="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$_)?$_[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$O
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$O);if(!$d&&support("table"))echo"<p class='error'>".lang(267).($l?".":": ".adminer()->error())."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$Qi,$v,$S);adminer()->selectOrderPrint($D,$ch,$v);adminer()->selectLimitPrint($x);if($fk!==null)adminer()->selectLengthPrint($fk);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(112).' '.lang(113)."\n";page_footer();exit;}}$E=$_GET["page"];$Fd=null;if($E=="last"){$Fd=get_val(count_rows($a,$Z,$We,$q));$E=floor(max(0,intval($Fd)-1)/$x);}$Ri=$M;$Sd=$q;if(!$Ri){$Ri[]="*";$Db=convert_fields($d,$l,$M);if($Db)$Ri[]=substr($Db,2);}foreach($M
as$w=>$X){$k=$l[idf_unescape($X)];if($k&&($za=convert_field($k)))$Ri[$w]="$za AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$X){if(isset($Ri[$w])&&$X["fun"])$Ri[$w].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$We&&$Mk){foreach($Mk
as$w=>$X){$Ri[]=idf_escape($w);if($Sd)$Sd[]=idf_escape($w);}}$I=driver()->select($a,$Ri,$Z,$Sd,$D,$x,$E,true);if(!is_object($I))echo"<p class='error'>".(adminer()->error()?:lang(25))."\n";else{if(JUSH=="mssql"&&$E)$I->seek($x*$E);$Ic=array();$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$de=($x&&(support("cursor")?$_GET["next"]!="":count($L)>=$x));if(is_ajax()&&$de)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$L){$Sf=max_input_vars(count($L[0])+1,20);echo($Sf&&count($L)>$Sf?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($Rk).">\n";if($_GET["page"]!="last"&&$x&&$q&&$We&&JUSH=="sql")$Fd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".lang(15)."\n";else{$Ja=adminer()->backwardKeys($a,$Nj);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$q&&$M?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".lang(268)."'".on('click','formCheck','^check').">");$wg=array();$Md=array();reset($M);$ni=1;foreach($L[0]as$w=>$X){if(!isset($Mk[$w])){$X=idx($_GET["columns"],key($M))?:array();$k=$l[$M?($X?$X["col"]:current($M)):$w];$A=($k?adminer()->fieldName($k,$ni):($X["fun"]?"*":h($w)));if($A!=""){$ni++;$wg[$w]=$A;$c=idf_escape($w);$qe=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$gc="&desc[0]=1";$nj=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$pj=($nj==$c||$nj==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($pj?" aria-sort='".($nj==$D[0]?"ascending":"descending")."'":"").">";$Ld=apply_sql_function($X["fun"],$A);$oj=isset($k["privileges"]["order"])||$Ld!=$A;echo($oj?"<a href='".h($qe.($pj&&$nj==$D[0]?$gc:''))."'>$Ld</a>":$Ld);$ag=($oj?"<a href='".h($qe.$gc)."' title='".lang(57)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($k["privileges"]["where"]))$ag
.="<a href='#fieldset-search' title='".lang(54)."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($ag?"<span class='column'>$ag</span>":"");}$Md[$w]=$X["fun"];next($M);}}$yf=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$w=>$X)$yf[$w]=max($yf[$w],min(40,strlen(utf8_decode($X))));}}echo($Ja?"<th>".lang(269):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$Cd)as$tg=>$K){$Jk=unique_array($L[$tg],$v);if(!$Jk){$Jk=array();reset($M);foreach($L[$tg]as$w=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$Jk[$w]=$X;next($M);}}$Kk="";foreach($Jk
as$w=>$X){$k=(array)$l[$w];$Ve=is_blob($k);if((JUSH=="sql"||JUSH=="pgsql")&&($Ve||preg_match('~'.text_type().'~',$k["type"]))&&strlen($X)>64){$w=(strpos($w,'(')?$w:idf_escape($w));$w="MD5(".($Ve||JUSH!='sql'||preg_match("~^utf8~",$k["collation"])?$w:"CONVERT($w USING ".charset(connection()).")").")";$X=md5($Ve?(string)driver()->value($X,$k):$X);}$Kk
.="&".($X!==null?"where[".url_escape(bracket_escape($w))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($w));}echo"<tr>".(!$q&&$M?"":"<td class='hover check'>".($We||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Kk)."' class='edit'>".lang(270)."</a> ").checkbox("check[]",substr($Kk,1),in_array(substr($Kk,1),(array)$_POST["check"])));reset($M);foreach($K
as$w=>$X){if(isset($wg[$w])){$c=current($M);$k=(array)$l[$w];if($X!=""&&(!isset($Ic[$w])||$Ic[$w]!=""))$Ic[$w]=(is_mail($X)?$wg[$w]:"");$y="";if(is_blob($k)&&$X!="")$y=ME.'download='.url_escape($a).'&field='.url_escape($w).$Kk;if(!$y&&$X!==null){foreach((array)$Cd[$w]as$n){if(count($Cd[$w])==1||end($n["source"])==$w){$y="";foreach($n["source"]as$r=>$qj)$y
.=where_link($r,$n["target"][$r],$L[$tg][$qj]);$y=($n["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($n["db"]),ME):ME).'select='.url_escape($n["table"]).$y;if($n["ns"])$y=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($n["ns"]),$y);if(count($n["source"])==1)break;}}}if($c=="COUNT(*)"){$y=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Jk))$y
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($Jk
as$ff=>$W)$y
.=where_link($r++,$ff,$W);}$re=select_value($X,$y,$k,$fk);$t=bracket_escape($Kk);$s=h("val[$t][".bracket_escape($w)."]");$Uh=idx(idx($_POST["val"],$t),bracket_escape($w));$Pk=idx($k["privileges"],"update");$Ec=!is_array($K[$w])&&!is_blob($k)&&is_utf8($X)&&$L[$tg][$w]==$X&&!$Md[$w]&&!$k["generated"]&&$Pk;$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$c,$_)?$l[idf_unescape($_[2])]["type"]:$k["type"]);$ek=preg_match('~text|json|lob~',$U);$Xe=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$c);echo"<td id='$s'".($Xe&&($X===null||is_numeric(strip_tags($re))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Ec&&$X!==null)||$Uh!==null){$Yd=h($Uh!==null?$Uh:$X);echo">".($ek?"<textarea name='$s' cols='30' rows='".(substr_count($X,"\n")+1)."'>$Yd</textarea>":"<input name='$s' value='$Yd' size='$yf[$w]'>");}else{$Gf=strpos($re,"<i>…</i>");echo($Pk?" data-text='".($Gf?2:($ek?1:0))."'".($Ec?"":" data-warning='".lang(271)."'"):"").">$re";}}next($M);}if($Ja)echo"<td>";adminer()->backwardKeysPrint($Ja,$L[$tg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$ma=get_settings("adminer_import");if($L||$E||$de){$Xc=true;if($_GET["page"]!="last"){if(!$x||(count($L)<$x&&($L||!$E)))$Fd=($E?$E*$x:0)+count($L);elseif(JUSH!="sql"||!$We){$Fd=($We?false:found_rows($S,$Z));if(intval($Fd)<max(1e4,2*($E+1)*$x))$Fd=first(slow_query(count_rows($a,$Z,$We,$q)));elseif(JUSH=='sql'||JUSH=='pgsql')$Xc=false;}}if(!support("cursor"))$de=(($Fd===false?count($L)+1:$Fd-$E*$x)>$x);$qh=($x&&($de||$E));if($qh)echo($de?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(272)).'>'.lang(273).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($qh){$Qf=($Fd===false?$E+($L?(count($L)>=$x?2:1):0):floor(($Fd-1)/$x));echo"<fieldset><legend>".lang(274)."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($Qf,$E+5);$r++)echo
pagination($r,$E);if($Qf>0)echo($E+5<$Qf?" …":""),($Xc&&$Fd!==false?pagination($Qf,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Qf'>".lang(275)."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($de?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(276)."</legend>";$oc=($Xc?"":"~ ").$Fd;$jf=($Fd!==false?($Xc?"":"~ ").lang(171,$Fd):"");echo
checkbox("all",1,0,$jf,on('click','countRows',$oc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(277)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(278),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(17),'\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(141),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(13),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(261),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(21),'\'',confirm(),'>
</div></fieldset>
';$Dd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($Dd['sql']);break;}}if($Dd){print_fieldset("export",lang(74)." <span id='selected2'></span>");$oh=adminer()->dumpOutput();echo($oh?html_select("output",$oh,$ma["output"])." ":""),html_select("format",$Dd,$ma["format"])," <input type='submit' name='export' value='".lang(74)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Ic,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(73)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($Rk?input_hidden(ini_get("session.upload_progress.name"),$Rk):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ma["format"])." <input type='submit' name='import' value='".lang(73)."'>".($Rk?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$q&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?lang(133):lang(132));$gl=($P?adminer()->showStatus():adminer()->showVariables());if(!$gl)echo"<p class='message'>".lang(15)."\n";else{echo"<table>\n";foreach($gl
as$K){echo"<tr>";$w=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($w)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Hj=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$A=>$S){json_row("Comment-$A",h($S["Comment"]).($S["Error"]?" <span class='error'>".h($S["Error"])."</span>":""));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$A",h($S[$w]));foreach(array_keys($Hj+array("Auto_increment"=>0,"Rows"=>0))as$w){if(array_key_exists($w,$S))json_row("$w-$A",format_status($S,$w));if($S[$w]!=""&&isset($Hj[$w]))$Hj[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}}}if(function_exists('Adminer\db_status'))$Hj=db_status();foreach($Hj
as$w=>$X)json_row("sum-$w",format_number($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$Xf=ME.(isset($_GET["select"])?"select=&":"");$Wj=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Wj&&!$j&&!$_POST["search"]){$I=true;$bg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$bg=lang(279);}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$bg=lang(280);}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$bg=lang(281);}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$bg=lang(282);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$bg
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("DBCC CHECKTABLE (".q(table($R)).") WITH TABLERESULTS")as$K)$bg
.="<b>".h($R)."</b>: ".h($K["MessageText"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$bg=lang(283);}elseif(!$_POST["tables"])$bg=lang(12);elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$bg
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(relative_uri(),$bg,$I);}page_header(($_GET["ns"]==""?lang(33).": ".h(DB):lang(166).": ".h($_GET["ns"])),$j,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$Id=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(284)."</h3>\n";$Vj=($Id?table_status():tables_list());if(!$Vj)echo"<p class='message'>".lang(12)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(285)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(54)."'>\n","</div></fieldset>\n";if(!$j&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr class="wrap">','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(165).'"'.on('click','formCheck','^(tables|views)\[').'>','<th'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($Xf,0,-1)).'">'.lang(147).'</a>';$d=array("Engine"=>array(lang(286).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$d["Collation"]=array(lang(137).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$d["Data_length"]=array(lang(287).doc_link(array('sql'=>'show-table-status.html',)),"create",lang(42),);if(support("indexes"))$d["Index_length"]=array(lang(288).doc_link(array('sql'=>'show-table-status.html',)),"indexes",lang(150),);$d["Data_free"]=array(lang(289).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(43));if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array(lang(49).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(42),);$d["Rows"]=array(lang(290).doc_link(array('sql'=>'show-table-status.html',)),"select",lang(39),);if(support("comment"))$d["Comment"]=array(lang(48).doc_link(array('sql'=>'show-table-status.html',)));$_a=array('Engine','Collation','Comment');foreach($d
as$w=>$c)echo"<th".($D==$w?" aria-sort='".(in_array($w,$_a)?"ascending":"descending")."'":"")."><a href='".h($Xf)."order=$w'>$c[0]</a>";echo"<tbody>\n";if($D){uasort($Vj,function($ga,$Ga)use($D,$_a){$J=($ga[$D]<$Ga[$D]?-1:($ga[$D]>$Ga[$D]?1:0));return(in_array($D,$_a)?$J:-$J);});}$T=0;$Hj=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($Vj
as$A=>$P){$jl=($Id?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($Id?$P:array('Engine'=>$P));$s=h("Table-".$A);echo'<tr><td class="hover">'.checkbox(($jl?"views[]":"tables[]"),$A,in_array("$A",$Wj,true),"","","",$s),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($A)."' title='".lang(40)."' id='$s'>".h($A).'</a>':h($A));if($jl&&!preg_match('~materialized~i',$P['Engine'])){$kk=lang(146);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($A)."' title='".lang(41)."'>$kk</a>":$kk),"<td align='right'><a href='".h(ME)."select=".url_escape($A)."' title='".lang(39)."'>?</a>";if(support("comment"))echo'<td>'.h($P['Comment']);}else{if($Id){foreach(array_keys($Hj)as$w)$Hj[$w]+=($P["Engine"]!="InnoDB"||$w!="Data_free"?idx($P,$w):0);}foreach($d
as$w=>$c){$s=" id='$w-".h($A)."'";echo($c[1]?"<td align='right'><a href='".h(ME."$c[1]=").url_escape($A)."'$s title='$c[2]'>".format_status($P,$w)."</a>":"<td$s>".h(idx($P,$w,'?')).($w=="Comment"&&$P["Error"]?" <span class='error'>".h($P["Error"])."</span>":""));}$T++;}echo"\n";}echo"<tr><td class='hover'><th>".lang(262,count($Vj)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Id&&function_exists('Adminer\db_status'))$Hj=db_status();foreach($Hj
as$w=>$Gj)echo($d[$w]?"<td align='right' id='sum-$w'>".($Id?format_number($Gj):""):"");echo"\n","</table>\n",($Id?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$cl="<input type='submit' value='".lang(291)."'".on_help("VACUUM")."> ";$Yg="<input type='submit' name='optimize' value='".lang(292)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$bi=(JUSH=="sqlite"?$cl."<input type='submit' name='check' value='".lang(293)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$cl.$Yg:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(293)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(294)."'".on_help("ANALYZE TABLE")."> ".$Yg."<input type='submit' name='check' value='".lang(293)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(295)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(296)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(142)."'".confirm().on_help("DROP TABLE").">":"");echo($bi?"<div class='footer'><div>\n<fieldset><legend>".lang(141)." <span id='selected'></span></legend><div>$bi\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(297)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(125)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(22)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(298)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(75)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(225)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(70)."</h3>\n";$Ji=routines();if($Ji){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(205).'<td>'.lang(47).'<td>'.lang(242)."<td class='hover'><tbody>\n";foreach($Ji
as$K){$A=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$A).'" title="'.lang(212).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$A).'">'.lang(153)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(241).'</a>':'').'<a href="'.h(ME).'function=">'.lang(240)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(72)."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".lang(205)."<td>".lang(299)."<td>".lang(231)."<td>".lang(232)."<td class='hover'><tbody>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?lang(300)."<td>".h($K["Execute at"]):lang(233)." ".h($K["Interval value"])." ".h($K["Interval field"])."<td>".h($K["Starts"])),"<td>".h($K["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($K["Name"]).'">'.lang(153).'</a>';echo"</table>\n";$Uc=get_val("SELECT @@event_scheduler");if($Uc&&$Uc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Uc)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(230)."</a>\n","</div>\n";}}}}page_footer();