<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 15.04.2018
 * Time: 13:28
 */

namespace yozh\form\traits;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yozh\form\interfaces\DefaultFiltersInterface;
use yozh\form\models\BaseActiveRecord;

trait ModelSearchTrait
{
	public $filter_search;
	
	public function rules( $rules = [], $update = false )
	{
		static $_rules;
		
		if( !$_rules || $update){
			
			$_rules = [
				
				'required' => [ [], 'required', 'except' => BaseActiveRecord::SCENARIO_FILTER ],
				
				'filter_search' => [ [ 'filter_search', ], 'filter', 'filter' => '\yii\helpers\HtmlPurifier::process' ],
				
			];
			
			if( $this instanceof ActiveRecordInterface ) {
				$_rules = parent::rules( Validator::merge( $_rules, $rules ) );
			}
			
		}
		
		return $_rules;
	}
	
	public function scenarios()
	{
		$scenarios = Model::scenarios();
		
		if( $this instanceof DefaultFiltersInterface ) {
			
			$scenarios[ static::SCENARIO_FILTER ] = $this->defaultFiltersList();
			
			$this->scenario = static::SCENARIO_FILTER;
		}
		
		// bypass scenarios() implementation in the parent class
		return $scenarios;
	}
	
	/**
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search( $params )
	{
		/**
		 * @var $query ActiveQuery
		 */
		$query = parent::find();
		
		$dataProvider = new ActiveDataProvider( [
			'query' => $query,
			//'sort'  => [ 'defaultOrder' => [ 'id' => SORT_DESC ] ],
		] );
		
		if( !( $this->load( $params ) && $this->validate() ) ) {
			return $dataProvider;
		}
		
		if( $this instanceof DefaultFiltersInterface ) {
			$this->_addDefaultFiltersConditions( $query );
		}
		
		return $dataProvider;
	}
	
	public function defaultFiltersList( ?array $only = null, ?array $except = null, ?bool $schemaOnly = false )
	{
		return parent::attributesDefaultList(
			$this->attributesIndexList()
			, $except
			, $schemaOnly
		);
	}
}