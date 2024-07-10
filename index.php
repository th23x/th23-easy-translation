<?php

// load basic config
require_once('config.php');

// define basic variables
$basics = array();
$basics['root'] = dirname(__FILE__);

// load translations
require_once($basics['root'] . '/inc/i18n.php');

// set language based on user selection
$langs = $i18n->get_languages();
$lang = (!empty($_POST['lang']) && !empty($langs[$_POST['lang']])) ? $_POST['lang'] : $config['default_lang'];
$i18n->change_language($lang);

// html page with translations
echo '<!doctype html><html>';
echo '<head>';
echo '<title>' . __('Translated title') . '</title>';
echo '</head>';
echo '<body>';
echo __('Here you see %s at work...', 'th23 Easy Translation'); // i18n: parses in script name

// simple language selection
echo '<form action="." method="post">';
echo '<select name="lang" id="langs">';
foreach($langs as $locale => $language) {
	$selected = ($locale == $lang) ? ' selected="selected"' : '';
	echo '<option value="' . $locale . '"' . $selected . '>' . $language . '</option>';
}
echo '</select><br><br>';
echo '<input type="submit" value="' . __('Submit') . '">';
echo '</form>';

echo '</body>';
echo '</html>';

?>
