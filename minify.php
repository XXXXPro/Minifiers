<?php

/******************************
 * Simple HTML code minifier.
 * Removes unnecessary spaces between tags, double spaces, replaces some html entities with unicode symbols
 * Optionally can remove HTML comments
 * Content of pre, code and script tags remains unchanged.
 *
 * Written by 4X_Pro, 2017 (http:/xpro.su)
 ******************************/

function minify_html($text,$remove_comments = true) {
  $key=md5(mt_rand()).'-';

  // processing pre tag (saving its contents)
  $pre_count=preg_match_all('|(<pre[^>]*>.*?</pre>)|is',$text,$pre_matches);
  for ($i=0; $i<$pre_count; $i++) $text=str_replace($pre_matches[0][$i],'<PRE|'.$i.'|'.$key.'>',$text);
  // processing code tag
  $code_count=preg_match_all('|(<code[^>]*>.*?</code>)|is',$text,$code_matches);
  for ($i=0; $i<$code_count; $i++) $text=str_replace($code_matches[0][$i],'<CODE|'.$i.'|'.$key.'>',$text);

  // processing script tag
  $script_count=preg_match_all('|(<script[^>]*>.*?</script>)|is',$text,$script_matches);
  for ($i=0; $i<$script_count; $i++) $text=str_replace($script_matches[0][$i],'<SCRIPT|'.$i.'|'.$key.'>',$text);

  // processing comments if they not to be removed
  if (!$remove_comments) {
    $comment_count=preg_match_all('|(<!--.*?-->)|s',$text,$comment_matches);
    for ($i=0; $i<$comment_count; $i++) $text=str_replace($comment_matches[0][$i],'<COMMENT|'.$i.'|'.$key.'>',$text);
  }

  // removing comments if need
  if ($remove_comments) {
    $text = preg_replace('|(<!--.*?-->)|s','',$text);
  }

  // replacing html entities
  $text = preg_replace('|&nbsp;|',' ',$text); // replacing with non-breaking space (symbol 160 in Unicode)
  $text = preg_replace('|&mdash;|','—',$text);
  $text = preg_replace('|&ndash;|','–',$text);
  $text = preg_replace('|&laquo;|','«',$text);
  $text = preg_replace('|&raquo;|','»',$text);
  $text = preg_replace('|&bdquo;|','„',$text);
  $text = preg_replace('|&ldquo;|','“',$text);

  $text = preg_replace('|(</?\w+[^>]+?)\s+(/?>)|s','$1$2',$text); // removing all contunous spaces
  while (preg_match('|<(/?\w+[^>]+/?)>\s+<(/?\w+?)|s',$text)) {
    $text = preg_replace('|<(/?\w+[^>]+/?)>\s+<(/?\w+?)|s','<$1><$2',$text); // removing all spaces and newlines between tags
  }
  $text = preg_replace('|\s\s+|s',' ',$text); // removing all contunous spaces

  // restoring processed comments
  if (!$remove_comments) {
    for ($i=0; $i<$comment_count; $i++) $text=str_replace('<COMMENT|'.$i.'|'.$key.'>',$comment_matches[0][$i],$text);
  }
  // restoring script tag
  for ($i=0; $i<$script_count; $i++) $text=str_replace('<SCRIPT|'.$i.'|'.$key.'>',$script_matches[0][$i],$text);
  // restoring code tag
  for ($i=0; $i<$code_count; $i++) $text=str_replace('<CODE|'.$i.'|'.$key.'>',$code_matches[0][$i],$text);
  // restoring pre tag
  for ($i=0; $i<$pre_count; $i++) $text=str_replace('<PRE|'.$i.'|'.$key.'>',$pre_matches[0][$i],$text);
  return $text;
}

