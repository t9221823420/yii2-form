<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 22.01.2018
 * Time: 14:34
 */

namespace yozh\form;

use kartik\select2\Select2;
use Yii;
use yii\base\DynamicModel;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\db\Query;
use yii\helpers\Html;
use yii\helpers\Json;
use yozh\base\components\helpers\ArrayHelper;
use yozh\base\components\helpers\Inflector;
use yozh\base\interfaces\models\ActiveRecordInterface;
use yozh\base\models\BaseActiveRecord;
use yozh\base\traits\ObjectTrait;
use yozh\form\ActiveField;
use yii\helpers\Url;

class ActiveForm extends \yii\bootstrap\ActiveForm
{
	use ObjectTrait;
	
	/**
	 * @var string $print
	 * @var array $refCondition
	 */
	protected static $defaultFieldParams = [
		'fields'       => [],
		'print'        => true,
	];
	
	/**
	 * @var string the default field class name when calling [[field()]] to create a new field.
	 * @see fieldConfig
	 */
	public $fieldClass = ActiveField::class;
	
	public $model;
	
	public $attributes;
	
	public $submitButton;
	
	public function fields( Model $Model, $attributes = null, $params = [] )
	{
		extract( ArrayHelper::setDefaults( $params, static::$defaultFieldParams ) );
		
		if( $fields instanceof \Closure ) {
			
			$fields = $fields( $this, $Model, $attributes, $params );
			
		}
		
		$params['fields'] = &$fields;;
		
		if( !$attributes ) {
			$attributes = array_diff( array_keys( $Model->attributes ), $Model instanceof ActiveRecord ? $Model->primaryKey( true ) : [] );
		}
		
		$result = $columns = [];
		
		if( $Model instanceof ActiveRecord ) {
			
			$Shema = Yii::$app->db->getSchema();
			
			$tableName = $Shema->getRawTableName( $Model::tableName() );
			
			$tableSchema = $Shema->getTableSchema( $tableName );
			$columns     = $tableSchema->columns;
			
		}
		
		foreach( $attributes as $attributeName ) {
			
			$output = $field = null;
			
			if( is_array( $fields ) && array_key_exists( $attributeName, $fields ) ) {
				
				$field = $fields[ $attributeName ];
				
				if( $field instanceof \Closure ) {
					$output = $field( $this, $Model, $attributes, $params );
				}
				else if( empty( $field ) ) {
					continue;
				}
				// $field can be set to true for order reason
				else if( $field !== true ) {
					$output = $field;
				}
				
			}
			
			else if( $columns[ $attributeName ] ?? false ) {
				
				$column = $columns[ $attributeName ];
				
				$field = $this->field( $Model, $attributeName );
				
				//if( preg_match_all('/(?<type>[a-z]+(?=(?:\(|$)))|(?<size>\d+)|\'(?<values>\w+)\'/', $column->dbType, $matches) ){
				if( preg_match( '/(?<type>[a-z]+)[\(]{0,}(?<size>\d*)/', $column->dbType, $matches ) ) {
					
					if( !$Model->isNewRecord && $Model instanceof BaseActiveRecord && $Model->isReadOnlyAttribute( $attributeName ) ) {
						// $output = 'foo<br />'; вывод статичных аттрибутов
					}
					
					//else if( isset( $attributeReferences[ $attributeName ] ) ) {
					else if( $Model instanceof ActiveRecordInterface
						&& $references = $Model->getAttributeReferences( $attributeName )
					) {
						
						foreach( $references as $refName => $Reference ) {
							
							$refModelClass = $Reference->refModelClass;
							
							$label = preg_replace( '/\sId$/', '', Html::encode( $Model->getAttributeLabel( $attributeName ) ) );
							
							if( $refModelClass && $refRoute = $refModelClass::getRoute( 'create' ) ) {
								$addon = [
									'append' => [
										'content'  => Html::a( '<i class="glyphicon glyphicon-plus"></i>', Url::to( [ '/' . $refRoute ] ), [
											'class' => 'btn btn-success',
											'title' => Yii::t( 'app', 'Add ' . $label ),
											//'data-toggle' => 'tooltip',
										] ),
										'asButton' => true,
									],
								];
							}
							else {
								$addon = false;
							}
							
							$refItems = $Model->getAttributeReferenceItems( $attributeName, $refName );
							
							$output = $field->widget( Select2::class, [
								'data'          => $refItems,
								'options'       => [
									'prompt' => Yii::t( 'app', 'Select ' . $label ),
								],
								'pluginOptions' => [
									'allowClear' => true,
								],
								'addon'         => $addon,
							] )->label( Yii::t( 'app', $label ) )
							;
							
						}
						
					}
					
					// for example - Dynamic properties
					else if( $Model->$attributeName instanceof Model ) {
						
						$subModel = $Model->$attributeName;
						
						$output = $this->fields( $subModel,
							$subModel instanceof \yozh\form\traits\AttributeActionListTrait
								? $subModel->attributesEditList()
								: $subModel->attributes()
							, $params
						);
						
					}
					
					else if( $matches['type'] == 'tinyint' && $matches['size'] == 1 ) { //boolean
						
						if( $Model->isNewRecord && $column->defaultValue ) {
							$Model->$attributeName = $column->defaultValue;
						}
						
						$output = $field->checkbox( [], false );
					}
					
					else if( $matches['type'] == 'enum' ) {
						$output = $field->dropDownList( array_combine( $column->enumValues, $column->enumValues ), [
							'prompt' => Yii::t( 'app', 'Select ' . Inflector::titleize( $attributeName ) ),
						] );
					}
					
					else if( ( $matches['type'] == 'varchar' && $matches['size'] > 256 )
						|| $matches['type'] == 'text'
					) {
						$output = $field->textarea( [ 'rows' => 3 ] );
					}
					
					else if( $matches['type'] == 'json' ) {
						
						if( is_array( $Model->$attributeName ) ) {
							$value = Json::encode( $Model->$attributeName );
						}
						else {
							$value = $Model->$attributeName;
						}
						
						$output = $field->textarea( [
							'rows'  => 3,
							'value' => $value,
						] );
					}
					
					else {
						$output = $field->textInput( [ 'maxlength' => true ] );
					}
					
				}
				
			}
			else if( in_array( $attributeName, $Model->safeAttributes() ) ) {
				
				$field = $this->field( $Model, $attributeName );
				
				$value = is_array( $Model->$attributeName )
					? Json::encode( $Model->$attributeName )
					: $Model->$attributeName;
				
				//$output = $field->textInput( [ 'maxlength' => true ] );
				$output = $field->textarea( [
					'rows'  => 3,
					'value' => $value,
				] );
				
			}
			
			if( $output ) {
				
				$result[ $attributeName ] = $output;
				
				if( $print ) {
					
					if( is_string( $output ) || is_numeric( $output ) || $output instanceof \yii\widgets\ActiveField ) {
						print $output;
					}
					
					else if( is_array( $output ) ) {
						
						foreach( $output as $item ) {
							
							if( $output instanceof \yii\widgets\ActiveField ) {
								print $output;
							}
						}
					}
					
				}
			}
			
		}
		
		return $result;
		
	}
	
