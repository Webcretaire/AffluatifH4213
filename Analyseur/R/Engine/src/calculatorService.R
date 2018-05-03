### Set library 
.libPaths( c("/home/ubuntu/R/x86_64-pc-linux-gnu-library/3.4", .libPaths()) )
# write(.libPaths(),stderr())

library(forecast)
library(ggplot2)
library(rjson)
library(plotly)


### include pascal script
source("modelCalulator.R")

### parse script args
args = commandArgs(trailingOnly=TRUE)
# 1 flux_id

# legacy code
# vec <- c(60,3600,86400)
# for(delta in vec){
#   modelCalculator(args[1],"../data/data.json", paste("../data/",delta,"/", sep = ""), delta)
# }

modelCalculator(args[1],"../data/data.json", "../data/model/", 60)


