<?php 

/***************************************************** 
 * Страница подтверждения регистрации нового пользователя. 
 * Возможность перехода на эту страницу должна обеспечиваться  
 * только с помощью ссылки, представленной в электронном письме. 
 *****************************************************/ 
require_once('site_code.php');
require_once('register_funcs.php');


site_header('Подтверждение регистрации');

if (!isset($feedback_str))
	$feedback_str="";

if ($_GET['hash'] && $_GET['email']) {
  $worked = user_confirm();
} else { 
  $feedback_str = "<P class=\"errormess\">ОШИБКА - Не верный линк.</P>"; 
} 


if ($worked != 1) {
  $feedback_str = '<P class="errormess">Что-то пошло не так.  
Отправить сообщение на mythclan@mail.ru с указанием причины. 
 Или перейдите на страницу index.php.</P>'; 
} else { 
   $feedback_str = '<P class="big">Регистрация подтверждена.  
<A HREF="index.php">Войдите</A> чтобы начать просмотр сайта.</P>'; 
} 

print<<<EOPAGE

<TABLE CELLPADDING=0 CELLSPACING=0 BORDER=0 ALIGN=CENTER WIDTH=621> 
<TR> 
  <TD><IMG WIDTH=15 HEIGHT=1 SRC=../images/spacer.gif></TD> 
  <TD WIDTH=606 CLASS=left> 
  $feedback_str
  </TD> 
</TR> 
</TABLE> 
EOPAGE;

site_footer();

?>