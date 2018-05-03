library(forecast)
library(ggplot2)
library(rjson)
library(lubridate)
#========================================================================= FORECASTING SERVICES
modelCalculator <- function(streamId, path, savePath, timeFreq) {
  dataRaw <- getRawDataJSON(path)
  # print(length(dataRaw))
  if (getDataSpan(dataRaw) < 1) {
    #TYPE 1 NLR (NO SEASONAL COMPONENT)
    data <- processData(dataRaw, timeFreq, T)
    ts <- convertToTs(data, as.POSIXct(names(data)[1]), timeFreq)
    
    fit <- tslm(ts ~ trend + I(trend^2) + I(trend^3) + I(trend^4) + I(trend^5) + I(trend^6) + I(trend^7) + I(trend^8) + I(trend^9))
  } else {
    #TYPE 2 HW (SEASONAL COMPONENT)
    # un sample toutes les h
    data <- processData(dataRaw, 3600, F)
    
    
    # generate time axis
    timeSeq <- createSeq(names(data)[1],3600, data)
    print(length(timeSeq))
    
    # print(length(data))
    # print(data)
    ts <- convertToTs(data, as.POSIXct(names(data)[1]), 24)
    # print(length(ts))
    # print(ts)
    fit <-  auto.arima(ts,approximation =FALSE)
    # fit <-  Arima(ts, order = c(1,0,0), seasonal = c(2,1,0))
    # fit <-  Arima(ts, order = c(1,0,1), seasonal = c(1,1,0))
    fit$timeSeq <- timeSeq
  }
  save(fit, file=paste(savePath, toString(streamId), ".rda", sep=""))
  
  return(fit)
}

#========================================================================= INTERNAL SERVICES
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

convertToTs <- function(dataRaw, startPoint, freq) {
  data <- ts(dataRaw, start=startPoint, frequency=freq)
  return(data)
}


processData <- function(data, timeFreq, smallSet) {
  span <- getDataSpan(data)
  
  if(smallSet) {
    nbCycle <- (as.numeric(span)*24*3600)/timeFreq
    f <- approxfun(as.POSIXct(names(data)), as.numeric(unlist(data, use.names = F)))
  } else {
    nbCycle <- (as.numeric(span)*24*3600)/timeFreq
    f1 <- smooth.spline(as.POSIXct(names(data)), as.numeric(unlist(data, use.names = F)), spar=0.4)
    f <- splinefun(f1$x, f1$y)
  }
  
  startPoint <- as.POSIXct(names(data)[1])
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
  
  # plot(data)
  # plot(processedData)
  
  print(nbCycle)
  print(length(dataValues))
  return(processedData)
}

# timestep en seconde
createSeq <-function(startdatetime, timestep,data){
  span <- getDataSpan(data)
  nbCycle <- (as.numeric(span)*24*3600)/timestep
  timesequence <- seq(ymd_hms(startdatetime), 
                      by = paste(timestep, 'sec'),length.out=(nbCycle+1))
  return(timesequence)
}
