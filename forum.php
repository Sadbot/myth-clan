<?php
// "Прошить" формы сессией
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");

require_once ('config/config.php');

// Содержимое html-тега title
site_header(FORUM_TITLE);

// Этот небольшой код для проверки того, существует ли форум,
// ID кторого передается методом GET
if ( isset( $_GET['idForum'] ) ) {
  $_GET['idForum'] = (int)$_GET['idForum'];
  if ( $_GET['idForum'] < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, есть ли форум с таким ID
  $res = $mysql->query ("SELECT name FROM ".TABLE_FORUMS." WHERE id_forum=?i",$_GET['idForum']);
 
  // Такого форума не существует - редирект на главную страницу
  if ( mysqli_num_rows( $res ) == 0 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
}

if ( !isset( $_GET['action'] ) ) $_GET['action'] = 'showMainPage';
$actions = array( 'showMainPage',
                  'showForum',
				  'showTheme',
				  'addForumForm',
				  'addForum',
				  'editForumForm',
				  'updateForum',
				  'forumUp',
				  'forumDown',
				  'deleteForum',
                  'addThemeForm',
                  'addTheme',
				  'editThemeForm',
				  'updateTheme',
				  'deleteTheme',
				  'lockTheme',
				  'unlockTheme',
                  'addPostForm',
                  'addPost',
				  'quickReply',
				  'editPostForm',
				  'updatePost',
				  'deletePost',
				  'editUserForm',
				  'updateUser',
				  'editUserFormByAdmin',
				  'updateUserByAdmin',
                  'searchForm',
				  'searchResult' );
if ( !in_array( $_GET['action'], $actions ) ) $_GET['action'] = 'showMainPage';

switch ( $_GET['action'] )
{
  case 'showMainPage':  // главная страница форума
    $content = getMainPage( $pageTitle );
	break;
  case 'showForum':     // список тем форума
    $content = getForum( $pageTitle );
	break;
  case 'showTheme':     // список сообщений темы
    $content = getTheme( $pageTitle );
	break;
  case 'addForumForm':  // форма для добавления нового форума
    $content = getAddForumForm();
	break;
  case 'addForum':      // добавить новый форум
    $content = addForum();
	break;
  case 'editForumForm': // форма для редактирования форума
    $content = getEditForumForm();
	break;
  case 'updateForum':   // обновить запись в таблице БД TABLE_FORUMS
    $content = updateForum();
	break;
  case 'forumUp':
    $content = forumUp();
	break;
  case 'forumDown':
    $content = forumDown();
	break;
  case 'deleteForum':   // удалить запись в таблице БД TABLE_FORUMS
    $content = deleteForum();
	break;
  case 'addThemeForm':  // форма для добавления новой темы
    $content = getAddThemeForm();
	break;
  case 'addTheme':      // добавить новую тему
    $content = addTheme();
	break;
  case 'editThemeForm': // форма для редактирования темы
    $content = getEditThemeForm();
	break;
  case 'updateTheme':   // обновить запись в таблице БД TABLE_THEMES
    $content = updateTheme();
	break;
  case 'deleteTheme':   // удалить тему
    $content = deleteTheme();
	break;
  case 'lockTheme':   // закрыть тему
    $content = lockTheme();
	break;
  case 'unlockTheme': // открыть тему
    $content = unlockTheme();
	break;
  case 'addPostForm':   // форма для добавления нового сообщения (поста)
    $content = getAddPostForm();
    break;
  case 'addPost':       // добавить новую запись в таблицу БД TABLE_POSTS
    $content = addPost();
	break;
  case 'quickReply':     // добавить новую запись в таблицу БД TABLE_POSTS
    $content = quickReply();
	break;
  case 'editPostForm':  // форма для редактирования сообщения (поста)
    $content = getEditPostForm();
    break;
  case 'updatePost':    // обновить запись в таблице БД TABLE_POSTS
    $content = updatePost();
	break;
  case 'deletePost':    // удалить запись в таблице БД TABLE_POSTS
    $content = deletePost();
	break;
  case 'editUserFormByAdmin':  // форма редактирования профиля (для администратора)
    $content = getEditUserFormByAdmin();
	break;
  case 'updateUserByAdmin':    // обновить данные о пользователе (для администратора)
    $content = updateUserByAdmin();
	break;
  case 'searchForm':    // форма для поиска по форуму
    $content = searchForm();
	break;
  case 'searchResult':  // результаты поиска по форуму
    $content = searchResult();
	break;
  default:
    $content = getMainPage();   
}

echo $content;

site_footer();


function getMainPage( &$pageTitle )
{
	global $mysql;
	$html='';
  $pageTitle = $pageTitle.' / Список сообществ';
  $html = $html.'<div class="menu">'."\n";
  $html = $html.'<table>'."\n";
  $html = $html.'<tr>'."\n";
  $html = $html.'<td><img src="./images/icon_mini_forums.gif" width="12" height="13" 
          border="0" alt="Список сообществ" align="bottom" />&nbsp;<a class="mainmenu" href="'.
		  $_SERVER['PHP_SELF'].'">Список сообществ</a>&nbsp;&nbsp;</td>'."\n";
  $res = $mysql -> query ( "SELECT id_forum, name, description FROM ".TABLE_FORUMS." WHERE 1 ORDER BY pos");
  if ( !$res ) {
    $msg = 'Ошибка при получении списка форумов';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  $html = '<h1>Список сообществ</h1>'."\n";
  if ( mysqli_num_rows( $res ) > 0 ) {
	while ( $forum = mysqli_fetch_array( $res ) ) {
	
	  $html = $html.'<table width="100%" cellpadding="0" cellspacing="0">'."\n";
	  $html = $html.'<tr>'."\n";
	  // Выводим название форума
	  $html = $html.'<td>';
	  $html = $html.'<div><a class="header" href="'.
	          $_SERVER['PHP_SELF'].'?action=showForum&idForum='.
	          $forum['id_forum'].'">'.
			  $forum['name'].'</a></div>'."\n";
	  $html = $html.'<div style="font-size:smaller">'.$forum['description'].'</a></div>';
	  // Выводим краткое описание форума
	  $html = $html.'</td>'."\n";
	  // Ссылка "Править форум"
      if ( isset( $_SESSION['user'] ) and $_SESSION['user']['status'] == 'admin' ) {
        $html = $html.'<td align="right"><a href="'.$_SERVER['PHP_SELF'].
		        '?action=forumUp&idForum='.$forum['id_forum'].'"><img 
				src="./images/icon_up.gif"
			    alt="Вверх" title="Вверх" /></a>&nbsp;'."\n";
        $html = $html.'<a href="'.$_SERVER['PHP_SELF'].
		        '?action=forumDown&idForum='.$forum['id_forum'].'"><img src="./images/icon_down.gif"
			    alt="Вниз" title="Вниз" /></a>&nbsp;'."\n";
		$html = $html.'<a href="'.$_SERVER['PHP_SELF'].
		        '?action=editForumForm&idForum='.$forum['id_forum'].'"><img 
				src="./images/icon_edit.gif"
			    alt="Править" title="Править" /></a>&nbsp;'."\n";
        $html = $html.'<a href="'.$_SERVER['PHP_SELF'].
		        '?action=deleteForum&idForum='.$forum['id_forum'].'"><img 
				src="./images/icon_delete.gif" alt="Удалить" title="Удалить" /></a></td>'."\n";				
      }
	  $html = $html.'</tr>'."\n";
	  $html = $html.'</table>'."\n";
	  
      $r = $mysql ->query ("SELECT id_theme, name, id_author, author, time, id_last_author, last_author, last_post, locked
	        FROM ".TABLE_THEMES."
			WHERE id_forum=?i
			ORDER BY last_post DESC
			LIMIT 3",$forum['id_forum']);
	  
	  $html = $html.'<table class="showTable">'."\n";
	  $html = $html.'<tr>'."\n";
	  $html = $html.'<th width="23"><img src="./images/null.gif" width="23" height="1" alt="" /></th>'."\n";
	  $html = $html.'<th width="50%">Тема</th>'."\n";
	  $html = $html.'<th width="14%">Автор</th>'."\n";
	  $html = $html.'<th width="14%">Добавлена</th>'."\n";
      // $html = $html.'<th width="6%">Ответов</th>'."\n";	  
	  $html = $html.'<th width="20%">Последнее&nbsp;сообщение</th>'."\n";
	  $html = $html.'</tr>'."\n";
	  
	  if ( mysqli_num_rows( $r ) == 0 ) 
	  {
	    $html = $html.'<tr>'."\n";
	    $html = $html.'<td colspan="6">'."\n";
		$html = $html.'В этом сообществе пока нет сообщений';
	    $html = $html.'</td>'."\n";
	    $html = $html.'</tr>'."\n";
		$html = $html.'</table>'."\n";
        continue;		
	  }

	  while ( $theme = mysqli_fetch_array( $r ) ) {
	    $html = $html.'<tr>'."\n";
	    if ( isset( $_SESSION['user'] ) ) { // это для зарегистрированного пользователя
	      // Если есть новые сообщения (посты) - только для зарегистрированных пользователей
	      if ( isset( $_SESSION['newThemes'] ) and in_array( $theme[0], $_SESSION['newThemes'] ) ) {
		    if ( $theme['locked'] == 0 ) // тема открыта
	          $html = $html.'<td align="center" valign="middle"><img src="./images/folder_new.gif" width="19"
		              height="18" alt="Новые сообщения" title="Новые сообщения" /></td>';
            else // тема закрыта		
	          $html = $html.'<td align="center" valign="middle"><img src="./images/folder_lock_new.gif" width="19"
		              height="18" alt="Новые сообщения" title="Новые сообщения" /></td>';
		  } else {
		    if ( $theme['locked'] == 0 ) // тема открыта
		      $html = $html.'<td align="center" valign="middle"><img src="./images/folder.gif" width="19" 
		              height="18" alt="Нет новых сообщений" title="Нет новых сообщений" /></td>';
            else // тема закрыта
		      $html = $html.'<td align="center" valign="middle"><img src="./images/folder_lock.gif" width="19" 
		              height="18" alt="Нет новых сообщений" title="Нет новых сообщений" /></td>';		  
          }
	    } else { // это для не зарегистрированного пользователя
	      if ( $theme['locked'] == 0 ) // тема открыта
		    $html = $html.'<td align="center" valign="middle"><img src="./images/folder.gif" width="19" 
		            height="18" alt="" /></td>';
          else // тема закрыта
		    $html = $html.'<td align="center" valign="middle"><img src="./images/folder_lock.gif" width="19"
                    height="18" alt="" /></td>';		
	    }
	    // Название темы
	    $html = $html.'<td>';
	    $html = $html.'<a class="topictitle" href="'.$_SERVER['PHP_SELF'].'?action=showTheme&idForum='.
	            $forum['id_forum'].'&id_theme='.$theme[0].'">'.$theme[1].'</a>';
	    $html = $html.'</td>';
	    $html = $html.'<td align="center" nowrap="nowrap">'."\n";
	    // Автор темы
		if ( $theme['id_author'] )
	      $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
	              $theme['id_author'].'">'.$theme['author'].'</a>';
		else
		  $html = $html.$theme['author'];
	    $html = $html.'</td>';
	    // Дата добавления темы
	    $html = $html.'<td align="center"><span class="details">';
	    $html = $html.$theme['time'];
	    $html = $html.'</span></td>'."\n";
	    // Количество ответов
	    // $html = $html.'<td align="center"><span class="details">';
	    // $html = $html.$theme[6];
	    // $html = $html.'</td></span>'."\n";
	    // Дата последнего обновления
	    $html = $html.'<td align="center"><span class="details">';
	    $html = $html.$theme['last_post'];
		// Автор последнего сообщения (поста)
		if ( $theme['id_last_author'] )
	      $html = $html.' <a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
	              $theme['id_last_author'].'">'.$theme['last_author'].'</a>';
		else
		  $html = $html.' '.$theme['last_author'];
	    $html = $html.'</span></td>'."\n";
	    $html = $html.'</tr>'."\n";
	  }
	  $html = $html.'</table>'."\n";
    }	  
  } else {
    $html = '<p>Не найдено ни одного форума</p>'."\n";
  }
  
  $html = $html.getStat();
  
  return $html;
}

// Функция возвращает список тем форума; ID форума передается методом GET
function getForum( &$pageTitle )
{	
  global $mysql;
  // Если не передан ID форума - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  // Получаем информацию о сообществе
  $res = $mysql -> query ( "SELECT name FROM ".TABLE_FORUMS." WHERE id_forum=?i",$_GET['idForum']);
  if ( !$res ) {
    $msg = 'Ошибка при получении списка тем форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  $forum = mysqli_fetch_row( $res );
  // Заголовок страницы (содержимое тега title)
  $pageTitle = $pageTitle.' / '.$forum[0];
  // Выводим название форума
  $html = '<h1>'.$forum[0].'</h1>'."\n";

  // Панель навигации
  $html = $html.'<div class="navDiv">'."\n";
  $html = $html.'<a class="navigation" href="'.$_SERVER['PHP_SELF'].'">Список сообществ</a>&nbsp;&gt;'."\n";
  $html = $html.'<a class="navigation" href="'.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.
          $_GET['idForum'].'">'.$forum[0].'</a>'."\n";
  $html = $html.'</div>'."\n";
  
  // Ссылка "Начать новую тему" - только для зарегистрированных пользователей
  if ( isset( $_SESSION['user'] ) ) {
    $addTheme = '<a href="'.$_SERVER['PHP_SELF'].'?action=addThemeForm&idForum='.
                $_GET['idForum'].'"><img src="./images/post.gif"  
		        alt="Начать новую тему" /></a>'."\n";
  }
  // Выбираем из БД количество тем форума - это нужно для 
  // построения постраничной навигации
  $res = $mysql -> query ("SELECT COUNT(*) FROM ".TABLE_THEMES." WHERE id_forum=?i",$_GET['idForum']);
  if ( !$res ) {
    $msg = 'Ошибка при получении списка тем форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  $total = mysqli_fetch_row( $res );
  
  if ( $total[0] == 0 ) {
  if ( isset( $_SESSION['user'] ) ) 
    return $html.$addTheme;
  else 
    return $html.'<p>В этом сообществе пока нет сообщений</p>'."\n";
  }
  
  // Число страниц списка тем форума (постраничная навигация)
  $cntPages = ceil( $total[0] / THEMES_PER_PAGE );
  
  // Проверяем передан ли номер текущей страницы (постраничная навигация)
  if ( isset($_GET['page']) ) {
    $page = (int)$_GET['page'];
    if ( $page < 1 ) $page = 1;
  } else {
    $page = 1;
  }

  if ( $page > $cntPages ) $page = $cntPages;
  // Начальная позиция (постраничная навигация)
  $start = ( $page - 1 ) * THEMES_PER_PAGE;

  // Строим постраничную навигацию, если это необходимо
  if ( $cntPages > 1 ) {
    // Функция возвращает html меню для постраничной навигации
    $pages = pageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=showForum&idForum='.
	                       $_GET['idForum'] );		   
  }
			 
  // Постраничную навигацию и ссылку "Начать новую тему" объединяем в один блок,
  // который выводится вверху и внизу страницы
  if ( isset( $pages ) or isset( $addTheme ) ) {
    $pagesAddTheme = '<table width="100%" cellpadding="0" cellspacing="0">'."\n";
    $pagesAddTheme = $pagesAddTheme.'<tr valign="middle">'."\n";
    if ( isset( $pages ) ) $pagesAddTheme = $pagesAddTheme.'<td>'.$pages.'</td>'."\n";
    if ( isset( $addTheme ) ) $pagesAddTheme = $pagesAddTheme.'<td align="right">'.$addTheme.'</td>'."\n";
    $pagesAddTheme = $pagesAddTheme.'</tr>'."\n";
    $pagesAddTheme = $pagesAddTheme.'</table>'."\n";
  }
  
  // Постраничная навигация и ссылка "Начать новую тему"
  if ( isset( $pagesAddTheme ) ) $html = $html.$pagesAddTheme;

  $res = $mysql->query ("SELECT id_theme, name, id_author, author, time, id_last_author, last_author, last_post, locked
	        FROM ".TABLE_THEMES."
			WHERE id_forum=?i
			ORDER BY last_post DESC
			LIMIT ?i, ".THEMES_PER_PAGE, $_GET['idForum'], $start);
  if ( !$res ) {
    $msg = 'Ошибка при получении списка тем форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  if ( mysqli_num_rows( $res ) > 0 ) {
	$html = $html.'<table class="showTable">'."\n";
    $html = $html.'<tr>'."\n";
	$html = $html.'<th>&nbsp;</th>';
    $html = $html.'<th width="50%">Темы</th>';
	$html = $html.'<th>Автор</th>';
	$html = $html.'<th>Добавлена</th>';
	// $html = $html.'<th>Ответов</th>';
	$html = $html.'<th>Последнее&nbsp;сообщение</th>'."\n";
	if ( isset( $_SESSION['user'] ) and $_SESSION['status'] != 'user' ) {
	  $html = $html.'<th>Правка</th>'."\n";
	  $html = $html.'<th>Блк.</th>'."\n";
	  $html = $html.'<th>Удл.</th>'."\n";
	}
	  
	$html = $html.'</tr>'."\n";
	while ( $theme = mysqli_fetch_array( $res ) ) {
	  $html = $html.'<tr>'."\n";
	  if ( isset( $_SESSION['user'] ) ) { // это для зарегистрированного пользователя
	    // Если есть новые сообщения (посты) - только для зарегистрированных пользователей
	    if ( isset( $_SESSION['newThemes'] ) and in_array( $theme['id_theme'], $_SESSION['newThemes'] ) ) {
		  if ( $theme['locked'] == 0 ) // тема открыта
	        $html = $html.'<td align="center" valign="middle"><img src="./images/folder_new.gif" width="19"
		            height="18" alt="Новые сообщения" title="Новые сообщения" /></td>';
          else // тема закрыта		
	        $html = $html.'<td align="center" valign="middle"><img src="./images/folder_lock_new.gif" width="19"
		            height="18" alt="Новые сообщения" title="Новые сообщения" /></td>';
		} else {
		  if ( $theme['locked'] == 0 ) // тема открыта
		    $html = $html.'<td align="center" valign="middle"><img src="./images/folder.gif" width="19" 
		            height="18" alt="Нет новых сообщений" title="Нет новых сообщений" /></td>';
          else // тема закрыта
		    $html = $html.'<td align="center" valign="middle"><img src="./images/folder_lock.gif" width="19" 
		            height="18" alt="Нет новых сообщений" title="Нет новых сообщений" /></td>';		  
        }
	  } else { // это для не зарегистрированного пользователя
	    if ( $theme['locked'] == 0 ) // тема открыта
		  $html = $html.'<td align="center" valign="middle"><img src="./images/folder.gif" width="19" 
		          height="18" alt="" /></td>';
        else // тема закрыта
		  $html = $html.'<td align="center" valign="middle"><img src="./images/folder_lock.gif" width="19"
                  height="18" alt="" /></td>';		
	  }
	  
	  // Название темы
	  $html = $html.'<td>';
	  $html = $html.'<a class="topictitle" href="'.$_SERVER['PHP_SELF'].'?action=showTheme&idForum='.
	          $_GET['idForum'].'&id_theme='.$theme['id_theme'].'">'.$theme['name'].'</a>';
	  $html = $html.'</td>';
	  $html = $html.'<td align="center">'."\n";
	  // Автор темы
	  if ( $theme['id_author'] ) {
	    $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
	            $theme['id_author'].'">'.$theme['author'].'</a>';
	  } else {
	    $html = $html.$theme[5];
	  }
	  $html = $html.'</td>';
	  // Дата добавления темы
	  $html = $html.'<td align="center" nowrap="nowrap"><span class="details">';
	  $html = $html.$theme['time'];
	  $html = $html.'</span></td>'."\n";
	  // Количество ответов
	  // $html = $html.'<td align="center" nowrap="nowrap">';
	  // $html = $html.$theme[6];
	  // $html = $html.'</td>'."\n";
	  // Дата последнего обновления
	  $html = $html.'<td align="center"><span class="details">';
	  $html = $html.$theme['last_post'];
      // Автор последнего сообщения (поста)
      if ( $theme['id_last_author'] )
	    $html = $html.' <a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
	            $theme['id_last_author'].'">'.$theme['last_author'].'</a>';
      else
		$html = $html.' '.$theme['last_author'];
	  $html = $html.'</span></td>'."\n";  
	  // Ссылки "Редактировать", "Закрыть"/"Открыть" и "Удалить" - 
	  // только для администратора и модератора
	  if ( isset( $_SESSION['user'] ) and $_SESSION['user']['status'] != 'user' ) {
	  $html = $html.'<td align="center" nowrap="nowrap">';
	  $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=editThemeForm&idForum='.
	          $_GET['idForum'].'&id_theme='.$theme[0].'"><img src="./images/icon_edit.gif"
			  alt="Править" title="Править" /></a>';
	  $html = $html.'</td>'."\n";
	  $html = $html.'<td align="center" nowrap="nowrap">';
	  if ( $theme['locked'] == 0 ) { // заблокировать тему
	    $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=lockTheme&idForum='.
	            $_GET['idForum'].'&id_theme='.$theme['id_theme'].'"><img src="./images/topic_lock.gif"
			    alt="Закрыть" title="Закрыть" /></a>';
	  } else { // разблокировать тему
	    $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=unlockTheme&idForum='.
	            $_GET['idForum'].'&id_theme='.$theme['id_theme'].'"><img src="./images/topic_unlock.gif"
			    alt="Открыть" title="Открыть" /></a>';
      }
	  $html = $html.'</td>'."\n";
	  $html = $html.'<td align="center" nowrap="nowrap">';
	  $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=deleteTheme&idForum='.
	          $_GET['idForum'].'&id_theme='.$theme['id_theme'].'"><img src="./images/icon_delete.gif"
			  alt="Удалить" title="Удалить" /></a>';
      $html = $html.'</td>'."\n";
	  
	  }
	  $html = $html.'</tr>'."\n";
	}
	$html = $html.'</table>'."\n";
	
    // Постраничная навигация и ссылка "Начать новую тему"
    if ( isset( $pagesAddTheme ) ) $html = $html.$pagesAddTheme;
  
  }
  return $html;
}

// Функция возвращает список сообщений(постов) темы; ID темы передается методом GET
function getTheme( &$pageTitle )
{
		global $mysql;
  // Если не передан ID форума - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  } 
  // Если не передан ID темы - функция вызвана по ошибке
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }  
  
  // Получаем из БД информацию о теме
  $res = $mysql->query ( "SELECT name, locked FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при получении списка сообщений темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showForum&idForum='.$_GET['idForum'] );
  }
  // Если запрошенной темы не существует - возвращаемся на форум
  if ( mysqli_num_rows( $res ) == 0 ) 
    return showInfoMessage( 'Запрошенная тема не найдена', 'action=showForum&idForum='.$_GET['idForum'] );
  
  list( $theme, $locked ) = mysqli_fetch_row( $res );
  // Заголовок страницы (содержимое тега title)
  $pageTitle = $pageTitle.' / '.$theme;
  // Название темы
  $html = '<h1>'.$theme.'</h1>'."\n";

  // Получаем информацию о сообществе - это нужно для построения панели навигации
  $res = $mysql->query ("SELECT name FROM ".TABLE_FORUMS." WHERE id_forum=?i",$_GET['idForum']);
  if ( !$res ) {
    $msg = 'Ошибка при получении списка сообщений темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.
                             $_GET['idForum'] );
  }
  $name_forum = mysqli_fetch_row($res);
  
  // Панель навигации
  $html = $html.'<div class="navDiv">'."\n";
  $html = $html.'<a class="navigation" href="'.$_SERVER['PHP_SELF'].'">Список сообществ</a>&nbsp;&gt;'."\n";
  $html = $html.'<a class="navigation" href="'.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.
          $_GET['idForum'].'">'.$name_forum[0].'</a>&nbsp;&gt;'."\n";
  $html = $html.'<a class="navigation" href="'.$_SERVER['PHP_SELF'].'?action=showTheme&idForum='.
          $_GET['idForum'].'&id_theme='.$id_theme.'">'.$theme.'</a>'."\n";
  $html = $html.'</div>'."\n";
  
  // Выбираем из БД количество сообщений - это нужно для 
  // построения постраничной навигации
  $res = $mysql->query ("SELECT COUNT(*) FROM ".TABLE_POSTS." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при получении списка сообщений темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showForum&idForum='.$_GET['idForum'] );
  }
  $total = mysqli_fetch_row( $res );
  // Не может быть темы, в которой нет сообщений (постов) - надо ее удалить
  if ( $total[0] == 0 ) {
	$r = $mysql->query( "DELETE FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
    if ( !$r ) {
      $msg = 'Ошибка при получении списка сообщений темы';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $q.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
    }
    return showInfoMessage( 'Запрошенная тема не найдена', 'action=showForum&idForum='.$_GET['idForum'] );	
  }

  // Число страниц списка сообщений (постов) темы (постраничная навигация)
  $cntPages = ceil( $total[0] / POSTS_PER_PAGE );
  
  // Проверяем передан ли номер текущей страницы (постраничная навигация)
  if ( isset($_GET['page']) ) {
    $page = (int)$_GET['page'];
    if ( $page < 1 ) $page = 1;
  } else {
    $page = $cntPages;
  }

  if ( $page > $cntPages ) $page = $cntPages;
  // Начальная позиция (постраничная навигация)
  $start = ( $page - 1 ) * POSTS_PER_PAGE;

  // Строим постраничную навигацию, если это необходимо
  if ( $cntPages > 1 ) {
    // Функция возвращает html меню для постраничной навигации
    $pages = pageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=showTheme&idForum='.
	                       $_GET['idForum'].'&id_theme='.$id_theme );		   
  } else {
    $pages = '&nbsp;';
  }
  
  // Получаем из БД список сообщений (постов) темы
  $res = $mysql->query ("SELECT a.id_post, a.name, a.id_author, a.time, a.putfile, a.locked, a.id_theme, 
                   DATE_FORMAT(a.edittime, '%d.%m.%Y') AS edittime, a.id_editor, 
                   IFNULL(b.login, '".NOT_REGISTERED_USER."') AS author, b.posts, b.avatar, b.url, 
				   DATE_FORMAT(b.date, '%d.%m.%Y') AS regtime, b.status AS status, 
				   IFNULL(b.signature, '') AS signature, IFNULL(b.locked, 0) AS blocked,
				   IFNULL(c.login, '') AS editor, IFNULL(c.status, '') AS editor_status	
            FROM ".TABLE_POSTS." as a LEFT JOIN ".TABLE_USERS." as b
            ON a.id_author=b.id	
            LEFT JOIN ".TABLE_USERS." as c
            ON a.id_editor=c.id			
			WHERE id_theme=?i ORDER BY time ASC 
			LIMIT ?i, ".POSTS_PER_PAGE,$id_theme,$start);
			
  if ( !$res ) {
    $msg = 'Ошибка при получении списка сообщений темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showForum&idForum='.$_GET['idForum'] );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    // Не может быть темы, в которой нет сообщений (постов) - надо ее удалить
	$r = $mysql->query ("DELETE FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
    if ( !$r ) {
      $msg = 'Ошибка при получении списка сообщений темы';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $q.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.

	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
    }
    return showInfoMessage( 'Запрошенная тема не найдена', 'action=showForum&idForum='.$_GET['idForum'] );	
  }
  // Ссылка "Ответить" (если тема закрыта - выводим сообщение "Тема закрыта")
  if ( $locked == 0 )
    $addPost = '<a href="'.$_SERVER['PHP_SELF'].'?action=addPostForm&idForum='.$_GET['idForum'].
	           '&id_theme='.$id_theme.'"><img src="./images/reply.gif"
		       alt="Ответить" title="Ответить" /></a>'."\n";
  else
    $addPost = '<img src="./images/reply_locked.gif"
		       alt="Тема закрыта" title="Тема закрыта" />'."\n";  
			 
  // Постраничную навигацию и ссылку "Ответить" объединяем в один блок,
  // который выводится вверху и внизу страницы
  $pagesAddPost = '<table width="100%" cellpadding="0" cellspacing="0">'."\n";
  $pagesAddPost = $pagesAddPost.'<tr valign="middle">'."\n";
  $pagesAddPost = $pagesAddPost.'<td>'.$pages.'</td>'."\n";
  $pagesAddPost = $pagesAddPost.'<td align="right">'.$addPost.'</td>'."\n";
  $pagesAddPost = $pagesAddPost.'</tr>'."\n";
  $pagesAddPost = $pagesAddPost.'</table>'."\n"; 
  
  $html = $html.$pagesAddPost;
  // Сообщения (посты) темы; каждое сообщение - отдельная таблица	  
  while ( $post = mysqli_fetch_array( $res ) ) {
	$html = $html.'<table class="postTable">'."\n";
	$html = $html.'<tr class="postTop">'."\n";
	$html = $html.'<td width="120"><span class="postAuthor" onClick="javascript:putName(\''.
	         $post['author'].'\')" onMouseOver="this.className=\'postAuthorOver\'"
             onMouseOut="this.className=\'postAuthor\'">'.$post['author'].
			 '</span><br/><img src="./images/null.gif" alt="" width="120" height="1" /></td>'."\n";
	$html = $html.'<td width="45%"><span class="details">&nbsp;Добавлено '.$post['time'].'</span></td>'."\n";
	$html = $html.'<td width="45%" align="right">';
	// Если тема не заблокирована - выводим ссылку "Ответить с цитатой"
	if ( $locked == 0 ) {
	  $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=addPostForm&idForum='.$_GET['idForum'].
	          '&id_theme='.$id_theme.'"><img src="./images/icon_quote.gif"
			   alt="Ответить с цитатой" title="Ответить с цитатой" border="0" /></a>&nbsp;&nbsp;';
    }
	// Определяем, нужно ли выводить ссылку "Редактировать"
    if ( hasRightEditPost( $post ) ) {
	  $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=editPostForm&idForum='.$_GET['idForum'].
	          '&id_theme='.$id_theme.'&id_post='.$post['id_post'].'"><img src="./images/icon_edit.gif"
			  alt="Править" title="Править" border="0" /></a>&nbsp;&nbsp;';
	}
	// Определяем, нужно ли выводить ссылку "Удалить"
    if ( hasRightDeletePost( $post ) ) {
	  $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=deletePost&idForum='.$_GET['idForum'].
	          '&id_theme='.$id_theme.'&id_post='.$post['id_post'].'"><img src="./images/icon_delete.gif"
			  alt="Удалить" title="Удалить" border="0" /></a>';
	}
	$html = $html.'</td>'."\n";
	$html = $html.'</tr>'."\n";
	$html = $html.'<tr class="postMiddle">'."\n";
	$html = $html.'<td>'."\n";
	// Если автор сообщения (поста) - зарегистрированный пользователь
	if ( $post['avatar'] ) {	  
	  // Аватар
	  if ( is_file( $post['avatar'] ) ) {
	    $html = $html.'<img src="'.$post['avatar'].'" width="100" height="100" alt="'.$post['avatar'].'" 
		        title="'.$post['author'].'" /><br/>'."\n";
	  } else {
	    $html = $html.'<img src="avatars/lfooto.png" alt="" width="100" height="100" 
		        style="border:1px solid #CCCCCC" /><br/>'."\n";
	  }
	  // Статус пользователя
	  $status = array( 'user' => 'Пользователь',
                       'moderator' => 'Модератор',
				       'admin' => 'Администратор' );
	  if ( $post['status'] == 'admin' ) 
	    $html = $html.'<span class="adminStatus">'.$status[$post['status']].'</span><br/>'."\n";
	  if ( $post['status'] == 'moderator' ) 
	    $html = $html.'<span class="moderStatus">'.$status[$post['status']].'</span><br/>'."\n";
	  /*
	  if ( $post['status'] == 'user' ) 
	    $html = $html.'<span class="userStatus">'.$status[$post['status']].'</span><br/>'."\n";
	  */
      // Рейтинг пользователя (по количеству сообщений)
	  $stars = '';
	  $rating = $post['posts'];
	  while( $rating > 0 ) {
        if ( $rating < 50 )
          $img = 'stars0.gif';
        else if ( $rating >= 50 and $rating < 100 )
          $img = 'stars1.gif';
        else if ( $rating >= 100 and $rating < 150 )
          $img = 'stars2.gif';
        else if ( $rating >= 150 and $rating < 200 )
          $img = 'stars3.gif';
        else if ( $rating >= 200 and $rating < 250 )
          $img = 'stars4.gif';
        else if ( $rating >= 250 and $rating < 300 )
          $img = 'stars5.gif';
        else if ( $rating >= 300 and $rating < 350 )
          $img = 'stars6.gif';
        else if ( $rating >= 350 and $rating < 400 )
          $img = 'stars7.gif';
        else if ( $rating >= 400 and $rating < 450 )
          $img = 'stars8.gif';
        else if ( $rating >= 450 and $rating < 500 )
          $img = 'stars9.gif';
        else
          $img = 'stars10.gif';
		$rating = $rating - 500;
        $stars = $stars.'<img src="./images/'.$img.'" alt="" /><br/>';
	  }
	  $html = $html.$stars.'<br/>'."\n";
	  // Количество сообщений
	  $html = $html.'<span class="details">Сообщений:&nbsp;'.$post['posts'].'</span><br/>'."\n";
	  // Дата регистрации
	   $html = $html.'<span class="details">Зарегистрирован: '.$post['regtime'].'</span><br/>'."\n";

	  // Если автор сообщения сейчас "на сайте" 
	  if ( isset( $_SESSION['usersOnLine'] )  ) {
	    if ( isset( $_SESSION['usersOnLine'][$post['id_author']] ) ) 
	      $html = $html.'<span class="details">Просматривает форум</span><br/>'."\n";
        else
	      $html = $html.'<span class="details">Покинул форум</span><br/>'."\n";
      }
	  // Если пользователь заблокирован
	  if ( $post['blocked'] ) 
	    $html = $html.'<span class="userLocked">[Заблокирован]</span><br/>'."\n";
	   
	} else { // Если автор сообщения - незарегистрированный пользователь
	  $html = $html.'<img src="./images/null.gif" alt="" width="100" height="100" 
		      style="border:1px solid #CCCCCC" /><br/>'."\n";	
	}
	
	$html = $html.'<br/><span class="quoteAuthor" onClick=quoteSelection(\''.$post['author'].'\');
            onMouseOver="catchSelection(); this.className=\'quoteAuthorOver\'" 
            onMouseOut="this.className=\'quoteAuthor\'">Цитировать</span>';
	
	$html = $html.'</td>'."\n";
	$html = $html.'<td colspan="2">'."\n"; 
	$html = $html.print_page( $post['name'] )."\n";
	// Если есть прикреплённый файл - формируем ссылку на него
    if( !empty( $post['putfile'] ) and is_file( './files/'.$post['putfile'] ) ) {
      $html = $html.'<div align="right"><img src="./images/file.gif" alt="Открыть файл" 
	          title="Открыть файл" align="absmiddle" />&nbsp;<a target="_blank" 
			  href="./files/'.$post['putfile'].'">'.
              ( getFileSize( './files/'.$post['putfile'] ) ).' Кб</a></div>'."\n";
    }
    if ( !empty( $post['signature'] ) ) {
	  $html = $html.'<br/><br/><hr>'."\n".'<div class="details">'.$post['signature'].'</div>'."\n";
	}
	$html = $html.'</td>'."\n";
	  
	$html = $html.'</tr>'."\n";
	$html = $html.'<tr class="postBottom">'."\n";
	$html = $html.'<td><a class="navigation" href="#top">Наверх</a></td>'."\n";
	// Если автор сообщения (поста) - зарегистрированный пользователь
	if ( $post['id_author'] ) {
	  $html = $html.'<td>'."\n";
	  $html = $html.'&nbsp;<a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
		      $post['id_author'].'"><img src="./images/icon_profile.gif"
			  alt="Посмотреть профиль" title="Посмотреть профиль" /></a>';
	  $html = $html.'&nbsp;&nbsp;'."\n";
	  if ( isset( $_SESSION['user'] ) ) {
	    $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=sendMailForm&idUser='.
		        $post['id_author'].'"><img src="./images/icon_email.gif"
			    alt="Написать письмо" title="Написать письмо" /></a>';
	    $html = $html.'&nbsp;&nbsp;'."\n";
	    $html = $html.'<a href="'.$_SERVER['PHP_SELF'].'?action=sendMsgForm&idUser='.
		        $post['id_author'].'"><img src="./images/icon_pm.gif" 
		        alt="Личное сообщение" title="Личное сообщение" /></a>';
	    $html = $html.'&nbsp;&nbsp;'."\n";
      }
	  if ( !empty( $post['url'] ) ) {
	  $html = $html.'<a href="'.$post['url'].'" target="_blank"><img src="./images/icon_www.gif"
			  alt="Сайт автора" title="Сайт автора" /></a>';
      }
	  $html = $html.'</td>'."\n";	  
	} else {
	  $html = $html.'<td><span class="details"><img src="./images/null.gif" alt="" width="1" 
	          height="20" align="absmiddle" />Незарегистрированный пользователь</span></td>'."\n";
	}
	// Если сообщение редактировалось...
	if ( !empty( $post['editor'] ) ) {
	  $html = $html.'<td align="right">';
	  if ( $post['id_author'] == $post['id_editor'] ) {
	    $html = $html.'<span class="editedByUser">Отредактировано автором '.$post['edittime'].'</span>'."\n";
      } else { 
	    if ( $post['editor_status'] == 'admin' )
	      $html = $html.'<span class="editedByAdmin">Отредактировано администратором '.
		          $post['editor'].' '.$post['edittime'].'</span>'."\n";
	    if ( $post['editor_status'] == 'moderator' )
	      $html = $html.'<span class="editedByModer">Отредактировано модератором '.
		          $post['editor'].' '.$post['edittime'].'</span>'."\n";
	   if ( $post['editor_status'] == 'user' )
           $html = $html.'<span class="editedByUser">Отредактировано '.
		           $post['editor'].' '.$post['edittime'].'</span>'."\n";
      }	  
	  $html = $html.'</td>'."\n";
	} else {
	  $html = $html.'<td>&nbsp;</td>'."\n";
	}
	$html = $html.'</tr>'."\n";
	$html = $html.'</table>'."\n";	
  }
  
  // Постраничная навигация и ссылка "Ответить"
  $html = $html.$pagesAddPost;
  
  // Если тема не закрыта - выводим форму для быстрого ответа
  if ( $locked == 0 ) $html = $html.getQuickReplyForm( $id_theme );
  
  // Если страницу темы запросил зарегистрированный пользователь, значит он ее просмотрит
  if ( isset( $_SESSION['user'] ) and isset( $_SESSION['newThemes'] ) ) {
    if ( count( $_SESSION['newThemes'] ) > 0 ) {
	  if ( in_array( $id_theme, $_SESSION['newThemes'] ) ) {		
	    unset( $_SESSION['newThemes'][$id_theme] );
	  }
	} else {
	  unset( $_SESSION['newThemes'] );  
	}
  }
  
  return $html;
}

// Функция возвращает форму для добавления нового форума
function getAddForumForm()
{
		global $mysql;
  // Если форум пытается создать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, иммет ли право этот пользователь создавать форумы
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  $html = '';
  
  $action = $_SERVER['PHP_SELF'].'?action=addForum';
  $title = '';
  $description = '';
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['addForumForm'] ) ) {
    $info = file_get_contents( './templates/infoMessage.html' );
	$info = str_replace( '{infoMessage}', $_SESSION['addForumForm']['error'], $info );
	$html = $html.$info."\n";
	$title  = htmlspecialchars( $_SESSION['addForumForm']['title'] );
	$description = htmlspecialchars( $_SESSION['addForumForm']['description'] );
	unset( $_SESSION['addForumForm'] );
  }
  
  // Считываем в переменную содержимое файла, 
  // содержащего форму для добавления форума
  $tpl = file_get_contents( './templates/addForumForm.html' );
  $tpl = str_replace( '{action}', $action, $tpl );
  $tpl = str_replace( '{title}', '', $tpl );
  $tpl = str_replace( '{description}', '', $tpl );
  
  $html = $html . $tpl;
  
  return $html;
}

// Функция добавляет новый форум (новую запись в таблицу БД TABLE_FORUMS)
function addForum()
{
		global $mysql;
  // Если форум пытается создать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) )  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, имеет ли право этот пользователь создавать форумы
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Если не переданы данные формы - значит функция была вызвана по ошибке
  if ( !isset( $_POST['title'] ) or
       !isset( $_POST['description'] ) )
  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Обрезаем переменные до длины, указанной в параметре maxlength тега input
  $title  = substr( $_POST['title'], 0, 120 );
  $description = substr( $_POST['description'], 0, 250 );
  // Обрезаем лишние пробелы
  $title  = trim( $title );
  $description = trim( $description );
  
  // Проверяем, заполнены ли обязательные поля
  $error = '';
  if ( empty( $title ) ) $error = $error.'<li>не заполнено поле "Название форума"</li>'."\n";
  if ( empty( $description ) ) $error = $error.'<li>не заполнено поле "Описание"</li>'."\n";
  // Проверяем поля формы на недопустимые символы
  if ( !empty( $title ) and !preg_match( "#^[-.;:,?!\/)(_\"\s0-9а-яА-Яa-z]+$#i", $title ) )
    $error = $error.'<li>поле "Название форума" содержит недопустимые символы</li>'."\n";
  if ( !empty( $description ) and !preg_match( "#^[-.;:,?!\/)(_\"\s0-9а-яА-Яa-z]+$#i", $description ) )
    $error = $error.'<li>поле "Описание" содержит недопустимые символы</li>'."\n";
	
  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем посетителя для исправления ошибок
  if ( !empty( $error ) ) {
	$_SESSION['addForumForm'] = array();
	$_SESSION['addForumForm']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'.
	"\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['addForumForm']['title'] = $title;
	$_SESSION['addForumForm']['description'] = $description;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=addForumForm' );
	die();
  }
  // Порядок следования - новый форум будет в конце списка
  $res = $mysql -> query ( "SELECT IFNULL(MAX(pos), 0) FROM ".TABLE_FORUMS);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении нового форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  $order = mysqli_fetch_row( $res);
  $order[0] += 1;
  $res = $mysql -> query ( "INSERT INTO ".TABLE_FORUMS."
            (
			name,
			description,
			pos
			)
			VALUES
			(
			?s,
			?s,
			?s
			)",$title,$description,$order[0]);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении нового форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	  $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	  '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  
  return showInfoMessage( 'Новый форум успешно добавлен', '' );
}

// Функция возвращает форму для редактирования форума;
// уникальный ID форума передается методом GET
function getEditForumForm()
{
		global $mysql;
  // Если форум пытается редактировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) )  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, имеет ли право этот пользователь редактировать форум
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID форума - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  } 
  
  $html = '';
  
  // Получаем из БД информацию о сообществе
  $res = $mysql->query ("SELECT name, description FROM ".TABLE_FORUMS." WHERE id_forum=?i",$_GET['idForum']);
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для редактирования форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	  $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	  '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  $forum = mysqli_fetch_array( $res );
  $action = $_SERVER['PHP_SELF'].'?action=updateForum&idForum='.$_GET['idForum'];
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['editForumForm'] ) ) {	
    $info = file_get_contents( './templates/infoMessage.html' );
	$info = str_replace( '{infoMessage}', $_SESSION['editForumForm']['error'], $info );
	$html = $html.$info."\n";	
	$title  = htmlspecialchars( $_SESSION['editForumForm']['title'] );
	$description = htmlspecialchars( $_SESSION['editForumForm']['description'] );
	unset( $_SESSION['editForumForm'] );
  } else {
    $title = htmlspecialchars( $forum['name'] );
    $description = htmlspecialchars( $forum['description'] );
  }
  // Считываем в переменную содержимое файла, 
  // содержащего форму для редактирования форума
  $tpl = file_get_contents( './templates/editForumForm.html' );
  $tpl = str_replace( '{action}', $action, $tpl );
  $tpl = str_replace( '{title}', $title, $tpl );
  $tpl = str_replace( '{description}', $description, $tpl );
  
  $html = $html . $tpl;
  
  return $html;
}

// Функция обновляет информацию о сообществе (запись в таблице БД TABLE_FORUMS);
// уникальный ID форума передается методом GET
function updateForum()
{
  // Если форум пытается редактировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) )  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, имеет ли право этот пользователь редактировать форум
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  } 
  // Если не передан ID форума - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }

  // Обрезаем переменные до длины, указанной в параметре maxlength тега input
  $title  = substr( $_POST['title'], 0, 120 );
  $description = substr( $_POST['description'], 0, 250 );
  // Обрезаем лишние пробелы
  $title  = trim( $title );
  $description = trim( $description );
  
  // Проверяем, заполнены ли обязательные поля
  $error = '';
  if ( empty( $title ) ) $error = $error.'<li>не заполнено поле "Название форума"</li>'."\n";
  if ( empty( $description ) ) $error = $error.'<li>не заполнено поле "Описание"</li>'."\n";
  // Проверяем поля формы на недопустимые символы
  if ( !empty( $title ) and !preg_match( "#^[-.;:,?!\/)(_\"\s0-9а-яА-Яa-z]+$#i", $title ) )
    $error = $error.'<li>поле "Название форума" содержит недопустимые символы</li>'."\n";
  if ( !empty( $description ) and !preg_match( "#^[-.;:,?!\/)(_\"\s0-9а-яА-Яa-z]+$#i", $description ) )
    $error = $error.'<li>поле "Описание" содержит недопустимые символы</li>'."\n";
	
  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем посетителя для исправления ошибок
  if ( !empty( $error ) )
  {
	$_SESSION['editForumForm'] = array();
	$_SESSION['editForumForm']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'.
	"\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['editForumForm']['title'] = $title;
	$_SESSION['editForumForm']['description'] = $description;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=editForumForm&idForum='.$_GET['idForum'] );
	die();
  }
  // Все поля заполнены правильно - выполняем запрос
  $res = $mysql->query ("UPDATE ".TABLE_FORUMS." 
            SET 
			name=?s, 
			description=?s
			WHERE id_forum=?i",$title,$description,$_GET['idForum']);
  if ( !$res ) {
    $msg = 'Ошибка при обновлении форума';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	      '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  } 
    
  return showInfoMessage( 'Обновление форума прошло успешно', '' ); 
}

function forumUp()
{
		global $mysql;
  // Если форум пытается редактировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) )  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, имеет ли право этот пользователь редактировать форум
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  } 
  // Если не передан ID форума - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  // Форум, который "поднимается" вверх
  $id_forum_up = $_GET['idForum'];
  $res = $mysql->query ("SELECT pos FROM ".TABLE_FORUMS." WHERE id_forum=?i",$id_forum_up);
  // Порядок следования форума, который "поднимается" вверх
  $order_up = mysqli_fetch_row( $res );
  $res = $mysql->query ("SELECT id_forum, pos  
            FROM ".TABLE_FORUMS." 
			WHERE pos<?s 
			ORDER BY pos DESC LIMIT 1",$order_up[0]);
  // Если форум, который "поднимается" вверх и так выше всех (первый в списке)
  if ( mysqli_num_rows( $res ) == 0 ) return true;
  // Порядок следования и ID форума, который находится выше и будет "опущен" вниз
  // ( поменявшись местами с форумом, который "поднимается" вверх )
  list( $id_forum_down, $order_down ) = mysqli_fetch_array( $res );
  // Меняем местами форумы
  $res1 = $mysql->query ("UPDATE ".TABLE_FORUMS." SET pos=?s WHERE id_forum=?i",$order_down,$id_forum_up);
  $res2 = $mysql->query ("UPDATE ".TABLE_FORUMS." SET pos=?s WHERE id_forum=?i",$order_up,$id_forum_down);
  if ( $res1 and $res2 )
    return showInfoMessage( 'Операция прошла успешно', '' );
  else
    return showInfoMessage( 'Ошибка при выполнении операции', '' );
}

function forumDown()
{
		global $mysql;
  // Если форум пытается редактировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) )  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Проверяем, имеет ли право этот пользователь редактировать форум
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  } 
  // Если не передан ID форума - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  // Форум, который "опускается" вниз
  $id_forum_down = $_GET['idForum'];
  $res =  $mysql->query ("SELECT pos FROM ".TABLE_FORUMS." WHERE id_forum=?i",$id_forum_down);
  // Порядок следования форума, который "опускается" вниз
  $order_down = mysqli_fetch_row( $res);
  $res = $mysql->query ("SELECT id_forum, pos  
            FROM ".TABLE_FORUMS." 
			WHERE pos>?s 
			ORDER BY pos LIMIT 1",$order_down[0]);
  // Если форум, который "опускается" вниз и так ниже всех (последний в списке)
  if ( mysqli_num_rows( $res ) == 0 ) return true;
  // Порядок следования и ID форума, который находится ниже и будет "поднят" вверх
  // ( поменявшись местами с форумом, который "опускается" вниз )
  list( $id_forum_up, $order_up ) = mysqli_fetch_array( $res );
  // Меняем местами форумы
  $res1 = $mysql->query ("UPDATE ".TABLE_FORUMS." SET pos=?s WHERE id_forum=?i",$order_down,$id_forum_up);
  $res2 = $mysql->query ("UPDATE ".TABLE_FORUMS." SET pos=?s WHERE id_forum=?i",$order_up,$id_forum_down);
  if ( $res1 and $res2 )
    return showInfoMessage( 'Операция прошла успешно', '' );
  else
    return showInfoMessage( 'Ошибка при выполнении операции', '' );
}

// Функция удаляет форум (запись в таблице TABLE_FORUMS)
function deleteForum()
{
		global $mysql;
  // Не зарегистрированный пользователь не может добавить тему
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  // Форум может удалить только администратор
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();  
  }
  // Если не передан ID форума - значит функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {

    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  // Можно удалить только форум, который не содержит тем (в целях безопасности)
  $res = $mysql->query ("SELECT COUNT(*) FROM ".TABLE_THEMES." WHERE id_forum=?i",$_GET['idForum']);
  if ( !$res ) {
    $msg = 'Ошибка при удалении форума';
    $err = 'Ошибка при выполнении запроса: <br/>'.
           $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
           '(Файл '. __FILE__ .', строка '. __LINE__ .')';
    return showErrorMessage( $msg, $err, true, '' );    	  
  } 
  $res_count=mysqli_fetch_row($res);
  if ( $res_count[0] > 0 )
    return showInfoMessage( 'Нельзя удалить форум, который содержит темы', '' );
  else
    return showInfoMessage( 'Форум успешно удален', '' );  
}


// Функция возвращает форму для добавления новой темы; 
// ID форума, куда добавляется тема передается методом GET
function getAddThemeForm()
{
		global $mysql;
  // Незарегистрированный пользователь не может добавить тему
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();  
  }
  // Если не передан ID форума, куда будет добавлена тема - 
  // значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  
  $html = '';
  $theme   = '';
  $message = '';

  if ( isset( $_SESSION['viewMessage'] ) and !empty( $_SESSION['viewMessage']['message'] ) ) {
    $view = file_get_contents( './templates/previewMessage.html' );
    $view = str_replace( '{message}', print_page( $_SESSION['viewMessage']['message'] ), $view ); 
    $html = $html.$view."\n";
    $theme   = htmlspecialchars( $_SESSION['viewMessage']['theme'] );
    $message = htmlspecialchars( $_SESSION['viewMessage']['message'] );
    unset( $_SESSION['viewMessage'] );
  }
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['addThemeForm'] ) ) {
    $info = file_get_contents( './templates/infoMessage.html' );
    $info = str_replace( '{infoMessage}', $_SESSION['addThemeForm']['error'], $info );
    $html = $html.$info."\n";
    $theme   = htmlspecialchars( $_SESSION['addThemeForm']['theme'] );
    $message = htmlspecialchars( $_SESSION['addThemeForm']['message'] );
    unset( $_SESSION['addThemeForm'] );
  }

  $action = $_SERVER['PHP_SELF'].'?action=addTheme&idForum='.$_GET['idForum'];
  $html = $html.file_get_contents( './templates/addThemeForm.html' ); 
  $html = str_replace( '{action}', $action, $html );
  $html = str_replace( '{theme}', $theme, $html );
  $html = str_replace( '{message}', $message, $html );
  
  
  
  $html.=var_dump($_SESSION);
  
  
  return $html;
}

