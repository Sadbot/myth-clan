<?php
	echo '<aside id="leftunit">';
	require_once 'bd.php';
	

if (!isset($myrow)) {
//проверяем, не извлечены ли данные пользователя из базы. Если нет, то он не вошел, либо пароль в сессии неверный. Выводим окно для входа. Но мы не будем его выводить для вошедших, им оно уже не нужно.
print <<<HERE
Привет, гость! <br>
<form action="testreg.php" id="registration" method="post">
<!-- testreg.php - это адрес обработчика. То есть, после нажатия на кнопку "Войти", данные из полей отправятся на страничку testreg.php методом "post"  -->
  <p>
    <label for="login">Ваш логин:</label><br>
    <input name="login" type="text" id="login" class="boxstone" maxlength="15" required/>

  </p>
<!-- В текстовое поле (name="login" type="text") пользователь вводит свой логин -->  
  <p>
    <label for="password">Ваш пароль:</label><br>
    <input name="password" type="password" id="password" class="boxstone" maxlength="15" required/>

  </p>
<!-- В поле для паролей (name="password" type="password") пользователь вводит свой пароль -->  
 
<p>
<input type="submit" name="submit" class="submitstone" value="Войти">
<!-- Кнопочка (type="submit") отправляет данные на страничку testreg.php  --> 
<br>
<!-- ссылка на регистрацию, ведь как-то же должны гости туда попадать  --> 
<a href="reg.php">Зарегистрироваться</a> 

<br>
<!-- ссылка на восстановление пароля  --> 
<a href="send_pass.php">Забыли пароль?</a> 

</p></form>
HERE;
}

else
{
//при удачном входе пользователю выдается все, что расположено ниже между звездочками.
//************************************************************************************
echo"Привет, $_SESSION[login] <br>";
echo("Текущее время:" . date("H:i:s") . "<br>и дата: " .date('d F Y '). "<br><br>");

echo"<a href='page.php?id=$_SESSION[id]'><img alt=$_SESSION[login] src=$myrow[avatar]></a><br>";
echo"<a href='exit.php'>Выйти</a>";
//************************************************************************************
//при удачном входе пользователю выдается все, что расположено ВЫШЕ между звездочками.

}

echo "</aside>";