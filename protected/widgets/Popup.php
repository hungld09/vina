<?php
class Popup extends CWidget {
	public $msisdn;
	public $usingServices;
	public function init() {
		
	}
	
	public function run() {
		$this->render("Popup", array('msisdn' => $this->msisdn, 'usingServices'=>$this->usingServices));
	}
}