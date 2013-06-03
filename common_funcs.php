<?php
function account_namevalid($login) {

  // должны иметь по крайней мере один символ 
  if (strspn($login, 
"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-") == 0) { 
    return false; 
  }
  
  // должна содержать все допустимые символы 
  if (strspn($login,
"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_") 
 != strlen($login)) {
    return false;
  }

  // минимальная и максимальная длина 
  if (strlen($login) < 5) {
    return false; 
  }
  if (strlen($login) > 25) {
    return false;
  }

  // Запрещённый логины для регистрации 
  if (preg_match("/^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)|(uucp) 
|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian) 
|(ns)|(download))$/i", $login)) {
    return false;
  } 
  if (preg_match("/^(anoncvs_)/", $login)) { 
    return false; 
  } 

return true; 
} 

function account_firstlastvalid($firstlast) {

  // должны иметь по крайней мере один символ 
  if (strspn($firstlast, 
"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789абвгдеёжзийклмнопрстуфхцчшщьыъэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯ") == 0) { 
    return false;
  }
  
  // должна содержать все допустимые символы 
  if (strspn($firstlast,
"abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-_абвгдеёжзийклмнопрстуфхцчшщьыъэюяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЬЫЪЭЮЯ") 
 != strlen($firstlast)) {
    return false;
  }

  // минимальная и максимальная длина 
  if (strlen($firstlast) < 3) {
    return false; 
  }
  if (strlen($firstlast) > 25) {
    return false;
  }

  // Запрещённый логины для регистрации 
  if (preg_match("/^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)|(uucp) 
|(operator)|(games)|(mysql)|(httpd)|(nobody)|(dummy)|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian) 
|(ns)|(download))$/i", $firstlast)) {
    return false;
  } 
  if (preg_match("/^(anoncvs_)/", $firstlast)) { 
    return false; 
  } 

return true; 
} 


function validate_email($email) { 
   if(preg_match("|^[-0-9a-z_\.]+@[-0-9a-z_^\.]+\.[a-z]{2,6}$|i", $email)) 
           { 
             return $email; 
           }
}

function pass_convertion($password)
{
	$converted    = md5($password);//шифруем пароль
	$converted    = strrev($converted);// для надежности добавим реверс
	$converted    = $converted."b3p6f";
	
	return $converted;
}


function GetRealIp()
{
 if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
 {
   $ip=$_SERVER['HTTP_CLIENT_IP'];
 }
 elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
 {
  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
 }
 else
 {
   $ip=$_SERVER['REMOTE_ADDR'];
 }
 return $ip;
}

function createphoto ($w, $input,$output)
{
	$q = 100;  // качество jpeg по умолчанию
	$f=$input;
	$src = imagecreatefromjpeg($f);
	// функция imagecreatefromjpeg создает изображение JPEG из файла
	// т.е. создаём исходное изображение на основе исходного файла и определяем его размеры
	$w_src = imagesx($src);
	$h_src= imagesy($src);
	// получение ширины и высоты изображения в пикселях
	$ratio = $w_src/$w;
	$w_dest = round($w_src/$ratio);
	$h_dest = round($h_src/$ratio);
	// получение координат для построения нового изображения необходимой нам ширины
	$dest = imagecreatetruecolor($w_dest,$h_dest);
	// функция  imagecreatetruecolor пустое полноцветное изображение размерами x_size и y_size.
	// Созданное изображение имеет черный фон.
	imagecopyresized($dest, $src, 0, 0, 0, 0, $w_dest, $h_dest, $w_src, $h_src);
	// Функция imagecopyresized копирует прямоугольные области с одного изображения на другое
	// вывод картинки и очистка памяти
	imagejpeg($dest,$output,$q);
	imagedestroy($dest);
	imagedestroy($src);
}

