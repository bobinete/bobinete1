<?

$fk_merchant_id = '174829'; //merchant_id ID мазагина в free-kassa.ru http://free-kassa.ru/merchant/cabinet/help/
$fk_merchant_key = 'c6wykdxx'; //Секретное слово http://free-kassa.ru/merchant/cabinet/profile/tech.php

if (isset($_GET['prepare_once'])) {
    $hash = md5($fk_merchant_id.":".$_GET['oa'].":".$fk_merchant_key.":".$_GET['l']);
    echo '<hash>'.$hash.'</hash>';
    exit;
}
?>