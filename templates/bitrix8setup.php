<?
header("Content-type: text/html; charset=cp1251");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Expires: 0");
header("Pragma: public");

error_reporting(E_COMPILE_ERROR|E_ERROR|E_CORE_ERROR|E_PARSE);

ob_implicit_flush(true);
set_time_limit(1800);

$strAction = $_REQUEST["action"];
if ($strAction!="LOAD" && $strAction!="UNPACK" && $strAction!="LOG")
	$strAction = "LIST";

if ($strAction=="LOAD" && (!isset($_REQUEST["url"]) || strlen($_REQUEST["url"])<=0))
	$strAction = "LIST";

if ($strAction=="UNPACK" && (!isset($_REQUEST["filename"]) || strlen($_REQUEST["filename"])<=0))
	$strAction = "LIST";

$lang = "ru";

$this_script_name = basename(__FILE__);

$MESS = array();
if ($lang=="ru")
{
	$MESS["LOADER_LICENSE_KEY"] = "Лицензионный ключ";
	$MESS["LOADER_TITLE"] = "Загрузка продукта \"1С-Битрикс: Управление сайтом 8.0\"";
	$MESS["LOADER_SUBTITLE1"] = "Загрузка продукта";
	$MESS["LOADER_SUBTITLE2"] = "1С-Битрикс: Управление сайтом 8.0";
	$MESS["LOADER_MENU_LIST"] = "Выбор дистрибутива";
	$MESS["LOADER_MENU_LOAD"] = "Загрузка дистрибутива с сервера";
	$MESS["LOADER_MENU_UNPACK"] = "Распаковка дистрибутива";
	$MESS["LOADER_TECHSUPPORT"] = "При возникновении проблем<br>с установкой продукта<br><a href=\"http://www.1c-bitrix.ru\">&quot;1С-Битрикс: Управление сайтом&quot;</a><br>обращайтесь в систему техподдержки<br><a href=\"http://www.1c-bitrix.ru\">компании &quot;Битрикс&quot;</a>";
	$MESS["LOADER_TITLE_LIST"] = "Выбор дистрибутива";
	$MESS["LOADER_TITLE_LOAD"] = "Загрузка дистрибутива на сайт";
	$MESS["LOADER_TITLE_UNPACK"] = "Распаковка дистрибутива";
	$MESS["LOADER_TITLE_LOG"] = "Отчет по загрузке";
	$MESS["LOADER_SAFE_MODE_ERR"] = "<font color=\"#FF0000\"><b>Внимание!</b></font> PHP на вашем сайте работает в Safe Mode. Установка продукта в автоматическом режиме невозможна. Пожалуйста, обратитесь в службу технической поддержки для получения дополнительной информации.";
	$MESS["LOADER_NO_PERMS_ERR"] = "<font color=\"#FF0000\"><b>Внимание!</b></font> PHP не имеет прав на запись в корневую папку #DIR# вашего сайта. Загрузка продукта может оказаться невозможной. Пожалуйста, установите необходимые права на корневую папку вашего сайта или обратитесь к администраторам вашего хостинга.";
	$MESS["LOADER_EXISTS_ERR"] = "<b>Внимание!</b> На сайте найдена распакованая копия продукта \"1С-Битрикс: Управление сайтом\". Вы должны удалить эту копию до загрузки и установки новой копии. В противном случае загрузчик <nobr>и / или</nobr> инсталлятор продукта могут работать некорректно.";
	$MESS["LOADER_IS_DISTR"] = "На сайте найдены загруженые дистрибутивы. Нажмите на название любого из дистрибутивов для его распаковки:";
	$MESS["LOADER_OVERWRITE"] = "<b>Внимание!</b> Существующие на сайте файлы могут быть перезаписаны файлами из дистрибутива.";
	$MESS["LOADER_IS_DISTR_PART"] = "На сайте найдены недогруженные дистрибутивы. Нажмите на название любого из недогруженных дистрибутивов для полной загрузки:";
	$MESS["LOADER_NEW_LOAD_TITLE"] = "Загрузить с сайта <a href=\"http://www.1c-bitrix.ru\" target=\"_blank\">http://www.1c-bitrix.ru</a> новый дистрибутив";
	$MESS["LOADER_NEW_ED"] = "Редакция дистрибутива";
	$MESS["LOADER_NEW_AUTO"] = "Автоматически запустить распаковку после загрузки";
	$MESS["LOADER_NEW_STEPS"] = "Загружать по шагам с шагом";
	$MESS["LOADER_NEW_STEPS0"] = "неограниченно долгим";
	$MESS["LOADER_NEW_STEPS30"] = "не более 30 секунд";
	$MESS["LOADER_NEW_STEPS60"] = "не более 60 секунд";
	$MESS["LOADER_NEW_STEPS120"] = "не более 120 секунд";
	$MESS["LOADER_NEW_STEPS180"] = "не более 180 секунд";
	$MESS["LOADER_NEW_STEPS240"] = "не более 240 секунд";
	$MESS["LOADER_NEW_LOAD"] = "Загрузить";
	$MESS["LOADER_DESCR"] = "Этот скрипт предназначен для загрузки дистрибутива \"1С-Битрикс: Управление сайтом\" с сайта <a href=\"http://www.1c-bitrix.ru/download/index.php\" target=\"_blank\">www.1c-bitrix.ru</a> непосредственно на ваш сайт, а так же для распаковки дистрибутива на вашем сайте.<br><br> Загрузите этот скрипт в корневую папку вашего сайта и откройте его в браузере (введите в адресной строке браузера <nobr>http://&lt;ваш сайт&gt;/".$this_script_name."</nobr>).";
	$MESS["LOADER_BACK_2LIST"] = "Вернуться в список дистрибутивов";
	$MESS["LOADER_LOG_ERRORS"] = "Произошли следующие ошибки:";
	$MESS["LOADER_NO_LOG"] = "Log-файл не найден";
	$MESS["LOADER_BOTTOM_NOTE1"] = "<b><font color=\"#FF0000\">Внимание!</font></b> По окончании установки продукта <b>обязательно</b> удалите скрипт <nobr>/".$this_script_name."</nobr> с вашего сайта. Доступ постороннего человека к этому скрипту может повлечь за собой нарушение работы вашего сайта.";
	$MESS["LOADER_KB"] = "кб";
	$MESS["LOADER_LOAD_QUERY_SERVER"] = "Запрашиваю сервер...";
	$MESS["LOADER_LOAD_QUERY_DISTR"] = "Запрашиваю дистрибутив #DISTR#";
	$MESS["LOADER_LOAD_CONN2HOST"] = "Открываю соединение к #HOST#...";
	$MESS["LOADER_LOAD_NO_CONN2HOST"] = "Не могу соединиться с #HOST#:";
	$MESS["LOADER_LOAD_QUERY_FILE"] = "Запрашиваю файл...";
	$MESS["LOADER_LOAD_WAIT"] = "Ожидаю ответ...";
	$MESS["LOADER_LOAD_SERVER_ANSWER"] = "Ошибка загрузки. Сервер ответил: #ANS#";
	$MESS["LOADER_LOAD_SERVER_ANSWER1"] = "Ошибка загрузки. У вас нет прав на доступ к этому дистрибутиву. Сервер ответил: #ANS#";
	$MESS["LOADER_LOAD_NEED_RELOAD"] = "Докачка дистрибутива невозможна. Начинаю качать заново.";
	$MESS["LOADER_LOAD_NO_WRITE2FILE"] = "Не могу открыть файл #FILE# на запись";
	$MESS["LOADER_LOAD_LOAD_DISTR"] = "Загружаю дистрибутив #DISTR#";
	$MESS["LOADER_LOAD_ERR_SIZE"] = "Ошибка размера файла";
	$MESS["LOADER_LOAD_ERR_RENAME"] = "Не могу переименовать файл #FILE1# в файл #FILE2#";
	$MESS["LOADER_LOAD_CANT_OPEN_WRITE"] = "Не могу открыть файл #FILE# на запись";
	$MESS["LOADER_LOAD_CANT_OPEN_READ"] = "Не могу открыть файл #FILE# на чтение";
	$MESS["LOADER_LOAD_LOADING"] = "Загружаю файл... дождитесь окончания загрузки...";
	$MESS["LOADER_LOAD_FILE_SAVED"] = "Файл сохранен: #FILE# [#SIZE# байт]";
	$MESS["LOADER_UNPACK_ACTION"] = "Распаковываю дистрибутив... дождитесь окончания распаковки...";
	$MESS["LOADER_UNPACK_UNKNOWN"] = "Неизвестная ошибка. Повторите процесс еще раз или обратитесь в службу технической поддержки";
	$MESS["LOADER_UNPACK_SUCCESS"] = "Дистрибутив успешно распакован";
	$MESS["LOADER_UNPACK_ERRORS"] = "Дистрибутив распакован с ошибками";
	$MESS["LOADER_KEY_DEMO"] = "Демонстрационная версия";
	$MESS["LOADER_KEY_COMM"] = "Коммерческая версия";
	$MESS["LOADER_KEY_TITLE"] = "Введите лицензионный ключ";
//suffixes:
//_encode_php5.tar.gz
//_encode_php4.tar.gz
//_source.tar.gz
	$MESS_ED = array(
		"big_business"=>"Большой бизнес",
		"portal"=>"Портал",
		"business"=>"Бизнес",
		"expert"=>"Эксперт",
		"small_business"=>"Малый бизнес",
		"standard"=>"Стандарт",
		"start"=>"Старт",
		"franchise"=>"Сайт 1С-Франчайзи",

	);
}
else
{
	$MESS["LOADER_LICENSE_KEY"] = "Your license key";
	$MESS["LOADER_TITLE"] = "Loading Bitrix Site Manager 8.0";
	$MESS["LOADER_SUBTITLE1"] = "Loading";
	$MESS["LOADER_SUBTITLE2"] = "Bitrix Site Manager 8.0";
	$MESS["LOADER_MENU_LIST"] = "Select package";
	$MESS["LOADER_MENU_LOAD"] = "Download installation package from server";
	$MESS["LOADER_MENU_UNPACK"] = "Unpack installation package";
	$MESS["LOADER_TECHSUPPORT"] = "If you experience problems installing<br><a href=\"http://www.bitrixsoft.com\">Bitrix Site Manager</a><br>please go to the <br><a href=\"http://www.bitrixsoft.com\">Bitrix</a> technical support service";
	$MESS["LOADER_TITLE_LIST"] = "Select installation package";
	$MESS["LOADER_TITLE_LOAD"] = "Uploading installation package to the site";
	$MESS["LOADER_TITLE_UNPACK"] = "Unpack installation package";
	$MESS["LOADER_TITLE_LOG"] = "Upload report";
	$MESS["LOADER_SAFE_MODE_ERR"] = "<font color=\"#FF0000\"><b>Attention!</b></font> Your PHP functions in Safe Mode. The Setup cannot proceed in automatic mode. Please consult the technical support service for additional instructions.";
	$MESS["LOADER_NO_PERMS_ERR"] = "<font color=\"#FF0000\"><b>Attention!</b></font> PHP has not enough permissions to write to the root directory #DIR# of your site. Loading is likely to fail. Please set the required access permissions to the root directory of your site or consult administrators of your hosting service.";
	$MESS["LOADER_EXISTS_ERR"] = "<b>Attention!</b> Setup has found the deployed copy of Bitrix Site Manager on your site. You must remove this copy before you proceed with uploading and installing a new copy. Otherwise, loader and/or installer will encounter problems.";
	$MESS["LOADER_IS_DISTR"] = "Uploaded installation packages found on the site. Click the name of any package to start installation:";
	$MESS["LOADER_OVERWRITE"] = "<b>Attention!</b> Files currently present on your site will possibly be overwritten with files from the package.";
	$MESS["LOADER_IS_DISTR_PART"] = "Incompletely uploaded installation packages found on the site. Click the name of any package to finish loading:";
	$MESS["LOADER_NEW_LOAD_TITLE"] = "Download new installation package from <a href=\"http://www.bitrixsoft.com\" target=\"_blank\">http://www.bitrixsoft.com</a>";
	$MESS["LOADER_NEW_ED"] = "package edition";
	$MESS["LOADER_NEW_AUTO"] = "automatically start unpacking after loading";
	$MESS["LOADER_NEW_STEPS"] = "load gradually with interval:";
	$MESS["LOADER_NEW_STEPS0"] = "unlimited";
	$MESS["LOADER_NEW_STEPS30"] = "less than 30 seconds";
	$MESS["LOADER_NEW_STEPS60"] = "less than 60 seconds";
	$MESS["LOADER_NEW_STEPS120"] = "less than 120 seconds";
	$MESS["LOADER_NEW_STEPS180"] = "less than 180 seconds";
	$MESS["LOADER_NEW_STEPS240"] = "less than 240 seconds";
	$MESS["LOADER_NEW_LOAD"] = "Download";
	$MESS["LOADER_DESCR"] = "This script will download the trial installation package of the Bitrix Site Manager from <a href=\"http://www.bitrixsoft.com/download/index.php\" target=\"_blank\">www.bitrixsoft.com</a> directly to your site and unpack it.<br><br> Upload the script to the root directory of your site and open it in browser (type <nobr>http://&lt;your site&gt;/".$this_script_name."</nobr> in your browser).";
	$MESS["LOADER_BACK_2LIST"] = "Back to packages list";
	$MESS["LOADER_LOG_ERRORS"] = "The following errors occured:";
	$MESS["LOADER_NO_LOG"] = "Log file not found";
	$MESS["LOADER_BOTTOM_NOTE1"] = "<b><font color=\"#FF0000\">Attention!</font></b> After you have finished installing, <b>please be sure</b> to delete the script <nobr>/".$this_script_name."</nobr> from your site. Otherwise, unauthorized persons may access this script and damage your site.";
	$MESS["LOADER_KB"] = "kb";
	$MESS["LOADER_LOAD_QUERY_SERVER"] = "Connecting server...";
	$MESS["LOADER_LOAD_QUERY_DISTR"] = "Requesting package #DISTR#";
	$MESS["LOADER_LOAD_CONN2HOST"] = "Establishing connection to #HOST#...";
	$MESS["LOADER_LOAD_NO_CONN2HOST"] = "Cannot connect to #HOST#:";
	$MESS["LOADER_LOAD_QUERY_FILE"] = "Requesting file...";
	$MESS["LOADER_LOAD_WAIT"] = "Waiting for response...";
	$MESS["LOADER_LOAD_SERVER_ANSWER"] = "Error while downloading. Server reply was: #ANS#";
	$MESS["LOADER_LOAD_SERVER_ANSWER1"] = "Error while downloading. Your can not download this package. Server reply was: #ANS#";
	$MESS["LOADER_LOAD_NEED_RELOAD"] = "Cannot resume download. Starting new download.";
	$MESS["LOADER_LOAD_NO_WRITE2FILE"] = "Cannot open file #FILE# for writing";
	$MESS["LOADER_LOAD_LOAD_DISTR"] = "Downloading package #DISTR#";
	$MESS["LOADER_LOAD_ERR_SIZE"] = "File size error";
	$MESS["LOADER_LOAD_ERR_RENAME"] = "Cannot rename file #FILE1# to #FILE2#";
	$MESS["LOADER_LOAD_CANT_OPEN_WRITE"] = "Cannot open file #FILE# for writing";
	$MESS["LOADER_LOAD_CANT_OPEN_READ"] = "Cannot open file #FILE# for reading";
	$MESS["LOADER_LOAD_LOADING"] = "Download in progress. Please wait...";
	$MESS["LOADER_LOAD_FILE_SAVED"] = "File saved: #FILE# [#SIZE# bytes]";
	$MESS["LOADER_UNPACK_ACTION"] = "Unpacking the package. Please wait...";
	$MESS["LOADER_UNPACK_UNKNOWN"] = "Unknown error occured. Please try again or consult the technical support service";
	$MESS["LOADER_UNPACK_SUCCESS"] = "The installation package successfully unpacked";
	$MESS["LOADER_UNPACK_ERRORS"] = "Errors occured while unpacking the installation package";
	$MESS["LOADER_KEY_DEMO"] = "Demo version";
	$MESS["LOADER_KEY_COMM"] = "Commercial version";
	$MESS["LOADER_KEY_TITLE"] = "Specify license key";

//suffixes:
//_encode_php5.tar.gz
//_encode_php4.tar.gz
//_source.tar.gz

	$MESS_ED = array(
		"enterprise" => "Enterprise",
		"professional" => "Professional",
		"smb" => "Small Business",
		"std" => "Standard",
		"str" => "Start",
		"premium" => "Premium",
		"ultimate" => "Ultimate",
	);
}

