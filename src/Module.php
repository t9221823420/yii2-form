<?php

namespace yozh\form;

use yozh\base\Module as BaseModule;

class Module extends BaseModule
{

	const MODULE_ID = 'form';
	
	public $controllerNamespace = 'yozh\\' . self::MODULE_ID . '\controllers';
	
}
