<?php
/** Adminer - Compact database management
* @link https://www.adminer.org/
* @author Jakub Vrana, https://www.vrana.cz/
* @copyright 2007 Jakub Vrana
* @license https://www.apache.org/licenses/LICENSE-2.0 Apache License, Version 2.0
* @license https://www.gnu.org/licenses/gpl-2.0.html GNU General Public License, version 2 (one or other)
* @version 5.5.1
*/namespace
Adminer;const
VERSION="5.5.1";error_reporting(24575);set_error_handler(function($uc,$wc){return!!preg_match('~^Undefined (array key|offset|index)~',$wc);},E_WARNING|E_NOTICE);$Sc=!preg_match('~^(unsafe_raw)?$~',ini_get("filter.default"));if($Sc||ini_get("filter.default_flags")){foreach(array('_GET','_POST','_COOKIE','_SERVER')as$X){$Li=filter_input_array(constant("INPUT$X"),FILTER_UNSAFE_RAW);if($Li)$$X=$Li;}}if(function_exists("mb_internal_encoding"))mb_internal_encoding("8bit");function
connection($f=null){return($f?:Db::$instance);}function
adminer(){return
Adminer::$instance;}function
driver(){return
Driver::$instance;}function
connect(){$yb=adminer()->credentials();$J=Driver::connect($yb[0],$yb[1],$yb[2]);return(is_object($J)?$J:null);}function
idf_unescape($t){if(!preg_match('~^[`\'"[]~',$t))return$t;$te=substr($t,-1);return
str_replace($te.$te,$te,substr($t,1,-1));}function
q($Q){return
connection()->quote($Q);}function
idx($ua,$w,$j=null){return($ua&&array_key_exists($w,$ua)?$ua[$w]:$j);}function
number($X){return
preg_replace('~[^0-9]+~','',$X);}function
number_type(){return'((?<!o)int(?!er)|numeric|real|float|double|decimal|money)';}function
remove_slashes(array$bj,$Sc=false){$J=array();foreach($bj
as$w=>$X)$J[stripslashes($w)]=(is_array($X)?remove_slashes($X,$Sc):($Sc?$X:stripslashes($X)));return$J;}function
bracket_escape($t,$Ca=false){static$zi=array(':'=>':1',']'=>':2','['=>':3','"'=>':4');return
strtr($t,($Ca?array_flip($zi):$zi));}function
min_version($ej,$Ke="",$f=null){$f=connection($f);$vh=$f->server_info;if($Ke&&preg_match('~([\d.]+)-MariaDB~',$vh,$A)){$vh=$A[1];$ej=$Ke;}return$ej&&version_compare($vh,$ej)>=0;}function
charset(Db$e){return(min_version("5.5.3",0,$e)?"utf8mb4":"utf8");}function
ini_set($Nf,$Y){return(function_exists('ini_set')?\ini_set($Nf,$Y):false);}function
ini_bool($Td){$X=ini_get($Td);return(preg_match('~^(on|true|yes)$~i',$X)||(int)$X);}function
ini_bytes($Td){$X=ini_get($Td);switch(strtolower(substr($X,-1))){case'g':$X=(int)$X*1024;case'm':$X=(int)$X*1024;case'k':$X=(int)$X*1024;}return$X;}function
sid(){static$J;if($J===null)$J=(SID&&!($_COOKIE&&ini_bool("session.use_cookies")));return$J;}function
set_password($dj,$N,$V,$F){$_SESSION["pwds"][$dj][$N][$V]=($_COOKIE["adminer_key"]&&is_string($F)?array(encrypt_string($F,$_COOKIE["adminer_key"])):$F);}function
get_password(){$J=get_session("pwds");if(is_array($J))$J=($_COOKIE["adminer_key"]?decrypt_string($J[0],$_COOKIE["adminer_key"]):false);return$J;}function
get_val($H,$l=0,$ob=null){$ob=connection($ob);$I=$ob->query($H);if(!is_object($I))return
false;$K=$I->fetch_row();return($K?$K[$l]:false);}function
get_vals($H,$b=0){$J=array();$I=connection()->query($H);if(is_object($I)){while($K=$I->fetch_row())$J[]=$K[$b];}return$J;}function
get_key_vals($H,$f=null,$yh=true){$f=connection($f);$J=array();$I=$f->query($H);if(is_object($I)){while($K=$I->fetch_row()){if($yh)$J[$K[0]]=$K[1];else$J[]=$K[0];}}return$J;}function
get_rows($H,$f=null,$k="<p class='error'>"){$ob=connection($f);$J=array();$I=$ob->query($H);if(is_object($I)){while($K=$I->fetch_assoc())$J[]=$K;}elseif(!$I&&!$f&&$k&&(defined('Adminer\PAGE_HEADER')||$k=="-- "))echo$k.error()."\n";return$J;}function
unique_array($K,array$v){foreach($v
as$u){if(preg_match("~PRIMARY|UNIQUE~",$u["type"])&&!$u["partial"]){$J=array();foreach($u["columns"]as$w){if(!isset($K[$w]))continue
2;$J[$w]=$K[$w];}return$J;}}}function
escape_key($w){if(preg_match('(^([\w(]+)('.str_replace("_",".*",preg_quote(idf_escape("_"))).')([ \w)]+)$)',$w,$A))return$A[1].idf_escape(idf_unescape($A[2])).$A[3];return
idf_escape($w);}function
where(array$Z,array$m=array()){$J=array();foreach((array)$Z["where"]as$w=>$X){$w=bracket_escape($w,true);$b=escape_key($w);$l=idx($m,$w,array());$Oc=$l["type"];$J[]=$b.(JUSH=="sql"&&$Oc=="json"?" = CAST(".q($X)." AS JSON)":(JUSH=="pgsql"&&preg_match('~^jsonb?$~',$l["full_type"])?"::jsonb = ".q($X)."::jsonb":(JUSH=="sql"&&is_numeric($X)&&preg_match('~\.~',$X)?" LIKE ".q($X):(JUSH=="mssql"&&strpos($Oc,"datetime")===false?" LIKE ".q(preg_replace('~[_%[]~','[\0]',$X)):" = ".unconvert_field($l,q($X))))));if(JUSH=="sql"&&preg_match('~char|text~',$Oc)&&preg_match("~[^ -@]~",$X))$J[]="$b = ".q($X)." COLLATE ".charset(connection())."_bin";}foreach((array)$Z["null"]as$w)$J[]=escape_key($w)." IS NULL";return
implode(" AND ",$J);}function
where_check($X,array$m=array()){parse_str($X,$Ua);remove_slashes(array(&$Ua));return
where($Ua,$m);}function
where_link($r,$b,$Y,$Kf="="){return"&where%5B$r%5D%5Bcol%5D=".urlencode($b)."&where%5B$r%5D%5Bop%5D=".urlencode(($Y!==null?$Kf:"IS NULL"))."&where%5B$r%5D%5Bval%5D=".urlencode($Y);}function
convert_fields(array$c,array$m,array$M=array()){$J="";foreach($c
as$w=>$X){if($M&&!in_array(idf_escape($w),$M))continue;$va=convert_field($m[$w]);if($va)$J
.=", $va AS ".idf_escape($w);}return$J;}function
cookie_path(){return
strtr(preg_replace('~\?.*~','',$_SERVER["REQUEST_URI"]),array(";"=>"%3B",","=>"%2C"));}function
cookie($C,$Y,$Be=2592000){header("Set-Cookie: $C=".rawurlencode($Y).($Be?"; expires=".gmdate("D, d M Y H:i:s",time()+$Be)." GMT":"")."; path=".cookie_path().(HTTPS?"; secure":"")."; HttpOnly; SameSite=lax",false);}function
get_url($Si,$sb){$J=@file_get_contents($Si,false,$sb);if(function_exists('http_get_last_response_headers'))$http_response_header=http_get_last_response_headers();return
array($J,(preg_match('~^HTTP/[\d.]+ (\d+)~',$http_response_header[0],$A)?$A[1]:''),);}function
get_settings($ub){parse_str($_COOKIE[$ub],$zh);return$zh;}function
get_setting($w,$ub="adminer_settings",$j=null){return
idx(get_settings($ub),$w,$j);}function
save_settings(array$zh,$ub="adminer_settings"){$Y=http_build_query($zh+get_settings($ub));cookie($ub,$Y);$_COOKIE[$ub]=$Y;}function
restart_session(){if(!ini_bool("session.use_cookies")&&(!function_exists('session_status')||session_status()==1))session_start();}function
stop_session($Yc=false){$Vi=ini_bool("session.use_cookies");if(!$Vi||$Yc){session_write_close();if($Vi&&ini_set("session.use_cookies",'0')===false)session_start();}}function&get_session($w){return$_SESSION[$w][DRIVER][SERVER][$_GET["username"]];}function
set_session($w,$X){$_SESSION[$w][DRIVER][SERVER][$_GET["username"]]=$X;}function
auth_url($dj,$N,$V,$i=null){$Ri=remove_from_uri(implode("|",array_keys(SqlDriver::$drivers))."|username|ext|".($i!==null?"db|":"").($dj=='mssql'||$dj=='pgsql'?"":"ns|").session_name());preg_match('~([^?]*)\??(.*)~',$Ri,$A);return"$A[1]?".(sid()?SID."&":"").($dj!="server"||$N!=""?urlencode($dj)."=".urlencode($N)."&":"").($_GET["ext"]?"ext=".urlencode($_GET["ext"])."&":"")."username=".urlencode($V).($i!=""?"&db=".urlencode($i):"").($A[2]?"&$A[2]":"");}function
is_ajax(){return($_SERVER["HTTP_X_REQUESTED_WITH"]=="XMLHttpRequest");}function
redirect($_,$B=null){if($B!==null){restart_session();$_SESSION["messages"][preg_replace('~^[^?]*~','',($_!==null?$_:$_SERVER["REQUEST_URI"]))][]=$B;}if($_!==null){if($_=="")$_=".";header("Location: $_");exit;}}function
query_redirect($H,$_,$B,$Qg=true,$Ac=true,$Jc=false,$mi=""){if($Ac){$Lh=microtime(true);$Jc=!connection()->query($H);$mi=format_time($Lh);}$Gh=($H?adminer()->messageQuery($H,$mi,$Jc):"");if($Jc){adminer()->error
.=error().$Gh.script("messagesPrint();")."<br>";return
false;}if($Qg)redirect($_,$B.$Gh);return
true;}class
Queries{static$queries=array();static$start=0;}function
queries($H){if(!Queries::$start)Queries::$start=microtime(true);Queries::$queries[]=(driver()->delimiter!=';'?$H:(preg_match('~;$~',$H)?"DELIMITER ;;\n$H;\nDELIMITER ":$H).";");return
connection()->query($H);}function
apply_queries($H,array$T,$xc='Adminer\table'){foreach($T
as$R){if(!queries("$H ".$xc($R)))return
false;}return
true;}function
queries_redirect($_,$B,$Qg){$Lg=implode("\n",Queries::$queries);$mi=format_time(Queries::$start);return
query_redirect($Lg,$_,$B,$Qg,false,!$Qg,$mi);}function
format_time($Lh){return
lang(0,max(0,microtime(true)-$Lh));}function
relative_uri(){return
str_replace(":","%3a",preg_replace('~^[^?]*/([^?]*)~','\1',$_SERVER["REQUEST_URI"]));}function
remove_from_uri($eg=""){return
substr(preg_replace("~(?<=[?&])($eg".(SID?"":"|".session_name()).")=[^&]*&~",'',relative_uri()."&"),0,-1);}function
get_file($w,$Jb=false,$Pb=""){$Qc=$_FILES[$w];if(!$Qc)return
null;foreach($Qc
as$w=>$X)$Qc[$w]=(array)$X;$J='';foreach($Qc["error"]as$w=>$k){if($k)return$k;$C=$Qc["name"][$w];$ui=$Qc["tmp_name"][$w];$qb=file_get_contents($Jb&&preg_match('~\.gz$~',$C)?"compress.zlib://$ui":$ui);if($Jb){$Lh=substr($qb,0,3);if(function_exists("iconv")&&preg_match("~^\xFE\xFF|^\xFF\xFE~",$Lh))$qb=iconv("utf-16","utf-8",$qb);elseif($Lh=="\xEF\xBB\xBF")$qb=substr($qb,3);}$J
.=$qb;if($Pb)$J
.=(preg_match("($Pb\\s*\$)",$qb)?"":$Pb)."\n\n";}return$J;}function
upload_error($k){$Te=($k==UPLOAD_ERR_INI_SIZE?ini_get("upload_max_filesize"):0);return($k?lang(1).($Te?" ".lang(2,$Te):""):lang(3));}function
repeat_pattern($rg,$x){return
str_repeat("$rg{0,65535}",$x/65535)."$rg{0,".($x%65535)."}";}function
is_utf8($X){return(preg_match('~~u',$X)&&!preg_match('~[\0-\x8\xB\xC\xE-\x1F]~',$X));}function
format_number($X){return
strtr(number_format($X,0,".",lang(4)),preg_split('~~u',lang(5),-1,PREG_SPLIT_NO_EMPTY));}function
friendly_url($X){return
preg_replace('~\W~i','-',$X);}function
table_status1($R,$Kc=false){$J=table_status($R,$Kc);return($J?reset($J):array("Name"=>$R));}function
column_foreign_keys($R){$J=array();foreach(adminer()->foreignKeys($R)as$n){foreach($n["source"]as$X)$J[$X][]=$n;}return$J;}function
fields_from_edit(){$J=array();foreach((array)$_POST["field_keys"]as$w=>$X){if($X!=""){$X=bracket_escape($X);$_POST["function"][$X]=$_POST["field_funs"][$w];$_POST["fields"][$X]=$_POST["field_vals"][$w];}}foreach((array)$_POST["fields"]as$w=>$X){$C=bracket_escape($w,true);$J[$C]=array("field"=>$C,"privileges"=>array("insert"=>1,"update"=>1,"where"=>1,"order"=>1),"null"=>1,"auto_increment"=>($w==driver()->primary),);}return$J;}function
dump_headers($Ed,$lf=false){$J=adminer()->dumpHeaders($Ed,$lf);$bg=$_POST["output"];if($bg!="text")header("Content-Disposition: attachment; filename=".adminer()->dumpFilename($Ed).".$J".($bg!="file"&&preg_match('~^[0-9a-z]+$~',$bg)?".$bg":""));session_write_close();if(!ob_get_level())ob_start(null,4096);ob_flush();flush();return$J;}function
dump_csv(array$K){$Ei=$_POST["format"]=="tsv";foreach($K
as$w=>$X){if(preg_match('~["\n]|^0[^.]|\.\d*0$|'.($Ei?'\t':'[,;]|^$').'~',$X))$K[$w]='"'.str_replace('"','""',$X).'"';}echo
implode(($_POST["format"]=="csv"?",":($Ei?"\t":";")),$K)."\r\n";}function
apply_sql_function($p,$b){return($p?($p=="unixepoch"?"DATETIME($b, '$p')":($p=="count distinct"?"COUNT(DISTINCT ":strtoupper("$p("))."$b)"):$b);}function
get_temp_dir(){return
ini_get("upload_tmp_dir")?:sys_get_temp_dir();}function
file_open_lock($Rc){if(is_link($Rc))return;$o=@fopen($Rc,"c+");if(!$o)return;@chmod($Rc,0660);if(!flock($o,LOCK_EX)){fclose($o);return;}return$o;}function
file_write_unlock($o,$Db){rewind($o);fwrite($o,$Db);ftruncate($o,strlen($Db));file_unlock($o);}function
file_unlock($o){flock($o,LOCK_UN);fclose($o);}function
first(array$ua){return
reset($ua);}function
password_file($g){$Rc=get_temp_dir()."/adminer.key";if(!$g&&!file_exists($Rc))return'';$o=file_open_lock($Rc);if(!$o)return'';$J=stream_get_contents($o);if(!$J){$J=rand_string();file_write_unlock($o,$J);}else
file_unlock($o);return$J;}function
rand_string(){return(function_exists('random_bytes')?bin2hex(random_bytes(16)):md5(uniqid(strval(mt_rand()),true)));}function
select_value($X,$z,array$l,$li){if(is_array($X)){$J="";if(array_filter($X,'is_array')==array_values($X)){$me=array();foreach($X
as$W)$me+=array_fill_keys(array_keys($W),null);foreach(array_keys($me)as$le)$J
.="<th>".h($le);foreach($X
as$W){$J
.="<tr>";foreach(array_merge($me,$W)as$Yi)$J
.="<td>".select_value($Yi,$z,$l,$li);}}else{foreach($X
as$le=>$W)$J
.="<tr>".($X!=array_values($X)?"<th>".h($le):"")."<td>".select_value($W,$z,$l,$li);}return"<table>$J</table>";}if(!$z)$z=adminer()->selectLink($X,$l);if($z===null){if(is_mail($X))$z="mailto:$X";if(is_url($X))$z=$X;}$X=driver()->value($X,$l);$J=adminer()->editVal($X,$l);if($J!==null){if(!is_utf8($J))$J="\0";elseif($li!=""&&is_shortable($l))$J=shorten_utf8($J,max(0,+$li));else$J=h($J);}return
adminer()->selectVal($J,$z,$l,$X);}function
is_blob(array$l){return
preg_match('~blob|bytea|raw|file~',$l["type"])&&!in_array($l["type"],idx(driver()->structuredTypes(),lang(6),array()));}function
is_mail($lc){$wa='[-a-z0-9!#$%&\'*+/=?^_`{|}~]';$bc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';$rg="$wa+(\\.$wa+)*@($bc?\\.)+$bc";return
is_string($lc)&&preg_match("(^$rg(,\\s*$rg)*\$)i",$lc);}function
is_url($Q){$bc='[a-z0-9]([-a-z0-9]{0,61}[a-z0-9])';return
preg_match("~^((https?):)?//($bc?\\.)+$bc(:\\d+)?(/.*)?(\\?.*)?(#.*)?\$~i",$Q);}function
is_shortable(array$l){return!preg_match('~'.number_type().'|date|time|year~',$l["type"]);}function
host_port($N){return(preg_match('~^(:([^:].*)|(\[(.+)\]|(([^:]+://)?[^:]+))(:(\d+))?)$~',$N,$A)?array($A[4].$A[5],$A[2].$A[8]):array($N,''));}function
count_rows($R,array$Z,$de,array$q){$H=" FROM ".table($R).($Z?" WHERE ".implode(" AND ",$Z):"");return($de&&(JUSH=="sql"||count($q)==1)?"SELECT COUNT(DISTINCT ".implode(", ",$q).")$H":"SELECT COUNT(*)".($de?" FROM (SELECT 1$H GROUP BY ".implode(", ",$q).") x":$H));}function
slow_query($H){$i=adminer()->database();$ni=adminer()->queryTimeout();$Ch=driver()->slowQuery($H,$ni);$f=null;if(!$Ch&&support("kill")){$f=connect();if($f&&($i==""||$f->select_db($i))){$ne=get_val(connection_id(),0,$f);echo
script("const timeout = setTimeout(() => { ajax('".js_escape(ME)."script=kill', function () {}, 'kill=$ne&token=".get_token()."'); }, 1000 * $ni);");}}ob_flush();flush();$J=@get_key_vals(($Ch?:$H),$f,false);if($f){echo
script("clearTimeout(timeout);");ob_flush();flush();}return$J;}function
get_token(){$Og=rand(1,1e6);return($Og^$_SESSION["token"]).":$Og";}function
verify_token(){list($vi,$Og)=explode(":",$_POST["token"]);return($Og^$_SESSION["token"])==$vi&&in_array($_SERVER["HTTP_SEC_FETCH_SITE"],array("","same-origin"));}function
compress_alphabet(){return
strtr(implode(range('"','~')),"'\\","!\n");}function
decompress_string($Q){$qa=array_flip(str_split(compress_alphabet()));$x=strlen($Q);$aj=($x?13*($x-1)/2-$qa[$Q[0]]:0);$Ia="";$ah=0;$bh=0;for($r=1;$r<$x;$r+=2){$ah=($ah<<13)+$qa[$Q[$r]]*93+$qa[$Q[$r+1]];$bh+=13;while($bh>=8&&$aj>=8){$bh-=8;$aj-=8;$Ia
.=chr($ah>>$bh);$ah&=(1<<$bh)-1;}}if($Ia=="")return"";return(function_exists('gzinflate')?gzinflate($Ia):inflate($Ia));}function
inflate($Ia){$ze=array(3,4,5,6,7,8,9,10,11,13,15,17,19,23,27,31,35,43,51,59,67,83,99,115,131,163,195,227,258);$_e=array(0,0,0,0,0,0,0,0,1,1,1,1,2,2,2,2,3,3,3,3,4,4,4,4,5,5,5,5,0);$Vb=array(1,2,3,4,5,7,9,13,17,25,33,49,65,97,129,193,257,385,513,769,1025,1537,2049,3073,4097,6145,8193,12289,16385,24577);$Xb=array(0,0,0,0,1,1,2,2,3,3,4,4,5,5,6,6,7,7,8,8,9,9,10,10,11,11,12,12,13,13);$J="";$G=0;do{$Tc=inflate_bits($Ia,$G,1);$U=inflate_bits($Ia,$G,2);if(!$U){$G=($G+7)&~7;$x=inflate_bits($Ia,$G,16);$G+=16;$J
.=substr($Ia,$G>>3,$x);$G+=$x<<3;}else{if($U==1){$Fe=array_merge(array_fill(0,144,8),array_fill(0,112,9),array_fill(0,24,7),array_fill(0,8,8));$Yb=array_fill(0,30,5);}else{$Ee=inflate_bits($Ia,$G,5)+257;$Wb=inflate_bits($Ia,$G,5)+1;$D=array(16,17,18,0,8,7,9,6,10,5,11,4,12,3,13,2,14,1,15);$ef=array_fill(0,19,0);$df=inflate_bits($Ia,$G,4)+4;for($r=0;$r<$df;$r++)$ef[$D[$r]]=inflate_bits($Ia,$G,3);$ff=inflate_table($ef);$Ae=array();while(count($Ae)<$Ee+$Wb){$Vh=inflate_symbol($Ia,$G,$ff);if($Vh==16)$Ae=array_merge($Ae,array_fill(0,inflate_bits($Ia,$G,2)+3,end($Ae)));elseif($Vh==17)$Ae=array_merge($Ae,array_fill(0,inflate_bits($Ia,$G,3)+3,0));elseif($Vh==18)$Ae=array_merge($Ae,array_fill(0,inflate_bits($Ia,$G,7)+11,0));else$Ae[]=$Vh;}$Fe=array_slice($Ae,0,$Ee);$Yb=array_slice($Ae,$Ee);}$Ge=inflate_table($Fe);$ac=inflate_table($Yb);while(($Vh=inflate_symbol($Ia,$G,$Ge))!=256){if($Vh<256)$J
.=chr($Vh);else{$x=$ze[$Vh-257]+inflate_bits($Ia,$G,$_e[$Vh-257]);$Zb=inflate_symbol($Ia,$G,$ac);$Af=strlen($J)-$Vb[$Zb]-inflate_bits($Ia,$G,$Xb[$Zb]);for($r=0;$r<$x;$r++)$J
.=$J[$Af+$r];}}}}while(!$Tc);return$J;}function
inflate_bits($Ia,&$G,$vb){$J=0;for($r=0;$r<$vb;$r++){$J+=((ord($Ia[$G>>3])>>($G&7))&1)<<$r;$G++;}return$J;}function
inflate_table(array$Ae){$R=array();$bb=0;for($Ja=1;$Ja<=max($Ae);$Ja++){foreach($Ae
as$Vh=>$x){if($x==$Ja){$R[$Ja][$bb]=$Vh;$bb++;}}$bb<<=1;}return$R;}function
inflate_symbol($Ia,&$G,array$R){$bb=0;$Ja=0;do{$bb=($bb<<1)+inflate_bits($Ia,$G,1);$Ja++;}while(!isset($R[$Ja][$bb]));return$R[$Ja][$bb];}function
script($Eh,$yi="\n"){return"<script".nonce().">$Eh</script>$yi";}function
script_src($Si,$Mb=false){return"<script src='".h($Si)."'".nonce().($Mb?" defer":"")."></script>\n";}function
nonce(){return' nonce="'.get_nonce().'"';}function
input_hidden($C,$Y=""){return"<input type='hidden' name='".h($C)."' value='".h($Y)."'>\n";}function
input_token(){return
input_hidden("token",get_token());}function
target_blank(){return' target="_blank" rel="noreferrer noopener"';}function
h($Q){return
str_replace(array('&','<','"',"'","\0"),array('&amp;','&lt;','&quot;','&#039;','&#0;'),$Q);}function
nl_br($Q){return
str_replace("\n","<br>",$Q);}function
checkbox($C,$Y,$Wa,$pe="",$Jf="",$ab="",$re=""){$J="<input type='checkbox' name='$C' value='".h($Y)."'".($Wa?" checked":"").($re?" aria-labelledby='$re'":"").">".($Jf?script("qsl('input').onclick = function () { $Jf };",""):"");return($pe!=""||$ab?"<label".($ab?" class='$ab'":"").">$J".h($pe)."</label>":$J);}function
optionlist($Of,$qh=null,$Wi=false){$J="";foreach($Of
as$le=>$W){$Pf=array($le=>$W);if(is_array($W)){$J
.='<optgroup label="'.h($le).'">';$Pf=$W;}foreach($Pf
as$w=>$X)$J
.='<option'.($Wi||is_string($w)?' value="'.h($w).'"':'').($qh!==null&&($Wi||is_string($w)?(string)$w:$X)===$qh?' selected':'').'>'.h($X);if(is_array($W))$J
.='</optgroup>';}return$J;}function
html_select($C,array$Of,$Y="",$If="",$re=""){static$pe=0;$qe="";if(!$re&&substr($Of[""],0,1)=="("){$pe++;$re="label-$pe";$qe="<option value='' id='$re'>".h($Of[""]);unset($Of[""]);}return"<select name='".h($C)."'".($re?" aria-labelledby='$re'":"").">".$qe.optionlist($Of,$Y)."</select>".($If?script("qsl('select').onchange = function () { $If };",""):"");}function
html_radios($C,array$Of,$Y="",$uh=""){$J="";foreach($Of
as$w=>$X)$J
.="<label><input type='radio' name='".h($C)."' value='".h($w)."'".($w==$Y?" checked":"").">".h($X)."</label>$uh";return$J;}function
confirm($B="",$rh="qsl('input')"){return
script("$rh.onclick = () => confirm('".($B?js_escape($B):lang(7))."');","");}function
print_fieldset($s,$ye,$hj=false){echo"<fieldset><legend>","<a href='#fieldset-$s'>$ye</a>",script("qsl('a').onclick = partial(toggle, 'fieldset-$s');",""),"</legend>","<div id='fieldset-$s'".($hj?"":" class='hidden'").">\n";}function
bold($La,$ab=""){return($La?" class='active $ab'":($ab?" class='$ab'":""));}function
js_escape($Q){return
addcslashes($Q,"\r\n'\\/");}function
pagination($E,$Ab){return" ".($E==$Ab?$E+1:'<a href="'.h(remove_from_uri("page|next").($E?"&page=$E".($_GET["next"]?"&next=".urlencode($_GET["next"]):""):"")).'">'.($E+1)."</a>");}function
hidden_fields(array$Ig,array$Hd=array(),$Bg=''){$J=false;foreach($Ig
as$w=>$X){if(!in_array($w,$Hd)){if(is_array($X))hidden_fields($X,array(),$w);else{$J=true;echo
input_hidden(($Bg?$Bg."[$w]":$w),$X);}}}return$J;}function
hidden_fields_get(){echo(sid()?input_hidden(session_name(),session_id()):''),(SERVER!==null?input_hidden(DRIVER,SERVER):""),input_hidden("username",$_GET["username"]);}function
file_input($Vd){$Oe="max_file_uploads";$Pe=ini_get($Oe);$Pi="upload_max_filesize";$Qi=ini_get($Pi);return(ini_bool("file_uploads")?$Vd.script("qsl('input[type=\"file\"]').onchange = partialArg(fileChange, "."$Pe, '".lang(8,"$Oe = $Pe")."', ".ini_bytes("upload_max_filesize").", '".lang(8,"$Pi = $Qi")."')"):lang(9));}function
enum_input($U,$xa,array$l,$Y,$oc=""){preg_match_all("~'((?:[^']|'')*)'~",$l["length"],$Me);$Bg=($l["type"]=="enum"?"val-":"");$Wa=(is_array($Y)?in_array("null",$Y):$Y===null);$J=($l["null"]&&$Bg?"<label><input type='$U'$xa value='null'".($Wa?" checked":"")."><i>$oc</i></label>":"");foreach($Me[1]as$X){$X=stripcslashes(str_replace("''","'",$X));$Wa=(is_array($Y)?in_array($Bg.$X,$Y):$Y===$X);$J
.=" <label><input type='$U'$xa value='".h($Bg.$X)."'".($Wa?' checked':'').'>'.h(adminer()->editVal($X,$l)).'</label>';}return$J;}function
input(array$l,$Y,$p,$Aa=false){$C=h(bracket_escape($l["field"]));echo"<td class='function'>";if(is_array($Y)&&!$p)$p="json";$je=($p=="json"||preg_match('~^jsonb?$~',$l["full_type"]));if($je&&$Y!=''&&(JUSH!="pgsql"||$l["type"]!="json"))$Y=json_encode(is_array($Y)?$Y:json_decode($Y),128|64|256);$Zg=(JUSH=="mssql"&&$l["auto_increment"]);if($Zg&&!$_POST["save"])$p=null;$hd=(isset($_GET["select"])||$Zg?array("orig"=>lang(10)):array())+adminer()->editFunctions($l);$tc=driver()->enumLength($l);if($tc){$l["type"]="enum";$l["length"]=$tc;}$xa=" name='fields[$C]".($l["type"]=="enum"||$l["type"]=="set"?"[]":"")."'".($Aa?" autofocus":"");echo
driver()->unconvertFunction($l)." ";$R=$_GET["edit"]?:$_GET["select"];if($l["type"]=="enum")echo
h($hd[""])."<td>".adminer()->editInput($R,$l,$xa,$Y);else{$td=(in_array($p,$hd)||isset($hd[$p]));echo(count($hd)>1?"<select name='function[$C]'>".optionlist($hd,$p===null||$td?$p:"")."</select>".on_help("event.target.value.replace(/^SQL\$/, '')",1).script("qsl('select').onchange = functionChange;",""):h(reset($hd))).'<td>';$Vd=adminer()->editInput($R,$l,$xa,$Y);if($Vd!="")echo$Vd;elseif(preg_match('~bool~',$l["type"]))echo"<input type='hidden'$xa value='0'>"."<input type='checkbox'".(preg_match('~^(1|t|true|y|yes|on)$~i',$Y)?" checked='checked'":"")."$xa value='1'>";elseif($l["type"]=="set")echo
enum_input("checkbox",$xa,$l,(is_string($Y)?explode(",",$Y):$Y));elseif(is_blob($l)&&ini_bool("file_uploads"))echo"<input type='file' name='fields-$C'>";elseif($je)echo"<textarea$xa cols='50' rows='12' class='jush-js'>".h($Y).'</textarea>';elseif(($ki=preg_match('~text|lob|memo~i',$l["type"]))||preg_match("~\n~",$Y)){if($ki&&JUSH!="sqlite")$xa
.=" cols='50' rows='12'";else{$L=min(12,substr_count($Y,"\n")+1);$xa
.=" cols='30' rows='$L'";}echo"<textarea$xa>".h($Y).'</textarea>';}else{$Gi=driver()->types();$Ve=(!preg_match('~int~',$l["type"])&&preg_match('~^(\d+)(,(\d+))?$~',$l["length"],$A)?((preg_match("~binary~",$l["type"])?2:1)*$A[1]+($A[3]?1:0)+($A[2]&&!$l["unsigned"]?1:0)):($Gi[$l["type"]]?$Gi[$l["type"]]+($l["unsigned"]?0:1):0));if(JUSH=='sql'&&min_version(5.6)&&preg_match('~time~',$l["type"]))$Ve+=7;echo"<input".((!$td||$p==="")&&preg_match('~(?<!o)int(?!er)~',$l["type"])&&!preg_match('~\[\]~',$l["full_type"])?" type='number'":"")." value='".h($Y)."'".($Ve?" data-maxlength='$Ve'":"").(preg_match('~char|binary~',$l["type"])&&$Ve>20?" size='".($Ve>99?60:40)."'":"")."$xa>";}echo
adminer()->editHint($R,$l,$Y);$Uc=0;foreach($hd
as$w=>$X){if($w===""||!$X)break;$Uc++;}if($Uc&&count($hd)>1)echo
script("qsl('td').oninput = partial(skipOriginal, $Uc);");}}function
process_input(array$l){$t=bracket_escape($l["field"]);$p=idx($_POST["function"],$t);if($p=="orig")return(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?idf_escape($l["field"]):false);if($p=="NULL")return"NULL";if(is_blob($l)&&ini_bool("file_uploads")){$Qc=get_file("fields-$t");if(!is_string($Qc))return
false;return
driver()->quoteBinary($Qc);}$Y=idx($_POST["fields"],$t);if($Y===null)return
false;if($l["type"]=="enum"||driver()->enumLength($l)){$Y=idx($Y,0);if($Y=="orig"||!$Y)return
false;if($Y=="null")return"NULL";$Y=substr($Y,4);}if($l["auto_increment"]&&$Y=="")return
null;if($l["type"]=="set")$Y=implode(",",(array)$Y);if($p=="json"){$p="";$Y=json_decode($Y,true);if(!is_array($Y))return
false;return$Y;}return
adminer()->processInput($l,$Y,$p);}function
search_tables(){$_GET["where"][0]["val"]=$_POST["query"];$th="<ul>\n";foreach(table_status('',true)as$R=>$S){$C=adminer()->tableName($S);if(isset($S["Engine"])&&$C!=""&&(!$_POST["tables"]||in_array($R,$_POST["tables"]))){$I=connection()->query("SELECT".limit("1 FROM ".table($R)," WHERE ".implode(" AND ",adminer()->selectSearchProcess(fields($R),array())),1));if(!$I||$I->fetch_row()){$Eg="<a href='".h(ME."select=".urlencode($R)."&where[0][op]=".urlencode($_GET["where"][0]["op"])."&where[0][val]=".urlencode($_GET["where"][0]["val"]))."'>$C</a>";echo"$th<li>".($I?$Eg:"<p class='error'>$Eg: ".error())."\n";$th="";}}}echo($th?"<p class='message'>".lang(11):"</ul>")."\n";}function
on_help($hb,$Ah=0){return
script("mixin(qsl('select, input'), {onmouseover: function (event) { helpMouseover.call(this, event, $hb, $Ah) }, onmouseout: helpMouseout});","");}function
edit_form($R,array$m,$K,$Oi,$k=''){$Yh=adminer()->tableName(table_status1($R,true));page_header(($Oi?lang(12):lang(13)),$k,array("select"=>array($R,$Yh)),$Yh);adminer()->editRowPrint($R,$m,$K,$Oi);if($K===false){echo"<p class='error'>".lang(14)."\n";return;}echo"<form action='' method='post' enctype='multipart/form-data' id='form'>\n";$jc=false;if(!$m)echo"<p class='error'>".lang(15)."\n";else{echo"<table class='layout nowrap'>".script("qsl('table').onkeydown = editingKeydown;");$Aa=!$_POST;foreach($m
as$C=>$l){echo"<tr><th>".adminer()->fieldName($l);$j=idx($_GET["set"],bracket_escape($C));if($j===null){$j=$l["default"];if($l["type"]=="bit"&&preg_match("~^b'([01]*)'\$~",$j,$Xg))$j=$Xg[1];if(JUSH=="sql"&&preg_match('~binary~',$l["type"]))$j=bin2hex($j);}$Y=($K!==null?($K[$C]!=""&&JUSH=="sql"&&preg_match("~enum|set~",$l["type"])&&is_array($K[$C])?implode(",",$K[$C]):(is_bool($K[$C])?+$K[$C]:$K[$C])):(!$Oi&&$l["auto_increment"]?"":(isset($_GET["select"])?false:$j)));if(!$_POST["save"]&&is_string($Y))$Y=adminer()->editVal($Y,$l);if(($Oi&&!isset($l["privileges"]["update"]))||$l["generated"])echo"<td class='function'><td>".select_value($Y,'',$l,null);else{$jc=true;$p=($_POST["save"]?idx($_POST["function"],$C,""):($Oi&&preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"now":($Y===false?null:($Y!==null?'':'NULL'))));if(!$_POST&&!$Oi&&$Y==$l["default"]&&preg_match('~^[\w.]+\(~',$Y))$p="SQL";if(preg_match("~time~",$l["type"])&&preg_match('~^CURRENT_TIMESTAMP~i',$Y)){$Y="";$p="now";}if($l["type"]=="uuid"&&$Y=="uuid()"){$Y="";$p="uuid";}if($Aa!==false)$Aa=($l["auto_increment"]||$p=="now"||$p=="uuid"?null:true);input($l,$Y,$p,$Aa);if($Aa)$Aa=false;}}if(!support("table")&&!fields($R))echo"<tr>"."<th><input name='field_keys[]'>".script("qsl('input').oninput = fieldChange;","")."<td class='function'>".html_select("field_funs[]",adminer()->editFunctions(array("null"=>isset($_GET["select"]))))."<td><input name='field_vals[]'>";echo"</table>\n";}echo"<p>\n";if($jc){echo"<input type='submit' value='".lang(16)."'>\n";if(!isset($_GET["select"]))echo"<input type='submit' name='insert' value='".($Oi?lang(17):lang(18))."' title='Ctrl+Shift+Enter'>\n",($Oi?script("qsl('input').onclick = function () { return !ajaxForm(this.form, '".lang(19)."…', this); };"):"");}echo($Oi?"<input type='submit' name='delete' value='".lang(20)."'>".confirm()."\n":"");if(isset($_GET["select"]))hidden_fields(array("check"=>(array)$_POST["check"],"clone"=>$_POST["clone"],"all"=>$_POST["all"]));echo
input_hidden("referer",(isset($_POST["referer"])?$_POST["referer"]:$_SERVER["HTTP_REFERER"])),input_hidden("save",1),input_token(),"</form>\n";}function
shorten_utf8($Q,$x=80,$Rh=""){if(!preg_match("(^(".repeat_pattern("[\t\r\n -\x{10FFFF}]",$x).")($)?)u",$Q,$A))preg_match("(^(".repeat_pattern("[\t\r\n -~]",$x).")($)?)",$Q,$A);return
h($A[1]).$Rh.(isset($A[2])?"":"<i>…</i>");}function
icon($Dd,$C,$Cd,$pi){return"<button type='submit' ".($C?"name='$C'":"draggable='true'")." title='".h($pi)."' class='icon icon-$Dd".($C?"":" jsonly")."'><span>$Cd</span></button>";}if(isset($_GET["file"])){if(substr(VERSION,-4)!='-dev'){if($_SERVER["HTTP_IF_MODIFIED_SINCE"]){header("HTTP/1.1 304 Not Modified");exit;}header("Expires: ".gmdate("D, d M Y H:i:s",time()+365*24*60*60)." GMT");header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");header("Cache-Control: immutable");}ini_set("zlib.output_compression",'1');if($_GET["file"]=="default.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('+c0=@iDWB2P?H+.Sh=;^l:{,zS(WKASq!V)jBmWb?2$th#Z-Ku]_;>Lan<jnKeCU;:~t5y5*eC7ehsBn^yDY2;h.o6kG`I;s9C
)r`$=4hC#.fpJw%?YwL)mj-qNj1#L>,CO1y.amH0(RpCZ1G"yjmkq-_R-.iBdAq:6sKYqdsMi9p{
;Dh5b-]C[00Vuu~-r].#d<y3??SZOYfAs^[*sI*i3FAx("`^,&jKhl`PXm8rvI:]pZ^n40+0sDIxK.O?g3[wmh9(BpHB:oy4*3yMo!0/G2+c<KpnP[2^zCJYKEq/M@<I
d91xW})*U(1VbQ>hF{sZ:v;;4p*/Y6kxKmE%fSxhy
WvupXRo985$6.$t1.o`SEgxt?2]J_t&!m
?E]Zt/S7j_,HS[kyX/]xM0Ej8@GVFnKOjGSk
y[%aV/THZsqO)Z"R71/qZCM!|l~@kQwFTHQ%UU{"/q}>~(V$z
plAor[*UnR,dC4&L;+e?"]_a0o|d1GWOSa]03EO8`.lu`8SUn,(V2:CW"D@1.[&&R???Wl)G;rzhl_bkj*s.TL%l%I~?)=eys$D;!u^;)t!opm^d=bYQ2gf_=Ua!m
BU*,bQ)=omlZD&@eU6vVX?b%0.n%VS]GbVb;VJYrsB>Si9H;.He[#,n8Lg`K!`<WU;!GCGmP)&:>b))?bYiB$YGoJkl*#$`3+owpU)$(OE[VgI2*+(5Mgi(D%lQt3YS..0~By#tL6it7ta>SC..k{uX3e!U
b-.V"Fs/xILp&5s*9+U1#;Wtt0lPX]d=tunSSW[3+ql+{$RV|l
`fci"x]k!I.;Y7hehmY1?ss%ox4PVnT>%6ttbriZGLE2*Vum36g@4(-Q6GQ
?Ann.}2Pc8T0GwD;Y|l*UyML,N-x4Lg/)9YL<e&,518>!DL.Op[(HB$WhkyuQ6pP#oNIr>o-4<i,:/ds8sS
?}Z)wyE46!*Y
[*:up.)%Z(W;#ttO@Sws~L8,9DT6?O&+pF>+eIz4+t|(m]8E2ODd1"sPQMp,pFXd0aa:AXYfQ=0K$gm8[4--76%e!x"yy#|3TQu%nk(E%;{>g`ABN^r[CEK]GU_-F,bSnA:>yO?X+%
n#_Da(KuaFo1KSVZ[5M9YgfdGF0bN-)j`$E7v3RRGO#C-I@J$k-c]P1,mc?d0lm,"AWQf#>&$Ty0$x"LMKEDVb%Chy&<p{?lBx<8F:?[uNA$b41&;*EP.w18
"O1ep*n3Q+7>/0}x)y[wj<FWXOS7qi<ry,Nn(&>UV_*D4*i#/A#m"5*#/B&4E;[g<tAenG5,gZfDx5|F36NFnN?6oWK]"F}p0S7xHER;:Tk8{y2irFYxs7S-a>AfC+0Z6]DAAk80k*b<;-}P7OI#+oL^<D7$u/Sk-E%6[dw4j;pKLJ<6v#"?@]28Gm-pQn:#L;DGIqneW
P>kk,rBE|anI%2g%Ih6%KO0[CqzV2y(R+E!-&Z<&{l<[8gxP;-[T#=~Jo6c
&:9qs?*i%?7[j/,&=[)gR,31a461N4`sC)L9lUuh"3`;N;Go}s@6Jf`%ocVd<1bc
<2(jIBa*lo<SOL.W>UI2s<gfY8*xoboIM~RC!#,AS,FoR/p]3wEkm;Ga,9G)Pf+k
>7/VN!<tBdkcHodco#>/,[+DU93C+yJ&qoW(vkA$PNG6c(]X
/U(`dMKoW?*BBndz<2yS)n@ot:kFsH&kD<7S^%syOf$5ASlCW[vNq&CW8,lF0L:tHVWKNAr1A9I`]O`MJjNaP}?_3k"V[u6*/0
;1"I=RRs~F,5PnVxv8]?[9t:bG3:M>bT^/E.d*b*KV"h4p`9Ynl?j2WJ&u6)(JqcC.IM&"BX}@FTmA@A%#q$#AJlwv*#Pw3tf
X)]aNskV4H#6Bku5iZjHj^OdWH:n~N3-!7AK-H-6`h%tfV$t9vh=)o}%:=(?Qq^W{k)_#x?su7Ej,7ml7AEj6"y3#4v9nHC-?Hqm8raoE7g"Y(jE)Q/KwT:2{-G:b_uFDRl2bNK@:^{X^$OGv(4+DK{^#TF/;
a
#:yRz(<TRXj+ZbRV">7*,O
@UAr8E@=[`3#oD-?2Rt@n6u8$)&X^APDeZ_[;!EKqKswXFWo_i*4rKxA*E,/N7oD
xSxqj;";X,>Ak=p&9JgTTgVkaq!D%9U@#,?X[jmxZTRyDweAIx)W,qMm:WTy()UQ%jgZ-x37ue7kkq.@{[jq1P^uhnSnhy6WNyEmpPgJQm^Go23m_tO,Gn:L_B^wUx"a[oFtLM/n]c|ecn*yVF}v[by,ap5x{5.9F1%]EEj27ypKkK&KlFQ_,b[yve0XsP"cf2_*/qiuyLrIMM/7_lI]zz)0;MCFubqBp_bQU7MwVy)-tr%Q
l8WUIbF6)"c?SFKIXwIZZ|SKm`A.</ancccTY"w<.wi2l:_T&lww){p5CN>wGo_~Z"_$([qgb8A=H:XwJS!Kji[ke|D&+fTN(aS$xayFy=0/D=mXXgeDh2]ne=>JH3ATi]fj:;=N++#W]<4S`mXsd<Fhw]b1)aMt("Iv)&^6xo/@R%De
40s.d8V2$0u>*
5LS5BrbEvJB:G
QrG=bQyo2hKiSagWBC*[03iq@
;8AG{CI[vgMbo=&]CUH<2J#$I)1Opt;
6T_jLmdf=2?`o;ceXMwP>-gZ#179]@=tG1y:#fDl~DPi3@0Pjer:?BMi#3O-$sf6Y-10c<%b.Y<N]q+EwBy*!.u)_J/:$1ZjZmVc;jAJz]imwg,Qtb/S)/lX$Re17Kkyi`5%p*2)Bc3C72~KdCcBLgteSrJdei-TeF-_jdE>im]H"6GkaM;`]=iIA1dp`5NaSeBn%PUU.T$cQSlj3E8A8X#K;/ZT*D?H|Z3_vxvQ&?&OEDYaz0f8PA@]U/
HrU3]~c7hieBb{7/crTVx%/+5-*@S12x^^
Tv
NI5,KYeq_~a8coh/]c#A&jg"
&,b-vH%!vWf)Cd?u$.4!t;luAGj,?6:4#c9tp+AX9^K-Tsh(]mx9)5m[0[6`Y[f0AU(j(G;#Lu7;hN1E^x<Jw.H?@^wF*
E+>WNXA0zOIN%9NBtef`lb4:]RLqoc4u4UX7}S9M**i;G,WqWJil4oFtPVS%6d&,IlU.BB&6v0|ojAX#bG/)sV6mX<7,(YbjMSVB?Y;@XTx@>Rj_x9I4yB-4pjwniDT2``8K1q$fi>RudNs1(kY8VU]/x..C=[7:.o]KW?L^j^^^5*>BHxaY<2bRZ4,SiA:c
cb"61L;(Y^&fSkA,:+o;TAcVL-8ihCI^:Mm@@nB2.o4mYJK.Keb:h=@a?=V(&@/|rn>})es]IsX)Ijwn3hT24#Y@j%5x?:o"m@b}V#ncaM@*Kt)86t.ocwm(K5H=0XfdV"&vit9HOnYfYxKT8k936(jg`1`G%SH83}bA_s,&>u=ATHaA^]*/`W8D&&*<Zi@K]%lo"S^TQY%&yL%~("8.r{QK3QqW7e+y%(=)3`<CbS(mFiXzcj<i?6ql@nEHXf/eFxM)>)a~Nc2=)LnV^?4J+T-T-vw#5vt-RvC_*MD"d$?u]S6sJM2^-=0;xuAOQ>P
?0v|T/LfA,1P[ZK^[F3o]m,1LPkT`@,+R}%<RG&/l7iwR@$/lPC,]9sw9+6L<c%k2S4}PJUULEfX[kQn<"8gEn("A1v}M:;,tCka]2w1"q]]wj2Nd[x^TIA)G<(yw.MTtX');}elseif($_GET["file"]=="dark.css"){header("Content-Type: text/css; charset=utf-8");echo
decompress_string('%OsbOb3V?!K0U*/_K8!!_q-f@v$o[s#eo
DBus<$lON[d8].,qN*m@-poH?R.tyR)O-"TS%KHdM%X3Y.^#Y_hHfaP;q*c?klD(H<QI%Lx,N=.GQ1$T#bV4tq#Nz%
?3w,vbJXqxP3M+9rv`E3"+&ta#-z:q#uH,gsfr;30Y
YD](8VM$vfC0N0c@>"2)GKk6Or*glU2;_e]
lG[qCHj,)N{dgvz5Bxo,St/",-!<m&YguCEOcf=QW&YqSe0y
4tMFl.S5
Q&<3MG&3I$EKa#w5d>?0G#ziI.mcH[Y^xh]n`F<v_9TD-S)yGMvrG^6L=^u]hu~T)XX@%sH2|^MUMM~_O=Lb/j<P[#8jzJ=&?+Lf&PJqQq.7RL#R!3,OUtS?m?2iGs<Z53YxESS/kZ&u!9n9m4#Z~@-oU.[]>QKtIKdd<Hl)Jt[fL#S_x"K+ANa$N"-vrNv

s;AjH"K_hHtyTN0yfA1K;/;F1cD>Dq?!D}QS[!.vMbxL7Yt*f95&ALDq5fB7EhPixM9U-DAhYDVZvb&*VNnYm(ws!LkSX1hT*rf?L?8pLXxiM@vwMc5+L:2N1cn6*pvi+]b-Aj?Ky>:w8"9"eJEDH7YPC/H4sp2BTlYJ4)>xqL$7MHqrU^F
Mq,+cY]Z4{i1WT_a+CI<OH2!jmJU5RI>@LSbVBv>m0_UI3wJw,DVQw*|?w%hHQT(fAdA:}Pu?y<E7VZ;o&F!SfCnKXXmJG3b-0(S1ciBQRFKE$/KyBZ-x~Y&F6),Vt<HA&pAezJiGbD]=9MElur7.Zf,Qp^{f?%4w+^N,&@4A@$CRbNWa
BpG&xQXj#<V0ho]GUh(F0y)N*XAG09Hi!8MGrt3p7rNw=qLDJ7BG1f8rSCi^;*wx=3-B=cetZY63!qW$uGUnB%^t^=7NDGe@tP');}elseif($_GET["file"]=="functions.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string('*hcbOg~ZSGrK,tnTpUC"nSgx@i@Au14)aq,?AJwvi&hL-=aQ=P|#W=sd(@=1d0#u
.Dh9L{3$vyM[!L&}1
o)Z&TjD5G9jK5-IUAr/2utCtE`FD<N4e_mc2W<c60>hiMM/`qL$1QlWFAYW#hIG_m1*ub?EHk)ZQMp4m_SG[
{GtgNjd`{cWocVqWla-"!N>PM:dEf/.(cs/Tvc.V
t}/;D)c~39?dv{Bsj282IuM8,{G;"WQl/W_eX&aD)k5_y
U$czc4T3;7hcQj>yJ$-s;U%xdq6+SzkhO-Hnk]%Qq<5QZx]eb[O4Z6C~,~Q~s.K6Q}H;)71&lMM{o-Hqy:^9SK@+uAB"OZJL3B`Q3RE!6N+4TV,fIeY#Wu<^oGnRYUYlh@M
y?`|><Qvv/DQYtSWb&;LyPgjDR%gXw"j+mB*!>q:Qp60w&<WL~r;gk;_J.B/`FcQRZ:6EU(jN<l*tn@n)5A&d0i
rUm3.+_nPLoa88x-LN_OVr0~Z_JC+&U"(M?Dt&_Y*%Uf_R8qH7:["om(P&iLTuxk,H/P!HZc0wrd:^cw!bvAM)wnlI"SO,ZoETvlFH^LDa@MvlN?yPFmWlmZsNk^q*X=[zLwGNC%`-E]_NEjvNO8(7)76+JLJy-8<e]jBhDO@2xC=7R8TM@6J?u9Qb#%j{^7;<IK#0W<G|pVKBWgQ;yLg]wWoB*^@%6VxLb;3M$6L^)^RRB2eSa=n3,E,i<L>4@^3v=q,^Lp0x=k5^b}n=Ie8?v3r8fR_D2wqj6mKFyyJ.yQ`3E1GWk@v_BXHBL&`}U1g~>-f;La*{%g($PUor+!%UXQxN_)lMc`b1LGmX`uP+g!,S=n>s^yp~0"MR/6PhTCn(<ei+Wk[:Map~q.c0"A
A=WJ:JnwJVR
;PN<(H^&J"{4(+a%A_>_iZk(7<>twY{!>_5uvn`)zn7"..@y2U,#Y*ri^D3*4rwgjvsG=G
K
KU?CiYQhtubWZ
mCZo
h;D-BobX^u}Ce]Hs^iGfYZ_(DR/ndSj<"#iMgoIdP!8Qv#!7f)rT}NF*"NGc=EW<SxQ?YTP!{QkFpZsO$--(!!z*?R=&M]D"A(nL^h%eXO?MVb)xb,P_G2>EE
}A+T*s`hRd[No=]@>e"PsIw_3-}UNK#p{A+x8V"Og>tQ#>NYnEh&_4^Z2jUvVgqmPVwB>kW$lUOt:$qVvi?xY/k(65OPqG)dQ<@3xR,&P)+=[FN.Y]3/=P}dW4ruq>L(q>->9tS/eDEDlLyp}Gb)317pN5iApal[o-2&][jcnfv]/mYbbSN4gVhSU"q9!]+R#g`oNn$<e64^W$7IRmjWMN3+B/jo}eC:$fi:hPbIS>R:_oO!;IB[Q?=PYO0MceXC+khRL0!`$;"_vtXqx)q&dCB<k#[!IS2]n8uC&$|Lc$/4e2Y$H2M(*X$)r/Y2E#n9b*eK]v!3@lL@|yxPpGa=oi1r9JM(LYDR8md7-"5cIMyxRupBVm7l5->FpSVGsx%5T3aB,
"=)&785(~IP([%Q(MF8]<*5e:lD9Z6yGN-WlD?""i-|
Pj[MpVT_;[Wavge?K1}&>3uOI&_
]:SS_K{-n@Cw7u_uV!lw:"}65q3f8taY:sp6]-pT$f~L!2`3H8=i|T}KuBVTH[jS2n6"Y+Ru>.l*U7RQ/L&bNcAbOlHiSsS1b
Iv(j#Uk=Gi:":$pHNLb0LgWviMY0J%.15D]]h%,b[8q5fOO`8Vs?XF-pbs7gvl!8?]mI~io8/:Go635o/Pek:Zr5bGhVdD3>v:ZInby5+^n4uFTkcE7M{<R6<3F8^7-jJGXfDw_lTY2JsY["XWX
!OFHqdDwHkle%PL`j))S]oO1@n-,h7RkpF&p{oDj5vluaWZ+i`EBIw|+@wB0V3kJ8X9%K&ko2<egaHP8I&x5AOt63:"bbNufQPfv]#C[VQuio(MQ6[e5dYamQ^:.L-ctoH/[AN(1JN.DUx?E6Rs=<w@25$)#V
Rb!17>UXQ^&ZSL*]OAgch/phl*)oEiuwE#PR/>[NyOdC?p?oml,:"v:0y=]VUyjZH)*ZqVr(I2a2_*PH<<t*8J0I?uoT@/$GKJZ.]fO-60`@G@O;lDjXA-bym
OH)usO_ZsVQtL=!.d9?<_CQqyh~HI9q1",u.st#Tb!7rHGg4"1kZ=n}vTh_riA_i?c5K>G5[E^`,gDr3EDy&#+GkS3Zd,E&Q`<1J[XT+s7E(?5|Pl2?X`VWVgwkKQdZ#IFJ_Th[y#nwmOWRX~mceTF-<T,S_j#n:?V6N
c=LhNYgREbxRKDP^Kscc]@d0b0uyQbcZ$*[qL@hc-Ai_nocNGLmvbq`O3YQ_=cu6j$xKg8[_%sP$Z@rC&[Vg5IJX"H&ow;p:mO`9%YDK&r-1-d$ompw0T&IZtswB8!W1C&f&6?:<na?y7>lUViBZJm3CFF$,kUV#UzO,9n"j1dk<I%m$sda$!Ta1%w&4YAc%])mQwgoh6xl@FUGb6&
p_ZjaCj)t0:5$!1PkoQ(T%zu}A?Rd
/dLn8!_JS56"U%;avgyU&fu#K#$7zCyk1n7KOhq7AyW@Lj[$q(noA,VYWfo#w%7b&TJ8r1_3V>)PCP3-dt>X!A|sdMBOI1odddY;v_F^
C%!L^kYV4`u#I])b[u(,?gLp
EQNN/D6I/22OA?u!FnJ[KL%ZH#HHICmia7lk|@)9--I3f6xL.I
whcR_g[t^|S)2h!;P2R-_i)CWVxM@?OCdMEvIqADhOJQkb6k=9e_de&1-~2nu]d~XN1BZ.QX-{]Y$OIJ[@2!h,R_">Clt}X>,#b`Kmpy:p?8bBN}Eaq7dKCoPVgwxP/8*gTDfL1z*edbM^<Xsj.G$x=YcFxZUQh-D0)]?$rL2DBLh5/F*7Sjke/sTdWR8Qfz<S90z!VR1AyQg(]n`UoLobr@w]HPVU5=HD-0=z#vwcMb$7k>/YSH%&]U5{jFfu<4fFYLu[2P8mI:"]qJD~Gbj_nV2h<j$ud*e8q*OT8Ww2V1L
Hr/?Jj>UR?VZ7:ve&e#0ssVN!)Y`1ZQRl6)Hx
oU:%6Vsg-Id.?va?A7x`k(aCu+EE&3%w8
p[Wa`m?G?hm9JIyi#A,-qokLI9AQ%*[V
=EAm|kVKu]|IL+:6
UVSum?[c,@GOsJB`_$"t/{@Z@{r6409|)Z1#l9RSax72ob4^k@[IKq$cb<?C=7NcKe<,CAnr
%rtr%>>%v.]v:^QtZ-ACm5v
&>&`Iaf:nF7heJs454xBFBHt2`f53:.od>W
ns>AIwDM"dNJ3y&!]DVi6C90xsme:c;Gu3"9)2,qMJ8r1TSHV<k[}C>Wuu!7iw">^UA_-prB#:ew"
q@FEjt#r|flmYy8j!CMZel#(t6%lvF4o]GVxqn&HRp|4RGn*$<d$;jD`fa3c+x]x9-b:W95[G6^p4o1U7`n.Q_pZ1P_5awJZtNLd2IlTbBYnly!okK|ctT{%O>z"Re08tAM&qOv<n@X`@oH9kq[R47/?u6l_d=a+n<W57G`^Kl
Y7D8(N?>]:%$`SL
51g|"LgKMMw3v_9JtB0Ly[;C9&8QT^-px)Q|4<yWO-N/[qWAba6`jiC5-69LrZx?H*i4iCPYiFHJ!&rZ,qj~@AX)3xKwtjq=](?UEu%XO|_Ww8?cMr`(1/w%XmvPkBQXLw(0r]@,w0Hx.v;5_.^bw"KVxK514>2yy|y:It9{dC"U4=H/!6$zbG*=^P%+s9:Vwck_<1&3dT>|T~UoJsM
P*bgL`yS79s>h"=`oAxd/YAo^tUsju*.-f1(Yywbb%lJA3!Rc4%Y+}QMax(#_eW<FZYqLa<409s*6]w5eG!Gtnt.;@FgF)DsHd@78,)9gA
7dcS3Z{d+;@B3_yL
k
c$M()qNM
W91Sqg4)LL%##W5U$U<rJrqr*QA%!9UOSPkUR0qY(lQK>6FP~h6?%w#e%H+?s7#.n(k_C]He*lGWY=J6vq}cM,(MtT~K&"9#*:R>xd3u)kgJ$fn2V[YpwTBZ65S(B@[v!ti!~^aW+IRy;3m2Sm?@>#_$]72nU#aR~mJ8B]P0R(Dvhr*`/I0fY[eY0p?_Qe<5"`T:aD=mcE2tFtce|yz^"%t/DftQ0*/[G-58uN9hpwqh;RLk2@?!4I(8r*R+u;Uj^LTwI7^?,pfh!A4S
"8[Jt<R[0(o|pv$a9V:-H7UNxJQAdS&!nT"H6]3JkfR8nAp36"XbH[8coaHzZ
%-dU=0gii7EE4~"j6I6:ql."BLcYG61|.2eMu%wZHomT7c#rFZWX@
&ky$2>3$D7GI(ayW3).XbnpW))@~,Qi,KnCh@96K)J9!p!+a8%/1r[/2Mx#m(H/F&.H]r_CYf:KI,u2P*w!yl1.)eCIFS6fyEZv?aVr.)GXR*WUKV]ha,*5[(~Kk.@A$E2"8lZF{I(yKOvc3"
<XCsIObKs#:~"(y5Q3oU,jHNhs/g&ZlYE?CD>%;S"<KUdIkf.Kq/wB:}Nnj>Dd[Q^ZK9utoZ)"70=)Z,q?R"._JNWO4an][GgJ<
V@)Q)sf=#l8xbgQ[[|6MG"_cgc;l@*Z"=ofpo-<B$V<DKM9lcEV/4gt4i#r7.NV$DqRb_R:xJN&>Vl,K=.X2I?Y=*BT9lE>{<v@#q@Z7w*0N)1Y/DEG-)X-^#AA["d05;tfrbd+?.Q8XuW@@<8q^lMiq5v>X>G/^hlbFO=v+
9+xw`-FxpS->2+?KsC=<t)/=EB373&wA^@Fd,O{H2Cvse7<!ZsGlB03@dx1<@ie<}.)QVmEMg8"jHG@4L`~qyJ`O5*g^"@$"ngdYr?I+d>.5Tea$Y,*E}JH.%7m&lNXjjVJ1X8P>u!90xce"xQgokpN/Hqar6WU<GiscV%^0No,:R-Yo8YD9m7}a_2A.SBn:L9I31tu^rha8;]._G&h("Reei=?>}6;QFGQ0Z[@7pyGe6?UZ:R~%[!h0O3E8P^Xc53za-P@VW_e!<#<gB(gmW_!4*_Et+mo8RYs)<tyt?0yABXwyZo+UZVkb94~EC22R|OHh=dhJLqo*GNbIhPH!}<V#8REnu(ntxX-_|c[ELBghCB7/S2YY5(n
9f$8caxoNFWNmIrZZ;@+/7#t}ICS^Y1G/U$
F<yu04AOJaJJ>=Dj22n,[.KWZ$BG8t&LRj[$5s;QEN@--.%evg!-Nm_1y
fdpTaNo6W*i,%($b[lz<Z7Z>1()ubPQ+CjdQ:9F&1mBAp4no,ru:kJBaG%WtiRD
jkg[5Fe]0m]v)X4p4AxOPBbOL!?q.oLc}@2xNWrV1jgD,@zfSp+P`qk.Aq,#w2H@!6S>l@FO0Ftu"WJP-P<fW2r+et_#>4EU
@/o%A,!v$3D:!Q
Q6<@|My6C&T4jtN)Kb1o:utHI56Rr>VnldMJ=en=A_*8]Sbh0W6ssEZ^~4C=]0s)+blaXMl5mSQy2t`S3mGf*^,N-h;W6>?m8.-goKWio+O["xoQk/8.So[@zj;&RE0<VtYAv)Zc9q<&a5Psuqi#kj{_dVb0Xq,Tp
gW!K5kMM+UVoo(g`a6>fW#abE!6#GWIHuXvku474|u0-K9-2B81Gnp7huLUf;JR#uB>P2pTQ<ZE;#G@k#.9$:,q3@BLo?`C7m8RkL@p@GFXiOz!l9QQUn#]i8`_jc;i0*mm-MfcXD`-26Q6/OopY{
ocv3ewLIz/#
jL1*C7u,9VTt4;^e[3GgteoLvZJy2VPi92_YWjSp=Z*=I1t-X+En*
oLx=9fjbM<#6N+GVLfb7)D/6o^ktdRx3?ggF|=Vkq2=G/cr8=%w4iN^`p_}!:Cy?JSxWDOkfMCZVpws<)R9*MazkYgn=&g?_klr`|M4V8Cu@Mw<B5<b[f%ZQA7RdJ^F@E!Fd!y$yzwIw7`RANZIyaz)o`U>Z>KL6Bvhb:h):*UZw<66yyTBz%*-chD[]}J8qA"p
ihF8>IISe:P/[JPHd<d%ggDGnVae|^~HP>.i.C=>"`)a=s3oES2`ce*6e%~DF_85N>`KnYk3;tWUOIlCktBF]0@GDt(uDPf6q0*3fyZJxN%7(5"M&DOPt^xw)OjdK3%LBl#doK%E"^a$3bKC!SJSw@C(UT`P5q,c[riVzBovSW$13+`8RX]I9)m^8d`vmOU&>SOAG$X5Ie-/rrZk?el^qs[w`3.V+m?]8/ZsKOvdM7$g2<UXarO^Wod(
Q`kG^Ygvy4
ig}e$L
h>Ag*w?_iqYCqX*;h)f>2WCwR*x*4xxIC0!iO|*ji%_<M5Dd&DtPV?WMF!c3me:n>nFn!B
bkL>~>c`4?,RY,JH+=t>^bU<EC03FS0ll1~(fH*WxyvYV2HyDL_L~>Tnc<SWn
0$)rh@=-[8$"5Iv>ExP*}SnN_yiD[32;jICSa3`aJsM."d,GGOu%,O+(X-#cH6f!eAsxgDaLvA6DO$!=;.0#=myh#F*[h$RY;)pNAa;+z^c[G][_m1335CEc^RO.PHciYQk*~eyDrO|O~&8r<oE,x9Ae&NG&"waNbDVKKW1#T<C]Qk0TK9v9t!F[rYPDdX>9|*!F4M#ZVcGI}ot[m&^CA#5`fQvHU9v7[FDF,XMG.i#gfPBD._~RfB*cKT-p-_J?pyxu|n!`,-,J!h}s}BM+AZQkB:FwQ*6IFguV?FL#$kjA#C)iUv%+GwtFg-:^9D^g4:D>TQ5EqTgjk,us*s-8$P_e[+jH
HodDUf:VXZFtjJo+rVqgk<Twib/*CnP%@lP2/6/1%7cu?`qA;,i-H+HCtSb%!YCKfH(eXdQ"]9l.
b@HtkN_PdQ1`n12_dlR[6nURroCMzCBhH".XS0/`2BiVia^E}FpRrP=N!>L[n4^X0NO5e*hEXLU]W_DIc5N;x(@!1qhH8$p3Kd,oU9#ftJHHx(KXe[OsyFcW.hl6KQ
OE6h;3/NH<IlEJFp">]24pFu23,GJu^trfRi#X=)>"1U?kr!k#X<ITK-c[5w+l&TWu4s<XJ8_W]w+LFJ<IZACA(f[Z&J+g%CV^xf!xR>TKZKH&@T<V:@^KBGR(J(;_FJXMW(Gjg=:Tm@h;>JI"L5nr"fL|$~-P%;8$-:-0RTHOR|v")69D"B%E;:SEgNtUmYY_g9NpA:JS1X&qAJ]xnIa#,y.mgW0LWrF:s}9-M!JTy/^f3-II9UWpW__2L7^t@(tHv;n5s2!/]H
V]>,+b[:&t)eu.yqgpU1Y@2TTw*8|MXvL5"/7b,VVF_AotmU!3hRC!`-Z(Ym7BOsu8*C*I|HR#`?}WBSCL#wWd_4r(`E`ApcYZHZLi?+!YbLn(0.Z4xLxZw7KAe/)6Y7Tvm/8B;KHJJ"}=,mwS4##kCJW)50f(.vqk2Ev,1cxIowrE]]*Cf7;_gBlZ"+jm0iiQ$q{1A2DoA>tjTZRs4+-:1W}"d.U)d-AX1
~WL!}j@vUpM0q0XInBObxUWM:CU#,D7#(6ieqiyiCd(,?#twIoZ"XfQ">c$5S3&Qi*q;N!2nQCgXGou:OIV4>Vn)[A@_H8z;h$Ho1k~:&KSh}aJv)O~&!&ng;^lYsv9-Wa[0#`<=73J6Fso:)*X6LX^5}K&/1YUE{E;+`*/Hxc)4xHlBlYte<5V!
^22pNMYU>3<;oxU35#7<uMAN_wG!wt/7HYNYl}0Zj5-{J$z&^de!,2%Jh|[{^dyE(b+Q2%P];SNZ7eZw?2n8s`!K;GYBS/T>Eqk2FE)1FjL}ZSaiR$9w;;pqiswJgc2zYvDNa2wW46tq%inPQM"nG:u7(Owy@.C`-"_T-SWIfF>AIFHQpt@
@j2[-
0V9*D
l0CR6d/hLMyDN97~r}/Lt-VdEtFLvhG)Csoe?`[V/AHjVic0!7:TC$+DAV6<)PG|0msR08&Qmo1/``A[,$qp^WE6dN[0eYH=lX!o/+*lfOh)$&tfVHg4=(4*ZoMObJy#.<dVjHy~41tpogC-iMq7k*JebE[Z&E-2V3K?+!D,$Iz)5Kw70-=)78vU[s:z*U=|gp9tJI)P&^2i`<P;rB^"@t)K)Ru_Z<KJ3`U"gk*-7e/6EPuvfR!u@sr
<Xi+pdmFkb[eKS!3Tik`mLUDbF1$yM<3jch#8"Z:gBVG!EtLU}fBY*,<C->ZQ8lOfF*<3f:[mkKn50ue"E#,>;>sNfAq*7Bf[o3%&T%=dFqkb+Lm:rrO;G+T&4xfVGm(cX7fEA]igLAf6=1MsK;t#u3).jD1e7x!G|,Uq4C!8zFvYKiFA!fw_I*dZ~v<F;U0`//;vpvw.v[aNSktLX$t0)+S`*=ai|7[!Tu;uZ<lq[jAW#=,<_Hthx,A4
qa&p$oo@s&6]rr`=,BjobTx7N1.2S+xR8O)9lKh4ANWKe7j3r8gDBj+]0gz(nMfCwn4dW>a1*B*S<ne_clj$g@S|V^pWq]"]1tT+(!cH;?1C[.mxl?*{am?zA:!4+~1X$c/cG{a]tUu@w;oZ(Ag$8wNmTJJAKzX*-"EZ.!jAl0l[9]&fX#j-9*LhW~h~StZ{U]E{VG6F]!39?_&|9Gi+-#`a<y<!T8N`Z/uo;9;&cSudlc5cboHLNgHcqRls0u_`qQE
1-#B&0p)4.lT@lFzC>b0VO#Qan$w+-HYu{8E_}r{VIN/MQH@vy7DT4f)X5V"<sk{*Qr-jOY/:j[9H^`+kSJ!WD&KP6uCVX;wCnaG!j2Cks?T)?4yMB)d<rTdej&RH9MqQo@Ksje6^!u92To4:u:#,%@]HcfAwAJamddgmMMsOe;4G_lQ6(-[CUxS`3mQZD+zu/8~f"^hJdt/S{O=#u4jUtdMa,qE7;z%x)E,0._O_HV@>Cc9hAv?@&$MK+Des8&8whAeJjWaL8w:VGKla`i`fdlK]DHjG;+LSMnQj}!!XL,s
xtpSFyVfYb]I1p|`?rR7GTvxaxbItM:2Qnoy7e;=)5d[Gm{;e,(=ly8w=sgk2XgjxUgO/>i*sw/k8WbP2Lf[$GQi_g@Vr?9A.".cL1@4`M}!exr])@[=wMF=dK^l!oNWEYvW&8Uq{92E7Tu3mx5[$Ev)~c,#/_rcfHB3Y^M;s`[iFQo,%qAc-p-lag|ls[#:L(r.tS,/P,X$cjVFZ"(Uu
2*~yYCpY>8,X,uXKhBZ99m444B1tvvpwjCB1W&.LPy:uqcYMN@*s.(f_uy}mX#zd%nMD(dGu6^TM^Z/r:F-wM=.F0cs2_$.z%HtEEAvUiTe]F-fciw]n4rLC%i@p*hfc.NrvJFu<Ir&1JOp]y,!y|e*7kQmx((-C%sXxTP,GJ_&f0I6teBy&%=)nV8XgrXaLd2[2~=T_U7F16aM>;k{AH(%p2#%(7P;ofRCY..CN,,v]G<gi[vfBYY&=:tSz"ghtW?kaqc?yA4BoA+JH~A&b9-q>pNSHrM?7!eiT
cw0Bx_A=G/>p@%bz"!L!_E(1JiW[OED[H/M>an8#svP_WSXQUdGrq}L[e&@*H>c%vdIQ?6toIq=YuzB,c]w_m;Qqj##:$Itb
qAIx%Q|XA8n60;eo/s/0Mn&H4,ISo
(v=7;.A
{x&<.asvm/q#5;YJ)UV?%]xsYvIl}WoB!$_XmRiW:O]jY6aS)^A.e`&Ln9a.%*)LzC~SSu4V,sCqPvQn"YA2lDfn^XdY&gjcUZ.z&HSb|dd>0@l=v)NWwMh9r@4up"I-?q-E:Lkb-8X"CE]7;"^dVFt)Q4o[i+|dae>ds%~*Al]z$TzqTc3vh#w;s*7oj>9hpgmQU.}C2N>L2ujH{7F.pBDT`)Z$<n%]3P23h(vw:Bh3:@cqtxj$d$5gZD+!dM{d@X1O*WcBLb>r#pScX"bHAh$]rl7[VWTv6ALR1hN0lr%H)jBkz9]wy=V2nZtP2;++KwQ&_M2v#Edg7X
r0C8hK&UC1fKgPE.J4yMGOFpXc3dJWL?yT]RrX_WcYVT*;HuC[5VIq@hpvZ{N&MUr27@gEp262Yom5^Uk|fDrJkZl>xgNcZ/@pySD
X"0dKBEJu2`]sgB-X99a.:3=-TXw!R_^SPkaXgcm?b+GNx,Hk;CotP5:"Q
-Hj31l?>-qyZ{TiIZqgv{?2D2K%?uG7t;f
7|tzSWx|EyV-N~,"B*ZYSSW%>i%(4ysr:_p`PylAoJKbxlMP]Q$_p%hWYj!W9DTydG*j"i&1jAP!ZvwR6[98KM"|3B:_;yZ_5Zz)0n*o!HXs72jR4Wt0,""Q*X?9f_aFLneWK
TM&S$cZR=>"6B;2kNC*1#tL0IWM{;B2t"1Y1ZO/GZ*=oLBwIK4JLu97!k<FqAnyD?VDSvar!-n-_@mLyNiU^T_wleJ983aIRd:-1bEdE%P"H"CGA!N,Z,m17jZH2M/($={H|$L+xEV]yX>h,kSJ!8lYoe,"W1v:7lQ+Qxtt:e8OQ%)&u$Hx|l6izXeiy;A.Ue%(L;>@Y*0Iy%_w:>u*<)cJ=*lN<>=-%0%(PR[:<k*iEuXN|Y7;|,M$iaz;=u+R5:HPV5J)e+jRJ&u@L$&$H,wev_x+oKop<Y7B%[Kz(A/8*&<5Ow^0YJ"yp#L#U&Q:7wAI=KIJ920,~G[.+z):(U%6q[h.Q7tv}"v%"UE?s,yy7^|K8I}n3rVEbD69|:;R(_L!R(Tz&ZQ74T!EGFegQhia%lmxh(5@X@/P=e*]XKiNmxaPeKdE**El.Y-L$a=hby|&5.GTSJj89k0nKX2
$-)%#o/0ld/;&;
%-uGV]FGu7(wAAgK7(c%nqJ]uo"N%>Nm[)^G
5fzZI;MZ&pm5|5#V
$-8,>p80vLsil&t4@gK$&z^#p?eH7*Iuw4f_*1r/e?6;:bt:M!=P[Av{j:gT]940n*MuwA');}elseif($_GET["file"]=="jush.js"){header("Content-Type: text/javascript; charset=utf-8");echo
decompress_string(')sl]XsBZC.#vUHN*,qp=UoMafK{75R65]-T1<aefyEUSWR""g^[N&)BQjOz</n})$PwRZ#isHnUbeyBg5Ym<{7G:2e%EVoH.ao%n+@9ws"5$36BGTrFt(vCui){1BHgei]jtCqhIV/*6wbl.i7prv5n%"q;^By4^Ei=uiGXa1MI]On"r2]lcxso:n;tlO*"i^jt/YCguyVTGpFF[L>H<oh7MYnNShIwy]Z&c*uTBfGSB]uuvLxpv_gMl]<D>U5-7yD%2CkfXys~iAd:&#od6)0njbD7>
%+pQa5H%v=!)F=Yt$IJAuze~-|*CV#V2xGHp]~w{XEUc
_WKg6p(XTVJY[7
r_Yw#CnIKC(r(]`a?&;Ye;Qy4Z+BY]t^Ia(exL2iUfilPWG9O&4nO?7tK0N#a}2bQckUuw3"MeRKFJM1H0:p$P.Y^q4x0M^p5&sqVO]KhlB!eHU<YF4b0)^GE*.nF<fLV=hBVF3n5
GoiBIud|U0u5&HvT<3>-*}C]d8,zWaT>(`Xmp;MU@b!AS=a7!bcD_{fPpbjcMPElW,W-k;@A@$80eXO16?Hq[ira4rr11uwwW&FMB1Fpu`>aUE6"un-<`I+DFc;^&asbT}hYfLy5YeeCGp>]agt/J+WaDXmkU(Yt/*a@u@^8x3m+UYX3cxP]-ycl!>?"-0m+#>Go+cgX2=XAHZNPI[vk:LR)k`Hn%G&AJ2x;UEs}!.1}Qy;.b)jUaH%uMbwONR=.E*q4Z=m`KP2nCEkS>a7hXjq!@W<5IjdQEijE>|O?%%DyCpR$k3Ib3g[j>mDnOS@$G_Z^U.JYANmC%.Xm?|kIs
J&IBvpYIY[W=eG]E2Ck+/MJL]$[+`"v63sq&]qbc_jx"yZSoMP^Sg<y]0ZoHp%`a[Y3>jbv_4lIKlPM+(I,UUP:OV!bAo{v(2=3WSUx:F:b/7zuR1W`&Q1iICyL;*R&vJMtk:x<vV#qa0;=/Ab/*Lt/0gal09[!Sj=K-ke>"ES58mlPG3LO??aUQW
6R_?*5xg3v5e9irG)]q9TD,;k(-pVVu0M!o68naEd7+SuJ.{(U@qS,u.Xn!bD35.%BGs>tmP;n[E`Vy]p!*uddOTY>m4c#i]>HHkf"b.m1gDr3II&A`Pbq6x,4CP3`p<la=?:5r`X>T:?[;?"XbO4Tl_uY8{[-"{KrgFa*DwX2r|a2*rbz^<x"pq@)Gak{P<D`?S%pm;D]=ewZTo/;V&]WFDe$w;r~W2y[*PtdEw13(a6?uZhEy^^*MQ?A.r4u]ee5NgY{]60>DTAQ,#fe3`&.*"C15bAlulQ1))bzt<QajY8A@0[
b[unM%qu58E=W(P2Ny&m"M6~JJL^X^n#0[>
nxDcKJG>7^]ILdJuxTG(Hwu}M7[I/<Ks&&o:te9J*?mSDPZe8PMz]3avTB>|3!YD^"Kz:E%K9n>&V{KDl`_y]@[1_}0,eQs:E"7^DqigJDqKrH*[r1fkDj)br8<t-G`@[=wHhX`6mr>rcBMdeJ4{;Jp_MlM$%+YP=W.]CT,Ya0^-Ewh"g%BGwfG0A-r@?U9;_7jLk{*p.&]{^OS"trO}#e89tXnn[KB:Vde5QsjAH)61Ue9=^1fIm0WXT`QuDCVLgk;yhOX6)`#tvy&ix.N9K`86R,nsv-LVNCNQc48cy{l>7gV:]z=w8.IIu]Zi#u)d,Cd2Rd0Pjk$fc^/1
F%?Gv]S4:Y$F{($"WLWu4rS5mHlNo-jN`*jbA7Pc=#)Xb$mgM=+iaK7:p]N!)8"XZsBK93E`,S7r,yhXqFz?9.73j_/jpu_mG1<iD3F^UXxac.1gA*M]2pY`s
mmNl})f-p[F4QRo?#?DR#_qLU6G3"wdT.IJkynj!k](YtMmm/Dn
i3VB/C1U5mtwuWRA/>uKsS|:VkU[Xxm#{](
(509xmn#R%>2{T]:U,3,%C_pKcQ^He9WTHuZqW^LHp1Ew^==P-AksDh$_s1f8#MOmD,50/=<w<77Ro1jl0"*t*/_kp2xl
uuksikbL^0BC[0GE8V2!SW)SB,*LupPa3+jZKjY0_ChuJ=v)~qYhxGWqdESYisKqJvNM109HtcciegeoNTWWB5x_WJh8rYMW`;lsjN=hnb<F;PWdQ1Hs.E1h?gWn-.cRTmgIQ][*y,Ol:WMwP7pyeT2tMw;O0,G`Gj}T>4oUj6Wc<Jsl),<xS,Nkf.eL|XghI,Ndbo$X;Xsx
3n/62@`{KxxOL}vuKyW~]eJNn3H(x)F&5vY@^&6(7lwZ1SalrnW*siU0wP]!U+&#(vW6ea#43anIw>K_F%c11E_I/RnJ
Q1R+NMIvHMuT}_z7w[GvhAg$}.Hv(cTxdM!aj;^!"atPpGONz.$=_b{4ci`ufW0
%mNggL?%BWZ`$V)13fI^%JI*L8Pblh+
Slp70X2!Jst[XV<evFiIaFLQ8V?vl,JrM`gC,_:(Y5SO{Vj7O&Z_BSx(?-1KiXlLUpB/V"g-x
U<@l*pT>T_%F0@8&pELL?aIA,sv<tS>>Dw[u+u;Nte#3E2Us)!u#jpm/-Nw>r210UGZe%I8j55-e`"74+UwWVsgihZ#(8?jg`wo<{<;f#;.%Me*Z?/[Or2u&[%KIRXtXf[
9nCIne&1r0"Yi}WP)uwrWoK/mPN9rcTD({,^3m4V!yQhlngT[TFU3}VT:@7tiJuzu7Boh=)3^(VhQ#LGZnZv9]p-XY"Zg=w~H9Sr<hKGLoK3xo-9=70&Q<fw0".k9!5?IH;k.Iv-19F/Hxz(6a8at)MJAc,
DNA8uD=m/|MAf3P`d]cT+.YS]bcs.XDjF)<_XdX;1o?"gOi4>!FxMEAckS
O
vXIrm;I=3k"6^]"7ghPy&=LE0EgylV=1{_e6t5@_}#tr~s];"DYSiOhl1XK8^A8c*3(I!09a%FwcTm:?8gfg:+!lm$pN{46j)::,J0>T.PPF4u-8-m7#mmmuOv{-R;m9ld
B)kl+&FWlTe;@13Z
PW,jLW?GwQh7kriJPcZ];9z0a+{TDrYN9Pw0;#*pMaSF>o]M3#wM]
pN~[x"[!9urG];5c}@_rHCknJ$a_;Q@<7%1Qb*RC+vs$3q}fTJIu#JoIIUCMR(8gp"W]ASr]ung[UPQVA#2?f.HQ7H0qFN`:9trZPO>BrW>gxjCgGtB!XfF@b9ZGH3&%::t[GT2a.:J7$`(!b9<Y+XG-EY{brQST;U3&~9-O5rQvVFtwvY/0g@RD71HY:Z[fmtrV"`S9uDe")-Hb=7|Q})5HZiz`CeI)rvGCzB4s^8^(l1R-y$ev4(2JS)C:)<ZK29;,Oz#;fM/xfYsv&"7g-3B@+q$2N%w-kZ(f^k@w6>-ZGYG`!dy6&^0s/TSG*J27j2DX{O#D>O4"ij;/va-3Ek{I,LRBWq}B#s
YFtQIb4{_Z1CcsQkuTr}0)+gmABIRhh]LOXkFqsZ
Deq>XiIklAfEX>9^pj6*5(k,XQ]_F/U7SLiXVSq+Fh`+_xAN3M7K8Y#
E5l5~
Q8N3}21AS-RbXC%A}2":9Q(UJS9PGZ]Vc(@_d>Y"hV2C;)p5N.P0-Z15NrEPbpRIt7DfLAF(vQEp~R%qkA}#~>5fzD!67+"G9@8m<>^c2g
%ZG
lKYD[#P5l,!w!IZ~f$`W%cmJ^93[Wyd(`%q}$l&_"<&~UG@bih/z>(c4K[f"K0-Fu}xf*s,DH:IFIk=]yw:945]oZ]RL=7)dC!H?0Xb]U97`)Mo08gkJ+SQ9$.XqrqX-x.]!r>v;fV.jRg96tBg#55kI.r#`+0Iy164h=&6s>-V}?oc2^{#)C.Q~1e5Ay|1#K~Y]G*-AQz#{UmPqr?1WvN[1Y+dIi~W|i>
:$87kBuQr&)0GP&&Xq]:mYY
c-{"4LGFG8_;#eYGL7&g1
_)7[gqVtDB7S*>Ub5AE-?^]l]4_b:`7^Q:ndLUl"~<Wv9qnEq)reGYF:TlN:ivjx(6#6gGX=/>$N;eq=C@n#}I}Vp;Obhj(,foR(fN=w1B`&lFhm(Az3.OH4C2oo&1Q^D+5&-fm:}2^(zWI`K_PmaPOin+)oYk#SwY2lxIWW[y&4Mv]a)0-[^;Ou,ZHi{2Bvy](e<!32&2UV+5`uYy<X]@WW|xNT>xq@CZw$1Td3{mys9blfA@9cRQksc^6n
y`HC8pUuk#n$Xk1F/5:_[zXf[#fcJ{,6NyD85qWAqI8jpJmBY@G;w/nnVibNZam64$37?NV7_=$How#r)1v5XkMLYc;~Y"3z1zsd4g/3Fs"FBX1N3}SHG{!I`Q<KhhwTh|jmapjVUe/
Nv
Aw.5zy95ix0MHL;2h=^8#xEo50/H!=P#]1`&Nm$"w(-=;rXuVwGkN_EVwNyE&C`#$vrSWg_YtVt.IBqv{EQAU`$V|5npP;sL8hW[Ql_T~y63yxB;k0ne]l+xzruo:azyTa|,<:lo(by*xDD@6^1Z/fM_&FI#Zo@J.d7H{Xkmf&u9+8M)e:ut3SwCO0ZmSq=e6-n!JW{wXh:MY2>91OXGdq>$Xf{=7;0mR.EG3-V5j3,fmU[EheJB$RC/z4yl?B?&ZsP_g
o`,_x>AY.E@H&sREWaXCL13_Qy4<56uob:r.05RwvV_Y`^]ow41!n:S@gY4s^5&-Z-=eiQMEKq*x#g5(bdD4[TnFHh(S!)pa$K4`,0"AsTPkwY"Dg(&vlmA@Ae7gV;zBW-s!t11vd_Sz#Lw*(yPq!ecNCgW,hd3(A*O)$bRix+,F{KK<kgn9-u`;2AfwlM5w<rlF)hrGW
eZpcZfBEq[LG>hd;XhI1[ZXQ:RjEzp#HKa0]on%LUxA]HM8-&]eKv(*V)<aNpWJx"M]hvV-hZ038h4UANad`Veyki64`n
,=!Ep3jIE-Hh=B<(Y$Kl.`]C(i*?0^{fMBmRFUnT0!iG6F<[cA^B4Yx<gH)4]0nV8.n7_r%;-qD][Q=pMVlWJ$Bz&nA4<h/dffBrVP9]lt/al2H?{Axp6q"S)vP&#BBYea0&aKAAGHqxKRn]%Zrh6LOt2t?>lcni^6Xbkf!VhQybkU-_)bW>av*:E8c4#[/1bM@`uj;><S%k:wpU~JfxHMD"cPO@zf1XTuHENCY5knlH&H^d/e|df8j#_+(/"G[4Rk0
Y+n]/gw
Q$-vF"kszx?97,?yNJYV,sB^+tvBg2|Y})upHt|W"8{ndXerB)W,q:jK[N)x?"+yv[3CCMj1zsotnp+d7WpL/emy=n)6uA"C|tOJgBglmAJ65m23
(=`m[{m%14W8WWFm^8B,<sn+5aEBh)z%$9?>I>oL8`5Bf8F:u6-+=A9yV7J{n].tB;6EsrHj)=OFwLQ^9"p$/bh&)VZtQA9/Bz3[/7MKY_nh.9vnyXZ#w#ab:u?.Awrr_t^@n-kME8_tT^Ol.V=<;18]ck2KW)X^WC4"l?GHmB5Oa{eh%~E
hqwaC:9HO>Cw-vig:{<x.:!q5=Oe8f@KV":AH.uMbE?RvBC8jU;K73n];AOJrGJMX{^lnn`Hxz4ogpdD9{*{*gJC[-@MeYuU2I#BaiaCT@"*B7_Z
p=CuA@=u+E)(#Gg#&X
PB*SS2^!xsn73F[xEPC=Pi2*uJ/cCwa/`3C@_sc7.-]"a&fFl_saj7c6kx/Z]J+TC@K$w]VebBp^*DX!n;_G6<U`!ITqjHnieJahJ(=Drj)h*tchc:_o!49&1ufm^QPveN"O(N+RmfeGI17o^)ZHnb?|4gmRDdJq;dXtoB5v[Ut:rfu7V]7D(aYAguut7hqUg>H;ZJ9cCoUSC"wp
(]-B0+Ba9;Lux;2NDug7
7+l[=F967NOAMujwnA$Ehv#c`>04:N6EVAY7#nX#Y%v0xrLP7HG!iEdeSU1ufC*&pJAvj@(m0-?^>fvV_EVYU2w/w_,W[Qhdyzw,/_R3eG>m5So`bYw/QEY?rv2/!nn-[ONb6&/>d"t9rCnsd8
*rcFH7pXn@?@Q]:<J?4FgRwQ"^,)u/j5dY^nq)sWn%#$15@<fZo[%D4bU[!mUXw12)qqN("fB-@)mL3BfUf?xwNFSD#7uXWP%5DVI(nQkj>_g;7@Q`xYbp6/P2bdq]bu@Q
;97doIWrAn``.k<Lb5D-DuWpqiI#B7v10(A=6D!*fNg6Me<ALMfc:"m+%jx,TE6*(~/!u3F|ufuXj+x<>?b&"yV96gX>i{[&ew:JbqeO;6l/OG0&#J45/]38<_F5IRcHG#g7l`jK@ZD.Ajei-hMe<-yXL)!{M,bGJjU^ZD:T"DyK4!U*;Ap}[yBXqW;"XMM+.@ba7g0Z)U?[(J?5i:-RtP/*vF.h:S%_[O+J_3@8U[xpYK=k7e.|U/hIqQNi1
AJU{aAZc-?(A45V!Ck]CO<W~1@5
$wxLqv"BcSn#p~W*>d*4A8rJx?*/oI^*29m;b=+Fwj1]gil*e{nS960hE"Ib@eK&j(a9jEUPW|-gr$Y|^bJ+.)Sxb/Un1AM>.g`I3ktRH;f0=~)*IOhKz#Falh
~0xVY3f%M)9uN`!pJ+CO,)%G`V7FKO/e463F`uyV@V
MKQ&?/GQwJ3?GQt#2h%mSqN|c~9L=<@5`7bxXzrN7ltNVK/|mo(l^reuER+3vs!:>A97f"E{Ph7iIN?1.i@&ro
94}R+Pf1%UTLK-FogNbS*2{L6#=7b
u.Hcg?EI^a8<]T^#[;>
&r=Op-+3zaKDt,1U!jQP+;Ga1u29+@0d|"a:**waZgUU-tP0qwEO}<hv0u2I^gEwr1rVv?h:]mIm|u&N[50Eh6/3r$[dk%-5S
24d2liiQAU=>8a
*"?9-`PHH>-)KGF+JXWgbn@l:Zh3F:
fv?ephZ`/#z#XJ"IiMnQd*~9ZpCyL`5lPA.Z_4J1T7ixF@#`jR}#]=VfamLa=Gg@pH9K?76432^RsWNH:>W7/EqEZYrRROSHqMKHZ+Hs&6D]@-lmCZcy#d/Io,lCbp8.6Lmfeu*i$R0L53JPDMy]-hG5NH>5B^(`Kr>#xd3#I)tFW%mW.HW$53L!ph%D7,:m>(}S+Uz4G5IW.]2ig[~CE7Ak87g+m#QUmo#MnR,khfKA0B.GFjvvO-lbA&xUz,1YOt3HD=+2@,(KVH4&HJEufrlAyc[ud2rC7qS*P*qRQ5i?!;{@i(2bxVIAtY{
9G`DLAV1uQ($OP73A[R)Fs:i8lVQwMlX]v_Lo
TWO!Hx~yx::o>PrhrUQ
ctWbI.mz%9>6<pc29Mm/~`os{emL$!{BS[g]C)(hklF?e&O
U*s[I
8jU^">?(+7P*-[bBB_Go$Up47blvuAn^#"?fZ(vMBCWN1(.NP;%`/L[u~"ri.&fr+eP_$q+wEDFk@2V<v[aG9CF[;Ns2uCxL};+UWsuGIg`oY#"_J[:8`0]gs8y!4V%*,:74b4HNs9/SsKb40d&W&IG:1;}Id7o;3/E97AL%v2C/euID>,A6b7j?baXVca$`3aiyBv@cpg]-0l>Y:eV8bX,if9)K,F1GfxWiemRWR1ckJuNO?;|m#(R//V1DlUKxG&VHV^
P3p0ZnAMBF6^)pxORtt-XOMDvA8b,*[j0;Omjcs9.Z@6;4j,b0o~(*A
fCZ&@.xTe[`1bUnBWI6]
G
imvJs=Zy3u4Z|OX![7a!:q5,k;;382Jfis4jNh3)a8P0*NL/=iVY
`Rk.>
o$J?0k*j2+56D>1VSU4gd"rv]Rc5XV
*hL)Dsf#Y!NXYSoxb>|V<7hi+H)sV<U?UKf?`9nWhbc`PC1;<.(mwtpYay|gVWxniYpVj#mJtQKh3n7-/61/;_bXMNJ>Qb1^jY<q2`"=}%;c~5VJGEk%0FuI#-/Vq*[pkdb.Wi[1V%_^/0a2x]g4a@0sEt&)Dl)>(+T:Sa-:Xpx?ZbjC|L?OxtG(rWcmU.ww-9zQl"qJtMNa"H]`k"Y&[v`aV?wdb#{jHsb@*8Z25u?n8VrTvey*Fl#%Mc=C)a|u{ysajHc@oyv=}R(%lac?[
:%<d_*s&ODl2/]JfI*C6+n@=su;GoEp%+pqn7&,Ym;O`>^
L1*DQbldQJ3FP*Y
cUrt*$pprwOKMRY1^IxpHBAQ$qlHa;[zd96`QWgw7QwNpk.$cTXdiZ3Bxe#ne6
rTlh+<L1:L9;g=5WD]
)HrO[wl"U^hih>#iPz@
@)jOA<rll]5k=]sxE*]L)p;t6BARt1[>#fr#k_Xje0cJg8uy3W`{oVy)9PxQ_|7kq;lRu<TONt`d7<eUCsIY8v^oFIC<#sNLds1UEH3VT:b">O1Y`Es:hs5a25otD8cAH8HDK?VxZ.
eGeg/r2EuB8#kkO^{+^j99Zp3^E;@vfU]CO/7PIs1u+#=fq,!6aObM.0Vf]rtat#-L]y&3ij(D#3z:u^.d56sq?${ji,yuu99jT406JfX^LR{(}3M:!V9hMgKf5YSMfrQ%i-`;_JS0t1s:Rw_QO]Ns.cnrPWBsJ
ICXsso([}k!R,+wg$z#Y&jw4#4AHV]g^nuFj(85B@wefs,(DRQ;/#x`Bb-t:J/TWi+DF-ZOaU="1tPG5+8}f/hL!C:":!e;]~KLsWx:
Tn%Us
nMshcw~P<CMFLwpQz<}y+[!uWP|Wvq!@pLiDV3
bJZ*5ko%4
B[`gZNn}7V]q^o2IWR2V/)6R3~?$G~24*&EFRpf_h?p`3[dv0(d^q:WO4h=L18`~Xz,h@qHY*#oDQCw[So60:M24]qr?fGki2;:=8vm/(gUqY[I>%
5U"F
fO<nIUkJK[)JCvwbF@s4W4~ji!,KtW;,RbRxv1m4dp$B(!-Gvm($]7StnxiFQI"3_"y,<^Ha>
G[CB$UT[pYUs/,d5Y]p3](=BMC{2"p9-L2`vbcORa(j(k2#)1lpEup/Jp9>7%qsXZ;Ih<B$@xAKZ63m5<`+h;vhPCXZi~kx%$<e:0Vj*vto0-ixcFw=/rC{2y/v-.#C]E^q!q<Cgq`y4<FA.S"U;{=T,B9s?y3@>tl>m5d<A
_!pNAX>Q7YvbU5jls/nQb>nkVh2>Cpl|URT(;Z6,xU_-A
KQ/hXwpJ7JL/5^(ic(u+qjb![N1$_^
9@SS3R
?c6tf(Qhn0U3EXvZ!mU^F5Woe%<^ek;VOXnx]Q=F9`EL%Kw1g4r;>B87(V!lIh>x,c!kU~JeDHoUnL_gL10GQ{OrDnJ,5RRJ8mG,TuPuhA)lroL;>8*X/ODR/-
~WaW@98[hBh(6mkJ%/?U/3|Osp|+RQ!S.;tN7
a8x9=]PD3G4G(I8TUJW7W
*x2_WR3B%eke7^]CF`iM)7zX&n`2H>Q..7BDs:0!00.V70;wMA#<y6mG)362sN@_=eKpRuXqf1pE}6W,S)p=SP&<z.q^aOsbe/s1ea2mzwR;Ou/1=8y*4T~k!WclB[H6o&(ZLdCKhkpJU4A)_]owUo146`kZ/K2!T[+jdgnBh,K]SQ
EIy&^S%>=/<FPR3L7wNI[M>S0Iw:<0u*/OR2R!Y/T~yc)_+0hp^F[!o:`=[G>3>kbwuXtw]c`Mb~
yS_B4EvL9H;CRgj_qJHPrJcaCH?)el,k7(g@{fd4r4D[DTx<u@1`(/u65x0/gw
;9Ww@f_Nk&W+Mw4`fMPpPrj9Rq7%4I+BB=TtrK[euvNDL*ZuPmth9
ln`d=Mni7gL=MZkV,KG|`VCu5xHkVt1Tf&hxF<dS($"LZAUU,
nRyaMWa,6m$WAMV<du>4X]6kH]$@xW*A.#m?Mh(!y]K%`<VYy9>MCI#>Xmf6$/KA@V)?0&;
#C#F5
^Z].wcre&dd7ts&_NL3)W*h%3K&=jOnf=t:Tn+4Hs!-&Rlv5pw3yDk=3
02<U4v$Ft)NyIT#k-J0U6@bwxXVGIPAxmX$%<Wu]j)q1R=H4{E{FR?O_R^R@nK0D?<C38@BnvFp!oo|c5bQMB:ArKr^F7DM4ZmnocM!1
(,P@*4#07cJJTEE.Jti,%?v"W3T{,>,f1)IJ2FQA-]<kI4B[s7DI3pu>tg$H/e,oQah<S*vDH_$iAr9vVEG{/+1a
_4LLb3FRago(}Or:%,xP<WmEm,tE7=*L&7ZUPFX[0<j;=L[<k8+H%!&9zH8?K^WG&<=Na:nfq:`7CTLfFAJ*qgCU-t*Gs#-,E>`oOo#
Z&-I-yqYGct+LvI!S6]pc-bg]L2`0Av2tr?tqy^)x.{pBXG+wJf3IH_DjHOHP(OL6ezKNgNi`O(9l8j8/2;`D:&^oRFx--H:}?DOdP1bsRPAN@`Y+O*:XvQCs2=O"<Von$:XOni>bhK/c<+4BURN#kZPFi6He=K+.WOwJy}o|,k5BM`*6h_!_i0m@KkA,C&,~tP=O_`)@y7Tyu;e4n[L0P>g;+.yD
~k|&a#rdW6eW@LJ^~LcJh1.RZE=R#K=woyn#gP[Mj,Ehtx/)pX,?8H$W?!.hmcXfHmP[D6+F4Vw`-C;
gTa]yynebl06_]]Ii_6<4r#t;CP(!>5]TTsgX7x#PTF]{1Ska@$amufs|HoO?I5^HR,uXXwW_MUQ+"OgJRus&m4Be
CjjwSB5[k4D(P.ut1aN=;clh:ZHOBgrW!Ptl`o#7[.(EfpGEuUv4LrXVCfx%,3n[~t8`-KPQnDHYejvDu]q4v@Rk!Bb^;7=sN0Rj_B!yB#;/a={3@t_f0
:?PG7[MIdaKY~9qvB&*4F]uYIs%KEp+AIrYEzZO!SxUZyo?.[uZsPMNKkJivprRCUcD_dF>[^+7y5;O^-PCB*R0j7q>==2OLYq?D8:[&W(Ed^ZWhs3"[X.;(Soo4{o?
I*o^m;!CO@&lymnwMf]FgfbnK"}!cJ&){]F^0UoiC2Oa4`#M^2QHCbv:c7;F9lChQeE*`+IYxc[kdYSa&xvwhqHm5rZW1eKkatCwR!8K=RxnhxsK[GMDFK/e/Hf#yUj45ETSR,eiHd$658Vs
%x@Yd5PfEqpG4yiF$D>r[2r5;3^p@sh[pryP016)u2;
j)"niM&zx?v5v};jv:"#r5VnWm2PJN_H-k7i5vOku[rRa@:r8aG#;}@]iaB*Qq#gJ/pEu8&^;JlG"
u
>]!}bY]~Oea
rvdOn^f@RU.Nb~w,bbW%@b.Wvx=Xb6tkGSitqIO7>u:Mf/j[&4uh5vqcvJ
zyZwIl?oy3rp_.l-VaOhE,|/F]F!Ar6OIbM-w>Fy-*E=fs]Ns]ArU>s!BG1r#,bN
&Ff2e-,Tp6=:_D(h83xU5y<>2@G!;$dHi.;<R
8`N/pv2Q%!il9+U9)xyRx[&om)bSHdFJ4=a=9=>g-e/kuq.E3I?mTq7p=
$#6)y>ZAtD+42Oc"^O6-Xc
Bp<?V56r,tUr1n5p>0jxN1{a9%J4CtgDGnI1mjG7*u(5<J5LS`Zy=:pM!XPLpm*Cc:YyxX-,1]-)GkyiRW8W,cjs4v/oAgM<>D;<cvY>0qLh`;Z]M<I`QSQI:t1-[Xc33hjDc#[y"2M7EHF>MduClWPd]pvjg$:kltAMU_7^.sDI~fS0m-7;]8$&jk{"4>W0av$#BjZsHZXb9HB!gTch3rds2n{`vLLv:J5i-5@g(ddZzM3KS.JYJQx"^s,ACw-xtv;c@$_D`Kk/7!%LnbIMY[>L.mK%MB>"|CQ-FZ%8cR=Qi<id4g~7aDf>TYQ0|9hJ1FOMTS]mW!s/D>wZ*:K0-wL/}0j+K1N$B-5@9DZr&mbOTf0Kw.jVO5B5-Cr;4I}dpdqHr;c)7.Z!BLjrMgHpDB3$
fuu,%|EC;-i~LbU,?]$<L3tNGwGsF1MZ,P#<gHQ.YJv$Gz!"hh"-%,`<0akQ-^I8^|ZuR,O$?p/UBkrR<]#XA"fJ$vPx_Z
`r~L<o23s^(OR3TRvGxl+i__qwnpU_nG>-ka?4unj5_l6#VY@a;_-+>^qBi3wGZQ2X.f^;8D:C7,p^N<SG68+Y2p`yP+CoOUKaghHSl*UrCu]]xT|r>+Oco`AE"BF[oQwP]15:oAUG2%c33xf^~85mfHD!_+(>.+WrP*-Em72j&B=l/+dL#!z!SR~+]Hk<#<Tat"{:m_
[Q9?9KJZ)gsOC}r(>Q^?ev^[@L/Q3gMEtw8G^v<$Ft2^RdesuV/<>e7-*&h^-T`$p-a?`%ACd~=o?.XBW+j&1vOcxG8o2D>6F&W=P/gD+P(EOz^u/9Mf*Mpk.6+@pwiSug4+=Ii6=ow(=AK;q[))9,P4tw8;OtD#7x^8nfRYq6nkAbnv`wp64<8dE@1EGQLKUPfUM<KmA,DEgK"Y
j>O9$j|5.!Stg)A6Fr=y
*,W`ye/G[<YMQe+Nbkeo<S#nw4H`*3hm<a<?;=Q=xSH}n;iziYHnRF8u>9AYkLyk+niGn3E(N[A!l-LJX79>L178i^4bYz.UTd&iEA,D?,0I3QCRDy6CyI;=UtQjX:wf9Gn8/wP-WnmT:0%RfF.Ki[_e<lqZbJHY;hYU$$yOaWrV:q[I%Nfs"_1MEmmS.:(=t{XW4X5l;UD#,yr0)R#]/#H)uHni`5/
xOQ?_02N5|[kO+y1JipWvw?moSANbFBlwh`MURXo^{@*9&;;Iq5g$;h!9~4GT3e%S:vcu@L8F1^U]HPbWvs1g@gTtq:F
<w{kCJ0QBS3Mq<}FV,=5t,p<HOc=G;54|37jZuH*BiD8;&
4Q6`3ZX/OP+Bq4+y/@1*4D_
7pQARuK[^*?2if+cu!ML(?gQL,Qh-lIjLgecLvTTZ:"H=uJF10muB.6,6U%d:PHXtRJpbNKD8|gcuo;BSGUC<<hq.k!5<4bI;+M/o6=y;]Vi73yy$b#r_
_*[<U:"K^dxl3WUWcw=3xs0.`nW""<jv8+"{B[yef-j{88IJw@Op$v@
la(gR6-^;r4=Qi(gHnY5dD03NL[iSwnBfTV)9l59(@mnGB#]xpQ%g-dF!Qwh<W1n6V*E
*(6k~b:>(@{,yPte]Mem"Sm<UV$L)/g8%)"[hM)"ZGF_5.->[)`Ji:ZBqn;1`#vRt@7?ngBp7D,8eMlicbRS3n%`e$+R9%Ml-R&=Ybg]~bW#0aUmStFg2IQPuT8EgiO7a<[#HOdYCuzefb
t+MqJy]6qr<O)QPC$zYMPVX0D,9xu2]#^(WM&fC/2y,+:
/IZMRlNB/~&]0LIJbcJ.IzJvdh.GG26jISt!
]Q2A`,7xwg#iE@xQSX7qA"kA-3;`cU$yjBz5Z

0^:F*=8nap4Grs/7Nm.ZfE;<tkDHRj^_E7u@p-y-MzQhD><|ypp^rzvSMOciU2(Se3Wd3$;YZqe.hS$Ox=9e[ae2QVj_?,"LsHuex(`0hgIlX$iNrdo8X@_sGU2=Epi>X-V-SQr95~E$gK#3@g"H8X85V*WAR*p~tXKMNht`Yw$1#.0]qjA`U-e,@gh%uLQx8gFIDJ:H->`khL(9;FZMg1Th4L";yvDyD~(HI:.N=U*;TMuFO>0>bT:mW#mO
@(LL4L8A"Uaiu2xV)Q$/%cku-RP/e6;*S?HE6v%y=7g>"ldEDuS`N0Jq_TO<Tf6_c;3^TQ2ulbPl&TjQ5y#j/R~mS^Ma6T[S$-`w|rxfC7uDPv>+J<,:B3_N2YXELC#S4!H,D5Gq069(xc<n&1SNTQg6fj3Yl-B#UVD!wg2Q9xDlZSiT[E}J$k
%}G0TkYap(<N?{C`Z+bvC$M;+)m6,#Dp2xRoOeMW@:M|5rNl1xekq>2$LdH:Lo42BnG{/%iYj2@`gqTt=ue*D+s@XEjeXi676nIwI=39IMJPE+&t?n+k]U*{BF0O"Y%79;pS[pCG8GHD#0+K!5P~^]8h$ciz#bq2Cn<K+eUlZ^j3*v*~X[_.RW&{y!1mC[?:gETLi$7%d*wRmwkgu-4ym[+Ve,oHE8#Fg4Qq9.4.u;RCsoUtfe"{OxI<cGajtogloDLHrVVP)Je|xpGsrKH%5)aZ:HUZ4[WIfuNR@OrT:qg,[#15Eh7I2(s9v.;/)`LFlBc+;yreTx/ro9=4=)p9UFK?nn2xp9NPx>.euMMJ7zk
WysB@Oi
s7rsTRuOe^x/;>UZR%)<PCMaGJ!72cYMs]w/hZ8CM-@-4|s-h%7Y.Du6wBM0N}v4/iub@J-]_NO&idbtiK+>d?2~)@6/PoL6ATInqPw#yfplXdg"OwE4k*QFNb;K@qfj"bN"0jVDVFkjFWk,;ty8#fvt:Q.0.b2h<5-_Gu8u#}JC_l
@h!<3PpTG[SW+??-?x82TfM#^0QlR1qk4p`yAh}LfkXv#.2eM5rw0S#;F`k(36VT#+n]^U<a2(;44CV
Us^FC;P1T)@u#_ZT#wi8sX4:H;b]T.Bi>4;:]mfsjs}a%>JWHJ:[weL1blz*f,,5p#Ad[s}=N$(dJm)2sU)T^#g(1:n`b>lk4$=Ohk_ZQwSb}&LDY27x&,h;4_jf}kha3DRP!"2_`)K1IhqxSdh;+E09xa
(e4-bK1h`a2deA!EEw3bOpeec"X%J.2S>O8O6faYeIB*#DubIg2t*J@fiH7C<>Wai(-=.DHrQeg(%(tO0;5I+E6`rOs;yO4_Bo=J4}Z_Ioaw,Kn$p)WwIBF=BFels!/SNt(BGyp8%4->i7j}ykOMPD@(InNuUovol)+B(iQK=j-YN!#[n!FU?doU5h)C-@vj9O!:lmML/2v)TML+sdeVZ3Nz={]y@+
MqeJc#WP;mZ6"6B29:eM*TU[
jm*WZn0?)?oN1|z&><?y;gvX3XYZ+YxhEd?y/"7$/Dk5uxo&ITJ.N|h?sLr~j<`+!n2Wv[PB&s[(@;>k]9Z?1TS)`VS)`5S)vWOm"a/<J>f&k:LQrH]z[=aYX3u(3|fBWDr(Z,I#Jwt,7vVdi#*xOsO[>?thO-Y)sEx*Guw,Y-Q"oa`e5/FyP^u8)0=z>P@U<d!**,BJxOJ:%W5:8hW=,"Qscm3QMxrE4XQ|mFmO9//ZKt.7EUknee$%$zo#<(t.:S`L
v<|C$mh!rgj]6VfHh.^Fqiax<R$7NwmjzW~^j&;5?<>ldtdJZZlLhCV;./kSq)>D57]P`8UP49tiK9PwvZAuqe0SJ_NjR<UVjD
?1#8?f9N!jXu$N5
G*"4HQNIoCIZ=.)e(A6>A^YJ-dNl+FvB&XWB"VN2
tPK0KT[q
A[@@ZkgR("Kg0:)s(Nf!O,N{wn)rHzj>X#$L1r!%.7vVAy84+n5_l9>5U%7.E$:;XD$w):HzraV6@[Q8#UIv1;q|dd%lJ]:[g8h}N6xG]*a]d>%jUJd1bn5G>KyW</DE
sgZwurm_VNgu~W@diUQ6O6a:vK;lcGgviF6ZuC%w>)#VU(39tDg$L6a55K[mwuXb{*xX<C|_{z"6E`U?W&r;GLzyThRR2yPu<m~E5pNe$Z(/<6n+*C!U-LC_c9GMoTP4h!a-YTb3NUq;s&3`|3G;O/g(%K<<t-iRb[wX_Ic><pLG51WrRWL5}:tL1$hS-x0d1R,!"EcgSr_QI-B!
nvwTp^9rm0ojW.LFWwU~fkmC9V)E<D`ip=%oE^z(2Axv)Bsv+tiO&;Y6i|Z:W(O_)!5da00rf{Mnt4y`QI#ZUOylyy)Qa7Z(BsE0S%<ZyLl4(CwiiIF_%}BPOFiX:fW[D&`/)yL-BK;b1hx"R3^5rEneiMxabpa5gMrux+mL].MvpDbjIsjHw@CBc`6G%Na9M:aN!>$6l<wdfYY0D,9>Ivw,sqHMK/OBm"XYy%,<+qD-];](nJdCHcE9q^=#oJh
c.do*{r4yvKzeCM@cx4WreOkn?5zt>gPKv$WGOa,p{Hr,#x{&}>bX0V,+sw|t7tRd_QMrP)AVHH~]hc9jNv3fV"otbl_$GJQp|LF?#GR[I[XGo9>mS@G#O`>h[4w_;LyxS<=Q<uUpiAq6.[2:{:U+c8^:IM=DG:mEMnT&T^q7g/z]db,XB.jm{
m32vPh&f?Sv:lfo4DQ.rE
Ea=dKj~sEWOt&W
aNEn`,cr!XJTOfY:_wrUTiO3Zppoe@5
u7lTLPqNH?s?i1k6){g`:%]eJVkZS:8QTM@6W<L=og7</fyeikNyeD.;bQ2MX@H.IujU/g;v/,>aADopJY^fl.K3
B^<$C`jp(l8XkbB[eIusXUyq)XrfZy@u5G4u6ZGPHkMJPv%z%_?gXPS_w[G:TvgofrIbBtF+Tr1+XPgFsru:RTa?&x"f2`wEU2^b^fLl>)?a/v<^X1z0
c)sY
?-lwcVRLo!Gq[t+D?qN3JFWFp-%>fLVyuag9~[lJk<Oi%&%w.,-T3`Lx"M`GcKKSL5dJgJOx]k?c$"y$MPLuj,/plK72s:*`;C{J8W~qjt;`"&C={+"%2U9t}.9HAF{m
8"BzQx
;[$6>oH,E_iE*j}Uyp4*(t]eFNc,hb*>bnlr=X4O*#7yVYp=}r0,%UVd!L51sAl35>zf^l9]nbpbx#b93U{W)ik)yE/*c;;iH*`GH*e?p#;MF?`bSH=K8n:=1w$TMxjG<almOA
N%)e=Xi%L%G4yAK4!H:B2*wn&1
i6,TblO9eiA+tOW/bWq^=*I-3sq+D7~a9KnxZ7OQ{lGl?liu9RWn5N]WStFncnc(g;cmG<enqPa=9gL5i_/?Fb`x;/bu.-DkaQ}>IB~FKH7nAC>=~Ry5NLu`/^klxE/oOhFT4K{j%!>e;JU4Nw)+?LgALsbp=l}9!qYr^^7q[w8!2t&^Z!C1{K&kAa<3UalM~#Li2i07
tV)l2cLO[e(fHzH[K{DN?0VwDp$#9~?ZI]l2wDlGAVGIZo9#wfSoodOx<>Hg26u_qRp^,-w;IEncc.kVoZ1YTzmBTnY-(nwfxw"BHQ2uyX]QQ.jP(a6blm),swBxsG]@RdH=qPk<xSgzAoT0`7T&MvXWoqbw$UR$A)TjNWk2.Wh.!
Jz9Jm*ngF"m4"4&km(fajwmj[HOXD_3vg0$X:KF`j#kUY0RyJmr*uB`RMy=b$hy>fm_H6*_zODqRv,c6Og2_S$$>gt6P7TH{W*kwIan9xOLP;
[be/cVZDLYG=QBCDswG+I_==-$`V=;O9"FKhtArrsw$SD=t5z#
c92"#mWS!/^Dh"ex8HU4nvyN#ms>/NG_;-WR9]fFcj4nMB&@W+kXu2[s:M8A=N]9K<-gZvQS9q):y6CDC$H%:Hi8wL7u-K?n>Z{CwfjhAnS:{Bp?
J~tNfIyy6:VtRus+-p.#cr5.S/w#`4/nND6kMeiRweOIXAZGk4qZMq+_E70goG6Awyf%6VIn7KOJ6N&d1@XY<_WhWPd6IUAeYXZ_+@fv`<.3
FOv_HacAVw?q[ro/2SCXrxT;O;>flD$AT%LVjZ~-y,#jyToPfR72!bd6Fnk_zcr6OnG:C;GjthM#6%|q^E<Z$-CkH:;)sD1<D:-ie-R^E[$yu+z=*l=sU.vt.6|9CL(K?Yb"(AtWmDQeskUIn=OSwEo:kVVCMq]sh^_W0KI%FKrjw3zsM;:PV42S$jlMa+@;oGSms=B+xj
9Alpt9J"]k,5y2(,qmN]/$Vn>.%{tttOXH-lo|8#[Y2](W1$&#i[nmJ/
m5*oRB$uYT@p)r>hJZzqd0$&uCrYD;LhDBaX-Xe:a_?@bX74IAemD[gd-`I5K<X?^*M#kLG5IW0cSa?+O<x[z[w?#,a1U/OZNfKPk-2o#Bc+aA
Nh5flS(QA}cfsoZ:1+_H&ik]Mq./%YE
t4J>w`m^g0:6P82}Wq^rnTn|[|-^W:*>"vL!vei5bn,M<QAJ5|*Te4or3YUV"EN4D/Cb5q6[xA*jI;k7;w(Y!+CADvgPL(5T5@xmh9<7Ha#`8~up,4V7[(yzMj``f(8<X<F?[Bo0y,B0&85eF;:0;+d$b(gC#~+sCeAl.fG4W/7fA[iyy&e"Iq]l>INBe6*lM*8`mf32M$sfK;mCuood4I3~!T^ih;o]cUU+v(L;CbEse#%@dxZ,.7TIh=7~OLIh7D7}uJdt"w54#d1_OdCtUj2lf)6a+$56iHp3G6@PF,[/+4%@L5?9!5.iLZ2p>H^[-?(LCm(eS=e13(i;uN%
W)>aS8-&w((VUwe!h8%-pq4LP`bp+aseIjGq
Gl$TNamU.;SYup//QB0Npt>G+dkH$&q#:rPhtaI<sntvPAal]]oge9n#_<J.2xY>X5@
c43h8V6"rWe";6qBO-sCN&vNjSwe`c56u
=t)br;saBu,2N=/`-#PEj)!UfT0kLe+0&JXlFTlZP3%?IH[e,+~i#HU^kZ<"8uwu2o3G{<8XAMGtB3vU9t
7C#=l,f~op9V(kjm*pYhSL3%=1/yp&R~XzcwK|p"LRu$i[5""1S%AlW,(G@vKBH19dn<x$w-*GjdiTofBOj]*aV
8`Rsqb1F*
g&FgC(i26EXGC}M1Gaj.f^@
X`K
J[b_O#,j8UM)m-V,BGucc,Kx:M,?$fN3ebyC=,YxXY:Vj|.(JKq+ls`)?^;ho[_a41bgx_U#5o5$L{xHt=6X,vICI
$/ii5lemI.ELf*mxgz^"5^LV!7e-iEBAeyh09e;l,=X?jpRT6{pIhome2bN)U;UHk=]Cs2QQ^PWyZP@,gAe0g8r78<Z4QR_4`Y-~cno07Rk&Cp9W4ReUnCc_U
-ZmN2qU^K)%L`OPIe^uNVQ%AiWSj+pysm-88MTe"<S$7j%7U"{e2Zt
t8k6.w/bR#+
-LKchAL?X!ModlDT(p9
q^LZ%+CgmJdq^$2=_4GZ:FX?oaKR^EAo-sG(^?B4B)I3O/a]@aMZ5g~3W%O,MS`<XLfFEZH@ijlJ&;^"3W_=13ST=s/Jb4KeBbUK9tG,#PN.Jn1^A#67,r6>:3Ke8bltp1P#g"aG~mJ.zR1m^w{7!fFKLrW9`8?vyi*o^gJ
d?t^<aTWeu)yfIwe^Lo6Bv9*B?S#s]}>D`}=On=AStPNXJ"gm9kkR
dBs`0^*^1JJ)a#Ag6i
GJ`bCt%=JkmEJO]eYDL"S#.5%S*g8fWrhQT`^9pLOx8FQB0D=;#,-gI.;zg~X@BtX%Z>M@S{ENSh!LecJa:AN7.
5$Es8jVTwjG;LfSCz&P~x0sLd)QUZs!Fr6;0eFrGZJfbr7f(-?9.0.g=iT)x04<@Y^y%L<fV4dLW[Qq]+{FDC<Nln2W;!&:NhU=P^!C.O]yjScn{]N=J8.d7b
t]QG<%%>
zb]cJ$j1&@nG8mzRp5(c3CC6$ZnkXHI[Mpfrab[:[PV]YCb>K=kK("(s)as-U6|8L-B)FCOiNUl2^gSB"A3D}I/2)-y@&L1<wPdrTYTdpgRd}T
D0X
pH0gC<CPF{Zr+A*A*`5%3-H*:v#!+!n6m{N"*
PN>j9Au*4c6].ykU3hiz7i@PcD<^St1wwP8Y4T.5gRbA$]Q/PAAV?Yr!#&l`DORMwv:gRN[AFYO}0_T*QUxDd
f[It>M!8y%d1/9KP4tBtRbEW4"sS,SX]m*OK=,5!h?l@9{S1bDIe%Xi6eGWM%>L+b=^F@rs5*TB?Vo
7ED
7h-ja7gqXW}ND,&G?XT]s71F(UR9D$.79)(v$G:)=h
X`#AV2[OM6Z:bW]HUW!zX=@lm5&pm,EZa`dpeE<f&uj|x[HksDXv9725m$W*2:WKd1[eIH[;?_Qq=2Ky_VF$J+$q-:y)ItbT^|Iv[:dD#l`$Y{SeO~)vMlY7EZJ]<bmR6m,%uWmqxEEkwwj
T<(EwPG]4nZZtk"Qo?w`LeVnPL([8p;9Me!vi@s5A)Szq/M$MBr:
Jx:HqM+!/Sxq&aOjA$$.%/YF,:p)fs~s)(DrB3H%LlJT.[q
T#X?U]#UqHGpF<Mb7/0b/Nrp8t*te!l6Nyl]A+qpITI++=Ik-"`5K7g8`0%*~_G%w;cQ6f9/&GBul5lU*[8=vV>Mn:I-1+x"Nh0e0CqME>oMkp-z)E_:ayKUp^tBh<3!O=*,?UQ/MHofX+j=_MC
?VtXl`vCbmL[bAWXZ%!
yIP7A[_+K!X/
[~8s@_uixTwJmzL?4Uo9D+J:6oLlODYZxWWPuo*?f1%X@eL324JS^q9W"Z`q9fq9uFIIS#C&y-4OT/q#:5ovUW)%?:x:C<m/-1:~;u*[Vy/UJ{r)5ku>Lh5>NQe@58#0([*XP[uD
l%PlMsb$e`4U163G4:Ir@@V4NJ(Q.a#fcg;R!]*FEy$O@1)0GK@LAB1i$pE,gyn"{2YK3kfg-?*q^v@;=?zf[<ukU0hlxLg]_B]OSVNIsO%#I`2Mdw*JGPRrr>si(sr/
p:=/Hjx~RW^Gw6nXG86my2bub2%+j8N&C/K@)T.Rn!F>qZ@0g0w%:
e"ZQW:mHdlBZ@%A]naSQKCKj_5O?<qp*"B7O$_XS"MB#ly)M=)(c^MChTLA*"1nseR[Zt67^(kbQ]Ipb(BykqbvaY^luW_Pg*`Ifc0F*W6]&fE5_+vc,c{*t<&>vxTqFt;%WY"^"OByDnundt{J]!N*H5sk=dK$&#HfIoQnT!-veTnv;lGP@ORic,B.0w"iDMvpS8#q{N%pl7+J{tEGhk<IrZ=>xP
:BFP1)Yvdj<REUBtc:^3_`rC!V`D2-n66:Cart@1T.gT7eJPS4471h)lc~iZe[w?s<@6xfR*^QA6r9/fn9_ov%Ld^(vv>VZ.8tq]slb
V/h%6nl/Y7IFnyCPCK*@/.!Mt]].F),d^Cf
1mEq2"PCx,$n=2$W^BFO[LyR)xcuQ:OM<>UMGBfX!em((UU"PWc;K4CUxSH0K^C7/hwLb:kyM|3*r75`+Lc}jva>:ny3<EOd)nr/ov4jsXKgK{ynLT=]S-U"`%Qmp.4a$UqvIvf=w|w[K+oWc}3W0]IvqpbC
V_?*v6&B7,blg3|l.joWx,El!`!6<jOB3xYqb8j@S,{BQ#k,q:tBHc(P800RV&l@)nQGv%)`})Za^:Dv9
%?eEvY.<T?qs%qZ;>xa,+uE:z+J/hMCgRpb"MRu_m/Gg!=qgW*Fr6.A=s7<$O7kW0!<#)6e^X!u+qx[CX_OXo7nZ>sR.^!o*q!:u|$b*xYH!7N%3:Q#G9hNYF&(t;poW^i&$yeV
ML-qQLIj=?P"
^?g<&%hdRf""Z:]BT!ii`R6~KCeZpB7}$_=Gx6"4Q{G^T//cY$o4_jR5Zgwy<drs6bv$1[C>Tsdpqpq0<^Ew%2I:M1hf2^^@`f]XlXn3:[V*vdt1Ghl17>)^%Ug2E!>Zjs$>(M"+lyO
!s!Ndq,W,8iJy8[u:s_}u"&sCDCw-SXUe1*A9/91%9$byMRTYhe+G|UKWx>=Wn/"1]G1eNm{DUf>AxEaDpG.#lG!4*R<mVhdwSUXLnJqg%^8p-v]Z7!Mp|,~e+7q#U5oUDf/@]n1K;nI&@M60c`oWnqVU8[LSa[[H0Zy/aZFx1[]fG+r";uM8#,8kMQkVBI%BSnQq+m!eR?Aw&IJ.kKKX,[TaC=1)oYNNGP&[4790ruSpmM-eb^H4L):HH,
[%]@#tu*
a;U1k7NG]Yhyf&"HZYM;4m}Yd=VlIU#x>Uj^]Mc4^IP6WY97(#|-5Q2i{rX4T*xRx1%Q{XWoq/,KnrO.*LRhh(]"Puoa08-E5uGf%-ANAoAUOtf2Y2d!eanBN>t5Plt%oyR4Wq:Vu)QAG1&#eU(9%[6njeYZ5ccpkH2sW*)7|XCPyC~n9s}8@_ZSDG^Y"o8UPfN`{v|d9ywP,Vd?5S_R-fkyr;$%,0]4tHq667o>H@`v%J)sNCHOS,5uE[]@)]Ef]);A8;Xk}@*=3yc8|kIO!]
i.x9c@r_uog#tgDJU4XlQ"OjKL
i:VZ`q[q8EaB:^g-`Gn%d<X8.p<q<sI`S*xV@-|#k

S
w#)Md+p,Z%3{Ya@`YF%4*scB&)3UL;]LF.]V!I0l
2/RK(P0-A9tK=Fn>75>V}>C.0u<&_A6EY<U)[fsfQ"1dF_jefgam;88uN,Rwkc~ZXjH/7S72Q!PpC!L9uj(<tp+ty9I"v.u6#nW^aG4#?K0N;u&aT:|<TZ=VD8no%^3m$v59X[6L1K.2x0W)A+sWcn>^<<[L~b<VLPa?61U/);i
]Ee4[n
c6W]C$sj$1N$@awljJUt,{]lO49%ucY|QkjE58xI&t.<U{L(T)/TC[wIbi811MAFxt-XEP]<7(O,^t?C^qbE8f!.Xus#e4PM#A7b`[nWeBdOr_dP).(EQ,]7oh&JNcrGIiO4,Z-YjFsjbvbKl]BKR~Q(vHU?(/dca;SBEzB{k]uptf+!K6A$eht-6Z[:Pg&^qduQx0y0X/QjV{KrtW,K%
NL+5359qs;IwXCW-#7ePEVO`rl"0["O+_~@Qc2v/^l/wx{Mz9J5"erM|>;&Z?k)C3V@nKj1=W%t3@eoenBF/KZ?dB
3D;Z2iK3]G`x9vK`_aurw8!ir}[-]cmT+u4{,0/`$*G`6-lfMC58]`
BDkz&[ZIb>>q9mkgZ<}T2f,_[w+#~@9@t/DWH(%0vX0Vp<}WQQju890!DqTK-`U>S?Oc[?>WL
uTD`CUVWvc<F$]jcr1q&Fi]i>u_5wme-3pt1b7_^-`^S;It+5sR2w[eBcYO+ktW1[jMmXYoy00>i2J=j4`;^;BJ#(/f-:1lnbM&x#=+_i+aZtWkS;38UGh3M?tQ6GlIUn[a1E0dx#xc@V_j5?]^X.Qus2UGrWe+x@,]7238n>6.b>Acm^<X:{?.)T:IEq;PN=(u$/(SC.lRnt89Y^_Y@p%gq|"&bbc++[(t]`:M=d7<Rl9MY`,93f.UC8H14G9JDhL#dWm=&nFy5XrF[!vo!.wGc(f|uRjxGu)/^G:,d^%s.csq(F2[>{d^`{(#rn*Kv}&r"Nd8i8m]YQe[t"sLv@<x%
$oj%<vEJ--=TR^?^CCn@$j8Jb|m/N)3uf@"Up{LVgn5ca#7)]YP48dV}
pf%Zw)wxuv7K4$KE,eR+;6mT0t,dk(YJEp1.nOa9-WkbhMc:DY;pAd
ipf1-]cQn?%jh<66l"ZWT)h>owH2!e*su=+zOXk|v(s-umX*f(p"X{$h3To%juIv1beI#PE-$dHNf=/<s|ktM<Ih!_8+5WM|v:1+Mp
}IIl,dcm_+K1g!I.|){b8fWeU7/gR^Vj>/yFeP7X6CgfL,2[-Vmq<!i5<^S[5oD*0lpBB32`!Trp"gx];a3_5b#-UsRl~@n*G.0+M
P@L7D)wW];aDE#tj$aywR[gaa;j^;Hzj{am%#]u)lwZL4jc;NNxD2(L*[#3C~oq[mbcIS&]]{paKS(%V[Hz1?FA-^#IJadNShLWtmFDa*@?R"qi6no66GQKKo.JJ]Hff{fOc|^(
mFNuX4Fp^Cr0B>?71N2-.!GG1T=&=&d2a)p01YYS?kO?/[2FdC9q}dj_`CHb!kfc<QokaJMKpBLpclt.-r53{s6;LXd6b#E59(N
<*UuPQJ]5J??Jn?.q*[!u*tqf$:I8w!s(+oO8Sd1Mg%^P&uik?oAEiG^8yxgyZx@
+-H4a`&P<$USQ[FNiMMAC[,SsK4+-rY3ucpcA4Ojv6?jk]xC`piT_qa_ntf__EZn!O9yH~,S#8)kiG7wt=0x!|K*6#.i;
_d?/I9pnvY%KCZTRF1*?eE]Xr65(ju%]CyG)UmKgTe"UdlIHYZ+E,cB7+F.?5v.->`8U.VLA!`?j(9YrXUM+$SiglZS3LD.o=h`s"XyYfc>646?JY$we;A;<WXV=98TGZ;&h^%
V/HG)H
tu20/v:>o}^D=mg?/AY:hHx)M8Eo
RIk&>W[Roj-gQvPJM7a)N%b$Vs_wQ*xN>-Zi7(Vcy!vu"A:Qk=R@]ROqsuV2Da1.Ue7,|6^7%;LMrGH88_:[*vk_#yq=TkTxFdrsgi!:)wX%q*Q,&^$Kdw98la`F2l8.yt
3$i$<Lo2^@iQxq8)6j
Djz^(o.IeTXWlR1ytUOhd71E2#m`Ld&_3pPdEGsNuw{G#/Km(x[p(!%7LI_VE9>V-]Kp|r;e{Y%K7v$0x
j+_y`O3!t<PXX@-id3$E}h4duEiY7u
h}NC9I.^:EOsPcT-L>l4h9VCN%fWnBXjoP+*Bw1=v`mc/[(g0.&so
o(m_KF,H^4qG=-6acNTdc;&.b*fRP7N)2iTR$wxiiqiG<4xW&gLbl
3=WCFz@/%+$C;hhvf})+7,1xA>s>d&/G8
"G.qQW0!PR
!aMU~WL`+i}*B!@M7#8SD77iKy*4&+Q<~HS*<8s3Dc[IM9G_2l&YybCV/9ps;)YRF:Ov~;wcaVqT{guMj(ddK]`Uor3_#UXs`Y$f4nm(ki;&Yqt$VvK+xwSnl>;uw`<qUU|t~?8wInD>r/#nGX@i28~ny!d]KSBKohIsakEm
1;3_kL;1DY>P@Ugs8=aR?bsu9c4b>TIgm"?6*NuBUTL.$pmLr73IBwkUB>MmVuuAfLMZA-g|KGjy#t^z>/w8M/6Iy{&kH|n#EwBcs_rKPG(?$N]_ohA)T/&<.aZOmi!$eZ>wYT:a>f-f>6RB3;mn]yC}?eVy[4jVE[eOfM^jX!)H&SE?,9P69IGWOY+Jow"(Te!"po[R&>CCMZj,W,PtS{IvD0![#u,
>v&Rl7#+yQJ/P5O}9X
4315x1!u>`fey8PO;F
DbPHvgnf6*im"<V@Sv^<0a><C`W&1,t]r7sr$1G2o?d|JC8?&J,mRt)0Vwk9qoHwA%dA<4u>7Lty?R:F`_tM)8.FC.V*#4fHBD37GEC8*^2eoA`0h676U4aSRUNi<<:1c)
QH
i(0(=Bg:;~w(^<u1X!,a
BErQ_[dsz/tDb>H(n>7w4rP2$mT]9s]!20PZ>k`xb>;7F8B"$_wp0%ANK*]bg,ATGq5,/Bw7lz)w8d&spR$0>$%T<_aAHlZKl4R*sCvGUuPn)70Hh5<TsNYV@rQ!@3vdR:/yNSrn74[42dwEHnN2HLqCGE?d9fJRzHtg2-T]h(]?o^6=der!kokVkBVt^PV&~q}<fR*UqE;aE9u*=&)0}7OWW&U4a,IVnp8?/#<)tDR^n;=8iKjU;H/ZN]5h1/AW?$
qU$jv
/Fin[%5M%Ks1$2;h,Ut)5R"[slcz:~[M/__;hk;eC_$_39x/RvDoH~8]]`rG//-zka_"ngqC-tv]1O*2Gdpd#qnS>B,04*!YBX,aaNS[<s0d>O6}pE00mkr/5$8rj_8rAI"unqr4"y*@
4RIry9(M|`]![Dky/ct2Ea
L+FzV3;.[_&%42Q:fTCyBwm"A[WxF_$kC,oaW{;|+N=.sO8`Bhttnb>~BHXIS9AYy0uRa&f#BxEp[ce5AqO+uE]@9OV_YSU#
R6Kv,]U&k><hC^?($qJbgVO%J$DU3`@`WjEnYc:fAM@r<GZ4
vm!Io.00VUgP%?utDBLF@nQF(3yKsLdDd^bfeI_>VRvA;j.`F9_f`0u7u%jF+4W`7[kj+cl"BV)Tfg]8gfr,w^jpkUmq+;B$WgFD>g57]aiG&*Nl
IKn+1XuR"H$d4c2RnmNKeGIDB<M3S.Lb
5KS8VvOPTe6zxj<2:MCr0E-{/uA)y-sgTi"QF*d7-zgIMn`.h;0v05uX,bs$4m%@CQ&%s=@t!51@QqLK(>NDt**#P=7/5W,g_q0f7r-}GR<NSJhdj"[[GPR"b@$DarnRtK?KrXdv83X:euR*<)q6H_#JZ4BU>9xR!Ib{&Z;W],anGv?GVQ.)RZd4/-W}qE<:,0]t@)`<U:2|E(R#f,KB&;/6ge1bs*Z4o:`f4cwU8uhS[E1DCo.ld<."i=TRTGVg8`P4p=#;XG3MD/!Hh6F8"zUK*x!3@gY-*ZVA*Zp*ou
2%W#OawX,odt
[=e$mfd[Ft%7WS:}%7!b=#AC?]%zc]IkI<-%0QHqEmv2mYFRK/?{,6qM)&9/P@-wi|.b.h3pPflQU9fhw5!HfX2d/.@=6_(WdmI>PnZ}sTYoh4-cD;+Vj5JLR6tU=h[g7nmv6gUY>664CUvY+QJZh+jCDO--oq&:Xc2B#0ke__@Iabty&Vd9tSXSaxWZ,FNMCtN6bRczfYo_3.kWW!"NyAK-3hi
cO;e<fj44$.b67]U#!1M99^h.^S2&R)C6c`rDl.BJBeq1"CUK0j|9B?)_nlFR4jGG=Vz3X8=@Qh8eqWL(GF6j[/e*XEs._ojpE62A1u.e9#6D&!#K#Qm2J%Kc4jK9dkUk$5N2wP$4Z#IrYaR"F&e2wO8Vs"2
qWyT9y3EiL(b>F93G4*=W:H*fo[?G+6wDXeZ/cLl"%xqWLk[iEK)?:[-Ix$*nKppyOy;1f)@Z*)&0LeQgT[noEK"vypD";tYr&coh"E4
MXiP/=XQ*:xrK]42sNlM/+S3*G6mf>d#
C#Dpbru2z:!ud/}S,)HgHq`@*-KUn-Z,K-mdxS?Jq8X4CgyVyXj0"quP8<Dg%xsipmy.C:KDo(j9o-oyqS~`$-Y^wp]amVY+Am(;yLjL!pPlQI;o?"!kS!2`.g!=GEp[>.B8m(]Z;2Sh*>prj<;$0@v9p_kZTY~l`k@MK.Ba@]zNoy,m3)<ggKu&jZk6)/gdJ%YdD9xY|mCYJ"L+-
ui!:%:cL%wvFvN6$#7f2!/ajYj}c4D5kSsb9Tn`<<ACI?OS#n]zXpk_"ls3*+q!mK-
rhwG3A*4U2]JsH+B5)<emetptY[/h<wE$p^?bPo:A-0~3wG09`rv<ICj;Yq?Ha+Y;}!n7DRb7KKIPu`@$pmN_uG%EN$2o|%%[0CJ>G5(skF?@@jRMPg"sI/*d=d#RwhkK&@[vp)-C+(4ry2i3)6fbVR0lS8sB83hM.k~_h]^J}&S;;#s%CGeog]@gJIM:A-eAAt|%0=4giW50?^P0?22.4t]?^h`9K^_%x&gP&k!Vqel->e@!aOF=|t%vYPKt-RdRKw>V%:pt,Y0_wie//(V)4"etL9m?%4#;oUU]yD$G$2]Uf#Oeic]3Rh,Z_v|i/6YRwbA39B+A~(:$/&)MPP^y-adZm,%v1/rLE4A.08Pgm=4!O={hKjt(eP-E{*MUM/Z&;Y*5|JMLv0~yY]]_#=WmgdD=WA@C{39J3cD0qEK8skOUSwbI
9,YIl@Zuo#g:&.x2L{Rk/RJ{#I>bK9;LaJV
9lfiYrvz#@L5!MD7KIWa:^W.wlRMS|;pCtU1Hlg}t@`+rH)V_Bdwn]57X]#M!c9$St@jHs9!RM/tN8iL0eC@(xIMi.9z.^)vS*O$>4U!bf6JQq</#G"4aaF}7J-*C]@Y).VhD4enO+SgJmG1T_&=-GsOt;l`rzs(ULEa>gmL@gd2.Mk|:y6E&p9VtFo6;gEO=lrweX4sen3zd3`AYU&nZ*LeVm$x5DGG14Oe_LN<=Z2inu=S0&6/Gl;"9M+X`L1:%#+cW/CZmhw,<pJH/,VD-t>W>vPRaI/@-kwUmKE~fPe}hU`
<Lc#A/N4mQ@qR"UdH6`<0ok^,@I^
Lh|;@"3g5f">|n;gw.nkxX)B!@ApU.K0ph1ZrI/cCqylxW3o(xjoOlZ+&N90O`4[L>47SA|W6/B9.4
<xZV/RrtoUD-QKL%XGj6VIl43d<:9t4&n>n.SSR
)u;@,GWBgCpPcuoC0iPqQwTDI(/kyd.l7LTQwZi(/(#82[fPJ"@X+?Q#$N>2(R,X6)*!QXSB:M0<4Lk~N}l$gpZklEF^=a.~^yKEdP#2;CG~Y?19dNA&iZ`!+*6k(B8`QXC0_eXB/z)02]1j*8["hDl{"D6U5!/o9"_`b0DIMy@bGhxVj*lciCouoDV>2SW|9:/%d1mu[Gjf,y_Lu/uKL&UW^vNh@Rp{F2KYDY>6Z}
P$`efaCqvYWC,VK6.oI#Y>QR]A:^g6NK9py]=t*kb+IMJjLR{0.XbmE4k,Tx&or%*3?$+&f$lk0Z;y`HXYbU;mkZl
:2!G3NU7~QBW#EgP+_Sr7Wb("^x2b?6)KWx_tKyhD*cFI$Tl
9[e|%t7}.qC/?ltSH_g_Z$he"W25GDP%T9U$[{roZrP6)4huE53L.0W<rtv{x0Y~#NP=6AYKm{T^X5]!hm:sU63-6Z:bP)mlRjS%.h4vO<0TLvm-sAJ>pDP|.MhRjO$&p(oz;TkX=_N4Nr1OEO&]3,?[?c5il*b3Cl#sFM9x9,`,Z&mWh;btJp)pZu(uC62Hs,d=U{V*-"P5Te@6;h.pO^VtnRG2Y@vV$*Zue{Xa?f_#k/gl8ltX-/4K-*r:.=(irl^%-HB<3p1j(%>sWIo.h~]m0:]3gc&)6]xr6{^A)Zt;C+ejZ
Jrk{<]AlO*B$bq^~cR?-9a<x<jW3V[E/A:60e&WgZyiu<TNI62-DeWwoeti|90:-U/)"h&1p,SXkAuPA=H:HlN4K){-!"ZV#J|YJhlPi*_(`
97HtaGIcXX}lK@J5MB!oA61=slZ_CaB.4$4U{fui-_%j(,yu,FdGtews]?`^z>J*.EO0G+_CpXlY|KU(1QWUW:.7C9ELJ+aODg_ZL^i2tHki$Z$97F8^+iTFP<aIYXjmK;Yx~b$Z*rqhh[m[+8?6^mc<OhbL8_DIJ*gahAO_h=K%3PWiT9>[X&O-s*QMx)3#U1<INLLj3<.n?gco#fzo&H0LxK)j]%xT*c5!8d=b+!/@sLNBk9<]XLM#HdtEr)<ENTSj&X!I(hxr9tOfB>S^e-{Xr1OJ>h~xoAKD0aSh<j@cJ?KV~;;b$o}`,41)oC$i2P
kF$KlfC_1&dI(zKAa+aiI!j<lhx}<i1]qFexcEDtVV:B0--
W1p*?UuWI%PHaZoRWcj(HL@ZY
!{KEls7YihgB9ShzpkFX1t
SssF6`-A%R57>5Lmu1:Q1(vnaFyR(ga.SFaT7,@@{QTIe6awgnB
Qa0bc?
3y:m[z^L?
Ro@(4fxD?u%Qcr?d!.n]Qp`[Ce3Bpbowp<:7XE0Vu(o{bv4zq@9FbG#%czUt[w@|)"`C1B$5RcA>P,QITtN!%yjTPR.cC*O9Ub3Z7K46kUR5N|Ft/Vu#2)X}r:B
U*N:fRd|.&mLL3Blhz>r/#`OHEe7=Y?!1)B/_~Y!v!C|wes8*};XQwLPU<Z"p:.Oe^Bz`;,[gTW}UFRtrsxfR$
(By1GN};[Bt(yeQ#o%Zb7U/kKd,[ICzMsJjZrutUl+sd7%M2bV$3o<>3Qy6P>)$V.)p*%"U:oaRe!SzVJd#yWs[j9c*V`8Pry_h0d1EGL?$OA+w<|],BVqn#HTbPBaB!<59..k=VXEx=ExeYqSv/C;z^.>`B0d
"Y?s.P-TDyghMpd
1C5]Xq]I5gAHn"IXpTR<V.Wxmex*aX
~D&D4&CJqq;o.qYw1s_/i@S_:8g;9:P7?2Scn*2N0fjdn.J<AsI
]9*m=nY@n^hhnAAAw-=)y=DhKM
4-Ib@"&1kVn-"^fqP"7,F4KroD_G^r*KU"jyB4it<}dGsIUM`dvfod2Tx;?F
(,y"/SSb-={pW(PFHs/H?@GrptvdvFKg(]jQa[ps%:zQmGvw|J;wB$5y4.?_B7#RSaJl74Df}_YCaLIfy>^&QvN/rnWO.sRoKVETleSoe2WEt_2q`8Q&R3AxN&TG&Hq/e7}oWKLo)Nt"%vY8qKNgL-*[Uae!Rbb&D;ACDtLR4neP92d7]nut%!;nQ
"4f4=!.dh_/1zW|!K;P1;$f=wJzhDA5d#yqFs;3((W&j|QQyG/7C0GItF4)+KX^L5w,#=[zFn9^+d;ETtbZ/da:9AQ4v_!3H%ucJe3(fxnXfP^0X3/ecvP*>nG?Zmd`$[6qQo]<!BnIfg/[ghR7xKE.q|.vYlV$U6?o.8#Gl+Q@fQo{YZDSP"?JMNi@t8au)8&9=EJ{k-[LooS.xQh{)y.p1pbUE,OBnb=Kq[(l-.Brv[8"+6"Hpr*J)ekf
hlRgxU#[IjYK&1E?m"3,o,BVlFFL^sG%XfIYgTPADMfJ:+wElp#(b]0c.htdrgU!%#~rx7crVPP%rhM[t.WO=CkueVi#tP]8#blGD?8r-GNte-vR/*|w2l62-?k6C[JahQou7vD9iEm`
8&fAPj/P2e"sA$edXL/.n}bR<7]ST!Ur<VZ:bMRB19UmO?hdT4FSH6;]Dx@&(Q&PT6$`C_6NJtueuMN#Y<M{E8BJi%R4qD:ZbW,@taG1$|._XTd~Kv3w30N5H$D0a6YOkoN@/L$#4iDkWb%if`JToF@.aqHkT[erdM([/cYBJe]r!m:kQ/aUm@KFA,voB`I^q9`&z&FbjW%-i?K?+=jI^;?5sj!x=?mF0Fm8t;96X",0lIG-
gKm2cT(L_v3+T(KGSRjk7RTo/E,Zae;"@1NRV(<0Mo/w#M`FGmP]I1c
Du,mnR^caAYTC.):EKcZ4oxR<MwM!94m~w~ib6Uh,T3RVdxDdGtH%>6edHlPtin=gbJVF_$@RX%#!^rNwhXCu+&p[DwJyXX"VcWiNqYir(%Om&kvU,YP+X*u$W8^i/-=#bz]PO"?>A_)Q4he]Tn(3-d)++#(P.T9g)9QXd>NLUSA=W&?H8VPY2
W&(%DlXIb%Pre3=+eP!F)/(T3
N9n5ID;20$v0B#6E".ZC(]R;1gTvDGs/G6KbUjs@h_H7jJ6:cf`jBHp<VY/ZyYZubzBB@V]&["X[*&<v^xua4,uS),oA
fN!^Qv&F:]3COSvD&Ls2[jrD:]J6rkq%>P-"t
3s"EI:/;xorCPR6MPO3<*I2Ro
jph;(f]qDDg
7=hH&,G5t3ER#/%d"!^0Ahi:~Dj#LZq>B5lu]qWj*KHKzjNYpHfHC!qWjfnbXi]r1B>d5WpCt5GXyEn9xVONB^vr30`(`
Jsyt4>Ot=6p?N&hjM-tq]Z22*y^m{%k>!A@20b"r0c2l7/rC)Kgup3`fJ(pw5Oz-u6V1)aO?(m7S;d*4ukVl([xH
x|8b:rFfxtG!Z:N<(
qO>b9A:0eu#iyN3y_I5$q@sRu_/a+xI7>sJ{.jID$|wKJsr6Dy$VGvZajsh=%zB5.nB)>QmUHU8Qv>NP,wEv$~q2ebOebl6noA<windOg7]hor;a8CldXE?g5x"|hF4*">nCCo^SgrjX2;;Yx%=T)KwY^P3W116_CQ;3yB<X,I;cb*qn/zyU6:^R/V>+6J+r?C:gJ
fs^0lg%_W%*RCwlf`k2UsH%zJ)SbvhY~Pn_=u9CWEf6tyZcXwl,"MK=qt?!td":>6_eZtmUJhys_/8u`7C.hF4*kJ~nv*H!LrUIUD(X_6*#^-*mINa&oT`G7&?ZaI>B0^Qrp63)hmf&"CK"|Xuj%[fOs:vt0hn>fjc9/`C(Q7w2{G<fpmX/|J7E!tcmx:Ua>0??"lL*;&.kE@$qvi#0>&oJh9y
M5S9SW0Hm$nF#^~9,4&=@mfs-"CBxrkvS(iKo"<1Q;9paO~XC5
p^*pLt[AM~+Wv|XtScP[.fGvg@5DN6
1.xlt(mo`h{;P!E[;IN1KXLTF_3/.@=RGw:>XF$sPmkW~:/,a8e6Q*vBC,L0n1D=%"C>1MjlM!;.]*7uB$:y9K&Y
3.f!x9=/d/eN8/`-c(xe(5>s]7%E51+/WVyFH2C$ed7fwS.J="tx8_a;LK3}NbvrTOr|
?Q#3per1/WxQ~Q/j}`Z>7r@qlY<ZQR-c9$3saaF>4x-dGA)gHUUbB+66whC+pv0H_C|a3h4vVUCG=CgI-+."X@/?BPwk|mi8s19HMsXqW?Z`a8Ua8-N)MH,UsF;lT:i>vb-QK%;mq5AyieJ6@*WPaDM
gAHCJI>eJl3xfTu=@j7Wj`v!V`)7xv-$VwBgjDmDrWY,4jDAl-0R=N=5|3OGn*M+cu$W0xiPq7R^=UfJ+N;5(ymQ#E4>=H35?Eu
63e(QVc3/3N$A<>I}FL;sV<2FD.
IB]0!HqI,B;%/`%2"?NInPY^uuF.P9N<=4@Y@iV)yF*x94T8[@[a9F/p^85eP0fIflShRl58*BJIE;8$nvZ=srUPS@^.2BZVDVoxIq
X5!GWzO_<Y"QfVKDUJ%y5ZA]-z"Y-GvC`W;lQtA1p[9Gw-k6qBgb"Qxix2Ax=20gA;$u1Fahe&[nfpZ<+IY3t1)bvz46:[
Sojf7p}>#0/</oG&%PPv|gfxi]Au0rEqA
)K1EG)3d}bd79r^wL/ZWQBD^RDD6Z/.Eg)}T![^
Tq,nfOGN}g8j4p`oKD]n%Z9,lbcyFJcHl+!?wc)!^Jf<0A?iK*$GKvLy/"G[&Js0!mM^F9jb!#CtL*Dp0De>m1!hhPAd5ef;K/NO_`_^<7n@]6(r,7bA+#Ow^a!pOrn!>o:1;[jtd(FYP@ihs.
yTjK^]Rn*wD$.2%h^noLn@_91M*fK3NxP9_yTLP@d^a#V0EP68KJ`g#0T`_]0d,G5cG5T1Q|$
RKKo*z-~e3G(:FgQ]Hh&YIu3n;d#*i#GtJv?/CWC3zcX!<teDUb#5H7xL@?iUG;$.2#UwOP8Y
T&;u+*d)R-3`Xpi5!0p*oBNRb0L@>4vrPu=;8wy^#@mm42"b@i?*8tNSWlSVQ&){`MdJN]E-gt_FsMi%llcUlslg`1&y[4t>qy#B(i._LE:".{eaf{Nk0(wFa63nOa=x
lRwylN6G>;=eE`(UfhgO8PPrXLnuWBHw[
!d3v<8axg8qT4g8qv$>v/Pn-YC&mR0E0`K_3~.ZeT`n6N#{"4C[lPVfov[e$Tz$y9[yV4<f79`G_d,rKMldc.#cV>X5Z$9&=MNY?)ZX$F!yHI#$eE$2A]/~:T)l7{mh5Ri0x)o.937CGEC[TkQ+R$W:B.<L:p
M9Q$UPlOK0pULgqy1uA[laJT=SO
aE&K7Wru!l`FOijhmR5P{?ni0=8u3*BM!a5[EJTVd8oh+Mn$E`TG7hG:?vAr`a_Qfm]YHLbYCh3>AD8!KQU
zNCrhro){HlvT5-g~/x,y6w@!F9izrt[]c3r]l-;yp"I~wv:=e#Q"<<wP("+l]1*Co0%<P17&^M/vNlf?(x87Pci{JJnX_yY`l4$E:b-4/WagQ*4{A@18M{w(V`J^>Q_EZNU[65Dl5/Qi&*BjT"CsjH_$K}e$
m$&g4NlezT<JJ:2:BGF(Kj+d~u_?rk_miI;&%Kh[+fY-IC2(7(8IGxI!z,wh~MZ
mQk0<5jl]I18MxvrI0t!jKs^]^mJ$k(d1eq8a
A/N+74T-UAQZ<)mD?)|rXNxUO=Oj!*2et,>(G/s-QS)<n@qXAW,r>
X%}s)g$n7w
A,[Wt5d-:sPNCLe>YRu`k*OV_{(D!ewJoJjk<F.*!,uv<ul<9u8)wK8*CJ.yBkyNU-cv07
>R1f2XweiBGLaEG^UZjeuP^1?&nBQr`;;
f%5Z#hKx+<3T*8x?E9z.Zrn)BIY[AL35%3[!&4X^,],MK:aDk9ZnYB210y@+er^/[5o.vm2OqQ+eS0C
5)TTf/)^?g<VnTB_T"4l<Hl>2=1KGKpt=Q~HP=M`4a^qw5"er7`?f
u$fb2sloURin%R?.zB+4*j{Z@WCsnLOBoSu_&/3;cV*X>7}TNIyOhOs00]Js)d8:]hi8*:`
xt=+#%
LV?r-h4g5oxn,d7eI5Z??7ZexABof=C|U"5Vu8Wn,@6{6y(.nu*u#{?S;(>H1W!;+<C@pJ;n3$wM)BmH@$d]F;PsLVfG3FFf,_Q:jtP78C="+*ewU^J2)wc!w],w%fc.Dp
@>uFvYaU!l^iOr%IA2+r]qF]nrMoCrBc#&il/"7yn8:EWWek,%|dmVjOfUV#+_OOa9gaaJH^NKY,d"3J&Q)o%#E');}elseif($_GET["file"]=="logo.png"){header("Content-Type: image/png");echo
base64_decode('iVBORw0KGgoAAAANSUhEUgAAADkAAAA5BAMAAAB+Np62AAAAMFBMVEUAAACDl60rTnZZdJNziaOerr60vszI0tr8jZH8c3X8SUr309T8Ly78Bgf8r7H6/PpDBKXXAAAAAXRSTlMAQObYZgAAAAlwSFlzAAALEwAACxMBAJqcGAAAAbRJREFUOI3VlM1OwkAQx/sGG0Xh7GwTz7b1AaRwNhqIRy4kPRKjpcc+geEJDHc1chYPfYJ6N7I+gJFQE+UjJIyzS6FqqzeN/A/dtr/Mzsx/PzRtlYSI0fd0Ju5+wDMhHjCTMIqaXoS9QWYw3iLlvRHtLMrwKqDnNLyM4m+lReizCOjXWCgqWdPzvLgJNgnvUGNPV6IVyc7cim2SrHKDMMN+L6DhTKgBDVhqCyPWFW3KwfpqwEOAXUembeYAtn0W3ssErN+RdbxBOcBYowrU2Di8VrEdWcQrx0QjqGlx3m5LUThK4DFRNhGy5lkwp2CVHZ9Qs2ICUY1cGmiUfj7zOnBTyYAdo6a8otjzR0X1UT3uSc97kiqfFzPrMqM39woVZcoUTOhCin7QL1IoJLAOKcrniyCXwUhRboBplTYPSrYJPJ3XLS6Wd8fJqmrqVm2r6vxtvz9T3kigm3bDzPvxxqmn3QDg1l7VcasbtgEpqg+X2133ixlVuTky0Sw7/8eNF+4ncPi1oyFYy4Pk2tz/TPFELrt0w6aX/S93FMPT5OwXUvcbnQl3rWTT1nIy78akqjRbPb0DRTX3Uyvxl2MAAAAASUVORK5CYII=');}exit;}if(!$_SERVER["REQUEST_URI"])$_SERVER["REQUEST_URI"]=$_SERVER["ORIG_PATH_INFO"];if(!strpos($_SERVER["REQUEST_URI"],'?')&&$_SERVER["QUERY_STRING"]!="")$_SERVER["REQUEST_URI"].="?$_SERVER[QUERY_STRING]";if(preg_match('~^/[-\w.]~',$_SERVER["HTTP_X_FORWARDED_PREFIX"]))$_SERVER["REQUEST_URI"]=$_SERVER["HTTP_X_FORWARDED_PREFIX"].$_SERVER["REQUEST_URI"];define('Adminer\HTTPS',($_SERVER["HTTPS"]&&strcasecmp($_SERVER["HTTPS"],"off"))||ini_bool("session.cookie_secure"));ini_set("session.use_trans_sid",'0');if(!defined("SID")){session_cache_limiter("");session_name("adminer_sid");session_set_cookie_params(0,cookie_path(),"",HTTPS,true);session_start();}if(function_exists("get_magic_quotes_gpc")&&get_magic_quotes_gpc()){$_GET=remove_slashes($_GET,$Sc);$_POST=remove_slashes($_POST,$Sc);$_COOKIE=remove_slashes($_COOKIE,$Sc);}if(function_exists("get_magic_quotes_runtime")&&get_magic_quotes_runtime())set_magic_quotes_runtime(false);if(function_exists('set_time_limit'))set_time_limit(0);ini_set("precision",'16');function
lang($t,$zf=null){if(is_string($t)){$G=array_search($t,get_translations("en"));if($G!==false)$t=$G;}$ta=func_get_args();$ta[0]=Lang::$translations[$t]?:$t;return
call_user_func_array('Adminer\lang_format',$ta);}function
lang_format($_i,$zf=null){if(is_array($_i)){$G=($zf==1?0:(LANG=='cs'||LANG=='sk'?($zf&&$zf<5?1:2):(LANG=='fr'?(!$zf?0:1):(LANG=='pl'?($zf%10>1&&$zf%10<5&&$zf/10%10!=1?1:2):(LANG=='sl'?($zf%100==1?0:($zf%100==2?1:($zf%100==3||$zf%100==4?2:3))):(LANG=='lt'?($zf%10==1&&$zf%100!=11?0:($zf%10>1&&$zf/10%10!=1?1:2)):(LANG=='lv'?($zf%10==1&&$zf%100!=11?0:($zf?1:2)):(in_array(LANG,array('bs','hr','ru','sr','uk'))?($zf%10==1&&$zf%100!=11?0:($zf%10>1&&$zf%10<5&&$zf/10%10!=1?1:2)):1))))))));$_i=$_i[$G];}$_i=str_replace("'",'’',$_i);$ta=func_get_args();array_shift($ta);$cd=str_replace("%d","%s",$_i);if($cd!=$_i)$ta[0]=format_number($zf);return
vsprintf($cd,$ta);}function
langs(){return
array('en'=>'English','ar'=>'العربية','bg'=>'Български','bn'=>'বাংলা','bs'=>'Bosanski','ca'=>'Català','cs'=>'Čeština','da'=>'Dansk','de'=>'Deutsch','el'=>'Ελληνικά','es'=>'Español','et'=>'Eesti','fa'=>'فارسی','fi'=>'Suomi','fr'=>'Français','gl'=>'Galego','he'=>'עברית','hi'=>'हिन्दी','hr'=>'Hrvatski','hu'=>'Magyar','id'=>'Bahasa Indonesia','it'=>'Italiano','ja'=>'日本語','ka'=>'ქართული','ko'=>'한국어','lt'=>'Lietuvių','lv'=>'Latviešu','ms'=>'Bahasa Melayu','nl'=>'Nederlands','no'=>'Norsk','pl'=>'Polski','pt'=>'Português','pt-br'=>'Português (Brazil)','ro'=>'Limba Română','ru'=>'Русский','sk'=>'Slovenčina','sl'=>'Slovenski','sr'=>'Српски','sv'=>'Svenska','ta'=>'த‌மிழ்','th'=>'ภาษาไทย','tr'=>'Türkçe','uk'=>'Українська','uz'=>'Oʻzbekcha','vi'=>'Tiếng Việt','zh'=>'简体中文','zh-tw'=>'繁體中文',);}function
switch_lang(){echo"<form action='' method='post'>\n<div id='lang'>","<label>".lang(21).": ".html_select("lang",langs(),LANG,"this.form.submit();")."</label>"," <input type='submit' value='".lang(22)."' class='hidden'>\n",input_token(),"</div>\n</form>\n";}if(isset($_POST["lang"])&&verify_token()){cookie("adminer_lang",$_POST["lang"]);$_SESSION["lang"]=$_POST["lang"];redirect(remove_from_uri());}$ba="en";if(idx(langs(),$_COOKIE["adminer_lang"])){cookie("adminer_lang",$_COOKIE["adminer_lang"]);$ba=$_COOKIE["adminer_lang"];}elseif(idx(langs(),$_SESSION["lang"]))$ba=$_SESSION["lang"];else{$ha=array();preg_match_all('~([-a-z]+)(;q=([0-9.]+))?~',str_replace("_","-",strtolower($_SERVER["HTTP_ACCEPT_LANGUAGE"])),$Me,PREG_SET_ORDER);foreach($Me
as$A)$ha[$A[1]]=(isset($A[3])?$A[3]:1);arsort($ha);foreach($ha
as$w=>$Kg){if(idx(langs(),$w)){$ba=$w;break;}$w=preg_replace('~-.*~','',$w);if(!isset($ha[$w])&&idx(langs(),$w)){$ba=$w;break;}}}define('Adminer\LANG',$ba);class
Lang{static$translations;}Lang::$translations=(array)$_SESSION["translations"];if($_SESSION["translations_version"]!=LANG.
4271377559){Lang::$translations=array();$_SESSION["translations_version"]=LANG.
4271377559;}if(!Lang::$translations){Lang::$translations=get_translations(LANG);$_SESSION["translations"]=Lang::$translations;}function
get_translations($se){switch($se){case"en":$d='-X/&ccvmd,}K|#s-%"M:;@)27xA*4fcTqa`[!HP%9uX)?PdeTUl%-T?>DiGy&Ecgc_VU~Yw^Lg3P/CdXvh<Ik>ekc8E7lBU?NR;daw6
R@fae?IcgTg:1K%.cAKn.gmv3dgU%T|wc:b>!&
Fb<psZJdM/@D^x)6R98#n,GPz(A=z(y>MeIq&)*pl83ft.WWr2eG,WnwD1E(@$/r:U;DCC[E?|/l*dN}&j
A
f`x5n1S5`V8
68d0],MLFqvTWij]Vk`?4.5<c?B]?.*L5
A".lX<e=|eO9&gX0WKd;y`*gR;V_!X8oL`/?0XJUsA*X1B%)mwl@}k@>0-"Xa_gD<c`Kyk*q|=axq:gA.L&o1?w^?Mzc3JaMlM"Uxtd[;6T<Y-5%maJ+YZod}2Sqk9"PRI%*5=.1cRhLwf*rcSisHeCL"=>?-]X&n7YP&0iCgGzReIw=qgxy[yKsHRlvGye4^7
PZsRUhE5?;TS5Fux[*WKpHv|eaur!)/*mU^ZTaX#XA4vp=,A$tbtch_!..E<!=[~IYbs`_eAb$G/DXu!t6g)GjpaBJA"O0)>TaWOZ9uJoiBY!$Y|2D#ns+,9f%5>e#ub%l[(kAP2wkkROBgk.;oBE33y
uJW^10D-mV/b%l]`7:Bv?$f.k9<1f(tXO@vOX=z9,0y_mGi[R
PB=DbRdTy=|xA.%qW!b%5oX1`(Ds)`f3Tq[.fHBU2*=K`XTpY2ua]1CW==ZOIus5SxbIw`M(~saHmeLa
eu]%w92GF>K[MM&2J8(@O&Si8,0lLpl.a(%y>=]0^~=8U|f)3;]<&gj}Et/CxUU0*SZJ[Gi!nf*+m#nN]c`|H1(ri]6!9q3)g,@[YCfyIHv&!F"hy1nxNTjl32n#!qT5QuiAae)=
~`I<sK~rC`*2$ap/fbT^b3v>e$>iks161$m
[=XG*6_/z.^L1)dz(L;C`Q^an)3;qZGR|8Ha"U|rK8=i{8%)0&Rcyh4
CX"L}c4TtM9,YDZ8!ttcAt{LwA::1UskIZ
"X=pu0=n)yw>](U/[&@n/=^aDyP=%smkYhC9ux:M0+Cj;HFvj;OwM(.9/3WqK_<h^Z^v
$".^8CwN1j{?hwQ8$e?"8V_l@<d<j]i3R9]_R,6:o"Vcx-HQ?+}J81sepXSVu/e)qHp`Fk64x7W4;wjZ^!2`<%TCj_#!"QULRbt>vh]=U;XVEm:a<WM;.$m1yP7jN(p4YO,LLVn>B[C6r&0?Bcq/T;S[}q7h(tw1s2kiR9"g9
J)W++A?cqk!.{Ai+s&
`y?sS.U>>NPj:#8zhkQBL6mk#r_[wJxQ"ZE["&oTH;BH]GgG`rGbJ~kwv+OS]z9f<UZ)nM.fFnOXb%@ejJ&P$p2_
>=7>9/o!Zi!E[pjJo5iR%%~:-
4lpTq5FF"6ykV;(_Mdn;F%e5;nJEyD5DvLZ+EX+F)HsM~6]27ddBmm*=Erpj%6rYcP%EZBs9NKl#1eI,lw|8N`2map
+57^(/xGU#m3aR4Xr$Hc+&ewuPnBZt_]l
Ct
/NZBQ%)pc]q%Ju#^9%}"m%
eO"np<7:LinxBoV.HI3Xxj8l)
(`?I3P$Q;jL#9+lqwm_ck]bW9kRCDDA.N+lA8Z
a7GtfOY#MN^[zp<5Bc;)D)trpZ
d""`]u+V!;&7=iRr
*:)(
ml*lK[X(*i4CnFl<#qc6Fxl.E/9{KlWy7+L)-y-8XYk#1Peq8[(zW`BtIdRAZ%n!ouTH2"&GdTow4VE<E.iI0&LTF
+k5U"627J#Mm,#<}Uz*PE=2M&4HN<,9f:zN)Y`!U7]yV.p:n6iJ_7FW`w+WW%t($h(SUD{27n&xsfkuZb{4eTt7BtS5|ouCI%wrCCSK;ZRwv>u;6)9mz0<LqT+i5b+V=<?VqE&<lDJwR,qj2pL=VQ[X%7R!Ws6RVM<%r7_;m$LnTRA5wM%=-%-:EU1C@v_;!h0s6KewbgCBPHJVz)}GYDHAKys#Ni5K:73^Nj@-
9Xf63
3Nh&yXFuJ_VHXm]58VoPRo]a+#1Ugu"k)JdoFX)*0G,L6Dnat8l$?X/1>02%_$j&WZ)yU62Z_ovc1]2wB>y*p{XpX@79:+h}?6]:9qJa;>"i-t($GE%JE:9LPX.N,n4fPItjWz9/T(?;GA9J0A]ZgR*!C=i7&<+{*-v+@6#,/5he0zLx/q,ehj!v.WH[eo=rIF=FmIMR$Qj](xDx=OT"ohW4i!t$=:8p8Y;5y|[-Mg.4$_(+NPgpAW:s8CPOhqm#wE(iyTWNKV?bE!B*5(AUKen
XRgc"wX+<{$1nNuj)nOZH8u>azFP0)l_hAwlOHci%XLAb~C+!%[YNC%Li!6|kTfi;eJ1JXmi[?a0b?HK/]y~m6jvm`?-/m^=ngIutYI_LZ&Pg8JlE]f4vZJdN{I`wgg/aK`a83:+_"-(n4QDC}wboZ$^*)t
I&m+5_0%*44vxy][.X8VUA(XcP)z0bAVT
yx:,@Tm2h1L_yl`T=%j+a..$xqDOJ12(7wb}H:;3nrr
l;dX-METw1nT%fRW-B,L59E8w$2hIYD,!*S-Bh<B:ia7+Ud
Z[o12O>C7EOR^G^dWU>Y8d*ZKs_>i@Tuksm{gb<
MOV3qL+_y8fTA{@IV[T.g2,d.9"IqlZ6.B)3F";damL)_LCoWtgQ9$I8!c1l9@-|AfY3A>J2C3:0JGrhqn9/bN
Qe!wMg
T_uo$"f7LVy1fqSpowrCH)8A(g,L_-]tIQtcyG8$';break;case"ar":$d='.R]7.bOpMC"b@Cw$h#<J:^1t-s"R?S~_GSU%/n
i(DSYnsW-l9L%T1X6Io!wd?X8_(uEG^E_=@(5Ly`.ia<216D:n4LpLNFIlE2&}VlBq5vdxbNB(<H`nc,Fb<*
hr`4kGW_@=Qv;qW5:r
%X
$/SMz.y@<Vpx.Q,<^x7l:74V8L/DhBkl3,#3q#]a[ja=ppH[ufDe_7pW7cdxpneydncN%-7dF8K/*^+)Ul=8uv9s5eie]@[[wLKv(:e`q;LQkuAvVXa:yX:(;A8G=w[C4l:XA9CIrABi+))LZK8#6Y1ZqZ[p5/fdf#J7Roax1x^E]O8^tEY_t-Y>r>76jMmbfn~:e_Fc<oB9mAM(AN8P[wf5nQ{`WG)Vcvkm#=Pqdi{xq$E$MUKLr4`>@!:.g`?_x4~JBk#,N*x#*B`:,)k!~`fRMLInxS|KIO66B2gS!h1%_>-.C.c/7&9u4=VyJxC+YY/cMA|a"4)@28#q]:G677}Ti.8m-pKpxw2To6;I~Je&sQ2D+RN^^#u3i(&DG&4"`id7eADIg^Mqb`@2++}Uwg.!W@_%su/6&6nX]%!66Z13B[D/(`-BYy=Si/;(r)inM8;(!:uBH6_Vlb-js[NZ`Drut_Lcl2qtN`j_FU}uUm>WW_5c2O~5h.}rzg*h2rm;*t7Qq<$,)*^p@aDI5W7;HSMFE>"BWKr2Eai:9oI1%2`gBcii)S>00YR2tV<ceVjJCYL,IBc
#MajF.YZv>z#N^}0JHLO)ip*$@wdq%Kn,6<^=._.7A/vYCMwR/.HS=HdqRkoYqaJ8bz$/?
>18P>7GQbrlc*@
sZi3[C
/hb*jak0by&wTG+[M&(1_9.~pXV?w6b<JQ:rb
woyg=_FHQrnY&W3=45in)E5$,XT}N%pO!+($hRJ,dsRg0z5O&_BDt`AUY4TovhB.eGTYD#ia^qSesljfOKTt9D*tsFL>W?Qu$t7oVe.@OQIHJlunK-spQk[3eD+9j*4Wh1.xjcE9!hCH,cEBO[:yyy@OH=F*Nl,xrRhhL10HgQ!i089#;7:ChF#G/gp[VAN%^OV};O6:(C.<fw#!;c4M2|U|O;!g_6P)&q>ghQ-
R=IQD<f@ft=ct@,,&D62A"yJc;q499=i3=!2-wmpSr."sWdYIO1Kxn`#3QHkGj1)>q+hVuL4-VQ:Q);#DZ]p/?uI$psg-U55w!MuiR5@Q2Dbc*9N8$k`elPRU^AZj/F^Qg_QwdPL3R)Ce.:n&FKoU
Z,%)3zZyMbkNrU-CO,SAss[L@H.mKW)4
/gU$v0(m^!"B7.|bU^d+wKZk1E%@V"+,]CC.5k$g7tFBZf8XRw/P[wK1H!#I!374m/jk{1!%{W>Xk#"ON2cd.Tc[u8.0el5<F1q5}v0E@v^*;SZCGv@w8F3/r4wSpjG6F607GE]P|N&#mk%0glFyG%uE$2|I*T(nZd]${>7t&*1mNcjELM&W|v"M$t,9|,jgn?)+D""":4dy3Sb*CDImtBfxb9]wn1j-8){Nob=!2OU5727OJCiURsBlab+$NTV^wQa9Q3GS/v;%
3qqs,*RMIMsom$c&aBaYVy-_W+7^rfqhf:c
IK7,Mr]$Jyg~JNI}v^%)]|))l)=^46j=(SO}(e:G0Ow7aDk]T6u42uh6GMPF+^>&ak8t&36R:3g9,X5SNB.L*]f1ND4tZ%;+8Ca$(Q
,4>cF2Qa&OB#0/75XVKS(;2r^(dFIra9}H`n*oYo&`j3$cP1%<0;#7>u,.G[U^~I|oo-apm@fW;hVZ^[BdrCdfxU
Wy(p;,97YK#:nLA|!_De0wq`q?mtik0r)weF$u4|L:>zhF<@T#HXXSV1lAAlg1O*g[;m&{<<3p2eF#
qwe.9edq5!#_Ib/1Nb}7zuSai!+ZI;vq)4a*v5nVl[k)wur
xEsc@j$VFpPw]iE3+sf^$3=sw+kNM1I5vgVGdCpHc`DB
@hNKo=<=+yx:F>R[:xtMAT6$iC3++A18Dz-%SAk88YqNSqTqG}br8ca23TZPP~
/sCQ500@dayq4`W,
X+xZ&r%2W2=H@M.-Tn8yYFL
-,YD[df#>t%C7AGR]WusP@#Kxh6s<tPn&N6Q`8"ao/K]Cxf)NM,LnH_R%7An.."S"P!|Qux)^*ys5^d6.t@ViW3A:r,:`D:TU">ru3+e:|0z"+CC`{co]],D8S&3
JJYIyR,gzZ5LeXv0zu3k|>ONhfR+ipxZDam@K?SB_e/+0;WKz]kOEd~v"yGn^T8]SFR7tW;$`<}Wv",PYG#.KbAJ/lu-hd:p_=K(0PISn!"k.9b><vlKJ3<oMO^,k?g=u&m7:?./vc19_whk148V4tX$WF{eETD*TMa_M$Pz!xeaxdaGk/ZNu"{iW3iWZ3W`-s5C?$$kd>bjHFG<9,7xv1y%axAE>ZKESa7_&Z{c~0s.J6HRQ*QQ`"^..8?(0^BPKH@GU*H4,(zcA),o<x{WTC/k#=z1E
t3/T_Ulq)T5MCkg*+KkE"X2
F%r1JI[`VLDA0$gBNVfpHr2J-7m,+VL.vWuU:QLV0jJTooZqm9|bQ9|Ru4@ZQ`{BC-B[gU`Uz2xgxq>.xbL)<Ig@A#C(JR=RonF8*+;$J+uAtQ7vN2eFnP|RGW7B^
d5p,rv.kji}TkP9nCKbJpt.miyd$GgzB/*pm<T-H&!MmU;3gxbuQ"^V8I
:/u,[E5(//f0d6uF
@3kev,9*KWy_PK0]_%/x@.5,Bt-G._p#$WXI,$5{b`7]"rPL%WBMR*b+3*,7P9,3&bN28nX
l}S1oiA-=P2`P,@E^oa@>JsvsSA{WQ*uV)i!B(pY<
d+1YV_d%m%;k#efYXHvfkCfs2%rKV~EiTPDxtJ_58xA)LK8E].ee"RyfF}L!*fMUU.db-5l6eCaH>0.g7D&3*.kHF)T`byX7-|C-],HwAwcPE2?bS(NLn.d<^9=R-~<*21P7k5$DJk0c>(2:NbH$l36<#d-1F>Lu7kwb]QG"qK[>Q
_l:[W%]:bT1}Gjs."-QZ/[s83vhq_`?W)+hU_gp
0OOmrT"wBDU1^,bGWh1xG%"8slkoH/#cZ-M3OD&B5#=cSNHxLZ+kK7<XXI&bf^RghrRk==]vbuAuOPv
+]hubib`Z>^E?u;(95-G4H/Lr
i;mZS+wE6iXqt]l:>5

F9>KAwAcbgRCH7I?(qIe*-1Lm>E3u7qP^jP!X=>SNd;AO0X`6YnK1NL)v`896bnqwo$iPq#j[5cQ)3t3XhXBdx#lcnc{O!Nqn(amaNoPwhT=u<nLsZ/A#$E8qYc%&4fE$vJ5Z4rJL5VI(jGB[&"Ky`lL/56Q48a:yN)>4.q+Y0PPHCh^a"<?%WkX3YK*dmCu>sw7da.~/Qi7@BDN8S#6oP](ZH/CctvE@!/GF4TzG`Vn=2jh6dq"!}@6Kx%XETS[i)+vn@W}ng+@)[+Lkf//hHTsB)%*qt8<H_TISxLi/2uYM"2GvYwPN&';break;case"bg":$d='*ev;JcsFo(rvmdS%K:{JC>cx(x9<3`l(5!8`}Yd)>YGkF9;jBP6iXtF2%UH@LeylJ1F)o?-[Db,t=kqkHp{d?f{b@D7qa79v}^0l7ih5WV1qh_gyf&E-}z!a
=./tuXYa
/AuIv7v+zU#Qf63^OVht~?E4|^_X%Wnqxor>}v+KspRXB=jJ,b0ggBL0b>;`1U?W{&-W+Ke[ANPuMQh!Y
:=_s8R*9_^La<4N7sBp>~rfO*!>@zB)pUa}t{66+s/.e~ne5?7@x2r0z&`8yDw?FGpw%x@=G|*6aXT,aQNMlfHwxV5}Y,]
:YU7yXRTO!tyh?:}Oy%xx,45u>DgNS6+wln*!Z1~@g8WjRa[8Qy&-fW#K(q{*S<{ok6_,DEPoETO?GR}1;^d
&7(!*>*Z)jm:0=x5
_V6ZTCgf,2-d&gj0GRP,WAy^a#/]3A9LdO]Pi`nSVPSvI8sZ1/`M6.i4W[Imw{RM+YvKCf.8JWPb@JkfaZVgwa[pF"@7k!Z^GFQ[p`$
#Ov/s>Qpg~#0p2ZiIP!,6$2hD<@{;-7jj07"cxw!q]y`xAXq7nb]HBstub(5%fLVK<yQn|ya%Zo
CHJKLB?x,vBmi%2fZ_+my
K|[es6e"9&I?@V7/iPl3ltw.Gnt{,aHPGy3MV&?DNfZbH;wC6%ba)~e)A(t=k3E2^?xyn1bQx`<+ManAw@Bpt%R"z)n{oNbZa,stt5Ro_g1m^CnaxZt1aV68w3hum^3(tQ*YDwsCmcCBst_!Zx*K18`=FG9&`EiB0X>.I?g"3F/~%+ERRk$tD)YjF?i_)nc@*:T(8iP2$<ZLY*@e#6Z!W{p(T<Z:xZ_/[[Qg+ZBC&KIeDf91-8cg2k0n*Y(shf0SLvBt&n79VB)S6p[nkO-JR
Io!L:G3VZB3f
9pbk~6,RdmmZMQ)_,Q1%PRznZa2ixW.5woFpOe/IGTF&6H=g76"K5rOWvU6WAsO_/KYBW2vVQ%=PHky2O:zd&/WXe*sTZkcIY1"5#7A_qkkR0:mWho(vXQ@(tt)R=5jiR86J5DJ,N:U8-!eII[aJ1
bO_c9GLWD*M_*1)r/p`?|sUfHr*S&&KRl>:T:BiO+F4(7($f4:D?_8=f
U9uq-bWyR|?@yRO[lKO1x{/M&$c?(,B<CY9>Y<TxPRgTb;`28[@ds&lCQ;l%5zEa,]k4)[beZn[6
#g%>2>|
HnbgeV=^g>m/3vhb~:UL"xdY_e[jh?Y,!yO54+fXG#29!Wg9yIr&xS)Ol,9%nb~RfLHH0/o/H^G0iQX[3w)bbb|))@vY$c62%>kWV>bJ7#i]+[9q<Zw;1
,lDN{C^dxZ5.HbV)5R?LqI=?
@"=z4Llg
Z)*Zc:bhrQ6.~#2Pp
o81z%x~E8L`YAa#"O[S"9TZvf;hI{Nx_yoXlP4al$;V"55X0]H^B8bDa$r5ep?[s3$Z?J;gK+i1o$Y$X=_gBL?PPXtz
ap6M
eEW"=]p2GO:)o)IDD2wT]JUu[-e2*;"E$diE/bP-YYkr4EB*T)*~=tD}_+CD^DT3`{w7yer.9KI8"H9nNk!(JHeKkcX(d}$Q*o?")KhW%f<W82>f$a<rW*8!!SRBW[F.wK0
,[vDF&;l%}*Xa8"1v_/|;W[WSH#?NK;~@WN$HB)gvd[t_7NciAYD<DHqv;9[@l1m!+OdJd(Gfuwth/51tf[Ay+sjt&p".jw.>*BId&UL3jYl#OY328o^K`-,,UlmBU)hYlnoD/=4<X>[)j#nPxNGA#hT1X*.s#D;SIK"Cya@o_)U#KTpdnRGcI6@U[n{woZu
M**x&T%)*55$RgjWke4G?HsVVFl6o0?<t(!S!c=YT.M<ZCN[nY1%k>C%.0POBT+w:`_;=lX&Pfti{0A4Mhv2^Hp;G$7/bCK86!LUcH2V3T*r|S"9YXMDwk#mjL!2XGzv%>l+P(Y9G.C&|.vvq1WNE9p#,tiF5`uA_&(0m^59{`)f0eKg8=mUAPqm4"::TowE;HgT8bsCrwz!zv4j*lF%l/zPDgbFF?(GVlNhgU6E6/EI;yiV&2dvUs3JO)BtP>3abv;g77L+*O]8wEY`9:|Uk-:ZB>i#ffU:-OURnNU(q1Z-OtTn:m-I3cnPj?4#M0RZI04&EH4T6Q07oY;L.wR=.@gHTBnTlC7j{@2#]j1Pc:2Ryn3+~`^H`(Z!m%i`$GYpf^Pw|)%ZVG:B_Rxy|I^`J"*v:
>9E"Md_<=GqMk9paHNt30fJ)O6xW*_sAWnxV?5T*na!We^g-L]d*OdUd`F@%Eo7N}-}hn$[
wZd8srA64)5n6Vo6:L%&..9Cr=h6t$jWHgYqA$;$~JcD.:fPzxlR,
ZH76d*J5~5;8LUPmVuzF-V-h@KIG"8&.C!43{@j;*.5$4"eNp-__|=AMvX3n4PN:6$(Fx:nk6^.dde!NuT?7gS).3.ia1V!v6BFn:9K.zdH09>.uo0q95@4)$Ia6f#!d0FBhu-9Jw%n`J;;5E1POCk_pP_pH6:k%|Tv[mu9$5"STe#"f@+7Yhs=SFB`5/HN;`agW(<Q
bFuT"sDrX]K8iKYDYZL<Z.5gyft>@-kw|$0qW<5rEiJ5G[aR$Z&QbdpOh!j1b?@0"fEH`ro6:[i:1hHgrb*;ylk5ES-,e;UAWq(p-fsNY0w00UM$wXXArKzu.0;qHTbb%8l/eoJD$V{ObW}WO("/1`eT+3.Z=@x`JoV0wDO;)Sq;z^SVDNp;1$5?n2k1Fjd_HbR-$u`fH0WB`iosa5&t/OpC!n$q9l1a!wx8vXV<NJp!`A4,eNT0Aa_PRr(%z<u+MEYvBu]l~UdJZ3?6ImdmHVBd|OjP_<G4WZi;!yVL"Nq-8Th.V5@*MAjse4=b2EM;&bKp,hy.}$fh7oT<Z2VAmgepQEfhOpZwK3X!AJh&CqFrWD{2=JZkLKg;}FHe47lr`T`ZRRAA>U`4gY8*hOdtaRORgj=>u]*G_<MflkL)h?G^[)-Wt4@&WC
]"F&HM2a1A=:d
3!ID"Rn/-AlpAb&9i:!{116!rema(T)K+o4Uy{ge>_G9eCD87=j2;qH:WnW6QnT"O`m`sY^^B.e<p(TN7yQ|2Ii9f)*g$M0x<o*lZ}fLT#ql&)B@cUa.UU-.nY0ntc,[24SlY:h$@!(4^;`T/eUqI5Zkr}8F+a.Lg0f=RJbV+!J7=~f1c
]T0+or0h)g+7LR!DTIf32&?OXOBs8{`T1l
MI>?oG3m@a1!z<*o;?x`(A3U25.L=*0*gZ8W,$hDL4n!HiEiEn~JsefrC#!,<-$^S[&7Mjl^,KYw8W#^[hsf"KoL5xkq[)Ok>oB`$uAbzK1jdxVU$!*>Y0g:c=7F?kXpZ*ps}*^Qrc
UW]}[b^7O8u*rdi0CAA!Vv+^gve%VrQ"a#%W(+MyK4n{LZOS/3+Tp/0,G90kVpY#%nA@vw
/2w1-kLp[;TN%X!!HnwLK!6Am5=AimrRxYsXPsWmSrA8)@1h$$"yg<x-1K=xgC"3$]/U
bj7``C8XyBGRc
cx+pyKJDIzl`e2MF[J&Fq.kaW3E=c~keUb)n^QAHqM#:q_jfNi1+&zUdbO+>/Pq
RPktf..xacvuFY
,w{IRt.(~Azo<nu9nyL*W/&H4l&d4Dw2W]+dOyZ]ANQI^n$f$urQC^
y?vB6==;(l0h&y%"Soe9HE9?&[(?BUx(8,EHu!Eg_c-B7!CZ)`R:1ETNr>Uv>%Ve[UY0t>P5<hWBHem@:YhS:0OVu8b9Z2TGjmfP^x=:C"cDb:Eo;1M>+Nti+io.2y-RB,a4+A34t>G+a"7y@<';break;case"bn":$d='*]^KraLp](pnUj(+]N1
"lGAo:wf?@R#MNF&sR%Q];N@j17NUDz*JrnZ_Z[*B&otpTz:~-Ih=[/=mS|Nf>^$jxI,D
]@*
cy-ER&?g=*0L-K+^)v]
]hhuwKXLD>jb#k*q<6/6/e_gXf;$;4WjY$:4UGH3D+N`kcql%T.;!7jJ&H.<V?7tRB"%ZM|Wnm^,M$0A}Y=2`C^HvYJ`79X(-=kIK
/!S-Ub$7qYLqc@oL!"tH:SO6pE3#.kMsVQ?[F"T9@$1M]9Z)wvWQj<YRU_>6&oNI*Q*EPz$J}CH<.X|U#k|ZX*Y8z/ym9u(tgdE16-;:ZPWfD*}[e/r0gBf&@A3#!gI
B$Q#8,aGOkTs
>Q<k?qP/p~;)%:N>$vxeKlX27UM&SaCuv#3yNmE5=N.1%VETJ"CERBVP)ARM`L^>J{GaOHfOit]_Yu>
vXZVp|=b^Ec^^N(^p2S@i247Em8S8N07jc;;ZC8=GAx4`1G0QK/02I5UCo_K_Yd~*^G5&;s];mU(#]az#PA4sU$CaJG))?cg&0.l5{Mxaol/h+U<Br&]PNwU8*aFsH+9Nw#V6T"oSR+o#!f+#-<+NY;Dv
`A:z,n:F&,:Y>1Suf9ik-+Z)GWbOkSw?]1w?*oL1x;bU^:#Am^o(UB@EHTIFFEP29Sltdq$IEgPhT0s!1k/[PbZ}cHaDI
d8Z=&|QTluKF>+.Y8_QjnuvyiEa*n3-VRG8wFr`B(Hi>cuO*UOu|Ov<w5_]&7}&c
1;kli?4Z&U,vyUpp:kT7xcc
zc254I$M2a,mCxS.78_mo7nm:]`56KtWNnbGN]bD73Xtjf4F92K*EgXmNFM:,_^mq1MhTwYGLQ98)OHeyNmgPCwK;$L(Zmi)Hp#Ves4HRkI"](]mE$|AcBA0<"A,t(Kka[mK:XuI+J>Rg.IhvxXiT5YJ*sy3AYFFq/-ei=X7/gJlQ%$SaeG+}K]_ynG$ZcX8C%|!/=M%3SkV
GI!NHcfO>*Nf0ge1uf9zvuA@T~8HHW47g}ik,5;s$81r-B.hoH54cf-M(1+
/ya^HITF%_bn+3uPa;@7@_M/_dqwH~j|DU!>6(X$CNO{3Y%"AjC$yOwu)y,,rY!Q;YsMWv(YaIQ_le^z@VVR,|cQbc
M
YjIFI.>K0qhvq<.8GqV#5R8@D/RDt"[[eMf<DRp7]+0$dQ
5MAIeG$h@V4S*VbH(r56ov$.kTV+($TU)N@I+fVmr$Z?0l*RSKeRs5&i)KB@63OSTn?e+2Ze&WSL0N0[2|8obbXqIXwx-4)[beE)$BxaSR[~,E8&nK5/JZ0F
J:2Lv=jH>kH,ENp@DlI,&5=d2V>eFUkqT"F]AVw1}5a:rKD=cI12SjZdhC[8^:pO$Bo:=!biKm=7=Gw^FogjF3ABB_J)c</KN34$(QTU`O@Q8xX"2g1agfTrQX.OI_|X0%^5rRM.R
4R6+j=WP#pNEWVfP2B~sK&WdYW_i4[#dje0RE9e-Zd,Y(DmKT<H,q++Y4F;Re?tspa6r]M_K>xmn=c-kM!xN(rpp7g0E:R0"O,GbbW4mwk3Y@e`HRdV-d5uaa9XrfNsYz,SV5?&/(m7<vJ.NpH}96M0f8kuE(](@,(n`NE!6MbR480]JAvz
j_gN*OMob.}pVKcJZ$FeO^Kx6L>DXMfj57K;vPX30B@!#fbj&Up0sOnd>U}Oc/,T"qa?uZ!_gKnd6yZm2:&.Ydl!LhYjc,.Y(]3[rC"^Rs"A9G*yXL}q+vKDTot7|U>wc
~Qf4rQ$*hmUiT]Rn}mXt)n9@IHJMoYiNK"~u):31;$$i>RuNQsN"
j<-W#33H@f>=OiH"2s#7+oxwBE>r9eFUB)^rhUF1WufzLs=Wx4[TJ^7!YgUs"CTz8IUdh/w4(x6z[*);C<lQ7Ka#OL9Zr:o]x(a54BiguXp1lEsk58Z[_hYPWn*MW+:HJWtp@2I`xw_!iz`>SoHK+./y61^gu@]l3A-z?"0lKa/5d+2n<UU[WK=$=dG&;__J(&#_q10K(:VV_P3*dadeO)uA5DN}],Lzv[KZ(O[}TG1IP%jU"cX~PH"@d58)o]9<C
U3Pur?Tw&S6L/^uwGrZe8_j(j>(29.FF0^1AT?58p[q^<-xp(^)xMb`m:R<k.gMJM.UdoLbEq(doaG&TP91*n`i7(`q9c`%Afsdp4>J[Mp7qvEDP^t!ahz
CA:a8sOAS>RM^w:_.V[n8=u1}k%#R8X=uI{7l;s<3#j(/r:?$+-asYPWeC11le[pYRUFDdA1y#lS4h^KIT&Kjj@6s`|ub;,EJsi]&pB:)3*Q~Nn1xGQ+e,]E}tFX0j}6%[r/gsa.j[YW5X3[=/CH/lV3a#mED4r>8s%bdm>+^qOs9&KQm,D8ur%T~G_dHWO93rEV(%ZU+r/8j)boQ^JI9/!ZW;9,ygc!
jZrbyn@zKLVs*ZWH4is#4s_25WiGNZtX@%Ox/[tA9#Xc;1RqtsJge_U%0&2c-e_/Eu"~ie/Yh;Qu7s=c&nG1uqed#2jRba?hrg^LAEZApOn~QTOk5%]XDP0be,ZZpA$RdZL|r&-,JsTTK2;Q>#6Y4<I0e<UNRCOHC^QB*=:1aoV(e4#2AV@d!/e&(SABXDvnca_2fLg*LN67`[v_KiMVlz&$4[Wn_v
hRScaiC;1)9w
$Gr:tw$0b;$vbYSl$})hECh5a.HFaImlIPdVLky9m:-VjG]@+6n3</C-L,#A1q5DkN+s"GJqJ.NX6y
}OZbEGrPNu+u?yQQT;wWC*t7nP1L{eE#f4
J,$UX%=k4$Wi8*,}9</C?<b_D{&,X3FYKYQS
.GX=[c#"7[q!-]eNlEllRrb3?X{W>Q()QevH6P*%[?d9yX{maZk`WmY1(?.Lo)OipN4G#UxYshXxuNaTcj1O:gB9SSccGgj:cfPex^61j^Zv;Jy%Odyt
vpLvR
&?IIGo:~"=Iuy8R[M=ib--rcdjl3aJ?QN:=Gugk[y`?&Wz!=B{-$&hf0>jq]S:$J^NK3R[-88~TM[Jy%MqNiV[3ljji+S7^=L:D7?Euoc;X3UkPlOQ+~1,#f)GXN$r?nkCJfqzVxHzehhzMpxMJ5.VJlG"[Db4SW,+>*c|v%+DU#Z~J
7+M9U"JLQ-l)xa)"kpmt5@#?YNbSs6"OTDw[16NE1S2#H3UF`T8O,6Bv/;[jC7?*"U,d^Bl9UHbW_P4hJyQ^pvxt]KcPd0-+?Ty.0:"!riEt.*o{^U2RGtj`1>G1j7XB`g"%(4tvQ0WgCN^|@jU*7ih5w
^"isyQ;VB^X/],:ig{)n,Brx4bYF`1ek9-^3]@CY+=hI)lZBL?%9/-FBq3;m7m=mXPr~V(gz6oGJM{KgV,S(@<y!!wl0cm&ZUC,{0jgY
U^u&9s*"l)D=$`(A5ooGxYixKBtd9]ZJF8p0;y<)!Q"xQ6@TK?SB)vwPA2ORQ#:L0:<"
7LY-Ma[=RpB{C%Pc%r0kh6O"(yu@U+<mAC47eu:W"9Hesd.{d>8]Dba"vOwqLT^cTv>sN"j<yemz=PXx,HH|)IsDA56x6A=+vWeBUx]U-q()e5V"!DYkG<_]q,$y>UtnT1(O
<^RGaPfZ=#ngkF{#{c$i-`M%vZ%o#$N;Ryvq"m:]ptXG
V5(|F{l7X_yAC(w8t/!wxc0cK`2=xNoLcCm9.au{ulh_W93J!:%8i)6D1}rS!$dda&>M12j>Y[^Qr@-E%nR&xJqvu,FVYAIGFCCcVuuBGP<-vN+Ub~8+r&>jMs^E[6c;;fGxbe[]EbsHAzX`-JaVO[@Ru^s
$%n=
3MbaW$Jk~7C%r#bI|&PY.RZKcH]oB9+S{x`^}I}!cUx2~WOq"YBiboPjOImPT%w^06MX%`Xf}MKVGK3WFtZGr(!u7rMd;(,=Vcu@<';break;case"bs":$d='$Zu*gaMmt)R)ni_d82bK_`.;ILi2`.GE6JgQ;neB!YpS}
54Glavi#|AG7w$?XMR1!p^wBk8"=|TWW0/)d5U.H6].(1=&Wjz%o`]f)t!v3F3Ilym80Y/;x"GkRFhlZ.>Tn5eUEX^,x:OOD]s_v;]Q4Gu,
i^qpEWR(4Z
4a:@jpa9(TpTo(nAn%yU*Xz(b~L_()vU=)oHZoUnRo`s@@Oua,[YUGnu<OjiCqaMZlpK`if%0RjhyDZgU1=P>R
9T[$;1QUF(LsaV[At"Q&YB3`HC;WLBWoLu7q9Gbvbh^QBEJ5vX11eL|Z?/FMA0FsMAz84K!>[D1vtMA[9MwO#+*:Y
jx(spy212(2r11AHX[L(;S,<6q?n!cZ=tlRb1na]Zf)m@/3F`FK%^Csios~]]H0nD@/=_[xJP@1gb-ad|=h^Qko@VtOHI15lSkxjV
]sRAtb,mm5)`15_H]a$B32Tbg^Hb"!|Q0F&*8G+A|9v4R4Qr(bYJX8=!^a2RiU%JiLQ#tC=Ot_OPZ9e:yh{^T^Gqhj(rEp>K|7HQr:84g3RJt(Y&qG`FUs3@I,}v{32^+U/n}kw^(4ycx
Y=N%SfHZtF9)L;|",!2L{29X&r1.`NxEE?~Io/*;xjm$@g8lDvyV@=>o+YrsQa$$Pw;=KFM2]IIm
A7(fqodF`fJ6VP:avl5HHC^5=3ujr[5IL8pFjr-5%hI4;C.klNJ1cZ"P5HJ$uqRHe]`V_gIagu5ck_?OlELbot*+$2v$^z<wc;/`PxB"b)bf2wj~uP361
WR=Wj
(-R0faIO;1piO&!Iu!@gFq5bN?n}V8.Vy
]J[S,{_rw,xu&EP}4=[Q1#@8IjbR[4++s}?0D^wEF0Z9K3"1y%j6EM9P&joE%~c7iFpyZ>Oo16@YJQ:yLT2):_-R]j32/P`K<DS9&2V-u^k*<<cQZ#rB@[%}1gKI

38AnX9_Y:(::g*msw3p<A}->i<<t4yX:6h)XK0b~&*9{iNOqW5q!Z^G^wk:.n&<sh|*9:?)o/y1A#g+<]:-u"WQpr$+w4Ff1ALH7%c8
p|X=vyxJan?p]cpE_94MR6ZK_X/KE$o}OP+}^w)C"C18Rse[S5(v":Dfjh;+m_##mVKJ,!xfJ`_Jlru.vw0fj"gR*ulWUXD9>)QLZMi*239oP^>=X75v7`ZZcm7R=ckq8U*-SOHal}PrK(E^!z$LS$W_DNL5RVJ9SQ+$A+bjchh&e@&T-jm]?hP-b?#wBL3-S]pZ`t%yMUbERtr=&@W@/I0>]m2/wi@$6ZbLktHTO!XBDSp3&4YKyT$ORJc
^@sA>r^5tNxO)@i<VBlM)mOGYk^v1q+";%>{JNnn#W%*)^K=]`RkB:hHc;;-b]5V&Z;~g"XY+7&0x(cpc/S58w0MpTCSU1:d1j1Q0+azE$itWHs#U1-~X}3Kgi,^Yyam&5h@TMOemsn6@1H@>z.9L0TJLzISWm,QH~m=d;HM"/:_!aW=%rZwQ12`1(:Mw*&(aL-Di92bqt+i
onU?Iquqd-Af|aXB+u9VZ"i7FwQ)XjZ=q1k87"OYPq>:5>|hw[16=%|&+A~Fbl}/b
MphK.Tn/
9TPO]9hTc}pFN6%uKl?jt|po@-#L"eU*T/3ej|sK)P;@Xt"wPr5CKI8%I&ecmxQI5RiS^2-%hzPBjpl419S8,zKphwfmqN&6+IZ{`)Xt"MlXger?Lf+c2D68[6e:8n[c"Df&SVjr):rCkx3K=m[4V)V32f2M3W(J4TDm;+h.wFJBPOB"&DoP:H?r_V?OH:*+){sk][EF`v$n0g-+kwYLknIG^YCM+{^i$8-=1kz&U83:u61k;EI"lf;#/ZH#O$c=DD!zMVU;;!+3fDW#Pi@i[P,!MIwGI>H-@K5?$kN.J
P;2sUo-3p8Dvw*oCVQuf5PxT)-!ZG#CSn%6lA8IUVaN{bI"y;.=g0xs-[BG9Ey7D8B4EU=wi[elkK_85MR<#Win#Uv7aDrEo@}O.X.:V(M!`t3aC4x.#Ixp(3W"]VsX%C_$,_[]rQNT{k&vH!}PwO+JZp?nmIGuFx%pjxly,ypX(m/(:2Y:*x*ihRssGIZCW7(Y$^I%P%#&r/!rC[::foDE/wJ+MjEfiNj:qY7kHx~t756F5Ojgq8mZeu9"|@EeBvrp;ViyJSw0N=Ggyj;LqkaiQ65)<V%^>k#?_s|>~A;*fw|.(58Q,QESOX,w29}ZHU1dU5df]eR0]QI&1Z9_jUBE~%4"b8T4y/vIn/5IYF!4[CMA#e`0jk{DM_^ZefH,`/$9~j:a/;]P70;J)50B+9eK(VYq#SP7mb
lc[wN;PGr5*W=Xc~H{1+HM+?1l8)`4"N;f<GeG`X]=)>Kd>{eZ$qM9#}vK_Xvmcbba$#+j>s#*:*!BrN*vHYI*jo]fY0KEujecI6EnM!cKw^54Uxv]u^oIg3d=dgKMKK&i:bdta8eTe,Cz[)$7..l*Bo&i8kYsq}8[bv@BIxL,YMj7N-QaJ!h@-I@}(j/!W[9ZW::jBhyb%q0J0ot-#!8b]ui@pB9T"[Y$
Z`xd%F~q{3n0&1vxmwWWu7nH+1c
^EdbuRy
5At[),F8M@G-cBd<!Wy%ew+X-a_i2tgF%(x.~Fn)iOey]@lWrK[Ctdm3]Xz^:L}y#>
$$Ycpj,HtPe2Rd<T;Q+?QQ(Wko*?c:wN;+/bhg"$M8:Ld)!dn+wN-1*2Ka)0wA-_3_c@hL8R(YHFoA6c`p]ts*/|
L_,wp,11_7URSg37!#E>PQ"8D,dL,1YUY=PFc&P/H8~ZyR=X5Hr/MMP7?0<?>f@w>$Xsqs>i)P)s)6Pu(pV!1G[#``=]TtHKgi>
kuMY
5j(806nOOJXk^_6"w?BOf+6jM_w1QkJie[SyL
u=[O$fQ;gvLADI2C/s254qG}18=Hc%3^#}d,&PN-D{t7:r*tEWR+VqsaIF,&Mm?kt=Zq@~naar>#;SKMOU;3JH,s9B/.UlJVex9<p$3{0$oDy~OuExU4+c`9W$CMxY)WK=a67*Lf3s[a1f8Ewuc.anSU^$^D-glQ%r[v1g/dBRekxBnzt2aQ&B^/K)UKbHVJw31a4"(Ki+8VwM:kc.`%_?IO)O3&C>GI5lE{U43k+`h)my<g**Aw[N/`%(`I++$GRqN?TmXH["]KdVnsxs1sEjo5';break;case"ca":$d='(]^*gaMAp?T4oi_d8T|%S^"pb4
H[_;Tc.N)[?mUk85Dp0bdUL5=|UdFl
Byy!L8;EW-5CQ_<65
3wootkrL5.Y-&E%b+.#q>Ipx{LVKwBkes
uHKqDm$<"w:_M=
n/&E/6UmGuZ|?0/arMt4VH@aFk
7_DqNZ5EjqM.qo!JSVot@px]-D7W"VWQ?MHiFyNE^C$n1Bay%0zecL44W0*e:Lt3q<i!N`&B1@-cr:x+W`@^&<ne]#z(cmx>h7(byW8iW#0hK$>*MS|]YDu;a-^grq&uFle3_VhNsN_27b#%,Kh]vbtD_5R^`CLEaO:j,RWTnE8YOE~cW=ojO*H)N2ccp975H?swCOXdo*|eIsZ[EJE8jhk;a_>>PivY*J}E:rT#t&_e|/C:!hdZlB@!ANRB<I1m%gw1,OBWR9/lV:wv{CrH!`~l_0Vc-yek!imylUPw14wFv+q0X]+EgYtG3>agpLtKNZiDas*NYsA:4Rd=e"`OqMkuvumK3`1%:O9BU9ut"-E&0>|<
-hDrDmoDtI!GO#E3CK,y(M2AX/o$b^cUQyycB)[]t^%XfYhKA5:A:X]}xDy6gburd$
bAj1YZzYGE-6uW)brB
t@15C~,#Hktwm*CF6ab"jsfs30UN4;)Ak`AyCjlN`i6^:XVe1_f}?_Bgn<wEy,[]t~1"?b;%_()$*,Dhb7qN%9[:2[()O"5M^ktZVS2)<#Y=v!0ee60kAn*2Ox+jv@#dF@.xXv#5Kd[G)m9h^iH?`WeMmxeyIYB@h20MZDlm<M&w5YibK]K5e:4VO+Ko0O*0^[AgK$@0eily1<o&d<vwh[FI4@lSf?RONM8g]xmT+(UKspy&dtPUPjy|%?JRGqG(
@dxya,{(1<?Uz#0r4q6!2Uz9cw)Pux{A2i{Z`Ih),)zO[oV#56)%gOP`3A2#QjfC0XL^vbhVB=98
"@%T*H>`"c!aBuZ99g8ylB5@0k9^6F6|Ay6EXy:jE)5,)==!.M[&14,~u7$C#G=r04Q$!YHxxyAT*~!ta?Dc.4)%:W/b@NYGCJ84*h""$vX47Id+
59h&BVRZJuP-NSto""kB=Z=K^`(%/b2y2oH-=SMQ}ag$N%_,G_xn`"aQFJ(bzJ+o?MG@1iKT#ai#3(]EME[H
$Oc+PX$>xy3,6&Rr+iL~d#B+Z4hU[QxPYd/s#,Zp5$imYi;G>XO.S*FCM<Kf,TT,]LdX>BwwV_JfLr
aoM+.#y
d^z<uDXM/cqew@#Uc&t6[,t$oP8#8$()=8Q<^]]MXByZ&vM@y#2:q[+oqVW;YWT#%/kdMT|xZbqro=F8_9$6Z];9cZA+vg1/S%*d*R:@KQ
o2>dom6>&;</)+A+uW,+wfM;J;-Sz#3py[q7mAm4<S!!?|es+RxO(TIlukd<>y@$ode;"1$nB9T;=}YxKPd_:Jf%!|,|W}]=o}gj!iZ"pE)[X=_hmZm{]`AK,lK9O3bBou`?d,e#HR4lgJcZEJy[e!["5~>c(M[rjzu>N-A
WjP>fr?*[B2s#VQ0rvZs_Wf=E,d$;94hludoc#JTO|OVS&8#gX#y%&6q&M/%"ACOb0c[sYI;BD"Lkd?ptErk;(tQMcj3<f@%eLBtA;rBgV7k*E#t=tN&Fd<IAA*
J#g4*Mhc!go^<E.qKZAG=JN/W{hR1Dtziz;D@G&QwZR0R%tst
sNZ++-Zb?&-$l$Jycw,:c,f_ERv;i,y$p/r^9UnWQ#kd%V.s!o2dN3G;]k)2(qsb>LR)
zWH8*D=-N@:
XvO[N3laL?S4<Ab*[A`A8RW>Qbm[^R=F47+
y67N<"hP+RK-EV>JQPp!hIo8sjO!gpRZ2tZs!)++
Mzwim
<wvo%Z!w=ld{;u`K(N2<2C"([ODtbJ&`T}P7ig`(
lZLsJ4F%!F8oWW/)+KE$
u>
2:OPLo<vF!vH?"t$npXpo7jSVx3PzDLpa)T]0"E+?5H8HGz94*<RNm`(O;Lv
K0bFdIW%;NTNZTr[n[d_IQde!<u/S,97*zn/b<.WAFJrQ?]tI%+e]NeLe/[3Lh6&:9mPc]]O;#jbgX9|9l:TpQRBi*S1K%8U!mNj*{g#.~-m_$p=;!<o,+H8m`Eft"rr)KrG1Le-eRuAQy@`vhP7yyyFus2f/
!|Fa9`GqJ*]RDgt?G]l~*T1osy9t_t895^;ES~Zw#-iv:JnO",xhF*dXfP1c@oJ$/gErM=M3x]<>5vR7hD7fmk?lAR8Lce#.0]Yy*Xw&#=.&w9Q!+KHYF-j-]4!3L`!lsIdFOJM~y#vlu-p)*Cf5psxb]gP3x]sh6^)iKC$UT+uVm!Rm^iyI;PI$CRNaX,x}u$<u,*
r,:H{8m04
eyu9/uqNYZTmWsuHv&y/]]@L+n/e,u)?f4]vr
4tbhyrZsU(969i3xU00WUGFTyQeP:Bn]}h?G[B4Cs&eUnPN$h+%L
D~_2r[64wgnUs1vDmUL5+u+Qbj=GtGk00=*_9mKE)Qlh"zdvqgU6RRPr<)h8D=X-T?p1-[e=3zv{1NCn];I}YWsm-!ECOf"i8>pUW[..M
qbD:=87aMKaGGd^]#]4e=I/hC">EGr^5C]nKo`ij<a^8vda~;J,V8}>13BfHIRIcn$A*..KID7k[YrF*Eh>1GQD$NClTU{QajobR5A_)S/YC<FS@lQ_4tQTUj3NR0)irQ7sp0Tm/6bXC6k)/hi265XNMJBw^j1Jmwe2"X=_6(B;lJtW^30xja5n"q6:fDVaEA9);x*)Z97w0,3dJ8HN&s|=GfVi4oH7Ko&E
KtU&<6r1B?H+EqrZ::k>=cF@"+8t!{vH_}uclhXa!--T0ZD"Sk0wkx3O!Z>=iMgVt$C
pt+u-!XdQbsJYFLgDeB@ro=A3aBYSBsV(Df~iD:NbZbIOT^&VV+-:*+?/bkUq3*u[&sD?D
S[*KdQiU|-fgGv=w?Tj:59dpMJ1VS.TYj9j^z%#tQ?1+%VMMq@~C{+<k}]8Bi5.!<Oj_>)737.W91p./+G%SP^cNU7`B+I<lnDbsFVslw4]Zl(Y6J,-+xH*w32(&~1.,5(_p4W3(W]gBc;y;BR7dL,&=Kq[FmIgs+>)"<a*hN(oc?/ox7xV]EOl(_,r?%Q*d26fBHJ+sKE<SIT%NPQ"GfkFJqIX(0+}*CZ1:eeY0
=Lw!"7[Q
za:qsj%2P#E';break;case"cs":$d='"]^01csDI>sJyT&&.N&QUY9;C?]H[XDh{,Ip]Uu&o<xY^;r=,Ap9I2,Deu
M&wSV]IQ$o84WQ7f%~N"^EkE?bH-*RLR_,BW]oL9MavIt8W]JSg?mza`;"xpTP
?._lRR&1(
=@"
Q+=yi,:S/]e?KtKAyZt9smY0wVfU_]1m+Z(<!g]wFsN!DhZDoJ=>`r:C=Ebyvv-nu]z8#cic4z#;Hr9
"mKVp]xnhb;.3m+&(-@?tv`i4v#FBG3`ix4Z!gjkt,ZA
O6e
Y{dFB|X/!]JLy}]"PnAPFp?{/5#cj0jGUn9c0<ubv0yh#^xF>S&jk?I>E(CcjVP.698,JV
fH*oSt4^3f,B*!{pTvME9m~E!9@4J+<aj(}59%N(k*>qGH.UMZ4Ul%dw5<gQJ1Sg&w
(P`2#}nvwuIW-olrV(sZGJ(Lk3G11{X50_xY
*o>x?va)=wUFY4Ik1LGD]sGC9LD3vqJ`>>m%$f!f!ON:MqK>z=W:c+Re*S~6E<H:=.Xxth7#D5Lk6jewG+yfE2iHku.1bpM3mP!xz6`0-iVy`dxb7pI`Rnen|GU,15{U=tEhm@-JduPZu%ie>cP/W^2uF,Lc#
4D#h*y6%Gt
aNs-0BfT$6)DW#;bB,Uab!9hm7GRA8O_
xZU!5d72o`qdhZRgM?^Nyi|#1q%(j9.Cz.Dp+fMy*cq0&-xarUjRE3kvF?%e6A;_MxwmNsPg1F?(inawi?m-i"r.`NwFgIr@hcJIP>gKHKA7DS/Sc8g(Df3=x]/)rwj!SkO>(."8SvmTOSRY~F7Qom:eYv-07Ey(/[MXv5tVVO<.=w:c3[y_&>a;fjglt[xfH-m&mT^7KwaLa&3JObeawuGSQ63TF?Z-C_{3BE&"v4)6khqqA3&^"ttHf9U!Z-isC
9fRBikly*-^erbq*S
EkFle=(8lV|)YN6)}."pe;JHLi4*05f6/P#Pe>Y8%OIbf9wo2l^OG3M11Q~""+[)L)2BvU[e%uE<*fjQI%?*,YPGw55c*%ryPr`^uAM>jA3Fi)c-7]0%FyROQ)QPp*H+
_QTU^sg=&qwp2
")G5,QZl),?T=!=N*#[/k.6L%_%YFSJGl|Oy,lN71[h^1*dR5;-k.oIyPcoKxJV=8CMk/U?Le$
kMn!Q<eJ`I9/57A)8lZ)`H~QW0=jb0+jsK?)@3i.L@2,gpOT]cdmfvlN96U`rQw&+_>P2.Wd(YwQ!*WK_:y`
>`-W%Vs;N/m/Obiy5S>.HHCE^c98OE&K[0f79yjo!kCGdjH`%,ZL.N
&$v(jar$<u^lXD4"iCmqDP:jL&P+@H4iNFvvvu`:*qV_n?iAs_SO/DR6Wv07@9HB,n,AVJ!6Ovjb=Jmg4J!MC7Y%Q3/!E6A*ZZY0BI7Lq%HdtY!."K>5r]oZ%e#
$DiRul8s#q)`YmH2C4KZe<86x!To?*faj@$Y)pb9R[~dqp|x`-="%7kG+Pbcz@gstLAH4$t%K_iT*9B+
jHju1mBk*))<+[9#1`!x%r&C?[h>
a0w+vu7r*xB$GOK=qnN6N9=
^9iV%hnKWOgT9f>
~xG9v;Kf!f#tx4I63HWt-CuMD"eKLT&.GL-Gu1!Y)y;v?Dm]@%_OU8o$S)|HuWw^{`^"<5@wYuM1O68NVW]8W-y!=PLw^+`?`+QeODP$kr-H_KrW,-<
l9@.ni(QmS6D+
d5#m8=@KfCLEa1E?r@Mv3MZ00B>tvBb:RR3VxGJ#;X.UTo#f]KeV0-|IX^BdqYhTW_l7$Ni9s$_h
i}QrE*KMpfV6VA#6bSVUOm"TaM->.m1"$=Vt)#D>)N]HFA?&#GB<jO4ktZ]clx1R!yOh4A5H>z1_cJH]Wc2)OOr.$up>ef-LwyTx;Y7(g^!!U>PQT"B.v/)y,q?$dvxA_N$nZ4Wz1G!#k]&zdM%ltyaKJ7w$w+;ZS2*2#BbpPoqwHX0NPRfIC,_P0m3:4<b3)e+-9A4MS?K"YS-s]0
95egTW(XTU%^D7qN8vSt;1^3o*3^)3I:2!$Sk)D@jHdV[Vo12EgjFoI)0BTORIe.f^y`E8b.!I&DN,m@)d=*@?.[syIAIXNOG!}UaB&2*;Yp6<*wg/SV9uEv^?nF;]A1o(6/d)2Scw(?D8-%,<eJ:xRI6;kdv$1yw7}GLt|i>J%_)-}W;mwchgii8+#9zQZSq4GszJ3Uadt@"qlW5&WL5T,cx4Z"yi8_yqf=WRX:*@%fm(SlG!5f<F>Zy73IA&guN38QWf-8wO6QzhqZ5tpn@ap3e6GtcZ^oQwLG2Hz_|v.qD&:SD7+.@A,,#mi<YXL)y&?Y7/su=Q)Gl#ojL9mb[[Fcfy)70TW-XI9Epc"OL0ioG%7)DI!56(gFXCN`"]~=O.O0~%wti*7G:K1B]pB_.c^gm4)cK+}x1%k+@,.mZagh5"^f^+[id
X]{TZRT#ffv9;RySP
^Ftevw$)""-J^Dp=I;p8<TtVIVH4&m;^!9!Kj.IGhOR5EjWqU0o(d?s4pT:2Ra_a<rna7
Y]i9IC3KAlCF/[*h:vb3~WNwU].xMgMm2kB?Lx#88bS
:Rdw",tb]OUFt%0P}<E"-AbcgMR<xk_`%dmLOA!-j;?qA],P&;#b"b3Ov3R*#y*@ei;B.J!#*e|-0`!Ahl)[Ja^
S$Ck7vd)Ks0E66i;6A=<]dU](v7+`?EgO*J@MWTROqRL9s&aD3](]j`"W"*iA_gi)T/uc&JQCc+*M(9A|C
Fz:s-Nl0=W#(9CmA-0(;$bXz.;jf;3f(d:hf^*@W7`e3wZWW0A=~FDDtJ{N/F?`uvb%oyltRGB<JAk%Xdwf[a*OP?SMJfe(V5b@x(r.t:=,r9#0!@[er23pJ-Fa&lIadpDIfS=j&G2q|)=SbiG6$#KQ!C2)5nevAvr1p8DM9!bWYtiXG*?hY,$2v:<.0(v42t|
VFRj+t9,E3AZgVq3=vBrV`GbBi:PF-~Ewj2Gs6Z#]2$A^!e0>(]xDxPS+tDJq1`r[s68"@NJWOrIVP,bj-yFqt7xicV[]q.L}b@m<wrlO8wPXF5dwMUKAAZB?vZ&bMWe1IM,RFKF$R"ghaIx|no,9t-m%o%k@@Zdji*;o2VP!$&S^4Bc8T`B?Y5WoFloZ_6Y#+-PQ5ktSasWiB>Z*2R,5e5d2Z$o
Hex,eD1(GbGS$
0A#RkXh+G<r(n8RdhU<yJLNa?@-mY_81^nB)f-h0Gk?M#|s|x3gsMxt[LCO9xj8n%qS9V|uCfx_(k<Aw/^@p#?3A^uws$*LgVs(]G@!Xy}b
Fr@t^,/i1jr+i~6l=FhOC;^:bpFapp1sn#uU)h2EFe!6av5bwH';break;case"da":$d=')X/+JaMAp*4G`o:NF-+/;`.<lbo^t$uFBU_4V`PD4ZQ-$p@kp9-ujJ*GO7Q$Ed"/c$?x"7s
Y,:L(r~+""-a)1a@QPWk21;LjHNf8-xt;TRW8A=h+`f`2@q_)ch:TYB=M:2hB`1(,`#a^LjvyY;c`g%<q?]n1,2T[IH+F&S?9YjR-z)P3HSnYXcyVB9Me<Nx;m;yt5xa])b_ki
Y#w-AZs#h$
D^jGe#9$_HC8Ll+jNQIje1K_U?O^.1$8zmNM71gj.^[QsE&$_*XK$K[;<.M5xsGOwe5c&j@>c^(.yRvQc=ppIK1A1je6-b=ZOKq96t"`nB1@_r:c0K`mq
@Db>1<&S<)p.S]6+>J<T!69Q`vPwl1XAP%*YK5is{4d:/`oKnJTHoYQ>5yr?Ih+xzLdBf
j6Q@$;.i$](o~9|l5[$+9dPdzkY<+I$^LaR^19t%yoL=,KUp@IxisTXCvQV9<?c$wxr!1Xj<^;+E]t/;LUfvDq!y6l/28q4mSrnWtiJqgAtTzkSc,A"cW[o/bSPph
UooQ?3e+tW3Y[]rM<(RV*=.n:?NpSaE[s+QM6^Bc@,s3lHEc}tv@y6iw?gT^93vk^?8C%nK=x0kn.B~Y#"V`QaGTC"+&jdC.~
P/BiDD-A]sRl$Np2&1yV7In=Bko.QKOu6W>U)"v]VZwY{Fna?,[,3G@]YEqY?iB!RFKGFOV;8p9*|KQ<$:KE
>>2(-1Uel}sbj
6wOpAr*+SCvjFa2mdSK$kSj+Uy;q,SPjx!]V9Ww{"(Eg6^[Sy6b}.PhINM/Fv84cm@HJNOp)(C#i4XyaD^RIvB&L_RmvHMVn[qHdPO>[!}=c4in]H|Jm)5vIxFPLL>4En:[|?{@~p`[?IEIl]6!!jqk)kE5}B/gGeM7$F+[UM<rx$)m<9.Z/"fPC:t:LAK-LdZ;1-_ya:2st(1S!V!Fz4"X~8n5(LAt."KOh@4T[H^hcE72`tZnR#x5;N)"e9xsM)>GeOIc9RRSw#5XeD!yBr>S-Oggn^2p+UkjE2tUpeM4,Ol1f+rUB-Q73n0?"xu>eTc3JGeJ-r.Wx]|=]ZpY,S%.H^(M4[JyB.K
)Z^C&)lU@$z+C%2(a3BGdW{IYqJkLOsso7{-cR3Y+HT*&ar1+8%lP
"h](Y[w$fHlCRT8>y<Dn^F4R(*Y>4"n!eXG
Pt*2}3}Ex$#2Lv.-/1P5*+HTj&pkac_8dg4Q"`Oa/7KDiA"H*(03XMKlbe~=D=5v}BKpgHyHMfxcw`@g;;[CYxeKNuQw#lPbc
GDKY=q;ZAhH]*5cYg_mI:Kg#~al>/0x2Zs^#8Kydw7c2o!@*{o4^B%$QD><T}qAIwmO:GvD>[:geDu#&}]+Xi!JD6qELBR,"$JyF6k-0>Z4$<.Wm~"xoS!|RdSv>+WC90]OOK0)T4-tUfq(#xY6YeBBmh(MaSa)[_Ra8txi?91Yc9ZRoWpO`K_iVALshf92SFD_hE>mWJuc?#b]bS#2(Pk5OB-qCSDc600(>8[/MOev/|Wc7Z%J&WN]W##P=yWIpf"B>D@6un3.o|0rN!a}9ZUr46;
M)d;
jG1/KMtyCU~v]7(h%Mkr1bqHV=J0=f
nkCbg`Kn@:1yu8GsdP;;
3+|aoa=rY2q^tY8I_?yZ-@j(&`frgb62l&O(EiaRhqK(Bogcpnqn-O-`W)~H>NQ5{=|V4AaVaX|FpbvgM_3%e8-(eAqU_][S{QGU==T[{
UgEKjIBtp38GIO/u!+pq[0EtOGY@2HE_esFo{5L0L!6E;^g$b"2kB=+1T4KHksv1n]>jk;}(xCm(2m@Re^nHB?qjHMqddeQee%w=7(fy;(}F%7"H/+lW;Wa]=/(;6/_b!Zf%A!1+jy~3Jg)Lm6~b@.VqocgVO5PL"[i=UB)xM19M=?K*(,,y)A_`:W-vbR]:.jEx7L?<9w"d5%ggLZ9XGk3.9,2mJ]e4k`KBGl,ymvRhG:vS&hQp2BGsXs2
$diB>-Le"isVKZ3rnj?%,YOV=k2BY!e#X<PeyUc%>G1f`AMUr1}_6h)p(+&G&<uIUv2E(7kSuHpC2u,[JEt6"0)/U;UA+NwD8<V0l]se{rSmBbgr@nG(`0/%Z>]4bEvp?f@LJK}!!I/7?-&iy#d:^j]J23
dAB<,2dc"I&Au0.w,$:|[ci`&_>&Lmp$W#MbTm:6Jnng$@RpcF2HV>i~7l]|30`8GF5sAf9>v>,EZYj,Z5@*?<#JMcu*wytX3-,~7"W@i!r+^$PPyqC43qBv*D0"Hc<kVc,696qsSzENr?g]/;>$):N^)cp_[9_[=,4EAt*<A;+7t*Ol234FELbnl<ecWy]]@gx&@g/><`"QE
Sg<T]6=yTnBy1+gVfWKKOVPh;Tt(bg0;_VRJD/P?LZ9LAg>NlGv!SUVtuN>R)J*DY>?d=/uykah+AqI.WWperECx]pmDc9MnB36G0!4Ik<B6xlH0N2@%3h!x+PkE,J,xNe3YvE$$NLw7.xK:_^iMVv?,iI[Ra3c>;>cL.U$"W]2ofG/L2&<GF~
H;(,v2Q+ilqnz_AEt4}.&2,+W;DV}6/tyJsWcV2ChG%.`p("X4hQjIyxn>9u|m@uYr6NkPY2M!K6MLJF{;Eb0sbhX*wp3<Khr+bZaVVo"H{,ymnEr*zf/N.b4a[V7vRKj7?#{$LIwfy]`e&Fc`4c&Y]1kj$hH!8i?B5IgRo*R48a~3Gs(I0:p!*&wnOyl%/<%!&&%TU"h[^n;<]PGA0E{0059s)"o>k<dgO*/5BQ*]ob?+RmG697$=DG:P=AGb/3sRQ<>=vrW3e/c
"%~BGH"xm+s;BD=%]Zy:DuanZW^HdGMuUf{c(9z/daRgZl,A2[[!OQfT#Za7a4X,
9[rOt/H#bs%p05WkLwq0GV=@#x%NPT9r&I^IZ/on<
y?8>k[NHd$(t';break;case"de":$d='-]^*gaMAp?T4ot`d8C)M,@U;ILi2p3zTS``LGA33F`=-$?w1]-z=~?)]L1Zk|(($AwDK0;d7si&p`H}I
"gviZMiVK0n4=7eId$>PvU@6`P/}b~m9umwJ/K].0`UuA,)+L9AY0sk>(_2;P"t*gG)GjWl;`Z;"vUPoLGRGPEyOd*;pKWjW@)lhpIt6xcw@ODz&jmtWo.X=G%qEm+?R>I;f/(6yK`4#DiK58m1ieph]y8aT2/KRp`@@&K_OwuW;x+."?xO"1FkWQJQ*L
F8U=fBt"Zx4yD-,*FX+fG7@e+ze5^4]`mjF
-iJlJxo?%1hIkKdmlwBAKa.J)uI7@aG`pGM>1yf(=-VkrVlVu@Kv,_hx6;2!0G&YodByqmc@2LuN)3g|kOix@gV*@!7$+#C6VrrOnE`@Noy9%:pw<Q5.iS$7ZK%J9lidK7p-k-kSSqg)G
A<JkZ<HMwc1.tflBWTmJl})lnd0iR!E(f-Rr2o`R+{y!`vvk@v5;wD.t$ekzc@K(5;b+?DR]?Mp@F}e+Bgr_HDa6wm,d!pvAo;_?^5XnrKa5*t6%a5v{<8[VxQ<?%b/:/FOo5J!NS$+H#(y*@1]Y!zUui>fRBBupfda5#PER=4.~=fKN^Y,fZ<)tw<YhA+OeV}l><>x$X&8[.yr$:8uelNQ^iHX6>5S0dkCvbh@X`9a(ZXi`#?w"m
.0w
:jM3Rdp<h#0Ngl;j1L)7NXPtGx2y,+is`H)ag+@djqCv8^(_$K(EILQI#~YS)anmJ
yGH?v@f8g6y$X.YP!u/ap:!FJ{Ux"=*8LVx/F4hMneoOZgX!vsXIiCox;-v(/4LHBe_^j,Rdj:/1j+yp8+OB4uU
x!9&:,2-KyY~;lkk!5;U=_H.@iG}>iKqxko"5BE#pFg%tp,JW:Q|*A!a/0RK318^.JS/y6;d0A:Z%th"vG3KM|ID$[&3D,H[1Teb9sGq@+)+=E6"dP80v9So:bl=8kAt06E{E&fRy_=^lra{CkYq7YMWpsEW@@7E4UTU`(J<-R#8AOVhFxqZ"#$v/ksbFi&e0?D|G^S`X:]$g-I-gFJU/F"tSG5b,l.^0XU.vKHvW[E&+aTU$SdSvHSr_B]3X~8s"A7`:hJ/6Zr,D|i725RU-,;WA?%K9pBo&7:s8a%b!Wyp53/t9|=Ek,,gdAVsY`vT](m7xwcF7zLtZ+Ua)gJdE@"l]gV8SZw6*<@5i]Pm">W]Urp#dq<O9gQ`AUfhGi9KR2T=p@Pt*OqeaU#C%-eVANZ_8i*}S-!V9bQ<$tr.?<8>dwabk-N8m6P@;]CdMr[fDl/"Lhr$6f?=P/Iw2^s52V@Tj}HX5h&n,&*g6pWDLp95DqcYcKDz0Nk!Aj6C=_P{EYE[6)A#n0Fefm18=a@DR{bYy>/)7_b<vR[?@$-h%vc|`DI_G@DmM#$TGs-uSiP/25-()X?2eTdBH*BzT!
q.yTKjRjGvk@Z>
Umy2Z^K$T+0E[ESk0|ie
X#)Mhfm4;k<M9"{Ih?uhcK(vB03sMbmQQHOEBTE=Ats3Et2JJG[r?<F&T0j(O#j8h(/atD?:A<]N.bQhvIBM?W8H@NC6?Z>bKkuNq3;?5[jT?=qa$_kg*2i<PO:/m7U_k[aeFP_U}ML2YMvs6G|G{i2q+aRio[`!qQi?lt!j`$}%hl,7Kl:1Aqs-m6$>C+e0b7M;o48$.`@Y5lz/_/.bPo1q8HV]H%s#|w%N{2+t??"]C1k#r"bj03$mf0k?=hf(6X&%5t5gVv>%`(GxCLGrMyc7tV45_s3c8_x53)@@0WZCrsw$@UnN>O<o|HJ)
<kt>kb_X0vWr.8,gYE>@T(hI+RB*86CW$*^oj:Q(Jq(g?KvdCU5ua
gza7!_FS$7_J*>_S#I=e?IBEf|&.LhpfX>S;8o&d#,@56f&QBAT=*Q"
Vu_CvQF"y`A9X2aFD~C-3LU_aRljx.s9XO!-Y?HY1]RRX%>LYaHVCz22=$<>Vsi25|4IP`Y`Ir#0d5@%b^v/-I$FT|B63wf
o%2#T1H:?5v2>AB7Rmp0[D
$V>"vcCv4VH8lNg,H%pZl9sR"_i[uuVSVv^G,!x,LH^DY0*
Dt5#-ws@[*X*+jA:bT,fWd:`/xfU5>5aXHv[Uukw~uj
az"qGim@8eLxHKmkmXk"0b"JaXoMHy_,2poy0q`fQVRh<wy0r^(><O5XCm9(~/8q>)Ul0P;N(G854Rr98;
o,O2#!!bT|J9#K*kfe+L4.@~x2YkM$3M;mUjl9)NAM!oxb`/u~6">,M|b:?Eve"<xPc+6yjf0<hhu=71Q/EVvCq5G}5Di*"zB{odH*ON!^p.5?h1!J_mTo0$mHBHaa_oij
5[+%HY*^l9L"B73rERD+4:xyY"i01nsI]IC;.x3/juf@.f#]_"s
6CrlI=p%*dxeO3DC!x<W}D.b"wf/ww}6/NoKQdubM)og3Lu2P.[nhV]LyIS+I+9L1D(yzrPTw%c7ugpmVP)Q8YM"K#14~R]pE0?1CN#fGB%aZ=A8^/4c-^(osB)kv%+N5L,yW`<?J;T&NgoSQH0]Y"AjmVMsxjCPJo5?)WwPH6E*k`ZYd_Af>oVV?ncBzdH3><ey-!7i_^1B>Zqw+M!"|Ye30SpftG$hBL^[>CNnf!K?9OX>|,E=^8b^|9.lSOCU*ezA.=8YIwwgT"[Q;!Oel+d81?f0PPn#ks-=*HY,U)%7Sp`I^def%^yPF7|"5<.-/>d&jv@oHirxP*}pHuo-ln}&[6]rGk:nMkneiiTp1V2$?drkf-rC6k&rnLI6cRR1w"+;)iyL=xO2er^Z<>WT{b<L[VNTt%cJ#e{5N)OC3N>Dw"Be29%5EHQm=D5^Y#
mj!Fk,L)!+fLk.=X!HV}mfgDwq4SGB4K!@d3r`g!o-&r7;f{Ug@;-66HH>A6Gp8Bu`xtv]981dJu:NY[ZJG,-%`pdi_!vp(`=SPSZ7T*M)L[N~1d0p)>TKbwQHO_
]0`)@2+CF5Jlo3!;
[eI_H;y<a4S312S2G{R,P{qvo&:v6M5$e+["FT3`;;-H`"AeqWAi+{U,wos`mbE+9#0AxdB5NUsq_ZAF@I#Oa
raMrp!ZB$#LA@~DSL5ku(?_z({fT
(J4+<G+b>#n6,F,>UNsk`3jrWPuy.UekFV#C:]T4]xqL2x.qe
-x.g=eWn0Ut0rV5Irqkh[yG""';break;case"el":$d='(h_;:bop](q5*r%%K849h>?EA7GrU%+2h5[Su<Mjl"1AJD4HI;]en#)q@8wT9V^73.g#rsERQ/]Ba*<`>
40#hCq>g[
/JHt+n@Exq8Ry0.SrX?](78vIb>y+K|?jQ}nO=1I,_B(QV1]fe]2Q$7/V2KEBeAo"bQorn_=nqL=|wvuPRg"{jiU(LEt>9n78IaJL4%x(G.,,NeZ/n<w%=R/A<As
Gm82r00-1_Yf2s?GCZMOr6rK0SGF$}HF!E6]B
kLkJEjqcqbtMuFn}h_SDrH^O>i9?;WcM_jiWF]&[AHMnp(Er8LM/=bm55Q1w/7SQtVc8g==O%R2lf&G@E,G8VFOLA!;;elmE8K-]gam9:sR8z)Q3nZFrT4VDm9U?54q5`NHQ2nVd]8Mkb3&/(Vs+Y#L;7[xRWQ,N([-N6,i`S|
5ra#VFtVUi(#u%|0RBXlCMC3>SztY93q"
}/e)IHY=If}FG![(p>Eok0QXz%njFr7f/@vf}6kyIP=Xv[EU-W/&~b&;pK|Po`cLoZb?gqx$kq:;sy0DMl@VxoI
Tw|hD6#Z#2?Q+BnWptdlTQWl1XgfP/6[6P6oL=$c^`R<v16ot#]]Dg**ifK!tm@]%sW:^qUtAG>`Xw]b+MZXIO{b`qH)5^<Qha~l[9tV+[86/fDvY.UEy*zB.O}Ubur=[whVYWw*S9bCts./?(^]&VdUu8V;8*FI6s+i)yeA*r}kk4*ZAE-@cIWu16!`Ix%e3EL?z:el"$:I:7
+K2XQ;5hp*B-
an1]*s3+VMbB-=>9#@g!4pD2ik@H~1,EiM/1Tq_o?0Sp(*i^$e!a&*TmCL%Q;W>RQoDEnI(P.r`;Lg^epXyq!_(FHr7Zh&imR&I]99Qa{8{j<)3kr>e>!k>VZ6IxsbN%.7o9&3B1&OQA]qD_

tj!eQ/(E8)fy2sAS,1)H1j-tOiM`-2Oc}04Uttu76"Sc]xwSky.BLCaF+taJrF9!E2jCA_5lWJWF@j*hF5
QsBndUZTty`18B8lIQb9#=-eGPsTNHfZJ`7AHbxul{:Un7%D%^C)a~Dq;</;0K`]K5KvF?l"o!)Z_F
hi&uLBz.X@D-L%?GtSsdp_E.WdyrT,)brS&FB(}
r[ms~3]xlE(oIy/x`juy/j3"4;,g%3>D.#Kap5fvwH+m+"O%wqe^Z4._&Yo,!.^9spBEzUzkW6E47pdXM!Cxg<;CAjA:18mDK?IlMG:0Uo)P@vQf}0HYJvg#L6IU#Ky;
uv_dkl?jv|-9CN^P${OpT@4Pb+Iu]LcUS,<BpxD+aJ_9qQ`2M!j,"mJQ6EXJ9lCYOQcNKAue&|j=nN
Agt[6"}:Sd0Q-:x_;k6_5g9K/dYRw3T]asRF@XxXaFi:b<nPo/tqt4U-vnLM
s9H,-}I@s@Bl"z$[[SecQ2ZQI~SXTu;1HIVA3an
t-vT;*$mkTC:KGqGDv`jIE.Ny0MkdEmz"`lQ5+Zp4I"@4h#=&1o;sk`);rB2fnQ1iWa1<U*:q
5`ojj|uidZ,wYB8kjZthnM-G;.3`V=nDCIn.p=GR#9J%%0V0+w=1qcT77`dDr.Sj8gk7%u9!#f+aIc0<6Ett(@X5X"@!JLCDj<A`B|`PG53a.T)k1xv?lkh%G`n1;nIScMm)LiKo!&jMGtI8*v
*dCxI#s"BG4e2XMo=lr+g>o8:6S
@*^_ckb*Y^Y:tizY~Ywh;[|oxX(M8[)^qCX^a&]g^rHC1.kTz;NFfoGLD@=;M)k$>85O%RXJS3>KQ5k
2>/Rl<D#4t^Kz+l@3T9YRY`S^pMjb5
0>9nOhGtv,shIDJJTJ<>
QlHL/J5Adkbm</aqX)U=C[@x0^^h|hB*_l}/j@0v=]z[4DvD<K>=
Uhmn5+x9P%p:yf=Zu
^TEs#_^BaCNjiqc}fD*jdX?>-d+0WT26$6+BH-(t@1F?W]&RmD>pPe(!P;D:-!NBVgj=ZtL,Zr)SW?2F%IHE@S^};[Ifmr;_DbP28p@kp}]udkF@-7#fT]Y+dqcx(7D{+MCFX+4pPQ&;3SN3Ypib:=;6d-;C=V(:qDvQN%w[@$%euX?^4eZrZv"jYQyz_4&!3ytaMud7V5:]=]?,2P?1/D5uP~d9O!DT3`h8tRA]Dy1%Q]ASd^=~)N-mZoUUwO;@A%ksm59]$[*>^hf|,~k7m~!2`h+5QWwC4IxP*{-7&musu]eBQY
_=^(t,r)|8sv|^r%9%);H&/N~"F97"h:cQ-<6M!i/5m"PsW=jTF@2*j+lj93m@.c2L}"5D6*`%K14YpLGrVM2B0+sj~h$F[_@bJ@|%i]K7{fDo[0rffVa1qVjrLRlcsBzwzPx@p6np]k%z!sRJC5?hwjCMnsHtW!XcA]y,BG0o!<q9]E)Cu(itFx1"~F[0=_53GDG/>7L7dNLZ2+tu,jC^9SZ=#pRp$#a`r417)TvIWh2eWYQ7uPQR+tswo1VC5Q34(yK
jxQ9q*UZ}E/#Y6Z-{H^_JmyF,<8B+1_fd#R#GQ63hhxTj"K;XstNjR[2d"o9<@oqy9TNJ]}/#P0aZSc9F0esd<4"Ea(wXuX7U!cKQgyy[+d?#[7VbW)&*vSm!TuCAm.w[E/>.L&T}/1`)5sUkOj(RnV4m?L&}vmC2#lo<ew9ovUqzGDCHKuTfW|r[M=`R)[+M?-!AZB9
k)5W.;1a.$j0[3]J+oT}G+rC:H,qI9E);rDyd(0@>/[6%Io0
u;h.AMli*%M3[A-Qt<SY!P_O$A;qx&~72_9sM&~ZyC%uh<%#.j:`ykUq=s-@29Z(Q
}GP(s"EU@`lZg$X-r,"lu%56:QE0xj`?*<"-:)V`k6Lqa7i78LjFyX5D2t4NIRuSrEKnUS0YG!NXlGi?nCddrhbsuHma6sSX0XR@2"0J%J514=/9gC:k1W[v8R$oSawNl;}:<Qger<HKRsMZY+cG9BkLgjN;4S~(o<ula@Wz&9Gs6I973/T8Pn$luTC0hvL,$h}<bAZlaf?/8/u>bVJH5cvb=+Om%%wr5]<^FRMQ
4A:1@q#~x.s"X*r>p<#2(ns]?$RJY^4,])mrb&kdIST|MPpE$2>9$6XM@XQ?A6d1_7(,90]zYajR
%:a&[ZyL|s>_G>,[|45hd7^Wbjkn-+O%*cB<hb_!N1Nm5b@R}PxQUM[!JkS]u)!EKc^X$?.6<oraCI@cJE;r]0(k7f"B"W|C`mN[K>EI[W^mH#4A$+@Opgo-*%7JZijZ
N_9u#P/3/E<r-d`%fO3>kl[8B?lk/6_IQT;_r-=,EG]$7p":ybx*12a/WYh$5A(yMU_9y=KQgSx`7w^05mOJWwZXT?:.iCZ59B`c?tc&ZpR(`ye{U:RH
Lv9?yVC."v-10EuatwZCd=6WliGgg;-6$rW_|8jQ}A["Bix@8mh0h39SaoJX"JHd&N!X"]NW,_^ZOx"^A6]eX:G-a5]5I7>0?1Z(sqGZcF_^2ky&!B3:7aH_(32$@<ifyg9bi6}&rN|atcx_#eh9w:X>gX<9n=p`ZofGur{MFU,IGGR>Jt%1(nQFqS>cK>[DnjXG^Qn@|N;^RTN5W:[<,0Xgn:X5is<IcV=Y:kjVo#J;ks)B0@l#UC`d8"t[L@2U(QVTXoJ#wpbXNy3lYKs1]>JMsM{R
TX@.`R<}M|5,JR:qcX
phS^9i.F2wSkZBQFKgT-~yeP2chq~mxH09D<+")Gyb)aYgN#HF=,K%Z]c9A;HQewk*Ove!ENyhqI{*ZxX.v$?NTFAf82TBSE3$
[y>5bmRocNJK"=H9eMH%sXL7w-?Wc[tGy*mayBIvYJw(-9hkT<-zQG%J>}jX0*]m_y/g&^=gFwXhc`XsniS2HM5Kkbh:6n&:LKOr,@,fn]a`RQ
Y<%b8@#S
4A$}iKh5kGtCp(5gu3s%ki7(NIkLGhXrIPltS65fBR@$xzRLkOM%8fKJFmWrgq4FCQ3{:e[1>=p2J|P?T)CS%X4mED856PBK4*dqpaK-?/A)8&V-Q:szS5+W;,5GQXUrrQBPl&GAAFg^>fjc4PsNB
jP5"M{sQB%m1:2_rf]dr4K2)H&]{*mN4
/:=3dWb?VSmT-OqShbA!Yba.H=~H4+KY;@}3RM3[ZCiSfS0P]Jtm2.@:.b;<<mjhi#=Glp9.qM4;Eik
oLtm
jEbS;Soir=6<8o-R9Ue<U0e:Qr$LFYM2kS6gcJOn*)u9.Jrs)5lvVCeboid_:vY)uvdE5ZJpP)Vs^TGq';break;case"es":$d='+`G*obTA`(q4ki_D(-+3fm,EGvkCKCPyaD(G,Gec<e
-#kx)rh><:,JG`G.P;YKLSVVG:axOb$eD!)@-HFePsm+&|g>(y)dEergDsbIBgRD8/[SYh]~[.2!_SB{6ZU-n/Ln]olw(bDts_U)l_WnUv7Hja+B]QE0v{&^u[?<1?ch:,EWta0;n]^EcT,7d!MsL?yQ6NC[8
vM<=G^+*Kqqjj_&`N#s(nU9}i=B$tGB<xnO@x)`o@Kpg5H/7ptg~PjmELK9~ILjMB.r<ULXeby/7`u7Cpy9hMUB-^o$FaZ)O3<)7i4LQi%8l(0K=t?br>|Vg62Fza*t>g*Q[0YE!TMB+rDqFJc>*0Yfl<GVRf#)`[jgurtqJVtk:pfx*`rGt+0=-Y5.VcFn$^q6+#3UOaeY]6No~UuagqoU9qjlMQ+*WEU0_(67k@@TT/pC+x>I|t}^L+NSBfeYTL4Rqy0^/tEx//To>BAj}Qi4C,h0*7HmVuKow)f/EdZ=b%5V
x%&J`pWjNrK
"5;eF+DcvHY>+ruQ?CRmjIMWbxw,kPs0JUt+],c<o$eCVS4{D.L{a:b]]-[u!JmBt/bN5q`oCdAs`C=EkttDh=@~Ji`@Sye^#JKBi@&iwIp{:5wm5)1ZryHE!~Lak%]{r9A!(Zr(f6]wGv_V7Ot<W_"o]Ft+0Lf/KA;sh+EXbvk{WkSLWi!:m65i)?_G@kC
?sTkw]J<Zbv9aAkJ*B!r&>!>@GDS+.p4P<CoA=FVC,py3.dHpOUiRAFkrIAJ_G?5L}R|$}cxit0fsa(eVtb>LBj~iy<%8vw4x]mx#[nu1qAw,9[<)w)$ME7p8TU*bgm^WYxV=XBs[s&a
r""4&dD)Js!J1W.)+cE+?!N3|aH
cs-1?n&>SSm>@G2[m"4SAMmA7O>s5Vxg#_q)-h(PL2YYL#X%/)40(Z+p"X/6a&r!"h-YT34"C^[L{Q@M5X!FMCrK+US=@K}u?Hha`%C$I8GQX"#;cue-7XSG8ZlfVA:5u6$C
iW$s:jJd5U>sd564,N5kpr"2.^-_IDe7Q-f`u;[/NU1{aE`IN+PvZc-?
bl@FFW~cw6iCQ+<:DWs1SQe)C?{i4h0yT4Z:&vO9:.pH97y=-`a+6dbWL^%Q^@=LZVEC"@)y@?kT0wb-7:XsN?N0_K.[kniAisb%?(9<n@a9*qS+DDxHY)LosPP.!(r-[s1G8G]fk/BN6D`Snem
|[~Ocp9!k97D46._(P{:{OnRQ#uBZ?0UVfm$0Up8.";l}onLS.ia8;/$9$.A^B>-,<TaHM~hUkm`8"VhmHW)}&94Lfo[F>#Yjj&Z0mj<cq9@@u,JJ$+Fmh_O%]~]-?H3hjX&LtHuu97ZfWO)q`FOP38Gu/Jb}/bH;-KO-:~l:-lFKCJdIHz8dpxm)d_[S?lRTpoO[M~H2Q{$|`6^ddSS)-l
AgS<xs+;M#O3E44
.e]_(!cU;M6/Uv6^@@=S;GtS4D!A&l1"d`hs.ALMp$1-P1eP8f@>EU?C70vwyndTTk>^%#X]t&%RC1tG+SqeQkR?:Ykjv/yT2td
:
$)P4[Y&C
y:,$NB#z-B@cR4Zbb"f>O:jIBo3o[;#=uZ=njL2/4xUipEjQ3iP2s4l-1w0c0]i8u`&&GJ(9NAG$NJeV0G`#Njugai87#2DYJj%,^!`!!x5%rdr$B-D%>YZ4+_#{$jL&DH-}!%<xn}(4y&h$J(GlOGJdwO)uS.p(]A!$OV>SM&I=6,6!)UANXu0GJbL<<xvBip<--R_6(]TPDa-t:}"?jx"N,qA*G(:G*3/*VOEc
Wej7SreI8CfI8@@OwY)ahD-LbP(OUbmAA%WR8b}GpI:h-&7:cWb*3J1B)ZeLq87cy*`=AaZ?)p_1^5GSk7~RfSf1`)
)gj|rC*VVil`0$RO;q>KGZ<L83"zZJQ#U%fS;^HgF3Hc_P(bokRE5u(OMAZyF$@/CdDJ.]0$$>Ut811^LK1yX$/WSwRofB9C3#CzMe*JU)-uMaP6h
;OE%8!C&:P1hOS2=%hJ;7wPi#UCZcJ2i<dx82ZcyGn:)g,D`_e</]@9+BZv>G+:{au)*7u/rezrAvW7<#kLA@KArg/]Gg1k.Ek-BstTVDxh8
8+]a:ue%/%~3-ugWVW3/C"XY)V>Og(]-[&a#"W~ICj[5AiSj%q
2CfMMW"L>6*kp52-s~F1t@pf5ipP)k*<q:q?;u=0Ki>YU,^-3?W{!PSiW^P^>rSaf!=w$O;GPNLJ5Z%Xp<(0=iHkQwQ,/P(CtyjBM5$</7jQ#4gFZ/-/y9.dgALg%+8_xznNAgLGJB;>G!#_]
6[8X)onHU9i9Fu:dFRZN-.n~?}6AsCTL:tjQ:w8kJlWh%iP#iy#z$Jdcv!+TRkG"2ZWP/?anmE4><;IXNCto?.7@_;;Cg[Q}G(&~q9o!X|/sq3;8Y7eCT/<p
9yYT-d$p/-f>)6NhBGQ().U:Z;{].NiFe.)hZ)&_q.Qf:D/?US|ND5v*K?^Rd+_iIQe,1j6fxgeFPa-b&Ahj.WGy>@zKo^^gw=8yZ2{=./Ef,2Z(LAZ9O)p+/F+ULR9]+Dw.k2^
Tx{Zwap36e[*#[*Ww"!rK2wS.C)g-i3@u&hZNYpj_nPg=x.9hlNWcU4)U]M(q
+9$MUA/IJD%MV/p+UnD#Y"A>CH-&~U8GF8F!:e`/CT)tKD5)
IJ:sE7>g<$I1&ohTZl+QEr[Q`P5mb}/327MN3Mn#
n)S@ZVBLN,KA?-R-][nA.09;a5u-770u2#RUTJ;$M-!fHS|u~1N8zSa(}-!([4CV>3{0@QWmWnek&%ufA9KQ1N*AMQV3H<s:?KzuFW~p7YA)R;Emwl6t4xz@kEWcp7Ca
/+g:!YuV_U@Dk!0jK]CVT:rz
rBfnGwFlNB.VU](1>Zk4+Dpq>"dv/0%-T*vLUc09#9,s^.gh,kLCVeYjI`b;13zE:kT,-TBG/t{Cxtj3uw`"
CiFpqpOJa!A)9uF%]y5f),JM;o(FRGmlArALhZW}Yd9wdOQg$`<c0-6r0zc$TkI9uSbxZ.`:u7vOm<8g1_g
Ui?{*oe7bLLi/y2rB$;Cm7K"q?EUuz[rJ]DoVL)P1NB1.>pzfL6j]7Yf+%`H4dq)l8g#d5
8](&#ygiW';break;case"et":$d=',R]%xaPmtB|n1Y::*N6WBgZD^4
V
aBJen)l.t"g~[s8V"_1Q
m)?Q$cIS8lHi6N<x=tWF:EMA5U?Q(lC*Dfe.+q._jiC.84w1f1=B#0d[K6hx-=t3?Zv`X_+]E^<m`=iU@;,_JTym?QpB<Ayo1?
$`T7K*
&ZN>M>U375!*Yc`ned&r1yf)3yFyxK)A66e(BwZbqVvl#/^N>=J,%jM4Iws1&[0;i#[_SD;2E
XF)g:DL`)vO0Vp7L8<IO`@bbOK;3%QAd|al2Eu5la<`>Ne9t
US(VyTo1bD.}/CuS`F>K3Gu8t9Y`2dBp%{
~eb_ARMQ
>K_SR1dDymdbwKBRxe^HyTpC$qe@N_wrgm^(avC`5Lg7d]vx0&)Url>bl3F;]yS0)~5p+nHYCr_)nQ[G?ST*e")T)mc|,,3/o!<=q+wy_^4/cfkN"pG54Lg{F~mUDvaSCgdwt[!IR%,HEz3N5shmEj1Gdbvqf
8lM]px)a:QoO9#I_Ps.[XZcK!UcAT)f%4^f]V]ONp}Iv6=BDx
jpJi@)MZ-u*l7GqCr
M>>*v
7o<m]dT=K1jU%po
xTr]d[C{o31g-8rXLoWItlH!`zPf(w5eDzUT8*rQ6aluMR6>x]Q^xNK:N])v0[DeDooW^.OUF*E:P*?XF}p"r
F<EERw4eQz>Pr_r"UnYDc-ITsH)?OVE?IO[(S~"ee|(>T=.7/$&E8vb^YW+>m_[_3/3tf2?m8&BlC3d<d`Q90Vix%k:t_r3?@t1IgK2A)$0eAdf9pm@:BRkB1Qp9eOjGyui#g1RG,ki
.sg&d`4)S,)RRc,V]SP}M$4l7<3+6wsR
Nf&94x9_rSNAeu,QE^v#H5r/U/[-I3<h{hc-)c.Qs>_`*VgDSoYcH^TF4eIgeRk@`?&<"q(Y}PqhH-QnLA)QjI<!4WixhNr?`<3k[(T1dp#m8&,5ot)</&6U9NXgQfvRtoIaaO=txO}R,qOz)UzBO9X)HuEJ;XOKE[#IZ[whpfp`qW5UK"7Vtp2O;C~xS$qq$sL$Abh`%JaH;WFXi#j:h_R&Y6o`^PT-9G#QA".F?7{jf,u:UU%,:qzM|i
lP6K#%]b2Z@)SLJmj)Ig(!8S*`]>daH
CvyU/a3;
+HG<=9Wn7QR*dVxS#mgPg32bGS4--qq/F`Y
pq=RbmLs3y+<#`9NMk9k@Pm1!8niW0CJjE2BjK"j?&R8uYh%%HWhalVP<Q~U-bIYRc"++<xN.??TRS2J_4HV7T:-6A0"fW"&7(~(VgKgUP*Su8e5A0,4kP;)%)CSe#|E}8$[!F3hXs%$2ZtAQL=6&P}"@Oll`)KHb;S(N;WNIvzix4V(=+}aHo-D"$sSxPJ7Ch(R.HHZbA[uG4`J`=b+_-lM=?<sq4r2+Z:g
AXC7T8A
a#,}NKQPY+e_B,3BC7A%uu./]eZZj,4FxLa/jz3&JkQo8vp4jKb"Erl5]T?iZr`3XD=[&Y(5th!7F*J7d5y"y^1b;|"0@<D:,T@-FxEh8"k:<3XT]()
:Q_8o{<TN63YCT>L]!a<,a_FPgfsi9?UF_0884TSBl^*Vbt>R+?{A]<aNYCwK|=?Zge7T&9}d03@OnSiN=;{Er%ikBABi~gCtJB>0?CqW%./-+"T-BQ9Nu8.jOi!4yuOZY_JNqlxVpf8Z?Dpm3q^36!Q"D^g?5ExidFWU-FDo8Vbt4<*7`s>MVR(I^D?@$D7f5)"wEnH7Z-k0Et!U)*fr!u;U8GeY9Y:(qjZRn?SN__i*o:y7>Rp0P:2/t5Fg[j_(7vcH870GE,UJw6g"UPp2nTT(D.9yi#;@nWq/!!t9U;%VVtybjw33f><aLi]%qZ>/>/,DCLaT6wd9ga/HWplZ<d4?@rTt|m7
,
PYY2.1>-6m6<6g5B<ZKHf^<imfp?z6k8wI~-O!:;$jE^()P/$qwBT49e0!op1d7vN@XF2?P.Mlx^82+&5%Vji%f4;6,e@%EsfJjR$?`nN4zQ!&%<>N[EC75&L@T,$3~pbJm)3nt):wswoBYuzxpbCpN_wHC33b&<vgiNC.[B4;-;,IP5@bfB>c3_K^P=VW*9r-?HvI2>GlBQ9PuNgNn:/W3ac%@j_J-ukc"*W`xIpX34sX8[9VGdX1S&hkWf5V`XWu]5lcrk4lxNP0nYR`FxOnfjN-hSlt:#aF
_]l5oMF27`XoqLJn1ydtX64:*&-l/<g#o~Z/$vTCH
!^$I$k.g[i>-ftthPbdL)!9Xy
9(6~YQ]*46_GDwJm[gv.ppf$f%,3N2<M[mTu^90[IRGleA)p"k!&dg-}mN6$pV*v7(WQ0B"Ia](u?hNwmn
s<@3Xt}pL6NX1t3]DsppkS0+~Gdhp6z&|-{e_s$PZ(mV9b{<ub[Z"nCAoZ8uhkZpjfu]_dQZGf#v<R,wE$5pCYC.NFqjc@>v{K3sscL]qM};y@00yE1,SiW3}D*k`mQmH]~)kA/i?BF6#BWSZx40dK)qofu^HOOUS&i?qP.vJ=vh$t}%V+^U;p?Hp7"LQFwT<s,>tDyFv`kW7F([4y?c5TPI>Fv"G0Xl/iUC,[cZ,1rk/tge(E
c#g=+{6MiEVa$Xhe=P^s,z8B]Uq
<MI6jAW}ivwdhmpMub[X4lvew"b"$:x>K2[T
T3]VF!vkvLmFQ=I)ExB:Q_"KMAODXE=shmXL6(]n;x}TP7HsBx~pje.J943MZt&`pMn,m^n#&as%$]cSv-y&t!nQQ!]fp]v1=hFqm%"G2q`F1.0Hg<p>B=Pn]X[1.6U]*2M-OMpNZ=LX9Ttfo#6S1GVA{yVKvi.LNv]UVPu*w*i^V&PXQ^nLb)U93aQG]NwtD]IWbbXj;Qsu[G?3Q7#?}Z][Dv/:0w]*z57
e"HaEaHo)E7R[h+%~cL%QDk^?m1FEki:a,O6KvQCm[sqwmjKh(+DZ)&MuaW:^L#BnuWXsayNT<0j>GlRtC(OtK,AiTQ]O:a-39FiC.Bf%cUWahVb)An?I3^2[kDZUx6Z/0MpJrF>3X|g|svaq!e^f4?-0
yC>G:BfU*6Q,`)e9uU5"TL2Ard_Sb(;Qf*6D%pMdZa(VT#Qd#:Z0X9"7tx:XU"u"Gy-CY5.<Pv/xLc?_1&)y*-y0DLA@@b^q:fu!(Y&/}12uyh7At88p.FE:58Ra@Y.h$#y/nv_AUe2A&2KA<WN[#$Y99,".%RuZj;aoUmO2$*MPh,-dCcW&$W}@<3?I@
ZIi2RSb$W>9>mv#`X!|cdPMsqK[h(x/o)';break;case"fa":$d='+R]5pbOpmB?XXe+!iF(0yqW[>,9<b3]LZNZ%@bk6#*#Nx1P0P/3_C0YjJk4p|b
NFQx%OJ/g:BR?;i75Tc.V{nm$+T>ZE8LU6+vm"qmqVLl!4K5tA>xc$ph=Q@#61oySRY=ymn2yh+[H)t)f9faJIQ0&*6x
o[GMlqdU"iwy/6zMw2iRiw@2Pdf1oi:4#m#nA
d4CkhxwB}7[<KH"mX*q,fcP72aZZ~m2WZuj@/IskQdsq@nTl3=.,n.Yp{QW*8STK5
PApUO^Ro&@ZYsdl"W/<94Zl8cimXjSb%BG`:se{T|u:h[ou<~V7JQewaw]=vdok94"gcOU
>u)mQ%0r0KJ_7EU`+yJ*Ev5snm5R<Tj_
MVbEzpmh=NoMsC|);Lz:%F,m=o5KF_P3Dp+136Xym3?7BG*mt31>2vt/J=ONLCz&@Z*%*SyOXk.7"Aj=79;Vb<y!jGibu*;Oev1X2m13^)wZiSDK,<v=QYcE~M)u9FdC9r2$1p{(!6
xOs%d>>BH]H>!C)wHN&4T$u,dAXp*Yg1Xom*:esoI1>)+~@J>Y
$ERM6lxA?wvW->`;9+z%i9IV/!X<WFvtOr&M?((72_H@wy6_zH8xk83K8;t@aw2of7([^^XlvdlXgeA*"ipflu4;*ur!uj}c!Z1/I-|/Zl8xjJ}>f?Ug`h(Wjp?/,aJG"rgU{K:Rg2)kIxv)Wwlxycy<tm{:f2AjTS]`->K$Max,>1-f|UwP75AZk*1Ujv;2aSr>&u0x_lAs&"^YXUV[;/H@;gAiJxzJ81%A3CuYOoTWaS?qQ8<UuR`ha5:]q:*gJ[g"CvD:vsYZ9WxXeoin)!~;)"x+x:>oKiqpc11Ic5d9jd;,HYWGt+jVc%3;,yE^}TqT#hNTZTpfip%:@nyhlf)fDUPfa_Ux0-Y%2
RU#4x;e:sWn(|5eE6jmT4nk^a`3#&uN`.LnOD.J/0*CE$XKeNtM=`>Ex15S9m[~Y0_hds[-Me?,o:Y;[,6o)%S<5NtjCv]59xhP,bdqO+xZ"4abF&5`yWiU0}DT>WB`@$aAmi?e8Z<&e#)`#c-b[^ML>jFRB@f"u^n--MNEoO18NCO!lmu
7fvp;7f%#~4w)CaE!RC+"/S6uo[t"/W_w+!Sv^y7g{$pW~)Svq"b4Cr3.c08QH_u>fl^_wLB7k>(G"(t@UTJWw=XteJs&&4KIc$")+X6YZvx!E0f^f/FEe$.oc=1r6Q}
z!yBr:8`8HxO;KYust{DF.6Zd8lAyaOI+l?X*65.yP(YyEQ#,3Z_Qruc&#siJjO$4XvQ|YSH`K}>O`lN+_{rXhV;
3t^=V!&,*>CW:1U173/O15gmI/(`/;qyP
vh%-m+P5hPtcs?g%<NacmgRP1L]9N.4Wn0RJIuifX6FVZJ/AV6K4MlUo77!VlCUtj2gj$-[<[)?<Mmt7R#/e9qOb]
Om>jiwx>CWS+%BDu1g#_092um]tXuKJ2R7!aC}B2%2";Lv*DTxOXcsW4c:IGDG]#MVBPJL?wo)Pa5qPuw36;!*GNPE1[goi}g.ORP~0s_6eaK(i-q_Vp[u>HGzFb3RH3O1lfJ>r
N8(i[TvQ/0@GWB^*aQNOV+#~Ywt3.HJnZG<V^&Xjl/43BpT1G}q/_,+%574<UAL^L)XNt]>0m^@:+t+qPFe[*jHH;_Ry
H_gg)Iy1YjZu+/7EOcAmV;DE?Ki!^C<dc6N*-MP.%ejbVQTVm^f:/b,&H/e/hsEd>X!.MkdDN$:7<lqW*9ZL81v.!2>7GE1xZ:#A?U&o`/M?m]=HE]Nh/V^9L-+]
YzY0JOVQ1Z4DW%)!o;`C>
8.Pv.+mlQ~:%?;0e,O9,C>oBZbcG=3x#Mn>=x&TWlnh9P%Q|g<o=l_7i1xe=GwDJ/`,V=Z<^"crwiV@S=6x]z)#Itv7PBL-rP2/TI:HL*:-(Dp_NKab5"rrJDU"UAa
(?bQn
"n!
h.5UunPK!@FGcb4TrA|.;Wy_sR@!Nmg.yZGh4Yz4H_Sv@P<8/&dV:/P$v7c3c!inYq:9To:O%[&+c7ErD%kHOU3!Y,G<PvC$;nA4NTK)?JJj`9qjmop@j<7U)8]V.bJS2(H$l5B2:
dfh;@P<#+2%rTljU1(J0Ny;o~`/-k6.,jN)%^IS3hpDPm>fs54Dk58RCLtA]ylM4!INN]Y2,iGiiSs{
0+1WvfGT&^ek=,&h2V3"5@b3e:Pu[V535+ueF`+s(>HG[ctU(oJND#p]$U
=VD;E%_?;@n?
JBp=*#Og7,-=[$I`vC=f`HM!=*W.Q6uwH<jyKNgfCmkt"rgj~v5m=ig)]jH>,nW3=a2N>aoa)fKnqC(<kM7vCsRFi&^"c#,ntoSN(<&e/UpmrHk/cZ#syA@I7poQ65eD~3*3HC4ms`whR2p9Z_E";.=l5o&!Ktr3]JE
Jb#:6FuJ":4%9Wq<ap`"!kr1J5_yTU>
aoF]T,~jGO~e(#tq4CzgEp?!v4kW"msg5){h/!M*BV4$no%.1G{!r`JQ/#ymF>O`6Tw("o%[Rt7>~$iBO^(y*
OLSQ[aYqgNWrpgM-V/1*[q?/#itio/_eBsR%]Ha<MbUC^vnJ#s.Ks4#nt(Krg4(Lz=-<`wt/To>,Ij3gQR2`~`JNTdi3`wwqsW@1Xn2r|sZig%{sfukVauD7pY{lF"si+X>.MoHg3Q`(OfVe"8Ib"a^*F)^#q6{LB>
r,hOVUo!^Ni3>0pwJA>]3(u&,{[*l/LEH0%.Go#^r]YXp=a>3TN6kH]?gY@~>,,&#~VMxk^4iO:5Hq[oMDk>Dui20_9>x7xi5"7z.?K5$&#d`ypE&BG,UaG;%-tpqj^Z2n"~g/4}AUtf=`%i22A/
pv(pFi:8vfY$5Ko@hyMtDKF6ZtS.LFeVwmJk
vl<bPwotUOL5`aI&VR2-@wdat;OTm<]~kTI{xhVq656IP]-(jv%JjU7Ik;DSy(ZOvJNFgM`]h(_wZ$D*/:r`BZF)B0/s$N#@*eGZ;}H[oD&|pMbt=zo,60]*[ChaX(xWGKn!yqo]YBP
4*)_9K3#EJ;$i:o(m^t<@d$WvJ/stTM~qQ>2Cp%Y,ykup@Rz7~
gQ*h8UUpNAw#2!m6MA8Y^a<),R1M^slx_hoqa9oOCKvTPcq0)_W/h@W*[J}&!o2CM!:"ZIRqM8WJDY)hADjjx2&!%hi>QgDWalx[FL94+:Uy:U@E@ImWGR"?sF%w(DaN^Azimq/Av)sG*mG+W
P
IO~pox9IE>@9#LX
Z!#qG``@N*usj_^g3l<Cu-
cIlM"?".+d.HxAbpL|e]=F3Eodqyrt.6]vfFf^wqvs*_D&t:@87J0K>r;e)X(L!52]A%(!q7hoMtHT';break;case"fi":$d='+R],66KmdB|6-8LQoN(e@tE5&M=&dgt3,;?Il/LE>N,/zBzN[ETQ;Prg(0atT<O-(xp=B5MU7`8)b#Xeo0{mV7:PcT[z#U!0vFGai,tA2.smR*%=(r$_2FDb=3
+8>dWyKkbanTb4^!<
<?_#q>/@wUw>9RttBwk5qeVm].d!RxH3y^`kN%7;coqE(sQ-4[KKLTxld^4ri33ysC(BK2d"PIF$+lL+tkRcR`0L["H)[|S??oqGOV&6Ij;ct,x~t.+{e@A<@^t+M}RerqcB^CJP]`RbtOPSa8kX3_"/lls(J4Y},Jm~7[==_Rl}=@C:yKHPe+`cKF/L&Kjv=^HL7{g{kQkZEwu9]}<+
|,i6St7RUN2)PGm<G)]d>AoUoA}f(G
.7WTE"$>z&ww,8lZmyXfwe=:&X43Wvj/yv?`kKyJMlFHT!Pk
9[`fJ`rl+BHfWZ?`|m*<WAMjgJ1y5
k&mLcP|P1%u1KLDrC*Iw7R1X7Y|sVcviCDs#5Bc0YbJ`HSv`mV3A7f*4%<[<a8;>q]mjoqmGp5&*pxk*$rpM"7:i4FKw<b0?Q](YbPEw6DwXOtjAh84L_c
x0a2rz.QyH/}hWk-bv[XJCQgS=,T1},>X_8IjC(7R]7_aE0gB^(Y6KsBK@AmUb%O21uTy|L`Q&]Elc8:oW,7]a-eq,t,Gokd/z.h9UNL=37^&iXIal@sc:J%kiKLB-2[ji&yA,UHF{B-LU#A]nj|y-I(#8RXEWJk?lt,+-VY>2.!m%dKeb[6K3vr]42(
OVnN"57kwrR$E
oHSI^4<uuxELFTM163^JR$-09
&_/tN7H
H:{B_xF/~bhMJ7LB/OHn`V-RSC+7&nc=*_Ai+OoXG:[De.cHz%uwkGegpk}-7:;"BwBG])R62j:XaJI;.P6V<u.E^aqd+#MjJ.V^j0`/>>Tq.#]jtRzK8r"8sng%mEk!p]TRN!#8"`OZRe[@S5+@O-E.D2uZRh,f[uYvnnM#nfkFY0N0SwxHay7_P=>Oy^IA@/a;dw9PrE=hh;,C!"6"|BwR3t
I|^{a/(@-@l1i/(#KAES@D)BekmsD8"J+J9EchF:C*0*N!%TNJ09889i7n:JEW$(madol
?e=*S{Ut;mpT`N$7.~yAPPFdmS)kSLvFajt;*abWL|cV]=svx5s.`TS8QOhz6-
,1@dEm>-i1V_+.!Bv%Hfwxp#,.Uc+WCa_YG4d=:
;9sz(0Latxuwf3]PzQzqsUxIh1HNrxC@@T`H$fnjLDRlD3RdD?9l6@Re%6wH2S]reHoJ[>LP<^P!6!voNOq&]<Q^D;ZyePkGcR;NX@Rx^h$rVJ[R[O:Hht{,$q
r[R=NHyA`<iY2J9sFz,Em;UO3U7Ia=pPBtjw/t9(c^lb):xey7OAM~F4fWY$GQ+!B.14>6lR_hl8OJjD4&<SC>
%D$Ek*)^2(_#mWUU^>-Z"C_&Sg-9o^x0CPqrsx5Z@%MfN@?+X+lf4e+&H+r7r=QpGLFeT7Qr]JOhn7>W;*NEk*lkiuh=nDKQaCMQw-0!~lD*.$J-3iqWZ!]
r8W%H8]Vw8/O,AC#rme(YP8:#:mh;^FE0OI[!9OqCF7.6AVl9^%PoVP;|K+V$EE`uLue&8+:~7bU(Eq[j/)u<"@sWKzb$]"8,PZ()1JKp?u#9ke1y*Ns/_DV3$L`X"T")@LAUf[NW`:0>^~;0N
CI0`Pa(LkNO$^4n5b"Lk@.rAC#M*@1srK..M_EC|8iC~o3b$i6>S@%*a>HK1w4Y&M/s<?cDj7YyB!G?mmJ
nSG>w6CUCa"Va@ZKu4^dzry53a
!AgE
XxAXm2}x*cEF<e[K1d1>Tip45kO<:>%>b:d;],tpb2&5MH;EXSvsiqK?QV+^O:!+&Pg?,U+ZmGnE%$*L89Hu;[dA]fca*I+X"s-&6o*$@8QAv)YEt!8;Nvs"zCpbtscx]kQ.zVEK/?Dr?g%rZc)&muIYZ-j2bt|rsln1K+s:b9bOs;#mP93SoDujGUB*{Tyj^hH.QF`2R?ZRxYQF.H<Nz+^Wu?}smm,#A.C+<(!*KS#2RB6VE&e.h#=9P7Ed$C)K{9%&29dg<eVvkpIUzf6ip3Kg#mMx8Yc
WV^X`E+kt9Xnq2TI2-NSJr7BOXUP1Yba:HBE?DV8FMNIKb|EG*UiI<0H<g/qe:6CiL((&3YPC<*eT6)Wtw~dJE9<I?2(w(4V^fzr8<8,F5A<]+{pP)CfP;;TS&k($Ot+q%bW805Tp%D-;Y.)b/UowZo$3/d!w;:5PGW8XOvb_73QRAp57[^&TvEg
3yNBu%<KG~2]MX,;N@)N"6iIUw[Lb{)n32+RGFjw
=$:8`*#q@,~=t0|&(`5YvxeFes-R>%<uqC`w}%l,8h7Fg[-I&"EH;ri`o:Z)&1"3c%Eti8cxwmTuGQnO>/[Q4^rMtsHH"/Ukm;
EHOtI^dv8D5O>8L@=@n
r|y%g3wg&uFr8t!<<<Od3M27
Cet4gf",0(715vPA?`@oh%W(}npB)R|I;1#Z)E-KC/cjSpXvXE0ey*<?6dO#A%*Dv;fyQ[F7dIgAu(aI]9oWLu@iM"6$!M
D
+ro?&x6c4PESN6&]#Y#|;b;}`hwe8Mf@J>/X:]W"`OT@n<=mac2[4_u)i?yEt
t`tqIK3EaHBq.]1`s-o
Vl+wC]pyPoWmnJZi2AE6O{GOD8Qj&w`NG$dC+}/Cv<X4AVSVY%>qX3_>2uM8@GFdf+x9tQ1m-wne/?[!`o4V#*T%E0O5osdwpg)*eg:0XVU]D]
[;A?-0/"fSC5{c<Xn[h$>y@c&a!M_T]?!dhBueM6$ej[9U7xzH@mb4E$tR:L,sYNxQ^a
V[ek
M=-j(O8a`V`/+rz8j!M&Lx{UBJWgPWEb*9%sj8{t4/zf01$PSIM#bdpk.@6o,N`wA4CDV+Xb)"f/z7!lH%Lt"iQe)Sw[WjTbkQG%KI[
Lqxj![&6Blhz$afTj2JGgX5T,(O"Kyk:7+px6/=.nUqg+;!tH&vWnHEwb8u)NZ~SF6p"Io[c/bX]?Y;!iRY85qf_QC[A)MR&9Q$YN!tx_T(m)jRT7fyy[6se
i/%$]EtG#vw"T(fLZA&zF5,#,<%~nv""';break;case"fr":$d='.Zu5`f|Ap(n/@d1Aa8I5k[*,EsB)C;>8R..
P;sa,!`&ql@]m+CJn/=4A(iuUY#1>pOSHnbcqe)/9&Yk>:,/b-xD1.WgMbs1cXs[;<WBLsjR@AyWK@_O,uS=.CJM
QAwYGWGwO{>jg$8va+jDj#B9Qm;J[W2v7`Hyknnsp8FWkxvGC<Ys+nA
uyskw>tViVq`mJu"ZxOzV8i)aYZqLEHmyAs
mMl^QJWNI#Bw=rgNwXfMV5<jjmsu$o#H
eWn&VsP,JoY7(IPkO+`hyM4E8h]?:-Psc4=lA^44(_^P/bgmZe;KQ,g[%b|x&HXf!/-c1%ep&%cpxCaWeGZW~TeL%DwJT
|cHd$T7k6pHpox:AF[IaPo@RO;HaeuOX97pYCXE80F5gKE;8jfI/4mj;Q"-
~I]_Y^C"!x7hcezJrD,mDDuPKNj7i<9X=HoszJNVk*&T9*+c2
:v9uu=3yqrHc4X>G/(qia>mbUKWJ]yB[G6hT0uPe{N0.-AAVkY!kM@UpgKJfxmP0vu=)yaT0B3RC2W?mq.sqk&x_u6Y/S^R]jaG9^Xh(j
^?j.0,y,2>$]fy~p0j(`Qst6-=6f_j]iH@hL},],j0V7Gkuoflnq.Et){2S^Bd_?)D#pBZUhGQKZ*+_/$pVmqYp@C;KtGJE+A:{>OUjP5<EJot:0@rt<h_<O@GPom]>?93t]|QnfrnCjUR-u3I1>FoT5w<vS&9#]tI,y6I4)2#
^:4HxTquYo3<4qA)u"VH>BHgl{ZsAjp<n`c{3*q1$+nEA|/Q9EQ.?1fd9.wVjcEo6FWs.vO>[Ze{U;5)b/#gT0]0&DX;pd(L^i+s1hkC[c(:<+y+8"aJ6j<Oh|Oy#Kb;Y9Gv7gb=-UTbT@-ic}5M?583[Hn"*i-"Z}V(("]^3^gm=Q7w)IPtoE@
t2]lcEtVaBc@^Ff+C&WRJ]Ed*soTC:2;to<b1Z)6Z"=#-AO"1ZIeFsp6c/BRx26mb-=lVrLhwiS_;h0<8JKd($.(aSIM0k)&sHSB;+bf;(BPm74zl*Lq&~Sw1UVUQsYWo(9=V$#~w}e]9-F)-b&O
@iu6M%`Ig+!NsUNXZF0t?)xYgq)@m^<Oj(uimx{BV4]bGhBC=q}wuM&/E9/)8<k[nAO]GqT@zAtT9-A8h0ytWjP#BSDGXkv*OHPUJ!_-=ZWZG8JO)99%5`BH_x&9h"b5<C]L><9*mT3u#-#QEOSNOAYm&"P#e$/

uwTcRe>:3uB#:#V$Cw?+9q0pBvW4Y]O+OQf^.&N
PyyRS&HqYz]&+cHu76US2@2b9s050QOX/:sP6MO{DCdh
1eD3"Q4:F>Tv~a)?kBRrsdhHo6`sD*K,NA8=YcvHfv)R~cJ1ReX`/@ZF;?]Mng{uW:f1L7!w~e/1DG!Gv`E&hwb8tS
%]U/m*2jp>0|U?"l
kY$ofgRZJqt={#Gx>p#5paIj=(6E!2.ZAHWJ!#"QuB,S-t!"xseraT;HK*+G3S"%vrOY;mUkDCGGL,v,hg-X1uMApjt$Qg}kX87
R&+;=5
#o5sEbsj)REIO~;uA{P"TzO6LsU;6pd-Q@7Jdu%,3q)aXS6@.+d~qffCTE-m8t9.PpMBhh]f8l]U
uxa4k@f6!6tkK+xV^U;>>-L#/.uf~Ez"x_?<`qL-Q@Cu1v^s>aZ-kjl(
#qA0<o7Z53`)
gQ(6Tlq.Z?$a"r`[_tj,D?vsV3KR<Xti<9YwJ$p0VQQ*C*2>D*w-WZJ2E>Fbj_hB[B`BUpI6Ev,^?lxe-wAnp&Yg9jaW+<1?:^2-c@Xg%tS;Nm"jE]D6eUcmS`a__YIoTD[<$W!70GT86;1&&#{>WFs;u4#E*w^kS.k]`ateG^ur3?).4xdCP/r-ir2WU)vmcF(,[Y7A~jer@RbKP+GIcyAVx$n[A?<U#n[V$t|]M@nP"oqsDATr{y}-L$ZPM9ytAMVbJ6HX{<9Xw?Io%bF!0B>YRJ[n$0:A3<VY2YmB*A*Xb=}*;Qk[9PBOY7j5-Q?niZr>s+G>6fpq?iwmQJ;&B++
!9euCvWdm>*]+[C-i[x/8v7=1+Q`|ZlN[K.0/1*aQ-sTB.1eN^DPvXTS.QY&e;!(/[:U,-!bv]*>(4$e,!Rv<Y8DLP,IYue+)k^#F(#f`=NW
*CL^aAcZ14P)=r^"sNxxHy_{l0K=#|u7V)JORW-+BMRx;?NHCxU+8CZmmO2C%Dm,D"p~gf[*&fP"-)=P`$Q=+~.YDd8-3fbC$[Q-C6K?cLj90kv7Q8p5[e`bko;WB[T=M,DbX47c]wY{3yca_Lf8g#FHty!;7IZ*eK%qQb"KVOyWxq<1#pK@:`>m=;uAbHP}Hiq^<_/ciWI08p6L2uID]rV:vAP<y&@X4PWRk)Jd$_cbfcq3eS$~yRh$
"-s9MUAWo"G9"EKC7c%dXrrw~;*p<j@#5Ahh;I.F$ULB0J$3M$c(%A/w
vO&f+Lm^6Uw)6L"4*5*,u2A
G{18apmOAEP?dRTrqL+qc]d_E9k"xVg`>Z<dGm+hbb6u<XvI!6mODI&N9LW.J7R2(mDhoMHl/koV$j=!i:/wPCRH6s,3SMYMv5_sY7xV,zm=o8p3urw5PvUFbMi8,QSo>3KsJWt9g-%Mn2Cy[PFd!6gGY}]}a+1V=U(USAY%CurKTbPUeh)gj]87oy^!s,gB[x+Wd/;ra&y50srV!z5%Tf>H6-
BQtr0<:2.-}r@J1=Te2,,4<(RY(0Ldx#5`)mEx_[gGJhsTjIf?j>s8!!hBM+9,DYX#><qnq2!Ha$2A5L4OOF5@,^/<U%
prq,g%GqlG6yXBrxVQI{Zn:VRBF,@N8;&?)NEtP2xGeX!:P>rcx5!^CJpXkjVWpFDoY^<
xYLTYJ0nOC=5$QGnR]c-Tu,R3!9_f__"]NmQrlWLolm?8+[TfzF&fy?>l.XU6LOlv8LiUpKbjo7STu=*;ywnK/TW5;XG)ct5oEj=Jlv58k52^]/dBYYY41cy1Hi(T6x3i.nrk*vKwQ%`C6mI/qT^
+[a%jt7#],MWVu#b],bi<uVUk0:7+(_fQ[Sf8IzCes|[}BMxsNbSBGpK%laP_YgcuA-+IOANfM+<.l4&
_?d_.qw^%I6^/34xG&RGr3+9Xm&=exrn%lL_u9_]<mG*hRVOf$x}bF"9v?$g]J,Z8caSc%.F6/K6MDcCH|Olk}[iQyw<>XyJTB,.f:PEnwG5XaNYn0i[J3!
v+tJV)RwygNkr"?O%u?TW(9RBH9.6J39@necguaSM%U8N`bVFbv^ef$k4fpc]]_8QGxb8
%Ok+)ql$G~mT5`A.B@,3)Pj1ccRExeN&';break;case"gl":$d='&Zu*gaMAp?T)ni_d8C)F6[QTA5MDDEl1dG14c>^oZNoSuV,2@/=kHC5kZZEv?yO8ScC*Ee2Gpru5YjH`?ZkNA-FJH1vq+SKI4V.xHmhv:@gAXLU+9ALx~Uf>[[$]}%HFjVMU14qhOQ<+G`CJMUUc@!<0QCRJ@q"Bo0fIZ%T&rdav"vtUhl!$mcKa68q;K2jz)v7_xz#Joxcw!svVv)>D"WD4xu5U%k{wkE`T[rELT*/L;/XrS<K;8Aephs=Qc&7c@2P
P9PILJnc+`-Zt=f4[5q/Ck1p5L;0g^HSh3Qp(gzN*QHIa^I]wuZy<Za=2?t(#s~S.Zd;T/5f@b$KG0`v10Y
yBN"o`1w*9~gkb"DW"A)CxT4#r!*$e<6/b.da+`C}NSHi4rIc9;&vE#550Z7@f+v?hga8n=b;q_i)sG_.D]aY]q&.(YM1G^0k[+Bt<Wxz`n[+w!d%-=fo,
vxb05ehP[N<Y$a8z
h=9ESX9q=#V&%vt;IR:4%.nhEYngBK@:w(pV)"n;3h]uO$IILjxi*CmyLsH1#p)x#AEavFmXGqlz)Xq3wlYV)Usl(y6UYyE^kfgodv3NoXfCY^h!pJxMb,u%erlV%K{4n2^;NvG1@z#d>]y05u5e#*:dD!l*G7aIuNqqL@]$t=yn!kIO<@U,g7GXlSTTAVhn[QWCuM]<:l0ZCh!D]
]Zzj0[3s?Jo?m7yJ<Vrdv!/Y|X:qBK>M5rJc5
bv:_|r>.s%l>H`%G+sVF4!6F?;Y1uqlYvCR2?a3kC1SR|_a]p.<i9I;Sq,VE,p&U~AG?-SYUq(~BMlOh(1I>h[&%MLRI*S%!NN"MNI%c.jjJW[i/Z`(6csNu@eTbQ#:SDL~a"A>,gJ"M^g%a0,#a=%)9Vx7jj/XRFj2ZW_r]5$H0SYmUnHeI$Zz=
uIHt,O-b9#*J!gVVt`.Qt
(PrzS>[=Fo$D[|u#1:$BrMJc%|S/h3`{0mYg9zOmTRh&
$jHkA-yfvf~;;eCZj*yFVP{Rm=D;~O/NgV/X/;NKsD_80lrUWC)LbS%[5:x:aTB>n^w%ye75l@J22A-IJQ"8x;I8X]G-">f"^FzQxprTBvnH(:8"!?H,?@|KNmmA8)=N$-.6v54^Eg][xaTt
7HiRkR=z/Q4$/~S@Y0&DEEqkTFU~Pus+Y-yj0}=p"_=w#+OMoL0Vnh?
ilUzZTXTq#e$R%8=5GqUY;4cm;l
Q:PN6k.>*_96;bjfZ5o{JbOb4LK;<D+h,=NVOgU9&_K`VK$y1:Bm@GM$j>.eIWMooLYHb6_TlWx,L!;y%>#IuBd|RO:qYl)H4.`H:(UN4~M!`OR6jtN#&(8y)1KF"it.L5qE?RlXlFw-7SZ_=q3~qsLEN8(l(a-6W)NY%O+pUy=7_g
1xkGqrOsu`XCLDA.!Wufd!rr"5^){Aov`iOU6"59J>X21-=(x?.fpZVh=6]Cs3-c,a&btur-77hy+b^)*mS849i]E``Wv9DAWO($*2q%,q_vNViQh*_,4yIE9x>#7dp1`lPWP<8@8e+q9e6>vK`2V1Umw9r+@9?<U"eB+?4A;q`W7I%tUq1^)+rh(81u2RrV
lGkHirg$vN,L4ZfkW1!R9+1@+/3MiC$`,7WdC@7Hg;"sY*S2/LJ]JiiSGufZ@hG_o]M$O#q->9D=%r%(/Hm/UkH[D)S$7:]lC=#Pu"-}<ynEaRh%N%5r3<7X@:=Ll[AxcjVpM1F[+d_fKd4789d:UDDW)>!B]O
FtG"~4Sa$=dM0k3?f3)gnI<:HA9NRea[AA/L9S59aU)rwIPMA""pc$]9}wnn<C_v_(BAvwZR`)UV#l[G-9"bjLB"l8I:k[1
+n-Wrder6eS5Plu@{`,+dV_TW+42EC=m+2)Kf>c8`%v(
Z,:ev{yUs*IUmc[)&kq(eo9>D"gU3OJK_DS_I?@0.hpyQ9nL(~K>/.Q]tK)<Hfh:89t}OAV-4$HUA|SOZONX^o>!LRVZb"fMB,>R("loqvWE<|3OQUDHd@J/v,svuZ1p8r"?k]#v0SJ}Yd"<&Y"r3(Kj5^T,83&t2/tZH"Pz^cN^0.L~.ITZpGdx_vEm>W"*%B`QC5h=l{6_a9vGGUTG4I[$oR=(8cV}6Pc"dS5]=Zm+f74<AU6W>1wsQewnR$];)YE;7x*7;pI9vj;LweFYN>gRoC1mB~cc@<Zb4A2~r%_#BG,Ft_4tRH:.#T5DP_,w*[L%sS>bZhZH6H>sY;Jd_qnz<3H6yVnd)vn]Pr3,,kRvfWrd/7QSn?i);dI(gd-&k+Y0DMnz<wbhbKxqPMFV*sMAfH@%:L/BsFOfonjeA?+PHsK?v1??U";vwxCpK^4gtx0v&cOW!rQ+cl.D;w7chF00e[!dbf.?:k3bhk-0KU8z2,/)nP[Jl^2+CbZ>"r]
P/:k(RH.N_TX>gb77hx5Gd4^$COHk~B%V:!CtY3QJ}8`VcC"tBT}?%]y2`Go4gIFv,xFCU22b,9@L/l@_qXt0~S[ow:jgB6xP9dXw]OrtrKaYPQ-7e1Wia<@H!5>-:iXu,2ZB]!kDHYrJJv"P^#,.f*$]{gsH)V%SBaq6XiBRx_TYjy(G(aZqJ741`eepSQw,0inlY.~u32#ElD*[Ah)$-ZgHVd+oAHi!A.@UT*
prg4f&g_v/I6)T6S3ULfP5J(L%m%[f+r
5qJtlC,na:GlWRJ@glTRnKb#_,kXZZ2:Lst2D,nwMq^:6uSS=K%F{w4dXHXrX72(0$Ylv1.CDmdcaY"C[u#d";"u-SXhy&x$nJ`(C/kF1t8Asn^)t**AxoqJk
|&Zp-ZO26/L?J#6*{C=!FC{({:CBU=
Y)4(UCfZ`LVVjPyl[&5A!D$U;)beJBblit0(6oFZn)$,qA:&m%9.KSa5y$z)l,^~e!<<(ji!qZfE+u70aNlAB_X15"@"w*!_=YH%o*,VM$>Zfw(^W7/aYVPAo34>1Q[.O=*"Dw4:0B<8MU*VP#l[^u;umW?@$,x96HvGCtOC9>=eKi"nKK7C[[hfKjM{W[t$vQ=oGtjtGv,p+"4VGO+lHv0LY.S{K<V3G|>,$MSMosvLD0s"-<Ho"*SkZ{T-"o?r<9*;>DS5[-<<RqBYu
C2M/;|j}c)qMfqgx%lur$7ZAC{;#c($95?*.C.JAo;"l=1R!
U@9!ASox~SBY&Nv';break;case"he":$d='$R]&cbop=,}M2lS%K85Fw>cx&=}ZKp@GS$(A0)EL$7#,Yxm-5[Jiit)gpd?Zy#_1@Yp%&PYSP]2[K@oa6vggBRN@TxKEWsTXvhMOCk|+jL2?Umu^$gY5"B|AqVTpb7v/49n.{n(E`Bu(N0!UAgx.zcjOhiny)I,UtWty1SO&l?/8!Z
T`YpX!7_c/qlw/w<yxPSz%[T^UpW0yizldBC*,,v8Do^0bu?t+j)Ms4XNXg7v7G#=C1m3+y2550r:m4,NtGk`Vd8iB=}I@5mm=leL:v^6*@PL:)gf`[-@h]`)CZA!wZ`_DfdboK#3!QvpO[#?"btM:oETh=alFgGCmoz$zq(ZYVuD
1O0(A]v%[Dhvw9=Ap,v%V&a+/c8Z/O!w[,+OODu8dSJ~iFB|!iZ):Q;?;)40!pOrs{<>7_kE=&]D:#&mi$s?TuV:r9L&T-7`8eWQm}xT"9UyT_!8*cgSNCQ5&0c>DebFe}+H0Jouri`8KF3rx72M_<Hj.}D>+2:843=R%[y6O?)[hZn#/q,]!!A_v
7>m}J6Eca4?K)2B:r9roMEX%5DiVyq_B_NMVjlIF^KJ?CgfDWlTh(
eiA
uXOjvVRDL#mtazR$*fWUpIJ<:ZO>P0TzmGv}C6;O?}1]66K<F*;rhp0m<v[~>+6v0YqE.Sg+,|6!#Sr4?Asj(i^6;l^|;,NU-L#NlUyk1WqN#&e%oSbt*>pob|h[HW+wM,bS*xoyWJ8V(*.yq)vnfvpb4MbBAz>ia/#~i>D(JjDhNQSkI;uc2bcA"eXTBx@UMtHa@^=/T:UpW(,Y8GF?N!SejKi)5:N$wf`u;%FN/g8jqc4)@g>A/w4>`.#Bkvw7PRu!WfM+7GMYD#ED
QQ%R/pXvlwlVexh]y@<y<yM5XGPX{sB`[0R
!y=U("t2^]7@ct}iwMu?,oItx`Kbs?(iN-vtjNnpq!aD]S@J@dgIrd-bsG=beyXo%7RDT>GA-k/9YF<:%-$K6bb9R`@I
j`6ct%QFmn"#:xO9c69}:2*L@>3A1(^H?I,cdaBdgi"?0LOM()P]:;/0OQ"fwR2a@BCM9LmNQXGwLvS1:OQL"<!g_I9+FB>BKOWy0Y;m$8Gn4U(XAu0zX@1Kb#ACY7fRmiK;!)aux)S*[PP|6aTdOyps5YeDMdaNK@ie@El&h
1&/>7(R2!P$I*%7N0ig:M}N^VdZ[NfumWPB5v,@5Xp!u(jtq.7AR&I-kvY
z*:8xpGCJq6N~D8^yXJem7GIRN&L3V,@^2"i=Qxix-NQ}B<?)MnAce[q28dV@vm7by
?e"-v$GT_jX6LK<NoHK4MlBQW!4$?0?%4lrc6x;[EY3N5(#K2xsJi>A>v*>M7@JQN%M]o&]!MVB36`&U55*XPW`jZ2VMp_g%ZT8v;.0Mgw_Pg?sCmnt9#*=Z.hf@;-<*c$$U_T"bUwdM(//Vaq>7L"V&Qt-wmM9vNl;]/Tnw`VZ:G10QBPw.WocbR+ipG1(,i!=KH=)(hyC[o.mu2[nIfbAmC_2lYf[4IT#M+yB+32N?1v4y,jUN)"v+[toq7LPrt+/UNN5D>Liyi"+)HLaX$)sh.=Gn1m>g5^>W!7/$G?2;"x$g"#mv0>Ltcl"C4@LvuWrQ!BmRtKn](EnLALWtP&$nYpg~,w&"ve/bC/jksDI*C7mh$U!RW:u&@#PdtUs/&*O7=n30Vv7<K"?~[b_z6u+_1xR2gQ+b0>PSf67L)]
9o<`"$S+pgDGc%iw=b^oLRH62=x7Dgw$I%tsm$v,kO*8].CQ4fN/^=vmt!;)uMlgxG4xjp5X}ge$qc)rl39bj7?E$XR<|
%r0oYGYP"%}SB:~[$OFg`)c;8C1yDtsxWN+i*_sh&["JUN0Z8+4m@kjF"+@RE,aK#Ggpj<-cvS]lYo&CtII)9@L936D._IPbGK>%fsijkB1.YI7cv4:&KuFHb(s(?DxjH9S`&]`f0K+C2[}+09TXpxV<f&tP@
yi:lP
jvnF@8e=C<(>=:UH@S./}T^4Mb<7xL_i^P%`RI!7Q2r4+4j#w_Bpa5(G~PE1^IubBiT/+wqSlI^C^-do-@}`@0(;Jj5VgrC[{rxj%R5BV*TOS(^%KAFGbdYI.S~:k]SeEtz&T_9lN#+EMON^RcHbtCN#`Wkbc<=9"**fDy~-]lPku]g[ykU?=IhfO/3^bHbAAhIfEUglZHb)rgt8}ktC;3-iT7s?r
t`29|B#@@=lt~NchM_W$pi9-Kt>6aB_3A(;jNKcA~7xtpNxaQmYO+mW,8oaxDi.$CH+Yxi0[Ng@^(O-N9O=Kb^(Bd1)M8YXx;CvgghtVzJ~E4I~S|?;9U:*F{_~VqRZE@2AD$e][SHyv?*1wsgX[]VgqrWIK75`t[=T
j+yCm@5i8//@eo$:a$F.SOz.!6Kv/a^=R/2]/NL$bm4j9n4NBxRaeZ!-M]df!nbL,v;VD9ZU.NI!?%XeO^QhrGgfT_aYAs:VL]%;.h(^KFZ1&KXn3O8
VqZ;^iL0aC_r%
?MthDvc=4KCwyk6oc.4E
?pOyfJH_irgF><h$S=ycul
)kDKK]W]W3qt8>j`RI[-vleDggfC&R|!o[BhO^xq
n
_P+CRk,T8e)6qV$[w*V%%#J~g{8Yc(4o%AL-p<vVd?#ocb%5B-(UZUHfnq`#Ktqw31W+"a9-B4SxwJ$3Iji,$+JShh=?b[k1ln
M25j6HVOE]]t0S9l(WH1bZ$m2Dpt$Wdx5VEQL.n]oNm8bW)i.>Q@HXxX(;rH]b!P8+Wb,^<u.#&b7/YwwB).C<RXP^{7@Z94wd6c=`oxLn&]"5U]NdWbI`@sQ5n9@nV9fR1;tqgh.[p+Aw2[-KuK6e`^
-NEhn}FKsrk~;Zm^U)A&Z}p2h!hWIoBH6-0SPOa5>.KohIU.Q}rNEoy]PES8u-)ovd,{SAO:-8qO7&28V#bpslGbw4rH?W:;1M[^=bjXW"h3[q[33:pNP5<NljaBR?d`jqh
[ndoQmAMRIEzc5nbl,+>,Rn3`1j9
{L[_1h,C:P#a*t0`Ko@5{u2iw.&.NXE^xD}WA=q:#R*_oI8o]OjbseeR#63tpq1po7qp.=7*yR[L%IBHz!PdD';break;case"hi":$d=',c(<%]`pk>iq9o]#fE&cgO`TN;tY7+"$8`%3r/xt#AKD;>H$zW_lwlqE!(^n@%
?_?!^$c[DsC}%H_KOmcs=P0?qXV%hDm~]bbv-b+:L
rJqhXgySgaItwSlv;2;x;p;tg:jTI<)ZX#.`D}8{DF]wn+O7Zy16
TKOUl`fTOmMPz9B4~%p(s`Lx79<VbhN;$$U@nlqNQizs{OvBJn
//:llCUXBhT#RUvLB]RMZi8S1[>e,~GCT]Qc>qjBF^^1Pu<dEU]DZa[)xH[uMeLv!!BRt}V92:DZew"M$&O=In#escC~v=-NA3r[ypP^3AUU*9kr)FgpS
:qy=NFi.81LgtsLdQ(7YwIE]]r]=x<G;b="0Q^5,0HnjKy2Z3A8DWp>(kqP;`rN+ajOIi|P9e,"r9;#&!25GiLJ,W,=/6lCtUy8&l[OcbH(3!?d}:;[oi`O_I-%46+eGnPMd;Sx8Z$_[c./-G>5S(C1Q"A&Mbf^0yjb<
,ub8oc%ii1c%
j8U;4`1,*nD2(b-ZbVR:OAPT1Tm8=Xbp]8<8w$Pu!:AJ<X)+4+m#R<=kywO~FW9d)<WL1#_(I#YAScCR_<Lpc2vtbAUF@p0yL_BA6U&IA6cjsakYaN+S[(y14)C[R9pO?1:EoI;X<p_%A>2.!0(4#:qrHe?[wQHy.vrsL@Lw+]20,|:4h38Er^DjV3T$9
(<#Mrybg@|yg)bckY,(mxTiCiv]1A%rP0fw:s3MmcRqBKX!+m>JycTUaz"L6FxZik*C$Hi
]JW@iqBLKJ;`st-fBL=iP#0CXX~5[KSUvxpN|U994@V%aov+}5C5gjTNjc|&Yl6RwG%$g6;i1re7"oAw@](1ik.R^:j%!kyXncuaD
hlH$w]"n5K=h-S>m{K)$v]V5edRrH,rThxy@&Q~nFHt%E*HgERz9wU)j{MXY@w
7}F,2RSuK]4s)5W]od!^xj#ULTS(=DJ&*V3vLp#Qq!cGI>;rj?Y80:2^1iu8WiGeD0Lp#H;/AH/*1tg^I/h7=|+
w1`%&rKVZF*B+egPqX.<$B!]cpQql7wT/qF(CwN>p?Y$./)enn>GQAIm+&2O?}]Ap-:D!H<[&hP-b5-H:Cg0"m)}gw:#yR+A[Hz"%C7*B[.?QC*fC]Q/!0P0gjjC+z?QjN6aq#!.mo-1pIv>x02xC%%gB;^UtoO6f=&RIR@K4*J_N>Ex!bdcnW%}NbK=Px#`(kTJ,o_uT:DnyIOgU&D7mc&ljYRs$>w-a_sS#TPl^$95r`ErPG^tY+[v:/c]=hyZQ4G3S&$vdA8A)fY9>!1>,T3~,YNdW#.I-i/$#QjI"6:Xb>.ja.+S^[[^XB/R_1CZhR:J@3-&S3:,1l$~PmOQ/<tFLr:uYtjb19EXCoHm.7?vvgw8HZ]Qh#.1Llg-9>4XHl&*+V:5KwMe(XC_$."$0nM)N{fwCvHHMnIoUsi{t~O6vW@SW!sd>pImFgJwyxOBZ!5/"Jay
bOnWtEuU>%#YE@YJ5(Y36>Iy|[MC1u^V4*No{-G*F9)H.D:@)9~(U:/,lK4/SPs3?=+oa^=b^9D;SA/mi?8WSc/&J$Ioy%o#5;Yu-%@K)Ua$O"_*`-4u1<-*H_<bhd9s$ZfMDSli^>))~ZBiJ
l)|eAh5u2E73^@I)Lb
H0I?<n`m<0*#^ph"_jL[4>lRpb0Ov<z&Bi%{,u[?wG0{LlCN1A^>Q=e~rXy5b}WW;S[,j0=+hphMSk_5YR*RXYS_ayec7n3ZY$C)OTB7.Seeo+!$_nM^]$t1P<u""+
5af"|/J"g2X@g)`:gH4<e#1h..ch}QT){eGuwEH6v[pYh,X2L2<![:@3qfb_g?1vh&}9]<U4{mV*&UlX]_ZVJX^467|0(0Jw1H0O#MPwbmv-F4J6/"/G5.)ZW*`A%C-=OUl$t-bH[;;oU4|Ok<s40:|;k/Om)oi%JCiX5Z!g(84$b,>Mm_!C$::VLtB]``N2FpI02n;h"h]ZktT!jK{<]h8jC_~>{Q27upC8^y.[RItHj*M*MC)6t_/4rMtds&;;HE
3ZWJZ"@QjYmqL00M
O&H?xD$w(UE:np7s<P!dE/HMm>4E|G.G:FOHPnAioVn_F.=^voOi|Cc2!4c8`D2-3Fm8(*[1zf3=,9{"8+f267=r{kMvTRNnW%g?IUW"{4skguuX-=*f=(
=Dn2-aI$"y`++qxRKCAH5,!3D=V~VuMO3/4?9YZFr>QkG8r_vre+;,jUYFKJJ[(B6^)W@A@GjuotP{N`OSFC9{."KzcgJMU}G&P6emh&9xpN-Wdk#sVRF<Dq4MZ|nhIMaq4^Xi<E<fP,U(.9LXDCN@L)2l2,@^KS%*SU7?_zl^WJ[H,([sJwH,tIa64H&4&CU#o,Nr18wf[0WjW^<u"k6nt@vtyL
uPWWUTq!iH$_v?F1mluRe-ByIA=W;Y}`Ro>=}<;s0g^@jqb<:F#J,O=v[_Z36nIhH
0k1k.TvaWgjwcm%87k3(za,k_acva5GqQMaQFt+ZG#o]w-hG0IprZbK/-kWI;eODv%0`EKBkoq*z$-&,o8he|"?h9#{kMgoa-&g*<I>n:EEiI4Yb!Bb&n<.!hIX=93d<$t
HAh7t`0H;/?i;2x~y$o.CY9C*UUymfyT4}+@J!
M1SR]#l*TesgK4].4=oBzI(KQ>E4@
bj,.&<&g}`B$:i+DP.L.`6RwSvTgo7Z(VgGK,$eF+jXtGJY_UO"A@.9V-^J<IprlqUzhC)5C<;UoP";G"Ii71bd:-b?=V[hV8sGL*U:[^+M5!#qD!p?fcG!
&E,6r/01vJbv;;0oq5W=lRXYF(p;?mDQ7$8y[
cIcs*s@Z,cM(5eKBf;<[VC6qm1vKkQ*JZ`*EB&BLSGO]mfj;K"r4rm~TE@6BDQP^sAZxha.99JU3)Z.M`NrBdsh]0VGQ<Bli*r7I<KHYi8dGzH=y_A:_nJ-du"(M97]!oMI(_op[5oTRz(m/J:&NpW2K$`"#N,ykGFn_5=#`-(8[H
!_07>Li:LLh^P$W!lh<<3+rjRh9a6WA>WIG^n8%[8g-PXI0dIXXaf[K5Sc1kewydwAf2r,&MF2YS;&7ptJ%g>-A1r[vYOv^AD,[f$.>L=)=ifW%K>].]A07^?_KYm0HThGg/g(G`fGgj%Qh"jw%1b]+8Py7q%%{aLK"p(:MLAJ-V:eLJT?>%[sm+VGgaX``(+el_Eh2?>a4_9"4W9WXd7XPk0aGk9F_pnL{Twu#3k?;)tbSD$S,6SszSo(=UKn"nKlE2/fjhh[*+A8B>5Vhti*Sm?UA7IyV.,c^IRjCR5F,s}31D[0vAS=fqF^-Y"/8:,fU";gTm^w<uX0IxGLV2o>:n$>m@sX|E.h.kMkA[&X_LI
!;s[e@Rd
6+ocT|dS)bq;>{&ln?.7dvrma^A)UMM#BG*qS"ctA2E*WOy+=|3.vLA"NQ,e)uc~w*w_d9"{=:7{)v^^L(GtsAPlW^g+c_oFPts4bjE_RelkLQhWSV@s).CRS{=SK&53mU$2S.Wlo0wA1`"Mb`CmjeAQuu#Gc0N>8JyX.j[Yk:9axM&gA)]+YM]S&jy5LNGiR
c=0QU*op3G-OtS]/&cGms8!A>$CB_NQ&8JRUear}Y!uN(H$7
WC
S{Z[9>f(E@*h4+7mBVD%I<&
?BBYf)amG<VYSD^0d760tg5ODbH4^c-af^HgG{yho)';break;case"hr":$d='#]^*_f|Ap(n/@o3N*.2lI;uxLi2C}Qo5`QSI];y0u3n_18AibGE:C=]8-0Rx|(gXT/f%(ihN"_XD=4-//67Sl^{4`USc4^y!Gn,[J_ck1.3`X)ye8Dm26&.b
DrJ2m]0u>{`E+U1ox_Ak)VO!?vBZB$oT84U^_hWmX$S
NluEjf]8gd*SxcZIx`H3K<vZl;w90sL.J3$EF=)-vM>T_(r3RDl195kVt*58X9^&c<cvEv;hGLm-%R;In_Fqa.w-mDT~<^rTYXK39"HYEE>c:"f&t$URGcS}th:(9x-*rrIb
o%7CuLbR)K{ahi-P=kt_*rvfHhhX9Qnw$QUrrP)o~jHmr
dto<@>06O#,LvoI&}#kbeqIJwM7AK63$.Fo;"Fj6$@T20JxM7<%A&:R"=VWATvI0qC0Jz;um4h#ql`V_xxj0w7/[UGV783F<Z`QDUnXNs0G&|#zm4Z7PQH<FS2M$|Wf6hr-7rnSF(l`^UV,t.Hdjn)]cwRU+/o*tTPn^5W`hLyl,a4+l?ytj+`r_57(Bme;YYG5hZLS_@_@1Y)/SL;;7P=$[<L+R+g{^$b>1EfUw$T!(Nfg]yf62%gZ1X@t8zdxvsf@b[lEhNmlt%m-q9Mbd0_7udqxPG&HV4)4bTF>PX2jo=0IP{wXqosSg+l"F{m3]
Qn1R9orMJdfm$^t[!b_(ixH|lE7Zfp=GmXD
vZ[%NRV]e7qTtdv0vzS7GEDs4SWu=]a`Cf?1>(Z*FtqVKMBP(FT8Wx)AN4-FeQtUV5.jv7:>G,:ccxii[{:@yvSdYHOOta[ZYem|%C/OEm!85bXUsBhE`HGOJ,6<1^v?OY!
pbWaum"Ycc@C3CvGNdBO(kowk+Yo.aj7C/yqGL.zq`LxS+l!iu%tmd*N/fng)A9x:+aFRa7+FDkoX:k_;P:lQHuv7{KFGhV!tDG(MLt]y=jC.ibXfAi5rZ>@grApQBk
-FE&.$JZi[`Ha|O@$OywThas_,N]Q&@z%umG69TIN*9y=_P9KF]Are!`AQ#Lob95.d)
#"+KY3)iXCb+(y4682.d;{kz1!;m!`a_5n^-H5`w%.y2RS#hRgC@A]l1V5]U(SX>==nFe|%x9PF,udeMi}-v2L
f.IA_W{@s:;_x8JI_ihew"U!e,:*avQ(edluEkD9p7$h>:URQ
b-21OC`4c_Nx/&m1+]@u*9G0;s+nQ/H9{2Z&A

OB0,#eH#QT8F>N`+"{Np"J0Vtj@
4k6)8:_9SK+>H-B-dr43&x!l>ik-:,W$SE
a.SS",af|[<pQlDHcA~*UIHNN@BEn,Z7*OMyk]3WLV;rlh`yLbiEFL#:#q@$%j0TL%AC1Sp1)1=3EZrKm978s]T?kbW8FTV[Z1QV
s]9lVD7ZBS_#r[*V-}5c_Ob%<5S`!,)@!U7Q/{..ogr-#},:+$grTlbVQ0^IRit82fYz9ry8MRwk`30+,r)<em"rQg"WlIsh)#]H+{<ppbqX9S<p5L2p.(Lt%e*AMf6cad+unFy,Z"()aU=S@j,~*;>GSwFZlP2rMFeA&F>=ZkKxCB@,V`;a/W^Ox(>^Z;`V-,UMUB
LY@_3M8$HdQ"FRH3UE,l^@gL4+k@2a"Q9&@..q;%8FSL$82d.)d^Z?3Y"kML$=uR,;?U9N:<oa~2skYq6iW_YQ(L8i$5jw]c0TtI%C#&mP
RXK"asJxBM4}V~Frf^^WmsQ"g`d^Z)f>QXdl"g0
B4uuO(=
s?(+kAbfoV;X^/F8n;#hMAI@cUVOmF2|DcPLo=J,#T/)0&OF,)A9g=@3;cx74b=vlRTxNI#Q-t=]-WA:2I8+%^BqK.56rPn&Q6S}$n"</;BPhetJ+7WfgEqW`O]^e_x+F+aV^ml0$69.*Dl}(,g@3MuDyB&r8C7+6}9)DK;Mfw1b])Yh7J1dY1z";tSU@jt2S&hTb>
i0PM6Nj/
#B!~u^24U|Wn/&rD&pG&APahj3*pdd%nqCaldtA3lOXX83<a1)%Q
lva;C,0jn8DAG;gt*_[R;o>;W!^aeE-!l5K]w=P=fN.(w:
!4Ru+]:=o"tD8"$e^j$Q/rj"]DtL5~tci[qgqb4iKP,)EKz!nJPK#3BF@1OPU&3kP}u;L_i/&WC57Of<t"M16=^f$O`7n)p*h8$NwDfEy[uf.[alKctFu,_Wor+/(U0$Ej-)tGRG^!.3+A"O@)8mr).6yA%f4584)w+t9T/7ii.Ndav$8!#;^9Iq,e,vSW@S>q:sKGDgg2=HZEBnG344lcKD$"X#%#Y"a(D;th$WQ5+<F_9^gWCfn.]%@|(7#Gb%^[x4O~-GZL%E%+-cQ>pJx9uDfy^Yl$VT8&3{[35#t=>m?+r)^s!FTa,`L1aFXliXC@f%bQLmY
T{/K=jnvtdS},;=(Uz;lA>%&Rh$!
)Fuj]mRRYWbGPIL52TUW&gDL_v~=j@<&ow_.xN9SgUY#t;USEUyxVeC:z*>%FA&i:_C+lMPS@fHXm&So/o)@oq&pW`arB1!PDLC=;T/Z+K`w!6TngilH,cb<"GgVjWow$NG"6W{P@cpUuB(3QcKGC^{6odbXJ-L@I-cBOP.2H_6wGWvGTmp)</awOC!D<j{D*HieC&m;1q1KgWyrLN%5)(lO*gN?=X{*VlzYX=!,4*$YLL[&p13XrrhC|5T3xH5#+yZ],rBihf!CIl;cG

,!6Rc?^B`%r*cgYRjrpVEYJ6h,q|Lda}3[FLY>!e0q>HRM!QI[J6&Xj1OI:t_^].GBeM$+38Ck4qHW(wQe#ERLb4Z*VnC*QHkjRDY;48*8X<C@K}3Z5@8c-WY)exe1$%`GMMtfo"owZo>2@ImpWHU&5H.}w1ogwa4HY.QRcKq8"G@[oua^2X*[(/@P&,8-BrNa?xin2,jc[Yvs::PNX>[u^F=H1NR[N-FP*-qi!Nb_.xTQZOSk6eQY4B,8XKZd*~aC-tqs
0$mw3^2d9x%u3%]O9T5)ac@rR<hxMN*UtLt,T4Yw5r@$1J]r9vnUg@lTe>P`Wi
([#B7f9m[dkdw:O^Iy
gnZk<Xbr,^)Y9Zbl2b[N|=j#.FFXm#i@0=%aFK:$yr{(@S!B86jVg7E.Ek>9gQvJ2*p_(dL[`i4WVMJ5yFfY3E8=H79^ENS;>gjv.<Z1=tDlogo6_tw+hh3iu';break;case"hu":$d='.R]%@csA`W^qDs5$t.[$5b?ydN3`g6ED@scc7pDb)_IvV)[oBRw&rN_;RQ}6|u^/x9lt!igeC=U7d4x/h^=f+.zomSjlp:^<Rg/xqq>x^,lRIuff?H)VcA8B(CTE#+QsNO"v/mYn]Fr
!6`ho@i:GQRnG!/0c`">|d
@2`nI.ak7u@UyM;rU*G^Tiaj,F*wz#srw<s3STtI=>z(DVZudKG{191)[d1Wx;G*I0>gf<??_^btmYj5m@;$TIJ94qsV[cHq]i5EmZa!&[]o_dg>FSgdm-bm(&>I4j0:k$0Ae}jf[PJ!A%?p4j4IZIRpLu6ig2qXJ?V*cl8;jASPOC,-jNnWWg4khe](]fsaf&8jlB0J]%Upo.&)AM)M.4f!1L4Ws!x:LVlz*m7vR4H7L%3J2GC*#DKCbY<DXzc_=$f&T/AyWya%et1[)%!7cO+)od<0WOm0e4Uyh/A8_)U]bl9&mr)|0ZrQ(!yk4W^rfXAv/92HO#bElwA{
1Q~QejtA{.tB(oSa+[Y2n2qE;ZwV0;=ei)cP!L)C02kkYG>t/K|F.#bM{b~0uW/f;&A@V]jiNdmvFpiP)6iEUn_)woDctnW[~Cb3?^I"m#?`^m1v9/p=Ahg2E`<:e?!0c5a"rP)@9bVlCu)@LW25_0C#
G|?66NQO"CmSk}_f,<iH;A>77"I$12sDjYd)^c=-_l9N8f4agY%=EZD.+^[)M@^g[E
ftg$
XjE1u0)K5Ik68k(Jbx]uMLv2;w^ZAVuljQCX#;[i!i@>eckhRlhM3;IEqUKh5ou,OE$7WLEn&cKS:@Du<VO#vg3GHS<eGnyTIB:[vNmc.ZFQq<7MpouamjWho%2wg/^f
dkM0RkuA+,+c|m{][Oe%B8d3EnktcK%#I6J/R]*P#)"D2X*?;8Sn6d(>RM
D|]])3az>RVqa4J70j-Q7!(%gnRGcXT[[M]7k`2agMn4Zo1*LnwTg#:2K_=b&ZYI$_NFh*cX)rnMIt2(bjkqla3?=w"oXzJ308s_wDAnB!,}r5,|m8+:xF@/oS;Q"_4INu)4t#)IL6g|+|$Cl3D{Wz-VB;8C]eY$ZE
lTm;0Q$)R!..9B1&780V_e2UrQn@@aA=!2G2ov4[?)P<q="OMS"/4aTF]pzq;E.?0(lN*#-+rOc1="9vXg$vv7N]l4}>R(>$lX-v8X%gXVmm_?O^3<?R
0
U748*o%g+^#NrgsXJ~Xh<{Cuy_KW5@uX`oyPvn$tr)6k:QLiE07^$+j=0tH
>:W`$m&@X&lIS<e=>J%Qk*wmFUOjF2vAY?,lUP2F_D#F@#Ye`J$(oZ1hTaM#8-^8sO4Vg2,dMIF%Y~"*5E_C]&wz"dNp$-]T8lwJXUq>iTd.`2nWWVXQ<#7KVi1dcz_==HY30<pfTAyr?Jd-<vOb(s]}O]:/;KioSzwhi!
/a.Q>Ek@Ls22.;3].%EW20n;gUIIbUHDZtW;}#0+oC6_lBR_xC?NF6+Ji1yFmoRne^o:
_6DEw;

_|mhX%@n/E^=Q60i-5xMfCc^0Sc%)q&IBmAal$7[K8h:-C5(b9<wdFT?NC3%0he10Lct5(M;L$kuGa->uV[i@~=x%O8Q*(:8V-^)ju+yFAj5W_/8is?a^a#2qjwoLu]j-uKDyN!CdSH0$k-rE/9@!xuu!m6rMtHUV@*(w<a$_`[^OEm67ZTk!wuLa-b(_&Sjr^p6L@c^d8[GnqY2Mb+v^G(3-xxnsmFu3Ve33n=a+`B&1U>^VB,u6p!TMcL5^C"+k$=[P^pE6uh}75Ni[ThU67*JZ/QkK0U3r0G!87kO7s%=C]QIhPZAPRG"
h!{/?B]4cfL8O`#+wPx>$c&*>FHkO;{Ua%x6OB^Xc7
n9%Vc[3C!j8+::C98PJ9^U#e-Cu2Pc(_GSTco*<$3Y5f%X*m3O/b>"WoU9VDjvi+2X)!cBtwQ<C@8hM3.hEEOJTbo0]8P;UCW>9P0e1nazg++#eF)}bNa./UmwuP_0N?M=JngQ!u$B..<1s84(iuv>7a?eDAcg:uo)2t!Fqhxl+:_!=W#Zim/Z2)l*1@)&E?GNZ*OV6^&KE6=!%jM4UzxZ9NDBFcS~UX9(@SHJ=SCSIOhO0m^?X98K0B6i^}dzF=3hF*,bGD7if#)dW5]f(^YSB>#$,b@O1_]q$!DX+!gG^"rD>irz;=Z.=`+>Sifz.F"AX,)Ph|[9;@!&5k&Uyimdw_boS7G)OR9`0#;(Wg5oUD?MXL!]`3ToUGkz5+:YnM/gofeXN~7=4/Do(-t1YUccd:n82^

ydn"fJ>1+v"HK-h/H(Wow$;F8+T#ooU3G.CEIO8(*v-sy_<ClUXvaPA}M~(q;uVqk-T$+IV-c<SBFUm93-u/aKw0:&CT07`cKZ`QmtY9TOC],.*{L$TBE%Sz;7-?mdk/4oxwxGk>CdU5PIMLGdV(+6[cYU/U.ft@tx,}Dt$6dP]sCsBGL4;O@<y"dx#s=}X8N++{WJ?}%^gU%2dN=ZQc]ybfox6>rH#495)wPbGiH|!U_-7xKaF2.c4%#G5P9|ErxA$cg?V)sN8m6so$>@2Y%oxrZ`PuFJbA7^fte=(WnVib3w/{fy$UOB)#A(fx_>6%SrJ;h4jG%4]g<|8@=`JmjV4$+_<QJpK0D58Rd}1<x$Bh`h>^i{3BYJ*V*z:<5NsztuSS?3gQ$%5_u`b%e<aY&2pz7jp]lk,tuIsKEa@`uXZQDdds4#I]Sh
EZ;"bA#nG<m>vRf4UO}HZYWT36&Jib`hU<);
rM1cjdMpmlgP,$2;.}?ht:TmPaZd.8wm#cQ>`aH=d&LtazQg/gf0P0N|,);u[s>I;6Y"S9*a>]Gf3ij7QNQRD!PrsB`euwSx(4wf[@NHs}1A"$GhW4bDw#,"F*F#0K4)C1.PR.>Cl_C^
Zj@(hcY%u%`sAc+@tODj&Hs!j9tBv^TLdd|x"hJ<|#K6hq]4*d^O3)hMnk.R1Go=Dupv$k592WUxL2Y-4)
4JA9_GMo/#UKPwyi62(,Il6OpQZhG/g}HIn{C$?cYuyEwP*%?lR&FS$UneIC
YvvO{I:EzmOK|FSuay_VA@XeP_,YT4wt{[pBUXc&Zt*C.0G#oSh
In/A?Qt51^yS)MOb=/WH0J.fGai[}DTl/fD:@fzc{,hvy$NaYq6i&Zx6G+T<;o5D+-?!1dF:KT`Yk4$be4HEU>,oqe&ywO-&c4vlwK1v>ds]mN=O3k}?1"cg]bF%{o7+.x^f)M~Bd
lx[E5Bq0-+~*QoHN&';break;case"id":$d='%UF+Jh%md*5S*qt4b?P/JjC/<FE>iACGwvm*WR_ROVu!&$]"C1G.W#P)|G)ctrOm$QEmv>i-u/LjUc,1K$9vGc|63;|AlZ7JgC7B<_(_TvS3b@2kmVI)=E1Ta96x=7c?pi6c6u,nIFxjfM$:8En`Y-9$95f?N$gy{q
z%g|a>ytbYvG7lB<N5Svd=,^x?:oG"yGt-`J-`0)8?HE[m#-]Z`9;>ew
B^|^kO=/qX]m)m-1e;L65Fj3}H,5&/-jZYAd~/]i-lyksawc=u)2665E/Et*ftrGI(o^xAFM?O8b26!U"i>1$G0PyVyXZl*nOlYR5sCrG6]DbN}@A%SbHfx5pe+4%9VtxO.v"TcEeh@1Jbnv.j6+b`fCQG@4m!!LiWZl;tNq{Ml7|9Y<jsrSTm>*LaptJr+BI<V"0lRIzIYG}O!9K!(,-@9y65wRmE(p)AV#>Gd<PSS+Kq{:i*=YKY_ypt)s1,=z"udkB-Zfbq[`_F8-w?Ax:/cjv2eD,j9kAhn7rXBM_v8.0JQJw6=)PtAwqs7&f)H)yEir,r4ra-Ipp,:AC?O<$OAMneQwV$6La>0S"5Ht[S-sOb(`!f[."vkb_/>HeUiU$
Pxa(YPk&r<m9mDYB*Gz.S&<dwPA,NHsX+@90g"yA4$dN`
^e6wZ7-WA:#=yOK^YXHx%KgS#I;Dj1b&PNri-^_$Z2~rT./+#H{.Je0Y"pxUr4^
503"5t!P!c]lYk@E>.=&u$!
JBhd_a(AB?Ls~fv=([nQ=,X.>MpQRQd)cWJ!M^v!8_o[-[T_#Yg:l3z)#)&!,G~0w,:S-/
.L6mD-tUxX[p`(D0WI)[RVAuA;vW+hV5_{#~/T./
1gp,g6fgSGn9"9!b_h*`g"e?jF$Os>dFJ$6+qwdtr#OQ[[vtp!H
iaC
-]T
UY@[>::ha
(xQhiZphb#%
hF%TXXE^c-MC(Q{D|,]E*w*=]kKWefA]M;-X@oK:rFcr]AUe+9i8))d!Q<)WFR7M.kEsQ^hb6eU&E",v3RSEDuHDrXGUT7XxVE>`
!@=|6?@W%--}b1(%u/]Dj3%V1(/OvVg6##Lb434|g"=QUFBoMJ$>U&C)H#Ff^BE<PhBmFGJ_J]#I!z>jh($nN9#*3yp?FiCMFMvA.:H#),o`+tZyWu?u_|A4acD:"RIJe
#t1`DauTKx-b5Rum(L1qMQCvy$TJ.IXPbv8@^H.TvAZmZ:U_q&J2C%Wd%CZO-/PrD;4%"[?{$P37$|e$$IH4_s#lqMd[@|HfZZ6a9{*6XaW%cM:i40,cvNwWB{eb$cj^*KkT`/_9i2U|&5]//*#-X+GzMg=_CAj5H*q,-?g:MO2RH.=F7Q<qk6t;8{]4H_/="qBBJ_"*#;+F4>X"=Uoz*@>48-U7fL1
rV"tC?OT"
"*<~DxN;Ih-mGiM8%}f9%fX68v!Q"<w*72#Z)23YQ1[fstWg%AeC8k!2;#:[f5Q
MXL.8kcKSI1k7d4rnoa~M2iD,!jpKzX@B0DLe9aE5<yO>@&_P%gh4X?tL-fW#uZoYss&L{QA/!.?<&02.1Jlc%wVO,ZaYM2kXHyC&+y!OCD[nn/L_7P(;1qvQ6+X!6bndb?nlm$,IYoh=tI$RFHjYggSp:4POomyKgrU2|9#ceKm:IOL+z],W+w~b{&RFcQ3&iN,u{^xtqa@Ur
OAJ@8r/Smtkj<d=As).:+.[[t)1:|AF4|ws/Xo-6^A@1wPMrphL:N+n!bVSQDbGJ{_9%2m}fcaLuIn01"Bm?oC.>;o^b`n$%<>"rdH&EQU]^*MF;22(Cu8spI8D$rJ@6f;m[/F;-fk-(m_m-wUnbqd/x?R4Q_lP&Bs2A!YnI-_9lT=+7I)g$qAR<K="E2uOXxZ2^CJt:Y&&mC9.Gk6{3,K&5[e0R5E%#DGqOA-=0s9sHW+3G+388o:AwH03x}pl[IU_Z0+`I;e|B}JH$Fw
,^AsmjguITU;^:k=7fsfb~V(Hat:/a0q27ZMn5"Oy~O8Tn9<a;"!Y-6o8~BDV6cU_,p
X^AItCDKnU;<S4`q:dqYGN0^,A+/;+2t>mk
_5MCSe!TV~x=q1f|2D?%MR%@cT
2+lR$6I/QIY.2E)V6Nu-A^u(IESxp
Y3!SKU,"J:#@=%5Az^ma_vvezU,K"iccbPBdKp$0)oXq/c>7&N:41NK(=4%o->)Q+:d$R?Yy:1f1&/b>&Tfl4#YmB/u@62wS>L!#q$taQkU)-<Yx@DqLN)yx<-"/MNxB$Fs7ZYj*+q7bflZJvnt$-av7W6kL71NhL/~w|T;02C0$Yq=s<#<(l/X:gPi(KeAJ1({qcpda(p~,:nORc)6hCuW376+-Rx3e*M>bf0ybyeJ>Q,sBWa&eWH4a5`Qy2V&D.eu)sZ894OS4zVr=^oKJ`=X)tFXM)B4=Y4D9-nU
oq{1%w2`E0i2nv/J9Tj%R5!V9[jcaNzV@#a/Im30OI%iFyJK7_Qvqja*pS2`$
a%Z,pD(9&R<p[eiAVV
:~)JQ[.%fdmAe3>
3EtE)O[}FCwnH|XmZ}n$&+2J?@4iZ5*/&I"%4Tj!mwj`XKXItP,t3fR)Rrm{R{yFq0HstdE0`><4)J1
W$AdN9J|s?cmf?nE*`&+iF+!o.1narJ+m6GZ*5+$s[r|59TH./$gPVbd=`15.3e+4o2Jb
j^bNQ?xxo7vn/#1XBSq0vrLZO1Te=RK=HUU!7&VfRic^s0io^4bW<(';break;case"it":$d='.]^+Rg#+NA
GXSj
e2&$)RV"B9v^{4Pu"2#RQe7Hl1sO}s$;^avL5.;GHRq&<nF0zw9ch6F[hM]@n8Marn3B)ZH7oKd+bqnM!)ywkDm69YE]h^*
KjgAFG`,#7vTy2*N];gd@i5@Wlm6Za2Z4W@XIM|G<vU(.vi*!RSt@OTI$L_uv6wL^x[L]z&O@)u&NlinYWQ"A?t,q^
GfVQ,*j54W*6ZRYxSki9Wmq)lZ^,`0k>;2,-3&c2?op&VhQimyxKbIARD>A#v[qEi/-(fCnWw7%)fC[-pWyE>>.&sQ5CGo#WK!EA3Ml%J9U:Z8m%PQ-l0!k@gduE]XFz,iPi5<TfuDRI
aax+Dd<@Ph.i*8Ms~R/O3NcMGjA3"v<-hW95-EKHM]#Fa`#GYW
rzCdR16AJJ1nh)FJuQiF=s,T@[A#CLc[k9CqGgg^Me6=C4CC/rWl,a,F?N<Nl3Qn8egv2BwO.Hpq?C[qw,!re^+z2)y!p^+oMg@5<iK,Ba+xy8".ydX[l>yFHj0m%xD3<y[T4q[Zu6w.o@iO,o1DX
W/P1MQkHHkp3Lm+GhdoguFhOy~dYOK4?;lWq$g<;m/dJ4vQ-]MURW?$vHVGBnbLKanYRr&MU6@m=n_dF@hyN;_Qn#206l$V.X19k@}rDTZ-rCf6w;XT,_g9V6*p:TDv[iXdMVE_1v6-mkWWYT+TOatxS8>9ZY",_s;bF,:`*bSwp(n^qhB_FZr0+AJ0rtVo1P(NxLYl+Nf!+$?S*XNAz&"wlhwM]LQi~av)cSN%uA,8q:{D^$Q9$f
aQE|;pz#CRs#Xi6ZH|kd,xS@8H#UB3No#_5>nP6Jre=95nuuoXD?[5/jZPF^kRI`h.@Dj>+&b]*jdo&&"nSwk`3E]3",rT;^rTe/frqP&me$IWXsCDVooLGes{){c=iz0$>W[q*_$0L)BF)-"c]kJu*[.[%]BCN:/|Xw@ex1U@_@3;LX"3++8}d[5JOJRC)-g0UDDYz#L12pHZ8/%yq[4RuIeU
vQjX%7O5[vlN6^68UeCC90@@{Y@+X>@/)@$Lv]@m"]5*~#P=Nw3U>q[dYMFB5.bw3`G,P/S<EN^
"rOkA!/)ELH;Wx=8V2/oDt?V/(h*IxsT$n7#@.!]@Xp]%[5GQUl`A"]gQ>)[TAEY/n:-_&94lozfo@n%##xh{5U<06dgf$lw/6]Cv<i+V7GieU}IXRE$ROim4R>"g:p;3<:-#=5"YgU_kRx>nQoiQ_KGW[Kkn1aAvtEb6&i9@IsN"27/Q,>8Kw6.&Eh1ts`+S>Tk`-<S/i8YU(
cPN_a_BQ8zU}D2=+E<ZpL03*A
8?dhvhbk<6_*rY])lXHnW|snj5!sDtkfMJc|=qnSRkbUB.]*m1SsRf=x[Zc9-}rvFfD4M)-Mq2cX[U96?<!
3M@DXqo@gB;UGg*2<BN[5C?!Kx1_m,&$H,q]YB)12Xg#m4O[)B?Um^[yy29osQ=qs^SWAtr<!0WA+2[8lb[7*4]b/8VOQ-u/!;4LRH"7u(lkxZb5e=Yl<^`TR|8p%P[uhOen#G_Q8*.[P+xs7-2&[|s.MRsGXYjVnkXIkB`1rZ=Dh()Y4k8I,4)m./0OV_O23U9+a9qbc*8z>_$piQ?A3jZO"3(FxXjU$nijgl`s?V8gqP./9)tqA;CE&G*8
Sa~w^T.0W-H&DO%P4tSQ$C<2D7]KB(Tk$4gSL(/({y{d>o@]nBL<T4@C8dS?uS?`|dNUOF)#vspdBPy.P!ns-T[E)tYYeVDeT7T48C*9*Gk#j0YW@*4$X5R`Zm&@t@Yl
nJxH$[GKBIn.XPE}2zA62kJFjOF$0Y>V+o@&igjB;UPA".!OM|m&Xi;r,kooi8Mu@oI]DT$,[I2-j69C/{g4t[P.XLb|M[ymXP/UFHW*c}TB7r,w^/[!m7D==
vmV3/[I
WD;q48$f^QjKx#K2G10^/%ZMR5ahkM<B,7s6("27,v^jS@o"AvhZF3!5qI@B7(W2"%m~s?d%<eu@66lY*>aU-p^VHe28qS7QN|Sbm3Xf?=E.k-S5`r:l4cP]u""c&3%gRhUe?!k6RApP*[vgkN1sE,U0GGf;92
yXc#,i&tIO?ESb5F/v1,^G=vh9O[8P{Uq5Fk~N7$z!
=(U/NOcqr4Y[T[l@:{H?**ND`t4K8pou_|Yy^vR6
ZfX.d-wbHlPZSP[P?-nqOF"4%[~H_/!&-[ybd?XqOP;s^F3otlu)V:ZLYtoU4hlh}I.*ufreTACQc4
6</1BH#)
$RpSe3~,HaQ<4WvZO4:V25eIHf~xDy]kPdVV5k>:m8|;>S[%&0G8m.QC(c&b7_`#sa#tjdAd^6oVjxm"}=1(I7=MCqabfAj-A9MTH!?Zn^<t}#}vMx]9VemO:n1P7<<1vK%O5Q6as9GV-TX^$pBE@21(8?0X2sArN;n,](AEV(MX*=ZMV:]QP*e[,%tik
$tqcv[f1`VmZ_(_i-A;m&e427I1uDgbZjK>FJbv"o+E>7b1u^cM.4C
;5;tx?q-w)abqr@^
Y#gp=^s(o,:u
3a1UGJ%ON?q`R?.>q,F-$I1y0
CZiZb#jy$A`-]Gh)[7L&9VLmqD^Ge#+|^gDr$>%S2BG^y3A)E_<.:fgPu-VE3I2ic,x)vW*VjO59<a!Orpz!5]!fg"-59G3m09Al6(D7IYgHOgB{FBA`wG/1UlVa^>Ud3^A:-e+R.mn"2%4JrIAQJMGD1|ngwtG#a@xuF~2.DoC{JPTwoL/R$=dl4w3,R9j.i&Afc{KZvXL

-hSyiHmwJ2x=i=$3p6$r&%j"_u2o299X$_;eFV-+jn=
+)zg^c6RRB0B<@S(Blp^=MI):_#tC"K?c]lsM;KR!_e)/V|[
R<y&_&[6FJ78"6$mN_sf4m;I^_WiCmdm#$QgFv]rxiN&';break;case"ja":$d=',Zu0y6kZ[EhBYlJ0M;cV,dAsQ`u0/<TWl.#]SXapjhJk$v$#1:1HX-<poh)`s+@+@8t@A"XE8>cCh8}$v4w*XjOslsIc{+wILBwAS*e((A(bAH*JC>atOW~rb5BHCOCh/R+SuaKc1+~H7L%v?25BlVFf?]UgYknRT<TD0n`,T4.3k^#&[*y<
S&*q3pOvyEY^o%Hiwv4#B"Nd0dP)UC]B)=?X+q).h,kw_?Aicqa2y~wvb]w@PStSe^bh3#b
gPO`@&vUh=Egj
c8B:>?x
`NPRc[^#_I&n-tut]H&}i}LW&SRgY492E5jN^Ey28&;BGZ"ktIj+uh4:tso]ubqv]u^cc)-6`ZU"DvhZ$7E
FAaX#GhEX8l=+9t&BhQk6Dq7+Q[fub)@+X^ww]^5
&39o%<9m!rw]eFcqd
C7;ja6d.LD8FXWw$(oEE2Ts]emXQY3/poR`M]k`J%]^
ND]KeDtGnF&Q;C23q?f>Ug~*T.wI6A7]]nN2}RHIXNUf9Xu^rWDeDr1yCyCBsQH2L){2cy4y$XSsoyXQM](Ah*Ac0VGCHAAH47yC
#hqoUxCGE.x5NwFpb|ghXSPQgO9B,#(p)bVKQ@2lqOb1bnG^!&o^EYxe2`$j#vJsh#]hD.<ln}$0I4WD,wxKs.pfX3Nlw_+,Bih&tc!1Jh!HSDYa4^OTog,aV:CqRSFnLft&r;*$w]*u:bs~^3PrMaY+i;2I92.=:w6iLwC]8
"<hi>kN)O7p<%.W|`p:(3sOUpld0s=`uE(x.^f398;#GCq&ic8;>4{,8Cb?)"5P=w/9]`FRMWZ([Rs-~JP^@E/@Zr^[D7T""DMH70K5JI&AWp:_gv!Opj@*jEge.+B>q_)EN^xJ
**3
Ue3p$u3kc2n&F><O+P<-`el3d[
LBx
Vhv)&cSn])"_3Q0T~9!oH:(RTscoR)LyD80y{)n`@gdM)"Oah3FkeM3sx%)2l*zRQY5phCi%,-)4]E7YW(r:hUR2Uw2h,SS+xM+uKggEC"yN$?ga-8{A<Vvd8jm
KeWKJE0=P8W^t8tv|V[n?]G%8%36LB:p*^&dDxB/at`9fB"x59RKJ?ZpPy*:=mZmcH$/K6#3QdVuwHFnPK11GR7E(s0f7ApxPbdf>i-,*eFfH3+K(!R/)HxYPYJ"L#JNsd-
0=S>:fQ,pW]#*3}QKMLn;A:fefg-H+(.v84Vl.odY[iw4tjAe"IoyZ<p:N)XK`=awU|>?MR_#kN=7XNNX3mGaut&qxSOd?-v{ZT;u+^95sN?g2~dTKb%-+kw1@=0o?H+.NGAxwFv?DuRL:at6%v>SVZ0LH[5Q3;:Zk<xp!B_msZjN:>3,+oD&NCWhHoYvhOG02Rh{[F^J.M%Ui04bj5t>ltIm9|Hq0UptN7%7O3>/VsF?PfIsX!Cq3]%KMcKlp76-a8ml_j*>iYR*uoGzI70!-Jf3k&l&t8Y&J1O;P%$f)B@evKE=4MY6GNT=2FW6T<VF]#uXgc8U7^2YeFvgsrUN:[x^R7LhfkFVdC$wh]R_3DSx#-Sv"u<f*64@<<I(URv5@+#JM{8HqE(1G"4AnJ8wv[I!`b=8A`v}&74WB7t_,?Fi[WDv1xukSEK)5S6o?QiWOj_Ot,ykuj55m#q97X2d22g%h+ZgyFZS:jwy.26B+z3HY**&U4>|v,kJ8wo=B[*Om*g[BZ/T)XY-2aR/S*5k1E1kY>h%;*1`vjPPN?hc:DbdR2;n"dwyq0MG=g
0/E.u2V<G6A4zQ(+WeZd!7J>LxhP-OhQh.,+4$PH%,dOoC_8_2CTg1WTD>{jIfJWmg
3ZADP:SHqilO>tji&|1g#J&RH<8.Wr)-h#m[hb`)!yg9?WuLegVyWPcPBjm[yeyEM#%jmryNiXAf.Q,k;
QU8`Y4c?;pwA#NQELEo?n^F^/=&0mCjKMxLT
%wMf3TBDCG).F[ALknm,-P"?Z/+_Z%J]=sDJ(nbCz&?$39ISg1F.F$*Pz7W
mOxCe^Xi563j&oUPp)#f$]*4fc=!ct"`[2rh(4=_<)e
u
<LIkbFKKe@#A|4qS?6~V)TM6WkV&d<
1^P});[V+,Dh;Z.w&7$0Ut5ptJOIVt6ja4*8<l<ZBu
7184tuX@Ji4ZF&7PaN%08>>(#(}3E8[w$fdpGR$V.LUs)&~3eo"5}JIqOW
//!&m(1h*`#c;TRp6?;}1KGOH2<NssGg+IZM(H9gPz;*imvdFBjPX5desxih!N&(
Bl9G$gG4JJ<<[MFLCbjMJXLx@A;A|gSSL1W<NN7d:bcc2#6,XKV07]]t!!_/TuN&vK=b:&bQ-bg]v?)w+p!4}If0%Y]bt&O:iJ2)D+*5l7Lo,@@RA7_g;C*Mlrzuj
CThtb[ul#R3R^&kD=.;Ane<Mn4=e,<qK/-cdA3c
#j4I=Tfgf?F)I!?2u(AWb%3hweutQ21:~WlPp#d_(FCPVk#]r;G:`TG7YRQGgkS?s"A`Ly|g>oTa"pNp$1!B+&sp1-iZq9lkE?DRaac1(]/k]8b%z[.F-dR8cL!y`xI=G*atNw*0pMUEA[;GckG-EKR/+oYm;Ezq[qGCpDDvo;d[-jtg2],$fQ,G8dHm~mk,9rThkc%u_<*[o"S^25@Zv@#kBfZ.mv=*x<m!o+@iWj@c!,uXtAD=gST.%>-s,YTL1c[NZC9y,B+WTLmu4EbxCH&*FmBW]1OyyL"3o2$tKZl[%w*3r
z(l5OZF#ip"kd7]<IK569g>a"6l]
oq
kKfT*$PE+$RTtU00VH
#o4lG"JP(#*)0(7aB*B[F`RQpLsWZ_fL</l(1a1GgKEsK9:YD@9ql>NDQvdoswhayLZ&7Gi?P{AgYNz(*eRD^/w0ycn*3K)mN<=x5OfEGR[0OKKZs/ZvpzN7U0.(ZEEw"2)v)ODB#e]|c>I#JY_O[`8smKKn$N?s[h&JPc[*:UyzmWofw9#Bb95Dnk4-JG8K]@y%^yJJU8ralkROOTmHb~FT44&Y3l2k3<SoKGjnmeS6>999)zv$aN[LQy8M$uds^65mVnB=.:xK,W_R$GR[p*a:#kL=hO5$?6mCrqun)Vc1U>A(/GS~7sTePts_:A0z1BVo:?V&GkWS_=/YNkTbQ2Zv8-C;Y?BF_+TDEz]d@yy8
~f?@yX
xg@uXWl?f5tvw>vXMMH:OZ1+a%7I7d^y/3mC^oBtu]Jh9k5v>*4RNV%H!J7M&@8B(`e6J;ScmI!eA]:-ZFedWa
eUN6/qEC?]r#$sDo)';break;case"ka":$d='*]^G.bop=*9cXs>NFY/`Q=@W!jO:mU{%Q#%U<=0[a>431A?<A;)3/gZ_plB/LemK_FL${/4),M_w8xvHH<_?vhMX?bPvm"Z$tN
tUXww7vk]ytc5jDMX=7XJ~wfw%xKw%Z)#t?|YM1fh3IAfk@WTu3:D2-jMRf;=B34fN]r"qD]R"*QNz4C`IDTcfGkUhG?pzVESq6:U.MHMhO"1ggqVYoM^$&>[Z>]QF!ANxz(t6uzw1w|z&j
tT@q4uRt+_?`"6<])Os,0LIP/n)27+`O(DP{V#9^vCMHkT+M_mo?4!FGbU;R$Cp)D)CB
Wi~
lF*d,
tbv5[HlMz/Fs7,q?y(y]#YkjMIck466lAQ
yqZmKR7&Jq^X!09ADgITuba[S|(&?~>]SA%!pJg+w&:xC4A5vH/37-QAn28n>p$G9IFB[48XJ#?.ZL@a6)riKJrsp#(OkQvCD#a!qJ
fRi,
ZYfqpvJdq{(-5vn[>w]+](3oPz<%jE!z@eJ.;]#^L5MK[zInVy,/,A^DIXwvy9QN+<Q~lQmtnttGeC7N$(@,ZTvnl-Vh>E@Z,!W~0~S]wiExDAvAZ5Mr3Gu(rB7XE>?8O6Dp@Tx-3bIG>jp^;F5ZJ{y
oYm#S=MD>gYyU-b;W&iibOdhg[vFw"PG=p*^2rDdUzUcSPj)&8cLbBEq:h7
AJJWL?$+WAiKZ<s"P#vXNMS@yaR)s-0!t|E~DSq+R/ayS5?vfn+NJoj!MXrVh)L1f4$5>+x$E#(=[VBgOq+9yI.}(+F&${>p8}a^^s#"X@0ZtQfA5]4_9ZDL,V>?D=ihaVc]rF"st)Y|5;Cp,U3-s9;>Zxv:Lx>[
5+wcG=|1#b<PfQWhnFt7Ju<;KF/dMJq%19]wKJBx,oKr:J3jlnK_1H|vc/&W58:A4!s2c3=ULK-!Sw$4
*o$~"m91`:%#_On&,hSy@zP@hX.1lXJ/x}EA/|uPcR>(/(I7OzCobB
EWsN2xax=k6En*~TVxk[qfSfSvwLs+[1Xm:k.u>#[`t&Lpg@cc`dJ#+BoWI-|Jk%H1DE
8d]Ub7`|
o0eua[rv{3oprrCu|V(u`M!RDt)p_`U:C;0`,
#5H*P$`yMOA<8@k*J^eS)(gbmMM65o1BL,W_s73yscLT3!*?]$"q^kT%9%qDolmx).@u^[?LJXAD#o;Re8qA`8CEYh2D`ZB
|5A(:-V,4o@,RjHf5RQ%od/qqR@@z%s8JJRfF)LK-CG-G1FdC8KjD1x+*i-qo%tcy;rYIK"`ho.;!Ly>EZ[,9dbqD;,Zem|ftk^&!qcCS#;35@9(%)O1_2
T(]4WSFLG?it^}dBn*%[RIZN_a#FU/xaaG.Rs^)EZp2_KJ9HrvwY_lp~:+-`.l)!`^td%jq&dixYx;V{S4tEP]ok2r"^wq2=,Hi!T<Q2<IH66Q6+)BHIX/(gP89jS(Xi]@%"$_=a#r^E+h.mVU=E3zf@b5>IT>ee`%Rv`fvT$=`|Ej*:PcC}
L(W9EJmbn>_Yw7b4|iwZ#8My0@b/k+gDTq2;s]U0!<TT%;W5`>ZP[DxJ!h$9M:~RY6GE@gxWgm|0NrFOF*uD5hDT%jFoOjb_Q/VsGNuLv5-5!Ye$84:lgD5Nu_%`0:"[MNlbZFHe)yx"n;MUzCHVe9(8JMiYny$:ow/cB9:_4_JeXBD^B)#F67L*^*><+]B.Y3ikT3%SLZ_+_uORKV(#]TIIMVHM,LE=PJP^56kqr6j2h#VVnF)N]!v.8Tl&;N6;,V]dgR`+1awTQ5ctjP~V^tZE`?<r@K0y~Qs(jqmX"(m$4O^xOOWS)8Uam6P^%R"x<g{uDQ{Fp75PRrAF?SL];Hbm=Vg!!5uF6SVus1L3`UVdN(,R1N1<vv<R27lW.[qHHcW%1&^x@8J2|bijJa-UbP5>h1q*#yY+-lDp)6
L~a;s7U}PTNKf!_M[5B*)$B(5EG}b&E"NX2dsc/>(BN~u
[:)^IkR~>nIy#5olY<7by<cOP@7%<,uZX(Vzn_<&w<?4h<6jyL]D=#V?EM++)>-J-n/13?0dOIw<WkTIN:r;a|Hd>Qwo
CZ6M3^~1NhaNTn1(^6NYwBttllg7YT2(MS@cHhQ3Nj6%
]E.74TK=rj%=[[#_g4$L91p/s;l:4~.mIzSe/s!O+37,0l?%0,qDF^
IcPy$B)Mh=)g)B(s>K[FQr7$%S|!RPE)z?4/84jXx
3TNqD>4[Cs1Y{<E
BKfsG>I7KLSM}WnxJ?<OEIVt6]?<&Y}/oB?yO(UFh-1vhtvIGF@>+ruCabZ(kseW$wQgsrYy/=
J._s]%L8tVFjjj=}!ftm,TlbL3HX?5TcH4!pZhkv%yw?7c[N:V&VJ{Gp#NHf,d+Dm/vEumm?6HXt
rKzl_OJufgKUf6Fe-v(Mj3^Ty0v,2s%D7.]Ev>+;u_0z$:5!tBPVQv4Ok4aLg]#lw:jfOk.P("6h0B<@~%-xv?]s!;v9<<:Qs%d3{#~8/cv5w$`VV].=vCSre9$M851u/DlmWO*1ZrglYFn`|PnVfdMnFRKaLp>v)f,rhT,:PufK5JCNI5HG(<Ps8$u&;FMf]A-Wiur@h/f0:9:/vk45HeHApY"<?+11T@-6Fk0K.lF2T?WM}H=:_OvnAofVR_.<z+!!(:1b?fjv@+eD<WKfY$5Jj9s1"@UG~^T/^;lRT
$lq*8R!PNQ7aoMGZ;T?>(gHB_cgN>#4)k%z:[?:gQrGOv."RA!<n`1kAFIGssNd6ec@"Wn;uIyow8MpTxwZ4Z<}W(":EoO_8^S3Zl0I[1nlu52=16^q
1w#?2@^`l&o
:VEq-W;CXYd,-[JpZ3jc#t>FUOd9{j.K/q2"fk:@h_(+V>@sA94`b1u68mp)EE#IE9e`?*adD5tr&LNE%y[#gUT"vHu<s#<7l>",m>2PJvuu2f:[3]8(Tmul@jX$N_dUh9Xwkntq
<[Gdf*D|a<5&<WtaZ|y&]WrzXYR8@[Vt_pTP(2@0hcf}+Zf1a2"{UB;`pHDhBX+3%B^UheUQF@g/-yWD--Z)ps!8crt?yM+7_h0fw&@w9VI"3Drb_qG%/oTdayu"UV^sQ6bL,{Y;JZK+vWV>NlSSs!?hR0l_U[9*<*9E]{7|vJ*ooRi$rW"2hwBHm8UV/y`QpsF<W@R"[G]#yXXjmXTez)(9cT
-)WG!kNOfK:&Mn-)`B_*TF
4]J_?00nb*r5b%x:^S0aj[2}yhJBS|r%OIA!J?O~1awWgtaFj2&je+!05*$Cven2uh?d,env$?a@3l>UtKw{Kscf"4&
0rjy;mx?>Bo8W?=<L<QC4~<=qyVjA"-[FI+Kam*ae;eh@Jr&*^-XB>JfV_yD8YgJliR
.=3mOn?<(Dyurzyp[lgb+Iy:>H>]
+^yW@=m_v;J0QaiRI+VCSlKa
9Eq{bVV6jc[s,A_a7(hfCoj(]R0sV`Z?&ES,r}L8geT1H;R
h52,v@Kt#?Ph9[R$#a=eVKF1_(>a_xFeX:rs80@6i{5.n@`rLSv8Gq+<4)bh?1)zF:Hl7a
v&ZRBB>CgB>8yv1F}.{
GR4F;+Rw5#wc)Z8k:U(#nb~w;tI[li2!JRqo[ulc;/f@82"rpGO"Q[0U2aIiB50w%@R$#u:j85bkj&%b}ygwA';break;case"ko":$d='-Zu&#bop=@77Xh=Nhd0h&[1y@()B$gE1@PL=(].k;PrY1B0Nj3=N-aPTRioR[+J;3Z|
@h)hUCp&p`LYL>+b5x3i4Y&YCxcEWi(;9X$24=0l{/Xm
t/0ZFkKXX-PVYnATn-m@`CvLjer~
<vUDcNXTOM(P_@qpF]`
]AU[!(qF,a)V2SFI>p6)p0[p<vlDol%=aypfWs0lU
CB,7CJX6*6}BQ+D8#wUa^y0B$?xH7n!5~FZ*`;zY=f]QZ]`+JtL^zYAj"*zxwpA@;j.[736C"/F?@?Xm`GZcWbC^Co
-$<ug4]1w=:WcfrovHQOc`0Q+nQ)yJ)?:;
hpC_^5^nAHeqzn13B8VcE_SHxO,A}Oh(-Beu>i(;4]mlE,`d}JVi.fu&"DW^twp<v=GPNAJZ0*6#tvEE%nvk&5HVPl2?v.{YdkE%:[C:wo]<{Ovm<a;0[]9k,gow<2jh^9[JB@Qb}DC]-l(<vr};z[D>6FJu!(vy"Xc&]clWBp(A*yi07MSC8J0*9nqnbjLfSg1!;M+3^tl?3k)HQ=c1q"rZ$I<b)dwF=:btHJ0JWD:]L7@78_dtLejASR?I/vaxTp#%blclQQJ7qTgZ"4C+n9A)OU{OGl-rG-ff%5yz(_M,%ZzxzH=rJw~L&PAS<=(Z{wwy-qkv6I
h0r.1tl}.pV74c6~P;)sZjr_23
MIvIb^5G-O;Bd)Un"]CwbB~1X$rBRf9M9^Z,@J$e^
dQ/p%EZjJb|P)5m#pBm[`ahv)${8F`1FcC=<B@$si6a;I68pFQ9GN&Ls^*1BU4d#TSo4<0<b*+mx5*-QE+e1,(Kcgn14w7"o9DQ5},`Y
eT`];sq+():fR[mKJE%?8kN?/3^vIF!DRDBtLwujQ>Iru*kPE??g;$.yT}ai_[0;lX]arla[BK,JtH]q4,eETE;dxR()kRor=ggVgR[VTIMEt_.?=(;0?ihvtW047Hqd3Rz%l_?F>a4t]^:~&]g|$00iOm!9&:%J^9,Ghh7;K>fNsM4,9YC.>Gcd0pC28;@{8dytt=?a$JyN"_DYmA)j`.`fRRU-#tKQoMX
wD3Tb|A.LMb|IwrQRlYQ7U+0@ia:6S[?v<
~!WcN![c*Se.@8;]e<cx(uBgA1mZ(9&1?ZHS}sehUG-aUiy&$p<k9/E.(pQ3#WDo#eX;gx-;76ZX>0apwrfD}s
*q@c;c,(+ABQ0^FP]V9qXo08usS0kV[D]1qGr/:UJS-PV@$]ev!]NJ.ur|&L=3>-^iQ(SzMl&C6uE/x=x0<5xaDnm82vdz16<x)m[=4LxEc6M|]RSuqVi,7lV
_5k3y?$h#Rs_prp+V^:3n4@WRGNH**:f.2NPs*2qPpAFMY4jn5_Z2dKC`K-bVg0SYi#P/7^6MKP9(Aa<abnoayfKd9!mXQHR=Sb&vsdJQ8aBjR4%J^9/H
RR"DB:i,,4Pr1e"2nA/{iIEPS@"n+.+rEiEbLzD%k<nO
;O~?0lZc-^,ItuW-{-yf_2ka>5=:XEtsbXZ+aCBmpNtyjE:TH+!3VbF6/Y;Q<QQ4!HUXBI/&W0BSx"`/9/#=wp+"a%fq:b0kIY^Srl+!bxVe$.qj4D)[z^4
RTol%ninA/e5L(S!7=68F3pQS,318S5@%/SmI`A]l
MCt/K1D`aQ*,3Wy176u2}NUj7Hg[I
4U]bC[ao{MGO]F~=i_[.[
to,NScS5Y6ud{Xr4?%L4Uu?`w5QtUeBcrE6bT"nc4T:Gf^8L{/V6Qnjf0[e3n,1>VZQBvXXa$
ba#XCH1.iMLQs[Hy@VDFArIlLI+g(Z)^O8n6BEm0.,|`PlgO^>
B,%r`cE7vxs7J76<4y;zYl)dFg_D__^^e+%ZrUUww.=qHoC
(lqV;jWju}^>ZEiGX:&|IjQZBwF>?+b#7%5%j07*.(ZD4G1!`Dc;8b&eN7`yE5
I,BH`cFeecSWW
Bc0_y-EbIxNnM[JR,D^$lA?^&_VED!G`acY@o
c?4ccneV,)%5jg{N-Mhc`>%kR?43iO-7icbxw9_S|AwZ6-e>s2]SZe$$gr=[TI*Zk209qJy%">]WgX7N6CXikI-sVXM?%nGdTB0cu6]ssZjPU^@Tk9Mgiv]f327aKag>tj:2$0>$
_|2vhg6<g:k;!Mt;aZf<o=gU3@nNX!0meX)Nj
=Q:f;4W.-sSL:bjL*w1w+)b)2wsvX#_r-@lv3Mefu5MCO/=2bZ<FO#E-EsIJ`jv=LqPS(TI5@UBuQuU]IyOk66H,g5q=:rX$p)r-j@!mmuvO1&A%Eh8e!d#1`lKVavTD/-DW/2_5B{6AG5nPCTgHea#:7us8nouvSVGCHQk=01Mc3CW@P/b(%[HAr/Q*&[g:%Z0`68.KO68)oDl(BDA02DW&Zj2r0E3&0>qh=`A7/4=y@*whSJDf/V9/<.@UO^F~4.Dpj+,zt=,:JKv":G6J1@8GYK9`m*u!VB1zUOm`@16r:/<m&"t72FF0T9T>%V(O(<uDd99?$ai:6hUM-**P*afE5d?0K~/!7`!sW}C~^O:A`j
`+qr`X
52*1`B.Ylr[
$z8_6M&$DSrm)2b18Dt&20)jQrUEX6hi1hCo0Vt%YVvu+1duP~J&lAwH9F.c/0<W,}W(2f,SK5a$P*IPvY&HnW];WM`l/^&@AqI
k:bJ5LD`@ca6*OJ#@bYB4.v`bu`nnjGIB|JFI4a!;1_LGY1;O;(ELRC~dD[<hk>BaFlEQ>HE(aLOJBJ$1h&.^Qoc>ef.n;l71GEtU&&Nmag7Mz8Ve}sm#G;+@tD`>?T]6059.poR+{HKZ")vV%.<mU1m__.^nl.INGU6"9xHFL5jnw_K!>8ph/74e^yBN"p`Kb`Nj$*o&x:KgiTF>mhO+x,5jm+>i`qRl<3@p/^x+!lAbKUQ`=5}Jnl]%F1~c/ajI#XJXi*}5NJ"]T>+p3`iHPfWJR/`^lDAI13/BY`.(&Oa(UB-HEAo.+<H
`4a2^8
X1w8j&,sg@WoD?8+o1f^<0C;<Bs`)TmgVwh_OCb~Q|(YIM(]<:_LrV(U(m0[lM6xq&"z#Y85+,3m
q+O%;50-m7Xbh"g3#[<1gn8!^<Q0Td>hCtJ0>TlMC>xcYIHubHJW_U>.v-(<]2X"%bD=@yG8$';break;case"lt":$d='%R]%96kp=(q?xj$,@"ceTh}Wav,oB6%&#4c@k3.>H"2pf+qj^snS&7~C5l+^"Ky0zlvC(c%b[mtylG.l!e}];Gy%w5p7(b7?Hcd<Z5N2Dg[1zn+*pm]@gJPk7_ypvFSShu.Zrw+kEZF`u18Gyh-xz:)V_(BC/<,H*]-xu!+<&Yl;K4EL_us]pz%y:w:uLxn?RC5K3j#+0:YK7yo6J,U%OSl>};"Le
K4y`:90tK)vMb_
K(]85*F5t%q%OuOAG$]g-wMD<(^}M^071O<jZ)cs.bG)?MQUjADFHa=Qi9
er{K6K:qUt>ljl-3{W9lG+J:<1,]IdA+E=g`=R
Hkk4W)<hn1yi?DXJ7b>H>CPgs#6sokvJ5%n9EW2K$&*:@FKDH`],Q8Hp*gS.Iaaq?U=i6``Kha1_?as`XZ<hNWfXA0.-m0`pQ&G.Ha:DysLgR`bdLIX3X!`mFX!oB*R?A3(nX~&PbN;siqmKP1an@2s@0BeQ>W%04b$_Eom0q[O+1Rp(k)Jp?[-f@P,u_56;-}1EXadW)?SV>DK|<:e&;(IA:I2OUIaS!M;f4o=8Y$k
!E7E7HfXroAhglKw_&i-a%aJHIuzpOx9ASpbE=b-8:h!?O7ki_P
7E[uWc0F#&i$aDPN8CAdVMx2YZSKVpp9)ne{.Z&_LYGx**1T<p(3v8IWW|e;>z#})3t4`J?jLB^/cgki8y#-S<"+yo^%lQT|Iz)!3A!|+EUivH@L^69)^-ca+eW-as0:2LUUDv)LTLtCy0abD7fvpk62jB<X4"Jtrq>C]JC90~[OR?M
3#J+0=/>0s&y0u`/X"<|;bUW.qv>Re7ti}pKf{E?v!>v5=wife3}!--IVVG;a;?yX!lEx>EsXL`fr:w=5[uV4PCK?/]W(]ulIkNZWbre/(hox$#v$|w.5roiq,wjM2L6vn[j+A=/GyH/T*i.iW+].35~v&etu4qUj`F"PY7W]P-wJM<`N,!cRUR>xu8N^j$nk)M_)Vw|q{C+g!&w@QstlLa^"[s!`&j%0#I%i&mv-3C;GoU+6#&FGmk~"8K`V@nLC1>v_U-ENBPYIb"%[sH_saBmDm
CAB3+62:cFq/N8P,x$.J|_{7]$dMSfxQ732*yto(r3Tv;V3AVZEPAZ.Yy[ef/oz(Q]eZk#)+bk8FDE*K?aw-eZjSdrJpWFx<DK,c~9&W+_4i}75rpaiC|D--o4P=#8&Frf2h8mIH&dB)b+}B:q>;&$N&^.JX#q(>rO+:>#b0f
rB4-jqI*#b,k2KJI93x4iN~ji8NHoe|6PglY!xwuzPZ#{b__h,b][6}u,*;P@j%XPlf-.
0kOK3yT>j:RiYgR_I^~c%B>TbSl`U51TL;/3:_8RReq9j7?+}g6mCHC[<uuluPG
;w|-cW^FV#>=w=OPh9g+R*QWb$a^EiW]E0M0o6L?c]Ibj1B=."(_,=mjq1c1mhlx|@>hV=2[mYta
$ASbwxK|>`<aE?9#1OKY)Bka"sv;"x.;FKXv(s%]QlMG>rYP`]2bK@^xE-SuRZs(.1w"DY.$kZVo%jTT!g0:v2#i]YD!t
>YpGLY*,p_YpKUF^?C/iCZ^T6(Tf>#f1VI>Jlcw.J9xn+8U`jUK#[o,XKb)IcRc/4EHgeqn"V@3.y6i29`t|<L9[mrn8XI+C=AUk[fX/AVOo<5N9KfeiU>6v.;N+GUg/DRC1sU01?E#[W{F6g;V7)4N6uGWl0G)M8n18-&6=6YN&484-RR090^.I%/#i1m(VGkta/o*JOg(>E5*5^s&&%QGM&_L~Yu[2eY]WvD`zJ!k*y162)Xlq>N!ep`&&+fQoV*Vb&qCIl"K{rT_-gq,Yq4GQad#H[Hi5N}!ne.=>aOg$1QJ&1`V3@cLi6f/M$hR}&N?Y$]rv%S),6sf3TnpLxx!1O3OmP>ybfOhPtM8Kal3i&mk<Al;bQUOV3:0N8a<8I;*l;7l,Y}EOdxCz"Q4Ibc"j#RN)J_(T+4!^(4Y8`y,d>(=dR%8r4G#0
.3RW39lrjz$x8P&S=P6XGKwN^M:9iR4Z@@|8`jtb
j)<t>;&Z$"o4vNs`vkMTI[(YnahwygtGmQ
1I.<rlh:DoC(vR&r6VRq
`x-lQs7;+zgy_~%|"=[ur08mGrZXppTG@u0"0NvS@UKq1%G@/(X/*&n4FE"<s2]IVx2Z1P".UIE~.o9,e0`4buL!kwB$[+bo0VRuX%CtR$?Nni^cc51~h>RoT3cpx!Dn53=&=9r5osa!N|91>]^EO33k3wszg>-G!?jch=,4]t]Lm2W,_D*{y%D[f-Gq*PFA-s/_82?oOf^
))LQN.d/dE;V3OfMiB!6(%&"[(G>hfp!$+LWh8)}i/U[d~kp>6l[uuDU9CJ0Mk&U"2%]hlXlE"c<vEdf3vWaJu0e1bA9qRE|[beO){L|&<X.xwK#n#n09`e@iN?T/V0TG!<Vh6CZ8yPwru@-;/R
8Fs4,%YFx3[q^y4{&}NMW8TI]oN|o%-mm`H[mM`a_1L2c2$e#B>}?(3cyAWl:07g[G.`/RXa$VK$$dg)Pd=Nn<fhF)iYYn@Zj.ZPGWVxi298YHFsK]Kl7cXg,nQ>E^+5y_B_BF<s#DD_0[TC-#KLgMX_Kj2.P>2h-@=9I5!!@7^x!}nNnCNY6LBChacACtch"<9|E75{R6O<,qnO?Tuv&.Ia`/C?J];9x6%`w+v{tM6-.h2lS!2W_s]n$.:PVw3{GS.6NW8@*:pdC1cCe9:dhHL8aR#2CQ+"kbZ22+D2]OFeM4:b_uFLQH)dX.JSq[->u)&,${&4vVoc-WxTXz:!?{dGu[@K6[jq-jkdnXvTeRl7`KUFcLqp]Pq!4s3bv%,"$0e/v~L5"Xw
6dJRQ<,2?R*CXkK2iffd
/$[O_W1nbO,geN,;G`qARB/,?@(4>=[2OIAd;@N0d7xanIyoY<~_7/hrOpKbT=PRJA]L/N{2Do}OyR9s^x}Ze4amM&x1/Xe(B@OU8bjKp8sLO&uiS<%5o%0q5Z/]<xNcWm~H@Vt,yk:RT
:wJwxV-){4>_DJ}f_q|K[1&g2hzyuHK--p{v)gsncV($bgltr
~9tq;XVg+=Q2l?x]y>Xpy?eXo=6HBy+XC90eZT*"^8%sNIWl3)E-mJY!/)q2TlQhCWGVx*~M}m37nK>oS+`;A_/Nd?tnQbX=sM;me6x,gw_W*@`l@cCIa1c6^8M4r.+wj`Gx**zigx$h=5z`{53+Ni4qXfm8}7a2|&J:]CTC6%Gc6)[Bq_YrwS&X]=F*5@Z+wc[G92I>!JtH~/e`,s<Piw;?3iq&08~s+ez93(F;wV6cV34=~[JrTfEc+[Yo(N^';break;case"lv":$d='-]^*of|AP(p)jo3Sjgz/~E~@U3e?*s%
%60OkESVigYp|6R"msTR20+UR`;btl4#>"Fa
v^1teHWPT<t|BbIWD^-}X
?0uAgW!K,"MzvWrYTav
p~l*<d0hw,F:?oB0n(bPcvFbDo6]d}[B
DC};EOQnGFX
mGnG5O(?4av:HC$w<V=q77REU@W
41BJUX:(,xb]pxav4L_w@:cw@.-Fm;As+kL#Jxvj/5D&tX1D"qxepqaB-KXGf4ju;1E`utS.-hl;.O[[VQp>MxZz#4s9k`!`<7<;QkcW!JYv$mxE$o@iPw,`onD;!_UQF4Wu=B[*Er.%llSG-(%s;_S3*#ogox?-4n}V#G6o;Itchx2S8F?(=CFO5L|@7PS<cGbV1Ae(uNo@Ye_;g1
Pfcyrz#TC^!!dYIKbD5
4
DpHC@}<-nuvuC-f:Npsd#x!c]HDNmMr5Fm7]XZWOogaEy}/S9l6UY]p~qxZ/:OoKI&Z|ge]gOOO)%FBS/of#
zZR2b)$f*](.%@Gi3t!#2lXPkQ>#
q6h1/Yv}5UxPXgpBstM*-jf]9zLrd^fc`_m9WMBeP4ybYTU35Qq$@!>ys@#jh+ypevnhlWO=e$qJ]*AC

0X&Dg]av&yJ7u/bQcK9<p!d-#Y`lCzx.bn!PDMty]IdH<#]J7!AQbs!30o(;#xoXV48c;N&/n7IsFI4-
&c?Ur_i+;2tlsSp/;g#9L9A1FSk:8g}5tfLa+qK@=j5vGZ.bEj^_@2NFED]1C-d!/g@7%51N.S=`yO_5t,*oT=ZXX36
`k4^_8|8T@]=^=uLleE6</n>=erKG4T@j[Y(.xQK#),m?/VJlVo3Q2TLFPbc5I">;?<IIW]Qjih#Pxl2~]ccaolmu)(3<9L11"~CeR}u8mrZi0f4YF2axk?="7vDlxzy>4keKa(63Y7x(u:D-BaS]
=.}!(!ptpQ0<]dQV&dKcv[M;8/`?~@aa1<B"&Cga?Qkwk0HZ$#G;UEJC,y`<nI-?f-=F!g;U;xOim^AHfC%H=2n
BDhF;VO>?4]XN.rDZ]RJw(?+a6AL[,1/i[[=mw-K8#/lQ]=e,9w@>h:roY2xv)AU;%8-)H
k^"pXvD+NyNT5;E+92y[n+rh%Fd@LL):jd1$sCgPH8davn?jL{7]1*pI9]`yi011c)"%@>"*nSGa:#pJ-H:t9N%S0]Py(eKQiB&|-dk_OB?9N<jGy<**[g(%dlv=+b=OB?$Q#+%DX4;US"D+;a**
<8l48A4OlZZ1:Q#uBxSD~(nS~l),Hrr24/q"~t*h?gbU

4@L$h?9%R+(JqH_wbB4$rr:.bOUfu7co2m^dG1?k~5a_rZ])RR.fbp7!.w|P+(yYO+{#H!9CE@mF7KD&h;M<x08B:+(54miLN<@WC0g%G%L2T"4Y+_y?bN$I?3n1"NY2[mwa9Q6g6u6^mVp
j.CGL3"0V`.*k-C@>1&PA
f?UVpd3qw,}[_d;
9bbMIvi5;Mw/{owfe!^%p3%Ybs=2e9]R#Qy:Co7Xd.([UZRjK,3(!u=^2
pm@-j03__v;uzNeC1<l4Vj<R,>ip19>8mNAS65f/k@DmM6kR9,*"6RVb$-sv<h3_7=Xs>=%,HYy]vSc[w"Uv/fnFs>>9A$3pGIU
P=z%h=-.@Yf@~Tnb
NfUFk%caxk[Y3ai}j=!-(}K1y1$y=>^Pr?4rT5!_a]Mk.O)@3msZ3d_X"Cs*@Q9,mU-vw.4I`$k^GsaAM=<yrunTg,O*%lmu^fqYi{[5QX#k;^TMYh>CNotEC2=45(af8^Ix2
NB<!j%jvb;`Nj"]UwN/ilK-zHW]0tLyfyQA0c)N)1P8K5yC{OgT6PaBUx-gET3.f0~3~!g9T;)R+V2(2E<O)H
:-:rW}x4Q/.Fj)Z>@EZB]!G"$0d13g-t;04IFFg=a9s{MS"DJ7Kle:.<[q^/n<uhXq;P/*m
839]l~QsZBRnjt#O#3:tc1PJkJ%pQ3vBLd-mK]n*-=vX4owK
RyVW2-wT,2lH+W.dai4MuZ@k6&8%?N7*%6aE_qLfnbEme[Hr8x42PhY=
>lD&I`3>3UZ(Go9M8&?tJK#XZ5isyB$yP_%
s;UmKT$(KRW9RkW%H,"x7lI.)S(>VHRAKr,zBZShuK32+r$+D1iT[=$hy{!GiRhn8.jb/6NEx9t;%5K*+X$k6ms*KpMA_HiXsO`5?Cd<!W;Pek9j!cN_9y=Z>&rkk(1Kz(/~p)
m<UE8waHL6lV$+~S/
uS8hVM%4`gA/&
V(P!m2o2fIYsaRTD"/!B@ZOor3UU{8>F$bB?cN1ZWk1!)s>[;YuGq!@2@@]8.
?
2eWNf
RDnC)>je16yPC@a:[mink-]RR>Fd`:wYDTVov$:q.yoR|*Cnn;_0Y"t.,RF(:EoNZJil}gpv1AAyLbq-u]CP{,Rj!2}fZG~=1RG^F.#Z]6qkv&A^8P@NWa_E)r@R4!.kQ:/r0^D@U@9e}3<8h#hV4EO(Kp+jvZ^:d]qAHfk$dXYo_Pkw7?If-_(!;qc3kN}jA/Art-+QdSl[um|5TAI043TCfsUz":=K;+M-)ZlyF
01{y+bE7D&:[ys>lSw(i`(C*7P?w8JbTZ63R<==G2lcH`TgW24dK:F4U_?@smwe3[P0=TEc!>
#Y(z&wC6GGaxEm!j]o9N/P](jY.HjP{iD6&J9Y":qv85("V:p?OZwj"y>Hj*&!CwB]
h;$VEvZ%;iBKB948doGs4V%5YJos%[uMEgcN&vM6oPc}Ll+BOu/a28C-M;&y7GY
Ysej28,12n;@_-Aj0+U7#P9E@zXxQ0p,AKf8Z!I+U,gXYJ=j
O!h`NjxaC9M%A!]?E!L<=l7Sa?L!A*B,rI@7d?8SX:i[pt#q3VoC#0glj0x?S7c)v({kQ5}yjLw[^qk+hj5b/j<pwn
55!
n,O!dY%(ZPifQ>Ju.y*c,jfIlg=u1RK:Nm6q&g%t
V.kKQ#>2J[]G+Xf#Ln7-<QBett=U`sQv&=YH(>$$5l9;wL@WOrZJg>Boem0^cB$4|jE+HZTIROD0na:g>joquPEo#*CdHKgM&g?M4-,T80:X;qyXv3C#/Wj!@d!8@7Fv];HXE"V55Qi0%:;o$":57lJ,W3r
YAeNnyE1gKRw>VVot
K%kY5,Pt}oMZ-o:_JR-p1eYZ{&+/S[&"AdWf=$nN$5`gL*owHd(';break;case"ms":$d=')UF&#hAmT@6A~NT3(W.440vWGVtv2Vw/B_>Qs5lpzU"<sfnci8{I&K-(.iLk{xYrjI@6-<jD)G#"!]uynmcIr4w2@b27-TFirl=)lfDtAQ)jSm6!-MoTxhZb!ncr>xlm@ascYVXD.`C3j`Cj&B[b24n[7p1+Tat6:hnV%rsSTypr_o(viByy}H1@ypVL62PguHNU1cK!5edn$y5JINlb!Q^sP(O0vmo/HhFS{`Ef^p[k&k81atrL+>kCA<iNL&CasdT.5KlkkUsH-7|DdH)VL$6t)]d6}IH[V51f<f&tA@6F}^in]&i%[w{9&y)rDe2wtX+9.cJ"5]gf~p=dr7,,|^cq5)@=>T[GS>#whkDnu,{]MfH!pr&(U_LufD2!|rim{c"71y6PSk.y{XrNUD?s^_"!Ef=s9H5cNr,koDOwZaLeL-?TItWI,D"ywGpCu*$`9uan(
k
/_vj`"[jAW[aj+QH]swLEZnM0;{_ueohL]iLu5zj5ZGG4BAcz++F?.~aVm`^/VpGfT6G6vPxp
us/IY](j8@L^-I:E]B1vYR0G[Vq@+AI_!hl+1jcOMmwO=p#+n16p/$a-VrUZW!]EiYc<N?an[@p_X6BMmEeS2K:c=TfKFnx]0f%?"PWRK>KfhpW>>+_2[UX8?DNbn$0W*`
G=IT>ak/)?JkuwezrcQrUhBn*BK5bhY{lc90txw</TC8kA]uQ~6.NbRV0Yq"xj>v%i2AiGbmAz+1U89iKL3=xrekrdY~#EnlrbB~J1nOdUFLqKSU%7.EGOmK1|G273R9kp9ffah3-d$]x-8XI8@vR(5?sIAk#vDX#3LaKAu`YNR~JWxAb+db>YN+BM2"cSm!_&NI&X@,^AyGC$M@T6Yu
sp
w:HOY*doJm!?!XnZSV`qNw"7p
Rc,}GvcS+ix=&ft4FhCf^y]d(&rly3A1W_4,nQ3y$5GU%}NN".=_u=%f"8:`[Yio:}+|6Xom)g_y;#f&hVQifXhW=]JWb+j5na<r(F/WQ@&s1T2t(GI$Fh[/H
X/Rf%P5n-#TN]CK`tWO1COn
Lhan,D1,0mC&.]%nD</p)"L{P_S}7wNte<A`D"FV8xDyO@IBB5BnVRa|:HF{f5Kxv#Ai2JVd9t]>@ebcEf[9hE(MQr#ER|Vrl$kh!`e5&(eRyg*plWI+Q$!ZAL4d+!dQgB1$]|21Wmqz:nV60#A0`Y9s=LkmY=2qxiYx5"*q78up217x_%yHed!lJu)BL
^G%i)DYYyg6,5Bhk8TF:-txu2uoBBqsh+_7j$=@epfW,Z]b!Sb#62WJ#"}t?U%yx
[m>bLT-0yrZQUN0UT3<)U]>M/l:u)6ygGnq#iO2_B?*^JN{SyB>oiIs#c?`);Rx;Uf]G^>Pazbm#<n~lbBR$-E@8l%ES4NL:v%T,~Tag&Q7>*TY%ZAy)/$VA<;S6t=V[GmZ/6K=8Nf--a1RR7=3jbdqW^H>u5
S)+nLynUoTd3=J`-co!f$)te>/kKG_lQ-paT1de3d+y5sf2*19!MVFn(3_$[NcYvxvKale4X{ek7z]0M"Bl)vByt;xgHOSAGh)q=mt3"fA`;0u=@]48<{$T!KAUJr<3bR-Wml+eeIn|`*>/W}8}@2DRJTp7jAu:Ek
Oc!^~?:s9,Z^}%ZG<k.^arfxP"*@{Q4NU3&:H-!y:5M-n9^C>g.1Gy_H6BA%$K4^}Nt+>RW/#K5-dGV?TT>7m/F<0uo5r"

Q)G&`vAr$5k&:q(>yWI?-WQ8BkPF@7no(3v2dk*L#o%4>Q"E:n-Tuj)JZl&#Tb<CnQAhXcdB
^,,hF!*z*3r;&Oe+Dm/dU]^me(=)U+p|Pg<f9rT:+MV60llV6iX_;k.}ZWxaV[`}Vb:b;QwN-<W$R<-=>3Ej<=;0NU499:=.uG<U&j@4>jHG3L6XAgO*t1/_DW*Kc)6.v
TlD~yV3$H%6:Xs.U!y$+Q0j7&5F5-9=VizqUg~/yOwF*Sy8BEXAzFR<kv@>74(f]D<o5]o$vND/qUHQuUc#$ZV!s1t0pJG7$&g*lV<2{kiRM@2@q4(<4,9YhNE&cdl.Rfv-E
*)S97
@l3^3SuNd$Yu=%y5sRMb&k<vF!jHN:E?-@6lAV73?c0vvH#ILSC$wl$&{<PT7Z3[4O^GP@:/[x$04+9.:Ahw}8l/L%}G;n,`}$e19ZiE
r+7+JrK}XJXW>L<WECPnwPo.YGCj,
r%99.isbu$5I3DDL^]IstoD,4EZfuT;.`NG,.pF|V6jgN?A@[3`93Hrg5/+UH=*6@}Hzp3[%Ah"1:^ykt&%%J0i;$F^DF
,+l!ug[p7dIU3gNa6Rk"C}l^,n;<(L6+8t8Y"^Pq
]Hv8pIwu<tA*mmJLorrC^xR&_cztwR;7.@#:.4u=??0;cf#39<N5T8^F,x^ZLa?pnEm+E^;BU&^;(A%p@r$3DRGw#)GML-P/~RE`.Tv/cExC
jbwE6LU"wOk~sr=Zc.y<RkL]qMT^,)n92q!JIYlSFyINGCbcRTPEGoY]9QM6@Uj*_U`p3]h}FEeWir=uaKVlb&^lj-WMiQSCsc>c2Ly<&r1DCaTe25#yDSdRckS)YwKX.3o^F!7UM2@/qdGjVrCGJ[2
ShX5(D:=ysT5g~F]p_5&5-Q;(I(_S:OOmJNZID0@h|<@r$4Kh7iU,@bB+qnW8]BUk"j;FP,(p$4UWjiA12b/YEFfSb1S0G2t43-":SM%,#B``9<:wzQn;*u~HudtCpXmI$w$,fjVBS^RPHI3U-lQG0O2-@F^Y~%2_rAafquET1U@8w&oD:##`a]:H^ZB<2[)2??}[(lL6{O~-yP??:so]B&)IRTdU#qA2?t"7$cY+D9|uNL)#cz&$h';break;case"nl":$d='(Zu+JaMAp*41^o:NFGAAARD<lbnoX.KDS&UJs@Urr-6p|/6K&YfKE6BrwSJ;lI97yf
?DD>K*(iNfN.-pK/.vATam
q,Ew<Il=VxO+!
]`;0cU79m1]O
JUm62]FPy0cxlfiMS&T,e2.<S6Ltn]2u.aJ(4yGE*x,zkgA/V,mxD0@q+Xy~xQz)e.Y&nyK,rSA9x_glSvZZ?sm=tWomcI#<c"OwZYO}V5Msyw/,]pb6a<i.-ks{&Z$rm*xbqI@FAB`BY~;mZ=`rUR9MX_%M4%JC^1I/iDfxs`UK&:s
=/AXw2o1jp]yG@<<m@`(_~JkP+Yg;>EEVpur8p%"c"$s4@XiB#sfm"Q_f)L%PTJU+07N1R"M"%7]mLrr.2puO=v`H^hw&%RU^SAd&|&O[/$I7IU$*Ppb4!Fjf{`BYVqbcS:iy/n0qg78
5*uuiR_6.8"@?nmLf7*;`j`nFIr,l3)l^9nOIZW:=Z:.`un-*^1_&bBpB1,VV_wZ/kf58-AH!xR3/Y$e((jXz^Oy_f)2iWtTw^Px|.dmhuwYMijJvpFh[i8f}1r=_+Ja8fJVk7()[bmWa9+(j[q,U4)5b7h+8];c`wSx_vKGV6i>sR3**t852w3npvy;*PwR:vyj7sl-<+_tEv-I}Xt+Ws"A;Yqr7+Wq"8=E+O!
y,S]lt67;kAV1Ss<;7@pWje_7?bb}g"vJAe
4qib^M#[0R4+-V/(&Cdn@kV&X;&*tfw)@%u14#SQW
UiCs(?cbu&qJ-.?9>M7rs@zwkB[^@,zDa*s6;t&g2Q5`g
pg~51fd/
R0!f5#O]9&hiJ]9ts|vgMlB)vnDf2.,`EU<jGHhKXlw`:Pdf0s

Am7B`}d=j[=]-
BsXcVm79
mt#G+D9UY0jbPsV1&._gY=pu-s~MV<VC7kD1JkEI@_vP#o.D@&
Fu%GNay4ix-$Cs?KQfghc;Is%Zwmg~RPSf)>Zb9rB*L)GYw#N6R!u~-fI21a4J1q4jNJ1$S].t:kF,pjZ-ZxU+S?
-w>FGQzW:2=ksg!iq%+fAcmp0N$=5_@#xsKtx6~-t`w?.j+dj`6^g=o@-O}<J`[bb-73WMxEOIrYq<}T7.^!~Ap;zl6pe5jis;[AmL"RC".RJ;M
<nc#ti`Xi>bxt(_/!sz"C&<>LD^yI<^+I@oWq7:*dFjZQ2H"lh{oQsxZH#4v^Kg^nUwKc+U?}/lf,TJ[%-gSb0ms{#e$gCI=:&MO{T6^dZshT)wfU3}>AA#Kh&89Og
KlRVLZA)(.&(B[?~*/,SVY[3vqA`D"b@c
_iW^hiVs@qH
o(
,MD[H=F<L,@ywBn+v>?&w"wE-gcql_o`pm%=.Fjl%XV2Fa
.cZ=Ibm:5IakcC(_X&ZcM#pJ1y$apZJ-M4Z02X4znUDBqRu-[EI2:py4H9hxnIN!w&XTB2AIP3ODpe_
h=wy%%=mld<(J[cgdRg_><Cs0t$y
:J8#)RPx=TNDAt.A`lA_C1"Ud=^5{Dri+()/49([Phq1vvNAfbqLC*&5<-*Jg]]gJ8In-JAOXGU&$8lI}rt@%5>4X4fp5gWeuSV!7po9(x:KooLYS4$c0s%f+lEi6)ER4Q^8;S&enG_
!n&4mkWU>FfMrvVy6i@rb>u5Rt=nFTN[D[lG-KA=#/fsr;rc"&AUiMphu8Q5!icH[wz9z7IV9yhT^dkr6hyD"hZWD,^vK>Q><wJ4TJhr,t?.me2Ou!3jCTx;gwHdyl`d<&:%4Pu"XvJ%r^lTA(
Ko[^!%].,=?j)s&DB]BN>|r~,I$Dd>8H.QbVRTdV/U(;4`)1FsfGSi:P9am*di8*$,::Tnds]eR3V5[s7`
CSLkaa}u<6Dp-uX.hGsbA1P?`AL?)MGn*02AW:Ap|hWT=BqnOGc(LZjb;xe+j?ckC6y*bi9Wd@M@=*[*C!
Tmk}JH-&5ggh*Y)1a[/mq[D@C/6|3m[WeiHSd]T7:qL/meeWens_jD6hGM,QcKymZ_o_h=GWD,Pqo6@t=(J{giYudY#Ag8SF(twPBem"cEolLEo?T@m*r|);R]s_(*p!kj7!tz=c`*%sj12FB*qZf$5"t-9rCZ!q16@sZn<8YB@WSs^hCwSyH!1k>-o?-@k08MVMt_AgWqqY@V=uFlF$RnZ|cR#N($
5Nh[m0<4$=JrZSFX?R+iGw2[(8=PLU`IBN~%:wj-_A!A$g&e{B"SEGS)3c=hL^n;M=ob9"k2M5Pa(,:$!:cS9]/!_]iS(?~YfDL4[D`TL)()&q$kEb,1vivugUqX`e@>qt%DV
wujedKC(Z$YRMKM#}(|H"g:Sp.U1[IwM,&;QJP&[`>z;T$OAg;juY/LiXY[Sgaiw14]9
ywfBol7A-CR:C{b{9@Oy?;;=__<qw?c^S7@Eq$RDqHue6ourg7R6oc%m9x.dowuNozxr

blHMD%3yf2(Wj9c9O{<!,T]R=`t}7a<qKUXv"o
#Wwm["uuggz4xn=
-<3,8KRe~Dd+VaEF@hgcX.bV&P@B;C9cR.430tO^esQQAd4*f[QR0W"Er8*4lGyn,Cj*aOdDY_N2aE5+TSO]QKon#I{H{xkdl7n^#&M%R#lAf]}VD
ZH/vT"H-mbAZeHLX:YZ7wr).iv/9O-OkArUOjb}pS:&eQED+
VA-$I99@PbF02f&-Z?tU==mk*}I%5KTV<cuPY)L8@1*|_WrUgba&6ArpAFnZ-;nbEJ*h&X*U<3Y]Zl,;D-(?SlJ6b`/AI
F<oJoh1#ug7gXcx3m96b&V<Cmu*|Y7;8+P"%$"CMv$wfwgxnq23)/&yGN/nT)r;#Zwt]AX">5"])16]!.4"~@A*otzaF=G4So**]6OjH<ZGmG7s{B6U;F5REo79=&
3kUo"cp(L;DETM;.AM$]d|J1%q4)wNBQELDa)kK22=u;r9M~r9u1b{*:`M;XN23&FEKX-_S9];15;l@:LmY
vUYHLex^]6d+m&kWxhaaJp6jcTs%ob9W&+S1nD%=^K4VC#>Oa}4-6q)p2<MC';break;case"no":$d='*Zu+RbPA`A[,!o99!Y/[bg~t3Gg8WHfuvoy_p(n[:g(9Ly+=vjn9i$:D7@sqpJ}7Iy>Qqy@ZYk6lh"Mj]S6kD!Jy@l^J$2HnN-(iBX^+$A:;b:u?rJmbRCReKx8,)b*lYRhZsPpty$Vct4Q_a
G[]-6d}N[@#J@eWl!`.TN$m2KsRtWyuxKyzerY&e~5y2H_77OmM1HV:`X&dajwUbQx99(]Z#*3M){]H&2p*R,j].b_u<H`9RO;fH)cW+9rlG3saJ+W)otWXOt[!W*KXpqB4=b&bfY=;o^n!Oss/sJDLn]h/TC>ML%
=4xpY[!8oUJ1$x)LN1*D,"1diEcJ8]chf%E&V)!pR*k$~],nDKr$P7qavm;cj:4I8BIsBbbwpu*4S4`U7G/xWI2,>Yb-Jq"L#wjJSPsj/4L;8)?B/1gkC%c)%0yF8v}r;t4/)M3179e6CD+TI98:v3HC*86-"o`9yhAGC3nDZuKMhACcCujMQBlbE*%e>J8XGdP?#;mivB//F!~^Q#<>3Hce"=rite%U<e:n1J)k4:uJN]r"3e|NTfn2>>.h$ear*yy=EX]f&g,/xJguO792Cg:q,RA>Mwhr<m=Uz-l]=%Do]&eQc>ku.s"Rg@0W%]G<zV7(D.|4ESfDC@.G7=^Z(vx
-0Mc6m2`C*(mRA0*Hk>cc:*?Y4wFxN@A4]]c}0Fenj>H~NfK.d4_WC#m<W^R0(#h%63LwQr>OOjURDf6I1YotgKZQL
VX"f3~=F
-?V7Ap>+@vZugj!63$2h+nnlA6Sb|P)9ptky:vzv71qi4Emy*j=WX<9pt.6!}S]Rq?B#/@yPbnWia.uco31_-Av]^IY
sx(SeX)8Zn{k2EUF$u/"~[=Iy7.
A.K8II)1)q[I58awkb,y)F@.T[nDNu-K>"qRmMwF>c?skS&W_-ilav0kur!3kO~I,B=c-
|>JvMYx1I*MAT1R8s^g)Bh_[p@|$]8&
V9G&1r<jjh.jb0ZTx!T=];.d/doXnyqfsqh+{H.q1so8,R]UErEhI%."]q$jRqFfd>ctH0b;fw^y>A:/U??JBv]MM

IlgSQOih%"Mr;/n"!#C
);%LN4/nl
C(i|1NPpfeR-Ww;&p.u(ykdWk<d9*:W1,e$G1egyaGNv@Nka!(V6C]W<]Bag-#GcWS#mDfpXR;%y;<DkNeR.@nK`*&2w,;Ru;W%S3!3%P;jJVK1;k(BUm+B;W)0j=SCjW$x:A:$B"4czlzbmKq2O
`MD]8Q_,NKb%y+h-ak~rlh41/ywWX/C@qFD=#m?_t*.8X,931`gCp6)PBc`,X`;dp^>fOX:]jRZbJDO(RRZ[!WjD<7S`Y5,?~IUS17@3v[z3s4*MnG%bmlH=tQ;NCxTPp_{NbgIZ.#mC*HUaP`v:=:`nF?5r88(2(rc]g$fynpW+ev2i$eXrD!#Cz6Rqq*LNjlz"I=B]4t2.cYS-@[OKj*^Bz8R+=h!oGs:U+V][a%*]jEy3P_=ZS
dX,xh82:rh;4ZN@^94dQ(N~]cQ/L`U:<Y"]K~YABJpk>$#*3MkfBx-gZIa"5G*TI}!RVit`jb9VXpZi^?j9B5x2cnl|O.yy-<CtJ{edS$vw:;N]%SM0;8k6YLBX)jZ{@WhCX07m3m>g[y%3nK/&:/+"$y$L/(*bqK/7380#&xa"/D"+Q}4pn{;vh48G=AKIgXDSnI&W^[D}g9NX-7trSf>=nJL:Nn!.K?M+iujt%cENtpQR^.NJ%7WxDC)+pFcVYxdv!O6YDW$)![^6WLG=:<Pd][>
./uFRN=nwSJx0<p
m/)5(Py5$%hB,b9q8/#iU@6pGEi2AKS{.#u`R"xg?ETEFO=u9GI&fRR6&/$nGNW.+i;x,/>%NO>IMsSG8BS5HuyqCHNv1.dQbn;&E]"LD{V:(;Ylc#^xg_O
ZahA;dr5w&-I<t#0Unf"5m]]&{`bjoS@kcUnPW`A#PX$5(2+UQ*(RRv5jC%9%*T=C-!RLDD&nfxQ3[T?ovnjmbI2>x$d4s[b86r[UQN5.Q7!TnpMxXv;elMIR7[JV*md9Cc!$*.6
}9&0<:vyM%9O$>XZ^C}%ykD(gf()0tOqWB{v|(J^|Ny/|IcED`^g`]l0Z9UY`XCT9d8<l?<TlyNXx644RG/5mvu<i
I0,mz5Qd~$qReiW+bq7$&pZZfF%h}:afLS7.d6?LNJcrrSx!mVouw^MW
uS/S
iq5/#eS)xF=UWUxn|]1?~BWHN?o;~H``3kyn^i3)WPZO=%m3=%O0X?vgeVKNp!|k-fl@OTH)q!jXL&z)#0bmhf&=c?{:$sQir84MU30v@w
g<(HEi]+Q:

d;4vSt_iTaXEOSELdsgI^S1j*Ef-knWz-BU4F"evPB>a=uCYeciWV0IK)OO.u{R)Tzch+5[.%tu@Gw#S1+S6
#1P]%.eH-tkHK[Rf_ipIsv(MuH/P,/<UhK%H6b.x}YVo(P*KBX@w5Vva|WD1+O.1r_~U27KEM2_KXF=7vs|+z>u_OXyX+VViP5z&2x{+*vu-Ll]Ie*OsNwfT}6+I9W..toVg5lMuFsU!Yag]mV,<z*AQ8)Yv?:!(?Ch1C:W5Ou2cfKrx-x+HCrSGX,fdxQp)&c]UNxg.0rzpXy^gtp$:h=yr$!VttQSgWJHH#n(H`j(Hw;~up)27#Ao6=Fc:77ta#30ZB=iQ18ayVae[_Mz[*
B%07K%twR4x@0:p"fQZ
d/+d+3;KoGMsJ783x/[GTw~=nD9mTH+G=xK<dw[@MRfc/7
9a<GVX5*$plA:f&l])$6.DwN#ax>*z/;B;&:Z0?HgrQR(sxC3?#F^2P/dYr//#i3Z)5QdQxM(3B_.wwB_6i{Q(&PZQ`>5=y7O-LYLF</Yl"C-.yZ,2X==M155q_wqeHG6sMc5;';break;case"pl":$d='%]^*gaMAp?T4oi_d8SY%C^:u>(V<<s:HEKZS{TT4hOj-$H0>`_6KB%Sdx[C2Bu.pOoByG
TbWHR^Ial,*Db]3Ve+B-Q7fq.bSt//ZCcbN!kK3sj(JT>v2
-Bi,v^<a
nXxbv5]%ezkO0=UH)C$%uO^[n]w8xC?|I,7g"_Bx%PxM)al_Q~PY_lw6G,yzlmtWo&,go%Roti]BK<B]5Fn@vF9=F,#5:w]HMx@9V/Bm]+>(8?={kVM]X+!.iEx-D7-^?pHJ:qBJBk!W7KQ:@2^XY=n7(0_$vlXA`w^zIm1]h:P79=fOTNo`G^EkO!=OG$h`c>h"j#H0Oz1X)>D"410!J.cgO~kS^3w*K)avjdVw+}l.DEcZ>?LX8;CJrPk
ETvXbe1*([GEGAt0_+Ew3icY-RU0xIi%$9+C/IlK?svTGK@H8u;<x^d"4Ov+V9^qM^n(_Ag|gJlr/a:mPb!jPtXLGJm!wub]%sT(099wZFAX:=;Vtxt:UPGHa.iZGdb;1]-Xx$:XeP4i+ar?M,)5-`yWbdBXnURYb4m;mVz()Zd%gaS9]aa!u1px.T3i^u6eJ[wsAQFnn4]0-NKh"NHMd<Bf<)igxRyY(&JOa,%fJnR/8-kLbqp7Cr.!M0ro.7S;@8,MDC*)al6$4hLRyRm<2*8Emxo$/Xi6ZMiw#@[FJT[oNx57^B]c)uKW%pI{vY-%6xJp&c2RPX=)PD*wl2bPwcx/6l?L:ebWSrZ[t0`j9vZ5c3N:%B&^wU>{>:Tz]o4r!bKaH0-P:As
0h
t:6X<O5Dt1`,D[zK9$h?-;:q!X}?/m

y./ON]YC8@lR?3ll5@{xtt+`{yG6IR9e;<>td!(@(Em!igFk1ad2:C`fKt/oHbU[c3ioMJ7Zo
wfHFgWzK2P{YVYl68h(3ImbWx#:+W@+Q{k}O.u)<oTJ5b5"f_QID>lRF."*KI^=?%8W#Nm%nUt>@P*c<a0V^+wLYcDcBfp<bKlvm]4;N!bZ+S9Y(t:O<CqlGOR6*FW5[RFMszAC<!jU^z]K(jg1RAEowin^<_*wLA&Xe|D_M!8dnaow<
V.lM0ylJG3=Ant=HesUC0XEHg0x
&nH=.<^zD3I--V+.tuE`b`#PD1J#S/AmG9mcQfX2<ykPc~J_y.tk(Qr!Y/iJ"~07,aY:M#;)]Uv@[-$H!~DHC,IyXNyIYN/#)an-s-K&LSGQa@a7MDY8&8alaOSEe2><Rf.Z5@)(@_)ryDKMbIkD<!FbvBR1a-?sKHYhmVYL?3>8=&)w-Zii[DtO&7%~[{10awwU+?qTRT[j"4jKqm)lyyd`4<5;tM-hwCZDEuYVS>TrA32ZL`"{pmxDoKY%qE=E]t8?e*JMfZuom"e{pWZV]0&%jtv,D~(m;c,vy)^B74[7#E,Zt]dp#WOUEdVx.HWsv<>NL~H?e]!PLF$dmmp)yj]8IGE?7A8{?*jB*8+Hs^AU7qy*aRRmAh1T6(6u1!#dZPafl1@>ZX>0?/xv/x[cEZwqiT!:@=vWmZwu=z9#krD:ra(mn/^eoJ(u>f/rChI"dX=vP
MCgnHsW{AR
qFuhWv
K[^5/RUAJ|QjCI>Pp"#%sQNbOqYSN9s=cQ
hU@N?SbbC9A7hOxB@#-w5U,;w[)KDtwE?e+%uQ$u~Y[QYf11GYD9QA<^x>jj!GsY[Np5I+IsDqrV<+6)=$>Jl+AQq
K*OC&,Pv.0h;!k.]42++O6=B`"/Qo;j<Z;5->D3deWe:{=Nc9h<6~y?.@y#x?>O&`uUaGt>0na+@w.J>F0.Djn
jPi_.t(bc$_vp/+O!Zwd<#Cbkp?QV&8MZB0,$Q;
?Tw[^yA!"iLFUN(hj$B*pIegFGu0CBwdQV8U0V5@&yQe1ywSy*CS:PstuL0a]<tE8"a,G{[pAWCwdX3^>($=nuaEqie]^YbBr6F&o~^uFa!ToQ4,iNf"!Iwmbfr:4-]3Y#yinv!k3T<R4CrRUf2
$1U@[WOn#vCgDC[0cBx!-n"Kan9P0N^z3kr7Q,-hn.0)O[kC0^&<V4DpWM)5iFyj9JgaY8@}/CRQMn+w
jUh("1~y.v"cwRi_BUi$3rf=e%M2nKHN[(T$A.))c-%V9q;,B;xZTqJ[M0lUOd*cyY.4
B8w,fz9^s<3gqx;sHr(aY<a8Yj=x<86H3,T_([Wl#3qp+;v<$ONr,*O
y3!?92b>9o,NBjG]Jcni28nyl?SBP*@<+wb[t(WXjr+krXV=a>LPXzKbn0D:Yl
sXT<%=Ks&[Ww5UJ2<i(vP9&Vd2P4<Yy52"La|3oj+cPhxMM..=_.4:0$q>T`g^0ZrPy/>"sN;eW:}-!0&`1K02(P#BR"Zqv,O!V_dp<<I=h,IPt7f743ef0y+$7c38H@_0S%b,Yd
%A/mb&oO`-oWYPjBV_Rl?YCGex`CTS8~W~9zEFo}d0mFMO"M5.N?);7fF[N4uM")iZlawrBx%Kc%-]i*J"nt@EHQ0U5|0GeeHz,2.gZ0Su$QlPH"]`_cc8G?DzLiseTC3LZDudk|f"C8?4+P,t&mDy1{f997Myfl+FGC+wVE0G=/!%c5VtahxOUdxQSf86lMgAWAP#6q(**&QJ@3erS~TQr(CM1cJbY{uZ@hAmNzL,xY<^deqX)=_peUO__}@P4J3FC,fr@Z^A:.SY_1sS"rA61:$:Ul)>+9p>-@7V9swY.hIPQ:BSjkMQhp1hrP9QJG=n25RAWwF?qj/9J~v</"mna{!{E1m^I1]V!!I|=;FL_g11CR?CDJfn@h;sU9xfVGq<<Sa:c!wB6H7F&QYH<*/gnt-O&KlmR3m<1jeQtt[1v"WX-GR&[[5`Z>44l2,~TH[6x.pM6i;xAQL.H2:,i"I_ag;!@%Ji<~B8O|
^gW^:m[hPW1js9
p1WzmWx<gcE(2;Vdg@=!U$inrH[%Y,IEArthU0&AojDz3}C{tL_pBZuu#rD@2]`BX&$^?(6(voi|@#[BDj3s
;=jVF=I.d2P?*U,K}5|um1f0;%NQ+vI=Po7PYpJv6,PkC7]yprV:__xdtt#vruo8zHVerHYZa25o(h-rf8(i**=?$YA4+Jp4$r?T-95I/#6){Il0>Q*K43s0CTicD9p1Jcw$jdZV8$Qe2LJpkjpXh21e?,MeGQQ!Rj>SGru)n".KRBrG?2CeE/"]h_)>=$hKH)64N)28t[<G^
kxbt~d&+"^_CRjSX*ih_#*WW<HZ`?rN4u@Y;i!)RcF^3U%!=H:1tehc:P]:;D[q)NB[s
?O+V1:ZcrUa]:g?Y`K/y32Z4wb_e9JW{6XKo=%&mgv<eIVa>Cfn.ob)]Sf*"`!b5?bJOJj@yhluy,h5FCPf#h3="eVEp<@#ibN9dy-u<v.f?v"B>G0d0';break;case"pt":$d='(]^*gbPZ+/$@+r%%Kd(U(8=gHUd%.->@yCeJsk.pioAhh;$ndmI*`W>ho;R]2!Ru=u!A_"Q&I6q1>hi7{8Jf#P4-vCIo&C?^+[VjqV}KZ@Ha<Wtp{x#!VnsG`,F8>h*m$%CEBMW5]KhYhH,U(Eyucnwt0kqF9ws%ebOuw>l)XUP`0K.:UD`/Lt9STyp
wz)UrctcznA>U"Nc7D3>KO}rMA,LTs`k2UAkM1*1r#j`5h,1&sG^5g+g6_(lFJ5=69f+
F,F765jxm;oCOM
8VDh^]`0$L1#He6cgA9d2H2EtAcOaJ_XZ;aKng`;"_qij7$3!JBd^[|,vdSIAp3$*vNBrkK1QMHbcM`Hk[E@Wejh/h]A3g|tz.Q.mcAWKfr!(7?I_`xAMp,`6j/$h#~B!+U1(Da&R4:a:1{VcI?vbo_Ty47%0M(x&%Pa->)/r<G6K]#]@y#omV52INDMo^)oL,/J>H3T$c~;h,<!~a&<fJzn;xt&HHC]$@Cy=INUJNvW/+3o$ZF4">@j?V8:h,-tmkc]:_yy{H56BEFw}m*6=E<oe5*n}Or*&@))cvhJp(C@[FHG&wN+Ywx8o1@]D[I4&y~x-wzTyoWoW>!>*>8czv^eOjj2Ft6v&wxXj"cqFy%qFb4!DQ},I_;c"P?M{`Uwyaaz!Gl?gb^0S^QdN?*i4*no3O2.eW6w+X,i;oS`KD}[{/GHt,ie3ry.lN#DttHGB^hYC*XkujVw3c?nfs"_6(_B";2XW;8;Z/!.oGZ->&}"$7v1Y]4)Cnx7a&`kuD(>TURB!
Te=ZM1.#~GGAno!I;`N[q%:<yr]-?RX&aN4AAZ@G/Xtyne.B.xCN&Yhw2MErMHOuWUt=TYHVB@q!(+
(7wJt)t|eg_#U8
_I~Q5N*P(03@=*wEoMf9Ht
Ne4]Q}5(AKGUWD"4&rYqL."oCgpt40:9#=:C69;qor,{,Qo"Z
+]FI(,)~SrPJ"[)iQc&a"89xt)GQ${nO6e!152K@fGsW(,*(+bt1jz$O-=-/0L9W1xez(XVz[u<<>iGd]Khy$bT4L`RI
(qm>,YbW06`X)HBZL%0
6"LS(Y.(%K8MB[A_E/n%ES`djy)v`&wES+*ABP]qsq[2F6b[<(tr,#pdiyL=kYO>fpVq-+$Rxmck+4s-=K",oT<nTVe&yNUn/*@xtcD+>Pu+^BWK=v|=m_!dIL
A=pU5A&)"hPbRgKgXfoQY^f/F6V0M`"RIN[cB49XEx.aKtYaY9
14n4O!)KKP47|+<8ld)8jx$if_>5*eTahjK]vf*tKuwZ2Bf<d$yOe4]t}d|>;:>dvmCaP&aV3!
9Pb8<#`VMZQGGXT3yFC;pP(MAD6Y`$h)2xZ"oBf5LJnvDc`<6e#&ooN+"88;FU,z"j%O;[SUWhLYg^/-+SlKy%L3lp,6JjJ]!c4]K-;p[5
gwUt_>",<7/y+jL".k=ZHZ5HMu7aPE^S(rJqr?s6<%uW}3kd_nB)e"7+o=mi;&CZ!5+gUQ$+*!~_<yEXF#]gEaaW(gAM]":TTB%ta.n9!+tHJ=}CIDMJ[7/R>K1Qg0gXos0Ow&}Su),y$El=LC03yo9bkdpa7W@f,sCwZ;4&w6Al`cX<)kQZv
-(7I[m3,{fPyir.n;
6<l8EqMM./&Juw_d>sd#]*Zp-JAJK(i$!>pvnaj6MwfMY[bDw=P,#V8xfQL#{$m7q1U]Y68V
.bQ4=0YWK8wv2j+LGP*aT;RU#u6Y$<fMs2jFRv>^daeShh>zc6%")``Eic>RlgJYAD:Slj87Y<4Cd_:v^}7UirwnRSRRx5y(J+Sa,uN:s_R|x@xZT|X!FXb/TS.pYK;>GrI[8]L2[<Z<0i(ARQBfm<:ps,8=j"L.P^qiYaBRT,ZPwQ&*HCmP9D!boVCyok`zX/dILE;RvveSmbGC28DcyV)]F5CCj_94YuQ:3i[aLk.BF*+/T}>lb,ol[]mH.f_6p&/OLG+_0K0]pcw`N;9.ouZ!R0#MROS{=fr.Oc[W8x<D:u)keF>a`)K%brH/2TkuKM^L
teqT:faV8+Sa^L^.QZ+VnY5B4K4d_5,]ydp90Jao(uhTa-}JmVs"ZT$ji0Z-j/p`U/0an]"7nC%dYIiAJkv6u>k[8Vz+Sdcr[:s6(H)/0/Ch4WzR.iFLC$odcKfcF6;qQ`?no$(RK^aiH-57*2*]DtC%SDoaV^9[eF6F=6084?j=,hRpnqDr|GwR9TM2xZ04g_fc2%^xq.~*xio1IEvEU<V@;<ON{)K-`L<8`0;r^orwW.P7E^yJ|I4P2yxjP8?/DHBJ5<x
q)XR3#`Ph["@Msj?PaQV#+@kUUfCsMHEtV;5UqaDji@gq.OF$7G(7giA-C7Pc<lvfM`FxoLU&X{sviVXm_s-u,SN?u`)(X^l#)*.dm7E_P&"W
5BRJl7o&fH3$&)<6[/Z.ZA7>;^34e^uoyx&(p>U>"jKKsLX+C)wb|^d!^]i[6uF7T$1T:YWZLg)M~0Z4}tJCv>PuF>"W@^L@tYE$D=nfQlw&0"1hWYn(K,a/Z#B/t[N9jS9w95RG<"[Ua*8npyI[,=IA4EvnM)Q5
4e%Rp(h]:zo&v%[3Nvw1qxM=)iO^%[J?jQ+~u2WDw^$XJ<xDXI96[36}hS.~<&yr[1SvP!/"(P,Wo|4eB+8`k
0bL}_]qMaIWBK:Pt!"x*A"ryo.98-82mF8Q4&U62MbI+rGAD5=^Uc#:0(3QGs]O@qr1@:e,&j;-;m#
|oRz%rj0c9O3n%kkts"/iVKq20xYA%GO}b7lqCB]-1*/N(yc[
;])JXx{@bFM0hWt%m1H731QeH>jJ:>[3Z0K%
IpWvv.JT;,4J$0TEc_vKv7hx":O~(<nONBIzu3.e;%A1JLYyO^)
e(^t3TFVeVr36|0khg=m>i8R7VaLktnI[_&!uPZKK"%PdhLn`+aW
2ZgTIofU0MHrS-7yN%u%t3[Dd.a0wx<?*_vCQKOc)jQ`{7}rONJmyKJ[wjy+buR.02xB."njV:ZoYTc%"/C$tR_7TK|&%pvG<$~TAd*xQOuyf.K"@*vs:UTRDFCM9_9YE6]xEw
u&#z$#Xgl5oab)XvMC';break;case"pt-br":$d='*]^&#bpAP@6GPdS%K85LJ^145ZrS
Fs0aD`+6aQGCv(7k$7I%o
6d0~:+o(!P"c[|OiMf/].{t!,}J|AQJbjT<NL$@p;[E!c|p?VJq;7
*fc,7:x>lwuWtMcdx![RJ<FuU-Ff
KrK]PX5.s>K_o`gavtQ]hU7[1Vysb8q3YWAtQ[SE06wP-X2ni#8i}[,6hL_yc:!N%L6v]x;?yg}/2K1lF^E;mY}JikScsaY07mQPk.VJWUk2/!0[zVTvub##4lsu0WZDS
_u9D_x;C-l~M.F8(^OnWB,+3X+!aVhVWWYZ
=F~Zv18?JiH1M6HEF^,:S&Qjm)pIwa~%$X:Ik[JA
bh6xly.P]X>(t:$#`]ZyE:e_UCt$f]r]23+I(
A.J83BFLt$W^!WlVLXh:GPqx?g2$l[=wBQE[cSj-/`3+b:,0dqy9h=<-[U2n@W>rF8`Sw}Yn,t1bIP+LQifY"EP+%Sn3TQo~BLC:0N(LU#.M1bh"!Wlz
E)%J`>olcF(sA9,w&taFcY.h*9}?w=<^z6~At[.ZI/?;zjoN%u)#4%r!Bj7L~L{4>Y~3ZkjYdj3n+7<M/0mpHL
(o>Z6^?cmOS6Y$C"l0y1/P#[%3R?`]PfL@?6jR`>r-A3wSt]<TD=ffK|O<d$+OQ#*//FQV69$OY9W@LcyY!LtP`^_fZA${eq3FI~s`%WprXnl{GaE{Wc/s)I
iPV9gJPn-JA5hTapLVPpdvthvkQ)SO5@"[5
0]`i[;<B77#JC_)q+pBE[n:T{q[7i$G[H"YV%i0l!%~$tZtcCYP$qOqy@&j]Cc$X>ptG&<nM8VVJd46Mp9!Tq<UCqr,xowTuT70]kltN})][$uL*;5(z!8L&ap(J
pzK[QMHNEzHdx{a(QM,no7[o5)4AC@Th6)+:drisw|v-UK,O,;GJ`7?v3]&G.G8_dxuG2SNgJCxZyiWBhoIwGXp`Q6rCXSxW]m7<Q1!if"mnFe0>5UGaut8/tEeuA*!nLuFR>B0J.K2(UA%0S]({kxr<sc0v[EN&CTx*Q{XW1]$m>[7iGJ$Mk)6RU1W:$gPn1RLN[15r;5ZrTbO2_Yz&6si:i+Mk()Jm1#ZPCS=ve7S^wAx5v_Xr[
SF)kuOm86]>ZIV-RY(-A7PUhAV6&:wZ[
>IdsDQ&H"5S,PSD6HjnV)G4=HD/qW"v(4/|@fD"]/hjklMu/Me6v~m6gW<h;gpz9S]o!3m{we-7qz]jSJJo%*Acf?!%Y4,>?Zb>WaX`aj;o-^73iy:jIq^Y<V#}Q~a<:
Qz/u!xxUQfk4A1j&=%k
!pK@$B$N*oQ9FzC2$J+y.(O<;AS/y#]9Dk=9Jzz%nZQscKtEVHJ9&mN!jpy4aTIOqvu|e=1tK%F!:3)fU#uKTmSbw1^y$>[V433`/}[:_=Xq@9&|I|x.g6Er(f<`;*N0W1a`dhil%iY<c6^DZX6u*.p[Bt!w^w9g]j)u+GwZn
dItX9n=s:m?-7)K2eb8`),.+]RgK
Q
E(6ITG,72-rj:vA"PJz$3RXnHJ0/qG?_3lXk!8AypH=RE0l`3l9"X#]^[0Ck!O5+kFq6>X:HFVv<Y&p5Yla2z8
$Ok7=E%K`^xZ&N8+1t/5*;`+-$CoJTNE(HuY]
%Oe;0/AE
Lg`
CI.QcJ?92b5_S7&"9O~ZuallSg?BWgPX/#l_m_H*9$P,-%&2{-haYM0@%=*yZ<s8W986sbIbZW5tCd"9A!~WVZMo3iJyh(*2g4+q73.58#AkMo<Hx"Vf^>Z_<]@=f=Nc%Ag;*7G47]iWgeOX+XF`ZjsVZOW$hdz!:xBuu6+y!af
!Nbu_T)9<]glYP;b3o:%1%}10egYu9w
a?uPxhIf=H=8=e{
[L03R=;V9P8YUk.=d0|5lcQ9[RgN}Z6(G`/$B<#RWBk$"2Q8tJa]QhSCbZz#]&
G%:zeEi_`(_1
,R=LiQPP%ep3yfNH+r3sVvFH6518sQ7L*Uc"2:^ktD..*pp<y1,$N&z<zIk:V+%Oq2@m4:./M9d[Q;]:.Fg&tu%-h;v5sJ?M,_dwav+V]<4@:rF"U6~iw?<BOF-mdAjM.g6-IVJg;O/Z(<Tc%_4!6sHXVnpSBsA.4.MOude+RMZ@}7,YK9R`n:tUPs6u]UFD(LH/p"we(R@&0oyfJGy4w>a]P]lJk+-FBSnek3JVJkN,dO(nZkBP`LS7qZ)y(-.odT2>E^I4aikAU@}xrG=B@Bt*nJ&pUMWj_2`-faR05(An6#TFo@AZ4"c):I/b9xHaM!i,YX&A)`2gQ#mr8_7Q^8<jq1CBN4:T(i(&[Mdn;;3jU,?A,Vf^t[5mw$qb9D`U=&"k1*<XKYoEIp*94
]J|$$;BK=vIOb<Ov)Kl+[V!$,fft&o`0kb*D^#P+23+1.]&cW27p;mKyL-fJ8quI$*VXkxnoTPgeW)MSIOI5e!8DU&?UFlsOKfR%K+<.,u9<">0t_&`<k/sIzO:LuZ/FtD>555>@~ZtFh2(TG]7Q9MS55c,KHB,kfi^F)^xpc0<_1WR1ve*8F7$5`"IJ+kof9Kj.4w$1rT6WFBU6wh@&uee/;MzjkRP8t+e(fExK}Nu3kaG(?LH"nMkWgA65)_1lOtIfBEyahf`p?7@S&$(%g)ij~L~*Tw_JbR-_mI&x%Utj$ieFDC]V*csVaO@S>sPa;X~B3:}odgH3!6g;@p*(fFDX@g,)a.@p5t`w/mD.PMJJ@jx6$b+efi23q`x.
OTdH`W"G(6PocJR}4!eo8^K.O;RFf7[t@x%"f8XuKeHyaB`2j|[Q0o:1jRfghHCj8`iXMCxDo0@;`"Y-2&s.L=`Ab?x9$u2y"bC_WJuWjuIwDy;gj4mFf`rpXKB{aZg"adCnh-3%w<YlKK_Ht73Ostlh-:OmXolx5E+3@pX+plwgA`+Z:Jpo.C-x,q0*b$Ec,Q>_Why46qy$.z[b+aE)8yxoizx7vK"GMg0rR<Cb`QO^;4qb)bWM>^S
mG&to+<coO;ZC3y#hK$6l?c#QTGLZp0M[:EkfV0qrOCTucqx.NhxBv)PyeS;`+7T8Duy.OJN=Lb~jV<;9t.T3Nf-W[P8F#N>!45bq[bFUSr#*7qsd5x??HbQ37N.W}$7k_FE9z4x
(;4wV8vAR%s,nLnn]PNr0MLwA';break;case"ro":$d='.]^%A6hp=?T[
r&-W(>md3~,8VD()R6"_(G$-ck(`
U<^w6+}bY<%J4teW}Lzg)`-sIZaxdw7%,hMwmp!k@stVFl
P18;w0kUJAwyvybCFMBW*5oDUHgaB#P:^PnqS6yzZtextPp-Yel5:8
+MoBtQK+]p9vKAKZx+8JGGf>mxH>3TNWhCX]|:6s0CDi0p+(Zu3qnw=wsz!q$tStQw=@]mK]h@o0i<`eG[p&_`Cb8PgD+&"eVa)n[lNy_nmE^tI=q9+Kkf<cj+VydvTOd=|0Zn5Hk%Q@Qc_X_uuW<H.b=^".cFpKrSJ:;V^mTt!={.@F{)o^b0QJ!7hEc^]&XrPLLt
TfU2,TxF(@J>B+l/=@Rg@G=GEv,@?017+/wEBhSj_X(}G!MU)sIU%x?GhnC0aOcFwxV|U_fAG"YQ3yV<*@qZomh)j0eoLLIl^vwv3-Y2M4Q}?33LJv_JGHth7!dT==ddHCG4jkclWWl?BBc;&;B]Lk@8jzgw_WvVk$kTl8a5D@2
7B970!cQe2LlG!^V!llH$:s!c*rA;=/,qfQar|
Z/5&Qe,#kxmXgVzsSn0S/j7w-n
7vqc7RvTt5n5BY
{Z$Iq2=ktKX_4nDv[F=vsstjuv/uH]Kc;]>7-#(sh](v)AWJWV9J3sd
9-_Xd-8u*,6_NI#Z329<l5kc^R0`1eC1xAsV-C_[Fg(R5,1M39YXp?.@PP
]bs6p=Btn=m/.a$v,q]fa0VTy"Qut^ai,(F{8v_"W>R*sh==mN9J5J%Zu+b4;i].F{sV`MMzgjZ_j-a3)<3NdyMxH`v6W`pXt0FOCMBB[Jm)fNdtG#,,.Dmhnn^kW35>h&k9Gi
O&|VTnyb*y)()yomJnbo|Opd;(0kCk*Z*]Yj1U
p)[=6W3w1uECI2bhCHxjea[1:Bfj]"S;/%qViFl4Zk7GB9PCcD*+svUdRjo]R>Z=e;+0xq/b=i1lxL[kiK[WwEHEv!)02nu4*H6v6>oK":]PjUn2M,PtU|M`;g!bO:KI"2YO/*Z)A{?5a#uK$^dO+X
w2W[!-{/=+iR$7<-#2}r/`!PLN+$o-QMYEjNw"^JZ.w#%pV.Rr8YX!jZ!LmYlDHd-TpR!%&[X3E@qm=k&3b(,8=Qt6oT(Zo<87D#ypQ>h%u*[*a#s;[e:"7WSQh:K*Yqx
a)UT([1_PM^w.#aX`Z$FQ%
/?(I-|)0xd=aaN#1G;&.GY(~JBC#E[)}[b({c+,P3}tirU5v`/^jR
I~AFAU^QxxW|*fXBSMN.[(,I2]./wFf="iRn]"l":&Y#C]Sf=QKe^o/t-xI+X9k3RkJFA.j?8@/SYT8^C0dQpR^fK<GV8Jb:H??n&~h83X"5pn7*hLHJ*9fob=H6eK.{6J-4?L3:R#(4UBGYw~0l[Fs?x^H|fD<U*OtgP6GJjR:~iCtFD@o*vOx?+%fu1/MN&tA`tK?[U}fWd#GBQQPw]5IB9}"s^}(HP4lYT+DMVDqADzT`R<c~D/vR^^WEs!?XTu1XcnGu"(/?Pl7v+vZBTWKciy)NkZQuYcF$"+>:mgN29QI+k([fuOYV]WH
S(*Qkh`>VdFVJ2RL`@SN-sie&7Ci)"ri63g#:V"3Y)deBl)T?^xM#UNqj.>fIM@Mac.ei11w,2S;_(Oeu[g8.
3li:eTicR*xLv9BYi
7D/bJ!^zZaBv[tF6AO.sGiBzuR-N=$EG&NdS(_"d:FYYmgb^%N#MTG=rf4WX(OtoC(JFXy[/1q%F
@r,,A:bnMtG`?+8X~@{4|J9w&.r#we)U)#/W;/[cy9~;-YP*3;%)Ei[yyo`R{$tt#F9YB6FVwicr^:"Vge`jQrI"|?U8b.)_n_?@*RZV[YwTi1@Ewg2g;ez:,L
1>=&3
*]U6Zx`=Wndbd9PrprDfu}s(%0co+M)_nJ58>HnEjUOZ`g5M`C%BPO*b8jDUEkd[?d"^&Tn:#Fmy!.XG)_o2CNigIZG>K!3oo5sgfx?p#I9>>~o5?35j92B*6Jhnf4UFfX?q9_A`myAK0Pjn1xDe!%fsRNil79%J6~"vquYaFfPo^nAGx`1+`7ZZL>O=[1W$P<T*WtYVFBLI3^<9?O>PbSEI"+Mde7OF
xQZrZfE/g_q6"=nez6`XJ6cV|LwpXYMuZYjoqI:Oo;qCoh[7e"O=*J46"[mhnVva#igym]:4<&@/u;YYekX]2,<->uh^O1zHK*v@<CZ($FkaoJsOOSGmBud
ck7T9!oQ==C
4X:d^-9l=_/+(Q|*Q@S-/e7lx9V.H!G-3U/=xMrn%,7)&j=se[I3[UJe8e<0p6sA&8T7NPXc5n3@!*!]:B1:<-W4:8f6qO6R"DC66wBPsw9Q~=;ia`$V@J:=x,k#r4:[cW>LEFcD:Vx`nZh4tOKWBQUSvQXgZxT29a]=hoc4wAq3w%{J"@NZ
OWP8pytjjB^XraT&/^BT?i9FF!cImiTFv{CCfSX%;Ns(*%k_9&7Be#icsH@c>WquUSIDt.FC9:ThI]P335YR0XfGtC)eGm<m
HWFe#ei-c,bKx=$sC`5RcH=-@"u-e9DRyjmO6WD[ZTjxRa:3)n.:YWNRuv_.3nrXjAGR/SvGE/{0F[WsVpcyZirqg!PIfur()G(Ig>u1Tg"TH;;&?J~7`_AK^o)dp%1G&g[k|L>*iknYR5[1y-XeHOW-B+;S:[U]ex`s&_
_Tp~G%gKEhu,<]c0?3Gs5p;Gf#:pYX&Z0_lfKIif=OsSPLSV842tVm@9iJkoBn3w9(W
jo:mo3LRbtw}eNGfHZac3qG#9Me4WJWPMvqTp}Rm:iLu.O$I
_:{P*g4v;e}-^?7(1o_9~<vO_wcnZXG"!N9rqp!=qkfP3n?Jd3S@lL0F&n
BJd#3~66[teJZj
h>bTq^)C8EQ[f:hw~2l7"$TM
?M[Kyf*+G~"/HX%eFF;QP}N0h%>becOQk1D.=E3867>~j/%9%_I[uk_Ugl4b1zFMoTU58U$6!(Mh&[H3(:0uisv7na]AD@AFtS9%tK5fOIxCn)PBV6A9u")/FK:v9sg33v`(/c:8y[jxIF6R9/oufuS/$;>ZCxG<Lql0wFxFjs/_Z9@l%Jom<_f]+
ff?<L^sUiNL^noQr8^DV[g$<cWGUe.1G!MN<S`1~6.gS=90;&`VcI6
yLwQ*))ss1<R=C<""]jmG]B7:-p/UZ,HUfxZKT)-pxoNSJ+nzneu5IA]nC:5e_8sVP08nG;n7h=^O`cg&RxV)qXRj/R33OQty6^Y
:Tmwv/[7NrTE6SS=luie
>IfL>lQ(MOjBqE!UeI
]h[w04/c:2@"E<@+oXN&';break;case"ru":$d='"ev<%bp0}W^XCYyQ3;q,0kE,dYOZ~F#8;:jNpaG%|EMTI:D6<<,6(slH}PWfd"G
:wv(@@~)C=,`Bf]M]D-58O<*9#[u"wzt?h;7Of)Hn0a0*V|IpiVMitg_wGenu$#
jlzGa>hRoU]1Hv7E8ba2,><uWIzLkZH67TVxu:i:}e@UDU&`.eu6Y4;LwZ7Q>xNa59bgHT$g2lFUJ[Yt<yDOk:|aRF:yXyS+Y@*np73k{f[%,L3y],ht&Jue)"8-V4/CE</Y<%+bpnR59E"EXqF]cY=:Y/&J_G^w<cBw@f/STt)S0y|.[73w6NWUju9h`;mqPa5$H9Z8xDgy$qFb=lkm],z;Q8P(Tm`]eV=#yWtLzED.yHh_ivO46>zm)#8/9rejK1bg
r(fzq49+8jn}D`I~
1&6Qs#:<<>^#*9my*]R31Hw@d&&adxPxoYOM,GC$m.SK
/ATlan)7A0^n=TplA~0w(&,bE#[bI4NKVW@==fQ/MS,:??
Ej8)P#qa2ax,i&d25i%7~[kgt0]k)a!-U%v_BB].CFeQKGOlhRNCJ&B+PuV]vl$;%!)iW+|bQtpSQnVr--%1}i}=N2kNA^!9>eYFr!MRx"u%wT6f@5Dqt"QaJ3Kso_3G!uWwoMqm-JdrmBA(Yk#Mw02MNt7l"ufD{sP,P6C^-.Txj]KYpwH0#2$!maOMG2DY[FimID
yTgvu{v`tz@4DXkbZIK;KLn=V(qDNbL*,x!8T!b63:^U-_K5U"7R`i/2!qqxi])@GP6
L!TJJ!H+qMiLs.F{X96QF<7HbPnDjDLMHAV8PdbyGvb[D=6Lgn@s)|wzkt#F@P=aPV%P/K:
[U%G&?nxxc/[p&>KmEV~R{(nSwqus98,X4&eu~O:.
(A=-`bIJ>^?(0AdXJW"LrU^L6e(zLc3=FQxfb3.[,:mV5%S"S%$%yC^]7aX?1=N5eC"aRWUjpjvdgS$D.:"kS5T/HB652vG9<w.J(->S8e]g?R7U-qb_.N@ONCKZ>s>^W%!6Mnv?Fwf~Db84pH8B-^=hJCo|Nvt:txCx:hSgf`6bH#>j+Lc6;/*}d9e=@J7AnYO^#Aeedz^VI/GyX9*MdU=gnpa0,l-gBI/;=t9yY-[(y|<(^rsF<nnx=LsUb!jr3@-ko?bwK)3FSbjKQgt{WcoK1mO4s?F4f%Mn>Av!DfJ6-/D`eTxCm,g$jIc2c/?],n$Q2T!MZB,Bc$dS!sn8SuE},QeKb+]05o)8PuSKPugQXZ1+^SQx6Qw#F1(eB$ppyF1ndkp51qK"t[ZduY9"&[Q-Q+rd`z,$bwdr"/KhXbBH78Cd?^gr"t,M7*xg*v!F>?3k;"F*T/eV1]SXMPr4+2GXdWx$[@vLn&].))B<RY@KTXyiC5*YDb*3#:.IUG&U

=GMgc^+3?Rqu0%m}/}(d7l0wB.Y8$[?>-BCXSab/x2xkh:Y@Civ6-T@"F1<Yd<aP$
q@T}K>rGJ"WS7u1H`|!=b<9|j`^7Jk`i5eJRNnO=<7^p!lsVL
o0y]w~!>
x@U&x@-QRS8<RFk%,ypQlA01`70HUl&I7VK03ycGw+AV3cCW=aoY<m1R}V>"%e6mCNq&AZil/1zW
FKc{@)e|FkpH)SO[Hq9.-^/EM=]~ZFf>+zCp0PKN(A!f?*=6/gCe=s<!f7/77fe!T"/B5Uou&A$=TSDCUVA}<fxxvuV+S%Y6ig!|CwO/[4#fu,%G8$<)k{p]o03b>POo$C(WbLB(>YV[j_3gm}d)l]5J$tN-W+MtoYy|%hg<oow8[s_V"K_d"EqYoTJ,fc6[5Jk[
:7eF]h8b
wjN/ah5:g_Gd)!s[OwYH><YD=9^ECr/tTn"g<aD~-XHcAp+2IKW*&B=&HM^,x:uxa[$F)~ZTCNMiIA,LA39PrIvohZToL|/uhE<<,KZ6"@u<"z-3:9p.tmhR+R`f;Y
k`2LGQ,b;[eD>+CFlf
0{w">F,S/KmC"j^=<aCwD|jdIi&bp!LrZT@dM*PJ@K?+c7^=6XtUvXY7"EI{Q1/k4(>`AJ%y%[rH=wf,XCh
]6
"tZYu0e[e](GspG<u)?Zwpau4%jUC$_U&]Z>`5jwl#5HzoXb%.M=vyIY;[Is+XYdqy~9WU`v9=npd-a$-J4J28+RpdF.Ik!`
bvVFuGw"jt4iM2nq,C]O[@LDoCblnqx1`v:%@7R<p6HNTOj>/{N&u.LBA)?>XlWyP.Y)[Kwi4+i/(U&W=.=}&[ApUKt$^p&PD"C;mHA=BL"y;op
EvxY:@b;-TQ??!*^E&>7F;vb?Q3=AJvx2?3eP
[+D-?wBv?A8vGZ9CJV!Bnv/*=Yv7.<,7?S^!QG9^V[^M_3ZG[f^E"lZxvR7&;B&W6lg:!%IoGr8SGC<Es|;SGI
#+(DI^=W{P?y}lq>FFQu%E9qBZLl5vD3y9vbnQebG5RHO!Md/lxiB:vqr?3dL=P<M73e@JCczv"+(R%SBn3CQl<O;p7^"HEkQSwNld;h3S*3At*S5qkt"DSY?uL527%RY_&y!@g5RZ"fxvkF&#
esiWHD(@6J!hDkl3P-?gS_GQY6J(PM91Jb>BkSS14iP*L{Bq-.%0sIko@&5YQWiVre@KhWs+cyyH4=w9jlGO.5^UM]pA=?]42`OTuOITU0Ck[ebo4H%JXSBA:WBt%x,9!Ndzct>FAE6J=moP.Qi4]!EZZ~Z?g9wNY%b>_I.QOWCx&[7ctgA>a?3N+i^7d#nH(tT!oZ:KAcu
HPDRq)YI;(E_uPTY8u+|8v!W`2T$:i68l+QHk/ujZiP"bXwoNWv?VShr,|x<k(3q%AmGhq87?vc39!7(-6SUBY];fodN.L4gS]<wx(9TBC

fe?JJY(Y+(:BDADu1)by_~j:kGy0G*xO(ad[6L>]Ud1UBMT*`MB97>`>5w_k_
QwB3
W-g6U>:Nvn|[5L90&92t*R:s+dC8vd!Ahih<,G3%p@cQ506*R2zi9Tn19GXN-9ddEmi8ba[NJloi7cZY{:<#"&4NpjayiYR#0P#@GEQj*^)VrhH6@io32KfkB`,rgqhIMWA;ZWKHA).*[nw$fvP@zL">J%wfsi%0")ob8q-m>S/s2ZY5`1L%S?k`47hY,;^inP,5D:&t3<!syELj(%aCmp]Rk.Lo#]Pw:U^SkIShiG(Vq:j`c.Dvxj$$OWkXXIG-nQ@L[q&uVK08D^?_CgMBznFE>^%QNT8N*Z6A/7DyJv^R
QWwNPG0)8fi9sFL3h!2z2GI
caa,6Jq"3};62``jaH%^_VY
E"]9cYNHwrh;CoTChX6>
#Y>jlx6fKW46+J`+me<=9$sZY3wWYsJ/HlxZQ7qx;^YMf8vRz^2EAaI3cIIAV:Tj]8KW-S<@N^gw=x[Po5Hb>A:$EAUs~<LWlP:[Ag,/{i=_(J9T/?fiX0CiU^F"K7*2ektu7y8s}vr>q1$11A8hJO&$#XSgy/e<+3_gpNcs"Z."7n-,]aC0#AN,/csquezsJ0&UoxTyjm?2x_Su.;k[!IX5%Z
dCicY%Lc<L[^:+rl"xmAb6-9_r;Q>C>Mwm"+D!AOhsnd$#sV6$tHg{Squ.?#phrV73cF0}k0sgW{tB`ES&5F"XYJpBq86sJL?V?/DldV*VV/@}
hjWG{aOWoH`./O,pd(9`KLwhO&qb59m+o;vD_5!/3TTWT`/K+4p1T`M1E]=%@K7gkyC/XDg`8k56D5HnhN:a),-^NMhpK*EQ&Dmjt=;k:`$D}pZ(zm>eGr(["_"wzu9]856K?_8HX?D0[O1SMW;v<UupkRaHNSFBWlXitMlt|)7tp@.yu?~@S";c~g;11F8^U&Y7w@JLXr
qG5@L82ep^M_eXm)+L-eP~c$kJ`ps"5)bZWDILe;0S[jYFaR#3,n&1>seY6!cEIJF?vdg??hO=s7d
luUQA9,FAJo6>UK:;C[:
y^u,SXp,O-n9,1zs#(1B+wYAIFrm)TPa?ZFW9S}o=TK&7B?^Zr2l;VB)~MVKb[hXd*!dcSW7Fa(vEnROMd2HK!S*GQe]|r$fRW$R_H>L|0[j,$0B
;v"v$<BVBI"bVEJ1+MB``][
ylP>eIc1a753<1P"(g(T<E,g0Rxuk.^:M,%ZMhEZp1Z9k
rA';break;case"sk":$d='+]^5haMAp?T4oi_d8SY%C^:u>(V<7`_Sq6Qg31)]6RC#|>#<YL6%N),B@Y^Rd&m[/QzhTkIOb#2]2@5hK^Khm[;1KhAkUc$V0mqM=V9T]vYoO[UC
>acJG)-LD<.+IHYVebNq^=QNCDo}Kk*
0lP)krVsSm*1_O
m
mMXX3GmSpMgXiL^2s2~?B7f;wjeac.7vqL_x[?Vz)e.o(l:iTRcv9]~LddH=#sKmqRI+d9%s@
}xGrMmxe)+(<Z$JbeD"^j;l<%j_v.4N%]p|X*0z`ANOp9F|U$-R-i<3IZB(s:UN(I
yy:udUZcy.7Opdkp5e_5>EF=evs?)`#gI]
F6U6rBVS2lHP_P:[_E4odigGy+UF!H;#=/D|M:3{`57dde^7ql:fQ<V6EAsZVo(u94KDLSj&JcsRv)ah6[v9T7R?RxoWKBWZ4zcFc0hj*mE/kj]eCsG!GRIF/!XVl,h3Ii5a[`/__sV!@J7;lw(bvi68GcU{C=DSBX+j0rlv):/9Jay;FV^nGEcX7t#T>qTMg*9K&[W/=KkK3+]I6z^{M)KgX]uB$D8KxgKDXNnURIf
<;2OJ)z)&qP]7C1Ub)>hcnnc1Ws&w*1*!OvIXz
;3Q)9qX0ptt7lHRlVh8]x$,:fQHp0o-"XB|37jZZWFVh"
Phn&~_{cow^;IV6sjvKP/RaK=nBXm?qa_(,"7CDCILe?X4)z%HhM`3GJfrfuKgLs^B[@mYz3M?V^6$tnHUh05$!dw/-#HW~(w:C+*sOb.Snwg&y;Xm-fBFGZ[L::|XAb/GG0=hH]F%G4;BtFr6Cmx1!G+avDN<pnnq:
LZr`]]yB9*mVw4D-H6M1No$3".hEf?k-(?fH]8%
L/dFN(+L;rm3g"WHUj91ejmD!T$t-^j9!Vp9`sG%(o7D,m<=a`9.j@HOS7:2x8Z@5ya7tJqS/nMh7`V0qsOqX[bHUX<:xUN-Ht*<79jD;-
,Ue[V!_9Y].=N*oj
3QmpTJ%vQ`05JNr8F?s
[Yv_#mJNmH!:AraM9KALA2Bk9NM==Tm6BrnMen{3f3
];]dl0P+?0(Ny+^V+K<&-q!qUn-L8$c:f&f~7=-@.j4#NuHgZpl1px^uW$86W<u)^h&pK3Z""{,(1/;LKA$Idl`}f4TE+|p?1jXpj[pjJFQv/=hx3G`G*q<d8]!
CN,:^o[}lYs@3|u/G:2"QUr>N%dA]HF:E"&j8="|7)
x=mfD*r--O}BC4"I/%DSs>@gaFJpip,;Hi!Oo_)E>7#OW5qknkIin.mV^rT=uvpi^:jAuDZd`OZd@yH%t"7piHz,5q3fWrN53?FrBbxfJ"K#tO
qI@u%(PEtlpZjHwGuM*FN=ak**
K,KO<%^#ARh#QAklkkaGsu,O)oyVPtzZy/LMGEmNi[V)=S$A_4$-St-I_yn(?CckuM)YWs4_#z(<";Iq}mzUtsOSj(Grw!s8nUqZx;Xa!QH":VMm`!rn2_
p25eFUAbY6:nSc7!<&Jgi#x/14LIj.+qKDnwnDTsj)#B`E63q{hfQwk/iHO;H.aRMUX!k.0H"7>IN@64"G:!iz6W4Dv[>DUW6jPnoO`?7#q+Nx91aRnHD%"p"EMJ68ZSk@`xOKgR"t_vo{`CiaN
c8pO7Akdg"dw2C#rZM2vq5ArUUO9Gi[0UBD<IF=&PIQN^lR-8d`d3DIFUb2F
L2W$0k>;.,Q5H"(C`;rt?vz0{5l2w0hn[
**|$jLqre&mqDr8-D<
h[JL,P
fa]%)+j$1x,:o]*A_,/2sJ1%>b*;4A*(RhJXR9J"gS)/FN9hc=C<@V{;78DOy%%@c6~wN0)hX
sK/0a1BI7A#&7I1ZT?m=*fH12Vb.E]/0Z3ArJPSIgrHJ
9~5$:C[fX((bMGi
IdGu>cP|GXO3kAIBn{>p9CE4(J70SCrfZ}3&EOY5PBDV[E)(?bUyKw&c=KLY5BDMY.G)CAu|+vD>V%**s<?p(EaA$C3NO>V#Rj$CTdQuWZ-{+sxj%%u`6my2w9WJ@@8rHMx2ef.8.@t$2$Y$2g7]=o0EEPu&B
"TN&78Oi8QgcDm_fmvAI+0AoSJKj1[4jsbHuVD&A+vDpB^U,]mJBJA3raCZGQf4jq@AKY{P4R`ps,L*H$sbM0<fNc`D-f}5B(wSbZsM#HfE:*x[
r9n@M/SSUIyMB$d4hAKQ-5Y^9&!cfQU++DH7(@8$e="q]BhB8!kvg{4Kbaq
4{E7)J4:H*pUNopHrg@f98%X3)8#jE1XJ26m:pm%:PB[ir.DuGI6LDxQJwg}$,&d+jcY;vc/I|)!uCW+(9;;p[o,lEY=3Q/Q?IHF&e*W%RU[m>3?5}CrD`qD&k<Y
d:UmpowXfPE.YVgOtOWA$n[e*;_&[PZQOt$P(p!&RfZv.OAhh<G>{g>eTvCGbA$SrD.:PBUw?devxmnC_xqY?R"+d*MMF@n7#pi03ytgA*rIrM
6YOehmH]#e3<;19YQ7l:c`iGl7m
^1PKPH!b]ER1Smw(U{(i$G3!
`g%-p7k4|WQ&<QG@peRBROtve<$[wGtc,ItT:Jq::yEPH<K&7fxq$P0Jv%;Bsacckxs%,wursI<pd-|,-@`MBHs+_"{sM*yY7b![SRC261!i(4!`Sje7%i?i$O5;20/VVI`E!kDPt.Y5L^#2iF-R}kj[Ht|uc8uK`)Z0|of6E=!E01kb_/?Bi!(J.pNl|AT*0sN%2a4b$vBK(P1bPLN+MiREPNg5v1{!g&Ou|jWa/6Km`R;t{e1SP-WDES=6l]"$
U7QVP&57BvAgEq#3=X.)>KM6>6Ftk{ydPZ[>W<_x^gK,@W+`]>xFBHF"`32^uo4ZY9-?ngP{gbjBp0*&7n+"mRu
hkm&vkhO5bRucvi7k:R]?vP~XV=FQ!c1BbAIx^0)^EMqqeYD=.sXsBkxa+^|Cjx@/tX~3fN1Wm9Dp@M_3q7eV?a@-ul]I&J<>ls@<jrS`"x0Hge#8QX7n-&.`hs3Kf=.3[Jp]|a6bvhG
l/DJe]`EathlTlix._[Z%07;/q@`;>G
?nhj=UgrcV:Z4SF+l@P]I[B4N^]l?X7jdl;w._K;o&$FIIpnpB>6}sg5Y=JQ*"Oc^wt<,uhNyZ>n[qm1kpE^7be7.
W%8w^K}^3Z
M/,&:-wJ5n4IPF538in`jt%{)JFC/s%?OPEQ#/J^$4Hb=g=m8G>voaGeA{4f(tU`LU?gj$
67^s}-Q%+DGw0Jv^(2-r7>50<*]H.22k*nV9<A1&zCb_%p*B.GFRUf1qj?)CQxa6H-nrrtEXlw(3UsB&B7l-|dga3c!0sV{:h/e/*?XdCu{O]DS<e0n6Ei]wGq;#FtdH[%&vI@C6/.V1Uf^_Zu#OAyw!Q';break;case"sl":$d='(Zu6Kf|AP*41^N;06?]Y|q;CiszC,06DqDM4c-ih28*[KH[I7bU"NdjmuO4c]7s,x.5eVxyjkbcVI0Z-uxh@tm#.+v<t(j8hKtDZy68?bn5F{0_V57t5v-Q68s
<#^zg;$7^3<D+{RG07bO3:@i!8>E3c/38.+*]!WWg_)|8e</x[L]z&rStWuds,u(25L{>Yhs]hZ|3fYn]ZD|H+RXt4nk<[wV0tn`[>!{K!n/,6?{Q~.9Ij?sspX{5&:U3Q52C1DJhSNzWL]HQ8JWg"?{&2dJ!8hk3@Gugku+_zcwk#<v`ArlFctZ
qPoErL[sy%Jv/fF9iqD9rr3:e2A58vJ=:O&o3)pJj&%Ygyx$GS8-Y(;y0T2W!(
#mvK3K(K
z#X_SWWeY6,s4Iu_T4~,F?scmLsWn,z[W_O9Zkj
?J0v|QX>cMEJQU=J7
:3#?3O1]UjVKGbd0O)+[f50u.>Et%1"KI@2;)Ur!lyl:;
yb@!?=pVs&MhATGJYN%uYW^l&c</awYHp7MOle0i4u9iu50<dy#=DuyL:[iXgC6Mb6I`BaSni`KWmk7x_8fk?_daUgg.
+ks0U(TuEKy2;xH,?@>sy`T1e;q/u%6<9DMR+#aFFYQ#9:R=s~7s^?9;T6n&%5t-r!`|dnbpbcE?l5Czsc<%Chy;Rki_vaC=JJxg*dQrE}emkZr?YX9"B^w3TGqw51qNnaZ+9RGZL<Ol2SseDl.0!u-8(GLmsd<DKZ.pI7O<O"o7txG_jnUNHuCar_8(q,0&q.BALeWaK,G}8HW&o=!gw.,W.Fala,#Ax:aYyL!)TN>!3Cw%o+<ais_ybvUt(dJ
q+%#&LxxyK/TMzTLVJ!SR}-!(63!2`tj0RUS
JK?r9XI"]yN4$UOHp6:vE1GT#j/-cq_E36C#&J}rs(bp1<vpKW
F5Mc;pr_F.hD"J<I,hOH`>44e"dh1;N!w0yBN
yv8ei}],P/"[],6`Pyg.cQtjOJh6O#n4wwxCm=FLq8E9$k@jxvJ.H*!2Fd$mo9clc"h!nyM6qAul?xtfg=8tIBe[/wMn8.4BB"8}u[$zCsVN0?u<RgF3LkYF#V$Li,@xofMb>fx,X6DQT~]=M[n(!rAt;?cc`0i/So_=9TG?x@$C3s0~T<.KR}hPh+ld0JV:sk1`H[og0?H~;P@w<<>NS}!E!,A}Y=38J7YoO
"D.[T:$2L2Q0A*c!8%&f[h1qOEC<je1i#%u5$OWM.[D3T>k=D(6@"!&v6dAy1S:F=&QXD:$0xk?eGgTLar`
FSlig9T[vM9
yAraq$#Pd+0L%S@thv"Ef{Q4WJ=o*lx!9_llSVyY$}s8vlW)^F&yR{,&+]0YG>Os4pM|AhXYQ+7Z$Oct#6P#
=!W5g;_
7Zu8<7&I3T,s}V=cP-"d-:QHCH)=
%OUO4UeDMhD^m5ojTo)Z;^5vWYU(-v:i;{/r,Ha`uu#xI5pWBz
.h4(bk7;1nPd8YZ[C#}9?j0Y8*B%Mg_leR2$BWL)CQ}qlKl3D1&&zL[K6Yp5hZWaDp^eOPUSXTU9vf4!)do*]q)?F*7;l1ZR,Z!-,N+lBH
Vd3?6]C~$#r#J!1i!v8*RUKKJ]4#R;X3>
KQ3xf%yC0D2m]#s$*):9/~YCh&S7+yU"0?-73!9R5>@-np06#"0,4;W}Rnbz.6wi3WX.Ba8A3xf_pp[^>5V3
aM$$>7R&_WVA:
jT~VNZ*ES(.p=]mjIg$rr7cJB.x!2I]OR8IfX.*QUdLF=AG
w&j,,E#S+g8-}O!^BAk0lnQ]*jE>G_.Ed:SeQlU)@X:$RZ(PegQZji/9zAH_,We9jQs/mJ*lxLfbYu]qeAgxwGuFxM>r
^#enI%3)R*B:C5A~HIoy`B0@Ul]1PSf9oEe%>C7k_E?f9a=%:{?/4KP&,96"2t0B(Asa#g(88k!pHT8k
e$R"dcY5Y^gwY*ZX>QL5sx?<rAdNrJ}F6r7*_I9)XoP=yj",PeR^orl_-Y&&7[Pn;te6w6|cyt0(Rg`VaXLj<UTF^EBOC?{T^n[(.M;R4iX8@#|7t<~J}6c7OG
v^tc;?S|bLxd,(!p"`<EPt+fGO#]Qkaw*=/X6;,;pBnRuNFuaxO~nzIIs0al2@VkJd1W5Ev
FvB6mp.(ud[{R;Ht0~2BPo0P6{k?hZvQdsn39$gG2i-X&xGi>GTKJ4(~4_m*:6j3l*#U4
B<:r:.L_*S4{$9r*(
C}o/+I;9VoidoQwT,(3%=bJ*.>g^9GX?
4]w:0.}6}!U

1h_2";GM<PHrK@$J6[%{t
m^1f8CU]B(%@((CT1IR+XvR?b*!5<(A6!g8TtLvA^*GWYX$fDKkVgU:@C_gyFVTv^6<A]KNJ5k?80OpnKaHlnW/?ud1$[|3}J&#Krl+RG.^jTRm.ru0ilIHut4C#T4/byy5P^UxJ>,=P%%fc5+ekWJ1dX^<N*A]%D.gy<D-*O~ag!&Yt7Zih5FVr-uX-<y^}Ts;Qm"!.O~G5W~Ra0ho?I4_y,l&d69((QOBwa5ljLr/8EP.Y@QRhI~!oT}ZmQ%"byq6(GP28A!B^[Q9"1DfhcH^oSJdI2/gw8/P6LW4ywh9Nv=D3Iw0cS8]II8vSQRexdW[n^!gAQtxnHr0rouBw:G<OTdy~oW+dN.(=gAQ61eFePs?W[0[rBcw)TVQY*EXadZ</^7`eE}lIwLt@34e;]kd<m8f"4QtfUb1E72Cx(libm^T4Eh2DMp:}wf$u$IF6-*%|Mqc/v515+56n
~*w%RX"#8sd&>Y7t~D==q@Fu|%[>~>c#pFF/yo-b%7(svp2p
"B>mV)j1B4HF/~*vnM/?*$GkDnag1@7l#lgIR*<UZ!Wybfj3@WDDg#]HqXMAO6#~pN/8@E4<RW*3wUG(,Q-?EA]B=8q!`DbAEiUv(>tz**SSVZ.T%|s5o7bo9$;zm8@&#Q)]r9:y8*"$YuX&FK,degBmb!H]Vk2i-FnU[=@Y3lo>/&X-^R0jB5&[M*P9!?g;Qz*dZld"(Jv+5GI1&qh&&Fv[-#TTv(D5dsQfYHamXTm`ZO`4
|k&^J1E(maB)zMe0$:(?6B]n+`m!XI4@*f[4:@qCK<-8i@ppP<:9WQ:NM584[A|=98t9nq{]cH[C4/a9g6"I}/mh:;EQFd%i!_`d>vSd9vMd+u(d&TKp8>0Ol;*tRvZMRcPbPpc^P7A';break;case"sr":$d='.c0@qaMD9(pl!VO#EN7
S&@ESv$H`WC@~&vlq(m)3Po(.398q%VN1gIZ_Yl,MSn"m;Q;$<W5
e!tJ2]Tov`]NKQ0avIt5(rKk*]tJO#,57;bay1^KhxcBtc6X27;<%aXn^oWjg1!LGF$!]FUc!IhILZv"6Hb=DxW[L*Z#.pBdG%c;<-XnD{7KgbBL;kQl=T.)M]NC`KJs<"KW+QF{qV9F;DM@ri/|r?*av*Nu7>_RpUqcIryxh"tSl5tWiDbYj)0-7>m.m0t_$w1h<>S;bs@w66`8daQE-IJuZ`y#Sxh-LX7fI4#I#OYcT1S+1|!dh.7uTI1,%Z-NQYAHW"?>Ou)7tcb~b9>5.xuX3?sZpO4k#p,E&
)=egDmxv"IenpWcna?Fs-v4v><%j5s"FU)vwBbT0-tad;!BJ!*IV;Y@Li>a<1[1=WK[hAz`q^D4nVtVU49_+Nd0d_nH/YnxRgS=FvR3F!WFMF``CwRg_9Xg`dCvX1L[)018Tj2[Ycg24%JRV4t.aEPp80_l~=nI-n$ihE&3a:Crjr_UOcDA~5jQxmB@WMt_{V
2@m>x_:c@VGXg]e1e^V9(8`_Z?%&DT>PV`E0k253rtQRx9&]nn6RvVwYXsn&u5xbc
?bKzarW"szxz^fT
))RV[.Gu/ewG-;`sGBBY+yi$fkq&*arwIvw@LI5V+Fq]14Oux"KOq&/2Duc|bgUU^lXii3ryG.I/B9qV=2E9>B5SQVUUx!*7QscGIVI$1jrenHB5$VBNSk^iOUkV.L#Om7V999d0yOZDa>kSc$Psw0757gMx]+L-R&5`,e=q=k7C+W46p,]$-.nPwLU:L;-KeP&O^,4I!uo:oln$.Og
K^`a3J`~QR^Y21fE(nVA+.;=STP.r?KfYCF36$P@BLtE-)DlF`YIPCuI4,+GCKQ@eV@F$[[;5naD=:RNp<sR^B";lE
4HhJW;60QpskcDFxt:ICxNXp5[?THT}TK(NBL6oVCxPED3{ipSe`^VMDnRo6`K
+@:ce+M:&ckohV/}<{g*eiHXdQ^ZT$I2HXe-e`cOuW>a7-o`yyZ[Yh(:c7o`k>$XAvi
ewSFXz_,^I/3"qGE
e;^hM?d_%PfT196Hu5u!(DEiKDxB/cOpsE9@ATL9x8X!~diRW
2
k-#.acqR5ScVN!cWFbH-i=cE%c^QY.x(UsN>PvPH|1k:y4_B<)<!.V@EZ
>2
4
*bfkXbY*I&#X/`q2FFBO8oX2Odv$aJI@U#
"(]PU2W<-ORpsn(1ux>Rkfcg"FwjsX0iY!69Z1+?,5hj:Y-Pf"1nvuclzJ%qi$H:*U9^GElXpB+)ZUf*^"X;XTCEj44
[a<J>hz*$GkD+prAY;;MK/cs&_U(eAoC3_>*JsXq<f`8@:6<h$PdPGsPqL7UurpK
X9kMbOl@4DF{da)jN_!?gQ$,sIdHasE$HF9#i.Kh//u^6IZ62NsvCzS57uvPn<hqe
*_G*_O?`I9!2mYMh;x;QkPG2#qVo?m`d+f,o/oR7?1@v)[(D%K?])yPPS9U?Z)B=(.J|![$0jkSlcWl$F=KJU
vDLz[}P2"tiC!E_#AbpZ:3!76|8pl?BRKJ;_C^e.%>tYDHYbV:%mD5[*+<%VBx:+=@tM=eK(yp!:@?HPkhX#`y&,,.&<6gk"g@a?;0=>XTO%@G)87LT}cz22n85M*%9ll?k+^&(#47t>[V(,nj
[CcRR3#gE_reYgB<_it6:;0rNhf/8VL#XU@o%5lFJ;:7oIR(~-jSDtK#*6X$z.EYwA1Im(XD615jH<O9;Oi*yC>!Ts+NrRd$w+1L%.<P%>84[@-:?ltxK?}5;-X$ax,fqq[#-[=&6F{0Y6?yRmX+{0|,]hkiA&x@PUi.8*N(
!C18C/d6J(*U?WQWt![6e3o_2bnSMEHx*:nGVaQ&EmmDOh#R]O;.erf&L(5=];,Au<ISKsxf
e?<miC78PEQJNTh930~supwP]]*Y1(5l1OAwnmk0@FW4p`Ir%G^kN.G
r^w_$e?Nm_I0jWb&[Qa<DQ%N$*YqXjqNbMqf[s[u?iwb~7t)Ay7NKg4Mv%jM_yf2MszVdhy/nlHG11>8{P;b~ox"kr
-G7M<O?1k-!FMisD78_h#9:6v3?5Y89&=15Y_]a@S1D2f1:@;QL9X,eALvXrm53w_MV36DJ^^!?^!M,@567u%)h*/l4XO=myozh@[[TywDDxb.@IY0aD@4NBVck("Hj>.pB|Hv2`a3^4bSx`xV1K+N9R9~Pfh+*F@n!r4Ms>%+:7U
9)!O,4Qwii?L@%^^f0.buHDh+d,ZN:`e0$+%KmI|S^.1@`!|<Rc;gK!Cw<L]SOZ
ZP@`&;rq_Ac,ad=T_b/`A2),H+?UayH:AAn?[,[HdaHa!FM:9
[3n2@z_eLxPKSknCNHi(xIchHMlW,^KQ<Uj`Cl/6;aO;i9N)Lp68f(JSe2$CwB[&NbC=+l82JV+VYvlJ?lLi/ijR.8._G$ZwfQ3J;.?zrB_
xn
mrw7Z5B"0r>Qx$M^|v>)U)8ZZklRbB7W2>T>G)5YGxy*/_`KDDOPhK
`oVgV^PL:J.>s~ZQ@P_Tm(T1Zob[o&Z9gYGx7R+6<B`MYX[@A.NJ$_sG3sItHD*@7F4/kcWhE*+9_DHrgGL
v6/)@t!I3(EaI_Ny9A@;iwM:X#&_DWvT+-H@2)/VO=EP2mDOY=MPE.SN,$#r^$5cM0C.A,C-3k/74B>,-s^0/qkf5B4-O?1H+7FXbcV%RHLJ[k@`3*YDWZTrp,C>aGe4m")8[r.S[.oR&v3b?L:OQoD!nlj^b;a
$a$)!NBS["2?`iX4AdNj1^pEZ$FPx!2VV/%Q6Y=t,7$1=p5)*?;4e/+,T49g^)[i[:_3qH/{2cEYIKk@Dx3Kbr7MwFi#`m_u+BSK_o=G?wF9AfR/7U/gu
b{T0d&5a_cH0Gk56M(s&)JB5;4_%S
2;(T8ylo.C->p<"YeE
@mq
(BK?Ua%qfT7,Z1YH.2<h@_p/,QAG=W9S-W2f8BAFeH4LTYk5CD)h5^{jKA3#kF6j;FF[[/XZ:)I_"]s"
Cs2zq])Nt1%Wjd;=e?3*PI+,c1D0?>vW`6/-odq}mq8!,cGlp[_IC]p.[G6N/.x5v085ia[p%
!Sfq(g#p_Qn5]uJ0W(6G[<b$
V&:q:T-x[H&Iflujq&sqz8%q(8e6:*h_%Z!3Zb/9*Gnx7vM2[KV[!:=<fOV>NarW34?xO[[5/cW&<,wbgej8vLWFmLmJib_uU&kCxK`rMX=C#drkOWe+}B]fK"i<o[
ht!aX+##;C%diz#e4:<[7r65wUd4,ZGU!h
7AM#x?m7Q7w={4p88hS^w9Ft>7ZF:8/vbE[E%$]v7)bAphg<CqI%:]VRpP%yJlPx;JW(ri?OUT6h"xU
=8q9Q`:7PGxX2S"Kq?/l6qo*@JAQ{iMQ;K}5@u1%L5A"Ej<2+_p&=<-OxK|-tYVt/cok?3;M"uIrIn_q]jqW^I"B%_`L59#(foBRagJ$y;R?vknGG7i?tyosITuyx
p?D&:[hmZON3B
5n7q<Sn/vZgs@RV;r;0LW([#LMnS%r}[U)of8Ntn+M9!]0-@$<;VWo2j|EF:J`wRX
lpW!jE5=YWz7XT"sE6QLoA$hMJufIGx%1*nE6=W=q]bmmmR^:[bgcuhsbBL&watD/ISi3NqAzeL:Sw*JSJ{U}&`3"o1q*W"YQHS9wJnWX!Uj`DLtyY@Puxy!_xg;&8gbQMA5y`G_^gji}Ha/U_$xdN&';break;case"sv":$d='%Zu%xaPmtB|n1Y::*84?lU-;IL(DBC.s#`?HO*eM5Lp!LE}@(C{-5;_a.jHa1"yCRK=LID/BX9A;CtX$aT-Bdp*Eb2pkxxz+9$64JRH+UvZ3zs_Yl-H^P>,<$_kH)^2;ElgTLO^&FmN5,x(Y3_70eB;K{&xbp/=8M]kS@Wb#~[}tPc@z"p-tWs.^UtMfet}%fGTh&P*1qxN28o$m<`"<`KRJqu(5:kM>X@=>0V7i.+(pW*7FPegkf8-r)Oo4AryeWF164Ju0{aRI,YP<e?|(pnXZw4T0Y,>q9OmBNe%L&C/W3r/sQHGeKt{@g9g.4lh%&)eRL@BGgwm)/cOauw"EPcC8qseRHnqcs^n)W+F4isvDU6T0Xg8Gd(ItKex6,t{14cj+|G"=Z@MI|RpkLo=/}=CIl^Uqn9^o!/$0Gpk+,Kh+}i4RvQEKr=;@+*DHiTzo.I1jK9!Wh+!xOEQ2kf9-U;:p")94Ym,hPb#EbE]8Jl!:sEMQ3NH<=6KK&g$wSIC2)urw>J|A_i5,C^ItS4b03b4K[@>s6jR]u50Pep)R%rKq9AavKYt6O6avJ3~aZPcC|7ILRF.1MB8?FZjy5N-Fm^M)3>|aj*dO^aObhHc_#watRrXykyk%CHAoKAFrVZE[zh*7+dnPEj0?;]]7W^ysnN07FR32A_{;(3"]()$!qEyh.B0=MQbKr<BWEti+IO|QS=X,1=1-;NRSCYIo@@Z%<yNgv4Dm"Oc@N!H7BY@()<D;NMvRhBj09-W/i<G<"dvWzX?b`X_P;Hjb:k*1Ey0C^rQB3Z.aV1kPnNyUDAL"ufiQ.eoyrE%o(SPw#VQV]SFtj1-lYL(L>CH(VCf+gBFEU5E-9ozn{=#1A/Rp8m~_W_[WIgT90aT<nog0(I`/mR|^&IUEK3ed97f8o0[I%A!V|<h`xl*0_8.=7DIBtDXOJ.T&KCZl:Z]Fw,7oi=[:<.sP=>r

efw|VH7J]|EE/}gdr*:,%MG9Ja]0#{#%?]wou+8$aPJ{LDuhE|E$&pmJ29gP#qQoR$]saIi{gb3uQNG
"4S_9
%NcD.hM!s
"{C,U1E=Wsl2R}r`@^u.QY,wXu8Lh^YRP2PL+#*_;TYLC+3-7ZRG1_1
-F`q)^ZLNhf=M}J9!UQ/07-pS
al/>?Nk,k}@DUoZM<):Z8J(+Ikfnm8:TDV0t?"!.[
(Br)"h/2cnFP19Z?llskT6o,OC<z%-VwVRFo%PNR$nOP9/44A2:Q+fJjCys/EHifj=YLf(aj>u;:9)8&J0J6QsHRfycaN
O=;:-X,Qo~lmRCU9!aFNNsNDY:0cO3!4>WU)1@q&;Uhx6H3/vpFw;
RYPAbh+J;>-.?k`?F6^_!XRZay9G?w?.g<5rx0Kw1Vt=q>@$MPOo7f<<Wb#:gh=jOI:<qADkmeqJ.-:.,l-nEmq=??v~r#5.a(a:*-vPVATQ>a`^[-qg!p=XOO*sL-CIa3tO=]$R,K/FQ<q/8js
e,%kV0by51HK%8D;
XK=^8EOam/j]ZAg9k_
yu(lRYkaQ.j32%"B<bRR_$Yqx%YS*@U.Vxbk,NWD1P;GYz
$*Z>np:j|U<Q4Y!bW:kJC-M5ipJcGh2aV:RyU7LiUx`Csv-2GsSi8[NH=8"fdWc%jP/)@Z>(."u3oTJxAa_;xIYEaSG/5Vh_kvCHwyNA::lW|L&Tmmx#xp$>4
**Y&ro~rP`1x-B_mUhk_"=`B"?saI20@I0L<H!KRbZG*YIr/xaSLS*Ddm+-7U.]?")h4gl}rC4sfcjcUV#SU3]gF+iLjr4sYmu9kET%NS[y-s(CqlEQ&@Ic=h+}1iZ5:2sl>"U`.xO`#&(h1-n
c:&Ejp<q;`RnKsi8SKujPW94Y^T$K@=`[F%~[$p"sfOCb3(k=7v)(
GTZTlC"fLq!x>g.~8*e`RA/{:03En7Xf(i]=9S<-cY>aM#rt/U[h8,Q2"vIPlP4)M?v"n}$^rG)*hsP2vg3~^;.|h`DlH3AH1+6ijhe%T,l
.5%"pjiv7MAa/Fr*=+(Uxe*AckL<uZ_}X-nmn!QlNRj3ay[>:qAN2w9KD>mo(#/|ToOSP>mD7M>GY5)xP|5_8[Q`u6Z@KmY
+1i
pVe_;_)d=^(uZ2[yZTiUu:*}ya6`5{D(_n96R
G:B<p
dcuz![a%uJ0Qq%nnLo)6^I<ui{J$bbe9NB@|Pt(qCVAg:xT
dmS9e]c-D/ntCSm5IeT8.^p<shZqGvn35*=<qWK6;RPWE.2}LQkYV4kIa+Det2nqiP^;p0=pWxq^!=FBx?R+"_Jqw/vY%QQQDffveQz%s|,3i%0u;`?EI{<&rt8?voXnijgM:RNu;1MfA|Nif9`m2+";pBiPC,HB^qvz`<%E*gBMR(
Z7Ha_IZc8(!4eK0v0hSNYnH27"}knl;RE3CtD"Wk3m(Sm84cs-^5GZ+G:I|P(L_$[LvX;L+F^g(GacXw{cCkkN|o*jq15*)1Ys_h#8;y%BL;5l+ulMmx{C"qn2vsno^(j/Wl|E%@9f}!1$1L
r+xMx].J2.@R+l1AYG_@1z%}9")LrLd>hGk?es-{Ya+v*+>%J.`>]IQEXDx>txuCD5Dqolbp&[6,1
guQ*/y)Fn+L`%Z!wT3Dae88WJC@5/R]Pi=%e2Gx(HCroD[G6<$
B"oDO;w;v59*aloa.NviPDnmZy?X{lW3n1crPT&bRjGh0$@t)[SUX3_1rwfErwRkiPGG_.2?$y(;m6~x0
*Z0E=@csaKs8Dr$tDf
%jT>-R
"q0m+[rR<(gN4$8O(Di3RZmMo7w`GYl-osl4?RP5Xb.#?b/>S,}uy"zFDi=VkZ?H&lI91h_PrbAq=+*Mjb,I0lrALf|j*I#<yy&SeIF#0E/XWu}%pySq3_u0KUFYsf0m_,a_jIEYso(I4oK/hZhuq3]4:kk(93pkqd/3M`979Ntm*nQK+c;v
=K';break;case"ta":$d='*evF{crpM@8+|u$#E"C0CUvGmc2_4^iBI<`)*^9ELF%9JsVZ
Pv)*0QndpsE|JPjGn&8+uC,M8Cp,xL7JU|,5=L1!=P.m[<$t!AwO2H)gYX$r9:7OWh3Tc3Al=H=eij@z5n;JMj(rMjjBHPJcLf>[6eud3Ky6K]Kkz(Mtd&LT7&!rs(LWW!,;4VM1l8E$;*3%"qfWODgC_uwN[hqOj]XO^2;baYL/l
XR/Nx~<9clxmcTK{bE7^j0C2.+dwqBT3-LxkPhN$EOWal7]Nw3jmtOjs^Ui;]zz(k/(]ynkS,gTV=B9J/5wBaor];Rh:$F6ZY:w)dlTkhC#8^j6riLidCD=Z),$69JTs;s_qhAwFl1k4>yOtyKZ[%:,>I:C#c`6&DvRCO!;~)R*d2LOw%YMRZx6]-&xE/gM&nDv7%UHDG"3rlQ!5dM6gS6TDJ,_w<OB^aQi;wP#1b7hi:2Cpo;!8H-!urYMD]jWj;hv_b~0$LT7MR=;g-e;!).S
,?libm.`HX?U?`DD8}-pOF^j]w<4P*fQh55,35T+H-bSmBS>+$m?+r12P.0;Dema=$.nAoT7EB[hf=7pwqN@#TeLp.k<O5JlPurmx0/},&60i6ux,%UtpL>,O)U{gG^MeD)e,z$bbhME,v/e"7ShxaRJjOF7Al[Q(]&rt{)[i?%&rJQGdKJ(T/0|P;]uc;%$T4ExjP%WrJ[WMV*|l!^FL2<IkukVlCf"-&ZG!x5u5_RBsh,b5&Jl3%[5OdN(WIs[6}Kv>1u:OY&K[0IpddpGpn>eiXstm|a^esGtUWbxv|B[D#=.MM%yVm@eh.w~g
p@o)uKp;0|wMsN,FvEh"k;`mFp8Huc$(n^aLR/<S3iw7@RLQ,N(Jwcul>!*]rVs!gi6sKA?8"&m,W%[!P.bJ7!_54+hV#6TS!tcQLNIkZ2eSwDy:"coYQj;pT})H@Z>/lWW5xKYJ)v(uH%p}_eUuVq2Q,K?8oa/e(daa`k>P1%X2,7ClEEt1O}],s]R-G_n8O[WXx&ijI(b8LW^Ym?5~<[%<o=JY.6^M7?k@#>j9&BjzhNHhd8OH.vcOgj,]g30TM^Cb:k@29i,DCV^|j&RH?3GX!%bolN^j9Z^scr@gK6wc/C5/ABaI2+H/byA7R5BL
deY1/grJ@_v?J7J3a!whU@)i[C0T5Q7DT]*qh/jg
._:}sw.^NGuS
(D
yx8k$zZtE
7ohp6*dTD{KRInjFTw/n*`_18+fV?{u;gCMaRu$>aT7Tc-42((
BywYFfmA}?.B3;1Nm(!$RdWNBj?"Zy8o.dO`Z^qny/6K4@vbi({^q")[0f|K!jp9,ned-mh,W8*^$X2.C=f2bA!@+LqB9gk@xSc!ba}CUcG;6wUW:4:N]#v`PY!,=
xr4%PGauTVHW0?34Ei"_/HdS^x`RxtM/gNNlExmsjmf].g=5Tw&PD0(x?6^59a,H^yy:+POtCT%p^N!q"a.Tp4Na(s=cKAucbR=WpsSp<ca-y.DgM+
*kEZ!_*3em&d([62H_8INi#n2[f4vNa~/ekwU9.{^3a6c#OI]!q3+bDYe<!7WguIPT"8JV$o,{mmk
MArafEr{0LNxKBtk
HSu?TM{`Xi)O"1?:2;4(T^`:,D4Q0GzI<1B+*La<u;rc+d.qr#lHU^y[U$^m)[GYY<p5{YAE#JB*3/]#E`;az&L-b_,d0j>Dq"&I3*D@>&eluqL<h^1<)6bQB]y!Kn"#b[MZ:,gCJ"_H^q[=[oI%@m!2,Bx^GIs+_:t!R^6hVDh:wHW>}JrG6qx4t]QnD"*=PDvdu>gtz-}xz%qoLy[Y@6Yrr0Yxt!(8euA/RSg&=t#"yGF:E;@1;pqAr[u3[]5j|:mE&^-oE,N1P(<gjp`Q+/-.oM8p.`:+)/8!0!Y&~p&Gad<qnS#thSk_Cccns7^$0cDih6>qIQEp>,1!~Fw5VOLxW2H.XLD98!zJc
"`%yV@qu/Y13yC<(@&3ELDp*FVb(&_}q(@yBT+U
6%U5mZXp1]+FEvi/r,%(-5c`uH9%#f8W9pw$?g(.z-8-T<.fuTso*P^@MH<J"t(MlXP8LM^&mSYjKJfeFZgcnNo"CX8KxP}6Hc$cm[!TQ[+m>.W,0kAD
$S$xD{R]Cq/|*DMs<vpc]CaM4S8K>&3^w1.2,joNhg^#VZDKg]N?r$T8Y/K*5BVHf(%r;>lNhjfFnFRy:zPPDeVd+r6)p^x@c?3)a
c~&=#VRlNdS_2`4fx`!(WVIf^)sTy=tSyocdQmKp!e&8Ptx/e;-Q@yB+CIHXA#I,45N12g&[m.u$Q6NyLD]bCN5.1Ce4<EXL__Rj82*2wcVb7crZtAE4I:/=hrm$R8jW1l)>5EIWisU8j&(@i1%eC?
wWh&F
u<Fff8jBE
#*lBI:CJ=R`)T>Y.!Ii2wZh4,J6$25IW9h7VhTrsC+0JlPxp#6lLqr;vUk"@lZlN"5Uv=2kvY"*,C"q[.5u`.qY;&+y,4o]X^&k#&cp4JU-aX+Hh3-K!a*J*xV)%rAby;.8;u,p3ZPTC-j-
=Sg"Z83b`6^%#/%#yY3gKC47#L/,%/}Z]3!*Iig"0NgsGO+.Hu!2#1w0_Yav+J4[,N9b2IL$.@?wlr5sSr")i0qZLkcK6D}Z(O|.s=NKz%@Uu"{V5@obdP!sF:N#Z-CL+3UE@1N0^X&@]b~kviQGS
QOttowN[#A[DLMNXYHG>%mw&+20qX94.7Y]LQ4YH(v{=(0HlkRjCEJj#m$kO4h@QA@(%uOnFHgk.Y05Dp3LS=)9RkRBr7g]9OgYBaX@E*G-hNR2-@NxJwVd0Rx"Es:8n*F&QU&82z1fcPxuW&I$OcUdekv)ETYy9ZHW7TxqrK2c7&-c=dGp!tMnM[kT;WgVDCB7AuH~J|<kRNo*dAlCMX^JBNijvRMX@}2S"K5U@,B/7rYzG-P*JMN7;I99Ef9Lo]`^ZH,Bp`?2+b":yPdYB6ZkfG6pJ>
6Sfg"
-cVI2a%*;n0#yCQ>/5I^RUU_o9YF*>Vt<+6M8KTQI)o2rh/Ia>j`j?HXYFTsK[!lf.#TL>tpM*2ucKtM?&;*<S{OjiC
i5h]&:%3z7T>c]SgFeN3k+3uiAIYV7c3{)Vy)+5"+6}
Kp6"jTCPnV"Y**L61A,TQ`T>0/^S,"qdLtVhL<Fc3;PM!74xw1rsQMNZ$h_"-Yr93mH[Xi7mV"u2L@.8w.AMl(cA4Gp2T/fN^N4a]2UAqP<AZaf`Wp{pB,S)H5YnJ?EH[-]S{x.G3r}aI4#G7?&>}y-<H&-(](0!dhTxdljGu%s*j/`$IXR`i
2Ca`S-za&WU-W%t$!_Nk=YC2!o>=l*,uA[ya1,7&6/?Yr!<ep*Es8!zXIpv%|$PvR33.!?c6ZdwH&N5<f-ms*LN^<gb4Xg9`0Rfm
(y<f55puuO?@aa5=FS#Y@dnmVM<vL/toRa-5RY4KViC?m+JtuhnRw6p^W0<USx6~otg~d|h`j-rr]lgP/q05Z5>
N!c+b_2l^:%9B7Azb[?#[$I1Qsw,/U
=rr9M58nC<w!lblH*Wp*?=!+s_;:EjgjF@>hfNo>qXrUipNO|s"JQB<(:3;9B%lZFf-wVJ(GL+QE^lG2>hcM!nMah0<Z5C<J=W4f!UWtkg!)naIY^kbmG1c,nl9M,>j@VW7F>%C0#M&sB9lF[HbD1
bIBUvZwitT;
8qW8mPAX8]1w,^y*}!Jc~hwSNM5NV[Sf;u9^+3`eNtl&=,m<>3*uZY@2!L6+SG$c1]J*NEHa%;kD8Yzy|k&VRV`TYEp[2i1#8)1@6l{0>ne-SMo^oy4_,1d1WSaUE
]N[f,fp
=f#B`OzlD@$dL%ET7d*+pTW@5oGAPi6J"]tevD|Qp(yk^M;h/<}v%WN>e-gNB(Z"=
iR!Tt>.po0-&@)IU(8l5mH5G=
UWIq7NlkqKDvFs
<0[Mnsb!e21mHM,4w.7O#Jd=k]wOs_6UUr]!xPF,MLOO:*!fxBgVI&AT6L-WM>rlZu/jw"w6un&hHic=4/3zJG^_*W
F4GlUV]td-i-JHOaF/G(qBLW0lJV<OfEo]S<5/~>AqwBQ7?9`7V*m4O[>9Wf,/nB=OzJZ*Dy72cxoo?xrt*Krg)gynjo[@1h8RO[1Fuw!b#?]X7gbu+0I66[vZw/y#u(<D+3zIFoK`oo@<bkF`;uf$?Qg<b_"<BdR#$Uw[p`#]J72h_<!!
6ol7u+u;e{[>oX^n;a4x9w3Vets
?6<Vp%@/l
f3q""(3<@udqozVXaaUx-ghmp$$P.bG1jODROHjWl]fNI~wn$K=Z=%5WAcex.4x1KgBw=cM&,?US6&,hQ6dj`P91
X%CTAg=u^O"@E
5RO$wx3;v.^Es_"
jEZh
BY2xG[W5E6OXj=h/v5#T4LWC
(t7@!1L#,0?Lc.pwd)(R;Lu-?7,!4v[_ocVK=';break;case"th":$d='&]^;zbop=@97Xh=NFd0eA
Fc)VI
1@Vhd"YH(S1Y<,he<:VS`ToKnL2?=x(e
1DEk#iAEOW+f=
#/"J&DG7wny`X~BQ5aJfsrH]5O(z6yTo2.yuI
E@nKf3Tw(E(kg@Q?Q4uFoKQ:vmTv+eSt/EIkT7/?twjumkT7=O?eSt/g0;TV%g;
Tw#W/d`+dGtg500sK[J-$16b_X4_o>AmTg(`KnMG^MZkVwY,BQfz=NgP)FI*yG;J6vYEI3-?4YW(lH?8%rhbCO!N/>_
O74qiFyN1vd!BYd!75Xc2vOGq+gIe:/g58[h)l^G*spH9sUOcDdN:b#G/XQ>IxP3Q(jzovuJeK2N(,5iSnx3F&=t*I[$1abOx,@g!Y$E0@ia>lWN%Ru,Rar6
}q-F;%+e*MV&""iwPExY6<U*0S0M:[
uiZ,Sqo>@
aEGw-n9
s}o1a$bv$0+/4c75M?/}z!;#vOk]S1w0LYje<zvo)wY_v,wwe#/w5|nxkHB2/z1P99=RyGQW]bU!%Py#_#bJtyRD6H8Is3<TEwNrBL]_
wA%G~TlJ=U62PXR<F]Ya
`5sV)`*GNT,~#4rwDbvh,wT5JLr@L{yU9zldA(ig3LlYjKYcRc"/<!UiV.P&?byx`lw@XGcsK|Wfd5Ec:zoh=~2+kL4Bw5_.6"wNIg+frRN%j?gg>F(8R/L4/]Bu<B@C>^QvLp?mZ&o[RoP|
4/(+*b>l!%~mCMpe)fnJ<7gKg`O0<8$T^)goufYF%X06J5Y2B(2vTO56UiMJYkD)4nlSPxvQGEfd`BuuxM*]:.xheq.7&xUpjc4sIa[0+7zNmGl2Kc:H$u6
OXZRbmvpn7#`~[uDp%|`Lx-)VcYh,TJ!/_eHMILLI2s._,2#U7{q%irMkU_5"6w:;Ds2
-IIi?Wd?Uxibo*WWEO/fN4j7@^Ngy*vWt~t(cTah*rEW.}>YY-6"D-39;&Q8Kv-CLB<T-X1a:LQ4K*jmDN4j=R(kG;)i`p&m3`?_Yl,Y*^6Zoa,b,xw#iU<I.~9PQ1,ai{AablMKuu4mR(s_IaYA5ce/Rf]cA33nc.#2(4U$?XYgg7MVd3y+.BNlv*--P^7%#Eo
A+JQ`;
Q,.j<
~0i2"V@KW)lISwfZ0<b6g_Xz(@/=u;RsKYu4|.l5jAPU+k_/_19`ah2c.d%f/M|]-p.,22JJf[os^im-Mt(HZBdgZ)~ODE.>l$HHPZ|P7?90B`"i}300TF7:X5]9M[RSN@UC`V?t@Pl.6"Z,A)#Lrs$dOx^l[(9y*1EqejmmU$!d68kK=/kxh>D8UHdnFGM..yw@;OW>-"&NMSW.2k:F/9HtiC>ktQV*~qTeTp+*@;8*eoq5fA*=lR`Z_FXWMj*w]YU0Xxaw})
0:0d&`=)3Se;f%I|5KH436rh
qK}1=<~XTasLS//&0a|S3q$nG.,tx.3]h*htGGh*@l/7"c=?OlCtl-4^]v/fweemfEFbQ+5xLUNondZ^cU[?`IRr&H
!EKe]8*]i%W]!s+`G<8:;)=?aAPCW8H-%)myvt(!:OKxSbX3E}!{.<g6OV8`Yedy)<:SlE?Nq~Fw"WI|k*v_t3KA-A3yPzRGXCp-5!h<
jZRYGtVnoK%QErcAMQH$Tm6*5ks.7#;PcP$NbB%(?Ui37=_^{D`R`DXVf8,HY*V-WN.3L#ziw9ssBfB8lj#+~lAt<S%(J9O5gW)n<K2Rm88(oNJ+.aa+x%L37Y`)za1iYW8q0X$yz
>5Cj0%[
Z8NV#;r?SG)?ZiM!1Pj*{(h#{Ai/|GQhFE~D[G@V?SW.,91v}^,8TJJQrqJOZk}jZ@`B{Rv%/;U1,]$,$6"qf!H-r0-`OnT3]K5A6CsPq5?<W(t`(eO"M0g"YUrxaQIx3"(1AqUD*k$Bj6(O3
dT%6qq/rTC<>-Jo(fTnlIYUJ2`hvFdi+-d#LhG17$Cv.JUJ!+_JnJf$->$9uRTv*D%eLw4o&(P1=.s_1T0U_g;nDh*?F"]P?/O(Hm
w&pbV:u:35jtv_?;6lURq:=9pt@w]_l!UD7b59Q"|+MUmZGSn[{K!L"njhc6+apRn#ygJBP9yx>K]({>i/QykwRuEHO$5
5Yn1Pg^ZA4z__MjE{[wVuy{:IyJ$YqH<eu(BIndG@5dB&Q41ks&;f(25_
Q_b3`_*?L7oBs6!]Gc2/Z6rWvCODsh%,c=m#_QF4K<u$`D@le:xW3A%D

}n&E)ra6x"Q_L1wPJWv5x)ZK7AlyY+%gj-1[QG2$54]H%h8:a_5>6!kZi&*S=R`Scwdm->0k=tO5u3=W5p/TH^l<FX3oQ#D!1%l";/qBz2?URXAtZ^gy~8jV_)_!#&jH#(x(.eKCFgi1_HB.@e2:HQrD9k}bCms8.#KC|K;9yt7Z|)85W.FoxN4,uTQ8&h$*AmBDfk@iT]dhTwFG3k[d[rV<oDXZg!K-14WkEcO0-
~WJa/`X*:$}hq/1ns61Fxp/;2R?8=oD.ve|M2%1L4BVO_TjDh
gB[N6aL"ZS.)7&IM%0p4b$qTe>z$]ynTUIwFiO0JB>;4D1Fk;yym:5m?jQ-E:ReZcA;IVECQ
(A1j9AQa==ScY}hGgN!QGsEDODhuo19~kuM[k0e~14%(WQ?v6Kth
v>Y/5.7S<#W(pn^6Fl<Ovx98N)pZgao(jqZ36%zpE[0u9[Qh:`@m7P^doa%rhRK6fgw,#(kuJJE)-ILF%V6e<B|[`^w&/aplU,v[)jCq5`H4~kgi?HC;u=D`m2kN1![1"TIHDo}OydV+TE7n6mktqO%WhImDTn7e?A!d~tY:#
z8&nzc,e.Sd]<H08$^K"oXa?S&cI$=m>z/YTO5#-hnzY,8gD4UBU<r~k|_yt_K1-?H.S$&RhMbnR!Q:_<`Q-BUbIKp1ZdYt7ksb6xK_Gh]4X,2|@%>b`]ZVHn_Bh3LcJ@@nE4i)]K0#0^Wg090XrC?DTUEA=^u`CK-m
*#J2Q0FyY
t@,x:X.q9,oav?zm*Oda2+gW<m4[dQ+UWscUDtQZB?0QT0
r&Z36/NT3q:8JV;^WrUGTCCt1pYTCBVPL:LYEMYI

XrXQZ`V@UMguM-4L"-[Iw{=(GoB)#_4uuE3
YsZ2<jyD@w.+)HHmb!0l[nf%>n;3_l2[jdvP
_Q@cc[1V{HdH3AIL`PQUjc*!"IWd"
1V:gmZ%32$w([<"0z>fwgZkM>sa0)weBT",?v/Yd[QXkOT(=o]+0JdQj!?;)7(k`DNbZ:^F?>)9pRv2u9l*2en1%s^1*7TY@yohQ?IG$%!y^kJP?U6{&|0bGO:nr;vWA(6M_urBkD>Q3BTmFm2ES`3RFJnnpkw9oSr$NbHM%r){wYrKWv<KOiD`@zrK_$&H
4G!oru:iMDLy&>!JxgiEEjoJC3JGH.:efCrAy1;DG4O9>yqhfs%7T]#;34M8&jTnB;x+T<DJ,Y_C4?{fiqq:%Jm_
`{JJ%@1$f&AMQo/Dq{]n(T`~cRG7h1lWvs:NLewg*Qx2Xwt=9pG*sYrnR#"z+(-i?HD=%9%nZ-nhLG9",KRl%[?xHH(ohQqyW]yc2UZUKI2nM=x8#-BLm:Ze>h]/338)wDFhZ,V)U@.k]UG*`zjZE9Az;0;j"6!Jo6q=f3&ak~jjM~Yc^,g3++abh?YkOSPkuX0iw"GB;VelkLgh(oYtSCvH)mf<*pg|=8y_hY,(@%oL;p/Xy4%9
GbmpcA:g:o1#cQ{v/uf,<4xDiyz4qnr[4TDn~6CAB&97A<m@Wd3TNh^7{CnrGf<^3jP90Gj57JWWkgr3QfRP)pwJ/RXRdVWE0yNKYm"!Oy7byV=N6';break;case"tr":$d='.R]*gbPDI?TK,
##Q!TFTZ0+ewTe)/mvt#7#9[UN[$l@f_#UFPD`%86>H*9NEpVuuF{`o;d1nwkqJkIXQUnO+dU_D(1t6LUhiT/V$>HR3Je:c,#w0>S_0?u`,
O(%E)_1KuEx61,FU?F+?GAQI3p%nE5MIfU/9XcMvW
%J~
RvkR;Si?xN%wOGPyV)5z)bkMeY=gjPPO"Dqq0m`R]lY4]y>.t41w%v_9@03fNR|l7Pk2=(@aZ
ECX]~D9<mwY[RU}9P]cP9T$5`uKrnt-CiwP7A`=EY]1&C>[:(I_F+XoN<mKG!J>Vmw[KCa5n:C*x>TUerv`&TI2G#nB/|L41oxc236r>,ShKuvqPfQqgd+`Du*v=KdjR8,1Y6TrvG;QXeKhX5LoFxR-AO?K_N;iz!M8l0kTvdqU5>FgAkS&N][$+%wYDqeNDlNW#&_%w2q=#^*G8Z$O_=&K_L*eHAqU$&&*]Fp75*T@]YNO
=.]DdF_"p:?JtHM=,.tbyz)^;?C])B9AJJxnae[Y]<QEfI`a-`S1(AN!$]ep;Tu
ejLu%?mA_2`hcM9k<DX-OvMUip7]#bYHU=7@/bm(cN#mi7}-Vqk([Xpo~fNjy&YUO1|;"_2KR[>Pv4DuG9U?"rn#2%VELdiOZ7P%vmvQh).)@4+mFQVQ5Ly-PPLR@q33&4iH0?a;[5vB2"c3z/!mAQ4Hd,,UH+Z%`s=B$O/j$IsNCCzQ@w*&*:-O<-0pEVjPJ!5->MJ
;/]I&S47z)J-Wh`0^_;1<W62J#u^K,op[h}6ZVo@w#/c_SOg>,LfXJ|s=_c9U
9@b`4v6iq5qUEPgp%Mui-^2rgv,]zn"(+bot<(rr;p>12Lp[JuNt~%oTZ93N:fwO1LjVXXkPaf/xb?R7dy20C+t*GnM*O;3kXz)A~55N2d8<W.?mmh}
,9s*<q"%)W)^W$>On_rN]ZJL{Q*uSLQSVbi"N`o>+U[!]Lce4.Wb9U0.}IgpMpr*}f~/21@E!ED0:i)9|ez#.LF-qm
3$o;eq!5=dY<rFe)Ak`l*Mj"`_c5)>
>CPMcYQPW)?3{B$/og2He[y<1(G"GZ
gw)SvD4x[]-4g|F?>n#(eU[Mr>T&S;C=#K)mDK8&*B^*kh-<y+/@Rdxxy<3z998ubWuZaR9N:e5_J+V1qPu3gwEIQY>Nok+%aU`a[KU)o&8R`GGYj,QO
eP-vx"98=J/,}B0,$PsQOm-93/NozV+u@Iv]aUIDb-i=W8yg_QQOj0]Z<
r"6*z3+DR?IQI!]r^87E!<k06[w[7<=d,I=2S9z+R8AMwcFly*%AmR]>sT=Fz*jFgMw$(wSHoDp*H>&-jRv@LpXK3SMi[31:pxQ(yxz!`Vssot%5fJ#MemUgA2bH8b&&bg6sE.E`8/*@
W%`cP.+]sc?3H$!p;|l?^3K]Vl0TN;&0ZbN;N},)3dU6ijKL5nS!-@>D%T&H9q`7d]kZw`MoM_l>D%H^$_yC@[@5HW[26d
Js-*88P"mF3.,wS+./7:u.MV
e@v6/bN.)&Yuv-FLIegHj;:z2feokgcYQ):5S$>)JC`s<BSpjIqPlwv.gEKLR;g{p55:1m%
/i[?l#T)NT)/C$F1#_lvjM5?3@T1$?^{D&xv.@@.W:18eY"tak+f)<FHN0gi0eUzC>v:d.8H9]Q{$SD&$-&3-npvTNwI8*D
EdKBws=(g$P[uIX!u9lf5yA/y5bRP63;f4X=/pK7UZ[h6lI~X0sQLijJX+HVTT[sNz8E5ubekwk~q6&HR**v-^ZE?0K0*I3ie*hK6HDDo}
R@`(S5PhI/,jlCW1?h&eNT-jm)E<)Cj:7%"?3FCj_S:4bZV$}$h+LocQDh*.oo.Ld/zYPL.Y~eg+G)&@"C*IRX1Dtq1)/72^?H$4?-8D*G>F}gJb5KCN)O=%^PP(OY/9JMQ-j#f<F=mkqq:Xm)I8y5DtHj)!dEF@BY3o--s${.+/i;<,YZuU[fWhJ%YV-]#2cU^PJ8`.nN(4iCcw+qk"I_2ykPR2FY2L.Y4j|ikFpZ%x!%PkA1IwD):=#Bp]Q9A0.=#/1TR*$-]DG87TJbFn^p>k0Mc)QU+@4c)QuuVN-jaX}5<O0Hbh<S-.PaVWiq3q.W*O$B&%mC!9*o/*,m
dIrJXnWMd]3x#?.A.|F^f#@2+v$lg=Uts>aIA7K78d@E)Add<-lEmEs8?2E=WZr79<GK]6nMHf6FqITRJjBXjMRXLAnix=Gi_zZcLwp;+3Y~$:um5^N[!+^74S[qbaK~T0Fb9<f45MTW?Q^%3uCgae.0M{U{)O!y#B!FjG!AF-1ABItsJ}G~_v5bTF.eMuOc2j_&`;YY)G[9#XjN)1_!:0]Sy*"6@2h_eF!/X$Mh2(rN5y%kN,;nPf`sYY"DaJ!)<:N^c8yJy_b@W@q@"e)46.Er@4_dwO`=7A)Qa_]T)]9~C?@_`=U1+cZ[e]:JqJ<FNGDxf@[#vuN;m5,Kb%$]/j<{E-i4DQU_$4N3UJ".Zl8*dKv8LTA~6/Dz_Ogbq{fkfCs!yRHQ&Ae<aaCY3"q[(<D(Nk;jYAb[,29-tmm.sYi?L&B0v*+C-6A&f:5ua{Bk8(K9N
DFlOieilBRPIM@x]1;>u=Yvu3vNph]XUvIiN]<(4x<)F*09(UKb0mx#OkyL&y&864T5{lZC6Op]|BUYby9,93K&3G-b`E)!u+Dg]PMn5N7+fy3cW!m>t;5i4,^"q$QO4ovCu<m
=F0M-jtpg"PF~nA5P"Odv&rTx"g5w:QI35R@aJFXwgl5?B-xmZD=]m##zy^BQkwQ^9VH%B&uuLVMqpZ%%W7,E&1,"d3vl>Ye/YMiLb7h2M
?]]Lid7)fk]oWk1AkIVGAin}-"YP^UfJ-KR6R#+1@wt-xXCq-O6>;1aGk/m&FT*WVeDl+?^;.im}<tc^iw5U^)3;s%2SM|HaS2E-3uC=H1iI[u+{rn5oVJo.?l!(TXLC*0X,%J)jo5.y<)7^pmQ%ifh8!WTM13N+0PRP
%NBhxoWY>xM`GUBO@(faoi6W0uZ$E>."C
V)N>,"-&
>cH2Sug~jbsy?N9]mOPI=o@mR[Mo]T3_lL$t,.Uf&SaljIxe""';break;case"uk":$d='!ev;zbp1@*7XC-u^z0p(dm;1xE<T:O{0H];OLOMwW"DBXG<A&,ILg:5oqTCU_daW?Op(.i7KaEaKaf<LlL_lK[,YnkGs3V#kd4[JQlRwX<^I`x[
eX7g8N#T>TeV^L,hegJ
|hM"_3WuScV4;7g<}A5FIv}WA7qweeoMlf#.,WhKf2N4]+734[D
+)rX00~B]<A>+Prx3:>d;Ze),,^lvo]&(cO1`>!Un4/Jm<OuO(x58#Z(MZ2EI0jm,(]MBoAyEw5DGyy>tz)@T@0eg4[<3X5"]dS4u6#O1/XW4!W&
D;o{1(2v=3xS9nJju1hkfNbOtc$XV+!2lyec;N$yjuq&,0Gh9O,qniT&+P9A`jqx3DQ?H$i)u
k/j!E,1lnL0
.M9f_bKwJ7VQp{IlDzQg*C"1;Y`K)
ZRLQMX9P#^bgX
DGr9%`*;:tDw@Lv+K.+fA@2*6%.*ltTmb,y9,>m8SZR_BV
8=CSlZJR60+UQFvqV$]>w(-
?iKXN%<[7,"e7yE>}oX32fhf2cA`fhwbf:8alMW)QY!&r,ZC!M]mksG8kj]^xb6
|jXmYI6eFhQj{o/%4nEO8p;m.hfaVqV%zw$e{4/XO,bJ[snj%Xo<_QtH)azjH*VjIrR7]
t47SYaquJwS=7tbgyX9*eetH<9EUh*]l9*,s;^Sfewuu!27`JY9%3oD$M3%=W&Sqht9,a4ZWjgoME_,y4$sg{h`m_2exUM2Ln<~^Qs2CaF)j
Gnxs$*J%w~0Wh#utG]<w_Zc`!Cp&cKt5kp6{4")Xg5i<!rEjW-4IMy7t3cwUl.O"9b9jGzVV8=8ge{Ol(pP{hzBh,tDe]]DJ!xC/t+Rmd=cbNb/@P~+_LA([&xQvraB:j3[^9Q<^5Rs"3Tp)%QUog_8D-ZP"5$#pgKE4S]]IoTq>g&,/QJH]#)"hs$MnhdaODv>NeXom&/w!/x+f,H&BJ/<)(;Li-]/W5Z;,Om[Z)ZTBG%WQ+3sg%z_hThl(d_KbYymb/E/qmdjl!?_ahh;[/}R/-6P;f5LA6z9oGuyD0}p#T=S-IPJmk@9b45famTuqw,&_]!P/QZ4EBk
*$+lKJAZF0%AQU1bjf?!$deZ.p>3avY]AG;?;(|pmG~]E[&?*C[CRMDY+`|ZiAea)[y9
KX!<`_Lsi*
ca86Pr]vFY^lp#Tn]12.>$O/OFB^]%Yd;dj1?&T=|FoyC:@c>.>#riZiM9*2#^vC-
XO^Jj+Yh>hL.Jvg`zF<:zFW16x6FU
!"b+/G.d=bf3T-ORvb`$,Jg=X)yNOTCrAq0][orJSI3vB>K4y9<tNPk+pIhK)T]r?&`fJvK1
BY4,cmh!>]
Yerku]6k#?&Ct[qVw:P6gYUdjv1*H*|X8Dbw4%ArZ(r#g7%RLJ?6Pdx6wO]2K&"e0EVHd#0#[v7?vqN&Eh+i@g;e^.4?=$;u>T>R4<vN;t`2|udiz%Mem49hO)"FnjtE&1/:hX]nWL!9uoi15@lvXaAW_
G=4c9aVt#[VI$h_4|bF9r
]gy")W0hJ`5j=`b-dqOVB:%[![IP|KM[isjC3Kj.FY+7tVce}ezk)!z11(;62Ud.G07&3eP:`%zlHYk;D*.><:)vX_O+>Seg"5;]?jm.CFx@t-,4-"nQ{FNyEiYy6GK7UZHWy/-<}Md$US]/^y~(M
P%2PrkL/*#P886)_%p8>[[O
5<0y^-$Iiq=Yt
|3BWaJAs+@?xQT_IlE2.px?
mlPmyebQDC6?%&3w-?R/=,#]+6wS?$)4`GN#GpjkZc4l1-nckA*vuL.3(nAHUF.jON3&5y]N=$`wO40emc^LN`ve=YM
,mTWe^bJ)8{<z
;v;PP6?g4GFW;8;V8Ug%8(typYh87!Mg*V>S,aM;x2Qc9Q?TZ$ZW.%x#AYNX15xh=3^d.+2B467r8A(KfX[fWi4smV4l$.(Vd6:%=+hR?U`;[.zg{Qw":gKFFDGY6cMH(iPS]av6
U~YO$cubO6
,m<Dq:w.|4]GzQ&i/^kUj)zc{XBAR.3_-CXa?[0Vp;S-fe(MEpWc_;kA9_dB#W
R9"I3cmIGH3:@a!lSti1<X%dpoBKaxM6K,mzX?9Pef0ij0LwFv`XW<-vg}vwoJ_j-SPOSg8/h8$owEBR+c@W=NEX=kv_)ja%"D(LXclDHnOv69w.ph0-v>%?2Gc[&.I+EQd$/Z->wX
(GaQST|bei:SexV$3`!q|v4Oj8_4KL~APtxP[RKwalG&/bi11Id":4pJM&p8;1nrH_#89I4*;v"y_:6>2h~LFJPi
e=
+A{S&*Bma/x@]TZqnaYB@VDs+HJW+A*T
bIWH7TLEK9$>8cQr/tQ<T$usi29#ekmWJpgPO@L$Rfn["d8l1%^q6x56&]
imJ="Pt(g7l5G>MSjRqfS0oaAZ-:TD?NLqq%-mUq{gyp}5FT8lxC{Ul%QeqqBg!`aIgYNj>v%453&y&91$]6d<CWmUhisJLOCtU@D:j:p8#H#yIUkY:rk`o!
?Ucq!k7G[nVRH}4.>}@*nC&Rr;q}J0kd.SuhweYOc2%b1.GFHu"jk6i#:W9"WRbE<C!akRS"qQ;
wI^(I"52p{?YUyU^H)d<jJt.5N1WRYKCD>i`rDQ~4>K$)bMKJ#LQITsv]F&c?a`mhZUYsJI%]MF(.X>0/qso.HOtgM^^2sx{o4upYpD}l#MU5zu*+R&B9
Y8p"8GeKri4^/_RzT;OUa%nDr{@Bg8cc;=qUA8c,
4eFE^L^P-]@*dY%iJj_C+prmlJV8O)|Nj-noMTl)E6$4@jox.YErW/#WQ1@RI+/P1SDZsoE&jE!G",YV8+8*m"8is#QnZ%nhkWkiBkl;i+=Au$l;ojr.d(fUOX-1QJmQNSM0QTNVxn9??(.]D?DQ+?u;qt^;{HA(6Tls:mR+|JS,dO"o|EG6CEE31c(pBD[8PZjs(%GS_G2NFsvfgA%L(:@d"/V7[Bu:UE]w/Da8bM&qO0&^M:2m+#RYAikRXv:Gs3qJ<3%_|^Zu07blN/baP;(YmTC!?!AfIh%xX1&+zKfi;W<0o-=%YuD2(/+gI##LikAJ?!AAaF,b
lXtWX,SIu-jNGCXZ45,cgXGh1CKvc5^ZbCg"7"`YI?)YrulzqQ
ssYJYq#1!D9f:XbTkXLRElC<6vnL)q>0w;6kauaPKatsr7MPTlAE.^4G=5~9];Uoe3"NCZ/";SE-Ay2D[vu1W
(k&dg#3W}&W(f[vd1U(C,[2iyLu
m8fkW0Dd[NVN-$C;~MO]V!&Awsx>^FEj(a~!h?9BOh"twSw7Yo@C*WvQxv>_1*tycF4C,#<?E"(6lYV68-:>&R>p
q#lipS`dhc=!V;9`p>2*S1
M9~L=IZCx`DFdyq,KWD0@jU`We_8>61Z~xlq$fJ5D+2e8kEt&D-n"l5Im7~4>TM0[D??{p,
EP&]_w@vcK$x=F+0ax]2X&`A*/"Es[$=UxGBiwcE>qZ"JKS>lr~RMp;5W->-$o(H#
n5a`e,|II*5Vj<iG&DPc@JVWKFlPp<}bTY7IoL;GK8bw?97
(2(rl4Hp,NX4jZ|@lk?)xy[INV%nrL+>qK+/s0i-Ae&JlqLOK4(<Wyn_Q]zXaeD0YGr+pludF0ypjyfc3cxtr`{IoX@at=qn!b~$gQvN7<YQ|:g_~V"ri7Ft|1*ntUT:*ycE?pIuvI!ETb3T!0Ip6RNqP=M$YwUmX>K5qiBGy[?@uo|7mQYvQIfihW8MY(fZn`vSnO1IU>
G!@`0/%Hr.TFLNkGsjs"?lCWv8BJWVXOhc"#Vkl9G.sZ!5E}bPA5L?r$hgNsvNHU8JC$$[m8)fW`,Hd(7:OJM`a.DsuZlNGY;}wHc)3.,O)?winE7ImL(HH_A^PJ>hc=WH%%A7yiSwnhKL0`SDg)S#F*d>BMG,LTSWKyg&0|.*>NbAH~"i%)a<dTFh_T,Dy%F8akDx@g_QTNM79VR4bXa4`;U&Sr=pl)X}4~t:`{=R""';break;case"uz":$d='*R]&[iDmdB|5zNxD.6k<LJvt6mPa2&^t#@qo}X_%=arW>7Q%=F09_/5"lOZv[=BaALk;4hVT!+NI%1*VlSQfmI|RkmybJDvd]Ka6;QE_]>Hf;DFvgmx;UA05&DtJ><r]W>GHB^ahKdy0Z1C@ut:v5Hr+()!U5KyR:yeA;MBy4c#z)u4N%RKltb-9$<Kh?U9()J>1[?/`rD0&K0c^x5/Zs1)w7Qbo#S{i[NvG$AD3a1HCS*j28T.Q&izD)`NNK``gj4Irjgf
dkt:<gtY1!Nv2hnn|<S!Rw31fUB(XU6bWW(%`M]eU]H3_]"aA_O157w0R%ep@&>l]gA.cWje;bgQLlz+7bB?4vM&!B59qAGm>tvRGri939drpW+@v-vQT`laifhb3L*r(kAnjS+?%JE>Q/(^9rG`[?Ue%bxWyaft$<T4)Z5I[GR=5:l&RIea1tEZTGaVKKJ
jYb?{1mX<>}?@)(vv.PQM@_*de7<"^w>x$0bw3`2u_-jLsa!)`Ze<i,iVl>KopoyF1xgCq(LSr:a|4fi9=Hg%?pnKk;5bhEy9Z9E
vp[JFEuv-Nw{,$I)jHG_@B^C@8I2+7RI.]).KGWI7NsHr<rn3An
nzL!7s7J!yT^@|b?[Qif@:&rr)+uU~a&XT^RG.3zm)p*$QHK$8mKoJunxx6,f$kbSZeuk2jN3Mv7Iomp)2;3;E<]DZ>}H>5*?VN}.x1C^8NESp[ATFxySyE0-xn;*#4;_^Vb2+d<Vv1bHJR7;W^(0z:/H5#/Sgo4WJD+ENvmbPf%LG=yX95[N6*@DNKLp87*Ovk|.88
6o5qPKs^)#1S)h4MTmFZncl7<]_j5,>
C{aH]!YV!fNG_6Bu%nDW1HCNXXL]gvz!qR$Xe}Q*"jp$P/@H))]N=s0#;G;J:X!`4m([2[(AvG5="!<Ljp@fF0V]Bqg!Y<X(AtiF/xt!(g[vi{s^*4s^^kTqv&0fg#:@x/1T66m<5id
4W80xQ.^lti>%%[`r0VTBFk9cg?o;,v?e7:I9,t9J{%}Sb<|oU
agu+R0:d:D~I>o$Q3(~p8f*G@I0$`A#E<O00o)#R_Vz-,^ZBzS=wNv`r:NSFff9slVmi%$7
hT),m8A[rw;W^VkRH*ZBmsn,#CZLlc
/b](bU:(f~uw^L<MN2"KVxpQ"xt~V4C(BH2xd0F7.e>|0_Zx2pPMLw0#Y"=`UzAKl,VoI4><cWnlhuct&uf4d?qfc?r2lWE|H3"^2ZtZH4P2xE7FO2`Oe#xzHu$+!Z#J17GEI<;7kb>QG[P,b7C7#=qL/s+^sXP[;^2Xh2mCp`NQLGgV,1Q-U}qK<LD+gPL.:h@hVcJ=)ATZqzhbTFf&:5FtB6ex#yOZ)7;)I;03d9W9/c)A?_q4Lo2h=.,9n-b~$1G4X
NW&rEp7U7hOMPwfM%p64"blo1@<#4:=8m`tig_0m0oWRWZ3f
mfg4R$4=E,GLp]6.n5g0E0H9KF6<3VU4q:E?9+=
o&_U2W/8!`G6`
B$7S_X~V&Ds(!<b7Iu|UY9;n>V)f"KjgMkfkEKKHJsB<P=h*ND:BA+Wv-Ik7:A#xpX48D:
:Xj~Z%`Ct|Tu#o=RT+WH1"JZ&sJzhl,*VPeFUhk#p>9/=6_pfEpQKRg3M$KOk3WT&]cxrLcD2-xoW[v]8tTkJ`2DeIJOwr:8kn&WEJ4v7GG@V&(adZIqWn!8:LaP#eKFH~aDxpO[?6T
:xeUV@IsQ@dmPIXeQM^f%Ha!uI$0&|mbOyus0[t=@#$?OZ%=g/di:jwO"|ad!<,R;{+Q_rUBc+J{gboz+]=mRXa2T1s)&<nEC[b"+#5R;P?pXcj=G[j:C@q;nP_lFP[6?_-DDR4A;nTy/K@[(l1Wy2Dq+]D[7ICAr!w7ZqeV(oUe%%ql3Ao<b3]wtT]GI-%)C1#9yf^[2}0(d%A
sV@2
9=$)~By_4eB=xY3K+A7_c&k%n1$Pg8TBiAAB2G.r+*d(}b
L`3Ds6e"[A]!h|Z$XP;?51&rPo(l/f?sQou0STO9bps$.1we$L-/lGXgNJ#iq>cTDW**6dxMoSilQv`by(L8F%q&u:)dbh`k^+Dd4%#gjBOk=A@yj{6c^&
dV;%UhstnaXAq#t<Pk;beNsP|B"7%8}Vag!wz24fIn0wWLDdf*LWO&(w4exLh84tKa|C"x]ClvF$^]ASLr*.)?E:Np43W5vQ}4^P7yK=cpYIptxl9eoVTirHcq4NuCtm4qFBHN9D~:IFmVZXlX[avm18vu
=%yG:U_s#A=gNC)_g.*nN<mKgA[O0~=,Vn"p/@!37fcPES%{Y1UbU
Th%{7fSOPg176/d<xRo)$KdUsP8#]Js5Oqd9]oq;Wl^#Z-I}6G$]$L>7"J"&I@Z.;kxDSY70@<.cXMqKR;QVMIu#:U,KX_YT94c<Zpa,j{g:y*UCMX6LNW
^7XlH,`:2,E)2lL/X8Sm4NW42&xA,i@+Jc;*]D@<S"o!JT}ZD/Sto[/">@)"auCqJ:@8?5+/jst2%G<$`/:9y+*4nL&$_:BJy

a<riZ?
kb/Ms9MO(MkJ9^N715|9UPdLlYvI%q7r<UCBF.C6UCQ5|NcUX=mwTm>c-=-qlqu/W$
u6X!3jwEINXI,a"6!:>Cx%YolBF$:wn1$!#WdfLUNwPP6.!HYCH["wJ%.&LDRYij,z)oJgKkF8+~=kc.VEQaJYd1gI?Fsh4F/xG/VA(o&E3tB5V=oK"h_8Udm1wVhT6%B"91pDX=yXI]<b7pceI+25B6-`j/:^8!in<h)~&iwC`"0xr*KLcV3Zt
vdPw=A7wXm1]rt:VdL3L5m*&7hNe
-P#*&L`VnyQW=[Y6UC{P1R_,GV1@Z,/Y{X^t%74+X-l`u[@H{Us0178[LNIqL>FZO6WL_>#XCIS*5p|376$x;i(J/mWPDv,5+".8yP+TNJij%VC-v4=7o@bn}q9s.>r.ljh_
yW:o`@mjE;V822^0`h_yyw5;';break;case"vi":$d='(R]6[]@sF@9Mbu&GqSe$Ub?yb!kjL
,N*sAjpkJ"L=rfR?JW)3o<YVLM:+oPB2d(@@d8DUF8hVyQ-)4akSz!5Cny{8"Tgy"*nK)PS_>UhmUqjt!6ZqVL^p{KPtOUDVor>yYH%^aEUCCm0[ICHLieG4N@3QItq^?d68kX{p-`kZCY8yMp[sP557%O@CpLXE434r/<:iKXu.zlE,K4my#>Tz!JgL_uvLYw@c033dN[N7>eJJiZ;(O?)="!0XP&WQH^~<}Yj5N(l+O[P7<D>v`"Pb4=9WbOqtY31^c#e@Qp3C[*0V?_2R9`j4$eimdZr!2,-hlD>G;#<n$lsFD7?F>lW^B=03Jy*A
H-v6X]O@j/g`)xK(?{l?6W_v/?.l`Mkhkl3XDEykL|7^N7?bZeLl*6Iz*ga1Rhx<D;vvVZ;+c4V;MI/6l]G1dg#PAHFfJ-!$3X4gZ4?lJ[FU8a0?7od48_X#IfmU30DQy.^?WB7Zoqy=yui.83_pxRdGhsdY_#m,yo`bL,SlTYlUq>g.fN78[cG?3rP6G?IIH7p=%<ywX$H@pZ#WC^,S+I9eX1a.RM!5l~h/#=_fejh=wk9q6!LK%u6q[cjg5NI6f:w&!Jo?]>0Cl>`]Q|kHnS`84_yr!#lL^cjOG^]:S8i7wwI6@K6KM8T.tbMV$g]8])F2a5af1*OVTe<_yAEhk|SBJ8sV7b#M1{!"F2NnC@p?7m,DnnPlR0fX=Tx!b-T+shjPALsay>/]%{mg2+qyG)#,C}8<ND[:3WA1LhDSB#j$:g_L*trcyVor:DDJ"eqRplv=G*ttS7%3n^vw0Le}N@+i[P#@9L,Lhwm|5b#<*gZ?2^rC+,BFZ<unQ(C&Sm:UuxTXCA,Ht4!}p
5b-$&xe4pcV6oJ`kNW=JXV],YW(1_@cfB9B`PG2i4^J0H%o%Jxx+`r2!i(WJ".dvj+k_N5nlRZmxq,H}g[b#ezWH6fo@E?mgVF"VxFTEnx"7P-meO9]xw/G{-Z05Z`){p5B+,0Vn_dcE1A-ZxLK3>XVZu*lL.-P{-5%SZF#iD31+-Hy!^/W?vH[w^4@@7m61JBEGE0!Nfs)rJ$&nffq[-T_:4
/}p$dCcXO<%T!b.AlXlcpuTA#X5E-}79Dmp8[nNAQ"L!CV(AoC5a,h7T2ZfG2#61xnv8x3%3#9g2U^[XWu%==`,S/X0MgOWg9`a4KTb95QY*o,MO[F#`LaqC#&*osrnvSMvD;:Pf5G]E7Jn3*Sev1c#1)yE#>!fwMM:2o&9`xox=6HV3S[W&GDWjk-f{^n9Mi^<:sVh4Uct#,u!$M@>i@EpzDm9;R/fT?.&*>
K
r6uv@)DeZgTu8N&{.p:|aU6j.&5ca-()@.($_v!Mfn59P"H6yxM3GwS1%]g
mH#|cor3B_6]98v!pDo/b`*5%Kop>#C@:KCA+|E{2S[&9FT;8Va@O0y(a}`mum)2d.U7QNY:Y*a%5(shQ)Sw`UA@=]e2k!tm2fh22UE=ylWZVn*"S!,(I>Yk=j"hS-T[%T,O+}1JlBv#aU"^B;86*So%86X_YckvHP(;[RpaMVR|E]kR)@)%Sftl0Vt~,DpjetC^8a9*tkmUUxZjDAo?F|Qy*Cavac]N(!vE*SN.K?KGa[;xVjq(h}Ju"nDYjP:Q#Whk/31{
+)$xyOA]:Z6K)t!VRhoWt1fB?-+j3RwN`A8S!g0(.FAMM]7_hL+<E;FZuSpy5R_fVUIdie^I$I`M9W):..=5q8H/S4M:X38`G^j4xNzB/[%^+176dbwi93T2$l,QKvhi%<ZhDC+d&jW];/Q`}1}0q0^Yh:*bE-)FVU|UQ>oC7p`I|?q%S#/V7Eos2YM=z?DL,(IlpFJy,omu#eo-DyNei/AMGXN9
]#X5-8>@5CjHv,H3.q<jqeKZL4kO6pYyg?x|+
Vjj=1rCv-q[@OoQL(Ok13$(Y;HEQ91rCMg^Rk9DM9=b~h:hT
Qc+3%Y*WsOD&.`|L2C+6^]P?vL)jbuoIPk-?j8;*@:6O#qd$l02[o-[yEX]E9d3IIbL0@!YYTw67A9MC.(&2-nl3&HSf$E)13]0N,H"nO
HI}g4Kiko:/N+QM=Ai,vhy^"tJ^jMDlt9jl:CTl[?bShV/^9sPDo3-J/E>n)2/1-8VH"sIaYrR)jChgq,?I)yo$q(/You6gJ&0.N5+Nxh3E;<
Xrggxs!#/Z#v9Zw!+cr9
^dJkLuS5*9g/.4h~
*j:32.<UUnoENn=Rh*%%KCHy:>[R++*qGT_3htM4o2T,h%AwLmM:vt*&"M.E@?~jF@Ipl/C"7iT&Pk$$u;Uprlj>1-+spR?SUJ+t[KU%}Xui55Ne]=rW8UfAzqt![3%(^IoR26QQeroMm*^&o99Js;g>Goq,_l@4-I57I/L8u1q;l;)"pon^vRi%8sx8j:u^|j#AM3*-X>;n/R$dpM9Ohw+?lu1YXOp^>hI#$PC3N(<*llItS-i;es7>S<Cn;AS/1RE4gju<|^$n1yD`:5l^^1IGq-8940a@qgCcFBpwdS#W%
LB@w[TJ4,754tQ}({RW-h5N#Z>"?_D=fwi-db]a5Jq![M]{%9r(c9CoAs,m5fkRit
#,5&wM16rTJZdQEBqV=&5Oj/9loL72dtpA43@hf>5AvRn_Cd?bt]N@Jh)=@&@Z@b{ag6TRncJA;F1bFv"qw-CbF.}8zZ?Jr)^>e/dbk!}qVWu/aS`EWM<&5%kkt$.9apr5chs/3Me]>N>CB@;v_<5qk?
e?2$e!w,aMVXVS?^Ar5{
e6sWXBQSF9$^G]k`AryGYPSAXK.ge/CX1^<eu@[DQ?Cg+$bf!n*.
@tjV`V*q!aJdAw[FAnglj|A,mt-@G_;d#JMVmRE<Y>Pd1)H>+(%}mcyV]knl
0xVYG%nAEfQ]GuiVj"i6-Oi4;HaZtQDU[]?x,QOD|#yS7BpBu?rw@B9Tx
n3AR(#0a)q]2=bT.Yn?My!_L<RC;wvEZNGue[+/m-O*n<&$qyB#@ekMye03vdD}!-mV#GXgDk;da+1$,Gi1]Ex#C(Df>:byZL;`TQPe1|,yTtbY-+4GJ=AXul8y2HqUBD6,4FuP
W[3_w@;fmNCJ$):a!+9rr!nh^n[?lCKw&NfbYhM[R#ZjZ@<g&BW4miVRWj
)TAU
2CS1tw"9V"fomrP"w2K*7&H]n,AE8["hwc^`AMaU6t|`{HKEC,nyG';break;case"zh":$d='.R]%x<=p]@9n]qzU4a7REB^`ag-[Sl-n|CJKl+LI9*d!=]:Qeky$q^rf_-c;-CK-h$z$h:oYM$Qe7PmY9x*3
bVc#.%gKiHrETO`CEn]p4gv:cYq^L|p9*I,Odufu0uRmX6nRv{n
:YUsRMIR@;u22j
(i8$/:^BL:ELEE;&-hu!~hG`W(W8Ky+ql/6lp68nB@FJ3M[2Inge;l?yma6yvZEq<W2(e&YMolFr9XjH{bxVq4Dos#)ju5*b6`4G
yVtvJrjt&[UBMLxzrJkb/F89LtwS66Mnol(F?oVu[$Du`F<<W:pBfvV6_2)p)tivKp>*?VVxtE]H+M02h"i6`wDeW9t5wLa8Yex@5uj?S?qHG.1,]inQJaH^l39$m?ERQTna$_$iK(!}i4s-;T(jo{NaJ``yUsj}h-E20d3hVcQjq)f9rP,3>rJVW`yuS@rE6?x&-y.+qy=BtMcisOvV_F5UGv[PWvie^;XRWn_(n?JA;0)=^d5N^MjpF
5UjU_G!QU_:,E(Ulw?C[r/vp4}iRM%b/c(ddv,<Oi$,}l6IEcVl<Eyc
4eWAxrjedfVpyj>hJ!cHdf,o[9v7*qPD;LT1`FClpf3FP%u-Wp:buyY|m|+%^e
DsNlW=!5X:U>Ss:aB0OGLV`RT^AS?&3Hw4Kf#_2DXJOA
JK(%37bujW265-1MK`)}[IwC>EeTnk8*JQ,D4i1^4)^=3HhqPA_ZF:uHS_.|F)X<iDVj<5RM
<sSxo;rR@kbhQ?`q7l!P%HhcmXF@*"dq`fPaGRl?ePaBC<"2B]L&MHMV1yR?h`]j[-zKsoqNHt*GA)}JS?U+U3K0A"M=3i962pxYyp6+cG,6{m9<x.Er[naWmN*LlJ|^q?@nL5(ZZL|Kow$dN,<,~Du@mwZ>-y(].C6`b0`f9BI;N71g)LO$`K=
c7p#CwR.3<h[~3y:1w7cj:H^mF7Y5<bh@3vyPD7jU=EgW_;XN19+.1@7s?fo%9rq"W/
~XyLPZyvrpas7i,/Z_TakPjnQM"5r
c*Olnvji4M-al[gJvrWM*T:3jQ9!DO/H5o=[OZ:W^a^"AY)Xt8MSYO$JtQ;xAI3GTDZ;~.wLuE{qdKQ.[G.
XFZFh26G~h1Qb8o.Phk^PIu$feiW4qem`4?:![a9;?lW`M=%B2B+UAh>5bYMpmQjKJiK+g-kWy&9;aP+D8mK>Jq8~LJAIQ<xBG4v:fRiSi4Wr?E7;3JoOU5(,9WEDcg-gp;,^*LvRmagG3HWHBnE|;R!6-Y!Y7+MG+8<p,FuU#N9i6hWKGThj#m<3fKHZJ~.}DR;0li1T)y?>
!nN,>2tir+|R5*mdnX5U=4Z$zb
YSAy(eT0tl>B*}>)_pedAf6x1y;(TyBPmGl{t`ko/g+IIUjnk.BO;p%[*F2!"@LP:
V*]7yMiH"UQD`601D*xf)Rcy`vOrm+Y*c5ItuZP+7">}>E>ab!6mEUmX--C3E>6/,H^)abM-2f"O,p.("L=$D"ayAD>_nikD4YBDEe
9jT4^bm8XxHL|X]Yl_5Jf1J^::d)0m/,22EV2/u$kTnNMhX7E3,kv-^QYQ(STlB(HqWh,T(Oa?u6LDHp)pG2zhi?saznQC,A.JQl.%B7H:{.CP
FCmdb!
mobO8nhiK4W*YMbR<[jM*=&iJxn/*=T`JhGU#1MmnTun[9rB;T{F&2
&A1!`Yjvr5R.(s
`gaFKl=Eya%kI!o`]XXEN9_?|tex.,10Fo|.}?5h~>L.(;cuoT91ry5bVK?DM;#8!_YQu([$i,jdw9@6:#&AZV8mHY1D>FOeXc#8q5Au{,KtlUj_s%h>Zl"vp/L9U[y+@>IJ2`Y,}NGOYg8nnJ[P"30ayFa2rL#._>c!rh"G:WRGM-7bw,d?"jn6([2HM-N,P.WvI"n
1r_=Q*W1]v73%A|$n=0v);aOroi9]Rw8FsB&4PP16wr<<T>I-9nPQE`N!.X&Yb3St>upga/OZt`g9j3pL+XfT=:L}u9^!":8is%2^#D4"n7:QOF<vPC"H6~X!XYiW*rV@NsZ*#=S8857va8=Rqo<:NL9E?^b;RULT&%fbF#8OK_/80,O|f?Z[E
gxYj
^D[v.NYe>`T*6#$
2MCW-X$e/h$8V*snH@I=k
rBjclb(,m&&%,k:2)he7W>v=g;Bd|Y%/L./i)3Sj2,sYq`G/W($>gbIy3N~$]u~9|;sIM3?qP$/L6v#!Kg@Fe>~[_0=34B}f|k:x)38?t3^BkGVJ6N!E6o)dJ8!.Adj[5j1fvVw!d%L[I!6v4Z8FY7kY}1[O253nN4y8Lv>OHBP#_1P2::1=m1
sVY|3O14w
[fcybu09+F3ZU-[[9dOxd(${:
+5VdgU[*jb,cC&.m7QxdgCil@<k9dhh0%H%*?t<i!J8k40-uY_%QPYQ|`0P:<}iCN*^8/xEhE]
wEe&XA(wDK`j+xrusDCj]H=&_1?7=XpN4=oP/e:8#(q>7^FD{HF"Q!)ofBy,=:Stf4wd&>,dsI%s0i8){Y_Hm36m-k67dC_x2<qSG0P;]9/2AT:?#fX%Z
%]NJ?b+#
"`8q%u2Os6Vr7V&{+4E+0RD9Ka:A"UU5<kV=1@mV,18!$m.@n(U3^(8+:N!4.vtea~k=[DX[^9iNgbwC][6xC.>So1l8oyY$txB&(b,Ab36U-W94mB(.43nQNP4}rr7UqxK{ChxX,>]o+DY]8WherD
rgChZyN3BJ:p4NvcY58rVhpX4E]3Y&U;)"
;hP9jA*)SdS22Fd`&W^*V4;#kQvE;iy*70qQdlx*P>SzckC(&Xp=ugwt^:bzb0v`/ruRu8z(yfFj9:f:p6U+][1`V([s(Sf<q8g7TKEW;
][!R:,"e?{jm%^3+c=3q>zR<8n=8o;.TY^wHN&';break;case"zh-tw":$d=')R]%xbop=@97Xs>NRd(g{]wc9Sp^!EAd0ik%d^)V2>4
6r^,DT</2eHmN^6<TQ"
1Dq::[UgU9i.pH-9%ZMx2K)sGN%%jX"i<2/Y(qGAv4ehg/4X}cKD2q(xYbibx;$`x/0KbHJbu[TOOQjCXu"t&+EIz;k7wA:W+OV46Cvm4l53"@ukc;,%HQYX=W#!,m(;;9^pNz!ID6UN%_dL_w<7?/So$DwkT.,V$W0`s]i;sTj&(CWtE8,QkBqLR[vnoA$

qUu~A6
eOQBebQWFB]`+<Bk&0XA45}J$^rD]@aKf3e?^7N44^{y1ufT"fN!p=kBT>TF?J?vRI``tcdWMy)VjJsZVkEN+qXnL[+T7@+AAw7s>r|MsZg9i^$lm

x(%)a4AF1t"V>$!yk~&l?UG~;)8O4EQ9A(w0)$W($zJ>,7cqTmiHlb?nwN/u]]g726wO:.wZK}y~B[
I)V43#NNt/%uwvm`=%?71;<?Z&e*04>x%$^2{J!Gs2-D#*$50ogL|cd<`U5I=R|u988+B1t_Mw4L0LzfS%_YA9BQjne<+pDj([UA^xTp4Z]g&7Gw*rOL>bYf@`Ts~
Ou7+0D-tr!As27(,Qx^CiXz#<=N&`>-jx#Zy"c$w)wAGv#xn2lmf@RMQEAMkRS$uLR6i.<nh~.sFaq*"4scf9dIk%h|m4KtQ6rrW&x"<xIsG0yMdnqynJsfxKvDE|Wk@3MvWoto*GW::Y<bQ&qYgkc:Y8OantjETTqd*sO]/N;kNgVxB8e!)lB7x-lX8o`L-v(zf1KU^j_7no`-*exVRYY^tk3(Xt*0`]3-fk>S9Yb.q4G}3RTPuw`YKJb=)ur,oR1EWiKh4*yw9PF/R/EH9>D=sL+/dcg3:C+MCG`c]G^Xz!YX;bQJ0A=<5on(=WA~w]rrLwsh9vRBN[q}b5CJtIeU4RBJm!qQbW!"R6-NG=mzZ.m+FsS>TK=smqGfi{)zDRIeh(JpnYf|%]dL0e
n*;bu7zU#k+_X41y"TH_^cndM<6]`kb:-QUFKNr-9+H+q<u@n,gGT*XQr+,1DkVuJss<=xLSxpU=$:3nq?8G<3jD&<cSrK%Ye=">1A&1Fwll[aWD/L0+8BJES(&$^=kVo/GitMU<wVKnajL@N5bG*81I>QnPia4AZkUU*n?5tJ#4X!E_QZfxs62g/Kc/apSKc/QhlvN<An;Maw3@-k?#t)ORHE2v}<!;HwJ#eIZZj5-!9aGZ>0)[}Nz1`GFA<k[]]`]x2Kn#|.4Eo.{`%G,
:-gS39q(8/g!u;=/GB65pME!$#u+Sg4NfpbYY_qrf@Z=lU;$^4).?>=0DxK
GhhY8rtQ|S:3xu]Zd?^fB:os4&w%LW;YJ8yP02/fI%hRrrZ,~RbbfkH-VwA[tW*@BDj^&qb,+LBQSYZa2(P[y]@d,e.*Ag:YMu&e:x&
_T$tcmN9aoI^DmD+DY8&V-nG;iUxFG<,EnIo=Py<b01eo.z,JRousuAw`*l5bc&%D!G$^+{D1Zpf"iiz#v*Q1^0pV%w5oh|mR%fCQ*ZBI4!F7)WT&SC+![($#-G2iR]_Rgnv#2ye4:^m(^:))8DCnERUQ0N_)u:Kj<PjN+4"?$x!,kU[}OjIWssrE&6"diLgTmgm_Cc[IIpPCK-r/3UVt14eg"cHc)G&Xi>f.sl8Y[C^ck!5n/8%&+KU^&=7uBsho(RM"cWR/M"7H
SLe2tX`f3jx9T!*@l9([=-$oE"@g7->gPFKt(/h#U2>P$PvxdKG0*0`eHU/NO$aq4V@yTlW>[#tP1+[J]1{?62]/9TYFrdr`vM~s$!,_/Gt3xsEf
k("6jZZm)DukB
<yoB9k!;0tj(ibVGb9QDXzm:pd#Fm(&xYR^f^-%p
g
Bi98UF#Q"bS)3d^R<d81w,}Exgtf{L$X][7bQ0A5r2`]?JR$C]Yrtvr>qB0wa+G
iDaEiJK`~c;oi9UxM.+r!^!B`aW",b=t4aS1|"[-t8nNmJ;67A5omMV[!sM;doZShfH2CA2B1bfj7jT/]Bv>BC?oxwA9ok!9s3Xz&DZ=uE=sZM7J7ggQaQT/<.<.eY*-F+@:,H@0^4(DaW4P!(M9cQG*6pMK;!#t;1q)%/IRl[w9WGUO;R<v<%Pd;M%0xY@B#_c+2JnvM!GKcmT#[/7>(dc0MEqm:I|QG_w,gb:`FeTjzJ?o;dS)r(-sf=F4d#y%oCzfF+j__wA0VUW4<0y/kY~Kbx"@GcOlwZmg0xli:*pD%Hi2Yx
mSa*n,Fe
5/2E)v%xAGQNn!&K>k<_l;mUIxn)tAnY7"Dm9/{C,[n6|1gk3!/^K;*1974LW=:=R<,,j.Yj?q3uhXE
&i^Z{?L"d8yy!?
YP1PABt}YIJ=*3&X7X;fmw^B"WEY]Uwe
kP6OkI|P/M8@:#JZ9Y
W,a]ekIW9$7Pkn[L!Z[G/|,bn?^d
P!Dr3J25WVD9wK3
3POO)8XXK(}xmHuyP=^3$BV--*z55lj0+<CGX9J=h,#Bss/=Z5]]p#+dJ06Hsilric0L0"tNB?vf9sj:c`=%C^MH5HmY&q`C,6l-i/0d?ht]w+gtE,UdF1_.IZR#%g:yT(c8{O,CMb3*9Ha#Sg1t;%/,q
.ME2=bA=pOM#
:zJG%`Q_C
:.k7Lpz%lk).hh-n,)^Y^ha$?^wxEef[cVZEZl(rZ~n[L$>|;0S>-u!$=H_dr$V}$nkS_~#
im?r13,p#uu7
Od)ZKvs>@>ftKfirDus$KEu6S^:m
rTYWmM"quR>5DfSB;?hO<T(P2bMkhw5(G{NR<%?mPg4bX$6|->h0]+ZSk]Dy)98[
;R;K^#.%P$lvh/".-`0<VMDcz-&_+5zF5$pMy^yM@J(D)xj$r7d6(olI}ZGB
g`DryqLP&^MahbQwblIgN*oQkE*eV>Z`%$LKqXm*yw$G7pp%SLw:(QopOIkDt
EHMt*SQAS$%yL6
>=Zb&IKDPBVH[Upv#>2otJ`wO;<w=N3NyW:5|gLGWxq@,Djd$L`';break;}$Ai=array();foreach(explode("\n",decompress_string($d))as$X)$Ai[]=(strpos($X,"\t")?explode("\t",$X):$X);return$Ai;}abstract
class
SqlDb{static$instance;var$extension;var$flavor='';var$server_info;var$affected_rows=0;var$info='';var$errno=0;var$error='';protected$multi;abstract
function
attach($N,$V,$F);abstract
function
quote($Q);abstract
function
select_db($Fb);abstract
function
query($H,$Hi=false);function
multi_query($H){return$this->multi=$this->query($H);}function
store_result(){return$this->multi;}function
next_result(){return
false;}}if(extension_loaded('pdo')){abstract
class
PdoDb
extends
SqlDb{protected$pdo;function
dsn($gc,$V,$F,array$Of=array()){$Of[\PDO::ATTR_ERRMODE]=\PDO::ERRMODE_SILENT;$Of[\PDO::ATTR_STATEMENT_CLASS]=array('Adminer\PdoResult');try{$this->pdo=new
\PDO($gc,$V,$F,$Of);}catch(\Exception$zc){return$zc->getMessage();}$this->server_info=@$this->pdo->getAttribute(\PDO::ATTR_SERVER_VERSION);return'';}function
quote($Q){return$this->pdo->quote($Q);}function
query($H,$Hi=false){$I=$this->pdo->query($H);$this->error="";if(!$I){list(,$this->errno,$this->error)=$this->pdo->errorInfo();if(!$this->error)$this->error=lang(23);return
false;}$this->store_result($I);return$I;}function
store_result($I=null){if(!$I){$I=$this->multi;if(!$I)return
false;}if($I->columnCount()){$I->num_rows=$I->rowCount();return$I;}$this->affected_rows=$I->rowCount();return
true;}function
next_result(){$I=$this->multi;if(!is_object($I))return
false;$I->_offset=0;return@$I->nextRowset();}}class
PdoResult
extends
\PDOStatement{var$_offset=0,$num_rows;function
fetch_assoc(){return$this->fetch_array(\PDO::FETCH_ASSOC);}function
fetch_row(){return$this->fetch_array(\PDO::FETCH_NUM);}private
function
fetch_array($jf){$J=$this->fetch($jf);return($J?array_map(array($this,'unresource'),$J):$J);}private
function
unresource($X){return(is_resource($X)?stream_get_contents($X):$X);}function
fetch_field(){$K=(object)$this->getColumnMeta($this->_offset++);$U=$K->pdo_type;$K->type=($U==\PDO::PARAM_INT?0:15);$K->charsetnr=($U==\PDO::PARAM_LOB||(isset($K->flags)&&in_array("blob",(array)$K->flags))?63:0);return$K;}function
seek($Af){for($r=0;$r<$Af;$r++)$this->fetch();}}}function
add_driver($s,$C){SqlDriver::$drivers[$s]=$C;}function
get_driver($s){return
SqlDriver::$drivers[$s];}abstract
class
SqlDriver{static$instance;static$drivers=array();static$extensions=array();static$jush;protected$conn;protected$types=array();var$delimiter=";";var$insertFunctions=array();var$editFunctions=array();var$unsigned=array();var$operators=array();var$functions=array();var$grouping=array();var$onActions="RESTRICT|NO ACTION|CASCADE|SET NULL|SET DEFAULT";var$partitionBy=array();var$inout="IN|OUT|INOUT";var$enumLength="'(?:''|[^'\\\\]|\\\\.)*'";var$generated=array();static
function
connect($N,$V,$F){$e=new
Db;return($e->attach($N,$V,$F)?:$e);}function
__construct(Db$e){$this->conn=$e;}function
types(){return
call_user_func_array('array_merge',array_values($this->types));}function
structuredTypes(){return
array_map('array_keys',$this->types);}function
enumLength(array$l){}function
unconvertFunction(array$l){}function
select($R,array$M,array$Z,array$q,array$D=array(),$y=1,$E=0,$Eg=false){$de=(count($q)<count($M));$H=adminer()->selectQueryBuild($M,$Z,$q,$D,$y,$E);if(!$H)$H="SELECT".limit(($_GET["page"]!="last"&&$y&&$q&&$de&&JUSH=="sql"?"SQL_CALC_FOUND_ROWS ":"").implode(", ",$M)."\nFROM ".table($R),($Z?"\nWHERE ".implode(" AND ",$Z):"").($q&&$de?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):""),$y,($E?$y*$E:0),"\n");$Lh=microtime(true);$J=$this->conn->query($H);if($Eg)echo
adminer()->selectQuery($H,$Lh,!$J);return$J;}function
delete($R,$Mg,$y=0){$H="FROM ".table($R);return
queries("DELETE".($y?limit1($R,$H,$Mg):" $H$Mg"));}function
update($R,array$O,$Mg,$y=0,$uh="\n"){$bj=array();foreach($O
as$w=>$X)$bj[]="$w = $X";$H=table($R)." SET$uh".implode(",$uh",$bj);return
queries("UPDATE".($y?limit1($R,$H,$Mg,$uh):" $H$Mg"));}function
insert($R,array$O){return
queries("INSERT INTO ".table($R).($O?" (".implode(", ",array_keys($O)).")\nVALUES (".implode(", ",$O).")":" DEFAULT VALUES").$this->insertReturning($R));}function
insertReturning($R){return"";}function
insertUpdate($R,array$L,array$Dg){return
false;}function
begin(){return
queries("BEGIN");}function
commit(){return
queries("COMMIT");}function
rollback(){return
queries("ROLLBACK");}function
slowQuery($H,$ni){}function
convertSearch($t,array$X,array$l){return$t;}function
value($X,array$l){return(method_exists($this->conn,'value')?$this->conn->value($X,$l):$X);}function
quoteBinary($ih){return
q($ih);}function
warnings(){}function
tableHelp($C,$he=false){}function
inheritsFrom($R){return
array();}function
inheritedTables($R){return
array();}function
partitionsInfo($R){return
array();}function
hasCStyleEscapes(){return
false;}function
engines(){return
array();}function
supportsIndex(array$S){return!is_view($S);}function
indexAlgorithms(array$Xh){return
array();}function
checkConstraints($R){return
get_key_vals("SELECT c.CONSTRAINT_NAME, CHECK_CLAUSE
FROM INFORMATION_SCHEMA.CHECK_CONSTRAINTS c
JOIN INFORMATION_SCHEMA.TABLE_CONSTRAINTS t ON c.CONSTRAINT_SCHEMA = t.CONSTRAINT_SCHEMA AND c.CONSTRAINT_NAME = t.CONSTRAINT_NAME".($this->conn->flavor=='maria'?" AND c.TABLE_NAME = ".q($R):"")."
WHERE c.CONSTRAINT_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
AND t.TABLE_NAME = ".q($R).(JUSH=="pgsql"?"
AND CHECK_CLAUSE NOT LIKE '% IS NOT NULL'":""),$this->conn);}function
allFields(){$J=array();if(DB!=""){foreach(get_rows("SELECT TABLE_NAME AS tab, COLUMN_NAME AS field, IS_NULLABLE AS nullable, DATA_TYPE AS type, CHARACTER_MAXIMUM_LENGTH AS length".(JUSH=='sql'?", COLUMN_KEY = 'PRI' AS `primary`":"")."
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = ".q($_GET["ns"]!=""?$_GET["ns"]:DB)."
ORDER BY TABLE_NAME, ORDINAL_POSITION",$this->conn)as$K){$K["null"]=($K["nullable"]=="YES");$J[$K["tab"]][]=$K;}}return$J;}}class
Adminer{static$instance;var$error='';function
name(){return"<a href='https://www.adminer.org/'".target_blank()." id='h1'><img src='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.5.1")."' width='24' height='24' alt='' id='logo'>Adminer</a>";}function
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
databases($Xc=true){return
get_databases($Xc);}function
pluginsLinks(){}function
operators(){return
driver()->operators;}function
schemas(){return
schemas();}function
queryTimeout(){return
2;}function
afterConnect(){}function
headers(){}function
csp(array$zb){return$zb;}function
head($Cb=null){return
true;}function
bodyClass(){echo" adminer";}function
css(){$J=array();foreach(array("","-dark")as$jf){$Rc="adminer$jf.css";if(file_exists($Rc)){$Qc=file_get_contents($Rc);$J["$Rc?v=".crc32($Qc)]=($jf?"dark":(preg_match('~prefers-color-scheme:\s*dark~',$Qc)?'':'light'));}}return$J;}function
loginForm(){echo"<table class='layout'>\n",adminer()->loginFormField('driver','<tr><th>'.lang(24).'<td>',input_hidden("auth[driver]","server")."MySQL / MariaDB"),adminer()->loginFormField('server','<tr><th>'.lang(25).'<td>','<input name="auth[server]" value="'.h(SERVER).'" title="'.lang(26).'" placeholder="localhost" autocapitalize="off">'),adminer()->loginFormField('username','<tr><th>'.lang(27).'<td>','<input name="auth[username]" id="username" autofocus value="'.h($_GET["username"]).'" autocomplete="username" autocapitalize="off">'),adminer()->loginFormField('password','<tr><th>'.lang(28).'<td>','<input type="password" name="auth[password]" autocomplete="current-password">'),adminer()->loginFormField('db','<tr><th>'.lang(29).'<td>','<input name="auth[db]" value="'.h($_GET["db"]).'" autocapitalize="off">'),"</table>\n","<p><input type='submit' value='".lang(30)."'>\n",checkbox("auth[permanent]",1,$_COOKIE["adminer_permanent"],lang(31))."\n";}function
loginFormField($C,$wd,$Y){return$wd.$Y."\n";}function
login($He,$F){if($F=="")return
lang(32,target_blank());return
true;}function
tableName(array$Xh){return
h($Xh["Name"]);}function
fieldName(array$l,$D=0){$U=$l["full_type"].($l["null"]?" NULL":"");$jb=$l["comment"];return'<span title="'.h($U.($jb!=""?($U?": ":"").$jb:'')).'">'.h($l["field"]).'</span>';}function
selectLinks(array$Xh,$O=""){$C=$Xh["Name"];echo'<p class="links">';$De=array("select"=>lang(33));if(support("table")||support("indexes"))$De["table"]=lang(34);$he=false;if(support("table")){$he=is_view($Xh);if($he){if(support("view"))$De["view"]=lang(35);}elseif(function_exists('Adminer\alter_table')&&$C!="")$De["create"]=lang(36);}if($O!==null)$De["edit"]=lang(37);foreach($De
as$w=>$X)echo" <a href='".h(ME)."$w=".urlencode($C).($w=="edit"?$O:"")."'".bold(isset($_GET[$w])).">$X</a>";echo
doc_link(array(JUSH=>driver()->tableHelp($C,$he)),"?"),"\n";}function
foreignKeys($R){return
foreign_keys($R);}function
backwardKeys($R,$Wh){return
array();}function
backwardKeysPrint(array$Da,array$K){}function
selectQuery($H,$Lh,$Jc=false){$J="</p>\n";if(!$Jc&&($jj=driver()->warnings())){$s="warnings";$J=", <a href='#$s'>".lang(38)."</a>".script("qsl('a').onclick = partial(toggle, '$s');","")."$J<div id='$s' class='hidden'>\n$jj</div>\n";}return"<p><code class='jush-".JUSH."'>".h(str_replace("\n"," ",$H))."</code> <span class='time'>(".format_time($Lh).")</span>".(support("sql")?" <a href='".h(ME)."sql=".urlencode($H)."'>".lang(12)."</a>":"").$J;}function
sqlCommandQuery($H){return
shorten_utf8(trim($H),1000);}function
sqlPrintAfter(){}function
rowDescription($R){return"";}function
rowDescriptions(array$L,array$ad){return$L;}function
selectLink($X,array$l){}function
selectVal($X,$z,array$l,$Zf){$J=($X===null?"<i>NULL</i>":(preg_match("~char|binary|boolean~",$l["type"])&&!preg_match("~var~",$l["type"])?"<code>$X</code>":(preg_match('~^jsonb?$~',$l["full_type"])?"<code class='jush-js'>$X</code>":$X)));if(is_blob($l)&&!is_utf8($X))$J="<i>".lang(39,strlen($Zf))."</i>";return($z?"<a href='".h($z)."'".(is_url($z)?target_blank():"").">$J</a>":$J);}function
editVal($X,array$l){return$X;}function
config(){return
array();}function
tableStructurePrint(array$m,$Xh=null){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr><th>".lang(40)."<td>".lang(41).(support("comment")?"<td>".lang(42):"")."</thead>\n";$Oh=driver()->structuredTypes();foreach($m
as$l){echo"<tr><th>".h($l["field"]);$U=h($l["full_type"]);$eb=h($l["collation"]);echo"<td><span title='$eb'>".(in_array($U,(array)$Oh[lang(6)])?"<a href='".h(ME.'type='.urlencode($U))."'>$U</a>":$U.($eb&&isset($Xh["Collation"])&&$eb!=$Xh["Collation"]?" $eb":""))."</span>",($l["null"]?" <i>NULL</i>":""),($l["auto_increment"]?" <i>".lang(43)."</i>":"");$j=h($l["default"]);echo(isset($l["default"])?" <span title='".lang(44)."'>[<b>".($l["generated"]?"<code class='jush-".JUSH."'>$j</code>":$j)."</b>]</span>":""),(support("comment")?"<td>".h($l["comment"]):""),"\n";}echo"</table>\n","</div>\n";}function
tableIndexesPrint(array$v,array$Xh){$hg=false;foreach($v
as$C=>$u)$hg|=!!$u["partial"];echo"<table>\n";$Kb=first(driver()->indexAlgorithms($Xh));foreach($v
as$C=>$u){ksort($u["columns"]);$Eg=array();foreach($u["columns"]as$w=>$X)$Eg[]="<i>".h($X)."</i>".($u["lengths"][$w]?"(".$u["lengths"][$w].")":"").($u["descs"][$w]?" DESC":"");echo"<tr title='".h($C)."'>","<th>$u[type]".($Kb&&$u['algorithm']!=$Kb?" ($u[algorithm])":""),"<td>".implode(", ",$Eg);if($hg)echo"<td>".($u['partial']?"<code class='jush-".JUSH."'>WHERE ".h($u['partial']):"");echo"\n";}echo"</table>\n";}function
selectColumnsPrint(array$M,array$c){print_fieldset("select",lang(45),$M);$r=0;$M[""]=array();foreach($M
as$w=>$X){$X=idx($_GET["columns"],$w,array());$b=select_input(" name='columns[$r][col]'",$c,$X["col"],($w!==""?"selectFieldChange":"selectAddRow"));echo"<div>".(driver()->functions||driver()->grouping?html_select("columns[$r][fun]",array(-1=>"")+array_filter(array(lang(46)=>driver()->functions,lang(47)=>driver()->grouping)),$X["fun"]).on_help("event.target.value && event.target.value.replace(/ |\$/, '(') + ')'",1).script("qsl('select').onchange = function () { helpClose();".($w!==""?"":" qsl('select, input', this.parentNode).onchange();")." };","")."($b)":$b)."</div>\n";$r++;}echo"</div></fieldset>\n";}function
selectSearchPrint(array$Z,array$c,array$v){print_fieldset("search",lang(48),$Z);foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT")echo"<div>(<i>".implode("</i>, <i>",array_map('Adminer\h',$u["columns"]))."</i>) AGAINST"," <input type='search' name='fulltext[$r]' value='".h(idx($_GET["fulltext"],$r))."'>",script("qsl('input').oninput = selectFieldChange;",""),(JUSH=='sql'?checkbox("boolean[$r]",1,isset($_GET["boolean"][$r]),"BOOL"):''),"</div>\n";}$Ra="this.parentNode.firstChild.onchange();";foreach(array_merge((array)$_GET["where"],array(array()))as$r=>$X){if(!$X||("$X[col]$X[val]"!=""&&in_array($X["op"],adminer()->operators())))echo"<div>".select_input(" name='where[$r][col]'",$c,$X["col"],($X?"selectFieldChange":"selectAddRow"),"(".lang(49).")"),html_select("where[$r][op]",adminer()->operators(),$X["op"],$Ra),"<input type='search' name='where[$r][val]' value='".h($X["val"])."'>",script("mixin(qsl('input'), {oninput: function () { $Ra }, onkeydown: selectSearchKeydown, onsearch: selectSearchSearch});",""),"</div>\n";}echo"</div></fieldset>\n";}function
selectOrderPrint(array$D,array$c,array$v){print_fieldset("sort",lang(50),$D);$r=0;foreach((array)$_GET["order"]as$w=>$X){if($X!=""){echo"<div>".select_input(" name='order[$r]'",$c,$X,"selectFieldChange"),checkbox("desc[$r]",1,isset($_GET["desc"][$w]),lang(51))."</div>\n";$r++;}}echo"<div>".select_input(" name='order[$r]'",$c,"","selectAddRow"),checkbox("desc[$r]",1,false,lang(51))."</div>\n","</div></fieldset>\n";}function
selectLimitPrint($y){echo"<fieldset><legend>".lang(52)."</legend><div>","<input type='number' name='limit' class='size' value='".intval($y)."'>",script("qsl('input').oninput = selectFieldChange;",""),"</div></fieldset>\n";}function
selectLengthPrint($li){if($li!==null)echo"<fieldset><legend>".lang(53)."</legend><div>","<input type='number' name='text_length' class='size' value='".h($li)."'>","</div></fieldset>\n";}function
selectActionPrint(array$v){echo"<fieldset><legend>".lang(54)."</legend><div>","<input type='submit' value='".lang(45)."'>"," <span id='noindex' title='".lang(55)."'></span>","<script".nonce().">\n","const indexColumns = ";$c=array();foreach($v
as$u){$Bb=reset($u["columns"]);if($u["type"]!="FULLTEXT"&&$Bb)$c[$Bb]=1;}$c[""]=1;foreach($c
as$w=>$X)json_row($w);echo";\n","selectFieldChange.call(qs('#form')['select']);\n","</script>\n","</div></fieldset>\n";}function
selectCommandPrint(){return!information_schema(DB);}function
selectImportPrint(){return!information_schema(DB);}function
selectEmailPrint(array$mc,array$c){}function
selectColumnsProcess(array$c,array$v){$M=array();$q=array();foreach((array)$_GET["columns"]as$w=>$X){if($X["fun"]=="count"||($X["col"]!=""&&(!$X["fun"]||in_array($X["fun"],driver()->functions)||in_array($X["fun"],driver()->grouping)))){$M[$w]=apply_sql_function($X["fun"],($X["col"]!=""?idf_escape($X["col"]):"*"));if(!in_array($X["fun"],driver()->grouping))$q[]=$M[$w];}}return
array($M,$q);}function
selectSearchProcess(array$m,array$v){$J=array();foreach($v
as$r=>$u){if($u["type"]=="FULLTEXT"&&idx($_GET["fulltext"],$r)!="")$J[]="MATCH (".implode(", ",array_map('Adminer\idf_escape',$u["columns"])).") AGAINST (".q($_GET["fulltext"][$r]).(isset($_GET["boolean"][$r])?" IN BOOLEAN MODE":"").")";}foreach((array)$_GET["where"]as$w=>$X){$cb=$X["col"];if("$cb$X[val]"!=""&&in_array($X["op"],adminer()->operators())){$nb=array();foreach(($cb!=""?array($cb=>$m[$cb]):$m)as$C=>$l){$Bg="";$mb=" $X[op]";if(preg_match('~IN$~',$X["op"])){$Jd=process_length($X["val"]);$mb
.=" ".($Jd!=""?$Jd:"(NULL)");}elseif($X["op"]=="SQL")$mb=" $X[val]";elseif(preg_match('~^(I?LIKE) %%$~',$X["op"],$A))$mb=" $A[1] ".adminer()->processInput($l,"%$X[val]%");elseif($X["op"]=="FIND_IN_SET"){$Bg="$X[op](".q($X["val"]).", ";$mb=")";}elseif(!preg_match('~NULL$~',$X["op"]))$mb
.=" ".adminer()->processInput($l,$X["val"]);if($cb!=""||(isset($l["privileges"]["where"])&&(preg_match('~^[-\d.'.(preg_match('~IN$~',$X["op"])?',':'').']+$~',$X["val"])||!preg_match('~'.number_type().'|bit~',$l["type"]))&&(!preg_match("~[\x80-\xFF]~",$X["val"])||preg_match('~char|text|enum|set~',$l["type"]))&&(!preg_match('~date|timestamp~',$l["type"])||preg_match('~^\d+-\d+-\d+~',$X["val"]))))$nb[]=$Bg.driver()->convertSearch(idf_escape($C),$X,$l).$mb;}$J[]=(count($nb)==1?$nb[0]:($nb?"(".implode(" OR ",$nb).")":"1 = 0"));}}return$J;}function
selectOrderProcess(array$m,array$v){$J=array();foreach((array)$_GET["order"]as$w=>$X){if($X!="")$J[]=(preg_match('~^((COUNT\(DISTINCT |[A-Z0-9_]+\()(`(?:[^`]|``)+`|"(?:[^"]|"")+")\)|COUNT\(\*\))$~',$X)?$X:idf_escape($X)).(isset($_GET["desc"][$w])?" DESC".(JUSH=='pgsql'&&idx($m[$X],"null")?" NULLS LAST":""):"");}return$J;}function
selectLimitProcess(){return(isset($_GET["limit"])?intval($_GET["limit"]):50);}function
selectLengthProcess(){return(isset($_GET["text_length"])?"$_GET[text_length]":"100");}function
selectEmailProcess(array$Z,array$ad){return
false;}function
selectQueryBuild(array$M,array$Z,array$q,array$D,$y,$E){return"";}function
messageQuery($H,$mi,$Jc=false){restart_session();$yd=&get_session("queries");if(!idx($yd,$_GET["db"]))$yd[$_GET["db"]]=array();if(strlen($H)>1e6)$H=preg_replace('~[\x80-\xFF]+$~','',substr($H,0,1e6))."\n…";$yd[$_GET["db"]][]=array($H,time(),$mi);$Ih="sql-".count($yd[$_GET["db"]]);$J="<a href='#$Ih' class='toggle'>".lang(56)."</a> <a href='' class='jsonly copy'>🗐</a>\n";if(!$Jc&&($jj=driver()->warnings())){$s="warnings-".count($yd[$_GET["db"]]);$J="<a href='#$s' class='toggle'>".lang(38)."</a>, $J<div id='$s' class='hidden'>\n$jj</div>\n";}return" <span class='time'>".@date("H:i:s")."</span>"." $J<div id='$Ih' class='hidden'><pre><code class='jush-".JUSH."'>".shorten_utf8($H,1e4)."</code></pre>".($mi?" <span class='time'>($mi)</span>":'').(support("sql")?'<p><a href="'.h(str_replace("db=".urlencode(DB),"db=".urlencode($_GET["db"]),ME).'sql=&history='.(count($yd[$_GET["db"]])-1)).'">'.lang(12).'</a>':'').'</div>';}function
editRowPrint($R,array$m,$K,$Oi){}function
editFunctions(array$l){$J=($l["null"]?"NULL/":"");$Oi=isset($_GET["select"])||where($_GET);foreach(array(driver()->insertFunctions,driver()->editFunctions)as$w=>$hd){if(!$w||(!isset($_GET["call"])&&$Oi)){foreach($hd
as$rg=>$X){if(!$rg||preg_match("~$rg~",$l["type"]))$J
.="/$X";}}if($w&&$hd&&!preg_match('~set|bool~',$l["type"])&&!is_blob($l))$J
.="/SQL";}if($l["auto_increment"]&&!$Oi)$J=lang(43);return
explode("/",$J);}function
editInput($R,array$l,$xa,$Y){if($l["type"]=="enum")return(isset($_GET["select"])?"<label><input type='radio'$xa value='orig' checked><i>".lang(10)."</i></label> ":"").enum_input("radio",$xa,$l,$Y,"NULL");return"";}function
editHint($R,array$l,$Y){return"";}function
processInput(array$l,$Y,$p=""){if($p=="SQL")return$Y;$C=$l["field"];$J=q($Y);if(preg_match('~^(now|getdate|uuid)$~',$p))$J="$p()";elseif(preg_match('~^current_(date|timestamp)$~',$p))$J=$p;elseif(preg_match('~^([+-]|\|\|)$~',$p))$J=idf_escape($C)." $p $J";elseif(preg_match('~^[+-] interval$~',$p))$J=idf_escape($C)." $p ".(preg_match("~^(\\d+|'[0-9.: -]') [A-Z_]+\$~i",$Y)&&JUSH!="pgsql"?$Y:$J);elseif(preg_match('~^(addtime|subtime|concat)$~',$p))$J="$p(".idf_escape($C).", $J)";elseif(preg_match('~^(md5|sha1|password|encrypt)$~',$p))$J="$p($J)";return
unconvert_field($l,$J);}function
dumpOutput(){$J=array('text'=>lang(57),'file'=>lang(58));if(function_exists('gzencode'))$J['gz']='gzip';return$J;}function
dumpFormat(){return(support("dump")?array('sql'=>'SQL'):array())+array('csv'=>'CSV,','csv;'=>'CSV;','tsv'=>'TSV');}function
dumpDatabase($i){}function
dumpTable($R,$Ph,$he=0){if($_POST["format"]!="sql"){echo"\xef\xbb\xbf";if($Ph)dump_csv(array_keys(fields($R)));}else{if($he==2){$m=array();foreach(fields($R)as$C=>$l)$m[]=idf_escape($C)." $l[full_type]";$g="CREATE TABLE ".table($R)." (".implode(", ",$m).")";}else$g=create_sql($R,$_POST["auto_increment"],$Ph);set_utf8mb4($g);if($Ph&&$g){if($Ph=="DROP+CREATE"||$he==1)echo"DROP ".($he==2?"VIEW":"TABLE")." IF EXISTS ".table($R).";\n";if($he==1)$g=remove_definer($g);echo"$g;\n\n";}}}function
dumpData($R,$Ph,$H){if($Ph){$Qe=(JUSH=="sqlite"?0:1048576);$m=array();$Fd=false;if($_POST["format"]=="sql"){if($Ph=="TRUNCATE+INSERT")echo
truncate_sql($R).";\n";$m=fields($R);if(JUSH=="mssql"){foreach($m
as$l){if($l["auto_increment"]){echo"SET IDENTITY_INSERT ".table($R)." ON;\n";$Fd=true;break;}}}}$I=connection()->query($H,1);if($I){$Wd="";$Na="";$me=array();$id=array();$Rh="";$Mc=($R!=''?'fetch_assoc':'fetch_row');$vb=0;while($K=$I->$Mc()){if(!$me){$bj=array();foreach($K
as$X){$l=$I->fetch_field();if(idx($m[$l->name],'generated')){$id[$l->name]=true;continue;}$me[]=$l->name;$w=idf_escape($l->name);$bj[]="$w = VALUES($w)";}$Rh=($Ph=="INSERT+UPDATE"?"\nON DUPLICATE KEY UPDATE ".implode(", ",$bj):"").";\n";}if($_POST["format"]!="sql"){if($Ph=="table"){dump_csv($me);$Ph="INSERT";}dump_csv($K);}else{if(!$Wd)$Wd="INSERT INTO ".table($R)." (".implode(", ",array_map('Adminer\idf_escape',$me)).") VALUES";foreach($K
as$w=>$X){if($id[$w]){unset($K[$w]);continue;}$l=$m[$w];$K[$w]=($X===null?"NULL":($X===false?0:unconvert_field($l,preg_match(number_type(),$l["type"])&&!preg_match('~\[~',$l["full_type"])&&is_numeric($X)?$X:(!is_blob($l)||is_utf8($X)?q($X):driver()->quoteBinary($X)))));}$ih=($Qe?"\n":" ")."(".implode(",\t",$K).")";if(!$Na)$Na=$Wd.$ih;elseif(JUSH=='mssql'?$vb%1000!=0:strlen($Na)+4+strlen($ih)+strlen($Rh)<$Qe)$Na
.=",$ih";else{echo$Na.$Rh;$Na=$Wd.$ih;}}$vb++;}if($Na)echo$Na.$Rh;}elseif($_POST["format"]=="sql")echo"-- ".str_replace("\n"," ",connection()->error)."\n";if($Fd)echo"SET IDENTITY_INSERT ".table($R)." OFF;\n";}}function
dumpFilename($Ed){return
friendly_url($Ed!=""?$Ed:(SERVER?:"localhost"));}function
dumpHeaders($Ed,$lf=false){$bg=$_POST["output"];$Ec=(preg_match('~sql~',$_POST["format"])?"sql":($lf?"tar":"csv"));header("Content-Type: ".($bg=="gz"?"application/x-gzip":($Ec=="tar"?"application/x-tar":($Ec=="sql"||$bg!="file"?"text/plain":"text/csv")."; charset=utf-8")));if($bg=="gz"){ob_start(function($Q){return
gzencode($Q);},1e6);}return$Ec;}function
dumpFooter(){if($_POST["format"]=="sql")echo"-- ".gmdate("Y-m-d H:i:s e")."\n";}function
importServerPath(){return"adminer.sql";}function
homepage(){echo'<p class="links">'.($_GET["ns"]==""&&support("database")?'<a href="'.h(ME).'database=">'.lang(59)."</a>\n":""),(support("scheme")?"<a href='".h(ME)."scheme='>".($_GET["ns"]!=""?lang(60):lang(61))."</a>\n":""),($_GET["ns"]!==""?'<a href="'.h(ME).'schema=">'.lang(62)."</a>\n":""),(support("privileges")?"<a href='".h(ME)."privileges='>".lang(63)."</a>\n":"");if($_GET["ns"]!=="")echo(support("routine")?"<a href='#routines'>".lang(64)."</a>\n":""),(support("sequence")?"<a href='#sequences'>".lang(65)."</a>\n":""),(support("type")?"<a href='#user-types'>".lang(6)."</a>\n":""),(support("event")?"<a href='#events'>".lang(66)."</a>\n":"");return
true;}function
navigation($if){echo"<h1>".adminer()->name()." <span class='version'>".VERSION;$uf=$_COOKIE["adminer_version"];echo" <a href='https://www.adminer.org/#download'".target_blank()." id='version'>".(version_compare(VERSION,$uf)<0?h($uf):"")."</a>","</span></h1>\n";switch_lang();if($if=="auth"){$bg="";foreach((array)$_SESSION["pwds"]as$dj=>$wh){foreach($wh
as$N=>$Xi){$C=h(get_setting("vendor-$dj-$N")?:get_driver($dj));foreach($Xi
as$V=>$F){if($F!==null){$Ib=$_SESSION["db"][$dj][$N][$V];foreach(($Ib?array_keys($Ib):array(""))as$i)$bg
.="<li><a href='".h(auth_url($dj,$N,$V,$i))."'>($C) ".h("$V@".($N!=""?adminer()->serverName($N):"").($i!=""?" - $i":""))."</a>\n";}}}}if($bg)echo"<ul id='logins'>\n$bg</ul>\n".script("mixin(qs('#logins'), {onmouseover: menuOver, onmouseout: menuOut});");}else{$T=array();if($_GET["ns"]!==""&&!$if&&DB!=""){connection()->select_db(DB);$T=table_status('',true);}adminer()->syntaxHighlighting($T);adminer()->databasesPrint($if);$ia=array();if(DB==""||!$if){if(support("sql")){$ia['sql']="<a href='".h(ME)."sql='".bold(isset($_GET["sql"])&&!isset($_GET["import"])).">".lang(56)."</a>";$ia['import']="<a href='".h(ME)."import='".bold(isset($_GET["import"])).">".lang(67)."</a>";}$ia['dump']="<a href='".h(ME)."dump=".urlencode(isset($_GET["table"])?$_GET["table"]:$_GET["select"])."' id='dump'".bold(isset($_GET["dump"])).">".lang(68)."</a>";}$Kd=$_GET["ns"]!==""&&!$if&&DB!="";if($Kd&&function_exists('Adminer\alter_table'))$ia['create']='<a href="'.h(ME).'create="'.bold($_GET["create"]==="").">".lang(69)."</a>";$ia=adminer()->menuActions($ia,$if);echo($ia?"<p class='links'>\n".implode("\n",$ia)."\n":"");if($Kd){if($T)adminer()->tablesPrint($T);else
echo"<p class='message'>".lang(11)."</p>\n";}}}function
syntaxHighlighting(array$T){echo
script_src(preg_replace("~\\?.*~","",ME)."?file=jush.js&version=5.5.1",true);if(support("sql")){echo"<script".nonce().">\n";if($T){$De=array();foreach($T
as$R=>$U)$De[]=preg_quote($R,'/');echo"var jushLinks = { ".JUSH.":";json_row(js_escape(ME).(support("table")?"table":"select").'=$&','/\b('.implode('|',$De).')\b/g',false);if(support('routine')){foreach(routines()as$K)json_row(js_escape(ME).'function='.urlencode($K["SPECIFIC_NAME"]).'&name=$&','/\b'.preg_quote($K["ROUTINE_NAME"],'/').'(?=["`]?\()/g',false);}json_row('');echo"};\n";foreach(array("bac","bra","sqlite_quo","mssql_bra")as$X)echo"jushLinks.$X = jushLinks.".JUSH.";\n";if(isset($_GET["sql"])||isset($_GET["trigger"])||isset($_GET["check"])){$ci=array_fill_keys(array_keys($T),array());foreach(driver()->allFields()as$R=>$m){foreach($m
as$l)$ci[$R][]=$l["field"];}echo"addEventListener('DOMContentLoaded', () => { autocompleter = jush.autocompleteSql('".idf_escape("")."', ".json_encode($ci)."); });\n";}}echo"</script>\n";}echo
script("syntaxHighlighting('".(preg_match('~^\d\.?\d~',connection()->server_info,$A)?$A[0]:"")."', '".connection()->flavor."');");}function
databasesPrint($if){$h=adminer()->databases();if(DB&&$h&&!in_array(DB,$h))array_unshift($h,DB);echo"<form action=''>\n<p id='dbs'>\n";hidden_fields_get();$Gb=script("mixin(qsl('select'), {onmousedown: dbMouseDown, onchange: dbChange});");echo"<label title='".lang(29)."'>".lang(70).": ".($h?html_select("db",array(""=>"")+$h,DB).$Gb:"<input name='db' value='".h(DB)."' autocapitalize='off' size='19'>\n")."</label>","<input type='submit' value='".lang(22)."'".($h?" class='hidden'":"").">\n";foreach(array("import","sql","schema","dump","privileges")as$X){if(isset($_GET[$X])){echo
input_hidden($X);break;}}echo"</p></form>\n";}function
menuActions(array$ia,$if){return$ia;}function
tablesPrint(array$T){echo"<ul id='tables'>".script("mixin(qs('#tables'), {onmouseover: menuOver, onmouseout: menuOut});");foreach($T
as$R=>$P){$R="$R";$C=adminer()->tableName($P);if($C!=""&&!$P["partition"])echo'<li><a href="'.h(ME).'select='.urlencode($R).'"'.bold($_GET["select"]==$R||$_GET["edit"]==$R,"select")." title='".lang(33)."'>".lang(71)."</a> ",(support("table")||support("indexes")?'<a href="'.h(ME).'table='.urlencode($R).'"'.bold(in_array($R,array($_GET["table"],$_GET["create"],$_GET["indexes"],$_GET["foreign"],$_GET["trigger"],$_GET["check"],$_GET["view"])),(is_view($P)?"view":"structure"))." title='".lang(34)."'>$C</a>":"<span>$C</span>")."\n";}echo"</ul>\n";}function
showVariables(){return
show_variables();}function
showStatus(){return
show_status();}function
processList(){return
process_list();}function
killProcess($s){return
kill_process($s);}}class
Plugins{private
static$append=array('dumpFormat'=>true,'dumpOutput'=>true,'editRowPrint'=>true,'editFunctions'=>true,'config'=>true);var$plugins;var$error='';private$hooks=array();function
__construct($wg){if($wg===null){$wg=array();$Ha="adminer-plugins";if(is_dir($Ha)){foreach(glob("$Ha/*.php")as$Rc)$this->includeOnce($Rc);}$xd=" href='https://www.adminer.org/plugins/#use'".target_blank();if(file_exists("$Ha.php")){$Ld=$this->includeOnce("$Ha.php");if(is_array($Ld)){foreach($Ld
as$vg)$wg[get_class($vg)]=$vg;}else$this->error
.=lang(72,"<b>$Ha.php</b>",$xd)."<br>";}foreach(get_declared_classes()as$ab){if(!$wg[$ab]&&(preg_match('~^Adminer\w~i',$ab)||is_subclass_of($ab,'Adminer\Plugin'))){$Vg=new
\ReflectionClass($ab);$pb=$Vg->getConstructor();if($pb&&$pb->getNumberOfRequiredParameters())$this->error
.=lang(73,$xd,"<b>$ab</b>","<b>$Ha.php</b>")."<br>";else$wg[$ab]=new$ab;}}}$this->plugins=$wg;$ja=new
Adminer;$wg[]=$ja;$Vg=new
\ReflectionObject($ja);foreach($Vg->getMethods()as$gf){foreach($wg
as$vg){$C=$gf->getName();if(method_exists($vg,$C))$this->hooks[$C][]=$vg;}}}function
includeOnce($Rc){return
include_once"./$Rc";}function
__call($C,array$fg){$ta=array();foreach($fg
as$w=>$X)$ta[]=&$fg[$w];$J=null;foreach($this->hooks[$C]as$vg){$Y=call_user_func_array(array($vg,$C),$ta);if($Y!==null){if(!self::$append[$C])return$Y;$J=$Y+(array)$J;}}return$J;}}abstract
class
Plugin{protected$translations=array();function
description(){return$this->lang('');}function
screenshot(){return"";}protected
function
lang($t,$zf=null){$ta=func_get_args();$ta[0]=idx($this->translations[LANG],$t)?:$t;return
call_user_func_array('Adminer\lang_format',$ta);}}Adminer::$instance=(function_exists('adminer_object')?adminer_object():(is_dir("adminer-plugins")||file_exists("adminer-plugins.php")?new
Plugins(null):new
Adminer));SqlDriver::$drivers=array("server"=>"MySQL / MariaDB")+SqlDriver::$drivers;if(!defined('Adminer\DRIVER')){define('Adminer\DRIVER',"server");if(extension_loaded("mysqli")&&$_GET["ext"]!="pdo"){class
Db
extends
\MySQLi{static$instance;var$extension="MySQLi",$flavor='';function
__construct(){parent::init();}function
attach($N,$V,$F){mysqli_report(MYSQLI_REPORT_OFF);list($Ad,$xg)=host_port($N);$Kh=adminer()->connectSsl();if($Kh)$this->ssl_set($Kh['key'],$Kh['cert'],$Kh['ca'],'','');$J=@$this->real_connect(($N!=""?$Ad:ini_get("mysqli.default_host")),($N.$V!=""?$V:ini_get("mysqli.default_user")),($N.$V.$F!=""?$F:ini_get("mysqli.default_pw")),null,(is_numeric($xg)?intval($xg):ini_get("mysqli.default_port")),(is_numeric($xg)?null:$xg),($Kh?($Kh['verify']!==false?2048:64):0));$this->options(MYSQLI_OPT_LOCAL_INFILE,0);return($J?'':$this->error);}function
set_charset($Ta){if(parent::set_charset($Ta))return
true;parent::set_charset('utf8');return$this->query("SET NAMES $Ta");}function
next_result(){return
self::more_results()&&parent::next_result();}function
quote($Q){return"'".$this->escape_string($Q)."'";}}}elseif(extension_loaded("mysql")&&!((ini_bool("sql.safe_mode")||ini_bool("mysql.allow_local_infile"))&&extension_loaded("pdo_mysql"))){class
Db
extends
SqlDb{private$link;function
attach($N,$V,$F){if(ini_bool("mysql.allow_local_infile"))return
lang(74,"'mysql.allow_local_infile'","MySQLi","PDO_MySQL");$this->link=@mysql_connect(($N!=""?$N:ini_get("mysql.default_host")),($N.$V!=""?$V:ini_get("mysql.default_user")),($N.$V.$F!=""?$F:ini_get("mysql.default_password")),true,131072);if(!$this->link)return
mysql_error();$this->server_info=mysql_get_server_info($this->link);return'';}function
set_charset($Ta){return
mysql_set_charset($Ta,$this->link)||mysql_set_charset('utf8',$this->link);}function
quote($Q){return"'".mysql_real_escape_string($Q,$this->link)."'";}function
select_db($Fb){return
mysql_select_db($Fb,$this->link);}function
query($H,$Hi=false){$I=@($Hi?mysql_unbuffered_query($H,$this->link):mysql_query($H,$this->link));$this->error="";if(!$I){$this->errno=mysql_errno($this->link);$this->error=mysql_error($this->link);return
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
attach($N,$V,$F){$Of=array(\PDO::MYSQL_ATTR_LOCAL_INFILE=>false);$Kh=adminer()->connectSsl();if($Kh){if($Kh['key'])$Of[\PDO::MYSQL_ATTR_SSL_KEY]=$Kh['key'];if($Kh['cert'])$Of[\PDO::MYSQL_ATTR_SSL_CERT]=$Kh['cert'];if($Kh['ca'])$Of[\PDO::MYSQL_ATTR_SSL_CA]=$Kh['ca'];if(isset($Kh['verify']))$Of[\PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT]=$Kh['verify'];}list($Ad,$xg)=host_port($N);return$this->dsn("mysql:charset=utf8".($Ad!=""?";host=$Ad":'').($xg?(is_numeric($xg)?";port=":";unix_socket=").$xg:""),$V,$F,$Of);}function
set_charset($Ta){return$this->query("SET NAMES $Ta");}function
select_db($Fb){return$this->query("USE ".idf_escape($Fb));}function
query($H,$Hi=false){$this->pdo->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY,!$Hi);return
parent::query($H,$Hi);}}}class
Driver
extends
SqlDriver{static$extensions=array("MySQLi","MySQL","PDO_MySQL");static$jush="sql";var$unsigned=array("unsigned","zerofill","unsigned zerofill");var$operators=array("=","<",">","<=",">=","!=","LIKE","LIKE %%","REGEXP","IN","FIND_IN_SET","IS NULL","NOT LIKE","NOT REGEXP","NOT IN","IS NOT NULL","SQL");var$functions=array("char_length","date","from_unixtime","lower","round","floor","ceil","sec_to_time","time_to_sec","upper");var$grouping=array("avg","count","count distinct","group_concat","max","min","sum");var$partitionBy=array("HASH","LINEAR HASH","KEY","LINEAR KEY","RANGE","LIST");static
function
connect($N,$V,$F){$e=parent::connect($N,$V,$F);if(is_string($e)){if(function_exists('iconv')&&!is_utf8($e)&&strlen($ih=iconv("windows-1250","utf-8",$e))>strlen($e))$e=$ih;return$e;}$e->set_charset(charset($e));$e->query("SET sql_quote_show_create = 1, autocommit = 1");$e->flavor=(preg_match('~MariaDB~',$e->server_info)?'maria':'mysql');add_driver(DRIVER,($e->flavor=='maria'?"MariaDB":"MySQL"));return$e;}function
__construct(Db$e){parent::__construct($e);$this->types=array(lang(75)=>array("tinyint"=>3,"smallint"=>5,"mediumint"=>8,"int"=>10,"bigint"=>20,"decimal"=>66,"float"=>12,"double"=>21),lang(76)=>array("date"=>10,"datetime"=>19,"timestamp"=>19,"time"=>10,"year"=>4),lang(77)=>array("char"=>255,"varchar"=>65535,"tinytext"=>255,"text"=>65535,"mediumtext"=>16777215,"longtext"=>4294967295),lang(78)=>array("enum"=>65535,"set"=>64),lang(79)=>array("bit"=>20,"binary"=>255,"varbinary"=>65535,"tinyblob"=>255,"blob"=>65535,"mediumblob"=>16777215,"longblob"=>4294967295),lang(80)=>array("geometry"=>0,"point"=>0,"linestring"=>0,"polygon"=>0,"multipoint"=>0,"multilinestring"=>0,"multipolygon"=>0,"geometrycollection"=>0),);$this->insertFunctions=array("char"=>"md5/sha1/password/encrypt/uuid","binary"=>"md5/sha1","date|time"=>"now",);$this->editFunctions=array(number_type()=>"+/-","date"=>"+ interval/- interval","time"=>"addtime/subtime","char|text"=>"concat",);if(min_version('5.7.8',10.2,$e))$this->types[lang(77)]["json"]=4294967295;if(min_version('',10.7,$e)){$this->types[lang(77)]["uuid"]=128;$this->insertFunctions['uuid']='uuid';}if(min_version('',10.5,$e)){$this->types[lang(81)]["inet6"]=39;if(min_version('','10.10',$e))$this->types[lang(81)]["inet4"]=15;}if(min_version(9,11.7,$e))$this->types[lang(75)]["vector"]=16383;if(min_version(5.7,10.2,$e))$this->generated=array("STORED","VIRTUAL");}function
unconvertFunction(array$l){return(preg_match("~binary~",$l["type"])?"<code class='jush-sql'>UNHEX</code>":($l["type"]=="bit"?doc_link(array('sql'=>'bit-value-literals.html'),"<code>b''</code>"):($l["type"]=="vector"?"<code class='jush-sql'>".($this->conn->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."</code>":(preg_match("~geometry|point|linestring|polygon~",$l["type"])?"<code class='jush-sql'>GeomFromText</code>":""))));}function
insert($R,array$O){return($O?parent::insert($R,$O):queries("INSERT INTO ".table($R)." ()\nVALUES ()"));}function
insertUpdate($R,array$L,array$Dg){$c=array_keys(reset($L));$Bg="INSERT INTO ".table($R)." (".implode(", ",$c).") VALUES\n";$bj=array();foreach($c
as$w)$bj[$w]="$w = VALUES($w)";$Rh="\nON DUPLICATE KEY UPDATE ".implode(", ",$bj);$bj=array();$x=0;foreach($L
as$O){$Y="(".implode(", ",$O).")";if($bj&&(strlen($Bg)+$x+strlen($Y)+strlen($Rh)>1e6)){if(!queries($Bg.implode(",\n",$bj).$Rh))return
false;$bj=array();$x=0;}$bj[]=$Y;$x+=strlen($Y)+2;}return
queries($Bg.implode(",\n",$bj).$Rh);}function
slowQuery($H,$ni){if(min_version('5.7.8','10.1.2')){if($this->conn->flavor=='maria')return"SET STATEMENT max_statement_time=$ni FOR $H";elseif(preg_match('~^(SELECT\b)(.+)~is',$H,$A))return"$A[1] /*+ MAX_EXECUTION_TIME(".($ni*1000).") */ $A[2]";}}function
convertSearch($t,array$X,array$l){return(preg_match('~char|text|enum|set~',$l["type"])&&!preg_match("~^utf8~",$l["collation"])&&preg_match('~[\x80-\xFF]~',$X['val'])?"CONVERT($t USING ".charset($this->conn).")":$t);}function
quoteBinary($ih){return"X".q(bin2hex($ih));}function
warnings(){$I=$this->conn->query("SHOW WARNINGS");if($I&&$I->num_rows){ob_start();print_select_result($I);return
ob_get_clean();}}function
tableHelp($C,$he=false){$Je=($this->conn->flavor=='maria');if(information_schema(DB))return
strtolower("information-schema-".($Je?"$C-table/":str_replace("_","-",$C)."-table.html"));if(DB=="mysql")return($Je?"mysql$C-table/":"system-schema.html");}function
partitionsInfo($R){$fd="FROM information_schema.PARTITIONS WHERE TABLE_SCHEMA = ".q(DB)." AND TABLE_NAME = ".q($R);$I=$this->conn->query("SELECT PARTITION_METHOD, PARTITION_EXPRESSION, PARTITION_ORDINAL_POSITION $fd ORDER BY PARTITION_ORDINAL_POSITION DESC LIMIT 1");$J=array();list($J["partition_by"],$J["partition"],$J["partitions"])=$I->fetch_row();$ng=get_key_vals("SELECT PARTITION_NAME, PARTITION_DESCRIPTION $fd AND PARTITION_NAME != '' ORDER BY PARTITION_ORDINAL_POSITION");$J["partition_names"]=array_keys($ng);$J["partition_values"]=array_values($ng);return$J;}function
hasCStyleEscapes(){static$Oa;if($Oa===null){$Jh=get_val("SHOW VARIABLES LIKE 'sql_mode'",1,$this->conn);$Oa=(strpos($Jh,'NO_BACKSLASH_ESCAPES')===false);}return$Oa;}function
engines(){$J=array();foreach(get_rows("SHOW ENGINES")as$K){if(preg_match("~YES|DEFAULT~",$K["Support"]))$J[]=$K["Engine"];}return$J;}function
indexAlgorithms(array$Xh){return(preg_match('~^(MEMORY|NDB)$~',$Xh["Engine"])?array("HASH","BTREE"):array());}}function
idf_escape($t){return"`".str_replace("`","``",$t)."`";}function
table($t){return
idf_escape($t);}function
get_databases($Xc){$J=get_session("dbs");if($J===null){$H="SELECT SCHEMA_NAME FROM information_schema.SCHEMATA ORDER BY SCHEMA_NAME";$J=($Xc?slow_query($H):get_vals($H));restart_session();set_session("dbs",$J);stop_session();}return$J;}function
limit($H,$Z,$y,$Af=0,$uh=" "){return" $H$Z".($y?$uh."LIMIT $y".($Af?" OFFSET $Af":""):"");}function
limit1($R,$H,$Z,$uh="\n"){return
limit($H,$Z,1,0,$uh);}function
db_collation($i,array$fb){$J=null;$g=get_val("SHOW CREATE DATABASE ".idf_escape($i),1);if(preg_match('~ COLLATE ([^ ]+)~',$g,$A))$J=$A[1];elseif(preg_match('~ CHARACTER SET ([^ ]+)~',$g,$A))$J=$fb[$A[1]][-1];return$J;}function
logged_user(){return
get_val("SELECT USER()");}function
tables_list(){return
get_key_vals("SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ORDER BY TABLE_NAME");}function
count_tables(array$h){$J=array();foreach($h
as$i)$J[$i]=count(get_vals("SHOW TABLES IN ".idf_escape($i)));return$J;}function
table_status($C="",$Kc=false){$J=array();foreach(get_rows($Kc?"SELECT TABLE_NAME AS Name, ENGINE AS Engine, TABLE_COMMENT AS Comment FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() ".($C!=""?"AND TABLE_NAME = ".q($C):"ORDER BY Name"):"SHOW TABLE STATUS".($C!=""?" LIKE ".q(addcslashes($C,"%_\\")):""))as$K){if($K["Engine"]=="InnoDB")$K["Comment"]=preg_replace('~(?:(.+); )?InnoDB free: .*~','\1',$K["Comment"]);if(!isset($K["Engine"]))$K["Comment"]="";if($C!="")$K["Name"]=$C;$J[$K["Name"]]=$K;}return$J;}function
is_view(array$S){return$S["Engine"]===null;}function
fk_support(array$S){return
preg_match('~InnoDB|IBMDB2I'.(min_version(5.6)?'|NDB':'').'~i',$S["Engine"]);}function
fields($R){$Je=(connection()->flavor=='maria');$J=array();foreach(get_rows("SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ".q($R)." ORDER BY ORDINAL_POSITION")as$K){$l=$K["COLUMN_NAME"];$U=$K["COLUMN_TYPE"];$jd=$K["GENERATION_EXPRESSION"];$Hc=$K["EXTRA"];preg_match('~^(VIRTUAL|PERSISTENT|STORED)~',$Hc,$id);preg_match('~^([^( ]+)(?:\((.+)\))?( unsigned)?( zerofill)?$~',$U,$Le);$j=$K["COLUMN_DEFAULT"];if($j!=""){$ge=preg_match('~text|json~',$Le[1]);if(!$Je&&$ge)$j=preg_replace("~^(_\w+)?('.*')$~",'\2',stripslashes($j));if($Je||$ge){$j=($j=="NULL"?null:preg_replace_callback("~^'(.*)'$~",function($A){return
stripslashes(str_replace("''","'",$A[1]));},$j));}if(!$Je&&preg_match('~binary~',$Le[1])&&preg_match('~^0x(\w*)$~',$j,$A))$j=pack("H*",$A[1]);}$J[$l]=array("field"=>$l,"full_type"=>$U,"type"=>$Le[1],"length"=>$Le[2],"unsigned"=>ltrim($Le[3].$Le[4]),"default"=>($id?($Je?$jd:stripslashes($jd)):$j),"null"=>($K["IS_NULLABLE"]=="YES"),"auto_increment"=>($Hc=="auto_increment"),"on_update"=>(preg_match('~\bon update (\w+)~i',$Hc,$A)?$A[1]:""),"collation"=>$K["COLLATION_NAME"],"privileges"=>array_flip(explode(",","$K[PRIVILEGES],where,order")),"comment"=>$K["COLUMN_COMMENT"],"primary"=>($K["COLUMN_KEY"]=="PRI"),"generated"=>($id[1]=="PERSISTENT"?"STORED":$id[1]),);}return$J;}function
indexes($R,$f=null){$J=array();foreach(get_rows("SHOW INDEX FROM ".table($R),$f)as$K){$C=$K["Key_name"];$J[$C]["type"]=($C=="PRIMARY"?"PRIMARY":($K["Index_type"]=="FULLTEXT"?"FULLTEXT":($K["Non_unique"]?(preg_match('~^(SPATIAL|VECTOR)$~',$K["Index_type"])?$K["Index_type"]:"INDEX"):"UNIQUE")));$J[$C]["columns"][]=$K["Column_name"];$J[$C]["lengths"][]=($K["Index_type"]=="SPATIAL"?null:$K["Sub_part"]);$J[$C]["descs"][]=null;$J[$C]["algorithm"]=$K["Index_type"];}return$J;}function
foreign_keys($R){static$rg='(?:`(?:[^`]|``)+`|"(?:[^"]|"")+")';$J=array();$wb=get_val("SHOW CREATE TABLE ".table($R),1);if($wb){preg_match_all("~CONSTRAINT ($rg) FOREIGN KEY ?\\(((?:$rg,? ?)+)\\) REFERENCES ($rg)(?:\\.($rg))? \\(((?:$rg,? ?)+)\\)(?: ON DELETE (".driver()->onActions."))?(?: ON UPDATE (".driver()->onActions."))?~",$wb,$Me,PREG_SET_ORDER);foreach($Me
as$A){preg_match_all("~$rg~",$A[2],$Eh);preg_match_all("~$rg~",$A[5],$gi);$J[idf_unescape($A[1])]=array("db"=>idf_unescape($A[4]!=""?$A[3]:$A[4]),"table"=>idf_unescape($A[4]!=""?$A[4]:$A[3]),"source"=>array_map('Adminer\idf_unescape',$Eh[0]),"target"=>array_map('Adminer\idf_unescape',$gi[0]),"on_delete"=>($A[6]?:"RESTRICT"),"on_update"=>($A[7]?:"RESTRICT"),);}}return$J;}function
view($C){return
array("select"=>preg_replace('~^(?:[^`]|`[^`]*`)*\s+AS\s+~isU','',get_val("SHOW CREATE VIEW ".table($C),1)));}function
collations(){$J=array();foreach(get_rows("SHOW COLLATION")as$K){if($K["Default"])$J[$K["Charset"]][-1]=$K["Collation"];else$J[$K["Charset"]][]=$K["Collation"];}ksort($J);foreach($J
as$w=>$X)sort($J[$w]);return$J;}function
information_schema($i){return($i=="information_schema")||(min_version(5.5)&&$i=="performance_schema");}function
error(){return
h(preg_replace('~^You have an error.*syntax to use~U',"Syntax error",connection()->error));}function
create_database($i,$eb){return
queries("CREATE DATABASE ".idf_escape($i).($eb?" COLLATE ".q($eb):""));}function
drop_databases(array$h){$J=apply_queries("DROP DATABASE",$h,'Adminer\idf_escape');restart_session();set_session("dbs",null);return$J;}function
rename_database($C,$eb){$J=false;if(create_database($C,$eb)){$T=array();$gj=array();foreach(tables_list()as$R=>$U){if($U=='VIEW')$gj[]=$R;else$T[]=$R;}$J=(!$T&&!$gj)||move_tables($T,$gj,$C);drop_databases($J?array(DB):array());}return$J;}function
auto_increment(){$_a=" PRIMARY KEY";if($_GET["create"]!=""&&$_POST["auto_increment_col"]){foreach(indexes($_GET["create"])as$u){if(in_array($_POST["fields"][$_POST["auto_increment_col"]]["orig"],$u["columns"],true)){$_a="";break;}if($u["type"]=="PRIMARY")$_a=" UNIQUE";}}return" AUTO_INCREMENT$_a";}function
alter_table($R,$C,array$m,array$Zc,$jb,$pc,$eb,$za,$mg){$ra=array();foreach($m
as$l){if($l[1]){$j=$l[1][3];if(preg_match('~ GENERATED~',$j)){$l[1][3]=(connection()->flavor=='maria'?"":$l[1][2]);$l[1][2]=$j;}$ra[]=($R!=""?($l[0]!=""?"CHANGE ".idf_escape($l[0]):"ADD"):" ")." ".implode($l[1]).($R!=""?$l[2]:"");}else$ra[]="DROP ".idf_escape($l[0]);}$ra=array_merge($ra,$Zc);$P=($jb!==null?" COMMENT=".q($jb):"").($pc?" ENGINE=".q($pc):"").($eb?" COLLATE ".q($eb):"").($za!=""?" AUTO_INCREMENT=$za":"");if($mg){$ng=array();if($mg["partition_by"]=='RANGE'||$mg["partition_by"]=='LIST'){foreach($mg["partition_names"]as$w=>$X){$Y=$mg["partition_values"][$w];$ng[]="\n  PARTITION ".idf_escape($X)." VALUES ".($mg["partition_by"]=='RANGE'?"LESS THAN":"IN").($Y!=""?" ($Y)":" MAXVALUE");}}$P
.="\nPARTITION BY $mg[partition_by]($mg[partition])";if($ng)$P
.=" (".implode(",",$ng)."\n)";elseif($mg["partitions"])$P
.=" PARTITIONS ".(+$mg["partitions"]);}elseif($mg===null)$P
.="\nREMOVE PARTITIONING";if($R=="")return
queries("CREATE TABLE ".table($C)." (\n".implode(",\n",$ra)."\n)$P");if($R!=$C)$ra[]="RENAME TO ".table($C);if($P)$ra[]=ltrim($P);return($ra?queries("ALTER TABLE ".table($R)."\n".implode(",\n",$ra)):true);}function
alter_indexes($R,$ra){$Sa=array();foreach($ra
as$X)$Sa[]=($X[2]=="DROP"?"\nDROP INDEX ".idf_escape($X[1]):"\nADD $X[0] ".($X[0]=="PRIMARY"?"KEY ":"").($X[1]!=""?idf_escape($X[1])." ":"")."(".implode(", ",$X[2]).")");return
queries("ALTER TABLE ".table($R).implode(",",$Sa));}function
truncate_tables(array$T){return
apply_queries("TRUNCATE TABLE",$T);}function
drop_views(array$gj){return
queries("DROP VIEW ".implode(", ",array_map('Adminer\table',$gj)));}function
drop_tables(array$T){return
queries("DROP TABLE ".implode(", ",array_map('Adminer\table',$T)));}function
move_tables(array$T,array$gj,$gi){$Yg=array();foreach($T
as$R)$Yg[]=table($R)." TO ".idf_escape($gi).".".table($R);if(!$Yg||queries("RENAME TABLE ".implode(", ",$Yg))){$Ob=array();foreach($gj
as$R)$Ob[table($R)]=view($R);connection()->select_db($gi);$i=idf_escape(DB);foreach($Ob
as$C=>$fj){if(!queries("CREATE VIEW $C AS ".str_replace(" $i."," ",$fj["select"]))||!queries("DROP VIEW $i.$C"))return
false;}return
true;}return
false;}function
copy_tables(array$T,array$gj,$gi){queries("SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO'");foreach($T
as$R){$C=($gi==DB?table("copy_$R"):idf_escape($gi).".".table($R));if(($_POST["overwrite"]&&!queries("\nDROP TABLE IF EXISTS $C"))||!queries("CREATE TABLE $C LIKE ".table($R))||!queries("INSERT INTO $C SELECT * FROM ".table($R)))return
false;foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K){$Bi=$K["Trigger"];if(!queries("CREATE TRIGGER ".($gi==DB?idf_escape("copy_$Bi"):idf_escape($gi).".".idf_escape($Bi))." $K[Timing] $K[Event] ON $C FOR EACH ROW\n$K[Statement];"))return
false;}}foreach($gj
as$R){$C=($gi==DB?table("copy_$R"):idf_escape($gi).".".table($R));$fj=view($R);if(($_POST["overwrite"]&&!queries("DROP VIEW IF EXISTS $C"))||!queries("CREATE VIEW $C AS $fj[select]"))return
false;}return
true;}function
trigger($C,$R){if($C=="")return
array();$L=get_rows("SHOW TRIGGERS WHERE `Trigger` = ".q($C));return
reset($L);}function
triggers($R){$J=array();foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")))as$K)$J[$K["Trigger"]]=array($K["Timing"],$K["Event"]);return$J;}function
trigger_options(){return
array("Timing"=>array("BEFORE","AFTER"),"Event"=>array("INSERT","UPDATE","DELETE"),"Type"=>array("FOR EACH ROW"),);}function
routine($C,$U){$m=get_rows("SELECT
	PARAMETER_NAME field,
	DATA_TYPE type,
	REGEXP_REPLACE(DTD_IDENTIFIER, '^[^(]+\\\\(?|\\\\)$', '') length,
	REGEXP_REPLACE(DTD_IDENTIFIER, '^[^ ]+ ', '') `unsigned`,
	1 `null`,
	DTD_IDENTIFIER full_type,
	".($U=="FUNCTION"?"''":"PARAMETER_MODE")." `inout`,
	CHARACTER_SET_NAME collation
FROM information_schema.PARAMETERS
WHERE SPECIFIC_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND SPECIFIC_NAME = ".q($C)."
ORDER BY ORDINAL_POSITION");$J=connection()->query("SELECT
	ROUTINE_COMMENT comment,
	CONCAT(IF(IS_DETERMINISTIC = 'YES', 'DETERMINISTIC\\n', ''), IF(SQL_DATA_ACCESS != 'CONTAINS SQL', CONCAT(SQL_DATA_ACCESS, '\\n'), ''), ROUTINE_DEFINITION) definition,
	'SQL' language
FROM information_schema.ROUTINES
WHERE ROUTINE_SCHEMA = DATABASE() AND ROUTINE_TYPE = '$U' AND ROUTINE_NAME = ".q($C))->fetch_assoc();if($m&&$m[0]['field']=='')$J['returns']=array_shift($m);$J['fields']=$m;return$J;}function
routines(){return
get_rows("SELECT SPECIFIC_NAME, ROUTINE_NAME, ROUTINE_TYPE, DTD_IDENTIFIER FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA = DATABASE()");}function
routine_languages(){return
array();}function
routine_id($C,array$K){return
idf_escape($C);}function
last_id($I){return
get_val("SELECT LAST_INSERT_ID()");}function
explain(Db$e,$H){return$e->query("EXPLAIN ".(min_version(5.7)?"":"PARTITIONS ").$H);}function
found_rows(array$S,array$Z){return($Z||$S["Engine"]!="InnoDB"?null:$S["Rows"]);}function
create_sql($R,$za,$Ph){$J=get_val("SHOW CREATE TABLE ".table($R),1);if(!$za)$J=preg_replace('~ AUTO_INCREMENT=\d+~','',$J);return$J;}function
truncate_sql($R){return"TRUNCATE ".table($R);}function
use_sql($Fb,$Ph=""){$C=idf_escape($Fb);$J="";if(preg_match('~CREATE~',$Ph)&&($g=get_val("SHOW CREATE DATABASE $C",1))){set_utf8mb4($g);if($Ph=="DROP+CREATE")$J="DROP DATABASE IF EXISTS $C;\n";$J
.="$g;\n";}return$J."USE $C";}function
trigger_sql($R){$J="";foreach(get_rows("SHOW TRIGGERS LIKE ".q(addcslashes($R,"%_\\")),null,"-- ")as$K)$J
.="\nCREATE TRIGGER ".idf_escape($K["Trigger"])." $K[Timing] $K[Event] ON ".table($K["Table"])." FOR EACH ROW\n$K[Statement];;\n";return$J;}function
show_variables(){return
get_rows("SHOW VARIABLES");}function
show_status(){return
get_rows("SHOW STATUS");}function
process_list(){return
get_rows("SHOW FULL PROCESSLIST");}function
convert_field(array$l){if(preg_match("~binary~",$l["type"]))return"HEX(".idf_escape($l["field"]).")";if($l["type"]=="bit")return"BIN(".idf_escape($l["field"])." + 0)";if($l["type"]=="vector")return(connection()->flavor=='maria'?"VEC_ToText":"VECTOR_TO_STRING")."(".idf_escape($l["field"]).")";if(preg_match("~geometry|point|linestring|polygon~",$l["type"]))return(min_version(8)?"ST_":"")."AsWKT(".idf_escape($l["field"]).")";}function
unconvert_field(array$l,$J){if(preg_match("~binary~",$l["type"]))$J="UNHEX($J)";if($l["type"]=="bit")$J="CONVERT(b$J, UNSIGNED)";if($l["type"]=="vector")$J=(connection()->flavor=='maria'?"VEC_FromText":"STRING_TO_VECTOR")."($J)";if(preg_match("~geometry|point|linestring|polygon~",$l["type"])){$Bg=(min_version(8)?"ST_":"");$J=$Bg."GeomFromText($J, $Bg"."SRID($l[field]))";}return$J;}function
support($Lc){return
preg_match('~^(comment|columns|copy|database|drop_col|dump|event|indexes|kill|privileges|move_col|procedure|processlist|routine|sql|status|table|trigger|variables|view'.(min_version(8)?'|descidx':'').(min_version('8.0.16','10.2.1')?'|check':'').')$~',$Lc);}function
kill_process($s){return
queries("KILL ".number($s));}function
connection_id(){return"SELECT CONNECTION_ID()";}function
max_connections(){return
get_val("SELECT @@max_connections");}function
types(){return
array();}function
type_values($s){return"";}function
schemas(){return
array();}function
get_schema(){return"";}function
set_schema($kh,$f=null){return
true;}}define('Adminer\JUSH',Driver::$jush);define('Adminer\SERVER',"".$_GET[DRIVER]);define('Adminer\DB',"$_GET[db]");define('Adminer\ME',preg_replace('~\?.*~','',relative_uri()).'?'.(sid()?SID.'&':'').(SERVER!==null?DRIVER."=".urlencode(SERVER).'&':'').($_GET["ext"]?"ext=".urlencode($_GET["ext"]).'&':'').(isset($_GET["username"])?"username=".urlencode($_GET["username"]).'&':'').(DB!=""?'db='.urlencode(DB).'&'.(isset($_GET["ns"])?"ns=".urlencode($_GET["ns"])."&":""):''));function
page_header($pi,$k="",$Ma=array(),$qi=""){page_headers();if(is_ajax()&&$k){page_messages($k);exit;}if(!ob_get_level())ob_start('ob_gzhandler',4096);$ri=$pi.($qi!=""?": $qi":"");$si=strip_tags($ri.(SERVER!=""&&SERVER!="localhost"?h(" - ".SERVER):"")." - ".adminer()->name());echo'<!DOCTYPE html>
<html lang="',LANG,'" dir="',lang(82),'">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="robots" content="noindex">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>',$si,'</title>
<link rel="stylesheet" href="',h(preg_replace("~\\?.*~","",ME)."?file=default.css&version=5.5.1"),'">
';$_b=adminer()->css();if(is_int(key($_b)))$_b=array_fill_keys($_b,'light');$ud=in_array('light',$_b)||in_array('',$_b);$sd=in_array('dark',$_b)||in_array('',$_b);$Cb=($ud?($sd?null:false):($sd?:null));$We=" media='(prefers-color-scheme: dark)'";if($Cb!==false)echo"<link rel='stylesheet'".($Cb?"":$We)." href='".h(preg_replace("~\\?.*~","",ME)."?file=dark.css&version=5.5.1")."'>\n";echo"<meta name='color-scheme' content='".($Cb===null?"light dark":($Cb?"dark":"light"))."'>\n",script_src(preg_replace("~\\?.*~","",ME)."?file=functions.js&version=5.5.1");if(adminer()->head($Cb))echo"<link rel='icon' href='data:image/gif;base64,R0lGODlhEAAQAJEAAAQCBPz+/PwCBAROZCH5BAEAAAAALAAAAAAQABAAAAI2hI+pGO1rmghihiUdvUBnZ3XBQA7f05mOak1RWXrNq5nQWHMKvuoJ37BhVEEfYxQzHjWQ5qIAADs='>\n","<link rel='apple-touch-icon' href='".h(preg_replace("~\\?.*~","",ME)."?file=logo.png&version=5.5.1")."'>\n";foreach($_b
as$Si=>$jf){$xa=($jf=='dark'&&!$Cb?$We:($jf=='light'&&$sd?" media='(prefers-color-scheme: light)'":""));echo"<link rel='stylesheet'$xa href='".h($Si)."'>\n";}echo"\n<body class='".lang(82)." nojs";adminer()->bodyClass();echo"'>\n",script("mixin(document.body, {onkeydown: bodyKeydown, onclick: bodyClick".(isset($_COOKIE["adminer_version"])?"":", onload: partial(verifyVersion, '".VERSION."')")."});
document.body.classList.replace('nojs', 'js');
if (!window.isSecureContext) {
	document.body.classList.add('insecure');
}
const offlineMessage = '".js_escape(lang(83))."';
const thousandsSeparator = '".js_escape(lang(4))."';"),"<div id='help' class='jush-".JUSH." jsonly hidden'></div>\n",script("mixin(qs('#help'), {onmouseover: () => { helpOpen = 1; }, onmouseout: helpMouseout});"),"<div id='content'>\n","<span id='menuopen' class='jsonly'>".icon("move","","menu","")."</span>".script("qs('#menuopen').onclick = event => { qs('#foot').classList.toggle('foot'); event.stopPropagation(); }");if($Ma!==null){$z=substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1);echo'<p id="breadcrumb"><a href="'.h($z?:".").'">'.get_driver(DRIVER).'</a> » ';$z=substr(preg_replace('~\b(db|ns)=[^&]*&~','',ME),0,-1);$N=adminer()->serverName(SERVER);$N=($N!=""?$N:lang(25));if($Ma===false)echo"$N\n";else{echo"<a href='".h($z)."' accesskey='1' title='Alt+Shift+1'>$N</a> » ";if($_GET["ns"]!=""||(DB!=""&&is_array($Ma)))echo'<a href="'.h($z."&db=".urlencode(DB).(support("scheme")?"&ns=":"")).'">'.h(DB).'</a> » ';if(is_array($Ma)){if($_GET["ns"]!="")echo'<a href="'.h(substr(ME,0,-1)).'">'.h($_GET["ns"]).'</a> » ';foreach($Ma
as$w=>$X){$Qb=(is_array($X)?$X[1]:h($X));if($Qb!="")echo"<a href='".h(ME."$w=").urlencode(is_array($X)?$X[0]:$X)."'>$Qb</a> » ";}}echo"$pi\n";}}echo"<h2>$ri</h2>\n","<div id='ajaxstatus' class='jsonly hidden'></div>\n";restart_session();page_messages($k);$h=&get_session("dbs");if(DB!=""&&$h&&!in_array(DB,$h,true))$h=null;stop_session();define('Adminer\PAGE_HEADER',1);}function
page_headers(){header("Content-Type: text/html; charset=utf-8");header("Cache-Control: no-cache");header("X-Frame-Options: deny");header("X-XSS-Protection: 0");header("X-Content-Type-Options: nosniff");header("Referrer-Policy: origin-when-cross-origin");foreach(adminer()->csp(csp())as$zb){$vd=array();foreach($zb
as$w=>$X)$vd[]="$w $X";header("Content-Security-Policy: ".implode("; ",$vd));}adminer()->headers();}function
csp(){return
array(array("script-src"=>"'self' 'unsafe-inline' 'nonce-".get_nonce()."' 'strict-dynamic'","connect-src"=>"'self' https://www.adminer.org","frame-src"=>"'none'","object-src"=>"'none'","base-uri"=>"'none'","form-action"=>"'self'",),);}function
get_nonce(){static$wf;if(!$wf)$wf=base64_encode(rand_string());return$wf;}function
page_messages($k){$Ri=preg_replace('~^[^?]*~','',$_SERVER["REQUEST_URI"]);$cf=idx($_SESSION["messages"],$Ri);if($cf){echo"<div class='message'>".implode("</div>\n<div class='message'>",$cf)."</div>".script("messagesPrint();");unset($_SESSION["messages"][$Ri]);}if($k)echo"<div class='error'>$k</div>\n";if(adminer()->error)echo"<div class='error'>".adminer()->error."</div>\n";}function
page_footer($if=""){echo"</div>\n\n<div id='foot' class='foot'>\n<div id='menu'>\n";adminer()->navigation($if);echo"</div>\n";if($if!="auth")echo'<form action="" method="post">
<p class="logout">
<span>',h($_GET["username"])."\n",'</span>
<input type="submit" name="logout" value="',lang(84),'" id="logout">
',input_token(),'</form>
';echo"</div>\n\n",script("setupSubmitHighlight(document);");}function
int32($nf){while($nf>=2147483648)$nf-=4294967296;while($nf<=-2147483649)$nf+=4294967296;return(int)$nf;}function
long2str(array$W,$ij){$ih='';foreach($W
as$X)$ih
.=pack('V',$X);if($ij)return
substr($ih,0,end($W));return$ih;}function
str2long($ih,$ij){$W=array_values(unpack('V*',str_pad($ih,4*ceil(strlen($ih)/4),"\0")));if($ij)$W[]=strlen($ih);return$W;}function
xxtea_mx($oj,$nj,$Sh,$le){return
int32((($oj>>5&0x7FFFFFF)^$nj<<2)+(($nj>>3&0x1FFFFFFF)^$oj<<4))^int32(($Sh^$nj)+($le^$oj));}function
encrypt_string($Nh,$w){if($Nh=="")return"";$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($Nh,true);$nf=count($W)-1;$oj=$W[$nf];$nj=$W[0];$Kg=floor(6+52/($nf+1));$Sh=0;while($Kg-->0){$Sh=int32($Sh+0x9E3779B9);$hc=$Sh>>2&3;for($cg=0;$cg<$nf;$cg++){$nj=$W[$cg+1];$mf=xxtea_mx($oj,$nj,$Sh,$w[$cg&3^$hc]);$oj=int32($W[$cg]+$mf);$W[$cg]=$oj;}$nj=$W[0];$mf=xxtea_mx($oj,$nj,$Sh,$w[$cg&3^$hc]);$oj=int32($W[$nf]+$mf);$W[$nf]=$oj;}return
long2str($W,false);}function
decrypt_string($Nh,$w){if($Nh=="")return"";if(!$w)return
false;$w=array_values(unpack("V*",pack("H*",md5($w))));$W=str2long($Nh,false);$nf=count($W)-1;$oj=$W[$nf];$nj=$W[0];$Kg=floor(6+52/($nf+1));$Sh=int32($Kg*0x9E3779B9);while($Sh){$hc=$Sh>>2&3;for($cg=$nf;$cg>0;$cg--){$oj=$W[$cg-1];$mf=xxtea_mx($oj,$nj,$Sh,$w[$cg&3^$hc]);$nj=int32($W[$cg]-$mf);$W[$cg]=$nj;}$oj=$W[$nf];$mf=xxtea_mx($oj,$nj,$Sh,$w[$cg&3^$hc]);$nj=int32($W[0]-$mf);$W[0]=$nj;$Sh=int32($Sh-0x9E3779B9);}return
long2str($W,true);}$tg=array();if($_COOKIE["adminer_permanent"]){foreach(explode(" ",$_COOKIE["adminer_permanent"])as$X){list($w)=explode(":",$X);$tg[$w]=$X;}}function
add_invalid_login(){$Fa=get_temp_dir()."/adminer-invalid";foreach(glob("$Fa*")?:array($Fa)as$Rc){$o=file_open_lock($Rc);if($o)break;}if(!$o)$o=file_open_lock("$Fa-".rand_string());if(!$o)return;$be=json_decode(stream_get_contents($o),true);$mi=time();if($be){foreach($be
as$ce=>$X){if($X[0]<$mi)unset($be[$ce]);}}$ae=&$be[adminer()->bruteForceKey()];if(!$ae)$ae=array($mi+30*60,0);$ae[1]++;file_write_unlock($o,json_encode($be));}function
check_invalid_login(array&$tg){$be=array();foreach(glob(get_temp_dir()."/adminer-invalid*")as$Rc){$o=file_open_lock($Rc);if($o){$be=json_decode(stream_get_contents($o),true);file_unlock($o);break;}}$ae=idx($be,adminer()->bruteForceKey(),array());$vf=($ae[1]>29?$ae[0]-time():0);if($vf>0)auth_error(lang(85,ceil($vf/60)),$tg);}$ya=$_POST["auth"];if($ya){session_regenerate_id();$dj=$ya["driver"];$N=$ya["server"];$V=$ya["username"];$F=(string)$ya["password"];$i=$ya["db"];set_password($dj,$N,$V,$F);$_SESSION["db"][$dj][$N][$V][$i]=true;if($ya["permanent"]){$w=implode("-",array_map('base64_encode',array($dj,$N,$V,$i)));$Fg=adminer()->permanentLogin(true);$tg[$w]="$w:".base64_encode($Fg?encrypt_string($F,$Fg):"");cookie("adminer_permanent",implode(" ",$tg));}if(count($_POST)==1||DRIVER!=$dj||SERVER!=$N||$_GET["username"]!==$V||DB!=$i)redirect(auth_url($dj,$N,$V,$i));}elseif($_POST["logout"]&&(!$_SESSION["token"]||verify_token())){foreach(array("pwds","db","dbs","queries")as$w)set_session($w,null);unset_permanent($tg);redirect(substr(preg_replace('~\b(username|db|ns)=[^&]*&~','',ME),0,-1),lang(86).' '.lang(87));}elseif($tg&&!$_SESSION["pwds"]){session_regenerate_id();$Fg=adminer()->permanentLogin();foreach($tg
as$w=>$X){list(,$Za)=explode(":",$X);list($dj,$N,$V,$i)=array_map('base64_decode',explode("-",$w));set_password($dj,$N,$V,decrypt_string(base64_decode($Za),$Fg));$_SESSION["db"][$dj][$N][$V][$i]=true;}}function
unset_permanent(array&$tg){foreach($tg
as$w=>$X){list($dj,$N,$V,$i)=array_map('base64_decode',explode("-",$w));if($dj==DRIVER&&$N==SERVER&&$V==$_GET["username"]&&$i==DB)unset($tg[$w]);}cookie("adminer_permanent",implode(" ",$tg));}function
auth_error($k,array&$tg){$xh=session_name();if(isset($_GET["username"])){header("HTTP/1.1 403 Forbidden");if(($_COOKIE[$xh]||$_GET[$xh])&&!$_SESSION["token"])$k=lang(88);else{restart_session();add_invalid_login();$F=get_password();if($F!==null){if($F===false)$k
.=($k?'<br>':'').lang(89,target_blank(),'<code>permanentLogin()</code>');set_password(DRIVER,SERVER,$_GET["username"],null);}unset_permanent($tg);}}if(!$_COOKIE[$xh]&&$_GET[$xh]&&ini_bool("session.use_only_cookies"))$k=lang(90);$fg=session_get_cookie_params();cookie("adminer_key",($_COOKIE["adminer_key"]?:rand_string()),$fg["lifetime"]);if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);page_header(lang(30),$k,null);echo"<form action='' method='post'>\n","<div>";if(hidden_fields($_POST,array("auth")))echo"<p class='message'>".lang(91)."\n";echo"</div>\n";adminer()->loginForm();echo"</form>\n";page_footer("auth");exit;}if(isset($_GET["username"])&&!class_exists('Adminer\Db')){unset($_SESSION["pwds"][DRIVER]);unset_permanent($tg);page_header(lang(92),lang(93,implode(", ",Driver::$extensions)),false);page_footer("auth");exit;}$e='';if(isset($_GET["username"])&&is_string(get_password())){list($Ad,$xg)=host_port(SERVER);if(preg_match('~[^-\w.:/]~',$Ad.$xg))auth_error(lang(94),$tg);if(preg_match('~^-?\d+~',$xg,$A)&&($A[0]<1024||$A[0]>65535))auth_error(lang(95),$tg);check_invalid_login($tg);$yb=adminer()->credentials();$e=Driver::connect($yb[0],$yb[1],$yb[2]);if(is_object($e)){Db::$instance=$e;Driver::$instance=new
Driver($e);if($e->flavor)save_settings(array("vendor-".DRIVER."-".SERVER=>get_driver(DRIVER)));}}$He=null;if(!is_object($e)||($He=adminer()->login($_GET["username"],get_password()))!==true){$k=(is_string($e)?nl_br(h($e)):(is_string($He)?$He:lang(96))).(preg_match('~^ | $~',get_password())?'<br>'.lang(97):'');auth_error($k,$tg);}if($_POST["logout"]&&$_SESSION["token"]&&!verify_token()){page_header(lang(84),lang(98));page_footer("db");exit;}if(!$_SESSION["token"])$_SESSION["token"]=rand(1,1e6);stop_session(true);if($ya&&$_POST["token"])$_POST["token"]=get_token();$k='';if($_POST){if(!verify_token()){$Td="max_input_vars";$Ue=ini_get($Td);if(extension_loaded("suhosin")){foreach(array("suhosin.request.max_vars","suhosin.post.max_vars")as$w){$X=ini_get($w);if($X&&(!$Ue||$X<$Ue)){$Td=$w;$Ue=$X;}}}$k=(!$_POST["token"]&&$Ue?lang(99,"'$Td'"):lang(98).' '.lang(100));}}elseif($_SERVER["REQUEST_METHOD"]=="POST"){$k=lang(101,"'post_max_size'");if(isset($_GET["sql"]))$k
.=' '.lang(102);}function
print_select_result($I,$f=null,array$Tf=array(),$y=0){$De=array();$v=array();$c=array();$Ka=array();$Gi=array();$J=array();for($r=0;(!$y||$r<$y)&&($K=$I->fetch_row());$r++){if(!$r){echo"<div class='scrollable'>\n","<table class='nowrap odds'>\n","<thead><tr>";for($ie=0;$ie<count($K);$ie++){$l=$I->fetch_field();$C=$l->name;$Sf=(isset($l->orgtable)?$l->orgtable:"");$Rf=(isset($l->orgname)?$l->orgname:$C);if($Tf&&JUSH=="sql")$De[$ie]=($C=="table"?"table=":($C=="possible_keys"?"indexes=":null));elseif($Sf!=""){if(isset($l->table))$J[$l->table]=$Sf;if(!isset($v[$Sf])){$v[$Sf]=array();foreach(indexes($Sf,$f)as$u){if($u["type"]=="PRIMARY"){$v[$Sf]=array_flip($u["columns"]);break;}}$c[$Sf]=$v[$Sf];}if(isset($c[$Sf][$Rf])){unset($c[$Sf][$Rf]);$v[$Sf][$Rf]=$ie;$De[$ie]=$Sf;}}if($l->charsetnr==63)$Ka[$ie]=true;$Gi[$ie]=$l->type;echo"<th".($Sf!=""||$l->name!=$Rf?" title='".h(($Sf!=""?"$Sf.":"").$Rf)."'":"").">".h($C).($Tf?doc_link(array('sql'=>"explain-output.html#explain_".strtolower($C),'mariadb'=>"explain/#the-columns-in-explain-select",)):"");}echo"</thead>\n";}echo"<tr>";foreach($K
as$w=>$X){$z="";if(isset($De[$w])&&!$c[$De[$w]]){if($Tf&&JUSH=="sql"){$R=$K[array_search("table=",$De)];$z=ME.$De[$w].urlencode($Tf[$R]!=""?$Tf[$R]:$R);}else{$z=ME."edit=".urlencode($De[$w]);foreach($v[$De[$w]]as$cb=>$ie){if($K[$ie]===null){$z="";break;}$z
.="&where".urlencode("[".bracket_escape($cb)."]")."=".urlencode($K[$ie]);}}}$l=array('type'=>($Ka[$w]?'blob':($Gi[$w]==254?'char':'')),);$X=select_value($X,$z,$l,null);echo"<td".($Gi[$w]<=9||$Gi[$w]==246?" class='number'":"").">$X";}}echo($r?"</table>\n</div>":"<p class='message'>".lang(14))."\n";return$J;}function
referencable_primary($sh){$J=array();foreach(table_status('',true)as$Yh=>$R){if($Yh!=$sh&&fk_support($R)){foreach(fields($Yh)as$l){if($l["primary"]){if($J[$Yh]){unset($J[$Yh]);break;}$J[$Yh]=$l;}}}}return$J;}function
textarea($C,$Y,$L=10,$gb=80){echo"<textarea name='".h($C)."' rows='$L' cols='$gb' class='sqlarea jush-".JUSH."' spellcheck='false' wrap='off'>";if(is_array($Y)){foreach($Y
as$X)echo
h($X[0])."\n\n\n";}else
echo
h($Y);echo"</textarea>";}function
select_input($xa,array$Of,$Y="",$If="",$ug=""){$fi=($Of?"select":"input");return"<$fi$xa".($Of?"><option value=''>$ug".optionlist($Of,$Y,true)."</select>":" size='10' value='".h($Y)."' placeholder='$ug'>").($If?script("qsl('$fi').onchange = $If;",""):"");}function
json_row($w,$X=null,$xc=true){static$Uc=true;if($Uc)echo"{";if($w!=""){echo($Uc?"":",")."\n\t\"".addcslashes($w,"\r\n\t\"\\/").'": '.($X!==null?($xc?'"'.addcslashes($X,"\r\n\"\\/").'"':$X):'null');$Uc=false;}else{echo"\n}\n";$Uc=true;}}function
edit_type($w,array$l,array$fb,array$bd=array(),array$Ic=array()){$U=$l["type"];echo"<td><select name='".h($w)."[type]' class='type' aria-labelledby='label-type'>";if($U&&!array_key_exists($U,driver()->types())&&!isset($bd[$U])&&!in_array($U,$Ic))$Ic[]=$U;$Oh=driver()->structuredTypes();if($bd)$Oh[lang(103)]=$bd;echo
optionlist(array_merge($Ic,$Oh),$U),"</select><td>","<input name='".h($w)."[length]' value='".h($l["length"])."' size='3'".(!$l["length"]&&preg_match('~var(char|binary)$~',$U)?" class='required'":"")." aria-labelledby='label-length'>","<td class='options'>",($fb?"<input list='collations' name='".h($w)."[collation]'".(preg_match('~(char|text|enum|set)$~',$U)?"":" class='hidden'")." value='".h($l["collation"])."' placeholder='(".lang(104).")'>":''),(driver()->unsigned?"<select name='".h($w)."[unsigned]'".(!$U||preg_match(number_type(),$U)?"":" class='hidden'").'><option>'.optionlist(driver()->unsigned,$l["unsigned"]).'</select>':''),(isset($l['on_update'])?"<select name='".h($w)."[on_update]'".(preg_match('~timestamp|datetime~',$U)?"":" class='hidden'").'>'.optionlist(array(""=>"(".lang(105).")","CURRENT_TIMESTAMP"),(preg_match('~^CURRENT_TIMESTAMP~i',$l["on_update"])?"CURRENT_TIMESTAMP":$l["on_update"])).'</select>':''),($bd?"<select name='".h($w)."[on_delete]'".(preg_match("~`~",$U)?"":" class='hidden'")."><option value=''>(".lang(106).")".optionlist(explode("|",driver()->onActions),$l["on_delete"])."</select> ":" ");}function
process_length($x){$sc=driver()->enumLength;return(preg_match("~^\\s*\\(?\\s*$sc(?:\\s*,\\s*$sc)*+\\s*\\)?\\s*\$~",$x)&&preg_match_all("~$sc~",$x,$Me)?"(".implode(",",$Me[0]).")":preg_replace('~^[0-9].*~','(\0)',preg_replace('~[^-0-9,+()[\]]~','',$x)));}function
process_type(array$l,$db="COLLATE"){return" $l[type]".process_length($l["length"]).(preg_match(number_type(),$l["type"])&&in_array($l["unsigned"],driver()->unsigned)?" $l[unsigned]":"").(preg_match('~char|text|enum|set~',$l["type"])&&$l["collation"]?" $db ".(JUSH=="mssql"?$l["collation"]:q($l["collation"])):"");}function
process_field(array$l,array$Fi){if($l["on_update"])$l["on_update"]=str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",$l["on_update"]);return
array(idf_escape(trim($l["field"])),process_type($Fi),($l["null"]?" NULL":" NOT NULL"),default_value($l),(preg_match('~timestamp|datetime~',$l["type"])&&$l["on_update"]?" ON UPDATE $l[on_update]":""),(support("comment")&&$l["comment"]!=""?" COMMENT ".q($l["comment"]):""),($l["auto_increment"]?auto_increment():null),);}function
default_value(array$l){if($l["default"]===null)return"";$j=str_replace("\r","",$l["default"]);$id=$l["generated"];return(in_array($id,driver()->generated)?(JUSH=="mssql"?" AS ($j)".($id=="VIRTUAL"?"":" $id"):" GENERATED ALWAYS AS ($j) $id"):(preg_match('~^GENERATED ~i',$j)?" $j":" DEFAULT ".(preg_match('~char|binary|text|json|enum|set~',$l["type"])||preg_match('~^(?![a-z])~i',$j)?(JUSH=="sql"&&preg_match('~text|json~',$l["type"])?"(".q($j).")":q($j)):str_ireplace("current_timestamp()","CURRENT_TIMESTAMP",(JUSH=="sqlite"?"($j)":$j)))));}function
type_class($U){foreach(array('char'=>'text','date'=>'time|year','binary'=>'blob','enum'=>'set',)as$w=>$X){if(preg_match("~$w|$X~",$U))return" class='$w'";}}function
edit_fields(array$m,array$fb,$U="TABLE",array$bd=array()){$m=array_values($m);$Lb=(($_POST?$_POST["defaults"]:get_setting("defaults"))?"":" class='hidden'");$kb=(($_POST?$_POST["comments"]:get_setting("comments"))?"":" class='hidden'");echo"<thead><tr>\n",($U=="PROCEDURE"?"<td>":""),"<th id='label-name'>".($U=="TABLE"?lang(107):lang(108)),"<td id='label-type'>".lang(41)."<textarea id='enum-edit' rows='4' cols='12' wrap='off' style='display: none;'></textarea>".script("qs('#enum-edit').onblur = editingLengthBlur;"),"<td id='label-length'>".lang(109),"<td>".lang(110);if($U=="TABLE")echo"<td id='label-null'>NULL\n","<td><input type='radio' name='auto_increment_col' value=''><abbr id='label-ai' title='".lang(43)."'>AI</abbr>",doc_link(array('sql'=>"example-auto-increment.html",'mariadb'=>"auto_increment/",)),"<td id='label-default'$Lb>".lang(44),(support("comment")?"<td id='label-comment'$kb>".lang(42):"");echo"<td>".icon("plus","add[".(support("move_col")?0:count($m))."]","+",lang(111)),"</thead>\n<tbody>\n",script("mixin(qsl('tbody'), {onclick: editingClick, onkeydown: editingKeydown, oninput: editingInput});");foreach($m
as$r=>$l){$r++;$Uf=$l[($_POST?"orig":"field")];$Tb=(isset($_POST["add"][$r-1])||(isset($l["field"])&&!idx($_POST["drop_col"],$r)))&&(support("drop_col")||$Uf=="");echo"<tr".($Tb?"":" style='display: none;'").">\n",($U=="PROCEDURE"?"<td>".html_select("fields[$r][inout]",explode("|",driver()->inout),$l["inout"]):"")."<th>",(support("move_col")?icon("move","","↕",lang(112))." ":"");if($Tb)echo"<input name='fields[$r][field]' value='".h($l["field"])."' data-maxlength='64' autocapitalize='off' aria-labelledby='label-name'".(isset($_POST["add"][$r-1])?" autofocus":"").">";echo
input_hidden("fields[$r][orig]",$Uf);edit_type("fields[$r]",$l,$fb,$bd);if($U=="TABLE"){echo"<td>".checkbox("fields[$r][null]",1,$l["null"],"","","block","label-null"),"<td><label class='block'><input type='radio' name='auto_increment_col' value='$r'".($l["auto_increment"]?" checked":"")." aria-labelledby='label-ai'></label>","<td$Lb>".(driver()->generated?html_select("fields[$r][generated]",array_merge(array("","DEFAULT"),driver()->generated),$l["generated"])." ":checkbox("fields[$r][generated]",1,$l["generated"],"","","","label-default"));$xa=" name='fields[$r][default]' aria-labelledby='label-default'";$Y=h($l["default"]);echo(preg_match('~\n~',$l["default"])?"<textarea$xa rows='2' cols='30' style='vertical-align: bottom;'>\n$Y</textarea>":"<input$xa value='$Y'>"),(support("comment")?"<td$kb><input name='fields[$r][comment]' value='".h($l["comment"])."' data-maxlength='".(min_version(5.5)?1024:255)."' aria-labelledby='label-comment'>":"");}echo"<td>",(support("move_col")?icon("plus","add[$r]","+",lang(111))." ":""),($Uf==""||support("drop_col")?icon("cross","drop_col[$r]","x",lang(113)):"");}}function
process_fields(array&$m){if($_POST["add"]){$m=array_values($m);array_splice($m,key($_POST["add"]),0,array(array()));}return$_POST["add"]||$_POST["drop_col"];}function
normalize_enum(array$A){$X=$A[0];return"'".str_replace("'","''",addcslashes(stripcslashes(str_replace($X[0].$X[0],$X[0],substr($X,1,-1))),'\\'))."'";}function
grant($kd,array$Hg,$c,$Gf){if(!$Hg)return
true;if($Hg==array("ALL PRIVILEGES","GRANT OPTION"))return($kd=="GRANT"?queries("$kd ALL PRIVILEGES$Gf WITH GRANT OPTION"):queries("$kd ALL PRIVILEGES$Gf")&&queries("$kd GRANT OPTION$Gf"));return
queries("$kd ".preg_replace('~(GRANT OPTION)\([^)]*\)~','\1',implode("$c, ",$Hg).$c).$Gf);}function
drop_create($dc,$g,$ec,$ji,$fc,$_,$bf,$Ze,$af,$Ef,$sf){if($_POST["drop"])query_redirect($dc,$_,$bf);elseif($Ef=="")query_redirect($g,$_,$af);elseif($Ef!=$sf){$xb=queries($g);queries_redirect($_,$Ze,$xb&&queries($dc));if($xb)queries($ec);}else
queries_redirect($_,$Ze,queries($ji)&&queries($fc)&&queries($dc)&&queries($g));}function
create_trigger($Gf,array$K){$oi=" $K[Timing] $K[Event]".(preg_match('~ OF~',$K["Event"])?" $K[Of]":"");return"CREATE TRIGGER ".idf_escape($K["Trigger"]).(JUSH=="mssql"?$Gf.$oi:$oi.$Gf).rtrim(" $K[Type]\n$K[Statement]",";").";";}function
create_routine($fh,array$K){$O=array();$m=(array)$K["fields"];ksort($m);foreach($m
as$l){if($l["field"]!="")$O[]=(preg_match("~^(".driver()->inout.")\$~",$l["inout"])?"$l[inout] ":"").idf_escape($l["field"]).process_type($l,"CHARACTER SET");}$Nb=rtrim($K["definition"],";");return"CREATE $fh ".idf_escape(trim($K["name"]))." (".implode(", ",$O).")".($fh=="FUNCTION"?" RETURNS".process_type($K["returns"],"CHARACTER SET"):"").($K["language"]?" LANGUAGE $K[language]":"").(JUSH=="pgsql"?" AS ".q($Nb):"\n$Nb;");}function
remove_definer($H){return
preg_replace('~^([A-Z =]+) DEFINER=`'.preg_replace('~@(.*)~','`@`(%|\1)',logged_user()).'`~','\1',$H);}function
format_foreign_key(array$n){$i=$n["db"];$xf=$n["ns"];return" FOREIGN KEY (".implode(", ",array_map('Adminer\idf_escape',$n["source"])).") REFERENCES ".($i!=""&&$i!=$_GET["db"]?idf_escape($i).".":"").($xf!=""&&$xf!=$_GET["ns"]?idf_escape($xf).".":"").idf_escape($n["table"])." (".implode(", ",array_map('Adminer\idf_escape',$n["target"])).")".(preg_match("~^(".driver()->onActions.")\$~",$n["on_delete"])?" ON DELETE $n[on_delete]":"").(preg_match("~^(".driver()->onActions.")\$~",$n["on_update"])?" ON UPDATE $n[on_update]":"").($n["deferrable"]?" $n[deferrable]":"");}function
tar_file($Rc,$ti){$J=pack("a100a8a8a8a12a12",$Rc,644,0,0,decoct($ti->size),decoct(time()));$Ya=8*32;for($r=0;$r<strlen($J);$r++)$Ya+=ord($J[$r]);$J
.=sprintf("%06o",$Ya)."\0 ";echo$J,str_repeat("\0",512-strlen($J));$ti->send();echo
str_repeat("\0",511-($ti->size+511)%512);}function
doc_link(array$qg,$ki="<sup>?</sup>"){$vh=connection()->server_info;$ej=preg_replace('~^(\d\.?\d).*~s','\1',$vh);$Ti=array('sql'=>"https://dev.mysql.com/doc/refman/$ej/en/",'sqlite'=>"https://www.sqlite.org/",'pgsql'=>"https://www.postgresql.org/docs/".(connection()->flavor=='cockroach'?"current":$ej)."/",'mssql'=>"https://learn.microsoft.com/en-us/sql/",'oracle'=>"https://www.oracle.com/pls/topic/lookup?ctx=db".preg_replace('~^.* (\d+)\.(\d+)\.\d+\.\d+\.\d+.*~s','\1\2',$vh)."&id=",);if(connection()->flavor=='maria'){$Ti['sql']="https://mariadb.com/kb/en/";$qg['sql']=(isset($qg['mariadb'])?$qg['mariadb']:str_replace(".html","/",$qg['sql']));}return($qg[JUSH]?"<a href='".h($Ti[JUSH].$qg[JUSH].(JUSH=='mssql'?"?view=sql-server-ver$ej":""))."'".target_blank().">$ki</a>":"");}function
db_size($i){if(!connection()->select_db($i))return"?";$J=0;foreach(table_status()as$S)$J+=$S["Data_length"]+$S["Index_length"];return
format_number($J);}function
set_utf8mb4($g){static$O=false;if(!$O&&preg_match('~\butf8mb4~i',$g)){$O=true;echo"SET NAMES ".charset(connection()).";\n\n";}}if(isset($_GET["status"]))$_GET["variables"]=$_GET["status"];if(isset($_GET["import"]))$_GET["sql"]=$_GET["import"];if(!(DB!=""?connection()->select_db(DB):isset($_GET["sql"])||isset($_GET["dump"])||isset($_GET["database"])||isset($_GET["processlist"])||isset($_GET["privileges"])||isset($_GET["user"])||isset($_GET["variables"])||$_GET["script"]=="connect"||$_GET["script"]=="kill")){if(DB!=""||$_GET["refresh"]){restart_session();set_session("dbs",null);}if(DB!=""){header("HTTP/1.1 404 Not Found");page_header(lang(29).": ".h(DB),lang(114),true);}else{if($_POST["db"]&&!$k)queries_redirect(substr(ME,0,-1),lang(115),drop_databases($_POST["db"]));page_header(lang(116),$k,false);echo"<p class='links'>\n";foreach(array('database'=>lang(117),'privileges'=>lang(63),'processlist'=>lang(118),'variables'=>lang(119),'status'=>lang(120),)as$w=>$X){if(support($w))echo"<a href='".h(ME)."$w='>$X</a>\n";}echo"<p>".lang(121,get_driver(DRIVER),"<b>".h(connection()->server_info)."</b>","<b>".connection()->extension."</b>")."\n","<p>".lang(122,"<b>".h(logged_user())."</b>")."\n";$h=adminer()->databases();if($h){$lh=support("scheme");$fb=collations();echo"<form action='' method='post'>\n","<table class='checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),"<thead><tr>".(support("database")?"<td>":"")."<th>".lang(29).(get_session("dbs")!==null?" - <a href='".h(ME)."refresh=1'>".lang(123)."</a>":"")."<td>".lang(124)."<td>".lang(125)."<td>".lang(126)." - <a href='".h(ME)."dbsize=1'>".lang(127)."</a>".script("qsl('a').onclick = partial(ajaxSetHtml, '".js_escape(ME)."script=connect');","")."</thead>\n";$h=($_GET["dbsize"]?count_tables($h):array_flip($h));foreach($h
as$i=>$T){$eh=h(ME)."db=".urlencode($i);$s=h("Db-".$i);echo"<tr>".(support("database")?"<td>".checkbox("db[]",$i,in_array($i,(array)$_POST["db"]),"","","",$s):""),"<th><a href='$eh' id='$s'>".h($i)."</a>";$eb=h(db_collation($i,$fb));echo"<td>".(support("database")?"<a href='$eh".($lh?"&amp;ns=":"")."&amp;database=' title='".lang(59)."'>$eb</a>":$eb),"<td align='right'><a href='$eh&amp;schema=' id='tables-".h($i)."' title='".lang(62)."'>".($_GET["dbsize"]?$T:"?")."</a>","<td align='right' id='size-".h($i)."'>".($_GET["dbsize"]?db_size($i):"?"),"\n";}echo"</table>\n",(support("database")?"<div class='footer'><div>\n"."<fieldset><legend>".lang(128)." <span id='selected'></span></legend><div>\n".input_hidden("all").script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^db/)); };")."<input type='submit' name='drop' value='".lang(129)."'>".confirm()."\n"."</div></fieldset>\n"."</div></div>\n":""),input_token(),"</form>\n",script("tableCheck();");}if(!empty(adminer()->plugins)){echo"<div class='plugins'>\n","<h3>".lang(130)."</h3>\n<ul>\n";foreach(adminer()->plugins
as$vg){$Rb=(method_exists($vg,'description')?$vg->description():"");if(!$Rb){$Vg=new
\ReflectionObject($vg);if(preg_match('~^/[\s*]+(.+)~',$Vg->getDocComment(),$A))$Rb=$A[1];}$mh=(method_exists($vg,'screenshot')?$vg->screenshot():"");echo"<li><b>".get_class($vg)."</b>".h($Rb?": $Rb":"").($mh?" (<a href='".h($mh)."'".target_blank().">".lang(131)."</a>)":"")."\n";}echo"</ul>\n";adminer()->pluginsLinks();echo"</div>\n";}}page_footer("db");exit;}adminer()->afterConnect();class
TmpFile{private$handler;var$size;function
__construct(){$this->handler=tmpfile();}function
write($rb){$this->size+=strlen($rb);fwrite($this->handler,$rb);}function
send(){fseek($this->handler,0);fpassthru($this->handler);fclose($this->handler);}}if(isset($_GET["select"])&&($_POST["edit"]||$_POST["clone"])&&!$_POST["save"])$_GET["edit"]=$_GET["select"];if(isset($_GET["callf"]))$_GET["call"]=$_GET["callf"];if(isset($_GET["function"]))$_GET["procedure"]=$_GET["function"];if(isset($_GET["download"])){$a=$_GET["download"];$m=fields($a);header("Content-Type: application/octet-stream");header("Content-Disposition: attachment; filename=".friendly_url("$a-".implode("_",$_GET["where"])).".".friendly_url($_GET["field"]));$M=array(idf_escape($_GET["field"]));$I=driver()->select($a,$M,array(where($_GET,$m)),$M);$K=($I?$I->fetch_row():array());echo
driver()->value($K[0],$m[$_GET["field"]]);exit;}elseif(isset($_GET["table"])){$a=$_GET["table"];$m=fields($a);if(!$m)$k=error()?:lang(11);$S=table_status1($a);$C=adminer()->tableName($S);page_header(($m&&is_view($S)?$S['Engine']=='materialized view'?lang(132):lang(133):lang(134)).": ".($C!=""?$C:h($a)),$k);$dh=array();foreach($m
as$w=>$l)$dh+=$l["privileges"];adminer()->selectLinks($S,(isset($dh["insert"])||!support("table")?"":null));$jb=$S["Comment"];if($jb!="")echo"<p class='nowrap'>".lang(42).": ".h($jb)."\n";if($m)adminer()->tableStructurePrint($m,$S);function
tables_links(array$T){echo"<ul>\n";foreach($T
as$K){$z=preg_replace('~ns=[^&]*~',"ns=".urlencode($K["ns"]),ME);echo"<li><a href='".h($z."table=".urlencode($K["table"]))."'>".($K["ns"]!=$_GET["ns"]?"<b>".h($K["ns"])."</b>.":"").h($K["table"])."</a>";}echo"</ul>\n";}$Sd=driver()->inheritsFrom($a);if($Sd){echo"<h3>".lang(135)."</h3>\n";tables_links($Sd);}if(support("indexes")&&driver()->supportsIndex($S)){echo"<h3 id='indexes'>".lang(136)."</h3>\n";$v=indexes($a);if($v)adminer()->tableIndexesPrint($v,$S);echo'<p class="links"><a href="'.h(ME).'indexes='.urlencode($a).'">'.lang(137)."</a>\n";}if(!is_view($S)){if(fk_support($S)){echo"<h3 id='foreign-keys'>".lang(103)."</h3>\n";$bd=foreign_keys($a);if($bd){echo"<table>\n","<thead><tr><th>".lang(138)."<td>".lang(139)."<td>".lang(106)."<td>".lang(105)."<td></thead>\n";foreach($bd
as$C=>$n){echo"<tr title='".h($C)."'>","<th><i>".implode("</i>, <i>",array_map('Adminer\h',$n["source"]))."</i>";$z=($n["db"]!=""?preg_replace('~db=[^&]*~',"db=".urlencode($n["db"]),ME):($n["ns"]!=""?preg_replace('~ns=[^&]*~',"ns=".urlencode($n["ns"]),ME):ME));echo"<td><a href='".h($z."table=".urlencode($n["table"]))."'>".($n["db"]!=""&&$n["db"]!=DB?"<b>".h($n["db"])."</b>.":"").($n["ns"]!=""&&$n["ns"]!=$_GET["ns"]?"<b>".h($n["ns"])."</b>.":"").h($n["table"])."</a>","(<i>".implode("</i>, <i>",array_map('Adminer\h',$n["target"]))."</i>)","<td>".h($n["on_delete"]),"<td>".h($n["on_update"]),'<td><a href="'.h(ME.'foreign='.urlencode($a).'&name='.urlencode($C)).'">'.lang(140).'</a>',"\n";}echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'foreign='.urlencode($a).'">'.lang(141)."</a>\n";}if(support("check")){echo"<h3 id='checks'>".lang(142)."</h3>\n";$Va=driver()->checkConstraints($a);if($Va){echo"<table>\n";foreach($Va
as$w=>$X)echo"<tr title='".h($w)."'>","<td><code class='jush-".JUSH."'>".h($X),"<td><a href='".h(ME.'check='.urlencode($a).'&name='.urlencode($w))."'>".lang(140)."</a>","\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'check='.urlencode($a).'">'.lang(143)."</a>\n";}}if(support(is_view($S)?"view_trigger":"trigger")){echo"<h3 id='triggers'>".lang(144)."</h3>\n";$Di=triggers($a);if($Di){echo"<table>\n";foreach($Di
as$w=>$X)echo"<tr valign='top'><td>".h($X[0])."<td>".h($X[1])."<th>".h($w)."<td><a href='".h(ME.'trigger='.urlencode($a).'&name='.urlencode($w))."'>".lang(140)."</a>\n";echo"</table>\n";}echo'<p class="links"><a href="'.h(ME).'trigger='.urlencode($a).'">'.lang(145)."</a>\n";}$Rd=driver()->inheritedTables($a);if($Rd){echo"<h3 id='partitions'>".lang(146)."</h3>\n";$ig=driver()->partitionsInfo($a);if($ig)echo"<p><code class='jush-".JUSH."'>BY ".h("$ig[partition_by]($ig[partition])")."</code>\n";tables_links($Rd);}}elseif(isset($_GET["schema"])){page_header(lang(62),"",array(),h(DB.($_GET["ns"]?".$_GET[ns]":"")));$Zh=array();$ai=array();$Nc=array();$da=($_GET["schema"]?:$_COOKIE["adminer_schema-".str_replace(".","_",DB)]);preg_match_all('~([^:]+):([-0-9.]+)x([-0-9.]+)(_|$)~',$da,$Me,PREG_SET_ORDER);foreach($Me
as$r=>$A){$Zh[$A[1]]=array((float)$A[2],(float)$A[3]);$ai[]="\n\t'".js_escape($A[1])."': [ $A[2], $A[3] ]";}$wi=0;$Ga=-1;$kh=array();$Ug=array();$xe=array();$pa=driver()->allFields();foreach(table_status('',true)as$R=>$S){if(is_view($S))continue;$G=0;$kh[$R]["fields"]=array();foreach($pa[$R]as$l){$G+=1.25;$Nc[$R][$l["field"]]=$G;$kh[$R]["fields"][$l["field"]]=$l;}$kh[$R]["pos"]=($Zh[$R]?:array($wi,0));foreach(adminer()->foreignKeys($R)as$X){if(!$X["db"]){$ve=$Ga;if(idx($Zh[$R],1)||idx($Zh[$X["table"]],1))$ve=min(idx($Zh[$R],1,0),idx($Zh[$X["table"]],1,0))-1;else$Ga-=.1;while($xe[(string)$ve])$ve-=.0001;$kh[$R]["references"][$X["table"]][(string)$ve]=array($X["source"],$X["target"]);$Ug[$X["table"]][$R][(string)$ve]=$X["target"];$xe[(string)$ve]=true;}}$wi=max($wi,$kh[$R]["pos"][0]+2.5+$G);}echo'<div id="schema" style="height: ',$wi,'em;">
<script',nonce(),'>
qs(\'#schema\').onselectstart = () => false;
const tablePos = {',implode(",",$ai)."\n",'};
const em = qs(\'#schema\').offsetHeight / ',$wi,';
document.onmousemove = schemaMousemove;
document.onmouseup = partialArg(schemaMouseup, \'',js_escape(DB),'\');
</script>
';foreach($kh
as$C=>$R){echo"<div class='table' style='top: ".$R["pos"][0]."em; left: ".$R["pos"][1]."em;'>",'<a href="'.h(ME).'table='.urlencode($C).'"><b>'.h($C)."</b></a>",script("qsl('div').onmousedown = schemaMousedown;");foreach($R["fields"]as$l){$X='<span'.type_class($l["type"]).' title="'.h($l["type"].($l["length"]?"($l[length])":"").($l["null"]?" NULL":'')).'">'.h($l["field"]).'</span>';echo"<br>".($l["primary"]?"<i>$X</i>":$X);}foreach((array)$R["references"]as$hi=>$Wg){foreach($Wg
as$ve=>$Rg){$we=$ve-idx($Zh[$C],1);$r=0;foreach($Rg[0]as$Eh)echo"\n<div class='references' title='".h($hi)."' id='refs$ve-".($r++)."' style='left: $we"."em; top: ".$Nc[$C][$Eh]."em; padding-top: .5em;'>"."<div style='border-top: 1px solid gray; width: ".(-$we)."em;'></div></div>";}}foreach((array)$Ug[$C]as$hi=>$Wg){foreach($Wg
as$ve=>$c){$we=$ve-idx($Zh[$C],1);$r=0;foreach($c
as$gi)echo"\n<div class='references arrow' title='".h($hi)."' id='refd$ve-".($r++)."' style='left: $we"."em; top: ".$Nc[$C][$gi]."em;'>"."<div style='height: .5em; border-bottom: 1px solid gray; width: ".(-$we)."em;'></div>"."</div>";}}echo"\n</div>\n";}foreach($kh
as$C=>$R){foreach((array)$R["references"]as$hi=>$Wg){if($kh[$hi]){foreach($Wg
as$ve=>$Rg){$hf=$wi;$Se=-10;foreach($Rg[0]as$w=>$Eh){$yg=$R["pos"][0]+$Nc[$C][$Eh];$zg=$kh[$hi]["pos"][0]+$Nc[$hi][$Rg[1][$w]];$hf=min($hf,$yg,$zg);$Se=max($Se,$yg,$zg);}echo"<div class='references' id='refl$ve' style='left: $ve"."em; top: $hf"."em; padding: .5em 0;'><div style='border-right: 1px solid gray; margin-top: 1px; height: ".($Se-$hf)."em;'></div></div>\n";}}}}echo'</div>
<p class="links"><a href="',h(ME."schema=".urlencode($da)),'" id="schema-link">',lang(147),'</a>
';}elseif(isset($_GET["dump"])){$a=$_GET["dump"];if($_POST&&!$k){$j=array("auto_increment"=>'');foreach(array("type","routine","event","trigger")as$Uh){if(support($Uh))$j[$Uh."s"]='';}save_settings(array_intersect_key($_POST+$j,array_flip(array("output","format","db_style","table_style","data_style"))+$j),"adminer_export");$T=array_flip((array)$_POST["tables"])+array_flip((array)$_POST["data"]);$Ec=dump_headers((count($T)==1?key($T):DB),(DB==""||$_GET["ns"]===""||count($T)>1));$fe=preg_match('~sql~',$_POST["format"]);if($fe){echo"-- Adminer ".VERSION." ".get_driver(DRIVER)." ".str_replace("\n"," ",connection()->server_info)." dump\n\n";if(JUSH=="sql"){echo"SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
".($_POST["data_style"]?"SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
":"")."
";connection()->query("SET time_zone = '+00:00'");connection()->query("SET sql_mode = ''");}}$Ph=$_POST["db_style"];$h=array(DB);if(DB==""){$h=$_POST["databases"];if(is_string($h))$h=explode("\n",rtrim(str_replace("\r","",$h),"\n"));}foreach((array)$h
as$i){adminer()->dumpDatabase($i);if(connection()->select_db($i)){if($fe){if($Ph)echo
use_sql($i,$Ph).";\n\n";$ag="";if($_POST["types"]){foreach(types()as$s=>$U){$tc=type_values($s);if($tc)$ag
.=($Ph!='DROP+CREATE'?"DROP TYPE IF EXISTS ".idf_escape($U).";;\n":"")."CREATE TYPE ".idf_escape($U)." AS ENUM ($tc);\n\n";else$ag
.="-- Could not export type $U\n\n";}}if($_POST["routines"]){foreach(routines()as$K){$C=$K["ROUTINE_NAME"];$fh=$K["ROUTINE_TYPE"];$g=create_routine($fh,array("name"=>$C)+routine($K["SPECIFIC_NAME"],$fh));set_utf8mb4($g);$ag
.=($Ph!='DROP+CREATE'?"DROP $fh IF EXISTS ".idf_escape($C).";;\n":"")."$g;\n\n";}}if($_POST["events"]){foreach(get_rows("SHOW EVENTS",null,"-- ")as$K){$g=remove_definer(get_val("SHOW CREATE EVENT ".idf_escape($K["Name"]),3));set_utf8mb4($g);$ag
.=($Ph!='DROP+CREATE'?"DROP EVENT IF EXISTS ".idf_escape($K["Name"]).";;\n":"")."$g;;\n\n";}}echo($ag&&JUSH=='sql'?"DELIMITER ;;\n\n$ag"."DELIMITER ;\n\n":$ag);}if($_POST["table_style"]||$_POST["data_style"]){foreach(($_GET["ns"]===""?(array)$_POST["schemas"]:(DB!=""||!support("scheme")?array(""):adminer()->schemas()))as$kh){if($kh!=""){set_schema($kh);if(DB==""&&(information_schema(DB)||$kh=="pg_catalog"))continue;}$gj=array();foreach(table_status('',true)as$C=>$S){$R=(DB==""||$_GET["ns"]===""||in_array($C,(array)$_POST["tables"]));$Db=(DB==""||$_GET["ns"]===""||in_array($C,(array)$_POST["data"]));if($R||$Db){$ti=null;if($Ec=="tar"){$ti=new
TmpFile;ob_start(array($ti,'write'),1e5);}adminer()->dumpTable($C,($R?$_POST["table_style"]:""),(is_view($S)?2:0));if(is_view($S))$gj[]=$C;elseif($Db){$m=fields($C);adminer()->dumpData($C,$_POST["data_style"],"SELECT *".convert_fields($m,$m)." FROM ".table($C));}if($fe&&$_POST["triggers"]&&$R&&($Di=trigger_sql($C)))echo"\nDELIMITER ;;\n$Di\nDELIMITER ;\n";if($Ec=="tar"){ob_end_flush();tar_file((DB!=""?"":"$i/")."$C.csv",$ti);}elseif($fe)echo"\n";}}if(function_exists('Adminer\foreign_keys_sql')){foreach(table_status('',true)as$C=>$S){$R=(DB==""||$_GET["ns"]===""||in_array($C,(array)$_POST["tables"]));if($R&&!is_view($S))echo
foreign_keys_sql($C);}}foreach($gj
as$fj)adminer()->dumpTable($fj,$_POST["table_style"],1);if($Ec=="tar")echo
pack("x512");}}}}adminer()->dumpFooter();exit;}page_header(lang(68),$k,($_GET["export"]!=""?array("table"=>$_GET["export"]):array()),h(DB));echo'
<form action="" method="post">
<table class="layout">
';$Hb=array('','USE','DROP+CREATE','CREATE');$bi=array('','DROP+CREATE','CREATE');$Eb=array('','TRUNCATE+INSERT','INSERT');if(JUSH=="sql")$Eb[]='INSERT+UPDATE';$K=get_settings("adminer_export");if(!$K)$K=array("output"=>"text","format"=>"sql","db_style"=>(DB!=""?"":"CREATE"),"table_style"=>"DROP+CREATE","data_style"=>"INSERT");echo"<tr><th>".lang(148)."<td>".html_radios("output",adminer()->dumpOutput(),$K["output"])."\n","<tr><th>".lang(149)."<td>".html_radios("format",adminer()->dumpFormat(),$K["format"])."\n",(JUSH=="sqlite"?"":"<tr><th>".lang(29)."<td>".html_select('db_style',$Hb,$K["db_style"]).(support("type")?checkbox("types",1,$K["types"],lang(6)):"").(support("routine")?checkbox("routines",1,$K["routines"],lang(64)):"").(support("event")?checkbox("events",1,$K["events"],lang(66)):"")),"<tr><th>".lang(125)."<td>".html_select('table_style',$bi,$K["table_style"]).checkbox("auto_increment",1,$K["auto_increment"],lang(43)).(support("trigger")?checkbox("triggers",1,$K["triggers"],lang(144)):""),"<tr><th>".lang(150)."<td>".html_select('data_style',$Eb,$K["data_style"]),'</table>
<p><input type="submit" value="',lang(68),'">
',input_token(),'
<table>
',script("qsl('table').onclick = dumpClick;");$Cg=array();if($_GET["ns"]===""){echo"<thead><tr><th style='text-align: left;'>","<label class='block'><input type='checkbox' id='check-schemas' checked class='jsonly'>".lang(151)."</label>","</thead>\n",script("qs('#check-schemas').onclick = partial(formCheck, /^schemas\\[/);");foreach(adminer()->schemas()as$kh)echo"<tr><td>".checkbox("schemas[]",$kh,true,$kh,"","block")."\n";}elseif(DB!=""){$Wa=($a!=""?"":" checked");echo"<thead><tr>","<th style='text-align: left;'><label class='block'><input type='checkbox' id='check-tables'$Wa class='jsonly'>".lang(134)."</label>".script("qs('#check-tables').onclick = partial(formCheck, /^tables\\[/);",""),"<th style='text-align: right;'><label class='block'>".lang(150)."<input type='checkbox' id='check-data'$Wa class='jsonly'></label>".script("qs('#check-data').onclick = partial(formCheck, /^data\\[/);",""),"</thead>\n";$gj="";$di=tables_list();foreach($di
as$C=>$U){$Bg=preg_replace('~_.*~','',$C);$Wa=($a==""||$a==(substr($a,-1)=="%"?"$Bg%":$C));$Eg="<tr><td>".checkbox("tables[]",$C,$Wa,$C,"","block");if($U!==null&&!preg_match('~table~i',$U))$gj
.="$Eg\n";else
echo"$Eg<td align='right'><label class='block'><span id='Rows-".h($C)."'></span>".checkbox("data[]",$C,$Wa)."</label>\n";$Cg[$Bg]++;}echo$gj;if($di)echo
script("ajaxSetHtml('".js_escape(ME)."script=db');");}else{$h=adminer()->databases();echo"<thead><tr><th style='text-align: left;'>","<label class='block'>".($h?"<input type='checkbox' id='check-databases'".($a==""?" checked":"")." class='jsonly'>".script("qs('#check-databases').onclick = partial(formCheck, /^databases\\[/);",""):"").lang(29)."</label>","</thead>\n";if($h){foreach($h
as$i){if(!information_schema($i)){$Bg=preg_replace('~_.*~','',$i);echo"<tr><td>".checkbox("databases[]",$i,$a==""||$a=="$Bg%",$i,"","block")."\n";$Cg[$Bg]++;}}}else
echo"<tr><td><textarea name='databases' rows='10' cols='20'></textarea>";}echo'</table>
</form>
';$Uc=true;foreach($Cg
as$w=>$X){if($w!=""&&$X>1){echo($Uc?"<p>":" ")."<a href='".h(ME)."dump=".urlencode("$w%")."'>".h($w)."</a>";$Uc=false;}}}elseif(isset($_GET["privileges"])){page_header(lang(63));echo'<p class="links"><a href="'.h(ME).'user=">'.lang(152)."</a>";$I=connection()->query("SELECT User, Host FROM mysql.".(DB==""?"user":"db WHERE ".q(DB)." LIKE Db")." ORDER BY Host, User");$kd=$I;if(!$I)$I=connection()->query("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', 1) AS User, SUBSTRING_INDEX(CURRENT_USER, '@', -1) AS Host");echo"<form action=''><p>\n";hidden_fields_get();echo
input_hidden("db",DB),($kd?"":input_hidden("grant")),"<table class='odds'>\n","<thead><tr><th>".lang(27)."<th>".lang(25)."<th></thead>\n";while($K=$I->fetch_assoc())echo'<tr><td>'.h($K["User"])."<td>".h($K["Host"]).'<td><a href="'.h(ME.'user='.urlencode($K["User"]).'&host='.urlencode($K["Host"])).'">'.lang(12)."</a>\n";if(!$kd||DB!="")echo"<tr><td><input name='user' autocapitalize='off'><td><input name='host' value='localhost' autocapitalize='off'><td><input type='submit' value='".lang(12)."'>\n";echo"</table>\n","</form>\n";}elseif(isset($_GET["sql"])){if(!$k&&$_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers("sql");if($_POST["format"]=="sql")echo"$_POST[query]\n";else{adminer()->dumpTable("","");adminer()->dumpData("","table",$_POST["query"]);adminer()->dumpFooter();}exit;}restart_session();$zd=&get_session("queries");$yd=&$zd[DB];if(!$k&&$_POST["clear"]){$yd=array();redirect(remove_from_uri("history"));}stop_session();page_header((isset($_GET["import"])?lang(67):lang(56)),$k);$Ce='--'.(JUSH=='sql'?' ':'');if(!$k&&$_POST){$o=false;if(!isset($_GET["import"]))$H=$_POST["query"];elseif($_POST["webfile"]){$Hh=adminer()->importServerPath();$o=@fopen((file_exists($Hh)?$Hh:"compress.zlib://$Hh.gz"),"rb");$H=($o?fread($o,1e6):false);}else$H=get_file("sql_file",true,";");if(is_string($H)){if(($Xe=ini_bytes("memory_limit"))!="-1")ini_set("memory_limit",max($Xe,strval(2*strlen($H)+memory_get_usage()+8e6)));if($H!=""&&strlen($H)<1e6){$Kg=$H.(preg_match("~;[ \t\r\n]*\$~",$H)?"":";");if(!$yd||first(end($yd))!=$Kg){restart_session();$yd[]=array($Kg,time());set_session("queries",$zd);stop_session();}}$Fh="(?:\\s|/\\*[\s\S]*?\\*/|(?:#|$Ce)[^\n]*\n?|--\r?\n)";$Pb=driver()->delimiter;$Af=0;$oc=true;$f=connect();if($f&&DB!=""){$f->select_db(DB);if($_GET["ns"]!="")set_schema($_GET["ns"],$f);}$ib=0;$vc=array();$gg='[\'"'.(JUSH=="sql"?'`#':(JUSH=="sqlite"?'`[':(JUSH=="mssql"?'[':''))).']|/\*|'.$Ce.'|$'.(JUSH=="pgsql"?'|\$([a-zA-Z]\w*)?\$':'');$xi=microtime(true);$ka=get_settings("adminer_import");while($H!=""){if(!$Af&&preg_match("~^$Fh*+DELIMITER\\s+(\\S+)~i",$H,$A)){$Pb=preg_quote($A[1]);$H=substr($H,strlen($A[0]));}elseif(!$Af&&JUSH=='pgsql'&&preg_match("~^($Fh*+COPY\\s+)[^;]+\\s+FROM\\s+stdin;~i",$H,$A)){$Pb="\n\\\\\\.\r?\n";$Af=strlen($A[0]);}else{preg_match("($Pb\\s*|$gg)",$H,$A,PREG_OFFSET_CAPTURE,$Af);list($dd,$G)=$A[0];if(!$dd&&$o&&!feof($o))$H
.=fread($o,1e5);else{if(!$dd&&rtrim($H)=="")break;$Af=$G+strlen($dd);if($dd&&!preg_match("(^$Pb)",$dd)){$Pa=driver()->hasCStyleEscapes()||(JUSH=="pgsql"&&($G>0&&strtolower($H[$G-1])=="e"));$rg=($dd=='/*'?'\*/':($dd=='['?']':(preg_match("~^$Ce|^#~",$dd)?"\n":preg_quote($dd).($Pa?'|\\\\.':''))));while(preg_match("($rg|\$)s",$H,$A,PREG_OFFSET_CAPTURE,$Af)){$ih=$A[0][0];if(!$ih&&$o&&!feof($o))$H
.=fread($o,1e5);else{$Af=$A[0][1]+strlen($ih);if(!$ih||$ih[0]!="\\")break;}}}else{$oc=false;$Kg=substr($H,0,$G+($Pb[0]=="\n"?3:0));$ib++;$Eg="<pre id='sql-$ib'><code class='jush-".JUSH."'>".adminer()->sqlCommandQuery($Kg)."</code></pre>\n";if(JUSH=="sqlite"&&preg_match("~^$Fh*+(ATTACH|VACUUM\\b.*\\bINTO)\\b~is",$Kg,$A)!==0){echo$Eg,"<p class='error'>".lang(153,preg_match('~ATTACH~i',$A[1])?'ATTACH':'VACUUM INTO')."\n";$vc[]=" <a href='#sql-$ib'>$ib</a>";if($_POST["error_stops"])break;}else{if(!$_POST["only_errors"]){echo$Eg;ob_flush();flush();}$Lh=microtime(true);if(connection()->multi_query($Kg)&&$f&&preg_match("~^$Fh*+USE\\b~i",$Kg))$f->query($Kg);do{$I=connection()->store_result();if(connection()->error){echo($_POST["only_errors"]?$Eg:""),"<p class='error'>".lang(154).(connection()->errno?" (".connection()->errno.")":"").": ".error()."\n";$vc[]=" <a href='#sql-$ib'>$ib</a>";if($_POST["error_stops"])break
2;}else{$mi=" <span class='time'>(".format_time($Lh).")</span>".(strlen($Kg)<1000?" <a href='".h(ME)."sql=".urlencode(trim($Kg))."'>".lang(12)."</a>":"");$ma=connection()->affected_rows;$jj=($_POST["only_errors"]?"":driver()->warnings());$kj="warnings-$ib";if($jj)$mi
.=", <a href='#$kj'>".lang(38)."</a>".script("qsl('a').onclick = partial(toggle, '$kj');","");$Cc=null;$Tf=null;$Dc="explain-$ib";if(is_object($I)){$y=$_POST["limit"];$Tf=print_select_result($I,$f,array(),$y);if(!$_POST["only_errors"]){echo"<form action='' method='post'>\n";$yf=$I->num_rows;echo"<p class='sql-footer'>".($yf?($y&&$yf>$y?lang(155,$y):"").lang(156,$yf):""),$mi;if($f&&preg_match("~^($Fh|\\()*+SELECT\\b~i",$Kg)&&($Cc=explain($f,$Kg)))echo", <a href='#$Dc'>Explain</a>".script("qsl('a').onclick = partial(toggle, '$Dc');","");$s="export-$ib";echo", <a href='#$s'>".lang(68)."</a>".script("qsl('a').onclick = partial(toggle, '$s');","")."<span id='$s' class='hidden'>: ".html_select("output",adminer()->dumpOutput(),$ka["output"])." ".html_select("format",adminer()->dumpFormat(),$ka["format"]).input_hidden("query",$Kg)."<input type='submit' name='export' value='".lang(68)."'>".input_token()."</span>\n"."</form>\n";}}else{if(preg_match("~^$Fh*+(CREATE|DROP|ALTER)$Fh++(DATABASE|SCHEMA)\\b~i",$Kg)){restart_session();set_session("dbs",null);stop_session();}if(!$_POST["only_errors"])echo"<p class='message' title='".h(connection()->info)."'>".lang(157,$ma)."$mi\n";}echo($jj?"<div id='$kj' class='hidden'>\n$jj</div>\n":"");if($Cc){echo"<div id='$Dc' class='hidden explain'>\n";print_select_result($Cc,$f,$Tf);echo"</div>\n";}}$Lh=microtime(true);}while(connection()->next_result());}$H=substr($H,$Af);$Af=0;}}}}if($oc)echo"<p class='message'>".lang(158)."\n";elseif($_POST["only_errors"])echo"<p class='message'>".lang(159,$ib-count($vc))," <span class='time'>(".format_time($xi).")</span>\n";elseif($vc&&$ib>1)echo"<p class='error'>".lang(154).": ".implode("",$vc)."\n";}else
echo"<p class='error'>".upload_error($H)."\n";}echo'
<form action="" method="post" enctype="multipart/form-data" id="form">
';$Ac="<input type='submit' value='".lang(160)."' title='Ctrl+Enter'>";if(!isset($_GET["import"])){$Kg=$_GET["sql"];if($_POST)$Kg=$_POST["query"];elseif($_GET["history"]=="all")$Kg=$yd;elseif($_GET["history"]!="")$Kg=idx($yd[$_GET["history"]],0);echo"<p>";textarea("query",$Kg,20);echo
script(($_POST?"":"qs('textarea').focus();\n")."qs('#form').onsubmit = partial(sqlSubmit, qs('#form'), '".js_escape(remove_from_uri("sql|limit|error_stops|only_errors|history"))."');"),"<p>";adminer()->sqlPrintAfter();echo"$Ac\n",lang(161).": <input type='number' name='limit' class='size' value='".h($_POST?$_POST["limit"]:$_GET["limit"])."'>\n";}else{$pd=(extension_loaded("zlib")?"[.gz]":"");echo"<fieldset><legend>".lang(162)."</legend><div>",file_input("SQL$pd: <input type='file' name='sql_file[]' multiple>\n$Ac"),"</div></fieldset>\n";$Id=adminer()->importServerPath();if($Id)echo"<fieldset><legend>".lang(163)."</legend><div>",lang(164,"<code>".h($Id)."$pd</code>"),' <input type="submit" name="webfile" value="'.lang(165).'">',"</div></fieldset>\n";echo"<p>";}echo
checkbox("error_stops",1,($_POST?$_POST["error_stops"]:isset($_GET["import"])||$_GET["error_stops"]),lang(166))."\n",checkbox("only_errors",1,($_POST?$_POST["only_errors"]:isset($_GET["import"])||$_GET["only_errors"]),lang(167))."\n",input_token();if(!isset($_GET["import"])&&$yd){print_fieldset("history",lang(168),$_GET["history"]!="");for($X=end($yd);$X;$X=prev($yd)){$w=key($yd);list($Kg,$mi,$kc)=$X;echo'<a href="'.h(ME."sql=&history=$w").'">'.lang(12)."</a>"." <span class='time' title='".@date('Y-m-d',$mi)."'>".@date("H:i:s",$mi)."</span>"." <code class='jush-".JUSH."'>".shorten_utf8(ltrim(str_replace("\n"," ",str_replace("\r","",preg_replace("~^(#|$Ce).*~m",'',$Kg)))),80,"</code>").($kc?" <span class='time'>($kc)</span>":"")."<br>\n";}echo"<input type='submit' name='clear' value='".lang(169)."'>\n","<a href='".h(ME."sql=&history=all")."'>".lang(170)."</a>\n","</div></fieldset>\n";}echo'</form>
';}elseif(isset($_GET["edit"])){$a=$_GET["edit"];$m=fields($a);$Z=(isset($_GET["select"])?($_POST["check"]&&count($_POST["check"])==1?where_check($_POST["check"][0],$m):""):where($_GET,$m));$Oi=(isset($_GET["select"])?$_POST["edit"]:$Z);foreach($m
as$C=>$l){if((!$Oi&&!isset($l["privileges"]["insert"]))||adminer()->fieldName($l)=="")unset($m[$C]);}if($_POST&&!$k&&!isset($_GET["select"])){$_=$_POST["referer"];if($_POST["insert"])$_=($Oi?null:$_SERVER["REQUEST_URI"]);elseif(!preg_match('~^.+&select=.+$~',$_))$_=ME."select=".urlencode($a);$v=indexes($a);$Ji=unique_array($_GET["where"],$v);$Ng="\nWHERE $Z";if(isset($_POST["delete"]))queries_redirect($_,lang(171),driver()->delete($a,$Ng,$Ji?0:1));else{$O=array();foreach($m
as$C=>$l){$X=process_input($l);if($X!==false&&$X!==null)$O[idf_escape($C)]=$X;}if($Oi){if(!$O)redirect($_);queries_redirect($_,lang(172),driver()->update($a,$O,$Ng,$Ji?0:1));if(is_ajax()){page_headers();page_messages($k);exit;}}else{$I=driver()->insert($a,$O);$ue=($I?last_id($I):0);queries_redirect($_,lang(173,($ue?" $ue":"")),$I);}}}$K=null;if($Z){$M=array();foreach($m
as$C=>$l){if(isset($l["privileges"]["select"])){$va=($_POST["clone"]&&$l["auto_increment"]?"''":convert_field($l));$M[]=($va?"$va AS ":"").idf_escape($C);}}$K=array();if(!support("table"))$M=array("*");if($M){$I=driver()->select($a,$M,array($Z),$M,array(),(isset($_GET["select"])?2:1));if(!$I)$k=error();else{$K=$I->fetch_assoc();if(!$K)$K=false;}if(isset($_GET["select"])&&(!$K||$I->fetch_assoc()))$K=null;}}if(!support("table")&&!$m){if(!$Z){$I=driver()->select($a,array("*"),array(),array("*"));$K=($I?$I->fetch_assoc():false);if(!$K)$K=array(driver()->primary=>"");}if($K){foreach($K
as$w=>$X){if(!$Z)$K[$w]=null;$m[$w]=array("field"=>$w,"null"=>($w!=driver()->primary),"auto_increment"=>($w==driver()->primary));}}}if($_POST["save"])$K=(array)$_POST["fields"]+($K?$K:array());edit_form($a,$m,$K,$Oi,$k);}elseif(isset($_GET["create"])){$a=$_GET["create"];$kg=driver()->partitionBy;$og=($kg?driver()->partitionsInfo($a):array());$Tg=referencable_primary($a);$bd=array();foreach($Tg
as$Yh=>$l)$bd[str_replace("`","``",$Yh)."`".str_replace("`","``",$l["field"])]=$Yh;$Wf=array();$S=array();if($a!=""){$Wf=fields($a);$S=table_status1($a);if(count($S)<2)$k=lang(11);}$K=$_POST;$K["fields"]=(array)$K["fields"];if($K["auto_increment_col"])$K["fields"][$K["auto_increment_col"]]["auto_increment"]=true;if($_POST&&!$k)save_settings(array("comments"=>$_POST["comments"],"defaults"=>$_POST["defaults"]));if($_POST&&!process_fields($K["fields"])&&!$k){if($_POST["drop"])queries_redirect(substr(ME,0,-1),lang(174),drop_tables(array($a)));else{$m=array();$pa=array();$Ui=false;$Zc=array();$Vf=reset($Wf);$oa=" FIRST";foreach($K["fields"]as$w=>$l){$n=$bd[$l["type"]];$Fi=($n!==null?$Tg[$n]:$l);if($l["field"]!=""){if(!$l["generated"])$l["default"]=null;$Jg=process_field($l,$Fi);$pa[]=array($l["orig"],$Jg,$oa);if(!$Vf||$Jg!==process_field($Vf,$Vf)){$m[]=array($l["orig"],$Jg,$oa);if($l["orig"]!=""||$oa)$Ui=true;}if($n!==null)$Zc[idf_escape($l["field"])]=($a!=""&&JUSH!="sqlite"?"ADD":" ").format_foreign_key(array('table'=>$bd[$l["type"]],'source'=>array($l["field"]),'target'=>array($Fi["field"]),'on_delete'=>$l["on_delete"],));$oa=" AFTER ".idf_escape($l["field"]);}elseif($l["orig"]!=""){$Ui=true;$m[]=array($l["orig"]);}if($l["orig"]!=""){$Vf=next($Wf);if(!$Vf)$oa="";}}$mg=array();if(in_array($K["partition_by"],$kg)){foreach($K
as$w=>$X){if(preg_match('~^partition~',$w))$mg[$w]=$X;}foreach($mg["partition_names"]as$w=>$C){if($C==""){unset($mg["partition_names"][$w]);unset($mg["partition_values"][$w]);}}$mg["partition_names"]=array_values($mg["partition_names"]);$mg["partition_values"]=array_values($mg["partition_values"]);if($mg==$og)$mg=array();}elseif(preg_match("~partitioned~",$S["Create_options"]))$mg=null;$B=lang(175);if($a==""){cookie("adminer_engine",$K["Engine"]);$B=lang(176);}$C=trim($K["name"]);queries_redirect(ME.(support("table")?"table=":"select=").urlencode($C),$B,alter_table($a,$C,(JUSH=="sqlite"&&($Ui||$Zc)?$pa:$m),$Zc,($K["Comment"]!=$S["Comment"]?$K["Comment"]:null),($K["Engine"]&&$K["Engine"]!=$S["Engine"]?$K["Engine"]:""),($K["Collation"]&&$K["Collation"]!=$S["Collation"]?$K["Collation"]:""),($K["Auto_increment"]!=""?number($K["Auto_increment"]):""),$mg));}}page_header(($a!=""?lang(36):lang(69)),$k,array("table"=>$a),h($a));if(!$_POST){$Gi=driver()->types();$K=array("Engine"=>$_COOKIE["adminer_engine"],"fields"=>array(array("field"=>"","type"=>(isset($Gi["int"])?"int":(isset($Gi["integer"])?"integer":"")),"on_update"=>"")),"partition_names"=>array(""),);if($a!=""){$K=$S;$K["name"]=$a;$K["fields"]=array();if(!$_GET["auto_increment"])$K["Auto_increment"]="";foreach($Wf
as$l){$l["generated"]=$l["generated"]?:(isset($l["default"])?"DEFAULT":"");$K["fields"][]=$l;}if($kg){$K+=$og;$K["partition_names"][]="";$K["partition_values"][]="";}}}$fb=collations();if(is_array(reset($fb)))$fb=call_user_func_array('array_merge',array_values($fb));$qc=driver()->engines();foreach($qc
as$pc){if(!strcasecmp($pc,$K["Engine"])){$K["Engine"]=$pc;break;}}echo'
<form action="" method="post" id="form">
<p>
';if(support("columns")||$a==""){echo
lang(177).": <input name='name'".($a==""&&!$_POST?" autofocus":"")." data-maxlength='64' value='".h($K["name"])."' autocapitalize='off'>\n",($qc?html_select("Engine",array(""=>"(".lang(178).")")+$qc,$K["Engine"]).on_help("event.target.value",1).script("qsl('select').onchange = helpClose;")."\n":"");if($fb)echo"<datalist id='collations'>".optionlist($fb)."</datalist>\n",(preg_match("~sqlite|mssql~",JUSH)?"":"<input list='collations' name='Collation' value='".h($K["Collation"])."' placeholder='(".lang(104).")'>\n");echo"<input type='submit' value='".lang(16)."'>\n";}if(support("columns")){echo"<div class='scrollable'>\n","<table id='edit-fields' class='nowrap'>\n";edit_fields($K["fields"],$fb,"TABLE",$bd);echo"</table>\n",script("editFields();"),"</div>\n<p>\n",lang(43).": <input type='number' name='Auto_increment' class='size' value='".h($K["Auto_increment"])."'>\n",checkbox("defaults",1,($_POST?$_POST["defaults"]:get_setting("defaults")),lang(179),"columnShow(this.checked, 5)","jsonly");$lb=($_POST?$_POST["comments"]:get_setting("comments"));echo(support("comment")?checkbox("comments",1,$lb,lang(42),"editingCommentsClick(this, true);","jsonly").' '.(preg_match('~\n~',$K["Comment"])?"<textarea name='Comment' rows='2' cols='20'".($lb?"":" class='hidden'").">".h($K["Comment"])."</textarea>":'<input name="Comment" value="'.h($K["Comment"]).'" data-maxlength="'.(min_version(5.5)?2048:60).'"'.($lb?"":" class='hidden'").'>'):''),'<p>
<input type="submit" value="',lang(16),'">
';}echo'
';if($a!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$a));if($kg&&(JUSH=='sql'||$a=="")){$lg=preg_match('~RANGE|LIST~',$K["partition_by"]);print_fieldset("partition",lang(181),$K["partition_by"]);echo"<p>".html_select("partition_by",array_merge(array(""),$kg),$K["partition_by"]).on_help("event.target.value.replace(/./, 'PARTITION BY \$&')",1).script("qsl('select').onchange = partitionByChange;"),"(<input name='partition' value='".h($K["partition"])."'>)\n",lang(182).": <input type='number' name='partitions' class='size".($lg||!$K["partition_by"]?" hidden":"")."' value='".h($K["partitions"])."'>\n","<table id='partition-table'".($lg?"":" class='hidden'").">\n","<thead><tr><th>".lang(183)."<th>".lang(184)."</thead>\n";foreach($K["partition_names"]as$w=>$X)echo'<tr>','<td><input name="partition_names[]" value="'.h($X).'" autocapitalize="off">',($w==count($K["partition_names"])-1?script("qsl('input').oninput = partitionNameChange;"):''),'<td><input name="partition_values[]" value="'.h(idx($K["partition_values"],$w)).'">';echo"</table>\n</div></fieldset>\n";}echo
input_token(),'</form>
';}elseif(isset($_GET["indexes"])){$a=$_GET["indexes"];$Pd=array("PRIMARY","UNIQUE","INDEX");$S=table_status1($a,true);$Nd=driver()->indexAlgorithms($S);if(preg_match('~MyISAM|M?aria'.(min_version(5.6,'10.0.5')?'|InnoDB':'').'~i',$S["Engine"]))$Pd[]="FULLTEXT";if(preg_match('~MyISAM|M?aria'.(min_version(5.7,'10.2.2')?'|InnoDB':'').'~i',$S["Engine"]))$Pd[]="SPATIAL";if(min_version('',11.7)&&preg_match('~MyISAM|InnoDB~i',$S["Engine"]))$Pd[]="VECTOR";$v=indexes($a);$m=fields($a);$Dg=array();if(JUSH=="mongo"){$Dg=$v["_id_"];unset($Pd[0]);unset($v["_id_"]);}$K=$_POST;if($K)save_settings(array("index_options"=>$K["options"]));if($_POST&&!$k&&!$_POST["add"]&&!$_POST["drop_col"]){$ra=array();foreach($K["indexes"]as$u){$C=$u["name"];if(in_array($u["type"],$Pd)){$c=array();$Ae=array();$Sb=array();$Od=(support("partial_indexes")?$u["partial"]:"");$Md=(in_array($u["algorithm"],$Nd)?$u["algorithm"]:"");$O=array();ksort($u["columns"]);foreach($u["columns"]as$w=>$b){if($b!=""){$x=idx($u["lengths"],$w);$Qb=idx($u["descs"],$w);$O[]=($m[$b]?idf_escape($b):$b).($x?"(".(+$x).")":"").($Qb?" DESC":"");$c[]=$b;$Ae[]=($x?:null);$Sb[]=$Qb;}}$Bc=$v[$C];if($Bc){ksort($Bc["columns"]);ksort($Bc["lengths"]);ksort($Bc["descs"]);if($u["type"]==$Bc["type"]&&array_values($Bc["columns"])===$c&&(!$Bc["lengths"]||array_values($Bc["lengths"])===$Ae)&&array_values($Bc["descs"])===$Sb&&$Bc["partial"]==$Od&&(!$Nd||$Bc["algorithm"]==$Md)){unset($v[$C]);continue;}}if($c)$ra[]=array($u["type"],$C,$O,$Md,$Od);}}foreach($v
as$C=>$Bc)$ra[]=array($Bc["type"],$C,"DROP");if(!$ra)redirect(ME."table=".urlencode($a));queries_redirect(ME."table=".urlencode($a),lang(185),alter_indexes($a,$ra));}page_header(lang(136),$k,array("table"=>$a),h($a));$Pc=array_keys($m);if($_POST["add"]){foreach($K["indexes"]as$w=>$u){if($u["columns"][count($u["columns"])]!="")$K["indexes"][$w]["columns"][]="";}$u=end($K["indexes"]);if($u["type"]||array_filter($u["columns"],'strlen'))$K["indexes"][]=array("columns"=>array(1=>""));}if(!$K){foreach($v
as$w=>$u){$v[$w]["name"]=$w;$v[$w]["columns"][]="";}$v[]=array("columns"=>array(1=>""));$K["indexes"]=$v;}$Ae=(JUSH=="sql"||JUSH=="mssql");$_h=($_POST?$_POST["options"]:get_setting("index_options"));echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap">
<thead><tr>
<th id="label-type">',lang(186);$Gd=" class='idxopts".($_h?"":" hidden")."'";if($Nd)echo"<th id='label-algorithm'$Gd>".lang(187).doc_link(array('sql'=>'create-index.html#create-index-storage-engine-index-types','mariadb'=>'storage-engine-index-types/',));echo'<th><input type="submit" class="wayoff">',lang(188).($Ae?"<span$Gd> (".lang(189).")</span>":"");if($Ae||support("descidx"))echo
checkbox("options",1,$_h,lang(110),"indexOptionsShow(this.checked)","jsonly")."\n";echo'<th id="label-name">',lang(190);if(support("partial_indexes"))echo"<th id='label-condition'$Gd>".lang(191);echo'<th><noscript>',icon("plus","add[0]","+",lang(111)),'</noscript>
</thead>
';if($Dg){echo"<tr><td>PRIMARY<td>";foreach($Dg["columns"]as$w=>$b)echo
select_input(" disabled",$Pc,$b),"<label><input disabled type='checkbox'>".lang(51)."</label> ";echo"<td><td>\n";}$ie=1;foreach($K["indexes"]as$u){if(!$_POST["drop_col"]||$ie!=key($_POST["drop_col"])){echo"<tr><td>".html_select("indexes[$ie][type]",array(-1=>"")+$Pd,$u["type"],($ie==count($K["indexes"])?"indexesAddRow.call(this);":""),"label-type");if($Nd)echo"<td$Gd>".html_select("indexes[$ie][algorithm]",array_merge(array(""),$Nd),$u['algorithm'],"label-algorithm");echo"<td>";ksort($u["columns"]);$r=1;foreach($u["columns"]as$w=>$b){echo"<span>".select_input(" name='indexes[$ie][columns][$r]' title='".lang(40)."'",($m&&($b==""||$m[$b])?array_combine($Pc,$Pc):array()),$b,"partial(".($r==count($u["columns"])?"indexesAddColumn":"indexesChangeColumn").", '".js_escape(JUSH=="sql"?"":$_GET["indexes"]."_")."')"),"<span$Gd>",($Ae?"<input type='number' name='indexes[$ie][lengths][$r]' class='size' value='".h(idx($u["lengths"],$w))."' title='".lang(109)."'>":""),(support("descidx")?checkbox("indexes[$ie][descs][$r]",1,idx($u["descs"],$w),lang(51)):""),"</span> </span>";$r++;}echo"<td><input name='indexes[$ie][name]' value='".h($u["name"])."' autocapitalize='off' aria-labelledby='label-name'>\n";if(support("partial_indexes"))echo"<td$Gd><input name='indexes[$ie][partial]' value='".h($u["partial"])."' autocapitalize='off' aria-labelledby='label-condition'>\n";echo"<td>".icon("cross","drop_col[$ie]","x",lang(113)).script("qsl('button').onclick = partial(editingRemoveRow, 'indexes\$1[type]');");}$ie++;}echo'</table>
</div>
<p>
<input type="submit" value="',lang(16),'">
',input_token(),'</form>
';}elseif(isset($_GET["database"])){$K=$_POST;if($_POST&&!$k&&!$_POST["add"]){$C=trim($K["name"]);if($_POST["drop"]){$_GET["db"]="";queries_redirect(remove_from_uri("db|database"),lang(192),drop_databases(array(DB)));}elseif(DB!==$C){if(DB!=""){$_GET["db"]=$C;queries_redirect(preg_replace('~\bdb=[^&]*&~','',ME)."db=".urlencode($C),lang(193),rename_database($C,$K["collation"]));}else{$h=explode("\n",str_replace("\r","",$C));$Qh=true;$te="";foreach($h
as$i){if(count($h)==1||$i!=""){if(!create_database($i,$K["collation"]))$Qh=false;$te=$i;}}restart_session();set_session("dbs",null);queries_redirect(ME."db=".urlencode($te),lang(194),$Qh);}}else{if(!$K["collation"])redirect(substr(ME,0,-1));query_redirect("ALTER DATABASE ".idf_escape($C).(preg_match('~^[a-z0-9_]+$~i',$K["collation"])?" COLLATE $K[collation]":""),substr(ME,0,-1),lang(195));}}page_header(DB!=""?lang(59):lang(117),$k,array(),h(DB));$fb=collations();$C=DB;if($_POST)$C=$K["name"];elseif(DB!="")$K["collation"]=db_collation(DB,$fb);elseif(JUSH=="sql"){foreach(get_vals("SHOW GRANTS")as$kd){if(preg_match('~ ON (`(([^\\\\`]|``|\\\\.)*)%`\.\*)?~',$kd,$A)&&$A[1]){$C=stripcslashes(idf_unescape("`$A[2]`"));break;}}}echo'
<form action="" method="post">
<p>
',($_POST["add"]||strpos($C,"\n")?'<textarea autofocus name="name" rows="10" cols="40">'.h($C).'</textarea><br>':'<input name="name" autofocus value="'.h($C).'" data-maxlength="64" autocapitalize="off">')."\n".($fb?html_select("collation",array(""=>"(".lang(104).")")+$fb,$K["collation"]).doc_link(array('sql'=>"charset-charsets.html",'mariadb'=>"supported-character-sets-and-collations/",)):""),'<input type="submit" value="',lang(16),'">
';if(DB!="")echo"<input type='submit' name='drop' value='".lang(129)."'>".confirm(lang(180,DB))."\n";elseif(!$_POST["add"]&&$_GET["db"]=="")echo
icon("plus","add[0]","+",lang(111))."\n";echo
input_token(),'</form>
';}elseif(isset($_GET["call"])){$ca=($_GET["name"]?:$_GET["call"]);page_header(lang(196).": ".h($ca),$k);$fh=routine($_GET["call"],(isset($_GET["callf"])?"FUNCTION":"PROCEDURE"));$Jd=array();$ag=array();foreach($fh["fields"]as$r=>$l){if(substr($l["inout"],-3)=="OUT"&&JUSH=='sql')$ag[$r]="@".idf_escape($l["field"])." AS ".idf_escape($l["field"]);if(!$l["inout"]||substr($l["inout"],0,2)=="IN")$Jd[]=$r;}if(!$k&&$_POST){$Qa=array();foreach($fh["fields"]as$w=>$l){$X="";if(in_array($w,$Jd)){$X=process_input($l);if($X===false)$X="''";if(isset($ag[$w]))connection()->query("SET @".idf_escape($l["field"])." = $X");}if(isset($ag[$w]))$Qa[]="@".idf_escape($l["field"]);elseif(in_array($w,$Jd))$Qa[]=$X;}$H=(isset($_GET["callf"])?"SELECT ":"CALL ").(idx($fh["returns"],"type")=="record"?"* FROM ":"").table($ca)."(".implode(", ",$Qa).")";$Lh=microtime(true);$I=connection()->multi_query($H);$ma=connection()->affected_rows;echo
adminer()->selectQuery($H,$Lh,!$I);if(!$I)echo"<p class='error'>".error()."\n";else{$f=connect();if($f)$f->select_db(DB);do{$I=connection()->store_result();if(is_object($I))print_select_result($I,$f);else
echo"<p class='message'>".lang(197,$ma)." <span class='time'>".@date("H:i:s")."</span>\n";}while(connection()->next_result());if($ag)print_select_result(connection()->query("SELECT ".implode(", ",$ag)));}}echo'
<form action="" method="post">
';if($Jd){echo"<table class='layout'>\n";foreach($Jd
as$w){$l=$fh["fields"][$w];$C=$l["field"];echo"<tr><th>".adminer()->fieldName($l);$Y=idx($_POST["fields"],$C);if($Y!=""){if($l["type"]=="set")$Y=implode(",",$Y);}input($l,$Y,idx($_POST["function"],$C,""));echo"\n";}echo"</table>\n";}echo'<p>
<input type="submit" value="',lang(196),'">
',input_token(),'</form>

<pre>
';function
pre_tr($ih){return
preg_replace('~^~m','<tr>',preg_replace('~\|~','<td>',preg_replace('~\|$~m',"",rtrim($ih))));}$R='(\+--[-+]+\+\n)';$K='(\| .* \|\n)';echo
preg_replace_callback("~^$R?$K$R?($K*)$R?~m",function($A){$Vc=pre_tr($A[2]);return"<table>\n".($A[1]?"<thead>$Vc</thead>\n":$Vc).pre_tr($A[4])."\n</table>";},preg_replace('~(\n(    -|mysql)&gt; )(.+)~',"\\1<code class='jush-sql'>\\3</code>",preg_replace('~(.+)\n---+\n~',"<b>\\1</b>\n",h($fh['comment']))));echo'</pre>
';}elseif(isset($_GET["foreign"])){$a=$_GET["foreign"];$C=$_GET["name"];$K=$_POST;if($_POST&&!$k&&!$_POST["add"]&&!$_POST["change"]&&!$_POST["change-js"]){if(!$_POST["drop"]){$K["source"]=array_filter($K["source"],'strlen');ksort($K["source"]);$gi=array();foreach($K["source"]as$w=>$X)$gi[$w]=$K["target"][$w];$K["target"]=$gi;}if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(" $C"=>($K["drop"]?"":" ".format_foreign_key($K))));else{$ra="ALTER TABLE ".table($a);$I=($C==""||queries("$ra DROP ".(JUSH=="sql"?"FOREIGN KEY ":"CONSTRAINT ").idf_escape($C)));if(!$K["drop"])$I=queries("$ra ADD".format_foreign_key($K));}queries_redirect(ME."table=".urlencode($a),($K["drop"]?lang(198):($C!=""?lang(199):lang(200))),$I);if(!$K["drop"])$k=lang(201);}page_header(lang(202),$k,array("table"=>$a),h($a));if($_POST){ksort($K["source"]);if($_POST["change"]||$_POST["change-js"])$K["target"]=array();else$K["source"][]="";}elseif($C!=""){$bd=foreign_keys($a);$K=$bd[$C];$K["source"][]="";}else{$K["table"]=$a;$K["source"]=array("");}echo'
<form action="" method="post">
';$Eh=array_keys(fields($a));if($K["db"]!="")connection()->select_db($K["db"]);if($K["ns"]!=""){$Xf=get_schema();set_schema($K["ns"]);}$Sg=array_keys(array_filter(table_status('',true),'Adminer\fk_support'));$gi=array_keys(fields(in_array($K["table"],$Sg)?$K["table"]:reset($Sg)));$If="this.form['change-js'].value = '1'; this.form.submit();";echo"<p><label>".lang(203).": ".html_select("table",$Sg,$K["table"],$If)."</label>\n";if(JUSH!="sqlite"){$Ib=array();foreach(adminer()->databases()as$i){if(!information_schema($i))$Ib[]=$i;}echo"<label>".lang(70).": ".html_select("db",$Ib,$K["db"]!=""?$K["db"]:$_GET["db"],$If)."</label>";}echo
input_hidden("change-js"),'<noscript><p><input type="submit" name="change" value="',lang(204),'"></noscript>
<table>
<thead><tr><th id="label-source">',lang(138),'<th id="label-target">',lang(139),'</thead>
';$ie=0;foreach($K["source"]as$w=>$X){echo"<tr>","<td>".html_select("source[".(+$w)."]",array(-1=>"")+$Eh,$X,($ie==count($K["source"])-1?"foreignAddRow.call(this);":""),"label-source"),"<td>".html_select("target[".(+$w)."]",$gi,idx($K["target"],$w),"","label-target");$ie++;}echo'</table>
<p>
<label>',lang(106),': ',html_select("on_delete",array(-1=>"")+explode("|",driver()->onActions),$K["on_delete"]),'</label>
<label>',lang(105),': ',html_select("on_update",array(-1=>"")+explode("|",driver()->onActions),$K["on_update"]),'</label>
',(DRIVER==='pgsql'?html_select("deferrable",array('NOT DEFERRABLE','DEFERRABLE','DEFERRABLE INITIALLY DEFERRED'),$K["deferrable"]).' ':''),doc_link(array('sql'=>"innodb-foreign-key-constraints.html",'mariadb'=>"foreign-keys/",)),'<p>
<input type="submit" value="',lang(16),'">
<noscript><p><input type="submit" name="add" value="',lang(205),'"></noscript>
';if($C!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$C));echo
input_token(),'</form>
';}elseif(isset($_GET["view"])){$a=$_GET["view"];$K=$_POST;$Yf="VIEW";if(JUSH=="pgsql"&&$a!=""){$P=table_status1($a);$Yf=strtoupper($P["Engine"]);}if($_POST&&!$k){$C=trim($K["name"]);$va=" AS\n$K[select]";$_=ME."table=".urlencode($C);$B=lang(206);$U=($_POST["materialized"]?"MATERIALIZED VIEW":"VIEW");if(!$_POST["drop"]&&$a==$C&&JUSH!="sqlite"&&$U=="VIEW"&&$Yf=="VIEW")query_redirect((JUSH=="mssql"?"ALTER":"CREATE OR REPLACE")." VIEW ".table($C).$va,$_,$B);else{$ii=$C."_adminer_".uniqid();drop_create("DROP $Yf ".table($a),"CREATE $U ".table($C).$va,"DROP $U ".table($C),"CREATE $U ".table($ii).$va,"DROP $U ".table($ii),($_POST["drop"]?substr(ME,0,-1):$_),lang(207),$B,lang(208),$a,$C);}}if(!$_POST&&$a!=""){$K=view($a);$K["name"]=$a;$K["materialized"]=($Yf!="VIEW");if(!$k)$k=error();}page_header(($a!=""?lang(35):lang(209)),$k,array("table"=>$a),h($a));echo'
<form action="" method="post">
<p>',lang(190),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',(support("materializedview")?" ".checkbox("materialized",1,$K["materialized"],lang(132)):""),'<p>';textarea("select",$K["select"]);echo'<p>
<input type="submit" value="',lang(16),'">
';if($a!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$a));echo
input_token(),'</form>
';}elseif(isset($_GET["event"])){$aa=$_GET["event"];$Zd=array("YEAR","QUARTER","MONTH","DAY","HOUR","MINUTE","WEEK","SECOND","YEAR_MONTH","DAY_HOUR","DAY_MINUTE","DAY_SECOND","HOUR_MINUTE","HOUR_SECOND","MINUTE_SECOND");$Mh=array("ENABLED"=>"ENABLE","DISABLED"=>"DISABLE","SLAVESIDE_DISABLED"=>"DISABLE ON SLAVE");$K=$_POST;if($_POST&&!$k){if($_POST["drop"])query_redirect("DROP EVENT ".idf_escape($aa),substr(ME,0,-1),lang(210));elseif(in_array($K["INTERVAL_FIELD"],$Zd)&&isset($Mh[$K["STATUS"]])){$jh="\nON SCHEDULE ".($K["INTERVAL_VALUE"]?"EVERY ".q($K["INTERVAL_VALUE"])." $K[INTERVAL_FIELD]".($K["STARTS"]?" STARTS ".q($K["STARTS"]):"").($K["ENDS"]?" ENDS ".q($K["ENDS"]):""):"AT ".q($K["STARTS"]))." ON COMPLETION".($K["ON_COMPLETION"]?"":" NOT")." PRESERVE";queries_redirect(substr(ME,0,-1),($aa!=""?lang(211):lang(212)),queries(($aa!=""?"ALTER EVENT ".idf_escape($aa).$jh.($aa!=$K["EVENT_NAME"]?"\nRENAME TO ".idf_escape($K["EVENT_NAME"]):""):"CREATE EVENT ".idf_escape($K["EVENT_NAME"]).$jh)."\n".$Mh[$K["STATUS"]]." COMMENT ".q($K["EVENT_COMMENT"]).rtrim(" DO\n$K[EVENT_DEFINITION]",";").";"));}}page_header(($aa!=""?lang(213).": ".h($aa):lang(214)),$k);if(!$K&&$aa!=""){$L=get_rows("SELECT * FROM information_schema.EVENTS WHERE EVENT_SCHEMA = ".q(DB)." AND EVENT_NAME = ".q($aa));$K=reset($L);}echo'
<form action="" method="post">
<table class="layout">
<tr><th>',lang(190),'<td><input name="EVENT_NAME" value="',h($K["EVENT_NAME"]),'" data-maxlength="64" autocapitalize="off">
<tr><th title="datetime">',lang(215),'<td><input name="STARTS" value="',h("$K[EXECUTE_AT]$K[STARTS]"),'">
<tr><th title="datetime">',lang(216),'<td><input name="ENDS" value="',h($K["ENDS"]),'">
<tr><th>',lang(217),'<td><input type="number" name="INTERVAL_VALUE" value="',h($K["INTERVAL_VALUE"]),'" class="size"> ',html_select("INTERVAL_FIELD",$Zd,$K["INTERVAL_FIELD"]),'<tr><th>',lang(120),'<td>',html_select("STATUS",$Mh,$K["STATUS"]),'<tr><th>',lang(42),'<td><input name="EVENT_COMMENT" value="',h($K["EVENT_COMMENT"]),'" data-maxlength="64">
<tr><th><td>',checkbox("ON_COMPLETION","PRESERVE",$K["ON_COMPLETION"]=="PRESERVE",lang(218)),'</table>
<p>';textarea("EVENT_DEFINITION",$K["EVENT_DEFINITION"]);echo'<p>
<input type="submit" value="',lang(16),'">
';if($aa!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$aa));echo
input_token(),'</form>
';}elseif(isset($_GET["procedure"])){$ca=($_GET["name"]?:$_GET["procedure"]);$fh=(isset($_GET["function"])?"FUNCTION":"PROCEDURE");$K=$_POST;$K["fields"]=(array)$K["fields"];if($_POST&&!process_fields($K["fields"])&&!$k){foreach($K["fields"]as$w=>$l){if($l["field"]=="")unset($K["fields"][$w]);}$Df=routine_id($ca,routine($_GET["procedure"],$fh));$rf=routine_id($K["name"],$K);$g=create_routine($fh,$K);$_=substr(ME,0,-1);$B=lang(219);if(!$_POST["drop"]&&$Df==$rf&&connection()->flavor!="mysql")query_redirect(substr_replace($g,' OR REPLACE',6,0),$_,$B);else{$ii="$K[name]_adminer_".uniqid();drop_create("DROP $fh $Df",$g,"DROP $fh $rf",create_routine($fh,array("name"=>$ii)+$K),"DROP $fh ".routine_id($ii,$K),$_,lang(220),$B,lang(221),$ca,$K["name"]);}}page_header(($ca!=""?(isset($_GET["function"])?lang(222):lang(223)).": ".h($ca):(isset($_GET["function"])?lang(224):lang(225))),$k);if(!$_POST){if($ca=="")$K["language"]="sql";else{$K=routine($_GET["procedure"],$fh);$K["name"]=$ca;}}$fb=get_vals("SHOW CHARACTER SET");sort($fb);$gh=routine_languages();echo($fb?"<datalist id='collations'>".optionlist($fb)."</datalist>":""),'
<form action="" method="post" id="form">
<p>',lang(190),': <input name="name" value="',h($K["name"]),'" data-maxlength="64" autocapitalize="off">
',($gh?"<label>".lang(21).": ".html_select("language",$gh,$K["language"])."</label>\n":""),'<input type="submit" value="',lang(16),'">
<div class="scrollable">
<table id="edit-fields" class="nowrap">
';edit_fields($K["fields"],$fb,$fh);if(isset($_GET["function"])){echo"<tr><td>".lang(226);edit_type("returns",(array)$K["returns"],$fb,array(),(JUSH=="pgsql"?array("void","trigger"):array()));}echo'</table>
',script("editFields();"),'</div>
<p>';textarea("definition",$K["definition"],20);echo'<p>
<input type="submit" value="',lang(16),'">
';if($ca!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$ca));echo
input_token(),'</form>
';}elseif(isset($_GET["check"])){$a=$_GET["check"];$C=$_GET["name"];$K=$_POST;if($K&&!$k){if(JUSH=="sqlite")$I=recreate_table($a,$a,array(),array(),array(),"",array(),"$C",($K["drop"]?"":$K["clause"]));else{$I=($C==""||queries("ALTER TABLE ".table($a)." DROP CONSTRAINT ".idf_escape($C)));if(!$K["drop"])$I=queries("ALTER TABLE ".table($a)." ADD".($K["name"]!=""?" CONSTRAINT ".idf_escape($K["name"]):"")." CHECK ($K[clause])");}queries_redirect(ME."table=".urlencode($a),($K["drop"]?lang(227):($C!=""?lang(228):lang(229))),$I);}page_header(($C!=""?lang(230).": ".h($C):lang(143)),$k,array("table"=>$a));if(!$K){$Xa=driver()->checkConstraints($a);$K=array("name"=>$C,"clause"=>$Xa[$C]);}echo'
<form action="" method="post">
<p>';if(JUSH!="sqlite")echo
lang(190).': <input name="name" value="'.h($K["name"]).'" data-maxlength="64" autocapitalize="off"> ';echo
doc_link(array('sql'=>"create-table-check-constraints.html",'mariadb'=>"constraint/",),"?"),'<p>';textarea("clause",$K["clause"]);echo'<p><input type="submit" value="',lang(16),'">
';if($C!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$C));echo
input_token(),'</form>
';}elseif(isset($_GET["trigger"])){$a=$_GET["trigger"];$C="$_GET[name]";$Ci=trigger_options();$K=(array)trigger($C,$a)+array("Trigger"=>$a."_bi");if($_POST){if(!$k&&in_array($_POST["Timing"],$Ci["Timing"])&&in_array($_POST["Event"],$Ci["Event"])&&in_array($_POST["Type"],$Ci["Type"])){$Gf=" ON ".table($a);$dc="DROP TRIGGER ".idf_escape($C).(JUSH=="pgsql"?$Gf:"");$_=ME."table=".urlencode($a);if($_POST["drop"])query_redirect($dc,$_,lang(231));else{if($C!="")queries($dc);queries_redirect($_,($C!=""?lang(232):lang(233)),queries(create_trigger($Gf,$_POST)));if($C!="")queries(create_trigger($Gf,$K+array("Type"=>reset($Ci["Type"]))));}}$K=$_POST;}page_header(($C!=""?lang(234).": ".h($C):lang(235)),$k,array("table"=>$a));echo'
<form action="" method="post" id="form">
<table class="layout">
<tr><th>',lang(236),'<td>',html_select("Timing",$Ci["Timing"],$K["Timing"],"triggerChange(/^".preg_quote($a,"/")."_[ba][iud]$/, '".js_escape($a)."', this.form);"),'<tr><th>',lang(237),'<td>',html_select("Event",$Ci["Event"],$K["Event"],"this.form['Timing'].onchange();"),(in_array("UPDATE OF",$Ci["Event"])?" <input name='Of' value='".h($K["Of"])."' class='hidden'>":""),'<tr><th>',lang(41),'<td>',html_select("Type",$Ci["Type"],$K["Type"]),'</table>
<p>',lang(190),': <input name="Trigger" value="',h($K["Trigger"]),'" data-maxlength="64" autocapitalize="off">
',script("qs('#form')['Timing'].onchange();"),'<p>';textarea("Statement",$K["Statement"]);echo'<p>
<input type="submit" value="',lang(16),'">
';if($C!="")echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,$C));echo
input_token(),'</form>
';}elseif(isset($_GET["user"])){$ea=$_GET["user"];$Hg=array(""=>array("All privileges"=>""));foreach(get_rows("SHOW PRIVILEGES")as$K){foreach(explode(",",($K["Privilege"]=="Grant option"?"":$K["Context"]))as$sb)$Hg[$sb=="File access on server"?"Server Admin":$sb][$K["Privilege"]]=$K["Comment"];}unset($Hg["Server Admin"]["Usage"]);foreach($Hg["Tables"]as$w=>$X)unset($Hg["Databases"][$w]);$qf=array();if($_POST){foreach($_POST["objects"]as$w=>$X)$qf[$X]=(array)$qf[$X]+idx($_POST["grants"],$w,array());}$ld=array();if(isset($_GET["host"])&&($I=connection()->query("SHOW GRANTS FOR ".q($ea)."@".q($_GET["host"])))){while($K=$I->fetch_row()){if(preg_match('~GRANT (.*) ON (.*) TO ~',$K[0],$A)&&preg_match_all('~ *([^(,]*[^ ,(])( *\([^)]+\))?~',$A[1],$Me,PREG_SET_ORDER)){foreach($Me
as$X){if($X[1]!="USAGE")$ld["$A[2]$X[2]"][$X[1]]=true;if(preg_match('~ WITH GRANT OPTION~',$K[0]))$ld["$A[2]$X[2]"]["GRANT OPTION"]=true;}}}}if($_POST&&!$k){$Ff=(isset($_GET["host"])?q($ea)."@".q($_GET["host"]):"''");if($_POST["drop"])query_redirect("DROP USER $Ff",ME."privileges=",lang(238));else{$tf=q($_POST["user"])."@".q($_POST["host"]);$pg=$_POST["pass"];$xb=false;$I=true;if($Ff!=$tf){$xb=queries("CREATE USER $tf IDENTIFIED BY ".($_POST["hashed"]?"PASSWORD ":"").q($pg));$I=$xb;}elseif($pg!="")$I=queries("SET PASSWORD FOR $tf = ".(min_version(8,99)||$_POST["hashed"]?q($pg):"PASSWORD(".q($pg).")"));if($I){$ch=array();foreach($qf
as$_f=>$kd){if(isset($_GET["grant"]))$kd=array_filter($kd);$kd=array_keys($kd);if(isset($_GET["grant"]))$ch=array_diff(array_keys(array_filter($qf[$_f],'strlen')),$kd);elseif($Ff==$tf){$Cf=array_keys((array)$ld[$_f]);$ch=array_diff($Cf,$kd);$kd=array_diff($kd,$Cf);unset($ld[$_f]);}if(preg_match('~^(.+)\s*(\(.*\))?$~U',$_f,$A)&&(!grant("REVOKE",$ch,$A[2]," ON $A[1] FROM $tf")||!grant("GRANT",$kd,$A[2]," ON $A[1] TO $tf"))){$I=false;break;}}}if($I&&isset($_GET["host"])){if($Ff!=$tf)queries("DROP USER $Ff");elseif(!isset($_GET["grant"])){foreach($ld
as$_f=>$ch){if(preg_match('~^(.+)(\(.*\))?$~U',$_f,$A))grant("REVOKE",array_keys($ch),$A[2]," ON $A[1] FROM $tf");}}}queries_redirect(ME."privileges=",(isset($_GET["host"])?lang(239):lang(240)),$I);if($xb)connection()->query("DROP USER $tf");}}page_header((isset($_GET["host"])?lang(27).": ".h("$ea@$_GET[host]"):lang(152)),$k,array("privileges"=>array('',lang(63))));$K=$_POST;if($K)$ld=$qf;else{$K=$_GET+array("host"=>get_val("SELECT SUBSTRING_INDEX(CURRENT_USER, '@', -1)"));$ld[(DB==""||$ld?"":idf_escape(addcslashes(DB,"%_\\"))).".*"]=array();}echo'<form action="" method="post">
<table class="layout">
<tr><th>',lang(25),'<td><input name="host" data-maxlength="60" value="',h($K["host"]),'" autocapitalize="off">
<tr><th>',lang(27),'<td><input name="user" data-maxlength="80" value="',h($K["user"]),'" autocapitalize="off">
<tr><th>',lang(28),'<td><input name="pass" id="pass" value="',h($K["pass"]),'" autocomplete="new-password">
',($K["hashed"]?"":script("typePassword(qs('#pass'));")),(min_version(8,99)?"":checkbox("hashed",1,$K["hashed"],lang(241),"typePassword(this.form['pass'], this.checked);")),'</table>

',"<table class='odds'>\n","<thead><tr><th colspan='2'>".lang(63).doc_link(array('sql'=>"grant.html#priv_level"));$r=0;foreach($ld
as$_f=>$kd){echo'<th>'.($_f!="*.*"?"<input name='objects[$r]' value='".h($_f)."' size='10' autocapitalize='off'>":input_hidden("objects[$r]","*.*")."*.*");$r++;}echo"</thead>\n";foreach(array(""=>"","Server Admin"=>lang(25),"Databases"=>lang(29),"Tables"=>lang(134),"Procedures"=>lang(242),)as$sb=>$Qb){foreach((array)$Hg[$sb]as$Gg=>$jb){echo"<tr><td".($Qb?">$Qb<td":" colspan='2'").' lang="en" title="'.h($jb).'">'.h($Gg);$r=0;foreach($ld
as$_f=>$kd){$C="'grants[$r][".h(strtoupper($Gg))."]'";$Y=$kd[strtoupper($Gg)];if($sb=="Server Admin"&&$_f!=(isset($ld["*.*"])?"*.*":".*"))echo"<td>";elseif(isset($_GET["grant"]))echo"<td><select name=$C><option><option value='1'".($Y?" selected":"").">".lang(243)."<option value='0'".($Y=="0"?" selected":"").">".lang(244)."</select>";else
echo"<td align='center'><label class='block'>","<input type='checkbox' name=$C value='1'".($Y?" checked":"").($Gg=="All privileges"?" id='grants-$r-all'>":">".($Gg=="Grant option"?"":script("qsl('input').onclick = function () { if (this.checked) formUncheck('grants-$r-all'); };"))),"</label>";$r++;}}}echo"</table>\n",'<p>
<input type="submit" value="',lang(16),'">
';if(isset($_GET["host"]))echo'<input type="submit" name="drop" value="',lang(129),'">',confirm(lang(180,"$ea@$_GET[host]"));echo
input_token(),'</form>
';}elseif(isset($_GET["processlist"])){if(support("kill")){if($_POST&&!$k){$oe=0;foreach((array)$_POST["kill"]as$X){if(adminer()->killProcess($X))$oe++;}queries_redirect(ME."processlist=",lang(245,$oe),$oe||!$_POST["kill"]);}}page_header(lang(118),$k);echo'
<form action="" method="post">
<div class="scrollable">
<table class="nowrap checkable odds">
',script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});");$r=-1;foreach(adminer()->processList()as$r=>$K){if(!$r){echo"<thead><tr lang='en'>".(support("kill")?"<th>":"");foreach($K
as$w=>$X)echo"<th>$w".doc_link(array('sql'=>"show-processlist.html#processlist_".strtolower($w),));echo"</thead>\n";}echo"<tr>".(support("kill")?"<td>".checkbox("kill[]",$K[JUSH=="sql"?"Id":"pid"],0):"");foreach($K
as$w=>$X)echo"<td>".($X!=""&&((JUSH=="sql"&&$w=="Info"&&preg_match("~Query|Killed~",$K["Command"]))||(JUSH=="pgsql"&&$w=="query")||(JUSH=="oracle"&&$w=="sql_text"))?"<code class='jush-".JUSH."' data-full='".h($X)."'>".shorten_utf8($X,100,"</code>").' <a href="'.h(ME.($K["db"]!=""?"db=".urlencode($K["db"])."&":"")."sql=".urlencode($X)).'">'.lang(246).'</a>'.' <a href="" class="jsonly copy">🗐</a>':h($X));echo"\n";}echo'</table>
</div>
<p>
',script("copyCode(qsl('table'));");if(support("kill"))echo($r+1)."/".lang(247,max_connections()),"<p><input type='submit' value='".lang(248)."'>\n";echo
input_token(),'</form>
',script("tableCheck();");}elseif(isset($_GET["select"])){$a=$_GET["select"];$S=table_status1($a);$v=indexes($a);$m=fields($a);$bd=column_foreign_keys($a);$Bf=$S["Oid"];$la=get_settings("adminer_import");$dh=array();$c=array();$oh=array();$Qf=array();$li="";foreach($m
as$w=>$l){$C=adminer()->fieldName($l);$of=html_entity_decode(strip_tags($C),ENT_QUOTES);if(isset($l["privileges"]["select"])&&$C!=""){$c[$w]=$of;if(is_shortable($l))$li=adminer()->selectLengthProcess();}if(isset($l["privileges"]["where"])&&$C!="")$oh[$w]=$of;if(isset($l["privileges"]["order"])&&$C!="")$Qf[$w]=$of;$dh+=$l["privileges"];}list($M,$q)=adminer()->selectColumnsProcess($c,$v);$M=array_unique($M);$q=array_unique($q);$de=count($q)<count($M);$Z=adminer()->selectSearchProcess($m,$v);$D=adminer()->selectOrderProcess($m,$v);$y=adminer()->selectLimitProcess();if($_GET["val"]&&is_ajax()){header("Content-Type: text/plain; charset=utf-8");foreach($_GET["val"]as$Ki=>$K){$va=convert_field($m[key($K)]);$M=array($va?:idf_escape(key($K)));$Z[]=where_check($Ki,$m);$J=driver()->select($a,$M,$Z,$M);if($J)echo
first($J->fetch_row());}exit;}$Dg=$Mi=array();foreach($v
as$u){if($u["type"]=="PRIMARY"){$Dg=array_flip($u["columns"]);$Mi=($M?$Dg:array());foreach($Mi
as$w=>$X){if(in_array(idf_escape($w),$M))unset($Mi[$w]);}break;}}if($Bf&&!$Dg){$Dg=$Mi=array($Bf=>0);$v[]=array("type"=>"PRIMARY","columns"=>array($Bf));}if($_POST&&!$k){$mj=$Z;if(!$_POST["all"]&&is_array($_POST["check"])){$Xa=array();foreach($_POST["check"]as$Ua)$Xa[]=where_check($Ua,$m);$mj[]="((".implode(") OR (",$Xa)."))";}$mj=($mj?"\nWHERE ".implode(" AND ",$mj):"");if($_POST["export"]){save_settings(array("output"=>$_POST["output"],"format"=>$_POST["format"]),"adminer_import");dump_headers($a);adminer()->dumpTable($a,"");$fd=($M?implode(", ",$M):"*").convert_fields($c,$m,$M)."\nFROM ".table($a);$nd=($q&&$de?"\nGROUP BY ".implode(", ",$q):"").($D?"\nORDER BY ".implode(", ",$D):"");$H="SELECT $fd$mj$nd";if(is_array($_POST["check"])&&!$Dg){$Ii=array();foreach($_POST["check"]as$X)$Ii[]="(SELECT".limit($fd,"\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m).$nd,1).")";$H=implode(" UNION ALL ",$Ii);}adminer()->dumpData($a,"table",$H);adminer()->dumpFooter();exit;}if(!adminer()->selectEmailProcess($Z,$bd)){if($_POST["save"]||$_POST["delete"]){$I=true;$ma=0;$O=array();if(!$_POST["delete"]){foreach($m
as$C=>$X){$t=bracket_escape($C);if(isset($_POST["fields"][$t])||$_FILES["fields-$t"]){$X=process_input($m[$C]);if($X!==null&&($_POST["clone"]||$X!==false))$O[idf_escape($C)]=($X!==false?$X:idf_escape($C));}}}if($_POST["delete"]||$O){$H=($_POST["clone"]?"INTO ".table($a)." (".implode(", ",array_keys($O)).")\nSELECT ".implode(", ",$O)."\nFROM ".table($a):"");if($_POST["all"]||($Dg&&is_array($_POST["check"]))||$de){$I=($_POST["delete"]?driver()->delete($a,$mj):($_POST["clone"]?queries("INSERT $H$mj".driver()->insertReturning($a)):driver()->update($a,$O,$mj)));$ma=connection()->affected_rows;if(is_object($I))$ma+=$I->num_rows;}else{foreach((array)$_POST["check"]as$X){$lj="\nWHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($X,$m);$I=($_POST["delete"]?driver()->delete($a,$lj,1):($_POST["clone"]?queries("INSERT".limit1($a,$H,$lj)):driver()->update($a,$O,$lj,1)));if(!$I)break;$ma+=connection()->affected_rows;}}}$B=lang(249,$ma);if($_POST["clone"]&&$I&&$ma==1){$ue=last_id($I);if($ue)$B=lang(173," $ue");}queries_redirect(remove_from_uri($_POST["all"]&&$_POST["delete"]?"page":""),$B,$I);if(!$_POST["delete"]){$_g=(array)$_POST["fields"];edit_form($a,array_intersect_key($m,$_g),$_g,!$_POST["clone"],$k);page_footer();exit;}}elseif(!$_POST["import"]){if(!$_POST["val"])$k=lang(250);else{$I=true;$ma=0;foreach($_POST["val"]as$Ki=>$K){$O=array();foreach($K
as$w=>$X){$w=bracket_escape($w,true);$O[idf_escape($w)]=(preg_match('~char|text~',$m[$w]["type"])||$X!=""?adminer()->processInput($m[$w],$X):"NULL");}$I=driver()->update($a,$O," WHERE ".($Z?implode(" AND ",$Z)." AND ":"").where_check($Ki,$m),($de||$Dg?0:1)," ");if(!$I)break;$ma+=connection()->affected_rows;}queries_redirect(remove_from_uri(),lang(249,$ma),$I);}}elseif(!is_string($Qc=get_file("csv_file",true)))$k=upload_error($Qc);elseif(!preg_match('~~u',$Qc))$k=lang(251);else{save_settings(array("output"=>$la["output"],"format"=>$_POST["separator"]),"adminer_import");$I=true;$gb=array_keys($m);preg_match_all('~(?>"[^"]*"|[^"\r\n]+)+~',$Qc,$Me);$ma=count($Me[0]);driver()->begin();$uh=($_POST["separator"]=="csv"?",":($_POST["separator"]=="tsv"?"\t":";"));$L=array();foreach($Me[0]as$w=>$X){preg_match_all("~((?>\"[^\"]*\")+|[^$uh]*)$uh~",$X.$uh,$Ne);if(!$w&&!array_diff($Ne[1],$gb)){$gb=$Ne[1];$ma--;}else{$O=array();foreach($Ne[1]as$r=>$cb)$O[idf_escape($gb[$r])]=($cb==""&&$m[$gb[$r]]["null"]?"NULL":q(preg_match('~^".*"$~s',$cb)?str_replace('""','"',substr($cb,1,-1)):$cb));$L[]=$O;}}$I=(!$L||driver()->insertUpdate($a,$L,$Dg));if($I)driver()->commit();queries_redirect(remove_from_uri("page"),lang(252,$ma),$I);driver()->rollback();}}}$Yh=adminer()->tableName($S);if(is_ajax()){page_headers();ob_start();}else
page_header(lang(45).": $Yh",$k);$O=null;if(isset($dh["insert"])||!support("table")){$fg=array();foreach((array)$_GET["where"]as$X){if(isset($bd[$X["col"]])&&count($bd[$X["col"]])==1&&($X["op"]=="="||(!$X["op"]&&(is_array($X["val"])||!preg_match('~[_%]~',$X["val"])))))$fg["set"."[".bracket_escape($X["col"])."]"]=$X["val"];}$O=$fg?"&".http_build_query($fg):"";}adminer()->selectLinks($S,$O);if(!$c&&support("table"))echo"<p class='error'>".lang(253).($m?".":": ".error())."\n";else{echo"<form action='' id='form'>\n","<div style='display: none;'>";hidden_fields_get();echo(DB!=""?input_hidden("db",DB).(isset($_GET["ns"])?input_hidden("ns",$_GET["ns"]):""):""),input_hidden("select",$a),"</div>\n";adminer()->selectColumnsPrint($M,$c);adminer()->selectSearchPrint($Z,$oh,$v);adminer()->selectOrderPrint($D,$Qf,$v);adminer()->selectLimitPrint($y);adminer()->selectLengthPrint($li);adminer()->selectActionPrint($v);echo"</form>\n";$E=$_GET["page"];$ed=null;if($E=="last"){$ed=get_val(count_rows($a,$Z,$de,$q));$E=floor(max(0,intval($ed)-1)/$y);}$ph=$M;$md=$q;if(!$ph){$ph[]="*";$tb=convert_fields($c,$m,$M);if($tb)$ph[]=substr($tb,2);}foreach($M
as$w=>$X){$l=$m[idf_unescape($X)];if($l&&($va=convert_field($l)))$ph[$w]="$va AS $X";}if(!$de&&$Mi){foreach($Mi
as$w=>$X){$ph[]=idf_escape($w);if($md)$md[]=idf_escape($w);}}$I=driver()->select($a,$ph,$Z,$md,$D,$y,$E,true);if(!$I)echo"<p class='error'>".error()."\n";else{if(JUSH=="mssql"&&$E)$I->seek($y*$E);$nc=array();echo"<form action='' method='post' enctype='multipart/form-data'>\n";$L=array();while($K=$I->fetch_assoc()){if($E&&JUSH=="oracle")unset($K["RNUM"]);$L[]=$K;}if($_GET["page"]!="last"&&$y&&$q&&$de&&JUSH=="sql")$ed=get_val(" SELECT FOUND_ROWS()");if(!$L)echo"<p class='message'>".lang(14)."\n";else{$Ea=adminer()->backwardKeys($a,$Yh);echo"<div class='scrollable'>","<table id='table' class='nowrap checkable odds'>",script("mixin(qs('#table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true), onkeydown: editingKeydown});"),"<thead><tr>".(!$q&&$M?"":"<td><input type='checkbox' id='all-page' class='jsonly'>".script("qs('#all-page').onclick = partial(formCheck, /check/);","")." <a href='".h($_GET["modify"]?remove_from_uri("modify"):$_SERVER["REQUEST_URI"]."&modify=1")."'>".lang(254)."</a>");$pf=array();$hd=array();reset($M);$Pg=1;foreach($L[0]as$w=>$X){if(!isset($Mi[$w])){$X=idx($_GET["columns"],key($M))?:array();$l=$m[$M?($X?$X["col"]:current($M)):$w];$C=($l?adminer()->fieldName($l,$Pg):($X["fun"]?"*":h($w)));if($C!=""){$Pg++;$pf[$w]=$C;$b=idf_escape($w);$Bd=remove_from_uri('(order|desc)[^=]*|page').'&order%5B0%5D='.urlencode($w);$Qb="&desc%5B0%5D=1";echo"<th id='th[".h(bracket_escape($w))."]'>".script("mixin(qsl('th'), {onmouseover: partial(columnMouse), onmouseout: partial(columnMouse, ' hidden')});","");$gd=apply_sql_function($X["fun"],$C);$Dh=isset($l["privileges"]["order"])||$gd!=$C;echo($Dh?"<a href='".h($Bd.($D[0]==$b||$D[0]==$w?$Qb:''))."'>$gd</a>":$gd);$Ye=($Dh?"<a href='".h($Bd.$Qb)."' title='".lang(51)."' class='text'> ↓</a>":'');if(!$X["fun"]&&isset($l["privileges"]["where"])){$Ye
.='<a href="#fieldset-search" title="'.lang(48).'" class="text jsonly"> =</a>';$Ye
.=script("qsl('a').onclick = partial(selectSearch, '".js_escape($w)."');");}echo($Ye?"<span class='column hidden'>$Ye</span>":"");}$hd[$w]=$X["fun"];next($M);}}$Ae=array();if($_GET["modify"]){foreach($L
as$K){foreach($K
as$w=>$X)$Ae[$w]=max($Ae[$w],min(40,strlen(utf8_decode($X))));}}echo($Ea?"<th>".lang(255):"")."</thead>\n";if(is_ajax())ob_end_clean();foreach(adminer()->rowDescriptions($L,$bd)as$nf=>$K){$Ji=unique_array($L[$nf],$v);if(!$Ji){$Ji=array();reset($M);foreach($L[$nf]as$w=>$X){if(!preg_match('~^(COUNT|AVG|GROUP_CONCAT|MAX|MIN|SUM)\(~',current($M)))$Ji[$w]=$X;next($M);}}$Ki="";foreach($Ji
as$w=>$X){$l=(array)$m[$w];if((JUSH=="sql"||JUSH=="pgsql")&&preg_match('~char|text|enum|set~',$l["type"])&&strlen($X)>64){$w=(strpos($w,'(')?$w:idf_escape($w));$w="MD5(".(JUSH!='sql'||preg_match("~^utf8~",$l["collation"])?$w:"CONVERT($w USING ".charset(connection()).")").")";$X=md5($X);}$Ki
.="&".($X!==null?urlencode("where[".bracket_escape($w)."]")."=".urlencode($X===false?"f":$X):"null%5B%5D=".urlencode($w));}echo"<tr>".(!$q&&$M?"":"<td>".checkbox("check[]",substr($Ki,1),in_array(substr($Ki,1),(array)$_POST["check"])).($de||information_schema(DB)?"":" <a href='".h(ME."edit=".urlencode($a).$Ki)."' class='edit'>".lang(256)."</a>"));reset($M);foreach($K
as$w=>$X){if(isset($pf[$w])){$b=current($M);$l=(array)$m[$w];if($X!=""&&(!isset($nc[$w])||$nc[$w]!=""))$nc[$w]=(is_mail($X)?$pf[$w]:"");$z="";if(is_blob($l)&&$X!="")$z=ME.'download='.urlencode($a).'&field='.urlencode($w).$Ki;if(!$z&&$X!==null){foreach((array)$bd[$w]as$n){if(count($bd[$w])==1||end($n["source"])==$w){$z="";foreach($n["source"]as$r=>$Eh)$z
.=where_link($r,$n["target"][$r],$L[$nf][$Eh]);$z=($n["db"]!=""?preg_replace('~([?&]db=)[^&]+~','\1'.urlencode($n["db"]),ME):ME).'select='.urlencode($n["table"]).$z;if($n["ns"])$z=preg_replace('~([?&]ns=)[^&]+~','\1'.urlencode($n["ns"]),$z);if(count($n["source"])==1)break;}}}if($b=="COUNT(*)"){$z=ME."select=".urlencode($a);$r=0;foreach((array)$_GET["where"]as$W){if(!array_key_exists($W["col"],$Ji))$z
.=where_link($r++,$W["col"],$W["val"],$W["op"]);}foreach($Ji
as$le=>$W)$z
.=where_link($r++,$le,$W);}$Cd=select_value($X,$z,$l,$li);$s=h("val[$Ki][".bracket_escape($w)."]");$Ag=idx(idx($_POST["val"],$Ki),bracket_escape($w));$Oi=idx($l["privileges"],"update");$jc=!is_array($K[$w])&&is_utf8($Cd)&&$L[$nf][$w]==$K[$w]&&!$hd[$w]&&!$l["generated"]&&$Oi;$U=(preg_match('~^(AVG|MIN|MAX)\((.+)\)~',$b,$A)?$m[idf_unescape($A[2])]["type"]:$l["type"]);$ki=preg_match('~text|json|lob~',$U);$ee=preg_match(number_type(),$U)||preg_match('~^(CHAR_LENGTH|ROUND|FLOOR|CEIL|TIME_TO_SEC|COUNT|SUM)\(~',$b);echo"<td id='$s'".($ee&&($X===null||is_numeric(strip_tags($Cd))||$U=="money")?" class='number'":"");if(($_GET["modify"]&&$jc&&$X!==null)||$Ag!==null){$qd=h($Ag!==null?$Ag:$K[$w]);echo">".($ki?"<textarea name='$s' cols='30' rows='".(substr_count($K[$w],"\n")+1)."'>$qd</textarea>":"<input name='$s' value='$qd' size='$Ae[$w]'>");}else{$Ie=strpos($Cd,"<i>…</i>");echo($Oi?" data-text='".($Ie?2:($ki?1:0))."'".($jc?"":" data-warning='".h(lang(257))."'"):"").">$Cd";}}next($M);}if($Ea)echo"<td>";adminer()->backwardKeysPrint($Ea,$L[$nf]);echo"</tr>\n";}if(is_ajax())exit;echo"</table>\n","</div>\n";}if(!is_ajax()){if($L||$E){$_c=true;if($_GET["page"]!="last"){if(!$y||(count($L)<$y&&($L||!$E)))$ed=($E?$E*$y:0)+count($L);elseif(JUSH!="sql"||!$de){$ed=($de?false:found_rows($S,$Z));if(intval($ed)<max(1e4,2*($E+1)*$y))$ed=first(slow_query(count_rows($a,$Z,$de,$q)));elseif(JUSH=='sql'||JUSH=='pgsql')$_c=false;}}$dg=($y&&($ed===false||$ed>$y||$E));if($dg)echo(($ed===false?count($L)+1:$ed-$E*$y)>$y?'<p><a href="'.h(remove_from_uri("page|next").($_GET["next"]?"&next=".urlencode($_GET["next"]):"")."&page=".($E+1)).'" class="loadmore">'.lang(258).'</a>'.script("qsl('a').onclick = partial(selectLoadMore, $y, '".lang(259)."…');",""):''),"\n";echo"<div class='footer'><div>\n";if($dg){$Re=($ed===false?$E+($L?(count($L)>=$y?2:1):0):floor(($ed-1)/$y));echo"<fieldset>";if(JUSH!="simpledb"&&JUSH!="redis"){echo"<legend><a href='".h(remove_from_uri("page"))."'>".lang(260)."</a></legend>",script("qsl('a').onclick = function () { pageClick(this.href, +prompt('".lang(260)."', '".($E+1)."')); return false; };"),pagination(0,$E).($E>5?" …":"");for($r=max(1,$E-4);$r<min($Re,$E+5);$r++)echo
pagination($r,$E);if($Re>0)echo($E+5<$Re?" …":""),($_c&&$ed!==false?pagination($Re,$E):" <a href='".h(remove_from_uri("page")."&page=last")."' title='~$Re'>".lang(261)."</a>");}else
echo"<legend>".lang(260)."</legend>",pagination(0,$E).($E>1?" …":""),($E?pagination($E,$E):""),($Re>$E?pagination($E+1,$E).($Re>$E+1?" …":""):"");echo"</fieldset>\n";}echo"<fieldset>","<legend>".lang(262)."</legend>";$Ub=($_c?"":"~ ").$ed;$Jf="const checked = formChecked(this, /check/); selectCount('selected', this.checked ? '$Ub' : checked); selectCount('selected2', this.checked || !checked ? '$Ub' : checked);";echo
checkbox("all",1,0,($ed!==false?($_c?"":"~ ").lang(156,$ed):""),$Jf)."\n","</fieldset>\n";if(adminer()->selectCommandPrint())echo'<fieldset',($_GET["modify"]?'':' class="jsonly"'),'><legend>',lang(254),'</legend><div>
<input type="submit" value="',lang(16),'"',($_GET["modify"]?'':' title="'.lang(250).'"'),'>
</div></fieldset>
<fieldset><legend>',lang(128),' <span id="selected"></span></legend><div>
<input type="submit" name="edit" value="',lang(12),'">
<input type="submit" name="clone" value="',lang(246),'">
<input type="submit" name="delete" value="',lang(20),'">',confirm(),'</div></fieldset>
';$cd=adminer()->dumpFormat();foreach((array)$_GET["columns"]as$b){if($b["fun"]){unset($cd['sql']);break;}}if($cd){print_fieldset("export",lang(68)." <span id='selected2'></span>");$bg=adminer()->dumpOutput();echo($bg?html_select("output",$bg,$la["output"])." ":""),html_select("format",$cd,$la["format"])," <input type='submit' name='export' value='".lang(68)."'>\n","</div></fieldset>\n";}adminer()->selectEmailPrint(array_filter($nc,'strlen'),$c);echo"</div></div>\n";}if(adminer()->selectImportPrint())echo"<p>","<a href='#import'>".lang(67)."</a>",script("qsl('a').onclick = partial(toggle, 'import');",""),"<span id='import'".($_POST["import"]?"":" class='hidden'").">: ",file_input("<input type='file' name='csv_file'> ".html_select("separator",array("csv"=>"CSV,","csv;"=>"CSV;","tsv"=>"TSV"),$la["format"])." <input type='submit' name='import' value='".lang(67)."'>"),"</span>";echo
input_token(),"</form>\n",(!$q&&$M?"":script("tableCheck();"));}}}if(is_ajax()){ob_end_clean();exit;}}elseif(isset($_GET["variables"])){$P=isset($_GET["status"]);page_header($P?lang(120):lang(119));$cj=($P?adminer()->showStatus():adminer()->showVariables());if(!$cj)echo"<p class='message'>".lang(14)."\n";else{echo"<table>\n";foreach($cj
as$K){echo"<tr>";$w=array_shift($K);echo"<th><code class='jush-".JUSH.($P?"status":"set")."'>".h($w)."</code>";foreach($K
as$X)echo"<td>".nl_br(h($X));}echo"</table>\n";}}elseif(isset($_GET["script"])){header("Content-Type: text/javascript; charset=utf-8");if($_GET["script"]=="db"){$Th=array("Data_length"=>0,"Index_length"=>0,"Data_free"=>0);foreach(table_status()as$C=>$S){json_row("Comment-$C",h($S["Comment"]));if(!is_view($S)||preg_match('~materialized~i',$S["Engine"])){foreach(array("Engine","Collation")as$w)json_row("$w-$C",h($S[$w]));foreach($Th+array("Auto_increment"=>0,"Rows"=>0)as$w=>$X){if($S[$w]!=""){$X=format_number($S[$w]);if($X>=0)json_row("$w-$C",($w=="Rows"&&$X&&$S["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?"~ $X":$X));if(isset($Th[$w]))$Th[$w]+=($S["Engine"]!="InnoDB"||$w!="Data_free"?$S[$w]:0);}elseif(array_key_exists($w,$S))json_row("$w-$C","?");}}}foreach($Th
as$w=>$X)json_row("sum-$w",format_number($X));json_row("");}elseif($_GET["script"]=="kill")connection()->query("KILL ".number($_POST["kill"]));else{foreach(count_tables(adminer()->databases())as$i=>$X){json_row("tables-$i",$X);json_row("size-$i",db_size($i));}json_row("");}exit;}else{$ei=array_merge((array)$_POST["tables"],(array)$_POST["views"]);if($ei&&!$k&&!$_POST["search"]){$I=true;$B="";if(JUSH=="sql"&&$_POST["tables"]&&count($_POST["tables"])>1&&($_POST["drop"]||$_POST["truncate"]||$_POST["copy"]))queries("SET foreign_key_checks = 0");if($_POST["truncate"]){if($_POST["tables"])$I=truncate_tables($_POST["tables"]);$B=lang(263);}elseif($_POST["move"]){$I=move_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$B=lang(264);}elseif($_POST["copy"]){$I=copy_tables((array)$_POST["tables"],(array)$_POST["views"],$_POST["target"]);$B=lang(265);}elseif($_POST["drop"]){if($_POST["views"])$I=drop_views($_POST["views"]);if($I&&$_POST["tables"])$I=drop_tables($_POST["tables"]);$B=lang(266);}elseif(JUSH=="sqlite"&&$_POST["check"]){foreach((array)$_POST["tables"]as$R){foreach(get_rows("PRAGMA integrity_check(".q($R).")")as$K)$B
.="<b>".h($R)."</b>: ".h($K["integrity_check"])."<br>";}}elseif(JUSH!="sql"){$I=(JUSH=="sqlite"?queries("VACUUM"):apply_queries("VACUUM".($_POST["optimize"]?" ANALYZE":""),$_POST["tables"]));$B=lang(267);}elseif(!$_POST["tables"])$B=lang(11);elseif($I=queries(($_POST["optimize"]?"OPTIMIZE":($_POST["check"]?"CHECK":($_POST["repair"]?"REPAIR":"ANALYZE")))." TABLE ".implode(", ",array_map('Adminer\idf_escape',$_POST["tables"])))){while($K=$I->fetch_assoc())$B
.="<b>".h($K["Table"])."</b>: ".h($K["Msg_text"])."<br>";}queries_redirect($_SERVER["REQUEST_URI"],$B,$I);}page_header(($_GET["ns"]==""?lang(29).": ".h(DB):lang(151).": ".h($_GET["ns"])),$k,true);if(adminer()->homepage()){if($_GET["ns"]!==""){$D=$_GET["order"];echo"<h3 id='tables-views'>".lang(268)."</h3>\n";$di=($D?table_status():tables_list());if(!$di)echo"<p class='message'>".lang(11)."\n";else{echo"<form action='' method='post'>\n";if(support("table")){echo"<fieldset><legend>".lang(269)." <span id='selected2'></span></legend><div>",html_select("op",adminer()->operators(),idx($_POST,"op",JUSH=="elastic"?"should":"LIKE %%"))," <input type='search' name='query' value='".h($_POST["query"])."'>",script("qsl('input').onkeydown = partialArg(bodyKeydown, 'search');","")," <input type='submit' name='search' value='".lang(48)."'>\n","</div></fieldset>\n";if($_POST["search"]&&$_POST["query"]!=""){$_GET["where"][0]["op"]=$_POST["op"];search_tables();}}echo"<div class='scrollable'>\n","<table class='nowrap checkable odds'>\n",script("mixin(qsl('table'), {onclick: tableClick, ondblclick: partialArg(tableClick, true)});"),'<thead><tr class="wrap">','<td><input id="check-all" type="checkbox" class="jsonly">'.script("qs('#check-all').onclick = partial(formCheck, /^(tables|views)\[/);",""),'<th><a href="'.h(substr(ME,0,-1)).'">'.lang(134).'</a>';$c=array("Engine"=>array(lang(270).doc_link(array('sql'=>'storage-engines.html'))));if(collations())$c["Collation"]=array(lang(124).doc_link(array('sql'=>'charset-charsets.html','mariadb'=>'supported-character-sets-and-collations/')));if(function_exists('Adminer\alter_table'))$c["Data_length"]=array(lang(271).doc_link(array('sql'=>'show-table-status.html',)),"create",lang(36));if(support('indexes'))$c["Index_length"]=array(lang(272).doc_link(array('sql'=>'show-table-status.html',)),"indexes",lang(137));$c["Data_free"]=array(lang(273).doc_link(array('sql'=>'show-table-status.html')),"edit",lang(37));if(function_exists('Adminer\alter_table'))$c["Auto_increment"]=array(lang(43).doc_link(array('sql'=>'example-auto-increment.html','mariadb'=>'auto_increment/')),"auto_increment=1&create",lang(36));$c["Rows"]=array(lang(274).doc_link(array('sql'=>'show-table-status.html',)),"select",lang(33));if(support("comment"))$c["Comment"]=array(lang(42).doc_link(array('sql'=>'show-table-status.html',)));foreach($c
as$w=>$b)echo"<th><a href='".h(ME)."order=$w'>$b[0]</a>";echo"</thead>\n";if($D){uasort($di,function($ga,$Ba)use($D){$J=($ga[$D]<$Ba[$D]?-1:($ga[$D]>$Ba[$D]?1:0));return(in_array($D,array('Engine','Collation','Comment'))?$J:-$J);});}$T=0;foreach($di
as$C=>$P){$fj=($D?is_view($P):$P!==null&&!preg_match('~table|sequence~i',$P));$P=($D?$P:array('Engine'=>$P));$s=h("Table-".$C);echo'<tr><td>'.checkbox(($fj?"views[]":"tables[]"),$C,in_array("$C",$ei,true),"","","",$s),'<th>'.(support("table")||support("indexes")?"<a href='".h(ME)."table=".urlencode($C)."' title='".lang(34)."' id='$s'>".h($C).'</a>':h($C));if($fj&&!preg_match('~materialized~i',$P['Engine'])){$pi=lang(133);echo'<td colspan="'.(count($c)-(support("comment")?2:1)).'">'.(support("view")?"<a href='".h(ME)."view=".urlencode($C)."' title='".lang(35)."'>$pi</a>":$pi),'<td align="right"><a href="'.h(ME)."select=".urlencode($C).'" title="'.lang(33).'">?</a>';if(support("comment"))echo'<td>'.h($P['Comment']);}else{foreach($c
as$w=>$b){$s=" id='$w-".h($C)."'";$X=idx($P,$w,'?');echo($b[1]?"<td align='right'><a href='".h(ME."$b[1]=").urlencode($C)."'$s title='$b[2]'>".(is_numeric($X)?($X<0?'?':($w=="Rows"&&$X&&$P["Engine"]==(JUSH=="pgsql"?"table":"InnoDB")?'~ ':'').format_number($X)):$X)."</a>":"<td id='$w-".h($C)."'>".h($X));}$T++;}echo"\n";}echo"<tr><td><th>".lang(247,count($di)),"<td>".h(JUSH=="sql"?get_val("SELECT @@default_storage_engine"):""),(collations()?"<td>".h(db_collation(DB,collations())):'');foreach(array("Data_length","Index_length","Data_free")as$w)echo($c[$w]?"<td align='right' id='sum-$w'>":"");echo"\n","</table>\n",($D?'':script("ajaxSetHtml('".js_escape(ME)."script=db');")),"</div>\n";if(!information_schema(DB)){$Zi="<input type='submit' value='".lang(275)."'> ".on_help("'VACUUM'");$Mf="<input type='submit' name='optimize' value='".lang(276)."'> ".on_help(JUSH=="sql"?"'OPTIMIZE TABLE'":"'VACUUM ANALYZE'");$Eg=(JUSH=="sqlite"?$Zi."<input type='submit' name='check' value='".lang(277)."'> ".on_help("'PRAGMA integrity_check'"):(JUSH=="pgsql"?$Zi.$Mf:(JUSH=="sql"?"<input type='submit' value='".lang(278)."'> ".on_help("'ANALYZE TABLE'").$Mf."<input type='submit' name='check' value='".lang(277)."'> ".on_help("'CHECK TABLE'")."<input type='submit' name='repair' value='".lang(279)."'> ".on_help("'REPAIR TABLE'"):""))).(function_exists('Adminer\truncate_tables')?"<input type='submit' name='truncate' value='".lang(280)."'> ".on_help(JUSH=="sqlite"?"'DELETE'":"'TRUNCATE".(JUSH=="pgsql"?"'":" TABLE'")).confirm():"").(function_exists('Adminer\drop_tables')?"<input type='submit' name='drop' value='".lang(129)."'>".on_help("'DROP TABLE'").confirm():"");echo($Eg?"<div class='footer'><div>\n<fieldset><legend>".lang(128)." <span id='selected'></span></legend><div>$Eg\n</div></fieldset>\n":"");$h=(support("scheme")?adminer()->schemas():adminer()->databases());$nh="";if(count($h)!=1&&function_exists('Adminer\move_tables')){echo"<fieldset><legend>".lang(281)." <span id='selected3'></span></legend><div>";$i=(isset($_POST["target"])?$_POST["target"]:(support("scheme")?$_GET["ns"]:DB));echo($h?html_select("target",$h,$i):'<input name="target" value="'.h($i).'" autocapitalize="off">'),"</label> <input type='submit' name='move' value='".lang(112)."'>",(support("copy")?" <input type='submit' name='copy' value='".lang(282)."'> ".checkbox("overwrite",1,$_POST["overwrite"],lang(283)):""),"</div></fieldset>\n";$nh=" selectCount('selected3', formChecked(this, /^(tables|views)\[/));";}echo"<input type='hidden' name='all' value=''>",script("qsl('input').onclick = function () { selectCount('selected', formChecked(this, /^(tables|views)\[/));".(support("table")?" selectCount('selected2', formChecked(this, /^tables\[/) || $T);":"")."$nh }"),input_token(),"</div></div>\n";}echo"</form>\n",script("tableCheck();");}echo(function_exists('Adminer\alter_table')?"<p class='links'><a href='".h(ME)."create='>".lang(69)."</a>\n":''),(support("view")?"<a href='".h(ME)."view='>".lang(209)."</a>\n":"");if(support("routine")){echo"<h3 id='routines'>".lang(64)."</h3>\n";$hh=routines();if($hh){echo"<table class='odds'>\n",'<thead><tr><th>'.lang(190).'<td>'.lang(41).'<td>'.lang(226)."<td></thead>\n";foreach($hh
as$K){$C=($K["SPECIFIC_NAME"]==$K["ROUTINE_NAME"]?"":"&name=".urlencode($K["ROUTINE_NAME"]));echo'<tr>','<th><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'callf=':'call=').urlencode($K["SPECIFIC_NAME"]).$C).'">'.h($K["ROUTINE_NAME"]).'</a>','<td>'.h($K["ROUTINE_TYPE"]),'<td>'.h($K["DTD_IDENTIFIER"]),'<td><a href="'.h(ME.($K["ROUTINE_TYPE"]!="PROCEDURE"?'function=':'procedure=').urlencode($K["SPECIFIC_NAME"]).$C).'">'.lang(140)."</a>";}echo"</table>\n";}echo'<p class="links">'.(support("procedure")?'<a href="'.h(ME).'procedure=">'.lang(225).'</a>':'').'<a href="'.h(ME).'function=">'.lang(224)."</a>\n";}if(support("event")){echo"<h3 id='events'>".lang(66)."</h3>\n";$L=get_rows("SHOW EVENTS");if($L){echo"<table>\n","<thead><tr><th>".lang(190)."<td>".lang(284)."<td>".lang(215)."<td>".lang(216)."<td></thead>\n";foreach($L
as$K)echo"<tr>","<th>".h($K["Name"]),"<td>".($K["Execute at"]?lang(285)."<td>".$K["Execute at"]:lang(217)." ".$K["Interval value"]." ".$K["Interval field"]."<td>$K[Starts]"),"<td>$K[Ends]",'<td><a href="'.h(ME).'event='.urlencode($K["Name"]).'">'.lang(140).'</a>';echo"</table>\n";$yc=get_val("SELECT @@event_scheduler");if($yc&&$yc!="ON")echo"<p class='error'><code class='jush-sqlset'>event_scheduler</code>: ".h($yc)."\n";}echo'<p class="links"><a href="'.h(ME).'event=">'.lang(214)."</a>\n";}}}}page_footer();