// Функция добавляет новую тему (новую запись в таблицу БД TABLE_THEMES)
function addTheme()
{
		global $mysql;
		
  // На зарегистрированный пользователь не может добавить тему
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();  
  }
  // Если не переданы данные формы - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) or
	   !isset( $_POST['theme'] ) or
	   !isset( $_POST['message'] ) or
	   !isset( $_FILES['attach'] ) )
  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
	
  // Обрезаем переменные до длины, указанной в параметре maxlength тега input
  $theme   = substr( $_POST['theme'], 0, 128 );
  $message = substr( $_POST['message'], 0, MAX_POST_LENGTH );
  // Обрезаем лишние пробелы
  $theme   = trim( $theme );
  $message = trim( $message );

  // Если пользователь хочет посмотреть на сообщение перед отправкой
  if ( isset( $_POST['viewMessage'] ) ) {
    $_SESSION['viewMessage']['theme'] = $theme;
	$_SESSION['viewMessage']['message'] = $message;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=addThemeForm&idForum='.$_GET['idForum'] );
	die();
  }
  
  // Проверяем, заполнены ли обязательные поля
  $error = '';
  if ( empty( $theme ) ) $error = $error.'<li>не заполнено поле "Тема"</li>'."\n";
  if ( empty( $message ) ) $error = $error.'<li>не заполнено поле "Сообщение"</li>'."\n";
  // Проверяем поля формы на недопустимые символы
  if ( !empty( $theme ) and !preg_match( "#^[-.;:,?!\/)=(_\"\s0-9а-яА-Яa-z]+$#i", $theme ) )
	$error = $error.'<li>поле "Тема" содержит недопустимые символы</li>'."\n";
  if ( $_FILES['attach']['size'] > MAX_FILE_SIZE )
    $error = $error.'<li>Размер файла больше '.(MAX_FILE_SIZE/1024).' Кб</li>'."\n";
	
  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем посетителя для исправления ошибок
  if ( !empty( $error ) )
  {
	$_SESSION['addThemeForm'] = array();
	$_SESSION['addThemeForm']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'.
	                                     "\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['addThemeForm']['theme'] = $theme;
	$_SESSION['addThemeForm']['message'] = $message;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=addThemeForm&idForum='.$_GET['idForum'] );
	die();
  }
  $res = $mysql->query("SELECT login, id
			FROM users
			WHERE id=?i AND remote_addr=INET_ATON(?s) AND confirm_hash=?s",
			$_COOKIE['id'], $_COOKIE['ip'],$_COOKIE['hash']);
  $user = mysqli_fetch_assoc($res);
  // Формируем SQL-запрос на добавление темы
  $res = $mysql->query ("INSERT INTO ".TABLE_THEMES." 
                  VALUES (NULL,?s,?s,?i, NOW(), ?i, ?s, NOW(),?i, 0 )"
				  ,$theme,$user['login'],$user['id'],$user['id'],$user['login'],$_GET['idForum']);
  // Выясняем первичный ключ только что добавленной записи -
  // это понадобится для добавления сообщения (поста) и файла
  $id_theme = $mysql->insertId();
 
  // Если поле выбора файла(рисунка) не пустое
  $file = '';
  if ( !empty($_FILES['attach']['name']) ) {
    // Проверяем не больше ли файл максимально допустимого размера
    if ( $_FILES['attach']['size'] <= MAX_FILE_SIZE ) { 
      // Проверяем, не является ли файл скриптом PHP или Perl, html; 
	  // если это так преобразуем его в формат .txt
      $extentions = array(".php",".phtml",".php3",".html",".htm",".pl");
      // Извлекаем из имени файла расширение
      $ext = strrchr( $_FILES['attach']['name'], "." ); 
      // Формируем путь к файлу    
      if ( in_array( $ext, $extentions ) )
        $file = $id_theme.'-'.date("YmdHis",time()).'.txt'; 
      else
        $file = $id_theme.'-'.date("YmdHis",time()).$ext; 
      // Перемещаем файл из временной директории сервера в
      // директорию /files Web-приложения
      if ( move_uploaded_file ( $_FILES['attach']['tmp_name'], './files/'.$file ) )
	    chmod( './files/'.$file, 0644 );
	}
  }

  // Формируем SQL-запрос на добавление сообщения
  $res = $mysql->query ("INSERT INTO ".TABLE_POSTS." 
            VALUES
            (
            NULL,
            ?s,
            ?s,
            ?s,
            ?i,
            NOW(),
			NOW(),
            0,
            ?i,
			0
			)",$message,$file,$user['login'],$user['id'],$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении новой темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  // Обновляем число оставленных сообщений и созданных тем
  $mysql->query ("UPDATE ".TABLE_USERS." SET themes=themes+1, posts=posts+1
        WHERE id = ?i",$user['id']);
	
  return showInfoMessage( 'Новая тема успешно добавлена', 
                          'action=showForum&idForum='.$_GET['idForum'] );
}

// Функция возвращает форму для редактирования темы; 
// ID форума и темы передаются методом GET
function getEditThemeForm()
{
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID темы - значит функция была вызвана по ошибке
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Получаем из БД информацию о редактируемой теме
  $res = $mysql->query ("SELECT name, author, id_forum FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для редактирования темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	  $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	  '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  } 
  if ( mysqli_num_rows( $res ) == 0 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'action=showForum&idForum='.$_GET['idForum'] );
	die();
  }
  $theme = mysqli_fetch_array( $res );
  $_GET['idForum'] = $theme['id_forum'];
  
  $html = '';
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['editThemeForm'] ) ) {
	$html = $html.$_SESSION['editThemeForm']['error'];
	$name = htmlspecialchars( $_SESSION['editThemeForm']['name'] );
	unset( $_SESSION['editThemeForm'] );
  } else {
	$name = htmlspecialchars( $theme['name'] );
  }
  
  // Формируем список форумов, чтобы можно было переместить тему в другой форум
  $res = $mysql->query ("SELECT id_forum, name FROM ".TABLE_FORUMS." WHERE 1 ORDER BY pos");
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для редактирования темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  $options = '';
  while ( $forum = mysqli_fetch_array( $res ) ) {
    if ( $forum['id_forum'] == $theme['id_forum'] )
	  $options = $options.'<option value="'.$forum['id_forum'].'" selected>'.$forum['name'].'</option>'."\n";
	else
	  $options = $options.'<option value="'.$forum['id_forum'].'">'.$forum['name'].'</option>'."\n";
  }
  
  $action = $_SERVER['PHP_SELF'].'?action=updateTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
  
  // Считываем в переменную файл шаблона, содержащего форму для редактирования темы
  $tpl = file_get_contents( './templates/editThemeForm.html' );
  
  $tpl = str_replace( '{action}', $action, $tpl );
  $tpl = str_replace( '{name}', htmlspecialchars( $theme['name'] ), $tpl );
  $tpl = str_replace( '{author}', htmlspecialchars( $theme['author'] ), $tpl );
  $tpl = str_replace( '{options}', $options, $tpl );
  
  $html = $html. $tpl;
  
  return $html;
}

