# -*- coding: utf-8 -*-
import gi
gi.require_version('Gtk', '3.0')
from gi.repository import Gtk, Gdk, GObject, GLib
GObject.threads_init()
import time
import threading
import ucardreader
import requests
import json
import textwrap
import urllib
import codecs
from qrcode import *

# debug
debug=1

displaysec=5
keydata=""
Udata={}
Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no', 'time':0}
Ulbs={}
Uconf={'locationid':0, 'location_name':"尚未連線"}
Ucard_url="https://learning.kl.edu.tw/moodle/local/ucard"
#Ucard_url="http://moodle.nchc.org.tw/moodle/local/ucard"

class Handler:
    def key(self, widget, event):
	global keydata
	keyname = Gdk.keyval_name(event.keyval)
	print "Key %s (%d) was pressed" % (keyname, event.keyval)
	if event.keyval >= 65294:
	    Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no', 'time':0}
	    return
	if event.keyval == 65293:
	    print keydata
	    self.testinput(keydata.upper())
	    return
	keydata=keydata+keyname

    def testinput(self, entry_text):
	global Ustatus
	global keydata
        print "Entry contents: %s\n" % entry_text
	Ustatus={'device':'yes', 'card':'yes', 'id':entry_text, 'oid':'no', 'time':0}
	keydata=''



class readucarddata(threading.Thread):

    def __init__(self):
	self.rfid_key16 = False
        threading.Thread.__init__(self)

    def run(self):
	global Ustatus
	global Udata
        while True:
	    time.sleep(2)
            try:
                if Ustatus['device'] == 'no':
                    rfid=ucardreader.Ureader()
                    rfid.connect()
                    Ustatus['device'] == 'yes'
                self.rfid_key16 = rfid.read()
		if self.rfid_key16 == False:
		    Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no', 'time':0}
		else:
		    Ustatus={'device':'yes', 'card':'yes', 'id':self.rfid_key16, 'oid':'no', 'time':0}
            except:
		Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no', 'time':0}
		Udata={}
                continue


#class UpdateData(threading.Thread):
class UpdateData():
    def __init__(self, lbs):
	#threading.Thread.__init__(self)
	self.lbs = Ulbs
	self.locationid=Uconf['locationid']
        if self.locationid == 0:
            read_location()
            self.lbs['location'].set_text(Uconf['location_name'])
	self.run()

    def run(self):
	#while True:
	#    print Udata
	#    print Ustatus
	#    if self.access_moodle() == True:
	#	self.update_labels()
	#    time.sleep(2)
	if self.access_moodle() == True:
	    self.update_labels()

    def running_labels(self):
	self.lbs['info'].set_text("\n課程資料讀取中，請稍候\n")

    def error_labels(self, mesg):
        self.lbs['date'].set_text(time.strftime('%Y/%m/%d'))
	self.lbs['time'].set_text(time.strftime('%H:%M:%S'))
	self.lbs['name'].set_text("")
	self.lbs['info'].set_text("\n卡片資訊讀取錯誤\n(錯誤代碼：%s)，請重試。\n或是連繫管理員" % mesg)
	self.lbs['sid'].set_text("")
	self.lbs['cid'].set_text("")
	path="icon/moodle.qr.png"
	self.lbs['qrcode'].set_from_file(path)

    def clear_labels(self):
        self.lbs['date'].set_text(time.strftime('%Y/%m/%d'))
	self.lbs['time'].set_text(time.strftime('%H:%M:%S'))
	self.lbs['name'].set_text("")
	self.lbs['info'].set_text("\n請放置卡片以查詢課程進度")
	self.lbs['sid'].set_text("")
	self.lbs['cid'].set_text("")
	path="icon/moodle.qr.png"
	self.lbs['qrcode'].set_from_file(path)

    def update_labels(self):
	self.lbs['name'].set_text(Udata['name'])
	self.lbs['info'].set_text(Udata['course'])
	self.lbs['sid'].set_text(Udata['sid'][:8])
	self.lbs['cid'].set_text(Udata['rfid_keyout'])
	self.lbs['date'].set_text(Udata['date'])
	self.lbs['time'].set_text(Udata['time'])
	self.lbs['info'].set_text(Udata['course'])
	qrtext="%s/student_courses.php?moodleid=%s/" % (Ucard_url, Udata['moodleid'])
        path="/tmp/qrcode.png"
        qr = QRCode(version=1, error_correction=ERROR_CORRECT_L, box_size=2, border=0)
        qr.add_data(qrtext)
        qr.make() # Generate the QRCode itself
        # im contains a PIL.Image.Image object
        im = qr.make_image()
        # To save it
        im.save(path)
	self.lbs['qrcode'].set_from_file(path)

    def access_moodle(self):

	global Udata
	global Ustatus
	global displaysec
	if Ustatus['card'] == "no":
	    if debug: print "no card"
	    Udata={}
	    self.clear_labels()
	    return False
	elif Ustatus['time'] != 0 and time.time() - Ustatus['time'] >= displaysec:
	    if debug: print "card removed"
	    Udata={}
	    Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no', 'time':0}
	    self.clear_labels()
	    return False
	elif Ustatus['id'] == Ustatus['oid']:
	    if debug: print "same id, keep data\n"
	    return False
	else:
	    self.running_labels()
            try:
                url = "%s/ucard1.php?rfid_key16=%s&location=%s" % (Ucard_url, Ustatus['id'], self.locationid)
		if debug: print url
                r = requests.get(url)
                moodle_data_st = json.loads(r.text)
                if moodle_data_st['status'] == '1':
                    moodle_data = moodle_data_st['result']
                    moodle_course = moodle_data_st['courses']
                    moodleid = moodle_data['moodleid']
                    sid = moodle_data['sid']
                    rfid_keyout = moodle_data['rfid_keyout']
                    name = moodle_data['name']
		    Ustatus['oid'] = Ustatus['id']
		    Ustatus['time'] = time.time()
                else:
	            self.error_labels('moodle 資料錯誤')
		    Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no', 'time':0}
		    return False
            except:
		self.error_labels('網路異常')
		if debug: print "access data error: url = %s" % url
                time.sleep(1)
		return False

        ctime = time.strftime('%H:%M:%S')
        cdate = time.strftime('%Y/%m/%d')
        location = Uconf['location_name']
	course_label_mesg=""
	for coursename in moodle_course:
	    if coursename != '':
		cmesg = u'進行中'
		if moodle_course[coursename] == 'YES' :
		    cmesg = u'完成'
		coursemesg = u"課程 %s, %s\n" % (coursename, cmesg)
                dedented_mesg = textwrap.dedent(coursemesg)
                wrap_coursemesg = textwrap.fill(dedented_mesg, width=22)
		course_label_mesg = course_label_mesg+"\n"+wrap_coursemesg
        udata={'name':name, 'moodleid':moodleid, 'sid':sid, 'location':location, 'rfid_keyout':rfid_keyout, 'time':ctime, 'date':cdate, 'course':course_label_mesg}
	Udata = udata
	return True

