library(xts)
library(plotly)
library(lubridate)
set.seed(42)
timesequence <- seq(ymd_hms('2014-01-21 00:00:00'), 
                    by = '15 min',length.out=(60*24*30/15))
min_data <- xts(rnorm(2880),timesequence)

ts_data <- ts(as.numeric(min_data), frequency = 10)
# out <- stl(ts_data, s.window = "per")
# ts_out <- merge(min_data, out$time.series)
# plot.zoo(ts_out)

timesequenceForecast <- seq(ymd_hms(tail(timesequence,n=1)), 
                    by = '150 min',length.out=(10))


fit <- auto.arima(min_data)
fore <- forecast(fit, h = 10, level = c(80, 95))

plot_ly() %>%
  add_lines(x = timesequence, y = ts_data,
            color = I("black"), name = "observed") %>%
  add_ribbons(x = timesequenceForecast, ymin = fore$lower[, 2], ymax = fore$upper[, 2],
              color = I("gray95"), name = "95% confidence") %>%
  add_ribbons(x = timesequenceForecast, ymin = fore$lower[, 1], ymax = fore$upper[, 1],
              color = I("gray80"), name = "80% confidence") %>%
  add_lines(x = timesequenceForecast, y = fore$mean, color = I("blue"), name = "prediction")


## some randopm stuff to understand ts 
a <- ts(rnorm(1000), start=as.POSIXct("2014-01-21 00:00"),frequency=30)
library(anytime)
time <- anytime(time(a))