<?php
/**
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		http://expressionengine.com/docs/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

// ------------------------------------------------------------------------

define('MINIMUM_PHP', '7.2.5');
define('MINIMUM_MYSQL', '5.5.3');
define('DOC_URL', 'https://docs.expressionengine.com/latest/');

// ------------------------------------------------------------------------

error_reporting(E_ALL);
ini_set('display_errors', 1);

define('SERVER_WIZ', TRUE);

global $vars, $requirements;
$vars = array();
load_defaults();

// AcceptPathInfo or similar support, i.e. no need for query strings
// check this first so it's already known before we go through
// the trouble of having the user fill out the database form
if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN')
{
	if ( ! isset($_COOKIE['wizard_segments']) && ! isset($_GET['cookie_check']))
	{
		setcookie('wizard_segments', 'check', time() + 60*60*2, '/', '', 0);

		@header("Location: index.php?cookie_check=yes");
	}
	elseif (isset($_GET['cookie_check']))
	{
		if (isset($_COOKIE['wizard_segments']))
		{
			@header("Location: index.php/segment_test/");
		}
		else
		{
			$vars['errors'][] = 'Cookies must be enabled';
		}
	}
	elseif($_COOKIE['wizard_segments'] == 'check')
	{
		$pathinfo = pathinfo(__FILE__);
		$self = ( ! isset($pathinfo['basename'])) ? 'index'.$ext : $pathinfo['basename'];
		$path_info = (isset($_SERVER['PATH_INFO'])) ? $_SERVER['PATH_INFO'] : @getenv('PATH_INFO');
		$orig_path_info = str_replace($_SERVER['SCRIPT_NAME'], '', (isset($_SERVER['ORIG_PATH_INFO'])) ? $_SERVER['ORIG_PATH_INFO'] : @getenv('ORIG_PATH_INFO'));

		if ($path_info != '' && $path_info != "/".$self)
		{
			$requirements['segment_support']['supported'] = 'y';
		}
		elseif($orig_path_info != '' && $orig_path_info != "/".$self)
		{
			$requirements['segment_support']['supported'] = 'y';
		}
		else
		{
			$requirements['segment_support']['supported'] = 'n';
		}

		setcookie('wizard_segments', $requirements['segment_support']['supported'], time() + 60*60*2, '/', '', 0);
		@header("Location: ../../index.php");
	}
	else
	{
		$requirements['segment_support']['supported'] = ($_COOKIE['wizard_segments'] == 'y') ? 'y' : 'n';
	}
}
else
{
	$requirements['segment_support']['supported'] = 'n'; // Windows rarely has support for URL Segments and is rather likely to screw up here.
}

// Memory Limit
$memory_limit = @ini_get('memory_limit');

if ($memory_limit == '-1') {
	$requirements['memory_limit']['supported'] = 'y';
} else {
	sscanf($memory_limit, "%d%s", $limit, $unit);

	if ($limit >= 32 || strtolower($unit) != 'm')
	{
		$requirements['memory_limit']['supported'] = 'y';
	}
}

// --------------------------------------------------------------------
// Display the form if this is the first load
// --------------------------------------------------------------------

if ( ! isset($_GET['wizard']) OR $_GET['wizard'] != 'run')
{
	$vars['form'] = TRUE;
	$vars['content'] = view('db_form', $vars, TRUE);
	display_and_exit();
}

// --------------------------------------------------------------------
// Validate form
// --------------------------------------------------------------------

$db = array(
	'db_hostname' => '',
	'db_username' => '',
	'db_password' => '',
	'db_name'     => ''
);

foreach ($db as $key => $val)
{
	if ( ! isset($_POST[$key]) OR ($_POST[$key] == '' && $key != 'db_password'))
	{
		$vars['message'] = 'The field '.ucfirst(str_replace('db_', '', $key)).' is required.';
		$vars['content'] = view('error_message', $vars, TRUE);
		display_and_exit();
	}

	$db[$key] = $_POST[$key];
}

// Database check
if (check_db($db) === TRUE)
{
	$requirements['mysql']['supported'] = 'y';
}

// PHP Version
if (version_compare(phpversion(), MINIMUM_PHP, '>='))
{
	$requirements['php']['supported'] = 'y';
}

// Check for json_encode and decode
if (function_exists('json_encode') && function_exists('json_decode'))
{
	$requirements['json_parser']['supported'] = 'y';
}

// Check for finfo_open
if (function_exists('finfo_open'))
{
	$requirements['fileinfo']['supported'] = 'y';
}

// Check for cURL
if (function_exists('curl_version'))
{
	$requirements['curl']['supported'] = 'y';
}

// Check for OpenSSL
if (function_exists('openssl_verify'))
{
	$requirements['openssl']['supported'] = 'y';
}

// Check for ZipArchive
if (class_exists('ZipArchive'))
{
	$requirements['ziparchive']['supported'] = 'y';
}

// CAPTCHAS need imagejpeg()
if (function_exists('imagejpeg'))
{
	$requirements['captchas']['supported'] = 'y';
}

// Image properties
if (function_exists('gd_info'))
{
	$requirements['image_properties']['supported'] = 'y';
}

// Image thumbnailing
if (function_exists('gd_info') OR function_exists('exec'))
{
	$requirements['image_resizing']['supported'] = 'y';
}

// GIF resizing
if (function_exists('imagegif'))
{
	$requirements['gif_resizing']['supported'] = 'y';
}

// JPG resizing
if (function_exists('imagejpeg'))
{
	$requirements['jpg_resizing']['supported'] = 'y';
}

// PNG resizing
if (function_exists('imagepng'))
{
	$requirements['png_resizing']['supported'] = 'y';
}

foreach($requirements as $requirement)
{
	if ($requirement['severity'] == 'required' && $requirement['supported'] == 'n')
	{
		$vars['errors'][] = $requirement['item'].' is required.';
	}
}

$vars['requirements'] = $requirements;
$vars['content'] = view('requirements_table', $vars, TRUE);
display_and_exit();


/**
 * Check DB
 *
 * @access	public
 * @param	array
 * @return	bool
 */
