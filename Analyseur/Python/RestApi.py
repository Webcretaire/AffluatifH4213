from flask import Flask, request, jsonify
from flask_httpauth import HTTPBasicAuth
from DataLoader import DataLoader
from REngineRunner import REngineRunner

app = Flask(__name__)
auth = HTTPBasicAuth()

users = {
    "xxx": "yyy"
}

@auth.get_password
def get_pw(username):
    if username in users:
        return users.get(username)
    return None

URL_FILE = "url.txt"
R_ANALYSER_FILE = "forecasterService.R"

@app.route("/predict", methods=['GET'])
@auth.login_required
def predict():
   print(request.method)
   if request.method == 'GET':
        try:
            flux_id = request.args['fid']
            delta = request.args['delta']
            time_unit = request.args['time_unit']

            with open(URL_FILE, 'w') as f:
                f.writelines('errorxx')
                f.close()

            r = REngineRunner(R_ANALYSER_FILE,True)
            r.run_with_cycle_management([flux_id, time_unit,delta])
            with open(URL_FILE, 'r') as f:
                url = str(f.readline())
                url = url[:-2]
                f.close()

            return jsonify(url)
        except ValueError:
            return jsonify("Please enter a valid flux_id")
        except KeyError:
            return jsonify("Please use provide all parameters (fid and delta_sec")




if __name__ == '__main__':
    app.run(debug=True)
