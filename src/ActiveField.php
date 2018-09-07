<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 29.05.2018
 * Time: 20:36
 */

namespace yozh\form;

use kartik\date\DatePicker;
use kartik\markdown\MarkdownEditor;
use kartik\widgets\DateTimePicker;
use powerkernel\tinymce\TinyMce;
use Yii;
use yii\helpers\Html;
use yii\web\View;
use yozh\base\traits\ObjectTrait;

class ActiveField extends \yii\bootstrap\ActiveField
{
	use ObjectTrait;
	
	const INPUT_TYPE_STRING = 'string';
	const INPUT_TYPE_TEXT   = 'text';
	
	const INPUT_TYPE_INTEGER = 'integer';
	const INPUT_TYPE_DECIMAL = 'decimal';
	
	const INPUT_TYPE_DATE     = 'date';
	const INPUT_TYPE_TIME     = 'time';
	const INPUT_TYPE_DATETIME = 'datetime';
	
	const INPUT_TYPE_BOOLEAN = 'boolean';
	const INPUT_TYPE_LIST    = 'list';
	
	const INPUT_TYPE_HASH = 'hash';
	const INPUT_TYPE_JSON = 'json';
	
	const WIDGET_TYPE_TEXT       = 'text';
	const WIDGET_TYPE_TEXTAREA   = 'textarea';
	const WIDGET_TYPE_TEXTEDITOR = 'texteditor';
	const WIDGET_TYPE_MARKUP     = 'markup';
	const WIDGET_TYPE_PASSWORD   = 'password';
	
	const WIDGET_TYPE_DATE     = 'date';
	const WIDGET_TYPE_TIME     = 'time';
	const WIDGET_TYPE_DATETIME = 'datetime';
	
	const WIDGET_TYPE_SWITCH   = 'switch';
	const WIDGET_TYPE_RADIO    = 'radio';
	const WIDGET_TYPE_CHECKBOX = 'checkbox';
	const WIDGET_TYPE_SELECT   = 'select';
	const WIDGET_TYPE_DROPDOWN = 'dropdown';
	
	const DEFAULT_INPUT_TYPE = self::INPUT_TYPE_STRING;
	
	const DEFAULT_WIDGET_TYPE = self::WIDGET_TYPE_TEXT;
	
	protected static $_inputsConfig;
	
	public $inputType;
	
	public $widgetType;
	
	static public function getWidgets( $inputType = null )
	{
		$inputType = $inputType ?? static::DEFAULT_INPUT_TYPE;
		
		$inputs = static::getInputs();
		
		if( isset( $inputs[ $inputType ]['widgets'] ) ) {
			return $inputs[ $inputType ]['widgets'];
		}
		
		throw new \yii\base\InvalidParamException( "Widgets for inputType '$inputType' not found." );
	}
	
	static public function getDefaultWidget( $inputType = null )
	{
		$widgets = static::getWidgets( $inputType );
		
		if( isset( $widgets[ static::DEFAULT_WIDGET_TYPE ] ) ) {
			return $widgets[ static::DEFAULT_WIDGET_TYPE ];
		}
		
		throw new \yii\base\InvalidParamException( "Default widget for inputType '$inputType' not found." );
	}
	
	public static function getInputs()
	{
		
		if( !static::$_inputsConfig ) {
			static::$_inputsConfig = static::_initConfig();
		}
		
		return static::$_inputsConfig;
	}
	
	public static function getLabel( $name )
	{
		//return preg_replace( '/^(type_|widget_)/', '', $name );
		return $name;
	}
	
