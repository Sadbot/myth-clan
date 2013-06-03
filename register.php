<?php 

/*****************************************************
 * Страница регистрации нового пользователя.  
 * Ссылки на эту страницу имеются в заголовках всех прочих страниц,  
 * которые предназначены для пользователей, выходящих из системы и  
 * входящих в систему. Тем не менее в проекте узла могут обнаруживаться дефекты;  
 * вполне возможно, что будет решено предоставлять доступ к этой странице только для посетителей, 
 * которые ещё не вошли в систему.  
 *****************************************************/
include_once('register_funcs.php');

$feedback_str = "";

if (isset($_POST['submit']) && $_POST['submit'] == 'Зарегистрироваться') 
{
	try
	{
		$feedback_str = user_register();
	}
	catch(Exception $e)
	{
		$feedback_str = $e->getMessage(). "\n";
	}
}
// ---------------- 
// Отображения формы 
// ---------------- 

// Название страницы и шапка 
site_header('Регистрация на mythclan',1,1);  
// Если послать что-то не равное null тогда отображаться левая и правая панели не будут

(isset($_POST['first_name'])) ? $first_name = htmlspecialchars($_POST['first_name']) : $first_name = "";
(isset($_POST['last_name'])) ? $last_name = htmlspecialchars($_POST['last_name']) : $last_name = "";
(isset($_POST['login'])) ? $login = htmlspecialchars($_POST['login']) : $login = "";
(isset($_POST['email'])) ? $email = htmlspecialchars($_POST['email']) : $email = "";
(isset($_POST['about'])) ? $about = htmlspecialchars($_POST['about']) : $about = "";
(isset($_POST['icq'])) ? $icq = htmlspecialchars($_POST['icq']) : $icq = "";
(isset($_POST['url'])) ? $url = htmlspecialchars($_POST['url']) : $url = "";

// В сочетании с вложенным документом не могут использоваться суперглобальные массивы 
?>

<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 ALIGN=CENTER WIDTH=621> 
<TR> 
  <TD ROWSPAN=10><IMG WIDTH=15 HEIGHT=1 SRC="../images/spacer.gif"></TD> 
  <TD WIDTH=606></TD> 
</TR> 
<TR> 
 <TD> 

<?php echo "<P class='errormess'>".htmlspecialchars($feedback_str)."</P>";?>
<P CLASS="left"><B>РЕГИСТРАЦИЯ</B><BR> 
</P> 
<FORM ACTION="<?=htmlspecialchars($_SERVER['PHP_SELF'])?>" METHOD="POST" enctype="multipart/form-data"> 
<P CLASS="bold">Имя<sup>*</sup><BR>
<INPUT TYPE="TEXT" NAME="first_name" VALUE="<?=$first_name?>" SIZE="20" MAXLENGTH="25" required></P>
<P CLASS="bold">Фамилия<sup>*</sup><BR>
<INPUT TYPE="TEXT" NAME="last_name" VALUE="<?=$last_name?>" SIZE="20" MAXLENGTH="25" required></P>
<P CLASS="bold">Логин<sup>*</sup><BR>
<INPUT TYPE="TEXT" NAME="login" VALUE="<?=$login?>" SIZE="10" MAXLENGTH="25" required></P>
<P CLASS="bold">Пароль<sup>*</sup><BR> 
<INPUT TYPE="password" NAME="password1" VALUE="" SIZE="10" MAXLENGTH="25" required></P>
<P CLASS="left"><B>Пароль</B> <sup>*</sup>(повторить)<BR>
<INPUT TYPE="password" NAME="password2" VALUE="" SIZE="10" MAXLENGTH="25" required></P>
<P CLASS="left"><B>Email</B> <sup>*</sup>(требуется для подтверждения регистрации)<BR>
<INPUT TYPE="TEXT" NAME="email" VALUE="<?=$email?>" SIZE="30" MAXLENGTH="50" required></P>

<P><label for="fupload">Выберите аватар. Изображение должно быть формата jpg, gif или png:</label><br>
<input type="file" name="fupload"></P>

<P CLASS="left">О себе<BR>
<INPUT type="text" NAME="icq" VALUE="<?=$about?>" SIZE="30" MAXLENGTH="200"></P>
<P CLASS="left">icq<BR>
<INPUT type="text" name="number" size="10" value="<?=$icq?>" MAXLENGTH="9"></P>
<P CLASS="left">URL вашей личной страницы<BR>
<INPUT type="url" NAME="icq" VALUE="<?=$url?>" SIZE="30" MAXLENGTH="12"></P>

<p CLASS="left">Введите    код с картинки <sup>*</sup><br>
<div id="captcha_container"><img  id="captcha" src="code/my_codegen.php" /></div> <p id="reload_captcha"> обновить </p>
<p><input    type="text" name="code" required></p>

<P>Обязательные для заполнения поля помечены <sup>*</sup><br />
Заполните эту форму и подтверждение о регистрации будет направлено вам на указанный email.
Как только вы нажмете на ссылку в письме, ваша учетная запись будет подтверждена
и вы cможете зайти на сайт.</P>

<P><INPUT TYPE="submit" NAME="submit" VALUE="Зарегистрироваться"></P>
</FORM>

</TD>
</TR>
</TABLE>

<?php
site_footer();

?>