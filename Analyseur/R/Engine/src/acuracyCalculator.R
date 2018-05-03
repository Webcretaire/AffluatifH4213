accuracyCalculator <- function(path, timeFreq, deltaPast) {
  dataRaw <- getRawDataJSON(path)
  
  if (getDataSpan(dataRaw) < 1) {
    #TYPE 1 NLR (NO SEASONAL COMPONENT)
    print("Data sample is too small please input a sample equivalent to a day or more.")
  } else {
    #TYPE 2 HW (SEASONAL COMPONENT)
    data <- processData(dataRaw, 3600, F)
    
    if(deltaPast > length(data)) {
      print("ERROR : specified delta is longer than provided delta")
    } else {
      dataActual=list()
      for(i in 1:deltaPast) {
        dataActual <- c(data[length(data)], dataActual)
        data <- data[-length(data)]
      }
      
      ts <- convertToTs(data, as.POSIXct(names(data)[1]), 24)
      fit <-  auto.arima(ts)
      fcast <- forecast(fit, h=deltaPast)
      
      similarity <- dist(as.vector(unlist(dataActual, use.names = F)), as.vector(unlist(fcast$mean, use.names = F)), method="Manhattan")
      vectorSimilarity=vector()
      
      for(i in 1:length(dataActual)) {
        vectorSimilarity <- c(vectorSimilarity, similarity[i, i])
      }
      
      #========== VIZ
      tsActual <- convertToTs(dataActual, as.POSIXct(names(dataActual)[1]), 24)
      plot(tsActual)
      plot(fcast$mean)
      plot(fcast)
      
      #print(vectorSimilarity)
      print(mean(vectorSimilarity))
      print(max(vectorSimilarity))
      #==========
    }
  }
  
  return(mean(vectorSimilarity))
}