<?php
/**
 * Created by PhpStorm.
 * User: Yihui Liu OMEN
 * Date: 2018/9/21
 * Time: 20:42
 */

namespace Social\Controller;
use Think\Controller;
class UserController extends Controller {
    public function index() {
        $user = session('user');
        if ($user) {
            $this->assign('user', $user);
            if ($user['block_id']) {
                $vote_wating = M('Voting')->where('`user_id`=' . $user['user_id'])->find();
                $this->assign('vote_wating', $vote_wating);
                $this->display();
            } else {
                $is_applying = M('Applicant')->where('`user_id`=' . $user['user_id'] . ' AND status = "pending"')->find();
                $this->assign('is_applying', $is_applying);
                $this->display('noblock');
            }
        }
    }

    public function login() {
        if (IS_POST) {
            $email = I('post.email');
            $password = I('post.password');
            $response['code'] = 0;
            if (!$email) {
                $response['msg'] = 'email address required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['msg'] = 'email format no correct';
            } elseif (!$password) {
                $response['msg'] = 'password required';
            }

            if (!$response['msg']) {
                $login = M('login')->where('`email` LIKE "' . $email . '" AND `password` LIKE "' . $password . '"')->find();
                if (!$login) {
                    $response['msg'] = 'the email or password is incorrect.';
                } else {
                    $user = M('User')->where('`user_id`=' . $login['user_id'])->find();
                    $user['email'] = $email;
                    session('user', $user);
                    $response['code'] = 1;
                    $this->assign('user', $user);
                }
            }

            $this->assign('response', $response);
            $this->display('loginresult');
        } else {
            $this->display();
        }
    }

    public function logout() {
        session('user', null);
        $this->redirect('Index/index');
    }

    public function signup() {
        if (IS_POST) {
            $email = I('post.email');
            $password = I('post.password');
            $user_name = I('post.user_name');
            $addr_number = I('post.number');
            $addr_street = I('post.street');
            $description = I('post.description');
            $photo = I('post.photo');

            $response['code'] = 0;
            if (!$email) {
                $response['msg'] = 'email address required';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response['msg'] = 'the email or password is incorrect.';
            } elseif (!$password) {
                $response['msg'] = 'password required';
            } elseif (!$user_name) {
                $response['msg'] = 'user name required';
            } elseif (!$addr_number) {
                $response['msg'] = 'address number required';
            } elseif($addr_number < 0 || $addr_number > 600) {
                $response['msg'] = 'please input a valid address number';
            } elseif (!$addr_street) {
                $response['msg'] = 'street number required';
            } elseif($addr_street < 0 || $addr_street > 50) {
                $response['msg'] = 'please input a valid street number';
            }

            $email_exist = M('login')->where('`email` LIKE "' . $email . '"')->find();
            if ($email_exist) {
                $response['msg'] = 'Email has been registered';
            }

            if (!$response['msg']) {
                $newLogin['email'] = $email;
                $newLogin['password'] = $password;
                $res = M('login')->add($newLogin);
                if ($res) {
                    $newUser['user_id'] = $res;
                    $newUser['user_name'] = $user_name;
                    $newUser['number'] = $addr_number;
                    $newUser['street'] = $addr_street;
                    $newUser['description'] = $description;
                    $newUser['photo'] = $photo;
                    $res2 = M('User')->add($newUser);
                    if ($res2) {
                        $response['code'] = 1;
                    }
                    $this->assign('user', $newUser);
                }
            }

            $this->assign('response', $response);
            $this->display('signupresult');

        } else {
            $this->display();
        }
    }

