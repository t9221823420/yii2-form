<?php

namespace yozh\form\controllers;

use yozh\crud\traits\CRUDTrait;
use yozh\form\models\BaseModel;
use yozh\form\AssetsBundle;
use yozh\base\controllers\DefaultController as Controller;


class DefaultController extends Controller
{
	use CRUDTrait {
		actionIndex as protected traitActionIndex;
	}
	
	protected static function defaultModel()
	{
		return BaseModel::className();
	}
	
	public function actionIndex()
	{
		//AssetsBundle::register( $this->view );
		return $this->traitActionIndex();
	}
	
}
