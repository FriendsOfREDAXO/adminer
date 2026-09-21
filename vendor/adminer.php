<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.1.0
*/namespace
Adminer;if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];const
VERSION="6.1.0";error_reporting(24575);set_error_handler(function($Uc,$Wc){return!!preg_match('~^Undefined (array key|offset|index)~',$Wc);},E_WARNING|E_NOTICE);$zd=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($zd||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$hl=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($hl)$$X=$hl;}}$_COOKIE=array_filter($_COOKIE,'is_scalar');if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Nb=adminer()->credentials();$J=Driver::connect($Nb[0],$Nb[1],$Nb[2]);return(is_object($J)?$J:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$uf=substr($t,-1);return
str_replace($uf.$uf,$uf,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($_a,$w,$i=null){return($_a&&array_key_exists($w,$_a)?$_a[$w]:$i);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|number|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
text_type(){return'char|text'.(JUSH=="sql"?'|enum|set':'');}function
is_searchable(array$k,array$X){if(!isset($k["privileges"]["where"]))return
false;$U=$k["type"];$gj=$X["val"];$Qa='binary$|bytea|raw|image|bfile|^vector$'.(JUSH=="mssql"?'|^timestamp$':'|^bit').(JUSH=="oracle"?'|^blob|^long|rowid':'');if(preg_match("~$Qa~",$U))return
false;if(preg_match(number_type(),$U)){$Og='-?\d+(\.\d+)?';return(bool)preg_match('~^'.$Og.(preg_match('~IN$~',$X["op"])?"( *, *$Og)*":'').'$~',$gj);}if(preg_match('~^(small)?date|^timestamp~',$U))return(bool)preg_match('~^\d+-\d+-\d+~',$gj);if(preg_match('~^time~',$U))return(bool)preg_match('~^\d+:\d+~',$gj);if(preg_match('~^bool~',$U)||(JUSH=="mssql"&&$U=="bit"))return(bool)preg_match('~^(t|f|true|false|[01])$~i',$gj);return
true;}function
remove_slashes(array$Cl,$zd=false){$J=array();foreach($Cl
as$w=>$X)$J[stripslashes($w)]=(is_array($X)?remove_slashes($X,$zd):($zd?$X:stripslashes($X)));return$J;}function
bracket_escape($t,$Ja=false){static$Ok=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($t,($Ja?array_flip($Ok):$Ok));}function
url_escape($Q){static$Ok=array();if(!$Ok){$Ok=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$cb)$Ok[$cb]=sprintf('%%%02X',ord($cb));for($r=0;$r<256;$r++){if($r<32||$r>126)$Ok[chr($r)]=sprintf('%%%02X',$r);}}return
strtr((string)$Q,$Ok);}function
min_version($Fl,$Nf="",$f=null){$f=connection($f);$uj=$f->server_info;if($Nf&&preg_match('~([\d.]+)-MariaDB~',$uj,$A)){$uj=$A[1];$Fl=$Nf;}return$Fl&&version_compare($uj,$Fl)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($jh,$Y){return(function_exists('ini_set')?\ini_set($jh,$Y):false);}function
ini_bool($Ne){$X=ini_get($Ne);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Ne){$X=ini_get($Ne);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$wh){$Qf=(int)ini_get("max_input_vars");return($Qf?(int)floor(($Qf-$wh)/$K):0);}function
max_input_vars_error(){$Ne="max_input_vars";return
lang(0,"<b>$Ne = ".ini_get($Ne)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($El,$N,$V,$F){$_SESSION["pwds"][$El][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$k=0,$Ab=null){$Ab=connection($Ab);$I=$Ab->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$k]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$xj=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($xj)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$j="<p class='error'>"){$Ab=connection($f);$J=array();$I=$Ab->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$j&&(defined('Adminer\PAGE_HEADER')||$j=="-- "))echo$j.adminer()->error()."\n";return$J;}function
unique_array($K,array$v){foreach($v
as$u){if(preg_match("~^(PRIMARY|UNIQUE)$~",$u["type"])&&!$u["partial"]){$J=array();foreach($u["columns"]as$w){if(!isset($K[$w]))continue
2;$J[$w]=$K[$w];}return$J;}}}function
where_function($Pd,$c,array$k){if($Pd=="md5")return"MD5(".(is_blob($k)||JUSH!='sql'||preg_match("~^utf8~",$k["collation"])?$c:"CONVERT($c USING ".charset(connection()).")").")";return(in_array($Pd,driver()->functions)||in_array($Pd,driver()->grouping)?apply_sql_function($Pd,$c):$c);}function
where(array$Z,array$l=array()){$J=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$c=idf_escape($w);$k=idx($l,$w,array());$td=$k["type"];$af=$k&&(is_blob($k)||preg_match('~binary~',$td));$J[]=$c.($af&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$td=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$k["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($td,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($k,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$td)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$J[]=idf_escape($w)." IS NULL";foreach((array)$Z["col"]as$r=>$ob){$X=idx($Z["val"],$r);$J[]=where_function(idx($Z["fun"],$r),idf_escape($ob),idx($l,$ob,array())).($X!==null?" = ".q($X):" IS NULL");}return
implode(" AND ",$J);}function
where_columns(array$l){$J=array();foreach((array)$_GET["null"]as$w)$J[$w]=true;foreach(array_keys((array)$_GET["where"])as$w)$J[bracket_escape($w,true)]=true;foreach((array)$_GET["col"]as$ob)$J[$ob]=true;return
array_intersect_key($J,$l);}function
where_check($X,array$l=array()){parse_str($X,$fb);remove_slashes(array(&$fb));return
where($fb,$l);}function
where_link($r,$c,$Y,$gh="="){$dh=($Y!==null?$gh:"IS NULL");return"&where[$r][col]=".url_escape($c).($dh!=first(adminer()->operators())?"&where[$r][op]=".url_escape($dh):"")."&where[$r][val]=".url_escape($Y);}function
convert_fields(array$d,array$l,array$M=array()){$J="";foreach($d
as$w=>$X){if($M&&!in_array(idf_escape($w),$M))continue;$Aa=convert_field($l[$w]);if($Aa)$J
.=", $Aa AS ".idf_escape($w);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($B,$Y,$Df=2592000){header("Set-Cookie: $B=".rawurlencode($Y).($Df?"; expires=".gmdate("D, d M Y H:i:s",time()+$Df)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($B=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($ql,$Eb){$http_response_header=null;$Vc=array();set_error_handler(function($Uc,$j)use(&$Vc){$Vc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$j);return
true;});$J=file_get_contents($ql,false,$Eb);restore_error_handler();$le=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($le,0,''),$A)?$A[1]:''),(array)$le,($J===false?implode("\n",$Vc):''),);}function
get_settings($Hb){parse_str($_COOKIE[$Hb],$yj);return$yj;}function
get_setting($w,$Hb="adminer_settings",$i=null){return
idx(get_settings($Hb),$w,$i);}function
save_settings(array$yj,$Hb="adminer_settings"){$Y=http_build_query($yj+get_settings($Hb));cookie($Hb,$Y);$_COOKIE[$Hb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($Dd=false){$tl=ini_bool("session.use_cookies");if(!$tl||$Dd){session_write_close();if($tl&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($El,$N,$V,$h=null){$pl=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($h!==null?"db|":"").($El=='mssql'||$El=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$pl,$A);return"$A[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($El!="server"||$N!=""?url_escape($El)."=".url_escape($N)."&":"")."username=".url_escape($V).($h!=""?"&db=".url_escape($h):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_,$gg=null){if($gg!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_!==null?$_:$_SERVER["REQUEST_URI"]))][]=$gg;}if($_!==null){if($_=="")$_=".";header("Location: $_");exit;}}function
query_redirect($H,$_,$gg,$zi=true,$dd=true,$od=false,$Bk=""){if($dd){$Qj=microtime(true);$od=!connection()->query($H);$Bk=format_time($Qj);}$Kj=($H?adminer()->messageQuery($H,$Bk,$od):"");if($od){adminer()->error
.=adminer()->error().$Kj.script("messagesPrint();")."<br>";return
false;}if($zi)redirect($_,$gg.$Kj);return
true;}class
Queries{static$queries=array();static$start=0;}function
remember_query($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");}function
queries($H){remember_query($H);return
connection()->query($H);}function
apply_queries($H,array$T,$Xc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Xc($R)))return
false;}return
true;}function
queries_redirect($_,$gg,$zi){$ui=implode("\n",Queries::$queries);$Bk=format_time(Queries::$start);return
query_redirect($ui,$_,$gg,$zi,false,!$zi,$Bk);}function
format_time($Qj){return
lang(1,max(0,microtime(true)-$Qj));}function
relative_uri($pl=''){return
preg_replace_callback('~^[^?]*~',function($A){return
str_replace(":","%3A",$A[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',($pl?:$_SERVER["REQUEST_URI"])));}function
remove_from_uri($Ah=""){return
substr(preg_replace("~(?<=[?&])($Ah".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($B,$cc=false){$vd=$_FILES[$B];if(!$vd)return
null;foreach($vd
as$w=>$X)$vd[$w]=(array)$X;$J=array();foreach($vd["error"]as$w=>$j){if($j)return$j;$m=$vd["name"][$w];$Jk=$vd["tmp_name"][$w];$Cb=file_get_contents($cc&&preg_match('~\.gz$~',$m)?"compress.zlib://$Jk":$Jk);if($cc){$Qj=substr($Cb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Qj))$Cb=iconv("utf-16","utf-8",$Cb);elseif($Qj=="\xEF\xBB\xBF")$Cb=substr($Cb,3);}$J[]=array($m,$Cb);}return$J;}function
get_file($w,$cc=false,$jc=""){$yd=get_files($w,$cc);if(!is_array($yd))return$yd;$J='';foreach($yd
as$vd){$Cb=$vd[1];$J
.=$Cb;if($jc)$J
.=(preg_match("($jc\\s*\$)",$Cb)?"":$jc)."\n\n";}return$J;}function
upload_error($j){$Yf=($j==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($j?lang(2).($Yf?" ".lang(3,$Yf):""):lang(4));}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
utf8_length($X){return
strlen(preg_replace('~[\x80-\xBF]~','',$X));}function
format_number($X){preg_match('~^#+([^#0]+)(?:(#+)\1)?(#*0)$~u',lang(5),$A);$Bj=strlen($A[3]);$J=number_format($X,0,".","");$J=preg_replace('~\B(?=(\d{'.(strlen($A[2])?:$Bj).'})*\d{'.$Bj.'}$)~',$A[1],$J);return
strtr($J,preg_split('~~u',lang(6),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$w){$X=idx($S,$w,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$xa=($w=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($xa?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$pd=false){$J=table_status($R,$pd);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$n){foreach($n["source"]as$X)$J[$X][]=$n;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$B=bracket_escape($w,true);$J[$B]=array("field"=>$B,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($B==driver()->primary),);}return$J;}function
dump_headers($xe,$xg=false){$J=adminer()->dumpHeaders($xe,$xg);$yh=$_POST["output"];if($yh!="text"||$J=="tar"){$yb=($yh!="text"&&$yh!="file"&&preg_match('~^[0-9a-z]+$~',$yh)?".$yh":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($xe).".$J$yb");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$Xk=$_POST["format"]=="tsv";foreach($K
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Xk?'\t':'[,;]|^$').'~',$X))$K[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($Xk?"\t":";")),$K)."\r\n";}function
parse_csv($Qb,$pj){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Qb,$Of);foreach($Of[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$pj]*)$pj~",$K.$pj,$Pf);$J[]=$Pf[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($p,$c){return($p?($p=="unixepoch"?"DATETIME($c, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($m){if(is_link($m))return;$o=@fopen($m,"c+");if(!$o)return;@chmod($m,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Ub){rewind($o);fwrite($o,$Ub);ftruncate($o,strlen($Ub));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$_a){return
reset($_a);}function
password_file($Kb){$m=get_temp_dir()."/adminer.key";if(!$Kb&&!file_exists($m))return'';$o=file_open_lock($m);if(!$o)return'';$J=stream_get_contents($o);if(!$J){$J=rand_string();file_write_unlock($o,$J);}else
file_unlock($o);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$z,array$k,$_k){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$mf=array();foreach($X
as$W)$mf+=array_fill_keys(array_keys($W),null);foreach(array_keys($mf)as$kf)$J
.="<th>".h($kf);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($mf,$W)as$zl)$J
.="<td>".select_value($zl,$z,$k,$_k);}}else{foreach($X
as$kf=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($kf):"")."<td>".select_value($W,$z,$k,$_k);}return"<table>$J</table>";}if(!$z)$z=adminer()->selectLink($X,$k);if($z===null){if(is_mail($X))$z="mailto:$X";if(is_url($X))$z=$X;}$X=driver()->value($X,$k);$J=adminer()->editVal($X,$k);if($J!==null){if(!is_utf8($J))$J="\0";elseif($_k!=""&&is_shortable($k))$J=shorten_utf8($J,max(0,+$_k));else$J=h($J);}return
adminer()->selectVal($J,$z,$k,$X);}function
is_blob(array$k){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$k["type"])&&!in_array($k["type"],idx(driver()->structuredTypes(),lang(7),array()));}function
is_mail($Lc){$Ca='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$_c='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Sh="$Ca+(\\.$Ca+)*@($_c?\\.)+$_c";return
is_string($Lc)&&preg_match("(^$Sh(,\\s*$Sh)*\$)i",$Lc);}function
is_url($Q){$_c='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($_c?\\.)+$_c(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_ipv6($ja){$q='[\da-f]{1,4}';$Ze='\d{1,3}(\.\d{1,3}){3}';return(bool)preg_match("~^(($q:){7}$q|($q:){6}$Ze|(($q:)*$q)?::(($q:)*($q|$Ze))?)$~iD",$ja);}function
is_shortable(array$k){return!preg_match('~'.number_type().'|date|time|year~',$k["type"]);}function
url_host($te){return(strpos($te,":")!==false?"[$te]":$te);}function
server_parts(array$Mh){return
array("scheme"=>(string)$Mh["scheme"],"host"=>(string)$Mh["host"],"port"=>(string)$Mh["port"],"socket"=>(string)$Mh["socket"],"path"=>(string)$Mh["path"],);}function
parse_server($N){if($N=="")return
server_parts(array());if($N[0]==":"&&!is_ipv6($N)){$Li=substr($N,1);if(preg_match('~^\d+$~D',$Li))return
server_parts(array("port"=>$Li));return(preg_match('~^/[-\w.:/]*$~D',$Li)?server_parts(array("socket"=>$Li)):null);}$ej="";if(preg_match('~^([-+.\w]+)://~',$N,$A)){$ej=strtolower($A[1]);$N=substr($N,strlen($A[0]));}if(preg_match('~^\[(.+)](:(\d+))?(/[-\w./]*)?$~D',$N,$A))return(is_ipv6($A[1])?server_parts(array("scheme"=>$ej,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);if(is_ipv6($N))return
server_parts(array("scheme"=>$ej,"host"=>$N));if(preg_match('~^(/[-\w./]*)(:(\d+))?$~D',$N,$A))return
server_parts(array("scheme"=>$ej,"host"=>$A[1],"port"=>$A[3]));return(preg_match('~^([-\w.]*)(:(\d+))?(/[-\w./]*)?$~D',$N,$A)?server_parts(array("scheme"=>$ej,"host"=>$A[1],"port"=>$A[3],"path"=>$A[4])):null);}function
count_rows($R,array$Z,$bf,array$q){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($bf&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$H":"SELECT COUNT(*)".($bf?" FROM (SELECT 1$H GROUP BY ".implode(", ",$q).") x":$H));}function
slow_query($H){$h=adminer()->database();$Ck=adminer()->queryTimeout();$Cj=driver()->slowQuery($H,$Ck);$f=null;if(!$Cj&&support("kill")){$f=connect();if($f&&($h==""||$f->select_db($h))){$nf=number(get_val(connection_id(),0,$f));echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$nf&token=".get_token()."'); }, 1000 * $Ck);");}}ob_flush();flush();$J=@get_key_vals(($Cj?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$xi=rand(1,1e6);return($xi^$_SESSION["token"]).":$xi";}function
verify_token(){list($Kk,$xi)=explode(":",$_POST["token"]);return($xi^$_SESSION["token"])==$Kk&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$pc=""){$ta=array_flip(str_split(compress_alphabet()));$x=strlen($Q);$Al=($x?13*($x-1)/2-$ta[$Q[0]]:0);$Qa="";$Li=0;$Mi=0;for($r=1;$r<$x;$r+=2){$Li=($Li<<13)+$ta[$Q[$r]]*93+$ta[$Q[$r+1]];$Mi+=13;while($Mi>=8&&$Al>=8){$Mi-=8;$Al-=8;$Qa
.=chr($Li>>$Mi);$Li&=(1<<$Mi)-1;}}if($Qa=="")return"";if($pc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$pc)),$Qa,ZLIB_FINISH);return($pc==""&&function_exists('gzinflate')?gzinflate($Qa):inflate($Qa,$pc));}function
inflate($Qa,$pc=""){$Af=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Bf=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$tc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$vc=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$pc;$G=0;do{$_d=inflate_bits($Qa,$G,1);$U=inflate_bits($Qa,$G,2);if(!$U){$G=($G+7)&~7;$x=inflate_bits($Qa,$G,16);$G+=16;$J
.=substr($Qa,$G>>3,$x);$G+=$x<<3;}else{if($U==1){$If=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$wc=array_fill(0,30,5);}else{$Hf=inflate_bits($Qa,$G,5)+257;$uc=inflate_bits($Qa,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$mg=array_fill(0,19,0);$lg=inflate_bits($Qa,$G,4)+4;for($r=0;$r<$lg;$r++)$mg[$D[$r]]=inflate_bits($Qa,$G,3);$ng=inflate_table($mg);$Cf=array();while(count($Cf)<$Hf+$uc){$ck=inflate_symbol($Qa,$G,$ng);if($ck==16)$Cf=array_merge($Cf,array_fill(0,inflate_bits($Qa,$G,2)+3,end($Cf)));elseif($ck==17)$Cf=array_merge($Cf,array_fill(0,inflate_bits($Qa,$G,3)+3,0));elseif($ck==18)$Cf=array_merge($Cf,array_fill(0,inflate_bits($Qa,$G,7)+11,0));else$Cf[]=$ck;}$If=array_slice($Cf,0,$Hf);$wc=array_slice($Cf,$Hf);}$Jf=inflate_table($If);$yc=inflate_table($wc);while(($ck=inflate_symbol($Qa,$G,$Jf))!=256){if($ck<256)$J
.=chr($ck);else{$x=$Af[$ck-257]+inflate_bits($Qa,$G,$Bf[$ck-257]);$xc=inflate_symbol($Qa,$G,$yc);$Ug=strlen($J)-$tc[$xc]-inflate_bits($Qa,$G,$vc[$xc]);for($r=0;$r<$x;$r++)$J
.=$J[$Ug+$r];}}}}while(!$_d);return($pc==""?$J:substr($J,strlen($pc)));}function
inflate_bits($Qa,&$G,$Jb){$J=0;for($r=0;$r<$Jb;$r++){$J+=((ord($Qa[$G>>3])>>($G&7))&1)<<$r;$G++;}return$J;}function
inflate_table(array$Cf){$R=array();$nb=0;for($Ra=1;$Ra<=max($Cf);$Ra++){foreach($Cf
as$ck=>$x){if($x==$Ra){$R[$Ra][$nb]=$ck;$nb++;}}$nb<<=1;}return$R;}function
inflate_symbol($Qa,&$G,array$R){$nb=0;$Ra=0;do{$nb=($nb<<1)+inflate_bits($Qa,$G,1);$Ra++;}while(!isset($R[$Ra][$nb]));return$R[$Ra][$nb];}function
script($Hj,$Nk="\n"){return"<script".nonce().">$Hj</script>$Nk";}function
script_src($ql,$fc=false){return"<script src='".h($ql)."'".nonce().($fc?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Yc,$de,$ya=null){$za=array();foreach(array_slice(func_get_args(),2)as$X)$za[]=json_encode($X,256);return" data-on$Yc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$de(".implode(", ",$za).")")."'";}function
input_hidden($B,$Y=""){return"<input type='hidden' name='".h($B)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($B,$Y,$hb,$pf="",$b="",$mb="",$rf=""){$J="<input type='checkbox' name='$B' value='".h($Y)."'".($hb?" checked":"").($pf==""&&$mb?" class='$mb'":"").($rf?" aria-labelledby='$rf'":"").$b.">";return($pf!=""?"<label".($mb?" class='$mb'":"").">$J".h($pf)."</label>":$J);}function
optionlist($C,$mj=null,$ul=false){$J="";foreach($C
as$kf=>$W){$lh=array($kf=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($kf).'">';$lh=$W;}foreach($lh
as$w=>$X)$J
.='<option'.($ul||is_string($w)?' value="'.h($w).'"':'').($mj!==null&&($ul||is_string($w)?(string)$w:$X)===$mj?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($B,array$C,$Y="",$b="",$rf=""){static$pf=0;$qf="";if(!$rf&&substr($C[""],0,1)=="("){$pf++;$rf="label-$pf";$qf="<option value='' id='$rf'>".h($C[""]);unset($C[""]);}return"<select name='".h($B)."'".($rf?" aria-labelledby='$rf'":"")."$b>".$qf.optionlist($C,$Y)."</select>";}function
html_radios($B,array$C,$Y="",$pj=""){$J="";foreach($C
as$w=>$X)$J
.="<label><input type='radio' name='".h($B)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$pj";return$J;}function
confirm($gg=""){return
on('click','confirmClick',$gg?:lang(8));}function
print_fieldset($s,$_f,$Il=false){echo"<fieldset><legend>","<a href='#fieldset-$s' class='toggle'>$_f</a>","</legend>","<div id='fieldset-$s'".($Il?"":" class='hidden'").">\n";}function
bold($Sa,$mb=""){return($Sa?" class='active $mb'":($mb?" class='$mb'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($E){return
remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($E,$Rb){return" ".($E==$Rb?($E?"<b>".($E+1)."</b>":$E+1):'<a href="'.h(pagination_href($E)).'">'.($E+1)."</a>");}function
hidden_fields(array$qi,array$Ae=array(),$hi=''){$J=false;foreach($qi
as$w=>$X){if(!in_array($w,$Ae)){if(is_array($X))hidden_fields($X,array(),$w);else{$J=true;echo
input_hidden(($hi?$hi."[$w]":$w),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
on_upload_progress(&$ol){$ol=(ini_bool("session.upload_progress.enabled")&&ini_get("session.upload_progress.name")?rand_string():"");return($ol?on('submit','uploadProgress',ME."upload=$ol",SESSION_NAME."=$ol"):"");}function
file_input($b,$Li=""){$Sf="max_file_uploads";$Tf=ini_get($Sf);$Yf="upload_max_filesize";$Zf=ini_bytes($Yf);$ei=ini_bytes("post_max_size");if($ei&&$ei<$Zf){$Yf="post_max_size";$Zf=$ei;}$ag=ini_get($Yf);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$Tf,lang(9,"$Sf = $Tf"),$Zf,lang(9,"$Yf = $ag")).">$Li":lang(10));}function
enum_input($U,$b,array$k,$Y,$Oc=""){preg_match_all("~'((?:[^']|'')*)'~",$k["length"],$Of);$hi=($k["type"]=="enum"?"val-":"");$hb=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($k["null"]&&$hi?"<label><input type='$U'$b value='null'".($hb?" checked":"")."><i>$Oc</i></label>":"");foreach($Of[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$hb=(is_array($Y)?in_array($hi.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$b value='".h($hi.$X)."'".($hb?' checked':'').'>'.h(adminer()->editVal($X,$k)).'</label>';}return$J;}function
input(array$k,$Y,$p,$Ha=false,$ll=false){$B=h(bracket_escape($k["field"]));echo"<td class='function'>";$Tc=driver()->enumLength($k);if($Tc){$k["type"]="enum";$k["length"]=$Tc;}$C=($k["type"]=="enum"||$k["type"]=="set");if(is_array($Y)&&!$p&&!$C)$p="json";$if=($p=="json"||preg_match('~^jsonb?$~',$k["full_type"]));if($if&&$Y!=''&&(JUSH!="pgsql"||$k["type"]!="json")&&(is_array($Y)||!$_POST["save"]))$Y=json_encode(is_array($Y)?$Y:json_decode($Y),128|64|256);$Ki=(JUSH=="mssql"&&$ll&&$k["auto_increment"]);if($Ki&&!$_POST["save"])$p=null;$Qd=(isset($_GET["select"])||$Ki?array("orig"=>lang(11)):array())+adminer()->editFunctions($k);$b=" name='fields[$B]".($C?"[]":"")."'".($Ha?" autofocus":"");echo
driver()->unconvertFunction($k)." ";$R=$_GET["edit"]?:$_GET["select"];if($k["type"]=="enum")echo
h($Qd[""])."<td>".adminer()->editInput($R,$k,$b,$Y);else{$fe=(in_array($p,$Qd)||isset($Qd[$p]));$Ad=0;foreach($Qd
as$w=>$X){if($w===""||!$X)break;$Ad++;}echo(count($Qd)>1?"<select name='function[$B]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($Qd,$p===null||$fe?$p:"")."</select>":h(reset($Qd)))."<td".($Ad&&count($Qd)>1?on('input','skipOriginal',$Ad):"").">";$Pe=adminer()->editInput($R,$k,$b,$Y);if($Pe!="")echo$Pe;elseif(preg_match('~bool~',$k["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($k["type"]=="set")echo
enum_input("checkbox",$b,$k,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($k)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$B'>";elseif($if)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($zk=preg_match('~text|lob|memo~i',$k["type"]))||preg_match("~\n~",$Y)){if($zk&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$bl=driver()->types();$Zk=$bl[$k["type"]];if(preg_match('~date|time|year~',$k["type"])){$Kd=(preg_match('~time~',$k["type"])&&preg_match('~^\d+$~',$k["length"])?$k["length"]+1:0);$bg=($Zk?$Zk+$Kd:0);}elseif(!preg_match('~int|vector~',$k["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$k["length"],$A))$bg=(preg_match("~binary~",$k["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$k["unsigned"]?1:0);else$bg=($Zk?$Zk+($k["unsigned"]?0:1):0);echo"<input".((!$fe||$p==="")&&preg_match('~^'.int_type().'$~',$k["type"])&&!preg_match('~\[]~',$k["full_type"])?" type='number'":"")." value='".h($Y)."'".($bg?" data-maxlength='$bg'":"").(preg_match('~char|binary~',$k["type"])&&$bg>20?" size='".($bg>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$k,$Y),(count($Qd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$k){$t=bracket_escape($k["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?idf_escape($k["field"]):false);if($p=="NULL")return"NULL";if(is_blob($k)&&ini_bool("file_uploads")){$vd=get_file("fields-$t");if(!is_string($vd))return
false;return
driver()->quoteBinary($vd);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($k["type"]=="enum"||driver()->enumLength($k)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($k["auto_increment"]&&$Y=="")return
null;if($k["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($k,$Y,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$oj="<ul>\n";foreach(table_status('',true)as$R=>$S){$B=adminer()->tableName($S);if(isset($S["Engine"])&&$B!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array(),$S)),1));if(!$I||$I->fetch_row()){$mi="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$B</a>";echo"$oj<li>".($I?$mi:"<p class='error'>$mi: ".adminer()->error())."\n";$oj="";}}}echo($oj?"<p class='message'>".lang(12):"</ul>")."\n";}function
on_help($zk,$Aj=0){return
on('mouseover','helpMouseover',$zk,$Aj).on('mouseout','helpMouseout');}function
on_help_value($Fi="",$Ji=""){return
on('mouseover','helpValueMouseover',$Fi,$Ji).on('mouseout','helpMouseout');}function
edit_form($R,array$l,$K,$ll,$j='',$H='',$Bk=''){$ik=adminer()->tableName(table_status1($R,true));page_header(($ll?lang(13):lang(14)),$j,array("select"=>array($R,$ik)),$ik);adminer()->editRowPrint($R,$l,$K,$ll,$H,$Bk);if($K===false){echo"<p class='error'>".lang(15)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$Jc=false;$Ol=($ll&&!isset($_GET["select"])?where_columns($l):array());$Fb=(count($Ol)!=count($l));if(!$Fb)$Ol=array();if(!$l)echo"<p class='error'>".lang(16)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ha=!$_POST;foreach($l
as$B=>$k){echo"<tr".($Ol[$B]?on('change','whereChange'):"")."><th>".adminer()->fieldName($k);$i=idx($_GET["set"],bracket_escape($B));if($i===null){$i=$k["default"];if($k["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$i,$Hi))$i=$Hi[1];if(JUSH=="sql"&&preg_match('~binary~',$k["type"]))$i=bin2hex($i);}$Y=($K!==null?($k["type"]=="set"&&is_array($K[$B])?implode(",",$K[$B]):(is_bool($K[$B])?+$K[$B]:$K[$B])):(!$ll&&$k["auto_increment"]?"":(isset($_GET["select"])?false:$i)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$k);if(($ll&&!isset($k["privileges"]["update"]))||$k["generated"])echo"<td class='function'><td>".select_value($Y,'',$k,null);else{$Jc=true;$p=($_POST["save"]?idx($_POST["function"],bracket_escape($B),""):($ll&&preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$ll&&$Y==$k["default"]&&preg_match('~^[\w.]+\(~',$Y))$p="SQL";if(preg_match("~time~",$k["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p="now";}if($k["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Ha!==false)$Ha=($k["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($k,$Y,$p,$Ha,$ll);if($Ha)$Ha=false;}}if(!fields($R)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($Jc){echo"<input type='submit' value='".lang(17)."'>\n";if(!isset($_GET["select"])&&$Fb){$qc=($Ol&&($j!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($ll?lang(18):lang(19))."' title='Ctrl+Shift+Enter'$qc".($ll?on('click','ajaxForm',lang(20)):"").">\n";}}echo($ll?"<input type='submit' name='delete' value='".lang(21)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
repeat_pattern($Sh,$x){return
str_repeat("$Sh{0,65535}",$x/65535)."$Sh{0,".($x%65535)."}";}function
shorten_utf8($Q,$x=80,$Yj=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$Q,$A);return(isset($A[2])?h($A[1]).$Yj:h(preg_replace('~\n[^\n]*\z~',"\n",$A[1]))."$Yj<i>…</i>");}function
icon($we,$B,$ve,$Ek,$b=""){return"<button ".($B?"type='submit' name='$B'":"draggable='true' tabindex='-1'")." title='".h($Ek)."' class='icon icon-$we".($B?"":" jsonly")."'$b><span>$ve</span></button>";}function
copy_icon(){$Ib=lang(22);return"<a href='' class='jsonly icon-copy' title='$Ib'><span>$Ib</span></a>";}if(isset($_GET["file"])){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('&c(<]iDp;+<8]XG-X#ET@P~g+44jkGE
JJMWB[P=;i#!X$XZ(f>Cx5+d&ydL<""!*rBpff!fkm^bQBjtpU)4hnlgdu9u"]lNs!9kv`}pLnMu[)!?
/nW}!<x5Ey]-bY_G+1cjtywbHdyuU[S<dax0
Hp*-71^<Q=:@:nTh|W:o^Mww"/r*_nT8FCXI``P&A[5^Z%0OT*zx^Qb+nx0qMeS5DapbVg]7?)iJ*"[4}C}*JqDk!0#.uZ{4cX*U8U!(c3>W+"$E"oK-|>@iWX6l|1W=f$g36bV,e8dwLN)WK,R-6f"$IOZ^&_g;N%[.eN;seVrL3)^@fQ(N)+I_+gr(D**O1?;]IF:DMgNjlmV/-JRE},jU`0E2b2_Fpf9

)@uK@TEX-E0q&d=R):2^8UCGmhA|!EnaiTCO9E]<D0H?4_DJf^E8g[o_!1xI!9j?+-`Te!B-`bsPmV=<$,`bFq(52j+hR#Loh4Z}^`&?XpC=4^4pi:lSI}%9<`@NphbTIeYKquP`DgCzfIErRzu1$VT.5awB]A5%[)d{5>a=mnN&rWqBo=.X
Tw&_{V
%Sq9f#Zp&+ZNQ$++;qk~9wv.sKdb"*)BaZ-Rd8a*dAT@)}2F<vSWC)>4(fBxIN6/FUTxn7"S8sH._j
MZyJaF1No9&(oC9Mj$!&r8G6`DEb/r[V9k:,KeQN$Nwm)C1*6S8"llf@pFAAoG/4D@sAUMa$tH0Nxe=WAn8ClYb]V:PG5NQvPEQ9MY9a"sK8d$sbwSU9DlE^VUB#8K_[0Il&{[@9qK~em90k2Er
iHE.IGE5lo=3Fm@1=MM34!r=KC}^AB%"z=;3e,S7Dr[A__{9aL7@?3]wUkew{s
Kr!tOUQ-Od;Xa6m5Lry}k~Z=lWG79i_<MR<{""v1@`P$4PJ;aven@*_GdL;_o"lx_$c<8y9IWTU|Osq9JkImMylT@c$N(Ln|xF2ss%m473U{*{D&9j!_shhxM3N9+>!6ukH~>U
H6~=q:K!~4_*?a.4,ptom8{8q3gi"*zP4(s").ZttK/:m
,t"S~Q*$5DrPlMp-tKNCfZarEOEgXu9y0WwS(y~/"Un1KV"hvF`RGjMPEjEne@V+cmCuTMpXT
<t`2aO<&@?M[UKSJ+*(YwINSt.pPpSJ;Pn5PD=Gc@R=fm!.[e,wG]@IPL]wWa`gc/:~U:Wc$DJ3Gj+
2eUL"[c!@Q8p/,S~mqr"<?
0XMM:b3+dE#]9i$gNv-yec"LJ(+ph]UoG0`k!AB#F!a[~"@r{?A`C#3&71G"4N)hTgk;>?fkeTe)six>@!2!b$R[u0<Md[!hN7}_o1btnf93]BD9{VpB7;n2}HZpvTVo}"
"mq2=-TaS&Q9bUSx
m8v3jj]B%?BHH/CR?FCX_P>PM^]p}*zU98:7d>nF79Q=@92WSCV!/R^0!J2RHE/C<o_N.3^=^6>mK*`hrF`bmW7=kKgPpE@c[x}cOZ[.MuN]nA82wYRQ8aZFL[SRTJA,U_~([MmpHPx&0YNv.m|cO3FQP%+]W#l?:I7(8
Qt@Y,sn_!b6U.0s52(&_K
beRDYJFd8xxs
y7>Hsl2^V9ah^&4i7&"0LF2Jlf[4*eQJeztm
JIetUG(Umx<Dk.qq"liXSR7P4F:]|9:iolL8zc]6"%%C00zP5j&j:Fp,%VTWQDr$"&D-Q&Qpv*oEM1[cNQ_1h%h%fVP0zlH<t,;#H#%e.Fctm:?&09}8ejQ*#ymG%jTVfbjP6;CorQ=bgXl.5%mFrp[6o*F;W8G^}-l`bf]DL*(kY"xL(><J6DZ_Rlgkh]45v^dCUo56(_=9-sm*<t1iF
)4~N~y7m1DJ-;BB*/=;Yg4XxvvRVBmW#.dz#B8/X+<l42=Z2,5[02HjT3Di@]*OIO&j7M^_O)poc3*pnK_@!
ZXx#*C:2R*2aX>hFKR_Sf$CNf3PXwcyB#C.PPia{43H8rWms:/D]=
`P(1b)vuJ]-DX3OymFG/^jL=62O+EV]OZI#L3(RXt^6GGf2SmK"w3"oMIIpDk=
nhqe5h`l}R}m[E~rO(2Y(FP.Wp8rOdY&o>@&ChKb<[1,
P}
y9--6(;1qd/E^LVj`I?CT!xlqLeQzbctp%/D45[9
"b*;u
h?yA5i"gG,jX2uN?QRi]3~8m8R(p(vf8o-qldR!QpK-hY3<)lT_E?$&2M*";9z
j7M[dJ)y!BJxwJ8J~hxwN_2,:qA07U.1,+koMfT-GI%xZ+Q$Jn9x)jLKPYSz"RfZ/`?j&"6$Sb91?&EC|<cJhdb=#B=l`m%]H/^$,IRO~y}lpNeoGTY8>ms9r.i&[,1j=GllhJuQO#+J|^PA.xi+4o1hK7Zg0rGb@qM#[aE8y/4WT;H@o"g-,G:,mE0cR$z9kE_L7MR:C(+SqQ:=HZ|[y8}
:hIGZCd$t&:ipF5IEU0:/?]o.k@3-l&W~<d!SCn;ArSFfgBCZ+7HwO$2O8.L~]<>w_i#jz$N/K.EsFVmy=@*XvZ$|C[m)I*4u1x+IC[B*e8=Mv;6C=m&HjrVB8^8tl/%mG5AJqLEqV_.
raqA(w^!JQr~HA2RrIrZD!
dKb`Z$h%b^V-Tk!MQ
W^>#@-1ECw:Y]wCsC!)z(FVv]N$R2A>wzA]B=@5IvZlb@(S2QTwRYw`]}qM6q56IV"N+4h*rAmJa;amtJwpnA-:,lPYy]!Pq!Mrl1r{_ba0@qW]w=+[vZyf[Cq5oTK;
-q!xZ,of!b-XuG`));GLkyiuX^:uXjf)hOEwwBTBuoDBaB;pFpK5ZnJVU,^B-+X4[z%`bMc)LnsvWZp]<crLlyGNXw2f0s4R,6tJ}R;fGx?M9SS`Z9`z#DG<wQMA~c#nmiLtU(SB1]&F;jScPh/>KCqskFd7%;z6xBb
+yww]o6,iy@NZ-&%]Aro:UgqKy$Ggs"]P@I7;MI`y`)d,dEBuV;6mj~
r0R3t,?Ttu<BBh]#-Eax`c#8<#iH@hq*y]HS;pZGw4kIg+pHO$nq0X!UA(2*BY@Ko
U]|f1]S03<5(Gp58GG(j_yMN=V"2i7zuMax#/4g=*M/H|^z$Q.rL1dxgIvqFHSX:jnkhldH?T!v/aS^d<FlQa@;3nOpCKrTmdt(m3bGdSpQl?iVIaCp
,;B4T*yH<orQLC7svv_Y6<8>(i:s5m2$bL`[@XQBIJS7dojJ@&?86w8J5lx6+b}t6:7IV4TUtyPb-yyvli0t1$=NAp+z%q+bW5gg
w>4qL[w=>mxM3n_%NlV)@x)+m?94$
bsT+@?R1)Kqa;g]6k?#65eL212qW[
6NadoRd+C<D8-.rJga:M6$+8(NOuc2@Ffg.^)Yf{wc+4MAx)QF?<SwBii&08AiS]Y&RF7mR59Y/o;8bo(r7xlSXuFAl6Y+$>"dEOPhM>LRe#oxJg[][iQj&n<X)L%ru^.nci31X2TN7{6Fc$+}?qR(k*Z_p24n):;%s]@/?<SmHQy=%`DM"2v-1Ln|dk9WUd,U>h]b`$d&P/CPM;#iH~TnmpB2Dr@D$6
)3^7YLMcULxiqQBn2
i/mGe0ya:1{L?k}%!wtJl+3D:03naJzsG(zjZ0n7fg4G9`BM:Wyy.mdVLv
Ov`qxB)v:kz)[.4C73^EOH&
3?V<M{s{_hM?lV>_VI38B&$mPs.(ASw?[B.%Es
pl>sgD+n!aDgUnasypNOi8I[yc/Im+wnlPDTM?.RC?h.%Oo.E_<5qnlgG<z5IY<VPLDY;pRatkmP"G8MM/CVR<_/^[rwR]I"-q&R{=.r|`GE:po4vXi=HL7:Krmy9i/"is[er&L.|YD[;NTo3_jT{3Li6;IDNKXYvI)Kn$&CqRbMppf69?J,a]Ch$BhO_%^S1]GVJ"3iqK%?lH;?B/qD-2&tOOuG0Qasf7maik>++jPLp.k+1n&mOi[(ZS}
H5]<I9/!HL[u^$RW2I`1K"SD=I+.+vKi>^W
)0_6&^)hx.LX"V6n;S~Zh62V"7>X!Xn"s8,-G0dy|Mn>Kfh&9gU]wceog<M){aw3rFpa#u&kTh%XTN5LO`R,3t5"YxO,!c[yB`St$]^K..mU[]dvHp1JUwcP>P8BjJM$B%!NP+y%"!%_rhw<J][hJr3c/`T?F4vjd0X[c,-.KI_o1iGA%6^!aHi%I3P*-KikjbcAM#80I^PK0Tq=Jr-HH*x&(RIa"Bpk"^~vbVRrTHA[KhLz$a&_g(Zq)LA4vugp@qPdy7{r1GqBYu@som-
y)S$><*`e=yC}S;vuIY4WGlsgp#[o>x+%HEcM+"27k-oInllT&Ka]C{w5ldP<w]a$FbKcx1Kj`sVdbipVpUX&Hh9a$^*.
e9^6{)<@lw.C3Oh:DR)ZlfwMPY6!{1IT/Ri=G1=8{@5d"bv6|yl$M]^4>"k)3dxj_<w@e`qv_BoLj#.6;Rl:h^"Ku`0pwd2hguL+CjYTOc
a=npT;Q:g!R.C#R"');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string(')OsbOb3V?!K0U*,j#-$TY2N&[`b!>wsTd_N`GuxPN9GOol*1@VDLlh_fdc430fu#lZ-r!f<.+=s=X(J2e>*"$r2geZo4@leYjQ1%,Ya^fK)KWrns9HN3Za[M&Ua[o)7sBH/u8kXg}4drw:$n$88?$
q.DLTGX#<D1t"V<MYp_Ma&R!lNy=^42%5+QTJ"M_zEIVt2b&@<iW5HXxa7"+HENrVp[-(?;l^q7O9Hb]:Sr
,WOw[;eXJ3/AYxWiY8v=afr;mm
2j7~=*!Bp~Z"dLH|e`)gkNjaXDNCg,tOd/Bee9aAhUna-ZLB;OF8<%r2e1x*xX$ZiG_Ot<kzJ%FMb$)(Q`hL2F*U3b$cI[XzX_yVm!=X`6&,RA>7e!9gn|F:S?FGgzw]+AWONX6E]$Hu$5^-Av"t[SRPD-dDP9jn"tZoFsSBWi!U
]MxVmGbSp6ix~D-FZ7DoJXY/zE9!l0/]_ZhqV=[.*yn"zS|U3V:p0%cK5pT+2_?0*<"/w-9$DgzF7#yWi<W,3"4>QoJftal+Tm>(PeM9JHTs;vxkWm9$<A7*iHsBl8Ig]>qQ38jy4P@0/ej$G,X[`Y>gf_|8q*^2Dnu#YI<#>h+;DK|$/DDimVm(m`WCVEYX1jS%84q"FCpAaU/4Yf
Q<ovd>ujL>jlSK$ADUHDsn1a>o@
;@5f]$+ZQNcbu-^=v>xaijt5[sMndunEa-5T28EWI"G!j1uhd)s:ch9c-:STXv8Dq82x=D]meVP[+d`LIY+k0"G?9H47
NBubq<z`![Z&|@7?P6j_[UcU{fnW0X^j_=5(,s<ii_zJS27M>X{xnK3M[W-rsA0k}H{mrK*vZ2&pNC@DA0;NWwLj&)j-eg5PfwA;O70]r,58hd_Eqn{Y@Ws+We9XpZFh)z(-@LIrbPy8da(hAcZV#?1X}E7dx7tw`28WL.XVqgdV!&yvq?3hO5.EHdr-kP>4[llRl9i0C+sj[+"u^v6Y#jXxd');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('#c4]`nsZ32#tW"t=D[}-dt|D
t4.fB*UvVm*X5y`rIcq94l$pS];=:p"0Z-:)cc`G+XY!YDcCJS7Ye"8kvSK!rgB,
QGJBTN|9mkJ=oHNq=
u]0z"ZHVgjFqY]+!jcRjDHI9j.ysjATBA2+)D`tcn";#,vw[ec:F"59cxe:"MJsOExc#f]Jg3B4x(I*HPir:p%TWdy7<JoX
iM~/S"3o!mqy^s(l{673#
UR+s.b5"R*rVPQ)5R>!BUk]Meh`t?]]h)NXY_6dtZ`u<ni1
t#S`]*gu(E`2MmXu"JjrVD{.|Ews"l1_B>"X)U)FYUs-SK|fcE<.1w]=xN?+w:atRAu#>28:4Fx6t0nVm=*PMYYT5c:pQ0`UuR^"GtbTV
Sv4(hKlWQ)A^,D[.qZthMKtOz=(pI`auf/O1Jk%HeF%q2;XksE`/(bq7yT!`{.1@_;~Q7jS[7L2t="<22MYBka8;8t=v.Oovq[@d"hqnY/`L,M>^Mxaw?CcwvZESN346m,i`yy5`os@!KIs
LwX0>aHRquo@LfTs;:(QjF]$(c~F_5+yGP[WAhmMSqi#mY5=2y6[u7$+$G;5a3v.bZOGDv:m:HuWpq^:6yhmi?e&wvYRYq"=:9cRVLDk%LObrwOwi,`nj6crk7rUJH48n1gc0]3B2-l5|qN2!FVaqNLA$Sb-F)5B}:)hPrSo,E#]l:nJ:h]$0u9.Jtj]KU,.Z3lPi6iJ47DGK#ZU]O#`SW[OgIS9pM:
3*oro2*HBY`+ddG/M_juYpRw-Q6@chBx[$q:)#rJw!OCE[@#xk(.LZRqw`>nJ-"#8opCF`pa
2r2`a7cu9)ug0t%KI62Q06yM("TW<3sgmUjOL!=f-)x7T5A6.t"]LwxS@-Zvy6C]G]S!a5
iXSu,;tb]-CQ<y<l$(+Bvx%FpjdKq537f+!1^v4>6BaZI>zWN"RCYCy$
?(>9Kt@N(S8I?qTI4F2.2(Dou)eZ"YmpCZ(=[vNk3z:!tWTqB-UZ-x7z<7np_N:EUG=#,`DoAKA3RdcOfDmS!?m$
*hz5492s_]Y=:`{r418#G4@=u5Q?-
70qTmA.U~PmF"N0=2P3w_nu+(C]6el:LD+q,<@=U2_q/3&0-
;V8vYB]HUVKkuFNe1,c4MFm&AYT9uH8E+ia(6E:mlzZ9>n$t.Mcb<~qnp7$`SuT{_yrN5NmfpLyb(K&+o3SVsu#1Rh7O?h`0Yfoeo0s
f|#]X6RVTDd?ndC&kAsE*@waxjA(*.)=Vi=7ofQ-TtY3Jpw/tSOG>C67LyCrh}Ps/whiIsu&De(lN}IG5HK"OsQ,&e-HG~%_?q(.g8?#N70!A?cYW
o
XDC`S"-g9PBCP=8+
d>K)b4KSJ;4f_JHM^k%Q|o^yA1%#?GG>^x#lnL{c$s}jpy$sh_7sL6}Y&&ML+^NlAd+4,mW/G^NS}9Gp5EaHRU#8-V8KB0F(C-UyCZ?n]Q3Bia"FJJ)z(%FR)XSiI$U&<f.0JXa&oE9>dPR+dB0M@yiIV"
cLcFA0&*NCIl3K_3iPNuE>.Q<B#3H3eqx.%?
GGUL:<nOsDU1oH1XI+]riTK`G<:0/Z[,XiM3BOL7;^$W)`X$;)8jXcN27Q$v[3Cb}GaN>-<1W]o=%HX0k=@3*N+CSyw3?D/^>#AG>daACASsOj"?%wM!rLf!|k47}8$][5oTW2B16"e*
j![q(u?3gj._<w6=6&@vSNWGNd"sF`M(J2]Q#~0C"{S[k&s.%8D~8{J)Q_H697M6y.I_Z/2c^GrF%6*RH?2!XO]N0~8&C2Y(^-YTXOS=!>Pk2:<GrA.+MfW6#R<n5iF~,l&)3tRMxJsq4YRQFJ;Txe!6M?(XlCs.K`X*G8HWf|LC]3$CH|W(,]FdD4ZGS7vxvl2w]rx.2;-]A9Y}+,?<#<;
)?PiuI2"w4K@KmuC%m96tK!?pfRhNO5bH~<%rAyE*i<6L/<7Zt3tvphyP-gTn#`12@r]s$d5T/
6cjl0%JXj.10y,p8Zl3eR/aThJd^L6FM>1*:_K>X.rP8X7;bJ@<A*Fo%qFCr~GsOq@+9Z_zi)O[R-NB_~]mDX"3@PgQ%r(N9c
lXkDYS_p`!CE#!Fp4=|m=f>q#3(5F9Hu[$h5x11Qq?pI-0!5iLQcqExSu6/DQ7]8MD"Z<_5XQ[HderuwS)."B1"xw1anHU}y3!(DS7%Dn$iRYqO-$jZ"B"a84!j
t/
3;FH*<^iKvAEkS4F+er>g}?Gr~X0j*EN.?hs/J2UaAw[[cq4c)K`AG8mDy0ctk68<|c5.o*@LmlnblMo^_Oxof]FqC71!0LJ8(_H"H>^cYa:=W+{lHYg2L:pC:CbkVv="hAd88X1qC(qFJKI"C!jAP8T4>[<J(o`wnrLpo3NdC`-hlt<!S;+?dG<nzv|Nv9DF66Q`rR+P|4k.Vjs@8/3rc%
UG%]&".PTx9+KKX*/ZjT4L/)Y(0DThSUl_+]b_!%8}b/5/T-tGGO,"Vl24P
2"H(xb_H+=$!N3cN:2S%KFf7:3C)UR)f[uZ#[NDFE}Bzm*/=VjQ@nIO#3?Mjq7eou{p{H&2sEx)?3jNmtxT}#P6]q]@9DU>6gPc4Mg$t@E$^&/L2Zvr@"[d|4}vYe)A3XGlT.MC^1p2]8+ah2XgMXdo8IF3g*uj#kLL?Auy`jE[e5qw.T*xkFxqHB"B~;.q&6)o/b3?D3`RZe7manPmMF#iwilcG68p#Yoq>1!f%dLv2ERML3R.35>!X+65d;kNfZU,p$)<xY,D<.el#.7,dWqC!!dSK2^
|_5nP.T#)T:ZUxHpLU#eIXwV~=E!&o$iK$-/]++4o>.D+X9
?JV4#D&-loig*#Ao.*(>&nyeC2ZT14+`Gt@qD>{6_@}s,U^^G1MBGqOf->H9zUz,lg!d;*U]U`E(qRh0op#G:L`ZQ(&G^SXv=Y<]#CL9NI}#z"Z5AbdNcWw++Ktld"6kBL%U`hIh9vNvN!v+1r4S$IH`aSG+,0!MmN4T{tD6w&IMk:OZjR3F&VZ=}Z?Vk)MU+0c3GTEM-N5nhFfuV/;L6k>2@N#H},)F/aKk1Ll=%mv.u<Sj+PQ!KGZbJ!}cpfNuc<=kb<:T"e6d?S-s=g?9iu/cMWMq1LG;#Ul@<Mof]")YvT0Gb2,,s47OEo#73<s1T?pGAvrS{Wgy.Hz*(!bW]W@]>5"pdn^@bMY-:el$`SziYO#w[6!N<"6Ik%~O@<%P>w2?mBkWW6Mc}@7%G[_mX&bB|)cpOF3M[7`f@1j8k:.hJ$&iX9`c#asN`es^{9xu`K5JblD<X&pWu5<Q104/u9M=Y6uksmAh&5gJvgViq8<8w5dCJelSwiZ^0m9F/dXx,>K=h0Gs$Jf]2PY]7qxyR%h$`4DtwBD4C2WO;GhRyFA6.RAHq2^KxS$.FXKvH@IBV=5Pv1_*Fd/s+6Ms+T"P6pKE(.nQ>SU!&NB`"A;U6CJ-%gi4;Dl#!R,JaGJaEj(5+_u$9!BdKlaAA3@E]*^VYv}V0X=)="/G|Cl,NH)lKfVy=56yIeu+,irBa;u=XA&J&Ex2$mR%$sX_-VG2ke^-gOze46&g"t}6V$L9cFVx4Q?2RO
Bz5_Q>+65nwf5B%[o;>P,->h2eA/`wk6Gw9ZmIhi[uQw^leE;q$qA7Qfqu#~P0`9LEDu>AB;^0JJNzb"*e`W=[1y->UM.uAlIR;Q$46hCPg5Z*p?4f<+pFAyg5IsR`Nmk<KA?j3dLjL%JFZcaYXQMt/E3!QdL0La"7d,.$q(H&xuD!`4#XK"Rj4|-xx^nBezY0MJYs$pGuhfO_2ZZg;KAdWpk@XF8yb!9FKab[@DeX:1fh(p(CuNIfS`NnK(%1COud!^#[J}N"91Uo[e9?g@/iJn,-77.[!LQ|
B*nO}N_7HfFN-.
d|njNNP+`
]cqqeUauHk]/LN$De
kK;g_&W!iG6eu^F2h;YnZ/Xc*r)l7>_MSmLRp%ecl9"jYYiiq,ALRVm;S=!&^0cxXEV6$oPHAoDvuGAX2kbQrMAe5-LfJ_<sG$/Qs#U
"L*5P?]zX:m}/1$NtIi5kvWE2:FkHj>b-P,M3J1)`59}jvI8hHW69?@2kGPbU-r2J(fB`N5Ukk2tVI4W4ict"H4?4@6+@^tWV)XScBsu@;w!o73,J.q=m,eUwlAx2X*z,W@6y.=U)kkBb1YO(9a!Cs>*X*7fk[hW7Mk-"R-?::>M;.9b(;o
Y+]VQ|SY$ofnrB;%Kv<6fP.julJDeg1]<s..@~Fm*0&qpU6k+x6:NBE"]`)0kpbLyWdNL8"^S7^}_!w_)/+AR2TPC~C!G=Ea*O<XJe03@N0Ye]s8pO8S%zG90J)PhB>Wi*mw^yK+9^r6#y0X0k@4+EuoSXr4$($kdKOX-qb8RFv/m49gGTUGi#[FCG1q-{XVN^W:6BQAhI]Uy<<lxsqfj.]baSTFw>_7>*1W*=DJl!qu(__WOGN~b-
-_w+@(q2#Q;#);yJAe/Qo!"bd505"(RG;-XVBLM8sd%SV#5>$=EGV4gds/#bD_yq-
XDgOR[Z4k9`pZj^5:(a1kF}KnqxFFl_dA^Y@
L@40<"tulCj7YW65VWwODf%Z<Ku:kCF0;cE43LT{^MUi/[Ma<da/Y=#ybF<bGw.blal7^FNL^2#fZ/
l`:#5JuGEk>L2B^T
>9KnPh-Ojl1@vq<K-W"fUJA$;^IHH12Mn~`vdQjra6ZM(%s{ew2(
xEor.G^/fK3aC%G?>57
J4f5/9K>/4^e{dYD`<#.$c`yf/kgRDu/ac^9$ICj_GcEZ$.IZ1T?BioePDggn905A.|X5sW.Zh&t/MILlBW5KCj?
$7]2Ee`/*=$3).`_6vK|DyKeV<@+OZgp/+$$?PJSTvLPRipBYG0e0
ISMVEx5wfkjX++5w`^
P2,%wufX]sI]1_!w=vp"U9*r.RkVKgk2optJ6`7v[q4y%*5<e2ml&b[`We)PTg<Fnus28[-&^E!xQJeGh@h=s__]K_|Z!ddcnICLCB/D7lpm:Scy94C<jQo50$aW&n<-jFyZ?]_8hN:8}1ScW^*Assus>j0Z5]N;zOu<dnomhdz*xAq]Lg(cu/S^PlA?{XlkT$^](rY]
F
j#YSoQ0b/$Q{p<$~&g
Bc8!>TF(w&9x`w}?3
}nT(?,K28v0,4f,%rriDZsR!S??qx-m6mNS6e,OM)o9%RZi0e[na)-7T
tNVmBy^L,<Y"a+XB=bY6Hp-F@xbl#,F6K|ZX(@&W!lJQw^tcUD8H3C<HU~m^jQl:y}SeM[#RGmYqppCi,{q(NTG3EBiL&-`]k(&5Ky.MmBvrbf`9.T(tXoNM%nE=R`NeU3mwC!Fg?;_?YK)`AKoId9e~d.SMS;DrX
5oe!jrDQ<CvX:TBQ-9<oU&`H$"C~V+bWD4q5/(oS@D#I]eV9_O)$jd%(EO%e><B)I{_D;mo4K#p-glp2>,GQ-GNc_G@|L7o-]5M9Y>R8noaG%+Wk=O-$3Lw*[A5_Uw"}puTldME`Eti7!]w`)t:ck`8AE9c]b@=0SC_b#6kXVz=h
{%x%_<#($7{@kX<G&Q}9)DY3jaX5i
=y3BGohic&C+%Pw=+R2"^(o!Q4BNgiHg
"hm>j|D6e_w
e_ief,"rpC-o`rs>LYExooYO`jer?^@&etLheIm.rt7b(yo=7%`!.kx{^!8TDWUG]`+RI;!3!n(LLI
#W?YwXkYXI~ym6F3{8qCp`%^}]]^b#1D^R73+hj5~jAV-Qp98x}>zkZlJ1wbKnNc(`@4*hZj"PeU@J<&XG`1zc3HTg-s8(v5f;sIKEBt86N%?*fdYji$K!X#rhALBNNq!<(N@5u>5DS9^q[JrMQl(2gkEt8S#-`6d;ewj_f:1P8e)76y`%S%2X*xO1d[3@:d9<zstg/o>lKL(E;N;&5*D@2KkuwCz->-jVo<|0be#7lfG#e=9GVDl4Je^c-]]$,L"A1S%MVXd5b.8_R.%W%93m1"Wm!r&bSJK-UZmUTY)VjUz87I6s{ENVD!i&(,lU[Cy7W!PY6o`ao,v$[Lg914?Z=oJG$91s0_=uFeGx{Aq6^oAUS#M).i1kKj+(e$pKCxxE0d"K/iS?u;w$HB5$)Ox-zF-BZ+Sf|W6_idJ[C(a(F3}ne?Q"nH}KB`[11h#DZH`+nr7A38x:M/{WT9I,Em]9Zl*l^iWez$zw2n/5SHcm-OSl7jiUA,W*aD"GOm[^ihN%+0fB
NjN]@r4KJB@c5~4q%{20EPq;QqQ!J-QuVT6PZ|]3_/4nk?i/Hi^<5k;t(soQnG:
l-:zbQA=I*&w9UD3u(De3kq2PN!Xe-ZnC>FO`DLYTLbwSoFgO72U1pR_Qu2b:RYRLVNSZvn4w{jxOkMvB9]7>2EVhZCk`pdeC5j8Swg.R!+ae^_JKc0N5X_-e%qpt/qbYJ72:SRlnZLE:*gT1|]Qs],.;E/a?Z3z$y8hR+7gg_mG0?9|gNfv0PF8N.d4p})r[~U65[vnmTyi339<^_BQ
8-5K)nrrhc="E0+`$1UjjRaZsmaLSi8_#R&As3~Q#/v
~U&S]jJ*t(h){G:-@*OkMGu2zssk1FuH!Dd8RG+C#y0EZoT74imHp(ILbaZ^4K_Fg8J7T,vQ{P(?o3T<zFbr
aBJV>F>:4A
xxrPKGl>>&pc-p-T25*OZ2jWQ6rEHt>laK5DzPXpPp|F|2(e*YMZxC_8G</7j(SFCc@nqycAog{1w1XF+=+cH@P&L`q,>Zg71M+O9FyqKry<XWT=k,0LV8c`@UX.Q*C:<M8xd3%j:8FARsiow9oWig"I%KrH,wjnl^m20X1=I9mU=0nrVxmx1$6*[xq<Sh*AL2qR6H}cwp7k<k
O>yv?=x=)eaBT4R#gnfXmG%9[f"+`3KhPm$O@(<%`X%EH,fGjQIw.ZRU,sG^y+E{H-AG]Z3XIc.&CX5qe}?i6/?xU`4,ms[msE;+hIJlYyaM*]vt9|8@Bq@Wm|L7AjBS9u4m!i`Qfxx^b7xpC}=z%_vVq/Y!fRf!]x3|Fbv
L%4H
)"}SLFmT#d}ZOM{K<DuX~e1FwxzSw@cXH5:5Y+_[o+$G-#pVr
*<;NtQK,Soz+>1#O_3_+mWeiW#:Fdg",{ywL,TF.ru4ZLS^xEpY)NCA]2PX^+rV%~rYo@^P5Y!82}=X:><[3hR:(1=n`K$]ANd.=cHDiY,Ac*3w]uAR8a.>cO,}Kgkjh[-xY)p/o>^FvHXgpz6jn.0&(2xO+meX<=lrc2S11?ge.h!SaeWgFLQ_pJ7+tg3vB(/kGD
89!C08A
<vM*gP6dEerP=u}KF>n,gf}4!>]nDVMw(9xORt!Y{
!-."[8#5sRlnO`tHF5xU&EpYir,n|sK>MNspdi5>reb+Vfk>nYV5<:5j
14"$ZClj8KA9Z>U]8YQ{Q:
#dfb]cl.wx!r+@1#9GU1Oa]+QAt@$N1#~dC4b!]/irlf3RY?p=T-kL<>b,D8i`VZX?FD5g}kNl%Cf3MLx.DVpR:$7RQoxC9tM+,=QJ%blj)!a"Nr<H1wT#XK_;8gmDN0C>>ERnz+:<GI&(TXZYG#gs{y.
-8N0[+BDvsj:LJT[58*=,u3:!p5a~l-+RA>`3q_1JxW
p#%ktN?`NrE>v({&>8^9D1<LAXu6l@!d]3x6|:sLHl#DfC@KDX8ryO&DR
4p{%n1c3sG2#@B3Xg;u/4W1B.9="=1:%w#dan.7fBs~x&RGB.,sT9R(=3V<V2A=Mp3@sH^)Z"CA4U<ZHmJA@b9BC*RM)C!A-E#eLUU`u?0xZDsj^77L
I61L3@p(g#Ek;)gs#:qlVU!Mo-t[&8/_lbGC{bUhVWG1#IU-n7tjolM[#apFlSZ]tjtJIAViBnwvH)DxU%}&WgUioPAB1Z^wy`ObZX9u1E^uc9rv"9}4diCW;4ncbK4#{i|kZ""JibYE9Yb]:29B=4y^e:z+~Ge7vS8uM!!CB@5^%&iF?hGQKK6#4>}tAY@QkR!Z"uV6PbkS3yluX.(&rVfxVS(H_Y,_cUMe/FqKB/.u#uX
)i~D2[dJpRK_vgU^_s)]qGq6V9T8=D<g(5Mdar1<hVS0qKg-e;5]CjccS&2=/FdJLLE+R3`

NvCOFjU*EFh`[NYk%5wcT`i/
7bki(A,nmSQtQt1cTMan9Bmsa3lO4<iYE
9Qb,#d69+OJWNM-4PD2,@xgaz/j)I)RDj:wN2w]c^Ol-%_mhT(g78neS*Cmuv!O71#-"@5=hiZ.ct#81j`Fwmd@3V@)gpV}!#n_x2l2gldYYvjZ3C01dC$|aVTf%0Jhfn>uwE405xYa".p%r"V]+_Y!-z.S6b8Dv|/z4=1$iNsd-k+}"fP!Gth);87@;?#(11HcCTV|qv]aJB(M-Br5eU$Mk^KIOFcpp0AqGCPng0#0e2<kGlM(p`1Dr#wHdl(=.Fm;C<$i,aP5T%XC`j3@X"eK5R::HT]>CxnpOF;}9xtq^?%)^_C$D~H[P~DDt@BM<KA*KOKnjh>c_~>%O(p8*o+ZwzrU@.r#YOD]:}O?*2?k$,(11%NC$"Dp
[<)XGe%2m[=K0f55SizBx&l_C8ABb69D+6Gi,ka%jM!:O
d%L"13_j,_1+u<;
15nN|>E)ETIt?OBZK=/mV$<2FCd
-
%;xn2x
"EtA"te{t(1NXD#C$
XOJ@bHBqTIJ|#ph$1ZN,x<Z{iWInTW1pN1adpz[r"PSc49$v9`+2w-nSMTUZ;d5rG*C}8[EAgGKDPBLLJ;*YNp)fjP/!<FZ/3Zu[r6,HraEB&d1CV+(KCY]@RHdLtHt*5FO@[=04HbdYD&L
p`n++Exk%%$<TeMoQ~s;e2
}F>0{3(8UatX3k>BFl,_8^
fQW~K^i#"PVHv!0sgKE<ZM@zh}Oj^k31B?"0)5JlCw5vXxe6P!hiT<gbBWqy%(#%7^IW)o!9Mhbl8.F>)(//N-?3llWK#QNiF+8XUR%_wOt5.GN;&58(6:0g-%u/b8CjIyg{S;b8e%m%@Vj}YPbBMktkH#`EAyX|#-^&^JIn`Dd_>Va=/(K)dnEp*_Tx)Fxo+o^AWu[]
&WS3t&kUxz!M{_vPLY3P-1)k0cE.^stYGxmH0g?j+p^v~2HcU!OH{dhc!8A`q-?;I^|V;&Ipi)^,Ll$X{<kZcDRp*S@Z<K"xn2Q;>iz!z(Mv-!#jk3_f4)|5U0
IE4^UfQ,Dqp,W3#[[nnj#+1K!g(lv#IR_Z7`#ciAQLsY6+<YXT.+cT@]69tzUpKN;_?.5bQy1wM>LSK5PFXrXxkL3q-gsn+]$9=7JmI^BR;6+hlKaojKWJM~64-?RT4mu
ut^<4zdo3rq;1,HMZe!RmXGZCZGux=Fk9O@&!X3|.I^h4y`X=):zm}_Dbx+nC{x`j?@gWI9yMJsf!iwy&]yJB~/Bfj>C37%.o7R*S__S5*=EHvWO=O>H:no_3-Y93!Ydy~+@2&J)#kY>Op>[DP3<A;+KGQA0R5E0ZhIR[B*|#a8,ubdl]uX{?Ph<W.?+W*,J=UZ1f;e{]SRxn{t_iI@ttkub%Dmq%j,6*g
t(h?K=TC|n=/~[E2#L?0r<2!L3jYK8F)y2mB0vm6)i5wq,|I[exLC
1#pJ>6i]VF!wWDeT&OIC{%F=nUA&EsS6(3N*2YeG>=8^?Qh$~DWdI<{j5TaB6F~;`iQ
DI6-BkU3]x:`Qy{--!|e;xo/9$t:Id$Hz7=7B&32dqEB%J+@+"Fue^}t}cykCUHXUVQs,Cjv)FeSOf8[t?r,+T_hlTj=>[^.?q?%;gHv?+G#P&.%19-9>`DPs1^!k%E-
4Q9"d:P5PgqgkH@0">iu);)j#0>1-?q"<{@}T8GO@_sV<kl`wKE{p7fw[
CE$oi^2aUBf)bQXZHFhb(Z8bf"WeSIQ%nhj~6$)_MYO5[P_Us`/cenM`&NKcrX;zj3UbBQau&(L%5c17PZf^>C+1,An9`P(kEnS60=mQE{:(Paq<Q=4N.omzu`;Qs{g
e;`s&UXRgiQ"V)Y-I9*D[e*VF!=fIM/ZRQ3!m/lA+*0u>a<yi-
iM
N>s_=WXXbQwVLdH<7-YJ
DGF*OJm/l_?nbf0B"53OVOX%tq^:l._nQ]F
lmf@2J)%<hqb#<pJxGqy^=MbMAeN-EW
kajTDEHh[]
`"_c_,MpD`n/D0?(.%5gE5=fZoS
/=Uuybaq3hB5AV=`1LwS:Sl^f#9;,B(A2Z"#G>/QhD[x(?]~+%3Mxae;Ey]^(}4`BekDC6H3sE6$P~a_QW?y
*,<aT[q?]%jpZqtIeY{BP#rh^++12b>H8f[:9o$Ex_|I`Pa`g[rD3H03$<xuI+IG"CP_>5[b%s19k.o9rfG(@/:$Y/6"<r,j&FrN(L^Cq-GPn<30/N(x:%}dej<6[lBy^Xy0HHRB}u)/@MRhtp3B}UPpMqK#Fl?jlDkdHJZd<!k%|Rw7L`soK)Ta6Z@A~dkqL
qQACbZ*g4T{Lxb):8x77OVbGa0Scpj33~==Z-1*7y9sDd_^2pUZ:tM7hhdh5n:CL}fJQr&9/Tj(%EZ,M58dUhNO-u2X]b[TRE2hu5:h&=nHZ:LoVUxRh3]FJKW},zNV.PT?Jx+nh{F>KSh]s*;q"*b!JH97/OoR-M51&U3pRiV<(,/a
,/w<*G%?sv2;P$%`+IIVs9IB9B$j|O;[~hi8L%gB."
78HBVplHr1fUm/?")_u`IMn#ER68s+/_plm~(v3#E)`mPKtX;+V*u*k~;v
ie#G>=RL5-UKBYS]1^ry(C3MBAel@H"9g>.+$i>xWo$n<L^7t`yqj`kH7<H-5W.(W$EKDIm6}!m3]*Q=LZ_L<>GNLS5c%,njvMV0TI
fanRNHtE^;^02x;Oyw^TA>?&CRDj$|_%ST#t<uCeIM=f+1_g]RxBDxNA("7.ABOEsX@y.$l036_$hpvLp&_%yxxUp6rqgvt&wM`IQU`9lrxDcgPLnWokitnbGDEpH,R)BF</S$C^H`2Q53wCu"c*35mlq1upHBP{w-GK!6jXC$^>4rbT!N%.EX`dvE9KDTQ$$9T9YL-.8rS/t|oc?Y`J)%gP#bW)p
k
0td6u7b].3ta]7>r
G>|scQ@$-FB^>8sLb;3LEZBq/UD4m
F>f0i8MeR:vG?:rlH-tQ.J%.?RycIF=%RJZ)2TfX5?3Ap_*g~
ocushI|
7bz,!^Sqcl}w_Hz(q+9"!`NlA6Q`UBKqi&NANr>Fn!=EzfU,w>9%WGqEhadwh/>1Vbk^jW0A&>{nP+klPM]V[Y7%,b?<5vxByo1h0#a4QI
vM%x11_CD4_$#vi<h|-j@OZ]r*
)N&0y/Hbu2:3=L~tvnE4)xzp*
[TTm|>IH0rR`mYN%QTnrwh[3C
Gb*vUn!l2FKK4:rXS6]/~HE=>@Y8!"G:^r:bJl9LO7k$AH5ZsI,D`vmX)dqne>Nf2HG;1Y@/x^I6BxGdf7U@1=@KNl$2H)hi@JDvKxbJa.03[Wgi4FAwYe=&
=DKX@!jpVyyT;5T3an:/lq-G20?eVS$zxpAK;5eF),@#McMz?H0`+x^ly)"Rdq!;j7xDwI>UE5$r=0(Qs`q5YZN}q]"1
z!m/<Fxf8_0c`&n*X%3DX@NG8p,ub`f/>_XiNw4oy]
ngMLOnHD_E.nt74zFt#KBf$3-}FwEY(X=rLg[50gK_
4r!&J
g$Bz%D;,A5hDYTdI7aZV2M)t8v.v-iQMA+$bJ[MJ)2&VeF!HK.G({-4gZk7o*B/v;[kK#gE#&g.+w0GLs^eP+rq)%.}v</L6%a]AcTg/k"xfRo0)GHw)18O]T#sk0/v`H;3pJI`j55Cg.F0qHwB!hL1T(ZdXmX;fk5oexWD"1,(3W2U+H"j=i6Euw>i&IG0*OyeAy*6B)&fJ],
(gx
E5z"(WS-jJ0Jb:iiJR3uv;O]y)(`HM!x>Xa;n<+.MScs,6W?z(7^Ii7:A6yf!?umwUc6opAxPJ-$uCqwD0[&3=*gk`rNRM
sNZfYFI)QA>vB4%5<l5-zwiy4Y3J~87x#3y-sld=os03+Lnl=(7wMM2H":K.FX6)S?(U`khlSt+R03i:CWnQ^weM_GMg+41iNEkB=nUW6;5h83]__UM50d2oGC%"=1jl;L]?)/@B
pW+x.rpE1i3&P1[!*a8%1|*b(bt?C!c{eokbJ8;r`~<LUbx0UcaO-;iHSNLUITGM6t(mWlSQb/KK;Y-/d0F@18FN)7)<mF$zc3@$1#`$T7PDeCu#W,:1hCr8GE%yw)e%4shqf=BDyk%>>p::C$(B)qukvmYIF0vwh&NbQ]O?Kx
Ewu4]_%FX[`w$4hsUY%fLB)R3F51fw%T`5Mqo2vg/:iA
#Gg3Cgu{k.P
*mHcM}J)>)$
=;>CPIU5VcZD%|ASb]qj8@_E@}9O!r!2o&-j:V`ROIFm68n7iWo1duPL?CsX_@(Zc~]5B#H?qF
p;u7^bxtxS|ji*tOEig=4^ju"jxC;gr#4bv7"TuQC
3vHCUD4kX>QZOF:@ab:Y3-[C#Ft<jPUbQn+!!W:r"X6Z(xS_YN3%[qir$&4qr*/#nxl^P`bkNj(evcX>drtNd+`l7r{u+*rTS`OkU${%4Qe1{)qn^SXi;$I:#)MCE:>88w}kCKQVdUmKfvQ01b>mJF)acu=WIqC4RNDY="]00m">oL}a_cX7:Z=$5iYW6An
Ct!y)R]ma&&x$]s!:l[_9V#3Jd2HDF}0#&L@d
O?,lla:TcUMCh$z__.Dg,j92wO{=9Fcy5TsfYq.m27@gwu5<7W@uH6Vsm5=RBm}R/Q;#P3(!$!d-D"ZL^C),FfdWs/LZoL1sS=su=T($L!uZ^
KU3,&YSB[qf>WVqg:AbyW3Nj$pNjlgw8EVl&
$SxGO~j:v[MM`0ih@~EYV.Qk"(r<dBZcs+:wyxO&*uLF4eycz!ADD6Y")f%J,#OYE+2VMWd-4qgnhPAIqNu_+vcM0q-A=>^Zt2BSnT,w=hTI`bfy-+tuT:A_1!e~NV+bX
(#]<u$Olv}O1AZ!/kq9CgW/6)TiTIC@=euja2MNx#hAA/Js25hSTo-$i;Y<j3g/UPWbnUeTA$QGtg9B>*N/Wr1v,WBJs@P#Z;qF1wSgYW:4&..jgkovYg*JS_e5`-@v#,O%nOOioyH(15-Hh3nn$^nR/2C*]EWDr6kyb&-(5L!*@."1xt+faV]K}X/34J3lY_POw-P<qwSEy[l=@sFsu!Hi0/[`UxBS%vao>pLBLJfJ*DdG$]5Y2Dw?z&k3ut90:Tt3
DaTY!KZ%:;UtZ/m]Ilt-3dv:vV]]vbD;*nsmCT4L(fi}v:E8)J59DOemv4,_C]
YMgO)+?=w(
bIA60,0r8F%<NflRGWwMO:RTWYgupP%eMI"x]irZlWWrPsVQ3l2R,>yM@mp]
3gCs91L#S*cJU,Hk/
yOb7!]=Z>=3U!VV+)1AjS7S98/"<*("$kiF"@c09%NK-).S4D%Dbp+)E@w-
`FiliY+y`+5L~^]ecO7d+uS/OgOlL1B@f`{jMjSa6xl`4^X/$Z?vojO;qZt<|.b&%]Hf{"(w_2H#m,&a="VV5A?.Z_E&.UoS
swr5l",NBfp?V3Fspapl3j.;T`*K$emgdA,c&"vf!C:62{+`ELTt@kQja?*NfuG9t~@J<YIDu#YrA9"so
Bp-*yft_0pMdt[wD*blw(zuxC6`Lf[AVR:w)CP/Q/Sj2GIdyw?*c6r@8.~xlN8N=c`&UwyL-d}=cy9hA37G3kAI7*o;1osn:Y9/MN#[>;{-"$6/J
Mm#6Y"+=eLK`)1wV+0BW)%MEbj[m]-WgHm"VLVN7lFmwX"+O?o5wz[DL;j;n%spE*rC%r5<..M#JCb96MjZidd{BfpgHo!5MTp]qch`:db183w8d]4F%<4}y.rJi#95qLK{b=$:YWx>os=o%ZoG');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('#hc]XHAs"H%tWN|U.+XC:&wAMKy$DBB;E%AtYhLA+imQ4AMgg./JRSnW/1.cRLJ<B*mAZP(Q1gkL[tG`6w;k3MBK|_>4u^FE>dAo5bgI3y9h[R&)L6qN=.WHKi:x"L}^7-|IEDZ7|UVqhf}vj[kKs4Cuxb}m,v[X,kXWY!u4Sa00OJ5OgXpvL9V[^HP:x3rw,d7wdea_HWMj*553va_7jr#]{_(E^919EMRc_uya:uvLv+E^FMJlCnKrq7kmAcJI/BnoIlKh~mOBoajr-
Q1I&^*zZW4^qkjxXSK:Wcyge2y.KthpN(tkyV6sJl!qf_4;"=q19vi
u8wjw@DW;,[vVe<RRr@Pjt==!5FF5~FB9q.7Purde5VF7Rs"5UFmpU7DDNj8%):,Kscm/_GdR-JnjF"oMil
eWLbHw"@S/`_*jPcoBZp
IyQGEG2e*ec:[3fIj8?S/]F"7xY[($zEj/GoM=((Xar-4e*q_c:*:ni>-3+*}H5iF90(BO.@jWAfqLLP(J5=uJ9<i96kzaCd`fYo
[*Iu(P
YpGIeLi7HMHX$lS"=y[E~[cy3L^Mi8+bLJ]F(VNr?1jJq[I&BH4MpiqPd37ivB>vRb^+)n4:9uPt4XRZ84."CWfcH=V<@2,J}e`hs^8)gtJ.*z(t-QYln[:L+CVf7W-vL=X48CqdoyM+~aM(rMfsfadG"XJr{]ZN-ICuf-(4#d5DR+lBWkN&tbtraUlhlkUvF4Ny^<46cFuYT
s#+%uRd:<I9@Mw+-=7XQyS[h;<~7X[hp#A>n{f2WqauN?Ed:0dj2o+qp2M7df)8<qe#V$56el"d5%^C@e
TS,5X%%^@sc/f&*L^%;tGV$U&IkX0uNTq[`g@(d)3Y1
zFuqZt2b-+SQ%["A>L{
z)|hYQNB^*0`UM8BQqt0Wn:jvn
n1b8WLUysWuj7.eSv=u*M/1*JZe_c"/kI8;B;<uT$!!9>X;Dh(s+vu)umYGIvK^)y~rM#oC
J1SM>XrKCY!iZ6C+PR0G:}EOse9$.[4[l:&aC
-
xrWUxR^FZ(Y;
ptBvlYi+FxC%vJG6%0e3&G^T(!%,OWpTdB-m._ekpYNRhwU0rg(rxRm[n?9aW0]oUq.lBszD?TaqOtn^OW/O2qUnROUc}6T-=($@q])!CmBByug8DMpG;<oOq.Ix+I->y/Ly(;vWUIH6nT_RpB*ay<cRR+>+Qt=WhxS/^5R&Xm>@$%dNy26("g
U@6EMJjkl[<YwVq2.~_0:>4q/"nC3rnsksGXS`?kHGyd?EkuiDd6P5`?
&BGNtv>Sn"NZe:;vh]VY/L^yz(O7.PEO>W<FW)#.:,ty@6c$;fRDrNaCX4.
q`-Pc!3L4eXXYf{R$mwH.:--fa;1ugIiC3B]l3OOvI%Y~,#[=WeyDbCAl:c+-s+k.-+l&jEJl0b]2F@]XuOg7t73`-+)ilCo1Is$$a;l
lHR,#c"+P1L
F#/FNbp@.207wP!T!~TIC.a2/lZw?veHalB~H44r[#XWcidemla:ek0uM5]2"v*Lk]Re"+>8i:N
vs^
mjQD>jZ4b`D>
4vx8~vmY#*wn78{X]GQ#lN1%!(w0&9eGmG~Y2UJs(QqbFs_LAd}gh:pD/o00x-&L`0`h(@?XG=Y*Fj2egAZW|R$UOU$3gY*eEevR7v7?ZL}[^rPvQ/R0`!u
jkFQ_glEt/mB{mpnL@M&DF"=&j]T!(Qxa7}!Q?tD5-bpkk_"55CwR&b%O=X-kj`Y,F3x~
&tU^~S!gxd=2=qehC:PSK%{
n2,Hx8.!D4)`(
"qf_cD}^(7,"x<1tUeC_f/,N`7^-=gXC5%gvQ?oLTR_+G1zD&7yr"UtKPgkBz#qH$b}jW6ngC6#S]J[VWlKm(f!Ct"+=rAsB<UOz%i^)mtMs1u8e.V=!8$RXjK2Qw08xCw>O?3>;ha$8AnRk<*PW+dgYuh]c12M.qjeeJf?[$q=ODhel$rYb.!qy+&VX~6,QmQJ7D
J#9_scTw|[`L1@ocd?R/+aQ6PhKbmrIK!UMTsOa@=tDK=_gVo$N;s>Z_Nr{9r]6]y`9
7!F#1.UDNx$Rc:XY4SB<k$Tq9Yv-/xQwXt}]6F9swO
d0?%$~J`rz`.2NmEwv&ac5%=D8H`wv@Nro
iV9rv`1wRg[_O&F^|8(S}%-+IBBN@JDmr(]L`i5vv(LmYr}C__lZf,d
i!.v4IzD}1|9&@MA3iT6Z<]<--<WS78QjHb7(ug+>qwwO[<_]NDIAu~!wwPwoh#1O<D]nM2Ygorjer-vT)$MusO`D@EZe"(!eCeU&_a->MN..
"/%_|U`qX[Eb*c`=3XxNc:z?Jx)!V*oahuQkx"r3^g>YG,(*"y@cP/B.dNaL
rSA5NQ2&p}mxsq8>URYB@3=`n2^6]8q}Tbn:e-<D1*4TF]E^qrE!b2obLE(_1Y.1.H]g6t9:9wU"]/4+fg]s>^:#q,,CwxVw_EC!W:d)/z*%h:/JilKa&%S[on*xeqN$ZWcyPEQQ"GQr)gwF`*/5y];/+]lrIXs6
Zx}xN3M8@Sd0Lz&%;lj]Ey$XfZ))Sl{SNgp:FYh?5G.xtVP!)r8l&bXoi=2ZV9O`Wt
z"ZJ"p:HGw_eo2)58%DJ"Qr|^//kaWh`8q+mfZpLfx(M$YvnfnXq
v@se1r%`i+NoM2%Xe3wew35_Qadk"gNQXOjo$&ASpX<;^SS1J"6WFM$hcDj#}w*;Epq<IjVjaN%Km2=EXS:`Rj7&3.VMR6Cs+7P4j-1syBc._gY"vWCR}K^5n3RYnlj90o,2Tj%Lf_Qps@jU5/k
x)N?|0:b}X>j$r#MnQfkM@n`Amo,!Vp"G)0
~CfjMbH-<o)r)+~K@!z[5aw3vk[w]$ZM>k{#ZC"7"+jW24#<^3yrtOzS5[yAT/nf4R_p}E2wa?N41l]Q1+/O1S9jfTqF1Ru<C(%9OvCwXNK.BEj%hS%tb&dD{eJ4;gEIO4n1{68A6/$6dfS27Ald2KW
eF*u-AoQeKBDZS|E:ZAorq2$[(}1D0FDUQo%]Nf?a@n-Zu8T"4>ED2:Y._aG^<3+VU99%)#JxBokum1(=I&s-)"N5:GPD".o0[Ku+Vz2|p[:zTSF_hxr8hYty]Qv.n&ixLsH*>|[.24K(LIIX]Bn2XL3CL?D7?W>"N7/}-i53"6c3Bjdrl"sZW-`K>b]]=2di:4mCAj#"`>IWE<CJ`J)mF(,JwSVw"gJr#][*c;<agxiF+IszTi3u&W[=l[)KP+6ui><@$%6Dhz[i%P56!z<x9btCvX(*;*WZ
G5]L
]9t
An_Z_eaJ5)&8-Gv:m)H}(U$0"SBL`BB$UT>b_=DQ4TS_/(4849f@a54_FKaibZD_h2Zv)|mrA5*kdjZ^<f#^$1n/0ZuM[18i
&7
lxY:8cHHRQ7-9[47/8?2(r1m8^R-X*1cX;Xju}]>f}D
ZH)b3%8X]
%y3s^q66B?
3xxboso@.Pjgj$oWMj-YHxo%Y?t;PEdjqG=QgxfEgAC1-<1q(yjnoV/Z,"Zq&kYJEm/ZndfM5tGy[a}-~?2%cP;&UcEL4@tWQs?r.q7./[#+Z%QGDpj3[48Jr5Wv$H{r
`"Jn:GA1sivgG_F&;PgE_Se5p`hkE1C3p;Fh>F;3GX2;RTgo:u<#F/A9!X%=0?c[0=(j0w=mQ?XRGtJb&`>O8*Oh$!fyq)8bQFh{<
80>t^/Y(j/kbT~GFP7A>ltyDQyT04XfS
E*O[p2yabfr%(-sf,*@;:*U`f9INO%?W4,[k`QZjZ6&!,(Qa##I=wHk(Ny&BnR0uc?o^-H2R6c4Afc{:(9$E.7Akqj?K)$n]F*s7t;25h$&>&VCZ8[RgWsGCG*%r7kp&"qJR"g?,E_R%3S.M~yVXrWb"pt(.*o6MrioJaD==>5CGQvgh{E9A7i&v`rO-KQsQI(Bg;#PDY.rWfebGaTSm2S3`oq0B]X3]=Y`$9R@]3dYH
>[dr%uP+<8C,fic/e0nD,fl=T[Dm
<rzI4dEgBjVU9J,PLC|>!=;-zk?U},It<7<GX_,q@gb9zvWa9,Mt4,J2oi4MI<bU2fZ^b+,x=_pG,sya<y9/#w
<)<&g#b.GN>3Ft>UG$!ERi$x<
xdxe1wnP]&).G>1jFEbq2,#pIfuJ+6PV
;@j1fL6QDq(=n>}BPCC
he,g;M1lvMV1:1:vN%=A)vV-yNtL.
FAYgc
f27
]3@hqI&k1Tjs$H8n;TsBO9Y>r=pG>f_gWm[:cAumxPEWz3RL^Xym|Gkv@O-su8xLH(|WLO^[|v6JOvLAT`"kAcrmw6tM
.[PckHfgy
EZhR
oK8ICgyHWYk"1,S`G`ETEbqC>vNn}7]Y"(,],C&Ugr-Aam)?S_F.++7!gq&Un`pa"K%%-bOc*)-Pz<%/hm,Hx[*DPc}Q&p-2509KN0
"/(nEF[DsoUPMxs$[__x$*DoRSp^kJ.UFt"VjOSJhYhGEDf.9^$"4zDEU8$3.S^i%<%eIOlc/mdfuxJ
k&vHGxjAyPxl[*4dZM+0;amuxZ@ryTjV`^TOGz4QGh7w0%.*Gf[+_BtA1HLzk"CiD3&nkx@KA{JkI6gPaX:`6OOa8TT<[c::T_qCshcHyb?c?xgt2{b{A,k/bll6B]KX?<K`k{F?%3IZAlUqHAi1fG"rX9?P:nKC`60h?D=OmR,[s:xc0r<
Jv?IJLq:].]=_Yh#[eyFsX#~S+V
Oh2s]MkYB,=IR<l5@r.N_+Q}GDNdF;hod"evdf!#Edy[L5wUE:Gcix&`-=+E<^4P^#k,G%9
bF3+H">4(PGCo.Xj
0A6+PIe3_kkBa"daKRGxo32geJf)|y
fEA/xjKlnj_4Y0(:?=K8>Y)v`DHObL%NlYYlU=vEU=E$U55>L/[wfH.vG6]|LDQsR{WwAEp+KMB3*fpv`V/dH@HBJZG:Xs>M+9&=)&n1`h)]Av,LHFbIOa]vTmXP0em6W#fEy]]|X:l0Q_#*cUL>&9E
%JL|m"VkP<`Nbf<V=p-#[myTS`%WR&]!`QA<CFBN[-cCZLk#Rd4b#Dt=+>]
RAtOy~uG/[wMsWYEjl`Jg/cN?Y>"GCa5L<%SPdm#6)^fxi^d5>sFh5i9x"MzXKM(y!Lnp>2fU|b/-UA9AZ@Hv]@OIDX0c2S#7$`7.|%qJ
1<J)h?st2{2Z8/4XMDvMPQmT=OQslMZPWIm0%3^wYS6,#nGt"ApoB;IQaikki0p8BgUFY>/Cghks]l$Ac#2w9b@fAk%,(e`8c)DURwI/GXVCL^N;6f$|9@7ghtbMnWFEC@bni(Wl..Eww4(JgADwV-hzqV:/d./Gy{>nl&T/10WBoNyYxbRWrt%iV*6u.Gnf))%D#["Ba{Y?Nswc_MgKE*Xl@4H!7zPpRlafiNA%;44Pv(LW6JTnz%>myzX+yBtzxM$-ZE>_E;3%Rm9hA}wG>OM{>U@/h,^oEnIC8T`m<(o^sLV5>MQRcY:~a$:l@Pua&-!F)&:,.&j}j.DKGWj&>OX*2DW}TrjKao$)m{)?_LMB4A;vuV&&iQQt,=u/7G[F53izPCr@dft5TpgzSTk*uuLvJcw{rJ6M[0n_lfAn6@!K;;x{`Tw2&jX!TtLV5o7p]vdao$:[_#l#T_UWir66=d19PI#0D(v}6kMAw]vOw/2WW)gC+6[^L7iPgz?WyEKx#ym`xT^wfvEhAay;Jpvgl<I-343LM`=LI?V(](tuKlqkFXcp$6lN!R:[]x0knTvbcMD9
Y>F%HN"^9j*5+qMGEX3sbt*mrm.^dQiM~xLn>0L?d>V/m]=q^%Goc;ew`SH";YMXCe.6Ub1vot:02)>=*K)ju@Smi_baRS.];@Fji>Lu8k~o&2pAzSMcsJilQ6YezLQRp,}OHb1Y/E<2_:kt9y;9V/*dP$$6;5/y0EQCql6FQDirItT/2]`sRFHa3VY,*D"jY>p"10aAXK!gk)]ovYfIpHnN5LO=
EL8RY91l)Ac|FMCnn
qm1}WnBei5DKk]bEQc.7_5pI"egcIX[+bYq`X4,T-G!<M3v{a03ym&Mt`,]4pI
YgOiBB2mFZW(pj%G1&Y"<_p+htAPweRO%-;5dCL!xQ#5INKn0ikfrv5Wntl-2at
O
iHyBR5D+r*spMy6Y`k4S^4=])*`FRD`2S3x4OoZT|i-NLM|",Onkk@+QF@|gat
#)ntDXK<@H7auP`RsSA18A6OQuTrFwRgc.<scprVX/FnZuo1c|RzT;M!HIF`Gv>Nm!/1agr9aWv9b7:N>#*C6VdNNiuh>OqP/xo4ahjH9YDF)V91D2[0I37*O1%nVKX,q<Fm@oU:=tZ&BXV2p"i.6n!TXQn<s#${UfT%PyhHi5xqhf2%C5k~,bI$[JuUa{E]#pAH&pPz?)%yroR;"O;!<0(Z$hT~/&nj%W]u0E9W/kjv>IYar[25@~8wlBjIxo$pi$Qlfs;Bi>kqajG&D}a">e:oqOsM#_Vct^RfAB&N[;o-_*`7+)eaixxdTzUr3LN31U`6@Q3G#v;I.@t;X8$*V.$7rZ!JY1pXEZAZj^!,]e?Jd[DN@"Q{;p3r9d!rA:/63#6HA1B#9xTacAe;.pL#[=GAFctRsa7Y[u!V
Ku2]*VNE?d.8S)YrXlJwoc%b%f2XKl=0C@5GhF3ItA]nmqlFJGGM(j?5XtY1F-t"p%~Jzw6OY!WAHs7QNOJNE8nW6f7S,l:s^hk4!AjuRocf"QOC`Fp<fN(.8*?
(#qU.(8+X3gkze1H|!wto#:KZyod<idOIL./!py3PX=gkP2mae4fP0U$hREUA$^69$}P+^
=11AaV.AgQn?;q[E@H4,0}bHjejtPPL*5)&Tc07~`(&l#ke@Q*ddJ%UKj=_MmOvCIAfz#+E%!I2K8(W@h$BP$p9=K|K7E<c&HIJC>$n{Mu^SIk/>n9=KYd(g_}Q`0^YNi&3j2wGCv`M(M.`o`HIgA9FI<?bvH1^~Au)nu;,
3[TOAGTpbZ3(x5I.8EL(x5:QQVvq(tCNi$;Tv,%/,iwT9%1_P&0,1y!kNL?Rc:`NlMkJHFNG&t&GI&na8CY;d6YdCcJRllJMC_YM1U2cevx5D|v8d_V)L?o6SJ`xri_P2Tk=%k:5fT8|%lroC$omSw$}
%O7^n4o6zN`.U33eY!q)/
fu=xh0u>tAJx[x:rf:OLB[=7&-+ErfLS]*w)qmQ-u!4WV","XQwh)9+->.^0O==+)OYX}4p@tVPsB6pR3Jg&u+^oOR]:E`Z6s+*dOnwA]eH[Nfb+P)eISnM`?5{M~RpjML2AUYF]wUspL%19A"Gw[N~b~gyQSH)-eAoAYI:1YBOg,AtOU:|a,sjq*X]Sdp2fC^4Xu7%l+0ny*KeB><EMl>Wm#"A[@dE[Ci`"a`h:?!E8a/,/zP@#1Ps>BNiNn$|Y8r
"/Ygx38&Q~(q&1OKJ@"S1=u+`%n0h/"%=bF1?9Q5aj7+3VKN0Su-%u#4HGbFt>ph6.GR`pq+T:>S&@!TNkMvCc$Qh>?5PTF$0.)QH%4Q)Rs:EiceyqtK0a&&]&=~Yjd7/^w
-7W|N!MycKx%6AR(^P-t`S0>RLYE3)GD]^(k/BHuhveCj%on,rWK]_m:82JQdrr^uw"+Hrh[7yx%[J$MTvUhjnaT=PG>=LPmFTo]cJ#jjyWIy9e.](s/hd3C!d^"c_[e<)<Xvc"`qj<;/Gu27XqPy,5?p]SC-6m$
=)3dx1wOUyHybE5E&5^0pK}&a?pwoQ(t-
e9Bv1G-a&C/JeI,n
XC$5Q7
92B6fnWDIm.;C,/P1W,9]2Yve?ls"tC-7L~e~_3</A0x
ft[`-&OckO3yaOX"9Ao9L_k2)^a%lISw!Y?~k&F@z%L"NTc/,g+|<y:<kW;}GQZgIPO5Ocka-.^)HK#<YGQ~1cWZ)udMV"30={QdWb"xSi5#Y]kx.l3m"i/}viDoDT@wwOr.WitW3`
eTDm:^69EKw4LaUC#R=a{]OMK/A?X`)2LO#iR4j=}lRU`Q7p),^
me#W?1[Kbk5AjSa>diJ#:KbYI3|)J#L;-^T%yK-vNd"`=
O-7AROslW-6L1T8
I5~f+AMRp-7*qa3R5Pw2,,Fi,03,$H>`4/{G%>wp*?s]W`5"p-8>488_3*e$`w!%J:V7uVN!DiO0C:"4VhDLWweoMQGE]ZIU9"dvpKnw7<Ky_RZ$C#H@i0VMIKIy1WyIhmL+(X9$?RW^ey6V&wFu1Db1*:%@@pq=[:&qdJYEx
l]Ph~
gw{wxU6D%^$d/d?l?EmO1@:&;B0v}(iH&u?=|Et[IF?6{&n58=XbQOc)lD+^"M:EnMlNQ8*6O
wmhx+K;8giLITX$RUcU)1`iA3w&bIh^Qst,lTEYdm+
t8q<Y`FcmX%8sU8:9emwk{u3(D
SrtBaTRy9c7ZO;:g)/dRbDWO>A(os66XHZqQ7MewYE{ew;5:zUeA0Hl0?b8a%Vu+4%5f>!NlV:WYc$U8Qexc0CYR*B&)m^472D|g&]XFfI58~o"&Xho/"Pj-F+xu5::;ku"_],"&*UEsHk_B*TQj9(}m>rA,S.YH?8&tVX}w/HHwo6?h
h2JWysHiEz>&(2w!4iH^^FF<,M.k9s=qE+?cm_u4C!oe^|JS
mWK/|6<UQIr_-ceIJ?_!c.mMC=LN^2sg}s.nU]$SautKYF%f0`~PDF0%)$WskB}I%rP
&)l[yDp]yf;,U#DSWo.*ZH^i9_gx["Vn[wSv})Qj4>dvj%5*0P`GEJ.E
+xH"O->K`Br;&3tX#z0I(Aw/4WPl,5aQXtWjngHlV6VFY58"M8hNT@f^D~f6q=Aoy]b[:(ojZU?^JK]UNH)eSqC9v+Hq+h;eRBWj$Pj)m-JvnObz%:#JBfW!raW"^!A6SZG>v
U;gX3.A2
rkQR&+_rr`6Hzo5KX.$mKlSX8_^F(qC=,b`Y&&fm[g)`bU]Hwi*2WFWS_VE0g?f,nh]u;1Re?>7lU!Q`"T]>Ykv0CS
fDu>:Zrx*va(W.Ts$F4h*i^22xw~-~P^9/YAu#6nk
bjD2b,0Q1[J@!U:9a4E=psZ{kbZtja71?,L.)A
/
Ux/p{L`"7O%kC6KG6.KbQ>`ANFsFWX(#sx8UPJ{)}a.,pO*fl&aYYh>f]
5,L(Rr;SZg0,]r+OmcbP.6?n~UTh/F4P;G&Qy@b]6*%Fu,gl=O;/rL`et*qUm&/.i+=:imYh&^q[x%GYmJH8PhP)H$ip@/n,,62E-e8hIp@Q](s45[_X(UgmZ(jgAhX8GKh?m=B#7Pvw~l_rwQT>~j%X8R_@fQJ7aG&5}BO;e;!_ZixQ<;%?!;2+i:NL*l
#R=]UP#JN8RFU.
!7p.],[xR5qrLa.1h<Q9Xc7]-oyw-Y]f?We0&gomd_":]0~5Ec!/.MQrv4vHYN@bfr3f{`P8;^<2e72?{NH
p(9:^?MabT-xyh>H|K]gkAsj/r-+~l:i$mAf9[>4U8x[WkzS{#hCIYt4o
Jgu.5W^x+?fr?BKire6eDjp%
wt,wOsa-1d4GW:GL:BEqh2JQpRAqu!ve^~bqWU%cNtv8,5qBtl]g[0kV8u4JrJ#?tdC~R&q^4r=/%]RNNMU].MY1Em&KF-V}5y8awy,>EjB)+Q#1m
["V^
Snlh*5S(Z+xMz"$DmD#)L
w+[Cz&g`;),yd0a>auC^8D}(@Egg7fe1r3B`=+#_M%Y@SU;:`@Q.r3Ggl;e^h;MnP;J0,A)/[Z^]$94oT)=@AFRQKq}sO`8u=NedjFVV4?]L^ovcdd?
|69bI@pB6UP,Ngg`FWp]=H3w/p3%fvEbKs0m(m[(X$_l"rdwg+)k+;N31b|qe)[&`Lm0X"6%j3xetr+rQw,Fb3yVkpWb&2J](
wp590P;-Hw
<cUU)7Qc%r92-
wSWa,jqY`!TP/6$y@DK(Gu>q:G2p6""~WrQen2d~sn^,N}.
,$<RlL/^`OpNqo>qqE4r$p_ZY9O5N+Y+9_@YF
^
J2F(TFA7Pop*qqPjfd9tksnCA:7$/Q0.]zs)>C,Z%HpUYgeO1nur(z;LF!]}4c&STMn`Y;Ifj,WiQ`w_=./s-Gmbd2jT5aiIN6XMe[A1pwR|CS,W<muroM+QULk(SZL&NY9nHoXbElTAt}3
`mdry63D5|R9KCj[n-VXE33r4B!"fj4dhc8:+n_&vX6@^bU@<gC,Ix=cU3R;bYpVu}j4XC9u8Q&Wnz?})J^?^N1_7l;NF(voN7H`<a0FbAQw+dGT5AVD=a-@[8:7xeF{?t5{T}hmKeV%4j#HsxdQ!as]4i)~;%3AQCf<?Qp!or%L1;ki-S-flbXAYU8+<_S?*c,$s>UPZ?w$f)J;JJG~;t30l>B/J)#},/bnU%[,oTq!DFo$POy`@PEIy-^ln_f1>oIcLgErMC46u9,u]=OA,/g~Gk9`MpkNGfs"OVl5Cv
Xm)by4
[`X/20n/9TpfVGh^p>v,Ppk2f[n^by`=T:4GxaWujU@A`*
}ZN)FgG5)TzH,@#/AGf1/l`MxRfj"k6*wTn[!2T[M<U5[k"V^vv@SGhD{oPO[U[/;$m$iZ.=&Fr*4k?P8NTOTwLojgq#ce&1Qn1eru$m0[[/*HBA"ZY,!)#i5[b:9#A4<KZ%m9UxW7+j*5<O:`M5>a;huA/%<vK+{e8Xuu$K8nBInJcw,,xMI+,d!r=cA$Yq1qfPS[Ks8]5$!m`yFnMH_+Gx@^9XXPLBbwUMqc^[Ll(Ynn1w|BGtD,=uYO!_:B$R1%+O!qgZCob^p+
?)8{*[sn_w+H,mrzu3XIWMstwFlXK2"%dI!,9v3xqh,oc-qrnXt:YAyv8r6Qw)t+W#:[dCnAVG50
,?qI9<K_OI}46x:IwDg(
qG$_69O<jUJ`*g5"3|!*-xs~MBW&?b
[iEE}L]GVYVny=}w40BEBe3d[HJmpBNW4d)C[bK@o@JF>B$uP`S4`fsUx=##F7<-#b:-9u1O8-17&+Wkvj%>~*F6}?-?=9b+D^+RlBha,2;=c]G8OSi
5La4;sJ"7_aI^VfOU1H(wODAMhSP?izN[+Xu^XE"y@DV](egcV/%~%BrlPi96Inm?C2T#%`]hDkr4ST+S5
%@T]ywkW>f+Ci!72
ALSQ&DAnd5?q&,&2QM@rqA5(St5w<aMJz6c:G]pX:ImBOXVs{r{1vi)u/21NQ#!OH]Mjr+OD#J~
.Z%W|H",KrjGcGWhnEO%r3Ms[[bd+EB9=xp/tk:ktZdPIE
.=;krO8M
bf
^7L~%Uk3wK8J4|:N$~n2ap$QhQ=|J2]pi.Mz!A,Kh_u^&FfTfpsTbZv;8+QYF0JEc,LHN?9$ocLJm_.XdRcCH
y[#l&u9ab!lON{@&
K$EtG,}TvtwW"b
J)amtG)GL_UTH"5{uyoDFbhNnTEvy&pSBwbY?6:]..l}avZxE0z$5yuJnBD#l7IvnbBw1}eZH3d[0-XaZ=u955scup+|u|A6AZ/Pc@5:r&,
5>cr&eb0A_7KuHh[5wR%knZsH:UP,J5RH=T.0b]>LyY@$<F"F,=|YFSDK,JxrH/]F+;b?PS)Q0demBw}Z
nIju"GMz[qY&DElgLGHqoeK|K+;Ju]F|9BC"
j_kqvbI5M%zKYt;SKTFMVHZ8K;0P;Gyu:w*m#$RHr!"EOQRlJR9j}X/[d.EpCO?U?nQ;zS_$%J&c?
c@Jv+5!TE.>(=VDs{<q);SCizeeNJcFDtsI6
,Iyc7{S0Pu$*MN#]v6wtc|RUrz8<L3ypfan?Y9Q%G-9+?1;CB*Nh
)*|U9cs+zaffnv{-*?7/Csjk$.3Hj5?L>1K/^B]s8ThLox;8FeBV>t-)T*Ccqb~Fe[|BZ2
ETJ{]y3FO?jG#$nasWqlivxI])+X7jDRe0a=
Qm@n}xDctv&2+Dz_L
_l+r%X6L/<
fP(s@/Jjx7C!>[U$@k>Qxs6{oXs-*)883=]/FZqLfe2Hv^Fl(I(9oz-j^2:{?ng`de:3Wgq47>N}2
yR-6d@KV(>[2(&#~
H2];$Pf$Ta^&uK<c@q^_V^aYu4M$tDjDlH"p|kz]uXKvG0HH>qs1Z@{M)15Z9gKL0
6,Cy9CsN;NF$<nf>}yG*^.0kvO;YVVr2auzjcm#`>9ZxI-=F:Ex+{U;xE(#ZEjRB[l`nY3=^?<mnBYnse6B0jl*s8X@JUkSv,u9sw#2l}GyLrOoVOWvD%o{@ev~pL)IT]_qmJbGvMnH>:@5fQR`r;NT#47Ce|?#7AprUa@lt!]l=:h1[4dT&8s@rr
DE3$9)pIc:Eq[vZn=xeDs`FAXUXe)"BAgGlePwA%C?OOo$^S,yOaO(!N|S$CA,SSJZ$ERH6,QJ:7QZN?f@
<?bBqfN]e@n
W3$VNDp]4#K`F%?QI}b6<Q>q_a1|+Eu-0sP`0)p6[viva.^&%fb))DSe4*o6,)PR.Ac@e~InIF`!fp1nVdy!AW-FM=tE*`#xidBCinJXtd/PY(l{cv2>]n$_YgbNdA3nl_c+,%BHX_xO!UGqK%_{Qi+<K=KR!WI5MK"U+lW8p@
~v%
:]#n.srpnkx7hX7bXQJCb5xa7*~]Zn:Lf_Q]B0,U(+TLj@/^6j}=W/Iv!6
oL7#q[PE!+gJo).J,U13+u5o$f06
/u@wJH62K[cwryV9FkMa9q#6YfbtN%_R[EFx^$


@v[_M~N9XX%uLDCbjrI5tqrR/-M7PV(eNwH`>qtAqA$/-6OTHO)2Fw>hMaw2nu[wZJRBefjlLLlR@QML1w,Wr|Q?qnR>IFJ@E4Ewu04sn)%AF6X`j$`[bUU>U6pNBuN0<^VurVs|CU9aG,yY2J&faUW/urEu7N+na8G:uH%jq[rpIxN5I+njob3aX0*?VYRB)oG2iKQ/o>e05-8lHx)Jrd*m[j8p?Vs&a=L5HL$<.M<G
F%9,Z11YvF*"s;!N-W!V67XjBY{>W6=Y"U#7j-{*&Y>M<T;sq0^g5XYXD.M%(@Y5ouN_w&-wH"7nL
}LdZkqXoXwZFp)|".n;,,VNVb_]0EcU,~/DOs
^QU`@YHX+>;;rj}5Z/nF3d&XwIt">yj6|15:T1GgeV`]:W#w}_8
;oa_!7{vZiQ9oh}ryxf@z6>R3j!iqh!y*FMAC-=`)K`"6fL%<L"Eqi0w2+B-1wc/Il=*>A;@XoM_po1.d[zEPihfJ5cUWJ_35usXr(4M*CXwXT`AgOkr;0UrPG6oQVu9(gT1Kc#@JuG$=2&o5:Irjiex+Rc95@$<.Z8?hI,
FFM;i=$REZ)ZLhVgULKL+"/B.q+jl8.k8YZ2t)A3W$3*JuE:igj(@l"M%g5!nr>p2+XQmSL*9,J_afuQ0f7oR/^HqLCcEvj/ou{!h%.,?@FO]bL&VK!&sSA9myXLj1Mu[mG"_pKk`Lb@k7VOQujlUlm<BXp
]>f;1l``sQc_
NxY1e|D-`
*>TZ;]k`;#%!;G!Vg;]W84Z^`yDchD#o$u"Uba7AB/&.=DEk%!g%KTHz,b38+g_lXh^SP5-BJS^b$.3V/y$Y)o5$f,pM/^L"xw;&*$e.-MS:mn36![Dbd(xmVJa`CBkOZYbja2/u+qIJ`-o}UPY64I?3V!?`*z3hd_=f4cfLSAr3F<;6tyTZ0E-;@,d!9P]s9+$t&fcf@1CDo_]2M$jC#|b1Q}?z2g*v@v9M0!M9gsArvI]S-Ax$t1-nE`n@5a
/RR%PTKya7pSj7[N(UUB6]&LviG9V6Wm*
"eLiheNSc8F85xQQIXZ$ZSs68qyJQ1djPlB^,ULU~B?^~I#Tx,jxawC,1SIjV<BVs[Q7k,XxDnWgJ_Al5&h
l`xo&Z-Yp")c3y|;;VD_p/hHAvluo7pHWyXv
ZB9I;:3ENk9=&X``W:9q@,gaUAZOKzL4/t+i0-7/NPVkxlq<E!=gZ6+z,$T#.+*@h#M@8kFf4BoQfvl-P@Q&l%=vf~uD_2;q0DNg)PBwDBO%S;OC&Spnw;[]trN9?CtWhsTSrGaJq^dR!OZ#y4n[A@k(a8x;C/bvNaNZ6|e&/TbF%0TJ
rqeUP>o:_=pN]_HFx+-sqm"

8?.8]bmm2JW.Ezl+HRwK._!<*&]"%*Eo1lbR8Hr1bWx
w(S$j+%M_)k0_8J8N;O@)0AtpR#+d8ErcrZIY)`ILg$`^u<ah#=-W*BsP)Mb>Lr!Qu1*]t/hg.z!l<s6)uR"vzXXE%*-=()cg8mT^5H/W[Hh?25o
^f"^4vK-{fG@$K*m[&!"|9UEqk|iDIF%E5Y6J4%X6YdL"S#9F+%*o8fW2oGTp^)wAez8DQB0D1W#*-g3)g^h#c?rG=jp@M0h^O?gkPMYtL[Z4dMr2NarXx:oCwjLJP+,]tV3b`-lkO<?";^WLgYo0c@C8UrQxib+A>0OO-QZw=F.#1W22^Qy9ulS%Gi@!<yncp+C[.-P64,c_&z<M;cW
@flB$uw$PaaQYK+k;sek94Z(2g8$2!]@Fh:?].p^iFY>MQi/1;$eD`^Dyna!4xaSlGwn#tX6x~Qg7y&`W{FlQ/qSv;GLJ3VG+Ga<A
A7q!n_&8skpl(1HVgyV|<DK4a#lmFnI]#)H<wkH/-|1n<s8+yY#.A<OPd?dY.#AEi5?:s)&34,p~E3eDsI<Om~kR`tX!)cnZl9Rl4q%yO:ou+g%kfbpX-u:s@M-n8E/xk{J|Jw*>mHD{Xm1}D,@K*4-4&*n6a7r@&VAX9Me9N6B>J.@mR3,@G=I@?`C%9Bg[e</t&cLkDjuc/@7Qkp8(psCU,ToA>jjkv;P"*mjujXM]5{XEjQ+hQnjb$}H0s5,]+z/N0K^us/S@rS,=y3k;YLJ/@2(,<?XavO+ygK>2>5rEnx<&tE=aL4P909nn2*@6h.it$93315y,KFVo?cIMSOPO&4I,il=s*c4D_8,G4e^)*z7.RP1/]T(x)pn:I5CtMzKms!p_8dU&El,DbNtE7dfCS!K1`sW@WVM~r+sA$`P%E:[JwF4Ugyh8oo013yDA1@GMkG1c2)tQ=s3xy>kAj)m5l%GEa"sv?[I10AAa7J
VG~*kt
o!9h(gu!sU-&Q3)=F__91e>$NdX$("^MTOmI^lm(A!UP3!,fKed8RQ"iqvTFES.>6UU6%zySh
&VJ#rJ@sVA6/q[6$+SMICd(lp=`:;p7gps?7l@4yBB"]u|Z6<3.W6~7vL=SnC([x9f-lE{Pk^f]YeO(kN;PIc$)+1?!<$9E07B2^NRU[OvNd-iRx*Syim-4I&so7:f^!,y:eVs!=%t?vK/F<IZ-{<u7r*5%F_h!T8eo,DIi10WQ9_~QA`6(#)<x=;"dm>s+k-Tm4La?h5HY3Ys[m73K{#2EM4%2#6%vgZbeMH_OJY70<,M)KS,4bG)XB*am&Ag-c3%w?CgdrZ2#qErEXRTs>m;",#GKN2xllrf//Kl-NmCe@&]:@X56TuW"H+j:3%rrZN"fyf8iby{#ev*^YxNo0vK-KoXi|mZS>quM?KMS$fuZ=_Hn(x$:q,q8lI/1TqsgMpjsC8;oI&K8+=?rn%;%zdNqS@%yScDVwQkZ
N8yQ-eyx!0#,YW:8H#6EY0^(#/6FdFH2$fr}wTI*/7oAy6O%$4xtTYK=C/JoyXn"d,p=jn>.bvsQV^@9
B<R]
$j+WqnhS#B8pwiFsVNX&"(3%O%t:#yrTtS<#^|;"+)5HSw>/#TkYff@{f%Fzg*LWUD:?"V9J-08[+1chWwW:c659>-`1Wu"yB"E8@M`PM[=NY2qX=3Q+*I8d?u!"o|*Fj=y7Cs#$VgXx,0_MtRCd5Upo(%FNNy8=Q*;o<oLB&G/a0|,7a5lEg][@LtohyBHku)g:ZisTiyMs@jxZw5=Np%a^%w"xsRc:mE5aYjT.U=UM0G#ur
>I1/!K&Lu85SuYP;
K%5e@w8+<cfclPs53]:Bc<him0/[{
NvX2pW(tGcDjF!1tGD%#}mtw%Jm97j20pv5QTBD,AUJ]sW0oO3IwALT,1Afl$wCF|>0od+-?>(^m@m"a{P^&8*&DB/]QZxYo,1*,f$GC#x+=Sm=,}r/NcXBn-yAM?>D"JnInWlg8"*xx|1U0:eT^+#4)EV
Y.BO5PtFxTRZ5{yQ1wT6u|gjm{HQf#Ez[=l2oMxSz)JW$g9j+9cWfO7(&vSU<R:^1]-uF0)IX@a-,~
KD]>5"#Ya"QutQ>w<+|Y%%Iz&I$:e7.y#`bH:7G;|&Dvf6I>*vIM!!6=K0K(TqRcl)Ie,$BoW"Zd9O8sv"
:$
t]KV$q=34cX/a-
qVtxf9nEVN.Eao7M]vX/l!Xeybm
#Pgc>3JfgI(FHKa6vyy<&)=Xj$HHm=5`tLVd
)o.kRK%c=Z7KgP5l<I4X:;^ROuFK>NiXt=ptO!ue:tRR&w;+xm#rVGeGgNafa>.U9@65jr`yn[lP2F.47-GrD%a/D&1j4:-_)00eEsu:6%%imXef
tUgYN,[KCgXRvd]=y{5By}D02f0q.q).u$jux]Z&"gmz^~e89_i,D0Bu6]O<X~MylAiPi5nmYk1<l/O,80+2]roHqjx
fRoHcm0x938B=`r&@%Qv10UjmKEfqS#RF.Lx=h%.C$#&X<a5L/qm,6!3;#^5p>-5Ngva3lnTNW.IMc0r6kK4)uYA5x8o,|92/oMX**CF>v4d45;y%[h=:Xo!12d?:EeA"Ze3Q{Srm}S)wAA@!qJK!=[1r.#6ftt6YU/OXgdo%ZSO%;&@[l_)ThJR@;bpGbY?nMdqdgbj<6Hs^_53D$6]N?Y_i8`[8Fp<S%/3x{d-J<hm4b""M<^bEj$I2ZJHoUrb=<(,Dz"ux|C=$tC0SW=87fu$N..E=a:wZlH_h:Q@h{,0,`504A$j2^>3gN"B.J*^b*2PcT32wpQ~-DR"Qno8s
c
9`i3"~Cbeks[9IgN"<8;LDLtwGkma%Ae>"4)T^q2
fk|(4(1_C*V#G-frKV|xo^xd*D)+&Oe8-,JGw*ZQ``L`bP(&;WCT4?*!``"-mxb8=tcQU2*Ga](NH+bP^93Z]j;Z,A@>Q)0<CJ-xbB"`]mU[L!fW)[F!sdETEP@a0)Z-Ov910hOvD7k<MpMDTF;;LQ3N6@(Gk/Z_Gv8p*1GLXA"S-7>v*c
y^$k`-4&)YiTrI_aisu"[Hsxf:Xe<2$r?>k@s-x}mpr*UQRhI>@.4uPIwZ80W@+#=c(a7KO|QrDm&?>;kb%W5;@%;q$H/U+07TE!b$FYL.v*(?PbU)NKm5#xfP
!pyDVAr<=_9n0`
M&y=?@M
0W;(1
TrQX=;K@+se3&9i]%cZSj.l]O^4mO2FZ<heL^EvZB$"aMFDZ6SDT5Vm?C`");%XV";g"7#k*?y+3?:(;;EQS.=2dIeO*!i+29Sr.8=9Nae>mZZ"}+b)::^920c[NT"chcm8/^gGJh`7Mr2*aF&+W4"T&<_0P-q"2(*swRp&sQ<_{A;5;/>l^/BEx>.")a/<;O%n@gGYF(h;4bxP(?9TS]#H)Q,a%
(v;u5lR$28YWuv6Tx0mZ~AOpW`eW(EdL_@NAtyT)fxK.g;u&4+R!FOlgz9"7EwCg$xc%I?
k*P#ycU+E-mkGk)vZ^ZI]u>-oJ4<q}XgSJ;tO|/iFIk^G.Y&+7]=C4Gx#h>$#iyv,IW8Ru4FAK2zIC9!wZK=0;V6q{v:d/!1,e59V!L>DH4P[3H-/B:?--2_ly_LgE]7F)KA-WCilB7yWC
[i]i-tTf2Caavn8c_/a(h=oQ0WPHfCBE;nZV>;QsZ`3"EdDN"
$SA0Q?84Y$=,,,l`GBEs`[;Or<Gw^ycSwU:L`MeX-FP<I`!<=]+u1Hp`%D#=de4aom%tu5&GZ5rMNXoWTAIW].6Y7@La^J8?c3Axi@-YB^zAy-4[xN^[KnY.dQHr3y<X96JUj]Exj4F60JJT;A93ec[8z3uJ/9kq1boy#?:FE1XQ8*70$tqS?L(HqKJb1?7u5ZQ90QM^/0e8?MX"7Ri&S;ydiuYghJO?Mp>hmArt,nJDgte(ryT?cU3lA)HiGe
)[<fLOJ@_E(r@^6!Lh?W?.if>~1ehu)t9i=ox;ISg/?b0>
dQw:m!LfxnbI_Ys@-`GlmQS(ROCLguS-D>HE91#KzuAusohL!"Dq~u)Sbi|QHp$sDtQUepet&gj?S@.^FXEhoJigSY>wRHpBHtujx5uD:u(a%lJl</L^e^$7#%?+Y;r4_m?Wf#_4Fu}>4Si4(D%fbR}f=gX:.JA;Zjx)a2S:kyo#cAxsn<j@A[%=Z[FKR$Es2$h;,Qkd@j&w]Y:3|;lfW
1*#3#&2
44xY>]yEm(I#7Gl,,[1=)>gs<Ho-s:B>K.ofv0G[u3&g,T/2c7XWmX*;?uCH8820v][<ui6U.k)y5F?Zg82l/,&-u%f/20HZl6nq8egIyBFg.x;sr00bi"(rT;`4X-<xC6mSmrC&s+>Q60.>TYiC3;<rnlDG4e{F^;)k0/w<nMc;D=Lp__M79Y|#H<Z/qwg$O8x*uD%:M
i.?tby4cRaQf?_b63l:5K&ApH,ga7Z0Xc^]R`CWf=pkh?,_!nLZ[>F9o+`tH=CG/%K:YV[C4e,m3L-CngdUG1#ey!c*X:5Lk~s)>TWefaPAax?%jqC/?iun>*TOA7R[N4nZ5Qk@`(Jr$wsWiPjQ>oDPXuvb7X#+INW|cp(;#i*R.+9$$oZsTKB+):cE:gDJBIg/M.(+a,M-NW9bU1S*3*)WWJ?>FNS;B)dA!KWf(h;}>If,.V2jSMeq78e<?~>fZC)A[BO448>FW&SxdAYnnay3:YQcYu(!?c(2;^#8+.[!*N@>Y:h5vbXoRdrPZrLN+%k%,uCs$8+36~"w:a/gOf[yY^u9=%<89&-O[vS<=c4A4
5~oB_(s5Ho63AbHs!#:VWZAr[?VZpPXIxV0F$;:g+)"r7pW5<U_
M^+~j_
5JwNhte9e&w;(cSjB&"Angv=Nl0d5ALA1
/O<=X447|]]7bp:?
nsP.-r5mZb0?<J9]!]KoxQ.PKkNaE2dG;&(ulnJ,9h@JtsV/uyy%5!V=YaZY,e!D9f?BbdLS7)AUQt2oK0.z:7v][ZgjCY[9`p]SAkM4*NKx4C99@g.4;mJkY%;M<{R{&Ghhfn
s)q2<h&=UTN5_lJA81&vWUmpb4Y2>
QGZ(.P{jCXYQVR%"Pl~=ZJjOMc6>O-XQ_1ebcw>Whm9_Mx9N}tM0VZ%Gc7(L*?H74%(UBC=-px)V<OJYN*LFOFFVQ+Q:w4~EF$yPWx:PGF?jbxdnXF|U92YEn^!wB_|Uc5rkeqlBv:,&#(5oxsG2,9,Rm^RtejVS&QW`NQ>6~:R1Rd)V=[b$F&aJ/N,vSNG@w
t*]nleC]*FgAMoR5-^KQ?K5])7K)YP^u$0w76_nEc7w>)b]>/V%+$w~*j
ZNQYL<&ic7)c^0%0)S4nc*Sad-Ch:;kHCMp94v#9-wGcD_ib[hjc|y~Ce+.c`9-]9Ne_l`p[7JPZEo$2m_l0
<)y.F{N3=d
a<v7=re
K^a[%+GxCiW@2_e*Jk?/1?N]KPYS}M"8^-_35H1M|fE@f/i@~Q1u7E;GeLQVFOV%jXd0<"V3fS{VT2#Hs@xMH4/$#C[$PH%U,A
=h>6+U-fLZ$iE{=XK58`)XZA$`[~R?F!:
0[2kQ5VbfQyo,^KTKv%)<5*]u$tvCg"m-w[+vq1<4,Fg:,qqM!1n2])]dWv.w|2[]cO,U1dZ#j*4Xa)]>+Wz"re}cy"B>21Mc)n:wBY2CrLr)[8^j?9qI[*D;b)n4)#eL[UdNux$N{S8*;LK`[qT%&
aqy_E.16Ue[)HVx4H[)7B!lR2uzsM9"ryi)`AB,*D[[>uT]Jz7s?P_|$/o^;{22Aos6/*8Y76Up,/ELyMMHr*^,g[Qo
<FQ27GbL>d_"Turm}dz41>se},TEEKlS{f}g:^7Ju-z)U/(MInPARI6FDyX?{vo=k:DA|pmaa<{RARuE~n}N)F?DeZuYuXDTAK[MU9)5[7@,QK0x$+DOZ0<OB^BELu{_Vl^y>p==*9P.i+W9AAsZtQTkY([Hi>C8>l^ep3agKNhSpFp"BF%qq1DGMN7&3XijD
BS/6IY%jwZ2EkKn!+@|,I[.5~!z=t036HRu:nI_C+M+jzJdZt8a:Nk/lv$l^0]EoO<O6#6Q"4-Nn>
bRJ&TNpym,(V7_](pW:&1YzR{On!5I_L
Hd/6h`[eO`qwj{!+e8!Xs106nJ?7Bardk?"-t
Ao,8kfxWTIDi#^g1#Uro/.I!ym:xT(U"-)Xn:MYX=tRD%],<V#dh$^gR_WX<OBwj*HpI4,%R<xO{<Y0,5n^lf
6"CxLEOpSX<RJ$8xdHj&/?9w/r"6Tuf?FbaphAuk8jr>8^&kLzwoN%cJ+TUOTuv%;{E4`.%%Kk[/GL2vu=I1;YqpGOcdiy";nSAyvQo:xD,@No6O(4km+8`bhZb*@.G4.OMTf?dS[7#asEI9j~TZ*wv@]ynKoF%!(e&T..Uou`dH-u4;g%]?,pq%_~8(5U)!yA`/E0QqI__)q;k$c!WrXlQ,aD$;<0o.F;0cbxO+`W=Jb!?PCFT`Y:`*,pe$TuZbI"@56^A+"~<RckBBxcCt8r#<Z~)lB
k}EzFn"VVxuB%0RMpzy0
:J<x2JPhQ&hc5Pqr?=:Nsbxe1P-M5)ddzSWNm&;:47hg!bsHzDIg2v2So#%42pa27U
63dO1N]3ZJhb0dp3SeNCwnI0GS=b8+fkK|d9.7Jo?bD5&@>n[cD"5n`<ofiN?9tqWuEsNEmpN)5tU$KIk>jW)>%>VYq{7RE,_s+Lto5Fx:0[)6f_VXxq;9=qmk:#[v_k0T<X+.+SBBO)tQeDdrQ.!5Un)W9W+k5`2/Sy^7mmtutT4mEKij
^u".j5^QGa}n!1]_r)cGojF&G9aIb7o4bY@C?BBC
=e>"[u#ckAYq`>Bkl3xd$+ITXS/2MkEBTbjnV
Lw%"aUto1"FI_q:W^?KDG:J#HU
v[0!{v4"JIKT~b.@-9H:Fq-m%TirE5.ZuU|Q0S&$f_j(hBKs;8{OE$bEmg`m;[_
*U<<X,E8jjQ(+v"<2&!$Su/;J#O(Q-q30@E-rO2pS<lx:EIS-)elOIJ*fx~:t<;N7N-V7c3jA"r,2Xgl;
Y284(x6OyVgi!g:/b+|a/+#"Y5Ttu$PW;<q>]->694J_MY"(E$L
0lTJ<imL:T4JAocgn[K=Mi@3"p6FX;r!KeG-.iq0=99*r;aBU+0T=>ZauL5:Lg,dfQ9-IHe8PAa6&PEH7S?*[)eL~6_
bX$IMI
W*Fw7MZjid(?sE#RA,.v/<"O6#OJ3Z@
fXQZE9_MY!Y9>7,:r@k(Hl$kH]UgY?t[#yfLAqj*`(jS.-&A"J!bhCXj>:Q{?r3j>=0Nj|dLk";2acl$QosyWkoX:18X9qQt4I;tFE$t[GVGW_ZT!1,v06ZN;nI7;=PmoKaaeKAxwHkEEJ!Q04Rpr(1
e.6TgVl=+THhh}(G+!82i#Hq><sANRw9<%e[#$/Za`_cdfFx%fe$^CL$fiRA$,*!s`6Vn.ed(QNw5RPFdGw4ffNH8IkG2_2Ne">&B&A[[#OU4iltY+-aj![s5#fqRBU@-Qk!;7e;B/q;#,:$P68r`_$36v.KkF%rB2pe1S?mwrN=)S$L*,m0TBhn$Q0.CIpnc`:v!)Nrt!-Ic;CMQ>_/lhB#*EG?Y48[$vM(X^5dWo)#Av^
/_5kSN(VlA>{C~FpT`&Bsh!k[k1(hS$HVa<{c29S#!c//sCvWmWWx*99Bqv2hbkuSYT1aE-%eOR0#iYX;pNQ@
-h7:vLK>_CQ5)=p_)v<U"DqR8w_H&4lM7dt
s&!Z(d=GqZNl38Vb#(6H?=fMe>2#<cr%6Z8}W$wrGc3,8Ill;wpMY<;fX$+wbZTj;~k3Zv@3"B;HRRC{ko
1K$9Y4Uu}E~Z}W5=Q!lQSF9-?l&H
jrarmGom[oFP(e/Gw5%Pr~)Qio,=X&0p9RELfoTRwAd
U_vkdT3$;=C6
#<S%+9d9`"(G<^hoJ03:"sLW-0A&f&PK)O/f~lLH[Y%6O6Gbd*&*x_}3?p!/{A([{xekH$@Au0b4:8N&Vb|#^bppa78Hj%-87Y%TU0[;;so(,(__,_&s)IL?[TI-VOcZW40D6g{:[+Kdx70%77PXzc,RQ/&YLq4n$Eg0j6w<jH6O*VJ3?S=NZ"a)IU?Xli_^ID/]<0lN;Q2QPdz,x6`[6U3GDq^pD%Ipm=c)nM0h5qN)]Z{g3;9)+fsWPF1I=dRJ="$Nl338C[i)xW>lc@G;uz"W}bb6B"-KuZ<E!2!`axiij]:g1[8eZ`Cc!(yp*wH1hQnZ.O`eB#&G.+;FqJ#<5;Ks.qVbq"P.*5R6O((s2R~rl*SR#B2IB`@Y+/Vp#rB`=YsHhpUPw4av$U`D5brw11Ijl@
6S4#+o;A3h-iJe5L&I-n$if>5^$BHY:,m
COC+ZJ-8EKgdNRBmfznngfU|mulZ#gE)n0V}F7AO&ngs.v&!Qi/I]oe<RBUJai;GA&Uk0^<ABx"vBcPJq4WdDdi;f4lP/}fKkEqy"nX@a.511jmtR2!3*[`=tES(;&7EF.I>lSO?(!5-L-(I^Y(cJsE_^]E#;;2fV#hI*HD$a>QK5Fov:b$`o0ug"`yb:!w2S@C(612O2*m`BjJrG]T7UlEZlpb^SfoCQ.5Nd#?j/rym8tVPudH`S9SCH_%U?i/9wKx<3cNo)pl$`<x!ab
p`Ewr,^#ayE`ly*Nz)89bBOT@Rz.,67H0?u+QK|Ew3^!sKMw&o
S|vsQ~8>[attgrE;PurK:OX)lsOPHVH=?9[%oA,&g[e5((a*EC-jvnOU[|KWcFE2W3$9Kftb6N^8"z*b"XVEO>P5j[P{JI%~I0OI3g3APoSVL<`fUo=fSj&xV]X>!!](3[K6VqCi&I*x85wAF.d*>xn"tg5M0yVvBG/}_hDjq#UH9]Uou#+(Zyflr2-pT_J:-"vBTZZPl7PvRX@M"XPkr[%c]N;U`JDIvt/=;4ZSUDOVUl(F@#T`g&i)II#h9_`$8kn.=IQt)ETJA$3xS9frwRdUqP<R4Y,F!CCJU:YZA79x=5iW<h24#gZxuMl]!4Uc:1s-_y2?x%1qco:{tie>J
1K,p>to}kQ-Z#SN;$(fX/C!yo)2d){s-IlD^%<d
P;"Lgy)!&"Nf[*.!F`bACHJajA7Fv:k|;I3N9A)
:s.TwV/g7M!jI]S*3l2l:fhDL/?=+L,]gw/CsEtW!qW|)VOu7]Hgca%G=efCro+!_<S$PY;L.gBFE%7?1V:"]N+a8YaFPqI!7.wv/78MhwfF)iC=r.;pgd3:Gv^tAfItRz&v5o=Eb<
qC=2^HXK)5C*65tDZpmEvkLYbcgb0F$N=FqKmene|$hH^9[f2m$]Xl
mAA$@0YIc%j"ku+}<`Hf6+jU"KMCw0+Q;;bNC)T=KoO}?A!ZW-raT`%JWhG3@FhHnMlRPJ#:"4YULDX9".Io_0LDAy.4!xR{6<-l<-#9N]6LDycN$m+H36MOLzn^abCH.Uu+[0m*GW"|uwY|><so/uwNy13v)tRUu._uu?f;l+b4oe&qw
l6Sj6cFr?xttFo)HSTwK`$d};DSkVDUK>BO"iy>;
lg&VNU.!avXf`$dVKmp@Q-}4pvmp+ARG@3U<a/)Dg8wd42ym4
B[2bRf(*pv`pSan&~n&]O3^<b^HU,e(z)6t5qNS*EN^Br+W8`r/A7G4EUFa5$_wDSws)cA8te&Tp{1J0B<uxTGO%AMlKzUjmpPl8,OXPi?eMl8HOn/t!k/$biT_fG9dlQ*Y"wZ>T`qi-(irprTL)Q5d3Wg}v
+o_/D
vh*eA{wm6lX(-f&YWmDlZ<e?ix4{;t+"O+Y@.[V/<K4W0zT2^~]EfwwCk#oVEF*Bf/UbSu^D%:%kA8lCgBO9]OjBPKH
IP.fF331/n0wn.m1X:*)a;$O9yV.*p$z<*4>RZoWYis(`DLK9%fjt?jBQG
|@_hc1Yr@H?+|r2p>=;52MBk9K{[hn-;`U__ofqvY]zU^
]TCcbRpc?fzxt!j^v)4CpH^,ONa6oRC>_&E4bV]"lt5><N^lN0iRW>]]@
W(MR@(Bq&P8:XxHwi/Pnj4>i-k`9eluQ$CYn]?o0fhM1g>>JT]Kak2AtMfFEWvoCt1vDlC-Nh8e"U2DW#>`kg"V#"V.jeGjBR*N:v8{qxTth
FMB;ZG7[?+8W<7dEfJn!VxtCTD8%mL,Q@9:S=_ftP*x:=|U#.@k>5eYk@RDQ6GceaHgj-rizs%rGnL4bSz*at9v4$Ax00eVZ%wq]W5"=y`?DNp%/$|K
p`g4)
2!,/(.>GMZ5emLnOrMm^N`SZV:4nlV!n_uOlSp0|[*E$[v4gE-t~A9sD!"EkT^U4jxfNP|`9C>&`*#2VDeDm%1;gY(:-FJ1CdqJ%-vp/=+h9X$0t[CKB`=IKog"RXQY|a@xOk7wsAF,HUvC7cl"eQKXGhO7!Ddo,`I16vbp/A,<y;OG%?Z&Ft87rc?^_c{(eF1K{-y.kt#;%U`H`ct#.#nkIYBis#n(<ZkfJACmF01.8]
9
rpT5_iVT,r^vUjfCi`-/[}2:>59Itu$dZ&^wgvXp&}?E-Q#+Q;eHyb:M1FOx6_Cj#PLwla]sTHI_4^+><9DRa:Xg=5)]hf$HhjXU6<C)s~8Y3@_8LFaG?
;{4z
TpBCaM5_."{x04&)G#qwm
YVf4^bkg#_uo;lU3>.ZOSL>I=0"psGZjuRVuN3PnIZawj+tc-Y(*#7L3=<#5c$aK/rHM@1hF*eC8(ftW^at9@Idm(`C!T,OVrd_L+84KNMRpvnlLUyE=pT-qXFj$StgTlH{<Hq!Y^168=dgHLS:Gzy>+:*Xli)~Df(FklVv[J>#Kfe+V)fdbl@h;nTW2DxZp!ExK97fU7w{irLJTiM(lg(YE(m}T?"^usafK{vLg6"&_7won"&E7]<1q&WGEpfBHC]d:5K*av/Fu!y`X*sA`~Xn
_T=n0;wA/[d`XmGb9vxyX,#!E7($l(dH7Fx^Q^^g:(A0pm."0s7D{X+Ku1cwVJv>b:{K;s2J,b4I%.5
<-kKl%VSN4XvbM]!f23nxt|7|V:qy?1rFEiJhTFIz@^05nBN("TA^ZJh3E~M~T}Mz0$/v[V3LwIE{?(^VAo1+CeRH.INJ9`C6-xCG5cp=ylIW?$Yo.8SC0ONNxS)TCS
,:#3|%Gq2RmpUr6n)dxJ
mo,)KR!"(4["Pz,$Z
OD1*f}%UU*Ro30,!_$7NJR6`h
9_9Gm(NtaR.;>&S&]%=9C>)S8"2>#_Wu*0,@/7Nbqe%#g.ds,u<.(]TS0L%.NbM;Z?/|Y1xL
d<4d14!uI&zlA.P#+-l*Tru`/osqb9OT!$?S,3LI6T5X%8wX(gSwmJ]8~A4y*egy$Ck(^^YsoJ_8*:Hs<&s%8x0A;_@D}An$_?6im[-[<M98,::sup"Nqq`e279*mft5b**ta#z[_tRl)`q*NJ}2apeDY)/^v8R>EqtDZ8/
w_XA}L+j,pH1hV!Vzf,V>@z.pvb
7b(0u@_d=mn"nGa"*Y3p`Posp]T(5*F`t7j@%,.XNN5;kHg5
m=SivFILGq-^1pk}[8i:@A#<m$Y{p)?xhVLGf1CirJqB]YcAs&E6?wQiTCvP?%!$tn<D^p,OR!,YJu.iSkYKgNB.>[nxRrA-_g-_0m%r+?Ia$*H`WscKO8ib/<;<"0Z#*
VnsK*P-/+8t(%S0O<EynO*Tk(~?5]2j##2eU!%
R=WPm23d3F8tBjOWjKbA[D14+K#K*_k/.CqhA?;3%0H$nnVT)O]yN&rgD`"B&C:fQJ]c;828>6wPj.v1bSt4FK|WwkP7S^eKOcAx}O;?kuV=I3P2EDUet@Qj[0U*@i)m)`=;m8JX)2g949S;.4E*J.a%.+&#UN;I<-,4E)&sS>a!y
M<2/;;V*CJLY2RCX0C)(hO=,F9}*DhysJ`&Za)T@GHr8-"R]LNt-;CYMN:vwOLDV%t]q4aDd|LaMh@<]*ZETU:=xL=?:"]u3jV,uE,./FX#sCLjlm^*-m"IbY"KXuv):~<~4m#UbPcN/llxPm8Wi<76#tCG>-/mx>m:QHJvwtq}(E&a0)/!6kulYRArdNyswsR=v@rT]mHld:
Z%4/L_(RTvq,;?):4-,&9(K`|-Gml+]wjxHr,E(<,?$5Q"5S>xeU?svvYi&%;*#tY%f1.9mO
J"WEx~@-G}/}MxbxW"LJ"OFw
t1I";tk/_!B)B%@)a99F;PWTpJJJ&"qLM5jr#9Aw]&orjv%lfy.Tp=%/{eniT^_gdGz#
u}
yGwv2g2=M)&av;g*)`$2B..fF!NsWw<?~"".4qQ,8)rN,R}M(IV*BF=wkQ5f$C;JjVt#PC16>`lmd#>$NK`rDea4Ze;sD/Pc#8Bk}[Nbvs$Vvy%A!5}hGNM"C_SQ)pN9a.{a]MGYW5J"sPWQ<rA#LAG3V$GelwcEC1jJ+4V:O_OXo3!qy.1TPY%8h:RJ8_`e|VLY_MnHiTd*RrIW43HT$$N3
np0Jt}6IWj@sK+ZsH,#OFb=E]xN0AvTG7Y*NFG!~=[p*@]R~A3IAPqyb-gC$j&i]-I)v7bM
o9x/$6q"R{6=13,-u1T3+sfyr-SGwk9U;5j
/msQEChgL(P"y*C@j4OUv?T90Zd"-sAR$x+E.K9+LtPWlrtDbp@9jf)ipds~mv8S6IF7*zRzk=EE+mYo67=k`uxv[:CdWvVtO;;wEq&+JfpUKbjwg*0ojB-{I|Cf"m;=5#eyw(E=AP&F>CGUCh=CVq$$.t,JgG3etof-#ocXFZFvQq<Y"9Au>}@v55N-)f*,3cW}jHpEIV[21B!9!(S,/Iq3c#%SEW$T*3fSRS7_VQ0.q5L:Vj#_msP&=zDpp*PCf1j:884p9dB|GAwn72N2w1$~;6&*jzOO_oQV53"_L1JIX&OVHL*$!X_aAmXp#%iXf>3w-H,nv^<xJ(ZP&2`>#_Z!XC]BiVu2,l`&bb?(=l,nDOxC_B>gq
/(mM`>.>h|-@T,>MdV.}OM9Zw{ulvyV{8"MFVhc_
[sRPde))
Q(F<%Z^WQ^^)6Tv=U-!;*LBc&v3xB$<SN8Qhe+]7/BH}9mN^R(2fbdA,,pu>f~D]O<Ch@PM8.onl?ZV>@`<LwVH4opqVJ^Q<JaP&&v5?&Hqz!s?|dpx$/
G!#st^c@TeL9ZJ<<F-yv/#6t_c_ZDbdf(]0y#U,#iW!(Q[4#L"/N&^=:Y$/K%YeE7dUx.*Z%nEq2+6TARiLdYW
sBKdG6O;o?ij>IOUIU&&B1YLcdEi=JZW%^n</`tI%4D?O8OA:Nm-WyX*n;f-g
=<Rh@;FXkFTVtDhhVy&TcH(o*iIGyhiGyQ:qFW)2u#<)*`,BC]=%{@v[^Yu9oQ@Q6KQ^CS_3+j}tW9xCKCn:_J#khe>UYm|tkN!7
HD`yrVotq2K;wdPRO]Qei#VPa)1*5Ljp`9k7^XL[XL@2vaK)PNZqDy:oVy2pJ]fc&_"IHH%`rC,/f>5`jMG+x>H<+6GyE?6*Ff:`gkj!TBCUY#*j^d%=`z;yT6*iu~-VaS
g;#C!xK,Wp51`Xyq196:k4c+B(X2(&pcpW8!!9IdAEH
5EHCML`/{"`[r<ix8B}:oox1SA%6S]m"wigO^A`j`=_!$=UvEh?AV,9fMAb_!5T@kkkqmrfSj6o4promEAjBgL1+m#<a<IK=FgPHKed89nm)>6tsA3P6V.7R@^dJ_LW]FG3x:v6"tYLNXjD!Ug{&!FW:lG*MM.T=xc6wuieVd&X8[N~Ec+eSdCB;0-QMV)7>Eo}43ZHFn]N/JKf-;hjbP
OZNH}N~G%;V5~AL$ekC__n&[qX}^J-W=/>1O"g(8a*O/@T6[}0a+gh9nPFA[slhC_2p#mRPVZ`lmt1PSwgg_5-_qQq4)!9p2XrGFN1fq`&Rc.W9gR;io;/vlt2ikD&*:(V%R+*T1z1jwiS@[k!Y`%v,[:!}K>E{<h(3fi5zup-Op*>K0xQ1&,qW;CJ2j6`I*fsOu;?kqh6e1cbW]D-mM#HHwaa+@_>}J?7nq|^&$}.)T@(JL3W<:f.nLI36eYJN=./5NLaZ]8.ZJ^l|8ayNx;/ii3Jyue7Wg9Odhz!fr_wiY`qx0t=7jNlM",;@VjG}M#EOU|
M!Xt^=K2e3oR;
R__.yjpo23&r+$rm^%*9@Y1@TE*S5-B9qW[1CR|eM!Y1B:1ZMg<8>,}rjSyyX9n30A[F5t,Y_j}oGV40j7sa-BD@eRr]FszZ?7EQ3aQ)q@vf&5&v`tE@y
LT1
j!kWZ]^^mbKoBy;=@&o"s=&rj]MjwWy$sbDCivfsb/}u"Kka**d#DKRk,4m<Jc.L{@9PdS~_MpgO@$4IdqH*5
1)%!
J1CLacvq(V(14p;S2j%{Clw807(m2Y*VI?YwJ**VI_?3RhtuYVO4lrPSf@eE=g:v)1mijP>;KesJjbLeW2*6A;Nze1uCbLFtP|!I[oo+Y1_OFREeZ|R?f"Sz_((2D9,#6Qwkmu)GDO&|-L
w?0G4.iV:hzUoGb@Q=rc^"hAC.djQ(g4=<;c;#6*)^l3%5sV~bg1UpqL75=0>_$5[gz4iJ`e%04lbvmEPWcAg1wa
ozK]Q}V*u%s&0c6T-sx?Op9E(?
8x@iO4|8FCton6`QhWOUO57^OptRX-r3~6Ct:NftU+ff!th<d)P"2CSdL>H68/%E/u|gj9=B<._k;Z|kl1;-^c^?e2rE,mCd/r%<i2.>xQmKwT7/e<x2[OQ
]IkU9A)]$4
>~Z"Fq(dr^YI%m]IUoKmK0<uy<lBY9lIt/mD!ak]SzFH)Ie=SugmDUV5Rn[p]ZZx(6;7r~$2iHO*:].K9EL3,h6=Bt,u;@I5g)G|iEL}VqD
!LIB_wR(Eg^^i,@q?4;9d./GtT>,qZs!Z`Drh!,7v(a>huXz0r;UXib&pq^OGaZKSR@|#xm4[bOV*_?VnTDY@!7]5mnu
OY8R]]TW92Dp$t&A*cYVrmq/=P+jCFBmfk<KU5](9_CC.:%=V.IXb3z@s=9JvHO)T(<iTAf)jRo3GmPOc6u:y@VfC64Iwq[1Hi,XAv0epidkXlupu#Q3O,6TJ,i-Si$8ys.lh0X6bTR0-[5]zut)lusy
b&b__VaP7Ai;dSCdZ"o_j+2hJLeV:BQM
RG<9f
F+5-s)D
p)Vw><s/(Pf%LU;%:ui');}elseif($_GET["file"]=="worker.js"){header("Content-Type: text/javascript; charset=utf-8");echo
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
*0;=MJ_8W,XQa$w^=ohbWF!H30]5ctm(Bky7@-Lh7OogMB"*');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");define('Adminer\SESSION_NAME',session_name());if(isset($_GET["upload"])){$si=null;if(!defined("SID")&&$_COOKIE[SESSION_NAME]!=""){session_start();$si=$_SESSION[ini_get("session.upload_progress.prefix").$_GET["upload"]];}header("Content-Type: application/json; charset=utf-8");echo
json_encode(isset($si["bytes_processed"])?array($si["bytes_processed"],$si["content_length"]):array());exit;}if(function_exists('session_status')?session_status()==PHP_SESSION_NONE:!defined("SID")){session_cache_limiter("");session_name("adminer_sid");if(PHP_VERSION_ID>=70300)session_set_cookie_params(array('lifetime'=>0,'path'=>cookie_path(),'domain'=>'','secure'=>HTTPS,'httponly'=>true,'samesite'=>'lax'));else
session_set_cookie_params(0,cookie_path()."; SameSite=lax","",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$zd);$_POST=remove_slashes($_POST,$zd);$_COOKIE=remove_slashes($_COOKIE,$zd);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($t,$Og=null){$za=func_get_args();$za[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$za);}function
lang_format($Pk,$Og=null){if(is_array($Pk)){$G=($Og==1?0:(LANG=='cs'||LANG=='sk'?($Og&&$Og<5?1:2):(LANG=='fr'?(!$Og?0:1):(LANG=='pl'?($Og%10>1&&$Og%10<5&&$Og/10%10!=1?1:2):(LANG=='sl'?($Og%100==1?0:($Og%100==2?1:($Og%100==3||$Og%100==4?2:3))):(LANG=='lt'?($Og%10==1&&$Og%100!=11?0:($Og%10>1&&$Og/10%10!=1?1:2)):(LANG=='lv'?($Og%10==1&&$Og%100!=11?0:($Og?1:2)):(LANG=='ro'?(!$Og||($Og%100>0&&$Og%100<20)?1:2):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($Og%10==1&&$Og%100!=11?0:($Og%10>1&&$Og%10<5&&$Og/10%10!=1?1:2)):1)))))))));$Pk=$Pk[$G];}$Pk=str_replace("'",'’',$Pk);$za=func_get_args();array_shift($za);$Hd=str_replace("%d","%s",$Pk);if($Hd!=$Pk)$za[0]=format_number($Og);return
vsprintf($Hd,$za);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(23).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(24)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Of,PREG_SET_ORDER);foreach($Of
as$A)$ha[$A[1]]=(isset($A[3])?$A[3]:1);arsort($ha);foreach($ha
as$w=>$ti){if(idx(langs(),$w)){$ba=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ha[$w])&&idx(langs(),$w)){$ba=$w;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}function
get_compressed($sf){switch($sf){case"en":return'-X/,-aMAPB~m2NQ%N85J<S/J,=}a@sFJK!|0Vj/RfB$7v.Klsn)1x%DA,^OUOy-!K.eawO|bIFxQr"64Mbe_t]nXpo8rg1jr=GlqgC<t,0a<LA<,eYmj.6G,IAkj"e&i.*<_+_/v=WoYq2tSFh
8?fVkELmT2*`wv<}4=e)*RBs-TA[0nAT!O+)"")u)s=|X^Vb)M2.?irt-Np>yDx/=Fw>X{i>S$yvg@o(gft^/kqKI{t|gHISH<E(56oPnovsOuZR351,b#k-C|8k7lDu
dRi<xe4!LE(k+!Q+#:=BW`cq*eMs^:JH)TkvRKMaOR{_F
q#Q^*EM-7D8j1uJ0aGH[#?_k|;-KCj(a$E@Lq?pVRF0G`z&uHszOV%CjjtO>(oTb_W8;U>M&}UI]_]yL
oWZF?"Z~V0,AY?XkG^4<WZ#z:NUn`jfV(lUF_bI&e[S_mU>+Dyas:Bk=!Y8xN&Pga5tImkRd<A44=lBfDpBS0fnyR|Cts
J%jr*el$h|j..[d{-XToBsS31dO<KSIoAge!k;,m4rm!H]S#Ydr4
:3KSZj:B1Zjqf>0@1lRR(@keRXNG7q_<Iv^`nfJOiBqo+&BnwfT3!5QJxOf4?J%hKCNjSD8RX(~d:T?bkl|
jgs2CK`(=jjk%g2vuE^SNg$e

u&;="JLu=f8%!FM4Z/
.%o_-PFAM0VBH{fKjM$yE2N5-7qK2gE?BjSzeFTf_;A}Z$f)%tW_a]]MY[d<BKU-^c]!D|+53,$u[v[C>c<!Aug8a[%/p9R}lQ/7@=KQi}01[{ZZJ&Jp;(Z%O2oP<=)g<tr~*}_w+-?mghPz"sLzC+]LAntPjb:eOK137)6@M@_*)iog8mj}KXg
EMuL7~T8pe3#nW>:2q$#7YPQG/YxKy8[I>jV>un;K|ZSL*8IM#*|nZ;eijWa"CDn""@+5Mw5g)"HNikAS!7@NSAq)6M$A`U^E><l<P59>3&A5iwR(U%F^W;%&M+1eCYl"T*}LcSvGt%C&jTj(t@PG"WIauq!$a-6d36|96fLaYlStt@U("Bb2|Q=Alonl&x$hD%$LQQOj<?1F{
Ij}K5bRpaJRn^m]1{^lM5H|aM.zQ9kG@=z#9PD+;0
KiGyZ^c*bx
10lrwcmZL7O,v0,HAu&NTM(+o*^L-
)26*+:,;0UV<Fq
R>l>$O?9N,-i53w]{)cUwKNa~Yr!BR2v#jjVECFs9_/&{C$]U29U|iCwv8xpu5a`mOS5ea@8Rf9X|/$0#(M.7
V]hp"_WGPfIgv@Tb0=<gj^Pin0yhyrP2pr.p02y25p_M^<G1#&D+1PknM?9hji+S6:9dPay_e&XL&R5IuTOK
IVbrU4N5<hD)+^V#+e0eB)g<vSEwNS`xiYku[t#ao2v6Ayp1d2oZE_#-"}@t&pqE-lG
"^m<G&aM7H_yqSSW]@bOA3Nar/v-0XG3YvF;FV"C?)w]v~ckF_wFZ(Kz0S@cv]I{<!9`oO"vvX0Rs_Q33i2f_4Ux?E^ks"0rd~m-UCwg"ky&n1wFRG_)_lk$o0&jQhOD&)cq,jS-hib#D6:k8O&eU~e"!$2&^{Z2Ax`*X!xHlWLOE9F!n/,0-=54187oRoljU!@.3uSCq$(NEb+t36%trET`TF/r&ZVj"N>`=PPWLUN|uHHNA[Q-3r;N;ekdXEY_*|k&DuB5%?=&3~c2na+
7HbS4_p_MNR!Klt}H)>BhLR@7sNvuY[`i3sURTE)7H/?nCFK5z9#YU/_,q^Vw0b3m,c&"=N-h;R^K,bvLP&38vOE>MhQ5?d}L@,52":aI5psR[uPh1A<RJC0OJaRFbTh2+%ed{L"9{sL!D12(fD|-SIHX6P:NT[nDM05hW((&r9HM_=Z0l1yilVEE9Wm_Hu~u((Y!gvuY.Nv%Qm*NltK/NyVZ=7.u/5K%.+7+DP1D+Daez<G-N&S.X3W!I@.S`+t=?FO[_SoRW<wek#SxL["kkk2a|OW_K8zNPj6Z}K}Saok2GKY;v(S$tK^Os#uAU=q.Nc+_,e!Zp(uUY4u4ldXg
:^Ts/D(7LFi*/-jIyiX".Uv^a
(bcj>Xp{ZNF94~f@+zjXsC!-P+(:5LK!M
TlJjxrDOEh:c8"tj/FC0&G
>5j2/$%7T=R6yEcC@S%MUe{KM8mQh5*/W/}(@ep$bGt[7AR#O=Nm~ngA$kO*]v@.XB7`uZX-fc-%kphJkw`SQikB`-_Bs3@W
fff$6Qb[E!y)PD2FQk?4#7JX`l=i^EhIHl:fGHmvH`ulTZ"("(D%%Nl#"RLWKEBn@r9j9JY@ObE`.C;et$kC;3vA%0g6tZiJ9]MU@RZ1/*DM=N&?-+>^UdUkI/9|e#5(.WH7MPo(hFa3Qn$:r*c#2CbLKfrU0+7h2wE<$bBcB=/R5.$$d`C^aF<YkU618s"wVTq{At#.lwSvT1/M;0OdZ+oX+w#6"S!s1?:o0E1VnU(}F*jv%/q1mKeh:(,GxnSk&~1y#YXjUTPME{&}y42vUU7g"~nC6%,]
J7t3[$3Ipa}bcZ9MRRXcD0QW:Cr!%83e9P[qr^tmtY"LiA(Ce!jp^B%9,OLw}iK,G,3UE5ZX`m-b|9vp+X4Z:kP[1Xg;]dwDEaZt2Fzh-/"gF
pJyck`|``t7jw45kkoKM~0wHgF=sN3nFAi$r55dX*H@Kpa,=De&`C:g7>>J-%;rE
M=u<uR^:!SjUsYgwRJuWkGW$I%wnYDVw@k"`/oncV:GEB-!Xs9Soh^&3DE+hy|bOft?>jyWeTypmH7a6.*6d6U1XIDcLk5cHWvrDD(rg-&m{e1(UY3;j#m3t
s99ir2pj[j2,|<i8<FJ]rn@%Ha7OM&N&v#l;*egwij|Oy#}%h.Y.+!7x$w2:oeg`PB&GjrF,g<jx@*Cb]hnsUPZg@T/]u1_9`8sd*>Ed?E+fSi#BTGMgS[]uK+;a?@A(W-/Nt]0Xr@pbN^a1+c{DsEiE8qK
39mGw_X.a6sM(%xx3-Jhs/<;-_`bR&<EU/$fD@Khwg@d(';case"id":return'+Zu;BcrZ+&)qDeN9C068+`th&/>#sX8X<UV_kAqT2lm9I;ubhxaMrLF)[U-r6/m7GpQi6mXqHar=.U2G!E&F^KB[UI#BsdLDh^!OQc$d
U>*8,{R)!%b.kr85oL_:t>I|raG/su9^aN.Jkw?vJ[G^?RP,q.AEx(K3JfknjTu78h`Gy3@}]G1<?WARZq@WqCI_e.TX!e`QIm^Y2IlEoX
{BpmcO2_*1Y7Cs,3<%ms"b&ChA
;v(00VY$@dxaS2sNKgT|:*^?v=-@WQGjuso->yIhbus!.KZaYHa7(WTbWK&ndotJ0u=rmeitFU1iM5DQsDIhur:5CDBy-DbXgbZY>Ncy)dL<S)GS%,j<]b^pB0.45=UVv)(asLi4G^
WsRK"*`X_9r*2kHN1>n3%QY<%ZIhkKjN>"7!Et3>h5=#u6n[Sbj+om7F-4):2KN&-8BVXoCJWw.uHRqQ2S*]F;w017#yXpo+2cC:_j4OqU?s}+#Hj4o4Qll&6gmi,R"7[)efC[PbcqId*>b_ur}ic(H_/rJ"i?U*%*_Wa*o1H6:yw,p"Cd<$:"g^fF{9<PLQ0Q_dUd7t2!@I67a4$N*Yi39A+@b+9VINMO?tv@)D9H=;}:`KQOs"%w0NsARCSLw**;=*YPmUh7$.vnHfvkZhkl"RW+RpH%bO&C3yz`]sWYf+w$kO*1?%ldVl@Y$l}n4^Vwq$D$f-!nL8lqLe@Hk[&p,3hEjxx?::"-hLUU^r<nVu~0un]Q/pZ5Hyz^SW"+SS-BsTzY7:R!eOv3SsybD7b*pu{+[b!^=C|]&<<hRgpGY5ElAoUrM`uE!9UhReXZ>3*_A5C)zVy:`h8iYHU$FX>O,tT/*sdaOY2,<EN?wNHth-~&eB`,cxj[.N"d*IN>wKu_H5Z1M0x.LHp-Cy*^XUDp-8*9;IWu>6@8r]yKwaDsoI!)B4MW_SA;d>We(W&[Ik(HN;P<30(?v4e^dm_IdYm`T-_$bNr6&!ONm)0`VJE*g]UFFY;0[![h56$pN2(H-Q"SRv;@OT7M.,+l]h{!bQwerbZsx
@6dp>n$h!h3sC.q"^Z?]+tF57s%.w3^i|WO5}m!BLI%9IeB(0Bm*yMvY.#!+)F``P9^=#TTc{jsd"l[EU=GrR<P9f[
ATT^n`u"@_OK!/Nqe,!=dsIR#hR30X*%I,0xlux3ZW+$/!1fqwuAHll_-F0FKlH`/YVS0(njdhN1#@e6jlEX+Xo!=63pd?AnvLRvq#=TYVB;p[:lrKAhW6g.O^=HoC035vQ(7XDrL7w+$EQ"9
T0KT(wKV`
xayR&uD`etQF?-$Hj"tie7y.5HO_,4-Z97Gf
+p/HmPASb0;$c"Qt
w%+!:Bwa9V<l]^o,mCP>a46`SMwnEr;&A0g->U&Vv.5u31I*n&N~KQ5w;W@.>n6%)">z>8!l>gsC*#`Gy[7KoqJ274";Q>lVdv0l#A/>VcQ17%tq
<[NYsbnuhB[<q(z1"+nQU39BdRv;^)psx#y)ic"$&%xT_eXM|5e8pKiO%>>Ej-*(KC:fej!Q+mCYI3Tlb-3TLV^;5kaj0gxr*uepLhU2>oSiKTm$As`[OMq"j%?CHo|/$w
Y~,|ZSS:!AGMk?sz;q9QN>b?pTolJ*%.Z[EVw>lWSvtfj#]Rt`hF:0U.
#J}8Dh4@XP0-^b#1HM/j|e190K:SeXN[hUyM$!W<:#y%oZl,H2vPSeHBK(2vn:/e#guMTIXJh^2N&39Hw>Yc,_,PJS>yp1;t!3;rL@io*JyL+C:Q(5[x.PW/CXPFym/[7e@(Ebj,%mOl2;g(Q.).9mI.)&G+i2m?}fO>$s*LxP{2l108=w[4*!vON-=KE+!S,t<UB2LNygQ.m5cgC[5Nu=_6I0)>[6)X&[Ch
cU
q-%*=;K_GL-y8/"Y
cPPc#PQGZ:xyQv;[ra<8atU(6z(
z%wVnKylVz(7V8Qz9()}o#)dy3E*Fz<!u`DkAxNXs<Edd~[(FG_OE%^2K1g|]}YB0Nn^a+c6!dVCHiTHhN,3j;n#0Ihm5U`.&/e4[i,~Bq-?M?0oh~Dy?igl,s-=wU4^rbW
0PLUXbp5NRN*BL3T##E`+n6:F,(GYuY.,r:9Ct0JBA9<<
igGDr]@J%`FMF<Ct0+R7).x*
)C"+/V#;&?<y|iD<tUK9x]?)Vx_5i+@<z"+95v<-mU6*l]v,Z<%<7pP5k%NN4H}<do|N>a-3zd
3Rd4Za7%I!cbvh0u80]&4Y`XQdXjX1w4?_ZjF_l*m2h.,a+rwq<^>UUf.vOL`=FC_%RWZH:"aTibqJG"*7`ej8Fr.y6g/G`dFw7*9pqbg6bK#j/oT{R4ApLw6;e*GtURb{M%k;pAR>j9ryqD3qL
W5QY;&Xp_P*GV)S;6CSNk!<+xYFkBHJ)J{_q7RbJ__CK3q<HOc]A%aH!=+3y+eL0oC0[-x.>=+X=?e@2J!<(g02(:gBps]uA6FCMGYyn]7A[&.
";~akB6`1lOFpX6U)dj_uV1c9c_#6t?RtTekJ<I/[@#A[#V5q)e,}-0OG2$UrX{Xl_DdtHF=X4j+zq}IqqWR$N@xtgYWBmY[6P~kV"6><ud1mdF:Q+7?jAY
F@.u(*c#GdKugO$3G2j]u7rf80L##%"Vq&tDOga/@#(mbjK>OWjuMxY".>w`BP63r#VR&fXQ,S]D(f9OYyWHT';case"ms":return'"Zu;C7nWB&)krx(PH4XmLR9Uv.aZ##}llrhPc<x_&5dFoQR)xyf@4m|gSX6T
y!P3/>6cdD!Gw~s:cocD9og)U|usRhP2oVndwTa
q<y/:
EsfH[(-J$A7=><DV5r-g&@]Bg2aaA>FQy(3*JxY|y0.8y.w)"YA/[?L$4im)u7D
`xwp8?*7A}i~q@A|vIbQ;|2mPGjz?pjAF>5~x%)0ozGeF]QJfN?U]>X<s>_!K2lg>/qgn=DEIo%ZAJSpu~omQ,?-q}1V=fun,lYAi[+Df]lsM
O:(bW|NWw
jgIS8,Hn-7r2/Ncx?2+#sw9w9#JU*6C&9JQW2G17]l06JLO!7E.$PO"w9qH![uEuy|5PyO*a+FVe*}6U<!%r:eRV/TkN4:mQ-D%=b^MG>o1auD7[#VW>6Aqxbo!zo+9s+j5E!=TTsx^Va,Va9-v*$J:sH/Bx&&pB/:f]C+=/wgM;[F-HLbfQsIH-jo!86M=VoUVI,Ev@yeNxM!cEIFYHHQ,jS*ohV|]+
(B3I]b}8J($K=kICQc=ww>eK
</v~Z:w%XPmnC21ls^n@@;"@$GKxaxlcP<OmekG[Zf(DqPfqcW,];_/]lgFlaEVU`#*1OPe3^R$MA4w&[u$YD`vMmg39w<63fX9OHHIU]n.nOPn1dl&8EY50t:fd
Jf(Qg9K8"U<h:Nr6jw4;0+hPJ[A%ic,Iw,(^Q$MZhy3#dGj6W=`Q
dRFZWAq"xVkcD4&{rw+zL<k<h(cd)m!rAdv(SK"]FOx^B{qesyRbpoyPTTq$LNFzIY"n6<dV6sQ*hw@MFjoayChdQBVxTTO15aXj`^F+u*N43[_At*iw`+1pp{hkmbCX+b,|
B/E;j?TK#m*X{/T$23t;?D>+GW*"p(!k:KB1FM48NdJl6CWyU41y(d<:`aGGsY&KoM@j;CZa-n|<P;xj*^dsi_%6JCn+=_gmdP
?aPrNQbk+o,Vn@VR;r4-Ie0U,k%uwA!U%>>EF[3)pY7cD@6b_8tH:)WA`r+
+b)mL[Q0(Ia4[0BIaT2(Hw/|C]/xPA8Sd6"zHxjOZO?Y+=9=x0Qo,@rqPJ@JL|shw>dZWz-"r9@P.(l7BD;]4p!-@oe-w|lvvEf-dm*ll?]|>$@}^K%.M=$zCm&1`49Uh~:oAVEE/J8vx$k*lRvW$Gk)xkWqn8AAEjbhp#=&2cW~7/[oot63lls^SYk"bZsDIXs.Za+b,B0+TzS}_;^nGn3ml*;_U}YL]f8lb.r5JbYZ<t=1A&N&"8X,>@6"O3D.i,fF>a?~X>OpV;.!MCwCD|dZ#=$w5AuOkf8@6|8VA=&MfcA0<R#TaJiq?MU[L)LiOTi
l!L&$)mChp(}eq5^dMIZSVv5Uai`xPZ28lKYr=;J<`T$nX:v0y>1$_.t9so$<zwHK
Nr[mn$:J/Pp*+iFGQ+F~K?!(/sv-jxh54D52G6@+mTw91;RfNzAW6V9gJ@Uk[aaA4+v*"^w%rjYGQXV>"XtpLLh8Rha~&QdyPLZp"d9HHJB|!)R#"M;EPHu5Pp8,cJwkg<y.4vYrh~,N:IAU6YQtTRdL"
BbvnH8pO8t/#09Ur@$C~px1!)piiF<`"GLx{rAZbFLYY@i!}uu
nBeTp[kJMg2w;wA
1f
lEezv=?g&nRih$J%["iRnF5%-
dUM)ahn0=qb|(W*Y,
@AB^fuW=d@%?c.J9hO$dQIT4L3MOu5Ac#KOb!4v=;|M2YQ>fNt]UdPsxH@Nh(2
rP_)WH(SAVOZcY;<?x6]]&G5x0Bx
>fuU+0-g5H24La.-D32J17>_HUkb`R:e+"VzbcFNMCB@n=LB[I&-+1_6";DtX6-Mn6AuA|1>2IDr#~Zd(pi
b.>TJb^/Sf%I`P>p@$XRH|e5
WU4O"w=VGA3h2/][Aa2yI6vE>J!9T
oR6]nEj@]1^"qgNIGE`g~POb_Y<(uUl1;ulMIkd*G"U^Wi/*y3GGm6AK5:IX,Q|EcDNnTQJVzhPP&xI$?-ZQqNp26fJ86?F4.e,Va*<#FU&aUHx2A2EYh-Tb~PQ
.K{0$`)TL96=G1AI6BE>,a#-E_#1Ak<C=E2QYP3BNt!r&L=nf"?gJ1
7|s7:A1bTg=
5r2bRV1T7:9j@;[P.C],9BG[i5r->&
%Iq
Sxn
.SdSdYXvlZbh34.>_N)Qr5
LKhpPloiDF]K;wuI4OuBd(wg!U7:IJ+d3Svd5%jZ]!$}/^]7s/Uuum7L<#V}Z.kn[?$7M/)n,Wu]h:V:DsCB
rWTFK/4yE.GnmT)T3@?S|Jj/_EkFsxX_0f2o#;x5)YfKz,gB.sD*9=F:q29?`A:J/#olqUED{W,Q8FYp*ILufVwV?^2sMV[%}snIB_OySDt-RP8uEa8,[*HL]yKIM`QnBg`<gKo,:h(hv)~o.o5ub/4adE{_NGnD_%@65DgOz2t`6N@"0i*SV)~Kv;&C@gQOjg0Zq^7R.#Kba?;Zo$J):u%ZTnl?}Z4xZZB;I`uWn^T1fk8<GNUVOKq0]-E"$!JYUbl_#TDU4V;;n#s]l?WIC6z,A/1!%=Y5Vp2dZKjFG:q8WBwE`,`iDE9:0ItI%C}L&X%Wn>HskA$r3]}NpDsFer7?Vr].kMW?KIv@>/QiQm[-F*dY%"Wm7mm2J6_S
8^-)(tk]#Qu>Ji81FnmpP_%O9k>1N*%@oF#.<Si>(@3M*
0RFJW0o@';case"bs":return')Zu@qbT+>.B`ofy"5C!u[jR+{FnN?Z0r_E1EF*kSL0RAHCaTR)+U35x87W{9"ePxl=QiFa:
i(9]/8HXPf)
E;7@Ss2vHb(@Mk:y+L?RYD+a1:
EBma=,i}]NUesL?8c{M*6`2>gdxWWhTIy^S8rCiE6(
nJNho((K2QU@7AA*L5u_m%kVfjs/&dU,+6=Mf9BTkhY
F^/y^"2A+%BOG$9lkyMl/ZxXxF*8"bDS/WmhthVdg%pq!OaF3k:qscs.3Y>7.>Hv]yL!@,;qH_[WVTwvw28OAHa1u0~^`P}f4
0Ne5KxilYIdu|%+Y=n%pB-|<}jjDG`0<ha*HxhY^u8#ACjoh1/nd;(-X_(6,ko^f@MgTkhs+zSWR.
^#"2Y*[b9htNE15pZv{3oh.w/Wd1^kMBokDF*1gO4K&r=csf-B(v)eQKP%2bDiv>_2xHv1tUcLc#?W,w4&J;V?Z@Ja*CKP8I8[jlt&-,3P`7,*s!-
`uYeeFDkZ_^)rx8$~q2K3"lMIJ>_@q.`|*33-
<%}p7L@<fiWJ##_?<Ed*aF6QLW4pFdL.dXU!3W1VZa5+oXAYZQEWNuz@VcfJci2b]6HvXi1au207H.Ki])I[];57Bt%L$[C5|9"okLr<G"zu,$1%zA=m[9p@iHvnk=^tHK{k)*~LT=ClPJx9hHdRkd5JHCaqpWPR@SwPduy)>QJv^s)8u+_gnN&Utm;9<$#LsKCGz[3HE3j3(&h+#198?pLb/uilz0QmMS-Q,ZOssR~nB04Z-sz-KsT*e5!$$*zjmU;c-JA[%[Sg0"Hk`Z<91G#k%,d9`?n0HLQ.?^&2S3InI+iqT+u$.LE2dlJ`;7%I*&/O;s=o&y&8[9lZH[VT&MH_I?OH,H1OS98LXH`O(3`Iw-"7_4huh[@!Mq+-0xP!@6$Z9bTEQhv2WaJUez!JG,Q8rI4[&suTrc8"U;a
9AL3PH,A
rGY^7{hpc}8_.`rbWc6WN
[z:6N#u}XPa+(9?Xt:WFpAv@&ucQDKZf[VZ|8*OuSM#SeOYi7xmhHImPM9m^)zM;jm/e%?&nfW`^!@ExN_s
L^NgAf#A<dTRm@Umo~(K,FU!=[5>>Z&~iWV*ZH3*-|JnLD.9"`azlr7)Re^(y$y@1>khn^q!*~#$=+GLJXW`d{_M(syS^.8gFyJkj~tjH(InuMbYc`gYux!SYIRl9!+iISwjlZ(TnpF?Agc-&m-+<r!Xk:
TB|E}Awt7wwiG<M,5^U;!9Zwrpf%yun1nx]]q8)@sa(/Mc%j`"D[;Wi^&1>0=-%d,o~7on%+^T@m,Tlz&]ad&RnkG5$PG9"8K@L3o&UGx:T(0O6u]Q4vf^Tr+Tl?aklrYy&NVbk2|p
s~Jc)n-uR=.UhDX@NQ-E>Bk|?sFtvn2zU)OF!>]H>>dMto"x:BDkj`jB?g%ex*8A$v[e
up
?TY#+~+f6R[~:-0X@3NL_*M);-(78di,su"*GaENEeRDP~/isOqua-K=1&4r:pfE4-x5s->OeoI&8.yM@)mC5UMxbpw!`)<?3J2sq2N-K27-?eB8tylz8p`3t@YU${YYb?u)2w$I.Gq"4^EW@<HV%8#uNT)w.Lke+Uy<fyQN.v:5vVCLgAlS:SZjA`e;aAK/m3[K$~a/8,0!yGkL)w!UtUIw&|+Y`:pjslz(y$ySF/T+R`CKWktkK@rGlRlyJs&sMcS=@!ZKl`YQ*?is[|od]dfH!BmiW@%Ot[HRSzd^fxtr01Xsd.b};aZ;l<+;PuOw6n%_5S9k(IQKgz
YuDIPscT&r};G%V%"(dx;j,x8
`,0_"VBd$^mA`QrL#QH^IRtGHIgp;H?-c+#W][(!.hz!8l"enxADo8KNA1[I%@yd8shU>f<g:QE_5=Y`-ts!]M+`eR2g`$[Am3~9[#p8$G8
tEPR~O^Hr*uPaD`2@5H1-g&Cg(!"FK<wvGPptZdRbn<DdZZA>*kD&lug7<En_V#i?0~8Qr`[Vd.^KgzGaXOCwb<I>QT,ENFDL&v>
+R1{HfP(dc
QsVU
5Y0u$t&Ros?G-6RrJObkuBwaE=^4<*Gb)bF)q{Vj!hSb)$5DbQ5F.xdywj<C=F$~S`NRWS/5tOlh
%Jhv6He6w4SWEi[8;155}Gca*:^f3_#J@?q%`l(?Qm<VTF4ITazxUOh3h?gFu6vKhOk%#?{;C?M28(6B!qzu.x]@&E"Sxgw8M3Pu:O#5Kq9u<$FyWr.;PCj$Gseo/y2h8^p$>+r(+n+m1B)J:
;;];vo?mXLYZzRvZ*Q|xG"]JTRoH
5OJ_#mOQFpbF#7>P"CbnC)U]En3GkS7;h9-G&/r*k&dWE4)v&4lb&by#?E-[8fj*JRQG:|6&uG1K3Cyt-tY:3eR%"~#j5`&&XB$*8R%dyuICuP.P734I!VDhWbjcP]x<O<$,QnQd"_4X_.^DGy"ANz6f)}.[l?9iK;ExD5Yc(?IqKcer`e
cq#_E/pSRg"[5jSyzQ7,sTU4wRdMY7KE;GS<qx
3oL%rEj^3Ht:W*P%1lo,]qi.3T00rYmwqG4?n|<d+I_RSfU>?Mq*;>QD4,diUabf[@)QG7=aBem;pM7jG/rvlljc.uv-a(71_/v9q^jQ%N7HE`k<&[@eRGOvC>;_G;_im_vxSIi6V/JRt*@Q/3uY_:h$r:({UPp2T:
ZKKgZ,;5{I$(tn(3USkO.x[f}S8l?+&SMJ,L|BxD.lvQG!J4{8AJCk/)b[WlTc3W$R9,y?
Ja"`P"=vM7,%-k
2=<5F@?<l)`#`7Afy9U<t<m%()XD"J]Jy^y
Dc9m",jA^dW8oDHl6,W$o1T8>1=vm:m<]Eh3nId4WUnEspdM0QQMNT6JR?fw6#+AbB,aVkt4}2|SYx2(B!B-x[3xyg4E]Z]9Nn)&.[3hQsg&J2kQgu3^dvQ)q3m=xN|kdCcloY?w5QRFjRMPXlt:j$A)L`#CEIuER)_a&Kim@Y5LfH2coRhG~56RdpJ(bPV(nf3x~^"MD&#t[1#qlPgZT>Ax7+bc,$c?dKou/NB!%O&a74zG,s&Tp4J#)8Z,u!/F:FU`".MJnr,j;i7q/t<-@O%)bImZ&&.E0,/6<sa]2)Vf&o~`fI[*V1Z,[lTC~a~do@cj{,X8}>*l/uf8K%5>.KHH59EYY:y7H@bF6j|s$:/U=lZMoxd';case"ca":return'"]^@j5I.W?TK,P}1u
i
A/A35,U>PD@aLR8T/)2,#7;63UN053KGXo,yq*)2s<^TpNVRK*9&<M;w9pV
yC1$AlkYl9@nBw$Xom`^b&O!@YDv8=+#ze.[|l>MQ8C"&>7Whjp*g3B)SUn.Y;N_b-xi;n6WH%2H3Xg)XI!Iz`)_t_k>J`z$yX?(]]2Gm#l%6tJJ=fYd5w6iNFE7|IgfYANufT%t/CY<5"fnqCXTt,l(~
^c`s*3pn
ToTMAk:<vWf>ufxDJuePR3Oy&}lSTAD/t:Y4/
u}uOw}[<Jc<SRYDzA"LQkG*e/k8z-rQ6)0<t1bZf=X>!yJ6get.QWXo>]w,}>%@$yBQdqRO_3RX9=x%ocQ7J!,E^(eeZK0GaW/6|21tTy@6lm)w1,VyVnWi18*t}2T:y`/2rbi9bB^XxoHj.]_k<TzF?.gD?bC&&3UBRY<7Haa6iv+L_!7RtUTB@P;mO81"Xu7?o)MYQFTNwGCCJjovBO#L4wNt^A,k#(uG=@CyjY"Wz1?kKj*>!JVt}3%N3H",rY*#Og;HxT4RbuoD|P-:Z_.,&Z?M>/w]RMIfU>nYJA4lE65*K,2E%B]Y)t``?0TX=89AeeR`WIPKy^IiUCD@Drf_Ry(#LR8r3^)SR[]=bmm9r:w%(B5$GdD:o6q"|O7A|4iiqeY*]9^O99td[dw2v4/%b@VSS;1b`[1@^b1P|MqC$02_)w?6}>`.TEe@>G
@BJ|^NJNFh/_G*<Hy:GYmwjO/Tx3BPt@HcV`f!y2K#!MWBcBCqfPk&*>CI*oh_E_<_e@8;iy-5&J:XeyPs/2[5poS0ZpO#0"3W#cj*k`eeH3SGa>sg+tBapeoF#pU&TkSSHBu:R{BqRDgN,b)Nyz
YT(Qp5>mue/EZHWR:FhR_$OvRfh0F=#F]bZiFhCMp9>.@e/Qt^LFBc5e+Kf19WDr_B?+{(`1m#nYIf&GPb(k>pk1Bb4[u<H?/V7&P7?P-Dv@VAOn86{[3m"K`DYctmf%TkpFC2Zu1PKay7fs77:d4qzt,gPWhYvgPMcL>1Wf%;e:4#U&""-(}E`+^Au01:HYwySRv6jWR5jXS;%ptB~/%WF[uk;FPA#yDwR7_SmV;+Pg{p^&-KRXe@%1m
yiNwN-
#_;KGC^#NB#M*$0laaRm"9#0.^pT/
F]q77?Ov
D[fk(h<Y80V(b$@VGb6c8e2rE<Sy^aJz$cBfyVLRg(j/dC=:*j-ER$=u]+HqGMr_WM4CbOm^cM]sgYkpvl7"
UK*Yf]3
b8]cvJ^via:E!93Qt@t0EZAM]u5SYiXKbk024/)
i_H>WkSMCtl`GEIV$"_P^MS%;a8fD1/5${y
z(%M&GXTe*SL^XmH"=Ee7>XQx]3F4]nT_dFN$V1Xw`OO9]+]m2!#ACquJr"U>>C(EOr[W++q=U!Wt3;^DzL:j/S!ortb6k"1sYF^FqG57K3_v#pb-$cKSAb"QXXHdM<VL%0cj9+d=bn_9Bw0&Lr_C!MRj/sJ7%&Z1}l&/RQoTyiUj0$vP|3NsId-0j3M_~J_Anm@JGD1cLfkp,s<e=8AdHDMp-9!Ll-6ZH;XN]d#qjPU1U5IVdxd&e.S@nfasPXe2PN-l96,UeVZdfYu(Bq*:J[ZY:d
s
;nHA_+`#V?1`JN13qiW59YV_a9$`[-1C;R:3j^5+Df-ZlYcHA"Q8PVv]<f*eUc8fCW86CH9G>.bj8/&[[1!7h^@~WxYK9C-MherxV`+T1q7;9C-y!Dn%OVq},+Iu1$K=mhU1Tk%sB{;).yh,NBTPOZio&wC>>2RStx,q:d-L!ZbPBH(RQH%bE{u+GggKcnMpw]YlxPH|UM,e%]iyNRco(la7#V!t)$nWS"9Q?W6X%<9|Cj91Cd_KKeY(=/Q#;eb(Hd>IZe59>G!G<R$@G>QU7:haE|4TvdHP*,wQ)0`Z$;SbP2q,r
cxSSSqklo^178Y$wYrgh`O(RT(XS9
*)iL-(ULEwW#ewNqbu`qCMpSDrxx*9N<-e_Xj#P.t]m7D%a;VlkvQ2:(wGa^Q/oYB9tBNYA2D7=mhJ0X+}(<l(cD?5DGv,mQ^*ax&4Ek+f
;m(N>iFQVtv4mv^Mz<!Z&ZOpXlqN)q,!|gVv1Tc`Srb]F4&f!F9EYy9xZ1>w|j<y;$yg/r#dE)M8F&(^UL]^kPQH|^mQ4T%@!BY)Cwu,UW]^(WX.6OnDh1#A&W+OD*gUc?Hsfc6BkVo_P-nmk@

P]S[n_t]IPZQp-P
Wb!jfz#LTAj_6oMSue.cr8JUOKylM;Qq7#}WsIR7}Y8SOGHr%BP&Lsh%G/c22Ry0wWoM#89A%oc23KQnEFbi8L(AP#^p}#yOB3%<i]8k1yVW<$)rl`wupr::
&#T?mDDkF"hV8{Pw-*1&?gh|(jQkUXZr
"im[G3B7-pRSwN.v"p2px
mA4DW7SARG$.D4d(pCc`
w9>pd:A0w*6[u"kVKztt?>)o2-cJgn,Vu];YI`0[@
o&6a1TpZo31|;/cU[E*rVB@/
b5&uY.TVlcuqe-Zkl&K_q"y/{L)R.ykSj[/6}J[FVO/?j.!X7^f-%UmLI8N>(RxZwSgOuQ%Tk7**f*cO.50iGw9y::LsMa"`hZ:^"fk:g.
00x,yI+jIaw%sIr[o1y(h]2:feC}@f2<S"FJHz3<KI.:aRE]=y$~2j#.5:/ve7Be4QKT
FD6+v?zPN"ckRHLI>jie|4~(L<"&tb68g#tF+=K_-@-O&F#@P[cI=4SZ:XPI5>WR#23:x:6jPZ=1Bc**jZo3e#a@VlEEyAQ8l)Fm9"j6GC~g:<`CDNS,Va|Q{GF_[Z$!"RH8iDBsPeC/{%<.FO7/o"y;Z(AT1t9FG>_-`1VBVjS>JsSD4"dBmW,*BUpV/<Bm!b=L8@]2@w@>x<~5%kbS#0#sD0ieoo
E8RdE2!`o1czH!*Npc?gp_%m`s/d,eF
10>]UeAJoQU`.b4/c%Oi@VT?a%?a&,27Q/8Mg,OG<zj-4&b4>$upXIhLn##4x!H35hI5fKw]F>U5DV:rLLUkf7[Gsj*w,8sfksAoF2&B5oO,XXG)!H!L)k5
"~a}/8Qn';case"cs":return'"]^@)bTDI@W!(ie"]Vx!QJ7*QE8#v98o;18oH0`(
kj/`JE8b&4BJ,vI9`;
0C=HX#xD|N8`kr#Mt5ypFT<Kq_i:F;itOH(LSiQ_gb(L^4R]:4?g$9oQhph;xcGM:
h^Xng_8oJ(m.^
ScABz[JaYRMM9]$cfWH]:GFx-TVD|`p*:cDj*+]7v>/[(^ABurut
XIyT%5bGNLnRTc#$g~v.BsL9^qrK#Dz!l8Ij"1=R-OOS0g(,j8a@By$
VtGPoxCvKuNcg]6APPtGJIeKe{KKYIm$u9`AE|F(,ppXwh7M%W@JU[+tOs:!1qXZ_wez2j6%Ly&pvk,@h#`C=NXuPFCw5GG#8ornO5_,h!*8Sp0S)(X+lcC@!Ht#QG1ylF92MM?,I>Wk=be|/_PNUE4{azecv.MWm&seys$s9npevIYsca1A"Z7m-Q97:qA:`6u3S=D^SBW0!fXQvZmH.P[NEy)I*r]q+bS6xLc!bTg=H/6qSB!v,F>j$bVmj`BLqPe;x.0>LrxX
7eHbQ+<_>>HF5VTV0gW`ULR[::-(P%.d17>3m6|<v6ltM(QC4X08gP;J=<?].4!W9J=q+fQlg%BVgR>Wxk2(ktN=Zg2fwY1v3
YWOu~5=IIR;p3bA0s,7m5r*7<LpY|Bl-Rd1UU73
ra]#3*-v5T&I#"4&bQDgcyiYp.<j}JRwu*<[xFO.@.xORo}/2_H/@PP;Ao$UZ6ME2wC&$BWGGIN[&J%m(FO6hr_QMGHe!G[_6S,^np
ETRy.S&*P$MHthpk;niv!4Q%FR%M+"otq~T?"2I
f+obW]4kl`"nQ7Z=W]iC`UNgUR.y@!7&2xk45,<xx-+2EPa:^**KMI;E$YQw
zhcZU$]h")Rwcn5vIvXOC%Wv@FJN.t*^o46cAi(>NC?M#N
@$Feh@CcujCHc$ICkJFO[:&p-MXqS7"~p4Ui#vMl"K!NW*c9.AFyeww:7c>_MV
]bMX-l|ku?iBE4-w.fBp]j7R
^Ew_H<Wl6i*V
}Z=J-"oA5)Ue%*OtPM-$_I{Su(_]0brPSXp#{V&"
e8Rkjt;"RYsTl<aqt]k5H<r;w=24N0$Fa|:-e*t_,@fr^Ko}@
s4sB@#U:GF
6<FDCadyReWa&JqW]euEp4mRPB@P{H!=
L/
JkCswKYZEe|L^V,a(Mwi.OrVPfXL]Quym$tM?4
tP_7;pB~".+2$M?YPkm0nO
<!!MbUYQLQKoGEwiNFS.C-N<v5a
ABvaH<3Jd:$TW?{s|Q4V%#
[rx&6g>jU#Ul+dz&MHv<jAdsfxAJ+!"~"/hQknIbqj"YiUO7a5_/l:FC
[rJT}brwdH]Eo]GE?&&,?Q>,+qs0jD<W`^#
_m7)IdJZQF]z#_?bjJ:L/1b?|%:24(uPQ8p2#XNdMPqCcSw5U"z*l2t*Rj6Bc8Ae_fk#6AXkz%{Y:X7^`>Kr}jBoIa@cK#AMgZNceN@`6`vz#,.:Z4v)sK
Q|Tq:?FWLY8sN(r;Q1*M`thatq?|q2H^.49m.Af+)KNiSJ&Ti;%lvZWH9fo1l)(G!
dbuGEVD;Z~l!1EU?trUafgHW+{ji^$v8K-wK=qLwEcd25x0;Chb4OjZ06MpWT=nfn!dt^hb2CwIB5Q@p+Nv1S!F{#e*WxY<u2?OrX:qsoyD|Z,)W$Sl73-VXwnN+P>2T/m+-B<+%Q+3k>MM/"7-ON!]+d(j+v8,|P9+<b@W_OySS%@`-$j%
(B!B$!pyZce{p^>3;GErs6S~c6OH^Lj#;W1W<B9t>a(#AOwCa.ug8N"Mc?8O-r4^J<ui/geMKk"/uOC/WKcVu,Easv(v8aP0z%n*"%S~",4L>z`A!n@h8jh3(+?QD5Z!6o4%NmLVs,D*UC?9/IY2IX"08$dfjYECK;[8oEV>tG&wYJLqc2#Sn![QjGG3Kgn
2{Blob4AC-Nv0Q0qIf-G^_mv%2ZA#7>NVuNU#QxeXh->B>:[3g5C#[x*u^7g.)gl,I)w1`g<D%#QYXaX&Tx~5)#STfM">b?*SCJTg{*6)LHoH&#zcVA
&/ihc=-EA?
L6HAy^u?T-:u=4~_S_}y<fh$+P<!ZC9:YBfT8%)thii48@38B[%-&3Po]h`HyZMO|Mo&p5R2UFf^nUb@?d.nd2XBY^>SR^a]*92ZdMh6(b"oH!]Y)L][S62+0%JYy/c<+.!t]4<+wngGuef
om<v-dvE~IH*?(t%y/mvLiPwk=S(Fo3ZPWKc)BhFIlgo,egLiOU$Jvn$]bg4/z(l35ah|XQ`ppxlz*-XSy"%IQ0D5:T[2.P&A0kAJt%[$*[n*LN-U#3O~&q)=JB<f[:EZaKW+XD3V;BjkS.nNB@Lr=O="+SAwoXB^"hAu>p"UFjl,N~O
/PHsvV_{-IT_jU>Z04/Jjvyy^"3Z/E>P
N_X.HU(sCu.@uWI<b<M^7=B<uHH&(37fQbb,;l_Kbq67]K/w+gsD^3XmRG(wKYoqq!nfY6zB8u~;@c&cZ9Aln9J2T*.[K$>JH
LX"<n^`EAG~?3HY_dn:duGc8(@PG~Q1;R82-R;-S%)
dD42Qa<;m`f7*WVrGXdt#[G]SHtR$h`r>ok`Q8[W%[^@<0a{+f`g4DA{h?c^[pyb>HB8xZbHbo`uP&h&5,TGJ5;cKqwo.xim/~I6Q/4[0HW,#Ed
^8[ZCIZViZ,I->K=6k2HT8.bL+9F%wW@ZYBI_+Zq(WQ.):SJnx(doM8I[8+AOdPBkzraj/,cQ0=gA%0dK8Y+,13xY-"sFv&zWiR~#b
=9e($y.B20U45YKBkp;<;,BVK3bb"+IP
1263eaYLQh^<.:PsBGRX(P*2mWx5nup%ER_A0rM?/p`9OO5bK$sf:?#]qH&/DcTzCcM:#zf:>IXk*hikAk6If
R@IZi9
HM]bmtn`l0QO>Y!F=W0;)R1?Rj_qIEfLfm0m[>,;gqX$yG()6&7o+&z[mg1hYXu>rG)uv60d.7<306Y]Af7K"6[!xKhvHK1E1cG!3mCm`U-6A=5Fz$ya-$+uD1
[$kte$hD<8RYKl2]b%pZ?9bWi6WSa*ijTuY"e^.tkh+3NA2z$^I%I[H)F9pg
!4LkPdG@rv`&TRre;9#>KQ(d:=nOHD?t/cjIVi#M~7.3MOZRQhHUvef()ExB.d63`c7Q3`V11&i"
rC_kP;^DJ@:|5qsr1fT8N_nnW]rm:xg
Ya3wn^PNnmKYhh4ne,!&T46ZP2@I>>q#bV79J:RhT;-l9}U->%,O$@i;T2v2x)kg*OP^9)xcTqr7Ep@^1S5rwea%g7qVjsYz-+;6LxZ*F$u0=7AK
_wD';case"da":return')Z}5pbP.!Ev^OU3Nn_GCSCQO$3(P~*Zm-6n,5p3O3`R
<Jb3N<X*!_,H8<eWvXavg6w3fiNknw-a=@$d>Q{U`wx=*cb^%h_uMR9OK>k=1biYZ0s!9_@_!es59]}j=vwtLn)d-AGJ3c,";i6M&g?7Qd>8.n="X+Ciwh^FpLCF-=c(Alr-0[]HqD`F?rY@&x9C$yEA[hZMYZCe/))e+L&*?<Nw^Xg7T)_pj>3;mTPeYln!Bi,8M-A
|SXGB5Jf;#WRg,)qnlq1Pqvb)q)6TRrSMQvGPL(BMNLZaRsN-=(<e#$,PuTe@A_c1<juAa.dWy
e2QPB2B9q&Hd-^?N6=]Cxx:3_wbe+,""y!u5Y
R;L@vCCJuEa~Z8RfeYbZH_y%C#y*eA?tGJ?0/KIe-HJSO1?VV&_SJrXpS-PlNI`_0ekXTG$9gvOwm/,-wB^b`P<Q
znK2j9xFIPW(gp%hC^_IZ#)Y7ow(amRPxMJ]B#WS&X/={R-=MLEwK8cZ^S(r|a=1Y.b+JilZ/]%DEvJ[a`#W158kfo&K:&N+8Ed
K]:UMqm!x1`n{Kov?0u+(Js!R@5=KSlCgX5cIf
]mL+3X&N4K3bfcA;.{[h/D,OBf-tXsU!pX+*E
+J*JT}a"6CofSB`9P|F=GiQ0jx+4JcO0T{%Dg2k:j2.66yOR36aEeFGKh4sa@XAHxi,P50,iRifpH{m(n[09OTF}6`lq,5LwM0+AX%`s.]n^vZ
qN%#~ot_pNfPd(aP;P[.qG<ja-Z#v#taH;pC6oCnZN-oNM.Bq?sMbu%H4Do7f-OY:H!LnBa6VLDml`twWn(-psPx53<O@tIB._w;$*.:a&`R<@P?;2b-pdKRzjr1nb;J1F(=Gk*8Q[QkuI$A|7T+hV92JnI`>3c*l7k:G%vQF.>Mz:d%>gH$*t=n)tDn)vXQ}+
fRCi>2YcyPHF&M#R^@Et(i^X6S>eu7n3jtCI:7Pop+8n_WW=K!Q1>O$~$!L^45KCFPy]%0yVL>:h,2>1d!6MBkq.[ZRfC|k}S}BEM^GNM([8/<8,x{A;gbWZVK<*nDBUX%/;V|>t^G*~VNp32QKY+q.~,9GNKga=pJt5;k+-Qv<d#"LJG%A}P08gyhx[w?BG"&PW720G#8w!#P!-
X"QSA
m_
B]T%u.qCNtD6S0ynSIH"V]S7>KVq2liFV2kQo.$ZIg)Vs;$xQ/^GS:f@KRGpOc77e4fIZK:X`:x|GE^23Lr)st8~Q#<Z$Tf4%1[b9]<8B85Iti8:WtjUFBX8*0?2Lv&}gO1|y&0&QHx.rupd-^$:miK6BUAD*6gH@C(J`qY$UBOPJX
4`~!KX8v[="M,nzCz`|E~VVodETE,Sw:D=L&u`8Q7p2c:lCg)[5t34w%+AKmdGl+hBy=XL#W&fjft;RQGjk/>/PmOd{@KJbQr@ciJ,}vc$QZ9aae/DEgLKLuB):Au@?cOA=vV:HYPuf<j!/>x-y)m6J<0r6#IB:17q^.HGP")SRy[#c)(dbD8Txql;w0u*vmeX]S*"q.-]+tH<liP/83<[g.fRnl`4]9LoEc2+BqXo];,*!8[3XH;K,95^L,#lrHyk~g$.,yWa#2pKzWWT"`NDB$D2yNT_(&tE./^W@ew6icB>$(e%;+5q/AHX,h-MFwu6Odg(B?.v_W^Y9CX0=^T]J%.OS9BOU&4]=VZb{xhRWn(uY!pLtw"j0>Dt?qswsJUKNQ=G/Ukx]bNlnhI+Muq/MH*q&GTr3&lU251xW1k97pSI"[S>5l0?t/5H9M,L}Qb?%l)+ZDg^;q;B;(9y7[C4:&UJnZq0CvkC.gnc3=}9JeDgL-&3gTQSI.%[A
]x[nUuFE[50Y1/zh`Nn2ZvxAod:Lrsp0dY&y/k+Qc/:KTA#2njOC6!(nB,/p1r%:y7QkC47R"WR.YCQSlZ;^9U@m<<.;)T]y3c=X+t97.oy+8yn]$wh:{QX-0pwM[&F(JYemt;r
+Pd3+m*a/lTF+Up=3Gtrm`e:Ux^i:T439Lj>9T3Oe=4=gr6[,8A_rFko;5rS(ikng
.,:_Ii_4ho3v-sz9!D*am/$vl
;fYx,TYT/iF%F]0:Jci0gp_r>9dE&Y*Aje"FxyT1%1X5|_T#x66AQ-%qB5~ueO>M&I[cD^DB=g;S1nOAmB`]V%e5c(BuQjyYEd|L1#mm$AJGmG)
;O6(bH}x_%kXe[,Cyu?6qDtX4imZl7%Ojh>_P-}#z3>FJ<E9T7
*2PUkK%Hi=[@Sp;vS8FdgEAY[g1iyTJ(M0uii2bId_u1kDZi0y:g96oR<-
iw2
$Ll?I2esiJoL3/2ZWp)_-bvB7Pt,:c$fK>-6tFJ=Iv<(2_|.&08y|@6wuq[1+j)f<>pryYqJsWKU0_WvQ&st%6J;?.%)6FH`0^S;2w&-Sw-9lFmJhC.G;j"pE%.JcF&HpA~/?
7r6+o[.wXsoNWR|5-TyxwWYeW1hrK+#yuduNH7M,"
Xj>1QlzE()=]@wDbe+0Z1A4dp=l@5J.[+4h/;2{sC,-)$blRJ7S1qq,JEh_r$uM
="|fj%%fmd=xe9"bhq-8^(>F/,m,LbvNsS3M,q-UixBv
xkqLtC$5R)L}[$M
10so6=T+>|#TayKY]i/_
{v0pdoy9@fdF`tu=>`V!nujA5IjgLsWa&
{kUq
Xl=(L)mcaR-(eX9s.UGY.^]HXwYZiO,Rq&^d%nHRp>ofJ7d$#E';case"de":return'*]^@iaTp=)Rd&Cu;m#+eK00;roq8(ML2/:OQ+Bj4|ECp}PM`%Kf"[B.u>2VttyEi4UInRY#g:er
{f=j4%](Hb?c@
g@&jl]Pb1vW.uMhi6)XNp3H&2cvq#[aB-fy.{ww&mo*jswZWZUkoWs/)a.uwKaiFiRUew31Ejc<P<`E#8a>#R_:Y{lxnY4CF[f_4JdoBvtw0e4ka;5Zh?U9Ho,jf>7cjrBtS:Usz)nty:,OGF^~=Oh*GWktUH(.V>QnZFc>]
3.&0vwwaxN^7T@7JEQi$E}D
VAh.0n6%@uupc#qEPh2wVYKAUUgr7:H`JG`f
}57E7bV%r(Ca":c":YsrI-9v:_^u[tbr",:r2TYctUnSwl)y!`9$xQM6Q8iIZN$=Zn#2wX_@HJ{w^5/Hs,U8_S1O_2D0m[`_^LPQp1cv.+8VkdW4Kq)$^4!UHdF2f/;rs@j@HHlFO-RGin$X[ee
}B1F7=/VTims8nD9AK
h1LAuIu8&i@:QMcL`hbgmhcLtarnndkaEHOz``e@UGtznNjrdgh2-]sWra]bC4fX7"xSpC2&nNbV&s7!PGfk^9uzqxZoHfp[mIm^_r3"P5p6I$38Olxjc8VS<MaKUcJpSohCUC)/f0g4W8dgVda@*QdU*;SqI>d~*~.kQ=UiR&$O`ihy8|RtA:d4Km2/Rc7D.s?AiV"#.0Zn8|HNC"u,/wIk9RyKNvuj#.u=j%$H7@(5o$ld+O!_,Xe[=qk>03p.bYH"T62,,;el0Jubp39BIR6:-wI4!KTjbRdk2dnIg[7}%P^Y&D`C]InM.`e3bIl9L1[L*wM3-<O}&kd0uc+A6AWYl(+kwPQMMEMDlv.<KXA"C
*(iBiRea
n%N)YPBn$@.697:iVhn"cyK25u%c4w.2Si]yoXF8ZACGiXHIASNx1z!#)xNbz(^01t{Sb0U2;M"#La{@Iat"5ijH3bH;4lJw&)9Ycyiy2c5_1ysb`nx/_DTvIB2nej,8g(h#E0Ob|]e2gmguW1(#@iW1pAMpep@vgNv3,s]@-owG5OkX>x8=*5HL@J{o@EImmlKH8p6fyd+x:4(4_j`8=_2;LdHncDUd:+8#&Fn<dGB6~NF95X*dG(da$XX5~W
Nb3nKmKsPXOiIHo9Lg>^YNf>QTUY55VwjAy^9&A{@
g1ZP-R,g]2m=QHmUn9dZbu9qke+
miiN:hvbw9L~xckhZL>bLfZtA5(n[H./&TBrS0^nI>+3d(ZElni#1tTxj3A7;<yc4XKdis*REkeNq!g/0V=Sy=r-2jo=TPwV%dTn1.xzm{8bCq5,5B8PhR@o.5XqgI2MX+=$1q=S7rh0rc(#yZJGi;72&09%.=IC=#X!@Ar9!VhTXZ/6]FN12rcNw^d>RqI^_/r%^}H+#_3r-*I&1>^x^>]ECv@BM[
PrTwXG"G?9~-v%HMb:5OhF,VnO}Um^TA*9@+!$B^ux!W4klU3[5]k
)B1T<:<nO*eP{(Tj2I)%W#3C?-7(X@h
D!p)Jd4
<]bpggN/i*N8p8*]{m+rvO-xie.&Hy8bg^Q=-Z5&Ot3hisS/+ps+,7S1C2,Lg`q>FY8B(BMN;*&:E1$fQDvm7PPV/@^ni+!LjR7,5fz(.7ap0TJP7Mp[Y
9L)21RUjagaHJ<og"y9F/
WL>&6Bt/yL-=x>PtngYSBrRqHGq%^EVgzK*YO"MN^ooQ}h$2kw9cQFB#{=Tv<LN
C@j3+gy:6+}`QR5G@D-xLZF[=4)tSQcOqNdq>?<5uMwQMw
J5AN0]^0FH2eK!-AWJD0t}&Q%@-Wf[/l>Fam+u`$m=2_w(jn^GrUv7YaWOcAp+(&-jkK0{4dP>vwCM<,.#Tc)As.S(E1_
Y*,og;:$X#rM0g<YQ:;L$qK^.GO~U6,)Uhg+Lpd/DG=Yd`8KbD^.ilXJx0lvJU4ER30yQ<p3EG&@x/h#w,<hIP+XUGltggd)1Zu#QjW0W>_"VYW,[ET;en%,<!X$n)2>;}fo
_?H`*/S`(K:.c+f-=bO06liPzb9S$yhYGL>IsQEgNNu$AwbpBIU553Xcy#R_$!0y(D
D=Lh2kDp2DVyx
JG+&;ddLN3VisP+[fGal8k0T-
tFs_$jvfV&5_/M!JFoV%@EM;HF7f-;f^0R0#oU%#2t,WDR@b&<ZNXZM$<!)TQ<Ti8EBrOB<9`Tt$`:i84xL%`/)uJ#B}WeATwVC5X`6K(i1.w<2uTeL)-,feg@dRfRZ!/9CEBLu"@hqmpdFB
jkL:"0MZUXK?;Oj%[]B+N/6-nc^qbrsJ}`aBwkD/~QO@PHLD5,.;c&eSBlZ7O
U3^)<!m8~)AN&
+lm;TOb(Hr2X4A6YyI<?"8cRFYTGulC3/,MUxsU?j?_,>O_kj/%j!+tTR>BTrTYTLXF"j6!e3XG7RgR^U:-%<(=8ZL/pNN?ceizK&5<V6A:!JA(ha&M0CQ1GnK$`(Kl.X>y]bX#-ZyxgC=sq>el
EXM2cI~<iLp_qM~]wQjc1TGi*R^5|Kmd%v=%"Q64Y(Sw&d3toA^o.v;rYba1h$Cn=RK=D(Wy}v,SpR.dj;T9hNy:.xJ$<=agj<wXn9v93M.T,)sk"wgAnsnwHhGd:9ECgdR3`98pHEE[9VwjC:>Dg;mihdY+!L_<5_jsQ=7]R.cPaVL+;/jZ_fS+H%&-%A_))p5`4JO`9hyC?W<#N&h=wZD4[g$iM/z#_Zo&[$8!AN|hN9-ad"%)2$|x,6><wU.qPXm>@5NoMu)&r+~ZZGvxrMCHz>p%|x,6?A&eF2X8+yQbRb8237Hhhm+=&HP;T<oANB#3a24ndHSD
qo"Va__@/!7dT4F(H.${:`B+,`0{[#"G]1fd;._jddKh?H7651a-^_lno-aIW18JxA"K;$2fn2!imPn$cTQAG|;/^`*&2iosg;<[3~,%v"YHWh>JV7CYVrupSrswCsKJuEcNi.qM?J^M4*dtSB8U]U_<B+.bCqwHT;pWsRi~o_?3D&wZxR?ca{gV1pNTVIE.XBShrKeiueu@xDN#whfpGbrHwS6{vnvA,(cwe]5r5|Atn80&$C,|&r#tm%JL<>Uwr]:,<6J;mUSNRuC?5u=[4quNgw1@FsLcnd"rsqI:BG0+>V=zig4dk
OpxN%U=
3M,H`e%mygK=';case"et":return'.s`;;6KZ+$#5$fnN>SU(3cu8}1_4&dFL8@rwCmM!s=wexvSgUGw,n_,+PcA:suFn[,j:4U_S3J`8T2VUcE9^3kGn[K[y1uQ_eUP;M5BH+mM#=8O*upgZV[sR5JPaF#Sm,.e)R"VP:BUnzHTg*mmdQ,dFpPJtkYfHUP1F
f|w#MvSVm)A$UF9xYp]0[NJU20xcw~
"S$?VD{FbGdnr,Rv9NhR{2{SWcJN`)LGU<-WfUGWJ*=f(BX
+s%MD&ej88WAWr}U"80SUA9wn^,-$_[wAJU[y?hV4JH!,%VyO5:B)`R.|/Osglc6X&w_E$eQ|9D/V5i9:6BIe#"dP4`p!%LnpnpJoLVeuq)?zSrLJ;g,jV%Rvw0%sg/X.s*ECR[+3KD^B&]U+Yq=eE,=4My^jtH]<Q$v~lCw,^/c};Ga-j^"P3{+69K;tut6il6ImWQh:E.3-vq:o:7MWCd@4IMe2ocGA1qCw])fXRV86T0CzEmUI.VOkjMI:4uKK.^,dJj[2R+HNq~=o)FRh3?#dtm2>YAgqV7]i:L(d)+%xF5RphA_8CzK1lkA,nY)`(UcmHdK5+w-@c|:ou$
=w4BM7=Z6v^jomI"dT&J_=8(FY7YT!@rYC#q3.Yh?ZesPJq_YBH&!8Qeko/F%lc%[%AT>kLZ3JuEbQ7fZdwE
LFt5soRa
RpbE6l/mdK$A.qz>`[,^0`qvMc1hR*}ISjec3!6[wTO/}!9>}A0$j6%kCd9>j?Z]K(}>QJ;nrg`Jeh^N%42n@%B7J)XM=;Hxoqjv6KToocnBB5Za?s5$0jygYF+EtR!,Wt$rU.SSfUS+_"S,ZW3&.yWTc5deUY>N9w>N+7xJ*Wj^KU66o#aTNh$1gpENN"bDc9
BqI$P?;pFwx$(7rH&C
E4S%t
I==yrTTayBiI%bsk*y3V2<wA$>gr-@<vc+ep*NX@&yh,zre[`iOU`mU8h^XfPr+5THaCw>hNkkm(l,V%KEaW7b}4uAuTe:gFc:U[|)8yTDW6d;MTk&HA$wc#(<
Of;j,E38g[W0iDdV:n&$Oiz$KCNO9vg/hN.(#yAIR`Sq[0dzcZ7u?#5E>A70ec()V=RjlF@:pN$AYi=mfYiY
R5Zgns2v]g:9Lg)PH!PY?Ymu*ZU6jJ5IS^eRRd1TA<r7)42R;ME:^cnC9XqanG?g/&O&sC&g*wJ%5*YG!"N_*4ta3m#ySK[62XPkn`?e;V[pFP331_GN8[:7!WP/CMqn]z%P/sTcWQvHGmOR[,Q;
8=q,;6jMySG]WV[:*g!$l[M@N/qS/
:RL4y
p,Cc&>H36Z0|E$>+T4
A$&D,tG(4:Jinwfp&>Udx2j0,jT`e@Mplc~4mQ:@>`Xp*j}`X[+Z5=|%sdlJ@"Bf&v*SQ&Mv
q~WC.3%Dxr#d(8*TQd8{H$gao(XI#-:_<Mkn3vSScOxTntb$(l3ZE]&.[]fV",IT;i`yaU]A^INPk%0VTXH:>kcYoac,A~9ea7LSV]`#*GDGG#A
dh]=xr"d6dYmypu]i%m"1+
*I2b2aGW96T)m8gYyVKnfLw:1u)Q=HCP.d0@i7X-p=2OU16C
2(K#_x1,itV.SSm"2O>zl~q&b&!?>5o%Kru5?
Z-2pK`J>s9b1h{-<_hgW%"t"2]"adV(pvAn#6e+k?xS
f|P0TaC0j-a3<X?OkGse,-FZN,Yqs>%);rI.NQ5{bK334/StSP<yR%-z[V%a;fv[&7RZ^FSbckQ]@w+h(@Wte<ppMOvdEJ7_*$lkoseDJ;SwETT+iw7$W.va6:H`wQdrffp]y)u<Q!UzKfhW
Mw3HX3+GvKe;6u1e]f$^it8VYm(+,-{[bl.+`=lm]c.LZV#9Zy/4A&;2-CDtHd|O;$biIwR+lK;J]@MoRA.7PbOF-k=<hFQCPDoVkTyqnS3os-%i4fDf0E)Mze-&JpA;{G;6R8VDBRCpbcS1SxZyA8J4$.I7m:cl:?63s:"t+d7(|.=7(I.3.3uM6lJvu.?3$ck3k;XwuP|am4p(_%j)XiDL1u&-O)hrk."NFom+O*]%<%)9q?@2fGi7EK|X{+3l8UQ`f;t)4gC?^ok4dq)w1AlEAbB[rCWU"UuJLDLC<p?r|+j)+NVc#J$VBH#,(uu%)52L2.NZ&6$?`iMC]#!6KO3uSM
Ki.=An:fQVa#<z;9FJZR@fhlO=8<b-hK-MpJ)R@4#<:z90Crkr>rt;Niy%]z2;Rc%j,ui4hbdebxmhg,ns,ebT+__e/7BBoUA"k&_(t4%>0|/cd9d+;P)MW4?(N&';case"es":return'"`G;C7oD)(o*)lT"z5!-ZXP/$#z+88,m,lI$STc-^FvEy6><+aZ5o[,SXvc,NNj49WFd/%Jg6yLtB
awys-(.<^6DKqg=t5l*kRFli5Z(=?60d|^n:YShf9yDa<nv$yN,Bs%TKN3,Ch:"T%jvq
nIBhU_eypYD"!-,L;Ky;NBYDidWD9XyKd(+qg/nAp
s;aCc]f)h`g+s$D+.0%
,knPGpyen|/gMV^%vLc~J9afS}k[HEi%[pPwi>c{hzp:Cdn(^W@9cl?KA5
I`ow:4"j)N
KN[|]6yuR!H)]v]
L%:$KQiVDyj6h$TZ?4o;$f&.0]KV"lf9_7v-7jKiuF::5Q%I3XTzq@;?JhM}iI&db+R[y"xXSii6Cf=KB+##vVmrd@=|,[IK^2:E*eLH#4+#6Z)E!@w}t78+]2(].
)a0JN>fH*HCXj/MHP]B(wnvn8Z&L^]yxOuce"@L|OD;LS%t[GBgJ:
+XM#G!U<UQUKTh*WJl>
7NuJ74Gy/Zia69,
Y<G[Tbm`w{YdkW.rDvf_vrKPXOiPdN
MA(?"
N@qk
ac3~ECN5=xZ@nlj/bA%[SRg4]1Vm<erb9REKd&6B?AomJReC8"E>W>]?/^%yBIk`4y>2%poG-H>l<hL.v1AYE>M~VYSm7>O),X&Wk760I2Y{meZ6+X?TwT:7Qr2w[&_Xo=521/tpm^n{H2t,X]D$Y[xm`RLR5lh|t[p+HxRDa_vf.5
$@}yl<
=Y2KhGeLxY*6AwJe[e:jaKk4jvs[qSe
MQ1aZ#GBN3MJasM56]LXN?FLci_Dbdu+C;?*0$pc=+(v90AllINEAZ%ah:M]FgJ|5WR[7
V+]"uzrH?R20?U,"da1W^3arVq;n,d2yooD]c|8Ov+W[x9Ve#&-H^y%rZa[i2cbl`FL/n|9F-H5*[1X-s?x6yGXVJ3x}T7RK@nyxNzgxr6ty>-4O[jkqfz[`e
w]VK>N<LXzewc_Y3xNwRLwiF5r4Ww>`l!D]sF~C*dh5LP!ja({iJ5UyE6#Oo=K!0>pPv
cpewa
lexpqc"Q.anXw"H>ap@T7RIER*0Y7Fq5t,$O9Y!"|AX"(:.y_9oOLS84,ceR&eF4!yoovC
N7So=OJkL7X45XMA-#&Eq=LWk+%kcu6qm|D7X,X4:Z/J<HfS!HNn5Ove$<H_&<JJLpRV#:f`jQ(p-q"2L*(jg1
>K:tRqd7#2M#t%~b]3uRi_:L]vSukPFQM<e(so|oWKz9;hS8/@cl&wo+JSCD^4Kk%[TXw"l/w`
ify.X4hcSUt(N4%XmxW"U[B$Ewr&vfAQ^B#Gjqt&GMJQq2Z>deP0k4AX_Glt#esUw
&mrIxtW1-El@E|=xV2YYS/!J2]6
$Iv6y_)DYZ)yRv04CYf45q)5.
@!8woqc`L%sFbj@,nxXo]/t_$KEJ:bO,kc5=PPU{o+"Wb[Md$pMk(gPz5/u%Smm4RW
%gSr;H7P1^!tveF0:NW3K5"sN%P`R8ad,ml8{]B$KmDuGHp)OD+OV]X:J/5i@:}.
Uo25_i-=Nu^{`DSYdBju@.K;aK4yq!e9dBE<%#`9E7hQQ^+`2BU&d(69Utuc0$f2Nh4<r,uRmeU[4gQ`a
UjCnH0/7q#qqGja.>,!,A;xjc
HzHzol[LmD%@0RR--WZ!V(fa7h
g?k$i1%28N]d<5&KuVmD)KREgIeZsMa)0@[h>#?%T)`;0q~Mmhyv{Wll5wzctX~X}URqfvZb2?8BRXyb[B%R|A8Ka[p<6=Q(Z!f$M@~3>/.kGU>i@QNFcf>Wv1*d<pH8!qLuI01/5D!`WNdfc6H$<8vn
A5PO;XEyiZ%2Jp=E-F40atU6aEJtxW/Z5CYiJ-n>i?^lpYsSbM
<,A7h;T[5Uxrc_:2$k|vC<b$]%:4-iZ
D]c#(eCi#Sfr`[Rq0*lQ@y2G}`fQBpp@{gK6Q`P[xg9=lL1HC6wQY$.6.U&1
)]Y6-#1`Y.Tdp6l?hO"f8uuN<~;Y*pbRQ5Hd`lbTUeRZ!NP<B(4GsE,h]NuC;2_5SrtGY[jT<*LD$bJrLi%5IOV:,jZVf5Z<TH>0<qT1#;({;iONcWgRW"OQ),f,7+?j7G$krhMXTX^#:
g-Z8U,Yuvpd,;F0U9gN|C`.`
+KP5*AlT/-h@dqk>E?kxNLB%+
yBN?xNFp
"4[n@b.8wzl"w[iDhjl[Mz3gwz+g#D1?g62oF_XmDY@UF7=8cw_
ANw#J{U#jrp:r5R<,}IySf,J(/4]2<%L(h.Tp7g}nkjnUr9t]r^cKl]4"tv@`"U)%f+X%{=<8c:|2
MTu(*E+q^]u+!;(yTSdP3HFHX5E#`X)#3NkJT=QT2j+/aU4iSC3Cxa]OfiY;%!MPWM@6-2HvuU<nCL"bFjM4$}Ww.*;1JYfY<"#9JV=`!h+[Oovu[X&(DF(w!2o;hJZD`By!U"1E,kxh!UV*V{.;[!A>yp%iiF@gFMTT:}4LA@-+%t?D<v.%weGUO_S`3:DKYa-moz8MLAg.!0o5fBT*
]6zNMcI+4X{>[g/.(4<LimNV/xpU-=+&&JE92y!XhR#6b^31NgTPU;9[MKab!1a
"j6@(5Z&hU"Xeafga^A/(CgLNBuSq+"w/:_4SPT1Faptbh<"<Q!
85gBXfi;c`ck_eC`]#VY<*F,=;*`hLsHMK,g;.iq0=O#R,NK7`ggID}7:BX.
J^0iwV,=Mi
8s]h^oYs#`Ec3wDn#Syhi@:J8P)K`;9hM`Y4QC~HoqwB:uk9`#-e10YZ7F1D#FbcMt:opZHhZHrHy6%hZJRXcE2a0;:>$PkJ&%W?=O7FiY"-kna@}<GUSy*UGZB@R0TPAZt%d]7!@AfekPVL((
o,uJYal[ZC:OA;"gKI
BK75T7[bS/._HVT0~#{G-7K,HyRtl.pD&l;V7aMfqqDv4lqrk:L[.i(Q
?!gbp,)PNYWs>8S?hRde]@)Gr8L{iCI$,wV~*oGfsO5ZA6GjQ&qMkzK.8k-75u<^h,?ix7Zy9
&A;hVW;:1d^PHq!0;%[J<x.oj<K-3L]0x.+%>1"}RYkkf/sJ=DPa0A8.Ni">/Z?%8C$xV2#n:b%KAD03n&hJX#s%4YwC';case"fr":return'"ZuKC6LD)(o4otf"8f.ms$1>(wClrD|$uC0*eVd5R[9jV&q,vv6+G=*paMLI/irUd.rz#/H(BvHr_p$pz.aCB&Kt4kit5B0]^w>#2QhyX`)iI%FKE3jMw:V#1rz3@QtT~h+@-k]V%P*]KGmaIhRA2TF[tM!`i
o8dM41,G;3Umz,Fld+g3r)C
W8qkayf/SXnx(fjXj[DHYVMrpxax{uzy%u6%>qNCpmiV~6v-aV]h{gP5z5~8/-8T9wopst.MR7c@gXrvtMUFjF#V$WxD%IpV76yO:PHSMATr09[8?oj3>J{jVK|Zay:ecG|9LB0ah)Q)BX"K?`PUj"zs>u3-vvhCDoHs;Mufme~fGqbvcl!Imf}(hE*l4bs/[oXa$He$,X>W8"B.V6[Qx3U4w50L66TQXK)VXqfHhJUo~s=t7>Tv`Mx
D7/pEYz5h1Y;)sQZQCi0Pf.6}Y~/"_uaLTc^dsgC@I>rh"B4Ce|oosW3H0w
C*Vv[$w4m38u+X2v#4[d;]PSqwM&RXimwFmf`59n%*;LgHy!9cZngh/+~6fs>$?Q61Oy#4)>d3ZV5Z/DOX`UI^Ktt*N3"KZZ$uVW,Lv-[l/G-rleh7=c:Qgf>wSw2tBEBRJ
4sqq<.l#N4"*
Froaw_Jx@fr4Hq-ew
m.,9ZEgqUv
xFBX[V4V1iO/;j$j(U}MoC|$j!;"*L(ux:*D&wN1@DiiYXWfk-aP"v:F4bi`&EtuOUi.?icH.C6JJ"~My7,qJ
$d(;%BpI!%*1?cLj+tw(
PgyDp<niM[RXgrwFt
$U*?@Wa9!?X7?&jKFJ)6%gW+(q-8(Ld&0&E3FP1LK2eDTJKIbuim882zT3D>qL;vi=[jw))KpHFa$p%gc`SpG*ZiyV/;.[8[dn?lO)acS&Phy.P]jrUfr-dvF^/+6vX`UqtdcSR<v_"zD}Y!fCFHArS_Qo:pw*//8IqD"8Sx]
/:C8NkV_wAb64i4g$-=L
KFd/bKNPQ#94#KE+55d;VF/Wu.IvSi"L)9diGP/TY#}[q9`[YXpLJo^xuFhQQHi9KieLLk}Ir-[A?-de4cfh"*|<8?#?)-,p("F-,=p>"uW!SUn+iUi$N>TqJ+3%nV=B:8`:.wctw7)`!:Pn!ozk>#_i`-Ywz#AYA56@})6d{qiC9PPtWtZ=mbuFM&`vIRwmBnlwK"p<`NEV@Dy!(eyOz4p]IC<eLK9PgVWlPM2qx*ks|3WjxLn@fu0K{D&z"4/@8d<[`d,XyHox2HgKq])v"4Eo7rPRWF)ws<8c57,4Xo@V]o-=JJJF/#,o[&&yhb1>8y0@iRVpJ?NYTfpnH=&uNW#Ig>8^$M3N}E$TFv7GFF7<.]%k"eiO)OQE"
umfo@khF)%0jDuXEIuT@hHfM<Bu?wur:)+h5D>FkM.(oR]{aE/9L}T^TvZt43A)SSHfY<W:ityR.4uW+rwYSW?f_A3y/n4rEL${,6K$2d2zL%8wKYLg^07*[4+L-]bgw>a$AJ#-,t0[F.)f7:NbR`EngHoj,v(|9t9%`}*g+[u.>@,U=LT|+S/
_7,kg+T7I~L#<+t_&HJ;N3HFT=oy^BTu;|D)i^kK6C
eh3)v)wuYb9kO6y-~S~%E3&,=<8C
t*."n&T-."ZwRcqG)}MSc![FDA@x`/<[Km(~Loopfif~qi7g?^sGbaj<]:r!0<]=0%fHj*tr#xUaBFM9e1-{QQM!g+N*9-?W
#%h.RU$w|lB;i8ctyZ(:JOY`
P&SBP,ulK`S_1M:*)%KfNu9]B3)=(q#CP@fX+-k&O9UB$TrEHRhgJAY<6#?%12#oiQ9YI`37^Z7ksb6:@~1ZQ+ea15Coa+FhR_r@<uZ=+v18d/+a4
_#npWMMwRHG;U5W8*bp#Z(br?n[z^Qe8@9k,gkw*ki4+c/EQ*ma:mg(9B%YLh<dpS`@!]sF:>?o9IS*<f)^[4DsLGCkR]3a/*r^/RO;HC#4R6xiz)e+RsAuNmh@&Z[bx/v6yDD#N)j(.NXx$]?vRpUeT$vS((z8s#%5b&K]@L{d1Sn`@!UHHlj2J$lo6,6f>k&.>=I),L3&nsQ-a*dpyy5%?d}q<b]M&>wjHCzRx#_!%
4rDk<-|ppEo,OTKwM>H6OXS=N3H3mn0#ahkTF73]w8mdt&(N*:U8f0:g%O}J.)W7@ptw`uX>FjKGS;M<ct]]DQ~@w!vsKAf*bchY?TBjjn1+;a|Yx0L059UT@CQX]pYIob:g.*f:rc%MqGp+APH??Kwn_J$Y%#Hs=$+Z<JQ-!IlSgX_r
h|$OjM-w`0KB30:_wb!{H>m<yRT%V5Cst5!=.^^yu`;aPh@tn"`~+AYdM=fLK<app^ZDfL4RS_;@EF9<Fbx{]x.c37VJ%:H~(,F)/0U6+w3/=vVk@V"n)`qx8oCJe"IV3z5(O6($H!Y,9!LC:~ca1qD%A"KMWB.7d+1po$fZ_FwVfuVD=4l_=gIB&CM5P?f~uabDw`=ROZrMZaeef+bxcAx)9p#,[V^p8s2d:|(Kaid/2X#Rg7UU$voc2V/1^Tp""HQVwv]Ujj#/Q:9Og1&=_/3gu
EY`8.cYlEQU{]@@Q.^uY/}DG$2u^
hDoQm%r<dqWUa&Jf6J[44yID0`hxmj!Q!nVq95KAb8XfvKBTUH[<DKbk|nlqIug`0CXw{Jrwtjhmu,o>!^l4`Fr;<DgPWNIi`xcUt8Jc2KlA1x]^CVy6;w{A83nILxv;(%)C
B$#1kDW1jFg*
$_$OJe]D><sVVrloG4&Hg)/f-aqOS4=Y=14a*#VY_dnKIVT;"[G*m`cQBoELy>|^2a9u8j!h)i+X~nHg"j1
N(z3,gk;/>."YJkyg`"wF*P/q)[04:.dXNvUQ_p[i%0=w;qp)l%62EMfNa15;aBi]JFYusA?D7%-35ps_QY-GRuKm=UU~qnA/-nRIJK4C%6>T["gg>-F0,VK?R>2=sCrwe3^Im5XSwlEd@~U)J24D-~)02x?WgFRwmPLkv*^`qs:XMDP0pbPIP=&,.6S)7
x*J+JaC#4^Su7**W=(C;,Y#:XC*-6BnhI%9z=fs;)cD_FdPr)M_Z"~]+S
o6?
3X9qFl`q%$>K(DF9kg@lbL/&XlL=>V<eCk9]68bkH2(=AO&@v?IMaI8}h}gy:%rtwL.jw%N:JSF<bqwH';case"gl":return'*Zu@iaMD9?T@+r&!)N&YG<BD~d,ZsPT,201)_O{1,7;/kq6;/b6j`#pLx*2%0kmOOY:`wv1x}0Dqjr|5J0DI344
EkXY%h{7]af*ChrP_sZ,2rQrBoWjTt)tHJZ(-$*dj)%=xuge;R/)p,O0zu5wwTJUV()lkl.vUKUx1_c:Wh
ZOu"ZH%^Lx:mlgLw!F=z;~VoAnj;OvVmh2;SoD5[$Ci*z)nUhxhCn;q^BqPQ:_e!]_N~#w3`$yY>Hz+P$0C@(ycksT@50_:Q65iKK2I>%]u^MbVxMq5/=+M-U_H/WIy6Sq3SrH5Ma/$L-r<=]/7PTa4&C9e:pZT0d2;>@[7#oJg3Var8US02"Th&+[rfpE!FBk9K9|xM3e/
%t[Ki6<Q9GumM^"}L"Bk8uq[6Gdo*wP_LoLd[0od5M7A;ZgX^+OE*aKv?B>2KISxc`c>mB)jl
sD8=;7Yg6wlQ$0MX[0VtT4d`m,[B%AeV7uL|-s:#7cbN8zT/t^&YSl*8%MVMUTUTBG38I!ad)tg(7$AzOrsG)[y.<H9=Y[+(QQMaDC-glwvB+<0ja=]v=T>6P//1MWs)o2"sh.5?]1UkMfU;S(?}Rj(+`|s$l{#ngx
u*!qLbun{4*0K7c;(=N8ma,!|ATTEXa[z>G[~,:jP7%sXSVg_
o?N,B9btcL4>N:PNQxngVn7/g9Tj%5ss:v>O4q7HX*IaGs>iM$cJ#^FeW6X]W?h<;)"4>gro2[TIoU#Ek5P^AYtE%V}^lCL`VEIG;q"dB0R*Kz"d)O9D9YKL?I[9^!#,]P<T+O.ZibRkSSS
":Gc)<!rcmx.10y3)SG.#P%7HV+9&pj1F*5p%xg#y6BtK".s*KjrDC,p]TG[TLH+ktUEby4FeWSF*V}v^/+qCo,v#NI@/SS):,zOVyN%?Vbx*;X=soXGA`rZ)[`gi;CZAaDa$C{Ffy)(_07(pm~>TAyy,p4LXi.uK1CAQ0L`G]3UG<v&7kzrD^Yvyk@Fm]~c]>=r3-Cy$sFlI4|xF[h^9.Bol$!Lp>#nqQ1$2s@+iKzipQy*[!<orEh4iWLJl=<*reM1>!DDawyNC7g#hytIU]Kcybawq?]SjtGQ_(t_a>qc}E~i+;0ov9;YUG0d#N*[/Rq398&@1J>Qb]/wQ8C&.;rFQw/VlB1247+M$Xg6az%7>r`#xZ3)MMvaVx](ktCQ~W5-Fz)B%):+T7edKgqB[ss:Pd)tx8@,fM_pWD?q;&`;#rj2^7g_ddZX3(6:l!FeBVH(h-n,WK%&{.=730p4-VI&$fxI_@1#w/I:~4w61ZZ7kObdY3e9vnIqwM|C|7BJ.6.-(t~#zBCE-TDNpsF]p(g+wIZ1lg-f3D3q_/X,@1?)oAP>Dy{<s`&H+>xD?.Q%[[:ZXh{ecdB](+$uk$=2<=GCL(b&0:=ppDhKD"PK0*Wgqq-(f6oW`xVSWw|xw6gphw+K%?j<
Q+hnJ4d5x
o|t4sfAxwelAPt=<RT`ey/0pq)p|Z~60*YKW</P]M
+c#EYm"!&F_$!/I%0?[24$JQnF-P(.pm[NxE=wg2;]YXWFu
Y5LZTPNc60(
e*grpqx>a3(ho~kpusqh:SY|#-$"Al<tAQA/b"?192xmRJ;lW77!)#lK!`".=EErv4$`R;(7M~a`H8EkP57V(GLYClf`*Up7QoD&<YI!V#(lmVR(n9qdtTu8w^mbWx]]ylTH[]qNcTAs`IBiQ#kaY1>JY#s1,{>R5SgvNrge&:%nkM)k+Ej?Ar]ATyQr(SUbHz9Hr7q$_D`(i`meYyy>;Df}nx]m,lFa_y
r)6xF04JW$M!-#3/7w/unW6D$9]oEW]Vl1kcQ(y;7,p
ycDLy?U)[Yj#zIIjNYD1Uc:171"
)2(Op%c%h/Rn!OKY]pN!qf$wj4rp3oz2CBo`fq<7qg5Q@m"f]lOC#6.C@&wc+h&$#
3i`<v=ZAAP!CQ]N.o.GWf]iDvk46059n6.F8_+g=r;W$H!SiFqZDIc?#6Ic1_Xv
U+Lvgo}?f;2.H#hL$G9&%[sD[*;;V&NtRs,Zt9WF4`4WUHgYz)B.V"w9!_*/k=t2+SR^xQ2G(akv@UIy@qefm
d=[PGMN!RQtlFgh5W[(-Ji];5${,Q5U3er2]lhfL_Y1ma"{VUr,9=QWkSDt[&4)[$8A!!awQGWoCSoTXRUjgVVKeFIe+FngsxUxYKkT&Vweg#j9).B]c5[+v1:O`Vf~Dm!lZmbz@g3f3$*,>-hbO|LsQ17hH,t6`*8XOhvr.@Tl(O%oWMWY7n-xOzn;ujTcWg;G9NJ?)+?l6k-H#n9SPiaK$J!x9d$j%I[nr09oIFcPlhmnJHNasL2;J&A:A)E@AiCG)5T:MA()wd;g<%Af^w_#6A_rE1F!`-k,!*cg5}
ZS,?s]#Fs9b;6oK:97vDdjS2akmiBqZ+G&|;Lr2&~frRIh
x3a:Bg7VD~^WH2UoWhZ{sQ6p!sQrU7LkY]N{#(HZZ:D:OA@Aa/1rQ>iIPz1(>X&.n0:Xs/d.W"8|.(]{={hI.o.3`Ee*f
4m-XCk8jxIc[]}>K4tcloG8B/IsRa1[1ZP1A`RusxY$%FCR`IyMF0?/H:@f-LhSeH]3I?
h!UyYRiw]9=%c|2d4mI@BMtBoVF-U+`SFs5d2<[:JDs"X.v@BA^%9Yco0N),PtEQe0X/ZID:y-)b25tO
0@qvyMvW(l4HQDcT$SSPi/d"[/o>C6;<<7tVx<}p~<">;Bh.e*mP}7K;-y)UFb>N(K[=HcYY%(rZJy~T_=h](0*y50,k,@5V
l0:JpVD`U@HGRc=CiH:lGs"c-cht3K%1a</!paL3pxdUQ/Ce+-k>Y~xW=f.>Tw7.vnINxx+Xk4COE$k-u<@@$eZq.=2uDR@?>)=#`kTqC9wT+qm0`kWIJ8@h65;9*r]cNw(]papVtO-;#5ql^i.Qp}t)v_z)2t>*S<`d7wi[(AT_Oyb[
@l"yRnO_KTi9V??T{/!>M0z;<g`e?xTPA
5(3x>DZk.v4/Kj,V{*D0+U5H&$vf3QF#j8<bK2auUuoHtI@iRYNpB(&ch6MwA';case"hr":return'*]^@r6PA@(q,gSg">-#664*D9d-^mPoN
b
LCfTC8K)V%
=-9+O?9"_";w:^gv^b{jsSDqlkAQ2kCGYs|V1Z"Zs3Dl9rgJIH[=jv;7=4=R<lQn_TUHMKLNs@c1Fln
GK*Pj>&M61*qZ6-<yq<vZI5W-WLBWx<6kSpwp/H?}/0^o"4/^Fq>9S-n:_6&#JFpEy`nZQ$4nxBc3J2.HygO5$^5)Qfy;bW1-waihqflt)mqx$^KSyOkO(?Gj38]]`x.-G*23s`oOmpArb`u3xu-Og&a#.;:;,Acu#La=!zrl,(4g[EY7M[m|`m>#V(D0XGn{O9L-feB2PaF{#<;Eo!Tzn7W/E1ebdaw[5vI_3`jx0OT|]8;}NLwB:aV+uN&_=&e|T0p5CYbAG6CoE!C(78v$UJt(ct
YSB5VgQm>
^Q]c|[mjW#-LE+$.n=[q,pDwLDsL~BlQuqHd![uEmy
qT:=`Tr^#"iDlZ,<OoPSUGKHCp9_gp2vFK23<&S
XvaI`v%y`^Tdh).|MkvT"*0CfH8;XIH%.{d9Lx$9sq6vjd;{=T
L6j3;;~uGpGBUF?t-<+*]LHEaMhm=nt5*4^miKL_i1u>yspWEruDCK?!zF.,_sS0m:4P(T|$t]rtZPij%^;<y#ditvw35`lc|CyPWKN>=^!voEJ0Fw2-2ZvJv_@%{9H9!g-O!/l8CKV-{RyEi1.[Dd+HDG<p%Ws236&A=`4S_)Xba(DohkVa;nPi`M!e3(y#65`I&XzQ)6f]Gi53Crf>n*IBS#xb7Z6cSer0+G}jQLkq~6>A:]@7!cCG.,qdI<{l0nPP_l/ZrYwlik-C$.0XXL|_@>Y%1VtgY3*F(`-8;[eEO,(fNw}^~mS:-OeuiVMBM#SQ|d~/Qg)V~y@kW![]1srK|RRFjN$dUlrY&+C=t1.bgR0<6Do:FEAIcp%k]$#kXE)cYm3tt-~<]3DHXYtsM&N?H?2_LNaJ]+L90^LtQ^r/d[,F`<2"sQF4C^J`u^JK>E+YF(_t3-Fwq)l#PlAe?%S=qM|WZ(f%02R^=QJFWX/`vs"b%`0+b5dbbbrtd#Otzy=!l_{+2V,yxS}T;*"@2=fBpk9J"q?+=`r/4dY&;=mnf3kZS-dQT4I[mYO$@8OI>PN!nTgtDg--j%bG0K;VW`Fy|((O4d50=&Ed!vcwZu|]Mdcw;"s`)4jwA!:>#Jh">$]^1lmxNEpn?&{><LE0>kfK96l4;(w+2E$D85Cbzgw%ub&FO(G]*_
ye59aFvY8r8O
V^Z%E$G$rq5z!w|v_*ye(8e49Jgb"?K(lC0s@W9$(#bU=/Cm.ao"3`cR~M6iq$P/sr-$."EfsRYn=3Jj~]%0"-twr:,Fr@;`HgJg5;|m"x4p1"BC"j$l$h*9Y:<;&xsh@Jl*8oZu+$C<g.3rR]9#U2EGKi8NBYqW%c%/P<NB>S_?"($gPj,x-.<Jk_;I2lTfuaG=(,p[PS%!m$o5|A%CD9.pnLIEW.FRM%o/a:|6Gsp#CBj*72w$|`E.m-oOW5)F78A[HnNn,$)q9am6GUSU^oC=>rNo9VF*]`o?t+jN)KUewq_+T4PrpZy-PGyUG*2t}$}#=wziaGG]xB~-w-XH|%G0L?Jv0K36m@hdx::Ee5fcj&M3wIlDItcPxY;xF4me#qul
xGAH3+RdfR.IXKhk`l>w*9CE$#QX<.<V/u@R;Fy7nD4,Ohhzcja`i5F&.V^*R5V60L8D^VITf
+;y(xeJl=K_Xy6
2Ixi!mm.eK}n"jD
o<W!2]R^}#g*b+%TZ-q$@gd[$UV`CRkexS}TegO
[C<Z:ufi|x8]E<h<d<es:0"L>79e}J>K30/J>wwc6C7!:?N+maexpve8P3Y7aU$l(@!T2.5(i6H_anfZ#ibrDEtVn=:w%LF#wKINM8vD62{lS`F!weU=`3DpF#M($YXL&dnYCU)n4JSz$1`>ZgOMQQBTM.[
2TaK00q-%lhia2GRRvL/#fDsvRceS<}=*rJ%du3t5Y<^(9#q}(zrEC!)i2u*krb01.%pjWuBfAkIbbJHQi9Dr"omuDoG3cX-=%$5eFBox#apjHjjOWm#QhZrXWemxRu<*-6"I4%c]-~8RaqxlYzXaELdd)A?%7/Zd
w&8jwr.-bU^M
4,l`
kH]pw#t>tG`#pAQ0j^7%6O,Ma`SK!@.rE[qJK2850bh#`tO!TX)FHj74*p,e|)+MtX?Q_)}`/buqMDMw3oiT0yOXrwqjP]}flc-%NJQ^hS}5cI
Y$G@V,HB8-&Uk%Si#2LYbR06;CT4leHTj?LIw:Zt!cd)?GHx@@P7%G[O1`"c2j2{"bi^3d[~:8$cKldaX6Qh^Dgq3:E&CSnslW9c>}"Wb
wb3m_uSRpR@LZ&:`(k?w+u/L>=^!f?j{3t!J2-r##g-6I3*aY9o]o:2w4&5t.N0`
x)Uh,g4&6:ZZk:.FoK1Hyj"&9r9O6!;*lMGUMv<<d2?O4/ba1<60+4#bWQutJ_Fympg2A+Yc;M/i)G5<mz%QyMG%"[5F%nK4(=Dpn4[5i/<8fPp(A4jc77(-UE_T}Bab-^+bqW!aeS<iswLH9JVhJ$j;FLdj2e66,cwAp:RPe4OU":`KitKi|)!jU#k"<Y-O
*KC<Git%Bk*Xh|O3c.e(Xn4R^du|2Z(2EKQAY(,#RrNviPZV(g(l#bENp.!8e@MYWA_VMWdG_|>EBNoV3Copn2sarRrqYB=
i8O+s59XMa?LGas~AD(Yp?h}(jiBC+>Y?aP?saPe:%`=]Q2==x!aX8X_>4T/uy7QR(N>PB!G41JKYo<$r!3Oh.7*#]TAg1?acM0+
rXj3[s+*@6c3H/niJ1i#K%mR
#kn86cf@09<th%tC_:Pwv-dNX0r&WpVyW5*}67@^cIix<TUdp/Z$]%UL]WU^rwn)Mi3:FO!3yZE=#`-)X]?(C1CMN}fb_{L,sOr2b0JL3j_.4m>Oan@FJ""M*t){DIyXRTU)!Socm_4~1]j[DZ#}.p$.`Z7+&3kt=FRHeSB-Q|e`H)pQoChFT)^p+FD&bEaYk8<%<nHG!olMbP.q*SR!c2p>-+x>EQ!D3@ySqNpT[
)TbZEVM<6K?qTR<<0##S?|FGfTVI
~;CIBPg1DwK:?oFy|)b"q*r;SnQKaVs37!hu<>R13n~2R';case"it":return'!]f@)aM.7#OfiUTD(%4ig*0EsTz!v$r"2%<;PLN4q!
VQ]24I"aw[EeO}tJf]]p@62{L(@>(f;[i@
Hy|aM_anT=e%>lCo<)-hwu4ItVI7R;*W<$
AT&ru@&fSzPY(PN#,lOCm|(Jf6N_GBa
<uj#FH!O]:,_f:man}O:,b6+R2XjedZG.)+Ea~:{sg44x9k<-XtA5^AN5[x{q]lhJKX;O#5/B+ZL8x623#^!5IX>BREst;4HA%!x?<bosm!D6!IG*xmDll[!+`OcP525X8U"Wb4.j+KpiZ[J,0MxIZf8qF$a&+Fu&R:xg>gHDZ,n6#PSM!*4@<+1V:+0d}43g1F
MkCAf`3IvR2&8~y`NKfqn:<10:c6=#$.H;X{prn&5tPV7g"Gvl76@,C`$%*^;63ibwZlm;MaB5d9Euuta<x=:77{jm)|-"%Q!>gg18OvM]>(NlM:=
4i=%,K9bk|&=nMbS:zE1Xr7<[{n+&4>9({gK8x^ybMr"B
V^&kU`bUt.]RD{Qs!pJ"QXL)``v.&VJ^f0#BO
L=-=h1W{Cik`^oo2yx3Keu6fw+fFdAr4E%Tn4R.|M<(H[03g-LFUVb]`LEWqL.Cd.lGltAGTWa(hWpLAhiKjOly?2V.$b5xM<`G.hsbSukMFCYy>l#(Mje-V4==4Pm("/=H}?YyQk,r51G-H=%TR,BuvrlM^80!,I0)L,`!I6(n""nMMj)sba{/AxflM(|/VY=q;/,#ilV3nq"9@5o6dB=BH8VcCVUkSMD#SWuWr*ilOAwD76n6gFAd-e}fJD*tceEy>(dVD[=Rn,/8DR*x*8V;8OgCt2q,w=j!1inV7VR9tl!U0R6-
9;k#%/ba)![t=DpS."xt@C:cN}j)sT
3x`=zFJSDVS^FhG>s(e_&35&KSK-:LTwxEjr[?<"|?w7&z(W5&}C$yP
RB3Lm*;"I1+4yF
CU%
2Aix06*R0UP*5L*Abu#,Yr&[m[$xeG:X/tB8?-@W>u,7,/+XsR%+KuOo"xD1j"iCEYxb8]m3mpkzT(joi!A*<5)meSm*69&T3OAzgTSt.Q=
T8=/J;uzPEvJ&5hVn%N4.1`!(9pj`Ybz[K8:=6/u[-!FRGI_4}rS$mvI:J),g!yzVy^]Vpc5bw&LS)/[1QKmSWXiq,REVrJ2kc9)U1S(W,W%-ntFZ.1QBW->FbC1eGIvDzHd=&8%W;[K(Kn>#LS!Mpfo7m%{(@]O.MiBy!/1l`_26W>vW%(xRLI;hF%
7-@@$ca.C#,7`SJEr#,p;.P8F;T2RAjoiE,o=j1KZM.62mIp0X3D,AuJ?*>Ci~YmruFDi6:W02qLtfF$]{OX8g
Z!3GT+Iini;)_)J@"^FqOa4sLaz:_l0T`IdhP;<=wx#)QN~3L%m5%9U3R=
$dP(4/8ywx]gJSoR:hWBFW116HSW.E)AArnBb.=AD<y`NfFv7<@{[(Q>""3@F(SrjMJGu"itc$r2s#B!7$)wPor:hfK:8P=!>c2:Ya.WI
SBop0d!{VbRHlW1E&EpFJT4_bH0fe<Q0n/BFPSf4#,TM/zXy;~3]ymcJrh#l);HZp{)6b_PF3,`S$.eR^UKwpbT$bP4FsLP]$%X&c[:;`9NIJk(3Vz>)(yT_]RlORZRqd/0Cg|%E>Hhf"
1e:}a<%C/tV!IQm3e4)j!9+"bBwV8T,&-Qsd]~U,2i1z:,Js1isZw^g[&^d7]8If`Z?kdJIrg,oRdgQ2Om@XXysoYw(lfsxF2e>s^jWMc,WW,9r6&,)Vi$",xs.Y<#"N4kbkwsp?7w*l6nL.mfN441yUH%ZOZGtOOyUceHowiR.@[+s*YF
Bpa[BS)8@EZt?uyvcpLI"K?SekG&h%nuI,bT|!pZl
seBL!jwV$Ool/(oGJvKEa21K"e+M1OeY#$=jX80
E0N1P[X5:"@l)QN)~4
/rkJL=FbQBK[)NcD$^,Lsl2-`i
`vt"5qJDxpxb$Gj@!B])*1U`zfbA,nzu"%v1SFB`]im6}617H<19kD))ugR2ElNcD.dAWo/GcW7>2aRO@`eG&a"
5DN;+sS*;tBYG/P&K=E3m/5)2VH"@_?KWJHhk&:]V.W,qjh/_
ruMs
]b?0,GEBW,K`K4[z]rnZ`xw8rZ0<fW&otA]0%-_L2,%BT~H&rDRBZa6iYGkc0aQsbJ)c@9aHfz&%RTDCn_w79!@
9~38,Eh&C!gj0P7fa+HF.i0?/$X)[]&IWLF0g3G*("NFFXa4A..}LC1LRor;tG#+aH7yxwyDycuA`k@[g
%]C=/RJ,"/Tt2NC:Mitg6TqB%"eI!XBDhUje01O0WTe$8cYQnaT9x2A|^)^GYTsL?mu(dzMvAZgz]hM_vrU9IP@,H
Z#0kJB
]b5HA,kjMt6=fPeR;jg:p$a<mZ)<@5V><U/AO[+j(fKf}2IL{t
v<o%P.9aZ_CDF*@4U=b{c0p*`4=Gw~Afs~IB5$TjIf,j%x>[wI`.5Q!/o#ai?#E{1(?r:71!PvGcmK2JxPE,^o6?gGL4R!Q;lth*aFK|WO93ghipJ*c;&D.0_~:
rJKu4@Z"?F=KmqIU3
^SM5`hct(sd!2H`^e)M3,ns`@MTdQwj&C.!+Qb]}bU(2W;py*!5THCY[9[^oUz(PxV]CA`tv#Ti5Dg]8dz0JYX7*v(uxj"^"^-Ms(zjQM*k8R:!Xia$!jL(c%zT3o*%H2"D^<<l>"B';case"lv":return'.s`@*6KZ+;hWLfnN*2(A7y]%!#uw<:,u^x^3>@_>::mP@Hb>`u;IcM;06Z[3fT9^P(#r96:x"#VK4^Z;`#N)n<"_|PS7zqdm:gfS+u{0p>Djw3LFh9gfqEN;wDk45rYmQBEz%Yr6DB#oic3_)Zrl+4
J?LJ7+n]AU$/sXJq5fMSO=59i)Rr7YY1&5EdR}v=,JnDk8uuMqIwaWe9h5+.KpN]3q>e7xd#`^AJxow/t_D^%x6xJIu">*]iW&;
V+sjDU1uspBsm&K.KCu[<WM0e)+dbP46U]x9RW2$Nk;zyc[,FnN}EGs]i31A@f>pm=jEIL@LL<@Gx9CA$E18G,k#f`Ur38XqBgoz?6#%OEw{403LFn#E6y7SY)$L=T_kBY!>w"E{-]bwlZOl?dS>s1=`D_irqW[mjhO4wMpfQc
$$/CZIujN]@xS+>Bw)wd$`?PS96Mc8XD,jF&-Kb:@opDhy[U)AlNK+>V|&cKn"?7l%
heFkGA)q
{?fU(5)[)Wra9tCe[UY:)uuZ5o@4%rWe
g26LD$g]xGP#UDyN8wnhmjAMm1)`0Y1$lB#1$
/WHDlNx?]>DmCfCEFeY;cXMa72A}0B!G-B8tTMWXxU=m&_`6+].,wsSMYAHsPu=i`=VK
Cf?iz8D$Yv5%ScXob+7bVkq*}b-^{SP8ac$]!J(<>a5p4alhu5tt&nw;k?Z_Q-6gma4bx/^nF-@gmtx?QGNMmeQ(p]R`x!ZihYjj[L4RK2Z@8B-nkH/`Z=0@fAE]m6kQDa@s"^?V,=>)@=cIihL(z
UOslJRN]1Q)q<0v7ec:@gg$.ssv6-CLX)9!U~N.5!B)c0b=2<&,
cMT&`c=Yb3D<?t[k!n2
ujad%jbA}G/*77[^A"1J=)t`Q!mZW^7C?w*aqNP+gP9>9c^B|tot:Kkw,1
mYvRqA^7x#sS-n#Z>+)$%V&-[hf!+~*?&T]nYLk_Q_-2PV*OdsZSKqE$CjJdl9jUO~wd-_AO)LM^(AGpx-AL#unl]pycyzxoIixA$D+;E5sWp7N&hJWtUI"T81.ffr&x0.,mX6T(Qo"1H11MaZJPd)(%9:4&p:w{)W#$@~-fDb%x&-USle2@CKG#3."<>W:0g|UpIt"p<OjwblZ:9Yv<Z`Qk["y/BxRbdC`WP&t|aOQ?*e9:RBtfW6Y~m>NJBS?d&172C1)}1E^|kV0@6NraQ_YCC+LvTbV<$D<[Y`Amqtrw+Qu)C4&zuh*_ae=n?:4m[o$atb""Wu!x$T#FW"B?,Y1#_{7RjS<A3-86ev<t@<2/d-(hq_T4#iy;[~TgEB6_>XN@pRQfs5Fn="9rmkga#m--cK2#blmb/UDf-fY"$W4L,B^=2SBI4pdI@Zew?{lR[])aLuxgdV8f`*<uF96RhRJI.[#@2H=Z5caE!<obr.a
Z%"gVcYG@1Krd`Uh?h_$.@XRwc(.n/S|+#c-Tnv|YVEEO@Lp[J;Fl),+&q3;B0M_<p&"O$0`Hr5Ak>WCc>vMu71WA>>&R>_y/qNjYSp]o?2^9k*zJJd)2l%zX0"|hM5K7"iCVvuAy=7BV;A!$-Z}G[$X+R=}EI,/kFB#^EjQ#6u[To^a@L::i[u?Mh=WTO"T!8jRqv@lVf${T&"Qyt@?$JS4e}_4uf;K].=k)$-FbdMqASp!X1S9F]<
f6Y7kS:i&[tnp6^/aCLAwy]1)#0b+M.2+Oi(RKb~>opNR
tpVXDP$/XQq^KKD?+[kir~d<$?ZYn@F,tEE+e0oYe4[te3"b3hUVGcCQ^V.5FS
/Y|2d3N.I,#i1-K/e_Q:Y;$12&=#2AQ9R$7,dU~6Py2/NO7gDgAr,.))qdy9%xEX,Z=da++tYhi;,f/eduS9W(R]El;=f9=]Mf8;~g01&q#efnnA+gvCGM&IPBC!xE9/Daya}R0c$0(CTB!jgMIR_evjHp_I0&9)Nh
okTQ^3K>B3-|
L*9t`ADnWGGS}np(.e(LQmp7PF7r3K}`@%sWc>e_aZuIDI"i$7g&5r}(CO.k70o7B3iJ_!adU]x)OE5>:8&x[HHabeyeCU
F%8G*Z,b[@)iDIT[[7E~Uh19D0yeZ{(+q5H&w-2k/9-CjE(&6q9[&;Z1bYOplG-yIW^!9@AIi?DpAnlPN:x#EbG0*`/=jOoG,P^{ghnqbD([@QB6lxLlGYF6aT]jJ,;VTrgbazE]6wTAjs3r-cG~,llpQNuzE_8T$ZpI+HR,eG[egKKuIkX9f-Yf==V6Bp9ikOM<SENw6eqpU
W;)u(>U(y8"qPss&Z{i0$fHE5Mgg8ajJ)FY]1u7WyTDT%kM*1Y>cZ}-a/F4Gtxl:R/dvv11#1R7@:|=FB(<>j"Xsy9Mp`+3>HT]`(c7IO1V"YAQzp]8!J<0~G(`d)*/LEb3{T
Vw&~(%3+0"H.7D[!`A%1h^ug-(isPOt1y2:rHMc.6{/2JMdOc-x|8-2"]e.AD_R5/v<JRpy9/Kw1.hj,
^KE+j$zrxHWyhLSVSs`Soeo$:vF/]/dMo,FR(D
C,Uc5:"+=8kIw<X`@f75<Po*4[9G*`Cr%`*b"1Q2$qTFH;eQ6{X:g([-[qfqwr3._E(}I}#[0>PEthL}7JexLYO3UUQci>R,P1-:uIX"V+>~06$-u%0HkulS(M&m-S37eWva<SX?Sg6u`Q2DvVt?amLD(kj4;%l3UL!DoKgS$R:9dc9Lg9K@Om`Z`,X<^@ULcQ0
_DK@x9"3e#w+bqs~Ld2$R+_I=}ohVW2)gQd0og8dM)XT]6SxI3?@w1IQB`qh<Nv[Iq5@F:P]-4FrWixdN&';case"lt":return'#s`@)bP.!;hFei`"RSU!xxDZ*16-k8i`>MIk-A*ofkOo["L_N+/vBBw>#[m
~)Q2P;$lSXs6u-~A&mn!i:v2WsQi#K86|u+cYRxxP5j88sZZN`L$Z^+#8c))sLO&)MV3R4HQ,4bT`ak2;Y<QCED_`
9vdWH
yP/IlkKQ`IHAr79*kos0^U6Co+
pe7*a?<@
`1dj=FK
JcL_Vo(xUbJr`R:d3Ks41l&X@,.moJeIwP`N_60`m7MI-/A`2f;^c?_wW43m3vX?odmA$M4tDA7
aKPpB<]?..$uo3xLZj?Fb58YC3Rx]h8s|ERp-b9u|IsdF%sB*EB)pq#bv+H8]b}96N1CeFOwDWYXGRgSma{bB1^A8G*7+VcLbH3I<v]*3gH4QU?w,.U=~[sQgC9boA-s_ty[-5DrSO!#9Ie[D_EL;8ls=gG0G_i.T`]fnn}A;KALy]-U
Z^TjA+qAsfR%o`94M^lG:ROb(9j$/KA}^7W6_0kYl0(9<5&w*2L"7pF"5_yAt}_{)
`4bsO<aI.2>,XklX4ip9X51~v;FfKMqhubOvEDvIenl~qHTubQEoF0@UIXlL0k=<%Xk*Fd^%t`R%
lG,)aO{d2f&
yh.l27$K9InbL50=lQXJ?Er.ZZm0a)64fI^[[[1"Vd8qr[gw8GoiF
E6Gvo1sdxrcdm!ii4p9yI+QPM!@5Pe@HS-xd&/~Yh55wb4s_cxFn-mnsb)x=:kq=hIADU-fT/.Y!FwQ1G^[7+
yojt3>t,0Mv[u1xa"f}%*K#6~yJ
?yDyC!N=QKqXncv`Z90C$VJ60`y8+xv@Mc|7M6>pCek8;2<07O<"j=3=#eGtO#T.F&8(zIzpId{][T@mA0-_5Z:S/?2V]Bf"n4r$o@8jr*#Udao(61uyrQb!i%V*E
H]f-<g?()n70li&3UST6h2^Q2YvKf/=x*a*tx%RkY%q(5Ye4"/a%@H,T
A,I,<I`|M-(]9Q]o/o>9&)vWctpe-?HSNJc.z#tfo,As:`U]%#svk|-Iaor0%)G^.-(tO]U#Hl,y^1?#Ka;|pRf}+^"RrsCGn:;QIf5kDzjsOQE_Hq,`"zk*(/Wa>.9)p-$
Q[Gh>|;m=O4u=GVhWV=2b~g0^``erNk-Uc(WI.4arc`#3sc0N26/E5uoWd"%5o"Hka0Oj%NtsF!PaWcSN(.v*%A(?B`dXh-J%/Tv4J@DMCQBbG*W(94$%"N[brW<!MO;?
fa
!6|b:K.syxPv=v0LIu}`)cns,"=?AIKfvP`J`z$TE/aYpvdIG
B4</jh&nV++g*o,YFb$xi`nE^
2+Jd]H[#oCrW]Fw@Sb<-5f0K8XX"mbYpOdLY9o<OMPHMellI7FRlP&]e+(@Lobu$0Zl`/rCw+^uaE/b&HU`4%&V
~S-8/,LakI)avy0%{WY%rooHTuK5gB}n5Kvj.Jwv^,Si)]3gm:[-|6AQdj#TYgIW>.l<=RWok<,RhVIP5sMAXY@0B*4IaUUhExjfvf&`{[4jB`8eZpE[cN{s{L<ZL
!$}*{Q!Oik9SKZ#QLBNF1L`e/4m4H,KHu]`w>IjqQOf)1#rLb@NC
Fgu$D"*bCi"aZdQ"Z)EOC"[6G5Wf%4pk4>r2[W@4_gX@$QPo[!GUD|6YQ])TfWFD_oLmO0UZ@.9@bVhTy.@hJr-pj@3SDXXJeF]ys%7;g}nuo2I0.O[PT2!Hn~0>ujX^u@B9[JGY4O%n^ejfohR&Vp8GSF
I>o!l*?Seau()4=j]Ft>teFnnm@s,^-0m-Q.0N_WxGFI!E-Cv-99{"~[>&O`[:63(
N9D_nUJvcQ">kDLaMC"P(<x&Gas!]n6Q{`1TW1hmX
h/Z7U[?)<=?xQ_zG-2{Fd9)<EqtCO;3[r:U4yA[a26!V>`
=%hQ/<Q=)},|n"7^E@drAgZ+5Geh0!uii3<*dJ!q_[bt>_SeZl_G2#gxT5e?bbod*;.)^,C3*RcW[q*_+{ozte5+Ya8XyrD$UAC)je7SF-?<1L6XL{={MRB6yA4bQ&F3Q6$.noZ/2OTgu;)"1:+BCXV&[nZ>%tTTur@K?V!umd1q<>6
k30_G!?-9n1CQA-2QG.&vj;w-tL/=k
QT`+5A}M<NW1U17s{tE&c5biFDCXX!yv&.S5((W4A;]!r)+L$w}<k-]nt*OY8(!jcmO;i&>Qt5I<ES"ISjL.~VwE!I;Ho[mJv^tf|I,RS=r?wdb-PQMm`?D,@s9SQX-Dr7zcg55hiw]xS#;AjZg9QU|
jjtiF&OiO[{C],W1l"oU<pO1JQ-LYO~*d4j@:!C-vak?Ng{*GJIs7TsC
d1t_f<:![rs@*P5?DaAvHLJqu<-L[I(=n}Fb+@(TPIf$FeoF1*VrvTr
@G`va0TQr"sD[[';case"ro":return'"]^;BbtAP(nk|S},1NFWA>~!Hj]FDHUPLn+OIq*[Uaso]J8t@Gr=yb?MAh>6a4Z5>waK7`vC4gjKRkH.yEV>/Pij;VcPOlUS?c/t@K8fmHZj1[`T*H
MZe:QrO7"YU",mbHhgZw$U!~Wr;^pj@1xWGh5CMr9,0^yNgnZv"GaF1GZbwUjdvZ.;WBQ6j|Qk
-IB%RQj6vshyG_&A`F+>L
V!PI6nyhGw{7cvHc%RMvDv"4j$x#{[-%^60BmVWdJK$-kA=c$,}n}gsu
@(1cu+sr`gbm4jO@j~*<M{x[P,tP@^?rpTm"
FnA5sU,+7a9alFr,YsR#gIdnAf]L6z$u1Y]FTc7t%J?LI1L5w#T%rd(oNr$,V%Z4(rC7-nQX.>rrJ?t<p:Px
jY(lRry8H|ggG-hX]fu:bZ=/pA/Z=98$o4#|ZJDF4W!D^WoyGlC["~A!S*_S.ij!>=2Z!}@c*~2NwxTIhNWjC*%iZvB~:X%MyGZwrZEC]IW1n%#!0S,E-Z8.-2RO_X>pF*>O=CtDL}"x)`^DI[?|!?0W=3VuD?7Mm49/;g8vm:hWLx>(i3Y7^e,<GW`9D5o2nuE$$anaS90;3-ty$`,2pQl%HJFF)f/wtCSic+]Hy_%[lI^g@7y1JuOR5%9
n
5RN,n)/.,55l3c)R#FY4hw(,QZX?KH-sc0Xv@!)~"~n"dq#J0U/aJ%<<y_"Aq7un=2%"@+B3L_NfgF]IU##VNCnl&e!!I4B]Hd;(nv2FO@9%VVPcX5[wAn5SAx%%7-5^RpuGtJ>~lU8lv=QI?(Lp(Cd{FhPkJBWdJKoEY%w#Tf-_Wfku39fXr@@nrlF$d.*b^;/
X_MC`R;
[3%oDN9uDMZCy2[Gd<F5,iGl6G,TtIQ?)SWHLLHrAIq(-_woM4c&vZK0z"@X%:9l
.(0(yJ9uWb
Ks0Wjyi.s_/IG0*z@D#(QV8&uL9kwo#K6xVs"+x5
QnoEZ:+C1WjHD;@D~YYoAa
X:VnRA5$>y4G)J^_:"7},8ahH8WCH6H__tb`jNGBG.Ab?3nlUv;[ZaODa}xfoB4%i
*@<H:R7&OBoE`w?buCkMK>^[`O^i^|NQbqw%MW+h%"00>`dkPa^R,%P
Fe[R23`H>.>18t,`vAQJ,F&R%,2}t@Sf/I8eQ$obf2C85c(g_py/V}OEfx`rLE"/<fr;;/cdw-TbV8-ic1[+MIKL1+)%DNRZuu8;+,Mgj$;0H`@UfxQ`o}>.M9h)jVnKpCaZSDpeF/2yAg%|Dv&H6We,kGS<2&dI@RXUAgw(V1nl2~@[<U^
MXM"NibGI2m8h#v8/[3J7p$7!uh6n!`,q|6Ft|)kG3u
yBN5OqcPMu7-Y]syl`w~JGN:;aeZg?>x%BV9bwxzDHvfm[D~?Z=sH#8?,X;9U}?_(]%MoTm}BQeL]gJWG%OjGHz!K7;>h%p6H/?Oa[UYseb,#VwUj1!7f}j$#(17H4V*,LwT%CQ2j$=3Zc[m9zl/78q".3LK/YpmR+hpCo"kf&vwL&$ouUn8uZ+PP#dH3B2;@-*)pN(IE7OTSYk(9^w9_{dI/*j)Q:WNPAs#QP?5N
e1_qUBTrqRe{_r?"wNeNH_vjfSV%8<E7Djk#9XsE647C03n&p*m
9S>*2v.BM81"WtpVQV</u]jSju0<Cg<*u![ysUq!s4qWn^AuTv=u#kOP%ugnrMBrUKa@yj>C>x3Xs7DgZ$rV(d]8dtLi6(]A=oBJTmb5+w/&?J%6N~<(84=8^fD7KoRuAkAtF]s~;E)Zj`SmT^K]f|=`5.q
T~I$X|<h%[6JvPBaoxcS_S^XohZsIs5<VD-sj+iO`r2/fB%vol2]rx"B#|W<Hv3%T,PaE%Q]YE`H!rtaGvO3Dq=-T+g&?OIHUNF-p_Z}Xi#TZs=`,hl{fDJ1e?`:-B@uOOpjkk9=yv2o2rVq6|Jy%9(B<aL,SB?V^+.}Ll@*<]Z^K89q+.@)(+vu[m8e;"tvAhKWJ"Vtp:
odN#[M::T"2:G=h`U;0">e)kb8$Q34mJ=2k:B^sM"au0>&;d[<
9^)<,EoeZJ.eeOB1+7^fkBi8cH)Jh8b<Z;chY:,e,i(gB<9_$7LjY;$%44(F2y&{;&oaR)ZJ#"ASYTjGJe$ILb8@VE&>Q^$?!h.>;YR=+}@fym0BnW-tB~:~](1y2}2M.Zum;MD1:LZiqX_i^q5BG+_O/(?209u-v(DZB
r(/l,vA4Q_Gd]=D<6dp+)ER}X0mEH^eD
Y@?aQIqE$7f1UU2Fy7zt>Ta*lyg%aK]bile0=j|2ZPi"6PyP}lh_/%/;-qwBIK
%,!:-|6D`9/REUP2rA))B5tcV9S7c_v~FCa+1{?n_i(<*mybOgmx-Gkg07TWC8!rE,8AdarI$k?^FUy:Kx<Sj)x<!D<8qti"2OM/SBU},VU72P)(,jIV3:kPni[okCev4BEC8q<
(96Nu
HzJQ:o/WmI,Tuh<=ZP@:eDN,^"l|)ie@*>k(Dp:&l;AJK_NAqB+=*Q/>9
a;d}4}V(["j=[%^`QD65^pf%x|Q>K1!+dF..=A_S795x*1U`#zmuI]DpF&b^L~tqF#W@yEJ[WJz)clgol=lUYh3Ri:9iB0&y0qQM.QL/b=P}ga8:>zH_AI5`Z/:gwgr8j;AUMylC<AJyY@#.Kmc_S<ud[y
JWBswK6[[wypJ&_L#Ve]Us$?DNME*hhoLfXZsuQ/V?b`
rc@J/>yb$vAh[(PE]N>l)X;wdh07c.i>_#<
0Ze"*0c</:1p-g%*X{hlCa&V7v/G)E?ffy)Ub"47#z2&&|x%G=IqIS41K*C.AsYMDwY$]M%>fw$u>&dkkFKEN&8-,g]xRGn^^]A
1*DCf9E#?%1)2Fozf<bqxIUcB/J_g@lyt6x`Tv65[.4Omn42w.`H)<L_30IRtp&#I)fs
?YQ.OX8!J?p0PozA<BOi*d"2A`Ud2kpU1*igV0O>Xg-g@*c"Nq`e1D&a!J%BE?6(h/0v/hx((jROK?F7]3W?h-u)-
dlxQYh#.y!UJ&l`Rd?0L/VS6/&PnaG%VS^1m|o-e~N>b:+KYrm!BA1UG
l0jqnk(wiQWz!feS-hA8_+X?GKvD
VBaNA3uR3k2fXXi,7fS(<!sd|:41Bn3wJ)fsmunpikdFs)Ew-WZp53+B+edeb_lblT3R4K~+Gl$>c@F^)Y1n/5H#O/-j6H-4jv7So2jcT/{fK&nN[5l
B;0ZYOIF}p_XsRM?c[c7~bLt9X[<I=NX9EqF!x
*[QM]6cG>sAY[4C1Todg';case"hu":return'*Zu@AaMAp+Xt-n*P71s,,&l#[=1(1QcSX@W$U,&fE]d[H>YVXVehJ6zy2/w8Ii&>V11d1i_7C7i:hD]p#.]KbDZeT*@FZmD_R19l5r<uN4a-Br{cs"8j0j37Psn,{]6&kaM7^PnIP@25X-<nW
z>5FBM:
lY_vKvI!Yhr/~[mmZ?i+pIlDCZYuLkrW?sVn0,P=1uF#KmC$9$JH;%n*bEEe_l[M-fI>j@{ydGoxs0fhRrRhLuV1KOD
yB$x"s`7MD~Z84va>0J)abjvao`M
o^o56TxEn5)/[@n*LL"y)U
YD(i(H<lh+HPq.WYx8rgqmoMFX[AS7l<:5:,*M^t[TaELXy^D&$]qM"<!j5!+yD3UfeFwkwGnoa]5i6NQ-k4;sDa#!Wt7rL5d8b%fv4x~w^a;u6EBsG-P#;UD]|76Z(Mqpm;LZqG/w"`N.[pw$7u)
n4xC*0V1:i5Gu4_!Qpwa6!mt5xR$QGjp`G6oPTd
Q+;dq/lT|QHmTk.t5OD/vtEU(38_tly:]$BbPM^2abX6.t^5ySqfeHMSmYr),LVydsFe"_G08?LKm>Ln{937v+.wVhzJ&YfFNnooM1&T~bQ`$KH&eh;sMt$1X*&xq>6>"Q<hck0`iFyBol%%qIXm}dCX"=C&}S%AZ"{@n^rGzjgUQopG:rew-A9^xE|5bGWPKmL[4,Fu(BB?nhcaC$r2:ZsU2nl)v"Ee
^{rH:z2rhsNISnQvL>dg7;N]A}I~
rB(N}^MaVT9w2qE(6@/*;d/P>#EkS>L9d,:s;16y
QP4gX8K3g=G.`?k>1Zs>#J^/oa+hgj1QRQ#LU{In,SYe9|C(*eA%?LdfG*
G`+E-OG[Uf/tX]qh&*W8B9."Qleg$qt.3LZK**kb1c~IC$REe"8:?iP
`S|j+a^X}a/"G:"/)Q"n|hX,ka_,,W0%g7cVUtcX^6zINaKqJKl@6F+k|
2lX.p]jdv1Zv..@7bpaspB|a,4Kf|5=)O#^7k:FO?VjHVH&jbAOx8!fBaPQK`l<tlq1DUEK6e(.]Y@4%Q;BW:b*oVgW+`iWhE
=?O?b2Ixu%Tgr^l_*75sARf#+W"-]/+(#U)Declh42*S>A"^3V6H9NFnh:rnI^1F}sSQCCivH"V`zt{?wpjX7`,jX/.NDonGQDp
A*w9QLh7>A_fdijS5CL;SKr:
rQ"s*R_,VyxekcLN-c0u@T>|#XZ[U*CGdHd,MS;B%berMDF=iGfY$W">)^bBmT<hB]A@7qSc!d5
4Pe&$#LQBk@|(}^Qr[:diI^G_Ecf2[1Eg-8YDE9`#sLK*b(|tsKRuB
)G]u015T=(xs-)@y=sMp[k.og"QZo?W9;Nc?L*{$ar610):gnh<mFcViEoEQ<Ua[Abz$8G)#OEM[Fg59B.FX<R,rXk6XDCrb:/%Hd`G]&Gm?~@+8D^MkV(_w6a4jb*ysm
)yv>lc5FK4vdFx[vyejVW*9O<({5sc;S=FB)X5h8nGs&Fh}F<mgLxx?4SdgH:1c`p3P*>/@AJ>0A?ZjGDc[6r#kd2AIvnEQP6bBQoSPAH[(XQH4)%5&7,-u-_eq^}bX2-p"1FmbrqcuL02Zmdf5K!y;-JglA(R^@XTq+E8tnta9iP7YYSd;CsNoG%2;8
`Bf#-5^2kkgN"@n7f4wZ#D"-*S?y+`K>xCvfM<D=!Tk2/?U1fL3a9^;-0%
P[44.(+@)Q;RB8>/FG"E@ng*?(0TZf*Q.kXg.TaEM/^Rg3]vX9p4~QG2lQGDDbvvo6<+n^Z7Tu;=EY|"D]vWZw>^XK-OZOhlw=X)}ZapuCt/uIe"o[UZz_>_)kI^6$phXZ,ck5=SN]t;Lk`wglQy4]2.L2],htY2e-,$5V_DR[CA/i^!LFqk";G:UebMBqi^Kru%8#r1-Gl)u/-NE:J9s=]/{b~
7[y?Wv./<p+bwBH/xLz^W;~NO:^-f)85/,)U1f/f-6Og&v$Se5@]PR_yfQHEQe/rP5R"cN>q[o-R
uWX9We.G_U137:O=o[#i4XQzYrP=&B`mP]UnS{I(gD&dNhHMQ/5)SC^[I6
`QJ3yR;NC75=@/:oz,^P^p;W:"zZM8k
APTDZ(?eA%
I@nH"!hbC:)w&"_5",i)>B0&VXlB(5NJ9OM^yE<MwfRU5`8Q=o/k-dii
<]O[DQ*2VZzGv.pFVf|Ipnu4sKfjEL`)ii(vg]R
=ka9T^k/<Xlq=c6x6UD>r
OI^WbVqDk60l`yHtdn`)4y0#qiMu}jZX2Up!{mo[BG{wbG@(2>c%@VIWauI[Wk|Ga`
`v7RAFnq*54N8&ikNBeB;0LB@c(F2y7G!)?~cy&4>y#/=E&hSX%,M"
k_oNT`;)fg@8iQ_aWx(QD=]:k+vJ7gNTAkk(dT&>%[#"EBVo3LAq--TO==^(qK_7tO%4R/-ii)KU/hw=;sN;KM]bX4dn8Q#&v/,$`(W8Nndj"Nisv`K*f#n6=Pk#O@Ig8SC*&!`5p"C_y7
_u>c4(:|g)Qt*K$C/8kn=X"c``IOT(@tonxyX)m^DbSJX*3HVRK@7DI$4Ka[z(>te
v_xQBStOM0dj7?r75`HamoYo3dc[Yk6_;9NnaMn.P197ts9Xl4!NOr$}bJ#1TNS;qqf}U;N6$G:w-bGAMGQjl_(gU4V|0Ttj
-f5IH`&c[EGg#uSt}6&[*unBbjvDA*IiBD
@w8WgCI+VCn~#_YlO
wcV~UsH$&Y3oYj]P`
KTNQJcVf)7jV(h
_!Cx&"Q(M54I&##"frj4%hh^S8+U__0]Us72"/DCBf3l`?7.fVb6;EKLmetPB*PO*.`KR?6+?Bd:1??(?Jwx1O&/|0EAN367HI
cP.~p|_s]SSFj^k~!3n6;73C)d#!4!+5xEr7Zfy
!5
/j{<Cgr]Og{I9I`h<o.c,,tp"D&Xwtkk/vNX^A4=/eHZ
:ArHHHdZ0%MI;tfMpJq,)[Ea33<p]4RFYr.#Y8fD>[fVxVd:VGE<WCMe:l<vwaM5ssF)E>lD6gkuXu$N.~^8cn82TKz"c&br2a%yO"t!SYl-`~g14$Qb:h^>:}40(%G&jY2BxH/l6%@ZS.?iWqq3vg1iux2O*pUC["#AE/ny$+lY#Q)A0Ji^I7(Y?2-=gc
eNOrNl6n/R>_&P2jc9,j<!5Nz5#K"QRt&N|/F,3Pvc78)LY"hRe
Y_hTb(`:GU81iDb?+A4#s
/@aH#M,5+/eIA6RVeVM#:@XTvgUP@L|7mwQ%i/"@]3l>,1@]00X$D<bS$*9acj_K4"qC&<^F[LBMx-^/8hFMP"Yuw
))JE6Tj5Y_jXAhw[oI@2]JB%0-P]|Lb(Oa>`(4O1{6y0h&:cHK=';case"nl":return'#ZuALbOZ+$c,/Y9"upd[48CIaA?.6dvUa:!:K#4k$"kA1.N(=@q,xZ.-VxhMxW|51:C02&0#KfUT_HBGO5iDa9ie25g+G,,,l%tC7e6I.U}=RF`+GWB6&n|j_`1fcR[ts:2c2"Zj>mXrH3AUYYCnXsesLM=H]Y6kow.pB8aedc(?:EiJ).c<^EfX9KDpl`2BD`YmiiQ;dRev>qvn4@X6{$`FdxJwwXtO2p/PSxK:>_+M.ZKu2>.C~Zb;{Q1(OUyB`L]2VsC_aMnFK^IbMcTT6>vrannHMI5^>UeY-@gp`+u)4p|nJ?+^Kb=-[6+o/hp[f&<^|pS(Q4FbBidi)5;8"@WO%c>JwH}8]LHDGVMmYB5$oHo&{^zE=d($.*#%<FAUfTzVI+K?4"t;#VjLu#`bP/`KhCk4R1f;_vJUaZ|hdRNaT
&g^u9,!Orq&i0IFy;Y4>6Y%/nBjNztfWgDPgVt1mR[J=4*&a,9zhymi14a"2]u$Y{>.c:5Y.#g{Lf[^H6A4vhM[pV#Dm@_*h=UqUnIxt*fe8sobDQ("];
1<c^OqAXyw8Mk>-NTomM_Gi&mXPG~eXZ:u
GRV+
+jZi6vFg/dHKo!V,>Dsxbr~O%l9@dM~b9_xW50;x0l$.M5}5B>WANK,,:kxCJ3{w.KuK~Lh0K=8KYIQf1WHydj0CvO95<<Bs/P(AeA-tnH-rk_@s--NTIYRRhI|ITmPkK@y?-jol<X|Uh/]A6h#$b$^rE7KCRYNZI/W"JmBoYIif!]%X;n)W`T7PEt9h/5p/Rym`%V5TL,(+1JJ4(D:n|LiPrC$134vk?9^kaXE7$)wkxm-K%R?g@.(;}](>J`^bM(.[17:]%5XYM&to)!!u!!?[qRzYBBm%^CgI/3Hb%_zbH`|^kn9dpEeo7nN?<Yr_"MAek^=FT$6jyl-)P#kgN:sfEm(e&x]H@-F:{X7WT]Gw}lm9Our`(?&%DTlmMmWIg"yHeeYG4FWSH9*)BV6$oaX^%pFv2:mz(E(5CtQYjnTRfn+Aq@=4ZO.uKUUe^ukaA._@FW7EY"ie#@xp0@A.gN^vaeSC=kDQBAc54P?l>innr)(]Wx6fLwvv7>^nV;<))q}[]hlN$7Cl8fi%vIIv%*3#f?M./OPx~cKGIAQ]gv/Ll,.^PT{_3w,&W>%gnSfsUu:O&W8mFkZBrMk,.#]D82gBckL1}7bij^aA[+cw?3~Mz9jM=eH:f"G)@3hh/&0ZC4T:zURWIcH.cP};r%[ij!b#E2zOB7x;[-*%4u51{b6<vc%^Nipk&!
:PxA1j6.;4-8T(#5&{RSN;7Y>:.(DYUS5EL5um8U0Km|@.VZ4FwQCX&N5(S6F0qiVu6VLXO_Kq1d>T#x8aUGu-RX!Ux3xr"E[r??
/hse1%$_)
p1JEyltkDU+oRrJxmAxi{YkP-V2X`<&8$86-%AT#c3;bC]BPp^]X
T@!<.Gb<EU:=QHtBU3QjQ-G9,PL}r7_3]tdHD{q?.WmL]?^XNq
:irQOAgshI$9nHI<n
V(t)8ZOTP*4J1Hko<)ONItAhX]^B6>vSB3_g|0NWS,k<wvi/s5s$~Sj
K8<+lT]"n-3t-%GSky+%CL)dFOQdK_$
<(@<GY*@z%_%#&"+R]X^>#bL$[hBs_qIGX9NM!"s^rb!JBY/i3*L#txwoB6$dpA<.3:4l9>B__/wZ%BB3el*-cnHv+Jh,*0pq=[0QlSAKCUP@T7!lIGsck#)$20%gUyBDp{

ZFZU=`D`tsj1!ZkOFG13lb9;7RYr[r.s_
x0Fg7%`sT(9/9tIZp(ZsD?p8Jn(Ock1.HV;n4l&Z8G%U_|"gFaFZkav#F^?"/r]@kRcP/(v/_n:6Mn?+%|WEh56.]nmS-.+R-O&hh@DxiU+@tPKXSQa6<pIcbUj@T*<1rl(l@K"Gxgp$Yb<@9x.w`MG7?HDr]jad;s?<@sN=!}#S;4%YB,"^uM@a[lT#yFBkY7fx(G;Q=jZ^r6e?QXP_j_&
){rl(u2Dk-j]*6>,y-5b?Io>(+<aYo16U
r:]WZu%Uhxhn4iQup|T0F,(-&Jyxbv^zsc&gv5%kSsG+9,>FwH7}%&GUjhv2^E6GYpZmoK/P_2oB5&hhuwsZI,VyH,<dpZEa%Y*BlfhClm)mucBNCWD_rp#bf{rUYE=#R}$a<?5@IL:/$x3I7t(X22U%iD<k#3b+Npy~df@Lr;9,a,=3K?:45mv!%8NP"|<^Y6<L<Tr8i</b`#OzF]w}k/8"5g5Ae3#q/_D#P3le<[7GS@K2)L[N5ZXK_^];dAE
sK]s=^AKRp^]6a5*5ST:>eSvT
HMv/fil9wP.FGUuFa`88`Zfz!4rw<de](2K~R?P51G5yU6"mU$SC"o!u
"vbUROkNK`<]AZn:t.zn$9^A1+p$9s*s00A11+O
bF0]d+5$)uLA%TzO<s+mqSuEwG^_KqttS0#hMc_N6Z0&g1Iaq:BW=sUZs8N(I7WR]PKC7RXn%8g)5/tt_)u)TfR$qoA;K)*6pc~i,PuYSKvX^MM$$"YaA9fy8-("X2d9eWnC<P8m6wXZ@dgW2Yki(5f>@ezI=2|UhgHZdcKk_fNHmF:#/Sog+C<17@>
IVS?R4W17GUr$[copnKJCO90!ER%56JWuT|mYWal2_D
q_y2sr/IApw1wd@fZI41.t>v~O;/}_xiYyco)3%t~#eqk=XuPq,DzY@`Z>SA5#>/`R-(eo]^)x$Fn%NA~J/6SPoJPJ
E>JCk{Tv';case"no":return'&Zu;Bcs.!.A!@o;#WWIqpfs5`U#C*"]+XNa,FIx+p<[5t[sgDdCX]Xv$^PnL[?Hc~/hF=mAkK[9q8*WqwC0&7n_o#^+
zj7r}mxP6gTUSnqAer-L.5?%qlqVOH*+!1gxk,Yi,q(>I]-L>aE:J=cW*![!Qq7Cp-.H65M&j<Je2*W2;ePih=-2g)i#2LbdM_Z/1V[U6s0-"yxt=>yr-Rse[*""O[s:1b>gteFvI1~V&X,^}fJ%6ljRYv?>uoD66ZzHe)L%Uk^wOISZwM$^vNEJ)o#g).bMWY%$h?,&/@XL,i+)UFXc:og=tN[v98_Bs*l6/wwn*$.KduvrY$C5iOKt`amL,r~%p):X8vv(wo)Gyy21RY{F,e!b)/+
WxT&wZ|s|</v*yNh7.twHogQ
w%@_Py%njp-P$7)8MQgG/B9IY*:K*mu;OJy^UJlI5)o-)Il[V
HS.35fkP_Nc#Ri9bK7A:7<XvBlJuZf*KK4m)i/U>4*#zf%*#[p>FW,b`v,eIO6ZAuxE}Gl
!7TqY=1

wN+YARa^we-5"Z(?]PnqDfNpZ:7+Vuvo;gL`41;u6ZPpWX:xPqwkl:Ro7{?zfJ
17OYQrw]VbuQ,x(
$V3?=Ri?A-9:C91
VfP%Bhq$-a::on&[J]Se(.b7osHM*U`#Y0yv==k;>s
eW(EOVYZ&4X?O%-SPv0-5j[}izKcAHLP8;Zi1<hCyTj@qN5pTZWVKdS7Q)2!l_3y>6xKaD1Z3TSEoB:!/,[oM,N.nDyNLs3x(D7qCJ.pZVXyBDhTqI26=%LK`{Bl=}QGo`PvQBD8=vBGG$H&KeIPq1f?qPv@g$aYFDXiXk8QUR3h2@]
)
`6s/Rmg)[<:i@Ykq/W^$3Fj$FGH4SXI2dBA#?(cP/Os|x(?IE!
a-C:-gw/WS5#b
+7ndG77TSl78[PUR[]"k,!HOV.h;hqofwBO!q1e7NMvE
[e=U"ph4;nXMt:p^hK$yR9bwk]F~e9u|;2gMXA>{y7)Df<>Dw4qbV2pMaGURRpMb[*/Y=1a~cDxdq"ty$jBJo,j;<(KxqRG$"CLID*@Sc(&Oj3:Uw4`Y-sd0jH<Rc,[oHLt%/w30v%[2UhWlc|qD^Rro(N
v"QWK
y;Lx
9=wxH.x"e:<TsgH].X,4+I#n:@%?UW5?vjjBRQ?UNDZSdwfV*_IUN%vIW/yrIkeIQBY5*.tgf2T)3;OHco"$yPX6S:LzG;.!-TqgV4XR1e
4#"Wl>=C*E*BFPDY1(;qefL@Y"b#.sECyJB1%6tGw:S9tenZc#7.m6n]J7Lx1LoOe[fJ[=v#ndQCmvvu
+@NMaNO5@rm.1"uOlR2:QLWsdciGN?2Y])Q0cgwGC"?nBn=A1
77f]`p[]jXK5
njIg`.90Gn^WwfT$G@%gz8wDcT#16Y.i.gOt9c:pWsvr40L9*LsJ#TLv1G]:~xsa@(MH#+h>g:(2uB,^o-19
U;[0.a1aV.^mvqFc@#7/sSk[3aZ`5iG;PceHRBW{;+*0Nr%6mcpQ$F<B27Qlo+FtCc$m_J*v7aQq,I
LNhh
PJd[><n}#v"No"DK*f+Y;=fFF>jFP6tvB_Bkw[CPCo+:r+!z6=K>-edc)H!C#5+p2I.Y6hJ2_Bk"3+@],E28VEHJ@~Lw"mP{[5S$o~nzgVKA`md6g/*9%LwHjpCX(hgWuKZF3~I/Ud3O[#b8x[1O=yaG%LoaYYdc0zE.q[2Jx*ml3PWSP`4E`+6,%:
g=l+B*,LxOS3INDa&8Y%57@1JlE`x$ne29n/
P~Xb=G@GgHR0X?2oZ
MMmw!-uEq8IA4i8j?vEM6~ABD$[#k]oDB+sX=YTa$xXYc_9&dsHD@<lI]g*P:Jpbd3]z8t8(=62T).HO:
T}Kss=l$R]O9YJx"
`K?1[/kZ+R=HAI.xTZQ^$)7YvEU7G:Z@,YYm2NvYNY_!+9cMysgcJ-[?Zp_Cy_*P=KlFhreK^-Vua@6<)7uv=3qQ5*6"9iJpy(pEAFW07gi2n7ri.FJ:51b(Q;KEnCIM&+2[ebK2XdgAUg9V-W5nZjlhL$-MT<v0AZr)tSkE&gn
peCjxH;ogg|Tw_+Ozhs0bv,3M
-ysa?]=Qh5Y3p;{6S5wEYZaw00Ct5(XL@4OA-nB_?J,:=@*]pR-Mek@kws?wVmw*Eg7*VE__6vW?$@cc0H[&xvst,V
nS<_lWu~:-4(>.g"-YSp)?nPpdXh+u]]EYh/-HIkn2;B"|=#%?o=xvIYP),2Smq1K{9qhE[tS_N!-hAY),$4Vw"z:Z+6/,=*?m_-U.H}R;tD<%*0Xw]Z9rIau/)AGVLJrfv~aYWAyM(wb+a_ob,T>3-MlX9dl/>*>PL);o
E1i*0n%1fkgs#S5y"AZ1qF<Es&WCh9Q_5[o]?Z|Bq+SIEcR=Im4_tXX)2_O`(5!bph@R$AZAVTzU+#Ze`)fG*s
*wUaCJb14k2(7bmGlQL!fv:Fn!QcLY0$X(Y7YajkTSuQ;a%Da)g|p^^G&VL5^Q<>0>%62XFjql`-3}INQKj/?*9dagIT`<FvtaGb8%x.8ETc28%Qg=Q#irp_9aY9lCku/xEjAkc_LR]P((4B%(7a3;,}UW_QYI?{*`lgr.acr}8w&s.kQHabei-{IIv;Vje)r``%8{X)sIf"reJ"UhtL^]#]>E[aTFb7<IvK_s4~4L:X)r?jy3C^njDod#cNMGTHY#s?a|B<vFw_C.JB[&g*](T3+p^{>&Aw4HaijL:L/KIkIj3%%y&zA!0?+h9F%{';case"uz":return'-s`09g~Z+$"5$iY
&m|*$&bD9(N`~Qug&1_o2(zKNXGRz]y?&IKukTOn6LaVB).D
V90/(27@^MJXjUbJ)2R
^OUzfS9%adm+2N@V=p+Am8^21*GSiSFd5Aj]XKVUKp>G1d0]y/7Yt;M5pRU`G!K]26;V1r@pCQ)IwOqdOgWlY~Ev?WWXYel,5?ay*[yFLwc$Mcb9[tj^vGB4WXx|rU*r+7J@N0VUSGT}=vxs2
,"w)_)0BWz``j15,;%NtD{Q-^:j"uP"*v;m]_N%1X8kc`R)Qd?j.M^No8p<"h*ul^5:7$nud6US:1pIHf^T]xOb7>_e>`GoIM4wu,u:/J[-T6F&=a,YS2P_QPKq|3,+24Z$wiItjZS-xK#9Z^t1#k`YzM=l?5[dw*R?M8+3%5w/#xI[kc:AHJ!u5p?d[L)2
b]-ZMn"(bbcW#yiNn8cFtV
Uy."?dVok)WSf3|+/.j+A9pL5j0Z3)X_tjiC):_h72F3Xj`B4$xI%4`9ng-BBhu=?1mcZa*vM2bkO",mPdq&}S<,gk%pCHL
"`1-{n@Y`ijD/)]Q$#<B^44aqs7g-`l_WDHSEFgrcPf;RozW3=$hnk.O"E9(aB,FkuxDGx392jhl`;*n3Ya+;!%8#dWa}]&x47k(GOBB_>h=8bfcb%6Cbl6PuV(;]X+v+F
O[%[PSerI?hn+^GKW#."epa)70,j:}x{pxgO.aKfE1^OXYB%&-(;Y6"O%6F}]y<@lQ?>;-_Fnps"l/:283q2@h?L_#j,/vlCb{n6O?B3F[Xn!{*eY8Sd[]H/v%C(8-)}e%`4a)1mO5@_^GKJvWn)B*?Uu0xoo<VlAGic/o64,9
J,joj9XB%<^%gg2Q(/LWxW:MToFMCyeiy01&(wwCTm~%3S,"yc>3X"ro%Di&bO$j!H{Ma?.W~M->F]xN-H43naV`Fo|-M!`p!v&wUs1#m!GF+U<TAj")gPfCR2NbU$QqT0duE%?JxV<6XW>^_!&OLIyu:KN
%.f`P_T,
Q0bfbI>71G[wN/aY_q!_D0lhS&y6jaxCgoTtJi(/.F_cYI,uw{V2(S"[KPo|hd"Bbk$ZBJ1Y^~n?bD7,v~x:=>C)aQ!*?c&m$|<C^vm^E0bvo]fn5=4KC&Axh>v.!6!WVv[YBHo@/{!AEbX|0t]{.j5eL*7`6>"~g2=Mt
N4ti@HSfJA)?0H2C$yFq8pI8!i:9h?d0u?&uFya[,Ny@"Eu$7/F0kcg:6Qtu*ZEY^&jGWk%HidPq4^.:!RdHtXqU6k""+K=wF1tI$HEI1%d`DVE^Qwt;`r"#O5bd1`*
C7wrp:Z"mCTVcXm4)J0anYj5T+C<ac.p=/K,>hRwUNV]C`,D=^Li2uP^y"7#grk/e:%`#5frf0s3-Sg8M"ptMMB5f=Lr:JO3u"PH-1(r*7M^+zD4M#p64ndAD:rS]Ea.GcO5:&;ju|xD=UsO.<iY[Bd+g!VNE{2W[w*)^uY69q*UV@a|k".QrK/=s&E-8]*AR/L_A.aMMHv]CQ#,b[L6BljyU`Y32ca)+WR}kgohNr%j<D]saW0v0~I})1"[+XSy,eZUc#;DY9rzeKxksFd@F
h~4rST+"2A0@cfZ($&$hP@-mkes-sp&uNaPkdbM0*_pE]dW%8s^Y)?3hOPCq5[<(.T)`32^vb*O>)j^A"QSfl|N.JHMw!5?oLkWf^^LOI>8hSyOy8=Dd"6@1W4)p(^5T-]wbw`4K0foVx8Z,Ki6{H~]@R#X
p^S!&yrK1#W<%tmEe2X3J/vN$ky+nX$mkdx7Z8qzdsvWuWeI++.>fV:[)W:VbtvuT_WE#|e^.TnJ$>6(+cA:8WP3;y+}mAhcY[0uD;Lf,hWm`muc&pGF+d6AGT=x4V4&Q@^0
Ld^:/^G]/=ayCI3w61[_PjfPFgMP0fjy^&zi)1;bylqIjTU>Ee+Ta.qP>J]ER%<o~uhkjtwKAbuT
A5nbNZ`Y`OK_d&#nSUl7yWgb24f2.;i2/|N0a)#/>$RD(=Kf;c#>U1"qe@?Lwd]:2]80[;*Y1e.q+#RULNn>qaV[v2$_#Tc!fqq<Ze6gV>oma}#68|%_!}.g*U*-3y^Hf!%xykhOMI(
Qg9PT9#oqc<>J0i>M
Zfw-H-U.6uc?y=Y!F)1[<J@X4q_tcct}ik;f3adz45#17`4<iVDg-V&LPZTtDnBMhFvd:KS:;-crQ_$,9zsF:vZ-%C-=8_OFS3@s/J95e3)BwbB&IoA.D$"c0@aE/FQyPwd
Q>wuUy+s;g4Kwu!k+^&.#Zj:w8Cq<Pjo5Ed`:nluqJmZ>/pw]@,NF48lxH]kMb;y>}1mm^9sDtG)SM_RdGi[[=wk(E]c(7c8!v
CsO$H>%-E
_
i<q
|Vqhug+B8D~lD69cGb6x?b:@98f+_(&,*$4+_J9:YU)vd2bqy!P
((w$}Pl+T+;!jtN:O.&QFvvT3Z0^|yOak4GnSf7<]bfiyqXj|W!Zhj*A<ec.;[a?HIA%m`A8s/x=ji8d-:w,QFD=M^w8kV:eNw*25Z?BZ+R=t3Di1^Rwvm"X[A(:Som+f(Z3KLg[<4,RGkxE<L]Ei#S8_kd+c=zxBW.<}ig+k57?e"Q%zR[EO!Y<![Q_?5bj<iOu-^pi@wMh:;r4>C$J)f#mf4&KcGZsTn;-QR;?u;?R`P`_*Y;<;og9vPJy?H::{V:]:-XlG&B)A%EmT3W+nDYmFPN&rFk50Eu8ce$5&,=BV&(cUZ~lsWIIEYu6p(A_gs]>HvAyG""';case"pl":return'&]^@ibPDi)R4oi`"Y6J2STDYw1?(x4
&Z9ap-#"S>0/4_1?5~(<S#?%+Kp:(3YhE!;a>l+|g3xvIlK(.Uec+:5f&]FuJA
M>jIZT$0y+FZW.xJP_cnyPe%6GLO?LI-
M"w4MTbJ>nL46VuR[c,C?NPjc:nq(+J5mvp#EF=D^6vC9D_2<TSMa7gzjw46y5?9am54-Yx@$%U@&}g>a<p!tWw>gBZ4eyTgnoj(jNDU2,XMgk"*Ao`}j1pzacCbj&^pEDV7q|F!6O_zdb;%xn6LlVClA4Dbpj?9
U?6Qukx2!uG]|UX1J5=e";2i")zd!KJ8d8*2G-
o5<hh]>kn(I{y(4Q>fs*rkv}Onjs:<JPy2qWvqOGA3i2MEA:,3j3OPc="|eFJtM"(>x!9/6D^?C[QegkX9j3SN)gbEe7A-5bT0h^%MJ;gu2}&94XehyoH#^7"m8*]iG_C:`(K-+IY-a*1Q_q#_a#E.VoLlb,KY9jfj1N6NW`9]13h#i:FsrRT(/ne#@J>[Uoc:Nxs0yerdr}d90?Ob
6Uh;`wB^1jgsm?f@XbL_-KKqgE);NimZSUN#;m^]OdV)`X]v2Tx^>[f6Yk%m[W5xS&wD?uO?>S!snoB6jBN#~;b1oCdaeX($-Oq"T4["alz8pWN$x4lra<Aa(3KghPn*e^;wfHjB"8M`#Z!B5"un1,2"uxB>HFJ7IWWtbvE:[%T.R]|B<*9vZ2qSZcW)k.C)xoOwj,H>#Ah.B4[FrlCx,wNn+<r*SqLh
ssNIm0&v::2D0aG5gc,,q.H,w;%Ct&L&&@B`e@.f]{Y:UfjK8~3!cM^O%Tn>1$:E^Jm!m):>*Dkz>uk:nLmsE?)$Y>n(o~y8:-]cXRnen,7Ev6D!7}D*Q[/g!v1+VLaW4tdSWIVYE0yd[8KTrZXSA,W;r*b%1zi:HU_B3&SK+gm7#)9rww`lou)A4zQ,bq!H?VHR)l8UTUActOR1&{C1&
"ws+kXRzq-LyqCCg]M+DZZUG1m]bQuZaYHPj-rc5ijgm0xZ1Z~AllK(eWR!SUgu6gG5*jg=!0sm:n(0r$dj8PC>Q&^uMV01*,?X#:0JoR"=4h|Tjn@qO7&IQ5qX0k*]|S7V&DM?5UQ^:gIIi9v1r`G&+"+u&#gugBsPl4{%iE*Nj9uX+/Yck`:aoP>+yCrHInXNcO:fN?0=$:%;8.A&veAM2(@G&w+KLWCz)sIxSw$SVR.4yrI-k6W
18M]Y,aee<sO}2PR,0T;VMr@[m>qZ["jkW`v>KIL^%-5_H
<ee#V:?M(#<XG8O!ZWA{9OU,=3jEX5lo)ZR#cd3O`S:!BRx;@S6DE`FpYfs-hsDU^-W>aErK7e6JInhh&Q[UFRQ5#Zl2iGU(S)e|n&CULTkhq&<?xRIR#XR?:91_8lVqK5@t*11
:W1>
R/.hw2j]Q=Ps<l"2~3RWTwX0HpB&KjN1NNTO)e|FO,)NbThH|NI>6_y*"/l.o/AbAjY#Ro-$(Nip<>x=/kk$
2L<lan<8/m>&Hj7BBp6#_k+LOi17,^Yhk_xEZFJ0jOAG09%vaqAPNCJrqSW/y1$H@LKABcr=c|YEJG*J${x*KzY+u|+|,9xf.@!d_Il}F")CqP[uAhmI0]GUOwxjemWxmwF6W{HS?Ud8/"%@_#;N*3$|.@TDlu)r(Rc,o4RYSNv`FH(^9ZCwA[,/*?TL"/sOBSsw&jPnc)8f#,aHdgoA(DL>Tg"s#%SaQ.."ZG?G+(Ypr_etr?^}x"++fAc;taa~d+g<ow6?R~dG>}#Q)-([X|D`+H)tY{2$)8>i.}qm[+r+D:@GQ<1PL`,{$DNbor7/LA]cGs$,?YiVU+GKA$P+/{4dmau;ZnDh[./qui0!2zOIf+UOeH=Z//Zmtg)8cBy2-<`90w$T-6;0/f@47WrY7Y:!-#sCp+c-1UnW%Y%z!K/%S+)(D+LMwNjHR;:msQ^6TIe&.MQawd8&M=(de!OJl`B2@B8%10_C;x
YOdu6h9RrwW
fA(pH;Ax""Vo06Lkd2*RzOl45Dbq4pmNf/R;Y&,hFM~p&0yr#PeSgI.,XK"4a,Bs<ezXG9QvM-l)FCch;xzmE_ndhSmI[NUQ5,I(tHG=R5zY6jc,i0;R)TvI#]("M=U#}<fN]Ll3nhuY!Vt)s<%S%q%;.&M3SXPd+yg>j2rvD[q8o^vu=P!/8f),?PMVysH"-[vskn;YT^jL>=,_>=[Sr%)0fNd:j5"OGn[=l/a$q&,&VPb91@H$5(JQxB+8,.5XS[WxY);QbBGUyu?:aEf11`N=22bR%#!O@Oo<[1TyZ=FCG5x[bv&#B^gh6EdyzV
xK_e(Xs(
VSqBa8QtTh+Os6>;i0Rf,x=-JkhR/-/`ii}?jvQEToSSyS4MiU0H;e:SM1~:FPcnrX,.V/>Qb.A%#Cf/[B[XPM|,n;66U?W2aA:p/;oK0"yp#1$#V&wfJl5Z,9$u$vgt1hnI
9W[]n2KPv<z"#jQ|e,GH3%]op[@5>x+L06Ejy%3NIHgsXd)o?L5yN"PT[D;F=ub`!y8W"FJ,<aA,p2yjc65YtQ
Bie!B10f]9+5&pFtkvCmaTU]]M/Gc:)OifTI*xDlee%]%j{K8gkPyou@HF[Ry+TiOKYD+s)p}_&i/ysv_EFX^V]1i1$yo^xyxgHm@bZT)Mji>k7Y|lv*{s+HY/5=#6s8M6r^kLFG="FioSu%uX0/JsALe74PkiaKq/kg8tl
:_1sY(ksFd3f!_d%Wrg*Se<nJ&Ng+AG%1_QAxNU%Wm[pMbd,aI!5YI>DY=^
eo>MQ:W?uuY(yHk0eG^?RpV2junS#3r5^QzAV`S]vO,Vhr&Nc!L_Y2`Jp$;(2^,9cw1_H1u;N-x
RWaJO*s0#d1u{9H^o:uA%!_Gj)NT3rmVIV%WPHn`&Ug%~[52TA[k4hER-AM$8&P%!EY[
#^UL=LcH/^T*_l6r]{)+krc8o[3lIMJ_q;kA+GL91:9";4-(0abXts!.m]G8@RW~8pn{631wc6ek[i,6Ia_uRtL<LcSS!ZZWXaVcNxs%G;vuM+U
;u?UP"y@Z^?kovM+]4iT&K,k!C)EOpP0w8)iHh%UpV46EC1w9k2;/0J
G5t,<TAu)*E:H_RHPd+iSNRU7SqTM
s9W-1W_Wq[l@h!$gET)I7;/VW0sh=Y3Td~b;KbIG]dJ7tDWh3X[P[!KR^oNsh!0dm!.~&B^,>1H27(;#V32e7Ah-nm@]M&fmFH7|3CCWiMu~)"vgsM7(W~Xq`Ih</Wa^<hc~e?r`Q+6,lPIM1PH%T*g#y<]]WH=#h9t[LbvV
/+mDRM^"xEsXIq?.K;gv!VU=N!~4{=+]6j}.xwEm!)<HA-fqi[SO:V01V!b)f46=x"Fm~@`/ubIJ8
+S~jyqsJOopP-dvXM+p`VSrl8SE';case"pt":return'!]^@qaMD9(pl!VR$@PL$0$eh8_yU|*X"8)J6$vzrGehCQtGJxn($[pNwb%#+}8D8]%"n&rIg3Lst-?HwMg9NiQTW.c@[Al*tMkGriutN;D:,(1~rFo$_+<1bV$A"c;/;7!utvO#vF)6Ss#NJIme8"qpQL4#Iu_(oOv<LLL7jyY"Z%lD(<=?@{:z(5P38U^[ZxO8[4ixi5<&]b1c%8J|WC,LyUstN%68w>x0?+Yhnw
~:$IjL"!Co5j}S-F*NF)|wcc<2!y}/JarFcav3tu=ELlK8Q_C_5J"G_]`ro.YH6Z"LGFO&<0z$.[(u0CRl(V:X42{x|dkm-eDMZD@Y>lj_838?)<Y$s46i5/d^2+hO?fGz!iMcFu8X%:{6q3p>w!:"IPIknMxP7nZl@i5j
_eq0lE,8d+;Lh0;1(MTA,cK(#)j6=t?nKA%/yn6O_+?vqMEm!(Z#:>./s?)<0)+~J5xhd{1Cic!?RYLHLS,kq`bGe525e+JT.HwOgHjKEFU}h4KHq_rZohP~E^:XMOz&vy=*2(qZHK!Uz&8`4I@DK&cGeABW;NQG.KE=5*CCe~o$OHOUh"eytsv9a@Ogk9@iGJa$8cr$Td6*LiVlX32da{.hnrf@bCl@ComBi4l3Det<Slk7Ls8|ZnU>&ZQ]vJMS;wX2.IYm#tA#[-.YF]m?k4oL,iBw!Fm6]4N$UvBiBFgd.]%FufZ;&YZxb$iA:WJVf
_CT{V^E;m6hP&nYY3QUVR@mAV_u&qhe[Y&"OU+]e%74wI3!$nB2a=I2DLIkRtxfv(2rbxrY<BYD-+!DtNK^/-X*78A*A&RIit)?RfrDBUwm1[sgEP|y"B~tAyhgHn8u)4MY-)@KjlynDn[r!RRM<FgrntpeJit(sWPX2p-)d]3f3)mQM-smI!~SfwV7)w7;gSA*GpxByJ.v0Ffc@oHK/vdEtTlLf0L]aV<;xOd0VwllE=8x,JY6u&,l3Q
c"1OoAwe&_+:qr$)U
(8uOIFQmgO].`B4GKnu
B[TTB$=_RCiZ!^yhO*hol8/h9,T65})!;qu^S!@|.g-59BNTi[RpJ-jS/Lse`=G_Ks?@_KU12#rK&1x:I`v:6}1fnx=G#eV&vq(FcT6i9KLg2)Bd!$r?*0Kg`^eMN-u~n"s}t[$:[M5BFq%PQ1&c8yl*LN
.bJ#=bgf|w5Pp-k`Wj&d9$
8Ie73OT(Pew0<bn:U69]B{rb1*Y|x-tS^otVBQW~0Rew?qEBd=&QSY$bvveRC~d,&c`BuI5{fw?Nd5D|!PmvlF?vrqJ>D@rTG4pll,:54Y#$aaUKON7m#1#Moyue-nPYv/S$rp&NaO"{l;HHCFArA_fP7%`n9+)|76BMw6%qjEI3vkAy>]_z4x$gYcopYli2H:SRNw%2?95OX=yDabC{@@$+N]y8I>x>6*SjkcJje>f
%x!Wn"Uj*CghZ!L|T(i_Aq&:Fv)mftN,xmfV/+)`>`q5;Keavz9kxZV>l}u]#2UgPSCn7T=n+("Gh2dmigfxU>y
T;J">j6k#5;b,suLajQ[VC!8q]wK[?jkBp:PyM^N^5X/G/4[MB.&E^["ae#(rT7im[px"X,-s4/EC2&^WkRO[XY:KKaJaxiK+GO^,v;@+!Hq5@)-5QNufs4b/9Jyj2Bq`qE0!@VY[K;/>?/ij&ec6BxN5@!Fh]@)WVP%V3j2ywE/O;:rwYiy@M[I5Js5P|`1%s<A#l4oS^kY31=bTd*TP9ats:l
SkKWL+gY>@mEcLMkecN/,GK>_~>,Zoo-dcDGUveqCv]>lv:HP4"VtiZpRxHHXqQ<cG]EBi)sbE6L_m;R@$U0ZGk/xNP,8(N$5{
BbP:
r0qO=QIfb<Rx3J*|m!(Ww{Mn$_/Zqw!
n5Xca))Xb~q~hq&aN2jxD$%!lI<Q9X
Yv
ye7W_Hvf1)Lw+zDH7OB3^@bmmv0SJT)3dVZ;/J.1!kZrEVb(^SphnbowABI{<J@<qCh,Y-nXNI<"P!W~dSiTKpI}gs5%:l;>Sw+ceK[VBcHLl*2Fea.PF{BM+Jir;i(s>Ysc8eYnMpH;-HmJ.y2T<zN3fps6a86ZWEj
B
J%8+AU?XM}vu,I8*A:)$<`#ot8ZoF`V}:=iJK!J]2pV4$5gOFH`7TXA4kqMG9sMKjaj"hM#Gyz[<1$A10,"UB}d!%4r:tCA
TcVg/&A6#VJ`vg02=|*IYQTIJdU47iWycM4>Jt1#W_k!Dq"6yh.WVsVz/BSN0o1JP-QR[>HYBP>>Ks-]a0(wua.Lc1x|(AFyV!nb;btwxQ+A9_P[g~RtqN4Iw5/%E)7PVVJedocmvTEPI_GE0W$rGNOl+BF+)R
lr&aiy+aEK^*E81c[f<9Nr)b)[;:4@mkvEmI[d-8Ssa*v"8Fiu>gPJcJ!qMK^=W=;=uObiCKc8)>!]wST@O[-U=6#xv[`4=.z/NqLJalt;x`iWC0?h0asv)2bt2gdf]86xJYub.+V)Tf$lM]yWId`p4kQo6C1Qog[Xl8oS?FTm-t%PHX{8I?z6K9FMEvq3-*VTLV9/KjaDND^0L5v9ZJ2b@A(PB$~p-sL9lDA`(](QMQF<I)Et}7spxfuv@9g8!5_ygBcLB/L+{-GY/)z`Ml~l*KJ[lM%
3E_A-st7P8yUViCYN;5_tN-WWNxnB:{smluZ!bU5tH-p)1*)C(8_p4b?FR|"2]y[Er
i6qzDvOsaY;(0uh.!:`"J%#W,:w:^m>!Sv?K-X:M1Qm1W&&j^rd#-#Ua>g6iCl/5@rljO0Iv^k?Y**0S=e[HL*GHAw*#a"*[7cTn_+N|R$X98-7dD$2y9FW@(}?]iTO->.[8VVghq8xRvbWCcWLv1w]%W9E}D[?1`(Dda#HMc=(mK]-kSpx:
mUEy;@UvjLSR[UsV<C+F0c!Tw0yK}NzJ]W<T]gwm%D
i;g|[SA)Vu)v&JPMo
GO-BfBJns{ApPFILN1,ab,$Pma?D:DijJwU$W6M%(L^$ye:q=k,y$0v5nY`Wo3qb2L:#]He"$f#%';case"pt-br":return'.]^@qaM+N.Bw(
$&;HJiZJKU{c8@38H8anQ,FJg4|WiWnl~=<QK0YKM$b,p-Y-!R5T)JYv1M5=Ix9=|_t.aE-;RrVmHuOrvy6cX=Q7i.st]hAcp[`jXodC%lP>+-,0V.bGAG|?/iP!39^*/iZy^></]dRqkJI9=HH[E6{[*6
Q{Y7F/72dceSh0-]&."O1#*g*M?tj`UR-?93>sFuoAWlvnUryzcXtPt)l
usslB6_xSvXBrH":>1<Vx*,j9b0AaL_3!ip?gpc|kJ4mp^E8np?^tgg}6Q[+pQo(@1]aqF<O55BOyWKaK1$LdwG>g
"X2@%LopgjnbP_^~g8XxqH="BfA0]{IJ%l"Vp_VyVQT3pFdFcsbVV`L|h?SBZ+t6K!*=nhJNEidyWoF1q~X`GNC3eJPdUK.F._7GeU$A.>YgPBKA0dy-WV`n?.V%KG,x$EOy(ge;aR*xI3p|p[wfNaPY0:"ihwdN7A$5Z8n]5!+Z5JO$lrrR;]xV3^5q>5H>L}RTi4`fM>od?OL$S+m9NDc9Ry(uiKVcX)AT-Vxu%S>5"#+Eye10:E*G)5Q.dhCt#y#/,N["W9*P^YwISkDQFvAzO$E,qXPlpzdHk=fPAExC=@e@b,okXoon)@y992%dn4S#u{Ue-x(K(}a
:@NB;KShcjvQhtjZ*?wxB-TmQ=L`QMLn&7;3f0(D0gEyW#969>ncpD=v2Spfp8q#BNnjl,0Vx}xZm]L,H,x])?YbG1)"`QSBaXbOPRaal3XNY~xdY=t{ffw<)?DXk&OL[)$y%n]KbP+R@x;?IIt(,RLI6iDm.DhH@FMEejM.`BMz7rpxl|^GduwML.an1Ua9^3SG-]MKjS</FN@2+Mz!9<28@)cXYcI4(ZLg5L#~x3=[K"ZqiC4ZDWiQL$V>@c-K536{q,U1,?0(`&4{EYyOq]Tz)gqfplIUJQSAkI]}L;`Un?O}x.,_dcsZHUrrelMi
;b$joG9Dpw2i[a5q>5Wr.(~S:SjW?mgrcqL+_7
6jOrwZ7$N_n_#4p<Hv`)g
30(8@J!=N)q38Y7oXf:2Q.=0nzEDR;j8>32MsjoGBAz$UR
0h]^9Q-Mm&M8WH@(V8_aZYJ]$w5h2D7HcDztZFo5.D#?lFD"%D@$8rA8p8KQvgkJa]qmRRx@Zn
B@j`iU]IB
6,_k`^JAw_W,M@(QIm[6
$h@yqEYYYe(lFN(5$B.LAA.aAKFKuDDhR53w<r;p{n+KwB9CY/n3d/($f_k*kkL.2@};5hoPA%S/lOLk?WbbQZ|34jz
P1bbE[SXo;OaE,n.:eD$,+x`aPpYpp^FxYc8]p?V[bB_/+(,h$Qd0*}&vC5%GX9no77Vt2RF5voZOu/EQSOMZo|olly6qoYR`2|0`E+(x;UU~if)mCiiESo=WGnvK"~+]:n$8ZOEnBTG=fb$vLpJ^dq*(n&!9,EDHpi<q:@Pz!q&(d?fZWuY)_~E9%WIim!](NT.ub6ns1}T7m"ooG2HPWt(=A6g.B_^ILz%|m0`O6Gb?!i9@_LB.!gSyci
HQl.N]mYPL<e.x3YY3.kBdRZh(ty)C(kK@a$3nKyH;t4U#{#flQ?/&<TM;!Iyho:gYj;sd@>&Is[N2BN5NOl?av@C<@CmxDB"sSc3Kymni
X[hENm-qH&Mj7I3^c7C..y=x@a-/$%RI5!DN@3JCQ7A!hL4oq"C7Jy1^oc.[/&2meGoo[vqE*$#xUN05#efOQJR7r[6p`C`QoCDtg-+-8eFOU7EGQC2%6*!<.@=To$C[W_5gN5W[tH$IozBim0-#6AMdqqLFhm,t>dN4Y_CP$#[2o=.*C6[HAT<jr~wy1ZrNd/<&3lT_M/CFa>Eef<bb@=;YkqpgjhiSsWVfl_23V[Jm#3q*?}]K20[ag&)?3B%JQ%l7AX"we]oPqCKhV4wl(X-+b"#M]wxU7h=-$%W}%M`8?a<g_c@NOC^[Jis^2MM$xP"4Z^H6u|Js0`SZR[eS,C
}=fP?M@@#axlDbkYO@RoR1Y!_GjQFO5at
[U)r=LM/87]MOh6^=>*r:(~@Bc!
?Y}*3pVE>[LslTtE{tWa%k:ghS>5a#z,-"r5N0<6w>[1@-VfGWY,Kn85I7[]%SgxYJQqGB<!Dh*:BlcZbg6fHaCXfLzfw2A;%5<AQJTg!W
f~SgG^sSi_-IkrVllasW06DB:J6[@^qjlnb^v%Id>+At;F1hm[>VE|kzY28S*!pUnY:6c_;W"A!iEABw;3DoeR_"R~O{B2]K
}sO:&leiDJS%Ya*?5V:8ZoDJ&%cO;7B+9K])`nsNJ2gZz+AOnZ=ZDF9!s*~$~=~F}
YBq
iWW#An{Ybg~@wq4bp(SvU&m4wXj`Y7H)Gn]r?
-og=[tq=^c|HepwVc%]Y.njV.%,psaKY,]
*d;w-<d0U{dke-/zmU8dINfiwY!uZh]fED7#CjA./KQk(-rm;>-ymtd2XfKfhIOQNL1zr]M7ww)l3-UD+Ywf@R]NNRg^&OGLhXZycgCs`I[Gg041!.6+KRxA[fsq
|NLRx!;X{jYkY5|^kA#A|h8q<
AyNfc>eX5
jnQWtHHcGR|P>4c0#f,mNZ0S,e|&`q|4d8JE-qum@9cmoa-r!T3fKbaRpV*2PJkyV+g@b
ln/maL7TLP"&~-5Z+nG/P6>LwlVgp]I,!-h@Mq3M#XtDJeTHuC-4"d<Ei_}w%qa*?)w9uIyU6`/mpT910Q<fw+@%&*h
lpwZ3;.Tw]-1I(gALY50(!%)js+<G29]ZT?B)m/.*0`oDvMOACJ2/.?c_+FhK!s>,)_qYUs7~PaysoT;,tk)o
mv6^~dkU,tj=CFl3&mNC6.@U.4cGg7$&i/ao&LE+eFJjS9"6pW2O.5+0:NFw^
hL9O"qfsWPj_m8"FT/UbYdU;Fr.tP_Bfb>(L&U45.2Scz^y6bx,#ZxE0*c"@]:e/81Yo(2^=yVv.+dT`S
sNa
T0+i99A?hv?I$y/Vt@64<SBK%pn5K0;vzTDQY-9LFpOQ!TrbEv-N&';case"sk":return'"]^@1bWpMA;Bio;$^JjiYS{%I=G1Qgt"=r}N}ab!rL$So8ku`g6<F0Fwad|ai#GTd0LFg!IZ)-lunR{XG-Pb_4`?mhOnl<O7h6Yyi=:(}FW%zS;56yGY>UbpfpJg"6@Q*;VLU#<v*5mpsM06#w[,PcfY:h3lSi3y7cbuxB&?#>z_]66:b7;K`NsYGk<&[FE1DA]o*"aW{LT5b^jS|l</A@AGcg~,NLVb=@U<oxbs,y#>q5Ipk+:+bg(FMp]e4j!4oX?BzP$@e0$H80%wuFawRVUt0=;$.kphBUu=mD(wJ0
j-hfx!fpf(9RkAVK=+/8=4<3t03j8<OVap&q%PDIX8*$=$"B),?o
d2pSrkF9<AXAL2OD%hcdk=#rF/#6{8B]8<k$g=><;t:&{M:Xv!CY]-"%Re]E0=`GV-qtCCyP[5Bnc5/wKBGMe=HGT9xG;t([5m_4HswR?b>0>Ei+dg4&aUW0~<#cAS_UbQ[Dn3aVQ=odfkqByLolLx_eEs?3bWn[4C*A<(soQnuS2uFRGepM:pDX9l_1vBbmzV`j<*4hF,:gPEt.;TyB<$
"IbrW-f
"3q<ToCv(^*Qxe6|>g6#D{q+R(rs"xZ+m/E545KIAuVdA3.B!R[L<ybS0LC&xN+~e^I=fW_f,C3"-z$]S1W"R|L`5v;~7eWQjqPc,^N^X5lz%@UyZ3kDp(kT%D7$1iuj1;W9Bzm&0V5`#&DLyI0V;$k6t-6#2_?WnpOH6MErt($<afb}du#L@n(X>epmVDM$nj6;JZGw]k;%Wz
&Un4E3Roc@c!%frFWboCyCqHYxuGc"MET45tAa3>4Beg8T4.&x-[tv8i[6Ci~
}>-kv6,s:
^U}kGbJ0d59Qm]^]T0nXpAyq1Fum<BV+@0p3iE^G_qbB{s%:(?i3Kdq;#o*4ir`!xg?pc:vPM)0_6mrG(1Rqm(z
IDio(IDe)[G,-)BhX@yuZ
*U^$b]"0dwAlNDCvEOWC8v,)NR!_c^x;Td|iu%:mC(OPR+!:sO0XYYkV9o966sa/Jh|*zpZy&/u_;J|Qc0!1
J4Za,v(m37^H+;r64`tv??.y&7vGH7mqSqy>#Uf}I[84T2,O:W7r$KcU_*Z[C-!Hv]WOMInNx*E%NE_UT9_YVY+:-r,./$^N?)"Uln1x^qLnN6hyF?st^#=j,oj/V+2HI)NP4;S4]&+eG+Vxw`Z7<7m:@?P06rhztHtOjZt_7`n^H19RvEgBy6EK"1ED-ECERO+DKJm}>hF!e4V*57@gih]qdL$*:8Y38<DY+=xP2iz(yR%4_@y.RN^n*B#([S$-I
uzMrxkVzH-2?OPIWpB`
Uv2v*v]"2XeRj
xw^c@$-<57er0Bgk6CfI2HB_V3Bsh40>S:`j%kN&TaJ(mtY/f~[08P
sF[$Co%>7rpe1SdeoJ3Xq[6?9A3Nn5AI+]IMecHc#?lN!$EmJxr:=J.Hd5=m(
:T6R%huBS13,`SjIRXIg0Z@uoUOg%D9:*=M#b9!7Ly{>Q$[.r;?hWOP]e<$
/"mFOY[TD"p778si+[Rf"La*Z.8;?%?^XR*>xr<KH:j1wJ-^rK1"(6hIHnxT%fy9S&DCv-eBLE<c`eu*TW)0}bR9>M*v@kBv7*^Abdtp#$b,qQo:bwWoV`.+~gqR=K!q{$*B80D<jWyL+q[l!W:)4;$"$:<4dNIhze3SH`$tyVo#_EzF6+!SY
NPuSk/
0T)w!AS&EyigDqj.VN6WCqUvR,+xM3$in*BY;F"e+p>P&}anKy#"x%:mP.rf2rcqp,[Wt{RP6,!eUCk+eZmQNK&}s*+kYCbyIVj0:{toc94?>uO0O<PRq1R~#|aXAc+1Rk%s<s]Ec{EPj.@~@g"f_AESP-;!Kz,d6#Dh:1:p4xM#oru5ebA.9#:^;uf$N__sq{Ktn7<f>t[_"8M`@lePS(83r&+`y#PJ/9&frUot4QkY;o_`d(Bx:"B:1?o:[Z=;i9p8`RV~8%+_s.uOJNQ}/J(QtyS:5QQ)4a;+d35Pvn$2-!ZcL1e>Q2PSBA3$lrL`k.1*_iPuj%Fy2m&7wR%Xq|]uEv`oD}773vN+as$("TriOrmNc!);jdh0ngv/d92pyGyhsC5U5
k+!E&~2`Q%q!s@pm]+)GN_OASQ3(2dxh8Ok<F{TwJ.,q`0,0!u!GDMY1_sj|QqRs8>"$*(ANm}B7rBv.SM^0Q|-C$xinLQpNI!,yQnF+@9NHMEQ5hFH&M[ikZLw_>c[x:rZ!gF5o5=s>#l)P/%U*Lo0)j%V1:pf1^WO;6D0k6|I#ty/8^JEA[kp2X8la-tovk6e2KDk3uobv6W)wA_wR<}=%3GjUM"$zg}:T7Wj(>2L3DOyASCg~dD4e(m
Vc-Ncp:")WD(H]`0.qv;do4dthALYQ6O}+2*f9S"/:5QZ"/0"^+G?cU.{Dll-ykfAyrRtk_G})mHuPwDT&ij_
W0PO~.:,:f:6+T9SVwxYjD[00wrK(5Y>}*5s0k)f-`Fc15983%B5v^SIvC<.w7;
s>2TQA&o(d)q~LU)OTZpYt[NC!uPKIdk7z$ftnyBf;=>
G"9?UG+*G
iQ"H>Q>~Ci[.F.;u:;/:KNoCab=k.?&@#ph[@9a=P:]MMC8Nxnh[^JFuBVFnJctk+i%n0UPZ!nVLFdnD]
iFL]6;V|o}W?=/EA2D`:MNV>B?QF61St:P)/6bHC-;VCMLG/5AEHenexA#Rd1Gl4:YbR;j*q&N))$*6_272~JjZI!nVtfwW[Np_I.l.HQ+UWVL<BuTaLPE1YG!G0$UBq1AWYZ0V.
thI5eHwL}FhBAfHuII=ThSg)7oJ]aK8:>D6m`xRY,Y"8Wf)6"iuN)8pZi>K;FdE-WQgH8$BQ%dZ``+Gl~aYoL]/E9%1MADW2ot.)qs4/pE>q`mIEZ+@3<K`;}+ml#8.>)UM(0
B/<cc.9=NA];F:P&mJwt;78(+i6Q(</e+/#xxQ8nb
K2:W!qNd#Ub7?HJ?LE?Hv43d_OT2rS{C6+$Q>&I2@m&85+toE8ptFs,,XD~M_oOS~!
[A,-52CoeIDU[<>Su+
+<#Tm:`y>oJ:tAQnbUB3%-uaS<@JG1bL<rW=Ul~N$)<;AlVIou=+^4xd,@o>G,T-Po2VD:"6)H3%SGL>|lW$LQ]wQy>[-/#T8O:/jM:okYfKWDkd5`])?!wZKJ+vnjOoX!
`IigR4s,!^AO%(8%cRf|H*W#<:-$C0+$4Y
LrKV(t-k:9E#FKnt9SBA-%@s)D$eN5jk{f~l_AOhxv.rLoeouQl2+Wi:Y/a39=jabt{%-2l%Bx}HM2ob:<r:6p=HS.Z5uwxi+c(0qBND[K!;iA7:@lqiA(bU`a<$hmcr@CgdTWG,DIviv';case"sl":return',Zu;:h&D))Q,SY/8obP0+CJw3dzOPZDH&datbsj>"e!"EL0"<@H+q&r6Q#dd.m)1mPumnm|JWML^~v)ECn0[nvw*_x`wr9%Vlvz`Bl:v=Uk2Gydl1R/r~t42)nG2@][>-[`y>NNwYqkpjz"v[Lz`o+voH]8USLLRgLOyoE!g{cgSKwMfQJ.&|l{i0w^>Zv:93y7EECr;k:VJ@`ODro(cOd!nGFke4*kn>jhL~dsdX4aU-OF,C_9dE
Hnr-:[Uv.m=+AxzI;>al?qzjxp(-OfZL.Jk!W!mUYrs-TG@hM/MMr"VJ|v_!NYfkR2ZxvH"6@`B=n5q0WBV7ZK9!]aW<<MllwJU=_kOc$n(LR4|dI#?wphiR)q2tw)XabvkUE7yG^+F]*xO?So:Z{8;8]XDKYh4q{Jz1a$rwTg`fP1Kp(4z5G_hD,;4Tr(+ff4IXW)-(KY+l
_TG7,=as0joQ;Hd"NRe.*X++fKQu3E?N6fK0cG50:G_N>m7J<,<s7|9nnpxbCO

r?^6<FxDb9;wjFTOmgX0$PlB0lfNz"g;]=[>^4`**;TSO}uV5}I"13yh6#^{0oGmy65le"d9B1VQ@qv}l`I~cm/Td4.bR{=Lq,79Kml^&C=`N3%V6ZDJ-.jw,)`kl*x(&6ifbb;6iNwR5Y:#$sn<[sdonE8vysRg+Te;6
,!Pwh!w2%tHowq!J&2+y#">;/j`T4PFyG)ld<2N=(6^<k@S@4sCJ@Vd^k[,;$Xej`i5AGt`E7Z+W
gA16x8z]tfK"EmSgEA,OLs/,]pOj
4W)Nb8`4iEJiiX6EN^e^?F^rW~7mMB?@"U2pdxP~1_KE^L8gmXt*;;4]Rp6:j/
sP6Vn"o1kK|#;,XGu1v(E!8lG&du(KKxVT4oP@KYFcZ>fFNCF^PY,J]-@$sSc"d(Kq/;Zp>:|BbQrburOAs-viY7KZ7PP+gK?o|f+lx4RW^%c/4.)_b;d.}^Fgr%1CxSAg!i`oLVi%.M}-R-gXUg/jqto2dk&yvQ42S#u@q`n6&2xvG:kk),JhxbAy2]=#]+FWN.:0>TGDZQuRG?oJ;AV?E_FRkOxcuuKS7Cxd[jtV<ZL#*Yz*#q`5hHW8Ds`#i/xyv4utWgnw#XCFebyHydma(i=7po6xhe5w@p-W^52M!
;Eq-5HgLa2"LN^L:_d7qk.1mG;eeW!:mL.)"P/<?U5V2AN[n]@mP;"e%H&G%4osHKZqC$usuROtqx67(/kW<^=8]R,=j)LFrJC&idA+WAB<Wg(4.MwC;^#Ir>4iC{:e:MGGB>7JsPx=hiyLR:wYqc>V6o-JG8F/?{Nx+@Xm#0&_h8YjMXQF<+;>ryc~A7XQ#09I0*T.7$O)Io3uQCV
&N`,TOC-ID`rNNhyV7&+O!"-bKLvf(@%OhsVJ%bR7F#D#,.
N.jrEY"U"$T
Scv"p]j,iis+jBhtcPc.R+IzVS0;pMF83=]//,PzSx*Gf(o0]5!kHEjJ;Qo@D8kV)$[aE;yYy]>4y#tzop`>o%Hguomyw|?B7^h{WUsRKA5{QeW^LtVN2@[^81g2#zDQh9kRJ462HX?>1fNW^p2sRGP>hAw027v7U9yvC_m1h6151DjFD:vNoM.^.RY6/p+cf2Slr=kyG]laQX:M8#m9gae%Xg7MByd;5&P05)M2[tL)*lur=(&5kE2s+p=^6G",ToU~#4*o(?KC&wvn>%rC/Vm38WOU:%dG(7jBdz&bs5cH*f:PsJq_:P9(DTG5:h;*u4.qQ:P5624<UIF:KUpcS$?6_iESp"wefo&>3O;H
G([]jH(tXB4>8)iRFM<M)#}s|>4m?;%qR2$O,CQ4%oehm&Q%Z"6Pm8eJ7Q1tcV326sjA@ec)_px2b)4P5F|wJ8eyZ^{,ihPSE`2F:/LDk^3?/#)Wg
pRB^uL_j0vJ[G%@-#vIx@5
&Hk,
1`gK:/3v4AJ(9aa7YL8<,;wr+Le7W/J?NZXBm!59K=_LzW;^0taEruk-]sT`MFAfS9Aq[9Bo3)
luT/RmLRS="M=lbSWF[aC4U:6[F|Jav?Ld8<4j`K!<bt!(.
))pmdsKMXR7fA?Vp*[i6A6UrWHy14ye<t{EmmCeyV;E;u<Hw#s2N&V"Ojdm6_!WR"n!HBF).n<Q~C]R-na$@Hlv]Hq<!ELGOOvEe!!)8S-H~do!c7m6mN`.sfPhQGOE!w{glWeevXrGFS46jYTXzAt=Eg
qh(~1PNP2Pvx4rn&l#6pU=y4#yO#/jw:V4vw/{@93[6yKQ/38&n3>-b=]iR(]./i[cm3g=2De0o<Atwh?^@&"o:ZOo6,fJQJIKYBuM36Inpc>HbQOCv/$fO?#hqH*J+C?`e.@kVjd]UR2U"4N&l_Yg1Q<T6`
/u7#&GM&$Iz9cyZaB2]#1]gV=ulAtOcEDHfr,1k)%I1L[^q?Nd,YMB^!s%kkk;a=LrZ?Lb2c,
}P*7fxdKN
^R$;`2Paf];<C5tQ*J+v(iq.<jqDNTu_o<fpIt;4?X*@1e%$+f)d&57qZt<HHiNyUW@7}>_BJy
4=+gAIc2wy%iw%Uh`6@6V!rr5
3P(</r/>(/C<cX.):zexg^MXBjI3x8TlxE
,M3%>o:IePx<1qKx3$w(R!%Ew2+?B0s?-h%/rh^UBmxE[XZ+;Bi]YuxED9~)GdIDu<D+n,ZR72igqkh5lAxQoA()$K1e2SL7*"X.kL=!o_8hDg"X*(qaLpZ?C^D8tK.JhK#w/28tS
.cz;Oj-3wURj<A+SAUI!dK1R<ViO;Q`+hJY"Y^EMM]"xN>av6ZYxg2c6h]A+CrglJZ9X.r|IOYKs1`:hVQKfrhZyUJTg
5urW+{sPhzrJo^XELyD
.+mON@H@0lq75n%/@d*L`2ZKN
+WT?/4sjnbcLD6.$JL4iCO,vcbLh*KuhdkX;5IWzhnk)5R`rAT"Yk8d>1^i}4jyIo6!3&gwQAN/r@-j*(V>zQI:XY
cP_%p8r|*;dy9qp@9$>o**qh.cg/jIGCYzHsU^(BralV4yA/Xurc&x>NPZ
6Ic$+G:,*ybXY=l9>$4GeF`4J-T;y>C*$a"km+2UrXji,d/5heu0eGrQlARPk5zE{fW+7^Gc-kGNbAv6W4i&C>e0ZZ
_gm9^Z2mce
v`%Jf64.E2,92G9Cit5Ffpd`d"1k5,4
N!bAyu:i(Fv5
1>ms6P5A,_8VZWW5qbMc-#';case"fi":return'%X/;;5LWR/#t?Si$9bL9>"/j*noNAUFaVAm8*i#"z+r"l0@Tz:nj:sDjiGnrSB"X#Dcj&Nr[6=mt{&;k1<4e13
e|Lc_iK*,E9/$RecTcQ!LGG&Kso<y6^Hy2
Cp4ZERpo&QAC9>aU4c2*)>3v:wF28>aKtTnf,^~Yw)cE>7m#b$E,*X>lLkkD!a~RpRT7]Mzs-[]z!w)yEy6m-nu^+t&3`s37l0j&JyhG<o/5g3h0MF]n(0dQZ3Mul>E#cfD2fF9oYRz)5`"Ha2"#2"(<MEdEw*JnXik4i7pWg1_j"X{f]"oPt3CEctkP}:%ni]p;WS
@Hvh=^G]</.%sz#fw-,=eeL<MPT7r%,BTm
K-_JWKDn)E-.wPo@&uq5V%-9D6%1J,?WJHD"^rb)7t_mgol4hMfi5h~"]KM9u,dm"6Tix<YPkiM.DMK0d)lJP*{67#DEW"f&6c$c8cGSqaR(K0i9fqUwLIq*=6eVNM*j|rEm>D#<1pNJnA=oPKx>6=_t_^hvCq~+5:+,<%GGd0+q~k)sd7_6.=[V;l`"9P19{sVQnEOFH;7R^#E<zLAP,jn3XW+-oW
;;,~S{
*EP;sXn!rV%1o,()&0_IZsh<o4apxqZ(gVR.^BuhIjv<7N*^i<CPcKs:MytUO^]pI3SQg^e%4=dX0KO@l!|)[YS=r/~4vApOWnf1(4`4$ALE,+mx5kzHQ3UpxS|TuHzk5Ao/XL)t,nVbaW>"p2M719y3tQ/HJ0qc]=+lP[VeP6zNFh4-F9I3.vY90iyV$a-<r=GnDIOqY%$!M#<5Dke3N$`x:3s#AANbl5SI-w"5(N>.OlLCkdYsPtA-TMPq}Jaw;L6gaDz_7a8kl5jYC"z^(f01DG1f"!8)$hC;2K|+RM.h_O)$l#&?nZ@ej3rqrR2lCGqKpC33+3`KPt=@^P(OGaR"YMyb/lcsr!Lj9HGf4S5Y5^nk[L(-BNGuncR4+yr4XD3@.l1.uPu8fM39ym-,7P
,d@p3!FWkeh1ar$qBA#S0n8CqT!O0BlTciCpVf:y:Toz#_u;$}U<B1Br@8P>9K?W&:MvrXR8^WFfd{/k:)TA6
N~Ei:q4*bjDv"$CuB~"=+d`d@Vs2->pFj+nq_*5Aq:a3iFVp/f8YB9mi#6S0@vXwgfpIdI9st)%!7s$h&hQHsv4LN6;!#P9EWo/Aj?22J$^BEo+mK=i"XTDl0hkFFb78OQ-4I[jvvUI-#98,&dLaR&Gl(uCh$wk6rv7(tYWSg{S`+;1=iq*<w6PN0_d+vdh&!7=A5g&v^!nJr*y@/@=rTb:F$@5!,_I(E!9(QA9IqexSv~+*aY`fkBaO6N*^9|&|,ugv
{y{39MrvDxoItV>](Ns#x2q]<>G0wG(%yFp0@d_OpRI;BY9$^PYYMy^uW,XY"eL6>h|o
cN;o*HDWM>Xa2$:838d(C_C0ax1)0`;>QjQaaIAB?%E
x%0P0!F5IuSRe,ldB&#E*e)q-</c?<[$c/hKT>#[$TU><LfP9ow17$0BBcY).O!~S5a1)P45!U]!:~6e3j+Gvhd!T9Fa#*_`230paca$S>P~
9dnXf(S0gOK7?EVVa(:-T0lT%jJ[i=74|%oB+9"$hQGlBDwv6eL3d,lI1Iy"D"lvOhR?>F9CgstWLrolQVT$
k.=Vf.+iilVwy8ky;:Ij[Q.IH:u8B-oevV3$)~I+cS"HGQi[,Ld*OJLds83=$%UO7FNtx!wyJfaWdQn*xU2D!l1Yp%G:8p7pnS.<8~j<J+e9#|LT5/#%,VLA1l/i;PwL80S~XvpOPXObKQ2=yt,r7kH
h7j_k{Dh@0RJ+pLyKcQW,@o9nkXE!LDd`EbEr{q!`^>s&"H$dGXA#S>Zxy7PS7&{geDF_Wt$OD0[[%2RF"eyL<pl.>CPYU5#-nusLwXk"_mg#s9x:k@B6&%tG(*H=ifiETtUr)W/Yi==Ga&.xM(J+;#WVq.agh*0.WrVC-k"?yL"Z#!M
PucXfZs5{
}.Q+yiGn.[8hd!RQrD!we7+Tvfqw2=+Ab*a%a3Vmv-!Q`An1@PVrl@?B~?tj$r%]VKOp*:3+CgCdkBT"[63#D!Rs5H$nvuDAG3
l"%OMT+oX+$g@$f32O$Tqt3AL}+]#*6P/IuW[3<w);i6YsI<@e"_6U[&!C8<"fSjGxDuHHPS.CCJE.%HwuQi"?*P3pKE!*
d+vJec}2JmylqW/E-[0GYUWf1&P)gn?.NkMg)I_#aP9A?VM7@9qL">nEj@HuV^:ASLndeeaDg.WKJwgxW#)K]>AiW&["{<Dv^
b*n)n&A-009NfGkxt3)*Gr_lXhZTdrVgWF(H=<FD"c6;"^EYK6qi#vbbh2OLg"LW+s
lg?
NqPBKd4Y)W_MebsrL!n&=}C@Z~+YNff^[p<mE<Px&
@/f<Y5XLwjE5EFU?;g=&KT.QpQjOS5(y_LpWuHh3T9cd?}+K70^{2(^M1J!v3A?03.Hk?dl!Be"
j;];T0->9ssk4U5y,+){!Yl!5(4HWk%]no]Iv`s@"FRPb:C&25(9o(!>huS`HuA*f4A[*gqGqmlH_DNS@k3WMb
P?EbDgE#x@}9icBA410I_7Bm=Z&0O;m`2eTMlx,$HkV49tU?n+Yx]Wg<vtu8(FeZ/fH3@%eZ9vDl?M.f9wRTGF~=s10dV8!rAFit9p[d&7RV}2=!95mP$i3u_Mu:&e:MmhuY-Z{n(J"cPjh:}=yidv(>qX5Dv3Gk_EYZa,?aa
W1.*y7i*4DH?/o"MBU)n9RKP48^QX?u3*LY6egX3.Y?nKk&s_QlxB>jy5xK>ykS@Kg5x
(>sB>dlnAGG0h5@;.#H/l`lY1Mp:]!w$7sA3E"Gw8NLTqHS
#w^klcvu&AGIIDP6O2dou]8.icgcm{/E/<P=&!hj/[%.UjRCZZ?jI(1pkq2]94>jGc8w:6a*_MrBB-E>2bjCa`vC9+QZW,4bnaqkkMgWkh@cE=rdc8wXYqbPi^dJsL7VdZ!OqQS
8+,W<(Expy<;(=k]G9-P?|3e73M@Eyg^m]g<w]&rP6nGq^ZMFo:TF-["p0[XRnkFT1%`NF]ZLJN07(LSg=OZ?s]LKcXcBv2m(;>}<_g`V>r"iBknoVGQEIv>PDccpS"XR^uf65._"y7jS3mt3=MDYS//OT`.dO-QWu
YD8@K(]4Wd0';case"sv":return'*Zu;:bP.!$#5$fnO0qR.GD{%HPLfppa1sTO]q)e*Wlx6~L@gqJe.WBf#_:TscN)LrTtV4]*3^3)8qO):<<6xKMX1)v[juLePZ3+6K`7K}@mZf6Zr;@4D=R,aLKhP,ducrS
XqV:aZPRmOu2A~P<x"puMT^7JabX=>
)N!sbyJ-c6KijDXC=bI*npRB7`NSw<d/+1yb-dAG;tWy~S$<FHO[h13C0"#3x[2t
t2tmyE+z]jIu2Ba%
Vj]I<d3%[v<;9EFtFuY?}dtbIr&lW?QKYs*0mAL^
GVM9G%LTJS"A<+<R3_GS0-@L48avR3d_W~xH@oY+2CU8l*p%/O$=1e9nvLa%(<@6_O*WB^JGJh6UgR-hDkJRs$]Vcy(QL-KeHgb(mU[Ks%tDF4>AQ~tx$]WNUSmyq$cp)/w9jXc<5XQ*Vqy`t6"8:!8]WsWRTT)|fm:vYlJfMhBF"Swy.zR21]*iyXb.cw<
=-r5,N5Rqg8,eB/X%77$%wc*2C]V:Om5MOK(R"l^h~!+
Jp-H
r
`34b54f=<INiYc7kIZOVsW+[/nen9d(8n2BYRS_Iv[yqARo("hi7bw]4l`h,XK"UDuohd)VOTL4i*UdyxO7H`MrC`?Nq=d6*lja"qmSlpq[7;}Ht?Kn,duKc2uwL-r$`/l:LL1#moRB|;{GcO9K`:*]r&^?`,zcZb%?DP7DPkt5-5I+`1ON3">B
RuF;efq2-1cK
?3*hcV.)=0gu0h?p1l3V7.wG=H2+1oXpBcK(_eA^hxSH?`_`xJq$/T<r*3sE$`RDLu>5-5TZ-/3R(=D?FSVi}_<LE?!IvZt8iuwQ0e2*7wkvl90>be2c5pWZ]V_vJIntnJH;GTJ]{"2^3H"J
Kch$LWH045=X`y#[Y#3gDPlgnZa
W<q&/&wC.,nnUi^6VeCF:?ffV.K]__VQ9i=;.DpLqD!Wx4I}tRmkeZ)1R1EM,ADaeayY8lslqC=nLj%lT+?ETB&j$1g"o|:jpd;$5
o#-R/"SXSw5!X55$]/nItRlBhq*hV{StRePaTH;;lNW4,KqjqQ"S=7%%Xcj97Rnkri>4cmMRxR8wleE#+q4XdAFs.U[DZ@(gepe*`@2IN+_k]D/E/`eu#]lXe;Av`)"28l=F:`%:B0B7z!GX+At-D,bjNFsq8FLDljsqu#Bzok6w<ut,rls;>d*!C%5+U,[-S]MfCY94v(f/+f0kKOcM3R:RHd:agtG#ctNnj4RNYJJHtzhCHFg,8e
r6b#otVl,^E3dyAF@MSdmTJA<vKfRFuQPH9+<hufPv=s#ZGk(HX;-%F^HG1g|]3
F/VVQhRo-D#OX]l?wT_0I/WSGX{RK.0[Xkv
:HWx,L2s%#H0Od;Q70z9x)M%5g5S[R]v~7^G-8P5h(?/M
E0,QYyT*vG>C?EJ+#>-^s2>8U*X/z6>+o9SWibD9@0K$"SG`}`t`UMssK%rYdB@m}"jV4L_._R0Y;qe%]R
4$xqIAt0-j7=D[S_xpr0avU3(T^W)XR76%-)tu>$Quwv&v0NakC8WBiXKhuFazu*c=8M(p)gJe_Mxm4b;}**9*&;$2Za
YNb1Kd>`107>R6IHD7=mXHP1p):rOMN!Hu&xcyv<u+<,{^OyfBmUhP#pz`$8KEmciUfIZ9g#H*&My[^.a:CdqP8Y+nUA!KE+.4i+L"rNQY#VpGjWp[Qmt"?&~5joWiW;mDc>yB$Jn$V*0e.H(A0Doi3-F+28t,gIPWM4sh]x|rd"[:23=o0<8NR@O`fbL0j^S?^-#IdkwmiS9U%#L_]kAt4I}2wR~B*c|P)3w(3=umN&u+VW=ATe:e3qO06x0^,g0o5su/d;:.r=66E]Xyex5uz3s4Q".VusOn4DGDU.VEg
`!?lbeGuW>E%zfSCBdl(c52Y1H^l%TEh#eddk!>e
=vm#a8.a*I=c7=BfQjJTPqr:u"J;G$-A+7_B3/[<u
B2`0(aOi&Nxf;w>s:g+1E_Y[_;@pM;,Wqy]V)-^v&R8QQXWZr[.oXplp
G.C&~GYOUm(Vi.6F3f`<lw{C(YdJKH6(^+>PRTeb1fx%SCv#9dd,bg/CHAbi{:lP@<LSn[R50[9iZ%Ler*K/Nq.wY`1^$xjsbA`x7o!0~uPEla!yTNoX0)^tDy>SR]Q4p"BF<GGLn_d;iJ#s[(tvVL!iNj8MGXC0Y?%`50TWIs]>{#~a@)]$m-^#MumOF1Qe"^h?G%Hncvo]uW"H&:(&X@Z2+fkxz!-0tuR6Ji3T4Po;>y$jxe;Tqu1a:uGqg,MsRozu/0)tAys:P1cOq$Vm+b34i1"XMO
:udh1nQQ6i1[.`2}q8P[<t
_`-*;4MP@!_9wh8dX-@hA-5vmY8[m^Hg]_m4)dtJlp4V^vi&h9g
1(51/wqJn+-ve,;L(dXtr);8<ws]6
~n"l<Yhq][LtkI?*z)rY{7WCZf9*XmG8PMPut0i"9f+k]17Hy=.^ArRas)#5Pq1y>X$?b[r>4I
w]LD/8voyfIAJaf&f96[p6Ui6ZRwULL;
e"].Z`K5LS!Le5T/w5ZPz7K.}<CZr4+06bg;5Y_]J4>b.rS`%vGR12K;?v$EfQeC~.pSq8,"|CMr
V+4c=5j!(iPDQ2rr*^?Q2$fwKJ_>)&5xen)cvpH61(2e;v.k*L!u_Fen"rs_RJ&,TDp5<.F"&r.|O_9D,|P=.vBC3[Qb`K&f%FH9fP16d$!C`xk!T,HbO@"eg"m/HLM5`v
M-PTujPe:VTfxh^v5GTvbCifh.S?
xfVIIvC7)6oy
hL(gVx]-]3??k:?NXs<i3fb
~wX
P-(.sSEgw(-<a?
a`P>w.&@eEkM,1=;9vq1E&Pc^Q&YwP?#0loCqi
8#V7)wA';case"vi":return'.X/<%]@Z[Efn]v,A--#6-2II*pY"c6>:}-un8SC!>;(@a`SI-kyI9,0RBE013!ae-9z)I<</fG(%8$/Uk/Hq~y9EfXs1YUo^YTjp2^+B0]`x27?p!r>nlR1"o3NkIs=YHR1:@VU!L2]4%A[3>L~ML._e{uxyu[3M{saBzQVlt>pA$b{*bWcK:dDcsH7ge9E%EQgrwhQD6d8E[u*ob+_G-x;KG57a6?j`T51Lzy@%mFpQ^ITi(SQH6nBda[FE9>Ph-uM##Zpx}5(4pmN_pDt<2GGujuPI-<RM%7eewK4[F=Z*1VZMHxxpV1tclE^`>mZW7I2<#N+:)ZC+XR_WjF>Imbi>9eff>`IV`15ieL;@N+GtdT](a]8+ex6/FjuIde2chYn.>P?V,E9LE.8O>aKk<pSytZ}VqeT_1Y}As]#o+z"S"?gDG8;5xv_?kHe?LtRa1Y&"#
%4.X!b4FcAb5^hp^!DzLq7=<!2jS&r-427.N,O2Sb,{M6NWk=V?*t9n]4B<VEwoJH9Cjbr|Te(cW^2er`ByH;tvd
t9A05*iIg0Owk<RJOx1HVyoIkS&Tq{B{?D*8^u>di/@<iu]rg><wrOqLh&nA%f
P3Xd_%X5,"IDD>^n^d<,gVGJxC*JOs+>6Imst:%4GL"=Wq%6?,?:/(Jp@izrm9zP?<PCC.BK{WIt5h[2#o3fAU-D~Gmw8M6x]^N&Dtc*~6_3i5JUvn}d2tr#yD]]f]n5:u#2Vf}T*?[-Q0JKzq$K=Gi.@Ry$/J*h!tbJ}K"=G?s7r"PV<k
n}Q=Q2k"ZO+TK
wK%0D2i+
Hj0lU-q`6K-_.Y)13$St`p)b}rK$
HBG|wKe5$$<6"23Vat#%$H<jUn"=3<V[*uwwCz4V-od#^tRa/u6:8r$<$hI4J=dcnB+Q<u&le?:o/Mc|nP+l+2#s70gI7ST8!%Fq4T3m%cJ{!Sm,9}Qfi-HweFOMBD*;j@9zT:7V3^;h
3#U"@bW^!d0-lX0MB&.]P8pd@l}TA7svXE^a]BM7+4D7=3##H#{LsBP^A$)oZndyFUJ!_iyE$l"&ag`&V!
Z)M.DSRZjJ(C!?6ggBptOQFANHvC+~n]Axy3Ss15=mg07K^woc8`eE]M3UX9cg]eh/er$&h7w-N]$bTKQQ5)L^H/gc<8?RJXvun0"KKmqki<%v)q>7^:`UK`7?-oU48%T%xScI.XcyN`#aOB6pK<o-tB,@hd#p&~Mf_aOZPv/zV(jMx8T|-H3F[v?Xj[g?(kZ/eACG:~"TL?om#~OqH^h/#b&M#CF/Mr5:[z4Z([Aa$aEHWUN>#l6|b9V^*@oG[<W#^3V!MXs?!V
*o4XwnZ:T;#(=B~q"L9i:fAw@ENoX]ZFc1&Pr-#K38S1|&HGMc{SBcJ:+v4J1*n?:)X(Y8nRR$)?31>pE4o@~YH@Kz(]^nves>@]L?;VWglb{lNy|9<pF+5rHQ?aa>!G`PMQ=qp,AuJ#ope+-xKnJt3LWQ|W0X.!p5v=By@$lgDB(O82*e4LO_LSc-D!mweHc@WI.uvB)evRGj*e=>PHXQ:Z(0Z2Q4E#wZRi|xbXOen*XNt[umJZ~vLnC29/UV=1|0E,1GO#PfLl<xFT>gz!/x:u7Q=7@lthzVU:IO>W]phF/6LS^::M!7%Nq"4O(P(EzuA("K_]KCPV{-wfW?Ux.-KO^>7
aPgTq8r/F2pN,)s*3$|RJO=#twl4qTe4Onz$7s!-K.uQ"N7@5qr@{N}OLkVTZ6^sL_sx1ic!7Si.dil9i<$BnW]5?VdS)_@Y[?]2pBoHSv&QXe*0yhT20tA127SML-;U[V;7z.T7q!H2(*8/;#_/!5ArWCx$"?{J;7,wUmHJ_%aSI]7Oj!<Qs@I6=Li&%4iNJfD(,$yEP8),_N5]Dj[lYWaZ&r&M6>[--,2MO4v[%(Q$o8}c+@cpNv.RZR-XUOU4TP>;N))[1di.w7VOJ!TujFNV6Pvm6"XII(x&g(pxEv8E34M)ZZ%MW7[?#.EcGM$Q9jjn8##N"3c`?!asr)V=|EXPy>a3cDO+ER4P/SdgIX>ZT$uV4T&8x."NmDYn?Wc-Xj5OLF-0+?+!2fvICg),#<*g<-f3{Q}&S#PWA`hFaPl#s1JnLlStJaNy+g%u3/<YCSPy88xe;iAh`U.4#H/&TjWfa>emct>;MVBJG>ERb-c=ZE<_@">eA:nN9[<e0,m^wIQi!^6ZuU<(}^gnj>KwRkeAZSV={"9@ZL8]I?I>$oL!Zgb7n0s_bDYHjhN!A/hM<Ci_Dbh-h2<UjW`WK>.N4%lu]p>SgM/cZ&tB]p(0Y(><<c>`|PPVm:GR,PS+22!`7v0MeeT,$^c8R&fFR>bl:gJyOD-ojBYa7d{VDceq}cRNzDQ16G3n(`
$3z$Np.Dh!ltqI(S,nm?2k>YYu"!`8Q|_ZBKD<bHe!m)J!"Q0-i
)z]PeQamcC0"E0&m/7!h(N[pPy%di?
kd;!!nvTFw^Q@c*QOYA*|1E[]/f3;.*qjv><<a6.cR/-WMv066[S&S+)k8!f+,F1c58[95=ac3{x1
k"r4R/kdHcYO~OY){8|3%wut<b1U3%y4{7DG],Z2/WI1[T&L6]5:z)/S4
?-RRX7<?2U#tN;^Pg
CM4[_mp)LlHlIVB]XL*_E^+A|27[10Q7<?G=7/SjV0F&h`CWjE-g~5C6k7A_"lX%9xfA5fO8Jk7XjAUFd["JYT0:AnnJ/E[b8B4^2X0e(^0*U0M<#6;w7!},%(!k+AwpkkXF=_8e6EaVF:1vY@B8YYW8kH=HI4vBluT+eL!A4%zbfbc,{_9wPph4C.96ZU{!.ZP?Q+nEWT81%,V8)6?B7J&V-J+7vZ>(L!bc:"hXu?(?N4Fl^@%peh/V`X~y|3664xQ[ty%[^I]c{ss-GM#+Kpw!,f1mJ7mkp1]Xi+6UXk]wK=Jg,;7.]4sK(:qm-H5[zC8Iunq,&<T
eKZ;6
797mCq+k
E+t#BTn2_#99EsNt=C2&X_?gGua;<iN|/EX;XKYbHJqQ;MK3c;^c41yrr$7&!;)Ncs6&xc3_]8xIZsABREdO&Y!{@XG_j)LjbQw0Pje39B2PAw=~J[.Qb9lto-.OgvVHjDcOLSXb3"F{Uq[(HEW"(FB>cxK9&e_hSnjXhvGr&x#l
rl!N
Txscx?V9ghL0Y5;gWB$5m97>y2^7YKr{m5wq,UwKA
3,%4)wgSr=5._8HA5OQ|L4B/>9C4B
L:Jm
5u-*-H6<kW720)M_~"NKJ]qujxeN&';case"tr":return'*UF@aaMDY(oK,Q#&GNRo+%KJs^tGg*|fpEs-<-!i]v0
t_^<=[}^1>}u}y?S,JBc#x.lwz#72)I@&DW2r
WDt]LVgCSBJcdepO:&a/y-?/$VaLJS`nfg2B],^rife`a7^I*w
,~k<Uiyq+UoB4_*Sfapm;lh;Q4K{t1LlwPRoM:9a
}iD,n0.b1LyC`^OCZQ[b!mMVu$3GEgUAIyr"u2>"ug=jsk[fhINsrMO*t!;w<Q)n1wQKq;iDU
KqsgHK:+.ko_z13VtvW6
,fo3#a;l["rN",?IdMMwPemx98?I:iX.Emk<G4:.i~8E=rSkQ]Bxej,xiB4^?ld"?~o@OGX1yo8|WhDnA>Rw7<*j_H1#?U`!V>qfcA!`CxrTNmhdN>.PxFgk2-MEe@(A[1R%g4T1HArPQzklsPa)d@cTVodYPI8fR6=67Y/fhjfabZ(7C2Q6d*g>5!^igxYLfmF%1>=x<_M8.ZCQ;LF&AR/wK%,*Ev16@$hvFSF~T4Fn[ko_*M9MiDQ;llH[NeI105HncCk2yt-t^eR*-HNQE,VtT5br+Aqwa)oCWz,E5(,DE%Xm@qI2kWYE:$N;P
IHdQbYp.jl:VJLCpSe;&7gb:267/P8A}19?./1h8@S#XU(1VX1K.-hllS0D&8LW%?wo!qb,?#]GUr0d%D))6]:]:v?y&Jp]?z&1ui!)I_?-TZ8vU_|j.Z`al`#9X3S*PHvG&e:v3f!8%M/;ETO15v4^byx^>".ojZYozpuHk>h^$YX#Src*w1<PHJlb(Hm`M%jXJ^g9/?htn[6wx+>&vPLF>uWP*>X(z#_&JjDA;g7:"h`A<(5_OoUaz&:urFMv?JGR.C>t_iHa$>K*|qf>AN.C-/`!lI2+IoDD=pOdg,woktv1I3=N0LoxXpCk)nh_N*hj=V[-dpAI,(@Ep!lK=@P!"clCpsYHMjmiQ`BMzS+!eKz4Wo<C&(jM"FJy27wcW$
*?D0["w5Osdj7NFun<;je~h"F&f1UVi:Tb
8w)eU;E7>g;+2_$7?
khZtt&Kc^JGNf0$CfAy[1P/:A#^<R(%a;`r;Y^6
"NF%~
r%Im3bL<jQn[$eYC_lG?7gHi2=)6;Byo+M_Hr6<fjr/(.gDG1&`i|,}L?jIT11H#X[>FHs0MS!o"zXh(C9iBKC%rQ%(FMe/K}6v<L]j
[JS1h*rAC<eR<^vh}LM06;u&)j>MW)1
m[KRyT@48ryrah>#b)V_eCnyZ[3Rp<03ieF7);;R@X&<Z"R;ur#O#@Pb>.`e{%9?/eF;?"mDul@,KwkS+aHL{s-(KLN3lcM3Wo,::c4Yn<}Y6/|<ZR.Xo
9EJA}1-=Ji71<QB`V_<)309sjLddt[mlG:">qoMtCaDKk:l))"Zmz,nvYNrg0qo,R_yZInHi`pLrT4a0qN/S:WHO+>>)3%xt4]7[LdLbbse4YuAkC&>IU7+Zfjw5^r^6t=%v#V=yJ-AoPeST*uVbO-dG*!O#/-L$6Zf&{!JQE/"_CLriFgQL%Hw8e@Xx>DIF02rY-NdP/RRhW-&4
%}e/cg"3>;4,$#OlFQ0ej~jny#s6!O?NHj8lnO^O+l*yFt0EAfS>05fF(X*<[)Vnm/b[2Xk5ws_}!uxe`aWKn|E&P7wqc
@+.v!lJI4HGZEEi?Cnu#Ie&l/wZPOU&B<xT=QIBZW99~^b@?$~O?n)qobkBXYhBlMg=O!7h::%4Hlp"UwDDmjOq9TEjekwih;14
]Q@m[O9w85a3)m++eRk8VNX(Uz!V(fYG&8
p:#P3=2*SCwY,?:?!;,0%y7Ji*CuCG61C)e8Z%4_s;#*6xdiAx$ufd:5Yi43l@vMwg?
{e,v~_[c,%GE22bo]=`nmMJ]0l<P.eC<`*BH7K_<%;)6HB+MCR<<T9Gty`7%^HfLBc1=
*;QA>FNf
I@L)-QG>Z:},V<EN>#X6zO5,C;~8dKxIE1/VptcG!`7#%lL/Z6Bo~/VAs1]=pwL%k(6[c,f_/,{46i""7lRZPk_
l:X0mg9+"#ecIA:iP3g6:J@rAx1F<1:[-4d?Pk<HYMlpu4VYcL^i3<30z(],0GW_^xXZS5"X&^84}IO7BdebBXx>optJl5s
EbP(A>9ARFKN7E?mP-wO,qJ(b/)W/2yJ:isM-3K6Kd>_ICjfPHMIY9KNq0irx=KNv$T:A]iYGwf$Rb^#N)3UV^($9?SdU%q5eN!i=P]?lk`pbG25iJ?E15[B{lL^[N!sJ*>NvjNAY1+sUy]_^^uo_KHc_PxF%
mxEO2rIGIc@"r,JyXNvRq]R/u`_eULEz"s%+i?R^wjw"F*+LtM%4FHa6@+#$`>1sWDURv,M?_f*-[P7@Ph.t>6F=~oMEP/Ei`dNoC-G^FL}Od6a"~Ay.1Ss.}v+Ee$L3|H9NGK^&:%L]H("BwBnC<N6<#9}<?dp7P-8Qs!`$5tm9?xY`9XgaIr~ot/:z#Z`#3aLHZ!uQr4h4H;B,nd^Y4ZD&vFe48#&8woMj`P#P+P$^_
ByP^z@T]n;qu(BrMQ9l#10/y!;$?4Ye!ockyxp4U
$DKrW|dUkv?_[`/{P55*Q#iz#/a}$A`"ZU*j"3^!bTA0UK/;m^6xq"0?ON:6@KRTTNr:HYf10q@B@~B8&$P!:Y%@DQM]H]q*2WXKZ+AzRWP?fONXXk=(wgm^&q%}b
V3mO4]Zg!^v6*+G#
JktD&ovuTQvv%F/^VSRC
RJDWIgcB/yjK]X$X<*j98:bJ`J7_]gVBod9y4=26"skbVB:M(7ZLZP)WB>i-lGUU6+A</&!/&CLoY@BGRsnF)/r8D{`[f$0^w"Lrs9X=a7<LO!gja.<=pie]>}OF?%]v>eWa]WBcin62Ei4@-FKj4Kxz^-/:X;Fk""by""y+dX/MOov~ZbZ;],P.lMR,<
$ol8jNeBA7Rp7M
0M$"3ks,j>s/UPyZTCm8yqqpyZmY|@X3WqJB~(K<]c9CbP(=L@nO6Z<r>st`jPJj."gFo[E;DJ_:<vr`>iWrCo`pLtT]m<]>yDTWvo@.9q++fW-/?r8>whvnOr5?S_^&OcsViV"&V6a^Z*v3`PPqoScchw4?Gq)ydRw$%U{4N5;-ilTRKupMk423}38dGDSnJnlm}a"rQU:y_F98?8dJX,2R_:mO]lNcbGkQ!m
Bo:TuH,K"x1k?a8/J2c`wA';case"bg":return'$ev@if{p=?T)nh>])gTD"
Y>Q4(
o_BwA86Oje{sV/#Z|FA-uI-@l9!$tiYCc-OuZg#xwKva2x2!1$JbG.(/`vzK9MVG`vdkSy@O3t*nyEWi*B|t1P[h
tU
]TsxQs?FMa-]GHQ/:u0K%)dMkz%`.Pz_}Eix;c3LIbv6DW@oqYs&ZbKG>75uJm]Mu_n=7ugD5GZYS@vMKpk_->Eqty6?cH8]K+^B*Vk
m3Y[u)MN5
<OT3^[E/D4D%)d"IRyO8Crg7vsvV]j5v][HRPja"In1/U]2wtxrdFBo/dMXhxH
,15Gt2-~Fw
$xj>:>ZI#nEZ-;JQHE;%gb+P%_gyUsQv(D>>p(Uioq(SvxLCr]@6v"jsY![6YJ5&:[cGaijLOlugBJ@O7,_A!E8xvW;^f1i76l
lvy`G`g"!gH,&je-NGeJp<qoue]C`TkBn`5asD.o4=F_`B#,4"a-F;
LQhv*wXg^7"/(D
@pylOV*zJ|VZ<,=Oq1*RXVCaj9ST7u/=>N)^D
=|SXT2AAFZdCsvQah]Z`v)lKxPgYiyS9P+VWTOH!cGL8uzCWr44.O`H*lgPL9]b"PrK$+,%yy9O#.d6H7
#51LV}LX&fd[=,VO3[nK=tcXRduO!Urv+*3KJ:C:<:Fd0+YYt^)yZKFJE#
5HP*>F$4oQ}7Q>>rc8jm=yWE]b1J@_j/z#pMHmpCHn}
B]%$>Z7W5fNTDpFxr,wAcnfv!WGkPjd/<p>.QEyBhN~;pjF.i!qJk.^Q*P~g;sgOmQ-MHp[3WJHq
NMgwtya9AC^[[*PA+A_}HTC2^lg8A=/d"w
m;3dc?Yg%c~40;I`<TJ5yM&bjPb71Vt_>;J.$gQma/cZPNp[gw~U+Jn+iSgXLZ1N:r_.F=&nqqHv$yDl@dNXu8GRf3^o/6RBdxZEi8@>gS-.-R!06$g6u6%,XK0,o(-G3djhi0;d7^P4{tx:s8Jjt/W0!T4.#vRhgAJj`tL7aE!>8>.cZKvZE.l1,HoX,aypWKsuZ_]<!O3W()5MY`kxjyVT@(V0x=XL|b-HZNts<X
aGZ@=9O/S&Wc
=pk_@N`ef_C!o%Vg}g[&I,-=xQ2[=_Cb#47:DLXo0Oexx5oTj*px*$,-T_*H{I#pd@R@Q4],Tq%X%#I`.6Yb,Ew]6cfH>!p[R*uIWDG`Bp]Ejv)Y=ncjeSlgaR%Rz7Uk=]g:pAgG$$}jXODcAY/B7.I
BpBUS+Cs}tb#;v^uf@
9qAcV
7PME#1>`E-Fz2.pW?%sB4
-MaE8T<7$;ixw^k
[EAiB)Mfo}aV(o&4;4hW1l"lp4CuIb"/5z9&YG@wPM&F&pktGdUu-NBlVCVbj
vQQ
hZjcJ.(qcygUqVTM1Vv):.d8fBh[FBnoR&HF9k"+:;Q`Z0xT8GQW8D5e:^c?TJF*jIO4=s[>;ra$*Z@e@>@gB,GXZbuny6.Q]9WZwU=},!4]_lvwXf7k@w>>2%3txcPO:>P`#a8.gX?gp*ciw+1eF;JaOhd!j46O&{P_*)`
4oA`eeqt/B?(ZG,*Hlj)0Zf=mvi%U1JGbVo_7ZlMs8WuY3=AH
Fyq{/ti,L
<<C=GUdq(Z@@71nSD5=}:-cT`*6#6K;+u(Zl+H;#b=#x7q:Tz$pZ0F(e(??+v4)"EX`]M-%$7z.gOI0&6Ydr?E:I[BV&I#p7y3QZh5Rj,xK}Y--6CG.7w1fHu8A(GY4f6KdIt^0A0,sKQFhc)LNovv%y:@2%5qUEN=v}N]
-9g%wt/ZK0Yf;ZIK8Ts]hN]%9;2F}9GdL#fvKHv7X?
"~=5E~]|xkM*gZFz
9e=9qnLh]rF+9ELB([WE*$d_v;!
AAzk9gk3GR7El8w%5fPZ$fDZa_eqBHEV#G"
7;~_[Sk6*2)TFJA1O<xGHO_r!CR-Jl;`V<yp9h_F.n<V5N2_9OYyBn.YhX-X/*UGQSq!2&K;a)9xxVY=3`@bV4@4Es{qi3JD&b$vsvMAa[/ZzT/Z$fc:>DB^Vt@4i&*"{H)A[V=tlxU
O-2ELV%7S&NmL/;"<ALEt#`U8j*%9>j*VsJS"KEg{0k5eU-z$g-c+)V.GK:cq;mqc?+6/
rl%88Zo-L)o=AkO$6Q8ZvjhoV3T6*kUc3xvew!#(-"NN4I6bP;g78Ny,W"t-etmFE!Df+]Jri:bGa./LrvgU;WsvM>YB"=l-wC!pi$sK|@R2.T:7l0kWmgpLIV:[p3}q%MEs`F6FJYm.eekAVY/C{j
w>35-]Z!jS6EuxS5dnyJF%C+.cjeCAE(2?/wl*R9plN7nkI!p[GK[<0[Qu^8NEB6N7>kY,.3Zxps4Sru"4L%hcY{,5tUxg1<o;N/q1/syObFIk%=CXeOoX`LHU2mW>^5IhRT!m,A*{rU#E+^:`9spsg)h9vL9lx8Nz&&/Z%^u8YlMaFK;i`-J%HD;fVC@oOSX;N+!ca@E/:LX-p[cxRQD-UohLSfROQ)n;#j<bTK#ap]8e7n$*r#Z=N#>"*)A7D:vi7_rX.WZOsi4EH!gFU5)(hnT.SKmLP9#gk1i
m9hZ9,dw.JP6++Y%91V"DbuLUi3^P<44ug"T!@U}2*-wlX]fOM!`ufd)%!b^Z*EC#Wq+:{9/$g"}yN.qPTDwJZ.NG8KL&K=A@TB{g3"8^_PV%i(@&a#%
)ZS_63pP0#8nly{&cs|r=lOm4[$u<tf=ix
[DU:J=iW<8=LJUQS+^8AWkk~`Vk29elWH(?{rdKF4JaJ/-#.^$5tl[E;
fB,&p=wQ?lLkH-cf]rW(!XOV}Z+e.pdeuve2jT%Bev3@_H(A.jH2_ZoX2K#MDaG%)xU^-gX9t=~TW>N?OE$l#L7w^^~8Kc}0}m0FWHqNZoivEm_9]@sB:SPs^o4@u=QU/)E3<gNMwtmFu7[J]nBbWnV;c]8@7+r()mR.=TI0B_3_1Mq8[>6y#SG.s?Luy>z<E"5uDE#!Z9<HagU2Svj1=>6yB=|kH8MSd>lBdBI.wN9t+qyHzN</=
S0a)TRf@?w"o=ICwuw$95c=Cg=DU&tMX$YqnL.)[sUv3n_w;VWyMA"&aX,+8o.f^*:lpBrS,&2r<{RMArh~cWWw)XbtTqaaag8-#IJIMWMg*[7(=$p[YF_H%**7ncj2(rnZjmXW>`jP(.2Vk?4|8sl>4}nT"nV|Ks$"$zC(bo"323>S=XS[So"!Wyi8e+W}K~_EaN_X]{/]V"QqSr-{:/11`f)wV&`s&w8R%x_iI*IW>ge<93IF&GR:u24}/|u4?bZV=wb:!%qTNmt6=D)M#yMO,{&2mon6@UsC+}Aw<GF$U|>zOXh?<jyX7+u,@_e#7AxD71o?VDW_/fiUQ&SJZotVd=>~,KT?Hwn-H"2kIgmuY_N<LV6Y4.Sr$W
WG*7Y)7,+QW>uGI`A%)Gx>2qe!lXW2X$2iGG]>D0hD!nLoP_`yQiPJT7YKIY
01F+@F1>YB7T#)rv/GNl8Cw,)^QH;(PH:1J>+C
09?+3+5h$QbLhCwk=Km_F$W=7O2rI=GC2"6rQb2^ae+CfvL]+vV_l7"tE!h3gHvQcc-
i7B8m32k9RU#j6(-n=7ak7a.px99}%"EkNxA)N<tkIyiZ6+VSJ`,GKY_(N]wiXBj4t9LXH$=@6_LoF^c~%YxD4$$#PM)^tb0hsYL>QuR<wlNS
&fpZXL7(55}C?QtfsD3Fst&!*bZXH5R6c)$XQOArqi+$y!F]6G}n"$}N/u9Iha5kUe#sK6.aed,28wy2`,CK9^ts)fFef)-c8nB#L-$]x."fP!x%^lnDqq!Vw3Ax^_*L1)GV~3>QRE}w"E)DK6zR`Vx&9@mRj,Y&f3u-}ngodh|3R]+#?z$
c8L9#RFCc>,uOP%I(Q_STf*';case"el":return',h_;:aMDY)R?lk)$/6R8)tLN=d,nI59#E=9"Ocqvdpp#O$1GB-3lL#Y[z/Bv>e]h^^5k>b*60?(K_3[?eg^;XS5o,#Q<FMO=BQzue7glUT`:fvm/6lRT_E7e"TX

mT4>H(j^Z~.#?1iZMqZfjj@G_k
Dqn@o/@uO50q<F9h8LFk4[<rB3cyiIDPKmQmZbi?Bwn>Np(wHc=hMPc=.XFnA3ZI;eSO+x{4L5hr+juUUFhIgD6F!B8G9%N"a6a[qIDqsmQY;9u6ZsJo)pfNow,T3mN3A8Z6C&XVxMxiUcH8Wtq:M^-i!bve_kkr[dJ0|WjgAV"O6P;G6Sw>S&)r9C|egDJF.(B@Db|V,CO>1#i-8a/?tR30}9CRHQl8q9?7)/)&wDPTtW;3u/&k=MO2P2f88XZ-d+d&1S2!L2@76B`WAMMIsinUADit+Yh]CDW*`@Cj_NFE#N@y5=lfs;s5E?q-6`HH+:(&OC.BgGJ6}-&@]g]2&VFGe44f|@J50QE;g/ZUng8sOos;P/vCp=xOEMu6hegWxN:u8%/P1s{/,c%([<44
Y`S3T(#mHm_&AA=llrL[0krQcq8|PMJ6*>G;lgU1^QkcNV*yK:<IWF
>>x5w7*DzA
DeQLQtdBkn*X_ITRcAwg_>1R$KD{v$Jr?$AqkkB#f%<M47PR<?okjuGG]<h9rf4bVA+e1H(^_x-c]ngow:%&U>P;cDd;2Nm["%TU:clc)FN5h[Yk#43-Fh8,a8ko*$IIIRhK4a+8yU/`k*>~%6.jpPhp.}$T-goxA:NX4y#xYyaPOC$D+fG_MkoRE#n^IpO(oEt!0FsK!y_QQ!*dTPEdn
-S.9Q7v856%EY{Gjs[9Fvvb(kPJw`|ns1}$:MpWotiS[-BgS0/w!S(1<Z."}h-VVMUQ8E{`xw34zP"Od(O]|(J^kB(?[Lv
SRjqBEO!1yyEjGl1NU(hdW$GTiP@N$<ST_0Wl;jQx-2hM^"F~P[wze|m|_
fz>!lQqX!!aa+ACl=-jJYyxciF:dX#rzf&E[>!_Z?
tkTBoP-u18q,?yVB>;)cND5!VD#slm=bdvY44!P`8@o~usU.Ts7r;}DGWQ6bcgCY:I:Fmw"9-CZ7vE=X]U7tUvj8(".s#i=
TjRvU$W=2x8.U6:s-C(~2:.&S7*2^J0"d2_9ETW8_i.QYJ-O*TI1F<N)JYxscDRo%9L)7z"npa7#:U*X*;I=73k]VYR4[:8.D$"5.o7H6t7,L_C>l?9CG<?wqw?$hRfWUkT0J
-OT%`_G%C^VGgnu~P9."qEJ.7USjp+TbSm@Z;zhLaLFUsq81h=a?k_L^iA()E5e3.)&N`;fzw"Ljm~vA:H(T?
?N9/:=&wSagW5/P{BTB}?%<`5[km>traS>,#Ay)L]`TyA0$ie22qAp<?15?9F2Su2h$[sfgt*L)>,,mmT/=ACos^D4^a6-k%YN`^/wI$1:)nS`Sinm)n=#hf*x`BpXg%9G.d!a=dyA>l]`5{GT(]F^<sB5uP"GTZjC1c+^J>1?^&s^<;b!9a*%F`y0vQ/:!k:|Uu4Eq4OT=_/KHn!VmP3Frb!DU>FHR_4yCG=W_G8|Aeg;e|Ygf.8bErE6@&I1=m8UlR,4_B%|xv
&"&#+Uv)UpF)|(%8JR>ZXZY8Bd!53O,qGC):vZ488ggI391voEhz%o^vd,dP{y&cdDL<Si6Ny)m){$p_CB/X/ja4V_Fk$x>
ML?Tu61/@-5VfCuXNGQ`js#v_@k+>EDN_[2u}1Z$3c!MG:bO{YQ[o.Wd9?]B?&7DiDHEVefGu"_!-x[wjLcu$%%Xz?~O_EzTHXwV9jB6-dSY[#0FLn4
rP9>di+G~t"0J)vW^bT<8D/VZRxC{tq#vG;17
G(-+W1tAC#|XFJRjh_:ynF
@]Ff0QU*8iH0TH@|X}f8)20FJ/Ny0}O8U)dZ0TO`d%"(%G-z[,3#-`@gA7%X
%*VnI0h-TBZ-gfg_W"v/L[w-^R8dein`s0)Ot/JdK^!^%*4$JU
RRmD4T`nWXfcnDYI^RSx1%R
PhHU$VxH/h?1@[TWi2+LGd#_s2`6_#AuIm*,?B-|PrZM;y_1DJVZog:nsPb*E~LZgh+)BK<o)Y?y>P0YWIpPb<%YDY$?0CCXh8H]byX2(,8nb{BueqOs0kBZ0Yk;Fu#/xnkk&#d;m5us(jpMg+!T:[MQ37:1cgb|1./MytV|?^<oV_n]d
ZUep<)V%XGuM4:Uv$RoX"Qd85b4r3=$RT^S$-KKLGy^nE`*Wm]r8V!4U4qL]FThJV)7W-f"Ht.kidm4S6W4+hEvT;(RY`BNgN|nHL.:}9TA;+U[lwTfxI%9V`|%v@_s=y=UZEdf7g#>MX=4#4%)h9Z#KD<&7)2BKI(+zpoUU?weCQkV0K9:)5(_vcu
t%ba-6,*iBU"wScyU1:smQqZ"
|!~e)YYM-[xRHZdPu1vCm1f)F^bc?viSdwwY!<VBkXiMLhWnuk(ymT{L
)&sLHSPmyF[
[lh&m0uJ/ubuVoohma&HaQOz^K0hJ[>=Q1)S[6`pN]c]!uDM;gLAKa>c3d@lekx:f1PC3isESgnZrz^-1=lm-`[JF3)1+HlU/v?;R<:jAA-dR)&Xuh^*eIRs2bfB1
%ju9*XEKsyTJl_9Vr%k3:kV}NJy,M;3W6E8CI
!`38Aa3hb]NF/s`H6F$5)
Jnw_:}h_a~
K*3glQqBGtXFx>xZ7@+k)u`Y!;CIY2gyU0@2?
e+TV7gib3Gs"W`_%}!d&R)rKb_.a>]5HYL1esIxlc#J^_s5r{Ws@*Gg3gR#f6!GcLJ"Ye)/1{?l4Cmy1j&"6rup;wmMc(C0l)3S*oVD26YwlYdO;>:3Y0Lnk`;"Xx+U/WwsjCm|$0P*U4c5V(Ck
K9gE-GwV!Y52yYW)m`wGU[)TGGNHJSu3R;b]^R<s~U!l9>YXEP1.&`8O6ISR"3mwzfF.iR37@H<-pDep]OixQM~qc<i"
Mu*9hp"dD#gjSD%>/<9Yz%EfXR4"-ws([xE/^Z%28#Qxp=(H<(LdW#?db>SXV<<agh))3kyDQ;Vp9KQ<F[jZmNOg;yU{EXjeT$*4/yQ<pf.>dki>ecKZsITg6_pqHyNA+t#0KNWj.=^c7yypR}em%3%x3ghJe/uvktEJz)A},&EXnb/z9X7er%WyKdPT7t5~e4"!X8<~x^$oF"<B:?cVSbV]?Ep[H)B&EZ7?k<=kq;f_6&_xOr%K
SZj*Nx<d
;z/C;4FyjP_g.[*g;.&
=YmW)FH{?/4OJti-w+n9V(j|.=w+-#cTs$XOIh$7[$sh)%X<!{Q|2B,`SGi+w>Q+tKOn!yR):kAu]
@3P>Mw>zk;vkFbD3kpb-f(:Nn,5bKhD|BE8r79uoX:PtZlmxgF!_b5GPZK,$"z1WF*M*on6<,9/2O`YRDIk^:DI4"%^q,zJW;eJt;=syaXD[cp^jYe4gfLz)`zA*"9=pm|0K>a=!/eb{^u)2?hc|s1rl"J1LIC@tj,Yq(e>k>j1gGK>:g)v*-7`uE68?<U2]j$k,TSDRod,
,#fSL5te8PZ0-zed0q?*M_5D=Li^cfL`a#mi9>2dRjvuQ;7VpV
`lNG(kkoC.=B1s{SXg"lcK/iG!0t{i!ecIL7na}MKy%X][TXr&.-q1^Hx]$ojmsAf$!T{S,LU?O3]M(ybS5bu.s_d5u*=q;xKAbh~5]6/k2AR7^4yG`Gm_b"a@I%dmmv)sUU}UK)iAjQ1G2XV[cX37#4qn@]rsI(Tpn.$r9X2P)({2d]bDQiKiF]NZmCwwY5I"?bp6MmWWL,fjo6"z&0I%@Bpy`!qsrFe"W0y8Y-B^%;<^$
O]$I4u5MT@x0t4v
d)6BP2p;jbEq8o{"C,,=+YJ]EsVb6^<Ccu-tBV5]``
SCtyS<4TV&faZ,S`wBT-nH]gSa`tyu?;3jBZXP#5wQf!;(SOHqO]SNH^p&:erYs$m_x{x6CJpqUd7MDo$QMffbL~FLH_W=H?*(8/o^54gJU/,6i|wdfXx@,52)L!c)i*5o,Aipl9V5%qf1fVcCsas"0.y8s8Cdcw*R0H8#YwAERkC,27`xveVyNShU&9NNyiSh
uLJ4nJR>=Z~L#H^m`?3,[@)?)9e8Gt@i}_3jf_q<6tSQIa"mH+Sl)=(yD]x>cEeg5ookXA$`W&$oKltW_bPePZxL,9$x:UP+Ea$^=R}l=o0K.+;a!](COR]3)C0[2b[MZ=
[(mS&U&AM3vz-gW|</rjqO1v]B_%X2x(EVP>0Zn1K*ZO_Q6(C`a;EI@_5aj?YvdD9M!6Bf@<(26TnmiZJ]Y401^IoCd7=$xfo)';case"ru":return'&ev;{5Hs&,zGFv(#qcg2YSnXt*>@E=1N,wS$9;)${=y%OPN<ST5Y9#MmoT;&y=Gjz?s*.)nT??6b_xPtql`j*OB@Ka,bPc.o~JMFk5)f[`.[:JXGRX=<-l(vgbG_fVYh<csn;E$`*[9b/$GhWG:uil>A[/pA.54
%,R5mg%f$FXFDm.VRW7M
.Vc{&iJX<QDbH@b7Fm`HyP4uJaLNn_1nq(8|k0hw4t`iJAIB+jxzYl,v_8V&>lDw"J!kgn``I<`?o]!7H-tTrFXe;n<KRqV+b1mAMgh2)]`fl1k^9E`;9[55Ia5KTzG#1g8D>zO!$TBVX_Ll1tvUD,5z6S7RS>4&vzdhfw3XMeciO[Lnm{B,>W>W@Ul[3wy;2kH[T9kzc:5|SP(_BB`gF/&!>o`eZ~Jbu$#1x7RFW#[/xfMhNxQ0!^)0Sb&Z%xAhOCbn!4N@;UBBg{Mju<PqZmhPN7%xS/uvO[3!q,?P
RtyY2R3Mp8egnd5l]lC@aX8/+H|-pH}#R=@`Ktvel7q(cm6-es>n-Whmfn<ns=m%aqs[]hkKiDy[X6/cDDk_jQLDhAml3Ks]7Esl+Pqw+";XzRoRw4*xN.wl+4^SnV~Syo)P%ir
r@#&?II0Or8S7q1$8U~-H05o17ES#@c<g_U*QT"?BU0MHV|bsY?&W6iQz3|DEx8<DDR5+$+2)/JfKZ=0FnMS/3so@u]%vC~
TKku7h3=n7Q.%gnTlZ7qI8{B>_eZR@T]N
vX@F,jA=+rB<}-tI7V,-_<zg<.>f~71%nD*j1S0re03#]CHpZ*pFnYonS[{(,uf9jhrYT
:jt!VZqrID=^gYjFPdf-^#~+TsaprHZj+UO;Tu]p0rh-VDUGYZFKQAHAbt#M*#RU1+dV9)uNai$
^!$:W0PWjB{M89KdB5,]y[p
4EsYk4M^f!0icG=junl6S?}ohX46s
aVQ*C,j#3DJEsPh4|j|*8#O.
=o/zMo8ALbcxJdE}x&K)$0ML9;@$s1^7We&s1Y/CU}:ZYI]]4+:}&aB%!ZB$pks3_a&upX]Y8d5#pfiuU%<gx_/68TXi>U?D!3Fqn?HCY%<s=S-N2C]g
kuZ2Xb,aYjBnjHonHf6UdK|$G_}Yky%6RJP?;nX7W`<hR;M:1:*pCaOQ&H9Rboc"iWy]z^).l,FNgyZ-JI?Wa#x.blbg238nnHJ+L2;VZpTqE2pW"^L05hyV=m%:8(f>QaO>(Dj/bMpqS=<*t`{`I!`W$gC&%F1g/1,yG5NT2,>F`!cV>+"Ijt5=]QS/4W!87G},FY#RR5^xFqopAl|pqtT@yeQi*O1tk0Y
"P8]4]#Nj8zkdZb/vO02
-{%Uv+Z4Fdm?4#C3">jiDvMCqbUI)w!08BXtA0<K4cOn>b"ryXc`)J
0lv,m6b11_NS]E0QqE"Il9#tE<J<9tzISD]NQEEa#1.(JU4Z*ZvN!Yo7lKriV9"xR[UY]A(NiQ"K{/.ZO5hy%KhN7ynR=Hvb%$ZwISd0+uH5pmq@I9_FvUx"bv"V#<q]5kDXLTTFWh$Y8$n(}W#Ti.2WI^3!b6j:[Pz+^NNVd((Zy6i3&yYnSAqf@dA;WNeTUtr=|

:maBF.Wq^k;Nb%Jlh<*bdZWBl?t>6wSNsD-f`{
!9tsmHLYVx
A>OLd1b4JYz)A$/I
P2IE=-JQudP%oq}@(pS^3_{Jg!jexH3^wG.]L1J.j89l!fVO)D|EgQyFw"oOJfXc$YSNkO<tzP`RBA35MH)8%UrH`Sa>|h9)B<PqnyNAM)a+wHpZ)S*sx#N@fBo8tmcyU?(uPB@BPuA0}Tx0<=RFB0/;V<QSk]/a,Bfif$u=hPfV_=l@>xE6gU?28*lgA$eT12s-#<ZovL%Q7DFoKTrHClIDgSi:~P%gA
m[yN|37$O(W*>a&%D?M.b?S`x:^8$1?[(0=$0reeTxJuev^DT6oc
O]M|f""mG1CO_rrO+Hs9x}6DcagXn15KF$7d>t`~omdziIN!5>V939X29,lLrZ_XC)b$_*Z_f@bb)
^;GNB!1X.
EP8@GCar(y(g(}0hB7!V="&mghXO]J0X>LlLKH8?UZ>r+nG[&ZIMcc1EMVUU:^
v`>cE!3MxfIib^V^(@2p&PsHV:Fk7gib-3vqT?i,KFJfRe,8B*OleP*$#^9?.Av)+aVW{GT-t3i!ap|EIaGXI8mRh8h38<2;)k%mp,`;_FgPsix[E#dV=47"IXtJOB2kNym0t96s!1SlKmzaP#ZvxumTpqx8!d&[1
e,Dc/(;/j;x>"G,nQH6CF6BkIE!5v*NUxn#^osCxn3Sg8ksOv=/bW0uX^0Q]&=FJ.+e
b;V`Ac%6iB%Bxg*>2G9
UYI00;m+u@qeQ]
X.7lLB!TZ$nNK&
V]#B"T#C1Gnc+D_>o7+t%X6"/O8j@GWR"9GXc2,dHoy4>J/u^!=HN]Y:Q;.3B_.K:Bn6FZ,ah??%S<.l;0
"/D&Xu[?:OR)
@PY[tZ@dLM*Q=&S`ekuU-L^F%A>V1bvMRgg`m_b9qt=K@Ro?`K_]U&YH,33WqI*O;/)`M3+V}L_*y=BVDisWT=jB7-p?rLYLhX*uW(:fNjY@]#lEDF=m)XBJ3:@#BaqeluAq&^ITEKjg,G&`^B]gK3qa!eIwZcc"l-H[*Sa01sjt81$2eMX/(*|T&HS^>xJE`JmE*vMNf@?oVO__!lBUZK;:dUX3:3RM$Drpc
WWum`Sr47mA/7c^UL[yxroxiEnV-v*UFVjwJ{R.S(wb+BJ?d&&>qN/UP*$[TP(VZ|[4jDlg)xJ[_U9vvV+J#4)8R?VgZeG519CkiMaGA4L!*uh^L`h(H_#:t:h$P^I97Tkeqb@fC}:9%~g)=;as*nZe>Hrm-LCtU|>^:/kR=(69-L:O-_K_`w(UZ3[o2&G
MVd&xl"}tx-3qNB<,HUfABfucZMel]k%:k4hnS1ZwfsFkr^Vb%i}Kj8:W3xp#vM%.KEp^pc6B59-CfOjcy6)DJ,?TWB)Q4#L:AiyRy=(W4_.`v&>jI&]#UZpF%=^6lPG[FE5o/dEv&tD_V2q`V6z0gi>E>7Y8c]vD:EOvPwrmEPe2b#PkO2:`o`m#&Z|y?F[["Tm@wM:Qbn")3D~&roT3%#tByth@9WZ8APUd_bATjQ+20%@5s6EPD4m,D@m$2%VsNC9]C#7@W#gi}Kr#5VrpVx~Gl?eb
j_Yz+x3~[W>E(N*y/MbI<dH:)l=FXN6ryV4o8d9P>yeXX&7P<ii{DUO&wksCb%#i[IZJ1bQ%/9,DlsA/L%-5cKikgFLO9h"X_w?yj*Z>sB*P)sf(Dr[k+Ds,9;mZ`G>NM/KBK_tSsmll0CX2GTxuL/_YELS(]~0-Q
Xv>jt%cpVPFTEQ/2^<;pLea,uuVzW&N1,RTk2F/U++4J@t2):[dAiJSO:!K"i/>mxCg9>*4gKOIT8VMQ?14"RUB)3unkqU(?5?/VXjfi:eI;UT8nj=Ji`Sl5F1D;N2p_D.$9i+9HX]J!P,1[IrRWXGpD,Nxga^GPm&&l<L,.+w/[]QH#WRGYGwYltV%_=Vw@Zejn7.O{4U)8yH46;ea|Fu@uD+lV>Y7Zd
ym?BBKm1mPA__znLPjjx*jp[L`X]%*V$>`W9b^Vft]G2tjm8DfBxkw4<<Nfre7mv>]#vMF!o[C;<Ya%_u^HIw"R&a"Vfn++55Wu2
ynv2^(2wq[@aHCfkGmvs<FjTpbCad5QRLnqtz2F9m$B5AmQKqdku6c1i&*81l7I^43?HrABJJtL
zP<4w
?J%mm&v_u]XZaWlftG^yIQV2)+z2Cq0"@nw#LoQ^jJ{Y>t#Y|0
N$
hvaT^*Ph?Znu?_S#:e<JnXc9<t$2(-4QA
%`H=/abvS`R=-rqhb,nx[6H6*`K@LK?),M}K(3l4=)-.K0JxVa&?"StPK_wmq`MvL!HB;]mt7LA"FbZB2W(v%^:=Y(uub>Z<PX.p*I6["^u6;MC3QA}Kw1
*JaOJn?GVOh$3k:B_lKKr)`34d=a;jY{VV!wYj`H3rV4)1yDus
3nc3"!$n
WNM*,(EYoo45HTXyaA4]9r8.@!?m;ach9Irr-v>*d3&lK/ws@w[4*>i?L+5UgnBH0L_gAv7YmUJH!},]8ZU7Z5-6)~5[E<O":G)ao-<%WN4#M4j"SK>Q!D[51RgzB?h4=BF(1Z$WeeZ`T.3>:;pHpLdOc:%cG&[j,p';case"sr":return'&c0@qbSZ+.C,gY=#+N1a?:B7=dsN>Pg21C0fplYmKwSfF*hLl7,ej#T),WI(JE}v?wih#M.rnkr.jQK*Z$`DQEY^+macZB?e6E_-MGYA@Mgy]s
W1)F"]^Q+j=LrV]h%9dp;/Gl2:9qj*W{f8e1aY(Q7VwI6%uV:{7ZA{Ae)tZq91`lKox[Pb:.^,3laAk+i5C<3Ltt;,(x.{XH%DdQ!=
6Jh=-Uu0@Xt%-KQ[0cQQe;gL?_Ukxpu47+7Jdvm)z1N;bQzZi:YV:7;b1-Rjel<uiDRKo@X>WJiDu&"-afG+Ay:CmAO>$#NDQdOg:LS&5/d;D#RUJVL!}5]2rlRN"(lG#]=Ga:U?)4*Ye)8s<*37b/H,P3LDs%KQ_<XbBG[]5<(d1pSNgg-2v]L0y.vkBgIY1l"&%/W;bDlJx>(JeHlV5@5IqfRv}Xo?##gy=qUYNE%w,*="d6r
iP|NU7-m,Mi<LZHR|tNY?=[s=JB@7AW5Wg5S$_zd08(_]I{=|"d&oqP"
o%_f]f^`PVge5X1

=[Q:Kxw_Z>>wE;v!V]|geY88
4aYPWWE)Rr3~a:8xbvr!i+&B0%L|6_gI9oL|:zNAypv/Mhtc0,nTDJg_
Zu3B2_7qxsZGj0h_=Ot^4A#2(>79X!ojQo:SaH(FIWm!vsY0O%DXl$5d3*OP8!GU(04y1(Ted^dO;*YypC3Ddu5?tCF/F2h?W7~0k-y&_(*_jLjZmds5{<x4y-1C
BoD6AXo`clTiXM9sm^<</P9i!dwfW!j|4oTifi#E_I1Gig=1-behGZ-TFANIK5bZjypj7R$>v[
Zt"*9HT6v1WX!6P[>6Nccgb_^dP0&x9%JXs6BoHv1W?-huGM2e+!+W"c<I@@<+w"0SDRqD?1%?.+XcQ/r[=bE"&Qenl3VP41=7qZ@#uS4+<+aZT#i.`6XI3YOd`D:3yYZFr
|57X
ZiPwZa]n7kNGPy8i)]7><H
q"]x"NFL)YHt{-Q2H0>_SEmMmCZh{wJ7gE,isO-W=;-@x@]FWX.80_g/4Q:gjTw`R7_4<`j!$&/ckI*)0<SdX9v2Se@x9P?^y,32oZ{NvNGfP
)e;9k+P*BcS5A67WXE*xME<2KCk[4NQf5,HJ9
;V`Rs:G=kn+W<&CZ{;gRO.)m9:%FM#K*>MoA
l7v:HkFMURt9@W,Lo9Ir..Ew"cq4.-PSD^Q(a<Yc[RVj(vnB5=DLfO^S7b"H/+;`UsxAHX%8CYWFe-Z5SbK(PKwhb]W9f1O%-+/#@1[/g}Q_sm!+0"b/,mN[AYP@3<moHG@+KIY:dNS1*3y0I}Q?)f_=/(46%r$>`m
X!Ci#:%u{--X!BF:z4+MTSO=K;*6W8mx"eq`?F}F0uSVvtYISeGg$C@>9/q<7,tE+qypU(Y+ea
1D;V+J)9)9u?oFCt:2s`-sNh$}fQ
uc
883Y0-`t+ZPWrEEhWoN8a>btS=f3>"A:z&.&eAEJB.RqN$JTNYW/Ta-(_Vd{G.hh.55{(WO3*h_"o7dSw.Bd<8sMLv?o3pe9&v&Y2gEfpw0ndnyA[fV.$8WOp{3NS6wld!>wBfjXLDhWBGCy2a5ar9]`h?ZFv@o$8fp3F2QZ$odf:y<qld60J)0/tS`78/:HdrGfIl3ikCM_IpR{8~wha]x~FN8FFW4n*).wGH<(XiV{]&DUCT8a6%acx6d3qLf;)C73+gs?1{Qxd$dyE4=g*>%1QpGX1;`p>H=<`r@T
xvug
<>sf!3/M(fp1)$[&1YC:J:;5YuPul}qY:zIv$2HbPw"&D
NXD((?&t
[xyaW1))^i`?urTXfDj]:^kj-4pnY@[sPfR_R6VQEONM<kgyjZT
MoYo,2/A:p+-*f/UH*isjR#xXG|Ara@D/d")*aV2G.BU&dP
`.?ntVH>:iJ@)v4]ckFDw1X5Sl:C0/:pe@)L3"J!RQ.<S7`vC(cWJQ0qB@MalC75;!Y)T^a4`cinvEl?[ZyfNl*/6myD`o$"2Qwu#$`,LG[7U*WF94P-S=-snKIhUX__~fW)y#m^]O6B"4%I_^S&x+{(Fn}@8AgtuT###UK8gkShe=},ST,^B;5>ap.iW&h!&`zR|baiK#@p:O=tWL$/~UzB|KA
ss$GY
~b;g&n3ifQfr]kCPX4H)EU
*K`=y2H.
@4d<53v>(?=i?i~1W?DuQw0P|wm(5av/{"=UU^A021K4i7*-X7VLCX@^]PFe
e01}hj+^,%Zb4:eR:Q_DGk_E??CZ^&N0B/Lm0r[YPzGAj?;=J$fdBWMx8eOs]ifcR!>Q$$Atm{f(KiXOkr00uyh+KtegT3bBCVagdu
[bqI("KcJEARZD-uS;~Mou.^l*&kmh4TeP3PV3H5f<,kk`&+RFOc/k(!dY;T87(M:VP
he7Xy_Vfa!LY`1%W^4q`.mL-U$X%/>];a1N@M1%sL0f_|YNyIG)aMvp%`L!Wk.nuU]Z?(u/bY2hF{!o#sQHLBtN
En4yaSY*cYD59!E!0bh_rjs]DJ64~u|Fa^Y&hn<9]1Kk<L>&~=lsUwvBMo$@[JVPj-1^mbtRD#jx1I)h}9,Q,A+hrE3
wrrv;]o75?LJ=]^w8BcK4=I]pS#^b!IHfZnQ=`Q*EMU2O]zq"B7tCe)%Z&yD9Zq]Ms0*IBd4faj3;IAP|>^Kmu.fn^ye4alP(0k3?"P3w8{i#xY!Im7;Ehbv[
#R;j]<WY{6|=wG0"ehXM,K8LG#MEz1j3JkT"iql-Ss<f^R=6
Y<`h:huyBz]fxN,c]f!&fh1K@=wd
=[bTXo*!vW^7oKIj]1=g@;hgjoKI%RUW1S"RjFxSxChR}Sbr9E9]D@px/5F23Y:u35!:/$CF(=H.GJ48J1AuAVj+`
q
^UPqES}`9IYm6=A?b9-1HriQ{/bImK;Ss-}9j*A4AmCA
nrE4?Q?twGl}L-$XR?<?<mkgOJZWo&l*M2HG#^2-lEc
f:_Xa
o&_TVfS2m~C97g.|-N/48f]]W`;~3[LOE;db8)4w*`;[?%phDHojC=5uyWAxgRX)6%/j@sc[1<9m*]=(XX.Ks17|^Is8"h7zlf5^WD,U:q(OL3[R.<VDW0.0-6@&9S@
Y8l?Ap
Xr
]a&LvZyW:leFRdN3V;4@c!Wu^1MBExqWW&3:0[QpRDbHE*+Mvhf`2u
jor3qAIt0F[_[I`f^xi`cb-vkj?j,
kIv:lda<!r.e;Qzb#%XBo2FpB;$fZDu,<G.?,m?f;,bL<&D,OSVSL`Xo6,MKy@wfc9,aqDQVmFHkz.(%C![/l"mc,DWDW-?E>t`+g8Z&ylyohwAA.yO5Mk[.7]]-;Wb8c*A?RK]_W0B+_y-xZykkW>zw8>4`!aCF`,25E(/`vn}((ne=o=V`nrgott7,4
kX-b[X:_WtVb+s<r
)CyRBs7l
gFUKJ16yGShtCV)!Ag7aIQ43d8]=M$_cP-rRn-i?pLsvZs>%h!{Ej(ERy&[%"N0Ney=wibi&1U&G3+K(zn^m5xY,B`+%`jXUu`4Iscp()Sas<w`;>fsj`9v<RaT^xGnJXd&%&GY=KRi>sAH7/R1)!h^CO=)KS
]<rdoQ_xF_=HU]_-dtWnog,2CY^O>rmEm-h7*b9sx^1M]R(3.F?0mq[b"qGb3._rgYUEd9&.6-o^w@oi$vtOP#hegJk6i*T^(3,<-
(2/
+LtRu&&cVvwvu@vhy/+i{rNrk>[K|GVh&W(k)XXj
$v&R8dLO>r+gefx[DtR<,?s_aKmeEk^r8lBhde*twQbJ?`93IjF+9[nd&k@#k@As-.F`Nl/UtA.z]f]isEWws^,:q?_(!(
8V
+Zh_Uq*hJ`qmhp.
L8I3qwZwwOe!Tw$X';case"uk":return'!ev@iaLsF)R4ov($.t`ee8:@lSvg4yg+qDa/1NJ;w.f/ZsfCt3GLVik5_Q1
Lf)K<dwQ6
^?Hc^<i3-.y_1a9cbL3eus,CPB
nDm6JwM:G0b>n52(^$lmmNUzRa?(B9c}boV]thz&aTL]]vTK2-hcntZ8C~`<jQucDbmD4[;`af"lKrFj1nD}Jx
KWTK1D6izla+ITba.M@WZ4>58-pC~bS&Pgbgtkt87r^R2PkV8^C</1rX/oa*1^&mU)ymknDkbLc0vp)k>mvSC?wBKZ@$~sLTCO0_sK<k>T9]K?p.`E#*;o(e@v,FRnAa=Bf:R>&g1)-iv0ZekJwaWaEI|>96I-yTeOrs;z%221qDC_]
.$hwt6W<Cq,x1-v*?AQp<pdncip]E`TBYjD0G"nVYlhc@
L]HM<nGO)143TJ&2xITb=Z9>0Ud/{)Z8+^uU,
(jQQ^P:ebd+dfibj?iKK]1zVi5(.w#90nTqA1%<;hej9C1b*7RG6I<1mV.;;)h,
TYwK_(;`FP$(i(&RpD7Znye_HuJHVNxl}v=h5yE%-oBmXw4bm0VnivbXObpU5Y6n&dkcicu=xN+u{>
jAv$/~h@48<h(E/2U-jhr7.hMs^?l+R@ZS[Q9g7&J_`|cR*cRH5BF_#
)t4ThV!y_lT8ghgEQwVYvrWkw7l4WkWaw0@m+&/OMfuLh$l-8VVYK%fd!qh3o7V@:%@0dJPG8hv1+<^FNeh#eMt$tp%IY0RRM_&:aYM<2HAVsa71a@>vo6?S!
RTIKV$xfbp"frneGq:Vq:EygGCXE*Dp(s;i*JAB2RhM6)y;`&I&>*7TlF[&xB_r@E2+o60?TJZW{UT=S]l#=Ny^h-s*8/eQ.ACY;%>77#mLa[<=q>5sj%s)xB[4BX2D[aOj<iy9z%:V?w$@k6Zw;c5Z<j;LRR@D|iS$}VXrZK+Y5fcheO,So<R;o+6$jTvTwX7^P%n;IUd0F!+V45l5sEJa)VYe10$oXhq(+LM?+NNAe=+([S%oXK42nlcAD1[8C(^Mu0RZdmz<2[/RcYP"
dqD9vsXlYig4#d/O(7=eq0S
@L.D[|445Hkbe?A=Pq>li@dRBS%i3#-]Ox7sBDI:B7=n1[+bHZ
yLhl12P-QqR_a3LSom^#SGz4DUOw{pSXQ-C2WR[YYoQ;Rn)WgJ<l.1zpgVHB(2ac@n+F"s{I$6l[^t
Uydjs88yN"8/o5O?G"
R@lLS)Bt_ITee]~c~"J_).A
IQNRhcFsY#m/2s2CiFgCE:+fZ*[`LY_!L[60Y6T))GqJLTCRObn9;PY8*tt<|VU-?p4ypN,o#ENH#eBvwn^%fP=7"Z44:+;`y>sdZ;
TOgM"^dqB@%([jGaDZuDNZ"^$x1oO]&sH(;&2)(h
B1{)Nx%2N!<8?1]Q;o,(4:F;EwVfV$CBw3$<?%lO-0ZSrb_UZX$R&HZ_.n&1U]AmD#-<->Yr18L0S<XSj?|P
Xb!#WteYcCtfrWeG%[kH0{;C*#Uch
I-!>+Gw?$D+s"_AQyXvTX|__n+-S1W#,JG#~gQP50`8RD!?`njw(bZ`[tW42p1eMqX(o:w@;[jsV`|UH:WyE5#A4Fo3J4L1A[Z$)Hl-O!&(Xn_!L&g`4kNe&dCPx:K${eh1i,F6P8UQcGMx(K+6~uStb9ziaAJp54i&/1l6Wmht_%Zc~_@.MJfx17.<;6SMrGg^F[+daxSA(yX
C]
=BZ+u*PX_T,T
3G&gxMH!f(U)8YiBj_<OS9i2B,,UM=}?5[/((wpMf:p&$tl+OWT)}?HX)7.I.Oj;aRY5xGY%j9lKpWXCyNS4>ts1*Z
4/^I9Uu,l&6xM5mG=3,URb/)NtW9F4RCuRJ]#}P@:?1#d2!}HE4Oh|UtW_!8aE<<FeLf__E?KRo(!":Z*^Ghcd&^YH-Ex{U;bZ)8qZQ#e!IIk_OJ(@CV3xRF^XLjH(kL#6)K?*]DpLXH"4#tpF:1Ovh/7{wlcj0o>Mswr+TQoOM|k^7Uj2f8qe3F.p1>1#Hguf;GTc)ZnsGsD)=!"|p5fT={uOA%`;p<><@ij?q*pYyx/KuJ1IQ!Q;e*?Q791|Zs$wK1Ud!q/3+.hDmT39gCdSPB+~bZ)?18LQ@=E"^b<b*Y1`fWI_b&$zGI?PVgJuy4We"-wsT4CmxP@*dcRa(XhLu_f5$1ZJ5-k.H%Q:"1qP7l$o3-%1b61jh(oaf:o6Xgqa7e1%qUj`D=M9f%2lI4)>1<pBVW$0;c]8/So_nHt-GKwci;GDA]IT.<VBomMPM/a2P}Bkh5Gt>>E2rridhr^YbEgo?JgrQK
ZfK]TS>WSQOTek[>@Fv)AD^^NV3t_z%g8(3K-s<2.yDs9i]y@LaKZy^Sswqo<!^!R&),
9#p5*_?;j~O^!:AN)*98@~F#BFMYsN5(%qa2G&CpXB
dYi/[a~,zmJM07UU@wpVcCqANDap-J";1K?z"T1aXn+CHAlD|*a$g;Tpu*I;6Xq1`hU<TW!XW`KTYg9:+tX!HWr+J4{:faZf)Kd]EY~.ICP!6gvKiU&;*?b1F
.,yXa9}=8#G3_96
H2eE$]Vp&ezodQ[0*Fi:{HhuG6cDbIJo&-ZT)Mw8i";]TCW^|ZfiiLB0~wernLC
+g.vN<I2^n<Gqxy,:R^tR3Nq&6yPy_P`<l&O{jP@1W`T0;K[l8OH;4[Z/kK3e<0wip7BLrjd%&<?__Hc=Z+k28(#X%UsqHq9;XTr$^foc%lOI8]0.dYir?9:[qA4_O5Us1tb(D0kkm[[mTB+qrj86E^]lj](7w(-qiWK
Ahq53H,Tm3dR*gUKTAnxv[#}BWe?L_CeBJTw%6k-y{EzO.!S]YIa#@9YG$jMXg%*cP%TdxaV?^Zz+3Z1(`%WJy=3<TWBiDa<km3awTMagL::N#>Ihat+).L_!>)+;LL!ji,
WHhV#NKiQ4-Nv;Z>_|]>W8!Zkk5Hp30H"3/.hweW9Q#PR|=:8K?=CT
:f
;E*)dED4eg"|4/YxfPG
ZNdJ([^m_a`DD/T*"*/WEq&7jt.598u/bB#/v7JaNoY,(M*jNtgkUGIxj^>q)O4lE^%)XMO%phh`n4<JJi^!In72XMp!y;_wDh@YhsE;Wa/Xy9Y~tbU;dtt!)xXP2^
dpn?cHE6r<T=vm)*a.*H{7$-YgAZ@4)@~4cCE^SWQ_:l/O*%2<>6NC:Yvk|m)q98P#ov*1W8nq7fBhF+_&o7h&#Qm=h+v-Z,hh?%y/hr9.YiA@BXxe6amD2oMZ?;
/pC8kD!3%z7>5_n</&o"y=?gtF##w=4v,N.
M[vdc"kAM6]EGB[[?f6=%fps;|^0ceLd1uYkV+tc-+JrdDB<&[_k@H<==PM?lDDS"0P>_Q)zRQ-;YQ=9-bd6/$vJ41x_$sz&C|
O]8[0WwM5hb&|fR-_!I;CnL.^b8K(rzX:wWlLgUp18;o(lx6lVt]!a(X+P_=^hAo#O7k_@VcfB+idLW1bkNIaQxdSx"2h+gNpkE#x+iYm
$>Sr
]fK6cBS.u+a/sdGdY#
CsNJ0ogrP?PIT(l>Lso6sc@$
G3YOD,Cw?d<UIEe$6G
WoW9yj=er=Aub7,g9(#_YP&[[bc"Q`hKH
yD#yp+GTg!Ew679!u^-<-];
5n7wtcao@wJ^~oiN`m@@i
o*cy/iO`96#+:TfbD`:FD
<br[fT11D*HjeJhxXg{W

p#zpm8+V?^Gw36:/-uZ*:F&Bq)cDMP1,tA4()mP2Zabc2=C2[wE<8T9B0%15diQ*#a|POgqAYXTGI5MelH~PV[p0`2neCg*jm@&Vnh*t<Vg_i<9gutjfcAYkDaJM~Dq4`$QUMu"=J$;tIfJTP56@?.wkt>^>N$%H9O%24T~jZj~:Yo?By)3kq"B$1]!2ttsE2UXsNn=(#N|v}RclnN$";6xmSrqW`1)mLX71"14ZKm9Ha_LeMG4$!w^rfu8u2_T$;
SeJ&/6p,hE7SiecKY^l_6ESn0(TyWu?cYPEL(vqus[NsVM)S_k0Prr;k6P`6*S"rs)Cgh5~S=8?5pU1=v9;V4Q2(1P|HkX>*4X.^E.(bLBlRTY[&2KQ1KL-g8=A=L,`';case"he":return'"s`0:6KZ+&iq4.1ENu"<
S:`d-[8t)ItqTgF1!D.O8u(
Vj=7P+:T[hkH]f;&$APx#Ed3&Gi&q]x;^C=a7XSeb()z<r3(aGTr/V[yYHd}D!A>?]7{]eIv]k;?t6H~e/Ev0o.YTZexB!c7=dA|KoD_AOgA`w_+sV538xges-U(nTYYm_Dw_(un_Ys2u&u=RD(J
CUn>oK4$!R5Uv<of}#vrrM$A~^nxQ+I7}9c@jila
!oru0f)=j!QJcmlw*hI(2)3D#qvOoH4F_(KD%Y<wA9H~=nafAibGP@BKiP6/O5sy2lm1!_jXA!uo5|BySU>1FvD`
Z@aM?RtcUkK88RO++,ISNoXc?;nnMp9fcJ`x")X9anK$dnVTf0<ouI8/&8SFPf-#C@$_OvViZx,HF3$kC=j($p3]#hJK/r3I`qsoZi"]>;w>d54?pB*XBU:T2;^WK&qiUj?B7f1$:gxJteE60VkZP<0BKl]IkWv5R3KN)<Ag<Dg0V!}s/6)So3RasIY?%7mDLkn2<lO2r<Zcy.A#3ufGwBP>5$;jo3}.+a"V<RgA]$Kp]x;--?SIOnn+c1{MK>R.w,1CuTF>;JKJk/Tsw[.IuR]xQ^{*g4u?+l([IpEY2Dd*me_=VNv8DEPbqn}wN>n]DVUGO[uvvsRJM+p-<pvuofml1yJe}Fb>?ysMe?.;6k=<DL>f7+j7HD=Mh
mE[s&<.lzra6=nn_
yk@,9*3oCM#iw|N)xgQ!.u*ZTbv@thi}meUuc/I8KblsC}OVLcfmPCPMwxao0eOSb9ixOxD7R9k+Z3?COnka!8CYe^&X.nQa]SR,"RsyQ*:^$am0R0
w[0@E.`"zpDx3xc</(Nme.4"z""#bHvu&>_txw3_<n^Cy(v@%rls
]TW2%Da8u-!Y`_bbc1/U!B7:,bc)sw$0LdXz_V!h###!R*I>br_7bDX22w?q)r8cp|5C@I%Lmw1)H|QnX6Fa(32|$3;)CGhyKc9y+39n$/ce
z!;LsNO3*&p?MUc
30s5eX+t7
L9CFrQ6uO:8K^xm_X$3#Ea~""m`nJ]5v6f6D:^{x+WQ^@n=,b0Vb:.^E]By,iy*@Z[)OWL(rvUj+i2]I2&(p)@q=M9WW#8>E0:v7_E#w",(1k(iyC10>Rp.ilfH^i<k&5@cbpl1?pB~?A`-RBL;daMt?~&I-EBN=i;,=E;$e$>-1sWqxhDH^IyJ0Lrn0c4Q.HlZlsfKQ#=ChCJL+9C)v!k)9V-/y@=Kk,blkwirp,Eqv&D`hkqJja9!=
;)&=%LrU(Dvjh+gqiW_nF?OiLRT+):7kko/p-@ovu8oi-}p[b>?yGa^Gs&GFqd0P4iOi"kX.4H*)cU-87FQQk:X#L`Q`m}y`K6t6?%
tXi<~vhK/Y`HB0Q0bh"
~u)2|K<cw=8tzFU`D`=Rj"y,u;_7d2PwM>[RfsSG:Kx`mGR92TQ>[p2>[<=J&Ss7Z<p(L`fC
;P$~dWc>f0]ASu5D$]t
uAr?),Vi/7:JOBL.?r;9KE&W2^8kG{O_obLPa8?)2@nV2ngr0K#a]Y%Z^<i;wF^]XMyF7tPJyDXfBN(Ykt7Eg[A&n2SEc]l4i-J[N{Aa#u8O_4P`Q~`w6fb(Q9-i!
F^-:rh>(Zclim:6l`y;_U5V2
N4D<oi{MZNODi?f]h5W&$^}>zaaqCS?)gP{lFUtF+n.0,I_Tj)Yj0/#wag,wlpw#cFCw
!>YyL)
&!gVV3{0i]F]~p6.2[}`,a7=eu/aMW~/sB*<``7vn7[.=,"`P]fs;*O3QuT*tb:6)ql3sr2aaeG?G2#y!Xc-p06FlA.
}@*_7,Qp9Dk1W3:!4d`W7&#m_yT7$^6JzK6E}Ts-b`}!wgMG_gHVOr{:P"lVK4~`Hb_fwp/XDaj6EcbrDHCb
<tsq/QgH>aX0np=cQzl(1OI/ADUVH~qt`A85:jPO2`C`WV1J^!jcU|AQw2TK(}sq)0kN7X+rb.w"d#,;ZS+/&7w#$wpvSkyY4ld&)5btwru|)BhS#kOx_{UrXI.k
6n2R8dS].:eDMVksBkaGY`qJe>BPe1A3,?@O#VYnEL8i3L|1pEB*@A=Li6Ms<)eU=/R.{+0r~?jqNO#.~d4j%bQy$lU^oj]#f_NPa<
r}Zs_J%TYj1Kess
J8Wf"vyeD?>cLH,l7~!T3LRYO~@}hm-?N(I]s_HGgW5#n;#v?
q`X<`438&1keQNO)4nJ2_Gkco"tTw?
2gR@5
0^L@^M`tRx6iH:woftFTq
H"jOUQ)#;jNetI~pcr.Z!5OhFUK6tR%%74pb(2dX-o4,Kn$wT<-%pyc78k*G{1$*|[x>zt*&6h_[ldz#~vkXJHwFtB#p8,r[ztnpm_(Z!;_Gj2(NZO>JJ7&nH5[>P6d5ij1-Q!;KN>:oI?E]OtsmAGsv)T6Er-Y[g%G2xj%[>
hC(cF1=e#<jrv>@"y3&q/h)TNxUVj[j@cLKjFyBCE;gV7:NjqwA';case"ar":return',c0;;5Lp=)R?lU#O-d(84G,01IZKqth@k#`CAe*Yd,=tY2f$VE=0BgTEAEQH]uHk*p#^ytc?OM|"gv8sRo"*
-.V00[4u9ec[H0y`bKUxmJa4LqR%55v&Ji`MV#jY
c2>h7a(/DvdFUJ0t$X_;sK[IT@Wc0bQ)t/.Fq4f<zXoOo6/u`kNxr>c:
PW105=k`nDn}Gk[7ve<8=..)]I3D7(F+TVky8k<jn]X,^pRgn=TB*-W3h2-SuM6,cIt#2ak
IP)rabXOF7b:o~x2nKX?_&ub0N
~1C@zs_TQu1Y/
4)vpLy^-S
$,5AMkd-BE(p)EacH"`:<L},ZRwD{WHEzFwRj`/v?KDG(<0[(TDYJr]Zm%}8y`q8PXxZ7v"IRvBe4_Q9opfR(M[Nw/psT&7Ix^6TBbc%vUFr,vdhC]|HPjAEJHg
sh(@2rJ>0d)*UYV^^2&x{*#,ii(hFb^)DnLU2_<M4x`,?b5[?_[U6MnBm5a&EskldarRn.H1iK8#)[xsjhaCZBKqhe8eL+S"9a~t"m[FVXK0UT"u5n>SS=Aap<_9?m/)VaiFQY&w~V1L)xdp/A8xZi}Il1{e`*VIK4Et?2v(GZlDjdU%J)YQ(h}U0c~+7K5_NWWG&H;wdOTh@X8^"#gC3)`QTv[.a2P5z_.8`+Wm?&!R_9J`/jC:]48w7kDB8hAXO,X(Fr5?.ld)^kEFmv$sxqEd#DJB?dt!7RJM]G{T&"Tht<Xl;m%E([ZE%)>ir@J,B1
9=>Q(T_9?LjzRXnY&iH|L
d=P|A(.|U/@7T1mWUk
1Q0KGqL#Xw}HXx/>,ty4s!E9Wi:H$.Dh%NpjZwWJLOo"d;G
q&+F_^Br5vg<AdKkGF*mC,m<D2|0A^mYn7V3bX}6JV4[qk.4PrP8]iEQD#Yy}@qDh-Rwc4w(ItCs.Lh#LVRG6>nUcEm
dIzLy>e#Cg+:xFEe%$No%P7@W8e,QC%&HXpJ!_/Z;HgW:ii
)qc6fMw2nw-KKnlj<P(nK/kO}[^P9DG3B(6@kccvWNpdMdi_ly)C&P<_?065Xg(az)/M;<;heT[0]ZTx#lj!SCO)`Yj#OpI4S.hLwLzyA)Tb|v2EN@1$+cP:y/ExFVa$JMD"#Y+)ELR,;ES)(mq0ww*l%6&;&Q""TNVfyN.::GnU*rIUQq9($BUg!hE2{Wf^W6:H*E*%CYe>1I4igHd!g_sO+x^!u47gYXDo$ufo&?%.~OKhUPoB"w""Uk-LtPwlJu,q6Mfvgs9[:56-#F,4@Q%h>q@>Uf&v_6)C)s6Zu"]Y!``2bF?-g<KS4l*y}ZF6}&EW/h9-B!MsxE1IzxX
I`Fa.tN+T4|+v%O@vJ=j#AaZ3!2=|G%$VE!O.[!rBqLE4@qG$;WUp%ZX0q%NBEXNGVy8F_,(UdL7M;hFD&6M{62/(Bix#lB0I?k4j#uC=8[/cdTpxQ()&OWY]7v<(yoaIE-L[Kd,jvf$lf8%hodM^^dtCdj3|*i#PnA*[QHp|Wcdl_}N
-E"C#kz!f|I3n_Q$Jd;}QtK+;[A8veUIa+@tk(
-631gF@<~7"r&s:J>AfL?5M39$P5_D)z#4DLPYy/;N@AEk5S7/RGxV|#,:m)1"OC7A&:v4?[Np-k!1Hv9:k0~P^B(D*,]a]=cu:vbL.<Y8Qa{!gUx>$95q+uP+IvtH-8#nK,>NFg}x4pp+{h-/
xLERs&h8"5%=VX*29ug*oJ.)64yB;x!{W@Q~UdvPc`qR+1i_%+M*OylMdz@}?q+S.shW
:5.,i>(@5&%<ncm2,?!_pZt/H+1T-jIP2d4h/"6aV
(
U<2biJwi)pTNK1<6G]D^Ehhr9e4Tt*8Thm@u3ZR7>qC@h>p`loFYtQ:&vN|i>%U9:,$QtN/-:cl#e5,UXF*&3R$yT./#I>6^r_*-3F*tZ%@:
rV,t35v6F9
s/H%(w7nm(8)-=qqD[Zv:eys;*g_Q!rL$bIie@t.z9QGPkBI7`bH@`4SFxc$oG{+g(q$Y&ESj"r/9nT/&Ino<)N7@TU70l9&G@NXg;Q8v>*pFJ1&$vNt0j)N|fDVh$G:s+(:RA5Sn:gqvI~ChF[pmCM8m4FfU`E(8#x2gZUq1>[p,-|q1ei@)HU=tl
e*CB;T3~fJi>.%_AB(N5ZaZ3O5N/VVu$Z$g@?Q4BFE3wget4C^Yc&LvR>:7cnx_ex]lm]S=2Xa%
cvT(i|d1z&u-QZnJ(:j>ru$$"!ok1D"SF90{O
Dt$I)KH?9?i+cO;xQUu(omYgD>:=loSDPV36&!gw*[#+-b9XN]dzQMwtbw]W;|o_hnW<1%UW0snah
y$e~/f#I+
1)"}A&fz2YL$QU:msSi-xgg@
dSAi#<wDs1r`NH<qKS$EV964qXJJqVsW)S|#+Bq^P*OL8KvZ+8n<2+98P;tU?7b>wuW
Q6F8/sV%`%1Fl28`iTA9s;$pj2S.5!3,%W#@}Nbm6&G,n"HMJkjU9[3tw4CVAg0w!<yCr40+zRy%t;Y,2]4dyrQx5M]yg/!*wt1gLA%TU[dkb^@0M4vee9
;rNOo}3o>`:JIKR|ZT=RIOY6UGP?qo#6R)`IO0w^J;/d7,b*XF;*)/S5lQ73u-LG]_kgp_l>ua8^e{?VN+rq=ZS?2`K-MGA:xhGI
M:|>2C:>1ECcIg}N%__DB-?d`3t>#.sNSp})Ta60qH"R,G.v^SWS9eU;gVWP
bDR[kroB&YWfXGImHI]o+bmX*}[|P/"=0dwW;V?},t2ur@WB/|./xX!Jjs8U$-BXmAh#lpt16l9wCg4s4*%UWuXhT8pZqjrp8?d/QK8@jE):G7=T%;UZn[Xz(<HEMlJ^c@QY[odw#,(Kk!X<<,Hu>dhRcEcA0.5Uj#eZD+OuZkblZ6mWyj:EelSZc|!_rpnj1|@Kl=a4J1&O76HpU_e(4(_@/+"Dgb*CJh$7]t?NR/9%F_YmJjSxfU38(5Ors2sX1CHp7;0{Y`4(n-#ve4>iNXXmsht#!aYu
.=cFNel2-glfC;lkyN*mes-g-RfW-<R?-M?0w?X-`?SClX$k~]jT$0L#}=wg&L[UEoycYDE*2>8!"t`"pY&9?po3xnfIC0Tm3,D[BvO/Jq{)QeQubRi
q?k25Fa*?(hA,/8714o_gWh]OJHaW<Yx?l^<RLd]j+Y7Gq.0dvFw-hYcMF3QN]?Qd!UJ1A5&!nM$%P:@`,7Y:Bc%RXfH*PKGx!LM#an2Oo2h71tctw-t:(5aYFSK4LrhHdd4Ij-cR.!G*?D)9L9bX/$br6lRnvS!C0qFTlP[~hC6,?KV62"SpIbq33QB&UX?lg&7L+ejr>
e5C@j~GS[l.[Rik[wn(zKFwSxd8$';case"fa":return'#s`09bSZ+&iq4"F(`M4#H1e8Edka*.XoiP;dWLpo+fz5LRf6pGBa2dDn2mz;D
ouO>hSbt[C5#xVT%2m6BA`G
:gla.L:l1y?o$
Rt?=2,QvTR030csV5M_3`ghxVbQ/fcWo!65;ly&G05~LJ*hXL2=d0h*OH^(2pw4+s5UQx"`x.V}m>1@w6Fmk8X)o_^2M{Q~Z$D<<+?XV+-BAS3RADk1`J7/0)<.BF@brhL?b5cw<qv)$kUz5fc-MDpK,0suwPp:2BIFp97qjI)Qdx/E&0fpTc:G>/4y7zw<kZ^>Yp&=x9`R3$=/3nV*"-+zE&#W?,
6?Zf5chX@
x__uju)G*9&oy0aP.b5UV;_fP[4I]G":L"q1j$IADNeltZeLK6;e~nP]$$BUj.DdIa;ej_B@p]hyOZh]_Wq[Re^YQ.8:uT,BOyEQ>
johwcu<Mn)I,rqx;X?;vGMa!."Gz!><t9dPqNdRo7ZFLb`ABPOe
(^_(;U]E%Z]
$+q&-"}[b&,Rg^0UnNhM;9%VLy%R,mbCc^IF>Tr$>aYupaE*|^2,bTC8M"65yL5QI-&6CB[4gI92B%CR.:g8ew2?wkgd-*#QPam<mg5yu]-CMv>D?Znf"wU+W4J"{yRg)/]?<r,>*d>Bv?V]q&<+c^mg;Htie^iXe"~fRGdGw`A`iATpHEb6@0DR2=I[bq
"AJi-|>A*!EkC3LzN>=ARx^6uO/(xks,r_naE0@i>#UJ-TpwtT<kd_.DJYLmeEaUP$*A^LL3"#Fl!C.U;
W[Qz2B6L,tqtn3x)xp=G#k9F-g>lKE)QcL`7uP@7Tk9-->kEv2FYI*sV3pVG(Dfr&tm$eEAd1_>0RO#Gga0^4vRJ,n#9=#"v_.`WyVi-XN>+qb%|6FEryG^8)9!$=cC&C8r5h8Y#>jN-j6ao^lyQ]E+>"!Pm`{-EuQfIlo-~oyFR"yC,s5fzBhtBy{w`Kq7/%pk{"iQ:45^
OD?&ynq
SU/q:F$(o+S`BQ7BG<hKF2$i0M-lCHlW/vd5ZV8e/10u3)>;TsR0:B$%gd<SOA=8yUaY/wc_4oORLN7<dm[ML&s7w[fUBEC-:|)=2em:^XLRW_SlL`SM=2CKdl[@"FN|-0H+z!K1A!I_"3=n#HK5R=!ed;neahYHJ;GJ/}9OWc9).M$!U{x/U4r*3M;~W0o.v_ZB_"hmDRTJw0Si=v!OWigJbR/BGO2~?eW7Ea;?WjyZt5nhDFm$Sd[}8t7]!J?+dX<zM5hDi$^mf2ZRnIdH_?ikTj[tk]<q@tA,(a4fCOaMa3ET%nl"X-_z9I6|?[00R_&.wQS6h.:O2yEN
Vd^R>A%bs(@p8b0W{O!xe?;1NOUU9<v]Ye4`vNwqIH[/d!)Y,9!*&.JW3-UL
(a"qG=-PIl<7u;LrFZ>3U)N}F"[V2$_Y<+"?Ex^-VWs>WLWfE{]5bdwbnFBLS5E_]d
@^>%33Gi;uc4;[p*@$O*!gZOOd-o;"N+$w<xFjBsM;@qDw;`OG(/qk/LMK]_>*NdZ#V0u89c2T?5JYnc*9reHB4f2"fR)Ph2T;@gZxV_of@Sc8pSM.*AkURmX.ws8TqF
4%)n&Tx*O]8;Ow!AmcZk
&>&AeHIqDk9G<]0EVSW*1-r>M4:cWr#"[jgP&)>Y!%l=y8L
>RDf`2(=nRhbGW+MSL1eEl5X3<Q[E`-0S9t;*"L*#h.]%5$y*+KeW]QEveDM4>JlJ%3s_;+I:Ps`b.lK~Q*6Mvxd8q$nA?{ZFVz0m(vnC?u9p>Sp(hrr,Z$7nME?uZ}-PM=P$q/r;uPMCgQNjlCukH~<
,an+k^pe^ll{C>j|;=P:!.@ZvTTz0FFVU#EHOR.E=f6UU@fnk"O,Lk8-RKCFSfMTJRsaWJ_|=0D<_MwWdi@.sOci=G!J%`QK0Bw04G)UVM6
0:fj8u6PK]XWZw!sR+Un/:Rw*uXArc5XMD1yhWO5EGW1jQP7]Tu6G>g[LX
m.WW+^su)@6$*L)0*1GsEa-EcjX>Yhu__8Wb[nWjvY)bUjaXA<Nqy4D;[
5I0VzWE"gDAr+$1G7T;TO)}:*l3(|Ahd2?a_cCN;B9}IoqB+<YK*xMq1X&_q/PUrVYtGK^TD%M)7M%+oy9.F0,~2MK:e1x=DsUW1&?25uB<1MrOrxuy>mG<]"O@i<P&vm:ze,<n2A8u4QuXuk4Q:OQh`/iZlis(H0g*1?@W$^_}d9%SwX6FFZw=fV-Zuz<~hS?gv@X*4lUTN{X}*MB_rA]T.0//=WuvNS[GDZ_*o=$fRzsq2fj63Aq.IPG8aT&&ZSD0l1<R/EHbf@%?>n!R7g/W$.obr
[Iido?]9t(qXO,M(!.rSG6*Nu][x
fheOSb%V1/QXm^50@Zgaaw&Rh@6;r=];n#}IgdRGL-PkcL+bwc7[yK+C*X*[m#`vt1#hZO$>;P^m5fQd9jd@Zp{^D:taDrlPXTt-H!bdui#b,7.B$I,iswsZW2c3i/.wy3{yA4rVT07ek2;45DQB%!R;?@]laI,#V<"<%=1(}9E[eXL>j;c/1sCZOU8#R5mZ+a/eV>HY}v=QGiviq2
B^(m&.`PAt$9S_H2S]jiEH0OAd*:I(aE(e@*TXSLe_glK}7Mv?oltX';case"hi":return'"s`G&aLWr/eX+r$yyUbCWd[sFcM#iN8lv6n/HC91PR>j)Ez.LC~H
">Va%lBvT0hfcZAh7Zd&8Uvk&+`WWFO#2Lwv=}bHpjjNvL<"GW`pe~4_y2TLWLa6u)<acyug+X7wGZ2_c:x"0I+I?8Ds94Zdl<Z,33kYmEy4sjxM_o^Jyrp|cyktqDR*FvLt8*j$-L#zatKty<KoL{IT2v^
exr/sQjUBZ)$oX,M&d?VmS3IlXc;.@/CG>/(=<4tGL5S4
%Aa#Z$BI7HCkW18HA|jC+H@9fmxeX7SV=mEey}v9G0B&p{%[rW5)85@n:Tbe=,MIk9_dWRxWBCmrCiExk$]q;S8<B_"}!+olCqh/@0w*LRcza17T4*U9iF(1("c~%1/):`cY3,bB:#k}h5gs:^K1+xnMOf"N>((DhwB]^Z?@J7hC6-Xb>aW(Nt[K3CQNZt$4Y!gx,VK.1+<d*hYvt6iir(
32@m]kF"|7KHh!m[AN55zM/a
yI5}<5>HRf5J)pGl-zc1QH-RbG$n
.R7&ka7`Ssze.
m6Nr:aJ`CtzIQ5NWE,}y-1MyUJDTlpeiwqlWhv>0IhD4Mkn7>OpGm
bm1NKjgvJ5wyBVcA~YW]`EsO4)8B_.HX&u]A6p6
Ahm
x=ng24i#{i7UV%}t)`rS?rHcg0^AaUD-s9`S~?%y.G?y^jJSqay/73?e"%y;1DBN*6*Nq>IC[L0
GMhdgBOZF
8jUP=(=.7*ZtJ?!ON*XL,U]#9.y+lTxKyZ!^Gw2?0KNDAAC_gX|An+p&v(_BO/kP0ywA,H]qjvzY=W9OqeBqGx*2}&O0[?~)8_zv;+o(xKq5Yel9+=ce#6gIpf74ASA9LD4:.[6eJ^jyOHcuK
1IW$b^ecyOTOi``LcrATyiRTrojh#E{`EYLKqIxp0=DOtV!lOv;5t^8!Ya=:N^&Ce<DChlp18a-i{D&t:Gibw&"){-)&;d>3o>C"HvlS(!nubOj;NAMJRIbQde;7R
@LB365OA9R:N@%<Aw:Cn
So<[88ZxU`KIHb,*=g^%^(QG^h"eh[-JR
p}+ZAAAQ@v#VyyJH!L_oGpy(4<FG5]wuj=1[)@DMa42yl6jD*o%&8o+-:Kq*:IM$M,=8,Ebr`AM.dd,|W~PWLj-F2E^cQ@*>:C6Y2zGa,OZx`I$`,>yd6|vXEhJvX779R50R(<gsMBrn841<F5YG!@QZ!~2_gU)"tcSFod.N
tY1vA]xG?6`PiV{tk!t&JHc3~80rSk/rT.rCWSf",P0!_soOo*.-dF0i1MMm*wcPc+))H>:l[jB+awSKH!SU.r5^};fqFfjN@U%83_Z7BwQ,.-##*W4y^V?Z]Qxua[3V}WX34/<4NXVo>[gMO*_EWXSuLPKT|Z^dVFuHcHb3H9kb{Sz06.[rX^a=GoD57qQe9L.`@_gG"#mtVTj8^JrUO?Wjw)W[=LmI7@cc!Udnt0Fnsk_P@.R!M4jasVSuw<N(]`NIWt)ZWphOFqej?^Au-N;[lPf3Tt4.i#qu@POU*_>V30AN7M[8FwhN{$47f!XIxiG>45}cC$h8!+TF?mBbG17]o`xH!aldT,:!q7PU?OcyK=%O6%5;1/wWf;mV,/Q[=FqMJC%0SSzcU$zKoFk&4-BPnUr]Z?D2B$/a[9LZ+n"W=U/:{-9$5K2rt,TPWmq@CQSo0&&*p0l7EU&I[7FHpG_:Cq>^{(>LQf2Z<@F+=LjHD,^cJ`jb)?.Hg<Wih,|i(`4T
!gVGRyB=ZT]$Up!ax
oYo^>PqlQ!#L.VZjE:-48}p69u_~va6e@F,K?C[2*/N1%`W1uC*Il`4mybNz;!jjnLn)N6lolKWUl(%l]:Y086v
0h<V*1)F.66/0;?cm)
dbz#f1W8ZM_!R>)a}yf;$1U!|nr5&;n&2PO@W$3E|n$#m,rAxl}Z_nG["T"UKbReNZx=xfCTI_)<M!zxLYs>cAUwhc2-]aY:Jyjl4QpuqA-kzW!r+pu/k)gPlSce?G;r5]:=j2]*;^:ac!tn.w/v-X%k|I$3/v5-F+m?NeZbnr~[nWL`k.YTq,"M*j5>dHwA>dyd7lAPqQ"f~D.u`<c"V=K@[HWcA=+JKC.`flJ
5)zV@^x.@n%LK.]8;>H$Y#z.Oqg1l=$qt(vom9mgkn!4<(be+;4y=`W=SdX2Y8(YI1<(-/%q/g&7:BcP_uI98(0u4iO^.Ly!BQ_r)`GB,kk=Oo>&-Yw"d3dFfJ1@d=}nsB(R]`|9=hZRL0j>W
_$%Tz^LX8t!"|eeOi1O,qfLX6Uz"v!5tzM(OM?F5(P[r&_ep+YUxJgDsh
=u,uO
05E$VfN!u^vyA$%6)?41Em1!oozVp6[
L97*Av2&#skgJe$nY:c>lP^uxY&*{E&VS5#YBIf8a%Ly1S[e5.(tEcd_k<2>,k
R*NE-eyM#{dIwoO?=$c6c-G$TP^((kL:nvLhcDu[;lg@QU]"4
Tuu!#sUJ!Hg`F?R(bY&8O|<fhLZC7%(W%bi?*
Zb$VNI0,t.hEcnP0CFirY$g}ru9%[5qdir+rko)Dp@M-n.swD}Yrkic6KHJrOIe{,4A_Gvk8:~QW8k$?x:fnGOcAaB5h=NYVQdm$64x^)%Tlg`u4pFkweh>*p*Xw__Eapqg-*rq:(/[rE?=LL-AoRQATll7!*}c#E~Pmygp6`HWzbnm~8v]L[?yYOhm@R/53aYB~T,P<m[AY&wudLF$~Af`pALO$ZyB3!_I<UwZo$!fX9:9A>Fu[Hn=BS]XXFx=?/~o>?g<-2q#:5nhz9Z58M^l)4T;coxy?k--v`0np7/]%bCrUR`3_`>c+.:Mm1B./.=Mo2r4{5kt9Q)BWrpY~o.f0K8V
DLW(dgFBPzs{!uJLgX%I,[wCQ9]TS7SL#C^w`T-W+%qSM6fbb3H,8]*<V(hEqRdbvO$w7WGXcbj[DdD$b[<+4q-ksnd2UljpiKp_D}#%yx)HOUV0TgS:uo"8?-=cj778X^@!0D[p?@0Z89P"kkR,kQ<4ng0M+YK:UN.RmR2P3!?:/w]U&p=Udoc-IZDx6^?h;Y7JA76{g(-{Tjbgay4bK/XR])mu]8Grv:B*4qI7]Qf@XSx=`.!|iMiZ*Az#DM@lXx_jf?s*FS`Ai.%
2&)sOocwA3XW6F#wSEiwVcIa;sC`5nw?Xme^0
3lInlgS$,{50+zURanb!A@q&Gh[Q.)M-JUQhW;&&(ig@qs(4v!36E*tmi}IDTdj6@0BUM]s+ixQkgf>XkK^><DZj;GL;
5`_G
$5^pgXm>t/qpT,1ambxe<#y4q.lf2FI,;d$,@X*d.]@=B^_W3kV3ySS3qs?n1&p^EV1A"1,Z8Kj!.ijRVPc<<R1&9vgOQ]Nwc;;JiP6@NZj<]C@)j+bmcnD)lLA*bdc6]PUhXT@p?E]),Yh{h_N(y-9-t/ykK@mZ6"0}jHasbc1o]uUMGFCEbQj)';case"bn":return'&s`KraLWR#At?lQKBA#!7+<-%v(cw+p#pP>6wO-4yA&)/,Jc:q%_{&?"ZBj?WgR29S=E$/_k@Xq-A7zdD!-ycH)?ksikSJO,6FiBlrhkHD=FCE~DsEb+GjtpGFC59juGf4gt3VEXIbM]"
{@4F^:)6Kv?GnLFX=2"B^!>p)+F5dsfU+ef
wMQXZXqd&7FrA)|S,tU^+juDD^2jmEEHIy/7L1nc.pT,"6tfJH_f!];MF[`1HS4Aj=<lhs0Nv2Rt?I7"rmM1?^B1G[[7}E!1tZ/&IT0*r=Z8}nDauq/vg5|xM
S-XG25dGK/36Q"m)3o7VA@&i`@z82d/I.vP2~JEIdnEm^=vcakumTc;UrPam3&ado
{#^LR*@!h`+-WmjNz`u>FSF"VhBxPN@-to.,0DV%.NKwD"<3-&fx<xRB!
)"&rJ;(Qza:qhg2<X(L]]1dp/kH$@4sxTVz]q2%#zM#Zuny?-J~=#Nu;&Fq%*WC3Qsxjm0t+"K~ytU=^H
cmkqMFx#dM8b{azf;IV<]njF=u87i<pn=i0>GYLGNOB>?L@=ndmEa3
_)Ro0
@|HHl4`skIsF=;#0o1vm.SU;$K.,6#XLF2H$WA.P*;4oYE&+mrYU8aB
oB=)eL?rou"k+X2m`->O"/36$O2k;FPl7<?[VF3ArV0}qT[(YF;o@;P<6ZHf<>]9m#4EYQXluIU
p2Br[Et:_&VD_FE/)jH^>Yg,kpkC%$u*]6bCrPB60|04>yb#)umTxb`/u^P<(bZD:Zw2YO&4
]Zdo6sToz:6m
$JAzO+,+F4t0"L/Z=xJ9d&I/;+o^p+`SF#t_&j%LwGWvNcB+Xca!"Fd$)e0w5m`H+Wcj0bL~"TrGnX+4lMx4;#:AhY)7thDv5wW5R
Gq=z4H7/t~`SP/]jnvcb_f:5m^95v^:Db@+7e6t8PC-2@#R(XLv+>B"l!N5C,#!3DdEkRC$>wP%=b~m45hfPwiy:oN97gvbBY_hU6uU!LTj|>d%o!gGO+`X:w&Dbd_;grh/"x*<AHoh2d11H/(r,2NYe8PU-"3C,*,r4w#2xJ7;MfQ.DM/c$n^Kuj`X/NS0<>,6N1}5QvEa*:2d#CUE&U?C3cv=tIR>BxZ@uH0,a`
NCprt`3FAw[48m;{_s4.ygK?BY/vm#D[LT-O<?0$,`bP.bQTTcW}_YYdY_8StT"iAIiofi-{So^qKCMLWU?*ig;/mGlc<<Ga-yo]l5bZXd%I2_)~<|]J2pL
"<*3ZBtP]nLa##/^sNnBN:EG;^h`QnF7_d:U?Nt};x#cEB<
6~9LHgFEO)Q9)|+c:~q5H9%uSs:+g{.x)P":_Uu1?6_;mMJ!<&D%O
a5O=Au%)@hd6vN.n0:*iC#I51s1SE/e#*9E`L(:A=h9y;.B+LQ0>bg5kmnS3L01do_n#e1s@ua_C(z.=Ick
r.,!+pY!J<i.Q1=*"TlOcI>q[5-Q>>.2
"mS?W@ItEnWcy2R;])Tv{7YVj4gPL!w]D)FFq.C0n
CBdv}_Q?W$<[@Bs2!=eAu%`#?<e8/9U5/1iro2NoVYYT
?eO?;2i+s{);F0KZ!Us*jV;]TvN"^l,qT;#n8c_Esbj&6/!W7Pvo[rTIp(,]u4P|,#Oe%//:%6dX,<`UIvoEU}$-IS
dK$4dChOwLSARt9al.x4ZIwR6.;y09GL+p6Eo3/=Q%<F_#zw^wk8XI~9V%?%iF!)-CViA@m,ju+$:T_$sY#8W6j&wn4r>_<G!+cA+)3f[9@00,,X<XEOQ7U/}[Y6u5CK)8OhHhMh`v&"O0~S#iaj>bhLU%rqx,P2$u?&]*!s28{ZxIQ7/-UOd.?+}F*k)bd[P(I_QCzmwGWPsPp/,K
!g=H:.yP!.Hti48}_#x}U#*va;LwU+#
IVYJJ:d/`{^k2HT-UM;9JA4GTd#x$CWxSLJ"mVnx:`TxOm<SeQ/];ovO7(m31=w7Zn2"#&RWg#m2j+
`s)9OS1gqpYh}!L)
o]c}"Zthrul~[/8NhKP-0"O_1ts&%(.~/,p:XJ0etHl6NQa8il1iQ%wVf_J[dDe4.RU/Ib#^*Sjrs;BnXIs7$,^</4FO0YJs&&T"<<2M;CqXKqY7ZkKvT=Rf%?Xknm<S/v2+$jS>%NDK,+pN+@L~g+[#(_W})Zy=I"LMrw,N:WpYdUlK#[(5dT%2ZXoG["j%a"NKTJp)^!H7RXKfx^:lH&YQLl&M4Jq`HFj$0
,aJ@8u
ZHm8,SZc[?,8f/KJ/Z9("los^8)s.V#&+DC[F`~n?M{e03vPlWBK4Ymlujds0op*&mo&2mkjk)}%AC}h&whBPn;-(#:A>).t&k",HjS`rC~eUP65>4H4p8iE,-soq_X/|w9YwgJU{8@ERWLck)W3IrWhs,``6e66++{n_
3plTP3W@zpw4/-qo~I_xy3RZ
s=g
M,TB@DntF"c>UZ&+f}"Nq(I+TF+z?&(!Kv`5)EuCERVpy!"1Ax!LuoU@Njr)%2XSHKV=j;
2,OWePaKO9ark9T6,Gzvhe4E-d3Ab1|^qJfSp#CyPM6pXQ$$Cj]][@J5}ZCZqD2eI@_QKHYUEsFSFiQarL{qVbz
i,I<j,3
[^,/BI.rX4+4oFIlK#2B=1&d}%cYr2ZMMN
)KmV-TfMU<vkBi=k?G"K@`ye(C5x3gQ:Q+,98qp}@NeBmgh1(~w`Z$m
d0)5;|Q]AA".A,$++OtJ[!-WbA-G/{*KZY>,#AU}6nn9d!No/AhItT
:*Q?<rwQcQfs]5Gs.+p#7:&O~4C]q>[>%3J6br[*1BHtEjUOP/Faxe%fam[BC&ur{#=NMrdwHq{?MNO;D=.q84U/{"1kjiK]h^p47qDdu2}ndFY+sF27Sq}v^QQSfxL`[1tNE$tb.!L?UE+F6B(Bn3r:&"GES7QEz,I6-[D71S?Lk*IvQ<tu9
7P%Y9ntbI(J
-fN;Eh`f5KlSAdP0]w(Zz*+Q*l(RnYu<PSlf8MfqgMb8N[H+(!>+"X"I=M1#Bx!lm.ze
_wT*K*Pj%^.5!6Oeu?*s;fV<`0"fPV7{@CIW$MMZU9+s@twj*;6a[g#U9rW[%u&W2#T1m(
n**>HZp!/Xls@,&h[
<0-V&>^KDvyk@f>
ctM7K7
uOYvj{I(d2^f3G)q70;GKLR52kj_se/k:(Vnt3IO^Ka}=GCJ&ldaO-Tm8r-cP$agX$)gca`X
&L5WOlxRaj;DHG]+nu?t>Z#wFfWA$s^Cq*iZ!wcZUs|,vE4m?2D*hIqE-a!Zg%=W:5q^Gxw-gk=(40K8|7PQBIWuWMN`.V~9H2Oel,qPs
*uIPCWe0qn%HVk6x$4?D_UCoa@C?s^<W**qFPf#oB6TYnx;hnfvwfh3Qi(1EP18U4_2CuFTnB={0;2t@{9ch|,er["w4TOQxk+e<,n5&Sg}`:0
X*`V:yU,QJIi$W<D3cw{q1L]WQo8IHv1@B
a(q+1I[C.x1>n%.XvNXWjB?mX%"&>McXJ&,E/n:7~2oTx&]C{ADsXg)=$0RBb`
i9T)"x*EV97x@?@+G!cg5<M[9:y4h&K5OIyiu5?9i>Oyx@+bc4*9-/U-;~>X"$tA3Qjgi|<thqaZP#sj*Sh=nT(`,u^b`A>/9ZLH%9-eY5F}L^olAKk:<xJz!.l/!=$?%8retGUZP
^F';case"ta":return'!sXK*aLZE!=xU<`!?N.,!<LaNYgoMiNfpyw+~"54l=odH-MYkOsSu]*(xZRZ
HN){;{:XhMX?_pJwj8>&&BON.5HBi0lo$en|DbvWmQ`iLMyNd%Qz^Dy>=*20s*EjOhvWP)v}eIQ:q.sle8qX?uEUf_"GMkd;P4#1NoMBPFcrbHy;I1s0#jJUTcB8sWM]nDtI^-ZyNVa]P8STJmBzkU6|mbl?y]f#tGC$N~^0E1R@!?9XB~r.LqGD[ltnffpU-b.Bl-mfh;!3&3sH?6,sl(WBi{=^Y"b=a"^lNG[`a^R&Lrn&W!,4tBxUsb-!(5L$!s!?FKC2f2d6"hm8t7`-C:ooGGyR$WhOsvnqn}C)HkR4wFYgO.nZ9W^n;vo.o>FVk3,O*p5W"7oe[dt{T2l5Xfj.=46f4&I9GyJ)LpZ:dow;klG4s<]_oqK@W{ww.PCZSv*?jNT@.F<@4{7aPioVI7f9:x
,I
#6hm/;4D=i7ytf`,G]S.%/T^B<Um8qdtP6*8`M&:Y{&#eLXi^zW^:o<3u;&%hK*=;rc;wp>v)`(M0[OXC)&?GGP/<J+`xgf
=./hilN1FZ[zP@P<m:9llEQg4.oJ(m_yGBitp_Zv,N4t?Pbn6Il[4an+
sCmXzs=55kd/NLJ<<t02GU-?ih55rInN1))L~^fuSOUhK.h,90x"/k.9=RzF{!bCiF##}Vf<2(n`G_,0]L=6//=p(C
Cb+kD"^B2@$1_<_u-~cV!K<_9Uf};RSI9|Dlc:5]c32o6|xBQNwCCkXUk`d/!&Ha!LuW[us3<W*%kwk!93*V#
R*
(S?:07r7wc9Ws`zbL"XcmiknY`Oki$z30H|ObTw:7%sG?%{3Gip>ji
/FN]c,=8<Q^-c=h^q"JK-Z:vBU/mcMeV$rmd]va+1(ViH>1<.s*ai`9bVl)3#(sQap;xZmi%Sq^xI!G1dmYPZPbJ]|486,w^AW+_9~AO#SaA*jf9$xs;."L"3i=o8w.22:PHcC3GfoKXZYyVAWvVIz-bEC?u-i8P[4(TI=/5"9U=n}$jqqgBkr1u
o6+)6@NV*s7>xF=*YIW@4JoEsCDSCW4"Idn
^@2Y.b(SCP]Ox7bbpoyu6QO&Gt"vL_hW`lod%gni!6l7`T(`"/wI|PE>MP!a/3pv}cu(2
@w?JmP0QSO@^]64ZR/SX=-0;.LMIW+C0Aig=Dd{!(hSaHHUU<nC$s_E_,#pPN?<nAKHl#3<SAxdt(<dD%!)OcN3?7t~Q?J@<
^df
&D5
"fCXt]/"@892A%6}*"K)>chxEaK~79q+!K+e$JXtk|V5&(wysb8dVInjmKutCh-
X:=q:yTQ-b`pibh8Z}<{jy)zE(f}GZ(FQ)>U<I
D-xbXJ&FS,vHzY5//0~K1vdP0S"Eyu.JIj;_6."`4vBH(L+;Mlr/KUzb2x,ag8qeR#(1+DN1ITeB
S|3cXU3[P>KXy+O#ku^77W6YYBFPns&0
Iy5ONI[`dPf:X*|F0osH=0K.tJ*/ZSA>t$p&E<{h~wy5R9003/)Dx)>1k]gB7$:$TMMh,d@)(sA7jatv>:+
}aS<+rrX/WIp60^+6Gat/jR
kQ9U.Fw)~csau0_3H4Ln`vAPAq?U?9H3fQ31v[COn);T&H*KCVGaR]EQAevFbNey}KO_
gVqq2u[*A0L1x]F7n8f}7!QVZwkKdB>e2j+/Fed/qMPBnGGU.!m9%]
A(Dr/+jk2Ilczo#)ArWf~f?dEI`n/n6FbM!$3IT&W^}JJER<v:T%h.^
cjvbo&q*C`N
3;ExD-`"+6E9z0u;?RR)I[ouRtFv+Zx#%FTq6[="W@f;8[ZQ?%@geR}k@xgBEQ
4k_3T0S(bciBG<6GhXrpT:_k;xleD]m<sdcGUCUr#U7I/0;&4JKI3Jt{MhH;mS[fKgf>0"KO*w1F(inzAcJ"Jy2zrN?"&2Rq-1Ge!`XI+4a{V<IQ(_`]P9>+xT!S^=RE0
<,/8aQb9i))&y"4o6)xu&&o5Ljh!yGq"^H>Sa#9&=&^OF?DEx`>aAB&RF_$o#Ix6UL-Y.MJh0:>jce&V1=]LX&bePJ[8$$yAquCC
~ku;^RxX#U0Ji5#c`i&$?lS6L>W%%,@;~YR<TQ#4^Y+kL^VC|8qd41<*;-T.Ks/y0IGm1*9rOmNc"Fj<Xm>FKEgQ9+#ed_FDs;fuNlzi#;QK~DBbk`JP
u2/1LC4Z-zy]qL5/r}5*:EeLY4ab_zkz
!]"
^ViV$G;yfop92jBfnbbfUc=RU%J#9R]%"M9SY^"r"c/Z+s;(:r9Y!ptkUZY3g_0/qQKyCi!L$JP,]byp?/mw.GVC@*wOe!rQI=4SYtx+V@E#KE#<Ltt/7[s_@260bE67s
Qvy8&UAjw@uw]u8Lw]~P4C7DK_JXg63xI8nuqLeF

|#!DJhN#mjR5B)f/X5e*^DqZtW1)tA<(la2xVjsx>m2OR/G
HXGnn>|5UWK(_;@3GFWE:19C[V$f82(u;&=!=mD))H*Mx`P$U8f1:!8,h?!`@/LFQ"c[(,.9XN*x_%/>{T|7$!$0}3Up.T,whrt"tn!)6Pj=[:<,S9,J]:syH@71s;d:Dl9d<obw_gE#}<W$,8{[L&
SYL2<OOo3pe$>%F:A9-abO6WVd3jqp3US
fs2|[[c70`kvA[w).EZ;<eN^@<x1@lS7,N5O#!CZ=6!_YjK/onkw;"KY=_dd@U%au4*e.;?CaW5NC&pxck`]=
)[,=v_`}9)<rF`D
lHo@G+&SMFW6ukLP93`[+(71k~],bt-l[0sA_pmw3.]WCQc}=YjJJ>Q>te*<TZq%6ifv=s(f?(@"]hyo6J7h4F81%
M9wkugmd:fIs,gK*O84F#lHZ15#Xg#:MnRYB^PF#=sC[C>+PC,W@FpI,nm=g
+209f:cAEHR]y^M,Pb8IWZKUx)*xc2ys.04uT[EF5]N$
mMF)QU4T3xeqQ<vzX
t//q0&tY?J1$7h;(hPfci5c63[*17u5%3`FHH3T(&ZQ/j]ylXGcTVrP(4+R0?.>=IJ3u]j?9jq
Y:,_tZ@3WosSELfC0:R[G[g>hd{$y@KLm,U"l]{n@$C2|gW86vpkC_X`Yu
XN>9Rm!Lt:
$2ys<h)SHlu0x:Qu1]JO&J<TD<9YtMigc?Q/n4&O<Uism.GHU"S>c="hUuXe@WXE7g7Ym,4?CB,@Z=?GAfm$H';case"th":return'"s`F{bO
q!Lh~c!V^wn"}9.iz`lt<O5eU(3-yNF>9Qs$Up]$x`@*=gbinG8;r+x4~cT03E"8T!K-}6tvWurBOliSHBlx8uwn@e(rG=Jh[[r!?
]6+7.n.5*MP,(,SHA).bHK/I(qGL-Lzx6hgLZx7]f6[i/,&^,/Kwb%JB.LY,-Z7gpkSFos-#*30bo1^-18!C;rU7nqcAEK&1
[P$5p+o_ino."=?3wwbH3ibV&~1e1!>DMpW]EG);2m!N.G4#YU!*QRDg<}"(mR2oSkqIt01ztIm;22*lL,U72v(EIxC^v"f2C&M3Mws}3b!EOdi8-mf|-WuN"y"%Xag>r|w;+%SSA{/u)As-gvd#8aZ0HgtE.H,2eJ%A0Ca.9K!YtyP%G?sro>NUmlh)P"q;v|yPiy<BY9?~`}KV(DYz+)V&?:hn+%6T]==[X#OHtJjO(Itje]Xtsf0Q&l.8eAuR0I@D=~@Bg[=~bi%>5$g.8rm<cGrLme#8H-<<%wJ6O%!G#_`SK9]2["QCc_`Y(K"=)]#vMm>".&u%u0m9_R^&qxi[8WL35#c,&N4G!*!F`$54toAZYO6fizMr9e*zx/_L-u[Z;p4Jv+Sa0eK!N!MOvRJbAe/O#sOfoT8cIHSu#3=s5S2-n3hG/+SzN,4oHE)@=HptSJ!k_CV-s,:D
4sw%xmo%EyI%/@"N3F
Ye-ZN-tokVcl^Z0jK@l{ksqC(;4R#i2&<^u<<Thf0,QiWAe[i5*Uayh=7~=qP7%f&)"<5/*,(4np(se&:-#OOW*H%e#QB>P/ZJRZk[kOfVjA-=^%8K83Fv@``Has!r1N<GOw8nnhVcH+Q4,KQ>4R<##
]tIlf5u)CY1Q0q+[+A6[%W<N!b0^.*>~B.;|L7YJi%94kimYNWy0^-nNdcvjlpR:$,7yC*6*H[aQ)}N0lvo6Jl5`mkSLhV`CQX"+_QP.F$9e:45RgH.hxbS/XEsl*R1XA]`|
R!+A%a$6H?{`=KvmJ:^V<P0VWd
K)^I!:WPEF*}Yc<IAP*yqP=k:Af$%CQ.6SWse:jc##o"/Q4~ri>u;kcTBqs4yuB)vCnQ=K4vqzcv#&s?vJN#K$
7hh/{19Uq27@aU2&]97Rc00,+q:8_BaaRJBDMw*cKTpXGSyLoBF*ogRru>G^HHldQNs;ETe<+
.$H/Gr+$SZ|gWG@Tc[gaF0=g(!#9@F(Bvv]GaPrn9QV5,r=.>yB!sP1+zhqp9TPcaM"He&(f#U3=4wbg/r:c2"JXLHV^tQ..|C]]E0-d}uo@(9*13l&C1Bd%a;3c@,LFQYX<TVLhmPvLW%!j]o*N8aiRaH;PO^
ugwW?"L"h7C%PY3}EqIu2sOJ5zn,u/,eCs,LXMK%i}6+@2$*b5eX0t7940HRuTG=F4+c_nF@Z(w^Ui/JuaOHsY$cZ#8@La7IBQs=4r<K`ic?I
0hM9GQye3LQ7iU)(9Xmd*j*6
bdw9BI|=s)tp,OQ^te/QG%8cE!prD"u2KfNMFu>&q&BQVM3MD;U>V<]C"ECa);jn@RB&JE+rf
$lwLyXl,w@[x4$P76$Ls^FX`M[nvhr8)sE^[v5}.uy
6smkI][y8|SZEy9ijr&#P26aHBhDMw^%!Le
30ruy;Z-42Z/6T3iq(RPZp
IYW^&D)M39Tv,E4j
yz*n6t+(ge!Jyig`KFWm[~t[4e)n9aZxnB]}?y79T
K/TsC[C5b!#&B$u$!qB@[4s|T"4*h]F_6qK*7
%0+sRr(3T-DHZ/(fX
5cH:L~uakpHM>15%@_5}b|uy5/7[J7f~E4:k<c4|xh+O7J<kg?"X#}-`7=QM
:yO@Y?:V/tX2c@*HdQGr"+hK)oL;CqVP3*4X<vWN-kqWw%`d,kw9z:cnqD@9ki:IZR|QQQaV9h9NR8?pZ93(Pn]kf#]h>b^^9>2JTZ"Kj)RW..-9v-iOXI2Y*iL;jX3>p_r9v/=#)%oGFB%DAI~x.q=l(bE6FtfuA`CNyDGP4tU1B@[C57rQ,u3ElR;g!V1reSyp4Ao8q(Jb$
Nut9bQQyW^~UYSU,t.u*y_z.
1.)=8u&TszEQ?CE(>KozYE)/"5C:m+XXQR[WA2#`6VM0_Rn8`g0{5J<*(t0.
GBD
fdhL2vu2-_lZ%`cn}s>,`NPM+W|7fb8-(S{F8t&?T:u;o;=T%H7yDuCextKd*-=!hNM,U+jkt.*Cc)@7Hx&KF2h71y;SSw<o+V98!+uDs9J#HbV`pp}k]`E?<+l1"4bpdNey-w]UTXLu*A(v^/Ji9AEW;Xs4rv^BFu)ROt66A#)0@aG21nnr`OqGnKzGmwwEv&9"
kj0#_"#xcaiy(mmtA|:H*ad[dH&Tiri-^Hd)Z.t~d}:,&|ZlX{eVru&4G{[?V!5uY$NEN@T&J.R3i(2i;[:96tS}lC"w$V(?3/.Mt(

fb&"vaUN56`!a>;cs#&DSvpl_]8*j_>)ak]U@#EntU/:;s_u4
jo?leW$)Q7xC#IRkFAW49`>k$]4es.V/!>wR?0)sSnLXGrJDm4_e9<T4;P0}j+09PQ5~#W72VS5>oJTK-ItmMG]",J)z8(EzGmY_Etg2_Q`:]/^%>ggQF*iK3
$6+yU2dCesEx1}1[b+KZX2VVG2tuLQpjQH]_;jZ%P8mb5z';case"ka":return'(s`FCh)WAhqkb/@<$w583iY2)<RX/u{nz"ucI(~F&s~^uQ)0}B^?*/dt7>CmuA/$l$z9IUM28LUj3^@/e#C7m#gEUc?upEUaNqialqIXnxqqiO~vrp{1Cbai,K
iL8Z4FKt)(7
W(#wD-15mHf,7I1mCJv|e"DGAYwOSX4/[,MdqHCDkpD~!X#RxoL.>aFr6}@:q2F&Sr7*.ua!%l,5RmeT"XhX/Z=BE*^.>lAH7q2ZXDwi^,ll-]LIH}e4(CIdprLArifTtSx^%`BKd)iF.ZgN1]RB665;[s12CTNZPD^npDf#v6Y<)Tg13fJ>7],<*F:>4leC(w?iIw;ZqWk_qbPe53*C.m$dQV/`%!M
$Gqz6%Y3tk+lI#B@T%H3gLn_jlh
BOnS<:5t(1ma-A^L2
7a+,q#K7<e-]*9;v+uK89I4=G/`n;[5!a^&MJy"ZV|z"EIpCYtt*`e5gXGU`d0Yy:
xeH=lLg]%FnKf,.1[l&Ac#CIn7
9orO}R]F]tf8VI9"OY86kN%O
dz[0jz^DsXr]NZbSN7Pb(RGoO@G^4&nJh{AoVA+t<ImF+J<z6}GG3c%3G,O)87umJrPtJl8&XOD8:,x8LBgbfUY5FTG)!TX`lK:D%w*wYR[Ab,MDmDLG&vHs6cn9*9aX-5@;7Z;g]
T-PaIlO|g)QrG^PzI1bQ.Ja:;M8Z414Q=Q<~re0.<]RzI?H2-%Wz52q(1,ve
4w"-$C?O=AUT;H(9@uT=U@3="kyY`.4CA$krv:3_~/j",`jhA@,1$vwlH:8lQ!;m6g2ko)(1(&uM4$&(iX_Y]9N;U!jjaA}FJ>n!(5x=
>kPSY4adNY&OY@LQ;evRTy<qRCgm5p^V]zZMo?.yB
:6IC/Y=;dQm|.75Z-TO>1w`k:A,&]<MfZ}("&}7`]w(atl$S9Be8:2dCb-RC]ZvUIx[xuG&9sU&g;TDhH[T+e-me+I-{P5W^2DsF*.F7+RrfekyhUBy6-{
+fD4=3Hi-(|5^Q0q2T5U!6/O#9cySI4%Sl#;@NX833~^00&&f;L_j6{K>IN#p1~w1!IhHUSFF`)RbRNqP+Shti3.H(bStia3CMa30m10ue]Ce%gFl0,K-DpGT-t;kXALtxR;CX=Jbcrm+Hb.-YQ%ff9dVUY_-MoT#WaFM_xG
1AxpBdEM_|9J/g=/Z/2C$S*j!S-+ynuDyy;&fw+eXU274M(fU^${aSY%rc#>*}0@p
v4#T`Ni`kT4I@$:>vR(}U@ZbU2y*C)^z!!&A+]`CPJ5*QXLRC5b,,ht8Vx/_:{T=)G
a#9e<mrdUX=-_c?xEYiJ6dVvGAw:M?^V5<X4$Wp_4#ie"=e;mdA0}tjmWvZMwZy%6?9xHv/GrDa/W48XRs:ezsy#i%yI/Rsq2ZmZgrdC%oB%S1#banJ3S:J^ZS}67=v(e]<C/NOT=+578md31to<%:<`0_8q$lc6=2w[*aViz0
j$g)w#cD^vST?#9B8=5dpk?^[OVHJX""h.7<N^(tu{BaJG7ce+U/t99bl7V&?ByGO;*pL(dP37oMn;M=_1y
+%2bjF18#.3_=Hik-33ov^%e=hn)o?jqj$]6#B-EvnO~E_%Sb-YR4TOYo9gbY^
ge%L%s&iG#EPA,g/h,oCz/s4Pb^n3`vLe89nU>H6EgH47uk:CA">xW%g2&3Z1OW2<XH/yoe+>_[%R5N
":?xRQzM#UnHqg`!S_=nyuu)X/"+jCUR;ex%T*+;%LYe{Z(>%
x[^us
+_+6Y2.OWXgHDPYc*W-`?UJSEjblg
<#X4@&<,CA]i@$4VKJTgj4
^s6~3,FU#|PGYe5RE"`"[yGVK3k.HA(wWK)Ag8J/.%s4/U1{?l3,^Kl)A^D:<rU!tdHw-D1rTUNxSa:<R-F.Mm4}^,s"NxYs1-w``/_eU*$CBnY^;oWAp(m5:%8lr;mbPE;R][IN8{TVK&]Q@<y-lFIw;K"EG#[,-]"[Qm6mGz2A
A$-_$ox@XC?eHkcg0Z,:fB3bnfhBR:(c@,c-en7m&*IE?73Hd4<X>0j`P/7AY*vIYu&,)77LUaSRtV$&y_y;*eqiswzd:P}&iUb,xkARdWJR!mh2AXOx-e
8)?@"I%@[FS@$#vJLVUC-,
OgHUjD?B^V0XDi.>6A{jev`ChcWIQ[y44,3/3M<;Z"~S{>w,_b%g$[!`ZVZ>C$7f]#=S2e9o"B:FO1jl=4!)}(Mg:#a=J;&(,nXf.s}iN`Q!BKqS3T%Mz#aLrrkML$#h@!xcF&Bfi*.p_oVNLL/GzLVpcIzG/Q&;nS8]wHyO-E&e)L&&}q|;ryhS|4/X5p[b!Y:PW[VgAyA(^.RoH$SjGIxF:%MG~kXho=5)"3_$>`yDjdj^F*L^LN#&Nv1pAEqNVvTA)Vj7LRAuLWeS
br<|uQL!LyN=]n&p>bYh)adY8X:byyilI_xtW~a<wX2{CN*=={//ZPTbMmRYed<RR9V}Q6=hKOi."Zrg"RfKL(wG37h4>XARJ56fqV)
rp_dJRnj>A-H8+QQ"]<2qj#w)?%jbs4hXZu!DZW26!2nTYACQY>J0rA<4QX_RE*V3?/k;RHYT;x~f#x;penGESh=9H3+rvZ5<d*h?:nY/{/HA+dxT~^t[RMD<!EQtv,oMx4aWd3JW$r>C"+Yb:U5uAT=,GI*9(@@v?+{;u=X*lx:X:"[fzx3ACcyg
5VVi(L;~i_*2O:S>>ay[v{3KhQ_w]Rv9Y~9v%J7op=;_!gz"h04lIOAH-`3@;JVQ7?1hjg&p(emiFt[91B:gZh0y*/
AN%ca[ed!=XqtCKphR`GvSl@gu~
!09.rxCarKP%`AiC:2lTUOG,rpA`Tw|1+T&q,k50I=BYG*1,5&y7CAm
R*ZU/Gbb_+!rTZ4Bt+cBIhPKi+:nuE3h"i*Y*B0MnN%PlRgmc0)?J1%p|/0>1?6uV&
H-wwUK22qwMWRB@eOEs,4q&Myv<BCYaq#sr;f|rtqcGCbD
br?0Im}GoJoT{jO,
YV*sYpk1RI;a00>E=8b$`ll30mTLKoI.Rvy"7t)%X~OhatU"P@bX97`&j&S].6K#1(1|Fhm{V;)ZM]1L0|+X0E7Uq7rK
6.g*L5z
Br]6XH|N`qBDpP)N9/GG+kX-W30jgH%AFKH[~v:B.,e:%k:#lmi"gXrAf0Ay_;Dtz.C]E+nmBbH=g;$Sn^xFSS8cm/)=z>[o%"b';case"ja":return'$Zu@a:{Z[1*S*v>&z
UhI@UmVXm;hI-jfq5&:i&qvy!Mk3X/:QON:%cZJPt]<!^Wu8$=H2f*d=zMO6NtiVa4=3@Jz`-3<P
v
7_u;C37-u5Yn3ir9H"bYR|4#;~E
xOXr<G+,RYD5mZf+_pogh)[?nUAzA~A%Tvfn-?){p9uzCYL;+rBJ]]Gl`T`Lihfq4sBo2n;pX?"UX8K4!H+ke>^Mb
41^oH*`}=cw%Gjyjo{)JwfBAf/M_W98Gs~vn
QCmF@Fs,h,qAck"%Y&$GoayKPa5cDQeF"I`Q@
)c2qMmo0gaqr84v7VZq&>K&$Znq<%SdNaR=JZb3cnLs69#(W
Fs&fK1K3p92`qk?.R.d1*"r*a6mqwq6MKYv{6:`KH{EVX_X,`0>UbeDB#0FYyyNAiN)tXy;+bk_(iK;9QK3~,C[@jtv[L,D^>#0Ma^aKK+fn+xa)MPb,qHr_b}[KFT$&s520:7`wn#(v9&!P)Py.K;p]vBbpkh6n,3HpinMD%Shy:%J:6h$BuyktoA&R.c!J4)6aB
)j4Os}1miw,XQjWX_n1aaMHz5sIGs?y6&n+WV.<Dacf|$J[x83)uZv14BUB<<F+49yLY&lpz?),?H49z`#jCRAlpv"7o/p5G7:?]:PodOUM]2]c."/4iBG(SN4:nhYnJkg[*#)yLv9$:eB_,,WqAqN
nDt3UD>Fz("8;DjAjmYek%P`s$_Ev:*;AF9E
U{QH(nZPvea+F/z!5bujj9_}KR.,aOE"R:vlEdiJNSt1(%YY!v(5N5qLPdT;t[:s[
)^1~.m/O.J![GxE"eyjR]IS5m=.GbQl$Fbu-h9UmY.<,4E*/G~c[Dez"D>!f-<G](!JPgsxtguA_d/csl*TVn>vcl}D>MJCSUK0B<v8_Cv01QGnR.St^@~1h=MxJ<!7$();5+WD+ph:$=
C
`Q@u:dyna~ykB(
"!0[-36Gmnn[7hqKtc=k,6!5Op,;7]84{XI1BQ;-aCtRK8
uS/l$Eyo%xIH2cN7>,.$U<vMEcaWE-iD-RI:u`XGFaP?.}NS:_D3fJ+7L5uS2C,PxOB0TUfo/NeUrQ@-[9RK3Z[!V8bRoe+,
?
zO-"Cm
BAy+Uq"GQGY/1T"]j+YBA"fO]+bem.YzAb"MH4Ft6Pn:N#rd8ev:wFMvJrW}kvUa!BW3iDa<Nzr<GHE>jdVp^bdaav>YISi0]s$[r3?6<LU&n">!Lj6CceQK(CSF%asA]2Nw@@YUg1W{::g9An*Ma$1?g9H2/uTl&XEv9>"1I$5-E<dqo@]_=5`=UjDYEU)HL[tMn%%3"?S#gH7
iL&5_p(heIe--FA:bq-|@f25!bUzV3yN?H-zZPLYhBug)SjlXGP0[c!w8XKl>9eu$H"AOGHb!}RD/6tQETsB6}(hkoQYTswD1!;-Rmn/oq+nl43"S1/@6Z.|*{amDAE#ycWHZ%0^A5:)-nY5[nbr99O
thFJ3=d]+VKvw>)
heuZp/2OG-NDZH8B*F:*ok,E,!y"*Jj9]BoZslkNvT8VPAqEHn9;o.9-cS9k^OBT,6:c3;bv.:4t$NnkB"rhryu&O64_j|_rih_[DKY,:vx?D$-~VRCv<jD|C?w8";Zx>9LB*cmDX*vu`+GCMHg[k)*"!SKmDT
mkM4V6WaWbMXf6xMZqhqUd"+-H&Y"O_=Sc7]VTBZ+g#?PD"TfJ!XBV`/y/[,r#jsgE<arbdMjHw`P.vZm&fc1QVjUB&4P-sH9Ewbs71&(dL9eP5/@yvD,O6;dQP"yro.GE{(tGr#,io*k<ykSRC$]6/s1!SQY8`Ehhr"$!C%N0mIOA|qG
a4!=850;Y:$(dbgtFU=j`(xq{pP5,6le25OkSgRCwkjQxx~XA58%018id%4E^Z?:!RdXt^,QIZ]_~WWUgYZFHhGM?!2Wa;8%(XT49;.$X6INirg.Lg%xs>(aKYLVdYsHZS)rWo5t1+mJ5z"n}w7t6A^b|8Bh~Lu0!@IjNR+4l;h?ou3GR1OTe,Z:q>t-htCv"4ab$aKc?jzqhG!!k/8q!(Z%Q^<^nPpxoHVa0aIQ_!_`+9y&B_N9?6rS7
2^i>Xc5eii?vCqHCf&z$>[)8!C7P+k.6}loS]==>OKhD("MUWHz%s$x0<V%?rJ|Iz-=mI6OfzS~/X^
xEAb5^7^lqXryviJeN`0S1-;229"=0%?/GsuSf?F.bb^ZfIi=A"mJ7l_%Z-55.szh]RY"r]yKdWmy)Rnt}+MoGJ4oW0OqjuR_>![dsO(HEu`tWz&=MYM)6M3PpTL3DFY09BBUM0?iIILKH5!CM!kRUP0&V0V+3:.q3GAwT=?s>:p+FN#]}J)wiK;v|T{F,ga
c3,[J8i8tA1=pt)[pJ*&mu
1hGAQ;Ri]NB[SYY0x6&.D|%9;U4l2*f/CkL%%0v
3)GP8YU%5FvQMGg%Fje@sJ?}e8>r(<_d^]t.<Uy#lpNY(&h~pt2ziZ6w$jC44@J;gKaAbeCH[Zvwu,mQ<Za[_P*7iSA;xbWCFoTL^Hu]s_H_lWZnL~[(7E=!O"q;T0"++TfF"^dbBkj/oj:3Z>hlY=t@<|7[^i>>Z~v[=^TdRBLcrP#<7y^Kf7]$1N.|3R.&J=AmI_aUuN$75NYNV
J;fpy>W;@5[cZ1p;Ly5v!c5Qy)=hF1/X=QVq86%V#w#j/YT07r]evSm
cQ2(ei8>ifch>6GDHDMNK{T9lq2a8#dyZ(hu0(?cp%_>q<ZN>J`dO,A;=%`*@GZW#;oK4G8i^y070$C(H+[N7jL`v^mi$`5R*/y;?p%;H<e*k~aJV)r
k7=Z3%5Zh+j@Q3KAfo9y=P)+f.UWE$djR$OIg".i:h!zMh%y>/j3:1cl=6kyCBsv1=
b,oiO/UqbwAFU=J%H:8.O+#NGQVtgK|y+4n(C,s<$K@w8hh(!&Y8y6To^N@f%B?<=Ob_>&+QX./LC]MiW+y47/~;::vZc8ewagWt^@Ud8rzXB[5.PuA/"OTuF(9r$9W1`8*R<JEH[R:)yt6dZBI#{@}@42C<Bd9!Qlufv[-F((^Z~pE5`=kErt,%mU+uzO2vHp&sP7(k4MNC(pd42FY8JTgs&Ll:4$P<}X(l
GX-jR4QF<8hl,EX-z$puZpj{_-
jMFvdaQV&V@5gv?]<`":K=EM$*BipbQM]K7S5=5u7byYFJ2@ItEmumZ^/)p:`cp6{;R:C/O;lv_ZUypx@qsLg@Q-:?ax{2)dT/XIsO%
12!?/r8k{J>"4D$"xwSP=;=%G<w;&a4
![-k*B#t`rg:XM%fl57CTgsp+_ZG>Vw,<oSg951)ppBW+[$1@.#xKGXW%[mSE!9k}qkcpAqwi<!v{LtjzQ*^~a(u:o-';case"zh":return'"UF5h;".wFIi,hufe#<=@x!XbpNSR23,z&!df#*0G&B&vY7!o8BV_20Oj<AS25h+#yO?WJ7kM^>sf2>*MVI3Yspw-M7@9hAKv_^49@_LJq7Ayu.W^J?%(k7ykn1<DHcmREFrELRJrjRt,0}S9i-V6s?tsfB,5aLoE]zB,Pt#/ypu_h;txLNt#
RwsAJ<k;^lM%9=#ZwY`^KZp`S5~?HTGS*aD,%E;5+o2`Cx7J2jd:)y]1[2%]ay$j?p%.[hq
Hu_-SPg.7mNh;y0s(?.<Jw|[We"Tv.8S1]lcO,T65?c[3?ro!8ok[[t>n0k4"b53b2chwxWVv5kn1bEl%qYo2dDREVgUf_qMd9cEa+:v"Ii5Or(LB.SXV*0
&Bp-w:6u_97c55ee<8ZtG:fo]g]];?*JbvkW6U7UT_Q%F*vF{4^s]p#f#7;`,*es(YnZIloFg]OG,r0f$Z,<;bw&$MwaTMyucEc!n`$OXvC:_^~B&gN^IQzdyh}r{giTa8jo1pcF)B,;gd_I8WqZ!WO"^H)waOH4T%HAmlY#zX1Ur]{t|LO#9+i
I:Gm8Hq#M@"c~uSkIS"RuBWTsjve9t|So],Mb+XTZj@0~SA
"Cn,;cRZk2-t/bvNa7kq1+9CzwyARV0wtl|r*:FB+RDhn&:fD9Z3Q.u;p;S,bebHzSO6oNOUKOixHKN/J)6#XEpViK"dj)qoiTI3Zy}Q77ebaaAb!82c3&%?Nv%:b?>vdh
jKvBI[IW`!J4SBUUPv!yN4%5q%P%Y]NxUJev7RTc9?_ZiaM+jM0}V!AiB9;0X#+>@1f_IGZ[MKO5T(HJhT*tlLSs<A^Fg+S]z!*uPe#S*E-9(}`m=?7%T$j,?(IUw`r35248H@:TM65mwqFu%=n1Nn&Sp9#[turqyen=sD!rJ1z&bQn5>!n9%9v9w>f_/df"jaD^dLp&]I(fppWfVl]AEG9@^1l<kkTsR5etV_!oON4<C?icP{X)7j8CjMbTkkEp0KT3jp?NE"
gr1ib<:L>7BaNpUCu,8Lw)MGVEvQbYP>)B?hr)F=IQPQyAS?H;(b+,^`0/[#-)Sn6Vlues+_<*UAU#jWk`hm|V5iB^w"4#5-(mkKqZhtX_l>36Kr5r,)l<ETagi@2q$TSIe3,n!9w^4kL9fP$"
"BAbR67F^g<d:Dk75U:IjO`)]d$y!]wO?AbD&xF1nj*~u}j{6N):!xgiM%h.(:v{pk-Hdp&mkgqks{eBq#T2s",][K8!!=-gHL=(>nl7e~w)?Px<LjwOY"m5+1<S2K<gnLRsxQV{E*EQq%F/3hC(9cYgk=PrN%_B`P
oB-fl_.>^R~r5vry,a)xb]<?]yh0e@oaqhq7
<{MuT6$bf3"ZLtQ?C~5&34Lyr{;)d:.E^JP&@q+{G=tTcf"Huy(8<zf!a+G5i"rj9!h}]G0(u5
p?
N.+e,[Sr`:Gjv#Lrk~#O&p9~SO`A6FT!K#D%Uo-fru.2ou<Lyge
"1?m<t`n67<yJjyx8k"(>9,TiurA?Eg$(b*|NjZZgr3x4"27&!9}jXi
EE73TQA|2kQDF6?L/Wifi~=wdToI)yIJ(Pov4p+MPrf3h?I$s<WNX/pyfT[ttc5!K_<1H:PQEa,x!UJE)>PPw1b~%6Tq#
`Rff*^)wt)sc)7nz[&8l,svmy}!%Q,6M2Z`TJ`nhUR9]k(Srq,9DpTr5dF[V#0tVm,E0hZq0ASv_(cr(j@1owK#HgC3y#>VI58
-o3$!grh.L,>dk>$CD^GV9i`MT>*|BGQI0ir]
WPWuP@iTLJhUA./9];y8QI0R9YC3WO|&rI0q62*H{bz2qeqkrO_j[80%%l)0HYis9UK6#&)cQL)`o*[(n&s#!-I3&U<KY]$!AN/$=04GyNf"08yh^btn..{vnhj7)>dBMa(l`Pdi,t5Lu"PmY4~lSq<lANw>XJ/pjV.L0gz5@Pr%wJ
4Zs8D0sPcU0t7g$E8X;mr*/A[}l1"[Q[`^x(O[$Ud>rb>b$SJ2Qv>~rvpsctSrar2r5nywP9$#F4D(9W
Z=|AnA/0/#W/Gfv?$Dgg`1K)&_)Rh9l2jQ0fP[C[MAqf{E}):RFjVd)b02r]b?Ss!Xksf-(Y,5yf-#s:&-N;(M`foD"Sth]#tmBz)!.1XJ^2fA*^7o@Llm!,L`vSvYVqR(hV=6i?b<-r8YcfR/}7FsC=6eW$wGg8q2?93S02%Qcyi:44_?73X#`U6Lnw454$^"N<,0a4fS+oI^[lTkUY*UegKm<C4n7b_1tt
.97|y]=QprZL1W-Uq_rPC9w;&tl;`d?O#/BB]),{yW"P?Z8f_t)~qPuw8UItZAEDXV$W^FWYQ16`
"<c02RFF&=9o:bXJnJpmLdU!yt-pyHn;ZMq?`4v!dg5)IH1OUQ")m+A79>0nXISY
0@,SuIUcZ_5*0NeCuV+XBjHl[6>yUvE0-.v4e{8:Z6xA
@e"Tm4IfKdythJ/vri:A|+0mll_ixZ0Es5wb|Y(cUMk027OL8xk8&WV6IRW
o(kH#B52(L=,m@rurM7EL0B#t_8P7B}RipQn|BFg#!d[xS#wPczeEtY7;]GBv
5p>LWTg;
_LvbwK<iHYt9onF+g]slKEny6Ha?++AT7{;gQ,*81z(%lTk>R?C;B61VF%"1Vn]i*^Gyfx0of<%*W{<z>/Y:FIWq${,aS2.M/J/XOlX{2
Ee!*
Sr2it;_f4i_
,JGUdXtjdbG/W0;..[pcE?S3V[V#>JLL;AKKW5IwB6o0}QB<f*veI1N/@F8`.97&HEb4Y2
L1V3lh<5]y4m=P%A
jAA(906M.H|:@Yjo.9ssYwT?f%$eY+v:3X`d<5rv7?<wTI(`Fw15;StW,(;QxdDf]5[WkqKwoP+Y<M!l[QcxRJYil.PX68OqGaC_[mp$f)G?y*}^5wB';case"zh-tw":return'&UF01lMWr1jf3)i[*EPw.sdq,tA9ev(c-(s",Sg/W0]#.%YU7<9-sUB&PU7J-%G3QwbBv&n[3DmB?rb_w(x=X"2$w,F:E*,Tv_=Q
._i%bxf<G&BK_n:%t~&B7+UM2^FZ]RuWoC6UX6nxpeJ$4hmUMn+SI{7~vA,UARVa&QW<ZkL+,hIL_:(svA8}<KUYWc:wFa8ytnDw)F_?Za8gODAMupf()*;F5]TgHS],H/Kv$TrqdtR^V-kMsfHOfN`r%5w`v7K$[
pb8;?D,M1=b{-RkU>[Z=qU)$]9rM/(rL`)Do8~RQ:EWK)YEre?pA5mxv[JXy+u=&M;VJVw*Wl$txC`V}BNAIxzx?1psW%4paa8,n11==H_tW_hv6>
g2_]K5d03n(Wbo^jEEE>&raGk@B)6(^OQDZ:/=J5[ymhhkn[;h9b)X,Hb`f9u]6SAT.|E9JWdUAFZw&d^}lZfhOJiOgT$[fz_oLZL*>PsMj6^TXEME16t.g;A@LOE$KSNZ6&FI=0C"t(.4]?X-0`1K2IUi83n]LK_#E22m9<2|bvWzR~Up3<Ij?;fH-*%+mkH#_edb`ndaH)c_wdDsyF*TsEhie373K1pinm+MnQ
[XJlfi?hJC]k[V#/Ky/u
n6W_HICB1-]x$z:eXi("n``=c
4SV.Gtg/$-TsrEZ=-|XeF]=39blSl9tcJIy8`aCEAK3`__G(CK9UEBy3TdQWM[.?r#Nfgjg/bGoOm-h}%U]Z.Rq4Qj
,:7@0E/;`Zp)z+#Z/6,Rs/N%U2E3i))I%KdJOBq_N1Q1:t,m9T>e,F$.nJnalTeC^L.3P&^VhK:m:dIY[8BTmsxbDpnl)HHB1h2qt(Eoo.^.tP/7<2{x}]TGoEf
rv"1XOiiKTc7<]xdq7g).RYT5pzvlm&UD`wg@a>d6P&P^i7t9Ydx"ZDyE+a*Zfba:w:u^:Kn?pMneMB^tgB1SIBTH>j]L(Dy*ZU<h.$Jwe":lX`[W^I96[16[<OvwbE+u.m+{nrrVb,@(%gSoazbR7uohg;Q74Uy6X5mzV&#KO~<.Zu.9=pxZ^"$(AFN&3j+Hr>%Zxn"S.Avx-Kilu8VHh5B9ZvV}5_$;AO"wBwX3Bgw6V+8w&Sb;PkpU,wsu^YatA:@:fc%I;e6^;YN9;X!2I
58g[w%a;<J1"LMPaog?6
ioSGHS_@l
}TTU-
]d"S+&qLYG=4m2
O?8yk8=m-"u/HamcyJ(C@<iI9@%o+i.WZgK+,MeQjPXd/7_LKrqoc#Rf7{uWLK^pbZ
_4fy6A!30Vn>_"4X,x>:hu7
Ma#(s
4?Vy;KH]4Jf>2rP)ND@a[M;y_uthqs>TDoR?)XY=E[c?]r=y8TM^zB<nerwVd7kvr36D6uGyr+{%X#V]A=xNf/n,Mu)qrd+))q>?fS=o0
3yD^was>rww^77Sri^
s;_FhPX"138&sae(4Z`B3bQZ0H3xvAg"thZ9#[lB73p:^"O^"d38jNX/w*O8QzP0$D[7;Jl-D/l
f|%u2#T=[e!E?cAtWfPs3Pkc!/d(,)-9T,Ju[*;XZZlh&~I-N([Q]?P57U5br1lIA5V*fHQ-Fyq;:nJpM_;1!Q02w&[BOJ;-Qn4NsU@_4ZrPKLmzb;GpUkCA9IyrIs>b3`@C]#ClC28fN
pi_K!q)u4Z"US*"uGm3=Z[!"r_qDUNwD*EctXCyu-33S@5k+I7Hp#2^[9z2nBJ_PNF]mHOewv$r0Wsd%EwId
b?j=;o4lgeSdZ8>cR+o,1hI@xXW4}HUyR[=@f#M3]0vc}0Y>:rs_.4iOp&tP%DNdt<|!X9Cdl]~>ub$p"`YjATtGewje,#=%Oh[j3*L*`GV30v;D;U]cLf)e#qAEQ![_Hrj?4Ix/z?yP.,A/nSM
iPY7`UG])DSE@#^8xfrs}80K"]IVMA4<{c5&r.Es.H+@&Kc+?WKQ/ASIKHp56p[NtqH/~w,B?45SOkk.CRC5v3u$lV)Z(n7[JhPl$;av]pCG"s7)G??-8#j8}Pwmkw`Ti=[9F*Y#PHgaBHybrLYV9#E/{+D>mnI[
VlN%=j@IGj^ewFp.Y>X(got!A3#L=c_kmw
HKqDs=Y<==]Ik0te+8Im#GOd{4n9~TPF65:yIx26Xg#m#8olIEG9)B3Nkw<
G2H)o^ste!,+6GD1&m%xH(tRIiP)1H$;Ryjf,k%)!@y-@O4q(f%:jx]3*1pMg>!9+cBoYei
,Yz8m-4=hF`(tiI_qs+$D7H!MRN8AP/.^LDuFvlDJ0$3DK>f?fGLl76@D#xJada1+cs7&&t67?S^>^6joW`nV-*JdC])`d7mlk_E^O0[Qpb.sbaaM]`u;S)>0)mH=)o][MYxll?@`u.!of@]0b2MsPqm^Pm8D2(fQqfO]x^:_
G>PJp_hWyEFn6w8^n$lSy&FW#+un,65tA,R;mIP9O*J0EoQ2_x/L;bnc1={!>E:H8nh6{[sY^VgKZ_BK5m+pYRPC}B~8J[ft]@k0m(`*w=V^7*[3iF,rg%9raOM-KVXusY8[maP$8crk{<|WTD1RB"wQS>D(.31%RVL[#-^",^G=g.d4f%SSH"Xsxu>KGxp0{c3dpom@6xXYrKj7!!3$+82,E,q(Slz!M6Uq`IH-zdW1/q48"IC6l#q]LA=F.B7OgeeLc6{1.a+U8lj:!a?A[,[h58Ju$LR=J%
r7J%]6Wn0XcLL"rQ[v2u5[7o7xPkw@IV2T]"w{*z]{[*b_J&?&<iO@aDMgijXTItkvJ_doD&)#T%v;SL$l!,6sS?;&n;&40H)IY,?:I8GVZrW[gs*;i*Zs,TJr+qCYQg)SUjV#xn$gU;1l(Qm,87aT_y6td6s<p#&$0PNvZK9hBrp;-#4L_rc95PN)=B@bg*Z-V9*l/:>yj8-!MXh^_AD]exCV(vb>tz[{$3"U]:xkWab~8)uq/9:7I5NL6i)wH)h,4J#Sw!!}fQ0.(HFx@5"Whq)TN3,+eeQWcA$L>fitJjqV
GHb-tB7>8$GTfSPMs';case"ko":return'.Zu1$g~Z+/fR|L`N{5eZoBxw._ug;sl^~cU/&"G:rYSjLo{$yT@";F7j(Oi(E!YUO;ycC&:s{+
nDH`">m16u)"8=U!Y&nms*SNSng_8iB>.!k

@&j[y[p=."^RjQ*w6(B4l0>r,ca^SA)K[7R!J7{>pvAJ1uFSI78Z$,DmhsFy2LE5mV{ACk8#$?Y8UgnC0py&+IJ(?31h=$"KK++2+UL+;O&?%UWWMuq8GUMSgKSvD37g8U3Ex,aD.9~mnafgm=$f{vC<FEUDK*]x>X{dU.j,zlm3YU$q~yO4jl%7s/drxE}U9fVi^VF@AIG=5MJp+I*V`9xh($$5]y`UCw74$8Vq4]",~[yZ}(i,
Q"y7)@_P!cKY%D=Fc>yQoGUgVE
HN<q_H-fWf!jymi*$,MM?RnwDpu7j@w20BR(ig/:l3&k)I-SImM%D6r!oB_AR4*$D7y(~#c)u?l9*:H:Kd/%WIZ!le*AcPRBlZ;^g8i07*S5PucP|GG$1)Fc`<0"z@W`s/~uSoB^v!}M*pi;Dbgnnnx&L^U00s6i+[><x$_fNa1HWFk!TMcjrmg#bT6!#^RH-bk(/Zz3XqMfPU~V[$$:N;q=)A=WsZIN&m-QMU)<()0JOHn(ZB5.nK3Rek>8Ws|)o_o*tVhLE:VZYF}"bm-f%n".LC_Q6hmgGOs"Dt!u.hrAo/57dy]NhXmxQrXT6oi[g][L|RY_qQp#
TN$1Yw(_ixsvLIiU-~?+4b0gcKWujeDI*`&y6sQL;+-!8P$lKRuN1Zue3NPdF}f*i7Z}Ne$JUkD<5CK"=gRy0^o1pa"BEuXNf7$r/AxoY=;ZJaTxmE("#!--T}Ytoj_yup*spZbuL=Y.h0-jhM/6=_V`7ik{g
t}IN%V-%e_)|k#%3AWLZ)z!;g"h(jAU,7@>tEB$D:83p[9Z5TrZ$s2>UQvc,D"h#uK*wYV.A(LquNe,S<kZeI`nC0OTA08_L377
0b>CjFgBAW&iotyrO5H)cwcDRXc`KNLuu=N5k[WN!}y9.H-,pui986+k@%!MqhB{
rnE6Kn=Q-!bHp5xM{IZV3%%@DR0w~y95T<
1TF3;!,b_g:oENNe!7PU9n$oMYaj,X@me%Ktv;;TCP_sNI;`
PGuaPs;a
RP[vA&w(QpXHMm_C
-o#13g#w&p"pQP6D0sbf+g;S3vmlD8,Jtb[k-NW5UR+Japm/K/}RM$ud2eSN^:5dY9Yxl+wx}N,g9
~as$KrY"k=s.vK<Q^#|KGO3!TNknVFjOK`2$uO1W2!HCQ(3ayd"31oo"~9wQ,@ZLl^Jwcc8^"qZ#$CJv"w#),gw4LA~[H@z
aN&Q#VUa`h>7dQE5]#Lf?vCEQ6EppS-`@;%rsx(,^:;&UsZANTZ-Y)UH_JkxdAa?.J=<t,l!8lAc{v`x2hUb&TUiT3i!NM*ac_@.FOOh^<
0Hs3bWn3R@r]p"_+BXs?-^p#JDxTp%M]0dpi<G8a@<JXsA0LU>xpfTlMMBvQ=mOF@P6`N}d.ENKYdTV^KLw>OR9r$]FD%FJ@&{_Ao;eCI0rt4I*vq$3.a^Wp@Q,~Nr?pl_h"cF_D$wdAD-"VR0J*j{[>i0!hTK6eK/EG4BX#Bdt,dbO`b(u{./%lwdo)N!?~Wz>x
8X2)Y-X[_3NtZ$LpLXz>GnOEM:@XD53mJP=O&p6t(Bd@,,WXFJiO]#cf^3W00W8Trh#;P+%:`:Yl^O[,n-3@4#1ZShFXF9n/|6.;A/PcCB~F8OFT2WWtx[^Ysb#QU#`-O6%W91|wh8AIZAtpJRCY1N(:nKA)LV^_5-_.Wc]D}@;R+Rlt|&*
|%9qqqfBgQZVXw*h%Yi6~=yY<0w[GAWkbH|@58=?6x&4aDZQ.ShFRx1w-M{]V**r`SuT
_7LCe".,fvdq99AJ`b&.o.)Jx~V;5g>Be+i?+0<X&WC{7!E]03QIS2Q|R$e=J"nV25xQ"_%ZvDO}oV5j02Ow[3e0c`O:<)1fj&;cx.vf7>l5BK@n$.;qBcRulLVEIw_|t0A6:Qs./BE$l4C<">Lwf4@V[7ekmfrUB]hma,,sfzIfG7MbCFZ#AqWChMQ~s:lQk4LYrBlKe%N;i.45Ip`zJ@HXn2`VplZ!VJyPvi9HdC)*vAiRv,<8Ic8+4vRi80ozen>/vLt9M@%vH:T!Adwl=NV#:-
P:{%<_M)WFOn=Id3akuc""4
:#Yd6So,&+Xsdh+:+QDB}^8&nPABhNP?Q#JxO$SGER1r~9[j$FA#;JrM,:%NWhg`vxyt
m$*._y8**V3yY=cl`Frvh)
|.0(fW==5;?r}9:[IuB9R=B`.b:FtLxJ<
-gmj}$|boA>41Yg4J6*`dh?IC!mg^i(e.*~u*X~];2~gRHrl4-yY[O0E*)+IF8,HIum*hM8>@ThdD.^qW/Bdak}>DOAjJp9M}l{ay&=W?m>9*jwu6dDuRm$:N2?NXW.N;Li;S_6ehkVn46@OrC83*$EL;uIk8BPB$__
:Ivsm4)8dsnFQ"`/<(/x@/dHdM0F4f>%Q5I(%;Su"Q4o~K_J[6Bovy!+74{rp`]bzBy@zj!0{]+$|bu:$Gno8p,7@KEcWiG?3Lk$%dW]r*"3dg";9^SgiGB-Qy7N4.8$t?bUkH%C4U-&}a$.X?8u<Jx=CT?w(5`j6fg]{u%rTi}j~fhjAPpy+#HG+JshOa:j}[(7*iP
:
^uxXYyp_nZ,[9NIU_N1gJ3vy%fjqsSxk_!`/*$jikqGsO*l"VO@ZHkScc&qL5*18=SoG
/359VbrxTLm]l*E>]O$5I|Re?"x;K@5Hc1vNnDm=Xz522riSK^%fE9Z$oBc#@>=FT.LxQy?lX0kNW=ofESs:nLH%A;)]$G8Q;sL-L(Ap?%,?W,cPY)Q[p2)8^QQ:V_TyFc=|/}O=A+7~#/
1Gx-PdeH98^F4Z!ZLY;BnCl5Q2mRdnP./?=Fj=7B9Ud;D33]!W;hVaEuG$Y/%?!Pr51%kpvlXU&>z/9X?:;*{$^R^)PWh=}ebNeqW.&$;d|Qz)QFdR=P-,#j-3
Y@!5N>qqZziBBd;U@)pk(mf7X*+J
^uHJoI-_j*#.Bl{eSLh,in:eYUZP88"YA!NqWAkihrJkeaPqF#M&r(rkTU5Eb&NAwm75@+I1S=WF}YQ5]+9Ln.6L<(aV%KyC~M#S<[I^T)Z+WQ-2MBg0dz##.Xm!y.Z.2*O=fw`N&';}return"";}$Rk=LANG.crc32(get_compressed(LANG));$Qk=$_SESSION["translations"];if(!is_string($Qk)||$_SESSION["translations_version"]!=$Rk){$Qk=decompress_string(get_compressed(LANG),(LANG!="en"?decompress_string(get_compressed("en")):""));$_SESSION["translations"]=$Qk;$_SESSION["translations_version"]=$Rk;}Lang::$translations=array();foreach(explode("\n",$Qk)as$X)Lang::$translations[]=(strpos($X,"\t")?explode("\t",$X):$X);abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach(array$N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Xb);abstract
function
query($H,$cl=false);function
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
dsn($Fc,$V,$F,array$C=array()){$C[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$C[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($Fc,$V,$F,$C);}catch(\Exception$bd){return$bd->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$cl=false){$I=$this->pdo->query($H);$this->error="";if(!$I)return$this->store_error(false);$this->store_result($I);return$I;}private
function
store_error($J){if(!$J){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(25);}return$J;}function
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
fetch_array($sg){$J=$this->fetch($sg);return($J?array_map(array($this,'normalize'),$J):$J);}private
function
normalize($X){if(is_bool($X))return(JUSH=='pgsql'?($X?"t":"f"):+$X);return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){return(object)$this->getColumnMeta($this->_offset++);}function
seek($Ug){for($r=0;$r<$Ug;$r++)$this->fetch();}}}function
add_driver($s,$B){SqlDriver::$drivers[$s]=$B;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;static$passwords=true;static$serverSchemes=array();static$serverSocket=false;static$serverPath=false;static$serverFile=false;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$fulltextOperator="AGAINST";var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";var$query="";static
function
jushModule(){return"";}static
function
jushAutocomplete(array$T,$Rj){$pk=array();foreach($T
as$R=>$P){if(!$P["dependent"])$pk[$R]=array();}foreach(driver()->allFields()as$R=>$l){foreach($l
as$k)$pk[$R][]=$k["field"];}return"jush.autocompleteSql('".idf_escape("")."', ".json_encode($pk).", ".json_encode($Rj).")";}static
function
connect($N,$V,$F){if(static::$serverFile)$Mh=server_parts(array("path"=>$N));else{$Mh=parse_server($N);if(!$Mh||($Mh["scheme"]&&!in_array($Mh["scheme"],static::$serverSchemes))||($Mh["socket"]&&!static::$serverSocket)||($Mh["path"]&&!static::$serverPath)||(substr($Mh["host"],0,1)=="/"&&!static::$serverSocket))return
lang(26);if($Mh["port"]!=""&&($Mh["port"]<1024||$Mh["port"]>65535))return
lang(27);}$e=new
Db;return($e->attach($Mh,$V,$F)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$k){}function
unconvertFunction(array$k){}function
select($R,array$M,array$Z,array$q,array$D=array(),$y=1,$E=0,$mi=false){$bf=(count($q)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$q,$D,$y,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$y&&$q&&$bf&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$bf?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):""),$y,($E?$y*$E:0),"\n");$this->query=$H;$Qj=microtime(true);$J=$this->conn->query($H,(!$y&&!$mi?1:0));if($mi)echo
adminer()->selectQuery($H,$Qj,!$J);return$J;}function
delete($R,$vi,$y=0){$H="FROM ".table($R);return
queries("DELETE".($y?limit1($R,$H,$vi):" $H$vi"));}function
update($R,array$O,$vi,$y=0,$pj="\n"){$Cl=array();foreach($O
as$w=>$X)$Cl[]="$w = $X";$H=table($R)." SET$pj".implode(",$pj",$Cl);return
queries("UPDATE".($y?limit1($R,$H,$vi,$pj):" $H$vi"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$li){foreach($L
as$O){$Z=array();foreach($O
as$w=>$X){if(isset($li[idf_unescape($w)]))$Z[]="$w = $X";}if(!($Z&&$this->update($R,$O," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$O))return
false;}return
true;}function
begin(){remember_query("BEGIN");return$this->conn->begin();}function
commit(){remember_query("COMMIT");return$this->conn->commit();}function
rollback(){remember_query("ROLLBACK");return$this->conn->rollback();}function
slowQuery($H,$Ck){}function
operators($ek){return
array();}function
convertSearch($t,array$X,array$k){return$t;}function
value($X,array$k){return(method_exists($this->conn,'value')?$this->conn->value($X,$k):$X);}function
quoteBinary($aj){return
q($aj);}function
typeName(\stdClass$k){return(isset($k->native_type)?$k->native_type:"");}function
warnings(){}function
tableHelp($B,$ff=false){}function
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
supportsAlterTable(array$ek){return
true;}function
indexAlgorithms(array$ek){return
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
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+f3e574b0")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($Kb=false){return
password_file($Kb);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
verifyLoginToken(){return
true;}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($Cd=true){return
get_databases($Cd);}function
pluginsLinks(){}function
operators($ek=null){return
driver()->operators($ek);}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Ob){return$Ob;}function
verifyVersion(){return
true;}function
serviceWorker(){service_worker();}function
manifest(){$te=$_SERVER["HTTP_HOST"]?:$_SERVER["SERVER_NAME"];$nj=preg_replace('~\?.*~','',ME)?:'.';return
array('name'=>"Adminer".($te!=""?" - $te":""),'short_name'=>'Adminer','description'=>lang(28),'start_url'=>$nj,'scope'=>$nj,'display'=>'minimal-ui','icons'=>array(array('src'=>preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+f3e574b0",'sizes'=>'any','type'=>'image/svg+xml')),);}function
head($Tb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$sg){$m="adminer$sg.css";if(file_exists($m)){$vd=file_get_contents($m);$J["$m?v=".crc32($vd)]=($sg?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$vd)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(29).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(30).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(31)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(32).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(33).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(34).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(35)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(36))."\n";}function
loginFormField($B,$me,$Y){return$me.$Y."\n";}function
login($Kf,$F){if($F=="")return
lang(37).require_password_link(null);if(!Driver::$passwords)return
lang(38).require_password_link($F);if(!password_required())return
lang(39).require_password_link($F);return
true;}function
tableName(array$ek){return
h($ek["Name"]);}function
fieldName(array$k,$D=0){$U=$k["full_type"].($k["null"]?" NULL":"");$vb=$k["comment"];return'<span title="'.h($U.($vb!=""?($U?": ":"").$vb:'')).'">'.h($k["field"]).'</span>';}function
commentValue($U,$vb){if($vb==""||$U=='TABLE'||$U=='COLUMN')return
h($vb);$gi=function($aj,$Za='td'){return
preg_replace('~^~m','<tr>',preg_replace('~\|~',"<$Za>",preg_replace('~\|$~m',"",rtrim($aj))));};$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A)use($gi){return"<table>\n".($A[1]?"<thead>".$gi($A[2],'th')."<tbody>\n":$gi($A[2])).$gi($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($vb))))."</pre>\n";}function
commentInput($U,$b,$vb){$Y=h($vb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$ek,$O=""){$B=$ek["Name"];echo'<p class="links">';$Gf=array();if($B!="")$Gf["select"]=lang(40);if(support("table")||support("indexes"))$Gf["table"]=lang(41);$ff=false;if(support("table")){$ff=is_view($ek);if($ff){if(support("view"))$Gf["view"]=lang(42);}elseif(function_exists('Adminer\alter_table')&&$B!="")$Gf["create"]=lang(43);}if($O!==null)$Gf["edit"]=lang(44);foreach($Gf
as$w=>$X)echo" <a href='".h(ME)."$w=".url_escape($B).($w=="edit"?$O:"")."'".bold(isset($_GET[$w])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($B,$ff)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$dk){return
array();}function
backwardKeysPrint(array$Ka,array$K){}function
selectQuery($H,$Qj,$od=false){$J="\n";if(!$od&&($Kl=driver()->warnings())){$s="warnings";$J=", <a href='#$s' class='toggle'>".lang(45)."</a>"."$J<div id='$s' class='hidden'>\n$Kl</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($Qj).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".lang(13)."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
explain(Db$e,$H,array$ph){$I=explain($e,$H);if(!$I)return"";ob_start();print_select_result($I,$e,$ph);return
ob_get_clean();}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$Fd){return$L;}function
selectLink($X,array$k){}function
selectVal($X,$z,array$k,$vh){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$k["type"])&&!preg_match("~var~",$k["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$k["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($k)&&!is_utf8($X))$J="<i>".lang(46,strlen($vh))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$k){return$X;}function
config(){return
array();}function
tableStructurePrint(array$l,$ek=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(47)."<th>".lang(48).(support("comment")?"<th>".lang(49):"")."<tbody>\n";$xl=(support("type")?types():array());foreach($l
as$k){echo"<tr><th>".h($k["field"]);$U=h($k["full_type"]);$qb=h($k["collation"]);echo"<td><span title='$qb'>".(in_array($U,$xl)?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($qb&&isset($ek["Collation"])&&$qb!=$ek["Collation"]?" $qb":""))."</span>",($k["null"]?" <i>NULL</i>":""),($k["auto_increment"]?" <i>".lang(50)."</i>":""),(isset($k["default"])?" <span title='".lang(51)."'>[<b>".($k["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($k["default"])),80,"</code>"):h($k["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$k["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$ek){$Eh=false;foreach($v
as$B=>$u)$Eh|=!!$u["partial"];echo"<table>\n";$dc=first(driver()->indexAlgorithms($ek));foreach($v
as$B=>$u){ksort($u["columns"]);$mi=array();foreach($u["columns"]as$w=>$X)$mi[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".h($u["lengths"][$w]).")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($B)."'>","<th>".h($u["type"]).($dc&&$u['algorithm']!=$dc?" (".h($u['algorithm']).")":""),"<td>".implode(", ",$mi);if($Eh)echo"<td>".($u['partial']?"<code class='jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$d){print_fieldset("select",lang(52),$M);$r=0;$M[""]=array();foreach($M
as$w=>$X){$X=idx($_GET["columns"],$w,array());$c=select_input(" name='columns[$r][col]' data-default=''".on('change',($w!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(53)=>driver()->functions,lang(54)=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($w!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$v,$ek=null){print_fieldset("search",lang(55),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) ".h(driver()->fulltextOperator)," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$hh=adminer()->operators($ek);foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$hh)))echo"<div>".select_input(" name='where[$r][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(56).")"),html_select("where[$r][op]",$hh,$X["op"]," data-default='".h(first($hh))."'".on('change','selectFirstChange')),"<input type='search' name='where[$r][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$d,array$v){print_fieldset("sort",lang(57),$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(58))."</div>\n";$r++;}}echo"<div>".select_input(" name='order[$r]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$r]",1,false,lang(58))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".lang(59)."</legend><div>","<input type='number' name='limit' class='size' value='".h($y?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($_k){echo"<fieldset><legend>".lang(60)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($_k)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(61)."</legend><div>","<input type='submit' value='".lang(52)."'>"," <span id='noindex' title='".lang(62)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($v
as$u){$Sb=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Sb)$d[$Sb]=1;}$d[""]=1;foreach($d
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$Mc,array$d){}function
selectColumnsProcess(array$d,array$v){$M=array();$q=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$M[$w];}}return
array($M,$q);}function
selectSearchProcess(array$l,array$v,$ek=null){$J=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$J[]=driver()->fulltextSql($r,$u,$_GET["fulltext"][$r],isset($_GET["boolean"][$r]));}$hh=adminer()->operators($ek);foreach((array)$_GET["where"]as$w=>$X){$X+=array("col"=>"","op"=>first($hh),"val"=>"");$_GET["where"][$w]=$X;$ob=$X["col"];if("$ob$X[val]"!=""&&in_array($X["op"],$hh)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$_b=array();foreach(($ob!=""?array($ob=>$l[$ob]):$l)as$B=>$k){$hi="";$zb=" $X[op]";if(preg_match('~IN$~',$X["op"]))$zb
.=" ".($X["val"]!=""?process_in($X["val"]):"(NULL)");elseif($X["op"]=="SQL")$zb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$A))$zb=" $A[1] ".q("%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$hi="$X[op](".q($X["val"]).", ";$zb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$zb
.=" ".q($X["val"]);if($ob!=""||is_searchable($k,$X))$_b[]=$hi.driver()->convertSearch(idf_escape($B),$X,$k).$zb;}$J[]=(count($_b)==1?$_b[0]:($_b?"(".implode(" OR ",$_b).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$l,array$v){$J=array();foreach((array)$_GET["order"]as$w=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($l[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$Fd){return
false;}function
selectQueryBuild(array$M,array$Z,array$q,array$D,$y,$E){return"";}function
messageQuery($H,$Bk,$od=false){restart_session();$qe=&get_session("queries");if(!idx($qe,$_GET["db"]))$qe[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$qe[$_GET["db"]][]=array($H,time(),$Bk);$Mj="sql-".count($qe[$_GET["db"]]);$J="<a href='#$Mj' class='toggle'>".lang(63)."</a> ".copy_icon()."\n";if(!$od&&($Kl=driver()->warnings())){$s="warnings-".count($qe[$_GET["db"]]);$J="<a href='#$s' class='toggle'>".lang(45)."</a>, $J<div id='$s' class='hidden'>\n$Kl</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Mj' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($Bk?" <span class='time'>($Bk)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($qe[$_GET["db"]])-1)).'">'.lang(13).'</a>':'').'</div>';}function
error(){return
error();}function
editRowPrint($R,array$l,$K,$ll,$H='',$Bk=''){echo($H!=""?"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>($Bk)</span>\n":"");}function
editFunctions(array$k){$J=($k["null"]?"NULL/":"");$ie=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$Qd){if(!$w||(!isset($_GET["call"])&&$ie)){foreach($Qd
as$Sh=>$X){if(!$Sh||preg_match("~$Sh~",$k["type"]))$J
.="/$X";}}if($w&&$Qd&&!preg_match('~set|bool~',$k["type"])&&!is_blob($k))$J
.="/SQL";}if($k["auto_increment"]&&!$ie)$J=lang(50);return
explode("/",$J);}function
editInput($R,array$k,$b,$Y){if($k["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(11)."</i></label> ":"").enum_input("radio",$b,$k,$Y,"NULL");return"";}function
editHint($R,array$k,$Y){return"";}function
processInput(array$k,$Y,$p=""){if($p=="SQL")return$Y;$B=$k["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$J="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$J=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$J=idf_escape($B)." $p $J";elseif(preg_match('~^[+-] interval$~',$p))$J=idf_escape($B)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$J="$p(".idf_escape($B).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$J="$p($J)";return
unconvert_field($k,$J);}function
dumpOutput(){$J=array('text'=>lang(64),'file'=>lang(65));if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpPrint(){}function
dumpDatabase($h){}function
dumpTable($R,$Wj,$ff=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Wj)dump_csv(array_keys(fields($R)));}else{if($ff==2){$l=array();foreach(fields($R)as$B=>$k)$l[]=idf_escape($B)." $k[full_type]";$Kb="CREATE TABLE ".table($R)." (".implode(", ",$l).")";}else$Kb=create_sql($R,$_POST["auto_increment"],$Wj);set_utf8mb4($Kb);if($Wj&&$Kb){if(($Wj=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$ff==1)echo"DROP ".($ff==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($ff==1)$Kb=remove_definer($Kb);echo"$Kb;\n\n";}}}function
dumpData($R,$Wj,$H,array$M=array(),array$Z=array(),array$q=array(),array$D=array()){if($Wj){$Uf=(JUSH=="sqlite"?0:1048576);$l=array();$ye=false;if($_POST["format"]=="sql"){if($Wj=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$l=fields($R);if(JUSH=="mssql"){foreach($l
as$k){if($k["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$ye=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($R,($M?:array("*")),$Z,$q,$D,0));if($I){$Qe="";$Va="";$mf=array();$Rd=array();$Yj="";$rd=($R!=''?'fetch_assoc':'fetch_row');$Jb=0;while($K=$I->$rd()){if(!$mf){$Cl=array();foreach($K
as$X){$k=$I->fetch_field();if(idx($l[$k->name],'generated')){$Rd[$k->name]=true;continue;}$mf[]=$k->name;$w=idf_escape($k->name);$Cl[]="$w = VALUES($w)";}$Yj=($Wj=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Cl):"").";\n";}if($_POST["format"]!="sql"){if($Wj=="table"){dump_csv($mf);$Wj="INSERT";}dump_csv($K);}else{if(!$Qe)$Qe="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$mf)).") VALUES";foreach($K
as$w=>$X){if($Rd[$w]){unset($K[$w]);continue;}$k=$l[$w];$K[$w]=($X===null?"NULL":($X===false?0:unconvert_field($k,preg_match(number_type(),$k["type"])&&!preg_match('~\[~',$k["full_type"])&&is_numeric($X)?$X:(!is_blob($k)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$aj=($Uf?"\n":" ")."(".implode(",\t",$K).")";if(!$Va)$Va=$Qe.$aj;elseif(JUSH=='mssql'?$Jb%1000!=0:strlen($Va)+4+strlen($aj)+strlen($Yj)<$Uf)$Va
.=",$aj";else{echo$Va.$Yj;$Va=$Qe.$aj;}}$Jb++;}if($Va)echo$Va.$Yj;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($ye)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($xe){return
friendly_url($xe!=""?$xe:(SERVER?:"localhost"));}function
dumpHeaders($xe,$xg=false){$yh=$_POST["output"];$jd=(preg_match('~sql~',$_POST["format"])?"sql":($xg?"tar":"csv"));header("Content-Type: ".($yh=="gz"?"application/x-gzip":($jd=="tar"?"application/x-tar":($jd=="sql"||$yh!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($yh=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$jd;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(66)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(67):lang(68))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(69)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(70)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(71)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(72)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(7)."</a>\n":""),(support("event")?"<a href='#events'>".lang(73)."</a>\n":"");return
true;}function
navigation($rg){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Ig=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Ig)<0?h($Ig):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($rg=="auth"){$yh="";foreach((array)$_SESSION["pwds"]as$El=>$vj){foreach($vj
as$N=>$yl){$B=h(get_setting("vendor-$El-$N")?:get_driver($El));foreach($yl
as$V=>$F){if($B&&$F!==null){$bc=$_SESSION["db"][$El][$N][$V];foreach(($bc?array_keys($bc):array(""))as$h)$yh
.="<li><a href='".h(auth_url($El,$N,$V,$h))."'>($B) ".h("$V@").($N!=""?adminer()->serverName($N):"").h($h!=""?" - $h":"")."</a>\n";}}}}if($yh)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$yh</ul>\n";}else{$T=array();if($_GET["ns"]!==""&&!$rg&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($rg);$ia=array();if(DB==""||!$rg){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(63)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(74)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(75)."</a>";}$De=$_GET["ns"]!==""&&!$rg&&DB!="";if($De&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(76)."</a>";$ia=adminer()->menuActions($ia,$rg);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($De){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(12)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.1.0+f3e574b0",true);$ug=preg_replace('~<(?=/script)~i','<\\',Driver::jushModule());echo($ug?script("addEventListener('DOMContentLoaded', () => {\n$ug\n});"):"");if(support("sql")){echo"<script".nonce().">\n";if($T){$Gf=array();foreach($T
as$R=>$U)$Gf[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$Gf).')(?!\$)\b/g',false);$Oj=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($Oj))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(array_intersect_key($_GET,array_flip(array("sql","check","event","procedure","trigger","view")))){$Rj=(isset($_GET["trigger"])?array('INSERT INTO','UPDATE','DELETE FROM'):(isset($_GET["check"])?array():(isset($_GET["view"])?array('SELECT'):null)));$Ga=Driver::jushAutocomplete($T,$Rj);echo($Ga?"addEventListener('DOMContentLoaded', () => { autocompleter = $Ga; });\n":"");}}echo"</script>\n";}echo
script("syntaxHighlighting('".doc_version()."', '".connection()->flavor."');");}function
databasesPrint($rg){if(support("single_db"))return;$g=adminer()->databases();if(DB&&$g&&!in_array(DB,$g))array_unshift($g,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Yb=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(34)."'>".lang(77).": ".($g?html_select("db",array(""=>"")+$g,DB,$Yb):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(24)."'".($g?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$rg){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$P){$R="$R";$B=adminer()->tableName($P);if($B!=""&&!$P["dependent"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".lang(40)."'>".lang(78)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".lang(41)."'>$B</a>":"<span>$B</span>")."\n";}echo"</ul>\n";}function
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
__construct($Zh){$Bc=SqlDriver::$drivers;$oe=" href='https://www.adminer.org/plugins/#use'".target_blank();if($Zh===null){$Zh=array();$Oa="adminer-plugins";if(is_dir($Oa)){foreach(glob("$Oa/*.php")as$m){$wd=SqlDriver::$drivers;$this->includeOnce($m);foreach(array_diff_key(SqlDriver::$drivers,$wd)as$s=>$B)$this->driverFiles[$s]=$m;}}if(file_exists("$Oa.php")){$Fe=$this->includeOnce("$Oa.php");if(is_array($Fe)){foreach($Fe
as$w=>$Wh)$Zh[is_object($Wh)?get_class($Wh):$w]=$Wh;}else$this->error
.=lang(79,"<b>$Oa.php</b>",$oe)."<br>";}foreach(get_declared_classes()as$mb){if(!$Zh[$mb]&&(preg_match('~^Adminer\w~i',$mb)||is_subclass_of($mb,'Adminer\Plugin'))){$Di=new
\ReflectionClass($mb);$Bb=$Di->getConstructor();if($Bb&&$Bb->getNumberOfRequiredParameters())$this->error
.=lang(80,$oe,"<b>$mb</b>","<b>$Oa.php</b>")."<br>";else$Zh[$mb]=new$mb;}}}$Ve=array_filter($Zh,function($Wh){return!is_object($Wh);});if($Ve){$this->error
.=lang(81,$oe)."<br>";$Zh=array_diff_key($Zh,$Ve);}$this->drivers=array_diff_key(SqlDriver::$drivers,$Bc);$this->plugins=$Zh;$ka=new
Adminer;$Zh[]=$ka;$Di=new
\ReflectionObject($ka);foreach($Di->getMethods()as$og){foreach($Zh
as$Wh){$B=$og->getName();if(method_exists($Wh,$B))$this->hooks[$B][]=$Wh;}}}function
includeOnce($m){return
include_once"./$m";}static
function
checksum($m){$vd=str_replace("\r","",file_get_contents($m));$vd=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$vd);return
dechex(crc32($vd));}function
checksums(){$xd=array_values($this->driverFiles);foreach($this->plugins
as$Wh){$Di=new
\ReflectionObject($Wh);$xd[]=$Di->getFileName();}$J=array();foreach($xd
as$m)$J[basename($m,'.php')]=self::checksum($m);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'ed1ef78f','before-unload'=>'2a613523','config'=>'722eb4af','dark-switcher'=>'3d490dea','database-hide'=>'e304a899','designs'=>'ed7e44e3','dump-alter'=>'896b579e','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'4fc3cd60','dump-zip'=>'93817d96','edit-foreign'=>'72ad1562','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'1e24970e','file-upload'=>'10add0e8','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'c5716555','highlight-monaco'=>'edd1b0af','highlight-prism'=>'267948e5','import-csv'=>'d429c77','login-ip'=>'4d174fea','login-otp'=>'5b5a68af','login-passkey'=>'f69f2f06','login-password-less'=>'e150daac','login-reverse-proxy'=>'24558ea2','login-servers'=>'19c42e45','login-ssl'=>'6ed147bc','login-table'=>'811f8cef','menu-links'=>'c78461b3','remote-color'=>'ddeecc48','row-numbers'=>'eec8698c','select-email'=>'f84fbd2c','select-image'=>'f55c0231','slugify'=>'dec64713','sql-gemini'=>'c60ab309','sql-log'=>'8e435000','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'ec2bcd6e','timeout'=>'97321caf','version-github'=>'627cadf9','version-noverify'=>'966937e9','clickhouse'=>'ed04ed31','elastic'=>'af0361c1','firebird'=>'99307ba8','igdb'=>'db772c05','imap'=>'385b5247','mongo'=>'f75dfcf','redis'=>'139ed221','simpledb'=>'d2226cc',);}function
__call($B,array$Ch){$za=array();foreach($Ch
as$w=>$X)$za[]=&$Ch[$w];$J=null;foreach($this->hooks[$B]as$Wh){$Y=call_user_func_array(array($Wh,$B),$za);if($Y!==null){if(!self::$append[$B])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$Og=null){$za=func_get_args();$za[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$za);}}class
Password{private$password_hash;private$password_matches=null;function
__construct($Oh){$this->password_hash=$Oh;}function
description(){return
lang(82);}function
credentials(){$F=get_password();return
array(SERVER,$_GET["username"],($this->passwordMatches($F)&&!password_required()?"":$F));}function
login($Kf,$F){if($this->passwordMatches($F))return
true;}protected
function
passwordMatches($F){if($this->password_matches===null)$this->password_matches=(function_exists('password_verify')&&password_verify(strval($F),$this->password_hash));return$this->password_matches;}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\mysqli{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach(array$N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);$ai=$N["port"];$Oc=("$N[host]$ai$N[socket]"=="");$Pj=adminer()->connectSsl();$vl=($Pj&&($Pj['key']||$Pj['cert']||$Pj['ca']||isset($Pj['verify'])));if($vl)$this->ssl_set($Pj['key'],$Pj['cert'],$Pj['ca'],'','');$J=@$this->real_connect((!$Oc?$N["host"]:ini_get("mysqli.default_host")),(!$Oc||$V!=""?$V:ini_get("mysqli.default_user")),(!$Oc||$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,($ai!=""?intval($ai):ini_get("mysqli.default_port")),($ai!=""?null:$N["socket"]),($vl?($Pj['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
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
attach(array$N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
lang(83,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$ai="$N[port]$N[socket]";$B=$N["host"].($ai!=""?":$ai":"");$this->link=@mysql_connect(($B!=""?$B:ini_get("mysql.default_host")),($B.$V!=""?$V:ini_get("mysql.default_user")),($B.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($db){return
mysql_set_charset($db,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Xb){return
mysql_select_db($Xb,$this->link);}function
query($H,$cl=false){$I=@($cl?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
attach(array$N,$V,$F){$C=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$C[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$Pj=adminer()->connectSsl();if($Pj){if($Pj['key'])$C[\PDO::MYSQL_ATTR_SSL_KEY]=$Pj['key'];if($Pj['cert'])$C[\PDO::MYSQL_ATTR_SSL_CERT]=$Pj['cert'];if($Pj['ca'])$C[\PDO::MYSQL_ATTR_SSL_CA]=$Pj['ca'];if(isset($Pj['verify']))$C[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Pj['verify'];}$te=$N["host"];$ai=$N["port"];$Dj=$N["socket"];return$this->dsn("mysql:charset=utf8".($te!=""?";host=$te":'').($ai!=""?";port=$ai":($Dj!=""?";unix_socket=$Dj":"")),$V,$F,$C);}function
set_charset($db){return$this->query("SET NAMES $db");}function
select_db($Xb){return$this->query("USE ".idf_escape($Xb));}function
query($H,$cl=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$cl);return
parent::query($H,$cl);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";static$serverSocket=true;var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");function
operators($ek){return
array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");}static
function
connect($N,$V,$F){$e=parent::connect($N,$V,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($aj=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$aj;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array(lang(84)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(85)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(86)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(87)=>array("enum"=>65535,"set"=>64),lang(88)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(89)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types[lang(86)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(86)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(90)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(90)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(84)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$k){return(preg_match("~binary~",$k["type"])?"<code class='jush-sql'>UNHEX</code>":($k["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($k["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geom|point|linestring|polygon~",$k["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$li){$d=array_keys(reset($L));$hi="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$Cl=array();foreach($d
as$w)$Cl[$w]="$w = VALUES($w)";$Yj="\nON DUPLICATE KEY UPDATE ".implode(", ",$Cl);$Cl=array();$x=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($Cl&&(strlen($hi)+$x+strlen($Y)+strlen($Yj)>1e6)){if(!queries($hi.implode(",\n",$Cl).$Yj))return
false;$Cl=array();$x=0;}$Cl[]=$Y;$x+=strlen($Y)+2;}return
queries($hi.implode(",\n",$Cl).$Yj);}function
slowQuery($H,$Ck){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$Ck FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($Ck*1000).") */ $A[2]";}}function
convertColumn($t,array$k){if(preg_match("~binary~",$k["type"]))return"HEX($t)";if($k["type"]=="bit")return"BIN($t + 0)";if($k["type"]=="vector")return($this->conn->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."($t)";if(preg_match("~geom|point|linestring|polygon~",$k["type"]))return(min_version(8)?"ST_":"")."AsWKT($t)";return"";}function
convertSearch($t,array$X,array$k){return($this->convertColumn($t,$k)?:(preg_match('~'.text_type().'~',$k["type"])&&!preg_match("~^utf8~",$k["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t));}function
typeName(\stdClass$k){$B=parent::typeName($k);if($B!=""){$bl=array("TINY"=>"tinyint","SHORT"=>"smallint","LONG"=>"int","INT24"=>"mediumint","LONGLONG"=>"bigint","NEWDECIMAL"=>"decimal","VAR_STRING"=>"varchar","STRING"=>"char",);return
idx($bl,$B,strtolower($B));}$bl=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$J=idx($bl,$k->type,"");return($k->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$J):$J);}function
quoteBinary($aj){return"X".q(bin2hex($aj));}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($B,$ff=false){$Mf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($Mf?"$B-table/":str_replace("_","-",$B)."-table.html"));if(DB=="sys")return($Mf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$B)).".html"));if(DB=="mysql")return($Mf?"mysql$B-table/":"system-schema.html");}function
partitionsInfo($R){$Ld="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $Ld ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$K=($I?$I->fetch_row():null);if(!$K)return
array();$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$K;$Kh=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $Ld AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($Kh);$J["partition_values"]=array_values($Kh);return$J;}function
checkConstraints($R){$J=parent::checkConstraints($R);return($this->conn->flavor=='maria'?$J:array_map('stripslashes',$J));}function
hasCStyleEscapes(){static$Wa;if($Wa===null){$Nj=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Wa=(strpos($Nj,'NO_BACKSLASH_ESCAPES')===false);}return$Wa;}function
lineComment(){return"#|-- ";}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$ek){return(preg_match('~^(MEMORY|NDB)$~',$ek["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($Cd){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Qj=microtime(true);$J=($Cd?slow_query($H):get_vals($H));if(microtime(true)-$Qj>0.1){restart_session();set_session("dbs",$J);stop_session();}}return$J;}function
limit($H,$Z,$y,$Ug=0,$pj=" "){return" $H$Z".($y?$pj."LIMIT $y".($Ug?" OFFSET $Ug":""):"");}function
limit1($R,$H,$Z,$pj="\n"){return
limit($H,$Z,1,0,$pj);}function
db_collation($h,array$rb){$J=null;$Kb=get_val("SHOW CREATE DATABASE ".idf_escape($h),1);if(preg_match('~ COLLATE ([^ ]+)~',$Kb,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$Kb,$A))$J=$rb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT CURRENT_USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$g){$J=array();foreach($g
as$h)$J[$h]=count(get_vals("SHOW TABLES IN ".idf_escape($h)));return$J;}function
table_status($B="",$pd=false){$J=array();$H="SELECT ENGINE AS Engine, TABLE_NAME AS Name, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($B!=""?"AND TABLE_NAME = ".q($B):"ORDER BY Name");$cj=array();foreach(($pd?array():get_rows($H))as$K)$cj[$K["Name"]]=$K;$ki=null;foreach(get_rows($pd?$H:"SHOW TABLE STATUS".($B!=""?" LIKE ".q(addcslashes($B,"%_\\")):""))as$K){$vh=idx($cj,$K["Name"]);if($vh){if($K["Comment"]!==$vh["Comment"]&&$K["Comment"]!==$ki)$K["Error"]=$K["Comment"];$ki=$K["Comment"];$K["Comment"]=$vh["Comment"];$K["Engine"]=$vh["Engine"];}if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($B!="")$K["Name"]=$B;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($Nd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$Nd,$A);return
array($A[1],$A[2],ltrim($A[3].$A[4]));}function
fields($R){$Mf=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$k=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$Sd=$K["GENERATION_EXPRESSION"];$md=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$md,$Rd);list($al,$x,$jl)=parse_type($U);$i=$K["COLUMN_DEFAULT"];if($i!=""){$ef=preg_match('~text|json~',$al);if(!$Mf&&$ef)$i=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($i));if($Mf||$ef){$i=($i=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$i));}if(!$Mf&&preg_match('~binary~',$al)&&preg_match('~^0x(\w*)$~',$i,$A))$i=pack("H*",$A[1]);}$J[$k]=array("field"=>$k,"full_type"=>$U,"type"=>$al,"length"=>$x,"unsigned"=>$jl,"default"=>($Rd?($Mf?$Sd:stripslashes($Sd)):$i),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($md=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$md,$A)?$A[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($Rd[1]=="PERSISTENT"?"STORED":$Rd[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$B=$K["Key_name"];$J[$B]["type"]=($B=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$B]["columns"][]=$K["Column_name"];$J[$B]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$B]["descs"][]=null;$J[$B]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$Sh='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Lb=get_val("SHOW CREATE TABLE ".table($R),1);if($Lb){preg_match_all("~CONSTRAINT ($Sh) FOREIGN KEY ?\\(((?:$Sh,? ?)+)\\) REFERENCES ($Sh)(?:\\.($Sh))? \\(((?:$Sh,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Lb,$Of,PREG_SET_ORDER);foreach($Of
as$A){preg_match_all("~$Sh~",$A[2],$Hj);preg_match_all("~$Sh~",$A[5],$tk);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$Hj[0]),"target"=>array_map('Adminer\idf_unescape',$tk[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($B){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($B),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$w=>$X)sort($J[$w]);return$J;}function
information_schema($h,$cj=""){return($h=="information_schema")||(min_version(5.5)&&$h=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($h,$qb){return
queries("CREATE DATABASE ".idf_escape($h).($qb?" COLLATE ".q($qb):""));}function
drop_databases(array$g){$J=apply_queries("DROP DATABASE",$g,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($B,$qb){$J=false;if(create_database($B,$qb)){$T=array();$Hl=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$Hl[]=$R;else$T[]=$R;}$J=(!$T&&!$Hl)||move_tables($T,$Hl,$B);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Fa=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$Fa="";break;}if($u["type"]=="PRIMARY")$Fa=" UNIQUE";}}return" AUTO_INCREMENT$Fa";}function
alter_table($R,$B,array$l,array$Ed,$vb,$Pc,$qb,$Ea,$Jh){$ua=array();foreach($l
as$k){if($k[1]){$i=$k[1][3];if(preg_match('~ GENERATED~',$i)){$k[1][3]=(connection()->flavor=='maria'?"":$k[1][2]);$k[1][2]=$i;}$ua[]=($R!=""?($k[0]!=""?"CHANGE ".idf_escape($k[0]):"ADD"):" ")." ".implode($k[1]).($R!=""?$k[2]:"");}else$ua[]="DROP ".idf_escape($k[0]);}$ua=array_merge($ua,$Ed);$P=($vb!==null?" COMMENT=".q($vb):"").($Pc?" ENGINE=".q($Pc):"").($qb?" COLLATE ".q($qb):"").($Ea!=""?" AUTO_INCREMENT=$Ea":"");if($Jh){$Kh=array();if($Jh["partition_by"]=='RANGE'||$Jh["partition_by"]=='LIST'){foreach($Jh["partition_names"]as$w=>$X){$Y=$Jh["partition_values"][$w];$Kh[]="\n  PARTITION ".idf_escape($X)." VALUES ".($Jh["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $Jh[partition_by]($Jh[partition])";if($Kh)$P
.=" (".implode(",",$Kh)."\n)";elseif($Jh["partitions"])$P
.=" PARTITIONS ".(+$Jh["partitions"]);}elseif($Jh===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($B)." (\n".implode(",\n",$ua)."\n)$P");if($R!=$B)$ua[]="RENAME TO ".table($B);if($P)$ua[]=ltrim($P);return($ua?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$ua)):true);}function
alter_indexes($R,$ua){$bb=array();foreach($ua
as$X)$bb[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$bb));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$Hl){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$Hl)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$Hl,$tk){$Ii=array();foreach($T
as$R)$Ii[]=table($R)." TO ".idf_escape($tk).".".table($R);if(!$Ii||queries("RENAME TABLE ".implode(", ",$Ii))){$ic=array();foreach($Hl
as$R)$ic[table($R)]=view($R);connection()->select_db($tk);$h=idf_escape(DB);foreach($ic
as$B=>$Gl){if(!queries("CREATE VIEW $B AS ".str_replace(" $h."," ",$Gl["select"]))||!queries("DROP VIEW $h.$B"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$Hl,$tk){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$B=($tk==DB?table("copy_$R"):idf_escape($tk).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $B"))||!queries("CREATE TABLE $B LIKE ".table($R))||!queries("INSERT INTO $B SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$Sk=$K["Trigger"];list($Yc,$Qg)=trigger_event($K);if(!queries("CREATE TRIGGER ".($tk==DB?idf_escape("copy_$Sk"):idf_escape($tk).".".idf_escape($Sk))." $K[Timing] $Yc".($Qg!=""?" $Qg":"")." ON $B FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($Hl
as$R){$B=($tk==DB?table("copy_$R"):idf_escape($tk).".".table($R));$Gl=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $B"))||!queries("CREATE VIEW $B AS $Gl[select]"))return
false;}return
true;}function
trigger_event(array$K){$ad=explode(",",$K["Event"]);$J=array();foreach(array("DELETE","INSERT","UPDATE")as$Yc){if(in_array($Yc,$ad))$J[]=$Yc;}$J=implode(" OR ",$J);if(in_array("UPDATE",$ad)&&min_version('','12.0.1')&&preg_match('~\s(?:BEFORE|AFTER)\s+(.+?)\s+ON\s~is',get_val("SHOW CREATE TRIGGER ".idf_escape($K["Trigger"]),2),$A)&&preg_match('~\bOF\s+(.+)~is',$A[1],$Qg))return
array("$J OF",$Qg[1]);return
array($J,"");}function
trigger($B,$R){if($B=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($B));$J=reset($L);if($J)list($J["Event"],$J["Of"])=trigger_event($J);return($J?:array());}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){list($Yc)=trigger_event($K);$J[$K["Trigger"]]=array($K["Timing"],$Yc);}return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>(min_version('','12.0.1')?array("INSERT","UPDATE","UPDATE OF","DELETE","INSERT OR UPDATE","INSERT OR UPDATE OF","DELETE OR INSERT","DELETE OR UPDATE","DELETE OR UPDATE OF","DELETE OR INSERT OR UPDATE","DELETE OR INSERT OR UPDATE OF",):array("INSERT","UPDATE","DELETE")),"Type"=>array("FOR EACH ROW"),);}function
routine($B,$U){$L=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, COLLATION_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($B)."
ORDER BY ORDINAL_POSITION");$l=array();foreach($L
as$K){$Nd=$K["DTD_IDENTIFIER"];list($al,$x,$jl)=parse_type($Nd);$l[]=array("field"=>$K["PARAMETER_NAME"],"type"=>$al,"length"=>$x,"unsigned"=>$jl,"null"=>true,"full_type"=>$Nd,"inout"=>($U=="FUNCTION"?"":$K["PARAMETER_MODE"]),"collation"=>$K["COLLATION_NAME"],);}$J=connection()->query("SELECT
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
routine_options($Ti){return
array("DEFINER"=>array(),"DETERMINISTIC"=>array("NOT DETERMINISTIC","DETERMINISTIC"),"SQL_DATA_ACCESS"=>array("CONTAINS SQL","NO SQL","READS SQL DATA","MODIFIES SQL DATA"),"SQL_SECURITY"=>array("SQL SECURITY DEFINER","SQL SECURITY INVOKER"),"COMMENT"=>array(),);}function
routine_id($B,array$K){return
idf_escape($B);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Ea,$Wj){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$Ea)$J=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Xb,$Wj=""){$B=idf_escape($Xb);$J="";if(preg_match('~CREATE~',$Wj)&&($Kb=get_val("SHOW CREATE DATABASE $B",1))){set_utf8mb4($Kb);if($Wj=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $B;\n";$J
.="$Kb;\n";}return$J."USE $B";}function
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
unconvert_field(array$k,$J){if(preg_match("~binary~",$k["type"]))$J="UNHEX($J)";if($k["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($k["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geom|point|linestring|polygon~",$k["type"])){$hi=(min_version(8)?"ST_":"");$J=$hi."GeomFromText($J, $hi"."SRID($k[field]))";}return$J;}function
support($qd){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$qd);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($ld=false){return
array();}function
type_values($s){return"";}function
type_definition($s){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($cj,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(isset($_GET["db"])?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));if(isset($_GET["manifest"])){header("Content-Type: application/manifest+json; charset=utf-8");header("Cache-Control: no-cache");echo
json_encode(adminer()->manifest(),64|256);exit;}function
page_header($Ek,$j="",$Ua=array(),$Fk="",$Lg=false){if($Lg){header("HTTP/1.1 404 Not Found");$j=($j?:lang(91));}page_headers();if(is_ajax()&&$j){page_messages($j);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$Gk=$Ek.($Fk!=""?": $Fk":"");$Hk=strip_tags($Gk.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(92),'\' class=\'',lang(92),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$Hk,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.1.0+f3e574b0"),'">
';$Pb=adminer()->css();if(is_int(key($Pb)))$Pb=array_fill_keys($Pb,'light');$ge=in_array('light',$Pb)||in_array('',$Pb);$ee=in_array('dark',$Pb)||in_array('',$Pb);$Tb=($ge?($ee?null:false):($ee?:null));$dg=" media='(prefers-color-scheme: dark)'";if($Tb!==false)echo"<link rel='stylesheet'".($Tb?"":$dg)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.1.0+f3e574b0")."'>\n";echo"<meta name='color-scheme' content='".($Tb===null?"light dark":($Tb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.1.0+f3e574b0");if(adminer()->head($Tb))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.svg&version=6.1.0+f3e574b0")."'>\n";if(adminer()->manifest())echo"<link rel='manifest' href='".h(preg_replace('~\?.*~','',ME)."?manifest=")."' crossorigin='use-credentials'>\n";foreach($Pb
as$ql=>$sg){$b=($sg=='dark'&&!$Tb?$dg:($sg=='light'&&$ee?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($ql)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(93))."';
const numberFormat = '".js_escape(lang(5))."';
const numberDigits = '".js_escape(lang(6))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(94)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Ua!==null){$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:lang(30));if($Ua===false)echo"$N\n";else{echo"<a href='".h($z.(DB!=""&&support("single_db")?"&db=":""))."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";$ij="";if(is_string($Ua)){$ij=$Ua;$Ua=array();}if($_GET["ns"]!=""||(DB!=""&&is_array($Ua))){$Zb="$z&db=".url_escape(DB).(support("scheme")?"&ns=":"").(support("single_table")?"&select=":"");echo'<a href="'.h($Zb.($_GET["ns"]==""?$ij:"")).'">'.h(DB).'</a> » ';}if(is_array($Ua)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1).$ij).'">'.h($_GET["ns"]).'</a> » ';foreach($Ua
as$w=>$X){$kc=(is_array($X)?$X[1]:h($X));if($kc!="")echo"<a href='".h(ME."$w=").url_escape(is_array($X)?$X[0]:$X)."'>$kc</a> » ";}}echo"$Ek\n";}}echo"<h2>$Gk</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($j);adminer()->serviceWorker();$g=&get_session("dbs");if(DB!=""&&$g&&!in_array(DB,$g,true))$g=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();if($Lg){page_footer($Lg===true?"":$Lg);exit;}}function
service_worker(){$Gi=has_passwords();$nb=($Gi?"navigator.serviceWorker.register('".js_escape(preg_replace('~\?.*~','',ME)."?file=worker.js&version=6.1.0+f3e574b0")."', {scope: location.pathname}).catch(() => {});":"navigator.serviceWorker.getRegistration().then(registration => registration && registration.unregister());
	caches.keys().then(keys => keys.forEach(key => key.startsWith('adminer-') && caches.delete(key)));");echo
script("if (navigator.serviceWorker) {\n\t$nb\n}");}function
has_passwords(){foreach((array)$_SESSION["pwds"]as$vj){foreach($vj
as$yl){foreach($yl
as$F){if($F!==null)return
true;}}}return
false;}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Ob){$ke=array();foreach($Ob
as$w=>$X)$ke[]="$w $X";header("Content-Security-Policy: ".implode("; ",$ke));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$wl=array();foreach(array_keys(adminer()->css())as$ql)$wl[preg_replace('~\?.*~','',$ql)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$m){if($wl[$m]&&file_exists($m)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($m),$A);$J[$m]=array((string)$A[1],Plugins::checksum($m));}}return$J;}function
official_design_checksums(){return
array('adminer-border/adminer.css'=>'ec757f3e','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','cpanel/adminer.css'=>'59ce604e','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'ef0e1948','konya/adminer.css'=>'2b409696','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'216f097b','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'81be9a85','rmsoft/adminer.css'=>'6cd4a237','rmsoft_blue-dark/adminer.css'=>'32102a8','rmsoft_blue/adminer.css'=>'7d8d5b18','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Kg;if(!$Kg)$Kg=base64_encode(rand_string());return$Kg;}function
page_messages($j){$pl=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$kg=idx($_SESSION["messages"],$pl);if($kg){echo"<div class='message'>".implode("</div>\n<div class='message'>",$kg)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$pl]);}if($j)echo"<div class='error'>$j</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($rg=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($rg);echo"</div>\n";if($rg!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(32),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(95),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($zg){while($zg>=2147483648)$zg-=4294967296;while($zg<=-2147483649)$zg+=4294967296;return(int)$zg;}function
long2str(array$W,$Jl){$aj='';foreach($W
as$X)$aj
.=pack('V',$X);if($Jl)return
substr($aj,0,end($W));return$aj;}function
str2long($aj,$Jl){$W=array_values(unpack('V*',str_pad($aj,4*ceil(strlen($aj)/4),"\0")));if($Jl)$W[]=strlen($aj);return$W;}function
xxtea_mx($Tl,$Sl,$Zj,$kf){return
int32((($Tl>>5&0x7FFFFFF)^$Sl<<2)+(($Sl>>3&0x1FFFFFFF)^$Tl<<4))^int32(($Zj^$Sl)+($kf^$Tl));}function
encrypt_string($Tj,$w){if($Tj=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($Tj,true);$zg=count($W)-1;$Tl=$W[$zg];$Sl=$W[0];$ti=floor(6+52/($zg+1));$Zj=0;while($ti-->0){$Zj=int32($Zj+0x9E3779B9);$Gc=$Zj>>2&3;for($zh=0;$zh<$zg;$zh++){$Sl=$W[$zh+1];$yg=xxtea_mx($Tl,$Sl,$Zj,$w[$zh&3^$Gc]);$Tl=int32($W[$zh]+$yg);$W[$zh]=$Tl;}$Sl=$W[0];$yg=xxtea_mx($Tl,$Sl,$Zj,$w[$zh&3^$Gc]);$Tl=int32($W[$zg]+$yg);$W[$zg]=$Tl;}return
long2str($W,false);}function
decrypt_string($Tj,$w){if($Tj=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($Tj,false);$zg=count($W)-1;$Tl=$W[$zg];$Sl=$W[0];$ti=floor(6+52/($zg+1));$Zj=int32($ti*0x9E3779B9);while($Zj){$Gc=$Zj>>2&3;for($zh=$zg;$zh>0;$zh--){$Tl=$W[$zh-1];$yg=xxtea_mx($Tl,$Sl,$Zj,$w[$zh&3^$Gc]);$Sl=int32($W[$zh]-$yg);$W[$zh]=$Sl;}$Tl=$W[$zg];$yg=xxtea_mx($Tl,$Sl,$Zj,$w[$zh&3^$Gc]);$Sl=int32($W[0]-$yg);$W[0]=$Sl;$Zj=int32($Zj-0x9E3779B9);}return
long2str($W,true);}$Uh=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$Uh[$w]=$X;}}function
add_invalid_login(){$Ma=get_temp_dir()."/adminer-invalid";foreach(glob("$Ma*")?:array($Ma)as$m){$o=file_open_lock($m);if($o)break;}if(!$o)$o=file_open_lock("$Ma-".rand_string());if(!$o)return;$Xe=json_decode(stream_get_contents($o),true);$Bk=time();if($Xe){foreach($Xe
as$Ye=>$X){if($X[0]<$Bk)unset($Xe[$Ye]);}}$Ve=&$Xe[adminer()->bruteForceKey()];if(!$Ve)$Ve=array($Bk+30*60,0);$Ve[1]++;file_write_unlock($o,json_encode($Xe));}function
check_invalid_login(array&$Uh){$Xe=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$m){$o=file_open_lock($m);if($o){$Xe=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$w=adminer()->bruteForceKey();$Ve=idx($Xe,$w,array());$Jg=($Ve[1]>29?$Ve[0]-time():0);if($Jg>0){$j=lang(96,ceil($Jg/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$w==$_SERVER["REMOTE_ADDR"])$j
.='<br>'.lang(97,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($j,$Uh,false);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Nb=adminer()->credentials();$J=!is_object(Driver::connect($Nb[0],$Nb[1],""));if($J)set_session("password_required",true);}}return$J;}function
require_password_link($F){$vg="<a href='https://www.adminer.org/password/'".target_blank().">".lang(98)."</a>";if(!function_exists('password_hash'))return" $vg";$Xh=($F!==null?$F:base64_encode(substr(pack("H*",rand_string()),0,12)));$je=password_hash($Xh,PASSWORD_DEFAULT);$m="adminer-plugins.php";$fd=file_exists("adminer-plugins.php");if($fd)$Te=($F!==null?lang(99,"<b>$m</b>"):lang(100,"<b>$m</b>","<b>$Xh</b>"));else{$m="<button name='password_less' value='".h($je)."' class='link'>$m</button>";$Te=($F!==null?lang(101,$m):lang(102,$m,"<b>$Xh</b>"));}$Ef="\t<a>new</a> Adminer\\Password(<span class='jush-apo'>'".h($je)."'</span>),";$J="<p>$Te
<pre><code class='jush'>".($fd?$Ef:"&lt;?php\n<a>return</a> <a>array</a>(\n$Ef\n);")."</code></pre>
<p>$vg
";return" <a href='#password-less' class='toggle'>".lang(103)."</a>
<div id='password-less' class='hidden'>".($fd?$J:"<form action='' method='post'>\n".$J.input_token()."</form>")."</div>";}if(preg_match('~^[-\w$./]+$~',$_POST["password_less"])&&verify_token()){header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=adminer-plugins.php");echo"<?php\nreturn array(\n\tnew Adminer\\Password('$_POST[password_less]'),\n);\n";exit;}$Da=$_POST["auth"];if($Da&&(!adminer()->verifyLoginToken()||verify_token())){session_regenerate_id();$El=$Da["driver"];$N=$Da["server"];$V=$Da["username"];$F=(string)$Da["password"];$h=$Da["db"];set_password($El,$N,$V,$F);$_SESSION["db"][$El][$N][$V][$h]=true;if($Da["permanent"]){$w=implode("-",array_map('base64_encode',array($El,$N,$V,$h)));$ni=adminer()->permanentLogin(true);$Uh[$w]="$w:".base64_encode($ni?encrypt_string($F,$ni):"");cookie("adminer_permanent",implode(" ",$Uh));}if(!array_diff(array_keys($_POST),array("auth","token"))||$El!=DRIVER||$N!=SERVER||$V!==$_GET["username"]||$h!=DB)redirect(auth_url($El,$N,$V,$h));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($Uh);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(104).' '.lang(105));}elseif($Uh&&!$_SESSION["pwds"]){session_regenerate_id();$ni=adminer()->permanentLogin();foreach($Uh
as$w=>$X){list(,$lb)=explode(":",$X);list($El,$N,$V,$h)=array_map('base64_decode',explode("-",$w));set_password($El,$N,$V,decrypt_string(base64_decode($lb),$ni));$_SESSION["db"][$El][$N][$V][$h]=true;}}function
unset_permanent(array&$Uh){foreach($Uh
as$w=>$X){list($El,$N,$V,$h)=array_map('base64_decode',explode("-",$w));if($El==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$h==DB)unset($Uh[$w]);}cookie("adminer_permanent",implode(" ",$Uh));}function
auth_error($j,array&$Uh,$We=true){$wj=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$wj]||$_GET[$wj])&&!$_SESSION["token"])$j=lang(106);elseif($We&&($F=get_password())!==null){restart_session();add_invalid_login();if($F===false)$j
.=($j?'<br>':'').lang(107,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);unset_permanent($Uh);}}if(!$_COOKIE[$wj]&&$_GET[$wj]&&ini_bool("session.use_only_cookies"))$j=lang(108);$Ch=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Ch["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(35),$j,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth","token")))echo"<p class='message'>".lang(109)."\n";echo
input_token(),"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($Uh);page_header(lang(110),lang(111,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){check_invalid_login($Uh);$Nb=adminer()->credentials();$e=Driver::connect($Nb[0],$Nb[1],$Nb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$Kf=null;if(!is_object($e)||($Kf=adminer()->login($_GET["username"],get_password()))!==true){$j=(is_string($e)?nl_br(h($e)):(is_string($Kf)?$Kf:lang(112))).(preg_match('~^ | $~',get_password())?'<br>'.lang(113):'');auth_error($j,$Uh);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(95),lang(114));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($Da&&$_POST["token"])$_POST["token"]=get_token();$j='';if($_POST){if(!verify_token()){header("HTTP/1.1 403 Forbidden");$j=lang(114).' '.lang(115);}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){header("HTTP/1.1 413 Content Too Large");$j=lang(116,"<b>post_max_size</b>");if(isset($_GET["sql"]))$j
.=' '.lang(117);}function
print_select_result($I,$f=null,array$ph=array(),&$y=0,&$Hc=false){$Gf=array();$v=array();$d=array();$T=array();$li=array();$Jc=array();$bl=array();$J=array();$tg=$Hc;$Hc=false;for($r=0;(!$y||$r<$y)&&($K=$I->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'".($tg?on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown'):"").">\n","<thead><tr>";for($hf=0;$hf<count($K);$hf++){$k=$I->fetch_field();$B=$k->name;$R=(isset($k->table)?$k->table:"");$oh=(isset($k->orgtable)?$k->orgtable:"");$nh=(isset($k->orgname)?$k->orgname:$B);$al=driver()->typeName($k);if($ph&&JUSH=="sql")$Gf[$hf]=($B=="table"?"table=":($B=="possible_keys"?"indexes=":null));elseif($oh!=""){$qa=($R!=""?$R:$oh);if($R!="")$J[$R]=$oh;if(!isset($v[$qa])){if(!isset($li[$oh])){$li[$oh]=array();foreach(indexes($oh,$f)as$u){if($u["type"]=="PRIMARY"){$li[$oh]=array_flip($u["columns"]);break;}}}$T[$qa]=$oh;$v[$qa]=$li[$oh];$d[$qa]=$li[$oh];}if(isset($d[$qa][$nh])){unset($d[$qa][$nh]);$v[$qa][$nh]=$hf;$Gf[$hf]=$qa;}elseif($tg&&isset($k->orgname)&&$k->db==DB&&!is_blob(array("type"=>$al)))$Jc[$hf]=array($qa,$nh,preg_match('~text|json|lob~',$al));}$bl[$hf]=$al;echo"<th title='".h(trim(($oh!=""?"$oh.$nh":($k->name!=$nh?$nh:""))." ".$al))."'>".h($B).($ph?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($B),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}foreach($Jc
as$hf=>$Za){if($d[$Za[0]])unset($Jc[$hf]);}echo"<tbody>\n";}$ze=array();foreach($v
as$qa=>$u){if($u&&!$d[$qa]){$t="";foreach($u
as$ob=>$hf){if($K[$hf]===null){$t=null;break;}$t
.="&where[".url_escape(bracket_escape($ob))."]=".url_escape($K[$hf]);}$ze[$qa]=$t;}}echo"<tr>";foreach($K
as$w=>$X){$z="";if(isset($Gf[$w])){if($ph&&JUSH=="sql"){$R=$K[array_search("table=",$Gf)];$z=ME.$Gf[$w].url_escape($ph[$R]!=""?$ph[$R]:$R);}elseif(idx($ze,$Gf[$w])!==null)$z=ME."edit=".url_escape($T[$Gf[$w]]).$ze[$Gf[$w]];}$b="";$Za=idx($Jc,$w);if($Za&&idx($ze,$Za[0])!==null&&is_utf8($X)){$Hc=true;$b=" data-name='".h("val[".bracket_escape($T[$Za[0]])."][".bracket_escape(substr($ze[$Za[0]],1))."][".bracket_escape($Za[1])."]")."' data-text='".($Za[2]?1:0)."'";}$X=select_value($X,$z,array('type'=>(preg_match('~binary~',$bl[$w])?'blob':$bl[$w])),null);echo"<td".(preg_match(number_type(),$bl[$w])?" class='number'":"")."$b>$X";}}$y=$r;echo($r?"</table>\n</div>":"<p class='message'>".lang(15))."\n";return$J;}function
textarea($B,$Y,$L=10,$sb=80,$jf=JUSH){echo"<textarea name='".h($B)."' rows='$L' cols='$sb' class='sqlarea jush-".h($jf)."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$C,$Y="",$Vh=""){if($C&&$Y!=""&&!isset($C[$Y]))$C=array($Y=>$Y)+$C;$sk=($C?"select":"input");return"<$sk$b".($C?"><option value=''>$Vh".optionlist($C,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$Vh'>");}function
json_row($w,$X=null,$Xc=true){static$Ad=true;if($Ad)echo"{";if($w!=""){echo($Ad?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($Xc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Ad=false;}else{echo"\n}\n";$Ad=true;}}function
flat_collations(){$rb=collations();return(is_array(reset($rb))?call_user_func_array('array_merge',array_values($rb)):$rb);}function
edit_type($w,array$k,array$rb,array$Gd=array(),array$nd=array()){$U=(string)$k["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($Gd[$U])&&!in_array($U,$nd))$nd[]=$U;$Uj=driver()->structuredTypes();if($Gd)$Uj[lang(118)]=$Gd;echo
optionlist(array_merge($nd,$Uj),$U),"</select><td>","<input name='".h($w)."[length]' value='".h($k["length"])."' size='3'".(!$k["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($rb?"<input list='collations' name='".h($w)."[collation]'".option_types($U,'('.text_type().')$')." value='".h($k["collation"])."' placeholder='(".lang(119).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$k["unsigned"]).'</select>':''),(isset($k['on_update'])?"<select name='".h($w)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(120).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$k["on_update"])?"CURRENT_TIMESTAMP":$k["on_update"])).'</select>':''),($Gd?"<select name='".h($w)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(121).")".optionlist(explode("|",driver()->onActions),$k["on_delete"])."</select> ":" ");}function
option_types($U,$bl){return" data-types='".h($bl)."'".(preg_match("~$bl~",$U)?"":" class='hidden'");}function
process_length($x){if(JUSH=="mssql"&&preg_match('~^\s*\(?\s*max\s*\)?\s*$~i',$x))return"(max)";$Sc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Sc(?:\\s*,\\s*$Sc)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$Sc~",$x,$Of)?"(".implode(",",$Of[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_in($X){$Sc=driver()->enumLength;if(preg_match("~^\\s*\\(?\\s*$Sc(?:\\s*,\\s*$Sc)*+\\s*\\)?\\s*\$~",$X)&&preg_match_all("~$Sc~",$X,$Of))return"(".implode(", ",$Of[0]).")";$J=array();foreach(explode(",",$X)as$gf)$J[]=q(trim($gf));return"(".implode(", ",$J).")";}function
process_type(array$k,$pb="COLLATE"){return" $k[type]".process_length($k["length"]).(preg_match(number_type(),$k["type"])&&in_array($k["unsigned"],driver()->unsigned)?" $k[unsigned]":"").(preg_match('~'.text_type().'~',$k["type"])&&$k["collation"]?" $pb ".(JUSH=="mssql"?$k["collation"]:q($k["collation"])):"");}function
process_field(array$k,array$Yk){if($k["on_update"])$k["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$k["on_update"]);return
array(idf_escape(trim($k["field"])),process_type($Yk),($k["null"]?" NULL":" NOT NULL"),default_value($k),(preg_match('~timestamp|datetime~',$k["type"])&&$k["on_update"]?" ON UPDATE $k[on_update]":""),(support("comment")&&$k["comment"]!=""?" COMMENT ".q($k["comment"]):""),($k["auto_increment"]?auto_increment():null),);}function
default_value(array$k){if($k["default"]===null)return"";$i=str_replace("\r","",$k["default"]);$Rd=$k["generated"];return(in_array($Rd,driver()->generated)?(JUSH=="mssql"?" AS ($i)".($Rd=="VIRTUAL"?"":" $Rd"):" GENERATED ALWAYS AS ($i) $Rd"):(preg_match('~^GENERATED ~i',$i)?" $i":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set|String~',$k["type"])||preg_match('~^(?![a-z])~i',$i)?(JUSH=="sql"&&preg_match('~text|json~',$k["type"])?"(".q($i).")":q($i)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($i)":$i)))));}function
edit_fields(array$l,array$rb,$U="TABLE",array$Gd=array()){$l=array_values($l);$ec=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$wb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(122):lang(123)),"<th id='label-type'>".lang(48)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<th id='label-length'>".lang(124),"<th>".lang(125);if($U=="TABLE")echo"<th id='label-null'>NULL\n","<th><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(50)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<th id='label-default'$ec>".lang(51),(support("comment")?"<th id='label-comment'$wb>".lang(49):"");$vf=!support("move_col");echo"<td>".icon("plus","add[".($vf?count($l):0)."]","+",lang(126),($vf?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($l
as$r=>$k){$r++;$qh=$k[($_POST?"orig":"field")];$rc=(isset($_POST["add"][$r-1])||(isset($k["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$qh=="");echo"<tr".($rc?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$k["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(127))." ":"");if($rc)echo"<input name='fields[$r][field]' value='".h($k["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$qh);edit_type("fields[$r]",$k,$rb,$Gd);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$r][null]",1,$k["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($k["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$ec>".(driver()->generated?html_select("fields[$r][generated]",array_merge(array("","DEFAULT"),driver()->generated),$k["generated"])." ":checkbox("fields[$r][generated]",1,$k["generated"],"","","","label-default"));$b=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($k["default"]);echo(preg_match('~\n~',$k["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$r][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$wb>".adminer()->commentInput('COLUMN',$b,$k["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(126))." ":""),($qh==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(128)):"");}}function
process_fields(array&$l){if($_POST["add"]){$l=array_values($l);array_splice($l,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
drop_create($Cc,$Kb,$Dc,$yk,$Ec,$_,$jg,$hg,$ig,$Yg,$Fg){if($_POST["drop"])query_redirect($Cc,$_,$jg);elseif($Yg=="")query_redirect($Kb,$_,$ig);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($_,$hg,queries($Cc)&&queries($Kb)&&driver()->commit());driver()->rollback();}elseif($Yg!=$Fg){$Mb=queries($Kb);queries_redirect($_,$hg,$Mb&&queries($Cc));if($Mb&&$Dc)queries($Dc);}else
queries_redirect($_,$hg,queries($yk)&&queries($Ec)&&queries($Cc)&&queries($Kb));}function
create_trigger($bh,array$K){$Dk=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$bh.$Dk:$Dk.$bh).preg_replace('~[\s;]+$~',''," $K[Type]\n$K[Statement]").";";}function
q_dollar($Q){$jc='$$';while(strpos($Q.$jc,$jc)!=strlen($Q))$jc='$_'.substr($jc,1);return$jc.$Q.$jc;}function
routine_collate($qb){static$eb=array();if($qb&&!$eb){foreach(collations()as$db=>$Bl){foreach((array)$Bl
as$X)$eb[$X]=$db;}}return($eb[$qb]?"CHARACTER SET ".q($eb[$qb])." ":"")."COLLATE";}function
create_routine($Ti,array$K){$O=array();$l=$K["fields"];ksort($l);foreach($l
as$k){if($k["field"]!=""){$Oe=(preg_match("~^(".driver()->inout.")\$~",$k["inout"])?$k["inout"]:"");$O[]="\n  ".(JUSH=="mssql"?"@$k[field]".process_type($k).($Oe?" $Oe":""):($Oe?"$Oe ":"").idf_escape($k["field"]).process_type($k,routine_collate($k["collation"])));}}$gc="";$C=array();foreach(routine_options($Ti)as$w=>$Cl){$Y=idx($K["options"],$w,"");if($w=="DEFINER")$gc=($Y?" $w=".implode("@",array_map('Adminer\q',explode("@",$Y,2))):"");elseif(!$Cl){if($Y!="")$C[]="$w ".q($Y);}elseif($Y!=reset($Cl)&&in_array($Y,$Cl))$C[]=$Y;}$tf=$K["language"];$hc=preg_replace('~[\s;]+$~','',$K["definition"]);$zc=(JUSH=="pgsql"||($tf&&$tf!="sql"));$Bh=($O?implode(",",$O)."\n":"");return"CREATE$gc $Ti ".table(trim($K["name"])).(JUSH=="mssql"&&$Ti=="PROCEDURE"?rtrim($Bh):" ($Bh)").($Ti=="FUNCTION"?"\nRETURNS".process_type($K["returns"],routine_collate($K["returns"]["collation"])):"").($tf?" LANGUAGE $tf":"").($C?"\n".implode(" ",$C):"").($zc?" AS ".q_dollar("\n".trim($hc)."\n"):(JUSH=="mssql"?"\nAS":"")."\n$hc;");}function
remove_definer($H){$gc=implode("@",array_map('Adminer\idf_escape',explode("@",logged_user(),2)));return
preg_replace('(^([A-Z =]+) DEFINER='.preg_quote($gc).')','\1',$H);}function
format_foreign_key(array$n){$h=$n["db"];$Mg=$n["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($h!=""&&$h!=$_GET["db"]?idf_escape($h).".":"").($Mg!=""&&$Mg!=$_GET["ns"]?idf_escape($Mg).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($m,$Ik){$J=pack("a100a8a8a8a12a12",$m,644,0,0,decoct($Ik->size),decoct(time()));$jb=8*32;for($r=0;$r<strlen($J);$r++)$jb+=ord($J[$r]);$J
.=sprintf("%06o",$jb)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$Ik->send();echo
str_repeat("\0",511-($Ik->size+511)%512);}function
doc_version(){$uj=connection()->server_info;if(JUSH=='oracle'){preg_match('~(?:.* |^)(\d+)\.\d+\.\d+\.\d+\.\d+~s',$uj,$A);return($A[1]>=18?$A[1]:"19");}$Fi=(JUSH=='sql'||connection()->flavor=='cockroach'?'~^\d+\.\d+~':'~^\d\.?\d~');$Fl=(preg_match($Fi,$uj,$A)?$A[0]:"");if(JUSH=='mssql')return($Fl>=15?"sql-server-ver$Fl":($Fl==12?"azuresqldb-current":"sql-server-2017"));return$Fl;}function
doc_link(array$Rh,$zk="<sup>?</sup>"){$Fl=doc_version();$rl=array('sql'=>"https://dev.mysql.com/doc/refman/$Fl/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Fl)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://docs.oracle.com/en/database/oracle/oracle-database/$Fl/",);if(connection()->flavor=='maria'){$rl['sql']="https://mariadb.com/kb/en/";$Rh['sql']=(isset($Rh['mariadb'])?$Rh['mariadb']:str_replace(".html","/",$Rh['sql']));}if(connection()->flavor=='cockroach'&&isset($Rh['cockroach'])){$rl['pgsql']="https://docs.cockroachlabs.com/docs/v$Fl/";$Rh['pgsql']=$Rh['cockroach'];}return($Rh[JUSH]?"<a href='".h($rl[JUSH].$Rh[JUSH].(JUSH=='mssql'?"?view=$Fl":""))."'".target_blank().">$zk</a>":"");}function
db_size($h){if(!connection()->select_db($h))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($Kb){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$Kb)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!="")page_header(lang(34).": ".h(DB),adminer()->error(),true,"","db");else{if(!isset($_GET["db"])&&support("single_db")){$g=adminer()->databases();if($g)redirect(ME."db=".url_escape($g[0]));}if($_POST["db"]&&!$j)queries_redirect(substr(ME,0,-1),lang(129),drop_databases($_POST["db"]));page_header(lang(130),$j,false);echo"<p class='links'>\n";foreach(array('database'=>lang(131),'privileges'=>lang(70),'processlist'=>lang(132),'variables'=>lang(133),'status'=>lang(134),)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".lang(135,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(136,"<b>".h(logged_user())."</b>")."\n";$g=adminer()->databases();if($g){$ej=support("scheme");$rb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(34).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(137)."</a>":"")."<th>".lang(138)."<th>".lang(139)."<th>".lang(140)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(141)."</a>"."<tbody>\n";$g=($_GET["dbsize"]?count_tables($g):array_flip($g));foreach($g
as$h=>$T){$Si=h(preg_replace('~&db=[^&]*~','',ME))."db=".url_escape($h);$s=h("Db-".$h);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$h,in_array($h,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$Si' id='$s'>".h($h)."</a>";$qb=h(db_collation($h,$rb));echo"<td>".(support("database")?"<a href='$Si".($ej?"&amp;ns=":"")."&amp;database=' title='".lang(66)."'>$qb</a>":$qb),"<td align='right'><a href='$Si&amp;schema=' id='tables-".h($h)."' title='".lang(69)."'>".($_GET["dbsize"]?format_number($T):"?")."</a>","<td align='right' id='size-".h($h)."'>".($_GET["dbsize"]?db_size($h):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(142)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(143)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ka=adminer();$Zh=($ka
instanceof
Plugins?$ka->plugins:array());$Bc=($ka
instanceof
Plugins?$ka->drivers:array());$oc=design_checksums();if($Zh||$Bc||$oc){$kb=($ka
instanceof
Plugins?$ka->checksums():array());$Rg=Plugins::officialChecksums();$ml=function($ql){return" (<a href='$ql'".target_blank()." class='update'>".VERSION."</a>)";};$Yh=function($vd)use($kb,$Rg,$ml){return($kb[$vd]&&$Rg[$vd]&&$kb[$vd]!==$Rg[$vd]?$ml("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(144)."</h3>\n<ul>\n";foreach($Zh
as$Wh){$Di=new
\ReflectionObject($Wh);$lc=(method_exists($Wh,'description')?$Wh->description():"");if(!$lc){if(preg_match('~^/[\s*]+(.+)~',$Di->getDocComment(),$A))$lc=$A[1];}$fj=(method_exists($Wh,'screenshot')?$Wh->screenshot():"");echo"<li><b>".get_class($Wh)."</b>".h($lc?": $lc":"").($fj?" (<a href='".h($fj)."'".target_blank().">".lang(145)."</a>)":"").$Yh(basename((string)$Di->getFileName(),'.php'))."\n";}foreach($Bc
as$s=>$B)echo"<li><b>".h($s)."</b>: ".h($B).$Yh(basename((string)$ka->driverFiles[$s],'.php'))."\n";if($oc){$Tg=official_design_checksums();foreach($oc
as$m=>$nc){list($B,$jb)=$nc;$Sg=$Tg["$B/$m"];echo"<li><b>".h($m)."</b>".h($B?": $B":"").($Sg&&$Sg!==$jb?$ml("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($Db){$this->size+=strlen($Db);fwrite($this->handler,$Db);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if($_GET["select"]!=""&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$l=fields($a);header("Content-Type: application/octet-stream");$Cl=array_merge((array)$_GET["where"],(array)$_GET["val"]);header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$Cl)).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$l)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$l[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$l=fields($a);if(!$l)$j=adminer()->error();$S=table_status1($a);$B=adminer()->tableName($S);$j=$j?:h($S["Error"]);page_header(($l&&is_view($S)?$S['Engine']=='materialized view'?lang(146):lang(147):lang(148)).": ".($B!=""?$B:h($a)),$j,array(),"",!$l);$Ri=array();foreach($l
as$w=>$k)$Ri+=$k["privileges"];adminer()->selectLinks($S,(isset($Ri["insert"])||!support("table")?"":null));$vb=$S["Comment"];if($vb!="")echo"<p class='nowrap'>".lang(49).": ".adminer()->commentValue('TABLE',$vb)."\n";if($l)adminer()->tableStructurePrint($l,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$z=preg_replace('~ns=[^&]*~',"ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($z."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$Me=driver()->inheritsFrom($a);if($Me){echo"<h3>".lang(149)."</h3>\n";tables_links($Me);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(150)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(151)."</a>\n";echo"</div>\n";}if(!is_view($S)&&driver()->supportsAlterTable($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(118)."</h3>\n";$Gd=foreign_keys($a);if($Gd){echo"<table>\n","<thead><tr><th>".lang(152)."<th>".lang(153)."<th>".lang(121)."<th>".lang(120)."<td class='hover'><tbody>\n";foreach($Gd
as$B=>$n){echo"<tr title='".h($B)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$z=($n["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($n["db"]),ME):($n["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($n["ns"]),ME):ME));echo"<td><a href='".h($z."table=".url_escape($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($B)).'">'.lang(154).'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(155)."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(156)."</h3>\n";$gb=driver()->checkConstraints($a);if($gb){echo"<table>\n";foreach($gb
as$w=>$X)echo"<tr title='".h($w)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($w))."'>".lang(154)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(157)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")&&driver()->supportsAlterTable($S)){echo"<div>\n","<h3 id='triggers'>".lang(158)."</h3>\n";$Vk=triggers($a);if($Vk){echo"<table>\n";foreach($Vk
as$w=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($w))."'>".lang(154)."</a>\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(159)."</a>\n","</div>\n";}$zj=driver()->shadowTables($a);if($zj){echo"<h3 id='shadow-tables'>".lang(160)."</h3>\n";tables_links($zj);}$Le=driver()->inheritedTables($a);if($Le){echo"<h3 id='partitions'>".lang(161)."</h3>\n";$Fh=driver()->partitionsInfo($a);if($Fh)echo"<p><code class='jush-".JUSH."'>BY ".h("$Fh[partition_by]($Fh[partition])")."</code>\n";tables_links($Le);}}elseif(isset($_GET["schema"])){page_header(lang(69),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));function
schema_column($R,array$Ci,array&$d){if(!isset($d[$R])){$d[$R]=0;foreach((array)idx($Ci,$R)as$B=>$Ei){if($B!=$R)$d[$R]=max($d[$R],schema_column($B,$Ci,$d)+1);}}return$d[$R];}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(preg_match("~$w|$X~",$U))return" class='$w'";}}$jk=array();$lk=array();$kk=array();$sd=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Of,PREG_SET_ORDER);foreach($Of
as$r=>$A){$jk[$A[1]]=array((float)$A[2],(float)$A[3]);$lk[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$cj=array();$Ci=array();$Gd=array();$sa=driver()->allFields();$pe=array();$mk=array();foreach(table_status('',true)as$R=>$S){if(!is_view($S)){if(adminer()->tableName($S)!=""&&!$S["dependent"])$mk[$R]=$S;else$pe[$R]=true;}}foreach($mk
as$R=>$S){$G=0;$cj[$R]["fields"]=array();foreach($sa[$R]as$k){$G+=1.25;$sd[$R][$k["field"]]=$G;$cj[$R]["fields"][$k["field"]]=$k;}foreach(adminer()->foreignKeys($R)as$X){if($X["db"]==""&&$X["ns"]==""&&!$pe[$X["table"]]){$Gd[$R][]=$X;$Ci[$X["table"]][$R]=array();}}}$d=array();$Vd=array();$Rl=array();$ae=array();foreach(array_keys($cj)as$B)schema_column($B,$Ci,$d);arsort($d);foreach($d
as$B=>$c){$pg=null;foreach((array)idx($Gd,$B)as$X){if($X["table"]!=$B&&$cj[$X["table"]])$pg=($pg===null?$d[$X["table"]]:min($pg,$d[$X["table"]]));}$d[$B]=max($c,(int)$pg-1);}foreach($cj
as$B=>$R){$c=$d[$B];$Vd[$c][]=$B;$Ak=.75*strlen($B);foreach($R["fields"]as$k)$Ak=max($Ak,.65*strlen($k["field"]));$Rl[$c]=max(idx($Rl,$c,0),ceil($Ak)+1);}foreach($Gd
as$B=>$Bl){foreach($Bl
as$X){$Zd=$d[$B]+(idx($d,$X["table"],$d[$B])>$d[$B]?1:0);$ae[$Zd]=idx($ae,$Zd,0)+1;}}ksort($Vd);$ne=0;$Ql=0;$tb=0;$ji=null;$hk=array();$ok=array();foreach($Vd
as$c=>$T){if($ji!==null){$tb=round($tb+$Rl[$ji]+1.7+idx($ae,$c,0)*.1,1);$D=array();foreach($T
as$B){$Zj=0;$Jb=0;$Cg=array_keys((array)idx($Ci,$B));foreach((array)idx($Gd,$B)as$X)$Cg[]=$X["table"];foreach($Cg
as$_g){if($cj[$_g]&&$d[$_g]<$c){$Zj+=$cj[$_g]["pos"][0];$Jb++;}}$D[$B]=($Jb?$Zj/$Jb:$ne);}asort($D);$T=array_keys($D);}$Lk=0;foreach($T
as$B){$G=1.25*count($cj[$B]["fields"]);$cj[$B]["pos"]=($jk[$B]?:array($Lk,$tb));$hk[$B]=$cj[$B]["pos"][1];$ok[$B]=$Rl[$c];$Lk+=2.5+$G;$ne=max($ne,$cj[$B]["pos"][0]+2.5+$G);$Ql=max($Ql,round($cj[$B]["pos"][1]+$Rl[$c],1));if(!$jk[$B])$kk[]="\n\t'".js_escape($B)."': [ ".$cj[$B]["pos"][0].", ".$cj[$B]["pos"][1]." ]";}$ji=$c;}$zf=array();$Na=array();foreach($Gd
as$B=>$Bl){foreach($Bl
as$X){$uk=idx($hk,$X["table"],$hk[$B]);$Ij=$hk[$B]+$ok[$B];$Qi=($uk-1>$Ij);$xf=($Qi?$Ij+1:min($hk[$B],$uk)-1);$Ma=idx($Na,(string)$xf,0);$Na[(string)$xf]=$Ma+1;$xf=round($Qi?min($xf+$Ma*.1,$uk-1):$xf-$Ma*.1,1);while($zf[(string)$xf])$xf-=.0001;$cj[$B]["references"][$X["table"]][(string)$xf]=array($X["source"],$X["target"]);$Ci[$X["table"]][$B][(string)$xf]=$X["target"];$zf[(string)$xf]=true;}}echo'<div id="schema" style="height: ',$ne,'em; width: ',$Ql,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$lk)."\n",'};
const tablePosDefault = {',implode(",",$kk)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$ne,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($cj
as$B=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em; width: ".$ok[$B]."em;'>",'<a href="'.h(ME).'table='.url_escape($B).'"><b>'.h($B)."</b></a>";foreach($R["fields"]as$k){$X='<span'.type_class($k["type"]).' title="'.h($k["type"].($k["length"]?"($k[length])":"").($k["null"]?" NULL":'')).'">'.h($k["field"]).'</span>';echo"<br>".($k["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$vk=>$Ei){foreach($Ei
as$xf=>$_i){$yf=$xf-$R["pos"][1];$Wj=($yf>0?"left: 100%; width: calc($yf"."em - 100%)":"left: $yf"."em");$Ql=($yf>0?"100%":(-$yf)."em");$r=0;foreach($_i[0]as$Hj)echo"\n<div class='references' title='".h($vk)."' id='refs$xf-".($r++)."' style='$Wj"."; top: ".$sd[$B][$Hj]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: $Ql;'></div></div>";}}foreach((array)$Ci[$B]as$vk=>$Ei){foreach($Ei
as$xf=>$wk){$yf=$xf-$R["pos"][1];$r=0;foreach($wk
as$tk)echo"\n<div class='references arrow' title='".h($vk)."' id='refd$xf-".($r++)."' style='left: $yf"."em; top: ".$sd[$B][$tk]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$yf)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($cj
as$B=>$R){foreach((array)$R["references"]as$vk=>$Ei){if($cj[$vk]){foreach($Ei
as$xf=>$_i){$qg=$ne;$Wf=-10;foreach($_i[0]as$w=>$Hj){$bi=$R["pos"][0]+$sd[$B][$Hj];$ci=$cj[$vk]["pos"][0]+$sd[$vk][$_i[1][$w]];$qg=min($qg,$bi,$ci);$Wf=max($Wf,$bi,$ci);}echo"<div class='references' id='refl$xf' style='left: $xf"."em; top: $qg"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Wf-$qg)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(162),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$j){$i=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$bk){if(support($bk))$i[$bk."s"]='';}save_settings(array_intersect_key($_POST+$i,array_flip(array("output","format","db_style","schema_style","table_style","data_style"))+$i),"adminer_export");$ra=(DB==""||$_GET["ns"]==="");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$jd=dump_headers((count($T)==1?key($T):DB),($ra||count($T)>1));$df=preg_match('~sql~',$_POST["format"]);if($df){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Wj=$_POST["db_style"];$g=array(DB);if(DB==""){$g=$_POST["databases"];if(is_string($g))$g=explode("\n",rtrim(str_replace("\r","",$g),"\n"));}foreach((array)$g
as$h){adminer()->dumpDatabase($h);if(connection()->select_db($h)){if($df&&$Wj)echo
use_sql($h,$Wj).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$cj){if($cj!=""){if(DB==""&&information_schema(DB,$cj))continue;set_schema($cj);}if($df&&$_POST["schema_style"]&&function_exists('Adminer\use_schema_sql'))echo
use_schema_sql($_GET["ns"],$_POST["schema_style"]).";\n\n";$Sj=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$id=array();$Wb=array();foreach($Sj
as$B=>$S){if($ra||in_array($B,(array)$_POST["tables"]))$id[$B]=$S;if($ra||in_array($B,(array)$_POST["data"]))$Wb[$B]=$S;}if($df){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($id);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$Wk=array();foreach($Wb
as$B=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($id[$B])))$Wk[]=$B;}echo
truncate_all_sql($Wk);}$xh="";if($_POST["types"]){foreach(types()as$s=>$U){$hc=type_definition($s);$Pg=($hc["kind"]=='d'?"DOMAIN":"TYPE");if($hc["definition"])$xh
.=($Wj!='DROP+CREATE'?"DROP $Pg IF EXISTS ".table($U).";;\n":"")."CREATE $Pg ".table($U)." $hc[definition];\n\n";else$xh
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$B=$K["ROUTINE_NAME"];$Ti=$K["ROUTINE_TYPE"];$Kb=create_routine($Ti,array("name"=>$B)+routine($K["SPECIFIC_NAME"],$Ti));set_utf8mb4($Kb);$xh
.=($Wj!='DROP+CREATE'?"DROP $Ti IF EXISTS ".table($B).";;\n":"")."$Kb;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$Kb=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($Kb);$xh
.=($Wj!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$Kb;;\n\n";}}echo($xh&&JUSH=='sql'?"DELIMITER ;;\n\n$xh"."DELIMITER ;\n\n":$xh);}if($_POST["table_style"]||$_POST["data_style"]){$Hl=array();foreach($Sj
as$B=>$S){$R=array_key_exists($B,$id);$Ub=array_key_exists($B,$Wb);if($R||$Ub){$Ik=null;if($jd=="tar"){$Ik=new
TmpFile;ob_start(array($Ik,'write'),1e5);}adminer()->dumpTable($B,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$Hl[]=$B;elseif($Ub){$l=fields($B);$M=array("*");$Gb=convert_fields($l,$l);if($Gb)$M[]=substr($Gb,2);adminer()->dumpData($B,$_POST["data_style"],"",$M);}if($df&&$_POST["triggers"]&&$R&&($Vk=trigger_sql($B)))echo"\nDELIMITER ;;\n$Vk\nDELIMITER ;\n";if($jd=="tar"){ob_end_flush();tar_file((DB!=""?"":"$h/")."$B.csv",$Ik);}elseif($df)echo"\n";}}if($df&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($id
as$B=>$S){if(!is_view($S))echo
foreign_keys_sql($B);}}if($df){foreach($Hl
as$Gl)adminer()->dumpTable($Gl,$_POST["table_style"],1);}if($jd=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(75),$j,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$ac=array('','USE','DROP+CREATE','CREATE');$dj=(JUSH=="mssql"?array('','DROP+CREATE','CREATE'):$ac);$nk=array('','DROP+CREATE','CREATE');$Vb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Vb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"schema_style"=>"","table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(163)."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".lang(164)."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(34)."<td>".html_select('db_style',$ac,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],lang(7)):"").(support("routine")?checkbox("routines",1,$K["routines"],lang(71)):"").(support("event")?checkbox("events",1,$K["events"],lang(73)):"")),(function_exists('Adminer\use_schema_sql')?"<tr><th>".lang(165)."<td>".html_select('schema_style',$dj,$K["schema_style"]):""),"<tr><th>".lang(139)."<td>".html_select('table_style',$nk,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],lang(50)).(support("trigger")?checkbox("triggers",1,$K["triggers"],lang(158)):""),"<tr><th>".lang(166)."<td>".html_select('data_style',$Vb,$K["data_style"]),'</table>
';adminer()->dumpPrint();echo'<p><input type=\'submit\' value=\'',lang(75),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$ii=array();if($_GET["ns"]===""&&support("scheme")){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(167)."'".on('click','formCheck','^schemas\[').">".lang(165)."</label>","<tbody>\n";foreach(adminer()->schemas()as$cj){if(!information_schema(DB,$cj))echo"<tr><td>".checkbox("schemas[]",$cj,true,$cj,"","block")."\n";}}elseif(DB!=""){$hb=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$hb class='jsonly' title='".lang(167)."'".on('click','formCheck','^tables\[').">".lang(148)."</label>","<th style='text-align: right;'><label class='block'>".lang(166)."<input type='checkbox' id='check-data'$hb class='jsonly' title='".lang(167)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$Hl="";$qk=tables_list();foreach($qk
as$B=>$U){$hi=preg_replace('~_.*~','',$B);$hb=($a==""||$a==(substr($a,-1)=="%"?"$hi%":$B));$mi="<tr><td>".checkbox("tables[]",$B,$hb,$B,"","block");if($U!==null&&!preg_match('~table~i',$U))$Hl
.="$mi\n";else
echo"$mi<td align='right'><label class='block'><span id='Rows-".h($B)."'></span>".checkbox("data[]",$B,$hb)."</label>\n";$ii[$hi]++;}echo$Hl;if($qk)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$g=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($g?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(167)."'".on('click','formCheck','^databases\[').">":"").lang(34)."</label>","<tbody>\n";if($g){foreach($g
as$h){if(!information_schema($h)){$hi=preg_replace('~_.*~','',$h);echo"<tr><td>".checkbox("databases[]",$h,$a==""||$a=="$hi%",$h,"","block")."\n";$ii[$hi]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Ad=true;foreach($ii
as$w=>$X){if($w!=""&&$X>1){echo($Ad?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$w%")."'>".h($w)."</a>";$Ad=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(70));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(168)."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Td=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Td?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(32)."<th>".lang(30)."<td class='hover'><tbody>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"]),"<td>".h($K["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($K["User"]).'&host='.url_escape($K["Host"])).'">'.lang(13)."</a>\n";if(!$Td||DB!="")echo"<tr><td><input name='user' autocapitalize='off'>","<td><input name='host' value='localhost' autocapitalize='off'>","<td class='hover'><input type='submit' value='".lang(13)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$j&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}if(!$j&&$_POST["val"]){$na=0;$Xj=true;$ab=array();$Zi=0;foreach($_POST["val"]as$L)$Zi+=count($L);$Pa=$Zi>1&&driver()->begin();foreach($_POST["val"]as$fk=>$L){$R=bracket_escape($fk,true);$l=fields($R);$gk=indexes($R);foreach($L
as$t=>$K){parse_str(bracket_escape($t,true),$Z);$el=array();foreach($Z["where"]as$w=>$X)$el[bracket_escape($w,true)]=$X;if(!$l||$Z["null"]||array_diff_key($el,$l)||!unique_array($el,$gk)){$Xj=false;break
2;}$O=array();$M=array();foreach($K
as$lf=>$X){$w=bracket_escape($lf,true);$k=idx($l,$w);if(!$k){$Xj=false;break
3;}$O[idf_escape($w)]=(preg_match('~char|text~',$k["type"])||$X!=""?adminer()->processInput($k,$X):"NULL");$M[$lf]=$w;}$wi=where($Z,$l);if(!driver()->update($R,$O," WHERE $wi",0," ")){$Xj=false;break
2;}$na+=connection()->affected_rows;$d=array();foreach($M
as$w)$d[]=idf_escape($w);$nl=driver()->select($R,$d,array($wi),$d);$Gg=($nl?$nl->fetch_row():array());$hf=0;foreach($M
as$lf=>$w){$k=$l[$w];$Vj=array('type'=>(preg_match('~binary~',$k["type"])?'blob':$k["type"]));$ab["val[$fk][$t][$lf]"]=select_value(idx($Gg,$hf++),"",$Vj,null);}}}if($Pa&&$Xj)$Xj=driver()->commit();queries_redirect(null,lang(169,$na),$Xj);if($Pa&&!$Xj)driver()->rollback();page_headers();page_messages($j);foreach($ab
as$B=>$X)echo"<div data-name='".h($B)."' hidden>$X</div>\n";exit;}restart_session();$re=&get_session("queries");$qe=&$re[DB];if(!$j&&$_POST["clear"]){$qe=array();redirect(remove_from_uri("history"));}stop_session();$la=get_settings("adminer_import");if($_POST&&$la)save_settings($la,"adminer_import");page_header((isset($_GET["import"])?lang(74):lang(63)),$j);$Ff=driver()->lineComment();if(!$j&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$jc=driver()->delimiter;$o=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Lj=adminer()->importServerPath();$o=@fopen((file_exists($Lj)?$Lj:"compress.zlib://$Lj.gz"),"rb");$H=($o?fread($o,1e6):false);}else$H=get_file("sql_file",true,$jc);if(is_string($H)){if(($eg=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($eg,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$ti=$H.(preg_match("~$jc\\s*\$~",$H)?"":$jc);if(!$qe||first(end($qe))!=$ti){restart_session();$qe[]=array($ti,time());set_session("queries",$re);stop_session();}}$Jj="(?:\\s|/\\*[\s\S]*?\\*/|(?:$Ff)[^\n]*\n?|--\r?\n)";$Ug=0;$Oc=true;$Ib=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$ub=0;$Vc=array();$Dh='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Ff.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$Mk=microtime(true);while($H!=""){if(!$Ug&&preg_match("~^$Jj*+DELIMITER\\s+(\\S+)~i",$H,$A)){$jc=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$Ug&&JUSH=='pgsql'&&preg_match("~^($Jj*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$jc="\n\\\\\\.\r?\n";$Ib=true;$Ug=strlen($A[0]);}else{preg_match("($jc\\s*|$Dh)",$H,$A,PREG_OFFSET_CAPTURE,$Ug);list($Id,$G)=$A[0];if(!$Id&&$o&&!feof($o))$H
.=fread($o,1e5);else{if(!$Id&&rtrim($H)=="")break;$Ug=$G+strlen($Id);if($Id&&!preg_match("(^$jc)",$Id)){$Xa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$Sh=($Id=='/*'?'\*/':($Id=='['?']':(preg_match("~^(?:$Ff)~",$Id)?"\n":preg_quote($Id).($Xa?'|\\\\.':''))));while(preg_match("($Sh|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$Ug)){$aj=$A[0][0];if(!$aj&&$o&&!feof($o))$H
.=fread($o,1e5);else{$Ug=$A[0][1]+strlen($aj);if(!$aj||$aj[0]!="\\")break;}}}else{$ti=substr($H,0,$G+($Ib?3:0));$H=substr($H,$Ug);$Ug=0;if($Ib){$jc=driver()->delimiter;$Ib=false;}$nb="<code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($ti)."</code>";if(preg_match("~^$Jj*+\$~",$ti)&&!preg_match('~/\*M?!~',$ti)){echo($_POST["only_errors"]?"":"<pre>$nb</pre>\n");continue;}$Oc=false;$ub++;$mi="<pre id='sql-$ub'>$nb</pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Jj*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$ti,$A)!==0){echo$mi,"<p class='error'>".lang(170,preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$Vc[]=" <a href='#sql-$ub'>$ub</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$mi;ob_flush();flush();}$Qj=microtime(true);if(connection()->multi_query($ti)&&$f&&preg_match("~^$Jj*+USE\\b~i",$ti))$f->query($ti);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$mi:""),"<p class='error'>".lang(171).(connection()->errno?" (".connection()->errno.")":"").": ".adminer()->error()."\n";$Vc[]=" <a href='#sql-$ub'>$ub</a>";if($_POST["error_stops"])break
2;}else{$z=ME."sql=".url_escape(trim($ti));$Bk=" <span class='time'>(".format_time($Qj).")</span>".(strlen($z)<1900?" <a href='".h($z)."'>".lang(13)."</a>":"");$na=connection()->affected_rows;$Kl=($_POST["only_errors"]?"":driver()->warnings());$Ll="warnings-$ub";if($Kl)$Bk
.=", <a href='#$Ll' class='toggle'>".lang(45)."</a>";$gd="";$hd="explain-$ub";if(is_object($I)){$y=$_POST["limit"];$Ng=$y;$Hc=!$_POST["only_errors"];if($Hc)echo"<form action='' method='post'>\n";$ph=print_select_result($I,$f,array(),$Ng,$Hc);if(!$_POST["only_errors"]){$Ng=max($I->num_rows,$Ng);echo"<p class='sql-footer'>".($Ng?($y&&$Ng>$y?lang(172,$y):"").lang(173,$Ng):""),$Bk;if($f&&preg_match("~^($Jj|\\()*+SELECT\\b~i",$ti)&&($gd=adminer()->explain($f,$ti,$ph))!="")echo", <a href='#$hd' class='toggle'>Explain</a>";if($Hc)echo", <input type='submit' name='save' value='".lang(17)."' class='jsonly' disabled"." title='".lang(174)."'".on('click','sqlSave',lang(20)).">";$s="export-$ub";echo", <a href='#$s' class='toggle'>".lang(75)."</a><span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$la["output"])." ".html_select("format",adminer()->dumpFormat(),$la["format"]).input_hidden("query",$ti)."<input type='submit' name='export' value='".lang(75)."'".($y?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Jj*+(CREATE|DROP|ALTER)$Jj++(DATABASE|SCHEMA)\\b~i",$ti)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(175,$na)."$Bk\n";}echo($Kl?"<div id='$Ll' class='hidden'>\n$Kl</div>\n":""),($gd!=""?"<div id='$hd' class='hidden explain'>\n$gd</div>\n":"");}$Qj=microtime(true);}while(connection()->next_result());}}}}}if($Oc)echo"<p class='message'>".lang(176)."\n";else{$Ee=connection()->inTransaction();driver()->rollback();if($Ee)echo"<pre><code class='jush-".JUSH."'>ROLLBACK".(JUSH=="mssql"?" TRANSACTION":"")." -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(177,$ub-count($Vc))," <span class='time'>(".format_time($Mk).")</span>\n";elseif($Vc&&$ub>1)echo"<p class='error'>".lang(171).": ".implode("",$Vc)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"';$ol="";if(!isset($_GET["import"]))echo
on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"));else
echo
on_upload_progress($ol);echo'>
';$dd="<input type='submit' value='".lang(178)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$ti=$_GET["sql"];if($_POST)$ti=$_POST["query"];elseif($_GET["history"]=="all")$ti=$qe;elseif($_GET["history"]!="")$ti=idx($qe[$_GET["history"]],0);echo"<p>";textarea("query",$ti,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$dd\n",lang(179).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$be=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(180)."</legend><div>",($ol?input_hidden(ini_get("session.upload_progress.name"),$ol):""),"SQL$be: ".file_input(" name='sql_file[]' multiple","\n$dd"),($ol?" <progress class='jsonly hidden' max='1' value='0'></progress>":""),"</div></fieldset>\n";$Be=adminer()->importServerPath();if($Be)echo"<fieldset><legend>".lang(181)."</legend><div>",lang(182,"<code>".h($Be)."$be</code>")," <input type='submit' name='webfile' value='".lang(183)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(184))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(185))."\n",input_token();if(!isset($_GET["import"])&&$qe){print_fieldset("history",lang(186),$_GET["history"]!="");for($X=end($qe);$X;$X=prev($qe)){$w=key($qe);list($ti,$Bk,$Kc)=$X;echo'<div><a href="'.h(ME."sql=&history=$w").'" class="hover">'.lang(13)."</a>"." <span class='time' title='".@date('Y-m-d',$Bk)."'>".@date("H:i:s",$Bk)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$Ff).*~m",'',$ti))),80,"</code>").($Kc?" <span class='time'>($Kc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(187)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(188)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$l=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$l):""):where($_GET,$l));$ll=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($l
as$B=>$k){if((!$ll&&!isset($k["privileges"]["insert"]))||adminer()->fieldName($k)=="")unset($l[$B]);}if($_POST&&!$j&&!isset($_GET["select"])){$_=relative_uri((string)$_POST["referer"]);if($_POST["insert"])$_=($ll?null:relative_uri());elseif(!preg_match('~^.+&select=.+$~',$_))$_=ME."select=".url_escape($a);$v=indexes($a);$fl=unique_array($_GET["where"],$v);$wi="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_,lang(189),driver()->delete($a,$wi,$fl?0:1));else{$O=array();foreach($l
as$B=>$k){$X=process_input($k);if($X!==false&&$X!==null)$O[idf_escape($B)]=$X;}if($ll){if(!$O)redirect($_);queries_redirect($_,lang(190),driver()->update($a,$O,$wi,$fl?0:1));if(is_ajax()){page_headers();page_messages($j);exit;}}else{$I=driver()->insert($a,$O);$wf=($I?last_id($I):0);queries_redirect($_,lang(191,($wf?" $wf":"")),$I);}}}$K=null;$H="";$Bk="";if($Z){$M=array();$lj=array("*");foreach($l
as$B=>$k){if(isset($k["privileges"]["select"])){$Aa=($_POST["clone"]&&$k["auto_increment"]?"''":convert_field($k));$c=($Aa?"$Aa AS ":"").idf_escape($B);$M[]=$c;if($Aa)$lj[]=$c;}}$K=array();if(!support("table")){$M=array("*");$lj=$M;}if($M){$Qj=microtime(true);$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));$H=str_replace("SELECT ".implode(", ",$M),"SELECT ".implode(", ",$lj),driver()->query);$Bk=format_time($Qj);if(!$I)$j=adminer()->error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$l&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$w=>$X){if(!$Z)$K[$w]=null;$l[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"]){$di=array();foreach((array)$_POST["fields"]as$w=>$X)$di[bracket_escape($w,true)]=$X;$K=$di+($K?$K:array());}edit_form($a,$l,$K,$ll,$j,$H,$Bk);}elseif(isset($_GET["create"])){function
referencable_primary($nj){$J=array();foreach(table_status('',true)as$ik=>$R){if($ik!=$nj&&!$R["dependent"]&&fk_support($R)){foreach(fields($ik)as$k){if($k["primary"]){if($J[$ik]){unset($J[$ik]);break;}$J[$ik]=$k;}}}}return$J;}$a=$_GET["create"];$Hh=driver()->partitionBy;$Lh=($Hh&&$a!=""?driver()->partitionsInfo($a):array());$Bi=referencable_primary($a);$Gd=array();foreach($Bi
as$ik=>$k)$Gd[str_replace("`","``",$ik)."`".str_replace("`","``",$k["field"])]=$ik;$sh=array();$S=array();$Lg=false;if($a!=""){$sh=fields($a);$S=table_status1($a);$Lg=(count($S)<2);}$va=($a==""||driver()->supportsAlterTable($S));$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$j)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$j){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(192),drop_tables(array($a)));else{$l=array();$sa=array();$sl=false;$Ed=array();$rh=reset($sh);$pa=" FIRST";foreach($K["fields"]as$k){$n=$Gd[$k["type"]];$Yk=($n!==null?$Bi[$n]:$k);if($k["field"]!=""){if(!$k["generated"])$k["default"]=null;$ri=process_field($k,$Yk);$sa[]=array($k["orig"],$ri,$pa);if(!$rh||$ri!==process_field($rh,$rh)){$l[]=array($k["orig"],$ri,$pa);if($k["orig"]!=""||$pa)$sl=true;}if($n!==null)$Ed[idf_escape($k["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$Gd[$k["type"]],'source'=>array($k["field"]),'target'=>array($Yk["field"]),'on_delete'=>$k["on_delete"],));$pa=" AFTER ".idf_escape($k["field"]);}elseif($k["orig"]!=""){$sl=true;$l[]=array($k["orig"]);}if($k["orig"]!=""){$rh=next($sh);if(!$rh)$pa="";}}$Jh=array();if(in_array($K["partition_by"],$Hh)){foreach($K
as$w=>$X){if(preg_match('~^partition~',$w))$Jh[$w]=$X;}foreach($Jh["partition_names"]as$w=>$B){if($B==""){unset($Jh["partition_names"][$w]);unset($Jh["partition_values"][$w]);}}$Jh["partition_names"]=array_values($Jh["partition_names"]);$Jh["partition_values"]=array_values($Jh["partition_values"]);if($Jh==$Lh)$Jh=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$Jh=null;$gg=lang(193);if($a==""){cookie("adminer_engine",$K["Engine"]);$gg=lang(194);}$B=trim($K["name"]);$_=ME.(support("table")?"table=":"select=").url_escape($B);$I=alter_table($a,$B,(JUSH=="sqlite"&&($sl||$Ed)?$sa:$l),$Ed,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$Jh);if($I&&!Queries::$queries&&$a!=""&&!$l&&!$Ed)redirect($_);queries_redirect($_,$gg,$I);}}page_header(($a!=""?lang(43):lang(76)),$j,array("table"=>$a),h($a),$Lg);if(!$_POST){$bl=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($bl["int"])?"int":(isset($bl["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($sh
as$k){if($k["generated"])$k["default"]=ltrim($k["default"]);$k["generated"]=$k["generated"]?:(isset($k["default"])?"DEFAULT":"");$K["fields"][]=$k;}if($Hh){$K+=$Lh;$K["partition_names"][]="";$K["partition_values"][]="";}}}$rb=flat_collations();$Qc=driver()->engines();foreach($Qc
as$Pc){if(!strcasecmp($Pc,$K["Engine"])){$K["Engine"]=$Pc;break;}}$Rf=max_input_vars(12,20);if($Rf){$pe=(count($K["fields"])>$Rf?"":" hidden");echo"<p".($pe?" id='max-fields' data-columns='$Rf'":"")." class='error$pe'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(195).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",(!$va?h($S["Engine"])."\n":($Qc?html_select("Engine",array(""=>"(".lang(196).")")+$Qc,$K["Engine"],on('change','helpClose').on_help_value())."\n":""));if($rb)echo"<datalist id='collations'>".optionlist($rb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".lang(119).")'>\n");echo"<input type='submit' value='".lang(17)."'>\n";}if(support("columns")&&$va){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$rb,"TABLE",$Gd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(50).": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(197),on('click','columnShowClick',6),"jsonly");$xb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$xb,lang(49),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($xb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$a)),'>
';if($Hh&&(JUSH=='sql'||$a=="")){$Ih=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",lang(199),$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Hh),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",lang(200).": <input type='number' name='partitions' class='size".($Ih||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($Ih?"":" class='hidden'").">\n","<thead><tr><th>".lang(201)."<th>".lang(202)."<tbody>\n";foreach($K["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($w==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Je=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$He=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Je[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Je[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Je[]="VECTOR";$v=indexes($a);$l=fields($a);$li=array();if(JUSH=="mongo"){$li=$v["_id_"];unset($Je[0]);unset($v["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$j&&!$_POST["add"]&&!$_POST["drop_col"]){$ua=array();foreach($K["indexes"]as$u){$B=$u["name"];if(in_array($u["type"],$Je)){$d=array();$Cf=array();$mc=array();$fh=array();$Ie=(support("partial_indexes")?$u["partial"]:"");$Ge=(in_array($u["algorithm"],$He)?$u["algorithm"]:"");$O=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$c){if($c!=""){$x=idx($u["lengths"],$w);$kc=idx($u["descs"],$w);$eh=idx($u["opclasses"],$w);$O[]=($l[$c]?idf_escape($c):$c).($x?"(".(+$x).")":"").($eh!=""?" ".idf_escape($eh):"").($kc?" DESC":"");$d[]=$c;$Cf[]=($x?:null);$mc[]=$kc;$fh[]="$eh";}}$ed=$v[$B];if($ed){ksort($ed["columns"]);ksort($ed["lengths"]);ksort($ed["descs"]);if($u["type"]==$ed["type"]&&array_values($ed["columns"])===$d&&(!$ed["lengths"]||array_values($ed["lengths"])===$Cf)&&array_values($ed["descs"])===$mc&&(!$ed["opclasses"]||array_values($ed["opclasses"])===$fh)&&$ed["partial"]==$Ie&&(!$He||$ed["algorithm"]==$Ge)){unset($v[$B]);continue;}}if($d)$ua[]=array($u["type"],$B,$O,$Ge,$Ie);}}foreach($v
as$B=>$ed)$ua[]=array($ed["type"],$B,"DROP");if(!$ua)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(203),alter_indexes($a,$ua));}page_header(lang(150),$j,array("table"=>$a),h($a));$ud=array_keys($l);if($_POST["add"]){foreach($K["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$K["indexes"][$w]["columns"][]="";}$u=end($K["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$K["indexes"]=$v;}$Cf=(JUSH=="sql"||JUSH=="mssql");$fh=driver()->indexOpclasses();$_j=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(204);$_e=" class='idxopts".($_j?"":" hidden")."'";if($He)echo"<th id='label-algorithm'$_e>".lang(205).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" hidden>',lang(206).($Cf?"<span$_e> (".lang(207).")</span>":"");if($Cf||support("descidx"))echo
checkbox("options",1,$_j,lang(125),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(208);if(support("partial_indexes"))echo"<th id='label-condition'$_e>".lang(209);echo'<td><noscript>',icon("plus","add[0]","+",lang(126)),'</noscript>
<tbody>
';if($li){echo"<tr><td>PRIMARY<td>";foreach($li["columns"]as$w=>$c)echo
select_input(" disabled",array_combine($ud,$ud),$c),"<label><input disabled type='checkbox'>".lang(58)."</label> ";echo"<td><td>\n";}$hf=1;foreach($K["indexes"]as$u){if(!$_POST["drop_col"]||$hf!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$hf][type]",array(-1=>"")+$Je,$u["type"],($hf==count($K["indexes"])?on('change','indexesAddRow'):""),"label-type");if($He)echo"<td$_e>".html_select("indexes[$hf][algorithm]",array_merge(array(""),$He),$u['algorithm'],"","label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$c){echo"<span>".select_input(" name='indexes[$hf][columns][$r]' title='".lang(47)."'".on('change','indexesChangeColumn',(JUSH=="sql"?"":$_GET["indexes"]."_")),($l&&($c==""||$l[$c])?array_combine($ud,$ud):array()),$c)," <span$_e>",($Cf?"<input type='number' name='indexes[$hf][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(124)."'>":"");if($fh){$eh=idx($u["opclasses"],$w);echo
html_select("indexes[$hf][opclasses][$r]",array(""=>"(".lang(210).")")+array_combine($fh,$fh)+($eh!=""?array($eh=>$eh):array()),$eh),'';}echo(support("descidx")?checkbox("indexes[$hf][descs][$r]",1,idx($u["descs"],$w),lang(58)):""),"<br>","</span></span>";$r++;}echo"<td><input name='indexes[$hf][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$_e><input name='indexes[$hf][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$hf]","x",lang(128),on('click','editingRemoveRow','indexes$1[type]'));}$hf++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(17),'\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$j&&!$_POST["add"]){$B=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(211),drop_databases(array(DB)));}elseif($B!==DB){if(DB!=""){$_GET["db"]=$B;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($B),lang(212),rename_database($B,(string)$K["collation"]));}else{$g=explode("\n",str_replace("\r","",$B));$Xj=true;$uf="";foreach($g
as$h){if(count($g)==1||$h!=""){if(!create_database($h,(string)$K["collation"]))$Xj=false;$uf=$h;}}restart_session();set_session("dbs",null);queries_redirect(preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($uf),lang(213),$Xj);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($B).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),lang(214));}}page_header(DB!=""?lang(66):lang(131),$j,array(),h(DB));$rb=collations();$B=DB;if($_POST)$B=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$rb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Td){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Td,$A)&&$A[1]){$B=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($B,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($B).'</textarea><br>':'<input name="name" autofocus value="'.h($B).'" data-maxlength="64" autocapitalize="off">')."\n",($rb?html_select("collation",array(""=>"(".lang(119).")")+$rb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):"")."\n",'<input type=\'submit\' value=\'',lang(17),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(143)."'".confirm(lang(198,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(126))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);$Xi=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$Ti=routine($_GET["call"],$Xi);page_header(lang(215).": ".h($ca),$j,"#routines","",!$Ti);$Ce=array();$xh=array();foreach($Ti["fields"]as$r=>$k){if(substr($k["inout"],-3)=="OUT"&&JUSH=='sql')$xh[$r]="@".idf_escape($k["field"])." AS ".idf_escape($k["field"]);if(!$k["inout"]||preg_match('~^(IN|OUTPUT)~',$k["inout"]))$Ce[]=$r;}if(!$j&&$_POST){$Ya=array();foreach($Ti["fields"]as$w=>$k){$X="";if(in_array($w,$Ce)){$X=process_input($k);if($X===false)$X="''";if(isset($xh[$w]))connection()->query("SET @".idf_escape($k["field"])." = $X");}if(isset($xh[$w]))$Ya[]="@".idf_escape($k["field"]);elseif(in_array($w,$Ce))$Ya[]=$X;}$za=implode(", ",$Ya);$H=(isset($_GET["callf"])||JUSH!="mssql"?(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Ti["returns"],"type")=="record"?"* FROM ":"").table($ca)."($za)":"EXEC ".table($ca).($za!=""?" $za":""));$Qj=microtime(true);$I=connection()->multi_query($H);$na=connection()->affected_rows;echo
adminer()->selectQuery($H,$Qj,!$I);if(!$I)echo"<p class='error'>".adminer()->error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang(216,$na)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($xh)print_select_result(connection()->query("SELECT ".implode(", ",$xh)));}}echo'
<form action="" method="post">
';if($Ce){echo"<table class='layout'>\n";foreach($Ce
as$w){$k=$Ti["fields"][$w];$B=$k["field"];echo"<tr><th>".adminer()->fieldName($k);$Y=idx($_POST["fields"],$B);if($Y!=""){if($k["type"]=="set")$Y=implode(",",$Y);}input($k,$Y,idx($_POST["function"],$B,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(215),'\'>
',input_token(),'</form>

',adminer()->commentValue($Xi,$Ti['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$B=$_GET["name"];$K=$_POST;if($_POST&&!$j&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$tk=array();foreach($K["source"]as$w=>$X)$tk[$w]=$K["target"][$w];$K["target"]=$tk;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $B"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$ua="ALTER TABLE ".table($a);$I=($B==""||queries("$ua DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($B)));if(!$K["drop"])$I=queries("$ua ADD".format_foreign_key($K));}queries_redirect(ME."table=".url_escape($a),($K["drop"]?lang(217):($B!=""?lang(218):lang(219))),$I);if(!$K["drop"])$j=lang(220);}$Lg=false;if(!$_POST&&$B!=""){$Gd=foreign_keys($a);$K=idx($Gd,$B,array());$Lg=!$K;}page_header(($B!=""?lang(221):lang(155)),$j,array("table"=>$a),h($B!=""?$B:$a),$Lg);if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($B!="")$K["source"][]="";else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Hj=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$th=get_schema();set_schema($K["ns"]);}$Ai=array_keys(array_filter(table_status('',true),function(array$S){return!$S["dependent"]&&fk_support($S);}));$tk=array_keys(fields(in_array($K["table"],$Ai)?$K["table"]:reset($Ai)));$b=on('change','foreignChange');echo"<p><label>".lang(222).": ".html_select("table",$Ai,$K["table"],$b)."</label>\n";if(JUSH!="sqlite"){$bc=array();foreach(adminer()->databases()as$h){if(!information_schema($h))$bc[]=$h;}echo"<label>".lang(77).": ".html_select("db",$bc,$K["db"]!=""?$K["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(223),'\'></noscript>
<table>
<thead><tr><th id="label-source">',lang(152),'<th id="label-target">',lang(153),'<tbody>
';$hf=0;foreach($K["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$Hj,$X,($hf==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$w)."]",$tk,idx($K["target"],$w),"","label-target");$hf++;}echo'</table>
<p>
<label>',lang(121),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>',lang(120),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]).' ':''),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)),'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(224),'\'></noscript>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$uh="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$uh=strtoupper($P["Engine"]);}if($_POST&&!$j){$B=trim($K["name"]);$Aa=" AS\n$K[select]";$_=ME."table=".url_escape($B);$gg=lang(225);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$B&&JUSH!="sqlite"&&$U=="VIEW"&&$uh=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($B).$Aa,$_,$gg);else{$xk="adminer_".uniqid();drop_create("DROP $uh ".table($a),"CREATE $U ".table($B).$Aa,"DROP $U ".table($B),"CREATE $U ".table($xk).$Aa,"DROP $U ".table($xk),($_POST["drop"]?substr(ME,0,-1):$_),lang(226),$gg,lang(227),$a,$B);}}$Lg=false;if(!$_POST&&$a!=""){$K=view($a);$Lg=!$K["select"];$K["name"]=$a;$K["materialized"]=($uh!="VIEW");if(!$j)$j=adminer()->error();}page_header(($a!=""?lang(42):lang(228)),$j,array("table"=>$a),h($a),$Lg);echo'
<form action="" method="post">
<p>',lang(208),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],lang(146)):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Ue=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Sj=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$j){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(229));elseif(in_array($K["INTERVAL_FIELD"],$Ue)&&isset($Sj[$K["STATUS"]])){$bj="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(230):lang(231)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$bj.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$bj)."\n".$Sj[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}$Lg=false;if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$Lg=!$L;$K=reset($L);}page_header(($aa!=""?lang(232).": ".h($aa):lang(233)),$j,"#events","",$Lg);echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(208),'<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(234),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(235),'<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>',lang(236),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Ue,$K["INTERVAL_FIELD"]),'<tr><th>',lang(134),'<td>',html_select("STATUS",$Sj,$K["STATUS"]),'<tr><th>',lang(49),'<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",lang(237)),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$Ti=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$j){foreach($K["fields"]as$w=>$k){if($k["field"]=="")unset($K["fields"][$w]);}$Zg=routine($_GET["procedure"],$Ti);$Xg=($Zg?routine_id($ca,$Zg):"");$Eg=routine_id($K["name"],$K);$Kb=create_routine($Ti,$K);$_=substr(ME,0,-1);$gg=lang(238);if(!$_POST["drop"]&&$Xg==$Eg&&connection()->flavor!="mysql")queries_redirect($_,$gg,queries(substr_replace($Kb,(JUSH=="mssql"?' OR ALTER':' OR REPLACE'),6,0)));else{$xk="adminer_".uniqid();drop_create("DROP $Ti $Xg",$Kb,"DROP $Ti $Eg",create_routine($Ti,array("name"=>$xk)+$K),"DROP $Ti ".routine_id($xk,$K),$_,lang(239),$gg,lang(240),$ca,$K["name"]);}}$Lg=false;if(!$_POST&&$ca!=""){$K=routine($_GET["procedure"],$Ti);$Lg=!$K;$K["name"]=$ca;}page_header(($ca!=""?(isset($_GET["function"])?lang(241):lang(242)).": ".h($ca):(isset($_GET["function"])?lang(243):lang(244))),$j,"#routines","",$Lg);if(!$_POST&&$ca=="")$K["language"]="sql";$rb=(JUSH=="sql"?flat_collations():array());$Ui=routine_languages();echo($rb?"<datalist id='collations'>".optionlist($rb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(208),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Ui?"<label>".lang(23).": ".html_select("language",array_keys($Ui),$K["language"],on('change','routineLanguage',$Ui))."</label>\n":""),'<input type=\'submit\' value=\'',lang(17),'\'>
';$Vi=strtolower($Ti);echo
doc_link(array('sql'=>"create-procedure.html",'mariadb'=>"create-$Vi/",),"?"),'<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$rb,$Ti);if(isset($_GET["function"])){echo"<tr><td>".lang(245);edit_type("returns",(array)$K["returns"],$rb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20,80,($Ui[$K["language"]]?:JUSH));echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$ca)),'>
';$Wi=routine_options($Ti);if($Wi){$kh=false;foreach($Wi
as$w=>$Cl){$i=($Cl?reset($Cl):"");$K["options"][$w]=idx($K["options"],$w,$i);if($K["options"][$w]!=$i)$kh=true;}print_fieldset("options",lang(125),$kh);echo"<table class='layout'>\n";foreach($Wi
as$w=>$Cl){$pf="label-option-$w";$Ek=str_replace("_"," ",$w);$M=array();foreach($Cl
as$Y)$M[$Y]=(strpos($Y,"$Ek ")===0?substr($Y,strlen($Ek)+1):$Y);echo"<tr><th id='$pf'>$Ek<td>".($M?html_select("options[$w]",$M,$K["options"][$w],"",$pf):"<input name='options[$w]' value='".h($K["options"][$w])."' aria-labelledby='$pf' autocapitalize='off'>")."\n";}echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$B="$_GET[name]";$K=$_POST;if($K&&!$j){$_=ME."table=".url_escape($a);$jg=lang(246);$hg=lang(247);$ig=lang(248);if(JUSH=="sqlite")queries_redirect($_,($K["drop"]?$jg:($B!=""?$hg:$ig)),recreate_table($a,$a,array(),array(),array(),"",array(),"$B",($K["drop"]?"":$K["clause"])));else{$ua="ALTER TABLE ".table($a);$fb=" CHECK ($K[clause])";$xk="adminer_".uniqid();drop_create("$ua DROP CONSTRAINT ".idf_escape($B),"$ua ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"").$fb,"$ua DROP CONSTRAINT ".idf_escape($K["name"]),"$ua ADD CONSTRAINT ".idf_escape($xk).$fb,"$ua DROP CONSTRAINT ".idf_escape($xk),$_,$jg,$hg,$ig,$B,$K["name"]);}}$Lg=false;if(!$K){$ib=driver()->checkConstraints($a);$Lg=($B!=""&&!$ib[$B]);$K=array("name"=>$B,"clause"=>$ib[$B]);}page_header(($B!=""?lang(249):lang(157)),$j,array("table"=>$a),h($B!=""?$B:$a),$Lg);echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo
lang(208).': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'',lang(17),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$B="$_GET[name]";$Uk=trigger_options();$K=trigger($B,$a);$Lg=($B!=""&&!$K);$K+=array("Trigger"=>$a."_bi");if($_POST){if(!$j&&in_array($_POST["Timing"],$Uk["Timing"])&&in_array($_POST["Event"],$Uk["Event"])&&in_array($_POST["Type"],$Uk["Type"])){$bh=" ON ".table($a);$Cc="DROP TRIGGER ".idf_escape($B).(JUSH=="pgsql"?$bh:"");$_=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($Cc,$_,lang(250));else{if($B!="")queries($Cc);queries_redirect($_,($B!=""?lang(251):lang(252)),queries(create_trigger($bh,$_POST)));if($B!="")queries(create_trigger($bh,$K+array("Type"=>reset($Uk["Type"]))));}}$K=$_POST;}page_header(($B!=""?lang(253):lang(159)),$j,array("table"=>$a),h($B!=""?$B:$a),$Lg);$Tk=on('change','triggerChange',"^".preg_quote($a,"/")."_[ba][iud]$",$a);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(254),'<td>',html_select("Timing",$Uk["Timing"],$K["Timing"],$Tk),'<tr><th>',lang(255),'<td>',html_select("Event",$Uk["Event"],$K["Event"],$Tk),(in_array("UPDATE OF",$Uk["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>',lang(48),'<td>',html_select("Type",$Uk["Type"],$K["Type"]),'<tr><th>',lang(208),'<td><input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
</table>
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($B!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,$B)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){function
grant($Td,array$pi,$d,$bh){if(!$pi)return
true;if($pi==array("ALL PRIVILEGES","GRANT OPTION"))return($Td=="GRANT"?queries("$Td ALL PRIVILEGES$bh WITH GRANT OPTION"):queries("$Td ALL PRIVILEGES$bh")&&queries("$Td GRANT OPTION$bh"));return
queries("$Td ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$pi).$d).$bh);}$ea=$_GET["user"];$pi=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$Eb)$pi[$Eb=="File access on server"?"Server Admin":$Eb][$K["Privilege"]]=$K["Comment"];}unset($pi["Server Admin"]["Usage"]);foreach($pi["Tables"]as$w=>$X)unset($pi["Databases"][$w]);$Dg=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$Dg[$X]=(array)$Dg[$X]+idx($_POST["grants"],$w,array());}$Ud=array();$I=(isset($_GET["host"])?connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])):null);$Lg=(isset($_GET["host"])&&!$I);if($I){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$Of,PREG_SET_ORDER)){foreach($Of
as$X){if($X[1]!="USAGE")$Ud["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$Ud["$A[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$j){$ah=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $ah",ME."privileges=",lang(256));else{$Hg=q($_POST["user"])."@".q($_POST["host"]);$Nh=$_POST["pass"];$Mb=false;$I=true;if($ah!=$Hg){$Mb=queries("CREATE USER $Hg IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($Nh));$I=$Mb;}elseif($Nh!="")$I=queries("SET PASSWORD FOR $Hg = ".(min_version(8,99)||$_POST["hashed"]?q($Nh):"PASSWORD(".q($Nh).")"));if($I){$Pi=array();foreach($Dg
as$Pg=>$Td){if(isset($_GET["grant"]))$Td=array_filter($Td);$Td=array_keys($Td);if(isset($_GET["grant"]))$Pi=array_diff(array_keys(array_filter($Dg[$Pg],'strlen')),$Td);elseif($ah==$Hg){$Wg=array_keys((array)$Ud[$Pg]);$Pi=array_diff($Wg,$Td);$Td=array_diff($Td,$Wg);unset($Ud[$Pg]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$Pg,$A)&&(!grant("REVOKE",$Pi,$A[2]," ON $A[1] FROM $Hg")||!grant("GRANT",$Td,$A[2]," ON $A[1] TO $Hg"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($ah!=$Hg)queries("DROP USER $ah");elseif(!isset($_GET["grant"])){foreach($Ud
as$Pg=>$Pi){if(preg_match('~^(.+)(\(.*\))?$~U',$Pg,$A))grant("REVOKE",array_keys($Pi),$A[2]," ON $A[1] FROM $Hg");}}}if($I&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(257):lang(258)),$I);if($Mb)connection()->query("DROP USER $Hg");}}page_header((isset($_GET["host"])?lang(32).": ".h("$ea@$_GET[host]"):lang(168)),$j,array("privileges"=>array('',lang(70))),"",$Lg);$K=$_POST;if($K)$Ud=$Dg;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Ud[(DB==""||$Ud?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(30),'<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>',lang(32),'<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>',lang(33),'<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],lang(259),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(70).doc_link(array('sql'=>"grant.html#priv_level"));$r=0;foreach($Ud
as$Pg=>$Td){echo'<th>'.($Pg!="*.*"?"<input name='objects[$r]' value='".h($Pg)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(30),"Databases"=>lang(34),"Tables"=>lang(148),"Procedures"=>lang(260),)as$Eb=>$kc){foreach((array)$pi[$Eb]as$oi=>$vb){echo"<tr><td".($kc?">$kc<td":" colspan='2'").' lang="en" title="'.h($vb).'">'.h($oi);$r=0;foreach($Ud
as$Pg=>$Td){$B="'grants[$r][".h(strtoupper($oi))."]'";$Y=$Td[strtoupper($oi)];if($Eb=="Server Admin"&&$Pg!=(isset($Ud["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$B><option><option value='1'".($Y?" selected":"").">".lang(261)."<option value='0'".($Y=="0"?" selected":"").">".lang(262)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$B value='1'".($Y?" checked":"").($oi=="All privileges"?" id='grants-$r-all'":($oi=="Grant option"?"":on('click','grantsClick',"grants-$r-all"))).">","</label>";$r++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(143),'\'',confirm(lang(198,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$j){$of=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$of++;}queries_redirect(ME."processlist=",lang(263,$of),$of||!$_POST["kill"]);}}page_header(lang(132),$j);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$r=-1;foreach(adminer()->processList()as$r=>$K){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$w=>$X)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$w=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(($K["db"]!=""?preg_replace('~&db=[^&]*~','',ME)."db=".url_escape($K["db"])."&":ME)."sql=".url_escape($X)).'">'.lang(264).'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo
format_number($r+1)."/".lang(265,max_connections()),"<p><input type='submit' value='".lang(266)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif($_GET["select"]!=""){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$l=fields($a);$Gd=column_foreign_keys($a);$Vg=$S["Oid"];$Ri=array();$d=array();$hj=array();$mh=array();$_k=null;foreach($l
as$w=>$k){$B=adminer()->fieldName($k);$Ag=html_entity_decode(strip_tags($B),ENT_QUOTES);if(isset($k["privileges"]["select"])&&$B!=""){$d[$w]=$Ag;if(is_shortable($k))$_k=adminer()->selectLengthProcess();}if(isset($k["privileges"]["where"])&&$B!="")$hj[$w]=$Ag;if(isset($k["privileges"]["order"])&&$B!="")$mh[$w]=$Ag;$Ri+=$k["privileges"];}list($M,$q)=adminer()->selectColumnsProcess($d,$v);$M=array_unique($M);$q=array_unique($q);$bf=count($q)<count($M);$Z=adminer()->selectSearchProcess($l,$v,$S);$D=adminer()->selectOrderProcess($l,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$gl=>$K){$Aa=convert_field($l[key($K)]);$M=array($Aa?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($gl,true),$l);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$li=$il=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$li=array_flip($u["columns"]);$il=($M?$li:array());foreach($il
as$w=>$X){if(in_array(idf_escape($w),$M))unset($il[$w]);}break;}}if($Vg&&!$li){$li=$il=array($Vg=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($Vg));}if($_POST&&!$j){$Nl=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$ib=array();foreach($_POST["check"]as$fb)$ib[]=where_check($fb,$l);$Nl[]="((".implode(") OR (",$ib)."))";}$Pl=$Nl;$Nl=($Nl?"\nWHERE ".implode(" AND ",$Nl):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$kj=($M?:array("*"));$Gb=convert_fields($d,$l,$M);if($Gb)$kj[]=substr($Gb,2);$H="";if(is_array($_POST["check"])&&!$li){$Ld=implode(", ",$kj)."\nFROM ".table($a);$Xd=($q&&$bf?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):"");$dl=array();foreach($_POST["check"]as$X)$dl[]="(SELECT".limit($Ld,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l).$Xd,1).")";$H=implode(" UNION ALL ",$dl);}adminer()->dumpData($a,"table",$H,$kj,$Pl,($bf?$q:array()),$D);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$Gd)){if($_POST["save"]||$_POST["delete"]){$I=true;$na=0;$Pa=false;$O=array();if(!$_POST["delete"]){foreach($l
as$B=>$X){$t=bracket_escape($B);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($l[$B]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($B)]=($X!==false?$X:idf_escape($B));}}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($li&&is_array($_POST["check"]))||$bf){$I=($_POST["delete"]?driver()->delete($a,$Nl):($_POST["clone"]?queries("INSERT $H$Nl".driver()->insertReturning($a)):driver()->update($a,$O,$Nl)));$na=connection()->affected_rows;if(is_object($I))$na+=$I->num_rows;}else{$Pa=count((array)$_POST["check"])>1&&driver()->begin();foreach((array)$_POST["check"]as$X){$Ml="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$l);$I=($_POST["delete"]?driver()->delete($a,$Ml,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$Ml)):driver()->update($a,$O,$Ml,1)));if(!$I)break;$na+=connection()->affected_rows;}if($Pa&&$I&&!driver()->commit())$I=false;}}$gg=lang(169,$na);if($_POST["clone"]&&$I&&$na==1){$wf=last_id($I);if($wf)$gg=lang(191," $wf");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$gg,$I);if($Pa)driver()->rollback();if(!$_POST["delete"]){$di=(array)$_POST["fields"];edit_form($a,array_intersect_key($l,$di),$di,!$_POST["clone"],$j);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$na=0;$Pa=count((array)$_POST["val"])>1&&driver()->begin();foreach((array)$_POST["val"]as$gl=>$K){$O=array();foreach($K
as$w=>$X){$w=bracket_escape($w,true);$O[idf_escape($w)]=(preg_match('~char|text~',$l[$w]["type"])||$X!=""?adminer()->processInput($l[$w],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($gl,true),$l),($bf||$li?0:1)," ");if(!$I)break;$na+=connection()->affected_rows;}if($Pa)$I=$I&&driver()->commit();queries_redirect(remove_from_uri(),lang(169,$na),$I);if($Pa)driver()->rollback();}else{save_settings(array("format"=>$_POST["separator"]),"adminer_import");$vd=get_file("csv_file",true);if(!is_string($vd))$j=upload_error($vd);elseif(!preg_match('~~u',$vd))$j=lang(267);else{$sb=array_keys($l);$pj=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Qb=parse_csv($vd,$pj);$na=count($Qb);driver()->begin();$L=array();foreach($Qb
as$w=>$Cl){if(!$w&&!array_diff($Cl,$sb)){$sb=$Cl;$na--;}else{$O=array();foreach($Cl
as$r=>$ob)$O[idf_escape($sb[$r])]=($ob==""&&$l[$sb[$r]]["null"]?"NULL":q(csv_value($ob)));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$li));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(268,$na),$I);driver()->rollback();}}}}$ik=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(52).": $ik",$j,array(),"",(!$l&&support("table")));$O=null;if(isset($Ri["insert"])||!support("table")){$O="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$A)?$A[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$O
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$O);if(!$d&&support("table"))echo"<p class='error'>".lang(269)."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$hj,$v,$S);adminer()->selectOrderPrint($D,$mh,$v);adminer()->selectLimitPrint($y);if($_k!==null)adminer()->selectLengthPrint($_k);adminer()->selectActionPrint($v);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(114).' '.lang(115)."\n";page_footer();exit;}}$E=$_GET["page"];$Jd=null;if($E=="last"){$Jd=get_val(count_rows($a,$Z,$bf,$q));$E=floor(max(0,intval($Jd)-1)/$y);}$jj=$M;$Wd=$q;if(!$jj){$jj[]="*";$Gb=convert_fields($d,$l,$M);if($Gb)$jj[]=substr($Gb,2);}foreach($M
as$w=>$X){$k=$l[idf_unescape($X)];if($k&&($Aa=convert_field($k)))$jj[$w]="$Aa AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$w=>$X){if(isset($jj[$w])&&$X["fun"])$jj[$w].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$bf&&$il){foreach($il
as$w=>$X){$jj[]=idf_escape($w);if($Wd)$Wd[]=idf_escape($w);}}$I=driver()->select($a,$jj,$Z,$Wd,$D,$y,$E,true);if(!is_object($I))echo"<p class='error'>".(adminer()->error()?:lang(25))."\n";else{if(JUSH=="mssql"&&$E)$I->seek($y*$E);$Nc=array();$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$he=($y&&(support("cursor")?$_GET["next"]!="":count($L)>=$y));if(is_ajax()&&$he)header("X-Next-Page: ".pagination_href($E+1));if($_GET["modify"]&&$L){$Xf=max_input_vars(count($L[0])+1,20);echo($Xf&&count($L)>$Xf?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'".on_upload_progress($ol).">\n";if($_GET["page"]!="last"&&$y&&$q&&$bf&&JUSH=="sql")$Jd=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".lang(15)."\n";else{$La=adminer()->backwardKeys($a,$ik);$Oi=array();reset($M);foreach($L[0]as$w=>$X){if(!isset($il[$w])){$X=idx($_GET["columns"],key($M))?:array();$Oi[$w]=array("fun"=>$X["fun"],"col"=>($M?$X["col"]:$w));next($M);}}echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$q&&$M?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".lang(270)."'".on('click','formCheck','^check').">");$Bg=array();$yi=1;foreach($Oi
as$w=>$X){$k=$l[$X["col"]];$B=($k?adminer()->fieldName($k,$yi):($X["fun"]?"*":h($w)));if($B!=""){$yi++;$Bg[$w]=$B;$c=idf_escape($w);$ue=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($w);$kc="&desc[0]=1";$Ej=preg_replace('~ DESC( NULLS LAST)?$~','',$D[0]);$Gj=($Ej==$c||$Ej==$w);echo"<th id='th[".h(bracket_escape($w))."]'".($Gj?" aria-sort='".($Ej==$D[0]?"ascending":"descending")."'":"").">";$Pd=apply_sql_function(h($X["fun"]),$B);$Fj=isset($k["privileges"]["order"])||$X["fun"];echo($Fj?"<a href='".h($ue.($Gj&&$Ej==$D[0]?$kc:''))."'>$Pd</a>":$Pd);$fg=($Fj?"<a href='".h($ue.$kc)."' title='".lang(58)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($k["privileges"]["where"]))$fg
.="<a href='#fieldset-search' title='".lang(55)."' class='text jsonly'".on('click','selectSearch',$w)."> =</a>";echo($fg?"<span class='column'>$fg</span>":"");}}$Cf=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$w=>$X)$Cf[$w]=max($Cf[$w],min(40,utf8_length($X)));}}echo($La?"<th>".lang(271):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$Gd)as$zg=>$K){$fl=unique_array($L[$zg],$v);if(!$fl){$fl=array();foreach($L[$zg]as$w=>$X){if(!in_array(idx(idx($Oi,$w,array()),"fun"),driver()->grouping))$fl[$w]=$X;}}$gl="";$r=0;foreach($fl
as$w=>$X){$Ni=idx($Oi,$w,array());$Pd=idx($Ni,"fun","");$ob=($Pd?$Ni["col"]:$w);$k=(array)$l[$ob];$af=is_blob($k);if(!$Pd&&(JUSH=="sql"||JUSH=="pgsql")&&($af||preg_match('~'.text_type().'~',$k["type"]))&&strlen($X)>64){$Pd="md5";$X=md5($af?(string)driver()->value($X,$k):$X);}if($Pd){$gl
.="&fun[$r]=".url_escape($Pd)."&col[$r]=".url_escape($ob).($X!==null?"&val[$r]=".url_escape($X===false?"f":$X):"");$r++;}else$gl
.="&".($X!==null?"where[".url_escape(bracket_escape($ob))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($ob));}echo"<tr>".(!$q&&$M?"":"<td class='hover check'>".($bf||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$gl)."' class='edit'>".lang(272)."</a> ").checkbox("check[]",substr($gl,1),in_array(substr($gl,1),(array)$_POST["check"])));foreach($K
as$w=>$X){if(isset($Bg[$w])){$Pd=$Oi[$w]["fun"];$ob=$Oi[$w]["col"];$k=(array)$l[$w];if($X!=""&&(!isset($Nc[$w])||$Nc[$w]!=""))$Nc[$w]=(is_mail($X)?$Bg[$w]:"");$z="";if(is_blob($k)&&$X!="")$z=ME.'download='.url_escape($a).'&field='.url_escape($w).$gl;if(!$z&&$X!==null){foreach((array)$Gd[$w]as$n){if(count($Gd[$w])==1||end($n["source"])==$w){$z="";foreach($n["source"]as$r=>$Hj)$z
.=where_link($r,$n["target"][$r],$L[$zg][$Hj]);$z=($n["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($n["db"]),ME):ME).'select='.url_escape($n["table"]).$z;if($n["ns"])$z=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($n["ns"]),$z);if(count($n["source"])==1)break;}}}if($Pd=="count"&&$ob==""){$z=ME."select=".url_escape($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$fl))$z
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($fl
as$kf=>$W){if(idx(idx($Oi,$kf,array()),"fun")){$z="";break;}$z
.=where_link($r++,$kf,$W);}}$ve=select_value($X,$z,$k,$_k);$t=bracket_escape($gl);$s=h("val[$t][".bracket_escape($w)."]");$fi=idx(idx($_POST["val"],$t),bracket_escape($w));$ll=idx($k["privileges"],"update");$Jc=!is_array($K[$w])&&!is_blob($k)&&is_utf8($X)&&$L[$zg][$w]==$X&&!$Pd&&!$k["generated"]&&$ll;$U=($Pd=="min"||$Pd=="max"?$l[$ob]["type"]:$k["type"]);$zk=preg_match('~text|json|lob~',$U);$cf=preg_match(number_type(),$U)||preg_match('~^(avg|ceil|char_length|count|count distinct|floor|len|length|round|sum|time_to_sec)$~',$Pd);echo"<td id='$s'".($cf&&($X===null||is_numeric(strip_tags($ve))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$Jc&&$X!==null)||$fi!==null){$ce=h($fi!==null?$fi:$X);echo">".($zk?"<textarea name='$s' cols='30' rows='".(substr_count($X,"\n")+1)."'>$ce</textarea>":"<input name='$s' value='$ce' size='$Cf[$w]'>");}else{$Lf=strpos($ve,"<i>…</i>");echo($ll?" data-text='".($Lf?2:($zk?1:0))."'".($Jc?"":" data-warning='".lang(273)."'"):"").">$ve";}}}if($La)echo"<td>";adminer()->backwardKeysPrint($La,$L[$zg]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){$ma=get_settings("adminer_import");if($L||$E||$he){$cd=true;if($_GET["page"]!="last"){if(!$y||(count($L)<$y&&($L||!$E)))$Jd=($E?$E*$y:0)+count($L);elseif(JUSH!="sql"||!$bf){$Jd=($bf?false:found_rows($S,$Z));if(intval($Jd)<max(1e4,2*($E+1)*$y))$Jd=first(slow_query(count_rows($a,$Z,$bf,$q)));elseif(JUSH=='sql'||JUSH=='pgsql')$cd=false;}}if(!support("cursor"))$he=(($Jd===false?count($L)+1:$Jd-$E*$y)>$y);$_h=($y&&($he||$E));if($_h)echo($he?'<p><a href="'.h(pagination_href($E+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(274)).'>'.lang(275).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($_h){$Vf=($Jd===false?$E+($L?(count($L)>=$y?2:1):0):floor(($Jd-1)/$y));echo"<fieldset><legend>".lang(276)."</legend>";if(!support("cursor")){echo
pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($Vf,$E+5);$r++)echo
pagination($r,$E);if($Vf>0)echo($E+5<$Vf?" …":""),($cd&&$Jd!==false?pagination($Vf,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Vf'>".lang(277)."</a>");}else
echo
pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($he?pagination($E+1,$E)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(278)."</legend>";$sc=($cd?"":"~ ").$Jd;$pf=($Jd!==false?($cd?"":"~ ").lang(173,$Jd):"");echo
checkbox("all",1,0,$pf,on('click','countRows',$sc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(174)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(279),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(17),'\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(142),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(13),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(264),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(21),'\'',confirm(),'>
</div></fieldset>
';$Hd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($Hd['sql']);break;}}if($Hd){print_fieldset("export",lang(75)." <span id='selected2'></span>");$yh=adminer()->dumpOutput();echo($yh?html_select("output",$yh,$ma["output"])." ":""),html_select("format",$Hd,$ma["format"])," <input type='submit' name='export' value='".lang(75)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($Nc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(74)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",($ol?input_hidden(ini_get("session.upload_progress.name"),$ol):""),file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$ma["format"])." <input type='submit' name='import' value='".lang(74)."'>".($ol?" <progress class='jsonly hidden' max='1' value='0'></progress>":"")),"</span>";echo
input_token(),"</form>\n",(!$q&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?lang(134):lang(133));$Dl=($P?adminer()->showStatus():adminer()->showVariables());if(!$Dl)echo"<p class='message'>".lang(15)."\n";else{echo"<table>\n";foreach($Dl
as$K){echo"<tr>";$w=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($w)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$ak=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$B=>$S){json_row("Comment-$B",h($S["Comment"]).($S["Error"]?" <span class='error'>".h($S["Error"])."</span>":""));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$B",h($S[$w]));foreach(array_keys($ak+array("Auto_increment"=>0,"Rows"=>0))as$w){if(array_key_exists($w,$S))json_row("$w-$B",format_status($S,$w));if($S[$w]!=""&&isset($ak[$w]))$ak[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}}}if(function_exists('Adminer\db_status'))$ak=db_status();foreach($ak
as$w=>$X)json_row("sum-$w",format_number($X));json_row("");}elseif($_GET["script"]=="kill"){if(!$j)connection()->query("KILL ".number($_POST["kill"]));}else{foreach(count_tables(adminer()->databases(false))as$h=>$X){json_row("tables-$h",format_number($X));json_row("size-$h",db_size($h));}json_row("");}exit;}else{if(!isset($_GET["select"])&&support("single_table")){$T=tables_list();if($T)redirect(ME.(support("table")?"table=":"select=").url_escape(key($T)));}$cg=ME.(isset($_GET["select"])?"select=&":"");$rk=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($rk&&!$j&&!$_POST["search"]){$I=true;$gg="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$gg=lang(280);}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$gg=lang(281);}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$gg=lang(282);}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$gg=lang(283);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$gg
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH=="mssql"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("DBCC CHECKTABLE (".q(table($R)).") WITH TABLERESULTS")as$K)$gg
.="<b>".h($R)."</b>: ".h($K["MessageText"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$gg=lang(284);}elseif(!$_POST["tables"])$gg=lang(12);elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$gg
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect(relative_uri(),$gg,$I);}page_header(($_GET["ns"]==""?lang(34).": ".h(DB):lang(165).": ".h($_GET["ns"])),$j,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];$Md=($D||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(285)."</h3>\n";$qk=($Md?table_status():tables_list());if(!$qk)echo"<p class='message'>".lang(12)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(286)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(55)."'>\n","</div></fieldset>\n";if(!$j&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr>','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(167).'"'.on('click','formCheck','^(tables|views)\[').'>','<th'.(!$D&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr($cg,0,-1)).'">'.lang(148).'</a>';$d=array("Engine"=>array(lang(287).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$d["Collation"]=array(lang(138).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$d["Data_length"]=array(lang(288).doc_link(array('sql'=>'show-table-status.html',)),"create",lang(43),);if(support("indexes"))$d["Index_length"]=array(lang(289).doc_link(array('sql'=>'show-table-status.html',)),"indexes",lang(151),);$d["Data_free"]=array(lang(290).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(44));if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array(lang(50).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(43),);$d["Rows"]=array(lang(291).doc_link(array('sql'=>'show-table-status.html',)),"select",lang(40),);if(support("comment"))$d["Comment"]=array(lang(49).doc_link(array('sql'=>'show-table-status.html',)),);$Ba=array('Engine','Collation','Comment');foreach($d
as$w=>$c)echo"<th".($D==$w?" aria-sort='".(in_array($w,$Ba)?"ascending":"descending")."'":"")."><a href='".h($cg)."order=$w'>$c[0]</a>";echo"<tbody>\n";if($D){uasort($qk,function($ga,$Ia)use($D,$Ba){$J=($ga[$D]<$Ia[$D]?-1:($ga[$D]>$Ia[$D]?1:0));return(in_array($D,$Ba)?$J:-$J);});}$T=0;$ak=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($qk
as$B=>$P){$Gl=($Md?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($Md?$P:array('Engine'=>$P));$s=h("Table-".$B);echo'<tr><td class="hover">'.checkbox(($Gl?"views[]":"tables[]"),$B,in_array("$B",$rk,true),"","","",$s),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($B)."' title='".lang(41)."' id='$s'>".h($B).'</a>':h($B));if($Gl&&!preg_match('~materialized~i',$P['Engine'])){$Ek=lang(147);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($B)."' title='".lang(42)."'>$Ek</a>":$Ek),"<td align='right'><a href='".h(ME)."select=".url_escape($B)."' title='".lang(40)."'>?</a>";if(support("comment"))echo'<td>'.h($P['Comment']);}else{if($Md){foreach(array_keys($ak)as$w)$ak[$w]+=($P["Engine"]!="InnoDB"||$w!="Data_free"?idx($P,$w):0);}foreach($d
as$w=>$c){$s=" id='$w-".h($B)."'";echo($c[1]?"<td align='right'><a href='".h(ME."$c[1]=").url_escape($B)."'$s title='$c[2]'>".format_status($P,$w)."</a>":"<td$s>".h(idx($P,$w,'?')).($w=="Comment"&&$P["Error"]?" <span class='error'>".h($P["Error"])."</span>":""));}$T++;}echo"\n";}echo"<tr><td class='hover'><th>".lang(265,count($qk)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($Md&&function_exists('Adminer\db_status'))$ak=db_status();foreach($ak
as$w=>$Zj)echo($d[$w]?"<td align='right' id='sum-$w'>".($Md?format_number($Zj):""):"");echo"\n","</table>\n",($Md?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$_l="<input type='submit' value='".lang(292)."'".on_help("VACUUM")."> ";$ih="<input type='submit' name='optimize' value='".lang(293)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$mi=(JUSH=="sqlite"?$_l."<input type='submit' name='check' value='".lang(294)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$_l.$ih:(JUSH=="mssql"?"<input type='submit' name='check' value='".lang(294)."'".on_help("DBCC CHECKTABLE")."> ":(JUSH=="sql"?"<input type='submit' value='".lang(295)."'".on_help("ANALYZE TABLE")."> ".$ih."<input type='submit' name='check' value='".lang(294)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(296)."'".on_help("REPAIR TABLE")."> ":"")))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(297)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(143)."'".confirm().on_help("DROP TABLE").">":"");echo($mi?"<div class='footer'><div>\n<fieldset><legend>".lang(142)." <span id='selected'></span></legend><div>$mi\n</div></fieldset>\n":"");$g=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($g)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(298)." <span id='selected3'></span></legend><div>";$h=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($g?html_select("target",$g,$h):'<input name="target" value="'.h($h).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(127)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(22)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(299)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(76)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(228)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(71)."</h3>\n";$Yi=routines();if($Yi){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(208).'<th>'.lang(48).'<th>'.lang(245)."<td class='hover'><tbody>\n";foreach($Yi
as$K){$B=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$B).'" title="'.lang(215).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$B).'">'.lang(154)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(244).'</a>':'').'<a href="'.h(ME).'function=">'.lang(243)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(73)."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".lang(208)."<th>".lang(300)."<th>".lang(234)."<th>".lang(235)."<td class='hover'><tbody>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?lang(301)."<td>".h($K["Execute at"]):lang(236)." ".h($K["Interval value"])." ".h($K["Interval field"])."<td>".h($K["Starts"])),"<td>".h($K["Ends"]),'<td class="hover"><a href="'.h(ME).'event='.url_escape($K["Name"]).'">'.lang(154).'</a>';echo"</table>\n";$Zc=get_val("SELECT @@event_scheduler");if($Zc&&$Zc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Zc)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(233)."</a>\n","</div>\n";}}}}page_footer();