<?php

class aliprule_list_auto_cache extends auto_cache{
	private $key = "aliprule:list";
	public function load($param)
	{
		$list = $GLOBALS['cache']->get($this->key);

		if($list === false)
		{
			//unserialize(
			$sql = "select id,name,money,(diamonds + gift_diamonds) as diamonds,gift_coins,iap_money,gift_diamonds from ".DB_PREFIX."recharge_rule where is_effect = 1 and is_delete = 0  order by sort";
			$list = $GLOBALS['db']->getAll($sql,true,true);
			foreach($list as $k=>$v){
				$list[$k]['name'] = '支付宝'.$v['name'];
				if(intval($v['gift_diamonds'])>0){
					$list[$k]['name'] = '支付宝'.$v['name']."(赠送".$v['gift_diamonds']."钻石)";
				}
			}
			$GLOBALS['cache']->set($this->key,$list);
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