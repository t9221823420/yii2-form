<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 22.01.2018
 * Time: 14:34
 */

namespace yozh\form\components;

use Yii;
use yii\db\ActiveRecord;
use yozh\base\components\Inflector;

class ActiveForm extends \yii\widgets\ActiveForm
{
	
	public function fields( ActiveRecord $model, $attributes = null, $print = true )
	{
		
		if( !$attributes ) {
			$attributes = array_diff( array_keys($model->attributes),  $model->primaryKey(true) );
		}
		
		$tableName = Inflector::resolveTableName( $model::tableName() );
		
		$tableSchema = Yii::$app->db->getSchema()->getTableSchema( $tableName );
		$columns     = $tableSchema->columns;
		
		$result = [];
		
		foreach( $attributes as $attributeName ) {
			if( isset( $columns[ $attributeName ] ) ) {
				
				$column = $columns[ $attributeName ];
				
				$field = $this->field( $model, $attributeName );
				
				//if( preg_match_all('/(?<type>[a-z]+(?=(?:\(|$)))|(?<size>\d+)|\'(?<values>\w+)\'/', $column->dbType, $matches) ){
				if( preg_match( '/(?<type>[a-z]+)[\(]{0,}(?<size>\d*)/', $column->dbType, $matches ) ) {
					
					if( $matches['type'] == 'tinyint' && $matches['size'] == 1 ) { //boolean
						
						if( $model->isNewRecord && $column->defaultValue){
							$model->$attributeName = $column->defaultValue;
						}
						
						$result[ $attributeName ] = $field->checkbox([], false);
					}
					
					else if( $matches['type'] == 'enum' ) {
						$result[ $attributeName ] = $field->dropDownList( array_combine($column->enumValues, $column->enumValues),
							[
								//'class'      => 'form-control',
								//'prompt'     => Yii::t( 'app', 'Select country' ),
							] );
					}
					
					elseif( ($matches['type'] == 'varchar' && $matches['size'] > 256 ) || $matches['type'] == 'text' ) {
						$result[ $attributeName ] = $field->textarea(['rows' => 3]);
					}
					
					else {
						$result[ $attributeName ] = $field->textInput( [ 'maxlength' => true ] );
					}
					
					if( $print ){
						print $result[ $attributeName ];
					}
					
				}
				
			}
		}
		
		return $result;
		
	}
	
}