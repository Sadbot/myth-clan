<?php 
	session_start();
	
	$title="Регистрация";
	require 'html_head.php';	
	echo'<section id="mainunit">';
			require 'leftmenu.php';
			require 'rightmenu.php';

		echo'<section id="centerunit">';
			 require 'mainmenu.php';?>
		
    <h2>Регистрация</h2>
    <form action="save_user.php" method="post" enctype="multipart/form-data" >
    <!--**** save_user.php - это адрес обработчика.  То есть, после нажатия на кнопку "Зарегистрироваться", данные из полей  отправятся на страничку save_user.php методом "post" ***** -->
<p>
    <label for="login">Ваш логин <sup>*</sup>:</label>
	<input name="login" type="text" size="15" maxlength="15" required />
    </p>
<!--**** В текстовое поле (name="login" type="text") пользователь вводит свой логин ***** -->
<p>
    <label for="password">Ваш пароль <sup>*</sup>:</label>
    <input name="password" type="password" size="15" maxlength="32" required />
    </p>
    <p>
    <label for="password2">Повторите пароль <sup>*</sup>:</label>
    <input name="password2" type="password" size="15" maxlength="32" required />
    </p>
	<p>
      <label for="email">Ваш E-mail <sup>*</sup>:</label>
      <input name="email"    type="email" size="15" maxlength="50" required />
            
	</p>
<!--**** В поле для паролей (name="password" type="password") пользователь вводит свой пароль ***** --> 
<p>
              <label for="fupload">Выберите аватар. Изображение должно быть формата jpg, gif или png:</label><br>
              <input type="FILE" name="fupload">
            </p>

<p>
    <label>Почему вы хотите вступить в сообщество?<br></label>
    <textarea rows="3" cols="40" name="about">Что вы можете сообщить о себе?</textarea>
	  
    </p>
	<p>Введите    код с картинки <sup>*</sup>:<br>      
<div id="captcha_container"><img  id="captcha" src="../code/my_codegen.php" /></div> <p id="reload_captcha"> обновить </p>

            <p><input    type="text" name="code" required></p>
            <!-- В “code/my_codegen.php” генерируется    код и рисуется изображение --> 

<p>
    <input type="submit" name="submit" value="Зарегистрироваться">

<!--**** Кнопочка (type="submit") отправляет данные на страничку save_user.php ***** --> 
</p>
  <br>  
   <p> <sup>*</sup> помечены поля для обязательного заполнения </p>
</section>
     </section>
     
     <script>	
	$(document).ready(function(){	
			$("#reload_captcha").css('cursor','pointer').click(function(){
				$("#captcha").remove();
				$("#captcha_container").html('<img  id="captcha" src="code/my_codegen.php" />');
			});	
	});	
    </script>  
     
<?php 
echo'	
		</section>
     </section>';
	require 'html_foot.php';
?>