library(RMySQL)
library(htmlTable)
con <- dbConnect(MySQL(), user='serena', password='serena@mysql@ucard', dbname='ucard', host='127.0.0.1')
dbListTables(con)
data.all <- dbReadTable(con, "ucard.semester_score")
class(data.all)
data.select <- dbGetQuery(con, "select * from ucard.semester_score where semester='1041' and stdyear='BBBBB' and stdlib='AAAAA' CCCCC")
attach(data.select)
summary(data.select)
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


# Plot
#-------------------------------------------------------------
png(file="Density_Score.png", width=1500, height=800, res=120)
hist(score,probability = TRUE)
dens <- density(score)
mean <- mean(score)
sd <- sd(score)
lines(dens, col="red", lty=2)
curve(dnorm(x,mean,sd),col="blue",add=TRUE)
#curve(dnorm(x,mean,sd),col="blue")
legend("topright",inset = 0.05, c("density","normal"),
       lty = c(2,1),col = c("red","blue"))
#-------------------------------------------------------------
png(file="Score__.png", width=1500, height=800, res=120)
hist(score,breaks=20)

png(file="Box_Score.png", width=1500, height=800, res=120)
boxplot(score)

png(file="QQPlot_Score.png", width=1500, height=800, res=120)
qqplot(score,idno)

# Close Connection
dbDisconnect(con)
q()
