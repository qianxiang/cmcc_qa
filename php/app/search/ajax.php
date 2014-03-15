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

define('IN_AJAX', TRUE);


if (!defined('IN_ANWSION'))
{
	die;
}

class ajax extends AWS_CONTROLLER
{
	var $per_page = 20;
	
	public function get_access_rule()
	{
		$rule_action['rule_type'] = "white";
		
		if ($this->user_info['permission']['search_avail'])
		{
			$rule_action['rule_type']="black"; //'black'黑名单,黑名单中的检查  'white'白名单,白名单以外的检查
		}
		
		$rule_action['actions'] = array();
		
		return $rule_action;
	}
	
	public function setup()
	{
		HTTP::no_cache_header();
	}
	
	public function search_result_action()
	{
		$limit = intval($_GET['page']) * $this->per_page . ', ' . $this->per_page;
		
		switch ($_GET['search_type'])
		{	
			case 'all':
				$search_result = $this->model('search')->search($_GET['q'], null, $limit);
			break; 
				
			case 'questions':
				$search_result = $this->model('search')->search($_GET['q'], 'questions', $limit);
			break;
			
			case 'topics':
				$search_result = $this->model('search')->search($_GET['q'], 'topics', $limit);
			break;
			
			case 'users':
				$search_result = $this->model('search')->search($_GET['q'], 'users', $limit);
			break;
			
			case 'articles':
				$search_result = $this->model('search')->search($_GET['q'], 'articles', $limit);
			break;
		}
		
		if ($this->user_id AND $search_result)
		{
			foreach ($search_result AS $key => $val)
			{
				switch ($val['type'])
				{
					case 'questions':
						$search_result[$key]['focus'] = $this->model('question')->has_focus_question($val['search_id'], $this->user_id);
					break;
					
					case 'topics':
						$search_result[$key]['focus'] = $this->model('topic')->has_focus_topic($this->user_id, $val['search_id']);
					break;
					
					case 'users':
						$search_result[$key]['focus'] = $this->model('follow')->user_follow_check($this->user_id, $val['search_id']);
					break;
				}
			}
		}
		
		
		TPL::assign('search_result', $search_result);
		
		if ($_GET['template'] == 'm')
		{
			TPL::output('m/ajax/search_result');
		}
		else
		{
			TPL::output('search/ajax/search_result');
		}
	}
	
	public function search_action()
	{
		if ($result = $this->model('search')->search($_GET['q'], $_GET['type'], intval($_GET['limit']), $_GET['topic_ids']))
		{
			H::ajax_json_output($this->model('search')->search($_GET['q'], $_GET['type'], intval($_GET['limit']), $_GET['topic_ids']));
		}
		else
		{
			H::ajax_json_output(array());	
		}
	}
}