function load_image($image)
{
	$valid_types =  array("gif","jpg", "png", "jpeg","GIF","JPG", "PNG", "JPEG");
	// создаем главную рабочую директорию =============================================
	$dir="avatars/";
	if (!is_dir($dir)) 
	{
		mkdir($dir,0755);
		// создали папку gallery в корне нашего сайта и установили права на чтение и запись
	}

			// первая проверка на наличие загружаемого файла
			$ext = substr($image['name'], 1 + strrpos($image['name'], "."));
			//получаем расширение загружаемого файла
			if (in_array ($ext, $valid_types))
			{
				$imageinfo = getimagesize($image["tmp_name"]);
				if($imageinfo["mime"] != "image/gif" || $imageinfo["mime"] != "image/jpeg" || $imageinfo["mime"] !="image/png" || $imageinfo["mime"] !="image/pjpeg")
					throw new Exception("Недопустимый формат файла");
				// сверяемся с массивом допустимых расширений и если совпадение найдено продолжаем работать
				// если нет - выводим сообщение об ошибке
				
				// получаем информацию о загруженном файле
				// функция getimagesize позволяет получить размер изображения в пикселях, а также mime-тип загруженного файла
				
					// проверяем действительно ли загрузенный файл является рисунком, и если все правильно продолжаем работу
					// такая проверка необходима для того, чтобы не было скрытой загрузки вредоносного исполняемого файла
					// т.е. банальной смены расширения php на jpg и попытке загрузки его на сервер

					$apend=date('YmdHis').rand(100,1000);
					// это имя, которое будет присвоенно изображению
					$output=$apend.".".$ext;
					// новое полное имя файла (добавили расширение к имени)
					$input=$_FILES['fupload']['tmp_name'];
					// временный файл который создается автоматически при загрузке изображения
					createphoto(200,$input,$dir.$output);
					// вызов функции по работе с изображением.
					// передаем два параметра: имя исходного изображения и то, которое нужно получить
					if (file_exists($dir.$output)) 
					{
						// проверка на существование загруженного файла
						return $dir.$output;
					}
				}
		return "avatars/lfooto.png";
}

function update_user($row)
{
	global $mysql;
	

	
	if(isset ($_POST['submit']) && $_POST['submit'] == 'Отправить')
	{
		if(isset($_POST['email']) && ($_POST['email'] != $row['email']))  //ИСПРАВИТЬ kогин не должен повторяться у пользователей!
		{
			if(account_namevalid($_POST['email']) == true)
			{
				$result = $mysql->query("SELECT count(*) FROM users WHERE email=?s",$_POST['email']);
				if(mysqli_num_rows($result) < 1)
				{
					$row['email'] = $_POST['email'];
					$args["email"] = htmlspecialchars($_POST['email']);
				}
				else
					throw new Exception("E-mail пользователя уже используется!");
			}
			else
				throw new Exception("Логин пользователя является недопустимым");
		}
		
		if(isset($_POST['first_name']) && ($_POST['first_name'] != $row['first_name']))
		{
			if(account_firstlastvalid($_POST['first_name']) == true)
			{
				$row['first_name'] = $_POST['first_name'];
				$args["first_name"] = htmlspecialchars($_POST['first_name']);
			}
			else
				throw new Exception("Имя пользователя является недопустимым");
		}
		
		if(isset($_POST['last_name']) && ($_POST['last_name'] != $row['last_name']))
		{
			echo"Работает";
			if (account_firstlastvalid($_POST['last_name']) == true) 
			{
				$row['last_name'] = $_POST['last_name'];
				$args["last_name"] = htmlspecialchars($_POST['last_name']);
			}
			else
				throw new Exception("Фамилия пользователя является недопустимой");
		}
		if(!empty($_POST['fupload']))
		{
			echo'fupload';
			$avatar = load_image($_FILES['fupload']);
			$args["avatar"] = $avatar;
			if($avatar != "avatars/lfooto.png")
			{
				$del_avatar=$mysql->getAll("SELECT avatar from users WHERE id=?i AND confirm_hash=?s AND INET_ATON(?s)",$_COOKIE['id'], $_COOKIE['hash'],$_COOKIE['ip']);
				unlink($del_avatar);
			}
		}
		if(isset($_POST['old_password']) && !empty($_POST['old_password']))
		{
			if(empty($_POST['old_password']) && empty($_POST['new_password1']) && empty($_POST['new_password2']))
				throw new Exception("Заполните поля паролей");
			if(strlen($_POST['new_password1']) <=5 && strlen($_POST['new_password1']) >= 32)
				throw new Exception( 'Пароль не должен быть длинее 32 символов и короче 5 символов');
			if($_POST['new_password1'] != $_POST['new_password2'])
				throw new Exception("Пароль не изменён, так как новый пароль повторен неправильно.");
			
			$result = $mysql->query("SELECT count(*) FROM users WHERE id=?s AND confirm_hash=?s AND remote_addr=INET_ATON(?s) AND password=?s",$_COOKIE['id'], $_COOKIE['hash'],$_COOKIE['ip'], pass_convertion($_POST['old_password']));
				if(mysqli_num_rows($result) < 1)
					throw new Exception("Пароль не изменён, так как прежний пароль введён неправильно.");
			
			$args["password"] = pass_convertion($_POST['new_password1']);
		}
	}
	
	
	if(!empty($args))
	{
		$result = $mysql->query("UPDATE users SET ?u WHERE id=?i AND confirm_hash=?s AND remote_addr=INET_ATON(?s)",$args,$_COOKIE['id'], $_COOKIE['hash'], $_COOKIE['ip']);
		if(!$result)
			throw new Exception("Ошибка обновления данных пользователя");
	}
	
	return $row;
}

