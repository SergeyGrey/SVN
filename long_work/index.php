<?php

function my_dump($__object){
	print "<pre>".print_r($__object, TRUE)."</pre>";
}

//$username = 'dcm';
//$password = '1234';
//
//svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_USERNAME, $username);
//svn_auth_set_parameter(SVN_AUTH_PARAM_DEFAULT_PASSWORD, $password); 
//    
//svn_auth_set_parameter(PHP_SVN_AUTH_PARAM_IGNORE_SSL_VERIFY_ERRORS, true);
//


//my_dump(glob(realpath('C:\\Repositories\\').'*'));
//echo realpath('C:\\Repositories\\');

function get_repo_users($__repo_name){

$file = file_get_contents('C:/Repositories/12345/conf/VisualSVN-SvnAuthz.ini');

preg_match_all('#\[/(.+?)\]#', $file, $matches);

$matches = $matches[1];

unset($matches[0]);

return $matches;
}
my_dump($matches);