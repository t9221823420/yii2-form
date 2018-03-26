<?php

namespace yozh\form\models;

use yozh\base\models\BaseModel as ActiveRecord;

abstract class BaseModel extends ActiveRecord
{
	/*
	private $_attributeActionListMethods = [
		'attributeIndexList',
		'_attributeList',
		'attributeCreateList',
		'attributeUpdateList',
		'attributeViewList',
		'attributeSearchList',
	];
	
	
	public function __call( $name, $arguments )
	{
		if( $name ){
			return call_user_func_array( $name, $arguments);
		}
		
		
	}
	*/
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return $this->_attributeList();
	}
	
	protected function _attributeList()
	{
		return array_diff( array_keys( $this->attributes ), $this->primaryKey( true ) );;
	}
	
	public function attributeIndexList()
	{
		return $this->_attributeList();
	}
	
	public function attributeEditList()
	{
		return $this->_attributeList();
	}
	
	public function attributeViewList()
	{
		return $this->_attributeList();
	}
	
	public function attributeCreateList()
	{
		return $this->_attributeList();
	}
	
	public function attributeUpdateList()
	{
		return $this->_attributeList();
	}
}