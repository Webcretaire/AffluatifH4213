from io import StringIO

from DataLoader import DBInterract
from pandas import Series, date_range, to_datetime
import matplotlib.pyplot as plt
import numpy as np
import pandas as pd
from DataLoader import DataLoader
PATH_CSV_FILE = "../R/Engine/data.csv"
PATH_JSON_FILE = "../R/Engine/data.json"
plt.style.use('fivethirtyeight')

list = [1,3,4,5,6,7,8,12]
path = "../R/Engine/data/eval/data_{}.json"
for num in list:
    DataLoader.writeJson(num, path.format(num))