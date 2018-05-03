### Set library and plotly
.libPaths( c("/home/ubuntu/R/x86_64-pc-linux-gnu-library/3.4", .libPaths()) )
write(.libPaths(),stderr())
Sys.setenv("plotly_username"="xxx")
Sys.setenv("plotly_api_key"="yyy")


library(plotly)
library(forecast)

# args = commandArgs(trailingOnly=TRUE)
# fore <- directSampleForecast(1, args[2], 60, args[3]/60)

fit <- ets(USAccDeaths)
fore <- forecast(fit, h = 48, level = c(80, 95))

p <- plot_ly() %>%
  add_lines(x = time(fore$x), y = fore$x,
            color = I("black"), name = "observed") %>%
  add_ribbons(x = time(fore$mean), ymin = fore$lower[, 2], ymax = fore$upper[, 2],
              color = I("gray95"), name = "95% confidence") %>%
  add_ribbons(x = time(fore$mean), ymin = fore$lower[, 1], ymax = fore$upper[, 1],
              color = I("gray80"), name = "80% confidence") %>%
  add_lines(x = time(fore$mean), y = fore$mean, color = I("blue"), name = "prediction")

# Create a shareable link to your chart
# Set up API credentials: https://plot.ly/r/getting-started
args = commandArgs(trailingOnly=TRUE)
file <- paste("multiple-forecast",args[1])
chart_link = api_create(p, filename=file)
cat(chart_link$embed_url)
