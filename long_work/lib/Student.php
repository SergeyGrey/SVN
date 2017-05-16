<?php

require_once ('SvnRepo_config.php');
require_once ('Analizers_config.php');

/**
 * Description of Student
 *
 * @author Сергей
 */
class Student {
	public $name;
	private $tasks_logs;
	public $results;
	
	public function __construct($__name){
		$this->name = $__name;
		$this->tasks_logs = $this->get_student_logs();
	//	$this->results = create_student_results();
	}
	
	private function get_student_logs()
	{
		$tasks = $GLOBALS['tasks_arr'];
		$url = $GLOBALS['url_of_repository'];
		
		$logs_arr = array();
		
		foreach ($tasks as $task) 
			@$logs_arr[$task->name] = svn_log ($url.$this->name.'/'.$task->name);
		
		// фильтрация по автору 
		foreach ($logs_arr as $task_number => $task_logs)
		{
			foreach ($task_logs as $commit_number => $commit)
			{
				if ($commit['author'] != $this->name)
					unset($logs_arr[$task_number][$commit_number]);
			}
		}		
		
		// Саммые последние коммиты будут в конце (фича)
		foreach ($logs_arr as $task_number => $task_logs)
			$logs_arr[$task_number] = array_reverse($task_logs);
		
		my_dump($logs_arr);
		
		return $logs_arr;
	}
	
	public function Analize()
	{
		$tasks_arr = $GLOBALS['tasks_arr'];
		$students_name_arr = $GLOBALS['Student_nick_arr'];
		
		$analizer_arr = $GLOBALS['analizer_arr'];
		foreach ($tasks_arr as $task){
		foreach ($analizer_arr as $analizer)
			$this->results[$task->name] = $analizer::Analize($this->tasks_logs, $task, $students_name_arr, $this->name);
		}
	}
}

$st = new Student('g2210');
