GatewayWorker 统一配置模板，拓展核心功能

php start.php start //以debug（调试）方式启动
php start.php start -d //以daemon（守护进程）方式启动
php start.php stop //停止
php start.php restart //重启
php start.php reload //平滑重启
php start.php status //查看状态
php start.php connections //查看连接状态


lsof -i:端口号  //查看端口占用
sudo kill -9 $(lsof -i:端口号 -t)//解除端口占用
