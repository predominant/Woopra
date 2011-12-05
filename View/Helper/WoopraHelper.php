<?php

App::uses('HtmlHelper', 'View/Helper');

/**
 * This Woopra Helper automatically includes the snippet for woopra tracking
 *
 * More information about customisation is here: http://www.woopra.com/docs/async/
 *
 * @package Woopra
 * @subpackage Woopra.View.Helper
 * @author Graham Weldon (http://grahamweldon.com)
 * @copyright 2011, Graham Weldon (http://grahamweldon.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
class WoopraHelper extends HtmlHelper {

/**
 * Default settings
 *
 * - domain (required): Domain name
 * - timeout (optional): Timeout in minutes (for users). Default 5
 * - query (optional): Include query data. Default false.
 *
 * @var array
 */
	protected $_settings = array(
		'timeout' => 5,
		'query' => false,
		'forceDomain' => false,
	);

/**
 * Default Constructor
 *
 * @param View $View The View this helper is being attached to.
 * @param array $settings Configuration settings for the helper.
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		if (!isset($settings['domain'])) {
			throw new ConfigureException('The "domain" value must be set on the Woopra helper');
		}
		$this->_settings = array_merge($this->_settings, $settings);
	}

/**
 * After layout callback.  afterLayout is called after the layout has rendered.
 *
 * Used to inject the snippet for Woopra
 *
 * @param string $layoutFile The layout file that was rendered.
 * @return void
 */
	public function afterLayout($layoutFile) {
		$this->_View->output = str_replace('</body>', $this->_snippet() . '</body>', $this->_View->output);
	}

/**
 * Generate Woopra Snippet
 *
 * @return string
 */
	protected function _snippet() {
		extract($this->_settings);
		$timeout *= 60 * 1000;
		$trackMethod = $this->_trackMethod();
		$visitor = $this->_visitor();
		$settings = $this->_settings();
		$script = <<<ENDSCRIPT
function woopraReady(tracker) {
    tracker.setDomain('{$domain}');
    tracker.setIdleTimeout({$timeout});
    tracker.{$trackMethod};
    return false;
}
(function() {
	{$visitor}
	{$settings}
    var wsc = document.createElement('script');
    wsc.src = document.location.protocol+'//static.woopra.com/js/woopra.js';
    wsc.type = 'text/javascript';
    wsc.async = true;
    var ssc = document.getElementsByTagName('script')[0];
    ssc.parentNode.insertBefore(wsc, ssc);
})();
ENDSCRIPT;
		return $this->scriptBlock($script);
	}

/**
 * Return the tracking method call based on the settings provided.
 *
 * @return string
 */
	protected function _trackMethod() {
		if ($this->_settings['query']) {
			return "trackPageview({type:'pageview',url:window.location.pathname+window.location.search,title:document.title})";
		}
		return "track()";
	}

/**
 * Generate visitor information variable
 *
 * @return string
 */
	protected function _visitor() {
		return '';
		
		// @TODO: This should allow the user to define callback functions or raw data to include in the woo_visitor variable.
		
		if (!isset($this->_settings['visitor'])) {
			return '';
		}
		
		$result = array();
		foreach ($this->_settings as $key => $value) {
			$result[$key] = $value;
		}

		return sprintf('var woo_visitor = %s;', json_encode($result));
	}

/**
 * Generate Woopra snippet settings
 *
 * @return void
 */
	protected function _settings() {
		$settings = array();

		if ($this->_settings['forceDomain']) {
			$settings['domain'] = $this->_settings['domain'];
		}
		
		if (empty($settings)) {
			return '';
		}

		return sprintf('var woo_settings = %s;', json_encode($settings));
	}
}
