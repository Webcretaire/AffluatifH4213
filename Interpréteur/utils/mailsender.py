# Import smtplib for the actual sending function
import smtplib
import datetime
import os
from email.mime.application import MIMEApplication
from email.mime.multipart import MIMEMultipart
from email.mime.text import MIMEText
from os.path import basename

MAIL = 'EMAIL_SENDER_ALERT'
PASSWORD = 'PASSWORD_SENDER_ALERT'

# Send mail to concerned users for alerts
def send_mail_alert(user_email, detection_date, image_file_name, stream_name) :
    msg = MIMEMultipart()
    msg['Subject'] = 'ALERTE : intrus détecté à l\'emplacement suivant : ' + stream_name
    msg['From'] = MAIL
    msg['To'] = user_email
    with open('../../mail-template/template.html', 'r', encoding='utf-8') as content_file:
        text = str(content_file.read())
        text = text.format(stream_name, str(detection_date), basename(image_file_name))
        msg.attach(MIMEText(text, 'html'))
        with open(image_file_name, "rb") as fil:
            part = MIMEApplication(
                fil.read(),
                Name=basename(image_file_name)
            )
            fil.close()
            # After the file is closed
            part.add_header('Content-ID', '<{}>'.format(basename(image_file_name)))
            msg.attach(part)

        # Send the message via our own SMTP server, but don't include the
        # envelope header.
        try:
            server = smtplib.SMTP_SSL('SMTP_SERVER_HOSTNAME', 465)
        #  server.starttls()
            server.login(MAIL, PASSWORD)
            server.sendmail(MAIL, [user_email], msg.as_string())
            server.quit()
        except Exception as e:
            print("Error sending email:")
            print(e)
    content_file.close()