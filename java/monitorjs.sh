count=`ps -ef | grep 'js.jar' | grep -v 'grep' |wc -l`
#echo "$count"
if [ 0 == $count ];then 
nohup /usr/bin/java -jar ./js.jar >/dev/null &
fi