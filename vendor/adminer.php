<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 6.0.0
*/namespace
Adminer;const
VERSION="6.0.0";error_reporting(24575);set_error_handler(function($Fc,$Hc){return!!preg_match('~^Undefined (array key|offset|index)~',$Hc);},E_WARNING|E_NOTICE);$id=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($id||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Ej=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Ej)$$X=$Ej;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$Cb=adminer()->credentials();$J=Driver::connect($Cb[0],$Cb[1],$Cb[2]);return(is_object($J)?$J:null);}function
idf_unescape($u){if(!preg_match('~^[`\'"[]~',$u))return$u;$Re=substr($u,-1);return
str_replace($Re.$Re,$Re,substr($u,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($wa,$x,$j=null){return($wa&&array_key_exists($x,$wa)?$wa[$x]:$j);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
int_type(){return'(tiny|small|medium|big)?int(eger|\d)?';}function
number_type(){return'(^('.int_type().'|decimal|numeric|real|(binary_|half_|scaled_)?float\d?|(binary_)?double( precision)?|(small)?money)$)';}function
remove_slashes(array$Wj,$id=false){$J=array();foreach($Wj
as$x=>$X)$J[stripslashes($x)]=(is_array($X)?remove_slashes($X,$id):($id?$X:stripslashes($X)));return$J;}function
bracket_escape($u,$Ea=false){static$pj=array(':'=>':1',']'=>':2','['=>':3','"'=>':4','='=>':5');return
strtr($u,($Ea?array_flip($pj):$pj));}function
url_escape($Q){static$pj=array();if(!$pj){$pj=array(' '=>'+');foreach(str_split("\"'<>#%&+=?".ini_get("arg_separator.input"))as$Ua)$pj[$Ua]=sprintf('%%%02X',ord($Ua));for($s=0;$s<256;$s++){if($s<32||$s>126)$pj[chr($s)]=sprintf('%%%02X',$s);}}return
strtr((string)$Q,$pj);}function
min_version($Zj,$kf="",$f=null){$f=connection($f);$ii=$f->server_info;if($kf&&preg_match('~([\d.]+)-MariaDB~',$ii,$B)){$ii=$B[1];$Zj=$kf;}return$Zj&&version_compare($ii,$Zj)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($ug,$Y){return(function_exists('ini_set')?\ini_set($ug,$Y):false);}function
ini_bool($qe){$X=ini_get($qe);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($qe){$X=ini_get($qe);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
max_input_vars($K,$Gg){$nf=(int)ini_get("max_input_vars");return($nf?(int)floor(($nf-$Gg)/$K):0);}function
max_input_vars_error(){$qe="max_input_vars";return
lang(0,"<b>$qe = ".ini_get($qe)."</b>");}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($Yj,$N,$V,$Xg){$_SESSION["pwds"][$Yj][$N][$V]=($_COOKIE["adminer_key"]&&is_string($Xg)?array(encrypt_string($Xg,$_COOKIE["adminer_key"])):$Xg);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$l=0,$rb=null){$rb=connection($rb);$I=$rb->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$l]:false);}function
get_vals($H,$c=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$c];}return$J;}function
get_key_vals($H,$f=null,$li=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($li)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$k="<p class='error'>"){$rb=connection($f);$J=array();$I=$rb->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$k&&(defined('Adminer\PAGE_HEADER')||$k=="-- "))echo$k.error()."\n";return$J;}function
unique_array($K,array$w){foreach($w
as$v){if(preg_match("~^(PRIMARY|UNIQUE)$~",$v["type"])&&!$v["partial"]){$J=array();foreach($v["columns"]as$x){if(!isset($K[$x]))continue
2;$J[$x]=$K[$x];}return$J;}}}function
escape_key($x){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$x,$B))return$B[1].idf_escape(idf_unescape($B[2])).$B[3];return
idf_escape($x);}function
where(array$Z,array$m=array()){$J=array();foreach((array)$Z["where"]as$x=>$X){$x=bracket_escape($x,true);$c=escape_key($x);$l=idx($m,$x,array());$cd=$l["type"];$_e=$l&&(is_blob($l)||preg_match('~binary~',$cd));$J[]=$c.($_e&&!is_utf8($X)?" = ".driver()->quoteBinary($X):(JUSH=="sql"&&$cd=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$l["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($cd,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($l,q($X)))))));if(JUSH=="sql"&&preg_match('~char|text~',$cd)&&preg_match("~[^ -@]~",$X))$J[]="$c = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$x)$J[]=escape_key($x)." IS NULL";return
implode(" AND ",$J);}function
where_columns(array$m){$J=array();foreach((array)$_GET["null"]as$x)$J[$x]=true;foreach((array)$_GET["where"]as$x=>$X){$x=bracket_escape($x,true);foreach($m
as$D=>$l){if($x==$D||strpos($x,idf_escape($D))!==false)$J[$D]=true;}}return$J;}function
where_check($X,array$m=array()){parse_str($X,$Wa);remove_slashes(array(&$Wa));return
where($Wa,$m);}function
where_link($s,$c,$Y,$rg="="){$og=($Y!==null?$rg:"IS NULL");return"&where[$s][col]=".url_escape($c).($og!=first(adminer()->operators())?"&where[$s][op]=".url_escape($og):"")."&where[$s][val]=".url_escape($Y);}function
convert_fields(array$d,array$m,array$M=array()){$J="";foreach($d
as$x=>$X){if($M&&!in_array(idf_escape($x),$M))continue;$xa=convert_field($m[$x]);if($xa)$J
.=", $xa AS ".idf_escape($x);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($D,$Y,$bf=2592000){header("Set-Cookie: $D=".rawurlencode($Y).($bf?"; expires=".gmdate("D, d M Y H:i:s",time()+$bf)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"").($D=="adminer_import"?"":"; HttpOnly")."; SameSite=lax",false);}function
get_url($Lj,$vb){$http_response_header=null;$Gc=array();set_error_handler(function($Fc,$k)use(&$Gc){$Gc[]=preg_replace('~^file_get_contents\([^)]*\):\s*~','',$k);return
true;});$J=file_get_contents($Lj,false,$vb);restore_error_handler();$Pd=(function_exists('http_get_last_response_headers')?http_get_last_response_headers():$http_response_header);return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',idx($Pd,0,''),$B)?$B[1]:''),(array)$Pd,($J===false?implode("\n",$Gc):''),);}function
get_settings($yb){parse_str($_COOKIE[$yb],$mi);return$mi;}function
get_setting($x,$yb="adminer_settings",$j=null){return
idx(get_settings($yb),$x,$j);}function
save_settings(array$mi,$yb="adminer_settings"){$Y=http_build_query($mi+get_settings($yb));cookie($yb,$Y);$_COOKIE[$yb]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==PHP_SESSION_NONE))session_start();}function
stop_session($od=false){$Oj=ini_bool("session.use_cookies");if(!$Oj||$od){session_write_close();if($Oj&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($x){return$_SESSION[$x][DRIVER][SERVER][$_GET["username"]];}function
set_session($x,$X){$_SESSION[$x][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($Yj,$N,$V,$i=null){$Kj=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($i!==null?"db|":"").($Yj=='mssql'||$Yj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Kj,$B);return"$B[1]?".(sid()?SID."&":"").($_GET["ext"]?"ext=".url_escape($_GET["ext"])."&":"").($Yj!="server"||$N!=""?url_escape($Yj)."=".url_escape($N)."&":"")."username=".url_escape($V).($i!=""?"&db=".url_escape($i):"").($B[2]?"&$B[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($A,$C=null){if($C!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($A!==null?$A:$_SERVER["REQUEST_URI"]))][]=$C;}if($A!==null){if($A=="")$A=".";header("Location: $A");exit;}}function
query_redirect($H,$A,$C,$Bh=true,$Nc=true,$Xc=false,$cj=""){if($Nc){$Bi=microtime(true);$Xc=!connection()->query($H);$cj=format_time($Bi);}$wi=($H?adminer()->messageQuery($H,$cj,$Xc):"");if($Xc){adminer()->error
.=error().$wi.script("messagesPrint();")."<br>";return
false;}if($Bh)redirect($A,$C.$wi);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");return
connection()->query($H);}function
apply_queries($H,array$T,$Ic='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$Ic($R)))return
false;}return
true;}function
queries_redirect($A,$C,$Bh){$xh=implode("\n",Queries::$queries);$cj=format_time(Queries::$start);return
query_redirect($xh,$A,$C,$Bh,false,!$Bh,$cj);}function
format_time($Bi){return
lang(1,max(0,microtime(true)-$Bi));}function
relative_uri(){return
preg_replace_callback('~^[^?]*~',function($B){return
str_replace(":","%3A",$B[0]);},preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($Lg=""){return
substr(preg_replace("~(?<=[?&])($Lg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_files($x,$Qb=false){$ed=$_FILES[$x];if(!$ed)return
null;foreach($ed
as$x=>$X)$ed[$x]=(array)$X;$J=array();foreach($ed["error"]as$x=>$k){if($k)return$k;$D=$ed["name"][$x];$kj=$ed["tmp_name"][$x];$tb=file_get_contents($Qb&&preg_match('~\.gz$~',$D)?"compress.zlib://$kj":$kj);if($Qb){$Bi=substr($tb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Bi))$tb=iconv("utf-16","utf-8",$tb);elseif($Bi=="\xEF\xBB\xBF")$tb=substr($tb,3);}$J[]=array($D,$tb);}return$J;}function
get_file($x,$Qb=false,$Wb=""){$hd=get_files($x,$Qb);if(!is_array($hd))return$hd;$J='';foreach($hd
as$ed){$tb=$ed[1];$J
.=$tb;if($Wb)$J
.=(preg_match("($Wb\\s*\$)",$tb)?"":$Wb)."\n\n";}return$J;}function
upload_error($k){$vf=($k==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($k?lang(2).($vf?" ".lang(3,$vf):""):lang(4));}function
repeat_pattern($Zg,$y){return
str_repeat("$Zg{0,65535}",$y/65535)."$Zg{0,".($y%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",lang(5)),preg_split('~~u',lang(6),-1,PREG_SPLIT_NO_EMPTY));}function
format_status(array$S,$x){$X=idx($S,$x,'?');if(!is_numeric($X))return
h($X);if($X<0)return'?';$ta=($x=="Rows"&&(JUSH=="sqlite"||$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")));return($ta?"~ ":"").format_number($X);}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Yc=false){$J=table_status($R,$Yc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$o){foreach($o["source"]as$X)$J[$X][]=$o;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$x=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$x];$_POST["fields"][$X]=$_POST["field_vals"][$x];}}foreach((array)$_POST["fields"]as$x=>$X){$D=bracket_escape($x,true);$J[$D]=array("field"=>$D,"full_type"=>"","type"=>"","privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>true,"auto_increment"=>($D==driver()->primary),);}return$J;}function
dump_headers($ae,$Nf=false){$J=adminer()->dumpHeaders($ae,$Nf);$Ig=$_POST["output"];if($Ig!="text"||$J=="tar"){$ob=($Ig!="text"&&$Ig!="file"&&preg_match('~^[0-9a-z]+$~',$Ig)?".$Ig":"");header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($ae).".$J$ob");}session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$xj=$_POST["format"]=="tsv";foreach($K
as$x=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($xj?'\t':'[,;]|^$').'~',$X))$K[$x]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($xj?"\t":";")),$K)."\r\n";}function
parse_csv($Fb,$hi){$J=array();preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Fb,$lf);foreach($lf[0]as$K){preg_match_all("~((?>\"[^\"]*\")+|[^$hi]*)$hi~",$K.$hi,$mf);$J[]=$mf[1];}return$J;}function
csv_value($X){return(preg_match('~^".*"$~s',$X)?str_replace('""','"',substr($X,1,-1)):$X);}function
apply_sql_function($q,$c){return($q?($q=="unixepoch"?"DATETIME($c, '$q')":($q=="count distinct"?"COUNT(DISTINCT ":strtoupper("$q("))."$c)"):$c);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($n){if(is_link($n))return;$p=@fopen($n,"c+");if(!$p)return;@chmod($n,0660);if(!flock($p,LOCK_EX)){fclose($p);return;}return$p;}function
file_write_unlock($p,$Jb){rewind($p);fwrite($p,$Jb);ftruncate($p,strlen($Jb));file_unlock($p);}function
file_unlock($p){flock($p,LOCK_UN);fclose($p);}function
first(array$wa){return
reset($wa);}function
password_file($g){$n=get_temp_dir()."/adminer.key";if(!$g&&!file_exists($n))return'';$p=file_open_lock($n);if(!$p)return'';$J=stream_get_contents($p);if(!$J){$J=rand_string();file_write_unlock($p,$J);}else
file_unlock($p);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$_,array$l,$bj){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$Ke=array();foreach($X
as$W)$Ke+=array_fill_keys(array_keys($W),null);foreach(array_keys($Ke)as$Je)$J
.="<th>".h($Je);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($Ke,$W)as$Tj)$J
.="<td>".select_value($Tj,$_,$l,$bj);}}else{foreach($X
as$Je=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($Je):"")."<td>".select_value($W,$_,$l,$bj);}return"<table>$J</table>";}if(!$_)$_=adminer()->selectLink($X,$l);if($_===null){if(is_mail($X))$_="mailto:$X";if(is_url($X))$_=$X;}$X=driver()->value($X,$l);$J=adminer()->editVal($X,$l);if($J!==null){if(!is_utf8($J))$J="\0";elseif($bj!=""&&is_shortable($l))$J=shorten_utf8($J,max(0,+$bj));else$J=h($J);}return
adminer()->selectVal($J,$_,$l,$X);}function
is_blob(array$l){return
preg_match('~blob|bytea|raw|file'.(JUSH=="mssql"?'|binary|image':'').'~',$l["type"])&&!in_array($l["type"],idx(driver()->structuredTypes(),lang(7),array()));}function
is_mail($xc){$za='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$mc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$Zg="$za+(\\.$za+)*@($mc?\\.)+$mc";return
is_string($xc)&&preg_match("(^$Zg(,\\s*$Zg)*\$)i",$xc);}function
is_url($Q){$mc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($mc?\\.)+$mc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$l){return!preg_match('~'.number_type().'|date|time|year~',$l["type"]);}function
host_port($N){return(preg_match('~^(:([^:].*)|(\[(.+)\]|(([^:]+://)?[^:]+))(:(\d+))?)$~',$N,$B)?array($B[4].$B[5],$B[2].$B[8]):array($N,''));}function
count_rows($R,array$Z,$Ae,array$r){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($Ae&&(JUSH=="sql"||count($r)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$r).")$H":"SELECT COUNT(*)".($Ae?" FROM (SELECT 1$H GROUP BY ".implode(", ",$r).") x":$H));}function
slow_query($H){$i=adminer()->database();$dj=adminer()->queryTimeout();$qi=driver()->slowQuery($H,$dj);$f=null;if(!$qi&&support("kill")){$f=connect();if($f&&($i==""||$f->select_db($i))){$Le=get_val(connection_id(),0,$f);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$Le&token=".get_token()."'); }, 1000 * $dj);");}}ob_flush();flush();$J=@get_key_vals(($qi?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$_h=rand(1,1e6);return($_h^$_SESSION["token"]).":$_h";}function
verify_token(){list($lj,$_h)=explode(":",$_POST["token"]);return($_h^$_SESSION["token"])==$lj&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q,$cc=""){$qa=array_flip(str_split(compress_alphabet()));$y=strlen($Q);$Vj=($y?13*($y-1)/2-$qa[$Q[0]]:0);$Ka="";$Nh=0;$Oh=0;for($s=1;$s<$y;$s+=2){$Nh=($Nh<<13)+$qa[$Q[$s]]*93+$qa[$Q[$s+1]];$Oh+=13;while($Oh>=8&&$Vj>=8){$Oh-=8;$Vj-=8;$Ka
.=chr($Nh>>$Oh);$Nh&=(1<<$Oh)-1;}}if($Ka=="")return"";if($cc!=""&&function_exists('inflate_init'))return
inflate_add(inflate_init(ZLIB_ENCODING_RAW,array('dictionary'=>$cc)),$Ka,ZLIB_FINISH);return($cc==""&&function_exists('gzinflate')?gzinflate($Ka):inflate($Ka,$cc));}function
inflate($Ka,$cc=""){$Ye=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$Ze=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$gc=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$ic=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J=$cc;$G=0;do{$jd=inflate_bits($Ka,$G,1);$U=inflate_bits($Ka,$G,2);if(!$U){$G=($G+7)&~7;$y=inflate_bits($Ka,$G,16);$G+=16;$J
.=substr($Ka,$G>>3,$y);$G+=$y<<3;}else{if($U==1){$ff=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$jc=array_fill(0,30,5);}else{$ef=inflate_bits($Ka,$G,5)+257;$hc=inflate_bits($Ka,$G,5)+1;$E=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$Gf=array_fill(0,19,0);$Ff=inflate_bits($Ka,$G,4)+4;for($s=0;$s<$Ff;$s++)$Gf[$E[$s]]=inflate_bits($Ka,$G,3);$Hf=inflate_table($Gf);$af=array();while(count($af)<$ef+$hc){$Li=inflate_symbol($Ka,$G,$Hf);if($Li==16)$af=array_merge($af,array_fill(0,inflate_bits($Ka,$G,2)+3,end($af)));elseif($Li==17)$af=array_merge($af,array_fill(0,inflate_bits($Ka,$G,3)+3,0));elseif($Li==18)$af=array_merge($af,array_fill(0,inflate_bits($Ka,$G,7)+11,0));else$af[]=$Li;}$ff=array_slice($af,0,$ef);$jc=array_slice($af,$ef);}$gf=inflate_table($ff);$lc=inflate_table($jc);while(($Li=inflate_symbol($Ka,$G,$gf))!=256){if($Li<256)$J
.=chr($Li);else{$y=$Ye[$Li-257]+inflate_bits($Ka,$G,$Ze[$Li-257]);$kc=inflate_symbol($Ka,$G,$lc);$gg=strlen($J)-$gc[$kc]-inflate_bits($Ka,$G,$ic[$kc]);for($s=0;$s<$y;$s++)$J
.=$J[$gg+$s];}}}}while(!$jd);return($cc==""?$J:substr($J,strlen($cc)));}function
inflate_bits($Ka,&$G,$_b){$J=0;for($s=0;$s<$_b;$s++){$J+=((ord($Ka[$G>>3])>>($G&7))&1)<<$s;$G++;}return$J;}function
inflate_table(array$af){$R=array();$eb=0;for($La=1;$La<=max($af);$La++){foreach($af
as$Li=>$y){if($y==$La){$R[$La][$eb]=$Li;$eb++;}}$eb<<=1;}return$R;}function
inflate_symbol($Ka,&$G,array$R){$eb=0;$La=0;do{$eb=($eb<<1)+inflate_bits($Ka,$G,1);$La++;}while(!isset($R[$La][$eb]));return$R[$La][$eb];}function
script($ui,$oj="\n"){return"<script".nonce().">$ui</script>$oj";}function
script_src($Lj,$Tb=false){return"<script src='".h($Lj)."'".nonce().($Tb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
on($Jc,$Id,$ua=null){$va=array();foreach(array_slice(func_get_args(),2)as$X)$va[]=json_encode($X,256);return" data-on$Jc='".str_replace(array('&','<',"'"),array('&amp;','&lt;','&#039;'),"$Id(".implode(", ",$va).")")."'";}function
input_hidden($D,$Y=""){return"<input type='hidden' name='".h($D)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($D,$Y,$Ya,$Ne="",$b="",$db="",$Pe=""){$J="<input type='checkbox' name='$D' value='".h($Y)."'".($Ya?" checked":"").($Ne==""&&$db?" class='$db'":"").($Pe?" aria-labelledby='$Pe'":"").$b.">";return($Ne!=""?"<label".($db?" class='$db'":"").">$J".h($Ne)."</label>":$J);}function
optionlist($vg,$ei=null,$Pj=false){$J="";foreach($vg
as$Je=>$W){$wg=array($Je=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($Je).'">';$wg=$W;}foreach($wg
as$x=>$X)$J
.='<option'.($Pj||is_string($x)?' value="'.h($x).'"':'').($ei!==null&&($Pj||is_string($x)?(string)$x:$X)===$ei?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($D,array$vg,$Y="",$b="",$Pe=""){static$Ne=0;$Oe="";if(!$Pe&&substr($vg[""],0,1)=="("){$Ne++;$Pe="label-$Ne";$Oe="<option value='' id='$Pe'>".h($vg[""]);unset($vg[""]);}return"<select name='".h($D)."'".($Pe?" aria-labelledby='$Pe'":"")."$b>".$Oe.optionlist($vg,$Y)."</select>";}function
html_radios($D,array$vg,$Y="",$hi=""){$J="";foreach($vg
as$x=>$X)$J
.="<label><input type='radio' name='".h($D)."' value='".h($x)."'".($x==$Y?" checked":"").">".h($X)."</label>$hi";return$J;}function
confirm($C=""){return
on('click','confirmClick',$C?:lang(8));}function
print_fieldset($t,$Xe,$ck=false){echo"<fieldset><legend>","<a href='#fieldset-$t' class='toggle'>$Xe</a>","</legend>","<div id='fieldset-$t'".($ck?"":" class='hidden'").">\n";}function
bold($Na,$db=""){return($Na?" class='active $db'":($db?" class='$db'":""));}function
js_escape($Q){return
str_replace("<","\\x3C",addcslashes($Q,"\r\n'\\"));}function
js_escape_re($Q){return
addcslashes(preg_quote($Q,"/"),"\r\n");}function
pagination_href($F){return
remove_from_uri("page|next").($F?"&page=$F".($_GET["next"]!=""?"&next=".url_escape($_GET["next"]):""):"");}function
pagination($F,$Gb){return" ".($F==$Gb?($F?"<b>".($F+1)."</b>":$F+1):'<a href="'.h(pagination_href($F)).'">'.($F+1)."</a>");}function
hidden_fields(array$uh,array$de=array(),$nh=''){$J=false;foreach($uh
as$x=>$X){if(!in_array($x,$de)){if(is_array($X))hidden_fields($X,array(),$x);else{$J=true;echo
input_hidden(($nh?$nh."[$x]":$x),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),($_GET["ext"]?input_hidden("ext",$_GET["ext"]):""),(isset($_GET[DRIVER])?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($b,$Nh=""){$pf="max_file_uploads";$qf=ini_get($pf);$vf="upload_max_filesize";$wf=ini_bytes($vf);$kh=ini_bytes("post_max_size");if($kh&&$kh<$wf){$vf="post_max_size";$wf=$kh;}$xf=ini_get($vf);return(ini_bool("file_uploads")?"<input type='file'$b".on('change','fileChange',(int)$qf,lang(9,"$pf = $qf"),$wf,lang(9,"$vf = $xf")).">$Nh":lang(10));}function
enum_input($U,$b,array$l,$Y,$_c=""){preg_match_all("~'((?:[^']|'')*)'~",$l["length"],$lf);$nh=($l["type"]=="enum"?"val-":"");$Ya=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($l["null"]&&$nh?"<label><input type='$U'$b value='null'".($Ya?" checked":"")."><i>$_c</i></label>":"");foreach($lf[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Ya=(is_array($Y)?in_array($nh.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$b value='".h($nh.$X)."'".($Ya?' checked':'').'>'.h(adminer()->editVal($X,$l)).'</label>';}return$J;}function
input(array$l,$Y,$q,$Ca=false,$Ij=false){$D=h(bracket_escape($l["field"]));echo"<td class='function'>";if(is_array($Y)&&!$q)$q="json";$Ge=($q=="json"||preg_match('~^jsonb?$~',$l["full_type"]));if($Ge&&$Y!=''&&(JUSH!="pgsql"||$l["type"]!="json"))$Y=json_encode(is_array($Y)?$Y:json_decode($Y),128|64|256);$Mh=(JUSH=="mssql"&&$Ij&&$l["auto_increment"]);if($Mh&&!$_POST["save"])$q=null;$zd=(isset($_GET["select"])||$Mh?array("orig"=>lang(11)):array())+adminer()->editFunctions($l);$Ec=driver()->enumLength($l);if($Ec){$l["type"]="enum";$l["length"]=$Ec;}$b=" name='fields[$D]".($l["type"]=="enum"||$l["type"]=="set"?"[]":"")."'".($Ca?" autofocus":"");echo
driver()->unconvertFunction($l)." ";$R=$_GET["edit"]?:$_GET["select"];if($l["type"]=="enum")echo
h($zd[""])."<td>".adminer()->editInput($R,$l,$b,$Y);else{$Kd=(in_array($q,$zd)||isset($zd[$q]));$kd=0;foreach($zd
as$x=>$X){if($x===""||!$X)break;$kd++;}echo(count($zd)>1?"<select name='function[$D]'".on('change','functionChange').on_help_value('^SQL$').">".optionlist($zd,$q===null||$Kd?$q:"")."</select>":h(reset($zd)))."<td".($kd&&count($zd)>1?on('input','skipOriginal',$kd):"").">";$se=adminer()->editInput($R,$l,$b,$Y);if($se!="")echo$se;elseif(preg_match('~bool~',$l["type"]))echo"<input type='hidden'$b value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked":"")."$b value='1'>";elseif($l["type"]=="set")echo
enum_input("checkbox",$b,$l,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($l)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$D'>";elseif($Ge)echo"<textarea$b cols='50' rows='12' class='jush-json'>".h($Y).'</textarea>';elseif(($aj=preg_match('~text|lob|memo~i',$l["type"]))||preg_match("~\n~",$Y)){if($aj&&JUSH!="sqlite")$b
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$b
.=" cols='30' rows='$L'";}echo"<textarea$b>".h($Y).'</textarea>';}else{$_j=driver()->types();$yf=(!preg_match('~int~',$l["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$l["length"],$B)?((preg_match("~binary~",$l["type"])?2:1)*$B[1]+($B[3]?1:0)+($B[2]&&!$l["unsigned"]?1:0)):($_j[$l["type"]]?$_j[$l["type"]]+($l["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$l["type"]))$yf+=7;echo"<input".((!$Kd||$q==="")&&preg_match('~^'.int_type().'$~',$l["type"])&&!preg_match('~\[]~',$l["full_type"])?" type='number'":"")." value='".h($Y)."'".($yf?" data-maxlength='$yf'":"").(preg_match('~char|binary~',$l["type"])&&$yf>20?" size='".($yf>99?60:40)."'":"")."$b>";}echo
adminer()->editHint($R,$l,$Y),(count($zd)>1?script("fire(qs('select', qsl('td').previousSibling), 'change');",""):"");}}function
process_input(array$l){$u=bracket_escape($l["field"]);$q=idx($_POST["function"],$u);if($q=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?idf_escape($l["field"]):false);if($q=="NULL")return"NULL";if(is_blob($l)&&ini_bool("file_uploads")){$ed=get_file("fields-$u");if(!is_string($ed))return
false;return
driver()->quoteBinary($ed);}$Y=idx($_POST["fields"],$u);if($Y===null)return
false;if($l["type"]=="enum"||driver()->enumLength($l)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($l["auto_increment"]&&$Y=="")return
null;if($l["type"]=="set")$Y=implode(",",(array)$Y);if($q=="json"){$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($l,$Y,$q);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$gi="<ul>\n";foreach(table_status('',true)as$R=>$S){$D=adminer()->tableName($S);if(isset($S["Engine"])&&$D!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$qh="<a href='".h(ME."select=".url_escape($R)."&where[0][op]=".url_escape($_GET["where"][0]["op"])."&where[0][val]=".url_escape($_GET["where"][0]["val"]))."'>$D</a>";echo"$gi<li>".($I?$qh:"<p class='error'>$qh: ".error())."\n";$gi="";}}}echo($gi?"<p class='message'>".lang(12):"</ul>")."\n";}function
on_help($aj,$oi=0){return
on('mouseover','helpMouseover',$aj,$oi).on('mouseout','helpMouseout');}function
on_help_value($Ih="",$Lh=""){return
on('mouseover','helpValueMouseover',$Ih,$Lh).on('mouseout','helpMouseout');}function
edit_form($R,array$m,$K,$Ij,$k=''){$Oi=adminer()->tableName(table_status1($R,true));page_header(($Ij?lang(13):lang(14)),$k,array("select"=>array($R,$Oi)),$Oi);adminer()->editRowPrint($R,$m,$K,$Ij);if($K===false){echo"<p class='error'>".lang(15)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$vc=false;$ik=($Ij&&!isset($_GET["select"])?where_columns($m):array());$wb=(count($ik)!=count($m));if(!$wb)$ik=array();if(!$m)echo"<p class='error'>".lang(16)."\n";else{echo"<table class='layout nowrap'".on('keydown','editingKeydown').">\n";$Ca=!$_POST;foreach($m
as$D=>$l){echo"<tr".($ik[$D]?on('change','whereChange'):"")."><th>".adminer()->fieldName($l);$j=idx($_GET["set"],bracket_escape($D));if($j===null){$j=$l["default"];if($l["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$j,$Jh))$j=$Jh[1];if(JUSH=="sql"&&preg_match('~binary~',$l["type"]))$j=bin2hex($j);}$Y=($K!==null?($K[$D]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$l["type"])&&is_array($K[$D])?implode(",",$K[$D]):(is_bool($K[$D])?+$K[$D]:$K[$D])):(!$Ij&&$l["auto_increment"]?"":(isset($_GET["select"])?false:$j)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$l);if(($Ij&&!isset($l["privileges"]["update"]))||$l["generated"])echo"<td class='function'><td>".select_value($Y,'',$l,null);else{$vc=true;$q=($_POST["save"]?idx($_POST["function"],bracket_escape($D),""):($Ij&&preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Ij&&$Y==$l["default"]&&preg_match('~^[\w.]+\(~',$Y))$q="SQL";if(preg_match("~time~",$l["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$q="now";}if($l["type"]=="uuid"&&$Y=="uuid()"){$Y="";$q="uuid";}if($Ca!==false)$Ca=($l["auto_increment"]||$q=="now"||$q=="uuid"?null:true);input($l,$Y,$q,$Ca,$Ij);if($Ca)$Ca=false;}}if(!fields($R)&&driver()->primary!="")echo"<tr>"."<th><input name='field_keys[]'".on('input','fieldChange').">"."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($vc){echo"<input type='submit' value='".lang(17)."'>\n";if(!isset($_GET["select"])&&$wb){$dc=($ik&&($k!=""||adminer()->error!="")?" disabled":"");echo"<input type='submit' name='insert' value='".($Ij?lang(18):lang(19))."' title='Ctrl+Shift+Enter'$dc".($Ij?on('click','ajaxForm',lang(20)):"").">\n";}}echo($Ij?"<input type='submit' name='delete' value='".lang(21)."'".confirm().">\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$y=80,$Hi=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$y).")($)?)u",$Q,$B))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$y).")($)?)",$Q,$B);return
h($B[1]).$Hi.(isset($B[2])?"":"<i>…</i>");}function
icon($Zd,$D,$Yd,$fj,$b=""){return"<button ".($D?"type='submit' name='$D'":"draggable='true' tabindex='-1'")." title='".h($fj)."' class='icon icon-$Zd".($D?"":" jsonly")."'$b><span>$Yd</span></button>";}function
copy_icon(){$zb=lang(22);return"<a href='' class='jsonly icon-copy' title='$zb'><span>$zb</span></a>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('!c0=@iDZ*tV?H*{U)[Q;B/1SR=Dh9&hJv;rrHHN,.V&KGmzhDwb9E:tfItN#CwUSwX?Xyeqi5d/N>]A"1lTaK
Tx^G#)>.UM~&(MUO{shFwKG+g4,>C*S:
f1hRcL)KhkmZFtH^qWCMBf7tZ{.#f{8V6<
#Nk9.jSA&0km
lxTc6$tVXF.+.*cJeW<wG~51NPIP4xT,`5Fw(3!{(~-,9<s}YqWT+L%^[i[s<&8ErH[O8<a)
ljb
$LurL4t]W%a>H/b
X/{EMCz:LXX((.yD>6A0+]t%ACU_
:"Bp%c=`r4T.#6G1(p
xo=TMNIiX,W0G-OEkD}^/L"3iRuM0)KZQ^aWB9dsO%0WmcO<LgliJIDSwKw0uo4(Piokl7g)}Qq_R"C>
^,?D183n.@41e}1M3L@&rCG$;yG3^fAu1qCeb_`V5R)ywQ+^^}Y?,S-#YZFZG
*@I%I_vxm,Tu:<aGT4wdZr#t8h]Nq~_-mA_aP)C2W
3#$o
g`gA/T"apmp;"31><i"",jWq9Wx4|Kj$:Svf`fH`|l`L/=wn!GzOm+(2zYb@S?I6~Dgg51]s$GQ<f%*sZ)4os*u%H<]daIUU7+nOS>!R,?jI-ZyOTT8YA+<ro/FX
5%v%]D1&UG`Rk{"Wc.*PH+X"!Vb@SA=T#6)+N_
VgZ:[vm-?:-d-#LVMbB`M*
o3=!8PG}PV45(W`#.!4Aj#=
`|=e];={gdf>3&l{-kM.$C*+s{3":?S*Zv4|Rl!*UYvBXH@}(A,#om09^h1i;#LHmj2,KUT]s;#mi|*91KjF]nE
u?>^sG`oFK
)Wofomi0<!n"hdYaSs6[44(o8rHBG_@1V2u@D_*jz/#ZgKg<,ob6)a>B~0
Nc9PJ]bx=7K{0`!<w~"{8gg&A2+L#$C,xw#&#5qLhH:Y
6oD1wS)Hu:z&]%$L:*RH&&hm9*p.)J&x-8E0z+soB4Y.o:5!`DtOyw7783CWgj
WZ%`ELhCb!<9!`!t;k@5]}^L$~J|@.agt}?B1>
;"ZGyF-kRn"BIwMi;iFn0;f?!>s@V%wLZZ[kKwyDKfGko5=+|UjHeZjXy;0;#G@L"d`Um3u4Z@)WU.Kf:>6w?u|8l*.uRy`amgR$8Nv?MAbetW1fZC=.a/i!<lm+CgiuJdI)Ig2l@6xS*[@!@B.hXtesj)KZ`"D(QZyUo#,afykRAvt+#nz?,6c9u&`9kdt)X35?[Y<n!"C4r!0$AJl3>+#H(mk1pQn,Z3ZZ]8D)q@wst)_4|I5f}dsW#hqo*!.
4#"_/$:mCAq.5UCVL;oIlfE&U`w!f9m@e?)4t)~-8Kr.@Bm$9-|,R
ult.W=H4(dSM?+2D
gapxO[e_/=:kYP05Q|i+[N_N-YHIAe)0I*>{8]&Z?!aMCh.,oL@6p&lY$/
U=Fds9>*<FAc!>,5<A*;C+!_3@O6|?
//8+>*;@Um0hT[y8<Yt,@dvwiU()maH>967;d_]`={>5EWy&s32*#uINV)k5YG"ekF2}hI1O:Mj?8&AG/j[.-n!P5/("uWRm`3"j5%iI+qc5SJ+:9eOv83%i]U%[V*dHY/2lm8EP@h:*ITM4#//"X9KhV|EJ;q*De_$X_uTkg^D"0(-oU$AjZ^;N#1fw]3U1a`)mkv^ymmdQDDS;q71|/~(_`/BNq++E<jkdNVV@mh?.W_4M<w=_(ybU80Bn/@V^N!54"@
H!U(`":dm[rVdms6(S],j-batnN&O(^ru_<To+HJ~-NHu&
@=6dl$NMZ6.-yJ1jkQMe$lOAr{RGpt_56jj]YcYfGIIoQ"Gp(LKTcE%
(#A-Ss>OBN92I3S^/SZ[Y{fR5U]f3~"At`%-@82:PR%ue
?eN{]g1_DyuG)bqfX_Qs^{78KXKDK$X3a[Ecg5g;AJ4X-!D6[i7{=;"[e<PUxWs#8`
A
"RPK}9NBKH69/U1HuFO6us<2>Oj!}^0)84[gIeYd+TUi:!R?Fdgb1)D%@K@H-J^5m+l!YgY?3xK#mU+#Qm]0g"V)XSM0|uEUQFXQBk%K.*BURB
10iC:gT=p:fn<{1$Zp5Ovs@e;!HiB|gIW:-0v}QHRYkl1<T*o2TX81n2`o1uT)@|7jq%"/7@G)frLDr8H3rf_Na<86aPUN*MUbTee4EdGd"1t=NaEF
;iySU?Y;v.4>
RP2b=1]Ad#4rICDk=x"Z`z?~X!`9<#2>$)ds-gMK0Ux00NP!8ZqDXXp3GXgn;Q/Yh-#qdOQ)S}u]q?W-CFEV(7eTC:uKZPMYS5$i,I
2[0ov7,YH0pH6R`n,nr(8U[C-nTMc4~8NY~#n])3`!4/vklG
N%[#+;)i29O[fww}VbQ]rKi%y-ZW>Gfjs~p(*;U,"!Nb?-U]j|.+n]tcTM&fIT9Txd(Xod^%"{+[N9i2wzya_4MaMv!mcFuYxzK2uWypf-Yk^aYCxviP2qUT6}5(x~cMiq^HyEAUC"wnB}[#@KK)25n!nubey@KY7vpFug_hT>MRR-PyeDx`nbnx+Esrx8K7s3mrcwqM21p|blg?r41.s2Fe>OEZ`k/JpHne&Us&nDhatAWZv[y&$@`;Qn@WmZnmpc6]Y4TVtMp;4DQvyF0k8pZD=5bWwf
&@wXiEVG3L~r9x>f
DJv/ymXPyEB_ctnuw=s~Cw_nhh`{Ej14p4<A."no_E=r?V>
X/mcPhtkK
SHlA[=3)jxBjTN?Fe=K3mXc[JfhI)O+H?s4z>nCQ1zsK7lTpOp/EyNykw~PCe>j:YBo!af6g2s.elsKdH90yhlW|`Ib;pXh[h8+thI)*3^^VMelUX,D7=E%gNYB|!Ui(a%<e,qo(V~,OVWBQiQa{E~JjL.l)Z)s~SM<;1@H4S=U(RF;7uOW[""8dG;qK-cOo"FZYYl"dR%j%1)P33!1_jb2My9WJ$HcX;,>3z#p{Vl.-7WAo(snORM+[dimjqqHs6f=}g$mcji$bJZ[:XSHOb>Xmtx/<NJHc]$bOxnwttNq~T+A~i&.dMyF@w]c8_/6O8XCKwJyFbyBn[4e~Mb[,2oL?
nw.DpU|W%F+RddY/Zd.3$0W!sX9Lx^%b7@mO2x}SPPcu5gdBS;rHC:7^tfOQRKVQD&s)9oM`LZ_q;f]]#43gQkr=Z_L9p.?`P=j.yac@GbLQ^f<GGZ~]6kya;<8:F@hcNG,P!41@F)/:1Qq@&!L?]UN!3MG]s[~OAU`#nEbRUKQ*1(wpi7z-+cg_!Rbg$@^7a,TC#U_Is_vM"7L=&1(@d.AN*2H2hG(a<4JcT4QrvvmyR9>VCK0L;+;0K/BTI7@[lNzAgjG:)Y5`IIGxr!$lWV1aWii,3F!/{`s13+P
WmZ/+Rx>/5kfH^HZw96+`+Kg
d{skqlg_Z&_>xhPZN4XZ]LMvK_DQt{mZ>{EvrNTg9Ym"-?2=4,a]XVx$<gc$#&rc`0c@w"b
iJmT:GD~_
_*u!sl`,twN*82)AXsWx_7Chyn=14#77aNK<%RuEZMtV5bYfhw2RC,-2"(L,y<0T4qAY:ww{Z~<,Y>D}(Bk4n+&c"gcS0j9VkYQ=uJ<^:uQ
et&&B>f3yfuUqMh2DNQD;mk?ce`(?3S-WN4Q1mQBHMO(Gt2+nYFK`tncD)YIlKodjnrl]=O|6G5>V:Ex=5w]75MD501&2,gCqF
k]B*OK&O4=sBXd&q8BwW5hdkg4S1j#0X,E7vh8)NNmp6R]1curHvrKxu59J$S:1L6A;Mx/840/;]@BF)AkbJ~D"3>/"SoFH+&mO[y"EbOnY$evn)/<+-->rA,2QHBqRdJk;W4j#^S7AHal}2Hr|mYOBF`,Xw!J(7+RrSuO[aRC^yfg`Y^qE:o;0d/Y9/CaT?ZW{B^F/S1p:<?qS(|,a_M$6L5UzkJJXb=!>?qKg`yn*Wr@Nf1l/UF^3^*u?/3yJi?`lUmesKe.$4kv5*Ca(`?m[EX0/aO>(xbesGcRlE_Tzf7d<XX`)9G1`d>6VfK@Id
n;HULv1;x]a+n|H5$GpIYWE/2@2bH*b~Euozf/VRryH=4fpDuUa&LMWGpnMtJfxRu!YDf^_3r-=3nMt_v[3Z9s%iTc/<AA
"jF:YJ4c},/bNbT^[Ekpl"UA;J%)h%kQ4b*56aD9FCwnmmTb59/A%AR`60iM=y%Qxs_XZ)XQQ8O8pc7Z4-$?i5EYH$c#?7_-Yslj.FL$Er7
c$p(GPo*@RfuCsbme7[v:$ZGKTl"_M_)Ym0h+QDWXq?4Eoh-R7bn~ga%7@ZBwnyMh7a');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('%OsbOb3V?!K0U*,j#-4V$+4lSl,oCh*02mX@fy~Y!-lFD?AZS5iE
nM`YKnnN5@7$,h]yHv0]"r/{_.;5=S+SNKE}<JYs`q%O%%)irj"Ua|G&>l)NqxPHIui")?!f$TF|nwt-nQCaG&Tzq)X$0a:"l<uhiWpN+Q>JUl.I??
0[m@%2{lZZ-SVaY0c(Abuipc;HrUB.?"L
&fe39+`O>CaP%DBGl_a;sKU:Vn{vUd)#z;(-4/lH:f/yqJRLo1D)]&Q)#F_Ex@I.Aoq!%P+x`#:u7a*NRit]e+S_#3_W;B1:p*qj1n&6tLeURFTa*Z%=PigZV?!E,M#fGWI7
Vby;v}uyiyNSk%!K32:q%~)Z7R]f7*[T1VD8GAHNE,gNAjPt3bJTq!),5tH82n<xEH5{06?o3=vyf/"d[Dx=^/`OW(R/VJpy<uN~pK
XY0h>3?PG;:6W2&H^g`XJac/.2vy_[sa[I@2XZ6h^)(qYAo-$5uc0Ep%,GX=n?^Dh<AHDPP6:^cBoLiHv;/&f"x+
Fxs2:m>cC)c>Lo
0T]2{suTY+[`^=g^8K@M"IJhD,eB]&O05-RUzKB;q=jP@t>t?wQJam-T
Ct4iGwsJeBb--L[GY@5KjZDe)KI2"iJ+I
sFktJV_tO_ae<,6L%wV]]G$83G65)NlCxcni0jK(!Hn+6;A/K;bfn9xSp=TVCsf``qH7Mimc,xAY3>O[u4w?fz&Fj
9f,[];aKusLC!8-;hiECD`(]x7[,6WvZQwb}-<xBhai*6z.x#y/,/,PfbzjJZY5<k)c%nD&#@k/fnmY$Bx2daWEELXWrfOnaM:!Fa_[qjXgtfwLcv6,3f~T:>3n3wR;MUKGkB;/1<=rsb.a0udo%x4L7HAUd8(4Q+6[S3/m5?BQNG}h9#D7rZ(C[A#`5XL+tAmR_k4;*wtK-0+ixONPclR9
9Q2)1cdU5,ODCdgYd!N6');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('$c4]`nsWl1ptWOv_h:.%y>(B8Jhsf@ooc^S6O.cGC9BHMu=?3)
3[.X?=Wv,ZyTxSc%"*Tj_GrEE9;FU$:J.f=X0d>JYBIVZj]D<aA3aJq*XnQqxcw)y-@0VgkGu?I^TmUVgb:3EfFdr)MNb!A,_(k]_9iV_"_(G,09n1q_nV?v_Ya}yH922HAvE)Q-Dca{7S&0T[#Dtk/#Bl_W&wjz.9y.XC]Yq%Jhb/AiQuuuIe`
H[3+1Q4&&~EB[RStfa@oiB?KrvDpHs&j#r_LJL`Fqt*g"xA[A8
[y~qK:8it:6_]`i&KGku1;72%Z|S"<"lD2ac}UGi{G0+g.v*]W)w>x/UcKbq9g^FuU`Cx2;l0o]3G78`XOn?+m4h40J%^y[,j8/jf`[Aa
ekiT
]!]LVm5+_"kYM9f,H8q4W[Pz%8**gF[*;oMUB}w6uH@/HF4QT,aW)t1GER
6x,RTi^tAv3wj1,rPT[<m[V[
v7w0=&nC3$Jan|l=tdXcXut7wuK[MZEdJ8uZSC^U)zk-h]L>2"Klj]:[sKC>q*P}c9tr,ySzuv&+rPNW!Ml13$YQx$tt6nbASO!]:H[_(b&6Q[7Z6!1nLF,X<wye,d3tfh"JO4b_,akkbL-gTUfs!onu3q,>=ZgGf}1)_HCzB[dxj}I&?d-:=[TFTqbm
jTGDH<FQsW):/7(`sMu5uZF%cu6-t%h-V@
S`nB8s;7eS9Y;P7uI*f%<Sj0e|;fCM1ZmLc-<V^"Aum=T-Xb2$xhvH+nfh%401Vm*95qb&;?hropn>:4HL6I`dBm#J4P+uZc-`3:8IP~y!z"&FTsPyc)eTPqO=MXC+&9GQY90mSIR/pOr,:dR/5J4sBQ:>.K8KeH)L2LK#+4%~avx><sm6t%D,?CM[__KlhK1M^6OLZafEXTfCH!s2s8;6E[yix:0<]pjSEtL^,m=iG%f?UkP|7Z?U:wqHDPQ|&E@>0Xlxb2W@Uz#x*yPK(-S)eaNJ&Bfs,vb8K*yQQ|X}s<m-Q1=FGg1p<:F&ULI@>Q[e`8]J$im:dY#D,Oty]u%`GZ<wZD7Xjt0(^oqGR6X7Z0=W+;g[FPTX7mbv@m_r1ox}ydcD&K2!q8$f3E8nwpiXvn,vJByWtZ*SBob5n7z#
Soub+!!"]Tv[d!h"a``]uxL);"fsAF"W=cgDso{(r,~(hkY9Rh5@}3F25%HS?"~>G!7C
%"uliWhZ[zfhK`&ZgH<Be(t5m]MN&m;9WMsZgD#j2/_(6tud34LhZa+*Y~HlkEP}LYr?I@k?[n=~Id2|"f]`<2x7^AP:DI%9+IOd!WwG+"""F_;a@hkicZ:FW0u@]upbdFl1*38IBbq
?l(~:S0P=gb<GY^t<5Ask!qJ+|d&0FR)XOV5eBb_yI?dmXVklmow;eIhOUyCO.K:RPg9G$j
`7se]e9iOE?w/B(R!V[CX4"("i2i@Cx>R9m+&8+Np3Y?k#3)JB_BEO*X1q?83pC
=aF2cPlq-e&L,
:~g1O(5WtRKTi$P:D:6-@!!7=oO/moc.$>c+]/hvY8Y*6wf_,+.k]84!J"
BcX&_j6iUX8-L;i(_oc
":03Pk]FuUv/1o9Cq+WNKqm74;VqvrBBoxm)D2@G_YjGVRky>9ao0pN;ZyR,*e/,.mchZXRZiND5i
E"R3/XkYDa!("eoNed99?<?rk_#
iVH8:NO({5N"18#lhc)O}F~;jr2kI!-0>Cr+lgIabnSY*oipd7oQ.o"#uh)+>"uIeuQV)jAuy3s5?twVBbXCdsrOQ&Qk(ayr<H3if)vVX.5ITL*^F3nbnPBBZ7ajgZ0$vMz?9s:@+JJbXAVS/fu7V>rRAKR)k_9`2oW"nT[fFAdFb
nfog:`79ePeSYtN0|(@-./s.v%_RmkF/&XLvK0VP1&b"i_<ph?o_-5h&:wB`lcI%,$7Oy?E;gs{en6s7o4mRS_=bi7KSrpZT=0c%ogvtFm#tC9kQ}3NamW]apPHV/#?biJP&JSw5O+a+`a[":"&[oMC2^D^IBZ&eua?IiB`H*N80:Wyg916?Gs"X0T,FX!]EJT(lIu{M0GjVP;/1F^J-Gb:WllMX3/_i/>RR:_*slBC6TlZCpr0k^yrGk6F#zwT
5NyFB@&AFGK&}sIdH)Z.I5E^tgYyVXe]%aac:aY#$Z/NGN21r0Z/vP;@%t/miyd_J^?.,ekJFXQ9V!f(|uaIdBA[cqr"eQ|<kX"OSE?C8AR"{Ory90uHYdR8(eh;_%U!2j.$6*}AVGI(7-d%5NFhUfL!(!^@CVR_h;znlt3qIg7pj0+8u"?S4rxvTN-o*;yT&9$PLZXT^T4=wI#rfRv+K&oTL:%P#_65sg#B&->[aYmIFSO),8W_qxe)qFk,)_M8ktxk"(|Oe/VC"DeCeu:yn!F0n+*&%&?;][YF?*sd<l)vWe*b5W$VZ6eC>Gx4"8+ah4^-J
n25m~hPP:vHJEy^l%c{?+rP&{c@DTcfm}au@$*6T@Kji)pM^*?ChLp6^q4wa1`vW&12T}HDNc_xr$5ngo_&S^7&#~nx<G;(BBeLmq/3>MY70.fj#ABt3.;*7!f&gM8+Ye!_1vc6pO9~pj(0Ek.xM[8*[Q.Z8&J9W|#4j$TWo(^V1Vo+Ktb3i1hP1hLUqcWCr2<r:6d+EK5PJ+f=U^e;D]lt,%Bq8zrf33%E!<_P+lQKg/SD%/wS_BS{x#?c`<*:kL*/5Rr^2}7ePX;w&T5LTYE<XHi!?nN^Xf,:*@iWD$+;0Lkk@IGn)3SUiqQL)b*(?{)q.DY/+j>jQUc>:E?`qx5OsjF,5I!P_W#i8.v~;0-gYJBFn(rg+V9o]mcEU<X#UF%_$fwL4aU5`Oe~bk$gqe)MU9V
$%FQc!`&W{
lr%*{wY@2ExA,V#x"@?XGZ@bf<lgg!/:&)Z+dh.*P5_kufS_7
<9;44lAyM+A,<!ct#Gnv~Vf?_yj]9LtEu[*)^JNY[o`QNRc6>UQldV;L;`;.
v`g$k
$f
$/-Q}N:3K[>uE#5O|Oinj2+0BP!cJ;^7/H,ssnkBd/[R}H&JsK%@n=bD8L@x`W)w."?,340*%8;#)uAq1!;))p_!n<%ni3Uo:7a5E>FT~.NZjZ;!f8=uZW9mU1zDyls0+9/"c0[CJeb#L:5C+Z5+O9J$+t)A48NE0D`lr^T,6^i^}wm98fNkUdg6bFdC,$LmQWoj`J;*XocC=w8*Wi^VupvA,BN8UQX&b*F-:xZ5j])T#PVpG(1.~Q;=S!&NB__=k#`C:2Rg)4;,$"d]-JasM[qu)**cA$9!NdKn7@N3@E-()VwmNiP8bNvNLV+abS)[[s:BWw8de,ZUpftEbm=)x?jk?+PG)^qO!M:b*UC$/_/Z__[=qT!6iq;
|!YW4[vC4
#?nRJG.t3k0n.YWkX*)1zGd+ol<dj:!D<.l,9)E>RxG",FBGUi)OoV(mwG(,?`$0
?g6:2wPTUSR+0aAEUlOaO"&F_yZL<65=WXPI5G*"a(,8PCYXvGiGyyIZH_xCI)5JNg_VeEM0+&n,y}:`.TL<W4(bX%5mb(]ZQ{a2x%E6^K-NJ*p0A@rPuXDt/U167_j8y1[R8I^;db2A%)^W+0:~Eu2fW$olA|c+1R:3KeO/^9`>VH>}sx48fzjE=ya:7)DP91</
IUA+.,S80mb-7^{YS%JPsgCd<s{B81Zf9F"K-C>l,.?C7jWq`,<K$myE[ON5u1<?$y7.P,->,4ma7_B
YOIdlna:sw
MDuC8y64K%VC6y.`a}yDe.KL8{C9oU]KO-U.aC#mBp+NaF*!.hAq&Cm&XB1XbD=;
eL5)C5t!}b72K_VP"l2F/hLXXffHHkGPaQeFFWZOJm#WaGLF;>:WXn2j`RM(GV^A36%YxbYsptKiBM{c4y?xq$E5qqxNb;gor*gE]0+T^IvNc96^0sq1{<:1#x%NR>bO*d{Ao5i<^"gE_ouy-3r8<SbS!/B+Eu=OK^{yJ3>f&lBk>pLM#(="v>WddPU&T4m@oZ{d0_WR"-8_(F+7ITpj33^n"9,]}7(>nUnbLyWdNL$3QPO_/thxF(|!{R2TVoMC!1;Ea*O<X?d0/*E0Yf@^YxfYV%xG91(!qP?>9i*As
3K+8ebKu!%c?>rne@)bSu,08[pl#SIGZ[np(=yp#9)I(J6D9iro(^(@Gv;*9SO}>PDZ^/L:6W*]SLwEAvB`K]K{y6*Uj]e%^V0g]i&zFUL=u7n+g]W7m|8D){jO+vCwTe2{69)+ySGm<n
{0c;sQ+q,#(>i^IL`dD),TtA@9"Kv`/?El$,0k&<;v1?v:BDZy(uJOH.#
G&k^>5
]mtfLphZ7Y"I(0eU5*b1]7$2f[Xd<cu&q"7HrfD:$90lgK`@,/yEH{TiuH[,Oj2V`UCrTF(XvwN|y6OI1
-(&Q[W)N!T#;nhXW$jTVv/8v+VR9r3D[_&$]Ua&>_2_<7.&1E$<gGG,l!gWEqX(Jy]&V
7%p_T+*F3(`C{`dx9#q%
16`Ay<6$m)9wEu5>O;a
H`lJJ>nexaBG%_B/JVg#mKbSedq
im_i$?)Ml4D(U)8rB3]?#XWDmtQ%_RggxOQ[^Z@`qnUE_UKLjj`9oX5puQY"IlbMa0HxdS9+jKe
RHD+JgJ&Q+RtuR"I3bb&
/LQY|b-b]uvEwg1!ELohh2,gBrW#ArHmxXs1>3/A$AJXHobi,yMYl,UgyK.gP5`Nv/E_jFlF|b[Yr&IO_IR3EqN0;jvaX-uA>^F<&t<&lDuH!k>^3ZkmI_w(<42hLdC22Rp/`_-YSZuZ9+$NQ(jH<U~5Xm9l+F3j1)sYm.=-}%5.IpWkc
zmMgh^o_
f>Tl_OLR?AU3N0V+#CrV3jt
@B[/+11Q4X+eM&HMss`JL5`K5X/8!2N*v5u)]iDaY:M(c*M[vO/7a(O0j>vGXvGd
P?dn(.//Zqu0,_xsjDA*bt[)o[&2X:8:NYy=!7KGpMaMs%}yeyy!J"CVW.6pQLRHX2Q[eqUQH;U)C;kj6".-Q)<(Eg*%uDVH4xCBm"X@|#e3*L8Q"c%N+c|iD)3FK0zXfg;m*T*5Wq_ivEi4j!eicSpqoM1FT/oha+<#dg6ZRj,hi(}J"_C[GxE8LNo-9^OE=%h73h}P)Y./k
3_nR~_b>,&f>BK(V$9.X1h4Re^:UGPRH~GCka,is-mu<,X~hV&+(UX,&Q_D<@H3K"2sZeubF%7!dKDu`J6Sb6/qCgD{+Th<2:uB;3UuVV*F>3sp<3-$NBSxIgHg)PbItNIMk~J{MscMynGhnV*rj%I00!PSWyrrS
>aauDl@R6$i-h;-FE-$ysHS)T-k*]UUMH%qLpxb(]7^]MtvFc/=b-sLDv+VD!)eO:@kj;Nw/PqX
yl&O<P^&@6<jYs:Blva5?(5PX<aZaiP,)du!37%e_OKZvi,>WFWxnfg>s5mj`n7$vtE0W
PaXkC]CxUEO^a_R/Pn*l[~eAZPX==.cUE]&
]T;Z`3NC732o1%?UP/[G?GAE5$#Q=4(Xa",cEaZ8Jqdy2!:H4c)"E|T^1yEDBSrHCyF.Z?V0dxE_[-sk1B
clDski2xuH%B2U/[:#B0(6$<G_rK0ojYNp&H;NDG]u}"ap/]CU=5A2IB|UeJxpD2)(G"E8o8Dh3E<Wrda%
N#"Cu`7Yk,geL=f(e[-
G@?t2RTim#,bFCp$qc3E0F^m.X$LoQ5,ygMfc=!0SXf,7(eCLoM6%8HAn&(;-Vk%B5]-AUg4jTdOdLfU+rSvG5jE:|DJu=nLjsw;iI;i:RS5B2O#:%-riqBvao@49WlwtY)weIrtFwE"V@M9+Cd>GcP21ZT,x8bn.WFJ2Y(Bt"3(cbAF6PG?*CKp%w3[GGjTc>-f6;.nB/>@!^7mp!kX*ApVfROJ[F+=-hkE.:m<S7wh)$UN5~ByYQSGL=q7oU&uQGSrRuPB8O-<mPFq2}v$":(i8fPh1&E#w
+lfx]d^E?8ppBl-"kq[>QFHpDcdG&8JjfR.LkOE^/R5b.?
i"U>45/hh7NID!5eAV+#Yf7b?Q|*/$-oNXqjB":O.X*-icZ8186)6*FsC:KX]CRlXRB+>mem:q=3#yyWjuhVl-<l9sK&@
0Z{_faJk&L&v-4`x?SmR;#f8P;&DAG7>MN+NRN.l5ohhtSe7_e5sD9Xs@O@94P#d4[zC|#VWSPm6E=wm1]u8enX29<9i$5>k6kZS8bu^
R(jteK??Wh35N8/l`@Q2MsDp&K*r%=7RPx=D
04{ag)pG;V(w*^d7
0G[<LvZqd%UTp7KFdP8"LikLbc1^c.d$emh;&2#zRA`~@dL6gQ+C.V(%e}>K1bj/v@(u6O]}5OG?,%iw:P]b=:*z[.^~Q}%1Z}wb!},;R/Y<r:g4_<=}b<hneVm2t6w9wx[A0jetpx2.[t>jHKB5Mj]T9SMi.BV8ds6WuhP3k&$4(QT7y0ZUQAht=gj}ZOnH*SEk-Z2KJp!_Y7]wD1J1"|TdGMf_@`w(=*/D(E<AULJ-MgX.PR<Gc#YXv1#[VM[1&7i+Ziu2)By%M(x<khZCS#lVdC8cBz
kG12VDkau*)1"#4ES*mJ0Wf7^[elz9q".THy*uhgEZW*YZu0%^=Cs:jAkDO4r6GSl@fnxprU6!V#a+mH%UIjnIJ&5V,f75#Abt"*ual9.A[[98/F"Dw
NVYho^293i!lJa/.O9o$,.Ks_5p
zPasn]a9l/kWd
{u[8FVbG0eBwFHCmv[z]2009X^;Puv
6~*/v*S/pM@"YC47)wX&J)SMYj-kr30Xk$-IQC"BsW]eoP@;o7<B;2^T%+E=Q>8`S7oBGFMssFj7eKI2SM2=v,6ai#?m%^Yv?eqR)+?u5(Bcrhnv&J->o]"iD78q#pO_q?L"u*U"xB>k9YGy
x#}ff0wj7?HfgAn*q=[53MAk4-T6$mYDwABaJ-UZTds#s<W&pIU`9WkqOI49BJe07Vb3AEZ0JCp<S3s
L=mA+f]itDR2f,menTLR@4%@x(8D<vp=x2w,=SLBQ;0U+4X
$21a$3=s?GBuNR^"N)~$y5O"q1w$~F*0lv_#|YF-#F:+>?3
aOD!Z*0/**enZI8d&Ik:t"]H@20B^o=LZ"l/TtunLCOYHu?XXkfqskKZRkg5f4NOxD6?d4D(<"blmb2+x"f?46
$%mNQs<)AqNbX4<!;Xkt%$&yJ^`v5H15*yi=?YrZo!A(Z|tKIz.`yS-<.GUa^^VbNz/huHRR,^8o>m<`Kqh`,nnItHb;)B:knHf9+iVo#hGOkjRdq^ZzCS^{*j`g,Bxdcs@d)h7[_<D<l._HhugO:=p9W!Ur5z+Slmvfkvu#3#uy`C".b2D!-/bMoBeoD>#hN6+r)QETrGdoVk/Mm.qoD4@=&?wO)(D,5i]_,{,RB0=X?,tPaTF*S]UtU4AvQ*50sNK,?VY!?0pP(ZJ8c$`_gww=IaTZ:F9#^>"sGwu2@/gF7Owl2,/VT%<<KjF&IU$nfKV[fAKvAZS0s~mWQ`[-L7azaMGRmP5Vi/Afn*E3945&3SJ=Zx:fG0?A)@15*/l!Ru7Z+`B#7D_ToMQY,SC~+l:^t^24r4yUA/i^x2TZ2"Uw1SDHt>;aCD?$-~Rm+o2gv}FxhnXTk<b@
Zo!L#_}H~E:*me>iT!0:is,ewa
gGTj0SDNW&ulC!?8?8,!ljnoW6=XK@%^>Z.;vVFsKJ(K8{f~/,<qFKpZ&:.0v;AvMRw>pGy}y%yeFYL|+Sm@$]:v)6]-".R|3^E4vF**y^o.oFt18,I7>0>GncxKf3xD(z"/@$dx,7)&BQUrCMWcN$6^#="#H~n;Z/,k(SX_Owys8&mE@)gdVpQ|m"xrEefIk.8EpL<CNq!d9{(TinvL[vaiaK5_.#"G)]T.FT/0PT[mvRg4;n2j.;mf3]D%a]sit|
QC*;8`wmyk/ZCv/Y56Q-ckcy:&s+5pvNR(gyWP{OPt/ZHgXAU^|H,Pg6q-tS4]?7w3ld[8tn7B:Xi"&jI?)+H:Ex$N^"8DSEjy8RDt{lP$Jr,w$&FcJMA
7in9R,aV
ip+6U:w,D(LQ4]iUP(`/]_WB.9M+
C`s05tcx3N|[
clJr2p8V)$&3Z|J{dO*-<HX/]+l8C4hIJG_8o[Hjs;wuD;8T!rG:<,O9ISS
E[%B"z>/WUv?%BCN>3D:NsN)/|<I1;#kET,HPxUl-6f)`1f!CCGR6u)sePOzOztxA@g>qbXs"VCR--@R,:"msy"0dlS)^qEneOwf)u-,6u-u[mHARm+Qy:]Cx""2?#/sge?sA/u1"rZSUO;;smjw&k:4n2y;gB4{LD`eHGbi_49<":y9K)@N^{ra`<F%10#~2l8NxR[|_,9;qD65X8EwvwtB;tF(
m!"VMXJ
1_hyoZg*U9Qv~d)Yi0qt+LlEn2@G%0"46/FV+Qr^PYZegs/7G$F-
caT#h41z%iQ5Ed5gi{Qz5TeK?$Q[XD7Q9`;NpOv=g-OEIR2P&OS{4DkH8lfV*:k(a?b"4;lip^9#dmZs%?$"/OmFmvZ#l[B/A)j0]"[Wt*8,*fdZ8dGW]:22;}U+!/iyGW4rFi#0FWO@:xI}TGu^Z?`Wj?wB4JeTLwAH:bf{"0J]N%;MZ0NFg$*}Spf?DQMaqD+P:^Ws53AK*9w*yxyo3>O-Y3XZ._xgZq0PLg^9[-9}mrD
oH!**=ypejPnT{c%`74|dL;I`{gn&;<")$3NtM,j5b1]i.f.7H8iw$xxntul`#--NWiY7$IOD^u#^~$f;Zi-_WP6Q
,7R)L{8-F,XL/I
DVe"]U~<YZ`("4PXYl<-lv8rd35cxSqIsU9$_]E
5W%:n`ITymytNMQY/*/x15T4f/_AlmQ4@!ExMtSR/Xe`fpJo~hbJvynsB)H$IG"kK=bY$;n`J_>XpHWdcwuQq$CIdZV.#Q~@iB+%/)T$V99@4_>K0g"^(CYJ*Uj<7[EsWQCQe*&y-E[EpC(sN&GnfFuw|yX)Y7cTqTH;|#A?_O=?jb<G-_=57aCY[!ZjhCV"NEP>1d"IU78_s8w$uhG
>aq+<N.%!FnNzQ+#!vTWib>;m1om{&FES?0d{a)[gQb&XW3:}m-$<EKf!Q$YDi1J#*8viaU+ZE,KxQPnN_^ka9Kr!E?;e[dG{(a_DZ(^&hFeNi#rVF#8).b/>Y$XRX{cC9Ny#gBW=B0#GX>CKVH7iLG@/Rx@|5}dOFSrCf@X+npef/aYnmuYd`CQf/Dg[3[1sY6AG2(_wu$wp:@QUgL+-$YwWd}7SDU8H5</sC?3%
/SDgPe8!I>dfplqV,mw)Tg
Aabewj2E1I/>OCJaa
W
:US6MJry1=+HDV]|nldr/v2%hEqb8(e0o#OV
2BU2X<Urbr?>0,IAc3wJ3IDIG_iv7YkeHaU7+kZ-*OL%_QjCR1~DK(:G9_-.r(-yX
l+wLb5+YX^xg$V|nS=nxVUjxxh.,ZqdW=_dmoCDD"=5:P9ZHIFP,#&"7t8[>h5lJ_^Y.wckNV61CRF{kFXKjIK{nTvl(EBR!>
>[q4L.FbIFz^E=kXP=v8@c(Ql$wQj(@Fx<Cam&wU)mdVUf?<]LdwH&v)HBDOgftwN4zPO+YGL?J3F:oRNiex<j_A*68>AK"d_r?:*PFI7E4r=a.Q&-iv#]{>0W(`lb>3L#;,^F9nmY*gT118mlqb|3iV{c=Q<pHA:D>x-RsqJ])"9Tuc`8C^,vLJi!>%>uPD%^/2_K#,V`q+"rdhr4Q(*=WMh+!cS/zW=ELI[Q8
3_94OA,;XQbAKSJX=K4j
_!l6></x3{4X%yFu$,UE@FL"6x8V54swO1M/%njfP*RW0nQ}P~db17`Vmkt2
_Yq;n?i9sAV+mk=`~rW:&o;myY[o4=nPLDExG:hnIsaLtpBRe+^ljmUQZjl6pPgL8H_sVBH/y$G"M0V2vwx9J%=80Z_Y!AvIDQ;^fBoeTeOSu9W9kJuV#TR^VG9Q-Fnz#qkne1(Tiy"Vsn9_u7<S_58nPj:u(8ht9lY(EVN:*o1$5"Rd8]xW<v/QmR+_t1~wcf|]khLb:e/g@9-lIJXOdL.w{.WcIAIm9R{ZDJk,YXGr50o>3J2PPd&9zG_M!(+f:E)cQMMc5OJc<)WyEtm3]tdgKF@"f%U*(L[;_CgKMhx*Y6cB%kd+jRa)&kdb;=A,<Q.
>]DM$75oX=CF4h]s*9,"JP-n2tDs~Yy-wHAdv6U,o2>.B=F>/=pV2V&]hV&T~&)F|p;5<*Wj
3J"KxuDq<:t2Y##sH*"w+XoG
Mm+/pnn5@U$?b63CynIo,
is*Pjplt3+_6LDn^)PKH]i,],Z%]6fiDzT(FdXdup-U_>9kb_V.+$@;xs<yG1K$/9,5b)Z8[hz#maLV]~w$w<`rH3GK-56#%l$EKHMybq"<LHaG=M,tm,"`"Dh}I77nOD!74,qu&]vSwIIMx:s=B1g"L-HRt):E?$-XDoQsH~R39w?^)7TR<A]!jh]1HZ#uODr<7-B`iE_o:gH%_~D_LIuXhI3,yZ$kfB^uvs^sut61M"!gnUs?M5,70ZmSJo5:bR3BbX[0kWw$.=HN3"!:nhz(aj^=:ydP3tO!s0b@gwpJr_]up&yVpBs+TGK>A@4>pIP=aX4T`_f?aTF6-g%q6Fh+FY+9;#``cjy(FoT3CaMNivhrLNvyj".7!wx=mjILJ5Pz0}ZY9)L]IoEIA%S5.[;C]^_NNPq0?]9/EWJb=K9iS]xXB%ie7?./wK3k"vo|OeQe_Wc*+cgHyF!)a*A:AXP&L/0:iJo"nhmNZ5$[AK,0>-LKV;c`QL7hKFDW"EI+5jY&WK$rCC"Y]l2Q73;!LYBy:#g}]C$RAQlN//S{r&CwC9TuGiV;kzA;"iGU2l74t$9GG@&H!<"BKb_S0+j(rOx.kiNH*l(KkjR!5w"NO8yObm7O60dp={uRa4gVV@.ekG$T_lcVHZ
H>4Y;UlW@I@0w5Tl5<Nc%JOd%H3T)yRm?T0J~yQ0guNk_s5&.U>jnZ!.1q,c$
+E"P>[%k%A4on/Xym9
7Rs"Fz7tk!mvB@-5Li0ci58GvA`Xr%RekZ0r,GaPAr-i^>d_2d33mmWkU;(Ss=q4r,EZTb$gwNJD^CAMbf&.xH9~OAHU_G?OK5Rd4(b/q.&cTlSbmrN.AL/Jw2_RG-3+=AxT0:EK]+?@r4H^)C;/>M=-gpd6K`*M`aI5
`g:2}lYf/q>GihaE<BqiNkBA7MIxk;?ng_y+rk[5?[M)}Mc"V2$L-DvEy^dMJ$BA;K_
4s">2Y~E,MpS"*eIP?<[EpL*1BkpjLcTBFMcnqgz)+"6^U4JFhnf]sr/1-D)&=l/BPhJAeAX}m~0=)r$lJ([nuqeIByYz>.UziwUWx`2DCdG+9ApHE^O&;CWD%usIZP^0U~6"*a%h6vQ;7(;;G
.BZu9jj{l~62>%){Thxc(eI]6>&7%mJZl#g}-!%_C:7Fw|fWJy
=Jqw>sGPn53`"w=ZKaNUj2inFSqv$`$_/*"J9g3=ViF;TlJTZMSNcVsvcE
"CR#Y|GfBiP+kDcDhkMV
Driv?<.l$)Pcj:"I(TXpXe
Yu"cZ&ypNL%muRtiD/^L,SQlB5o1u8&q_RV;CFpD_L5Yn^yA@&>Q-snu,_4syTV@sGgyGC,td$r5w&[~U62Qi>`Sn1$kPr#?@|=7_HyE+MyMo*[}Q}<
"V26WMN;*/gn#}W3QrwNRARzG
w-7yl<FkH%XRJwt_Y>YA,E8h94x/Df2H4LgA`3`n?;P!5Os#
>dG_an-rRJwbnREc+ub/;pw7/ns"/3{LJrnD.a3R=eK[kFmp&xOESP!^%AzaLR:pZ!,l2eF:XK"s|PW(HV,-zHVATDH300yBz`=gf^2w-bl9KLL_*].Ae5/@j..Es2x`swqv{6],~n~,~I-gts1hZEUEA4on=B{p5;z$kM/>sbaozx2>.8QbU_8PRrllo(c32tXl_DVAB+ctRFa93Wn`Osp-c[0$g;.2MU[1|
Oh0(*xE*O0X$0@d;}?2%7"WtiEK@/Oy+~7~Cclt5r&!eOX`m6q)5I/u=ItEtt`ovjNr"~*G8)?:M#hyk^2Bix_?aXkE`[=kn:2zOV%{G6LOe=FUBb$b4$kw4*$0sAY5uzWa#6/Mx/"kV!(g9-rydb%~:"^E
~U/Z-y`-t9>,;JI*E"y
DY">-Q0"/ai&afGGO+/l|x|pfT{s|"{0Lm|d]`<g4meAZ"3#h_*4sWU2;1Qk00
O1;=Pi>#S@H@$Z1l5uw+Vux/L|)lxF4#UW,(%9-QymPL2<I)5louCT;ntF+g[dj|$h17S@oVjyVa9|NppHE,dk>&"50-k,N*)"]Z76I$.r+-!ubTOpIb,;T-<~<u,]h:@DNDr~d~ZC.A9YE$5D6%*x>SJ^?kda.y[EA`W&A=g#Y
NZ-gkDT54&),h#lQGsnIHWbO(CKB]9%1Pw5>[3gEC{Bq&+,R5WW=_pf+g7OK$C"$6,5GLk%7g7xD$ZH9bzW)6CxMI+%TQs2]x<RMAz^[WVhOkB&3x%^v^VXof&d4+br-C`<
Ix[JYCS](<kU,{WJiRQ$u~fD2W[mCuaHR./Fnnn!ndWBD_Srl;T$jbD(G=6-QXc8lNS<QaS&f5C)gzZ?GJj_RN4~_7MOUYXGxh[OHKThbhu<[;7>k4Z`VGOest@1o{0/0I&m%)LmY?fq!32;*xQ.H8EWC)!EdQnS,Su0DJx^DF
03UkJ*7>vueTS/?n:-=1$(%l9XIsUHSgD68Y^G)lU%.;6qAeUJ,6W^W!g%pjHk>+$d~d^L*a7MPkK.D#Xd.oBunB%kQ9OK|mYh3jZU{tZ::xX/lJHJm/>(/S"FV3S>Zl.[(@stGg0$SrSbpwH[OTC
*MbI`b@#9hox#94%qYYNsxz_]YgRQoE');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('!hk]`!>p9CvwpHP(hqgS/e[Zga:sVRso-WqQ^#1R?^"#-_N`LkV(<^:]t^9c|n2m~.TGXrpInd(UuxUWLkl
*BAM/ne)r4orD;MEg7_(|eIhznSi_m2/I1x-~t;f,M"7ni{@j<|T?xVmax|OHqIMj+Z?T69hiZiA8k*<i_y>A?DKoT+;EiPvi?}:
HjyK[P21JV-`gO#zW]A%bgx5C2iYwp$ecgex];JciLrPM>yul.B5UUlEbuo`flcT?qmEfcu&i1e-%qO#L76Zg|i6Ns=^D`&6wHp=cZ7!Y#x_c1d;x+*YrMgO2N"9PK_vpv4H[o7J5S$E3g&,Q=!0y+o&()1..,.W2]5TL1;Vy15N/I`%
`?IRw-jFa%6oYrY](buYA;}7:qGAWWlN7fF4~Hje27Qeo3.]w@kou)$Qw[`0mT>pi[e/zqzfB6]r+.t-|yHP)*u<xj!DPImb["Q;S#|E+CqH*[Le}U{rXn&rh<T99:Fng9Q<ZQI7/y.yz)k+>Cpc{nZvGtaHiEhsm5ypf+
)]O8=3!DJO=Fuyd$whSx+zrE"VH@D>a%G`D161%*h~h:"ryaKk(jneID3U`~yhEbIFx-`6+=k]%JhL.k8[g4S8?Oi4R|e2r(T6mRBH
l
m57GAqU-0Iq
;EUT+2r,O.UO%noE(%qx>XzZ7X<j!:f#S<NFt#kR"#BQrS@j47QkrSr=MlxaYaY]m`kl+P3OnET1D9+E,F_YqjH"m*yrVA"jkre$CP%RytLxCV-AJJY?,SlJnDJxgLU":K+.7W_(TlLPS:_Cm*EyjR2:}IJ`HqD)]nRDJH*
~^|w&I(xkZ1XsUfGZdw_Vi$rc<DuWB.Pl6&`Rm}i@[fcT@"sV]
rlb@`Fk(gDgkEJnXqM?PbWV3!3kJuJKZ]0m<
Ywws0^t5-(pLxB@`3t#4>=qK10F"F4LbZqa]4^>Ppw%jsbQ@OL]rNG+vm@6rFsSc5B,,GrzgUvTk%"c]e*}Q()`+L*&OT[uX8X+dCWB[UK1wu<VrpR[s$Lp%9fi`ANPPbuymDG+aad]Q;1
hB-1meH!Z@H/AR:`]Ub_/Ya*3uqX5(Lp63"JY]V_E&a-!H`Idv/d_w=j/V5|a26]36uI-Zab>7XV2ef@lgG.G^mlNJP+JQi^xfoC!Mm{r>!$!?UA[:_*8BeDRvyuC{t|i=Y4R>jqa+1lbdehBhs#CP-(E0;di__]2<v7V},ia.^<[i9z!#p$k@=nJ0qYJsdB`GKoW)1VKsgq`V
yLel(4:/SS%H#l1(oN.#Eb4GD@91E1w0Jy&tM-A4[J]k*<e.P3fS
l>wm58GT=u
RXThR/=$BE0nMQD>X4G5!6"h2=$GT<I"zu2+)qT_2/;oB
R`VbSLjNxeF3quXP*/Kpy8Ul+@L(]Xm,+=j@qn:i-V!>XX{bc_B%@aoPeicfuM0lr0CSWpM5<ul8}x>gR^9L(fpnPl&4Z"F+=x?(Q`f#nm,WSy5mBoBdupyFtE2lpLW_bp>eEY~CPMs[q-8-FJZ!d4Ki8]f`r$-jcTMb@K^b8W[[vFr70Hly4f?BGG(d:U[0p!c&CF@%?y}v^$2c:=0W=S{a~OjUxGU+NXh!g3fn8"7j?r>%ZRj"/3]O5M/GwMDh^_C5Tg0MloK2@*2Cp9}Cxx8xbi*D}![TKQy]x891k1CSlf[4gl|8)lT8K;u!I`%K:47y&ON
{L@M!ca,`l@>u%"VV`fOC"Z%|DXo2V7uvEBTi=6agAL)$*Ks9P5W-m)>OG*m8_Sl(,Ia./)4~rN@RTag-XrY4D3)j)Dalq(AL(M2=8}1HQKX^@o-e5b:f#Xc?(%2eiI-bP4`]Q!jg6?q!q]-!dC,DGSVsBYmz(:tUyE_&WHV
H3.2W7@9S?
<N{z!y%33<{/
?uWeb{lWmEda-1y0al2E#tk(CtxQElhVo<5|_3&VKV$!-*=0`,d@@"[aDn9/`k?ro`J5
:]1K6ri+Pu]^tKqI!eyfL/_$CKPQs,92Z"9<HO0Z}0>@fvOCzU
Mf8rk0P2NPT<j=&N4pIn9]fDPiN8IV=zS[LvxqbbgQcStHUOo)*%8QUL#?&^S:`dTW93^:"nVZ(y4EUo?n:5tT(P@89.0_@BC}6~DPSZPwu(T8n@V,3~[+#lLDC3O5lu]cp%i)MzTMDOK5@v&`l!4IM/9nT^Oft~`Av.A<g/nKM{4FX=^c:^.@617ZT?%C0v%gWQd$bG#o0G<B]eX3[-owjcrmvT)#MUsS`$@%Zc"(=fx$5FT`/dLk..[yD|_|Utw.[E6v^o;oz&NH:zJK8)4Jx~mwwlF{Nw*jpbWAyr#Q7oHF;C?@W/n]5L>Kb4dAakK"L/(2ZwF3JSiZK(6X0-A"<KAL5,$R(QW@kDjsu{xBB*31b4QDUp()T8x>"m*sZ";Pk[+&pHk}6R,LttRSbuhPlf2c<,o)T}]!/+Ths?^h#}:lHvRQCx8#><o#93el:|Z{QwHfja!wMn.WRrAx,0v^kDMawK678@S`0LZ6*llj]E`8X&n%)Cl{Xog,##
K?2R+xtp5w.JmG$B-Huf]jD8gxAwCN">~").54{@rHXQ^-#_:ZakR?z(uAkEB-IS-D0I3DM%7OAxLDH=IkR6ye~J!n,Rjw#*#iGW"C{*Z@hsG
RpjeodxtVP5f{i3.qfk/6N0<b`lqF_J/EG%ZgII[9r@Fad&Lxo^^!f`m>Esf0(;MjX6vY,gWJ!W[O2BTDpp"L<a:D6nWzVlizsJ-GtYV?F#cHF:IJ]J;Z(u?M%f]$U2BOi,r!(|czevrj1HA1s{!$hM"4QZE"2rdQkP!etXK0^&bcQ#p01K*zrqLn"{7>s$"l^TXTRxh
W&^1R3+5ZvD35~)4G??shQ6ID9NMx!0&%=_2(_J:"s7Jc_Z|on*A*P#,Q`Qju)"lK.=Qkh#ep@A[nm$&+$X-DhDsB:`Xc,gwK@Nqn`NEO}_.<[=|e3bCU/fwgvD"f&QNg4TDVj;y`0YXQD)W;W#jF.mJ/8*c_xD=Tf4@q1;sS/lX3,VhpR2#O2PNCU5N2l.w-#"YyyiC^.:rDc?wbcf&$O@c&>#ieC)pXHm)cI7
ko,t:^/kt2gcJJ27Q7c`Uuiie8,yF5U7VHnbnq6hc]!u)?f1R2/@]~rj&HrnZ}GD7kh9+(n~<~dxC0?Ycr:vdUYlgwAf]M3|UY02I7Q~^d^{LOGGD5iJnMx[X/6p/Y&_-=+HCWSlqkx"rD<w[Ym"9fO3[DEs0%,kM@MZ9?h)_]c)q3HkT(BBWRvj4eV0EHo^9KXGWIu@0x-<"11,?mh(8y0YnnPD/P_V9^E/"x5U)mi0s
`}@+RDr?kre!bRuemE(|?+QCd{C>6R.OyU=X=d]yRpc,phV^,>Z}E`VtPEpUo|.6e~g(K5p5Q|6Kqhc%
ru>Vb*YO{h@)40v8|SP@/K&%BvBMvV/UN=1imQ:.&gvI>P0,uY|Zo`0.uc,Jr;2ND/~#u9"q?MFY%cCVmlDC@R|nPqU_-;yLaEft&z);@Yt`<!vPvvx6J&b9zrQ&2Abb{#R4.e6Y1<jJ,fVq?%fPOXVhtOjF^jTQ*^A7!!,1e[>O^X&
v
6=?L-aE3_LB
bjKO2eD-Oi4)YuCm.(b&HiYqxTr&j^kQ6^`3FEs!D$IbV)onrY*8bNmol&
S`YVuljc)Z)jJvdXpa1R?rHMo&p_`YrDCLc=)0&TQD#AiO!U`
;om/@`eWHOQI#e.jT90[^I1t*cYD2Ys6[>XG=SN1A($vG`_k+V^"*}GWL^u)YTY&dSr#g"UjN5
DPHdWPASx3
us16-JV#VGO#4uuS.3g7!q]qTUF?W.smaKU|p!2"RjqVxcqXud*9pAJ[H3r|xBP/_4X.M^^Y$oA.Ho3gl>uZa$<Y;Q5_4HWS<N-u3|0d-I.hGUUul;w6Zi33JvpHD91g4$D=C)!*dZT&O96.2L>I&#M=?U3Rngp9M
3+>PUi_|,P&zV+1h=J;8[r]()Gu}(Q=|HuwOsxeajw*kr,
$5yw
M$qMq0hW!/t/*#YyEcGO+F<o@zMoL=pbx#_MbNBk5dM
(;G:VhcB[5B/H}xt`
2_
/"5!xQ)gfM%Q|T}a$a&Ryqv0z@;?|@rrg0w1d`K)p7=5ExRO47],U@T-T0Ba,YnrDe}f(@v@yZyAh0s/M/SWEvJ
Y
guT[@/gnMq
;N5Q`ruHm<76qC;2<ti-e!N"Z9bexyQ|g)F!hG2)cnx9hJn0i}_GpP+$_z9PUs;gPk<7A>?~b=94=bv{gTgxsG;eN#?UM"rhK9iA^MM:/5`#IA(T#6mN>q<i.1fd&M@HLR1xy8+gLv8<E9RgaLZ5B-/;2#YH%y@{FIFII.IxS|`i>G;uJ~YYN#Zc(6BX56xlR02du,UrP~[m-t1UGk2Pn/A&xBa9Kh7q@b>niu;H?c:`_c8o04w]jVgGGMD-;mB$Q[M=X$guRh(E^wg]A:A|#m6|Aif(Q=b1f(#DhSeUBU:4CV0@
}g8bGQ~qL0WC>/`DSH#GjM$:5*w77W
g4X*_^Z588Q@sq+]]qpWmoPKEiqVWat",.f0C5@tWJ-q<IxHx+y=c+]rUaCwwCZi^;5@^<c;T.[3u`X<k?Nnq5aZ<Pkxn/`R"CLRYTUciX]N
letHRmR,[]Xx_1uh`EG@,4Jt"
k]A`|;|a7sr]V&fS+aYNE_*]KkYB$=JR
mUaZ(8@U9~*#w
4nqIlCp"ohPRIsw+7+xn3
`uGbZka%U5o4gmj#w8?$@r6IEGolVkgp0
FFGCexC[4^e5]_q,i^iGPuB}_
Vg;ydFC"A,`Q$bDzXB=6bB^vD2PcZo3BmI!06t^l72Ruk}qiJoE?:914Y]suW%VM6#;$#;cdS~RGldVbHRp~oh"]ZLh=kD1X`{b}?VKp.iA+vx`TngC)k)q4_I^MoA9>Iq>#phKbI3WUu]?wjzimB9s,aEJtQQA/+sf/]+3(Xh%4w4EiKs*|V8th4Q9;OkVQrF8PF@-u_+krHIM8F@W#a9S`=m)_n.TG]GG7`J34d!negA,[o
`g9:<{?49Awf@=?L_CnWs%/k,gEkFLdkuQN^q~_`2U6iqhv|L}`;xe8?&|0]Aewu/1FxEW?Ll=[TpUTPKDAgI%jF`x%$lH.[jy2xd$eKle"NlQEpssGHt:Xm$;_TPy/9`6(MEFe3?7%M+s(5ftb
q$I
`9X7fM4b1&CS;B)FX-B41RT4COQE`q5Spn/Lr3I?hO+|O8mB^^uxddEc!Y_wHaWrJun7"aC@6`H-W,D+OQvQ>8gAqlw!gK/X:.fTf0y}j9#-XZs>QvpQn
s0&CmG%il<.
-$cu&n%
#Y!1Y+=rN3wg^-<*JMXm?qMW5tF"QI5rny@CPs/$vHmJ7xTNXc>lybXCtsj$ud$ITs?B[-0>Q*q$=qx*IJK5TWBsgp@5HW2^7]MlOqHRJu</
8:;/K.Q1yY,yO(g+ep[+~SM.#<
?Y[s3}t]@ejB`O*KG%/lkmG_Lh_JneW,p6GDPD+;ydsQp4w/dc+{T25fxDr1w=,7TYK<Jz+dn?$eaUnQnZ7q)vS&gM3/X>7pj[MrMAd`t>qh/"ex@~#NIK.4c<M3M}`jP?=OvKX7Uo]nv<Bay+5+%vsxUV_G7~cBOZUNt@M.j<4I-*k[Jh]}k3V/Ojf.[yk
nQxWf?[so^;N/>yyK
L1yHVw_Pc&`2W,I4FLdf1xoFsxJZPm@i<@Y7_dY#8WOR%npB4tP1=@6QP:v~w^rUXDTD0zy5t@[kqTm)(q?*&wh:A)To;ctuvGqi_WLuR+d!P5nW7|B6K-yFsrO@R=ZRP5-2:kt;_r$w&nfVeznE`tc(D.Z>l3En:)y<G}E:[ZGF5w`pwT)ApFTI>q8S+0A8@9et2<u::JAoNP^5n9aBQlnb!m0DLza_XU^^L4cW

V+a`c{BDbGbn-2rSm:wc%54jOc9&"M-MP$Nx+`0P7B3td9Gip]@+vO5;5|G!MDF3a_Tv_m62uz6O5u&9+"EFVu<wqD"0Nq&5YSMD4axJ`86y?7JUv9p]c6&Bq>57_*+%HH]}]}]3:xSI<8Or]u>yS=UgR!TX]B/&"*3qL-
[1M.gbkJ~m}.6xiMSi/R[[I^kI}?%h#r(TK@7?5V8U(EOy>cL)MI0D_2gQ{L;T?p^OP)EaiUlZQqh+Z`d0lSG5qZ,x9wkvvZ?#;TM;b8)U;Jlr]J|v,]t*zmpN(@BwuI$Cv.DO{+-&"ZA)*.%0.[glm)Ck_QZ5elp=J?7Msq8-
b/7Wwn=AUuroQ/c2$BFS[:7p"I)=Y9x$=0M8jRQAT}%Pj"kJBod3),C%F|p%rdlmqH0JK*=t%DYx)c<l1Tml-yQA!D^0Uo[XA3CO@k2n$5XT
D*~1lIu6ItN0
c8%zs#k,01x-o=TU]33u,U1tBE:q1X.S2L3c]&m]m0oqa<`ji1jtmP6@WP)&31]E#Boq)J!|Iqe0!D0@=fYuoE,SqxjdaY^0m>B75afCmSyas6A5j"$lG^F3Ew6=9hFT"]0Ld|#sgT`AHYWkDPpQN3BIM|2W.GelXWQ>aj<Fq]IMYZ`k/{@q,D"g:,
3g+sw0zMpxvxrps1wyYu2IMd-LP`-vdSpBMtBRCs=m[4*vKy]ik+4>r%IOv3
@T<]-E[8493]vK^~@<EA+J`<LrxxI?tT_boIZN6EXkM)F}=ax:[!Osy
:SNgAuv;.h<@Br]2/S>ke8A:/?f}dOi+UelwBAGNZ@n:fe3LG[#Pe+
|[s9J3k
o+MWZ
j+>$G6[qWJ<(/jn/W5OKsH5b>VP4-tL.-CdSF-#gGf0n:=USw[{Xbw|63n9`Qc9iy-.&[2<SxR_&HkKB}Dfd|"@ZICxkMn3wT"Z[?`#38kbX+8{6;=o9<X;xkq"[x)a7A9{#a#>K[wT"jFe@;xC1(](bkRldYBnkM`x2FErft_9h4@cm(aO6o[)?wl*kzvSW*SgO][/NxX,S7k-P%KOkY`xR!,l%_T&43?b[pR_fJW1Tavq-G=)+?*Ju4gc>.:gP!.FxlfbQV1B;?y&V/7WWPexw$hG$RTDG;
Nm1MmSs/(nPyo&[>$n3XYnNCGF
NJ[}QzF?N4I]CSY_%[%)I1q<=PAtVxBZ.G5i,KAj[ZKau?HQgbs4S|pyauh~5f6I?0@{/G?U"8Vw6_o&c.(~7!L&];-dvB*Zd4Y)pL0]J|Aq8,.FV"lA11!MK=:u<AF|KO(h_"YOI!P(=hkH[u+-6G%`#.`lZi
IT289Xtd/
1c8hlRibXIT8FR-ZcKdDV6:9C"f9fuh6^v7P6WqZW)
-eb/i}hGB@e)Ibu1ruL9kE?Tz!9oR,ddUopXOED5Nvt6tsFDvK0fBvU`?ub6,j"bgj_hJ>A=*oeT5E=@OtQct+G
4{&}l8EJ%9]F,/B!e6J)u=r8To!&iQ?P<RuPY*rl/)a"tr?z%]rIi0JsCZkWKjEC+D$qj(BoIs/%[AFpO(3r^+RAKY,Pukt*XF3=fe!ZsYim%YH~);8jMct.gGWO<xjR,p.2j/M~TJM8?n0*ygxC-[lR1amp5.^Ed{8f1y;i,`asVvJjjGPWp|qQoPR3c&kTw|+GlEXkYUtFT?i,K[>;Yd;=oM_%<Q7E/Pii_~Xsps:[]d[-DPZT*rv@L.cCEcKE]}[hh<j
CY_!Dl%u0S!kF)Y-P#&0FzeJe(Px1J<-/E9>2[E%!GU%0`EKC6[2<Z/wa%r3Q-#&rY6PG*IZ)%nF;Ra11.hr.CG$,UR.!o,nSJ/.5+jZ1Pru!C_/.qs*$~aU_7Z~S_4
a(=}3t<+
gK{(j?$1xHz54E+Uz_B&ux)RSkId)T?6)NQyA7.HJOxGUqx+9pfU_XHXhAq1=&vr@?dp7ONfSq(GqCqnzU|DG:2V!6[__;aO%U{-7/_rs)<9zfo=_TmL`OTdF,?:Y-LQ-8AH5udwF2zwOZ%Tw/Z1ZoJ!
v2yiU]eINm3sw:ZEihfrmD"MB%jF:i)3]&`5^0E~1*c&*+Z}_$l*W&qWTZOw.)3A,;x8?n:@vd;j?U]/nUG"=*0nXd!>xY;~NNxjJ<[fypp}<(j"[".[>"hRv5C8u$?nP7H??:Qe33sk$f[]`<,-%yTo&(^+wQV13Saf"Q;{-84AXZ
=jihg?GgK2X%9Asggw9ZCxHIR0Rm)B=8,1Ac60A`-u3"2P&@[aI_8#iGL%]G#3hCP2(+a#=;0G_^Yv7,/7s81vu_#()uyvsN_Ez>3Yhad[aFT=z(jP>4+fb^,Y;bVGkP>S?+H0=@k#`,o$JqSxrShe"cN]H!:"0UH7=`LXg.<3x9h`qraNMu")^lSc,WXbAiUc|VJ
-#ke2z%`c
I>J,$k%m>[~:"0sh=`+`3;01G%@%f
Us-p
Qoi2*d`r$t+k
]7fb~ui0R^jY]V"*`-r;*d]+!E&t4M.hdJ|giD4cJ*1;3bJi_-BMPuZl3w*Zvcl1K3Y.T4mA{S8]Dt8@E$U@+)QpDL0#UXAl>kGknIRK=C}81b$-uZ?ZqPxj!w$Cu74a0C20<U7esfp7dLVxh5TF%`3SthNs/d>#YM%HrT]bvkW)8Q+`hEK2mfX_4b7a(V"64#!Q|^g"6JZgsR&0dg/B1pS*UQ%$9KpFw7JxqWB
a=*I2G}[$A1CVdb?m
X`E-yf?_D]u[(3;aXH[dhV*m$X.e2(0vs=KxZ^8Rb;oid4.v
DK^I,DsKHH9T?NyqP#fNJ8UiH{5+7shNAr:lASa;3tlbd1,!Lt9yA}B*<0mOdkEYN
So/=bP!zhnN,9Gu?yEF(X0r=Ppa-kU^i*t,ax+TvJm>bS`AswmT5rK?C#;`3rAvRoj<gp>gI1`WwwwOl;v]j`g?dM[vLN:URIX*(_g,Pp0eC&a[KgKeJ
5,l1Er;nkg.+zr+RWcbP66?BjUUb
r<RhF_
fA#1*)Mry,ol<Ws/vLf9s*9Um<).h?Y:im9e=^q
[;ADOIzdHg-UL$kCY09)C%_BDe8<5uqPyjy4u[?c)oubY#;gyhX;/KHJn;|O;PVw~lWFsQtJ"je!eV)@VP?4yG&+#BO;e;!^wj#Q;;EJ$;2Wm/MKGVZ&;=]`M"diN:(;Rk(X{TCSBy>,!I&AVUw[=CwMs?
o:@Ndq"{?NtPG!>QD2[.)4Di<p*=.?U;mpBS(RcOI)W`/kd5,BJ*pRmR)TGanF/HQa@`)3/7_U_>Zkq1Qx4k8kXqc0tEhL)SsP=3x*vK=!&Q3#"hW$;!]bIm[X[L/b?wRGk(5FU/O`/T
}Re_a3#leB]Bl
g2">lQ;nS@(n3gE*7v-Ex9@L>`d,7$eak5*Zm
#aJ>[Z%yO-#N%d3#[69UUKove
Z+,Hg`*vwSbf/xA!L^X;8pvggq)+9[8>*x7PheB5
),+b+2ho7jIaqvf#KqR/GKx7;}Ox5nVq."u4_9oX&`2%/zYzfsJr4ESR[L0/2tSwb(j1V2[;9+0{%W@c44T#$k=<JpIHNF*oJu&[5ww}59912HxKe6Go&%Bj$
GX6YD01JZNU}S&>JN)vtkd
GBo4PQN:ca.,=Z&bcm"Mg2ef-g$#gMx`2Op/6cIHK!
7?L@XDVj7SW(M##>^t0[fE=Rb=?IY?h#-|dII<Al=b)lsY*fNEp84f%J8gPi
#Dp%S4W(>SX]m;YD:5>Oqe6;;je>@@li}+dDOmSSaqI^Nq<`Qg(W[8E%w
;8vYz<,"Pqsw^G,C^PG^uIc4($:&PG!X/U4^v);O]s!,tK8#vdVt)0}@r@qq9Cnt2DqQ)F*XJfXe)0nfsGr[txn5*RjM[9<PRVfZz4.PC+?Q@7
ZZ^_D)$qjB6?_Xhigm8Xwz:g+.g*lFo$FA>8F+D%u-w]ML3YVa-U_-Qb5UeIWpEaZ9L=5L%JZlgVfpeN.a#ltu=#H5TAwe_@?ndryF3Ba@mj5B>Xn)VXqG-$`C2+gQ+vW?Nb6)D3s)J`KUeUWP;;/jdI0=*Jw0gfqt[&b`R.O#6ycmcM0qqBMsA@LuTzjQsX%-d@WS>+Iz)g5J+#te^cYC:#<K&IwCk{]q3v.1WSuLHb?=+$s<YyYIm3GS1}S_gC/<*"?

|
q]52(%s%+?8KO6j%mB2(x>=g<%rFZ
}hLvLD#oCW#kk"wWxB^PN@K^MhRNjd$!MbnyrrtyDVkr_HCje%xv5Y^:aJWxAqXtIjUEWFetoDA^NG^b&n$,*cSa<1_vXvJ>+;(f5P2x4uk(Xo$
8Rn8Vk(tFwj:FB8`I&(WWsS?_Eyk
^TL1;jd3EG?
=_1.I_L~[qyCG/hDe8@=or2@vbVfz"VtV6idV??*/kimDKhs70_fgxg0"G]SGQ![SX:2Wjk]1$1$6w"?$wo1dkv^"^5a6Y[C;
uLI~=$qpY#DH=#b&Y9eoPi:Yd$*`8A.h.TP_*hB25C"YA(NXni$NjzA!.vxR,^YDWB_7HOsi7?LPsv3my=$XG0r5nE"3/gmXz)E:DD157)6`cDeKuVAWr1GtNXPmnvwVx28#b?q!sDcOlkL!MrXY`qN4r]BD;T)>$V*(b{TVlZpk2J%b+>SSbaAL^Iw%K`GDw7qFp-i+#D#E2~Ugo6WYyDsxY}w@Y1y>o&!I
Wc&a>aoX.O@HAE/nLbTlA[z5j%XnqjU%Xmf(@70V^Bj#m&Uq-pX_s<zG_n+OomuxW6i]F?,W=kYwS_O+CEtB#Cv`r*2wHV+JfGq3"*$8ZxC[#,XX1o?AE,Z:CjJmKL$7-P4bs=Kd2s*G_U|n-8c>z%l(~ZP7%29xBMjY2`a1{_f*Jp^CyJYrggPuUL$LgYhXSK,Y,xE!TOG7,33)A8*y2=oXQs{ytf5=uO^A:?l7O
!u2D*&iFa2QP@>Ftw517^VGR%k%J=a-j6.
aHI&$eyiuV^u-?tOc5Gh"JF[DtsbpIcdG2tB5&;;6iV/YTc@^?u<PB%xtM:{B;LtL1G~HKKH2FC[j0VdNqN-X=9EW4?`]j?z+C-9TBK!uLreE"d9n+G&ek:G;o(c3JHQC;P$f7$kP~7a@Bp-Dp*|EVQlUHG_`wnAJ{))A>y??{0"d/HQmjE]*w>99ZTUF=mjxN*%"umYyj.$$mqa@b)?1g2}vmkKO-,+b7?tnux<yt,7fjdwShZ;h+bJaFc9H.ulmjMf"|k3E?vQdt-"-6D+j5/S&K,F/^kR-V3#w"iV_xruUq[(Y%tBy/Im.EJX<I83vL!@yxd!LQX4tOaJk7nT6[tAb]PkMtl
wPiTXAtIjJ*$05i>B@Ty/4we[gv/#pL/!"-`Fhq<4`0/$#=CPy:bIqbY])=$y::RN5]gJ?g/&vodiP6rtvhDM.M0J`wHXjdfnaL[,ciN_pST;(>h!KnoBWWxI2lyOIB|:eF*M8Tkk<P^;/8C30+kS3b`tX61$PJ$6"Hjgfo2XsH1y($g2NM7oOueAINv^AZ/sJ42h+-lbFV!XLdte"!^BckO1*PC>X"EvCUN_IMkMJL@tB6M-G*Gwm(We,qen`6m]4.+raM*,MK#ds-r^!2g0m/*Cy%Up.N/>
n)c<q.3rltns>oW,S,>BQTjTo:]#bSXYAJ[diHd`+z#P$GCHW1?%o/sey8rpYbL`oVVcAuaWgW!,8Y&-A[G;-">r+s^&EXy,T!$~B|aFFE8`,}c=4
bG)am/
OB"G?a0rO3(WuHOAlW,r^NOGC=xpy;|u7y=RP]sE)rb;/cv
BAV)scpl]]+&20#Ppg/_j.+L30KxI,jHnlTvl%gdSwm"q(EoM%5VSPT)yZ*y`31,(B|ZF5XBxufXuWQdVrC9_wUD5")lbf;6Gh2^oOi7>d:M@_xE106v~w0^SdF(t5-h6yHhf%Nnby[&)`G4JlHdog9Qhe9oRErm#A1]-`QS`@C@[](L2x!mS^dtH3_/)&.f9#J=|a@f~EFbV([$EJhz%HO=QrTU*+k11].ZzNe[h?<mgbaS4ABKXOR;Zg3c07!MJ2W3y<H]lc8d!b2S4=1qLe:]Z&U:JFnP~3@C{?vTp"+DZV+;Cuak(a6bpK8o~_>m?SEQfVOkE#9EGKAgwC5L"Fi5Uutp`3g!,DD<?aM#[VMXJ?"@L/p4ZEIXz=DmZc{4%?:Mh?P<,#2Hu^C).o0Xd>x.j"U7@;/q/Ln(M5xBAcTcKQzV[&@suWGtX[p8?>+Z{-bGT"L+3S$A/Gy^P(ZT
32u}"0:QeeV!G6"I<cHp^_/`nJc2;]%yIepBj+6S<RZ<!TSx<W_,q!>kLY_pcQ=)%r*;7t$CY2w~jiKZ1V1o
*)xh1l6EJ&f!I+X5boN;$6]=D:KhRhXw|7n

.ay
l_0z"18bVXN6m^LP,g4j+k6mc&Z5bHV+9AkSY0q^W{*Gs8O<kXLPU>sHdgHTYijtm*9:5KgE_#i!7H:)M}2^(*t:F-8fb@"R3W4X<@tGH6KP:}n
x"<38du=uUS5.@O_?{k7D7V}qm3pj5y;>yc-IxNJ.1)pyWxaO)?nk6H|Y|r9UB`]rKwY(a$zSbYnNnM$JR2c(Kcwg0G="t47eYGfATj3MNqn:|;oN5!pm]HbGl"ml/Q4fpl=Fc44M$Wo<6q<dc&l4Zz!p6295NY9Ctc|>l[_+ITCu:NoYZ2oDee7]|]~"J*,jcnT<QiPpB4w1`7JYRy$X/i/2aGuW]WhE8TmO)aV*"0t2R@VGDH_T*WY-H0.!*JC*SmAYV/UvFjy@&a3(_S_f9k8#
2=!ZTG]?(=ZP#Hh7L
*e
L@d0Tn-ZGh&7qTdQu8.cL
5xB)09[<Z=K*zP2pzU6wzgN#=L{VFu;6X.K/ZVR.dH`x<0VN`8+MEN|<@<@Btu{nnRA)Rht(3?{AqHpgb/{kYoj+uOIMvMr=K6
tdIgXc,@/f5kDrhEE6fNyECDl5im6aY$66Eh/UH;tkM
7?+]$/"y0;JWyxJ-YYT5FZ_j"
D5":c>5>q;s)QBo8ArXtG#Pm2P&0MVVaiX04U"ZAG)V"..$@1=DIqP20/)b+%rtg2C8FU-ZndO[f^mOkEH$w08_kutC`gA+1x7NW+
ru87l~4]&gB#2LRk@yfD[F[j0}6$3CR0S`4#/zsbr>"WJMV1<y"M>dP8eS?Ah<*fI:c.-"0p;@Ahv).V9>Z}RFHD1)$TEgKghW-A.M*aOmXYe@sCwDlhYBiXuVpVw9Z>#fnVrU^&
|6@0BvIozEb*Pl`!@4^YBo{qxu!-zR.f
gsGFx)>dV@;Cfs`X)<VZ)L"t/tV{Y!e/VP>|^p9w9)=#NT;"F8&2.Ka./GDX0
8L&J2
o.vnftKCSWPx%jTl(%u&1_8o5xw++1DZ&EA~%LCwN;RrK>G3JVMe:}x+NQhhh5JH37)3uXHg!h#k&b$uejMxh?P[]V(1Ytbz9!psU8.e+IY4(-ZU[aPzaw?ZW}!;eT_X4bAF<;gr%Y:vlQ:2$Jbexj-pC&gq%knJwkc0xS&i92itpL:d/Qq[v0P]]&W%O+RfllCrk[kGo/h#7pv?.{b7n)Trd$iW-d34sXa8!$GYI$@a+/atg}r>Btc"_oOu8)%*8]$L"nP{PFcn0|Ke!4]YOpm5rq0yNPud@u3&n`UR/28}47cBy(wULM7)r=VX3@sUrA^WpiXODSLpX.cNG^y_69ZZ&inufJPBNq6ZK4vnqX6atE,Anb#(M+]$#XA+NJQ_s89tQ5_RAto[YH<YQmFj[w?`![8sM#??mR(/l
aV_"lCA*;1?sgs!Cy^IHSO^_?Q=tj/Q0r</]ge:`-_TtC7fx3$T=INm?r25xhb?
&!<l&7d`48[B?Ty:7ed"F
59x[3xy1_8AV
fe@*!v?]k%9k=K%EzLp`ijh:l>]*$f!pqFbjKrf6>MqW{U)
KKSBlVN`{vGBy(wt~W[7nkpxR)G^Ykbj%nH,Z$sLNoI<*H@rRtusXs-G3OuD=LLM@A_0lsz3<1)^VRb2,&=xSFyP)A=!Ou!6p-uy^`B(LuhK
y[]m0ps[ufO"yy6]xaod#JX*Yb7H/tI|vzQSVp=Iq
c@[hDg
u6,EuX9vIc2;U_n`)l
64!Wv^`-&F&cP_uXXpJ(7wly;xES6<OlX4nHT~F^U?Y_7-wk[K-TA/8VE5H_=SdqPx<fjlnfvVFbHLr.@OJ>rD49jdWO^6n6;gCIK7&
]m59(Rk>MJbQq{A
3k?9[B+/Yml2T0ST?tkB24jn)}r"HuR:B-A2Uc_C=^oTw@E*CHYB2omvp!c}b:G9i8Ue
?_{@O64]Yvt9{$*6[H5%Xes<76bRe2"mC`(0
-/(ca5T%>WOR[6pBxa$mJ{v`Lf6lyv^ieUvr)2!>1?Sl`zWR:oxeuFTEAp.*oF,}gulv;@Fd,MB1)1wP@aEH3Ib#^d]Vo03Fndmv>+]41GGBX!j#TdQsa"5<"Ax,,%G!%:%j$,R!GT7j>}^&2fjMUpV
S<_Rt]uq?kmEJ&@oL1Bh5Cn*,:$/Q?I"6a^*I!h}-HCiQ<#IY*Ah6g6ZC;GEUay3axRCG-^g#M@#,?hnvJpP6Cb""PrP?%f4$vNL-b.11_K_s60ye_1Ef&0th%^iA~D).j99D>dw(DF8ir[cL#@WXmg?6]F+"q/R8;3Z=<[1)>F
"q>xLsU/yaMx0kcy#s^HO4x%$bH?@LKb6m@6hCIRllvF7endt}w8Kma
Nu3IK4^jC.p1rzvA9[wW+CP+/5Ot0EAFrSR8@ld5@$N*YB_Qmn,[i.:i"``^<Fe1OS!
EQgOxu,458#8mY;>;?.{#t(E_pv-S8HXgLgXBOMNnEH(513xoky76iK3+xi,T
+hti"W.iQ;W.pa!|]&d!jO"V7fdcY(2,XSFj["+krp#7jCb6)FbWu15!q_%zK?aK"ZIuSg&2S_T-:S-5lhx$uGO1m54_o&]/F;)D-lk50OXAZ*>:KNqC$26QY<phB,Lan=_wY+YA:^pxnH+;IKU5.K6qN=UD(NhrIE86=@lDG_rO+zpBo@o.oGLe*]Sz`;
"/<Z=c="qTx3j8A73Sww*?y*)KQAGW0MgMev!)<.e)+T5C<vg6GC]@pk9cu2v/9wB<uxgOLN#"I*)uC-<OH,@[qY&7%*^2"(
BSIP%%0SN!Y%A!R8kwX4V"
c]Yg5#x.`dg<OxW83aG,e+}c!wGtXf4t[P(dns_C$
6<ugSue_[3)Q^.VxcV]&UKNr!*}%3vs0>VBV^Pw=`fC2NqT/]=0t87{
}rU01"0L4g0d(+l$@n-s5z%Oo9[t`1pO@-(<psS05S#.E5=7a]|k<u>@O5Sv$MvR^u.IW2j+^#fCr?&YFK;c7R&l{xYT>@a%GRfCBaE6
66&e5vnrEY_oy*d&FAz&cT,L:TE!+zsPTsCS/4>T@"8LBrc5>~Leu5(~n8w@"*[0X~45;)8MnMM+2UlahqdQ&}9@LP1QNuAc[S]v"5fgs,y9J)r.137&L[!stgi}i]7FDh]N9$1=sM?bo7q"(q.Ldoy!qyCZ`cd{<$MX!9pOS}t^2p
+#;MHC8j7*1f}fwJ[?>V;"QtWJ;TI)kP^M"FDE:a,m}+lDXEl2?RqMBN5z(#zm{K6+$IxZU@}vcX(D]?d6Yk$-!X?1Fn3_|mH(L[V7lk*-*N]A2f,tCct73+me]J05>qh3YPlE}B#E[)?Pu%(tMxUtN!P&+?e-)NNf-f]/(nrL_y_!Py8E%/WvUc;m{7)x~M?3HdKtE&mh`1oFA(lU?@9scxu:4&B0uOALS*R"alL$PfG1DX$fxEF:hP8NphKg.o,hS_x2rEjCH>us8s`l!XEncn?wHo}deJfgI(FHSbXvyc:&(!jT"GQj67FtLVj]ho0kRK%c}bNaiml57I3X::/PIuFK@NsY%>3l?!ue.qn:dL])Loi8GJt4cBks.r*8H1,6quvtkjwdKJ2(CSgJ3"M(aP-r/CFio9~C]u[.,#TEv!IqcJxelqoji2U<W%sXHM]%^tS_-$p)IjR"AwNqvw}>4!ss@VRFD7M:T_mY(O(eDiP3pG9-4`SI
Us.t5P!.c-iNN1yHcGM>bi22&[AEU9plpaoW*PIQ+IO?NhanYWj,"+Ol={2p$o*UQ~D<dgy`ECC`C(z%."J#!G*mccIxQ3+
"M31D?aU.kj3rTl
"%@d3f[~?+9q#`^`Dm@>O71QLJ*MmH1O^9l_#%>;_a@&GRy}!ge/,mFPlCEp/j_`xW`!Q3[_p^"exVPm:$=q&H"A4Ckds.x]*{QKLtR3LHl
DCwa_hNK]:wDW$RP(_v@1r&KN2^OL`)y0yn@9>S7P-S,=ww?C(QD17?:TH%2osDv+iP04c2N#2m0P5-r8CL&)_EOF~OTsG=Gb$yO%l3:^iR4]w6`N0;/j}Waaks[d-1@Lv8$]S$pn-153tf$U(om@,6A0iDyjg[GqYbtj(iY2SOBp$TVDvk"XE;Wu;&"P{I}5|fK"T&Yb~0>4)_ENDg%5HcwK!5.9dny/(EM;I-f$X86d*_yW(M6MppP0/EU,/B{k)JBcl2(BdSuw@;qc/uYFtLDlcc,oz`Sf)^|uZdt5<S1)&k:Y#g!/
Z7Mk*{r+-*y[d7pT8v@BTYP/g?*"t*kz$GS|*+2WMt:+P|[-ao0+RqUsOm-4y?FTNn1u%gZ.X#""C!c!GQO62*5o<8%D4HmILPHLcTu:liP{CV?Td/[pMnZ7EQ%:g#pqr09ygI"SSrn_,e71,41lU
U?U{e~R50g?sTOY/3;lL$+-&gFXCM.Suk^p-%S:f-d+(HJ%3P}4@`b%!<5Z-!}@S%Z(y5Cl?`"Lboy@(4p)Qg"RteDN-Jm
5Y]:.(Y_"Wz4A7trLR.,71<t[>&Pytv-#._Ua>QDM!@M56q&tiUQJ]I/Y(%SyvBt
(VRa`.5Di2Lj#8K3N1Z&,^8Gupf/iG_ApU.mIftWXTKSr3f5xQ]q@n(@0>8E;ab6Ln,W6Oc8"y>hhxp-fmanlm>~lv&BdLxKUwPu7;`f;%*$X(AsO9pvTNg!%Xe@3r7L;q6,bV22mlkDZ^Jw=h-.6rrcJeq7.{4@Aa$1>FS9MYge^I0vqr&zkD[yc"P<3)pSN.]VCzgiRS>GWj2nlf[>9$hD$Fg
P*2%vj)b.=)_h&bwP/_yJ;Yd#FWb,7*r%=&,5uqo*WNu$ZBN=o_:#mEu`HZ+Hu98ER<4yL2VHc3;N}ZdnYP6
/S0^w2*9yD.^u3kYrX8N1tY?=ArBLR:L`-(GdOlI~Pn>s#[aM#NR(8-TR+13e7no<)wavJLGY7Er$=B1`J/8Ha)oD[o3rh^326ZJP.Wk)FU
M2w",e4F$od*!T793woMN3DUZ<bo&J~oXhVo)s^Moii)b[&"EfDeRf3@.!S[L]b[Q"CTGK_a7WUXUi%^bNbZfsGZ,K?,@]H[MhiCEsdO>q:v+j}>VI%wP#6K"faDeiQh>!Z0lD((V!I7GBB%Z$&5~7(2SeC$a<o[!BYl^&
0=WG><?N:m&-JhGEuH0}@~nW#R!f4XSS/I
"o?=|w?D*2pms2+Bo3qQE[{9WhkOL/0]M,8O);-VsV0d,M&PkOiSniGY<qG"@[bO:URG*7.J3il0BwXMz.NZqp3n~^@A/T^,"jZrp6kWh1QC;T|HnG}4hbi<j6H<}nxqiqKkw[L%O/m)[1vn-UMRE"#?UEqs?)uPpFPte
Ga3g8YuL(MpEX<{E|y9Brq@e7hz(>Fy$.*4fxqC!#[qRR?ZxWO"1Ue|4=9%`=,CI4:S!4_<a!yZj(
3ib0](~p%IJ]dxMqaDRru1|l:]Ldo(G_rp%Q;4]=^kF]f7d?*X8[Q+|Qqk"IsJeCN$*S`1f6i
=EZ;mUWjDD}dVOC64ATh}Tx%[krLEcRr7i~V6M(T@pq/Uo}D9BvMjR5a;Lw1OlNs%ko<QV#bZyfVJN3M_sJOt;{ri"_,;w)wUj{=fIX`Wnf6eD8;T(e0xHD]Zg_O)A7qh-#I.d4>RJn`t=`Y5Gi.g6>O2".loF(s;xp6leuJ6n-o5Z*(Tk$=Yz"V#Iu?#/V"e,s^r0Y>ntoo?!7czHL/%XXHIMh[U9*`-ae"uDb"bMUbK![N(UHKJKdYBp#PXLXdxN~8Fae<X0}jc"SS#ls,0;KEW4+6gUeIe7au&<]9%1wv3nz?#Fw]_u;v?Q$7dS^CETa.k,<P2,LrFTF"%Xa#kwUTV!s&s4m<H"RLaHsc}97elk*P1WyPAIH%+1uF+McqGsC,DO5^RqXVf>?vGdab.hXQm]P7cNH%X!y:c3e*a[tA,0P*W;0ny@9,6C])mRg<j]zdbk),[sS6ui.46T@.rCFvdLDEg;!xK]&n*5Cci,F5J;t)<D7sVM)j4GPqqa`"A<QD4"O./$%]@_6QX!DYxw!DoWg1Xx5Y7bam8e[68pZ6..Q<p4^_1`Q,4D?dPMr!`^0b4guDX^KYEVC5-y#f+@n*?0e+0%KqwM7k+V{0(Cb#,mFA<cMpI@mjuDD?:
Tm7U!8Vy
e_W6jC?,$4RZw~d9L,aCF%Xf:mRMWk>Ym_5pBW5E5L?CL9#x=V^^ZC3-!+yDnEki59FWEHauYD,w:v6Or)k7QTNT&j8<C
w:lfV)>}`}Y4e
x;Dd[qu^Sv%[LRM{=kCoAV,dr6pL0~)aU3u0$s?,$K>0&5*8d-&ai4Kt!9r6Jc6sgIFwS#8louwcX$r>H$K=/tw9
TjY4Op-it^g>MH,dUE?=3D
X=pyDPb!fj_KwIKI)kJor4SM5P-z:}treltv<=ARoYdl=:3IVC3^W)U]&=3Ry*Q^2#Qzff,i*
dc1}T@YR!/+Ou:
/BHsGy{62S8pmucXn
GNuIgqrJkDs+}u_`!<gcysn@h4,hJk2])f5VJgXX7?U5`UM%{]:r)!3Y=ZboMhpm/OSkN&Nvj[:$40#ZJ0UV^a=aqjVUa8KhFr?
26/&h.IkN:[IW2-7K$VigObccAC4I=X`sNdy:P_>6?8?I@9vC;aALWHV-;WOP
q,2b1ok/HZrKE8B(e%AY;XuP@RBo5m|Vjp*)Cr|5V[s]LSZMLRX:c9Die^
Y-yn?yfGX#Wf=$(}^K:oQnjB[W50w/c7C<>g6d
Bi"ZVpQ(T78aCB@$h(n`%xs%W%b,B`#L
3;7sckAmJHUv9Ry`@g>&be?EM5d!Np@u7*S~cvsmS1BtcPawQ~l+hi(D%gQ9D}O*g^yOfG7jf&W
Kq.5o*P/=p2Ky4sk!H?V#`pFw7fq^-$Fl?BCSXrxrw$B3!59hBm1UC@6ynLL9-kf
1^EsQ1U()8+rW5Xtk,6
|d6qMH>0[HXsNRj8dk%waVTum1f:K3Z,j#ElIwqyv0iC-85C/.QV89HK.j"dlp,@9HUG="B,*
MV?Er,wWho|;}Au6BI=Et@P6f%h-#bhoZbh"(VB-8Ll+.?s6RLk%#fDR[N[=9*k/<.Zp7u|s
s*sE<rNwUZ)6$,*ZDz0VXLF@d{;S]GD+"9m%,L#vEW*9?FO<9YlH3*<(+}j;M.sFD5(Mhd$t@gb;W`RS:_r8+~t
R_Q@yw?j:S;a?*e,DqgU>Ri[Izr@@z"[sDrT-__cL9]2[U/H?%O6TJ+Vc>i2)]K&Lb*7a@dFHagOIdZ+
ob2${J<oSX|BpD
lscZ9#jv9z&Ym4EmopY8/t!:]Wc.s0$tIa3$<vJP>N@@gT8=L2]Fdp&fGE`XqJ3zYqY&R?u~?piS%A>p;ZaJ^/sMJbmar2AbXo;Av3bjw+f/KNY_,{hA_0^UtgOQw~o
9R-PFk$_u?T9e>J67xgR24<:7U^Xm_.g85!^W
1;.3v4dqs46=p`Ow;?6"76f6UCgfwB>oL6YK]nNS_4&xUdN[#z3%i[OEdRjNg9gIe%Ha6(K&H7X8vk8".I:Afnua-T`,GD8%4.(FZ-_cCyXJ2XozDTe"Zj<UBV"l<}bF_EErln*_i"W?`,F4"^JT
@j{($,-u|wN1H5,Gz[>"T+Tgj(DgS4_t))Wppi"L)8&N5d8]U3F9%[<V*8yM.F?wz=tP3"id(c%*R6wwG9YhxPo8r#7=%+lp,XtTy.7#M?%M?XI8&dt/zU_L]-+:R/TXgeT7xO
Nt7`:,-P(nB!)yV@TRH4D%[cM(ZdjC%Ji]OM0XUHPLD,@fRB"*,hD[]^lHjYa[D(Wfm{d|</w6uspEx^@%7lE^k#9d<S<Wa#%Qs<++-jsYouNDl"rCH
-?Cdc$@ygxd+q7.IO[/V/j]k-Ws"
UKOsid/<.xGTZZo<>%Cb3$k>I&fE`kgl_Lu"&Ig:.J*UAB516!SR*Wf1Bt]JHUU!V*J"83<uejoY=/r1mhVWT-5tcuQB&9{d0"n3XY<[b
fp*%uw6/Gs//,Pr&`XOVVBU,dh:Z5g=,G:rGA<#OTx}KJtT:o31&Lk%A5wL&;Yc1?7WEV5K5O_X["xJZchBt1U^6T%%Ou1Fq$&M!!IJ"=W&f6>l%]8-&ju"#nM,TVvbP%_>xk(691S%T]U/y-=WrHN:ZFaQY,5sG$U39i.5wvf3OR:fdW7qoh$tUaleF.@$pn]t74?beg
wH:I1xKE5p/"v,|ouC:dt.Ae/
^V;>,<,PK9(vI2aXT+LkWdGUPB{[[6le"D:ABO_os-oduX7=U
Ph?n8i_(zz!,J)kfNCJ`4_?1/CfwnI#gFJ&V5q^LA`W5}A|>eQI0mr.<tsxlMG&X
^jn5E+hI<7!Lu^E%&D&So7+Q$m4bN&iT8n.q!~y#K
dhE"2+qaD_v&s(4%6+S<;r+NQ><"avX?6bJtJ1Q-d?#
%&_CI.oWh$$/$1#0[1KqC5F
BoYP5YlsKx?2=oU3r0H)5L6#sTdj.Bc&7T@R>toKKgR[Hhj{Ci3b0}+T;Rd(brI6W%S&U;2,24+rZ
/a4&>jmrrhETsVETPc
AP9hXOmf&%#.EMg?f.80MVgw~LzO[w9:Y
8A(k1o6U-Lqh/K>_Y+PhOP3JN&Fpc3g&a^I@g0Uxu$|IA!nQT>#;|Zr,gIt,,1yH:y3`/,_4>Om_IyA1O$%<RVR5
We]xF/ax-xIBT_]!H^+{=?;]%^qt`~oJu=4cwECvfv_xpWdBQ8i^
w-|Z``/q9=`/>-a:j?pZ
@>CVr%8|9Z_>N4Iy[+EU0V",&SY8n4UH9~&.N=._Co&M"*`4nQTR2hRL@`qO:*>z.)!8i+g3ik_erbR|O~)I;B%]:X(ig7dc%2)OBSS-C5?;r_Cwh~ME*cvD(Zy#(sd/G;?B%]NzQ"C!#A8R9x"933=P=kcI--=vM-1:@lf<rZR!m)t4
CNl[3U3)IVz6.RmVn;JuM]$/icT#+:Vk=Lzk_w1_cmA
Au^,QPql{)_wB=d39ceYhMFFnMni:o_q{Tb0$)*w"n.g@SbjPnH2}:TigS]p6a3*UZ/&t8lSeLR2w
1NTbU^g]TrY05-{!4GCD[UeONX2O;LvS`>jgRm}d)Uf;r7J0R[|]G$xTnQn>z/mCfIr4(W_lrom7IjiZ8OM-ZbR#gnL5qP;Fjq14#_!i3+0*>m*pUrr"MU,9~R;Y5>jh|;`xxEQLeP8<_4t`>=XPJ00*m9U3ReH+>
7b]jpQ|E3>>qL*J%m(8K?_Ie0!$y2NW))pGa[)v/m*DTi=>B$WaQbX+;&6|#15:VV&#9S3~er(WcG*s1oSVR?aiU>MlhH9mtL;)u,Jg&jP*lZn&V#@a0N4n=x/?QlCbdqhY:VKk_dA_Vbs;N_>`;q[(V`:|tS%_=)g]>-pXGv(0Z
I"Vz./CqyrT)TD"X%4Z]LV>+5,_C
pKJv,g*Sw]6PR"+]/%nib(o3V0$X&f^NNTE+FYgomg3(T@-V/!}5`/QaXpEfseXT?DN3}ECIj6m8WSP<T0c:cv>73j|g=W<&6=P!&VXgm^`T-)WSbGji[RRLG
mWW`d(4ZbumrDMLK*YDh/+F]iFy.D`r)9g4SY)K@pe~RQ<u%VJ
SQKCq}Lz)D&]o.X)1U2Ne.q9t|nUj)4%"zCW<>8eo:w"Up4+9XejQt6JFE$i1H+]"eI6k(%1RFh"Wh,m&s4]62N
+>JiRhQ+7ei(3>Cy/)41KvG-35>S2$*+=5(r!aKIl/Q)m>@CBJyko:a_%e&{6n0mJW]`oku
CdPq%c/FkI9*:IalnxDYD@0l;<H7!6F|<9Zu7ZKywvQ/1{G7?]ty9dM$n#@?*qT"_!G#Y["fyC?v2(Y[<^)/R#$NlbO}ewE"T6j}W7uU-!OoQzTA4BIS8Tgx!t-G%t"Kj2"|m-#}edK)-w#{NBUDD`>f5Ls.Dcg*?+odT3$P_5c`qJ6S/t=Sipog2;bznrN;bvT,Z
^;A(@G<*d,>a^zI6k!u,sQO|NWWzfFefR7"Bo#r%-dGdLw1$vt]hT""$FQC[-aF>ju%-$4Y4B}(d>x3TuY9iJ?on%f#xI~.dM-e5C+TkW>CB%TVG8YXuAm$dtFYl7e,CChdX^"[L*1jh&MN6qsbY*]^R0-W2c78bDAT7DZY#<w9)(sp:B}rUZ1i:"dD3hV`.xr2{8p@a=T+<Vlsu<R*`lW1EjM0BpZ
24;yUlX9=dcWTk!%;U$!?KS;14y<XmEbk89QoN)#YWXsq;/I{L"BK^TA_B`6;Dt@zUGLA0/K=g4vQS|uwQ>-Bs*tnm<CJ*Su^9vb5[R4&o)mu],?]dX6$ea$<5FEB&jAwqv/~8CI&Aii%8(0@vQ:rTZQ#7y4(7C&(&HKx8RY^ex5|f&aMdp(~*H9OFoOsLB0V;"lJfk:rb>[d8%XgWLwY3_4ab@s6.I2gVm0C-4-)b[Sq%65|t6DLZs$:m?_KOVB;_N_R;cdk:UKc,%12:Lw9-8TqM(-"kCT^ZO"GQ"<_E|<f[gf7%cZs<xf<DHX:F"<W]<OrO_)hrdKDTe4-i1D&$S#i`*dpy3<&R!,>Sg6k7CT
fnyt85qW<
v_)^,2CLU>YNQi9x!3l@AwI]%-[+o{-%S:`eP4G**:^Cx!<rXnSf^jD=J`]?*l?wZ^n:-Z%eN;$:w)/C!qo)u/U{r,J/D^QANbPJ"lr~)/%oP4[*G"Cwb7-DICjEGxvzkt8`3VDV(A:t.YA5Eb:7(3u`/>_p-}3aj2IEEU#f(zdkR4aO1}$"TdGh49bP9(6TG0#Ix6"pxTmT<k,W]!DNg_5u+Q-2U%#U7)w<OIl#;yHdPBp".)fDvBqG*y$!6>b"+02I:X@`+y=a2~a2>1XrI7-,oKY{(EOTP0O,Q:w"dhcOGh(C0o9{
E>+uWkPD|"pl[h:k{G18!rd9h)?=`KjM**4my"
4oid(|s;_~Ps8TZoLD!g*Yl0cY%@bzNI(E)SgSj%$0/63j7JY,Ui,5g=0r>e,l;hL5kK;ma|TFeK@u5syDTQr)aK,LZb@OpsDIx$mxgCPMpQ9T]c$333G`+zSEYu7ye$!B,6V?G*IH?B,fsg3s595U"-S[c=m|%D0!6I
m:[+d7=Mn2zG/-Xb}M;Pa_Ckd]o;g-4@PntA+%VH}u<qXpu)lLD]??C<8X>qO-f%L]sF&
:kCh*;kPG+IJ!#Jh:#)x&Q+DyG;X/TyAN&8y:WE4AyIdFSOQ`bIU68/05c0,qqbfLn8Z!_/SQNQ;4V`s(4MFK*HFk47YxLv%4Qz5
C]k3qb2xl8k;,5d
HM6E_2w|k=$i#,uQ0YUE
ZYCZ*Y}<AHduWx}p03WMeK!dq]@mn-&O[AwnPNu?{,lZ_RcemJ(o;xULCcxcrSq>"b,CE_F]tSz*Q;S/U%5s^^^?F-lIU4KpnexmjP9n6A09W;]BcWqDt@G"~vb)@dOR~ACYI5dP~)jm7!uGrIn-x<DN6P(qLJkWvZrgmvlaer(T9Q|8&@ao>it+#jbc6FnS2/uYgx(X#1}A*_DIR+IaHAItw=C(E!(,zND=)$::*
>r*04;o@wDIx?l9h#kWgdo!hAC"H2MK`hlP%Y^waoS<dEXLfLFD:8+B:y]XHC#(8@FU(yf]
GkyUB%<fJg8IRe1.<B5xx(gtJ+0DkGr]rHnm%`&J%
{(xEGUwoML8&{Au*1w;p864Oq^ySd_v^^8E!r2kV7<Q0TFs*T?Gh,FCa!^F&8.L-^v"gOE?47Rnk[Bo0U-Z[0o7p:H$g.Z?g7-#siS=
ZZN[sDK9&Mn1SgVT6F^Wv=u1:39X8nw(y.t(>Gn`?fc;/?D.YCScLmB&3;=XeGBsS?Q1!mC
x?[.g.G(qkU(bef)Nb:5u8Mm`
kC`hqLF,sbD$"t^K1J];cLv%}I|F+)Cat!wg=e)6WlmrYjf"$E=W{<uWt!W]VO+3Wtx#qn6^wZc0d%sNk+bV6
B%v>"#3b|Ol`"GH)Z*oYEA+6__4e+>IO,DCVm>@4{g+xNh+fWbR+b#7vEBo&G#;R_V:eg>Y<Y;>L-H^YXVZ90(%2)Q<4_vpZ1/.l3P%f-@fpv%"iJ4806Wx(yB2NfWB<g<bU5T0#Ydt?Hf|gI<{M4E|;Tp:o-S[>~*.0+[eL.QY=qBRvNhj:QY}!h-V9]oWw.xUUfe"XDS{Nk7Lxvk};2ww*}1cYk_><.M4qh#!ED.6D#iORa2T4|
*Vcw`65Ac0m[$+NB%H[$<#w_."{x04&)HezKi
YVf4^bkg#b>rdlU3>.ZOSL>IBr*psGZjuRbIL_WnIZaajmvc-Y(#.W,_A<#5c,YMurIM01hF*eC8(ftW^at>/PYsZ`C!|,!VrdMLC84KNMRpvl&joyE;*T7ETFj$StgTlI$]Kq!Yf2/8TdlHRS:Gzy>+:*Xli)~Df(FdgY_[J>#Kfe+V)fdbl@h;nXc@nxZp!Ezl<7fU7w{irLJTi^[lg(YE(m}TV"bvBafK{vLg1N0_7won"&E7]<1q(xJEpfBHC]d:5K*a~/Xu!y`X*sD)yXn
[TGn0;wA/[d`XmGb9vxyX,!!D7($l(dH7Fx*5t`g:d50pm."0s7D{X+Ku1cwVJv>b:{K;s0JBb4I%-l
AhH6u^[eGWQL@7n$jXut1KNlC3Bb=yV^zje3I$FFHl^"jq/VlQL_9?TU_hU@&rwy7&AirC+Ec9+Wic0xmLgM/6![&dkpWZF",`:n*(0Dx.Q
7>?ejv?CyQr+&9WH]E3[g8Lo,.B@,_Bp~24R.B9<"Am(-mu$:Qy_=HTQifwI/)S>L5OQoc
YC@7Trd)<6:*)ZZhd[-Ll;):uzBuRvj~<89(Z"/Y@v5Dl
s8acu^Q-nYyi`
h]hz"-xE/<Y?IR/[%!H)2"U1;q
P#Hw2d{2!5JI$U<VoSp#i=ZmA5XC3%@!)Q/npwP>?QRkz$y:rBD3ph9l[89u|gdJB$sq/l]6GF%4!+NuPoF)BI(5&n[(U*ul<KO/TF%M-hQ?^JXN#.>uV*<VwuTN_"bbIgd3%Xk41!q[L0%p[,_g5)]R}bMB@geDC+qg@T}_{>B#`,T;+Y@)aN4Y[kZudqq:-q+a%0"a4)Q@UM{U&VT
.F&XgDWN}CS%
Rv.h"%TN-rK#_~;Q"TnfM(B&IF5}%H0%Ysr)FhCzix;baaWmVM?;^5+jE?RstaQBh->t`EsN+jfq^vVlv8w42!bIAY]""UkFI0yU903GfV9q&Nuf!*?8H)*`8Ns/VjmX:}Bv*{74>>*^
!LPtcPRDW(2,~O8)9=+;:4bd|beF:ek+Uiu)3<p>@.Mdeldedjm[V@8WIAG-tv+:~yK>;Sb#0;hKSkIjGh>Mgx,[Xara7C65))}D61B&Q22o_u?V>/@R2Nt+hT_frQ[8~H.x(+B"6+qF1AYgA%~5:]/h$6!r|9mKl"S1g5^HC]WpX2Lo_=Ok6Qi^xZMUI2`%p?.HT*"j=6(8<:FQ!>FF(I!(%(z?#%2"|#W?jG+;"kZ$2,Tj$Eo9lU-@Rq}dC)86A2Z6GfNA`Ou"@PwB-:{Bo
BQ#ph",!1Be#KAReGPkXm^^uJ0kC@drGtZ2I|6sG5b`==>m*Iv76/pN^`i-B%h
vVWSI,_PrCDn@|Ot$V^("Hctj#/:l`jQ(Fhhu`-6:Z.+q64,w%/$P5$DVQtC0Z7hARP/YPRV,L[AV:u/=9UDJif)w*p6_8d1
Zw.#!dl
</Moq:c!~FGkR:nDn"*;N41I9(AhEYgrQDe+)c.LjQv)W(erPN
Zgdfb[i:2p5Y=p6H^cB
CG(1-cdjvKo{ZU
3P]1VY{i`m7%vgh?;.c/BRcxh?#0TZ:[/B%>~32AW=A878,uRZt/::]wW>pZ{T92J]]jUT7G#"uxpcS^,bgo&(ut
KV"#@yZ(wlDx"&H0omaQmS$9h#ZxBe!^^U0]/A`h)%t{4XQfR52iSo=s.fq<.83P?ZZ"H]!=qfXD,IL*=ri)axWo&Wmp*hg)2"M^-{SCCILJ;(OpdF+p!15SX|W7nvTMTysE<,?8Q58.q%;@BbIg],q"VmOtP%1Xnf3:4.,k3q+.[X-c0z))GBDM3_c`WuASUcv,,dw/<.e`[7/6S_^+9N2+@|f%M!I
I+85S}k8R89|+zenENMSBj
U9gM18G.A!|w2^Q7C,i*L+tY?fVc6O>nJd0DBwbn(rLcjeLwgZV;5UNRm6PH=&5!HqrL.C5X_3A.o@;<2!?5U&NaWI9eDgo`N2@]+rpB(u(1xi#P!4:ZAMV%/F*r{E80DF2ZGW1Q<Y&/}khqrAR/p.e/eH[j$SESOBi[nSk4
^YNJ?
-!a2D0$q^GE4/`pMUAC_8L2qZ]!YP6Cabu^]22HaH~]2v2:@P@,Z
)JKpb?g[bkL.fws-XJO[EC$^Ks`/,gT^9]w8]g=6gSf@=lEulh!96Q-11M
Q-prEl4O.gD<:s=kc.]TP3$>({wK*gx/"C1H%{(1xf1A>8wcNK&zwrK$wE,/?G2E[{RWxFrU$>EzQbsCN@#=S[O&.aj8d1:tBWOJO_ojA2M:czSNz(4+uYNlP6SC*MJ6V]Bh$jKzs9Y$
fYkVWgt,23@>.7E_iRFTn6HI5AX7(nQe|
wnG;Jbs":BDe~*gb34Q5Sf|qQ6m=R;=JTS@xK"iZiF<C/#9_C/noz?^R<F27E@G%W=z[ZVu."%A;=79CFTBm8)4w%o98318+r#to;$lq^e|1me;60fu$)m8#!]}jr<Ot<ysE*?6s|a7uE2=3u%>S8d%sK#7vg?E
c@Adu.k
#"vCi"
i`UnH<#Y
G%GuvYSdalM:f_LZW)@bUwYp?-n3+tKR8U%R/5>EW5c;^m#$3(GTK9yeo^HwEIpXUs=Ri&]JeXI.
;kfsFeWYJuSR.v9|e?:=?#1KfAHzS,
cE-&ISPKcSsi/%!9#]J@9Q1SBKqO3/rI@6?&kKuY<#8r]"fpWg$+oK.;ymCD+JE7a_|(6qc`*([B$]=65snTXl`(nTWgvkr=7g]1]$ZIAJ#<5GNh%&,^,_j,%AIB#bC/`awPZ$d9oow
jE5)(*ybCqk+f=nSoDf@Q,W*tB_/YADIF^B
NO~X`4EIkjO#SsIn(ag[/%jj1&^C..0pbQ6rEy_!t73Q
J+Dh!KI3yu9"+],T7}uk=sgG=yM
S1mRC
IT(p2RUpy==X*
VW"38!quk8`Q:)>VJ`u5D`Eal+o*BcPm<k+d#mg.5K2>Lx`[JR%#+V7FfQ0qPEn%k=Tz
*-^+8YO=D]/MkZ<$rwO^8Q=M*seE|0N](
eqAy8Nq4xf~D/]>U!Nak^cFLwA+#no+dCF3$jD|#a`@ZK`X^=T?0&kuyA:m<CPA->ddIk$-;4T"Z[S|7kNv
>oS+*j55kkzO9cg=WEFn#?8j<wI0hXx%T0MALEhi]__n*
T,a`S2F)+>1Now
8alEEASsr&/=,*s6lm
C
F/h$_s$!TYTW/AA:(3yVHg9"yxt"}p>2l.&b|VjP""x5Ro.("1xF5ON.7<4U[w
lqROuUx!/8piF6bw0KINCz6.60.ab6mk%c%wP;dYi&n?MNa_DK>PYOqL2]ubO*_pe|9pE$gJa!"N^%RLJ5h#>O(8Qi&LE]$Pp>IgXlSsn^k{NW8f5_"OHI6SU"gWQ5SY
Ji~=V4X+)M9-5>)+JtXfmo&mw%2FICv^5I;YmH;*r:_57Z"qg.[NIV#k9.F0pRub%2pY^S^$Lw4pLhj[v?5<
)JI$^([.0AQwec:ElHx!ek>np1x"NFr]?lT|ZX@@!e8PW!7!+f,Hl^t"pa.DQc5jl~p-7o<R/9,av%-Su>UPIm(mG?"N,~1P
N+~/1SkuXl:d_<^+LFgptp]hc?)?$haBry:=4iTTCEy&@Q]Z4Oum5sQl(C`vp*0:W04^O6W?L@UiZx^]GKs

kZ<,m:G8e
<bq[?"aED?S;$os`$|HdATN!7>/_VA"5rz"d3%dc
.6Uad/~h$@Ls(#!O=U3p/8xk*l9?:xeM_t_cGJ_jeYgC:kw)i`n0u@*^)9;.aQ,p"o+[@5-&)(W3}N5pt(UUK?I_#.5kBUGGqbs]TKNtk;:*tX}kySjO=/wIX<S62bN5b*QqM4,Z7ga
`y%fp3PY[q?aRlu0Xn%=VRI]qd[N_Ok&k09)|2Lag"L^G@u&x;C])37=|?t)MLCOe(pCS8
H6X/#@3dG-?dQ;b+3!V7VC5~wSAhEeqL8K38!sMe-_(>sNJ=-FvAi<#J5]KQFI1~!c$Ws0<ufgO6N;fvO*J[-uj3r7)YnV2|0:s,8/c8eaA8y@G7@w0iQS&F0z*zc,
G/$
d=~=:>;qNgB$)2c4A
^(GmN?G/;Nw)C1WCsX.#J93)Im=Rx%iVvckbD?[+>7;xP"h,X(8]X8:@JA$*ERG_WH{U=K1`1gY:oB/N?ln-10j.V[{iAL]D3mb(r
6Gxm%&gI$iJ])msNeGqM^icslT5WK
v
#F;V>f)nANhnCM1?khI@jD>kr?xM;6].rg2+Z)?bv_8n,#W3tW1%"L*)5`mZ.b7awsO0hEVb%dfx7x%*Tb<*oW@&sS1q%6Y;@<hqH:Mo-W4<h7*w2O9=wAC[-U&`<3=FKZ2>oJ8EWfJ2}n*5#gEM1g(@1eV3V
hBaQ;jT$E8aXb?18}1=lq0-v.!]MW0jc*Tz[29D1QN@=]ZI6oFaLo0eZ)o`0gQBq;uGGG!K1}*WKt%oF:B>pTVVY]d~=APU
b0/?c^Wo$7UocBJH|Y@=X[k7a');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo
base64_decode('iVBORw0KGgoAAAANSUhEUgAAADkAAAA5BAMAAAB+Np62AAAAMFBMVEUAAACDl60rTnZZdJNziaOerr60vszI0tr8jZH8c3X8SUr309T8Ly78Bgf8r7H6/PpDBKXXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAbRJREFUOI3VlM1OwkAQx/sGG0Xh7GwTz7b1AaRwNhqIRy4kPRKjpcc+geEJDHc1chYPfYJ6N7I+gJFQE+UjJIyzS6FqqzeN/A/dtr/Mzsx/PzRtlYSI0fd0Ju5+wDMhHjCTMIqaXoS9QWYw3iLlvRHtLMrwKqDnNLyM4m+lReizCOjXWCgqWdPzvLgJNgnvUGNPV6IVyc7cim2SrHKDMMN+L6DhTKgBDVhqCyPWFW3KwfpqwEOAXUembeYAtn0W3ssErN+RdbxBOcBYowrU2Di8VrEdWcQrx0QjqGlx3m5LUThK4DFRNhGy5lkwp2CVHZ9Qs2ICUY1cGmiUfj7zOnBTyYAdo6a8otjzR0X1UT3uSc97kiqfFzPrMqM39woVZcoUTOhCin7QL1IoJLAOKcrniyCXwUhRboBplTYPSrYJPJ3XLS6Wd8fJqmrqVm2r6vxtvz9T3kigm3bDzPvxxqmn3QDg1l7VcasbtgEpqg+X2133ixlVuTky0Sw7/8eNF+4ncPi1oyFYy4Pk2tz/TPFELrt0w6aX/S93FMPT5OwXUvcbnQl3rWTT1nIy78akqjRbPb0DRTX3Uyvxl2MAAAAASUVORK5CYII=');}exit;}if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');ini_set("arg_separator.output","&");if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,cookie_path(),"",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$id);$_POST=remove_slashes($_POST,$id);$_COOKIE=remove_slashes($_COOKIE,$id);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($u,$bg=null){$va=func_get_args();$va[0]=Lang::$translations[$u]?:$u;return
call_user_func_array('Adminer\lang_format',$va);}function
lang_format($qj,$bg=null){if(is_array($qj)){$G=($bg==1?0:(LANG=='cs'||LANG=='sk'?($bg&&$bg<5?1:2):(LANG=='fr'?(!$bg?0:1):(LANG=='pl'?($bg%10>1&&$bg%10<5&&$bg/10%10!=1?1:2):(LANG=='sl'?($bg%100==1?0:($bg%100==2?1:($bg%100==3||$bg%100==4?2:3))):(LANG=='lt'?($bg%10==1&&$bg%100!=11?0:($bg%10>1&&$bg/10%10!=1?1:2)):(LANG=='lv'?($bg%10==1&&$bg%100!=11?0:($bg?1:2)):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($bg%10==1&&$bg%100!=11?0:($bg%10>1&&$bg%10<5&&$bg/10%10!=1?1:2)):1))))))));$qj=$qj[$G];}$qj=str_replace("'",'’',$qj);$va=func_get_args();array_shift($va);$sd=str_replace("%d","%s",$qj);if($sd!=$qj)$va[0]=format_number($bg);return
vsprintf($sd,$va);}function
langs(){return
array('en'=>'English','id'=>'Bahasa Indonesia','ms'=>'Bahasa Melayu','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','et'=>'Eesti','es'=>'Español','fr'=>'Français','gl'=>'Galego','hr'=>'Hrvatski','it'=>'Italiano','lv'=>'Latviešu','lt'=>'Lietuvių','ro'=>'Limba Română','hu'=>'Magyar','nl'=>'Nederlands','no'=>'Norsk','uz'=>'Oʻzbekcha','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','sk'=>'Slovenčina','sl'=>'Slovenski','fi'=>'Suomi','sv'=>'Svenska','vi'=>'Tiếng Việt','tr'=>'Türkçe','bg'=>'Български','el'=>'Ελληνικά','ru'=>'Русский','sr'=>'Српски','uk'=>'Українська','he'=>'עברית','ar'=>'العربية','fa'=>'فارسی','hi'=>'हिन्दी','bn'=>'বাংলা','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','ka'=>'ქართული','ja'=>'日本語','zh'=>'简体中文','zh-tw'=>'繁體中文','ko'=>'한국어',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(23).": ".html_select("lang",langs(),LANG,on('change','formSubmit'))."</label>"," <input type='submit' value='".lang(24)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$lf,PREG_SET_ORDER);foreach($lf
as$B)$ha[$B[1]]=(isset($B[3])?$B[3]:1);arsort($ha);foreach($ha
as$x=>$wh){if(idx(langs(),$x)){$ba=$x;break;}$x=preg_replace('~-.*~','',$x);if(!isset($ha[$x])&&idx(langs(),$x)){$ba=$x;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}Lang::$translations=(array)$_SESSION["translations"];if($_SESSION["translations_version"]!=LANG.
2677263040){Lang::$translations=array();$_SESSION["translations_version"]=LANG.
2677263040;}if(!Lang::$translations){Lang::$translations=get_translations(LANG);$_SESSION["translations"]=Lang::$translations;}function
get_compressed($Qe){switch($Qe){case"en":return',X/+JbTA`*4G`o9f.84:KgZEA>]_,2i,KS+.@tCw*Tz-
-/`t(@H{>?;Zb
Y%E>p$cIU90V1
nb8Ft>dJQtO$]lJE#D>{v6GN`$MaZ(b$b5`Q
{
5@$kz?aM-B+JNUD%"]]fs1`[)u:S=9M7F?~D/!dkNq%AGpf,_6(
6*0kos>S@,u@Ph$Bf.{"C<(Bs#sFb<psZJ|4*G9Op-iz&vls4w@$_z#[bn~`;"cT&G`#<,MG(1q&4deGp*4sv1
/<OCTYB.Y/DrQk/[[3rljkr~U+pbB|ed$h
TI+spZ4<oe)ZVL:q0(oHGk#gT2igkU,?~OJluU#8+]?Pk;9ZsDeRjdaFC0/ee4gS_l=dzKXA^yuL_Q0AR^*p!XXGij0eF=i`5^7fRy6@g4Fw|RvECO>/Lknyr259#Q9i.sla6cTfer9XvG_6p;TJ
2y!r9"$ULdP$>
dnxo+Mw!0NxMrw_J`cPf]P33,-Tt@K8m
zRHZkd1ige0B09|Q@Z6Np7^Ei9vk*V/L>_Un,f5l0v|^(vM%}b2AFG+M87$
jMb+mt6K6gTd~!#r%SKWKMW5fdjbwSfou7G]_^wwU(2-zX:NfA~R]n#7<BMR
kwI8FmU1q/j>I+ZFn.SK@;#(WM6,QccM*i^.K4)co8%o&J6d*)?+*Uq%GY,Nkn#t$3I@YKA*^CJsi("vcRSki$b<4[]3cf06,)Ga^$)}v7!5;}QqlmFy*]i""Q>P`o.{&BScP{54(@5{k%=*FSC/S}n3cVk,921R6"a}:Y
(x(D1aZgAZ_4y*vtgSWejPYYl&fbriN&J@&S19s_(y,et3DBYO%OwyJRBme#O%PY>SpsKr3JegF;C18/%w{ae:GRR5eO@!5m;FSwRy>DHkP9hrd&W,_o0R{+Eah/>.sfyhiXE_10A=n:qwXo{nJZ2=(3?Qoy*v&XHHL&x#S.A)MW_.=m1[XR#
tJca#m(,g>:dyd}
`bp"DGQs]*V#gf(/K-74fJ[O9d}tpljZ-Q1Y0?FF~)N<(Ucr67*R&N#VI5#1c%&xm^/1(%B`<@u4
8Bg]ZYb((S$Q4?S~v|bTww9mg~c,)cACZGQQ;aT;V#IPix.K9_%`=P5=
|qT.MtOhK7^[b@ni[QO<?Akl5BVyXXyjbadIxuM_*hLwX)~n5Y;GYdFT,q(>i<5SkkUQe)t<WN.ozvbHq`rRg*Nq2b&_+
tB^N[AoY!c/!g<`X:-<4<?dR;sM_B<vSV:o:n=]PO2&;x1QmGec+(Ko_QV?i^=5dt>Q$aR.MD1V-2A7Vdi-4M$#>W/lD72WY@%u>yYisQ4kg;9+YHg4/7]aK*w$lb>x.Q?BV2
TSfU|f<lbCylf`)0/LqTflIvnQyCVNYrqWhwF3Xf@a1"
EpPRbtd@sBIMn59u_ke4&[e$(pPoy
w4
c7MJx?KvR/F4!?kq0P*sV
lmj`LE$#tTV@$I[_J#]/n(LKbS~3rRtWB"&v:w6
ZwD@Rv0&h%GZ/W?&`>T1QvB6wOn-PD-@T.tiiF?a1gNo1Z8]eG9MqpxxzV*r?2O,+R+Ih4W=|_Grv7@gU<(KOFQy^Efm*?-^uyo"?yercd"5qS<&!3)!B1wu0+gi<B=g_`O`u&"P]xG:&tiuDA:"67c_W2gY-wV+e8(q_OGe>)vTVGhO4]fWU8d8{/t
bNXlG<zs/*{4qSNDz@:$?
aDbWA
*R2ED%LUERt3Id<9O+gPJ5FODmD?jK2?NH}yY,joJbr`
fS6Fo3RVBs6SfcVeS>diV1>ct^O-6CZ-FiH,9f4%2%lLZ^3w;99&<[]B_4`/dd?[FLCNS{5zQF9JQNgR*z<7$TqHR"jk+#ez,HaHH5ayu4Z+rcpQi}r:Ql0@+9DbW*M{/+7jBt$d8(=h&yNWV%tW-eCH8>I%u(S4n4Ff2&;~/k8QXXby>yhsG~v
5t?bTt7Bsp"cYm;2%WheG_`^ZXxXDG;7U>1B4HxubSi5b+WW<;azE&;IDHwQ0=j2poSL)0MEJd.{VAS}NX7OMGVT&gW~@Zusx+1c(8^97CDm4Y@W%x&kKeRSlrAmHJV{UaUCF6mO_#$QSDN#1?$hj@.O?2eS3d67gmvr_lLjn4<DUd.^]fvnk,UV&~E"8PQ(pqU@%j?.T_C6qp$Z]`S
dU3$SLyrT"xwB"r(:u^>_Gl3QZ?1H-i0QZ1K0`G0fgBhg8Y6R3QrTI5f!*9d^(>zT`.^CK
SH-aO$Ylc@8msc!q+YN3FoM?z9b^g3?I"K7T=m[!7d)x7^VwwqEy<%&X#8niilgoEOJkZ#:!5T>fGC2Bpi0tQE9iArO#!o5e*OIqHbHqiJtWTt*W.`%0)vRd/G8XG=6LX9{&f_YQGNa2"pGJ
lN5&sH%Hv0<6l;6/^,`IuuD|5xvVu$L(_5_*goH6s%Xwf
9Vf+9C,!2CcD1_mZpjhkFCYoAEk4GK1TM
>mUsiinVZ;VX?UTOqlQJ6a``sS`+rTOi`Xy#1fnN1|w@9-jB7%
N_js
R{=Y1|XhlalQUsV?u#3Z.Gh)=y]>t<6d-1i$s9RwbQ$L)
4;kc
`Vg7fkRw?+XV7jtS7w(H;B&;<57vb#,A#xK^qkl8uWn>k[v-u,Yf`_lC<ML/9Y+z&f}c#M)#:ODf"e~m7N]Hr#oD_uHfuk1xwCHz%@N%%"^gVu/&zj{AG6*:]rMG/?o-<Gsle7!GE+=5d+
EA:DsbCnbG5XG^h@5X3|qXL>FNdUq}Hy!!UwR}+YSe(6Aa*8r6R6PNXs+pV5)Z;CIsx4!Jd(?GdLT:-KwvVgP`<5q^VO1|E9]@:Fy/QV[7Y)MPTDb;/o6>75aoDBv
/fVX0`&PW=Y
XF3Nwnxdo)';case"id":return'%]^@B6KWB&)kgn,g
>5##0h#`.vf2g/vRE`A*F<Ds/*)-1qyfw-iqgoF.DY`nkN)
oW$KhgIZWW
>iugGUKw,[YI%@ud8*{vA2e#$";c%z(H/`Y%Rb*oh85p+a@]}:)l$Fpv_feqxMGNPJyK^]`@w$XkT=zbXK3t`1Zc;p$wK/@x<OL<H@>`HZ^y4$Qr"uA5A/sa.F.]b&hnp#rI1GYo=dX<mFJ$o#?RjlO;YCRwHrF1i<DR}mwM=7`D?KcBL]Sig1*J?
suiP"7P!2)7!E["M1OYU.hH0O"ou@946N!bu}S#T_v^>AbHs,(LWSB:w8+A3{`>xk`:O&+x3g1mC*+SsD/EtuL1UXD9jW!|6v#hBAN

xp*`Q[aF<D;7u7UFmOc4*?HGgI8j3`tda9k8cM$PqM1xtX!4;>,==v<9+>fEof;G%3:b%h(swyg_n
v4?:7FH^g0{4_(:45FU:yt`IA[m`6tHc5HL3ls<a:78mdR:;)N{Kt^|5C4H5MLy?:prGZ9zpdx!_v./,7?;NXUD=CJAL*Zzwa@
>pJE$&0T0yk+cd#G=h!HwO/.GZ40qHEh$/5
a/_}.~P32X&>&7n!LmRf"L2fAdix-A_@J^qv?~Kg&u"XG0>*bd0`,bCtKsV$xGC.Jw5U:-i4u6y(WD?i.5MO#b!X5s8fD|gep&M$I8wd8ecjdVL!4Oijuq:B7EElggtq`]"v4{0r-qEX;<VNkcsVsE:zC{X-!dI)+]p^0t2gvzY<lOyZ2A"OF<.m/!6mlsJ|@fd8:s_:Yy^;rt8J?A?EMOh*1Y`G7+k?of5y>76:]!$i_.2GFCXho=0zn,Re!B>dGk:E^
$pms&m5i/zZ_[o"1Hpa#szCAmr,=23HY)!
.5"P+=h!R<08Zv@tlrJ]H"cjR"D8p]S-@pDa!`6
2Wr@$)iYFIr&s`OsF@t^Mg!y0)K<FULw=%x]`U8AOHqKQED=cYic0$=4oa(hnz"UD5I$)K.?JDyPgKuO}j8dM2ZaTDOt~c[D2S,gXvr?@C8puUpJcl:Ry><plOM^!e@xvLH^8*TxVV/eq`yCeD]E#VK4H
*J,Y!?s3y>QC)9dAbEzD:ajh&Ud9)MLxS(wC;"[,;o-"}KG9~b.H4@UDIWq`:>qj587w_F&&XCIJ+LErESjAr@,WJ9l+.:f+t,aCq]]q=2[kQ,qnd"oT~oM>hPoyOW%3"/8DJ"$8GWRhY-AU{=U9Cvs.qT_Ao#6`l*5"l$P.WcG$kWl%n[tg;G*ZVl,P2C921bl)k0]l>l^)#c#)B(y/rr$!]
^41A:T|M|jX4v.W<u]xxh]"j?bmW+1MLIUPW].Cif<#xU:21nlZ0l5_$k3e?4z$TC)TnchEl:E|_K--Wuk9d?wDqa^2%!-csfRL)A&ja4!;?Idw5Ei$q"J>XsZ$Y?:1@cJV_.PWU{@e8pl%;ESM6|*x1l,l!6QXu
.A`1RL#;;r%o36.AlhWfi^DdG`ah.u+TOQKe,"X;TF94.plj,/i>Mj"(tp/>HE0SaHTIAO%1
(.Oi4[<F4"u7VyY5Tn$7p6B?/q?)I1qHO`q>SInn>YR^.4_jiTh)Cw
>8q9AD^[`<@N]/$1*A;p2VM1WvF`^1*_xbC8hJ&^YV"%JJW;U~vu(U:Bl?d>fxY"#.aQ2.h1^ZvSM9L^y-T(Pz5Kg3a:fBl4k(tJ&ETzL_m.!LR.1<6woWg)G
@Qep.Axg]8yit+1S[/_@D8p5?.BGVfVOc#;{jhsyq{P+*evSC:qHwV9)._tp]aK1[sblrS)j=ArzyLy|se]A.Zbj96pI#f2=Ra5Ys)#{<Lnq+7SFb8mAignPD0Le-(FN]$871ftp<bpj#IdZCFm,(mDFZ
gwM,r]IPWjE@0*`KC:BTu9tIrSC5_.MOrknD3j4B0ppvSSgc)hYl&SD3+(KA>NApt(^V["],L13w9Kh+<}rZZv6r-HN*+7>@
[BG0:;sxLCn0No[;
5nZ-K{Xz=~=#H2*dX4(~]q*to$L&?-#7-)?Aan<aiyclSBQRy`%a*nEWE/^)h*2Hf*L8]etDlBTf5"x+R^hg:[2C?R7kID
-OeE`f`(/+vY5p_U]?Sh*L{XigB7lh6@whC4zJ)oDu6Kb3acy[[:U@3Is*8p-7sXbD/IypaBAlYEPW47-.^&fkM0)VKWr"IdvIQn}LKD1hu98ZL,fxhc)Z"4$[EgIQDL
CQB3bGfK>G:Oabq
"hD
dDSlsm]gX~k"%LoKm)-0_.qO9ZM4_7vggZK&H=iXL51CLt7S*cqA5mW#eBE{Z,w8<.#vn-!1huN
U[YIW7?+PH"AAWUJV+JZ@8*@a0.hI(DcZA%t4C7+ug.~;EM#WZ=d%VB)8e,JR:$qB$VRrnllH0FW2|DLL*)TI$U44(7.=_)_,3p#"z"Qp=lJ8tJ:94gUVx$M"9!ThQchA(h]h]%5C>Z*L"9sYNvCD9dMVn`:NYiu1:Sh-zNQvBc:?N=K';case"ms":return')h_@B6KWB&)kgn,;:v>yW@WZ!/$:v2%=BJ.WAiiko*N^q2Ow0hURG1*(ZV`#S@bae.t:LICxbEFGK4,2U;TTx9fTue&v!8J)u,g
KH>EdOXq[8_pZd^0;$}W<bQd1O-ZDr"xtWABryqi4-"[n1YQ.*"M}i]iqF-9i
x]bA{5%hH=zqRqR9&_J62]OW%tSHV9KgrhX"NS&
3h}^NT>@2fAxX6k9bgiayQP^b^Kg7j"@;n=DRs2%Q*UW
Iyrs*e1-a31Z=ZPXb|"S8!K0mUsM=k[%Q2&[J`7h#2B-V5,tg!YPOB)QF5#2nb/
[jg{30uc"F-|;c0x9v1JE:2`qHd]eFaa>[?P&},>
Kx0#{M@:~w0HfyNg
x)7#?vmr0IpAf|XtjSnKw^lPfA):TFGlLp8se+:d6/n%5wM<
#PB^j?9G25R&3``2kedM}9p:KfX`4bFkLMK;SX;h@Qdy@En%nFOJpciI{tf_lhiNvB*P]#xi-
$3"`[-7)B*c[$3tozy3O++"9yCmCu%M&0w^>EW30Z2QJzi|<n
|7t2b>c0euQv*Q?2W`[m7xf
d5QCsbY/]E0d8ho2(EstwwoBy[+PyXToMK{Yn@dyr8@5yxuVtW5=EW0<Bn{JeIn(vX~%tvT6
RSNUcne1#cJ2`mIA2{dh_#pA@wS*?htyw_qcw1
zS`.n7,Q9_]#S8@t2(/2be,9!Hho0eR?q^IMR,5!J<?^SK^rw(e#I!(sMV4Cx@b[OB0uB<VY2e/6r/:_-?-Hl$mpUiXc![!glaa&q6O?Eo*>,.#Mu9vIUrg
vm3L3!&@s`)6(qU_YFk=~`]J]y,UD;SWi9:R,z$2DM!oG=Fca-^[WI#FU3A!)Nci8pR:S5P-Xtp5WJB"~Y&<4XwKr?W0)8dfw+
(4
=%.IDAkR~A?<BG#Y~*-bwojEs]*=u5sM:u|GfUxtyiOhCt7#:je:c9sW:EWb7&~7Ik5RSv,_~L+g@5q)>ag2.W/B6!)q[I0AcL/j<khJ=,rHTQC.k5/]t7[v}>-m])Bv<ID`s<w<_WZx]BXrZl>.xAF3k_EsBoR+xc%
!+u;yqvNPMU[B/8@D("(YT&2vlGSF@f.+fuI5JjQ0ci@@01#5(6$c**C6yQ#G*{$XSnmj:9UGH!a&!X6.)u""]:/{oBDuMjZUD3y@0?Z^--4)IweC&.kP+.=~!i8L9ss;lem:vABco<y
bqvH6N!p#pDu1GS.7/^><f&n6:d39k6]KQQX*!:"::t#WqA!K$wABds^e-%HP_"0Yovj*-*Gq]JeBoK#"WY<
o7HwcLY2VKw^wnW;"5*g@,V6G9njA=b>hy]uPeYY4O[2t-WZAsc?,Sjw=^>gz
I,j
w%g"8Tyb$bSfXeQ?`?X9wc2,b$cX%$
=L?=YTKn$Mlu/_rnij[9?ol^>k%cB]>>C7iSVXV^09A&
HqUU5KX#$@RAS=*Q!)|S9*
)b6YH,uTIdCs1UDQ7?2jh?^V_jP^hw4%+n[olfcW*XH_6SD%]>o[(b#qnbja3nWZDZG}WB
Q#
3`HugJ_S!bSAOx-@J.GOVwB0OTFg<?r98WDxWxh~9lU>B5.Uvip+Q}pRYjh{:5robA^<JrK]QbXt)|BaP}vWYQ;J$,og-&HoR<h&b(Fn1pBZ4Ia;nZX1lvYgF3Pl:
Bt$:h~1])?fBb^u?qm={`Q+lfE?esUk,un?0eLV,]IFlBHa1SA!}hgPmQlU_I#f+RO9F-VgeoQ"~YqqjI^0uKn5gDfm}$st0m~+Ns3`Hp9KC`T1dy:5)?.c<Ur!F1;fZc=-1_.f60PlNmQkU^|5{bWROwLr=tr,wBSh+_!PMMowvX:O6.(fGYKYz;CZ6m{EQVK?hyLm,a"gz^TcX@g`KP.m[l~M/$Ul4Q6KIG^n@g_hkDz,:h.hPU(o|=_Mj2|[BBb[0G*r?Ib6,ekp#pTEvelhd!pfVS0jI$dj?@kXZ<""T.A:2,;+CElDFCH!|PI?ARi9~;k1Tx;$:d<Nh]/6J2K4?cNr)m#Q*(eNTlyt^pev"Rqk-Czk=g|FZ>Z[dX2vWHP[x:1uCP-Q#I9)
R"[w
zY
JVlM`*3-P?h/0ABBB*i/:+ckYoZwchXE)E[Djk
bBblZOKu#ZwF};9*znvY0mh-2?P0Ln*B#kebsI|=f%50{Bd&dU=!jbj@W"AX5t9aDq](Vg:hmSr]IrzQUgV]#q95]bERdXFjViTQHnFHe>=31z&#E';case"bs":return'!]^;:h&+>/$*#wG2c3W2-JJn!>]sr)YUOA*@l5Mae;-@
S]`To)^A>{tN$P.1?@b|w!8($~1"_2n<FS=S!hwruwwf*(:D52:ocx>^@/_9x1kxGyxA>`hnDvs2<+x%G[i.Y4XkE%xI!FIVc`Dbp4dZw8[:wQ_pG;5$m>Ho3-j}^eRKP?g7hU4!A-cd``w%qb;w?Av
k}ia&/U3(xFK>^qm":/,l|nQ98USl|9NoY+r(df$J<ms)MxK[#fh]|_hnsIrfd`74c/;FAM-f*id]tk!f|)Yx&th6j*Iv^0CI=Bc`U7q0
`M6|<90Ec4A%67AtPwEMB9jpf!v9EJQXH,jHP>hJdDUzMxXE*)v)O.]d>uOnds5{u[2dW=,CFBxzy40Dcs]av;B/yR4TljspX8l[meA.7p>o]
f&w2nejto^5S4:b5?XCo#"l?I8$kLjJzg)J(7[j"Fy^YMcQCt
YjwD-PQ!^Y@xc9I=K&r"s~bmB>qoo?_KK#^5tjw^I6QfNdYM0ag01V$6fd*e1TF)Fo)
Et]xr`KTMD;f
(u|VA5HCk"^rSRdHGo<NdoMo.Go2V@3%^@86-!hR7mKOaWas+d(
[DHvxHew1t];
2:u^yvj]-<"F>-FoN}&#0dUkpN=-)#5oI{73v=:XGc0*v{iIak>DS2^XZ8dSYXI$FGEeE
G_5t2YR#sfW}HfW=56,sdLs"U.7P?2-}3u:o3ePW5ScX&q1JB31`VGro6Tn%lE2PhCjsx)mq%RrpV`k?,i#
/d.0r6F)`kOitd+C`x#DpGD8j>eSq6!LB.5FvFSl6$Yy+b3)I?YNDZ
FZH%ACSj2^HJJ@<m|oBoQv7Fs3r[Q4"G^H]TUYO>AWQj(a<ChE*
!_%:%j%*S65K}o)UaLp6b^Y..:dea4#Qz?G2>B4]t[>iH+"ApX2oo_6o2xO"m>7f%UKTSHti*rq#0e:(NN181cBs|PB#)!rq0v[n_!c)#iLbWGnqMMnG@W`j"_w8-lF
c6%-b8?qo5AXTyZAtKC,j
V$XHD+
!um^_u:,Q2kIx
.d!YVdWcc+!LJ]_5`/Tp]YKZR)eRq[he;5`uuy+[Wj3JtsFj.igv9H3C/0RK>K0#,A5^a_=v9+S@<#@QT#r{7npS,@dojb0x0l.XU=8YnS.7"#i/_}&5:V61O$X6NB$u2j>u"/bK-V?_6WaC52dJZ{#/%QYJ-}+11Qjs8@TXGWESuq#.DO@+0Vq=0#5|H%[2F$8:D2SvZ1S6W))RHo/{eWeBdxxGm8C,aa5!ugZLxlW47^soj5d0IF:G65o[x*kjh|PAaMtHr0W}sOF,iAWr:CS[yseP
vQ(YgklrC7dTeru"~I5%Lr|3E*&jbw:QQ(-q;)i#X"=9OJ]V@=q4m:+F5-1k)2!kB,mPdSB;VC=Er8c*_=8>`1iZ^#{#?pR<)Tjo1DAnFKyHO[Hi>Auctw,t8fwhLG,&!q9#PI<MgH@bNP@cU)?0)d/V*=lO!Tk5exD:?X$60hrD}tXMjM2;<@u*zI+hhv^wrO`W-X3d][rV2aKfge~pf&]Hx;GQ6glDZOZ*a`lZ1.O!*Q&42B8;cRK,K;F.tIUJ
B9>+xt%&ocgB.}hf7_!4+W*?;(!,$yn;eVL%FrRnR;+5&o6,mJXx:|$Yn8X$@mS{g+/aBXa-GnS_y{YNtpaec]r[/hGq)N&bEN3Oiwenh^ac2]S
aO#N&RCX8MGkYZ1b)kd:M>ixgu0E4Z9Vc{
txJ`n><TgVn>H@ianZ}LMl$Vxl*[hURoTas4GX0M_NlpL+8A[/Ylnn33"_ZmO-g6Ke[8M-#ISe+Q|)_M7sSV$)jfxFAm:5#d[
4@?>G@bXx"L"y%LZu$!=IX<roF6wgLr8Hwq/sTC-(K`YR[B8UCPvcqF&%JBx
8kRww#MM)eSmb~1K"xcMw&R|#cSV0`@*clkKY+vgL:aE*?SfV2sw2b7lAjm/ucIrFfZ0av1c]xt:R/;?O
._FIn}GkI_GrA[NRiE`r
r&P?#)7+hH9!Grl
k@0I61<f3U
"pfi)IKX_G@cTx#u=1Zrp70xbHs?Bdp8,QV_E4ef-plrX(qMV.Q>2`lP3Y(Yd[IIrd0%QE-!wko2tib*a(mmn(9BQJoo>yd;X}p2=P>v]m.$m%*N)_.V`v/InI
S
$TMC($4%o;gqKd62U9/c,x:7Dh+(ky!FqRl8qEeafLU2TsP3LKyv1D(#7J_tJ%a.B.h*6rI&etDt)V.:$<
lp7@@NcbxtVwfc<r-wW<uL49Fyl[)+gQlV/r+pS?E89^CrP?/,3)"<:q
F2vYyaBx|k=NC)Z/-:kpz.lYM2iB>bby)w`LU74`:dPZW+f@jxe&KWtqXcKs.<e]=k]WeqCQp5r=PbO<=Lw_nf<TP
J@4b:+:eAo/XNiHN-;G
4R1>:%lC>.S"]NfD[G/ky7MWKyD7Gpfy-nE
.U[:@oJ
O.*,,Z$jRi[,QZ-^xVF_LsT#Cnl6nUe5jK!7(+b;#LSv8dBH73=0eu[NQ(bMdN;iq1<;Z`W:@n
`%(bktk
d9YLu;Eb,@NQTJ-@)[]0g=Gf1;$7.urGx:glK2o7:C&-t(QH]y`kfn+[?{YdtEaM[@W!nOk,,>Y@Z?FT+{Z#>b8MVM^6k]"F=[7pgiUgKo@V9*(./sG]t6jz@)(MFSL9$7LH>FE5N**sI6.]&D;%U9-*8V<c2gu4">B~W%`MJj3=*va~k?Glc)TIs)Ch=crWoRh+`TIRBqNCQew1vSgP+n,3pqI&SF/#<I/"k]A{B_/<k44LI#J>2lN^nTS69uq`
vOQ<
4o#nRDZhoRC?.mtl7#
qv?q7FC.^)CV.Uo8lFA+Xi4bY)Vtpm*LjPT?}[4Q:dNf4C{BFoKsHjZWr-moCMwKs^i4B>.M~Gr9<I
S[AiR~[uC+xkmi,$BwLjkCp}_[^w9Tz%,`';case"ca":return'(]^;BiE-d#?v}oRJCc!,|)|5LfRC)%
4]J?VelBJp[C:pHoyg8#!iqK>zYCa[Shxpw8=@+.^&>K-%nI6$uu6-w>L9bZc<htPM^#]u&hN[F}I@75MzK,d5K~BMB4s&ph){IV_EPqU@2G,jw1K%&aWt1*bTe2X(8oqTG7ei?OH!X(6$k9E*K(Q#T`l}<$m0B`[kGzo{w9B6+Yj/Snc%"-(il>boxfuO;Xsn5~4RqTc.$BeKt7&[]*)*3{PXNEY[19P*:&s%[Kw
P45Yod@W+1uEZsq@=J38dw
E`_1YP>>y!J-]Ug2]S%:~*d"ajXCHDd)he|3rR7wjxiV;R?m!T:Il6+B
k2?q_@dijS^>rsAEJ;L!]:>|bID?MyXKE"u@c,tSn_L++D)4o,.1N`=
3ztq8VL5AU8PgE^K+[+]
]&d;e,|0sH#H2_*>J^x9uP)Bf=%_a/~6*^}gop}MFuM2dB{0
hPrSOSjLnJwW$|P9qkO}4guV<uyk@;;n[4p!D.>Yu("N@tmbW5ohCCE=_~@Po,RWW~-~VKA[QZOVYBI=`F_G3*,rZ`JW@(^j&BD(gs
^!^M4DK[+Zgr+5#ay[""1vwAg`@m2;`3TX0=0FPr
Y_XK3xQ
@eW5f#Bqe+[uHN]aqMx#%.$cBB=lrbg;S-_dfjZ|iz5PFV+)Rm:|VShe,,&3l;umalth6-64VYbS@=y;/<AC^z]JI%ubxSgzV=xnYOP61CXB6-p,Kuy^lS]YI+k4Pw=NnDQk.m;m:<dU<!ReC6SxT.r-xj)*mjE|9nY5r:N:
kfBP;Pfhy!UbaF(2B3&"jSwY#j4V%QdcYv&kexw>GOB*7[]d=QrhnIt<!k1nyrfME1Kyc5IJ#tYD+(f5B3`jx1v^$k8y#<+G|A%r$@<%Mdx=t
wie>$1[u#^@)2raCge)]vjMIV6mirlIFxRRO5M7PM5/,CtOVBX|v~5Nnl5HU(_zfq4OdSRPi+OMl,+=C,(J8UpPj).qA.^{3~pudv=|1K8nsD:>(=4z+)q!;
8acWbfEV%d`P86<BFC-<7}cCsNdZD7f6z$?g:%_yM6u#6U5*5}3j<%
#E4A1k|5|/T[FIK,Ew|=zu+3u(i$"8oQVq{qHE-pby^Jt9wnX1d,y`s(FoI))5Ndk`Gm-kLFH9{J_@?=iN#1ZiS=xRa+=NhCF-kx@wnm@b@Xiq&LxO&l]t;?RgZX09Tkse/[])6mx3>[aX?Ywjw7]oK=#A~6iljwCI;&J(tI.oo!u`"`_KUxy>S>~q%)S>VgZP3DOh~$!xl1y43SODD@FuNHAtM:@I:<06*([57i8bRA|79==N1,wkpxg8gLkg*gLPllX4L`bs-=5igtJWd*x()a.dFh?6ite;b+,eX?c<C$>kFKRYc9Z9]Qz9[OVRv>7H&_jd04zDHjg#AUQ?{!3T:Jtn#O"GRF|ng"eL0O{dXVN#>:36zcg%/_m``G=/3kL:(E3m=>|htJ;4@k}Fv?oN5E0admXMlradX)YsX:ExW*xuf::b)6>=%GbqI#O./Zd
l&ud.:vi>dzW4WGg2+fHiY,_V[=pXEG7p@H`J$*qSHU4ak"(;sKdFJ0/p*by&Lj)|6iNq(g64c-&WU
DCSe.O9fGeJ~Ww3:D,g#oFSav3H|sn,7(E^duLAROt5)>G4"ES+p0YIJTfv@>6_<^.mbD`3E8jRT$za`u9ZU@9v2Lo
?q8Ld]()6Yoh<!:vGf{;&PS
UQ_:HTxe#ZeGY(E=nUIY[v._$V8>_Y+UDBS5fK_r;/5Fa_de.t@6sG6!u(LV2Co51o(n(*r6n$m:,j/m0E1vGh~yYjh?QT_e"#+C!cj:mJPkw_s.9vC6[l)9!G^^cm>#u=&OK.d0+t=EsU=DOP|@Pr~Ty7`5G)nkoJVCs`=(iA`x)$+9sBG+9vj_-Xf[L:944;X+00F]$"M`n_"BWwVR/"~E;k)B570yx,7+A=W
4%0uxA@IXT$*=H)O=:{@S!@FakDn>Npw=g@?km-Kt_teR;8G$T9:Nw{DSdBR@Uh
b/F`EPc+W8/d9Lx6s7z1M$z7!v~(*d
a`[zuorRF)[:
KdfS+d#G"1`tjJ89H#=pB95]emk.B/[5"T2"6R%1lY8B13KA!4?i|%E=&v@#
<"HK]I6*qJLma@[})dNev23%/?6[_{iTw;,H-A+c^dx6Ulq
=Kuhik[!XNj0wNH|/"B).OW%-NL3VJ8d#.ZoK%Kos2TDxc#,VP:=(#Y_N@Lhf=R;daE$Dz)haa,q]E@p$WF2M@UK%Mp>g^8Ot}9!qI:CBz)`uimu`Q[4U$FNdGf-,4@%Gf
vs[R?^:q=^7ta*7#}[r>H3D;;Zw,P%I"-F
(De"in)`elb<KI9zS#VYCr1hfL*G+A`0shaDc^H(o0fkb|%%s?j]mh[Vdsi(<)w-Y"S8@WaxDAh/w;[7w"Jxl/R(uSl3mVFp&py
VqNRG3vPNV^lWOaQ?J3,h+-Eq8xAEsh81$P#b6NSU-/Bt&YI*$(d`^0iBgU+).*6Wh@IfWEnwKW@V+K&E+;~7:`fj0D8gonrIeHt.
x:$lX$bZP{&xDKtU&BF?bn;2&D!]i34dFu>=k(f_DPanTG=7
rh`4CITUBwU8eap;B9KKgJr(
B8l:9J7Oj>Da9I-n[]YW9ib;HD%_8vGnEj(9,>"bGVa!O}K[gbetAsZSw:r(6JlW0j.wux04@`rl*Z)dc/kN/$Zz/F-X&fb}.
V_l%$&BMBNxpr[5VfLLV;dtal>WYyq`up=(IaI,6YILd(8A>:j8sDQWJWvGEfDsx$.q+]P?:bChX%9v@%k(nM{>aCi`^^
X3`yb`"b';case"cs":return'(]^@)bSZKF)4otc"
3UX7u[2]Z@2e*UFqc:[mDZaa*O/{OIwLu>KXq+!#X4NFK@#x$EShQNvqWO6i4%UldPU6
{KzEf5YuOx~6yX
7%mH6,vhYm.+a8qNJ"uPQ)@2B(M{Nh&D?~B@3>5p>cy^_-X&5piH&(X1QcK7PRU7pLO[f]b)RtQdIY=@yfVM_62a>oW6lMggoN*~K1=Bk!Yo0a0fdzKEdkm:v.k$iHJJsM;/,hn#k%riceS/=AG|?r:ec7p#2L:gOv5,`p.a7^68ex"Qs(a!w_$3s+?L$1lIZzvk4Pe>Av:;7}!]vu%<dAl*;yc_WdfRqmYM*Hdz9yJi?e/Ahyxxa;Z#H39;2A]_DS)l.dc+s.f8xK^1o?q*(Pr!g4ZtbPb2%iqb0wmFS3Hfxgkr4#/2_k"&hjb$j5#FQHVvudPbPZvB@BwfHZTBc/q+)qV";]J+aFRmh>QZsAQ1DsYXGs<d-qa3w(M,C]w&as&gNetJ&$r#@38XhjkVO(%bsd$zFvPi"{V&[T4ELTH_fdE%rcja6+WNYgE86(K~)|ko.Jg#,r]#].:?b!(<`P9`,XH:_C&Y"Y4!WR"-*PGZ_}-Jv+?uYFhOlYz$[i#~BlWFV_(7HE?k6X*U&z:#l-Xfr=>W/RL@K68aTUuq#&22^N@SQ@s
$N-<+iUD6s]xd&Yc=$*+";GsmdO@t*XB8Vo060tDp6rKh9_.wa[CQ6gqjr@g0&_>(+=_
_gn$:`c48QMqlKjVSR6wo*GUqaLk$V%MzA%.tQ6Win."1x~7CZlSD3RSolV7a*{[[*Lj
26sK2tyRV!qu`Fj;P%=g&t6Y$VQ1Hy,O^BJLe/W3]
KXO4%,]1ST(JEsq81#r}nM_V%|3]G:6/:3jE;!-[Q_Naw0?B?2(~jVvT>OiVqp1(tk$[2w$[J=Z!!P3Z0J64=.lK]@%6]mxuy/dv5+YW=R0~RmcgmyERn^VYa38Qj
Y%nsfL[qcc=}N~0B46KG^.8n%50@+W
mOVWtZ74W7CBhFMeVqB*=;f)>FW`[7/f-8Z`MP0]SRkgs1$QP#tKOutFuz!j]*qz#`osu.s1FZ{,0mEB&[PEtjt.*=Q#RW*f,XYr!67c$?^D:%!ZBj&=<rlAZDIu6qj?M+
%wD?PM[48Xm<#e,"f{19;a":N|P?3;vS4Ytl0j1uCUKa6Tv~m~a-"5F[eMgLil:8@5Z:)chv!gc$.-a0bY:ihCLI:L.<dlMV%1v"u`>P!;><b5Z67[Itv/(^,zpLq_i~,h1C_"kQh1,U-SeY)ll/I-Q7X%oLl&G:&dHe0m2[3XV8D~55Vb+f4YUh$i=6_N>ParGOU_*X;^q5[$FZVFtagWA0riA<&WxXyW]3^`D?+PqyK=9KMr3.UQ,:N#<$=;szLp@u_)n})_v&Q=*d/f[620?uYEA4&[/e%=K77..SPAUQ^?O?D5f=;lNUn,+[f,CCEV.XP8/rpQ!"T/&tDzRX8E>>S<=0(3-g9
TbO2o7Z|pTCb+,C=t9/*
.]D^Lb#2EW3
xZ/o+6L0wZPVQL:37p^Q[wFc?lc3i>_hTfc
x]h/}PF[[8fTz6TS{pgi&[TU2"u.:#P8[Tq+NHATcCYUYSZLu+]qy:5^I)2
h2_DT_FiLb|s5gH9k+&Qd6C#{7Giw*QeXW-aNm}]{V@`}Iji!UTHb<0WiBh<Kig
Sl"7NQ(VR&IV+=qSpm1V6d:Tu2IA[j4Se4FV#^"i?=M<ZR+J2y?wLOdB(%{OfOiw=E,mq:P7{F~yM^7N[/p`}RLN0Pm$_V?wm+S!UGoRZdw`?^v@`(:o.d3[fl3am,5/jJS.Dv!8)[evA"jc{CZDvT[8Urdn,/rTS#xs;D([1_r0W=5@<"=IGt"WZpE[@Dq0(Ls6rD6)wo6^ea]3}U;+IC
8g/wuU_lo3B-B5RX
uX]*fOA#tGWP]D(raiWrZ04(TRu"dhaW@&}.nZaDKy@TcP5T^"C5MmFuYbV>{E<h1JL2a(b:Nr@w]kT2`5Ul_Vc9999[Kj9wm0|QvO8(_nB$c(`8p72LmtW3Qmo+KUWlpxERxi&xz;,d*!<MNvAEu9{6(^w<HbEG9
91u-Rx*^kD.b0b"`-/_S%_y^{%okB,)&B*"I1
18!<dQefET@SwM%Mz0Hv^?L]@6>mvMM!6:0hu)`
!aH9XS3c>).C{pV:zTCRfn-VNPddqszu:D}a,j]11["Zts,
3u<y?ry*<[P4"!o(SeRG-11p`(,F#bS&=sn^dDwpxO$wrN4OmFzIODU128pb^9G/P87&F$_BZ]gti0}65M<8LF1sy5Z[FP5!c4x7)(0=lg4&#Bf7VG_:VG.&?fxM+^W6""SF_DR
KWH76tWM@r3H+M@rcHCJPM/3IaEh?NmF:*"QG`%S0yYLr6(:XanT`3+Em?v?F5?O"!
_t/b1{,reYonNw!:HI81(sv3s.E16i:rsq/DcDJ.O,Y]o;oaVNW*K|=5_dsNae,W2#RrY-*NxKH~2|t[=IUWqHpi&]*BU>]d$!D9n"O0?5o4Lh9(kR2aXy74<6<Ak6]DSy=6V_BfemLn8r%%_DewfJ+Pk_wrnrDlPPt8.TEJ
GY?N*
OGXLw)=y><RrKi.%0h3be?T(ed
V4I<nF5s)bxGPx.qs+ji,}VGd"nb0pXEYKqmPm=1R3QNJo."uHP8KtK:8~F
=fp/H,>R2v:s/zN2ox;_,udZJ}l+XKD`J9*FUQN2T@g$0PrT$,;KE@AllYQ-P=lf8^weqd;k;g^;FmOpFs2Vp7)11KtkS=3F0x1uL1PWyIxS
7H9"oDuQxC=3`kTaCV8ne(ei/dyo|?vg7%r,Fem<xGi.;emsvUW<Ay4@iZ6O";P8SQFba<XuHF,(X>:aY"VPj)nX?DyyR`|;pMDL4BA/}$MCI:]p?drNUy7dX-!Koad8!2Y+0Y$.$$Q7KFkOL4`tyLv%}f)Vg+i_f;n(hLDHH#KCxKBw-
}J@g5uk1
D~t<+nsJTRr]aNWj9/_qip->ENr<_i=ih"SC(RJ}ux5gLXZnD#Yd.]N&x4+~:/%q?S,}ZJM+&~n![X9uBB=ruc:]^:uSl8kIQ]U3e]TX/JHf^$JZ^@<7%Mt;0:!Eh8`v;d%%7ha*HR&n';case"da":return')Z};BbP.!$s^OQ
dpJmd.;7bF*RQ4[{d1G9^B1%=mR~o,1C6^!#+N!n8cG)_0
m@*/5p|,$QwG;h`o(BikAw7d&?#;r!0Dyn]P#jcD</}V51F/wp`uiC2[[bXi^ksd.Ot./@"h.XnYsS)Ht?rP-[0o~$U.?&lBJe!:ZD,fpe"9Ki,`>[.*G;{=xP[[9I!awh[Y:v`kNW|1^o1sMwXE.$:@]kd+TuoG@4A)YXTK6:7fy$Er"8f$"kEriDGTK_7lKw#oC"+,):dCl;)$u#IJ.NUCM1G?}yAW?kMpKhi2
9}yB7@uM,CR4qObA;tVOVy)D@oUzcw(*lwB`&<_27%RN=}lD(LWrZ|5o@{=fb=ClB@z&(W_Otj,z`+Rt_&<e5{U,J~9oS?((PWgu-^*0?CGv9"`8"MZV/{u7j+=Kz&cEFUij-Z_[L=Z06f*zdoRZ:B9vW@IU(?D>_lY$e{dA/0cYuMCz2pe|D/"8UF9/yaNhL$Y9>P[<&BT<>}n@W><WwN4=G_O{W2hrnOaT7|
b"2ANS]9R6O7*x|Bz7t&~;89}]*>g4NQ9[Wfh*?f{+"kb&QV8w]bM?wdlm*<wF|&v[`XzvVBWlkjGSBa}Re<OCYE|sAx7M@TR_CR19jDNkTI4M6.]J,E4SqI=<-1:wK37[4k3MzpRV6!|db#N0"aQS3,7>r=;>z`5%,>d6-1snwD(eq_8(-gx!cV]-H4FuwO<1SD"veOu/cm@JwyVGhgZs:6|8d3oAL&]&WZBXu?J$6TJhVy,YL7=cW03tw=8a!#`eU%.1_Se!QAMfkd+y/&OKFg4h@$>iwMNg;AOdn>=%#EGd!KwK@#!3kD+x3jh..S*+bnu[;wXK/F~S2)kKER1so^);V(@(;$55}<T(-n_93&7gWFM?/>4
j%-O3]K"Ft375xbr5?-U_xu.ZEbc[hmv|;5mlQ7Yy,}iNPvfJ@bqv7d]2wVAEQIN&aM4@(FErMw5X4_U}:sceAN)GFcFhP-0=mKgfWD^$e;.Q)1jd6K/k_KlKM1`z&J,Bq,R3U%u/x_C13biF+!tq`fI(a^LwcrvfAs6KB[[y2Seg1,Xb7t=g7e_U^Tm97,k&oZ@8i>#
g:
[^lwsIz:zAuWLJQo
<$=YxAjkM_&w7k^d(?Y4bkT2MK":xOdD,H`0LRMFQ(0c2@E~#^1g(XoMsrJAb.Uo??!,6n"JaNO;RIM-8W&`dVE//Pwd(O8DE`-%g1fm4__@H?^:23F`YY)Yc"7b>s.ztcj1FK&K.h9J`F644OY7q[Jz1ep{8e>4j%@Gr[.NR2s5kIZB1XxB%c#E`OKtPN6_2bW8=qua:F5$;%XLV3)KU8Dgg$`N`9LvMpeuSo,FO=GzAuQLOeAgKkgOVw$^l3C)Q]h?5{^vSm;[@6AYjWRy%AZ`kXOv^nBZUwvbh+dne5Y/3M?P?REkwOW<7M/W<dG8%GAS>-aGZdBxeA>fo&5W_>(0H>CWMpFbH9jGIe"<E(C|5a.e(toc?]6ju3k+G}H#
.fQ6
G>TNK"a.T"H~tLC,@aqjmP:t`_u0Fqu43$0R[Qc_QY[j>)U<M%v!#VXPqhEG.mJ^t9`7!(iZRt[{FJqTE2DR@+jKxEfX$X@>-Y0{pwcfoh=0aAjsvL?VPT[I1n[}_l((P!#jl~c.PGoj:iP=&fh_,Z>cgEN!rgX&1&dIhe.@hY0&(koLRoR-!djx`g$Xa%`3l.MQx/$n9:B@-QT+q|]9KE][o-p-kc4U#"jODAWJ[CMac;g9[%lgR2h-WX)Qx
Md2eRUB7!h7e)nKj7u;0+9*W#^xqUp[!Lk>"k_O$c>I}Wu2r1*N^K2fl,Yu,WB&jw-kycHg#v!/(DWW^/@Jl,aIf6MkpLb?HW;10
FTOv7#!2Wu^ViX>rjq4DFc<K<eua2MH?VMd-{HGd+eR!>b|^*-^[rj{8N[R`*tQ[jmnM&Ob3MlX+Q^M4@TO-gv$Fs0XDE_Q9p#`lXZ%$M-;fDNxg5bOfZK$c[O-Sn?DW0LY
DoN>@LXlEWQW>><Ugymb)3+wxEW74q*wTAjL_0q[cjxoZ:q%VK%rW*BTP*Gn>I|.HAr;;E&FwK^EEi9v`h}*Mq<vxYh&ml8dto6A3
yllEf[JX5^I>4AXsZVZO
]J]:,,`IEOGIQ<ul_
ms/qXK<u]ZIa;9H|*@xF.Qa<_I!f8a(_6B#xm.E>*z]LHqN`gq%7KZ)taS?O;RuL%J>@w1VuR5[kr}=?q??9@sFo1(ys"nN9MK1!jD
#k+./N<7Q_(iafX?Q%C=z<B)*RI_-mhAhc%?l^k9Z?indd0N|_"V^k$
B%SBjm(SQ9`sI9#!"7iWi_E9z^1ZS:^BvDQb?n
h);~c6iCxRW-Hg[E;S0dt?YcSGm"BR5DxB;yc;Jhi1jqU~weZPn0p:99,"o4B/q1m;ND2Mg5lr[ESkX8*-q)*eEidnQ>>LFw6dli]mmQ2GbhG@/|T-a?VZN7,7(xybj{j[9t#29[p6yBQa9?+l+);rQ5RzKzdR3
[!n_bb';case"de":return'+]^@ibSpM?SHCo9%qN)5;gY7E"&(p=:*(F:xUeLtA+8.w-d=
:$>+Yuq`P,Gp94tJRyuTiN?jL3j@<-U-@us#n}I4ery%dDV`@g4m"84kB*!UTSn4JgadR/,=7e!5H4pkg:SHCUm<s`Zl^T(SaZceE%[g9QyU,vf#";)pu
Js+qj,Z8hiAYJ_RgkcJ+qB@jfDRew^MIv`I`>IZ|[?$|Huqv6SySQIIMel7dJvm0p304-x*B!1HxM_4d]$hqx!:l
5UCf6p1%.?1bt?JjW1e:3aoAZK?T!<
E0aLd4,cSjG"iMSZn<aL72V|D5,x8OVcnw(TPxQF&:)n&/b1qHo+Q1j/^c0|c#:`>`ngv>J7:zS;WrQ4cn^D@>Dg5_tfJ}4rM~st,!!(-Z`o9]*F.g*5mCKIsj.z`KZeuRY^rr6LpK,&,9ir9ohkkglKY[%2[/s@[r@;o7A*3V5,`8!m&UsoE5$<L1m4hdpV+&7/,~aNiV__?)qV;~lW<JhHkS)=<GD"E2,!mpF?)Yx$lulr4qW4Zwy-;Wni;8LD+JZ:B:9|aE2oA~o`Q)!<lY794JK(;$+DflgE"Uf90+?ge!NdAH%%#r7sl(<EL/v-_Aq+=m(XZ)>vA|xq,]LEnb$A3-K
r4p;<*T=+52&u(%XTsbY
$5r
?ZMU(YT-O<;]fO|4WDvFcjYE`svbna+waYE53aE-MuFUscR]w`8&^F=f9oiH{5"d5f$(K=D[Qyyt4Fd^._J>"<VDbdykodvM|.GPuv4E:$5Dv(~?xk?KFT4>X1I%yi2bRi8M*LQ8?7K^7#oRmCnh!%qp^?IdvQUHOC
[>m;OJa"kK:Dd|afY[gR)VlN`d!_u?
wFukTy}xF;94P5k3V-26&Z<<),n4%F)ik54h@#QF!e[6Egit/l>(4OA9O+h:h.~2%_F8e7d/c]6NB--PZKGm%MsrvYyk]!y)P/PV6B!c1&wdJj6!:iAPNqJ1i(rQ9q+L010Z|SZN(ATTSt*KVcg5sp&DQa!i`"QBYykwX`NF^28MSR6+_
w-=z(FenqB.?7z)KrM=0

u]DkXE?:kRHdD(+TJUzT,P!ORoaE6+w-@`K"iDrE{Na]0e^[XC>?tQ8(j+/`UMM!t1K4D?}I8&|v[OyqhpInDXW@s2%,$CGf{mQRIZs^>Qe3q7n0ntuq,F8UtR%)oE)FC;<(qdV_tFOn2kOm_/KP6h6D$@(PJE+:^eN194<7~,Dr$0ET?*g6^7:J3xl3i,gkj$Hc1w=K&2^A5Xyd4
<Gh#^g0/i(tB~sJ`7JK,AGrX[Hp2<x/qYE
c;#81et3w6sO.K8VS1r8pS)ltRL"Pb^4_|HLn$j11>M?v<ps11Zdh|r6OTC.-:J*J.vFw&4P"6MN?z9XF2K^a
^YxY=>F!aYJ|h7"c8,;U#,Wj=Ca4C{t%W_apm;Q<V=MGh<N@Y]E$xb9cpHSPHD;FeUU7@}B)5|_RAp`|7-[R7<YXH6p@*)+c#KyP/~S91;WW56gsb^$8cC#Nx"SA]0B$&9M7$I8e&!AMs&xn_^n?m2[in^FB/6/tlO7tZH$0w7P$5,Ry5q[iY~A#,q?k3,3QR<BG1{t`X?mY?vOQT+)Ld*
Hoa6
p#p/i!P-AafaQ+E8fyY5Mbq+dxZvHafj/0o
dO(2]&ex5fF3qqJKu0g-eC;,#^qL3!RD.^IE@!AIDbO-H[^}9T7YthC@[%jKJ"VjygdTc>4jw!m&mjpyk$b2a=W"ERg$QIei5Kguj<[sMv4!Vd2B+-2Q[aA
kZitA"UyXL9R*
<Wz%l`k)iQaBQ.YEnu`5=6.:ca(hH?P"WFCUue_~nw+|NK>goru,+nA%N#B/_^@tib4/oib6xyj:OX*FTrY/PxO^H4%0N9V=rpTuK:f.)k#M3kjt/q:tE;?Y7,Cem{UgAFp(j1IU7!Ncm{1YSt#Lv"!M1MTJ8$a9cv9D?(%Rq=6:-LAg!{X?d-xD,UtSx|)`NA2.4|3m*jx]3W,LJ2#ToSFTluu3Sop]xgoQqs!q#@gGJhbeXtz!S:$+cbnyo~?l_~=Hxw013M[qeg0ZH7k{+`deMPd;M<yv4=D"?AoB=y_OPLg+M=k*nRbN8[DMD$cxG0,04kcIg!,56i<ln&kmi/Md@j]6^6e)L~DP_8/>!>-DO*sNaI&lwMPF[s`_,v)XP8W^)IE9-P"z^w_"bl-MRNlB1&W+^
?+p@skz$?JE_Hi/*Hih*q6cxLgq&^MDRm.:HTY5!Ho:S"}DyS,(9H2_AZUqR@Za;dGuqMgnJAj9OM~i7571E23(VK2BUhDo<Eyn
.)(:fmqcU
T}DU1/9H0[/v_;_Y?#7I`TZ|(
G:i:6z6y;Yi_LTnLBs99RQ6<W_"CfCKsdr.UVlgACOJLkqVdtp`@I+[V#52Af*Qt,[G(e_aMX8-Fe{knMALC#jc5O79@(@XrYyrk%)
ddFACVLe]Z}vs+FN?i}Pp?NNj0-YXe-P3PPXi=f"M$QE`1N!7#XV+Ar@,k&ch.7)A0xPruD7iQ+/V^=d6/QO`&kb)2ZEousl//U[-qbKqrkH2Doeh]Mbs+B/3O%L_e9?
<P81,ZCkTspI4+/RWZZs`S&bV:Dn^g#WkUs/C,1kI+2$-PnYEG5Rke3],bgPAYfMl#5`njTkZJ^~pvZF`#1@FtfyZ1-*Ez:w5h>+NNA=`f9(b<H;#uq`4@SwjF30k9[4=bpQA;QOWF1<MV;>5<ySc%n>Spy?H-:[PP&POvm3FY;i%Z
7"l8r,I<kUk_DqdGN-e$i!Qr&OCRp^;57*qV+J=BNTboB,yQ@HysefM]H+uAn-ZM*,cly87rHVwRII6rJCK-18PShb[yIwVp)E>5"th113/ig/ki%9+l~Zk,br#
LWXgmozKYwUU#(#CA
@%iv>B8p+C`Me
@/fUU
OdbCh=M0AmDAqC}]PE|dz%1-91r!)Y@Vf-Z,a#O&lyZtX';case"et":return'!h_;:g~Wb$#!:wB3J>@_i&`<(>B7w:4cqF(gQ:2SZ0PTy,$X=N[xfJUnSR+^T.HYOaeAmDlgc^zT`"mY^BYtS0RU<bH
zN<2nmSs]&]#|[
VtgO"9@BY<8@=FjHY?kV##M#cUn@6rC1H9=gPE#y32R?r5s9oB>M`gn/K6h54sTI%-NQHF;PK6aS8PpwfY4i:E7W
M1XwECBq097h0@/`FOc9(lx@f*q%lm:;EAA`0<i-_b$IqHKs{ag=bp=Y|SEm@-y#XsqH|rgtrHAYaL+Le21C:y}d:vP;ta,RIrytYo[o?+lFKwoT2R9_o:QY/3{g}RwY;,NUES3tI?JLuO$M2FW<#ri;^TwMc3G@"ri,#3:=LO6sFM;mK?SS>f5@d3zxr
g;Mty9q`)?8+%wM2!4J@G&DK5o);?99WJp&M67uh:LRk>Pi_3oZ&bPM_s
!.sRad|we@@Cg])i3bRQ&T#N{oH0aI@K>F4jjIUXI(2=%5m>%Dx_vFEaM%bfI!vqfml@w
V
k/BTE0c%:)<"m+;Gfluq3)btFWFE4w5e~9GHF2q,~$JQo^Cj?:44oM?@.dC]CO17MFG[et$WbDE9[9#0!L{b?kXsYQzLPhJgxt+]O*+O&PUH|5Owax9NS#CjbU,nSt39d3gpSe/a22HqN<g]lG}4]D5xfw@0hp0E~`w>Cw=WZM5#1eBn%6TseK8g-<#[r-=?E8y-G:"wU2|1UP}?F#{0WE;7DlivwKd2/r>qb=VBAq$8_FBf*Qw)}86wft[EfaZDN<^KryZF>,.SXc3:W,V
pYR22g(04htars$TM_5n})@8C]>h"O!1n[RrBZaF:Uq@-:mL=
sIy7EXC^|)t76kV(sGnNPw5^|TZ/|.#fl@ruo`F]4oFJ#"5gE/tqundkFFN/^YrQ)9>r/C&j|,TR|]dy|26AP=,B[7*:UmS^,O1%[+DLL(3i[>m(/ikFOyIN6km[
l?JI+6cPfo)9t8$JC*_Q[FJ-B3[`6sg/3=reJE)pJ)Zui;!VG,r:@N5%qPg}*W*b=xGy4dwdN+YuXBdTe-5f)fcN8h*?G".}gjs1T,`7j1.r2Cni]+q{T(@g:~Cz/x@|ir1q6`^Hw2M5IT%<2w/pv~6j?a0tQH5BSA
JniVH!>=$dpqs36scx,yfj#rDtK(aS2*6O+Z$O$R]TBjJIj1_%P]@_^c;i]8G$YkDVqFN.R0gqgOEQ*[e.|aj<Jc6rQi;ydJ87P_^uI=KWhm7VX-9*!%(e)*dm<,ZwO/I?XJ%N?g-E7QGtZ%<8*yzC02G
a6sWNP>3)ewVuwwiNHl?Z.v*VNg#qhj3T;vwk@%N$h5
u+r*u:f;Tc/K~v"DUY.a#C)CG7{WQU4Ane:/<xOdnOR^k,5AH+/Q[^5_T$c.cTxe{8085RuXxCb1$0vpBs/*AGUCY[mSo@S`9Uf3t(Gb`JcAJqNw"D2Lg15gP,9DE%D#EJIoiEu^cH%wcYdDc0o(}6KffAc<
,;%8P|8yrN=uXzJ(Gr8*@7nhQBSfdo?jvUE/51B"d>AXrcyQ&f8"xtc`7fE;X3X2AZYgxHY&&r!XE=^s*Vx0/a9>m
-
f5OfS5m>L`&H&*"n7<j@EOscJm)cD6UMu)[5O2pd^y]!&^kxom2-N)>=Ung#*F;qE`5&<UyoW?).OhntO^QB/BZ?P"rlIUP0:>@6:pny/|?fA`laN8x{7O:en^iQeDdyB;L
c."B23IyuGaGqTgOu,X25AG:DEp8I?yXFULm<%b*q@k6ASwE3+E/w1f!c99Vk1^infTr`A3/9X[RmR,^r5a5F@a7279cRT3>&LYtcia1COB[9DEdLhRun5XCG&aEMBL/xQC@kGagi]C0DlKXd1qm&c[^@2lw`6h3Arq.&_BsEFvbx}DF[o+jlr4jN_q#X"BSAeCm0TDGfmA:8v/4tJL[txNIa
9hW3d_e=JM:`Ve]dM2CPjC1.`%4Q=aeW]8LjNYm<"`_#&VjO4pKa:([3!TpzJ#t@)G%R#50.?as~-cGi=7!H*cXy6IUqJNNM1-%ON8)>VR1*eP+vh,==l/JK1!gv80;f&@B(FdT;s#,4%W1#Lk`$xohBa/:Z05B&`vL.)mbdq(tD3g7{T^?]*lI=$2w#$_*Rv_6WK{EO
#nT;3IBk.^yy95`<^oN`ILIYIrAv<OvPLM@,h%!eM:.7Gr71y!Ji[f,8x^bp{E&7lX+b4OBj!S{KCa:HM``EK]n0OThd)*!M=JT:`CUeiY1Jn_z^j$1CwYnSRyBan2/o$%k';case"es":return'&`G@j6LD)?T)ntd1l(VVNy7:.FhQ?!QUa.+`f]F/"Z__$"W@uk|#Dt`l}9K)gi2EmVEuTiNBW>OWk"iEC%zIaBdc2i&I`s
mcIy!<Af:Lmm(ClLw&s}EKp,%7Rr=eBTO]o0k5Xz`Y<ntSN]x[3
L<4)ptoLPDd{Zt!$6VO{PNnp-~Q#T#
D2_Gp9bDafzc9k|%RK&2!`df"US(/G~S@d?b8L^xiSxG<nj;[Ib9RowG_z%v1<iO3v,)
HGJi
iZrp>B-$b7kwje%bRk`$p/cw-`^kF`<l.VetoHlWz2v@vT4$hBtCvPn"+6,Hu=LiitPn>DnQb_d_xC;
%*JH:xmueywR5jGF}xOH:A&-^eN53cP;Zj>9f%0]BNJvp@HDCX4,<F&SHX<6=faZ+dl$g/+3I0G(o>%SK!]gMml/{`|=hrVFW[x/ZyLJ+_[pf^S#;ThV|BQh{5$qA9
WZ
{cZaZQ$&ZU3
#023"OBJ5K4NvJe8G,ooEZGgt,DX5@f<9XzpQSjaCFLMD%q#LFpsJWO`C:rK(?Bs?n9IOgv#@a5EzTsYiZQt7$f0x70(>sKWelh^4)n:BWAZ[/mrU*+4<QDndsh%?qV@ZP$P}B5XfS2!u({-OB]n]o-jXJSpfx=r/-M@#Q$rB5]E|ZCnS6tSJ%rf./Tv].8]bRu+>.0$VFgb0ciCB9cM[<)rI^=cZ==iv@X[kOe@qJ~xZx2v$,_2>4!_9/^lg/F(gK3<QH,-/e%C<Mo*7ic^-GXiJ8nX:5.>wD$$t/L4)B@9PLNwPt]?/duVuBQ/n=E4~g%cKv*9Jvk;nTqc[3d/FZX(Y:Lm0eDY_<YyZV7IU+P_2tMjq;.669^e[:,YoCaseeI.LRugutuFvO|USDlK]g}U}U_<w6mX)5cl[2Pta-VwGJ>wCn!4{SgJC?BSs.!:je#4Kr~2L/X&&i1DyMO^eR{Z1sUQ`gZ,C99b_vw!jA^x^RpErOyWoohl{W/$88W`NopZtf4;aS{R*?.-(bnI0,vCL"3ydcTdICAwS#?TbTRDlu{,3l_gom_s
?X//-CW;OX3O_3l9yj6|gvAJWiT}71K*_al0wT&V.s[.MAm3G?;RDl>}_xf&iD0rfVQf>JsH]Ts|Ul.dMsKX=v^x]nr}HzDXe]605"cG`VJ{ng^4.SH!jqn*pIVNwaeT1xw3N982<~oI-R36e,+%q#h]`vnBm&ORnr;FeR1)wV8PM3N0gcoJSo]-8E!F.)+mLYal8:5daJ,<*N97PW
mE31@7%IyhoI[8o7L9j>#5t2tn+!gkzyKkZWzk]i^m!7[veq_F^^Bpu-::]tv7X$pR`v?3c0}/Y;A.1uCnU1-C<%NEc`)`yv;yE!^0Pk;sUk)HmSgl|Lyg>a{%Fe82_SVP1_ukqT8RRmH:m%H?UHc:ks1+kgt
T:H*9732m=av
:hL1sGmjy[5>pd%j1@"g,d7LG6Ve(T]EM.G3="GX"kE|.xm:N3yb#}Vg1jbpP>5JkR]BO{Ft8z&.rT*ZZkM)`QW7I&mYMQ]N_jD!y#B@<W[Bq+c}vD9Bm,s1kp0cQ<KZI8?f]I%
3ex8nw5|pfH~sPL6yM"5oQF=^MSMOh(>e2i6>x#zS0Y4A5")h]<=0@=u$K#^FJ.}q!
[$Ig;w]RlA&pj^H4*muJ^RPAui*`q,y:F5n5@_[@JC8@,8UfQkamo@dr0]:-7eo0HUq<#4=m`jq.NNp!1*DQ!+i.-qA-MZPA)`:]Mc{JYWtqN.-D+]0&suT/{bbI*k3_yY+HiIAq$
J<VxEXT^_H~t6T=nb/17b<eI.F8`/`_jk^iHqKb)iWFu[JW$6K[g4L!UPdjIP
S,K?W#+Jr>0Z<(xU]%<5Kibm5#)_D#{wiWkxzt)hHM,mx
Jp~_S>6km5}8``,:isBSj-
VW.$[&MELa-}>C;,IDg"/z)/bf_$Ah:l3wZjpL
uDc8]56p/D?5*t"df(,+&nq8ZH=2Pi0jzq9&cQv?e66gLDCuJ$aowm~.4s1tu9Bb/uKcPvzY[&kVd-NUJejyPpuG~o3:iorr5X+^2-5s~*W*5*:IQ&*_uJ>:^L2u:q@?KY6QoTjAnQw<[o?ifsS4jZzEfqA?]c`6ik/c_n,-cuZXSUFV]?R<aOBg|P%vu+ec;6}VGQ)2B(,efYyT+pfUFiBt(iIy]>yONfJPG#p?FW^343<<nu?kJ
%kr@0"jjX@73U;./sD{3ZRc]YlWvMkvc~MeuCWv-p!tg@n&@yEBywV3.{-*,@Qx){VfyR+9P.&x]^-@x#hU9k
TeF1qAAmPh27B_9oA&G,bJ]uU72Z#smV-b7Y{yWG#JS`~sC]9Qb][Zd>f0e25<2kSs%Fp,/OcZkqc=7xO7>CTG9=@oAV_gFi/DhHd!$^CUa+n%["3mMn
h`AKnpp7D&6yYW[,#]Zrr}=PO6Ug,no7gK22R8"YtXPSTIG]a1F"JPm|B3]famH+0Xqd6pkz?:(;fc
:a)wES2hlSj]^auM%L>9z1HgPJxAqMWXGoHY|cjP`IGhJYrxN:+3{1!R%;hZs(Yxw=BU6rab}>fgagDW+Cz))GC@>`Djk:SNx6EHoj"CTeJ>@nj4W4vudNkH#p?XV_n2
AQjb"D3#n=Jf+$kE-Zu>yRL0uEE"qUPdJJ?{u|=Ht4$m3U@LN>>+IHawRF(2p0oe@+SS8G2(T4`wyPU7"s@FV,#vN20LW6r58B1FqOvL]0)&02d6`[F/Q9LAEe:.PoDO=F)3>+wxY!mF]_peqY
p]^Ni]S$u>gbsn!1`TSh[)87zSuy=uMS:T*]VLh`uTBb+2$:Y];eF%95*#(YUC1ZR3RUT"Frr/hJ34`o8
n*L#giq!wijoHoLu4:`2xx&awOaJy^wjU;HH<-XDP,we:h>3tN6';case"fr":return'*Zu;C6kYx#@Bau$"G3>&./h"!ZZ86-{/BDo0K_tJ~<wD<w^`dX[*DcuAkqqK6yR,<@6bW<IS7K55c
unay11&w6F``[.8+Gtit,GCp]<be9J~%6fH
WE5E`:RcJ]4MZ
S>&f[W7EP_L0*O%TKb<<XL>p}MtE|NX_*rI$qo=Wy3Q
lQ4C;x`Gz6Hr,c}L:G%j7
/Ps=7d<O^vR#xf$fo&0+M3rndw6ny`t$,QONJpZ(ps
emyU2.JRM5G&J},LGij(PTJy3F$en(3;M,T$9CI?5PK"HgJ{XMqmStlKS:aM!u6|_SFBIn*j&Wr:.b#flP)G94&HbAeIAUMe`[q!ecqbw:p7_o$|(hE***^gH^]jn(6&g+>S?TShO1XTqI<h1>iKXYxC-m,_?Hmyemd_xM1b,*nNM~2s:{yn]!YZGO
7q4tZOc>br$
qh5tNyQ:$<kK3)T60056[6_5F8R<+[K/+o
1m5GcA+_hcG`@a#[WswpV9OTKkxn<Pltrr%M98hBn~!H9|gng{pD]NR0bV)U78A-)BKiRdTauMidod#_FT@`s-JWJ`P&mX9I]nhdxE=>L$fNS&bbz)aVr3u@o}=SfVc]C)U]N?o3tNA
e5.Mk0O!sJnYj0@CZ*2=5o-;oH159%z"Y8:iccV.8c[F^?9ixSaVZ0WIe5Q:q(GT.rU]j(Of2BUpA/sH1wX(xm[RQqN*13
wyx2MT_=2YCct,U&>nG"<3fhs4RvLe
x-gW*raMFej"]~XO`[l}c,/C.zq,htZ]bNFX=0tHsof2xc)R$uFS?dChgn2"aqcTRHl3%4qlZ.c#0&mvT$_Y;Y!J-<H|N^X,d9vdAq*iI^Od-!J:6,IADQ`|Wrp*F@M8__="_7)7YfNux:GqT!DgTpntiT:s;Ndc?LOu6[mF`xHK9Po5V.x9d.;&ibjX.b9Wf}@@*1@kQ!b&?%N4=^<4FrPu4MoVK
Tv+L#3La7^ciA
O#93wG*b8:^t=$yU8A0uOpTb)zMQ1ANMByl9(X%-?+n]@45~=A7|
_Fa&sZJmS0>:?d8/tsq]bcM:Qvw8SN.!|HB?V99c&ba>Rh.hULbgfnn:3;!PSvqo{#OiyBu1u>vj+`##Ou)4h1&*OyQV?Bh9cwU^!eHfL;rYDuQuY=jGKUYx=78?1@->nb3AFZ!wp"(7,D`SxKRJ]XXq&/wus$8i5Z^_#
^r.%{bLS{m1X@lC>&J6Pi;F<2P7`,D!R]oW?;&=vf<D<xn?thZuK3hh]I:0
K:}U"Z?dw,J<9?><v^I$B;hoJaQ-/FvRgM]YJ`9Oac<U`sLK&on`T3cHiHR1ts/52Q|mp<tWK-FK5(O")FaxP.0QXMg4za<A`q`XaWh;TGdK`UCDX2lS&)fAT3ME(T*0m8`f2y8L|^2"v-*t^g$-Mhv&,/]ILUZQur&;)XrwznD:.HVu!%;@l^Jh@p6qHgubCU^1#bn8DI:0@]lJ`R)6i*]<kMDiuZ(k9oguvKp7#^<H
;YbY:14rNWU9]#nUg>)(xD"k!?V-"HaaH*Ui0n1M8zAQ(g=A">Z,cP*W9]V6"X>Cw2,o2"mLMAcQsk>3DidKZ8rn2D/&fLVos*Mdi|>J#7u;)x@[N;qH_!P,Wig*&[!BZSwK0z2FgOP:kTX{""uBh%Qg
UXxT)tG`KV92%gv/.4k+?"k0+-}]*h*a]oJW:-z,k:f]1W_.g
N.{&dEcGiy>,yUVl4@Iw`?71M0~K4%~EDGz,U#@(@7KqMe7CUC3&R%c)v3H1/$YO"S"kCMUlRil@v=XN[t|Yegw+vIX?SerW<R}BDQ?6r7
.8/K+4^o$G6?R@1#eR_BQ?a*N{hz&ypTN,/g>{r2B?CT7hrT8qWY@X=>%*pjMS_Z9!,,QaSkT{<T+(AhdmUT:yvru=m=*:.&,"HY$r0}TN`LH|"_KYxc]0V9@S03VQQPj@qQ&G]wZ#7G)

(sF=N=pRrEL05e9qIc)OAqw
$/}-CTyWHp;#,vgSea@2fW<QBx^
OaH6#W,tO.mw`"<*=IZpjA_!7hGR~!gQi`VF1Zp"rp
Eo_GNAZ%5"w}/OtyDYU`U3s>pdLZ;1h>y:B?-a[OK$1g$I3=X2yfm"sAAkU9,OIN85j!-04(sc*^0`5Ed?F:Og$W0xHHTugZS"*NZJjUgtF1]eKO<`:l_?B+BqG]=-(1ZpaS:,+Tw(IW+l2"`#X(Gp[?j~+y+}oQ"az(%klpc=F@K+)~GA
J-?ZH0OV%<)V{R/M"s<BE54JSlA$NXbuigb8dqWt.+U?m[..{$CFUa[.i-bAc"jeae">D;N]vD*c-FcVSA9jAf2y+){63SB=]$)2>1Ll=88Iwgkj^RZ@a9=6#Zsf}p%GPD[*n8;%L#0>/0JP3*m@,&R6Ni$)pX46+aa+7%#iGOu(7=KatPPJ2-d4?y>AR%`+5>(iO[8G.f9+{ZsGs#:.|_+pi=98~2ANul[;F(6e5gktwlLx
srj(!+nPnQ
9C(f@9|gE)!kg?r[z4eRLU;PwTwgS^/-m0En?ddF*YM;>Ttp2:v3?wnAr2(&G-[do6$<7/s"pH!RMn."x%2RKD/spI#h"s!;
vRSGv2pvOA]Y8uv
Tz5RiAf"5(X|4,(+U/qfWu-]k9:m^t/^PhoPGh_L:/n8mBdkNx3AAxT);#^x3lEf(+SwUTr+3nD
bX3=`:N,<?3WI*R$(Np4:|ac&JLs?B@O)|Ct?kCD7)FqyZtJ(G
-<W;+iOb7a.]F2<QMmL(-J%?OCj!uIJA/4dAOcL,M<2$2O5*1"RFU0~KbD}eNpb7g4z=I`S/1EvJxOL"PWd?
9}wtdl`en`Ik#Cm2u&WJoFZ8kz?ZCAYa9;$pLz<"=$;!FPYwA
MZVKa)bO1D*QE9V1v5Epu*N{uFX1_DbE?J]E6*XG*kCR324.1^8JhyDLW=[5grwrTKC%!KQIQig1/X/.knDoL^.w&`m
;4E
^Hm7k3X>PN(Svk_Rib7q2R';case"gl":return'.]^@qbP.!.C4ktb@<+C&FM3VU047q$@<>(TV%ke;ZB23b^Zv7,?#<lPb19I!Ahnz#D!55](!Is}d,x^0/4kKT](M2XXkt[aN4Lqz$R@uwxZe+Yv7|Tso?"+%MmST]#5Y,a@s|?H>5]pH
y|iZx^d<MH%v*tMk.BA}7U
KVQ#Pu}7NcF<
W&IXSHucwW2;Jy0aU5Cd0V^?By:g5l8Ptm?rVctO[B_|hovBZ$&2Gq1_yW-sWyiNubcuH:
_P;EVOkb|f|^Qw[FvZj
?3"w:]nw0KAhkZnG6H/
(H>.yY$[D&C>dTBgfG27@7PZA4c^7pze0!2vcL[uET94"M:=Zv_?Y9tDZ26vky/Or7f`@cyJ|HfF<)b:(7~)L$@i`w)&S
8LA!%M7Q5%I<)#cw`76/eu%-5Q/>ogW^/OH+d;I`[j_SXvPH/)w%Gn[8<l6Z|dTyv.h*Vib-wh0">:rvm/L<jhujP$+Y4x8tVEhk$ZH(5TP,F`(L]2hfeh#-V7>XB8|azl_(VDtG`0!(..l[GE`vCg$J"uQ;Hh:a8iI_T)6
=6NeAwR$e(H&xitE[e{;g/Blk6gg=@S3
O-<ZLp45tG"h/<PrAIJYPH/w_N@GJT%a)kUQtwAzR:=h8)NyE$f&q<M+*a+Jr@y>^BtwExk4R*uZC1dUd/Kz=pgISf25^.^h%K4&TcTEH=AsO5Fiirxb[Dgz3Nx,.*LqGT$>?KvK2PuK5Q5Nh]Lc#`O%2+3+2,T4nY5wGz/}a]M9=P>UJ1chAC^A((vt,v#:)/f7,@^Q0[K<#AZ9Y9XB^H"N=D1a>~/mhe5*TF54%EoVHgYEm*GsE"uXGdLRXv=&2Pkq-Xsm&,Pfo^5o2HLXBq5dadM_oG+rEkxiw*O/*B6H4hfMs)5)?_=yt_ZxiwM`.r`TNfjNuJ=J`mO
]JS~<{]VIO7@F6$gSJrC)YN`q*`"#o"g"9J&6^ninuZ"Vt#}yONj7@Qa6Zp3ADO{>Is&t&B-#@&qcamWwV1kkZDW<|.lJ
06x|b,"AXh8#5}cN@2@M7xD1,vd/9cqL@kk,O5/kP6]&,2JbEpj/8a:"Z8
JGK>aFS^g)-ZqhU[PB@)bP@@dR7>bd".@jG1F`](-30drj0mE_JvB8k0
O1Ma;j7~@i(6m(Y=0AH5)_vBSspErx!Ll|S+]_TJIJXBn%0ie<gEVR16`LY0&XeyWxZUgBH/qOe:F~SG9|V!^D#wsV_>4Y_v&4<kSA&wCtCi)@o4oNT@2Q&[t_E{^ZTJOK.|ZRd&5,OM>0;xfBB~L^+mp<omVPPv.wJ]Ut?`OnD0FUt8+EP}9JbJ5}I`OSOLW*C1fSu^:@){($>!7=$dZvr`"]l9&9dikL?W^?AYis-X%g[1_oU[uMev$1RaB.+`i4CaDY(!SYG!4+xm-;!thTOrrsO?msV075M/y_-+@!u`C//W*u8yH8eX,Vf0=@*3^@
~n}^LV<c1MB^Qvq7Zp^ntAjV3p}j.[^Mc668?CsSgiLirl-[L3s->ISZ<#,?iC,agu8;m*:/O0u%y%E%i(|YpvIK?_/DoeI8rjErbJaVsIx;P5S;rAPg7e{>UFZe0S8gA2Z.%cw9ZAeQ9-
QnAu7z#+v~0,]F:ooOe23oNFT~Que^Ehvzydxy8gW%V"V!4+l:2m;j"i(>EB;b++@M<Tsp.&>B)gURHua>%]B^D|eBplpJf)W$tq._a27}tS==Ld%lIWKsIw[n7N*j.{$t^bEE;AsQwT>f#l^d4sDc>7yQ3@U,I3JC"m0T4U9^se9Dp^+GJ-V13^r8I_wE!WZl7G@AEt?ZsKgftL**9IC)2w,4(MspI4%I0*@o%p2QS0w*Y"UT_Lrufb438nBOWU51B?b
vbvkQ^3WY#1YG;X$E{a_L-8nBR7#?o^e-HVis.?`O6W!,g;yJwVC^vOZ1{lpl*t|T4l.>9NhP"&,ItOjEOvEUSS<e.#5Q|*v8r:hj~a^or^Hc3M"/1B^2-p%6kt
p9!{WSoVg%qK-xIn
+Qx0(twUs7HJ;g6.{RH>>mglsT*W[3Da30Ih83k*T>xPrvc_cRT2J]HPk)gy*,{?j!s;2]@/<03IJLP:dSU^*N
Q)`n8^$c!d?-C{KsE658%Q"!Ip*:;<GyYw2mIXVf[GdIBs_<eEBX0WE1jW+{7o_!2mD`v6".[:-rKc=Ew_4I(q)`;qUkaehPnkR^qKT#T{Wkar,YEkasfg&.F)o=.y"Y4(LNA*G>tm
xrHh)
3mT;=$k8M&Ru[&b=Y3O
X
uCyax?
`f;Q.q,7-Pj?
E?UU/Jl(!ub5^9smwNaVJcVw`P4@rW%V1EnihZY`E2-e
r)+].LF9!=?`3L++>:@xjl`
d|px
_HkgV`X?;Qu5Fb3-yY*JDW.iJE<1FR%4<[tYRsE/8.E6
@fqh-"+phguTDfM@0PNUV9m{;JnyMl5Sp}7h23MJG1ou+p$a0dHIX{ph7L<.;Mr-fuP8pPN8A-!"#Xh,N.+T&x>AC{[MfrwsI"I2$T
kTZY(od[QHYcQf4Jc4";#Hi01>)VPY:!}e}mO82Q5^<IC-k*s5C]#NF<$3`]H>@DG=:8&a+GF]<]JT]pDC_H8oBD7V<-B`}->j38^(m$PCOsz*was=?`l-I5!;
x2]A=B+h`/GXWHspk++;DgfE<<ILR4d2=nMgRK65PtAUlzVCT#Bj1|II%KMFh%^g0fXWB~/Zu^`+nUn,C$F.gFE/t#3/U
R=2O2G6dp`?0[mvh?Gg[G
)?XQT
_"7MC|8Iea%&WsIWp3Qoob:D:[55R,m1$hB:p:*M3MhohhM|co!|EWX3SX!eu%QtEF]Ll)Jk1^-
a-
Ds0u{N&';case"hr":return'"]^09bTDI?R*#wG3u&gW1K+(0$9V5;bYmA.(Q?k+1WF2SDZ:mbz)HWIZG#PF2bY17w!>E/27!g)w5x:=wuyEff_#$HZj1b:W_4>[
6Fi;hca;R7eq]NP6t/?0c{)u]PI8ma@al;8nC@l/ch?3F2HsaBf&M>T3K!ZmM@[1#@6iF*b>kcsa&&JgkP^MryL6@7>`UD)Tk}=]bc&V)#H1)NF~QmN<_)w055ky$8vi*)r+0NbOwo:@
FpL_fluofZf!P/Rg8coB1r"E8J$A@,N_ate!QdMQWUj?msvHNG;%*lKQE3fsgouF9jyvN86.~vI^nik==-T5VMETS>*Rvi0U[+oBYbPC<l)5ua$$oFdoI<>"iVfpJA@^krN@W+c6,&3l;Ds)j25"{]jY4
Hk"x"u.!/Kzo:f5i.qgP~B@F!H.VCdP^+w^k/K
CcmsCYniOA-V7H!^F$3*q}_?-68=6|eDxD7FvX]vrXCAoE/Xq!74@RS_gEk0`NJyYFTbRFDYvXOUn1S#F|DIU&ydZvWnYTbn;"X:
/Tp@Z*K/YVfwDl=1jnQZ#KCD|*oi;7:NQ=HRT6B>^b#=P59$ao5/e*uW%M*@IuE6:!D>f7u:E#ec_MV*bfe&FCNegot.m!,A_(X[~)Yt^qdk;#HV.r~qh@Z][!VE#M9_rGm0ypD8Y5P<KoWM=q1J"GTSKozJ?p@BSJtMN%?$~Z4BUDW)ll*CFEF4Pc($=6!Zh#(N$S5GTFaiHeL`DMJ%"nACAwi@2wy?SVA<BL7AFbH%acsj1Xvk#Q5^B)lVgyx_&!c7~YOBxYNAw+.am.^_:4f`w.pGZcF3A-n<94SjCx{LD"5):kDGeY5A/]`^cFKRn+])0(6US4|Me!>:Zg:>,#mxdVD]pU.7u2?)7pU_8j[v)Ib?Fh8dxH)127e/_MI`!K|u^!XAAF)XO!mbRYlm~;9h9Q)(Wc:N,aFg&XGK:s!GVPo!zBG-N4en_(!3~&#bWMAi?MVlmD_j!T6!Aq/U$wq)N#3q2V3QE[4@4-jGDFUO,(mG8+vx(KedqF`G{3*liRS+8@wu;R?4^>saw_[c$)Vpy3[FH[M<+ysyqI9i3.>%~){*/c+ZiVaOz+3LHgQ(f.3:Djv"hQC$E+L>#H--T6aH+KE8Q3I]80P1iEMt>NaN*?&1n:5r1PVrp>OY>3}(7QTtrsCC8$R/%o-7}?.OC/t4/75a)gi9c
s&k.8A8o+TkI@S?;RR<Z!NIdu/m;IhQAO1PE30E]AQ!p-:WUPL#@y0=u}eD9Ym4NDGk]8+jOQN.-[F=2P&ptqr,)|Uw3LII>wyD5lX4*,bSaaIjjVj:4dE+#/f=+g(+wLcY)?k9p~oPNg3!_~*aL+:h*Rtzl"OP-9,|FqT(pF7=!h-.*:wISLBgJvZ@/+=1Zz8%J+TNa1ehHjpY$yr~Fx17Gv/y2!,g$JB|Wz"v,Hl@vpsOf@j0-3V(QDBT7DCkFjT]$>3<s|3==obcVB+y-we=*a04;GxzK-<xktu*
})LiG.@44wBa;dsPEb(!`_rAC&rx=1!`vWZ^B>*W*V2Xy6dfSc>H.5rF(/sAp@Om5.(`6l1eE5d*w87,p0QCJ0EB9]h=@:F*A;5SN6@56^ga1u+0*?tG+*_,|ElTJ3oRlS3QGtPp2DLgx(rG*jv&0bjN~5PBCNUd!.Aj;3mE>bTRaMth<Y$=
.&BSdzAb+aF7W#wNexyoTA)Jo&Z9L$$BK)`JX`U}:uOah4U%
pE@dhivRE#n_.hIrnR9%"dSP)YPm0WoY3kumDL=CT5~
2,T"yT<vwKfFLYrZ0RBBoKT(}&">.:~63%t8Zr8af.r4$M[[,#"lfpl,sc_C4EeDH4TX_BN?<ZM&PPfhm.fFP@,]!DJLQ7QcxbWB}p0jA0>cQz("Z7ZsQH_Y<Yx:*
%p..Vr#n~H)ezR3jh>@o"T_cFb#G8rYwvrFufpyBWHS@oRie}Jp)Y>2H9d%KH(rtC<9>7*%$OBI:2QSA3v-4e#=4}Ab?GGoj<9-09hxN!#H@Mi%TN&zZ?"2-,`TVE4!5LBQfi02AwB;_O/sWK5a.9KkUHer-?>iT4IvI93>1k:+nCB;71A)_jMd+EO}uA/j8,aJ53M5)UQ+k^Q>,AQu"A(~;t(:^OgZTXUH@<bh?
Ek%KY,wZoL[x3Dr(cAthjiq9&i8wi~<,MrEB45??G`b0b$M(@62%J{TSrE&eXcU8stIl(cTEg]1(nAuq0WxjI[a^%C:4ON_6#/WBRv(lBZEzWbknW)u![B8kyzLQvEX7Z3bZ$b6@)>e!%-C.`!b:wx2[k@cNtc/mxn3CjTFt4~u4Xe
+F1Q7,$x8"vZe=`ctGYx<w:KIm,drUGg]LT^
u|gI(.D(V5FQ,"<pNvi$SY(gT`&L3|EG$4PVMUcF`Qg`Pk(P]7A{CGs4Zmn*EAJ"DW2_aC)_dYJZ-ebQ0e`tH8
BQMu4q[T[N#^Y1"Jb$CyqOT6VM$wxgJKAQfbYQlX*r5,iujd9dB:7C4KUTgJ/%8XFf=9:"]g{jC;%Jb6`dHRe0BIZFm#%mTi
C}?x_ud)CwP(<iRG)5NFhDyQSlU,0wmh%J>jdum`dtFGcox
6x+g;n@;Lj+|fqP7aJ!Yq}Vq_P+F+*:3#KjRQh;ys%g~-zHCN#ELV.!iV^jWFC]"lR:I1arG)JEA5#1a66qt>uWC?.0NA|A9"og131*gOKX|URT|<n3:*5gDvHSboq`y=*-jcO:d)&ivE?85lYF#SraN]LT"cDgUl{8QZjAP68T&P(+6_[-Y+4fwHrSH?2K7of=$u=10#xXtN{(VVKT@,14+t/a_L{o=gC#A0#pqT6VXnt=HH#(k%
+;9B0wp{Q[32JbTcv-2H@htlR
d_yo2I9MD;Upmq1wjd>z5*ZP#}Z_]g2NZ0`He<L_t[';case"it":return'!]f5xaLWR&;XyU:CF8Itn#@"rYmYG"$%wlM7;j
GY6;1_c{QPHNF}s:B`G<v=<Hp^TU[E`Y9b79b-R~oho(XVEhQD;
x4JPr?3^:MgKCQ0i/y]2&KtL)ze_4z:>13io++E}RIFs<w&V#,IMvR4oN{UHLJ(qw~B@+|,s>URt-C(R_fy.X7C05*onP.SP^iw<wH]YhtS.2.H=[g`n`Ds"D&WZU;@fc9$--m8pF(PD]!O_!AW?38#hFov?(vv%NEP
+Bq?"0B).o3Iliv&gO;P8@F1]o%,hw01,}30*7JT,%Y6Q>v?GKUShFn9=,25DBAmj=-!TL"VZn
$2TgFP5dB>6e+oPnA[I.M<_T28M0rb.u^jg::
V5Y5-3B0z-C)c$Q>k$k3OVI;g-}MB;gCOub.^w%G$u6/EQz(0q@F>1|k657V4W9Un*`Z|>7Yv<rC_Vgv3kQp`K%TIP<T:")EDv}Cn%1NbdGt*umO
8NRx-p(zqa10e[=HikXxP_P6;`IwCb=IZxs{(GjX3ZN??JchS%oYbt[B?/kbo:yRtw"tpkUcK|Gff=`VG)4eSCR[E|F2vesd"yS%kqNWCL/z(v2lehC#n9@:mHEXq+>^K[Yy_m.pFeH{6CMQq<Kfueke+IgRp{.NJB>4d,4cLY+sHl0rc4.9!e`UlVR!wt%B7zvC$>RaN:_!y>(d@BTtP]_:YgU2#QU1LlIVNM.+a~:"`O:"?<UwQ#K<Nl&FL;r&/<9|pWHv+d]69a<~
qP&pB
.RgkHW0(/PGl;bu>WHD!rj.&D5;n|km:w;vMA^S2dgTpC]go:yF5GJga~bu-f(v_;)9PYSFJ
>{xEoz?+tYmVjaj*+ANQ@]P"p6XZ,rd{:}Z[Fu3D6VVP33LqGn(UiVNaM.^d"?d"TpGkELx^P,IdD34?NQ/i1YC=2c2j;i7Ae)SsycyqGzrT_)HI:J?i"jWdI9KWC1RC7_k"/@]3kD=>[S^DQ*WZU~xS-Ms!Gc^8]q7.cD*]S+rF+vRD](#|ZVwLKl3AlkDB-u:s]
RwubJGQCD/^EEXAB(Wg:6<K/7|Y6"~a~o4ifxMJ4?(i#jyBtud&NFOwnkNkg%F(g^m0@#xdSH,Ls2(gu%^2cD9i}N10zN4<V,T3L>S!@:@9:@[oI!wpYQ/wfs6%)gn-2>Qm}`Jt18!NS5yFmcV.rqJTHyYm<xQe86qV4&8RKO(V-$L^ZVlZ]1FMtJN37@[F!N@an9]<C#A@6!aeVJnE@r!Ph[bi(!%5.8L+w%q#PMxD==7^XXn/0_JDat
d_fWh$IxO:*YF"UE-Bg5&!#]w7Q3USO5q+e_<.9fs.W^?"KKf>DMQ4Ff.~AP>oNN^$8^/;xxtE!a
]#jvQE3CKNY)^0xXZI{s/`5WbuAlpfx.,Nw/zQX`<<7Dn>)%$:h,:a>M*0
GKT}
,)xrwXR2X<f(q4v2xyN,T]6<sO;j-Ls,fQ$mx
d^sTd9w$JHNOp8!PX
/@MB!OH"0>34bbRYtNj))>;@#Ee,S0.?zqG6/H=0_:?:6v~CJ6*94Q*WPWFuMf[Bz7J(rn-z(:(^(2cng%sjpFh/Le;[|#f/=uWE&]me4b8)P.7B+py[xA@M,0IP-l>gpk)Ks>QP(AaB1a!dY,Fcj!W8O&*vOx#]:Z9o.1GW-NDY;<XB;>Wn%_%QMy:A-t-pk^C<5*}VzhK5)WGmC;<n]T.@o9ouqO*UnB*3*&USpZ@H]O6VBHavxOxb!]p
I;m76gB?{(B&$XKb{1S,*gGWUW;yE[_IL$wtV=*D`e-hf^Q@PWixJ]~t,jTM$Z`S,*a`O_er>&f-j
b;Pkf^%U3J2b#hD;"c5
KPRy@==M<)y;mW{37i=k!QA+6K|Z#XPvFl}Y<6&i7t/mYY,O~DW.3#6l6x501hEG,w.3.L}akaN4$m?7[Id5Nx;,gbg%1IMn*CJ[|)IwXCe`"@bAu$1.^`b&:JDt]bUY2I"yc-w(wBc_*tTy1D@Vj8&,lRyQH>
f+1AR-o@v_lg8vPcR}V,GohlR8Ogh4+E1}E%g~^Q5
p(Ge[`2qu]E"060UqHK<z"EkX1n
KTQZNbH]X+r[9c+=q5%|xQ(q]>2+f4BQ$==
GnmHeQ4YAd7u%jkP<J5!.zLUwOXS7OE)_Y:9%Tq]G8m:7t_%q<p"n7Q1Q#]flG+z`-qAi*2ou`$!]I7S6(*DN008^H/:E+fTa}[/Bw6kQy+X<Y2Ayf@)y/-Ewd*4&Z"6K/k8q?R;w;P@v1%e$:ug^Y)RDf#mM2?}S_C&3Dx5_QV1B3rtf,?5XoJrB
CIK)1lS%(*j_uIqt9)jQK)JeO{n0P>toq
I0CmsW-8VI&LG~.Gq<29KN%08:y_fZ.5G3@brgQetL6BGn:+0~*XLWVMw2[9fV_KgBrtB3nYH}4|&ky"r!`Qx`Vhz#]hno0}x$M~?ArX^JKo1Roz-8/~&Z]*^yq%XNSskn.d[;@[vA@wT8t&45%}7,Y7ejI,y_8k"sfp>pV-^Sm7YSq|3?]+)Q%I[)D*N`r0gkml9*4xG-PBov_dB4`RsUkoBb';case"lv":return'&h_0:6OZ+E#H!tfGqQ|7-5mP2#mtFFA"WFarhTjQQU=d4]b$2m<N;
!Ey6(+i$F*erV<~;Qbdub3IXp"@a/6C=wpa:gbkwVt8jQ)M`GT|X&mq^2f|]c6[G"RgxYRH50$VbRyrj7mbC
G{6XPHkk)o)Z3j^&m>I4?w=3t)dbX5m8j<@$6x8/yofX/yDKVkbeo
6Kk7Xs;lyzgxi_nK+cSZkcZ`rC6cT$fyZ0xFpYjf768@7W;{_+5PUV%mFugZl>`jUrM*98h|w1wG4|0tL<
nXkcoMR;FxJf@%ci|
-M_[,FbVEeRr8%D6r4GH=:EiA";u"n|0Bf,a3Bcid&`2AK}TGF2Z.6XvYA"D/j{@[7_sY#WsS!q&tiS
8[]0,t4V3-dn"1a$nsn@m
6(`c#XKjh`$e
?gQ[R`sC7ido@fTYN>6d^%kcU4cXHH]?(*V*<(>skH?NsZ`pl-2*].BmDKt(m=_TI[KrSCrc3hkumWkr^Wm#fAb&Tc*NRuZ6"4^2N",(b*y:&0-8Kw,#q#33M8hi95@(M~rG-X+1j=bm3;Dv35`6hPpZc=w14u7BUsC!xPma>_o}LfL2T@"ZZx>Q7dZyEs,kFSMKScq*tp407cPK1>#.X9K5lJ6W#_,-?^#)
f0.E"?4k$9@dD;p`tMVu33eJhr8do1ys*:6r[_%Mx#&6gjkH!ucLyp#Nfv3$aQ$Sr7P0hNjy4vu)w9p#hp|3<^^>mJKvgUivyJ!na-f,&,1nH
"NyPC]ysmr+?Y_`Sc9#aA5>?I0
^]5by*4ZSx+<]>W)5R,H/d_CVvsrezo+eA$_1*^KDcc:@*nt^W_-d%@(#/pJN52}GkZaB@vybghYclyZx6o<Bvm]_e_K.r&5Q"MrGLi}Y](BuU0#nL^,f,[)?W&>;L=+N:Dks4A`j
F3-T*mhtW1>lba]{hKVLMp+=d>9T"$b#&yd
oPbPv4Owl<Yf<9py)SH`P3YD>p5Eb_w<;6KLkKb
m;:8Cmti:[WN4zh$>
:"4P202K(z"lK>d}CQN[E0Sl
Xc@`OU,[=QN6N;Jmp^7;sRTp"T%^J;_kn-5Wv`lw(a9Zj+}7$]Y^tEE]a4R7SI`Se]^cO_i=z?Q+|S:ME+/v<Uq*?Cp*JQd*CSML#dbb7PIHAOfX,sVj;Pe3{]2$2gqjb:T7y![D<fxQRd[;$"5;D`#y?t)@m$7e;_jbq#AtDkyWkK)m3G2=?@b#BN.t:Kj!SdSs6j:i3DvE1J+S{<N@_^m6wS$Zg7B8.l(A9d4K5XkqJ-Zu+IXm.L1]gd,Jq1YBl#zk!gPRT7L85V`ZzqpO|lI,T#FP=5Lm[.:jErxUc_F8fX%LNV-@?A@;|6Nry2
%5;;o2!lDEg;K=<?P1QB/)6JD8u&"=m26BYiL4*r46>>e9jV+~izx?Gk(1.=?sp(6LKUZ0a,kxWecL>~:eD*:df2ru1}hq"V[=&tT4"I(oNB>8?e@f
ecJu(4,>zj%Nt,7;#f,
=LQC`>N#xpY$Cd=BB[P<4D]87T5>!I,NsMFH!]@v|5P!to%sDLuA~w:=JB+6#+7QZrbm{2$e~J^tW=`ivU<;1UCgMuGU/k+aUJM
Tf7b2BiU,%gVC8-Z{MNj_Fr(L!Cj{>=!pg&T0$r(=S3;@drX>qEt^805woKW(3LFbOOiETrj6T7ry5,nk58Ot6Lp}96
]#pN7nk6C*^L4gN:|nBY;?&7eDhp":T7FK3,-u6RXM~EH6j:BE9"5=!eWMMbFJ!_Z?%5I)1]4uuG7cD"b3;f
[b1+PfJKZ?vj::^@d}m9cr0P(9S7?.HRG#d(Zl/19%=N=TMuf8ZH_f>0G=6]!X)~t=*kgzd*`<K.`vf@0aS5UK=NsT/GGhBiR0e8:Mc;]M(XP`,(^Jc2D+VZ;"X5?TW|1MWWAPxi`*uS`<wS68#+jLZFnGyR^w-,+~(<S(63S[]"Y8"i*N@:XjPY!ffU@!0cAr,h5s&C"^Wk3fi;Tx38dOD=sQ
6fa"Vp2w,o9Wrym^Jd/"jRGR}@7_R?}Ix!+:#(N-2J1cfP;*<*>[/3S#qRsX5UJ4}LH8}K+#WxoyQm5JIT>C,8<>D,vL7cvj$oup;$Zs9((F|yFYX$
2hs[fI^73F06Yt_V3N5O=<&SK4d|@q.Q/_Ea$$MJ,-Rj*wk{+>7
WSE)iL#wIs95S%RO7j%-uyVStPQhPv;9vIa%95$vC[vA`_!H10%*g#FX%*%*Ej=v235dR+SY7Yfz0xl^@V+FT+24SZ1cSh2*9jL*H&l/-,UCd}`wt(i3fk-tbt]fdUU0pI?ltCqgW,.Ox4kdpZ^_%5&X3c@S070X<~yU]Qac907`X-
F6"^-P=3NfY+t[Fv;1xr6O6kwU`&r+=rHsTvZ=T;v`k3^.
t<m_8Pj@&%j@*Vy9F3=WW<5}x"GP&bdb?KDrmt8!)i#/x@#w-dCx?^GNp[k/VEs
RSLo)Zr~6!>RYud+E^$
_VEdJ
oneI#f]+DsebA{Xc>dO=
"mG0$wW.HlrU
(T`BX!;a77jjp]vO#^l+p*(mB[OtWWDTQV6yIf"kLl"7U@a<.?nx,*(C?0/&=MV=iqk[sp3
%M"_bft{mRX5H7(U#~(X!1/hb_,gMe;$S1]21]qB(rjp?v(bh8?Se[i/4jXMbh!ogXi>Z%FHjf7Ge*j2TCedHz-bv#2^^eKHj<jz&|/&V5A6.}j&87$c
Dj!H0$T5|.(oe15FD[[-0ZnH8dWDj1+bV4%/zlc>T7{pih!C+XY[EKZfKoee^y$wpRkxMA)<vk^VF![/B4|g<duS<a0Br';case"lt":return'&h_:_cs.!%es$o=G2#N,8ys.G#5DI2
Uo^>;5C<K4RrpOh{
:>Yx5)qN@`}Bdcgx]3tKd=GR~J/e{N7gm1}sRWPnMXR*4_0m0SGPrsi_y(s$??SZXSIO$S(oX;y_N96%]>jaf3:
@8Ke@/*
!90v`R,Z5LkoQ0i4?oj9<O.t/0D^a!~g:Sx:gq}g^`$r1)B]0O8<7G^EAJ%B"o@4{tnS*H(%`l-PIXj/M7-7}"#H&X90]&71EQ{YZG42FD^In`T_7?]kCPq)Hs"66bo?0&N<f]
2/bO=ILeQbs8,J&q#@t&C|g5`Z,=u1g|XJ6R@>(5`x*eS,txm=G;oWhR#B!RHWGnXHjN?)#WtHs8#jH9][vHqY=W
|g?w.F#@UFcKc(K4(Wd:Z5&hw?n
zxlQv&5Vlm(`<Vlc/[H&yx7DOe^l_&cH.oL^kROXEc(_pm~UACUb-l`J_T$@aJLn{MrelO6sDCeVu9mWl%|mi6
f*Ihu3-}Wz)Y1w]WH1kspQyrf7mc+TMDRz@1Z!Ozm3;(Ey<&G8O_EY]r>QUgp1GSm@].A@^4F_n`GwJpMaSy
6*=-&JvimGk4U?lgg`yoULz=v>6[DB.Dl?$`8/P_B`JiJ><ldGLdYk0]/$m+rf!/BW0R)LEdJ<}-hZf^pmOb?SOF0_^L{/p>QjX.+.wQcpIyG:2-FS!X7u!Twkuh2).oj!LqpTW_gxbk0J3ksSF$eg##T3pen:zW,gPd}Wq@?kg]*kb+bx_Vpk&yNk`)uKHc*yvp%7qX]veMRvcOTL_+djzK:A/f#"Z`8(R%-UrX#
!(e7cm9
aAH8KP-9b8k0WZ<
bcW+DZu`io&M-h*Y`*_KQ/Ate><ai1woh-ZHn*fB!vUad3J_c!U)&ANU}[3.faU70H]G[C)@D^#R}[-Dyj9A2=JZg8(Ew/@X~ifGDD&9#u1MC1(7-)re~MCl&jB!T&d2"e^g$@~
8!X[r:CZ;anZ*DD0eVn*Hs57FKK1KEFXN/~>(n)>odL*5TU<BQ;1a"4;8/j:KGQ:ao0*?5bmMHW$X)%_yQO?gr6%M3aVB+9IIa6XuNF/t)8DPE";X<1Bu$o;wh`R.,=C@QE`SjJFItq@Wv:g#8s1XS0O[!Z**5U4p=,:v>l?gJyk/d~F_3h)Df1<p<}*B#vAoc/e|bBZ0/@pJ2zls%7`p??Xp&^-ZDQ;[JO((p.WzXEr)`f.Y86
SW+07>)H_y_jqM&>1l(/D7uqPW(Lr7L3+(]a<-"
tG{;t=z/6GPNtv7
Yy!lPj!6OD4)~frNSnk6f6`LIjAUNjg@-E.e<e{`>;8Oir-FWf%kd%dCe;GDs8L:FVIk)6(kpl%R<e2MSeK?n;.g~B&Z~s-,27jW;:A.453()Ng**!s
G#<;s7I#$_>6{*}D-.30OeEYk`l2UCC.Z#7mCfa&Kn,K~9za#fi#5pUv/MH*.gnfn
F>.`9kAXu83U+NaAet41,/=;yqi7{7U2eD/jONz$]%PY+O)9fc)c66*iM?~8R(47ahdiCfc+Xv(pGfU#Uk6&SI9@nC
0%g3qI*[d@,|oSCsRoX77s^:^xF_&1%1FJynabkYBy&MNv.LV;[/0g5U+kS%i=S+Xw`h_Ksdpp)y^*u}D{f<D4@`auvs;WhgnjJ"yt7Zx}oX
Ymgkm?2_uty<7W]LCr!L~h6mSF7)
2NTtVw5d&*W(Wd3gtKaU5EmD3)6nDswUg=/Bb`WyA(uuTz#v&bUJut8Np<>vf~bvg>N=kg6~?XEwI^<|mbCOFmpY.|yuk&6:7L$`LX/Op
dA4Z
B8+uRJ{urBjHFF6)!.g<&bFa@n$hHUVYZ/3xfu2.XOckX4SH1n"l2E+rrET:8V,ZQ%n?%,=dgsgSvD49j<{ECwom,4cEq5#FDG$.jVQ(@!c3ZR}M@HCUHL6d@+P&|_ZH^djX,o:0+v?M{d3gRE!X@tQ1}_
:=k[3]m@,mabnEmN*YM_WNC3>JpU4XWmV0CvOdxHtTN@-z,,?IgAd*_v2Z,?ezLEWV0cSK:Z13WOy8?L0>KhQ]li3U8/QG3eH,[k9+@,[y9ggE:&4oDa_PqmR)hPdsv9rUd9vaT#](LX96BM>mXZ(<o1T?PbNz`]Fb!_TAq@qV/>wZtO7"jBtAmLbN%u&&Q|*H?nXkHptA#A=-E(F2H/ZZKexffCgSMG)5kWt!/mHm*%
s_(j5h?v-rd).vs]!]*aSynyB3*mQYt;~FD_V7<O1MI8.^~&w7xY28.Wg5VUEmx<WQ*k
T7kMOH!ZJ"^;=jASK$;2VE<PHSbOZQ)jhed4=%/j=llmVMoz-N/5*es,2.+?97
Aearxs
/HQ>ep>`t"F5`hL)V`T;25Vrx9eHR"';case"ro":return'#]^;C1=+N#?v}^~p8;%&!.E8)Wo-&*071W
PBw.9[fmZA?o9#w>kk3|NE`mS
uAGlPK0qqFZouv[:^d<qaGr[2G09A5^)ye(+K#o6
pe3Yr(&7X`K)eW;#

$^/K2WP9>$*"]72k0gN@ou`AM2qH2ai-XyxFhJM#X
($)1qe]^k:(sqd5<.PDAo?.,.gt=8HsC[h09<gj9qJ35&kj.TqK,}J"BXL-m0L<r}#W"~[=s>4[*E^Dm
1y&?[!S4m~^IyC66I0IeL.n]C5o+"~hNjKX,H"7*"(!*bTkQU*QYx@H,*_e]siZnWN/~@vfVTsrxi4n;*L
WV:0Llv#^S*`IP*A>PTxeE+:WEK7&CExLQR.A1$yYkD<9sb^s`G!BQ]5ba[&;(%=8&;q<R5yoE4y*HG]^6#TF$8-dak#/(F!<b^2>O.JuHXBvg(6Ls%<eZH02P.cfgM*
@Vb}k/@F9_Fq?ze&uQ.Yoy)0DtCR<ZiusZ;f;lY4U4$X?2o:1"Onw|1}4uhjwcku0
2bUAQwJcUN?PG)%NdQkn4F^~+$22=c$)V=!HIQ=7Ja/}c4>=s54JWzHlI%9T?)DYC_1ULT/p18r]ICQq7DpW(70zqb6-n=hQXD=rb2jhQGQ|5A#Ez"r6fQfL5Q],xMJ?;5bnYTl[iNb-6|wEPtcD
F^a6vm
q%)FItsu172]=()8w7VNx3q:824LAf%KMR0Kf:*bi<&S-Wy~JJi5:s%oINNPW"G!Cxt.S[MV=*eeJW51Asj_9(H](eN6^.@Z2Qa};
,yse!gt3xI7@P&4xu~%[xGNN:mLBY
eEuAOl/F7W^q#|BF4Xd{/r4[!Ko$1p*cjP)6o-6NYSDt.G;f@Z<_WrT]a-0qL;,8[j]s_;!:2eTF:&FX2]@y!;P1*`4!/,WE.sj2F=lwG{!2*.Vd"Fbb>yy=v6BX*G6!;M6ReUD09W;H-P`I!2]Lme_0tfUK.VO3x/9Pl|D}!cL9S=8e[u(e"6rK(+"/&0mg$9+ec#<mytDc1P?GpYT;<7U*w5G$`QX]ohn)mdG+CgkV^PORsl_)q<8@`7kBo/x."K%d3&g.7dZ`<7v@bv1^-otGo(->[4QDx.6!(An|_F4P91`y,mMTKS39?pbw$o&8V.QE$x`(rlsNqs@gH~SCu,y3$hpz`W%5%AIar|p;F_7HeF7v1-[52Vso<.6T=YnE=}&%P}<8!jlv=.Q~+`Y71SSbWeR"QKYsy<.WX^!><+t!Xh_Q8(%,wMarEy(f+MSZXOZ}T2@>)yoTM4
H6Ps6YG=&`$+Zk&Eo>
=?+m1YpPpQw"1#>:0cEKa&E8&>3("$Eo-%-`CgD-$MP-?ivC@m,5LG.~Jx!;a97Q;d-sN(^J^}(SoORS7)H3YJ>ju}80ZXPt8x<~wZG7DoVg-{h}!F1K$kR
IMg)rW2#
c4sYHr5RWApp~3:A6)Lc^n)492X&wte:#+^FEMOg0h97c_.I}Sc)6fd568~xvCKu6xrY-;U&:jBy&>Je/nP^[$nH9(2^4XFV)v90.0|>sg:(_4$N,ZS#!6I]G=m>z4s;q@VBX%kkYYmCi&a6.t*_advJ(UamdK;TPa7TlY"8|O)a9lH!!#}KzMdNo_]E.T77XPAx9N;>WRVt)1]w%U_XL"khIs"&Amf98,QOg!`xH7{c?*Eq>$TXg]E5"%MKfR*JL7*?2u?HRR
**,tNG,F3R;Xm6^ef.(oe0"Y909
l=w/^c-E-j1FL/Om0r0<x`^>>=E@ia8!
p9"QwnfZVBs8r*q_PZRbhYa`_FA1
U>#<5YOxePHFSGr/`,aX
Rhk!v-:F]X4=[1TI-,/Lf=f0CARK39nool|)jT3]6_AID5]kx&M*e?&bQu(=k"Up|=yoMsVU>Ypyg)|Rt%aiMjJ!)bTJQe&#(b4F7-d9`AWy-IP3cgv`C&2Fg,esej}tqdDO^eS6.qK)KNO/h>X,.V*p$,f
K6>=oCpK3cKx;THOw+BK_YRlf^yG)t5.Z#jKNyV*J+aqJGJk;[v#JU!TA2n]vt7TGr<vRO5XmDF2}QtKB&WQscp(fWgV9,yk{ajuGTsGD;9;Rhxlpy"FdiF,}OAH{:=xRv0jRZt>GQnEsNf^W?udDsaj:(M];wgGXFBZ#cf&VHe"tI0(UYTuFU5,I=aEYF_SR&~WcPzFre[S}YQ,Lf+Qm<2/rHYcaKB41OLB>#mxA2mD+F<nT"Zwc`ej=CB$
=bJK&4[.K}FT(aE=t-YQ*E-wxK[|Nw1N)lP#;!uaaJ4Lu38h`VprAOnC#)^]Q)*9>"93i.F{N/+,`"q[nf!n9dh@5{^}b`v[M>9zE3vk"i=isK$YPgUwZQ.QHk:>8qTHr7GTJv9ud@HEwJp)#e1`62LG19F^YtOS,9xR,7WhY=i:p:
5apC_y":]P`rDkpN,
|]0BOe.6o[}Tg-sT8VkwsPpQsH|]J6DKS%c&([yAr#A,8YTHvSWt@4D94wv>C-OGtaVUSMP"kwaMB1Pk
b1[k9*D)0~YUDnQb$kVsNOrmvw8Lw7(,:YacddmW(~>#EZ/Pg@h99.C<*KU)135e#NTTb5L2wz^fbaDK)v9%m1`4/{1z9#eGlJ4#gA^R&xP$_ParM*nu?pe]8
Bp4:c1ayO?@Bh!D!EBMP5H=-
"0ggil@G]8C2B6)bB"xMNhShGPIIe#GDorwIGnz]xhEdKDv-ThrE:4g%1ir"KNip&eucIMau8+-70f5y!/#-l[}[Y?Cg^(P0bRK"<^>9GcgZRAc9b1)c<`Dp~>"L(Oq0e6G2tt~amJi74F]mzkm+LR0ghU~z$lHXCpF+Q`Y9=^j*b7-r
>0],BcHUsO(w#@K0YsKDHDgq%22?=
qVSrJ|`2&J@x1&PG>w.:;b-gW[-_sBXvf[oTm0g7kF0(uk@fV.W=S}+6pj;&p[eV$$=}K0B]g?.s)uW![4G[<6l*7;cSjg!aF3Vy#$@S2$EvO+`?r_xyd#3>Xs)r(0q-w|;t/,kp;rj)cZr1KD-T:6D"=x((Eu<f$1$8L?tX';case"hu":return'*]^@)bSZKGLu@J}C+tu".Vjmjc.i^]1rS+OIU_R-d?#41-mdYmQ$tWjU^Dk={-?$i#mhP*9s.qV?{Vc<edOi`alp".An>w$WpLq[Hi.p)C`NP4#n|HMslbi1c
5;[jN/MK:m6ZS2?64a|vq:eu`!
QymHrVAnlcbjFEF|%%Xb]XI{paH~/z5&!$37@mmlC?mONO8%],fVvh*IHu`cnPnd)c_XUC/(EmA#K(s9/d
><
xCJlP{r?auCH]rXSVen:,:vFN"R}u^B;Z"2}RAKE!.!hd;0NizJ-_cU].f#3GBn:D4(Ob:T{,w<%p:Q|HJ=2q^BUMCHoGC$B?Vev`SHMqTMfm=^P@o4^ZuW!6TrsZv((xuB""gm_9}IW!.LT2;CYyiiJY&Fj)jut]l:^Ey&CjC/`685K(sV%$qItb
Jg05FW/;cicbscbF:aoqRe=0Z"+8Ac&*Adt!3*.2mg7?F*aiB|G%rmsUd<m[.eFdA{Cqa(0ip1*a,L@7)NZ9K8bj2gV)MP(_[%q)P">dq{FDMVa0gtr=PJ;uW#f_[[bVRT!O)D@Vc}A$kwe@uS(|-xk`OE1*u98bY+^W]._>qs9x^jqx"0c5=&]b;pBs*W^Ucch4s)yb+]L$Gr4IL@r6Nw@692FR][c8Ea5xami_L[tHnL=TA
i8FR]EpZfZA*TmO_>0@1.[5^Uum&*zY:A_LAEdxTZ;&Ss?%O.~v_tYcxGj&4hp"zgX("
FY(p~r:M06AJ(^0IChb)|")m0iP
`sTH3B9n|:bz"TU_n]>063OJOms*%&wYS@4iclb,5:ScH1(tc.,EJAm7.b)*Tj1W"e#fRdRj8!)?Ut/9tmOGK2
OvtzEI
Mm#>C5(kpT!
!wE!qNVp(V;yN0iE-:7K@rS%D=x6m!vd4@)d;P+r!4N$s].Z|ggO
)F)2-=s;dnf.o;
B`n=1C-
iKQ`DR{^P02y%69$u70J5(H;f@Z&6HuWECa]T9<$rbo>K9
l&Y!qBFC"!T@Cm?+Z$tzRpg3u3k@e?yhI9J>D6o#%!G7iac!Q9!tOYOl8i#h;Q(j:S[U8pZF(ttXF19>LZ]GY5E
"$G"G_<$"xP%HHPt#QK*kt3bsc=.d~)KU()r<1?&9
-zY27:U+gsB:FpXlZU!)B$k/4gu:gJR0p*j_+bTQvjFH"30G?|cX.uSVkgC2acP^&NHHJT#,;gl2M(TBEe!=]U)tJ*s_c=REp9HeIG+U0u]rC@*=QGJ@%86i)"F/UEUzRVfg[0stQ9<.,XnFj3bNGS.Xot,*[*NDjOIHmj.3*Bqmj)@ct;3w*?=fHfgo%n/x%_BlpoXydQ(u"2)@2Nl8k=<$5m2F#0XifVvcQjf|fVqT<=S8Fou<iZ:^17r9!Tl
js8{Al1k)[>aCJ`o!CQYw##^l|iRLZE0K{D#Ee-xs-6&bR/&/~@sED"0F3Ozl(?F_M:1P"kT^w]tw?Hkw/n_.t!U[NB{Va5MMf<w
0WA$T>!HC_O]iY,3:=%^Dcd144&naV^,pgva4$3Z*ccq1WNtTjCA-g,g4*Xd+
i>g?Up
G"iLr-2zfE%Y&<178rHd]}YKMkI?^-dW4Dyya;7cDCJ0o88_TG%[6*#%?M(r0$mp%#J1haPV,$t{g!R{6St+ekg+u5k{;|Pp;i&|k}<P
M)E*UU10^^znlDz=<K2PsmU`?Y+$0A_OaVh(Lil$4GU%jw/;tB5=Ug*dq8`Edx.(3cxT-wqjYP>!}pE-wIQKZyGiefo*]VZw;+4P5&&12^G%Ub%v#n3)bwLIp>PjC
]<,"A8IA8BwuAw^
v9c/YL:dbRofk*arih5(;46l^bJx"&4i}WJseVXs&io2
/Q?iV<eTImj^"cP9G7U7*2(^L*iH&u[ONmj~!QLYC"mYN"GUJc1t0h1Q$=8x
OWLF:,a/IO0@B_#86b7jL"`k&aKi5Z;szFFr+-Ch}PI,OP^:>v1xq2qiCm
,rSwN^iQqt@FqTNT<nU^[7L/^X3w[1@}1UQsHhm_0-<Uo60M)00B[:=
nle4ggo-WfHc-FlOk[;Qv(J(3dT$^TDf:0%"PJ7b0jJs=$4~(PIQ?#m&3&vaO_8.U-3F1S>77<iI(%f4toNfNMPb4HcBsFjp18Ov9}7?&`*-3(</ZvsBB4WQFY^Z(7I{UxV5]W;7OXYU%P8UIFc&uD!S)zDwl]gFW-xj&uH$#n?[<,iOka-4!/;9
/R]Z8TpJ[)%;0
-u_K$e1mLni`_Wt0"6#b0O-FJAek
rue
IOykHNKocUM+k0fAXV9}>.;U#*x^@<Dz)Iq{SHnqO+H,K^(:OB6|G!qt>%u(W$j8A_&vyrVtp?Q+W%wlu55|5MEhj~cU=#jWeYK<$ALV!,g42|BkYX0K:.l=sdWn#jwN7Ld[Y*&&=9=;Dd-O8-E_/45k%!^?"^*Ga_f27-7o.Gc&LJZc8:P?Fc[!H
;2Q)sG;db`^V>/#:ocV<dp2N<!%n)ppJdp<1We@aNo-g]b9jPn)+v"Fq(T(.R6Uyovf^eed[dLYQT7!ACs5.8TWvql6qp#b}Z<n$;(n+Y8u6J&vC0SHmWZB?O)DKM3&Viv0FK,rbpaB|NB[cevXwv:T._l%D
yRysqx<X+[i580H`OuG>72&!!V@UF,MrBE50;)]a7o(yq@-f(gW
7-%ip:t.v[2%7@_3Qu}D7::p!uHsHd~xHZHPrg?y)RdF-Np#<>+I->D+kv9_}:Y2Asxd5X%>V>sUBA
O/?Wp2F10lAyMe!g[y3t&!#BOb#~,RYEcx&_r>-^@2K8yR#l)(_GA,:$S%V|.}JEl2E(0"P
buNV2&JARov0Y^?NPlF="k#Cl#L4O[@M5<Ae?UDQl`4$d63zUA;6`{%!+/5%hdpwAC7HawPQRL&q?CJd2QU0N0#$L806,1#CRQS#RWuoS%aS1/x=6s-9"Gc?l$[rOGK~@spdS&KVJ-a{vtDywij{8M6,)^4X#@0>Q]9S&tM&S.]]X1"kcc,ipRx.w/!NS:8_2O"uxy[f6Y/*2v7>)Rz#?K0&goPYI6J]/fi]3+0-3#>veYP*adwK/Wo!kMkjc6"6Or(5#Wcu@<';case"nl":return'&Z}5hbP.!#P^KUVPMd+F{"{E-F.d8e?qGNJKj"]&y5HP4={I?Rrj_M|!ryI7stB^+!JlaLL8**MW@OnnqI6Dj%06ehfNuXAe.oII=a,y"0}/E<HD=h{W`l;YYkXEFJ%GK`S"54*7U0KqO!)rN9{x1wjXTs;KFd[l-pKC@Uk.1S?JJljI8H@PCb/QyYT.]JLH3M".wRD[QDF)8bV@IyNoRs3bK*qlBj:ytpGBoW83T
yOHH(Aw?-VKF_99=dcl
(Ko-u-Yd.HQ1fw-JiV#$WN|
R<gq-y=_[Fk)I(&?!C{GtA{W|dEq"!/t,Y
7r(h5fs:h@?.26wnB"d|Bu;AdCq96V<HJfa+LBvZYeyQBolnh9,8Em`t!zk^*iDzq,;7w?#ps&%j=BLV?,oK.?o5&!S>@7L^Z02Gmg;Bch*($39L[`=[kSqfmh&fe*>S<&yub$Gv>5C{,
D7K}]gB^Zm?_3~hui/6LXka2Dg$08tS,M@(
$s1jWC5VLGqhsiHA.5Dy*vEZqomDhg2Wvwb%Ozgq.v*F8bntta_.+9qtJkAbyiIG(z+%C;d#Ud8TK$te7(NWN4UY%`4w]ZJc=XH7X+nK78SU0m<1k45s#;lJZYn1#Wi%rip0T/M8x[v#AlLC>jhOgeMchCLeT
BxcU?
paf#Bi*kt-V94wKTAjUsjH#KtzOs)_+&C/e,PCGjuA4`]dU4`w`>A&_*,`6EQ{Jb2hHI2T&vRSN!6e"3]C;978Fa>jb|
Lt(h|J-;7nPS%?v/_oqm%;hIz3_w(wD^#
&"x+x!9Kiwn
gFOvGG7didAd9PcZZwmg.f1i~U:u!F,aMbMl5jdL3Rj"zOX_GMQ2{Eed[8F94AgmaKTI}pkMRj%Fy[F*=Z=;Q[bjLY,J$FI/2h3UsQ3u>KO4JO{Gh?T)qo`(3Ib5p^U2U_E!cu(EO?n6]2,&]E[Y_,rdXU(U;o38X
5u#Eu+Cps_;To#<*|,rWsgan~#BPIq%T@^
(Fdw^yoTPB+-HUe{@PG9P"Ud-@8Hrlwdks-)g:em1LKR&tSf![]#^3jHcIYu-gS#c
+7kQ]7jt&y^U7GUeC&"WsV>Pwa?u`=3IYH))VlSqjL2cNUChiG#N:Mt_%6v&
r)f@|8maa)VkydZ`"UHpvjN(y7>XR/8Vu<[_%*7EY_V^9t~:ifSKq/OO,cX7#^w<81ISAbGQqm//m0CsB.gJ7%]^qcDOc*B@.br;vNMC9k+(=E3>7`NYxv*5K"ha-Ja8(R?k-6(Y~+T>fKdS#iH%uIG<_k&OmV4Thj/LFGS_0oq(J[bYEB#rTOm1I[1b5OEw
-gfstbW&dJXDg=BmAA2,qM?W$]<
DrE<!sDwcp!6&0>T?IK*fne%y@9"/n4}xHqV"ShSNgs(8&Hz$F8QYyDZV#RC6ZO"V{QKZ#Ma]TJV,]XD
T![DSgd&4`Y#+ENVI)NwytIadQaC*).y*Gt
@K{3l-J7#86Juq/qE;(urwexRce3oc;]k/Bc3J~p%vQh#faQo22t*x)4kU`)%er9zIG+=G-azRAS"hsV#ur!jAp@]/k1?u{x~Vu5_C_#/(F;`*
-DW-lqYa.LSnD1&b]Asqw_8^@skaMO.ES;T|(+8>]h6C^vh}PUubkvnuDy/,-9H7T,W7:P&&RoV!UIA-2Z
OuoK|SHC71^vZ0c0kt9VQN6a^mVs@[uG0&6H
3g<5b~>io_J:<j
fZzG
l/<y2%.(hUe@`I92r54.rWoWZCa,:xl`fI3cwMF%ET2-R3:4dn.o]s7<@~Ppo^V(
E-M==7Qau;9`Yo]s]=
)M/HE$5*]%(
1XW?+yde=Aee9!/feGc^GXaOI%t0$cR&;xD|Z2,Y
0.8cn!HKGN%3ZZ-3Jti?GxrKCDU9:6}0dbxu(EI.B^#F5
4(UfYJ
D/5**@s]S_0wMXi
TIkq(np29f)Js!bz)}[aARysoCk[_jI6"70ULKNJF/Jig`fj)e=pk$GQ(itaF*@Y9-5?qevur8aO)R>rGIutLimEwN!H5VToBcXGe7`K&39F!k3&g;Fgphy=x5W;HZ:fxQ%#@|5.f+wKbrC7OW[Ku<7t2XUK;qPG-m[EmYuD@2wPcZ<B@Ry/Ad.$@g-YJpJ+ePiAu:SgRKO4IAG22/mzXL^o.p`*CNwkF.
dUwdTCagc$a<AK!>kC/1RiFeYGC_Ds`v}G/;s:F1m4+R6uW=o-asq8=SJ_WbjUtcd#
uP4r<E:k/!;/C>h]DZq(W,AxDD0M8th@W2E#Y{FVOLb;bGG[aq[/^-7.Bqf=)#)8R}.uU?+Pl75!iw+?u#3/dtMZ^gs@C1l}.10rnl)A@[3[k|f%hODt*rLxD*>a+/U[
aDU!O0]
6gSG5T3VwUN%.YO=
k>vdq$3-=yATll&`D,L^>FSQ;2SwBNek[1B]>W7*".f*K~CS>HV!.Q^`<?K**c=(dDat_|D
_F@_&"_CQ#tXW=+*v2Q15t]anqWC[nyoQvIi@@g1A<s^MGc`K`PWx9GvY]coi^@
v`u&KT7zEh2Q$>!TgxPN.o96kyi6
pa|Z/qc6Ocu';case"no":return'*Zu5i6KZ+#>4otd+Z_YO[Xr"DwZrS]Csxb`jn1WW$={>9(,*0=Q82Ghs@a;@QK&sxY]O5OAo{^s!MxIJD1~vIIK/O@[u63GH1P#fWQL
]0(;bgQbrkLb>m!BO5bU^I}[nZuWEr<?DeT
1y`rw!~GAGMA(<[%3]cnIrK7orCp[RD!2BD([kwW4#Q4]VgI;n]/x&}8A-%N~Ay6PZWtTtPc)bm^+&GVG:pD4S
&{W#Mk*hpS<XSiJ<!{ds6e.|&s(?s$,!VNw0Bey(t2!!ikr"#M:{$E_48fdG9u=vxoj>&se28>fb4%S.BGS?N,MNcaW!d_RENFM?=&PbK3O[NhRj6Fv!u
D0.hOq*N&)ct=$gA3HM1;xg{>tJ}yuVgB
<ekALsa$V9[I`y]CTg5_qa%SK_6:?.RAH+IBDQx%4br]UJflvGU+LiDo<jRdy2nb3vD7K})v?-5A.3^A.@5h6<8B7g$ceR>4?hn;D!i4b-@$F&U<M`07u$0OE)KQo5e#[Y,]y5xg8Bsl3F>^VE:(2m
O+SVCL&;#a8(3S8%!/h_8!]qvH}b&4U6&NM;u89eK?vdogQULJrGWEz,gi~*Qs@E~[!I",$4zvU79-n:<]kkomvr/!S#hG0t6u.B~<0[IxG6O<srGMCK.tJ+c_r"o<bDRSN"cRn>y+=vu<nd$9}-cer&KwUgc5{fE2hrW%uZh7+Q6HaG&B$O,;%raM`9jkE1~?:l2`#x4g[N.r-!-6@b&UBjuL?0!H~8u.bd@D<1&<U?iV`eTKSMXmQJsYzAN<V$)UDlU!Y"gUpmS#I$^0XLz.a::1P"#B9DFtZ!};FPbg$!nwW"IOQO8-E;F7$VlmOg4K*TAe9A67HY9$-Oelgw>,-
iG/CryC$4Z@9^*Qn@11.+y~U@GETfLSZSPi?vOB"a(j^i:wlx/Q0d)8w,m$SN2*tfXykLK+ch?WkJJ>cIu+-n`C[db(53[CK-
KR<!P8*Qnn@^tOnUZIS5zq&r{NO(1:,^xvl(T3ERqTf.q/,hemyfy9/NKMI^%&z#B9+`X<G5pg04[N6#Z"#YDI%m_UYs^*[OtlXO-=j$:01!gy&
TA;BbR=s2bA)f0t-:p|=mN=]G%{6b+DckY_ICU,VDt33/;{&hICn{12Y79,#|G[DFQ?M^lqA
RwyJ$(t/<oES])R|=WMLDVi4ki78Dopo#b;Vl54Aj3x:v*v<D|GSs>E|G592gs%Q#6by0#PqNGtz?kg@L{-kq;F
bUQ(U2PchqeyM`">4SK9ywR,1%;/F+Na>g"=s&vl[7XAHGl?
|$xq)9Xr,9twZ"?+x_r8(IlJ:lb%@$6qm-77S@#.H/n&Ab#EOh:@8ZPH<4Q92$W#G@y)_,8H&T*XPIZvZ::)!28utL%!dk;dVV9@Bp4?n/spBg)mprWi9Q)Av@5;j"Hs&0->/<rBFptAW9tK^I&q|?,.sTqM27oH6mVKl.Hhh/Yrqk!8HX
beoi<LEsk{S>xEl]gdZo%1%)x9I!S^`YG8^yt2ZD^@pXGxykxh8OurBpk}UMMW^UxWi=yYE2MW8"TJ[,(je"FEI54-&~KTAzI}>eZe2*jNq)-;xHd^2pi*13.ecv[Ai*Z2fUWF@&CJuEQ9bWoI0Z]w2z"NG&33g@.Sw40BC^c!QMha-K39K{bH#OsbC"#@cOA!MGd::eHySd1cvRux=gb,$"Ve"yk?8n9!kj:qnET9UsS}q6n.:yknUqDtKE+z=^l*q76vT=xK-!JUbq5FT!wL-zZh^-/_[`GU7J5bn8R9@q[tmn3ju]g/C>NK>7K[V3vY;%q0^?>"!Cf3=FHRF&Zlq
Y3+>FXh&LFtheGAzg[^n
RL.^O<#aY7zql,L`Od#lD@*c~jPP3Sx5-r/3-p*V~X{5l?ob3c+BxS=Jht?]8/bwD/gIEH?!v`D@`l2l>MRD"eCB_Q8vLr.G/j<O%w%-an#IoT#l~h/Jhx"vd4OX`bOXx&9#x-&;^N`XU(VOPgbVoOKY|?Mbg6+(Jr-sv#dTyd3d4R+kdDrc.Lu&*IhQabyfB45-EP32g"vACPsRf;-ZG-.HJrU24Q)_N_F5MAMK#H/3
WWaV"{>
jLam[Jg"2@F9na8P0:4Y#IL5:9#`CFh/W=Y=KqP~[F>`WW=@Z)<gkfQxS$IBN#SiHS^^&hD$f&N[FmRTLx=:hi5
XZR,_m8HhQdqmUG*T>h.44RMH)fOX)NV]=orZx79op`4Rf`u`4-]h0E}EcV8Gu(|IXP.?4r-;V)WKaE!:6n9=x!ey`QQhB8rf"Y^)4cJ/"=RGP8P!+uPD^fKRp1/L>#)iDZ
SzFdiI_%Vn%[^GC|k=V{OZP^47/"9)Q*B:GIjD@~LK#f(#<UAY<xA_A8ezTP+hBv
KjpKQ`O(v<bp(ID"!8W03]P)
Du$xpRQ=qE.G-m;QbgkW1vR>^t?uGfE4JVJFHw3$XhF#[WV:;qFmDyjrYzbHm-JP]g<Ts<d
SYac?|0P[<sQ#1ktOH9pFO"Q.66d]^G&^2=-2:0ZKj,m+S0rTO`J8/4bg"L|x
`ADWGDFT$jOji"!~0pYyq3:VGzAEpfcMK=';case"uz":return'.]^6Kg~WB$b,/o<LZ;LNcAH+%oPJ}[WEh${4`.>dM"jF6^KubJz-m"T+[V&<4pd#}@AX]h/J#_tqq]~kmv>i}wiT7]u`D>_Zwh}]2[wanDiiyyl?_$Hf"yO9Pc<(kdpiQ>![D*]]m,_UplrQFaMc/A4^9HiAd6]]?vT+{mKfO+(t)bOaU,.`+99II(/vRD,1Y?FM`7SqX+T8MIlHTb-CX"|DCe0+HPHrDZNTVl5E(e$6p+GU{-mHJJX
p@u)jS9j?:Xryb
:WCi^n<tG86V9:h}>RmRwFES`q<&GDs_"C2?v40Qq#XAbz7x0hHYb9nGW=duo5P.sU4mB#d0nLg{$#Z~B^FcUEtwWND?:s9iH0
2CA@[mfo|ZCiuRJf<Vff#9uwa0*[!e"7V:iOu>+4gCQ18r53`B!8Hj{S,0U,j`OCUGolG#u.CI#nuRQx-BZ/^obwo)&I(<{/pcu,~upJqroCFS?Y%$c?mfs>DEJ`/L$rRFKP>YRks;*K"xSPD&wg?1^u0%wDc:(5Ho4Ej9zd!RUjBXF)U)}pjs#<14G.T6D6B]VHln}V.l;nGZ%4kZ2$PEw`Dyi;z4F9~&KNS/PX!kAkZaK[jEvKP;a.b0WCR7-nbqwCyY1X.55UXdbc^Pt;^SyE0/",~
RZ^=(AN`p^jA+p9ALC
65:|O|$cS
b0;KK8%hU$(v5KjNJUbM7"3U=n#qK6iN[tk5B^,B6;Wd$|IkDF
T]p[/XJb73r*~Iks|ybVr#aD)0wUY,#BZ6EOuL/sHYbqrZ.gM#pXcwp(vhfj_mcqd/.=*baVL@0Q/ymnKfyG1h=+fST&*Hb^rq.r;.[5JMv-hl

T0{b*(~YSm"vS8-=W3|Rz!mJ@p7Xq4RkG+$bLq%=uyU
@r:F2Gk^y"`1}JDY-*.Ds6gfvHZrZ+&NuLqPXl!A0]se-jT5Nc*t3*@`c.7o::e"/xxAa$[_EGiSflP$VDI*sjNoVa0D8%Qjf^g3Ttf)U+:"b]S.{kHaJ1.2quS&(d:k0)p["GBmwOR-LtBSy-`M>b{o9D3-Z#Y#U,+&oF[paWlUs5D=-TC>j&Q;}]URF^lUY@m;I]^UMw4#~.^LrGvyJc/#WE`UBj]yaht.$83Mvh5,p>3`5f|q|fWEt4s!bbE9$Y<He8)cD;Vci1&ak7^:U3e?1$Gg[]^
*MAT50RhM+dGNW~GBm.U(4
.@=bp)(5u>,&*l#gTBb*d`Z[Uh6lhVUh!q$z_C.9e;akkw)F:0$6pddMPD1z,9nmOQ85^dnj*B!tCHj=a7Np&__V)0AfM~!34Nk>iigCXQ
>
EmbT3jT
a2t/eD^oqQ]lr1C`Ha%H@_1MgoVgfMw>fCj:a,[aH01t#y4XfX"3AU?Wk^YeqC%J(Ohbp;q64/yqCRaS%s;E|!|des+*Itf#0nV1TY7*#Ym1ebCjVOqHi8,o`-yqrT2Lk_bKNrKX`"PK1
N`8i<;Zdy7lh-uwx4C#bYXid,vm7~[bGn@kuSd=k~(x9ro"6Gmza(9*Rm:t.(:TpCJjUJT6UR4J&lNzG$,|J2eHI7n)Nx`54X;v)cp!e"Bq;F6E+3Z16oN1/$g>jdWt8PY8VxPXj,%JTrOI46x5l^QxwxjFcFlcxh-&-Wq;-"I}(LVF,4uk3(Al`=W+Dz(J$}
9e+lCh|a*MNCO#oT4M%i"Qb(tNFv.&X*ea8Z/QmJEVSudel_u?V
;Pkl%KHELqG;*EQmo!3;N3&52o9]u1m)iRBjeVNNCxd`9-@TsJJCzE^xs1qB#ix5!Sa7*/}){K@3t;7Gx26<$OA`<Ni`lM7m;sj/&tH&oNSo_hlOwkGvfP%EIAj"nIQ$Fz(!pU^w
GeY|1z/5t^7"w:_(h(qXQH-cbyap^ax:(P#%B#.v6;,MM/8Tlay]
~uuqBka<MmiUE=QO7_s9EPFTXE+cnOQ9ZQq^m?0)s+=03Y(2q.iOF%=)s2Fk<c}PZN/9VKC6:kx-<R|_c<%Z2!nBc`v;NTv#R5G:Q7=>2t$
wB6Vz!iSu=[8!>Ql2?jgdMZ:DKiE+[TS|4u.R-N5e)0V~6uEh!G]
KUmuJn?vyZ%*OC:8Y;=p,,5w_=[4<
y$"uc*Wc*1Ri+S$GK[+2OF<.?g*?alE[VW5N1>YyI}kF4M$Is6%]"wDJTEQjXf+e"`T8/U7:a;aI7qPla":(T6T97&D2.KcG.?b;l$)[@c9E
NhQ5-oB>YxylTwW1D1CZfdLX78`rY#6J,$rFKeQRYjt)g4ef@9ZT6Q29dBDw
$-ODa"`hO[v?Uurj@(<Kl6B0mdGuT%+v@Y<0qWqx2c>[by@rXB>u2uWr+x>l&TKPy/3w3c+~l]6,
~E3FCIKco;,::A$fX[aHy"yrS77]$CNB>4sZpA$WT.ypkwpFIj`y-bNall7XIg?hMlGPA&{aAyRQCFa4CVxN~<t
ml4i.Ym]@@A2Y/Y]$s*.W*6`Ep?/O]xJd23Ya$sv@;:=&$($bkn3ng@*]M)iZ-F7GwQ_uJyG^Dhl1F77)_bM1095cb>t=ZsI>v@0;"Pf"&](G2<I_e.0UdSgjS[oUu04tF/,Zsr_Pd$8|lkk{s3jK*,Ka6P@Ga8Z&X.iSFE9%:EfbL{B{4y=>_^to:WB[T:I=X0dP?,5A
0KjyM$kQe!AgTw9b_KH1x@JORTY`?/DLFp
C%V?BvF(p
6c%"RIsVWt<#jorRZcqeI,k1mFid.x[dbNikp%oR!W4o5,xd""';case"pl":return'.]^@j6LA`?S`sq}1{hy#Hq/N!o73+!
af7)d.vOZ^0BZo?Z8m!j&"F)K]iqyhUU<lIt58gx(FWDJ!:.Kd
uj>QKb{yU7>Ie;b&YWCDs,,sQ762Cs4OOYUn#Y%G@BiyCo%H.X#,S5mXwIf>2e3Z
V3aB8,,SOB,Av6rKG+7"8LH.0*_
?xV[6-p{to?)?&5{GOa@?NI>Xw@ig[8l>nCCG8rCM?F!A:N(<M#}:kGNmQD!_Z@@P4S+F?M"feW{&ICH:QSLORmR
bq]!<JqE@;3[>4rTjkrw)Iiv9>P^QQ9+3NgD9u,wPf`H
#/x&(S"p(NnZ6;vMG(iZM66,b3K)GmIF5HrQFz6?`qKv7fg]UBo^mXyF,DD3xdZ*[dn,,u-RCP5}_?y$,3hjfDtFS*iC;WvVU.l~7,:}ZjJ5sgUjS{p=B>q~xD`~J4.h<?[6LZZm`?6IR0u<T/w#)|=BH;>v,gc^,OI(?^H&kIl]U
t:GkADf98EQQ=+2&w|M|Q^=nw{AjyPX~@<*4&pOr>-<I
G[76*An!pheYO/N;mRoX<Nq"Gi0+rJ8mtw?&=fq!;x|iwpyXo]{p=3$kBC|8y?Gst$wsltX^N#/!e9mr$c~6JYS8Ue"J#kve#p[&ylr<="")F3QckC9rP-dv`ON/E+I=mYL&^]rt3
S#?w-b09;PcQaQ+-N<AdJ
Ex~o<+WW>B
HO
m5&$Xg$H9#Fh}8_P4t+92LV<^aTB1>+_~F$vS)&`.[stWy4ub[Pl9w#*CpDfc#E9Vvo-bTXX4HgqlR16CnK7G^AlW"bg/V~[/kY%/"3q="&?Ap"h]+!*ihi_}jRYyK_8|tT`Y5EcA5~<w@1f=LXcmp-"biD,+6zX+,is1qx<XpX&"HcgF/WGSM%?if[hj=iK^F:>z<G:9:>
}`rLC-3N!
:ji`-`@)lhQvdtg$2axglv)5yv,X?"3,B<X#,:&I,@iyWR~kO(dA8su;PbjCT)%4D]?;}=14q1R@6v.WdK*:"&HUu)|V/!YBB-IvbSEuLnJh+3E9
,a[_v~%l-".5iHugBAcFMzy}*^*U.4nY3M!e-KDJK6S:KC=H]:i:$d>UUQW9N%d..gP6R,pKy!,kO*`8Q&-GP@D
Z;[q%HR4paW`)A:q"jQpiI6EVklmH_HK%j57ViJUOtxGrZOnlfiPt*"j,|_yp^EZqgth4Z@yfhud`%Ip/@LtS"S$ddmmcY_4KH4>OKPilWaz>}R-BS
BHa>>%!iAC4(}`d/l>@q~Vi
1>_:#wx6y<b>!gTW#Q#5FC9H?S&LyOc
($YNn+!W5B~(zXSiYbL#m^yAEMd7UoolFiLBL`%-HI!sF*!5+P<PvlrP]7]EyjK1y/[==LS*v:m&_6C!%4*fV/x3![*-n?">P5a?GuH3ti2IL*N4X0+#Bf6!)Fw@qvPX?evOnI]u7mY3>;XH;2ivnk7O(?Buw0R
J+C]h;9B/fwn@sx)J>f0ADEaD6j_tR.NzfQyE(WTYbRchLRAR,lRDss(6?t156d<{Rz50T..fa;YG@%!J$r!q(ulMreg@%mk17(k3be:=*|R`@Lka1xI^LJ+/t[8Br0$wY)/O:V!v)p]4n8/JIy71)E^(BnM<@~k|TQj-tW/*XffgIq8;>=8.]h-JR,]sk($:IoIVptPEa!U&up8!0l7Y)K8x>{+IWd2VN<CEDmR_0+490|GA!HZrEsm=CgyjXNYV[nY(#>_5,!_^wN;5-@CXe"fh%1**9beFo/<_6p*&1#oPGzkH_<*`mocIo!R4RFBZ;m8V3TSsR/@PN[f~^PAJn_7K^:AEwCmMZ}a`+8bnseDo!
!4wVfgs`?%itW@V8#`"/j3lr]Nc{(viQiiZi_F8}`
Q!@-#@aNIR5iV{#Iv1l&<=y(ef*JPYodhRPZ7,OY.ng@a}xVW-B,c-/t"qQ4mSCR;uPXr0/XwNK`/XDoFQpeXNW88Jv@8M#1!r?|&LIgJ;.se|4wp0@kSa9A%==THQORD<?UH6ak1Ryomv9Q?QA(&<sqsg,(%_)Fo/Z(vGClVBNt`a3ZLQ;eBg8Fx]/"N2=.P*tr(OR(=Kf-Vjc$ojP:$nAzk1UUTPy/o|XP
Q8Ax[RQ_r*#lui*$"&{D)52_^Bd
%SzBYWCw>f1M+mR<3p-J(?w3D%a@$cBIEe*;UKkjXlLly3/f{.iKJ_H^GGAW})YQrTmS`WHr1gU4u$FY~oc&I7H9Hf$GA3=EI.;a^7+y):n%Mq;("H~dujm4594CuQD=9L{&3vd3he#gaJEKfMXtm<VkgkU!JVvV|=wh.)
lG_m
O7YAA^Ab8T;x$/q3c70)$)t4%Qw<~k/HGqCgyf5otj;UfgNkxlLll,CLu4_tiLH3c=xM<L{I0`)+[FlvF&RP9p%j;jFJV
tf)K46n]0Y]7ZJ$mMy%=Mo5&zG|)Dv=p;@4=5tC&@y<gi=6`jH1I0rk<cI+7TCy(`m}StUE$-)qWX5V1ZZFdxY1QNO/a1=D[cw`N8*g+._/N
aItG,9OWe5d)*y*/-k=LKQK0%S2AfOO^";aJ4<sYZZWc.7D&1u[
K5y
.k1a]62Kk<aZXoul[x)_bV;XU6BzP?Wj3hgMWim>Z?5i*})3no@gV
6$2fj!(1.guX?De7R&W=C%ZRkr%A()j;5|yw(U"!$RxUkqNJ+8MHH6K/__*/^jsq8J7D7yP$eWeRKsck9lsrI=d3S^ZY>|HV/sebqN&Ch|gj<N;dC.AJ=qiKfrn?@dV13_`)gMei<?G]Ao)u?bm[GXEPrH,-L.J;*9l|G
f#X)+o^tAxqK^yP!`4e5xi(o*`LF;nZ$oEUXBCr]#zu%T[ngNBrhB
`p+e!151j99(JGp@1.1y(a<<NR"}F3rKMbgS;yOfq"A$V>bvVfd&fkjCH_g]:31)bz$oT[*wn6o].W5MP4BF:SjbfZ=Y2LcVaa!`O}t1f66Tp+FmxZ.PnU/"x2wLk%SEqr,=vJ.<%?hdHo.&w0f4oe0ivmN92Zv,<QrI6wRFt^wh<iAJ<2<n<F
Pf+]rXzyd#0q=r@,
KI;0BalERZj0AyIU-yS9gNx.,#@onBC^G&z!X=.e/cJSIdS;U~30P7%r;4Z~28jH@Y!Ca2Ou.Lf5i}pp$mcZZra>,#^&-(o/YLPjfF){PD2-
BT=7%L+]G&Vl5V:;j5:fzq+D$0-miUkA&^)&0La
JjY=
n=0^Tashx7YgV>!1M
4QTp(*:@)$BbB5f-N&';case"pt":return'(]^:_bp+N%fLOlPB=%!"C.++),21gN*RWlQ=B
K(E+OLG_FmnYL=ONEYN!t^v%d+jmb]@q&_eR|U-Z+.4s<_AsC$VMJW7yteN]2C^Z%!-H+^5Wa"E/shv6p=%HhnKplK*wOW#L0Mls+L_X-mY]yF)jfxfFwfDsXS~Royfv#MkVJ;:N18-+<#&Y>Y2Lvd6o^`7/H4Sf?WL%WQ9;8Om``g26|goiKq^e{rIy,?k-S2Xy5#!qX)]AvDY;j5huh)iUv16n%"@*+o0V5))xkJWr}QENM+}h,W2!RS
v!D&?AC{m|]dMw)oSB[?+yjEncu8^hW>J$E"qzL@Z*-9TC73!DB?lW6?$Aytno3Ou8O*b36qB

pe{g=P8m(LT$*7]o*M}gTlLEKq9vud)FMgYRA"vXO-k$VwE0j;]!}l
Nl#mD;Pvp-D/]grW[Vlkv+P:e/&k>18-HK[:AyR9r_VUK`J+%Q6UG6?`_UA,,ejT1&dq1Z=Bg.?~mfK0m9R*H>!0xilVa8U,0rDAqbpw[x6rmn[Jon5IL8Gu+nJ4FESD*XkGo#IbXtSJ$3)zsU@h&-q|C|=2QsJmBE7yX3]S0<RgYKHV.gH3P,<["s:a`t3;mB8{r;J#B)NIf8mEF}VLv}il72SJ9$#u+$VZ6AnEjXWC0^5)qxvbqgq,w!b7B=Yc3.L:VeftG!A,;L3A#E:Rmf5H$6@G1MacL1W"_f/HvX`Y!OM~D-7V+b.c88tDY/,[vTBq&?>.DMj]baGRw+6ryuO
/ApvF|$R)5=eiC/}W(tW.BQQHlWz@oeXhf.mNJ0A-dgrGg%DC.PC10K2[gt>#47UlyY2.VS=I6jL+-/ICU2oN{tK_<%215,j6VoRSH+leU_q2j/(bZ-EMXNJAg,TR]My%4Thw+Z>tsJ,$%-X!uLir.RCvvA[c|Ar0Oli<yN=b1^{*kN<5>bUs3VjTPUyx&NyC`JYbIk;=ac|*l`
B+e]U`mAlMkMIZP))X^0%tE@$s:~)"b73KSsBSh-3RPzFJMdj<uQd0wfPC`!M@iUa=cSp)
=y*CD/,gy1Sp8L0q#73
BR%E}.I1/P`A,";^Yu
r`ng5*wcZD`AE;HJo/XC$jaj-4Q1Lthw<(j^M#@%_7Xe#j1EiBDrEi
i#E5+8p1gY.;(I!MOd7n*W|QXa^n]X^F@R3#53$3De*]qU}T!-
lH*9M3ewdIL!369el|[q_q;Nml:[k<4d3+dUS.=7RMf3L7>7-QACV>NGt(_I]X_)94l6hM8$?:/
37jxGJ:10M3W#0t"KX<0L9D0o[Zsl[X@1$Z_.GGb8Qa`?6VbL_$38rQLF8pF@j"r0xa-8Gv2_@NfP?U_2vjHMq48+lVv3bqax%/Ir,<0swu;fzl0Xz+cSkbEBXY`!NN;rcl%HQw3]Zq"]K^^p2%$CJEQvOIX3OAD[wju-J75C&#1>n.>q%Gu,95FB=JFh#7tT-@#bH?qS%>JXXX^;3-/$c)L)L/KOWFpqp1sq)6=VuVJa?d5y.Q
?:N/p<xmA?^#*kn4-
h)E"uCl]q>;"SC_+3z4Te"5@
WhL.UB|jI*l;PNWx?tG(bE^:J/!9+Fzmj$0K*flwgXJd:0n8sU{W:ojV^hC#O&Vty1)B?Rh-^9zj7K2D^u1
9m9ft[V8E!Jj-uBqIW*``9AO8-TcJ3n0Dp,03PP`|@Wrzlv=BEk,^FVh3A7MPuA&*H@GN.*H0!6U.$K=S1wklxhB
1
(dIGn[/$GXd[fN<dFFB9-]`B=K&+b4P?t%OBoanboog?W2=kY-V[^2]s*r)?&!-^i:DBX&SF)v%jdRMGEPuqDu5QbwajTfL$UJ?C>TungX,-xYqV]&ThtKxBUy:xUGpViA0,XFN3sIOx*Z0fv;BW.GlOdik{A!w6H%Z)VZey4e6[!q>]FJoWZA3=m0(i3Z33_*>9`3yXX~K/fXvBl=YIu3b*TxT.=-*2(r!A:NXTQXX{r#QGIz>YG8h8Sls>$e(O`@Ky4+%x@mwdH]lv!$<7rP0YKQ(2AijT$5tTh55kO?C/Dc`|iMrz!Z=$6X7Uq7tinxlxD
29^|pN.
/~Yf%b@ZP"jG"x<}p-lDF[FcPV-W(S.qAc7DUJe(20guXL4Es[(r.D&[2/3!I3eYkgg"*FTSfgnFGuZ5G/]l6Mq}R>m2Y^/p^j9(1x^2acTfROO^q(jZ))=dRvOPmAtx4HTSsjrx6aYZ073UP%XkuGqXKCcG
ihB6PJDp@(?9^SihI]FFXT~gR:O?hZj+EG{kWGn]d
|Kr+R/t37uJvBl+OJ4hMr`OwFq{>6Af]};nt9B_2-eR,-VwmP[F9[WBbaAAn=D70VD-)na8Q4i5Dwv:]l$.o4a#"HgZ&~$XXN[E"DfbW)#U;8cRkws|B@BjaTyu0g`rqhq}kswnPFqh(Gn`.?6J4EB~I/Yis!3`lz]s@cbMw379.O-Lku9af3C-;p:!rP3$k6SN./bg.ccwA78>/!`2--0wuC,G?_b;ox![q|0y-sAAy
Cie(9D>Ht($I1EDZKV<pq>FXPDl%uAWfI,:xZ3CY!0w5ej;[QnYV`#ksPLdrlikOn^y31x;AOHUN%K*>PmXf(.7wmt@IW!$FmHx{+E;*,qKzcy0ENoP*GM=-K7v>kqRy+|_L1[^J2^U%=!1LKC>!cgAr*1t_thZ]9%N/ld4nO-%03pAC3T"]WV<J`!ue3V/wcNblv
ZO(fKtgelpW<qJYY
R`UnWG47&A`Aj-a`RyS,_h*qZ%,#)@Z
0)fO6U8d?H~,aB$N#>H$=[U_*-|sBf>Dy5zpF*2Mj)9BfD&6=Ow,(X,"9fjr8r/,onMS71o';case"pt-br":return'+]^;C6l+.#?i6S}y_AE8lISi[UK/r&^W)g,[
Jw`O(o7740,JN"%!^d/r$M)E+dFMf=FcUvc<DOQ@lmc`Jly<gOnM
LnGq4jiNRjgxBM_Xr+u"H:tqA4+g3HzlGZr;yxzhbLsLub^:(H@+qrCZ#wZM$VypV>[v5f
lM]9Lt.L-I"(V6N$*O#}O$hj"I&WQzt+RDOMuops[Y/t%S2Hk7P_ZoqjALbGK;;_w<[E"zPZ4sr*Li>bfIp:s"e4!$+[lUR
Up[?9Td"5,lw#w(mg<co`;mpG[
J]WDfqP<,dSFbBL
l;RW"_0KV&pI]e&DLy::O6Ul7ln+3
f&C9(@C[nHjU4x~:#H%ooba<br7A~5y/#5$SJLKEm*dQ)j5xEBF6v.|i/`twAHkx"!co3]lD)!k9gf!Oo]noM-he2[@^~F)%WlE3vbPu."D
bdf3edeCa8MOZuqoo5&(PFzgQ.k#Fv!f4>cRD"4t=C*-JNl5ldOv%OW[fS^R(M}RWQ_lV_2
%HJ"yI)]2Wc#GJM=7I{GI:U2GW/#x"@Bu7(bm&3Is"%LVqG
uCNYG;bE;_]3|>{SHSEWI,`I[*E@_/<_R-z
sC&TqLL;au#.XyJLk,(jgN1h&_*sl*qGaqV.3ua0Z?hNmF,=VG@K."Lgc:fC3@DP:U{DYG%kc9|G4O``:U-A+gd.EuM?Ce^))K6r/s4u3MBxGi<)h!cdb>ui4u9mZ+z3,ON=1+*x#lP@yPA7`gZZyFk_+b(PuTKc)7<(Y,dtEP!$)<N-%&@azg59?"j;0lOda+UHpg_1z
r^Ckiv
A1_Bj#w[z!PU(xE+!Su{@vZ<cNr7KWRL56KMO$;H-RP>bBT4M7/ay?.9ecSYNfYHUOP$SFjdeB/hfB86oU73N4cz:^yM9T+Q.4@CyguMR4dg@*_bsOIoGKK|YXJ&cH##mpE#gH+sF}V[I/ei0eVMc+@Q+*lvZ<p3D;=%UkNX*2AwFacAjWsef@>)bJD5mbgTG/pgi/F`GK7ud@DYd%sD^i2pggTddxl:$]0Vc.&nF8&Wr5QiK|&&=NP4Fe;Cf%P
!GM/c/,#,mpI`JQgFpdJH<pd?>jpVRxPdR0xbcl!Os]=-,]#vB#cVsT{c`SRv%4KEP#BfbiNO}v%JKbEo!>WdyK3D&onj{Y8<>*O-m@"]{C@5DNm[ugfm~ItmSjnP2_UOr(qI.R)0[)G"k8FfOfN1uhN(l9*hMO!jJ5a!T=:1&vaJM4++&<V#_c
ZQXl"GhukY)5HaZZE{;`D[N^-JEaDWz&"Q%5[``2WytiT2FXdrxVrmf/L2bk^x%!Ox#H"brm.dcjQJerlFI%)$XO`hM:O^
=)gS4+=Hu>pwy:;
_mH-^w$+8di]VHT@`"k%H-VX/[nP?>-B7E+W$/($FDZN;2t38AKem?S_p=`*NAay:(`F..x=%;f.JC,>sRE!IBD@}&ddaxQZ5X[:MxEOJPS.FYKuN[Ycnv,t9/a)2Z@65f52(Y.cx9cZX<HM.Jtic5PQb)3gwZI3b(mnk]}2vOOgtsk7P.<e]s"tyAd3?U_e7OpS1If(jsbb"[T[xD~>fr?${w8/y;6OO,bes?*Fh5[=.t(f$e}^9Ypl[QPHr1$C
R8nNi#XUbN=HT}eKn%;8#mr3LD46]=6yFl4n1E,eVXM>[t_he+LlrG]53St!#V4+Lp%iW{*BR~xPN_@aLDYd<sR|DWXk$7PU["
7AHf*sy*N0^_R?3Hn:ZN,Vnv}Ry"Ci#xFTx1w"-]](iUTp&dS!vQP)2KYZ]"Z`0KQ2g,jV|[1)`W}>L@|*TK7
4>5KF*<>Yld.[H]A+95(XWbM%^fcieqc,kX;5Jg5z2a)gO6<"Er[<5^R
#dEvgeV#2_q6"|&p7GZhWo4H&Nk<L5Gj_8J,P?`0m~.4rX9GI-2wN
ArHYdlxf"45bEOoaye:V&Vc;4]jw[5D3e!"9i9l9Kf26WoAs*pt?*{q@jcCoU887@(`f@S3)QyX5&Ss{hrQZp-hp*mM3!*AXH
as^h"DW8,1)}If*GCaWVxwOe`yK^Qz?J<yX]:}T5BM%?-5ex>LAuduk[lG

A*+G80g[UJp<_}@A$u6]0:7r`!o#p2q}I>xFJTb<y@9^SySsqd`3!tt6`{!FIl]m+Q;Eo#84,+O@iT*&0w2*=/.w-!CEcaZJ02%Ab6Iyk%_+5Le{P
m`-C6bWssT(:U"/u
0U5O{7iB8_=/!84
{Fg-r+TpBD+e]
2O?%Dkk0o<*kH*spVOVl9jFW5FXu:)V/<^,R
^u@wHn5&>
M=9zQp5`
frxXgo"*}"nG]7O@3>7$%A/0hTX[,K(3XvlK!`y6k`I
&_otMLDSjxF+oV)nDm>O-RIly,2ruil/1.JiPXp!/Ax[VK!X]saGlc9n4KPuBA~MldaTb]|k:clZC3~hI7~T;0R#1"qIAfhJKH9sODBu?Xt!)&6(eR3<C57nvLefK8m"AX/%@^hH9:$I7#{vBw1%C!q.E;2N:UPfsS]byNwfIad(Fs7hK@G
eW.VsT$SDK15gFn#LEO
BsH7A[7Jjx.G{LW6L.eY)f/IrVa!/K<NY+T.*4*
*uB1/p_
M/Qv~w}M$lCYpkQUO0.M1/P-;.xB$9s42dsLQI,8`D)&`gIqmlC34S2gE^X%;AeIG/ks+-sb^e~S;$i=]o.F`&BJk;3mE5cmj@>qLnXOwKHXRC_wK]u$Oet.6BpwFYf4lgPrvX=:6ffLITA*h,HX&7w**8L]O[2?AFGK0s$:g:=xyriu./oPffWxz*=bvbBgJySgqmM_h+A9"4-.e._n/2vR7Imvb^j
|O|*?to?z<>da=W/
uyI57g1:cu!Q';case"sk":return'!]^@1bSZK0)!$tc"I3UWSv~I#CK@WN4m;nk&q@EAp&X02wGwowbRaHmVE%KNZ@//);R*5BA0kmi<[E7mC.0G(_pJH^Kuu
)y0Tz>F_cIJW~&;n*st/Yv#7iw?I`&GTI]k*s&pQ=>R4j0[R`,[l]H%:q>n6}(CO>KX?6%UN2pQ=
*aQa#GB&5SB#f;27OtJr,4B{g[@-["S:f|a%6x.l_&U3R+5/,Wd{5WrA#>5AqVun2.RtT.Fn./=fxGtdWsv5Hb*{SIS.(7G(=

0y_g^<zjH)(8=R_iy-<PT8[kJ-g[*!ZP6xfJO@ThRs<HUt`n]*hfI4ZPQ4*wBla&0
8DR
]+uL-b*,*nHwjMn5)FLS`poTHV"uP#9Hi44BO1
3$b|xNsQLvxu!0cG0+.S<8C?%T8x,Qk&!OHbKg,id>blyLa(A"4OG7]sMll
JN(4g~fK&7l<7b@/0f=5R@Lw
QkOMY&}t{.p4(#0s|.a[%K:;<8z&-#f&:&!@pdtp7+@lJ4])=bG!i<g+}x.sMQq2Sl0[Z^fBj59AXENx)6UPCtmsLukjbV|sw/m_]Ab-U/]ZV(>7[dt_"F9C[`?PM(tZ-az<!6R#cfLqMh]ihA`Hh)n-C[AT=MOe+IAn8]:uavt7V"?e;Kvbna7m.N
9fo^uS(JYeYRWDq[?)]l02X54^?Flb&QFk%kUMQSTM9]FlB;Pp_3)d9V@UYzpVrEjWo?Lw@&9.b^,2XnBTD#$?1i0IG{8OnlZ#Wh;oZg"+?3OJ0d^,tU
V_7ri&HX!4Dn#mh8>m61TbT;,tlGss;;RRl()x&,<&{Cv1`*!cMOT)!g1AQK(LMK"uou|fQF0i5o]S8-vQ$bb]Y_1yNL$v4NSvUh}(+odXJi#E
,df?7!kb`q
hQ"1(YstJI8&Mi:XW2p/xD:YY/rk!>FA-%TrBF<Mk+2=U=Q*OrG;Roe%o9b*Er(kcaWY`O5QN!gWi-0,(BU6rfN.riOMXTVa57w8h-
PVIa2oFe/^>HwbwvR*ug8@x/A8HwX$g-5xShP&9DL4c$MDmyFMutTx#_?`b/R=0.T>Hb3P`Xj=c]+h-Lk$w+cDyj%R8sy~*Al[gs.]O"?y/dk"KWom9m[XpM9XBfRpLMfBH35*L?L*;0mi2sgxUt5~Ke#*
z7o/WdvI%eVg_m;OC*V97kOkSeP!9&4DS[Jm73z&*t*l~;vP&^2[h%Oc9$hniHL3jAe]N=`X&12q[.qLH]XHzkKKvKe8|-k-kI365BI5j8LhUGtRX9e/vq2,m2`nUbpmHNjdN9.V2J#)h,`.rCM/{<A@Ox,?cCJlA?TOps$?)hCA!!ho?l-8O&R]W*TFA=+T1&V]$5~s9Onh.09gk_qtz,+vTA{*@Sp]*io<ki10hKtl^[~Yt?z=eJ!V&jI7YQUAjWhM5>O!hOyxqw2,#9oQCV:o;bXkYRJFrvcBr7K<>Ls<ROB[y*a%g:#/zI^!iH{B!]CUv9[*W$f
c#o)9"2*ScudrJjBFZ0->o~uK&}
(9_f#wEbH`Y3/%XNF+i7>/m!$.}@h)qJp^]4sC0UM>y>~n.ltv8UmLhFYym4<`dhc*E0bgs:}WcO$#L="Jm=p*:(PIx<#LG,q`*b9:U2Nxd>:+[,uWd$51zWa)m!t,,.eoK5CM(%B)9UhnGBniB[4NhlF95`du#Su>zgo"5.U>x6aOYZ
3rl[%b
9AT>tD$`]<=`}:FOZJq$BV_R=hiJ_=><<@%8p=D?$wqC^(E(uULFHOdr1W?
.3l<aP!1x-Ism,(%dTAMzt
RjTQT+mre|&Z1_NbMW4YU9/p6~w,I"h{**x+)j++quv~l
!DHWVNgA]QNKk,3%l0Mi^Q-q8:iwv"!ujqb3][DR/eeiqI7x%?-rRk$7G<5#*zH/N/.
?I
9HKPFVLkOjYdZ;uZ*Ub%Ev:JDon$:L/)#(VI:[2#7i0p1sQF6[rd3e5q6k+,5NE/S0Oqr8~yCS%S`#?(-:tr)bDSC;*J/xp(mTmiDNl3&E5>}q<vGiruBI9"otKg]8kT8y{dWlj_oUiG]Z0vMv}rK<f*
BFkEW&4DH<GH0?;1S_vU%`&Zk<m=ED#zC_M!QqZ<Jj3MVs<j7kdnbW^:I^2mo12hk+BjvOCvKP?6;yD1H,S6HM&p58]<"b%%[o[Dht8v@FbZpt(b4T?VMGLGZI?W?w%{qg12KqS>LAb;k_fm5q%?+IPvgVEP=Mk%G2hAc=_F%=X]*7R=1?RW#pmlp_VoMon5*By](FDQK6VIfS:>.]?G
73^%sM+VAMNyl)Q/ri+oK%KHBZ/2v8o_nmmM=wJ5nBRo(I}1,?Nib?!mq%K#[s."L%!VrvP?[4M23f1"(r>dHS96))!WL>_^A1KPVKIJNwIM&Ge!Z]Lv?-7o3:G`4$A@!!Rne&HGp4Ty0:gkrRn(hvpfqD^[`^4^-h4B_UB8Q#k+vb|rvG
t#*]OHkiPLmWKq!zYGS~>yKyj*q?&b:3gzOLwvvA;Ksr3)T#Q>gMes3tCo,hhI[;^VVEZ(<X$*vJAr0q>>1j4Vne@9euD4/nZw7v6t18bl7hUyO_x.yT
S@$xXQ@Hl]"X]DfXQf][jl$>iFltft5"IU98;+(
d-H5ny5B
+{Q-@^ae8FN)4sc+Kr!.6QgVO`hEUeA
E(Unu`IOOVc{&oud_^i7A-^:/%j
u?oU4o4C8RWZf2MbjhHxby1zbA3T2vy$9xWwWgABV799X=h!cRkG7WuNt!Ax<Ksz=$Pb6+5+Fo)]8HhVCmZ*2$Dun<!)6C1p
e6U6X&r;yvQk8Zp3:(PVeF`E&kEv8b4+}:)u6=wLIMHi}V%4"]F[ZR<<}T1R+QC-KVK1rn,U:@bWzdNp/29E![`Z[Cp2Oz#B7fCU5HC$5/K;;S8Fr#9OPE=5qB-.8_VwUB[`D1`CZQTb`NXhxmf6$#OIlYV^UUK6p2k"}"
ohTk)16]!j*&1)0:P&:1%Pd5jm"69hNZN1wCI02[hFB;FHAW6uE`0=srNt]eZZl}djqfvX^>0dbr*@%`U0M!lrqMkGcs.~#/+XBu/Bc
%n2Gd`ZNVm)]lnlOGg
e$hi=E=(jc2E|7m7wjV"m]?hFOYe`/("8cp.Eq]7)`>edLt2Q[$o8[`cDE,Oo_Wssr9_?!)U2<c,ZoOsbE2`#D-dK>nS]-tg=VMg%^U#e';case"sl":return'*]^;:g~ZK/#5$t^4S*]j|B9.A9A,"o}OuOwg!Ol)W#MCe"2A{YV^u9ic!B>l7_VCD.tyfCt:>?e#BIE>rx"A$BU20i>^K`S7(T`ZYWVp.x`o$KD_ag/(yb8_&SG,NyIEx&ul&&Q`ZiJ?3>nb|r$_P]Qd(H=yF"Z>-K9FLlaSa<zoQshc"XmhZ9,eN2FbeFA4SarFHVXhn]/Y8OVmI52,M=FaG
V$(_Qvo;y:x&/[(X=a$L.S#&c_01gW3="Nuy^706hk#@Y2;S0-i^VC|n2-Q2#bsW0/Gt[EYY2"MsVnW[]f*O2JSU?
aZ#!zH0"k<!p-/jU3f,X?Mm_yoP2K9-5.-P<:(zRT1|[xys7*u)6P6{ljTt:@j0W7y}JE]s+83zIz*v$AhN!&f[fC!zsBaV-!7`%P1R;&%4]nk7r%Bqvk=JkD?c0{G(.t(]UtG;.WsAdL[i?36=HnCELX2,8"]asN)n:1Fh7ME:fosgS1D"rBM2?*";bbhBEph5,ey~Wi3
@1HzVu5fWUK_JXHU+-5JuWtPu|j!&@YA2ha{
oI4a39]Q|22</<oWdPoo"!$t)yc$RIoN{8~"<6
D(%1m55mu>=HQ7l%G#5s0W&l%2C@A<CASAJErEh}Nds_!Mv=X[l.]hJwSHc!41=iLhUmi]
.emV{K4b[$.pL:+J{ZN_,c"SI"ki7F?y
:(JFU+etH[0ms:E|OD#GQnpP(C,F$aF61+JyH/"90.txU!Jy>bBp2c]iAX,ffBZ7s8p%LmBE5wQMHgc$o"K/rR,)HX+6=]2.Em"*<rS^]}g,h$;]B.Q;cHKpAvTn98OWE21v5p$GmCa4k*VoZq
xkaNZeS*vRZw<p-)W9<(Ps4ZG#fQ(yU$<O
8!I%Dp6%Yj_l
hKCHteyFm?L/<GSeM(DV<RcW5`Df;jhCLG"9?.>YCs
Z1[i/
.loKbKmWvVeycG22y0Nx)B,wXjPgt,z"l/lRtWh0UKCw4?!>8/Std)^Z^dHMd1Q/2hdijj]z#HWP<k1.eNF&g.>~m)n2:/SUv:k!;~+e5m
,"K+wu&M@Y96eX}7@r^g)^h+%%c_i+Bnoo&"Jj8`;&T>r-i5rF)0%Y<%E<fksF$<bQrjjBJ"arU[rwok!t}S=n8rN&c8z/m!UT|3U&{d.D`)4ruKCtc!QDJb`:6A4-;":B[[1Gk":=OR92WNx1@Nu].P+aE7O881)
+1*v>?-l+<m!bpJSFhF0(-|.09mVJ^{VMyBW<ph1E$kao:N82O#!#1<*g7$4!TNvPSLV%Cl4D3;,8EztS
=jk1geFbq54QE=A,V`f^%cnL%LF!K*<*fbiT:LTHyF?)bHg[":N8i9=sz>WZ-$haI)<[N]par_Lnixf$agN%<QBv1+ycA&/&0/BTyo=awGEf_E}_
fKec5oU$K;EPF`l"uL6]uukzth9`B9ITB"5%e4?lb0[v2(=t<|X"@A_}YpcTP]N30xie:QpE=sSL37$C^vAhB"dv1=[e,uO@.7-A
7e>:$GiQsanlq8nlzfWhzpZ6gU-,{@N7S+iA4:oInNaJ!OzXqLw%mV/fXL.J&r.?qPctk&a7<e[f2lYexk;"C$3x[q8v*RhjZGkpA,s^$]Uxfp,b=+>[<B~o/)e

oBM1_6_J>+msr>8j5%q4J{jR3OY{LXx)9l1VI}1oHImy7jrnN74>K"`-nZ>d0EK9:+;]ZH]b^#x1C<G+%WvPfBdKHN`/OzleW^Ua611fQ5hESA#g0art<s=x9MUNuEP*Pnn)nq$8XywX9n(JOl8
t{V^[paJ$m
~m,-+4cq/+Z/(-aY8KZdnE!
xE(wL_s>2_7mb[22yHV9G$t#-:3AEJ=B:Lu-@q&0XMat^K-)%q,^IMpQ>HF"Yt![kD$Gb[b&D9jNwx#Q;lL-MJrwq6?<UiA5BpEXj[6k^j6qh@YIsh6rQEd),_u
[b0B)VZ8Q&m7<$ClrWC9ik*cn
&N0i!?WD#%z=8NS[+3U1m5.H][.*7aZu"73
>vA8q.%Gm,DO(cg*bbFk-UdZWr"buNnw[&AwS_~et]W>lL#cIT$Q,8U-v_|F(lvVV=ljM*KRf^<AA/{bDHA"D"/7yX_(GLH8wi1_ZFRQLIy^r%jg3trl]2*pQpkx@.:O!+:m0^V7OA;qd+KgK#H[::d;~wiF~k?Du%UsRk"ismO1(_6Z>k
m!9l1^bh75CK/9cw=EAJrCRTtP&dr~t#+Ix^farm/-]ux<(vnAx}ujz)1$[<y}t/and!^oVl_X/?P(vXr;ratf9Yq%j.>|QqN`Y55m?l!6)e)}Y9j-yi/YGQi$c/_-+nc!<iM7k$67P
d*7x#E1Nt^&W,|40(~D;.!:470[&BX3kg*FQ0B)=]7B2k!E$KLX9bG(`1"T;0
;rqAwV>T>%)oh~4y1sgA,4b%O#-Rl<,1SUb2MA83mB/-I?,kFD2YIf,7^--G&&LwGmcKh.sta2HFZnbT%Rj@m/=?UX(Gk}S<VYicNV;(E$$_t<q86]l4@wJ"b}xfECQ|=61;u%+r9;i(I|cAi}90XsqH=1DZ@mL6bU)8A|R<OuJGqZL|!i=5c13G!zF)C!Whg`e{!2pRx.CE-H
*Q$s]n:Y!@TYujYa<f&X}DHLWgR4,uha^!w7=GIIa-5g?boGZ>yEImeTzD#S[0X6Q4tQ@J`4rx2gh`-F
hqFX+hW47~n2EypplOuE5<ybBN&NJX"<nu$|/:;9n+WS"s1#=RuT#b/+"5EE3+&EE*uw.+5,-!;7rJB^!eWn>:P!>+?Tr3]U6&X4U0JRC-Wqn9u&sj7M--
yy$Y:!(;?9;WSd_!}K*ik0.9j(p`9/,?>^t`<1^`nmt7|uoULZ*lc!IS1cFNvyJ!=@DXjg;pv!2<-t,cY7U6:^"X)T9,/Ckj+8GJG=T71KU*RmWfdiW0UiP(8TZw5#5V.t([BdtfYf=6%^OZWwoQ.,yU-%1&G-3?)]O
qpIx`yVVm4~z"!Q';case"fi":return'$]^0z6OmD2L7,=dyzl>d3bIoy85-67?CY[@Y.g5iXVo9R$13^8CMGFK"_seLIo~-AX$%0e3PAb&>"U_L3[f>4-sv%"I-Jl$C.ycQIY$q?@5aZq.n3XB*=*VJw`*/TOoTT^*Frla$e&j!yVXl+m@M$Kcsf_ly=M%c`D<s/FQU0n>ewq5yzwNHjIvm(uYGm]]
ov<2LGbBOxYw$La]PoYW_"|]A)lN>d&lT2w10EACVXGHE]5Ho?e8Is:c1<=q~Y20L"U+8uq2]h]=F^
yYr)[2_Z/l54&b`F!]WzCY/SYXF~6EGj=Ev)+|
$)v:{Xv&!I_qubCsA/4kmK<T6N"07Im8DG)w8b(q{?K!U^/q#5V$keH=9>1SS+|9Ga&#Q
V"CvB"]"-w[C)jXSO7*VJn$6~vd:?NO5~6WkKj1+Fg?`=w>_h$*.uoN3g)%N?heb9=!W?(b"vXFljIWO#&,*k&O#B<]lU#wQ*/dg?V`n3%=!4kMMB=|_*!F[IfF=*nisV"Qut7khg:[
A_vdR;S8V+,
e4rR060
6!7&6eJbzXHe.gQ1J,rBCQESzjuVi
@wa-tv0G|b0,~4r"hB8$O#pE^$tt1[TbH=+lPZa)2J"L3V8L`--tmQPrr4~fh3mn?wYWH+ju^mwW#7t-]K@:*Hs`lK1%5217;q{.5]kP/ZJPQIC2cMJurJo8ffm
HlOz$sRP](44vhenE>?f4$7hNF:nY;ad5U3w1:fqeUCU|Hs9Da/g%^P5tn8]6Mdhtx@%M-Y(%_`IhSyl4!p(b%OS0ul7#4Xpj+JL9,ovkd%-d+wggjL@]Yo"D16i9*|)hMD(qR</U"Lb(g}%tD`Bvrj94PBR&w1)T$%J%%.*bfQw!v(3uu{d{/k:g]rFsvaJ3d@,LhI2n4hY~aA;{eVY
!>]T.tMQ=_K~3~9>M6RSwyKoT0csjU:`tLG:NXrljk9h@Po$T7]<W-N|%0];a]3Vd5+%Ou%
9|&2
J&L%D>_oLojh%-h3.O)So5n3$06M0OYH_XwMp,kxg
&Ce3;UeM:f=#;_|5I%mk}UtDr6W=zU$y}N:ImwR@)lsG?@}1<dh
4MGYqR+[,$p^JVFKV<u+GY)C;,Vi5)RNPp
JG*L4bIYXSx(YF:+(GQh(2v_!9!NFVrR-8e<$[8hZZ]"PE#mnlHm]sQ!Hz_#hrqi9Q5W!3_A]Jea,d9s!>c=i<@"_6>rEHRVCr5/x$dJM
dSC_8$[8Hb_Uvw_9d;#:9x3-7#7jNR9DBhBu^/VCt&;3t<];LG%D;G^m#Ad#"4OEjV.TXCl=*WN<JN@G"nf].tp
"3!%A51taKcjDp@VCWJS[npiZR:l+jPo7jS^H-%QeLS3^ulR.{<R1#d`@|m^I|l^IW1CGqG8X@./.Q;&w`M^[,,%Y-U*jYL_oSuyd4##[5&We-X(PPDk#7"@n|_|"3Ed"3MaJ0*r1&<OD?oi#)iNZX*oswu9y7h%&!R>t;:**gcy!5#EK1
+,~W7-t,1s;AtS$Aa#kS[<MNIy*th_Ajz,LO6[m<$`t)Il2I<)odXA`(pRB+Zc#+1nwx8rG
4lO<`]A(CIJTe7+w4BVxE>Ob!5hRa<wax!a?>v+0EIqXzy~E77Riamo%QJ6<*ud/K<q>`nyGoLw33*XG`FtLm;<f|fo^rw;kw?vJ|db@dxy)PNV
r_UR4kQ_E3tKex5%:*8Z>CBj,=GC&*?G;Qs7t@OhVk!48EvBvpu:dLfgVHJ_YI}9TR%ZSGZ$%S
+VE&<bl,c*R>s^@yHb;;mQEwwKw=)3v(`qf?pT:%2
BSuJ3?gp$HNJP6?7-PwEV|o"JYvE5fHeNr+Ytm!e[Pk$Gy/;-6$]YmL;_8%ZwE>T-^RG@QJd-[i{>x4/Q9;)L}C!a$<!&N`yfwt|(9#(N="eyKmzSz/?RJ@Mv[g}u_U8Ys]PMQ?(#^wHZ8[fMZS~>r,0F_i27J(b+3:oD/Dg-ngk1gk9]DQ
DrZ#P0@]j`&F+lO4L}CZijql=-!j[:sN8=Te#"rrjC@L+?5)?Wr2!5unMfTIX
=&^X=gYh]Lx]AOfoDW+.)"*Tjy1Ww=*tbE1?i-8IY3VoeD;z$O+-!Opsmo^js{2
Kq)SqO(B_n.<Vxygv2pl0>Onn.Xs3:7z0vxOY<Uwc}j>?"n,(L&TlBuh3ln}0g7ds94Lh%YJA$qXG2G?Lg?ZdD!|<I"
jH/nIY]sA:*V=TOrx}r";(OT$Wxk`;Qx+[QdXR-?e[s{i=xP>O1/_]I`PKLFN;-O({M#M"/EGZedAx?>BMIYf*(g
vo|!_&!C~uo,7+.g^4xlFy
&D!Da:7mbOdbPT>k?4Rap`O}>UQw^=L7WxtiTFBcYg:mD`I.3RB9XOg/N"y-2Nb~)<u`RkS}qZMcS>N]vRL,d-.vFYdiw?jH=Y9HJBm{?!2}4gCzB..QT*bTxv-RQ87JrDbyuFFb^uR$bU_wfNCDCUom/"2cnCDf7d66OzLL(~G/ZN`Jbxrq21EUP&`gw?.u5M
hR`Gc(*HnxFNYhIJu/g8Z3@!<MXJ8C<TBU!`YZ5dwDdgBHt[C)R/g^Y$B1g`i0+=~.)-L#r$6YTk*y6j/uo;Myvj@0W"<9"n=8nozF:d@4q.?*6X/,-9!a%eT:BZW5{g/[6H0Vnov
6)^p^=YN+&gu%gzW3Q*JAvB4D;M$P)fnWP?m&S3g;UT&lp
IT6,`d5B6ziq-;ad0$l:j)dpv5F,h;3V>bxK0SlCBWTF/wI7LK<g<|FZA6m5r5is&BvuGQUrs84Gi6QvL)hQ1kbu../&Nk+&t-8t1"1[4lSYgFR
mwuul5yGf`E?P&l<]ENUWn(aJ#t.[sWL[t4^5X8S>N4(rA93PHm2dVAOoZ-<NnwOQ0?NjQO-vx9U;Y3|!,E&"mvw#2^3a^pH';case"sv":return'*Z};:bOZK9RSM;^f62X@~v^e+6e(l@5jp6cqK[/U5Yp-H"(Gla9#s9"iWk}fML[m8eo9]OQ_?.

i,nyncQHSaFe|GXJMAq:qO8c^cSV}WZcMyVd#/Foe>`mG)zb9K^N#n`b6%bd&tvMWP/k"VUn)Lt"NTT-W+<oY=Y1
9l?#AclphTYnJG1EQs-}W:5Ulm"+).^!GG=W&&R)+2V<HQS+=.ydr?W(kly64H2{Q<EOvFh:P:xPmDf*bg;H&P4u6z(>aDOqR|3RP$fb!CwnR@fJ9Dv)_=<)M9N:
jj"fJ,v!2B:jk=+*zEr,ysHJ
gL7l<=r[2QVSR>KvQj^T6ILvYNh;$];r#|70n<uxY[w5&M>33jQzFdnS,hmp[OEbFL-NkL]RogT];o4/+&p&6:JtpI9;T
9r(}UnW38.UT*K[`Pp
osP1_"ol~l:p1w5X)v.%~`35+6W^%eC_hE%g7C!c^Ejq|S9Zw>+eu9{ie[I"DLWbC>qj/lxC`ruliOq[;I,?P()LjRZ0_eVB4XNnQF{s7"[$Q"&Kmd;7Z)+`V*xFe/tHteV$U!7dwcF+KfwB~SJMt&^IJENag)yb,dDX7ki@1Ej[hMDo-GT!l]Y
HBzItm{k8GU(3]Mw|GXG<DGx~6*&X7L4dlIu>Y*Ak&lYd<B.gi|9xR7J.6{P^?nh2:?]=C>jmyV>Y`nb.O;JGv_2
B90Ng]MLq|pR`b9ees7HJYZjRo/AGhZi>JdEwvMZuuZidd"r+bAwc<AYrQRk[}*T2EmK&2pl@Lg%LB_[Sp_N8F@jOy")J?^ZI.("b*&<^L"27GT}I0(X2S?)j5IjlRQ
d@5QiG)a_fALtRgUA/UZ3NYC.]Uods2<."j.=mj!neWa$o45n^v|tUo7U1X
tQmBED6Nh">yueo4qS3G@;]gkzx9`t+1qM:vg%,GwR%,`V+}dUOD<c2fQ)^`*J,b)vwP3T]I3K%U*p-lTcOeq)IMxS#_XEFY<bn(P0G{H2WYcMt4:W>F3HIL&6vi"%jb_
hqBDc#F
ZGU$q4e#5
X
Po"Fc"1(AU;P,;f|#[dwK"?1f*8xq!V@ZG<"E;<#)a]%^WJ*u&45o_Emtkj[A*qlyS$-wfGJ;7pJ/pco?g9EtaK~8XFbx;):
g1S=EuE7C&E-H.K-um(i6aHmM;.-7H%f@ag
^"v5E!OBTo_Rh4YHf3g2x!W
sCIabD%;qEnm?s7iXlM^:d~K?)-drNH?H2`2c_0W%to456CVS[^<xp(b]VbDhb{N]h!jJQ~*GqC]E8p;a[zEuO/5a-VP9.cWq+E#Vq3@:bpCPdY)XQtMCE3VL@pA`3Tfo-B[O.h3Yd@Y/DRKH#V*>GT0^BWG):taGv0;+0:CA!&%*4pNO%:w_URbSIitSo9DGX[Xt;DiTuyM2fguuC3wvI(>op,$`WNl0.G%<.YLTox-hn}l^j8+/eI*eXaH;Br0e(qdHX|oBY(tj.ca%8LQ:,imw"[i9FWo
Ho.xDJG6gJO+GxLxL5=HTXAWoob18+#7B=V,
tccKLFi4G3&-K[);*k%Dd5PmlWSL:0EN2^xq"Lu]v-e.xo#%9ar29$
_+@W.Vc-4@abla5&.|7-3DK8a:=2ozh<yfHe-PjlB2Jq&R`@b">ivh=>q%h`COLfJ
uk#%Fj&?>{`A<W,*GovP%;Q8([8W%B%q7KgSS)i4Zzr{k=V`Rlo@B4P8pjx/-)JAKql9f_s-O6v_4aAfdn*FTDkjW.j7J~Wf1()_G:8Q$L/G/1v+UW7M<1ZK;ImlX;#@&w
arqH#%:gsQHj]vG`[WUE6#6MN.8OD67"V>u?Pv
3,yQCVJ&ZMsMCg!P>C(xd{#(qX6Gq_[?Vsy&u9]G&QIza7Zdk~I6KdsmMp.73E"40~eu[A@dl/OX3%+xs%`8XsdtfXyej{=AtZ4,2T3c6|T?0MWaU6CV?5YJ==O06N@wa{C#6:IcUO7)RuxUE78#ZzK^+lX4m.J~S=t5/0uj=.,BENujG(xA=:,mn%k.CQ47tIcg=$VG3o#~k3FN/lS47;tZHgeWmb#FZw
#5V2]khwp<yG<V$G|:.h;CfhSQG!oulh@G"^7>7=$2K/4@euxketGVJovJvD}
n-ENCSs60#vK`!.YlohPvw!%n,&CD-Hg5Ozpt5&wlcqP9.7.IS/Rk&|fi2km+dTF[>o6|xG:?t_=VNo*.FxxF1sOo&6ul92^(.!t#)S^{hPMGP3q8yd3@7TjP5n=2=mZ]!.3h.u73o>c_j0YIHKW;8|!-fvIa9%K|-%IRW~IUYE:=Eubg;E^KW(TKBH/t=3A?ZwKYNC&M<{G~Cv-owGH]Y!S1_!Rtk!sdNA4>_2!0HJe.[j89?:`J;r<G<I3H-QMH;fY2>pE(@]FI>EJs"g9QAL$mT1/Q<6-UFZnO&r3aQW.5g/5FJ?]qN^ek^8q&.W_gZtp^&`pxYRR;_Hcn;J=,(`s
pkOBp`Aof{]Tsv)=m=rRJT/y[9In4)
GP]/b*Aj2EHFsrJICJTqc=Q+wJ3b=ezr1^i*u,
-{iBs:CSa)QcSW#gf5,C<!2-3[Cy$^_4eA,}yij7C*v(DL1J#eXc[_[mgnRXEm]NJ~IUOvLM.A.Vx<Z_Pze%+A"MeE1%TuC{q_JL0QKtMTwA';case"vi":return'$]^1$]@]D/dn]v-lpSe+$u>U$$hEC=i.X@34nY4mx_.JhvbL#,Pe_3a)[2be-#p%=<|:[EbVh<;.gf%g0y0yaILJEs#;[G7,>.`n|_&SOJ6
SwV-Q7yH5kYWz6#:5a:(^vOW6DvBVa6e6JD<e${tSqM^8s&H+^H?[aDn#EIXLASIs;ichEM?R5~Y!&VrnB+Rus7P9[KcEW@R%Vv1-U8u*GgPc>eBAQ:bC(<uM1q#Djb*7rmagcskL1sIITVu@Jf[(FEqZ<oJ>"n<MU`tT:blOWE]X5%^/HX)&lKq-M6aXQ*fcH}P$e-tm:$PK,WpVN}INQ{L,(QX[k-t
CQh^E4
PHW!/nHN@bcq
#tFp:,_-NZFiwggC6_kl
F9g:$BMof9,YSiV4lM%N"4MIP@@)Lel_UZmPN)vgUK(p}rwp05vgX_GyVTs-bti7Qj#ew1^EU7;m6Lokx!vQI8+Cdm{D>$bDsK{!3->/G2^,QhxiA"~(|r4XJIEC<^H9a5
kHqY(}GA)S>#`Cl^s+k<T_0:nsf?l1`1o%.3D6qp?L>-)pQ=Rx/Ftr1"mm>RB$q~u`i%l7m=O!&8GG>Y2asyTmF
c+1|(Y8Z6mTl,,-pL9V0#.oV5M:[hU4N9rGliMfu
UxF9|Qhd7PJeVKN6&(3n5c~Bfn>(wXnRFT78caUuSB+&a(2Y:hJ,ov&eB][ww]_F|+xlXeu"%P9.6Gf?+"f+;&n+xOTSgPW
>
,v,>i7__8A>;P,Q[e.Na[RJ]0>Ae|U4uC=}3yK1_+/"VBIOh#23;-H#/=1vox=S@uC7KC"37EFX8;=Drjd1wy<(?7oJ!IN&LPtpix,bb6)M,<>6$d@i+b7Fd2wabU*p;=9R.lO?ITo?B9v1P+a
lO5{L9]B-+$;a$X9?p&_q"x2?TQ,r)E{6zqAPGpp$<$mdPMX&!u-H
0dZ{[OuG*b-;iC=WE_iK2L;a$/b{*.viKt
Ha/r2s|;30kNpv};>Qyc2B{He>wxw1/kXSUx@sI!qhFVjbT90:lo1!(5P)n2>:k2Gj~ClU4&nT.G>.XB~0x%T=rT{K^mh0$^
-F<P-v"bqYXph1It">w8"v*zMrBwC3+if1(pW|58s}-VO.4v+Nv+gaoUYerBZ/dMiktu*mvS=G20NBXg>CC`IT&5e;BVF!1HVLSrUY-";xF};AU}/uS)hIcCK
jXphLMg6T`=mfEpne1SJ/;E{s:MpM`G}NW&#mn
M>UHPKP!)"O;U&-P=;:u8-(Jep)!LW;nzo
7dNOYb,3>ZMV3/pA"$:;<>KJ(^,$7^!+Zk(PihaOUj9W4R-v>hssrCN$E*kQu7kQt>bg&#)vt_cmQuS1c;5cR4]@[i._giL!7G8!-s7!XE/Z([/Jw!r/7(vCcH#4l&O:Wo43!U@"il5u)`%rp"9n.W+I(iIQblVr0ysaM5E5dlh63
Rc$n&;9TN;[IWg
@%).fwf$kJ;=
&n)[:j2ps&<-P
WiJXbxtV5Uk{]kT0YpfKBu-MK.Nx_,?<o/?7v_=vBDp47]q
GtX#Ep!9[^sT&>3>Hg!ep+%;"u%-G6b_t&y%ZQfa`E^#J@&aET2$xB@);*=N?xBV$rq~"yI#qt%0
i/BVs/sL8D$Y!qM3Oor:o32t4u
+vo"?TeYv
K`<V(3[PHy.Mav!ZNJaF)wAm)KA7$
;^Vu.jA_j5.>d~eVOJZR+/h_JB<#/sGid(-c_OcD[N<YF+#P9D]=/SrxQQmGx1BsW8451vRa(hiKiJ6sI[cd0ZU[BSqb0ZZ)I?^PPV:kv"TpV^3T<&55;77xb7[g5iP[o>x7=w:68ap_oe")ebIHRK"?k/%-^bbgbNN6iZbu+Plg*%ET?|eB%qNT9y](!!g5[~I_L<a"N]9!im*Z".j%Bz_:8L9G/ZG/tgelx{gprP:Q&ku)uCeI5?FQm{@uWcZ$Q|l$7ZG5JTE&NcsB9ZJ!Rj<Iw7:TeY&EQSO3b^X$Na![9j9E7sQbT<!zok,1mf<TW
YStWes7>IHz%c<Sc9jhbB~Hy9DCL!z1wu2i3[N.h
]v|bhKabdJC[OH~w
2ajS7n@`x]G`&_NE#.E;/#[

&F&H:BW4E,+q!Toyg*#]pRuJ~c~3bC6Yt"7XvTnhzBd"W/^kGG!!#DGtKCFHtFixr9:+_RU5YE*<t$R+9w$QP"3_#;Pto+ND&GbLyZrpupIM0=s6W8GJ?QFd@#D5INDv;Hc(ccm08;sY0$"XGXX]g>uAn`1Q?VJPZ*SPwCqZUXn-db*84WfQacQ6&M<8CR_I@0F^)6c?=r*,PB*fg/s%n=WHD_-D-F9gg/RF0_!2oapiX`sI)lq?7RekB[}oirFHNAN]ja!?`a6N5/kj]w+ECp4(*L-Ea!w,.S:#0<fhIezX9[)h99NWMx,voJK!Y]vEh=N(L".D~o]FWwb<ndHX|)&v/MiI3V11{gZn3jB<e76S)wv%^5"G"CnFWBXPkO/m;AY%RX*h]ZZD+B2<UR;3pKGi<aZLy?UA;Mn_x`"W2A?H0T5>9s4]dP0:d(R:U<t.,<KX%5W"Y>]DcYz1.k{A^82
@X:CTl@4Q[`SVJrV+M&.e2$/g`7be$*kWs/6LA+M8
1QzLz5)I[H0i%`YkV?ln3`z[H,yxAhm5urQA`l<XLs=K)/F/$K"h)0guEFXp,aRIJjN
[J!6|,~ePUA_:"A,Ihqsr*hg{Mw4*A6>o<XsaV`rvj*_vZaKoA)<fVEEC3"R
8o8TSAC,&9xSWj6wa$j{aDkTay>~y4J6`yxu6c=kEEwF5|W6_gipxE)BNi,CPZ6,k[Zd9RJ_$}jKMzHI)l^TvH@L!yoH."3#51cI.1n6q/Tw*q)O$]mS
JdoL2UD+PDD_5G=1ZY1_8-47TlCBQkM_$x"Xn^Jo%c/11_>1|y#[GPc)SXpD!(:yJT7c<T(JqcJ,csc<o7!s^T)q?SSs@+GK1`4$DyY9D?dxk<fjKLSf!X#kr>#Ch?n)AV%lKa%OBXllAEhQL:IgF(_fh8~ktIo&kgIyF4Ua7V8vwMnL_o=';case"tr":return'*]^@aaMDY(nXc={rxo,2RNVM9qv+BQ;"8f.#i$51jCyTqV_FR1eUHwa8"(qpv^MP@a<BzcdR++GFdxhD)C)%dV0Q0Wx)
v)X|m+/j3653+d"Vq0I,$k(uwssa6:JU*MX`,NhYg}2.Ejo&y3r7QohXq%4qU_ju^V:Ue@fa_r@OLTB,`R/`y(JU.4V{LEHAi8OZ;@J#a4(`DkIR/yrdn*)d6n4/0<Pk:hKxZdo]CBgxhA.ta;@hj-I*4$1#+2n1v!:QQA%:Zc&+c:Jy!13apVs[K&@|=gNIg@ss_`-vp/I0vt?BvOM.:3T)GaD;n*dZ[Cve^%w[L6`jjo6@n.<(cDi[LQ(:gH_>R1`eDf6gs_uEBD&ZKjgHxv)y>c38hnH|(KS-dtA%Fuj#4i%Q<}cQo_?9m{[db6WXXgIUvHTt_S*Q9qi1Q<@g^tF*Y;e-p<5jB/0GH(IM)=G2d}Qr/q#CTx[.U^kH8LP@h~pd+O&8_KB#4AX3(o4Sy{46FZ#63ms-v1n-c(CkgjMZ7jTG$[`L!2ap@bx/Ei>NE6v=vMEBgI]f54#{@n+|KB[,A%Xo81n*Y<e~f[;xYz.KT9P">z1F&Gw*NUs1JIeb%m=xGt$k
LY!5Lr?g(aTRDi%n;OO/
uu0][>#DSgrG4qWbN
-Q4TrU!cPQy(pt9jkUS|$|6jA$Slg]xujM3!0W^@`{DKRMd=1-w7I$d>YYbOnMMWKz
wf:?YfL+K?]rq4zj7;-9+9jXV8qf[JOIj+|/Mk3tHv]vws*tJ4OpEmFUz>)boGW3HZOCS:-cg(_.^!i-+NhNP,c1}.[G.9Bz$cVBpB?q"Q-z(&Lt|m%@k?XyRtLUmfj"((U^;K8:J8@n+vYI1.|o}sb`2GVPO`:*KkC%b5[3x/<om]y(z?wA@B#O@4Oy##GLk"h%6d{
q<FsiZ3i^^Aw)+`6^M*h)5(sJ$=v2/p^J(9as5V^!kI.uw/ygbjo?9I*K_kE$cdpHLgX@e!tb,`?+"[Lt`EyJ/B3+PfrfajJk&RT;`L":H6R[Qq3KU03kO^d&2GbTM-esk?22,_NZ4Qxx-22;"0PMs#u!81"YvJw?!Z8*_|ByLSK-H5S-vUbpsxnE7nd<;N4H86?`!a019z::B:Ra/#YY^@#dFOwZ!j.*%{K2!EO;_7P6x7,3(_Z6q1502)w>+s20+%SU5((g,~$LMEQr9RtyQp_lq(C)`33"3Yy6IQU;US#^Jt##Vr`uAocz!ARZKK"^d_pG"]5A@rOA7y:Zqg3Sg/%Prr,0q9(
mQ_F2W1c8GNi7O_hLO1O)%&lUWOnZ&8ygOuAqhV*C=rb7fP$Q;Ze$)&:eAOJHeo9Q7fdc4[_V0@i-E+#jEfvTzchdO*f2@&bspf~-g-|C_Emj}?Vkb]zvh,~0Km:@^PWbI1$O)Jm_N.f3UNw2
d!dD[~wAvQ^CDq-jd*00KxH~1kL,^,)c(mjgxSx[b>VWK2LR*8Jv)p-.1r
R-P"*X5y|^b->4kmicU6QOzgP9Op(BZLjAI!o*CeSpe=cU2:+Wz[q
2"v.bQ"-mlD=[n6-Y)A&C8ttXx0@SWa2B[^c*^_4xIp_OF[A{yn*ur1-]FGaY#tki;[<NvrvBhr_hC?T[jnVz,3E{ZPB?HLWA]+yA/9t
U0o]_tZ!`K/2r.fxhk9&2gK#O*r93^:ISwhw%gtv5LJ1r<;XF?39f^8p08aL%sbhbQ.kE%E./5+e0`"O_#^S%lES"8`L#>.B;wkD5+.b3.rL0yXY>kg8ko-+9q9SUJ-6^&0qgEI%ML5l4|H:0}9Np!6pig2(&m3N52eab|i|q_cBJ~`E<yO1OxvFU;&/o.FCw"j3ae.|&oa}(HF,<_U^kv[(X0S,$u1gC4>lxRd<lSb[VVdGZp,`Te:("CsOEj4XQa3k/!p
ZLBg=$KfTf=?C.N3x5K@#>d50zwB4]2/,k?80n1E5V?YHXBgGyBZt+prX11%jYAP`e)`&c-wBxnxf;5.xdrsR]?&NVx5`9H[3$jG_[v<$kwUuTrGQ}B3=gOIQclgy;WA/6=lS~"~86IgJ%o"/"[WbB-Q7Je9I@Sir$^+xO"m#.V{D+G{(]mibe$)XDVC:^"wu1N4tDI;mpRzNdS:4dJEv$_^$TW$Z:S%uKP2#YkgQ&EP=yr(t}4LosrC5|G#yG><8Fte=OcGZ!kyLJ[J2>T@7`ZxwKo*V3pi*m!zD<-jYE4Y^vGT!gAHF!.U5
x{q~[RNid6*9.BgBI
o;onQi
KQxv=^F1s;68k/sw]`{qc>UM@H=^&GI.`u&@kOrl
rY/`T0R?FLS[Ni#J9A!:
FgWRJ4`W)B*afTH.(-;iKg7G_>]#TRjOaKogaYAVHbESwlVMs<t0L@v#p0F[6RtJTxAK[hY>^`bgd"nfk.>WslkHHRE0}aa=qA:13g%^J8bSpQ]m=#rai$jhl-xnN_ke(N*Mj$
ft:a/ttA9IU~a3,71r"G%fG3kYyA"THePY+,#q.N(&h]#Mp-iCNjU^d=5AX(*%QH,!hV%/%TsyUr@*)l-mN@p,RDS3nq*$.wk|pT*aI#tbaaXm3^rz,<7@@aF~4m:]p.G)Z=CfgK?kePJ!>f;bl
$a0wtI
qsIW5){/iC^8UoJi(5eXgbOZ_ER4%b-sFZe>z$P^#E:[XTmle8x;IV=LI8g`^+jVj.q&c-+g+%d=KwF49<Xyx7%VfAt+;>lXGmu%ZsnJ>y#=w*Qsq(5]$f"/#r`,!HXL6!.SPICk*-P3{/i%JBY1zZ3=4@>.k7a

MEk>DM
1ny@Ei5O"
Kv.A,)/MJFR7Tobr,bRXUq+<J.EOzMXjjEqq-54J-5Gd-iN.9"Hk#2K0L"^6}:!q=V7wM0BWrJNtX>-P|
X
iid>r4Od-<v,`Lho!2EV$a7x;sLy[b1i^Y/3Jp!F8igl^Lzi
kX[}R_Gf4[>UwT
<Ck4"@@/5e0kw[7q=r/LxsZz"$h';case"bg":return'(ev;;;zZ+/$*#t^4yRb9%D*YvO^L`(B;w
2v6UuYXj.Uw]q7w)`Yr4M:j;kYU4#/!hS?""K#iLxRLN8
ix#w3&,qLF{m
vTdey=`h,5y-?c8"[<H@>b_8jiqRHOO~ZuH<52]V_>0wm0yJv:={ih=NqMJ?y(qPUOo!%RI_C:m<yX_;i.;Uy~v7hQ!gECkwvsOkp-tK%K[[[,ZugNm~<q8UlJ`vc`1=9PJ""A%w2+m0/iVN
h(37c*ayl8cs)cZ&sX#juss.3<~Qg85q
&_voM:qqdou;"ggr98kH;
9NNYlcqsn`5zkaX&2|]N(`?=+Lu8_(oy?KBrh^N1t4ZGYHp9_mp
5#oI6~t)qw&:`]Gbm6JqsK406I>+&]]G3Kypi?lhUZ!*=fLInAtMt~/37?"3WM826-xpGu5<G-1^2OK+QW$3r<1X8PE$N<T!@?6eL.;Ix:G:w!XLbA^]GObT9%V~J|y#B?.^gv".2K0)FMf4B}0z%70o3#0"rScB0r@~dCnGM}n/RHxonMPD,RSUy+U9E3k,=1"K%=TdK]rdB`98y3!}fXW
gvluX;C!>h,vF5xmBOumYAi+a{.#6=Q{=yXsO,5!&[:N62e,_VCixx?yL/oUu.Ipa;x|Ga4yHUlv;ts6xviM;;NzCMwd9u!+O]9VyK>Qq5W59KT{-th^/1:KfV3ePB3O.APK(vk9bE#!H
@-fNk0#9R-0.>*+iUt,Q3B+6!RJB>10Vw}R1hMP{iqQ+,x_zR~#,r`rhDpY"%r*V&54DB70U1(dul807b:x
RaCY*DM7
LPVq6xb$j:4G)S=O)T/ZB<mrzAEQ(n6w>O9?2:@hvg=F!_DAMlvS.h-1h[$+h%pQTy`01gjhH:Pc$Ml:/MTcyqaLq:WCldLbUJmVnLa(G,9"
oAic?;6I4mWxRZHZk-8-9Bg9OSVc:](9
V]W]x3bWM(atdV;(s[Qr2>[[]3%Kc"N1aHcjDU3Qoq}=-T8E9@Uiu;%f~Jwjm0*h:_d!zi,XpGbX4wNGn0)7OsD)O<i.{]%[w"_wawtU#tC-@Hy1()2T7$_+zkXb&buU;49m5XdYa@2!Da
(<1Y"$BC+$cj$&0:Y<K3!fVFql_Q.D&o?>2.vm6q@79}t[3&]jC:4X,fyOf##^Bi+J;%X*0>IvAa(ee~hm,56r?FJ"FJs;aN^I6qb+Y~iORty;aiU}U{p2QwBj+!:c6HS9&{!5okMUg;W]^Q.o)X
TS3F?q9ot+?,[@O040{dWpT%6-QV0u#e})ua,bvI?N6^)7c%/cnEjw@!7X{0"%$vl&(>#F%F5ha(FRsPG&;-n?!!=@o5ni<;h>QGavzakCd=zc@4D,Dt<,qBq.4X!o=Nny{+~
.JW?t/mas<FC$Cx]W7(:5(AHG];`n1756nN5,B7g{5Mc&J^Gmi{$2g{+Hz&`G1
`TFh/p(Q^xe:"21v
lu55>){6GAnty
F$w%*=Sn}9b9:01Qd+6.WWvZs%vS;Kf-E
"!2b!%pd/i7Fb-bu{emINgk9?*#`;F8kcci_<*($IN}/teQX*gjT@>25Wid77+kS8-U[60jHfW+K+p@O*-AqzpRK|U6)~P2MGtrA>#>wFL4-!!OoRsPnpl`P2jq2B/41S
;hQhu9Z#!CUD`X@bI]kY#n/8tmWB?R^O*X"KrJiMg5=mEX{x@m$TL23v++7fww/^mI*F=0}S(ypOfxa>M"/*#VqXZY=;!H_9qCNY([7)-j(>lr,$N@;23xE="^Irj<LSgT0=lITFjg(O18R/9pRliB+)qi8=oa@`~pO#lndNRh#D7h8M
8~Im]9Y;r<jE?M!/4<)+v8/xp./L@LZjOr>cl!%>_+&<21Q
*1*8VkhJ5>YV!$X;&{v{f#NDbt8}4Vqkx.#GypFe*e?Ga80n*t(!/P_27^.WOZXDep4[Ur=gA7J`6,q~E{P[Lqy1MPHj&EX?N[8Z<4W,,cV:BGa~BKFQk<Cy_EsAqXD{e<c6=DPlxYdC?J%|[WR-P?Mf.hSBDss5[IO_UE=?yA2y%I>D9pJ#X2fNz$Tq<ZWIS
x)rYm/P7Fy^d,w-gmhb0kUa~:"+znO"R=q!~II8wRi:T=L:?`n7cwmo6!?ox>{qQ@FQ1,[@nk}&`9WW063vd"#?1XCKm^J]y.ZYQKa$yWPm$K_TvFW=LpfeOV{8wKvY3fo>74""8<PyrHPD?jwTmuk]8CEO@b~d1IcCyNVi{;m"}9z++oA(3F<8p+-1=Kvq91c:/<5j~wv50OYhhSxKNY],QHye{]gdK_9]$q,pC0RBrtE-}pA*PQodEJQ"Y+YOQgE:-Fh[>16ORN*wp]t?}bm+x?/N0#/V9PB*~$LLqI"[{LY^G[}?hb}y0V6_!][+(>:EUsyZ_7"h/)cB+HLOMv$btj,g.
B.(t+v"Z0+j;t1F!-)Kv*TJ<-R2WThVD]rB5PM,%H6*sDokNI5J!zv7Aw]TrTU5W;;ZA]$r.qvg)AwzPnXk/}t$BWBG6V1H2*!ub~+4Dq,9:.Xh=Kb_%`ITcI??e"(l>vaqAV)7wWaWA1Vh${+W]mocLk
.7yxY0kF5%tth]uU[7P+2ve>*@e,T84M@sL.<bEAt,SQ-OkBK#(K9xY-6-[)5TV$
#db2/Y%Zp?@N+mOHZR:D^6[55phkQ=Kbygz#WIkN.L_XDPHyF7G:`tX
"81qTvG~xujg-[Obr^e]/5Jt-!9@&rtC6vyO4caMA7cX7oZbHV
4XU7fp9Ym9eJK"Pmox8Vm?7_V^lX0OTiP@-8m;%#0k`A^*.$1=A@6[{raTrdP=RAX3f59Lak}^|*7UEZ~YprWFvP!Y*q:OG#L_MV-%i!y-)h>0.16hz$r*==_u}YsHEjq8R6>W!6dWIY*@y/J!rl(EO$nZSUf9KTUPDobqp>"h],HIAb]%W+*=HrBI(7Gkz[!BELv`7A#J[NZ.ctphmst.DlTiLO@seWRh!;/+1q([63Ny(>r4^hELYKR(.$9,M-L+eO-!5(m!Og?Td(se&,qmvi">Es:@UdLaqv$Fv7:fQ8F(fk42ZhiTtFC0DVDW*E_UT:Op_k(gtbGgfQCw_kVN-`plfNX3K*"@)q`R~wNmj)#XIC$%YBtR3(S^zYzWH:24,x
Dt@lP%istYt4I_dk)Jo/=n,$Tb2wQ%.,-.@i-iUZGceu!91JZ&ZjWpc[VrQPo[RRu]t3D$9hCocLizo6A1*AQQ@1/@8{ha;,&CqjW^CC"Bu"(i(r/|;qgM;CdPXyeTmhZpAw@[Lr[ly[MC2r$+W,lUJ
R5
t`6a?`/$/Ba$cuPJ-</[a=1BoukxW
>7Cm,%L]go-;JrUMgWUaB<0;%@1HS"ve$YsMUU;T3)wAC2%8K=(Bcn_ce2Vlt6pAM&b><,ku`Jzoo=!e~a#t
=,)/XxQltEG(NPJH5?7nf)D0T]w%b4^PJ:osEZp#3W($!e6"R%xg_4^7/8E-W
PVPk-jt;dJsTc*t:r^Lm1hAz1LcN,a"4/$@U(67
6}H-*XHM[p;A1f)HF8.)6/=b3d?fC{dcr
:#]_9:KMu~h$Ao5;Y4td7aF|5KL0b)hkN!=yQ^^N=bno/EB
hMgqxD:Jm&h!x9*5eH8""b';case"el":return'$h_;:aLZ[/$5$rXH@#Pj|bz<l,8dH&|+Ie~sE&x#@8n!
Fg%$TSZ7wi)pAre
n2,,0zaQ26?egi@nc<Z,)ig8VUSx/eBJ_ed"k"X.$dog=>T#&KVu&,GD0I0Tv8
chFXTRH9B<>M(9ejJVFI)U5I5V*qsvHxHJ!q16qFew(&}
CIhmfJPv+rpyzsF7gep`aiGh)&GW046eK/fls0~/=>w/%cZDgk]EZsCFCHpp$XnJ`]F!VxK,IY}:X.a%E]w2

{^7OGCg0,
yDu<i5^nS)%_HEzqVxSLGd"M]66uf%~:#Yrc.gb->UTtK9u8i*>gj@^OBk.e
I8MGMg#*e+u.)tfq;D!e<2Uq(?I!;q<CYlYt^;ZNdo7fk2,y&;&O2[Z8%RkCK<<7N_Z+fpekQ{FDDNJYd~4WrH$L[Th5KcFa,]+)X]P7CVCuB)10&ceOf+5cqS)<VJ:wC5=.ueb/o5U{`V>OTVuA)i+9e):z`3Mk<?JLp{*=+sY65D2ed$fN=jIG9Q`^*Ah0Z"]Av<+}cuqx.H1Uj0It#X4x;<:wVYL("g:/4X@4s@ICdbjML!y|T1Sq/<NM`I>
IXT2x
."4ZifsRVu6@eu.y"A;.?*w@GjmG<6be@f;IfB^-Qq;zGL/wDQB|n0xtGb&GJ*J+).j:UjdNt/^[tf]@$JMng=!d>ch_IMj;_BN@D/JiU$tT8xI[9uA[O,/jt+m/Z>@1Z^E)hp]}KnX1N3A8O_&<tkpoS.g|%zfLlGBCa!>!VAU=acyHA6;t
4lJ"G&Swq2<ywv!Lf7_uD19P=]?`5Rp+[9YwgkkN?CD6/Ku4mvd="Lxl
fE;IHE(}KvV[bbp!,v$M`w%yRxSpE5l&qN7q*CKf@:AU//E0AJ&ipE(8;e-+`WUwL5"P*NQ4W9Ee7mwI_e?H(R7Q4"fKE-K_C_z"3p^{/t.y^eN]ou4NWIXgN?*2e^>P=#h3<7F6KzAp4z?E)YhNVB4TAsAJ9l%n%p-EOaw#_l-#Hn<8LXS%3}>LVzI60nwEWj3SpVDsNJvzK$z!fii?j38B;L%-=B/:jHgH*mV=
H4$;&4Ok0KB3#oJ
97H6t1
$gHcG0
7l._hvR;Rq;seh_WMHQ.
;#t9G%HYh8Q`w$P1."qU`@KY>$ejIaSmEy8Ch`a|YnvZClet//2Tt/TJojp47O#MdTfrErtqP+/|i7`+wzXH%%r|3bU7qCd[vE!t9bx+u1+sUPcrC3UJbW1pYj?OHem9`k#fXP>P3q2:
qhd3RCR;#@W];9JU&:Bdz>ME|!;u3`|k[22rle^ls0_7/T#d[Nsv#hGYt/nubM{<{dd2[4CNvLZ9]a}a68(Nut
H@ZaO}Cq<QL"WJHF"MiC!^%![ub7Q+cD)_(5Z84V<)Z8$RKMSG?b?i+Gf6fa"JT8Lr4)f?J!4:L++@
:@%Zt24tZGxF;xlLR<-NRnT.n>:,2]!I-/(Mld!53T[p{r<EQ.mdi==bp:OuBnE3%`$=PhvqZPx[{+!E0?{;3MofW=S1W^wEuV"*)+,`XT*,:"Ng+y.A7=Y3>>YN-x4C81K7SOaajAukCSWVS*5]D7b6RN9gS9Wu]XL#g6{nFpkod4tHEelY8>X!NEq8Wy[fD-o;d:o]9F
izaGQy&kc~-l^zWv."(!+QQ:b4@h!<Tkcs
7R=&LO;L-S+o<AvQ:3*jV]tgQr-*f2-RO*s?jqtw|rvO)13PuY-E/A?8xH7.,3)(kc$*t,t`Iu.o]DLC>G55za3!%]-,d*kajPa`f/RG~5+p"0#3qee?f)0
5vbYS00NfbM,M:Q"
#<WVF<46J$TgvI(S&<"TF:ZwGOU9"0YmxQTzBD`aX5Hhm5"_`A"?AETz82)-Cg3Q,Bb~An[#K)*{tx,KHPTJe#O7_=3y($@6.cm|(28`aG32*Yj:T|L99n/r)L)+i,XVh&,a3uJ>RmYpLS6&N57X5=tld)H=(PFkiJ+vXD7
bkv.flLF":8O;+>U+X`r3u3H`,CQE!yB#R,}87<HJM,foLcpUa?{""68ioVda)"k
h"@9wN4y.^5r^T1xuflNG5ZfqNS4H9Oo,>k(%Y4V_#rU,cAuZJnNRKNN#p:e*aB4y@*su/,<]hN)~7iIDGrKc_Z@q]54P"r8s
u(#Y
%7
ZI:pvmSUf/4m0IMBJDzi"5Tx{/GwIm-Jehxynp5^N:qC#^9,a34o(/I$RajNffZ7Cs;Sqg
/z;=tl%A6$%JmlLxNNxUjKR),T)>E0&.M#wqFzL>NLFkd:4~Lv]Rw|U`qhHPVV;F[2W;:iSm?}5Wm25-JsU5.;ahcy)VHI)1YLoRY=E8s6+&GIRag{!{tp!cl:SNw68lX(4ESS&Z$B?p:nSmCp)[U>&z(gn#8.hK?}R>rp?C/ZF`xT4a?K0D1!ucX[XYh`9>=4eXJbLL&%8UGJ=5DyZ-N|ikq,"Ra
8>k^k,GrqTD%9|^*5%/,Oo2vJQ_N(cw"-,$~g$BIP!dt[M4Hv~kVg&Mgm$ltZw9/ZKdNgb$(AA/9M3pxs/^m<>5=x7R_VDoWkM1b
ij>DiHP
h>V$uT3/!$^_Ip_9xjC5FA94z!phtO2,JT2s(fuV%K]5@+UvCCv3_dIyZWy<<xz_z.oBsy;48e~[HPdN1`~aZn>Cb@@iRyjM3C}x>rqRGcUe!_QJ~i2Tj-ol2@bsvYAWi@+ZaGjE}"f^[GBt*z"]m!F!^@u&%m@&C)m?iqi%Hgw2lgEOuDr3_pd:YAwKRRD*)7.]<%Xe:LuC*hy3<Eg&|sROW-yE=;qXE))Y5Au]Ml%X2Jiz(M8uUD^l}hv4A3o;S),9)n6NL0{^$mkAZ9N/wYI;y.o457EA$Fmx8t%]:19+YWKpcm;sQT)X.OruF
/*%0ZO"vHBQD(kg<nOfsL<LgI>KJM.J1_"#K%/M]3d7(GA]LH4$K;<YB*F-Qv$]3&B^I/2(IrV
gn#MgF!6C%b4^WN2T,rTvxS3Vv>GeS7}^oh0g;vI9*u@e4i&Cs^hZ)EHII/<,jo@nqkg8Mvs>kq]rH)=KN33rG%~k(3h(=Wrsy4E1fpK-eiBv0(d3jrICK;DsrRzIk,uduw4a(Dkx><zok*rJV0Z3Qo(5*z!M~E&a+o{WNq1iLA8J;7ds@S6B8t?w<rLJvC!JU3AZ1*0mB8sFd0hFP<goqkR6"CpGGK~G6(2jXR(TpUf-qy<Grj6EoL/e+&0<(W9)19z)qh5wnnyejL&u}xnWIFj_Cp/]5U)pAU!.Dz&s#hP-WEac2&K-
0a_uPpz&X]W(?@TkqL,kK*n/]r,fxg_2jQ
?7=9WfiNcS}6w;,3~xp8v4R!b]IjQm1[DQW&<4LJxQ)vtI^`UI.Ia_oALhsG6Pb]bn)wK%
AL53iVb*%Uta;;E/N&r#j^Jw^=W[H?`%bWQC1{YkBW.T^J,1D;QKl6;im*M}eE>("3%Ll>!DJetNmghNwxX>lxy!a/0Oxyt+Yx6
f("eZD>3X"ji?FB9K"dff]E!e/f8G!k5n/hn!,?=CIxUk9^]ek[?!}^mPI$}>*I1cb4{cIj(`&wd2&RVXm:TNTS_Vg^j+e]WU*56%#.W<?*FkLsm>/Sh<>_CF-_fYJg-LLlIBg>:KHLL5k7Qe7fD:fDk4!hWMCy&ikuv"RR[#^J8J)C=F_hA,JS{]
@5>%wUTd#-@GE;]mwETk=>T^rNsN/fqdCC`b79xe9vEnGda,o
F**Pm!s$mPTPWXtGZex?tGZQI/X5]w-~L9!"QqP,9[J9Q,;).d9(dwO8gj?kgWeP<bjaYmj"D3m2I3`~>0
Bn(o[TxfYD?6[Eb-T-ZKLWAiin&E^kM0r6;]H5Dw"qabHpGDjrlg:l{b7c/G|y7aWb*J8:@,FxR!qWAch@*9-*q]P0+5H5*J%KynnQ5dIHQq0=DAdp5k=+?Z>hU6hdu1y()*
6}!!vP?/<7OQWSk|x@6Es.=2nr;>Az6r6+6(k!K/]e_~A0
H?#1LgaD[@t41slP{8P]0otZWL^F)?&H,I}[=)LcJh6U-Y)A&nJOBy7`+LSv}o9';case"ru":return')ev<%aLs&@8GFh<MtWJOLJ:8>-AxW$}SXH<"sS:U<AuZQHv94g?>>@^[t9Wp=&7:x"Wxg5FB+0InTyBcs&2a+6~N*KvItw(XwX?q^W;`9Z
vw+0WD`y
cwv#l4fUGmT7s!`slF7ls@W>8]OT7gomD>oMtYF]oXg+G6b>
)+P!4A4SJADLqDSJAC!FOx3Xw_*61DDsK!iC
L>}

jquFH,awVLVwf$4.q#Pj=3Uxf
9jw(=:jb4XZ*
RFhZD%AM6Nb3Bt!mPhSULSy`1EOn{=J:9E!tID3.tsS^`,wcu`Un13xfo86mt&xPnYK*+6@4JJ6wY<;?(:>J$Ko<ZbrB]%n/hY{5[3doW0AkF,wcrGMP%Ewj<L)YC^1mFr*&3VapV`rigAl2YDgA#`S)&ij+3"rFhlOk5G<)?cG806w=v
m".`utaEo#9v2^V*6viv|f{Zn`dY/P"p"`:99CI@SFT./@@(uIh2,#L$rt+4x=73#T@-EW}(c8c#h
}K-)=^aaT45%A1
$N*xstJ2sO6c
NYh-)
]p@VEM5<SELm(E=0-Dhn$qdJPG5MmD}h<ug8@iU;&bWm~wRR;lrF~@C2rokZkj%*|J7%Uqc<6m%yPo0#MN2c%esR["R7.OH4`e(?j+Xv2H/jZs]Gp>{rU$)Jcq/kOH]S{=W.>:$3vs}m~<%e!hJBjH<"BG(kDN]7beiSM`&B{!8T%4l7C-h(}e^R8")9%Q$[t*36}HaZ([w${9{Nw?tQy:?%2NZSIbA`Mi}sH8<6=
ydy0<&!s!&dyG@FE<NMe7U9&!<?2YX9%E)5"c1]IPpUxw%T<ucJH(;B<LY.T{WCYOx+MV/0o<bFD&^^Lb>R>I6ZYGG36&kNo{aByK@0[a*^d(+B[!A6>^._!(S0c{dIYpik%e7ggLkvFDxCDa
j=wQM&n4zXA+3>%$@2g8KY96Dy809Kgq1^_a]
9!/F3T4hPa`**Hbv67iX{fB$2$i"$alJnCt.~O/j]lUswY|h,pVA]lS[{#Tgs9=6asG7rg#b2taTP(;/1ot-;M@Y:^/tp-SszJx)s=mw#QXITXumu_Zwe
!9`OZ+.Biw,aAL,hgJ~L8OG!QXHfll+)O=Jt^CIAU+%WEPtJj;iW/+08H)h@HdY7i(ne9E*VyX{7W2")^M6nQX#OZoM@KBtl8a@uJgJCGE#1?3vE{7--9f2K%(}#.BS`]/.LW>%*FKfwefOI5fd:rp:!U2XJR>;R>7/?T*m9md&U24$g}O`-3Pl3;[&/XO!k_H3=~;3fnQwl`4M*/pi_xjAf>-Qk(mK30.MlzCs)$Y[L}WNVrMg)Cjt5@_]68@CefG>c&Q2b1I^W/IxWOZ5*sY_2HO+:(kb"CTC$Fmou4Z6w1Lb&enA=]1iN6_}LWZvVyd|Ep%7G2
]0Fbv?H!&62[1j.FtFo,GOz1d
hD*o569
yw[7@6?vmMROD+y58;~Enxod/`+ng1_R
)NZIyAb0&o:{v>q9EZ%li.%frs/d)3XM.MIS-[h]w7Dmw}BO=!d}*twiKdeQ^gD.Q$]W_rkcLSr]!ioq:#H`]lrBD2QZpZPvaW;?jO_&/Z>Ow4@.2mM$CqdG6~77GlvtahJ2Y6YpO}3a/mHqEwTB^L8|?:N<8wG9owWfb@Y|l9
r2~2p9[
V[O6rcamI<rnRqY*Bj.)["2;W"hJ)asKvh;>|Y4RlKpLUH!.vFGsf&i+ZCfehKB*oNw33E{Nq.`41x1dHtjoQxV"+K]?|goFR,8OnMr;d0wUNtl`Le&`P;)>ch|cc<$rJ#[fFyCh8CXreZe%sVxbI]TM61]tUYVdxF:SiiP@d84aq65hb^~;E-9a
Z<ci?.-{m_bOg8+Z5#p+wcqHUepawwA2pZ*`RF@l%_"aZ=Z2bnv9ej)jBbWqf[PX$+O=YBGnP5l<0V)UbpAhY>(usYNU^LYoY62W]97-xg?s
E?&P&x_=f24212y*kI{qsCI]s^*JwkWM#HpE(Z-Ph/e!7ig%a=e@aEDO.h@M?W?nn]VjN)6)~,+4L`m:ic5&Es>&^Cba1/8.`ivIzW[b>T3nQEi/.YSjYDd5X)=4Xv=^xt-p/RIFH;`#B=cR~_~PC0w[3P=(O?VFSMUX!,H]HSKy(*E[r4eL2FCS"C^bj2!
RwQn*hn>M66=]WnP;8jo*^Oh:/3D%u6.@"&y{&suCN:-os=hsC>iQ9J3PE84n@rDPD"G=SEFNFr>VCHmIY-^DWklrxok}&U.RY8WK_/^ecA;7UU?i`s@1J@vY-GXr]&DmCL?NGIMQi%tpBe,*sR]$X"lp9*6IB%ig2d&~ejJ*wOvF<UWyAcS,apS~gG3.Pg_;T6Ut>"qZ/1A9Vv)`yc9tcD"$%+u4]1w}r37k#/I1Kj?[w{<593TcMUR6ccqyi^bqNoSsT`g]o%E}J8e|h4;ctg8ZgKJ[F]+Ji_+&I[AwPMVb)rd<b(?x
mMIN<%O1-!UoE!JfHcG
4--*d@bM7VyL%X[ts@svq58?cK4AmxaFGSD5lrDMm*
LVq<%u(37b,wu-ZNf2AWn0Dcq(x+w0d.ro;,wB;#K/f4tWHeWg?wqmjPM1:EG{+epLd.F9Fs"i"&0J$YC&Z9yi/U(f)95-oJp4Np("pd$;ffrH=O.?t3Hy.A=$U|_RPU9QZ#:r+
jF=Bj$w,G6MpJmnw<jM&1+8YFAx@$k/3hat_E8TLQOho=*7Qo)g;/-"wP_g*#./-
RqL"mP+kg#;#K#p/*l5t9
%q|4}RO?L^`DCeCWFV-Y>k[<gTNodhlCnHE2&pkew`utL?r"cEOqc%vB3PS.q1iU0#]H9>h${2m3h/YUBmm(jn@2ullhh?*x<Z%-TbW$oS.rK?)(hgEH
lVc4EMmi)8r8Ko8u.DVxYmIyq{4Q*-MF!t]<[vc1<ajoCPb<G,/rh$C}vY
ZUtqd>iroRVgGmJk(ns&x<)x}@`WH>PkAb.(Pw$P}Ub&b2PUwW@-p1X0%:[E-&1`_eN"L?HOe(()|;(V%24Y;gv
+/T_=3AdK+!wOa+ciyJq2/SLG2cY5]2^LK
9Dh1^
?!W`02)XxNEktar8pe$t?EdyY#uk7/D2
)U6A2,NRAuJ0=qiC{8D8ImOOPxcSjb$$n;1o_.Z0nexnMlbvYV&pt7n!6_alPJxiE)1m"5ub&+}qJ;yd>KRJc(VO:dHCYTD&O+>6IDH[Tv@7<Kw4X^x,xit%)5v:s9CM%u1FmV|>gL_eJ#>n0<|6(f,o83@]G,.[PVpsNc|O5_JLT1w#3p]f^i^`a+xu8qSu#5OMqO!h|fF?}Ll9)V&w4h*S6R]=jM@M7pT+%rtZt_xXOor?*aHu!0KiJ1$B2WZk.Y4s6Hmy/_HK2c<+O/4J<
wmv>eJXtI#a/@<_9;4UOnLUw"R"a0UvYSt(vhxJ#K#Ve!5/*4J{6fx[:V$XGdb/)cMo[?ijC#s=+EoXYhK9>N"[uM+9,fMmRPyuYz
?hHS[>rhO,OOt?#N
u`D<6ZpH)F`T_3sW_FX]5b+PHFK*R{V6vs"#mSO*KFa0OeDKwtDxt~7`
S(-Iek32/nXn1=PSVnWth/xHA4CW)keZkGf&y^9:<z!!o]$IHItCpMOJUJf&[[#rGUOMb6KiUp4Ih9mL2u3,$N~&PvKs2#b(8hg!Enz`2RmAdFYMXx:(X,n^U`G7{lOK&_>v]J0uMD"r3.~h;T<s&3Hhy>Xjwx)kab!7NyH3yy/["cZvr9QI|2$I<1WmgyM3/9.QtN{*9`!IpdprhXhPrY&Z5y_U
fDKbX>dW/j^Bn08:k4g1`*s]vjO<n0E7Sv
IGx_eN>03=Wx>y>Qsm
!&vwJ;J`.UmkE/M>5XG{8_jA-G4q<&OA_M;3x.^dQG02/E=7i=`3<Nx7bsq]@70=KVUgz$A8OrvdFb>33u%BR403<y:`gT3Iw
A4[#Ril#0zOb3?shFRM5wjMBTzI$Q3McH16u95dD6a!UQ;n`U.X/eSo#,`K2E5Q&a>]ja>@61uV2,|&.';case"sr":return'&c0<%aLp]*6GDh=KC(L+u?$#L05aW&("lFwI4DjY)Re1Z*}bufR5O9JQRWYO|p9`Z^K=BrEZQVXd/e/@okXcZM2
hx[n_s(QJ@:uZh3ntYVB,LfmPx[1^qZf+bYGMTQ!>x,byA65lbQ.MhnfQmBf}U"MHU9@9kgv]C264Le
m5:.}:)sNG4Vs6LdY&Yx/D:nQ,lvgKaGrCDC.f5="Oz2^u9a
l)ezV?,?E$2nu(;(H.B{7ft*ZUG~GX2_l+pv_.<=gW6}hv`W5^@jpz5nlogI5R_>UQ(OD"]%JsFB]k/p!wI:jMo;5ggtgrovlfD]o~nIrFB86@=EOh=J3E_@4"al%$I@ptcBhGQNh)C[o~H-1c"W=c[">bFEHeyh8)xp+pf/W6W6>PM/8VF~Oc-]luKB/^(%`,`Jl~?ogXsC:[D&Ie[321I&qpeTl;@t/O(pv@eXO.B=Ee1..es</ucDO*UCN~$bilrqpzV+Sc#hr[)
qddyG`Hm7},LG9`]aq1lU;F*qztG
0_@j>XirIaFZ,
!OlT*va]I<=M)na:,hCKW+9es"q6}KA;=[0o,Fy>|EP:SF}^%6tQt;<x##C]r=eqkToyM8hu(k{B}EZc3.hPy,2N~[N77e|-x`xd>^t5Ha8PzP[3:[}KhtW(bY,6[?+i+0/hq*5N=I]C;A3BEN+.,;Nr^lKwL-a(PD4yTX0M-C#/8$go<v)xCd}H
.up4,Z+$e&weN:>K-I#B(sc<]%1)J!IP:aD()--e;O,f3-eo<tB}[c#u^E<b)XRDtJmK
`)^!cJHyokPAx4`BXFdW9ej`&1Q-mDu+%7J<f:,;gO:J:M`gFDge!>@aSH#Px_qcUZQE_YhjemO3uY
I6xmR.KQJro48]p!qIYyT}%^!WBOBHhFy6_im>FZd+1GU`5dO[oJ*{NcM3!+Ad2#4*1p0%/78&UpK?88:!iX3M8Vc}eV
]CRYmz)=%J"6&3kLfjQ.dB4xMAl#S$/mB%VcNV>]h
jm/;x4m8Es^pqn@$$aRD9y~s$(afLDFJruLa7*!A}=.c)YW:lUG]D4Pjn:BTxc-E4%l4_j.BT@OODYc({Ff-sKcQ"V*=ek(8F@4,fb0Ki^tE6B79f*v#lvd"oKS;KJtM)_O^h7i>hy=Lh?.x,Nf]YZs&L$01VGAKrZ>SJ
874CsxoRSFTWLp.B1(2IYKcQm65)Eo;`(32GfXoHNf"R,6z]
)ESUg%LoISN,,iaO[k<s
FQ:OLf?dPiEs^je>&3O3*CP=9tOh!i)0Wp?i6=RHhp`dG5PDuVjI+[*xvT3x%x,9"b.5[DxN5^rGDE7k70(Y9L+j9b"/}949D0<4Q){Qa.:SyhMgfnIENng^v`7c.G|I3r;d|sxR}r8dS8u^Y+IALw(3>H"@nKR&t@^Y
.)[n
{Ac^<`"lO)xcY^U1%&Z.wB#+N/k=F@NI6Zgp|fH"0-&A(x7F`1oj/pmTLIxBW2^^[1w
5E%k`2~xm^><L>sb+4m6$s*qyiv=fHL[vb5=dvq:p9OBdRhtZ%0(c>YOc:,1gfgA6]k2@Q4qe=tn.4|?yQ:#(?w]6-]RSYJJ*_Ocg"ZQ!NEl~f162T_$6(Wjtjkt/*6VW#/!4eoQZZ-QjTD/nn5*}9H7Tcf9Is7Hho@?XFGQtojQ=#2:LVCsT]ZD"AYiAAdH8LHf}b|<e7Tds3eh{i9`uDgQsv7D4?QBat]$Bi}yzgME^RmYKY^$$50r%Ue
7]{Q[yoI
xMFY2wor:nj6iE-im:^S,%Z7.P%b%bCFr+BN-7pt]v$&c:6,_%k=S#=@,_@GHCU!QySfe/NV`makE*@n7,9Ghl[(DJ&!ca2j,u5l?{:#w5C}x?ww/s+>c<h9;AQfeC=}-5XAmDuYMm2Nnx]!$T!jAtUrO68cbAP56$h?ZhS:*_UgIIZYA1vo/"dc)%6&((={"V>.&=,{FtAsWVc"OZ5vR_sIMOr%`wsy"a&xpEBr#yD{qUk(A*uU4h`krnugYg?"ISlw`Q:meT]|t[h7b|<fmp:U
273?zO]HHRm>%v-![v=:Q:BM+/nPRRp(0#W,V<~<2B*Y(w@v6`vvS?Bx-_3ni5%s%PPKuT
5aP&wE7F$|4@XhcQM@Qn3)_
1jIDS
?CENq|dosd^/mWRl^612HVVs>VOmK^/_.<gH<?tTDB&0@|srjSyk]
K1(Y[sTe*NF
QaDb]ytC;O@,I2m~tv>C9RMWqh;)S?=([Fa8+[Ydv#W
ZO&W-4M
&Js4Aew21ol{0J*_9)UM=+Rw$hI0s@DT$DLYs>4p.<rIR2`iHP8H:8i"j)HQ/Mmi?yd2XqaLYI*5E5t}97pyljvgcf2%QN*BXUfKUG;akN!l2"EotqFZ@I/^@yjO)o-"cr?W8Dg>hdXc5.u2dM$HU"*Xj"+a?qj[&uBmIfKOxm@9PCi8&7:0NJ6U--%>4MpG%kQu)*)dn~>&CI>o9Zp7s)jSi4qeU[MJo
]{LbDp2M7,EKD<QpEheJh$MPZ&1`GGe7[?Eb4q$<te.Y_?`b&#[AP.*+RyA)YUWP%eH+?]2$s(c>rjxjAoelm@.xrd8;C*y48u_FuWt~i:?60rT~`Ga*uzP>*Bpi;%Jy>vLANa.<Lu;v&M?uHUT_WvcxO"^!6^fm-J<.5!%@Lbp?F-U,oJ8*w|J@4?FF]8(lO$+YE7]wAf^$AOrU/Q_3tG1AL+k&P=/Sl6rCoum}m<
H7o/NXg1x8M]KbVUZ#3@G+Lq|hHY`jQ
Fh?M@vR$Q-.4g5QE-X[^JytbQHX@[yG[AoxsAE,xR[!t~!</R3A1U>,dnO2#5D
MWEiQg!KCp5vj+VE<
X=/XjJ[^e-i-tzS%kV:N={YknO`De1*FH)g"WgeRb(2r9m[lf9)t
|*zjfRRx+FUo@@mj53lS]N"gu9=-Ee?%!^o[V"WK&I;GHp$e.sHohW%h{0(AX,4KaomY6.<<;XJEIown$&ta{n|UX0o;eiP<^n0PtP
KeC1&Ott(?QKO:W0SilJ8y/>wbrQeyreqEi5yv-TeyVU)}KTqpI_y`OkE$2MM:*/7nf#3Y7)uTV,%%ZoJhI|w^vUhvH@M!aS/X["
U@4D?-]vUAMj5W}]mp:[3,m/sazUrYnnaY-j6J0n]-g<?i~/w?z*}`|=yklWr-2K&Ku5Jm#DU$
XixZ.IJ#
Cyx<w+d)1!1+~N%8^j)1{!}ebi]Dj?-sH4LYmfSE(m(oU7p9g0Q`vq.1i,.^4icCSDhNWmke)wsk=[*a]b@TL?)X&+@+:wS58H[ozFF2SgJc&p/vAPQ[6"nxLp5>Euo2jjuMl[.989WmS)l!5Gx3-HpWMVzRarwl
X1=Ymz,0R5i=/)g]wN$Af=U@_G9oUjor+I9{(%wC[y
0z(9cGV6yN`i73q`[W>.-Hd[>>Ulzr4N`J
crqUwLhA!2g5[.In
9-V]&PD^E2b$<?8R"PyaPRt.m3xr@x8cfRufw^Uts4Lj6!jp~q14ogtv8a+whGn<$^py4r{3"*}tNCWLz<6Dm[m)$
=UvTWV_9#3+ni@";C1QfBkb^QdYpRy`4+^!&vMh(:3%[Dw%v}]UM<I<6&j-`-pCK-';case"uk":return')ev;zaLsF,zGFv&xGwIpf8Z!U%+6<8=.+=u9H-n=x"HSAQpm|g<rz=ojIP<n>M3`NTRPQklt3IB_JR3y=lOZIkt0A?sAr?AMn3sG3m]chj^gi<e3)]"<RA]4Mm"2]:_Mm%:wdy`3*;K0%_/GTL;y8T^lcozg4jL_FhJR0C^bu+dJG;
1kU@7]0eVMK1YF;-7bVS(y
GE0fKAR+ZT_FBk8%N0;oz%Z[wIU1Kd%UCo"I6,Ya.LyrsV2?]VqcGj
70TbF1j!r"$YJf:7eJo:vUj;O0IQ:ik=igJ2*&(A%gvmKRH`A8tbmCI|&ZR/^+D:
?"w6TM5DMil)TmD/b<>TjDay|<JaPw%?y)upx5eO_;xy%#e:|J/e2]qb
-&5jL(bajJ/l!vo>WuAOr&QoA}J{rX%,1NkOc85ah7ZyE|:~S~%l[nn}m#&:F))>y@CoSVR1A*j?oqK]0"&CVxgiN`(e@x05#]]`#,sC:^_Pg<[:/i2ADffo@[U+VV&Ve0pvZ5J5"(O;OWv2n}wvpNCqU]L!s0cgfdN16|MikfE3`JFx>j*7^?0SDa=GAQ$%Ok(x@d$&);Ec"MB:"p,rWlI=?sWzD|1W62TUQ5G1-W<n1m]9l-Gqa7:7*vsH0{q&Y/utjp:H>x&I.4x-D,IA_N[KGLx%QX;aPJBV957-s(4H#9Cnf(!G+4%/:oxn-Dq:(+u:74I_)IHbTmvAZ}nv-Z?=[bX]RqYkgPZ^v|?=vuPp`*K}E[Z?".%omC9l&,$!MC@C_1*]5eHiND8Y_el/(@Ee*+k^x9UVqwXAIcgB^a::k.48e[[)QnqHV<#ev0+OQzR
)Lc51P:=smCTib=Vu9fp=p
K2W%OPa<d9lUvO]1XVUt,1ON7cy&fIZck>d*b(J<Dvf>=1=
sx7^,)CsFI8!)Q}=Qf2PJ%[WyeBE|Z9]ZQDtZ.(MOe.@lAhQZuB)!+79"*z_[HHYy_cMaK|Joib<Wa_U[rjF&eO2gBmk+M=t):#*#%yix0b"gFk>?72I}YXX0!M,6OA/Rv9@mssttaS+"mstj
+I"Ji0ODO^t_YitARQ^q1>
?_7+cp!7L7M/K*mNs1Rk3MxH:ww6"IyyqRf^P8pj[[GK5|G1_+123FfX<,Ru[g6a*8))=CHVTGR_IShjxa8!U)aU<Hb.HXK6e:H13!w@pCuYnn;lP=E}Oz!vLEr8Ht-|n+M").QS@N1z;X]g[c!^(rP!H`Fz4<U[%j-%YTj6)cVi9VDd:K7vfBHiV"ILLzawY&lt.hB-tEUfS$Ukc0Q/4F%-vx!s5r>1ofL!C:/3ORb?v-eLU%NY]D]|H$,z-:/
,m,J$clBr#nyS=`D6c8+dL%gjLfpn!gul2WFbfS4JWxKyCnktGK5ku0`RUJKE
"Ix
jyY1HK]^RqxrX>bjFCUESE
:MD6]8{ov5]r-^PZ>]Oa/`BsO"l%|jXsv=ok`;KLBNSLiOA&j"3q"q3=nZx.T3y$;OH<7s.OecTldhP,#Ap)!25Rr#JAN,9<WN1?A8+l(B:`&E|9OZ8ew;;`3496PxS`7L8K,br"Sr%1IHeHw`Fs9JG%NRjj;*<NU15WURd-Cq+(|S&6st+7}<v;mxz(M#+`XKD^jZ2ZFHlY>F]P!U9Dptf=laz(-mfy17s63B2j(le#_(x-~5g$,ii@"&uj0Kh;R%I
K?P1~-l6d@R1<u}&H&;e~g/?*)&47/NB9"l_QBu+%[.%{$Ae
0HG"j~RVSwv_mD=w"D+vS@M^Qv(Mj%G};j<|#$O}Ec1dFxq]npq}X/;cVCOg+]ifK`3W:5/*F?U<V{4Q;x>dA)$-2.H/>2
io2j.VTj2;N4;X+Q/5EY`^a8I%vZ"x+#Xj.eP1:@)72u-(6FK*:2eQx;@?Z[y]6PQ)jBLeLDIuKi[@O@SD[[EX$/3D8$3e$:x7}f^fFe>pl``0:he*NS^^lVa[t:zJ`O95rkV
QW0%|`e-pCoMzx9NV["2yQ~@^RXtynT07r-!csvhfQ*H*^w1Xa$Eb90AP>JcBYL:A.Pk^so#Qr]XA:YPnZ}.wT!Ds!csdg!PtFTAytX=CTzJ
<K+g82pTbgT@R5F%
Zu!EUpb$E^HdM,)ucx7itCD?N(P=E+[kmDybHsHs!CtO!ffCdeN9@Z7so-]jl&N<B&ybn>n>dVf%`pW;M-w1L0tf&DT`dl/*^a]_COB5Pl_hUB~AP>y?ls,geiRc?)@,BkyS9m-_"ra$"V}eSRTae442#m+T1lbDXcE:
[nh*`|OxJ6Uy!t3e/pGha}EDV,^?48ifp5Z4Mhi(Cs86Wd("+j1^,%Jdsk"QQ11Er-k|M-*"I#<;oBS}1;`*Uh[7KVgdN(IQ
]]ZR<Ni?gia4Aey
@fTRsEF_;.Ndi.;yOcuSH-dSLkOkC2<sE4YYI%&?$q$0?.q%^Sgn:qm
5+pPXn0EZm)#.G:xG1@L4mCe:<Ndu)+:F)zavuXb"ebY2Z72aLv5r?Vt_ew0W
=6so,4fp"YjkQj[B[Ai%n*+W9U7m%P&fGpnXRlkfh)Qe?@#&@Dwm`[2DCyDhi-8!pl(9(2D2Yn~ZwIVI^olO(y:"}[Y=Ycf*+*9wsyTH#F1ha9N0
4HD<FW/y(vVbkxC4X!/>^H[go[GdBn->JJX:Q<J4[ZpQPFBSD!LGr/FbODvo#ibxF$By`]?<!GfW:vjQGO@u,i,CPF-1%$Vnwfv=H4=i=+`~DkKqd-]
8|Zvh6V3)u_imjL`:?1.%[MQJL8`F&oy)6P:=6?uJ"sPSZw<I<i%VNf!.q:<9Y.?kN1U)Lt^9(1Gm[[jJ5#Bx@V59lI^b>@pt6BHQ;s9K4rv&t]DRs3b3)?]319GW{tE@*c7z$,(Jiu(PTJ2k,&1Kw^8Ke*zPKF@q[OZZ_k.HA?j;z8jkeG.o/`^T>o"bJuF;3F`iser!s3#7M>@h#-.nm:&vp=HYP1O0-5PyCR|G29X^"1f.gM[lJ,~iRW)A."sG>lM1kwy%^wTaY(gGdJctI5ECuw{DvM`%l!:z&G14w[lPb2>,I4,bn+Lnt;j,lb70VD"X6j07Wwji2#yO6(-/zj+T?rG!]dUXzV`F(AV!yRFM`i:$#5?r(U8[/`wk/#s89p5B^9bN=>EJPPOrO$O2B3jVT,FaB`E)>W}Qwu5$((&!N*Fs_c:sYK+a`mSVJV`N?asKd@3T#+;W~@n>|Wqx}hk/j70yd]Rf`O*"@rVc0?7]lQ<bHf+_:[dGdN4D9)#k@*:JmH<n725K[Xo3_9tL^`j;atLH{Tlskpf:VvHH7#[nyGdl@a3,8f1JK<h1L
z_!dChW%<Ff3O#Vw(/%f9>
b]<4)2b>%KmxA}@&gZ.E?E3Xhwl~hC*z;81XM@g-<gItE:Hr$$Pg(v$q1
H(v2s*Q-`aq|E&RdHG>q+bJ@p0u.7[U`]O:51dw~i<`vBnYg?j[der@?]<R$6;9mqMHTkP]R)e(cliFAVhF,5}r}]1mX_<!-;4C8pvSC`uOVJ7quuNuU^~xN6{>G(@VPyZL:XH>p5AsQyH.fBYB%(bfn;^<>mrVpAhA;&&#qhrT,_!%CufM+8iJS]cGOsf$l?H?xD.pV4m:8p+cC3?
WsdUMR~Va97x@ffuRleA"nMb%;`L;okn(2aJ%rHU1+q;3b+A?<U3jj2IJ]~x)Z,=-X.60ZvAPkll4jGlN_1K|X/*7hbj.=8<#M9fEJtQ9J~Pqn@8W6E!50`#G2BH#c0<,IOZON!"/,#xk3L%ZMVdqk{M_6!Hm6IbC_-^P4|$]1>idBfJ52Tp<2H_*gkf.IqQ{q
/CkZMO$%YU]{_7oYswlIrXfyE|O/l*Z!r9Fn,CHB
sv9pTMw]a`F0O:"+,L)N@MATv=-C#i.xwR*C!=RB~&.';case"he":return'&h_0:6KZ+&iq4.1ENu"<
S:`d-[8t)Itqh{`-P%T@oz(]*f=7P+:T[hkH]f;&$B<l2ReX:To!^KK{R9f^+|:,qW8i7ROR9)I_D-$t)l&,YHGYByBfIZGih&!&Kh9#@E+p.IJcx8Erc7j"glM5eqiHm3P<JM]4*89y;OutTT$#@4fMZ9XgCvvqu)g/u;ED/!=
Z|l?S.0=^BOwu=p8KUWY@8Rm!/T[[dOR&Tp,AZ5CL:@ftkJo=L`YdqW~LQgfQFLEDM@]swt(_]sRRGPQYh@Jv$0j!{`cCL@jhW@"0Ef;/q^(3EAXk9ZugUQCp`ip3/UQ;ioLhKii[5(MO0<qgAy%)Fb;Bep*)
h_dKp,<No
yQvcmT>|%,v+.1&)861!rdg<3xS("xZ1VoM8]7
)f^
:N~[NqiOEgl%7Tf+*;ramYpl[0*[?=Q!O?#W3J8MVV8bzmS52?/O2Qy`0D%mqwO:5X)0EjM<h"6H|R0rLlUY|$[[BrDE%H}wWA+HJ(:])qvk?PX@x4.R#[z7g-0u5Qe^q*H;_ry+
C;JnQaaNr&xv_YNCw7=;Q"2qZ-QfO<Ysuz#i>x)|!hbnFX)SR?:+tU7vsCFNbMYwi(Tlh0Wa2aTz%|%I/hPgfkw>P@R>6b(JV)O8:0m3/NW,A6J*y
EzjvKS
TZyqPw]pr8d
2>P>qVmJtKz^+v4
}XEl"h%j5s46;lhu^vZAmN^/%$L-I65dA#gB{elAPjLeq;%>+IyvxB,FO[zVr)oehLr[iKL(n@=)/;WWN%O??40[J8Oa8=u=s-cQ5?y<=HE.P>koM?R$ZV]9on~"[HJHBY8lkixg7A:VegLJ/S|U!_E,-B^XeF2B|)@/w,lq_?8cH-[Tc#8.nwwvU_v#9pG#_ENb&.M(ygM,"9K4_u!Qp
H:jAB7ohEh_MfS^,%`E`L*x,pC$AQgyj}VJae:~Fgdm01*,*!Y{f:S
dxv*@-aK:h)Dxo>v9pNWraO~fH3}l6CL&kTaImyBZ}eXn(L.2yRea^jGqCD5NpBB$JytST<2xE&CUGT)3HW"n}c@AjY:;:VI)Ke,.*3T5R;T66g6h81z-T<E,*t"
w-b05ALTrR+q9oEAOHw0~f_CU-]OI:9(!P+4uc1DJ]MFp:hH-]eLH
Gx9_2fhAH?2)l_|V<_g>P_
5w:D_UKdg=H;DlVy4UP`P|H07U4f6LoQatqU)p<#+Js[8B*^=%<;eU7jlbgyvN;:V,XYOA>q:*K%f|1

8Qmy~_-&V<X0c6iQ0"lNW>eU`-_2t.G+I_1OTNEpwaemL=Xttw"Y!4_@snRY_1YPbEp%)/mfl-6-dfTPHj.d)c5`}m(9s&N&il{?4M2<&tgwh#%fSn&K(sNkvH/W~o;vBs?jR>@mdV-?voz9do!wo3t;ieckGI}[U%Kt[Wr#$nN?Y%^FX0
n#>h:`uc!9[gY*4}uGeq:0HCo^8!2c.Hw,8X)o/$LhlLd
fRNIy6NNI,Vt;b[(CxWx=#ev_S?Ky>Sq&wL1Gn+5ps@D<RdIP%dSuyZ|+:9f=Q-S7lYgOQ.
)C!2H*.aF%Vrv#-mpz,/^f`9Sbvr(IBK+ScB6eDm<,0?63KWP.ypq6^g7Uvsnh`vvQ@A1b@+#V;`[z7N*)Hi<mk,3;Sf-|DnaYCn)v1`Str[;c:UvijO
f%m4L?$r_CX*E%
i+bdQ!v2-u4,qy="1M!CWf%WLoSc0o9j]`%t9tZqGA8OZt2y&|d??gb>6S<?;zOY7&?|q~g^2w>;;/@-FHqDYqh0EG?chla-L,).*@Do1C2Iw
oDDI
7,YQY8"<#IWNka9m`O(4<>X@dEBIa?dt4PK_)wP#PBCw`h4s/7SbIa/Wd,A;:u>F|Cg-B?:`EfODP@)hl,VaxNOFvXg$M(Q;afhEwW|=H3Wd[v2=,Y.5(oxG:au)K93UhMgk|&%%dgveFatv_gXf9t==N0/V[e,D{5+Iu^?;VD,vG[qDcJL=iZj^#+zS_E@>,AJW@E{+sVC#7*d0CV;p{;:t/Z+EYcKA?3PZNTo3CB-"nTJl51d+0`>^"jin3SE>%_,DWazcmpSllgVrd@dly?d[zIN5+^BbfeuGGW=Tme;p4R-rI>0:M$g9ksFrzo6JnH@@H^V5F1@xY<alLDBo,[0W,]!A.bKONJ+EcoT&_iwJFwtR9!
HAZ.rQyf,_i>o{]a)JXU$CGaDR#%@@@H
:w78]wf@Ox/"GR^d8j
_^ks*-r5y&F;,<cOl2.z=~.L7YlV4fTc?44dF-@rmZS9t06]J(,IKM
Zpg4tt.%OvI.p3]M`Y"uwHon}_uE(!t;oP{^tTbh7Ku%9@a)e2Bm`_3
>d+AoP*r>h|a-C@0Db+v2@(9&C&5ZXPPR6NdF)82;@vVKw`jp@^U,2cW3oW(Or)3>d28)Ez-PeQ*=;Uc1@D
{h>-;,@((t?;;bR?"O;_%s_Rs=8?r
*y9)^f7VdH@-1Y0jBZ[d!"2';case"ar":return',h_09aLZ;$"i*`%N6-#-SL@Dz"8nu6o1ag)=th4ci=zLplA:4SWVb9k
#:e.m`{/&
odaX8)PCGnw)"]*JHtMK{n!s_$>f;n:0wL9=0ltaF,d=Ah>J)h-S:=jq=CBlfczY:m=S$?/K)krftA{Gl`xX-fl`/`#u!oN5VbH``VrEU7PslC:J[Y@RbtfSGxyy1Ze[*3&7o<7g]^e1Y]VFy;J"@+vq
-"<m`FfN_[3{13v~Ni%rQHeMd!0ik;r:oxkwSuMLtF7hg*sCc=pQ+W?u)e!Q6op`*YgS<IfGFmks!2Bn&<w=,[PdiGKAI>y`kABle3Ytqm%ccfRB)"(LuEe~&`x]*x.)wh).HdLVOSiqoqQN*|Xw-bl`[f)d(nYCKCg{m|5Q.Eeh]")pCE-8yWcIamOH
UI}8o7g8:*5D"dm08dMq}9iTZ7n!n;|I/D.z%uNH|yXX=ZH8.gtQlv6:0Rhn_#liDjO8ofz%,fJgb&V?V(F3VGL%`L?ovC3v%
YVY.q]Ad]U,sn&Um5pV
7aG<+:~w1Ikq;.O"CF6d34&9MHtG7HcOn0fY-4@AOI(N)^$:QvT
/MY2ei4*M!@&/6KE*.8Lq8-lp<)aYop?L
{CEsRyGZ`mp6Da&6A[1AKQ{!nNI%(6blh]Mk02]g)YOh"sO.><WWI,LMf"$2i9./GenuF;#QERn:Sbi_87`S;c1ey]rIq"yea)pdp
cb=BWO)#<z%$t`VN%nqw-G<X@R;
O"J?P5X;("g_"v"S9(@[tp=i$Ro)I5?IgR2!A(J6M;0w#,p83d!iIvoj7o2*}FU&#,{AoyecdWt`3I~A7v1NV9"the8l;w9.];x;fBa&#Ok8_=hvuN7pYxd3]DUFgt(#=N#NttR&W.*-VkKcbv!:&(oc<-`Itd./-0%vl?z:~"0;{BIW<W^Z4t")6&j>g^Q4zbQ,+W^#iQAGp6R"5jDs(^+hrgrbNK,<?7959BC,>teQ0/dlRJ4S~h%YG+%8xf#Y^b*K+Xxpl7TTX*kCg!ll38UV#O>oK:QTTNc]K5df<OqsM1@["p,4+g^%qd1h:Cw";%CmgmpMVFB>vf$dKyW:dxJ-v.ro@k7j%/x:#Y[KuQ=ggi.r[`^hF(hO,GZ6(RK,&Vg0BC*nt"znnqmM1T"_)-Q@!?r,.WO2z0#)rn
&_:`XsRH;#JuT1$kWz<Z/c5!S$b$V[XSl]?:Z2-V0CUr2~L`>,UGL2e!-v2RE~O.

YpB)sWJxu@Gq,_R}b,Sf/#AN_[UA=a=!u!?(>3rlKHW+XM6raJELtU&wGiq7><?1)i)#qW-9%cs[`#:3Qp[-4<$CnmE|0)
%Zt
;FM7PXr9GqX+*$tN4:G%9U6^
%=VO7xF}-iq7"nGFh0PH<T/maK;i]ta}(Wxxo^7Tfn%Hh
xR-1)!xxbTg(;9qY[Yb(V>@#+W3qG#vIBKjRt(F97<-,%gJT@3.z1D6J*xI?M"Bv0DKTkK-(r;FA@STz=2T*TNaM-7@fCvyA;/xlJ/jEQn2
mee:;Hg8LXjk
e50kZ=h@5%/DzIq(Bj4]),m&p_*!7]6A9C+*cM!<xpv%[0ehRUer(f=tH]dmb8U`5b=>z^TL(6=$E+j&v#65+,y;-k+mA`;x5r"gd]:)?7=![(@KA2
d+pr_Uo<=2Y8:rhO64r4miT$TwA
6tnVFdS7+JKF0:&xldELne)5BE<<pQ8X*p1gdIo
dJaQa-1T6<%{7Pt4Ec_kJqX9H8
/s%FtD1/.qUMB"(Aac|&4^-c-86EV>6x:v%>Mkiv9Zvc+BkCS
UH&!c@d@0[Ee!mv0s>Sv,N,Slr%"EblXHi/t!_L.vCW3U)mmxO:Q*LW@2Ww6bmum9,fNFn,hErbX_450C$@0^kfTeX:HWBNdspiWE$E$/4gkPL&GyHpHyE&ix]k^c1vG1[*UKfOh4`3$m6M<YPy(J"}FUYq6SS_xQ%lB2rObPB
qrI+h6n|N)_esC41_w3fa1a{80L-]=T^CSfx1HQL$/8v47g7iEsTnARG@JG7r*H-.ZGwDor/.2XX7wiUFAA5)/un5pCY[2%YAA
Uq1mgbtHgdUPa1n;28=?IK6oOP9yhR3(+k5*n)4^(>4ShjA_jRDy@g&H
4w*^_@3fS-nnV7N~lD*7cP.h/4SXdfcy]ohXLSDd#;V6#"4^hVV=p].Fos/$QNQeIqEe4h-P$J]%C1x4VMb]-IBRxYYTu(b<1Yv&EMG=B%7-[3p4>/oL0ZeQqhqXAZfJ(X?q]y7+ogkf+.ug>ULiw|NgNM2|CDB%qaY6_8ll=.3&YyXB,=2lh1(
mexVNxbEtnlhF#[|IDjG;Ow>:!?|-%X*ppx!U,6gF7w%@k+$$7xnhbc"ohIEuGkD5O!l<TsudP=vK-Uw=j^Qq`s/_(BYyg8$';case"fa":return'&h_/Vh%Z+&HokQ1Il$zq{Wx!M+/:`f+#O-&6~6507&/On10"KI,/lWm#NJ}Mxy&:kNa]|Dum;(|d4/hw8runeNC1*w;rQszuo]jLc?SuV"Wng]bAe]hK[sn?&f+R5rAodago"M(6|PHUAB"uQ]smdmAyRZ~bekd0Z.lj<+t6Bf|`w.CW{CPG{Y&?p5zoKm}_Q5PVkOZq!ro]$A#hJ5p7Wd8k-M5<[2#=%x7bQ1~VH1Uv9U7Dgf=f`3XwT9D+Pw
iS6idS^ha{*alc^"H?%w7e_8SMDb>Fbg.~d&M>]=NdpW<mEr8FmXeOc1U<4ot%0r7<9#WA45XPs[I213&8^F)*IV:ln"?@]{^D.RPwbY:~<
I.xFTJ.YE5IM48ZA=s-RuB]6)rVfbDW4A=
We10EGO9n>{7YS;(UFm>1?6OsnMj2c&BrRaS
Rb0$fnE/BW!CNA<<bJ-^.A@A9!O,j[cDrZsa6_.k*~YOY`KfcO)q9(^oBz5CFzGZE@n"RKu<`Z/mx"g+6[5$h8Gp)=W?aWgWx&y<M-Ou"a^E:5v55]`}<d7Dn?@&/5(!$w@le4t9LH$[`>3<>#oonQUjux({-Qyh,&ZQCmWjdvhb;dMUj`v[qTRUA9U_KyQ_
mp8I+%;vfLVcPME=VNpBZe8T
Vbmfx&Ecx$(k-e%2m2F@m;@|a`E$^3Z>b8-S4^N|2_`3<E=g7h@k7vH`MLH&DGTY1z-v?tj_aWAEL)WRuiBD[1+@tTY@VAII
r&V1sVh^{7_[`KTd^iD4$9!cWKyV0OQtlCF0]+kWpat@6yNA}.3q%;OpPQGtpTyf/vvs9
fka0M<7`Mek?"q/:q$!A?g%Rt)cU7*S)rp=l=ve"Q:d`.s[TX5EdKlYfW#QZGJk"jeh#.*ZLfE-.f]/0eA[k%fW)>,47#=om^0sAT92FqXhE^C0m],zk$MT@
xuTH#RprZyO8*`RRA]xO*/3gN*a=RR]%Ec]hIiv^*$on<FwyxNfb_WM)]>xu%CCWFa)eHqCri_@c#X;t$z[ftU^>+5]bKF?`_*nPn=L0@D9pFsp$3<5KisW}i;o%-hgw>>NECFQSK
IX/O:t".Wqy>=2c~#x?!Q,^~[u24wRCg5+)uni.V-$nRe2`to4ag-E9~@q>[=DKAo6CisAIk+0z(o]#fY1^b9cZ{tOj7-+c|aE@]AaQBlgxZ=+
1R(-_f$LSs#(8R5_IeMp|8{5C:e=e7[;cB&<Nf{`:t.o7@M.r6$SyrS6rt!m)X<+*=}A|Ogfg+2_bX#0FYV;~7A!@TJOr8+@^37`^-xe{Q],h:`eDlyRKTx"4VJfrDW"1=>ev&T"XfKgY-W6+B`BcT,@<q-.,!V>(1u="E;NJ,474N(
@nSuC-N,HVC!Kek6-/GUJ1U_a?U9XZx_Ja|*,D]1s-<Cqh1"7,bTLKkj6=V(tmOcJ6O9GKmr60owpNxO$e&,aB#=#be$`4Ti`e
a8vV>n,)@zR(jh&QG)xxCG0u4o3}$lF"ee[BZ*7lGsNNDf7F.r]!3MasdT:kZLxM,r/:Z@8KczL&i~]zcnOw0aPXl)<*#xuUlGc1vo;v.))Gl*m|_42yo9!fP41Zepcqmjm0%?uN0.C%/k?q/}<P)swfR?@gQ&tH1Begdhswm
iLN.QuwHF2Q}S|a]s/R2seU}Wg>
Mjk`Hc2sk!c`vTJlf$AFUG-zgM11/Q-5ue=UO3@]/{ZoD3C^3r%;sF];_?sxQ"$YvT>$N:"n`(dTs4/p-gN?D+fiF+9I_GGC1xFs@(=la+f>P2OSsN8l@vwAX:UG2*KGGBK~+C5kW(KRT:V[Y#(@<idD-K9sV;(Km!m,Ka0obbuF[|d=3|bvN-YE&CJyqxxnB8ws]0M
i[)D*1__QvH)^H%sKvH3AX]M"H>HHUVq/NkceAZx/P_ru.;
KB>J=^9bs6
5?:E[(j_[B4nhQzN#hGi/O_un4tef5#Vy]R0:_#NJW>vC24W%q%hap&f/V>WAeV[=JNF2dr>(;iqxipcUi<?_+OeVTZNpEZ.],T3Bs#V$f@5Ub>W[Rlg])6oxLz^TDq7LH9U;sE.,F`jHiOc4Eq^l05)F%G4<>B1!gDnf%y$6QW1b
z]@utCsX:B%o%(#TSntKf+dcI)h
8XmnO`sgNU~3ZD0VDSCB(&Wa@nlnr?3`|N&VwZh/PE7
ewg[=$-4=CoTk9!r]XJu6TDYs8ARY`f`/M%>
L~N;=o`v.!1^D*iBa+BzL9Si;UUcs_1W3reiK(OX8KXVDCEV-!K`X
b
ZloaPR8n@.0F)W(JFeoI"`f<X~FuLHZcQb22EZTSj3Tp+EI-pX]NQQfS+Q.miu@g9Z]SksC.MD!(Iw%`iBOCDMVdy<4ZJUdA^=o#nPt^h9HC`%[z*%4_htQ:G]u_ygt`Ce0rn$
fQt
z0dY1u~0MW%yYkY?"o4P6WP)&BvNbm$?N_<42wZZaK:Dwd*i1rg?41SY@AysgthCI6(L889D7qa]B))%>Q(EJ1Xp#Q,8/KMt3J+"6e$2^1E7&CWuRCN0IRCKNpzJ|!YKq9s&7jnJ3.OlK=_>_?DG8&KqxJKOZtL%uGxq~SZAb"?NK<(CL5k<JpK%{';case"hi":return'!c0G&aLWR$eX+VPyyq6CxYSc-4PN;VDA`+T$n^**Bgen41D?q_=$jaE%XDss?%:6m9Dn87O=UxFsZfNK$SPwqe%X>c@DsTkB9^)Zw^Qm=pYG!vT]zm>aIyDy~H)cjR9eyJjyJ_G=Q/(m=hqJwEN,eD%R1vsh3vAJ}MjbA?kyz65b3@%hXu^dC"J:`Uot/t5xNw&wspQCg[|s*=Xm}d3=M*!T!"N&l?SnjDh_*L]:^&bm;&*VpG5lpIhlCkumcgs_zby3;VNFT1}C)X;^Ln7w#h9TA@Uqiyyq*WXBJHkpp#(*A$q@xqcU=]U^3n10cxkM=A9_OC"7Vju
5;LPTQ>eKP.qKkmnfBp5M&op9PA".U!+;,2agcoLc#|x$W]cuma7MaM0e6U;,(!B~eI?%Q##HA7JLnkeTl!d)Iq*FvG:&1/(N!$8Q%w-VmpA$H
)Z$ecw+b4UOB;W?}C_/g,(0784aH`cVByK`Q3]
!+ZHP*fvx?WSZZ]L|5Kh/K@"$vf7xW8MbRqY<<C%7gW"@?^DnxeTV_Es5c2V~4Qv8(qmKVQME1r#/pSwBP+WR:,PQCVW8-B,
(#C$.|r[Ip_m"[^IPiNKEudo?
HhlGM9IG6>GUK.K.UV:XbN+II}d-pZ_iaS%1r5sZQI)K<[od:]w0%}+=+mIhUQ1b)w1R,J.=q7)P!=4?k%^Mxf&;Qh7CWMjV(|(=45w7u`J[(E=G9/CL^~uk=EKU^3%nZm)(;~/Vx,[xOQR#V*YHic+~9R,0DUAp;4:
Xz`S`09L?`0CiimRp",g,RfUi~D1+5fNC6Z@,a.=OA+="j9(buK</80+Gu"xsj+#qo
~VE^Bv]Y<=C&7;nPL;b+G"PJAce9SHttztWr5]s(P/-#yR&f@xy9kyh"x2:>,*tub$.(f=73
5]4]>BZ^7e*}3(]4OU7H6VJy:0*3!ZJL"cg!<a7H;HAK#.UXr$N
ONM>TYOioN)5ow`-lBQIJ,W*Ek*<Yy"$H"LLF#e~EjUn>F`?RSZDg{(ZmB0_dNn-D7=[#p!b;rE.I:/wTl@gU99r^<neob
#=,mJg?oBrr.:-&LS*7LM/JV~,#N~8vF-&:B}?0_|q`fu-#?Gc5VW,F4Y9@OO_rP^0LO%QR:RP"!0dp+Y&Ihwmu"MxSnv^<%(b;$GtIXl=am"F:4Fu-u&?+Cp,mVP7e>[kQ)GfV_s>%.uNCH~#HNjhpI.TQf*>>_i"!:8f6XYROFpXx&RO`9
r?o=Ic,0HmDtAE+*hncim&dr)^VQ
|Ov=|d(QISrAcvaoP
,x3C=[&3
<h:Hcy?;:{]+4Xg.eI0AJ:l|Uh*1#-f&MA4lP0@5!L
5S$q@thiYWN6B=v;0-%n^#e4P2h3R%`%;h%O)>JPTT>_&f)5R/B:/!gx/jfD-Izl#$tb4y}c9Gxj9(12GN]n/.Nxfxehg^0>s85pQJN.y$q]lF^].s);kTH_iDhC60NA-A;#YOq_6cLYZ^-O^Ptytf9q?b?(7h9J`8jSQHHh{B8g.MKD?60X*-<:R#(n}
A]BUH%[$Mmx8)+f_r`U/-r)>Kp:5>Vx$p8.wq,-N]"&&rr^h$$6Uji
B2R5yMiFLkEO!USnO=f%cObrqGAUd[5_*C4Rge[HU?,A?Sxzp@?bY}LT(7tX*=p(w8>E_5:s-"=?ll87k]X8$ql[O0c]V[HMmyCpe!>ZNVP2,1E<n,E}Y`A3,s3{mn+^e3+bv_V[N~d49Xs@VP.nLJueP8>xuaHc=_Al6LuC51.wGIW2"YbBDiKKGzx#(02T5dUW=%+]gY-
/wW59xEk[/
S[i=m,D#TsY[Z#FT8.^@9ou.0o(0%=^oSmmCrn4%)L,Z$:b0v)63Gj[&J($)$Cf.M;vHg:}!X=xi#:dfD9[+21"99]Lw"N:TP>%tS1V,|,*CSD1?W]>8<=ndtE/+if227Fhu5d*Pg
f9Q8jpm../"1;
74{*hps%;kb5bVI>.En4FSWoNitL"iDI{3VU^7r-n_ebyGj4OQ><<;iO7WnI-?:ov@nx_M-#EYJ@Hs!5L]PbR]/U.mu2qy^Cd=gMDpYuP(aBBPvI3o_(7[3aL;>n(KpOg?kW/d@p[W0U$.T+`3jN.IAlP%=NhwD^8>ht}!CjE(}5a&B*o_`*`oj"JM[.yqgf%FT8BokmwT5
q<~TXD.Duo;@r`s32PJ`~d;e/5IQO*go.RU5SNNcHD]q<xX+na1`E/8#QpGW
p411AQ@q).kAxr;v.7(#OIK2Z$e8o{Ru$L)_,h0(k74q5_*TRU"
U[1zKCHV8GQtg29`yR&(nKs^lpt9S4FdWPCz,}3}ccdgAk31KFD_
(r4L/+`g$YqsJL^Efe[<^q4<jEym}keq1M-N7]+8|LWnt5t*~Up/H"Y(7pTAeS;wCL+?L)kW<+
^j4hOvqBx`i4*0JuaxteC`H}E_[%?NOJ_UheW|@>F{UOA(*]+$7M*EgMDAiasr-5%?)opF^FIA&:gL&>iGOS*K4WCk>E2ks]k#i!y.,~B_MeTG71;;]]wj^IUiRj3Bqs&,u2a0"zr?_G"F*{.9xLK=`bS/KqBV
3jZ^lTKb2TUgX@g@OuJKnVpF5;j!gpG4b7WrC<Ddy0NJ:b*<J:10UdnD;s,aiO]BV6<GQ??&uVhdfYfqKgSV"h[VLRNV}G<G(nAZI*(^nL>Us"IksV.D8k7`{L-/w^gFd3D9OR/Cn%?T1/(:TeamaFeW#V"0]DZLRJT-:X4_==3kZ2ERO>zlomUwhch4cOEKo"0Zv
/qU=~r8n3d?1K3KjdUELoQZ5S.fkJ!0oiKculdI(pc0n)OpSXdJJdYCppVihrG-aGq}CO-{EXb(N<X[3v/1T^PpdrtRNbKOwzTgvW!e5oT=O)>9lr6F6qtB(]:?@3ryV;;=pg;af#3d8QIg8Q9CsFkp(ZQo2qQ!^VRDQaG9?V>~0%kMv$SNc8l0T0Rw91(zT0$>kk@^]>FkS^s.
)n|V%(/Ca7|)`=W-W_V6!#G(2g8a*(5x6@f%{5_H7I]Wx$Rgcszlhn#lu.#96bG3zaEjcCR)c(Fyju@)-v=QQg
[T;-<Q&GQ>b]^);A.4Y#,~"R,:>4I>6oAV7s9Q@>KN*E!gOP?U42OhVy8)ZY$Eq(.IAyRpyot72Q0J"h)Um>`_t;;Vh<82#o`@U(eQ4t*=R|aY.32%Q(LMcmT%<tf8w_UIdPY99z1%4C>sPc!-Y$(_S@1J]1uPxES4.P.=h.k*,o.+,bxsF(yxk@noR|.aV.N;LeY,rx"|<Zlzuq-0rZH?TOSngo_Xd@Q0n<SBvCnJ;xeusQ64H,UCEou+3JJFS
N"8I$@;DJ[,#Z.x>If^BkqPNK*7v&uMR0/uWVfai,"1&5joGQ]kEm>EAq:,6FxyG`gEpn.t!SNTlt_,i1Or*l%TeK#G}<[67[dn(C)LL_xlkB@3vKd@j/<]<4DaCt/+>*
#UZlH^B%SS7a';case"bn":return'%]^KraPWQeHnU-LHYA#,l33d=r!Lt/K2(&^:<,BgNC

*rsXg/BlSe+vN$sx.b{9
wEr:Rx_vh.;4PfQ#;?.C.l
Ep9g}
Z3B@bx9kFgul2j,YR<zEj)l[B<jFGa9v{3QmRq^s<w+ltq&pl]o4P-?B(brL~c@<H,4*9#8b9Jl<<b0"u,{s0o"^;Eit7f^QQw/x#`zV|4BY=G*tz&)w6MnO%65I1,PXC..6ck~!VvY$a`:<g`9SFjsH>+/o>R21(msN:c^QSRkP^)LqkS,ZnG@[(.P#
03YOrq6u%3dqL"udgT-XG25csO/36Q"m)3o7VA@%i`5yCzX&
%P^
clsN"jDgna%[C<}rFUGc!jz=m){]_2Cr>M[:gZ5#gs11:0q0Tw1&8eB`t+}02Cn?k:NT0(S"2(W)Wmj]"Y9(1PxNwR4+9L?[[H#7(2r#GbJ^+UDg!Ge1)Ms5#V
*!`Vc%uG,<*L6P052ya!4{e^U~2ns~nxrz(4x$=|nW*^b;y!D~eD8^k"t&u.AvmI_{_^ve1&(0a,R$Cs3up"s81a<)d+nR1jqz.=NM#7W:"a[KD}6Dsj8+HUcu(1r&=udG%.ny*nUk:!%A&vYW*pt[dWbMt]f}Y.Ab%Ems
719d3]RC4c&0Sn@tdfwwBU"H<?q)1q`LmDJh?"
^m]T%:n/
<j%p*5GG|B>LP?_7CTzUXi_aCqziTjQsC(6v[EOA3d.U@Ocg8)]s*Wtp->
B=#ssR;rehbrFZQT$e]ylr_t8HdBmIR@kve}2iZxIH9veEouvpL/4;L<)[IGcA?yLYn7t:p-tjxKd8VlWNy83r$E[qmGG!p}ARPrd#6KApm.--/O^]M@#4K-e>a
pW^E1~GgL8C[eSRq2oLy^SZZhX0&j+E&OZvX<wv@M&lCp]_`$%C*ZW50-[6i%mQ^G*6yhY+L`9G3@YwC-iFMUTYOsksI&nK69BU1fO[
oZ&z(nGgiga~<yT&Q[bp*LsGC$WcyQWE)`F/$n,5Q-e:QD)^=#[zm<o(n{Uyu}q<]EDE"&`c8L)n*xjOv$g@WCn[RyxshKN`7.@B;*$epeq4ZHapz"$UgRl:5i>6QNEw15#9/<2?XTy^lGe]GV9!j=Dau|NT3uh:Lf2YoUkiWj9YG$Wwo-57IwGVs:A*:H*RxPwQ+3+D1,g&U%T2F_!yI$_)gYX=6?WjtXXe$F7{v~(@uxr;h$ByWdLNqhOs!EOXyC#
_((e1<uz(OA@:1;^6/gL&:XSSs&F-_,^aEfV#j9?^HD9-SEI>fPx8d,o#k!V@QOk]F"fvjYOGlJaoJ7RhEPsp_xy;=&[3?@Uk>DAMJxutXlslaxg4F7$>{RU=e/}]SKnO3+Fh8oEI*lhEI=
9*VuCRVsjPCN,[b-7<_zPs]G!RMeBkOY2>#2?2=:Nvg62^9eFeuC-Qa&O4FO#CW
^LvDEp0|p65Ol199-TcG[NFiKdK6Uqa3*@mxvbcn83Me`,/##U,h]y@QU~".u(P@#irtYDjZ[*6OP*^[GlLA7*?|c<fhKz,F*Uu@=lI/H>tCQ+3iP[7h:A(}/a4Xb^CKd#M46pGzq2V0C+wnHpo15`eLaJaXZwh+Gb&o`YCFm^h"A(D|9[:}moTnjQToSO&wb0Jcw%m0Hy]"A]?,%0,bG1Ng5oGC0Eh:]_hA<>&s2!")pTo--+#YaSveu<%f)B>@*Z-%yX.Y>.>~2N91LhLqhrkf^fFIfBIIY}j2kj;+,9B0!1LEBl))*L=:th(O"-9:jNwEN0(-P?aiK~JL!3
q;}3~7U2B2,"Y%zUS]Qtuyu8$B=4@-a$9es<lZu,C7QIWi%"$T<Xh56#}x<=s?zTOVB?Q9>McCu`Zqm&Fw9%3#~/J^N^.(T!NSQ16Rk6n=s?nZMj;(Ahav{JLr@
?In$0UKNyF8K/<^aIgMD&oiUFreB]TSBX[IB+.fIJ=M`UUvumJcZhn0X__EvEutg>thk,7t`xmsSUNv=csauH,1bBtjhj1AQGr=f1e:laoD/Ok(>i6.7_G$!}o<+"aHM?sG=s*LPlYkjB7+%7g]O
X9Q/$tmN,U-Zq-)j(a7xjHE[?O$z%#9%`.Jp@!=$<,]~5#`rTgL.s([S4_ts(*KB2s*!gt*<d?_[?w
$AE%M5k16cQtlp!4eB??tei]bEw.E(x#o[8-L:[-T;:Am"<yr:lb!x,`*C;l4"/i5^~e2@jG*gsnlY;R"7DFb#r
|H]?A6iM(I8%SWsW8Aih/do@88u^oR$[VKrXRb@oq5?cl!eQ90^`0LZ@+[%G&:dd|m"%N(;(Xj?g_i2cMU9K{Jy[m6"<GOR4tw-;!/FS22_!Hmt:mG{.$!Vp]2
g3FHr<c|YStZ<yU11[D=[I5~)3O@q4eD`rSdKS*;nHT8=
nF0VCuid5025NMvlXQ^Dx*kQrk>PvS%xFSX0uE&>K!MgN6)zGOu=]0GF^yweyiMulEph$8Zs/,C~JsoUb.Zp[:2&nR?SJ6:fdHmZc~W<a0U<[z`%CzcFe4rYb+Tw%Dp#*z]G&@*X3Ta)F~e@Goy1clOpZ%`,i%FDdYcZl
&WcS4~(tQbJMt(QU^MTr")dEw"*UI=N#3O$em@hJHi>~>ln6EoXQYGJZY9j1i_c+3WB0@~Sz:.s|vrvD2]szCAlyg_4mvF-KK(e)`3;HEfk)<@28ZZ,F(]jUq3$dqlYaKo5cA&V%d|[9n<*K=JLZZBe2Gl-*E.e^B+m^kE1pb+a7X,/_pGn<b4`#Yqgbh8UcAYXA5M/Tg$kP5Kz&,l3!awYKy4t7.bt
:~A&Av;(VoTey46GT9_|H!QGE-DG/y^OKa>8,i:7qCt_oy"v@JjDu&"VYUSd)eu]tb*QIwNwE#,ovH.SKr*E[163/,5X=&ritm48m"r8=z;5Ak4-2_@Dt<t[uLLtb[j*=}p"vXYu=d#".Ku2VSuw2=jvZ0^(p%[+G7bsS@>"y0p/TNCmx#g*,veJ#2Tc&Sedc[Pj>JCd]`#U-]^TU
A_*U]MTSh!h"c!9QR9l/+f9"R*%q<
$PehJ{]tk@u./[EYN13wl/_bq2%WDZ6-S5yX=|6dxAcCYv?+/fUQ*J"MQ"5
cKH=4e!:uz]+ENdy;V6a)U5X=rkF7TKOs9KvnRmm-?r"WuQ/Hb>%8yNE>puO>1/VbNoOcn?FO3CAS75*4:iL02UoMYW*86n5B!?R=-fc;Z6I$FI>Nl[K3:y)fyV#!TKT,8lmCv9/Zur^"#bjsnOAtw*9.k^xF(f[h28?R&t]Y0vQNl$ETiGRg(<OZ0!,R6h8]y7lOGp!WX8BB]b5PW*!>~@Hqhd-EDZhfKBd;M1[Kc/KbG-Y`0^]":>$eXO{y)vg:(njV(9FM<.7J%eIR-?&bKJDQ`GF`$9UGCj5`V!(Kg/C_u<NO7TzA@vO$$*<oPJ
xhxp5ru9;It2fHM?RvV"68C8wzZm[uhhigICNdEn%EUVxc9WFXi8Tpg4hHW7P[
TjB-=&8r,I)Nz%TM-V[4(yVApTzs&;F+
a$Gfr`2lHhP"&JtC>Qn~2t7%WcUF(loWO(==kq</,7!u=rR&8$Gn/4Uxah^GD!CI94PNkcI8l3W8$zH1,)J`=x8>8o;7UN7YdX%IyF[H7!ok,uBX?Z@F-QZtZ2IXD$0W58iO';case"ta":return'*h_KraLZ:eGw(lQHYJj5Ew4gs--pN>w[&v9fg#tC.bg-g`U:z8$yt83uC7JPWQqmK=QkI_v(Aj=&u3lMq;NjD1?4.Ii5>_B!JI.Ayj/rwkSt+L~yPXo^%J4fcmJj0c$tky^2Yb}wPilLwy5wVbo0!RN*X-GSR*`J2"+S^!Htc@9`Xn|Op]~5E-ui"[DiVGN3`=JAwmDeIFQABydS,tmHk7/y|z)^/JWw0yw@Ar^EK]tt!*]wT1>SrR&z(*>nr%1,d]@JuddOywaeJF?>!W6P]al%X#^yb]f3/hl"zL[iV7Gd5PyZ+l~p[b8)a&z5VNkSuq]Q:8F&6ATn,N_)Q8Ji2dIa8Gl`Y^9xS#~R&g2Yq2T46
WS_lg1e$DS{2>fq!LBTQo.<+%ba"]2?6Owv+QwTeV<!2!>|h!h=AFySn
G,0*CB@+NHoGIa?q!RY^.ZCKU?T?NhE+a@w{7P%x=WFh#z.iw<t#]xJ+]%Ozs|OVqaQ3E3;5
zsG_K(B(V(E)^*~2$XT(i&tX/Gqb<Y-W
U3"?@j/sVIy<tpd
un@)yir|.*9EU"QEn>L~)cmzX
S%%]fSSf-*1Cb4%P=#7.o;Ym[E!6VT`"_DCLjgOjXBC`QT69R&3kfvI2rmQUfDwVp[L*!^lmoAkAeW/:j}-KnwyQm&avE^k<L`.ODMbS_u,4I}L=Jsu)0?8NDPmevG^>:I$z0jb(/CP(VTLl!!*qFI`GlTU48jbf>Yt>xHQBl_AHCEL
yQ
55jfx<oPHxAM~$WpOQigl?CVCXDna2#F1:[+Ym92iN{DrD8;l/LT^+=+y3&unVb<e]dJcq]waR%0RmR@@b%fgEuCl^@*%ij..11WG
oTX&t3_*!YaEw5lJAaMa+3`vzz"8<p20!Y|_k-|#;d5F+xI?Hfu7Z,in6jTP=QAPZ=$u2gn^.I|(r=aOapDTXeW[G!e=n6b*`YNE2("Iog;nrWS66vzyN7FNL@U4fGFH|IMkN0oX&bQL|]e"@54,~X^u7KZ$1MRFp
4)hfaeYSXYK-ksXY-
{O~S,Xpx5-i%w?X4
Xa4;mf[+G+dM@D&y^|3U,jLuOg0g;-j9OHZ_b%G{C<q2oGuPe&%~y1ucHs4qqx3>KneY503Ve"4pWEeZ%*cpA
aZw][2Q8M:v6lAja(5_ia}J~>;`bUC+Bf/_5Nz=AjMQHA%Q?,3^G$y,2wNpF]PqR#{wqNWw=PWG]2BDc,Z_zM%D`gU#W"LqjQDUNuEZkC]QaG/Q3s8#k,DO*X5"Li2g6Z6<`=eoO5BXFIgDLmfu)VA9wu7sn6+^*!;8nGZacx:t
M}!z2[h0*D@&(JT0XdJ|WWbS(Nu1$0RRxnW2ggVwRFP=ggR!"ffLhem[&7@x9g:
WG@}.ny##{97C=icgFJKshMNir$R8M1k3OGk`:EvEswpP}(r&kRhJxH^6|5I0$k`,J^#y(QfsF%G:JJS,YIvJ7+GKn?aV5vW)G%_h$^&X}_]-)e]>JPdgl4b%PLa<VqYB(BfwlV*@OA{BDBXp#-!n"bcWxx-OgH9.MDq!8j!?qLcQ|G>CT6:52
lP&@Q<QA8=]S;cV[:i%ZO*Id^-|4ZYLo("l`A=q;J3nr7,516JI;!D]:<@/;-eT=&=v;4J4cgk)+>904sX
G=)0?]/f>Qs/Zr3^0~^N"K)8T,XfX[^5TWRnt1GHVpb&nicGQPxyb:`^5bcS7,?W)QxgVdEH%[++%m;iW=UhV=S;*a;rug7jgr-|pJN4:h:
+Sf_?J,55fF-L#Q)]=Lr^2LpBQK&>9jePQwJys>5.Ie[9v$[awZs>Yj8R1c*_*d8YR9t>6i,Yb]q?5NB`3:|P3*HGAOgj4xok=!O;@<B9X=93qjDu+dkYLMkp{wB!J_)v:=4I[;~B1o}@l_HE{x"Cn3n8I5CGes$1:NU9VNA>o1q;Q-yG<6wZDUKM,$MmYpL;W:+j{$`,{m~8{6{[_<LWxCs,nH{69ws
;;Y>)#w=j@BJucJ`u_PjL^C.0"jDQ^G1j0EJB6%![&w]U0b#q^#h@eS+TH(i5Os!AA`Fqp5Zw0ne/v@>R5|=;V9+<E]
0"eeYLcj40+R[3*!ua|d3,s=q:4d)6>t=G(^Eu*Ql#ZIvoOA3!bO*;/jMP^=ko>;Zb%/C41OF_;@x0%,m^MK?xmfeI.e7K04^..w?v{?PG*51/Pt[:~2xEnRiQmX}+%Vl&HT_aE=<HBT$DhGwMvBNf?PK"
fCOT,[fpjkoi,enNRu/lr5N!w}jnZR_k5q[&WTlD9zYEhQs;rz^N/4"_lyuN4&xW/m9)`7
tQ+8*q}pg,+I1aK^[X2&ZQ}[g*KD
lRYzZvH|!Rym2Ikti:yzb~8u$S!>U!lUeC7+uZ+Agf%s4O/t)7C-0bsu-{jva7ASw;9l2}B=A2b$u(s.`XxcjPBg3@7"_$&eC]EU4.m{gcr&J5>4)Z&WD_1TTjWf-o5"urSf(2P[`[e5DX6:Y<g|+hav4r^pNKAe):lmI;Wg/FVG/=Zn"_W/P%lP$k3F],7
8,Ux[OvXvN#/T!+O]%XLiU%`lcmi7{[dYt=0,at*N="Fl(VHv-u.u<)gQ6r;W=tj?,m>)}bq)RX>hK*t9OVrIZ9HHG>uF-(_1#S2CY>Eeps|_.=xL_@m_[Xc-8O+NchVT2"!t|K|caDsSya&8

O!}mxM,R2Tx4"5Fd*px7}J+[!O|,<a0mC2(J.txg,S:[9Z~;Nusil)I_l]"x#F?-A
XULADy9gvu5)RtK)Sdi[w
.#H[A,1.!vCZ:Y_4RyK?Z;-$t>E@60@wrb2<Gl|CI
(Ub.>><g6
,_D+g_K*
-He5Xz@$qAYJw5*TOOxuiJ>_nCHW&dVsWNPgPFcvPwHHdrxnwSviB[WEJCk[R=7gBI_Zr^06eS?9dbuJcvh,6O<#dS=XY`+SHNA:^?Qc/z)vGHUGI0XdN[kA
p=$JAAiTn6V$w,ol<klqY@qGMfCCocYFv3`wV4RMGri.#hdp?O(S,/l)KWvV]I!9u.p=4/PT/FdDbBbYM>TN1^!9>Mh&[VG<7L=3s2X)6A7)nLuG#wk[{&;.dKi&j(;^]XQG]4Xy^_gZSR

0BG`iIl/8KS=UlNl2Scv/(jboviQ,"F#j%$`5WG5
<XTMjlWs]+q#F.$N4+28a,8HqXU.1y4QLCp5H/+x6EYtK]a0+xR-*P%5cUD&BMUt]-w~L?cnwA';case"th":return'!h_F;aLZ;&iXNGq;m2N81o5_G*T^ITco*RWT[q{OO#iG:=k;V!aHtNVJ;2pnRj8=!](nA
wx$e%<,CUf;J$xBczl:DDn@^3464F^mMPjgp7rX#@
c=FBld|WXS;S)w:czQZnA6VagD27gcZ`E[fc>JCl9c?Tdi2:T(EGMRP)r,
:UF)MZv+b>K[#>:xsXFk=UT!K>b2ckZH2ELT:@jKe9uIu6KGu{8*n,cR=-,:B&,^(*0|8sL-maIU(x"7$eE$.N[;GvNiDwGt/pW(*gUq[/ya]nR7@(/Y+w6
/}fW;bpQ7k7I@*xu6*6^bz6XJ~*dSO8qq_!vDO"<5DNUN&cspbvSL]&Rfm1}(z%`IU3L2G".FGWQxOQN[[_XdTNrm~txo+6v_%ABxSHyC0h`c3u)euX_n|7Ee]>z=/Jv139[/uh~.`)A>e#6!>?vO&(!u:nBA)o{]7_]qc3a;~96RN^cbyf})Y(?k^_OS_H5Yuh
@g]3sZ_u]]a{L"AENqG
-od-oTY3n=6=sbu@1DU_QFr[0-L9#Qr"0*
R"u1-7NIIiE"YOVgYGGIe5{FhG~#:`4!HoWa$RB[~oov}W:FAr,Dn<|<HbGDZw.S`
hECA?MV84&v^5--%
NkD^Yt$oMU({o2`RI@DMt>:to**uIO)a@u@jewI=]C[(sH;*1q;x2-%XM1kwo/hKZ`JO@<wQnFnVK)EJXL9.bNu%mm>A-**TQ)Q$j2
u$h(:fiO."sWD=ywWEx1-1Ltg6"d/G6R>3O=^lHTGr;xUbuA_-fwM"[Gw$=h{iK1
EPa4va8H_IR?HTA!K!90.{D[C&!~wiTyB@`3j~=&A{$oGT>aaG8z#vfQ!)x#!URgU%b)=Xu~3;5@RG-8YQD$YtFucfBWIqGwE=-d=$JIat:?<*3qom-`i^o
)4(d)t*ss%0x+6jcZtF)p]w:Y9[PG9%w^WOeF0wd%!B&^!<oyI?@P3s1N*"xxnr=aLm-uQZPdso|YV69`@L+0t5@SI,e8GL8[Kn^IhCV,EWvZ<Y%H`VFLQegCE)%q91UO5d>(ybD`?A]/(MlS|^EtV.a[5up,bmU03?s$:b/HGM}rv<]XD"F@qkr)*<3)`c22"rFYIF3^rC$_645ebb"lrS37Qi0,G[QM:Ol-VKu5T,L-:;.Zf=5rOk8A#j&/pRYTJOj:/rD@k3DGC+uQ%D<?4.=);txc8&51]13->Q~5,*_$d8M*NA07ii#i$48[haF@b2"gwmjpM?S;<xvQ7nnd--N5yW@P;5#(WR]dxn7G?6PaII$H6&T9ecM/U>YYG&S$usMvy,iClKUqs>z/#
Ckvicrs]^]u_*d+UKrr2k/I;ut6fcm/!(Lz%-;K3.m$XMi*)>cpIRG%6(q2[ZW5<"Wt`I4Z
+W+E)RSJn7WnIOyp1w_Hy,o9K,>Uu%UoA(4.$_T@%Tc*t[Vhd`
CWZQw6UW;_<2/c/^gYqz,l)#Q`Xq=l#GZ@4/(*Ir`3+3*F<v*)KFC`!%sq5[fv4zN@gXKoV~*z]tl
M}het,RkvsK-oFE@gw2PadM,WrB|@1p
5=vJkt.g=5poA_KghX(GT}FOVs!B*`
1iP)QbHjFL`.SR}@JA`*<`hk90
djYu&ExdnIv^;(g5%PW8/7RPhl3HMCYrCaL`<S9yj(
Kcz-%#/s{"1EAO$Rw]&ubO$
PI|utNn>0+7cL
emeN}ECe(l.tfJTF[e=B@Wz*I<*PJvOFN),Na5(vD?`HM>M%-3X^(]ChrC15rkfek)0?-B!f(=#sD5^g-OB]s$,c5xs`-72MnlJU(8=KY8/*O@4El^b%DvNJH0
XqicH}a!b@:Ddq3)3U.2D)x09?-UA;fvFid1POVi9g;[a$)W^wHF5
^npji4*>fZ?TpC)PqVj
a?o>nBE)6c>h.JLQVh)S]B<GlzkJYm)7Np9}k!2+k(]s+|Mg^"t2f%"]<@2K5O9eZ,LV:Ow0l@Q{O(G=i-`N7C.L?Do]^^ayO_dlEPgpq])OYWB}*7"rj:oq.]o0_UoOerW|2|DO(^7SA..`_Rg6nO#u@Et[0vB.N_<$61YX_2_jgGmE,51D/1,}w_ek"j
]l4^+^V!!clgSrat.sjuxLB:b-mXf29iPBR5<peW)bU`=YZ<k@]2nJ6y,vfexyxd4C7!^d;i?S+oBVx[+l14_xFKF4.6~hjSQV8K@Ur??pd=*3v#HnXAIb:Fv@eQZ&;1"JdD`N7may#iPMMuj*{yH/*i=>
(;cq4ivfBFu)ROA<bU#)0@aDqInnv,-Kj7*yTtKqPv%6-]T</jD<7|Qr-LT&$"nOMnU:&=hl)/?hL.[8#ApR<ToIG}Txwr"u"M;m$.jOs0cJOyu~/VZs=aj+2Or1dAuWgytIi(76>=I.TC8815RIi$V`aXFu@4y0L<HcF%H*5mhZ(YjO].1Yu>hRQorTO|%SR_^ookpf_TX*sCI[u>r9a"[c<LqmD%:`I7=2kRkjsC"#QI#"$LsfZ}"E<J=]N"dj
fw%%)REmuk$PHL9LXFNaYh["f:2#>3csoRYHp1Xwtsx@R(O%9;+fT7N^Admt/h<C/?"9rTT6((]bD8KWj4/PaE$V
hg3`K@]#P-]5V:+UmoeO<OhS<g+[FVS1C/$66NH8TSA#c`<)q@t:w~L?2=l%maw=#%';case"ka":return'"c(F;mTWGgo[I42sW#CN7JTF)q,8qq0,x"s<Q,%4dwheY._fA<Nx[4>cN4L""C6-k0;6+ar%4!YOH,yhc`xrgy6,YkKcxrq],JA)VbR[#6z?wyVM?L!kYm;6J6u&=a-XFq!RR#2IS=6pNnl(StT6P#8O8Qw!I/&*uw`--O%n(g~CC#$wO`0_EV)x"YI]Qj-/CFk+I&>Fz7S+dh<)!NL#=#DB*okAPM{^fXFlpkh5a!pc9aKu7QOM9uKbZw+gj4`bBp-]q(T-!Ip9Ogvq0NjG1*$4f%+;[a%*Q+G_0?tvV9LKEt+J*`5)O5F==BZVKfr2i
O=[n4MmiwZBbcW3qOX@CV8P+["
:9N_d^cdp4%}2F]r^K*el9US90-dh~RQ,yA?bv`%u{>%,/Rns^1Pa{+:q1&K-/o~2Cit3q=(v[*v/G#=vXPYEG30j`oAY:<w/_CY=U0sSi7aK"s[CK#$tIgn(iR1R{c$-!c6TAp=P#1
Cm<{QP/x^sm$1albue%-_M+QWQXDhJvmVe2$gG={#nft6`s=Dgd$J7+Xv^lbrZ[`L"U>+"_!8$ACa,2wK4J+XiJjQdY.<4n7p,v?$@7CxdW^t5Ewbq4,6BtEM1wM&fGb/]:_UE^7818^eEOLu9S_q
95Q#&)IV%5O#u_Dy`UIh@#8.^CVN`P10!#s^:~s1ZJH6A44{2
f?)fYr>Z3+>W2"1n.?5;X"dj1g:LPFfF(ZI
dqjSJV0#FO5p7QaVk=y63P-g!|Cl[|A/=Z,`/q6J6X-UpJ1_/c
pRlrMZsa-lMt>1RVZVsL)RFhqlk*~SLJ3
z;5*r+F+%B:
`Di6#Xo!uFL(F5+n|>S)b`xQ6_QKz
2%I@/_c%=]G.eGuo<x`M`Jp"Bf!]Dbxp
VO
Da-B](+Um6~B[]DKA-^-LsYBdvY%O;}p1OX!j#@.jc#&^[k@^:G$-2{7yDpyRg&-ivSRZqm=75x]?WDO{BXo23`9)/[4;!Prfk6eVP%*UJ]P*("h&h8X_25fEX]0iBT>~wde7I7-BAMUL)w;)&3uSg,C"BP)
,~)T1lL
)g+|5E84"=5q[MRB:&i
S(Oc

Eo8A;!K-#~]x*>.DMM`<XUuclaO$xD^tgG)NP_mGC__]NIem:05]4%L2/?&-7]XG$HK~`U5nx,O;Zl#ABc,v@h_48u=<v7n1]297?`8P7-7)#VCD.U*{pCFx95vv8Kp$CkKI]q(Y(A)$w3o([&eS470;fd->r#r-
5heU|)&[vH/7-3>muJ6+mJ0lK5n.Ak@@]0CfDKF3p5M(NqG!Z0uZ.$eYYWHChE>l1vpj[im
Sy_qtHWweX-k!-J@k>EVU(th/Pb({lFWwJ1[L$~lhwPVPknw0R3p?moCKO!qf`(Vls1I,-(]6qtgh[2,5@n*Wmv$.I.ZvquNxq=xf_e2MEU
}bt&$dan7SiCsjj=r#c1GgrtUeSE+O&I(dkPtClp,CI.69G^Wwrb[T~lpwTR[X%6
wc;V_~NLp2>#DKZ*YpSWOPb>9R<G:Xrj`2Wuuf0dXE*{uYOfRNTX/[*W.&iWgtRnj2RvSEg3o<dF66r}&88o9vYfR:G(=M6!kR[v3^!0&<RJ]EMU`gRx<!B!GzI@jArhP>Nb_uo]CSRuS
QFeE3jym2%5-WkK!gr#^gYL]K~qA#Td^)gl*aoaI0>U.f7B3O=ah#%h<WthXeR3$14P8"|!tasty:N=VVqim#*`4[(1h;ROw.dF;2klZU<%OI$42f84SCp]9U&Kf.bK#=B!d7U5FkR<s-7Wxk7=w:A85X/!:,?DBYvFhY-pjQ
ABdP@V`5p#:WeG@Sp~kc
="4
O!"a5W"eD%,MlDq_l5tJL,Ne&vDAga0nRRPPLi;>#j[5.[u+$SnOBr?UO0e,yF6NgT<OkN~5>vp1UC*M)loTaO"w,/bvf+Q,[(jO|_},Z-nRYXb%l5x<Y6pPnFQy-kxepAV>.@vE_8<>Ug.ny!zU/=Y_$q~<q("s3U]9:Pe,1[S;X@n2Yqzjp^GOJ71W~+9NQFY3/-]kA+xR29`*

+_[:O
5W02pUPmxOL_.R$Hiax_u.fQxgNXgTHu%;_eCS
V^IDjf&T4K
(v3L~)+@&[Vx@E.o!H@N:7%v-7qM|8,JxV@%lV1u9<^)Ec_-^CFw"lNgZtl@B>$V/y6Zy&O!%5OW4]t0"2in8NXT2rR-n:dl#<_*3!4"K)a-b>)4g:fexWN8[+2JcX+X/v?TpNgfy>^4I4E)4p]K^$>s]&cK@Riei.vBn62<po:]7r}pPYO1Bi5TdSPe!9tW&S|3pC@31K+iV
s`k!ao)q,w:c.<
nGeV.l^A1}H2dBgW=%s<Mu-fD_Z3=__[VQ#WOu6eWnwVU7SkO8<Rp#mbq:$;#>Qq
Q8^pb6tQ*-]gF0&qsy4T+Y$d]"te=2RYw<4

-~>)K172.G0CyI]o0"pw[4<VRZ<rolF^-B__08P{BJ>$bIQ+DjuvA9+NT3o+08EavzZKu[QVCh*3=Ew..32t1k?2);Q]o}3:dqW(Sm0!AuLmItS(r65i(y&3&g($,Lo8&kM],ddE5ld:DXvF8*k)=n;n1hOI=]giE,hP?Iv5;O%V[OS]jVF[Ax:X+v>E!5^u&9gmq9<)+&&.W{g2Og.(Dv#zcc0VE!UAoxTLA
OD#~]}f&9"6PR5S&9k.U;ye~iQqg6w6DY9wipi6&AIH%7crl"+9Q2Q^zE&WVb|E=57xyNY7}RO3cSc">gu_;DheO?T*-+5c"h[z#w$Y
^NW8M0fIe=4RaU;`Crh}ltZ~UJJVt~Hp0aE|"kS*,0c[t29uJo%px~^4bhW6,amy-%M%%ZYaZ7EV#tPI;8!nr"UVDsSFUm0vJb@Yg
2l.)IqFz;3ncjnW*HGU{lSBIDD+-[!m;Z59#F/hV"6y@NAi"cpj[!|?_QEqFU>LMd~Ebl>W>MBj97Q;j[~WxSL7{>XTxPhKpk%pt0iBW[wG^6oKZ&CYb=qZK7}Tz%n?KJ47R2xxAml0:7x<Wgs;A7)N/oOQPCA3ETjIU:PhsWK4=t;m:nQ02b]r2.pZdv7.SNXmm+j8SR!$kHWX-puwj9]+C`jjk2v&x0/4L`w#++{EYBc$he"sB1"]uIOFOQ=f{2p[nLYB[m-r6D0AJ.,TO0DP?szr)k]
<)6n_(@SSw`#5
=coz&Pp>o;~Xq)~aD-L16w4Xy&tb[G~F`ChwB';case"ja":return'-Zu5hbSX%1jq9n
Pmb(o`3Cy]e!hP-/gVB{SH&,dgSWV0Spvc<w<wQ+4tK0u@Jx=7j#n9!`Y
6B^TCR;7TJ9h1V&OJk;&FXdT)hJ75Ep*&>z$CkThs|S#^KS+Lhu]i6nb7]Kw!nXbq8L_A:K,hwt#I0A~Ad@|mT]+f.g)&%p*SXHIx301a(AOs%rus$]C=X_ByAf&Rx[CrQ:;X`c`[hRxobo2jvWEwk`!AGb+LQxGI/5g;5eDS6y_=.e8OTx40)={GB%p!9Xu<>m/Qd)hF#t#Wj>qCddSxv(1iOTxf%Q~<0?FxyLs$7HBV1MK6&%Pcl]Yfu^nm(P<LlmWc=?&O:1dbUP67{7|=~GDKm`CD$2X:#.L1lw"bl2)m:1*ck?NhxsRw<>k)qhwf^Gp97uNz#-:qv
oyIT!BVsIqIGO9dVR:Kjs05L?cK(;4.kKKVa,w!fq+X*,Jfb``usD70c_G7etrUsf$+aZW~6a:2n_b!bH1D6(S>qism3~D{mpxU4$b6B9NS7)*]8Ro54fknJ
t;b%)eitfq%):-J)M,bOmHnT^x@r=O(IvONdrn9CU_JNKjNLHlTAav:H%bH38-jK-CWxdJ[5v9co:Yfbjgbwqbr}x[R]`+6NCFX"kOoq,w5Vw&hT2ILht]o:"9HDH:s<iXql!)E-v5amE~GU#m8f[QJOwV6%%;i3!_T(2;x:O/XJ/Yr,jzStP?Z(GJd#hKMc)#djE$mN#Qc0_i`1>n"n*Jv{x??`w8heMqwjWRl[JV#RIpY^r!"zArhhh$,H##3bQNi[(EsKR:m%r&k&y&n9q;Cu/!lm1XBPHTz)w>o%R&J0Cb%[8
M81u%u_gnsctVeM$TClyD]?;XMt:Ubf)"_pM&F(IKi3vO7y{#jbJ*:e[r-)FgaP/t6wVd&&aD)enyH0zLWN+E]kd!!s}mtgt;U&i@Q!zmc;$h(!:eE+$<6sMBM9_Xl;"n}k.PMff_7wj+>,m,c1ReN^:#HTd%LObQ.r*!`=)%4j>nGsZjc^7O4EKFqTR8$uwrpGS6DN?m5>v+Yq25j1Jp*<mkj$l<-Y)]`Yo8_h{@CZm-WOKN6@~JB?HFcE/&Ud>s,g+c*U!wDV/QAc.I>bC53[f=%+dKgC;!po4MB(=IJnx"IwX-@bsSM>sYK[*5o"6F
2wh_>5pCSYqNK
Cxy2-<dg.0sm-"]RZQHzwQ/Fl"If)SIl6-D15_3]@4"r;jFA_K^f1Q(<9>[U(]G*-/A0;vj(=0r)DZLD.`e(P`,eHJ,2WmC-#PuNuOf5L.b+>lYdZ
.4QEq~rD/)$#o8-rKvkW0N
3M
.Ls+MGP.sivX$|"V/}XN_z
WaSyiwna_3mfO[OyhH2/U"kRGcld=xZU1ltp,C<IGqt9+HmX/eFf#Vql99C8di%lXcAV=<:dQdXIIlbE_k738dmjr]mJa[^^Nm>w4$y$7CdZ_c89~H@[Tm`Ir8dg?TPjKfDPWYxj^Gb1vTJ9`nZf#_.w>w#RzSfKPw.[kcNx&_^7L"e8<@=:$5nt6-niohn>+dvM*XSN9=ZHaL{jjnMi^V|:Wk*e{yZc&I=[ar*gZjtM_wgcvHxAR!3YA=#K]F`=SifehnHlUlaFRtz,[:Bs>2$Gh#B$H64(T+X:x2]#NXk#[sGi3DMQujVg=::LxS]89eD;M6kF/*i%a$si@T1YY-@,:R{C~O|^91h.&&vvd=-d@*!84(t]4j4e`;6$#-*&L;8
[9[THOtiB]C1b,P*wy8o&w;v~^UbeG*DInX@TNjj?sPwh<kACo_Z[0T%Y
$1X_kyiZSaAsOqK>4W[#@q$l@oPKpam)1omHEY.1/r[k$0@KCqU6K0>IUZVeI+rYt))O^UWtD]T(#5}3,Pb`e;?crP,T$o:/2s6%H#Wkf,oYa5k=/DMa6[1aO%3hFY7T3/e.n77Qs^s+qQOb))/?TE%yc/n:?GKW>)~nSa~MV26Q*h$3@wG`9Y:e5/O+nlw
HhnU4O=?:PxT,Fzx*XH.[%=uq;U]B0ckvYEoa#C4Cr&[b@nqfYc6m
ODY8n-f30T5MByFbAy
$Iqa<8QY;;Hx,9M!
D348gbKW-?HEt&r@$*8*@?`j|)#R;qYE/ZlULRL<(hXG_txAV(l#Lb-6sO{kj(wY:T,bx2[3!.rBi/DY#O1oDnh$P3%P|9t>1SZR~b6tK0Q
B@R5==9HO"h
&;s&@={@y*V8R!r^yGa/i$+p0ix>Joe$p&+Fq^%haulG&?AX<a},k9(XgP1va,@V/uUvGq7UMRX-T%pm8H<>I%$iT%EB<hJ@}U,6:?:a9NOO6uO8TVu^+1O6/_K&#HN;=gop6.-(nOZM%d>UVo%@.p0OsOyDfkeZ@,)Bk=@4ylzjcru[0W<xxDT/gxf.%iDhO7g&jfqh6V2gqSEK)_HT^_:#&;$9}em4_m5#s;qXC<.DBKT5=""[i4m=/@+8B+
wG&wRc4G7ff>LFU/_$3(kIU$`lXn
7aOhJk8a+,;2#3d6FOSbs
hT}@*2dO$273=ji"7aZDv]51jFchFLx83SGWUu?@}UuL9!Z0WdKG8$@R;U4"RV!Hv_Ue(+{T-f^W*75(};zj<E?U"_AZ^c26|Gpa+rLH:&dr(`#G6wAnrZ"NZeDU|79iIVge[=l9@%300F()Y>)wiMQuTwF_;3}Z>4Dx`l%#X5k$|)M(]))Gg.jR[GG_+3PxbQ"qX&Rb{8i5snH;3k.[~!1OlZ20yf|vQpY=h[@g"Q1WPQI)irUAvZ74@b7=SP]ofUkK<%u=hlH]3Cm)}>DJ%#sO~)0VlX5?(X$A(%cb:xtvry3HyQbqv=4.<YO
nf.<nH>T3&@CLQ#Ucdv`*N[UI$c,qI:=xD47%=W!:lY^c^!v.u^,wPVJB4)LL$(^)uAfOj"JL7;txw*`#xDy<i+c^6,2v0%!-?*P8h;Ji+^N9+Jd&xZFfyQ%vs)F6>a#|=0.HuKH`YZ,|i)M<,Rc9u(L;dh1F?hut*k>hgxC3
"O"<VxS:_*);FK?ZUmF_Qqo0@0|/,PYweQ1TdY,A[Kq`rp(QOid4/giBTvryk]q<HBNefD&bz_e1jrLVf5))|pBW/[$GJR78y_}0qm8>6F:0c+9+njG5!XR)u-9E!djhx_P`L[ZVh:f(stWd(';case"zh":return'(UF%0lQ.W1jvhGW8`82yD]_vS^>w0L_d"#T-)8f&.;"Y!"f$l/|4(>7e4/i">E+=rCyZyirWYthSr<+U<PgVMK+GyEJcxH;4d/>rzEWn?1"@bI^^+YJW@@./^@vQmL]MOcX56/VRgXY8cV
:7=:[]=CnL-@]kH4Zx63>N7B]2^O/57r#@9.7P&*g<
NsZEW2V5rjL`$[Kl(N#H~89nbWq
>%Z?_8?p@2,,}79!zr<w@@r^m=(+D@1QWjBs(xL8^fvYHyQSkeHST
%F{*#m*51

xY5~KIq>@G?Vxfrg*e)|sFt:Z!,hN[Lz^K:DhAZ5OGt>iuGfk+l=gIUC5~,1To-U;s@Kqgmgv_B`pRI>U+vg8hh_j<vc?ddm?QhN=[to[s0dH_ncH,uW41S*]m7AqXYXn(.
bX:-r|QD:ra3e^]:u@:w6%b4CztpmvdWI{I:]Hv(+s^cmkJp0#/;:yt"#"<n+&42
7s5
h&=/1AY@N^TpRv@C,rV&L*fR51D2<K!%bQu;"k[DA/tJ94UsZ!*m:4(m,gb)x:2vgq>E^Z++H@n*_WU*S!MRX/)$Z)mUW,%%9ov!5K_x}.!uYkeix<r*h%BU.3KtCZQgF9X49p^M[.fxvtVpV2S+/OTl"a3+D>lJ~(;D4@9*X=Z_X/s8w]SrjAgYHs=9?H%0pGqO~@4eJ.>k<3KWV*XSe
_D}>C+#t*H>)}pEl2px2}45!Yt|G+R5LfHX_0-sZ=rO6"indKbRrARt,keG:$0<z&jS4a+BPS2@RjtkIi=yq/`xlsV_WGp>UG?A6e^eI=E(^UyjgGPa*Q;lyfV+a$%(mSbOBAM_5MFKm@3j:;>l1Zgv*h@%doS.R~h}MkN7v`>uW"n77y[PpyAT6.D&9tB[?nk{
nZI8Lc4s%qsohSyy.WQVlg}W;d/psAiom_27Sa!vS-
Fi((
`Cg7$Sh2#r4_q]^.kF|8$mT)p+WRnv8aMB5NFo+cG+g"-_!bFo./V]XMeAs]f4;@c$GB0W+L!hOS"`}W..]T!kDCc&%sXtv-Svt#*7v,S32rblM[Klk4Y.poBFXG[ln6eGWK,T.J{;9g]Nc3e%n0
)eK|*OA2kLS*eJ@V
@f_oLP@M9K%xbs>?_F^UG]Ln_EIXqQ4[f:uO5qRbM0#h4;!)I!!K<L
8j:Vs,7C["Ox?SFri0da>,=5qSD_1I`RT0Y.%X@`DzP/Ba>2N~TFi(mc$CT5`6Gjv7L34QI/9Eq
,}![eJ%8bU$kM8Plrjg,GKCGykP`q{]"cy*gsMkE@~YK8U-yG5
>L,i#RYM&0m[>CQTNMf*ecBn&yrp->+pkZFF31d
B8WF&0rSLg{
v9PmCkm"TZ"/+rUHF2*+xZE;DFQE8=(94G}o2f(@Xlk52;JqB0>:}$xx1-6Rp)<7nhLAf!shi^kxsLSS]gLS"?(CRI/C.2Uaq+rX[c-dYejSsWtcOuD1U=7(Pk`,`C=&~n)m?0M7z;}P47pyU7X2&;7mfN2sCatR*Ovt~SaI5dbvK5{*;n#-,N%_gweu^;q/H7y"Ix)kN]#5?#?7]Oe+g315>h7@Cc&N7wUAj43/E&R7?cC1DL,t2sz$tZ@*M_-"Kdu=oa32!-~[V?U[5(VB/R2Fl?_rc"$ccpJ%"u3xy+D(Tq(B=B2dsLPF}S9^A*
f0<G=@<CXN"MP6$hjJc?[4]Me%i_7)IpqD@-RA1I1%XH6`8Q;^eLc/X$0%;d?cU.J:Urx+39JX<kKkX3GFt;+4czIK)RH,W|F]VZ-$dK2.JC=l(bE7XI]HfrlqKxMK[E-G%i.2Cmrv;dHH"0?tJv]G
T8pa8xEk3E@:Od$*VHiJk(xx__X*=AqTNj_<#$Wj?KlknnPuD3%H`Cr>
$mG+^-@JKLG9TeCF^M7Y1A$Y$Bkd4C
-XzCB>MSSvQtTWb^:PoNOC[6`tH5cZ#Un>Cc)W}[13=x!KNv1iiG$k%"R6LZ#23&2SUBp_<4>bIGHhu>oP6=)fVcK5G1N/$Z=fGK~L"N,^+B-u-:"+?-Yk/.OUW`3RhXK#Cs>g}Hcof)cj[MqIc9@(}oUy5CK6,c%N
cbuEZz+OiWrBMbxve=[0o.X
az6ro"6|K2w5gF_YDSd,Xx5Lo>#WGI;ADo:,@rDWn9`?_gln%sQVJI0eVH>=xWUTV2Og;J1>JFD5"#0XUcKmpAc)v6e5>f6UpO2#8fFG7b;FM8GRZ>08*hElN6SLg3RSYS^3ts3lQW.HD5RlE"3.6XgyE
H2UDsY]>sFiz(zBRXW=SRJ&*))X[1[MEC=>^b7fHln%&a&^/qj4ESKhSJU7g97Uv6udFO-^SfIuMv7Rjlb8fJ;E
ngw@5JX`/H?KR+J>bpM1ovr%sNwbJ?::)NZG4^@-xthGK-XA3+T+J5IVV%ki=wo=d<nMGe%$I8.@R-J#
uYw,aS:2WvDE(dmU#I<nf=@o3R>o@P/GR6$CWGa^%[GcHA_;KE-(j
PA#ga!>iHe5W^6Zx<][v^R|P1R_P2BSoD1?cf[BJe=/GO$y>=Aah!vqM[#~L@0xQ@gy(/"!+iw2#foOC[lWQ^#XsPMhUu+rQk6HS8&3-UE~wy)lw>7W?EC*B%(7jPrytg_u%r[G*n(($$
jQ|q~r9qzs);IZT,}@C*uG<BZ:{Z@kP
m^m"UfRY:=Z+cRx<jAGy[Wi%wcY).U>$Hn(C1/t7[x(2g%y]@=-iayHK+<"5y=*b,H%IM2X:a;qS+AG=XXeWqu?.7h(5bhkz(%K';case"zh-tw":return'$UF09lQ,10Fkb:
Zg>
c8b^RqFV!HCCu=^N#2-YBbC=G2Nz,y*uD*Gxz#QM2Fg^0~pQa/G63N.Y
VvC"0?b!y37S{q!R,JVZi5MkC!`,gS#TFG&BKR!@wvD#Y7+q(5=3/]2q[,})yy}mAJ:/td:%yO8EEXe.UM-Z?qWyRNJfJptBTv;%U)c6GcO.cW:fu;r8+;bH_/:&}wbA!Cp(1=ay~P:PG@c.jm~LSs3xNg_eF=*rWeQ=Mu7vFRm/9_w(Vwgn)w,A&v;hJOL0XLMdKHR:rvm?>DdM
?>GRLLlYsBvK?B>,O0jp)YT+W"ip=o7Z+*@*S?XPEPc_I:6=(Xb.7];PmFi,w8nPm]RbBc).tlb[q^i:RlH_w=KF3;),Jbsn&f37!7Bkg!x.Qj!4X0;]]e6<qh#z3o4L5zfIIE6zv4n_Hce6O~>KU,bg8"
IA:%N?:h3jK_390m0JXm@8iM4U0.tO|5zIqgRy;=>K6H<p/;gWH>@SDBc:tg@^`0t3k,EBoH^d#[0?t[kZ+;xy"OscbQTRt$VNuh^Z]&{e(rHv~oP_K?p03lq>"yD
zG.IaI.pB,K^py,n/^=3:rP)ktt#|b/d%tPASXrb?)G_-^s1maqV&h=6x
Oj.fqN7T(m;-sVt_k?;=KhJq$W$U&<|*Sh(
:A/Dm`;$vSI[x]@"sUXA.Ho)L29gn=TgD6:`:Zyc~"6b~^MB.AX3FO~@_OC]
Dg?4gCJlo7P%;`u(,xQbj:6#_o!>WUR}OwuZMMa-5DRt*BdMbn3p_[X}W;)tpWby^>8~VxK
;3_t62A#R0YPKQK_7KtDrHwhWN`.Mrmk<5d+ZY(oC$yLUZP
*VQnyc)rP8DgXrMb^L2%a::sg5NBkWs}dx[|rD86hC)|G]&,S>Acu;6Xt<J[Uulqlq7s]MDmk3+{v0:Ht,5A%E[j;c[W
HteIK#EXCv&GFIfYSQVi&-kSjujmq
ep#fm[GkK
j9Fd#7`LGH(B7R[0nFZ`t$opDO}Tjtl4EkWt:2z%8#CJ*#M#fR1VgctU*ad7aE<BmmPAm=IO6?t$f:xm_"<wJ3O0e7O."BqAujwiCCc^Fx1uBcY@4
>xTE_@8-YAvpv2k41I-?(bybkd")1v4J;;VY**O730G/`mGn0Cp6Sc$1$Vxy[1WDdpZowMX]y#RYQNMv`%y$Axn#9IkDp_zTocQW1.1YF^Lm8+"43MX";`F0c`*Z>Zf?5+c=~.g)mF<(?0
@bDYU:*;
bT<3n;q*UC-viS3I
g=)l3cS|
^<icB0J!!D`Cf=kQ5
|u;#UNhRDbC`I
-$3pCvQs-1I0O4I0
Rklh:~b5n]0c*eVq^<q*TBn^r*qJ#],
sg5$?LX|V;K$d>lhkveDGsptt9IT#W?LB-&Hwe]P+zKmgIt6y)8N!Za"rv=D<#Nb#NBa;A&b8!GgPx&#)heqs@:E
FG^etPLD43ogvx{OD8dtWYpoR38rT1Ed)2B"CS_Kv5g9R8pG5)>l}0RdA$4>C&posX?i<6ZtzXCyvIv10Y*hhuc%
d)s5FdOTw`N
*L0/aRJkJY%Ec<.rwZHlYrO~TTvNda%VP
OiuS8$plNQ9TmxB7t!LF
>"a]03zQ0Qgj"p%8<d&n?;*eX"h3*knOr$(%8fs2y:`%8oeZDBf%>*+JEA=uy]lyUjp;{5p66Z_e#+x*:(Jjm1lC|5sC@C9se]3qrQ8Xo<m@ETp&y!~o.NidOO{VBYwslnxbwJfQ{k=RrHxee.a$CMQw+$T
ZpSgW1qL]wn>`%v[0CwF^07?g?!+cX:@6.tIAQmJ/w4W3B0X5S{><yT!cr$=)]/.)9!HOIO(2CaS~0"^Yl$!g-$1R[pfiPN5hR<Wo;m9P(^y~CU53k%qfyJ:aUg7jmyi;0B$xe.KWb`z)9#nH!gFlBwODnfdMWnHl^fMnL@Q4v0G|s:_&2Zan1fBnveO[s:HH,(QIA}9mQ[(E7DXQ%P%Ab*3UfWF~!SQl8[+;o?ua*OvdfhKIR|hB.Z6?/;P_U4-B&sNYyCLlUx*B"j=qE+@ZB8hBOK2JX3Y+e@usj
]:O>OK93gp0WQxM7g(kG^;5S8=EJv6
UDzr%*s.Gy87Y<MC4c~"c^VG0khtBJ:g$%9OtTJ;o0r*/2C"yq3nSDZp2B90;MS?jYXEJ-h=_!M3(CJ7]U8on1@XYRE[TKa517z]I*z.O.(-sPB!fRG6X>mjTYwuDr~#!uQaEprcn`rA3WU:
^xvhK3,5Ps.MG&7b4#Jwja+^c&ny_xPrk;NkvJ6mS
b.49(@Oh%J[W_Y&kbw,F*Jf:A^oM&`#eQUf)Eb3X33oA(_3E.hr$>:J$Ld.fPQv"=#$[;C("EKZRrB*iC(`SOxf:a:l,.T-hM2b,,;U^g1txCY$IoYwBFKi!Bfv=&Tj>R1#x(khV@6EgRP;{tIII&N6~Xuo6JnPX)H?P9}Ef-<**M2!^#GePyL+z0Fk-k;ea`QrY!~Pojq@OCk>Acqyx,{sdyvL[?/<(5;AoG,HnwGH+3S]4ezwL<jki9z
46OII;uRDH
`t[ACeEe*]"IKo]lfg#?:L>p`f>cpHR9JwDCU<I8fC3IDu9~4</dl`Uf3a#P]19}%/e6]cX&#=O>wJd<_y5!.fyaU5"$+M7g5}bU_D[u@uJek%k-Vk!R""0`fWUX1BYh9jh7:e_)$^NrE05|T4>fIGiI)uy](A:,E&Z@Zh@hl,QTDk.89|b}A4[]YG"|9)!v`aYDFN)v)wp8#q-*7183qI
1*cqiRQ8DBEAP:L`D2KiB[`&5pTgc]]B&fM.,ks"`_*TN&?L8[9
1+gy.J>
Ht_-uM4>8P.P
-bERYMOHo9';case"ko":return'*Zu1$bOZK/fnZxg$j.^#H#<Bpt])fIMoI*,&aF6)YAK=<("hHPG.hA)f~LHVty,I3IZt3h;M8@&HJGSiuhMj&`6hqnqw6?R1Dex>^!{tA4&d"htH(B!;+vn:N7s(kd
=Ob#0
fB4{Mn_I$Vs~oQ"]X;o9?V?sMG2ph(E|L7F]#y+1pzE8K?P*/&+"1U+ZNQ>r-*v;j-!!K%AtO?niW4
4RRcz@gI>[#k$9{$`B7AI]yPwBV&8SHNrrs
KAM[&jS
K%<Sm
vv@8Pk+1-Ma$VV>d~r{V;MaPSs8wN_Py+@][hO][W7wuEd/C:/t3}yEs.ork8)].n%}MhG67)!//uO/fP2%>u1IWi@-X^X5"B;[jUFF[R"ow%5Zo&>x<v]0g,f8ugA8=,l>CcF9+RSL4bSqLa7Sm>0jB:,^?3I;Fo[|Hv[yjr*o7u^!?Vs!Za%z2>+"Op9v?E^nd~[|1CJ.:T-&7fl0X-E!P1&qAw@w<Q!-i^Hd#25}O~0~,-gFls5o!
`Vmb(53-RB^H2d^}<*=Wv5X<"AJB,iE?_;/(c.b4lkp`H*#3i~[6v;^DI4Q3C_76;yV{Ulr~C+!9`IK07(1>BDlSPqz)dj<;;%UnSFD^5+_-%zAmFU*]&Il?;Y7-g7:jFMqW2HF?"3o}=jK`G`A&vr:V)c<_q%hHOTT7P>WqC!1gXQ=r4)Rw`#*|tZ=FD
:wo]6P5Y<P2&o{B>#X!l*^?i^rK]]o1v)QA{l*MrcGyNCoM
C5[6!Vxq]MZY(N]/DdOtTBO=BH+_BzZ0,f.T=h_od/p[2Q(R+~E*,9,2:,4P7/2(j,W]]Qx}g)s:p,.AXNc`>S*j<)D}>p;CJ/ktZC#OPW5n-Fbwkg<a=+@^u|6JJYvV`ap}uRz#[CxB
K4UV/+]nc&tZ05vC.Y0Tw>?>Ntn0!N0FX+eiCIsvTyFKMNO(SO3tW)z4!T-Z:(3t<6&QKq2jPTLQpta@(Nz(-,yjc=ovuFyJGu@,2a"UXvWn&X
U]aDb3R^/^-Lcz.Z9Wp0uXs9P19`+zhqlz]5RadAhxe8@xs@YdZUKl5d@fO~$m)BeRC-*
8*?[pX"AV;#54u](+-(RjVb;)Y7tM/"jxkr^@;5:8*/=r#<1GtNQOO]>SRmhrrt<7M){4,mCtI$zH!5fS=Pysw$omxd+@B2;$2qT77A~qWkSqo0$T`G@%VXX1N"=_?fenArrkeheO95rkiT0S3!lgcnAi.]i8)o5&!,`Y)E($q(,
oY>1uZwz)Y3<}2ILqeaSbN&ja51PIFYtwpMNr-{*83D<#vuVoObIS8]5Q"K*VeBT*f%^uw$VS:K"=C.Pb?zJ%ysLJbS";07b22FLR9IdGkf4`RgX2a<H>e
Y=`wY>hbyl1;P)<dUz"eO[>-#t?jKF6.1*7FPuH}m;K}(9k:Nvkl$!Yhe%+3JM;GkTk^./^J-fK$gBYC$,OViD*=$CBu-
%lVeV)pfKiZ*7;%r
Xc
ePLpQR3DD7WKX6GIkK6)rZvu]x"sU=i^LF)
mB/+EpySH?,[DHGA4Ixs^Xam^a#VG|q:f~fa"Xv9KLR.M661v}rl88RcVb?{49,oZB[!mBU?;2>E20Brgi"J2Se:b..$9}XfC=`kKrq2Y#0hGB,MPKk?N?s*y|-j0++J;9EI:^Ua&e<Y"]WgN|Ha5[hsq!:^G*F?Xn989PP)[",ZtS@qDd]~ZwDDvqTI.E"(ADAV*(H}p>3u!m*9AmD.oKi0Cpmw[")hx;^1+h6KO]jZ4worGw(P89:WfGPGkZ9[[A5D+sk"g4[[nzgIu}F+EGQM/&x.g(1DZU9Dcd]1;g_B_Fe"&et(EGKq+Cjmr>s`a$pOeF>u8!HVl5KYY
Lx9Q(;Q3"<yR]q]OvP.cAeVYof8S0S1Z>su^iQ0{3[^TO;F#k]aki$xv#3bV1o@w=eh;EvK@Tw$gRSEUH
QA[
pGFM%"+D,G^ybT!Zl4Nb4E^Cj#bsJXu;W:Cy)9KyTrZ,ofHdXzlgvo:d5FJ-d1[/Iz()mim9e~8XfC_0=pBxm3Znw[*:t@yiK?DTQJX@nd5M-/#N]:il%B;nPQoT+
u|bYm#D#&inR&$fwVl*z@/K4v3a>t)_VR[t>
CIRA+VRlsAn0,P(brBIc2Tx+~FtXSWJa*Z[YQxV,~T,f;]3s%JTd
0N&mvcd-;4[af,k;U7r,K!K&vk2gr*q:Zi(V=(L3C$c8kD*8jqc|KDW+JzCB4sw8Mw12C(=o*i<]re0~l.GyXE2b^cfe3UW8gO4304J&>dGM9;ow?5AfOU
SfyefIf1E93eqa_qVN)*d.#2NO|O|M!x$AxdfwaxWJ=rlvg]KyuTu[(vm:
(a5Qu1HOTSe,K{d>bt^N4g@G/"9^v}SOEJ%^<H]`fJ+ENLo1NlD>%(5$TMFcC,#R)>mPW.
YV^a~=FNqw(Ir]LPo_^q[?AcDXEN^Gu>j[Jv7h,kWvKjwrSjQn3iP-SM`L[naK1lzi2
;!oJK
OJg;]ynfphTm#=]5p@<TzQyEWD4F"-IN]TTn-cc&pL9EDo/3H>Zk!%Z4aKZid+{JRVory8]oOEsh^K~mc]us0cA5(^-qlRo9bc*R!Ljpek86m6PqO_uoV?F#v0me2D}[R7UtAsBnHF_l|)]$dv:Q(*w0$
004*9`wx;$rsN3=(7]J0{3feLE;=|/}P`b>2O,*
9??"Yq!s"vw(S]dsgyWcgrYP(9m;ulP2|13PE7$SO@B^{m.0H3LH=yX)j_3/}dak0"/?S-1`Re9,0jCb[d-6u&efhPP7das>171Heu5_)IaEz]oSs-V,xA/*^x6ov4mQ_uDY:]"D$TLOa,?#fn*oTv4YI27q]<GGWB7<,<*12%Zj^oNm.1ejl@`coc05r1#7ht!t[=!+yJ75TX3ogBH93u%qbq;2XEbhb//@bti%DP[68x70*3)(]N}:#($j.O!1c%I9Bisj0e3
Qrj[,1}Iol@P5>e=<)&$fU8[,XD24x6K9No8=)SJ"8|+h!
Qv!Myw5;';}}function
get_translations($Qe){$cc=($Qe!="en"?decompress_string(get_compressed("en")):"");$rj=array();foreach(explode("\n",decompress_string(get_compressed($Qe),$cc))as$X)$rj[]=(strpos($X,"\t")?explode("\t",$X):$X);return$rj;}abstract
class
SqlDb{static$instance;static$untrusted=false;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$V,$Xg);abstract
function
quote($Q);abstract
function
select_db($Mb);abstract
function
query($H,$Aj=false);function
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
dsn($sc,$V,$Xg,array$vg=array()){$vg[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$vg[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($sc,$V,$Xg,$vg);}catch(\Exception$Lc){return$Lc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$Aj=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(25);return
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
fetch_array($Lf){$J=$this->fetch($Lf);return($J?array_map(array($this,'unresource'),$J):$J);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($gg){for($s=0;$s<$gg;$s++)$this->fetch();}}}function
add_driver($t,$D){SqlDriver::$drivers[$t]=$D;}function
get_driver($t){return
SqlDriver::$drivers[$t];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();var$primary="";static
function
connect($N,$V,$Xg){$e=new
Db;return($e->attach($N,$V,$Xg)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$l){}function
unconvertFunction(array$l){}function
select($R,array$M,array$Z,array$r,array$E=array(),$z=1,$F=0,$qh=false){$Ae=(count($r)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$r,$E,$z,$F);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$z&&$r&&$Ae&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($r&&$Ae?"\nGROUP BY ".implode(", ",$r):"").($E?"\nORDER BY ".implode(", ",$E):""),$z,($F?$z*$F:0),"\n");$Bi=microtime(true);$J=$this->conn->query($H,(!$z&&!$qh?1:0));if($qh)echo
adminer()->selectQuery($H,$Bi,!$J);return$J;}function
delete($R,$yh,$z=0){$H="FROM ".table($R);return
queries("DELETE".($z?limit1($R,$H,$yh):" $H$yh"));}function
update($R,array$O,$yh,$z=0,$hi="\n"){$Wj=array();foreach($O
as$x=>$X)$Wj[]="$x = $X";$H=table($R)." SET$hi".implode(",$hi",$Wj);return
queries("UPDATE".($z?limit1($R,$H,$yh,$hi):" $H$yh"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$ph){foreach($L
as$O){$Z=array();foreach($O
as$x=>$X){if(isset($ph[idf_unescape($x)]))$Z[]="$x = $X";}if(!($Z&&$this->update($R,$O," WHERE ".implode(" AND ",$Z))&&$this->conn->affected_rows)&&!$this->insert($R,$O))return
false;}return
true;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$dj){}function
convertSearch($u,array$X,array$l){return$u;}function
value($X,array$l){return(method_exists($this->conn,'value')?$this->conn->value($X,$l):$X);}function
quoteBinary($Wh){return
q($Wh);}function
typeName(\stdClass$l){return(isset($l->native_type)?$l->native_type:"");}function
warnings(){}function
tableHelp($D,$Ee=false){}function
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
indexAlgorithms(array$Ni){return
array();}function
indexOpclasses(){return
array();}function
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
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.0")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
credentials(){return
array(SERVER,$_GET["username"],get_password());}function
connectSsl(){}function
permanentLogin($g=false){return
password_file($g);}function
bruteForceKey(){return$_SERVER["REMOTE_ADDR"];}function
serverName($N){return
h($N);}function
database(){return
DB;}function
databases($nd=true){return
get_databases($nd);}function
pluginsLinks(){}function
operators(){return
driver()->operators;}function
schemas(){$J=schemas();if($_GET["ns"]!=""&&!in_array($_GET["ns"],$J))array_unshift($J,$_GET["ns"]);return$J;}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$Db){return$Db;}function
verifyVersion(){return
true;}function
head($Ib=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$Lf){$n="adminer$Lf.css";if(file_exists($n)){$ed=file_get_contents($n);$J["$n?v=".crc32($ed)]=($Lf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$ed)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(26).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(27).'<td>',"<input name='auth[server]' value='".h(SERVER)."' title='".lang(28)."' placeholder='localhost' autocapitalize='off'>"),adminer()->loginFormField('username','<tr><th>'.lang(29).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(30).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(31).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(32)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(33))."\n";}function
loginFormField($D,$Qd,$Y){return$Qd.$Y."\n";}function
login($hf,$Xg){if($Xg==""||!password_required())return
lang(34,target_blank());return
true;}function
tableName(array$Ni){return
h($Ni["Name"]);}function
fieldName(array$l,$E=0){$U=$l["full_type"].($l["null"]?" NULL":"");$lb=$l["comment"];return'<span title="'.h($U.($lb!=""?($U?": ":"").$lb:'')).'">'.h($l["field"]).'</span>';}function
commentValue($U,$lb){if($lb==""||$U=='TABLE'||$U=='COLUMN')return
h($lb);$mh=function($Wh){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($Wh))));};$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';return"<pre>\n".preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($B)use($mh){$ld=$mh($B[2]);return"<table>\n".($B[1]?"<thead>$ld<tbody>\n":$ld).$mh($B[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($lb))))."</pre>\n";}function
commentInput($U,$b,$lb){$Y=h($lb);return(preg_match('~\n~',$Y)?"<textarea$b rows='2' cols='".($U=='TABLE'?20:30)."' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");}function
selectLinks(array$Ni,$O=""){$D=$Ni["Name"];echo'<p class="links">';$df=array("select"=>lang(35));if(support("table")||support("indexes"))$df["table"]=lang(36);$Ee=false;if(support("table")){$Ee=is_view($Ni);if($Ee){if(support("view"))$df["view"]=lang(37);}elseif(function_exists('Adminer\alter_table')&&$D!="")$df["create"]=lang(38);}if($O!==null)$df["edit"]=lang(39);foreach($df
as$x=>$X)echo" <a href='".h(ME)."$x=".url_escape($D).($x=="edit"?$O:"")."'".bold(isset($_GET[$x])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($D,$Ee)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Mi){return
array();}function
backwardKeysPrint(array$Fa,array$K){}function
selectQuery($H,$Bi,$Xc=false){$J="\n";if(!$Xc&&($ek=driver()->warnings())){$t="warnings";$J=", <a href='#$t' class='toggle'>".lang(40)."</a>"."$J<div id='$t' class='hidden'>\n$ek</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($Bi).")</span>".(support("sql")?" <a href='".h(ME)."sql=".url_escape($H)."' class='hover'>".lang(13)."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$qd){return$L;}function
selectLink($X,array$l){}function
selectVal($X,$_,array$l,$Fg){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$l["type"])&&!preg_match("~var~",$l["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$l["full_type"])?"<code class='jush-json'>$X</code>":$X)));if(is_blob($l)&&!is_utf8($X))$J="<i>".lang(41,strlen($Fg))."</i>";return($_?"<a href='".h($_)."'".(is_url($_)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$l){return$X;}function
config(){return
array();}function
tableStructurePrint(array$m,$Ni=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(42)."<td>".lang(43).(support("comment")?"<td>".lang(44):"")."<tbody>\n";$Ei=driver()->structuredTypes();foreach($m
as$l){echo"<tr><th>".h($l["field"]);$U=h($l["full_type"]);$hb=h($l["collation"]);echo"<td><span title='$hb'>".(in_array($U,(array)$Ei[lang(7)])?"<a href='".h(ME.'type='.url_escape($U))."'>$U</a>":$U.($hb&&isset($Ni["Collation"])&&$hb!=$Ni["Collation"]?" $hb":""))."</span>",($l["null"]?" <i>NULL</i>":""),($l["auto_increment"]?" <i>".lang(45)."</i>":""),(isset($l["default"])?" <span title='".lang(46)."'>[<b>".($l["generated"]?"<code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($l["default"])),80,"</code>"):h($l["default"]))."</b>]</span>":""),(support("comment")?"<td>".adminer()->commentValue('COLUMN',$l["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$w,array$Ni){$Og=false;foreach($w
as$D=>$v)$Og|=!!$v["partial"];echo"<table>\n";$Rb=first(driver()->indexAlgorithms($Ni));foreach($w
as$D=>$v){ksort($v["columns"]);$qh=array();foreach($v["columns"]as$x=>$X)$qh[]="<i>".h($X)."</i>".($v["lengths"][$x]?"(".h($v["lengths"][$x]).")":"").($v["descs"][$x]?" DESC":"");echo"<tr title='".h($D)."'>","<th>".h($v["type"]).($Rb&&$v['algorithm']!=$Rb?" (".h($v['algorithm']).")":""),"<td>".implode(", ",$qh);if($Og)echo"<td>".($v['partial']?"<code class='jush-".JUSH."'>WHERE ".h($v['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$d){print_fieldset("select",lang(47),$M);$s=0;$M[""]=array();foreach($M
as$x=>$X){$X=idx($_GET["columns"],$x,array());$c=select_input(" name='columns[$s][col]' data-default=''".on('change',($x!==""?'selectFieldChange':'selectAddRow')),$d,$X["col"]);echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$s][fun]",array(-1=>"")+array_filter(array(lang(48)=>driver()->functions,lang(49)=>driver()->grouping)),$X["fun"]," data-default=''".on('change',($x!==""?'helpClose':'selectFunAddRow')).on_help_value(' (.*)|$','($1)'))."($c)":$c)."</div>\n";$s++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$d,array$w){print_fieldset("search",lang(50),$Z);foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$v["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$s]' value='".h(idx($_GET["fulltext"],$s))."' data-default=''".on('input','selectFieldChange').">",(JUSH=='sql'?checkbox("boolean[$s]",1,isset($_GET["boolean"][$s]),"BOOL"):''),"</div>\n";}$sg=adminer()->operators();foreach(array_merge((array)$_GET["where"],array(array()))as$s=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],$sg)))echo"<div>".select_input(" name='where[$s][col]' data-default=''".on('change',($X?'selectFieldChange':'selectAddRow')),$d,$X["col"],"(".lang(51).")"),html_select("where[$s][op]",$sg,$X["op"]," data-default='".h(first($sg))."'".on('change','selectFirstChange')),"<input type='search' name='where[$s][val]' value='".h($X["val"])."' data-default=''".on('input','selectFirstChange').on('keydown','selectSearchKeydown').on('search','selectSearchSearch').">","</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$E,array$d,array$w){print_fieldset("sort",lang(52),$E);$s=0;foreach((array)$_GET["order"]as$x=>$X){if($X!=""){echo"<div>".select_input(" name='order[$s]' data-default=''".on('change','selectFieldChange'),$d,$X),checkbox("desc[$s]",1,isset($_GET["desc"][$x]),lang(53))."</div>\n";$s++;}}echo"<div>".select_input(" name='order[$s]' data-default=''".on('change','selectAddRow'),$d),checkbox("desc[$s]",1,false,lang(53))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($z){echo"<fieldset><legend>".lang(54)."</legend><div>","<input type='number' name='limit' class='size' value='".h($z?:"")."' data-default='50'".on('input','selectFieldChange').">","</div></fieldset>\n";}function
selectLengthPrint($bj){echo"<fieldset><legend>".lang(55)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($bj)."' data-default='100'>","</div></fieldset>\n";}function
selectActionPrint(array$w){echo"<fieldset><legend>".lang(56)."</legend><div>","<input type='submit' value='".lang(47)."'>"," <span id='noindex' title='".lang(57)."'></span>","<script".nonce().">\n","const indexColumns = ";$d=array();foreach($w
as$v){$Hb=reset($v["columns"]);if($v["type"]!="FULLTEXT"&&$Hb)$d[$Hb]=1;}$d[""]=1;foreach($d
as$x=>$X)json_row($x);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$yc,array$d){}function
selectColumnsProcess(array$d,array$w){$M=array();$r=array();foreach((array)$_GET["columns"]as$x=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$x]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$r[]=$M[$x];}}return
array($M,$r);}function
selectSearchProcess(array$m,array$w){$J=array();foreach($w
as$s=>$v){if($v["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$s)!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$v["columns"])).") AGAINST (".q($_GET["fulltext"][$s]).(isset($_GET["boolean"][$s])?" IN BOOLEAN MODE":"").")";}$sg=adminer()->operators();foreach((array)$_GET["where"]as$x=>$X){$X+=array("col"=>"","op"=>first($sg),"val"=>"");$_GET["where"][$x]=$X;$fb=$X["col"];if("$fb$X[val]"!=""&&in_array($X["op"],$sg)){if($X["op"]=="SQL"&&(!$_POST||!verify_token()))SqlDb::$untrusted=true;$qb=array();foreach(($fb!=""?array($fb=>$m[$fb]):$m)as$D=>$l){$nh="";$pb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$fe=process_length($X["val"]);$pb
.=" ".($fe!=""?$fe:"(NULL)");}elseif($X["op"]=="SQL")$pb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$B))$pb=" $B[1] ".adminer()->processInput($l,"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$nh="$X[op](".q($X["val"]).", ";$pb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$pb
.=" ".adminer()->processInput($l,$X["val"]);if($fb!=""||(isset($l["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$l["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$l["type"]))&&(!preg_match('~date|timestamp~',$l["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"]))))$qb[]=$nh.driver()->convertSearch(idf_escape($D),$X,$l).$pb;}$J[]=(count($qb)==1?$qb[0]:($qb?"(".implode(" OR ",$qb).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$m,array$w){$J=array();foreach((array)$_GET["order"]as$x=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$x])?" DESC".(JUSH=='pgsql'&&idx($m[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$qd){return
false;}function
selectQueryBuild(array$M,array$Z,array$r,array$E,$z,$F){return"";}function
messageQuery($H,$cj,$Xc=false){restart_session();$Td=&get_session("queries");if(!idx($Td,$_GET["db"]))$Td[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$Td[$_GET["db"]][]=array($H,time(),$cj);$yi="sql-".count($Td[$_GET["db"]]);$J="<a href='#$yi' class='toggle'>".lang(58)."</a> ".copy_icon()."\n";if(!$Xc&&($ek=driver()->warnings())){$t="warnings-".count($Td[$_GET["db"]]);$J="<a href='#$t' class='toggle'>".lang(40)."</a>, $J<div id='$t' class='hidden'>\n$ek</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$yi' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($cj?" <span class='time'>($cj)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".url_escape(DB),"db=".url_escape($_GET["db"]),ME).'sql=&history='.(count($Td[$_GET["db"]])-1)).'">'.lang(13).'</a>':'').'</div>';}function
editRowPrint($R,array$m,$K,$Ij){}function
editFunctions(array$l){$J=($l["null"]?"NULL/":"");$Nd=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$x=>$zd){if(!$x||(!isset($_GET["call"])&&$Nd)){foreach($zd
as$Zg=>$X){if(!$Zg||preg_match("~$Zg~",$l["type"]))$J
.="/$X";}}if($x&&$zd&&!preg_match('~set|bool~',$l["type"])&&!is_blob($l))$J
.="/SQL";}if($l["auto_increment"]&&!$Nd)$J=lang(45);return
explode("/",$J);}function
editInput($R,array$l,$b,$Y){if($l["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$b value='orig' checked><i>".lang(11)."</i></label> ":"").enum_input("radio",$b,$l,$Y,"NULL");return"";}function
editHint($R,array$l,$Y){return"";}function
processInput(array$l,$Y,$q=""){if($q=="SQL")return$Y;$D=$l["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$q))$J="$q()";elseif(preg_match('~^current_(date|timestamp)$~',$q))$J=$q;elseif(preg_match('~^([+-]|\|\|)$~',$q))$J=idf_escape($D)." $q $J";elseif(preg_match('~^[+-] interval$~',$q))$J=idf_escape($D)." $q ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$q))$J="$q(".idf_escape($D).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$q))$J="$q($J)";return
unconvert_field($l,$J);}function
dumpOutput(){$J=array('text'=>lang(59),'file'=>lang(60));if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($i){}function
dumpTable($R,$Fi,$Ee=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Fi)dump_csv(array_keys(fields($R)));}else{if($Ee==2){$m=array();foreach(fields($R)as$D=>$l)$m[]=idf_escape($D)." $l[full_type]";$g="CREATE TABLE ".table($R)." (".implode(", ",$m).")";}else$g=create_sql($R,$_POST["auto_increment"],$Fi);set_utf8mb4($g);if($Fi&&$g){if(($Fi=="DROP+CREATE"&&!function_exists('Adminer\drop_sql'))||$Ee==1)echo"DROP ".($Ee==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($Ee==1)$g=remove_definer($g);echo"$g;\n\n";}}}function
dumpData($R,$Fi,$H,array$M=array(),array$Z=array(),array$r=array(),array$E=array()){if($Fi){$rf=(JUSH=="sqlite"?0:1048576);$m=array();$be=false;if($_POST["format"]=="sql"){if($Fi=="TRUNCATE+INSERT"&&!function_exists('Adminer\truncate_all_sql'))echo
truncate_sql($R).";\n";$m=fields($R);if(JUSH=="mssql"){foreach($m
as$l){if($l["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$be=true;break;}}}}$I=($H!=""?connection()->query($H,1):driver()->select($R,($M?:array("*")),$Z,$r,$E,0));if($I){$te="";$Pa="";$Ke=array();$_d=array();$Hi="";$ad=($R!=''?'fetch_assoc':'fetch_row');$_b=0;while($K=$I->$ad()){if(!$Ke){$Wj=array();foreach($K
as$X){$l=$I->fetch_field();if(idx($m[$l->name],'generated')){$_d[$l->name]=true;continue;}$Ke[]=$l->name;$x=idf_escape($l->name);$Wj[]="$x = VALUES($x)";}$Hi=($Fi=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$Wj):"").";\n";}if($_POST["format"]!="sql"){if($Fi=="table"){dump_csv($Ke);$Fi="INSERT";}dump_csv($K);}else{if(!$te)$te="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$Ke)).") VALUES";foreach($K
as$x=>$X){if($_d[$x]){unset($K[$x]);continue;}$l=$m[$x];$K[$x]=($X===null?"NULL":($X===false?0:unconvert_field($l,preg_match(number_type(),$l["type"])&&!preg_match('~\[~',$l["full_type"])&&is_numeric($X)?$X:(!is_blob($l)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$Wh=($rf?"\n":" ")."(".implode(",\t",$K).")";if(!$Pa)$Pa=$te.$Wh;elseif(JUSH=='mssql'?$_b%1000!=0:strlen($Pa)+4+strlen($Wh)+strlen($Hi)<$rf)$Pa
.=",$Wh";else{echo$Pa.$Hi;$Pa=$te.$Wh;}}$_b++;}if($Pa)echo$Pa.$Hi;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($be)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($ae){return
friendly_url($ae!=""?$ae:(SERVER?:"localhost"));}function
dumpHeaders($ae,$Nf=false){$Ig=$_POST["output"];$Sc=(preg_match('~sql~',$_POST["format"])?"sql":($Nf?"tar":"csv"));header("Content-Type: ".($Ig=="gz"?"application/x-gzip":($Sc=="tar"?"application/x-tar":($Sc=="sql"||$Ig!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($Ig=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Sc;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
importPrint(){}function
importProcess(){return
false;}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(61)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(62):lang(63))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(64)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(65)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(66)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(67)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(7)."</a>\n":""),(support("event")?"<a href='#events'>".lang(68)."</a>\n":"");return
true;}function
navigation($Kf){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$Wf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$Wf)<0?h($Wf):"").version_iframe()."</a>","</span></h1>\n";switch_lang();if($Kf=="auth"){$Ig="";foreach((array)$_SESSION["pwds"]as$Yj=>$ji){foreach($ji
as$N=>$Sj){$D=h(get_setting("vendor-$Yj-$N")?:get_driver($Yj));foreach($Sj
as$V=>$Xg){if($D&&$Xg!==null){$Pb=$_SESSION["db"][$Yj][$N][$V];foreach(($Pb?array_keys($Pb):array(""))as$i)$Ig
.="<li><a href='".h(auth_url($Yj,$N,$V,$i))."'>($D) ".h("$V@").($N!=""?adminer()->serverName($N):"").h($i!=""?" - $i":"")."</a>\n";}}}}if($Ig)echo"<ul id='logins'".on('mouseover','menuOver').on('mouseout','menuOut').">\n$Ig</ul>\n";}else{$T=array();if($_GET["ns"]!==""&&!$Kf&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($Kf);$ia=array();if(DB==""||!$Kf){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(58)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(69)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".url_escape(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(70)."</a>";}$ge=$_GET["ns"]!==""&&!$Kf&&DB!="";if($ge&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(71)."</a>";$ia=adminer()->menuActions($ia,$Kf);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($ge){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(12)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=6.0.0",true);if(support("sql")){$Ie="adminer-plugins/jush-".JUSH.".js";echo(file_exists($Ie)?script_src($Ie,true):""),"<script".nonce().">\n";if($T){$df=array();foreach($T
as$R=>$U)$df[]=js_escape_re($R);echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b(?<!\$)('.implode('|',$df).')(?!\$)\b/g',false);$_i=array("sql","check","event","procedure","trigger","view","type","table","processlist");if(support("routine")&&array_intersect_key($_GET,array_flip($_i))){foreach(routines()as$K)json_row(js_escape(ME).'function='.url_escape($K["SPECIFIC_NAME"]).'&name=$&','/\b'.js_escape_re($K["ROUTINE_NAME"]).'(?=["`\]]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$Si=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$m){foreach($m
as$l)$Si[$R][]=$l["field"];}echo"addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape("")."', ".json_encode($Si)."); });\n";}}echo"</script>\n";}echo
script("syntaxHighlighting('".(preg_match('~^\d\.?\d~',connection()->server_info,$B)?$B[0]:"")."', '".connection()->flavor."');");}function
databasesPrint($Kf){$h=adminer()->databases();if(DB&&$h&&!in_array(DB,$h))array_unshift($h,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Nb=on('mousedown','dbMouseDown').on('change','dbChange');echo"<label title='".lang(31)."'>".lang(72).": ".($h?html_select("db",array(""=>"")+$h,DB,$Nb):"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(24)."'".($h?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$Kf){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'".on('mouseover','menuOver').on('mouseout','menuOut').">";foreach($T
as$R=>$P){$R="$R";$D=adminer()->tableName($P);if($D!=""&&!$P["partition"])echo'<li><a href="'.h(ME).'select='.url_escape($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select hover")." title='".lang(35)."'>".lang(73)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.url_escape($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".lang(36)."'>$D</a>":"<span>$D</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($t){return
kill_process($t);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$drivers=array();var$driverFiles=array();var$error='';private$hooks=array();function
__construct($fh){$oc=SqlDriver::$drivers;$Rd=" href='https://www.adminer.org/plugins/#use'".target_blank();if($fh===null){$fh=array();$Ja="adminer-plugins";if(is_dir($Ja)){foreach(glob("$Ja/*.php")as$n){$fd=SqlDriver::$drivers;$this->includeOnce($n);foreach(array_diff_key(SqlDriver::$drivers,$fd)as$t=>$D)$this->driverFiles[$t]=$n;}}if(file_exists("$Ja.php")){$ie=$this->includeOnce("$Ja.php");if(is_array($ie)){foreach($ie
as$x=>$dh)$fh[is_object($dh)?get_class($dh):$x]=$dh;}else$this->error
.=lang(74,"<b>$Ja.php</b>",$Rd)."<br>";}foreach(get_declared_classes()as$db){if(!$fh[$db]&&(preg_match('~^Adminer\w~i',$db)||is_subclass_of($db,'Adminer\Plugin'))){$Gh=new
\ReflectionClass($db);$sb=$Gh->getConstructor();if($sb&&$sb->getNumberOfRequiredParameters())$this->error
.=lang(75,$Rd,"<b>$db</b>","<b>$Ja.php</b>")."<br>";else$fh[$db]=new$db;}}}$xe=array_filter($fh,function($dh){return!is_object($dh);});if($xe){$this->error
.=lang(76,$Rd)."<br>";$fh=array_diff_key($fh,$xe);}$this->drivers=array_diff_key(SqlDriver::$drivers,$oc);$this->plugins=$fh;$ja=new
Adminer;$fh[]=$ja;$Gh=new
\ReflectionObject($ja);foreach($Gh->getMethods()as$If){foreach($fh
as$dh){$D=$If->getName();if(method_exists($dh,$D))$this->hooks[$D][]=$dh;}}}function
includeOnce($n){return
include_once"./$n";}static
function
checksum($n){$ed=str_replace("\r","",file_get_contents($n));$ed=preg_replace('~\n\tprotected \$translations = array\(.*?\n\t\);~s','',$ed);return
dechex(crc32($ed));}function
checksums(){$gd=array_values($this->driverFiles);foreach($this->plugins
as$dh){$Gh=new
\ReflectionObject($dh);$gd[]=$Gh->getFileName();}$J=array();foreach($gd
as$n)$J[basename($n,'.php')]=self::checksum($n);return$J;}static
function
officialChecksums(){return
array('adminer.js'=>'a0599090','backward-keys'=>'afce3b7d','before-unload'=>'48618ca0','config'=>'f49cc617','dark-switcher'=>'3d490dea','database-hide'=>'90c6c0dc','designs'=>'56f1c186','dump-alter'=>'d078b2db','dump-bz2'=>'f0d0e336','dump-date'=>'adc7f1c7','dump-json'=>'767dd321','dump-xml'=>'9f039895','dump-zip'=>'93817d96','edit-foreign'=>'8c874a58','edit-textarea'=>'a24c3cc','editor-setup'=>'a7dc3a37','editor-views'=>'5c12b185','enum-option'=>'a2563959','file-upload'=>'235eaa7a','foreign-system'=>'ebb4c654','frames'=>'b0e1d11a','highlight-codemirror'=>'f1a34275','highlight-monaco'=>'6a92cc58','highlight-prism'=>'4c12cf3','import-csv'=>'1d174088','login-ip'=>'b4766b62','login-otp'=>'62c517c0','login-passkey'=>'f69f2f06','login-password-less'=>'97c37010','login-reverse-proxy'=>'7bb63f11','login-servers'=>'f9ac2f28','login-ssl'=>'6ed147bc','login-table'=>'7b15c3cd','menu-links'=>'f1f86a60','remote-color'=>'33a766c2','row-numbers'=>'eec8698c','select-email'=>'ead22272','select-image'=>'f55c0231','slugify'=>'4d5adde6','sql-gemini'=>'fabc3537','sql-log'=>'b4355039','table-indexes-structure'=>'a90cc0c9','table-structure'=>'a8458e02','tables-filter'=>'f8f51976','timeout'=>'90597366','version-github'=>'497af47b','version-noverify'=>'966937e9','clickhouse'=>'5bb80dfb','elastic'=>'f7017c4','firebird'=>'5499d1a','igdb'=>'170d083','imap'=>'ac143217','mongo'=>'c3b8f5a4','redis'=>'12f1a73b','simpledb'=>'79488f8b',);}function
__call($D,array$Mg){$va=array();foreach($Mg
as$x=>$X)$va[]=&$Mg[$x];$J=null;foreach($this->hooks[$D]as$dh){$Y=call_user_func_array(array($dh,$D),$va);if($Y!==null){if(!self::$append[$D])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($u,$bg=null){$va=func_get_args();$va[0]=idx($this->translations[LANG],$u)?:$u;return
call_user_func_array('Adminer\lang_format',$va);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$V,$Xg){mysqli_report(MYSQLI_REPORT_OFF);list($Wd,$gh)=host_port($N);$Ai=adminer()->connectSsl();$Qj=($Ai&&($Ai['key']||$Ai['cert']||$Ai['ca']||isset($Ai['verify'])));if($Qj)$this->ssl_set($Ai['key'],$Ai['cert'],$Ai['ca'],'','');$J=@$this->real_connect(($N!=""?$Wd:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$Xg!=""?$Xg:ini_get("mysqli.default_pw")),null,(is_numeric($gh)?intval($gh):ini_get("mysqli.default_port")),(is_numeric($gh)?null:$gh),($Qj?($Ai['verify']!==false?MYSQLI_CLIENT_SSL:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($Va){if(parent::set_charset($Va))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Va");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}function
inTransaction(){return
false;}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$V,$Xg){if(ini_bool("mysql.allow_local_infile"))return
lang(77,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),($N.$V!=""?$V:ini_get("mysql.default_user")),($N.$V.$Xg!=""?$Xg:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Va){return
mysql_set_charset($Va,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Mb){return
mysql_select_db($Mb,$this->link);}function
query($H,$Aj=false){$I=@($Aj?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
attach($N,$V,$Xg){$vg=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);if(isset($_GET["select"]))$vg[\PDO::MYSQL_ATTR_MULTI_STATEMENTS]=false;$Ai=adminer()->connectSsl();if($Ai){if($Ai['key'])$vg[\PDO::MYSQL_ATTR_SSL_KEY]=$Ai['key'];if($Ai['cert'])$vg[\PDO::MYSQL_ATTR_SSL_CERT]=$Ai['cert'];if($Ai['ca'])$vg[\PDO::MYSQL_ATTR_SSL_CA]=$Ai['ca'];if(isset($Ai['verify']))$vg[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Ai['verify'];}list($Wd,$gh)=host_port($N);return$this->dsn("mysql:charset=utf8".($Wd!=""?";host=$Wd":'').($gh?(is_numeric($gh)?";port=":";unix_socket=").$gh:""),$V,$Xg,$vg);}function
set_charset($Va){return$this->query("SET NAMES $Va");}function
select_db($Mb){return$this->query("USE ".idf_escape($Mb));}function
query($H,$Aj=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Aj);return
parent::query($H,$Aj);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");static
function
connect($N,$V,$Xg){$e=parent::connect($N,$V,$Xg);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($Wh=iconv("windows-1252","utf-8//IGNORE",$e))>strlen($e))$e=$Wh;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array(lang(78)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(79)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(80)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(81)=>array("enum"=>65535,"set"=>64),lang(82)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(83)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types[lang(80)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(80)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(84)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(84)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(78)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$l){return(preg_match("~binary~",$l["type"])?"<code class='jush-sql'>UNHEX</code>":($l["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($l["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geometry|point|linestring|polygon~",$l["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$ph){$d=array_keys(reset($L));$nh="INSERT INTO ".table($R)." (".implode(", ",$d).") VALUES\n";$Wj=array();foreach($d
as$x)$Wj[$x]="$x = VALUES($x)";$Hi="\nON DUPLICATE KEY UPDATE ".implode(", ",$Wj);$Wj=array();$y=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($Wj&&(strlen($nh)+$y+strlen($Y)+strlen($Hi)>1e6)){if(!queries($nh.implode(",\n",$Wj).$Hi))return
false;$Wj=array();$y=0;}$Wj[]=$Y;$y+=strlen($Y)+2;}return
queries($nh.implode(",\n",$Wj).$Hi);}function
slowQuery($H,$dj){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$dj FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$B))return"$B[1] /*+ MAX_EXECUTION_TIME(".($dj*1000).") */ $B[2]";}}function
convertSearch($u,array$X,array$l){return(preg_match('~char|text|enum|set~',$l["type"])&&!preg_match("~^utf8~",$l["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($u USING ".charset($this->conn).")":$u);}function
typeName(\stdClass$l){$_j=array("decimal","tinyint","smallint","int","float","double",7=>"timestamp","bigint","mediumint","date","time","datetime","year",15=>"varchar","bit",242=>"vector",245=>"json","decimal","enum","set","tinytext","mediumtext","longtext","text","varchar","char","geometry",);$J=idx($_j,$l->type,"");return
parent::typeName($l)?:($l->charsetnr==63?str_replace(array("text","varchar","char"),array("blob","varbinary","binary"),$J):$J);}function
quoteBinary($Wh){return"X".q(bin2hex($Wh));}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($D,$Ee=false){$jf=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower(str_replace("_","-",DB)."-".($jf?"$D-table/":str_replace("_","-",$D)."-table.html"));if(DB=="sys")return($jf?"sys-schema/":strtolower("sys-".str_replace("_","-",preg_replace('~^x\$~','',$D)).".html"));if(DB=="mysql")return($jf?"mysql$D-table/":"system-schema.html");}function
partitionsInfo($R){$vd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $vd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$K=($I?$I->fetch_row():null);if(!$K)return
array();$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$K;$Ug=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $vd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($Ug);$J["partition_values"]=array_values($Ug);return$J;}function
hasCStyleEscapes(){static$Qa;if($Qa===null){$zi=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Qa=(strpos($zi,'NO_BACKSLASH_ESCAPES')===false);}return$Qa;}function
lineComment(){return"#|-- ";}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$Ni){return(preg_match('~^(MEMORY|NDB)$~',$Ni["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($u){return"`".str_replace("`","``",$u)."`";}function
table($u){return
idf_escape($u);}function
get_databases($nd){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$Bi=microtime(true);$J=($nd?slow_query($H):get_vals($H));if(microtime(true)-$Bi>0.1){restart_session();set_session("dbs",$J);stop_session();}}return$J;}function
limit($H,$Z,$z,$gg=0,$hi=" "){return" $H$Z".($z?$hi."LIMIT $z".($gg?" OFFSET $gg":""):"");}function
limit1($R,$H,$Z,$hi="\n"){return
limit($H,$Z,1,0,$hi);}function
db_collation($i,array$ib){$J=null;$g=get_val("SHOW CREATE DATABASE ".idf_escape($i),1);if(preg_match('~ COLLATE ([^ ]+)~',$g,$B))$J=$B[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$g,$B))$J=$ib[$B[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$h){$J=array();foreach($h
as$i)$J[$i]=count(get_vals("SHOW TABLES IN ".idf_escape($i)));return$J;}function
table_status($D="",$Yc=false){$J=array();foreach(get_rows($Yc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($D!=""?"AND TABLE_NAME = ".q($D):"ORDER BY Name"):"SHOW TABLE STATUS".($D!=""?" LIKE ".q(addcslashes($D,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($D!="")$K["Name"]=$D;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
parse_type($xd){preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$xd,$B);return
array($B[1],$B[2],ltrim($B[3].$B[4]));}function
fields($R){$jf=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$l=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$Ad=$K["GENERATION_EXPRESSION"];$Vc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Vc,$_d);list($zj,$y,$Gj)=parse_type($U);$j=$K["COLUMN_DEFAULT"];if($j!=""){$De=preg_match('~text|json~',$zj);if(!$jf&&$De)$j=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($j));if($jf||$De){$j=($j=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($B){return
stripslashes(str_replace("''","'",$B[1]));},$j));}if(!$jf&&preg_match('~binary~',$zj)&&preg_match('~^0x(\w*)$~',$j,$B))$j=pack("H*",$B[1]);}$J[$l]=array("field"=>$l,"full_type"=>$U,"type"=>$zj,"length"=>$y,"unsigned"=>$Gj,"default"=>($_d?($jf?$Ad:stripslashes($Ad)):$j),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Vc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Vc,$B)?$B[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($_d[1]=="PERSISTENT"?"STORED":$_d[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$D=$K["Key_name"];$J[$D]["type"]=($D=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$D]["columns"][]=$K["Column_name"];$J[$D]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$D]["descs"][]=null;$J[$D]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$Zg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$Ab=get_val("SHOW CREATE TABLE ".table($R),1);if($Ab){preg_match_all("~CONSTRAINT ($Zg) FOREIGN KEY ?\\(((?:$Zg,? ?)+)\\) REFERENCES ($Zg)(?:\\.($Zg))? \\(((?:$Zg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$Ab,$lf,PREG_SET_ORDER);foreach($lf
as$B){preg_match_all("~$Zg~",$B[2],$ui);preg_match_all("~$Zg~",$B[5],$Wi);$J[idf_unescape($B[1])]=array("db"=>idf_unescape($B[4]!=""?$B[3]:$B[4]),"table"=>idf_unescape($B[4]!=""?$B[4]:$B[3]),"source"=>array_map('Adminer\idf_unescape',$ui[0]),"target"=>array_map('Adminer\idf_unescape',$Wi[0]),"on_delete"=>($B[6]?:"RESTRICT"),"on_update"=>($B[7]?:"RESTRICT"),);}}return$J;}function
view($D){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($D),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$x=>$X)sort($J[$x]);return$J;}function
information_schema($i,$Yh=""){return($i=="information_schema")||(min_version(5.5)&&$i=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($i,$hb){return
queries("CREATE DATABASE ".idf_escape($i).($hb?" COLLATE ".q($hb):""));}function
drop_databases(array$h){$J=apply_queries("DROP DATABASE",$h,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($D,$hb){$J=false;if(create_database($D,$hb)){$T=array();$bk=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$bk[]=$R;else$T[]=$R;}$J=(!$T&&!$bk)||move_tables($T,$bk,$D);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$Ba=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$v){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$v["columns"],true)){$Ba="";break;}if($v["type"]=="PRIMARY")$Ba=" UNIQUE";}}return" AUTO_INCREMENT$Ba";}function
alter_table($R,$D,array$m,array$pd,$lb,$Ac,$hb,$Aa,$Tg){$ra=array();foreach($m
as$l){if($l[1]){$j=$l[1][3];if(preg_match('~ GENERATED~',$j)){$l[1][3]=(connection()->flavor=='maria'?"":$l[1][2]);$l[1][2]=$j;}$ra[]=($R!=""?($l[0]!=""?"CHANGE ".idf_escape($l[0]):"ADD"):" ")." ".implode($l[1]).($R!=""?$l[2]:"");}else$ra[]="DROP ".idf_escape($l[0]);}$ra=array_merge($ra,$pd);$P=($lb!==null?" COMMENT=".q($lb):"").($Ac?" ENGINE=".q($Ac):"").($hb?" COLLATE ".q($hb):"").($Aa!=""?" AUTO_INCREMENT=$Aa":"");if($Tg){$Ug=array();if($Tg["partition_by"]=='RANGE'||$Tg["partition_by"]=='LIST'){foreach($Tg["partition_names"]as$x=>$X){$Y=$Tg["partition_values"][$x];$Ug[]="\n  PARTITION ".idf_escape($X)." VALUES ".($Tg["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $Tg[partition_by]($Tg[partition])";if($Ug)$P
.=" (".implode(",",$Ug)."\n)";elseif($Tg["partitions"])$P
.=" PARTITIONS ".(+$Tg["partitions"]);}elseif($Tg===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($D)." (\n".implode(",\n",$ra)."\n)$P");if($R!=$D)$ra[]="RENAME TO ".table($D);if($P)$ra[]=ltrim($P);return($ra?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$ra)):true);}function
alter_indexes($R,$ra){$Ta=array();foreach($ra
as$X)$Ta[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Ta));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$bk){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$bk)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$bk,$Wi){$Kh=array();foreach($T
as$R)$Kh[]=table($R)." TO ".idf_escape($Wi).".".table($R);if(!$Kh||queries("RENAME TABLE ".implode(", ",$Kh))){$Vb=array();foreach($bk
as$R)$Vb[table($R)]=view($R);connection()->select_db($Wi);$i=idf_escape(DB);foreach($Vb
as$D=>$ak){if(!queries("CREATE VIEW $D AS ".str_replace(" $i."," ",$ak["select"]))||!queries("DROP VIEW $i.$D"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$bk,$Wi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$D=($Wi==DB?table("copy_$R"):idf_escape($Wi).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $D"))||!queries("CREATE TABLE $D LIKE ".table($R))||!queries("INSERT INTO $D SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$sj=$K["Trigger"];if(!queries("CREATE TRIGGER ".($Wi==DB?idf_escape("copy_$sj"):idf_escape($Wi).".".idf_escape($sj))." $K[Timing] $K[Event] ON $D FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($bk
as$R){$D=($Wi==DB?table("copy_$R"):idf_escape($Wi).".".table($R));$ak=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $D"))||!queries("CREATE VIEW $D AS $ak[select]"))return
false;}return
true;}function
trigger($D,$R){if($D=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($D));return
reset($L);}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($D,$U){$L=get_rows("SELECT PARAMETER_NAME, DTD_IDENTIFIER, PARAMETER_MODE, CHARACTER_SET_NAME
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($D)."
ORDER BY ORDINAL_POSITION");$m=array();foreach($L
as$K){$xd=$K["DTD_IDENTIFIER"];list($zj,$y,$Gj)=parse_type($xd);$m[]=array("field"=>$K["PARAMETER_NAME"],"type"=>$zj,"length"=>$y,"unsigned"=>$Gj,"null"=>true,"full_type"=>$xd,"inout"=>($U=="FUNCTION"?"":$K["PARAMETER_MODE"]),"collation"=>$K["CHARACTER_SET_NAME"],);}$J=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	CONCAT(IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC\\n', ''), IF(SQL_DATA_ACCESS != 'CONTAINS SQL', CONCAT(SQL_DATA_ACCESS, '\\n'), ''), ROUTINE_DEFINITION) definition,
	'SQL' language
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($D))->fetch_assoc();if($m&&$m[0]['field']=='')$J['returns']=array_shift($m);$J['fields']=$m;return$J;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($D,array$K){return
idf_escape($D);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$Aa,$Fi){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$Aa)$J=preg_replace('~(\n\)[^\n]*?) AUTO_INCREMENT=\d+~','\1',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Mb,$Fi=""){$D=idf_escape($Mb);$J="";if(preg_match('~CREATE~',$Fi)&&($g=get_val("SHOW CREATE DATABASE $D",1))){set_utf8mb4($g);if($Fi=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $D;\n";$J
.="$g;\n";}return$J."USE $D";}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K)$J
.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$l){if(preg_match("~binary~",$l["type"]))return"HEX(".idf_escape($l["field"]).")";if($l["type"]=="bit")return"BIN(".idf_escape($l["field"])." + 0)";if($l["type"]=="vector")return(connection()->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."(".idf_escape($l["field"]).")";if(preg_match("~geometry|point|linestring|polygon~",$l["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($l["field"]).")";}function
unconvert_field(array$l,$J){if(preg_match("~binary~",$l["type"]))$J="UNHEX($J)";if($l["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($l["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geometry|point|linestring|polygon~",$l["type"])){$nh=(min_version(8)?"ST_":"");$J=$nh."GeomFromText($J, $nh"."SRID($l[field]))";}return$J;}function
support($Zc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').(min_version(8,99)?'|fast_status':'').')$~',$Zc);}function
kill_process($t){return
queries("KILL ".number($t));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types($Uc=false){return
array();}function
type_values($t){return"";}function
type_definition($t){return
array("kind"=>"","definition"=>"");}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($Yh,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').($_GET["ext"]?"ext=".url_escape($_GET["ext"]).'&':'').(isset($_GET[DRIVER])?DRIVER."=".url_escape(SERVER).'&':'').(isset($_GET["username"])?"username=".url_escape($_GET["username"]).'&':'').(DB!=""?'db='.url_escape(DB).'&'.(isset($_GET["ns"])?"ns=".url_escape($_GET["ns"])."&":""):''));function
page_header($fj,$k="",$Oa=array(),$gj=""){page_headers();if(is_ajax()&&$k){page_messages($k);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$hj=$fj.($gj!=""?": $gj":"");$ij=strip_tags($hj.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang=\'',LANG,'\' dir=\'',lang(85),'\' class=\'',lang(85),' nojs\'>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$ij,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=6.0.0"),'">
';$Eb=adminer()->css();if(is_int(key($Eb)))$Eb=array_fill_keys($Eb,'light');$Ld=in_array('light',$Eb)||in_array('',$Eb);$Jd=in_array('dark',$Eb)||in_array('',$Eb);$Ib=($Ld?($Jd?null:false):($Jd?:null));$zf=" media='(prefers-color-scheme: dark)'";if($Ib!==false)echo"<link rel='stylesheet'".($Ib?"":$zf)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=6.0.0")."'>\n";echo"<meta name='color-scheme' content='".($Ib===null?"light dark":($Ib?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=6.0.0");if(adminer()->head($Ib))echo"<link rel='icon' href='data:image/gif;base64,"."R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=6.0.0")."'>\n";foreach($Eb
as$Lj=>$Lf){$b=($Lf=='dark'&&!$Ib?$zf:($Lf=='light'&&$Jd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$b href='".h($Lj)."'>\n";}echo"\n<body class='";adminer()->bodyClass();echo"'>\n",script((isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"onload = partial(verifyVersion, '".VERSION."');\n")."
const offlineMessage = '".js_escape(lang(86))."';
const thousandsSeparator = '".js_escape(lang(5))."';
const urlSeparators = '".js_escape(ini_get("arg_separator.input"))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'".on('mouseover','helpKeep').on('mouseout','helpMouseout')."></div>\n","<div id='content'>\n","<span id='menuopen' class='jsonly'".on('click','menuToggle')."><button title='".lang(87)."' class='icon icon-move' aria-expanded='false'></button></span>\n";if($Oa!==null){$_=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($_?:".").'">'.get_driver(DRIVER).'</a> » ';$_=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:lang(27));if($Oa===false)echo"$N\n";else{echo"<a href='".h($_)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Oa)))echo'<a href="'.h($_."&db=".url_escape(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Oa)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Oa
as$x=>$X){$Xb=(is_array($X)?$X[1]:h($X));if($Xb!="")echo"<a href='".h(ME."$x=").url_escape(is_array($X)?$X[0]:$X)."'>$Xb</a> » ";}}echo"$fj\n";}}echo"<h2>$hj</h2>\n","<div id='ajaxstatus' role='status' class='jsonly'></div>\n";restart_session();page_messages($k);$h=&get_session("dbs");if(DB!=""&&$h&&!in_array(DB,$h,true))$h=null;stop_session();define('Adminer\PAGE_HEADER',1);ob_flush();flush();}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$Db){$Od=array();foreach($Db
as$x=>$X)$Od[]="$x $X";header("Content-Security-Policy: ".implode("; ",$Od));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"https://www.adminer.org","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
design_checksums(){$Rj=array();foreach(array_keys(adminer()->css())as$Lj)$Rj[preg_replace('~\?.*~','',$Lj)]=true;$J=array();foreach(array("adminer.css","adminer-dark.css")as$n){if($Rj[$n]&&file_exists($n)){preg_match('~^/\* Adminer design ([-\w]+) \*/~',file_get_contents($n),$B);$J[$n]=array((string)$B[1],Plugins::checksum($n));}}return$J;}function
official_design_checksums(){return
array('adminer-border/adminer-dark.css'=>'b2527e3','adminer-border/adminer.css'=>'430977ad','adminer-dark/adminer-dark.css'=>'a26bcd7b','brade/adminer.css'=>'be4161f0','bueltge/adminer.css'=>'1a8f00b4','dracula/adminer-dark.css'=>'cfaf61dd','esterka/adminer.css'=>'1f805f36','flat/adminer.css'=>'49a61af9','galkaev/adminer-dark.css'=>'16c46f94','haeckel/adminer.css'=>'147a3565','hever/adminer.css'=>'78b8cd43','konya/adminer.css'=>'3cc606c5','lavender-light/adminer.css'=>'bf03f5d7','lucas-sandery/adminer.css'=>'6596353','mancave/adminer-dark.css'=>'e1ac813d','mvt/adminer.css'=>'ebd3afdc','nette/adminer.css'=>'5ab360e7','ng9/adminer.css'=>'488583cf','nicu/adminer.css'=>'ecb9bd1e','pappu687/adminer.css'=>'b58d128c','paranoiq/adminer.css'=>'64d27e5','pepa-linha/adminer.css'=>'baf25f0','pokorny/adminer.css'=>'ee9eea6d','price/adminer.css'=>'b3c939b2','rmsoft/adminer.css'=>'391d54ad','rmsoft_blue-dark/adminer.css'=>'17714d77','rmsoft_blue/adminer.css'=>'c0f192ea','win98/adminer.css'=>'e82d63c3',);}function
version_iframe(){return(isset($_COOKIE["adminer_version"])||!adminer()->verifyVersion()?"":"<noscript><iframe sandbox src='https://www.adminer.org/version/?current=".VERSION."&amp;noscript=1'></iframe></noscript>");}function
get_nonce(){static$Yf;if(!$Yf)$Yf=base64_encode(rand_string());return$Yf;}function
page_messages($k){$Kj=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$Ef=idx($_SESSION["messages"],$Kj);if($Ef){echo"<div class='message'>".implode("</div>\n<div class='message'>",$Ef)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Kj]);}if($k)echo"<div class='error'>$k</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($Kf=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($Kf);echo"</div>\n";if($Kf!="auth")echo'<form action="" method="post">
<p class="logout">
<span title="',lang(29),'">',h($_GET["username"])."\n",'</span>
<input type=\'submit\' name=\'logout\' value=\'',lang(88),'\' id=\'logout\'>
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($Pf){while($Pf>=2147483648)$Pf-=4294967296;while($Pf<=-2147483649)$Pf+=4294967296;return(int)$Pf;}function
long2str(array$W,$dk){$Wh='';foreach($W
as$X)$Wh
.=pack('V',$X);if($dk)return
substr($Wh,0,end($W));return$Wh;}function
str2long($Wh,$dk){$W=array_values(unpack('V*',str_pad($Wh,4*ceil(strlen($Wh)/4),"\0")));if($dk)$W[]=strlen($Wh);return$W;}function
xxtea_mx($lk,$kk,$Ii,$Je){return
int32((($lk>>5&0x7FFFFFF)^$kk<<2)+(($kk>>3&0x1FFFFFFF)^$lk<<4))^int32(($Ii^$kk)+($Je^$lk));}function
encrypt_string($Di,$x){if($Di=="")return"";$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($Di,true);$Pf=count($W)-1;$lk=$W[$Pf];$kk=$W[0];$wh=floor(6+52/($Pf+1));$Ii=0;while($wh-->0){$Ii=int32($Ii+0x9E3779B9);$tc=$Ii>>2&3;for($Jg=0;$Jg<$Pf;$Jg++){$kk=$W[$Jg+1];$Of=xxtea_mx($lk,$kk,$Ii,$x[$Jg&3^$tc]);$lk=int32($W[$Jg]+$Of);$W[$Jg]=$lk;}$kk=$W[0];$Of=xxtea_mx($lk,$kk,$Ii,$x[$Jg&3^$tc]);$lk=int32($W[$Pf]+$Of);$W[$Pf]=$lk;}return
long2str($W,false);}function
decrypt_string($Di,$x){if($Di=="")return"";if(!$x)return
false;$x=array_values(unpack("V*",pack("H*",md5($x))));$W=str2long($Di,false);$Pf=count($W)-1;$lk=$W[$Pf];$kk=$W[0];$wh=floor(6+52/($Pf+1));$Ii=int32($wh*0x9E3779B9);while($Ii){$tc=$Ii>>2&3;for($Jg=$Pf;$Jg>0;$Jg--){$lk=$W[$Jg-1];$Of=xxtea_mx($lk,$kk,$Ii,$x[$Jg&3^$tc]);$kk=int32($W[$Jg]-$Of);$W[$Jg]=$kk;}$lk=$W[$Pf];$Of=xxtea_mx($lk,$kk,$Ii,$x[$Jg&3^$tc]);$kk=int32($W[0]-$Of);$W[0]=$kk;$Ii=int32($Ii-0x9E3779B9);}return
long2str($W,true);}$bh=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($x)=explode(":",$X);$bh[$x]=$X;}}function
add_invalid_login(){$Ha=get_temp_dir()."/adminer-invalid";foreach(glob("$Ha*")?:array($Ha)as$n){$p=file_open_lock($n);if($p)break;}if(!$p)$p=file_open_lock("$Ha-".rand_string());if(!$p)return;$ye=json_decode(stream_get_contents($p),true);$cj=time();if($ye){foreach($ye
as$ze=>$X){if($X[0]<$cj)unset($ye[$ze]);}}$xe=&$ye[adminer()->bruteForceKey()];if(!$xe)$xe=array($cj+30*60,0);$xe[1]++;file_write_unlock($p,json_encode($ye));}function
check_invalid_login(array&$bh){$ye=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$n){$p=file_open_lock($n);if($p){$ye=json_decode(stream_get_contents($p),true);file_unlock($p);break;}}$x=adminer()->bruteForceKey();$xe=idx($ye,$x,array());$Xf=($xe[1]>29?$xe[0]-time():0);if($Xf>0){$k=lang(89,ceil($Xf/60));if($_SERVER["HTTP_X_FORWARDED_FOR"]!=""&&$x==$_SERVER["REMOTE_ADDR"])$k
.='<br>'.lang(90,'<b>login-reverse-proxy</b>'," href='https://www.adminer.org/plugins/?version=".VERSION."'".target_blank());auth_error($k,$bh);}}function
password_required(){static$J;if($J===null){$J=(bool)get_session("password_required");if(!$J){$Cb=adminer()->credentials();$J=!is_object(Driver::connect($Cb[0],$Cb[1],""));if($J)set_session("password_required",true);}}return$J;}$_a=$_POST["auth"];if($_a){session_regenerate_id();$Yj=$_a["driver"];$N=$_a["server"];$V=$_a["username"];$Xg=(string)$_a["password"];$i=$_a["db"];set_password($Yj,$N,$V,$Xg);$_SESSION["db"][$Yj][$N][$V][$i]=true;if($_a["permanent"]){$x=implode("-",array_map('base64_encode',array($Yj,$N,$V,$i)));$rh=adminer()->permanentLogin(true);$bh[$x]="$x:".base64_encode($rh?encrypt_string($Xg,$rh):"");cookie("adminer_permanent",implode(" ",$bh));}if(count($_POST)==1||DRIVER!=$Yj||SERVER!=$N||$_GET["username"]!==$V||DB!=$i)redirect(auth_url($Yj,$N,$V,$i));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$x)set_session($x,null);unset_permanent($bh);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(91).' '.lang(92));}elseif($bh&&!$_SESSION["pwds"]){session_regenerate_id();$rh=adminer()->permanentLogin();foreach($bh
as$x=>$X){list(,$cb)=explode(":",$X);list($Yj,$N,$V,$i)=array_map('base64_decode',explode("-",$x));set_password($Yj,$N,$V,decrypt_string(base64_decode($cb),$rh));$_SESSION["db"][$Yj][$N][$V][$i]=true;}}function
unset_permanent(array&$bh){foreach($bh
as$x=>$X){list($Yj,$N,$V,$i)=array_map('base64_decode',explode("-",$x));if($Yj==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$i==DB)unset($bh[$x]);}cookie("adminer_permanent",implode(" ",$bh));}function
auth_error($k,array&$bh){$ki=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$ki]||$_GET[$ki])&&!$_SESSION["token"])$k=lang(93);else{restart_session();add_invalid_login();$Xg=get_password();if($Xg!==null){if($Xg===false)$k
.=($k?'<br>':'').lang(94,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($bh);}}if(!$_COOKIE[$ki]&&$_GET[$ki]&&ini_bool("session.use_only_cookies"))$k=lang(95);$Mg=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$Mg["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(32),$k,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".lang(96)."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($bh);page_header(lang(97),lang(98,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){list($Wd,$gh)=host_port(SERVER);if(preg_match('~[^-\w.:/]~',$Wd.$gh))auth_error(lang(99),$bh);if(preg_match('~^-?\d+~',$gh,$B)&&($B[0]<1024||$B[0]>65535))auth_error(lang(100),$bh);check_invalid_login($bh);$Cb=adminer()->credentials();$e=Driver::connect($Cb[0],$Cb[1],$Cb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$hf=null;if(!is_object($e)||($hf=adminer()->login($_GET["username"],get_password()))!==true){$k=(is_string($e)?nl_br(h($e)):(is_string($hf)?$hf:lang(101))).(preg_match('~^ | $~',get_password())?'<br>'.lang(102):'');auth_error($k,$bh);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(88),lang(103));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($_a&&$_POST["token"])$_POST["token"]=get_token();$k='';if($_POST){if(!verify_token())$k=lang(103).' '.lang(104);}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$k=lang(105,"<b>post_max_size</b>'");if(isset($_GET["sql"]))$k
.=' '.lang(106);}function
print_select_result($I,$f=null,array$_g=array(),&$z=0){$df=array();$w=array();$d=array();$Ma=array();$_j=array();$J=array();for($s=0;(!$z||$s<$z)&&($K=$I->fetch_row());$s++){if(!$s){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($Fe=0;$Fe<count($K);$Fe++){$l=$I->fetch_field();$D=$l->name;$zg=(isset($l->orgtable)?$l->orgtable:"");$yg=(isset($l->orgname)?$l->orgname:$D);if($_g&&JUSH=="sql")$df[$Fe]=($D=="table"?"table=":($D=="possible_keys"?"indexes=":null));elseif($zg!=""){if(isset($l->table))$J[$l->table]=$zg;if(!isset($w[$zg])){$w[$zg]=array();foreach(indexes($zg,$f)as$v){if($v["type"]=="PRIMARY"){$w[$zg]=array_flip($v["columns"]);break;}}$d[$zg]=$w[$zg];}if(isset($d[$zg][$yg])){unset($d[$zg][$yg]);$w[$zg][$yg]=$Fe;$df[$Fe]=$zg;}}if($l->charsetnr==63)$Ma[$Fe]=true;$_j[$Fe]=$l->type;echo"<th title='".h(trim(($zg!=""?"$zg.$yg":($l->name!=$yg?$yg:""))." ".driver()->typeName($l)))."'>".h($D).($_g?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($D),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"<tbody>\n";}echo"<tr>";foreach($K
as$x=>$X){$_="";if(isset($df[$x])&&!$d[$df[$x]]){if($_g&&JUSH=="sql"){$R=$K[array_search("table=",$df)];$_=ME.$df[$x].url_escape($_g[$R]!=""?$_g[$R]:$R);}else{$_=ME."edit=".url_escape($df[$x]);foreach($w[$df[$x]]as$fb=>$Fe){if($K[$Fe]===null){$_="";break;}$_
.="&where[".url_escape(bracket_escape($fb))."]=".url_escape($K[$Fe]);}}}$l=array('type'=>($Ma[$x]?'blob':($_j[$x]==254?'char':'')),);$X=select_value($X,$_,$l,null);echo"<td".($_j[$x]<=9||$_j[$x]==246?" class='number'":"").">$X";}}$z=$s;echo($s?"</table>\n</div>":"<p class='message'>".lang(15))."\n";return$J;}function
referencable_primary($fi){$J=array();foreach(table_status('',true)as$Oi=>$R){if($Oi!=$fi&&fk_support($R)){foreach(fields($Oi)as$l){if($l["primary"]){if($J[$Oi]){unset($J[$Oi]);break;}$J[$Oi]=$l;}}}}return$J;}function
textarea($D,$Y,$L=10,$jb=80){echo"<textarea name='".h($D)."' rows='$L' cols='$jb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($b,array$vg,$Y="",$ch=""){if($vg&&$Y!=""&&!isset($vg[$Y]))$vg=array($Y=>$Y)+$vg;$Vi=($vg?"select":"input");return"<$Vi$b".($vg?"><option value=''>$ch".optionlist($vg,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$ch'>");}function
json_row($x,$X=null,$Ic=true){static$kd=true;if($kd)echo"{";if($x!=""){echo($kd?"":",")."\n\t\"".addcslashes($x,"\r\n\t\"\\/").'": '.($X!==null?($Ic?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$kd=false;}else{echo"\n}\n";$kd=true;}}function
edit_type($x,array$l,array$ib,array$rd=array(),array$Wc=array()){$U=(string)$l["type"];echo"<td><select name='".h($x)."[type]' class='type' aria-labelledby='label-type'".on_help_value().">";if($U&&!array_key_exists($U,driver()->types())&&!isset($rd[$U])&&!in_array($U,$Wc))$Wc[]=$U;$Ei=driver()->structuredTypes();if($rd)$Ei[lang(107)]=$rd;echo
optionlist(array_merge($Wc,$Ei),$U),"</select><td>","<input name='".h($x)."[length]' value='".h($l["length"])."' size='3'".(!$l["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($ib?"<input list='collations' name='".h($x)."[collation]'".option_types($U,'(char|text|enum|set)$')." value='".h($l["collation"])."' placeholder='(".lang(108).")'>":''),(driver()->unsigned?"<select name='".h($x)."[unsigned]'".option_types($U,'^$|'.number_type()).'><option>'.optionlist(driver()->unsigned,$l["unsigned"]).'</select>':''),(isset($l['on_update'])?"<select name='".h($x)."[on_update]'".option_types($U,'timestamp|datetime').'>'.optionlist(array(""=>"(".lang(109).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"CURRENT_TIMESTAMP":$l["on_update"])).'</select>':''),($rd?"<select name='".h($x)."[on_delete]'".option_types($U,'`')."><option value=''>(".lang(110).")".optionlist(explode("|",driver()->onActions),$l["on_delete"])."</select> ":" ");}function
option_types($U,$_j){return" data-types='".h($_j)."'".(preg_match("~$_j~",$U)?"":" class='hidden'");}function
process_length($y){$Dc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$Dc(?:\\s*,\\s*$Dc)*+\\s*\\)?\\s*\$~",$y)&&preg_match_all("~$Dc~",$y,$lf)?"(".implode(",",$lf[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$y)));}function
process_type(array$l,$gb="COLLATE"){return" $l[type]".process_length($l["length"]).(preg_match(number_type(),$l["type"])&&in_array($l["unsigned"],driver()->unsigned)?" $l[unsigned]":"").(preg_match('~char|text|enum|set~',$l["type"])&&$l["collation"]?" $gb ".(JUSH=="mssql"?$l["collation"]:q($l["collation"])):"");}function
process_field(array$l,array$yj){if($l["on_update"])$l["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$l["on_update"]);return
array(idf_escape(trim($l["field"])),process_type($yj),($l["null"]?" NULL":" NOT NULL"),default_value($l),(preg_match('~timestamp|datetime~',$l["type"])&&$l["on_update"]?" ON UPDATE $l[on_update]":""),(support("comment")&&$l["comment"]!=""?" COMMENT ".q($l["comment"]):""),($l["auto_increment"]?auto_increment():null),);}function
default_value(array$l){if($l["default"]===null)return"";$j=str_replace("\r","",$l["default"]);$_d=$l["generated"];return(in_array($_d,driver()->generated)?(JUSH=="mssql"?" AS ($j)".($_d=="VIRTUAL"?"":" $_d"):" GENERATED ALWAYS AS ($j) $_d"):(preg_match('~^GENERATED ~i',$j)?" $j":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set|String~',$l["type"])||preg_match('~^(?![a-z])~i',$j)?(JUSH=="sql"&&preg_match('~text|json~',$l["type"])?"(".q($j).")":q($j)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($j)":$j)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$x=>$X){if(preg_match("~$x|$X~",$U))return" class='$x'";}}function
edit_fields(array$m,array$ib,$U="TABLE",array$rd=array()){$m=array_values($m);$Sb=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$mb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(111):lang(112)),"<td id='label-type'>".lang(43)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' hidden></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".lang(113),"<td>".lang(114);if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(45)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<td id='label-default'$Sb>".lang(46),(support("comment")?"<td id='label-comment'$mb>".lang(44):"");$Se=!support("move_col");echo"<td>".icon("plus","add[".($Se?count($m):0)."]","+",lang(115),($Se?on('click','editingAddLastRow'):"")),"<tbody".on('click','editingClick').on('input','editingInput').on('keydown','editingKeydown').">\n";foreach($m
as$s=>$l){$s++;$Ag=$l[($_POST?"orig":"field")];$ec=(isset($_POST["add"][$s-1])||(isset($l["field"])&&!idx($_POST["drop_col"],$s)))&&(support("drop_col")||$Ag=="");echo"<tr".($ec?"":" hidden").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$s][inout]",explode("|",driver()->inout),$l["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(116))." ":"");if($ec)echo"<input name='fields[$s][field]' value='".h($l["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$s-1])?" autofocus":"").">";echo
input_hidden("fields[$s][orig]",$Ag);edit_type("fields[$s]",$l,$ib,$rd);if($U=="TABLE"){echo"<td><label class='block'>".checkbox("fields[$s][null]",1,$l["null"],"","","","label-null")."</label>","<td><label class='block'><input type='radio' name='auto_increment_col' value='$s'".($l["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Sb>".(driver()->generated?html_select("fields[$s][generated]",array_merge(array("","DEFAULT"),driver()->generated),$l["generated"])." ":checkbox("fields[$s][generated]",1,$l["generated"],"","","","label-default"));$b=" name='fields[$s][default]' aria-labelledby='label-default'";$Y=h($l["default"]);echo(preg_match('~\n~',$l["default"])?"<textarea$b rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$b value='$Y'>");if(support("comment")){$b=" name='fields[$s][comment]' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'";echo"<td$mb>".adminer()->commentInput('COLUMN',$b,$l["comment"]);}}echo"<td>",(support("move_col")?icon("plus","add[$s]","+",lang(115))." ":""),($Ag==""||support("drop_col")?icon("cross","drop_col[$s]","x",lang(117)):"");}}function
process_fields(array&$m){if($_POST["add"]){$m=array_values($m);array_splice($m,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
normalize_enum(array$B){$X=$B[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($X[0].$X[0],$X[0],substr($X,1,-1))),'\\'))."'";}function
grant($Bd,array$th,$d,$mg){if(!$th)return
true;if($th==array("ALL PRIVILEGES","GRANT OPTION"))return($Bd=="GRANT"?queries("$Bd ALL PRIVILEGES$mg WITH GRANT OPTION"):queries("$Bd ALL PRIVILEGES$mg")&&queries("$Bd GRANT OPTION$mg"));return
queries("$Bd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$d, ",$th).$d).$mg);}function
drop_create($pc,$g,$qc,$Zi,$rc,$A,$Df,$Bf,$Cf,$kg,$Uf){if($_POST["drop"])query_redirect($pc,$A,$Df);elseif($kg=="")query_redirect($g,$A,$Cf);elseif(support("transaction_ddl")){driver()->begin();queries_redirect($A,$Bf,queries($pc)&&queries($g)&&driver()->commit());driver()->rollback();}elseif($kg!=$Uf){$Bb=queries($g);queries_redirect($A,$Bf,$Bb&&queries($pc));if($Bb)queries($qc);}else
queries_redirect($A,$Bf,queries($Zi)&&queries($rc)&&queries($pc)&&queries($g));}function
create_trigger($mg,array$K){$ej=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$mg.$ej:$ej.$mg).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
q_dollar($Q){$Wb='$$';while(strpos($Q.$Wb,$Wb)!=strlen($Q))$Wb='$_'.substr($Wb,1);return$Wb.$Q.$Wb;}function
create_routine($Sh,array$K){$O=array();$m=(array)$K["fields"];ksort($m);foreach($m
as$l){if($l["field"]!="")$O[]=(preg_match("~^(".driver()->inout.")\$~",$l["inout"])?"$l[inout] ":"").idf_escape($l["field"]).process_type($l,"CHARACTER SET");}$Ub=rtrim($K["definition"],";");return"CREATE $Sh ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($Sh=="FUNCTION"?" RETURNS".process_type($K["returns"],"CHARACTER SET"):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q_dollar("\n".trim($Ub)."\n"):"\n$Ub;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$o){$i=$o["db"];$Zf=$o["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$o["source"])).") REFERENCES ".($i!=""&&$i!=$_GET["db"]?idf_escape($i).".":"").($Zf!=""&&$Zf!=$_GET["ns"]?idf_escape($Zf).".":"").idf_escape($o["table"])." (".implode(", ",array_map('Adminer\idf_escape',$o["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$o["on_delete"])?" ON DELETE $o[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$o["on_update"])?" ON UPDATE $o[on_update]":"").($o["deferrable"]?" $o[deferrable]":"");}function
tar_file($n,$jj){$J=pack("a100a8a8a8a12a12",$n,644,0,0,decoct($jj->size),decoct(time()));$ab=8*32;for($s=0;$s<strlen($J);$s++)$ab+=ord($J[$s]);$J
.=sprintf("%06o",$ab)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$jj->send();echo
str_repeat("\0",511-($jj->size+511)%512);}function
doc_link(array$Yg,$aj="<sup>?</sup>"){$ii=connection()->server_info;$Zj=preg_replace('~^(\d\.?\d).*~s','\1',$ii);$Mj=array('sql'=>"https://dev.mysql.com/doc/refman/$Zj/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$Zj)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$ii)."&id=",);if(connection()->flavor=='maria'){$Mj['sql']="https://mariadb.com/kb/en/";$Yg['sql']=(isset($Yg['mariadb'])?$Yg['mariadb']:str_replace(".html","/",$Yg['sql']));}return($Yg[JUSH]?"<a href='".h($Mj[JUSH].$Yg[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$Zj":""))."'".target_blank().">$aj</a>":"");}function
db_size($i){if(!connection()->select_db($i))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($g){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$g)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(DB==""&&isset($_GET["ns"]))redirect(remove_from_uri('ns'));if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header(lang(31).": ".h(DB),lang(118),true);}else{if($_POST["db"]&&!$k)queries_redirect(substr(ME,0,-1),lang(119),drop_databases($_POST["db"]));page_header(lang(120),$k,false);echo"<p class='links'>\n";foreach(array('database'=>lang(121),'privileges'=>lang(65),'processlist'=>lang(122),'variables'=>lang(123),'status'=>lang(124),)as$x=>$X){if(support($x))echo"<a href='".h(ME)."$x='>$X</a>\n";}echo"<p>".lang(125,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(126,"<b>".h(logged_user())."</b>")."\n";$h=adminer()->databases();if($h){$Zh=support("scheme");$ib=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n","<thead><tr>".(support("database")?"<td class='hover'>":"")."<th".(JUSH!='mssql'?" aria-sort='ascending'":"").">".lang(31).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(127)."</a>":"")."<td>".lang(128)."<td>".lang(129)."<td>".lang(130)." - <a href='".h(ME)."dbsize=1'".on('click','ajaxSetHtml',ME."script=connect").">".lang(131)."</a>"."<tbody>\n";$h=($_GET["dbsize"]?count_tables($h):array_flip($h));foreach($h
as$i=>$T){$Rh=h(ME)."db=".url_escape($i);$t=h("Db-".$i);echo"<tr>".(support("database")?"<td class='hover'>".checkbox("db[]",$i,in_array($i,(array)$_POST["db"]),"","","",$t):""),"<th><a href='$Rh' id='$t'>".h($i)."</a>";$hb=h(db_collation($i,$ib));echo"<td>".(support("database")?"<a href='$Rh".($Zh?"&amp;ns=":"")."&amp;database=' title='".lang(61)."'>$hb</a>":$hb),"<td align='right'><a href='$Rh&amp;schema=' id='tables-".h($i)."' title='".lang(64)."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($i)."'>".($_GET["dbsize"]?db_size($i):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(132)." <span id='selected'></span></legend><div>\n"."<input type='hidden' name='all' value=''".on('click','countDbs').">\n"."<input type='submit' name='drop' value='".lang(133)."'".confirm().">\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}$ja=adminer();$fh=($ja
instanceof
Plugins?$ja->plugins:array());$oc=($ja
instanceof
Plugins?$ja->drivers:array());$bc=design_checksums();if($fh||$oc||$bc){$bb=($ja
instanceof
Plugins?$ja->checksums():array());$dg=Plugins::officialChecksums();$Jj=function($Lj){return" (<a href='$Lj'".target_blank()." class='update'>".VERSION."</a>)";};$eh=function($ed)use($bb,$dg,$Jj){return($bb[$ed]&&$dg[$ed]&&$bb[$ed]!==$dg[$ed]?$Jj("https://www.adminer.org/plugins/?version=".VERSION):"");};echo"<div class='plugins'>\n","<h3>".lang(134)."</h3>\n<ul>\n";foreach($fh
as$dh){$Gh=new
\ReflectionObject($dh);$Yb=(method_exists($dh,'description')?$dh->description():"");if(!$Yb){if(preg_match('~^/[\s*]+(.+)~',$Gh->getDocComment(),$B))$Yb=$B[1];}$ai=(method_exists($dh,'screenshot')?$dh->screenshot():"");echo"<li><b>".get_class($dh)."</b>".h($Yb?": $Yb":"").($ai?" (<a href='".h($ai)."'".target_blank().">".lang(135)."</a>)":"").$eh(basename((string)$Gh->getFileName(),'.php'))."\n";}foreach($oc
as$t=>$D)echo"<li><b>".h($t)."</b>: ".h($D).$eh(basename((string)$ja->driverFiles[$t],'.php'))."\n";if($bc){$fg=official_design_checksums();foreach($bc
as$n=>$ac){list($D,$ab)=$ac;$eg=$fg["$D/$n"];echo"<li><b>".h($n)."</b>".h($D?": $D":"").($eg&&$eg!==$ab?$Jj("https://www.adminer.org/?version=".VERSION."#extras"):"")."\n";}}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size=0;function
__construct(){$this->handler=tmpfile();}function
write($ub){$this->size+=strlen($ub);fwrite($this->handler,$ub);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$m=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$m)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$m[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$m=fields($a);if(!$m)$k=error()?:lang(12);$S=table_status1($a);$D=adminer()->tableName($S);page_header(($m&&is_view($S)?$S['Engine']=='materialized view'?lang(136):lang(137):lang(138)).": ".($D!=""?$D:h($a)),$k);$Qh=array();foreach($m
as$x=>$l)$Qh+=$l["privileges"];adminer()->selectLinks($S,(isset($Qh["insert"])||!support("table")?"":null));$lb=$S["Comment"];if($lb!="")echo"<p class='nowrap'>".lang(44).": ".adminer()->commentValue('TABLE',$lb)."\n";if($m)adminer()->tableStructurePrint($m,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$_=preg_replace('~ns=[^&]*~',"ns=".url_escape($K["ns"]),ME);echo"<li><a href='".h($_."table=".url_escape($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$pe=driver()->inheritsFrom($a);if($pe){echo"<h3>".lang(139)."</h3>\n";tables_links($pe);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<div>\n","<h3 id='indexes'>".lang(140)."</h3>\n";$w=indexes($a);if($w)adminer()->tableIndexesPrint($w,$S);if(driver()->supportsAlterIndex($S))echo'<p class="links hover"><a href="'.h(ME).'indexes='.url_escape($a).'">'.lang(141)."</a>\n";echo"</div>\n";}if(!is_view($S)){if(fk_support($S)){echo"<div>\n","<h3 id='foreign-keys'>".lang(107)."</h3>\n";$rd=foreign_keys($a);if($rd){echo"<table>\n","<thead><tr><th>".lang(142)."<td>".lang(143)."<td>".lang(110)."<td>".lang(109)."<td class='hover'><tbody>\n";foreach($rd
as$D=>$o){echo"<tr title='".h($D)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$o["source"]))."</i>";$_=($o["db"]!=""?preg_replace('~db=[^&]*~',"db=".url_escape($o["db"]),ME):($o["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".url_escape($o["ns"]),ME):ME));echo"<td><a href='".h($_."table=".url_escape($o["table"]))."'>".($o["db"]!=""&&$o["db"]!=DB?"<b>".h($o["db"])."</b>.":"").($o["ns"]!=""&&$o["ns"]!=$_GET["ns"]?"<b>".h($o["ns"])."</b>.":"").h($o["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$o["target"]))."</i>)","<td>".h($o["on_delete"]),"<td>".h($o["on_update"]),'<td class="hover"><a href="'.h(ME.'foreign='.url_escape($a).'&name='.url_escape($D)).'">'.lang(144).'</a>',"\n";}echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'foreign='.url_escape($a).'">'.lang(145)."</a>\n","</div>\n";}if(support("check")){echo"<div>\n","<h3 id='checks'>".lang(146)."</h3>\n";$Xa=driver()->checkConstraints($a);if($Xa){echo"<table>\n";foreach($Xa
as$x=>$X)echo"<tr title='".h($x)."'>","<td><code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim($X)),80,"</code>"),"<td class='hover'><a href='".h(ME.'check='.url_escape($a).'&name='.url_escape($x))."'>".lang(144)."</a>","\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'check='.url_escape($a).'">'.lang(147)."</a>\n","</div>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<div>\n","<h3 id='triggers'>".lang(148)."</h3>\n";$vj=triggers($a);if($vj){echo"<table>\n";foreach($vj
as$x=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($x)."<td class='hover'><a href='".h(ME.'trigger='.url_escape($a).'&name='.url_escape($x))."'>".lang(144)."</a>\n";echo"</table>\n";}echo'<p class="links hover"><a href="'.h(ME).'trigger='.url_escape($a).'">'.lang(149)."</a>\n","</div>\n";}$oe=driver()->inheritedTables($a);if($oe){echo"<h3 id='partitions'>".lang(150)."</h3>\n";$Pg=driver()->partitionsInfo($a);if($Pg)echo"<p><code class='jush-".JUSH."'>BY ".h("$Pg[partition_by]($Pg[partition])")."</code>\n";tables_links($oe);}}elseif(isset($_GET["schema"])){page_header(lang(64),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Pi=array();$Qi=array();$bd=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$lf,PREG_SET_ORDER);foreach($lf
as$s=>$B){$Pi[$B[1]]=array((float)$B[2],(float)$B[3]);$Qi[]="\n\t'".js_escape($B[1])."': [ $B[2], $B[3] ]";}$mj=0;$Ia=-1;$Yh=array();$Fh=array();$We=array();$pa=driver()->allFields();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$G=0;$Yh[$R]["fields"]=array();foreach($pa[$R]as$l){$G+=1.25;$bd[$R][$l["field"]]=$G;$Yh[$R]["fields"][$l["field"]]=$l;}$Yh[$R]["pos"]=($Pi[$R]?:array($mj,0));foreach(adminer()->foreignKeys($R)as$X){if(!$X["db"]){$Ue=$Ia;if(idx($Pi[$R],1)||idx($Pi[$X["table"]],1))$Ue=min(idx($Pi[$R],1,0),idx($Pi[$X["table"]],1,0))-1;else$Ia-=.1;while($We[(string)$Ue])$Ue-=.0001;$Yh[$R]["references"][$X["table"]][(string)$Ue]=array($X["source"],$X["target"]);$Fh[$X["table"]][$R][(string)$Ue]=$X["target"];$We[(string)$Ue]=true;}}$mj=max($mj,$Yh[$R]["pos"][0]+2.5+$G);}echo'<div id="schema" style="height: ',$mj,'em;">
<script',nonce(),'>
const tablePos = {',implode(",",$Qi)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$mj,';
document.onmousemove = schemaMousemove;
document.onmouseup = event => schemaMouseup(event, \'',js_escape(DB),'\');
</script>
';foreach($Yh
as$D=>$R){echo"<div class='table'".on('mousedown','schemaMousedown')." style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.url_escape($D).'"><b>'.h($D)."</b></a>";foreach($R["fields"]as$l){$X='<span'.type_class($l["type"]).' title="'.h($l["type"].($l["length"]?"($l[length])":"").($l["null"]?" NULL":'')).'">'.h($l["field"]).'</span>';echo"<br>".($l["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$Xi=>$Hh){foreach($Hh
as$Ue=>$Ch){$Ve=$Ue-idx($Pi[$D],1);$s=0;foreach($Ch[0]as$ui)echo"\n<div class='references' title='".h($Xi)."' id='refs$Ue-".($s++)."' style='left: $Ve"."em; top: ".$bd[$D][$ui]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$Ve)."em;'></div></div>";}}foreach((array)$Fh[$D]as$Xi=>$Hh){foreach($Hh
as$Ue=>$d){$Ve=$Ue-idx($Pi[$D],1);$s=0;foreach($d
as$Wi)echo"\n<div class='references arrow' title='".h($Xi)."' id='refd$Ue-".($s++)."' style='left: $Ve"."em; top: ".$bd[$D][$Wi]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$Ve)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($Yh
as$D=>$R){foreach((array)$R["references"]as$Xi=>$Hh){if($Yh[$Xi]){foreach($Hh
as$Ue=>$Ch){$Jf=$mj;$tf=-10;foreach($Ch[0]as$x=>$ui){$hh=$R["pos"][0]+$bd[$D][$ui];$ih=$Yh[$Xi]["pos"][0]+$bd[$Xi][$Ch[1][$x]];$Jf=min($Jf,$hh,$ih);$tf=max($tf,$hh,$ih);}echo"<div class='references' id='refl$Ue' style='left: $Ue"."em; top: $Jf"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($tf-$Jf)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".url_escape($da)),'" id="schema-link">',lang(151),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$k){$j=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Ki){if(support($Ki))$j[$Ki."s"]='';}save_settings(array_intersect_key($_POST+$j,array_flip(array("output","format","db_style","table_style","data_style"))+$j),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Sc=dump_headers((count($T)==1?key($T):DB),(DB==""||$_GET["ns"]===""||count($T)>1));$Ce=preg_match('~sql~',$_POST["format"]);if($Ce){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Fi=$_POST["db_style"];$h=array(DB);if(DB==""){$h=$_POST["databases"];if(is_string($h))$h=explode("\n",rtrim(str_replace("\r","",$h),"\n"));}foreach((array)$h
as$i){adminer()->dumpDatabase($i);if(connection()->select_db($i)){if($Ce&&$Fi)echo
use_sql($i,$Fi).";\n\n";foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$Yh){if($Yh!=""){if(DB==""&&information_schema(DB,$Yh))continue;set_schema($Yh);}$Ci=($_POST["table_style"]||$_POST["data_style"]?table_status('',true):array());$Rc=array();$Lb=array();foreach($Ci
as$D=>$S){if(DB==""||$_GET["ns"]===""||in_array($D,(array)$_POST["tables"]))$Rc[$D]=$S;if(DB==""||$_GET["ns"]===""||in_array($D,(array)$_POST["data"]))$Lb[$D]=$S;}if($Ce){if($_POST["table_style"]=="DROP+CREATE"&&function_exists('Adminer\drop_sql'))echo
drop_sql($Rc);if($_POST["data_style"]=="TRUNCATE+INSERT"&&function_exists('Adminer\truncate_all_sql')){$wj=array();foreach($Lb
as$D=>$S){if(!is_view($S)&&!($_POST["table_style"]=="DROP+CREATE"&&isset($Rc[$D])))$wj[]=$D;}echo
truncate_all_sql($wj);}$Hg="";if($_POST["types"]){foreach(types()as$t=>$U){$Ub=type_definition($t);$cg=($Ub["kind"]=='d'?"DOMAIN":"TYPE");if($Ub["definition"])$Hg
.=($Fi!='DROP+CREATE'?"DROP $cg IF EXISTS ".idf_escape($U).";;\n":"")."CREATE $cg ".idf_escape($U)." $Ub[definition];\n\n";else$Hg
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$D=$K["ROUTINE_NAME"];$Sh=$K["ROUTINE_TYPE"];$g=create_routine($Sh,array("name"=>$D)+routine($K["SPECIFIC_NAME"],$Sh));set_utf8mb4($g);$Hg
.=($Fi!='DROP+CREATE'?"DROP $Sh IF EXISTS ".idf_escape($D).";;\n":"")."$g;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$g=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($g);$Hg
.=($Fi!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$g;;\n\n";}}echo($Hg&&JUSH=='sql'?"DELIMITER ;;\n\n$Hg"."DELIMITER ;\n\n":$Hg);}if($_POST["table_style"]||$_POST["data_style"]){$bk=array();foreach($Ci
as$D=>$S){$R=array_key_exists($D,$Rc);$Jb=array_key_exists($D,$Lb);if($R||$Jb){$jj=null;if($Sc=="tar"){$jj=new
TmpFile;ob_start(array($jj,'write'),1e5);}adminer()->dumpTable($D,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$bk[]=$D;elseif($Jb){$m=fields($D);$M=array("*");$xb=convert_fields($m,$m);if($xb)$M[]=substr($xb,2);adminer()->dumpData($D,$_POST["data_style"],"",$M);}if($Ce&&$_POST["triggers"]&&$R&&($vj=trigger_sql($D)))echo"\nDELIMITER ;;\n$vj\nDELIMITER ;\n";if($Sc=="tar"){ob_end_flush();tar_file((DB!=""?"":"$i/")."$D.csv",$jj);}elseif($Ce)echo"\n";}}if($Ce&&$_POST["table_style"]&&function_exists('Adminer\foreign_keys_sql')){foreach($Rc
as$D=>$S){if(!is_view($S))echo
foreign_keys_sql($D);}}if($Ce){foreach($bk
as$ak)adminer()->dumpTable($ak,$_POST["table_style"],1);}if($Sc=="tar")echo
pack("x1024");}}}}adminer()->dumpFooter();exit;}page_header(lang(70),$k,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Ob=array('','USE','DROP+CREATE','CREATE');$Ri=array('','DROP+CREATE','CREATE');$Kb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Kb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(152)."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".lang(153)."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(31)."<td>".html_select('db_style',$Ob,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],lang(7)):"").(support("routine")?checkbox("routines",1,$K["routines"],lang(66)):"").(support("event")?checkbox("events",1,$K["events"],lang(68)):"")),"<tr><th>".lang(129)."<td>".html_select('table_style',$Ri,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],lang(45)).(support("trigger")?checkbox("triggers",1,$K["triggers"],lang(148)):""),"<tr><th>".lang(154)."<td>".html_select('data_style',$Kb,$K["data_style"]),'</table>
<p><input type=\'submit\' value=\'',lang(70),'\'>
',input_token(),'
<table',on('click','dumpClick'),'>
';$oh=array();if($_GET["ns"]===""){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly' title='".lang(155)."'".on('click','formCheck','^schemas\[').">".lang(156)."</label>","<tbody>\n";foreach(adminer()->schemas()as$Yh){if(!information_schema(DB,$Yh))echo"<tr><td>".checkbox("schemas[]",$Yh,true,$Yh,"","block")."\n";}}elseif(DB!=""){$Ya=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Ya class='jsonly' title='".lang(155)."'".on('click','formCheck','^tables\[').">".lang(138)."</label>","<th style='text-align: right;'><label class='block'>".lang(154)."<input type='checkbox' id='check-data'$Ya class='jsonly' title='".lang(155)."'".on('click','formCheck','^data\[')."></label>","<tbody>\n";$bk="";$Ti=tables_list();foreach($Ti
as$D=>$U){$nh=preg_replace('~_.*~','',$D);$Ya=($a==""||$a==(substr($a,-1)=="%"?"$nh%":$D));$qh="<tr><td>".checkbox("tables[]",$D,$Ya,$D,"","block");if($U!==null&&!preg_match('~table~i',$U))$bk
.="$qh\n";else
echo"$qh<td align='right'><label class='block'><span id='Rows-".h($D)."'></span>".checkbox("data[]",$D,$Ya)."</label>\n";$oh[$nh]++;}echo$bk;if($Ti)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$h=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($h?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly' title='".lang(155)."'".on('click','formCheck','^databases\[').">":"").lang(31)."</label>","<tbody>\n";if($h){foreach($h
as$i){if(!information_schema($i)){$nh=preg_replace('~_.*~','',$i);echo"<tr><td>".checkbox("databases[]",$i,$a==""||$a=="$nh%",$i,"","block")."\n";$oh[$nh]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$kd=true;foreach($oh
as$x=>$X){if($x!=""&&$X>1){echo($kd?"<p>":" ")."<a href='".h(ME)."dump=".url_escape("$x%")."'>".h($x)."</a>";$kd=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(65));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(157)."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$Bd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($Bd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(29)."<th>".lang(27)."<td class='hover'><tbody>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"]),"<td>".h($K["Host"]),'<td class="hover"><a href="'.h(ME.'user='.url_escape($K["User"]).'&host='.url_escape($K["Host"])).'">'.lang(13)."</a>\n";if(!$Bd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".lang(13)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$k&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$Ud=&get_session("queries");$Td=&$Ud[DB];if(!$k&&$_POST["clear"]){$Td=array();redirect(remove_from_uri("history"));}stop_session();$ka=get_settings("adminer_import");if($_POST&&$ka)save_settings($ka,"adminer_import");page_header((isset($_GET["import"])?lang(69):lang(58)),$k);$cf=driver()->lineComment();if(!$k&&$_POST&&!(isset($_GET["import"])&&adminer()->importProcess())){$Wb=driver()->delimiter;$p=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$xi=adminer()->importServerPath();$p=@fopen((file_exists($xi)?$xi:"compress.zlib://$xi.gz"),"rb");$H=($p?fread($p,1e6):false);}else$H=get_file("sql_file",true,$Wb);if(is_string($H)){if(($_f=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($_f,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$wh=$H.(preg_match("~$Wb\\s*\$~",$H)?"":$Wb);if(!$Td||first(end($Td))!=$wh){restart_session();$Td[]=array($wh,time());set_session("queries",$Ud);stop_session();}}$vi="(?:\\s|/\\*[\s\S]*?\\*/|(?:$cf)[^\n]*\n?|--\r?\n)";$gg=0;$_c=true;$zb=false;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$kb=0;$Gc=array();$Ng='[\'"'.(JUSH=="sql"?'`':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$cf.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$nj=microtime(true);while($H!=""){if(!$gg&&preg_match("~^$vi*+DELIMITER\\s+(\\S+)~i",$H,$B)){$Wb=preg_quote($B[1]);$H=substr($H,strlen($B[0]));}elseif(!$gg&&JUSH=='pgsql'&&preg_match("~^($vi*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$B)){$Wb="\n\\\\\\.\r?\n";$zb=true;$gg=strlen($B[0]);}else{preg_match("($Wb\\s*|$Ng)",$H,$B,PREG_OFFSET_CAPTURE,$gg);list($td,$G)=$B[0];if(!$td&&$p&&!feof($p))$H
.=fread($p,1e5);else{if(!$td&&rtrim($H)=="")break;$gg=$G+strlen($td);if($td&&!preg_match("(^$Wb)",$td)){$Ra=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$Zg=($td=='/*'?'\*/':($td=='['?']':(preg_match("~^(?:$cf)~",$td)?"\n":preg_quote($td).($Ra?'|\\\\.':''))));while(preg_match("($Zg|\$)s",$H,$B,PREG_OFFSET_CAPTURE,$gg)){$Wh=$B[0][0];if(!$Wh&&$p&&!feof($p))$H
.=fread($p,1e5);else{$gg=$B[0][1]+strlen($Wh);if(!$Wh||$Wh[0]!="\\")break;}}}else{$_c=false;$wh=substr($H,0,$G+($zb?3:0));$kb++;$qh="<pre id='sql-$kb'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($wh)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$vi*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$wh,$B)!==0){echo$qh,"<p class='error'>".lang(158,preg_match('~ATTACH~i',$B[1])?'ATTACH':'VACUUM INTO')."\n";$Gc[]=" <a href='#sql-$kb'>$kb</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$qh;ob_flush();flush();}$Bi=microtime(true);if(connection()->multi_query($wh)&&$f&&preg_match("~^$vi*+USE\\b~i",$wh))$f->query($wh);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$qh:""),"<p class='error'>".lang(159).(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$Gc[]=" <a href='#sql-$kb'>$kb</a>";if($_POST["error_stops"])break
2;}else{$_=ME."sql=".url_escape(trim($wh));$cj=" <span class='time'>(".format_time($Bi).")</span>".(strlen($_)<1900?" <a href='".h($_)."'>".lang(13)."</a>":"");$ma=connection()->affected_rows;$ek=($_POST["only_errors"]?"":driver()->warnings());$fk="warnings-$kb";if($ek)$cj
.=", <a href='#$fk' class='toggle'>".lang(40)."</a>";$Pc=null;$_g=null;$Qc="explain-$kb";if(is_object($I)){$z=$_POST["limit"];$ag=$z;$_g=print_select_result($I,$f,array(),$ag);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$ag=max($I->num_rows,$ag);echo"<p class='sql-footer'>".($ag?($z&&$ag>$z?lang(160,$z):"").lang(161,$ag):""),$cj;if($f&&preg_match("~^($vi|\\()*+SELECT\\b~i",$wh)&&($Pc=explain($f,$wh)))echo", <a href='#$Qc' class='toggle'>Explain</a>";$t="export-$kb";echo", <a href='#$t' class='toggle'>".lang(70)."</a><span id='$t' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ka["output"])." ".html_select("format",adminer()->dumpFormat(),$ka["format"]).input_hidden("query",$wh)."<input type='submit' name='export' value='".lang(70)."'".($z?"":on('click','sqlExport')).">".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$vi*+(CREATE|DROP|ALTER)$vi++(DATABASE|SCHEMA)\\b~i",$wh)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(162,$ma)."$cj\n";}echo($ek?"<div id='$fk' class='hidden'>\n$ek</div>\n":"");if($Pc){echo"<div id='$Qc' class='hidden explain'>\n";print_select_result($Pc,$f,$_g);echo"</div>\n";}}$Bi=microtime(true);}while(connection()->next_result());}$H=substr($H,$gg);$gg=0;if($zb){$Wb=driver()->delimiter;$zb=false;}}}}}if($_c)echo"<p class='message'>".lang(163)."\n";else{$he=connection()->inTransaction();driver()->rollback();if($he)echo"<pre><code class='jush-".JUSH."'>ROLLBACK -- Adminer</code></pre>\n";if($_POST["only_errors"])echo"<p class='message'>".lang(164,$kb-count($Gc))," <span class='time'>(".format_time($nj).")</span>\n";elseif($Gc&&$kb>1)echo"<p class='error'>".lang(159).": ".implode("",$Gc)."\n";}}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form"',(isset($_GET["import"])?"":on('submit','sqlSubmit',remove_from_uri("sql|limit|error_stops|only_errors|history"))),'>
';$Nc="<input type='submit' value='".lang(165)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$wh=$_GET["sql"];if($_POST)$wh=$_POST["query"];elseif($_GET["history"]=="all")$wh=$Td;elseif($_GET["history"]!="")$wh=idx($Td[$_GET["history"]],0);echo"<p>";textarea("query",$wh,20);echo($_POST?"":script("qs('textarea').focus();")),"<p>";adminer()->sqlPrintAfter();echo"$Nc\n",lang(166).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$Gd=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(167)."</legend><div>","SQL$Gd: ".file_input(" name='sql_file[]' multiple","\n$Nc"),"</div></fieldset>\n";$ee=adminer()->importServerPath();if($ee)echo"<fieldset><legend>".lang(168)."</legend><div>",lang(169,"<code>".h($ee)."$Gd</code>")," <input type='submit' name='webfile' value='".lang(170)."'>","</div></fieldset>\n";adminer()->importPrint();echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(171))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(172))."\n",input_token();if(!isset($_GET["import"])&&$Td){print_fieldset("history",lang(173),$_GET["history"]!="");for($X=end($Td);$X;$X=prev($Td)){$x=key($Td);list($wh,$cj,$wc)=$X;echo'<div><a href="'.h(ME."sql=&history=$x").'" class="hover">'.lang(13)."</a>"." <span class='time' title='".@date('Y-m-d',$cj)."'>".@date("H:i:s",$cj)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(preg_replace('~\s+~',' ',ltrim(preg_replace("~^(?:$cf).*~m",'',$wh))),80,"</code>").($wc?" <span class='time'>($wc)</span>":"")."</div>\n";}echo"<input type='submit' name='clear' value='".lang(174)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(175)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$m=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$m):""):where($_GET,$m));$Ij=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($m
as$D=>$l){if((!$Ij&&!isset($l["privileges"]["insert"]))||adminer()->fieldName($l)=="")unset($m[$D]);}if($_POST&&!$k&&!isset($_GET["select"])){$A=$_POST["referer"];if($_POST["insert"])$A=($Ij?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$A))$A=ME."select=".url_escape($a);$w=indexes($a);$Cj=unique_array($_GET["where"],$w);$zh="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($A,lang(176),driver()->delete($a,$zh,$Cj?0:1));else{$O=array();foreach($m
as$D=>$l){$X=process_input($l);if($X!==false&&$X!==null)$O[idf_escape($D)]=$X;}if($Ij){if(!$O)redirect($A);queries_redirect($A,lang(177),driver()->update($a,$O,$zh,$Cj?0:1));if(is_ajax()){page_headers();page_messages($k);exit;}}else{$I=driver()->insert($a,$O);$Te=($I?last_id($I):0);queries_redirect($A,lang(178,($Te?" $Te":"")),$I);}}}$K=null;if($Z){$M=array();foreach($m
as$D=>$l){if(isset($l["privileges"]["select"])){$xa=($_POST["clone"]&&$l["auto_increment"]?"''":convert_field($l));$M[]=($xa?"$xa AS ":"").idf_escape($D);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$k=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!$m&&driver()->primary!=""){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$x=>$X){if(!$Z)$K[$x]=null;$m[$x]=array("field"=>$x,"null"=>($x!=driver()->primary),"auto_increment"=>($x==driver()->primary));}}}if($_POST["save"]){$jh=array();foreach((array)$_POST["fields"]as$x=>$X)$jh[bracket_escape($x,true)]=$X;$K=$jh+($K?$K:array());}edit_form($a,$m,$K,$Ij,$k);}elseif(isset($_GET["create"])){$a=$_GET["create"];$Rg=driver()->partitionBy;$Vg=($Rg&&$a!=""?driver()->partitionsInfo($a):array());$Eh=referencable_primary($a);$rd=array();foreach($Eh
as$Oi=>$l)$rd[str_replace("`","``",$Oi)."`".str_replace("`","``",$l["field"])]=$Oi;$Cg=array();$S=array();if($a!=""){$Cg=fields($a);$S=table_status1($a);if(count($S)<2)$k=lang(12);}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$k)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$k){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(179),drop_tables(array($a)));else{$m=array();$pa=array();$Nj=false;$pd=array();$Bg=reset($Cg);$oa=" FIRST";foreach($K["fields"]as$x=>$l){$o=$rd[$l["type"]];$yj=($o!==null?$Eh[$o]:$l);if($l["field"]!=""){if(!$l["generated"])$l["default"]=null;$vh=process_field($l,$yj);$pa[]=array($l["orig"],$vh,$oa);if(!$Bg||$vh!==process_field($Bg,$Bg)){$m[]=array($l["orig"],$vh,$oa);if($l["orig"]!=""||$oa)$Nj=true;}if($o!==null)$pd[idf_escape($l["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$rd[$l["type"]],'source'=>array($l["field"]),'target'=>array($yj["field"]),'on_delete'=>$l["on_delete"],));$oa=" AFTER ".idf_escape($l["field"]);}elseif($l["orig"]!=""){$Nj=true;$m[]=array($l["orig"]);}if($l["orig"]!=""){$Bg=next($Cg);if(!$Bg)$oa="";}}$Tg=array();if(in_array($K["partition_by"],$Rg)){foreach($K
as$x=>$X){if(preg_match('~^partition~',$x))$Tg[$x]=$X;}foreach($Tg["partition_names"]as$x=>$D){if($D==""){unset($Tg["partition_names"][$x]);unset($Tg["partition_values"][$x]);}}$Tg["partition_names"]=array_values($Tg["partition_names"]);$Tg["partition_values"]=array_values($Tg["partition_values"]);if($Tg==$Vg)$Tg=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$Tg=null;$C=lang(180);if($a==""){cookie("adminer_engine",$K["Engine"]);$C=lang(181);}$D=trim($K["name"]);$A=ME.(support("table")?"table=":"select=").url_escape($D);$I=alter_table($a,$D,(JUSH=="sqlite"&&($Nj||$pd)?$pa:$m),$pd,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$Tg);if($I&&!Queries::$queries)redirect($A);queries_redirect($A,$C,$I);}}page_header(($a!=""?lang(38):lang(71)),$k,array("table"=>$a),h($a));if(!$_POST){$_j=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($_j["int"])?"int":(isset($_j["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Cg
as$l){if($l["generated"])$l["default"]=ltrim($l["default"]);$l["generated"]=$l["generated"]?:(isset($l["default"])?"DEFAULT":"");$K["fields"][]=$l;}if($Rg){$K+=$Vg;$K["partition_names"][]="";$K["partition_values"][]="";}}}$ib=collations();if(is_array(reset($ib)))$ib=call_user_func_array('array_merge',array_values($ib));$Bc=driver()->engines();foreach($Bc
as$Ac){if(!strcasecmp($Ac,$K["Engine"])){$K["Engine"]=$Ac;break;}}$of=max_input_vars(12,20);if($of){$Sd=(count($K["fields"])>$of?"":" hidden");echo"<p".($Sd?" id='max-fields' data-columns='$of'":"")." class='error$Sd'>".max_input_vars_error()."\n";}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(182).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($Bc?html_select("Engine",array(""=>"(".lang(183).")")+$Bc,$K["Engine"],on('change','helpClose').on_help_value())."\n":"");if($ib)echo"<datalist id='collations'>".optionlist($ib)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".lang(108).")'>\n");echo"<input type='submit' value='".lang(17)."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$ib,"TABLE",$rd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(45).": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(184),on('click','columnShowClick',5),"jsonly");$nb=($_POST?$_POST["comments"]:get_setting("comments"));if(support("comment")){echo
checkbox("comments",1,$nb,lang(44),on('click','editingCommentsClick',true),"jsonly").' ';$b=" name='Comment' data-maxlength='".(min_version(5.5)?2048:60)."'".($nb?"":" class='hidden'");echo
adminer()->commentInput('TABLE',$b,$K["Comment"]);}echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';}echo'
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$a)),'>
';if($Rg&&(JUSH=='sql'||$a=="")){$Sg=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",lang(186),$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$Rg),$K["partition_by"],on('change','partitionByChange').on_help_value('.','PARTITION BY $&'))."\n","(<input name='partition' value='".h($K["partition"])."'>)\n",lang(187).": <input type='number' name='partitions' class='size".($Sg||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($Sg?"":" class='hidden'").">\n","<thead><tr><th>".lang(188)."<th>".lang(189)."<tbody>\n";foreach($K["partition_names"]as$x=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off"'.($x==count($K["partition_names"])-1?on('input','partitionNameChange'):'').'>','<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$x)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$me=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$ke=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$me[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$me[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$me[]="VECTOR";$w=indexes($a);$m=fields($a);$ph=array();if(JUSH=="mongo"){$ph=$w["_id_"];unset($me[0]);unset($w["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$k&&!$_POST["add"]&&!$_POST["drop_col"]){$ra=array();foreach($K["indexes"]as$v){$D=$v["name"];if(in_array($v["type"],$me)){$d=array();$af=array();$Zb=array();$qg=array();$le=(support("partial_indexes")?$v["partial"]:"");$je=(in_array($v["algorithm"],$ke)?$v["algorithm"]:"");$O=array();ksort($v["columns"]);foreach($v["columns"]as$x=>$c){if($c!=""){$y=idx($v["lengths"],$x);$Xb=idx($v["descs"],$x);$pg=idx($v["opclasses"],$x);$O[]=($m[$c]?idf_escape($c):$c).($y?"(".(+$y).")":"").($pg!=""?" ".idf_escape($pg):"").($Xb?" DESC":"");$d[]=$c;$af[]=($y?:null);$Zb[]=$Xb;$qg[]="$pg";}}$Oc=$w[$D];if($Oc){ksort($Oc["columns"]);ksort($Oc["lengths"]);ksort($Oc["descs"]);if($v["type"]==$Oc["type"]&&array_values($Oc["columns"])===$d&&(!$Oc["lengths"]||array_values($Oc["lengths"])===$af)&&array_values($Oc["descs"])===$Zb&&(!$Oc["opclasses"]||array_values($Oc["opclasses"])===$qg)&&$Oc["partial"]==$le&&(!$ke||$Oc["algorithm"]==$je)){unset($w[$D]);continue;}}if($d)$ra[]=array($v["type"],$D,$O,$je,$le);}}foreach($w
as$D=>$Oc)$ra[]=array($Oc["type"],$D,"DROP");if(!$ra)redirect(ME."table=".url_escape($a));queries_redirect(ME."table=".url_escape($a),lang(190),alter_indexes($a,$ra));}page_header(lang(140),$k,array("table"=>$a),h($a));$dd=array_keys($m);if($_POST["add"]){foreach($K["indexes"]as$x=>$v){if($v["columns"][count($v["columns"])]!="")$K["indexes"][$x]["columns"][]="";}$v=end($K["indexes"]);if($v["type"]||array_filter($v["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($w
as$x=>$v){$w[$x]["name"]=$x;$w[$x]["columns"][]="";}$w[]=array("columns"=>array(1=>""));$K["indexes"]=$w;}$af=(JUSH=="sql"||JUSH=="mssql");$qg=driver()->indexOpclasses();$ni=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap odds">
<thead><tr>
<th id="label-type">',lang(191);$ce=" class='idxopts".($ni?"":" hidden")."'";if($ke)echo"<th id='label-algorithm'$ce>".lang(192).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" hidden>',lang(193).($af?"<span$ce> (".lang(194).")</span>":"");if($af||support("descidx"))echo
checkbox("options",1,$ni,lang(114),on('click','indexOptionsShow'),"jsonly")."\n";echo'<th id="label-name">',lang(195);if(support("partial_indexes"))echo"<th id='label-condition'$ce>".lang(196);echo'<th><noscript>',icon("plus","add[0]","+",lang(115)),'</noscript>
<tbody>
';if($ph){echo"<tr><td>PRIMARY<td>";foreach($ph["columns"]as$x=>$c)echo
select_input(" disabled",array_combine($dd,$dd),$c),"<label><input disabled type='checkbox'>".lang(53)."</label> ";echo"<td><td>\n";}$Fe=1;foreach($K["indexes"]as$v){if(!$_POST["drop_col"]||$Fe!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$Fe][type]",array(-1=>"")+$me,$v["type"],($Fe==count($K["indexes"])?on('change','indexesAddRow'):""),"label-type");if($ke)echo"<td$ce>".html_select("indexes[$Fe][algorithm]",array_merge(array(""),$ke),$v['algorithm'],"","label-algorithm");echo"<td>";ksort($v["columns"]);$s=1;foreach($v["columns"]as$x=>$c){echo"<span>".select_input(" name='indexes[$Fe][columns][$s]' title='".lang(42)."'".on('change','indexesChangeColumn',(JUSH=="sql"?"":$_GET["indexes"]."_")),($m&&($c==""||$m[$c])?array_combine($dd,$dd):array()),$c)," <span$ce>",($af?"<input type='number' name='indexes[$Fe][lengths][$s]' class='size' value='".h(idx($v["lengths"],$x))."' title='".lang(113)."'>":"");if($qg){$pg=idx($v["opclasses"],$x);echo
html_select("indexes[$Fe][opclasses][$s]",array(""=>"(".lang(197).")")+array_combine($qg,$qg)+($pg!=""?array($pg=>$pg):array()),$pg),'';}echo(support("descidx")?checkbox("indexes[$Fe][descs][$s]",1,idx($v["descs"],$x),lang(53)):""),"<br>","</span></span>";$s++;}echo"<td><input name='indexes[$Fe][name]' value='".h($v["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$ce><input name='indexes[$Fe][partial]' value='".h($v["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$Fe]","x",lang(117),on('click','editingRemoveRow','indexes$1[type]'));}$Fe++;}echo'</table>
</div>
<p>
<input type=\'submit\' value=\'',lang(17),'\'>
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$k&&!$_POST["add"]){$D=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(198),drop_databases(array(DB)));}elseif(DB!==$D){if(DB!=""){$_GET["db"]=$D;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".url_escape($D),lang(199),rename_database($D,(string)$K["collation"]));}else{$h=explode("\n",str_replace("\r","",$D));$Gi=true;$Re="";foreach($h
as$i){if(count($h)==1||$i!=""){if(!create_database($i,(string)$K["collation"]))$Gi=false;$Re=$i;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".url_escape($Re),lang(200),$Gi);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($D).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),lang(201));}}page_header(DB!=""?lang(61):lang(121),$k,array(),h(DB));$ib=collations();$D=DB;if($_POST)$D=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$ib);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$Bd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$Bd,$B)&&$B[1]){$D=stripcslashes(idf_unescape("`$B[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($D,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($D).'</textarea><br>':'<input name="name" autofocus value="'.h($D).'" data-maxlength="64" autocapitalize="off">')."\n",($ib?html_select("collation",array(""=>"(".lang(108).")")+$ib,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):"")."\n",'<input type=\'submit\' value=\'',lang(17),'\'>
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(133)."'".confirm(lang(185,DB)).">\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(115))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);page_header(lang(202).": ".h($ca),$k);$Uh=(isset($_GET["callf"])?"FUNCTION":"PROCEDURE");$Sh=routine($_GET["call"],$Uh);$fe=array();$Hg=array();foreach($Sh["fields"]as$s=>$l){if(substr($l["inout"],-3)=="OUT"&&JUSH=='sql')$Hg[$s]="@".idf_escape($l["field"])." AS ".idf_escape($l["field"]);if(!$l["inout"]||substr($l["inout"],0,2)=="IN")$fe[]=$s;}if(!$k&&$_POST){$Sa=array();foreach($Sh["fields"]as$x=>$l){$X="";if(in_array($x,$fe)){$X=process_input($l);if($X===false)$X="''";if(isset($Hg[$x]))connection()->query("SET @".idf_escape($l["field"])." = $X");}if(isset($Hg[$x]))$Sa[]="@".idf_escape($l["field"]);elseif(in_array($x,$fe))$Sa[]=$X;}$H=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($Sh["returns"],"type")=="record"?"* FROM ":"").table($ca)."(".implode(", ",$Sa).")";$Bi=microtime(true);$I=connection()->multi_query($H);$ma=connection()->affected_rows;echo
adminer()->selectQuery($H,$Bi,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang(203,$ma)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($Hg)print_select_result(connection()->query("SELECT ".implode(", ",$Hg)));}}echo'
<form action="" method="post">
';if($fe){echo"<table class='layout'>\n";foreach($fe
as$x){$l=$Sh["fields"][$x];$D=$l["field"];echo"<tr><th>".adminer()->fieldName($l);$Y=idx($_POST["fields"],$D);if($Y!=""){if($l["type"]=="set")$Y=implode(",",$Y);}input($l,$Y,idx($_POST["function"],$D,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type=\'submit\' value=\'',lang(202),'\'>
',input_token(),'</form>

',adminer()->commentValue($Uh,$Sh['comment']);}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$D=$_GET["name"];$K=$_POST;if($_POST&&!$k&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$Wi=array();foreach($K["source"]as$x=>$X)$Wi[$x]=$K["target"][$x];$K["target"]=$Wi;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $D"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$ra="ALTER TABLE ".table($a);$I=($D==""||queries("$ra DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($D)));if(!$K["drop"])$I=queries("$ra ADD".format_foreign_key($K));}queries_redirect(ME."table=".url_escape($a),($K["drop"]?lang(204):($D!=""?lang(205):lang(206))),$I);if(!$K["drop"])$k=lang(207);}page_header(($D!=""?lang(208):lang(145)),$k,array("table"=>$a),h($D!=""?$D:$a));if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($D!=""){$rd=foreign_keys($a);$K=$rd[$D];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$ui=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$Dg=get_schema();set_schema($K["ns"]);}$Dh=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$Wi=array_keys(fields(in_array($K["table"],$Dh)?$K["table"]:reset($Dh)));$b=on('change','foreignChange');echo"<p><label>".lang(209).": ".html_select("table",$Dh,$K["table"],$b)."</label>\n";if(JUSH!="sqlite"){$Pb=array();foreach(adminer()->databases()as$i){if(!information_schema($i))$Pb[]=$i;}echo"<label>".lang(72).": ".html_select("db",$Pb,$K["db"]!=""?$K["db"]:$_GET["db"],$b)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type=\'submit\' name=\'change\' value=\'',lang(210),'\'></noscript>
<table>
<thead><tr><th id="label-source">',lang(142),'<th id="label-target">',lang(143),'<tbody>
';$Fe=0;foreach($K["source"]as$x=>$X){echo"<tr>","<td>".html_select("source[".(+$x)."]",array(-1=>"")+$ui,$X,($Fe==count($K["source"])-1?on('change','foreignAddRow'):""),"label-source"),"<td>".html_select("target[".(+$x)."]",$Wi,idx($K["target"],$x),"","label-target");$Fe++;}echo'</table>
<p>
<label>',lang(110),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>',lang(109),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(support("deferrable")?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]).' ':''),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)),'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
<noscript><p><input type=\'submit\' name=\'add\' value=\'',lang(211),'\'></noscript>
';if($D!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$D)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Eg="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$Eg=strtoupper($P["Engine"]);}if($_POST&&!$k){$D=trim($K["name"]);$xa=" AS\n$K[select]";$A=ME."table=".url_escape($D);$C=lang(212);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$D&&JUSH!="sqlite"&&$U=="VIEW"&&$Eg=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($D).$xa,$A,$C);else{$Yi="adminer_".uniqid();drop_create("DROP $Eg ".table($a),"CREATE $U ".table($D).$xa,"DROP $U ".table($D),"CREATE $U ".table($Yi).$xa,"DROP $U ".table($Yi),($_POST["drop"]?substr(ME,0,-1):$A),lang(213),$C,lang(214),$a,$D);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($Eg!="VIEW");if(!$k)$k=error();}page_header(($a!=""?lang(37):lang(215)),$k,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>',lang(195),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],lang(136)):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($a!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$a)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$we=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Ci=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$k){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(216));elseif(in_array($K["INTERVAL_FIELD"],$we)&&isset($Ci[$K["STATUS"]])){$Xh="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(217):lang(218)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$Xh.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$Xh)."\n".$Ci[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?lang(219).": ".h($aa):lang(220)),$k);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(195),'<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(221),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(222),'<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>',lang(223),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$we,$K["INTERVAL_FIELD"]),'<tr><th>',lang(124),'<td>',html_select("STATUS",$Ci,$K["STATUS"]),'<tr><th>',lang(44),'<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",lang(224)),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($aa!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$aa)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$Sh=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$k){foreach($K["fields"]as$x=>$l){if($l["field"]=="")unset($K["fields"][$x]);}$jg=routine_id($ca,routine($_GET["procedure"],$Sh));$Tf=routine_id($K["name"],$K);$g=create_routine($Sh,$K);$A=substr(ME,0,-1);$C=lang(225);if(!$_POST["drop"]&&$jg==$Tf&&connection()->flavor!="mysql")query_redirect(substr_replace($g,' OR REPLACE',6,0),$A,$C);else{$Yi="adminer_".uniqid();drop_create("DROP $Sh $jg",$g,"DROP $Sh $Tf",create_routine($Sh,array("name"=>$Yi)+$K),"DROP $Sh ".routine_id($Yi,$K),$A,lang(226),$C,lang(227),$ca,$K["name"]);}}page_header(($ca!=""?(isset($_GET["function"])?lang(228):lang(229)).": ".h($ca):(isset($_GET["function"])?lang(230):lang(231))),$k);if(!$_POST){if($ca=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$Sh);$K["name"]=$ca;}}$ib=get_vals("SHOW CHARACTER SET");sort($ib);$Th=routine_languages();echo($ib?"<datalist id='collations'>".optionlist($ib)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(195),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($Th?"<label>".lang(23).": ".html_select("language",$Th,$K["language"])."</label>\n":""),'<input type=\'submit\' value=\'',lang(17),'\'>
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$ib,$Sh);if(isset($_GET["function"])){echo"<tr><td>".lang(232);edit_type("returns",(array)$K["returns"],$ib,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($ca!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$ca)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$D=$_GET["name"];$K=$_POST;if($K&&!$k){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$D",($K["drop"]?"":$K["clause"]));else{$I=($D==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($D)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".url_escape($a),($K["drop"]?lang(233):($D!=""?lang(234):lang(235))),$I);}page_header(($D!=""?lang(236):lang(147)),$k,array("table"=>$a),h($D!=""?$D:$a));if(!$K){$Za=driver()->checkConstraints($a);$K=array("name"=>$D,"clause"=>$Za[$D]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo
lang(195).': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type=\'submit\' value=\'',lang(17),'\'>
';if($D!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$D)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$D="$_GET[name]";$uj=trigger_options();$K=(array)trigger($D,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$k&&in_array($_POST["Timing"],$uj["Timing"])&&in_array($_POST["Event"],$uj["Event"])&&in_array($_POST["Type"],$uj["Type"])){$mg=" ON ".table($a);$pc="DROP TRIGGER ".idf_escape($D).(JUSH=="pgsql"?$mg:"");$A=ME."table=".url_escape($a);if($_POST["drop"])query_redirect($pc,$A,lang(237));else{if($D!="")queries($pc);queries_redirect($A,($D!=""?lang(238):lang(239)),queries(create_trigger($mg,$_POST)));if($D!="")queries(create_trigger($mg,$K+array("Type"=>reset($uj["Type"]))));}}$K=$_POST;}page_header(($D!=""?lang(240):lang(149)),$k,array("table"=>$a),h($D!=""?$D:$a));$tj=on('change','triggerChange',"^".preg_quote($a,"/")."_[ba][iud]$",$a);echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(241),'<td>',html_select("Timing",$uj["Timing"],$K["Timing"],$tj),'<tr><th>',lang(242),'<td>',html_select("Event",$uj["Event"],$K["Event"],$tj),(in_array("UPDATE OF",$uj["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>',lang(43),'<td>',html_select("Type",$uj["Type"],$K["Type"]),'</table>
<p>',lang(195),': <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("fire(qs('#form')['Timing'], 'change');"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if($D!="")echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,$D)),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$ea=$_GET["user"];$th=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$vb)$th[$vb=="File access on server"?"Server Admin":$vb][$K["Privilege"]]=$K["Comment"];}unset($th["Server Admin"]["Usage"]);foreach($th["Tables"]as$x=>$X)unset($th["Databases"][$x]);$Sf=array();if($_POST){foreach($_POST["objects"]as$x=>$X)$Sf[$X]=(array)$Sf[$X]+idx($_POST["grants"],$x,array());}$Cd=array();if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$B)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$B[1],$lf,PREG_SET_ORDER)){foreach($lf
as$X){if($X[1]!="USAGE")$Cd["$B[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$Cd["$B[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$k){$lg=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $lg",ME."privileges=",lang(243));else{$Vf=q($_POST["user"])."@".q($_POST["host"]);$Wg=$_POST["pass"];$Bb=false;$I=true;if($lg!=$Vf){$Bb=queries("CREATE USER $Vf IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($Wg));$I=$Bb;}elseif($Wg!="")$I=queries("SET PASSWORD FOR $Vf = ".(min_version(8,99)||$_POST["hashed"]?q($Wg):"PASSWORD(".q($Wg).")"));if($I){$Ph=array();foreach($Sf
as$cg=>$Bd){if(isset($_GET["grant"]))$Bd=array_filter($Bd);$Bd=array_keys($Bd);if(isset($_GET["grant"]))$Ph=array_diff(array_keys(array_filter($Sf[$cg],'strlen')),$Bd);elseif($lg==$Vf){$ig=array_keys((array)$Cd[$cg]);$Ph=array_diff($ig,$Bd);$Bd=array_diff($Bd,$ig);unset($Cd[$cg]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$cg,$B)&&(!grant("REVOKE",$Ph,$B[2]," ON $B[1] FROM $Vf")||!grant("GRANT",$Bd,$B[2]," ON $B[1] TO $Vf"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($lg!=$Vf)queries("DROP USER $lg");elseif(!isset($_GET["grant"])){foreach($Cd
as$cg=>$Ph){if(preg_match('~^(.+)(\(.*\))?$~U',$cg,$B))grant("REVOKE",array_keys($Ph),$B[2]," ON $B[1] FROM $Vf");}}}if($I&&!Queries::$queries)redirect(ME."privileges=");queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(244):lang(245)),$I);if($Bb)connection()->query("DROP USER $Vf");}}page_header((isset($_GET["host"])?lang(29).": ".h("$ea@$_GET[host]"):lang(157)),$k,array("privileges"=>array('',lang(65))));$K=$_POST;if($K)$Cd=$Sf;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$Cd[(DB==""||$Cd?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(27),'<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>',lang(29),'<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>',lang(30),'<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],lang(246),on('click','hashedClick'))),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(65).doc_link(array('sql'=>"grant.html#priv_level"));$s=0;foreach($Cd
as$cg=>$Bd){echo'<th>'.($cg!="*.*"?"<input name='objects[$s]' value='".h($cg)."' size='10' autocapitalize='off'>":input_hidden("objects[$s]","*.*")."*.*");$s++;}echo"<tbody>\n";foreach(array(""=>"","Server Admin"=>lang(27),"Databases"=>lang(31),"Tables"=>lang(138),"Procedures"=>lang(247),)as$vb=>$Xb){foreach((array)$th[$vb]as$sh=>$lb){echo"<tr><td".($Xb?">$Xb<td":" colspan='2'").' lang="en" title="'.h($lb).'">'.h($sh);$s=0;foreach($Cd
as$cg=>$Bd){$D="'grants[$s][".h(strtoupper($sh))."]'";$Y=$Bd[strtoupper($sh)];if($vb=="Server Admin"&&$cg!=(isset($Cd["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$D><option><option value='1'".($Y?" selected":"").">".lang(248)."<option value='0'".($Y=="0"?" selected":"").">".lang(249)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$D value='1'".($Y?" checked":"").($sh=="All privileges"?" id='grants-$s-all'":($sh=="Grant option"?"":on('click','grantsClick',"grants-$s-all"))).">","</label>";$s++;}}}echo"</table>\n",'<p>
<input type=\'submit\' value=\'',lang(17),'\'>
';if(isset($_GET["host"]))echo'<input type=\'submit\' name=\'drop\' value=\'',lang(133),'\'',confirm(lang(185,"$ea@$_GET[host]")),'>
';echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$k){$Me=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$Me++;}queries_redirect(ME."processlist=",lang(250,$Me),$Me||!$_POST["kill"]);}}page_header(lang(122),$k);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds"',on('click','tableClick').on('dblclick','tableClick'),'>
';$s=-1;foreach(adminer()->processList()as$s=>$K){if(!$s){echo"<thead><tr lang='en'>".(support("kill")?"<td class='hover'>":"");foreach($K
as$x=>$X)echo"<th>$x".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($x),));echo"<tbody>\n";}echo"<tr>".(support("kill")?"<td class='hover'>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$x=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$x=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$x=="query")||(JUSH=="oracle"&&$x=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".url_escape($K["db"])."&":"")."sql=".url_escape($X)).'">'.lang(251).'</a>'.' '.copy_icon():h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo($s+1)."/".lang(252,max_connections()),"<p><input type='submit' value='".lang(253)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$w=indexes($a);$m=fields($a);$rd=column_foreign_keys($a);$hg=$S["Oid"];$la=get_settings("adminer_import");$Qh=array();$d=array();$bi=array();$xg=array();$bj=null;foreach($m
as$x=>$l){$D=adminer()->fieldName($l);$Qf=html_entity_decode(strip_tags($D),ENT_QUOTES);if(isset($l["privileges"]["select"])&&$D!=""){$d[$x]=$Qf;if(is_shortable($l))$bj=adminer()->selectLengthProcess();}if(isset($l["privileges"]["where"])&&$D!="")$bi[$x]=$Qf;if(isset($l["privileges"]["order"])&&$D!="")$xg[$x]=$Qf;$Qh+=$l["privileges"];}list($M,$r)=adminer()->selectColumnsProcess($d,$w);$M=array_unique($M);$r=array_unique($r);$Ae=count($r)<count($M);$Z=adminer()->selectSearchProcess($m,$w);$E=adminer()->selectOrderProcess($m,$w);$z=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Dj=>$K){$xa=convert_field($m[key($K)]);$M=array($xa?:idf_escape(key($K)));$Z[]=where_check(bracket_escape($Dj,true),$m);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$ph=$Fj=array();foreach($w
as$v){if($v["type"]=="PRIMARY"){$ph=array_flip($v["columns"]);$Fj=($M?$ph:array());foreach($Fj
as$x=>$X){if(in_array(idf_escape($x),$M))unset($Fj[$x]);}break;}}if($hg&&!$ph){$ph=$Fj=array($hg=>0);$w[]=array("type"=>"PRIMARY","columns"=>array($hg));}if($_POST&&!$k){$hk=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Za=array();foreach($_POST["check"]as$Wa)$Za[]=where_check($Wa,$m);$hk[]="((".implode(") OR (",$Za)."))";}$jk=$hk;$hk=($hk?"\nWHERE ".implode(" AND ",$hk):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$di=($M?:array("*"));$xb=convert_fields($d,$m,$M);if($xb)$di[]=substr($xb,2);$H="";if(is_array($_POST["check"])&&!$ph){$vd=implode(", ",$di)."\nFROM ".table($a);$Ed=($r&&$Ae?"\nGROUP BY ".implode(", ",$r):"").($E?"\nORDER BY ".implode(", ",$E):"");$Bj=array();foreach($_POST["check"]as$X)$Bj[]="(SELECT".limit($vd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m).$Ed,1).")";$H=implode(" UNION ALL ",$Bj);}adminer()->dumpData($a,"table",$H,$di,$jk,($Ae?$r:array()),$E);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$rd)){if($_POST["save"]||$_POST["delete"]){$I=true;$ma=0;$O=array();if(!$_POST["delete"]){foreach($m
as$D=>$X){$u=bracket_escape($D);if(isset($_POST["fields"][$u])||$_FILES["fields-$u"]){$X=process_input($m[$D]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($D)]=($X!==false?$X:idf_escape($D));}}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($ph&&is_array($_POST["check"]))||$Ae){$I=($_POST["delete"]?driver()->delete($a,$hk):($_POST["clone"]?queries("INSERT $H$hk".driver()->insertReturning($a)):driver()->update($a,$O,$hk)));$ma=connection()->affected_rows;if(is_object($I))$ma+=$I->num_rows;}else{foreach((array)$_POST["check"]as$X){$gk="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m);$I=($_POST["delete"]?driver()->delete($a,$gk,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$gk)):driver()->update($a,$O,$gk,1)));if(!$I)break;$ma+=connection()->affected_rows;}}}$C=lang(254,$ma);if($_POST["clone"]&&$I&&$ma==1){$Te=last_id($I);if($Te)$C=lang(178," $Te");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page|next":""),$C,$I);if(!$_POST["delete"]){$jh=(array)$_POST["fields"];edit_form($a,array_intersect_key($m,$jh),$jh,!$_POST["clone"],$k);page_footer();exit;}}elseif(!$_POST["import"]){$I=true;$ma=0;foreach((array)$_POST["val"]as$Dj=>$K){$O=array();foreach($K
as$x=>$X){$x=bracket_escape($x,true);$O[idf_escape($x)]=(preg_match('~char|text~',$m[$x]["type"])||$X!=""?adminer()->processInput($m[$x],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check(bracket_escape($Dj,true),$m),($Ae||$ph?0:1)," ");if(!$I)break;$ma+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang(254,$ma),$I);}elseif(!is_string($ed=get_file("csv_file",true)))$k=upload_error($ed);elseif(!preg_match('~~u',$ed))$k=lang(255);else{save_settings(array("output"=>$la["output"],"format"=>$_POST["separator"]),"adminer_import");$jb=array_keys($m);$hi=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$Fb=parse_csv($ed,$hi);$ma=count($Fb);driver()->begin();$L=array();foreach($Fb
as$x=>$Wj){if(!$x&&!array_diff($Wj,$jb)){$jb=$Wj;$ma--;}else{$O=array();foreach($Wj
as$s=>$fb)$O[idf_escape($jb[$s])]=($fb==""&&$m[$jb[$s]]["null"]?"NULL":q(csv_value($fb)));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$ph));if($I)driver()->commit();queries_redirect(remove_from_uri("page|next"),lang(256,$ma),$I);driver()->rollback();}}}$Oi=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(47).": $Oi",$k);$O=null;if(isset($Qh["insert"])||!support("table")){$O="";foreach((array)$_GET["where"]as$X){$Y=$X["val"];if(is_array($Y))$Y=(count($Y)==1&&preg_match('~^val-(.*)~s',reset($Y),$B)?$B[1]:"");if($X["col"]!=""&&$Y!=""&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$Y)))))$O
.="&set[".url_escape(bracket_escape($X["col"]))."]=".url_escape($Y);}}adminer()->selectLinks($S,$O);if(!$d&&support("table"))echo"<p class='error'>".lang(257).($m?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div hidden>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$d);adminer()->selectSearchPrint($Z,$bi,$w);adminer()->selectOrderPrint($E,$xg,$w);adminer()->selectLimitPrint($z);if($bj!==null)adminer()->selectLengthPrint($bj);adminer()->selectActionPrint($w);echo"</form>\n";foreach((array)$_GET["where"]as$X){if($X["op"]=="SQL"&&!in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"))){echo"<p class='error'>".lang(103).' '.lang(104)."\n";page_footer();exit;}}$F=$_GET["page"];$ud=null;if($F=="last"){$ud=get_val(count_rows($a,$Z,$Ae,$r));$F=floor(max(0,intval($ud)-1)/$z);}$ci=$M;$Dd=$r;if(!$ci){$ci[]="*";$xb=convert_fields($d,$m,$M);if($xb)$ci[]=substr($xb,2);}foreach($M
as$x=>$X){$l=$m[idf_unescape($X)];if($l&&($xa=convert_field($l)))$ci[$x]="$xa AS $X";}if(JUSH=="pgsql"||JUSH=="mssql"){foreach((array)$_GET["columns"]as$x=>$X){if(isset($ci[$x])&&$X["fun"])$ci[$x].=" AS ".idf_escape(apply_sql_function($X["fun"],($X["col"]!=""?$X["col"]:"*")));}}if(!$Ae&&$Fj){foreach($Fj
as$x=>$X){$ci[]=idf_escape($x);if($Dd)$Dd[]=idf_escape($x);}}$I=driver()->select($a,$ci,$Z,$Dd,$E,$z,$F,true);if(!is_object($I))echo"<p class='error'>".(error()?:lang(25))."\n";else{if(JUSH=="mssql"&&$F)$I->seek($z*$F);$zc=array();$L=array();while($K=$I->fetch_assoc()){if($F&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}$Md=($z&&(support("cursor")?$_GET["next"]!="":count($L)>=$z));if(is_ajax()&&$Md)header("X-Next-Page: ".pagination_href($F+1));if($_GET["modify"]&&$L){$uf=max_input_vars(count($L[0])+1,20);echo($uf&&count($L)>$uf?"<p class='error'>".max_input_vars_error()."\n":"");}echo"<form action='' method='post' enctype='multipart/form-data'>\n";if($_GET["page"]!="last"&&$z&&$r&&$Ae&&JUSH=="sql")$ud=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".lang(15)."\n";else{$Ga=adminer()->backwardKeys($a,$Oi);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').on('keydown','editingKeydown').">\n","<thead><tr>".(!$r&&$M?"":"<td class='hover check'><input type='checkbox' id='all-page' class='jsonly' title='".lang(258)."'".on('click','formCheck','^check').">");$Rf=array();$zd=array();reset($M);$Ah=1;foreach($L[0]as$x=>$X){if(!isset($Fj[$x])){$X=idx($_GET["columns"],key($M))?:array();$l=$m[$M?($X?$X["col"]:current($M)):$x];$D=($l?adminer()->fieldName($l,$Ah):($X["fun"]?"*":h($x)));if($D!=""){$Ah++;$Rf[$x]=$D;$c=idf_escape($x);$Xd=remove_from_uri('(order|desc)[^=]*|page|next').'&order[0]='.url_escape($x);$Xb="&desc[0]=1";$ri=preg_replace('~ DESC( NULLS LAST)?$~','',$E[0]);$ti=($ri==$c||$ri==$x);echo"<th id='th[".h(bracket_escape($x))."]'".($ti?" aria-sort='".($ri==$E[0]?"ascending":"descending")."'":"").">";$yd=apply_sql_function($X["fun"],$D);$si=isset($l["privileges"]["order"])||$yd!=$D;echo($si?"<a href='".h($Xd.($ti&&$ri==$E[0]?$Xb:''))."'>$yd</a>":$yd);$Af=($si?"<a href='".h($Xd.$Xb)."' title='".lang(53)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($l["privileges"]["where"]))$Af
.="<a href='#fieldset-search' title='".lang(50)."' class='text jsonly'".on('click','selectSearch',$x)."> =</a>";echo($Af?"<span class='column'>$Af</span>":"");}$zd[$x]=$X["fun"];next($M);}}$af=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$x=>$X)$af[$x]=max($af[$x],min(40,strlen(utf8_decode($X))));}}echo($Ga?"<th>".lang(259):"")."<tbody>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$rd)as$Pf=>$K){$Cj=unique_array($L[$Pf],$w);if(!$Cj){$Cj=array();reset($M);foreach($L[$Pf]as$x=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$Cj[$x]=$X;next($M);}}$Dj="";foreach($Cj
as$x=>$X){$l=(array)$m[$x];$_e=is_blob($l);if((JUSH=="sql"||JUSH=="pgsql")&&($_e||preg_match('~char|text|enum|set~',$l["type"]))&&strlen($X)>64){$x=(strpos($x,'(')?$x:idf_escape($x));$x="MD5(".($_e||JUSH!='sql'||preg_match("~^utf8~",$l["collation"])?$x:"CONVERT($x USING ".charset(connection()).")").")";$X=md5($_e?(string)driver()->value($X,$l):$X);}$Dj
.="&".($X!==null?"where[".url_escape(bracket_escape($x))."]=".url_escape($X===false?"f":$X):"null[]=".url_escape($x));}echo"<tr>".(!$r&&$M?"":"<td class='hover check'>".($Ae||information_schema(DB)?"":"<a href='".h(ME."edit=".url_escape($a).$Dj)."' class='edit'>".lang(260)."</a> ").checkbox("check[]",substr($Dj,1),in_array(substr($Dj,1),(array)$_POST["check"])));reset($M);foreach($K
as$x=>$X){if(isset($Rf[$x])){$c=current($M);$l=(array)$m[$x];if($X!=""&&(!isset($zc[$x])||$zc[$x]!=""))$zc[$x]=(is_mail($X)?$Rf[$x]:"");$_="";if(is_blob($l)&&$X!="")$_=ME.'download='.url_escape($a).'&field='.url_escape($x).$Dj;if(!$_&&$X!==null){foreach((array)$rd[$x]as$o){if(count($rd[$x])==1||end($o["source"])==$x){$_="";foreach($o["source"]as$s=>$ui)$_
.=where_link($s,$o["target"][$s],$L[$Pf][$ui]);$_=($o["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.url_escape($o["db"]),ME):ME).'select='.url_escape($o["table"]).$_;if($o["ns"])$_=preg_replace('~([?&]ns=)[^&]+~','\1'.url_escape($o["ns"]),$_);if(count($o["source"])==1)break;}}}if($c=="COUNT(*)"){$_=ME."select=".url_escape($a);$s=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Cj))$_
.=where_link($s++,$W["col"],$W["val"],$W["op"]);}foreach($Cj
as$Je=>$W)$_
.=where_link($s++,$Je,$W);}$Yd=select_value($X,$_,$l,$bj);$u=bracket_escape($Dj);$t=h("val[$u][".bracket_escape($x)."]");$lh=idx(idx($_POST["val"],$u),bracket_escape($x));$Ij=idx($l["privileges"],"update");$vc=!is_array($K[$x])&&!is_blob($l)&&is_utf8($X)&&$L[$Pf][$x]==$X&&!$zd[$x]&&!$l["generated"]&&$Ij;$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$c,$B)?$m[idf_unescape($B[2])]["type"]:$l["type"]);$aj=preg_match('~text|json|lob~',$U);$Be=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$c);echo"<td id='$t'".($Be&&($X===null||is_numeric(strip_tags($Yd))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$vc&&$X!==null)||$lh!==null){$Hd=h($lh!==null?$lh:$X);echo">".($aj?"<textarea name='$t' cols='30' rows='".(substr_count($X,"\n")+1)."'>$Hd</textarea>":"<input name='$t' value='$Hd' size='$af[$x]'>");}else{$if=strpos($Yd,"<i>…</i>");echo($Ij?" data-text='".($if?2:($aj?1:0))."'".($vc?"":" data-warning='".lang(261)."'"):"").">$Yd";}}next($M);}if($Ga)echo"<td>";adminer()->backwardKeysPrint($Ga,$L[$Pf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$F||$Md){$Mc=true;if($_GET["page"]!="last"){if(!$z||(count($L)<$z&&($L||!$F)))$ud=($F?$F*$z:0)+count($L);elseif(JUSH!="sql"||!$Ae){$ud=($Ae?false:found_rows($S,$Z));if(intval($ud)<max(1e4,2*($F+1)*$z))$ud=first(slow_query(count_rows($a,$Z,$Ae,$r)));elseif(JUSH=='sql'||JUSH=='pgsql')$Mc=false;}}if(!support("cursor"))$Md=(($ud===false?count($L)+1:$ud-$F*$z)>$z);$Kg=($z&&($Md||$F));if($Kg)echo($Md?'<p><a href="'.h(pagination_href($F+1)).'" class="loadmore"'.on('click','selectLoadMore',lang(262)).'>'.lang(263).'</a>':''),"\n";echo"<div class='footer'><div>\n";if($Kg){$sf=($ud===false?$F+($L?(count($L)>=$z?2:1):0):floor(($ud-1)/$z));echo"<fieldset><legend>".lang(264)."</legend>";if(!support("cursor")){echo
pagination(0,$F).($F>5?" …":"");for($s=max(1,$F-4);$s<min($sf,$F+5);$s++)echo
pagination($s,$F);if($sf>0)echo($F+5<$sf?" …":""),($Mc&&$ud!==false?pagination($sf,$F):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$sf'>".lang(265)."</a>");}else
echo
pagination(0,$F).($F>1?" …":""),($F?pagination($F,$F):""),($Md?pagination($F+1,$F)." …":"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(266)."</legend>";$fc=($Mc?"":"~ ").$ud;$Ne=($ud!==false?($Mc?"":"~ ").lang(161,$ud):"");echo
checkbox("all",1,0,$Ne,on('click','countRows',$fc))."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':" title='".lang(267)."'"),'>
<legend><a href=\'',h($_GET["modify"]?remove_from_uri("modify"):relative_uri()."&modify=1"),'\'>',lang(268),'</a></legend><div>
<input type=\'submit\' id=\'save\' value=\'',lang(17),'\'',($_GET["modify"]?'':" class='jsonly' disabled"),'>
</div></fieldset>

<fieldset><legend>',lang(132),' <span id="selected"></span></legend><div>
<input type=\'submit\' name=\'edit\' value=\'',lang(13),'\'>
<input type=\'submit\' name=\'clone\' value=\'',lang(251),'\'>
<input type=\'submit\' name=\'delete\' value=\'',lang(21),'\'',confirm(),'>
</div></fieldset>
';$sd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$c){if($c["fun"]){unset($sd['sql']);break;}}if($sd){print_fieldset("export",lang(70)." <span id='selected2'></span>");$Ig=adminer()->dumpOutput();echo($Ig?html_select("output",$Ig,$la["output"])." ":""),html_select("format",$sd,$la["format"])," <input type='submit' name='export' value='".lang(70)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($zc,'strlen'),$d);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import' class='toggle'>".lang(69)."</a>","<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",file_input(" name='csv_file'"," ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"])." <input type='submit' name='import' value='".lang(69)."'>"),"</span>";echo
input_token(),"</form>\n",(!$r&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?lang(124):lang(123));$Xj=($P?adminer()->showStatus():adminer()->showVariables());if(!$Xj)echo"<p class='message'>".lang(15)."\n";else{echo"<table>\n";foreach($Xj
as$K){echo"<tr>";$x=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($x)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: application/json; charset=utf-8");if($_GET["script"]=="db"){$Ji=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$D=>$S){json_row("Comment-$D",h($S["Comment"]));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$x)json_row("$x-$D",h($S[$x]));foreach(array_keys($Ji+array("Auto_increment"=>0,"Rows"=>0))as$x){if(array_key_exists($x,$S))json_row("$x-$D",format_status($S,$x));if($S[$x]!=""&&isset($Ji[$x]))$Ji[$x]+=($S["Engine"]!="InnoDB"||$x!="Data_free"?$S[$x]:0);}}}if(function_exists('Adminer\db_status'))$Ji=db_status();foreach($Ji
as$x=>$X)json_row("sum-$x",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases(false))as$i=>$X){json_row("tables-$i",$X);json_row("size-$i",db_size($i));}json_row("");}exit;}else{$Ui=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($Ui&&!$k&&!$_POST["search"]){$I=true;$C="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$C=lang(269);}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$C=lang(270);}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$C=lang(271);}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$C=lang(272);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$C
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),(array)$_POST["tables"]));$C=lang(273);}elseif(!$_POST["tables"])$C=lang(12);elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$C
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect($_SERVER["REQUEST_URI"],$C,$I);}page_header(($_GET["ns"]==""?lang(31).": ".h(DB):lang(156).": ".h($_GET["ns"])),$k,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$E=$_GET["order"];$wd=($E||support("fast_status"));echo"<div>\n","<h3 id='tables-views'>".lang(274)."</h3>\n";$Ti=($wd?table_status():tables_list());if(!$Ti)echo"<p class='message'>".lang(12)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(275)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'".on('keydown','submitKeydown','search').">"," <input type='submit' name='search' value='".lang(50)."'>\n","</div></fieldset>\n";if(!$k&&$_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'".on('click','tableClick').on('dblclick','tableClick').">\n",'<thead><tr class="wrap">','<td class="hover"><input id="check-all" type="checkbox" class="jsonly" title="'.lang(155).'"'.on('click','formCheck','^(tables|views)\[').'>','<th'.(!$E&&JUSH!='sqlite'?" aria-sort='ascending'":'').'><a href="'.h(substr(ME,0,-1)).'">'.lang(138).'</a>';$d=array("Engine"=>array(lang(276).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$d["Collation"]=array(lang(128).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$d["Data_length"]=array(lang(277).doc_link(array('sql'=>'show-table-status.html',)),"create",lang(38),);if(support("indexes"))$d["Index_length"]=array(lang(278).doc_link(array('sql'=>'show-table-status.html',)),"indexes",lang(141),);$d["Data_free"]=array(lang(279).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(39));if(function_exists('Adminer\alter_table'))$d["Auto_increment"]=array(lang(45).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(38),);$d["Rows"]=array(lang(280).doc_link(array('sql'=>'show-table-status.html',)),"select",lang(35),);if(support("comment"))$d["Comment"]=array(lang(44).doc_link(array('sql'=>'show-table-status.html',)));$ya=array('Engine','Collation','Comment');foreach($d
as$x=>$c)echo"<th".($E==$x?" aria-sort='".(in_array($x,$ya)?"ascending":"descending")."'":"")."><a href='".h(ME)."order=$x'>$c[0]</a>";echo"<tbody>\n";if($E){uasort($Ti,function($ga,$Da)use($E,$ya){$J=($ga[$E]<$Da[$E]?-1:($ga[$E]>$Da[$E]?1:0));return(in_array($E,$ya)?$J:-$J);});}$T=0;$Ji=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach($Ti
as$D=>$P){$ak=($wd?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($wd?$P:array('Engine'=>$P));$t=h("Table-".$D);echo'<tr><td class="hover">'.checkbox(($ak?"views[]":"tables[]"),$D,in_array("$D",$Ui,true),"","","",$t),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".url_escape($D)."' title='".lang(36)."' id='$t'>".h($D).'</a>':h($D));if($ak&&!preg_match('~materialized~i',$P['Engine'])){$fj=lang(137);echo'<td colspan="'.(count($d)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".url_escape($D)."' title='".lang(37)."'>$fj</a>":$fj),"<td align='right'><a href='".h(ME)."select=".url_escape($D)."' title='".lang(35)."'>?</a>";if(support("comment"))echo'<td>'.h($P['Comment']);}else{if($wd){foreach(array_keys($Ji)as$x)$Ji[$x]+=($P["Engine"]!="InnoDB"||$x!="Data_free"?idx($P,$x):0);}foreach($d
as$x=>$c){$t=" id='$x-".h($D)."'";echo($c[1]?"<td align='right'><a href='".h(ME."$c[1]=").url_escape($D)."'$t title='$c[2]'>".format_status($P,$x)."</a>":"<td$t>".h(idx($P,$x,'?')));}$T++;}echo"\n";}echo"<tr><td class='hover'><th>".lang(252,count($Ti)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');if($wd&&function_exists('Adminer\db_status'))$Ji=db_status();foreach($Ji
as$x=>$Ii)echo($d[$x]?"<td align='right' id='sum-$x'>".($wd?format_number($Ii):""):"");echo"\n","</table>\n",($wd?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Uj="<input type='submit' value='".lang(281)."'".on_help("VACUUM")."> ";$tg="<input type='submit' name='optimize' value='".lang(282)."'".on_help(JUSH=="sql"?"OPTIMIZE TABLE":"VACUUM ANALYZE")."> ";$qh=(JUSH=="sqlite"?$Uj."<input type='submit' name='check' value='".lang(283)."'".on_help("PRAGMA integrity_check")."> ":(JUSH=="pgsql"?$Uj.$tg:(JUSH=="sql"?"<input type='submit' value='".lang(284)."'".on_help("ANALYZE TABLE")."> ".$tg."<input type='submit' name='check' value='".lang(283)."'".on_help("CHECK TABLE")."> "."<input type='submit' name='repair' value='".lang(285)."'".on_help("REPAIR TABLE")."> ":""))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(286)."'".confirm().on_help(JUSH=="sqlite"?"DELETE":"TRUNCATE".(JUSH=="pgsql"?"":" TABLE"))."> ":"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(133)."'".confirm().on_help("DROP TABLE").">":"");echo($qh?"<div class='footer'><div>\n<fieldset><legend>".lang(132)." <span id='selected'></span></legend><div>$qh\n</div></fieldset>\n":"");$h=(support("scheme")?adminer()->schemas():adminer()->databases());if(count($h)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(287)." <span id='selected3'></span></legend><div>";$i=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($h?html_select("target",$h,$i):'<input name="target" value="'.h($i).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(116)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(22)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(288)):""),"</div></fieldset>\n";}echo"<input type='hidden' name='all' value=''".on('click','countTables',$T).">\n",input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links hover'><a href='".h(ME)."create='>".lang(71)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(215)."</a>\n":""),"</div>\n";if(support("routine")){echo"<div>\n","<h3 id='routines'>".lang(66)."</h3>\n";$Vh=routines();if($Vh){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(195).'<td>'.lang(43).'<td>'.lang(232)."<td class='hover'><tbody>\n";foreach($Vh
as$K){$D=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".url_escape($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').url_escape($K["SPECIFIC_NAME"]).$D).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td class="hover"><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').url_escape($K["SPECIFIC_NAME"]).$D).'">'.lang(144)."</a>";}echo"</table>\n";}echo'<p class="links hover">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(231).'</a>':'').'<a href="'.h(ME).'function=">'.lang(230)."</a>\n","</div>\n";}if(support("event")){echo"<div>\n","<h3 id='events'>".lang(68)."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".lang(195)."<td>".lang(289)."<td>".lang(221)."<td>".lang(222)."<td><tbody>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?lang(290)."<td>".h($K["Execute at"]):lang(223)." ".h($K["Interval value"])." ".h($K["Interval field"])."<td>".h($K["Starts"])),"<td>".h($K["Ends"]),'<td><a href="'.h(ME).'event='.url_escape($K["Name"]).'">'.lang(144).'</a>';echo"</table>\n";$Kc=get_val("SELECT @@event_scheduler");if($Kc&&$Kc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($Kc)."\n";}echo'<p class="links hover"><a href="'.h(ME).'event=">'.lang(220)."</a>\n","</div>\n";}}}}page_footer();