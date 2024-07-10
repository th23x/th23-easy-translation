<?php

// load and apply translations
class i18n {

	// module information
	public $info;

	// current language code
	public $lang;

	private $translations;

	// load default language
	public function __construct() {
		$this->info = array(
			'module' => 'th23_easy_translation',
			'version' => '0.2.0',
		);
		$this->requirements();

		global $config;
		$this->lang = (!empty($config['default_lang'])) ? $config['default_lang'] : 'en_US';
		$this->load_translations();
	}

	// get all available languages, based on available translation files in /lang directory
	public function get_languages() {
		global $basics;
		// English is available even without translation file
		$langs = array('en_US' => 'English');
		foreach(glob($basics['root'] . '/lang/*.php') as $file) {
			$lang = include($file);
			// first entry in translation file should be in the form 'locale' => 'translated language name'
			if(!empty($lang) && !empty($key = array_key_first($lang)) && !empty($value = $lang[$key])) {
				$langs[$key] = $value;
			}
		}
		return $langs;
	}

	// change language to be used for translations
	public function change_language($language) {
		$this->lang = $language;
		$this->load_translations();
	}

	// load all translations for current language from respective translation file
	private function load_translations() {
		global $basics;
		$file = $basics['root'] . '/lang/' . $this->lang . '.php';
		// note: in case file does not exist, empty array will cause fallback to English
		$this->translations = (file_exists($file)) ? include($file) : array();
	}

	// translate strings to current language - for usage see description of __() function below
	public function translate($args = array()) {
		// empty in, empty out, no translation needed
		if(empty($args) || empty($args[0])) {
			return '';
		}
		// first argument passed must be text to be translated
		$text = array_shift($args);
		// check for available translation, or fallback to English
		$translation = (!empty($this->translations[$text])) ? $this->translations[$text] : $text;
		// parse in further arguments into the translated string
		return (empty($args)) ? $translation : vsprintf($translation, $args);
	}

	// check requirements
	// requirements as array('class' => array('module' => 'th23_module_name', 'min' => '0.3.0', 'max' => '0.8.0'), ...);
	// note: min and max version are optional, can check multiple classes/modules
	private function requirements() {
		$requirements = array();
		$failed = '';
		foreach($requirements as $class => $info) {
			global $$class;
			if(empty($$class->info) || $info['module'] != $$class->info['module']) {
				$failed .= '<br>* is missing ' . $class . ' class by <strong>' . $info['module'] . '</strong>';
				continue;
			}
			if(!empty($info['min']) && version_compare($$class->info['version'], $info['min'], '<')) {
				$failed .= '<br>* requires <strong>' . $info['module'] . '</strong> module with <strong>minimum version ' . $info['min'] . '</strong> (currently ' . $$class->info['version'] . ')';
			}
			if(!empty($info['max']) && version_compare($$class->info['version'], $info['max'], '>')) {
				$failed .= '<br>* requires <strong>' . $info['module'] . '</strong> module with <strong>maximum version ' . $info['max'] . '</strong> (currently ' . $$class->info['version'] . ')';
			}
		}
		if(!empty($failed)) {
			die('<strong>' . $this->info['module'] . '</strong>' . $failed);
		}
	}

}
$i18n = new i18n();

// simplify calls to translations class via globally available wrapper function
// * simple usage: __('English text')
// * parsing in variables: __('%d cups of %s', 2, 'tea')
// note: singular vs plural to be handled where used
// echo (1 == $amount) ? _s('%d file', $amount) : _s('%d files', $amount);
function __() {
	global $i18n;
	// pass on all arguments provided
	return $i18n->translate(func_get_args());
}

?>
