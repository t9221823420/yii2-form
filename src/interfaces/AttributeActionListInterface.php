<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 10:05
 */

namespace yozh\form\interfaces;

interface AttributeActionListInterface
{
	public function attributeLabels();
	
	public function attributeIndexList();
	
	public function attributeEditList();
	
	public function attributeViewList();
	
	public function attributeCreateList();
	
	public function attributeUpdateList();
	
}