// Функция обновляет информацию о теме (запись в таблице БД TABLE_THEMES);
// уникальный ID темы передается методом GET
function updateTheme()
{
		global $mysql;
  // Если не переданы данные формы - функция вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) or
       !isset( $_GET['id_theme'] ) or
	   !isset( $_POST['id_forum'] ) or
       !isset( $_POST['name'] ) )
  {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.$_GET['idForum'] );
	die();
  }
  $id_forum = (int)$_POST['id_forum'];
  if ( $id_forum < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.$_GET['idForum'] );
	die();
  }
  
  // Если тему пытается редактировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'action=showForum&idForum='.$_GET['idForum'] );
	die();
  }

  // Проверяем, имеет ли право этот пользователь редактировать тему
  if ( $_SESSION['user']['status'] == 'user' ) {
    $msg = 'У вас нет прав для редактирования темы';
    return showInfoMessage( $msg, 'action=showForum&idForum='.$_GET['idForum'] );
  }

  // Обрезаем переменные до длины, указанной в параметре maxlength тега input
  $name   = substr( $_POST['name'], 0, 128 );
  // Обрезаем лишние пробелы
  $name   = trim( $name );

  // Проверяем, заполнены ли обязательные поля
  $error = '';
  if ( empty( $name ) ) $error = $error.'<li>не заполнено поле "Тема"</li>'."\n";
  // Проверяем поля формы на недопустимые символы
  if ( !empty( $name ) and !preg_match( "#^[-.;:,?!/)=(_\"\s0-9а-яА-Яa-zA-Z]+$#i", $name ) )
	$error = $error.'<li>поле "Тема" содержит недопустимые символы</li>'."\n";
	
  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем пользователя для исправления ошибок
  if ( !empty( $error ) )
  {
	$_SESSION['editThemeForm'] = array();
	$_SESSION['editThemeForm']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'."\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['editThemeForm']['name'] = $name;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=editThemeForm&idForum='.
	        $_GET['idForum'].'&id_theme='.$id_theme );
	die();
  }
  
  // Если тема перемещается в другой форум, мы
  // должны проверить, что этот форум существует
  $tmp = '';
  if ( $id_forum != $_GET['idForum'] ) {
    $query = "SELECT id_forum FROM ".TABLE_FORUMS." WHERE 1";
    $res = mysql_query( $query );
    if ( !$res ) {
      $msg = 'Ошибка при обновлении темы';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
    }
    while ( $id = mysqli_fetch_row( $res ) ) $ids[] = $id[0];
    if ( !in_array( $id_forum, $ids ) ) 
	  return showInfoMessage( 'Ошибка при обновлении темы', 'action=showForum&idForum='.$_GET['idForum'] );
	else
	  $tmp = $mysql->parse(', id_forum=?i',$id_forum);
  }
  
  // Запрос на обновление темы
  $res = $mysql->query ("UPDATE ".TABLE_THEMES." 
            SET name=?s?p 
			WHERE id_theme=?i",$name,$tmp,$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при обновлении темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  } else {
    return showInfoMessage( 'Обновление темы прошло успешно', 'action=showForum&idForum='.$_GET['idForum'] );
  } 
}

