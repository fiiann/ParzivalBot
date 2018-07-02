<?php
	require_once('connect.php');
	
	function getUser($id_user){
		$query='SELECT * FROM users WHERE id_user="'.$id_user.'" OR username="'.$id_user.'" OR phone_number="'.$id_user.'" ';
		$result=mysqli_query($GLOBALS['db'],$query);
		
		if(mysqli_num_rows($result)>0){
			return mysqli_fetch_array($result);
		}else{
			return false;
		}
	}
	function cekUser($id_user){
		$query='SELECT * FROM users WHERE id_user="'.$id_user.'" OR username="'.$id_user.'" OR phone_number="'.$id_user.'" ';
		$result=mysqli_query($GLOBALS['db'],$query);
		
		if(mysqli_num_rows($result)>0){
			return true;
		}else{
			return false;
		}
	}
	function registerUser($data){
		$query='INSERT INTO users SET id_user="'.$data['id_user'].'", username="'.$data['username'].'", password="'.$data['password'].'", phone_number="'.$data['phone_number'].'", first_name="'.$data['first_name'].'", last_name="'.$data['last_name'].'" ';
		$result=mysqli_query($GLOBALS['db'],$query);
		
		return $result;
	}
  
?>
