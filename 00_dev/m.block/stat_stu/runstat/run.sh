#!/bin/bash
AYear=${1}
Semester=${2}
Grade=${3}
Course=${4}
SCHNo=${5}
WORKDIR=$(cd "$(dirname "$0")"; pwd)
cd ${WORKDIR}
ORGSQLR="${WORKDIR}/hist_sql.R"
NEWSQLR=$(date +"%Y%m%d%H%M%S")
cp ${ORGSQLR} ${WORKDIR}/${NEWSQLR}.R
sed -i "s/1041/${AYear}${Semester}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/BBBBB/${Grade}/g" ${WORKDIR}/${NEWSQLR}.R
if [ "${Course}" != "ALL" ];then
   sed -i "s/and stdlib='AAAAA'/and stdlib='${Course}'/g" ${WORKDIR}/${NEWSQLR}.R
else
   sed -i "s/and stdlib='AAAAA'//g" ${WORKDIR}/${NEWSQLR}.R
fi

if [ "${SCHNo}" != "0" ];then
   sed -i "s/CCCCC/and schno='${SCHNo}'/g" ${WORKDIR}/${NEWSQLR}.R
else
   sed -i "s/CCCCC//g" ${WORKDIR}/${NEWSQLR}.R
fi

sed -i "s/Score__/Score_A${SCHNo}${AYear}${Semester}${Grade}${Course}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/Box_Score/Box_Score_A${SCHNo}${AYear}${Semester}${Grade}${Course}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/QQPlot_Score/QQPlot_Score_A${SCHNo}${AYear}${Semester}${Grade}${Course}/g" ${WORKDIR}/${NEWSQLR}.R
sed -i "s/Density_Score/Density_Score_A${SCHNo}${AYear}${Semester}${Grade}${Course}/g" ${WORKDIR}/${NEWSQLR}.R
/usr/bin/R --no-save < ${WORKDIR}/${NEWSQLR}.R 2>&1 > ${NEWSQLR}.log

#ORGINDEXR="${WORKDIR}/index_sql.R"
#cp ${ORGINDEXR} ${WORKDIR}/index_${NEWSQLR}.R
#sed -i "s/1041/${AYear}${Semester}/g" ${WORKDIR}/index_${NEWSQLR}.R
#if [ "${Course}" != "ALL" ];then
#   sed -i "s/and subno like 'AAAAA'/and subno like '${Course}%'/g" ${WORKDIR}/index_${NEWSQLR}.R
#else
#   sed -i "s/and subno like 'AAAAA'//g" ${WORKDIR}/index_${NEWSQLR}.R
#fi
#
#if [ "${SCHNo}" != "0" ];then
#   sed -i "s/CCCCC/and schno='${SCHNo}'/g" ${WORKDIR}/index_${NEWSQLR}.R
#else
#   sed -i "s/CCCCC//g" ${WORKDIR}/index_${NEWSQLR}.R
#fi
#
#/usr/bin/R --no-save < ${WORKDIR}/index_${NEWSQLR}.R 2>&1 > index_${NEWSQLR}.log

${WORKDIR}/parsing.sh ${NEWSQLR} A${SCHNo}${AYear}${Semester}${Grade}${Course} 1
#${WORKDIR}/parsing.sh index_${NEWSQLR} A${SCHNo}${AYear}${Semester}${Grade}${Course} 3
rm -f ${NEWSQLR}.R
#rm -f index_${NEWSQLR}.R