// Функция обработки bbCode
function print_page($message)
{
	// Разрезаем слишком длинные слова
    $message = preg_replace_callback(
              "|([a-zа-я\d!]{35,})|i",
              "split_text",
              $message);
			  
  // Тэги - [code], [php], [sql]
  preg_match_all( "#\[php\](.+)\[\/php\]#isU", $message, $matches );
  $cnt = count( $matches[0] );
  for ( $i = 0; $i < $cnt; $i++ ) {
    $phpBlocks[] = '<div class="codePHP">'.highlight_string( $matches[1][$i], true ).'</div>';
    // Вот над этим надо будет подумать - усовершенствовать рег. выражение
    // $phpBlocks[$i] = str_replace( '<div class="codePHP"><br />', '<div class="codePHP">', $phpBlocks[$i] );
    $uniqidPHP = '[php_'.uniqid('').']';
    $uniqidsPHP[] = $uniqidPHP;
    $message = str_replace( $matches[0][$i], $uniqidPHP, $message ); 
  }

  $spaces = array( ' ', "\t" );
  $entities = array( '&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;' );
	
  preg_match_all( "#\[code\](.+)\[\/code\]#isU", $message, $matches );
  $cnt = count( $matches[0] );

  for ( $i = 0; $i < $cnt; $i++ ) {
	$codeBlocks[] = '<div class="code">'.nl2br( str_replace( $spaces, $entities, htmlspecialchars( $matches[1][$i] ) ) ).'</div>';
	// Вот над этим надо будет подумать - усовершенствовать рег. выражение
	$codeBlocks[$i] = str_replace( '<div class="code"><br />', '<div class="code">', $codeBlocks[$i] );
	$uniqidCode = '[code_'.uniqid('').']';
	$uniqidsCode[] = $uniqidCode;
    $message = str_replace( $matches[0][$i], $uniqidCode, $message ); 
  }
	
  preg_match_all( "#\[sql\](.+)\[\/sql\]#isU", $message, $matches );
  $cnt = count( $matches[0] );
  for ( $i = 0; $i < $cnt; $i++ ) {
    $sqlBlocks[] = '<div class="codeSQL">'.highlight_sql( $matches[1][$i] ).'</div>';
    // Вот над этим надо будет подумать - усовершенствовать рег. выражение
    $sqlBlocks[$i] = str_replace( '<div class="codeSQL"><br />', '<div class="codeSQL">', $sqlBlocks[$i] );
    $uniqidSQL = '[sql_'.uniqid('').']';
    $uniqidsSQL[] = $uniqidSQL;
    $message = str_replace( $matches[0][$i], $uniqidSQL, $message ); 
  }

  preg_match_all( "#\[js\](.+)\[\/js\]#isU", $message, $matches );
  $cnt = count( $matches[0] );
  for ( $i = 0; $i < $cnt; $i++ ) {
    $jsBlocks[] = '<div class="codeJS">'.geshi_highlight($matches[1][$i], 'javascript', '', true).'</div>';
    // Вот над этим надо будет подумать - усовершенствовать рег. выражение
    $jsBlocks[$i] = str_replace( '<div class="codeJS"><code><br />', '<div class="codeJS"><code>', $jsBlocks[$i] );
    $uniqidJS = '[js_'.uniqid('').']';
    $uniqidsJS[] = $uniqidJS;
    $message = str_replace( $matches[0][$i], $uniqidJS, $message ); 
  } 
	
  preg_match_all( "#\[css\](.+)\[\/css\]#isU", $message, $matches );
  $cnt = count( $matches[0] );
  for ( $i = 0; $i < $cnt; $i++ ) {
    $cssBlocks[] = '<div class="codeCSS">'.geshi_highlight($matches[1][$i], 'css', '', true).'</div>';
    // Вот над этим надо будет подумать - усовершенствовать рег. выражение
    $cssBlocks[$i] = str_replace( '<div class="codeCSS"><code><br />', '<div class="codeCSS"><code>', $cssBlocks[$i] );
    $uniqidCSS = '[css_'.uniqid('').']';
    $uniqidsCSS[] = $uniqidCSS;
    $message = str_replace( $matches[0][$i], $uniqidCSS, $message ); 
  } 

  preg_match_all( "#\[html\](.+)\[\/html\]#isU", $message, $matches );
  $cnt = count( $matches[0] );
  for ( $i = 0; $i < $cnt; $i++ ) {
    $htmlBlocks[] = '<div class="codeHTML">'.geshi_highlight($matches[1][$i], 'html4strict', '', true).'</div>';
    // Вот над этим надо будет подумать - усовершенствовать рег. выражение
    $htmlBlocks[$i] = str_replace( '<div class="codeHTML"><br />', '<div class="codeHTML">', $htmlBlocks[$i] );
    $uniqidHTML = '[html_'.uniqid('').']';
    $uniqidsHTML[] = $uniqidHTML;
    $message = str_replace( $matches[0][$i], $uniqidHTML, $message ); 
  }	
	/*
preg_match_all( "#\[img\][\s]*([\S]+)[\s]*\[\/img\]#isU", $message, $matches );
foreach ( $matches[0] as $src ) {
  $img = file_get_contents( $src );
  file_put_contents( );
}
*/
	
  $message = htmlspecialchars( $message );
  $message = preg_replace("#\[b\](.+)\[\/b\]#isU", '<b>\\1</b>', $message);
  $message = preg_replace("#\[i\](.+)\[\/i\]#isU", '<i>\\1</i>', $message);
  $message = preg_replace("#\[u\](.+)\[\/u\]#isU", '<u>\\1</u>', $message);
  $message = preg_replace("#\[quote\](.+)\[\/quote\]#isU",'<div class="quoteHead">Цитата</div><div class="quoteContent">\\1</div>',$message);
  $message = preg_replace("#\[quote=&quot;([- 0-9a-zа-яА-Я]{1,30})&quot;\](.+)\[\/quote\]#isU", '<div class="quoteHead">\\1 пишет:</div><div class="quoteContent">\\2</div>', $message);
  $message = preg_replace("#\[url\][\s]*([\S]+)[\s]*\[\/url\]#isU",'<a href="\\1" target="_blank">\\1</a>',$message);
  $message = preg_replace("#\[url[\s]*=[\s]*([\S]+)[\s]*\][\s]*([^\[]*)\[/url\]#isU",
                             '<a href="\\1" target="_blank">\\2</a>',
                             $message);
  $message = preg_replace("#\[img\][\s]*([\S]+)[\s]*\[\/img\]#isU",'<img src="\\1" alt="" />',$message);
  $message = preg_replace("#\[color=red\](.+)\[\/color\]#isU",'<span style="color:#FF0000">\\1</span>',$message);
  $message = preg_replace("#\[color=green\](.+)\[\/color\]#isU",'<span style="color:#008000">\\1</span>',$message);
  $message = preg_replace("#\[color=blue\](.+)\[\/color\]#isU",'<span style="color:#0000FF">\\1</span>',$message);
  $message = preg_replace_callback("#\[list\]\s*((?:\[\*\].+)+)\[\/list\]#siU",'getUnorderedList',$message);
  $message = preg_replace_callback("#\[list=([a|1])\]\s*((?:\[\*\].+)+)\[\/list\]#siU", 'getOrderedList',$message);
	
  $message = nl2br( $message);
	
  if ( isset( $uniqidCode ) ) $message = str_replace( $uniqidsCode, $codeBlocks, $message );
  if ( isset( $uniqidPHP ) ) $message = str_replace( $uniqidsPHP, $phpBlocks, $message );
  if ( isset( $uniqidSQL ) ) $message = str_replace( $uniqidsSQL, $sqlBlocks, $message );
  if ( isset( $uniqidJS ) ) $message = str_replace( $uniqidsJS, $jsBlocks, $message );
  if ( isset( $uniqidCSS ) ) $message = str_replace( $uniqidsCSS, $cssBlocks, $message );
  if ( isset( $uniqidHTML ) ) $message = str_replace( $uniqidsHTML, $htmlBlocks, $message );
	
  // Над этим тоже надо будет подумать
  $message = str_replace( '</div><br />', '</div>', $message );
	
	// Удаляем непарные теги - сам не знаю, нужно это делать или нет?
	// $tags = array( '[b]', '[/b]', '[i]', '[/i]', '[u]', '[/u]', '[code]', '[quote]', '[/quote]', '[/code]', '[url]', '[/url]' );
	// $message = str_replace( $tags, '', $message );
	
  return $message;
}

