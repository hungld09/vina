<?php
class AssetItem extends CWidget {
	public $asset;
	public $posterUrl;
	public $isFree = false;
	public function init() {
		
	}
	
	public function run() {
		$this->render("AssetItem", array('asset' => $this->asset, 'posterUrl' => $this->posterUrl, 'isFree' => $this->isFree));
	}
}