<?php
namespace Social\Controller;
use Think\Controller;
class IndexController extends Controller {
    public function index(){
        if (!session('user')) {
            $this->display();
        } else{
            $user = session('user');
            if ($user['block_id']) {
                $this->redirect('Topic/index');
            } else {
                $this->redirect('User/index');
            }
        }
    }
}