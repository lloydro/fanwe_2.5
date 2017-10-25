<?php

/**
 * @param $num  要格式化的数据
 */

//如果数据大于10000，则保留两位小数并以万为单位，否则数据不变;并将结果转字符串格式
function format_for_ten_thousand($num)
{
	if(!isset($num))
	{
		$num = '0';
	}
	else if($num>=10000)
	{
		$num = floatval($num)/10000;
		$num = number_format($num,2,'.','');
		$num .= "万";
	}
	$num = strval($num);
	return $num;
}