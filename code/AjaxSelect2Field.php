<?php

/**
 * A dropdown field with ajax loaded options list, uses jquery select2
 * http://ivaynberg.github.io/select2/
 * @author shea@livesource.co.nz
 **/
class AjaxSelect2Field extends DropdownField{

	private static $allowed_actions = array('search');

	protected $config = array(
		'classToSearch' 		=> 'SiteTree',
		'searchFields' 			=> array('Title'),
		'resultsLimit' 			=> 200,
		'minimumInputLength' 	=> 2,
		'resultsFormat' 		=> '$Title',
		'selectionFormat' 		=> '$Title',
		'placeholder'			=> 'Search...',
		'excludes'				=> array(),
		'filter'				=> array(),
		'multiple'				=> false,
	);

	/**
	 * @param string $name
	 * @param string $title
	 * @param null|DataList $source
	 * @param null|DataList $value
	 */
	public function __construct($name, $title = '', $source = array(), $value = '') {

		parent::__construct($name, $title, $source, $value);
	}

	public function Field($properties = array()){
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');

		Requirements::javascript(SELECT2_MODULE . "/select2/select2.js");
		Requirements::javascript(SELECT2_MODULE . '/javascript/ajaxselect2.init.js');

		Requirements::css(SELECT2_MODULE . "/select2/select2.min.css");
		Requirements::css(SELECT2_MODULE . "/css/ajaxSelect2.css");

		// var_dump($this->Value());

		$properties = array_merge($properties, array(
			'Options' => $this->getOptions()
		));

		return $this
				->customise($properties)
				->renderWith(array("templates/AjaxSelect2Field"));
	}

	protected function getOptions() {
		$options = ArrayList::create();
		$source = $this->getSource();

		if(!$source) {
			$source = new ArrayList();
		}

		if(is_object($source)) {
			$selected = false;
			if ($this->value === '') {
				$selected = false;
			} else {
				foreach($source as $value => $title) {
					$selected = ($value == $this->value);
				}
			}
		} else {
			$object = DataObject::get($this->source)->byID($this->Value());
			if ($object) {
				$options->push(
					ArrayData::create(array(
						'Title' => $object->Title,
						'Value' => $object->ID,
						'Selected' => true
					))
				);
			}

		}

		$values = $this->Value();

		if(!$values) {
			$options->push(
				ArrayData::create(array(
					'Value' => '',
					'Title' => '',
					'Selected' => true
				))
			);
		}

		return $options;
	}

	public function search($request){
		// $list = $this->getSource();
		$list = DataObject::get($this->getSource());

		$params = array();
		$searchFields = $this->getConfig('searchFields');
		foreach($searchFields as $searchField) {
			$name = (strpos($searchField, ':') !== FALSE) ? $searchField : "$searchField:partialMatch";
			$params[$name] = $request->getVar('term');
		}
		$start = (int)$request->getVar('id') ? (int)$request->getVar('id') * $this->getConfig('resultsLimit') : 0;
		$list = $list->filterAny($params)->exclude($this->getConfig('excludes'));
		$filter = $this->getConfig('filter');
		if (count($filter) > 0) {
			$list->filter($filter);
		}
		$total = $list->count();
		$results = $list->sort(strtok($searchFields[0], ':'), 'ASC')->limit($this->getConfig('resultsLimit'), $start);

		$return = array(
			'list' => array(),
			'total' => $total
		);

		$originalSourceFileComments = Config::inst()->get('SSViewer', 'source_file_comments');
		Config::inst()->update('SSViewer', 'source_file_comments', false);
		foreach($results as $object) {
			$return['list'][] = array(
				'id' => $object->ID,
				'templateResult' => html_entity_decode(SSViewer::fromString($this->getConfig('resultsFormat'))->process($object)),
				'templateSelection' => SSViewer::fromString($this->getConfig('selectionFormat'))->process($object)
			);
		}
		Config::inst()->update('SSViewer', 'source_file_comments', $originalSourceFileComments);
		return Convert::array2json($return);
	}


	public function setConfig($k, $v){
		$this->config[$k] = $v;
		return $this;
	}


	public function getConfig($k){
		return isset($this->config[$k]) ? $this->config[$k] : null;
	}


	/**
	 * @return Array
	 */
	public function getAttributes() {
		$attributes = array_merge(
			parent::getAttributes(),
			array(
				'data-searchurl' => $this->Link('search'),
				'data-minimuminputlength' => $this->getConfig('minimumInputLength'),
				'data-resultslimit' => $this->getConfig('resultsLimit'),
				'data-placeholder' => $this->getConfig('placeholder'),
				'data-multiple'		=> $this->getConfig('multiple')
			)
		);


		// var_dump($this->Value());
		// var_dump($this->source);


		if ($this->Value() && $object = DataObject::get($this->source)->byID($this->Value())){
			// var_dump($object->Title);
			$originalSourceFileComments = Config::inst()->get('SSViewer', 'source_file_comments');
			Config::inst()->update('SSViewer', 'source_file_comments', false);
			$attributes['data-selectioncontent'] = html_entity_decode(SSViewer::fromString($this->getConfig('selectionFormat'))->process($object));
			Config::inst()->update('SSViewer', 'source_file_comments', $originalSourceFileComments);
			return $attributes;
		}

		return $attributes;
	}

}