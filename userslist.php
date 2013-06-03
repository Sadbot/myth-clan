<?php
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");

require_once("config/config.php");

site_header();

if(isset($_SESSION['user']) && $_SESSION['user']==true)
{

  // Выбираем из БД количество пользователей - это нужно для 
  // построения постраничной навигации
  $res = $mysql->query ("SELECT COUNT(*) FROM users WHERE 1");
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

  $res = $mysql->query ("SELECT id, login, status, email, url, icq, date, posts
            FROM ".TABLE_USERS." 
            WHERE 1 ORDER BY date ASC LIMIT ?i, ".USERS_PER_PAGE,$start);

  // Выводим "шапку" таблицы
  echo '<table class="showTable">'."\n";
  echo'<tr>'."\n";
  echo'<th>Имя</th>'."\n";
  echo'<th>Статус</th>'."\n";
  echo'<th>Зарегистрирован</th>'."\n";
  echo'<th>Сообщений</th>'."\n";
  if ( isset( $_SESSION['user'] ) ) { 
    echo'<th>Личное сообщение</th>'."\n";
  }
  echo'<th>WWW</th>'."\n";
  echo'<th>ICQ</th>'."\n";
  if ( isset( $_SESSION['user'] ) and $_SESSION['user']['status'] == 'admin' )
    echo'<th>Правка</th>'."\n";
  echo'</tr>'."\n";

  $status = array( 'user' => 'Пользователь',
                   'moderator' => 'Модератор',
				   'admin' => 'Администратор' );
  
  while( $user = mysqli_fetch_array( $res ) ) {
    echo'<tr align="center">'."\n"; 
    echo'<td align="left"><a href="profile.php?id='.
	        $user['id'].'">'.$user['login'].'</a></td>'."\n";
	echo'<td align="left">'.$status[$user['status']].'</td>'."\n";
	echo'<td>'.$user['date'].'</td>'."\n";
	echo'<td>'.$user['posts'].'</td>'."\n";
    if ( isset( $_SESSION['user'] ) ) { 
      echo'<td><a href="'.$_SERVER['PHP_SELF'].'?action=sendMsgForm&idUser='.
	          $user['id'].'">Написать</a></td>'."\n";
    }
    if ( !empty( $user['url'] ) ) 
      echo'<td align="left"><a href="'.$user['url'].'" target="_blank">'.$user['url'].'</td>'."\n";
    else
      echo'<td align="left">&nbsp;</td>'."\n";
    if ( !empty( $user['icq'] ) ) 
      echo'<td>'.$user['icq'].'</td>'."\n";
    else
      echo'<td>&nbsp;</td>'."\n"; 
    if ( isset( $_SESSION['user'] ) and $_SESSION['user']['status'] == 'admin' ) {
      echo'<td><a href="'.$_SERVER['PHP_SELF'].
		        '?action=editUserFormByAdmin&idUser='.$user['id'].'"><img 
				src="./images/icon_edit.gif" alt="Править" title="Править" /></a></td>'."\n";
    }				
    echo'</tr>'."\n";
  }

  echo'</table>'."\n";

  // Строим постраничную навигацию
  if ( $cntPages > 1 ) {
	echopageIterator( $page, $cntPages, $_SERVER['PHP_SELF'].'?action=showUsersList' );
  }
}

site_footer();