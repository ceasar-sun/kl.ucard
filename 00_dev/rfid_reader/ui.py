from Tkinter import *

class APP(Frame):

	def createWidget(self):
		list = ['Time', 'Location', 'Card ID', 'Name', 'Student ID']
		listLength = len(list)
		var = StringVar()
		
	
		#lid = label id
		for lid in range(listLength):
			self.label= Label(self, text=" "+list[lid]+": ", font=16, height=1, pady=8)
			self.label.grid(row=lid, sticky=W, ipadx=45) 		
		
		#eid = entry id
		for eid in range(listLength):
			self.entry= Entry(self, width=40, textvariable=var)
			self.entry.grid(row=eid, column=1, sticky=E) 				

 		self.mulEntry= Text(self, height=5, width=60, pady=8)
		self.mulEntry.grid(row=5, column=0, columnspan=2, sticky=E)


	def __init__(self, data=None):
		Frame.__init__(self, data)
		self.pack(anchor=NW) #display on main window
		self.createWidget()


def main():
	root = Tk()
	root.title('Window')
	root.geometry('560x315') #size

	win = APP(data=root)
	win.mainloop() #display on screen
	root.quit()

main()