	protected static function _initConfig()
	{
		$config = array_merge( array_flip( static::getConstants( 'INPUT_TYPE' ) ), [
			
			/*
			static::INPUT_TYPE_STRING => [
				'widgets' => [
					static::WIDGET_TYPE_TEXT => [
						'rules'  => [],
						'config' => [],
					],
				],
			],
			*/
			
			static::INPUT_TYPE_TEXT => [
				'widgets' => [
					static::WIDGET_TYPE_TEXTAREA,
					static::WIDGET_TYPE_TEXTEDITOR,
					static::WIDGET_TYPE_MARKUP,
				],
			],
			
			static::INPUT_TYPE_DATETIME => [
				'widgets' => [
					static::WIDGET_TYPE_DATETIME,
					static::WIDGET_TYPE_DATE,
					static::WIDGET_TYPE_TIME,
				],
			],
			
			/*
			static::INPUT_TYPE_BOOLEAN => [
				'widgets' => [
					static::WIDGET_TYPE_SWITCH,
					static::WIDGET_TYPE_RADIO,
				],
			],
			*/
		
		] );
		
		$inputResult = [];
		
		$widgets = static::getConstants( 'WIDGET_TYPE' );
		
		foreach( $config as $inputType => $inputConfig ) {
			
			if( !is_array( $inputConfig ) ) {
				
				$inputType = $inputType;
				
				if( in_array( $inputConfig, $widgets ) ) {
					$widget = $inputConfig;
				}
				else {
					$widget = static::DEFAULT_WIDGET_TYPE;
				}
				
				$inputConfig = [
					'widgets' => [
						$widget,
					],
				];
			}
			
			if( !isset( $inputConfig['name'] ) ) { //
				$inputConfig['name'] = $inputType;
			}
			
			if( !isset( $inputConfig['label'] ) ) { //
				$inputConfig['label'] = static::getLabel( $inputType );
			}
			
			$inputConfig['label'] = Yii::t( 'app', ucfirst( $inputConfig['label'] ) );
			
			foreach( $inputConfig['widgets'] as $widgetName => $widgetConfig ) {
				
				unset( $inputConfig['widgets'][ $widgetName ] );
				
				if( !is_array( $widgetConfig ) ) {
					$widgetName   = $widgetConfig;
					$widgetConfig = [
						'rules'  => [],
						'config' => [],
					];
				}
				
				if( !isset( $widgetConfig['name'] ) ) { //
					$widgetConfig['name'] = $widgetName;
				}
				
				if( !isset( $widgetConfig['label'] ) ) { //
					$widgetConfig['label'] = static::getLabel( $widgetName );
				}
				
				$widgetConfig['label'] = Yii::t( 'app', ucfirst( $widgetConfig['label'] ) );
				
				$inputConfig['widgets'][ $widgetName ] = $widgetConfig;
				
			}
			
			$inputResult[ $inputType ] = $inputConfig;
		}
		
		return $inputResult;
	}
	
	public function baseWidget( $type, $data = null, $options = [] )
	{
		if( in_array( $type, self::getConstants( 'WIDGET_TYPE_' ) ) ) {
			$this->widgetType = $type;
		}
		
		switch( $type ) {
			
			case self::WIDGET_TYPE_TEXTAREA :
				
				$options = array_merge_recursive( $options, [
					'rows' => 3,
				] );
				
				$this->textarea( $options );
				
				break;
			
			case self::WIDGET_TYPE_TEXTEDITOR :
				
				$options = array_merge_recursive( $options, [
					//'id'   => 'editor-' . $PropertiesModel->primaryKey,
					'rows' => 20,
					//'data-id' => $PropertiesModel->primaryKey,
					//'value'   => $PropertiesModel->$type,
				] );
				
				$this->widget(
					TinyMce::class,
					[
						'options' => $options,
					]
				);
				
				break;
			
			case self::WIDGET_TYPE_MARKUP :
				
				$options = array_merge_recursive( $options, [
					//'id'   => 'editor-' . $PropertiesModel->primaryKey,
					//'data-id' => $PropertiesModel->primaryKey,
					//'value'   => $PropertiesModel->$type,
				] );
				
				$this->widget(
					MarkdownEditor::class,
					[
						//'model' => $PropertiesModel,
						//'attribute' => $PropertiesModel->type,
						'options' => $options,
					]
				);
				
				break;
			
			case self::WIDGET_TYPE_DATETIME :
			case self::WIDGET_TYPE_DATE :
			case self::WIDGET_TYPE_TIME :
				
				$options = array_merge_recursive( $options, [
				] );
				
				$config = [
					'type'          => DateTimePicker::TYPE_COMPONENT_APPEND,
					'convertFormat' => true,
					'options'       => [
						'class'       => 'form-control',
						'placeholder' => Yii::t( 'app', 'Select date' ),
					],
					'pluginOptions' => [
						'format'         => 'HH:mm d.MM.yyyy',
						'todayHighlight' => true,
						'todayBtn'       => true,
						'autoclose'      => true,
						'saveFormat'     => 'php:Y-m-d H:i:s',
					],
				];
				
				$this->widget( DateTimePicker::class, $config );
				
				break;
			
		}
		
		return $this;
	}
	
	public function __toString()
	{
		$output = parent::__toString();
		
		switch( $this->widgetType ) {
			
			case self::WIDGET_TYPE_TEXTEDITOR :
				
				$widgetId = $this->getInputId();
				
				$script = <<< JS
tinyMCE.EditorManager.execCommand('mceRemoveEditor', true, '$widgetId' );
tinyMCE.EditorManager.execCommand('mceAddEditor', true, '$widgetId' );
console.log('foo');

JS;
				
				break;
			
		}
		
		if( $script ?? false ) {
			$this->form->view->registerJs( $script, View::POS_LOAD );
		}
		
		return $output;
	}
	
	public function static( $options = [] )
	{
		$options = array_merge( $this->inputOptions, $options );
		
		$this->addAriaAttributes( $options );
		$this->adjustLabelFor( $options );
		
		Html::addCssClass($options, 'static');
		
		$this->parts['{input}'] = Html::tag('div', $this->model->{$this->attribute}, $options);
		
		return $this;
	}
	
}