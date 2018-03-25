<?php

use yozh\base\components\Migration;
use yozh\base\components\db\Schema;

/**
 * Class m180305_040759_tablename_table_dev
 */
class m000000_000000_tablename_table_dev extends Migration
{
	protected static $_table = 'tablename';
	
	/**
	 * {@inheritdoc}
	 */
	public function safeUp()
	{
		
		// remove this
		return true;
		
		static::$_columns = [
			'id' => $this->primaryKey(),
		];
		
		$this->alterTable( [
			'mode' => self::ALTER_MODE_IGNORE,
		] );
		
		return false;
		
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function safeDown()
	{
		echo "m180305_040759_tablename_table_dev cannot be reverted.\n";
		
		return false;
	}
	
	/*
	// Use up()/down() to run migration code without a transaction.
	public function up()
	{

	}

	public function down()
	{
		echo "m180305_040759_tablename_table_dev cannot be reverted.\n";

		return false;
	}
	*/
}
