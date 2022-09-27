<?PHP
# Автоподгрузка классов
function __autoload($name){ include("classes/_class.".$name.".php");}

# Класс конфига 
$config = new config;

# Функции
$func = new func;

# База данных
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);


$fk_merchant_id = '174829'; //merchant_id ID мазагина в free-kassa.ru (http://free-kassa.ru/merchant/cabinet/help/)
$fk_merchant_key = 'c6wykdxx'; //Секретное слово http://free-kassa.ru/merchant/cabinet/profile/tech.php
$fk_merchant_key2 = 'c6wykdxx'; //Секретное слово2 (result) http://free-kassa.ru/merchant/cabinet/profile/tech.php

$ik_payment_amount = round(floatval($_POST['AMOUNT']),2);
$user_id = $_SESSION["user_id"];
	
$hash = md5($fk_merchant_id.":".$_POST['AMOUNT'].":".$fk_merchant_key2.":".$_POST['MERCHANT_ORDER_ID']);

if ($hash != $_POST['SIGN']) die("SumError");
    
   
   
   $bonus_in =  $ik_payment_amount;
   
   
   
   
   
   
   $db->Query("SELECT user, referer_id FROM db_users_a WHERE id = '{$user_id}' LIMIT 1");
   $user_ardata = $db->FetchArray();
   $user_name = $user_ardata["user"];
   $refid = $user_ardata["referer_id"];
   
   # Зачисляем баланс
   $serebro = sprintf("%.4f", $ik_payment_amount);
   
   $db->Query("SELECT insert_sum FROM db_users_b WHERE id = '{$user_id}' LIMIT 1");
   $ins_sum = $db->FetchRow();
   
   
   $add_tree = ( $ik_payment_amount >= 499.99) ? 2 : 0;
   $lsb = time();
   $bon_ref = ($serebro * 0.15);
   $dadd = time();
   $db->Query("UPDATE db_users_b SET amonp = amonp + '$bonus_in', insert_sum = insert_sum + '$ik_payment_amount' WHERE id = '{$user_id}'");
   $db->Query("UPDATE db_users_a SET rayting = rayting + '$ray' WHERE id = '{$user_id}'");
   

   		
   
   
   
   # Зачисляем средства рефереру и дерево
   $add_tree_referer = ($ins_sum <= 0.01) ? ", a_t = a_t + 1" : "";
   $db->Query("UPDATE db_users_b SET amonb = amonb + $bon_ref, bon_ref = bon_ref + '$bon_ref' WHERE id = '$refid'");



   # Статистика пополнений
   $da = time();
   $dd = $da + 60*60*24*15;
   $db->Query("INSERT INTO db_insert_money (user, user_id, money, serebro, date_add, date_del) 
   VALUES ('$user_name','$user_id','$ik_payment_amount','$serebro','$da','$dd')");
   
   # Конкурс
//$competition = new competition($db);
//$competition->UpdatePoints($user_id, $ik_payment_amount);
#--------
   
	# Обновление статистики сайта
	$db->Query("UPDATE db_stats SET all_insert = all_insert + '$ik_payment_amount' WHERE id = '1'");


?>