function check_db($db_config)
{
	global $vars, $requirements;

	foreach ($db_config as $key => $val)
	{
		$db_config[$key] = addslashes(trim($val));
	}

	if ( ! class_exists('PDO'))
	{
		$vars['errors'][] = 'Unable to connect to your database server. Contact your server administrator about enabling PDO.';
	}

	$options = array(
		PDO::ATTR_PERSISTENT => FALSE,
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_CASE => PDO::CASE_NATURAL,
		PDO::ATTR_STRINGIFY_FETCHES => ! (extension_loaded('pdo_mysql') && extension_loaded('mysqlnd'))
	);

	$pdo = new PDO(
		"mysql:host={$db_config['db_hostname']};dbname={$db_config['db_name']}",
		$db_config['db_username'],
		$db_config['db_password'],
		$options
	);

	if ( ! $pdo)
	{
		$vars['errors'][] = 'Unable to connect to your database server.';
	}
	else
	{
		$server_supports_utf8mb4 = TRUE;

		// Check version requirement
		if (version_compare($pdo->getAttribute(PDO::ATTR_SERVER_VERSION), MINIMUM_MYSQL, '>=') !== TRUE)
		{
			$vars['errors'][] = "Your MySQL server version does not meet the minimum requirements";
			$server_supports_utf8mb4 = FALSE;
		}

		// Check client version for utf8mb4 support
		$client_info = $pdo->getAttribute(PDO::ATTR_CLIENT_VERSION);

		if (strpos($client_info, 'mysqlnd') === 0)
		{
			$msyql_client_version = preg_replace('/^mysqlnd ([\d.]+).*/', '$1', $client_info);
			$mysql_client_target = '5.0.9';
		}
		else
		{
			$msyql_client_version = $client_info;
			$mysql_client_target = '5.5.3';
		}

		$client_supports_utf8mb4 = version_compare($msyql_client_version, $mysql_client_target, '>=');

		$requirements['emoji_support']['supported'] = ($client_supports_utf8mb4 && $server_supports_utf8mb4) ? 'y' : 'n';

		$queries = array(
			'create' => "CREATE TABLE IF NOT EXISTS ee_test (
				ee_id int(2) unsigned NOT NULL auto_increment,
				ee_text char(2) NOT NULL default '',
				PRIMARY KEY (ee_id))",
			'alter'  => "ALTER TABLE ee_test CHANGE COLUMN ee_text ee_text char(3) NOT NULL",
			'insert' => "INSERT INTO ee_test (ee_text) VALUES ('hi')",
			'update' => "UPDATE ee_test SET ee_text = 'yo'",
			'drop'   => "DROP TABLE IF EXISTS ee_test",
		);

		foreach($queries as $type => $sql)
		{
			if ($pdo->query($sql) === FALSE)
			{
				$vars['errors'][] = "Your MySQL user does not have ".strtoupper($type)." permissions";
			}
		}
	}

	$return = (count($vars['errors']) > 0) ? FALSE : TRUE;

	if ( ! $client_supports_utf8mb4)
	{
		$vars['errors'][] = "Your MySQL client version ({$msyql_client_version}) does not meet the minimum requirements to support Emojis ({$mysql_client_target}). <a href='" . DOC_URL . "troubleshooting/install_and_update/emoji_support.html' rel='external'>Read how to fix this</a>.";
	}

	return $return;
}


