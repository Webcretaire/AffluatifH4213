library(forecast)
library(ggplot2)

#========================================================================= FORECASTING SERVICES
forecaster <- function(path, forecastDelta){
  load(toString(path))
  fcast <- forecast(fit, h=forecastDelta)
  fcast$timeSeq <- fit$timeSeq 
  return(fcast)
}