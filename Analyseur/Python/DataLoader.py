import os

import mysql.connector
from datetime import datetime
from mysql.connector import errorcode
import json

PATH_CSV_FILE = "../data.csv"
R_PATH_JSON_FILE = "../data/data/json"
PY_PATH_JSON_FILE = "../R/Engine/data/data.json"


class DBInterract:

    def __init__(self):
        self.cnx = None

    def open_connection(self):
        try:
            self.cnx = mysql.connector.connect(user='xxx', password='yyy',
                                               host='zzz',
                                               database='ddd')
        except mysql.connector.Error as err:
            if err.errno == errorcode.ER_ACCESS_DENIED_ERROR:
                print("Something is wrong with your user name or password")
            elif err.errno == errorcode.ER_BAD_DB_ERROR:
                print("Database does not exist")
            else:
                print(err)
            if self.cnx is not None:
                self.cnx.close()
            self.cnx = None

    def close_connection(self):
        if self.cnx is not None:
            self.cnx.close()
        self.cnx = None

    def fetch_data(self, flux_id):
        """

        :param flux_id:
        :return: DataFrame containing data from the flux_id
        """
        if self.cnx is not None:
            try:
                cursor = self.cnx.cursor()
                cursor.execute(
                    """SELECT date, nombre FROM Affluatif.affluence_flux  where flux_id= %(flux_id)s and type= %(type)s order by date asc""",
                    {'flux_id': flux_id,
                     'type': 1})
                data = cursor.fetchall()
                return data
            except Exception as e:
                raise (e)


class DataLoader:
    @staticmethod
    def writeJson(flux_id, path=PY_PATH_JSON_FILE):
        db = DBInterract()
        db.open_connection()
        data = db.fetch_data(flux_id)
        with open(path, "w") as file:
            dict = {}
            for row in data:
                dict[row[0].strftime("%Y-%m-%d %H:%M:%S")] = row[1]
            d = json.dumps(dict, indent=4)
            file.write(d)
        os.chmod(PY_PATH_JSON_FILE, 0o777)
        db.close_connection()

    @staticmethod
    def get_json_file_path():
        return R_PATH_JSON_FILE
