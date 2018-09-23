<?php
/**
 * Created by PhpStorm.
 * User: Yihui Liu OMEN
 * Date: 2018/9/22
 * Time: 15:50
 */

namespace Social\Controller;
use Think\Controller;
class TopicController extends Controller {

    public function index() {
        $user = session('user');
        if ($user) {
            //添加friend的topic列表
            //先获得所有friend
            $friend_id_array = array();
            $from_friends = M('Friend')->where('`to_user_id`=' . $user['user_id'] . ' AND `status` = "accepted"')->select();
            foreach ($from_friends as $from_friend) {
                array_push($friend_id_array, $from_friend['from_user_id']);
            }
            $to_friends = M('Friend')->where('`from_user_id`=' . $user['user_id'] . ' AND `status` = "accepted"')->select();
            foreach ($to_friends as $to_friend) {
                array_push($friend_id_array, $to_friend['to_user_id']);
            }
            //添加自己
            array_push($friend_id_array, $user['user_id']);

            $friend_id_str = implode(',', $friend_id_array);
            $topic_id_array = array();
            $self_topics = M('Topic')->where('`user_id` IN (' . $friend_id_str . ')')->select();
            foreach ($self_topics as $topic) {
                array_push($topic_id_array, $topic['topic_id']);
            }

            $hood_info = M('Hoods')->where('`hood_corner1x` <=' . $user['number'] . ' AND `hood_corner2x` >=' . $user['number']
                . ' AND `hood_corner1y` <=' . $user['street'] . ' AND `hood_corner2y` >=' . $user['street'])->find();
            if ($topic_id_array) {
                $topic_id_str = implode(',', $topic_id_array);
                $other_topics = M('Hoodgroup')->where('`hood_id` = ' . $hood_info['hood_id'] .
                    ' AND `topic_id` NOT IN (' . $topic_id_str . ')')->select();
            } else {
                $other_topics = M('Hoodgroup')->where('`hood_id` = ' . $hood_info['hood_id'])->select();
            }
            foreach($other_topics as $other_topic) {
                array_push($topic_id_array, $other_topic['topic_id']);
            }
            $topic_id_str = implode(',', $topic_id_array);
            $topic_list = array();
            if ($topic_id_array) {
                $topic_list = M('Topic')->join('`subject` ON `subject`.`sub_id` = `topic`.`sub_id`')
                                        ->join('`user` ON `user`.`user_id` = `topic`.`user_id`')
                                        ->field('topic.*, user.user_name, subject.sub_description')
                                        ->where('`topic_id` IN (' . $topic_id_str . ')')
                                        ->order('date_created desc')->select();
            }

            for ($i = 0; $i < count($topic_list); $i++) {
                $topic_list[$i]['reply'] = M('Message')->join('`user` ON `user`.`user_id` = `message`.`user_id`')
                    ->where('`topic_id`=' . $topic_list[$i]['topic_id'])
                    ->field('message.*, user.user_name')
                    ->order('create_time desc')->select();
            }

            $subjects = M('Subject')->select();

            $this->assign('user', $user);
            $this->assign('subjects', $subjects);
            $this->assign('topic_list', $topic_list);
            $this->display();
        }
    }

    public function postTopic() {
        date_default_timezone_set('US/Eastern');
        $user = session('user');
        $topic['title'] = I('post.title');
        $topic['user_id'] = $user['user_id'];
        $topic['sub_id'] = I('post.sub_id');
        $topic['date_created'] = date('Y-m-d H:i:s');

        if (!$topic['title']) {
            $response = "input something!";
        }
        if ($response) {
            echo json_encode($response);
        } else {
            $res = M('Topic')->add($topic);

            if ($res) {
                $hood_info = M('Hoods')->where('`hood_corner1x` <=' . $user['number'] . ' AND `hood_corner2x` >=' . $user['number']
                    . ' AND `hood_corner1y` <=' . $user['street'] . ' AND `hood_corner2y` >=' . $user['street'])->find();
                $hoodgroup['topic_id'] = $res;
                $hoodgroup['hood_id'] = $hood_info['hood_id'];
                M('Hoodgroup')->add($hoodgroup);
                $response = "ok";
                echo json_encode($response);
            }
        }
    }

    public function delTopic() {
        if (IS_POST) {
            $topic_id = I('post.topic_id');
            $res = M('Topic')->where('`topic_id`=' . $topic_id)->delete();
            if ($res) {
                M('Message')->where('`topic_id`=' . $topic_id)->delete();
                $response = "ok";
                echo json_encode($response);
            }
        }
    }

    public function reply() {
        date_default_timezone_set('US/Eastern');
        if (IS_POST) {
            $user = session('user');
            $reply['text'] = I('post.reply_text');
            $reply['topic_id'] = I('post.topic_id');
            $reply['user_id'] = $user['user_id'];
            $reply['create_time'] = date('Y-m-d H:i:s');
            if (!$reply['text']) {
                $response = "input something!";
            }

            if ($response) {
                echo json_encode($response);
            } else {
                $res = M('Message')->add($reply);
                if ($res) {
                    $response = "ok";
                    echo json_encode($response);
                }
            }
        }
    }

    public function delReply() {
        if (IS_POST) {
            $msg_id = I('post.msg_id');
            $res = M('Message')->where('`msg_id`=' . $msg_id)->delete();
            if ($res) {
                $response = "ok";
                echo json_encode($response);
            }
        }
    }
}