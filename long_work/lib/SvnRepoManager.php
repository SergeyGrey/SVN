<?php


// htpasswd.exe -D htpasswd newOne Удалить пользователя
//  htpasswd.exe -bm htpasswd newOne 1234 создать пользователя
// svn mkdir -m Create --username dcm --password 1234 https://Grey:2020/svn/testRepo/qwerty // создать папку

/**
 * Description of SvnRepoManager
 * Easy managing of Svn_Repository
 * @author Сергей
 */
 class SvnRepoManager {
	// Путь до репозитория в ФС
	private static $repo_dir = 'C:/Repositories/';
	// URL корня репозиториев
	private static $repo_url = 'https://Grey:2020/svn/';
	// название папки с заданием
	private static $task_folder_name = 'tasks';
	// nick админа
	private static $admin_name = 'dcm';
	// пароль
	private static $password = '1234';

	/**
	 * Создание репозитория с пользователями 
	 * @param string $__repo_name название репозитория
	 * @param array of strings $__users_arr массив имён студентов
	 * @return string строка с именами пользователей и их паролями
	 */
	public static function create_repo($__repo_name, $__users_arr){
	// сохраняем директорию скрипта и переходим в директорию репозиториев
	$script_dir = getcwd();
	chdir(self::$repo_dir);
	
	// инициализируем строку с никами и паролями
	$result = "User:Password".PHP_EOL;
	
	// make folder for repository
	system("mkdir ".$__repo_name);
	
	// create a repository
	system("svnadmin create ".$__repo_name);
	
	// создаём конфигурационный файл доступа к репозиторию
	file_put_contents (self::$repo_dir.$__repo_name.'/conf/VisualSVN-SvnAuthz.ini', SvnRepoManager::create_conf_file($__users_arr));
	
	// create a tasks folder
	system("svn mkdir -m Create --username ".self::$admin_name.' --password '.self::$password.' '.self::$repo_url.$__repo_name.'/'.self::$task_folder_name);
	
	// Register_users and create their own folders
	foreach ($__users_arr as $user){
		// generate password
		$password = SvnRepoManager::generate_sequence(6);
		
		// write it into output
		$result.=$user.':'.$password.PHP_EOL;
		
		// добавляем пользователя в файл htpasswd !!! файл htpasswd.exe должен находиться в корневой папке с репозиториями
		system('htpasswd.exe -bm htpasswd '.$user.' '.$password);
		
		// create a folder for user
		system("svn mkdir -m Create --username ".self::$admin_name.' --password '.self::$password.' '.self::$repo_url.$__repo_name.'/'.$user);
	}
	
	// возвращаемся  в начальную директорию
	chdir($script_dir);
	
	return $result;
	}
	
	/**
	 * Удаления репозитория
	 * @param string $__repo_name название удаляемого репозитория
	 * @return string information about result of deleting
	 */
	public static function delete_repo($__repo_name){
		// сохраняем директорию скрипта и переходим в директорию репозиториев
		$script_dir = getcwd();
		chdir(self::$repo_dir);
		
		// Информация о результатах удаления
		$result_info = '';
		
		// получаю всех пользователей репозитория
		$current_users_arr = SvnRepoManager::get_repo_users(self::$repo_dir.$__repo_name.'/conf/VisualSVN-SvnAuthz.ini');
		
		// получаю пользователей всех остальных репозиториев
		$other_users_arr = array();
		foreach(glob(self::$repo_dir.'*') as $repo)
			if ($repo != self::$repo_dir.$__repo_name && is_dir($repo))
				$other_users_arr = array_merge ($other_users_arr, SvnRepoManager::get_repo_users($repo.'/conf/VisualSVN-SvnAuthz.ini'));
		// удаляю повторяющихся пользователей
		array_unique($other_users_arr);
		
		/* 
		 * для каждого пользователя репозитория проверяю его наличие в других репозиториях 
		 * если повторился, то вывожу сообщение о невозможности удаления,
		 * иначе удаляю программой htpasswd
		 */
		foreach ($current_users_arr as $user){
			if (!in_array($user, $other_users_arr))
				system('htpasswd -D htpasswd '.$user);				
			else 		
				$result_info.= 'can not delete '.$user.PHP_EOL;
		}
		
		// Удаляю репозиторий
		system('RD /S /Q '.$__repo_name);
		
		// возвращаемся  в начальную директорию
		chdir($script_dir);
		
		return $result_info;
	}
	
	/**
	 * Генерация случайного пароля
	 * @param integer $__length длинна пароля
	 * @param string $__allowed_symbols символы пароля
	 * @return string пароль
	 */
	private static function generate_sequence($__length, $__allowed_symbols = '1234567890abcdefjh'){
		$count_symbols = strlen($__allowed_symbols);
		$sequence = '';
		
		// генерация случайной последовательности символов
		for ($index = 0; $index < $__length; $index++)
			$sequence .= substr($__allowed_symbols, rand(1, $count_symbols) - 1, 1);
  
		 return $sequence;
	}
	
	/**
	 * Костыль но пока так
	 * создание текста для записи в VisualSVN-SvnAuthz.ini (файл доступа к репозиторию)
	 * @param array $__users_arr  пользователи репозитория
	 * @return string текст для файла VisualSVN-SvnAuthz.ini
	 */
	private static function create_conf_file($__users_arr){
		$conf_str = '[/]'.PHP_EOL;
		
		$conf_str.= '@Admins=rw'.PHP_EOL;
		
		foreach ($__users_arr as $user)
			$conf_str.= $user.'=rw'.PHP_EOL;
		
		$conf_str.=PHP_EOL;
		
		$conf_str.= '[/'.self::$task_folder_name.']'.PHP_EOL;
		
		$conf_str.= '@Admins=rw'.PHP_EOL;
		
		foreach ($__users_arr as $user)
			$conf_str.= $user.'=r'.PHP_EOL;
		
		$conf_str.=PHP_EOL;
		
		foreach ($__users_arr as $user)
		{
			$conf_str.= '[/'.$user.']'.PHP_EOL;
			
			foreach ($__users_arr as $nick)
				$conf_str.= $user == $nick
						  ? $nick.'=rw'.PHP_EOL
						  : $nick.'='.PHP_EOL;
		}
		
		return $conf_str;
	}
	
	/**
	 * Получение информации о пользователе из ini файла
	 * @param string $__ini_file_path путь к конфигурационному файлу
	 * @return array список пользователей
	 */
	private static function get_repo_users($__ini_file_path){
		// считываю файл
		$file = file_get_contents($__ini_file_path);
		// нахожу папка пользователей
		preg_match_all('#\[/(.+?)\]#', $file, $matches);
		
		// забираю необходимый карман
		$matches = $matches[1];
		// удаляю из списка название папки с заданиями
		unset($matches[0]);

		return $matches;
	}
}
