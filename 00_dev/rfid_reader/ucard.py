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
from qrcode import *

# debug
debug=1

Udata={}
Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no'}
Ulbs={}
Uconf={'locationid':10, 'location_name':"文化中心"}
Ucard_url="http://moodle.nchc.org.tw/moodle/local/ucard"

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
		    Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no'}
		else:
		    Ustatus={'device':'yes', 'card':'yes', 'id':self.rfid_key16, 'oid':'no'}
            except:
		Ustatus={'device':'no', 'card':'no', 'id':'no', 'oid':'no'}
		Udata={}
                continue


#class UpdateData(threading.Thread):
class UpdateData():
    def __init__(self, lbs):
	#threading.Thread.__init__(self)
	self.lbs = Ulbs
	self.locationid=Uconf['locationid']
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
	print "running"
        #self.lbs['date'].set_text(time.strftime('%Y/%m/%d'))
	#self.lbs['time'].set_text(time.strftime('%H:%M:%S'))
	#self.lbs['name'].set_text("")
	self.lbs['info'].set_text("\n課程資料讀取中，請稍候\n")
	#self.lbs['sid'].set_text("")
	#self.lbs['cid'].set_text("")
	#path="icon/moodle.qr.png"
	#self.lbs['qrcode'].set_from_file(path)

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
	if Ustatus['card'] == "no":
	    if debug: print "no card"
	    Udata={}
	    self.clear_labels()
	    return False
	elif Ustatus['id'] == Ustatus['oid']:
	    if debug: print "same id, keep data\n"
	    return False
	else:
	    self.running_labels()
            try:
                url = "%s/ucard1.php?rfid_key16=%s&location=%s" % (Ucard_url, Ustatus['id'], self.locationid)
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
                else:
	            self.error_labels('moodle 資料錯誤')
		    return False
            except:
		self.error_labels('網路異常')
		if debug: print "access data error: url = %s" % url
                time.sleep(1)

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
                wrap_coursemesg = textwrap.fill(dedented_mesg, width=14)
		course_label_mesg = course_label_mesg+"\n"+wrap_coursemesg
        udata={'name':name, 'moodleid':moodleid, 'sid':sid, 'location':location, 'rfid_keyout':rfid_keyout, 'time':ctime, 'date':cdate, 'course':course_label_mesg}
	Udata = udata
	return True

builder = Gtk.Builder()
builder.add_from_file("icon/kl-kiosk-ui.glade")

window = builder.get_object("Xwin")
lname = builder.get_object("name")
linfo = builder.get_object("info")
lsid = builder.get_object("sid")
lcid = builder.get_object("cid")
ldate = builder.get_object("date")
ltime = builder.get_object("time")
lqrcode = builder.get_object("Icode")
lbs = {'name':lname, 'info':linfo, 'sid':lsid, 'cid':lcid, 'date':ldate, 'time':ltime, 'qrcode':lqrcode}
Ulbs=lbs
window.connect("delete-event", Gtk.main_quit)
GObject.timeout_add_seconds(2, UpdateData, lbs)
window.show_all()

#thrUpdate = UpdateData(lbs)
#thrUpdate.daemon = True
#thrUpdate.start()

thrReader = readucarddata()
thrReader.daemon = True
thrReader.start()

Gtk.main()
