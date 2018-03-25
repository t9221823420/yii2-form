<?php

namespace yozh\form;

class AssetsBundle extends \yozh\base\AssetBundle
{

    public $sourcePath = __DIR__ .'/../assets/';

    public $css = [
        //'css/yozh-form.css'
	    // ['css/yozh-form.print.css', 'media' => 'print'],
    ];
	
    public $js = [
        //'js/yozh-form.js'
    ];
	
	public $publishOptions = [
		//'forceCopy'       => true,
	];
	
}