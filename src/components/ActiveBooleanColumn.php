<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 26.03.2018
 * Time: 21:52
 */

namespace yozh\form\components;

use Closure;
use Yii;
use yii\db\ActiveRecord;
use yii\jui\Widget;
use yii\web\View;
use yozh\base\components\helpers\ArrayHelper;
use yozh\base\components\utils\Config;
use yozh\form\AssetBundle;
use yozh\form\Module;
use yii\helpers\Json;
use yii\helpers\Url;

class ActiveBooleanColumn extends \yii\grid\DataColumn
{
	public $class = 'yozh-active-boolean';
	
	public $format = 'html';
	
	public $template = '<i class="glyphicon glyphicon-{value}"></i>';
	
	public $templateValue = [ 'minus', 'ok' ];
	
	public $ownerClass = [ 'yozh-false', 'yozh-true' ];
	
	public $url;
	
	public $callback;
	
	public $confirm = true;
	
	public $data = [];
	
	public function __construct( array $config = [] )
	{
		
		$config['url'] ?? $this->url = Url::to( [ 'switch' ] );
		
		parent::__construct( $config );
		
	}
	
	
	public function renderFilterCell()
	{
		if( !$this->filter ) {
				$this->filter = [ Yii::t( 'app', 'No' ), Yii::t( 'app', 'Yes' ) ];
		}
		
		return parent::renderFilterCell(); // TODO: Change the autogenerated stub
	}
	
	
	/**
	 * @param $Model ActiveRecord
	 * @param mixed $key
	 * @param int $index
	 * @return string
	 *
	 */
	public function renderDataCell( $Model, $key, $index )
	{
		if( $this->attribute ) {
			
			$View          = Yii::$app->controller->view;
			$attribute     = $this->attribute;
			$primaryKey    = key( $Model->getPrimaryKey( true ) );
			$template      = $this->template;
			$templateValue = $this->templateValue;
			
			$params = [
				'model'  => $Model,
				'key'    => $key,
				'index'  => $index,
				'widget' => $this,
			];
			
			foreach( $templateValue as $key => $item ) {
				if( is_numeric( $key ) ) {
					$templateValue = [ '{value}' => $templateValue ];
					break;
				}
			}
			
			if( !$this->value ) {
				
				$this->value = function( $Model ) use ( $attribute, $templateValue, $template ) {
					
					$pairs = [];
					if( is_array( $templateValue ) && count( $templateValue ) ) {
						foreach( $templateValue as $key => $item ) {
							if( is_array( $item ) && count( $item ) == 2 ) {
								$pairs[ $key ] = $Model->$attribute ? $item[1] : $item[0];
							}
							else {
								throw new \yii\base\InvalidParamException( "incorrect set of templateValue" );
							}
						}
					}
					else {
						throw new \yii\base\InvalidParamException( "templateValue have to be an array" );
					}
					
					return strtr( $template, $pairs );
				};
			}
			
			$this->contentOptions = ArrayHelper::merge( [
				'class'    => '',
				'url'      => $this->url,
				'callback' => '',
			], $this->contentOptions );
			
			if( !$this->contentOptions instanceof Closure ) {
				
				if( $this->contentOptions['class'] instanceof Closure ) {
					$this->contentOptions['class'] = call_user_func_array( $this->contentOptions['class'], $params );
				}
				else if( is_string( $this->contentOptions['class'] ) ) {
					$this->contentOptions['class'] .= ' ' . $this->class;
				}
				
				$this->contentOptions['url']      = Config::setWithClosure( $this->contentOptions['url'], $this->url, $params );
				$this->contentOptions['callback'] = Config::setWithClosure( $this->contentOptions['callback'], $this->callback, $params );
				
				if( $this->data instanceof Closure ) {
					$this->data = call_user_func_array( $this->data, $params );
				}
				
				if( !is_array( $this->data ) ) { //
					throw new \yii\base\InvalidParamException( "data have to be an array" );
				}
				
				$params = ArrayHelper::merge( [
					'attribute'      => $attribute,
					'primaryKey'     => $primaryKey,
					'template'       => $template,
					'templateValue'  => $templateValue,
					'data'           => $this->data,
					'contentOptions' => $this->contentOptions,
					'ownerClass'     => $this->ownerClass,
				], $params );
				
				$this->contentOptions = function( $Model ) use ( $params ) {
					
					extract( $params );
					
					$contentOptions['class'] .= ' ' . $ownerClass[ $Model->$attribute ? 1 : 0 ];
					
					if( empty( $data ) ) {
						$contentOptions = ArrayHelper::merge( [
							'data-' . $primaryKey => $Model->primaryKey,
							'data-attribute'      => $attribute,
							'data-value'          => $Model->$attribute ? 0 : 1,
						], $contentOptions );
					}
					
					else {
						
						foreach( $data as $key => $datum ) {
							
							// [ 'attribute1', 'attribute1',  ]
							if( is_numeric( $key ) && is_string( $datum ) && isset( $Model->$datum ) ) {
								$contentOptions[ 'data-' . $datum ] = $Model->$datum;
							}
							
							// [ 'attr' => 'attribute1', 'value' => 'attribute2'  ]
							// is_string( $attribute ) only for understand scheme
							else if( is_string( $key ) && is_string( $datum ) && isset( $Model->$datum ) ) {
								$contentOptions[ 'data-' . $key ] = $Model->$datum;
							}
							
							// [ 'attr' => 'some value', 'value' => 'another value'  ]
							else if( is_string( $key ) ) {
								$contentOptions[ 'data-' . $key ] = $datum;
							}
							
						}
						
						if( !isset( $contentOptions[ 'data-' . $attribute ] ) ) {
							$contentOptions[ 'data-' . $attribute ] = $Model->$attribute;
						}
						
					}
					
					/*
					if( $this->confirm ){
						$contentOptions[ 'data-confirm' ] = Module::t( Module::MODULE_ID, 'Are you sure you want to switch this item?' );
					}
					*/
					
					return $contentOptions;
				};
			}
			
			if( empty( $this->data ) ) { //
				$value = 'value';
			}
			else {
				$value = $attribute;
			}
			
			$options = [
				'value'         => $value,
				'template'      => $template,
				'templateValue' => $templateValue,
				'ownerClass'    => $this->ownerClass,
			];
			
			$View->registerJs(
				'Yozh.Form.activeBoolean = ' . Json::htmlEncode( $options ) . ';',
				View::POS_END,
				'yozh.form'
			);
			
			AssetBundle::register( $View );
		}
		
		return parent::renderDataCell( $Model, $key, $index ); // TODO: Change the autogenerated stub
	}
	
	
}