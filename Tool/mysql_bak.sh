#!/bin/bash
bak_dir=/data/mysql/back/

if [ ! -d ${bak_dir} ]
then
	mkdir -p ${bak_dir}
fi

user="root"
pwd="root"
dbname="ztb"
filename=$(date "+%Y%m%d".zip)

if [ ! -w ${bak_dir}${filename} ]
then
	touch ${bak_dir}${filename}
	chmod 0666 ${bak_dir}${filename}
fi 

mysqldump --skip-opt -q --no-autocommit --default-character-set=utf8mb4 -u${user} -p${pwd} ztb | gzip > ${bak_dir}${filename}

#删除3天前备份
last=$(date -d "3 days ago" +%Y%m%d.zip)

if [ -f ${bak_dir}${last} ]
then
	rm -rf ${bak_dir}${last}
fi


#导入命令
#gunzip < /data/mysql/back/20210713-0748.zip | mysql -uroot -proot --default-character-set=utf8mb4 ztb
