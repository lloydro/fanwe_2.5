<?php

class banner_edu_list_auto_cache extends auto_cache{
	private $key = "banner_edu:list:";
	
	public function load($params = array(), $is_real)
	{
		$type = $params['type'] ? intval($params['type']) : 0;
		$show_position = $params['show_position'] ? intval($params['show_position']) : 0;
		$this->key .= "{$type}_{$show_position}";
		$key_bf = $this->key.'_bf';
		$list = $GLOBALS['cache']->get($this->key,true);
		
		if ($list === false || !$is_real) {
			$is_ok =  $GLOBALS['cache']->set_lock($this->key);
			if(!$is_ok){
				$list = $GLOBALS['cache']->get($key_bf,true);
			}else{
				
				$where=" 1=1 ";
				if(isset($params['type']))
				{
					$where .=" and type=".$params['type']."";
				}
				if(isset($params['show_position']))
				{
					$where .=" and show_position=".$show_position."";
				}
				$sql = "select id,title,image,url,type,show_id,show_position from ".DB_PREFIX."index_image where ".$where." order by sort asc";
				$list = $GLOBALS['db']->getAll($sql,true,true);
				foreach($list as $k=>$v){
					$list[$k]['image'] = get_spec_image($v['image']);

                    if ($v['type'] == 8 && $v['show_id'] > 0) {
                        //直播列表
                        $m_config = load_auto_cache("m_config");//初始化手机端配置
                        $sdk_version_name = strim($_REQUEST['sdk_version_name']);
                        $dev_type = strim($_REQUEST['sdk_type']);
                        if ($dev_type == 'ios' && $m_config['ios_check_version'] != '' && $m_config['ios_check_version'] == $sdk_version_name) {
                            $video_list = load_auto_cache("edu_select_video_check");
                        } else {
                            $video_list = load_auto_cache("edu_select_video");
                        }

                        foreach($video_list as $kk => $vv){
                            if(($vv['room_id'] == $v['show_id']) && $vv['is_verify'] ==0){
                                $video=$vv;
                            }
                        }

                        if($video)
                        {
                            $list[$k]['video'] = $video;
                        }else{
                            unset($list[$k]);
                        }
                    }
				}
                $list = array_values($list);
				$GLOBALS['cache']->set($this->key, $list, 3600, true);
				
				$GLOBALS['cache']->set($key_bf, $list, 86400, true);//备份
				//echo $this->key;
			}
 		}
		
		return $list;
	}
	
	public function rm($param)
	{
		$GLOBALS['cache']->rm($this->key);
	}
	
	public function clear_all()
	{
		$GLOBALS['cache']->rm($this->key);
	}
}
?>