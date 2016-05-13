<?php
class Header extends CWidget {
	public $msisdn = '';
	public $subscriber;
	public $usingServices;
	public $noAccountInfo = false;
	public function init() {
		
	}
	
	public function run() {
		$this->render("Header", array('msisdn' => $this->msisdn, 'subscriber' => $this->subscriber, 'usingServices' => $this->usingServices, 'noAccountInfo' => $this->noAccountInfo));
	}
}
