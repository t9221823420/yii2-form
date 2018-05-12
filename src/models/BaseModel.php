<?php

namespace yozh\form\models;

use yozh\base\models\BaseModel as ActiveRecord;
use yozh\form\traits\AttributeActionListTrait;

abstract class BaseModel extends ActiveRecord
{
	use AttributeActionListTrait;
}