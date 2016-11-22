#!/bin/bash

WORKDIR=$(cd "$(dirname "$0")"; pwd)
cd ${WORKDIR}
RLOG="${1}.log"
#RLOG="20160731233225.log"
Count=${3}
for LineNum in `grep -n 'gmisc_table' ${RLOG} | cut -f1 -d:`;do
   TableStart=`echo ${LineNum}`
   TableEnd=`expr ${TableStart} + 15`
   cat ${RLOG} | sed -n "${TableStart},${TableEnd}p" | sed "s/border-bottom/border-left: 2px solid grey;border-right: 2px solid grey;border-bottom/g" > ${2}.${Count}st
   Count=`expr ${Count} + 1`
done
rm -f ${RLOG}
