<?php
/*
+--------------------------------------------------------------------------
|   WeCenter [#RELEASE_VERSION#]
|   ========================================
|   by WeCenter Software
|   © 2011 - 2013 WeCenter. All Rights Reserved
|   http://www.wecenter.com
|   ========================================
|   Support: WeCenter@qq.com
|   
+---------------------------------------------------------------------------
*/


if (!defined('IN_ANWSION'))
{
	die;
}

define('GEO_EARTH_RADIUS', 6371);	// 地球半径, 平均半径为 6371km

class geo_class extends AWS_MODEL
{
	/**
	* 计算某个经纬度的周围某段距离的正方形的四个点
	*
	* @param longitude float 经度
	* @param latitude float 纬度
	* @param radius float 该点所在圆的半径,该圆与此正方形内切, 单位: 千米
	* @return array 正方形的四个点的经纬度坐标
	*/
	public function get_square_point($longitude, $latitude, $radius = 1)
	{
		$target_longitude = rad2deg((2 * asin(sin($radius / (2 * GEO_EARTH_RADIUS)) / cos(deg2rad($latitude)))));
		
		$target_latitude = rad2deg(($radius / GEO_EARTH_RADIUS));
		
		return array(
			'TL' => array('latitude' => $latitude + $target_latitude, 'longitude' => $longitude - $target_longitude),	// Top left point 
			//'TR' => array('latitude' => $latitude + $target_latitude, 'longitude' => $longitude + $target_longitude),	// Top right point 
			//'BL' => array('latitude' => $latitude - $target_latitude, 'longitude' => $longitude - $target_longitude),	// Bottom left point 
			'BR' => array('latitude' => $latitude - $target_latitude, 'longitude' => $longitude + $target_longitude)	// Bottom right point 
		);
	}
	
	public function set_location($item_type, $item_id, $longitude, $latitude)
	{
		$this->delete('geo_location', "`item_type` = '" . $this->quote($item_type) . "' AND `item_id` = " . intval($item_id));
		
		return $this->insert('geo_location', array(
			'item_type' => $item_type,
			'item_id' => $item_id,
			'longitude' => $longitude,
			'latitude' => $latitude,
			'add_time' => time()
		));
	}
	
	public function get_distance($latitude_a, $longitude_a, $latitude_b, $longitude_b)
	{
		/*
		Convert these degrees to radians
		to work with the formula
		*/
		
		$latitude_a = ($latitude_a * pi()) / 180;
		$longitude_a = ($longitude_a * pi()) / 180;
		
		$latitude_b = ($latitude_b * pi()) / 180;
		$longitude_b = ($longitude_b * pi()) / 180;
		
		/*
		Using the
		Haversine formula
		
		http://en.wikipedia.org/wiki/Haversine_formula
		
		calculate the distance
		*/
		
		$calcLongitude = $longitude_b - $longitude_a;
		$calcLatitude = $latitude_b - $latitude_a;
		$stepOne = pow(sin($calcLatitude / 2), 2) + cos($latitude_a) * cos($latitude_b) * pow(sin($calcLongitude / 2), 2);  $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
		
		$calculatedDistance = GEO_EARTH_RADIUS * $stepTwo;
		
		return round($calculate);
	}
}