    public function applyBlock(){
        date_default_timezone_set('US/Eastern');
        $user = session('user');
        if (!$user['block_id']) {
            $block_info = M('Blocks')->where('`block_corner1x` <=' . $user['number'] . ' AND `block_corner2x` >=' . $user['number']
                . ' AND `block_corner1y` <=' . $user['street'] . ' AND `block_corner2y` >=' . $user['street'])
                ->find();
            $is_applied = M('Applicant')->where('`user_id`=' . $user['user_id'] .
                ' AND `block_id`='. $block_info['block_id'] .
                ' AND `status` = "pending"')
                ->find();
            if ($is_applied) {
                //正在申请
                $status = 0;
            } else {
                $status = 1;
                $voters = M('User')->where('`block_id`=' . $block_info['block_id'])->select();
                $application['user_id'] = $user['user_id'];
                $application['vote'] = 0;
                $application['voter'] = count($voters);
                $application['voted'] = 0;
                $application['status'] = 'pending';
                $application['time'] = date('Y-m-d H:i:s');
                $application['block_id'] = $block_info['block_id'];
                $res = M('Applicant')->add($application);
                if (!$voters) {
                    $status = 2;
                    M('Applicant')->where('`app_id`=' . $res)->save(array('status' => 'approved'));
                    M('User')->where('`user_id`=' . $user['user_id'])->save(array('block_id' => $block_info['block_id']));
                    $user['block_id'] = $block_info['block_id'];
                    session('user', $user);
                } else {
                    foreach ($voters as $voter) {
                        M('Voting')->add(array('app_id' => $res, 'user_id' => $voter['user_id']));
                    }
                }
            }
            $this->assign('block_info', $block_info);
            $this->assign('status', $status);
            $this->display();
        }
    }

    public function vote() {
        $user = session('user');
        if (IS_POST) {
            $app_id = I('post.app_id');
            $action = I('post.action');
            $app = M('Applicant')->where('`app_id`=' . $app_id)->find();
            $status = 'pending';
            if ($action =='accept') {
                //更新app['vote']和['voted']的值
                $app['vote'] = $app['vote'] + 1;
                $app['voted'] = $app['voted'] + 1;
                if ($app['voter'] <= 3) {
                    if ($app['vote'] == $app['voter']) {
                        //总人数小于3，并全票接受，通过该申请
                        $status = 'approved';
                    }
                } else {
                    //总人数大于3，得票到达3票则通过申请
                    if ($app['vote'] == 3) {
                        $status = 'approved';
                    }
                }
                if ($status == 'approved') {
                    //更新通过了的用户的block_id
                    M('User')->where('`user_id`=' . $app['user_id'])->save(array('block_id' => $app['block_id']));
                }
                $res = M('Applicant')->where('`app_id`=' . $app['app_id'])
                                     ->save(array('vote' => $app['vote'], 'voted' => $app['voted'], 'status' =>$status));
                if ($res) {
                    $res2 = M('Voting')->where('`app_id`=' . $app_id . ' AND `user_id`=' . $user['user_id'])->delete();
                }
            } elseif ($action == 'decline') {
                //更新app['voted']的值
                $app['voted'] = $app['voted'] + 1;
                if ($app['voter'] <= 3) {
                    //总人数不多于3， 此时一旦有人拒绝则不再是全票通过，拒绝该申请
                    $status = 'declined';
                } else {
                    //总人数大于3时，若因为该用户的拒绝，导致即使剩下的所有人都通过也不能凑齐3票
                    //则拒绝该申请
                    if ($app['vote'] + ($app['voter'] - $app['voted']) < 3) {
                        $status = 'declined';
                    }
                }
                $res = M('Applicant')->where('`app_id`=' . $app['app_id'])
                    ->save(array('voted' => $app['voted'], 'status' =>$status));
                if ($res) {
                    $res2 = M('Voting')->where('`app_id`=' . $app_id . ' AND `user_id`=' . $user['user_id'])->delete();
                }
            }

            if ($status != 'pending') {
                //或成功或失败，无论如何，此时都该把voting中该app_id的条目删除
                M('Voting')->where('`app_id`=' . $app_id)->delete();
            }

            if ($res2) {
                echo json_encode('ok');
            } else {
                echo json_encode('Something wrong');
            }

        } else {
            $vote_list = M('Voting')->where('`user_id`=' . $user['user_id'])->select();
            if ($vote_list) {
                $app_id_array = array();
                foreach ($vote_list as $vote) {
                    array_push($app_id_array, $vote['app_id']);
                }
                $app_id_array_str = implode(',', $app_id_array);
                $app_user_list = M('User')->join('`applicant` ON `user`.`user_id` = `applicant`.`user_id`')
                    ->where('`status` = "pending" AND `app_id` IN (' . $app_id_array_str . ')')->select();
            }
            $this->assign('user_list', $app_user_list);
            $this->display();
        }
    }