function split_text($matches) 
{
  return wordwrap($matches[1], 35, ' ',1);
}

function getUnorderedList( $matches )
{
  $list = '<ul>';
  $tmp = trim( $matches[1] );
  $tmp = substr( $tmp, 3 );
  $tmpArray = explode( '[*]', $tmp );	 
  $elements = '';
  foreach ( $tmpArray as $value ) {
	$elements = $elements.'<li>'.trim($value).'</li>';
  }
  $list = $list.$elements;
  $list = $list.'</ul>';
  return $list;
}

function getOrderedList( $matches )
{
  if ( $matches[1] == '1' )
	$list = '<ol type="1">';
  else
	$list = '<ol type="a">';
  $tmp = trim( $matches[2] );
  $tmp = substr( $tmp, 3 );
  $tmpArray = explode( '[*]', $tmp );
 
  $elements = '';
  foreach ( $tmpArray as $value ) {
	$elements = $elements.'<li>'.trim($value).'</li>';
  }
  $list = $list.$elements;
  $list = $list.'</ol>';
  return $list;
}

function highlight_sql( $sql ) 
{
  $sql = preg_replace("#(\"|'|`)(.+?)\\1#i", "<span style='color:red'>\\0</span>", $sql );
  $sql = preg_replace("#\b(SELECT|INSERT|UPDATE|DELETE|ALTER|TABLE|DROP|CREATE|ADD|WHERE|MODIFY|CHANGE|AS|DISTINCT|IN|ASC|DESC|ORDER|BY|GROUP|SET|FROM|INTO|LIKE|NOT|REGEXP|MAX|AVG|SUM|COUNT|MIN|AND|OR|VALUES|INDEX|HAVING|NULL|ON|BETWEEN|UNION|CONCAT|LIMIT|ANY|ALL|KEY|INNER|LEFT|RIGHT|JOIN|IFNULL|DEFAULT|CHARSET|PRIMARY|ENGINE)\b#i", "<span style='color:teal;font-weight:bold'>\\1</span>", $sql );

  $spaces = array( ' ', "\t" );
  $entities = array( '&nbsp;', '&nbsp;&nbsp;&nbsp;&nbsp;' );

  $sql = nl2br( str_replace( $spaces, $entities, $sql ) );
  $sql = str_replace( 'span&nbsp;style', 'span style', $sql );

  return $sql;
}

