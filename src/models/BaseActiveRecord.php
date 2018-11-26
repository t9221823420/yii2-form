<?php

namespace yozh\form\models;

use yozh\base\models\BaseActiveRecord as ActiveRecord;
use yozh\form\interfaces\AttributeActionListInterface;
use yozh\form\interfaces\DefaultFiltersInterface;
use yozh\form\traits\AttributeActionListTrait;
use yozh\form\traits\DefaultFiltersTrait;

abstract class BaseActiveRecord extends ActiveRecord implements AttributeActionListInterface, DefaultFiltersInterface
{
	use AttributeActionListTrait, DefaultFiltersTrait;
	
	const SCENARIO_FILTER = 'filter';
	
	public function defaultFiltersList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesIndexList( $only, $except, $schemaOnly );
	}
	
}