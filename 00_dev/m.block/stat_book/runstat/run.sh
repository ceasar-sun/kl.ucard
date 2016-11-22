#!/bin/bash
Book0=${1}
Book1=${2}
Book2=${3}
Book3=${4}
Book4=${5}
Book5=${6}
Book6=${7}
Book7=${8}
Book8=${9}
Book9=${10}
PNGName=${11}
WORKDIR=$(cd "$(dirname "$0")"; pwd)
cd ${WORKDIR}
ORGSQLR="${WORKDIR}/bookrec_sql.R"
NEWSQLR=$(date +"%Y%m%d%H%M%S")
cp ${ORGSQLR} ${WORKDIR}/${NEWSQLR}.R
sed -i "s/Book.png/${PNGName}.png/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book0/${Book0}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book1/${Book1}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book2/${Book2}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book3/${Book3}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book4/${Book4}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book5/${Book5}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book6/${Book6}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book7/${Book7}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book8/${Book8}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/book9/${Book9}/g" ${WORKDIR}/${NEWSQLR}.R

/usr/bin/R --no-save < ${WORKDIR}/${NEWSQLR}.R 2>&1 > ${NEWSQLR}.log
rm -f ${NEWSQLR}.R