function LoaderGetMessage($name)
{
	global $MESS;
	return $MESS[$name];
}

umask(0);
if (!defined("BX_DIR_PERMISSIONS"))
	define("BX_DIR_PERMISSIONS", 0755);

if (!defined("BX_FILE_PERMISSIONS"))
	define("BX_FILE_PERMISSIONS", 0644);

class CArchiver
{
	var $_strArchiveName = "";
	var $_bCompress = false;
	var $_strSeparator = " ";
	var $_dFile = 0;

	var $_arErrors = array();
	var $iArchSize = 0;
	var $iCurPos = 0;
	var $bFinish = false;

	function CArchiver($strArchiveName, $bCompress = false)
	{
		$this->_bCompress = false;
		if (!$bCompress)
		{
			if (@file_exists($strArchiveName))
			{
				if ($fp = @fopen($strArchiveName, "rb"))
				{
					$data = fread($fp, 2);
					fclose($fp);
					if ($data == "\37\213")
					{
						$this->_bCompress = True;
					}
				}
			}
			else
			{
				if (substr($strArchiveName, -2) == 'gz')
				{
					$this->_bCompress = True;
				}
			}
		}
		else
		{
			$this->_bCompress = True;
		}

		$this->_strArchiveName = $strArchiveName;
		$this->_arErrors = array();
	}

