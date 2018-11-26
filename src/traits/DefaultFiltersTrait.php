<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 9:27
 */

namespace yozh\form\traits;

trait DefaultFiltersTrait
{
	/**
	 * @param array|null $only
	 * @param array|null $except
	 * @param bool|null $schemaOnly
	 * @return array of filters which add andWhereConditions
	 *
	 */
	public function defaultFiltersList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return [];
	}
	
	
	/**
	 * @param $query
	 * @param null $tableName
	 * @param array|null $only
	 * @param array|null $except
	 * @param bool|null $schemaOnly
	 *
	 * add andWhere conditions for all of $Model's attributes OR defined by defaultFiltersList
	 */
	protected function _addDefaultFiltersConditions( $query, $tableName = null, ?array $only = null, ?array $except = null, ?bool $schemaOnly = true )
	{
		$tableName = $tableName ?? $query->getRawTableName();
		
		$attributes = $this->defaultFiltersList( $only, $except, $schemaOnly );
		
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