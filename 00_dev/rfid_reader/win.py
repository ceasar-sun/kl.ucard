# -*- coding: utf-8 -*-
import pygtk, gtk
pygtk.require('2.0')
import time, pango


class MyWindow(gtk.Window):

	def __init__(self):
		super(MyWindow, self).__init__()
		
		win = gtk.Window(gtk.WINDOW_TOPLEVEL)
		win.set_position(gtk.WIN_POS_CENTER)
		win.set_size_request(560,315)
		win.set_title("User Information")
		win.connect("destroy", self.close)
		
		#draw area
		self.area = gtk.DrawingArea()
		self.area.set_size_request(560, 315)
		self.area.modify_bg(gtk.STATE_NORMAL, gtk.gdk.color_parse('#aeebff'))

		#label
		self.pangolayout = self.area.create_pango_layout("")
	
		fixed = gtk.Fixed()
		fixed.put(self.area, 0, 0)
		win.add(fixed)
		self.area.connect("expose-event", self.area_expose_bg)	
		self.area.connect("expose-event", self.area_upper)
		self.area.connect("expose-event", self.area_lower)
		self.area.connect("expose-event", self.logo)
		self.area.show()		

		win.show_all()

	#components that will be shown on the window
	def area_expose_bg(self, area, event):
		w, h = area.window.get_size()
		self.gc = area.window.new_gc()	
		
		#background		
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#C4f3ff'))
		self.area.window.draw_rectangle(self.gc, True, 20, 20, 520, 275)
		
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#E7faff'))
		self.area.window.draw_rectangle(self.gc, True, 25, 15, 510, 285)

		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#FFFFFF'))
		self.area.window.draw_rectangle(self.gc, True, 33, 10, 494, 295)
		
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#e1e3e4'))
		self.area.window.draw_line(self.gc, 335, 60, 335, 305)

		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#e1e3e4'))
		self.area.window.draw_line(self.gc, 35, 60, 525, 60)
		return 

	def area_upper(self, area, event):

		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#13427a'))
		self.pangolayout.set_font_description (pango.FontDescription("KacstDigital 22"))
		self.pangolayout.set_text("樂   學   網")
		self.area.window.draw_layout(self.gc, 210, 12, self.pangolayout)
		return
	
	def area_lower(self, area, event):	
		#lower left window(name, other info)
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#131313'))
		self.pangolayout.set_font_description (pango.FontDescription("KacstDigital 12"))
		self.pangolayout.set_text("姓名: ")
		self.area.window.draw_layout(self.gc, 88, 70, self.pangolayout)	

		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#131313'))
		self.pangolayout.set_text("課程資訊: ")
		self.area.window.draw_layout(self.gc, 88, 145, self.pangolayout)

		#area for name input
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#e1e3e4'))
		self.area.window.draw_rectangle(self.gc, True, 50, 102, 200, 28)
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#131313'))
		self.pangolayout.set_font_description (pango.FontDescription("KacstDigital 10"))
		self.pangolayout.set_text("aaa")
		self.area.window.draw_layout(self.gc, 60, 105, self.pangolayout)	
		
		#area for other info input
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#e1e3e4'))
		self.area.window.draw_rectangle(self.gc, True, 50, 176, 270, 110)
		self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#131313'))
		self.pangolayout.set_text("aaa")
		self.area.window.draw_layout(self.gc, 60, 181, self.pangolayout)

		#lower right window
		#sid = student ID, cid = card ID
		sid = '學號'
		cid = '卡號'
		now = time.strftime('%Y/%m/%d %H:%M:%S')
		location = '場館'
		contact = '聯絡方式'
		items = [sid, cid, now, location, contact]
		yarea = 61
		ycontent = 70
		for i in range(len(items)):
			if i%2 == 0:
				self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#e1e3e4'))
			else:
				self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#FFFFFF'))
			
			self.area.window.draw_rectangle(self.gc, True, 336, yarea, 190, 40)
			self.gc.set_rgb_fg_color(gtk.gdk.color_parse('#131313'))
			self.pangolayout.set_font_description (pango.FontDescription("KacstDigital 11"))			
			self.pangolayout.set_text(items[i])
			self.area.window.draw_layout(self.gc, 380, ycontent, self.pangolayout)	
			yarea += 40			
			ycontent += 40	

		self.pangolayout.set_font_description (pango.FontDescription("KacstDigital 8"))
		self.pangolayout.set_text("(for any issue)")
		self.area.window.draw_layout(self.gc, 455, 245, self.pangolayout)	

		return
	
	def logo(self, area, event):	
		#keelung education center logo
		img = gtk.gdk.pixbuf_new_from_file_at_size("icon/edu.jpg", 130, 140)
		self.image = gtk.gdk.Pixmap(area.window, img.get_width(), img.get_height())
		self.image.draw_pixbuf(self.gc, img, 0, 0, 0, 0)
		self.area.window.draw_drawable(self.gc, self.image, 0, 0, 340, 270, *self.image.get_size())

		#NCHC logo
		img = gtk.gdk.pixbuf_new_from_file_at_size("icon/nchc.jpg", 55, 50)
		self.image = gtk.gdk.Pixmap(area.window, img.get_width(), img.get_height())
		self.image.draw_pixbuf(self.gc, img, 0, 0, 0, 0)
		self.area.window.draw_drawable(self.gc, self.image, 0, 0, 470, 262, *self.image.get_size())

		#sid, cid, time, location, contact icon
		icons = ['icon/sid.png', 'icon/cid.png', 'icon/time.png', 'icon/location.png', 'icon/contact.png', 'icon/name.png', 'icon/note.png']
		
		yLeft = 65 #left window
		yRight = 65 #right window
		for i in range(len(icons)):
			img = gtk.gdk.pixbuf_new_from_file_at_size(icons[i], 31, 31)
			self.image = gtk.gdk.Pixmap(area.window, img.get_width(), img.get_height())
			self.image.draw_pixbuf(self.gc, img, 0, 0, 0, 0)
			if i < 5: 

				self.area.window.draw_drawable(self.gc, self.image, 0, 0, 340, yRight, *self.image.get_size())
				yRight += 40
			else:
				
				self.area.window.draw_drawable(self.gc, self.image, 0, 0, 50, yLeft, *self.image.get_size())
				yLeft += 75
		return
	

	#close the application
	def close(self, widget, data=None):
		gtk.main_quit()
		

	def main(self):
		gtk.main()

if __name__ == "__main__":

   	win = MyWindow() #run class MyWindow 
	win.main()

