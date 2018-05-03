import mysql.connector
from datetime import datetime
from mysql.connector import errorcode
import time
# DB Interract class
class DBInterract:
    # constructor 
    def __init__(self):
        self.cnx = None
    # open connection to DB
    def open_connection(self):
        try:
            self.cnx = mysql.connector.connect(user='USERNAME_DB', password='PASSWORD_DB',
                                               host='HOSTNAME_DB',
                                               database='DB_NAME')
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

    #close connection to DB
    def close_connection(self):
        if self.cnx is not None:
            self.cnx.close()
        self.cnx = None

    # return true if the data is successfully committed on the database
    def send_affluence_data(self, flux_id, class_id, affluence_count):
        if self.cnx is not None :
            try:
                cursor = self.cnx.cursor()
                add_values = ('INSERT INTO affluence_flux '
                              '(flux_id, type, nombre) '
                              'VALUES (%(flux_id)s, %(type)s, %(nombre)s)')
                data_values = {
                    'flux_id': flux_id,
                    'type': class_id,
                    'nombre' : affluence_count
                }
                cursor.execute(add_values, data_values)
                self.cnx.commit()
                return True
            except:
                self.cnx.rollback()
                print("[*] ERROR sending affluence " + str(affluence_count) + " to DB for stream " + str(flux_id))
                return False
    # Returns stream name 
    def getStreamDescription(self,flux_id):
        if self.cnx is not None:
            try:
                cursor = self.cnx.cursor()
                query = ("SELECT description from flux_video where id=%(id)s")
                cursor.execute(query, {"id":flux_id})
                return cursor.fetchone()[0]
            except mysql.connector.Error as err:
                print("[*] ERROR retrieving stream description from flux " + str(flux_id))
                print("[*] ERROR details : {}".format(err))
                return None
    # Return last alert date
    def getLastDateAlert(self, alert_id):
        if self.cnx is not None:
            try:
                cursor = self.cnx.cursor()
                query = ("SELECT derniere_alerte FROM alertes WHERE id = %(id)s")
                cursor.execute(query, {"id":alert_id})
                return cursor.fetchone()[0]
            except mysql.connector.Error as err:
                print("[*] ERROR retrieving last date description from flux " + str(alert_id))
                print("[*] ERROR details : {}".format(err))
                return None


    # Return all alerts of stream
    def getAlertsFromStream(self, flux_id):
        if self.cnx is not None:
            try:
                cursor = self.cnx.cursor()
                query = ("SELECT * from alertes where flux_id = %(flux_id)s")
                cursor.execute(query, {"flux_id":flux_id})
                alerts_list = []
                for (alert) in cursor:
                    alerts_list.append(alert)
                return alerts_list
            except mysql.connector.Error as err:
                print("[*] ERROR retrieving alerts from flux " + str(flux_id))
                print("[*] ERROR details : {}".format(err))
                return []
    # Return wanted classes from stream
    def get_classes_from_stream(self, flux_id):
        if self.cnx is not None:
            try:
                cursor = self.cnx.cursor()
                query = ("SELECT classe FROM classe_flux "
                         "WHERE flux_id = %(flux_id)s")

                cursor.execute(query, {"flux_id":flux_id})
                classe_liste = []
                for (classe) in cursor:
                    classe_liste.append(classe)
                return classe_liste
            except mysql.connector.Error as err:
                print("[*] ERROR retrieving class from flux " + str(flux_id))
                print("[*] ERROR details : {}".format(err))
                return []
    # Return emails of subsribed users of a stream
    def getStreamUsersEmails(self, flux_id):
        if self.cnx is not None:
            try:
                cursor = self.cnx.cursor()
                query = ("SELECT mail FROM `utilisateurs` INNER JOIN flux_utilisateur on utilisateurs.id = flux_utilisateur.utilisateur_id WHERE flux_utilisateur.flux_id = %(flux_id)s")
                cursor.execute(query, {"flux_id":flux_id})
                mails = []
                for (mail) in cursor:
                    mails.append(mail)
                return mails
            except mysql.connector.Error as err:
                print("[*] ERROR retrieving mails linked to flux " + str(flux_id))
                print("[*] ERROR details : {}".format(err))
                return []

    # returns infos from last affluence optimum
    def getMaxAffluenceInfos(self, flux_id):
        if self.cnx is not None :
            try:
                cursor = self.cnx.cursor()
                query = ('SELECT affluence_flux.id, nombre, date, image_max_affluence FROM affluence_flux INNER JOIN flux_video on flux_video.id = affluence_flux.flux_id WHERE nombre = (SELECT MAX(nombre) AS maxFlux  FROM affluence_flux INNER JOIN flux_video on affluence_flux.flux_id = flux_video.id WHERE flux_id = %(id)s  GROUP BY affluence_flux.flux_id) AND affluence_flux.flux_id= %(id)s ORDER BY date DESC LIMIT 1 ')

                data_values = {
                    'id': flux_id,
                }
                cursor.execute(query, data_values)
                row = cursor.fetchone()
                return row
            except Exception as e:
                print("[*] ERROR getting max affluence infos from DB for stream " + str(flux_id))
                print(e)
                return None

    # Sends image data to DB 
    def send_image_data(self, flux_id, imageBytes):
        if self.cnx is not None :
            try:
                cursor = self.cnx.cursor()
                query = ('UPDATE flux_video SET image_max_affluence = %(blob)s WHERE id=%(flux_id)s')
                data_values = {
                    'flux_id': flux_id,
                    'blob' : imageBytes
                }
                cursor.execute(query, data_values)
                self.cnx.commit()
                return True
            except Exception as e:
                self.cnx.rollback()
                print("[*] ERROR sending max affluence image to DB for stream " + str(flux_id))
                print(e)
                return False
    # Sets last alert date
    def setLastDateAlert(self, alerte_id):
        if self.cnx is not None :
            try:
                cursor = self.cnx.cursor()
                query = ('UPDATE alertes SET derniere_alerte = CURDATE() WHERE id = %(id)s')
                data_values = {
                    'id': alerte_id
                }
                cursor.execute(query, data_values)
                self.cnx.commit()
                return True
            except Exception as e:
                self.cnx.rollback()
                print("[*] ERROR updating last alert date for alert " + str(alerte_id))
                print(e)
                return False

    # Returns stream state (active or not)
    def get_flux_actif(self, flux_id) :
        if self.cnx is not None :
            try:
                cursor = self.cnx.cursor()
                query = ('SELECT actif from flux_video WHERE id=%(flux_id)s')
                data_values = {
                    'flux_id': flux_id,
                }
                cursor.execute(query, data_values)
                row = cursor.fetchone()
                return row[0]==1
            except Exception as e:
                self.cnx.rollback()
                print("[*] ERROR getting stream state (active/inactive)" + str(flux_id))
                print(e)
                return False
    # Set stream waiting flag for interpreter
    def set_stream_not_waiting_for_interpreter(self, flux_id):
        if self.cnx is not None :
            try:
                cursor = self.cnx.cursor()
                query = ('UPDATE flux_video SET waiting_interpret = %(val)s WHERE id=%(flux_id)s')
                data_values = {
                    'flux_id': flux_id,
                    'val' : False
                }
                cursor.execute(query, data_values)
                self.cnx.commit()
                return True
            except Exception as e:
                self.cnx.rollback()
                print("[*] ERROR setting flux not waiting for interpreter anymore" + str(flux_id))
                print(e)
                return False
