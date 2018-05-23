<?php
/**
 * Created by PhpStorm.
 * User: bw_dev
 * Date: 10.04.2018
 * Time: 16:39
 */

namespace yozh\form\traits;

use yozh\base\traits\actions\ActionUpdateAttributeTrait;

trait ActiveBooleanColumnTrait
{
	use ActionUpdateAttributeTrait;
	
	public function actionSwitch( $id, $attribute, $value )
	{
		return static::actionUpdateAttribute( $id, $attribute, $value );
	}
}