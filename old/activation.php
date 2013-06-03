<?php
	session_start();
    include ("bd.php");// файл bd.php должен быть в той же папке, что и все остальные, если это не так, то просто    измените путь  
         
 if    (isset($_GET['code'])) {$code =$_GET['code']; } //код подтверждения 
            else 
            {    exit("Вы    зашли на страницу без кода подтверждения!");} //если не указали code,    то выдаем ошибку
 if (isset($_GET['login'])) {$login=$_GET['login'];    } //логин,который    нужно активировать
            else 
            {    exit("Вы    зашли на страницу без логина!");} //если не указали логин, то выдаем ошибку
 $result = mysql_query("SELECT    id    FROM    user WHERE login='$login'",$db); //извлекаем    идентификатор пользователя с данным логином
            $myrow    = mysql_fetch_array($result); 
 $activation    =md5($myrow['id']).md5($login);//создаем    такой же код подтверждения





var_dump($activation,$myrow, $result);

 if ($activation == $code) {//сравниваем полученный из url и сгенерированный код 
                     mysql_query("UPDATE    users SET activation='1' WHERE login='$login'",$db);//если равны, то активируем пользователя
                     echo "Ваш Е-мейл подтвержден! Теперь вы можете    зайти на сайт под своим логином! <a href='index.php'>Главная    страница</a>";
                     }
            else {echo "Ошибка! Ваш Е-мейл не    подтвержден! <a    href='index.php'>Главная    страница</a>";
            //если    же полученный из url и    сгенерированный код не равны, то выдаем ошибку
            }
            ?>