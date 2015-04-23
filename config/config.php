<?php

$config = array(

	//数据库连接信息
	'database' => array(
		'default' => array( //主从从..
			array(
				'hostname'   => 'localhost',
				'port'   => 3306,
				'database'   => 'table2form',
				'username'   => 'root',
				'password'   => '1',
				'persistent' => false,
				'charset'	  => 'utf8',
				'table_prefix' => 'jd_',
			),
		 ),
		'jdphp' => array( //主从从..
			array(
				'hostname'   => 'localhost',
				'port'   => 3306,
				'database'   => 'jdphp108',
				'username'   => 'root',
				'password'   => '1',
				'persistent' => false,
				'charset'	  => 'utf8',
				'table_prefix' => 'jd_',
			),
		),
	),

);
?>