// --------------------------------------------------------------------

/**
 * Display and Exit
 *
 * @access	public
 * @return	void
 */
function display_and_exit()
{
	global $vars;
	echo view('container', $vars);
	exit;
}

// --------------------------------------------------------------------

/**
 * Load default variables
 *
 * @access	public
 * @return	void
 */
function load_defaults()
{
	global $vars, $requirements;

	$vars['form']        = FALSE;
	$vars['heading']     = "ExpressionEngine Server Compatibility Wizard";
	$vars['title']       = "ExpressionEngine Server Compatibility Wizard";
	$vars['content']     = '';
	$vars['errors']      = array();
	$vars['db_hostname'] = (isset($_POST['db_hostname'])) ? $_POST['db_hostname'] : '';
	$vars['db_username'] = (isset($_POST['db_username'])) ? $_POST['db_username'] : '';
	$vars['db_password'] = (isset($_POST['db_password'])) ? $_POST['db_password'] : '';
	$vars['db_name']     = (isset($_POST['db_name'])) ? $_POST['db_name'] : '';


	$requirements = array(
		'php' => array(
			'item'      => "PHP Version ".MINIMUM_PHP." or greater",
			'severity'  => "required",
			'supported' => 'n'
		),
		'mysql' => array(
			'item'      => "MySQL (Version ".MINIMUM_MYSQL.") support in PHP",
			'severity'  => "required",
			'supported' => 'n'
		),
		'memory_limit' => array(
			'item'      => '>= 32 MB Memory Allocated to PHP',
			'severity'  => 'required',
			'supported' => 'n'
		),
		'json_parser' => array(
			'item'      => 'JSON Parser',
			'severity'  => 'required',
			'supported' => 'n'
		),
		'fileinfo' => array(
			'item'      => 'File Information (fileinfo)',
			'severity'  => 'required',
			'supported' => 'n'
		),
		'curl' => array(
			'item'      => 'cURL',
			'severity'  => 'required',
			'supported' => 'n'
		),
		'openssl' => array(
			'item'      => 'OpenSSL',
			'severity'  => 'required',
			'supported' => 'n'
		),
		'ziparchive' => array(
			'item'      => 'ZipArchive',
			'severity'  => 'required',
			'supported' => 'n'
		),
		'emoji_support' => array(
			'item'      => "Emoji Support",
			'severity'  => "suggested",
			'supported' => 'n'
		),
		'segment_support' => array(
			'item'      => "URL Segment Support",
			'severity'  => "suggested",
			'supported' => 'n'
		),
		'captchas' => array(
			'item'      => "CAPTCHAs and watermarking",
			'severity'  => "suggested",
			'supported' => 'n'
		),
		'image_properties' => array(
			'item'      => "Image property calculations using GD",
			'severity'  => "suggested",
			'supported' => 'n'
		),
		'image_resizing' => array(
			'item'      => "Image Thumbnailing using GD, GD2, Imagemagick or NetPBM",
			'severity'  => "suggested",
			'supported' => 'n'
		),
		'gif_resizing' => array(
			'item'      => "GIF Image Resizing Using GD (or GD 2)",
			'severity'  => "optional",
			'supported' => 'n'
		),
		'jpg_resizing' => array(
			'item'      => "JPEG Image Resizing Using GD (or GD 2)",
			'severity'  => "optional",
			'supported' => 'n'
		),
		'png_resizing' => array(
			'item'      => "PNG Image Resizing Using GD (or GD 2)",
			'severity'  => "optional",
			'supported' => 'n'
		),
	);
}

