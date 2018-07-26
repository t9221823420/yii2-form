<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 9:27
 */

namespace yozh\form\traits;

trait AttributesFilterTrait
{
	
	public function attributesFilter( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return [];
	}
	
	protected function _addDefaultFilters( $query, $tableName = null, ?array $only = null, ?array $except = null, ?bool $schemaOnly = true )
	{
		$tableName = $tableName ?? $query->getRawTableName();
		
		$attributes = $this->attributesFilter( $only, $except, $schemaOnly );
		
		foreach( $attributes as $attribute ) {
			
			if ( is_array($this->$attribute ) ){
				$query->andFilterWhere( [ "$tableName.$attribute" => $this->$attribute ] );
			}
			else{
				$query->andFilterWhere( [ 'like', "$tableName.$attribute", $this->$attribute ] );
			}
			
		}
	}
	
}