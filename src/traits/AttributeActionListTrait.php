<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 9:27
 */

namespace yozh\form\traits;

trait AttributeActionListTrait
{
	
	/**
	 * @inheritdoc
	 */
	public function attributeLabels()
	{
		return [];
	}
	
	public function attributeIndexList()
	{
		/*
		return [
			'title' => 'title',
		];
		*/
		
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
		return $this->attributeEditList();
	}
	
	public function attributeUpdateList()
	{
		return $this->attributeEditList();
	}
	
	protected function _attributeList()
	{
		$attributes = array_diff( array_keys( $this->attributes ), array_merge( $this->primaryKey( true ),  [
				'created_at',
				'updated_at',
				'deleted_at',
			])  );
		
		return array_combine( $attributes, $attributes);
	}
}