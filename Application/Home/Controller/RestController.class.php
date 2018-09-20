<?php

namespace Home\Controller;

use Think\Controller;

class RestController extends Controller
{
    public function index()
    {
        $this->display();
    }

    public function findByZip()
    {
        if (IS_AJAX) {
            if (!empty($_POST['zipcode'])) {
                $zip = $_POST['zipcode'];
                $arr = M('Restaurants')->join("restandzip on restaurants.rest_id = restandzip.rest_id")
                    ->where('zipcode = %s', $zip)
                    ->scope('findByZipField')
                    ->select();
                if ($arr) {
                    echo JSON_encode($arr);
                } else {
                    echo "No restuarant";
                }
            }
        } else {
            $this->display();
        }
    }

    public function googleMap()
    {
        $this->display();
    }

    public function findByAddr()
    {
        $this->display();
    }

    public function restPage()
    {
        $id = $_GET['id'];
        if (empty($id)) {
            $id = 1;
        }
        $arr = M('Restaurants')->where('rest_id = %s', $id)->select();
        $this->assign('data', $arr[0]);
        $this->display();
    }

    public function receipt_success()
    {
        $this->display();
    }
}