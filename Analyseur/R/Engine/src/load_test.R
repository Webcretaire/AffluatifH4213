library(xts)
library(zoo)
library(readr)
data <- read_csv("~/Documents/INSA/4IF/smart/Affluatif-Analyseur/R/Engine/data.csv", col_types = cols(date = col_datetime(format = "%Y-%m-%d %H:%M:%S")))
data.ts = as.ts(data)

