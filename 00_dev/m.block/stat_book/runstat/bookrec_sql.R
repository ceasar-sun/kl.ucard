library(RMySQL)
library(htmlTable)

png(file="Book.png", width=1500, height=800, res=120)
book_account <- c(book0,book1,book2,book3,book4,book5,book6,book7,book8,book9)
barplot(book_account)

# Close Connection
q()
