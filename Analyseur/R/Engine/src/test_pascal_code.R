library(anytime)
library(plotly)
Sys.setenv("plotly_username"="xxx")
Sys.setenv("plotly_api_key"="yyy")
source("modelCalulator.R")
source("forecaster.R")
modelCalculator(12,"../data/data.json", paste("../data/",60,"/", sep = ""), 60)
fore <- forecaster("../data/model/12.rda",7*24)


# seuillage
fore$mean <-ifelse(fore$mean>=0,fore$mean,0)
fore$lower[,2] <-ifelse(fore$lower[,2]>=0,fore$lower[,2],0)
fore$lower[,1] <-ifelse(fore$lower[,1]>=0,fore$lower[,1],0)
fore$upper[,2] <-ifelse(fore$upper[,2]>=0,fore$upper[,2],0)
fore$upper[,1] <-ifelse(fore$upper[,1]>=0,fore$upper[,1],0)

# time pred
timesequenceForecast <- seq(ymd_hms(tail(fore$timeSeq,n=1)), 
                            by = '1 hour',length.out=(7*24+1))

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

chart_link = api_create(p, filename="issou")
