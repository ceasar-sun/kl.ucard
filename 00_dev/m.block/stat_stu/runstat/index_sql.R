library(RMySQL)
library(htmlTable)
con <- dbConnect(MySQL(), user='serena', password='serena@mysql@ucard', dbname='ucard', host='127.0.0.1')
dbListTables(con)
data_index.all <- dbReadTable(con, "ucard.semester_setgrp")
class(data_index.all)
data_index.select <- dbGetQuery(con, "select * from ucard.semester_setgrp where semester='1041' and subno like 'AAAAA' CCCCC")
attach(data_index.select)
summary(data_index.select)
summary(score)

m <- mean(score)
s <- sd(score)
out_quant <- quantile(score,c(0.25,0.5,0.75,1))
output_quart <-
  matrix(paste(out_quant, LETTERS[1:4]),
         ncol=4, byrow = TRUE)
htmlTable(output_quart,header =  paste(c("1st", "2nd",
                            "3rd", "4th"), "Quartile"))
desc.stats <- function(x, na.omit=FALSE){
  x <- x[!is.na(x)]
  n <- length(x)
  mean <- mean(x)
  sd <- sd(x)
  skew <- sum((x-mean)^3/sd^3)/n
  kurt <- sum((x-mean)^4/sd^4)/n -3
  
  summary_table <- list(mean=mean, std=sd,skewness=skew, kurtosis=kurt)
  #return(list(mean=mean, variable=var,skewness=skew, kurtosis=kurt))
  return(summary_table)
}
#################Table#######################
content <- desc.stats(score)
output_table <- 
  matrix(paste(content, LETTERS[1:4]), 
         ncol=4, byrow = TRUE)

htmlTable(output_table,header =  paste(c("mean", "std",
                            "skewness", "kurtosis"), "Descriptive Statistics"))
#################Table#########################
Score_A00_Distribution <- ks.test(score, "pnorm", m, s) #pnorm
Score_A00_Distribution

table(score)



# Close Connection
dbDisconnect(con)
q()
