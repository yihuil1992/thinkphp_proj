<?php
namespace Home\Controller;
use Think\Controller;

class RestController extends Controller{
	public function index(){
		// $str = "J#32156654#321564ADF#QFQWFCQW";
		// $array = explode('#', $str);
		// echo "<pre>";
		// print_r($array);
		// echo "</pre>";
		echo phpinfo();
		$this->display();
	}

	public function test(){
		/*
		* Take transaction_id, amount, and payment_method, call /chop to get URL, then redirect to that URL
		*/
	    // change the following 2 parameters to suit your deployment
	    $baseurl = "";
	    $token = "9C70436299584605AAA6AEA6C85DB9C9";
	    // you are welcome to change the parameters below, but shouldn't need to
		$reference = 'test1234';
		$payment_method = $_POST['payment_method'];
		$amount = '579';
		$currency = "RMB";
		$callback_url_success = $baseurl."receipt_success";
	    // $ipn_url = $baseurl."ipn";
	    $mobile_result_url = $baseurl."receipt_success?reference=$reference";
	    $callback_url_fail = "";
	    // no need to change below this line
	    $params = "&currency=".urlencode($currency).
	              "&amount=$amount".
	              "&reference=".urlencode($reference).
	              // "&ipn_url=".urlencode($ipn_url).
	              // "&mobile_result_url=".urlencode($mobile_result_url).
	              // "&callback_url_success=".urlencode($callback_url_success).
	              // "&callback_url_fail=".urlencode($callback_url_fail);
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, "http://dev.citconpay.com/chop/refund");
	    curl_setopt($curl, CURLOPT_POST, 8);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	        'Authorization: Bearer '.$token
	    ));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl,CURLOPT_POSTFIELDS, $params);
	    $result = curl_exec($curl);
	    curl_close($curl);
	    $response = json_decode($result);
	    echo '<pre>';
	    var_dump($response);
	    echo '</pre>';
	}

	public function findByZip(){
		if (IS_AJAX){
			if(!empty($_POST['zipcode'])){
				$zip = $_POST['zipcode'];
				$rest = D('Restaurants');
				$arr = $rest->join("restandzip on restaurants.rest_id = restandzip.rest_id")
							->where('zipcode = %s',$zip)
							->scope('findByZipField')
							->select();
				$this->ajaxReturn($arr);
			}
		}else{
			$this->display();	
		}
	}

	public function googleMap(){
		$this->display();
	}

	public function findByAddr(){
		$this->display();	
	}

	public function restPage(){
		$id = $_GET['id'];
		if (empty($id)){
			$id = 1;
		}
		$rest = D('Restaurants');
		$arr = $rest->where('rest_id = %s',$id)->select();
		$this->assign('data',$arr[0]);
		$this->display();
	}

	public function receipt_success(){
		$this->display();
	}

	public function ipn(){
		echo "ipn page";
	}

	public function payment(){
		/*
		* Take transaction_id, amount, and payment_method, call /chop to get URL, then redirect to that URL
		*/
	    // change the following 2 parameters to suit your deployment
	    $baseurl = "";
	    $token = "9C70436299584605AAA6AEA6C85DB9C9";
	    // you are welcome to change the parameters below, but shouldn't need to
		$reference = $_POST['transaction_id'];
		$payment_method = $_POST['payment_method'];
		$amount = $_POST['amount'];
		$currency = "USD";
		$callback_url_success = $baseurl."receipt_success";
	    $ipn_url = $baseurl."ipn";
	    $mobile_result_url = $baseurl."receipt_success?reference=$reference";
	    $callback_url_fail = "";
	    // no need to change below this line
	    $params = "payment_method=".urlencode($payment_method).
	              "&currency=".urlencode($currency).
	              "&amount=$amount".
	              "&reference=".urlencode($reference).
	              "&ipn_url=".urlencode($ipn_url).
	              "&mobile_result_url=".urlencode($mobile_result_url).
	              "&callback_url_success=".urlencode($callback_url_success).
	              "&callback_url_fail=".urlencode($callback_url_fail);
	    $curl = curl_init();
	    curl_setopt($curl, CURLOPT_URL, "http://dev.citconpay.com/chop/chop");
	    curl_setopt($curl, CURLOPT_POST, 8);
	    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($curl, CURLOPT_HTTPHEADER, array(
	        'Authorization: Bearer '.$token
	    ));
	    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($curl,CURLOPT_POSTFIELDS, $params);
	    $result = curl_exec($curl);
	    curl_close($curl);
	    $response = json_decode($result);
	    echo "<pre>";
	    var_dump($response);
	    echo "</pre>";
	    // if ($response->{'result'} == 'success') {
	    //    header("Location: ".$response->{'url'});
	    //    exit();
	    // } else {
	    //    echo $response->{'error'};
	    // }178d978b50d7564bd19051fc4
	}
}