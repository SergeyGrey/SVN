<?php

/**
 * Description of Stupid_Anal
 * Заглушка для анализаторов
 * @author Сергей
 */
class Stupid_Anal{
	private static $name = "Stupid_Anal";

	public static function Analize($__commits, $__task, $__students_names_arr, $__current_student){
		return array('message' => self::$name, 'value' => 1);
	}
}
