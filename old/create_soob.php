<?php
	session_start();
	require_once 'bd.php';// файл bd.php должен быть в той же папке, что и все остальные, если это не так, то просто измените путь 


	$title="Сайт клана Миф";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';
		if (empty($myrow))
	    {
			//Проверяем,    зарегистрирован ли вошедший
	        echo("Вход на эту    страницу разрешен только зарегистрированным пользователям!"); 
		}
			
		else 
		{
			 
		echo "<a href='all_soob.php'>Вернуться к сообществам</a>";
			 
		$error="";
		
		if(isset($_POST['create']))
		{	
			if(empty($_POST['name']))
			{
				$error.='<li>Не ввели название сообщества</li>';
				unset($_POST['create']);
			}
			if(empty($_POST['rule']))
			{
				$error.='<li>Не ввели правила сообщества</li>';
				unset($_POST['create']);
			}
	
	
	$valid_types =  array("gif","jpg", "png", "jpeg","GIF","JPG", "PNG", "JPEG");
	// создаем главную рабочую директорию =============================================
	$dir="avatars/";
	if (!is_dir($dir)) {
		mkdir($dir,0755);
		// создали папку gallery в корне нашего сайта и установили права на чтение и запись
	}
	if (isset($_POST['fupload']))
	{
		echo'есть фуплоад';
			// первая проверка на наличие загружаемого файла
			$ext = substr($_FILES['fupload']['name'], 1 + strrpos($_FILES['fupload']['name'], "."));
			//получаем расширение загружаемого файла
			if (in_array ($ext, $valid_types))
			{
				// сверяемся с массивом допустимых расширений и если совпадение найдено продолжаем работать
				// если нет - выводим сообщение об ошибке
				$imageinfo = getimagesize($_FILES['fupload']['tmp_name']);
				// получаем информацию о загруженном файле
				// функция getimagesize позволяет получить размер изображения в пикселях, а также mime-тип загруженного файла
				if($imageinfo['type'] != 'image/gif' && $imageinfo['type'] != 'image/jpeg' && $imageinfo['type'] != 'image/png')
				{
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
						echo ("<p>Изображение загружено</p>");
							$avatar = $dir.$output;
					}
					else 
					{
						echo "<p>Изображение не было загружено и был заменен стандартным изображением</p>";
					}
				}
				else echo "<p>Неверный тип загружаемого файла. Аватар был заменен стандартным изображением</p>";
			}
			else echo "<p>Данное расширение недопустимо для загрузки. Аватар был заменен стандартным изображением</p>";
		}
		//в случае    не соответствия формата, выдаем соответствующее сообщение
		if(empty($avatar))
			$avatar="avatars/lfooto.png";
                     
                      
			
			
			if (!empty($error))
			{
				echo "<p>Возникли следующие ошибки:</p>";
				echo '<ul>';
				echo $error;
				echo '</ul>';
			}//if empty error
			else
			{			
				$name_soob = htmlspecialchars($_POST['name']);
				$name_soob = stripslashes($_POST['name']);
				$rule_soob = htmlspecialchars($_POST['rule']);
				$rule_soob = stripslashes($_POST['rule']);
				if (isset($_POST['hide']) && $_POST['hide'] == '1')
					$hide_soob = 1;
				else
					$hide_soob = 0;
					
				var_dump($avatar);
					
				
				if (mysql_query("INSERT INTO soobs (name,rule,logo,hide,id_author) VALUES ('$name_soob', '$rule_soob','$avatar', $hide_soob, $_SESSION[id]);"))
					echo'Сообщество создано!';
				else 
					echo'Ошибка mysql запроса!';
			
			}//else no error
		}//if isset post
?>
			 

    <form action="#" method="post">
    	<label for="name">Название:</label>
        <input type="text" name="name" /><br />
        
    	<label for="rule">Правила:</label>
        <input type="text" name="rule" /><br />
        
      	<label for="fupload">Выберите аватар. Изображение должно быть формата jpg, gif или png:<br></label>
        <input type="FILE" name="fupload"><br />
        
        <label for="hide" >Закрытое сообщество</label>
        <input type="checkbox" name="hide" value='1' /><br />
        
        <input type="submit" name="create" value="Создать" />
    </form>
    
            
<?php
	}
	echo'
		</section>
     </section>';
	require 'html_foot.php'; 
?>