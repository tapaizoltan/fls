#! /bin/bash
project_name=fls
project_folder=fls

today="$(date '+%Y-%m-%d')"

# SQL szerver config
db_host=mysql
db_port=3306
db_database=fls
db_username=root
db_password=root

clear

# Adatbázis mentés
echo "MySql adatok mentese..."
sleep 1
mysqldump --host="$db_host" --port="$db_port" --user="$db_username" --password="$db_password" $db_database > /var/www/html/$project_folder/database/backup/$db_database-$today.sql
echo $(date) /database/backup/$db_database-$today.sql: mysqldump >> /var/www/html/$project_folder/storage/logs/daily_finish.log
echo "MySql adatok mentese... OK!"

# Git repository feltöltés

# echo "Napi munka feltoltese a Git repository-ba..."
# sleep 1
# git add --all
# git commit -m "Napi munka feltoltese a Git repository-ba."
# git fetch
# git push -u origin main
# echo $(date) origin main: git push >> /storage/logs/daily_finish.log
# echo "Napi munka feltoltese a Git repository-ba... OK!"
# echo "Jo pihenest! ;-)"