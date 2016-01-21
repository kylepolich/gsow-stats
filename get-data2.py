import requests
import MySQLdb as mdb
import pandas as pd
import numpy as np
import json
import ConfigParser
import time
import datetime
from dateutil.relativedelta import relativedelta

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

query = "select * from edits where pageid is null"
df = pd.read_sql(query, conn)

q = "UPDATE edits set pageid={} WHERE edit_id={}"
cur = conn.cursor()
for r in range(df.shape[0]):
  row = df.iloc[r]
  editid = row['edit_id']
  title = row['page']
  url = 'https://en.wikipedia.org/w/api.php?action=query&format=json&titles=' + title
  req = requests.get(url)
  j = json.loads(req.text)
  try:
    pages = j['query']['pages']
    keys = pages.keys()
    if len(keys)==1:
      key = keys[0]
      pageid = pages[key]['pageid']
      q2 = q.format(pageid, editid)
      cur.execute(q2)
      conn.commit()
  except:
    print 'Error on ', r, title, url
    continue

cur.close()




# UPDATE PAGE VIEWS

query = """
    SELECT t1.pageid, t1.page as title, t1.start
     , min(t2.dt) as first_dt
     , max(t2.dt) as last_dt
    FROM edits t1
    LEFT JOIN page_views t2
     ON t1.pageid = t2.pageid
    WHERE t1.pageid is not null
    AND t1.start <> '0000-00-00'
    group by t1.pageid, t1.page, t1.start
"""

df2 = pd.read_sql(query, conn)

query = "INSERT INTO page_views (pageid, project, dt, views) VALUES({}, '{}', '{}', {}) ON DUPLICATE KEY UPDATE views=VALUES(views)"

# TODO: only get 90 if we have nothing, otherwise get less

nnow = datetime.datetime.utcnow()

print nnow
nday = nnow.day

cur = conn.cursor()
for r in range(df2.shape[0]):
    row = df2.iloc[r]
    last_dt = row['last_dt']
    first_dt = row['first_dt']
    start = row['start']
    title = row['title'].decode('cp1252')
    if last_dt is None:
        last_dt = start#datetime.datetime.now() - datetime.timedelta(365*10,0)
    else:
        last_dt = datetime.datetime.combine(row['last_dt'], datetime.time(0))
    if first_dt is not None and first_dt > start.to_datetime().date():
        last_dt = start.to_datetime()
    current = last_dt
    nnow = datetime.datetime.combine(nnow, datetime.time(0))
    cday = current.day
    nnow = nnow - relativedelta(days=nnow.day-1)
    current = current - relativedelta(days=current.day-1)
    #print title, current, nnow, cday, nday
    c = 0
    while current < nnow or (current.year==nnow.year and current.month==nnow.month and (cday < nday-1 or c>0)):
        if 1==1:
            url = 'http://stats.grok.se/json/en/' + current.strftime('%Y%m') + '/' + title
            print 'Getting', url
            r = requests.get(url)
            data = json.loads(r.text)
            data['pageid'] = row['pageid']
            time.sleep(.1)
            project = data['project']
            pageid = data['pageid']
            dvs = data['daily_views']
            for dv in dvs.keys():
                pv = dvs[dv]
                q = query.format(pageid, project, dv, pv)
                res = cur.execute(q)
                conn.commit()
        current = current + relativedelta(months=1)
        c += 1

cur.close()


