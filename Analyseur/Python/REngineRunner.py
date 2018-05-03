import os
import subprocess
from itertools import cycle
from subprocess import Popen, PIPE

ROUND_ROBIN_FILE = 'rbf.txt'
NB_MAX = 10

class REngineRunner:
    def __init__(self,path_r_file, with_rb):
        self.path_r_file = path_r_file
        # use for plotly url in r script
        nb = 0
        if with_rb:
            with open(ROUND_ROBIN_FILE,'r') as f:
                nb = f.readline()
            nb = int(nb)
            if nb >= NB_MAX:
                nb = 1
            else:
                nb +=1
            with open(ROUND_ROBIN_FILE, 'w') as f:
                f.write(str(nb))
            self.nb = nb

    def run_with_cycle_management(self,args):
        cmd = ['Rscript',self.path_r_file]
        cmd.extend(args)
        # round robin number
        cmd.append(str(self.nb))

        process = Popen(cmd, stdout=PIPE, stderr=PIPE, bufsize=1,
                                   universal_newlines=True,cwd="../R/Engine/src")
        process.wait()
        print(process.args)
        for i in process.stderr.readlines():
            print(i)


    def run_update(self,flux_id):
        cmd = ['Rscript', self.path_r_file, str(flux_id)]
        process = Popen(cmd, stdout=PIPE, stderr=PIPE,
                        universal_newlines=True, cwd="../R/Engine/src")
        process.wait()
        for line in process.stderr.readlines():
            print(line)
