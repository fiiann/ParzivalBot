<?php
require_once('config.php');
require_once('kamus.php');
require_once('functions.php');
require_once('MUser.php');

$update = file_get_contents('php://input');
$update = json_decode($update, TRUE);

if(isInArray('callback_query', $update)){
	$chatId = $update["callback_query"]["message"]["chat"]["id"];
	// $message=$update["callback_query"]["message"]["text"];
	$data=$update["callback_query"]["data"];

	$exp=explode('#',$data);
	if($exp[0]=='HP'){
		$message=$exp[0];
		$phone_number=$exp[1];
	}else if($exp[0]=='ISIPULSA'){
		$message=$exp[0];
		$nominal=$exp[1];
		$phone_number=$exp[2];
	}else{
		$message='unknown';
	}
}else{
	$chatId = $update["message"]["chat"]["id"];
	if(isInArray('contact', $update['message'])){
		$message = 'sendcontact_yo';
		$contact = $update["message"]["contact"];
	}else{
		$message = $update["message"]["text"];
	}
}

switch($message) {
	case "/start":
		$keyboard = [
        'keyboard' => [
           [['text'=> 'Daftar', 'request_contact'=>true]]
		],
        'resize_keyboard' => true,
		'one_time_keyboard' => true];
		
		sendKeyboard($chatId, $txt_welcome, $keyboard);
		break;
	case 'Daftar':
		$id=$update['message']['from']['id'];
		$username=$update['message']['from']['username'];
		$first_name=$update['message']['from']['first_name'];
		if(isInArray('last_name', $update['message']['from'])) $last_name=$update['message']['from']['last_name'];
		else $last_name=null;
		
		$keyboard = [
        'keyboard' => [
           [['text'=> 'Daftar', 'request_contact'=>true]]
		],
        'resize_keyboard' => true,
		'one_time_keyboard' => true];
		
		sendKeyboard($chatId, 'Silakan tekan tombol DAFTAR di bawah', $keyboard);		
		break;
	case "sendcontact_yo":
		$keyboard = [
        'keyboard' => [
            [['text' => 'Pulsa'],['text' => 'Internet']],
            [['text' => 'Token'],['text' => 'Tagihan']],
            [['text' => 'Game'],['text' => 'History']],
            [['text' => 'Saldo'],['text' => 'Cancel']]
		],
        'resize_keyboard' => true,
		'one_time_keyboard' => true];
		
		$id=$update['message']['from']['id'];
		$username=$update['message']['from']['username'];
		$phone_number=$contact['phone_number'];
		$first_name=$contact['first_name'];
		if(isInArray('last_name', $contact)) $last_name=$contact['last_name'];
		else $last_name=null;
		
		// cek user
		if(cekUser($id)){
			sendKeyboard($chatId, 'Akun anda sudah pernah terdaftar atas nama '.$first_name.' '.$last_name.' dengan nomor hp '.$phone_number.'.', $keyboard);
		}else{
			// daftarkan
			$data=array(
				'id_user'=>$id,
				'username'=>$username,
				'phone_number'=>$phone_number,
				'first_name'=>$first_name,
				'last_name'=>$last_name,
			);
			registerUser($data);
			sendKeyboard($chatId, 'Yeay, Akun anda berhasil terdaftar atas nama '.$first_name.' '.$last_name.' dengan nomor hp '.$phone_number.'. Selamat bertransaksi.', $keyboard);
		}
		break;
	case "Pulsa":
		$id=$update['message']['from']['id'];
		$user=getUser($id);

		$keyboard = [
        'inline_keyboard' => [
            [['text' => 'Isi Pulsa Ke Nomor Saya Sendiri', 'callback_data'=>'HP#'.$user['phone_number'].'#']]
            // [['text' => 'Tambahkan Nomor Lain', 'callback_data'=>'ADDCONTACT']]
		],
        'resize_keyboard' => true,
		'one_time_keyboard' => true];
		
		sendKeyboard($chatId, $txt_pulsa, $keyboard);
		break;
	case "Token":
		sendMessage($chatId, $txt_token);
		break;
	case "Game":
		sendMessage($chatId, $txt_game);
		break;
	case "Internet":
		sendMessage($chatId, $txt_internet);
		break;
	case "Tagihan":
		sendMessage($chatId, $txt_tagihan);
		break;
	case "History":
		sendMessage($chatId, $txt_history);
		break;
	case "Saldo":
		sendMessage($chatId, $txt_saldo);
		break;
	case "Cancel":
		sendMessage($chatId, $txt_cancel);
		break;
	case "HP":
		$keyboard = [
		'inline_keyboard' => [
            [['text' => '5', 'callback_data'=>'ISIPULSA#5#'.$phone_number.'#'],['text' => '10', 'callback_data'=>'ISIPULSA#10#'.$phone_number.'#'],['text' => '15', 'callback_data'=>'ISIPULSA#15#'.$phone_number.'#']],
            [['text' => '20', 'callback_data'=>'ISIPULSA#20#'.$phone_number.'#'],['text' => '25', 'callback_data'=>'ISIPULSA#25#'.$phone_number.'#'],['text' => '30', 'callback_data'=>'ISIPULSA#30#'.$phone_number.'#']],
            [['text' => '50', 'callback_data'=>'ISIPULSA#50#'.$phone_number.'#'],['text' => '100', 'callback_data'=>'ISIPULSA#100#'.$phone_number.'#']]
		],
        'resize_keyboard' => true,
		'one_time_keyboard' => true];
		
		sendKeyboard($chatId, 'Silakan pilih nominal.', $keyboard);
		break;
	case "ISIPULSA":
		$id=$chatId;
		$user=getUser($id);
		
		sendMessage($admin, $user['first_name'].' melakukan pengisian pulsa. Anda harus memproses secara manual.');
		sendMessage($admin, $nominal.'#'.$phone_number.'#');
		sendMessage($chatId, 'Kami sedang memproses permintaan anda. Tunggu sebentar.');
		break;
	case "Tambahkan Nomor Lain":
		sendMessage($chatId, 'Tambahkan nomor hp dengan format : ADDCONTACT#NAMA#NO_HP');
		break;
	default: 
		sendMessage($chatId, $txt_default.' '.json_encode($update));
}
?>