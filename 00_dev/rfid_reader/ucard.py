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

# debug
debug=1

Udata={}
Ustatus={'device':'no', 'card':'no', 'id':'no'}
Ulbs={}
Uconf={'locationid':10, 'location_name':"文化中心"}

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
                    Ustatus={'device':'no', 'card':'no', 'id':'no'}
		else:
                    Ustatus={'device':'yes', 'card':'yes', 'id':self.rfid_key16}
            except:
                Ustatus={'device':'no', 'card':'no', 'id':'no'}
		Udata={}
                continue


#class UpdateData(threading.Thread):
class UpdateData():
    def __init__(self, lbs):
	#threading.Thread.__init__(self)
	self.lbs = Ulbs
	self.rfid_key16 = ""
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

    def error_labels(self, mesg):
	self.lbs['time'].set_text(time.strftime('%H:%M:%S'))
	self.lbs['name'].set_text("")
	self.lbs['info'].set_text("\n卡片資訊讀取錯誤\n(錯誤代碼：%s)，請重試。\n或是連繫管理員" % mesg)
	self.lbs['sid'].set_text("")
	self.lbs['cid'].set_text("")

    def clear_labels(self):
	self.lbs['time'].set_text(time.strftime('%H:%M:%S'))
	self.lbs['name'].set_text("")
	self.lbs['info'].set_text("\n請放置卡片以查詢課程進度")
	self.lbs['sid'].set_text("")
	self.lbs['cid'].set_text("")

    def update_labels(self):
	self.lbs['name'].set_text(Udata['name'])
	self.lbs['info'].set_text(Udata['course'])
	self.lbs['sid'].set_text(Udata['sid'][:8])
	self.lbs['cid'].set_text(Udata['rfid_keyout'])
	self.lbs['time'].set_text(Udata['time'])
	self.lbs['info'].set_text(Udata['course'])

    def access_moodle(self):

	global Udata
	global Ustatus
	if Ustatus['card'] == "no":
	    if debug: print "no card"
	    Udata={}
	    self.rfid_key16 = ""
	    self.clear_labels()
	    return False
	elif Ustatus['id'] == self.rfid_key16:
	    if debug: print "same id %s = %s, keep data\n" % (Ustatus['id'], self.rfid_key16)
	    return False
	else:
            try:
                url = "http://moodle.nchc.org.tw/moodle/local/ucard/ucard1.php?rfid_key16=%s&location=%s" % (Ustatus['id'], self.locationid)
                r = requests.get(url)
                moodle_data_st = json.loads(r.text)
                if moodle_data_st['status'] == '1':
                    moodle_data = moodle_data_st['result']
                    moodle_course = moodle_data_st['courses']
                    sid = moodle_data['sid']
                    rfid_keyout = moodle_data['rfid_keyout']
                    name = moodle_data['name']
		    self.rfid_key16 = Ustatus['id']
                else:
	            self.error_labels('moodle 資料錯誤')
		    return False
            except:
		self.error_labels('網路異常')
		if debug: print "access data error: url = %s" % url
                time.sleep(1)

        ctime = time.strftime('%Y/%m/%d %H:%M:%S')
        location = Uconf['location_name']
	course_label_mesg=""
	for coursename in moodle_course:
	    if coursename != '':
		cmesg = u'進行中'
		if moodle_course[coursename] == 'YES' :
		    cmesg = u'完成'
		coursemesg = u"課程 %s, %s\n" % (coursename, cmesg)
		course_label_mesg = course_label_mesg+coursemesg
	udata={'name':name, 'sid':sid, 'location':location, 'rfid_keyout':rfid_keyout, 'time':ctime, 'course':course_label_mesg}
	Udata = udata
	return True

builder = Gtk.Builder()
builder.add_from_file("icon/kl-kiosk-ui.glade")

window = builder.get_object("Xwin")
lname = builder.get_object("name")
linfo = builder.get_object("info")
lsid = builder.get_object("sid")
lcid = builder.get_object("cid")
ltime = builder.get_object("time")
lbs = {'name':lname, 'info':linfo, 'sid':lsid, 'cid':lcid, 'time':ltime}
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
