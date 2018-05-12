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
			$attributes = array_diff( array_keys( $model->attributes ), $model->primaryKey( true ) );
		}
		
		$tableName = Inflector::resolveTableName( $model::tableName() );
		
		$tableSchema = Yii::$app->db->getSchema()->getTableSchema( $tableName );
		$columns     = $tableSchema->columns;
		
		$result = [];
		
		foreach( $attributes as $attributeName ) {
			
			$output = $field = null;
			
			if( $columns[ $attributeName ] ?? false ) {
				
				$column = $columns[ $attributeName ];
				
				$field = $this->field( $model, $attributeName );
				
				//if( preg_match_all('/(?<type>[a-z]+(?=(?:\(|$)))|(?<size>\d+)|\'(?<values>\w+)\'/', $column->dbType, $matches) ){
				if( preg_match( '/(?<type>[a-z]+)[\(]{0,}(?<size>\d*)/', $column->dbType, $matches ) ) {
					
					if( $matches['type'] == 'tinyint' && $matches['size'] == 1 ) { //boolean
						
						if( $model->isNewRecord && $column->defaultValue ) {
							$model->$attributeName = $column->defaultValue;
						}
						
						$output = $field->checkbox( [], false );
					}
					
					else if( $matches['type'] == 'enum' ) {
						$output = $field->dropDownList( array_combine( $column->enumValues, $column->enumValues ),
							[
								//'class'      => 'form-control',
								//'prompt'     => Yii::t( 'app', 'Select country' ),
							] );
					}
					
					else if( ( $matches['type'] == 'varchar' && $matches['size'] > 256 ) || $matches['type'] == 'text' ) {
						$output = $field->textarea( [ 'rows' => 3 ] );
					}
					
					else {
						$output = $field->textInput( [ 'maxlength' => true ] );
					}
					
					if( $print ) {
						print $output;
					}
					
				}
				
			}
			else if( in_array( $attributeName, $model->safeAttributes() ) ) {
				
				$field  = $this->field( $model, $attributeName );
				$output = $field->textInput( [ 'maxlength' => true ] );
				
			}
			
			if( $output ) {
				
				$result[ $attributeName ] = $output;
				
				if( $print ) {
					print $output;
				}
				
			}
			
		}
		
		return $result;
		
	}
	
}