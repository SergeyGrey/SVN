<?php

require_once 'SvnRepo_config.php';
require_once 'Student.php';
require_once 'RepoVisualizer.php';


/**
 * Description of SvnRepoCollector
 * 
 * @author Сергей
 */
class SvnRepoCollector{
	public static $students;
	
	public static function initialise_students($__names_arr){
		self::$students = array();
		
		foreach ($__names_arr as $name)
			self::$students[$name] = new Student($name);
	}
	
	public static function process($student_name){
		self::$students[$student_name]->Analize();
	}
	
	public static function js_visualize($__student_names_arr){ /// ($__
		$js_result_str = '';
		
		foreach ($__student_names_arr as $name)
			$js_result_str.= RepoVisualizer::visualize(self::$students[$name]->results);
		
		return $js_result_str;
	}
}

SvnRepoCollector::initialise_students($GLOBALS['Student_nick_arr']);

SvnRepoCollector::process('g2210');

my_dump(SvnRepoCollector::$students['g2210']);