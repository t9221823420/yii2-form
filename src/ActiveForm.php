<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 22.01.2018
 * Time: 14:34
 */

namespace yozh\form;

use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\helpers\Html;
use yozh\base\components\ArrayHelper;
use yozh\base\components\Inflector;

class ActiveForm extends \yii\widgets\ActiveForm
{
	
	public $model;
	
	public $attributes;
	
	public $submitButton;
	
	public function fields( Model $Model, $attributes = null, $print = true )
	{
		
		if( !$attributes ) {
			$attributes = array_diff( array_keys( $Model->attributes ), $Model->primaryKey( true ) );
		}
		
		if( $this->attributes ) {
			$attributes = array_intersect( $attributes, (array)$this->attributes );
		}
		
		$tableName = Inflector::resolveTableName( $Model::tableName() );
		
		$tableSchema = Yii::$app->db->getSchema()->getTableSchema( $tableName );
		$columns     = $tableSchema->columns;
		
		$result = [];
		
		foreach( $attributes as $attributeName ) {
			
			$output = $field = null;
			
			if( $columns[ $attributeName ] ?? false ) {
				
				$column = $columns[ $attributeName ];
				
				$field = $this->field( $Model, $attributeName );
				
				//if( preg_match_all('/(?<type>[a-z]+(?=(?:\(|$)))|(?<size>\d+)|\'(?<values>\w+)\'/', $column->dbType, $matches) ){
				if( preg_match( '/(?<type>[a-z]+)[\(]{0,}(?<size>\d*)/', $column->dbType, $matches ) ) {
					
					if( $matches['type'] == 'tinyint' && $matches['size'] == 1 ) { //boolean
						
						if( $Model->isNewRecord && $column->defaultValue ) {
							$Model->$attributeName = $column->defaultValue;
						}
						
						$output .= $field->checkbox( [], false );
					}
					
					else if( $matches['type'] == 'enum' ) {
						$output .= $field->dropDownList( array_combine( $column->enumValues, $column->enumValues ),
							[
								//'class'      => 'form-control',
								//'prompt'     => Yii::t( 'app', 'Select country' ),
							] );
					}
					
					else if( ( $matches['type'] == 'varchar' && $matches['size'] > 256 ) || $matches['type'] == 'text' ) {
						$output .= $field->textarea( [ 'rows' => 3 ] );
					}
					
					else {
						$output .= $field->textInput( [ 'maxlength' => true ] );
					}
					
				}
				
			}
			else if( in_array( $attributeName, $Model->safeAttributes() ) ) {
				
				$field  = $this->field( $Model, $attributeName );
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
	
	public function run()
	{
		
		if( $this->model instanceof ActiveRecord ) {
			
			$Model = $this->model;
			
			$this->fields( $Model,
				$Model instanceof AttributeActionListInterface
					? $Model->attributeEditList()
					: array_keys( $Model->attributes )
			);
			
		}
		
		print $this->_renderSubmitButton();

		parent::run();
	}
	
	protected function _renderSubmitButton()
	{
		if( $submitButton = $this->submitButton ) {
			
			if( $submitButton === true ){
				$submitButton = [];
			}
			
			Html::addCssClass( $submitButton, 'btn' );
			
			$tag = ArrayHelper::remove( $submitButton, 'tag', 'button' );
			
			if( !$tag ) {
				$tag = ArrayHelper::remove( $submitButton, 'tagName', 'button' );
			}
			
			$label = ArrayHelper::remove( $submitButton, 'label', 'Submit' );
			
			if( $tag === 'button' && !isset( $submitButton['type'] ) ) {
				$submitButton['type'] = 'submit';
			}
			
			return Html::tag( $tag, $label, $submitButton );
		}
	}
	
	
}