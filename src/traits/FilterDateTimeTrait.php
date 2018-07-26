<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 12.05.2018
 * Time: 9:27
 */

namespace yozh\form\traits;

trait FilterDateTimeTrait
{
	public $filter_dateFrom;
	public $filter_dateTo;
	
	protected function _addFilterDateTime( $query, $tableName = null, $attribute = 'timestamp', $format = "Y-m-d" )
	{
		
		$tableName = $tableName ?? $query->getRawTableName();
		
		if( $this->filter_dateFrom ?? false ) {
			$query->andWhere( [ '>=', "$tableName.$attribute", date( $format, strtotime( $this->filter_dateFrom ) ) ] );
		}
		else {
			//$this->filter_dateFrom = date( 'Y-m-d H:i:s' ); // some default params
		}
		
		if( $this->filter_dateTo ?? false ) {
			$query->andWhere( [ '<=', "$tableName.$attribute", date( $format, strtotime( $this->filter_dateTo . ' +1 day' ) ) ] );
		}
		else {
			//$this->filter_dateTo = $this->filter_dateFrom;  // some default params, but not less $this->dateFrom
		}
		
	}
	
}