class Lingua_Stem_Ru
{
    var $VERSION = "0.02";
    var $Stem_Caching = 0;
    var $Stem_Cache = array();
    var $VOWEL = '/аеиоуыэюя/';
    var $PERFECTIVEGROUND = '/((ив|ивши|ившись|ыв|ывши|ывшись)|((?<=[ая])(в|вши|вшись)))$/';
    var $REFLEXIVE = '/(с[яь])$/';
    var $ADJECTIVE = '/(ее|ие|ые|ое|ими|ыми|ей|ий|ый|ой|ем|им|ым|ом|его|ого|ему|ому|их|ых|ую|юю|ая|яя|ою|ею)$/';
    var $PARTICIPLE = '/((ивш|ывш|ующ)|((?<=[ая])(ем|нн|вш|ющ|щ)))$/';
    var $VERB = '/((ила|ыла|ена|ейте|уйте|ите|или|ыли|ей|уй|ил|ыл|им|ым|ен|ило|ыло|ено|ят|ует|уют|ит|ыт|ены|ить|ыть|ишь|ую|ю)|((?<=[ая])(ла|на|ете|йте|ли|й|л|ем|н|ло|но|ет|ют|ны|ть|ешь|нно)))$/';
    var $NOUN = '/(а|ев|ов|ие|ье|е|иями|ями|ами|еи|ии|и|ией|ей|ой|ий|й|иям|ям|ием|ем|ам|ом|о|у|ах|иях|ях|ы|ь|ию|ью|ю|ия|ья|я)$/';
    var $RVRE = '/^(.*?[аеиоуыэюя])(.*)$/';
    var $DERIVATIONAL = '/[^аеиоуыэюя][аеиоуыэюя]+[^аеиоуыэюя]+[аеиоуыэюя].*(?<=о)сть?$/';

