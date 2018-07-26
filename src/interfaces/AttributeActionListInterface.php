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
	public function attributeLabels( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
	public function attributesIndexList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
	public function attributesEditList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
	public function attributesViewList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
	public function attributesCreateList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
	public function attributesUpdateList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false );
	
}