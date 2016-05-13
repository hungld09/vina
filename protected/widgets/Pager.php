<?php
class Pager extends CWidget {
	public $pager;
	public $baseUrl;
	public $delimiter = '/';
	public function init() {

	}
	public function run() {
		$this->render("Pager", array(
			'pager' => $this->pager,
			'requestUrl' => $this->baseUrl,
			'delimiter' => $this->delimiter,
		));
	}
}