// --------------------------------------------------------------------

/**
 * Load View
 *
 * This function is used to load a "view" file.  It has three parameters:
 *
 * 1. The name of the "view" file to be included.
 * 2. An associative array of data to be extracted for use in the view.
 * 3. TRUE/FALSE - whether to return the data or load it.  In
 * some cases it's advantageous to be able to return data so that
 * a developer can process it in some way.
 *
 * @access	public
 * @param	string
 * @param	array
 * @param	bool
 * @return	void
 */
function view($view, $vars = array(), $return = FALSE)
{
	return _mini_loader(array(
		'_ci_view'   => $view,
		'_ci_vars'   => $vars,
		'_ci_return' => $return
	));
}

// --------------------------------------------------------------------


/**
 * Loader
 *
 * This function is used to load views and files.
 * Variables are prefixed with _ci_ to avoid symbol collision with
 * variables made available to view files
 *
 * @access	private
 * @param	array
 * @return	void
 */
function _mini_loader($_ci_data)
{
	static $_ci_cached_vars = array();

	// Set the default data variables
	foreach (array('_ci_view', '_ci_vars', '_ci_path', '_ci_return') as $_ci_val)
	{
		$$_ci_val = ( ! isset($_ci_data[$_ci_val])) ? FALSE : $_ci_data[$_ci_val];
	}

	// Set the path to the requested file
	if ($_ci_path == '')
	{
		$_ci_ext = pathinfo($_ci_view, PATHINFO_EXTENSION);
		$_ci_file = ($_ci_ext == '') ? $_ci_view.'.php' : $_ci_view;
		$_ci_path = './views/'.$_ci_file;
	}
	else
	{
		$_ci_x = explode('/', $_ci_path);
		$_ci_file = end($_ci_x);
	}

	if ( ! file_exists($_ci_path))
	{
		exit('Unable to load the requested file: '.$_ci_file);
	}

	/*
	 * Extract and cache variables
	 *
	 * You can either set variables using the dedicated $this->load_vars()
	 * function or via the second parameter of this function. We'll merge
	 * the two types and cache them so that views that are embedded within
	 * other views can have access to these variables.
	 */
	if (is_array($_ci_vars))
	{
		$_ci_cached_vars = array_merge($_ci_cached_vars, $_ci_vars);
	}
	extract($_ci_cached_vars);

	/*
	 * Buffer the output
	 *
	 * We buffer the output for two reasons:
	 * 1. Speed. You get a significant speed boost.
	 * 2. So that the final rendered template can be
	 * post-processed by the output class.  Why do we
	 * need post processing?  For one thing, in order to
	 * show the elapsed page load time.  Unless we
	 * can intercept the content right before it's sent to
	 * the browser and then stop the timer it won't be accurate.
	 */
	ob_start();

	// If the PHP installation does not support short tags we'll
	// do a little string replacement, changing the short tags
	// to standard PHP echo statements.

	include($_ci_path); // include() vs include_once() allows for multiple views with the same name

	$buffer = ob_get_contents();
	@ob_end_clean();
	return $buffer;
}

// --------------------------------------------------------------------

/* End of file index.php */
/* Location: ./index.php */
