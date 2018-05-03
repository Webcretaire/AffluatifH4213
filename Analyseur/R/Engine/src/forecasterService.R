### Set library and plotly
.libPaths( c("/home/ubuntu/R/x86_64-pc-linux-gnu-library/3.4", .libPaths()) )
# write(.libPaths(),stderr())
Sys.setenv("plotly_username"="xxx")
Sys.setenv("plotly_api_key"="yyy")

library(forecast)
library(ggplot2)
library(rjson)
library(plotly)
library(lubridate)


### include pascal script
source("forecaster.R")

### parse script args
args = commandArgs(trailingOnly=TRUE)
# 1 flux_id
# 2 time unit (60, 3600 or 86400)
# 3 delta_seconds
# 4 round robin number

# legacy code
# path <- paste("../data/",args[2],"/",args[1],".rda",sep="")
# write(path,stderr())
# fore <- forecaster(path, args[3])

path <- paste("../data/model/",args[1],".rda",sep="")
fore <- forecaster(path, as.integer(args[3]))

# seuillage
fore$mean <-ifelse(fore$mean>=0,fore$mean,0)
fore$lower[,2] <-ifelse(fore$lower[,2]>=0,fore$lower[,2],0)
fore$lower[,1] <-ifelse(fore$lower[,1]>=0,fore$lower[,1],0)
fore$upper[,2] <-ifelse(fore$upper[,2]>=0,fore$upper[,2],0)
fore$upper[,1] <-ifelse(fore$upper[,1]>=0,fore$upper[,1],0)

# time pred
write(length(fore$timeSeq),stderr())
write(length(fore$x),stderr())
timesequenceForecast <- seq(ymd_hms(tail(fore$timeSeq,n=1)), 
                            by = '1 hour',length.out=(as.integer(args[3])+1))

# break issue
lower2 <- c(tail(fore$x,n=1),fore$lower[, 2])
lower1 <- c(tail(fore$x,n=1),fore$lower[, 1])
upper2 <- c(tail(fore$x,n=1),fore$upper[, 2])
upper1 <- c(tail(fore$x,n=1),fore$upper[, 1])
mean   <- c(tail(fore$x,n=1),fore$mean)

p <- plot_ly() %>%
    add_lines(x = fore$timeSeq, y = fore$x,
              color = I("black"), name = "observed") %>%
    add_ribbons(x = timesequenceForecast, ymin = lower2, ymax = upper2,
                color = I("gray95"), name = "95% confidence") %>%
    add_ribbons(x = timesequenceForecast, ymin = lower1, ymax = upper1,
                color = I("gray80"), name = "80% confidence") %>%
    add_lines(x = timesequenceForecast, y = mean, color = I("blue"), name = "prediction")

# Create a shareable link to your chart
# Set up API credentials: https://plot.ly/r/getting-started
args = commandArgs(trailingOnly=TRUE)
file <- paste("multiple-forecast",args[4])
chart_link = api_create(p, filename=file)
cat(chart_link$web_url)
fileURL<-file("../../../Python/url.txt")
writeLines(c(chart_link$web_url), fileURL)
close(fileURL)

