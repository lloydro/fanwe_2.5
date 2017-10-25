实时查看： java -jar js.jar

程序会自动清除：2天以前的日志; jdk1.7以上

在后台运行
1、安装成 win32或win64 服务
	把程序放在 D:\yydb_service 目录；
	把public/app.js文件，复制一份到： D:\yydb_service 目录
	以管理员身份运行 InstallJS32.bat 或 InstallJS64.bat

-------------------------------------------------
如何查看系统是32还是64位
1、打开开始--- 运行--- 输入 cmd 
2、出现黑色窗口（命令提示符）界面，输入systeminfo命令
3、加载完成命令之后，输出的信息，如下图找到
	如果是X86就是  32位系统，
	如果是 显示X64 就是64位系统
	
2、linux 配置步骤

==================2-1: 说明=================

https://help.aliyun.com/knowledge_detail/6507861.html#ECS%20Linux%E6%9C%8D%E5%8A%A1%E5%99%A8%E5%AE%89%E8%A3%85JDK%E9%85%8D%E7%BD%AEJAVA%E7%8E%AF%E5%A2%83
ECS Linux服务器安装JDK配置JAVA环境

==================2-2：主要命令=============

把 js_lib,monitorjs.sh,js.jar 这3个 放在  java 目录下

2.1) nohup java -jar js.jar >/dev/null &  //在后台运行;需要先定位到 java 目录
2.2) ps -ef|grep js.jar | grep -v 'grep'    //查询当前后台运行的进程 $pid
2.3) kill -9 `ps -ef | grep 'js.jar' | grep -v 'grep' | awk '{ print $2}'` //结束

==================2-3：配置步骤===============

//把文档格式改为：linux格式
sed -i "s/\r//" ./monitorjs.sh

1、设置执行权限
chmod +x ./monitorjs.sh

2、添加定时监听（每1分钟执行一次，/home/wwwroot/fanwe/java 为程序路径）
命令：crontab -e
1-59 * * * * cd /home/wwwroot/fanwe/java && sh ./monitorjs.sh

==================2-4：操作说明===============

操作说明：
1、输入crontab -e 
2、按 i 进入编辑模式
3、复制粘贴【1-59 * * * * cd /home/wwwroot/fanwe/java &&sh  ./monitorjs.sh】进去，注意替换目录
4、按 esc 键
5、输入 :wq 保存退出
6、查看定时任务是否添加成功
命令：crontab -l
