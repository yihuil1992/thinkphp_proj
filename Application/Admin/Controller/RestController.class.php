<?php
namespace Admin\Controller;
use Think\Controller;

class RestController extends Controller{
	public function index(){
		header('Location: '.$uri.'/Home/findbyzip.html');
		exit;
	}
	public function findByZip(){
		echo "find successfully!";
	}
}