import os
#import sqlite3
import os
import time
import datetime
import requests
from smartcard.System import readers
from smartcard.util import toHexString

# debug
debug=0

class Ureader():
    def __init__(self):
	if debug: print "init"
	self.connection = ""
	
    def connect(self):
	# smart card reader
	#['Alcor Micro AU9522 00 00', 'Alcor Micro AU9522 00 01', 'NXP PR533 (3.70) 01 00']
	r = readers()
	if debug: print r
	self.connection = r[2].createConnection()
    def read(self):
	# keyword to ask card id
	SELECT = [0xFF, 0xCA, 0x00, 0x00, 0x00]

        if debug: print "reading data"
        try:
	    self.connection.connect()
	    data, sw1, sw2 = self.connection.transmit( SELECT )
	    if debug: print "%x %x" % (sw1, sw2)
	    #90 0
	    if debug: print data
	    #cardid = ''.join(str(e) for e in data)
	    cardid = ''.join(hex(e).upper()[2:] for e in data)
	    if debug: print "cardid: %s" % cardid
	    return cardid
	except:
	    if debug: print "no card data"
	    return False

 
if __name__ == '__main__':
    rfid=Ureader()
    rfid.connect()
    data = rfid.read()
    print data

