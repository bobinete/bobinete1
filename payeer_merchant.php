<?PHP
# Автоподгрузка классов
function __autoload($name){ include("classes/_class.".$name.".php");}

# Класс конфига 
$config = new config;

# Функции
$func = new func;

# База данных
$db = new db($config->HostDB, $config->UserDB, $config->PassDB, $config->BaseDB);

if (isset($_POST["m_operation_id"]) && isset($_POST["m_sign"]))
{
	$m_key = $config->secretW;
	$arHash = array($_POST['m_operation_id'],
			$_POST['m_operation_ps'],
			$_POST['m_operation_date'],
			$_POST['m_operation_pay_date'],
			$_POST['m_shop'],
			$_POST['m_orderid'],
			$_POST['m_amount'],
			$_POST['m_curr'],
			$_POST['m_desc'],

			$_POST['m_status'],
			$m_key);
	
	$sign_hash = strtoupper(hash('sha256', implode(":", $arHash)));
	if ($_POST["m_sign"] == $sign_hash && $_POST['m_status'] == "success")
	{
		
	$db->Query("SELECT * FROM db_payeer_insert WHERE id = '".intval($_POST['m_orderid'])."'");
	if($db->NumRows() == 0){ echo $_POST['m_orderid']."|error"; exit;}
	
	
	
	
	
	
	
	
	
	
	
	
	
	

		
		
	$payeer_row = $db->FetchArray();
	if($payeer_row["status"] > 0){ echo $_POST['m_orderid']."|success"; exit;}
	
	$db->Query("UPDATE db_payeer_insert SET status = '1' WHERE id = '".intval($_POST['m_orderid'])."'");
	
	$ik_payment_amount = $payeer_row["sum"];
	
	//$bonus_in =  $ik_payment_amount + ($ik_payment_amount * 0.99); //АКЦИЯ
	$bonus_in =  $ik_payment_amount;
	$ray = $ik_payment_amount*10/100;
	$user_id = $payeer_row["user_id"];
   
	# Настройки
	$db->Query("SELECT * FROM db_config WHERE id = '1' LIMIT 1");
	$sonfig_site = $db->FetchArray();
   
   $db->Query("SELECT user, referer_id FROM db_users_a WHERE id = '{$user_id}' LIMIT 1");
   $user_ardata = $db->FetchArray();
   $user_name = $user_ardata["user"];
   $refid = $user_ardata["referer_id"];
   
$db->Query("SELECT * FROM db_users_a WHERE id = '$refid' LIMIT 1");
    $user_ref = $db->FetchArray();
	$refusid = $user_ref["id"]; 
    $refuser = $user_ref["user"];
 
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
   

   
   $db->Query("INSERT INTO `db_activ` (`user`, `user_id`, `text`, `sum`, `date_add`, `date_del`) VALUES ('$user_name', '$user_id', '<b>Пополнение</b> Payerr WALLET', '$ik_payment_amount', '$dadd', '".($dadd+60*60)."');");//активность
   $db->Query("INSERT INTO `db_activ` (`user`, `user_id`, `text`, `sum`, `date_add`, `date_del`, `type`) VALUES ('$refuser', '$refusid', 'Получил <b>реферальные</b> отчисления.', '$bon_ref', '$dadd', '".($dadd+60*60)."', '1');");//за активность рефу		
			
   
   
   
   # Зачисляем средства рефереру и дерево
   $add_tree_referer = ($ins_sum <= 0.01) ? ", a_t = a_t + 1" : "";
   $db->Query("UPDATE db_users_b SET amonb = amonb + $bon_ref, bon_ref = bon_ref + '$bon_ref' WHERE id = '$refid'");

# Конкурс
$competition = new competition($db);
$competition->UpdatePoints($user_id, $ik_payment_amount);
 # КОНКУРС
$db->Query("SELECT * FROM db_invcompetition_users WHERE user_id = '{$user_id}'");
$in = $db->FetchArray();

		
$a=$in["user_id"];
if($a > 0)
{
$usname = $user_name;
}
else
{
$usname = $user_name;
$db->Query("INSERT INTO db_invcompetition_users (user, user_id, points) VALUES ('$usname','$user_id','0')");
}

$db->Query("SELECT * FROM db_invcompetition WHERE status = '0' LIMIT 1");
$invcomp = $db->FetchArray();

$db->Query("SELECT COUNT(*) FROM db_invcompetition_users WHERE user_id = '{$user_id}'");
$rett = $db->FetchArray();

if ($invcomp["date_add"] >= 0 AND $invcomp["date_end"] > $da)
{
$db->Query("UPDATE db_invcompetition_users SET points = points + '$ik_payment_amount' WHERE user_id = '$user_id'");
}
else
{
$db->Query("UPDATE db_invcompetition_users SET points = points + '0' WHERE user_id = '$user_id'");
}
#-------------#


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
		
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	echo $_POST['m_orderid']."|success";
	exit;
	
	
	}
	echo $_POST['m_orderid']."|error";
}
?>
