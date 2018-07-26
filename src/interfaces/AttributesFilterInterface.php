<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 10:05
 */

namespace yozh\form\interfaces;

interface AttributesFilterInterface
{
	
	public function attributesFilter( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
}