	public function run()
	{
		
		if( $this->model instanceof Model ) {
			
			$Model = $this->model;
			
			$this->fields( $Model,
				$Model instanceof \yozh\form\interfaces\AttributeActionListInterface
					? $Model->attributesEditList()
					: $Model->attributes()
			);
			
		}
		
		print $this->_renderSubmitButton();
		
		return parent::run();
	}
	
	public function init()
	{
		
		if( !isset( $this->fieldConfig['class'] ) && $this->fieldClass ) {
			$this->fieldConfig['class'] = $this->fieldClass;
		}
		
		parent::init(); // TODO: Change the autogenerated stub
		
	}
	
	public function field( $model, $attribute, $options = [] ): ActiveField
	{
		return parent::field( $model, $attribute, $options ); // TODO: Change the autogenerated stub
	}
	
	public function group( $content, $label = null, $options = [] )
	{
		if( is_array( $content ) ) {
			$content = implode( '', $content );
		}
		
		return '<div class="form-group">'
			. ( $label ? Html::label( $label ) : '' )
			. '<div class="panel panel-default"><div class="panel-body">'
			. $content
			. '</div></div></div>';
	}
	
	protected function _renderSubmitButton()
	{
		if( $submitButton = $this->submitButton ) {
			
			if( $submitButton === true ) {
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