    public function friends() {
        $user = session('user');

        $friend_id_array = array();
        $from_friends = M('Friend')->where('`to_user_id`=' . $user['user_id'] . ' AND `status` = "accepted"')->select();
        foreach ($from_friends as $from_friend) {
            array_push($friend_id_array, $from_friend['from_user_id']);
        }
        $to_friends = M('Friend')->where('`from_user_id`=' . $user['user_id'] . ' AND `status` = "accepted"')->select();
        foreach ($to_friends as $to_friend) {
            array_push($friend_id_array, $to_friend['to_user_id']);
        }
        $friend_list = array();
        if ($friend_id_array) {
            $friend_id_str = implode(',', $friend_id_array);
            $friend_list = M('User')->where('`user_id` IN (' . $friend_id_str . ')')->select();
        }
        $applying_friend = M('Friend')->join('`user` ON `user`.`user_id` = `to_user_id`')
            ->where('`from_user_id`=' . $user['user_id'] . ' AND `status` = "pending"')
            ->select();
        foreach ($applying_friend as $friend) {
            array_push($friend_id_array, $friend['user_id']);
        }

        $friend_request = M('Friend')->join('`user` ON `user`.`user_id` = `from_user_id`')
            ->where('`to_user_id`=' . $user['user_id'] . ' AND `status` = "pending"')
            ->select();
        foreach ($friend_request as $friend) {
            array_push($friend_id_array, $friend['user_id']);
        }

        if ($friend_id_array) {
            $friend_id_str = implode(',', $friend_id_array);
            $block_list = M('User')->where('`block_id`=' . $user['block_id'] .
                                    ' AND `user_id` NOT IN (' . $friend_id_str . ')')->select();
        } else {
            $block_list = M('User')->where('`block_id`=' . $user['block_id'])->select();
        }


        $this->assign('block_list', $block_list);
        $this->assign('friend_request', $friend_request);
        $this->assign('applying_friend', $applying_friend);
        $this->assign('friend_list', $friend_list);
        $this->display();
    }

    public function acceptRequest() {
        $user = session('user');
        if (IS_POST) {
            $from_user_id = I('post.user_id');
            $res = M('Friend')->where('`from_user_id`=' . $from_user_id . ' AND `to_user_id`=' . $user['user_id'])
                              ->save(array('status' => 'accepted'));
            if ($res) {
                $response = "ok";
                echo json_encode($response);
            }
        }
    }

    public function deleteFriend() {
        $user = session('user');
        if (IS_POST) {
            $friend_user_id = I('post.user_id');
            $res = M('Friend')->where('(`from_user_id`=' . $friend_user_id . ' AND `to_user_id`=' . $user['user_id'] .
                        ') OR (' . '`to_user_id`=' . $friend_user_id . ' AND `from_user_id`=' . $user['user_id'] . ')')
                        ->delete();
            if ($res) {
                $response = "ok";
                echo json_encode($response);
            }
        }
    }

    public function sendRequest() {
        $user = session('user');
        if (IS_POST) {
            $friend['from_user_id'] = $user['user_id'];
            $friend['to_user_id'] = I('post.user_id');
            $friend['status'] = 'pending';
            $res = M('Friend')->add($friend);
            if ($res) {
                $response = "ok";
                echo json_encode($response);
            }
        }
    }

    public function searchByEmail() {
        $user = session('user');
        if (IS_POST) {
            $friend_id_array = array();
            $from_friends = M('Friend')->where('`to_user_id`=' . $user['user_id'])->select();
            foreach ($from_friends as $from_friend) {
                array_push($friend_id_array, $from_friend['from_user_id']);
            }
            $to_friends = M('Friend')->where('`from_user_id`=' . $user['user_id'])->select();
            foreach ($to_friends as $to_friend) {
                array_push($friend_id_array, $to_friend['to_user_id']);
            }

            $email = I('post.email');
            if ($email == $user['email']) {
                $response['status'] = 'error';
                $response['msg'] = 'You can not add yourself as friend.';
            } else {
                $search_user = M('Login')->where('`email` LIKE "' . $email . '"')->find();
                if ($search_user) {
                    if (in_array($search_user['user_id'], $friend_id_array)) {
                        $response['status'] = 'error';
                        $response['msg'] = 'The user is already in your friend list.';
                    } else {
                        $target_user = M('User')->where('`user_id`=' . $search_user['user_id'])->find();
                        $response['user'] = $target_user;
                        $response['status'] = 'ok';
                        $response['msg'] = 'find user successfully';
                    }
                } else {
                    $response['status'] = 'error';
                    $response['msg'] = 'Can not find a user with this email.';
                }
            }

            echo json_encode($response);
        }
    }

}