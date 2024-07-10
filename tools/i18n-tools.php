<?php

// note: this file is only for use by translators - do NOT use this enabled in production!
exit;

// define required config values
$config = array(
	'root' => dirname(dirname(__FILE__)),
);

// load main translations class
require_once($config['root'] . '/inc/i18n.php');

// anything to do?
$langs = $i18n->get_languages();
if(!empty($_POST['new']) && empty($langs[$_POST['new']]) && empty($_POST['lang'])) {
	$locale = $_POST['new'];
	$translations = array();
}
elseif(!empty($_POST['lang']) && !empty($langs[$_POST['lang']]) && 'en_US' != $_POST['lang'] && empty($_POST['new'])) {
	$locale = $_POST['lang'];
	// load previous translations
	$file = $config['root'] . '/lang/' . $_POST['lang'] . '.php';
	$translations = (file_exists($file)) ? include($file) : array();
}
if(isset($translations)) {

	// get all php files with language strings
	function get_files($dir, $files, $top = false){
	    $entries = scandir($dir);
		// on top level exclude "lang" and "tools" folders as well as "config.php" file
		$excluded = ($top) ? array('.', '..', 'lang', 'tools', 'config.php'): array('.', '..');
		foreach($entries as $entry) {
			if(in_array($entry, $excluded)) {
				continue;
			}
			elseif(is_dir($dir . '/' . $entry)) {
				$files = get_files($dir . '/' . $entry, $files);
			}
			elseif('.php' == substr($entry, -4)) {
				$files[] = $dir. '/' . $entry;
			}
		}
		return $files;
	}
	$files = get_files($config['root'], array(), true);

	// get all language strings and translator comments from files
	$confirmed = array($locale);
	foreach($files as $file) {
		$matches = array();
		$string = false;
		/*
		https://regex101.com/r/sbL2P7/1
		note: "g" modifier does not exist in PHP, using preg_match_all instead
		note: all regex need to convert used \ into \\ in PHP due to the first backslash consumed by PHP itself
		identify multiline comments: /\/\*[\s\S]*?\*\//mg
		ignore everything after // until end of line: /\/\/.*$/mg
		get translatable strings & translator comments: /(?:__\(\s*('|")(.*?)(?<!\\)\1\s*\)|(\/\/\s*i18n:.*$))/mg
		add names to match groups: ?<string> and ?<comment>
		only match inline comments not full line comments: (?<!^)
		*/
		preg_match_all('/(?:\\/\\*[\\s\\S]*?\\*\\/|\\/\\/(?!\\s*i18n:).*$|__\\(\\s*(\'|")(?<string>.*?)(?<!\\\\)\\1\\s*\\)|(?<comment>(?<!^)\\/\\/\\s*i18n:.*$))/m', file_get_contents($file), $matches, PREG_SET_ORDER);
		foreach($matches as $match) {
			if(!empty($match['string'])) {
				if(in_array($match['string'], $confirmed)) {
					$ignored = true;
				}
				else {
					$string = true;
					$ignored = false;
					$confirmed[] = $match['string'];
				}
			}
			elseif(!empty($match['comment']) && $string && !$ignored) {
				$string = false;
				$confirmed[] = $match['comment'];
			}
		}
	}

	// compile new/updated source + translation + comment list
	$pairs = array();
	$source = $translation = $comment = '';
	foreach($confirmed as $string) {
		// check for translator comment
		if(substr($string, 0, 2) === '//') {
			$comment = $string;
			continue;
		}
		// previous string to be recorded
		if(!empty($source)) {
			$pairs[] = array('source' => $source, 'translation' => $translation, 'comment' => $comment);
		}
		$source = $string;
		$translation = (!empty($translations[$string])) ? $translations[$string] : '';
		$comment = '';
	}
	// make sure we record any last string as well
	if(!empty($source)) {
		$pairs[] = array('source' => $source, 'translation' => $translation, 'comment' => $comment);
	}

	echo '<!doctype html><html>';
	echo '<head>';
	echo '<title>i18n Tools</title>'; // i18n: note for translator
	echo '</head>';
	echo '<body>';
	// create i18n language file
	echo '<pre>';
	echo '&lt;?php' . "\n\n";
	echo '/*' . "\n";
	echo 'This is a translation file, only edit the translation string in between \'\' on the right hand side of each line!' . "\n";
	echo '\'source\' => \'translation\', // i18n: comment' . "\n\n";
	echo 'Usage: The file needs to be stored in the "lang" folder and named according to the locale standard followed by ".php"' . "\n";
	echo '/lang/de_DE.php' . "\n\n";
	echo 'IMPORTANT: Single quotes \' must be escaped in the translation using a backslash before \\\'' . "\n\n";
	echo 'IMPORTANT: First entry in a translation file should be \'locale_as_per_international_standard\' => \'translated language name\'' . "\n";
	echo '\'de_DE\' => \'Deutsch\',' . "\n\n";
	echo 'Note: Can also be used to "translate" date, time and number formats by applying translation as format eg date(__(\'m/d/Y\'), time())' . "\n";
	echo '\'m/d/Y\' => \'d.m.Y\',' . "\n";
	echo '\'g:i a\' => \'G:i\',' . "\n";
	echo '\'#,###\' => \'#.###\',' . "\n";
	echo '\'#.##\' => \'#,##\',' . "\n";
	echo '*/' . "\n\n";
	echo 'return array(' . "\n";
	echo '  // IMPORTANT: See info about mandatory first entry in each translation file above!' . "\n";
	foreach($pairs as $pair) {
		echo '  \'' . $pair['source'] . '\' => \'' . $pair['translation'] . '\', ' . $pair['comment'] . "\n";
	}
	echo ');' . "\n\n";
	echo '?&gt;';
	echo '</pre>';
	echo '</body>';
	echo '</html>';

}
// ask translator what to do
else {
	echo '<!doctype html><html>';
	echo '<head>';
	echo '<title>i18n Tools</title>'; // i18n: note for translator
	echo '</head>';
	echo '<body>';
	echo '<form method="post">';
	echo '<label for="langs">Update existing language file (except "en_US" which is hard coded):</label><br>';
	echo '<select name="lang" id="langs">';
	echo '<option value="" selected="selected"></option>';
	foreach($i18n->get_languages() as $locale => $language) {
		if('en_US' != $locale) {
			echo '<option value="' . $locale . '">' . $language . '</option>';
		}
	}
	echo '</select><br><br>';
	echo '<label for="new">Or create new language file (enter locale, eg "de_DE"):</label><br>';
	echo '<input type="text" name="new" id="new" value=""><br><br>';
	echo '<input type="submit" value="Submit">';
	echo '</form>';
	echo '</body>';
	echo '</html>';
}

?>
