<?php
class Footer extends CWidget {
	public $categories;
	public $dontGoBack = false;
	public $showBanner = true;
	public function init() {
		
	}
	public function run() {
		$this->render("Footer", array('categories' => $this->categories, 'dontGoBack' => $this->dontGoBack, 'showBanner' => $this->showBanner));
	}
}
