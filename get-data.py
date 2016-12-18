import requests
import MySQLdb as mdb
import pandas as pd
import numpy as np
import json
import ConfigParser
import time
import datetime

propertiesFile = "my.properties"
cp = ConfigParser.ConfigParser()
cp.readfp(open(propertiesFile))

db_host     = cp.get('Params', 'db_host')
db_user     = cp.get('Params', 'db_user')
db_password = cp.get('Params', 'db_password')
db_db       = cp.get('Params', 'db_db')

conn = mdb.connect(host=db_host, user=db_user, passwd=db_password, db=db_db)

url1 = 'http://en.wikipedia.org/w/api.php?action=query&list=usercontribs&ucuser='
url2 = '&uclimit=5000&ucdir=newer&format=json'

cache = {}

query = "select * from editor"
df = pd.read_sql(query, conn)

query = "INSERT INTO contributions (comment, ns, pageid, parentid, revid, size, ts, title, editor_id) VALUES ('{}',{},{},{},{},{},'{}','{}',{})"
query2 = "UPDATE editor SET last_check='{}' where editor_id={}"
cur = conn.cursor()

for r in range(df.shape[0]):
    row = df.iloc[r]
    name = row['name']
    editor_id = row['editor_id']
    last_check = row['last_check']
    uccontinue = '0'
    while uccontinue != -1:
        url = url1 + name + url2
        if uccontinue != '0':
            url = url + '&uccontinue=' + str(uccontinue)
        elif last_check is not None and last_check != '':
            url = url + '&ucstart=' + last_check
        try:
            data = cache[url]
            print 'Running ' + url
        except KeyError:
            print 'Getting ' + url
            r = requests.get(url)
            data = json.loads(r.text)
            cache[url] = data
            contributions = data['query']['usercontribs']
            
            if len(contributions) > 0:
                max_timestamp = contributions[0]['timestamp']
                mts = pd.to_datetime(max_timestamp)
            for contribution in contributions:
                ts = pd.to_datetime(contribution['timestamp'])
                if ts > mts:
                    mts = ts
                    max_timestamp = contribution['timestamp']
                title = contribution['title'].replace("'", "''").encode('ascii', 'ignore')
                if contribution.has_key('comment'):
                    comment = contribution['comment'].replace("'", "''").encode('ascii', 'ignore')
                else:
                    comment = ''
                q = query.format(comment, contribution['ns'], contribution['pageid'], contribution['parentid'], contribution['revid'], contribution['size'], contribution['timestamp'], title, editor_id)
                res = cur.execute(q)
                conn.commit()
            if len(contributions) > 0:
                mts = mts + datetime.timedelta(0,1)
                q2 = query2.format(max_timestamp, editor_id)
                cur.execute(q2)
                conn.commit()
        
        if data.has_key('continue'):
            uccontinue = data['continue']['uccontinue']
        else:
            uccontinue = -1

cur.close()


