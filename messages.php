<?php
require_once("common_funcs.php");
require_once("login_funcs.php");
require_once("site_code.php");

site_header();

if(!isset($_SESSION['user']))
{
	header( 'Location: '.$_SERVER['PHP_SELF'] );
}
				
  echo '<h1>Личные сообщения (входящие)</h1>'."\n";
  
  
  echo'<table class="showTable">'."\n";
  echo'<tr>'."\n";
  echo'<th width="2%">&nbsp;</th>'."\n";
  echo'<th width="15%">Отправитель</th>'."\n";
  echo'<th width="63%">Тема сообщения</th>'."\n";
  echo'<th width="15%">Дата</th>'."\n";
  echo'<th width="5%">Удл.</th>'."\n";
  echo'</tr>'."\n";
  // Запрос на выборку входящих сообщений
  
  // id_rmv - это поле указывает на то, что это сообщение уже удалил
  // один из пользователей. Т.е. сначала id_rmv=0, после того, как
  // сообщение удалил один из пользователей, id_rmv=id_user. И только после
  // того, как сообщение удалит второй пользователь, мы можем удалить
  // запись в таблице БД TABLE_MESSAGES
  $res = $mysql->query ("SELECT a.id_msg, a.subject, a.from_user, a.sendtime, a.viewed, b.login
            FROM messages a INNER JOIN users b
            ON a.from_user=b.id
            WHERE a.to_user=?i
			AND id_rmv<>?i
			ORDER BY sendtime DESC",$_COOKIE['id'],$_COOKIE['id']);
  
  while ( $msg = mysqli_fetch_row( $res ) ) {
    echo'<tr>'."\n";
    // Если сообщение еще не прочитано
	if ( $msg[4] == 0 )
	  echo'<td align="center" valign="middle"><img src="./images/folder_new.gif" width="19"
		      height="18" alt="" /></td>';
	else
      echo'<td align="center" valign="middle"><img src="./images/folder.gif" width="19" 
		      height="18" alt="" /></td>';	  
	echo'<td>'.$msg[5].'</td>'."\n";
	echo'<td><a href="'.$_SERVER['PHP_SELF'].'?action=showMsg&idMsg='.
	        $msg[0].'">'.$msg[1].'</a></td>'."\n";
	echo'<td>'.$msg[3].'</td>'."\n";
	echo'<td align="center"><a href="'.$_SERVER['PHP_SELF'].
	        '?action=deleteMsg&idMsg='.$msg[0].'"><img src="./images/icon_delete.gif"
			alt="Удалить" title="Удалить" border="0" /></a></td>'."\n";
	echo'</tr>'."\n";    
  }
  if ( mysqli_num_rows( $res ) == 0 ) {
    echo'<tr>'."\n";
	echo'<td colspan="4">В этой папке нет сообщений</td>'."\n";
	echo'</tr>'."\n";
  }
  echo'</table>'."\n";




site_footer();