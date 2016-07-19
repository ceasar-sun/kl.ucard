import os
import sqlite3
import os
import time
import datetime
import requests
from smartcard.System import readers
from smartcard.util import toHexString

# debug
debug=1

# sqlite
sqlitedb = "/home/thomas/work_house/ucard-reader/ucard_log"
conn = sqlite3.connect(sqlitedb)
cursor = conn.cursor()

# smart card reader
#['Alcor Micro AU9522 00 00', 'Alcor Micro AU9522 00 01', 'NXP PR533 (3.70) 01 00']
r=readers()
if debug: print r
connection = r[2].createConnection()

# cache card id to local database
def cache(cid):

    log_datetime = datetime.datetime.fromtimestamp(time.time()).strftime('%Y-%m-%d %H:%M:%S')
    cursor.execute("INSERT INTO log VALUES (?, ?, ?)", (log_datetime, cid, 'save'))
    if debug: print [log_datetime, cid]
    conn.commit()
    return True

def regist(cid):
    print "regist %s" % cid
    url = "http://moodle.nchc.org.tw/ucard.php?cid=%s&location=2" % cid
    r = requests.get(url)
    print r
    print r.text


# main
# keyword to ask card id
SELECT = [0xFF, 0xCA, 0x00, 0x00, 0x00]
while True:
    print "reading data"
    try:
	connection.connect()
	data, sw1, sw2 = connection.transmit( SELECT )
	if debug: print "%x %x" % (sw1, sw2)
	#90 0
	if debug: print data
	cardid = ''.join(str(e) for e in data)
	if debug: print "cardid: %s" % cardid
	#[201, 164, 245, 230]
	time.sleep(1)
    except:
	if debug: print "no card data"
	time.sleep(1)
	continue


    cache(cardid)
#displaycard()
    regist(cardid)
#finish()