	function extractFiles($strPath, $vFileList = false)
	{
		$this->_arErrors = array();

		$v_result = true;
		$v_list_detail = array();

		$strExtrType = "complete";
		$arFileList = 0;
		if ($vFileList!==false)
		{
			$arFileList = &$this->_parseFileParams($vFileList);
			$strExtrType = "partial";
		}

		if ($v_result = $this->_openRead())
		{
			$v_result = $this->_extractList($strPath, $v_list_detail, $strExtrType, $arFileList, '');
			$this->_close();
		}

		return $v_result;
	}

	function &GetErrors()
	{
		return $this->_arErrors;
	}

	function _extractList($p_path, &$p_list_detail, $p_mode, $p_file_list, $p_remove_path)
	{
		global $iNumDistrFiles;

		$v_result = true;
		$v_nb = 0;
		$v_extract_all = true;
		$v_listing = false;

		$p_path = str_replace("\\", "/", $p_path);

		if ($p_path == ''
			|| (substr($p_path, 0, 1) != '/'
				&& substr($p_path, 0, 3) != "../"
				&& !strpos($p_path, ':')))
		{
			$p_path = "./".$p_path;
		}

		$p_remove_path = str_replace("\\", "/", $p_remove_path);
		if (($p_remove_path != '') && (substr($p_remove_path, -1) != '/'))
			$p_remove_path .= '/';

		$p_remove_path_size = strlen($p_remove_path);

		switch ($p_mode)
		{
			case "complete" :
				$v_extract_all = TRUE;
				$v_listing = FALSE;
				break;
			case "partial" :
				$v_extract_all = FALSE;
				$v_listing = FALSE;
				break;
			case "list" :
				$v_extract_all = FALSE;
				$v_listing = TRUE;
				break;
			default :
				$this->_arErrors[] = array("ERR_PARAM", "Invalid extract mode (".$p_mode.")");
				return false;
		}

		clearstatcache();

		$tm=time();
		while((extension_loaded("mbstring")? mb_strlen($v_binary_data = $this->_readBlock(), "latin1") : strlen($v_binary_data = $this->_readBlock())) != 0)
		{
			$v_extract_file = FALSE;
			$v_extraction_stopped = 0;

			if (!$this->_readHeader($v_binary_data, $v_header))
				return false;

			if ($v_header['filename'] == '')
				continue;

			// ----- Look for long filename
				if ($v_header['typeflag'] == 'L')
			{
				if (!$this->_readLongHeader($v_header))
					return false;
			}


			if ((!$v_extract_all) && (is_array($p_file_list)))
			{
				// ----- By default no unzip if the file is not found
				$v_extract_file = false;

				for ($i = 0; $i < count($p_file_list); $i++)
				{
					// ----- Look if it is a directory
					if (substr($p_file_list[$i], -1) == '/')
					{
						// ----- Look if the directory is in the filename path
						if ((strlen($v_header['filename']) > strlen($p_file_list[$i]))
							&& (substr($v_header['filename'], 0, strlen($p_file_list[$i])) == $p_file_list[$i]))
						{
							$v_extract_file = TRUE;
							break;
						}
					}
					elseif ($p_file_list[$i] == $v_header['filename'])
					{
						// ----- It is a file, so compare the file names
						$v_extract_file = TRUE;
						break;
					}
				}
			}
			else
			{
			  $v_extract_file = TRUE;
			}

			// ----- Look if this file need to be extracted
			if (($v_extract_file) && (!$v_listing))
			{
				if (($p_remove_path != '')
					&& (substr($v_header['filename'], 0, $p_remove_path_size) == $p_remove_path))
				{
					$v_header['filename'] = substr($v_header['filename'], $p_remove_path_size);
				}
				if (($p_path != './') && ($p_path != '/'))
				{
					while (substr($p_path, -1) == '/')
						$p_path = substr($p_path, 0, strlen($p_path)-1);

					if (substr($v_header['filename'], 0, 1) == '/')
						$v_header['filename'] = $p_path.$v_header['filename'];
					else
						$v_header['filename'] = $p_path.'/'.$v_header['filename'];
				}
				if (file_exists($v_header['filename']))
				{
					if ((@is_dir($v_header['filename'])) && ($v_header['typeflag'] == ''))
					{
						$this->_arErrors[] = array("DIR_EXISTS", "File '".$v_header['filename']."' already exists as a directory");
						return false;
					}
					if ((is_file($v_header['filename'])) && ($v_header['typeflag'] == "5"))
					{
						$this->_arErrors[] = array("FILE_EXISTS", "Directory '".$v_header['filename']."' already exists as a file");
						return false;
					}
					if (!is_writeable($v_header['filename']))
					{
						$this->_arErrors[] = array("FILE_PERMS", "File '".$v_header['filename']."' already exists and is write protected");
						return false;
					}
				}
				elseif (($v_result = $this->_dirCheck(($v_header['typeflag'] == "5" ? $v_header['filename'] : dirname($v_header['filename'])))) != 1)
				{
					$this->_arErrors[] = array("NO_DIR", "Unable to create path for '".$v_header['filename']."'");
					return false;
				}

				if ($v_extract_file)
				{
					if ($v_header['typeflag'] == "5")
					{
						if (!@file_exists($v_header['filename']))
						{
							if (!@mkdir($v_header['filename'], BX_DIR_PERMISSIONS))
							{
								$this->_arErrors[] = array("ERR_CREATE_DIR", "Unable to create directory '".$v_header['filename']."'");
								return false;
							}
						}
					}
					else
					{
						if (($v_dest_file = @fopen($v_header['filename'], "wb")) == 0)
						{
							$this->_arErrors[] = array("ERR_CREATE_FILE", "Error while opening '".$v_header['filename']."' in write binary mode");
							return false;
						}
						else
						{
							$n = floor($v_header['size']/512);
							for ($i = 0; $i < $n; $i++)
							{
								$v_content = $this->_readBlock();
								fwrite($v_dest_file, $v_content, 512);
							}
							if (($v_header['size'] % 512) != 0)
							{
								$v_content = $this->_readBlock();
								fwrite($v_dest_file, $v_content, ($v_header['size'] % 512));
							}

							@fclose($v_dest_file);

							@chmod($v_header['filename'], BX_FILE_PERMISSIONS);
							@touch($v_header['filename'], $v_header['mtime']);
						}

						clearstatcache();
						if (filesize($v_header['filename']) != $v_header['size'])
						{
							$this->_arErrors[] = array("ERR_SIZE_CHECK", "Extracted file '".$v_header['filename']."' have incorrect file size '".filesize($v_filename)."' (".$v_header['size']." expected). Archive may be corrupted");
							return false;
						}
					}
				}
				else
				{
					$this->_jumpBlock(ceil(($v_header['size']/512)));
				}
			}
			else
			{
				$this->_jumpBlock(ceil(($v_header['size']/512)));
			}

			if ($v_listing || $v_extract_file || $v_extraction_stopped)
			{
				if (($v_file_dir = dirname($v_header['filename'])) == $v_header['filename'])
					$v_file_dir = '';
				if ((substr($v_header['filename'], 0, 1) == '/') && ($v_file_dir == ''))
					$v_file_dir = '/';

				$p_list_detail[$v_nb++] = $v_header;

				if ($v_nb % 100 == 0)
					SetCurrentProgress($this->iCurPos, $this->iArchSize, False);
			}

			if ($_REQUEST['by_step'] && (time()-$tm) > 20) 
			{
				SetCurrentProgress($this->iCurPos, $this->iArchSize, False);
				return true;
			}
		}
		$this->bFinish = true;
		return true;
	}

