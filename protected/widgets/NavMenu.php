<?php
class NavMenu extends CWidget {
	public $userName;
        public function init() {
		
	}
	
	public function run() {
		$this->render("NavMenu", array('userName' => $this->userName,));
	}
}