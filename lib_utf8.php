<?php
// http://jmnote.com/wiki/Lib_utf8.php
function utf2euc($str) { return iconv("UTF-8","cp949//IGNORE", $str); }
function euc2utf($str) { return iconv("cp949","UTF-8//IGNORE", $str); }
function utf8_charAt($str, $num) { return mb_substr($str, $num, 1, 'UTF-8'); }
function utf8_strlen($str) { return mb_strlen($str, 'UTF-8'); }
function utf8_substr($str, $start, $len=-1) {
  if($len==-1)$len = mb_strlen($str, 'UTF-8')-$start;
  return mb_substr($str, $start, $len, 'UTF-8');
}

function utf8_chr($num) {
   if($num<128) return chr($num);
   if($num<2048) return chr(($num>>6)+192).chr(($num&63)+128);
   if($num<65536) return chr(($num>>12)+224).chr((($num>>6)&63)+128).chr(($num&63)+128);
   if($num<2097152) return chr(($num>>18)+240).chr((($num>>12)&63)+128).chr((($num>>6)&63)+128).chr(($num&63)+128);
   return false;
}

function utf8_ord($ch) {
  $len = strlen($ch);
  if($len <= 0) return false;
  $h = ord($ch{0});
  if ($h <= 0x7F) return $h;
  if ($h < 0xC2) return false;
  if ($h <= 0xDF && $len>1) return ($h & 0x1F) <<  6 | (ord($ch{1}) & 0x3F);
  if ($h <= 0xEF && $len>2) return ($h & 0x0F) << 12 | (ord($ch{1}) & 0x3F) << 6 | (ord($ch{2}) & 0x3F);          
  if ($h <= 0xF4 && $len>3) return ($h & 0x0F) << 18 | (ord($ch{1}) & 0x3F) << 12 | (ord($ch{2}) & 0x3F) << 6 | (ord($ch{3}) & 0x3F);
  return false;
}
?>