// Функция удаляет тему; ID темы передается методом GET
function deleteTheme()
{
		global $mysql;
  // Если тему пытается удалить не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Только администратор или модератор может удалить тему
  if ( $_SESSION['user']['status'] == 'user' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID темы, которую надо удалить
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Выдаем пользователю сообщение с просьбой подтвердить свое
  // желание удалить тему
  if ( !isset( $_GET['confirm'] ) ) {
    $html = '<div align="center"><p>Вы действительно хотите удалить эту тему?</p>'."\n";
    $html = $html.'<input type="button" name="yes" value="Да" 
            onClick="document.location.href=\''.$_SERVER['PHP_SELF'].'?action=deleteTheme&idForum='.
		    $_GET['idForum'].'&id_theme='.$id_theme.'&confirm=yes\'" />&nbsp;&nbsp;'."\n";
    $html = $html.'<input type="button" name="no" value="Нет" 
            onClick="document.location.href=\''.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.
		    $_GET['idForum'].'\'" /></div>'."\n";
    $tpl = file_get_contents( './templates/infoMessage.html' );
    $tpl = str_replace( '{infoMessage}', $html, $tpl );
    return $tpl; 
  }
  
  // Это небольшой код для удаленния коллизий в БД;
  // каким-то образом во время тестирования форума
  // у меня появилось несколько тем, в которых не
  // было сообщений (постов)
  // Вообще, надо подумать об использовании InnoDB и Foreign Key
  $mysql->query ("DELETE FROM ".TABLE_THEMES." WHERE id_theme NOT IN (SELECT DISTINCT id_theme FROM ".TABLE_POSTS.")");
  $mysql->query ("DELETE FROM ".TABLE_POSTS." WHERE id_theme NOT IN (SELECT id_theme FROM ".TABLE_THEMES.")");
  
  // Сперва мы должны удалить все сообщения (посты) темы;
  // начнем с того, что удалим файлы вложений
  $res = $mysql->query ("SELECT putfile, id_author FROM ".TABLE_POSTS."
            WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при удалении темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	        $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	        '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  if ( mysqli_num_rows( $res ) > 0 ) {
    while( $file = mysqli_fetch_row( $res ) ) {
	  if ( !empty( $file[0] ) and is_file( './files/'.$file[0] ) ) unlink( is_file( './files/'.$file[0] ) );
	  // заодно обновляем таблицу TABLE_USERS - надо обновить поле posts (кол-во сообщений)
	  if ( $file[1] ) { 
	    // Здесь надо будет переделать - выполнять только один запрос
		// UPDATE users SET posts=posts-1 WHERE id_author IN (3, 5, 12);
	    $mysql->query ("UPDATE ".TABLE_USERS." SET posts=posts-1 WHERE id_author=?s",$file[1]);
      }
	}
  }
  
  // Продолжаем - удаляем сообщения (посты)
  $res = $mysql->query ("DELETE FROM ".TABLE_POSTS." WHERE id_theme=?i",$id_theme);
  if ( !$res ) 
  {
    $msg = 'Ошибка при удалении темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	        $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	        '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }

  // Обновляем таблицу TABLE_USERS - надо обновить поле themes
  $mysql->query ("UPDATE ".TABLE_USERS." SET themes=themes-1 
            WHERE id_author=(SELECT id_author FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme.")");
  
  // Теперь удаляем тему (запись в таблице TABLE_THEMES)
  $res = $mysql->query ("DELETE FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при удалении темы';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	        $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	        '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  
  return showInfoMessage( 'Тема удалена', 
                          'action=showForum&idForum='.$_GET['idForum'] );
}

// Закрыть тему
function lockTheme()
{
		global $mysql;
  // Если тему пытается закрыть не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Только администратор или модератор может закрыть тему
  if ( $_SESSION['user']['status'] == 'user' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID темы, которую надо закрыть
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  } 
  
  // Сначала заблокируем сообщения (посты) темы
  $res = $mysql->query ("UPDATE ".TABLE_POSTS." SET locked=1 WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при попытке заблокировать тему';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  } 
  // Теперь заблокируем тему
  $res = $mysql->query ("UPDATE ".TABLE_THEMES." SET locked=1 WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при попытке заблокировать тему';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
 
  return showInfoMessage( 'Тема закрыта', 
                          'action=showForum&idForum='.$_GET['idForum'] );
}

// Открыть тему
function unlockTheme()
{
  // Если тему пытается разблокировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Только администратор или модератор может разблокировать тему
  if ( $_SESSION['user']['status'] == 'user' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID темы, которую надо разблокировать
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Сначала разблокируем сообщения (посты) темы
  $res = $mysql->query ("UPDATE ".TABLE_POSTS." SET locked=0 WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при попытке разблокировать тему';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  } 
  // Теперь разблокируем тему
  $res = $mysql->query ("UPDATE ".TABLE_THEMES." SET locked=0 WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при попытке разблокировать тему';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
 
  return showInfoMessage( 'Тема открыта', 
                          'action=showForum&idForum='.$_GET['idForum'] ); 
}

// Функция возвращает форму для добавления нового сообщения (поста)
function getAddPostForm()
{
		global $mysql;
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID темы, куда будет добавлено сообщение - 
  // значит функция была вызвана по ошибке
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Проверяем, не заблокирована ли тема?
  $res = $mysql->query ("SELECT locked FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для добавления нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.$_GET['idForum'] );
	die();
  }
  $res_locked=mysqli_fetch_row($res);
  if ( $res_locked[0] == 1 )
    return showInfoMessage( 'Вы не можете добавить сообщение - тема заблокирована.', 
	                        'action=showForum&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  
  $message = '';
  $html = '';
  
  if ( isset( $_SESSION['viewMessage'] ) and !empty( $_SESSION['viewMessage'] ) ) {
    $view = file_get_contents( './templates/previewMessage.html' );
	$view = str_replace( '{message}', print_page( $_SESSION['viewMessage'] ), $view ); 
	$html = $html.$view."\n";
	$message = htmlspecialchars( $_SESSION['viewMessage'] );
	unset( $_SESSION['viewMessage'] );
  }
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['addPostForm'] ) ) {
    $info = file_get_contents( './templates/infoMessage.html' );
	$info = str_replace( '{infoMessage}', $_SESSION['addPostForm']['error'], $info );
	$html = $html.$info."\n";
    $message = htmlspecialchars( $_SESSION['addPostForm']['message'] );
    unset( $_SESSION['addPostForm'] );
  }
  
  $tpl = file_get_contents( './templates/addPostForm.html' );
  $action = $_SERVER['PHP_SELF'].'?action=addPost&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
  $tpl = str_replace( '{action}', $action, $tpl );
  $tpl = str_replace( '{message}', $message, $tpl );
  
  $html = $html . $tpl;
  
  return $html."\n";
}

// Функция добавляет новое сообщение(пост) (новую запись в таблицу БД TABLE_POSTS)
function addPost()
{	
  global $mysql;  
  // Если не переданы данные формы - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) or
       !isset( $_GET['id_theme'] ) or
       !isset( $_POST['message'] ) or
       !isset( $_FILES['attach'] ) 
	   )
  {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Проверяем, не заблокирована ли тема?
  $res = $mysql->query ("SELECT locked FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для добавления нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.$_GET['idForum'] );
	die();
  }
  $res_locked = mysqli_fetch_row($res);
  if ( $res_locked[0] == 1 )
    return showInfoMessage( 'Вы не можете добавить сообщение - тема заблокирована.', 
	                        'action=showForum&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );

  $msgLen = strlen( $_POST['message'] );
							
  // Обрезаем сообщение (пост) до длины MAX_POST_LENGTH
  $message = substr( $_POST['message'], 0, MAX_POST_LENGTH );
  // Обрезаем лишние пробелы
  $message = trim( $message );
  // Если пользователь хочет посмотреть на сообщение перед отправкой
  if ( isset( $_POST['viewMessage'] ) ) 
  {
	$_SESSION['viewMessage'] = $message;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=addPostForm&idForum='.
	        $_GET['idForum'].'&id_theme='.$id_theme );
	die();
  }

  // Проверяем, правильно ли заполнены поля формы
  $error = '';
  if ( empty( $message ) ) $error = $error.'<li>не заполнено поле "Сообщение"</li>'."\n";
  if ( $msgLen > MAX_POST_LENGTH ) 
    $error = $error.'<li>длина сообщения больше '.MAX_POST_LENGTH.' символов</li>'."\n";
  if ( !empty( $_FILES['attach']['name'] ) and $_FILES['attach']['size'] > MAX_FILE_SIZE ) 
    $error = $error.'<li>размер файла вложения больше '.(MAX_FILE_SIZE/1024).' Кб</li>'."\n";
	
  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем пользователя для исправления ошибок
  if ( !empty( $error ) )
  {
	$_SESSION['addPostForm'] = array();
	$_SESSION['addPostForm']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'."\n".
    '<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['addPostForm']['message'] = $message;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=addPostForm&idForum='.
	        $_GET['idForum'].'&id_theme='.$id_theme );
	die();
  }
  $file = '';
  if ( !empty( $_FILES['attach']['name'] ) ) {
    // Массив недопустимых расширений файла вложения
    $extentions = array('.php', '.phtml', '.php3', '.html', '.htm', '.pl');
    // Извлекаем из имени файла расширение
    $ext = strrchr( $_FILES['attach']['name'], "." ); 
    // Формируем путь к файлу    
    if ( in_array( $ext, $extentions ) )
      $file = $id_theme.'-'.date("YmdHis",time()).'.txt'; 
    else
      $file = $id_theme.'-'.date("YmdHis",time()).$ext; 
    // Перемещаем файл из временной директории сервера в директорию files
      if ( move_uploaded_file ( $_FILES['attach']['tmp_name'], './files/'.$file ) )
	    chmod( './files/'.$file, 0644 );
  }
  
  if ( isset( $_SESSION['user'] ) ) {
  	$res = $mysql->query("SELECT login FROM users WHERE id=?i AND remote_addr=INET_ATON(?s) AND confirm_hash=?s",$_COOKIE['id'],$_COOKIE['ip'],$_COOKIE['hash']);
  	$row = mysqli_fetch_assoc($res);
  	$name = $row['login'];
  	$id_user = $_SESSION['id'];
  } else {
    $name = NOT_REGISTERED_USER;
    $id_user = 0;
  }

  // Защита от того, чтобы один пользователь не добавил 
  // 100 сообщений за одну минуту
  if ( isset( $_SESSION['unix_last_post'] ) and ( time()-$_SESSION['unix_last_post'] < 10 ) ) {
    return showInfoMessage( 'Ваше сообщение уже было добавлено', 
                            'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
 
  // Все поля заполнены правильно - выполняем запрос к БД
  $res = $mysql->query ("INSERT INTO ".TABLE_POSTS."
            (
			name,
			putfile,
			author,
			id_author,
			time,
			id_theme
			)
			VALUES
			(
			?s,
			?s,
			?s,
			?i,
			NOW(),
			?i
			)",$message,$file,$name,$id_user,$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
  $res = $mysql->query ("UPDATE ".TABLE_THEMES." 
            SET 
			id_last_author=?i,
			last_author=?s,
			last_post=NOW()
			WHERE id_theme=?i",$id_user,$name,$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
  
  // Добавляем в массив $_SESSION	время последнего сообщения;
  // Это нужно для того, чтобы один пользователь не добавил 
  // 100 сообщений за одну минуту
  $_SESSION['unix_last_post'] = time();
  
  // Обновляем количество сообщений для зарегистрированного пользователя
  if ( isset( $_SESSION['user'] ) ) {
	$res = $mysql->query ("UPDATE ".TABLE_USERS." SET posts=posts+1 WHERE id = ?i",$_SESSION['id']);
    if ( !$res ) {
      $msg = 'Ошибка при добавлении нового сообщения';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 
	                           'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
    }
  }

  return showInfoMessage( 'Ваше сообщение успешно добавлено', 
                          'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
}

// Функция добавляет новое сообщение(пост) (новую запись в таблицу БД TABLE_POSTS)
function quickReply()
{
	global $mysql;
  // Если не переданы данные формы - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) or
       !isset( $_GET['id_theme'] ) or
       !isset( $_POST['message'] )
	   )
  {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  // Проверяем, не заблокирована ли тема?
  $res = $mysql->query ("SELECT locked FROM ".TABLE_THEMES." WHERE id_theme=?i",$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для добавления нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showForum&idForum='.$_GET['idForum'] );
	die();
  }
  $res_locked=mysqli_fetch_row($res);
  if ( $res_locked[0] == 1 )
    return showInfoMessage( 'Вы не можете добавить сообщение - тема заблокирована.', 
	                        'action=showForum&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );

  if ( isset( $_SESSION['user'] ) ) {
  	$res = $mysql->query("SELECT login FROM users WHERE id=?i AND remote_addr=INET_ATON(?s) AND confirm_hash=?s",$_COOKIE['id'],$_COOKIE['ip'],$_COOKIE['hash']);
    $row = mysqli_fetch_assoc($res);
  	$name = $row['login'];
	$id_user = $_SESSION['id'];
  } else {
    $name = NOT_REGISTERED_USER;
    $id_user = 0;
  }
  // Обрезаем сообщение (пост) до длины MAX_POST_LENGTH
  $message = substr( $_POST['message'], 0, MAX_POST_LENGTH );
  // Обрезаем лишние пробелы
  $message = trim( $message );

  // Проверяем, правильно ли заполнены поля формы
  if ( empty( $message ) ) 
	  return showInfoMessage( 'Не заполнено поле "Сообщение"', 
                              'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );

  // Защита от того, чтобы один пользователь не добавил 
  // 100 сообщений за одну минуту
  if ( isset( $_SESSION['unix_last_post'] ) and ( time()-$_SESSION['unix_last_post'] < 10 ) ) {
    return showInfoMessage( 'Ваше сообщение уже было добавлено несколькими секундами ранее', 
                            'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
							  
  // Все поля заполнены правильно - выполняем запрос к БД
  $res = $mysql->query ("INSERT INTO ".TABLE_POSTS."
            (
			name,
			putfile,
			author,
			id_author,
			time,
			id_theme
			)
			VALUES
			(
			?s,
			'',
			?s,
			?i,
			NOW(),
			?i
			)",$message,$name,$id_user,$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
  $res = $mysql->query ("UPDATE ".TABLE_THEMES." 
            SET 
			id_last_author=?i,
			last_author=?s,
			last_post=NOW()
			WHERE id_theme=?i",$id_user,$name,$id_theme);
  if ( !$res ) {
    $msg = 'Ошибка при добавлении нового сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
  
  // Добавляем в массив $_SESSION время последнего сообщения;
  // Это нужно для того, чтобы один пользователь не добавил 
  // 100 сообщений за одну минуту
  $_SESSION['unix_last_post'] = time();
 
  // Обновляем количество сообщений для зарегистрированного пользователя
  if ( isset( $_SESSION['user'] ) ) {
	$res = $mysql->query ("UPDATE ".TABLE_USERS." SET posts=posts+1 WHERE id = ?i",$_SESSION['id']);
    if ( !$res ) {
      $msg = 'Ошибка при добавлении нового сообщения';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 
	                           'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
    }	
  }

  return showInfoMessage( 'Ваше сообщение успешно добавлено', 
                          'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
}

// Функция возвращает форму для редактирования сообщения(поста)
function getEditPostForm()
{
	global $mysql;
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID темы - значит функция была вызвана по ошибке
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не передан ID сообщения - значит функция была вызвана по ошибке
  if ( !isset( $_GET['id_post'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_post = (int)$_GET['id_post'];
  if ( $id_post < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }

  // Если сообщение пытается редактировать не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }

  // Получаем из БД сообщение
  $res = $mysql->query ("SELECT name, putfile, id_author, author, time, id_theme, locked 
            FROM ".TABLE_POSTS." 
			WHERE id_post=?i",$id_post);
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для редактирования сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  // Если сообщение не найдено - редирект на страницу темы
  if ( mysqli_num_rows( $res ) == 0 ) 
    return showInfoMessage( 'Ошибка при формировании формы для редактирования сообщения', 
	                        'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
	
  $post = mysqli_fetch_array( $res ); 
  $id_theme = $post['id_theme']; 
  
  // Проверяем, имеет ли пользователь право редактировать это сообщение (пост)
  if ( !hasRightEditPost( $post ) ) {
    $msg = 'У вас нет прав для редактирования этого сообщения';
	$queryString = 'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
    return showInfoMessage( $msg, $queryString );
  }	
  
  $message = htmlspecialchars( $post['name'] );
  $html = '';
  
  if ( isset( $_SESSION['viewMessage'] ) and !empty( $_SESSION['viewMessage'] ) ) {
    $view = file_get_contents( './templates/previewMessage.html' );
	$view = str_replace( '{message}', print_page( $_SESSION['viewMessage'] ), $view ); 
	$html = $html.$view."\n";
	$message = htmlspecialchars( $_SESSION['viewMessage'] );
	unset( $_SESSION['viewMessage'] );
  }
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['editPostForm'] ) ) {
    $html    = $html . $_SESSION['editPostForm']['error'];
    $message = htmlspecialchars( $_SESSION['editPostForm']['message'] );
    unset( $_SESSION['editPostForm'] );
  }
  
  $tpl = file_get_contents( './templates/editPostForm.html' );

  $action = $_SERVER['PHP_SELF'].'?action=updatePost&idForum='.
            $_GET['idForum'].'&id_theme='.$id_theme.'&id_post='.$id_post;
  $tpl = str_replace( '{action}', $action, $tpl );
  $tpl = str_replace( '{message}', $message, $tpl );
  // Если ранее был загружен файл - надо предоставить возможность удалить его
  $unlinkfile = '';

  if ( !empty( $post['putfile'] ) and is_file( './files/'.$post['putfile'] ) ) {
    $unlinkfile = '<input type="checkbox" name="unlink" value="1" />&nbsp;Удалить загруженный ранее файл<br/>'."\n";
  }
  $tpl = str_replace( '{unlinkfile}', $unlinkfile, $tpl );
  $html = $html . $tpl;
  
  return $html."\n";
}

function updatePost()
{
  // Если не переданы данные формы - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) or
       !isset( $_GET['id_post'] ) or
       !isset( $_POST['message'] ) or
       !isset( $_FILES['attach'] ) 
    )
  {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  $id_post = (int)$_GET['id_post'];
  if ( $id_post < 1 ) {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }

  // Проверяем, имеет ли пользователь право редактировать это сообщение (пост)
  $res = $mysql->query ("SELECT * FROM ".TABLE_POSTS." WHERE id_post=?i",$id_post);
  if ( !$res ) {
    $msg = 'Ошибка при обновлении сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    $msg = 'Ошибка при обновлении сообщения: сообщение не найдено';
    return showInfoMessage( $msg, '' );
  }
  $post = mysqli_fetch_array( $res );
  $id_theme = $post['id_theme'];
  if ( !hasRightEditPost( $post ) ) {
    $msg = 'У вас нет прав для редактирования этого сообщения';
	$queryString = 'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
    return showInfoMessage( $msg, $queryString );
  }

  // Обрезаем сообщение до длины MAX_POST_LENGTH
  $message = substr( $_POST['message'], 0, MAX_POST_LENGTH );
  // Обрезаем лишние пробелы
  $message = trim( $message );

  // Если пользователь хочет посмотреть на сообщение перед отправкой
  if ( isset( $_POST['viewMessage'] ) ) 
  {
	$_SESSION['viewMessage'] = $message;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=editPostForm&idForum='.
	        $_GET['idForum'].'&id_theme='.$id_theme.'&id_post='.$id_post );
	die();
  }
  
  // Проверяем, правильно ли заполнены поля формы
  $error = '';
  if ( empty( $message ) ) $error = $error.'<li>не заполнено поле "Сообщение"</li>'."\n";
  if ( !empty( $_FILES['attach']['name'] ) and $_FILES['attach']['size'] > MAX_FILE_SIZE ) 
      $error = $error.'<li>размер файла вложения больше '.(MAX_FILE_SIZE/1024).' Кб</li>'."\n";
	
  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем посетителя для исправления ошибок
  if ( !empty( $error ) ) {
	$_SESSION['editPostForm'] = array();
	$_SESSION['editPostForm']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'."\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['editPostForm']['message'] = $message;
	header( 'Location: '.$_SERVER['PHP_SELF'].'?action=editPostForm&idForum='.
	         $_GET['idForum'].'&id_theme='.$id_theme.'&id_post='.$id_post );
	die();
  }
  
  $file = $post['putfile'];
  // Такой ситуации быть не должно, но я случайтно удалил файл вложения
  // вручную, и после этого нельзя было правильно загрузить файл
  if ( !empty( $file ) and !is_file( './files/'.$post['putfile'] ) ) $file = '';
  // Если выставлен флажок "Удалить загруженный ранее файл"
  if ( isset( $_POST['unlink'] ) and !empty( $file ) and is_file( './files/'.$post['putfile'] ) ) {
    if ( unlink( './files/'.$post['putfile'] ) ) $file = '';
  }
  if ( !empty( $_FILES['attach']['name'] ) ) {
    // Если пользователь загружает новый файл - мы должны сперва удалить старый
	// (при условии, что он вообще был загружен ранее)
    if ( !empty( $file ) and is_file( './files/'.$post['putfile'] ) ) {
      if ( unlink( './files/'.$post['putfile'] ) ) $file = '';	  
	}
	// Загружать новый файл мы будем только при условии, что был успешно
	// удален ранее загруженный (или он не загружался вовсе)
	if ( empty( $file ) ) {
      // Массив недопустимых расширений файла вложения
      $extentions = array('.php', '.phtml', '.php3', '.html', '.htm', '.pl');
      // Извлекаем из имени файла расширение
      $ext = strrchr( $_FILES['attach']['name'], "." ); 
      // Формируем путь к файлу    
      if ( in_array( $ext, $extentions ) )
        $new = $id_theme.'-'.date("YmdHis",time()).'.txt'; 
      else
        $new = $id_theme.'-'.date("YmdHis",time()).$ext; 
      // Перемещаем файл из временной директории сервера в директорию files
      if ( move_uploaded_file ( $_FILES['attach']['tmp_name'], './files/'.$new ) ) {
	    chmod( './files/'.$new, 0644 );
	    $file = $new;
	  }
	}
  }
  
  // Все поля заполнены правильно - выполняем запрос к БД
  $res = $mysql->query ("UPDATE ".TABLE_POSTS." SET
			name=?s,
			putfile=?s,
			id_editor=?i,
			edittime=NOW()
			WHERE id_post=?i",$message,$file,$_SESSION['user']['id_author'],$id_post);
  if ( !$res ) {
    $msg = 'Ошибка при обновлении сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	  $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	  '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	$queryString = 'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
	return showErrorMessage( $msg, $err, true, $queryString );
  } 
    
  $msg = 'Cообщение успешно исправлено';
  $queryString = 'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
  return showInfoMessage( $msg, $queryString );
  
}

// Функция удаляет сообщение (пост)
function deletePost()
{
	global $mysql;
  // Если не передан ID форума - значит функция была вызвана по ошибке
  if ( !isset( $_GET['idForum'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не прередан ID темы - значит функция вызвана по ошибке
  if ( !isset( $_GET['id_theme'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_theme = (int)$_GET['id_theme'];
  if ( $id_theme < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не прередан ID сообщения (поста) - значит функция вызвана по ошибке
  if ( !isset( $_GET['id_post'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  $id_post = (int)$_GET['id_post'];
  if ( $id_post < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  } 

  // Выдаем пользователю сообщение с просьбой подтвердить свое
  // желание удалить сообщение (пост)
  if ( !isset( $_GET['confirm'] ) ) {
    $html = '<div align="center"><p>Вы действительно хотите удалить это сообщение?</p>'."\n";
    $html = $html.'<input type="button" name="yes" value="Да" 
            onClick="document.location.href=\''.$_SERVER['PHP_SELF'].'?action=deletePost&idForum='.
		    $_GET['idForum'].'&id_theme='.$id_theme.'&id_post='.$id_post.
		    '&confirm=yes\'" />&nbsp;&nbsp;'."\n";
    $html = $html.'<input type="button" name="no" value="Нет" 
            onClick="document.location.href=\''.$_SERVER['PHP_SELF'].'?action=showTheme&idForum='.
		    $_GET['idForum'].'&id_theme='.$id_theme.'\'" /></div>'."\n";
    $tpl = file_get_contents( './templates/infoMessage.html' );
    $tpl = str_replace( '{infoMessage}', $html, $tpl );
    return $tpl; 
  }
  
  // Получаем из БД информацию об удаляемом сообщении - это нужно,
  // чтобы узнать, имеет ли право пользователь удалить это сообщение
  $res = $mysql->query ("SELECT * FROM ".TABLE_POSTS." WHERE id_post=?i",$id_post);
  if ( !$res ) {
    $msg = 'Ошибка при удалении сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	  $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	  '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showForum&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    return showInfoMessage( 'Сообщение успешно удалено', 
	                        'action=showForum&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  } 
  $post = mysqli_fetch_array( $res );   
  if ( !hasRightDeletePost( $post ) ) {
    return showInfoMessage( 'У вас нет прав, чтобы удалить это сообщение', 
	                        'action=showForum&idForum='.$_GET['idForum'].'&id_theme='.$id_theme );
  }
  
  // Удаляем файл, если он есть
  if ( !empty( $post['putfile'] ) and is_file( './files/'.$post['putfile'] ) )
    unlink( './files/'.$post['putfile'] );
  $res = $mysql->query ("DELETE FROM ".TABLE_POSTS." WHERE id_post=?i",$id_post);
  if ( !$res ) {
    $msg = 'Ошибка при удалении сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showTheme&idForum='.$_GET['idForum'].'&id_theme='.$post['id_theme'] );
  }
  // Если это - единственное сообщение темы, то надо удалить и тему
  $res = $mysql->query ("SELECT COUNT(*) FROM ".TABLE_POSTS." WHERE id_theme=?i",$post['id_theme']);
  if ( !$res ) {
    $msg = 'Ошибка при удалении сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showForum&idForum='.$_GET['idForum'] );
  }
  $res_count=mysqli_fetch_row($res);
  if ( $res_count == 0 ) {
    // Прежде чем удалять тему, надо обновить таблицу TABLE_USERS
	$res = $mysql->query ("UPDATE ".TABLE_USERS." 
	          SET themes=themes-1 
			  WHERE id_author=(SELECT id_author FROM ".TABLE_THEMES." WHERE id_theme=?i)",$post['id_theme']);
    if ( !$res ) {
      $msg = 'Ошибка при удалении сообщения';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	        '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
    }
    $res = $mysql->query ("DELETE FROM ".TABLE_THEMES." WHERE id_theme=?i",$post['id_theme']);
    if ( !$res ) {
      $msg = 'Ошибка при удалении сообщения';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	        '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.$_GET['idForum'] );
    }
	// Если мы удалили тему, то мы не можем в нее вернуться;
	// поэтому редирект будет на страницу форума, а не страницу темы
	$deleteTheme = true;
  } 
  
  // Обновляем количество сообщений, оставленных автором сообщения ...
  $res = $mysql->query ("UPDATE ".TABLE_USERS." SET posts=posts-1 WHERE id=?i",$post['id_author']);
  if ( !$res ) {
    $msg = 'Ошибка при удалении сообщения';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 
	                         'action=showForum&idForum='.$_GET['idForum'] );
  }
  // ... и таблицу TABLE_THEMES
  if ( !isset( $deleteTheme ) ) {
    $res = $mysql->query ("SELECT id_author, author, time
	          FROM ".TABLE_POSTS."
			  WHERE id_theme=?i 
			  ORDER BY id_post DESC
			  LIMIT 1",$post['id_theme']);
    if ( !$res ) {
      $msg = 'Ошибка при удалении сообщения';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.
	                           $_GET['idForum'].'&id_theme='.$post['id_theme'] );
    }
	list( $id_last_author, $last_author, $last_post ) = mysqli_fetch_row( $res );
	$res = $mysql->query ("UPDATE ".TABLE_THEMES." 
	          SET id_last_author=?i, last_author=?s, last_post=?s
			  WHERE id_theme=",$id_last_author,$last_author,$last_post,$post['id_theme']);
    if ( !$res ) {
      $msg = 'Ошибка при удалении сообщения';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, true, 'action=showForum&idForum='.
	                           $_GET['idForum'].'&id_theme='.$post['id_theme'] );
    }  
  }
	
  if ( isset( $deleteTheme ) ) {
    return showInfoMessage( 'Сообщение успешно удалено', 'action=showForum&idForum='.$_GET['idForum'] );
  } else {
    return showInfoMessage( 'Сообщение успешно удалено', 'action=showTheme&idForum='.
                            $_GET['idForum'].'&id_theme='.$post['id_theme'] ); 
  }
}


// Функция возвращает html формы для редактирования данных о пользователе
// (только для администратора форума)
function getEditUserFormByAdmin()
{
	global $mysql;
  // Если не передан ID пользователя - значит функция вызвана по ошибке
  if ( !isset( $_GET['idUser'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showUsersList' );
	die();
  }
  $id = (int)$_GET['idUser'];
  // ID зарегистрированного пользователя не может быть меньше 
  // единицы - значит функция вызвана по ошибке
  if ( $id < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showUsersList' );
	die();
  }
  // Если информацию о пользователе пытается редактировать 
  // не зарегистрированный пользователь
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Только администратор имеет право на эту операцию
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  
  $statusArray = array( 'user' => 'Пользователь',
                        'moderator' => 'Модератор',
				        'admin' => 'Администратор' );  
  $html = '';
  
  // Если при заполнении формы были допущены ошибки
  if ( isset( $_SESSION['editUserFormByAdmin'] ) ) {
    $info = file_get_contents( './templates/infoMessage.html' );
	$info = str_replace( '{infoMessage}', $_SESSION['editUserFormByAdmin']['error'], $info );
    $html = $html.$info."\n";
	$name      = htmlspecialchars( $_SESSION['editUserFormByAdmin']['name'] );
	$status    = $_SESSION['editUserFormByAdmin']['status'];
    $email     = htmlspecialchars( $_SESSION['editUserFormByAdmin']['email'] );
	$oldEmail  = htmlspecialchars( $_SESSION['editUserFormByAdmin']['oldEmail'] );
    $timezone  = $_SESSION['editUserFormByAdmin']['timezone'];
    $icq       = htmlspecialchars( $_SESSION['editUserFormByAdmin']['icq'] );
    $url       = htmlspecialchars( $_SESSION['editUserFormByAdmin']['url'] );
    $about     = htmlspecialchars( $_SESSION['editUserFormByAdmin']['about'] );
    $signature = htmlspecialchars( $_SESSION['editUserFormByAdmin']['signature'] );
    unset( $_SESSION['editUserFormByAdmin'] );
  } else {
    // Получаем данные о пользователе из БД
    $res = $mysql->query ("SELECT id_author, name, email, url, icq, about, photo, status, timezone, signature 
              FROM ".TABLE_USERS." 
              WHERE id_author=?i",$id);
    if ( !$res ) {
      $msg = 'Ошибка при получении информации о пользователе';
	  $err = 'Ошибка при выполнении запроса: <br/>'.
	         $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	         '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	  return showErrorMessage( $msg, $err, 'action=showUsersList', true );
    }
    if ( mysqli_num_rows( $res ) == 0 ) 
      return showInfoMessage( 'Пользователь не найден', 'action=showUsersList' );
    $user = mysqli_fetch_array( $res );
	
	$name      = htmlspecialchars( $user['name'] );
	$status    = $user['status'];
	$email     = htmlspecialchars( $user['email'] );
	$oldEmail  = $email;
    $timezone  = $user['timezone'];
    $icq       = htmlspecialchars( $user['icq'] );
    $url       = htmlspecialchars( $user['url'] );
    $about     = htmlspecialchars( $user['about'] );
    $signature = htmlspecialchars( $user['signature'] );
  }

  $userStatus = '<select name="status">'."\n";
  foreach( $statusArray as $key => $value ) {
    if ( $key == $status )
	  $userStatus = $userStatus.'<option value="'.$key.'" selected>'.$value.'</option>'."\n";
	else
	  $userStatus = $userStatus.'<option value="'.$key.'">'.$value.'</option>'."\n";
  }
  $userStatus = $userStatus.'</select>'."\n";
  
  $action = $_SERVER['PHP_SELF'].'?action=updateUserByAdmin&idUser='.$id;
  
  $tpl = file_get_contents( './templates/editUserFormByAdmin.html' );
  $tpl = str_replace( '{action}', $action, $tpl );
  $tpl = str_replace( '{name}', htmlspecialchars( $name ), $tpl );
  $tpl = str_replace( '{status}', $userStatus, $tpl );
  $tpl = str_replace( '{email}', htmlspecialchars( $email ), $tpl );
  $tpl = str_replace( '{icq}', htmlspecialchars( $icq ), $tpl );
  $tpl = str_replace( '{url}', htmlspecialchars( $url ), $tpl );
  $tpl = str_replace( '{about}', htmlspecialchars( $about ), $tpl );
  $tpl = str_replace( '{signature}', htmlspecialchars( $signature ), $tpl );
  
  $options = '';
  for ( $i = -12; $i <= 12; $i++ ) {
    if ( $i < 1 ) 
	  $value = $i.' часов';
	else
	  $value = '+'.$i.' часов';
    if ( $i == $timezone )
      $options = $options . '<option value="'.$i.'" selected>'.$value.'</option>'."\n";
    else
      $options = $options . '<option value="'.$i.'">'.$value.'</option>'."\n";
  }
  $tpl = str_replace( '{options}', $options, $tpl);
  $tpl = str_replace( '{servertime}', date( "d.m.Y H:i:s" ), $tpl );
  // Если ранее был загружен файл - надо предоставить возможность удалить его
  $unlinkfile = '';
  if ( is_file( './photo/'.$id ) ) {
    $unlinkfile = '<br/><input type="checkbox" name="unlink" value="1" />
	              Удалить загруженный ранее файл'."\n";
  }
  $tpl = str_replace( '{unlinkfile}', $unlinkfile, $tpl );
  
  $html = $html.$tpl;
  
  return $html;
}

// Функция обновляет данные пользователя (только для администратора форума)
function updateUserByAdmin()
{
		global $mysql;
  // Если не передан ID пользователя - значит функция вызвана по ошибке
  if ( !isset( $_GET['idUser'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showUsersList' );
	die();
  }
  $id = (int)$_GET['idUser'];
  // ID зарегистрированного пользователя не может быть меньше 
  // единицы - значит функция вызвана по ошибке
  if ( $id < 1 ) {
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=showUsersList' );
	die();
  }
  // Если профиль пытается редактировать не зарегистрированный 
  // пользователь - функция вызвана по ошибке
  if ( !isset( $_SESSION['user'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();
  }
  // Только администратор имеет право на эту операцию
  if ( $_SESSION['user']['status'] != 'admin' ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }
  // Если не переданы данные формы - функция вызвана по ошибке
  if ( !isset( $_POST['name'] ) or
       !isset( $_POST['status'] ) or
	   !isset( $_POST['email'] ) or
	   !isset( $_POST['oldEmail'] ) or
       !isset( $_POST['newpassword'] ) or
	   !isset( $_POST['confirm'] ) or
       !isset( $_POST['timezone'] ) or
       !isset( $_POST['icq'] ) or
       !isset( $_POST['url'] ) or
       !isset( $_POST['about'] ) or
       !isset( $_POST['signature'] ) or
       !isset( $_FILES['avatar'] ) 
    )
  {
	header( 'Location: '.$_SERVER['PHP_SELF'] );
	die();
  }

  // Обрезаем переменные до длины, указанной в параметре maxlength тега input
  $email        = substr( $_POST['email'], 0, 60 );
  $oldEmail     = substr( $_POST['oldEmail'], 0, 60 );
  $newpassword  = substr( $_POST['newpassword'], 0, 30 );
  $confirm      = substr( $_POST['confirm'], 0, 30 );
  $icq          = substr( $_POST['icq'], 0, 12 );
  $url          = substr( $_POST['url'], 0, 60 );
  $about        = substr( $_POST['about'], 0, 1000 );
  $signature    = substr( $_POST['signature'], 0, 500 );

  // Обрезаем лишние пробелы
  $email        = trim( $email );
  $oldEmail     = trim( $oldEmail );
  $newpassword  = trim( $newpassword );
  $confirm      = trim( $confirm );
  $icq          = trim( $icq );
  $url          = trim( $url );
  $about        = trim( $about );
  $signature    = trim( $signature );

  // Проверяем, заполнены ли обязательные поля
  $error = '';
  
  // Надо выяснить, что хочет сделать администратор: 
  // поменять e-mail, изменить пароль или и то и другое
  $changePassword = false;
  $changeEmail = false;
	
  if ( !empty( $newpassword ) ) { // хочет изменить пароль
	$changePassword = true;
    if ( empty( $confirm ) ) $error = $error.'<li>не заполнено поле "Подтвердите пароль"</li>'."\n";
    // Проверяем, не слишком ли короткий новый пароль
    if ( strlen( $newpassword ) < MIN_PASSWORD_LENGTH )
      $error = $error.'<li>длина пароля должна быть не меньше '.MIN_PASSWORD_LENGTH.' символов</li>'."\n";
    // Проверяем, совпадают ли пароли
    if ( !empty( $confirm ) and $newpassword != $confirm ) 
      $error = $error.'<li>не совпадают пароли</li>'."\n";
    // Проверяем поля формы на недопустимые символы
    if (  !preg_match( "#^[-_0-9a-z]+$#i", $newpassword ) )
      $error = $error.'<li>поле "Новый пароль" содержит недопустимые символы</li>'."\n";
    if ( !empty( $confirm ) and !preg_match( "#^[-_0-9a-z]+$#i", $confirm ) )
      $error = $error.'<li>поле "Подтвердите пароль" содержит недопустимые символы</li>'."\n";
  }
  if ( $email != $oldEmail ) { // хочет изменить e-mail
	$changeEmail = true;
    if ( empty( $email ) ) $error = $error.'<li>не заполнено поле "Адрес e-mail"</li>'."\n";
    // Проверяем корректность e-mail
    if ( !empty( $email ) and !preg_match( "#^[0-9a-z_\-\.]+@[0-9a-z\-\.]+\.[a-z]{2,6}$#i", $email ) )
      $error = $error.'<li>поле "Адрес e-mail" должно соответствовать формату 
		       somebody@somewhere.ru</li>'."\n";	
  }    
  
  // Проверяем поля формы на недопустимые символы
  if ( !empty( $icq ) and !preg_match( "#^[0-9]+$#", $icq ) )
    $error = $error.'<li>поле "ICQ" содержит недопустимые символы</li>'."\n";
  if ( !empty( $about ) and !preg_match( "#^[-\[\].;:,?!\/)(_\"\s0-9а-яА-Яa-z]+$#i", $about ) )
    $error = $error.'<li>поле "Интересы" содержит недопустимые символы</li>'."\n";
  if ( !empty( $signature ) and !preg_match( "#^[-\[\].;:,?!\/)(_\"\s0-9а-яА-Яa-z]+$#i", $signature ) )
    $error = $error.'<li>поле "Подпись" содержит недопустимые символы</li>'."\n";
	
  // Проверяем корректность URL домашней странички	
  if ( !empty( $url ) and !preg_match( "#^(http:\/\/)?(www.)?[-0-9a-z]+\.[a-z]{2,6}\/?$#i", $url ) )
    $error = $error.'<li>поле "Домашняя страничка" должно соответствовать формату http://www.homepage.ru</li>'."\n";

  if ( !empty( $_FILES['avatar']['name'] ) ) {
    $ext = strrchr( $_FILES['avatar']['name'], "." );
    $extensions = array( ".jpg", ".gif", ".bmp", ".png" );
    if ( !in_array( $ext, $extensions ) ) 
	  $error = $error.'<li>недопустимый формат файла аватара</li>'."\n";
    if ( $_FILES['avatar']['size'] > MAX_AVATAR_SIZE ) 
      $error = $error.'<li>размер файла аватора больше '.(MAX_AVATAR_SIZE/1024).' Кб</li>'."\n";
  }

  $statusArray = array( 'user' => 'Пользователь',
                        'moderator' => 'Модератор',
				        'admin' => 'Администратор' ); 
  if ( in_array( $_POST['status'], $statusArray ) )
    $status = $_POST['status'];
  else
    $status = 'user';
  
  $timezone = (int)$_POST['timezone'];
  if ( $timezone < -12 or $timezone > 12 ) $timezone = 0;

  // Если были допущены ошибки при заполнении формы - 
  // перенаправляем посетителя на страницу редактирования
  if ( !empty( $error ) ) {
    $_SESSION['editUserFormByAdmin'] = array();
    $_SESSION['editUserFormByAdmin']['error'] = '<p class="errorMsg">При заполнениии формы были допущены ошибки:</p>'.
	"\n".'<ul class="errorMsg">'."\n".$error.'</ul>'."\n";
	$_SESSION['editUserFormByAdmin']['name'] = $_POST['name'];
	$_SESSION['editUserFormByAdmin']['status'] = $status;
    $_SESSION['editUserFormByAdmin']['email'] = $email;
	$_SESSION['editUserFormByAdmin']['oldEmail'] = $oldEmail;
    $_SESSION['editUserFormByAdmin']['timezone'] = $timezone;
    $_SESSION['editUserFormByAdmin']['icq'] = $icq;
    $_SESSION['editUserFormByAdmin']['url'] = $url;
    $_SESSION['editUserFormByAdmin']['about'] = $about;
    $_SESSION['editUserFormByAdmin']['signature'] = $signature;
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=editUserFormByAdmin' );
    die();
  }	

  // Если выставлен флажок "Удалить загруженный ранее файл"
  if ( isset( $_POST['unlink'] ) and is_file( './photo/'.$id ) ) {
    unlink( './photo/'.$id );
  } 
  if ( !empty( $_FILES['avatar']['name'] ) and 
       move_uploaded_file ( $_FILES['avatar']['tmp_name'], './photo/'.$id ) ) {
	chmod( './photo/'.$id, 0644 );
  }
  
  // Все поля заполнены правильно - записываем изменения в БД
  $tmp = '';  
  if ( $changePassword ) {
    $tmp = $tmp."passw='".mysql_real_escape_string( md5( $newpassword ) )."', ";
  }
  if ( $changeEmail ) {
    $tmp = $tmp."email='".mysql_real_escape_string( $email )."', ";
  }
  $res = $mysql->query ("UPDATE ".TABLE_USERS." SET ".$tmp."
            status=?s,
		    timezone=?s,
		    url=?s,
		    icq=?s,
		    about=?s,
		    signature=?s
			WHERE id_author=?i",$status,$timezone,$url,$icq,$about,$signature,$id);
  if ( !$res ) {
    $msg = 'Ошибка при обновлении профиля';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	      '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  } else {
    return showInfoMessage( 'Профиль был изменён', '' );
  }
}

// Функция возвращает html списка пользователей форума
function getUsersList()
{
	global $mysql;
  // Выбираем из БД количество пользователей - это нужно для 
  // построения постраничной навигации
  $res = $mysql->query ("SELECT COUNT(*) FROM ".TABLE_USERS." WHERE 1");
  if ( !$res ) {
    $msg = 'Ошибка при формировании списка пользователей';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showUsersList' );
  }
  $total = mysqli_fetch_row( $res );
    
  // Проверяем передан ли номер текущей страницы (постраничная навигация)
  if ( isset($_GET['page']) ) {
    $page = (int)$_GET['page'];
    if ( $page < 1 ) $page = 1;
  } else {
    $page = 1;
  }

  // Число страниц списка пользователей (постраничная навигация)
  $cntPages = ceil( $total[0] / USERS_PER_PAGE );
  if ( $page > $cntPages ) $page = $cntPages;
  // Начальная позиция (постраничная навигация)
  $start = ( $page - 1 ) * USERS_PER_PAGE;

  $res = $mysql->query ("SELECT id_author, name, status, email, url, icq, puttime, posts
            FROM ".TABLE_USERS." 
            WHERE 1 ORDER BY puttime ASC LIMIT ?i, ".USERS_PER_PAGE,$start);

  if ( !$res ) {
    $msg = 'Ошибка при формировании списка пользователей';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, 'action=showUsersList' );
  }

  // Выводим "шапку" таблицы
  $html = '<table class="showTable">'."\n";
  $html = $html.'<tr>'."\n";
  $html = $html.'<th>Имя</th>'."\n";
  $html = $html.'<th>Статус</th>'."\n";
  $html = $html.'<th>Зарегистрирован</th>'."\n";
  $html = $html.'<th>Сообщений</th>'."\n";
  if ( isset( $_SESSION['user'] ) ) { 
    $html = $html.'<th>Личное сообщение</th>'."\n";
    $html = $html.'<th>E-mail</th>'."\n";
  }
  $html = $html.'<th>WWW</th>'."\n";
  $html = $html.'<th>ICQ</th>'."\n";
  if ( isset( $_SESSION['user'] ) and $_SESSION['user']['status'] == 'admin' )
    $html = $html.'<th>Правка</th>'."\n";
  $html = $html.'</tr>'."\n";

  $status = array( 'user' => 'Пользователь',
                   'moderator' => 'Модератор',
				   'admin' => 'Администратор' );
  
  while( $user = mysqli_fetch_array( $res ) ) {
    $html = $html.'<tr align="center">'."\n"; 
    $html = $html.'<td align="left"><a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
	        $user['id_author'].'">'.$user['name'].'</a></td>'."\n";
	$html = $html.'<td align="left">'.$status[$user['status']].'</td>'."\n";
	$html = $html.'<td>'.$user['puttime'].'</td>'."\n";
	$html = $html.'<td>'.$user['posts'].'</td>'."\n";
    if ( isset( $_SESSION['user'] ) ) { 
      $html = $html.'<td><a href="'.$_SERVER['PHP_SELF'].'?action=sendMsgForm&idUser='.
	          $user['id_author'].'">Написать</a></td>'."\n";
      $html = $html.'<td><a href="'.$_SERVER['PHP_SELF'].'?action=sendMailForm&idUser='.
	          $user['id_author'].'">Написать</a></td>'."\n";
    }
    if ( !empty( $user['url'] ) ) 
        $html = $html.'<td align="left"><a href="'.$user['url'].'" target="_blank">'.$user['url'].'</td>'."\n";
    else
        $html = $html.'<td align="left">&nbsp;</td>'."\n";
    if ( !empty( $user['icq'] ) ) 
        $html = $html.'<td>'.$user['icq'].'</td>'."\n";
    else
        $html = $html.'<td>&nbsp;</td>'."\n"; 
    if ( isset( $_SESSION['user'] ) and $_SESSION['user']['status'] == 'admin' ) {
      $html = $html.'<td><a href="'.$_SERVER['PHP_SELF'].
		        '?action=editUserFormByAdmin&idUser='.$user['id_author'].'"><img 
				src="./images/icon_edit.gif" alt="Править" title="Править" /></a></td>'."\n";
    }				
    $html = $html.'</tr>'."\n";
  }

  $html = $html.'</table>'."\n";

  // Строим постраничную навигацию
  if ( $cntPages > 1 ) {
	$html = $html.pageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=showUsersList' );
  }
  return $html;
}

// Вспомогательная функция - после выполнения пользователем каких-либо действий
// выдает информационное сообщение и делает редирект на нужную страницу с задержкой
function showInfoMessage( $message, $queryString )
{
  if ( !empty( $queryString ) ) $queryString = '?'.$queryString;
  header( 'Refresh: '.REDIRECT_DELAY.'; url='.$_SERVER['PHP_SELF'].$queryString );
  $html = file_get_contents( './templates/infoMessage.html' );
  $html = str_replace( '{infoMessage}', $message, $html );
  return $html;
}

// Вспомогательная функция - выдает сообщение об ошибке 
// и делает редирект на нужную страницу с задержкой
function showErrorMessage( $message = '', $error = '', $redirect = false, $queryString = '' )
{
  if ( $redirect ) {
    if ( !empty( $queryString ) ) $queryString = '?'.$queryString;
    header( 'Refresh: '.REDIRECT_DELAY.'; url='.$_SERVER['PHP_SELF'].$queryString );
  }
  $html = file_get_contents( './templates/infoMessage.html' );
  $html = str_replace( '{infoMessage}', $message, $html );
  if ( DEBUG_MODE ) {
    $tpl = file_get_contents( './templates/errorMessage.html' );
    $tpl = str_replace( '{errorMessage}', $error, $tpl );
    $html = $html.$tpl."\n";
  }
  return $html;
}

// Функция возвращает true или false в зависимости от того, имеет ли
// право пользователь редактировать сообщение (пост)
function hasRightEditPost( $post )
{
	global $mysql;
  // Незарегистрированный пользователь не имеет право редактировать сообщения
  if ( !isset( $_SESSION['user'] ) ) return false;
  // Если пользователь - администратор или модератор, он имеет право 
  // редактировать любые сообщения (посты)
  if ( $_SESSION['user']['status'] != 'user' ) return true;
  // Обычный пользователь не может редактировать чужие сообщения (посты)
  if ( $_SESSION['user']['id_author'] != $post['id_author'] ) return false;
  // Пользователь не может редактировать сообщение, если оно заблокировано
  if ( $post['locked'] == 1 ) return false;
  // Обычный пользователь может редактировать свое сообщение, 
  // только если на него не было ответов
  $res = $mysql->query ("SELECT id_post FROM ".TABLE_POSTS." 
            WHERE id_theme=?i AND time>?s",$post['id_theme'],$post['time']);
  if ( !$res ) return false;
  if ( mysqli_num_rows( $res ) == 0 ) 
    return true;
  else
    return false;
}

// Функция возвращает true или false в зависимости от того, имеет ли
// право пользователь удалить это сообщение (пост)
function hasRightDeletePost( $post )
{
	global $mysql;
  // Незарегистрированный пользователь не имеет право удалять сообщения
  if ( !isset( $_SESSION['user'] ) ) return false;
  // Если пользователь - администратор или модератор, он имеет право 
  // удалять любые сообщения (посты)
  if ( $_SESSION['user']['status'] != 'user' ) return true;
  // Обычный пользователь не может удалять чужие сообщения (посты)
  if ( $_SESSION['user']['id_author'] != $post['id_author'] ) return false;
  // Пользователь не может удалять сообщение, если оно заблокировано
  if ( $post['locked'] == 1 ) return false; 
  // Обычный  пользователь имеет право удалять свои
  // сообщения, если на них еще не было ответа
  $res = $mysql->query ("SELECT id_post FROM ".TABLE_POSTS." 
            WHERE id_theme=?i AND time>?s",$post['id_theme'],$post['time']);
  if ( !$res ) return false;
  if ( mysqli_num_rows( $res ) == 0 ) 
    return true;
  else
    return false;
}

// Функция возвращает html формы для авторизации на сообществе
function getLoginForm()
{
  $html = '';
/*  if ( isset( $_SESSION['loginForm']['error'] ) ) {
    $info = file_get_contents( './templates/infoMessage.html' );
	$info = str_replace( '{infoMessage}', $_SESSION['loginForm']['error'], $info );
	$html = $html.$info."\n";
	unset( $_SESSION['loginForm']['error'] );
  } */ 
  $html = file_get_contents( './templates/loginForm.html' );
  return $html;
}


// Функция возвращает html формы для поиска по форуму
function searchForm()
{
	global $mysql;
  $html = '';
  
  $res = $mysql->query ("SELECT id_forum, name FROM ".TABLE_FORUMS." WHERE 1 ORDER BY pos");
  if ( !$res ) {
    $msg = 'Ошибка при формировании формы для поиска';
	$err = 'Ошибка при выполнении запроса: <br/>'.
	       $query.'<br/>'.mysqli_errno().':&nbsp;'.mysqli_error().'<br/>'.
	       '(Файл '. __FILE__ .', строка '. __LINE__ .')';
	return showErrorMessage( $msg, $err, true, '' );
  }
  if ( mysqli_num_rows( $res ) > 0 ) {
    $options = '<option value="0">Все имеющиеся</option>'."\n";
    while( $forum = mysqli_fetch_row( $res ) ) {
	  $options = $options.'<option value="'.$forum[0].'">'.$forum[1].'</option>'."\n";
	}
    $html = file_get_contents( './templates/searchForm.html' );
	$action = $_SERVER['PHP_SELF'].'?action=searchResult';
	$html = str_replace( '{options}', $options, $html );
	$html = str_replace( '{action}', $action, $html );
  }
  
  return $html;
}

function searchResult()
{

  if ( isset( $_POST['words'] ) and
	   isset( $_POST['id_forum'] ) and
       isset( $_POST['where'] ) )
  {	
    if ( empty( $_POST['words'] ) ) {
      header( 'Location: '.$_SERVER['PHP_SELF'].'?action=searchForm' );
      die();  
    }
	// Обрезаем строку до длины, указанной в атрибуте maxlength
    $search = substr( $_POST['words'], 0, 64 );
    // Убираем пробелы в начале и конце строки поиска
    $search = trim( $search );
    // Убираем все "ненормальные" символы
    $good = preg_replace("#[^a-zа-я\s\-]#i", " ", $search);
	$good = trim( $good );
    if ( empty( $good ) ) {
      header( 'Location: '.$_SERVER['PHP_SELF'].'?action=searchForm' );
      die();  
    }
    // Сжимаем двойные пробелы
    $good = ereg_replace(" +", " ", $good);
		
    // Получаем корни искомых слов
    $stemmer = new Lingua_Stem_Ru();
    $tmp = explode( " ", $good );
    foreach ( $tmp as $wrd ) {
      // Если слово слишком короткое - не используем его
      if ( strlen($wrd) < 3 ) continue;
      $words[] = $stemmer->stem_word($wrd);
    }
    // Склеиваем массив $words обратно в строку
    $string = implode( "* ", $words );
    $string = $string."*";
  
    // Теперь надо выяснить, где будем искать
    $where = $_POST['where'];
    $whereArray = array( 'themes', 'posts', 'everywhere' );
    if ( !in_array( $where, $whereArray ) ) $where = 'themes';
	
    // Записываем все данные в сессию - это нам понадобится при 
    // построении постраничной навигации результатов поиска
    $_SESSION['search']['query'] = $search;
    $_SESSION['search']['good'] = $good;
	$_SESSION['search']['words'] = $words;
	// Это нам потребуется для подсветки искомых слов
	$_SESSION['search']['words'] = implode( '|', $words );
    $_SESSION['search']['string'] = $string;
	$_SESSION['search']['where'] = $where;
	$id_forum = (int)$_POST['id_forum'];
	if ( $id_forum < 0 ) $id_forum = 0;
	$_SESSION['search']['id_forum'] = $id_forum;
	
    header( 'Location: '.$_SERVER['PHP_SELF'].'?action=searchResult' );
    die();
  }
  
  if ( !isset( $_SESSION['search'] ) ) {
    header( 'Location: '.$_SERVER['PHP_SELF'] );
    die();  
  }
	
  // Если поиск осуществляется по названиям тем
  if ( $_SESSION['search']['where'] == 'themes' )
    $result = searchResultThemes();
  else if ( $_SESSION['search']['where'] == 'posts' )
    $result = searchResultPosts();
  else
    $result = searchResultEverywhere();
	
  $html = '<table class="showTable">'."\n";
  $html = $html.'<tr>'."\n";
  $html = $html.'<th>Результаты поиска</th>'."\n";
  $html = $html.'<tr>'."\n";
  $html = $html.'</table>'."\n";

  $html = $html.$result."\n";

  return $html;
}

// Поиск только в названиях тем
function searchResultThemes()
{
	global $mysql;
  // Составляем запрос к БД, чтобы узнать количество записей в результатах поиска - 
  // это нужно для построения постраничной навигации
  $forum = '';
  if ( $_SESSION['search']['id_forum'] ) $forum = $mysql->parse(' AND id_forum=?i',$_SESSION['search']['id_forum']);
  $res =  $mysql->query ("SELECT COUNT(*)
            FROM ".TABLE_THEMES." 
            WHERE MATCH (name) AGAINST (?s IN BOOLEAN MODE)?p",$_SESSION['search']['string'],$forum);
  $total = mysqli_fetch_row( $res);
  if ( $total[0] == 0 ) return 'По вашему запросу ничего не найдено';
 
  // Число страниц результатов поиска (постраничная навигация)
  $cntPages = ceil( $total[0] / SEARCH_THEMES_PER_PAGE );
  
  // Проверяем передан ли номер текущей страницы (постраничная навигация)
  if ( isset($_GET['page']) ) {
    $page = (int)$_GET['page'];
    if ( $page < 1 ) $page = 1;
  } else {
    $page = 1;
  }

  if ( $page > $cntPages ) $page = $cntPages;
  // Начальная позиция (постраничная навигация)
  $start = ( $page - 1 ) * SEARCH_THEMES_PER_PAGE;

  // Строим постраничную навигацию, если это необходимо
  if ( $cntPages > 1 ) {
    // Функция возвращает html меню для постраничной навигации
    $pages = pageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=searchResult' );		   
  }

  $res = $query( "SELECT id_theme, name, id_forum 
            FROM ".TABLE_THEMES." 
            WHERE MATCH (name) AGAINST (?s IN BOOLEAN MODE)?p
			ORDER BY MATCH (name) AGAINST (?s IN BOOLEAN MODE) DESC 
			LIMIT ?i, ".SEARCH_THEMES_PER_PAGE,$_SESSION['search']['string'],$forum,$_SESSION['search']['string'],$start);
  $html = "<ul>\n";
  while ( $theme = mysqli_fetch_array( $res ) ) {
    $html = $html.'<li><a class="topictitle" href="'.$_SERVER["PHP_SELF"]."?action=showTheme".
	        '&idForum='.$theme['id_forum'].'&id_theme='.$theme['id_theme'].'&page=1">'.
			$theme['name'].'</a></li>'."\n";	
  }
  $html = $html."</ul>\n";

  // Постраничная навигация
  if ( isset( $pages ) ) $html = $html.$pages."\n";
  
  return $html;	
}

// Поиск в сообщениях (постах)
function searchResultPosts()
{
	global $mysql;
  // Составляем запрос к БД, чтобы узнать количество записей в результатах поиска - 
  // это нужно для построения постраничной навигации
  $forum = '';
  if ( $_SESSION['search']['id_forum'] ) $forum = $mysql->parse(' AND b.id_forum=?i',$_SESSION['search']['id_forum']);
  $res = $mysql->query ("SELECT COUNT(*) 
            FROM ".TABLE_POSTS." a INNER JOIN ".TABLE_THEMES." b
            ON a.id_theme=b.id_theme 
            WHERE MATCH (a.name) AGAINST (?s IN BOOLEAN MODE)?p",$_SESSION['search']['string'],$forum);
			
  $total = mysqli_fetch_row( $res );

  if ( $total[0] == 0 ) return 'По вашему запросу ничего не найдено';
 
  // Число страниц результатов поиска (постраничная навигация)
  $cntPages = ceil( $total[0] / SEARCH_POSTS_PER_PAGE );
  
  // Проверяем передан ли номер текущей страницы (постраничная навигация)
  if ( isset($_GET['page']) ) {
    $page = (int)$_GET['page'];
    if ( $page < 1 ) $page = 1;
  } else {
    $page = 1;
  }

  if ( $page > $cntPages ) $page = $cntPages;
  // Начальная позиция (постраничная навигация)
  $start = ( $page - 1 ) * SEARCH_POSTS_PER_PAGE;

  // Строим постраничную навигацию, если это необходимо
  if ( $cntPages > 1 ) {
    // Функция возвращает html меню для постраничной навигации
    $pages = pageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=searchResult' );		   
  }

  $res = $mysql->query ("SELECT a.id_post, a.name, a.id_theme, b.id_forum, b.name 
            FROM ".TABLE_POSTS." a INNER JOIN ".TABLE_THEMES." b
            ON a.id_theme=b.id_theme 
            WHERE MATCH (a.name) AGAINST (?s IN BOOLEAN MODE)?p
			ORDER BY MATCH (a.name) AGAINST (?s IN BOOLEAN MODE) DESC
			LIMIT ?i, ".SEARCH_POSTS_PER_PAGE,$_SESSION['search']['string'],$forum,$_SESSION['search']['string'],$start);
		
  $html = '';
  while ( $post = mysqli_fetch_row( $res ) ) {
    
    $html = $html.'<div style="margin: 5px 0 5px 0">'."\n";
	$html = $html.'<img src="./images/folder.gif" width="19" height="18" alt="" align="top" />
	        <a class="topictitle" href="'.$_SERVER["PHP_SELF"]."?action=showTheme".
	        '&idForum='.$post[3].'&id_theme='.$post[2].'&page=1">'.$post[4].'</a>'."\n";
	$html = $html.'</div>'."\n";
	
	$html = $html.'<table class="postTable">'."\n";
    $html = $html.'<tr>'."\n";
	$html = $html.'<td>'."\n";
	$message = print_page( $post[1] );
	$message = preg_replace("/\b(".$_SESSION['search']['words'].")(.*?)\b/i", 
	           "<span style='color:red; font-weight:bold'>\\0</span>", $message);
    $html = $html.$message."\n";
	$html = $html.'</td>'."\n";
	$html = $html.'</tr>'."\n";
	$html = $html."</table>\n";
  }

  // Постраничная навигация
  if ( isset( $pages ) ) $html = $html.$pages."\n";
  
  return $html;	
}

// Поиск в названиях тем и сообщениях
function searchResultEverywhere()
{
	global $mysql;
  // Составляем запрос к БД, чтобы узнать количество записей в результатах поиска - 
  // это нужно для построения постраничной навигации
  $forum = '';
  if ( $_SESSION['search']['id_forum'] ) $forum = ' AND b.id_forum='.$_SESSION['search']['id_forum'];
  $res = $mysql->query ("SELECT COUNT(*) 
            FROM ".TABLE_POSTS." a INNER JOIN ".TABLE_THEMES." b
            ON a.id_theme=b.id_theme 
            WHERE MATCH (a.name, b.name) AGAINST (?s IN BOOLEAN MODE)?p",$_SESSION['search']['string'],$forum);

  $total = mysqli_fetch_row( $res );

  if ( $total[0] == 0 ) return 'По вашему запросу ничего не найдено';
 
  // Число страниц результатов поиска (постраничная навигация)
  $cntPages = ceil( $total[0] / SEARCH_EVERYWHERE_PER_PAGE );
  
  // Проверяем передан ли номер текущей страницы (постраничная навигация)
  if ( isset($_GET['page']) ) {
    $page = (int)$_GET['page'];
    if ( $page < 1 ) $page = 1;
  } else {
    $page = 1;
  }

  if ( $page > $cntPages ) $page = $cntPages;
  // Начальная позиция (постраничная навигация)
  $start = ( $page - 1 ) * SEARCH_EVERYWHERE_PER_PAGE;

  // Строим постраничную навигацию, если это необходимо
  if ( $cntPages > 1 ) {
    // Функция возвращает html меню для постраничной навигации
    $pages = pageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=searchResult' );		   
  }

 $res = $mysql->query ("SELECT a.id_post, a.name, a.id_theme, b.id_forum, b.name 
            FROM ".TABLE_POSTS." a INNER JOIN ".TABLE_THEMES." b
            ON a.id_theme=b.id_theme 
            WHERE MATCH (a.name, b.name) AGAINST (?s IN BOOLEAN MODE)?p
			ORDER BY MATCH (a.name, b.name) AGAINST (?s IN BOOLEAN MODE) DESC
			LIMIT ?i, ".SEARCH_EVERYWHERE_PER_PAGE,$_SESSION['search']['string'],$forum,$_SESSION['search']['string'],$start);
		
  $html = ''; 
  while ( $post = mysqli_fetch_row( $res ) ) {
  
    $html = $html.'<div style="margin: 5px 0 5px 0">'."\n";
	$html = $html.'<img src="./images/folder.gif" width="19" height="18" alt="" align="top" />
	        <a class="topictitle" href="'.$_SERVER["PHP_SELF"]."?action=showTheme".
	        '&idForum='.$post[3].'&id_theme='.$post[2].'&page=1">'.$post[4].'</a>'."\n";
	$html = $html.'</div>'."\n";		
	
	$html = $html.'<table class="postTable">'."\n";	
    $html = $html.'<tr>'."\n";
	$html = $html.'<td>'."\n";
	$message = print_page( $post[1] );
	$message = preg_replace("/\b(".$_SESSION['search']['words'].")(.*?)\b/i", 
	           "<span style='color:red; font-weight:bold'>\\0</span>", $message);
    $html = $html.$message."\n";
	$html = $html.'</td>'."\n";
	$html = $html.'</tr>'."\n";
	$html = $html."</table>\n";
  }
  
  // Постраничная навигация
  if ( isset( $pages ) ) $html = $html.$pages."\n";
  
  return $html;
}

// Функция возвращает html меню для постраничной навигации
function pageIterator( $page, $cntPages, $url )
{

  $html = '<div class="pagesDiv">&nbsp;Страницы: ';
  // Проверяем нужна ли стрелка "В начало"
  if ( $page > 3 )
    $startpage = '<a class="pages" href="'.$url.'&page=1"><<</a> ... ';
  else
    $startpage = '';
  // Проверяем нужна ли стрелка "В конец"
  if ( $page < ($cntPages - 2) )
    $endpage = ' ... <a class="pages" href="'.$url.'&page='.$cntPages.'">>></a>';
  else
    $endpage = '';

  // Находим две ближайшие станицы с обоих краев, если они есть
  if ( $page - 2 > 0 )
    $page2left = ' <a class="pages" href="'.$url.'&page='.($page - 2).'">'.($page - 2).'</a> | ';
  else
    $page2left = '';
  if ( $page - 1 > 0 )
    $page1left = ' <a class="pages" href="'.$url.'&page='.($page - 1).'">'.($page - 1).'</a> | ';
  else
    $page1left = '';
  if ( $page + 2 <= $cntPages )
    $page2right = ' | <a class="pages" href="'.$url.'&page='.($page + 2).'">'.($page + 2).'</a>';
  else
    $page2right = '';
  if ( $page + 1 <= $cntPages )
    $page1right = ' | <a class="pages" href="'.$url.'&page='.($page + 1).'">'.($page + 1).'</a>';
  else
    $page1right = '';

  // Выводим меню
  $html = $html.$startpage.$page2left.$page1left.'<strong>'.$page.'</strong>'.
          $page1right.$page2right.$endpage."\n";

  $html = $html.'</div>'."\n";

  return $html;
}

// Статистика форума
function getStat()
{
	global $mysql;
  $html = '<table class="showTable">'."\n";
  $html = $html.'<tr><th>Статистика</th></tr>'."\n";
  $html = $html.'<tr>'."\n";
  $html = $html.'<td>'."\n";
  $html = $html.'<div class="details">'."\n";
  
  $res = $mysql->query ('SELECT COUNT(*) FROM '.TABLE_POSTS);
  $res_count=mysqli_fetch_row($res);
  if ( !$res ) return '';
  	$html = $html.'Наши пользователи оставили сообщений: '.$res_count[0].'<br/>'."\n";
  $res = $mysql->query ('SELECT COUNT(*) FROM '.TABLE_USERS);
  $res_count=mysqli_fetch_row($res);
  if ( !$res ) return '';
  $html = $html.'Всего зарегистрированных пользователей: '.$res_count[0].'<br/>'."\n";  
  
  $res = $mysql->query ('SELECT id, login FROM '.TABLE_USERS.' ORDER BY id DESC LIMIT 1');
  if ( !$res ) return '';
  list( $id_user, $name ) = mysqli_fetch_array( $res );
  $html = $html.'Последний зарегистрированный пользователь: '.
          '<a href="'.$_SERVER['PHP_SELF'].'?action=showUserInfo&idUser='.
		  $id_user.'">'.$name.'</a><br/>'."\n";
		  
  // Пользователи on-line
  if ( isset( $_SESSION['usersOnLine'] ) ) {
    $cnt = count( $_SESSION['usersOnLine'] );
	$onLine = '';
	if ( $cnt > 0 ) {
      $onLine = $onLine.'Сейчас на сообществе: ';
	  foreach ( $_SESSION['usersOnLine'] as $id => $name ) {
	    $onLine = $onLine.'<a href="'.$_SERVER['PHP_SELF'].
		          '?action=showUserInfo&idUser='.$id.'">'.$name.'</a>, ';	
	  }
	  $onLine = substr( $onLine, 0, (strlen( $onLine )-2) );
	}
	$html = $html.$onLine."\n";
  }
  $html = $html.'</div>'."\n";
  $html = $html.'</td>'."\n";
  $html = $html.'</tr>'."\n";
  $html = $html.'</table>'."\n";
  
  return $html;
}

// Функция помещает в массив $_SESSION['usersOnLine'] список зарегистрированных 
// пользователей, которые в настоящий момент просматривают форум
function getUsersOnLine()
{
	global $mysql;
  $res = $mysql->query ("SELECT id, login 
            FROM ".TABLE_USERS." 
			WHERE UNIX_TIMESTAMP(last_time)>".( time() - 60 * TIME_ON_LINE )."
			ORDER BY status DESC");
  if ( $res ) {
    if ( isset( $_SESSION['usersOnLine'] ) ) unset( $_SESSION['usersOnLine'] );
    $cnt = mysqli_num_rows( $res );
    if ( $cnt > 0 ) {
      for ( $i = 0; $on = mysqli_fetch_array( $res ); $i++ ) {
	    $_SESSION['usersOnLine'][$on['id']] = $on['login'];
      }
    }
  }
  return;
}

// Функция возвращает форму для быстрого ответа в тему
function getQuickReplyForm( $id_theme )
{
  $html = file_get_contents( './templates/quickReplyForm.html' );
  $action = $_SERVER['PHP_SELF'].'?action=quickReply&idForum='.$_GET['idForum'].'&id_theme='.$id_theme;
  $html = str_replace( '{action}', $action, $html );
  return $html;
}

// Возвращает размер файла в Кб
function getFileSize( $file )
{
  return number_format( (filesize($file)/1024), 2, '.', '' );
}