	function _readBlock()
	{
		$v_block = "";
		if (is_resource($this->_dFile))
		{
			if (isset($_REQUEST['seek']))
			{
				fseek($this->_dFile, intval($_REQUEST['seek']));

				$this->iCurPos = IntVal($_REQUEST['seek']);

				unset($_REQUEST['seek']);
			}
			$v_block = @fread($this->_dFile, 512);

			$this->iCurPos +=  (extension_loaded("mbstring")? mb_strlen($v_block, "latin1") : strlen($v_block));
		}
		return $v_block;
	}

	function _readHeader($v_binary_data, &$v_header)
	{
		if ((extension_loaded("mbstring")? mb_strlen($v_binary_data, "latin1") : strlen($v_binary_data)) ==0)
		{
			$v_header['filename'] = '';
			return true;
		}

		if ((extension_loaded("mbstring")? mb_strlen($v_binary_data, "latin1") : strlen($v_binary_data)) != 512)
		{
			$v_header['filename'] = '';
			$this->_arErrors[] = array("INV_BLOCK_SIZE", "Invalid block size : ".strlen($v_binary_data)."");
			return false;
		}

		$v_checksum = 0;
		for ($i = 0; $i < 148; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));
		for ($i = 148; $i < 156; $i++)
			$v_checksum += ord(' ');
		for ($i = 156; $i < 512; $i++)
			$v_checksum+=ord(substr($v_binary_data, $i, 1));

		$v_data = unpack("a100filename/a8mode/a8uid/a8gid/a12size/a12mtime/a8checksum/a1typeflag/a100link/a6magic/a2version/a32uname/a32gname/a8devmajor/a8devminor/a155prefix/a12temp", $v_binary_data);

		$v_header['checksum'] = OctDec(trim($v_data['checksum']));
		if ($v_header['checksum'] != $v_checksum)
		{
			$v_header['filename'] = '';

			if (($v_checksum == 256) && ($v_header['checksum'] == 0))
				return true;

			$this->_arErrors[] = array("INV_BLOCK_CHECK", "Invalid checksum for file '".$v_data['filename']."' : ".$v_checksum." calculated, ".$v_header['checksum']." expected");
			return false;
		}

		// ----- Extract the properties
		$v_header['filename'] = trim($v_data['prefix']."/".$v_data['filename']);
		$v_header['mode'] = OctDec(trim($v_data['mode']));
		$v_header['uid'] = OctDec(trim($v_data['uid']));
		$v_header['gid'] = OctDec(trim($v_data['gid']));
		$v_header['size'] = OctDec(trim($v_data['size']));
		$v_header['mtime'] = OctDec(trim($v_data['mtime']));
		if (($v_header['typeflag'] = $v_data['typeflag']) == "5")
			$v_header['size'] = 0;

