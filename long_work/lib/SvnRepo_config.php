<?php

function my_dump($__object){
	print "<pre>".print_r($__object, TRUE)."</pre>";
}

/*
 * Файл с конфигурациями программы
 * 
 * 
 * 
 * 
 */

$username = 'g2210';
$password = 'sg150517';

svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $username);
svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $password); 
    
svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);

$url_of_repository = 'https://labx.ru:7878/svn/2016/php/';

// Массив оцениваемых студентов
$Student_nick_arr = array(
	'g2210'
);

$tasks_path = '../tasks_conf/';

$tasks_arr = array();

$task_files = glob($tasks_path."*");

foreach ($task_files as $task)
{
	$str = file_get_contents($task);
		$tasks_arr[] = json_decode($str);	
}

my_dump($tasks_arr);

my_dump($test);