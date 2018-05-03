import sys
sys.path.insert(0, '../analyzer_queue')
import analyzerQuery
import time
# Thread used to update analyzer models every TIME_UPDATE_ANALYZER_MN minutes
def updateModels(lock, streamList, config):
    while 1:
        try:
            lock.acquire()
            print("[*] Updating analyzer models.")
            if len(streamList) == 0:
                lock.release()
                print("[*] Stopped model update because streamList is empty")
                break
            for i in range(len(streamList)):
                analyzer = analyzerQuery.AnalyzerQuery(streamList[i][1])
                analyzer.pushQueue()
                print("[*] Updating model " + streamList[i][0] + ".")
        finally:
            lock.release()
            print("[*] Finished updating model, new update will be pushed in " + config["GENERAL"]["TIME_UPDATE_ANALYZER_MN"] + " minutes.")
            time.sleep(int(config["GENERAL"]["TIME_UPDATE_ANALYZER_MN"])*60)