		return true;
	}

	function _readLongHeader(&$v_header)
	{
		$v_filename = '';
		$n = floor($v_header['size']/512);
		for ($i = 0; $i < $n; $i++)
		{
			$v_content = $this->_readBlock();
			$v_filename .= $v_content;
		}
		if (($v_header['size'] % 512) != 0)
		{
			$v_content = $this->_readBlock();
			$v_filename .= $v_content;
		}

		$v_binary_data = $this->_readBlock();

		if (!$this->_readHeader($v_binary_data, $v_header))
			return false;

		$v_header['filename'] = $v_filename;

		return true;
	}

	function _jumpBlock($p_len = false)
	{
		if (is_resource($this->_dFile))
		{
			if ($p_len === false)
				$p_len = 1;

			@fseek($this->_dFile, @ftell($this->_dFile)+($p_len*512));
		}
		return true;
	}

	function &_parseFileParams(&$vFileList)
	{
		if (isset($vFileList) && is_array($vFileList))
			return $vFileList;
		elseif (isset($vFileList) && strlen($vFileList)>0)
			return explode($this->_strSeparator, $vFileList);
		else
			return array();
	}

	function _openRead()
	{

		if ($this->_bCompress)
		{
			$this->_dFile = @fopen('compress.zlib://'. $this->_strArchiveName, "rb");
			$this->iArchSize = filesize($this->_strArchiveName) * 3;
		}
		else
		{
			$this->_dFile = @fopen($this->_strArchiveName, "rb");
			$this->iArchSize = filesize($this->_strArchiveName);
		}

		if (!$this->_dFile)
		{
			$this->_arErrors[] = array("ERR_OPEN", "Unable to open '".$this->_strArchiveName."' in read mode");
			return false;
		}

		return true;
	}

	function _close()
	{
		if (is_resource($this->_dFile))
		{
			@fclose($this->_dFile);

			$this->_dFile = 0;
		}

		return true;
	}

	function _dirCheck($p_dir)
	{
		if ((@is_dir($p_dir)) || ($p_dir == ''))
			return true;

		$p_parent_dir = dirname($p_dir);

		if (($p_parent_dir != $p_dir) &&
			($p_parent_dir != '') &&
			(!$this->_dirCheck($p_parent_dir)))
			return false;

		if (!is_dir($p_dir) && !mkdir($p_dir, BX_DIR_PERMISSIONS))
		{
			$this->_arErrors[] = array("CANT_CREATE_PATH", "Unable to create directory '".$p_dir."'");
			return false;
		}

		return true;
	}

}

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
<title><?= LoaderGetMessage("LOADER_TITLE") ?></title>
<meta http-equiv="Content-Type" content="text/html; charset=Windows-1251">
<style type="text/css">
	.text {font-family:Verdana,Arial, Helvetica, sans-serif; font-weight:normal; font-size:12px; color:#365069;}
	.error_text {font-family: Verdana,Arial, Helvetica, sans-serif; font-size:13px; color:#FF0000; font-weight:bold;}
	.warning_text {font-family: Verdana,Arial, Helvetica, sans-serif; font-size:13px; color:#990000; font-weight:bold;}
	.ok_text {font-family: Verdana,Arial,Helvetica,sans-serif; font-size:13px; color:#00FF00; font-weight:bold;}

	.tablehead, .tablehead1, .tablehead2, .tablehead3, .tablehead4, .tablehead5 {background-color:#C2DBED; padding:3px;}
	.tablehead1, .tablehead2, .tablehead3 {}
	.tablehead1 {}
	.tablehead3 {}
	.tablehead4, .tablehead5 {}
	.tablehead5 {}

	.tablebody, .tablebody1, .tablebody2, .tablebody3, .tablebody4 {background-color:#E2EFF7; padding:5px;}
	.tablebody1 {}
	.tablebody2 {}
	.tablebody3 {}
	.tablebody4 {}

	.tablebodytext, .tableheadtext, .tablefieldtext {font-family:Verdana,Arial, Helvetica, sans-serif; font-size:12px;}
	.tableheadtext, .tablebodytext {font-family:Verdana,Arial, Helvetica, sans-serif;color:#000000}
	.tablefieldtext {font-family:Verdana,Arial, Helvetica, sans-serif;color:#365069;}

	INPUT.button {padding:2px; font-family:Tahoma; font-size:12px; cursor: pointer;}
	INPUT.typeinput {font-size:12px;}
	.typeselect {font-family:Verdana,Arial, Helvetica, sans-serif;font-size:12px;}

	h3 {font-family:Verdana,Arial, Helvetica, sans-serif; font-size:14px; font-weight: bold; color: #585858; margin-bottom: 5px;}


	.smalltext{font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-size:10px;}
	.version{font-family:Verdana, Arial, Helvetica, sans-serif; color:#FF9933; size:18px; font-weight:bold;}
	.bitrixtitle{font-family:Verdana, Arial, Helvetica, sans-serif; color:#4083B5; size:18px; font-weight:bold;}
	.install{font-family:Verdana, Arial, Helvetica, sans-serif; size:18px; font-weight:bold;}
	.head{font-family:Verdana, Arial, Helvetica, sans-serif; font-weight:bold; color:#365069; size:18px;}
	.headbitrix{font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-size:12px; font-weight:bold;}
	.title{font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-weight:bold; font-size:16px;}
	.menu{ background-color:#E6F1F9; font-family:Verdana, Arial, Helvetica, sans-serif; color:#B4C0D0; font-size:12px; padding-left:10px; padding-right:5px;}
	.menuact{background-color:#D8E8F4; font-family:Verdana, Arial, Helvetica, sans-serif; color:#365069; font-size:12px; padding-left:10px; padding-right:5px; font-weight:bold;}
	.text11 {font-family:Verdana,Arial, Helvetica, sans-serif; font-weight:normal; color:#365069; font-size:12px; margin-bottom: 5px;}
</style>
</head>
<body link="#6C93AE" alink="#F1555A" vlink="#a4a4a4">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr height="50">
		<td width="0%" valign="bottom" style="border-left: 1px solid #D5E7F3; border-top: 1px solid #D5E7F3;"> </td>
		<td width="100%" align="center" style="background-position:left bottom; background-repeat:no-repeat; border-right: 1px solid #D5E7F3; border-left: 1px solid #D5E7F3; border-top: 1px solid #D5E7F3;"><font class="install"><?= LoaderGetMessage("LOADER_SUBTITLE1") ?></font>&nbsp;<font class="bitrixtitle">&quot;<?= LoaderGetMessage("LOADER_SUBTITLE2") ?>&quot;</font>&nbsp;</td>
	</tr>
</table>
<table width="100%"  border="0" cellspacing="1" cellpadding="0" bgcolor="#D5E7F3">
	<tr>
		<td><table width="100%" border="0" cellspacing="0" cellpadding="0" bgcolor="#F4FBFF">
				<tr>
					<td valign="top" style="border-right: 1px solid #D5E7F3;"><table width="0%" border="0" cellspacing="0" cellpadding="0">
							<tr>
								<td>
								<br>
								<table width="0%"  border="0" cellspacing="0" cellpadding="0">
									<tr>
										<td class="<?= ($strAction=="LIST" ? "menuact" : "menu")?>" width="100%" style="height: 43px;"><?= LoaderGetMessage("LOADER_MENU_LIST") ?></td>
										<td style="padding-right: 7px;"></td>
									</tr>
									<tr><td style="height: 8px;"></td></tr>
									<tr>
										<td class="<?= ($strAction=="LOAD" ? "menuact" : "menu")?>" width="100%" style="height: 43px;"><?= LoaderGetMessage("LOADER_MENU_LOAD") ?></td>
										<td style="padding-right: 7px;"></td>
									</tr>
									<tr><td style="height: 8px;"></td></tr>
									<tr>
										<td class="<?= ($strAction=="UNPACK" ? "menuact" : "menu")?>" width="100%" style="height: 43px;"><?= LoaderGetMessage("LOADER_MENU_UNPACK") ?></td>
										<td></td>
									</tr>
									<tr><td style="height: 8px;"></td></tr>
								</table>
								<p align="center" class="text" style="padding:7px;"><br><br><?= LoaderGetMessage("LOADER_TECHSUPPORT") ?></p>
								</td>
							</tr>
						</table></td>
					<td style="background-repeat:repeat-x; background-position:top; padding-bottom:25px;" align="left" valign="top">
						<div style="padding:15px">
							<p><font class="title"><?
							if ($strAction=="LIST")
								echo LoaderGetMessage("LOADER_TITLE_LIST");
							elseif ($strAction=="LOAD")
								echo LoaderGetMessage("LOADER_TITLE_LOAD");
							elseif ($strAction=="UNPACK")
								echo LoaderGetMessage("LOADER_TITLE_UNPACK");
							elseif ($strAction=="LOG")
								echo LoaderGetMessage("LOADER_TITLE_LOG");
							?></font></p>
							<font class="text">
							<?
							if ($strAction=="LIST")
							{
								/*************************************************/
								if (ini_get("safe_mode") == "1")
								{
									echo LoaderGetMessage("LOADER_SAFE_MODE_ERR");
									?>
									<br><br>
									<?
								}

								if (!is_writable($_SERVER["DOCUMENT_ROOT"]))
								{
									echo str_replace("#DIR#", $_SERVER["DOCUMENT_ROOT"], LoaderGetMessage("LOADER_NO_PERMS_ERR"));
									?>
									<br><br>
									<?
								}

								if (file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix")
									&& is_dir($_SERVER["DOCUMENT_ROOT"]."/bitrix"))
								{
									echo LoaderGetMessage("LOADER_EXISTS_ERR");
									?>
									<br><br>
									<?
								}

								$arLocalDistribs = array();
								$arLocalDistribs_tmp = array();

								$handle = @opendir($_SERVER["DOCUMENT_ROOT"]);
								if ($handle)
								{
									while (false !== ($ffile = readdir($handle)))
									{
										if (is_file($_SERVER["DOCUMENT_ROOT"]."/".$ffile))
										{
											if (strtolower(substr($ffile, -7))==".tar.gz")
											{
												$arLocalDistribs[] = $ffile;
											}
											elseif (strtolower(substr($ffile, -11))==".tar.gz.tmp")
											{
												$arLocalDistribs_tmp[] = $ffile;
											}
										}
									}
									closedir($handle);
								}

								if (count($arLocalDistribs)>0)
								{
									echo LoaderGetMessage("LOADER_IS_DISTR")
									?>
									<br><br>
									<?
									for ($i = 0; $i < count($arLocalDistribs); $i++)
									{
										?><a href="<?= $this_script_name ?>?action=UNPACK&filename=<?= urlencode($arLocalDistribs[$i]) ?>&by_step=Y"><?= $arLocalDistribs[$i] ?></a><br><?
									}
									?>
									<br><br>
									<?= LoaderGetMessage("LOADER_OVERWRITE") ?>
									<br><br><br>
									<?
								}

								if (count($arLocalDistribs_tmp)>0)
								{
									echo LoaderGetMessage("LOADER_IS_DISTR_PART")
									?>
									<br><br>
									<?
									for ($i = 0; $i < count($arLocalDistribs_tmp); $i++)
									{
										?><a href="<?= $this_script_name ?>?action=LOAD&url=<?= urlencode(substr($arLocalDistribs_tmp[$i], 0, strlen($arLocalDistribs_tmp[$i])-4)) ?>&iTimeOut=25"><?= $arLocalDistribs_tmp[$i] ?></a><br><?
									}
									?>
									<br><br><br>
									<?
								}
								?>


								<form method="post" action="<?= $this_script_name ?>?action=LOAD">
									<table border="0" cellspacing="0" cellpadding="4">
									<tr>
										<td colspan="2" style="border-left: 1px solid #D5E7F3;  border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;" align="center">
											<font class="text"><b><?= LoaderGetMessage("LOADER_NEW_LOAD_TITLE") ?></b></font>
										</td>
									</tr>
									<tr valign="top">
										<td align="right" style="border-left: 1px solid #D5E7F3;  border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text"><?= LoaderGetMessage("LOADER_LICENSE_KEY") ?>:</font>
										</td>
										<td style="border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text">
											<input type="radio" name="licence_type" value="" checked id="licence_type_1" onclick="this.form.LICENSE_KEY.disabled=this.checked"><label for="licence_type_1"><?echo LoaderGetMessage("LOADER_KEY_DEMO")?></label><br>
											<input type="radio" name="licence_type" value="" id="licence_type_2" onclick="this.form.LICENSE_KEY.disabled=!this.checked"><label for="licence_type_2"><?echo LoaderGetMessage("LOADER_KEY_COMM")?></label>
												<input type="text" name="LICENSE_KEY" size="40" disabled title="<?echo LoaderGetMessage("LOADER_KEY_TITLE")?>">
											</font>
										</td>
									</tr>
									<tr>
										<td align="right" style="border-left: 1px solid #D5E7F3;  border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text"><?= LoaderGetMessage("LOADER_NEW_ED") ?>:</font>
										</td>
										<td style="border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text">
											<select name="url">
												<?
												foreach ($MESS_ED as $key => $value)
												{
													?><option value="<?= $key ?>"><?= $value ?></option><?
												}
												?>
											</select>
											</font>
										</td>
									</tr>
									<tr>
										<td align="right" style="border-left: 1px solid #D5E7F3;  border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text"><?= LoaderGetMessage("LOADER_NEW_AUTO") ?>:</font>
										</td>
										<td style="border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text">
											<input type="checkbox" name="action_next" value="UNPACK" checked>
											</font>
										</td>
									</tr>
									<tr>
										<td align="right" style="border: 1px solid #D5E7F3;">
											<font class="text"><?= LoaderGetMessage("LOADER_NEW_STEPS") ?>:</font>
										</td>
										<td style="border-bottom: 1px solid #D5E7F3;  border-right: 1px solid #D5E7F3;  border-top: 1px solid #D5E7F3;">
											<font class="text">
											<select name="iTimeOut">
												<option value="0"><?= LoaderGetMessage("LOADER_NEW_STEPS0") ?></option>
												<option value="25"><?= LoaderGetMessage("LOADER_NEW_STEPS30") ?></option>
												<option value="55" selected><?= LoaderGetMessage("LOADER_NEW_STEPS60") ?></option>
												<option value="115"><?= LoaderGetMessage("LOADER_NEW_STEPS120") ?></option>
												<option value="175"><?= LoaderGetMessage("LOADER_NEW_STEPS180") ?></option>
												<option value="235"><?= LoaderGetMessage("LOADER_NEW_STEPS240") ?></option>
											</select>
											</font>
										</td>
									</tr>
									<tr>
										<td align="center" colspan="2">
											<font class="text">
											<input type="submit" value="<?= LoaderGetMessage("LOADER_NEW_LOAD") ?>">
											</font>
										</td>
									</tr>
									</table>
								</form>

								<br><br><br>
								<?= LoaderGetMessage("LOADER_DESCR") ?>

								<?
								/*************************************************/
							}
							elseif ($strAction=="LOAD" || $strAction=="UNPACK")
							{
								/*************************************************/
								?>
								<a href="<?= $this_script_name ?>?action=LIST">&lt;&lt;<?= LoaderGetMessage("LOADER_BACK_2LIST") ?></a><br>
								<script language="JavaScript">
								<!--
								var ns4 = (document.layers) ? true : false;
								var ie4 = (document.all) ? true : false;

								var txt = '';
								if (ns4)
								{
									txt+='<table border=0 cellpadding=0 cellspacing=0><tr><td>';
									txt+='<layer width="300" height="15" bgcolor="#365069" top="0" left="0"></layer>';
									txt+='<layer width="298" height="13" bgcolor="#ffffff" top="1" left="1"></layer>';
									txt+='<layer name="PBdone" width="298" height="13" bgcolor="#D5E7F3" top="1" left="1"></layer>';
									txt+='</td></tr></table>';
								}
								else
								{
									txt+='<div style="top:0px; left:0px; width:300; height:15px; background-color:#365069; font-size:1px;"><div style="position:relative; top:1px; left:1px; width:298px; height:13px; background-color:#ffffff; font-size:1px;"><div id="PBdone" style="position:relative; top:0px; left:0px; width:0px; height:13px; background-color:#D5E7F3; font-size:1px;"></div></div></div>';
								}
								//-->
								</script>

								<form method="post" name="status_form">
								<table>
								<tr>
									<td>
										<script language="JavaScript">
										<!--
											document.write(txt);
										//-->
										</script>
									</td>
									<td>
										<input type="text" name="progress" size="20" style="font-family: Arial; font-size: 8pt; border: 0 solid; background-color: #F4FBFF;">
									</td>
								</tr>
								<tr>
									<td colspan="2">
										<!--input type="text" name="status" size="90" style="font-family: Arial; font-size: 8pt; border: 0 solid; background-color: #F4FBFF;"-->
										<textarea name="status" rows="5" cols="90" style="overflow: visible; font-family: Arial; font-size: 8pt; border: 0 solid; background-color: #F4FBFF;"></textarea>
									</td>
								</tr>
								</table>
								</form>


								<script language="JavaScript">
								<!--
								function findlayer(name,doc)
								{
									var i,layer;
									for (i = 0; i < doc.layers.length; i++)
									{
										layer=doc.layers[i];
										if (layer.name==name)
											return layer;
										if (layer.document.layers.length>0)
											if ((layer=findlayer(name,layer.document))!=null)
												return layer;
									}
									return null;
								}

								var PBdone = (ns4) ? findlayer('PBdone', document) : (ie4) ? document.all['PBdone'] : document.getElementById('PBdone');

								function SetProgr(val)
								{
									if (ns4)
									{
										PBdone.clip.left = 0;
										PBdone.clip.top = 0;
										PBdone.clip.right = val*298/100;
										PBdone.clip.bottom = 13;
									}
									else
										PBdone.style.width = (val*298/100) + 'px';
								}
								//-->
								</script>
								<?
								/*************************************************/
							}
							elseif ($strAction=="LOG")
							{
								/*************************************************/
								?>
								<a href="<?= $this_script_name ?>?action=LIST">&lt;&lt;<?= LoaderGetMessage("LOADER_BACK_2LIST") ?></a><br>
								<?
								if (file_exists($_SERVER["DOCUMENT_ROOT"]."/".$this_script_name.".log")
									&& ($ft = fopen($_SERVER["DOCUMENT_ROOT"]."/".$this_script_name.".log", "rb")))
								{
									?><br><?= LoaderGetMessage("LOADER_LOG_ERRORS") ?><br><br><?
									while (!feof($ft))
									{
										$buffer = fgets($ft, 4096);
										echo $buffer."<br>";
									}
									fclose($ft);
								}
								else
								{
									echo LoaderGetMessage("LOADER_NO_LOG");
								}
								/*************************************************/
							}
							?>
							<br><br>
							<?= LoaderGetMessage("LOADER_BOTTOM_NOTE1") ?>
							</font>
						</div>
					</td>
				</tr>
			</table></td>
	</tr>
</table>
</body>
</html>
<?
if ($strAction=="LOAD" || $strAction=="UNPACK")
{
	function SetCurrentStatus($text)
	{
		$text = preg_replace("/[\s\n\r]+/", " ", $text);
		echo "<script>document.status_form.status.value=\"$text\";</script>\n";
		flush();	
	}

	function SetCurrentProgress($val, $strRequestedSize = 0, $ShowSize = True)
	{
		$val = IntVal($val);
		if ($val>0)
		{
			$iProc = $val/$strRequestedSize*100;
			if ($ShowSize)
				$rVal = sprintf("%01.1f", Round($val/1000.0, 1))." ".LoaderGetMessage("LOADER_KB");
			else
				$rVal = $val;
			echo "<script>document.status_form.progress.value=\"".$rVal."\";SetProgr(".IntVal($iProc).");</script>\n";
		}
		else
		{
			echo "<script>document.status_form.progress.value=\"\";SetProgr(0);</script>\n";
		}
		flush();
	}

	function getmicrotime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}
}

if ($strAction=="LOAD")
{
	/*********************************************************************/

	if($lang=="ru")
		$site = "http://www.1c-bitrix.ru/";
	else
		$site = "http://www.bitrixsoft.com/";

	if($_REQUEST['LICENSE_KEY'] <> '')
	{
		$path = 'private/download/';
		$suffix = '_source.tar.gz';
	}
	else
	{
		$path = 'download/';
		if(version_compare(phpversion(), '5.0.0') == -1)
			$suffix = '_encode_php4.tar.gz';
		else
			$suffix = '_encode_php5.tar.gz';
	}
		
	$strRequestedUrl = $site.$path.$_REQUEST["url"].$suffix;

	$strRequestedSize = 10000000.0;
	$iTimeOut = IntVal($_REQUEST["iTimeOut"]);

	$proxyaddr = "";
	$proxyport = "";

	$strUserAgent = "BitrixSiteLoader";
	$strFilename = $_SERVER["DOCUMENT_ROOT"]."/".basename($strRequestedUrl);

	function LoadFile($strRequestedUrl, $strFilename, $iTimeOut = 0)
	{
		global $proxyaddr, $proxyport, $strUserAgent, $strRequestedSize;

		$iTimeOut = IntVal($iTimeOut);
		if ($iTimeOut>0)
			$start_time = getmicrotime();

		$strRealUrl = $strRequestedUrl;
		$iStartSize = 0;
		$iRealSize = 0;

		$bCanContinueDownload = False;

		// ИНИЦИАЛИЗИРУЕМ, ЕСЛИ ДОКАЧКА
		$strRealUrl_tmp = "";
		$iRealSize_tmp = 0;
		if (file_exists($strFilename.".tmp") && file_exists($strFilename.".log"))
		{
			$fh = fopen($strFilename.".log", "rb");
			$file_contents_tmp = fread($fh, filesize($strFilename.".log"));
			fclose($fh);

			list($strRealUrl_tmp, $iRealSize_tmp) = split("\n", $file_contents_tmp);
			$strRealUrl_tmp = Trim($strRealUrl_tmp);
			$iRealSize_tmp = IntVal(Trim($iRealSize_tmp));
		}
		if ($iRealSize_tmp<=0 || strlen($strRealUrl_tmp)<=0)
		{
			$strRealUrl_tmp = "";
			$iRealSize_tmp = 0;

			if (file_exists($strFilename.".tmp"))
				@unlink($strFilename.".tmp");

			if (file_exists($strFilename.".log"))
				@unlink($strFilename.".log");
		}
		else
		{
			$strRealUrl = $strRealUrl_tmp;
			$iRealSize = $iRealSize_tmp;
			$iStartSize = filesize($strFilename.".tmp");
		}
		// КОНЕЦ: ИНИЦИАЛИЗИРУЕМ, ЕСЛИ ДОКАЧКА

		SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_SERVER"));

		// ИЩЕМ ФАЙЛ И ЗАПРАШИВАЕМ ИНФО
		do
		{
			SetCurrentStatus(str_replace("#DISTR#", $strRealUrl, LoaderGetMessage("LOADER_LOAD_QUERY_DISTR")));

			$lasturl = $strRealUrl;
			$redirection = "";

			$parsedurl = parse_url($strRealUrl);
			$useproxy = (($proxyaddr != "") && ($proxyport != ""));

			if (!$useproxy)
			{
				$host = $parsedurl["host"];
				$port = $parsedurl["port"];
				$hostname = $host;
			}
			else
			{
				$host = $proxyaddr;
				$port = $proxyport;
				$hostname = $parsedurl["host"];
			}

			$port = $port ? $port : "80";

			SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_CONN2HOST")));
			$sockethandle = @fsockopen($host, $port, $error_id, $error_msg, 30);
			if (!$sockethandle)
			{
				SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_NO_CONN2HOST"))." [".$error_id."] ".$error_msg);
				return false;
			}
			else
			{
				if (!$parsedurl["path"])
					$parsedurl["path"] = "/";

				SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_FILE"));
				$request = "";
				if (!$useproxy)
				{
					$request .= "HEAD ".$parsedurl["path"].($parsedurl["query"] ? '?'.$parsedurl["query"] : '')." HTTP/1.0\r\n";
					$request .= "Host: $hostname\r\n";
				}
				else
				{
					$request .= "HEAD ".$strRealUrl." HTTP/1.0\r\n";
					$request .= "Host: $hostname\r\n";
				}

				if ($strUserAgent != "")
					$request .= "User-Agent: $strUserAgent\r\n";

				$request .= "\r\n";

				fwrite($sockethandle, $request);

				$result = "";
				SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_WAIT"));

				$replyheader = "";
				while (($result = fgets($sockethandle, 4096)) && $result!="\r\n")
				{
					$replyheader .= $result;
				}
				fclose($sockethandle);

				$ar_replyheader = split("\r\n", $replyheader);

				$replyproto = "";
				$replyversion = "";
				$replycode = 0;
				$replymsg = "";
				if (ereg("([A-Z]{4})/([0-9.]{3}) ([0-9]{3})", $ar_replyheader[0], $regs))
				{
					$replyproto = $regs[1];
					$replyversion = $regs[2];
					$replycode = IntVal($regs[3]);
					$replymsg = substr($ar_replyheader[0], strpos($ar_replyheader[0], $replycode) + strlen($replycode) + 1, strlen($ar_replyheader[0]) - strpos($ar_replyheader[0], $replycode) + 1);
				}

				if ($replycode!=200 && $replycode!=302)
				{
					if ($replycode==403)
						SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER1")));
					else
						SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER")));
					return false;
				}

				$strLocationUrl = "";
				$iNewRealSize = 0;
				$strAcceptRanges = "";
				for ($i = 1; $i < count($ar_replyheader); $i++)
				{
					if (strpos($ar_replyheader[$i], "Location") !== false)
						$strLocationUrl = trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
					elseif (strpos($ar_replyheader[$i], "Content-Length") !== false)
						$iNewRealSize = IntVal(Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1)));
					elseif (strpos($ar_replyheader[$i], "Accept-Ranges") !== false)
						$strAcceptRanges = Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
				}

				if (strlen($strLocationUrl)>0)
				{
					$redirection = $strLocationUrl;
					$redirected = true;
					if ((strpos($redirection, "http://")===false))
						$strRealUrl = dirname($lasturl)."/".$redirection;
					else
						$strRealUrl = $redirection;
				}

				if (strlen($strLocationUrl)<=0)
					break;
			}
		}
		while (true);
		// КОНЕЦ: ИЩЕМ ФАЙЛ И ЗАПРАШИВАЕМ ИНФО

		$bCanContinueDownload = ($strAcceptRanges == "bytes");

		// ЕСЛИ НЕЛЬЗЯ ДОКАЧИВАТЬ
		if (!$bCanContinueDownload
			|| $iNewRealSize != $iRealSize)
		{
			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_NEED_RELOAD"));
			$iStartSize = 0;
		}
		// КОНЕЦ: ЕСЛИ НЕЛЬЗЯ ДОКАЧИВАТЬ

		// ЕСЛИ МОЖНО ДОКАЧИВАТЬ
		if ($bCanContinueDownload)
		{
			$fh = fopen($strFilename.".log", "wb");
			if (!$fh)
			{
				SetCurrentStatus(str_replace("#FILE#", $strFilename.".log", LoaderGetMessage("LOADER_LOAD_NO_WRITE2FILE")));
				return false;
			}
			fwrite($fh, $strRealUrl."\n");
			fwrite($fh, $iNewRealSize."\n");
			fclose($fh);
		}
		// КОНЕЦ: ЕСЛИ МОЖНО ДОКАЧИВАТЬ

		SetCurrentStatus(str_replace("#DISTR#", $strRealUrl, LoaderGetMessage("LOADER_LOAD_LOAD_DISTR")));
		$strRequestedSize = $iNewRealSize;

		// КАЧАЕМ ФАЙЛ
		$parsedurl = parse_url($strRealUrl);
		$useproxy = (($proxyaddr != "") && ($proxyport != ""));

		if (!$useproxy)
		{
			$host = $parsedurl["host"];
			$port = $parsedurl["port"];
			$hostname = $host;
		}
		else
		{
			$host = $proxyaddr;
			$port = $proxyport;
			$hostname = $parsedurl["host"];
		}

		$port = $port ? $port : "80";

		SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_CONN2HOST")));
		$sockethandle = @fsockopen($host, $port, $error_id, $error_msg, 30);
		if (!$sockethandle)
		{
			SetCurrentStatus(str_replace("#HOST#", $host, LoaderGetMessage("LOADER_LOAD_NO_CONN2HOST"))." [".$error_id."] ".$error_msg);
			return false;
		}
		else
		{
			if (!$parsedurl["path"])
				$parsedurl["path"] = "/";

			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_QUERY_FILE"));

			$request = "";
			if (!$useproxy)
			{
				$request .= "GET ".$parsedurl["path"].($parsedurl["query"] ? '?'.$parsedurl["query"] : '')." HTTP/1.0\r\n";
				$request .= "Host: $hostname\r\n";
			}
			else
			{
				$request .= "GET ".$strRealUrl." HTTP/1.0\r\n";
				$request .= "Host: $hostname\r\n";
			}

			if ($strUserAgent != "")
				$request .= "User-Agent: $strUserAgent\r\n";

			if ($bCanContinueDownload && $iStartSize>0)
				$request .= "Range: bytes=".$iStartSize."-\r\n";

			$request .= "\r\n";

			fwrite($sockethandle, $request);

			$result = "";
			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_WAIT"));

			$replyheader = "";
			while (($result = fgets($sockethandle, 4096)) && $result!="\r\n")
				$replyheader .= $result;

			$ar_replyheader = split("\r\n", $replyheader);

			$replyproto = "";
			$replyversion = "";
			$replycode = 0;
			$replymsg = "";
			if (ereg("([A-Z]{4})/([0-9.]{3}) ([0-9]{3})", $ar_replyheader[0], $regs))
			{
				$replyproto = $regs[1];
				$replyversion = $regs[2];
				$replycode = IntVal($regs[3]);
				$replymsg = substr($ar_replyheader[0], strpos($ar_replyheader[0], $replycode) + strlen($replycode) + 1, strlen($ar_replyheader[0]) - strpos($ar_replyheader[0], $replycode) + 1);
			}

			if ($replycode!=200 && $replycode!=302 && $replycode!=206)
			{
				SetCurrentStatus(str_replace("#ANS#", $replycode." - ".$replymsg, LoaderGetMessage("LOADER_LOAD_SERVER_ANSWER")));
				return false;
			}

			$strContentRange = "";
			$iContentLength = 0;
			$strAcceptRanges = "";
			for ($i = 1; $i < count($ar_replyheader); $i++)
			{
				if (strpos($ar_replyheader[$i], "Content-Range") !== false)
					$strContentRange = trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
				elseif (strpos($ar_replyheader[$i], "Content-Length") !== false)
					$iContentLength = IntVal(Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1)));
				elseif (strpos($ar_replyheader[$i], "Accept-Ranges") !== false)
					$strAcceptRanges = Trim(substr($ar_replyheader[$i], strpos($ar_replyheader[$i], ":") + 1, strlen($ar_replyheader[$i]) - strpos($ar_replyheader[$i], ":") + 1));
			}

			$bReloadFile = True;
			if (strlen($strContentRange)>0)
			{
				if (eregi(" *bytes +([0-9]*) *- *([0-9]*) */ *([0-9]*)", $strContentRange, $regs))
				{
					$iStartBytes_tmp = IntVal($regs[1]);
					$iEndBytes_tmp = IntVal($regs[2]);
					$iSizeBytes_tmp = IntVal($regs[3]);

					if ($iStartBytes_tmp==$iStartSize
						&& $iEndBytes_tmp==($iNewRealSize-1)
						&& $iSizeBytes_tmp==$iNewRealSize)
					{
						$bReloadFile = False;
					}
				}
			}

			if ($bReloadFile)
				$iStartSize = 0;

			if (($iContentLength+$iStartSize)!=$iNewRealSize)
			{
				SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_ERR_SIZE"));
				return false;
			}

			if (!$bReloadFile)
			{
				@unlink($strFilename.".tmp1");

				if (!@rename($strFilename.".tmp", $strFilename.".tmp1"))
				{
					SetCurrentStatus(str_replace("#FILE2#", $strFilename.".tmp1", str_replace("#FILE1#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_ERR_RENAME"))));
					return false;
				}
			}

			$fh = fopen($strFilename.".tmp", "wb");
			if (!$fh)
			{
				SetCurrentStatus(str_replace("#FILE#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_CANT_OPEN_WRITE")));
				return false;
			}

			if (!$bReloadFile)
			{
				$fh1 = fopen($strFilename.".tmp1", "rb");
				if (!$fh1)
				{
					SetCurrentStatus(str_replace("#FILE#", $strFilename.".tmp1", LoaderGetMessage("LOADER_LOAD_CANT_OPEN_READ")));
					return false;
				}

				do
				{
					$data = fread($fh1, 8192);
					if (strlen($data) == 0)
						 break;
					fwrite($fh, $data);
				}
				while (true);

				fclose($fh1);
				@unlink($strFilename.".tmp1");
			}

			$iCntr = 0;
			$bFinished = True;
			$downloadsize = $iStartSize;
			SetCurrentStatus(LoaderGetMessage("LOADER_LOAD_LOADING"));
			while (!feof($sockethandle))
			{
				if ($iTimeOut>0 && (getmicrotime()-$start_time)>$iTimeOut)
				{
					$bFinished = False;
					break;
				}
//if ($iCntr % 10 == 0)
//	sleep(1);
				if ($iCntr % 20 == 0)
					SetCurrentProgress($downloadsize, $strRequestedSize);

				$result = fread($sockethandle, 40960);
				$downloadsize += strlen($result);
				if ($result=="")
					break;

				$iCntr++;
				fwrite($fh, $result);
			}
			SetCurrentProgress(0);

			fclose($fh);
			fclose($sockethandle);

			if ($bFinished)
			{
				@unlink($strFilename);
				if (!@rename($strFilename.".tmp", $strFilename))
				{
					SetCurrentStatus(str_replace("#FILE2#", $strFilename, str_replace("#FILE1#", $strFilename.".tmp", LoaderGetMessage("LOADER_LOAD_ERR_RENAME"))));
					return false;
				}
				@unlink($strFilename.".tmp");
			}
			else
			{
				return 2;
			}

			SetCurrentStatus(str_replace("#SIZE#", $downloadsize, str_replace("#FILE#", $strFilename, LoaderGetMessage("LOADER_LOAD_FILE_SAVED"))));
			@unlink($strFilename.".log");
			return 1;
		}
		// КОНЕЦ: КАЧАЕМ ФАЙЛ
	}

	$vLoadRes = LoadFile($strRequestedUrl.($_REQUEST["LICENSE_KEY"]<>''? "?lp=".md5($_REQUEST["LICENSE_KEY"]):''), $strFilename, $iTimeOut);
	if ($vLoadRes==1)
	{
		if ($_REQUEST["action_next"]=="UNPACK")
		{
			echo "<script>window.location = \"".$this_script_name."?action=UNPACK&by_step=Y&filename=".urlencode(basename($strRequestedUrl))."&xz=".rand(0, 32000)."\";</script>\n";
			flush();	
		}
	}
	elseif ($vLoadRes==2)
	{
		echo "<script>window.location = \"".$this_script_name."?action=LOAD&url=".urlencode($_REQUEST["url"])."&LICENSE_KEY=".urlencode($_REQUEST["LICENSE_KEY"])."&iTimeOut=".$iTimeOut."&action_next=".urlencode($_REQUEST["action_next"])."&xz=".rand(0, 32000)."\";</script>\n";
		flush();
	}
	/*********************************************************************/
}
elseif ($strAction=="UNPACK")
{
	/*********************************************************************/
//	$iNumDistrFiles = 8000;

	SetCurrentStatus(LoaderGetMessage("LOADER_UNPACK_ACTION"));
	$oArchiver = new CArchiver($_SERVER["DOCUMENT_ROOT"]."/".$_REQUEST["filename"], true);
	$tres = $oArchiver->extractFiles($_SERVER["DOCUMENT_ROOT"]);
	SetCurrentProgress($oArchiver->iCurPos, $oArchiver->iArchSize, False);
	if ($tres)
	{
		if (!$oArchiver->bFinish)
			echo "<script>window.location = \"".$this_script_name."?action=UNPACK&filename=".urlencode(basename($oArchiver->_strArchiveName))."&by_step=Y&seek=".$oArchiver->iCurPos."\";</script>\n";
		else // finish
		{
			unlink($_SERVER["DOCUMENT_ROOT"]."/".$_REQUEST["filename"]);
			$strInstFile = "index.php";
			if (!file_exists($_SERVER["DOCUMENT_ROOT"]."/".$strInstFile))
			{
				SetCurrentStatus(LoaderGetMessage("LOADER_UNPACK_UNKNOWN"));
			}
			else
			{
				SetCurrentStatus(LoaderGetMessage("LOADER_UNPACK_SUCCESS"));
				echo "<script>window.location = \"/".$strInstFile."\";</script>";
				flush();	
			}
		}
	}
	else
	{
		SetCurrentStatus(LoaderGetMessage("LOADER_UNPACK_ERRORS"));
		$arErrors = &$oArchiver->GetErrors();
		if (count($arErrors)>0)
		{
			if ($ft = fopen($_SERVER["DOCUMENT_ROOT"]."/".$this_script_name.".log", "wb"))
			{
				foreach ($arErrors as $value)
					fwrite($ft, "[".$value[0]."] ".$value[1]."\n");

				fclose($ft);
				echo "<script>window.location = \"".$this_script_name."?action=LOG&xz=".rand(0, 32000)."\";</script>";
				flush();	
			}
		}
	}
	/*********************************************************************/
}
?>
