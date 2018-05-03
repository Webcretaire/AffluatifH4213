library(forecast)
library(ggplot2)
library(rjson)

#========================================================================= FORECASTING SERVICES
directSampleForecast <- function(streamId, path, timeFreq, forecastDelta) {
  dataRaw <- getRawDataJSON(path)
  
  if (getDataSpan(dataRaw) < 1) {
    #TYPE 1 NLR (NO SEASONAL COMPONENT)
    #fcast <- splinef(ts, h=forecastDelta)
    data <- processData(dataRaw, timeFreq, T)
    ts <- convertToTs(data, as.POSIXct(names(data)[1]), timeFreq)
    
    fit <- tslm(ts ~ trend + I(trend^2) + I(trend^3) + I(trend^4) + I(trend^5) + I(trend^6) + I(trend^7) + I(trend^8) + I(trend^9))
    fcast <- forecast(fit, h=forecastDelta)
    plot(fit$fitted.values)
  } else {
    #TYPE 2 HW (SEASONAL COMPONENT)
    #fcast <- hw(ts, h=forecastDelta)
    #fcast <- holt(ts, h=forecastDelta)
    #fcast <- forecast(ts, h=forecastDelta)
    data <- processData(dataRaw, 3600, F)
    ts <- convertToTs(data, as.POSIXct(names(data)[1]), 24)
    
    fit <-  auto.arima(ts)
    fcast <- forecast(fit, h=forecastDelta)
  }
  save(fit, file=paste("./Engine/data/", toString(streamId), ".rda"))
  
  plot(fcast)
  return(fcast)
}

modelCalculator <- function(streamId, path, savePath, timeFreq) {
  dataRaw <- getRawDataJSON(path)
  
  if (getDataSpan(dataRaw) < 1) {
    #TYPE 1 NLR (NO SEASONAL COMPONENT)
    data <- processData(dataRaw, timeFreq, T)
    ts <- convertToTs(data, as.POSIXct(names(data)[1]), timeFreq)
    
    fit <- tslm(ts ~ trend + I(trend^2) + I(trend^3) + I(trend^4) + I(trend^5) + I(trend^6) + I(trend^7) + I(trend^8) + I(trend^9))
  } else {
    #TYPE 2 HW (SEASONAL COMPONENT)
    data <- processData(dataRaw, 3600, F)
    ts <- convertToTs(data, as.POSIXct(names(data)[1]), 24)
    
    fit <-  auto.arima(ts)
  }
  save(fit, file=paste(savePath, toString(streamId), ".rda", sep=""))
  
  return(fit)
}

forecaster <- function(path, forecastDelta){
  load(toString(path))
  fcast <- forecast(fit, h=forecastDelta)
  
  return(fcast)
}

#========================================================================= INTERNAL SERVICES

#autoplot capsule service
printplot <- function(ts, title, xtag, ytag){
  print(autoplot(ts) +
          ggtitle(title) +
          xlab(xtag) +
          ylab(ytag))
}

getDataSpan <- function(data){
  span <- as.POSIXct(tail(names(data), n=1)) - (as.POSIXct(names(data)[1]))
  units(span) <- "days"
  return(span)
}

#JSON to R data retrieving
getRawDataJSON <- function(path) {
  dataRaw <- fromJSON(file = path)
  dataValues <- c(as.numeric(unlist(dataRaw, use.names = F)))
  dataNames <- as.POSIXct(names(dataRaw))
  dataList <- setNames(dataValues, dataNames)
  
  return(dataList)
}


getSampleData <- function(path, timeFreq) {
  dataRaw <- getRawDataJSON(path)
  data <- processData(dataRaw, timeFreq)
  
  return(data)
}


getModelData <- function(streamId) {
  dataRaw <- getRawDataJSON(path)
  dataList <- dataRaw[[streamId]]
  
  return(dataList)
}


convertToTs <- function(dataRaw, startPoint, freq) {
  data <- ts(dataRaw, start=startPoint, frequency=freq)
  
  return(data)
}


assembleModel <- function(sampleData, modelData) {
  model <- c(modelData, sampleData)
  startPoint <- as.numeric(names(model)[1])
  endPoint <- as.numeric(tail(names(model), n=1))
  
  f <- splinefun(as.numeric(names(model)), as.numeric(unlist(model, use.names = F)))
  
  
  return(model)
}


processData <- function(data, timeFreq, smallSet) {
  startPoint <- as.POSIXct(names(data)[1])
  endPoint <- as.POSIXct(tail(names(data), n=1))
  
  if(smallSet) {
    span <- endPoint - startPoint
    units(span) <- "days"
    nbCycle <- (as.numeric(span)*24*3600)/timeFreq
    
    f <- approxfun(as.POSIXct(names(data)), as.numeric(unlist(data, use.names = F)))
  } else {
    span <- endPoint - startPoint
    units(span) <- "days"
    nbCycle <- (as.numeric(span)*24*3600)/timeFreq
    
    f1 <- smooth.spline(as.POSIXct(names(data)), as.numeric(unlist(data, use.names = F)), spar=0.4)
    f <- splinefun(f1$x, f1$y)
  }
  
  dataValues <- f(as.POSIXct(startPoint))
  dataNames <- as.POSIXct(startPoint)
  interval <- as.POSIXct(startPoint + timeFreq)
  for (i in 1:nbCycle){
    if(f(as.POSIXct(interval)) < 0) {
      dataValues <- c(dataValues, 0)
    } else {
      dataValues <- c(dataValues, f(as.POSIXct(interval)))
    }
    dataNames <- c(dataNames, as.POSIXct(interval))
    
    interval <- as.POSIXct(interval + timeFreq)
  }
  
  processedData <- setNames(dataValues, dataNames)
  
  plot(data)
  plot(processedData)
  
  return(processedData)
}

print("----------------------------------- main start")

#================================== Retrieving JSON data
par(mfrow=c(4,1))

#res <- directSampleForecast("65416541", "./Engine/datat.json", 60, 100)

modelCalculator("6541654", "./Engine/datat.json", "./Engine/data/", 60)
print(list.files())
res <- forecaster("./Engine/data/6541654.rda", 10)
#res <- getSampleData("./Engine/data.json", 3600)
#f <- splinefun(as.numeric(names(data)), as.numeric(unlist(data, use.names = F)))
#curve(f(x, deriv = 0), 1910, 2000)

#ts <- convertToTs(f(x, deriv = 0), 1910)
#data <- dataRaw[['muchAboveNormal']]
#data <- as.data.frame(dataRaw.muchAboveNormal())

print("data retrieved :")
print(res)
plot(res)

#save(f, file = "f.RData")

#================================== Ploting data

#colnames(data) <- NULL
#rownames(data) <- NULL

#printplot(ts1, "US Climate Extremes Index", "Year", "Measure")

print("----------------------------------- main end")