    function s(&$s, $re, $to)
    {
        $orig = $s;
        $s = preg_replace($re, $to, $s);
        return $orig !== $s;
    }

    function m($s, $re)
    {
        return preg_match($re, $s);
    }

    function stem_word($word)
    {
        $word = strtolower($word);
        $word = strtr($word, 'ё', 'е');
        # Check against cache of stemmed words
        if ($this->Stem_Caching && isset($this->Stem_Cache[$word])) {
            return $this->Stem_Cache[$word];
        }
        $stem = $word;
        do {
          if (!preg_match($this->RVRE, $word, $p)) break;
          $start = $p[1];
          $RV = $p[2];
          if (!$RV) break;

          # Step 1
          if (!$this->s($RV, $this->PERFECTIVEGROUND, '')) {
              $this->s($RV, $this->REFLEXIVE, '');

              if ($this->s($RV, $this->ADJECTIVE, '')) {
                  $this->s($RV, $this->PARTICIPLE, '');
              } else {
                  if (!$this->s($RV, $this->VERB, ''))
                      $this->s($RV, $this->NOUN, '');
              }
          }

          # Step 2
          $this->s($RV, '/и$/', '');

          # Step 3
          if ($this->m($RV, $this->DERIVATIONAL))
              $this->s($RV, '/ость?$/', '');

          # Step 4
          if (!$this->s($RV, '/ь$/', '')) {
              $this->s($RV, '/ейше?/', '');
              $this->s($RV, '/нн$/', 'н');
          }

          $stem = $start.$RV;
        } while(false);
        if ($this->Stem_Caching) $this->Stem_Cache[$word] = $stem;
        return $stem;
    }

    function stem_caching($parm_ref)
    {
        $caching_level = @$parm_ref['-level'];
        if ($caching_level) {
            if (!$this->m($caching_level, '/^[012]$/')) {
                die(__CLASS__ . "::stem_caching() - Legal values are '0','1' or '2'. '$caching_level' is not a legal value");
            }
            $this->Stem_Caching = $caching_level;
        }
        return $this->Stem_Caching;
    }

    function clear_stem_cache()
    {
        $this->Stem_Cache = array();
    }
}