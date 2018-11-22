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
			'print'        => true,
			'refItems'     => null,
			'refQuery'     => null,
			'refCondition' => [],
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
		
		$attributeReferences = [];
		if( $Model instanceof ActiveRecordInterface ) {
			foreach( $Model->getShemaReferences() as $refName => $reference ) {
				foreach( $reference as $fk => $pk ) {
					$attributeReferences[ $fk ][ $refName ] = $reference;
				}
			}
			
			$fk = $pk = null;
		}
		
		if( !$attributes ) {
			$attributes = array_diff( array_keys( $Model->attributes ), $Model instanceof ActiveRecord ? $Model->primaryKey( true ) : [] );
		}
		
		if( $this->attributes ) {
			$attributes = array_intersect( $attributes, (array)$this->attributes );
		}
		
		$result = $columns = [];
		
		if ( $Model instanceof ActiveRecord ){

			$Shema = Yii::$app->db->getSchema();
			
			$tableName = $Shema->getRawTableName( $Model::tableName() );
			
			$tableSchema = $Shema->getTableSchema( $tableName );
			$columns     = $tableSchema->columns;
		
		}
		
		foreach( $attributes as $attributeName ) {
			
			$output = $field = null;
			
			if( $columns[ $attributeName ] ?? false ) {
				
				$column = $columns[ $attributeName ];
				
				$field = $this->field( $Model, $attributeName );
				
				//if( preg_match_all('/(?<type>[a-z]+(?=(?:\(|$)))|(?<size>\d+)|\'(?<values>\w+)\'/', $column->dbType, $matches) ){
				if( preg_match( '/(?<type>[a-z]+)[\(]{0,}(?<size>\d*)/', $column->dbType, $matches ) ) {
					
					if( !$Model->isNewRecord && $Model instanceof BaseActiveRecord && $Model->isReadOnlyAttribute( $attributeName ) ) {
						// $output .= 'foo<br />'; вывод статичных аттрибутов
					}
					
					else if( isset( $attributeReferences[ $attributeName ] ) ) {
						
						foreach( $attributeReferences[ $attributeName ] as $refName => $reference ) {
							
							$refAttributes = $Shema->getTableSchema( $reference[0] )->columns;
							
							if( isset( $refAttributes['name'] ) ) {
								$refLabel = 'name';
							}
							else if( isset( $refAttributes['title'] ) ) {
								$refLabel = 'title';
							}
							else {
								$refLabel = $reference[ $attributeName ];
							}
							
							/*
							 * @todo доделать, когда будет полностью реализован Relations
							 * на текущий момент определяется по связям в БД
							 * нет возможности определить какой класс отвечает за getList
							 */
							if( 0 && ( new ReflectionClass( 'TheClass' ) )->implementsInterface( ActiveRecordInterface ) ) {
								
								$refItems = 1;
								
							}
							else{

								$refQuery = ( new Query() )
									->select( [ $refLabel, $reference[ $attributeName ] ] )
									->from( $reference[0] )
									->andFilterWhere( $refCondition )
								;
								
								$refItems = $refQuery->indexBy( $reference[ $attributeName ] )->column();
								
							}
							
							
							$label = preg_replace( '/\sId$/', '', Html::encode( $Model->getAttributeLabel( $attributeName ) ) );
							
							/*
							$output .= $field->dropDownList( $refItems, [
								'prompt' => Yii::t( 'app', 'Select item' ),
							] )
							;
							*/
							
							$relationGetter = 'get' . Inflector::camelize( preg_replace( '/_id$/', '', $attributeName ) );
							
							if( method_exists( $Model, $relationGetter )
								&& $activeQuery = $Model->$relationGetter()
							) {
								$refModelClass = $activeQuery->modelClass;
							}
							else {
								$refModelClass = null;
							}
							
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
							
							$output .= $field->widget( Select2::class, [
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
					
					else if( $matches['type'] == 'tinyint' && $matches['size'] == 1 ) { //boolean
						
						if( $Model->isNewRecord && $column->defaultValue ) {
							$Model->$attributeName = $column->defaultValue;
						}
						
						$output .= $field->checkbox( [], false );
					}
					
					else if( $matches['type'] == 'enum' ) {
						$output .= $field->dropDownList( array_combine( $column->enumValues, $column->enumValues ), [
							'prompt' => Yii::t( 'app', 'Select ' . Inflector::titleize( $attributeName ) ),
						] );
					}
					
					else if( ( $matches['type'] == 'varchar' && $matches['size'] > 256 )
						|| $matches['type'] == 'text'
					) {
						$output .= $field->textarea( [ 'rows' => 3 ] );
					}
					
					else if( $matches['type'] == 'json' ) {
						
						if( is_array( $Model->$attributeName ) ) {
							$value = Json::encode( $Model->$attributeName );
						}
						else {
							$value = $Model->$attributeName;
						}
						
						$output .= $field->textarea( [
							'rows'  => 3,
							'value' => $value,
						] );
					}
					
					else {
						$output .= $field->textInput( [ 'maxlength' => true ] );
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
					? $Model->attributesEditList()
					: array_keys( $Model->attributes )
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
	
	public function field( $model, $attribute, $options = [] ): ActiveField
	{
		return parent::field( $model, $attribute, $options ); // TODO: Change the autogenerated stub
	}
	
	
}