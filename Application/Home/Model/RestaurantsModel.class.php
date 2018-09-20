<?php
namespace Home\Model;
use Think\Model;

class RestaurantsModel extends Model{
	protected $_scope = array(
		'findByZipField'=>array(
			'field'=>'restaurants.rest_id,rest_name,rest_addr'
		)
	);
}