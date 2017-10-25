<?php

// 资金管理——统计模块

class StatisticsModuleAction extends CommonAction{


	

	/**
	 * 统计图表
	 */
	public function chart()
	{

		//列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		$sql_pay .= 'is_paid=1 and ';	
		if($map2['start_time'] == '' && $map2['end_time'] == ''){	
			$_REQUEST['start_time'] =date("Y-m-d",mktime(0,0,0,date('m'),1,date('Y')));
			$_REQUEST['end_time'] = date("Y-m-d",mktime(23,59,59,date('m'),date('t'),date('Y')));
			$map2['start_time'] = to_timespan($_REQUEST['start_time']);
			$map2['end_time'] =to_timespan($_REQUEST['end_time'])+86399;
		}	

/*		$map2['start_time'] = 1473177600;
		$map2['end_time'] = 1474632000;*/

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$sql_pay .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";	
		}		

		$model = D ();
		$sql_str = "SELECT sum(money) money,DATE_FORMAT(FROM_UNIXTIME(pay_time+28800),'%Y-%m-%d') bdate FROM ".DB_PREFIX."payment_notice WHERE 1=1 ";
		$user_payment_sql .= " and ".$sql_pay." 1=1 ";

		if($map2['end_time'] > to_timespan('2017-07-01')){
			$user_payment_sql .= ' and payment_id !=2 ';
		}


		$sql_str .= " and ".$sql_pay." 1=1 group by bdate ORDER BY bdate asc ";
        $payRatesql =$GLOBALS['db']->getAll($sql_str);

