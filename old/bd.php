<?php
		
	$USER_STATUS_ADMIN = 2;
	$USER_STATUS_USER = 0;	
	
	$host = "localhost";
	
	$username = "root";
	
	$passwd = "071293";
	
	$dbname = "mythclan";

	$connection = mysqli_connect($host,$username,$passwd,$dbname) or die("Не могу подсоединиться к БД!");

function shutdown()
{
	echo'	
		</section>
     </section>';
	require 'html_foot.php';
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

function generate_code() //запускаем    функцию, генерирующую код. Можно даже вывести ее в отдельный файл.
{
	$hours = date("H"); // час       
	$minuts = substr(date("H"), 0 ,    1);// минута 
	$month = date("m");    // месяц
	$year_day = date("z"); // день в году
	$str = $hours . $minuts . $month .    $year_day; //создаем строку
	$str =    md5(md5($str)); //дважды шифруем в md5
              $str =    strrev($str);// реверс строки
              $str =    substr($str, 5, 4); // извлекаем 4 символов,    начиная с 5
              $array_mix = preg_split('//',    $str, -1, PREG_SPLIT_NO_EMPTY);
              srand ((float)microtime()*1000000);
              shuffle ($array_mix);
              return implode("",    $array_mix);
}

function    chec_code($code) //проверяем код
{
	$code = trim($code);//удаляем пробелы
	$array_mix = preg_split ('//',    generate_code(), -1, PREG_SPLIT_NO_EMPTY);
	$m_code = preg_split ('//', $code, -1,    PREG_SPLIT_NO_EMPTY);
	$result = array_intersect ($array_mix,    $m_code);
	if    (strlen(generate_code())!=strlen($code))
	{ 
		return    FALSE;
	}
	if    (sizeof($result) == sizeof($array_mix))
	{   
		return TRUE;
	}
    else
    {   
		return FALSE;
	}
}
		
