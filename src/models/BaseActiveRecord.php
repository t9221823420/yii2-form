<?php

namespace yozh\form\models;

use yozh\base\models\BaseActiveRecord as ActiveRecord;
use yozh\form\interfaces\AttributeActionListInterface;
use yozh\form\interfaces\AttributesFilterInterface;
use yozh\form\traits\AttributeActionListTrait;
use yozh\form\traits\AttributesFilterTrait;

abstract class BaseActiveRecord extends ActiveRecord implements AttributeActionListInterface, AttributesFilterInterface
{
	use AttributeActionListTrait, AttributesFilterTrait;
	
	const SCENARIO_FILTER = 'filter';
	
	public function attributesFilter( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesIndexList( $only, $except, $schemaOnly );
	}
	
}