		/*admin_ajax_return($payRatesql);*/
		$sql_refund .= 'is_pay=3 and ';	
		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$sql_refund .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";	
		}
		$model = D ();
		$sql_str = "SELECT sum(money) money,DATE_FORMAT(FROM_UNIXTIME(pay_time+28800),'%Y-%m-%d') bdate FROM ".DB_PREFIX."user_refund WHERE 1=1 ";
		$user_refund_sql .= " and ".$sql_refund." 1=1 ";		
		$sql_str .= " and ".$sql_refund." 1=1 group by bdate ORDER BY bdate asc ";
        $refundRatesql =$GLOBALS['db']->getAll($sql_str);

        //整理两张表的X坐标
        for($i=0;$i<count($payRatesql);$i++)
        {
        	for($j=0;$j<count($refundRatesql);$j++)
        	{
        		if($payRatesql[$i]['bdate'] == $refundRatesql[$j]['bdate'])
        		break; 
        	}
        	if($j==count($refundRatesql))
        	{
        		$refundRatesql[] = array('money'=>0,'bdate'=>$payRatesql[$i]['bdate']);
        	}
        }
        for($i=0;$i<count($refundRatesql);$i++)
        {
        	for($j=0;$j<count($payRatesql);$j++)
        	{
        		if($refundRatesql[$i]['bdate'] == $payRatesql[$j]['bdate'])
        		break; 
        	}
        	if($j==count($payRatesql))
        	{
        		$payRatesql[] = array('money'=>0,'bdate'=>$refundRatesql[$i]['bdate']);
        	}
        }

        //两表均按照时间排序
        for($k = 0;$k<count($refundRatesql);$k++)
        {
        	for($l = 0; $l<count($refundRatesql)-$k-1; $l++)
        	if($refundRatesql[$l]['bdate']>$refundRatesql[$l+1]['bdate'])
        	{
        		$tmp = $refundRatesql[$l];
        		$refundRatesql[$l] = $refundRatesql[$l+1];
        		$refundRatesql[$l+1] = $tmp;
        	}
        }
        for($k = 0;$k<count($payRatesql);$k++)
        {
        	for($l = 0; $l<count($payRatesql)-$k-1; $l++)
        	if($payRatesql[$l]['bdate']>$payRatesql[$l+1]['bdate'])
        	{
        		$tmp = $payRatesql[$l];
        		$payRatesql[$l] = $payRatesql[$l+1];
        		$payRatesql[$l+1] = $tmp;
        	}
        }

        $this->assign ( 'list', $payRatesql );
		$this->assign ( 'list2', $refundRatesql );


		$user_payment = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where 1=1 ".$user_payment_sql));
		$this->assign("user_payment",$user_payment);

		$user_refund = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_refund where 1=1 ".$user_refund_sql));
		$this->assign("user_refund",$user_refund);
		$this->display ();
		return;
	}



	/*
     * 充值统计
    */
    public function statistics_recharge(){
        //列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		$parameter .= 'is_paid=1&';
		$sql_w .= 'is_paid=1 and ';

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			//unset($map2);
		}		
					
		$model = D ();

		$sql_str = "SELECT sum(money) money,user_id,is_paid" .
		" FROM ".DB_PREFIX."payment_notice WHERE 1=1 ";


		if($map2['end_time'] > to_timespan('2017-07-01')){
			$user_refund_sql .= ' and payment_id !=2 ';
		}

		$user_refund_sql .= " and ".$sql_w." 1=1 ";		
		$sql_str .= " and ".$sql_w." 1=1 group by user_id ";
		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'money');
		$this->assign ( 'list', $voList );


		$count = $model->query($sql_str);
		$count = count($count);
		$this->assign("count",$count);

		$user_refund = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."payment_notice where 1=1 ".$user_refund_sql));
		$this->assign("user_refund",$user_refund);
		$this->display ();
		return;
    }

    /**
	 * 提现统计
	 */
	public function statistics_refund()
	{
		//列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		$parameter .= 'is_pay=3&';
		$sql_w .= 'is_pay=3 and ';

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			unset($map2);
		}		
					
		$model = D ();

		$sql_str = "SELECT sum(money) money,sum(ticket) ticket,user_id,is_pay" .
		" FROM ".DB_PREFIX."user_refund WHERE 1=1 ";

		$user_refund_sql .= " and ".$sql_w." 1=1 ";		
		$sql_str .= " and ".$sql_w." 1=1 group by user_id ";

		$voList = $this->_Sql_list($model, $sql_str, "&".$parameter,'money');
		$this->assign ( 'list', $voList );

		$count = $model->query($sql_str);
		$count = count($count);
		$this->assign("count",$count);

		$user_refund = floatval($GLOBALS['db']->getOne("SELECT sum(money) FROM ".DB_PREFIX."user_refund where 1=1 ".$user_refund_sql));

		$this->assign("user_refund",$user_refund);
		$this->display ();
		return;
	}


	//导出电子表,type=1为充值统计表，否则为提现统计表
	public function export_csv($page = 1)
	{

		$type = $_REQUEST['type'];
		/*admin_ajax_return($type);*/
	
		$pagesize = 10;
		set_time_limit(0);
		$limit = (($page - 1)*intval($pagesize)).",".(intval($pagesize));

		//列表过滤器，生成查询Map对象
		$map2 = $this->com_search();
		if($type)
		{
			$sql_w .= 'is_paid=1 and ';
		}
		else
		{
			$sql_w .= 'is_pay=3 and ';
		}
		

		if($map2['start_time'] != '' && $map2['end_time'] != ''){
			$parameter.="pay_time between '". $map2['start_time'] . "' and '". $map2['end_time'] ."'&";
			$sql_w .="pay_time between '". $map2['start_time']. "' and '". $map2['end_time'] ."' and ";		
			//unset($map2);
		}		
		
		if($type)	//充值
		{
			$sql_str = "SELECT t1.user_id,t2.nick_name as name,sum(t1.money) money,t1.is_paid" .
			" FROM ".DB_PREFIX."payment_notice t1,".DB_PREFIX."user t2 WHERE t1.user_id = t2.id and 1=1 ";
		}		
		else
		{
			$sql_str = "SELECT sum(t1.money) as money,t2.nick_name as name,sum(t1.ticket) as ticket,t1.user_id" .
			" FROM ".DB_PREFIX."user_refund t1,".DB_PREFIX."user t2  WHERE  t1.user_id = t2.id and 1=1 ";
		}
		
		if($map2['end_time'] > to_timespan('2017-07-01')){
			$user_refund_sql .= ' and payment_id !=2 ';
		}

		$user_refund_sql .= " and ".$sql_w." 1=1 ";		
		$sql_str .= " and ".$sql_w." 1=1 group by user_id ";

        $time ='1970-01-01 16:00:00';
        $sql =$sql_str." limit ";
        $sql .= $limit;
        /*admin_ajax_return($sql);*/
        $list=$GLOBALS['db']->getAll($sql);
        /*admin_ajax_return($list);*/
		if($list)
		{
			register_shutdown_function(array(&$this, 'export_csv'), $page+1);
            $m_config = load_auto_cache('m_config');
            $ticket_name = $m_config['ticket_name']!=''?$m_config['ticket_name']:'印票';
            if($type)
			$refund_value = array( 'user_id'=>'""','name'=>'""', 'money'=>'""');
			else
			$refund_value = array( 'user_id'=>'""','name'=>'""','ticket'=>'""', 'money'=>'""');	
			if($page == 1)
			{
				if($type)
				$content = iconv("utf-8","gbk","用户ID,用户昵称,充值金额￥");
				else
				$content = iconv("utf-8","gbk","用户ID,用户昵称,提现印票,提现金额￥");	
				$content = $content . "\n";
			}
			foreach($list as $k=>$v)
			{
				$refund_value['user_id'] = '"' . iconv('utf-8','gbk',$list[$k]['user_id']) . '"';
				$refund_value['money'] = '"' . iconv('utf-8','gbk',$list[$k]['money']) . '"';
				$refund_value['name'] = '"' . iconv('utf-8','gbk',$list[$k]['name']) . '"';
				if(!$type)
				{
					$refund_value['ticket'] = '"' . iconv('utf-8','gbk',$list[$k]['ticket']) . '"';
				}
				$content .= implode(",", $refund_value) . "\n";
			}

			//
			if($type)
			header("Content-Disposition: attachment; filename=recharge_statistics.csv");
			else
			header("Content-Disposition: attachment; filename=refund_statistics.csv");
			echo $content ;
		}
		else
		{
			if($page==1)
				$this->error(L("NO_RESULT"));
		}

	}


}