<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 9:27
 */

namespace yozh\form\traits;

use yozh\base\interfaces\models\ActiveRecordInterface;

trait AttributeActionListTrait
{
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return [];
	}
	
	public function attributesIndexList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesDefaultList( $only, $except, $schemaOnly );
	}
	
	public function attributesEditList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesDefaultList( $only, $except, $schemaOnly );
	}
	
	public function attributesViewList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesDefaultList( $only, $except, $schemaOnly );
	}
	
	public function attributesCreateList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesEditList( $only, $except, $schemaOnly );
	}
	
	public function attributesUpdateList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return $this->attributesEditList( $only, $except, $schemaOnly );
	}
	
	protected function _defaultExceptAttributes(){
		return array_merge( $this->primaryKey( true ), [
			'created_at',
			'updated_at',
			'deleted_at',
			'filter_search',
		] );
	}
	
	public function attributesDefaultList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		if( is_null($except)  ){
			$except = $this->_defaultExceptAttributes();
		}
		
		if( $this instanceof ActiveRecordInterface ) {
			$names = $this->attributes( $only, $except, $schemaOnly );
		}
		else {
			
			$names = $this->attributes();
			
			if( $only ) {
				$names = array_intersect( $names, $only );
			}
			
			if( $except ) {
				$names = array_diff( $names, $except );
			}
			
		}
		
		return array_combine( $names, $names );
	}
}