def read_location():
    #f = codecs.open("test", "r", "utf-8")
    f = codecs.open('/boot/location', 'r', "utf-8-sig")
    location_str = f.read()
    
    location_str = location_str.rstrip()

    url_location_str = urllib.quote(location_str.encode('utf-8'))
    #url = "%s/location.php?location=%s" % (Ucard_url, location_str)
    url = "%s/location.php?location=%s" % (Ucard_url, url_location_str)
    location_id = ''
    try:
	r = requests.get(url)
	location_id = r.text
	if debug: print "response = %s" % (r)
    except:
	location_id = ''
	if debug: print "requests.get url fail"

    if location_id != '':
        global Uconf
        Uconf={'locationid':location_id, 'location_name':location_str}
    else:
	time.sleep(2)
        Uconf={'locationid':0, 'location_name':location_str.encode('utf-8')+"連線定位失敗"}
	if debug: print "location in text = %s" % (location_str)
	if debug: print "location in url = %s" % (url)
    

read_location()
builder = Gtk.Builder()
builder.add_from_file("icon/kl-kiosk-ui.glade")
builder.connect_signals(Handler())

window = builder.get_object("Xwin")
lname = builder.get_object("name")
linfo = builder.get_object("info")
lsid = builder.get_object("sid")
lcid = builder.get_object("cid")
ldate = builder.get_object("date")
ltime = builder.get_object("time")
llocation = builder.get_object("location")
lqrcode = builder.get_object("Icode")
lbs = {'name':lname, 'info':linfo, 'sid':lsid, 'cid':lcid, 'date':ldate, 'time':ltime, 'qrcode':lqrcode, 'location':llocation}
Ulbs=lbs
window.connect("delete-event", Gtk.main_quit)
GObject.timeout_add_seconds(2, UpdateData, lbs)
llocation.set_text(Uconf['location_name'])
window.show_all()

#thrUpdate = UpdateData(lbs)
#thrUpdate.daemon = True
#thrUpdate.start()

#thrReader = readucarddata()
#thrReader.daemon = True
#thrReader.start()

Gtk.main()
