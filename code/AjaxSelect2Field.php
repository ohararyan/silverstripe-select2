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

	protected $sourceObject, $keyField;

	/**
	 * @param string $name
	 * @param string $title
	 * @param null|DataList $source
	 * @param null|DataList $value
	 */
	public function __construct($name, $title = '', $source = array(), $value = '') {

		$this->sourceObject = $source;
		$this->keyField     = $value;

		parent::__construct($name, $title, $source, $value);
	}

	public function Field($properties = array()){
		Requirements::javascript(THIRDPARTY_DIR . '/jquery/jquery.js');
		Requirements::javascript(THIRDPARTY_DIR . '/jquery-entwine/dist/jquery.entwine-dist.js');

		Requirements::javascript(SELECT2_MODULE . "/select2/select2.js");
		Requirements::javascript(SELECT2_MODULE . '/javascript/ajaxselect2.init.js');

		Requirements::css(SELECT2_MODULE . "/select2/select2.min.css");
		Requirements::css(SELECT2_MODULE . "/css/ajaxSelect2.css");

		var_dump($this->value);
		var_dump($this->Value());


		// $record = $this->Value() ? $this->objectForKey($this->Value()) : null;
		// var_dump($record);

		$properties = array_merge($properties, array(
			'Options' => $this->getOptions()
		));

		// return parent::Field($properties);

		return $this
				->customise($properties)
				->renderWith(array("templates/AjaxSelect2Field"));
	}

	protected function getOptions() {
		$options = array();
		$source = $this->getSource();

		if(is_object($source)) {
			$options[] = new ArrayData(array(
				'Value' => '1',
				'Title' => 'Hello'
			));
		}

		// if($source) {

			// var_dump($source);

		// 	foreach($source as $value => $title) {
		// 		$selected = false;

		// 		var_dump($value);

		// 		$options[] = new ArrayData(array(
		// 			));
		// 	}
		// }

		// return $options;

		// $dataClass = $source->dataClass();

		$values = $this->Value();


		var_dump($options);
		die();
		return $options;

		// // var_dump($source);
		// // var_dump($values);

		// if(!$values) {
		// 	return $options;
		// }



	}


	/**
	 * Get the object where the $keyField is equal to a certain value
	 *
	 * @param string|int $key
	 * @return DataObject
	 */
	protected function objectForKey($key) {
		if($this->keyField == 'ID') {
			return DataObject::get_by_id($this->sourceObject, $key);
		} else {
			return DataObject::get_one($this->sourceObject, "\"{$this->keyField}\" = '".Convert::raw2sql($key)."'");
		}
	}

	public function search($request){
		$list = DataList::create($this->getConfig('classToSearch'));

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
				'resultsContent' => html_entity_decode(SSViewer::fromString($this->getConfig('resultsFormat'))->process($object)),
				'selectionContent' => SSViewer::fromString($this->getConfig('selectionFormat'))->process($object)
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



		// var_dump($this->ID());
		if($this->Value() && $object = DataObject::get($this->getConfig('classToSearch'))->byID($this->Value())){
			$originalSourceFileComments = Config::inst()->get('SSViewer', 'source_file_comments');
			Config::inst()->update('SSViewer', 'source_file_comments', false);
			$attributes['data-selectioncontent'] = html_entity_decode(SSViewer::fromString($this->getConfig('selectionFormat'))->process($object));
			Config::inst()->update('SSViewer', 'source_file_comments', $originalSourceFileComments);
		}

		return $attributes;
	}
}